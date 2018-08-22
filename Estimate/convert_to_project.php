<?php include('../include.php');
checkAuthorised('estimate');
error_reporting(0);
$estimate = filter_var($_GET['estimate'], FILTER_SANITIZE_STRING);
$status = preg_replace('/[^a-z]/','',strtolower(get_config($dbc, 'estimate_project_status')));
$projectid = $_GET['projectid'];
$max_sort = 0;
if($projectid > 0) {
	$max_sort = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT MAX(`sort_order`) max_sort FROM `project_scope` WHERE `projectid`='$projectid'"))['max_sort'];

	$user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
	mysqli_query($dbc, "INSERT INTO `project_history` (`updated_by`, `description`, `projectid`) VALUES ('$user', 'Estimate #$estimate attached to ".PROJECT_NOUN."', '$projectid')");
} else {
	mysqli_query($dbc, "INSERT INTO `project` (`project_name`,`created_date`,`created_by`,`start_date`,`businessid`,`clientid`,`siteid`,`afe_number`,`projecttype`) SELECT `estimate_name`,'".date('Y-m-d')."','".$_SESSION['contactid']."',`businessid`,`clientid`,`start_date`,`siteid`,`afe_number`,`estimatetype` FROM `estimate` WHERE `estimateid`='$estimate'");
	$projectid = mysqli_insert_id($dbc);
	$user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
	mysqli_query($dbc, "INSERT INTO `project_history` (`updated_by`, `description`, `projectid`) VALUES ('$user', 'Created from Estimate #$estimate', '$projectid')");

	mysqli_query($dbc, "INSERT INTO `project_detail` (`projectid`,`detail_issue`,`detail_problem`,`detail_gap`,`detail_technical_uncertainty`,`detail_base_knowledge`,`detail_do`,`detail_already_known`,`detail_sources`,`detail_current_designs`,`detail_known_techniques`,`detail_review_needed`,`detail_looking_to_achieve`,`detail_plan`,`detail_next_steps`,`detail_learnt`,`detail_discovered`,
			`detail_tech_advancements`,`detail_work`,`detail_adjustments_needed`,`detail_future_designs`,`detail_objective`,`detail_targets`,`detail_audience`,`detail_strategy`,`detail_desired_outcome`,`detail_actual_outcome`,`detail_check`)
		SELECT '$projectid',`detail_issue`,`detail_problem`,`detail_gap`,`detail_technical_uncertainty`,`detail_base_knowledge`,`detail_do`,`detail_already_known`,`detail_sources`,`detail_current_designs`,`detail_known_techniques`,`detail_review_needed`,`detail_looking_to_achieve`,`detail_plan`,`detail_next_steps`,`detail_learnt`,`detail_discovered`,
			`detail_tech_advancements`,`detail_work`,`detail_adjustments_needed`,`detail_future_designs`,`detail_objective`,`detail_targets`,`detail_audience`,`detail_strategy`,`detail_desired_outcome`,`detail_actual_outcome`,`detail_check` FROM `estimate_detail` WHERE `estimateid`='$estimate'");
}
mysqli_query($dbc, "INSERT INTO `project_scope` (`projectid`,`estimateline`,`heading`,`description`,`src_table`,`src_id`,`rate_card`,`uom`,`qty`,`cost`,`profit`,`margin`,`price`,`retail`,`multiple`,`sort_order`)
	SELECT '$projectid',`id`,`heading`,`description`,`src_table`,`src_id`,`rate_card`,`uom`,`qty`,`cost`,`profit`,`margin`,`price`,`retail`,`multiple`,`sort_order`+$max_sort FROM `estimate_scope` WHERE `estimateid`='$estimate' AND `deleted`=0");
mysqli_query($dbc, "UPDATE `estimate` SET `projectid`='$projectid', `status`='$status', `status_date`=DATE(NOW()) WHERE `estimateid`='$estimate'");
$before_change = '';
$history = "Estimates detail entry has been updated for estimate id $estimate. <br />";
add_update_history($dbc, 'estimates_history', $history, '', $before_change);
?>
<script>
window.location.replace('../Project/projects.php?edit=<?= $projectid ?>');
</script>
