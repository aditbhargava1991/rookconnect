<?php // Get Pay Period for Staff to set default dates
$default_tab = get_config($dbc, 'timesheet_default_tab');
$_GET['tab'] = !empty($_GET['tab'] && $_GET['tab'] != 'timesheets') ? $_GET['tab'] : (!empty($default_tab) ? $default_tab : 'Custom');

switch($_GET['tab']) {
	case 'Daily':
		$today_date = date('Y-m-d');
		$period_start_date = $today_date;
		$period_end_date = $today_date;
		if(!empty($_GET['pay_period'])) {
			$period_start_date = date('Y_m-d', strtotime($today_date.$_GET['pay_period'].' days'));
			$period_end_date = date('Y_m-d', strtotime($today_date.$_GET['pay_period'].' days'));
		}
		if(empty($_GET['search_start_date'])) {
			$search_start_date = $period_start_date;
		}
		if(empty($_GET['search_end_date'])) {
			$search_end_date = $period_end_date;
		}
		break;
	case 'Weekly':
		$today_date = date('Y-m-d');
		if(date('w', strtotime($today_date)) != 0) {
			$period_start_date = date('Y-m-d', strtotime('last sunday', strtotime($today_date)));
		} else {
			$period_start_date = $today_date;
		}
		if(date('w', strtotime($today_date)) != 6) {
			$period_end_date = date('Y-m-d', strtotime('next saturday', strtotime($today_date)));
		} else {
			$period_end_date = $today_date;
		}
		if(!empty($_GET['pay_period'])) {
			$period_start_date = date('Y-m-d', strtotime($period_start_date.$_GET['pay_period'].' weeks'));
			$period_end_date = date('Y-m-d', strtotime($period_end_date.$_GET['pay_period'].' weeks'));
		}
		if(empty($_GET['search_start_date'])) {
			$search_start_date = $period_start_date;
		}
		if(empty($_GET['search_end_date'])) {
			$search_end_date = $period_end_date;
		}
		break;
	case 'Bi-Weekly':
		$today_date = date('Y-m-d');
		if(date('w', strtotime($today_date)) != 0) {
			$period_start_date = date('Y-m-d', strtotime('last sunday', strtotime($today_date)));
		} else {
			$period_start_date = $today_date;
		}
		if(date('w', strtotime($today_date)) != 6) {
			$period_end_date = date('Y-m-d', strtotime('next saturday', strtotime($today_date.' + 1 week')));
		} else {
			$period_end_date = $today_date;
		}
		if(!empty($_GET['pay_period'])) {
			$period_start_date = date('Y-m-d', strtotime($period_start_date.($_GET['pay_period']*2).' weeks'));
			$period_end_date = date('Y-m-d', strtotime($period_end_date.($_GET['pay_period']*2).' weeks'));
		}
		if(empty($_GET['search_start_date'])) {
			$search_start_date = $period_start_date;
		}
		if(empty($_GET['search_end_date'])) {
			$search_end_date = $period_end_date;
		}
		break;
	case 'Monthly':
		$today_date = date('Y-m-d');
		$period_start_date = date('Y-m-01', strtotime($today_date));
		$period_end_date = date('Y-m-t', strtotime($today_date));
		if(!empty($_GET['pay_period'])) {
			$period_start_date = date('Y-m-01', strtotime($today_date.$_GET['pay_period'].' months'));
			$period_end_date = date('Y-m-t', strtotime($today_date.$_GET['pay_period'].' months'));
		}
		if(empty($_GET['search_start_date'])) {
			$search_start_date = $period_start_date;
		}
		if(empty($_GET['search_end_date'])) {
			$search_end_date = $period_end_date;
		}
		break;
	case 'Custom':
		$pay_period = mysqli_query($dbc, "SELECT * FROM `pay_period` WHERE CONCAT(',',`staff`,',') LIKE '%,".$search_staff.",%' OR '".$search_staff."' = 'ALL' UNION SELECT * FROM `pay_period` WHERE `all_staff`='1'");
		$last_period = date('Y-m-d',strtotime($search_start_date.' - 1 day'));
		if($pay_period = mysqli_fetch_array($pay_period)) {
			$start = $pay_period['start_date'];
			$end_period = $pay_period['end_date_period'];
			$period = $pay_period['pay_period'];
			$start_year = 0;
			$start_month = 0;
			$start_day = 0;
			$end_year = 0;
			$end_month = 0;
			$end_day = 0;
			switch($period) {
				case 'Custom':
					$today_date = date('Y-m-d');
					$pay_period = mysqli_fetch_array(mysqli_query($dbc, "SELECT *, IF(IFNULL(`end_date`,'') = '', `end_date_period`, `end_date`) `end_date` FROM `pay_period` WHERE (CONCAT(',',`staff`,',') LIKE '%,".$search_staff.",%' OR '".$search_staff."' = 'ALL') AND '$today_date' BETWEEN `start_date` AND IF(IFNULL(`end_date`,'') = '', `end_date_period`, `end_date`) AND `pay_period` = 'Custom' UNION SELECT *, IF(IFNULL(`end_date`,'') = '', `end_date_period`, `end_date`) `end_date` FROM `pay_period` WHERE `all_staff`='1' AND '$today_date' BETWEEN `start_date` AND IF(IFNULL(`end_date`,'') = '', `end_date_period`, `end_date`) AND `pay_period` = 'Custom'"));
					if(!empty($_GET['pay_period'])) {
						$pay_periods_query = mysqli_fetch_all(mysqli_query($dbc, "SELECT *, IF(IFNULL(`end_date`,'') = '', `end_date_period`, `end_date`) `end_date` FROM `pay_period` WHERE (CONCAT(',',`staff`,',') LIKE '%,".$search_staff.",%' OR '".$search_staff."' = 'ALL') AND `pay_period` = 'Custom' UNION SELECT *, IF(IFNULL(`end_date`,'') = '', `end_date_period`, `end_date`) `end_date` FROM `pay_period` WHERE `all_staff`='1' AND `pay_period` = 'Custom' ORDER BY `start_date`"),MYSQLI_ASSOC);
						foreach($pay_periods_query as $key => $pay_period_query) {
							if(strtotime($today_date) >= strtotime($pay_period_query['start_date']) && strtotime($today_date) <= strtotime($pay_period_query['end_date'])) {
								if(!empty($pay_periods_query[$key + $_GET['pay_period']])) {
									$pay_period = $pay_periods_query[$key + $_GET['pay_period']];
								}
								break;
							}
						}
					}
					$start = empty($pay_period['start_date']) ? $today_date : $pay_period['start_date'];
					$end_period = $pay_period['end_date_period'];
					$period = $pay_period['pay_period'];
					$start_year = date('Y',strtotime($start));
					$start_month = date('n',strtotime($start));
					$start_day = date('j',strtotime($start));
					$end_year = date('Y',strtotime($pay_period['end_date']));
					$end_month = date('n',strtotime($pay_period['end_date']));
					$end_day = date('j',strtotime($pay_period['end_date']));
					if(empty($pay_period['end_date'])) {
						$date_obj = new DateTime($start);
						$date_obj->add(new DateInterval('P1M'))->sub(new DateInterval('P1D'));
						$end_year = $date_obj->format('Y');
						$end_month = $date_obj->format('n');
						$end_day = $date_obj->format('j');
					}
					break;
				case 'Weekly':
					$start_day_of_week = date('l',strtotime($start));
					$today_day_of_week = date('l');
					if($start_day_of_week == $today_day_of_week) {
						$start_date = date('Y-m-d');
					} else {
						$start_date = date('Y-m-d', strtotime('last '.$start_day_of_week));
					}
					if(get_config($dbc, 'timesheet_submit_mode') == 'auto') {
						$last_period = date('Y-m-d',strtotime($start_date.' - 1 day'));
					}
					if($current_period > 0) {
						$start_date = date('Y-m-d', strtotime($start_date.' + '.($current_period * 7).' days'));
					} else if($current_period < 0) {
						$start_date = date('Y-m-d', strtotime($start_date.' - '.($current_period * -7).' days'));
					}
					$date_obj = new DateTime($start_date);
					$start_year = $date_obj->format('Y');
					$start_month = $date_obj->format('n');
					$start_day = $date_obj->format('j');
					$date_obj->add(new DateInterval('P6D'));
					$end_year = $date_obj->format('Y');
					$end_month = $date_obj->format('n');
					$end_day = $date_obj->format('j');
					break;
				case 'Bi-Weekly':
					$start_date = new DateTime($start);
					$target_date = new DateTime();
					$days_to_period = $target_date->diff($start_date)->days % 14;
					$target_date->modify('-'.$days_to_period.' days');
					if(get_config($dbc, 'timesheet_submit_mode') == 'auto') {
						$last_period = date('Y-m-d',strtotime($target_date->format('Y-m-d').' - 1 day'));
					}
					if($current_period > 0) {
						$target_date->add(new DateInterval('P'.($current_period * 14).'D'));
					} else if($current_period < 0) {
						$target_date->sub(new DateInterval('P'.($current_period * -14).'D'));
					}
					$start_year = $target_date->format('Y');
					$start_month = $target_date->format('n');
					$start_day = $target_date->format('j');
					$target_date->add(new DateInterval('P13D'));
					$end_year = $target_date->format('Y');
					$end_month = $target_date->format('n');
					$end_day = $target_date->format('j');
					break;
				case 'Semi-Monthly':
					if (!empty($end_period)) {
						$start_day_of_month = date('j',strtotime($start));
						$end_day_of_month = date('j',strtotime($end_period));
						$today_day_of_month = date('j');
						$day_differences = $end_day_of_month - $start_day_of_month;
						if ($day_differences < 0) {
							$start_day_of_month = date('j',strtotime($end_period)) + 1;
							$end_day_of_month = date('j',strtotime($start)) - 1;
						}
						if ($today_day_of_month >= $start_day_of_month && $today_day_of_month <= $end_day_of_month) {
							$start_date_obj = new DateTime(date('Y-m-'.$start_day_of_month));
							$end_date_obj = new DateTime(date('Y-m-'.$end_day_of_month));
						} else if ($today_day_of_month > $end_day_of_month) {
							$end_year = date('Y');
							$end_day_of_month += 1;
							$month = date('n') + 1;
							$end_year = $month > 12 ? $end_year + 1 : $end_year;
							$month = $month > 12 ? 1 : $month;
							$start_day_of_month = '01';
							$start_date_obj = new DateTime(date('Y-m-'.$end_day_of_month));
							$end_date_obj = new DateTime(date($end_year.'-'.$month.'-'.$start_day_of_month));
						} else {
							$start_day_of_month -= 1;
							$end_day_of_month += 1;
							$month = date('n') - 1;
							$start_date_obj = new DateTime(date('Y-'.$month.'-'.$end_day_of_month));
							$end_date_obj = new DateTime(date('Y-m-'.$start_day_of_month));
						}
						if(get_config($dbc, 'timesheet_submit_mode') == 'auto') {
							$last_period = date('Y-m-d',strtotime($start_date_obj->format('Y-m-d').' - 1 day'));
						}
						if($current_period > 0) {
							$start_date_obj->add(new DateInterval('P'.floor($current_period / 2).'M'));
							$end_date_obj->add(new DateInterval('P'.floor($current_period / 2).'M'));
							if($current_period % 2 > 0) {
								$start_date_obj->sub(new DateInterval('P1D'));
								$start_date_obj->add(new DateInterval('P1M'));
								$end_date_obj->add(new DateInterval('P1D'));
								$new_start_obj = $end_date_obj;
								$end_date_obj = $start_date_obj;
								$start_date_obj = $new_start_obj;
							}
						} else if($current_period < 0) {
							$start_date_obj->sub(new DateInterval('P'.floor($current_period / 2 * -1).'M'));
							$end_date_obj->sub(new DateInterval('P'.floor($current_period / 2 * -1).'M'));
							if($current_period % 2 < 0) {
								$start_date_obj->sub(new DateInterval('P1D'));
								$end_date_obj->sub(new DateInterval('P1M'));
								$end_date_obj->add(new DateInterval('P1D'));
								$new_start_obj = $end_date_obj;
								$end_date_obj = $start_date_obj;
								$start_date_obj = $new_start_obj;
							}
						}
						$start_year = $start_date_obj->format('Y');
						$start_month = $start_date_obj->format('n');
						$start_day = $start_date_obj->format('j');
						$end_year = $end_date_obj->format('Y');
						$end_month = $end_date_obj->format('n');
						$end_day = $end_date_obj->format('j');
					} else {
						$start_day_of_month = date('j',strtotime($start));
						$today_day_of_month = date('j');
						$length_of_month = date('t');
						$half_of_month = floor($length_of_month / 2);
						if($today_day_of_month >= $start_day_of_month && $today_day_of_month < ($start_day_of_month + $half_of_month)) {
							$date_obj = new DateTime(date('Y-m-'.$start_day_of_month));
						} else if(($start_day_of_month + $half_of_month) > $length_of_month && $today_day_of_month < $start_day_of_month - $half_of_month) {
							$month = date('n') - 1;
							$year = date('Y');
							if($month < 1) {
								$year--;
								$month = 12;
							}
							$date_obj = new DateTime(date($year.'-'.$month.'-'.$start_day_of_month));
						} else {
							$start_day_of_month += $half_of_month;
							$month = date('n');
							$year = date('Y');
							if($start_day_of_month > $length_of_month) {
								$start_day_of_month -= $length_of_month;
							}
							$date_obj = new DateTime(date($year.'-'.$month.'-'.$start_day_of_month));
						}
						if(get_config($dbc, 'timesheet_submit_mode') == 'auto') {
							$last_period = date('Y-m-d',strtotime($date_obj->format('Y-m-d').' - 1 day'));
						}
						if($current_period > 0) {
							$date_obj->add(new DateInterval('P'.floor($current_period / 2).'M'.($current_period % 2 * $half_of_month).'D'));
						} else if($current_period < 0) {
							$date_obj->sub(new DateInterval('P'.floor($current_period / 2 * -1).'M'.($current_period % 2 * -1 * $half_of_month).'D'));
						}
						$start_year = $date_obj->format('Y');
						$start_month = $date_obj->format('n');
						$start_day = $date_obj->format('j');
						$date_obj->add(new DateInterval('P'.$half_of_month.'D'));
						$end_year = $date_obj->format('Y');
						$end_month = $date_obj->format('n');
						$end_day = $date_obj->format('j');
					}
					break;
				case 'Monthly':
				default:
					$start_date = new DateTime($start);
					$todays_date = new DateTime();
					if($start_date->format('j') < $todays_date->format('j')) {
						$start_date = new DateTime(date('Y-m-'.$start_date->format('d')));
					} else {
						$start_date = new DateTime(date('Y-m-'.$start_date->format('d')));
						$start_date->sub(new DateInterval('P1M'));
					}
					if(get_config($dbc, 'timesheet_submit_mode') == 'auto') {
						$last_period = date('Y-m-d',strtotime($start_date->format('Y-m-d').' - 1 day'));
					}
					if($current_period > 0) {
						$start_date->add(new DateInterval('P'.($current_period).'M'));
					} else if($current_period < 0) {
						$start_date->sub(new DateInterval('P'.($current_period * -1).'M'));
					}
					$start_year = $start_date->format('Y');
					$start_month = $start_date->format('n');
					$start_day = $start_date->format('j');
					$end_date = $start_date;
					$end_date->add(new DateInterval('P1M'))->sub(new DateInterval('P1D'));
					$end_year = $end_date->format('Y');
					$end_month = $end_date->format('n');
					$end_day = $end_date->format('j');
					break;
			}
			if($start_month < 1) {
				$start_month = 12;
				$start_year--;
			}
			if($end_month < 1) {
				$end_month = 12;
				$end_year--;
			}
			if(empty($_GET['search_start_date'])) {
				$search_start_date = date('Y-m-d',strtotime($start_year.'-'.$start_month.'-'.$start_day));
			}
			if(empty($_GET['search_end_date'])) {
				$search_end_date = date('Y-m-d',strtotime($end_year.'-'.$end_month.'-'.$end_day));
			}
		}
	break;
}