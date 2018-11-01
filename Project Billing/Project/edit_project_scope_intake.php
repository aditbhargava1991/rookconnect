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
$project_security = get_security($dbc, 'project');
$form_count = 0; ?>
<!-- <h3>Intake Forms</h3> -->
<ul>
<?php
$forms = mysqli_query($dbc, "SELECT * FROM `intake` WHERE `deleted` = 0 AND `projectid` = '$projectid'");
while($form = mysqli_fetch_assoc($forms)) {
	$intake_form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake_forms` WHERE `intakeformid` = '".$form['intakeformid']."'"));
	$form_count++; ?>
	<li><a href="../Intake/<?= $form['intake_file'] ?>">Intake #<?= $form['intakeid'] ?>: <?= !empty($intake_form['form_name']) ? $intake_form['form_name'].':' : '' ?> <?= !empty($form['contactid']) ? get_contact($dbc, $form['contactid']) : (!empty($form['name']) ? $form['name'] : 'No Contact') ?>: <?= $form['received_date'] ?></a></li>
<?php } ?>
</ul>
<?php if($form_count == 0) {
	echo "<h2>No Intake Forms Found</h2>";
} ?>
<?php include('next_buttons.php'); ?>