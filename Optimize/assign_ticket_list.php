<?php include('../include.php');
$security_access = $dbc->query("SELECT `region_access`, `location_access`, `classification_access` FROM `contacts_security` WHERE `contactid`='".$_SESSION['contactid']."'")->fetch_assoc();
$region = filter_var(($_GET['region'] != '' ? $_GET['region'] : '%'),FILTER_SANITIZE_STRING);
$location = filter_var(($_GET['location'] != '' ? $_GET['location'] : '%'),FILTER_SANITIZE_STRING);
$classification = filter_var(($_GET['classification'] != '' ? $_GET['classification'] : '%'),FILTER_SANITIZE_STRING);
$date = filter_var(($_GET['date'] != '' ? $_GET['date'] : date('Y-m-d')),FILTER_SANITIZE_STRING);

$ticket_list = $dbc->query("SELECT `tickets`.*, IF(`ticket_schedule`.`id` IS NULL, `tickets`.`ticketid`,`ticket_schedule`.`id`) `id`, IF(`ticket_schedule`.`id` IS NULL, 'ticketid','id') `id_field`, IF(`ticket_schedule`.`id` IS NULL, 'tickets','ticket_schedule') `table`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipment`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, `ticket_schedule`.`client_name`, `ticket_schedule`.`address` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` WHERE `tickets`.`deleted`=0 AND IFNULL(`ticket_schedule`.`deleted`,0)=0 AND IFNULL(NULLIF(IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`),''),'$date')='$date' AND IFNULL(IFNULL(`ticket_schedule`.`city`,`tickets`.`city`),'') != '' AND CONCAT(',',IFNULL(`tickets`.`region`,''),',') LIKE '%,$region,%' AND CONCAT(',',IFNULL(`tickets`.`con_location`,''),',') LIKE '%,$location,%' AND CONCAT(',',IFNULL(`tickets`.`classification`,''),',') LIKE '%,$classification,%' AND IFNULL(NULLIF(IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`),''),0)=0 AND TRIM(IF(`ticket_schedule`.`id` IS NULL, CONCAT(IFNULL(`tickets`.`address`,''),',',IFNULL(`tickets`.`city`,'')),CONCAT(IFNULL(`ticket_schedule`.`address`,''),',',IFNULL(`ticket_schedule`.`city`,'')))) != '' AND (IFNULL(`type`,'') != 'warehouse' AND TRIM(IF(`ticket_schedule`.`id` IS NULL, CONCAT(IFNULL(`tickets`.`address`,''),',',IFNULL(`tickets`.`city`,'')),CONCAT(IFNULL(`ticket_schedule`.`address`,''),',',IFNULL(`ticket_schedule`.`city`,'')))) NOT IN (SELECT TRIM(CONCAT(IFNULL(`address`,''),IFNULL(`city`,''))) FROM `contacts` WHERE `category`='Warehouses'))");
echo '<h3 class="text-center">Unbooked '.TICKET_TILE.($ticket_list->num_rows > 0 ? ' <em><small>('.$ticket_list->num_rows.')</small></em>' : '').'<br /><em><small class="out_of_view"></small></em></h3>';
if($ticket_list->num_rows > 0) { ?>
    <script>
    $(document).ready(function() {
        calcTicketListWidth();
    });
    function calcTicketListWidth() {
        $('#ticket_list_scroll').height($('.ticket_list').height() - $('#ticket_list_scroll').offset().top + $('.ticket_list').offset().top);
    };
    </script>
    <span class="cursor-hand" onclick="$(this).closest('.scalable').addClass('collapsed'); scaleFunction();"><b>Hide List >></b></span>
    <div style="overflow-y:auto;padding:0 0.5em;" id="ticket_list_scroll">
	<?php while($ticket = $ticket_list->fetch_assoc()) { ?>
		<?php $region_list = explode(',',get_config($dbc, '%_region', true));
		$region_colours = explode(',',get_config($dbc, '%_region_colour', true));

		//Check region/location/classification
		if(empty($ticket['region']) && $ticket['businessid'] > 0) {
			$ticket['region'] = get_contact($dbc, $ticket['businessid'], 'region');
		}
		if(empty($ticket['con_location']) && $ticket['businessid'] > 0) {
			$ticket['con_location'] = get_contact($dbc, $ticket['businessid'], 'con_locations');
		}
		if(empty($ticket['classification']) && $ticket['businessid'] > 0) {
			$ticket['classification'] = get_contact($dbc, $ticket['businessid'], 'classification');
		}

		//Ticket colour
		$ticket_colour = '#D7FFFF';
		foreach($region_list as $i => $region) {
			if($ticket['region'] == $region) {
				$ticket_colour = empty($region_colours[$i]) ? $ticket_colour : $region_colours[$i];
			}
		}
		
		//Block type
		if($_GET['type'] == 'schedule' && $_GET['mode'] == 'staff') {
			$data_blocktype = 'dispatch_staff';
		}

		$customer = get_client($dbc, $ticket['businessid']);
		$assigned_staffids = explode(',', trim($ticket['contactid'], ','));
		$assigned_staff = '';
		foreach ($assigned_staffids as $assigned_staffid) {
			$assigned_staff .= get_contact($dbc, $assigned_staffid).', ';
			$staff_filters[$assigned_staffid]++;
		}
		$assigned_staff = rtrim($assigned_staff, ', ');
		$preferred_staff = [$ticket['preferred_staff'] * 1];
		$contacts_preferred = mysqli_query($dbc, "SELECT `contacts`.`assign_staff` FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` LEFT JOIN `contacts` ON CONCAT(',',`tickets`.`businessid`,',',`tickets`.`clientid`,',',`project`.`businessid`,',',`project`.`clientid`,',') LIKE CONCAT('%,',`contacts`.`contactid`,',%') AND `contacts`.`deleted`=0 WHERE `tickets`.`ticketid`='".$ticket['ticketid']."'");
		while($contact_pref = mysqli_fetch_assoc($contacts_preferred)) {
			$preferred_staff[] = $contact_pref['assign_staff'] * 1;
		}

		$calendar_ticket_card_fields = explode(',',get_config($dbc, 'calendar_ticket_card_fields'));
		$unbooked_html = '<span class="block-item ticket" style="position: relative; background-color: '.$ticket_colour.'; border: 1px solid rgba(0,0,0,0.5); color: #000; margin: 0.25em 0 0;" data-type="ticket" data-table="'.$ticket['table'].'" data-id="'.$ticket['id'].'" data-id-field="'.$ticket['id_field'].'" data-min-time="'.$ticket['pickup_start_available'].'" data-max-time="'.$ticket['pickup_end_available'].'" data-preferred-staff="'.json_encode(array_unique(array_filter($preferred_staff))).'" data-text="'.get_ticket_label($dbc, $ticket).' '.$ticket['heading'].' '.$ticket['project_name'].' '.$customer.'" data-project="'.$ticket['projectid'].'" data-cust="'.$ticket['businessid'].'" data-staff="'.$ticket['contactid'].'" data-staffnames="'.$assigned_staff.'" data-region="'.$ticket['region'].'" data-location="'.$ticket['con_location'].'" data-classification="'.$ticket['classification'].'" data-status="'.$ticket['status'].'" data-timestamp="'.date('Y-m-d H:i:s').'" '.$data_blocktype.' data-startdate="'.$ticket['to_do_date'].'" data-projecttype="'.$ticket['projecttype'].'" title="View '.TICKET_NOUN.'">
				<div class="drag-handle full-height" title="Drag Me!">
					<img class="drag-handle black-color inline-img pull-right" src="'.WEBSITE_URL.'/img/icons/drag_handle.png" />
				</div>
				<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].'" data-ticketid="'.$ticket['ticketid'].'" onclick=\'
				overlayIFrameSlider(this.href+"&calendar_view=true","auto",true,true); return false;\' style="text-decoration: none; display: block;">
				'.get_ticket_label($dbc, $ticket).($ticket['sub_label'] != '' ? '-'.$ticket['sub_label'] : '').($ticket['scheduled_lock'] > 0 ? '<img class="inline-img" title="Time has been Locked" src="../img/icons/lock.png">' : '').'<br />
				'.(in_array('project',$calendar_ticket_card_fields) ? PROJECT_NOUN.' #'.$ticket['projectid'].' '.$ticket['project_name'].'<br />' : '').'
				'.(in_array('customer',$calendar_ticket_card_fields) ? 'Customer: '.$customer.'<br />' : '').'
				'.(in_array('assigned',$calendar_ticket_card_fields) ? 'Assigned Staff: '.$assigned_staff.'<br />' : '').'
				'.(in_array('start_date',$calendar_ticket_card_fields) && !empty($ticket['to_do_date']) ? 'Date: '.$ticket['to_do_date'] : '');
		if(in_array('preferred',$calendar_ticket_card_fields)) {
			foreach(array_unique(array_filter($preferred_staff)) as $pref_staff) {
				$unbooked_html .= "<br />Preferred Staff: ".get_contact($dbc, $pref_staff);
			}
		}
		if(in_array('available',$calendar_ticket_card_fields)) {
			if($ticket['pickup_start_available'].$ticket['pickup_end_available'] != '') {
				$unbooked_html .= "<br />Available ";
				if($ticket['pickup_end_available'] == '') {
					$unbooked_html .= "After ".$ticket['pickup_start_available'];
				} else if($ticket['pickup_start_available'] == '') {
					$unbooked_html .= "Before ".$ticket['pickup_end_available'];
				} else {
					$unbooked_html .= "Between ".$ticket['pickup_start_available']." and ".$ticket['pickup_end_available'];
				}
			}
		}
		$unbooked_html .= (in_array('address',$calendar_ticket_card_fields) ? '<br />Address: '.$ticket['pickup_name'].($ticket['pickup_name'] != '' ? '<br />' : ' ').$ticket['client_name'].($ticket['client_name'] != '' ? '<br />' : ' ').$ticket['pickup_address'].($ticket['pickup_address'] != '' ? '<br />' : ' ').$ticket['address'].($ticket['address'] != '' ? '<br />' : ' ').$ticket['pickup_city'] : '').'
			</a></span>';

		echo $unbooked_html; ?>
	<?php }
    echo '</div>';
} else {
	echo '<h4>No '.TICKET_TILE.' Found</h4>';
}