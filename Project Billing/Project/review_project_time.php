<?php $result = mysqli_query($dbc, "SELECT * FROM (SELECT CONCAT('Checklist ',`checklist`.`checklist_name`,' Item #',`checklist_name`.`checklistnameid`) time_type, `checklist_name`.`checklist` time_heading, `checklist_name_time`.`contactid` time_staff, `checklist_name_time`.`timer_date` time_date, '' time_start, '' time_end, `checklist_name_time`.`work_time` time_length FROM `checklist` RIGHT JOIN `checklist_name` ON `checklist`.`checklistid`=`checklist_name`.`checklistid` RIGHT JOIN `checklist_name_time` ON `checklist_name_time`.`checklist_id`=`checklist_name`.`checklistnameid` WHERE `projectid`='$projectid' UNION
	SELECT CONCAT('Ticket #',`tickets`.`ticketid`) time_type, `tickets`.`heading` time_heading, `ticket_timer`.`created_by` time_staff,  `ticket_timer`.`created_date` time_date, `ticket_timer`.`start_time` time_start, `ticket_timer`.`end_time` time_end, TIMEDIFF(`ticket_timer`.`end_time`,`ticket_timer`.`start_time`) time_length FROM `tickets` RIGHT JOIN `ticket_timer` ON `tickets`.`ticketid`=`ticket_timer`.`ticketid` WHERE `projectid`='$projectid' AND `ticket_timer`.`deleted` = 0 UNION
	SELECT CONCAT('Task #',`tasklist`.`tasklistid`) time_type, `tasklist`.`heading` time_heading, `tasklist`.`contactid` time_staff, `tasklist`.`task_tododate` time_date, '' time_start, '' time_end, `tasklist`.`work_time` time_length FROM `tasklist` WHERE `projectid`='$projectid' UNION
	SELECT CONCAT(`tasklist`.`project_milestone`,' Milestone #',`tasklist`.`tasklistid`) time_type, `tasklist`.`heading` time_heading, `tasklist_time`.`contactid` time_staff, `tasklist_time`.`timer_date` time_date, '' time_start, '' time_end, `tasklist_time`.`work_time` time_length FROM `tasklist` RIGHT JOIN `tasklist_time` ON `tasklist`.`tasklistid`=`tasklist_time`.`tasklistid` WHERE `tasklist`.`projectid`='$projectid') timers");

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

		echo '<td data-title="Type">' . $row['time_type'] . '</td>';
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