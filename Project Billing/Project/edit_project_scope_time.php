<?php include_once('../include.php');
error_reporting(0);
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
if(!isset($project)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
}
$value_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype'"))[0]),explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL'"))[0])))); ?>
<?php if($_GET['contactid'] > 0 && !empty($_GET['task'])) {
	$contactid = filter_var($_GET['contactid'],FILTER_SANITIZE_STRING);
	$task = filter_var($_GET['task'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `time_cards` (`projectid`,`business`,`staff`,`date`,`start_time`,`type_of_time`,`timer_start`) VALUES ('$projectid','','$contactid',DATE(NOW()),DATE_FORMAT(NOW(),'%H:%i'),'$task',UNIX_TIMESTAMP())");
	echo "<script> window.location.replace('?edit=$projectid&tab=time_clock'); </script>";
} ?>
<h3>Time Clock <?= $_GET['contact'] > 0 ? get_contact($dbc, $_GET['contact']) : '' ?></h3>
<?php if(empty($_GET['contact'])) {
	$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contacts`.`contactid`, `contacts`.`first_name`, `contacts`.`last_name`, MAX(`time_cards`.`timer_start`) timer FROM `contacts` LEFT JOIN `time_cards` ON `contacts`.`contactid`=`time_cards`.`staff` AND `timer_start` > 0 WHERE `contacts`.`category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `contacts`.`deleted`=0 AND `contacts`.`status` > 0 AND `contacts`.`show_hide_user`='1' GROUP BY `contacts`.`contactid`, `contacts`.`first_name`, `contacts`.`last_name`"));
	foreach($staff_list as $staff) { ?>
		<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12">
			<a href="?edit=<?= $projectid ?>&tab=time_clock&contact=<?= $staff['contactid'] ?>"><?= $staff['first_name'].' '.$staff['last_name'].($staff['timer'] > 0 ? '<br /><small><em>End Timer</em></small>' : '') ?></a>
		</div>
	<?php }
} else {
	$staff = filter_var($_GET['contact'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs`=(UNIX_TIMESTAMP() - `timer_start`)/3600, `timer_start`=0, `end_time`=DATE_FORMAT(NOW(),'%H:%i') WHERE `staff`='$staff' AND `timer_start` > 0");
	$tasks = mysqli_query($dbc, "SELECT 'Regular Hrs.' work_desc,'Regular Time' label UNION SELECT 'Extra Hrs.' work_desc,'Overtime' label UNION SELECT * FROM (SELECT `work_desc`, CONCAT(`category`,': ',`work_desc`) label FROM `staff_rate_table` WHERE `deleted`=0 AND CONCAT(',',`staff_id`,',') LIKE '%,$staff,%' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') ORDER BY `category`,`work_desc`) tasks");
	while($task = mysqli_fetch_assoc($tasks)) { ?>
		<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12">
			<a href="?edit=<?= $projectid ?>&tab=time_clock&contactid=<?= $staff ?>&task=<?= $task['work_desc'] ?>"><?= $task['label'] ?></a>
		</div>
	<?php }
} ?>
<?php include('next_buttons.php'); ?>