<?php include_once('../include.php');
include_once('../Dispatch/dashboard_functions.php');
include_once('../Dispatch/config.php');
ob_clean();

$daily_date = $_POST['date'];
$equipmentid = $_POST['equipmentid'];
$completed_tickets = 0;
$total_tickets = 0;
$latest_updated_time = 0;

$equipment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT *, CONCAT(' #', `unit_number`) label, `region` FROM `equipment` WHERE `equipmentid` = '$equipmentid'"));

$equip_assign = mysqli_fetch_array(mysqli_query($dbc, "SELECT ea.*, e.*, ea.`notes`, ea.`classification` FROM `equipment_assignment` ea LEFT JOIN `equipment` e ON ea.`equipmentid` = e.`equipmentid` WHERE e.`equipmentid` = '".$equipment['equipmentid']."' AND ea.`deleted` = 0 AND DATE(`start_date`) <= '$daily_date' AND DATE(ea.`end_date`) >= '$daily_date' AND CONCAT(',',ea.`hide_days`,',') NOT LIKE '%,$daily_date,%' ORDER BY ea.`start_date` DESC, ea.`end_date` ASC, e.`category`, e.`unit_number`"));
$equipment_assignmentid = $equip_assign['equipment_assignmentid'];

$equip_regions = $equipment['region'].'*#*'.$equip_assign['region_list'];
$equip_locations = $equipment['location'].'*#*'.$equip_assign['location_list'];
$equip_classifications = $equipment['classification'].'*#*'.$equip_assign['classification_list'];

$equip_regions = array_filter(array_unique(explode('*#*', $equip_regions)));
$equip_locations = array_filter(array_unique(explode('*#*', $equip_locations)));
$equip_classifications = array_filter(array_unique(explode('*#*', $equip_classifications)));

//POPULATE
$summary_result = [];
$warehouse_query = '';
if($combine_warehouses == 1 || $dont_count_warehouse > 0) {
	$warehouse_query = " AND IFNULL(`ticket_schedule`.`type`,'') NOT LIKE '%warehouse%' AND REPLACE(REPLACE(IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''),IFNULL(`tickets`.`city`,''))),' ',''),'-','') NOT IN (SELECT REPLACE(REPLACE(CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')),' ',''),'-','') FROM `contacts` WHERE `category`='Warehouses')";
}

$pickup_query = '';
if($combine_pickups == 1) {
	$pickup_query = " AND `ticket_schedule`.`type` != 'Pick Up'";
}

$all_tickets_sql = "SELECT `tickets`.*, `ticket_schedule`.`id` `stop_id`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`equipment_assignmentid`,`tickets`.`equipment_assignmentid`) `equipment_assignmentid`, IFNULL(`ticket_schedule`.`teamid`,`tickets`.`teamid`) `teamid`, IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`) `contactid`, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`id`, 0) `ticket_scheduleid`, IFNULL(`ticket_schedule`.`last_updated_time`,`tickets`.`last_updated_time`) `last_updated_time`, CONCAT(' - ',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name`, IFNULL(`ticket_schedule`.`address`,`tickets`.`pickup_address`) `pickup_address`, IFNULL(`ticket_schedule`.`city`,`tickets`.`pickup_city`) `pickup_city`, `ticket_schedule`.`notes` `delivery_notes`, `tickets`.`businessid`, CONCAT(`start_available`,' - ',`end_available`) `availability`, `ticket_schedule`.`end_available` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done')".$warehouse_query.$pickup_query.$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query.$ticket_customer_query;
$tickets = mysqli_fetch_all(mysqli_query($dbc, $all_tickets_sql),MYSQLI_ASSOC);

foreach($tickets as $ticket) {
	$total_tickets++;
	if(in_array($ticket['status'],$calendar_checkmark_status)) {
		$completed_tickets++;
	}
	$customer_notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid` = '".$ticket['ticketid']."' AND `src_table` = 'customer_approve' AND `line_id` = '".$ticket['stop_id']."' AND `deleted` = 0"));
	if(strtotime($customer_notes['completed_time']) > $latest_updated_time) {
		$latest_updated_time = strtotime($customer_notes['completed_time']);
	}
	$time_compare = $ticket['to_do_date'].(!empty($ticket['end_available']) ? date('H:i:s', strtotime($ticket['end_available'])) : date('H:i:s', strtotime($ticket['to_do_start_time'].' + '.$delivery_timeframe_default.' hours')));
	$customer_notes['completed_time'] = empty(str_replace('0000-00-00 00:00:00','',$customer_notes['completed_time'])) ? date('Y-m-d H:i:s') : convert_timestamp_mysql($dbc, $customer_notes['completed_time']);
	if(strtotime($customer_notes['completed_time']) > strtotime($time_compare)) {
		$summary_result['Out Of Window']['count']++;
		$summary_result['Out Of Window']['label'] = 'Out Of Window';
	} else if($customer_notes['completed'] == 1 && strtotime($customer_notes['completed_time']) <= strtotime($time_compare)) {
		$summary_result['On Time']['count']++;
		$summary_result['On Time']['label'] = 'On Time';
	} else {
		$summary_result['Ongoing']['count']++;
		$summary_result['Ongoing']['label'] = 'Ongoing';
	}
}

$title_color = get_equipment_color($equipment['equipmentid']);

$border_styling = '';

$region_label = '';
if($group_regions == 1) {
	$region_label = ' ('.str_replace('*#*', ', ', $equipment['region']).')';
}

$truck_svg = draw_svg_truck($summary_result['On Time']['count'], $summary_result['Out Of Window']['count'], $summary_result['Ongoing']['count']);
$summary_html = '<div class="dispatch-summary-tab" data-equipment="'.$equipmentid.'" data-region=\''.json_encode($equip_regions).'\' data-location=\''.json_encode($equip_locations).'\' data-classification=\''.json_encode($equip_classifications).'\' data-latest-updated-time="'.$latest_updated_time.'" '.$border_styling.'><a href="" onclick="summary_select_equipment(this); return false;"><div class="dispatch-summary-title" style="background-color: #'.$title_color.'"><b>'.$equipment['label'].'</b>'.$region_label.' - Completed '.$completed_tickets.' of '.$total_tickets.' '.TICKET_TILE.'</div></a><a href="" onclick="summary_select_equipment(this); return false;"><div class="dispatch-summary-tab-truck">'.$truck_svg.'</div></a><div class="dispatch-summary-tab-block"></div></div>';

$ontime_summary_arr = [
	[
		'label' => 'On Time',
		'count' => empty($summary_result['On Time']['count']) ? 0 : $summary_result['On Time']['count'],
		'color' => '#00ff00'
	],
	[
		'label' => 'Out Of Window',
		'count' => empty($summary_result['Out Of Window']['count']) ? 0 : $summary_result['Out Of Window']['count'],
		'color' => '#ff0000'
	],
	[
		'label' => 'Ongoing',
		'count' => empty($summary_result['Ongoing']['count']) ? 0 : $summary_result['Ongoing']['count'],
		'color' => '#ddd'
	]
];

$result_list = [
	'equipmentid'=>$equipment['equipmentid'],
	'ontime_summary'=>$ontime_summary_arr,
	'html'=>$summary_html,
	'latest_updated_time'=>$latest_updated_time
];
echo json_encode($result_list);