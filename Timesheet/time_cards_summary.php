<?php include_once('../include.php');
include_once('../Calendar/calendar_functions_inc.php');
include_once('../Timesheet/config.php');
if(empty($search_staff)) {
    $search_staff = $_SESSION['contactid'];
    if(!empty($_GET['search_staff'])) {
        $search_staff = $_GET['search_staff'];
    }
    if(!empty($_GET['search_client'])) {
        $search_staff = $_GET['search_client'];
    }
}
if(empty($search_start_date)) {
    $search_start_date = date('Y-m-01');
    $search_end_date = date('Y-m-t');
    if(!empty($_GET['search_start_date'])) {
        $search_start_date = $_GET['search_start_date'];    
    }
    if(!empty($_GET['search_end_date'])) {
        $search_end_date = $_GET['search_end_date'];    
    }
}
if(empty($search_staff)) {
    $search_project = 0;
    if(!empty($_GET['search_project'])) {
        $search_project = $_GET['search_project'];    
    } 
} 
if(empty($search_staff)) {
    $search_ticket = 0;
    if(!empty($_GET['search_ticket'])) {
        $search_ticket = $_GET['search_ticket'];    
    } 
} 
if(empty($search_staff)) {
    $search_site = '';
    if(!empty($_GET['search_site'])) {
        $search_site = $_GET['search_site'];    
    } 
} 
$current_period = isset($_GET['pay_period']) ? $_GET['pay_period'] : 0;
$timesheet_time_format = get_config($dbc, 'timesheet_time_format');
$timesheet_start_tile = get_config($dbc, 'timesheet_start_tile');
$timesheet_rounding = get_config($dbc, 'timesheet_rounding');
$timesheet_rounded_increment = get_config($_SERVER['DBC'], 'timesheet_rounded_increment') / 60;
$value_config = explode(',',get_field_config($dbc, 'time_cards'));
if(!in_array('reg_hrs',$value_config) && !in_array('direct_hrs',$value_config) && !in_array('payable_hrs',$value_config) && !in_array('total_hrs',$value_config)) {
	$value_config = array_merge($value_config,['total_hrs']);
}
include('pay_period_dates.php');
$layout = get_config($dbc, 'timesheet_layout');

$summary_times = [];
if($layout == '' || $layout == 'multi_line') {
	if ( empty($search_site) ) {
	    $sql = "SELECT `time_cards_id`, `date`, SUM(IF(`type_of_time` NOT IN ('Extra Hrs.','Relief Hrs.','Sleep Hrs.','Sick Time Adj.','Sick Hrs.Taken','Stat Hrs.','Stat Hrs.Taken','Vac Hrs.','Vac Hrs.Taken','Break'),`total_hrs`,0)) REG_HRS,
		SUM(IF(`type_of_time`='Extra Hrs.',`total_hrs`,0)) EXTRA_HRS,
		SUM(IF(`type_of_time`='Relief Hrs.',`total_hrs`,0)) RELIEF_HRS, SUM(IF(`type_of_time`='Sleep Hrs.',`total_hrs`,0)) SLEEP_HRS,
		SUM(IF(`type_of_time`='Sick Time Adj.',`total_hrs`,0)) SICK_ADJ, SUM(IF(`type_of_time`='Sick Hrs.Taken',`total_hrs`,0)) SICK_HRS,
		SUM(IF(`type_of_time`='Stat Hrs.',`total_hrs`,0)) STAT_AVAIL, SUM(IF(`type_of_time`='Stat Hrs.Taken',`total_hrs`,0)) STAT_HRS,
		SUM(IF(`type_of_time`='Vac Hrs.',`total_hrs`,0)) VACA_AVAIL, SUM(IF(`type_of_time`='Vac Hrs.Taken',`total_hrs`,0)) VACA_HRS,
		SUM(`highlight`) HIGHLIGHT, SUM(`manager_highlight`) MANAGER,
		GROUP_CONCAT(DISTINCT NULLIF(`comment_box`,'') SEPARATOR ', ') COMMENTS, GROUP_CONCAT(`projectid`) PROJECTS, GROUP_CONCAT(`clientid`) CLIENTS,
		SUM(`timer_tracked`) TRACKED_HRS,
		SUM(IF(`type_of_time`='Direct Hrs.',`total_hrs`,0)) DIRECT_HRS, SUM(IF(`type_of_time`='Indirect Hrs.',`total_hrs`,0)) INDIRECT_HRS, SUM(IF(`type_of_time`='Break',`total_hrs`,0)) BREAKS,
        `ticket_attached_id`, `manager_approvals`, `coord_approvals`, `manager_name`, `coordinator_name`, `ticketid`, `start_time`, `end_time` FROM `time_cards`
        WHERE `staff`='$search_staff' AND `date` >= '$search_start_date' AND `date` <= '$search_end_date' AND `deleted`=0 $sql_approv GROUP BY `date`";
	} else {
	    $sql = "SELECT `time_cards_id`, `date`, SUM(IF(`type_of_time` NOT IN ('Extra Hrs.','Relief Hrs.','Sleep Hrs.','Sick Time Adj.','Sick Hrs.Taken','Stat Hrs.','Stat Hrs.Taken','Vac Hrs.','Vac Hrs.Taken','Break'),`total_hrs`,0)) REG_HRS,
		SUM(IF(`type_of_time`='Extra Hrs.',`total_hrs`,0)) EXTRA_HRS,
		SUM(IF(`type_of_time`='Relief Hrs.',`total_hrs`,0)) RELIEF_HRS, SUM(IF(`type_of_time`='Sleep Hrs.',`total_hrs`,0)) SLEEP_HRS,
		SUM(IF(`type_of_time`='Sick Time Adj.',`total_hrs`,0)) SICK_ADJ, SUM(IF(`type_of_time`='Sick Hrs.Taken',`total_hrs`,0)) SICK_HRS,
		SUM(IF(`type_of_time`='Stat Hrs.',`total_hrs`,0)) STAT_AVAIL, SUM(IF(`type_of_time`='Stat Hrs.Taken',`total_hrs`,0)) STAT_HRS,
		SUM(IF(`type_of_time`='Vac Hrs.',`total_hrs`,0)) VACA_AVAIL, SUM(IF(`type_of_time`='Vac Hrs.Taken',`total_hrs`,0)) VACA_HRS,
		SUM(`highlight`) HIGHLIGHT, SUM(`manager_highlight`) MANAGER,
		GROUP_CONCAT(DISTINCT NULLIF(`comment_box`,'') SEPARATOR ', ') COMMENTS, GROUP_CONCAT(`projectid`) PROJECTS, GROUP_CONCAT(`clientid`) CLIENTS,
		SUM(`timer_tracked`) TRACKED_HRS,
		SUM(IF(`type_of_time`='Direct Hrs.',`total_hrs`,0)) DIRECT_HRS, SUM(IF(`type_of_time`='Indirect Hrs.',`total_hrs`,0)) INDIRECT_HRS, SUM(IF(`type_of_time`='Break',`total_hrs`,0)) BREAKS,
        `ticket_attached_id`, `manager_approvals`, `coord_approvals`, `manager_name`, `coordinator_name`, `ticketid`, `start_time`, `end_time` FROM `time_cards`
        WHERE `staff`='$search_staff' AND `date` >= '$search_start_date' AND `date` <= '$search_end_date' AND IFNULL(`business`,'') LIKE '%$search_site%' AND `deleted`=0 $sql_approv GROUP BY `date`";
	}
	if($layout == 'multi_line') {
		$sql .= ", `time_cards_id`";
	}
	$sql .= " ORDER BY `date`, IFNULL(STR_TO_DATE(`start_time`, '%l:%i %p'),STR_TO_DATE(`start_time`, '%H:%i')) ASC, IFNULL(STR_TO_DATE(`end_time`, '%l:%i %p'),STR_TO_DATE(`end_time`, '%H:%i')) ASC";
	$result = mysqli_query($dbc, $sql);
	if(in_array('total_tracked_hrs',$value_config)) {
		$summary_times['Total Tracked Hours'] = 0;
	}
	if(in_array('reg_hrs',$value_config) || in_array('payable_hrs',$value_config)) {
		$summary_times[(in_array('payable_hrs',$value_config) ? 'Payable' : 'Regular').' Hours'] = 0;
	}
	if(in_array('start_day_tile',$value_config)) {
		$summary_times[$timesheet_start_tile] = 0;
	}
	if(in_array('direct_hrs',$value_config)) {
		$summary_times['Direct Hours'] = 0;
	}
	if(in_array('indirect_hrs',$value_config)) {
		$summary_times['Indirect Hours'] = 0;
	}
	if(in_array('extra_hrs',$value_config)) {
		$summary_times['Extra Hours'] = 0;
	}
	if(in_array('relief_hrs',$value_config)) {
		$summary_times['Relief Hours'] = 0;
	}
	if(in_array('sleep_hrs',$value_config)) {
		$summary_times['Sleep Hours'] = 0;
	}
	if(in_array('training_hrs',$value_config)) {
		$summary_times['Training Hours'] = 0;
	}
	if(in_array('sick_hrs',$value_config)) {
		$summary_times['Sick Time Adjustment'] = 0;
	}
	if(in_array('sick_used',$value_config)) {
		$summary_times['Sick Hours Taken'] = 0;
	}
	if(in_array('stat_hrs',$value_config)) {
		$summary_times['Stat Hours'] = 0;
	}
	if(in_array('stat_used',$value_config)) {
		$summary_times['Stat Hours Taken'] = 0;
	}
	if(in_array('vaca_hrs',$value_config)) {
		$summary_times['Vacation Hours'] = 0;
	}
	if(in_array('vaca_used',$value_config)) {
		$summary_times['Vacation Hours Taken'] = 0;
	}
	if(in_array('breaks',$value_config)) {
		$summary_times['Breaks'] = 0;
	}
	while($row = mysqli_fetch_array($result)) {
		foreach($config['hours_types'] as $hours_type) {
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
		if(in_array('training_hrs',$value_config) && $row['timecardid'] > 0) {
			if(is_training_hrs($dbc, $row['timecardid'])) {
				$row['TRAINING'] = $row['REG_HRS'];
				$row['REG_HRS'] = 0;
			} else {
				$row['TRAINING'] = 0;
			}
		} else {
			$row['TRAINING'] = 0;
		}
		if(in_array('start_day_tile',$value_config) && !($row['ticketid'] > 0)) {
			$row['DRIVE'] = $row['REG_HRS'];
			$row['REG_HRS'] = 0;
		} else {
			$row['DRIVE'] = 0;
		}
		if(in_array('total_tracked_hrs',$value_config)) {
			$summary_times['Total Tracked Hours'] += $row['TRACKED_HRS'];
		}
		if(in_array('reg_hrs',$value_config) || in_array('payable_hrs',$value_config)) {
			$summary_times[(in_array('payable_hrs',$value_config) ? 'Payable' : 'Regular').' Hours'] += $row['REG_HRS'];
		}
		if(in_array('start_day_tile',$value_config)) {
			$summary_times[$timesheet_start_tile] += $row['DRIVE'];
		}
		if(in_array('direct_hrs',$value_config)) {
			$summary_times['Direct Hours'] += $row['DIRECT_HRS'];
		}
		if(in_array('indirect_hrs',$value_config)) {
			$summary_times['Indirect Hours'] += $row['INDIRECT_HRS'];
		}
		if(in_array('extra_hrs',$value_config)) {
			$summary_times['Extra Hours'] += $row['EXTRA_HRS'];
		}
		if(in_array('relief_hrs',$value_config)) {
			$summary_times['Relief Hours'] += $row['RELIEF_HRS'];
		}
		if(in_array('sleep_hrs',$value_config)) {
			$summary_times['Sleep Hours'] += $row['SLEEP_HRS'];
		}
		if(in_array('training_hrs',$value_config)) {
			$summary_times['Training Hours'] += $row['TRAINING'];
		}
		if(in_array('sick_hrs',$value_config)) {
			$summary_times['Sick Time Adjustment'] += $row['SICK_ADJ'];
		}
		if(in_array('sick_used',$value_config)) {
			$summary_times['Sick Hours Taken'] += $row['SICK_HRS'];
		}
		if(in_array('stat_hrs',$value_config)) {
			$summary_times['Stat Hours'] += $row['STAT_AVAIL'];
		}
		if(in_array('stat_used',$value_config)) {
			$summary_times['Stat Hours Taken'] += $row['STAT_HRS'];
		}
		if(in_array('vaca_hrs',$value_config)) {
			$summary_times['Vacation Hours'] += $row['VACA_AVAIL'];
		}
		if(in_array('vaca_used',$value_config)) {
			$summary_times['Vacation Hours Taken'] += $row['VACA_HRS'];
		}
		if(in_array('breaks',$value_config)) {
			$summary_times['Breaks'] += $row['BREAKS'];
		}
	}
} else if($layout == 'position_dropdown' || $layout == 'ticket_task') {
	if($search_site > 0) {
		$limits .= " AND IFNULL(`business`,'') LIKE '%$search_site%'";
	}
	if($search_ticket > 0) {
		$limits .= " AND IFNULL(`ticketid`,'') = '$search_ticket'";
	}
	$result = get_time_sheet($search_start_date, $search_end_date, $limits, ', `staff`, `date`, `time_cards_id`');
	if(in_array('total_tracked_hrs',$value_config)) {
		$summary_times['Time Tracked'] = 0;
	}
	if(in_array('start_day_tile',$value_config)) {
		$summary_times['Driving Time'] = 0;
	}
	$summary_times['Hours'] = 0;
	if(in_array('vaca_hrs',$value_config)) {
		$summary_times['Vacation Hours'] = 0;
	}
	foreach($result as $row) {
		if(in_array('total_tracked_hrs',$value_config)) {
			$summary_times['Time Tracked'] += $row['timer'];
		}
		if(in_array('start_day_tile',$value_config) && empty($row['ticketid'])) {
			$summary_times['Driving Time'] += $row['hours'];
		}
		if((!in_array('start_day_tile',$value_config) || !empty($row['ticketid'])) && $row['type_of_time'] != 'Vac Hrs') {
			$summary_times['Hours'] += $row['hours'];
		}
		if(in_array('vaca_hrs',$value_config) && $row['type_of_time'] == 'Vac Hrs') {
			$summary_times['Vacation Hours'] += $row['hours'];
		}
	}
}

$total_time = 0; ?>
<div id="no-more-tables">
	<h2>Time Summary</h2>
	<table class="table table-bordered table-striped">
        <thead>
            <tr class="hidden-xs hidden-sm">
                <th>Summary</th>
                <th>Hours</th>
                <?php if(in_array('compensation',$value_config) && $hourly_comp > 0) { ?><th>Compensation</th><?php } ?>
            </tr>
        </thead>
		<?php foreach($summary_times as $summary_label => $summary_time) {
			$total_time += $summary_time; ?>
			<tr>
				<td data-title="Summary"><?= $summary_label ?></td>
				<td data-title="Hours" align="right"><?= ($timesheet_time_format == 'decimal' ? number_format($summary_time,2) : time_decimal2time($summary_time)) ?></td>
                <?php if(in_array('compensation',$value_config) && $hourly_comp > 0) { ?><td data-tile="Compensation"><?= number_format($summary_time * $hourly_comp,2) ?></td><?php } ?>
			</tr>
		<?php } ?>
		<tr>
			<td data-title="Total Hours"><b>Total Hours</b></td>
			<td data-title="Hours" align="right"><b><?= ($timesheet_time_format == 'decimal' ? number_format($total_time,2) : time_decimal2time($total_time)) ?></b></td>
                <?php if(in_array('compensation',$value_config) && $hourly_comp > 0) { ?><td data-tile="Compensation"><?= number_format($total['COMP_TOTAL'],2) ?></td><?php } ?>
		</tr>
	</table>
</div>