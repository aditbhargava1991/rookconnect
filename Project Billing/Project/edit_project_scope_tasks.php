<?php error_reporting(0);
include_once('../include.php');
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
if(!isset($projectid)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
		if($tile == 'project' || $tile == config_safe_str($type_name)) {
			$project_tabs[config_safe_str($type_name)] = $type_name;
		}
	}
}
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
$task_security = get_security($dbc, 'tasks');
$_GET['status'] = empty($_GET['status']) ? 'assigned' : $_GET['status']; ?>
<h3><!-- Tasks -->&nbsp;
	<?php if($task_security['edit'] > 0) { ?>
		<a href="../Tasks_Updated/add_task.php?projectid=<?= $projectid ?>&from=<?= urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']) ?>" class="btn brand-btn pull-right">New Task</a>
	<?php } ?>
	<a href="?edit=<?= $projectid ?>&tab=tasks&status=archive" class="hide-titles-mob <?= $_GET['status'] == 'archive' ? 'active_tab' : '' ?> btn brand-btn pull-right">Archived</a>
	<a href="?edit=<?= $projectid ?>&tab=tasks&status=assigned" class="hide-titles-mob <?= $_GET['status'] == 'assigned' ? 'active_tab' : '' ?> btn brand-btn pull-right">Scheduled</a>
	<a href="?edit=<?= $projectid ?>&tab=tasks&status=unassigned" class="hide-titles-mob <?= $_GET['status'] == 'unassigned' ? 'active_tab' : '' ?> btn brand-btn pull-right">Unassigned</a></h3>
    <div class="notice double-gap-top double-gap-bottom popover-examples">
        <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL ?>/img/info.png" class="wiggle-me" width="25"></div>
        <div class="col-sm-11"><span class="notice-name">NOTE: </span>View and edit all tasks that have been created for this project from here.</div>
        <div class="clearfix"></div>
    </div>
<?php $task_status = explode(',',get_config($dbc,'task_status'));
$milestones = explode('#*#',mysqli_fetch_array(mysqli_query($dbc, "SELECT `milestone` FROM `project_path_milestone` WHERE `project_path`='".$project['project_path']."'"))['milestone']);
$tab_filter = '';
if($_GET['status'] == 'archive') {
	$tab_filter = "AND `status`='Archive'";
} else if($_GET['status'] == 'assigned') {
	$tab_filter = "AND (`status` != '' AND IFNULL(project_milestone,'') IN ('".implode("','",$milestones)."') AND IFNULL(task_tododate,'0000-00-00') != '0000-00-00' AND IFNULL(created_by,'') != '')";
} else if($_GET['status'] == 'unassigned') {
	$tab_filter = "AND (`status` = '' OR IFNULL(project_milestone,'') NOT IN ('".implode("','",$milestones)."') OR IFNULL(task_tododate,'0000-00-00') = '0000-00-00' OR IFNULL(created_by,'') = '')";
}
$tasks = mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `projectid`='$projectid' AND `deleted`=0 ".$tab_filter);
while($task = mysqli_fetch_array($tasks)) { ?>
	<div class="dashboard-item form-horizontal">
		<h3><!--<a href="../Tasks/add_task.php?tasklistid=<?= $task['tasklistid'] ?>&from=<?= urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']) ?>">Task #<?= $task['tasklistid'] ?></a>-->
        <a href="" onclick="overlayIFrameSlider('../Tasks_Updated/add_task.php?tasklistid=<?= $task['tasklistid'] ?>', '50%', false, true, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;">Task #<?= $task['tasklistid'] ?></a>
		<span class="pull-right small">
		<?php foreach(explode(',',$task['contactid']) as $assignid) {
			if($assignid > 0) {
				profile_id($dbc, $assignid);
			}
		} ?>
		</span><div class="clearfix"></div></h3>
		<div class="col-sm-4 form-group">
			<label class="col-sm-4">Heading:</label>
			<div class="col-sm-8"><?= $task['heading'] ?></div>
		</div>
		<div class="col-sm-4 form-group">
			<label class="col-sm-4">Status:</label>
			<div class="col-sm-8">
				<select name="status" data-table="tasklist" data-id-field="tasklistid" data-id="<?= $task['tasklistid'] ?>" class="chosen-select-deselect">
					<option></option>
					<?php foreach($task_status as $status) { ?>
						<option <?= $task['status'] == $status ? 'selected' : '' ?> value="<?= $status ?>"><?= $status ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="col-sm-4 form-group">
			<label class="col-sm-4">To Do Date:</label>
			<div class="col-sm-8">
				<input type="text" name="task_tododate" data-table="tasklist" data-id-field="tasklistid" data-id="<?= $task['tasklistid'] ?>" value="<?= $task['task_tododate'] ?>" class="datepicker form-control">
			</div>
		</div>
		<div class="col-sm-8 form-group">
			<label class="col-sm-2">Task:</label>
			<div class="col-sm-10 small"><?= html_entity_decode($task['task']) ?></div>
		</div>
		<div class="col-sm-4 form-group">
			<label class="col-sm-4">Staff:</label>
			<div class="col-sm-8"><?= get_contact($dbc, $task['contactid']) ?></div>
		</div>
		<div class="clearfix"></div>
	</div>
<?php } ?>
<?php include('next_buttons.php'); ?>