<?php include_once('../include.php');
checkAuthorised('agenda_meeting');
if(!isset($agendameetingid) && isset($_GET['agendameetingid']) && basename($_SERVER['SCRIPT_FILENAME']) == 'meeting_time_tracking.php') {
	ob_clean();
	$agendameetingid = filter_var($_GET['agendameetingid'],FILTER_SANITIZE_STRING);
}
echo '<h4>Current Time Towards Meeting</h4>';
$query_check_credentials = "SELECT *, CONCAT(IFNULL(`start_time`,''),' - ',IFNULL(`end_time`,''),' (',IFNULL(`timer`,''),')') `time`, TIME_TO_SEC(`timer`) `second_count` FROM `agenda_meeting_timer` WHERE `agendameetingid` = '$agendameetingid' ORDER BY `created_date` DESC, `timerid` DESC";
$result = mysqli_query($dbc, $query_check_credentials);
$num_rows = mysqli_num_rows($result);
if($num_rows > 0) { ?>
	<table class='table table-bordered'>
	<tr class=''>
		<th>Type</th>
		<th>Time</th>
		<th>Date</th>
		<th>Added By</th>
	</tr>
	<?php $add_times = 0;
	while($row = mysqli_fetch_array($result)) {
		echo '<tr data-id="'.$row['timerid'].'">';
		$by = $row['created_by'];
		echo '<td data-title="Type">'.$row['timer_type'].'</td>';
		echo '<td data-title="Time">'.$row['time'].'</td>';
		echo '<td data-title="Date">'.substr($row['created_date'],0,10).'</td>';
		echo '<td data-title="Added By">'.get_staff($dbc, $by).'</td>';
		echo '</tr>';

		if($row['end_time'] != '' && $row['type'] == 'Meeting') {
			if($row['second_count'] > 0) {
				$add_times += $row['second_count'];
			} else {
				$add_times += round(abs(strtotime($row['start_time']) - strtotime($row['end_time'])),2);
			}
		}
	}

	$hours = str_pad(floor($add_times / 3600),2,'0',STR_PAD_LEFT);
	$minutes = str_pad(floor(($add_times % 3600) / 60),2,'0',STR_PAD_LEFT);
	$seconds = str_pad(floor($add_times % 60),2,'0',STR_PAD_LEFT);

	echo '<tr><td><b>Total Time Spent - Timer</b></td><td colspan="3"><b>'.$hours.':'.$minutes.':'.$seconds.'</b></td></tr>';
	echo '</table>';
} else {
	echo "<h5>No Time Tracked</h5>";
}

$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `agenda_meeting_timer` WHERE `agendameetingid` = '$agendameetingid' ORDER BY `timerid` DESC"));
$start_time = $row['start_timer_time'];
if($start_time == '0' || $start_time == '') {
	$time_seconds = 0;
} else {
	$time_seconds = (time()-$start_time);
} ?>

<input type="hidden" id="timer_type" value="<?= $row['timer_type'] ?>">
<input type="hidden" class="start_time" value="<?= $time_seconds ?>">
<input type="hidden" id="login_contactid" value="<?= $_SESSION['contactid'] ?>">