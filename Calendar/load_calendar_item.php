<?php include_once('../include.php');
checkAuthorised('calendar_rook');
include_once('../Calendar/calendar_functions_inc.php');
include_once('../Calendar/calendar_settings_inc.php');
if(!$is_mobile_view) {
	ob_clean();
}
$edit_access = vuaed_visible_function($dbc, 'calendar_rook');
$config_type = $_POST['config_type'];
$calendar_date = $_POST['calendar_date'];
$contact_id = $_POST['contact_id'];
$day_start = get_config($dbc, $config_type.'_day_start');
$day_end = get_config($dbc, $config_type.'_day_end');
$day_period = get_config($dbc, $config_type.'_increments');
$day_of_week = date('l', strtotime($calendar_date));
$region_list = explode(',',get_config($dbc, '%_region', true));
$region_colours = explode(',',get_config($dbc, '%_region_colour', true));
$calendar_ticket_card_fields = explode(',',get_config($dbc, 'calendar_ticket_card_fields'));
$calendar_type = $wait_list;

$profile_icon = '';
if(get_contact($dbc, $contact_id, 'category') == 'Staff' && ($_GET['type'] != 'equipment' || $_GET['mode'] == 'staff' || $_GET['mode'] == 'contractors')) {
	$profile_icon = profile_id($dbc, $contact_id, false);
}

if(($_GET['type'] == 'uni' || $_GET['type'] == 'my') && empty($_GET['shiftid']) && $_GET['mode'] != 'shift') {
	// Contact Blocks - Universal

	//Populate the text for the column header
	$calendar_table[$calendar_date][$contact_id]['title'] = $profile_icon.get_contact($dbc, $contact_id);

	if(strpos(','.$calendar_type.',', ',ticket,') !== FALSE) {
		//Pull all tickets for the current contact from the ticket table
		$all_tickets_sql = "SELECT * FROM `tickets` WHERE (internal_qa_date = '".$calendar_date."' OR `deliverable_date` = '".$calendar_date."' OR '".$calendar_date."' BETWEEN `to_do_date` AND `to_do_end_date`) AND (`contactid` LIKE '%,".$contact_id.",%' OR `internal_qa_contactid` LIKE '%,".$contact_id.",%' OR `deliverable_contactid` LIKE '%,".$contact_id.",%') AND `deleted` = 0 AND `status` NOT IN ('Archive', 'Done')";
		$result_tickets_sql = mysqli_query($dbc, $all_tickets_sql);
		$tickets_time = [];
		$tickets_notime = [];
		$tickets_multiday = [];
		while($row_tickets = mysqli_fetch_array($result_tickets_sql)) {
	        if(($row_tickets['status'] == 'Internal QA') && ($calendar_date == $row_tickets['internal_qa_date']) && (strpos($row_tickets['internal_qa_contactid'], ','.$contact_id.',') !== FALSE)) {
	        	if (!empty($row_tickets['internal_qa_start_time'])) {
	        		$tickets_time[] = $row_tickets;
	        	} else {
	        		$tickets_notime[] = $row_tickets;
	        	}
	        } else if (($row_tickets['status'] == 'Customer QA' || $row_tickets['status'] == 'Waiting On Customer') && ($calendar_date == $row_tickets['deliverable_date']) && (strpos($row_tickets['deliverable_contactid'], ','.$contact_id.',') !== FALSE)) {
	        	if (!empty($row_tickets['deliverable_start_time'])) {
	        		$tickets_time[] = $row_tickets;
	        	} else {
	        		$tickets_notime[] = $row_tickets;
	        	}
	        } else if (($row_tickets['status'] != 'Customer QA' && $row_tickets['status'] != 'Internal QA') && ($calendar_date >= $row_tickets['to_do_date'] && $calendar_date <= $row_tickets['to_do_end_date']) && (strpos($row_tickets['contactid'], ','.$contact_id.',') !== FALSE)) {
	        	if ($row_tickets['to_do_date'] != $row_tickets['to_do_end_date']) {
	        		$tickets_multiday[] = $row_tickets;
	        	} else {
	        		if (!empty($row_tickets['to_do_start_time'])) {
		        		$tickets_time[] = $row_tickets;
		        	} else {
		        		$tickets_notime[] = $row_tickets;
		        	}
	        	}
	        }
		}
	} else {
		$tickets_time = '';
		$tickets_notime = '';
		$tickets_multiday = '';
	}
	if(strpos(','.$calendar_type.',', ',appt,') !== FALSE) {
		//Pull all appointments for the current contact from the booking table
		$all_booking_sql = "SELECT * FROM `booking` WHERE ('$contact_id' IN (`therapistsid`,`patientid`) OR CONCAT('*#*',`therapistsid`,'*#*') LIKE '%*#*$contact_id*#*%') AND `follow_up_call_status` NOT LIKE '%cancel%' AND ((`appoint_date` LIKE '%".$calendar_date."%') OR '".date('Y-m-d H:i:s', strtotime($calendar_date.' '.$day_start))."' BETWEEN `appoint_date` AND `end_appoint_date` OR '".date('Y-m-d H:i:s', strtotime($calendar_date.' '.$day_end))."' BETWEEN `appoint_date` AND `end_appoint_date`) AND `deleted` = 0";
		$appointments = mysqli_fetch_all(mysqli_query($dbc, $all_booking_sql),MYSQLI_ASSOC);
	} else {
		$appointments = '';
	}

	//Pull all shifts for the current contact from the contacts_shifts table
	if($use_shifts !== '') {
		$shifts = checkShiftIntervals($dbc, $contact_id, $day_of_week, $calendar_date, 'shifts');
		$daysoff = checkShiftIntervals($dbc, $contact_id, $day_of_week, $calendar_date, 'daysoff');
	} else {
		$shifts = [];
		$daysoff = [];
	}

	if(!empty($shifts)) {
		$start_time = date('H:i:s', strtotime($shifts[0]['starttime']));
	} else {
		if($availability_indication == 1) {
			$shifts = 'NO_SHIFT';
			$daysoff = 'NO_DAYSOFF';
		}
		$start_time = date('H:i:s', strtotime($day_start));
	}

	//Loop through each time on the calendar and populate it
	$current_duration = 0;
	$current_row = date('H:i:s', strtotime($day_start));
	while(strtotime($current_row) <= strtotime($day_end)) {
		if ($current_duration > 0 && $current_block[0] == 'ticket') {
			$current_duration = $current_duration - ($day_period * 60);
			$current_block = ['ticket', ''];
		} else if ($current_duration > 0 && $current_block[0] == 'appt') {
			$current_duration = $current_duration - ($day_period * 60);
			$current_block = ['appt', ''];
		} else {
			$current_block = '';
		}
		if (!empty($tickets_time)) {
			foreach ($tickets_time as $key => $ticket) {
				if ($ticket['status'] == 'Internal QA') {
					$current_start_time = date('H:i:s', strtotime($ticket['internal_qa_start_time']));
					if (!empty($ticket['internal_qa_end_time'])) {
						$ticket_duration = (strtotime($ticket['internal_qa_end_time']) - strtotime($current_start_time));
					} else {
						$estimated_time = explode(':',$ticket['max_qa_time']);
						$ticket_duration = ($estimated_time[0] * 3600) + ($estimated_time[1] * 60);
					}
				} else if ($ticket['status'] == 'Customer QA') {
					$current_start_time = date('H:i:s', strtotime($ticket['deliverable_start_time']));
					if (!empty($ticket['deliverable_end_time'])) {
						$ticket_duration = (strtotime($ticket['deliverable_end_time']) - strtotime($current_start_time));
					} else {
						$estimated_time = explode(':',$ticket['max_qa_time']);
						$ticket_duration = ($estimated_time[0] * 3600) + ($estimated_time[1] * 60);
					}
				} else {
					$current_start_time = date('H:i:s', strtotime($ticket['to_do_start_time']));
					if (!empty($ticket['to_do_end_time'])) {
						$ticket_duration = (strtotime($ticket['to_do_end_time']) - strtotime($current_start_time));
					} else {
						$estimated_time = explode(':',$ticket['max_time']);
						$ticket_duration = ($estimated_time[0] * 3600) + ($estimated_time[1] * 60);
					}
				}
				if ($current_row <= $current_start_time && date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) > $current_start_time) {
					$current_block = ['ticket', $ticket];
					if ($current_duration <= $ticket_duration - ($day_period * 60)) {
						$current_duration = $ticket_duration - ($day_period * 60);
					}
					unset($tickets_time[$key]);
					$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
					if(in_array($ticket['status'], $calendar_checkmark_status)) {
						$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
					}
				}
			}
		}
		if ($current_block == '' && !empty($tickets_notime) && $current_row >= $start_time) {
			$ticket_notime = array_shift($tickets_notime);
			if ($ticket_notime['status'] == 'Internal QA' || $ticket_notime['status'] == 'Customer QA') {
				$estimated_time = explode(':', $ticket_notime['max_qa_time']);
			} else {
				$estimated_time = explode(':', $ticket_notime['max_time']);
			}
			$ticket_duration = ($estimated_time[0] * 3600) + ($estimated_time[1] * 60);
			$current_block = ['ticket', $ticket_notime];
			$current_duration = $ticket_duration - ($day_period * 60);
			$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
			if(in_array($ticket_notime['status'],$calendar_checkmark_status)) {
				$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
			}
		}
		if ($current_block == '' && !empty($appointments)) {
			foreach($appointments as $key => $appt) {
				foreach(explode('*#*',$appt['appoint_date']) as $a => $appt_start) {
					$appt_end = explode('*#*',$appt['end_appoint_date'])[$a];
					$appt_staff = explode('*#*',$appt['therapistsid'])[$a];
					$appt_service = explode('*#*',$appt['serviceid'])[$a];
					$appt_type = explode('*#*',$appt['type'])[$a];
					if($appt_staff == $contact_id && date('Y-m-d', strtotime($appt_start)) != date('Y-m-d', strtotime($appt_end)) && $calendar_date != date('Y-m-d', strtotime($appt_start))) {
						$appt['appoint_date'] = $appt_start;
						$appt['end_appoint_date'] = $appt_end;
						$appt['therapistsid'] = $appt_staff;
						$appt['type'] = $appt_type;
						$current_block = ['appt', $appt];
						if ($current_duration <= (strtotime($appt_end) - strtotime($appt_start) - ($day_period * 60))) {
							$current_duration = strtotime($appt_end) - strtotime($appt_start) - ($day_period * 60);
						}
						unset($appointments[$key]);
					} else if($appt_staff == $contact_id && ($current_block == '' || $current_block == ['appt', '']) && $current_row <= date('H:i:s', strtotime($appt_start)) && date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) > date('H:i:s', strtotime($appt_start))) {
						$appt['appoint_date'] = $appt_start;
						$appt['end_appoint_date'] = $appt_end;
						$appt['therapistsid'] = $appt_staff;
						$appt['serviceid'] = $appt_service;
						$appt['type'] = $appt_type;
						$current_block = ['appt', $appt];
						if ($current_duration <= (strtotime($appt_end) - strtotime($appt_start) - ($day_period * 60))) {
							$current_duration = strtotime($appt_end) - strtotime($appt_start) - ($day_period * 60);
						}
						unset($appointments[$key]);
						$calendar_table[$calendar_date][$contact_id]['total_appt']++;
					}
				}
			}
		}
		if (empty($shifts) && $current_block == '') {
			$current_block = ['shift', 'SHIFT', $current_row, $calendar_date, $contact_id];
		} else if ($current_block == '') {
			foreach ($shifts as $shift) {
				if($current_block == '' && $current_row >= date('H:i:s', strtotime($shift['starttime'])) && $current_row < date('H:i:s', strtotime($shift['endtime']))) {
					$current_block = ['shift', 'SHIFT', $current_row, $calendar_date, $contact_id];
					if(!empty($shift['break_starttime']) && !empty($shift['break_endtime']) && ($current_row >= date('H:i:s', strtotime($shift['break_starttime'])) && $current_row < date('H:i:s', strtotime($shift['break_endtime'])))) {
						$current_block = '';
					}
				}
			}
			foreach ($daysoff as $dayoff) {
				if($current_block != '' && $current_row >= date('H:i:s', strtotime($dayoff['starttime'])) && $current_row < date('H:i:s', strtotime($dayoff['endtime']))) {
					$current_block = '';
				}
			}
		}
		$calendar_table[$calendar_date][$contact_id][] = $current_block;
		$current_row = date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row)));
		if(date('Y-m-d H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) >= date('Y-m-d H:i:s', strtotime($day_end))) {
			break;
		}
	}

	if (!empty($tickets_multiday)) {
		$current_block = $tickets_multiday[0];
		if ($current_block['to_do_date'] == $calendar_date && !empty($current_block['to_do_start_time'])) {
			$current_row = ceil((strtotime($current_block['to_do_start_time']) - strtotime($day_start)) / ($day_period * 60));
			if(empty($calendar_table[$calendar_date][$contact_id][$current_row]) || $calendar_table[$calendar_date][$contact_id][$current_row][1] == 'SHIFT') {
				array_shift($tickets_multiday);
				$calendar_table[$calendar_date][$contact_id][$current_row] = ['ticket', $current_block, 'all_day_ticket'];
				$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
				if(in_array($current_block['status'],$calendar_checkmark_status)) {
					$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
				}
			}
		} else {
			for ($ticket_i = 0; $ticket_i < count($calendar_table[$calendar_date][$contact_id]); $ticket_i++) {
				if (date('H:i:s', strtotime($day_start) + ($ticket_i * $day_period * 60)) >= $start_time && ($calendar_table[$calendar_date][$contact_id][$ticket_i] == '' || $calendar_table[$calendar_date][$contact_id][$ticket_i] [1] == 'SHIFT')) {
					$calendar_table[$calendar_date][$contact_id][$ticket_i] = ['ticket', $current_block, 'all_day_ticket'];
					array_shift($tickets_multiday);
					$current_row = $ticket_i;
					$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
					if(in_array($current_block['status'],$calendar_checkmark_status)) {
						$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
					}
					break;
				}
			}
		}
		$ticket_end_time = (!empty($current_block['to_do_end_time']) && $current_block['to_do_end_date'] == $calendar_date) ? strtotime($current_block['to_do_end_time']) : strtotime($day_end);
		$ticket_end_time = ceil(($ticket_end_time - strtotime($day_start)) / ($day_period * 60));
		while ($current_row < $ticket_end_time) {
			if ($calendar_table[$calendar_date][$contact_id][$current_row][1] == 'SHIFT') {
				$calendar_table[$calendar_date][$contact_id][$current_row] = '';
			}
			$current_row++;
		}
	}

	//Warning messages
	$calendar_table[$calendar_date][$contact_id]['warnings'] = '';

	//Tickets not displayed
	$tickets_not_scheduled = [];
	foreach ($tickets_time as $ticket) {
		$tickets_not_scheduled[] = $ticket['ticketid'];
		$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
		if(in_array($ticket['status'],$calendar_checkmark_status)) {
			$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
		}
	}
	foreach ($tickets_notime as $ticket) {
		$tickets_not_scheduled[] = $ticket['ticketid'];
		$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
		if(in_array($ticket['status'],$calendar_checkmark_status)) {
			$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
		}
	}
	foreach ($tickets_multiday as $ticket) {
		$tickets_not_scheduled[] = $ticket['ticketid'];
		$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
		if(in_array($ticket['status'],$calendar_checkmark_status)) {
			$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
		}
	}
	$ticket_notes[$calendar_date][$contact_id] = $tickets_not_scheduled;

	//Add warnings
	if(!empty($ticket_notes[$calendar_date][$contact_id])) {
		$ticket_urls = '';
		foreach($ticket_notes[$calendar_date][$contact_id] as $ticketid) {
			if($edit_access == 1) {
				$ticket_urls .= "<a href='".WEBSITE_URL."/Ticket/index.php?edit=".$ticketid."' onclick='overlayIFrameSlider(this.href+\"&calendar_view=true\"); return false;'>#".$ticketid."</a>, ";
			} else {
				$ticket_urls .= "#".$ticketid.', ';
			}
		}
		$ticket_urls = rtrim($ticket_urls, ', ');
		$calendar_table[$calendar_date][$contact_id]['warnings'] .= "The following ".TICKET_TILE." are either out of the Calendar time-frame, has a time conflict, or there are too many ".TICKET_TILE.": ".$ticket_urls.'<br>';
	}

	//Appointments that were not displayed
	$appt_not_scheduled = [];
	foreach ($appointments as $appt) {
		$appt_not_scheduled[] = $appt['bookingid'];
		$calendar_table[$calendar_date][$contact_id]['total_appt']++;
	}
	$appt_notes[$calendar_date][$contact_id] = $appt_not_scheduled;

	//Add warnings
	if(!empty($appt_notes[$calendar_date][$contact_id])) {
		$appt_urls = '';
		foreach($appt_notes[$calendar_date][$contact_id] as $bookingid) {
			$page_query['action'] = 'view';
			$page_query['bookingid'] = $bookingid;
			$appt_page_query = $page_query;
			unset($appt_page_query['unbooked']);
			if(vuaed_visible_function($dbc, 'calendar_rook')) {
				if($edit_access == 1) {
					$appt_urls .= "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Calendar/booking.php?".http_build_query($appt_page_query)."\"); return false;'>#".$bookingid."</a>, ";
				} else {
					$appt_urls .= '#'.$bookingid.', ';
				}
			}
			unset($page_query['action']);
			unset($page_query['bookingid']);
		}
		$appt_urls = rtrim($appt_urls, ', ');
		$calendar_table[$calendar_date][$contact_id]['warnings'] .= "The following Appointments are either out of the Calendar time-frame, has a time conflict, or there are too many Appointments: ".$appt_urls.'<br>';
	}
} else if($_GET['type'] == 'event') {
	$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '$contact_id'"));
	// Events
	$projectid = $project['projectid'];
	$calendar_table[$calendar_date][$projectid]['title'] = $project['project_name'];

	//Pull all tickets for the current project
	$all_tickets_sql = "SELECT * FROM `tickets` WHERE `projectid` = '$projectid' AND `to_do_date` = '$calendar_date' AND `deleted` = 0 AND `status` NOT IN ('Archive', 'Done')";
	$tickets = mysqli_fetch_all(mysqli_query($dbc, $all_tickets_sql),MYSQLI_ASSOC);
	if($_GET['offline'] == 1) {
		$changes = mysqli_query($dbc, "SELECT * FROM `calendar_offline_edits` WHERE `contactid`='".$_SESSION['contactid']."'");
		while($change = mysqli_fetch_assoc($changes)) {
			$key = array_search($change['id'],array_column($tickets,'ticketid'));
			$tickets[$key][$change['field_name']] = $change['value'];
		}
	}

	//Loop through each time on the calendar and populate it
	$current_row = date('H:i:s', strtotime($day_start));
	while(strtotime($current_row) <= strtotime($day_end)) {
		$current_ticket = '';
		foreach($tickets as $key => $ticket) {
			if($current_ticket == '' && $current_row <= date('H:i:s', strtotime($ticket['member_start_time'])) && date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) > date('H:i:s', strtotime($ticket['member_start_time']))) {

				$current_ticket = ['ticket_event', $ticket, $project['project_name']];
				unset($tickets[$key]);
				$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
				if(in_array($ticket['status'],$calendar_checkmark_status)) {
					$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
				}
			}
		}
		$calendar_table[$calendar_date][$projectid][] = $current_ticket;
		$current_row = date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row)));
		if(date('Y-m-d H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) >= date('Y-m-d H:i:s', strtotime($day_end))) {
			break;
		}
	}

	$tickets_not_scheduled = [];
	foreach ($tickets as $ticket) {
		$tickets_not_scheduled[] = $ticket['ticketid'];
		$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
		if(in_array($ticket['status'],$calendar_checkmark_status)) {
			$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
		}
	}
	$ticket_notes[$calendar_date][$projectid] = $tickets_not_scheduled;

	//Add warnings
	if(!empty($ticket_notes[$calendar_date][$projectid])) {
		$ticket_urls = '';
		foreach($ticket_notes[$calendar_date][$projectid] as $ticketid) {
			$ticket_urls .= "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Ticket/preview_ticket.php?action=view&ticketid=".$ticketid."\"); return false;'>#".$ticketid."</a>, ";
		}
		$ticket_urls = rtrim($ticket_urls, ', ');
		$calendar_table[$calendar_date][$projectid]['warnings'] = "The following ".TICKET_TILE." are either out of the Calendar time-frame, has a time conflict, or there are too many ".TICKET_TILE.": ".$ticket_urls;
	}
} else if($_GET['type'] == 'schedule' && $_GET['block_type'] == 'equipment') {
	$equipment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `equipmentid`, `unit_number`, `make`, `model`, `category`, `region`, CONCAT(`category`, ' #', `unit_number`) label FROM `equipment` WHERE `equipmentid` = '$contact_id'"));

	$calendar_table[$calendar_date][$equipment['equipmentid']]['region'] = explode('*#*',$equipment['region'])[0];
	// Equipment Blocks
	$equip_assign = mysqli_fetch_array(mysqli_query($dbc, "SELECT ea.*, e.*,ea.`notes` FROM `equipment_assignment` ea LEFT JOIN `equipment` e ON ea.`equipmentid` = e.`equipmentid` WHERE e.`equipmentid` = '".$equipment['equipmentid']."' AND ea.`deleted` = 0 AND DATE(`start_date`) <= '$calendar_date' AND DATE(ea.`end_date`) >= '$calendar_date' AND CONCAT(',',ea.`hide_days`,',') NOT LIKE '%,$calendar_date,%' ORDER BY ea.`start_date` DESC, ea.`end_date` ASC, e.`category`, e.`unit_number`"));
	if(!empty($equip_assign)) {
		$calendar_table[$calendar_date][$equipment['equipmentid']]['region'] = $equip_assign['region'];
		$equipassign_data[$calendar_date][$equipment['equipmentid']] = $equip_assign['equipment_assignmentid'];
		$hide_staff = explode(',',$equip_assign['hide_staff']);
		$team_name = '';
		$team_contactids = [];
		$client = (get_contact($dbc, $equip_assign['clientid']) != '-' ? get_contact($dbc, $equip_assign['clientid']) : get_client($dbc, $equip_assign['clientid']));
		$equip_assign_team = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `teams` WHERE `teamid` = '".$equip_assign['teamid']."'"));

        $team_contacts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` ='".$equip_assign_team['teamid']."' AND `deleted` = 0"),MYSQLI_ASSOC);
        foreach ($team_contacts as $team_contact) {
        	if(!empty($team_contact['contactid']) && !in_array($team_contact['contactid'], $hide_staff)) {
	    		$team_contactids[$team_contact['contactid']] = [get_contact($dbc, $team_contact['contactid'], 'category'), get_contact($dbc, $team_contact['contactid']), $team_contact['contact_position']];
        	}
        }

        $equip_assign_contacts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '".$equip_assign['equipment_assignmentid']."' AND `deleted` = 0"),MYSQLI_ASSOC);
        foreach ($equip_assign_contacts as $equip_assign_contact) {
        	if(!empty($equip_assign_contact['contactid'])) {
	    		$team_contactids[$equip_assign_contact['contactid']] = [get_contact($dbc, $equip_assign_contact['contactid'], 'category'), get_contact($dbc, $equip_assign_contact['contactid']), $equip_assign_contact['contact_position']];
	    	}
        }

        foreach ($team_contactids as $key => $value) {
        	$cur_staff = '<span class="equip_assign_staff" data-contact="'.$key.'" data-contact-name="'.get_contact($dbc, $key).'">'.($edit_access == 1 ? '<sup><a href="" onclick="removeStaffEquipAssign(this); return false;" style="font-size: x-small; color: #888; text-decoration: none; top: -0.2em; position: relative;">x</a></sup>' : '').$value[0].': '.(!empty($value[2]) ? $value[2].': ' : '').$value[1].'</span>';
        	$team_name .= $cur_staff.'<br />';
        }
        $team_name = rtrim($team_name, '<br />');

	    $query = $_GET;
	    unset($query['equipment_assignmentid']);
	    unset($query['teamid']);
	    unset($query['unbooked']);
		$calendar_table[$calendar_date][$equipment['equipmentid']]['title'] = '<div class="equip_assign_block" data-equip-label="'.$equipment['label'].'" data-date="'.$calendar_date.'" data-equip="'.$equipment['equipmentid'].'" data-equip-assign="'.$equip_assign['equipment_assignmentid'].'">'.($edit_access == 1 ? '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Calendar/equip_assign.php?equipment_assignmentid='.$equip_assign['equipment_assignmentid'].'&region='.$_GET['region'].'\'); return false;">' : '').$equipment['label'].(!empty($client) ? ' - '.$client : '').($edit_access == 1 ? '</a>' : '').'<br />'.$team_name.'</div>';
	} else {
		$team_name = '(No Team Assigned)';
	    $query = $_GET;
	    unset($query['equipment_assignmentid']);
	    unset($query['teamid']);
	    unset($query['unbooked']);
		$calendar_table[$calendar_date][$equipment['equipmentid']]['title'] = '<div class="equip_assign_block" data-date="'.$calendar_date.'" data-equip="'.$equipment['equipmentid'].'">'.($edit_access == 1 ? '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Calendar/equip_assign.php?equipment_assignmentid=NEW&equipmentid='.$equipment['equipmentid'].'&region='.$_GET['region'].'&start_date='.$calendar_date.'&end_date='.$calendar_date.'\'); return false;">' : '').$equipment['label'].($edit_access == 1 ? '</a>' : '').'<br />'.$team_name.'</div>';
	}
	// Add Sorting and Mapping icons
	if(get_config($dbc, 'scheduling_calendar_sort_auto') == 'map_sort' && $edit_access == 1) {
		$calendar_table[$calendar_date][$equipment['equipmentid']]['title'] .= '<a href="" class="pull-right" onclick="get_addresses(\''.$calendar_date.'\', \''.$equipment['equipmentid'].'\'); return false;"><img class="inline-img" title="Sort '.TICKET_TILE.'" src="../img/sort-icon.png"></a>';
	}
	$calendar_table[$calendar_date][$equipment['equipmentid']]['title'] .= '<a href="" class="pull-right" onclick="get_day_map(\''.$calendar_date.'\', \''.$equipment['equipmentid'].'\'); return false;"><img class="inline-img" title="View Schedule Map" src="../img/icons/navigation.png"></a>';

	//Populate the text for the column header
	$equipment_notes[$equipment['equipmentid']] = html_entity_decode($equip_assign['notes']);

	//Pull all tickets for the current equipment
	// $all_tickets_sql = "SELECT * FROM `tickets` WHERE '".$calendar_date."' BETWEEN `to_do_date` AND `to_do_end_date` AND `equipmentid` = '".$equipment['equipmentid']."' AND `deleted` = 0 AND `status` NOT IN ('Archive', 'Done')";
	$allowed_regions_query = " AND IFNULL(`tickets`.`region`,'') IN ('".implode("','", array_merge($allowed_regions,['']))."')";
	$allowed_locations_query = " AND IFNULL(`tickets`.`con_location`,'') IN ('".implode("','", array_merge($allowed_locations,['']))."')";
	$allowed_classifications_query = " AND IFNULL(`tickets`.`classification`,'') IN ('".implode("','", array_merge($allowed_classifications,['']))."')";

	$warehouse_query = '';
	if($combine_warehouses == 1) {
		$warehouse_query = " AND IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''),IFNULL(`tickets`.`city`,''))) NOT IN (SELECT CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')) FROM `contacts` WHERE `category`='Warehouses')";
		$all_warehouses_sql = "SELECT `tickets`.*, `ticket_schedule`.`id` `stop_id`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`equipment_assignmentid`,`tickets`.`equipment_assignmentid`) `equipment_assignmentid`, IFNULL(`ticket_schedule`.`teamid`,`tickets`.`teamid`) `teamid`, IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`) `contactid`, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`id`, 0) `ticket_scheduleid`, IFNULL(`ticket_schedule`.`last_updated_time`,`tickets`.`last_updated_time`) `last_updated_time`, CONCAT(' - ',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name`, IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),' ',IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''), IFNULL(`tickets`.`city`,''))) `warehouse_full_address` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$calendar_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$calendar_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND IFNULL(IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`),'') != '' AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done') AND IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''),IFNULL(`tickets`.`city`,''))) IN (SELECT CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')) FROM `contacts` WHERE `category`='Warehouses')".$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query;
		$warehouse_tickets = mysqli_fetch_all(mysqli_query($dbc, $all_warehouses_sql),MYSQLI_ASSOC);
	}

	$all_tickets_sql = "SELECT `tickets`.*, `ticket_schedule`.`id` `stop_id`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`equipment_assignmentid`,`tickets`.`equipment_assignmentid`) `equipment_assignmentid`, IFNULL(`ticket_schedule`.`teamid`,`tickets`.`teamid`) `teamid`, IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`) `contactid`, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`id`, 0) `ticket_scheduleid`, IFNULL(`ticket_schedule`.`last_updated_time`,`tickets`.`last_updated_time`) `last_updated_time`, CONCAT(' - ',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$calendar_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$calendar_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND IFNULL(IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`),'') != '' AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done')".$warehouse_query.$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query;
	$tickets = mysqli_fetch_all(mysqli_query($dbc, $all_tickets_sql),MYSQLI_ASSOC);

	//Loop through each time on the calendar and populate it
	$current_row = date('H:i:s', strtotime($day_start));
	while(strtotime($current_row) <= strtotime($day_end)) {
		if ($current_duration > 0) {
			$current_duration = $current_duration - ($day_period * 60);
			$current_ticket = ['ticket_equip', ''];
		} else {
			$current_ticket = '';
		}
		foreach($tickets as $key => $ticket) {
			if($current_ticket == '' && $current_row <= date('H:i:s', strtotime($ticket['to_do_start_time'])) && date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) > date('H:i:s', strtotime($ticket['to_do_start_time']))) {
				$current_start_time = date('H:i:s', strtotime($ticket['to_do_start_time']));
				$ticket_duration = (strtotime($ticket['to_do_end_time']) - strtotime($current_start_time));
				if ($current_duration <= $ticket_duration - ($day_period * 60)) {
					$current_duration = $ticket_duration - ($day_period * 60);
				}

				$current_assignstaff = explode(',',$ticket['contactid']);
				$current_team = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `teams` WHERE `teamid` = '".$ticket['teamid']."'"));
				$current_team_contacts = '';
				foreach (explode('*#*', $current_team['contactid']) as $single_cat) {
					$cat_contacts = explode(',',$single_cat);
					foreach ($cat_contacts as $single_contact) {
						$current_assignstaff[] = $single_contact;
					}
				}
				$current_assignstaff = implode(',', $current_assignstaff);
				$current_ticket = ['ticket_equip', $ticket, $ticket['region'], $ticket['businessid'], $current_assignstaff, $ticket['teamid'], $equip_assign['equipment_assignmentid'], 'dispatch_equip', $ticket['ticket_table']];
				unset($tickets[$key]);
				$calendar_table[$calendar_date][$equipment['equipmentid']]['total_tickets']++;
				if(in_array($ticket['status'],$calendar_checkmark_status)) {
					$calendar_table[$calendar_date][$equipment['equipmentid']]['completed_tickets']++;
				}
			}
		}
		if($combine_warehouses == 1 && ($current_ticket == '' || $current_ticket[1] == '')) {
			$current_warehouse = '';
			$current_count = 0;
			$current_ticketids = [];
			foreach($warehouse_tickets as $key => $ticket) {
				if(($current_warehouse == '' || $ticket['warehouse_full_address'] == $current_warehouse) && $current_row <= date('H:i:s', strtotime($ticket['to_do_start_time'])) && date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) > date('H:i:s', strtotime($ticket['to_do_start_time']))) {
					$current_warehouse = $ticket['warehouse_full_address'];
					$current_count++;
					$current_ticketids[] = ($ticket['stop_id'] > 0 ? 'ticket_schedule-'.$ticket['stop_id'] : 'tickets-'.$ticket['ticketid']);
					$current_ticket = ['ticket_equip', 'warehouse', $current_warehouse, $current_count, $current_ticketids, $ticket];
					unset($warehouse_tickets[$key]);
					$calendar_table[$calendar_date][$equipment['equipmentid']]['total_tickets']++;
					if(in_array($ticket['status'],$calendar_checkmark_status)) {
						$calendar_table[$calendar_date][$equipment['equipmentid']]['completed_tickets']++;
					}
				}
			}
		}
		if($current_ticket == '' && $current_duration <= 0) {
			$current_ticket = ['ticket_equip', 'SHIFT', $current_row, $calendar_date, $equipment, $equip_assign];
		}
		$calendar_table[$calendar_date][$equipment['equipmentid']][] = $current_ticket;
		$current_row = date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row)));
		if(date('Y-m-d H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) >= date('Y-m-d H:i:s', strtotime($day_end))) {
			break;
		}
	}
	foreach($warehouse_tickets as $ticket) {
		$tickets[] = $ticket;
	}

	$tickets_not_scheduled = [];
	foreach ($tickets as $ticket) {
		$tickets_not_scheduled[] = $ticket['ticketid'];
		$calendar_table[$calendar_date][$equipment['equipmentid']]['total_tickets']++;
		if(in_array($ticket['status'],$calendar_checkmark_status)) {
			$calendar_table[$calendar_date][$equipment['equipmentid']]['completed_tickets']++;
		}
	}
	$ticket_notes[$calendar_date][$equipment['equipmentid']] = $tickets_not_scheduled;

	//Add warnings
	if(!empty($ticket_notes[$calendar_date][$equipment['equipmentid']])) {
		$ticket_urls = '';
		foreach($ticket_notes[$calendar_date][$equipment['equipmentid']] as $ticketid) {
			if($edit_access == 1) {
				$ticket_urls .= "<a href='".WEBSITE_URL."/Ticket/index.php?edit=".$ticketid."' onclick='overlayIFrameSlider(this.href+\"&calendar_view=true\"); return false;'>#".$ticketid."</a>, ";
			} else {
				$ticket_urls .= '#'.$ticketid.', ';
			}
		}
		$ticket_urls = rtrim($ticket_urls, ', ');
		$calendar_table[$calendar_date][$equipment['equipmentid']]['warnings'] = "The following ".TICKET_TILE." are either out of the Calendar time-frame, has a time conflict, or there are too many ".TICKET_TILE.": ".$ticket_urls;
	}
} else if($_GET['type'] == 'schedule' && $_GET['block_type'] == 'dispatch_staff') {
	// Contact Blocks - Tickets
	$teams = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `teamid` SEPARATOR ',') as teams_list FROM `teams_staff` WHERE `contactid` = '$contact_id' AND `deleted` = 0"));
	if(!empty($teams['teams_list'])) {
		$teams_query = 'OR `teamid` IN ('.$teams['teams_list'].')';
	} else {
		$teams_query = '';
	}
	$equip_assign = mysqli_fetch_array(mysqli_query($dbc, "SELECT ea.* FROM `equipment_assignment` ea LEFT JOIN `equipment_assignment_staff` eas ON ea.`equipment_assignmentid` = eas.`equipment_assignmentid` WHERE ea.`deleted` = 0 AND DATE(`start_date`) <= '$calendar_date' AND DATE(ea.`end_date`) >= '$calendar_date' AND CONCAT(',',ea.`hide_days`,',') NOT LIKE '%,$calendar_date,%' AND ((eas.`contactid` = '$contact_id' AND eas.`deleted` = 0) $teams_query) ORDER BY ea.`start_date` DESC, ea.`end_date` ASC"));

	//Populate the text for the column header
	$calendar_table[$calendar_date][$contact_id]['title'] = $profile_icon.get_contact($dbc, $contact_id);
	$equipassign_data[$calendar_date][$contact_id] = $equip_assign['equipment_assignmentid'];
	if(!empty($equip_assign)) {
		$equipment_staff = mysqli_fetch_array(mysqli_query($dbc, "SELECT *, CONCAT(`category`, ' #', `unit_number`) label FROM `equipment` WHERE `equipmentid` = '".$equip_assign['equipmentid']."'"));
	    $query = $_GET;
	    unset($query['equipment_assignmentid']);
	    unset($query['teamid']);
	    unset($query['unbooked']);
		$calendar_table[$calendar_date][$contact_id]['title'] .= '<br>('.($edit_access == 1 ? '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Calendar/equip_assign.php?equipment_assignmentid='.$equip_assign['equipment_assignmentid'].'&region='.$_GET['region'].'\'); return false;">' : '').$equipment_staff['label'].($edit_access == 1 ? '</a>' : '').')';
	} else {
		$calendar_table[$calendar_date][$contact_id]['title'] .= '<br>(No Assignment)';
	}

	//Pull all tickets for the current contact
	$allowed_regions_query = " AND IFNULL(`tickets`.`region`,'') IN ('".implode("','", array_merge($allowed_regions,['']))."')";
	$allowed_locations_query = " AND IFNULL(`tickets`.`con_location`,'') IN ('".implode("','", array_merge($allowed_locations,['']))."')";
	$allowed_classifications_query = " AND IFNULL(`tickets`.`classification`,'') IN ('".implode("','", array_merge($allowed_classifications,['']))."')";

	$all_tickets_sql = "SELECT `tickets`.*, `ticket_schedule`.`id` `stop_id`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`equipment_assignmentid`,`tickets`.`equipment_assignmentid`) `equipment_assignmentid`, IFNULL(`ticket_schedule`.`teamid`,`tickets`.`teamid`) `teamid`, IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`) `contactid`, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`id`, 0) `ticket_scheduleid`, IFNULL(`ticket_schedule`.`last_updated_time`,`tickets`.`last_updated_time`) `last_updated_time`, CONCAT(' - ',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, `ticket_schedule`.`type` `delivery_type` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$calendar_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$calendar_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND IFNULL(IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`),'') != '' AND (`tickets`.`contactid` LIKE '%,".$contact_id.",%' OR `ticket_schedule`.`contactid` LIKE '%,$contact_id,%') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done')".$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query;
	$tickets = mysqli_fetch_all(mysqli_query($dbc, $all_tickets_sql),MYSQLI_ASSOC);

	//Loop through each time on the calendar and populate it
	$current_row = date('H:i:s', strtotime($day_start));
	while(strtotime($current_row) <= strtotime($day_end)) {
		$current_ticket = '';
		foreach($tickets as $key => $ticket) {
			if($current_ticket == '' && $current_row <= date('H:i:s', strtotime($ticket['to_do_start_time'])) && date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) > date('H:i:s', strtotime($ticket['to_do_start_time']))) {
				$current_assignstaff = explode(',',$ticket['contactid']);
				$current_team = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `teams` WHERE `teamid` = '".$ticket['teamid']."'"));
				$current_team_contacts = '';
				foreach (explode('*#*', $current_team['contactid']) as $single_cat) {
					$cat_contacts = explode(',',$single_cat);
					foreach ($cat_contacts as $single_contact) {
						$current_assignstaff[] = $single_contact;
					}
				}
				$current_assignstaff = implode(',', $current_assignstaff);
				$current_ticket = ['ticket_equip', $ticket, $ticket['region'], $ticket['businessid'], $current_assignstaff, $ticket['teamid'], $equip_assign['equipment_assignmentid'], 'dispatch_staff', $ticket['ticket_table']];
				unset($tickets[$key]);
				$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
				if(in_array($ticket['status'],$calendar_checkmark_status)) {
					$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
				}
			}
		}
		$calendar_table[$calendar_date][$contact_id][] = $current_ticket;
		$current_row = date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row)));
		if(date('Y-m-d H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) >= date('Y-m-d H:i:s', strtotime($day_end))) {
			break;
		}
	}

	$tickets_not_scheduled = [];
	foreach ($tickets as $ticket) {
		$tickets_not_scheduled[] = $ticket['ticketid'];
		$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
		if(in_array($ticket['status'],$calendar_checkmark_status)) {
			$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
		}
	}
	$ticket_notes[$calendar_date][$contact_id] = $tickets_not_scheduled;

	//Add warnings
	if(!empty($ticket_notes[$calendar_date][$contact_id])) {
		$ticket_urls = '';
		foreach($ticket_notes[$calendar_date][$contact_id] as $ticketid) {
			if($edit_access == 1) {
				$ticket_urls .= "<a href='".WEBSITE_URL."/Ticket/index.php?edit=".$ticketid."' onclick='overlayIFrameSlider(this.href+\"&calendar_view=true\"); return false;'>#".$ticketid."</a>, ";
			} else {
				$ticket_urls .= '#'.$ticketid.', ';
			}
		}
		$ticket_urls = rtrim($ticket_urls, ', ');
		$calendar_table[$calendar_date][$contact_id]['warnings'] = "The following ".TICKET_TILE." are either out of the Calendar time-frame, has a time conflict, or there are too many ".TICKET_TILE.": ".$ticket_urls;
	}
} else if(isset($_GET['shiftid']) || $_GET['type'] == 'shift' || $_GET['mode'] == 'shift') {
	// Shift Blocks

	//Populate the text for the column header
	$calendar_table[$calendar_date][$contact_id]['title'] = $profile_icon.get_contact($dbc, $contact_id);

	//Pull all shifts for the current contact from the contacts_shifts table
	if($_GET['mode'] == 'client') {
		$shifts = checkShiftIntervals($dbc, '', $day_of_week, $calendar_date, 'shifts', $contact_id);
		$daysoff = checkShiftIntervals($dbc, '', $day_of_week, $calendar_date, 'daysoff', $contact_id);
		$all_conflicts = getShiftConflicts($dbc, '', $calendar_date, '', '', '', $contact_id);
	} else {
		$shifts = checkShiftIntervals($dbc, $contact_id, $day_of_week, $calendar_date, 'shifts');
		$daysoff = checkShiftIntervals($dbc, $contact_id, $day_of_week, $calendar_date, 'daysoff');
		$all_conflicts = getShiftConflicts($dbc, $contact_id, $calendar_date);
	}

	$shift_conflicts = [];
	foreach($all_conflicts as $conflict) {
		$shift_conflicts = array_merge(explode('*#*',$conflict), $shift_conflicts);
	}

	//Loop through each time on the calendar and populate it
	$current_row = date('H:i:s', strtotime($day_start));
	$current_duration = 0;
	while(strtotime($current_row) <= strtotime($day_end)) {
		$current_day = '';
		$current_shift = '';
		$current_dayoff = '';
		foreach ($shifts as $key => $shift) {
			if($current_shift == '' && $current_row <= date('H:i:s', strtotime($shift['starttime'])) && date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) > date('H:i:s', strtotime($shift['starttime']))) {
				$current_shift = $shift;
				$shift_duration = strtotime($shift['endtime']) - strtotime($shift['starttime']);
				if ($current_duration <= $shift_duration - ($day_period * 60)) {
					$current_duration = $shift_duration - ($day_period * 60);
				}
				unset($shifts[$key]);
				$calendar_table[$calendar_date][$contact_id]['total_shifts']++;
			} else if($current_shift == '' && date('H:i:s', strtotime($day_start)) > date('H:i:s', strtotime($shift['starttime']))) {
				$current_shift = $shift;
				$shift_duration = strtotime($shift['endtime']) - strtotime($day_start);
				if ($current_duration <= $shift_duration - ($day_period * 60)) {
					$current_duration = $shift_duration - ($day_period * 60);
				}
				unset($shifts[$key]);
				$calendar_table[$calendar_date][$contact_id]['total_shifts']++;
			}
		}
		foreach ($daysoff as $key => $dayoff) {
			if($current_dayoff == '' && $current_row <= date('H:i:s', strtotime($dayoff['starttime'])) && date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) > date('H:i:s', strtotime($dayoff['starttime']))) {
				$current_dayoff = $dayoff;
				$dayoff_duration = strtotime($dayoff['endtime']) - strtotime($dayoff['starttime']);
				if ($current_duration <= $dayoff_duration - ($day_period * 60)) {
					$current_duration = $dayoff_duration - ($day_period * 60);
				}
				unset($daysoff[$key]);
				$calendar_table[$calendar_date][$contact_id]['total_dayoff']++;
			}
		}
		if (!empty($current_shift) || !empty($current_dayoff)) {
			if(in_array($current_shift['shiftid'], $shift_conflicts)) {
				$has_conflict = true;
			} else {
				$has_conflict = false;
			}
			$current_day = ['shift', $current_shift, $current_dayoff, $has_conflict];
		} else if ($current_duration <= 0) {
			$current_day = ['no_shift', $current_row];
		} else {
			$current_duration = $current_duration - ($day_period * 60);
		}

		$calendar_table[$calendar_date][$contact_id][] = $current_day;
		$current_row = date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row)));
		if(date('Y-m-d H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) >= date('Y-m-d H:i:s', strtotime($day_end))) {
			break;
		}
	}

	//Shifts that were not displayed
	$shifts_not_scheduled = [];
	foreach ($shifts as $shift) {
		$shifts_not_scheduled[] = $shift['shiftid'];
		$calendar_table[$calendar_date][$contact_id]['total_shifts']++;
	}
	foreach ($daysoff as $dayoff) {
		$shifts_not_scheduled[] = $dayoff['shiftid'];
		$calendar_table[$calendar_date][$contact_id]['total_dayoff']++;
	}
	$shifts_notes[$calendar_date][$contact_id] = $shifts_not_scheduled;

	//Add warnings
	if(!empty($shifts_notes[$calendar_date][$contact_id])) {
		$shift_urls = '';
		foreach($shifts_notes[$calendar_date][$contact_id] as $shiftid) {
			$page_query['shiftid'] = $shiftid;
			if($edit_access == 1) {
				$shift_urls .= "<a href='?".http_build_query($page_query)."'>#".$shiftid."</a>, ";
			} else {
				$shift_urls .= '#'.$shiftid.', ';
			}
			unset($page_query['shiftid']);
		}
		$shift_urls = rtrim($shift_urls, ', ');
		$calendar_table[$calendar_date][$contact_id]['warnings'] = "The following Shifts/Days Off are either out of the Calendar time-frame, has a time conflict, or there are too many Shifts/Days Off: ".$shift_urls;
	}
} else if($calendar_type == 'ticket') {
	// Contact Blocks - Tickets

	//Populate the text for the column header
	$calendar_table[$calendar_date][$contact_id]['title'] = $profile_icon.get_contact($dbc, $contact_id);
	$calendar_table[$calendar_date][$contact_id]['calendar_type'] = $calendar_type;

	//Pull all tickets for the current contact from the ticket table
	$all_tickets_sql = "SELECT * FROM `tickets` WHERE (internal_qa_date = '".$calendar_date."' OR `deliverable_date` = '".$calendar_date."' OR '".$calendar_date."' BETWEEN `to_do_date` AND `to_do_end_date`) AND (`contactid` LIKE '%,".$contact_id.",%' OR `internal_qa_contactid` LIKE '%,".$contact_id.",%' OR `deliverable_contactid` LIKE '%,".$contact_id.",%') AND `deleted` = 0 AND `status` NOT IN ('Archive', 'Done')";
	$result_tickets_sql = mysqli_query($dbc, $all_tickets_sql);
	$tickets_time = [];
	$tickets_notime = [];
	$tickets_multiday = [];
	while($row_tickets = mysqli_fetch_array($result_tickets_sql)) {
        if(($row_tickets['status'] == 'Internal QA') && ($calendar_date == $row_tickets['internal_qa_date']) && (strpos($row_tickets['internal_qa_contactid'], ','.$contact_id.',') !== FALSE)) {
        	if (!empty($row_tickets['internal_qa_start_time'])) {
        		$tickets_time[] = $row_tickets;
        	} else {
        		$tickets_notime[] = $row_tickets;
        	}
        } else if (($row_tickets['status'] == 'Customer QA' || $row_tickets['status'] == 'Waiting On Customer') && ($calendar_date == $row_tickets['deliverable_date']) && (strpos($row_tickets['deliverable_contactid'], ','.$contact_id.',') !== FALSE)) {
        	if (!empty($row_tickets['deliverable_start_time'])) {
        		$tickets_time[] = $row_tickets;
        	} else {
        		$tickets_notime[] = $row_tickets;
        	}
        } else if (($row_tickets['status'] != 'Customer QA' && $row_tickets['status'] != 'Internal QA') && ($calendar_date >= $row_tickets['to_do_date'] && $calendar_date <= $row_tickets['to_do_end_date']) && (strpos($row_tickets['contactid'], ','.$contact_id.',') !== FALSE)) {
        	if ($row_tickets['to_do_date'] != $row_tickets['to_do_end_date']) {
        		$tickets_multiday[] = $row_tickets;
        	} else {
        		if (!empty($row_tickets['to_do_start_time'])) {
	        		$tickets_time[] = $row_tickets;
	        	} else {
	        		$tickets_notime[] = $row_tickets;
	        	}
        	}
        }
	}

	//Pull all shifts for the current contact from the contacts_shifts table
	if($use_shifts !== '') {
		$shifts = checkShiftIntervals($dbc, $contact_id, $day_of_week, $calendar_date, 'shifts');
		$daysoff = checkShiftIntervals($dbc, $contact_id, $day_of_week, $calendar_date, 'daysoff');
	} else {
		$shifts = [];
		$daysoff = [];
	}

	if(!empty($shifts)) {
		$start_time = date('H:i:s', strtotime($shifts[0]['starttime']));
	} else {
		if($availability_indication == 1) {
			$shifts = 'NO_SHIFT';
			$daysoff = 'NO_DAYSOFF';
		}
		$start_time = date('H:i:s', strtotime($day_start));
	}

	//Loop through each time on the calendar and populate it
	$current_duration = 0;
	$current_row = date('H:i:s', strtotime($day_start));
	while(strtotime($current_row) <= strtotime($day_end)) {
		if ($current_duration > 0) {
			$current_duration = $current_duration - ($day_period * 60);
			$current_ticket = ['ticket', ''];
		} else {
			$current_ticket = '';
		}
		foreach ($tickets_time as $key => $ticket) {
			if ($ticket['status'] == 'Internal QA') {
				$current_start_time = date('H:i:s', strtotime($ticket['internal_qa_start_time']));
				if (!empty($ticket['internal_qa_end_time'])) {
					$ticket_duration = (strtotime($ticket['internal_qa_end_time']) - strtotime($current_start_time));
				} else {
					$estimated_time = explode(':',$ticket['max_qa_time']);
					$ticket_duration = ($estimated_time[0] * 3600) + ($estimated_time[1] * 60);
				}
			} else if ($ticket['status'] == 'Customer QA') {
				$current_start_time = date('H:i:s', strtotime($ticket['deliverable_start_time']));
				if (!empty($ticket['deliverable_end_time'])) {
					$ticket_duration = (strtotime($ticket['deliverable_end_time']) - strtotime($current_start_time));
				} else {
					$estimated_time = explode(':',$ticket['max_qa_time']);
					$ticket_duration = ($estimated_time[0] * 3600) + ($estimated_time[1] * 60);
				}
			} else {
				$current_start_time = date('H:i:s', strtotime($ticket['to_do_start_time']));
				if (!empty($ticket['to_do_end_time'])) {
					$ticket_duration = (strtotime($ticket['to_do_end_time']) - strtotime($current_start_time));
				} else {
					$estimated_time = explode(':',$ticket['max_time']);
					$ticket_duration = ($estimated_time[0] * 3600) + ($estimated_time[1] * 60);
				}
			}
			if ($current_row <= $current_start_time && date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) > $current_start_time) {
				$current_ticket = ['ticket', $ticket];
				if ($current_duration <= $ticket_duration - ($day_period * 60)) {
					$current_duration = $ticket_duration - ($day_period * 60);
				}
				unset($tickets_time[$key]);
				$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
				if(in_array($ticket['status'],$calendar_checkmark_status)) {
					$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
				}
			}
		}
		if ($current_ticket == '' && !empty($tickets_notime) && $current_row >= $start_time) {
			$ticket_notime = array_shift($tickets_notime);
			if ($ticket_notime['status'] == 'Internal QA' || $ticket_notime['status'] == 'Customer QA') {
				$estimated_time = explode(':', $ticket_notime['max_qa_time']);
			} else {
				$estimated_time = explode(':', $ticket_notime['max_time']);
			}
			$ticket_duration = ($estimated_time[0] * 3600) + ($estimated_time[1] * 60);
			$current_ticket = ['ticket', $ticket_notime];
			$current_duration = $ticket_duration - ($day_period * 60);
			$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
			if(in_array($ticket_notime['status'],$calendar_checkmark_status)) {
				$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
			}
		}
		if (empty($shifts) && $current_ticket == '') {
			$current_ticket = ['ticket', 'SHIFT', $current_row, $calendar_date, $contact_id];
		} else if ($current_ticket == '') {
			foreach ($shifts as $shift) {
				if($current_ticket == '' && $current_row >= date('H:i:s', strtotime($shift['starttime'])) && $current_row < date('H:i:s', strtotime($shift['endtime']))) {
					$current_ticket = ['ticket', 'SHIFT', $current_row, $calendar_date, $contact_id];
					if(!empty($shift['break_starttime']) && !empty($shift['break_endtime']) && ($current_row >= date('H:i:s', strtotime($shift['break_starttime'])) && $current_row < date('H:i:s', strtotime($shift['break_endtime'])))) {
						$current_ticket = '';
					}
				}
			}
			foreach ($daysoff as $dayoff) {
				if($current_ticket != '' && $current_row >= date('H:i:s', strtotime($dayoff['starttime'])) && $current_row < date('H:i:s', strtotime($dayoff['endtime']))) {
					$current_ticket = '';
				}
			}
		}
		$calendar_table[$calendar_date][$contact_id][] = $current_ticket;
		$current_row = date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row)));
		if(date('Y-m-d H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) >= date('Y-m-d H:i:s', strtotime($day_end))) {
			break;
		}
	}

	if (!empty($tickets_multiday)) {
		$current_ticket = $tickets_multiday[0];
		if ($current_ticket['to_do_date'] == $calendar_date && !empty($current_ticket['to_do_start_time'])) {
			$current_row = ceil((strtotime($current_ticket['to_do_start_time']) - strtotime($day_start)) / ($day_period * 60));
			if(empty($calendar_table[$calendar_date][$contact_id][$current_row]) || $calendar_table[$calendar_date][$contact_id][$current_row][1] == 'SHIFT') {
				array_shift($tickets_multiday);
				$calendar_table[$calendar_date][$contact_id][$current_row] = ['ticket', $current_ticket, 'all_day_ticket'];
				$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
				if(in_array($current_ticket['status'],$calendar_checkmark_status)) {
					$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
				}
			}
		} else {
			for ($ticket_i = 0; $ticket_i < count($calendar_table[$calendar_date][$contact_id]); $ticket_i++) {
				if (date('H:i:s', strtotime($day_start) + ($ticket_i * $day_period * 60)) >= $start_time && ($calendar_table[$calendar_date][$contact_id][$ticket_i] == '' || $calendar_table[$calendar_date][$contact_id][$ticket_i] [1] == 'SHIFT')) {
					$calendar_table[$calendar_date][$contact_id][$ticket_i] = ['ticket', $current_ticket, 'all_day_ticket'];
					array_shift($tickets_multiday);
					$current_row = $ticket_i;
					$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
					if(in_array($current_ticket['status'],$calendar_checkmark_status)) {
						$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
					}
					break;
				}
			}
		}
		$ticket_end_time = (!empty($current_ticket['to_do_end_time']) && $current_ticket['to_do_end_date'] == $calendar_date) ? strtotime($current_ticket['to_do_end_time']) : strtotime($day_end);
		$ticket_end_time = ceil(($ticket_end_time - strtotime($day_start)) / ($day_period * 60));
		while ($current_row < $ticket_end_time) {
			if ($calendar_table[$calendar_date][$contact_id][$current_row][1] == 'SHIFT') {
				$calendar_table[$calendar_date][$contact_id][$current_row] = '';
			}
			$current_row++;
		}
	}

	$tickets_not_scheduled = [];
	foreach ($tickets_time as $ticket) {
		$tickets_not_scheduled[] = $ticket['ticketid'];
		$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
		if(in_array($ticket['status'],$calendar_checkmark_status)) {
			$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
		}
	}
	foreach ($tickets_notime as $ticket) {
		$tickets_not_scheduled[] = $ticket['ticketid'];
		$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
		if(in_array($ticket['status'],$calendar_checkmark_status)) {
			$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
		}
	}
	foreach ($tickets_multiday as $ticket) {
		$tickets_not_scheduled[] = $ticket['ticketid'];
		$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
		if(in_array($ticket['status'],$calendar_checkmark_status)) {
			$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
		}
	}
	$ticket_notes[$calendar_date][$contact_id] = $tickets_not_scheduled;

	//Add warnings
	if(!empty($ticket_notes[$calendar_date][$contact_id])) {
		$ticket_urls = '';
		foreach($ticket_notes[$calendar_date][$contact_id] as $ticketid) {
			if($edit_access == 1) {
				$ticket_urls .= "<a href='".WEBSITE_URL."/Ticket/index.php?edit=".$ticketid."' onclick='overlayIFrameSlider(this.href+\"&calendar_view=true\"); return false;'>#".$ticketid."</a>, ";
			} else {
				$ticket_urls .= '#'.$ticketid.', ';
			}
		}
		$ticket_urls = rtrim($ticket_urls, ', ');
		$calendar_table[$calendar_date][$contact_id]['warnings'] = "The following ".TICKET_TILE." are either out of the Calendar time-frame, has a time conflict, or there are too many ".TICKET_TILE.": ".$ticket_urls;
	}
} else if($calendar_type == 'workorder') {
	// Contact Blocks - Work Orders

	//Populate the text for the column header
	$calendar_table[$calendar_date][$contact_id]['title'] = $profile_icon.get_contact($dbc, $contact_id);
	$calendar_table[$calendar_date][$contact_id]['calendar_type'] = $calendar_type;

	//Get all the teams this contact is in
	$all_teams = "SELECT * FROM `teams` WHERE `deleted` = 0 AND `contactid` LIKE '%".$contact_id."%' AND (`start_date` <= '".$calendar_date."' OR `start_date` IS NULL OR `start_date` = '' OR `start_date` = '0000-00-00') AND (`end_date` >= '".$calendar_date."' OR `end_date` IS NULL OR `end_date` = '' OR `end_date` = '0000-00-00')";
	// $all_teams = "SELECT * FROM `teams` WHERE '".$calendar_date."' BETWEEN `start_date` AND `end_date` AND `deleted` = 0 AND `contactid` LIKE '%".$contact_id."%'";
	$teams = mysqli_fetch_all(mysqli_query($dbc, $all_teams),MYSQLI_ASSOC);
	$contact_teams = '';
	foreach ($teams as $team) {
		$split_categories = explode('*#*', $team['contactid']);
		foreach ($split_categories as $category) {
			if (strpos(','.$category.',', ','.$contact_id.',') !== FALSE) {
				$contact_teams .= $team['teamid'].',';
			}
		}
	}
	$contact_teams = trim($contact_teams, ',');

	//Pull all work orders for the current contact from the workorder table
	$all_work_orders = "SELECT * FROM `workorder` WHERE (`contactid` LIKE '%,".$contact_id.",%' OR `assign_teamid` IN (".$contact_teams.")) AND `to_do_date` = '".date('Y-m-d', strtotime($calendar_date))."'";
	$work_orders = mysqli_fetch_all(mysqli_query($dbc, $all_work_orders),MYSQLI_ASSOC);

	//Loop through each time on the calendar and populate it
	$current_row = date('H:i:s', strtotime($day_start));
	while(strtotime($current_row) <= strtotime($day_end)) {
		$current_wo = '';
		foreach ($work_orders as $key => $work_order) {
			if($current_wo == '' && $current_row <= date('H:i:s', strtotime($work_order['to_do_time'])) && date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) > date('H:i:s', strtotime($work_order['to_do_time']))) {
				$current_wo = ['workorder', $work_order];
				unset($work_orders[$key]);
			}
		}
		$calendar_table[$calendar_date][$contact_id][] = $current_wo;
		$current_row = date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row)));
		if(date('Y-m-d H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) >= date('Y-m-d H:i:s', strtotime($day_end))) {
			break;
		}
	}

	//Work orders that were not displayed
	$workorders_not_scheduled = [];
	foreach ($work_orders as $work_order) {
		$workorders_not_scheduled[] = $work_order['workorderid'];
	}
	$workorder_notes[$calendar_date][$contact_id] = $workorders_not_scheduled;

	//Add warnings
	if (!empty($workorder_notes[$calendar_date][$contact_id])) {
		$workorder_urls = '';
		foreach ($workorder_notes[$calendar_date][$contact_id] as $workorderid) {
			if($edit_access == 1) {
				$workorder_urls .= "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Work Order/edit_workorder.php?action=view&workorderid=".$workorderid."\"); return false;'>#".$workorderid."</a>, ";
			} else {
				$workorder_urls .= '#'.$workorderid.', ';
			}
		}
		$workorder_urls = rtrim($workorder_urls, ', ');
		$calendar_table[$calendar_date][$contact_id]['warnings'] = "The following Work Orders are either out of the Calendar time-frame, has a time conflict, or there are too many Work Orders: ".$workorder_urls;
	}
} else if($_GET['mode'] == 'tickets') {
	$ticket_list = mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `deleted`=0 AND ('".$calendar_start."' BETWEEN `to_do_date` AND `to_do_end_date` OR `to_do_date` BETWEEN '".date('Y-m-d', strtotime($calendar_start.' -'.($day - 1 + $weekly_start).' days'))."' AND '".date('Y-m-d', strtotime($calendar_start.' -'.($day - 7 + $weekly_start).' days'))."')");
	while($ticket = mysqli_fetch_assoc($ticket_list)) {
		$calendar_table[$calendar_date][$ticket['ticketid']]['title'] = TICKET_NOUN.' #'.$ticket['ticketid'].' '.$ticket['heading'];
	}
} else if($calendar_type == 'appt') {
	// Contact Blocks - Appointments

	//Populate the text for the column header
	$calendar_table[$calendar_date][$contact_id]['title'] = $profile_icon.get_contact($dbc, $contact_id);

	//Pull all appointments for the current contact from the booking table
	$all_booking_sql = "SELECT * FROM `booking` WHERE ('$contact_id' IN (`therapistsid`,`patientid`) OR CONCAT('*#*',`therapistsid`,'*#*') LIKE '%*#*$contact_id*#*%') AND `follow_up_call_status` NOT LIKE '%cancel%' AND ((`appoint_date` LIKE '%".$calendar_date."%') OR '".date('Y-m-d H:i:s', strtotime($calendar_date.' '.$day_start))."' BETWEEN `appoint_date` AND `end_appoint_date` OR '".date('Y-m-d H:i:s', strtotime($calendar_date.' '.$day_end))."' BETWEEN `appoint_date` AND `end_appoint_date`) AND `deleted` = 0";
	$appointments = mysqli_fetch_all(mysqli_query($dbc, $all_booking_sql),MYSQLI_ASSOC);

	//Pull all shifts for the current contact from the contacts_shifts table
	if($use_shifts !== '') {
		$shifts = checkShiftIntervals($dbc, $contact_id, $day_of_week, $calendar_date, 'shifts');
		$daysoff = checkShiftIntervals($dbc, $contact_id, $day_of_week, $calendar_date, 'daysoff');
	} else {
		if($availability_indication == 1) {
			$shifts = 'NO_SHIFT';
			$daysoff = 'NO_DAYSOFF';
		}
		$shifts = [];
		$daysoff = [];
	}

	//Loop through each time on the calendar and populate it
	$current_duration = 0;
	$current_row = date('H:i:s', strtotime($day_start));
	while(strtotime($current_row) <= strtotime($day_end)) {
		if ($current_duration > 0) {
			$current_duration = $current_duration - ($day_period * 60);
			$current_appt = ['appt', ''];
		} else {
			$current_appt = '';
		}
		foreach($appointments as $key => $appt) {
			foreach(explode('*#*',$appt['appoint_date']) as $a => $appt_start) {
				$appt_end = explode('*#*',$appt['end_appoint_date'])[$a];
				$appt_staff = explode('*#*',$appt['therapistsid'])[$a];
				$appt_service = explode('*#*',$appt['serviceid'])[$a];
				$appt_type = explode('*#*',$appt['type'])[$a];
				if($appt_staff == $contact_id && date('Y-m-d', strtotime($appt_start)) != date('Y-m-d', strtotime($appt_end)) && $calendar_date != date('Y-m-d', strtotime($appt_start))) {
					$appt['appoint_date'] = $appt_start;
					$appt['end_appoint_date'] = $appt_end;
					$appt['therapistsid'] = $appt_staff;
					$appt['type'] = $appt_type;
					$current_appt = ['appt', $appt];
					if ($current_duration <= (strtotime($appt_end) - strtotime($appt_start) - ($day_period * 60))) {
						$current_duration = strtotime($appt_end) - strtotime($appt_start) - ($day_period * 60);
					}
					unset($appointments[$key]);
					$calendar_table[$calendar_date][$contact_id]['total_appt']++;
				} else if($appt_staff == $contact_id && ($current_appt == '' || $current_appt == ['appt', '']) && $current_row <= date('H:i:s', strtotime($appt_start)) && date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) > date('H:i:s', strtotime($appt_start))) {
					$appt['appoint_date'] = $appt_start;
					$appt['end_appoint_date'] = $appt_end;
					$appt['therapistsid'] = $appt_staff;
					$appt['serviceid'] = $appt_service;
					$appt['type'] = $appt_type;
					$current_appt = ['appt', $appt];
					if ($current_duration <= (strtotime($appt_end) - strtotime($appt_start) - ($day_period * 60))) {
						$current_duration = strtotime($appt_end) - strtotime($appt_start) - ($day_period * 60);
					}
					unset($appointments[$key]);
					$calendar_table[$calendar_date][$contact_id]['total_appt']++;
				}
			}
		}
		if(($_GET['mode'] == 'client' && $current_appt == '') || (empty($shifts) && $current_appt == '')) {
			$current_appt = ['appt', 'SHIFT', $current_row, $calendar_date, $contact_id];
		} else if($current_appt == '') {
			foreach ($shifts as $shift) {
				if($current_appt == '' && $current_row >= date('H:i:s', strtotime($shift['starttime'])) && $current_row < date('H:i:s', strtotime($shift['endtime']))) {
					$current_appt = ['appt', 'SHIFT', $current_row, $calendar_date, $contact_id];
					if(!empty($shift['break_starttime']) && !empty($shift['break_endtime']) && ($current_row >= date('H:i:s', strtotime($shift['break_starttime'])) && $current_row < date('H:i:s', strtotime($shift['break_endtime'])))) {
						$current_appt = '';
					}
				}
			}
		}
		foreach ($daysoff as $dayoff) {
			if($current_appt != '' && $current_row >= date('H:i:s', strtotime($dayoff['starttime'])) && $current_row < date('H:i:s', strtotime($dayoff['endtime']))) {
				$current_appt = '';
			}
		}
		$calendar_table[$calendar_date][$contact_id][] = $current_appt;
		$current_row = date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row)));
		if(date('Y-m-d H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) >= date('Y-m-d H:i:s', strtotime($day_end))) {
			break;
		}
	}

	//Appointments that were not displayed
	$appt_not_scheduled = [];
	foreach ($appointments as $appt) {
		$appt_not_scheduled[] = $appt['bookingid'];
		$calendar_table[$calendar_date][$contact_id]['total_appt']++;
	}
	$appt_notes[$calendar_date][$contact_id] = $appt_not_scheduled;

	//Add warnings
	if(!empty($appt_notes[$calendar_date][$contact_id])) {
		$appt_urls = '';
		foreach($appt_notes[$calendar_date][$contact_id] as $bookingid) {
			$page_query['action'] = 'view';
			$page_query['bookingid'] = $bookingid;
			$appt_page_query = $page_query;
			unset($appt_page_query['unbooked']);
			if($edit_access == 1) {
				$appt_urls .= "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Calendar/booking.php?".http_build_query($appt_page_query)."\"); return false;'>#".$bookingid."</a>, ";
			} else {
				$appt_urls .= '#'.$bookingid.', ';
			}
			unset($page_query['action']);
			unset($page_query['bookingid']);
		}
		$appt_urls = rtrim($appt_urls, ', ');
		$calendar_table[$calendar_date][$contact_id]['warnings'] = "The following Appointments are either out of the Calendar time-frame, has a time conflict, or there are too many Appointments: ".$appt_urls;
	}
} else if($_GET['type'] == 'estimates') {
	// Contact Blocks - Estimates

	//Populate the text for the column header
	$calendar_table[$calendar_date][$contact_id]['title'] = $profile_icon.get_contact($dbc, $contact_id);
	$calendar_table[$calendar_date][$contact_id]['notes'] = '';

    //pull all next actions from estimate_actions table
    $query_next_actions = "SELECT `ea`.* FROM `estimate_actions` AS `ea` JOIN `estimate` AS `e` ON (`ea`.`estimateid`=`e`.`estimateid`) WHERE FIND_IN_SET ('$contact_id', `e`.`assign_staffid`) AND `e`.`deleted`=0 AND FIND_IN_SET('$contact_id', `ea`.`contactid`) AND `ea`.`deleted`=0 AND `ea`.`due_date`='". date('Y-m-d', strtotime($calendar_date)) ."'";
    $next_actions = mysqli_fetch_all(mysqli_query($dbc, $query_next_actions),MYSQLI_ASSOC);

	//Loop through each time on the calendar and populate it
	//$day_start2 = "10:00:00";
    $current_row = date('H:i:s', strtotime($day_start));
	while(strtotime($current_row) <= strtotime($day_end)) {
		$current_estimate = '';
		//if (strtotime($current_row) >= strtotime($day_start2) ) {
            foreach($next_actions as $next_action) {
                if ( $current_estimate == '' && $calendar_date == date('Y-m-d', strtotime($next_action['due_date'])) ) {
                    $current_estimate = ['estimate', $next_action];
                    array_shift($next_actions);
					$calendar_table[$calendar_date][$contact_id]['total_estimates']++;
                }
            }
        //}
		$calendar_table[$calendar_date][$contact_id][] = $current_estimate;
		$current_row = date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row)));
		if(date('Y-m-d H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) >= date('Y-m-d H:i:s', strtotime($day_end))) {
			break;
		}
	}

	//Estimates that were not displayed
	$est_not_scheduled = [];
	foreach ($next_actions as $next_action) {
		$est_not_scheduled[] = $next_action['estimateid'];
		$calendar_table[$calendar_date][$contact_id]['total_estimates']++;
	}
	$est_notes[$contact_id] = $est_not_scheduled;

	//Add Notes
	if(!empty($est_notes[$contact_id])) {
		$est_urls = '';
		foreach($est_notes[$contact_id] as $estimateid) {
			$est_urls .= '<a href="'.WEBSITE_URL.'/Estimate/estimates.php?view='.$estimateid.'" onclick="overlayIFrameSlider('.WEBSITE_URL.'/Estimate/estimates.php?view='.$estimateid.'); return false;" style="color: black;">#'.$estimateid.'</a>, ';
		}
		$est_urls = rtrim($est_urls, ', ');
		$calendar_table[$calendar_date][$contact_id]['warnings'] .= "The following Estimates are either out of the Calendar time-frame, has a time conflict, or there are too many Estimates: ".$est_urls."<br>";
	}
}

if(!isset($equipment)) {
	//Add notes
	$contact_notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `calendar_notes` WHERE `date` = '".$calendar_date."' AND `contactid` = '".$contact_id."' AND `deleted` = 0 AND `is_equipment` = 0"))['note'];
	$calendar_table[$calendar_date][$contact_id]['notes'] = html_entity_decode($contact_notes);

	//Add reminders
    $reminderids = [];
    //Reminders
    $reminders_query = "SELECT * FROM `reminders` WHERE `reminder_date` = '$calendar_date' AND `contactid` = '$contact_id' AND `deleted` = 0";
    $reminders_result = mysqli_fetch_all(mysqli_query($dbc, $reminders_query),MYSQLI_ASSOC);
    foreach ($reminders_result as $reminder) {
        mysqli_query($dbc, "INSERT INTO `daysheet_reminders` (`reminderid`, `contactid`, `type`, `date`, `done`) SELECT '".$reminder['reminderid']."', '".$contact_id."', 'reminder', '".$calendar_date."', '0' FROM (SELECT COUNT(*) rows FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['reminderid']."' AND `type` = 'reminder' AND `date` = '".$calendar_date."' AND `contactid` = '".$contact_id."' AND `deleted` = 0) num WHERE num.rows = 0");
        $reminderid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daysheetreminderid` FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['reminderid']."' AND `type` = 'reminder' AND `date` = '".$calendar_date."' AND `contactid` = '".$contact_id."' AND `deleted` = 0"))['daysheetreminderid'];
        $reminderids[] = $reminderid;
    }
    $sales_reminders_query = "SELECT * FROM `sales` WHERE `new_reminder` = '$calendar_date' AND (`primary_staff` = '$contact_id' OR CONCAT(',',`share_lead`,',') LIKE '%,$contact_id,%')";
    $sales_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $sales_reminders_query),MYSQLI_ASSOC);
    foreach ($sales_reminders_result as $reminder) {
        mysqli_query($dbc, "INSERT INTO `daysheet_reminders` (`reminderid`, `contactid`, `type`, `date`, `done`) SELECT '".$reminder['salesid']."', '".$contact_id."', 'sales', '".$calendar_date."', '0' FROM (SELECT COUNT(*) rows FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['salesid']."' AND `type` = 'sales' AND `date` = '".$calendar_date."' AND `contactid` ='".$contact_id."' AND `deleted` = 0) num WHERE num.rows = 0");
        $reminderid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daysheetreminderid` FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['salesid']."' AND `type` = 'sales' AND `date` = '".$calendar_date."' AND `contactid` = '".$contact_id."' AND `deleted` = 0"))['daysheetreminderid'];
        $reminderids[] = $reminderid;
    }
    $estimates_reminders_query = "SELECT `ea`.*, `e`.`estimate_name` FROM `estimate_actions` AS `ea` JOIN `estimate` AS `e` ON (`ea`.`estimateid`=`e`.`estimateid`) WHERE FIND_IN_SET ('$contact_id', `e`.`assign_staffid`) AND `e`.`deleted`=0 AND FIND_IN_SET('$contact_id', `ea`.`contactid`) AND `ea`.`deleted`=0 AND `ea`.`due_date`='". date('Y-m-d', strtotime($calendar_date)) ."'";
    $estimates_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $estimates_reminders_query),MYSQLI_ASSOC);
    foreach ($estimates_reminders_result as $reminder) {
        mysqli_query($dbc, "INSERT INTO `daysheet_reminders` (`reminderid`, `contactid`, `type`, `date`, `done`) SELECT '".$reminder['id']."', '".$contact_id."', 'estimate', '".$calendar_date."', '0' FROM (SELECT COUNT(*) rows FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['id']."' AND `type` = 'estimate' AND `date` = '".$calendar_date."' AND `contactid` ='".$contact_id."' AND `deleted` = 0) num WHERE num.rows = 0");
        $reminderid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daysheetreminderid` FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['id']."' AND `type` = 'estimate' AND `date` = '".$calendar_date."' AND `contactid` = '".$contact_id."' AND `deleted` = 0"))['daysheetreminderid'];
        $reminderids[] = $reminderid;
    }
    $projects_reminders_query = "SELECT `pa`.*, `p`.`project_name` FROM `project_actions` AS `pa` JOIN `project` AS `p` ON (`pa`.`projectid`=`p`.`projectid`) WHERE FIND_IN_SET ('$contact_id', `pa`.`contactid`) AND `p`.`deleted` = 0 AND `pa`.`deleted` = 0 AND `pa`.`due_date` = '".date('Y-m-d', strtotime($calendar_date))."'";
    $projects_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $projects_reminders_query),MYSQLI_ASSOC);
    foreach ($projects_reminders_result as $reminder) {
        mysqli_query($dbc, "INSERT INTO `daysheet_reminders` (`reminderid`, `contactid`, `type`, `date`, `done`) SELECT '".$reminder['id']."', '".$contact_id."', 'project', '".$calendar_date."', '0' FROM (SELECT COUNT(*) rows FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['id']."' AND `type` = 'project' AND `date` = '".$calendar_date."' AND `contactid` ='".$contact_id."' AND `deleted` = 0) num WHERE num.rows = 0");
        $reminderid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daysheetreminderid` FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['id']."' AND `type` = 'project' AND `date` = '".$calendar_date."' AND `contactid` = '".$contact_id."' AND `deleted` = 0"))['daysheetreminderid'];
        $reminderids[] = $reminderid;
    }
    $pfu_reminders_query = "SELECT * FROM `project` WHERE `followup` = '".$calendar_date."' AND `project_lead` = '".$contact_id."'";
    $pfu_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $pfu_reminders_query),MYSQLI_ASSOC);
    foreach ($pfu_reminders_result as $key => $reminder) {
        $project_exists = false;
        foreach ($projects_reminders_result as $project_action) {
            if ($project_action['projectid'] == $reminder['projectid']) {
                $project_exists = true;
                unset($pfu_reminders_result[$key]);
            }
        }
        if (!$project_exists) {
            mysqli_query($dbc, "INSERT INTO `daysheet_reminders` (`reminderid`, `contactid`, `type`, `date`, `done`) SELECT '".$reminder['projectid']."', '".$contact_id."', 'project_followup', '".$calendar_date."', '0' FROM (SELECT COUNT(*) rows FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['projectid']."' AND `type` = 'project_followup' AND `date` = '".$calendar_date."' AND `contactid` ='".$contact_id."' AND `deleted` = 0) num WHERE num.rows = 0");
            $reminderid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daysheetreminderid` FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['projectid']."' AND `type` = 'project_followup' AND `date` = '".$calendar_date."' AND `contactid` = '".$contact_id."' AND `deleted` = 0"))['daysheetreminderid'];
            $reminderids[] = $reminderid;
        }
    }
    $alerts_reminders_query = "SELECT * FROM `alerts` WHERE `alert_date` = '$calendar_date' AND `alert_user` = '$contact_id'";
    $alerts_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $alerts_reminders_query),MYSQLI_ASSOC);
    foreach ($alerts_reminders_result as $reminder) {
        mysqli_query($dbc, "INSERT INTO `daysheet_reminders` (`reminderid`, `contactid`, `type`, `date`, `done`) SELECT '".$reminder['alertid']."', '".$contact_id."', 'alert', '".$calendar_date."', '0' FROM (SELECT COUNT(*) rows FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['alertid']."' AND `type` = 'alert' AND `date` = '".$calendar_date."' AND `contactid` = '".$contact_id."' AND `deleted` = 0) num WHERE num.rows = 0");
        $reminderid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daysheetreminderid` FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['alertid']."' AND `type` = 'alert' AND `date` = '".$calendar_date."' AND `contactid` = '".$contact_id."' AND `deleted` = 0"))['daysheetreminderid'];
        $reminderids[] = $reminderid;
    }

    //If reminders not found, mark it as deleted
    $reminderids = "'".implode("','",$reminderids)."'";
        $date_of_archival = date('Y-m-d');
    mysqli_query($dbc, "UPDATE `daysheet_reminders` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `daysheetreminderid` NOT IN (".$reminderids.") AND `date` = '".$calendar_date."' AND `date` >= '".date('Y-m-d')."' AND `contactid` = '".$contact_id."' AND `done` = 0 AND `deleted` = 0");

    //Display Reminders
	$reminders_list = mysqli_query($dbc, "SELECT * FROM `daysheet_reminders` WHERE `date` = '".$calendar_date."' AND `contactid` = '".$contact_id."' AND `deleted` = 0 AND `done` = 0");
    $num_rows = mysqli_num_rows($reminders_list);
    if ($num_rows > 0) {
    	$calendar_table[$calendar_date][$contact_id]['reminders'] ='';
		foreach ($reminders_list as $daysheet_reminder) {
		    if ($daysheet_reminder['type'] == 'reminder') {
		        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `reminders` WHERE `reminderid` = '".$daysheet_reminder['reminderid']."'"));
		        $calendar_table[$calendar_date][$contact_id]['reminders'] .= $reminder['subject'].'<br>';
		    } else if ($daysheet_reminder['type'] == 'sales') {
		        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales` WHERE `salesid` = '".$daysheet_reminder['reminderid']."'"));
		        $calendar_table[$calendar_date][$contact_id]['reminders'] .= '<a href="../Sales/sale.php?p=preview&id='.$reminder['salesid'].'">Follow Up Sales: Sales #'.$reminder['salesid'].'</a><br>';
		    } else if ($daysheet_reminder['type'] == 'estimate') {
		        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `ea`.*, `e`.`estimate_name` FROM `estimate_actions` AS `ea` JOIN `estimate` AS `e` ON (`ea`.`estimateid`=`e`.`estimateid`) WHERE `ea`.`id` = '".$daysheet_reminder['reminderid']."'"));
		        $calendar_table[$calendar_date][$contact_id]['reminders'] .= '<a href="../Estimate/estimates.php?view='.$reminder['estimateid'].'">Follow Up Estimate: '.$reminder['estimate_name'].'</a><br>';
		    } else if ($daysheet_reminder['type'] == 'project') {
		        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `pa`.*, `p`.`project_name` FROM `project_actions` AS `pa` JOIN `project` AS `p` ON (`pa`.`projectid`=`p`.`projectid`) WHERE `pa`.`id` = '".$daysheet_reminder['reminderid']."'"));
		        $calendar_table[$calendar_date][$contact_id]['reminders'] .= '<a href="../Project/projects.php?edit='.$reminder['projectid'].'">Follow Up Project: '.$reminder['project_name'].'</a><br>';
		    } else if ($daysheet_reminder['type'] == 'project_followup') {
		        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '".$daysheet_reminder['reminderid']."'"));
		        $calendar_table[$calendar_date][$contact_id]['reminders'] .= '<a href="../Project/projects.php?edit='.$reminder['projectid'].'">Follow Up Project: '.$reminder['project_name'].'</a><br>';
		    } else if ($daysheet_reminder['type'] == 'alert') {
		        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `alerts` WHERE `alertid` = '".$daysheet_reminder['reminderid']."'"));
		        $calendar_table[$calendar_date][$contact_id]['reminders'] .= '<a href="'.$reminder['alert_link'].'">Alert: '.$reminder['alert_text'].' - '.$reminder['alert_link'].'</a><br>';
		    }
		}
		$calendar_table[$calendar_date][$contact_id]['reminders'] = rtrim($calendar_table[$calendar_date][$contact_id]['reminders'], '<br>');
	}
} else {
	$contact_notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `calendar_notes` WHERE `date` = '".$calendar_date."' AND `contactid` = '".$equipment['equipmentid']."' AND `deleted` = 0 AND `is_equipment` = 1"))['note'];
	$calendar_table[$calendar_date][$equipment['equipmentid']]['notes'] = html_entity_decode($contact_notes);
}

if($ticket_summary == 1) {
	$calendar_table[0][0]['ticket_summary'] = TICKET_NOUN.' Summary';
	if(isset($equipment)) {
		$completed_tickets = $calendar_table[$calendar_date][$equipment['equipmentid']]['completed_tickets'] > 0 ? $calendar_table[$calendar_date][$equipment['equipmentid']]['completed_tickets'] : 0;
		$total_tickets = $calendar_table[$calendar_date][$equipment['equipmentid']]['total_tickets'] > 0 ? $calendar_table[$calendar_date][$equipment['equipmentid']]['total_tickets'] : 0;
		$calendar_table[$calendar_date][$equipment['equipmentid']]['ticket_summary'] = 'Completed '.$completed_tickets.' of '.$total_tickets.' '.($total_tickets == 1 ? TICKET_NOUN : TICKET_TILE);
	} else {
		$completed_tickets = $calendar_table[$calendar_date][$contact_id]['completed_tickets'] > 0 ? $calendar_table[$calendar_date][$contact_id]['completed_tickets'] : 0;
		$total_tickets = $calendar_table[$calendar_date][$contact_id]['total_tickets'] > 0 ? $calendar_table[$calendar_date][$contact_id]['total_tickets'] : 0;
		$calendar_table[$calendar_date][$contact_id]['ticket_summary'] = 'Completed '.$completed_tickets.' of '.$total_tickets.' '.($total_tickets == 1 ? TICKET_NOUN : TICKET_TILE);
	}
}

include('../Calendar/load_calendar_item_display.php');