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
$ticket_customer_query = '';
if($is_customer) {
	$ticket_customer_query = " AND (`tickets`.`businessid` = '".$_SESSION['contactid']."' OR CONCAT(',', `tickets`.`clientid`, ',') LIKE '%,".$_SESSION['contactid'].",%')";
}

$profile_icon = '';
if(get_contact($dbc, $contact_id, 'category') == 'Staff' && ($_GET['type'] != 'equipment' || $_GET['mode'] == 'staff' || $_GET['mode'] == 'contractors')) {
	$profile_icon = profile_id($dbc, $contact_id, false);
}

if(($_GET['type'] == 'uni' || $_GET['type'] == 'my') && empty($_GET['shiftid']) && $_GET['mode'] != 'shift' && $_GET['block_type'] != 'team') {
	// Contact Blocks - Universal

	//Populate the text for the column header
	$calendar_table[$calendar_date][$contact_id]['title'] = $profile_icon.get_contact($dbc, $contact_id);

	if(strpos(','.$calendar_type.',', ',ticket,') !== FALSE) {
		//Pull all tickets for the current contact from the ticket table
		$all_tickets_sql = "SELECT *, IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) `to_do_end_date` FROM `tickets` WHERE (internal_qa_date = '".$calendar_date."' OR `deliverable_date` = '".$calendar_date."' OR '".$calendar_date."' BETWEEN `to_do_date` AND IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`)) AND (CONCAT(',',`contactid`,',') LIKE '%,".$contact_id.",%' OR CONCAT(',',`internal_qa_contactid`,',') LIKE '%,".$contact_id.",%' OR CONCAT(',',`deliverable_contactid`,',') LIKE '%,".$contact_id.",%') AND `deleted` = 0 AND `status` NOT IN ('Archive', 'Done')";
		$calendar_table[$calendar_date][$contact_id]['deleted_tickets'] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `num_rows` FROM `tickets` WHERE (internal_qa_date = '".$calendar_date."' OR `deliverable_date` = '".$calendar_date."' OR '".$calendar_date."' BETWEEN `to_do_date` AND IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`)) AND (CONCAT(',',`contactid`,',') LIKE '%,".$contact_id.",%' OR CONCAT(',',`internal_qa_contactid`,',') LIKE '%,".$contact_id.",%' OR CONCAT(',',`deliverable_contactid`,',') LIKE '%,".$contact_id.",%') AND (`deleted` = 1 OR `status` IN ('Archive', 'Done')"))['num_rows'];
		$result_tickets_sql = mysqli_query($dbc, $all_tickets_sql);
		$tickets_time = [];
		$tickets_notime = [];
		$tickets_multiday = [];
		while($row_tickets = mysqli_fetch_array($result_tickets_sql)) {
	        if(($row_tickets['status'] == 'Internal QA') && ($calendar_date == $row_tickets['internal_qa_date']) && (strpos(','.$row_tickets['internal_qa_contactid'].',', ','.$contact_id.',') !== FALSE)) {
	        	if (!empty($row_tickets['internal_qa_start_time'])) {
	        		$tickets_time[] = $row_tickets;
	        	} else {
	        		$tickets_notime[] = $row_tickets;
	        	}
	        } else if (($row_tickets['status'] == 'Customer QA' || $row_tickets['status'] == 'Waiting On Customer') && ($calendar_date == $row_tickets['deliverable_date']) && (','.strpos($row_tickets['deliverable_contactid'].',', ','.$contact_id.',') !== FALSE)) {
	        	if (!empty($row_tickets['deliverable_start_time'])) {
	        		$tickets_time[] = $row_tickets;
	        	} else {
	        		$tickets_notime[] = $row_tickets;
	        	}
	        } else if (($row_tickets['status'] != 'Customer QA' && $row_tickets['status'] != 'Internal QA') && ($calendar_date >= $row_tickets['to_do_date'] && $calendar_date <= $row_tickets['to_do_end_date']) && (strpos(','.$row_tickets['contactid'].',', ','.$contact_id.',') !== FALSE)) {
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
		$tickets_not_scheduled[] = ['id' => $ticket['ticketid'], 'note' => $ticket['ticketid'].' - Conflicting or Out of Calendar Time-Frame'];
		$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
		if(in_array($ticket['status'],$calendar_checkmark_status)) {
			$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
		}
	}
	foreach ($tickets_notime as $ticket) {
		$tickets_not_scheduled[] = ['id' => $ticket['ticketid'], 'note' => $ticket['ticketid'].' - No Time Set'];
		$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
		if(in_array($ticket['status'],$calendar_checkmark_status)) {
			$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
		}
	}
	foreach ($tickets_multiday as $ticket) {
		$tickets_not_scheduled[] = ['id' => $ticket['ticketid'], 'note' => $ticket['ticketid'].' - Multi-Day '.TICKET_NOUN.' that cannot be displayed'];
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
				$ticket_urls .= "<a href='".WEBSITE_URL."/Ticket/index.php?edit=".$ticket['id']."' onclick='overlayIFrameSlider(this.href+\"&calendar_view=true\"); return false;'>#".$ticket['id'].'</a>
                <img src="../img/info.png" title="'.$ticket['note'].'" class="no-toggle inline-img">
                <a href="" onclick="removeTicketSchedule('.$ticket['id'].'); return false;"><img src="../img/remove.png" class="inline-img no-toggle" title="Remove From Day"></a>, ';
			} else {
				$ticket_urls .= "#".$ticketid.' <img src="../img/info.png" title="'.$ticket['note'].'" class="no-toggle inline-img">, ';
			}
		}
		$ticket_urls = rtrim($ticket_urls, ', ');
		$calendar_table[$calendar_date][$contact_id]['warnings'] .= $ticket_urls.'<br>';
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
					$appt_urls .= "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Calendar/booking.php?".http_build_query($appt_page_query)."\"); return false;'>#".$bookingid.'</a>
                        <img src="../img/info.png" title="Conflicting or Out of Calendar Time-Frame" class="no-toggle inline-img">, ';
				} else {
					$appt_urls .= '#'.$bookingid.', ';
				}
			}
			unset($page_query['action']);
			unset($page_query['bookingid']);
		}
		$appt_urls = rtrim($appt_urls, ', ');
		$calendar_table[$calendar_date][$contact_id]['warnings'] .= $appt_urls.'<br>';
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
			$ticket_urls .= "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Ticket/preview_ticket.php?action=view&ticketid=".$ticketid."\"); return false;'>#".$ticketid.'</a>
                <img src="../img/info.png" class="inline-img no-toggle" title="Conflicting or Out of Calendar Time-Frame">
                <a href="" onclick="removeTicketSchedule('.$ticketid.'); return false;"><img src="../img/remove.png" class="inline-img no-toggle" title="Remove From Day"></a>, ';
		}
		$ticket_urls = rtrim($ticket_urls, ', ');
		$calendar_table[$calendar_date][$projectid]['warnings'] = $ticket_urls;
	}
} else if($_GET['block_type'] == 'equipment') {
    $equip_options = explode(',',get_config($dbc, 'equip_options'));
	$equipment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `equipmentid`, `unit_number`, `make`, `model`, `category`, `region`, CONCAT(`category`, ' #', `unit_number`) `label`, `region`, `classification`, `next_service_date`, `follow_up_date` FROM `equipment` WHERE `equipmentid` = '$contact_id'"));

	$calendar_table[$calendar_date][$equipment['equipmentid']]['region'] = explode('*#*',$equipment['region'])[0];
	// Equipment Blocks
	$equip_assign = mysqli_fetch_array(mysqli_query($dbc, "SELECT ea.*, e.*, ea.`notes`, ea.`classification` FROM `equipment_assignment` ea LEFT JOIN `equipment` e ON ea.`equipmentid` = e.`equipmentid` WHERE e.`equipmentid` = '".$equipment['equipmentid']."' AND ea.`deleted` = 0 AND DATE(`start_date`) <= '$calendar_date' AND DATE(ea.`end_date`) >= '$calendar_date' AND CONCAT(',',ea.`hide_days`,',') NOT LIKE '%,$calendar_date,%' ORDER BY ea.`start_date` DESC, ea.`end_date` ASC, e.`category`, e.`unit_number`"));
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
                $row_cat = get_contact($dbc, $team_contact['contactid'], 'category');
                $row_name = '';
                if($row_cat == 'Staff') {
                    $row_name = get_contact($dbc, $team_contact['contactid']);
                } else {
                    $row_name = get_contact($dbc, $team_contact['contactid'],'name_company');
                }
	    		$team_contactids[$team_contact['contactid']] = [$row_cat, $row_name, $team_contact['contact_position']];
        	}
        }

        $equip_assign_contacts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '".$equip_assign['equipment_assignmentid']."' AND `deleted` = 0"),MYSQLI_ASSOC);
        foreach ($equip_assign_contacts as $equip_assign_contact) {
        	if(!empty($equip_assign_contact['contactid'])) {
	    		$team_contactids[$equip_assign_contact['contactid']] = [get_contact($dbc, $equip_assign_contact['contactid'], 'category'), get_contact($dbc, $equip_assign_contact['contactid']), $equip_assign_contact['contact_position']];
	    	}
        }

        foreach ($team_contactids as $key => $value) {
        	$cur_staff = '<span class="equip_assign_staff" data-contact="'.$key.'" data-contact-name="'.$value[1].'">'.($edit_access == 1 ? '<sup><a href="" onclick="removeStaffEquipAssign(this); return false;" style="font-size: x-small; color: #888; text-decoration: none; top: -0.2em; position: relative;">x</a></sup>' : '').$value[0].': '.(!empty($value[2]) ? $value[2].': ' : '').$value[1].'</span>';
        	$team_name .= $cur_staff.'<br />';
        }
        $team_name = rtrim($team_name, '<br />');

        // Display Region, optionally
        $region_label = '';
        if(in_array('region_sort',$equip_options) && !empty($equipment['region'])) {
            $region_label = ' <small>('.str_replace('*#*', ', ', $equipment['region']).')</small>';
        }

        // Display Classification
		$equip_classifications = implode('*#*',array_filter(array_unique([$equipment['classification'], $equip_assign['classification']])));
		$equip_classifications = implode('*#*', array_filter(array_unique(explode('*#*', $equip_classifications))));
		$classification_label = '';
		if($equip_display_classification_ticket == 1) {
			$equip_classifications = getEquipmentTicketClassification($dbc, $equipment['equipmentid'], $calendar_date, $calendar_date);
			if(!empty($equip_classifications)) {
				$classification_label = ' - '.implode(', ', $equip_classifications);
			}
		} else if($equip_display_classification == 1 && !empty($equip_classifications)) {
			$classification_label = ' - '.str_replace('*#*', ', ', $equip_classifications);
		}

	    $query = $_GET;
	    unset($query['equipment_assignmentid']);
	    unset($query['teamid']);
	    unset($query['unbooked']);
		$calendar_table[$calendar_date][$equipment['equipmentid']]['title'] = '<div class="equip_assign_block" data-equip-label="'.$equipment['label'].'" data-date="'.$calendar_date.'" data-equip="'.$equipment['equipmentid'].'" data-equip-assign="'.$equip_assign['equipment_assignmentid'].'">'.($edit_access == 1 ? '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Calendar/equip_assign.php?equipment_assignmentid='.$equip_assign['equipment_assignmentid'].'&region='.$_GET['region'].'\'); return false;">' : '').$equipment['label'].$region_label.$classification_label.(!empty($client) ? ' - '.$client : '').($edit_access == 1 ? '</a>' : '').'<br />'.$team_name.'</div>';
	} else {
		$team_name = '(No Team Assigned)';

        // Display Region, optionally
        $region_label = '';
        if(in_array('region_sort',$equip_options) && !empty($equipment['region'])) {
            $region_label = ' <small>('.str_replace('*#*', ', ', $equipment['region']).')</small>';
        }

        // Display Classification
		$equip_classifications = implode('*#*', array_filter(array_unique(explode('*#*', $equipment['classification']))));
		$classification_label = '';
		if($equip_display_classification_ticket == 1) {
			$equip_classifications = getEquipmentTicketClassification($dbc, $equipment['equipmentid'], $calendar_date, $calendar_date);
			if(!empty($equip_classifications)) {
				$classification_label = ' - '.implode(', ', $equip_classifications);
			}
		} else if($equip_display_classification == 1 && !empty($equip_classifications)) {
			$classification_label = ' - '.str_replace('*#*', ', ', $equip_classifications);
		}

	    $query = $_GET;
	    unset($query['equipment_assignmentid']);
	    unset($query['teamid']);
	    unset($query['unbooked']);
		$calendar_table[$calendar_date][$equipment['equipmentid']]['title'] = '<div class="equip_assign_block" data-date="'.$calendar_date.'" data-equip="'.$equipment['equipmentid'].'">'.($edit_access == 1 ? '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Calendar/equip_assign.php?equipment_assignmentid=NEW&equipmentid='.$equipment['equipmentid'].'&region='.$_GET['region'].'&start_date='.$calendar_date.'&end_date='.$calendar_date.'\'); return false;">' : '').$equipment['label'].$region_label.$classification_label.($edit_access == 1 ? '</a>' : '').'<br />'.$team_name.'</div>';
	}
	// Add Sorting and Mapping icons
    $map_sort_warning = '';
    $map_sortable = explode(',',get_config($dbc, 'scheduling_calendar_sort_auto'));
	if(in_array('map_sort', $map_sortable) && $edit_access == 1) {
        $map_sort_tickets = $dbc->query("SELECT `tickets`.*, `ticket_schedule`.`id`, CONCAT(IF(REPLACE(IFNULL(IFNULL(NULLIF(`ticket_schedule`.`serviceid`,''),`tickets`.`serviceid`),''),',','')='','Missing Services, ',''),IF(NULLIF(IFNULL(NULLIF(IFNULL(NULLIF(IFNULL(NULLIF(`ticket_schedule`.`est_time`,0),`ticket_schedule`.`cust_est`),'00:00'),`tickets`.`est_time`),0),`tickets`.`max_time`),'00:00:00') IS NULL,'No Time Estimate Added','')) `errors` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` WHERE `tickets`.`deleted`=0 AND `tickets`.`status` NOT IN ('Archive','Archived') AND IFNULL(NULLIF(`ticket_schedule`.`equipmentid`,0),`tickets`.`equipmentid`) = '".$equipment['equipmentid']."' AND IFNULL(NULLIF(`ticket_schedule`.`to_do_date`,''),`tickets`.`to_do_date`) = '".$calendar_date."' AND (REPLACE(IFNULL(IFNULL(NULLIF(`ticket_schedule`.`serviceid`,''),`tickets`.`serviceid`),''),',','')='' OR NULLIF(IFNULL(NULLIF(IFNULL(NULLIF(IFNULL(NULLIF(`ticket_schedule`.`est_time`,0),`ticket_schedule`.`cust_est`),0),`tickets`.`est_time`),0),`tickets`.`max_time`),'00:00:00') IS NULL) GROUP BY `tickets`.`ticketid`");
        $map_sorted_ticket = $dbc->query("SELECT * FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` WHERE `tickets`.`deleted`=0 AND `tickets`.`status` NOT IN ('Archive','Archived') AND IFNULL(NULLIF(`ticket_schedule`.`equipmentid`,0),`tickets`.`equipmentid`) = '".$equipment['equipmentid']."' AND IFNULL(NULLIF(`ticket_schedule`.`to_do_date`,''),`tickets`.`to_do_date`) = '".$calendar_date."' AND IFNULL(NULLIF(`ticket_schedule`.`sort_status`,0),`tickets`.`sort_status`)=1");
        $map_unsorted_tickets = $dbc->query("SELECT * FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` WHERE `tickets`.`deleted`=0 AND `tickets`.`status` NOT IN ('Archive','Archived') AND IFNULL(NULLIF(`ticket_schedule`.`equipmentid`,0),`tickets`.`equipmentid`) = '".$equipment['equipmentid']."' AND IFNULL(NULLIF(`ticket_schedule`.`to_do_date`,''),`tickets`.`to_do_date`) = '".$calendar_date."' AND IFNULL(NULLIF(`ticket_schedule`.`sort_status`,0),`tickets`.`sort_status`)=0");
        if($map_sort_tickets->num_rows > 0) {
            // Add warning if sorting is disabled due to missing fields
            while($map_sort_error = $map_sort_tickets->fetch_assoc()) {
                $map_sort_warning .= '<a href="../Ticket/index.php?edit='.$map_sort_error['ticketid'].'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\',\'auto\',true,false); return false;" class="no-toggle">'.get_ticket_label($dbc, $map_sort_error).'</a>
                    <img src="../img/info.png" class="no-toggle inline-img" title="Unable to Sort due to '.trim($map_sort_error['errors'],', ').'">
                    <a href="" onclick="removeTicketSchedule('.$map_sort_error['ticketid'].',\''.$map_sort_error['id'].'\'); return false;"><img src="../img/remove.png" class="inline-img no-toggle" title="Remove From Day"></a><br />';
            }
        } else if(($map_unsorted_tickets->num_rows > 0 || !in_array('limit_single_optimize',$map_sortable)) && $map_unsorted_tickets->num_rows > 0 && (!in_array_starts('class_',$map_sortable) || in_array('class_'.config_safe_str($equipment['classification']),$map_sortable))) {
            $calendar_table[$calendar_date][$equipment['equipmentid']]['title'] .= '<a href="" class="pull-right" onclick="get_addresses(\''.$calendar_date.'\', \''.$equipment['equipmentid'].'\'); return false;"><img class="inline-img" title="Sort '.TICKET_TILE.'" src="../img/sort-icon.png"></a>';
            if(in_array('hide_unoptimized',$map_sortable)) {
                $map_sort_warning .= $map_unsorted_tickets->num_rows.' '.TICKET_TILE.' Waiting to be Sorted'.($map_sorted_ticket->num_rows > 0 ? ' ('.$map_sorted_ticket->num_rows.' Already Sorted)' : '');
                $allow_unsorted = " AND IFNULL(NULLIF(`ticket_schedule`.`sort_status`,0),`tickets`.`sort_status`)=1 ";
            }
        } else if($map_unsorted_tickets->num_rows == 0 && in_array('limit_single_optimize',$map_sortable)) {
            $map_sort_warning .= 'Day Already Optimized';
        }
	}
	$calendar_table[$calendar_date][$equipment['equipmentid']]['title'] .= '<a href="" class="pull-right" onclick="get_day_map(\''.$calendar_date.'\', \''.$equipment['equipmentid'].'\'); return false;"><img class="inline-img" title="View Schedule Map" src="../img/icons/navigation.png"></a>';

	//Populate the text for the column header
	$equipment_notes[$equipment['equipmentid']] = html_entity_decode($equip_assign['notes']);

	//Pull all tickets for the current equipment
	// $all_tickets_sql = "SELECT * FROM `tickets` WHERE '".$calendar_date."' BETWEEN `to_do_date` AND `to_do_end_date` AND `equipmentid` = '".$equipment['equipmentid']."' AND `deleted` = 0 AND `status` NOT IN ('Archive', 'Done')";
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

	$warehouse_query = '';
	if($combine_warehouses == 1) {
		$warehouse_query = " AND REPLACE(REPLACE(IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''),IFNULL(`tickets`.`city`,''))),' ',''),'-','') NOT IN (SELECT REPLACE(REPLACE(CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')),' ',''),'-','') FROM `contacts` WHERE `category`='Warehouses')";
		$all_warehouses_sql = "SELECT `tickets`.*, `ticket_schedule`.`id` `stop_id`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`equipment_assignmentid`,`tickets`.`equipment_assignmentid`) `equipment_assignmentid`, IFNULL(`ticket_schedule`.`teamid`,`tickets`.`teamid`) `teamid`, IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`) `contactid`, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`id`, 0) `ticket_scheduleid`, IFNULL(`ticket_schedule`.`last_updated_time`,`tickets`.`last_updated_time`) `last_updated_time`, CONCAT(' - ',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name`, IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),' ',IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''), IFNULL(`tickets`.`city`,''))) `warehouse_full_address` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$calendar_date."' BETWEEN `tickets`.`to_do_date` AND IFNULL(`tickets`.`to_do_end_date`,`tickets`.`to_do_date`) OR '".$calendar_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND IFNULL(IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`),'') != '' AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done') AND REPLACE(REPLACE(IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''),IFNULL(`tickets`.`city`,''))),' ',''),'-','') IN (SELECT REPLACE(REPLACE(CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')),' ',''),'-','') FROM `contacts` WHERE `category`='Warehouses')".$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query.$ticket_customer_query.$allowed_ticket_types_query;
		$warehouse_tickets = mysqli_fetch_all(mysqli_query($dbc, $all_warehouses_sql),MYSQLI_ASSOC);
	}

	$pickup_query = '';
	if($combine_pickups == 1) {
		$pickup_query = " AND `ticket_schedule`.`type` != 'Pick Up'";
		$all_pickups_sql = "SELECT `tickets`.*, `ticket_schedule`.`id` `stop_id`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`equipment_assignmentid`,`tickets`.`equipment_assignmentid`) `equipment_assignmentid`, IFNULL(`ticket_schedule`.`teamid`,`tickets`.`teamid`) `teamid`, IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`) `contactid`, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`id`, 0) `ticket_scheduleid`, IFNULL(`ticket_schedule`.`last_updated_time`,`tickets`.`last_updated_time`) `last_updated_time`, CONCAT(' - ',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name`, IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),' ',IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''), IFNULL(`tickets`.`city`,''))) `pickup_full_address` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$calendar_date."' BETWEEN `tickets`.`to_do_date` AND IFNULL(`tickets`.`to_do_end_date`,`tickets`.`to_do_date`) OR '".$calendar_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND IFNULL(IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`),'') != '' AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done') AND `ticket_schedule`.`type` = 'Pick Up'".$warehouse_query.$allow_unsorted.$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query.$ticket_customer_query.$allowed_ticket_types_query;
		$pickup_tickets = mysqli_fetch_all(mysqli_query($dbc, $all_pickups_sql),MYSQLI_ASSOC);
	}

	$all_tickets_sql = "SELECT `tickets`.*, `ticket_schedule`.`id` `stop_id`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`equipment_assignmentid`,`tickets`.`equipment_assignmentid`) `equipment_assignmentid`, IFNULL(`ticket_schedule`.`teamid`,`tickets`.`teamid`) `teamid`, IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`) `contactid`, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`id`, 0) `ticket_scheduleid`, IFNULL(`ticket_schedule`.`last_updated_time`,`tickets`.`last_updated_time`) `last_updated_time`, CONCAT(' - ',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name`, IFNULL(`ticket_schedule`.`address`,`tickets`.`pickup_address`) `pickup_address`, IFNULL(`ticket_schedule`.`city`,`tickets`.`pickup_city`) `pickup_city`, `ticket_schedule`.`notes` `delivery_notes`, CONCAT(`start_available`,' - ',`end_available`) `availability` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$calendar_date."' BETWEEN `tickets`.`to_do_date` AND IFNULL(`tickets`.`to_do_end_date`,`tickets`.`to_do_date`) OR '".$calendar_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND IFNULL(IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`),'') != '' AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done')".$warehouse_query.$pickup_query.$allow_unsorted.$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query.$ticket_customer_query.$allowed_ticket_types_query;
	$tickets = mysqli_fetch_all(mysqli_query($dbc, $all_tickets_sql),MYSQLI_ASSOC);

	//Populate the text for the column header
	$equipment_notes[$equipment['equipmentid']] = html_entity_decode($equip_assign['notes']);

	//Loop through each time on the calendar and populate it
	$current_row = date('H:i:s', strtotime($day_start));
	while(strtotime($current_row) <= strtotime($day_end)) {
		if ($current_duration > 0) {
			$current_duration = $current_duration - ($day_period * 60);
			$current_ticket = ['ticket_equip', ''];
		} else {
			$current_ticket = '';
		}
		$current_tickets = [];
		foreach($tickets as $key => $ticket) {
			if(($current_ticket == '' || $combine_time == 1) && $current_row <= date('H:i:s', strtotime($ticket['to_do_start_time'])) && date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) > date('H:i:s', strtotime($ticket['to_do_start_time']))) {
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
				$current_tickets[] = $current_ticket;
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
				if(($current_warehouse == '' || strtolower($ticket['warehouse_full_address']) == strtolower($current_warehouse)) && $current_row <= date('H:i:s', strtotime($ticket['to_do_start_time'])) && date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) > date('H:i:s', strtotime($ticket['to_do_start_time']))) {
					$current_warehouse = $ticket['warehouse_full_address'];
					$current_count++;
					$current_ticketids[] = ($ticket['stop_id'] > 0 ? 'ticket_schedule-'.$ticket['stop_id'] : 'tickets-'.$ticket['ticketid']);
					$current_ticket = ['ticket_equip', 'warehouse', $current_warehouse, $current_count, $current_ticketids, $ticket];
					unset($warehouse_tickets[$key]);
					if($dont_count_warehouse != 1) {
						$calendar_table[$calendar_date][$equipment['equipmentid']]['total_tickets']++;
						if(in_array($ticket['status'],$calendar_checkmark_status)) {
							$calendar_table[$calendar_date][$equipment['equipmentid']]['completed_tickets']++;
						}
					}
				}
			}
		}
		if($combine_pickups == 1 && ($current_ticket == '' || $current_ticket[1] == '')) {
			$current_pickup = '';
			$current_count = 0;
			$current_ticketids = [];
			foreach($pickup_tickets as $key => $ticket) {
				if(($current_pickup == '' || $ticket['pickup_full_address'] == $current_pickup) && $current_row <= date('H:i:s', strtotime($ticket['to_do_start_time'])) && date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) > date('H:i:s', strtotime($ticket['to_do_start_time']))) {
					$current_pickup = $ticket['pickup_full_address'];
					$current_count++;
					$current_ticketids[] = ($ticket['stop_id'] > 0 ? 'ticket_schedule-'.$ticket['stop_id'] : 'tickets-'.$ticket['ticketid']);
					$current_ticket = ['ticket_equip', 'pickup', $current_pickup, $current_count, $current_ticketids, $ticket];
					unset($pickup_tickets[$key]);
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
		if(count($current_tickets) > 1 && $combine_time == 1) {
			$current_ticket = ['ticket_equip_combine', $current_tickets];
		}
		$calendar_table[$calendar_date][$equipment['equipmentid']][] = $current_ticket;
		$current_row = date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row)));
		if(date('Y-m-d H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) >= date('Y-m-d H:i:s', strtotime($day_end))) {
			break;
		}
	}
	foreach($pickup_tickets as $ticket) {
		$tickets[] = $ticket;
	}
	$tickets_not_scheduled = [];
	foreach ($tickets as $ticket) {
		$tickets_not_scheduled[] = ['id'=>$ticket['ticketid'],'stop'=>$ticket['id']];
		$calendar_table[$calendar_date][$equipment['equipmentid']]['total_tickets']++;
		if(in_array($ticket['status'],$calendar_checkmark_status)) {
			$calendar_table[$calendar_date][$equipment['equipmentid']]['completed_tickets']++;
		}
	}
	foreach($warehouse_tickets as $ticket) {
		$tickets_not_scheduled[] = ['id'=>$ticket['ticketid'],'stop'=>$ticket['id']];
		if($dont_count_warehouse != 1) {
			$calendar_table[$calendar_date][$equipment['equipmentid']]['total_tickets']++;
			if(in_array($ticket['status'],$calendar_checkmark_status)) {
				$calendar_table[$calendar_date][$equipment['equipmentid']]['completed_tickets']++;
			}
		}
	}
	$ticket_notes[$calendar_date][$equipment['equipmentid']] = $tickets_not_scheduled;

	//Add warnings
	$calendar_table[$calendar_date][$contact_id]['warnings'] = [];
	if($passed_service > 0 && $equipment['next_service_date'] < $calendar_date && $equipment['next_service_date'] < date('Y-m-d') && $equipment['next_service_date'] != '0000-00-00') {
		$calendar_table[$calendar_date][$contact_id]['warnings'][] = '<span style="font-weight: bold; color: red;">Service Date has passed ('.$equipment['next_service_date'].').</span> ';
	}
	if($service_followup > 0 && $equipment['follow_up_date'] == $calendar_date) {
		$calendar_table[$calendar_date][$contact_id]['warnings'][] = 'Follow Up: Next Service Date is scheduled for '.$equipment['next_service_date'].'. ';
	}
	if($service_date > 0 && $equipment['next_service_date'] == $calendar_date) {
		$calendar_table[$calendar_date][$contact_id]['warnings'][] = 'Next Service Date scheduled for today. ';
	}
	if($warning_num_tickets > 0 && $calendar_table[$calendar_date][$equipment['equipmentid']]['total_tickets'] >= $warning_num_tickets) {
		$calendar_table[$calendar_date][$contact_id]['warnings'][] = 'There are '.$calendar_table[$calendar_date][$equipment['equipmentid']]['total_tickets'].' '.TICKET_TILE.' on this day which exceeds the set limit of '.$warning_num_tickets.'. ';
	}
	if(!empty($map_sort_warning)) {
		$calendar_table[$calendar_date][$contact_id]['warnings'][] = $map_sort_warning;
	}

	if(!empty($ticket_notes[$calendar_date][$equipment['equipmentid']])) {
		$ticket_urls = '';
		foreach($ticket_notes[$calendar_date][$equipment['equipmentid']] as $ticketid) {
			if($edit_access == 1) {
                $calendar_table[$calendar_date][$equipment['equipmentid']]['warnings'][] = "<a href='".WEBSITE_URL."/Ticket/index.php?edit=".$ticketid['id']."' onclick='overlayIFrameSlider(this.href+\"&calendar_view=true\"); return false;'>#".$ticketid['id'].'</a>
                    <img src="../img/info.png" class="inline-img no-toggle" title="Conflicting or Out of Calendar Time-Frame">
                    <a href="" onclick="removeTicketSchedule('.$ticketid['id'].',\''.$ticketid['stop'].'\'); return false;"><img src="../img/remove.png" class="inline-img no-toggle" title="Remove From Day"></a>';
			} else {
                $calendar_table[$calendar_date][$equipment['equipmentid']]['warnings'][] = "#".$ticketid['id'].' <img class="no-toggle inline-img" title="Conflicting or Out of Calendar Time-Frame" src="../img/info.png">';
			}
		}
	}
	$calendar_table[$calendar_date][$contact_id]['warnings'] = implode('<br />', $calendar_table[$calendar_date][$contact_id]['warnings']);
} else if($_GET['type'] == 'schedule' && $_GET['block_type'] == 'dispatch_staff') {
	// Contact Blocks - Tickets
    $equip_options = explode(',',get_config($dbc, 'equip_options'));
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
        
        // Get Region Label
        $region_label = '';
        if(in_array('region_sort') && !empty($equipment_staff['region'])) {
            $region_label = ' <small>('.implode(', ',explode('*#*',$equipment_staff['region'])).')</small>';
        }
		$calendar_table[$calendar_date][$contact_id]['title'] .= '<br>('.($edit_access == 1 ? '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Calendar/equip_assign.php?equipment_assignmentid='.$equip_assign['equipment_assignmentid'].'&region='.$_GET['region'].'\'); return false;">' : '').$equipment_staff['label'].$region_label.($edit_access == 1 ? '</a>' : '').')';
	} else {
		$calendar_table[$calendar_date][$contact_id]['title'] .= '<br>(No Assignment)';
	}

	//Pull all tickets for the current contact
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

	$all_tickets_sql = "SELECT `tickets`.*, `ticket_schedule`.`id` `stop_id`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`equipment_assignmentid`,`tickets`.`equipment_assignmentid`) `equipment_assignmentid`, IFNULL(`ticket_schedule`.`teamid`,`tickets`.`teamid`) `teamid`, IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`) `contactid`, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`id`, 0) `ticket_scheduleid`, IFNULL(`ticket_schedule`.`last_updated_time`,`tickets`.`last_updated_time`) `last_updated_time`, CONCAT(' - ',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, `ticket_schedule`.`type` `delivery_type`, CONCAT(`start_available`,' - ',`end_available`) `availability` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$calendar_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$calendar_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND IFNULL(IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`),'') != '' AND (`tickets`.`contactid` LIKE '%,".$contact_id.",%' OR `ticket_schedule`.`contactid` LIKE '%,$contact_id,%') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done')".$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query.$ticket_customer_query.$allowed_ticket_types_query;
	$tickets = mysqli_fetch_all(mysqli_query($dbc, $all_tickets_sql),MYSQLI_ASSOC);

	//Loop through each time on the calendar and populate it
	$current_row = date('H:i:s', strtotime($day_start));
	while(strtotime($current_row) <= strtotime($day_end)) {
		$current_ticket = '';
		$current_tickets = [];
		foreach($tickets as $key => $ticket) {
			if(($current_ticket == '' || $combine_time == 1) && $current_row <= date('H:i:s', strtotime($ticket['to_do_start_time'])) && date('H:i:s', strtotime('+'.$day_period.' minutes', strtotime($current_row))) > date('H:i:s', strtotime($ticket['to_do_start_time']))) {
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
				$current_tickets[] = $current_ticket;
				unset($tickets[$key]);
				$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
				if(in_array($ticket['status'],$calendar_checkmark_status)) {
					$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
				}
			}
		}
		if(count($current_tickets) > 1 && $combine_time == 1) {
			$current_ticket = ['ticket_equip_combine', $current_tickets];
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
	$calendar_table[$calendar_date][$contact_id]['warnings'] = '';
	if($warning_num_tickets > 0 && $calendar_table[$calendar_date][$contact_id]['total_tickets'] >= $warning_num_tickets) {
		$calendar_table[$calendar_date][$contact_id]['warnings'] .= 'There are '.$calendar_table[$calendar_date][$contact_id]['total_tickets'].' '.TICKET_TILE.' on this day which exceeds the set limit of '.$warning_num_tickets.'.';
	}

	if(!empty($ticket_notes[$calendar_date][$contact_id])) {
		$ticket_urls = '';
		foreach($ticket_notes[$calendar_date][$contact_id] as $ticketid) {
			if($edit_access == 1) {
				$ticket_urls .= "<a href='".WEBSITE_URL."/Ticket/index.php?edit=".$ticketid."' onclick='overlayIFrameSlider(this.href+\"&calendar_view=true\"); return false;'>#".$ticketid.'</a>
                    <img src="../img/info.png" class="inline-img no-toggle" title="Conflicting or Out of Calendar Time-Frame">
                    <a href="" onclick="removeTicketSchedule('.$ticketid.'); return false;"><img src="../img/remove.png" class="inline-img no-toggle" title="Remove From Day"></a>, ';
			} else {
				$ticket_urls .= '#'.$ticketid.' <img src="../img/info.png" class="inline-img no-toggle" title="Conflicting or Out of Calendar Time-Frame">, ';
			}
		}
		$ticket_urls = rtrim($ticket_urls, ', ');
		$calendar_table[$calendar_date][$contact_id]['warnings'] .= "<br />".$ticket_urls;
	}
	$calendar_table[$calendar_date][$contact_id]['warnings'] = trim($calendar_table[$calendar_date][$contact_id]['warnings'], '<br />');
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
	if(get_config($dbc, 'shift_hide_if_day_off') == 1 && !empty($daysoff)) {
		$shifts = [];
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
				$shift_urls .= "<a href='?".http_build_query($page_query)."'>#".$shiftid.'</a><img src="../img/info.png" class="inline-img no-toggle" title="Conflicting or Out of Calendar Time-Frame">, ';
			} else {
				$shift_urls .= '#'.$shiftid.'<img src="../img/info.png" class="inline-img no-toggle" title="Conflicting or Out of Calendar Time-Frame">, ';
			}
			unset($page_query['shiftid']);
		}
		$shift_urls = rtrim($shift_urls, ', ');
		$calendar_table[$calendar_date][$contact_id]['warnings'] = $shift_urls;
	}
} else if(($calendar_type == 'ticket' || $_GET['type'] == 'uni') && $_GET['block_type'] == 'team') {
	// Contact Blocks - Tickets
	// $contact_id = explode('team_',$contact_id)[1];

	//Populate the text for the column header
	$team =	mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `teams` WHERE `teamid` = '$contact_id'"));
	$team_name = '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Calendar/teams.php?teamid='.$contact_id.'\'); return false;">'.(!empty($team['team_name']) ? $team['team_name'] : 'Team #'.$team['teamid']).'</a><br />';

	$contact_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$contact_id' AND `deleted` = 0"),MYSQLI_ASSOC);
	$contacts_query = [];
	$contacts_arr = [];
	foreach ($contact_list as $contact) {
		$team_name .= '<span class="team_staff" data-contact="'.$contact['contactid'].'" data-contact-name="'.get_contact($dbc, $contact['contactid']).'">'.($edit_access == 1 ? '<sup><a href="" onclick="removeStaffTeam(this); return false;" style="font-size: x-small; color: #888; text-decoration: none; top: -02.em; position: relative;">x</a></sup>' : '').(!empty($contact['contact_position']) ? $contact['contact_position'] : get_contact($dbc, $contact['contactid'], 'category')).': '.get_contact($dbc, $contact['contactid']).'</span><br />';
		if(strtolower(get_contact($dbc, $contact['contactid'], 'category')) == 'staff') {
			$contacts_query[] = " CONCAT(',',IFNULL(`contactid`,''),',') LIKE '%,".$contact['contactid'].",%'";
			$contacts_arr[] = $contact['contactid'];
		}
	}
	if(!empty($contacts_query)) {
		$contacts_query = " AND ".implode(" AND ", $contacts_query);
		$contacts_arr = array_filter(array_unique($contacts_arr));
		sort($contacts_arr);
		$contacts_arr = implode(',',$contacts_arr);
	} else {
		$contacts_query = " AND 1=0";
		$contacts_arr = ',PLACEHOLDER,';
	}
	$team_name = rtrim($team_name, '<br />');

	$calendar_table[$calendar_date][$contact_id]['title'] = '<div class="team_assign_block" data-date="'.$calendar_date.'" data-team="'.$contact_id.'">'.$team_name.'</div>';
	$calendar_table[$calendar_date][$contact_id]['calendar_type'] = $calendar_type;

	//Pull all tickets for the current contact from the ticket table
	$all_tickets_sql = "SELECT *, IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) `to_do_end_date` FROM `tickets` WHERE '".$calendar_date."' BETWEEN `to_do_date` AND IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) AND `deleted` = 0 AND `status` NOT IN ('Archive', 'Done', 'Internal QA', 'Customer QA')".$contacts_query.$allowed_ticket_types_query;
	$calendar_table[$calendar_date][$contact_id]['deleted_tickets'] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `num_rows` FROM `tickets` WHERE '".$calendar_date."' BETWEEN `to_do_date` AND IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) AND (`deleted` = 1 OR `status` IN ('Archive', 'Done'))".$contacts_query.$allowed_ticket_types_query))['num_rows'];
	$result_tickets_sql = mysqli_query($dbc, $all_tickets_sql);
	$tickets_time = [];
	$tickets_notime = [];
	$tickets_multiday = [];
	while($row_tickets = mysqli_fetch_array($result_tickets_sql)) {
		$ticket_contacts = array_filter(array_unique(explode(',',$row_tickets['contactid'])));
		sort($ticket_contacts);
		if(implode(',',$ticket_contacts) == $contacts_arr) {
	        if ($calendar_date >= $row_tickets['to_do_date'] && $calendar_date <= $row_tickets['to_do_end_date']) {
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
	}

	//Pull all shifts for the current contact from the contacts_shifts table
	$shifts = [];
	$daysoff = [];

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
			$current_start_time = date('H:i:s', strtotime($ticket['to_do_start_time']));
			if (!empty($ticket['to_do_end_time'])) {
				$ticket_duration = (strtotime($ticket['to_do_end_time']) - strtotime($current_start_time));
			} else {
				$estimated_time = explode(':',$ticket['max_time']);
				$ticket_duration = ($estimated_time[0] * 3600) + ($estimated_time[1] * 60);
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
			$estimated_time = explode(':', $ticket_notime['max_time']);
			$ticket_duration = ($estimated_time[0] * 3600) + ($estimated_time[1] * 60);
			$current_ticket = ['ticket', $ticket_notime];
			$current_duration = $ticket_duration - ($day_period * 60);
			$calendar_table[$calendar_date][$contact_id]['total_tickets']++;
			if(in_array($ticket_notime['status'],$calendar_checkmark_status)) {
				$calendar_table[$calendar_date][$contact_id]['completed_tickets']++;
			}
		}
		if (empty($shifts) && $current_ticket == '') {
			$current_ticket = ['ticket', 'SHIFT', $current_row, $calendar_date, ($contacts_arr != ',PLACEHOLDER,') ? $contacts_arr : ''];
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
				$ticket_urls .= "<a href='".WEBSITE_URL."/Ticket/index.php?edit=".$ticketid."' onclick='overlayIFrameSlider(this.href+\"&calendar_view=true\"); return false;'>#".$ticketid.'</a>
                    <img src="../img/info.png" class="inline-img no-toggle" title="Conflicting or Out of Calendar Time-Frame">
                    <a href="" onclick="removeTicketSchedule('.$ticketid.'); return false;"><img src="../img/remove.png" class="inline-img no-toggle" title="Remove From Day"></a>, ';
			} else {
				$ticket_urls .= '#'.$ticketid.' <img src="../img/info.png" class="inline-img no-toggle" title="Conflicting or Out of Calendar Time-Frame">, ';
			}
		}
		$ticket_urls = rtrim($ticket_urls, ', ');
		$calendar_table[$calendar_date][$contact_id]['warnings'] = $ticket_urls;
	}
} else if($calendar_type == 'ticket') {
	// Contact Blocks - Tickets

	//Populate the text for the column header
	$calendar_table[$calendar_date][$contact_id]['title'] = $profile_icon.get_contact($dbc, $contact_id);
	$calendar_table[$calendar_date][$contact_id]['calendar_type'] = $calendar_type;

	//Pull all tickets for the current contact from the ticket table
	if($_GET['mode'] == 'client') {
		$all_tickets_sql = "SELECT *, IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) `to_do_end_date` FROM `tickets` WHERE (internal_qa_date = '".$calendar_date."' OR `deliverable_date` = '".$calendar_date."' OR '".$calendar_date."' BETWEEN `to_do_date` AND IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`)) AND (CONCAT(',',`businessid`,',') LIKE '%,".$contact_id.",%' OR CONCAT(',',`clientid`,',') LIKE '%,".$contact_id.",%') AND `deleted` = 0 AND `status` NOT IN ('Archive', 'Done')".$allowed_ticket_types_query;
		$calendar_table[$calendar_date][$contact_id]['deleted_tickets'] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `num_rows` FROM `tickets` WHERE (internal_qa_date = '".$calendar_date."' OR `deliverable_date` = '".$calendar_date."' OR '".$calendar_date."' BETWEEN `to_do_date` AND IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`)) AND (CONCAT(',',`businessid`,',') LIKE '%,".$contact_id.",%' OR CONCAT(',',`clientid`,',') LIKE '%,".$contact_id.",%') AND (`deleted` = 1 OR `status` IN ('Archive', 'Done'))".$allowed_ticket_types_query))['num_rows'];
	} else {
		$all_tickets_sql = "SELECT *, IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) `to_do_end_date` FROM `tickets` WHERE (internal_qa_date = '".$calendar_date."' OR `deliverable_date` = '".$calendar_date."' OR '".$calendar_date."' BETWEEN `to_do_date` AND IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`)) AND (CONCAT(',',`contactid`,',') LIKE '%,".$contact_id.",%' OR CONCAT(',',`internal_qa_contactid`,',') LIKE '%,".$contact_id.",%' OR CONCAT(',',`deliverable_contactid`,',') LIKE '%,".$contact_id.",%') AND `deleted` = 0 AND `status` NOT IN ('Archive', 'Done')".$allowed_ticket_types_query;
		$calendar_table[$calendar_date][$contact_id]['deleted_tickets'] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `num_rows` FROM `tickets` WHERE (internal_qa_date = '".$calendar_date."' OR `deliverable_date` = '".$calendar_date."' OR '".$calendar_date."' BETWEEN `to_do_date` AND IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`)) AND (CONCAT(',',`contactid`,',') LIKE '%,".$contact_id.",%' OR CONCAT(',',`internal_qa_contactid`,',') LIKE '%,".$contact_id.",%' OR CONCAT(',',`deliverable_contactid`,',') LIKE '%,".$contact_id.",%') AND (`deleted` = 1 OR `status` IN ('Archive', 'Done'))".$allowed_ticket_types_query))['num_rows'];
	}
	$result_tickets_sql = mysqli_query($dbc, $all_tickets_sql);
	$tickets_time = [];
	$tickets_notime = [];
	$tickets_multiday = [];
	while($row_tickets = mysqli_fetch_array($result_tickets_sql)) {
        if(($row_tickets['status'] == 'Internal QA') && ($calendar_date == $row_tickets['internal_qa_date']) && (strpos(','.$row_tickets['internal_qa_contactid'].',', ','.$contact_id.',') !== FALSE)) {
        	if (!empty($row_tickets['internal_qa_start_time'])) {
        		$tickets_time[] = $row_tickets;
        	} else {
        		$tickets_notime[] = $row_tickets;
        	}
        } else if (($row_tickets['status'] == 'Customer QA' || $row_tickets['status'] == 'Waiting On Customer') && ($calendar_date == $row_tickets['deliverable_date']) && (strpos(','.$row_tickets['deliverable_contactid'].',', ','.$contact_id.',') !== FALSE)) {
        	if (!empty($row_tickets['deliverable_start_time'])) {
        		$tickets_time[] = $row_tickets;
        	} else {
        		$tickets_notime[] = $row_tickets;
        	}
        } else if (($row_tickets['status'] != 'Customer QA' && $row_tickets['status'] != 'Internal QA') && ($calendar_date >= $row_tickets['to_do_date'] && $calendar_date <= $row_tickets['to_do_end_date']) && (strpos(','.$row_tickets['contactid'].',', ','.$contact_id.',') !== FALSE || $_GET['mode'] == 'client')) {
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
				$ticket_urls .= "<a href='".WEBSITE_URL."/Ticket/index.php?edit=".$ticketid."' onclick='overlayIFrameSlider(this.href+\"&calendar_view=true\"); return false;'>#".$ticketid.'</a>
                    <img src="../img/info.png" class="inline-img no-toggle" title="Conflicting or Out of Calendar Time-Frame">
                    <a href="" onclick="removeTicketSchedule('.$ticketid.'); return false;"><img src="../img/remove.png" class="inline-img no-toggle" title="Remove From Day"></a>, ';
			} else {
				$ticket_urls .= '#'.$ticketid.' <img src="../img/info.png" class="inline-img no-toggle" title="Conflicting or Out of Calendar Time-Frame">, ';
			}
		}
		$ticket_urls = rtrim($ticket_urls, ', ');
		$calendar_table[$calendar_date][$contact_id]['warnings'] = $ticket_urls;
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
				$workorder_urls .= "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Work Order/edit_workorder.php?action=view&workorderid=".$workorderid."\"); return false;'>#".$workorderid.'</a> <img src="../img/info.png" class="inline-img no-toggle" title="Conflicting or Out of Calendar Time-Frame">, ';
			} else {
				$workorder_urls .= '#'.$workorderid.' <img src="../img/info.png" class="inline-img no-toggle" title="Conflicting or Out of Calendar Time-Frame">, ';
			}
		}
		$workorder_urls = rtrim($workorder_urls, ', ');
		$calendar_table[$calendar_date][$contact_id]['warnings'] = $workorder_urls;
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
				$appt_urls .= "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Calendar/booking.php?".http_build_query($appt_page_query)."\"); return false;'>#".$bookingid.'</a> <img src="../img/info.png" class="inline-img no-toggle" title="Conflicting or Out of Calendar Time-Frame">, ';
			} else {
				$appt_urls .= '#'.$bookingid.' <img src="../img/info.png" class="inline-img no-toggle" title="Conflicting or Out of Calendar Time-Frame">, ';
			}
			unset($page_query['action']);
			unset($page_query['bookingid']);
		}
		$appt_urls = rtrim($appt_urls, ', ');
		$calendar_table[$calendar_date][$contact_id]['warnings'] = $appt_urls;
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
			$est_urls .= '<a href="'.WEBSITE_URL.'/Estimate/estimates.php?view='.$estimateid.'" onclick="overlayIFrameSlider('.WEBSITE_URL.'/Estimate/estimates.php?view='.$estimateid.'); return false;" style="color: black;">#'.$estimateid.'</a> <img src="../img/info.png" class="inline-img no-toggle" title="Conflicting or Out of Calendar Time-Frame">, ';
		}
		$est_urls = rtrim($est_urls, ', ');
		$calendar_table[$calendar_date][$contact_id]['warnings'] .= $est_urls."<br>";
	}
}

if(!isset($equipment)) {
	if($_GET['block_type'] != 'team') {
		//Add notes
		$contact_notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `calendar_notes` WHERE `date` = '".$calendar_date."' AND `contactid` = '".$contact_id."' AND `deleted` = 0 AND `is_equipment` = 0"))['note'];
		$calendar_table[$calendar_date][$contact_id]['notes'] = html_entity_decode($contact_notes);

		//Add reminders
		$reminder_html = [];
		include_once('../Profile/daysheet_functions.php');
	    $reminders_list = mysqli_query($dbc, "SELECT * FROM `daysheet_reminders` WHERE `date` = '".$calendar_date."' AND `contactid` = '".$contact_id."' AND `deleted` = 0 ORDER BY `done` ASC");

	    if(mysqli_num_rows($reminders_list) > 0) {
	        $reminders_list = mysqli_fetch_all($reminders_list,MYSQLI_ASSOC);
		    foreach ($reminders_list as $daysheet_reminder) {
		        $reminder_label = daysheet_get_reminder($dbc, $daysheet_reminder);
		        if(!empty($reminder_label)) {
		        	if($edit_access != 1) {
		        		$reminder_label = strip_tags($reminder_label);
		        	}
		        	$reminder_html[] = '<div style="font-weight: normal;" class="daysheet_row">'.($_SESSION['contactid'] == $contact_id ? '<input style="position: relative; vertical-align: middle; top: -0.25em; width: 1.5em; height: 1.5em;" type="checkbox" name="daysheet_reminder" value="'.$daysheet_reminder['daysheetreminderid'].'" '.($daysheet_reminder['done'] == 1 ? 'checked="checked"' : '').' onclick="setDaysheetReminder(this);">&nbsp;&nbsp;' : '').'<span '.($daysheet_reminder['done'] == 1 ? 'style="text-decoration: line-through;"' : '').'>'.$reminder_label.'</span></div>';
		        }
		    }
		}

		$calendar_table[$calendar_date][$contact_id]['reminders'] = implode('',$reminder_html);
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
	if($ticket_summary_deleted == 1 && $calendar_table[$calendar_date][$contact_id]['deleted_tickets'] > 0) {
		$calendar_table[$calendar_date][$contact_id]['ticket_summary'] .= '<br />'.$calendar_table[$calendar_date][$contact_id]['deleted_tickets'].' Deleted '.($calendar_table[$calendar_date][$contact_id]['deleted_tickets'] == 1 ? TICKET_NOUN : TICKET_TILE);
	}
}

if(!isset($equipment)) {
	$calendar_table[$calendar_date][$contact_id]['shifts'] = '';
	if($_GET['block_type'] == 'team') {
		$team =	mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `teams` WHERE `teamid` = '$contact_id'"));

		$contact_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$contact_id' AND `deleted` = 0"),MYSQLI_ASSOC);
		$contacts_arr = [];
		foreach ($contact_list as $contact) {
			$shifts = checkShiftIntervals($dbc, $contact['contactid'], $day_of_week, $calendar_date, 'shifts');
			$daysoff = checkShiftIntervals($dbc, $contact['contactid'], $day_of_week, $calendar_date, 'daysoff');
			$shifts_arr = [];
			if(!empty($daysoff)) {
				$shifts_arr = ['<b>'.get_contact($dbc, $contact['contactid']).'</b>'];
				foreach($daysoff as $dayoff) {
					$shifts_arr[] = "Time Off: ".date('h:i a', strtotime((empty($dayoff['starttime']) ? '12:00 am' : $dayoff['starttime']))).' - '.date('h:i a', strtotime((empty($dayoff['endtime']) ? '11:59 pm' : $dayoff['endtime'])));
				}
			} else if(!empty($shifts)) {
				$shifts_arr = ['<b>'.get_contact($dbc, $contact['contactid']).'</b>'];
				foreach($shifts as $shift) {
					$shifts_arr[] = "Shift: ".date('h:i a', strtotime((empty($shift['starttime']) ? '12:00 am' : $shift['starttime']))).' - '.date('h:i a', strtotime((empty($shift['endtime']) ? '11:59 pm' : $shift['endtime'])));
				}
			}
			$contacts_arr[] = implode('<br />', $shifts_arr);
		}
		$calendar_table[$calendar_date][$contact_id]['shifts'] = implode('<br />', array_filter($contacts_arr));
	} else {
		$shifts = checkShiftIntervals($dbc, $contact_id, $day_of_week, $calendar_date, 'shifts');
		$daysoff = checkShiftIntervals($dbc, $contact_id, $day_of_week, $calendar_date, 'daysoff');
		$shifts_arr = [];
		if(!empty($daysoff)) {
			foreach($daysoff as $dayoff) {
				$shifts_arr[] = "Time Off: ".date('h:i a', strtotime((empty($dayoff['starttime']) ? '12:00 am' : $dayoff['starttime']))).' - '.date('h:i a', strtotime((empty($dayoff['endtime']) ? '11:59 pm' : $dayoff['endtime'])));
			}
		} else if(!empty($shifts)) {
			foreach($shifts as $shift) {
				$shifts_arr[] = "Shift: ".date('h:i a', strtotime((empty($shift['starttime']) ? '12:00 am' : $shift['starttime']))).' - '.date('h:i a', strtotime((empty($shift['endtime']) ? '11:59 pm' : $shift['endtime'])));
			}
		}
		$calendar_table[$calendar_date][$contact_id]['shifts'] = implode('<br />', $shifts_arr);
	}
} else {
	$calendar_table[$calendar_date][$equipment['equipmentid']]['shifts'] = '';
}

include('../Calendar/load_calendar_item_display.php');