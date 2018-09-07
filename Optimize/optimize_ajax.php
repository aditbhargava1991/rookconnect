<?php include_once('../include.php');
ob_clean();

if($_GET['action'] == 'add_macro') {
	set_config($dbc, 'upload_macros', filter_var(implode('#*#',$_POST['value']),FILTER_SANITIZE_STRING));
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
	$max_start = $dbc->query("SELECT MAX(`to_do_start_time`) FROM (SELECT `to_do_start_time` FROM `ticket_schedule` WHERE `equipmentid`='$equipmentid' AND `to_do_date`='$date' AND `deleted`=0 UNION SELECT `to_do_start_time` FROM `tickets` WHERE `equipmentid`='$equipmentid' AND `to_do_date`='$date' AND `deleted`=0) `times`")->fetch_array()[0];
	if($max_start == '') {
		$max_start = '7:00 AM';
	}
	$start_time = date('g:i a',strtotime($max_start.' + 1 hour'));
	$dbc->query("UPDATE `$table` SET `status`='$default_status', `to_do_date`='$date', `equipmentid`='$equipmentid', `to_do_start_time`=".($table == 'ticket_schedule' ? "IF(`scheduled_lock`='1',`to_do_start_time`,'$start_time')" : "'$start_time'")." WHERE `$id_field`='$id'");
	//Update warehouse stop to match above
	$dbc->query("UPDATE `ticket_schedule` SET `status`='$default_status', `to_do_date`='$date', `equipmentid`='$equipmentid' WHERE `ticketid` = '$ticketid' AND (`type` = 'warehouse' OR IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),'') IN (SELECT CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')) FROM `contacts` WHERE `category`='Warehouses'))");
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
		$increment = '30 minutes';
	}
	$available_increment = get_config($dbc, 'delivery_timeframe_default');
	$dbc->query("INSERT INTO `ticket_history` (`ticketid`,`userid`,`src`,`description`) VALUES ($ticketid,".$_SESSION['contactid'].",'optimizer','Deliveries assigned to be completed at $start_time on $date at increments of $increment.')");
	$stops = $dbc->query("SELECT `id` FROM `ticket_schedule` WHERE `ticketid`='$ticket' AND `deleted`=0 ORDER BY `id`");
	while($stop = $stops->fetch_assoc()) {
		$start_available = $start_time;
		$end_available = date('H:i',strtotime($start_time.' + '.$available_increment.' hours'));
		$dbc->query("UPDATE `ticket_schedule` SET `to_do_start_time`='$start_time', `start_available`='$start_available', `end_available`='$end_available', `equipmentid`='$equipmentid' WHERE `id`='".$stop['id']."'");
		$start_time = date('H:i',strtotime($start_time.' + '.$increment));
	}
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