<?php error_reporting(0);
include_once('../include.php');
include_once('../Ticket/field_list.php');
$folder = FOLDER_NAME;
if(isset($_GET['folder'])) {
	$folder = filter_var($_GET['folder'], FILTER_SANITIZE_STRING);
	$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
}
if(basename($_SERVER['SCRIPT_FILENAME']) == 'add_ticket_checkin.php') {
	ob_clean();
	$strict_view = strictview_visible_function($dbc, 'ticket');
	$tile_security = get_security($dbc, ($_GET['tile_name'] == '' ? 'ticket' : 'ticket_type_'.$_GET['tile_name']));
	if($strict_view > 0) {
		$tile_security['edit'] = 0;
		$tile_security['config'] = 0;
	}
	$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'"));
	$ticket_roles = explode('#*#',get_config($dbc, 'ticket_roles'));
	$ticket_role = mysqli_query($dbc, "SELECT `position` FROM `ticket_attached` WHERE `src_table`='Staff' AND `position`!='' AND `item_id`='".$_SESSION['contactid']."' AND `ticketid`='$ticketid' AND `ticketid` > 0");
	if($get_ticket['to_do_date'] > date('Y-m-d') && strpos($value_config,',Ticket Edit Cutoff,') !== FALSE && config_visible_function($dbc, 'ticket') < 1) {
		$access_staff_checkin = false;
		$access_all_checkin = false;
	} else if($get_ticket['status'] == 'Archive') {
		$access_staff_checkin = 0;
		$access_all_checkin = 0;
	} else if(config_visible_function($dbc, 'ticket') > 0) {
		$access_staff_checkin = check_subtab_persmission($dbc, 'ticket', ROLE, 'staff_checkin');
		$access_all_checkin = check_subtab_persmission($dbc, 'ticket', ROLE, 'all_checkin');
	} else if((count($ticket_roles) > 1 || explode('|',$ticket_roles[0])[0] != '') && mysqli_num_rows($ticket_role) > 0) {
		$ticket_role = mysqli_fetch_assoc($ticket_role);
		foreach($ticket_roles as $ticket_role_level) {
			$ticket_role_level = explode('|',$ticket_role_level);
			if($ticket_role_level[0] > 0) {
				$ticket_role_level[0] = get_positions($dbc, $ticket_role_level[0], 'name');
			}
			if($ticket_role_level[0] == $ticket_role['position']) {
				$access_staff_checkin = in_array('staff_checkin',$ticket_role_level);
				$access_all_checkin = in_array('all_checkin',$ticket_role_level);
			}
		}
	} else if(count(array_filter($arr, function ($var) { return (strpos($var, 'default') !== false); })) > 0) {
		foreach($ticket_roles as $ticket_role_level) {
			$ticket_role_level = explode('|',$ticket_role_level);
			if(in_array('default',$ticket_role_level)) {
				$access_staff_checkin = in_array('staff_checkin',$ticket_role_level);
				$access_all_checkin = in_array('all_checkin',$ticket_role_level);
			}
		}
	} else {
		$access_staff_checkin = check_subtab_persmission($dbc, 'ticket', ROLE, 'staff_checkin');
		$access_all_checkin = check_subtab_persmission($dbc, 'ticket', ROLE, 'all_checkin');
	}
	$value_config = get_field_config($dbc, 'tickets');
	if($get_ticket['ticket_type'] != '') {
		$value_config .= get_config($dbc, 'ticket_fields_'.$get_ticket['ticket_type']).',';
	}

	//Action Mode Fields
	if($_GET['action_mode'] == 1) {
		$value_config_all = $value_config;
		$value_config = ','.get_config($dbc, 'ticket_action_fields').',';
		if(!empty($get_ticket['ticket_type'])) {
			$value_config .= get_config($dbc, 'ticket_action_fields_'.$get_ticket['ticket_type']).',';
		}
		if(empty(trim($value_config,','))) {
			$value_config = $value_config_all;
		} else {
			foreach($action_mode_ignore_fields as $action_mode_ignore_field) {
				if(strpos(','.$value_config_all.',',','.$action_mode_ignore_field.',') !== FALSE) {
					$value_config .= ','.$action_mode_ignore_field;
				}
			}
			$value_config = ','.implode(',',array_intersect(explode(',',$value_config), explode(',',$value_config_all))).',';
		}
	}

	//Apply Templates
	if(strpos($value_config,',TEMPLATE Work Ticket') !== FALSE) {
		$value_config = ',Information,PI Business,PI Name,PI Project,PI AFE,PI Sites,Staff,Staff Position,Staff Hours,Staff Overtime,Staff Travel,Staff Subsistence,Services,Service Category,Equipment,Materials,Material Quantity,Material Rates,Purchase Orders,Notes,';
	}
	$query_daily = "";
	if(strpos($value_config,',Time Tracking Current,') !== FALSE) {
		$query_daily = " AND `date_stamp`='".date('Y-m-d')."' ";
	}

	$sort_field = 'Check In';
	$field_list = $accordion_list[$sort_field];
	$field_sort_order = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_fields` WHERE `ticket_type` = '".(empty($get_ticket['ticket_type']) ? 'tickets' : 'tickets_'.$get_ticket['ticket_type'])."' AND `accordion` = '".$sort_field."'"))['fields'];
	$field_sort_order = explode(',', $field_sort_order);
	foreach ($field_list as $default_field) {
		if(!in_array($default_field, $field_sort_order)) {
			$field_sort_order[] = $default_field;
		}
	}
	$renamed_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($get_ticket['ticket_type']) ? 'tickets' : 'tickets_'.$get_ticket['ticket_type'])."' AND `accordion` = '".$sort_field."'"))['accordion_name'];
}
if(!empty($query_daily)) {
	$query_daily = " AND (`date_stamp`='".date('Y-m-d')."' OR IFNULL(`checked_out`,'') = '')";
}
if(strpos($value_config, ',Checkin Delivery,') !== FALSE && $_GET['stop'] > 0) {
	$dbc->query("INSERT INTO `ticket_attached` (`ticketid`, `src_table`, `item_id`, `line_id`) SELECT `ticketid`, 'Delivery', '".$_SESSION['contactid']."', `id` FROM `ticket_schedule` WHERE `deleted`=0 AND `ticketid`='$ticketid' AND `id`='".$_GET['stop']."' AND `id` NOT IN (SELECT `line_id` FROM `ticket_attached` WHERE `deleted`=0 AND `ticketid`='$ticketid' AND `src_table`='Delivery')");
	$dbc->query("UPDATE `ticket_attached` SET `item_id`='".$_SESSION['contactid']."' WHERE `src_table`='Delivery' AND `line_id`='".$_GET['stop']."' AND `deleted`=0 AND `arrived`=0");
} ?>
<script>
$(document).ready(function() {
	setSave();
});
</script>
<h3><?= (!empty($renamed_accordion) ? $renamed_accordion : (strpos($value_config, ','."Check In Member Drop Off".',') !== FALSE ? 'Member Drop Off' : 'Check In')) ?>
<?php $checkins = mysqli_query($dbc, "SELECT `id`, `src_table`, `arrived`, `item_id`, `position`, `description`, `notes`, `timer_start` FROM `ticket_attached` WHERE `item_id` > 0 AND `src_table` NOT IN ('Wait List') AND `deleted`=0 AND `ticketid`='$ticketid' AND '$ticketid' > 0 AND (`src_table` != 'Delivery' OR `line_id`='$stopid') AND `tile_name`='".$folder."' $query_daily ORDER BY `src_table` != 'Staff', `src_table`");
if(strpos($value_config, ',Checkin Delivery Require,') !== FALSE && !($_GET['stop'] > 0)) { ?>
	<script>
	$(document).ready(function() {
		$('[data-table]').click(cancelClick);
	});
	</script>
<?php }
if(strpos($value_config, ',Checkin Delivery,') !== FALSE && !($_GET['stop'] > 0)) { ?>
	</h3><h4>Please access a specific Stop to check in</h4>
<?php } else if(mysqli_num_rows($checkins) == 0) { ?>
	</h3><h4>No records found attached to this <?= TICKET_NOUN ?>.</h4>
<?php } else if(strpos($value_config, ',Checkin Hide All Button,') === FALSE) { ?>
	<button class="btn brand-btn pull-right" onclick="if($('#collapse_checkin,#tab_section_ticket_checkin').find('.toggle[value=0]').length > 0 && confirm('Are you sure you want to check in all individuals?')) { checkinAll(this); } return false;">Check In All</button></h3>
	<div class="clearfix"></div>
<?php } else { ?>
	</h3>
<?php }
$src_group = '';
$checkins = mysqli_fetch_all($checkins,MYSQLI_ASSOC);
foreach ($field_sort_order as $field_sort_order) {
	foreach ($checkins as $checkin) {
		if('Checkin '.$checkin['src_table'] == $field_sort_order) {
			if($checkin['src_table'] != $src_group && in_array($checkin['src_table'],['Members','Staff','Staff_Tasks','Delivery','Clients','material','equipment']) && strpos($value_config, ',Checkin '.$checkin['src_table'].',') !== FALSE) {
				$src_group = $checkin['src_table'];
				if($src_group != '') {
					echo "<hr>\n";
				}
				echo "<h4>".ucwords($src_group == 'Staff_Tasks' ? 'Staff' : $src_group)."</h4>";
			}
			if($checkin['src_table'] == 'Members' && strpos($value_config, ',Checkin '.$checkin['src_table'].',') !== FALSE) { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?= get_contact($dbc, $checkin['item_id']) ?>:</label>
					<div class="col-sm-8 <?= $access_all_checkin == TRUE ? 'toggleSwitch mobile-lg' : '' ?>">
						<input type="hidden" name="checkin_id[]" value="<?= $checkin['item_id'] ?>">
						<input type="hidden" name="arrived" data-table="ticket_attached" data-id="<?= $checkin['id'] ?>" data-id-field="id" value="<?= $checkin['arrived'] ?>" class="toggle" <?= strpos($value_config,',Check Out Before Check In,') !== FALSE ? 'data-no-toggle-off="1"' : '' ?>>
						<span style="<?= $checkin['arrived'] > 0 && (!empty($checkin['timer_start']) || strpos($value_config,',Check Out Before Check In,') === FALSE) ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg inline-img on_img"> Click to Check In</span>
						<span style="<?= $checkin['arrived'] > 0 && (!empty($checkin['timer_start']) || strpos($value_config,',Check Out Before Check In,') === FALSE) ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg inline-img off_img"> Checked In</span>
					</div>
					<?php $pdf_contents[] = [$checkin['src_table'], get_contact($dbc, $checkin['item_id']).' - '.($checkin['arrived'] > 0 ? 'Checked In' : 'Not Checked In')]; ?>
					<div class="col-sm-12">
						<div class="panel-group" id="checkin<?= $checkin['id'] ?>">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Enter Member's Medication details here."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
										<a data-toggle="collapse" data-parent="#checkin<?= $checkin['id'] ?>" href="#collapse_medication_<?=$checkin['id'] ?>">
											Medications<span class="glyphicon glyphicon-plus"></span>
										</a>
									</h4>
								</div>

								<div id="collapse_medication_<?=$checkin['id'] ?>" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="hide-titles-mob">
											<label class="col-sm-4">Medication</label>
											<label class="col-sm-4">Dosage</label>
											<label class="col-sm-3">Time</label>
										</div>
										<?php
										if (strpos($value_config, ',Medication Multiple Days,') !== false) {
											if(!empty($get_ticket['to_do_date'])) {
												$ticket_start_date = $get_ticket['to_do_date'];
												$ticket_end_date = empty(str_replace('0000-00-00','',$get_ticket['to_do_end_date'])) ? $get_ticket['to_do_date'] : $get_ticket['to_do_end_date'];
												for($cur_date = $ticket_start_date; strtotime($cur_date) <= strtotime($ticket_end_date); $cur_date = date('Y-m-d', strtotime($cur_date.' + 1 day'))) {
													$cur_start_time = $get_ticket['start_time'];
													$cur_end_time = $get_ticket['end_time'];
													if($cur_date != $ticket_start_date) {
														$cur_start_time = '00:00 am';
													}
													if($cur_date != $ticket_end_date) {
														$cur_end_time = '12:00 am';
													}
													mysqli_query($dbc, "INSERT INTO `ticket_attached` (`ticketid`, `item_id`, `src_table`, `position`, `description`, `shift_start`, `date_stamp`) SELECT `ticket_attached`.`ticketid`, `ticket_attached`.`item_id`, 'medication', `medication`.`title`, `medication`.`dosage`, `medication`.`administration_times`, '$cur_date' FROM `ticket_attached` LEFT JOIN `medication` ON `ticket_attached`.`item_id`=`medication`.`clientid` LEFT JOIN `ticket_attached` med_attached ON `med_attached`.`item_id`=`ticket_attached`.`item_id` AND `med_attached`.`position`=`medication`.`title` AND `med_attached`.`ticketid`=`ticket_attached`.`ticketid` AND `med_attached`.`date_stamp` = '$cur_date' WHERE `ticket_attached`.`src_table`='Members' AND `ticket_attached`.`ticketid`='$ticketid' AND `medication`.`deleted`=0 AND (TIME_TO_SEC(`medication`.`administration_times`) IS NULL OR TIME_TO_SEC(`medication`.`administration_times`) BETWEEN TIME_TO_SEC('".$cur_start_time."') AND TIME_TO_SEC('".$cur_end_time."')) AND `med_attached`.`item_id` IS NULL AND IFNULL(`medication`.`title`,'') != ''");
												}
											}
										} else {
											mysqli_query($dbc, "INSERT INTO `ticket_attached` (`ticketid`, `item_id`, `src_table`, `position`, `description`, `shift_start`) SELECT `ticket_attached`.`ticketid`, `ticket_attached`.`item_id`, 'medication', `medication`.`title`, `medication`.`dosage`, `medication`.`administration_times` FROM `ticket_attached` LEFT JOIN `medication` ON `ticket_attached`.`item_id`=`medication`.`clientid` LEFT JOIN `ticket_attached` med_attached ON `med_attached`.`item_id`=`ticket_attached`.`item_id` AND `med_attached`.`position`=`medication`.`title` AND `med_attached`.`ticketid`=`ticket_attached`.`ticketid` WHERE `ticket_attached`.`src_table`='Members' AND `ticket_attached`.`ticketid`='$ticketid' AND `medication`.`deleted`=0 AND (TIME_TO_SEC(`medication`.`administration_times`) IS NULL OR TIME_TO_SEC(`medication`.`administration_times`) BETWEEN TIME_TO_SEC('".$get_ticket['start_time']."') AND TIME_TO_SEC('".$get_ticket['end_time']."')) AND `med_attached`.`item_id` IS NULL AND IFNULL(`medication`.`title`,'') != ''");
										}
										$medications = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `src_table`='medication' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `item_id`='{$checkin['item_id']}' AND `deleted`=0 ".$query_daily);
										$medication = mysqli_fetch_assoc($medications);
										do { ?>
											<div class="multi-block">
												<div class="col-sm-4">
													<label class="show-on-mob">Medication:</label>
													<input type="text" name="position" <?= $access_all_checkin == TRUE && $medication['arrived'] == 0 ? 'data-table="ticket_attached" data-id="'.$medication['id'].'" data-id-field="id" data-type="medication" data-type-field="src_table" data-attach="'.$checkin['item_id'].'" data-attach-field="item_id"' : 'readonly' ?> class="form-control" value="<?= $medication['position'] ?>">
												</div>
												<div class="col-sm-4">
													<label class="show-on-mob">Dosage:</label>
													<input type="text" name="description" <?= $access_all_checkin == TRUE && $medication['arrived'] == 0 ? 'data-table="ticket_attached" data-id="'.$medication['id'].'" data-id-field="id" data-type="medication" data-type-field="src_table" data-attach="'.$checkin['item_id'].'" data-attach-field="item_id"' : 'readonly' ?> class="form-control" value="<?= $medication['description'] ?>">
												</div>
												<div class="col-sm-3">
													<label class="show-on-mob">Time:</label>
													<input type="text" name="shift_start" <?= $access_all_checkin == TRUE && $medication['arrived'] == 0 ? 'data-table="ticket_attached" data-id="'.$medication['id'].'" data-id-field="id" data-type="medication" data-type-field="src_table" data-attach="'.$checkin['item_id'].'" data-attach-field="item_id"' : 'readonly' ?> class="datetimepicker form-control" value="<?= $medication['shift_start'] ?>">
												</div>
												<?php if($access_all_checkin == TRUE) { ?>
													<div class="col-sm-1">
														<img class="inline-img pull-right" data-history-label="Medication" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
														<img class="inline-img pull-right" data-history-label="Medication" onclick="remMulti(this);" src="../img/remove.png">
													</div>
												<?php } ?>
											</div>
										<?php } while($medication = mysqli_fetch_assoc($medications)); ?>
									</div>
								</div>
							</div>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Enter Member's Guardian information here."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
										<a data-toggle="collapse" data-parent="#checkin<?= $checkin['id'] ?>" href="#collapse_guardian_<?=$checkin['id'] ?>">
											Guardian<span class="glyphicon glyphicon-plus"></span>
										</a>
									</h4>
								</div>

								<div id="collapse_guardian_<?=$checkin['id'] ?>" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="hide-titles-mob">
											<label class="col-sm-3">First Name</label>
											<label class="col-sm-3">Last Name</label>
											<label class="col-sm-3">Phone Number</label>
											<label class="col-sm-3">Confirmed</label>
										</div>
										<?php $guardian_status = explode(',',$checkin['notes']);
										$guardians = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts_medical` WHERE `contactid`='{$checkin['item_id']}'"));
										$guardians_first_name = explode('*#*',$guardians['guardians_first_name']);
										$guardians_last_name = explode('*#*',$guardians['guardians_last_name']);
										$guardians_work_phone = explode('*#*',$guardians['guardians_work_phone']);
										$guardians_home_phone = explode('*#*',$guardians['guardians_home_phone']);
										$guardians_cell_phone = explode('*#*',$guardians['guardians_cell_phone']);
										$visible_count = 0;
										foreach($guardians_first_name as $i => $first_name) {
											$visible_count += $guardian_status[$i] > 1 ? 0 : 1;
											$contact_number_type = ($guardians_cell_phone[$i] != '' ? 'guardians_cell_phone' : ($guardians_work_phone[$i] != '' ? 'guardians_work_phone' : 'guardians_home_phone'));
											$contact_number_label = ($contact_number_type == 'guardians_cell_phone' ? 'Cell Phone' : ($contact_number_type == 'guardians_work_phone' ? 'Work Phone' : 'Home Phone')); ?>
											<div class="multi-block" style="<?= $guardian_status[$i] > 1 ? 'display:none;' : '' ?>">
												<div class="col-sm-3">
													<label class="show-on-mob">First Name:</label>
													<input type="text" name="guardians_first_name" <?= $access_all_checkin == TRUE ? 'data-table="contacts_medical" data-id="'.$guardians['contactmedicalid'].'" data-id-field="contactmedicalid" data-concat="*#*" data-attach="'.$checkin['item_id'].'" data-attach-field="contactid"' : 'readonly' ?> class="form-control" value="<?= $first_name ?>">
												</div>
												<div class="col-sm-3">
													<label class="show-on-mob">Last Name:</label>
													<input type="text" name="guardians_last_name" <?= $access_all_checkin == TRUE ? 'data-table="contacts_medical" data-id="'.$guardians['contactmedicalid'].'" data-id-field="contactmedicalid" data-concat="*#*" data-attach="'.$checkin['item_id'].'" data-attach-field="contactid"' : 'readonly' ?> class="form-control" value="<?= $guardians_last_name[$i] ?>">
												</div>
												<div class="col-sm-3">
													<label class="show-on-mob"><?= $contact_number_label ?>:</label>
													<input type="text" name="<?= $contact_number_type ?>" <?= $access_all_checkin == TRUE ? 'data-table="contacts_medical" data-id="'.$guardians['contactmedicalid'].'" data-id-field="contactmedicalid" data-concat="*#*" data-attach="'.$checkin['item_id'].'" data-attach-field="contactid"' : 'readonly' ?> class="form-control" value="<?= $$contact_number_type[$i] ?>">
												</div>
												<div class="col-sm-2">
													<label class="show-on-mob">Confirmed:</label>
													<div <?= $access_all_checkin == TRUE ? 'class="toggleSwitch mobile-lg"' : '' ?>>
														<input type="hidden" name="notes" data-table="ticket_attached" data-id="<?= $checkin['id'] ?>" data-id-field="id" data-concat="," value="<?= $guardian_status[$i] ?>">
														<span style="<?= $guardian_status[$i] > 0 ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg inline-img"> Unconfirmed</span>
														<span style="<?= $guardian_status[$i] > 0 ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg inline-img"> Confirmed</span>
													</div>
												</div>
												<?php if($access_all_checkin == TRUE) { ?>
													<div class="col-sm-1">
														<input type="hidden" name="deleted" value="<?= $guardian_status[$i] ?>" onchange="$(this).closest('.multi-block').find('[name=notes]').val(this.value == 1 ? 2 : 0).change(); return false;">
														<button class="btn brand-btn pull-right" onclick="return false;">Save</button>
														<img class="inline-img pull-right" data-history-label="Guardian" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
														<img class="inline-img pull-right" data-history-label="Guardian" onclick="remMulti(this);" src="../img/remove.png">
													</div>
												<?php } ?>
											</div>
										<?php }
										if($visible_count == 0) { ?>
											<div class="multi-block">
												<div class="col-sm-3">
													<label class="show-on-mob">First Name:</label>
													<input type="text" name="guardians_first_name" <?= $access_all_checkin == TRUE ? 'data-table="contacts_medical" data-id="'.$guardians['contactmedicalid'].'" data-id-field="contactmedicalid" data-concat="*#*" data-attach="'.$checkin['item_id'].'" data-attach-field="contactid"' : 'readonly' ?> class="form-control">
												</div>
												<div class="col-sm-3">
													<label class="show-on-mob">Last Name:</label>
													<input type="text" name="guardians_last_name" <?= $access_all_checkin == TRUE ? 'data-table="contacts_medical" data-id="'.$guardians['contactmedicalid'].'" data-id-field="contactmedicalid" data-concat="*#*" data-attach="'.$checkin['item_id'].'" data-attach-field="contactid"' : 'readonly' ?> class="form-control">
												</div>
												<div class="col-sm-3">
													<label class="show-on-mob">Cell Phone:</label>
													<input type="text" name="guardians_cell_phone" <?= $access_all_checkin == TRUE ? 'data-table="contacts_medical" data-id="'.$guardians['contactmedicalid'].'" data-id-field="contactmedicalid" data-concat="*#*" data-attach="'.$checkin['item_id'].'" data-attach-field="contactid"' : 'readonly' ?> class="form-control">
												</div>
												<div class="col-sm-2">
													<label class="show-on-mob">Confirmed:</label>
													<div <?= $access_all_checkin == TRUE ? 'class="toggleSwitch mobile-lg"' : '' ?>>
														<input type="hidden" name="notes" data-table="ticket_attached" data-id="<?= $checkin['id'] ?>" data-id-field="id" data-concat="," value="0">
														<span style="<?= $guardian_status[$i] > 0 ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg inline-img"> Unconfirmed</span>
														<span style="<?= $guardian_status[$i] > 0 ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg inline-img"> Confirmed</span>
													</div>
												</div>
												<?php if($access_all_checkin == TRUE) { ?>
													<div class="col-sm-1">
														<input type="hidden" name="deleted" value="0" onchange="$(this).val($(this).val() + 1).change(); $(this).closest('.multi-block').find('[name=notes]').val(this.value).change();">
														<button class="btn brand-btn pull-right" onclick="return false;">Save</button>
														<img class="inline-img pull-right" data-history-label="Guardian" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
														<img class="inline-img pull-right" data-history-label="Guardian" onclick="remMulti(this);" src="../img/remove.png">
													</div>
												<?php } ?>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Enter Member's Emergency Contact information here."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
										<a data-toggle="collapse" data-parent="#checkin<?= $checkin['id'] ?>" href="#collapse_emerg_checkin_<?=$checkin['id'] ?>">
											Emergency Contacts<span class="glyphicon glyphicon-plus"></span>
										</a>
									</h4>
								</div>

								<div id="collapse_emerg_checkin_<?=$checkin['id'] ?>" class="panel-collapse collapse">
									<div class="panel-body">
										<div class="hide-titles-mob">
											<label class="col-sm-3">First Name</label>
											<label class="col-sm-3">Last Name</label>
											<label class="col-sm-3">Phone Number</label>
											<label class="col-sm-2">Relationship</label>
										</div>
										<?php $show_hide = explode(',',$checkin['description']);
										$emerg_contact = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts_medical` WHERE `contactid`='{$checkin['item_id']}'"));
										$emergency_first_name = explode('*#*',$emerg_contact['emergency_first_name']);
										$emergency_last_name = explode('*#*',$emerg_contact['emergency_last_name']);
										$emergency_contact_number = explode('*#*',$emerg_contact['emergency_contact_number']);
										$emergency_relationship = explode('*#*',$emerg_contact['emergency_relationship']);
										foreach($emergency_first_name as $i => $first_name) { ?>
											<div class="multi-block" style="<?= $show_hide[$i] > 0 ? 'display:none;' : '' ?>">
												<div class="col-sm-3">
													<label class="show-on-mob">First Name:</label>
													<input type="text" name="emergency_first_name" <?= $access_all_checkin == TRUE ? 'data-table="contacts_medical" data-id="'.$emerg_contact['contactmedicalid'].'" data-id-field="contactmedicalid" data-concat="*#*" data-attach="'.$checkin['item_id'].'" data-attach-field="contactid"' : 'readonly' ?> class="form-control" value="<?= $first_name ?>">
												</div>
												<div class="col-sm-3">
													<label class="show-on-mob">Last Name:</label>
													<input type="text" name="emergency_last_name" <?= $access_all_checkin == TRUE ? 'data-table="contacts_medical" data-id="'.$emerg_contact['contactmedicalid'].'" data-id-field="contactmedicalid" data-concat="*#*" data-attach="'.$checkin['item_id'].'" data-attach-field="contactid"' : 'readonly' ?> class="form-control" value="<?= $emergency_last_name[$i] ?>">
												</div>
												<div class="col-sm-3">
													<label class="show-on-mob">Contact Number:</label>
													<input type="text" name="emergency_contact_number" <?= $access_all_checkin == TRUE ? 'data-table="contacts_medical" data-id="'.$emerg_contact['contactmedicalid'].'" data-id-field="contactmedicalid" data-concat="*#*" data-attach="'.$checkin['item_id'].'" data-attach-field="contactid"' : 'readonly' ?> class="form-control" value="<?= $emergency_contact_number[$i] ?>">
												</div>
												<div class="col-sm-2">
													<label class="show-on-mob">Relationship:</label>
													<input type="text" name="emergency_relationship" <?= $access_all_checkin == TRUE ? 'data-table="contacts_medical" data-id="'.$emerg_contact['contactmedicalid'].'" data-id-field="contactmedicalid" data-concat="*#*" data-attach="'.$checkin['item_id'].'" data-attach-field="contactid"' : 'readonly' ?> class="form-control" value="<?= $emergency_relationship[$i] ?>">
												</div>
												<?php if($access_all_checkin == TRUE) { ?>
													<div class="col-sm-1">
														<input type="hidden" name="description" data-table="ticket_attached" data-id="<?= $checkin['id'] ?>" data-id-field="id" data-concat="," value="<?= $show_hide[$i] ?>">
														<input type="hidden" name="deleted" value="<?= $show_hide[$i] ?>" onchange="$(this).closest('div').find('[name=description]').val(this.value).change();">
														<button class="btn brand-btn pull-right" onclick="return false;">Save</button>
														<img class="inline-img pull-right" data-history-label="Emergency Contacts" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
														<img class="inline-img pull-right" data-history-label="Emergency Contacts" onclick="remMulti(this);" src="../img/remove.png">
													</div>
												<?php } ?>
											</div>
										<?php }
										if(array_sum($show_hide) >= count($emergency_first_name)) { ?>
											<div class="multi-block">
												<div class="col-sm-3">
													<label class="show-on-mob">First Name:</label>
													<input type="text" name="emergency_first_name" <?= $access_all_checkin == TRUE ? 'data-table="contacts_medical" data-id="'.$emerg_contact['contactmedicalid'].'" data-id-field="contactmedicalid" data-concat="*#*" data-attach="'.$checkin['item_id'].'" data-attach-field="contactid"' : 'readonly' ?> class="form-control" value="">
												</div>
												<div class="col-sm-3">
													<label class="show-on-mob">Last Name:</label>
													<input type="text" name="emergency_last_name" <?= $access_all_checkin == TRUE ? 'data-table="contacts_medical" data-id="'.$emerg_contact['contactmedicalid'].'" data-id-field="contactmedicalid" data-concat="*#*" data-attach="'.$checkin['item_id'].'" data-attach-field="contactid"' : 'readonly' ?> class="form-control" value="">
												</div>
												<div class="col-sm-3">
													<label class="show-on-mob">Contact Number:</label>
													<input type="text" name="emergency_contact_number" <?= $access_all_checkin == TRUE ? 'data-table="contacts_medical" data-id="'.$emerg_contact['contactmedicalid'].'" data-id-field="contactmedicalid" data-concat="*#*" data-attach="'.$checkin['item_id'].'" data-attach-field="contactid"' : 'readonly' ?> class="form-control" value="">
												</div>
												<div class="col-sm-2">
													<label class="show-on-mob">Relationship:</label>
													<input type="text" name="emergency_relationship" <?= $access_all_checkin == TRUE ? 'data-table="contacts_medical" data-id="'.$emerg_contact['contactmedicalid'].'" data-id-field="contactmedicalid" data-concat="*#*" data-attach="'.$checkin['item_id'].'" data-attach-field="contactid"' : 'readonly' ?> class="form-control" value="">
												</div>
												<?php if($access_all_checkin == TRUE) { ?>
													<div class="col-sm-1">
														<input type="hidden" name="description" data-table="ticket_attached" data-id="<?= $checkin['id'] ?>" data-id-field="id" data-concat="," value="0">
														<input type="hidden" name="deleted" value="0" onchange="$(this).closest('div').find('[name=description]').val(this.value).change();">
														<button class="btn brand-btn pull-right" onclick="return false;">Save</button>
														<img class="inline-img pull-right" data-history-label="Emergency Contacts" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
														<img class="inline-img pull-right" data-history-label="Emergency Contacts" onclick="remMulti(this);" src="../img/remove.png">
													</div>
												<?php } ?>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<hr class="visible-xs">
			<?php } else if(in_array($checkin['src_table'],['Staff','Staff_Tasks','Delivery','Clients']) && strpos($value_config, ',Checkin '.$checkin['src_table'].',') !== FALSE) { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?= get_contact($dbc, $checkin['item_id']).($checkin['position'] != '' ? ' - '.$checkin['position'] : '') ?>:</label>
					<div class="col-sm-8 <?= ($checkin['src_table'] == 'Staff' && $access_staff_checkin == TRUE) || ($checkin['src_table'] != 'Staff' && $access_all_checkin == TRUE) ? 'toggleSwitch mobile-lg' : '' ?>">
						<input type="hidden" name="checkin_id[]" value="<?= $checkin['item_id'] ?>">
						<input type="hidden" name="arrived" data-table="ticket_attached" data-id="<?= $checkin['id'] ?>" data-id-field="id" value="<?= $checkin['arrived'] ?>" class="toggle" <?= strpos($value_config,',Check Out Before Check In,') !== FALSE ? 'data-no-toggle-off="1"' : '' ?>>
						<span style="<?= $checkin['arrived'] > 0 && (!empty($checkin['timer_start']) || strpos($value_config,',Check Out Before Check In,') === FALSE) ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg inline-img on_img"> Click to Check In</span>
						<span style="<?= $checkin['arrived'] > 0 && (!empty($checkin['timer_start']) || strpos($value_config,',Check Out Before Check In,') === FALSE) ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg inline-img off_img"> Checked In</span>
					</div>
				</div>
				<hr class="visible-xs">
				<?php $pdf_contents[] = [$checkin['src_table'], get_contact($dbc, $checkin['item_id']).' - '.($checkin['arrived'] > 0 ? 'Checked In' : 'Not Checked In')]; ?>
			<?php } else if(in_array($checkin['src_table'],['material']) && strpos($value_config, ',Checkin '.$checkin['src_table'].',') !== FALSE) {
				$material = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `material` WHERE `materialid`='{$checkin['item_id']}'")); ?>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?= $material['category'].': '.$material['sub_category'].' '.$material['name'] ?>:</label>
					<div class="col-sm-8 <?= $access_all_checkin == TRUE ? 'toggleSwitch mobile-lg' : '' ?>">
						<input type="hidden" name="checkin_id[]" value="<?= $checkin['item_id'] ?>">
						<input type="hidden" name="arrived" data-table="ticket_attached" data-id="<?= $checkin['id'] ?>" data-id-field="id" value="<?= $checkin['arrived'] ?>" class="toggle" <?= strpos($value_config,',Check Out Before Check In,') !== FALSE ? 'data-no-toggle-off="1"' : '' ?>>
						<span style="<?= $checkin['arrived'] > 0 && (!empty($checkin['timer_start']) || strpos($value_config,',Check Out Before Check In,') === FALSE) ? 'display: none;' : '' ?>" onclick="$('[data-table]').off('click',cancelClick);"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg inline-img on_img"> Click to Check In</span>
						<span style="<?= $checkin['arrived'] > 0 && (!empty($checkin['timer_start']) || strpos($value_config,',Check Out Before Check In,') === FALSE) ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg inline-img off_img"> Checked In</span>
					</div>
				</div>
				<hr class="visible-xs">
				<?php $pdf_contents[] = ['Material', ($material['category'].': '.$material['sub_category'].' '.$material['name']).' - '.($checkin['arrived'] > 0 ? 'Checked In' : 'Not Checked In')]; ?>
			<?php } else if(in_array($checkin['src_table'],['equipment']) && strpos($value_config, ',Checkin '.$checkin['src_table'].',') !== FALSE) {
				$equipment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='{$checkin['item_id']}'")); ?>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?= $equipment['category'].': '.$equipment['make'].' '.$equipment['model'].' '.$equipment['label'].' '.$equipment['unit_number'] ?>:</label>
					<div class="col-sm-8 <?= $access_all_checkin == TRUE ? 'toggleSwitch mobile-lg' : '' ?>">
						<input type="hidden" name="checkin_id[]" value="<?= $checkin['item_id'] ?>">
						<input type="hidden" name="arrived" data-table="ticket_attached" data-id="<?= $checkin['id'] ?>" data-id-field="id" value="<?= $checkin['arrived'] ?>" class="toggle" <?= strpos($value_config,',Check Out Before Check In,') !== FALSE ? 'data-no-toggle-off="1"' : '' ?>>
						<span style="<?= $checkin['arrived'] > 0 && (!empty($checkin['timer_start']) || strpos($value_config,',Check Out Before Check In,') === FALSE) ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg inline-img on_img"> Click to Check In</span>
						<span style="<?= $checkin['arrived'] > 0 && (!empty($checkin['timer_start']) || strpos($value_config,',Check Out Before Check In,') === FALSE) ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg inline-img off_img"> Checked In</span>
					</div>
				</div>
				<hr class="visible-xs">
				<?php $pdf_contents[] = ['Equipment', ($equipment['category'].': '.$equipment['make'].' '.$equipment['model'].' '.$equipment['label'].' '.$equipment['unit_number']).' - '.($checkin['arrived'] > 0 ? 'Checked In' : 'Not Checked In')]; ?>
			<?php }
		}
	}
}
if(strpos($value_config, ',Checkin Get To Work,') !== FALSE && $access_all_checkin == TRUE) { ?>
	<a href="<?= WEBSITE_URL ?>/home.php" class="btn brand-btn pull-right">Get To Work</a>
	<div class="clearfix"></div>
<?php } ?>