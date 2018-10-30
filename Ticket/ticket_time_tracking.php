<?php include_once('../include.php');
if(!isset($ticketid) && isset($_GET['ticketid']) && basename($_SERVER['SCRIPT_FILENAME']) == 'ticket_time_tracking.php') {
	ob_clean();
	$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
	$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `spent_time`, `max_time`, `max_qa_time`, `created_date` FROM `tickets` WHERE `ticketid`='$ticketid'"));
}
echo '<h4>Current Time Towards '.TICKET_NOUN.'</h4>';
if($get_ticket['max_time'] != '00:00:00' && $get_ticket['max_time'] != '') {
	mysqli_query($dbc, "INSERT INTO `ticket_time_list` (`ticketid`, `time_type`, `time_length`, `created_date`) SELECT '$ticketid', 'Completion Estimate', '{$get_ticket['max_time']}', '{$get_ticket['created_date']}' FROM (SELECT COUNT(*) rows FROM `ticket_time_list` WHERE `time_type`='Completion Estimate' AND `ticketid`='$ticketid') num WHERE num.rows=0");
}
if($get_ticket['max_qa_time'] != '00:00:00' && $get_ticket['max_qa_time'] != '') {
	mysqli_query($dbc, "INSERT INTO `ticket_time_list` (`ticketid`, `time_type`, `time_length`, `created_date`) SELECT '$ticketid', 'QA Estimate', '{$get_ticket['max_qa_time']}', '{$get_ticket['created_date']}' FROM (SELECT COUNT(*) rows FROM `ticket_time_list` WHERE `time_type`='QA Estimate' AND `ticketid`='$ticketid') num WHERE num.rows=0");
}
$query_check_credentials = "SELECT * FROM (SELECT 0 `id`, `tickettimerid`, `timer_type` `type`, `start_time`, `end_time`, CONCAT(IFNULL(`start_time`,''),' - ',IFNULL(`end_time`,''),' (',IFNULL(`timer`,''),')') `time`, `timer`, `created_date`, `created_by`, TIME_TO_SEC(`timer`) `second_count`, `deleted`, `deleted_by`, 'ticket_timer' `ticket_table` FROM ticket_timer WHERE ticketid='$ticketid' AND `ticketid` > 0
	UNION SELECT `id`, 0 `tickettimerid`, `time_type` `type`, '00:00:00' `start_time`, '00:00:00' `end_time`, `time_length` `time`, '00:00:00' `timer`, `created_date`, `created_by`, TIME_TO_SEC(`time_length`) `second_count`, `deleted`, `deleted_by`, 'ticket_time_list' `ticket_table` FROM `ticket_time_list` WHERE `ticketid`='$ticketid' AND `ticketid` > 0) times ORDER BY `created_date` DESC, `tickettimerid` DESC";
$result = mysqli_query($dbc, $query_check_credentials);
$num_rows = mysqli_num_rows($result);
if($generate_pdf) {
	ob_clean();
}
if($num_rows > 0) { ?>
	<div id="no-more-tables">
    <table class='table table-bordered'>
	<tr class='hidden-xs'>
		<th>Tab</th>
		<th>Time</th>
		<th>Date</th>
		<th>Added By</th>
		<th>Function</th>
	</tr>
	<?php $add_times = 0;
	$est_times = 0;
	$manual_time_total = array();
	$manual_second = 0;
	$manual_minute = 0;
	$manual_hour = 0;
	while($row = mysqli_fetch_array($result)) {
		if($row['deleted'] == 1) {
			$deleted_styling = 'style="text-decoration: line-through;"';
		} else {
			$deleted_styling = '';
		}
		echo '<tr data-table="'.$row['ticket_table'].'" data-id="'.($row['id'] > 0 ? $row['id'] : $row['tickettimerid']).'" '.$deleted_styling.'>';
		$by = $row['created_by'];
		echo '<td data-title="Tab">'.$row['type'].'</td>';
		echo '<td data-title="Time">'.$row['time'].'</td>';
		echo '<td data-title="Date">'.substr($row['created_date'],0,10).'</td>';
		echo '<td data-title="Added By">'.get_staff($dbc, $by).'</td>';
		echo '<td data-title="Function">';
		if($row['deleted'] == 1) {
			echo '<i>Deleted by '.get_contact($dbc, $row['deleted_by']).'</i>';
		} else {
			echo '<a href="" onclick="deleteTicketTime(this); return false;">Delete</a>';
		}
		echo '</td>';
		echo '</tr>';
		if($row['end_time'] != '' && $row['type'] == 'Work') {
			if($row['deleted'] == 0) {
				if($row['second_count'] > 0) {
					$add_times += $row['second_count'];
				} else {
					$add_times += round(abs(strtotime($row['start_time']) - strtotime($row['end_time'])),2);
				}
			}
		} else if(strpos($row['type'],'Estimate') !== FALSE) {
			if($row['deleted'] == 0) {
				$est_times += $row['second_count'];
			}
		}

		if($row['type'] == 'Manual Time' && $row['deleted'] != 1) {
			$manual_time_total_array = explode(":", $row['time']);
			$manual_second += $manual_time_total_array[2];
			$manual_minute += $manual_time_total_array[1];
			$manual_hour += $manual_time_total_array[0];
		}
	}

	$hours = str_pad(floor($add_times / 3600),2,'0',STR_PAD_LEFT);
	$minutes = str_pad(floor(($add_times % 3600) / 60),2,'0',STR_PAD_LEFT);
	$seconds = str_pad(floor($add_times % 60),2,'0',STR_PAD_LEFT);
	$est_hours = str_pad(floor($est_times / 3600),2,'0',STR_PAD_LEFT);
	$est_minutes = str_pad(floor(($est_times % 3600) / 60),2,'0',STR_PAD_LEFT);
	$est_seconds = str_pad(floor($est_times % 60),2,'0',STR_PAD_LEFT);
	$manual_hour = str_pad($manual_hour,2,'0',STR_PAD_LEFT);
	$manual_minute = str_pad($manual_minute,2,'0',STR_PAD_LEFT);
	$manual_second = str_pad($manual_second,2,'0',STR_PAD_LEFT);


	echo '<tr><td><b>Total Time Estimated</b></td><td colspan="4"><b>'.$est_hours.':'.$est_minutes.':'.$est_seconds.'</b></td></tr>';
	echo '<tr><td><b>Total Time Spent - Timer</b></td><td colspan="4"><b>'.$hours.':'.$minutes.':'.$seconds.'</b></td></tr>';
	echo '<tr><td><b>Total Time Spent - Manual</b></td><td colspan="4"><b>'.$manual_hour.':'.$manual_minute.':'.$manual_second.'</b></td></tr>';
	echo '</table></div>';
} else {
	echo "<h5>No Time Tracked</h5>";
} ?>
<?php if($generate_pdf) {
	$pdf_contents[] = ['', ob_get_contents()];
} ?>
<script type="text/javascript">
function deleteTicketTime(link) {
	var id = $(link).closest('tr').data('id');
	var table = $(link).closest('tr').data('table');
	$.ajax({
		url: '../Ticket/ticket_ajax_all.php?action=delete_ticket_time',
		method: 'POST',
		data: { id: id, table: table },
		success: function(response) {
			reloadTimes();
		}
	});
}
</script>
