<?php $layout = get_config($dbc, 'timesheet_layout');
$value_config = explode(',',get_field_config($dbc, 'time_cards'));
define('COLSPAN', 1 + (in_array('schedule',$value_config) ? 1 : 0) + (in_array('scheduled',$value_config) ? 1 : 0) + (in_array('ticketid',$value_config) ? 1 : 0) + (in_array('show_hours',$value_config) ? 1 : 0)
    + (in_array('total_tracked_hrs',$value_config) && in_array($layout,['', 'multi_line']) ? 1 : 0) + (in_array('start_time',$value_config) || in_array('start_time_editable',$value_config) ? 1 : 0)
    + (in_array('end_time',$value_config) || in_array('end_time_editable',$value_config) ? 1 : 0) + (in_array('planned_hrs',$value_config) ? 1 : 0)
    + (in_array('tracked_hrs',$value_config) ? 1 : 0) + (in_array('total_tracked_time',$value_config) ? 1 : 0) + (in_array('start_day_tile',$value_config) ? 1 : 0) + ($layout == 'ticket_task')
    + ($layout == 'position_dropdown') + (in_array('total_tracked_hrs',$value_config) && in_array($layout,['position_dropdown', 'ticket_task']) ? 1 : 0)
    + (in_array($layout,['position_dropdown', 'ticket_task']) ? 1 : 0));

function get_hours_report($dbc, $staff, $search_start_date, $search_end_date, $search_position, $search_project, $search_ticket, $report_format = '', $hours_types, $override_value_config = '') {
  $filter_query = '';
  if(!empty($search_project)) {
    $filter_query .= " AND `projectid` = '$search_project'";
  }
  if(!empty($search_ticket)) {
    $filter_query .= " AND `ticketid` = '$search_ticket'";
  }

	if($staff == '') {
    if(empty($search_project) && empty($search_ticket) && empty($search_position)) {
  		return '<h4>Please select a staff member.</h4>';
    } else {
      $filter_position_query = '';

      if(!empty($search_position)) {
        $tickets_sql = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT(`ticketid`) FROM `ticket_attached` WHERE `deleted` = 0 AND `position` = '$search_position' AND (`src_table` = 'Staff_tasks' OR `src_table` = 'Staff')"),MYSQLI_ASSOC);
        $tickets_position = [];
        foreach ($tickets_sql as $ticket_sql) {
          $tickets_position[] = $ticket_sql['ticketid'];
        }
        $tickets_position = "'".implode("'",$tickets_position)."'";
        $filter_position_query = " AND `ticketid` IN ($tickets_position)";
      }
      $staff_with_filters = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT `staff` FROM `time_cards` WHERE `date` < '$search_start_date' AND `date` >= '$start_of_year' AND `deleted`=0 $filter_query $filter_position_query"),MYSQLI_ASSOC);
      foreach($staff_with_filters as $search_staff) {
        $staff_list[] = ['contactid'=>$search_staff['staff'],'first_name'=>'','last_name'=>get_contact($dbc, $search_staff['staff'])];
      }
    }
	// } else if($staff == 'ALL') {
	// 	$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category`='Staff' AND `deleted`=0 AND `status` > 0 AND `contactid` IN (SELECT `staff` FROM `time_cards` WHERE `deleted`=0)"));
	// } else if($staff > 0) {
	// 	$staff_list = [['contactid'=>$staff,'first_name'=>'','last_name'=>get_contact($dbc,$staff)]];
	// }
  } else {
    $staff_list = [];
    foreach (explode(',',$staff) as $search_staff) {
      if($search_staff > 0) {
        $staff_list[] = ['contactid'=>$search_staff,'first_name'=>'','last_name'=>get_contact($dbc, $search_staff)];
      }
    }
  }
	include_once('../Calendar/calendar_functions_inc.php');
	$layout = get_config($dbc, 'timesheet_layout');
	$highlight = get_config($dbc, 'timesheet_highlight');
	$mg_highlight = get_config($dbc, 'timesheet_manager');
	$submit_mode = get_config($dbc, 'timesheet_submit_mode');
	$value_config = explode(',',get_field_config($dbc, 'time_cards'));
	$timesheet_comment_placeholder = get_config($dbc, 'timesheet_comment_placeholder');
	$timesheet_start_tile = get_config($dbc, 'timesheet_start_tile');
	$timesheet_rounding = get_config($dbc, 'timesheet_rounding');
	$timesheet_rounded_increment = get_config($_SERVER['DBC'], 'timesheet_rounded_increment') / 60;
	if(!in_array('reg_hrs',$value_config) && !in_array('direct_hrs',$value_config) && !in_array('payable_hrs',$value_config)) {
		$value_config = array_merge($value_config,['reg_hrs','extra_hrs','relief_hrs','sleep_hrs','sick_hrs','sick_used','stat_hrs','stat_used','vaca_hrs','vaca_used']);
	}
	if(!empty($override_value_config)) {
		$value_config = explode(',',$override_value_config);
	}
	$report = '';
	if($report_format == 'to_array') {
		$report_output = [];
	}
	$timesheet_time_format = get_config($dbc, 'timesheet_time_format');
	$report_name = [];
	$report_blocks = [];

	foreach($staff_list as $staff) {
    $search_staff = $staff['contactid'];

    $filter_position_query = '';
    if(!empty($search_position)) {
      $tickets_sql = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT(`ticketid`) FROM `ticket_attached` WHERE `deleted` = 0 AND `position` = '$search_position' AND (`src_table` = 'Staff_tasks' OR `src_table` = 'Staff') AND `item_id` = '$search_staff'"),MYSQLI_ASSOC);
      $tickets_position = [];
      foreach ($tickets_sql as $ticket_sql) {
        $tickets_position[] = $ticket_sql['ticketid'];
      }
      $tickets_position = "'".implode("'",$tickets_position)."'";
      $filter_position_query = " AND `ticketid` IN ($tickets_position)";
    }

		$report_name[] = $staff['first_name'].' '.$staff['last_name'];
		$report_block = '';
		$schedule = mysqli_fetch_array(mysqli_query($dbc, "SELECT `scheduled_hours`, `schedule_days` FROM `contacts` WHERE `contactid`='$search_staff'"));
		$schedule_hrs = explode('*',$schedule['scheduled_hours']);
		$schedule_days = explode(',',$schedule['schedule_days']);
		$schedule_list = [0=>'---',1=>'---',2=>'---',3=>'---',4=>'---',5=>'---',6=>'---'];
		foreach($schedule_days as $key => $day_of_week) {
			$schedule_list[$day_of_week] = $schedule_hrs[$day_of_week];
		}

		$start_of_year = date('Y-01-01', strtotime($search_start_date));
		$sql = "SELECT IFNULL(SUM(IF(`type_of_time`='Sick Hrs.Taken',`total_hrs`,0)),0) SICK_HRS,
			IFNULL(SUM(IF(`type_of_time`='Stat Hrs.',`total_hrs`,0)),0) STAT_AVAIL,
			IFNULL(SUM(IF(`type_of_time`='Stat Hrs.Taken',`total_hrs`,0)),0) STAT_HRS,
			IFNULL(SUM(IF(`type_of_time`='Vac Hrs.',`total_hrs`,0)),0) VACA_AVAIL,
			IFNULL(SUM(IF(`type_of_time`='Vac Hrs.Taken',`total_hrs`,0)),0) VACA_HRS
			FROM `time_cards` WHERE `staff`='$search_staff' AND `date` < '$search_start_date' AND `date` >= '$start_of_year' AND `deleted`=0 $filter_query $filter_position_query";
		$year_to_date = mysqli_fetch_array(mysqli_query($dbc, $sql));

		$stat_hours = $year_to_date['STAT_AVAIL'];
		$stat_taken = $year_to_date['STAT_HRS'];
		$vacation_hours = $year_to_date['VACA_AVAIL'];
		$vacation_taken = $year_to_date['VACA_HRS'];
		$sick_taken = $year_to_date['SICK_HRS'];

		$colspan = 2;
		if(in_array('schedule',$value_config)) {
			$colspan++;
		}
		if(in_array('scheduled',$value_config)) {
			$colspan++;
		}
		if(in_array('ticketid',$value_config)) {
			$colspan++;
		}
		if(in_array('planned_hrs',$value_config)) {
			$colspan++;
		}
		if(in_array('tracked_hrs',$value_config)) {
			$colspan++;
		}
		if(in_array('total_tracked_time',$value_config)) {
			$colspan++;
		}
		if(in_array('start_time',$value_config)) {
			$colspan++;
		}
		if(in_array('end_time',$value_config)) {
			$colspan++;
		}
		if(!in_array('show_hours',$value_config)) {
			$colspan--;
		}
		if($layout == 'position_dropdown') {
			$colspan++;
		}
		if($layout == 'ticket_task') {
			$colspan = $colspan + 2;
		}

		if(in_array($layout,['','multi_line','position_dropdown','ticket_task'])):
			$report_block .= '<table class="table table-bordered" style="width:100%;">
				<tr class="hidden-xs hidden-sm">
					<td colspan="'.COLSPAN.'">Balance Forward Y.T.D.</td>
					'.(in_array($layout,['position_dropdown', 'ticket_task']) ? '<td></td>' : '').'
					'.(in_array('reg_hrs',$value_config) || in_array('payable_hrs',$value_config) ? '<td></td>' : '').'
					'.(in_array('start_day_tile_separate',$value_config) ? '<td></td>' : '').'
					'.(in_array('extra_hrs',$value_config) ? '<td></td>' : '').'
					'.(in_array('relief_hrs',$value_config) ? '<td></td>' : '').'
					'.(in_array('sleep_hrs',$value_config) ? '<td></td>' : '').'
					'.(in_array('training_hrs',$value_config) ? '<td></td>' : '').'
					'.(in_array('sick_hrs',$value_config) ? '<td></td>' : '').'
					'.(in_array('sick_used',$value_config) ? '<td>'.$sick_taken.'</td>' : '').'
					'.(in_array('stat_hrs',$value_config) ? '<td>'.$stat_hours.'</td>' : '').'
					'.(in_array('stat_used',$value_config) ? '<td>'.$stat_taken.'</td>' : '').'
					'.(in_array('vaca_hrs',$value_config) ? '<td>'.$vacation_hours.'</td>' : '').'
					'.(in_array('vaca_used',$value_config) ? '<td>'.$vacation_taken.'</td>' : '').'
					'.(in_array('breaks',$value_config) ? '<td></td>' : '').'
					'.(in_array('view_ticket',$value_config) && $report_format != 'to_array' ? '<td></td>' : '').'
					'.(in_array('comment_box',$value_config) ? '<td></td>' : '').'
				</tr>
				<tr class="hidden-xs hidden-sm">
					<th><div>Date</div></th>
					'.(in_array('schedule',$value_config) ? '<th><div>Schedule</div></th>' : '').'
					'.(in_array('scheduled',$value_config) ? '<th><div>Scheduled Hours</div></th>' : '').'
					'.(in_array('ticketid',$value_config) ? '<th><div>'.TICKET_NOUN.'</div></th>' : '').'
					'.(in_array('show_hours',$value_config) ? '<th><div>Hours</div></th>' : '').'
					'.(in_array('total_tracked_hrs',$value_config) && in_array($layout,['', 'multi_line']) ? '<th><div>Total Tracked<br />Hours</div></th>' : '').'
					'.(in_array('start_time',$value_config) || in_array('start_time_editable',$value_config) ? '<th><div>Start<br />Time</div></th>' : '').'
					'.(in_array('end_time',$value_config) || in_array('end_time_editable',$value_config) ? '<th><div>End<br />Time</div></th>' : '').'
					'.(in_array('planned_hrs',$value_config) ? '<th><div>Planned<br />Hours</div></th>' : '').'
					'.(in_array('tracked_hrs',$value_config) ? '<th><div>Tracked<br />Hours</div></th>' : '').'
					'.(in_array('total_tracked_time',$value_config) ? '<th><div>Total Tracked<br />Time</div></th>' : '').'
					'.(in_array('start_day_tile',$value_config) ? '<th><div>'.$timesheet_start_tile.'</div></th>' : '').'
					'.(in_array($layout,['ticket_task']) ? '<th><div>'.TICKET_NOUN.'</div></th><th><div>Task</div></th>' : '').'
					'.(in_array($layout,['position_dropdown']) ? '<th><div>Position</div></th>' : '').'
					'.(in_array('total_tracked_hrs',$value_config) && in_array($layout,['position_dropdown', 'ticket_task']) ? '<th><div>Total Tracked<br />Hours</div></th>' : '').'
					'.(in_array($layout,['position_dropdown','ticket_task']) ? '<th><div>Hours</div></th>' : '').'
					'.(in_array('reg_hrs',$value_config) || in_array('payable_hrs',$value_config) ? '<th><div>'.(in_array('payable_hrs',$value_config) ? 'Payable' : 'Regular').'<br />Hours</div></th>' : '').'
					'.(in_array('start_day_tile_separate',$value_config) ? '<th><div>'.$timesheet_start_tile.'</div></th>' : '').'
					'.(in_array('extra_hrs',$value_config) ? '<th><div>Extra<br />Hours</div></th>' : '').'
					'.(in_array('relief_hrs',$value_config) ? '<th><div>Relief<br />Hours</div></th>' : '').'
					'.(in_array('sleep_hrs',$value_config) ? '<th><div>Sleep<br />Hours</div></th>' : '').'
					'.(in_array('training_hrs',$value_config) ? '<th><div>Training<br />Hours</div></th>' : '').'
					'.(in_array('sick_hrs',$value_config) ? '<th><div>Sick Time<br />Adjustment</div></th>' : '').'
					'.(in_array('sick_used',$value_config) ? '<th><div>Sick Hrs.<br />Taken</div></th>' : '').'
					'.(in_array('stat_hrs',$value_config) ? '<th><div>Stat<br />Hours</div></th>' : '').'
					'.(in_array('stat_used',$value_config) ? '<th><div>Stat. Hrs.<br />Taken</div></th>' : '').'
					'.(in_array('vaca_hrs',$value_config) ? '<th><div>Vacation<br />Hours</div></th>' : '').'
					'.(in_array('vaca_used',$value_config) ? '<th><div>Vacation<br />Hrs. Taken</div></th>' : '').'
					'.(in_array('breaks',$value_config) ? '<th><div>Breaks</div></th>' : '').'
					'.(in_array('view_ticket',$value_config) && $report_format != 'to_array' ? '<th><div>'.TICKET_NOUN.'</div></th>' : '').'
					'.(in_array('comment_box',$value_config) && $report_format != 'to_array' ? '<th><div>Comments</div></th>' : '').'
				</tr>';

				if ( empty($search_site) ) {
					$sql = "SELECT `time_cards_id`, `date`, SUM(IF(`type_of_time` NOT IN ('Extra Hrs.','Relief Hrs.','Sleep Hrs.','Sick Time Adj.','Sick Hrs.Taken','Stat Hrs.','Stat Hrs.Taken','Vac Hrs.','Vac Hrs.Taken'),`total_hrs`,0)) REG_HRS,
					SUM(IF(`type_of_time`='Extra Hrs.',`total_hrs`,0)) EXTRA_HRS,
					SUM(IF(`type_of_time`='Relief Hrs.',`total_hrs`,0)) RELIEF_HRS, SUM(IF(`type_of_time`='Sleep Hrs.',`total_hrs`,0)) SLEEP_HRS,
					SUM(IF(`type_of_time`='Sick Time Adj.',`total_hrs`,0)) SICK_ADJ, SUM(IF(`type_of_time`='Sick Hrs.Taken',`total_hrs`,0)) SICK_HRS,
					SUM(IF(`type_of_time`='Stat Hrs.',`total_hrs`,0)) STAT_AVAIL, SUM(IF(`type_of_time`='Stat Hrs.Taken',`total_hrs`,0)) STAT_HRS,
					SUM(IF(`type_of_time`='Vac Hrs.',`total_hrs`,0)) VACA_AVAIL, SUM(IF(`type_of_time`='Vac Hrs.Taken',`total_hrs`,0)) VACA_HRS,
					SUM(`highlight`) HIGHLIGHT, SUM(`manager_highlight`) MANAGER,
					GROUP_CONCAT(`comment_box` SEPARATOR ', ') COMMENTS,
					SUM(`timer_tracked`) TRACKED_HRS, SUM(IF(`type_of_time`='Break',`total_hrs`,0)) BREAKS, `type_of_time`, `ticket_attached_id`, `ticketid`, `start_time`, `end_time` FROM `time_cards` WHERE `staff`='$search_staff' AND `date` >= '$search_start_date' AND `date` <= '$search_end_date' AND `deleted`=0 $filter_query $filter_position_query GROUP BY `date`";
				} else {
					$sql = "SELECT `time_cards_id`, `date`, SUM(IF(`type_of_time` NOT IN ('Extra Hrs.','Relief Hrs.','Sleep Hrs.','Sick Time Adj.','Sick Hrs.Taken','Stat Hrs.','Stat Hrs.Taken','Vac Hrs.','Vac Hrs.Taken'),`total_hrs`,0)) REG_HRS,
					SUM(IF(`type_of_time`='Extra Hrs.',`total_hrs`,0)) EXTRA_HRS,
					SUM(IF(`type_of_time`='Relief Hrs.',`total_hrs`,0)) RELIEF_HRS, SUM(IF(`type_of_time`='Sleep Hrs.',`total_hrs`,0)) SLEEP_HRS,
					SUM(IF(`type_of_time`='Sick Time Adj.',`total_hrs`,0)) SICK_ADJ, SUM(IF(`type_of_time`='Sick Hrs.Taken',`total_hrs`,0)) SICK_HRS,
					SUM(IF(`type_of_time`='Stat Hrs.',`total_hrs`,0)) STAT_AVAIL, SUM(IF(`type_of_time`='Stat Hrs.Taken',`total_hrs`,0)) STAT_HRS,
					SUM(IF(`type_of_time`='Vac Hrs.',`total_hrs`,0)) VACA_AVAIL, SUM(IF(`type_of_time`='Vac Hrs.Taken',`total_hrs`,0)) VACA_HRS,
					SUM(`highlight`) HIGHLIGHT, SUM(`manager_highlight`) MANAGER,
					GROUP_CONCAT(`comment_box` SEPARATOR ', ') COMMENTS,
					SUM(`timer_tracked`) TRACKED_HRS, SUM(IF(`type_of_time`='Break',`total_hrs`,0)) BREAKS, `type_of_time`, `ticket_attached_id`, `ticketid`, `start_time`, `end_time` FROM `time_cards` WHERE `staff`='$search_staff' AND `date` >= '$search_start_date' AND `date` <= '$search_end_date' AND IFNULL(`business`,'') LIKE '%$search_site%' AND `deleted`=0 $filter_query $filter_position_query GROUP BY `date`";
				}
				if(in_array($layout,['multi_line','position_dropdown', 'ticket_task'])) {
					$sql .= ", `time_cards_id`";
				}
				$sql .= " ORDER BY `date`, IFNULL(DATE_FORMAT(CONCAT_WS(' ',DATE(NOW()),`start_time`),'%H:%i'),STR_TO_DATE(`start_time`,'%l:%i %p')) ASC, IFNULL(DATE_FORMAT(CONCAT_WS(' ',DATE(NOW()),`end_time`),'%H:%i'),STR_TO_DATE(`end_time`,'%l:%i %p')) ASC";
				$result = mysqli_query($dbc, $sql);
				$date = $search_start_date;
				$row = mysqli_fetch_array($result);
				$total = ['REG'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'TRACKED_HRS'=>0,'BREAKS'=>0,'TRAINING'=>0,'DRIVE'=>0];
			    $date_total = ['HOURS'=>0,'REG'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'BREAKS'=>0,'TRAINING'=>0,'DRIVE'=>0];
				while(strtotime($date) <= strtotime($search_end_date)) {
					$attached_ticketid = 0;
					$timecardid = 0;
					$ticket_attached_id = 0;
					$time_type = '';
					$hl_colour = '';
					$start_time = '';
					$end_time = '';
					if($row['date'] == $date) {
						foreach($hours_types as $hours_type) {
							if($row[$hours_type] > 0) {
								switch($timesheet_rounding) {
									case 'up':
										$row[$hours_type] = ceil($row[$hours_type] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
										break;
									case 'down':
										$row[$hours_type] = floor($row[$hours_type] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
										break;
									case 'nearest':
										$row[$hours_type] = round($row[$hours_type] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
										break;
								}
							}
						}
						$hl_colour = ($row['MANAGER'] > 0 && $mg_highlight != '#000000' && $mg_highlight != '' ? 'background-color:'.$mg_highlight.';' : ($row['HIGHLIGHT'] > 0 && $highlight != '#000000' && $highlight != '' ? 'background-color:'.$highlight.';' : ''));
						$hrs = ['REG'=>$row['REG_HRS'],'EXTRA'=>$row['EXTRA_HRS'],'RELIEF'=>$row['RELIEF_HRS'],'SLEEP'=>$row['SLEEP_HRS'],'SICK_ADJ'=>$row['SICK_ADJ'],
							'SICK'=>$row['SICK_HRS'],'STAT_AVAIL'=>$row['STAT_AVAIL'],'STAT'=>$row['STAT_HRS'],'VACA_AVAIL'=>$row['VACA_AVAIL'],'VACA'=>$row['VACA_HRS'],'TRACKED_HRS'=>$row['TRACKED_HRS'],'BREAKS'=>$row['BREAKS']];
						$comments = html_entity_decode($row['COMMENTS']);
						if(empty(strip_tags($comments))) {
							$comments = $timesheet_comment_placeholder;
						}

						foreach($total as $key => $value) {
							$total[$key] += $hrs[$key];
			                $date_total[$key] += $hrs[$key];
						}
						$timecardid = $row['time_cards_id'];
						$ticket_attached_id = $row['ticket_attached_id'];
						$attached_ticketid = $row['ticketid'];
			            $time_type = $row['type_of_time'];
						$start_time = !empty($row['start_time']) ? date('h:i a', strtotime($row['start_time'])) : '';
						$end_time = !empty($row['end_time']) ? date('h:i a', strtotime($row['end_time'])) : '';

						if(in_array('training_hrs',$value_config) && $timecardid > 0) {
							if(is_training_hrs($dbc, $timecardid)) {
								$hrs['TRAINING'] = $hrs['REG'];
								$hrs['REG'] = 0;
								$total['REG'] -= $hrs['TRAINING'];
								$total['TRAINING'] += $hrs['TRAINING'];
			                    $date_total['REG'] -= $hrs['TRAINING'];
			                    $date_total['TRAINING'] += $hrs['TRAINING'];
							} else {
								$hrs['TRAINING'] = 0;
							}
						} else {
							$hrs['TRAINING'] = 0;
						}
						if(in_array('start_day_tile_separate',$value_config) && !($row['ticketid'] > 0)) {
							$hrs['DRIVE'] = $hrs['REG'];
							$hrs['REG'] = 0;
							$total['REG'] -= $hrs['DRIVE'];
							$total['DRIVE'] += $hrs['DRIVE'];
			                $date_total['REG'] -= $hrs['DRIVE'];
			                $date_total['DRIVE'] += $hrs['DRIVE'];
						} else {
							$hrs['DRIVE'] = 0;
						}

						$row = mysqli_fetch_array($result);
					} else {
			            $date_total = ['HOURS'=>0,'REG'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'BREAKS'=>0,'TRAINING'=>0,'DRIVE'=>0];
						$hrs = ['REG'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'TRACKED_HRS'=>0,'BREAKS'=>0,'TRAINING'=>0,'DRIVE'=>0];
						$comments = '';
					}
					// $hours = mysqli_fetch_array(mysqli_query($dbc, "SELECT IF(`dayoff_type` != '',`dayoff_type`,CONCAT(`starttime`,' - ',`endtime`)) FROM `contacts_shifts` WHERE `deleted`=0 AND `contactid`='$search_staff' AND '$date' BETWEEN `startdate` AND `enddate` ORDER BY `startdate` DESC"))[0];
					$day_of_week = date('l', strtotime($date));
					$shifts = checkShiftIntervals($dbc, $search_staff, $day_of_week, $date, 'all');
					if(!empty($shifts)) {
						$hours = '';
						$hours_off = '';
						foreach ($shifts as $shift) {
							$hours .= $shift['starttime'].' - '.$shift['endtime'].'<br>';
							$hours_off = $shift['dayoff_type'] == '' ? $hours_off : $shift['dayoff_type'];

						}
						$hours = $hours_off == '' ? $hours : $hours_off;
					} else {
						$hours = $schedule_list[date('w',strtotime($date))];
					}
					$mod = '';
					if($date < $last_period) {
						$mod = 'readonly';
					}
					//Planned & Tracked Hours
					$ticket_labels = get_ticket_labels($dbc, $date, $search_staff, $layout, $timecardid, ($report_format == 'to_array' ? 'pdf' : ''));
					$planned_hrs = get_ticket_planned_hrs($dbc, $date, $search_staff, $layout, $timecardid);
					$tracked_hrs = get_ticket_tracked_hrs($dbc, $date, $search_staff, $layout, $timecardid);
					$total_tracked_time = get_ticket_total_tracked_time($dbc, $date, $search_staff, $layout, $timecardid);
					$report_block .= '<tr style="'.$hl_colour.'" bgcolor="'.( date('d', strtotime($date))%2==1 ? 'white' : '#eee' ).'">'.
						'<td data-title="Date">'.$date.'</td>
						'.(in_array('schedule',$value_config) ? '<td data-title="Schedule">'.$hours.'</td>' : '').'
						'.(in_array('scheduled',$value_config) ? '<td data-title="Scheduled Hours"></td>' : '').'
						'.(in_array('ticketid',$value_config) ? '<td data-title="'.TICKET_NOUN.'">'.$ticket_labels.'</td>' : '').'
						'.(in_array('show_hours',$value_config) ? '<td data-title="Hours">'.$hours.'</td>' : '').'
						'.(in_array('total_tracked_hrs',$value_config) && in_array($layout,['', 'multi_line']) ? '<td data-title="Total Tracked Hours">'.(empty($hrs['TRACKED_HRS']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['TRACKED_HRS'],2) : time_decimal2time($hrs['TRACKED_HRS']))).'</td>' : '').'
						'.(in_array('start_time',$value_config) || in_array('start_time_editable',$value_config) ? '<td data-title="Start Time" style="text-align:center">'.$start_time.'</td>' : '').'
						'.(in_array('end_time',$value_config) || in_array('end_time_editable',$value_config) ? '<td data-title="End Time" style="text-align:center">'.$end_time.'</td>' : '').'
						'.(in_array('planned_hrs',$value_config) ? '<td data-title="Planned Hours">'.$planned_hrs.'</td>' : '').'
						'.(in_array('tracked_hrs',$value_config) ? '<td data-title="Tracked Hours">'.$tracked_hrs.'</td>' : '').'
						'.(in_array('total_tracked_time',$value_config) ? '<td data-title="Total Tracked Time">'.$total_tracked_time.'</td>' : '').'
						'.(in_array('start_day_tile',$value_config) ? '<td data-title="'.$timesheet_start_tile.'">'.(empty($hrs['DRIVE']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['DRIVE'],2) : time_decimal2time($hrs['DRIVE']))).'</td>' : '').'
						'.(in_array($layout,['ticket_task']) ? '<td data-title="'.TICKET_NOUN.'">'.get_ticket_label($dbc, mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$row['ticketid']."'"))).'</td><td data-title="Task">'.$row['type_of_time'].'</td>' : '').'
						'.(in_array($layout,['position_dropdown']) ? '<td data-title="Position">'.$row['type_of_time'].'</td>' : '').'
						'.(in_array('total_tracked_hrs',$value_config) && in_array($layout,['position_dropdown', 'ticket_task']) ? '<td data-title="Total Tracked Hours">'.(empty($hrs['TRACKED_HRS']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['TRACKED_HRS'],2) : time_decimal2time($hrs['TRACKED_HRS']))).'</td>' : '').'
						'.(in_array($layout,['position_dropdown', 'ticket_task']) ? '<td data-title="Hours">'.(empty($hrs['REG']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['REG'],2) : time_decimal2time($hrs['REG']))).'</td>' : '').'
						'.(in_array('reg_hrs',$value_config) || in_array('payable_hrs',$value_config) ? '<td data-title="'.(in_array('payable_hrs',$value_config) ? 'Payable' : 'Regular').' Hours">'.(empty($hrs['REG']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['REG'],2) : time_decimal2time($hrs['REG']))).'</td>' : '').'
						'.(in_array('start_day_tile_separate',$value_config) ? '<td data-title="'.$timesheet_start_tile.'">'.(empty($hrs['DRIVE']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['DRIVE'],2) : time_decimal2time($hrs['DRIVE']))).'</td>' : '').'
						'.(in_array('extra_hrs',$value_config) ? '<td data-title="Extra Hours">'.(empty($hrs['EXTRA']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['EXTRA'],2) : time_decimal2time($hrs['EXTRA']))).'</td>' : '').'
						'.(in_array('relief_hrs',$value_config) ? '<td data-title="Relief Hours">'.(empty($hrs['RELIEF']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['RELIEF'],2) : time_decimal2time($hrs['RELIEF']))).'</td>' : '').'
						'.(in_array('sleep_hrs',$value_config) ? '<td data-title="Sleep Hours">'.(empty($hrs['SLEEP']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['SLEEP'],2) : time_decimal2time($hrs['SLEEP']))).'</td>' : '').'
						'.(in_array('training_hrs',$value_config) ? '<td data-title="Training Hours">'.(empty($hrs['TRAINING']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['TRAINING'],2) : time_decimal2time($hrs['TRAINING']))).'</td>' : '').'
						'.(in_array('sick_hrs',$value_config) ? '<td data-title="Sick Time Adjustment">'.(empty($hrs['SICK_ADJ']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['SICK_ADJ'],2) : time_decimal2time($hrs['SICK_ADJ']))).'</td>' : '').'
						'.(in_array('sick_used',$value_config) ? '<td data-title="Sick Hours Taken">'.(empty($hrs['SICK']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['SICK'],2) : time_decimal2time($hrs['SICK']))).'</td>' : '').'
						'.(in_array('stat_hrs',$value_config) ? '<td data-title="Stat Hours">'.(empty($hrs['STAT_AVAIL']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['STAT_AVAIL'],2) : time_decimal2time($hrs['STAT_AVAIL']))).'</td>' : '').'
						'.(in_array('stat_used',$value_config) ? '<td data-title="Stat Hours Taken">'.(empty($hrs['STAT']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['STAT'],2) : time_decimal2time($hrs['STAT']))).'</td>' : '').'
						'.(in_array('vaca_hrs',$value_config) ? '<td data-title="Vacation Hours">'.(empty($hrs['VACA_AVAIL']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['VACA_AVAIL'],2) : time_decimal2time($hrs['VACA_AVAIL']))).'</td>' : '').'
						'.(in_array('vaca_used',$value_config) ? '<td data-title="Vacation Hours Taken">'.(empty($hrs['VACA']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['VACA'],2) : time_decimal2time($hrs['VACA']))).'</td>' : '').'
						'.(in_array('breaks',$value_config) ? '<td data-title="Breaks">'.(empty($hrs['BREAKS']) ? '' : ($timesheet_time_format == 'decimal' ? number_format($hrs['BREAKS'],2) : time_decimal2time($hrs['BREAKS']))).'</td>' : '').'
						'.(in_array('view_ticket',$value_config) && $report_format != 'to_array' ? '<td data-title="'.TICKET_NOUN.'" style="text-align:center">'.(!empty($attached_ticketid) ? '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Ticket/edit_tickets.php?edit='.$attached_ticketid.'&calendar_view=true\',\'auto\',false,true, $(\'#timesheet_div\').outerHeight()); return false;" data-ticketid="'.$attached_ticketid.'" class="view_ticket" '.($attached_ticketid > 0 ? '' : 'style="display:none;"').'>View</a>' : '').'</td>' : '').'
						'.(in_array('comment_box',$value_config) ? '<td data-title="Comments"><span>'.$comments.'</span></td>' : '').
					'</tr>';
					if(in_array('total_per_day',$value_config) && $date != $row['date']) {
						$report_block .= '<tr style="font-weight: bold;" bgcolor="'.( date('d', strtotime($date))%2==1 ? 'white' : '#eee' ).'">
							<td data-title="" colspan="'.$colspan.'">Day Totals</td>
							'.(in_array('total_tracked_hrs',$value_config) ? '<td data-title="Total Tracked Hours">'.($timesheet_time_format == 'decimal' ? number_format($date_total['TRACKED_HRS'],2) : time_decimal2time($date_total['TRACKED_HRS'])).'</td>' : '').'
							'.(in_array('reg_hrs',$value_config) || in_array('payable_hrs',$value_config) ? '<td data-title="'.(in_array('payable_hrs',$value_config) ? 'Payable' : 'Regular').' Hours">'.($timesheet_time_format == 'decimal' ? number_format($date_total['REG'],2) : time_decimal2time($date_total['REG'])).'</td>' : '').'
							'.(in_array('start_day_tile_separate',$value_config) ? '<td data-title="'.$timesheet_start_tile.'">'.($timesheet_time_format == 'decimal' ? number_format($date_total['DRIVE'],2) : time_decimal2time($date_total['DRIVE'])).'</td>' : '').'
							'.(in_array('extra_hrs',$value_config) ? '<td data-title="Extra Hours">'.($timesheet_time_format == 'decimal' ? number_format($date_total['EXTRA'],2) : time_decimal2time($date_total['EXTRA'])).'</td>' : '').'
							'.(in_array('relief_hrs',$value_config) ? '<td data-title="Relief Hours">'.($timesheet_time_format == 'decimal' ? number_format($date_total['RELIEF'],2) : time_decimal2time($date_total['RELIEF'])).'</td>' : '').'
							'.(in_array('sleep_hrs',$value_config) ? '<td data-title="Sleep Hours">'.($timesheet_time_format == 'decimal' ? number_format($date_total['SLEEP'],2) : time_decimal2time($date_total['SLEEP'])).'</td>' : '').'
							'.(in_array('training_hrs',$value_config) ? '<td data-title="Training Hours">'.($timesheet_time_format == 'decimal' ? number_format($date_total['TRAINING'],2) : time_decimal2time($date_total['TRAINING'])).'</td>' : '').'
							'.(in_array('sick_hrs',$value_config) ? '<td data-title="Sick Time Adjustment">'.($timesheet_time_format == 'decimal' ? number_format($date_total['SICK_ADJ'],2) : time_decimal2time($date_total['SICK_ADJ'])).'</td>' : '').'
							'.(in_array('sick_used',$value_config) ? '<td data-title="Sick Hours Taken">'.($timesheet_time_format == 'decimal' ? number_format($date_total['SICK'],2) : time_decimal2time($date_total['SICK'])).'</td>' : '').'
							'.(in_array('stat_hrs',$value_config) ? '<td data-title="Stat Hours">'.($timesheet_time_format == 'decimal' ? number_format($date_total['STAT_AVAIL'],2) : time_decimal2time($date_total['STAT_AVAIL'])).'</td>' : '').'
							'.(in_array('stat_used',$value_config) ? '<td data-title="Stat Hours Taken">'.($timesheet_time_format == 'decimal' ? number_format($date_total['STAT'],2) : time_decimal2time($date_total['STAT'])).'</td>' : '').'
							'.(in_array('vaca_hrs',$value_config) ? '<td data-title="Vacation Hours">'.($timesheet_time_format == 'decimal' ? number_format($date_total['VACA_AVAIL'],2) : time_decimal2time($date_total['VACA_AVAIL'])).'</td>' : '').'
							'.(in_array('vaca_used',$value_config) ? '<td data-title="Vacation Hours Taken">'.($timesheet_time_format == 'decimal' ? number_format($date_total['VACA'],2) : time_decimal2time($date_total['VACA'])).'</td>' : '').'
							'.(in_array('breaks',$value_config) ? '<td data-title="Breaks">'.($timesheet_time_format == 'decimal' ? number_format($date_total['BREAKS'],2) : time_decimal2time($date_total['BREAKS'])).'</td>' : '').'
							'.(in_array('view_ticket',$value_config) ? '<td data-title=""></td>' : '').'
							<td data-title=""></td>
						</tr>';
					}
					if(!in_array($layout,['multi_line','position_dropdown', 'ticket_task']) || $date != $row['date']) {
						$date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
			            $date_total = ['HOURS'=>0,'REG'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'BREAKS'=>0,'TRAINING'=>0,'DRIVE'=>0];
					}
				}
				$report_block .= '<tr>
					<td data-title="" colspan="'.COLSPAN.'">Totals</td>
					'.(in_array($layout,['position_dropdown', 'ticket_task']) ? '<td data-title="Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['TRACKED_HRS'],2) : time_decimal2time($total['REG'])).'</td>' : '').'
					'.(in_array('reg_hrs',$value_config) || in_array('payable_hrs',$value_config) ? '<td data-title="'.(in_array('payable_hrs',$value_config) ? 'Payable' : 'Regular').' Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['REG'],2) : time_decimal2time($total['REG'])).'</td>' : '').'
					'.(in_array('start_day_tile_separate',$value_config) ? '<td data-title="'.$timesheet_start_tile.'">'.($timesheet_time_format == 'decimal' ? number_format($total['DRIVE'],2) : time_decimal2time($total['DRIVE'])).'</td>' : '').'
					'.(in_array('extra_hrs',$value_config) ? '<td data-title="Extra Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['EXTRA'],2) : time_decimal2time($total['EXTRA'])).'</td>' : '').'
					'.(in_array('relief_hrs',$value_config) ? '<td data-title="Relief Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['RELIEF'],2) : time_decimal2time($total['RELIEF'])).'</td>' : '').'
					'.(in_array('sleep_hrs',$value_config) ? '<td data-title="Sleep Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['SLEEP'],2) : time_decimal2time($total['SLEEP'])).'</td>' : '').'
					'.(in_array('training_hrs',$value_config) ? '<td data-title="Training Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['TRAINING'],2) : time_decimal2time($total['TRAINING'])).'</td>' : '').'
					'.(in_array('sick_hrs',$value_config) ? '<td data-title="Sick Time Adjustment">'.($timesheet_time_format == 'decimal' ? number_format($total['SICK_ADJ'],2) : time_decimal2time($total['SICK_ADJ'])).'</td>' : '').'
					'.(in_array('sick_used',$value_config) ? '<td data-title="Sick Hours Taken">'.($timesheet_time_format == 'decimal' ? number_format($total['SICK'],2) : time_decimal2time($total['SICK'])).'</td>' : '').'
					'.(in_array('stat_hrs',$value_config) ? '<td data-title="Stat Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['STAT_AVAIL'],2) : time_decimal2time($total['STAT_AVAIL'])).'</td>' : '').'
					'.(in_array('stat_used',$value_config) ? '<td data-title="Stat Hours Taken">'.($timesheet_time_format == 'decimal' ? number_format($total['STAT'],2) : time_decimal2time($total['STAT'])).'</td>' : '').'
					'.(in_array('vaca_hrs',$value_config) ? '<td data-title="Vacation Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['VACA_AVAIL'],2) : time_decimal2time($total['VACA_AVAIL'])).'</td>' : '').'
					'.(in_array('vaca_used',$value_config) ? '<td data-title="Vacation Hours Taken">'.($timesheet_time_format == 'decimal' ? number_format($total['VACA'],2) : time_decimal2time($total['VACA'])).'</td>' : '').'
					'.(in_array('breaks',$value_config) ? '<td data-title="Breaks">'.($timesheet_time_format == 'decimal' ? number_format($total['BREAKS'],2) : time_decimal2time($total['BREAKS'])).'</td>' : '').'
					'.(in_array('view_ticket',$value_config) ? '<td data-title=""></td>' : '').'
					'.(in_array('comment_box',$value_config) ? '<td data-title=""></td>' : '').'
				</tr>';
				$report_block .= '<tr>
					<td colspan="'.COLSPAN.'">Year-to-date Totals</td>
					'.(in_array($layout,['position_dropdown', 'ticket_task']) ? '<td data-title=""></td>' : '').'
					'.(in_array('reg_hrs',$value_config) || in_array('payable_hrs',$value_config) ? '<td data-title=""></td>' : '').'
					'.(in_array('start_day_tile_separate',$value_config) ? '<td data-title=""></td>' : '').'
					'.(in_array('extra_hrs',$value_config) ? '<td data-title=""></td>' : '').'
					'.(in_array('relief_hrs',$value_config) ? '<td data-title=""></td>' : '').'
					'.(in_array('sleep_hrs',$value_config) ? '<td data-title=""></td>' : '').'
					'.(in_array('training_hrs',$value_config) ? '<td data-title=""></td>' : '').'
					'.(in_array('sick_hrs',$value_config) ? '<td data-title=""></td>' : '').'
					'.(in_array('sick_hrs',$value_config) ? '<td data-title="Sick Hours Taken">'.($timesheet_time_format == 'decimal' ? number_format($total['SICK']+$sick_taken,2) : time_decimal2time($total['SICK']+$sick_taken)).'</td>' : '').'
					'.(in_array('stat_hrs',$value_config) ? '<td data-title="Stat Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['STAT_AVAIL']+$stat_hours,2) : time_decimal2time($total['STAT_AVAIL']+$stat_hours)).'</td>' : '').'
					'.(in_array('stat_used',$value_config) ? '<td data-title="Stat Hours Taken">'.($timesheet_time_format == 'decimal' ? number_format($total['STAT']+$stat_taken,2) : time_decimal2time($total['STAT']+$stat_taken)).'</td>' : '').'
					'.(in_array('vaca_hrs',$value_config) ? '<td data-title="Vacation Hours">'.($timesheet_time_format == 'decimal' ? number_format($total['VACA_AVAIL']+$vacation_hours,2) : time_decimal2time($total['VACA_AVAIL']+$vacation_hours)).'</td>' : '').'
					'.(in_array('vaca_used',$value_config) ? '<td data-title="Vacation Hours Taken">'.($timesheet_time_format == 'decimal' ? number_format($total['VACA']+$vacation_taken,2) : time_decimal2time($total['VACA']+$vacation_taken)).'</td>' : '').'
					'.(in_array('breaks',$value_config) ? '<td data-title=""></td>' : '').'
					'.(in_array('view_ticket',$value_config) ? '<td data-title=""></td>' : '').'
					'.(in_array('comment_box',$value_config) ? '<td data-title=""></td>' : '').'
				</tr>
			</table>';
		elseif($layout == 'table_add_button'):
			$report_block .= '<table class="table table-bordered">
					<tr class="hidden-sm hidden-xs">
						<th>Date</th>
						<th>Staff</th>
						<th>Hours</th>
						<th>Type</th>
					</tr>';
					$time_cards = mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `staff`='$search_staff' AND `date` >= '$search_start_date' AND `date` <= '$search_end_date' AND `business` LIKE '%$search_site%' AND `deleted`=0 $filter_query $filter_position_query");
					while($time_card = mysqli_fetch_assoc($time_cards)) {
						$report_block .= '<tr class="hidden-sm hidden-xs">
							<td data-title="Date">'.$time_card['date'].'</td>
							<td data-title="Staff">'. get_contact($dbc, $time_card['staff']).'</td>
							<td data-title="Hours">'.$time_card['total_hours'].'</td>
							<td data-title="Type">'.$time_card['type_of_time'].'</td>
						</tr>';
					}
				$report_block .= '</table>';
		elseif($layout == 'rate_card' || $layout == 'rate_card_tickets'):/*
			$desc_inc = 0;
			for($date = $search_start_date; strtotime($date) <= strtotime($search_end_date); $date = date("Y-m-d", strtotime("+1 day", strtotime($date)))) {
				if($layout == 'rate_card_tickets') {
					$ticket_sql = "SELECT `tickets`.*, `osbn`.`item_id` `osbn` FROM `tickets` LEFT JOIN `ticket_attached` `osbn` ON `tickets`.`ticketid`=`osbn`.`ticketid` AND `osbn`.`src_table`='Staff' AND `osbn`.`deleted`=0 AND `osbn`.`position`='Team Lead' WHERE `tickets`.`ticketid` IN (SELECT `ticketid` FROM `time_cards` WHERE `deleted`=0 AND `staff`='$search_staff' AND `date`='$date' UNION SELECT `ticketid` FROM `tickets` WHERE CONCAT(',',`contactid`,',') LIKE '%,$search_staff,%' AND (`to_do_date`='$date' OR '$date' BETWEEN `to_do_date` AND `to_do_end_date` OR `internal_qa_date`='$date' OR `deliverable_date`='$date') AND `deleted`=0)";
				} else {
					$ticket_sql = "SELECT 0 `ticketid`";
				}
				$ticket_query = mysqli_query($dbc, $ticket_sql);
				$ticket = mysqli_fetch_assoc($ticket_query);
				do {
					$daily_total = 0;
					$cat_total = 0;
					$work_hours_sql = "SELECT IFNULL(SUM(`total_hrs`),0) hours, `category`, `work_desc`, `hourly`, `daily`, `color_code`, `location`, `customer`, `day`, `travel_range_1`, `travel_range_5`, `travel_range_1_5`, `comment_box` FROM `staff_rate_table` staff LEFT JOIN `time_cards` sheet ON CONCAT(',',staff.`staff_id`,',') LIKE CONCAT('%,',sheet.`staff`,',%') AND sheet.`type_of_time`=staff.`work_desc` AND sheet.`date`='$date' AND sheet.`deleted`=0 WHERE CONCAT(',',staff.`staff_id`,',') LIKE '%,$search_staff,%' AND staff.`deleted`=0 GROUP BY `category`, `work_desc` ORDER BY `category`, `sort_order`, `work_desc`, `hourly`";
					$work_result = mysqli_query($dbc, $work_hours_sql);
					$location = mysqli_fetch_array($work_result)['location'];
					$customer = mysqli_fetch_array($work_result)['customer'];
					$work_result = mysqli_query($dbc, $work_hours_sql);
					$day_of_week = date('l', strtotime($date));
					$shifts = checkShiftIntervals($dbc, $search_staff, $day_of_week, $date);
					if(!empty($shifts)) {
						$shift = '';
						$hours_off = '';
						foreach ($shifts as $shift_detail) {
							$shift .= $shift_detail['starttime'].' - '.$shift_detail['endtime'].'<br>';
							$hours_off = $shift['dayoff_type'] == '' ? $hours_off : $shift['dayoff_type'];

						}
						$shift = $hours_off == '' ? $shift : $hours_off;
					} else {
						$shift = $schedule_list[date('w',strtotime($date))];
					}
					$report_block .= "<div class='form-group' style='border:solid black 1px; display:inline-block; margin:1em; width:30em;'>";
					$report_block .= "<div style='border:solid black 1px; padding:0.25em; width: 30em;'><div style='display:inline-block; width:12em;'>Date:</div><div style='display:inline-block; width:16em;'>$date</div>";
					if($shift != '') {
						$report_block .= "<div style='display:inline-block; width:12em;'>Hours:</div><div style='display:inline-block; width:16em;'>$shift</div>";
					}
					if($ticket['ticketid'] > 0) {
						$report_block .= "<div style='display:inline-block; width:12em;'>".TICKET_NOUN.":</div><div style='display:inline-block; width:16em;'>".get_ticket_label($dbc, $ticket).($ticket['osbn'] > 0 ? "<br />OSBN: ".get_contact($dbc, $ticket['osbn']) : '')."</div>";
					}
					$report_block .= "<div style='display:inline-block; width:11.7em;'>Customer:</div>";
					?>
					<div style='display:inline-block; width:16em;'>
						<input type='hidden' name='customer_date[]' value='<?php echo $date; ?>'>
						<select data-placeholder="Choose a Customer..." name="customer[]" class="chosen-select-deselect form-control">
							<option value=""></option>
							<?php
								$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name FROM contacts WHERE category='Business' AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
								foreach($query as $id) {
									$selected = '';
									$selected = $id == $customer ? 'selected = "selected"' : '';
									echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id,'name').'</option>';
								}
							?>
						</select>
					</div>
					<?php
					echo "<div style='display:inline-block; width:12em;'>Location:</div><div style='display:inline-block; width:16em;'><input type='hidden' name='location_date[]' value='$date'><input type='text' name='location[]' class='form-control'  value='$location'></div></div>";
					$category = '';
					while($hours = mysqli_fetch_array($work_result)) {
						if($hours['category'] != $category) {
							if($category != '') {
								echo "<div style='display:inline-block; width:12em;'>$category Total</div><div style='display:inline-block; text-align:right; width:16em;' class='cat-total'>$".number_format($cat_total,2)."</div>";
								echo "<div style='display:inline-block;  vertical-align:top; width:12em;'><img class='inline-img smaller' data-target='#desc_".$desc_inc."' onclick='expandArea(this);' src='../img/icons/ROOK-edit-icon.png'>$category Description</div><input type='hidden' name='comment_date[]' value='$date'><input type='hidden' name='comment_cat[]' value='$category'><input type='hidden' name='cat_comment[]' id='desc_".$desc_inc."' value='".$comment_box."'><div class='comment_box_text' style='display:inline-block; width:16em;'>".$comment_box;
								echo "<p style='display:none;'><a class='pull-right' href='' data-target='#desc_".$desc_inc."' onclick='expandArea(this)'>Edit Description</a></p></div></div>";
								$desc_inc++;
							}
							$category = $hours['category'];
							$cat_total = 0;
							echo "<div style='border:solid black 1px; padding:0.25em; width: 30em;' class='category-block'><div style='display:inline-block; width:12em;'>$category</div>
							<div style='display:inline-block; text-align:center; width:4em;'>Day</div><div style='display:inline-block; text-align:center; width:4em;'>Hours</div><div style='display:inline-block; text-align:center; width:4em;'>Rate</div><div style='display:inline-block; text-align:center; width:4em;'>Total</div>";
						}
						echo "<div style='background-color:".$hours['color_code'].";'>";
						echo "<div style='display:inline-block; width:12em;'>".$hours['work_desc']."</div><div style='display:inline-block; width:4em;'>";
						if ($hours['daily'] > 0) {
							$checked = '';
							if ($hours['day'] == 1) {
								$checked = 'checked';
							}
							echo "<input type='hidden' name='day_cat[]' value='$category'><input type='hidden' name='day_type[]' value='".$hours['work_desc']."'><input type='hidden' name='day_date[]' value='$date'><input type='hidden' name='day[]' value='1'>";
							echo "<input type='checkbox' data-rate='".$hours['daily']."' ".$checked." style='margin-left: 2em;' name='day_checkbox[]' value='1'></div>";
							echo "<div style='display:inline-block; text-align:right; width:4em;'><input type='text' name='' data-rate='".$hours['hourly']."' class='form-control' disabled value=''></div>";
							echo "<div style='display:inline-block; text-align:right; width:4em;' class='row-rate'>$".$hours['daily']."</div>";
							echo "<div style='display:inline-block; text-align:right; width:4em;' class='row-total'>$";
							if ($checked == 'checked') {
								echo $hours['daily'];
							} else {
								echo '0.00';
							}
							echo "</div></div>";
							if ($checked != '') {
								$cat_total += $hours['daily'];
								$daily_total += $hours['daily'];
							}
						} else {
							echo "<input type='hidden' name='hours_cat[]' value='$category'><input type='hidden' name='hours_type[]' value='".$hours['work_desc']."'><input type='hidden' name='hours_date[]' value='$date'>";
							echo "</div>";
							$hourly_rate = $hours['hourly'];
							if ($hours['travel_range_1'] > 0 || $hours['travel_range_5'] > 0 || $hours['travel_range_1_5'] > 0) {
								if ($hours['hours'] >= 5) {
									$hourly_rate = $hours['travel_range_5'];
								} else if ($hours['hours'] < 5 && $hours['hours'] >= 1) {
									$hourly_rate = $hours['travel_range_1_5'];
								} else {
									$hourly_rate = $hours['travel_range_1'];
								}
								echo "<div style='display:inline-block; text-align:right; width:4em;'><input type='text' ".($security['edit'] > 0 ? '' : 'readonly')." name='hours[]' data-rate='".$hourly_rate."' data-rate-travel1='".$hours['travel_range_1']."' data-rate-travel5='".$hours['travel_range_5']."' data-rate-travel15='".$hours['travel_range_1_5']."' class='form-control' value='".$hours['hours']."'></div>";
							} else {
								echo "<div style='display:inline-block; text-align:right; width:4em;'><input type='text' ".($security['edit'] > 0 ? '' : 'readonly')." name='hours[]' data-rate='".$hourly_rate."' class='form-control' value='".$hours['hours']."'></div>";
							}
							echo "<div style='display:inline-block; text-align:right; width:4em;' class='row-rate'>$".$hourly_rate."</div>";
							echo "<div style='display:inline-block; text-align:right; width:4em;' class='row-total'>$".number_format($hourly_rate * $hours['hours'],2)."</div>";
							if($hours['comment_box'] != '' && in_array(['Comments','text','comment_box'],$config['settings']['Choose Fields for Time Sheets']['data']['General'])) {
								echo html_entity_decode($hours['comment_box']);
							}
							echo "</div>";
							$cat_total += $hours['hours'] * $hourly_rate;
							$daily_total += $hours['hours'] * $hourly_rate;
						}
						$comment_box = $hours['comment_box'];
					}
					if($category != '') {
						echo "<div style='display:inline-block; width:12em;'>$category Total</div><div style='display:inline-block; text-align:right; width:16em;' class='cat-total'>$".number_format($cat_total,2)."</div>";
						echo "<div style='display:inline-block; vertical-align:top; width:12em;'>$category Description</div><input type='hidden' name='comment_date[]' value='$date'><input type='hidden' name='comment_cat[]' value='$category'><input type='hidden' name='cat_comment[]' id='desc_".$desc_inc."' value='".$comment_box."'><div class='comment_box_text' style='display:inline-block; width:16em;'>".$comment_box;
						echo "<p><a class='pull-right' href='#desc_".$desc_inc."' onclick='expandArea(this)'>Edit Description</a></p></div></div>";
						$desc_inc++;
					}
					echo "<div style='border:solid black 1px; padding:0.25em; width:30em;'><div style='display:inline-block; width:12em;'>Daily Total</div><div style='display:inline-block; text-align:right; width:16em;' class='day-total'>$".number_format($daily_total,2)."</div></div></div>";
				} while($ticket = mysqli_fetch_assoc($ticket_query));
			}*/
		endif;
		$report_blocks[] = $report_block;
		$report_block = '';
	}
	if(in_array('staff_combine',$value_config)) {
		foreach($report_blocks as $i => $report) {
			$key = array_search($report,array_slice($report_blocks,$i+1));
			while($key !== FALSE) {
				$report_name[$i+$key+1] .= ', '.$report_name[$i];
				unset($report_blocks[$i]);
				unset($report_name[$i]);
				$key = array_search($report,array_slice($report_blocks,$i+1));
			}
		}
	}
	foreach($report_blocks as $i => &$report) {
		$report = '<h3>'.$report_name[$i].'</h3>'.$report;
	}
	if($report_format == 'to_array') {
		return $report_blocks;
	}
	return implode('',$report_blocks);
} ?>

<?php function get_egs_hours_report($dbc, $see_staff, $search_start_date, $search_end_date,$staff,$report_format, $tab, $override_value_config = '', $override_timesheet_payroll_fields = '') {
	global $config;
	$value_config = explode(',',get_field_config($dbc, 'time_cards_total_hrs_layout'));
	if(empty(array_filter($value_config))) {
		$value_config = ['reg_hrs','overtime_hrs','doubletime_hrs'];
	}
	if(!empty($override_value_config)) {
		$value_config = explode(',',$override_value_config);
	}
	$layout = get_config($dbc, 'timesheet_layout');
	$timesheet_payroll_fields = '';
	$timesheet_start_tile = get_config($dbc, 'timesheet_start_tile');
	$timesheet_time_format = get_config($dbc, 'timesheet_time_format');
	$timesheet_payroll_layout = get_config($dbc, 'timesheet_payroll_layout');
	$timesheet_payroll_overtime = get_config($dbc, 'timesheet_payroll_overtime');
	$timesheet_payroll_doubletime = get_config($dbc, 'timesheet_payroll_doubletime');
	$timesheet_rounding = get_config($_SERVER['DBC'], 'timesheet_rounding');
	$timesheet_rounded_increment = get_config($_SERVER['DBC'], 'timesheet_rounded_increment') / 60;
	if($tab == 'payroll' && $report_format != 'to_array') {
		$total_columns = 3;
	} else {
		$total_columns = 2;
	}
	if($tab == 'payroll') {
		$timesheet_payroll_fields = ','.get_config($dbc, 'timesheet_payroll_fields').',';
		if(!empty($override_timesheet_payroll_fields)) {
			$timesheet_payroll_layout = ','.$override_timesheet_payroll_fields.',';
		}
		$total_columns += count(array_filter(array_unique(explode(',',$timesheet_payroll_fields))));
	}
	$total_columns += count(array_diff(array_filter(array_unique($value_config)),['staff_combine','total_per_day']));
	$col_width = 100 / $total_columns;

    $pass_var = '';
    foreach (array_unique(array_filter($see_staff)) as $search_staff_pass) {
        $pass_var .= 'search_staff%5B%5D='.$search_staff_pass.'&';
    }

    if($report_format != 'to_array') {
        echo '<a href="payroll.php?'.$pass_var.'search_start_date='.$search_start_date.'&search_end_date='.$search_end_date.'&search_user_submit=Search" name="display_all_inventory" class="btn brand-btn mobile-block pull-right">Go Back</a>';
    }

    if($staff == '') {
  		return '<h4>Please select a staff member.</h4>';
    } else {
        $staff_list = [];
        if(!is_array($staff)) {
        	$staff = explode(',',$staff);
        }
        foreach ($staff as $search_staff) {
            if($search_staff > 0) {
                $staff_list[] = ['contactid'=>$search_staff,'first_name'=>'','last_name'=>get_contact($dbc, $search_staff)];
            }
        }
    }

    $report = '';
	$report_blocks = [];
	$report_name = [];
	foreach($staff_list as $staff) {
        $search_staff = $staff['contactid'];

		$report_name[] = $staff['first_name'].' '.$staff['last_name'];

		$start_of_year = date('Y-01-01', strtotime($search_start_date));
        $total_colspan = 2;
        $report .= '<table cellpadding="3" border="0" class="table table-bordered" style="text-align:left; border:1px solid #ddd;">
                <tr class="hidden-xs hidden-sm">
                    <th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Date</div></th>'.
                    (in_array('view_ticket',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>'.TICKET_NOUN.'</div></th>' : '').
	                (strpos($timesheet_payroll_fields, ',Expenses Owed,') !== FALSE ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Expenses Owed</div></th>' : '').
	                (strpos($timesheet_payroll_fields, ',Mileage,') !== FALSE ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Mileage</div></th>' : '').
	                (strpos($timesheet_payroll_fields, ',Mileage Rate,') !== FALSE ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Mileage Rate</div></th>' : '').
	                (strpos($timesheet_payroll_fields, ',Mileage Total,') !== FALSE ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Mileage Total</div></th>' : '').
                    (in_array('start_time',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Start Time</div></th>' : '').
                    (in_array('end_time',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>End Time</div></th>' : '').
                    (in_array('planned_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Planned Hours</div></th>' : '').
                    (in_array('tracked_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Tracked Hours</div></th>' : '').
                    (in_array('total_tracked_time',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total Tracked Time</div></th>' : '').
                    (in_array('total_tracked_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Tracked Hours</div></th>' : '').
                    (in_array('reg_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Reg. Time</div></th>' : '').
                    (in_array('start_day_tile_separate',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>'.$timesheet_start_tile.'</div></th>' : '').
                    (in_array('payable_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Payable Hours</div></th>' : '').
                    (in_array('overtime_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Over Time</div></th>' : '').
                    (in_array('doubletime_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Double Time</div></th>' : '').
                    (in_array('extra_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Extra Hours</div></th>' : '').
                    (in_array('relief_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Relief Hours</div></th>' : '').
                    (in_array('sleep_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Sleep Hours</div></th>' : '').
                    (in_array('training_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Training Hours</div></th>' : '').
                    (in_array('sick_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Sick Time Adjustment</div></th>' : '').
                    (in_array('sick_used',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Sick Hours Taken</div></th>' : '').
                    (in_array('stat_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Stat Hours</div></th>' : '').
                    (in_array('stat_used',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Stat Hours Taken</div></th>' : '').
                    (in_array('vaca_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Vacation Hours</div></th>' : '').
                    (in_array('vaca_used',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Vacation Hours Taken</div></th>' : '').
                    (in_array('breaks',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Breaks</div></th>' : '').
                    '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total</div></th>
                    '.($tab == 'payroll' && $report_format != 'to_array' ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Unapprove</div></th>' : '').'
                </tr>';
                $grand_total = 0;
                $mileage_total = 0;
                $mileage_rate_total = 0;
                $mileage_cost_total = 0;
                $limits = "AND `staff`='$search_staff'";
                if($search_site > 0) {
                    $limits .= " AND `business` LIKE '%$search_site%'";
                }
                if($tab == 'payroll') {
                	$limits .= " AND (`approv` = 'Y' OR `approv` = 'P')";
                }
                // $result = get_time_sheet($search_start_date, $search_end_date, $limits, ', `staff`, `date`, `time_cards_id`');
                $sql = "SELECT `time_cards_id`, `date`, SUM(IF(`type_of_time` NOT IN ('Extra Hrs.','Relief Hrs.','Sleep Hrs.','Sick Time Adj.','Sick Hrs.Taken','Stat Hrs.','Stat Hrs.Taken','Vac Hrs.','Vac Hrs.Taken','Break'),`total_hrs`,0)) REG_HRS,
			        SUM(IF(`type_of_time`='Extra Hrs.',`total_hrs`,0)) EXTRA_HRS,
			        SUM(IF(`type_of_time`='Relief Hrs.',`total_hrs`,0)) RELIEF_HRS, SUM(IF(`type_of_time`='Sleep Hrs.',`total_hrs`,0)) SLEEP_HRS,
			        SUM(IF(`type_of_time`='Sick Time Adj.',`total_hrs`,0)) SICK_ADJ, SUM(IF(`type_of_time`='Sick Hrs.Taken',`total_hrs`,0)) SICK_HRS,
			        SUM(IF(`type_of_time`='Stat Hrs.',`total_hrs`,0)) STAT_AVAIL, SUM(IF(`type_of_time`='Stat Hrs.Taken',`total_hrs`,0)) STAT_HRS,
			        SUM(IF(`type_of_time`='Vac Hrs.',`total_hrs`,0)) VACA_AVAIL, SUM(IF(`type_of_time`='Vac Hrs.Taken',`total_hrs`,0)) VACA_HRS,
			        SUM(`highlight`) HIGHLIGHT, SUM(`manager_highlight`) MANAGER,
			        GROUP_CONCAT(DISTINCT `comment_box` SEPARATOR ', ') COMMENTS, SUM(`timer_tracked`) TRACKED_HRS, SUM(IF(`type_of_time`='Break',`total_hrs`,0)) BREAKS, `type_of_time`, `ticket_attached_id`, `ticketid`, `start_time`, `end_time`, `approv`, `coord_approvals`, `manager_approvals`, `manager_name`, `coordinator_name`
			        FROM `time_cards` WHERE `date` >= '$search_start_date' AND `date` <= '$search_end_date' $limits AND `deleted`=0 GROUP BY `time_cards_id` ORDER BY `date`, IFNULL(DATE_FORMAT(CONCAT_WS(' ',DATE(NOW()),`start_time`),'%H:%i'),STR_TO_DATE(`start_time`,'%l:%i %p')) ASC, IFNULL(DATE_FORMAT(CONCAT_WS(' ',DATE(NOW()),`end_time`),'%H:%i'),STR_TO_DATE(`end_time`,'%l:%i %p')) ASC";
		        $result = mysqli_fetch_all(mysqli_query($dbc, $sql),MYSQLI_ASSOC);
                $date = $search_start_date;
                $i = 0;
				$bgcolor = 'white';
			    $date_total = ['ROW_HRS'=>0,'OVERTIME'=>0,'DOUBLETIME'=>0,'REG'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'BREAKS'=>0,'TRAINING'=>0,'DRIVE'=>0,'TRACKED_HRS'=>0];
			    $total = ['ROW_HRS'=>0,'OVERTIME'=>0,'DOUBLETIME'=>0,'REG'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'BREAKS'=>0,'TRAINING'=>0,'DRIVE'=>0,'TRACKED_HRS'=>0];
                while(strtotime($date) <= strtotime($search_end_date)) {
                	$mileage = 0;
                	$mileage_rate = 0;
                	$mileage_cost = 0;
                    if($result[$i]['date'] == $date) {
                        $row = $result[$i++];
			            $hrs = ['ROW_HRS'=>0,'OVERTIME'=>0,'DOUBLETIME'=>0,'REG'=>$row['REG_HRS'],'EXTRA'=>$row['EXTRA_HRS'],'RELIEF'=>$row['RELIEF_HRS'],'SLEEP'=>$row['SLEEP_HRS'],'SICK_ADJ'=>$row['SICK_ADJ'],'SICK'=>$row['SICK_HRS'],'STAT_AVAIL'=>$row['STAT_AVAIL'],'STAT'=>$row['STAT_HRS'],'VACA_AVAIL'=>$row['VACA_AVAIL'],'VACA'=>$row['VACA_HRS'],'BREAKS'=>$row['BREAKS'],'TRACKED_HRS'=>$row['TRACKED_HRS']];
			            foreach($hrs as $key => $hr) {
			            	if($hr > 0) {
			                    switch($timesheet_rounding) {
			                        case 'up':
			                            $hrs[$key] = ceil($hrs[$key] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
			                            break;
			                        case 'down':
			                            $hrs[$key] = floor($hrs[$key] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
			                            break;
			                        case 'nearest':
			                            $hrs[$key] = round($hrs[$key] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
			                            break;
			                    }
				            	$total[$key] += $hrs[$key];
				            	$date_total[$key] += $hrs[$key];
				            	if($key != 'TRACKED_HRS') {
					            	$hrs['ROW_HRS'] += $hrs[$key];
					            	$date_total['ROW_HRS'] += $hrs[$key];
					            	$grand_total += $hrs[$key];
					            }
				            }
			            }

			            if(in_array('training_hrs',$value_config) && $row['time_cards_id'] > 0) {
			                if(is_training_hrs($dbc, $row['time_cards_id'])) {
			                    $hrs['TRAINING'] = $hrs['REG'];
			                    $hrs['REG'] = 0;
			                    $total['REG'] -= $hrs['TRAINING'];
			                    $total['TRAINING'] += $hrs['TRAINING'];
			                    $date_total['REG'] -= $hrs['TRAINING'];
			                    $date_total['TRAINING'] += $hrs['TRAINING'];
			                } else {
			                    $hrs['TRAINING'] = 0;
			                }
			            } else {
			                $hrs['TRAINING'] = 0;
			            }
			            if(in_array('start_day_tile_separate',$value_config) && !($row['ticketid'] > 0)) {
			                $hrs['DRIVE'] = $hrs['REG'];
			                $hrs['REG'] = 0;
			                $total['REG'] -= $hrs['DRIVE'];
			                $total['DRIVE'] += $hrs['DRIVE'];
			                $date_total['REG'] -= $hrs['DRIVE'];
			                $date_total['DRIVE'] += $hrs['DRIVE'];
			            } else {
			                $hrs['DRIVE'] = 0;
			            }

                        //Mileage
                        $mileage_start = $date.' 00:00:00';
                        $mileage_end = $date.' 23:59:59';
                        $mileage = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`mileage`) `mileage_total` FROM `mileage` WHERE `deleted` = 0 AND `staffid` = '$search_staff' AND `ticketid` = '".$row['ticketid']."' AND '".$row['ticketid']."' > 0 AND (`start` BETWEEN '$mileage_start' AND '$mileage_end' OR `end` BETWEEN '$mileage_start' AND '$mileage_end')"))['mileage_total'];
                        $mileage_total += $mileage;

                        //Mileage Rate
                        $mileage_customer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `clientid` FROM `tickets` WHERE `ticketid` = '".$row['ticketid']."' AND '".$row['ticketid']."' > 0"))['clientid'];
                        $mileage_rate = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `mileage` `price` FROM `rate_card` WHERE `clientid` = '$mileage_customer' AND '$mileage_customer' > 0 AND `deleted` = 0 AND `on_off` = 1 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') UNION
                            SELECT `cust_price` `price` FROM `company_rate_card` WHERE LOWER(`tile_name`)='mileage' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')"))['price'];
                        $mileage_rate_total += $mileage_rate;

                        //Mileage Calculated Cost
                        $mileage_cost = $mileage * $mileage_rate;
                        $mileage_cost_total += $mileage_cost;
	                    $planned_hrs = get_ticket_planned_hrs($dbc, $date, $search_staff, $layout, $row['time_cards_id']);
				        $tracked_hrs = get_ticket_tracked_hrs($dbc, $date, $search_staff, $layout, $row['time_cards_id']);
				        $total_tracked_time = get_ticket_total_tracked_time($dbc, $date, $search_staff, $layout, $row['time_cards_id']);
                    } else {
					    $date_total = ['ROW_HRS'=>0,'OVERTIME'=>0,'DOUBLETIME'=>0,'REG'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'BREAKS'=>0,'TRAINING'=>0,'DRIVE'=>0,'TRACKED_HRS'=>0];
			            $hrs = ['ROW_HRS'=>0,'OVERTIME'=>0,'DOUBLETIME'=>0,'REG'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'BREAKS'=>0,'TRAINING'=>0,'TRACKED_HRS'=>0];
                        $row = '';
                        $mileage = 0;
                        $mileage_rate = 0;
                        $mileage_cost = 0;
                        $planned_hrs = '';
                        $tracked_hrs = '';
                        $total_tracked_time = '';
                    }
                    $start_times = [!empty($row['start_time']) ? date('h:i a', strtotime($row['start_time'])) : ''];
                    $end_times = [!empty($row['end_time']) ? date('h:i a', strtotime($row['end_time'])) : ''];
                    $ticketids = [$row['ticketid']];
                    $mileages = [];
                    $mileage_rates = [];
                    $mileage_costs = [];
                    if($mileage > 0) {
	                    $mileages[] = $mileage;
	                    $mileage_rates[] = $mileage_rate;
	                    $mileage_costs[] = $mileage_cost;
	                }
                    if($timesheet_payroll_layout == 'group_days') {
                    	$multidays = false;
                    	while($result[$i]['date'] == $date) {
                    		$next_row = $result[$i];
                    		$next_hrs = ['ROW_HRS'=>0,'OVERTIME'=>0,'DOUBLETIME'=>0,'REG'=>$next_row['REG_HRS'],'EXTRA'=>$next_row['EXTRA_HRS'],'RELIEF'=>$next_row['RELIEF_HRS'],'SLEEP'=>$next_row['SLEEP_HRS'],'SICK_ADJ'=>$next_row['SICK_ADJ'],'SICK'=>$next_row['SICK_HRS'],'STAT_AVAIL'=>$next_row['STAT_AVAIL'],'STAT'=>$next_row['STAT_HRS'],'VACA_AVAIL'=>$next_row['VACA_AVAIL'],'VACA'=>$next_row['VACA_HRS'],'BREAKS'=>$next_row['BREAKS'],'TRACKED_HRS'=>$next_row['TRACKED_HRS']];
				            foreach($next_hrs as $key => $hr) {
				            	if($hr > 0) {
				                    switch($timesheet_rounding) {
				                        case 'up':
				                            $next_hrs[$key] = ceil($next_hrs[$key] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
				                            break;
				                        case 'down':
				                            $next_hrs[$key] = floor($next_hrs[$key] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
				                            break;
				                        case 'nearest':
				                            $next_hrs[$key] = round($next_hrs[$key] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
				                            break;
				                    }
					            	$hrs[$key] += $next_hrs[$key];
					            	$total[$key] += $next_hrs[$key];
					            	$date_total[$key] += $next_hrs[$key];
					            	if($key != 'TRACKED_HRS') {
						            	$hrs['ROW_HRS'] += $next_hrs[$key];
						            	$grand_total += $next_hrs[$key];
						            }
					            }
				            }

				            if(in_array('training_hrs',$value_config) && $next_row['time_cards_id'] > 0) {
				                if(is_training_hrs($dbc, $next_row['time_cards_id'])) {
				                    $hrs['TRAINING'] += $next_hrs['REG'];
				                    $hrs['REG'] -= $next_hrs['REG'];
				                    $total['REG'] -= $next_hrs['REG'];
				                    $total['TRAINING'] += $next_hrs['REG'];
				                    $date_total['REG'] -= $next_hrs['REG'];
				                    $date_total['TRAINING'] += $next_hrs['REG'];
				                }
				            }
				            if(in_array('start_day_tile_separate',$value_config) && !($next_row['ticketid'] > 0)) {
				                $hrs['DRIVE'] += $next_hrs['REG'];
				                $hrs['REG'] -= $next_hrs['REG'];
				                $total['REG'] -= $next_hrs['REG'];
				                $total['DRIVE'] += $next_hrs['REG'];
				                $date_total['REG'] -= $next_hrs['REG'];
				                $date_total['DRIVE'] += $next_hrs['REG'];
				            }

		                    $start_times[] = !empty($next_row['start_time']) ? date('h:i a', strtotime($next_row['start_time'])) : '';
		                    $end_times[] = !empty($next_row['end_times']) ? date('h:i a', strtotime($next_row['end_times'])) : '';
                    		$ticketids[] = $next_row['ticketid'];
                    		$multidays = true;

	                        //Mileage
	                        $mileage_start = $date.' 00:00:00';
	                        $mileage_end = $date.' 23:59:59';
	                        $mileage = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`mileage`) `mileage_total` FROM `mileage` WHERE `deleted` = 0 AND `staffid` = '$search_staff' AND `ticketid` = '".$next_row['ticketid']."' AND '".$next_row['ticketid']."' > 0 AND (`start` BETWEEN '$mileage_start' AND '$mileage_end' OR `end` BETWEEN '$mileage_start' AND '$mileage_end')"))['mileage_total'];
	                        $mileage_total += $mileage;

	                        //Mileage Rate
	                        $mileage_customer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `clientid` FROM `tickets` WHERE `ticketid` = '".$next_row['ticketid']."' AND '".$next_row['ticketid']."'"))['clientid'];
	                        $mileage_rate = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `mileage` `price` FROM `rate_card` WHERE `clientid` = '$mileage_customer' AND '$mileage_customer' > 0 AND `deleted` = 0 AND `on_off` = 1 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') UNION
	                            SELECT `cust_price` `price` FROM `company_rate_card` WHERE LOWER(`tile_name`)='mileage' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')"))['price'];
	                        $mileage_rate_total += $mileage_rate;

	                        //Mileage Calculated Cost
	                        $mileage_cost = $mileage * $mileage_rate;
	                        $mileage_cost_total += $mileage_cost;

	                        if($mileage > 0) {
			                    $mileages[] = $mileage;
			                    $mileage_rates[] = $mileage_rate;
			                    $mileage_costs[] = $mileage_cost;
			                }

                    		$i++;
                    	}
		                $planned_hrs = get_ticket_planned_hrs($dbc, $date, $search_staff);
				        $tracked_hrs = get_ticket_tracked_hrs($dbc, $date, $search_staff);
				        $total_tracked_time = get_ticket_total_tracked_time($dbc, $date, $search_staff);
                    }
                    if(empty($mileages)) {
                    	$mileages = [''];
                    	$mileage_rates = [''];
                    	$mileage_costs = [''];
                    }
	                if($timesheet_payroll_doubletime > 0 && $hrs['REG'] > $timesheet_payroll_doubletime) {
	                	$hrs['DOUBLETIME'] = $hrs['REG'] - $timesheet_payroll_doubletime;
	                	$hrs['REG'] -= $hrs['DOUBLETIME'];
	                	$total['REG'] -= $hrs['DOUBLETIME'];
	                	$total['DOUBLETIME'] += $hrs['DOUBLETIME'];
	                }
	                if($timesheet_payroll_overtime > 0 && $hrs['REG'] > $timesheet_payroll_overtime) {
	                	$hrs['OVERTIME'] = $hrs['REG'] - $timesheet_payroll_overtime;
	                	$hrs['REG'] -= $hrs['OVERTIME'];
	                	$total['REG'] -= $hrs['OVERTIME'];
	                	$total['OVERTIME'] += $hrs['OVERTIME'];
	                }
                    $start_times = implode('<br />',$start_times);
                    $end_times = implode('<br />',$end_times);
                    $ticketids = array_filter(array_unique($ticketids));

	                $expenses_owed = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`total`) `expenses_owed` FROM `expense` WHERE `deleted` = 0 AND `staff` = '$search_staff' AND `status` = 'Approved' AND `approval_date` = '$date'"))['expenses_owed'];
                    if($hrs['ROW_HRS'] > 0 || ($expenses_owed > 0 && strpos($timesheet_payroll_fields, ',Expenses Owed,') !== FALSE)) {
						$show_row = true;
                    	$view_ticket = [];
                    	foreach($ticketids as $ticketid) {
	                    	if($ticketid > 0) {
	                    		$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '{$ticketid}'"));
	                    		if($report_format == 'to_array') {
	                    			$view_ticket[] = get_ticket_label($dbc, $ticket);
	                    		} else {
		                    		$view_ticket[] = '<a href="" onclick="viewTicket(this); return false;">'.get_ticket_label($dbc, $ticket).'</a>';
		                    	}
	                    	}
	                    }
	                    $mileage_html = [];
	                    $mileage_rate_html = [];
	                    $mileage_cost_html = [];
	                    foreach($mileages as $mileage_i => $mileage) {
	                    	$mileage_html[] = !empty($mileage) ? number_format($mileage,2) : '0.00';
	                    	$mileage_rate_html[] = '$'.(!empty($mileage_rates[$mileage_i]) ? number_format($mileage_rates[$mileage_i],2) : '0.00');
	                    	$mileage_cost_html[] = '$'.(!empty($mileage_costs[$mileage_i]) ? number_format($mileage_costs[$mileage_i],2) : '0.00');
	                    }
	                    $mileage_html = implode('<br>',$mileage_html);
	                    $mileage_rate_html = implode('<br>',$mileage_rate_html);
	                    $mileage_cost_html = implode('<br>',$mileage_cost_html);
	                    $view_ticket = implode('<br>',$view_ticket);
                        $report .= '<tr bgcolor="'.$bgcolor.'">
							<td style="border-top:1px solid #ddd; border-right:1px solid #ddd; border-left: 1px solid #ddd;" data-title="Date">'.$date.'</td>'.
                            (in_array('view_ticket',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="'.TICKET_NOUN.'">'.$view_ticket.'</td>' : '').
			                (strpos($timesheet_payroll_fields, ',Expenses Owed,') !== FALSE ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Expenses Owed">$'.($expenses_owed > 0 ? number_format($expenses_owed,2) : '0.00').'</td>' : '').
			                (strpos($timesheet_payroll_fields, ',Mileage,') !== FALSE ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Mileage">'.$mileage_html.'</td>' : '').
			                (strpos($timesheet_payroll_fields, ',Mileage Rate,') !== FALSE ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Mileage Rate">'.$mileage_rate_html.'</td>' : '').
			                (strpos($timesheet_payroll_fields, ',Mileage Total,') !== FALSE ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Mileage Total">'.$mileage_cost_html.'</td>' : '').
			                (in_array('start_time',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Start Time">'.$start_times.'</td>' : '').
			                (in_array('end_time',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="End Time">'.$end_times.'</td>' : '').
			                (in_array('planned_hrs',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Planned Hours">'.$planned_hrs.'</td>' : '').
			                (in_array('tracked_hrs',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Tracked Hours">'.$tracked_hrs.'</td>' : '').
			                (in_array('total_tracked_time',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Tracked Time">'.$total_tracked_time.'</td>' : '').
			                (in_array('total_tracked_hrs',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Time Tracked">'.(empty($hrs['TRACKED_HRS']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($hrs['TRACKED_HRS'],2) : time_decimal2time($hrs['TRACKED_HRS']))).' h</td>' : '').
                            (in_array('reg_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Hours">'.(empty($hrs['REG']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($hrs['REG'],2) : time_decimal2time($hrs['REG']))).' h</td>' : '').
                            (in_array('start_day_tile_separate',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="'.$timesheet_start_tile.'">'.(empty($hrs['DRIVE']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($hrs['DRIVE'],2) : time_decimal2time($hrs['DRIVE']))).' h</td>' : '').
                            (in_array('payable_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Payable Hours">'.(empty($hrs['REG']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($hrs['REG'],2) : time_decimal2time($hrs['REG']))).' h</td>' : '').
                            (in_array('overtime_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Over Time">'.(empty($hrs['OVERTIME']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($hrs['OVERTIME'],2) : time_decimal2time($hrs['OVERTIME']))).' h</td>' : '').
                            (in_array('doubletime_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Double Time">'.(empty($hrs['DOUBLETIME']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($hrs['DOUBLETIME'],2) : time_decimal2time($hrs['DOUBLETIME']))).' h</td>' : '').
                            (in_array('extra_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Extra Hours">'.(empty($hrs['EXTRA']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($hrs['EXTRA'],2) : time_decimal2time($hrs['EXTRA']))).' h</td>' : '').
                            (in_array('relief_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Relief Hours">'.(empty($hrs['RELIEF']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($hrs['RELIEF'],2) : time_decimal2time($hrs['RELIEF']))).' h</td>' : '').
                            (in_array('sleep_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Sleep Hours">'.(empty($hrs['SLEEP']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($hrs['SLEEP'],2) : time_decimal2time($hrs['SLEEP']))).' h</td>' : '').
                            (in_array('training_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Training Hours">'.(empty($hrs['TRAINING']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($hrs['TRAINING'],2) : time_decimal2time($hrs['TRAINING']))).' h</td>' : '').
                            (in_array('sick_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Sick Time Adjustment">'.(empty($hrs['SICK_ADJ']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($hrs['SICK_ADJ'],2) : time_decimal2time($hrs['SICK_ADJ']))).' h</td>' : '').
                            (in_array('sick_used',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Sick Hours Taken">'.(empty($hrs['SICK']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($hrs['SICK'],2) : time_decimal2time($hrs['SICK']))).' h</td>' : '').
                            (in_array('stat_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Stat Hours">'.(empty($hrs['STAT_AVAIL']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($hrs['STAT_AVAIL'],2) : time_decimal2time($hrs['STAT_AVAIL']))).' h</td>' : '').
                            (in_array('stat_used',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Stat Hours Taken">'.(empty($hrs['STAT']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($hrs['STAT'],2) : time_decimal2time($hrs['STAT']))).' h</td>' : '').
                            (in_array('vaca_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Vacation Hours">'.(empty($hrs['VACA_AVAIL']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($hrs['VACA_AVAIL'],2) : time_decimal2time($hrs['VACA_AVAIL']))).' h</td>' : '').
                            (in_array('vaca_used',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Vacation Hours Taken">'.(empty($hrs['VACA']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($hrs['VACA'],2) : time_decimal2time($hrs['VACA']))).' h</td>' : '').
                            (in_array('breaks',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Breaks">'.(empty($hrs['BREAKS']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($hrs['BREAKS'],2) : time_decimal2time($hrs['BREAKS']))).' h</td>' : '').'
                            <td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total">'.(empty($hrs['ROW_HRS']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($hrs['ROW_HRS'],2) : time_decimal2time($hrs['ROW_HRS']))).' h</td>';
                            if($tab == 'payroll' && $report_format != 'to_array') {
		                    	$report .= '<td align="center" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Unapprove">';
		                    	$report .= '<a href="" onclick="unapproveTimeSheet(this); return false;"'.($timesheet_payroll_layout ? ' data-type="day" data-date="'.$date.'"' : ' data-type="id" data-timesheetid="'.$row['time_cards_id'].'"').' data-staff="'.$search_staff.'">Unapprove</a>';
		                    	$report .= '</td>';
		                    }
                        $report .= '</tr>';
                    }
                    if(in_array('total_per_day',$value_config) && $date != $result[$i]['date'] && $timesheet_payroll_layout != 'group_days' && ($hrs['ROW_HRS'] > 0 || ($expenses_owed > 0 && strpos($timesheet_payroll_fields, ',Expenses Owed,') !== FALSE))) {
		                $report .= '<tr bgcolor="'.$bgcolor.'">
		                    <td style="border-top:1px solid #ddd; border-right:1px solid #ddd; border-left:1px solid #ddd; font-weight:bold;" data-title="">Day Totals</td>'.
		                    (in_array('view_ticket',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title=""></td>' : '' ).
			                (strpos($timesheet_payroll_fields, ',Expenses Owed,') !== FALSE ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Expenses Owed">$'.($expenses_owed > 0 ? number_format($expenses_owed,2) : '0.00').'</td>' : '').
			                (strpos($timesheet_payroll_fields, ',Mileage,') !== FALSE ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Mileage">'.($mileage_total > 0 ? number_format($mileage_total,2) : '0.00').'</td>' : '').
			                (strpos($timesheet_payroll_fields, ',Mileage Rate,') !== FALSE ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Mileage Rate">$'.($mileage_rate_total > 0 ? number_format($mileage_rate_total,2) : '0.00').'</td>' : '').
			                (strpos($timesheet_payroll_fields, ',Mileage Total,') !== FALSE ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Mileage Cost">$'.($mileage_cost_total > 0 ? number_format($mileage_cost_total,2) : '0.00').'</td>' : '').
		                    (in_array('start_time',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title=""></td>' : '' ).
		                    (in_array('end_time',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title=""></td>' : '' ).
		                    (in_array('planned_hrs',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title=""></td>' : '' ).
		                    (in_array('tracked_hrs',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title=""></td>' : '' ).
		                    (in_array('total_tracked_time',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title=""></td>' : '' ).
		                    (in_array('total_tracked_hrs',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="">'.($timesheet_time_format == 'decimal' ? number_format($date_total['TRACKED_HRS'],2) : time_decimal2time($date_total['TRACKED_HRS'])).' h</td>' : '' ).
		                    (in_array('reg_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Regular">'.($timesheet_time_format == 'decimal' ? number_format($date_total['REG'],2) : time_decimal2time($date_total['REG'])).' h</td>' : '' ).
		                    (in_array('start_day_tile_separate',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total '.$timesheet_start_tile.'">'.(empty($date_total['DRIVE']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($date_total['DRIVE'],2) : time_decimal2time($date_total['DRIVE']))).' h</td>' : '').
		                    (in_array('payable_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Payable">'.($timesheet_time_format == 'decimal' ? number_format($date_total['REG'],2) : time_decimal2time($date_total['REG'])).' h</td>' : '' ).
		                    (in_array('overtime_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Over Time">'.($timesheet_time_format == 'decimal' ? number_format($date_total['OVERTIME'],2) : time_decimal2time($date_total['OVERTIME'])).' h</td>' : '' ).
		                    (in_array('doubletime_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data="Total Double Time">'.($timesheet_time_format == 'decimal' ? number_format($date_total['DOUBLETIME'],2) : time_decimal2time($date_total['DOUBLETIME'])).' h</td>' : '' ).
		                    (in_array('extra_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Extra Hours">'.(empty($date_total['EXTRA']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($date_total['EXTRA'],2) : time_decimal2time($date_total['EXTRA']))).' h</td>' : '').
		                    (in_array('relief_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Relief Hours">'.(empty($date_total['RELIEF']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($date_total['RELIEF'],2) : time_decimal2time($date_total['RELIEF']))).' h</td>' : '').
		                    (in_array('sleep_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Sleep Hours">'.(empty($date_total['SLEEP']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($date_total['SLEEP'],2) : time_decimal2time($date_total['SLEEP']))).' h</td>' : '').
		                    (in_array('training_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Training Hours">'.(empty($date_total['TRAINING']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($date_total['TRAINING'],2) : time_decimal2time($date_total['TRAINING']))).' h</td>' : '').
		                    (in_array('sick_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Sick Time Adjustment">'.(empty($date_total['SICK_ADJ']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($date_total['SICK_ADJ'],2) : time_decimal2time($date_total['SICK_ADJ']))).' h</td>' : '').
		                    (in_array('sick_used',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Sick Hours Taken">'.(empty($date_total['SICK']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($date_total['SICK'],2) : time_decimal2time($date_total['SICK']))).' h</td>' : '').
		                    (in_array('stat_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Stat Hours">'.(empty($date_total['STAT_AVAIL']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($date_total['STAT_AVAIL'],2) : time_decimal2time($date_total['STAT_AVAIL']))).' h</td>' : '').
		                    (in_array('stat_used',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Stat Hours Taken">'.(empty($date_total['STAT']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($date_total['STAT'],2) : time_decimal2time($date_total['STAT']))).' h</td>' : '').
		                    (in_array('vaca_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Vacation Hours">'.(empty($date_total['VACA_AVAIL']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($date_total['VACA_AVAIL'],2) : time_decimal2time($date_total['VACA_AVAIL']))).' h</td>' : '').
		                    (in_array('vaca_used',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Vacation Hours Taken">'.(empty($date_total['VACA']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($date_total['VACA'],2) : time_decimal2time($date_total['VACA']))).' h</td>' : '').
		                    (in_array('breaks',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Breaks">'.(empty($date_total['BREAKS']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($date_total['BREAKS'],2) : time_decimal2time($date_total['BREAKS']))).' h</td>' : '').'
		                    <td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Hours">'.($timesheet_time_format == 'decimal' ? number_format($date_total['ROW_HRS'],2) : time_decimal2time($date_total['ROW_HRS'])).' h</td>
		                    '.($tab == 'payroll' && $report_format != 'to_array' ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title=""></td>' : '').'
		                </tr>';
                    }
                    if($date != $row['date']) {
                        $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
						if($show_row) {
                            if ($bgcolor == 'white') {
                                $bgcolor = '#eee';
                            } else {
                                $bgcolor = 'white';
                            }
                            $show_row = false;
                        }
                    }

                }

                $expenses_owed = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`total`) `expenses_owed` FROM `expense` WHERE `deleted` = 0 AND `staff` = '$search_staff' AND `status` = 'Approved' AND `approval_date` BETWEEN '$search_start_date' AND '$search_end_date'"))['expenses_owed'];

                $report .= '<tr>
                    <td style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="">Totals</td>'.
                    (in_array('view_ticket',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title=""></td>' : '' ).
	                (strpos($timesheet_payroll_fields, ',Expenses Owed,') !== FALSE ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Expenses Owed">$'.($expenses_owed > 0 ? number_format($expenses_owed,2) : '0.00').'</td>' : '').
	                (strpos($timesheet_payroll_fields, ',Mileage,') !== FALSE ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Mileage">'.($mileage_total > 0 ? number_format($mileage_total,2) : '0.00').'</td>' : '').
	                (strpos($timesheet_payroll_fields, ',Mileage Rate,') !== FALSE ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Mileage Rate">$'.($mileage_rate_total > 0 ? number_format($mileage_rate_total,2) : '0.00').'</td>' : '').
	                (strpos($timesheet_payroll_fields, ',Mileage Total,') !== FALSE ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Mileage Cost">$'.($mileage_cost_total > 0 ? number_format($mileage_cost_total,2) : '0.00').'</td>' : '').
                    (in_array('start_time',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title=""></td>' : '' ).
                    (in_array('end_time',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title=""></td>' : '' ).
                    (in_array('planned_hrs',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title=""></td>' : '' ).
                    (in_array('tracked_hrs',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title=""></td>' : '' ).
                    (in_array('total_tracked_time',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title=""></td>' : '' ).
                    (in_array('total_tracked_hrs',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="">'.($timesheet_time_format == 'decimal' ? number_format($total['TRACKED_HRS'],2) : time_decimal2time($total['TRACKED_HRS'])).' h</td>' : '' ).
                    (in_array('reg_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Regular">'.($timesheet_time_format == 'decimal' ? number_format($total['REG'],2) : time_decimal2time($total['REG'])).' h</td>' : '' ).
                    (in_array('start_day_tile_separate',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total '.$timesheet_start_tile.'">'.(empty($total['DRIVE']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['DRIVE'],2) : time_decimal2time($total['DRIVE']))).' h</td>' : '').
                    (in_array('payable_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Payable">'.($timesheet_time_format == 'decimal' ? number_format($total['REG'],2) : time_decimal2time($total['REG'])).' h</td>' : '' ).
                    (in_array('overtime_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Over Time">'.($timesheet_time_format == 'decimal' ? number_format($total['OVERTIME'],2) : time_decimal2time($total['OVERTIME'])).' h</td>' : '' ).
                    (in_array('doubletime_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data="Total Double Time">'.($timesheet_time_format == 'decimal' ? number_format($total['DOUBLETIME'],2) : time_decimal2time($total['DOUBLETIME'])).' h</td>' : '' ).
                    (in_array('extra_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Extra Hours">'.(empty($total['EXTRA']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['EXTRA'],2) : time_decimal2time($total['EXTRA']))).' h</td>' : '').
                    (in_array('relief_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Relief Hours">'.(empty($total['RELIEF']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['RELIEF'],2) : time_decimal2time($total['RELIEF']))).' h</td>' : '').
                    (in_array('sleep_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Sleep Hours">'.(empty($total['SLEEP']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['SLEEP'],2) : time_decimal2time($total['SLEEP']))).' h</td>' : '').
                    (in_array('training_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Training Hours">'.(empty($total['TRAINING']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['TRAINING'],2) : time_decimal2time($total['TRAINING']))).' h</td>' : '').
                    (in_array('sick_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Sick Time Adjustment">'.(empty($total['SICK_ADJ']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['SICK_ADJ'],2) : time_decimal2time($total['SICK_ADJ']))).' h</td>' : '').
                    (in_array('sick_used',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Sick Hours Taken">'.(empty($total['SICK']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['SICK'],2) : time_decimal2time($total['SICK']))).' h</td>' : '').
                    (in_array('stat_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Stat Hours">'.(empty($total['STAT_AVAIL']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['STAT_AVAIL'],2) : time_decimal2time($total['STAT_AVAIL']))).' h</td>' : '').
                    (in_array('stat_used',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Stat Hours Taken">'.(empty($total['STAT']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['STAT'],2) : time_decimal2time($total['STAT']))).' h</td>' : '').
                    (in_array('vaca_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Vacation Hours">'.(empty($total['VACA_AVAIL']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['VACA_AVAIL'],2) : time_decimal2time($total['VACA_AVAIL']))).' h</td>' : '').
                    (in_array('vaca_used',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Vacation Hours Taken">'.(empty($total['VACA']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['VACA'],2) : time_decimal2time($total['VACA']))).' h</td>' : '').
                    (in_array('breaks',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Breaks">'.(empty($total['BREAKS']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['BREAKS'],2) : time_decimal2time($total['BREAKS']))).' h</td>' : '').'
                    <td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Total Hours">'.($timesheet_time_format == 'decimal' ? number_format($grand_total,2) : time_decimal2time($grand_total)).' h</td>
                    '.($tab == 'payroll' && $report_format != 'to_array' ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title=""></td>' : '').'
                </tr>
            </table>';

        $tb_field = $value['config_field'];

		$report_blocks[] = $report;
		$report = '';
	}
	if(in_array('staff_combine',$value_config)) {
		foreach($report_blocks as $i => $report) {
			$key = array_search($report,array_slice($report_blocks,$i+1));
			while($key !== FALSE) {
				$report_name[$i+$key+1] .= ', '.$report_name[$i];
				unset($report_blocks[$i+$key]);
				unset($report_name[$i+$key]);
				$key = array_search($report,array_slice($report_blocks,$i+1));
			}
		}
	}
	foreach($report_blocks as $i => &$report_block) {
		$report_block = '<div class="clearfix"></div><br style="display:none;" /><h3 class="triple-gap-top">'.$report_name[$i].'</h3>'.$report_block;
	}
	if($report_format == 'to_array') {
		return implode('',$report_blocks);
	}
	return implode('',$report_blocks);
} ?>

<?php function get_egs_main_hours_report($dbc, $staff, $search_start_date, $search_end_date, $report_format = '', $tab, $override_value_config = '', $override_timesheet_payroll_fields = '') {
	global $config;
	$value_config = explode(',',get_field_config($dbc, 'time_cards_total_hrs_layout'));
	if(empty(array_filter($value_config))) {
		$value_config = ['reg_hrs','overtime_hrs','doubletime_hrs'];
	}
	if(!empty($override_value_config)) {
		$value_config = explode(',',$override_value_config);
	}
	$layout = get_config($dbc, 'timesheet_layout');
	$timesheet_payroll_fields = '';
	$timesheet_start_tile = get_config($dbc, 'timesheet_start_tile');
	$timesheet_time_format = get_config($dbc, 'timesheet_time_format');
	$timesheet_payroll_layout = get_config($dbc, 'timesheet_payroll_layout');
	$timesheet_payroll_overtime = get_config($dbc, 'timesheet_payroll_overtime');
	$timesheet_payroll_doubletime = get_config($dbc, 'timesheet_payroll_doubletime');
	$timesheet_rounding = get_config($_SERVER['DBC'], 'timesheet_rounding');
	$timesheet_rounded_increment = get_config($_SERVER['DBC'], 'timesheet_rounded_increment') / 60;
	$total_columns = 2;
	if($tab == 'payroll') {
		$timesheet_payroll_fields = ','.get_config($dbc, 'timesheet_payroll_fields').',';
		if(!empty($override_timesheet_payroll_fields)) {
			$timesheet_payroll_layout = ','.$override_timesheet_payroll_fields.',';
		}
		$total_columns += count(array_diff(array_filter(array_unique(explode(',',$timesheet_payroll_fields))),['Mileage','Mileage Rate','Mileage Total']));
	}
	$total_columns += count(array_diff(array_filter(array_unique($value_config)),['view_ticket','start_time','end_time','planned_hrs','tracked_hrs','total_tracked_time','staff_combine','total_per_day']));
	$col_width = 100 / $total_columns;

    if($staff == '' || count(array_filter($staff)) == 0) {
  		return '<h4>Please select a staff member.</h4>';
    } else {
        $staff_list = [];
		if(!is_array($staff)) {
			$staff = explode(',',$staff);
		}
        foreach ($staff as $search_staff) {
            if($search_staff > 0) {
                $staff_list[] = ['contactid'=>$search_staff,'first_name'=>'','last_name'=>get_contact($dbc, $search_staff)];
            }
        }
    }

	$report_output = [];
	$report_name = [];

	foreach($staff_list as $staff) {
		$report = '';
        $search_staff = $staff['contactid'];

		$start_of_year = date('Y-01-01', strtotime($search_start_date));
        $total_colspan = 2;

                $grand_total = 0;
                $limits = "AND `staff`='$search_staff'";
                if($search_site > 0) {
                    $limits .= " AND `business` LIKE '%$search_site%'";
                }
                if($tab == 'payroll') {
                	$limits .= " AND (`approv` = 'Y' OR `approv` = 'P')";
                }
                if($timesheet_payroll_layout == 'group_days') {
                	$group_by = 'date';
                } else {
                	$group_by = 'time_cards_id';
                }

                $sql = "SELECT `time_cards_id`, `date`, SUM(IF(`type_of_time` NOT IN ('Extra Hrs.','Relief Hrs.','Sleep Hrs.','Sick Time Adj.','Sick Hrs.Taken','Stat Hrs.','Stat Hrs.Taken','Vac Hrs.','Vac Hrs.Taken','Break'),`total_hrs`,0)) REG_HRS,
			        SUM(IF(`type_of_time`='Extra Hrs.',`total_hrs`,0)) EXTRA_HRS,
			        SUM(IF(`type_of_time`='Relief Hrs.',`total_hrs`,0)) RELIEF_HRS, SUM(IF(`type_of_time`='Sleep Hrs.',`total_hrs`,0)) SLEEP_HRS,
			        SUM(IF(`type_of_time`='Sick Time Adj.',`total_hrs`,0)) SICK_ADJ, SUM(IF(`type_of_time`='Sick Hrs.Taken',`total_hrs`,0)) SICK_HRS,
			        SUM(IF(`type_of_time`='Stat Hrs.',`total_hrs`,0)) STAT_AVAIL, SUM(IF(`type_of_time`='Stat Hrs.Taken',`total_hrs`,0)) STAT_HRS,
			        SUM(IF(`type_of_time`='Vac Hrs.',`total_hrs`,0)) VACA_AVAIL, SUM(IF(`type_of_time`='Vac Hrs.Taken',`total_hrs`,0)) VACA_HRS,
			        SUM(`highlight`) HIGHLIGHT, SUM(`manager_highlight`) MANAGER,
			        GROUP_CONCAT(DISTINCT `comment_box` SEPARATOR ', ') COMMENTS, SUM(`timer_tracked`) TRACKED_HRS, SUM(IF(`type_of_time`='Break',`total_hrs`,0)) BREAKS, `type_of_time`, `ticket_attached_id`, `ticketid`, `start_time`, `end_time`, `approv`, `coord_approvals`, `manager_approvals`, `manager_name`, `coordinator_name`
			        FROM `time_cards` WHERE `date` >= '$search_start_date' AND `date` <= '$search_end_date' $limits AND `deleted`=0 GROUP BY `$group_by` ORDER BY `date`, IFNULL(DATE_FORMAT(CONCAT_WS(' ',DATE(NOW()),`start_time`),'%H:%i'),STR_TO_DATE(`start_time`,'%l:%i %p')) ASC, IFNULL(DATE_FORMAT(CONCAT_WS(' ',DATE(NOW()),`end_time`),'%H:%i'),STR_TO_DATE(`end_time`,'%l:%i %p')) ASC";
		        $result = mysqli_fetch_all(mysqli_query($dbc, $sql),MYSQLI_ASSOC);
                $date = $search_start_date;
                $i = 0;
			    $total = ['ROW_HRS'=>0,'OVERTIME'=>0,'DOUBLETIME'=>0,'REG'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'BREAKS'=>0,'TRAINING'=>0,'DRIVE'=>0,'TRACKED_HRS'=>0];
	            $hrs = ['ROW_HRS'=>0,'OVERTIME'=>0,'DOUBLETIME'=>0,'REG'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'BREAKS'=>0,'TRAINING'=>0,'TRACKED_HRS'=>0];
                while(strtotime($date) <= strtotime($search_end_date)) {
                    if($result[$i]['date'] == $date) {
                        $row = $result[$i++];
			            $hrs = ['ROW_HRS'=>0,'OVERTIME'=>0,'DOUBLETIME'=>0,'REG'=>$row['REG_HRS'],'EXTRA'=>$row['EXTRA_HRS'],'RELIEF'=>$row['RELIEF_HRS'],'SLEEP'=>$row['SLEEP_HRS'],'SICK_ADJ'=>$row['SICK_ADJ'],'SICK'=>$row['SICK_HRS'],'STAT_AVAIL'=>$row['STAT_AVAIL'],'STAT'=>$row['STAT_HRS'],'VACA_AVAIL'=>$row['VACA_AVAIL'],'VACA'=>$row['VACA_HRS'],'BREAKS'=>$row['BREAKS'],'TRACKED_HRS'=>$row['TRACKED_HRS']];
			            foreach($hrs as $key => $hr) {
			            	if($hr > 0) {
			                    switch($timesheet_rounding) {
			                        case 'up':
			                            $hrs[$key] = ceil($hrs[$key] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
			                            break;
			                        case 'down':
			                            $hrs[$key] = floor($hrs[$key] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
			                            break;
			                        case 'nearest':
			                            $hrs[$key] = round($hrs[$key] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
			                            break;
			                    }
				            	$total[$key] += $hrs[$key];
				            	if($key != 'TRACKED_HRS') {
					            	$grand_total += $hrs[$key];
					            }
				            }
			            }

			            if(in_array('training_hrs',$value_config) && $row['time_cards_id'] > 0) {
			                if(is_training_hrs($dbc, $row['time_cards_id'])) {
			                    $hrs['TRAINING'] = $hrs['REG'];
			                    $hrs['REG'] = 0;
			                    $total['REG'] -= $hrs['TRAINING'];
			                    $total['TRAINING'] += $hrs['TRAINING'];
			                } else {
			                    $hrs['TRAINING'] = 0;
			                }
			            } else {
			                $hrs['TRAINING'] = 0;
			            }
			            if(in_array('start_day_tile_separate',$value_config) && !($row['ticketid'] > 0)) {
			                $hrs['DRIVE'] = $hrs['REG'];
			                $hrs['REG'] = 0;
			                $total['REG'] -= $hrs['DRIVE'];
			                $total['DRIVE'] += $hrs['DRIVE'];
			            } else {
			                $hrs['DRIVE'] = 0;
			            }
                    } else {
			            $hrs = ['ROW_HRS'=>0,'OVERTIME'=>0,'DOUBLETIME'=>0,'REG'=>0,'EXTRA'=>0,'RELIEF'=>0,'SLEEP'=>0,'SICK_ADJ'=>0,'SICK'=>0,'STAT_AVAIL'=>0,'STAT'=>0,'VACA_AVAIL'=>0,'VACA'=>0,'BREAKS'=>0,'TRAINING'=>0,'TRACKED_HRS'=>0];
                        $row = '';
                    }

	                if($timesheet_payroll_doubletime > 0 && $hrs['REG'] > $timesheet_payroll_doubletime) {
	                	$hrs['DOUBLETIME'] = $hrs['REG'] - $timesheet_payroll_doubletime;
	                	$hrs['REG'] -= $hrs['DOUBLETIME'];
	                	$total['REG'] -= $hrs['DOUBLETIME'];
	                	$total['DOUBLETIME'] += $hrs['DOUBLETIME'];
	                }
	                if($timesheet_payroll_overtime > 0 && $hrs['REG'] > $timesheet_payroll_overtime) {
	                	$hrs['OVERTIME'] = $hrs['REG'] - $timesheet_payroll_overtime;
	                	$hrs['REG'] -= $hrs['OVERTIME'];
	                	$total['REG'] -= $hrs['OVERTIME'];
	                	$total['OVERTIME'] += $hrs['OVERTIME'];
	                }

                    if($date != $row['date']) {
                        $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                    }

                }

                $base_url = $_SERVER[REQUEST_URI];

                $expenses_owed = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`total`) `expenses_owed` FROM `expense` WHERE `deleted` = 0 AND `staff` = '$search_staff' AND `status` = 'Approved' AND `approval_date` BETWEEN '$search_start_date' AND '$search_end_date'"))['expenses_owed'];

                $report .= '<tr>
                    <td style="border-top:1px solid #ddd; border-right:1px solid #ddd;font-weight:bold;" data-title="Staff">'.($report_format != 'to_array' ? '<a href="'.$base_url.'&see_staff='.$search_staff.'">'.$staff['first_name'].' '.$staff['last_name'].'</a>' : $staff['first_name'].' '.$staff['last_name']).'</td>'.
	                (strpos($timesheet_payroll_fields, ',Expenses Owed,') !== FALSE ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Expenses Owed">$'.($expenses_owed > 0 ? number_format($expenses_owed,2) : '0.00').'</td>' : '').
	                (in_array('total_tracked_hrs',$value_config) ? '<td style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Time Tracked">'.(empty($total['TRACKED_HRS']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['TRACKED_HRS'],2) : time_decimal2time($total['TRACKED_HRS']))).' h</td>' : '').
	                (in_array('reg_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Reg.">'.($timesheet_time_format == 'decimal' ? number_format($total['REG'],2) : time_decimal2time($total['REG'])).' h</td>' : '').
                    (in_array('start_day_tile_separate',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total '.$timesheet_start_tile.'">'.(empty($total['DRIVE']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['DRIVE'],2) : time_decimal2time($total['DRIVE']))).' h</td>' : '').
                    (in_array('payable_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Payable Hours">'.(empty($total['REG']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['REG'],2) : time_decimal2time($total['REG']))).' h</td>' : '').
                    (in_array('overtime_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Over">'.($timesheet_time_format == 'decimal' ? number_format($total['OVERTIME'],2) : time_decimal2time($total['OVERTIME'])).' h</td>' : '').
                    (in_array('doubletime_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Double">'.($timesheet_time_format == 'decimal' ? number_format($total['DOUBLETIME'],2) : time_decimal2time($total['DOUBLETIME'])).' h</td>' : '').
                    (in_array('extra_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Extra Hours">'.(empty($total['EXTRA']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['EXTRA'],2) : time_decimal2time($total['EXTRA']))).' h</td>' : '').
                    (in_array('relief_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Relief Hours">'.(empty($total['RELIEF']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['RELIEF'],2) : time_decimal2time($total['RELIEF']))).' h</td>' : '').
                    (in_array('sleep_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Sleep Hours">'.(empty($total['SLEEP']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['SLEEP'],2) : time_decimal2time($total['SLEEP']))).' h</td>' : '').
                    (in_array('training_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Training Hours">'.(empty($total['TRAINING']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['TRAINING'],2) : time_decimal2time($total['TRAINING']))).' h</td>' : '').
                    (in_array('sick_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Sick Time Adjustment">'.(empty($total['SICK_ADJ']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['SICK_ADJ'],2) : time_decimal2time($total['SICK_ADJ']))).' h</td>' : '').
                    (in_array('sick_used',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Sick Hours Taken">'.(empty($total['SICK']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['SICK'],2) : time_decimal2time($total['SICK']))).' h</td>' : '').
                    (in_array('stat_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Stat Hours">'.(empty($total['STAT_AVAIL']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['STAT_AVAIL'],2) : time_decimal2time($total['STAT_AVAIL']))).' h</td>' : '').
                    (in_array('stat_used',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Stat Hours Taken">'.(empty($total['STAT']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['STAT'],2) : time_decimal2time($total['STAT']))).' h</td>' : '').
                    (in_array('vaca_hrs',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Vacation Hours">'.(empty($total['VACA_AVAIL']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['VACA_AVAIL'],2) : time_decimal2time($total['VACA_AVAIL']))).' h</td>' : '').
                    (in_array('vaca_used',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Vacation Hours Taken">'.(empty($total['VACA']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['VACA'],2) : time_decimal2time($total['VACA']))).' h</td>' : '').
                    (in_array('breaks',$value_config) ? '<td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Breaks">'.(empty($total['BREAKS']) ? ($timesheet_time_format == 'decimal' ? '0.00' : '0:00') : ($timesheet_time_format == 'decimal' ? number_format($total['BREAKS'],2) : time_decimal2time($total['BREAKS']))).' h</td>' : '').'
                    <td align="right" style="border-top:1px solid #ddd; border-right:1px solid #ddd;" data-title="Total Time">'.($timesheet_time_format == 'decimal' ? number_format($grand_total,2) : time_decimal2time($grand_total)).' h</td>
                </tr>
            ';

        $tb_field = $value['config_field'];

		$report_output[] = $report;
		$report = '';
	}

	return '<table cellpadding="3" border="0" class="table table-bordered" style="text-align:left; border:1px solid #ddd;">
            <tr class="hidden-xs hidden-sm">
                <th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Staff</div></th>'.
                (strpos($timesheet_payroll_fields, ',Expenses Owed,') !== FALSE ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total Expenses Owed</div></th>' : '').
                (in_array('total_tracked_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total Tracked Hours</div></th>' : '').
                (in_array('reg_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total Reg. Time</div></th>' : '').
                (in_array('start_day_tile_separate',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total '.$timesheet_start_tile.'</div></th>' : '').
                (in_array('payable_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total Payable Hours</div></th>' : '').
                (in_array('overtime_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total Over Time</div></th>' : '').
                (in_array('doubletime_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total Double Time</div></th>' : '').
                (in_array('extra_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total Extra Hours</div></th>' : '').
                (in_array('relief_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total Relief Hours</div></th>' : '').
                (in_array('sleep_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total Sleep Hours</div></th>' : '').
                (in_array('training_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total Training Hours</div></th>' : '').
                (in_array('sick_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total Sick Time Adjustment</div></th>' : '').
                (in_array('sick_used',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total Sick Hours Taken</div></th>' : '').
                (in_array('stat_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total Stat Hours</div></th>' : '').
                (in_array('stat_used',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total Stat Hours Taken</div></th>' : '').
                (in_array('vaca_hrs',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total Vacation Hours</div></th>' : '').
                (in_array('vaca_used',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total Vacation Hours Taken</div></th>' : '').
                (in_array('breaks',$value_config) ? '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total Breaks</div></th>' : '').
                '<th style="border-right: 1px solid #ddd; text-align:center; width:'.$col_width.'%;font-weight:bold;"><div>Total Time</div></th>
            </tr>'.implode('',$report_output).'</table>';
} ?>