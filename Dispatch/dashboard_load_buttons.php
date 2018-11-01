<?php include_once('../include.php');
include_once('../Dispatch/dashboard_functions.php');
include_once('../Dispatch/config.php');
ob_clean();

$daily_date = $_POST['date'];
$active_equipment = json_decode($_POST['active_equipment']);

$equip_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT *, CONCAT(' #', `unit_number`) label, `region` FROM `equipment` WHERE `deleted`=0 ".$equip_cat_query." $allowed_equipment_query $customer_query ORDER BY ".($group_regions == 1 ? "IFNULL(NULLIF(`region`,''),'ZZZ'), " : '')." `label`"),MYSQLI_ASSOC);

//POPULATE
$equipment_buttons = [];
$equipment_active_li = [];
foreach($equip_list as $equipment) {
	$equip_assign = mysqli_fetch_array(mysqli_query($dbc, "SELECT ea.*, e.*, ea.`notes`, ea.`classification` FROM `equipment_assignment` ea LEFT JOIN `equipment` e ON ea.`equipmentid` = e.`equipmentid` WHERE e.`equipmentid` = '".$equipment['equipmentid']."' AND ea.`deleted` = 0 AND DATE(`start_date`) <= '$daily_date' AND DATE(ea.`end_date`) >= '$daily_date' AND CONCAT(',',ea.`hide_days`,',') NOT LIKE '%,$daily_date,%' ORDER BY ea.`start_date` DESC, ea.`end_date` ASC, e.`category`, e.`unit_number`"));
	$equipment_assignmentid = $equip_assign['equipment_assignmentid'];

	$equip_regions = $equipment['region'].'*#*'.$equip_assign['region_list'];
	$equip_locations = $equipment['location'].'*#*'.$equip_assign['location_list'];
	$equip_classifications = $equipment['classification'].'*#*'.$equip_assign['classification_list'];
	
	$equip_regions = array_filter(array_unique(explode('*#*', $equip_regions)));
	$equip_locations = array_filter(array_unique(explode('*#*', $equip_locations)));
	$equip_classifications = array_filter(array_unique(explode('*#*', $equip_classifications)));
	
	$all_tickets_sql = "SELECT `tickets`.*, `ticket_schedule`.`id` `stop_id`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`equipment_assignmentid`,`tickets`.`equipment_assignmentid`) `equipment_assignmentid`, IFNULL(`ticket_schedule`.`teamid`,`tickets`.`teamid`) `teamid`, IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`) `contactid`, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`id`, 0) `ticket_scheduleid`, IFNULL(`ticket_schedule`.`last_updated_time`,`tickets`.`last_updated_time`) `last_updated_time`, CONCAT(' - ',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name`, IFNULL(`ticket_schedule`.`address`,`tickets`.`pickup_address`) `pickup_address`, IFNULL(`ticket_schedule`.`city`,`tickets`.`pickup_city`) `pickup_city`, `ticket_schedule`.`notes` `delivery_notes`, `tickets`.`businessid`, CONCAT(`start_available`,' - ',`end_available`) `availability`, `ticket_schedule`.`end_available` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done')".$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query.$ticket_customer_query." LIMIT 1";
	$tickets = mysqli_fetch_all(mysqli_query($dbc, $all_tickets_sql),MYSQLI_ASSOC);

	if(get_config($dbc, 'dispatch_tile_hide_empty') != 1 || !empty($tickets)) {
		$title_color = get_equipment_color($equipment['equipmentid']);
		$region_label = (empty($equipment['region']) ? 'No Region' : implode(', ',explode('*#*', $equipment['region'])));
		$region_key = array_search($equipment['region'], $contact_regions);
		$region_colour = $region_colours[$region_key];
		$region_html = '<div class="dispatch-region-block small" style="border: 0; background-color: '.$region_colour.'" data-region="'.$equipment['region'].'">'.$region_label.'</div>';

		$equipment_buttons[] = [
			'region'=>[
				'label'=>$region_label,
				'color'=>$region_colour,
				'html'=>$region_html
			],
			'html'=>'<a href="" data-region=\''.json_encode($equip_regions).'\' data-location=\''.json_encode($equip_locations).'\' data-classification=\''.json_encode($equip_classifications).'\' data-equipment="'.$equipment['equipmentid'].'" data-activevalue="'.$equipment['equipmentid'].'" class="dispatch-equipment-button-a" onclick="select_equipment(this); return false;"><div class="pull-left" style="background-color: #'.$title_color.'; width: 20px; height: 20px; margin: 5px;"></div><li data-equipment="'.$equipment['equipmentid'].'" class="dispatch-equipment-button '.(($reset_active != 1 && (empty($active_equipment) || in_array($equipment['equipmentid'], $active_equipment))) || ($reset_active == 1 && !empty($tickets)) ? 'active blue' : '').'">'.$equipment['label'].'</li><div class="clearfix"></div></a>'];
		$equipment_active_li[] = '<a data-activevalue="'.$equipment['equipmentid'].'" class="active_li_item" style="cursor: pointer;"><li class="active blue">'.$equipment['label'].'</li></a>';
	}
}

$result_list = [
	'buttons' => $equipment_buttons,
	'active_li' => $equipment_active_li
];
echo json_encode($result_list);