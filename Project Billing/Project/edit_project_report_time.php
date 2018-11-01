<?php include_once('../include.php');
error_reporting(0);
if(!isset($project)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
}
$value_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype'"))[0]),explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL'"))[0]))));
$result = mysqli_query($dbc, "SELECT * FROM (SELECT CONCAT('Timesheet for ',`date`) time_type, 'timesheet' `src`, 0 `srcid`, '' time_heading, `staff` time_staff, `date` time_date, `start_time` time_start, `end_time` time_end, SEC_TO_TIME(`total_hrs` * 3600) time_length FROM `time_cards` LEFT JOIN `ticket_time_list` ON `ticket_time_list`.`created_date` LIKE CONCAT(`time_cards`.`date`,'%') AND `time_cards`.`ticketid`=`ticket_time_list`.`ticketid` AND `time_cards`.`staff`=`ticket_time_list`.`created_by` AND `time_cards`.`total_hrs` * 3600 = TIME_TO_SEC(`ticket_time_list`.`time_length`) AND `ticket_time_list`.`deleted`=0 WHERE `time_cards`.`deleted`=0 AND `projectid`='$projectid' AND `ticket_time_list`.`id` IS NULL UNION
	SELECT CONCAT('".TICKET_NOUN." #',`tickets`.`ticketid`) time_type, 'ticket' `src`, `tickets`.`ticketid` `srcid`, `tickets`.`heading` time_heading, `ticket_timer`.`created_by` time_staff,  `ticket_timer`.`created_date` time_date, `ticket_timer`.`start_time` time_start, `ticket_timer`.`end_time` time_end, TIMEDIFF(`ticket_timer`.`end_time`,`ticket_timer`.`start_time`) time_length FROM `tickets` RIGHT JOIN `ticket_timer` ON `tickets`.`ticketid`=`ticket_timer`.`ticketid` WHERE `projectid`='$projectid' AND `ticket_timer`.`deleted` = 0 UNION
	SELECT CONCAT('".TICKET_NOUN." #',`ticket_attached`.`ticketid`) time_type, 'ticket' `src`, `ticket_attached`.`ticketid` `srcid`, `ticket_attached`.`position` time_heading, `ticket_attached`.`hours_tracked` time_staff,  `ticket_attached`.`date_stamp` time_date, '' time_start, '' time_end, `ticket_attached`.`hours_tracked` time_length FROM `ticket_attached` WHERE `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `projectid`='$projectid' AND `deleted`=0) AND `deleted`=0 UNION
	SELECT CONCAT('".TICKET_NOUN." #',`ticket_time_list`.`ticketid`) time_type, 'ticket' `src`, `ticket_time_list`.`ticketid` `srcid`, `ticket_time_list`.`time_type` time_heading, `ticket_time_list`.`created_by` time_staff,  LEFT(`ticket_time_list`.`created_date`,10) time_date, MID(`ticket_time_list`.`created_date`,11) time_start, '' time_end, `ticket_time_list`.`time_length` time_length FROM `ticket_time_list` WHERE `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `projectid`='$projectid' AND `time_type`='Manual Time' AND `deleted`=0) AND `deleted`=0 UNION
	SELECT CONCAT(`tasklist`.`project_milestone`,' Task #',`tasklist`.`tasklistid`) time_type, 'task' `src`, `tasklist`.`tasklistid` `srcid`, `tasklist`.`heading` time_heading, `tasklist_time`.`contactid` time_staff, `tasklist_time`.`timer_date` time_date, '' time_start, '' time_end, `tasklist_time`.`work_time` time_length FROM `tasklist` RIGHT JOIN `tasklist_time` ON `tasklist`.`tasklistid`=`tasklist_time`.`tasklistid` WHERE `tasklist`.`projectid`='$projectid') timers ORDER BY `time_date`, `time_start`");
// echo '<h3>Total Time Tracked</h3>';
if(mysqli_num_rows($result) > 0) {
    echo '<div id="no-more-tables"><table class="table table-bordered">';
    echo '<tr class="hidden-xs hidden-sm">
        <th>Type</th>
        <th>Heading</th>
        <th>Staff</th>
        <th>Date</th>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Duration</th>
        </tr>';
	$total_time = 0;
	while($row = mysqli_fetch_array( $result )) {
		echo '<tr>';

		$time_length = date('G:i',strtotime(date('Y-m-d ').$row['time_length']));
		$minutes = explode(':',$time_length);
		$total_time += ($minutes[0] * 60) + $minutes[1];

		echo '<td data-title="Type">' .($row['src'] == 'ticket' ? '<a href="" onclick="overlayIFrameSlider(\'../Ticket/index.php?edit='.$row['srcid'].'\'); return false;">'.get_ticket_label($dbc, $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='".$row['srcid']."'")->fetch_assoc().'</a>') : ($row['src'] == 'task' ? '<a href="" onclick="overlayIFrameSlider(\'../Tasks_Updated/add_task.php?tasklistid='.$row['srcid'].'\'); return false;">'.$row['time_type'].'</a>' : $row['time_type'])) . '</td>';
		echo '<td data-title="Heading">' . html_entity_decode($row['time_heading']) . '</td>';
		echo '<td data-title="Staff">' . get_contact($dbc, $row['time_staff']) . '</td>';
		echo '<td data-title="Date">' . $row['time_date'] . '</td>';
		echo '<td data-title="Start Time">' . $row['time_start'] . '</td>';
		echo '<td data-title="End Time">' . $row['time_end'] . '</td>';
		echo '<td data-title="Duration">' . $time_length . '</td>';

		echo "</tr>";
	}
    echo '<tr>
        <td colspan="6">Total Time Tracked</td>
        <td data-title="Total Time Tracked">'.floor($total_time/60).':'.sprintf("%02d", $total_time%60).'</td>
        </tr>';

	echo '</table></div>';
} else {
    echo "<h2>No Time Found.</h2>";
}
include('next_buttons.php'); ?>