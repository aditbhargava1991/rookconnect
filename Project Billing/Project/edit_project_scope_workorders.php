<?php error_reporting(0);
include_once('../include.php');
if(!isset($projectid)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
		if($tile == 'project' || $tile == config_safe_str($type_name)) {
			$project_tabs[config_safe_str($type_name)] = $type_name;
		}
	}
}
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
$workorder_security = get_security($dbc, 'work_order');
$_GET['status'] = empty($_GET['status']) ? 'assigned' : $_GET['status']; ?>
<h3><!-- Work Orders -->&nbsp;
	<?php if($workorder_security['edit'] > 0) { ?>
		<a href="../Work Order/add_workorder.php?projectid=<?= $projectid ?>&from=<?= urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']) ?>" class="btn brand-btn pull-right">New Work Order</a>
	<?php } ?>
	<a href="?edit=<?= $projectid ?>&tab=workorders&status=archive" class="hide-titles-mob <?= $_GET['status'] == 'archive' ? 'active_tab' : '' ?> btn brand-btn pull-right">Archived</a>
	<a href="?edit=<?= $projectid ?>&tab=workorders&status=assigned" class="hide-titles-mob <?= $_GET['status'] == 'assigned' ? 'active_tab' : '' ?> btn brand-btn pull-right">Scheduled</a>
	<a href="?edit=<?= $projectid ?>&tab=workorders&status=unassigned" class="hide-titles-mob <?= $_GET['status'] == 'unassigned' ? 'active_tab' : '' ?> btn brand-btn pull-right">Unassigned</a></h3>
    <div class="notice double-gap-top double-gap-bottom popover-examples">
        <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL ?>/img/info.png" class="wiggle-me" width="25"></div>
        <div class="col-sm-11"><span class="notice-name">NOTE: </span>View and edit all work orders that have been created for this project from here.</div>
        <div class="clearfix"></div>
    </div>
<?php $workorder_status = explode(',',get_config($dbc,'workorder_status'));
$milestones = explode('#*#',mysqli_fetch_array(mysqli_query($dbc, "SELECT `milestone` FROM `project_path_milestone` WHERE `project_path`='".$project['project_path']."'"))['milestone']);
$tab_filter = '';
if($_GET['status'] == 'archive') {
	$tab_filter = "AND `status`='Archive'";
} else if($_GET['status'] == 'assigned') {
	$tab_filter = "AND `status`!='Archive' AND (`status` != '' AND IFNULL(milestone,'') IN ('".implode("','",$milestones)."') AND IFNULL(to_do_date,'0000-00-00') != '0000-00-00' AND REPLACE(IFNULL(contactid,''),',','') != '')";
} else if($_GET['status'] == 'unassigned') {
	$tab_filter = "AND `status`!='Archive' AND (`status` = '' OR IFNULL(milestone,'') NOT IN ('".implode("','",$milestones)."') OR IFNULL(to_do_date,'0000-00-00') = '0000-00-00' OR REPLACE(IFNULL(contactid,''),',','') = '')";
}
$workorders = mysqli_query($dbc, "SELECT * FROM `workorder` WHERE `projectid`='$projectid' AND `deleted`=0 ".$tab_filter);
while($workorder = mysqli_fetch_array($workorders)) { ?>
	<div class="dashboard-item form-horizontal">
		<h3><a href="../Work Order/add_workorder.php?workorderid=<?= $workorder['workorderid'] ?>&from=<?= urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']) ?>">Work Order #<?= $workorder['workorderid'] ?></a>
		<span class="pull-right small">
		<?php foreach(explode(',',$ticket['contactid']) as $assignid) {
			if($assignid > 0) {
				profile_id($dbc, $assignid);
			}
		} ?>
		</span><div class="clearfix"></div></h3>
		<div class="col-sm-4 form-group">
			<label class="col-sm-4">Heading:</label>
			<div class="col-sm-8"><?= $workorder['heading'] ?></div>
		</div>
		<div class="col-sm-4 form-group">
			<label class="col-sm-4">Status:</label>
			<div class="col-sm-8">
				<select name="status" data-table="workorder" data-id-field="workorderid" data-id="<?= $workorder['workorderid'] ?>" class="chosen-select-deselect">
					<option></option>
					<?php foreach($workorder_status as $status) { ?>
						<option <?= $workorder['status'] == $status ? 'selected' : '' ?> value="<?= $status ?>"><?= $status ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="col-sm-4 form-group">
			<label class="col-sm-4">To Do Date:</label>
			<div class="col-sm-8">
				<input type="text" name="to_do_date" data-table="workorder" data-id-field="workorderid" data-id="<?= $workorder['workorderid'] ?>" value="<?= $workorder['to_do_date'] ?>" class="datepicker form-control">
			</div>
		</div>
		<div class="col-sm-4 form-group">
			<label class="col-sm-4">Service:</label>
			<div class="col-sm-8"><?= $workorder['service'] ?></div>
		</div>
		<div class="col-sm-4 form-group">
			<label class="col-sm-4">Staff:</label>
			<div class="col-sm-8"><?php $staff_list = [];
			foreach(array_filter(array_unique(explode(',',$workorder['contactid']))) as $staffid) {
				$staff_list[] = get_contact($dbc, $staffid);
			}
			echo implode(', ',$staff_list); ?></div>
		</div>
		<div class="col-sm-4 form-group">
			<label class="col-sm-4">Deliverable Date:</label>
			<div class="col-sm-8">
				<input type="text" name="deliverable_date" data-table="workorder" data-id-field="workorderid" data-id="<?= $workorder['workorderid'] ?>" value="<?= $workorder['deliverable_date'] ?>" class="datepicker form-control">
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
<?php } ?>
<?php include('next_buttons.php'); ?>