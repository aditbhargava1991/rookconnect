<?php include_once('../include.php');
ob_clean();

if($_GET['action'] == 'admin_status') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$status = filter_var($_POST['status'],FILTER_SANITIZE_STRING);
	$user = $_SESSION['contactid'];
	$dbc->query("UPDATE `incident_report` SET `status`='$status', `approved_by`='$user' WHERE `incidentreportid`='$id'");
} else if($_GET['action'] == 'manager_status') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$status = filter_var($_POST['status'],FILTER_SANITIZE_STRING);
	$user = $_SESSION['contactid'];
	$dbc->query("UPDATE `incident_report` SET `manager_status`='$status', `manager_approved_by`='$user' WHERE `incidentreportid`='$id'");
} else if($_GET['action'] == 'quick_actions') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	if($field == 'flag_colour') {
		$colours = explode(',', get_config($dbc, "ticket_colour_flags"));
		$labels = explode('#*#', get_config($dbc, "ticket_colour_flag_names"));
		$colour_key = array_search($value, $colours);
		$new_colour = ($colour_key === FALSE ? $colours[0] : ($colour_key + 1 < count($colours) ? $colours[$colour_key + 1] : 'FFFFFF'));
		$label = ($colour_key === FALSE ? $labels[0] : ($colour_key + 1 < count($colours) ? $labels[$colour_key + 1] : ''));
		echo $new_colour.html_entity_decode($label);
		mysqli_query($dbc, "UPDATE `incident_report` SET `flag_colour`='$new_colour', `flag_start`='0000-00-00', `flag_end`='9999-12-31' WHERE `incidentreportid`='$id'");
	}
}