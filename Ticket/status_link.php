<?php $guest_access = true;
include_once ('../include.php');
$details = json_decode(decryptIt($_GET['s']), TRUE);
$ticketid = filter_var($details['ticket'],FILTER_SANITIZE_STRING);
$stopid = filter_var($details['stop'],FILTER_SANITIZE_STRING);
$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'"));
$get_stop = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `ticketid`='$ticketid' AND `id`='$stopid'"));
$date = empty($get_stop['to_do_date']) ? $get_ticket['to_do_date'] : $get_stop['to_do_date'];
$contacts = array_filter(explode(',',$get_stop['contactid']));
$equipment = $get_stop['equipmentid'];
if(empty($contacts)) {
	$equip_assign = $get_stop['equipment_assignmentid'];
	if(empty($equip_assign)) {
		if(empty($equipment)) {
			$equipment = $get_ticket['equipmentid'];
		}
		$equip_assign = $dbc->query("SELECT * FROM `equipment_assignment` WHERE `equipmentid`='$equipment' AND '$date' BETWEEN `start_date` AND `end_date` AND `deleted`=0 ORDER BY `start_date` DESC")->fetch_assoc()['equipment_assignmentid'];
	}
	$assigned_team = $dbc->query("SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid`='$equip_assign' AND `deleted`=0");
	while($contact = $assigned_team->fetch_assoc()) {
		$contacts[] = $contact['contactid'];
	}
}
$driver_cookie = $dbc->query("SELECT `location_cookie` FROM `equipment` WHERE `equipmentid`='$equipment'")->fetch_assoc()['location_cookie'];
$class = $get_ticket['classification'];
$icon = '';
$icon_size = '';
$class_logos = explode('*#*',get_config($dbc, '%_class_logos', true, '*#*'));
foreach(explode(',',get_config($dbc, '%_classification', true, ',')) as $i => $contact_classification) {
	if($contact_classification == $class) {
		$icon = $class_logos[$i];
		$icon_size = getimagesize('..'.str_replace(WEBSITE_URL,'',$icon));;
	}
} ?>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= EMBED_MAPS_KEY ?>&callback=setMarker"></script>
<script>
var latitude = 0;
var longitude = 0;
function loadStatus(destination, service, display, map) {
	$.post('status_info.php', { destination: '<?= urlencode($get_stop['address'].','.$get_stop['city'].','.$get_stop['postal_code']) ?>', driver: '<?= $driver_cookie ?>' }, function(response) {
		response = response.split('#*#');
		$('[name=eta_text]').text(response[0]);
		if((response[1] > 0 || response[1] < 0) && (response[2] > 0 || response[2] < 0)) {
			latitude = response[1];
			longitude = response[2];
			service.route({
				origin: latitude+','+longitude,
				destination: destination,
				travelMode: 'DRIVING',
				avoidTolls: true
			}, function(response, status) {
				if (status === 'OK') {
					display.setDirections(response);
					var marker = new google.maps.Marker({
						position: {lat: latitude * 1, lng: longitude * 1},
						map: map,
						<?= empty($icon) ? '' : "icon: { url: '".$icon."', size: new google.maps.Size(".($icon_size[0] > 0 ? $icon_size[0] : 150).", ".($icon_size[1] > 0 ? $icon_size[1] : 150)."), origin: new google.maps.Point(0, 0), anchor: new google.maps.Point(28, 8), scaledSize: new google.maps.Size(32,32) }," ?>
						title: 'Driver',
						zIndex: 1
					});
					var marker = new google.maps.Marker({
						position: {lat: response.routes[0].overview_path[response.routes[0].overview_path.length - 1].lat(), lng: response.routes[0].overview_path[response.routes[0].overview_path.length - 1].lng()},
						map: map,
						title: 'Driver',
						zIndex: 1
					});
				}
			});
		} else {
			$('#map').load(response[1]);
		}
	});
}
function setMarker() {
	var map = new google.maps.Map(document.getElementById('map'), { zoom: 12 });

	var directionsService = new google.maps.DirectionsService;
	var directionsDisplay = new google.maps.DirectionsRenderer({
		draggable: false,
		map: map,
		suppressMarkers: true
	});

	loadStatus('<?= $get_stop['address'].','.$get_stop['city'].','.$get_stop['postal_code'] ?>', directionsService, directionsDisplay, map);
}
</script>
<div class="container no-gap-pad" style="height: 100vh; width: 100vw; overflow-y: hidden;">
	<div class="row">
		<h3 class="text-center"><img src="<?= WEBSITE_URL.'/Settings/download/'.get_config($dbc, 'logo_upload') ?>" style="height:50px;"><br />We're On Our Way!<br /><?= implode(' - ',array_filter([$get_stop['location_name'], $get_stop['client_name'], $get_stop['address']])) ?><br />ETA: <span name="eta_text"></h3>
		<div id="map" style="height:calc(100vh - 140px - <?= count($contacts) * 6 ?>em);width:100vw;border:none;"></div>
		<?php foreach($contacts as $contact) {
			$contact = $dbc->query("SELECT `contacts`.`tile_name`, `contacts`.`first_name`, `contacts`.`last_name`, `contacts_upload`.`contactimage` FROM `contacts` LEFT JOIN `contacts_upload` ON `contacts`.`contactid`=`contacts_upload`.`contactid` WHERE `contacts`.`contactid`='$contact'")->fetch_assoc();
			$img_url = '../'.($contact['tile_name'] == 'staff' || $contact['category'] == 'staff' ? (file_exists('../Staff/download/'.$contact['contactimage']) ? 'Staff' : 'Profile') : 'Contacts').'/download/'.$contact['contactimage'];
			echo '<div class="col-sm-6 small"><div class="pull-left"><img class="inline-img" src="'.(!empty($contact['contactimage']) && file_exists($img_url) ? $img_url : '../img/person.PNG').'" style="width: auto; height:6em; max-height:6em;"></div>'.decryptIt($contact['first_name']).' '.decryptIt($contact['last_name']).' - '.get_config($dbc, 'company_name').'<br />
				<img class="inline-img" src="../img/icons/star.png"><img class="inline-img" src="../img/icons/star.png"><img class="inline-img" src="../img/icons/star.png"><img class="inline-img" src="../img/icons/star.png"><img class="inline-img" src="../img/icons/star.png"><br />
				125,462 Deliveries Completed<br />
				Website: <a href="http://www.customhomedelivery.com">www.customhomedelivery.com</a>';
			echo '<div class="clearfix"></div></div>';
		} ?>
		<div class="clearfix"></div>
	</div>
</div>