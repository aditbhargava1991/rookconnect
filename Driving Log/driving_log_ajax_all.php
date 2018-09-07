<?php
include('../include.php');
ob_clean();
if(isset($_GET['fill'])) {
if($_GET['fill'] == 'startdl') {
    $driverid = $_GET['driverid'];

    $result = mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND contactid NOT IN($driverid)");

    echo "<option value=''></option>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<option value = '".$row['contactid']."'>".decryptIt($row['first_name']).' ' .decryptIt($row['last_name'])."</option>";
    }
}

if($_GET['fill'] == 'drivinglog') {
    date_default_timezone_set('America/Denver');
	$timer_name = $_GET['timer_name'];
	$time = $_GET['time'];
    $drivinglogid = $_GET['drivinglogid'];
    $dl_comment = filter_var($_GET['dl_comment'],FILTER_SANITIZE_STRING);
    //$dl_comment = $_GET['dl_comment'];

	$query_insert_graph = 'DELETE FROM driving_log_timer WHERE inspection_mode=1 AND drivinglogid = "'.$drivinglogid.'"';
	$result_insert_graph = mysqli_query($dbc, $query_insert_graph);

    $end_date = '';
    $amendments = '';
    $current_time = date('h:i A');

		$reverse_explode = array_reverse(explode(':',$time));

			$i = 0;
			$len = count($reverse_explode);

			foreach( $reverse_explode as $time ) {


				if ($i == 0) {
					$seconds = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				} else {
					$minutes = $time;
				}
				// …
				$i++;

			}
			$sec_from_min = $minutes * 60;
			$sec_from_hours = $hours*60*60;
			$total_secs = $sec_from_min+$sec_from_hours+$seconds;
			$start_time = date('h:i A', strtotime(date('h:i:s A'))-$total_secs);


    if(!empty($_GET['current_timer'])) {
        $current_timer = $_GET['current_timer'];
    }
    $last_timer_value = $current_timer.'*#*'.time();

    $time_name = '';
    $final_timer = '';
	$resetter = NULL;
    if($timer_name == 'off_duty_timer') {
        $time_name = 'off_duty_time';
        $final_timer = 'final_off_duty_timer';

		// GET TOTAL OFF-DUTY TIME

		$col = "SELECT `reset_cycle` FROM driving_log_timer";
		$result = mysqli_query($dbc, $col);
		if (!$result){
			$colcreate = "ALTER TABLE `driving_log_timer` ADD COLUMN `reset_cycle` VARCHAR(555) NULL";
			$result = mysqli_query($dbc, $colcreate);
		}
		$get_driver = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM driving_log WHERE drivinglogid='$drivinglogid'"));
		$driverid = $get_driver['cycle'];
		$cycler = $get_driver['cycle'];

		$get_time_left = "SELECT * FROM `driving_log` WHERE `driverid` = '$driverid' AND `cycle` = '$cycler' ORDER BY `drivinglogid` DESC";

		$result1 = mysqli_query($dbc, $get_time_left);
		$num_rows1 = mysqli_num_rows($result1);

		if($num_rows1 > 0) {

			$on_duty_time = '';
			$seconds = 0;
			$minutes = 0;
			$hours = 0;

							$reverse_explode = array_reverse(explode(':',$time));

							$i = 0;
							$len = count($reverse_explode);

							foreach( $reverse_explode as $time ) {

								if ($i == 0) {
									$seconds += $time;

								} else if ($i == $len - 1) {
									$hours += $time;
								} else {
									$minutes += $time;
								}
								// …
								$i++;

							}

			while($row1 = mysqli_fetch_array($result1)) {

				$drivinglogid = $row1['drivinglogid'];

				$select_timers = "SELECT * FROM driving_log_timer WHERE drivinglogid = '$drivinglogid' ORDER BY timerid DESC";

				$result2 = mysqli_query($dbc, $select_timers);
				$num_rows2 = mysqli_num_rows($result2);
				$is_reset = '';
				if($num_rows2 > 0) {
					while($row2 = mysqli_fetch_array($result2)) {

						if($row2['reset_cycle'] == 1) {
							$is_reset .='1';
							break;
						}

						if($row2['off_duty_timer'] !== '' && $row2['off_duty_timer'] !== NULL) {

							$reverse_explode = array_reverse(explode(':',$row2['off_duty_timer']));

							$i = 0;
							$len = count($reverse_explode);

							foreach( $reverse_explode as $time ) {

								if ($i == 0) {
									$seconds += $time;
								} else if ($i == $len - 1) {
									$hours += $time;
								} else {
									$minutes += $time;
								}
								// …
								$i++;

							}
						}
					}
				}
			}
		}

		// SUM UP OFF DUTY TIME

				$minute_from_seconds = $seconds/60;
				$minute_add = floor($minute_from_seconds);
				$seconds_left = $minute_from_seconds - $minute_add;
				$seconds = $seconds_left*60;

				$minutes = $minutes + $minute_add;

				$hours_from_minutes = $minutes/60;
				$hour_add = floor($hours_from_minutes);
				$minutes_left = $hours_from_minutes - $hour_add;
				$minutes = $minutes_left*60;

				$hours = $hours+$hour_add;

				$hours_left = sprintf("%02d", $hours);
				$minutes_left = sprintf("%02d", $minutes);
				$seconds_left = sprintf("%02d", $seconds);
				//if statement

				if($cycler == 'Cycle 1(7 days)') {
					if($hours_left >= 36) {
						$resetter = 1;
					} else {
						$resetter = NULL;
					}
				} else {
					if($hours_left >= 72) {
						$resetter = 1;
					} else {
						$resetter = NULL;
					}
				}


		// END COUNT OF OFF DUTY TIME

    }
	$time = $_GET['time'];
	$drivinglogid = $_GET['drivinglogid'];
    if($timer_name == 'sleeper_berth_timer') {
        $time_name = 'sleeper_berth_time';
        $final_timer = 'final_sleeper_berth_timer';
    }
    if($timer_name == 'driving_timer') {
        $time_name = 'driving_time';
        $final_timer = 'final_driving_timer';
    }
    if($timer_name == 'on_duty_timer') {
        $time_name = 'on_duty_time';
        $final_timer = 'final_on_duty_timer';
    }

	$result_timer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(timerid) AS total_timer FROM driving_log_timer WHERE drivinglogid='$drivinglogid'"));
    $old_level = $result_timer['total_timer'];
    $level = $result_timer['total_timer']+1;

    if(!empty($_GET['ame'])) {
        $amendments = $_GET['amendments'];
        $amendments_comment = $_GET['amendments_comment'];
    }
    if($time != '00:00:00' && $time !== '') {

			$query_insert_report = "INSERT INTO `driving_log_timer` (`drivinglogid`, `level`, $timer_name, $time_name, `end_$time_name`, $final_timer, `dl_comment`, `amendments`, `amendments_comment`, `reset_cycle`) VALUES ('$drivinglogid', '$level', '$time', '$start_time', '$current_time', '$time', '$dl_comment', '$amendments', '$amendments_comment' , '$resetter')";
			$result_insert_report = mysqli_query($dbc, $query_insert_report);
			$timerid = mysqli_insert_id($dbc);
			$check_24_time = $time;
			include ('get_total_timer_times.php');
			if(isset($under_24hour_time)) {
				 $result_update_t = mysqli_query($dbc, "UPDATE `driving_log_timer` SET end_$time_name='11:59 PM', $timer_name = '$under_24hour_time' WHERE drivinglogid='$drivinglogid' AND timerid='$timerid'");
			}
			$query_update_t = "UPDATE `driving_log` SET last_timer_value='$last_timer_value' WHERE drivinglogid='$drivinglogid'";
			$result_update_t = mysqli_query($dbc, $query_update_t);
			$ame_time = $start_time;
			// UPDATE TIMER TIME AND END TIME OF PREVIOUS TIMER (In case of amendments)
			$result_level = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT off_duty_time, sleeper_berth_time, driving_time, on_duty_time, end_off_duty_time, end_sleeper_berth_time, end_driving_time, end_on_duty_time FROM driving_log_timer WHERE drivinglogid='$drivinglogid' AND level='$old_level'"));

            if($result_level['off_duty_time'] != '' && $result_level['off_duty_time'] != NULL) {
				$end_time_sub = $result_level['end_off_duty_time'];
				$start_time_sub = $result_level['off_duty_time'];
				$field_end = 'end_off_duty_time';
				$timerfield = 'off_duty_timer';
            }
            if($result_level['sleeper_berth_time'] !== '' && $result_level['sleeper_berth_time'] != NULL) {
				$end_time_sub = $result_level['end_sleeper_berth_time'];
				$start_time_sub = $result_level['sleeper_berth_time'];
				$field_end = 'end_sleeper_berth_time';
				$timerfield = 'sleeper_berth_timer';
            }
            if($result_level['driving_time'] !== '' && $result_level['driving_time'] != NULL) {
				$end_time_sub = $result_level['end_driving_time'];
				$start_time_sub = $result_level['driving_time'];
				$field_end = 'end_driving_time';
				$timerfield = 'driving_timer';
            }
            if($result_level['on_duty_time'] !== '' && $result_level['on_duty_time'] != NULL) {
				$end_time_sub = $result_level['end_on_duty_time'];
				$start_time_sub = $result_level['on_duty_time'];
				$field_end = 'end_on_duty_time';
				$timerfield = 'on_duty_timer';
            }

			// GET THE AMENDED TIME AND ORIGINAL START TIME DIFFERENCE FOR PREVIOUS TIMER

		/* Beginning time of the new amendment >> */ $end_time_sub  = date("H:i", strtotime($ame_time));
		/* Beginning time of the previous timer >> */ $start_time_sub  = date("H:i", strtotime($start_time_sub));
		$value = date("g:i a", strtotime($ame_time));
		// Get start time and end time difference.
		$reverse_explode = array_reverse(explode(':',$start_time_sub));
			$i = 0;
			$len = count($reverse_explode);
			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				$i++;
			}
			$sec_from_min = $minutes * 60;
			$sec_from_hours = $hours*60*60;
			$total_secs_start = $sec_from_min+$sec_from_hours;
			$reverse_explode = array_reverse(explode(':',$end_time_sub));
			$i = 0;
			$len = count($reverse_explode);
			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				$i++;
			}
			$sec_from_min = $minutes * 60;
			$sec_from_hours = $hours*60*60;
			$total_secs_end = $sec_from_min+$sec_from_hours;
			$timer_time = $total_secs_end - $total_secs_start;
			$timer_time = $timer_time/(60*60);
			$timer_time_hours = floor($timer_time);
			$time_mins = ($timer_time - $timer_time_hours)*60;
			$timer_time_hours = sprintf("%02d", $timer_time_hours);
			$time_mins = sprintf("%02d", $time_mins);
			$timer_time = $timer_time_hours.':'.$time_mins.':00';
		//	 $result_update_t = mysqli_query($dbc, "UPDATE `driving_log_timer` SET end_off_duty_time='$value' WHERE drivinglogid='$drivinglogid' AND level='$old_level'");
			$query_update = "UPDATE `driving_log_timer` SET $field_end='$value', $timerfield = '$timer_time'  WHERE drivinglogid='$drivinglogid'  AND level='$old_level'";
			$result_update = mysqli_query($dbc, $query_update);
    }

    if(!empty($_GET['end'])) {
        $end_date = date('Y-m-d');
        $query_update = "UPDATE `driving_log` SET end_date='$end_date' WHERE drivinglogid='$drivinglogid'";
        $result_update = mysqli_query($dbc, $query_update);
    }

}

if($_GET['fill'] == 'amendments') {
    date_default_timezone_set('America/Denver');
	$id = $_GET['id'];
	$value = $_GET['value'];
    $action = $_GET['action'];

    if($action == 'sign') {
        $query_update = "UPDATE `driving_log` SET sign=1 WHERE drivinglogid='$id'";
        $result_update = mysqli_query($dbc, $query_update);
    }
    if($action == 'update') {
        $column = $_GET['column'];
        if($column == 'off') {
            $field = 'off_duty_time';
			$field_end = 'end_off_duty_time';
			$timerfield = 'off_duty_timer';
        }
        if($column == 'sleeper') {
            $field = 'sleeper_berth_time';
			$field_end = 'end_sleeper_berth_time';
			$timerfield = 'sleeper_berth_timer';
        }
        if($column == 'driving') {
            $field = 'driving_time';
			$field_end = 'end_driving_time';
			$timerfield = 'driving_timer';
        }
        if($column == 'on') {
            $field = 'on_duty_time';
			$field_end = 'end_on_duty_time';
			$timerfield = 'on_duty_timer';
        }
		// GET THE AMENDED TIME AND ORIGINAL START TIME DIFFERENCE
		$get_the_times = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM driving_log_timer WHERE timerid='$id'"));
		$drivinglogid = $get_the_times['drivinglogid'];
		include ('fix_negative_bug.php');
		$end_time_sub = $get_the_times[$field_end];
		$get_the_timer = $get_the_times[$timerfield];
		$end_time_sub  = date("H:i", strtotime($end_time_sub));

		// Get start time and end time difference.
		$reverse_explode = array_reverse(explode(':',$value));
			$i = 0;
			$len = count($reverse_explode);
			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				$i++;
			}
			$sec_from_min = $minutes * 60;
			$sec_from_hours = $hours*60*60;
			$total_secs_start = $sec_from_min+$sec_from_hours;
		$reverse_explode = array_reverse(explode(':',$end_time_sub));
			$i = 0;
			$len = count($reverse_explode);
			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				$i++;
			}
			$sec_from_min = $minutes * 60;
			$sec_from_hours = $hours*60*60;
			$total_secs_end = $sec_from_min+$sec_from_hours;
			$timer_time = $total_secs_end - $total_secs_start;
			$timer_time = $timer_time/(60*60);
			$timer_time_hours = floor($timer_time);
			$time_mins = ($timer_time - $timer_time_hours)*60;
			$timer_time_hours = sprintf("%02d", $timer_time_hours);
			$time_mins = sprintf("%02d", $time_mins);
			$timer_time = $timer_time_hours.':'.$time_mins.':00';

			// DONE GETTING DIFFERENCE...

		$value = date("g:i a", strtotime($value));
        $query_update = "UPDATE `driving_log_timer` SET $field='$value', $timerfield = '$timer_time', amendments_status = 'Pending'  WHERE timerid='$id'";
        $result_update = mysqli_query($dbc, $query_update);

        $level = get_dltimer($dbc, $id, 'level');
        $old_level = ($level-1);
        $drivinglogid = get_dltimer($dbc, $id, 'drivinglogid');

        if($level != 1) {
            $result_level = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT off_duty_time, sleeper_berth_time, driving_time, on_duty_time, end_off_duty_time, end_sleeper_berth_time, end_driving_time, end_on_duty_time FROM driving_log_timer WHERE drivinglogid='$drivinglogid' AND level='$old_level'"));

			// DONE GETTING DIFFERENCE...
            if($result_level['off_duty_time'] !== '' && $result_level['off_duty_time'] !== NULL) {

				$end_time_sub = $result_level['end_off_duty_time'];
				$start_time_sub = $result_level['off_duty_time'];
				$field_end = 'end_off_duty_time';
				$timerfield = 'off_duty_timer';
            }
            if($result_level['sleeper_berth_time'] !== '' && $result_level['sleeper_berth_time'] !== NULL) {
				$end_time_sub = $result_level['end_sleeper_berth_time'];
				$start_time_sub = $result_level['sleeper_berth_time'];
				$field_end = 'end_sleeper_berth_time';
				$timerfield = 'sleeper_berth_timer';
            }
            if($result_level['driving_time'] !== '' && $result_level['driving_time'] !== NULL) {
				$end_time_sub = $result_level['end_driving_time'];
				$start_time_sub = $result_level['driving_time'];
				$field_end = 'end_driving_time';
				$timerfield = 'driving_timer';
            }
            if($result_level['on_duty_time'] !== '' && $result_level['on_duty_time'] !== NULL) {
				$end_time_sub = $result_level['end_on_duty_time'];
				$start_time_sub = $result_level['on_duty_time'];
				$field_end = 'end_on_duty_time';
				$timerfield = 'on_duty_timer';
            }

				// GET THE AMENDED TIME AND ORIGINAL START TIME DIFFERENCE FOR PREVIOUS TIMER

		$end_time_sub  = date("H:i", strtotime($value));
		$start_time_sub  = date("H:i", strtotime($start_time_sub));
		echo $start_time_sub;
		// Get start time and end time difference.
		$reverse_explode = array_reverse(explode(':',$start_time_sub));
			$i = 0;
			$len = count($reverse_explode);
			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				$i++;
			}
			$sec_from_min = $minutes * 60;
			$sec_from_hours = $hours*60*60;
			$total_secs_start = $sec_from_min+$sec_from_hours;
			$reverse_explode = array_reverse(explode(':',$end_time_sub));
			$i = 0;
			$len = count($reverse_explode);
			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				$i++;
			}
			$sec_from_min = $minutes * 60;
			$sec_from_hours = $hours*60*60;
			$total_secs_end = $sec_from_min+$sec_from_hours;
			$timer_time = $total_secs_end - $total_secs_start;
			$timer_time = $timer_time/(60*60);
			$timer_time_hours = floor($timer_time);
			$time_mins = ($timer_time - $timer_time_hours)*60;
			$timer_time_hours = sprintf("%02d", $timer_time_hours);
			$time_mins = sprintf("%02d", $time_mins);
			$timer_time = $timer_time_hours.':'.$time_mins.':00';
		//	 $result_update_t = mysqli_query($dbc, "UPDATE `driving_log_timer` SET end_off_duty_time='$value' WHERE drivinglogid='$drivinglogid' AND level='$old_level'");
			$query_update = "UPDATE `driving_log_timer` SET $field_end='$value', $timerfield = '$timer_time', amendments_status = 'Pending'  WHERE drivinglogid='$drivinglogid'  AND level='$old_level'";
			$result_update = mysqli_query($dbc, $query_update);

        }
		include ('fix_negative_bug.php');
    }
	if($action == 'update_end_time') {
        $column = $_GET['column'];
        if($column == 'off') {
            $field = 'off_duty_time';
			$field_end = 'end_off_duty_time';
			$timerfield = 'off_duty_timer';
        }
        if($column == 'sleeper') {
            $field = 'sleeper_berth_time';
			$field_end = 'end_sleeper_berth_time';
			$timerfield = 'sleeper_berth_timer';
        }
        if($column == 'driving') {
            $field = 'driving_time';
			$field_end = 'end_driving_time';
			$timerfield = 'driving_timer';
        }
        if($column == 'on') {
            $field = 'on_duty_time';
			$field_end = 'end_on_duty_time';
			$timerfield = 'on_duty_timer';
        }
		// Get The Difference between the New End TIME AND START TIME
		$get_the_times = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM driving_log_timer WHERE timerid='$id'"));
		$drivinglogid = $get_the_times['drivinglogid'];
		include ('fix_negative_bug.php');
		$start_time_sub = $get_the_times[$field];
		$get_the_timer = $get_the_times[$timerfield];
		$start_time_sub  = date("H:i", strtotime($start_time_sub));

		// Get start time and end time difference.
		$reverse_explode = array_reverse(explode(':',$value));
			$i = 0;
			$len = count($reverse_explode);
			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				$i++;
			}
			$sec_from_min = $minutes * 60;
			$sec_from_hours = $hours*60*60;
			$total_secs_end = $sec_from_min+$sec_from_hours;
		$reverse_explode = array_reverse(explode(':',$start_time_sub));
			$i = 0;
			$len = count($reverse_explode);
			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				$i++;
			}
			$sec_from_min = $minutes * 60;
			$sec_from_hours = $hours*60*60;
			$total_secs_start = $sec_from_min+$sec_from_hours;
			$timer_time = $total_secs_end - $total_secs_start;
			$timer_time = $timer_time/(60*60);
			$timer_time_hours = floor($timer_time);
			$time_mins = ($timer_time - $timer_time_hours)*60;
			$timer_time_hours = sprintf("%02d", $timer_time_hours);
			$time_mins = sprintf("%02d", $time_mins);
			$timer_time = $timer_time_hours.':'.$time_mins.':00';

			// DONE GETTING DIFFERENCE...

		$value = date("g:i a", strtotime($value));
        $query_update = "UPDATE `driving_log_timer` SET $field_end='$value', $timerfield = '$timer_time', amendments_status = 'Pending'  WHERE timerid='$id'";
        $result_update = mysqli_query($dbc, $query_update);

        $level = get_dltimer($dbc, $id, 'level');
        $next_level = ($level+1);
        $drivinglogid = get_dltimer($dbc, $id, 'drivinglogid');

			$check_if_there_is_a_next_level = mysqli_query($dbc, "SELECT off_duty_time, sleeper_berth_time, driving_time, on_duty_time, end_off_duty_time, end_sleeper_berth_time, end_driving_time, end_on_duty_time FROM driving_log_timer WHERE drivinglogid='$drivinglogid' AND level='$next_level'");
			$check_if_there_is_a_next_level = mysqli_num_rows($check_if_there_is_a_next_level);

            if($check_if_there_is_a_next_level > 0) {
				echo '12345';
				$result_level = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT off_duty_time, sleeper_berth_time, driving_time, on_duty_time, end_off_duty_time, end_sleeper_berth_time, end_driving_time, end_on_duty_time FROM driving_log_timer WHERE drivinglogid='$drivinglogid' AND level='$next_level'"));

				if($result_level['off_duty_time'] !== '' && $result_level['off_duty_time'] !== NULL) {

					$end_time_sub = $result_level['end_off_duty_time'];
					$start_time_sub = $result_level['off_duty_time'];
					$field = 'off_duty_time';
					$timerfield = 'off_duty_timer';
				}
				if($result_level['sleeper_berth_time'] !== '' && $result_level['sleeper_berth_time'] !== NULL) {
					$end_time_sub = $result_level['end_sleeper_berth_time'];
					$start_time_sub = $result_level['sleeper_berth_time'];
					$field = 'sleeper_berth_time';
					$timerfield = 'sleeper_berth_timer';
				}
				if($result_level['driving_time'] !== '' && $result_level['driving_time'] !== NULL) {
					$end_time_sub = $result_level['end_driving_time'];
					$start_time_sub = $result_level['driving_time'];
					$field = 'driving_time';
					$timerfield = 'driving_timer';
				}
				if($result_level['on_duty_time'] !== '' && $result_level['on_duty_time'] !== NULL) {
					$end_time_sub = $result_level['end_on_duty_time'];
					$start_time_sub = $result_level['on_duty_time'];
					$field = 'on_duty_time';
					$timerfield = 'on_duty_timer';
				}
					// GET THE AMENDED TIME AND ORIGINAL START TIME DIFFERENCE FOR NEXT TIMER
			$end_time_sub  = date("H:i", strtotime($end_time_sub));
			$start_time_sub  = date("H:i", strtotime($value));
			// Get start time and end time difference.
			$reverse_explode = array_reverse(explode(':',$start_time_sub));
				$i = 0;
				$len = count($reverse_explode);
				foreach( $reverse_explode as $time ) {
					if ($i == 0) {
						$minutes = $time;
					} else if ($i == $len - 1) {
						$hours = $time;
					}
					$i++;
				}
				$sec_from_min = $minutes * 60;
				$sec_from_hours = $hours*60*60;
				$total_secs_start = $sec_from_min+$sec_from_hours;
				$reverse_explode = array_reverse(explode(':',$end_time_sub));
				$i = 0;
				$len = count($reverse_explode);
				foreach( $reverse_explode as $time ) {
					if ($i == 0) {
						$minutes = $time;
					} else if ($i == $len - 1) {
						$hours = $time;
					}
					$i++;
				}
				$sec_from_min = $minutes * 60;
				$sec_from_hours = $hours*60*60;
				$total_secs_end = $sec_from_min+$sec_from_hours;
				$timer_time = $total_secs_end - $total_secs_start;
				$timer_time = $timer_time/(60*60);
				$timer_time_hours = floor($timer_time);
				$time_mins = ($timer_time - $timer_time_hours)*60;
				$timer_time_hours = sprintf("%02d", $timer_time_hours);
				$time_mins = sprintf("%02d", $time_mins);
				$timer_time = $timer_time_hours.':'.$time_mins.':00';
				$query_update = "UPDATE `driving_log_timer` SET $field='$value', $timerfield = '$timer_time', amendments_status = 'Pending'  WHERE drivinglogid='$drivinglogid'  AND level='$next_level'";
				$result_update = mysqli_query($dbc, $query_update);

			}
		include ('fix_negative_bug.php');
    }
    if($action == 'update_comment') {
        $dl_comment = filter_var($value,FILTER_SANITIZE_STRING);
        $query_update = "UPDATE `driving_log_timer` SET dl_comment='$dl_comment', amendments_status = 'Pending' WHERE timerid='$id'";
        $result_update = mysqli_query($dbc, $query_update);
    }
    if($action == 'add') {
        $timer_name = $_GET['timer_name'];
        $ame_time = $_GET['ame_time'];
		$ender_time = $_GET['ender_time'];
		$drivinglogid = $_GET['id'];
		$timer_name = $_GET['timer_name'];

        $comment = $_GET['comment'];

		$resetter = NULL;
		// Get start time and end time difference.
		$reverse_explode = array_reverse(explode(':',$ame_time));

			$i = 0;
			$len = count($reverse_explode);

			foreach( $reverse_explode as $time ) {


				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				// …
				$i++;

			}
			$sec_from_min = $minutes * 60;
			$sec_from_hours = $hours*60*60;
			$total_secs_start = $sec_from_min+$sec_from_hours;


		$reverse_explode = array_reverse(explode(':',$ender_time));

			$i = 0;
			$len = count($reverse_explode);

			foreach( $reverse_explode as $time ) {


				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				// …
				$i++;

			}
			$sec_from_min = $minutes * 60;
			$sec_from_hours = $hours*60*60;
			$total_secs_end = $sec_from_min+$sec_from_hours;


			$timer_time = $total_secs_end - $total_secs_start;
			$timer_time = $timer_time/(60*60);

			$timer_time_hours = floor($timer_time);
			$time_mins = ($timer_time - $timer_time_hours)*60;
			$timer_time_hours = sprintf("%02d", $timer_time_hours);
			$time_mins = sprintf("%02d", $time_mins);
			$timer_time = $timer_time_hours.':'.$time_mins.':00';

        if($timer_name == 'Off-Duty') {

			// GET TOTAL OFF-DUTY TIME

		$col = "SELECT `reset_cycle` FROM driving_log_timer";
		$result = mysqli_query($dbc, $col);
		if (!$result){
			$colcreate = "ALTER TABLE `driving_log_timer` ADD COLUMN `reset_cycle` VARCHAR(555) NULL";
			$result = mysqli_query($dbc, $colcreate);
		}
		$get_driver = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM driving_log WHERE drivinglogid='$drivinglogid'"));
		$driverid = $get_driver['driverid'];
		$cycler = $get_driver['cycle'];

		$get_time_left = "SELECT * FROM `driving_log` WHERE `driverid` = '$driverid' AND `cycle` = '$cycler' ORDER BY `drivinglogid` DESC";

		$result1 = mysqli_query($dbc, $get_time_left);
		$num_rows1 = mysqli_num_rows($result1);

		if($num_rows1 > 0) {

			$on_duty_time = '';
			$seconds = 0;
			$minutes = 0;
			$hours = 0;

							$reverse_explode = array_reverse(explode(':',$timer_time));

							$i = 0;
							$len = count($reverse_explode);

							foreach( $reverse_explode as $time ) {

								if ($i == 0) {
									$minutes += $time;

								} else if ($i == $len - 1) {
									$hours += $time;
								}
								// …
								$i++;

							}

			while($row1 = mysqli_fetch_array($result1)) {

				$drivinglogid = $row1['drivinglogid'];

				$select_timers = "SELECT * FROM driving_log_timer WHERE drivinglogid = '$drivinglogid' ORDER BY timerid DESC";

				$result2 = mysqli_query($dbc, $select_timers);
				$num_rows2 = mysqli_num_rows($result2);
				$is_reset = '';
				if($num_rows2 > 0) {
					while($row2 = mysqli_fetch_array($result2)) {

						if($row2['reset_cycle'] == 1) {
							$is_reset .='1';
							break;
						}

						if($row2['off_duty_timer'] !== '' && $row2['off_duty_timer'] !== NULL) {

							$reverse_explode = array_reverse(explode(':',$row2['off_duty_timer']));

							$i = 0;
							$len = count($reverse_explode);

							foreach( $reverse_explode as $time ) {

								if ($i == 0) {
									$seconds += $time;
								} else if ($i == $len - 1) {
									$hours += $time;
								} else {
									$minutes += $time;
								}
								// …
								$i++;

							}
						}
					}
				}
			}
		}

		// SUM UP OFF DUTY TIME

				$minute_from_seconds = $seconds/60;
				$minute_add = floor($minute_from_seconds);
				$seconds_left = $minute_from_seconds - $minute_add;
				$seconds = $seconds_left*60;

				$minutes = $minutes + $minute_add;

				$hours_from_minutes = $minutes/60;
				$hour_add = floor($hours_from_minutes);
				$minutes_left = $hours_from_minutes - $hour_add;
				$minutes = $minutes_left*60;

				$hours = $hours+$hour_add;

				$hours_left = sprintf("%02d", $hours);
				$minutes_left = sprintf("%02d", $minutes);
				$seconds_left = sprintf("%02d", $seconds);
				//if statement

				if($cycler == 'Cycle 1(7 days)') {
					if($hours_left >= 36) {
						$resetter = 1;
					} else {
						$resetter = NULL;
					}
				} else {
					if($hours_left >= 72) {
						$resetter = 1;
					} else {
						$resetter = NULL;
					}
				}


		// END COUNT OF OFF DUTY TIME

            $time_name = 'off_duty_time';
            $timer_name = 'off_duty_timer';
        }
        if($timer_name == 'Sleeper Berth') {
            $time_name = 'sleeper_berth_time';
            $timer_name = 'sleeper_berth_timer';
        }
        if($timer_name == 'Driving') {
            $time_name = 'driving_time';
            $timer_name = 'driving_timer';
        }
        if($timer_name == 'On-Duty') {
            $time_name = 'on_duty_time';
            $timer_name = 'on_duty_timer';
        }
        $comment = str_replace("__","&",$comment);
        $comment = str_replace("***"," ",$comment);
        $comment = filter_var($comment,FILTER_SANITIZE_STRING);
		$result_timer = mysqli_num_rows(mysqli_query($dbc, "SELECT * FROM driving_log_timer WHERE drivinglogid='$id' AND inspection_mode IS NULL"));
		$level = $result_timer+1;
		$ender_time = date("g:i a", strtotime($ender_time));
		$ame_time = date("g:i a", strtotime($ame_time));
        $query = mysqli_query($dbc,"INSERT INTO `driving_log_timer` (level, drivinglogid, $timer_name, $time_name, end_$time_name, amendments_comment, reset_cycle) VALUES ('$level', '$id', '$timer_time', '$ame_time', '$ender_time', '$comment', '$resetter')");
		echo $query;

        $old_level = ($level-1);

        if($level != 1) {

            $result_level = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT off_duty_time, sleeper_berth_time, driving_time, on_duty_time, end_off_duty_time, end_sleeper_berth_time, end_driving_time, end_on_duty_time FROM driving_log_timer WHERE drivinglogid='$id' AND level='$old_level'"));
			// DONE GETTING DIFFERENCE...
            if($result_level['off_duty_time'] != '' && $result_level['off_duty_time'] != NULL) {
				$end_time_sub = $result_level['end_off_duty_time'];
				$start_time_sub = $result_level['off_duty_time'];
				$field_end = 'end_off_duty_time';
				$timerfield = 'off_duty_timer';
            }
            if($result_level['sleeper_berth_time'] !== '' && $result_level['sleeper_berth_time'] != NULL) {
				$end_time_sub = $result_level['end_sleeper_berth_time'];
				$start_time_sub = $result_level['sleeper_berth_time'];
				$field_end = 'end_sleeper_berth_time';
				$timerfield = 'sleeper_berth_timer';
            }
            if($result_level['driving_time'] !== '' && $result_level['driving_time'] != NULL) {
				$end_time_sub = $result_level['end_driving_time'];
				$start_time_sub = $result_level['driving_time'];
				$field_end = 'end_driving_time';
				$timerfield = 'driving_timer';
            }
            if($result_level['on_duty_time'] !== '' && $result_level['on_duty_time'] != NULL) {
				$end_time_sub = $result_level['end_on_duty_time'];
				$start_time_sub = $result_level['on_duty_time'];
				$field_end = 'end_on_duty_time';
				$timerfield = 'on_duty_timer';
            }

			// GET THE AMENDED TIME AND ORIGINAL START TIME DIFFERENCE FOR PREVIOUS TIMER
		/* Beginning time of the new amendment >> */ $end_time_sub  = date("H:i", strtotime($ame_time));
		/* Beginning time of the previous timer >> */ $start_time_sub  = date("H:i", strtotime($start_time_sub));
		$value = date("g:i a", strtotime($ame_time));
		// Get start time and end time difference.
		$reverse_explode = array_reverse(explode(':',$start_time_sub));
			$i = 0;
			$len = count($reverse_explode);
			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				$i++;
			}
			$sec_from_min = $minutes * 60;
			$sec_from_hours = $hours*60*60;
			$total_secs_start = $sec_from_min+$sec_from_hours;
			$reverse_explode = array_reverse(explode(':',$end_time_sub));
			$i = 0;
			$len = count($reverse_explode);
			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				$i++;
			}
			$sec_from_min = $minutes * 60;
			$sec_from_hours = $hours*60*60;
			$total_secs_end = $sec_from_min+$sec_from_hours;
			$timer_time = $total_secs_end - $total_secs_start;
			$timer_time = $timer_time/(60*60);
			$timer_time_hours = floor($timer_time);
			$time_mins = ($timer_time - $timer_time_hours)*60;
			$timer_time_hours = sprintf("%02d", $timer_time_hours);
			$time_mins = sprintf("%02d", $time_mins);
			$timer_time = $timer_time_hours.':'.$time_mins.':00';
		//	 $result_update_t = mysqli_query($dbc, "UPDATE `driving_log_timer` SET end_off_duty_time='$value' WHERE drivinglogid='$drivinglogid' AND level='$old_level'");
			$query_update = "UPDATE `driving_log_timer` SET $field_end='$value', $timerfield = '$timer_time'  WHERE drivinglogid='$drivinglogid'  AND level='$old_level'";
			echo $query_update;
			$result_update = mysqli_query($dbc, $query_update);

        }
    }
}

if($_GET['fill'] == 'end_amendments') {
    date_default_timezone_set('America/Denver');
	$id = $_GET['id'];
	$value = $_GET['value'];

    $result_level = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT off_duty_time, sleeper_berth_time, driving_time, on_duty_time, end_off_duty_time, end_sleeper_berth_time, end_driving_time, end_on_duty_time FROM driving_log_timer WHERE timerid='$id'"));
			// DONE GETTING DIFFERENCE...
            if($result_level['off_duty_time'] != '' && $result_level['off_duty_time'] != NULL) {
				$end_time_sub = $result_level['end_off_duty_time'];
				$start_time_sub = $result_level['off_duty_time'];
				$field_end = 'end_off_duty_time';
				$timerfield = 'off_duty_timer';
            }
            if($result_level['sleeper_berth_time'] !== '' && $result_level['sleeper_berth_time'] != NULL) {
				$end_time_sub = $result_level['end_sleeper_berth_time'];
				$start_time_sub = $result_level['sleeper_berth_time'];
				$field_end = 'end_sleeper_berth_time';
				$timerfield = 'sleeper_berth_timer';
            }
            if($result_level['driving_time'] !== '' && $result_level['driving_time'] != NULL) {
				$end_time_sub = $result_level['end_driving_time'];
				$start_time_sub = $result_level['driving_time'];
				$field_end = 'end_driving_time';
				$timerfield = 'driving_timer';
            }
            if($result_level['on_duty_time'] !== '' && $result_level['on_duty_time'] != NULL) {
				$end_time_sub = $result_level['end_on_duty_time'];
				$start_time_sub = $result_level['on_duty_time'];
				$field_end = 'end_on_duty_time';
				$timerfield = 'on_duty_timer';
            }

    $query_update = "UPDATE `driving_log_timer` SET $field='$value' WHERE timerid='$id'";
    $result_update = mysqli_query($dbc, $query_update);


	// GET THE AMENDED TIME AND ORIGINAL START TIME DIFFERENCE FOR PREVIOUS TIMER
		/* End time of the newly amendmended timer >> */ $end_time_sub  = date("H:i", strtotime($value));
		/* Beginning time of the newly amendmended timer >> */ $start_time_sub  = date("H:i", strtotime($start_time_sub));
		$value = date("g:i a", strtotime($value));
		// Get start time and end time difference.
		$reverse_explode = array_reverse(explode(':',$start_time_sub));
			$i = 0;
			$len = count($reverse_explode);
			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				$i++;
			}
			$sec_from_min = $minutes * 60;
			$sec_from_hours = $hours*60*60;
			$total_secs_start = $sec_from_min+$sec_from_hours;
			$reverse_explode = array_reverse(explode(':',$end_time_sub));
			$i = 0;
			$len = count($reverse_explode);
			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				$i++;
			}
			$sec_from_min = $minutes * 60;
			$sec_from_hours = $hours*60*60;
			$total_secs_end = $sec_from_min+$sec_from_hours;
			$timer_time = $total_secs_end - $total_secs_start;
			$timer_time = $timer_time/(60*60);
			$timer_time_hours = floor($timer_time);
			$time_mins = ($timer_time - $timer_time_hours)*60;
			$timer_time_hours = sprintf("%02d", $timer_time_hours);
			$time_mins = sprintf("%02d", $time_mins);
			$timer_time = $timer_time_hours.':'.$time_mins.':00';
			$query_update = "UPDATE `driving_log_timer` SET $field_end='$value', $timerfield = '$timer_time', amendments_status = 'Pending' WHERE timerid='$id'";
			$result_update = mysqli_query($dbc, $query_update);
}

if($_GET['fill'] == 'timer_amendments') {
    date_default_timezone_set('America/Denver');
	$id = $_GET['id'];
	$drivinglogid = $_GET['id'];
    $timer_name = $_GET['timer_name'];
    $ame_time = $_GET['ame_time'];
	$ender_time = $_GET['ender_time'];
	$resetter = NULL;
		// Get start time and end time difference.
		$reverse_explode = array_reverse(explode(':',$ame_time));

			$i = 0;
			$len = count($reverse_explode);

			foreach( $reverse_explode as $time ) {


				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				// …
				$i++;

			}
			$sec_from_min = $minutes * 60;
			$sec_from_hours = $hours*60*60;
			$total_secs_start = $sec_from_min+$sec_from_hours;


		$reverse_explode = array_reverse(explode(':',$ender_time));

			$i = 0;
			$len = count($reverse_explode);

			foreach( $reverse_explode as $time ) {


				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				// …
				$i++;

			}
			$sec_from_min = $minutes * 60;
			$sec_from_hours = $hours*60*60;
			$total_secs_end = $sec_from_min+$sec_from_hours;

			$timer_time = $total_secs_end - $total_secs_start;
			$timer_time = $timer_time/(60*60);

			$timer_time_hours = floor($timer_time);
			$time_mins = ($timer_time - $timer_time_hours)*60;
			$timer_time_hours = sprintf("%02d", $timer_time_hours);
			$time_mins = sprintf("%02d", $time_mins);
			$timer_time = $timer_time_hours.':'.$time_mins.':00';

    $comment = $_GET['comment'];

    if($timer_name == 'Off-Duty') {

			// GET TOTAL OFF-DUTY TIME

		$col = "SELECT `reset_cycle` FROM driving_log_timer";
		$result = mysqli_query($dbc, $col);
		if (!$result){
			$colcreate = "ALTER TABLE `driving_log_timer` ADD COLUMN `reset_cycle` VARCHAR(555) NULL";
			$result = mysqli_query($dbc, $colcreate);
		}
		$get_driver = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM driving_log WHERE drivinglogid='$drivinglogid'"));
		$driverid = $get_driver['driverid'];
		$cycler = $get_driver['cycle'];

		$get_time_left = "SELECT * FROM `driving_log` WHERE `driverid` = '$driverid' AND `cycle` = '$cycler' ORDER BY `drivinglogid` DESC";

		$result1 = mysqli_query($dbc, $get_time_left);
		$num_rows1 = mysqli_num_rows($result1);

		if($num_rows1 > 0) {

			$on_duty_time = '';
			$seconds = 0;
			$minutes = 0;
			$hours = 0;

							$reverse_explode = array_reverse(explode(':',$timer_time));

							$i = 0;
							$len = count($reverse_explode);

							foreach( $reverse_explode as $time ) {

								if ($i == 0) {
									$minutes += $time;

								} else if ($i == $len - 1) {
									$hours += $time;
								}
								// …
								$i++;

							}

			while($row1 = mysqli_fetch_array($result1)) {

				$drivinglogid = $row1['drivinglogid'];

				$select_timers = "SELECT * FROM driving_log_timer WHERE drivinglogid = '$drivinglogid' ORDER BY timerid DESC";

				$result2 = mysqli_query($dbc, $select_timers);
				$num_rows2 = mysqli_num_rows($result2);
				$is_reset = '';
				if($num_rows2 > 0) {
					while($row2 = mysqli_fetch_array($result2)) {

						if($row2['reset_cycle'] == 1) {
							$is_reset .='1';
							break;
						}

						if($row2['off_duty_timer'] !== '' && $row2['off_duty_timer'] !== NULL) {

							$reverse_explode = array_reverse(explode(':',$row2['off_duty_timer']));

							$i = 0;
							$len = count($reverse_explode);

							foreach( $reverse_explode as $time ) {

								if ($i == 0) {
									$seconds += $time;
								} else if ($i == $len - 1) {
									$hours += $time;
								} else {
									$minutes += $time;
								}
								// …
								$i++;

							}
						}
					}
				}
			}
		}

		// SUM UP OFF DUTY TIME

				$minute_from_seconds = $seconds/60;
				$minute_add = floor($minute_from_seconds);
				$seconds_left = $minute_from_seconds - $minute_add;
				$seconds = $seconds_left*60;

				$minutes = $minutes + $minute_add;

				$hours_from_minutes = $minutes/60;
				$hour_add = floor($hours_from_minutes);
				$minutes_left = $hours_from_minutes - $hour_add;
				$minutes = $minutes_left*60;

				$hours = $hours+$hour_add;

				$hours_left = sprintf("%02d", $hours);
				$minutes_left = sprintf("%02d", $minutes);
				$seconds_left = sprintf("%02d", $seconds);
				//if statement

				if($cycler == 'Cycle 1(7 days)') {
					if($hours_left >= 36) {
						$resetter = 1;
					} else {
						$resetter = NULL;
					}
				} else {
					if($hours_left >= 72) {
						$resetter = 1;
					} else {
						$resetter = NULL;
					}
				}


		// END COUNT OF OFF DUTY TIME


        $time_name = 'off_duty_time';
        $timer_name = 'off_duty_timer';
    }
    if($timer_name == 'Sleeper Berth') {
        $time_name = 'sleeper_berth_time';
        $timer_name = 'sleeper_berth_timer';
    }
    if($timer_name == 'Driving') {
        $time_name = 'driving_time';
        $timer_name = 'driving_timer';
    }
    if($timer_name == 'On-Duty') {
        $time_name = 'on_duty_time';
        $timer_name = 'on_duty_timer';
    }
    $comment = str_replace("__","&",$comment);
    $comment = str_replace("***"," ",$comment);

    $comment = filter_var($comment,FILTER_SANITIZE_STRING);
    $result_timer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(timerid) AS total_timer FROM driving_log_timer WHERE drivinglogid='$id'"));
    $level = $result_timer['total_timer']+1;

	$ender_time = date("g:i a", strtotime($ender_time));
	$ame_time = date("g:i a", strtotime($ame_time));

    $query = mysqli_query($dbc,"INSERT INTO `driving_log_timer` (level, drivinglogid, $timer_name, $time_name,end_$time_name, dl_comment, reset_cycle, amendments_status) VALUES ('$level', '$id', '$timer_time', '$ame_time', '$ender_time', '$comment', '$resetter', 'Pending')");

}

if($_GET['fill'] == 'timerpriority') {
	$timerid = $_GET['timerid'];
	$level = $_GET['priority'];

	//
	$result_t = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT drivinglogid, level FROM driving_log_timer WHERE timerid='$timerid'"));
	$db_drivinglogid = $result_t['drivinglogid'];
	$db_level = $result_t['level'];

	$query_update_es = "UPDATE `driving_log_timer` SET `level` = '$level' WHERE `timerid` = '$timerid'";
	$result_update_es = mysqli_query($dbc, $query_update_es);

	// Update all other priority
	if($db_level > $level) {
		$ticket_select1 = "SELECT * FROM driving_log_timer WHERE drivinglogid='$db_drivinglogid' AND level >= '$level' AND level < '$db_level' AND timerid != '$timerid'";
		$result1 = mysqli_query($dbc, $ticket_select1);
		$num_rows1 = mysqli_num_rows($result1);

		if($num_rows1 > 0) {
			while($row1 = mysqli_fetch_array($result1)) {
				$timerid_gp = $row1['timerid'];
				$query_update_t1 = "UPDATE `driving_log_timer` SET level = level + 1 WHERE `timerid` = '$timerid_gp'";
				$result_update_t1 = mysqli_query($dbc, $query_update_t1);
			}
		}
	}
	if($db_level < $level) {
		$ticket_select2 = "SELECT * FROM driving_log_timer WHERE drivinglogid='$db_drivinglogid' AND level <= '$level' AND level > '$db_level' AND timerid != '$timerid'";
		$result2 = mysqli_query($dbc, $ticket_select2);
		$num_rows2 = mysqli_num_rows($result2);

		if($num_rows2 > 0) {
			while($row2 = mysqli_fetch_array($result2)) {
				$timerid_lp = $row2['timerid'];
				$query_update_t2 = "UPDATE `driving_log_timer` SET level = level - 1 WHERE `timerid` = '$timerid_lp'";
				$result_update_t2 = mysqli_query($dbc, $query_update_t2);
			}
		}
	}


	//
}
}
if(isset($_GET['view_log_info'])) {
	date_default_timezone_set('America/Denver');
	$drivinglogid = $_GET['drivinglogid'];
	include ('fix_negative_bug.php');
	$result_graph = mysqli_query($dbc, "SELECT * FROM driving_log_timer WHERE drivinglogid='$drivinglogid' ORDER BY level");
	$result = mysqli_query($dbc, "SELECT MAX(level) FROM driving_log_timer WHERE drivinglogid='".$drivinglogid."'");
	$max_level = mysqli_fetch_array($result);
	$next_level = $max_level[0]+1;

	// DELETE PREVIOUS INSPECTION MODE TIMER ROW

	$query_insert_graph = 'DELETE FROM driving_log_timer WHERE inspection_mode=1 AND drivinglogid = "'.$drivinglogid.'"';
	$result_insert_graph = mysqli_query($dbc, $query_insert_graph);

    $graph_value = '';
    $start_time = '00:00';
	$start_log = '';
    $result_dl = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM driving_log_timer WHERE drivinglogid='$drivinglogid' AND level = 1"));
	if($result_dl['end_off_duty_time'] !== '' && $result_dl['end_off_duty_time'] !== NULL) {
		$start_log = date("G:i", strtotime($result_dl['end_off_duty_time']));
	} else if ($result_dl['end_driving_time'] !== '' && $result_dl['end_driving_time'] !== NULL) {
		$start_log = date("G:i", strtotime($result_dl['end_driving_time']));
	} else if ($result_dl['end_sleeper_berth_time'] !== '' && $result_dl['end_sleeper_berth_time'] !== NULL) {
		$start_log = date("G:i", strtotime($result_dl['end_sleeper_berth_time']));
	} else if($result_dl['end_on_duty_time'] !== '' && $result_dl['end_on_duty_time'] !== NULL) {
		$start_log = date("G:i", strtotime($result_dl['end_on_duty_time']));
	}
    if($start_log != '00:00') {
			$end_time=0;
		//Turn minutes into a fraction
			$reverse_explode = array_reverse(explode(':',$start_log));
			$i = 0;
			$len = count($reverse_explode);

			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				// …
				$i++;
			}

			$minutes = round(($minutes/60)*100);
			if(strlen($minutes) < 2) {
					$minutes = '0'.$minutes;
			}
			/*This is the converted variable: */$start_log = $hours.'.'.$minutes;
		//Finish minute to fraction conversion

		$start_log = ltrim($start_log, '0');
        $graph_value .= "40,, [0.00, ".$start_log."],, 'Off-Duty',,";

        $start_time = str_replace(".", ":", $start_log);

    }

    while($row = mysqli_fetch_array($result_graph)) {

        // Final

        if($row['off_duty_timer'] != '') {

            //$end_time = addTime($start_time, date("G:i", strtotime($row['final_off_duty_timer']));
            $fet = str_replace(":", ".",  date("G:i", strtotime($row['end_off_duty_time'])));
            $fst = str_replace(":", ".", date("G:i", strtotime($row['off_duty_time'])));

			//Turn minutes into a fraction
			$reverse_explode = array_reverse(explode('.',$fet));
			$i = 0;
			$len = count($reverse_explode);

			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				// …
				$i++;
			}
			$minutes = round(($minutes/60)*100);
			if($minutes > 100) {
				$minutes = 99;
			}
			if(strlen($minutes) < 2) {
					$minutes = '0'.$minutes;
			}
			/*This is the converted variable: */$fet = $hours.'.'.$minutes;
		//Finish minute to fraction conversion
		//Turn minutes into a fraction
			$reverse_explode = array_reverse(explode('.',$fst));
			$i = 0;
			$len = count($reverse_explode);
			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				// …
				$i++;
			}
			$minutes = round(($minutes/60)*100);
			if($minutes > 100) {
				$minutes = 99;
			}
			if(strlen($minutes) < 2) {
					$minutes = '0'.$minutes;
			}
			/* This is the converted variable: */$fst = $hours.'.'.$minutes;
		//Finish minute to fraction conversion
            $final_end_time = ltrim($fet, '0');
            $final_start_time = ltrim($fst, '0');

            //$graph_value .= "40,, [".$final_start_time.", ".$final_end_time."],, 'Off-Duty',,";
			$graph_value .= "40,, [".$final_start_time.", ".$final_end_time."],, 'Off-Duty',,";

            $start_time = $end_time;
        }

        if($row['sleeper_berth_timer'] != '') {
           // $end_time = addTime($start_time, $row['final_sleeper_berth_timer']);
            $fet = str_replace(":", ".", date("G:i", strtotime($row['end_sleeper_berth_time'])));
            $fst = str_replace(":", ".", date("G:i", strtotime($row['sleeper_berth_time'])));
			//Turn minutes into a fraction
			$reverse_explode = array_reverse(explode('.',$fet));
			$i = 0;
			$len = count($reverse_explode);

			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				// …
				$i++;
			}
			$minutes = round(($minutes/60)*100);
			if($minutes > 100) {
				$minutes = 99;
			}
			if(strlen($minutes) < 2) {
					$minutes = '0'.$minutes;
			}
			/*This is the converted variable: */$fet = $hours.'.'.$minutes;
		//Finish minute to fraction conversion
		//Turn minutes into a fraction
			$reverse_explode = array_reverse(explode('.',$fst));
			$i = 0;
			$len = count($reverse_explode);

			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				// …
				$i++;
			}
			$minutes = round(($minutes/60)*100);
			if($minutes > 100) {
				$minutes = 99;
			}
			if(strlen($minutes) < 2) {
					$minutes = '0'.$minutes;
			}
			/*This is the converted variable: */$fst = $hours.'.'.$minutes;
		//Finish minute to fraction conversion
            $final_end_time = ltrim($fet, '0');
            $final_start_time = ltrim($fst, '0');
            //$graph_value .= '{x: 20, y:['.$final_start_time.', '.$final_end_time.'], label: "Sleeper Berth"},';
            $graph_value .= "30,, [".$final_start_time.", ".$final_end_time."],, 'Sleeper Berth',,";

            $start_time = $end_time;
        }

        if($row['driving_timer'] != '') {
            //$end_time = addTime($start_time, $row['final_driving_timer']);
            $fet = str_replace(":", ".", date("G:i", strtotime($row['end_driving_time'])));
            $fst = str_replace(":", ".", date("G:i", strtotime($row['driving_time'])));
			//Turn minutes into a fraction
			$reverse_explode = array_reverse(explode('.',$fet));
			$i = 0;
			$len = count($reverse_explode);

			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				// …
				$i++;
			}
			$minutes = round(($minutes/60)*100);
			if($minutes > 100) {
				$minutes = 99;
			}
			if(strlen($minutes) < 2) {
					$minutes = '0'.$minutes;
			}
			/*This is the converted variable: */$fet = $hours.'.'.$minutes;
		//Finish minute to fraction conversion
		//Turn minutes into a fraction
			$reverse_explode = array_reverse(explode('.',$fst));
			$i = 0;
			$len = count($reverse_explode);

			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				// …
				$i++;
			}
			$minutes = round(($minutes/60)*100);
			if($minutes > 100) {
				$minutes = 99;
			}
			if(strlen($minutes) < 2) {
					$minutes = '0'.$minutes;
			}
			/* This is the converted variable: */$fst = $hours.'.'.$minutes;
		//Finish minute to fraction conversion
            $final_end_time = ltrim($fet, '0');
            $final_start_time = ltrim($fst, '0');
            //$graph_value .= '{x: 30, y:['.$final_start_time.', '.$final_end_time.'], label: "Driving"},';
            $graph_value .= "20,, [".$final_start_time.", ".$final_end_time."],, 'Driving',,";

            $start_time = $end_time;
        }

        if($row['on_duty_timer'] != '') {
            //$end_time = addTime($start_time, $row['final_on_duty_timer']);

            $fet = str_replace(":", ".", date("G:i", strtotime($row['end_on_duty_time'])));
            $fst = str_replace(":", ".", date("G:i", strtotime($row['on_duty_time'])));
			//Turn minutes into a fraction
			$reverse_explode = array_reverse(explode('.',$fet));
			$i = 0;
			$len = count($reverse_explode);

			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				// …
				$i++;
			}
			$minutes = round(($minutes/60)*100);
			if($minutes > 100) {
				$minutes = 99;
			}
			if(strlen($minutes) < 2) {
					$minutes = '0'.$minutes;
			}
			/* This is the converted variable: */$fet = $hours.'.'.$minutes;
		//Finish minute to fraction conversion
		//Turn minutes into a fraction
			$reverse_explode = array_reverse(explode('.',$fst));
			$i = 0;
			$len = count($reverse_explode);

			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				// …
				$i++;
			}
			$minutes = round(($minutes/60)*100);
			if($minutes > 100) {
				$minutes = 99;
			}
			if(strlen($minutes) < 2) {
					$minutes = '0'.$minutes;
			}
			/*This is the converted variable: */$fst = $hours.'.'.$minutes;
		//Finish minute to fraction conversion
            $final_end_time = ltrim($fet, '0');
            $final_start_time = ltrim($fst, '0');
            //$graph_value .= '{x: 40, y:['.$final_start_time.', '.$final_end_time.'], label: "On-Duty"},';
            $graph_value .= "10,, [".$final_start_time.", ".$final_end_time."],, 'On-Duty',,";

            $start_time = $end_time;
        }

    }

    //$graph_value_f =  str_replace("23.59","24.00",$graph_value);

    $gv =  str_replace("[.","[0.",$graph_value);

    $g1 = substr($gv, 0, -2);

    $final_string = $g1;
	$time_ofthe_timer = $_GET['timer_valu'];


	// Start GET onclick timer start time and finish time for graph
	$reverse_explode = array_reverse(explode(':',$time_ofthe_timer));

			$i = 0;
			$len = count($reverse_explode);

			foreach( $reverse_explode as $time ) {


				if ($i == 0) {
					$seconds = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				} else {
					$minutes = $time;
				}
				// …
				$i++;

			}

			$sec_from_min = $minutes * 60;
			$sec_from_hours = $hours*60*60;
			$total_secs = $sec_from_min+$sec_from_hours+$seconds;
			$ongoing_times = date('G.i', strtotime(date('h:i:s A'))-$total_secs);
			$ongoing_end_time = date('G.i');

			//Turn minutes into a fraction
			$reverse_explode = array_reverse(explode('.',$ongoing_end_time));
			$i = 0;
			$len = count($reverse_explode);

			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				// …
				$i++;
			}
			$minutes = round(($minutes/60)*100);
			if($minutes > 100) {
				$minutes = 99;
			}
			if(strlen($minutes) < 2) {
					$minutes = '0'.$minutes;
			}
			/*This is the converted variable: */$ongoing_end_time = $hours.'.'.$minutes;
		//Finish minute to fraction conversion


			//Turn minutes into a fraction
			$reverse_explode = array_reverse(explode('.',$ongoing_times));
			$i = 0;
			$len = count($reverse_explode);

			foreach( $reverse_explode as $time ) {
				if ($i == 0) {
					$minutes = $time;
				} else if ($i == $len - 1) {
					$hours = $time;
				}
				// …
				$i++;
			}
			$minutes = round(($minutes/60)*100);
			if($minutes > 100) {
				$minutes = 99;
			}
			if(strlen($minutes) < 2) {
					$minutes = '0'.$minutes;
			}
			/*This is the converted variable: */$ongoing_times = $hours.'.'.$minutes;
		//Finished minute to fraction conversion


	// Finished onclick timer start time and finish time for graph
	//$end_timer_nom = $_GET['end_timer_name'];
	$end_timer_nom = $_GET['timer_names'];
	$what_timer = $end_timer_nom;
	if($end_timer_nom == 'on_duty_timer') {
		$end_timer_nom = 'On-Duty';
		$locator = '10';

	}
	if($end_timer_nom == 'off_duty_timer') {
		$end_timer_nom = 'Off-Duty';
		$locator = '40';
	}
	if($end_timer_nom == 'driving_timer') {
		$end_timer_nom = 'Driving';
		$locator = '20';
	}
	if($end_timer_nom == 'sleeper_berth_timer') {
		$end_timer_nom = 'Sleeper Berth';
		$locator = '30';
	}
	$query_insert_graph = 'INSERT INTO `driving_log_timer` (`drivinglogid`, `level`, `'.$what_timer.'`, `inspection_mode`, `dl_comment`) VALUES ("'.$drivinglogid.'", "'.$next_level.'", "'.$time_ofthe_timer.'", "1", "Time at Inspection Mode")';
	$result_insert_graph = mysqli_query($dbc, $query_insert_graph);

    if($final_end_time < '24.00') {
        $final_string .= ",,".$locator.",, [".$ongoing_times.",".$ongoing_end_time."],, '".$end_timer_nom."'";
    }

    if (strpos($final_string,'Off-Duty') === false) {
        $final_string .= ",,40,, [24.00,24.00],, 'Off-Duty'";
    }
    if (strpos($final_string,'Driving') === false) {
        $final_string .= ",,20,, [24.00,24.00],, 'Driving'";
    }
    if (strpos($final_string,'On-Duty') === false) {
        $final_string .= ",,10,, [24.00,24.00],, 'On-Duty'";
    }
    if (strpos($final_string,'Sleeper Berth') === false) {
        $final_string .= ",,30,, [24.00,24.00],, 'Sleeper Berth'";
    }
	$final_string =  str_replace("[.","[0.",$final_string);
	$final_string =  str_replace(", .",", 0.",$final_string);

	$graphid = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM driving_log_graph WHERE drivinglogid='$drivinglogid'"));
	$graphid = $graphid['graphid'];
		if ($graphid !== NULL && $graphid !== ''){
			$query_insert_graph = 'UPDATE driving_log_graph SET graph_data = "'.$final_string.'"  WHERE drivinglogid = "'.$drivinglogid.'"';
			 $result_insert_graph = mysqli_query($dbc, $query_insert_graph);
			//echo $query_insert_graph;
			if (!$result_insert_graph) {
				die('Invalid query: ' . mysql_error());
			}
		} else {

			$query_insert_graph = 'INSERT INTO `driving_log_graph` (`drivinglogid`, `graph_data`) VALUES ("'.$drivinglogid.'", "'.$final_string.'")';
			 $result_insert_graph = mysqli_query($dbc, $query_insert_graph);
		}

	include('notices.php');

    //$query_update_log = "UPDATE `driving_log` SET `status` = 'Done'  WHERE `drivinglogid` = '$drivinglogid'";
    //$result_update_log = mysqli_query($dbc, $query_update_log);
    //header('Location: amendments.php?graph=on&drivinglogid='.$drivinglogid);
	?>
	<?php
}

if($_GET['fill'] == 'auditdismiss') {
	$dlogid = $_GET['dlogid'];
	$turn = $_GET['turn'];
	$query_update_t2 = "UPDATE `driving_log` SET audit_dismiss = '$turn' WHERE `drivinglogid` = '$dlogid'";
	$result_update_t2 = mysqli_query($dbc, $query_update_t2);
}

if($_GET['fill'] == 'checkoffdays') {
    $driverid = $_GET['driverid'];
    $yesterday = date('Y-m-d', strtotime(date('Y-m-d').' - 1 days'));

    $query_on_days = "SELECT * FROM `driving_log` WHERE `driverid` = '$driverid' AND `start_date` IS NOT NULL AND `start_date` != '' AND `start_date` != '0000-00-00' ORDER BY `start_date` DESC";
    $result_on_days = mysqli_fetch_assoc(mysqli_query($dbc, $query_on_days));
    $on_day_date = $result_on_days['start_date'];

    if (!empty($on_day_date)) {
        $query_off_days = "SELECT * FROM `driving_log_time_off` WHERE `driverid` = '$driverid' AND `end_date` IS NOT NULL AND `end_date` != '' AND `end_date` != '0000-00-00' ORDER BY `end_date` DESC";
        $result_off_days = mysqli_fetch_assoc(mysqli_query($dbc, $query_off_days));
        $off_day_date = $result_off_days['end_date'];
        if (strtotime($off_day_date) >= strtotime($yesterday)) {
            echo 'good';
        } else if (strtotime($on_day_date) >= strtotime($yesterday)) {
            echo 'good';
        } else {
            if (strtotime($off_day_date) >= strtotime($on_day_date)) {
                $from_date = date('Y-m-d', strtotime($off_day_date.' + 1 days'));
            } else {
                $from_date = date('Y-m-d', strtotime($on_day_date.' + 1 days'));
            }
            echo $from_date.','.$yesterday;
        }
    } else {
        echo 'good';
    }
}

if($_GET['fill'] == 'logdaysoff') {
    $driverid = $_GET['driverid'];
    $start_date = $_GET['startdate'];
    $end_date = $_GET['enddate'];
    $main_office_address = get_config($dbc, 'main_office_address_dl');

    mysqli_query($dbc, "INSERT INTO `driving_log_time_off` (`start_date`, `end_date`, `main_office_addy`, `driverid`) VALUES ('$start_date', '$end_date', '$main_office_address', '$driverid')");
}

if($_GET['fill'] == 'toggleviewmode') {
    $contactid = $_GET['contactid'];
    $query = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT *, COUNT(*) as num_rows FROM `driving_log_view_only_mode` WHERE `contactid` = '$contactid'"));
    // $view_only_mode = ($query['view_only_mode'] == 1 ? 0 : 1);
    $view_only_mode = ($_GET['viewonlymode'] == '1' ? 0 : 1);
    if ($query['num_rows'] > 0) {
        mysqli_query($dbc, "UPDATE `driving_log_view_only_mode` SET `view_only_mode` = '$view_only_mode' WHERE `contactid` = '$contactid'");
    } else {
        mysqli_query($dbc, "INSERT INTO `driving_log_view_only_mode` (`contactid`, `view_only_mode`) VALUES ('$contactid', '$view_only_mode')");
    }
}
else if($_GET['action'] == 'mileage_fields') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	if($name == 'start' || $name == 'end') {
		$value = date('Y-m-d h:i:s',strtotime($value));
	}
	if($id > 0) {
		mysqli_query($dbc, "UPDATE `mileage` SET `$name`='$value' WHERE `id`='$id'");
	} else {
		mysqli_query($dbc, "INSERT INTO `mileage` (`$name`) VALUES ('$value')");
		echo mysqli_insert_id($dbc);
	}
} ?>