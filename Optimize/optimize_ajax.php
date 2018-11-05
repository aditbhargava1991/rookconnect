<?php include_once('../include.php');
ob_clean();

if($_GET['action'] == 'add_macro') {
	set_config($dbc, 'upload_macros', filter_var(implode('#*#',$_POST['value']),FILTER_SANITIZE_STRING));
	set_config($dbc, 'upload_macro_businesses', filter_var(implode('#*#',$_POST['businesses']),FILTER_SANITIZE_STRING));
}
if($_GET['action'] == 'bb_macro_warehouse_assignments') {
	set_config($dbc, 'bb_macro_warehouse_assignments', filter_var(implode('#*#',$_POST['value']),FILTER_SANITIZE_STRING));
}
else if($_GET['action'] == 'lock') {
	$region = filter_var($_POST['region'],FILTER_SANITIZE_STRING);
	if($region != '') {
		set_config($dbc, 'region_lock_'.config_safe_str($region), time());
	}
	$location = filter_var($_POST['location'],FILTER_SANITIZE_STRING);
	if($location != '') {
		set_config($dbc, 'location_lock_'.config_safe_str($location), time());
	}
	$classification = filter_var($_POST['classification'],FILTER_SANITIZE_STRING);
	if($classification != '') {
		set_config($dbc, 'classification_lock_'.config_safe_str($classification), time());
	}
}
else if($_GET['action'] == 'assign_ticket') {
	$equipmentid = filter_var($_POST['equipment'],FILTER_SANITIZE_STRING);
	$table = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
	$id_field = filter_var($_POST['id_field'],FILTER_SANITIZE_STRING);
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	$default_status = get_config($dbc, 'ticket_default_status');
	$ticketid = $dbc->query("SELECT `ticketid` FROM `$table` WHERE `$id_field`='$id'")->fetch_array()[0];
	$max_start = $dbc->query("SELECT MAX(`to_do_end_time`) FROM (SELECT IFNULL(NULLIF(`to_do_end_time`,''),`to_do_start_time`) `to_do_end_time` FROM `ticket_schedule` WHERE `equipmentid`='$equipmentid' AND `to_do_date`='$date' AND `deleted`=0 AND `type` != 'warehouse' AND IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),'') NOT IN (SELECT CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')) FROM `contacts` WHERE `category`='Warehouses') UNION SELECT IFNULL(NULLIF(`to_do_end_time`,''),`to_do_start_time`) `to_do_end_time` FROM `tickets` WHERE `equipmentid`='$equipmentid' AND `to_do_date`='$date' AND `deleted`=0) `times`")->fetch_array()[0];
	if($max_start == '') {
		$max_start = '07:00';
	}
    $warehouse_start_time = get_config($dbc, 'ticket_warehouse_start_time');
	$start_time = date('H:i',strtotime($max_start) + 1800);
	$dbc->query("UPDATE `$table` SET `status`='$default_status', `to_do_date`='$date', `equipmentid`='$equipmentid', `to_do_start_time`=".($table == 'ticket_schedule' ? "IF(`scheduled_lock`='1',`to_do_start_time`,'$start_time')" : "'$start_time'")." WHERE `$id_field`='$id'");
	//Update warehouse stop to match above equipment and date
	$dbc->query("UPDATE `ticket_schedule` SET `status`='$default_status', `to_do_date`='$date', `equipmentid`='$equipmentid' WHERE `deleted` = 0 AND `ticketid` = '$ticketid' AND (`type` = 'warehouse' OR IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),'') IN (SELECT CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')) FROM `contacts` WHERE `category`='Warehouses'))");
	//Update first warehouse stop to warehouse start time
	$warehouse_hours = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts`.`hours_of_operation` FROM `ticket_schedule` LEFT JOIN `contacts` ON IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),'') = CONCAT(IFNULL(`contacts`.`address`,''),IFNULL(`contacts`.`city`,'')) AND `contacts`.`category`='Warehouses' WHERE `ticket_schedule`.`deleted` = 0 AND `ticket_schedule`.`ticketid` = '$ticketid' AND (`ticket_schedule`.`type` = 'warehouse' OR IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),'') IN (SELECT CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')) FROM `contacts` WHERE `category`='Warehouses')) ORDER BY `id` ASC LIMIT 1"))['hours_of_operation'];
	$day_i = date('w',strtotime($date));
	$hours = explode('-',explode(',',$warehouse_hours)[$day_i]);
	$hop_start_time = !empty($hours[0]) ? date('H:i', strtotime($hours[0])) : '';
	$hop_end_time = !empty($hours[1]) ? date('H:i', strtotime($hours[1])) : '';
	if(!empty($hop_start_time) && !empty($hop_end_time)) {
		if(strtotime($warehouse_start_time) < strtotime($hop_start_time)) {
			$warehouse_start_time = date('h:i a',strtotime($hop_start_time));
		}
		if(strtotime($warehouse_start_time) > strtotime($hop_end_time)) {
			$warehouse_start_time = date('h:i a',strtotime($hop_end_time));
		}
	}

	$dbc->query("UPDATE `ticket_schedule` SET `to_do_start_time` = '$warehouse_start_time' WHERE `deleted` = 0 AND `ticketid` = '$ticketid' AND `id` = (SELECT `id` FROM (SELECT `id` FROM `ticket_schedule` WHERE `deleted` = 0 AND `ticketid` = '$ticketid' AND (`type` = 'warehouse' OR IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),'') IN (SELECT CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')) FROM `contacts` WHERE `category`='Warehouses')) ORDER BY `id` ASC LIMIT 1) as `schedule_check`)");
	//Update last warehouse stop (if exists) to 30 minutes after the latest stop of the day for all stops of that day for that equipment
	$warehouse_end_time = date('H:i', strtotime($start_time) + 1800);
	$dbc->query("UPDATE `ticket_schedule` SET `to_do_start_time` = '$warehouse_end_time' WHERE `id` IN (SELECT `id` FROM (SELECT `id` FROM `ticket_schedule` `main_table` WHERE `equipmentid`='$equipmentid' AND `to_do_date`='$date' AND `deleted`=0 AND (`type` = 'warehouse' OR IFNULL(NULLIF(CONCAT(IFNULL(`main_table`.`address`,''),IFNULL(`main_table`.`city`,'')),''),'') IN (SELECT CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')) FROM `contacts` WHERE `category`='Warehouses')) AND (SELECT COUNT(`id`) FROM `ticket_schedule` WHERE `equipmentid` = '$equipmentid' AND `to_do_date`='$date' AND `ticketid`=`main_table`.`ticketid` AND `deleted` = 0 AND (`type` = 'warehouse' OR IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),'') IN (SELECT CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')) FROM `contacts` WHERE `category`='Warehouses'))) > 1 AND `id` = (SELECT `id` FROM `ticket_schedule` WHERE `equipmentid` = '$equipmentid' AND `to_do_date`='$date' AND `ticketid`=`main_table`.`ticketid` AND `deleted` = 0 AND (`type` = 'warehouse' OR IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),'') IN (SELECT CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')) FROM `contacts` WHERE `category`='Warehouses')) ORDER BY `id` DESC LIMIT 1)) `warehouse_stops`)");

	$dbc->query("INSERT INTO `ticket_history` (`ticketid`,`userid`,`src`,`description`) VALUES ($ticketid,".$_SESSION['contactid'].",'optimizer','".($table == 'tickets' ? TICKET_NOUN : 'Delivery (ID: '.$id.')')." assigned to be completed at $start_time on $date.')");
}
else if($_GET['action'] == 'assign_ticket_deliveries') {
	$equipmentid = filter_var($_POST['equipment'],FILTER_SANITIZE_STRING);
	$ticket = filter_var($_POST['ticket'],FILTER_SANITIZE_STRING);
	$start_time = filter_var($_POST['start'],FILTER_SANITIZE_STRING);
	$increment = filter_var($_POST['increment'],FILTER_SANITIZE_STRING);
	if($start_time == '') {
		$start_time = '08:00';
	}
	$start_time = date('H:i',strtotime($start_time));
	if($increment == '') {
		$increment = get_config($dbc, 'scheduling_increments').' minutes';
	}
	if($increment == '') {
		$increment = '30 minutes';
	}
	$available_increment = get_config($dbc, 'delivery_timeframe_default');
	$dbc->query("INSERT INTO `ticket_history` (`ticketid`,`userid`,`src`,`description`) VALUES ($ticketid,".$_SESSION['contactid'].",'optimizer','Deliveries assigned to be completed at $start_time on $date at increments of $increment.')");
	$stops = $dbc->query("SELECT `id`, `est_time` FROM `ticket_schedule` WHERE `ticketid`='$ticket' AND `deleted`=0 ORDER BY `id`");
	while($stop = $stops->fetch_assoc()) {
        $est_time = $stop['est_time'];
        $end_time = date('H:i', strtotime($start_time) + ($est_time > 0 ? $est_time * 3600 : 1800));
		$increment_time = get_config($dbc, 'scheduling_increments');
        $increment_time = ($increment_time > 0 ? $increment_time * 60 : 1800);
        $start_available = $start_time;
		$end_available = date('H:i',strtotime($start_time.' + '.$available_increment.' hours'));
		$dbc->query("UPDATE `ticket_schedule` SET `to_do_start_time`='$start_time', `to_do_end_time`='$end_time', `start_available`='$start_available', `end_available`='$end_available', `equipmentid`='$equipmentid' WHERE `id`='".$stop['id']."'");
        $date = get_field_value('to_do_date','ticket_schedule','id',$stop['id']);
        $start_time = $end_time;
	}
    echo $date;
}
else if($_GET['action'] == 'archive') {
    $id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
    $field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
    $table = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
    if($table == 'tickets' && $field == 'ticketid') {
        $dbc->query("UPDATE `ticket_schedule` SET `deleted`=1 WHERE `$field`='$id'");
        $ticketid = $id;
    } else {
        $ticketid = $dbc->query("SELECT `ticketid` FROM `$table` WHERE `$field`='$id'")->fetch_array()[0];
    }
    $dbc->query("UPDATE `$table` SET `deleted`=0 WHERE `$field`='$id'");
    $dbc->query("INSERT INTO `ticket_history` (`ticketid`,`userid`,`src`,`description`) VALUES ('$ticketid','".$_SESSION['contactid']."','Trip Optimizer','Row #$id of $table archived')");
}
else if($_GET['action'] == 'tile_settings') {
	set_config($dbc, 'optimize_dont_count_warehouse', filter_var($_POST['optimize_dont_count_warehouse'],FILTER_SANITIZE_STRING));
}
else if($_GET['action'] == 'stop_types') {
	set_config($dbc, 'optimize_stop_types', $_POST['optimize_stop_types']);
}