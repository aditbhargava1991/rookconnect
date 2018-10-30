<?php
require_once('../phpsign/signature-to-image.php');
$default_tab = !empty(get_config($dbc, 'timesheet_default_tab')) ? get_config($dbc, 'timesheet_default_tab') : 'Custom';
$_GET['tab'] = !empty($_GET['tab']) && FOLDER_NAME != 'daysheet' ? $_GET['tab'] : $default_tab;
switch($_GET['tab']) {
	case 'Daily':
		$pay_period_label = 'Day';
		break;
	case 'Weekly':
		$pay_period_label = 'Week';
		break;
	case 'Bi-Weekly':
		$pay_period_label = 'Two Weeks';
		break;
	case 'Monthly':
		$pay_period_label = 'Month';
		break;
	case 'Custom':
		$pay_period_label = 'Pay Period';
		break;
}

//Fix bugged out Time Cards where staff is 0 from a Ticket
$empty_time_cards = mysqli_query($dbc, "SELECT `time_cards`.*, `ticket_attached`.`item_id` FROM `time_cards` LEFT JOIN `ticket_attached` ON `time_cards`.`ticket_attached_id` = `ticket_attached`.`id` WHERE `time_cards`.`staff` = 0 AND `time_cards`.`ticket_attached_id` > 0 AND `ticket_attached`.`item_id` > 0 AND `ticket_attached`.`src_table` LIKE 'Staff%'");
while($empty_time_card = mysqli_fetch_assoc($empty_time_cards)) {
	mysqli_query($dbc, "UPDATE `time_cards` SET `staff` = '".$empty_time_card['item_id']."' WHERE `time_cards_id` = '".$empty_time_card['time_cards_id']."'");
}

//Insert any Time Sheet records for Staff that have Set Hours, and overwrite existing Regular Hours for those days with the Set Hours
include_once('../Calendar/calendar_functions_inc.php');
$today_date = date('Y-m-d');
$set_hours = mysqli_query($dbc, "SELECT * FROM `contacts_shifts` WHERE `set_hours` = 1 AND `deleted` = 0");
while($row = mysqli_fetch_assoc($set_hours)) {
	$staff_id = $row['contactid'];
	$start_date = $row['startdate'];
	$end_date = empty(str_replace('0000-00-00','',$row['enddate'])) ? '9999-12-31' : $row['enddate'];
	if($staff_id > 0 && !empty(str_replace('0000-00-00','',$start_date))) {
		mysqli_query($dbc, "UPDATE `time_cards` SET `deleted` = 1 WHERE `date` BETWEEN '$start_date' AND '$end_date' AND `staff` = '$staff_id' AND `type_of_time` NOT IN ('Extra Hrs.','Relief Hrs.','Sleep Hrs.','Sick Time Adj.','Sick Hrs.Taken','Stat Hrs.','Stat Hrs.Taken','Vac Hrs.','Vac Hrs.Taken','Break') AND `shiftid` = 0");
		for($cur_day = $start_date; strtotime($cur_day) <= strtotime($today_date); $cur_day = date('Y-m-d', strtotime($cur_day.' + 1 day'))) {
			$day_of_week = date('l', strtotime($cur_day));
			$shifts = checkShiftIntervals($dbc, $staff_id, $day_of_week, $cur_day, '', '', ' AND `set_hours` = 1');
			foreach($shifts as $shift) {
				$shiftid = $shift['shiftid'];
				$total_hrs = ((strtotime($shift['endtime']) - strtotime($shift['starttime']))/3600);
				mysqli_query($dbc, "INSERT INTO `time_cards` (`staff`, `date`, `shiftid`, `type_of_time`, `total_hrs`, `comment_box`) SELECT '$staff_id', '$cur_day', '$shiftid', 'Regular Hrs.', '$total_hrs', 'Added from Set Hours.' FROM (SELECT COUNT(*) `rows` FROM `time_cards` WHERE `staff` = '$staff_id' AND `date` = '$cur_day' AND `shiftid` = '$shiftid') `num` WHERE `num`.`rows` = 0");
			}
		}
	}
}

// error_reporting(0);

global $config;

$config['tile_name'] = 'Timesheet';

function config_visible_function_custom($dbc)
{
	//return (config_visible_function($dbc, 'timesheet') == 1);
	return true;
}

function vuaed_visible_function_custom($dbc)
{
	//return (vuaed_visible_function($dbc, 'timesheet') == 1);
	return true;
}

$config['tabs'] = [ 'Time Sheets' => array('Daily' => 'time_cards.php?tab=Daily', 'Weekly' => 'time_cards.php?tab=Weekly', 'Bi-Weekly' => 'time_cards.php?tab=Bi-Weekly', 'Monthly' => 'time_cards.php?tab=Monthly', 'Custom' => 'time_cards.php?tab=Custom'),
	'Pay Period' => array('Custom' => 'pay_period.php', 'Last Month' => 'pay_period_last_month.php', 'Current Month' => 'pay_period_current_month.php'),
	'Holidays' => array('Holidays'=>'holidays.php', 'Stat Pay Calculation'=>'stat_pay_calculation.php'),
	'Coordinator Approvals' => array('Daily' => 'time_card_approvals_coordinator.php?tab=Daily', 'Weekly' => 'time_card_approvals_coordinator.php?tab=Weekly', 'Bi-Weekly' => 'time_card_approvals_coordinator.php?tab=Bi-Weekly', 'Monthly' => 'time_card_approvals_coordinator.php?tab=Monthly', 'Custom' => 'time_card_approvals_coordinator.php?tab=Custom'),
	'Manager Approvals' => array('Daily' => 'time_card_approvals_manager.php?tab=Daily', 'Weekly' => 'time_card_approvals_manager.php?tab=Weekly', 'Bi-Weekly' => 'time_card_approvals_manager.php?tab=Bi-Weekly', 'Monthly' => 'time_card_approvals_manager.php?tab=Monthly', 'Custom' => 'time_card_approvals_manager.php?tab=Custom'),
	'Reporting' => array('Daily' => 'reporting.php?tab=Daily', 'Weekly' => 'reporting.php?tab=Weekly', 'Bi-Weekly' => 'reporting.php?tab=Bi-Weekly', 'Monthly' => 'reporting.php?tab=Monthly', 'Custom' => 'reporting.php?tab=Custom'),
	'Payroll' => array('Daily' => 'payroll.php?tab=Daily', 'Weekly' => 'payroll.php?tab=Weekly', 'Bi-Weekly' => 'payroll.php?tab=Bi-Weekly', 'Monthly' => 'payroll.php?tab=Monthly', 'Custom' => 'payroll.php?tab=Custom') ];

$config['time_tabs'] = ['Daily','Weekly','Bi-Weekly','Monthly','Custom'];

$timesheet_tabs = explode(',',get_config($dbc, 'timesheet_tabs'));
$ordered_tabs = [];
foreach($timesheet_tabs as $timesheet_tab) {
	if(!empty($timesheet_tab)) {
		$ordered_tabs[$timesheet_tab] = $config['tabs'][$timesheet_tab];
		unset($config['tabs'][$timesheet_tab]);
	}
}
foreach($config['tabs'] as $key => $timesheet_tab) {
	$ordered_tabs[$key] = $timesheet_tab;
}
$config['tabs'] = $ordered_tabs;

$config['hours_types'] = ['REG_HRS','DIRECT_HRS','INDIRECT_HRS','EXTRA_HRS','RELIEF_HRS','SLEEP_HRS','SICK_ADJ','SICK_HRS','STAT_AVAIL','STAT_HRS','VACA_AVAIL','VACA_HRS','TRACKED_HRS','BREAKS'];

$timesheet_start_tile = get_config($dbc, 'timesheet_start_tile');

$config['pdf_options'] = [
	'schedule' => 'Schedule',
	'scheduled' => 'Scheduled Hours',
	'view_ticket' => 'View '.TICKET_NOUN,
	'ticketid' => TICKET_NOUN,
	'show_hours' => 'Hours',
	'total_tracked_hrs' => 'Total Tracked Hours',
	'start_time' => 'Start Time',
	'end_time' => 'End Time',
	'start_time_editable' => 'Start Time',
	'end_time_editable' => 'End Time',
	'planned_hrs' => 'Planned Hours',
	'tracked_hrs' => 'Tracked Hours',
	'total_tracked_time' => 'Total Tracked Time',
	'total_tracked_hrs' => 'Time Tracked',
	'reg_hrs' => 'Regular Hours',
	'payable_hrs' => 'Payable Hours',
	'start_day_tile_separate' => $timesheet_start_tile,
	'direct_hrs' => 'Direct Hours',
	'indirect_hrs' => 'Indirect Hours',
	'extra_hrs' => 'Extra Hours',
	'relief_hrs' => 'Relief Hours',
	'sleep_hrs' => 'Sleep Hours',
	'training_hrs' => 'Training Hours',
	'sick_hrs' => 'Sick Time Adjustment',
	'sick_used' => 'Sick Hours Taken',
	'stat_hrs' => 'Stat Hours',
	'stat_used' => 'Stat Hours Taken',
	'vaca_hrs' => 'Vacation Hours',
	'vaca_used' => 'Vacation Hours Taken',
	'breaks' => 'Breaks',
	'comment_box' => 'Comments',
	'signature' => 'Parent/Guardian Signature',
	'total_per_day' => 'Day Totals'
];

/* Time Cards */
$config['settings']['Choose Fields for Time Sheets']['config_field'] = 'time_cards';
$config['settings']['Choose Fields for Time Sheets']['data'] = array(
	'General' => array(
			array('Editable Dates', 'hidden', 'editable_dates'),
			array('Schedule', 'hidden', 'schedule'),
			array('Scheduled Hours', 'hidden', 'scheduled'),
			array(TICKET_NOUN, 'dropdown', 'ticketid'),
			array('Show Hours', 'hidden', 'show_hours'),
			array('Total Tracked Hrs', 'hidden', 'total_tracked_hrs'),
			array('Start Time (Editable)', 'time', 'start_time_editable'),
			array('Start Time', 'time', 'start_time'),
			array('End Time (Editable)', 'time', 'end_time_editable'),
			array('End Time', 'time', 'end_time'),
			array('Planned Hours ('.TICKET_TILE.')', 'hidden', 'planned_hrs'),
			array('Tracked Hours ('.TICKET_TILE.')', 'hidden', 'tracked_hrs'),
			array('Total Tracked Time ('.TICKET_TILE.')', 'hidden', 'total_tracked_time'),
			array($timesheet_start_tile, 'time', 'start_day_tile'),
			array(TICKET_NOUN.' Dropdown', 'hidden', 'ticket_select'),
			array('Task Dropdown', 'hidden', 'task_select'),
			array('Position Drop Down', 'hidden', 'position_select'),
			array('Task Tracked Hrs', 'hidden', 'total_tracked_hrs_task'),
			array('Total Hours', 'hidden', 'total_hrs'),
			array('Regular Hours', 'hidden', 'reg_hrs'),
			array('Payable Hours', 'hidden', 'payable_hrs'),
			array($timesheet_start_tile.' - Separate Hours', 'time', 'start_day_tile_separate'),
			array('Direct Hours', 'hidden', 'direct_hrs'),
			array('Indirect Hours', 'hidden', 'indirect_hrs'),
			array('Extra Hours', 'hidden', 'extra_hrs'),
			array('Relief Hours', 'hidden', 'relief_hrs'),
			array('Sleep Hours', 'hidden', 'sleep_hrs'),
			array('Training Hours', 'hidden', 'training_hrs'),
			array('Sick Time', 'hidden', 'sick_hrs'),
			array('Sick Taken', 'hidden', 'sick_used'),
			array('Sick Taken - Check Conflicts', 'hidden', 'sick_used_conflicts'),
			array('Stat Hours', 'hidden', 'stat_hrs'),
			array('Stat Taken', 'hidden', 'stat_used'),
			array('Vacation Hrs', 'hidden', 'vaca_hrs'),
			array('Vacation Taken', 'hidden', 'vaca_used'),
			array('Breaks', 'hidden', 'breaks'),
			array('View '.TICKET_NOUN, 'hidden', 'view_ticket'),
			array('Hourly Compensation', 'hidden', 'compensation'),
			array('Comments', 'hidden', 'comment_box'),
			array('Parent/Guardian Signature', 'signature', 'signature'),

			array('Day Totals', 'hidden', 'total_per_day'),
			array('Calculate Hours On Start/End Time Change', 'hidden', 'calculate_hours_start_end'),
			array('Show Time Overlaps', 'hidden', 'time_overlaps'),
			array('Approve All Checkbox', 'hidden', 'approve_all'),

			array(PROJECT_NOUN, 'dropdown', 'project'),
			array('Search by Client', 'dropdown', 'search_client'),

			array('Business', 'dropdown', 'business'),
			array('Staff', 'dropdown', 'staff'),

			array('Customer', 'dropdown', 'customer'),
			array('Search by Staff', 'dropdown', 'search_staff'),
			array('Position Drop Down', 'hidden', 'position'),
			array('Type of Time', 'dropdown', 'type_of_time'),
			array('Payment', 'text', 'payment'),
			array('Confirmation', 'text', 'confirm'),
			array('Frequency', 'text', 'frequency'),
			array('Time Slot', 'text', 'time_slot'),
			array('Cell Phone Carrier', 'dropdown', 'contact_staff'),
			array('Timeframe', 'text', 'timeframe'),
			array('Date', 'date', 'date'),
			array('Hide Signature on PDF', '', 'signature_pdf_hidden'),
			array('Combine Staff on Report', 'tab', 'staff_combine'),
			array('Payable Hours Updates '.TICKET_NOUN, 'hidden', 'update_ticket_payable'),
		)
);

$config['settings']['Choose Fields for Time Sheets Dashboard']['config_field'] = 'time_cards_dashboard';
$config['settings']['Choose Fields for Time Sheets Dashboard']['data'] = array(
	'General' => array(
			array('Select User Groups', 'dropdown', 'search_by_groups'),
			array('Select Security Levels', 'dropdown', 'search_by_security'),
			array('Select Positions', 'dropdown', 'search_by_position'),
			array('Search by '.PROJECT_NOUN, 'dropdown', 'search_by_project'),
			array('Search by '.TICKET_NOUN, 'dropdown', 'search_by_ticket'),
			array('Business', 'dropdown', 'business'),
			array('Staff', 'dropdown', 'staff'),
			array('Date', 'date', 'date'),
			array('Start Time', 'time', 'start_time'),
			array('End Time', 'time', 'end_time'),
			array('Type of Time', 'dropdown', 'type_of_time'),
			array('Total Hrs', 'hours', 'total_hrs'),
			array('Daily Tab', 'tab', 'Daily'),
			array('Weekly Tab', 'tab', 'Weekly'),
			array('Bi-Weekly Tab', 'tab', 'Bi-Weekly'),
			array('Monthly Tab', 'tab', 'Monthly'),
			array('Custom Tab', 'tab', 'Custom'),
		)
);

/* Time Card Reporting - Just Use the Settings from Time Sheet */
/*$config['settings']['Choose Fields for Time Sheets Reporting Dashboard']['config_field'] = 'time_cards_reporting_dashboard';
$config['settings']['Choose Fields for Time Sheets Reporting Dashboard']['data'] = array(
	'General' => array(
			array('Business', 'dropdown', 'business'),
			array('Staff', 'dropdown', 'staff'),
			array('Manager Name', 'text', 'manager_name'),
			array('Coordinator Name', 'dropdown', 'coordinator_name'),
			array('Date', 'date', 'date'),
			array('Start Time', 'time', 'start_time'),
			array('End Time', 'time', 'end_time'),
			array('Type of Time', 'dropdown', 'type_of_time'),
			array('Total Hrs', 'hours', 'total_hrs'),
			array('Search by Groups', '', 'search_by_groups'),
			array('Search by Security Level', '', 'search_by_security'),
			array('Search by Position', '', 'search_by_position'),
			array('Search by '.PROJECT_NOUN, '', 'search_by_project'),
			array('Search by '.TICKET_NOUN, '', 'search_by_ticket'),

		)
);*/

/* Time Card Approvals - Just Use the Settings from Time Sheet */
/*$config['settings']['Choose Fields for Time Sheets Approvals Dashboard']['config_field'] = 'time_cards_approvals_dashboard';
$config['settings']['Choose Fields for Time Sheets Approvals Dashboard']['data'] = array(
	'General' => array(
			array('Business', 'dropdown', 'business'),
			array('Staff', 'dropdown', 'staff'),
			array('Date', 'date', 'date'),
			array('Start Time', 'time', 'start_time'),
			array('End Time', 'time', 'end_time'),
			array('Type of Time', 'dropdown', 'type_of_time'),
			array('Total Hrs', 'hours', 'total_hrs'),
		)
);

$config['settings']['Choose Fields for Time Sheets Approvals']['config_field'] = 'time_cards_approvals';
$config['settings']['Choose Fields for Time Sheets Approvals']['data'] = array(
	'General' => array(
			array('Comment Box', 'textarea', 'comment_box'),
			array('Manager Name', 'text', 'manager_name'),
			array('Date', 'date', 'date_manager'),
			array('Manager Signature Box', 'sign', 'manager_signature'),
			array('Coordinator Name', 'dropdown', 'coordinator_name'),
			array('Date', 'date', 'date_coordinator'),
			array('Coordinator Signature', 'sign2', 'coordinator_signature'),
			array('Approved', 'hidden', 'approv'),
			array('Total Tracked Hrs', 'hidden', 'total_tracked_hrs'),
			array('Regular Hours', 'hidden', 'reg_hrs'),
			array('Direct Hours', 'hidden', 'direct_hrs'),
			array('Indirect Hours', 'hidden', 'indirect_hrs'),
			array('Extra Hours', 'hidden', 'extra_hrs'),
			array('Relief Hours', 'hidden', 'relief_hrs'),
			array('Sleep Hours', 'hidden', 'sleep_hrs'),
			array('Sick Time', 'hidden', 'sick_hrs'),
			array('Sick Taken', 'hidden', 'sick_used'),
			array('Stat Hours', 'hidden', 'stat_hrs'),
			array('Stat Taken', 'hidden', 'stat_used'),
			array('Vacation Hrs', 'hidden', 'vaca_hrs'),
			array('Vacation Taken', 'hidden', 'vaca_used'),
		)
);*/

/* Pay Period */
$config['settings']['Choose Fields for Pay Period']['config_field'] = 'pay_period';
$config['settings']['Choose Fields for Pay Period']['data'] = array(
	'General' => array(
			array('Staff', 'multiselect', 'staff'),
			//array('Staff Group', 'text', 'staff_group'),
			array('All Staff', 'checkbox', 'all_staff'),
			array('Pay Period', 'dropdown', 'pay_period'),
			array('Start Date of First Pay Period', 'date', 'start_date'),
            array('End Date of First Pay Period', 'date', 'end_date_period'),
			array('End Date', 'date', 'end_date'),
		)
);

$config['settings']['Choose Fields for Pay Period Dashboard']['config_field'] = 'pay_period_dashboard';
$config['settings']['Choose Fields for Pay Period Dashboard']['data'] = array(
	'General' => array(
			array('Staff', 'dropdown', 'staff'),
			//array('Staff Group', 'text', 'staff_group'),
			array('All Staff', 'checkbox', 'all_staff'),
			array('Pay Period', 'dropdown', 'pay_period'),
			array('Start Date', 'date', 'start_date'),
            array('End Date of First Pay Period', 'date', 'end_date_period'),
			array('End Date', 'date', 'end_date'),
			array('Last Month Tab', 'tab', 'pay_period_last_month.php'),
			array('Current Month Tab', 'tab', 'pay_period_current_month.php'),
		)
);

$config['settings']['Choose Fields for Total Hours Tracked Layout']['config_field'] = 'time_cards_total_hrs_layout';
$config['settings']['Choose Fields for Total Hours Tracked Layout']['data'] = array(
	'General' => array(
			array('View '.TICKET_NOUN, 'hidden', 'view_ticket'),
			array('Start Time', 'time', 'start_time'),
			array('End Time', 'time', 'end_time'),
			array('Total Tracked Hrs', 'hidden', 'total_tracked_hrs'),
			array('Planned Hours ('.TICKET_TILE.')', 'hidden', 'planned_hrs'),
			array('Tracked Hours ('.TICKET_TILE.')', 'hidden', 'tracked_hrs'),
			array('Total Tracked Time ('.TICKET_TILE.')', 'hidden', 'total_tracked_time'),
			array('Payable Hours', 'hidden', 'payable_hrs'),
			array('Regular Hours', 'hidden', 'reg_hrs'),
			array($timesheet_start_tile.' - Separate Hours', 'time', 'start_day_tile_separate'),
			array('Over Time Hours', 'hidden', 'overtime_hrs'),
			array('Double Time Hours', 'hidden', 'doubletime_hrs'),
			array('Direct Hours', 'hidden', 'direct_hrs'),
			array('Indirect Hours', 'hidden', 'indirect_hrs'),
			array('Extra Hours', 'hidden', 'extra_hrs'),
			array('Relief Hours', 'hidden', 'relief_hrs'),
			array('Sleep Hours', 'hidden', 'sleep_hrs'),
			array('Training Hours', 'hidden', 'training_hrs'),
			array('Sick Time', 'hidden', 'sick_hrs'),
			array('Sick Taken', 'hidden', 'sick_used'),
			array('Stat Hours', 'hidden', 'stat_hrs'),
			array('Stat Taken', 'hidden', 'stat_used'),
			array('Breaks', 'hidden', 'breaks'),
			array('Vacation Hrs', 'hidden', 'vaca_hrs'),
			array('Vacation Taken', 'hidden', 'vaca_used'),
			array('Comments', 'hidden', 'comment_box'),
			array('Combine Staff on Report', 'tab', 'staff_combine'),
			array('Day Totals', 'hidden', 'total_per_day')
		)
);

/* Holidays */
$config['settings']['Choose Fields for Holidays']['config_field'] = 'holidays';
$config['settings']['Choose Fields for Holidays']['data'] = array(
	'General' => array(
			array('Name', 'text', 'name'),
			array('Date', 'date', 'date'),
			array('Paid', 'checkbox', 'paid'),
		)
);

$config['settings']['Choose Fields for Holidays Dashboard']['config_field'] = 'holidays_dashboard';
$config['settings']['Choose Fields for Holidays Dashboard']['data'] = array(
	'General' => array(
			array('Name', 'text', 'name'),
			array('Date', 'date', 'date'),
			array('Paid', 'checkbox', 'paid'),
			array('Holidays', 'hidden', 'Holidays'),
			array('Stat Pay Calculation', 'hidden', 'Stat Pay Calculation')
		)
);

function get_tabs($tab = '', $subtab = '', $custom = array())
{
	global $config;
	global $dbc;
	$default_tab = !empty(get_config($dbc, 'timesheet_default_tab')) ? get_config($dbc, 'timesheet_default_tab') : 'Custom';
	$approvals = approval_visible_function($dbc, 'timesheet');
	$timesheet_manager_approvals = !empty(get_config($dbc, 'timesheet_manager_approvals')) ? get_config($dbc, 'timesheet_manager_approvals') : 'Manager Approvals';

	$html = '';
	$subhtml = '';
	$tab_config = get_config($dbc, 'timesheet_tabs');
	foreach($config['tabs'] as $title => $contents) {
		if(strpos(','.$tab_config.',',','.$title.',') !== FALSE && ($approvals > 0 || $title == 'Time Sheets' || $title == 'Pay Period' || $title == 'Holidays')) {
			$title_subtab = str_replace ( ['&', '/', ',', ' ', '___', '__'], ['', '_', '', '_', '_', '_'], $title );
            if(is_array($contents)) {
				$active = '';
				if($title == $tab) {
					$active = 'active_tab';
				}

				if($title == 'Holidays') {
					$url = $contents['Holidays'];
				} else {
					$url = !empty($contents[$default_tab]) ? $contents[$default_tab] : $contents['Custom'];
				}

                if ( check_subtab_persmission($dbc, 'timesheet', ROLE, $title_subtab) === true ) {
                    $html .= "<a href='".$url."'><button type='button' class='btn brand-btn mobile-block ".$active."' >".($title == 'Manager Approvals' ? $timesheet_manager_approvals : ($title == 'Holidays' ? 'Stat Holidays' : $title))."</button></a>";
                } else {
                    $html .= "<button type='button' class='btn disabled-btn mobile-block'>".($title == 'Manager Approvals' ? $timesheet_manager_approvals : ($title == 'Holidays' ? 'Stat Holidays' : $title))."</button>&nbsp;";
                }

				if($title == $tab) {
					foreach($contents as $subtitle => $content) {
						$subactive = '';
						if($subtitle == $subtab) {
							$subactive = 'active_tab';
						}

						$get_field_config = mysqli_fetch_assoc(mysqli_query($custom['db'],"SELECT ".$custom['field']." FROM field_config"));
						$value_config = ','.$get_field_config[$custom['field']].',';

						if (strpos($value_config, ','.$content.',') !== FALSE || strpos($value_config, ','.$subtitle.',') !== FALSE) {
							$subhtml .= "<a href='".$content."'><button type='button' class='btn brand-btn mobile-block ".$subactive."' >".$subtitle."</button></a>";
						}
					}
				}
			} else {
				$active = '';
				if($title == $tab) {
					$active = 'active_tab';
				}

                if ( check_subtab_persmission($dbc, 'timesheet', ROLE, $title_subtab) === true ) {
                    $html .= "<a href='".$contents."'><button type='button' class='btn brand-btn mobile-block ".$active."' >".($title == 'Manager Approvals' ? $timesheet_manager_approvals : ($title == 'Holidays' ? 'Stat Holidays' : $title))."</button></a>";
                } else {
                    $html .= "<button type='button' class='btn disabled-btn mobile-block'>".($title == 'Manager Approvals' ? $timesheet_manager_approvals : ($title == 'Holidays' ? 'Stat Holidays' : $title))."</button>&nbsp;";
                }
			}
		}
	}
	return '<div>'.$html.'</div><br><div>'.$subhtml.'</div>';
}

function get_all_inputs($data) {
	global $config;
	$fields = array();

	if(isset($data)) {
		foreach($data as $tabs) {
			foreach($tabs as $field) {
				$fields[] = $field[2];

			}
		}
	}
	return $fields;
}

function get_post_inputs($data) {
	global $config;
	$fields = array();
	foreach($data as $tabs) {
		foreach($tabs as $field) {
			if($field[1] == 'upload') {
				$fields[$field[2]] = $_FILES[$field[2]]["name"];
				if($fields[$field[2]] == '') {
					if(isset($_POST[$field[2].'_hidden'])) {
						$fields[$field[2]] = $_POST[$field[2].'_hidden'];
					}
				}
			} else if($field[1] == 'multiselect') {
				$fields[$field[2]] = implode(',',$_POST[$field[2]]);
			} else {
				$fields[$field[2]] = filter_var(htmlentities($_POST[$field[2]], FILTER_SANITIZE_STRING));
			}
		}
	}
	return $fields;
}

function get_post_uploads($data) {
	global $config;
	$fields = array();

	foreach($data as $tabs) {
		foreach($tabs as $field) {
			if($field[1] == 'upload') {
				$fields[$field[2]] = $_FILES[$field[2]]["name"];
			}
		}
	}
	return $fields;
}

function move_files($files) {
	foreach($files as $file => $name) {
		move_uploaded_file($_FILES[$file]["tmp_name"], "download/". $_FILES[$file]["name"]);
	}
}

function prepare_insert($ins_data = array(), $table = '') {
	$columns = $values = [];
	foreach($_POST as $field => $value) {
		if(in_array($field, ['time_cards_id','business','projectid','ticketid','agendameetingid','staff','contact_staff','date','start_time','end_time','type_of_time','timeframe','time_slot','frequency','confirm','payment','timer_start','total_hrs','timer_tracked','highlight','manager_highlight','comment_box','manager_name','date_manager','manager_signature','coordinator_name','date_coordinator','coordinator_signature','approv','location','customer','day','shift_tracked','day_tracking_type','created_by','clientid','deleted','name','paid'])) {
			$columns[] = "`$field`";
			$values[] = "'".filter_var($value,FILTER_SANITIZE_STRING)."'";
		}
	}
	$sql = "INSERT INTO `".$table."` (".implode(',',$columns).") VALUES (".implode(',',$values).")";
	return $sql;
}

function prepare_update($up_data = array(), $table = '', $key = '', $value = '') {
	$fields = array();
	foreach($up_data as $field => $val) {
		$fields[] = "$field = '$val'";
	}
	if(is_array($value)) {
		$value = implode(',', $value);
		$sql = "UPDATE ".$table." SET " . join(', ', $fields) . " WHERE ".$key." IN (".$value.")";
	} else {
		$sql = "UPDATE ".$table." SET " . join(', ', $fields) . " WHERE ".$key." = '".$value."'";
	}
	return $sql;
}

function get_field($field, $value, $dbc = '')
{
	if($dbc == '') {
		$dbc = $_SERVER['DBC'];
	}
	$html = '';

   	if($field[2] == 'staff' && $field[1] == 'multiselect') {
        return call_staff($dbc, $field[2], $value, 'multi-staff');
    } else if($field[2] == 'staff') {
        return call_staff($dbc, $field[2], $value, 'staff');
    } else if($field[2] == 'contact_staff') {
        return call_staff($dbc, $field[2], $value, 'contact_staff');
    } else if($field[2] == 'customer') {
   		return call_staff($dbc, $field[2], $value, 'customer');
    } else if($field[2] == 'coordinator_name') {
   		return call_staff($dbc, $field[2], $value, 'staff');
   	} else if($field[2] == 'business' && get_software_name() == 'breakthebarrier') {
   		return call_staff($dbc, $field[2], $value, 'business');
   	} else if($field[2] == 'project') {
   		return call_projects($dbc, $field[2], $value);
   	} else if($field[2] == 'ticketid') {
   		return call_tickets($dbc, $field[2], $value);
    } else if($field[2] =='manager_name') {
        $html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">
                      <input name="'.$field[2].'" id="'.$field[2].'" type="text" class="form-control" value="'.decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).'">
                    </div>
                  </div>';

   	} else if($field[1] == 'text') {
		$html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">
                      <input name="'.$field[2].'" id="'.$field[2].'" class="form-control" value="'.$value.'">
                    </div>
                  </div>';

   	} else if($field[1] == 'checkbox') {
		if($field[2] == 'all_staff') {
			$html .= "<script>
			function toggle_all_staff() {
				if(this.checked) {
					$('[name=\"staff[]\"]').attr('disabled','true').trigger('change.select2');
				} else {
					$('[name=\"staff[]\"]').removeAttr('disabled').trigger('change.select2');
				}
			}
			$(document).ready(function() {
				$('[name=all_staff]').change(toggle_all_staff).trigger('change');
			});
			</script>";
		}
		$html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">
                      <input type="checkbox" name="'.$field[2].'" id="'.$field[2].'" '.($value == 1 ? 'checked' : '').' class="form-control1" style="height: 20px;width: 20px;" value="1">
                    </div>
                  </div>';
   	} else if($field[1] == 'sign') {
		$html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">';
                    include ('../phpsign/sign.php');
                    $html .= '</div>
                  </div>';
    } else if($field[1] == 'sign2') {
		$html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">';
                    include ('../phpsign/sign2.php');
                    $html .= '</div>
                  </div>';
    } else if($field[2] == 'approv') {
        $html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label"></label>
                    <div class="col-sm-8">
                      <input name="'.$field[2].'" id="'.$field[2].'" type="hidden" class="form-control" value="Y">
                    </div>
                  </div>';
    }
   	else if($field[1] == 'time') {
        $html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">
                      <input name="'.$field[2].'" id="'.$field[2].'" type="text" class="form-control datetimepicker"" value="'.$value.'">
                    </div>
                  </div>';
   	} else if($field[1] == 'hours') {
        $html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">
                      <input name="'.$field[2].'" id="'.$field[2].'" type="text" class="form-control timepicker" value="'.$value.'">
                    </div>
                  </div>';
    } else if($field[2] == 'pay_period') {
   		$options_array = array(
   				'Weekly',
   				'Bi-Weekly',
   				'Semi-Monthly',
   				'Monthly',
                'Custom',
   			);

   		$options_html = '<option value=""></option>';

   		foreach($options_array as $option_array) {
   			$selected = '';
   			if($option_array == $value) {
   				$selected = 'selected';
   			}
   			$options_html .= '<option value="'.$option_array.'" '.$selected.'>'.$option_array.'</option>';
   		}

		$html .= '<div class="form-group">
            <label for="pay_period" class="col-sm-4 control-label">Pay Period:</label>
            <div class="col-sm-8">
              <select id="pay_period" name="pay_period" class="chosen-select-deselect form-control" width="380">
                '.$options_html.'
              </select>
            </div>
          </div>';
   	} else if($field[2] == 'type_of_time') {

   		$options_array = array(
   				'Regular Hrs.',
   				'Extra Hrs.',
   				'Relief Hrs.',
   				'Sleep Hrs.',
   				'Sick Time Adj.',
   				'Sick Hrs.Taken',
   				'Stat Hrs.',
   				'Stat Hrs.Taken',
   				'Vac Hrs.',
   				'Vac Hrs.Taken'
   			);

   		$options_html = '<option value=""></option>';

   		foreach($options_array as $option_array) {
   			$selected = '';
   			if($option_array == $value) {
   				$selected = 'selected';
   			}
   			$options_html .= '<option value="'.$option_array.'" '.$selected.'>'.$option_array.'</option>';
   		}

		$html .= '<div class="form-group">
            <label for="type_of_time" class="col-sm-4 control-label">Type Of Time:</label>
            <div class="col-sm-8">
              <select id="type_of_time" name="type_of_time" class="chosen-select-deselect form-control" width="380">
                '.$options_html.'
              </select>
            </div>
          </div>';
    } else if($field[1] == 'date') {
		if($field[2] == 'end_date') {
			$html .= "<script>
			function toggle_end_date() {
				if($('[name=pay_period]').val() != undefined && $('[name=pay_period]').val() != 'Custom') {
					$('[name=end_date]').closest('.form-group').hide();
				} else {
					$('[name=end_date]').closest('.form-group').show();
				}
			}
			$(document).ready(function() {
				toggle_end_date();
				$('[name=pay_period]').change(toggle_end_date);
			});
			</script>";
		}
		$html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">
                      <input class="datepicker form-control" name="'.$field[2].'" id="'.$field[2].'" value="'.$value.'">
                    </div>
                  </div>';
    } else if($field[1] == 'textarea') {
		$html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">
                      <textarea name="'.$field[2].'" rows="5" cols="50" class="form-control">'.$value.'</textarea>
                    </div>
                  </div>';
    }
	return $html;
}

function call_staff($dbc, $field, $value, $type = '') {

	if($type == 'client') {
		$title = 'Client';
		$category = 'Clients';
	} else if($type == 'customer') {
		$title = 'Customer';
		$category = 'Staff';
	} else if($type == 'contact_staff') {
		$title = 'Cell Phone Carrier';
		$category = 'Staff';
	} else if($type == 'staff') {
		$title = 'Staff';
		$category = 'Staff';
	} else if($type == 'multi-staff') {
		$title = 'Staff';
		$category = 'Staff';
		$field = 'staff[]';
	} else if($type == 'business') {
		$title = BUSINESS_CAT;
		$category = BUSINESS_CAT;
	}

	$html = '';
	$html .= '<div class="form-group">';
	$html .= '<label for="client" class="col-sm-4 control-label">'.$title.':</label>';
	$html .= '<div class="col-sm-8">';
	$html .= '<select class="chosen-select-deselect form-control" data-placeholder="Select a '.$title.'..." name="'.$field.'" '.($type == 'multi-staff' ? 'multiple' : '').' id="'.$field.'">';
	$html .= '<option value=""></option>';

	$query = mysqli_query($dbc,"SELECT contactid, name, first_name, last_name FROM contacts WHERE category ".($type == 'customer' ? '!=' : '=')." '".$category."' order by name");

	while($row = mysqli_fetch_array($query)) {
		$selected = '';
		if (strpos(','.$value.',', ','.$row['contactid'].',') !== FALSE) {
			$selected = 'selected';
		}

        if(decryptIt($row['name']) != '') {
            $html .= '<option '.$selected.' value="'.$row['contactid'].'">'.decryptIt($row['name']).'</option>';
        } else {
            $html .= '<option '.$selected.' value="'.$row['contactid'].'">'.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
        }
    }

	$html .= '</select>';
	$html .= '</div>';
	$html .= '</div>';
	return $html;
}

function call_projects($dbc, $field, $value) {

	$html = '<div class="form-group"><label for="client" class="col-sm-4 control-label">'.PROJECT_NOUN.':</label>';
	$html .= '<div class="col-sm-8"><select class="chosen-select-deselect form-control" data-placeholder="Select '.PROJECT_NOUN.'" name="projectid"><option />';

	$query = $dbc->query("SELECT * FROM `project` WHERE `deleted`=0 ORDER BY `project_name`");
	while($row = $query->fetch_assoc()) {
		$html .= '<option '.($value == $row['projectid'] ? 'selected' : '').' value="'.$row['projectid'].'">'.get_project_label($dbc, $row).'</option>';
	}

	$html .= '</select></div></div>';
	return $html;
}

function call_tickets($dbc, $field, $value) {

	$html = '<div class="form-group"><label for="client" class="col-sm-4 control-label">'.TICKET_NOUN.':</label>';
	$html .= '<div class="col-sm-8"><select class="chosen-select-deselect form-control" data-placeholder="Select '.TICKET_NOUN.'" name="ticketid"><option />';

	if($_GET['projectid'] > 0) {
		$project_clause = "AND `projectid`='{$_GET['projectid']}'";
	}
	$query = $dbc->query("SELECT * FROM `tickets` WHERE `deleted`=0 $project_clause ORDER BY `ticketid` DESC");
	while($row = $query->fetch_assoc()) {
		$html .= '<option '.($value == $row['ticketid'] ? 'selected' : '').' value="'.$row['ticketid'].'">'.get_ticket_label($dbc, $row).'</option>';
	}

	$html .= '</select></div></div>';
	return $html;
}

function get_time_sheet($start_date = '', $end_date = '', $limits = '', $group = ", `staff`, `date`") {
	// Assign Default Limits
	$start_date = filter_var($start_date,FILTER_SANITIZE_STRING) ?: date('Y-m-01');
	$end_date = filter_var($end_date,FILTER_SANITIZE_STRING) ?: date('Y-m-t');
	$limits = $limits ?: "AND `staff`='{$_SESSION['contactid']}' AND `approv`='N'";

	// Add Checklist Time and Email Time to Time Sheet
	$_SERVER['DBC']->query("INSERT INTO `time_cards` (`checklistnameid`, `total_hrs`, `date`, `staff`, `type_of_time`, `comment_box`, `business`) SELECT `checklistnameid`, TIME_TO_SEC(`work_time`) / 3600, `timer_date`, `checklist_name_time`.`contactid`, 'Regular Hrs.', CONCAT('Checklist Item #',`checklistnameid`), `businessid` FROM `checklist_name_time` LEFT JOIN `checklist_name` ON `checklist_name_time`.`checklist_id`=`checklist_name`.`checklistnameid` LEFT JOIN `checklist` ON `checklist_name`.`checklistid`=`checklist`.`checklistid` WHERE `checklist_time_id` NOT IN (SELECT `checklist_time_id` FROM `checklist_name_time` LEFT JOIN `time_cards` ON `checklist_name_time`.`contactid`=`time_cards`.`staff` AND `checklist_name_time`.`checklist_id`=`time_cards`.`checklistnameid` AND `checklist_name_time`.`timer_date`=`time_cards`.`date` WHERE `time_cards`.`deleted`=0)");
	$_SERVER['DBC']->query("INSERT INTO `time_cards` (`email_communicationid`, `total_hrs`, `date`, `staff`, `type_of_time`, `comment_box`, `business`) SELECT `communication_id`, TIME_TO_SEC(`timer`) / 3600, `email_communication_timer`.`created_date`, `email_communication_timer`.`created_by`, 'Regular Hrs.', CONCAT('Email Communication: ',`subject`), `businessid` FROM `email_communication_timer` LEFT JOIN `email_communication` ON `email_communication_timer`.`communication_id`=`email_communication`.`email_communicationid` WHERE `email_communicationid` NOT IN (SELECT `communication_id` FROM `email_communication_timer` LEFT JOIN `time_cards` ON `email_communication_timer`.`created_by`=`time_cards`.`staff` AND `email_communication_timer`.`communication_id`=`time_cards`.`email_communicationid` AND `email_communication_timer`.`created_date`=`time_cards`.`date` WHERE `time_cards`.`deleted`=0)");

	// Get a list of time that is not to be included
	$timesheet_include_time = explode(',',get_config($_SERVER['DBC'], 'timesheet_include_time'));
	if(!in_array('ticket',$timesheet_include_time)) {
		$limits .= " AND IFNULL(`ticketid`,0)='0'";
	}
	if(!in_array('project',$timesheet_include_time)) {
		$limits .= " AND IFNULL(`projectid`,0)='0'";
	}
	if(!in_array('meeting',$timesheet_include_time)) {
		$limits .= " AND IFNULL(`agendameetingid`,0)='0'";
	}
	if(!in_array('email',$timesheet_include_time)) {
		$limits .= " AND IFNULL(`email_communicationid`,0)='0'";
	}
	// if(!in_array('task',$timesheet_include_time)) {
		// $limits .= " AND IFNULL(`tasklistid`,0)='0'";
	// }
	if(!in_array('checklist',$timesheet_include_time)) {
		$limits .= " AND IFNULL(`checklistnameid`,0)='0'";
	}

	// Get the list of time to display
	$timesheet_min_hours = get_config($_SERVER['DBC'], 'timesheet_min_hours');
	$timesheet_rounding = get_config($_SERVER['DBC'], 'timesheet_rounding');
	$timesheet_rounded_increment = get_config($_SERVER['DBC'], 'timesheet_rounded_increment') / 60;
	if($timesheet = $_SERVER['DBC']->query("SELECT MAX(`time_cards_id`) `id`, `date`, `staff`, `type_of_time`, SUM(`total_hrs`) `hours`, SUM(`timer_tracked`) `timer`, GROUP_CONCAT(`comment_box` SEPARATOR '&lt;br /&gt;') COMMENTS, SUM(`highlight`) HIGHLIGHT, SUM(`manager_highlight`) MANAGER, GROUP_CONCAT(`projectid`) PROJECTS, GROUP_CONCAT(`clientid`) CLIENTS, GROUP_CONCAT(`business`) BUSINESS, `ticketid`, `start_time`, `end_time`, `coord_approvals`, `manager_approvals`, `manager_name`, `coordinator_name` FROM `time_cards` WHERE `deleted`=0 AND `date` BETWEEN '$start_date' AND '$end_date' $limits GROUP BY `type_of_time` $group ORDER BY `date`, IFNULL(DATE_FORMAT(CONCAT_WS(' ',DATE(NOW()),`start_time`),'%H:%i'),STR_TO_DATE(`start_time`,'%l:%i %p')) ASC, IFNULL(DATE_FORMAT(CONCAT_WS(' ',DATE(NOW()),`end_time`),'%H:%i'),STR_TO_DATE(`end_time`,'%l:%i %p')) ASC")) {
		$time = [];
		while($time_card = $timesheet->fetch_assoc()) {
			if($time_card['hours'] < $timesheet_min_hours) {
				$time_card['hours'] = $timesheet_min_hours;
			}
			switch($timesheet_rounding) {
				case 'up':
					$time_card['hours'] = ceil($time_card['hours'] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
					break;
				case 'down':
					$time_card['hours'] = floor($time_card['hours'] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
					break;
				case 'nearest':
					$time_card['hours'] = round($time_card['hours'] / $timesheet_rounded_increment) * $timesheet_rounded_increment;
					break;
			}
			$time[] = $time_card;
		}
		return $time;
	} else {
		return [];
	}
}

function get_ticket_labels($dbc, $date, $staff, $layout = '', $time_cards_id, $format = '') {
	$ticket_labels = [];
	$sql = "SELECT `ticketid` FROM `time_cards` WHERE `date` = '$date' AND `staff` = '$staff' AND `deleted` = 0";
	if(($layout == 'multi_line' || $layout == 'position_dropdown' || $layout == 'ticket_task') && isset($time_cards_id)) {
		$sql .= " AND `time_cards_id` = '$time_cards_id'";
	}
	$query = mysqli_query($dbc, $sql);
	while($row = mysqli_fetch_assoc($query)) {
		$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '{$row['ticketid']}'"));
		$ticket_label = get_ticket_label($dbc, $ticket);
		if(!empty($ticket_label) && $ticket_label != '-') {
			if($format == 'pdf') {
				$ticket_labels[] = get_ticket_label($dbc, $ticket);
			} else {
				$ticket_labels[] = '<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].'" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].'&calendar_view=true\',\'auto\',false,true, $(\'#timesheet_div\').outerHeight()); return false;">'.get_ticket_label($dbc, $ticket).'</a>';
			}
		}
	}
	$ticket_labels = implode('<br />', $ticket_labels);
	return $ticket_labels;
}
function get_ticket_planned_hrs($dbc, $date, $staff, $layout = '', $time_cards_id) {
	$planned_hrs = [];
	$sql = "SELECT ta.*, t.`start_time`, t.`end_time` FROM `time_cards` tc LEFT JOIN `tickets` t ON t.`ticketid` = tc.`ticketid` LEFT JOIN `ticket_attached` ta ON ta.`id` = tc.`ticket_attached_id` WHERE t.`deleted` = 0 AND ta.`deleted` = 0 AND tc.`deleted` = 0 AND tc.`ticketid` > 0 AND tc.`date` = '$date'";
	if(($layout == 'multi_line' || $layout == 'position_dropdown' || $layout == 'ticket_task') && isset($time_cards_id)) {
		$sql .= " AND tc.`time_cards_id` = '$time_cards_id'";
	}
	$sql .= " GROUP BY tc.`ticket_attached_id`";
	$query = mysqli_query($dbc, $sql);
	while($row = mysqli_fetch_assoc($query)) {
		if(!empty(str_replace('00:00','',$row['start_time'])) || !empty(str_replace('00:00','',$row['end_time']))) {
			$planned_hrs[] = $row['start_time'].' - '.$row['end_time'];
		}
	}
	$planned_hrs = implode('<br />', $planned_hrs);
	if(empty($planned_hrs)) {
		return '---';
	} else {
		return $planned_hrs;
	}
}
function get_ticket_tracked_hrs($dbc, $date, $staff, $layout = '', $time_cards_id) {
	$tracked_hrs = [];
	$sql = "SELECT ta.*, t.`start_time`, t.`end_time` FROM `time_cards` tc LEFT JOIN `tickets` t ON t.`ticketid` = tc.`ticketid` LEFT JOIN `ticket_attached` ta ON ta.`id` = tc.`ticket_attached_id` WHERE t.`deleted` = 0 AND ta.`deleted` = 0 AND tc.`deleted` = 0 AND tc.`ticketid` > 0 AND tc.`date` = '$date'";
	if(($layout == 'multi_line' || $layout == 'position_dropdown' || $layout == 'ticket_task') && isset($time_cards_id)) {
		$sql .= " AND tc.`time_cards_id` = '$time_cards_id'";
	}
	$sql .= " GROUP BY tc.`ticket_attached_id`";
	$query = mysqli_query($dbc, $sql);
	while($row = mysqli_fetch_assoc($query)) {
		$attached_checkins = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_attached_checkin` WHERE `ticket_attached_id` = '".$row['id']."'"),MYSQLI_ASSOC);
		if(!empty($attached_checkins)) {
			foreach($attached_checkins as $attached_checkin) {
				$tracked_hrs[] = $attached_checkin['checked_in'].' - '.$attached_checkin['checked_out'];
			}
		} else {
			$tracked_hrs[] = $row['checked_in'].' - '.$row['checked_out'];
		}
	}
	$tracked_hrs = implode('<br />', $tracked_hrs);
	if(empty($tracked_hrs)) {
		return '---';
	} else {
		return $tracked_hrs;
	}
}
function get_ticket_total_tracked_time($dbc, $date, $staff, $layout = '', $time_cards_id) {
	$timesheet_time_format = get_config($dbc, 'timesheet_time_format');
	$tracked_time = [];
	$sql = "SELECT ta.*, t.`start_time`, t.`end_time` FROM `time_cards` tc LEFT JOIN `tickets` t ON t.`ticketid` = tc.`ticketid` LEFT JOIN `ticket_attached` ta ON ta.`id` = tc.`ticket_attached_id` WHERE t.`deleted` = 0 AND ta.`deleted` = 0 AND tc.`deleted` = 0 AND tc.`ticketid` > 0 AND tc.`date` = '$date'";
	if(($layout == 'multi_line' || $layout == 'position_dropdown' || $layout == 'ticket_task') && isset($time_cards_id)) {
		$sql .= " AND tc.`time_cards_id` = '$time_cards_id'";
	}
	$sql .= " GROUP BY tc.`ticket_attached_id`";
	$query = mysqli_query($dbc, $sql);
	while($row = mysqli_fetch_assoc($query)) {
		$curr_tracked_time = 0;

		$attached_checkins = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_attached_checkin` WHERE `ticket_attached_id` = '".$row['id']."'"),MYSQLI_ASSOC);
		if(!empty($attached_checkins)) {
			foreach($attached_checkins as $attached_checkin) {
				$curr_tracked_time += number_format((strtotime(date('Y-m-d').' '.$attached_checkin['checked_out']) - strtotime(date('Y-m-d').' '.$attached_checkin['checked_in']))/3600,2);
			}
		} else if(!empty($row['checked_out']) && !empty($row['checked_in'])) {
			$curr_tracked_time += number_format((strtotime(date('Y-m-d').' '.$row['checked_out']) - strtotime(date('Y-m-d').' '.$row['checked_in']))/3600,2);
		} else if($row['hours_tracked'] > 0) {
			$curr_tracked_time += number_format($row['hours_tracked'],2);
		}
		$tracked_time[] = ($timesheet_time_format == 'decimal' ? $curr_tracked_time : time_decimal2time($curr_tracked_time));
	}
	$tracked_time = implode('<br />', $tracked_time);
	if(empty($tracked_time)) {
		return '---';
	} else {
		return $tracked_time;
	}
}

function is_training_hrs($dbc, $time_cards_id) {
	$training_hrs = false;
	$timecard = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `time_cards_id` = '$time_cards_id'"));
	if($timecard['ticketid'] > 0) {
		$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '{$timecard['ticketid']}'"));
		if($ticket['projectid'] > 0) {
			$project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '{$ticket['projectid']}'"));
			if($project['projecttype'] == 'staff_training') {
				$training_hrs = true;
			}
		}
	}
	return $training_hrs;
}

function get_pdf_options($dbc, $styling = '', $timesheet_tab = '') {
	global $config;
	if($styling == 'EGS') {
		$value_config = explode(',',get_field_config($dbc, 'time_cards_total_hrs_layout'));
		if(empty(array_filter($value_config))) {
			$value_config = ['reg_hrs','overtime_hrs','doubletime_hrs'];
		}
	} else {
		$value_config = explode(',',get_field_config($dbc, 'time_cards'));
		if(!in_array('total_hrs',$value_config) && !in_array('reg_hrs',$value_config) && !in_array('direct_hrs',$value_config) && !in_array('payable_hrs',$value_config) && !in_array('total_hrs',$value_config)) {
		    $value_config = array_merge($value_config,['total_hrs']);
		}
	}
	$timesheet_payroll_fields = '';
	if($timesheet_tab == 'payroll') {
		$timesheet_payroll_fields = ','.get_config($dbc, 'timesheet_payroll_fields').',';
	}

	$options_html = '';
	foreach($value_config as $value) {
		foreach($config['pdf_options'] as $key => $label) {
			if($key == $value) {
				$options_html .= '<label class="form-checkbox"><input type="checkbox" name="pdf_options" value="'.$key.'" checked>'.$label.'</input></label>';
			}
		}
		if(strpos($timesheet_payroll_fields, ',Expenses Owed,') !== FALSE) {
			$options_html .= '<label class="form-checkbox"><input type="checkbox" name="payroll_pdf_options" value="Expenses Owed" checked>Expenses Owed</input></label>';
		}
		if(strpos($timesheet_payroll_fields, ',Mileage,') !== FALSE) {
			$options_html .= '<label class="form-checkbox"><input type="checkbox" name="payroll_pdf_options" value="Mileage" checked>Mileage</input></label>';
		}
		if(strpos($timesheet_payroll_fields, ',Mileage Rate,') !== FALSE) {
			$options_html .= '<label class="form-checkbox"><input type="checkbox" name="payroll_pdf_options" value="Mileage Rate" checked>Mileage Rate</input></label>';
		}
		if(strpos($timesheet_payroll_fields, ',Mileage Total,') !== FALSE) {
			$options_html .= '<label class="form-checkbox"><input type="checkbox" name="payroll_pdf_options" value="Mileage Total" checked>Mileage Total</input></label>';
		}
	}

	return $options_html;
}

function set_stat_hours($dbc, $staffid, $start_date, $end_date) {
	if($start_date > date('Y-m-d')) {
		$start_date = date('Y-m-d');
	}
	if($end_date > date('Y-m-d')) {
		$end_date = date('Y-m-d');
	}

	$holidays = mysqli_query($dbc, "SELECT * FROM `holidays` WHERE `date` BETWEEN '$start_date' AND '$end_date' AND `deleted` = 0 AND `paid` = 1");
	$contact = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '$staffid'"));
	while($holiday = mysqli_fetch_assoc($holidays)) {
		if($staffid > 0) {
			switch($contact['stat_pay']) {
				case 'Alberta Standard 4%':

					$end_date = $holiday['date'];
					$start_date = date('Y-m-d', strtotime($end_date.' - 28 days'));
					$total_hrs = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(IF(`type_of_time` NOT IN ('Extra Hrs.','Relief Hrs.','Sleep Hrs.','Sick Time Adj.','Sick Hrs.Taken','Stat Hrs.','Stat Hrs.Taken','Vac Hrs.','Vac Hrs.Taken','Break'),`total_hrs`,0)) REG_HRS FROM `time_cards` WHERE `staff` = '$staffid' AND `date` BETWEEN '$start_date' AND '$end_date' AND `deleted` = 0 AND `approv` != 'N'"))['REG_HRS'];
					if($total_hrs > 0) {
						$stat_hrs = ($total_hrs * 1.04) * 0.05;

						$time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `type_of_time` = 'Stat Hrs.' AND `staff` = '$staffid' AND `date` = '$end_date' AND `holidayid` ='".$holiday['holidays_id']."'"));
						if(!empty($time_card)) {
							mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs` = '$stat_hrs' WHERE `time_cards_id` = '".$time_card['time_cards_id']."'");
						} else {
							mysqli_query($dbc, "INSERT INTO `time_cards` (`holidayid`,`date`,`staff`,`type_of_time`,`total_hrs`,`comment_box`) VALUES ('".$holiday['holidays_id']."','$end_date','$staffid','Stat Hrs.','$stat_hrs','Stat Hours added automatically based on calculation from ".$contact['stat_pay'].". Hours may change as previous hours get altered/approved.')");
						}
					}
					break;
				case 'Alberta Standard 6%':
					$end_date = $holiday['date'];
					$start_date = date('Y-m-d', strtotime($end_date.' - 28 days'));
					$total_hrs = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(IF(`type_of_time` NOT IN ('Extra Hrs.','Relief Hrs.','Sleep Hrs.','Sick Time Adj.','Sick Hrs.Taken','Stat Hrs.','Stat Hrs.Taken','Vac Hrs.','Vac Hrs.Taken','Break'),`total_hrs`,0)) REG_HRS FROM `time_cards` WHERE `staff` = '$staffid' AND `date` BETWEEN '$start_date' AND '$end_date' AND `deleted` = 0 AND `approv` != 'N'"))['REG_HRS'];
					if($total_hrs > 0) {

						$stat_hrs = ($total_hrs * 1.06) * 0.05;

						$time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `type_of_time` = 'Stat Hrs.' AND `staff` = '$staffid' AND `date` = '$end_date' AND `holidayid` ='".$holiday['holidays_id']."'"));
						if(!empty($time_card)) {
							mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs` = '$stat_hrs' WHERE `time_cards_id` = '".$time_card['time_cards_id']."'");
						} else {
							mysqli_query($dbc, "INSERT INTO `time_cards` (`holidayid`,`date`,`staff`,`type_of_time`,`total_hrs`,`comment_box`) VALUES ('".$holiday['holidays_id']."','$end_date','$staffid','Stat Hrs.','$stat_hrs','Stat Hours added automatically based on calculation from ".$contact['stat_pay'].". Hours may change as previous hours get altered/approved.')");
						}
					}
					break;


				case 'Alberta Standard Salary':
					$end_date = $holiday['date'];

					    $days_per_week = get_contact($dbc, $staffid, 'days_per_week');
					    $hours_per_week = get_contact($dbc, $staffid, 'hours_per_week');
						$stat_hrs = ($hours_per_week/$days_per_week);

						$time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `type_of_time` = 'Stat Hrs.' AND `staff` = '$staffid' AND `date` = '$end_date' AND `holidayid` ='".$holiday['holidays_id']."'"));
						if(!empty($time_card)) {
							mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs` = '$stat_hrs' WHERE `time_cards_id` = '".$time_card['time_cards_id']."'");
						} else {
							mysqli_query($dbc, "INSERT INTO `time_cards` (`holidayid`,`date`,`staff`,`type_of_time`,`total_hrs`,`comment_box`) VALUES ('".$holiday['holidays_id']."','$end_date','$staffid','Stat Hrs.','$stat_hrs','Stat Hours added automatically based on calculation. Hours may change as previous hours get altered/approved.')");
						}
					break;

			}
			switch($contact['vaca_pay']) {
				case 'Alberta Standard 4%':
					$end_date = $holiday['date'];
					$start_date = date('Y-m-d', strtotime($end_date.' - 28 days'));
					$total_hrs = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(IF(`type_of_time` NOT IN ('Extra Hrs.','Relief Hrs.','Sleep Hrs.','Sick Time Adj.','Sick Hrs.Taken','Stat Hrs.','Stat Hrs.Taken','Vac Hrs.','Vac Hrs.Taken','Break'),`total_hrs`,0)) REG_HRS FROM `time_cards` WHERE `staff` = '$staffid' AND `date` BETWEEN '$start_date' AND '$end_date' AND `deleted` = 0 AND `approv` != 'N'"))['REG_HRS'];
					if($total_hrs > 0) {
						$vaca_hrs = $total_hrs * 0.04;

						$time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `type_of_time` = 'Vac Hrs.' AND `staff` = '$staffid' AND `date` = '$end_date' AND `deleted` = 0 AND `holidayid` ='".$holiday['holidays_id']."'"));
						if(!empty($time_card)) {
							mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs` = '$vaca_hrs' WHERE `time_cards_id` = '".$time_card['time_cards_id']."'");
						} else {
							mysqli_query($dbc, "INSERT INTO `time_cards` (`holidayid`,`date`,`staff`,`type_of_time`,`total_hrs`,`comment_box`) VALUES ('".$holiday['holidays_id']."','$end_date','$staffid','Vac Hrs.','$vaca_hrs','Vacation Hours added automatically based on calculation from ".$contact['stat_pay'].". Hours may change as previous hours get altered/approved.')");
						}
					}
					break;
				case 'Alberta Standard 6%':
					$end_date = $holiday['date'];
					$start_date = date('Y-m-d', strtotime($end_date.' - 28 days'));
					$total_hrs = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(IF(`type_of_time` NOT IN ('Extra Hrs.','Relief Hrs.','Sleep Hrs.','Sick Time Adj.','Sick Hrs.Taken','Stat Hrs.','Stat Hrs.Taken','Vac Hrs.','Vac Hrs.Taken','Break'),`total_hrs`,0)) REG_HRS FROM `time_cards` WHERE `staff` = '$staffid' AND `date` BETWEEN '$start_date' AND '$end_date' AND `deleted` = 0 AND `approv` != 'N'"))['REG_HRS'];
					if($total_hrs > 0) {
						$vaca_hrs = $total_hrs * 0.06;

						$time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `type_of_time` = 'Vac Hrs.' AND `staff` = '$staffid' AND `date` = '$end_date' AND `deleted` = 0 AND `holidayid` ='".$holiday['holidays_id']."'"));
						if(!empty($time_card)) {
							mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs` = '$vaca_hrs' WHERE `time_cards_id` = '".$time_card['time_cards_id']."'");
						} else {
							mysqli_query($dbc, "INSERT INTO `time_cards` (`holidayid`,`date`,`staff`,`type_of_time`,`total_hrs`,`comment_box`) VALUES ('".$holiday['holidays_id']."','$end_date','$staffid','Vac Hrs.','$vaca_hrs','Vacation Hours added automatically based on calculation from ".$contact['stat_pay'].". Hours may change as previous hours get altered/approved.')");
						}
					}
					break;

				case 'Alberta Standard Salary':
					$end_date = $holiday['date'];

					    $days_per_week = get_contact($dbc, $staffid, 'days_per_week');
					    $hours_per_week = get_contact($dbc, $staffid, 'hours_per_week');
						$vaca_hrs = ($hours_per_week/$days_per_week);

						$time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `type_of_time` = 'Vac Hrs.' AND `staff` = '$staffid' AND `date` = '$end_date' AND `deleted` = 0 AND `holidayid` ='".$holiday['holidays_id']."'"));
						if(!empty($time_card)) {
							mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs` = '$vaca_hrs' WHERE `time_cards_id` = '".$time_card['time_cards_id']."'");
						} else {
							mysqli_query($dbc, "INSERT INTO `time_cards` (`holidayid`,`date`,`staff`,`type_of_time`,`total_hrs`,`comment_box`) VALUES ('".$holiday['holidays_id']."','$end_date','$staffid','Vac Hrs.','$vaca_hrs','Vacation Hours added automatically based on calculation. Hours may change as previous hours get altered/approved.')");
						}
					break;

			}
		}
	}
}