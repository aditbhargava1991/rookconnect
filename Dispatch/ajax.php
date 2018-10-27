<?php
include_once('../include.php');
ob_clean();

if($_GET['action'] == 'save_config') {
	$field = $_POST['field'];
	$value = filter_var(htmlentities($_POST['value']),FILTER_SANITIZE_STRING);
	set_config($dbc, $field, $value);
} else if($_GET['action'] == 'ticket_status_color') {
	$status = $_POST['status'];
    $color_code = $_POST['color'];
    mysqli_query($dbc, "INSERT INTO `field_config_ticket_status_color` (`status`) SELECT '$status' FROM (SELECT COUNT(*) rows FROM `field_config_ticket_status_color` WHERE `status` = '$status') num WHERE num.rows = 0");
    mysqli_query($dbc, "UPDATE `field_config_ticket_status_color` SET `color` = '$color_code' WHERE `status` = '$status'");
} else if($_GET['action'] == 'sort_equipment_latest_updated') {
	include_once('../Dispatch/config.php');
	ob_clean();

	$daily_date = $_POST['date'];
	$equipments = json_decode($_POST['equipment']);
	$equip_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT `equipmentid` FROM `equipment` WHERE `equipmentid` IN (".implode(',',$equipments).")"),MYSQLI_ASSOC);
	$equip_times = [];
	foreach($equip_list as $equipment) {
		$latest_updated_time = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT MAX(`ticket_attached`.`completed_time`) `latest_updated_time` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted` = 0 LEFT JOIN `ticket_attached` ON `ticket_schedule`.`id` = `ticket_attached`.`line_id` AND `ticket_attached`.`src_table` = 'customer_approve' AND `ticket_attached`.`deleted` = 0 WHERE ('".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done')".$warehouse_query.$pickup_query.$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query.$ticket_customer_query))['latest_updated_time'];
		if(strtotime($latest_updated_time) > 0) {
			$equip_times[$equipment['equipmentid']] = $latest_updated_time;
		}
	}
	arsort($equip_times);
	$equip_sort = [];
	foreach($equip_times as $equipmentid => $latest_updated_time) {
		$equip_sort[] = $equipmentid;
	}
	foreach($equipments as $equipmentid) {
		if(!in_array($equipmentid, $equip_sort)) {
			$equip_sort[] = $equipmentid;
		}
	}
	echo json_encode($equip_sort);
}