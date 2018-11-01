<?php

$teams_list = [];
$teams = mysqli_query($dbc, "SELECT 'team' `block_type`, 0 `contactid`, `teamid`, '#3ac4f2' `calendar_color`, `team_name`, `region` FROM `teams` WHERE `deleted` = 0 AND (DATE(`start_date`) <= '$new_today_date') AND (DATE(`end_date`) >= '$new_today_date' OR `end_date` IS NULL OR `end_date` = '' OR `end_date` = '0000-00-00') AND CONCAT(',',IFNULL(`hide_days`,''),',') NOT LIKE '%,$new_today_date,%'");
while($team = mysqli_fetch_assoc($teams)) {
	$contacts_arr = [];
	$contact_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '".$team['teamid']."' AND `deleted` = 0"),MYSQLI_ASSOC);
	foreach ($contact_list as $contact) {
		if(strtolower(get_contact($dbc, $contact['contactid'], 'category')) == 'staff') {
			$contacts_arr[] = $contact['contactid'];
		}
	}
	if(!empty($contacts_arr)) {
		$teams_list[implode(',', $contacts_arr)] = $team['teamid'];
	}
}

$all_staff = [];
$all_teams = [];
$tickets = mysqli_query($dbc,"SELECT * FROM `tickets` WHERE `deleted`=0 AND (`internal_qa_date` = '$new_today_date' OR `deliverable_date` = '$new_today_date' OR '$new_today_date' BETWEEN `to_do_date` AND IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`)) AND `status` NOT IN ('Archive','Done')".$allowed_ticket_types_query);
while($row = mysqli_fetch_assoc($tickets)) {
	if($row['internal_qa_date'] == $new_today_date) {
		$all_contacts = array_filter(array_unique(explode(',', $row['internal_qa_contactid'])));
		sort($all_contacts);
	} else if($row['deliverable_date'] == $new_today_date) {
		$all_contacts = array_filter(array_unique(explode(',', $row['deliverable_contactid'])));
		sort($all_contacts);
	} else {
		$all_contacts = array_filter(array_unique(explode(',', $row['contactid'])));
		sort($all_contacts);
	}
	$all_clients = array_filter(array_unique(explode(',', $row['clientid'])));

	if(!empty($all_contacts)) {
		if($teams_list[implode(',', $all_contacts)] > 0) {
			$all_teams[$teams_list[implode(',', $all_contacts)]]['count']++;
			foreach($all_clients as $clientid) {
				$all_teams[$teams_list[implode(',', $all_contacts)]]['clients'][] = $clientid;
			}
		}
		foreach($all_contacts as $contact_id) {
			$all_staff[$contact_id]['count']++;
			foreach($all_clients as $clientid) {
				$all_staff[$contact_id]['clients'][] = $clientid;
			}
		}
	} else {
		$all_staff['Unassigned']['count']++;
		foreach($all_clients as $clientid) {
			$all_staff['Unassigned']['clients'][] = $clientid;
		}
	}
}

foreach($all_teams as $contact_id => $ticket) {
	$ticket_clients = [];
	foreach(array_filter(array_unique($ticket['clients'])) as $clientid) {
		$ticket_clients[] = !empty(get_client($dbc, $clientid)) ? get_client($dbc, $clientid) : get_contact($dbc, $clientid);
	}
	$ticket_clients = implode(', ', $ticket_clients);

	$ticket_count = $ticket['count'];
	$row = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `teams` WHERE `teamid` = '".$contact_id."'"));
	$staff = (!empty($row['team_name']) ? get_team_name($dbc, $row['teamid']) : 'Team #'.$row['teamid']).' ('.get_team_name($dbc, $row['teamid'], ', ', 1).')';
    if(empty($row['calendar_color'])) {
    	$row['calendar_color'] = '#6DCFF6';
    }

    if($contact_id > 0) {
	    $column .= '<div class="calendar_block calendarSortable" data-region="'.$row['region'].'" data-blocktype="'.$row['block_type'].'" data-contact="'.$row['contactid'].'" data-team="'.$row['teamid'].'">';
		$column .= '<span class="sortable-blocks" style="display:block; margin: 0.5em; padding:5px; color:black; border-radius: 10px; background-color:'.$row['calendar_color'].'">';
		$column .= '<b>'.$staff.'</b>';
		if(in_array('client',$calendar_ticket_staff_summary_fields)) {
			$column .= '<br /><b>Client:</b> '.$ticket_clients;
		}
		$column .= '<br />('.$ticket_count.' '.($ticket_count == 1 ? TICKET_NOUN : TICKET_TILE).')';
		$column .= '</span>';
		$column .= '</div>';
	}
}
foreach($all_staff as $contact_id => $ticket) {
	$ticket_clients = [];
	foreach(array_filter(array_unique($ticket['clients'])) as $clientid) {
		$ticket_clients[] = !empty(get_client($dbc, $clientid)) ? get_client($dbc, $clientid) : get_contact($dbc, $clientid);
	}
	$ticket_clients = implode(', ', $ticket_clients);

	$ticket_count = $ticket['count'];
	$row = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '".$contact_id."'"));
	if($contact_id == 'Unassigned' || empty($contact_id)) {
		$staff = 'Unassigned';
	} else {
		$staff = get_staff($dbc, $row['contactid']);
	}
    if(empty($row['calendar_color'])) {
    	$row['calendar_color'] = '#6DCFF6';
    }

    $column .= '<div class="calendar_block calendarSortable" data-region="'.$row['region'].'" data-blocktype="'.$row['block_type'].'" data-contact="'.$row['contactid'].'" data-team="'.$row['teamid'].'">';
	$column .= '<span class="sortable-blocks" style="display:block; margin: 0.5em; padding:5px; color:black; border-radius: 10px; background-color:'.$row['calendar_color'].'">';
	$column .= '<b>'.$staff.'</b>';
	if(in_array('client',$calendar_ticket_staff_summary_fields)) {
		$column .= '<br /><b>Client:</b> '.$ticket_clients;
	}
	$column .= '<br />('.$ticket_count.' '.($ticket_count == 1 ? TICKET_NOUN : TICKET_TILE).')';
	$column .= '</span>';
	$column .= '</div>';
}
?>