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
		url: 'projects_ajax.php?action=approve_time',
		method: 'POST',
		data: {
			id: id
		}
	});
}
</script>
<h3>Time Sheets</h3>
<?php $timesheet = mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `projectid`='$projectid'");
if(mysqli_num_rows($timesheet) > 0) { ?>
	<table class="table table-bordered">
		<tr class="hidden-sm hidden-xs">
			<th>Staff</th>
			<th>Date</th>
			<th>Hours</th>
			<th>Type of Hours</th>
			<th>Comment</th>
			<?php if(vuaed_visible_function($dbc, 'timesheet')) { ?>
				<th></th>
			<?php } ?>
		</tr>
		<?php while($card = mysqli_fetch_array($timesheet)) { ?>
			<tr>
				<td data-title="Staff"><?= get_contact($dbc, $card['staff']) ?></td>
				<td data-title="Date"><?= $card['date'] ?></td>
				<td data-title="Hours"><?= number_format($card['total_hrs'],2) ?></td>
				<td data-title="Type of Hours"><?= $card['type_of_time'] ?></td>
				<td data-title="Comment"><?= $card['comment'] ?></td>
				<?php if(vuaed_visible_function($dbc, 'timesheet')) { ?>
					<td data-title="Function"><a href="../Timesheet/add_time_cards.php?time_cards_id=<?= $card['time_cards_id'] ?>">Edit</a> | 
						<?= $card['approv'] == 'N' ? '<a href="" onclick="approve_sheet('.$card['time_cards_id'].');">Approve</a>' : $card['approv'] == 'P' ? 'Paid' : 'Approved' ?></td>
				<?php } ?>
			</tr>
		<?php } ?>
	</table>
<?php } else {
	echo "<h2>No time has been tracked for this project.</h2>";
} ?>
<?php include('next_buttons.php'); ?>