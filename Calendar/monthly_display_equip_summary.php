<?php
$region_list = explode(',',get_config($dbc, '%_region', true));
$region_colours = explode(',',get_config($dbc, '%_region_colour', true));
$allowed_regions_arr = [];
foreach($allowed_regions as $allowed_region) {
	$allowed_regions_arr[] = " CONCAT(',',`tickets`.`region`,',') LIKE '%,$allowed_region,%'";
}
$allowed_regions_arr[] = " IFNULL(`tickets`.`region`,'') = ''";
$allowed_regions_query = " AND (".implode(' OR ', $allowed_regions_arr).")";

$allowed_locations_arr = [];
foreach($allowed_locations as $allowed_location) {
	$allowed_locations_arr[] = " CONCAT(',',`tickets`.`con_location`,',') LIKE '%,$allowed_location,%'";
}
$allowed_locations_arr[] = " IFNULL(`tickets`.`con_location`,'') = ''";
$allowed_locations_query = " AND (".implode(' OR ', $allowed_locations_arr).")";

$allowed_classifications_arr = [];
foreach($allowed_classifications as $allowed_classification) {
	$allowed_classifications_arr[] = " CONCAT(',',`tickets`.`classification`,',') LIKE '%,$allowed_classification,%'";
}
$allowed_classifications_arr[] = " IFNULL(`tickets`.`classification`,'') = ''";
$allowed_classifications_query = " AND (".implode(' OR ', $allowed_classifications_arr).")";

$equipment_category = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"))['equipment_category'];
$equipment_categories = array_filter(explode(',', $equipment_category));
if(empty($equipment_categories) || count($equipment_categories) > 1) {
    $equipment_category = 'Equipment';
}
$equip_cat_query = '';
if(count($equipment_categories) > 0) {
    $equip_cat_query = " AND `equipment`.`category` IN ('".implode("','", $equipment_categories)."')";
}

$result = mysqli_query($dbc, "SELECT ea.*, e.*, ea.`notes`, CONCAT(e.`category`, ' #', e.`unit_number`) `label`, IFNULL(NULLIF(ea.`region`,''),e.`region`) `region`, IFNULL(NULLIF(ea.`location`,''),e.`location`) `location`, IFNULL(NULLIF(ea.`classification`,''),e.`classification`) `classification` FROM `equipment_assignment` ea LEFT JOIN `equipment` e ON ea.`equipmentid` = e.`equipmentid` WHERE ea.`deleted` = 0 AND DATE(`start_date`) <= '$new_today_date' AND DATE(ea.`end_date`) >= '$new_today_date' AND CONCAT(',',ea.`hide_days`,',') NOT LIKE '%,$new_today_date,%' ORDER BY e.`category`, e.`unit_number`");

while($row = mysqli_fetch_array( $result )) {
	$equipment_assignmentid = $row['equipment_assignmentid'];
	$completed_tickets = 0;

	$hide_staff = explode(',',$row['hide_staff']);
	$team_name = '';
	$team_contactids = [];
	$client = (get_contact($dbc, $row['clientid']) != '-' ? get_contact($dbc, $row['clientid']) : get_client($dbc, $row['clientid']));
	$equip_assign_team = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `teams` WHERE `teamid` = '".$row['teamid']."'"));

    $team_contacts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` ='".$equip_assign_team['teamid']."' AND `deleted` = 0"),MYSQLI_ASSOC);
    foreach ($team_contacts as $team_contact) {
    	if(!empty($team_contact['contactid']) && !in_array($team_contact['contactid'], $hide_staff)) {
    		$team_contactids[$team_contact['contactid']] = [get_contact($dbc, $team_contact['contactid'], 'category'), get_contact($dbc, $team_contact['contactid']), $team_contact['contact_position']];
    	}
    }

    $equip_assign_contacts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '".$row['equipment_assignmentid']."' AND `deleted` = 0"),MYSQLI_ASSOC);
    foreach ($equip_assign_contacts as $equip_assign_contact) {
    	if(!empty($equip_assign_contact['contactid'])) {
    		$team_contactids[$equip_assign_contact['contactid']] = [get_contact($dbc, $equip_assign_contact['contactid'], 'category'), get_contact($dbc, $equip_assign_contact['contactid']), $equip_assign_contact['contact_position']];
    	}
    }

    foreach ($team_contactids as $key => $value) {
    	$cur_staff = $value[0].': '.(!empty($value[2]) ? $value[2].': ' : '').$value[1];
    	$team_name .= $cur_staff.'<br />';
    }
    $team_name = '<b>'.$row['label'].'</b><br />'.rtrim($team_name, '<br />');

    if(empty($row['calendar_color'])) {
    	$row['calendar_color'] = '#6DCFF6';
    }
    if($row['region'] != '') {
		foreach($region_list as $region_line => $region_name) {
			if($region_name == $row_ticket['region']) {
				$row['calendar_color'] = $region_colours[$region_line];
			}
		}
    }
	$all_tickets_sql = "SELECT IFNULL(`ticket_schedule`.`status`,`tickets`.`status`) `status` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$new_today_date."' BETWEEN `tickets`.`to_do_date` AND IFNULL(`tickets`.`to_do_end_date`,`tickets`.`to_do_date`) OR '".$new_today_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND IFNULL(IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`),'') != '' AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$row['equipmentid']."') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done')".$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query.$allowed_ticket_types_query;
	$tickets = mysqli_fetch_all(mysqli_query($dbc, $all_tickets_sql),MYSQLI_ASSOC);
	$completed_tickets = 0;
	foreach($tickets as $ticket) {
		if(in_array($ticket['status'], $calendar_checkmark_status)) {
			$completed_tickets++;
		}
	}

    $column .= '<div class="calendar_block calendarSortable" data-region="'.$row['region'].'" data-location="'.$row['location'].'" data-classification="'.$row['classification'].'" data-blocktype="'.$_GET['block_type'].'" data-contact="'.$row['equipmentid'].'" data-equipmassign="'.$row['equipment_assignmentid'].'">';
	$column .= '<span class="sortable-blocks" style="display:block; margin: 0.5em; padding:5px; color:black; border-radius: 10px; background-color:'.$row['calendar_color'].'">';
	$column .= $team_name;
	$column .= '<br />(Completed '.$completed_tickets.' of '.count($tickets).' '.(count($tickets) == 1 ? TICKET_NOUN : TICKET_TILE).')';
	$column .= '</span>';
	$column .= '</div>';
} ?>