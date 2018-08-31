<?php $edit_access = vuaed_visible_function($dbc, 'calendar_rook'); ?>
<style>
.popped-field {
	border: 1px solid rgb(221, 221, 221);
	font-size: 1.2em;
	max-width: 15em;
	padding: 1em;
}
.highlightCell {
	background-color: rgba(0,0,0,0.2) !important;
}
a.shift {
	z-index: -1;
}
td {
	z-index: 1;
}
td:empty {
	z-index: 0;
}
</style>
<script type="text/javascript">
var offline_mode = '<?= $_GET['offline'] ?>';
$(document).ready(function() {
    $(window).resize(function() {
    	resizeBlocks();
    }).resize();
    $('a').on('click', function() {
    	resizeBlocks();
    });
    $('.collapsible').on('click', function() {
    	resizeBlocks();
    });
});
function resizeBlocks() {
	$('.used-block').each(function() {
		var rows = $(this).data('blocks');
		var parent = $(this).closest('td');
		var header = 0;
		if (parent.prev().is('thead:visible')) {
			header = $(this).closest('table').find('thead tr').first()[0].clientHeight;
		}
		$(this).css('top', header);
		$(this).css('left', '0');
		$(this).css('margin', '0');
		$(this).css('padding', '0.2em');
		$(this).height((parent.innerHeight() * parseInt(rows)) - header);
		$(this).height('calc(' + $(this).height() + 'px - 2px + ' + rows + 'px)');
		$(this).closest('td').find('.ui-resizable-e').height((parent.innerHeight() * parseInt(rows)) - header);
		// $(this).width(parent.innerWidth());
		// $(this).width('calc(' + $(this).width() + 'px)');
	});
}
</script>
<table class="table table_bordered <?= $appointment_calendar ?>" style="overflow-x: auto;">
	<?php $region_list = explode(',',get_config($dbc, '%_region', true));
	$region_colours = explode(',',get_config($dbc, '%_region_colour', true));
	$calendar_ticket_card_fields = explode(',',get_config($dbc, 'calendar_ticket_card_fields')); ?>
	<?php foreach($calendar_table[0][0] as $calendar_row => $calendar_times) {
		if($calendar_row === 'warnings' || $calendar_row === 'reminders' || $calendar_row === 'notes') {
			echo '<tr class="calendar_notes_row">';
		} else if($calendar_row === 'ticket_summary') {
			echo '<tr class="ticket_summary_row">';
		} else {
			echo ($calendar_row === 'title' ? "<thead><tr style='position: absolute; z-index: 9;'>" : '<tr>');
		}
		foreach($calendar_table as $current_day => $calendar_col_date) {
			foreach($calendar_col_date as $contact_id => $calendar_col) {
				if(!empty($equipassign_data[$current_day][$contact_id])) {
					$equipassignid_data = "data-equipassign='".$equipassign_data[$current_day][$contact_id]."'";
				} else {
					$equipassignid_data = "";
				}
				if($calendar_row === 'title') {
					echo "<th data-contact='$contact_id' $equipassignid_data data-date='".$current_day."' data-row='title' style='";
					if($equipassign_data[$current_day][$contact_id] > 0) {
						$equipassign_region = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `region` FROM `equipment_assignment` WHERE `equipment_assignmentid`='".$equipassign_data[$current_day][$contact_id]."'"))['region'];
						foreach($region_list as $region_line => $region_name) {
							if($equipassign_region == $region_name) {
								echo "background: ".$region_colours[$region_line].";color: #000;";
							}
						}
					}
					echo ($contact_id > 0 ? 'border-left: 1px solid rgb(221, 221, 221); min-width: 15em; width: 50%;' : 'max-width: 7em; min-width: 7em; width: 7em;')."padding:0;'><div class='resizer' style='min-width:100%; max-width:100%; padding:0.5em;'>";
					echo ($current_day == 0 ? $calendar_col['title'] : ($_GET['view'] == 'daily' ? $calendar_col[$calendar_row] : date('l, F d', strtotime($current_day)).'<br>'.$calendar_col[$calendar_row]))."</div></th>";
				} else if ($contact_id > 0 && ($calendar_row === 'warnings' || $calendar_row === 'reminders' || $calendar_row === 'notes')) {
					echo "<td data-date='".$current_day."' $equipassignid_data data-calendartype='".$_GET['type']."' data-calendarmode='".$_GET['mode']."' data-contact='$contact_id' style='position:relative; ".($contact_id > 0 ? 'border-left: 1px solid rgb(221, 221, 221); min-width: 15em; width: 50%;' : 'max-width: 7em; min-width: 7em; width: 7em;')."'><div class='calendar_notes' style='overflow-y: hidden;'>".$calendar_col[$calendar_row].'</div>';
					if($calendar_row == 'notes' && $contact_id != 0) {
						echo '<div class="calendar_notes_btn" style="text-align: right; position: relative;">'.($edit_access == 1 ? '<a class="edit_calendar_notes" href=""><sub>EDIT</sub></a>' : '').'</div>';
						echo '<div class="calendar_notes_edit" style="display:none;"><textarea style="resize: vertical;" class="noMceEditor form-control">'.html_entity_decode($calendar_col[$calendar_row]).'</textarea></div>';
					}
					echo '<a class="expand-div-link" href="" onclick="expandDiv(this); return false;"><div style="font-size: 1.5em; text-align: center;">...</div></a>';
				} else if ($contact_id > 0 && $calendar_row === 'ticket_summary') {
					echo "<td data-contact='$contact_id' data-draggable='0' style='position:relative; ".($contact_id > 0 ? 'border-left: 1px solid rgb(221, 221, 221); min-width: 15em; width: 50%;' : 'max-width: 7em; min-width: 7em; width: 7em;')."'>".$calendar_col[$calendar_row]."</td>";
				} else {
					$is_shift = '';
					if ($calendar_col[$calendar_row][1] == 'SHIFT' || $calendar_col[$calendar_row][0] == 'no_shift') {
						$is_shift = ' background-color: #eee';
					}
					echo "<td data-region='".$calendar_table[$current_day][$contact_id]['region']."' data-date='".$current_day."' data-calendartype='".$_GET['type']."' data-contact='$contact_id' $equipassignid_data data-time='$calendar_row' data-duration='".($day_period * 60)."' style='position:relative; ".($contact_id > 0 ? 'border-left: 1px solid rgb(221, 221, 221); min-width: 15em; width: 50%;' : 'max-width: 7em; min-width: 7em; width: 7em;').$is_shift."'>";
					if($contact_id > 0 && $calendar_col[$calendar_row] != '' && $calendar_col[$calendar_row] != $calendar_col[$calendar_row - 1]) {
						if(($_GET['type'] == 'uni' || $_GET['type'] == 'my') && empty($_GET['shiftid'])) {
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
										$staff_shift = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts_shifts` WHERE `contactid` = '".$contact_id."' AND `startdate` <= '".date('Y-m-d', strtotime($current_day))."' AND `enddate` >= '".date('Y-m-d', strtotime($current_day))."' AND CONCAT(',', `repeat_days`, ',') LIKE '%,".$day_of_week.",%' AND `deleted` = 0 AND (`dayoff_type` = '' OR `dayoff_type` IS NULL)"));
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
									echo ($edit_access == 1 ? "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Calendar/booking.php?".http_build_query($appt_page_query)."\"); return false;'>" : "")."<div class='used-block' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-appt='".$appt['bookingid']."' ";
									echo "data-duration='$duration' style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%; $ticket_styling'>";
									echo "<span class='$status_class' style='display: block; float: left; width: calc(100% - 2em);'>";
									echo "<b>".$current_start_time." - ".$current_end_time."</b><br />";
									echo get_contact($dbc, ($_GET['mode'] == 'client' ? $appt['therapistsid'] : $appt['patientid']))." - ".($appt['serviceid'] > 0 ? get_services($dbc, $appt['serviceid'], "CONCAT(`category`,' ',`heading`)") : get_type_from_booking($dbc, $appt['type'])).'<br />';
									echo $appt['follow_up_call_status'];
									echo "</span><div class='drag-handle full-height' title='Drag Me!'><img class='drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' style='filter: brightness(200%); float: right; width: 2em;' title='Drag'></div></div>".($edit_access == 1 ? "</a>" : "");
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
									$heading = $ticket['heading'];
									$estimated_time = substr($ticket['max_time'], 0, 5);
									$status = $ticket['status'];
									$status_class = $status;
									$rows = 1;
									if($calendar_checkmark_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
										$checkmark_ticket = 'calendar-checkmark-ticket';
									} else {
										$checkmark_ticket = '';
									}
									if($calendar_highlight_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
										$ticket_styling = ' background-color:'.$calendar_completed_color[$status].';';
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
										$staff_shift = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts_shifts` WHERE `contactid` = '".$contact_id."' AND `startdate` <= '".date('Y-m-d', strtotime($current_day))."' AND `enddate` >= '".date('Y-m-d', strtotime($current_day))."' AND CONCAT(',', `repeat_days`, ',') LIKE '%,".$day_of_week.",%' AND `deleted` = 0 AND (`dayoff_type` = '' OR `dayoff_type` IS NULL)"));
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
									echo ($edit_access == 1 ? "<a href='".WEBSITE_URL."/Ticket/index.php?edit=".$ticket['ticketid']."' onclick='overlayIFrameSlider(this.href+\"&calendar_view=true\"); return false;'>" : "")."<div class='used-block ".$calendar_ticket."' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-duration='$duration' data-ticket='".$ticket['ticketid']."'' data-businessid='".$businessid."' data-contactid='".$contactid."' data-internal_qa_contactid='".$internal_qa_contactid."' data-deliverable_contactid='".$deliverable_contactid."' data-status='".$status."' ";
									echo "style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%;".$ticket_styling."'>";
									echo "<span class='$status_class' style='display: block; float: left; width: calc(100% - 2em);'>";
									if($ticket_status_color_code == 1 && !empty($ticket_status_color[$status])) {
										echo '<div class="ticket-status-color" style="background-color: '.$ticket_status_color[$status].';"></div>';
									}
									echo "<b>".($ticket['scheduled_lock'] > 0 ? '<img class="inline-img" title="Time has been Locked" src="../img/icons/lock.png">' : '').TICKET_NOUN." #".$ticket['ticketid']." : ".get_contact($dbc,$ticket['businessid'],'name')." : ".$heading." (".$estimated_time.")".'<br />'.$current_start_time." - ".$current_end_time.'<br />'."Status: ".$status."</b></span><div class='drag-handle full-height' title='Drag Me!'><img class='drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' style='filter: brightness(200%); float: right; width: 2em;' title='Drag'></div></div>".($edit_access == 1 ? "</a>" : "");
								}
							} else if($calendar_col[$calendar_row][0] == 'shift') {
								$rows = 1;
								$therapistsid = $calendar_col[$calendar_row][4];
								$patientid = '';
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
								echo ($edit_access == 1 ? "<a href='' onclick='universalAdd(this); return false;' class='shift' data-appturl='".WEBSITE_URL."/Calendar/booking.php?".http_build_query($page_query)."' data-ticketurl='".WEBSITE_URL."/Ticket/index.php?calendar_view=true&edit=0'>" : "");
								echo "<div data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-duration='$duration' style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%; opacity: 0;'>";
								echo "</div>".($edit_access == 1 ? "</a>" : "");
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
							echo ($edit_access == 1 ? "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Work Order/edit_workorder.php?action=view&workorderid=".$workorder['workorderid']."\"); return false;'>" : "")."<div class='used-block' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-duration='$duration' data-workorder='".$workorder['workorderid']."' data-region='".$region."' data-businessid='".$businessid."' data-assignstaff='".$assign_staff."' data-teamid='".$teamid."' ";
							echo "style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%;'>";
							echo "<span class='$status_class' style='display: block; float: left; width: calc(100% - 2em);'>";
							echo "<b>Work Order #".$workorder['heading'].'<br />'.get_client($dbc,$workorder['businessid']).'<br />'.$start_time." - ".$end_time."</b></span><div class='drag-handle full-height' title='Drag Me!'><img class='drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' style='filter: brightness(200%); float: right; width: 2em;' title='Drag'></div></div>".($edit_access == 1 ? "</a>" : "");
						} else if ($calendar_col[$calendar_row][0] == 'ticket_equip') {
							if($calendar_col[$calendar_row][1] == 'SHIFT') {
								$rows = 1;
								$current_time = $calendar_col[$calendar_row][2];
								$current_date = $calendar_col[$calendar_row][3];
								$equipment = $calendar_col[$calendar_row][4];
								$equipment_assignment = $calendar_col[$calendar_row][5];

								$equipmentid = $equipment['equipmentid'];
								$equipment_assignmentid = $equipment_assignment['equipment_assignmentid'];
								echo ($edit_access == 1 ? "<a href='' data-equipmentid='".$equipmentid."' data-equipmentid='".$equipment_assignmentid."' data-currenttime='".$current_time."' data-currentdate='".$current_date."' onclick='dispatchNewWorkOrder(this); return false;' class='shift'>" : "");
								echo "<div data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%; opacity: 0;'>";
								echo "</div>".($edit_access == 1 ? "</a>" : "");
							} else if (!empty($calendar_col[$calendar_row][1])) {
								$ticket = $calendar_col[$calendar_row][1];
								$region = $calendar_col[$calendar_row][2];
								$businessid = $calendar_col[$calendar_row][3];
								$assign_staff = $calendar_col[$calendar_row][4];
								$teamid = $calendar_col[$calendar_row][5];
								$equipment_assignmentid = $calendar_col[$calendar_row][6];
								$block_type = $calendar_col[$calendar_row][7];
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
								} else if (!empty($max_time)) {
									$end_time = date('h:i a', strtotime('+'.$max_time_hour.' hours +'.$max_time_minute.' minutes', strtotime($start_time)));
								} else {
									$end_time = date('h:i a', strtotime('+'.($day_period * 2).' minutes', strtotime($start_time)));
								}
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
								echo ($edit_access == 1 ? "<a href='".WEBSITE_URL."/Ticket/index.php?edit=".$ticket['ticketid']."' onclick='overlayIFrameSlider(this.href+\"&calendar_view=true\"); return false;'>" : "")."<div class='used-block ".$checkmark_ticket."' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-duration='$duration' data-ticket='".$ticket['ticketid']."' data-region='".$region."' data-businessid='".$businessid."' data-assignstaff='".$assign_staff."' data-teamid='".$teamid."' data-status='".$ticket['status']."' data-equipassign='".$equipment_assignmentid."' data-blocktype='".$block_type."' ";
								echo "style='";
								if($calendar_highlight_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
									echo 'background-color:'.$calendar_completed_color[$status].';';
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
												echo "background-color:".$region_colours[$region_line].";";
											}
										}
									}
								}
								echo "height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%;'>";
								echo "<span class='$status_class' style='display: block; float: left; width: calc(100% - 2em);'>";
								if($ticket_status_color_code == 1 && !empty($ticket_status_color[$status])) {
									echo '<div class="ticket-status-color" style="background-color: '.$ticket_status_color[$status].';"></div>';
								}
								echo '<b>'.($ticket['scheduled_lock'] > 0 ? '<img class="inline-img" title="Time has been Locked" src="../img/icons/lock.png">' : '').get_ticket_label($dbc, $ticket).($ticket['sub_label'] != '' ? '-'.$ticket['sub_label'] : '').'</b>'.
									(in_array('project',$calendar_ticket_card_fields) ? '<br />'.PROJECT_NOUN.' #'.$ticket['projectid'].' '.$ticket['project_name'].'<br />' : '').
									(in_array('customer',$calendar_ticket_card_fields) ? '<br />'.'Customer: '.get_contact($dbc, $ticket['businessid'], 'name') : '').
									(in_array('time',$calendar_ticket_card_fields) ? '<br />'."(".$max_time.") ".$start_time." - ".$end_time : '');
								if(in_array('available',$calendar_ticket_card_fields)) {
									if($ticket['pickup_start_available'].$ticket['pickup_end_available'] != '') {
										echo '<br />'."Available ";
										if($ticket['pickup_end_available'] == '') {
											echo "After ".$ticket['pickup_start_available'];
										} else if($ticket['pickup_start_available'] == '') {
											echo "Before ".$ticket['pickup_end_available'];
										} else {
											echo "Between ".$ticket['pickup_start_available']." and ".$ticket['pickup_end_available'];
										}
									}
								}
								echo (in_array('address',$calendar_ticket_card_fields) ? '<br />'.$ticket['pickup_name'].($ticket['pickup_name'] != '' ? '<br />' : ' ').$ticket['client_name'].($ticket['client_name'] != '' ? '<br />' : ' ').$ticket['pickup_address'].($ticket['pickup_address'] != '' ? '<br />' : ' ').$ticket['pickup_city'] : '');
								echo '<br />'."Status: ".$status."</b></span><div class='drag-handle full-height' title='Drag Me!'><img class='black-color pull-right inline-img drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' title='Drag'></div></div>".($edit_access == 1 ? "</a>" : "");
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
								echo ($edit_access == 1 ? "<a href='?".http_build_query($page_query)."' class='shift'>" : "");
								echo "<div data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-duration='$duration' style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%; opacity: 0;'>";
								echo "</div>".($edit_access == 1 ? "</a>" : "");
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
								$calendar_color = mysqli_fetch_array(mysqli_query($dbc, "SELECT `calendar_color` FROM `contacts` WHERE `contactid` = '".$shift['contactid']."'"))['calendar_color'];
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

									if(basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']) == 'calendars_mobile.php') {
										$echo_url = "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Calendar/shifts.php?".http_build_query($page_query)."\"); return false;'>";
									} else {
										$echo_url = "<a href='?".http_build_query($page_query)."'>";
									}

									echo ($edit_access == 1 ? $echo_url : "")."<div class='used-block' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-shift='".$shift['shiftid']."' data-recurring='$recurring' data-currentdate='$current_day' ";
									echo "data-duration='$duration' style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%;".$shift_styling."'>";
									echo "<span class='shift' style='display: block; float: left; width: calc(100% - 2em);'>".$warning_icon;
									echo "<b>".date('g:i a', strtotime($shift['starttime']))." - ".date('g:i a', strtotime($shift['endtime']))."</b>".'<br />';
									if($_GET['mode'] == 'client') {
										echo '<b>'.get_contact($dbc, $shift['contactid']).'</b>';
									} else if(!empty($shift['clientid'])) {
										echo '<b>'.get_contact($dbc, $shift['clientid']).'</b>';
									}
									echo "</span><div class='drag-handle full-height' title='Drag Me!'><img class='drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' style='filter: brightness(200%); float: right; width: 2em;' title='Drag'></div></div>".($edit_access == 1 ? "</a>" : "");
									unset($page_query['shiftid']);
								}
								
								if (!empty($dayoff)) {
									$rounded_starttime = strtotime($dayoff['starttime']) - (strtotime($dayoff['starttime']) % (60 * $day_period));
									$duration = (strtotime($dayoff['endtime']) - $rounded_starttime);
									if ($duration > $day_period * 60) {
										$rows = ceil($duration / ($day_period * 60));
									}
									$page_query['shiftid'] = $dayoff['shiftid'];

									if(basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']) == 'calendars_mobile.php') {
										$echo_url = "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Calendar/shifts.php?".http_build_query($page_query)."\"); return false;'>";
									} else {
										$echo_url = "<a href='?".http_build_query($page_query)."'>";
									}

									echo ($edit_access == 1 ? $echo_url : "")."<div class='used-block' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-shift='".$dayoff['shiftid']."' data-recurring='$recurring' data-currentdate='$calendar_row' ";
									echo "data-duration='$duration' style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%; background-color: #aaa;'>";
									echo "<span class='dayoff' style='display: block; float: left; width: calc(100% - 2em);'>";
									echo "<b>".date('g:i a', strtotime($dayoff['starttime']))." - ".date('g:i a', strtotime($dayoff['endtime']))."</b>".'<br />';
									echo $dayoff['dayoff_type'];
									echo "</span><div class='drag-handle full-height' title='Drag Me!'><img class='drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' style='filter: brightness(200%); float: right; width: 2em;' title='Drag'></div></div>".($edit_access == 1 ? "</a>" : "");
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
								$ticket_url = '../Ticket/index.php?edit=0&contactid='.$staffid.'&startdate='.$startdate.'&enddate='.$enddate.'&starttime='.$starttime.'&endtime='.$endtime;
								echo ($edit_access == 1 ? '<a href="'.$ticket_url.'" target="_blank" class="shift">' : '');
								echo "<div data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%; opacity: 0;'>";
								echo "</div>".($edit_access == 1 ? "</a>" : "");
							} else if (!empty($ticket)) {
								$businessid = $ticket['businessid'];
								$contactid = $ticket['contactid'];
								$internal_qa_contactid = $ticket['internal_qa_contactid'];
								$deliverable_contactid = $ticket['deliverable_contactid'];
								$heading = $ticket['heading'];
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
								if($calendar_highlight_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
									$ticket_styling = ' background-color:'.$calendar_completed_color[$status].';';
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
									$staff_shift = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts_shifts` WHERE `contactid` = '".$contact_id."' AND `startdate` <= '".date('Y-m-d', strtotime($current_day))."' AND `enddate` >= '".date('Y-m-d', strtotime($current_day))."' AND CONCAT(',', `repeat_days`, ',') LIKE '%,".$day_of_week.",%' AND `deleted` = 0 AND (`dayoff_type` = '' OR `dayoff_type` IS NULL)"));
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
								echo ($edit_access == 1 ? "<a href='".WEBSITE_URL."/Ticket/index.php?edit=".$ticket['ticketid']."' onclick='overlayIFrameSlider(this.href+\"&calendar_view=true\"); return false;'>" : "")."<div class='used-block' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-duration='$duration' data-ticket='".$ticket['ticketid']."'' data-businessid='".$businessid."' data-contactid='".$contactid."' data-internal_qa_contactid='".$internal_qa_contactid."' data-deliverable_contactid='".$deliverable_contactid."' data-status='".$status."' ";
								echo "style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%;".$ticket_styling."'>";
								echo "<span class='$status_class' style='display: block; float: left; width: calc(100% - 2em);'>";
								echo '<img src="'.WEBSITE_URL.'/img/'.$date_color.'" style="width:1em;" border="0" alt=""> ';
								if($ticket_status_color_code == 1 && !empty($ticket_status_color[$status])) {
									echo '<div class="ticket-status-color" style="background-color: '.$ticket_status_color[$status].';"></div>';
								}
								echo '<b>'.($ticket['scheduled_lock'] > 0 ? '<img class="inline-img" title="Time has been Locked" src="../img/icons/lock.png">' : '').get_ticket_label($dbc, $ticket).($ticket['sub_label'] != '' ? '-'.$ticket['sub_label'] : '').'</b>'.
									(in_array('project',$calendar_ticket_card_fields) ? '<br />'.PROJECT_NOUN.' #'.$ticket['projectid'].' '.$ticket['project_name'].'<br />' : '').
									(in_array('customer',$calendar_ticket_card_fields) ? '<br />'.'Customer: '.get_contact($dbc, $ticket['businessid'], 'name') : '').
									(in_array('time',$calendar_ticket_card_fields) ? '<br />'."(".$estimated_time.") ".$current_start_time." - ".$current_end_time : '');
								if(in_array('available',$calendar_ticket_card_fields)) {
									if($ticket['pickup_start_available'].$ticket['pickup_end_available'] != '') {
										echo '<br />'."Available ";
										if($ticket['pickup_end_available'] == '') {
											echo "After ".$ticket['pickup_start_available'];
										} else if($ticket['pickup_start_available'] == '') {
											echo "Before ".$ticket['pickup_end_available'];
										} else {
											echo "Between ".$ticket['pickup_start_available']." and ".$ticket['pickup_end_available'];
										}
									}
								}
								echo '<br />'."Status: ".$status."</b></span><div class='drag-handle full-height' title='Drag Me!'><img class='black-color pull-right inline-img drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' title='Drag'></div></div>".($edit_access == 1 ? "</a>" : "");
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
							echo ($edit_access == 1 ? "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Work Order/edit_workorder.php?action=view&workorderid=".$workorder['workorderid']."\"); return false;'>" : "")."<div class='used-block' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-duration='$duration' data-workorder='".$workorder['workorderid']."' data-region='".$region."' data-businessid='".$businessid."' data-assignstaff='".$assign_staff."' data-teamid='".$teamid."' ";
							echo "style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%;'>";
							echo "<span class='$status_class' style='display: block; float: left; width: calc(100% - 2em);'>";
							echo "<b>Work Order #".$workorder['heading'].'<br />'.get_client($dbc,$workorder['businessid']).'<br />'.$start_time." - ".$end_time."</b></span><div class='drag-handle full-height' title='Drag Me!'><img class='drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' style='filter: brightness(200%); float: right; width: 2em;' title='Drag'></div></div>".($edit_access == 1 ? "</a>" : "");
						} else if ($calendar_col[$calendar_row][0] == 'ticket_event') {
							$ticket = $calendar_col[$calendar_row][1];
							$project_name = $calendar_col[$calendar_row][2];
							$status = $ticket['status'];
							if($calendar_checkmark_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
								$checkmark_ticket = 'calendar-checkmark-ticket';
							} else {
								$checkmark_ticket = '';
							}
							if($calendar_highlight_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
								$ticket_styling = ' background-color:'.$calendar_completed_color[$status].';';
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
							echo "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Ticket/preview_ticket.php?action=view&ticketid=".$ticket['ticketid']."\"); return false'><div class='used-block ".$checkmark_ticket."' data-contact='$contact_id' data-blocks = '$rows' data-row='$calendar_row' data-duration='$duration' data-ticket='".$ticket['ticketid']."' ";
							echo "style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%;".$ticket_styling."'>";
							echo "<span class='$status_class' style='display: block; float: left; width: calc(100% - 2em);'>";
							if($ticket_status_color_code == 1 && !empty($ticket_status_color[$status])) {
								echo '<div class="ticket-status-color" style="background-color: '.$ticket_status_color[$status].';"></div>';
							}
							echo "<b>$project_name".'<br />'.$start_time." - ".$end_time."</b></span><div class='drag-handle full-height' title='Drag Me!'><img class='drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' style='filter: brightness(200%); float: right; width: 2em;' title='Drag'></div></div></a>";
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
								echo ($edit_access == 1 ? "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Calendar/booking.php?".http_build_query($page_query)."\"); return false;' class='shift'>" : "");
								echo "<div data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-appt='".$appt['bookingid']."' data-duration='$duration' style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%; opacity: 0;'>";
								echo "</div>".($edit_access == 1 ? "</a>" : "");
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
									$staff_shift = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts_shifts` WHERE `contactid` = '".$contact_id."' AND `startdate` <= '".date('Y-m-d', strtotime($current_day))."' AND `enddate` >= '".date('Y-m-d', strtotime($current_day))."' AND CONCAT(',', `repeat_days`, ',') LIKE '%,".$day_of_week.",%' AND `deleted` = 0 AND (`dayoff_type` = '' OR `dayoff_type` IS NULL)"));
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
								echo ($edit_access == 1 ? "<a href='' onclick='overlayIFrameSlider(\"".WEBSITE_URL."/Calendar/booking.php?".http_build_query($appt_page_query)."\"); return false;'>" : "")."<div class='used-block' data-contact='$contact_id' data-blocks='$rows' data-row='$calendar_row' data-appt='".$appt['bookingid']."' ";
								echo "data-duration='$duration' style='height: calc(".$rows." * (1em + 15px) - 1px); overflow-y: hidden; top: 0; left: 0; margin: 0; padding: 0.2em; position: absolute; width: 100%;'>";
								echo "<span class='$status_class' style='display: block; float: left; width: calc(100% - 2em);'>";
								echo "<b>".$current_start_time." - ".$current_end_time."</b>".'<br />';
								echo get_contact($dbc, ($_GET['mode'] == 'client' ? $appt['therapistsid'] : $appt['patientid']))." - ".($appt['serviceid'] > 0 ? get_services($dbc, $appt['serviceid'], "CONCAT(`category`,' ',`heading`)") : get_type_from_booking($dbc, $appt['type'])).'<br />';
								echo $appt['follow_up_call_status'];
								echo "</span><div class='drag-handle full-height' title='Drag Me!'><img class='drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' style='filter: brightness(200%); float: right; width: 2em;' title='Drag'></div></div>".($edit_access == 1 ? "</a>" : "");
								unset($page_query['action']);
								unset($page_query['bookingid']);
							}
						}
					} else if ($calendar_col[$calendar_row] != $calendar_col[$calendar_row - 1]) {
						echo $calendar_col[$calendar_row];
					}
					echo "</td>";
				}
				if($calendar_col[$calendar_row] != '' && $contact_id > 0 && $calendar_row != 'title' && $calendar_row != 'notes') {
				}
			}
		}
		echo "</tr>".($calendar_row === 'title' ? "</thead>" : '');
	} ?>
</table>