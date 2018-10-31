<?php include('../include.php');
$region = filter_var(($_GET['region'] != '' ? $_GET['region'] : '%'),FILTER_SANITIZE_STRING);
$location = filter_var(($_GET['location'] != '' ? $_GET['location'] : '%'),FILTER_SANITIZE_STRING);
$classification = filter_var(($_GET['classification'] != '' ? $_GET['classification'] : '%'),FILTER_SANITIZE_STRING);
$date = filter_var(($_GET['date'] != '' ? $_GET['date'] : date('Y-m-d')),FILTER_SANITIZE_STRING);
$zoom = filter_var(($_GET['zoom'] > 0 ? $_GET['zoom'] : 10),FILTER_SANITIZE_STRING);
$zoom_ratios = [ 6=>[0.230922908978305,0.123661840801085],
    7=>[0.458994917845767,0.348501551348513],
    8=>[0.893571593309453,0.405758727090875],
    9=>[1.77372619572837,1.08202327224233],
    10=>[3.521067551250225,2.286361409982097],
    11=>[7.57708952244115,5.26699691972239],
    12=>[14.983267251143,10.5931735801158],
    13=>[28.8840931419373,20.9496281975475],
    14=>[56.6768541754863,43.461532041142],
    15=>[113.97653092433,89.3081481577125],
    16=>[244.863134370952,70.8276117126311] ]; // 6, 7, 8, 9, 10, 11

$city_target = $dbc->query("SELECT IFNULL(`ticket_schedule`.`address`,`tickets`.`address`) `address`, IFNULL(`ticket_schedule`.`city`,`tickets`.`city`) `city` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` WHERE `tickets`.`deleted`=0 AND IFNULL(`ticket_schedule`.`deleted`,0)=0 AND IFNULL(NULLIF(IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`),''),'$date')='$date' AND IFNULL(IFNULL(`ticket_schedule`.`city`,`tickets`.`city`),'') != '' AND CONCAT(',',IFNULL(`tickets`.`region`,''),',') LIKE '%,$region,%' AND CONCAT(',',IFNULL(`tickets`.`con_location`,''),',') LIKE '%,$location,%' AND CONCAT(',',IFNULL(`tickets`.`classification`,''),',') LIKE '%,$classification,%' AND IFNULL(NULLIF(IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`),''),0)=0 AND TRIM(IF(`ticket_schedule`.`id` IS NULL, CONCAT(IFNULL(`tickets`.`address`,''),',',IFNULL(`tickets`.`city`,'')),CONCAT(IFNULL(`ticket_schedule`.`address`,''),',',IFNULL(`ticket_schedule`.`city`,'')))) != '' AND (IFNULL(`type`,'') != 'warehouse' AND TRIM(IF(`ticket_schedule`.`id` IS NULL, CONCAT(IFNULL(`tickets`.`address`,''),',',IFNULL(`tickets`.`city`,'')),CONCAT(IFNULL(`ticket_schedule`.`address`,''),',',IFNULL(`ticket_schedule`.`city`,'')))) NOT IN (SELECT TRIM(CONCAT(IFNULL(`address`,''),IFNULL(`city`,''))) FROM `contacts` WHERE `category`='Warehouses')) GROUP BY IFNULL(`ticket_schedule`.`city`,`tickets`.`city`) ORDER BY COUNT(*) DESC")->fetch_assoc();
if($city_target['address'] != '' || $city_target['address'] != '') {
	$height = $_GET['y'];
	$width = $_GET['x'];
	$ratio = $width / $height;
	if($ratio < 1) {
		$height = 640;
		$width = round($height * $ratio);
	} else {
		$width = 640;
		$height = round($width / $ratio);
	}
	$map_url = 'https://maps.googleapis.com/maps/api/staticmap?center='.urlencode(implode(',',$city_target)).'&zoom='.$zoom.'&size='.$width.'x'.$height.'&maptype=roadmap&key='.EMBED_MAPS_KEY;
}
$ticket_list = $dbc->query("SELECT `tickets`.`ticket_label`, IF(`ticket_schedule`.`id` IS NULL, `tickets`.`ticketid`,`ticket_schedule`.`id`) `id`, IF(`ticket_schedule`.`id` IS NULL, 'ticketid','id') `id_field`, IF(`ticket_schedule`.`id` IS NULL, 'tickets','ticket_schedule') `table`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipment`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IF(`ticket_schedule`.`id` IS NULL, CONCAT(IFNULL(`tickets`.`address`,''),',',IFNULL(`tickets`.`city`,'')),CONCAT(IFNULL(`ticket_schedule`.`address`,''),',',IFNULL(`ticket_schedule`.`city`,''))) `address`, `ticket_schedule`.`coordinates`, `ticket_schedule`.`coord_auto` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` WHERE `tickets`.`deleted`=0 AND IFNULL(`ticket_schedule`.`deleted`,0)=0 AND IFNULL(NULLIF(IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`),''),'$date')='$date' AND IFNULL(IFNULL(`ticket_schedule`.`city`,`tickets`.`city`),'') != '' AND CONCAT(',',IFNULL(`tickets`.`region`,''),',') LIKE '%,$region,%' AND CONCAT(',',IFNULL(`tickets`.`con_location`,''),',') LIKE '%,$location,%' AND CONCAT(',',IFNULL(`tickets`.`classification`,''),',') LIKE '%,$classification,%' AND IFNULL(NULLIF(IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`),''),0)=0 AND TRIM(IF(`ticket_schedule`.`id` IS NULL, CONCAT(IFNULL(`tickets`.`address`,''),',',IFNULL(`tickets`.`city`,'')),CONCAT(IFNULL(`ticket_schedule`.`address`,''),',',IFNULL(`ticket_schedule`.`city`,'')))) != '' AND (IFNULL(`type`,'') != 'warehouse' AND TRIM(IF(`ticket_schedule`.`id` IS NULL, CONCAT(IFNULL(`tickets`.`address`,''),',',IFNULL(`tickets`.`city`,'')),CONCAT(IFNULL(`ticket_schedule`.`address`,''),',',IFNULL(`ticket_schedule`.`city`,'')))) NOT IN (SELECT TRIM(CONCAT(IFNULL(`address`,''),IFNULL(`city`,''))) FROM `contacts` WHERE `category`='Warehouses'))");
$equipment_list = $dbc->query("SELECT `equipmentid`, CONCAT(`category`,': ',`make`,' ',`model`,' ',`unit_number`) `label` FROM `equipment` WHERE `deleted`=0 ORDER BY `make`, `model`, `unit_number`")->fetch_all(MYSQLI_ASSOC);
if($map_url != '') {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://maps.google.com/maps/api/geocode/xml?address='.urlencode(implode(',',$city_target)).'&key='.EMBED_MAPS_KEY);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$map_center = simplexml_load_string(curl_exec($ch))->result->geometry->location;
	curl_close($ch); ?>
	<script>
    zoom = <?= $zoom ?>;
	var height = $('.map_view').height() / 2;
	var width = $('.map_view').width() / 2;
	</script>
	<img style="margin:auto;" src="<?= $map_url ?>" class="map_img">
    <img class="inline-img cursor-hand no-toggle" title="Zoom In Map" style="position:absolute;top:calc(<?= $_GET['y'] ?>px - 4.5em);left:calc(<?= $_GET['x'] ?>px + 20% - 2.5em);" onclick="if(zoom < 16) { zoom++; get_map_view(); } else { console.log('Maximum Zoom Reached!'); }" src="../img/icons/ROOK-ZoomIn-icon.png">
    <img class="inline-img cursor-hand no-toggle" title="Zoom Out Map" style="position:absolute;top:calc(<?= $_GET['y'] ?>px - 2.5em);left:calc(<?= $_GET['x'] ?>px + 20% - 2.5em);" onclick="if(zoom > 6) { zoom--; get_map_view(); } else { console.log('Minimum Zoom Reached!'); }" src="../img/icons/ROOK-ZoomOut-icon.png">
	<?php while($ticket = $ticket_list->fetch_assoc()) {
        $coordinates = explode('#*#',$ticket['coordinates']);
        if(!empty(array_filter($coordinates))) {
            $ticket_coordinates = new stdClass();
            $ticket_coordinates->lat = $coordinates[0];
            $ticket_coordinates->lng = $coordinates[1];
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://maps.google.com/maps/api/geocode/xml?address='.urlencode($ticket['address']).'&key='.EMBED_MAPS_KEY);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $ticket_coordinates = simplexml_load_string(curl_exec($ch))->result->geometry->location;
            curl_close($ch);
            if($ticket['table'] == 'ticket_schedule' && !($ticket['coord_auto'] > 0)) {
                set_field_value($ticket_coordinates->lat.'#*#'.$ticket_coordinates->lng,'coordinates',$ticket['table'],$ticket['id_field'],$ticket['id']);
            }
        }
        if($ticket_coordinates->lat > 0 || $ticket_coordinates->lat < 0 || $ticket_coordinates->lng > 0 || $ticket_coordinates->lng < 0) { ?>
            <script>
            var y_pos = Math.round(height * <?= isset($zoom_ratios[$zoom]) ? $zoom_ratios[$zoom][0] : $zoom_ratios[10][0] ?> * <?= $ratio > 1 ? $ratio : 1 ?> * (<?= $map_center->lat ?> - <?= $ticket_coordinates->lat ?>) + height - 20);
            var x_pos = Math.round(width * <?= isset($zoom_ratios[$zoom]) ? $zoom_ratios[$zoom][1] : $zoom_ratios[10][1] ?> * <?= $ratio > 1 ? 1 : $ratio ?> * (<?= $ticket_coordinates->lng ?> - <?= $map_center->lng ?>) + width - 10);
            if(y_pos > 0 && x_pos > 0 && y_pos < height * 2 && x_pos < width * 2) {
                $('.map_view').append('<span class="cursor-hand ticket" data-table="<?= $ticket['table'] ?>" data-id-field="<?= $ticket['id_field'] ?>" data-id="<?= $ticket['id'] ?>" style="position:absolute;top:'+y_pos+'px;left:calc(20% + '+x_pos+'px);"><img class="no-toggle drag-handle" title="<?= $ticket['ticket_label'] ?>" src="../img/icons/location-pin.png" style="height:20px;width:20px;"></span>');
            }
            </script>
        <?php } ?>
	<?php } ?>
	<script>
	$(document).ready(function() {
		initOptions();
		$('.map_view').find('img.map_img').css('width',(width * 2) + 'px').css('height',(height * 2) + 'px').on('dragstart', function(event) { event.preventDefault(); });
		initTooltips();
		$('.map_view img.map_img').mousedown(function() {
			$('.draw_sort').empty();
            first_dot = '';
			$(document).mousemove(mouseMove);
			$(document).mouseup(doneDraw);
		})
	});
	function doneDraw() {
		ticket_list = [];
		$('.drag-handle').each(function() {
			var y = $(this).closest('span').offset().top;
			var x = ($(this).closest('span').offset().left+10);
			if($('.drawn_dot').filter(function() { return $(this).offset().top < y && $(this).offset().left < x; }).length > 0
				&& $('.drawn_dot').filter(function() { return $(this).offset().top < y && $(this).offset().left > x; }).length > 0
				&& $('.drawn_dot').filter(function() { return $(this).offset().top > y && $(this).offset().left < x; }).length > 0
				&& $('.drawn_dot').filter(function() { return $(this).offset().top > y && $(this).offset().left > x; }).length > 0) {
				ticket_list.push(this);
			}
		});
        var first_dot = $('.drawn_dot').first();
		var final_dot = $('.drawn_dot').last();
        if(first_dot != final_dot) {
            var color = '#000000';
            var size = '2px';
			var init_x = first_dot.offset().left;
			var init_y = first_dot.offset().top;
			var fin_x = final_dot.offset().left;
			var fin_y = final_dot.offset().top;
            var off_x = $('.draw_sort').offset().left;
            var off_y = $('.draw_sort').offset().top;
			var diff_x = fin_x - init_x;
			var diff_y = fin_y - init_y;
			var x_inc = (Math.abs(diff_x) > Math.abs(diff_y) ? (diff_x > 0 ? 1 : -1) : diff_x / Math.abs(diff_y));
			for(var i = init_x; (i < fin_x && diff_x > 0) || (i > fin_x && diff_x < 0); i += x_inc) {
				init_y += (diff_y / (diff_x / x_inc));
				$(".draw_sort").append(
					$('<div class="drawn_dot"></div>')
						.css('position', 'absolute')
						.css('top', (init_y - off_y) + 'px')
						.css('left', (i - off_x) + 'px')
						.css('width', size)
						.css('height', size)
						.css('background-color', color)
				);
			}
        }
		if(final_dot.length > 0) {
			$(".draw_sort").append('<img class="inline-img drag_handle cursor-hand" src="../img/icons/drag_handle.png" style="max-width:100px;position:absolute;top:'+(final_dot.offset().top+8-$('.draw_sort').offset().top)+'px;left:'+(final_dot.offset().left+8-$('.draw_sort').offset().left)+'px;z-index:1;">');
		}
		$(document).off('mousemove',mouseMove).off('mouseup',doneDraw);
		initDraw();
	}
	function mouseMove(e) {
        var color = '#000000';
        var size = '2px';
		var prev_dot = $('.drawn_dot').last();
		var off_x = $('.draw_sort').offset().left;
		var off_y = $('.draw_sort').offset().top;
		if(prev_dot.length > 0) {
			var prev_x = prev_dot.offset().left;
			var prev_y = prev_dot.offset().top;
			var diff_x = e.pageX - prev_x;
			var diff_y = e.pageY - prev_y;
			var x_inc = (Math.abs(diff_x) > Math.abs(diff_y) ? (diff_x > 0 ? 1 : -1) : diff_x / Math.abs(diff_y));
			for(var i = prev_x; (i < e.pageX && diff_x > 0) || (i > e.pageX && diff_x < 0); i += x_inc) {
				prev_y += (diff_y / (diff_x / x_inc));
				$(".draw_sort").append(
					$('<div class="drawn_dot"></div>')
						.css('position', 'absolute')
						.css('top', (prev_y - off_y) + 'px')
						.css('left', (i - off_x) + 'px')
						.css('width', size)
						.css('height', size)
						.css('background-color', color)
				);
			}
		}
        $(".draw_sort").append(
            $('<div class="drawn_dot"></div>')
                .css('position', 'absolute')
                .css('top', (e.pageY - off_y) + 'px')
                .css('left', (e.pageX - off_x) + 'px')
                .css('width', size)
                .css('height', size)
                .css('background-color', color)
        );
	}
	</script>
<?php } else if($ticket_list->num_rows > 0) {
	while($ticket = $ticket_list->fetch_assoc()) { ?>
		<h4><?= $ticket['ticket_label'] ?></h4>
		<div class="form-group">
			<label class="col-sm-4">Equipment:</label>
			<div class="col-sm-8">
				<select class="chosen-select-deselect" data-placeholder="Select Equipment" name="equipmentid" data-table="<?= $ticket['table'] ?>" data-id-field="<?= $ticket['id_field'] ?>" data-id="<?= $ticket['id'] ?>"><option />
					<?php foreach($equipment_list as $equipment) { ?>
						<option <?= $ticket['equipment'] == $equipment['equipmentid'] ? 'selected' : '' ?> value="<?= $equipment['equipmentid'] ?>"><?= $equipment['label'] ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4">Scheduled:</label>
			<div class="col-sm-2">
				<input type="text" name="to_do_date" data-table="<?= $ticket['table'] ?>" data-id-field="<?= $ticket['id_field'] ?>" data-id="<?= $ticket['id'] ?>" value="<?= $ticket['to_do_date'] ?>" class="form-control datepicker" placeholder="Scheduled Date">
			</div>
			<div class="col-sm-2">
				<input type="text" name="to_do_start_time" data-table="<?= $ticket['table'] ?>" data-id-field="<?= $ticket['id_field'] ?>" data-id="<?= $ticket['id'] ?>" value="<?= $ticket['to_do_start_time'] ?>" class="form-control datetimepicker" placeholder="Scheduled Start">
			</div>
			<div class="col-sm-2">
				<input type="text" name="to_do_end_time" data-table="<?= $ticket['table'] ?>" data-id-field="<?= $ticket['id_field'] ?>" data-id="<?= $ticket['id'] ?>" value="<?= $ticket['to_do_end_time'] ?>" class="form-control datetimepicker" placeholder="Scheduled End">
			</div>
		</div>
	<?php }
} else {
	echo '<h3>No Unscheduled '.TICKET_TILE.' Found</h3>';
}