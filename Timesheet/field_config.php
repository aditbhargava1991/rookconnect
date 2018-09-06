<?php
include ('../include.php');
checkAuthorised('timesheet');
include 'config.php';
$_GET['from_url'] = 'index.php'.(!empty($_GET['from_url']) ? '?url='.$_GET['from_url'] : '');

if (isset($_POST['submit']) && $_POST['submit'] == 'reporting') {
    $timesheet_reporting_styling = filter_var($_POST['timesheet_reporting_styling'],FILTER_SANITIZE_STRING);
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE `name` = 'timesheet_reporting_styling'"));

    if($get_field_config['configid'] > 0) {
	    mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_reporting_styling' WHERE `name`='timesheet_reporting_styling'");
    } else {
        mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('timesheet_reporting_styling', '$timesheet_reporting_styling')");
    }
}

if (isset($_POST['submit']) && $_POST['submit'] == 'payroll') {
    $timesheet_payroll_styling = filter_var($_POST['timesheet_payroll_styling'],FILTER_SANITIZE_STRING);
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE `name` = 'timesheet_payroll_styling'"));

    if($get_field_config['configid'] > 0) {
	    mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_payroll_styling' WHERE `name`='timesheet_payroll_styling'");
    } else {
        mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('timesheet_payroll_styling', '$timesheet_payroll_styling')");
    }

    $timesheet_payroll_fields = filter_var(implode(',',$_POST['timesheet_payroll_fields']),FILTER_SANITIZE_STRING);
    set_config($dbc, 'timesheet_payroll_fields', $timesheet_payroll_fields);
    $timesheet_payroll_layout = filter_var($_POST['timesheet_payroll_layout'],FILTER_SANITIZE_STRING);
    set_config($dbc, 'timesheet_payroll_layout', $timesheet_payroll_layout);
    $timesheet_payroll_overtime = filter_var($_POST['timesheet_payroll_overtime'],FILTER_SANITIZE_STRING);
    set_config($dbc, 'timesheet_payroll_overtime', $timesheet_payroll_overtime);
    $timesheet_payroll_doubletime = filter_var($_POST['timesheet_payroll_doubletime'],FILTER_SANITIZE_STRING);
    set_config($dbc, 'timesheet_payroll_doubletime', $timesheet_payroll_doubletime);
}

if (isset($_POST['submit']) && $_POST['submit'] == 'fields') {
    foreach($config['settings'] as $settings => $value) {
        if(isset($value['config_field'])) {
            $post_value = implode(',',$_POST[$value['config_field']]);
        }

        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldconfigid) AS fieldconfigid FROM field_config"));
        if($get_field_config['fieldconfigid'] > 0) {
            $query_update = "UPDATE `field_config` SET ".$value['config_field']." = '".$post_value."' WHERE `fieldconfigid` = 1";
            $result_update = mysqli_query($dbc, $query_update);
        } else {
            $query_insert_config = "INSERT INTO `field_config` (`".$value['config_field']."`) VALUES ('".$post_value."')";
            $result_insert_config = mysqli_query($dbc, $query_insert_config);
        }
    }

	if(!empty($_FILES['upload_logo']['name'])) {
		$name = htmlspecialchars(basename($_FILES['upload_logo']['name']), ENT_QUOTES);
		move_uploaded_file($_FILES['upload_logo']['tmp_name'], 'download/'.$name);
		$result = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_pdf_logo' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_pdf_logo') CONFIG WHERE `rows`=0");
		$result = mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$name' WHERE `name`='timesheet_pdf_logo'");
	}

	$timesheet_pdf_fields = implode(',', $_POST['pdf_fields']);
	set_config($dbc, 'timesheet_pdf_fields', $timesheet_pdf_fields);

	$timesheet_security_roles = implode(',', $_POST['timesheet_security_roles']);
	set_config($dbc, 'timesheet_security_roles', $timesheet_security_roles);
} else if(isset($_POST['submit']) && $_POST['submit'] == 'approvals') {
	$config_ids = implode(',',array_filter($_POST['configid']));
	$deleted_sql = "DELETE FROM `field_config_supervisor` WHERE `fieldconfigid` NOT IN ($config_ids)";
	$deleted_result = mysqli_query($dbc, $deleted_sql);
	foreach($_POST['manager'] as $row => $value) {
		if($value > 0) {
			$configid = $_POST['configid'][$row];
			$coordinators = ','.implode(',',$_POST['managed_coord'][$row]).',';
			$security_levels = implode(',',$_POST['managed_security_level'][$row]);
			if($configid == '') {
				$sql_manager = "INSERT INTO `field_config_supervisor` (`position`, `supervisor`, `staff_list`, `security_level_list`) VALUES ('Manager', '$value', '$coordinators', '$security_levels')";
			}
			else {
				$sql_manager = "UPDATE `field_config_supervisor` SET `supervisor`='$value', `staff_list`='$coordinators', `security_level_list` = '$security_levels' WHERE `fieldconfigid`='$configid'";
			}
			$result_manager = mysqli_query($dbc, $sql_manager);
		}
	}
	foreach($_POST['coordinator'] as $row => $value) {
		if($value > 0) {
			$configid = $_POST['configid'][$row];
			$staff = ','.implode(',',$_POST['coordinated_staff'][$row]).',';
			$security_levels = implode(',',$_POST['coordinated_security_level'][$row]);
			if($configid == '') {
				$sql_coordinator = "INSERT INTO `field_config_supervisor` (`position`, `supervisor`, `staff_list`, `security_level_list`) VALUES ('Coordinator', '$value', '$staff', '$security_levels')";
			}
			else {
				$sql_coordinator = "UPDATE `field_config_supervisor` SET `supervisor`='$value', `staff_list`='$staff', `security_level_list` = '$security_levels' WHERE `fieldconfigid`='$configid'";
			}
			$result_coordinator = mysqli_query($dbc, $sql_coordinator);
		}
	}
	$submit_mode = $_POST['submit_mode'];
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_submit_mode' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_submit_mode') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$submit_mode' WHERE `name`='timesheet_submit_mode'");
	$highlight = $_POST['highlight'];
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_highlight' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_highlight') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$highlight' WHERE `name`='timesheet_highlight'");
	$highlight_manager = $_POST['highlight_manager'];
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_manager' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_manager') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$highlight_manager' WHERE `name`='timesheet_manager'");
	$timesheet_approval_initials = filter_var($_POST['timesheet_approval_initials'],FILTER_SANITIZE_STRING);
	set_config($dbc, 'timesheet_approval_initials', $timesheet_approval_initials);
	$timesheet_approval_status_comments = filter_var($_POST['timesheet_approval_status_comments'],FILTER_SANITIZE_STRING);
	set_config($dbc, 'timesheet_approval_status_comments', $timesheet_approval_status_comments);
	$timesheet_approval_import_export = filter_var($_POST['timesheet_approval_import_export'],FILTER_SANITIZE_STRING);
	set_config($dbc, 'timesheet_approval_import_export', $timesheet_approval_import_export);
	$timesheet_manager_approvals = filter_var($_POST['timesheet_manager_approvals'],FILTER_SANITIZE_STRING);
	set_config($dbc, 'timesheet_manager_approvals', $timesheet_manager_approvals);
} else if(isset($_POST['submit']) && $_POST['submit'] == 'tabs') {
	set_config($dbc, 'timesheet_tabs', implode(',',$_POST['tab_config']));
	set_config($dbc, 'timesheet_layout', $_POST['timesheet_layout']);
	set_config($dbc, 'timesheet_min_hours', $_POST['timesheet_min_hours']);
	set_config($dbc, 'timesheet_rounding', $_POST['timesheet_rounding']);
	set_config($dbc, 'timesheet_rounded_increment', $_POST['timesheet_rounded_increment']);
	set_config($dbc, 'timesheet_manual_hours', $_POST['timesheet_manual_hours']);
	set_config($dbc, 'timesheet_include_time', implode(',',$_POST['timesheet_include_time']));
	set_config($dbc, 'timesheet_comment_placeholder', $_POST['timesheet_comment_placeholder']);
	set_config($dbc, 'timesheet_time_format', $_POST['timesheet_time_format']);
	set_config($dbc, 'timesheet_record_history', $_POST['timesheet_record_history']);
	set_config($dbc, 'timesheet_default_tab', $_POST['timesheet_default_tab']);
} else if(isset($_POST['submit']) && $_POST['submit'] == 'day_tracking') {
	$timesheet_start_tile = filter_var($_POST['timesheet_start_tile'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_start_tile' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_start_tile') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_start_tile' WHERE `name`='timesheet_start_tile'");
	$timesheet_running_button = filter_var($_POST['timesheet_running_button'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_running_button' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_running_button') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_running_button' WHERE `name`='timesheet_running_button'");
	$timesheet_track_shifts = filter_var($_POST['timesheet_track_shifts'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_track_shifts' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_track_shifts') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_track_shifts' WHERE `name`='timesheet_track_shifts'");
	$timesheet_hide_others = filter_var($_POST['timesheet_hide_others'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_hide_others' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_hide_others') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_hide_others' WHERE `name`='timesheet_hide_others'");
	$timesheet_end_tile = filter_var($_POST['timesheet_end_tile'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_end_tile' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_end_tile') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_end_tile' WHERE `name`='timesheet_end_tile'");
	$timesheet_always_show = filter_var($_POST['timesheet_always_show'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_always_show' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_always_show') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_always_show' WHERE `name`='timesheet_always_show'");
	$timesheet_add_day_comment = filter_var($_POST['timesheet_add_day_comment'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_add_day_comment' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_add_day_comment') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_add_day_comment' WHERE `name`='timesheet_add_day_comment'");
	$timesheet_direct_indirect = filter_var($_POST['timesheet_direct_indirect'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_direct_indirect' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_direct_indirect') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_direct_indirect' WHERE `name`='timesheet_direct_indirect'");
	$timesheet_hide_groups = filter_var($_POST['timesheet_hide_groups'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_hide_groups' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_hide_groups') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_hide_groups' WHERE `name`='timesheet_hide_groups'");
	$timesheet_track_clients = filter_var($_POST['timesheet_track_clients'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_track_clients' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_track_clients') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_track_clients' WHERE `name`='timesheet_track_clients'");
	$timesheet_client_category = filter_var($_POST['timesheet_client_category'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_client_category' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_client_category') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_client_category' WHERE `name`='timesheet_client_category'");
	set_config($dbc, 'day_tracking_preset_note', $_POST['day_tracking_preset_note']);
	set_config($dbc, 'timesheet_hide_past_days', $_POST['timesheet_hide_past_days']);
	set_config($dbc, 'timesheet_break_option', $_POST['timesheet_break_option']);
	set_config($dbc, 'ticket_force_starts_day', $_POST['ticket_force_starts_day']);
	set_config($dbc, 'active_ticket_button', $_POST['active_ticket_button']);
	set_config($dbc, 'timesheet_force_end_midnight', $_POST['timesheet_force_end_midnight']);
} else if(isset($_POST['submit']) && $_POST['submit'] == 'holiday') {
	set_config($dbc, 'holiday_update_noti', filter_var($_POST['holiday_update_noti']));
	set_config($dbc, 'holiday_update_staff', filter_var($_POST['holiday_update_staff']));
	set_config($dbc, 'holiday_update_date', filter_var($_POST['holiday_update_date']));
}
?>
<style>
.config_ulli li {
    list-style: none;
    float: left;
    width: 20%;
}
</style>
</head>
<body>

<?php include ('../navigation.php');
if($_GET['tab'] == 'approvals') {
	$_GET['tab'] = 'approvals';
} else if($_GET['tab'] == 'day_tracking') {
	$_GET['tab'] = 'day_tracking';
} else if($_GET['tab'] == 'tabs') {
	$_GET['tab'] = 'tabs';
} else if($_GET['tab'] == 'reporting') {
	$_GET['tab'] = 'reporting';
} else if($_GET['tab'] == 'payroll') {
	$_GET['tab'] = 'payroll';
} else if($_GET['tab'] == 'holiday') {
	$_GET['tab'] = 'holiday';
} else {
	$_GET['tab'] = 'fields';
} ?>

<div class="container">
<div class="row">
<h1>Time Sheets</h1>
<div class="pad-left gap-top double-gap-bottom"><a href="<?= $_GET['from_url'] ?>" class="btn config-btn">Back to Dashboard</a></div>

<div class="tab_container">
	<a href="?tab=tabs&from_url=<?= $_GET['from_url'] ?>" class="btn brand-btn <?php echo ($_GET['tab'] == 'tabs' ? 'active_tab' : ''); ?>">Time Sheet Tabs</a>
	<a href="?tab=fields&from_url=<?= $_GET['from_url'] ?>" class="btn brand-btn <?php echo ($_GET['tab'] == 'fields' ? 'active_tab' : ''); ?>">Time Sheet Fields</a>
	<a href="?tab=approvals&from_url=<?= $_GET['from_url'] ?>" class="btn brand-btn <?php echo ($_GET['tab'] == 'approvals' ? 'active_tab' : ''); ?>">Approvals</a>
	<a href="?tab=day_tracking&from_url=<?= $_GET['from_url'] ?>" class="btn brand-btn <?php echo ($_GET['tab'] == 'day_tracking' ? 'active_tab' : ''); ?>"><?= get_config($dbc, 'timesheet_start_tile') ?: 'Start Day' ?></a>
	<a href="?tab=reporting&from_url=<?= $_GET['from_url'] ?>" class="btn brand-btn <?php echo ($_GET['tab'] == 'reporting' ? 'active_tab' : ''); ?>">Reporting</a>
	<a href="?tab=payroll&from_url=<?= $_GET['from_url'] ?>" class="btn brand-btn <?php echo ($_GET['tab'] == 'payroll' ? 'active_tab' : ''); ?>">Payroll</a>
	<a href="?tab=holiday&from_url=<?= $_GET['from_url'] ?>" class="btn brand-btn <?php echo ($_GET['tab'] == 'holiday' ? 'active_tab' : ''); ?>">Holidays</a>
</div><br />

<form id="form1" name="form1" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
<?php if($_GET['tab'] == 'fields'): ?>
	<div class="panel-group" id="accordion2">
	<?php
	$k=0;
	$layout = get_config($dbc, 'timesheet_layout');
	foreach($config['settings'] as $settings => $value) {
		if(isset($value['config_field'])) {
			if($value['config_field'] == 'time_cards_total_hrs_layout') {
				$get_field_config = @mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$value['config_field']." FROM field_config"));
				$value_config = ','.$get_field_config[$value['config_field']].',';
				if(empty(trim($value_config,','))) {
					$value_config = ',reg_hrs,overtime_hrs,doubletime_hrs,';
				}
			} else {
				$get_field_config = @mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$value['config_field']." FROM field_config"));
				$value_config = ','.$get_field_config[$value['config_field']].',';
				if(strpos($value_config,',reg_hrs,') === FALSE && strpos($value_config,',direct_hrs,') === FALSE && strpos($value_config,',payable_hrs,') === FALSE && !in_array($layout, ['ticket_task','position_dropdown'])) {
					$value_config .= 'reg_hrs,extra_hrs,relief_hrs,sleep_hrs,sick_hrs,sick_used,stat_hrs,stat_used,vaca_hrs,vaca_used,';
				}
			}
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field<?php echo $k; ?>" >
							<?php echo $settings; ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>
				<div id="collapse_field<?php echo $k; ?>" class="panel-collapse collapse">
					<div class="panel-body">
						<ul class="config_ulli">
							<?php if($settings == 'Choose Fields for Time Sheets') {
								echo "<h4>These settings will be used for the Time Sheets tab, the Approval tabs, the Reporting tab, and the Payroll tab.</h4>";
							}
							foreach($value['data'] as $tabs) {
								foreach($tabs as $field) {
							 ?>
							<li>
								<input type="checkbox" <?php if (strpos($value_config, ','.$field[2].',') !== FALSE) { echo " checked"; } ?> value="<?php echo $field[2];?>" style="height: 20px; width: 20px;" name="<?php echo $value['config_field']; ?>[]">&nbsp;&nbsp;<?php echo $field[0]; ?>
							</li>
							<?php }
							}
							?>
						</ul>
					</div>
				</div>
			</div>
			<?php
			$k++;
		}
	}

	?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field_pdf_config" >
						PDF Configuration<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_field_pdf_config" class="panel-collapse collapse">
				<div class="panel-body">
					<div class="form-group">
						<label for="additional_note" class="col-sm-4 control-label">Logo for PDF:
								<span class="popover-examples list-inline">&nbsp;
								<a href="#job_file" data-toggle="tooltip" data-placement="top" title="" data-original-title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
								</span>
						</label>
						<div class="col-sm-8">
							<div class="form-group clearfix">
								<?php $logo_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `value` FROM `general_configuration` WHERE `name`='timesheet_pdf_logo' UNION SELECT '' `value`"))['value'];
								if($logo_config != '') {
									echo "<a href='download/$logo_config'>View Logo</a><br />";
								} ?>
								<input name="upload_logo" type="file" data-filename-placement="inside" class="form-control">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Fields:</label>
						<div class="col-sm-8">
							<?php $timesheet_pdf_fields = ','.get_config($dbc, 'timesheet_pdf_fields').','; ?>
							<label class="form-checkbox"><input type="checkbox" name="pdf_fields[]" class="form-control" value="Location" <?= strpos($timesheet_pdf_fields, ',Location,') !== FALSE? ' checked' : '' ?>> Location</label>
							<label class="form-checkbox"><input type="checkbox" name="pdf_fields[]" class="form-control" value="Manager Initials" <?= strpos($timesheet_pdf_fields, ',Manager Initials,') !== FALSE? ' checked' : '' ?>> Manager Initials</label>
							<label class="form-checkbox"><input type="checkbox" name="pdf_fields[]" class="form-control" value="Coordinator Initials" <?= strpos($timesheet_pdf_fields, ',Coordinator Initials,') !== FALSE? ' checked' : '' ?>> Coordinator Initials</label>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field_staff" >
						Staff Settings<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_field_staff" class="panel-collapse collapse">
				<div class="panel-body">
					<div class="form-group">
						<label for="additional_note" class="col-sm-4 control-label">Limit Security Levels:</label>
						<div class="col-sm-8">
							<select name="timesheet_security_roles[]" multiple class="chosen-select-deselect form-control">
								<?php $timesheet_security_roles = array_filter(explode(',',get_config($dbc, 'timesheet_security_roles')));
								foreach(get_security_levels($dbc) as $security_name => $security_level) {
									echo '<option value="'.$security_level.'" '.(in_array($security_level, $timesheet_security_roles) ? 'selected' : '').'>'.$security_name.'</option>';
								} ?>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-6">
			<a href="<?= $_GET['from_url'] ?>" class="btn config-btn btn-lg">Back</a>
		</div>
		<div class="col-sm-6">
			<button type="submit" name="submit" value="fields" class="btn config-btn btn-lg pull-right">Submit</button>
		</div>
		<div class="clearfix"></div>
	</div>
<?php elseif($_GET['tab'] == 'approvals'):
	$submit_mode = get_config($dbc, 'timesheet_submit_mode');
	$highlight = get_config($dbc, 'timesheet_highlight');
	$highlight_manager = get_config($dbc, 'timesheet_manager');
	$i = 0; ?>
	<div class="panel-group" id="accordion2">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_submit" >
						Approval Settings<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_submit" class="panel-collapse collapse">
				<div class="panel-body">
					<div class="form-group">
						<label class="col-sm-4 control-label">Submit Timesheet:</label>
						<div class="col-sm-8">
							<label class="form-checkbox"><input type="radio" <?= $submit_mode == '' || $submit_mode == 'manual' ? 'checked' : '' ?> name="submit_mode" value="manual">Manually Submit</label>
							<label class="form-checkbox"><input type="radio" <?= $submit_mode == 'auto' ? 'checked' : '' ?> name="submit_mode" value="auto">Automatically Submit</label>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Highlight Staff Changes:<br /><em>Black will have no highlight effect.</em></label>
						<div class="col-sm-8">
							<input type="color" value="<?= $highlight ?>" name="highlight" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Highlight Changes at Approvals:<br /><em>Black will have no highlight effect.</em></label>
						<div class="col-sm-8">
							<input type="color" value="<?= $highlight_manager ?>" name="highlight_manager" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Show Approval Initials:</label>
						<div class="col-sm-8">
							<?php $timesheet_approval_initials = get_config($dbc, 'timesheet_approval_initials'); ?>
							<label class="form-checkbox"><input type="checkbox" value="1" name="timesheet_approval_initials" <?= $timesheet_approval_initials == 1 ? 'checked' : '' ?>> Enable</label>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Show Approval Status In Comments:</label>
						<div class="col-sm-8">
							<?php $timesheet_approval_status_comments = get_config($dbc, 'timesheet_approval_status_comments'); ?>
							<label class="form-checkbox"><input type="checkbox" value="1" name="timesheet_approval_status_comments" <?= $timesheet_approval_status_comments == 1 ? 'checked' : '' ?>> Enable</label>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Import/Export CSV:</label>
						<div class="col-sm-8">
							<?php $timesheet_approval_import_export = get_config($dbc, 'timesheet_approval_import_export'); ?>
							<label class="form-checkbox"><input type="checkbox" value="1" name="timesheet_approval_import_export" <?= $timesheet_approval_import_export == 1 ? 'checked' : '' ?>> Enable</label>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Rename Manager Approvals Tab:</label>
						<div class="col-sm-8">
							<?php $timesheet_manager_approvals = get_config($dbc, 'timesheet_manager_approvals'); ?>
							<input type="text" name="timesheet_manager_approvals" class="form-control" value="<?= $timesheet_manager_approvals ?>">
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_managers" >
						Approval Managers<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_managers" class="panel-collapse collapse">
				<div class="panel-body">
					<div id="no-more-tables">
						<table class="table table-bordered manager_table"><tr class='hidden-xs hidden-sm'><th class="col-sm-3">Manager Name</th><th class="col-sm-4">Coordinators</th><th class="col-sm-4">Security Level</th><th class="col-sm-1"></th></tr>
							<?php $manage_config_result = mysqli_query($dbc, "SELECT `fieldconfigid`, `supervisor`, `staff_list`, `security_level_list` FROM `field_config_supervisor` WHERE `position`='Manager' UNION SELECT '', '', '', ''");
							while($manage_config = mysqli_fetch_array($manage_config_result)) {
								echo "<tr><td data-title='Manager Name:'>";
								echo "<input name='configid[".$i."]' type='hidden' value='".$manage_config['fieldconfigid']."'>";
								echo "<select name='manager[".$i."]' data-placeholder='Select a Manager' class='chosen-select-deselect'><option></option>";
								$staff_list = mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 AND `status` = 1 AND `show_hide_user` = 1 OR `contactid` = '".$manage_config['supervisor']."'");
								while($staff = mysqli_fetch_array($staff_list)) {
									echo "<option ".($staff['contactid'] == $manage_config['supervisor'] ? 'selected' : '')." value='".$staff['contactid']."'>".get_contact($dbc,$staff['contactid'])."</option>";
								}
								echo "</select>";
								echo "</td><td data-title='Coordinators'>";
								echo "<select name='managed_coord[".$i."][]' data-placeholder='Select Coordinators to be Managed' multiple class='chosen-select-deselect'><option></option>";
								$staff_list = mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 AND `status` = 1 AND `show_hide_user` = 1");
								while($staff = mysqli_fetch_array($staff_list)) {
									echo "<option ".(strpos(','.$manage_config['staff_list'].',',','.$staff['contactid'].',') !== false ? 'selected' : '')." value='".$staff['contactid']."'>".get_contact($dbc,$staff['contactid'])."</option>";
								}
								echo "</select>";
								echo "</td><td data-title='Security Levels'>";
								echo "<select name='managed_security_level[".$i++."][]' data-placeholder='Select Security Levels to be Coordinated' multiple class='chosen-select-deselect'><option></option>";
								$security_levels = get_security_levels($dbc);
								foreach($security_levels as $level_label => $security_level) {
									echo "<option ".(strpos(','.$manage_config['security_level_list'].',',','.$security_level.',') !== false ? 'selected' : '')." value='".$security_level."'>".$level_label."</option>";
								}
								echo "</select>";
								echo "</td><td><button class='btn brand-btn' onclick='$(this).closest(\"tr\").remove(); return false;'>Delete</button></td></tr>";
							} ?>
						</table>
					</div>
					<button class="btn brand-btn pull-right" onclick="addManager(); return false;">Add Manager</button>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_coordinators" >
						Approval Coordinators<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_coordinators" class="panel-collapse collapse">
				<div class="panel-body">
					<div id="no-more-tables">
						<table class="table table-bordered coordinator_table"><tr class='hidden-xs hidden-sm'><th class="col-sm-3">Coordinator Name</th><th class="col-sm-4">Staff</th><th class="col-sm-4">Security Level</th><th class="col-sm-1"></th></tr>
							<?php $manage_config_result = mysqli_query($dbc, "SELECT `fieldconfigid`, `supervisor`, `staff_list`, `security_level_list` FROM `field_config_supervisor` WHERE `position`='Coordinator' UNION SELECT '', '', '', ''");
							while($manage_config = mysqli_fetch_array($manage_config_result)) {
								echo "<tr><td data-title='Coordinator Name:'>";
								echo "<input name='configid[".$i."]' type='hidden' value='".$manage_config['fieldconfigid']."'>";
								echo "<select name='coordinator[".$i."]' data-placeholder='Select a Coordinator' class='chosen-select-deselect'><option></option>";
								$staff_list = mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 AND `status` = 1 AND `show_hide_user` = 1 OR `contactid` = '".$manage_config['supervisor']."'");
								while($staff = mysqli_fetch_array($staff_list)) {
									echo "<option ".($staff['contactid'] == $manage_config['supervisor'] ? 'selected' : '')." value='".$staff['contactid']."'>".get_contact($dbc,$staff['contactid'])."</option>";
								}
								echo "</select>";
								echo "</td><td data-title='Staff Members'>";
								echo "<select name='coordinated_staff[".$i."][]' data-placeholder='Select Staff Members to be Coordinated' multiple class='chosen-select-deselect'><option></option>";
								$staff_list = mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 AND `status` = 1 AND `show_hide_user` = 1");
								while($staff = mysqli_fetch_array($staff_list)) {
									echo "<option ".(strpos(','.$manage_config['staff_list'].',',','.$staff['contactid'].',') !== false ? 'selected' : '')." value='".$staff['contactid']."'>".get_contact($dbc,$staff['contactid'])."</option>";
								}
								echo "</select>";
								echo "</td><td data-title='Security Levels'>";
								echo "<select name='coordinated_security_level[".$i++."][]' data-placeholder='Select Security Levels to be Coordinated' multiple class='chosen-select-deselect'><option></option>";
								$security_levels = get_security_levels($dbc);
								foreach($security_levels as $level_label => $security_level) {
									echo "<option ".(strpos(','.$manage_config['security_level_list'].',',','.$security_level.',') !== false ? 'selected' : '')." value='".$security_level."'>".$level_label."</option>";
								}
								echo "</select>";
								echo "</td><td><button class='btn brand-btn' onclick='$(this).closest(\"tr\").remove(); return false;'>Delete</button></td></tr>";
							} ?>
						</table>
					</div>
					<button class="btn brand-btn pull-right" onclick="addCoordinator(); return false;">Add Coordinator</button>
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-6">
			<a href="<?= $_GET['from_url'] ?>" class="btn config-btn btn-lg">Back</a>
		</div>
		<div class="col-sm-6">
			<button type="submit" name="submit" value="approvals" class="btn config-btn btn-lg pull-right">Submit</button>
		</div>
		<div class="clearfix"></div>
	</div>
	<script>
	var row_next_num = <?php echo $i; ?>;
	function addManager() {
		var row = $('.manager_table tr:last').clone();
		row.find('input').val('');
		resetChosen(row.find("select[class^=chosen]"));
		var row_num = row.find('input[type="hidden"]').attr('name').replace('configid','');
		row.find('[name*="'+row_num+'"]').each(function() { $(this).attr('name',$(this).attr('name').replace(row_num,'['+row_next_num+']')); });
		$('.manager_table').append(row);
		row_next_num++;
	}

	function addCoordinator() {
		var row = $('.coordinator_table tr:last').clone();
		row.find('input').val('');
		resetChosen(row.find("select[class^=chosen]"));
		var row_num = row.find('input[type="hidden"]').attr('name').replace('configid','');
		row.find('[name*="'+row_num+'"]').each(function() { $(this).attr('name',$(this).attr('name').replace(row_num,'['+row_next_num+']')); });
		$('.coordinator_table').append(row);
		row_next_num++;
	}
	</script>

<?php elseif($_GET['tab'] == 'tabs'):
	$tab_config = get_config($dbc,'timesheet_tabs'); ?>
	<script type="text/javascript">
	$(document).ready(function() {
		$('.sortable_tabs_div').sortable({
			items: '.sortable_tabs'
		});
	});
	</script>
	<div class="panel-group" id="accordion2">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_managers" >
						Active Tabs<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_managers" class="panel-collapse collapse">
				<div class="panel-body">
					<div class="sortable_tabs_div">
						<em>Drag tabs to reorder.<br></em>
						<?php foreach($config['tabs'] as $tab_name => $link) {
							echo "<label class='sortable_tabs'><input ".(strpos(','.$tab_config.',',','.$tab_name.',') !== FALSE?'checked':'')." type='checkbox' name='tab_config[]' value='".$tab_name."'>&nbsp;&nbsp;".$tab_name."</label>&nbsp;&nbsp;";
						} ?>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_time_tab" >
						Default Time Period Tab<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_time_tab" class="panel-collapse collapse">
				<div class="panel-body">
					<?php $default_tab = !empty(get_config($dbc, 'timesheet_default_tab')) ? get_config($dbc, 'timesheet_default_tab') : 'Custom';
					foreach($config['time_tabs'] as $time_tab) {
						echo "<input ".($time_tab == $default_tab ? 'checked' : '')." type='radio' name='timesheet_default_tab' value='".$time_tab."'>&nbsp;&nbsp;".$time_tab."</label>&nbsp;&nbsp;";
					} ?>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_layout" >
						Time Sheet Options<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_layout" class="panel-collapse collapse">
				<div class="panel-body">
					<h4>Time Sheet Layout</h4>
					<?php $layout = get_config($dbc, 'timesheet_layout');
					echo "<label><input ".($layout == ''?'checked':'')." type='radio' name='timesheet_layout' value=''>&nbsp;&nbsp;Default Layout</label>&nbsp;&nbsp;";
					echo "<label><input ".($layout == 'multi_line'?'checked':'')." type='radio' name='timesheet_layout' value='multi_line'>&nbsp;&nbsp;Show Multiple Lines</label>&nbsp;&nbsp;";
					// echo "<label><input ".($layout == 'position_columns'?'checked':'')." type='radio' name='timesheet_layout' value='position_columns'>&nbsp;&nbsp;Time Sheet with Position Columns</label>&nbsp;&nbsp;";
					echo "<label><input ".($layout == 'position_dropdown'?'checked':'')." type='radio' name='timesheet_layout' value='position_dropdown'>&nbsp;&nbsp;Time Sheet with Position Drop Down</label>&nbsp;&nbsp;";
					echo "<label><input ".($layout == 'ticket_task'?'checked':'')." type='radio' name='timesheet_layout' value='ticket_task'>&nbsp;&nbsp;Time Sheet with ".TICKET_NOUN." Tasks</label>&nbsp;&nbsp;";
					echo "<label><input ".($layout == 'rate_card'?'checked':'')." type='radio' name='timesheet_layout' value='rate_card'>&nbsp;&nbsp;Rate Card Category Layout</label>&nbsp;&nbsp;";
					echo "<label><input ".($layout == 'rate_card_tickets'?'checked':'')." type='radio' name='timesheet_layout' value='rate_card_tickets'>&nbsp;&nbsp;Rate Card Category Per ".TICKET_NOUN."</label>&nbsp;&nbsp;";
					echo "<label><input ".($layout == 'table_add_button'?'checked':'')." type='radio' name='timesheet_layout' value='table_add_button'>&nbsp;&nbsp;Table Layout with Add Button</label>&nbsp;&nbsp;"; ?>
					<h4>Time Sheet Options</h4>
					<?php $timesheet_include_time = explode(',',get_config($dbc, 'timesheet_include_time')); ?>
					<div class="form-group">
						<label class="col-sm-4 control-label">Tracked Time to Include:</label>
						<div class="col-sm-8">
							<label class="form-checkbox"><input type="checkbox" name="timesheet_include_time" value="ticket" <?= in_array('ticket',$timesheet_include_time) ? 'checked' : '' ?>> <?= TICKET_TILE ?></label>
							<label class="form-checkbox"><input type="checkbox" name="timesheet_include_time" value="project" <?= in_array('project',$timesheet_include_time) ? 'checked' : '' ?>> <?= PROJECT_TILE ?></label>
							<label class="form-checkbox"><input type="checkbox" name="timesheet_include_time" value="meeting" <?= in_array('meeting',$timesheet_include_time) ? 'checked' : '' ?>> Meetings</label>
							<label class="form-checkbox"><input type="checkbox" name="timesheet_include_time" value="email" <?= in_array('email',$timesheet_include_time) ? 'checked' : '' ?>> Email Communication</label>
							<label class="form-checkbox"><input type="checkbox" name="timesheet_include_time" value="task" <?= in_array('task',$timesheet_include_time) ? 'checked' : '' ?>> Tasks</label>
							<label class="form-checkbox"><input type="checkbox" name="timesheet_include_time" value="checklist" <?= in_array('checklist',$timesheet_include_time) ? 'checked' : '' ?>> Checklists</label>
						</div>
					</div>
					<?php $timesheet_min_hours = get_config($dbc, 'timesheet_min_hours'); ?>
					<div class="form-group">
						<label class="col-sm-4 control-label">Minimum Hours per Position:</label>
						<div class="col-sm-8">
							<input type="number" name="timesheet_min_hours" step="any" min="0" max="24" value="<?= $timesheet_min_hours ?>" class="form-control">
						</div>
					</div>
					<?php $timesheet_rounding = get_config($dbc, 'timesheet_rounding'); ?>
					<div class="form-group">
						<label class="col-sm-4 control-label">Rounding Method:</label>
						<div class="col-sm-8">
							<label class="form-checkbox"><input type="radio" name="timesheet_rounding" value="" <?= $timesheet_rounding == '' ? 'checked' : '' ?>> No Rounding</label>
							<label class="form-checkbox"><input type="radio" name="timesheet_rounding" value="nearest" <?= $timesheet_rounding == 'nearest' ? 'checked' : '' ?>> Round to Nearest</label>
							<label class="form-checkbox"><input type="radio" name="timesheet_rounding" value="up" <?= $timesheet_rounding == 'up' ? 'checked' : '' ?>> Round Up</label>
							<label class="form-checkbox"><input type="radio" name="timesheet_rounding" value="down" <?= $timesheet_rounding == 'down' ? 'checked' : '' ?>> Round Down</label>
						</div>
					</div>
					<?php $timesheet_rounded_increment = get_config($dbc, 'timesheet_rounded_increment'); ?>
					<div class="form-group">
						<label class="col-sm-4 control-label">Round Hours to Number of Minutes:</label>
						<div class="col-sm-8">
							<input type="number" name="timesheet_rounded_increment" step="any" min="0" max="60" value="<?= $timesheet_rounded_increment ?>" class="form-control">
						</div>
					</div>
					<?php $timesheet_manual_hours = get_config($dbc, 'timesheet_manual_hours'); ?>
					<div class="form-group">
						<label class="col-sm-4 control-label">Enter Hours Manually:</label>
						<div class="col-sm-8">
							<input type="number" name="timesheet_manual_hours" step="any" min="0" max="24" value="<?= $timesheet_manual_hours ?>" class="form-control">
						</div>
					</div>
					<?php $timesheet_comment_placeholder = get_config($dbc, 'timesheet_comment_placeholder'); ?>
					<div class="form-group">
						<label class="col-sm-4 control-label">Empty Comments Placeholder:</label>
						<div class="col-sm-8">
							<input type="text" name="timesheet_comment_placeholder" value="<?= $timesheet_comment_placeholder ?>" class="form-control">
						</div>
					</div>
					<?php $timesheet_time_format = get_config($dbc, 'timesheet_time_format'); ?>
					<div class="form-group">
						<label class="col-sm-4 control-label">Time Format:</label>
						<div class="col-sm-8">
							<label class="form-checkbox"><input type="radio" name="timesheet_time_format" value="" <?= empty($timesheet_time_format) ? 'checked' : '' ?>>HH:MM</label>
							<label class="form-checkbox"><input type="radio" name="timesheet_time_format" value="decimal" <?= $timesheet_time_format == 'decimal' ? 'checked' : '' ?>>Decimal</label>
						</div>
					</div>
					<?php $timesheet_record_history = get_config($dbc, 'timesheet_record_history'); ?>
					<div class="form-group">
						<label class="col-sm-4 control-label">Record History in Comments:</label>
						<div class="col-sm-8">
							<label class="form-checkbox"><input type="checkbox" name="timesheet_record_history" value="1" <?= $timesheet_record_history == 1 ? 'checked' : '' ?>> Enable</label>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-6">
			<a href="<?= $_GET['from_url'] ?>" class="btn config-btn btn-lg">Back</a>
		</div>
		<div class="col-sm-6">
			<button type="submit" name="submit" value="tabs" class="btn config-btn btn-lg pull-right">Submit</button>
		</div>
		<div class="clearfix"></div>
	</div>
<?php elseif($_GET['tab'] == 'day_tracking'): ?>
	<div class="panel-group" id="accordion2">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_time_tile" >
						Day Tracking<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_time_tile" class="panel-collapse collapse in">
				<div class="panel-body">
					<script>
					function disableTimeTracking(value) {
						if(value != '') {
							$('[type=checkbox][name=timesheet_start_tile]').removeAttr('checked');
						} else {
							$('[type=checkbox][name=timesheet_start_tile]').prop('checked',true);
						}
					}
					function displayClientCategory() {
						if($('[name="timesheet_track_clients"]').is(':checked')) {
							$('.timesheet_track_clients').show();
						} else {
							$('.timesheet_track_clients').hide();
						}
					}
					</script>
					<?php $timesheet_start_tile = get_config($dbc, 'timesheet_start_tile');
					echo "<label class='col-sm-4 control-label'>Start Time Tracking Tile:</label><div class='col-sm-8'><input type='text' name='timesheet_start_tile' value='$timesheet_start_tile' class='form-control' onchange='disableTimeTracking(this.value);'></div>";
					echo "<label class='col-sm-4'></label><label class='form-checkbox'><input ".($timesheet_start_tile == ''?'checked':'')." type='checkbox' name='timesheet_start_tile' value=''> Disable Start Time Tracking Tile</label><br>";
					$ticket_force_starts_day = get_config($dbc, 'ticket_force_starts_day');
					echo "<label class='col-sm-4'></label><label class='form-checkbox any-width'><input ".($ticket_force_starts_day > 0 ? 'checked' : '')." type='checkbox' name='ticket_force_starts_day' value='1'> Force Day Start when Checked In on ".TICKET_NOUN."</label><br>";
					$timesheet_break_option = get_config($dbc, 'timesheet_break_option');
					echo "<label class='col-sm-4'></label><label class='form-checkbox'><input ".($timesheet_break_option > 0 ? 'checked' : '')." type='checkbox' name='timesheet_break_option' value='1'> Enable Break Button</label><br>";
					$timesheet_track_shifts = get_config($dbc, 'timesheet_track_shifts');
					echo "<label class='col-sm-4'></label><label class='form-checkbox any-width'><input ".($timesheet_track_shifts == '1'?'checked':'')." type='checkbox' name='timesheet_track_shifts' value='1'> Track Time Based On Shifts (If Shift Exists)</label><br>";
					$timesheet_hide_others = get_config($dbc, 'timesheet_hide_others');
					echo "<label class='col-sm-4'></label><div class='col-sm-8'><label class='form-checkbox any-width'><input ".($timesheet_hide_others == '0'?'checked':'')." type='radio' name='timesheet_hide_others' value='0'> Allow Starting/Ending All User's Days</label>
						<label class='form-checkbox any-width'><input ".($timesheet_hide_others == '2'?'checked':'')." type='radio' name='timesheet_hide_others' value='2'> Allow Starting/Ending Other User's Days Within a Group</label>
						<label class='form-checkbox any-width'><input ".($timesheet_hide_others == '1'?'checked':'')." type='radio' name='timesheet_hide_others' value='1'> Don't Allow Starting/Ending Other User's Days</label></div><br>";
					$timesheet_always_show = get_config($dbc, 'timesheet_always_show');
					echo "<label class='col-sm-4'></label><label class='form-checkbox any-width'><input ".($timesheet_always_show == '1'?'checked':'')." type='checkbox' name='timesheet_always_show' value='1'> Show button on every page</label><br>";
					$timesheet_add_day_comment = get_config($dbc, 'timesheet_add_day_comment');
					echo "<label class='col-sm-4'></label><label class='form-checkbox any-width'><input ".($timesheet_add_day_comment == '0'?'checked':'')." type='radio' name='timesheet_add_day_comment' value='0'> No Day Tracking Comments</label>";
					echo "<label class='form-checkbox any-width'><input ".($timesheet_add_day_comment == '1'?'checked':'')." type='radio' name='timesheet_add_day_comment' value='1'> Add Comment About Day Ttracking</label>";
					echo "<label class='form-checkbox any-width'><input ".($timesheet_add_day_comment == '3'?'checked':'')." type='radio' name='timesheet_add_day_comment' value='3'> Use Task Types for Day Tracking</label>";
					echo "<label class='form-checkbox any-width'><input ".($timesheet_add_day_comment == '2'?'checked':'')." type='radio' name='timesheet_add_day_comment' value='2'> Preset Day Tracking Comment:</label>";
					echo "<div class='col-sm-8 pull-right'><input type='text' name='day_tracking_preset_note' value='".get_config($dbc, 'day_tracking_preset_note')."' class='form-control' placeholder='Preset Day Tracking Comment...'></div><div class='clearfix'></div>";
					$timesheet_direct_indirect = get_config($dbc, 'timesheet_direct_indirect');
					echo "<label class='col-sm-4'></label><div class='col-sm-8'><label class='form-checkbox any-width'><input ".(!($timesheet_direct_indirect > 0)?'checked':'')." type='radio' name='timesheet_direct_indirect' value='0'> Simple Time Tracking</label>
						<label class='form-checkbox any-width'><input ".($timesheet_direct_indirect == '1'?'checked':'')." type='radio' name='timesheet_direct_indirect' value='1'> Use Direct/Indirect Hours</label>
						<label class='form-checkbox any-width'><input ".($timesheet_direct_indirect == '2'?'checked':'')." type='radio' name='timesheet_direct_indirect' value='2'> Use Position by Day</label></div><br>";
					$timesheet_hide_groups = get_config($dbc, 'timesheet_hide_groups');
					echo "<label class='col-sm-4'></label><label class='form-checkbox any-width'><input ".($timesheet_hide_groups == '1'?'checked':'')." type='checkbox' name='timesheet_hide_groups' value='1' onchange='displayClientCategory();'> Hide Staff Groups</label><br>";
					$timesheet_hide_past_days = get_config($dbc, 'timesheet_hide_past_days');
					echo "<label class='col-sm-4'></label><label class='form-checkbox any-width'><input ".($timesheet_hide_past_days == '1'?'checked':'')." type='checkbox' name='timesheet_hide_past_days' value='1'> Hide ".TICKET_TILE." After Sign Out</label><br>";
					$timesheet_track_clients = get_config($dbc, 'timesheet_track_clients');
					echo "<label class='col-sm-4'></label><label class='form-checkbox any-width'><input ".($timesheet_track_clients == '1'?'checked':'')." type='checkbox' name='timesheet_track_clients' value='1' onchange='displayClientCategory();'> Track Clients</label><br>";
					$timesheet_client_category = get_config($dbc,'timesheet_client_category');
					echo "<div class='form-group timesheet_track_clients' ".($timesheet_track_clients != 1 ? 'style="display:none;"' : '').">";
					echo "<label class='col-sm-4 control-label'>Client Category:</label>";
					echo "<div class='col-sm-8'>";
					echo "<select data-placeholder='Select a Client Category...' name='timesheet_client_category' class='chosen-select-deselect form-control'>";
					echo "<option></option>";
					$category_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT `category` FROM `contacts` WHERE `deleted` = 0 AND `status` = 1 ORDER BY `category`"),MYSQLI_ASSOC);
                    foreach($category_list as $category) {
                    	echo "<option value='".$category['category']."' ".($timesheet_client_category == $category['category'] ? 'selected' : '').">".$category['category']."</option>";
                    }
					echo "</select>";
					echo "</div>";
					echo "</div>";
					$timesheet_running_button = get_config($dbc, 'timesheet_running_button');
					echo "<div class='clearfix'></div><label class='col-sm-4 control-label'>Active Time Tracking Button:<br /><em>Button in the top of the screen that notifies the user that they are tracking time. Leaving this blank will disable the button.</em></label>";
					echo "<div class='col-sm-8'><input type='text' name='timesheet_running_button' value='$timesheet_running_button' class='form-control'></div>";
					$active_ticket_button = get_config($dbc, 'active_ticket_button');
					echo "<div class='clearfix'></div><label class='col-sm-4 control-label'>Active ".TICKET_NOUN." Button:<br /><em>Button in the top of the screen that notifies the user that they are working on a ".TICKET_NOUN.".</em></label>";
					echo "<div class='col-sm-8'>
						<label class='form-checkbox'><input type='radio' name='active_ticket_button' ".($active_ticket_button == '' ? 'checked' : '')." value='' class='form-control'>Default: Running ".TICKET_NOUN." #</label>
						<label class='form-checkbox'><input type='radio' name='active_ticket_button' ".($active_ticket_button == 'ticket_label' ? 'checked' : '')." value='ticket_label' class='form-control'>".TICKET_NOUN." Label</label>
						<label class='form-checkbox'><input type='radio' name='active_ticket_button' ".($active_ticket_button == 'disable_active_ticket' ? 'checked' : '')." value='disable_active_ticket' class='form-control'>Disable ".TICKET_NOUN." Tracking Button</label>
					</div>";
					$timesheet_end_tile = get_config($dbc, 'timesheet_end_tile');
					echo "<div class='clearfix'></div><label class='col-sm-4 control-label'>End Time Tracking Tile:<br /><em>Only appears after Start Time Tracking Tile has started the time tracking.</em></label>";
					echo "<div class='col-sm-8'><input type='text' name='timesheet_end_tile' value='$timesheet_end_tile' class='form-control'></div>";
					$timesheet_force_end_midnight = get_config($dbc, 'timesheet_force_end_midnight');
					echo "<div class='clearfix'></div><label class='col-sm-4 control-label'>Force End Day at Midnight:</label>";
					echo "<div class='col-sm-8'><label class='form-checkbox'><input ".($timesheet_force_end_midnight > 0 ? 'checked' : '')." type='checkbox' name='timesheet_force_end_midnight' value='1'> Enable</label></div>";
					?>
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-6">
			<a href="<?= $_GET['from_url'] ?>" class="btn config-btn btn-lg">Back</a>
		</div>
		<div class="col-sm-6">
			<button type="submit" name="submit" value="day_tracking" class="btn config-btn btn-lg pull-right">Submit</button>
		</div>
		<div class="clearfix"></div>
	</div>

<?php elseif($_GET['tab'] == 'reporting'):
	$timesheet_reporting_styling = get_config($dbc,'timesheet_reporting_styling');
    ?>
	<div class="panel-group" id="accordion2">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_managers" >
						Reporting Format<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_managers" class="panel-collapse collapse">
				<div class="panel-body">

                    <div class="form-group">
                      <label for="site_name" class="col-sm-4 control-label">Styling:</label>
                      <div class="col-sm-8">
                        <label class="form-checkbox"><input type="radio" name="timesheet_reporting_styling" <?= $timesheet_reporting_styling == 'Default' ? 'checked' : '' ?> data-table="tickets" data-id="<?= $timesheet_reporting_styling ?>" data-id-field="timesheet_reporting_styling" class="form-control" value="Default"> Default</label>
                        <label class="form-checkbox"><input type="radio" name="timesheet_reporting_styling" <?= $timesheet_reporting_styling == 'EGS' ? 'checked' : '' ?> data-table="tickets" data-id="<?= $timesheet_reporting_styling ?>" data-id-field="timesheet_reporting_styling" class="form-control" value="EGS"> Total Time Tracked</label>
                      </div>
                    </div>

				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-6">
			<a href="<?= $_GET['from_url'] ?>" class="btn config-btn btn-lg">Back</a>
		</div>
		<div class="col-sm-6">
			<button type="submit" name="submit" value="reporting" class="btn config-btn btn-lg pull-right">Submit</button>
		</div>
		<div class="clearfix"></div>
	</div>

<?php elseif($_GET['tab'] == 'payroll'):
	$timesheet_payroll_styling = get_config($dbc,'timesheet_payroll_styling');
    ?>
    <script type="text/javascript">
    function displayEGSOptions() {
    	if($('[name="timesheet_payroll_styling"]:checked').val() == 'EGS' || $('[name="timesheet_payroll_styling"]:checked').val() == 'Both') {
    		$('.egs_div').show();
    	} else {
    		$('.egs_div').hide();
    	}
    }
    </script>
	<div class="panel-group" id="accordion2">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_managers2" >
						Payroll Format<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_managers2" class="panel-collapse collapse">
				<div class="panel-body">

                    <div class="form-group">
                      <label for="site_name" class="col-sm-4 control-label">Styling:</label>
                      <div class="col-sm-8">
                        <label class="form-checkbox"><input type="radio" name="timesheet_payroll_styling" <?= $timesheet_payroll_styling == 'Default' ? 'checked' : '' ?> data-table="tickets" data-id="<?= $timesheet_payroll_styling ?>" data-id-field="timesheet_payroll_styling" class="form-control" value="Default" onchange="displayEGSOptions();"> Default</label>
                        <label class="form-checkbox"><input type="radio" name="timesheet_payroll_styling" <?= $timesheet_payroll_styling == 'EGS' ? 'checked' : '' ?> data-table="tickets" data-id="<?= $timesheet_payroll_styling ?>" data-id-field="timesheet_payroll_styling" class="form-control" value="EGS" onchange="displayEGSOptions();"> Total Time Tracked</label>
                        <label class="form-checkbox"><input type="radio" name="timesheet_payroll_styling" <?= $timesheet_payroll_styling == 'Both' ? 'checked' : '' ?> data-table="tickets" data-id="<?= $timesheet_payroll_styling ?>" data-id-field="timesheet_payroll_styling" class="form-control" value="Both" onchange="displayEGSOptions();"> Both as Tabs</label>
                      </div>
                    </div>

                    <div class="egs_div" <?= $timesheet_payroll_styling != 'EGS' && $timesheet_payroll_styling != 'Both' ? 'style="display:none;"' : '' ?>>
	                    <div class="form-group">
	                    	<?php $timesheet_payroll_layout = get_config($dbc, 'timesheet_payroll_layout'); ?>
	                    	<label class="col-sm-4 control-label">Layout:</label>
	                    	<div class="col-sm-8">
	                    		<label class="form-checkbox"><input type="radio" name="timesheet_payroll_layout" value="" <?= $timesheet_payroll_layout != 'group_days' ? 'checked' : '' ?>> Multiple Lines per Day</label>
	                    		<label class="form-checkbox"><input type="radio" name="timesheet_payroll_layout" value="group_days" <?= $timesheet_payroll_layout == 'group_days' ? 'checked' : '' ?>> Group Days Together</label>
	                    	</div>
	                    </div>
	                    <div class="form-group">
	                    	<?php $timesheet_payroll_overtime = get_config($dbc, 'timesheet_payroll_overtime'); ?>
	                    	<label class="col-sm-4 control-label">Over Time Hours:</label>
	                    	<div class="col-sm-8">
	                    		<input type="number" name="timesheet_payroll_overtime" class="form-control" value="<?= $timesheet_payroll_overtime ?>" step="0.01">
	                    	</div>
	                    </div>
	                    <div class="form-group">
	                    	<?php $timesheet_payroll_doubletime = get_config($dbc, 'timesheet_payroll_doubletime'); ?>
	                    	<label class="col-sm-4 control-label">Double Time Hours:</label>
	                    	<div class="col-sm-8">
	                    		<input type="number" name="timesheet_payroll_doubletime" class="form-control" value="<?= $timesheet_payroll_doubletime ?>" step="0.01">
	                    	</div>
	                    </div>
	                </div>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_payroll_fields" >
						Payroll Fields<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_payroll_fields" class="panel-collapse collapse">
				<div class="panel-body">

                    <div class="form-group">
						<label for="site_name" class="col-sm-4 control-label">Payroll Fields:</label>
						<div class="col-sm-8">
							<?php $timesheet_payroll_fields = ','.get_config($dbc, 'timesheet_payroll_fields').','; ?>
							<label class="form-checkbox"><input type="checkbox" name="timesheet_payroll_fields[]" value="Expenses Owed" <?= strpos($timesheet_payroll_fields, ',Expenses Owed,') !== FALSE ? 'checked' : '' ?>> Expenses Owed</label>
							<label class="form-checkbox"><input type="checkbox" name="timesheet_payroll_fields[]" value="Mileage" <?= strpos($timesheet_payroll_fields, ',Mileage,') !== FALSE ? 'checked' : '' ?>> Mileage</label>
							<label class="form-checkbox"><input type="checkbox" name="timesheet_payroll_fields[]" value="Mileage Rate" <?= strpos($timesheet_payroll_fields, ',Mileage Rate,') !== FALSE ? 'checked' : '' ?>> Mileage Rate</label>
							<label class="form-checkbox"><input type="checkbox" name="timesheet_payroll_fields[]" value="Mileage Total" <?= strpos($timesheet_payroll_fields, ',Mileage Total,') !== FALSE ? 'checked' : '' ?>> Mileage Total</label>
						</div>
                    </div>

				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-6">
			<a href="<?= $_GET['from_url'] ?>" class="btn config-btn btn-lg">Back</a>
		</div>
		<div class="col-sm-6">
			<button type="submit" name="submit" value="payroll" class="btn config-btn btn-lg pull-right">Submit</button>
		</div>
		<div class="clearfix"></div>
	</div>

<?php elseif($_GET['tab'] == 'holiday'): ?>
	<div class="panel-group" id="accordion2">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_holiday_noti" >
						Statutory Holidays Update Notification<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_holiday_noti" class="panel-collapse collapse">
				<div class="panel-body">

					<div class="form-group">
						<label class="col-sm-4 control-label">Enable Notifications:</label>
						<div class="col-sm-8">
							<?php $holiday_update_noti = get_config($dbc, 'holiday_update_noti'); ?>
							<label class="form-checkbox"><input type="checkbox" name="holiday_update_noti" value="1" onchange="if($(this).is(':checked')) { $('#holiday_update_div').show(); } else { $('#holiday_update_noti_div').hide(); }" <?= $holiday_update_noti == 1 ? 'checked' : '' ?>></label>
						</div>
					</div>

					<div id="holiday_update_div" <?= $holiday_update_noti == 1 ? '' : 'style="display:none;"' ?>>
						<div class="form-group">
							<label class="col-sm-4 control-label">Staff:</label>
							<div class="col-sm-8">
								<select name="holiday_update_staff" class="chosen-select-deselect form-control">
									<option></option>
									<?php $holiday_update_staff = get_config($dbc, 'holiday_update_staff');
									$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 AND `status` > 0 AND `show_hide_user` > 0"));
									foreach($staff_list as $staff) {
										echo '<option value="'.$staff['contactid'].'" '.($staff['contactid'] == $holiday_update_staff ? 'selected' : '').'>'.$staff['full_name'].'</option>';
									} ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-4 control-label">Date (MM-DD):<br><em>Notifications will be sent weekly from this date each year until they are turned off.</em></label>
							<div class="col-sm-8">
								<?php $holiday_update_date = get_config($dbc, 'holiday_update_date'); ?>
								<input type="text" name="holiday_update_date" class="form-control datepickernoyear" value="<?= $holiday_update_date ?>">
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-6">
			<a href="<?= $_GET['from_url'] ?>" class="btn config-btn btn-lg">Back</a>
		</div>
		<div class="col-sm-6">
			<button type="submit" name="submit" value="holiday" class="btn config-btn btn-lg pull-right">Submit</button>
		</div>
		<div class="clearfix"></div>
	</div>
<?php endif; ?>

</form>
</div>
</div>

<?php include ('../footer.php'); ?>