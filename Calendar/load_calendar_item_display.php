<?php
$column = [];

$edit_access = vuaed_visible_function($dbc, 'calendar_rook');
if($is_customer) {
	$edit_access = 0;
}
$ticket_view_access = tile_visible($dbc, 'ticket');
$region_list = explode(',',get_config($dbc, '%_region', true));
$region_colours = explode(',',get_config($dbc, '%_region_colour', true));
$calendar_ticket_card_fields = explode(',',get_config($dbc, 'calendar_ticket_card_fields'));
$calendar_time_behind_cell = get_config($dbc, 'calendar_time_behind_cell');
$calendar_ticket_status_icon = get_config($dbc, 'calendar_ticket_status_icon');
$calendar_col = $calendar_table[$calendar_date][$contact_id];
$current_day = $calendar_date;
$today_date = date('Y-m-d');

if($today_date == $current_day && !$is_mobile_view) {
	$theme_active_color = get_calendar_today_color($dbc);
	$theme_active_color = darken_color($theme_active_color);
	$highlight_today = '; border-left: 3px solid #'.$theme_active_color.'; border-right: 3px solid #'.$theme_active_color.';';
	$background_highlight_today = '; background: #'.$theme_active_color.';';
}

if($_GET['type'] == 'ticket' || $_GET['type'] == 'uni') {
	if($_GET['block_type'] == 'team') {
		$contact_region = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `region` FROM `teams` WHERE `teamid` = '$contact_id'"))['region'];
	} else {
		$contact_region = explode(',', mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `region` FROM `contacts` WHERE `contactid` = '$contact_id'"))['region'])[0];
	}
	if(!empty($contact_region)) {
		foreach($region_list as $region_line => $region_name) {
			if($contact_region == $region_name) {
				if($today_date == $current_day && !$is_mobile_view) {
					$bg_color = darken_color(trim($region_colours[$region_line],'#'), 40);
					$highlight_today = '; border-left: 3px solid #'.$bg_color.'; border-right: 3px solid #'.$bg_color.';';
					$background_highlight_today = "; background: #".$bg_color.";color: #000;";
				} else {
					$column['title'] .= "background: ".$region_colours[$region_line].";color: #000;";
				}
			}
		}
	}
}

$calendar_table[0][0] = [];
$current_row = strtotime($day_start);
while($current_row <= strtotime($day_end)) {
	$calendar_table[0][0][] = date('g:i a', $current_row);
	$current_row = strtotime('+'.$day_period.' minutes', $current_row);
}

//Equip assign data
if(!empty($equipassign_data[$current_day][$contact_id])) {
	$equipassignid_data = "data-equipassign='".$equipassign_data[$current_day][$contact_id]."'";
} else {
	$equipassignid_data = "";
}

$region_group = '';
if($columns_group_regions == 1) {
	$region_group = $calendar_table[$calendar_date][$contact_id]['region'];
}

//Table header title
$column['title'] = '';
$column['title'] .= "<th ".($today_date == $current_day ? 'class="today-active"' : '' )." data-contact='$contact_id' data-blocktype='".$_GET['block_type']."' data-region-group='".$region_group."' $equipassignid_data data-date='".$current_day."' data-row='title' style='";
if($equipassign_data[$current_day][$contact_id] > 0) {
	$equipassign_region = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `region` FROM `equipment_assignment` WHERE `equipment_assignmentid`='".$equipassign_data[$current_day][$contact_id]."'"))['region'];
} else {
	$equipassign_region = $calendar_col['region'];
}
if(!empty($equipassign_region)) {
	foreach($region_list as $region_line => $region_name) {
		if($equipassign_region == $region_name) {
			if($today_date == $current_day && !$is_mobile_view) {
				$bg_color = darken_color(trim($region_colours[$region_line],'#'), 40);
				$highlight_today = '; border-left: 3px solid #'.$bg_color.'; border-right: 3px solid #'.$bg_color.';';
				$background_highlight_today = "; background: #".$bg_color.";color: #000;";
			} else {
				$column['title'] .= "background: ".$region_colours[$region_line].";color: #000;";
			}
		}
	}
}
$column['title'] .= ($contact_id > 0 ? 'border-left: 1px solid rgb(221, 221, 221); min-width: 15em; width: 50%;' : 'max-width: 7em; min-width: 7em; width: 7em;')."padding:0;".$highlight_today.$background_highlight_today."' data-color='".$bg_color."'><div class='resizer' style='min-width:100%; max-width:100%; padding:0.5em;'>";
$column['title'] .= ($current_day == 0 ? $calendar_col['title'] : ($_GET['view'] == 'daily' ? $calendar_col['title'] : date('l, F d', strtotime($current_day)).'<br>'.$calendar_col['title']))."</div></th>";

//Shifts
$column['shifts'] = '';
$column['shifts'] .= "<td ".($today_date == $current_day ? 'class="today-active"' : '' )." data-date='".$current_day."' data-contact='$contact_id' data-blocktype='".$_GET['block_type']."' data-region-group='".$region_group."' style='position:relative; ".($contact_id > 0 ? 'border-left: 1px solid rgb(221, 221, 221); min-width: 15em; width: 50%;' : 'max-width: 7em; min-width: 7em; width: 7em;').$highlight_today."'><div class='calendar_notes' style='overflow-y: hidden;'>".$calendar_col['shifts'].'</div>';
$column['shifts'] .= '<a class="expand-div-link" href="" onclick="expandDiv(this); return false;"><div style="font-size: 1.5em; text-align: center;">...</div></a>';

//Notes
$column['notes'] = '';
$column['notes'] .= "<td ".($today_date == $current_day ? 'class="today-active"' : '' )." data-date='".$current_day."' $equipassignid_data data-calendartype='".$_GET['type']."' data-calendarmode='".$_GET['mode']."' data-contact='$contact_id' data-blocktype='".$_GET['block_type']."' data-region-group='".$region_group."' style='position:relative; ".($contact_id > 0 ? 'border-left: 1px solid rgb(221, 221, 221); min-width: 15em; width: 50%;' : 'max-width: 7em; min-width: 7em; width: 7em;').$highlight_today."'><div class='calendar_notes' style='overflow-y: hidden;'>".$calendar_col['notes'].'</div>';
$column['notes'] .= '<div class="calendar_notes_btn" style="text-align: right; position: relative;">'.($edit_access == 1 ? '<a class="edit_calendar_notes" href=""><sub>EDIT</sub></a>' : '').'</div>';
$column['notes'] .= '<div class="calendar_notes_edit" style="display:none;"><textarea style="resize: vertical;" class="noMceEditor form-control">'.html_entity_decode($calendar_col['notes']).'</textarea></div>';
$column['notes'] .= '<a class="expand-div-link" href="" onclick="expandDiv(this); return false;"><div style="font-size: 1.5em; text-align: center;">...</div></a>';

//Reminders
$column['reminders'] = '';
$column['reminders'] .= "<td ".($today_date == $current_day ? 'class="today-active"' : '' )." data-date='".$current_day."' $equipassignid_data data-calendartype='".$_GET['type']."' data-calendarmode='".$_GET['mode']."' data-contact='$contact_id' data-blocktype='".$_GET['block_type']."' data-region-group='".$region_group."' style='position:relative; ".($contact_id > 0 ? 'border-left: 1px solid rgb(221, 221, 221); min-width: 15em; width: 50%;' : 'max-width: 7em; min-width: 7em; width: 7em;').$highlight_today."'><div class='calendar_notes' style='overflow-y: hidden;'>".$calendar_col['reminders'].'</div>';
if('reminders' == 'notes' && $contact_id != 0) {
	$column['reminders'] .= '<div class="calendar_notes_btn" style="text-align: right; position: relative;">'.($edit_access == 1 ? '<a class="edit_calendar_notes" href=""><sub>EDIT</sub></a>' : '').'</div>';
	$column['reminders'] .= '<div class="calendar_notes_edit" style="display:none;"><textarea style="resize: vertical;" class="noMceEditor form-control">'.html_entity_decode($calendar_col['reminders']).'</textarea></div>';
}
$column['reminders'] .= '<a class="expand-div-link" href="" onclick="expandDiv(this); return false;"><div style="font-size: 1.5em; text-align: center;">...</div></a>';

//Warnings
$column['warnings'] = '';
$column['warnings'] .= "<td ".($today_date == $current_day ? 'class="today-active"' : '' )." data-date='".$current_day."' $equipassignid_data data-calendartype='".$_GET['type']."' data-calendarmode='".$_GET['mode']."' data-contact='$contact_id' data-blocktype='".$_GET['block_type']."' data-region-group='".$region_group."' style='position:relative; ".($contact_id > 0 ? 'border-left: 1px solid rgb(221, 221, 221); min-width: 15em; width: 50%;' : 'max-width: 7em; min-width: 7em; width: 7em;').$highlight_today."'><div class='calendar_notes' style='overflow-y: hidden;'>".$calendar_col['warnings'].'</div>';
if('warnings' == 'notes' && $contact_id != 0) {
	$column['warnings'] .= '<div class="calendar_notes_btn" style="text-align: right; position: relative;">'.($edit_access == 1 ? '<a class="edit_calendar_notes" href=""><sub>EDIT</sub></a>' : '').'</div>';
	$column['warnings'] .= '<div class="calendar_notes_edit" style="display:none;"><textarea style="resize: vertical;" class="noMceEditor form-control">'.html_entity_decode($calendar_col['warnings']).'</textarea></div>';
}
$column['warnings'] .= '<a class="expand-div-link" href="" onclick="expandDiv(this); return false;"><div style="font-size: 1.5em; text-align: center;">...</div></a>';

//Ticket summary
$column['ticket_summary'] = '';
$column['ticket_summary'] .= "<td ".($today_date == $current_day ? 'class="today-active"' : '' )." data-date='".$current_day."' data-contact='$contact_id' data-blocktype='".$_GET['block_type']."' data-region-group='".$region_group."' data-draggable='0' style='position:relative; ".($contact_id > 0 ? 'border-left: 1px solid rgb(221, 221, 221); min-width: 15em; width: 50%;' : 'max-width: 7em; min-width: 7em; width: 7em;').$highlight_today."'>".$calendar_col['ticket_summary']."</td>";

//Rows
$column['rows'] = [];
foreach($calendar_table[0][0] as $calendar_row => $calendar_cell) {
	$locked_optimize = false;
	foreach(array_filter(explode(',',$calendar_table[$current_day][$contact_id]['region'])) as $region_name) {
		$lock_time = get_config($dbc, 'region_lock_'.config_safe_str($region_name));
		if($lock_time > time() - 40000) {
			$locked_optimize = true;
		}
	}
	foreach(array_filter(explode(',',$calendar_table[$current_day][$contact_id]['location_description'])) as $loc_name) {
		$lock_time = get_config($dbc, 'location_lock_'.config_safe_str($loc_name));
		if($lock_time > time() - 40000) {
			$locked_optimize = true;
		}
	}
	foreach(array_filter(explode(',',$calendar_table[$current_day][$contact_id]['classification'])) as $class_name) {
		$lock_time = get_config($dbc, 'classification_lock_'.config_safe_str($class_name));
		if($lock_time > time() - 40000) {
			$locked_optimize = true;
		}
	}
	$row_html = '';
	
	$is_shift = '';
	if ($calendar_col[$calendar_row][1] == 'SHIFT' || $calendar_col[$calendar_row][0] == 'no_shift') {
		$is_shift = ' background-color: #eee';
	}
	$row_html .= "<td ".($today_date == $current_day ? 'class="today-active"' : '' )." data-region='".$calendar_table[$current_day][$contact_id]['region']."' data-date='".$current_day."' data-calendartype='".$_GET['type']."' data-contact='$contact_id' data-blocktype='".$_GET['block_type']."' data-region-group='".$region_group."' $equipassignid_data data-time='$calendar_row' data-duration='".($day_period * 60)."' style='position:relative; ".($contact_id > 0 ? 'border-left: 1px solid rgb(221, 221, 221); min-width: 15em; width: 50%;' : 'max-width: 7em; min-width: 7em; width: 7em;').$is_shift.$highlight_today."'>";
	if($calendar_time_behind_cell == 1 && $calendar_row >= 0) {
		$current_row = date('h:i a', strtotime($day_start));
		$current_row = date('h:i a', strtotime('+'.($day_period * $calendar_row).' minutes', strtotime($current_row)));
		$row_html .= '<div class="calendar-cell-time">'.$current_row.'</div>';
	}
	if($contact_id > 0 && $calendar_col[$calendar_row] != '' && $calendar_col[$calendar_row] != $calendar_col[$calendar_row - 1]) {
        if($calendar_col[$calendar_row][0] == 'estimate') {
            $estimateid = $calendar_col[$calendar_row][1]['estimateid'];
            $estimate_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `estimate_name` FROM `estimate` WHERE `estimateid`='$estimateid'"));
            echo '<a href="'.WEBSITE_URL.'/Estimate/estimates.php?view='.$estimateid.'" onclick="overlayIFrameSlider('.WEBSITE_URL.'/Estimate/estimates.php?view='.$estimateid.'); return false;">Follow-Up: '. $estimate_name['estimate_name'] .'</a>';
        } else if(($_GET['type'] == 'uni' || $_GET['type'] == 'my') && empty($_GET['shiftid']) && $_GET['mode'] != 'shift' && $_GET['block_type'] != 'team') {
			$ticket_styling = '';
			$calendar_color = mysqli_fetch_array(mysqli_query($dbc, "SELECT `calendar_color` FROM `contacts` WHERE `contactid` = '".$contact_id."'"))['calendar_color'];
			if (!empty($calendar_color)) {
				$ticket_styling = ' background-color:'.$calendar_color.';';
			}
			if($calendar_col[$calendar_row][0] == 'appt') {
				$appt = $calendar_col[$calendar_row][1];
				if(!empty($appt)) {
					$rows = 1;
					$status_class = 'unconfirmed';
					switch($appt['follow_up_call_status']) {
						case 'Booking Confirmed':
							$status_class = 'confirmed';
							break;
						case 'Arrived':
							$status_class = 'arrived';
							break;
						case 'Invoiced':
							$status_class = 'invoiced';
							break;
						case 'Paid':
							$status_class = 'paid';
							break;
						case 'Rescheduled':
							$status_class = 'rescheduled';
							break;
						case 'Late Cancellation / No-Show':
							$status_class = 'late_noshow';
							break;
						case 'Cancelled':
							$status_class = 'cancelled';
							break;
					}
					if(date('Y-m-d', strtotime($appt['end_appoint_date'])) != date('Y-m-d', strtotime($appt['appoint_date']))) {
						$current_start_time = date('h:i a', strtotime($day_start) + ($calendar_row * $day_period * 60));
						$day_of_week = date('l', $current_day);
						$staff_shift = checkShiftIntervals($dbc, $contact_id, $day_of_week, $current_day)[0];
						if (date('Y-m-d', strtotime($appt['end_appoint_date'])) == $current_day && !empty($appt['end_appoint_date'])) { 
							$current_end_time = date('h:i a', strtotime($appt['end_appoint_date']));
						} else if (!empty($staff_shift['endtime'])) {
							$current_end_time = date('h:i a', strtotime($staff_shift['endtime']));
						} else {
							$current_end_time = date('h:i a', strtotime($day_end));
						}
						$duration = strtotime($current_end_time) - strtotime($current_start_time);
					} else {
						$current_start_time = date('h:i a', strtotime($appt['appoint_date']));
						$current_end_time = date('h:i a', strtotime($appt['end_appoint_date']));
						$duration = (strtotime($appt['end_appoint_date']) - strtotime($appt['appoint_date']));
					}
					if ($duration > $day_period * 60) {
						$rows = ceil($duration / ($day_period * 60));
					}
					if ($duration < $day_period * 60) {
						$duration = $day_period * 60;
					}
					$page_query['action'] = 'view';
					$page_query['bookingid'] = $appt['bookingid'];
					$appt_page_query = $page_query;
					unset($appt_page_query['add_reminder']);
					unset($appt_page_query['unbooked']);
					unset($appt_page_query['equipment_assignmentid']);
					unset($appt_page_query['teamid']);
					$row_html .= ($edit_access == 1 ? "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Calendar/booking.php?".http_build_query($appt_page_query)."\"); return false;'>" : "")."<div class='used-block' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-appt='".$appt['bookingid']."' data-clientid='".$appt['patientid']."' ";
					$row_html .= "data-duration='$duration' style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%; $ticket_styling'>";
					$row_html .= "<span class='$status_class' style='display: block; float: left; width: calc(100% - 2em);'>";
					$row_html .= "<b>".$current_start_time." - ".$current_end_time."</b><br />";
					$row_html .= get_contact($dbc, ($_GET['mode'] == 'client' ? $appt['therapistsid'] : $appt['patientid']))." - ".($appt['serviceid'] > 0 ? get_services($dbc, $appt['serviceid'], "CONCAT(`category`,' ',`heading`)") : get_type_from_booking($dbc, $appt['type'])).'<br />';
					$row_html .= $appt['follow_up_call_status'];
					$row_html .= "</span><div class='drag-handle full-height' title='Drag Me!'><img class='drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' style='filter: brightness(200%); float: right; width: 2em;' title='Drag'></div></div>".($edit_access == 1 ? "</a>" : "");
					unset($page_query['action']);
					unset($page_query['bookingid']);
				}
			} else if($calendar_col[$calendar_row][0] == 'ticket') {
				$ticket = $calendar_col[$calendar_row][1];
				if(!empty($ticket)) {
					$businessid = $ticket['businessid'];
					$contactid = $ticket['contactid'];
					$internal_qa_contactid = $ticket['internal_qa_contactid'];
					$deliverable_contactid = $ticket['deliverable_contactid'];
					$heading = $ticket['heading'].$ticket['location_description'];
					$estimated_time = substr($ticket['max_time'], 0, 5);
					$status = $ticket['status'];
					if($calendar_checkmark_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
						$checkmark_ticket = 'calendar-checkmark-ticket';
					} else {
						$checkmark_ticket = '';
					}
					$status_class = $status;
					$rows = 1;
					$ticket_styling = '';
					$calendar_color = mysqli_fetch_array(mysqli_query($dbc, "SELECT `calendar_color` FROM `contacts` WHERE `contactid` = '".$contact_id."'"))['calendar_color'];
					if($calendar_ticket_color_code_tabs == 1 && !empty($ticket_tabs_color[$ticket['ticket_type']])) {
						$ticket_styling = ' background-color:'.$ticket_tabs_color[$ticket['ticket_type']].';';
					} else if($calendar_highlight_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
						$ticket_styling = ' background-color:'.$calendar_completed_color[$status].';';
					} else if($calendar_highlight_incomplete_tickets == 1 && in_array($status, $calendar_incomplete_status)) {
						$ticket_styling = ' background-color:'.$calendar_incomplete_color[$status].';';
					} else if (!empty($calendar_color)) {
						$ticket_styling = ' background-color:'.$calendar_color.';';
					}
					if ($status == 'Internal QA') {
						if (!empty($ticket['internal_qa_start_time'])) {
							$current_start_time = date('h:i a', strtotime($ticket['internal_qa_start_time']));
							if (!empty($ticket['internal_qa_end_time'])) {
								$duration = (strtotime($ticket['internal_qa_end_time']) - strtotime($current_start_time));
								$current_end_time = date('h:i a', strtotime($ticket['internal_qa_end_time']));
							} else {
								$max_time = explode(':',$ticket['max_qa_time']);
								$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
								$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
							}
						} else {
							$current_start_time = date('h:i a', strtotime($day_start) + ($calendar_row * $day_period * 60));
							$max_time = explode(':',$ticket['max_qa_time']);
							$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
							$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
						}
					} else if ($status == 'Customer QA') {
						if (!empty($ticket['deliverable_start_time'])) {
							$current_start_time = date('h:i a', strtotime($ticket['deliverable_start_time']));
							if (!empty($ticket['deliverable_end_time'])) {
								$duration = (strtotime($ticket['deliverable_end_time']) - strtotime($current_start_time));
								$current_end_time = date('h:i a', strtotime($ticket['deliverable_end_time']));
							} else {
								$max_time = explode(':',$ticket['max_qa_time']);
								$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
								$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
							}
						} else {
							$current_start_time = date('h:i a', strtotime($day_start) + ($calendar_row * $day_period * 60));
							$max_time = explode(':',$ticket['max_qa_time']);
							$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
							$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
						}
					} else {
						if (!empty($ticket['to_do_start_time'])) {
							$current_start_time = date('h:i a', strtotime($ticket['to_do_start_time']));
							if (!empty($ticket['to_do_end_time'])) {
								$duration = (strtotime($ticket['to_do_end_time']) - strtotime($current_start_time));
								$current_end_time = date('h:i a', strtotime($ticket['to_do_end_time']));
							} else {
								$max_time = explode(':',$ticket['max_time']);
								$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
								$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
							}
						} else {
							$current_start_time = date('h:i a', strtotime($day_start) + ($calendar_row * $day_period * 60));
							$max_time = explode(':',$ticket['max_time']);
							$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
							$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
						}
					}
					if ($calendar_col[$calendar_row][2] == 'all_day_ticket') {
						$current_start_time = date('h:i a', strtotime($day_start) + ($calendar_row * $day_period * 60));
						$day_of_week = date('l', $current_day);
						$staff_shift = checkShiftIntervals($dbc, $contact_id, $day_of_week, $current_day)[0];
						if ($ticket['to_do_end_date'] == $current_day && !empty($ticket['to_do_end_time'])) { 
							$current_end_time = date('h:i a', strtotime($ticket['to_do_end_time']));
						} else if (!empty($staff_shift['endtime'])) {
							$current_end_time = date('h:i a', strtotime($staff_shift['endtime']));
						} else {
							$current_end_time = date('h:i a', strtotime($day_end));
						}
						$duration = strtotime($current_end_time) - strtotime($current_start_time);
					}
					if ($duration > $day_period * 60) {
						$rows = ceil($duration / ($day_period * 60));
					}
					if ($duration < $day_period * 60) {
						$duration = $day_period * 60;
					}
					$date_color = 'block/green.png';
					if($new_today_date < date('Y-m-d',strtotime("-2 days"))) {
						$date_color = 'block/red.png';
					}
					if($new_today_date == date('Y-m-d',strtotime("-1 days")) || $new_today_date == date('Y-m-d',strtotime("-2 days"))) {
						$date_color = 'block/orange.png';
					}
					$status_icon = get_ticket_status_icon($dbc, $ticket['status']);
				    if(!empty($status_icon)) {
				        $icon_img = '';
				    	$icon_background = '';
				    	if($calendar_ticket_status_icon == 'background' && $status_icon != 'initials') {
			    			$icon_background = " background-image: url(\"".$status_icon."\"); background-repeat: no-repeat; height: 100%; background-size: contain; background-position: center;";
				    	} else {
					    	if($status_icon == 'initials') {
								$icon_img = '<span class="id-circle-small pull-right" style="background-color: #6DCFF6; font-family: \'Open Sans\';">'.get_initials($ticket['status']).'</span>';
					    	} else {
						        $icon_img = '<img src="'.$status_icon.'" class="pull-right" style="max-height: 20px;">';
						    }
						}
				    } else {
				        $icon_img = '';
				    	$icon_background = '';
				    }
				    $recurring_icon = '';
				    if($ticket['is_recurrence'] == 1) {
				    	$recurring_icon = "<img src='".WEBSITE_URL."/img/icons/recurring.png' style='width: 1.2em; margin: 0.1em;' class='pull-right' title='Recurring ".TICKET_NOUN."'>";
				    }
					$row_html .= ($ticket_view_access == 1 ? "<a href='".WEBSITE_URL."/Ticket/index.php?edit=".$ticket['ticketid']."' onclick='overlayIFrameSlider(this.href+\"&calendar_view=true\"); return false;'>" : "")."<div class='used-block ".($locked_optimize ? 'no_change ' : '').$calendar_ticket."' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-duration='$duration' data-ticket='".$ticket['ticketid']."' data-clientid='".$ticket['clientid']."' data-businessid='".$businessid."' data-contactid='".$contactid."' data-internal_qa_contactid='".$internal_qa_contactid."' data-deliverable_contactid='".$deliverable_contactid."' data-status='".$status."' data-timestamp='".date('Y-m-d H:i:s')."' data-tickettype='".$ticket['ticket_type']."' ";
					$row_html .= "style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%;".$ticket_styling.$icon_background."'>";
					$row_html .= "<span class='$status_class' style='display: block; float: left; width: calc(100% - 2em);'>".$icon_img;
					$row_html .= '<img src="'.WEBSITE_URL.'/img/'.$date_color.'" style="width:1em;" border="0" alt=""> ';
					if($ticket_status_color_code == 1 && !empty($ticket_status_color[$status])) {
						$row_html .= '<div class="ticket-status-color" style="background-color: '.$ticket_status_color[$status].';"></div>';
					}
					$row_html .= $recurring_icon;
			        $row_html .= calendarTicketLabel($dbc, $ticket, $max_time, $current_start_time, $current_end_time);
					$row_html .= "</b></span><div class='drag-handle full-height' title='Drag Me!'><img class='black-color pull-right inline-img drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' title='Drag'></div></div>".($ticket_view_access == 1 ? "</a>" : "");
				}
			} else if($calendar_col[$calendar_row][0] == 'shift') {
				$rows = 1;
				$therapistsid = $calendar_col[$calendar_row][4];
				$patientid = '';
				$current_time = $calendar_col[$calendar_row][2];
				$current_date = $calendar_col[$calendar_row][3];
				$appoint_date = $calendar_col[$calendar_row][3].' '.date('h:i a', strtotime($calendar_col[$calendar_row][2]));
				$end_appoint_date = $calendar_col[$calendar_row][3].' '.date('h:i a', strtotime("+30 minutes", strtotime($calendar_col[$calendar_row][2])));

				$page_query['action'] = 'edit';
				$page_query['bookingid'] = 'NEW';
				$page_query['appoint_date'] = $appoint_date;
				$page_query['end_appoint_date'] = $end_appoint_date;	
				$page_query['patientid'] = $patientid;
				$page_query['therapistsid'] = $therapistsid;
				unset($page_query['add_reminder']);
				unset($page_query['unbooked']);
				unset($page_query['equipment_assignmentid']);
				unset($page_query['teamid']);
				$row_html .= ($edit_access == 1 ? "<a href='' onclick='universalAdd(this); return false;' class='shift' data-appturl='".WEBSITE_URL."/Calendar/booking.php?".http_build_query($page_query)."' data-ticketurl='".WEBSITE_URL."/Ticket/index.php?edit=0&calendar_view=true&new_ticket_calendar=true&current_time=".$current_time."&current_date=".$current_date."&calendar_contactid=".$contact_id."'>" : "");
				$row_html .= "<div class='resizable-shift' data-shifttype='universal' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-duration='$duration' style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%; opacity: 0;'>";
				$row_html .= "</div>".($edit_access == 1 ? "</a>" : "");
				$page_query['add_reminder'] = $_GET['add_reminder'];
				$page_query['unbooked'] = $_GET['booked'];
				$page_query['equipment_assignmentid'] = $_GET['equipment_assignmentid'];
				$page_query['teamid'] = $_GET['teamid'];
				unset($page_query['action']);
				unset($page_query['bookingid']);
				unset($page_query['appoint_date']);
				unset($page_query['end_appoint_date']);
				unset($page_query['therapistsid']);
				unset($page_query['patientid']);
			}
		} else if($calendar_col[$calendar_row][0] == 'workorder_equip') {
			$workorder = $calendar_col[$calendar_row][1];
			$region = $calendar_col[$calendar_row][2];
			$businessid = $calendar_col[$calendar_row][3];
			$assign_staff = $calendar_col[$calendar_row][4];
			$teamid = $calendar_col[$calendar_row][5];
			$rows = 2;
			$status_class = 'incomplete';
			$max_time = explode(':', $workorder['max_time']);
			$max_time_hour = $max_time[0];
			$max_time_minute = $max_time[1];
			$start_time = date('h:i a', strtotime($workorder['to_do_time']));
			$end_time = date('h:i a', strtotime('+'.$max_time_hour.' hours +'.$max_time_minute.' minutes', strtotime($start_time)));
			$rounded_starttime = strtotime($start_time) - (strtotime($start_time) % (60 * $day_period));
			$duration = strtotime($end_time) - $rounded_starttime;
			if ($duration > $day_period * 60) {
				$rows = ceil($duration / ($day_period * 60));
			}
			switch($workorder['status']) {
				case 'Done':
					$status_class = 'completed';
					break;
			}
			$row_html .= ($edit_access == 1 ? "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Work Order/edit_workorder.php?action=view&workorderid=".$workorder['workorderid']."\"); return false;'>" : "")."<div class='used-block' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-duration='$duration' data-workorder='".$workorder['workorderid']."' data-region='".$region."' data-businessid='".$businessid."' data-assignstaff='".$assign_staff."' data-teamid='".$teamid."' ";
			$row_html .= "style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%;'>";
			$row_html .= "<span class='$status_class' style='display: block; float: left; width: calc(100% - 2em);'>";
			$row_html .= "<b>Work Order #".$workorder['heading'].'<br />'.get_client($dbc,$workorder['businessid']).'<br />'.$start_time." - ".$end_time."</b></span><div class='drag-handle full-height' title='Drag Me!'><img class='drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' style='filter: brightness(200%); float: right; width: 2em;' title='Drag'></div></div>".($edit_access == 1 ? "</a>" : "");
		} else if ($calendar_col[$calendar_row][0] == 'ticket_equip' || $calendar_col[$calendar_row][0] == 'ticket_equip_combine') {
			if($calendar_col[$calendar_row][1] == 'warehouse' || $calendar_col[$calendar_row][1] == 'pickup') {
				$warehouse_label = $calendar_col[$calendar_row][1] == 'pickup' ? 'Pick Up' : 'Warehouse';
				$ticket = $calendar_col[$calendar_row][5];
				$warehouse_ticketids = $calendar_col[$calendar_row][4];
				$warehouse = $calendar_col[$calendar_row][2];
				$warehouse_count = $calendar_col[$calendar_row][3];
				$rows = 1;
				$row_html .= ($edit_access == 1 ? "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Calendar/view_warehouse_pickups.php?warehouse=".urlencode($warehouse)."&ticketids=".implode(',',$warehouse_ticketids)."\"); return false;'>" : "")."<div class='used-block' data-blocks='$rows' data-row='$calendar_row' data-duration='$duration' ";
				$row_html .= "style='";
				$delivery_color = get_delivery_color($dbc, $warehouse_label);
				if(!empty($delivery_color)) {
					$row_html .= "background-color:".$delivery_color.';';
				} else {
					if($ticket['region'] == '') {
						$ticket['region'] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '".$equipment_assignmentid."'"))['region'];
						if($ticket['region'] == '') {
							$ticket['region'] = explode('*#*', mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '".$ticket['equipmentid']."'"))['region'])[0];
						}
					}
					if($ticket['region'] != '') {
						foreach($region_list as $region_line => $region_name) {
							if($region_name == $ticket['region']) {
								$row_html .= "background-color:".$region_colours[$region_line].";";
							}
						}
					}
				}
				$row_html .= "height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%;'>";
				$row_html .= "<b>".$warehouse_label.": ".$warehouse." (".$warehouse_count." Pick Up".($warehouse_count > 1 ? 's': '').")</b>";
				$row_html .= "</div>".($edit_access == 1 ? "</a>" : "");
			} else if($calendar_col[$calendar_row][1] == 'SHIFT') {
				$rows = 1;
				$current_time = $calendar_col[$calendar_row][2];
				$current_date = $calendar_col[$calendar_row][3];
				$equipment = $calendar_col[$calendar_row][4];
				$equipment_assignment = $calendar_col[$calendar_row][5];

				$equipmentid = $equipment['equipmentid'];
				$equipment_assignmentid = $equipment_assignment['equipment_assignmentid'];
				$row_html .= ($edit_access == 1 ? "<a href='' data-equipmentid='".$equipmentid."' data-equipment_assignmentid='".$equipment_assignmentid."' data-currenttime='".$current_time."' data-currentdate='".$current_date."' onclick='dispatchNewWorkOrder(this); return false;' class='shift'>" : "");
				$row_html .= "<div class='resizable-shift' data-shifttype='ticket_equip' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%; opacity: 0;'>";
				$row_html .= "</div>".($edit_access == 1 ? "</a>" : "");
			} else if($calendar_col[$calendar_row][0] == 'ticket_equip_combine') {
				$row_htmls = [];
				$greatest_rows = 1;
				foreach($calendar_col[$calendar_row][1] as $row_ticket) {
					$cur_html = '';

					$ticket = $row_ticket[1];
					$region = $row_ticket[2];
					$businessid = $row_ticket[3];
					$assign_staff = $row_ticket[4];
					$teamid = $row_ticket[5];
					$equipment_assignmentid = $row_ticket[6];
					$block_type = $row_ticket[7];
					$ticket_table = $row_ticket[8];

					$rows = 1;
					$status = $ticket['status'];
					$status_class = 'incomplete';
					if($calendar_checkmark_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
						$checkmark_ticket = 'calendar-checkmark-ticket';
					} else {
						$checkmark_ticket = '';
					}
					$max_time = explode(':', $ticket['max_time']);
					$max_time_hour = $max_time[0];
					$max_time_minute = $max_time[1];
					$start_time = date('h:i a', strtotime($ticket['to_do_start_time']));
					if(!empty($ticket['to_do_end_time'])) {
						$end_time = date('h:i a', strtotime($ticket['to_do_end_time']));
					} else if (!empty($ticket['max_time']) && $ticket['max_time'] != '00:00:00') {
						$end_time = date('h:i a', strtotime('+'.$max_time_hour.' hours +'.$max_time_minute.' minutes', strtotime($start_time)));
					} else {
						$end_time = date('h:i a', strtotime('+'.($day_period * 2).' minutes', strtotime($start_time)));
					}
					$max_time = $ticket['max_time'];
					$rounded_starttime = strtotime($start_time) - (strtotime($start_time) % (60 * $day_period));
					$duration = strtotime($end_time) - $rounded_starttime;
					if ($duration > $day_period * 60) {
						$rows = ceil($duration / ($day_period * 60));
					}
					if($rows > $greatest_rows) {
						$greatest_rows = $rows;
					}
					if ($duration < $day_period * 60) {
						$duration = $day_period * 60;
					}
					$status_icon = get_ticket_status_icon($dbc, $ticket['status']);
				    if(!empty($status_icon)) {
				        $icon_img = '';
				    	$icon_background = '';
				    	if($calendar_ticket_status_icon == 'background' && $status_icon != 'initials') {
			    			$icon_background = " background-image: url(\"".$status_icon."\"); background-repeat: no-repeat; height: 100%; background-size: contain; background-position: center;";
				    	} else {
					    	if($status_icon == 'initials') {
								$icon_img = '<span class="id-circle-small pull-right" style="background-color: #6DCFF6; font-family: \'Open Sans\';">'.get_initials($ticket['status']).'</span>';
					    	} else {
						        $icon_img = '<img src="'.$status_icon.'" class="pull-right" style="max-height: 20px;">';
						    }
						}
				    } else {
				        $icon_img = '';
				    	$icon_background = '';
				    }
				    $cur_html .= ($ticket_view_access == 1 ? "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Ticket/index.php?calendar_view=true&edit=".$ticket['ticketid']."&stop=".$ticket['stop_id']."\"); return false;'>" : "")."<div class='combined_block ".$checkmark_ticket."' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-duration='$duration' data-ticket='".$ticket['ticketid']."' data-region='".$region."' data-clientid='".$ticket['clientid']."' data-businessid='".$businessid."' data-assignstaff='".$assign_staff."' data-teamid='".$teamid."' data-status='".$ticket['status']."' data-equipassign='".$equipment_assignmentid."' data-blocktype='".$block_type."' data-tickettable='".$ticket_table."' data-ticketscheduleid='".$ticket['ticket_scheduleid']."' data-timestamp='".date('Y-m-d H:i:s')."' data-tickettype='".$ticket['ticket_type']."' ";
				    $cur_html .= "style='border-bottom: 1px solid rgb(221,221,221);";
					$delivery_color = get_delivery_color($dbc, $ticket['delivery_type']);
					if($calendar_ticket_color_code_tabs == 1 && !empty($ticket_tabs_color[$ticket['ticket_type']])) {
						$cur_html .= ' background-color:'.$ticket_tabs_color[$ticket['ticket_type']].';';
					} else if($calendar_highlight_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
						$cur_html .= 'background-color:'.$calendar_completed_color[$status].';';
					} else if($calendar_highlight_incomplete_tickets == 1 && in_array($status, $calendar_incomplete_status)) {
						$cur_html .= 'background-color:'.$calendar_incomplete_color[$status].';';
					} else if(!empty($delivery_color)) {
						$cur_html .= "background-color:".$delivery_color.';';
					} else {
						if($ticket['region'] == '') {
							$ticket['region'] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '".$equipment_assignmentid."'"))['region'];
							if($ticket['region'] == '') {
								$ticket['region'] = explode('*#*', mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '".$ticket['equipmentid']."'"))['region'])[0];
							}
						}
						if($ticket['region'] != '') {
							foreach($region_list as $region_line => $region_name) {
								if($region_name == $ticket['region']) {
									$cur_html .= "background-color:".$region_colours[$region_line].";";
								}
							}
						}
					}
					$cur_html .= $icon_background;
					$cur_html .= " margin: 0; padding: 0.2em; width: 100%; height: 100%;'>";
					$cur_html .= "<div class='$status_class' style='display: block; float: left; width: calc(100% - 2em);'>".$icon_img;
					if($ticket_status_color_code == 1 && !empty($ticket_status_color[$status])) {
						$cur_html .= '<div class="ticket-status-color" style="background-color: '.$ticket_status_color[$status].';"></div>';
					}
					$cur_html .= calendarTicketLabel($dbc, $ticket, $max_time, $start_time, $end_time);
					$cur_html .= "</b></div>";
					$cur_html .= "<div class='clearfix'></div></div>".($ticket_view_access == 1 ? "</a>" : "");
					$row_htmls[] = $cur_html;
				}

				$row_html .= "<div class='used-block combined_blocks' data-contact='$contact_id' data-blocks='$greatest_rows' data-row='$calendar_row' data-duration='$duration' data-ticketcombined='".implode('#*#',$all_ticketids)."' data-blocktype='".$block_type."' data-timestamp='".date('Y-m-d H:i:s')."' ";
				$row_html .= "style='";
				$row_html .= "height: calc(".$greatest_rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0; position: absolute; width: 100%;'><span style='display: block; float: left; width: calc(100% - 2em);'>";
				$row_html .= implode('<div class="clearfix"></div>',$row_htmls);
				$row_html .= "</span>";
				$row_html .= "<div class='drag-handle full-height' title='Drag Me!'><img class='black-color pull-right inline-img drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' title='Drag'>";
				if($drag_multiple == 1) {
					$row_html .= "<br /><span style='position: relative; left: 4px;'><input type='checkbox' name='multi_book' data-contact='".$contact_id."' data-date='".$calendar_date."' style='width: 1.5em; height: 1.5em;' title='Check me took book multiple ".TICKET_TILE."' class='no-slider'></span>";
				}
				$row_html .= "</div>";

				$row_html .= "</div>";
			} else if (!empty($calendar_col[$calendar_row][1])) {
				$ticket = $calendar_col[$calendar_row][1];
				$region = $calendar_col[$calendar_row][2];
				$businessid = $calendar_col[$calendar_row][3];
				$assign_staff = $calendar_col[$calendar_row][4];
				$teamid = $calendar_col[$calendar_row][5];
				$equipment_assignmentid = $calendar_col[$calendar_row][6];
				$block_type = $calendar_col[$calendar_row][7];
				$ticket_table = $calendar_col[$calendar_row][8];
				$rows = 1;
				$status = $ticket['status'];
				$status_class = 'incomplete';
				if($calendar_checkmark_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
					$checkmark_ticket = 'calendar-checkmark-ticket';
				} else {
					$checkmark_ticket = '';
				}
				$max_time = explode(':', $ticket['max_time']);
				$max_time_hour = $max_time[0];
				$max_time_minute = $max_time[1];
				$start_time = date('h:i a', strtotime($ticket['to_do_start_time']));
				if(!empty($ticket['to_do_end_time'])) {
					$end_time = date('h:i a', strtotime($ticket['to_do_end_time']));
				} else if (!empty($ticket['max_time']) && $ticket['max_time'] != '00:00:00') {
					$end_time = date('h:i a', strtotime('+'.$max_time_hour.' hours +'.$max_time_minute.' minutes', strtotime($start_time)));
				} else {
					$end_time = date('h:i a', strtotime('+'.($day_period * 2).' minutes', strtotime($start_time)));
				}
				$max_time = $ticket['max_time'];
				$rounded_starttime = strtotime($start_time) - (strtotime($start_time) % (60 * $day_period));
				$duration = strtotime($end_time) - $rounded_starttime;
				if ($duration > $day_period * 60) {
					$rows = ceil($duration / ($day_period * 60));
				}
				if ($duration < $day_period * 60) {
					$duration = $day_period * 60;
				}
				switch($workorder['status']) {
					case 'Done':
						$status_class = 'completed';
						break;
				}
				$status_icon = get_ticket_status_icon($dbc, $ticket['status']);
			    if(!empty($status_icon)) {
			        $icon_img = '';
			    	$icon_background = '';
			    	if($calendar_ticket_status_icon == 'background' && $status_icon != 'initials') {
		    			$icon_background = " background-image: url(\"".$status_icon."\"); background-repeat: no-repeat; height: 100%; background-size: contain; background-position: center;";
			    	} else {
				    	if($status_icon == 'initials') {
							$icon_img = '<span class="id-circle-small pull-right" style="background-color: #6DCFF6; font-family: \'Open Sans\';">'.get_initials($ticket['status']).'</span>';
				    	} else {
					        $icon_img = '<img src="'.$status_icon.'" class="pull-right" style="max-height: 20px;">';
					    }
					}
			    } else {
			        $icon_img = '';
			    	$icon_background = '';
			    }
				$row_html .= ($ticket_view_access == 1 ? "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Ticket/index.php?calendar_view=true&edit=".$ticket['ticketid']."&stop=".$ticket['stop_id']."\"); return false;'>" : "")."<div class='used-block ".$checkmark_ticket."' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-duration='$duration' data-ticket='".$ticket['ticketid']."' data-region='".$region."' data-clientid='".$ticket['clientid']."' data-businessid='".$businessid."' data-assignstaff='".$assign_staff."' data-teamid='".$teamid."' data-status='".$ticket['status']."' data-equipassign='".$equipment_assignmentid."' data-blocktype='".$block_type."' data-tickettable='".$ticket_table."' data-ticketscheduleid='".$ticket['ticket_scheduleid']."' data-timestamp='".date('Y-m-d H:i:s')."' data-tickettype='".$ticket['ticket_type']."' ";
				$row_html .= "style='";
				$delivery_color = get_delivery_color($dbc, $ticket['delivery_type']);
				if($calendar_ticket_color_code_tabs == 1 && !empty($ticket_tabs_color[$ticket['ticket_type']])) {
					$row_html .= ' background-color:'.$ticket_tabs_color[$ticket['ticket_type']].';';
				} else if($calendar_highlight_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
					$row_html .= 'background-color:'.$calendar_completed_color[$status].';';
				} else if($calendar_highlight_incomplete_tickets == 1 && in_array($status, $calendar_incomplete_status)) {
					$row_html .= 'background-color:'.$calendar_incomplete_color[$status].';';
				} else if(!empty($delivery_color)) {
					$row_html .= "background-color:".$delivery_color.';';
				} else {
					if($ticket['region'] == '') {
						$ticket['region'] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '".$equipment_assignmentid."'"))['region'];
						if($ticket['region'] == '') {
							$ticket['region'] = explode('*#*', mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '".$ticket['equipmentid']."'"))['region'])[0];
						}
					}
					if($ticket['region'] != '') {
						foreach($region_list as $region_line => $region_name) {
							if($region_name == $ticket['region']) {
								$row_html .= "background-color:".$region_colours[$region_line].";";
							}
						}
					}
				}
				$row_html .= $icon_background;
				$row_html .= "height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%;'>";
				$row_html .= "<span class='$status_class' style='display: block; float: left; width: calc(100% - 2em);'>".$icon_img;
				if($ticket_status_color_code == 1 && !empty($ticket_status_color[$status])) {
					$row_html .= '<div class="ticket-status-color" style="background-color: '.$ticket_status_color[$status].';"></div>';
				}
				$row_html .= calendarTicketLabel($dbc, $ticket, $max_time, $start_time, $end_time);
				$row_html .= "</b></span>";
				if($ticket['scheduled_lock'] > 0) {
					$row_html .= "<div class='drag-handle full-height' title='Time is locked for this ".TICKET_NOUN."' onclick='changeScheduledTime(this);'><img class='black-color pull-right inline-img no-slider' src='../img/icons/lock.png'></div>";
				} else {
					$row_html .= "<div class='drag-handle full-height' title='Drag Me!'><img class='black-color pull-right inline-img drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' title='Drag'>";
					if($drag_multiple == 1) {
						$row_html .= "<br /><span style='position: relative; left: 4px;'><input type='checkbox' name='multi_book' data-contact='".$contact_id."' data-date='".$calendar_date."' style='width: 1.5em; height: 1.5em;' title='Check me took book multiple ".TICKET_TILE."' class='no-slider'></span>";
					}
					$row_html .= "</div>";
				}
				$row_html .= "</div>".($ticket_view_access == 1 ? "</a>" : "");
			}
		} else if ($calendar_col[$calendar_row][0] == 'shift' || $calendar_col[$calendar_row][0] == 'no_shift') {
			if ($calendar_col[$calendar_row][0] == 'no_shift') {
				$rows = 1;
				if($_GET['mode'] == 'client') {
					$shift_clientid = $contact_id;
				} else {
					$shift_staffid = $contact_id;
				}
				$shift_startdate = $current_day;
				$shift_enddate = $current_day;
				$shift_starttime = date('h:i a', strtotime($calendar_col[$calendar_row][1]));
				$shift_endtime = date('h:i a', strtotime('+'.$day_period.' minutes', strtotime($calendar_col[$calendar_row][1])));

				$page_query['shiftid'] = 'NEW';
				$page_query['shift_startdate'] = $shift_startdate;
				$page_query['shift_enddate'] = $shift_enddate;	
				$page_query['shift_starttime'] = $shift_starttime;
				$page_query['shift_endtime'] = $shift_endtime;
				$page_query['shift_staffid'] = $shift_staffid;
				$page_query['shift_clientid'] = $shift_clientid;
				unset($page_query['add_reminder']);
				unset($page_query['unbooked']);
				unset($page_query['equipment_assignmentid']);
				unset($page_query['teamid']);
				$row_html .= ($edit_access == 1 ? "<a href='".WEBSITE_URL."/Calendar/shifts.php?".http_build_query($page_query)."' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Calendar/shifts.php?".http_build_query($page_query)."\"); return false;' class='shift'>" : "");
				$row_html .= "<div class='resizable-shift' data-shifttype='shift' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-duration='$duration' style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%; opacity: 0;'>";
				$row_html .= "</div>".($edit_access == 1 ? "</a>" : "");
				$page_query['shiftid'] = $_GET['shiftid'];
				$page_query['add_reminder'] = $_GET['add_reminder'];
				$page_query['unbooked'] = $_GET['booked'];
				$page_query['equipment_assignmentid'] = $_GET['equipment_assignmentid'];
				$page_query['teamid'] = $_GET['teamid'];
				unset($page_query['shift_startdate']);
				unset($page_query['shift_enddate']);
				unset($page_query['shift_starttime']);
				unset($page_query['shift_endtime']);
				unset($page_query['shift_staffid']);
				unset($page_query['shift_clientid']);
			} else {
				$shift = $calendar_col[$calendar_row][1];
				$dayoff = $calendar_col[$calendar_row][2];
				$has_conflict = $calendar_col[$calendar_row][3];
				$recurring = date('Y-m-d', strtotime($shift['startdate'])) == date('Y-m-d', strtotime($shift['enddate'])) ? 'no' : 'yes';
				$rows = 1;
				$shift_styling = '';
				$calendar_color = '';
				if($shift_client_color == 1 && !empty($shift['clientid'])) {
					$calendar_color = mysqli_fetch_array(mysqli_query($dbc, "SELECT `calendar_color` FROM `contacts` WHERE `contactid` = '".$shift['clientid']."'"))['calendar_color'];
				}
				if(empty($calendar_color)) {
					$calendar_color = mysqli_fetch_array(mysqli_query($dbc, "SELECT `calendar_color` FROM `contacts` WHERE `contactid` = '".$shift['contactid']."'"))['calendar_color'];
				}
				if (!empty($calendar_color)) {
					$shift_styling = ' background-color:'.$calendar_color.';';
				}
				$shift_fields = explode(',',mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_shifts`"))['enabled_fields']);
				if(in_array('conflicts_highlight', $shift_fields) && $has_conflict) {
					$shift_styling = ' background-color:#f00;';
				}
				$warning_icon = '';
				if(in_array('conflicts_warning', $shift_fields) && $has_conflict) {
					$warning_icon = '<img title="This shift has a conflict with another shift." src="'.WEBSITE_URL.'/img/icons/yellow-warning.png" class="pull-right" style="max-height: 20px;">';
				}
				if (!empty($shift)) {
					$block_starttime = strtotime($day_start) > strtotime($shift['starttime']) ? strtotime($day_start) : strtotime($shift['starttime']);
					$block_endtime = strtotime($day_end) < strtotime($shift['endtime']) ? strtotime($day_end) : strtotime($shift['endtime']);
					$rounded_starttime = $block_starttime - ($block_starttime % (60 * $day_period));
					$duration = ($block_endtime - $rounded_starttime);
					if ($duration > $day_period * 60) {
						$rows = ceil($duration / ($day_period * 60));
					}
					$page_query['shiftid'] = $shift['shiftid'];
					$page_query['current_day'] = $current_day;

					$echo_url = "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Calendar/shifts.php?".http_build_query($page_query)."\"); return false;'>";

					$row_html .= ((empty($lock_date) || $shift['startdate'] >= $lock_date) && $edit_access == 1 ? $echo_url : "")."<div class='used-block ".($shift['startdate'] < $lock_date && !empty($lock_date) ? 'no_change' : '')."' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-shift='".$shift['shiftid']."' data-recurring='$recurring' data-currentdate='$current_day' ";
					$row_html .= "data-duration='$duration' style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%;".$shift_styling."'>";
					$row_html .= "<span class='shift' style='display: block; float: left; width: calc(100% - 2em);'>".$warning_icon;
					if(!empty($shift['heading'])) {
						$row_html .= "<b>".$shift['heading']."</b>".'<br />';
					}
					$row_html .= "<b>".date('g:i a', strtotime($shift['starttime']))." - ".date('g:i a', strtotime($shift['endtime']))."</b>".'<br />';
					if($_GET['mode'] == 'client') {
						$row_html .= '<b>'.get_contact($dbc, $shift['contactid']).'</b><br />';
					} else if(!empty($shift['clientid'])) {
						$row_html .= '<b>'.get_contact($dbc, $shift['clientid']).'</b><br />';
					}
					if(!empty($shift['notes'])) {
						$row_html .= '<b>Notes: '.html_entity_decode($shift['notes']).'</b ><br />';
					}
					$row_html .= "</span>";
					if($shift['startdate'] < $lock_date && !empty($lock_date)) {
						$row_html .= "<div class='drag-handle full-height' title='Date is passed the Lock Date.'><img class='black-color pull-right inline-img' src='".WEBSITE_URL."/img/icons/lock.png'></div>";
					} else {
						$row_html .= "<div class='drag-handle full-height' title='Drag Me!'><img class='drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' style='filter: brightness(200%); float: right; width: 2em;' title='Drag'></div>";
					}
					$row_html .= "</div>".($edit_access == 1 ? "</a>" : "");
					unset($page_query['shiftid']);
				}
				
				if (!empty($dayoff)) {
					$rounded_starttime = strtotime($dayoff['starttime']) - (strtotime($dayoff['starttime']) % (60 * $day_period));
					$duration = (strtotime($dayoff['endtime']) - $rounded_starttime);
					if ($duration > $day_period * 60) {
						$rows = ceil($duration / ($day_period * 60));
					}
					$page_query['shiftid'] = $dayoff['shiftid'];

					$echo_url = "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Calendar/shifts.php?".http_build_query($page_query)."\"); return false;'>";

					$row_html .= ((empty($lock_date) || $dayoff['startdate'] >= $lock_date) && $edit_access == 1 ? $echo_url : "")."<div class='used-block ".($dayoff['startdate'] < $lock_date && !empty($lock_date) ? 'no_change' : '')."' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-shift='".$dayoff['shiftid']."' data-recurring='$recurring' data-currentdate='$calendar_row' ";
					$row_html .= "data-duration='$duration' style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%; background-color: #aaa;'>";
					$row_html .= "<span class='dayoff' style='display: block; float: left; width: calc(100% - 2em);'>";
					if(!empty($dayoff['heading'])) {
						$row_html .= "<b>".$shift['heading']."</b>".'<br />';
					}
					$row_html .= "<b>".date('g:i a', strtotime($dayoff['starttime']))." - ".date('g:i a', strtotime($dayoff['endtime']))."</b>".'<br />';
					$row_html .= $dayoff['dayoff_type'];
					$row_html .= "</span>";

					if($dayoff['startdate'] < $lock_date && !empty($lock_date)) {
						$row_html .= "<div class='drag-handle full-height' title='Date is passed the Lock Date.'><img class='black-color pull-right inline-img' src='".WEBSITE_URL."/img/icons/lock.png'></div>";
					} else {
						$row_html .= "<div class='drag-handle full-height' title='Drag Me!'><img class='drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' style='filter: brightness(200%); float: right; width: 2em;' title='Drag'></div>";
					}
					$row_html .= "</div>".($edit_access == 1 ? "</a>" : "");
					unset($page_query['shiftid']);
				}
			}
		} else if ($calendar_col[$calendar_row][0] == 'ticket') {
			$ticket = $calendar_col[$calendar_row][1];
			if ($ticket == 'SHIFT') {
				$rows = 1;
				$staffid = $calendar_col[$calendar_row][4];
				$startdate = $calendar_col[$calendar_row][3];
				$enddate = $calendar_col[$calendar_row][3];
				$starttime = date('h:i a', strtotime($calendar_col[$calendar_row][2]));
				$endtime = date('h:i a', strtotime("+30 minutes", strtotime($calendar_col[$calendar_row][2])));
				$row_html .= ($edit_access == 1 ? "<a onclick='overlayIFrameSlider($(this).data(\"ticketurl\")); return false;' data-ticketurl='".WEBSITE_URL."/Ticket/index.php?edit=0&calendar_view=true&new_ticket_calendar=true&current_time=".$starttime."&current_date=".$startdate."&calendar_contactid=".$staffid."&end_time=".$endtime."' class='shift'>" : '');
				$row_html .= "<div class='resizable-shift' data-shifttype='ticket' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%; opacity: 0;'>";
				$row_html .= "</div>".($edit_access == 1 ? "</a>" : "");
			} else if (!empty($ticket)) {
				$businessid = $ticket['businessid'];
				$clients = [];
				foreach(array_filter(explode(',',$ticket['clientid'])) as $clientid) {
					$client = !empty(get_client($dbc, $clientid)) ? get_client($dbc, $clientid) : get_contact($dbc, $clientid);
					if(!empty($client) && $client != '-') {
						$clients[] = $client;
					}
				}
				$clients = implode(', ',$clients);
				$contactid = $ticket['contactid'];
				$internal_qa_contactid = $ticket['internal_qa_contactid'];
				$deliverable_contactid = $ticket['deliverable_contactid'];
				$heading = $ticket['heading'].$ticket['location_description'];
				$estimated_time = substr($ticket['max_time'], 0, 5);
				$status = $ticket['status'];
				if($calendar_checkmark_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
					$checkmark_ticket = 'calendar-checkmark-ticket';
				} else {
					$checkmark_ticket = '';
				}
				$status_class = $status;
				$calendar_color = mysqli_fetch_array(mysqli_query($dbc, "SELECT `calendar_color` FROM `contacts` WHERE `contactid` = '".$contact_id."'"))['calendar_color'];
				if($calendar_ticket_color_code_tabs == 1 && !empty($ticket_tabs_color[$ticket['ticket_type']])) {
					$ticket_styling = ' background-color:'.$ticket_tabs_color[$ticket['ticket_type']].';';
				} else if($calendar_highlight_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
					$ticket_styling = ' background-color:'.$calendar_completed_color[$status].';';
				} else if($calendar_highlight_incomplete_tickets == 1 && in_array($status, $calendar_incomplete_status)) {
					$ticket_styling = ' background-color:'.$calendar_incomplete_color[$status].';';
				} else if (!empty($calendar_color)) {
					$ticket_styling = ' background-color:'.$calendar_color.';';
				}
				$rows = 1;
				if ($status == 'Internal QA') {
					if (!empty($ticket['internal_qa_start_time'])) {
						$current_start_time = date('h:i a', strtotime($ticket['internal_qa_start_time']));
						if (!empty($ticket['internal_qa_end_time'])) {
							$duration = (strtotime($ticket['internal_qa_end_time']) - strtotime($current_start_time));
							$current_end_time = date('h:i a', strtotime($ticket['internal_qa_end_time']));
						} else {
							$max_time = explode(':',$ticket['max_qa_time']);
							$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
							$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
						}
					} else {
						$current_start_time = date('h:i a', strtotime($day_start) + ($calendar_row * $day_period * 60));
						$max_time = explode(':',$ticket['max_qa_time']);
						$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
						$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
					}
				} else if ($status == 'Customer QA') {
					if (!empty($ticket['deliverable_start_time'])) {
						$current_start_time = date('h:i a', strtotime($ticket['deliverable_start_time']));
						if (!empty($ticket['deliverable_end_time'])) {
							$duration = (strtotime($ticket['deliverable_end_time']) - strtotime($current_start_time));
							$current_end_time = date('h:i a', strtotime($ticket['deliverable_end_time']));
						} else {
							$max_time = explode(':',$ticket['max_qa_time']);
							$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
							$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
						}
					} else {
						$current_start_time = date('h:i a', strtotime($day_start) + ($calendar_row * $day_period * 60));
						$max_time = explode(':',$ticket['max_qa_time']);
						$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
						$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
					}
				} else {
					if (!empty($ticket['to_do_start_time'])) {
						$current_start_time = date('h:i a', strtotime($ticket['to_do_start_time']));
						if (!empty($ticket['to_do_end_time'])) {
							$duration = (strtotime($ticket['to_do_end_time']) - strtotime($current_start_time));
							$current_end_time = date('h:i a', strtotime($ticket['to_do_end_time']));
						} else {
							$max_time = explode(':',$ticket['max_time']);
							$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
							$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
						}
					} else {
						$current_start_time = date('h:i a', strtotime($day_start) + ($calendar_row * $day_period * 60));
						$max_time = explode(':',$ticket['max_time']);
						$duration = ($max_time[0] * 3600) + ($max_time[1] * 60);
						$current_end_time = date('h:i a', strtotime($current_start_time) + $duration);
					}
				}
				if ($calendar_col[$calendar_row][2] == 'all_day_ticket') {
					$current_start_time = date('h:i a', strtotime($day_start) + ($calendar_row * $day_period * 60));
					$day_of_week = date('l', $current_day);
					$staff_shift = checkShiftIntervals($dbc, $contact_id, $day_of_week, $current_day)[0];
					if ($ticket['to_do_end_date'] == $current_day && !empty($ticket['to_do_end_time'])) { 
						$current_end_time = date('h:i a', strtotime($ticket['to_do_end_time']));
					} else if (!empty($staff_shift['endtime'])) {
						$current_end_time = date('h:i a', strtotime($staff_shift['endtime']));
					} else {
						$current_end_time = date('h:i a', strtotime($day_end));
					}
					$duration = strtotime($current_end_time) - strtotime($current_start_time);
				}
				if ($duration > $day_period * 60) {
					$rows = ceil($duration / ($day_period * 60));
				}
				if ($duration < $day_period * 60) {
					$duration = $day_period * 60;
				}
				$date_color = 'block/green.png';
				if($new_today_date < date('Y-m-d',strtotime("-2 days"))) {
					$date_color = 'block/red.png';
				}
				if($new_today_date == date('Y-m-d',strtotime("-1 days")) || $new_today_date == date('Y-m-d',strtotime("-2 days"))) {
					$date_color = 'block/orange.png';
				}
				$status_icon = get_ticket_status_icon($dbc, $ticket['status']);
			    if(!empty($status_icon)) {
			        $icon_img = '';
			    	$icon_background = '';
			    	if($calendar_ticket_status_icon == 'background' && $status_icon != 'initials') {
		    			$icon_background = " background-image: url(\"".$status_icon."\"); background-repeat: no-repeat; height: 100%; background-size: contain; background-position: center;";
			    	} else {
				    	if($status_icon == 'initials') {
							$icon_img = '<span class="id-circle-small pull-right" style="background-color: #6DCFF6; font-family: \'Open Sans\';">'.get_initials($ticket['status']).'</span>';
				    	} else {
					        $icon_img = '<img src="'.$status_icon.'" class="pull-right" style="max-height: 20px;">';
					    }
					}
			    } else {
			        $icon_img = '';
			    	$icon_background = '';
			    }
			    $recurring_icon = '';
			    if($ticket['is_recurrence'] == 1) {
			    	$recurring_icon = "<img src='".WEBSITE_URL."/img/icons/recurring.png' style='width: 1.2em; margin: 0.1em;' class='pull-right' title='Recurring ".TICKET_NOUN."'>";
			    }
				$row_html .= ($ticket_view_access == 1 ? "<a href='".WEBSITE_URL."/Ticket/index.php?edit=".$ticket['ticketid']."' onclick='overlayIFrameSlider(this.href+\"&calendar_view=true\"); return false;'>" : "")."<div class='used-block ".($locked_optimize ? 'no_change' : '')."' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-duration='$duration' data-ticket='".$ticket['ticketid']."'' data-clientid='".$ticket['clientid']."' data-businessid='".$businessid."' data-contactid='".$contactid."' data-internal_qa_contactid='".$internal_qa_contactid."' data-deliverable_contactid='".$deliverable_contactid."' data-status='".$status."' data-timestamp='".date('Y-m-d H:i:s')."' data-tickettype='".$ticket['ticket_type']."' ";
				$row_html .= "style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%;".$ticket_styling.$icon_background."'>";
				$row_html .= "<span class='$status_class' style='display: block; float: left; width: calc(100% - 2em);'>".$icon_img;
				$row_html .= '<img src="'.WEBSITE_URL.'/img/'.$date_color.'" style="width:1em;" border="0" alt=""> ';
				if($ticket_status_color_code == 1 && !empty($ticket_status_color[$status])) {
					$row_html .= '<div class="ticket-status-color" style="background-color: '.$ticket_status_color[$status].';"></div>';
				}
				$row_html .= $recurring_icon;
		        $row_html .= calendarTicketLabel($dbc, $ticket, $max_time, $current_start_time, $current_end_time);
				$row_html .= "</b></span><div class='drag-handle full-height' title='Drag Me!'><img class='black-color pull-right inline-img drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' title='Drag'></div></div>".($ticket_view_access == 1 ? "</a>" : "");
			}
		} else if ($calendar_col[$calendar_row][0] == 'workorder') {
			$workorder = $calendar_col[$calendar_row][1];
			$region = $workorder['region'];
			$teamid = $workorder['assign_teamid'];
			$assign_staff = $workorder['contactid'];
			$businessid = $workorder['businessid'];
			$rows = 2;
			$status_class = 'incomplete';
			$max_time = explode(':', $workorder['max_time']);
			$max_time_hour = $max_time[0];
			$max_time_minute = $max_time[1];
			$start_time = date('h:i a', strtotime($workorder['to_do_time']));
			$end_time = date('h:i a', strtotime('+'.$max_time_hour.' hours +'.$max_time_minute.' minutes', strtotime($start_time)));
			$rounded_starttime = strtotime($start_time) - (strtotime($start_time) % (60 * $day_period));
			$duration = strtotime($end_time) - $rounded_starttime;
			if ($duration > $day_period * 60) {
				$rows = ceil($duration / ($day_period * 60));
			}
			switch($workorder['status']) {
				case 'Done':
					$status_class = 'completed';
					break;
			}
			$row_html .= ($edit_access == 1 ? "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Work Order/edit_workorder.php?action=view&workorderid=".$workorder['workorderid']."\"); return false;'>" : "")."<div class='used-block' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-duration='$duration' data-workorder='".$workorder['workorderid']."' data-region='".$region."' data-businessid='".$businessid."' data-assignstaff='".$assign_staff."' data-teamid='".$teamid."' ";
			$row_html .= "style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%;'>";
			$row_html .= "<span class='$status_class' style='display: block; float: left; width: calc(100% - 2em);'>";
			$row_html .= "<b>Work Order #".$workorder['heading'].'<br />'.get_client($dbc,$workorder['businessid']).'<br />'.$start_time." - ".$end_time."</b></span><div class='drag-handle full-height' title='Drag Me!'><img class='drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' style='filter: brightness(200%); float: right; width: 2em;' title='Drag'></div></div>".($edit_access == 1 ? "</a>" : "");
		} else if ($calendar_col[$calendar_row][0] == 'ticket_event') {
			$ticket = $calendar_col[$calendar_row][1];
			$project_name = $calendar_col[$calendar_row][2];
			$status = $ticket['status'];
			if($calendar_checkmark_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
				$checkmark_ticket = 'calendar-checkmark-ticket';
			} else {
				$checkmark_ticket = '';
			}
			if($calendar_ticket_color_code_tabs == 1 && !empty($ticket_tabs_color[$ticket['ticket_type']])) {
				$ticket_styling = ' background-color:'.$ticket_tabs_color[$ticket['ticket_type']].';';
			} else if($calendar_highlight_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
				$ticket_styling = ' background-color:'.$calendar_completed_color[$status].';';
			} else if($calendar_highlight_incomplete_tickets == 1 && in_array($status, $calendar_incomplete_status)) {
				$ticket_styling = 'background-color:'.$calendar_incomplete_color[$status].';';
			} else {
				$ticket_styling = '';
			}
			$start_time = date('h:i a', strtotime($ticket['member_start_time']));
			$end_time = date('h:i a', strtotime($ticket['member_end_time']));
			$rounded_starttime = strtotime($start_time) - (strtotime($start_time) % (60 * $day_period));
			$rows = 2;
			$duration = strtotime($end_time) - $rounded_starttime;
			if ($duration > $day_period * 60) {
				$rows = ceil($duration / ($day_period * 60));
			}
			$status_icon = get_ticket_status_icon($dbc, $ticket['status']);
		    if(!empty($status_icon)) {
		        $icon_img = '';
		    	$icon_background = '';
		    	if($calendar_ticket_status_icon == 'background' && $status_icon != 'initials') {
	    			$icon_background = " background-image: url(\"".$status_icon."\"); background-repeat: no-repeat; height: 100%; background-size: contain; background-position: center;";
		    	} else {
			    	if($status_icon == 'initials') {
						$icon_img = '<span class="id-circle-small pull-right" style="background-color: #6DCFF6; font-family: \'Open Sans\';">'.get_initials($ticket['status']).'</span>';
			    	} else {
				        $icon_img = '<img src="'.$status_icon.'" class="pull-right" style="max-height: 20px;">';
				    }
				}
		    } else {
		        $icon_img = '';
		    	$icon_background = '';
		    }
			$row_html .= "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Ticket/preview_ticket.php?action=view&ticketid=".$ticket['ticketid']."\"); return false'><div class='used-block ".$checkmark_ticket."' data-contact='$contact_id' data-blocks = '$rows' data-row='$calendar_row' data-duration='$duration' data-ticket='".$ticket['ticketid']."' data-timestamp='".date('Y-m-d H:i:s')."' data-tickettype='".$ticket['ticket_type']."' ";
			$row_html .= "style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%;".$ticket_styling.$icon_background."'>";
			$row_html .= "<span class='$status_class' style='display: block; float: left; width: calc(100% - 2em);'>".$icon_img;
			if($ticket_status_color_code == 1 && !empty($ticket_status_color[$status])) {
				$row_html .= '<div class="ticket-status-color" style="background-color: '.$ticket_status_color[$status].';"></div>';
			}
			$row_html .= "<b>$project_name".'<br />'.$start_time." - ".$end_time."</b></span><div class='drag-handle full-height' title='Drag Me!'><img class='drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' style='filter: brightness(200%); float: right; width: 2em;' title='Drag'></div></div></a>";
		} else {
			if ($calendar_col[$calendar_row][1] == 'SHIFT') {
				$rows = 1;
				if ($_GET['mode'] == 'client') {
					$patientid = $calendar_col[$calendar_row][4];
					$therapistsid = '';
				} else {
					$therapistsid = $calendar_col[$calendar_row][4];
					$patientid = '';
				}
				$appoint_date = $calendar_col[$calendar_row][3].' '.date('h:i a', strtotime($calendar_col[$calendar_row][2]));
				$end_appoint_date = $calendar_col[$calendar_row][3].' '.date('h:i a', strtotime("+30 minutes", strtotime($calendar_col[$calendar_row][2])));

				$page_query['action'] = 'edit';
				$page_query['bookingid'] = 'NEW';
				$page_query['appoint_date'] = $appoint_date;
				$page_query['end_appoint_date'] = $end_appoint_date;	
				$page_query['patientid'] = $patientid;
				$page_query['therapistsid'] = $therapistsid;
				unset($page_query['add_reminder']);
				unset($page_query['unbooked']);
				unset($page_query['equipment_assignmentid']);
				unset($page_query['teamid']);
				$row_html .= ($edit_access == 1 ? "<a href='' onclick='overlayIFrameSlider($(this).data(\"appturl\")); return false;' data-appturl=".WEBSITE_URL."/Calendar/booking.php?".http_build_query($page_query)." class='shift'>" : "");
				$row_html .= "<div class='resizable-shift' data-shifttype='appt' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-appt='".$appt['bookingid']."' data-duration='$duration' style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%; opacity: 0;'>";
				$row_html .= "</div>".($edit_access == 1 ? "</a>" : "");
				$page_query['add_reminder'] = $_GET['add_reminder'];
				$page_query['unbooked'] = $_GET['booked'];
				$page_query['equipment_assignmentid'] = $_GET['equipment_assignmentid'];
				$page_query['teamid'] = $_GET['teamid'];
				unset($page_query['action']);
				unset($page_query['bookingid']);
				unset($page_query['appoint_date']);
				unset($page_query['end_appoint_date']);
				unset($page_query['therapistsid']);
				unset($page_query['patientid']);
			} else if (!empty($calendar_col[$calendar_row][1])) {
				$appt = $calendar_col[$calendar_row][1];
				$rows = 1;
				$status_class = 'unconfirmed';
				switch($appt['follow_up_call_status']) {
					case 'Booking Confirmed':
						$status_class = 'confirmed';
						break;
					case 'Arrived':
						$status_class = 'arrived';
						break;
					case 'Invoiced':
						$status_class = 'invoiced';
						break;
					case 'Paid':
						$status_class = 'paid';
						break;
					case 'Rescheduled':
						$status_class = 'rescheduled';
						break;
					case 'Late Cancellation / No-Show':
						$status_class = 'late_noshow';
						break;
					case 'Cancelled':
						$status_class = 'cancelled';
						break;
				}
				if(date('Y-m-d', strtotime($appt['end_appoint_date'])) != date('Y-m-d', strtotime($appt['appoint_date']))) {
					$current_start_time = date('h:i a', strtotime($day_start) + ($calendar_row * $day_period * 60));
					$day_of_week = date('l', $current_day);
					$staff_shift = checkShiftIntervals($dbc, $contact_id, $day_of_week, $current_day)[0];
					if (date('Y-m-d', strtotime($appt['end_appoint_date'])) == $current_day && !empty($appt['end_appoint_date'])) { 
						$current_end_time = date('h:i a', strtotime($appt['end_appoint_date']));
					} else if (!empty($staff_shift['endtime'])) {
						$current_end_time = date('h:i a', strtotime($staff_shift['endtime']));
					} else {
						$current_end_time = date('h:i a', strtotime($day_end));
					}
					$duration = strtotime($current_end_time) - strtotime($current_start_time);
				} else {
					$current_start_time = date('h:i a', strtotime($appt['appoint_date']));
					$current_end_time = date('h:i a', strtotime($appt['end_appoint_date']));
					$duration = (strtotime($appt['end_appoint_date']) - strtotime($appt['appoint_date']));
				}
				if ($duration > $day_period * 60) {
					$rows = ceil($duration / ($day_period * 60));
				}
				if ($duration < $day_period * 60) {
					$duration = $day_period * 60;
				}
				$page_query['action'] = 'view';
				$page_query['bookingid'] = $appt['bookingid'];
				$appt_page_query = $page_query;
				unset($appt_page_query['add_reminder']);
				unset($appt_page_query['unbooked']);
				unset($appt_page_query['equipment_assignmentid']);
				unset($appt_page_query['teamid']);
				$row_html .= ($edit_access == 1 ? "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Calendar/booking.php?".http_build_query($appt_page_query)."\"); return false;'>" : "")."<div class='used-block' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-appt='".$appt['bookingid']."' data-clientid='".$appt['patientid']."' ";
				$row_html .= "data-duration='$duration' style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%;'>";
				$row_html .= "<span class='$status_class' style='display: block; float: left; width: calc(100% - 2em);'>";
				$row_html .= "<b>".$current_start_time." - ".$current_end_time."</b>".'<br />';
				$row_html .= get_contact($dbc, ($_GET['mode'] == 'client' ? $appt['therapistsid'] : $appt['patientid']))." - ".($appt['serviceid'] > 0 ? get_services($dbc, $appt['serviceid'], "CONCAT(`category`,' ',`heading`)") : get_type_from_booking($dbc, $appt['type'])).'<br />';
				$row_html .= $appt['follow_up_call_status'];
				$row_html .= "</span><div class='drag-handle full-height' title='Drag Me!'><img class='drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' style='filter: brightness(200%); float: right; width: 2em;' title='Drag'></div></div>".($edit_access == 1 ? "</a>" : "");
				unset($page_query['action']);
				unset($page_query['bookingid']);
			}
		}
	} else if ($calendar_col[$calendar_row] != $calendar_col[$calendar_row - 1]) {
		$row_html .= $calendar_col[$calendar_row];
	}
	$row_html .= "</td>";

	$column['rows'][$calendar_row] = ['time'=>$calendar_row,'html'=>$row_html];
}

if(!$is_mobile_view) {
	echo json_encode($column);
}
?>