<?php
$day_start = get_config($dbc, 'ticket_day_start');
$day_period = get_config($dbc, 'ticket_increments');
if(!isset($day_start)) {
	$day_start = "6:00 am";
}
if(!isset($day_period)) {
	$day_period = 15;
}
$region_list = explode(',',get_config($dbc, '%_region', true));
$region_colours = explode(',',get_config($dbc, '%_region_colour', true));
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

$all_tickets = [];
$tickets = mysqli_fetch_all(mysqli_query($dbc,"SELECT *, IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) `to_do_end_date` FROM `tickets` WHERE `deleted`=0 AND (`internal_qa_date` = '$new_today_date' OR `deliverable_date` = '$new_today_date' OR '$new_today_date' BETWEEN `to_do_date` AND IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`)) AND `status` NOT IN ('Archive','Done')".$allowed_ticket_types_query." ORDER BY ".(in_array('Ticket Type', $sidebar_filters) ? "`ticket_type`, " : '')." `to_do_start_time`, `to_do_end_time`"),MYSQLI_ASSOC);
$deleted_tickets = [];
if($ticket_summary_tab_deleted == 1) {
	$deleted_tickets = mysqli_fetch_all(mysqli_query($dbc,"SELECT *, IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) `to_do_end_date` FROM `tickets` WHERE (`internal_qa_date` = '$new_today_date' OR `deliverable_date` = '$new_today_date' OR '$new_today_date' BETWEEN `to_do_date` AND IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`)) AND (`deleted` = 1 OR `status` IN ('Archive','Done'))".$allowed_ticket_types_query." ORDER BY `to_do_start_time`, `to_do_end_time`"),MYSQLI_ASSOC);
}
$all_tickets = ['tickets'=>$tickets, 'deleted_tickets'=>$deleted_tickets];
$current_tickettype = '';
foreach($all_tickets as $key => $tickets) {
	if(count($tickets) > 0 && $key == 'deleted_tickets') {
		$column .= '<h4>Deleted '.TICKET_TILE.'</h4>';
	}
	foreach($tickets as $row) {
		$businessid = $row['businessid'];
		$clients = [];
		foreach(array_filter(explode(',',$row['clientid'])) as $clientid) {
			$client = !empty(get_client($dbc, $clientid)) ? get_client($dbc, $clientid) : get_contact($dbc, $clientid);
			if(!empty($client) && $client != '-') {
				$clients[] = $client;
			}
		}
		$clients = implode(', ',$clients);
		$heading = $row['heading'].$row['location_description'];
		$estimated_time = substr($row['max_time'], 0, 5);
		$status = $row['status'];
		if($calendar_checkmark_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
			$checkmark_ticket = 'calendar-checkmark-ticket';
		} else {
			$checkmark_ticket = '';
		}
		$status_class = $status;
		$rows = 1;
		$ticket_styling = '';

		$calendar_color = '#6DCFF6';
	    if($row['region'] != '') {
			foreach($region_list as $region_line => $region_name) {
				if($region_name == $row['region']) {
					$calendar_color = $region_colours[$region_line];
				}
			}
	    }
		if($calendar_ticket_color_code_tabs == 1 && !empty($ticket_tabs_color[$row['ticket_type']])) {
            $ticket_styling = ' background-color:'.$ticket_tabs_color[$row['ticket_type']].';';
        } else if($calendar_highlight_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
			$ticket_styling = ' background-color:'.$calendar_completed_color[$status].';';
		} else if($calendar_highlight_incomplete_tickets == 1 && in_array($status, $calendar_incomplete_status)) {
			$ticket_styling = ' background-color:'.$calendar_incomplete_color[$status].';';
		} else if (!empty($calendar_color)) {
			$ticket_styling = ' background-color:'.$calendar_color.';';
		}
		if ($status == 'Internal QA') {
			if (!empty($row['internal_qa_start_time'])) {
				$current_start_time = date('h:i a', strtotime($row['internal_qa_start_time']));
				if (!empty($row['internal_qa_end_time'])) {
					$duration = (strtotime($row['internal_qa_end_time']) - strtotime($current_start_time));
					$current_end_time = date('h:i a', strtotime($row['internal_qa_end_time']));
				} else {
					$max_time = explode(':',$row['max_qa_time']);
					$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
					$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
				}
			} else {
				$current_start_time = date('h:i a', strtotime($day_start) + ($calendar_row * $day_period * 60));
				$max_time = explode(':',$row['max_qa_time']);
				$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
				$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
			}
		} else if ($status == 'Customer QA') {
			if (!empty($row['deliverable_start_time'])) {
				$current_start_time = date('h:i a', strtotime($row['deliverable_start_time']));
				if (!empty($row['deliverable_end_time'])) {
					$duration = (strtotime($row['deliverable_end_time']) - strtotime($current_start_time));
					$current_end_time = date('h:i a', strtotime($row['deliverable_end_time']));
				} else {
					$max_time = explode(':',$row['max_qa_time']);
					$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
					$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
				}
			} else {
				$current_start_time = date('h:i a', strtotime($day_start) + ($calendar_row * $day_period * 60));
				$max_time = explode(':',$row['max_qa_time']);
				$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
				$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
			}
		} else {
			if (!empty($row['to_do_start_time'])) {
				$current_start_time = date('h:i a', strtotime($row['to_do_start_time']));
				if (!empty($row['to_do_end_time'])) {
					$duration = (strtotime($row['to_do_end_time']) - strtotime($current_start_time));
					$current_end_time = date('h:i a', strtotime($row['to_do_end_time']));
				} else {
					$max_time = explode(':',$row['max_time']);
					$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
					$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
				}
			} else {
				$current_start_time = date('h:i a', strtotime($day_start) + ($calendar_row * $day_period * 60));
				$max_time = explode(':',$row['max_time']);
				$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
				$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
			}
		}
		$date_color = 'block/green.png';
		if($new_today_date < date('Y-m-d',strtotime("-2 days"))) {
			$date_color = 'block/red.png';
		}
		if($new_today_date == date('Y-m-d',strtotime("-1 days")) || $new_today_date == date('Y-m-d',strtotime("-2 days"))) {
			$date_color = 'block/orange.png';
		}
		$status_icon = get_ticket_status_icon($dbc, $row['status']);
	    if(!empty($status_icon)) {
	        $icon_img = '';
	    	$icon_background = '';
	    	if($calendar_ticket_status_icon == 'background' && $status_icon != 'initials') {
				$icon_background = " background-image: url(\"".$status_icon."\"); background-repeat: no-repeat; height: 100%; background-size: contain; background-position: center;";
	    	} else {
		    	if($status_icon == 'initials') {
					$icon_img = '<span class="id-circle-small pull-right" style="background-color: #6DCFF6; font-family: \'Open Sans\';">'.get_initials($row['status']).'</span>';
		    	} else {
			        $icon_img = '<img src="'.$status_icon.'" class="pull-right" style="max-height: 20px;">';
			    }
			}
	    } else {
	        $icon_img = '';
	    	$icon_background = '';
	    }
	    $recurring_icon = '';
	    if($row['is_recurrence'] == 1) {
	    	$recurring_icon = "<img src='".WEBSITE_URL."/img/icons/recurring.png' style='width: 1.2em; margin: 0.1em;' class='pull-right' title='Recurring ".TICKET_NOUN."'>";
	    }

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

		$assigned_staff = [];
		foreach($all_contacts as $contact_id) {
			$assigned_staff[] = get_contact($dbc, $contact_id);
		}
		$assigned_staff = implode(', ', $assigned_staff);

		if($row['ticket_type'] != $current_tickettype && in_array('Ticket Type', $sidebar_filters)) {
			$current_tickettype = $row['ticket_type'];
			$column .= '<h4 data-tickettype="'.$row['ticket_type'].'" class="ticket_type_header">'.$ticket_types[$row['ticket_type']].'</h4>';
		}
	    $column .= '<div class="calendar_block calendarSortable" data-ticket="'.$row['ticketid'].'" data-region="'.$row['region'].'" data-blocktype="'.$row['block_type'].'" data-business="'.$row['businessid'].'" data-client="'.$row['clientid'].'" data-contact="'.implode(',',$all_contacts).'" data-team="'.$teams_list[implode(',',$all_contacts)].'" data-tickettype="'.$row['ticket_type'].'" data-equipment="'.$row['equipmentid'].'">';
		$column .= '<span class="sortable-blocks" style="display:block; margin: 0.5em; padding:5px; color:black; border-radius: 10px; background-color:'.$row['calendar_color'].';'.$ticket_styling.'"><a href="" onclick="'.($edit_access == 1 ? 'overlayIFrameSlider(\''.WEBSITE_URL.'/Ticket/index.php?calendar_view=true&edit='.$row['ticketid'].'\');' : '').'return false;">';
		$column .= $recurring_icon;
		if($key == 'deleted_tickets') {
			$column .= calendarTicketLabel($dbc, $row, $max_time, $current_start_time, $current_end_time, get_config($dbc, 'calendar_ticket_card_fields_summary_deleted'));
		} else {
			$column .= calendarTicketLabel($dbc, $row, $max_time, $current_start_time, $current_end_time, get_config($dbc, 'calendar_ticket_card_fields_summary'));
		}

		$column .= '</a></span>';
		$column .= '</div>';
	}
}

?>