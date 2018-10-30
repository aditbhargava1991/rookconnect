<?php error_reporting(0);
include_once('../include.php');
include_once('../Ticket/field_list.php');
$folder = FOLDER_NAME;
if(isset($_GET['folder'])) {
	$folder = filter_var($_GET['folder'], FILTER_SANITIZE_STRING);
	$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
}
if(basename($_SERVER['SCRIPT_FILENAME']) == 'add_ticket_summary.php') {
	ob_clean();
	$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'"));
	$ticket_roles = explode('#*#',get_config($dbc, 'ticket_roles'));
	$ticket_role = mysqli_query($dbc, "SELECT `position` FROM `ticket_attached` WHERE `src_table`='Staff' AND `position`!='' AND `item_id`='".$_SESSION['contactid']."' AND `ticketid`='$ticketid' AND `ticketid` > 0");
	if($get_ticket['to_do_date'] > date('Y-m-d') && strpos($value_config,',Ticket Edit Cutoff,') !== FALSE && $config_visible_function($dbc, 'ticket') < 1) {
		$access_complete = false;
	} else if($get_ticket['status'] == 'Archive') {
		$access_complete = 0;
	} else if(config_visible_function($dbc, 'ticket') > 0) {
		$access_complete = check_subtab_persmission($dbc, 'ticket', ROLE, 'complete');
	} else if((count($ticket_roles) > 1 || explode('|',$ticket_roles[0])[0] != '') && mysqli_num_rows($ticket_role) > 0) {
		$ticket_role = mysqli_fetch_assoc($ticket_role);
		foreach($ticket_roles as $ticket_role_level) {
			$ticket_role_level = explode('|',$ticket_role_level);
			if($ticket_role_level[0] > 0) {
				$ticket_role_level[0] = get_positions($dbc, $ticket_role_level[0], 'name');
			}
			if($ticket_role_level[0] == $ticket_role['position']) {
				$access_complete = in_array('complete',$ticket_role_level);
			}
		}
	} else if(count(array_filter($arr, function ($var) { return (strpos($var, 'default') !== false); })) > 0) {
		foreach($ticket_roles as $ticket_role_level) {
			$ticket_role_level = explode('|',$ticket_role_level);
			if(in_array('default',$ticket_role_level)) {
				$access_complete = in_array('complete',$ticket_role_level);
			}
		}
	} else {
		$access_complete = check_subtab_persmission($dbc, 'ticket', ROLE, 'complete');
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
	$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"));
	$sort_field = 'Summary';
	$renamed_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($get_ticket['ticket_type']) ? 'tickets' : 'tickets_'.$get_ticket['ticket_type'])."' AND `accordion` = '".$sort_field."'"))['accordion_name'];
}
if(!empty($_GET['date'])) {
	$current_date = filter_var($_GET['date'],FILTER_SANITIZE_STRING);
	$query_daily = " AND `date_stamp`='".$current_date."' "; ?>
    <input type="hidden" name="load_for_date" value="<?= $_GET['date'] ?>">
<?php }

$summary_hide_positions = get_config($dbc, 'ticket_summary_hide_positions');
if($get_ticket['ticket_type'] != '') {
	$summary_hide_positions .= '#*#'.get_config($dbc, 'ticket_summary_hide_positions_'.$get_ticket['ticket_type']);
}
$summary_hide_positions = array_filter(explode('#*#', $summary_hide_positions));

$hide_positions = '';
if(!empty($summary_hide_positions)) {
	$hide_positions = " AND IFNULL(`position`,'') NOT IN ('".implode("','", $summary_hide_positions)."')";
}
?>
<h3><?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : (strpos($value_config, ','."Staff Summary".',') !== FALSE ? 'Staff Summary' : 'Summary')) ?><?= !empty($_GET['date']) ? ' - '.$_GET['date'] : '' ?></h3>
<?php if($access_complete === TRUE) { ?>
	<?php if(strpos($value_config, ',Summary Times,') !== FALSE) { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Start Time On Site:</label>
			<div class="col-sm-8">
				<input type="text" name="start_time" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control datetimepicker" value="<?= $get_ticket['start_time'] ?>">
			</div>
			<label class="col-sm-4 control-label">End Time On Site:</label>
			<div class="col-sm-8">
				<input type="text" name="end_time" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control datetimepicker" value="<?= $get_ticket['end_time'] ?>">
			</div>
		</div>
	<?php }
	if(strpos($value_config,',Time Tracking Hours,') !== FALSE) { ?>
		<div class="form-group">
			<div class="col-sm-3 text-center hide-titles-mob">Staff</div>
			<div class="col-sm-2 text-center hide-titles-mob">Date</div>
			<div class="col-sm-1 text-center hide-titles-mob">Start Time</div>
			<div class="col-sm-1 text-center hide-titles-mob">End Time</div>
			<div class="col-sm-1 text-center hide-titles-mob">Time</div>
			<div class="col-sm-3 text-center hide-titles-mob">Task</div>
			<div class="clearfix"></div>
			<?php $summary_staff = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Staff','Staff_Tasks')".$query_daily);
			if($summary_staff->num_rows > 0) {
				while($summary = mysqli_fetch_array($summary_staff)) { ?>
					<div class="form-group summary multi-block">
						<div class="col-sm-3"><select name="item_id" data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="form-control chosen-select-deselect"><option></option>
							<?php foreach($staff_list as $staff) {
								echo "<option ".($staff['contactid'] == $summary['item_id'] ? 'selected' : '')." value='".$staff['contactid']."'>".$staff['first_name'].' '.$staff['last_name']."</option>";
							} ?></select>
						</div>
						<div class="col-sm-2"><input data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" type="text" name="date_stamp" value="<?= $summary['date_stamp'] ?>" class="form-control datepicker"></div>
						<div class="col-sm-1"><input data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" type="text" name="checked_in" value="<?= $summary['checked_in'] ?>" class="form-control datetimepicker"></div>
						<div class="col-sm-1"><input data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" type="text" name="checked_out" value="<?= $summary['checked_out'] ?>" class="form-control datetimepicker"></div>
						<div class="col-sm-1"><input data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" type="text" name="hours_tracked" value="<?= round($summary['hours_tracked'],2) ?>" class="form-control timepicker"></div>
						<div class="col-sm-3">
							<select name="position" data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="form-control chosen-select-deselect" data-row="<?= $j ?>"><option></option>
								<?php $main_tasks = explode(',',$get_ticket['task_available']);
								$project_tasks = mysqli_query($dbc, "SELECT `description`, `src_table`, `src_id` FROM `project_scope` WHERE `projectid`='".$get_ticket['projectid']."' AND (`description` != '' OR (`src_table`='services' AND `src_id` > 0))");
								while($project_task = mysqli_fetch_assoc($project_tasks)) {
									if($project_task['src_id'] > 0) {
										$main_tasks[] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading` FROM `services` WHERE `serviceid`='".$project_task['src_id']."'"))['heading'];
									} else {
										$main_tasks[] = $project_task['description'];
									}
								}
								$main_tasks = array_filter($main_tasks);
								$task_groups = get_config($dbc, 'ticket_'.$ticket_type.'_staff_tasks');
								if($task_groups == '') {
									$task_groups = get_config($dbc, 'ticket_ALL_staff_tasks');
								}
								if($task_groups == '') {
									$task_groups = get_config($dbc, 'site_work_order_tasks');
								}
								foreach(explode('#*#',$task_groups) as $task_group) {
									$task_group = explode('*#*',$task_group);
									echo "<optgroup label='".$task_group[0]." Tasks' />";
									unset($task_group[0]); ?>
									<?php foreach($task_group as $task_name) {
										if(count($main_tasks) == 0 || in_array($task_name,$main_tasks)) { ?>
											<option <?= ($summary['position'] == $task_name ? 'selected' : '') ?> value="<?= $task_name ?>"><?= $task_name ?></option>
										<?php } else if($summary['position'] == $task_name) { ?>
											<option <?= ($summary['position'] == $task_name ? 'selected' : '') ?> value="<?= $task_name ?>"><?= $task_name ?> (Extra Billing)</option>
										<?php } else { ?>
											<option disabled><?= $task_name ?></option>
										<?php }
									} ?>
								<?php } ?>
								<option></option>
							</select>
						</div>
						<div class="col-sm-1">
							<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
							<a href="" onclick="addMulti(this, 'inline'); return false;"><img class="inline-img pull-right" data-history-label="Staff Summary" src="../img/icons/ROOK-add-icon.png"></a>
							<a href="" onclick="remMulti(this); return false;"><img class="inline-img pull-right" data-history-label="Staff Summary" src="../img/remove.png"></a>
						</div>
					</div>
				<?php }
			} else { ?>
				<h4>No Staff Found</h4>
			<?php } ?>
			<div class="clearfix"></div>
		</div>
	<?php } else if(strpos($value_config,',Time Tracking Date,') !== FALSE && strpos($value_config,',Time Tracking Time,') !== FALSE) { ?>
		<div class="form-group">
			<div class="col-sm-2 text-center hide-titles-mob">Date</div>
			<div class="col-sm-3 text-center hide-titles-mob">Time</div>
			<div class="col-sm-3 text-center hide-titles-mob" style="<?= strpos($value_config,',Time Tasks,') === FALSE ? 'display:none;' : '' ?>">Task</div>
			<div class="col-sm-3 text-center hide-titles-mob">Staff</div>
			<div class="clearfix"></div>
			<?php $summary_staff = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Staff','Staff_Tasks')".$query_daily);
			while($summary = mysqli_fetch_array($summary_staff)) { ?>
				<div class="form-group summary">
					<div class="col-sm-2"><input data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" type="text" name="date_stamp" value="<?= $summary['date_stamp'] ?>" class="form-control datepicker"></div>
					<div class="col-sm-3"><?= $summary['checked_in'].' - '.$summary['checked_out'].': '.$summary['hours_tracked'] ?></div>
					<div class="col-sm-3" style="<?= strpos($value_config,',Time Tasks,') === FALSE ? 'display:none;' : '' ?>">
						<select name="position" data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="form-control chosen-select-deselect" data-row="<?= $j ?>"><option></option>
							<?php $main_tasks = explode(',',$get_ticket['task_available']);
							$project_tasks = mysqli_query($dbc, "SELECT `description`, `src_table`, `src_id` FROM `project_scope` WHERE `projectid`='".$get_ticket['projectid']."' AND (`description` != '' OR (`src_table`='services' AND `src_id` > 0))");
							while($project_task = mysqli_fetch_assoc($project_tasks)) {
								if($project_task['src_id'] > 0) {
									$main_tasks[] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading` FROM `services` WHERE `serviceid`='".$project_task['src_id']."'"))['heading'];
								} else {
									$main_tasks[] = $project_task['description'];
								}
							}
							$main_tasks = array_filter($main_tasks);
							$task_groups = get_config($dbc, 'ticket_'.$ticket_type.'_staff_tasks');
							if($task_groups == '') {
								$task_groups = get_config($dbc, 'ticket_ALL_staff_tasks');
							}
							if($task_groups == '') {
								$task_groups = get_config($dbc, 'site_work_order_tasks');
							}
							foreach(explode('#*#',$task_groups) as $task_group) {
								$task_group = explode('*#*',$task_group);
								echo "<optgroup label='".$task_group[0]." Tasks' />";
								unset($task_group[0]); ?>
								<?php foreach($task_group as $task_name) {
									if(count($main_tasks) == 0 || in_array($task_name,$main_tasks)) { ?>
										<option <?= ($summary['position'] == $task_name ? 'selected' : '') ?> value="<?= $task_name ?>"><?= $task_name ?></option>
									<?php } else if($summary['position'] == $task_name) { ?>
										<option <?= ($summary['position'] == $task_name ? 'selected' : '') ?> value="<?= $task_name ?>"><?= $task_name ?> (Extra Billing)</option>
									<?php } else { ?>
										<option disabled><?= $task_name ?></option>
									<?php }
								} ?>
							<?php } ?>
							<option></option>
						</select>
					</div>
					<div class="col-sm-3"><select name="item_id" data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="form-control chosen-select-deselect"><option></option>
						<?php foreach($staff_list as $staff) {
							echo "<option ".($staff['contactid'] == $summary['item_id'] ? 'selected' : '')." value='".$staff['contactid']."'>".$staff['first_name'].' '.$staff['last_name']."</option>";
						} ?></select>
					</div>
					<div class="col-sm-1">
						<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					</div>
				</div>
			<?php } ?>
			<div class="clearfix"></div>
		</div>
	<?php } else if(strpos($value_config,',Time Tracking Time,') !== FALSE) { ?>
		<div class="form-group">
			<div class="col-sm-3 text-center hide-titles-mob">Time</div>
			<div class="col-sm-2 text-center hide-titles-mob">Hours</div>
			<div class="col-sm-3 text-center hide-titles-mob" style="<?= strpos($value_config,',Time Tasks,') === FALSE ? 'display:none;' : '' ?>">Task</div>
			<div class="col-sm-3 text-center hide-titles-mob">Staff</div>
			<div class="clearfix"></div>
			<?php $summary_staff = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Staff','Staff_Tasks')".$query_daily);
			while($summary = mysqli_fetch_array($summary_staff)) { ?>
				<div class="form-group summary">
					<div class="col-sm-3"><?= $summary['checked_in'].' - '.$summary['checked_out'] ?></div>
					<div class="col-sm-2"><input data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" <?= !empty($summary['timer_start']) ? 'readonly' : '' ?> type="number" name="hours_tracked" value="<?= $summary['hours_tracked'] ?>" class="form-control" min="0" step="any"></div>
					<div class="col-sm-3" style="<?= strpos($value_config,',Time Tasks,') === FALSE ? 'display:none;' : '' ?>">
						<select name="position" data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="form-control chosen-select-deselect" data-row="<?= $j ?>"><option></option>
							<?php $main_tasks = explode(',',$get_ticket['task_available']);
							$project_tasks = mysqli_query($dbc, "SELECT `description`, `src_table`, `src_id` FROM `project_scope` WHERE `projectid`='".$get_ticket['projectid']."' AND (`description` != '' OR (`src_table`='services' AND `src_id` > 0))");
							while($project_task = mysqli_fetch_assoc($project_tasks)) {
								if($project_task['src_id'] > 0) {
									$main_tasks[] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading` FROM `services` WHERE `serviceid`='".$project_task['src_id']."'"))['heading'];
								} else {
									$main_tasks[] = $project_task['description'];
								}
							}
							$main_tasks = array_filter($main_tasks);
							$task_groups = get_config($dbc, 'ticket_'.$ticket_type.'_staff_tasks');
							if($task_groups == '') {
								$task_groups = get_config($dbc, 'ticket_ALL_staff_tasks');
							}
							if($task_groups == '') {
								$task_groups = get_config($dbc, 'site_work_order_tasks');
							}
							foreach(explode('#*#',$task_groups) as $task_group) {
								$task_group = explode('*#*',$task_group);
								echo "<optgroup label='".$task_group[0]." Tasks' />";
								unset($task_group[0]); ?>
								<?php foreach($task_group as $task_name) {
									if(count($main_tasks) == 0 || in_array($task_name,$main_tasks)) { ?>
										<option <?= ($summary['position'] == $task_name ? 'selected' : '') ?> value="<?= $task_name ?>"><?= $task_name ?></option>
									<?php } else if($summary['position'] == $task_name) { ?>
										<option <?= ($summary['position'] == $task_name ? 'selected' : '') ?> value="<?= $task_name ?>"><?= $task_name ?> (Extra Billing)</option>
									<?php } else { ?>
										<option disabled><?= $task_name ?></option>
									<?php }
								} ?>
							<?php } ?>
							<option></option>
						</select>
					</div>
					<div class="col-sm-3"><select name="item_id" data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="form-control chosen-select-deselect"><option></option>
						<?php foreach($staff_list as $staff) {
							echo "<option ".($staff['contactid'] == $summary['item_id'] ? 'selected' : '')." value='".$staff['contactid']."'>".$staff['first_name'].' '.$staff['last_name']."</option>";
						} ?></select>
					</div>
					<div class="col-sm-1">
						<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					</div>
				</div>
			<?php } ?>
			<div class="clearfix"></div>
		</div>
	<?php } else if(strpos($value_config,',Time Tracking Date,') !== FALSE) { ?>
		<div class="form-group">
			<div class="col-sm-2 text-center hide-titles-mob">Date</div>
			<div class="col-sm-3 text-center hide-titles-mob">Time</div>
			<div class="col-sm-3 text-center hide-titles-mob" style="<?= strpos($value_config,',Time Tasks,') === FALSE ? 'display:none;' : '' ?>">Task</div>
			<div class="col-sm-3 text-center hide-titles-mob">Staff</div>
			<div class="clearfix"></div>
			<?php $summary_staff = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Staff','Staff_Tasks')".$query_daily);
			while($summary = mysqli_fetch_array($summary_staff)) { ?>
				<div class="form-group summary">
					<div class="col-sm-2"><input data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" type="text" name="date_stamp" value="<?= $summary['date_stamp'] ?>" class="form-control datepicker"></div>
					<div class="col-sm-3"><input data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" <?= !empty($summary['timer_start']) ? 'readonly' : '' ?> type="number" name="hours_tracked" value="<?= $summary['hours_tracked'] ?>" class="form-control" min="0" step="any"></div>
					<div class="col-sm-3" style="<?= strpos($value_config,',Time Tasks,') === FALSE ? 'display:none;' : '' ?>">
						<select name="position" data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="form-control chosen-select-deselect" data-row="<?= $j ?>"><option></option>
							<?php $main_tasks = explode(',',$get_ticket['task_available']);
							$project_tasks = mysqli_query($dbc, "SELECT `description`, `src_table`, `src_id` FROM `project_scope` WHERE `projectid`='".$get_ticket['projectid']."' AND (`description` != '' OR (`src_table`='services' AND `src_id` > 0))");
							while($project_task = mysqli_fetch_assoc($project_tasks)) {
								if($project_task['src_id'] > 0) {
									$main_tasks[] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading` FROM `services` WHERE `serviceid`='".$project_task['src_id']."'"))['heading'];
								} else {
									$main_tasks[] = $project_task['description'];
								}
							}
							$main_tasks = array_filter($main_tasks);
							$task_groups = get_config($dbc, 'ticket_'.$ticket_type.'_staff_tasks');
							if($task_groups == '') {
								$task_groups = get_config($dbc, 'ticket_ALL_staff_tasks');
							}
							if($task_groups == '') {
								$task_groups = get_config($dbc, 'site_work_order_tasks');
							}
							foreach(explode('#*#',$task_groups) as $task_group) {
								$task_group = explode('*#*',$task_group);
								echo "<optgroup label='".$task_group[0]." Tasks' />";
								unset($task_group[0]); ?>
								<?php foreach($task_group as $task_name) {
									if(count($main_tasks) == 0 || in_array($task_name,$main_tasks)) { ?>
										<option <?= ($summary['position'] == $task_name ? 'selected' : '') ?> value="<?= $task_name ?>"><?= $task_name ?></option>
									<?php } else if($summary['position'] == $task_name) { ?>
										<option <?= ($summary['position'] == $task_name ? 'selected' : '') ?> value="<?= $task_name ?>"><?= $task_name ?> (Extra Billing)</option>
									<?php } else { ?>
										<option disabled><?= $task_name ?></option>
									<?php }
								} ?>
							<?php } ?>
							<option></option>
						</select>
					</div>
					<div class="col-sm-3"><select name="item_id" data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="form-control chosen-select-deselect"><option></option>
						<?php foreach($staff_list as $staff) {
							echo "<option ".($staff['contactid'] == $summary['item_id'] ? 'selected' : '')." value='".$staff['contactid']."'>".$staff['first_name'].' '.$staff['last_name']."</option>";
						} ?></select>
					</div>
					<div class="col-sm-1">
						<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					</div>
				</div>
			<?php } ?>
			<div class="clearfix"></div>
		</div>
	<?php } else if(strpos($value_config,',Time Tracking,')) { ?>
		<div class="form-group">
			<div class="col-sm-4 text-center">Name</div>
			<div class="col-sm-4 text-center" style="<?= strpos($value_config,',Time Tasks,') === FALSE ? 'display:none;' : '' ?>">Task</div>
			<div class="col-sm-3 text-center"><span class="popover-examples list-inline">
					<a href="" data-toggle="tooltip" data-placement="top" title="This is the time that has been saved. It does not include time currently being tracked. It cannot be edited while you are tracking time. In order to edit it, you will first need to stop the timer."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
				</span>Hours Tracked</div>
			<div class="clearfix"></div>
			<?php $summary_staff = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Staff','Staff_Tasks')".$query_daily);
			$summary = mysqli_fetch_array($summary_staff);
			do { ?>
				<div class="form-group summary">
					<div class="col-sm-4"><select name="item_id" data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="form-control chosen-select-deselect"><option></option>
						<?php foreach($staff_list as $staff) {
							echo "<option ".($staff['contactid'] == $summary['item_id'] ? 'selected' : '')." value='".$staff['contactid']."'>".$staff['first_name'].' '.$staff['last_name']."</option>";
						} ?></select></div>
					<div class="col-sm-4" style="<?= strpos($value_config,',Time Tasks,') === FALSE ? 'display:none;' : '' ?>">
						<?php if($summary['src_table'] != 'Staff_Tasks') { ?>
							<select name="position" data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="form-control chosen-select-deselect" data-row="<?= $j ?>"><option></option>
								<?php foreach(explode('#*#', $task_groups) as $task_group) {
										$task_group = explode('*#*', $task_group);
										unset($task_group[0]);
										foreach($task_group as $task) {
											if(count($main_tasks) == 0 || in_array($task,$main_tasks) || $task == $summary['position']) { ?>
												<option <?= $summary['position'] == $task ? 'selected' : '' ?> value="<?= $task ?>"><?= $task ?></option>
											<?php } else { ?>
												<option disabled><?= $task ?></option>
											<?php }
										}
									}
									foreach($task_list as $task_group) {
									$task_group = explode('*#*',$task_group);
									echo "<optgroup label='".$task_group[0]." Tasks' />";
									unset($task_group[0]); ?>
									<?php foreach($task_group as $task_name) { ?>
										<option <?= ($summary['position'] == $task_name ? 'selected' : '') ?> value="<?= $task_name ?>"><?= $task_name ?></option>
									<?php } ?>
								<?php } ?>
								<option></option>
							</select>
						<?php } else { ?>
							<input type="text" readonly name="position" data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="form-control" data-row="<?= $j ?>" value="<?= $summary['position'] ?>">
						<?php } ?>
					</div>
					<div class="col-sm-3"><input data-disabled="<?= $summary['hours_tracked'] > 0 ? 'true' : 'false' ?>" data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" <?= !empty($summary['timer_start']) ? 'readonly' : '' ?> type="number" name="hours_tracked" value="<?= $summary['hours_tracked'] ?>" class="form-control" min="0" step="any"></div>
					<div class="col-sm-1">
						<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
						<a href="" onclick="$(this).closest('.form-group.summary').remove(); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a>
					</div>
					<input type="hidden" name="summary_timer_start[]" value="<?= $summary['timer_start'] ?>">
					<input type="hidden" name="summary_disabled[]" value="<?= $summary['hours_tracked'] ?>">
				</div>
			<?php } while($summary = mysqli_fetch_array($summary_staff)); ?>
			<?php $members = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Members','Clients')".$query_daily);
			while($summary = mysqli_fetch_array($members)) { ?>
				<div class="form-group summary">
					<div class="col-sm-4"><?= get_contact($dbc, $summary['item_id']) ?></div>
					<div class="col-sm-4" style="<?= strpos($value_config,',Time Tasks,') === FALSE ? 'display:none;' : '' ?>">
						<input type="text" readonly name="position" readonly class="form-control" data-row="<?= $j ?>" value="<?= $summary['src_table'] ?>">
					</div>
					<div class="col-sm-3"><input data-disabled="<?= $summary['hours_tracked'] > 0 ? 'true' : 'false' ?>" readonly type="number" name="hours_tracked" value="<?= $summary['hours_tracked'] ?>" class="form-control"></div>
				</div>
			<?php } ?>
			<button class="btn brand-btn pull-right" id="summary_btn" onclick="add_staff_summary(); return false;">Add</button>
			<div class="clearfix"></div>
		</div>
	<?php } else if(strpos($value_config,',Time Tracking Set,')) { ?>
		<div class="form-group">
			<div class="col-sm-4 text-center hide-titles-mob">Name</div>
			<div class="col-sm-2 text-center hide-titles-mob">Hours</div>
			<div class="col-sm-6 text-center hide-titles-mob">Comment</div>
			<div class="clearfix"></div>
			<?php $summary_staff = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Staff','Staff_Tasks')".$query_daily);
			$summary = mysqli_fetch_array($summary_staff);
			do { ?>
				<div class="form-group summary">
					<div class="col-sm-4"><label class="show-on-mobile">Name:</label>
						<select name="item_id" data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="form-control chosen-select-deselect"><option></option>
						<?php foreach($staff_list as $staff) {
							echo "<option ".($staff['contactid'] == $summary['item_id'] ? 'selected' : '')." value='".$staff['contactid']."'>".$staff['first_name'].' '.$staff['last_name']."</option>";
						} ?></select>
						<label class="form-checkbox"><input type="checkbox" name="discrepancy" <?= $summary['discrepancy'] == 1 ? 'checked' : '' ?> data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" value="1">Do Not Require Notes</label>
						<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					</div>
					<div class="col-sm-2"><label class="show-on-mobile">Hours:</label><input data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" <?= strpos($value_config,',Time Tracking Edit Past Date') !== FALSE && $get_ticket['to_do_date'] != '' ? 'data-date="'.$get_ticket['to_do_date'].'"' : '' ?> type="number" name="time_set" value="<?= $summary['hours_set'] ?>" class="form-control" min="0" step="any"></div>
					<div class="col-sm-6">
						<label class="show-on-mobile">Comment:</label><input data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" type="text" name="time_comment" value="" class="form-control">
						<?php $time_comments = mysqli_query($dbc, "SELECT `comment_box` FROM `time_cards` WHERE `staff`='{$summary['item_id']}' AND `ticketid`='{$summary['ticketid']}' AND `deleted`=0 AND IFNULL(`comment_box`,'') != ''".$query_daily);
						while($time_comment = mysqli_fetch_assoc($time_comments)) {
							echo $time_comment['comment_box']."<br />";
						} ?>
					</div>
				</div>
			<?php } while($summary = mysqli_fetch_array($summary_staff)); ?>
			<?php $members = mysqli_query($dbc, "SELECT `ticket_attached`.*, `time_cards`.`comment_box` FROM `ticket_attached` LEFT JOIN `time_cards` ON `ticket_attached`.`ticketid`=`time_cards`.`ticketid` WHERE `ticket_attached`.`tile_name`='".FOLDER_NAME."' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticketid` > 0 AND `ticket_attached`.`deleted`=0 AND `ticket_attached`.`position`!='Team Lead' AND `ticket_attached`.`src_table` IN ('Members','Clients')".$query_daily);
			while($summary = mysqli_fetch_array($members)) { ?>
				<div class="form-group summary">
					<div class="col-sm-4"><label class="show-on-mobile">Name:</label><?= get_contact($dbc, $summary['item_id']) ?><a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a></div>
					<div class="col-sm-2"><label class="show-on-mobile">Hours:</label><input readonly type="number" name="hours_set" value="<?= $summary['hours_set'] ?>" class="form-control"></div>
					<div class="col-sm-6"><label class="show-on-mobile">Comment:</label><input type="text" data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" name="time_comment" value="" class="form-control"><?= $summary['comment_box'] ?></div>
				</div>
			<?php } ?>
			<button class="btn brand-btn pull-right" id="summary_btn" onclick="add_staff_summary(); return false;">Add</button>
			<div class="clearfix"></div>
		</div>
	<?php } else if(strpos($value_config,',Time Tracking Hrs,')) { ?>
		<div class="form-group">
			<div class="col-sm-4 text-center hide-titles-mob">Name</div>
			<div class="col-sm-2 text-center hide-titles-mob">Hours</div>
			<div class="col-sm-6 text-center hide-titles-mob">Comment</div>
			<div class="clearfix"></div>
			<?php $summary_staff = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Staff','Staff_Tasks')".$query_daily);
			$summary = mysqli_fetch_array($summary_staff);
			do { ?>
				<div class="form-group summary">
					<div class="col-sm-4"><label class="show-on-mobile">Name:</label>
						<select name="item_id" data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="form-control chosen-select-deselect"><option></option>
						<?php foreach($staff_list as $staff) {
							echo "<option ".($staff['contactid'] == $summary['item_id'] ? 'selected' : '')." value='".$staff['contactid']."'>".$staff['first_name'].' '.$staff['last_name']."</option>";
						} ?></select>
						<label class="form-checkbox"><input type="checkbox" name="discrepancy" <?= $summary['discrepancy'] == 1 ? 'checked' : '' ?> data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" value="1">Do Not Require Notes</label>
						<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					</div>
					<div class="col-sm-2"><label class="show-on-mobile">Hours:</label><input data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" <?= strpos($value_config,',Time Tracking Edit Past Date') !== FALSE && $get_ticket['to_do_date'] != '' ? 'data-date="'.$get_ticket['to_do_date'].'"' : '' ?> type="number" name="time_set" value="<?= $summary['hours_tracked'] ?>" class="form-control" min="0" step="any"></div>
					<div class="col-sm-6">
						<label class="show-on-mobile">Comment:</label><input data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" type="text" name="time_comment" value="" class="form-control">
						<?php $time_comments = mysqli_query($dbc, "SELECT `comment_box` FROM `time_cards` WHERE `staff`='{$summary['item_id']}' AND `ticketid`='{$summary['ticketid']}' AND `deleted`=0 AND IFNULL(`comment_box`,'') != ''".$query_daily);
						while($time_comment = mysqli_fetch_assoc($time_comments)) {
							echo $time_comment['comment_box']."<br />";
						} ?>
					</div>
				</div>
			<?php } while($summary = mysqli_fetch_array($summary_staff)); ?>
			<?php $members = mysqli_query($dbc, "SELECT `ticket_attached`.*, `time_cards`.`comment_box` FROM `ticket_attached` LEFT JOIN `time_cards` ON `ticket_attached`.`ticketid`=`time_cards`.`ticketid` WHERE `ticket_attached`.`tile_name`='".FOLDER_NAME."' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticketid` > 0 AND `ticket_attached`.`deleted`=0 AND `ticket_attached`.`position`!='Team Lead' AND `ticket_attached`.`src_table` IN ('Members','Clients')".$query_daily);
			while($summary = mysqli_fetch_array($members)) { ?>
				<div class="form-group summary">
					<div class="col-sm-4"><label class="show-on-mobile">Name:</label><?= get_contact($dbc, $summary['item_id']) ?><a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a></div>
					<div class="col-sm-2"><label class="show-on-mobile">Hours:</label><input readonly type="number" name="hours_tracked" value="<?= $summary['hours_tracked'] ?>" class="form-control"></div>
					<div class="col-sm-6"><label class="show-on-mobile">Comment:</label><input type="text" data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" name="time_comment" value="" class="form-control"><?= $summary['comment_box'] ?></div>
				</div>
			<?php } ?>
			<button class="btn brand-btn pull-right" id="summary_btn" onclick="add_staff_summary(); return false;">Add</button>
			<div class="clearfix"></div>
		</div>
	<?php }
	if(strpos($value_config,',Planned Tracked Payable Staff,')) { ?>
		<div class="form-group">
			<table id="no-more-tables" class="table table-bordered summary_table">
				<tr class='hide-titles-mob'>
					<th>Staff</th>
					<th>Planned Hours</th>
					<th>Tracked Hours</th>
					<th>Total Tracked Time</th>
					<th <?= check_subtab_persmission($dbc, 'ticket', ROLE, 'view_payable') ? '' : 'style="display:none;"' ?>>Payable Hours</th>
				</tr>
				<?php $summary_staff = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Staff','Staff_Tasks')".$query_daily);
				 while($summary = mysqli_fetch_array($summary_staff)) { ?>
					<tr class="summary">
						<td data-title="Staff"><?= get_contact($dbc, $summary['item_id']) ?>
						<br><label class="form-checkbox"><input type="checkbox" name="discrepancy" <?= $summary['discrepancy'] == 1 ? 'checked' : '' ?> data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" value="1">Do Not Require Notes</label></td>
						<td data-title="Planned Hours"><?= $get_ticket['start_time'].' - '.$get_ticket['end_time'] ?></td>
						<td data-title="Tracked Hours">
							<?php $tracked_hours = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_attached_checkin` WHERE `ticket_attached_id` = '".$summary['id']."'"),MYSQLI_ASSOC);
							if(!empty($tracked_hours)) {
								$tracked_html = [];
								foreach($tracked_hours as $tracked_hour) {
									$tracked_html[] = $tracked_hour['checked_in'].' - '.$tracked_hour['checked_out'];
								}
								$tracked_html = implode('<br />',$tracked_html);
							} else {
								$tracked_html = $summary['checked_in'].' - '.$summary['checked_out'];
							}
							echo $tracked_html;
							?>
						</td>
						<td data-title="Total Tracked Time">
							<?php $tracked_time = '-';

							if(!empty($tracked_hours)) {
								$tracked_time = 0;
								foreach($tracked_hours as $tracked_hour) {
									$tracked_time += number_format((strtotime(date('Y-m-d').' '.$tracked_hour['checked_out']) - strtotime(date('Y-m-d').' '.$tracked_hour['checked_in']))/3600,2);
								}
							} else if(!empty($summary['checked_out']) && !empty($summary['checked_in'])) {
								$tracked_time = number_format((strtotime(date('Y-m-d').' '.$summary['checked_out']) - strtotime(date('Y-m-d').' '.$summary['checked_in']))/3600,2);
							} else if($summary['hours_tracked'] > 0) {
								$tracked_time = number_format($summary['hours_tracked'],2);
							}
							echo $tracked_time; ?>
						</td>
						<td data-title="Payable Hours" <?= check_subtab_persmission($dbc, 'ticket', ROLE, 'view_payable') ? '' : 'style="display:none;"' ?>>
							<input data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" <?= strpos($value_config,',Time Tracking Edit Past Date') !== FALSE && $get_ticket['to_do_date'] != '' ? 'data-date="'.$get_ticket['to_do_date'].'"' : '' ?> type="number" name="time_set" value="<?= $summary['hours_set'] ?>" class="form-control" min="0" step="any">
						</td>
					</tr>
				<?php } ?>
			</table>
		</div>
	<?php }
	if(strpos($value_config,',Planned Tracked Payable Staff Multiple Times,')) { ?>
		<div class="form-group">
			<table id="no-more-tables" class="table table-bordered summary_table">
				<tr class='hide-titles-mob'>
					<th>Staff</th>
					<th>Planned Hours</th>
					<th>Tracked Hours</th>
					<th>Total Tracked Time</th>
					<th <?= check_subtab_persmission($dbc, 'ticket', ROLE, 'view_payable') ? '' : 'style="display:none;"' ?>>Payable Hours</th>
				</tr>
				<?php $summary_staff = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Staff','Staff_Tasks')".$query_daily);
				 while($summary = mysqli_fetch_array($summary_staff)) {
					 $staff_times = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `src_table` = 'Multiple_Timesheet_Row' AND `item_id` = '".$summary['id']."' AND '".$summary['id']."' > 0 AND `deleted` = 0"),MYSQLI_ASSOC); ?>
					<tr class="summary">
						<td data-title="Staff"><?= get_contact($dbc, $summary['item_id']) ?>
						<br><label class="form-checkbox"><input type="checkbox" name="discrepancy" <?= $summary['discrepancy'] == 1 ? 'checked' : '' ?> data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" value="1">Do Not Require Notes</label></td>
						<td data-title="Planned Hours">
							<?php $planned_html = [];
							foreach($staff_times as $staff_time) {
								$planned_html[] = $staff_time['date_stamp'].' '.$staff_time['start_time'].' - '.$staff_time['end_time'];
							}
							$planned_html = implode('<br />',$planned_html);
							echo $planned_html;
							?>
						</td>
						<td data-title="Tracked Hours">
							<?php $tracked_hours = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_attached_checkin` WHERE `ticket_attached_id` = '".$summary['id']."'"),MYSQLI_ASSOC);
							if(!empty($tracked_hours)) {
								$tracked_html = [];
								foreach($tracked_hours as $tracked_hour) {
									$tracked_html[] = (!empty(str_replace('0000-00-00','',$tracked_hour['checked_in_date'])) ? $tracked_hour['checked_in_date'].' ' : '').$tracked_hour['checked_in'].' - '.(!empty(str_replace('0000-00-00','',$tracked_hour['checked_out_date'])) ? $tracked_hour['checked_out_date'].' ' : '').$tracked_hour['checked_out'];
								}
								$tracked_html = implode('<br />',$tracked_html);
							} else {
								$tracked_html = $summary['checked_in'].' - '.$summary['checked_out'];
							}
							echo $tracked_html;
							?>
						</td>
						<td data-title="Total Tracked Time">
							<?php $tracked_time = '-';

							if(!empty($tracked_hours)) {
								$tracked_time = 0;
								foreach($tracked_hours as $tracked_hour) {
									$tracked_time += number_format((strtotime((!empty(str_replace('0000-00-00','',$tracked_hour['checked_out_date'])) ? $tracked_hour['checked_out_date'] : date('Y-m-d')).' '.$tracked_hour['checked_out']) - strtotime((!empty(str_replace('0000-00-00','',$tracked_hour['checked_in_date'])) ? $tracked_hour['checked_in_date'] : date('Y-m-d')).' '.$tracked_hour['checked_in']))/3600,2);
								}
							} else if(!empty($summary['checked_out']) && !empty($summary['checked_in'])) {
								$tracked_time = number_format((strtotime(date('Y-m-d').' '.$summary['checked_out']) - strtotime(date('Y-m-d').' '.$summary['checked_in']))/3600,2);
							} else if($summary['hours_tracked'] > 0) {
								$tracked_time = number_format($summary['hours_tracked'],2);
							}
							echo $tracked_time; ?>
						</td>
						<td data-title="Payable Hours" <?= check_subtab_persmission($dbc, 'ticket', ROLE, 'view_payable') ? '' : 'style="display:none;"' ?>>
							<?php foreach($staff_times as $staff_time) { ?>
								<input type="number" min=0 step="<?= $hour_increment ?>" name="hours_set" data-table="ticket_attached" data-id="<?= $staff_time['id'] ?>" data-id-field="id" data-type="Multiple_Timesheet_Row" data-type-field="src_table" class="form-control" value="<?= $staff_time['hours_set'] ?>">
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
			</table>
		</div>
	<?php }
	if(strpos($value_config,',Planned Tracked Payable Members,')) { ?>
		<div class="form-group">
			<table id="no-more-tables" class="table table-bordered summary_table">
				<tr class='hide-titles-mob'>
					<th>Member</th>
					<th>Planned Hours</th>
					<th>Tracked Hours</th>
					<th>Total Tracked Time</th>
					<th <?= check_subtab_persmission($dbc, 'ticket', ROLE, 'view_payable') ? '' : 'style="display:none;"' ?>>Payable Hours</th>
				</tr>
				<?php $summary_members = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Members')".$query_daily);
				 while($summary = mysqli_fetch_array($summary_members)) { ?>
					<tr class="summary">
						<td data-title="Member"><?= get_contact($dbc, $summary['item_id']) ?></td>
						<td data-title="Planned Hours"><?= $get_ticket['member_start_time'].' - '.$get_ticket['member_end_time'] ?></td>
						<td data-title="Tracked Hours">
							<?php $tracked_hours = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_attached_checkin` WHERE `ticket_attached_id` = '".$summary['id']."'"),MYSQLI_ASSOC);
							if(!empty($tracked_hours)) {
								$tracked_html = [];
								foreach($tracked_hours as $tracked_hour) {
									$tracked_html[] = $tracked_hour['checked_in'].' - '.$tracked_hour['checked_out'];
								}
								$tracked_html = implode('<br />',$tracked_html);
							} else {
								$tracked_html = $summary['checked_in'].' - '.$summary['checked_out'];
							}
							echo $tracked_html;
							?>
						</td>
						<td data-title="Total Tracked Time">
							<?php $tracked_time = '-';

							if(!empty($tracked_hours)) {
								$tracked_time = 0;
								foreach($tracked_hours as $tracked_hour) {
									$tracked_time += number_format((strtotime(date('Y-m-d').' '.$tracked_hour['checked_out']) - strtotime(date('Y-m-d').' '.$tracked_hour['checked_in']))/3600,2);
								}
							} else if(!empty($summary['checked_out']) && !empty($summary['checked_in'])) {
								$tracked_time = number_format((strtotime(date('Y-m-d').' '.$summary['checked_out']) - strtotime(date('Y-m-d').' '.$summary['checked_in']))/3600,2);
							} else if($summary['hours_tracked'] > 0) {
								$tracked_time = number_format($summary['hours_tracked'],2);
							}
							echo $tracked_time; ?>
						</td>
						<td data-title="Payable Hours" <?= check_subtab_persmission($dbc, 'ticket', ROLE, 'view_payable') ? '' : 'style="display:none;"' ?>>
							<input data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Members" data-type-field="src_table" <?= strpos($value_config,',Time Tracking Edit Past Date') !== FALSE && $get_ticket['to_do_date'] != '' ? 'data-date="'.$get_ticket['to_do_date'].'"' : '' ?> type="number" name="time_set" value="<?= $summary['hours_set'] ?>" class="form-control" min="0" step="any">
						</td>
					</tr>
				<?php } ?>
			</table>
		</div>
	<?php }
	if(strpos($value_config,',Total Time Tracked')) { ?>
		<?php $staff_total = mysqli_fetch_array(mysqli_query($dbc, "SELECT GREATEST(SUM(`hours_tracked`), SUM(`hours_set`)) as total_hours_tracked FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Staff','Staff_Tasks')".$query_daily))['total_hours_tracked'];
		$members_total = mysqli_fetch_array(mysqli_query($dbc, "SELECT GREATEST(SUM(`hours_tracked`), SUM(`hours_set`)) as total_hours_tracked FROM `ticket_attached` WHERE `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Members')".$query_daily))['total_hours_tracked'];
		$clients_total = mysqli_fetch_array(mysqli_query($dbc, "SELECT GREATEST(SUM(`hours_tracked`), SUM(`hours_set`)) as total_hours_tracked FROM `ticket_attached` WHERE `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Clients')".$query_daily))['total_hours_tracked']; ?>
		<div class="form-group">
			<table id="no-more-tables" class='table table-bordered'>
				<tr class='hide-titles-mob'>
					<th>Tab</th>
					<th>Total Hours Tracked</th>
				</tr>
				<?php if(strpos($value_config,',Total Time Tracked Staff,')) { ?>
					<tr>
						<td data-title="Type">Staff</td>
						<td data-title="Hours Tracked"><?= !empty($staff_total) ? $staff_total : 0 ?></td>
					</tr>
				<?php } ?>
				<?php if(strpos($value_config,',Total Time Tracked Members,')) { ?>
					<tr>
						<td data-title="Type">Members</td>
						<td data-title="Hours Tracked"><?= !empty($members_total) ? $members_total : 0 ?></td>
					</tr>
				<?php } ?>
				<?php if(strpos($value_config,',Total Time Tracked Clients,')) { ?>
					<tr>
						<td data-title="Type"><?= $client_accordion_category ?></td>
						<td data-title="Hours Tracked"><?= !empty($clients_total) ? $clients_total : 0 ?></td>
					</tr>
				<?php } ?>
			</table>
		</div>
	<?php }
	if(strpos($value_config, ',Summary Materials Summary,')) { ?>
		<h4>Materials Summary</h4>
		<div class="form-group">
			<?php $summary_materials = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE (`ticket_attached`.`item_id` > 0 OR `description` != '') AND `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 AND `src_table`='material'".$query_daily);
			if($summary_materials->num_rows > 0) { ?>
				<div class="col-sm-4 text-center"><label>Material</label></div>
				<div class="col-sm-4 text-center"><label>Quantity</label></div>
				<div class="col-sm-4 text-center"><label>Used</label></div>
				<div class="clearfix"></div>
				<?php while($summary = mysqli_fetch_array($summary_materials)) {
                    if($summary['item_id'] > 0) {
                        $material = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `material` where `materialid` = '".$summary['item_id']."'"));
                    } else {
                        $material = ['name' => $summary['description']];
                    } ?>
					<div class="form-group summary">
						<div class="col-sm-4 text-center"><?= (!empty($material['category']) ? $material['category'].': ' : '').$material['sub_category'].' ',$material['name'] ?></div>
						<div class="col-sm-4 text-center"><?= $summary['qty'] ?></div>
						<div class="col-sm-4 text-center"><?= $summary['completed'] == 1 ? 'Yes' : 'No' ?></div>
						<div class="clearfix"></div>
					</div>
				<?php } ?>
			<?php } else { ?>
				<h4>No Materials Found</h4>
			<?php } ?>
		</div>
	<?php }
	if(strpos($value_config, ',Summary Times,') !== FALSE) { ?>
		<div class="form-group clearfix">
			<label class="col-sm-4 control-label">Total Time On Site:</label>
			<div class="col-sm-8">
				<input type="text" name="total_time_on_site" class="form-control timepicker" value="">
			</div>
		</div>
	<?php } ?>
	<?php if(strpos($value_config, ',Complete,') === FALSE && empty($_GET['date'])) { ?>
		<div class="form-group">
			<label class="col-sm-4">Signature:</label>
			<div class="col-sm-8">
				<?php $output_name = 'summary_signature';
				include('../phpsign/sign_multiple.php'); ?>
			</div>
		</div>
	<?php }
} else {
	if(strpos($value_config, ',Summary Times,') !== FALSE) { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Start Time On Site:</label>
			<div class="col-sm-8">
				<input type="text" name="start_time" readonly class="form-control" value="<?= $get_ticket['start_time'] ?>">
			</div>
			<?php $pdf_contents[] = ['Start Time On Site', $get_ticket['start_time']]; ?>
			<label class="col-sm-4 control-label">End Time On Site:</label>
			<div class="col-sm-8">
				<input type="text" name="end_time" readonly class="form-control" value="<?= $get_ticket['end_time'] ?>">
			</div>
			<?php $pdf_contents[] = ['End Time On Site', $get_ticket['end_time']]; ?>
		</div>
	<?php }
	if(strpos($value_config,',Time Tracking,')) { ?>
		<div class="form-group">
			<div class="col-sm-4 text-center hide-titles-mob">Name</div>
			<div class="col-sm-4 text-center hide-titles-mob" style="<?= strpos($value_config,',Time Tasks,') === FALSE ? 'display:none;' : '' ?>">Task</div>
			<div class="col-sm-3 text-center hide-titles-mob"><span class="popover-examples list-inline">
					<a href="" data-toggle="tooltip" data-placement="top" title="This is the time that has been saved. It does not include time currently being tracked. It cannot be edited while you are tracking time. In order to edit it, you will first need to stop the timer."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
				</span>Hours Tracked</div>
			<div class="clearfix"></div>
			<?php $staff = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Staff','Staff_Tasks')".$query_daily);
			while($summary = mysqli_fetch_array($staff)) { ?>
				<div class="form-group summary">
					<div class="col-sm-4"><?= get_contact($dbc, $summary['item_id']) ?></div>
					<div class="col-sm-4" style="<?= strpos($value_config,',Time Tasks,') === FALSE ? 'display:none;' : '' ?>">
						<input type="text" readonly name="position" readonly class="form-control" data-row="<?= $j ?>" value="<?= $summary['position'] ?>">
					</div>
					<div class="col-sm-4"><input data-disabled="<?= $summary['hours_tracked'] > 0 ? 'true' : 'false' ?>" readonly type="number" name="hours_tracked" value="<?= $summary['hours_tracked'] ?>" class="form-control"></div>
				</div>
				<?php $pdf_content = '';
				$pdf_content .= 'Name: '.get_contact($dbc, $summary['item_id']).'<br>';
				if(strpos($value_config,',Time Tasks,') !== FALSE) {
					$pdf_content .= 'Task: '.$summary['position'].'<br>';
				}
				$pdf_content .= 'Hours Tracked: '.$summary['hours_tracked'];
				$pdf_contents[] = ['Staff', $pdf_content]; ?>
			<?php } ?>
			<?php $members = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Members','Clients')".$query_daily);
			while($summary = mysqli_fetch_array($members)) { ?>
				<div class="form-group summary">
					<div class="col-sm-4"><?= get_contact($dbc, $summary['item_id']) ?></div>
					<div class="col-sm-4" style="<?= strpos($value_config,',Time Tasks,') === FALSE ? 'display:none;' : '' ?>">
						<input type="text" readonly name="position" readonly class="form-control" data-row="<?= $j ?>" value="<?= $summary['src_table'] ?>">
					</div>
					<div class="col-sm-4"><input data-disabled="<?= $summary['hours_tracked'] > 0 ? 'true' : 'false' ?>" readonly type="number" name="hours_tracked" value="<?= $summary['hours_tracked'] ?>" class="form-control"></div>
				</div>
				<?php $pdf_content = '';
				$pdf_content .= 'Name: '.get_contact($dbc, $summary['item_id']).'<br>';
				$pdf_content .= 'Hours Tracked: '.$summary['hours_tracked'];
				$pdf_contents[] = [$summary['src_table'], $pdf_content]; ?>
			<?php } ?>
			<div class="clearfix"></div>
		</div>
	<?php } else if(strpos($value_config,',Time Tracking Set,')) { ?>
		<div class="form-group">
			<div class="col-sm-4 text-center">Name</div>
			<div class="col-sm-2 text-center">Hours</div>
			<div class="col-sm-6 text-center">Comment</div>
			<div class="clearfix"></div>
			<?php $summary_staff = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Staff','Staff_Tasks')".$query_daily);
			$summary = mysqli_fetch_array($summary_staff);
			do { ?>
				<div class="form-group summary">
					<div class="col-sm-4"><label class="show-on-mobile">Name:</label><?= get_contact($dbc, $summary['item_id']) ?></div>
					<div class="col-sm-2"><label class="show-on-mobile">Hours:</label><input readonly name="time_set" value="<?= $summary['hours_set'] ?>" class="form-control"></div>
					<div class="col-sm-6">
						<label class="show-on-mobile">Comment:</label>
						<?php $time_comments = mysqli_query($dbc, "SELECT `comment_box` FROM `time_cards` WHERE `staff`='{$summary['item_id']}' AND `ticketid`='{$summary['ticketid']}' AND `deleted`=0 AND IFNULL(`comment_box`,'') != ''".$query_daily);
						$pdf_content_comment = [];
						while($time_comment = mysqli_fetch_assoc($time_comments)) {
							echo $time_comment['comment_box']."<br />";
							$pdf_content_comment[] = $time_comment['comment_box'];
						} ?>
					</div>
				</div>
				<?php $pdf_content = '';
				$pdf_content .= 'Name: '.get_contact($dbc, $summary['item_id']).'<br>';
				$pdf_content .= 'Hours: '.$summary['hours_set'].'<br>';
				$pdf_content .= 'Comment: '.implode('<br>', $pdf_content_comment);
				$pdf_contents[] = ['Staff', $pdf_content]; ?>
			<?php } while($summary = mysqli_fetch_array($summary_staff)); ?>
			<?php $members = mysqli_query($dbc, "SELECT `ticket_attached`.*, `time_cards`.`comment_box` FROM `ticket_attached` LEFT JOIN `time_cards` ON `ticket_attached`.`ticketid`=`time_cards`.`ticketid` WHERE `ticket_attached`.`tile_name`='".FOLDER_NAME."' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticketid` > 0 AND `ticket_attached`.`deleted`=0 AND `ticket_attached`.`position`!='Team Lead' AND `ticket_attached`.`src_table` IN ('Members','Clients')".$query_daily);
			while($summary = mysqli_fetch_array($members)) { ?>
				<div class="form-group summary">
					<div class="col-sm-4"><label class="show-on-mobile">Name:</label><?= get_contact($dbc, $summary['item_id']) ?></div>
					<div class="col-sm-2"><label class="show-on-mobile">Hours:</label><input readonly type="number" name="hours_set" value="<?= $summary['hours_set'] ?>" class="form-control"></div>
					<div class="col-sm-6"><label class="show-on-mobile">Comment:</label><input type="text" readonly name="time_comment" value="" class="form-control"><?= $summary['comment_box'] ?></div>
				</div>
				<?php $pdf_content = '';
				$pdf_content .= 'Name: '.get_contact($dbc, $summary['item_id']).'<br>';
				$pdf_content .= 'Hours: '.$summary['hours_set'].'<br>';
				$pdf_content .= 'Comment: '.implode('<br>', $pdf_content_comment);
				$pdf_contents[] = [$summary['src_table'], $pdf_content]; ?>
			<?php } ?>
			<button class="btn brand-btn pull-right" id="summary_btn" onclick="add_staff_summary(); return false;">Add</button>
			<div class="clearfix"></div>
		</div>
	<?php } else if(strpos($value_config,',Time Tracking Hrs,')) { ?>
		<div class="form-group">
			<div class="col-sm-4 text-center">Name</div>
			<div class="col-sm-2 text-center">Hours</div>
			<div class="col-sm-6 text-center">Comment</div>
			<div class="clearfix"></div>
			<?php $summary_staff = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Staff','Staff_Tasks')".$query_daily);
			$summary = mysqli_fetch_array($summary_staff);
			do { ?>
				<div class="form-group summary">
					<div class="col-sm-4"><label class="show-on-mobile">Name:</label><?= get_contact($dbc, $summary['item_id']) ?></div>
					<div class="col-sm-2"><label class="show-on-mobile">Hours:</label><input readonly name="time_set" value="<?= $summary['hours_tracked'] ?>" class="form-control"></div>
					<div class="col-sm-6">
						<label class="show-on-mobile">Comment:</label>
						<?php $time_comments = mysqli_query($dbc, "SELECT `comment_box` FROM `time_cards` WHERE `staff`='{$summary['item_id']}' AND `ticketid`='{$summary['ticketid']}' AND `deleted`=0 AND IFNULL(`comment_box`,'') != ''".$query_daily);
						$pdf_content_comment = [];
						while($time_comment = mysqli_fetch_assoc($time_comments)) {
							echo $time_comment['comment_box']."<br />";
							$pdf_content_comment[] = $time_comment['comment_box'];
						} ?>
					</div>
				</div>
				<?php $pdf_content = '';
				$pdf_content .= 'Name: '.get_contact($dbc, $summary['item_id']).'<br>';
				$pdf_content .= 'Hours: '.$summary['hours_tracked'].'<br>';
				$pdf_content .= 'Comment: '.implode('<br>', $pdf_content_comment);
				$pdf_contents[] = ['Staff', $pdf_content]; ?>
			<?php } while($summary = mysqli_fetch_array($summary_staff)); ?>
			<?php $members = mysqli_query($dbc, "SELECT `ticket_attached`.*, `time_cards`.`comment_box` FROM `ticket_attached` LEFT JOIN `time_cards` ON `ticket_attached`.`ticketid`=`time_cards`.`ticketid` WHERE `ticket_attached`.`tile_name`='".FOLDER_NAME."' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticketid` > 0 AND `ticket_attached`.`deleted`=0 AND `ticket_attached`.`position`!='Team Lead' AND `ticket_attached`.`src_table` IN ('Members','Clients')".$query_daily);
			while($summary = mysqli_fetch_array($members)) { ?>
				<div class="form-group summary">
					<div class="col-sm-4"><label class="show-on-mobile">Name:</label><?= get_contact($dbc, $summary['item_id']) ?></div>
					<div class="col-sm-2"><label class="show-on-mobile">Hours:</label><input readonly type="number" name="hours_tracked" value="<?= $summary['hours_tracked'] ?>" class="form-control"></div>
					<div class="col-sm-6"><label class="show-on-mobile">Comment:</label><input type="text" readonly name="time_comment" value="" class="form-control"><?= $summary['comment_box'] ?></div>
				</div>
				<?php $pdf_content = '';
				$pdf_content .= 'Name: '.get_contact($dbc, $summary['item_id']).'<br>';
				$pdf_content .= 'Hours: '.$summary['hours_tracked'].'<br>';
				$pdf_content .= 'Comment: '.implode('<br>', $pdf_content_comment);
				$pdf_contents[] = [$summary['src_table'], $pdf_content]; ?>
			<?php } ?>
			<button class="btn brand-btn pull-right" id="summary_btn" onclick="add_staff_summary(); return false;">Add</button>
			<div class="clearfix"></div>
		</div>
	<?php }
	if(strpos($value_config,',Planned Tracked Payable Staff,')) { ?>
		<div class="form-group">
			<table id="no-more-tables" class="table table-bordered summary_table">
				<tr class='hide-titles-mob'>
					<th>Staff</th>
					<th>Planned Hours</th>
					<th>Tracked Hours</th>
					<th>Total Tracked Time</th>
					<th <?= check_subtab_persmission($dbc, 'ticket', ROLE, 'view_payable') ? '' : 'style="display:none;"' ?>>Payable Hours</th>
				</tr>
				<?php $summary_staff = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Staff','Staff_Tasks')".$query_daily);
				 while($summary = mysqli_fetch_array($summary_staff)) { ?>
					<tr class="summary">
						<td data-title="Staff"><?= get_contact($dbc, $summary['item_id']) ?></td>
						<td data-title="Planned Hours"><?= $get_ticket['start_time'].' - '.$get_ticket['end_time'] ?></td>
						<td data-title="Tracked Hours">
							<?php $tracked_hours = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_attached_checkin` WHERE `ticket_attached_id` = '".$summary['id']."'"),MYSQLI_ASSOC);
							if(!empty($tracked_hours)) {
								$tracked_html = [];
								foreach($tracked_hours as $tracked_hour) {
									$tracked_html[] = $tracked_hour['checked_in'].' - '.$tracked_hour['checked_out'];
								}
								$tracked_html = implode('<br />',$tracked_html);
							} else {
								$tracked_html = $summary['checked_in'].' - '.$summary['checked_out'];
							}
							echo $tracked_html;
							?>
						</td>
						<td data-title="Total Tracked Time">
							<?php $tracked_time = '-';

							if(!empty($tracked_hours)) {
								$tracked_time = 0;
								foreach($tracked_hours as $tracked_hour) {
									$tracked_time += number_format((strtotime(date('Y-m-d').' '.$tracked_hour['checked_out']) - strtotime(date('Y-m-d').' '.$tracked_hour['checked_in']))/3600,2);
								}
							} else if(!empty($summary['checked_out']) && !empty($summary['checked_in'])) {
								$tracked_time = number_format((strtotime(date('Y-m-d').' '.$summary['checked_out']) - strtotime(date('Y-m-d').' '.$summary['checked_in']))/3600,2);
							} else if($summary['hours_tracked'] > 0) {
								$tracked_time = number_format($summary['hours_tracked'],2);
							}
							echo $tracked_time; ?>
						</td>
						<td data-title="Payable Hours" <?= check_subtab_persmission($dbc, 'ticket', ROLE, 'view_payable') ? '' : 'style="display:none;"' ?>><?= $summary['hours_set'] ?></td>
					</tr>
					<?php $pdf_content= '';
					$pdf_content .= 'Planned Hours: '.$get_ticket['start_time'].' - '.$get_ticket['end_time'].'<br>';
					$pdf_content .= 'Tracked Hours: '.$tracked_html.'<br>';
					$pdf_content .= 'Tracked Time: '.$tracked_time.'<br>';
					$pdf_content .= 'Payable Hours: '.$summary['hours_set'];
					$pdf_contents[] = ['Staff: '.get_contact($dbc, $summary['item_id']), $pdf_content]; ?>
				<?php } ?>
			</table>
		</div>
	<?php }
	if(strpos($value_config,',Planned Tracked Payable Staff Multiple Times,')) { ?>
		<div class="form-group">
			<table id="no-more-tables" class="table table-bordered summary_table">
				<tr class='hide-titles-mob'>
					<th>Staff</th>
					<th>Planned Hours</th>
					<th>Tracked Hours</th>
					<th>Total Tracked Time</th>
					<th <?= check_subtab_persmission($dbc, 'ticket', ROLE, 'view_payable') ? '' : 'style="display:none;"' ?>>Payable Hours</th>
				</tr>
				<?php $summary_staff = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Staff','Staff_Tasks')".$query_daily);
				 while($summary = mysqli_fetch_array($summary_staff)) {
					 $staff_times = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `src_table` = 'Multiple_Timesheet_Row' AND `item_id` = '".$summary['id']."' AND '".$summary['id']."' > 0 AND `deleted` = 0"),MYSQLI_ASSOC); ?>
					<tr class="summary">
						<td data-title="Staff"><?= get_contact($dbc, $summary['item_id']) ?>
						<br><label class="form-checkbox"><input type="checkbox" name="discrepancy" <?= $summary['discrepancy'] == 1 ? 'checked' : '' ?> data-table="ticket_attached" data-id="<?= $summary['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" value="1">Do Not Require Notes</label></td>
						<td data-title="Planned Hours">
							<?php $planned_html = [];
							foreach($staff_times as $staff_time) {
								$planned_html[] = $staff_time['date_stamp'].' '.$staff_time['start_time'].' - '.$staff_time['end_time'];
							}
							$planned_html = implode('<br />',$planned_html);
							echo $planned_html;
							?>
						</td>
						<td data-title="Tracked Hours">
							<?php $tracked_hours = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_attached_checkin` WHERE `ticket_attached_id` = '".$summary['id']."'"),MYSQLI_ASSOC);
							if(!empty($tracked_hours)) {
								$tracked_html = [];
								foreach($tracked_hours as $tracked_hour) {
									$tracked_html[] = (!empty(str_replace('0000-00-00','',$tracked_hour['checked_in_date'])) ? $tracked_hour['checked_in_date'].' ' : '').$tracked_hour['checked_in'].' - '.(!empty(str_replace('0000-00-00','',$tracked_hour['checked_out_date'])) ? $tracked_hour['checked_out_date'].' ' : '').$tracked_hour['checked_out'];
								}
								$tracked_html = implode('<br />',$tracked_html);
							} else {
								$tracked_html = $summary['checked_in'].' - '.$summary['checked_out'];
							}
							echo $tracked_html;
							?>
						</td>
						<td data-title="Total Tracked Time">
							<?php $tracked_time = '-';

							if(!empty($tracked_hours)) {
								$tracked_time = 0;
								foreach($tracked_hours as $tracked_hour) {
									$tracked_time += number_format((strtotime((!empty(str_replace('0000-00-00','',$tracked_hour['checked_out_date'])) ? $tracked_hour['checked_out_date'] : date('Y-m-d')).' '.$tracked_hour['checked_out']) - strtotime((!empty(str_replace('0000-00-00','',$tracked_hour['checked_in_date'])) ? $tracked_hour['checked_in_date'] : date('Y-m-d')).' '.$tracked_hour['checked_in']))/3600,2);
								}
							} else if(!empty($summary['checked_out']) && !empty($summary['checked_in'])) {
								$tracked_time = number_format((strtotime(date('Y-m-d').' '.$summary['checked_out']) - strtotime(date('Y-m-d').' '.$summary['checked_in']))/3600,2);
							} else if($summary['hours_tracked'] > 0) {
								$tracked_time = number_format($summary['hours_tracked'],2);
							}
							echo $tracked_time; ?>
						</td>
						<td data-title="Payable Hours" <?= check_subtab_persmission($dbc, 'ticket', ROLE, 'view_payable') ? '' : 'style="display:none;"' ?>>
							<?php $payable_html = [];
							foreach($staff_times as $staff_time) {
								$payable_html[] = $staff_time['hours_set'];
							}
							$payable_html = implode('<br />',$payable_html);
							echo $payable_html;
							?>
						</td>
					</tr>
					<?php $pdf_content= '';
					$pdf_content .= 'Planned Hours: '.$planned_html.'<br>';
					$pdf_content .= 'Tracked Hours: '.$tracked_html.'<br>';
					$pdf_content .= 'Tracked Time: '.$tracked_time.'<br>';
					$pdf_content .= 'Payable Hours: '.$payable_html;
					$pdf_contents[] = ['Staff: '.get_contact($dbc, $summary['item_id']), $pdf_content]; ?>
				<?php } ?>
			</table>
		</div>
	<?php }
	if(strpos($value_config,',Planned Tracked Payable Members,')) { ?>
		<div class="form-group">
			<table id="no-more-tables" class="table table-bordered summary_table">
				<tr class='hide-titles-mob'>
					<th>Member</th>
					<th>Planned Hours</th>
					<th>Tracked Hours</th>
					<th>Total Tracked Time</th>
					<th <?= check_subtab_persmission($dbc, 'ticket', ROLE, 'view_payable') ? '' : 'style="display:none;"' ?>>Payable Hours</th>
				</tr>
				<?php $summary_members = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `tile_name`='".FOLDER_NAME."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 $hide_positions AND `src_table` IN ('Members')".$query_daily);
				 while($summary = mysqli_fetch_array($summary_members)) { ?>
					<tr class="summary">
						<td data-title="Member"><?= get_contact($dbc, $summary['item_id']) ?></td>
						<td data-title="Planned Hours"><?= $get_ticket['member_start_time'].' - '.$get_ticket['member_end_time'] ?></td>
						<td data-title="Tracked Hours"><?= $summary['checked_in'].' - '.$summary['checked_out'] ?></td>
						<td data-title="Total Tracked Time">
							<?php $tracked_time = '-';
							if(!empty($summary['checked_out']) && !empty($summary['checked_in'])) {
								$tracked_time = number_format((strtotime(date('Y-m-d').' '.$summary['checked_out']) - strtotime(date('Y-m-d').' '.$summary['checked_in']))/3600,2);
							} else if($summary['hours_tracked'] > 0) {
								$tracked_time = number_format($summary['hours_tracked'],2);
							}
							echo $tracked_time; ?>
						</td>
						<td data-title="Payable Hours" <?= check_subtab_persmission($dbc, 'ticket', ROLE, 'view_payable') ? '' : 'style="display:none;"' ?>><?= $summary['hours_set'] ?></td>
					</tr>
					<?php $pdf_content= '';
					$pdf_content .= 'Planned Hours: '.$get_ticket['member_start_time'].' - '.$get_ticket['member_end_time'].'<br>';
					$pdf_content .= 'Tracked Hours: '.$summary['checked_in'].' - '.$summary['checked_out'].'<br>';
					$pdf_content .= 'Payable Hours: '.$summary['hours_set'];
					$pdf_contents[] = ['Member: '.get_contact($dbc, $summary['item_id']), $pdf_content]; ?>
				<?php } ?>
			</table>
		</div>
	<?php }
} ?>