<?php
if($staff > 0) {
	$all_contacts = [$staff];
	if($clientid > 0) {
		$all_contacts[] = $clientid;
	}
	foreach ($all_contacts as $staff) {
		if(!$midnight_end) {
			mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs`=GREATEST(IF('$time_interval' > 0,CEILING(((($time - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600) / '$time_interval') * '$time_interval',((($time - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600)),'$time_minimum'), `timer_tracked` = (($time - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `end_time`='$hour', `end_time_from` = 'End Day Tile' WHERE `staff`='$staff'AND `type_of_time` != 'day_tracking' AND `timer_start` > 0");

			//Check Out of Tickets/Work Orders
			$all_attached = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `src_table` = 'Staff_Tasks' AND `item_id` = '".$staff."' AND `arrived` = 1 AND `completed` = 0 AND `deleted` = 0");
			while($attached = mysqli_fetch_array($all_attached)) {
				$hours = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`total_hrs`) FROM `time_cards` WHERE `ticketid`='{$attached['ticketid']}' AND `staff`='{$attached['item_id']}' AND `comment_box` LIKE '% for {$attached['position']}'"))[0];
				mysqli_query($dbc, "UPDATE `ticket_attached` SET `hours_tracked`='$hours' WHERE `id`='".$attached['id']."'");
				mysqli_query($dbc, "UPDATE `ticket_attached` SET `checked_out`='".date('h:i a')."' WHERE `id`='".$attached['id']."'");
				mysqli_query($dbc, "UPDATE `ticket_attached` SET `completed`=1 WHERE `id`='".$attached['id']."'");
			}

			//End Paused Day Tracking
		    $date_of_archival = date('Y-m-d');
			$dbc->query("UPDATE `time_cards` SET `day_tracking_type`='Unresumed Day Tracking', `deleted`=1, `date_of_archival` = '$date_of_archival', `timer_start`=0 WHERE `staff`='$staff' AND `deleted`=0 AND `timer_start` > 0 AND `type_of_time`='day_tracking' AND `day_tracking_type` LIKE 'Work:%'");
		}

		//End Day
		$shifts = checkShiftIntervals($dbc, $staff, $day_of_week, $date);
		$timesheet_track_shifts = get_config($dbc, 'timesheet_track_shifts');
		$tracking_id = $dbc->query("SELECT MAX(`time_cards_id`) `id`, MAX(`timer_start`) `timer` FROM `time_cards` WHERE `timer_start` > 0 AND `type_of_time`='day_tracking' AND `staff`='$staff' $midnight_query")->fetch_assoc();
		$timer_start = date('Y-m-d H:i', $tracking_id['timer']);
		$tracking_id = $tracking_id['id'];
		if(!empty($shifts) && $timesheet_track_shifts == '1' && $tracking_id['id'] > 0) {
			$total = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`total_hrs`) hours, MIN(`timer_start`) timer FROM `time_cards` WHERE CONCAT(`date`,' ',`start_time`) > '$date $hour' AND `staff`='$staff' AND `shift_tracked` = 0 $midnight_query"));
			$total_tracked = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`timer_tracked`) hours, MIN(`timer_start`) timer FROM `time_cards` WHERE CONCAT(`date`,' ',`start_time`) > '$date $hour' AND `staff`='$staff' AND `shift_tracked` = 1"));
			$hours = ($total['hours'] + $total_tracked['hours']) * 1;
			$minimum = ($time_minimum > $hours ? $time_minimum - $hours : 0);
			$seconds = ($time > $total['timer'] + ($hours * 3600) ? $time +($hours * 3600) : $total['timer']);
			mysqli_query($dbc, "UPDATE `time_cards` SET `timer_tracked` = (($time - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `type_of_time`=IF(`day_tracking_type` IS NULL OR `day_tracking_type` = '', 'Regular Hrs.', `day_tracking_type`), `end_time`='$hour', `end_time_from` = 'End Day Tile', `comment_box`=CONCAT(IFNULL(CONCAT(`comment_box`,'&lt;br /&gt;'),''),'$comment') $highlight_query WHERE `timer_start` > 0 AND `type_of_time`='day_tracking' AND `staff`='$staff' $id_query");
		} else if($tracking_id['id'] > 0) {
			$total = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`total_hrs`) hours, MIN(`timer_start`) timer FROM `time_cards` WHERE CONCAT(`date`,' ',`start_time`) > '$timer_start' AND `staff`='$staff' AND `type_of_time` != 'day_tracking' $midnight_query"));
			$seconds = $time - ($total['hours'] * 3600);
			$hours = $total['hours'] * 1;
			$minimum = ($time_minimum > $hours ? $time_minimum - $hours : 0);
			// $seconds = ($time > $total['timer'] + ($hours * 3600) ? $time +($hours * 3600) : $total['timer']);
			mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs`=GREATEST(IF('$time_interval' > 0,CEILING(((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600) / '$time_interval') * '$time_interval',((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600)),'$minimum'), `timer_tracked` = (($time - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `type_of_time`=IF(`day_tracking_type` IS NULL OR `day_tracking_type` = '', 'Regular Hrs.', `day_tracking_type`), `end_time`='$hour', `end_time_from` = 'End Day Tile', `comment_box`=CONCAT(IFNULL(CONCAT(`comment_box`,'&lt;br /&gt;'),''),'$comment') $highlight_query WHERE `timer_start` > 0 AND `type_of_time`='day_tracking' AND `staff`='$staff' $id_query");
		}
	}

	// Go on Break or Resume from Break
	if($_POST['submit'] == 'day_break') {
		$dbc->query("INSERT INTO `time_cards` (`staff`, `date`, `start_time`, `type_of_time`, `timer_start`, `day_tracking_type`) VALUES ('$staff','$date','$hour','day_tracking','$time','Break:$tracking_id')");
	} else if($_POST['submit'] == 'day_resume') {
		$dbc->query("INSERT INTO `time_cards` (`staff`, `date`, `start_time`, `type_of_time`, `timer_start`, `day_tracking_type`) SELECT `staff`, '$date', '$hour', 'day_tracking', '$time', `type_of_time` FROM `time_cards` WHERE CONCAT('Break:',`time_cards_id`) IN (SELECT `day_tracking_type` FROM `time_cards` WHERE `time_cards_id`='$tracking_id')");
		$dbc->query("UPDATE `time_cards` SET `type_of_time`='Break', `comment_box`=CONCAT('Break for ',`total_hrs`,' hours') WHERE `type_of_time` LIKE 'Break%' AND `time_cards_id`='$tracking_id'");
	} else if($_POST['submit'] == 'end_break') {
		$dbc->query("UPDATE `time_cards` SET `type_of_time`='Break' WHERE `type_of_time` LIKE 'Break%' AND `time_cards_id`='$tracking_id'");
	}
}