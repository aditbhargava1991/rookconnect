<?php include('../include.php');
ob_clean();
$key = strtolower($_POST['key']);
$from = urlencode($_POST['url']); ?>
<h3>Search Results for <?= $_POST['key'] ?></h3>
<button class="btn brand-btn pull-right" onclick="$('.search_list').val('').keyup().focus(); return false;">Clear Search</button><br /><div class="clearfix"></div>
<?php $projectid = filter_var($_POST['project'],FILTER_SANITIZE_STRING);
$project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
$projecttype = $project['project_type'];
$tab_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_tabs` FROM field_config_project WHERE type='$projecttype'"))['config_tabs']),explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_tabs` FROM field_config_project WHERE type='ALL'"))['config_tabs']))));
if(count($tab_config) == 0) {
	$tab_config = explode(',','Path,Information,Details,Documents,Dates,Scope,Estimates,Tickets,Work Orders,Tasks,Checklists,Email,Phone,Reminders,Agendas,Meetings,Gantt,Profit,Report Checklist,Billing,Field Service Tickets,Purchase Orders,Invoices');
}
if(in_array('Path',$tab_config)) {
	foreach(explode('#*#',mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`milestone` SEPARATOR '#*#') `milestones` FROM `project_path_milestone` WHERE `project_path_milestone` IN (".($project['project_path'] == '' ? 0 : $project['project_path']).")"))['milestones']) as $milestone) {
		if(strpos(strtolower($milestone),$key) !== FALSE) { ?>
			<a href="?edit=<?= $projectid ?>&tab=path_<?= config_safe_str($milestone) ?>"><div class="dashboard-item"><h3 class="no-margin pad-5"><?= PROJECT_NOUN ?> Path Milestone <?= $milestone ?></h3></div></a>
		<?php }
	}
}
if(in_array('External Path',$tab_config)) {
	foreach(explode('#*#',mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`milestone` SEPARATOR '#*#') `milestones` FROM `project_path_milestone` WHERE `project_path_milestone` IN (".($project['external_path'] == '' ? 0 : $project['external_path']).")"))['milestones']) as $milestone) {
		if(strpos(strtolower($milestone),$key) !== FALSE) { ?>
			<a href="?edit=<?= $projectid ?>&tab=path_external_path_<?= config_safe_str($milestone) ?>"><div class="dashboard-item"><h3 class="no-margin pad-5">External <?= PROJECT_NOUN ?> Path Milestone <?= $milestone ?></h3></div></a>
		<?php }
	}
}
if(in_array('Notes',$tab_config)) {
	$notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `notes` FROM `project_comment` WHERE `type`='project_note' AND `projectid`='6' AND `comment` LIKE '%$key%'"))['notes'];
	if($notes > 0) { ?>
		<a href="?edit=<?= $projectid ?>&tab=notes"><div class="dashboard-item"><h3 class="no-margin pad-5"><?= PROJECT_NOUN ?> Notes: <?= $notes ?> matches</h3></div></a>
	<?php }
}
if(in_array('Details',$tab_config)) {
	$details = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `details` FROM (SELECT `detail_issue` FROM `project_detail` WHERE `detail_issue` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_problem` FROM `project_detail` WHERE `detail_problem` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_gap` FROM `project_detail` WHERE `detail_gap` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_technical_uncertainty` FROM `project_detail` WHERE `detail_technical_uncertainty` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_base_knowledge` FROM `project_detail` WHERE `detail_base_knowledge` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_do` FROM `project_detail` WHERE `detail_do` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_already_known` FROM `project_detail` WHERE `detail_already_known` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_sources` FROM `project_detail` WHERE `detail_sources` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_current_designs` FROM `project_detail` WHERE `detail_current_designs` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_known_techniques` FROM `project_detail` WHERE `detail_known_techniques` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_review_needed` FROM `project_detail` WHERE `detail_review_needed` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_looking_to_achieve` FROM `project_detail` WHERE `detail_looking_to_achieve` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_plan` FROM `project_detail` WHERE `detail_plan` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_next_steps` FROM `project_detail` WHERE `detail_next_steps` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_learnt` FROM `project_detail` WHERE `detail_learnt` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_discovered` FROM `project_detail` WHERE `detail_discovered` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_tech_advancements` FROM `project_detail` WHERE `detail_tech_advancements` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_work` FROM `project_detail` WHERE `detail_work` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_adjustments_needed` FROM `project_detail` WHERE `detail_adjustments_needed` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_future_designs` FROM `project_detail` WHERE `detail_future_designs` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_objective` FROM `project_detail` WHERE `detail_objective` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_targets` FROM `project_detail` WHERE `detail_targets` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_audience` FROM `project_detail` WHERE `detail_audience` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_strategy` FROM `project_detail` WHERE `detail_strategy` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_desired_outcome` FROM `project_detail` WHERE `detail_desired_outcome` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_actual_outcome` FROM `project_detail` WHERE `detail_actual_outcome` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_check` FROM `project_detail` WHERE `detail_check` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail2_issue` FROM `project_detail` WHERE `detail2_issue` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail2_plan` FROM `project_detail` WHERE `detail2_plan` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail2_do` FROM `project_detail` WHERE `detail2_do` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail2_check` FROM `project_detail` WHERE `detail2_check` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail2_adjust` FROM `project_detail` WHERE `detail2_adjust` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_procedure_id` FROM `project_detail` WHERE `detail_procedure_id` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_quote` FROM `project_detail` WHERE `detail_quote` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_dwg` FROM `project_detail` WHERE `detail_dwg` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_quantity` FROM `project_detail` WHERE `detail_quantity` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_sn` FROM `project_detail` WHERE `detail_sn` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_total_project_budget` FROM `project_detail` WHERE `detail_total_project_budget` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `detail_detail` FROM `project_detail` WHERE `detail_detail` LIKE '%$key%' AND `projectid`='$projectid' UNION
		SELECT `comment` FROM `project_comment` WHERE `type`!='project_note' AND `projectid`='$projectid' AND `comment` LIKE '%$key%') `detail_list`"))['details'];
	if($details > 0) { ?>
		<a href="?edit=<?= $projectid ?>&tab=details"><div class="dashboard-item"><h3 class="no-margin pad-5"><?= PROJECT_NOUN ?> Details: <?= $details ?> matches</h3></div></a>
	<?php }
}
if(in_array('Documents',$tab_config)) {
	$docs = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `docs` FROM `project_document` WHERE `deleted`=0 AND `projectid`='$projectid' AND CONCAT(IFNULL(`upload`,''),IFNULL(`label`,''),IFNULL(`link`,'')) LIKE '%$key%'"))['docs'];
	if($docs > 0) { ?>
		<a href="?edit=<?= $projectid ?>&tab=documents"><div class="dashboard-item"><h3 class="no-margin pad-5">Documents: <?= $docs ?> matches</h3></div></a>
	<?php }
}
if(in_array('Tickets',$tab_config)) {
	$tickets = mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `projectid`='$projectid' AND `deleted`=0 AND CONCAT(IFNULL(`ticketid`,''),IFNULL(`status`,''),IFNULL(`heading`,''),IFNULL(`assign_work`,''),IFNULL(`to_do_date`,'')) LIKE '%$key%'");
	while($ticket = mysqli_fetch_assoc($tickets)) { ?>
		<a href="../Ticket/index.php?edit=<?= $ticket['ticketid'] ?>&from=<?= $from ?>"><div class="dashboard-item"><h3 class="no-margin pad-5"><?= get_ticket_label($dbc, $ticket) ?></h3></div></a>
	<?php }
}
if(in_array('Tasks',$tab_config)) {
	$tasks = mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `projectid`='$projectid' AND `deleted`=0 AND CONCAT(IFNULL(`tasklistid`,''),IFNULL(`status`,''),IFNULL(`heading`,'')) LIKE '%$key%'");
	while($task = mysqli_fetch_assoc($tasks)) { ?>
		<a href="" onclick="overlayIFrameSlider('../Tasks_Updated/add_task.php?type=&tasklistid=<?= $task['tasklistid'] ?>', '75%', true, true); return false;"><div class="dashboard-item"><h3 class="no-margin pad-5">Task #<?= $task['tasklistid'] ?> <?= $task['heading'] ?></h3></div></a>
	<?php }
}
if(in_array('Email',$tab_config)) {
	$emails = mysqli_query($dbc, "SELECT * FROM `email_communication` WHERE `projectid`='$projectid' AND `deleted`=0 AND CONCAT(IFNULL(`communication_type`,''),IFNULL(`subject`,''),IFNULL(`email_body`,''),IFNULL(`to_staff`,''),IFNULL(`cc_staff`,''),IFNULL(`to_contact`,''),IFNULL(`cc_contact`,''),IFNULL(`new_emailid`,''),IFNULL(`status`,''),IFNULL(`today_date`,''),IFNULL(`from_email`,'')) LIKE '%$key%'");
	while($email = mysqli_fetch_assoc($emails)) { ?>
		<a href="../Email Communication/add_communication.php?type=<?= $email['communication_type'] ?>&email_communicationid=<?= $email['email_communicationid'] ?>&from_url=<?= $from ?>"><div class="dashboard-item"><h3 class="no-margin pad-5">Email: <?= $email['subject'] ?> sent on <?= $email['today_date'] ?></h3></div></a>
	<?php }
}
if(in_array('Phone',$tab_config)) {
	$calls = mysqli_query($dbc, "SELECT * FROM `phone_communication` WHERE `projectid`='$projectid' AND `deleted`=0 AND CONCAT(IFNULL(`communication_type`,''),IFNULL(`comment`,''),IFNULL(`status`,''),IFNULL(`doc`,'')) LIKE '%$key%'");
	while($call = mysqli_fetch_assoc($calls)) { ?>
		<a href="../Phone Communication/add_communication.php?type=<?= $call['communication_type'] ?>&phone_communicationid=<?= $call['phone_communicationid'] ?>&from_url=<?= $from ?>"><div class="dashboard-item"><h3 class="no-margin pad-5">Phone Call: <?= $call['doc'] ?></h3></div></a>
	<?php }
}
if(in_array('Agendas',$tab_config) || in_array('Meetings',$tab_config)) {
	$meetings = mysqli_query($dbc, "SELECT * FROM `agenda_meeting` WHERE `projectid`='$projectid' AND CONCAT(IFNULL(`date_of_meeting`,''),IFNULL(`location`,''),IFNULL(`meeting_objective`,''),IFNULL(`agenda_topic`,''),IFNULL(`agenda_note`,'')) LIKE '%$key%'");
	while($meeting = mysqli_fetch_assoc($meetings)) { ?>
		<a href="../Agenda Meetings/add_<?= strtolower($meeting['type']) ?>.php?agendameetingid=<?= $meeting['agendameetingid'] ?>&from=<?= $from ?>"><div class="dashboard-item"><h3 class="no-margin pad-5"><?= $meeting['type'].' '.$meeting['date_of_meeting'] ?></h3></div></a>
	<?php }
}
if(in_array('Expenses',$tab_config)) {
	$expenses = mysqli_query($dbc, "SELECT * FROM `expense` WHERE `projectid`='$projectid' AND `deleted`=0 AND CONCAT(IFNULL(`category`,''),IFNULL(`title`,''),IFNULL(`description`,'')) LIKE '%$key%'");
	while($expense = mysqli_fetch_assoc($expenses)) { ?>
		<a href="" onclick="overlayIFrameSlider('../Expense/edit_expense.php?expenseid=<?= $expense['expenseid'] ?>', '80%', true, true); return false;"><div class="dashboard-item"><h3 class="no-margin pad-5">Expense: <?= $expense['title'] ?> <?= $expense['ex_date'] ?> $<?= $expense['amount'] ?></h3></div></a>
	<?php }
} ?>
<button class="btn brand-btn pull-right" onclick="$('.search_list').val('').keyup().focus(); return false;">Clear Search</button>
