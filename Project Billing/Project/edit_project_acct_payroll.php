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
<script>
function approve_sheet(id) {
	$.ajax({
		url: 'projects_ajax.php?action=pay_time',
		method: 'POST',
		data: {
			id: id
		}
	});
}
</script>
<h3>Payroll</h3>
<?php $staff = sort_contacts_query(mysqli_query($dbc, "SELECT `contacts`.`contactid`, `contacts`.`first_name`, `contacts`.`last_name` FROM `time_cards` LEFT JOIN `contacts` ON `time_cards`.`staff`=`contacts`.`contactid` WHERE `time_cards`.`approv`='N' AND `time_cards`.`manager_name` != '' AND `time_cards`.`projectid`='$projectid' GROUP BY `contacts`.`contactid`, `contacts`.`first_name`, `contacts`.`last_name`"));
if(count($staff) > 0) {
	$dates = mysqli_query($dbc, "SELECT `date` FROM `time_cards` WHERE `time_cards`.`approv`='N' AND `time_cards`.`manager_name` != '' AND `time_cards`.`projectid`='$projectid' GROUP BY `date`"); ?>
	<table class="table table-bordered">
		<tr class="hidden-sm hidden-xs">
			<th>Staff</th>
			<?php while($date = mysqli_fetch_array($dates)['date']) { ?>
				<th>Hours: <?= $date ?></th>
			<?php }
			mysqli_data_seek($dates,0); ?>
			<th>Total</th>
		</tr>
		<?php foreach($staff as $row) { ?>
			<tr>
				<td data-title="Staff"><?= $row['first_name'].' '.$row['last_name'] ?></td>
				<?php while($date = mysqli_fetch_array($dates)['date']) {
					$hours = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(CONCAT(`type_of_time`,': ',`total_hrs`) SEPARATOR '<br />') FROM (SELECT `type_of_time`, SUM(`total_hrs`) total_hrs FROM `time_cards` WHERE `staff`='{$row['contactid']}' AND `date`='$date' AND `approv`='N' AND `time_cards`.`manager_name` != '' AND `time_cards`.`projectid`='$projectid' GROUP BY `type_of_time`) time")); ?>
					<td data-title="Hours: <?= $date ?>"><?= $hours[0] ?></td>
				<?php }
				mysqli_data_seek($dates,0); ?>
				<td data-title="Total"><?php echo mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(CONCAT(`type_of_time`,': ',`total_hrs`) SEPARATOR '<br />') FROM (SELECT `type_of_time`, SUM(`total_hrs`) total_hrs FROM `time_cards` WHERE `staff`='{$row['contactid']}' AND `approv`='N' AND `time_cards`.`manager_name` != '' AND `time_cards`.`projectid`='$projectid' GROUP BY `type_of_time`) time"))[0]; ?></td>
			</tr>
		<?php } ?>
	</table>
<?php } else {
	echo "<h2>No time found for Payroll for this project.</h2>";
} ?>
<?php include('next_buttons.php'); ?>