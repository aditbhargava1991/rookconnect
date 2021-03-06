<?php
require_once('../phpsign/signature-to-image.php');

//Fix bugged out Time Cards where staff is 0 from a Ticket
$empty_time_cards = mysqli_query($dbc, "SELECT `time_cards`.*, `ticket_attached`.`item_id` FROM `time_cards` LEFT JOIN `ticket_attached` ON `time_cards`.`ticket_attached_id` = `ticket_attached`.`id` WHERE `time_cards`.`staff` = 0 AND `time_cards`.`ticket_attached_id` > 0 AND `ticket_attached`.`item_id` > 0 AND `ticket_attached`.`src_table` LIKE 'Staff%'");
while($empty_time_card = mysqli_fetch_assoc($empty_time_cards)) {
	mysqli_query($dbc, "UPDATE `time_cards` SET `staff` = '".$empty_time_card['item_id']."' WHERE `time_cards_id` = '".$empty_time_card['time_cards_id']."'");
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

$config['tabs'] = [ 'Time Sheets' => array('Weekly' => 'time_cards_weekly.php', 'Bi-Weekly' => 'time_cards_bi_weekly.php', 'Monthly' => 'time_cards_monthly.php', 'Custom' => 'time_cards.php'),
	'Pay Period' => array('Custom' => 'pay_period.php', 'Last Month' => 'pay_period_last_month.php', 'Current Month' => 'pay_period_current_month.php'),
	'Holidays' => 'holidays.php',
	'Coordinator Approvals' => 'time_card_approvals_coordinator.php',
	'Manager Approvals' => 'time_card_approvals_manager.php',
	'Reporting' => 'reporting.php',
	'Payroll' => 'payroll.php' ];

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

/* Time Cards */
$config['settings']['Choose Fields for Time Sheets']['config_field'] = 'time_cards';
$config['settings']['Choose Fields for Time Sheets']['data'] = array(
	'General' => array(
			array(PROJECT_NOUN, 'dropdown', 'project'),
			array('Business', 'dropdown', 'business'),
			array('Customer', 'dropdown', 'customer'),
			array(TICKET_NOUN, 'dropdown', 'ticketid'),
			array('Search by Staff', 'dropdown', 'search_staff'),
			array('Search by Client', 'dropdown', 'search_client'),
			array('Show Hours', 'hidden', 'show_hours'),
			array('Scheduled Hours', 'hidden', 'scheduled'),
			array('Schedule', 'hidden', 'schedule'),
			array('Position Drop Down', 'hidden', 'position'),
			array('Start Time', 'time', 'start_time'),
			array('End Time', 'time', 'end_time'),
			array('Start Time (Editable)', 'time', 'start_time_editable'),
			array('End Time (Editable)', 'time', 'end_time_editable'),
			array($timesheet_start_tile, 'time', 'start_day_tile'),
			array('Calculate Hours On Start/End Time Change', 'hidden', 'calculate_hours_start_end'),
			array('Type of Time', 'dropdown', 'type_of_time'),
			array('Address', 'text', 'address'),
			array('Phone', 'text', 'phone'),
			array('Email', 'text', 'email'),
			array('Payment', 'text', 'payment'),
			array('Confirmation', 'text', 'confirm'),
			array('Frequency', 'text', 'frequency'),
			array('Time Slot', 'text', 'time_slot'),
			array('Staff', 'dropdown', 'staff'),
			array('Cell Phone Carrier', 'dropdown', 'contact_staff'),
			array('Timeframe', 'text', 'timeframe'),
			array('Date', 'date', 'date'),
			array('Total Hrs', 'hours', 'total_hrs'),
			array('Total Tracked Hrs', 'hidden', 'total_tracked_hrs'),
			array('Planned Hours ('.TICKET_TILE.')', 'hidden', 'planned_hrs'),
			array('Tracked Hours ('.TICKET_TILE.')', 'hidden', 'tracked_hrs'),
			array('Total Tracked Time ('.TICKET_TILE.')', 'hidden', 'total_tracked_time'),
			array('Payable Hours', 'hidden', 'payable_hrs'),
			array('Payable Hours Updates '.TICKET_NOUN, 'hidden', 'update_ticket_payable'),
			array('Regular Hours', 'hidden', 'reg_hrs'),
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
			array('View '.TICKET_NOUN, 'hidden', 'view_ticket'),
			array('Comments', 'hidden', 'comment_box'),
			array('Parent/Guardian Signature', 'signature', 'signature'),
			array('Hide Signature on PDF', '', 'signature_pdf_hidden'),
			array('Approve All Checkbox', 'hidden', 'approve_all'),
			array('Show Time Overlaps', 'hidden', 'time_overlaps'),
			array('Editable Dates', 'hidden', 'editable_dates')
		)
);

$config['settings']['Choose Fields for Time Sheets Dashboard']['config_field'] = 'time_cards_dashboard';
$config['settings']['Choose Fields for Time Sheets Dashboard']['data'] = array(
	'General' => array(
			array('Business', 'dropdown', 'business'),
			array('Staff', 'dropdown', 'staff'),
			array('Date', 'date', 'date'),
			array('Start Time', 'time', 'start_time'),
			array('End Time', 'time', 'end_time'),
			array('Type of Time', 'dropdown', 'type_of_time'),
			array('Total Hrs', 'hours', 'total_hrs'),
			array('Weekly Tab', 'tab', 'time_cards_weekly.php'),
			array('Bi-Weekly Tab', 'tab', 'time_cards_bi_weekly.php'),
			array('Monthly Tab', 'tab', 'time_cards_monthly.php'),
			array('Custom Tab', 'tab', 'time_cards.php'),
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
		)
);

function get_tabs($tab = '', $subtab = '', $custom = array())
{
	global $config;
	global $dbc;
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
				$url = $contents['Custom'];
				
                if ( check_subtab_persmission($dbc, 'timesheet', ROLE, $title_subtab) === true ) {
                    $html .= "<a href='".$url."'><button type='button' class='btn brand-btn mobile-block ".$active."' >".($title == 'Manager Approvals' ? $timesheet_manager_approvals : $title)."</button></a>";
                } else {
                    $html .= "<button type='button' class='btn disabled-btn mobile-block'>".($title == 'Manager Approvals' ? $timesheet_manager_approvals : $title)."</button>&nbsp;";
                }
                
				if($title == $tab) {
					foreach($contents as $subtitle => $content) {
						$subactive = '';
						if($subtitle == $subtab) {
							$subactive = 'active_tab';
						}

						$get_field_config = mysqli_fetch_assoc(mysqli_query($custom['db'],"SELECT ".$custom['field']." FROM field_config"));
						$value_config = ','.$get_field_config[$custom['field']].',';

						if (strpos($value_config, ','.$content.',') !== FALSE) {
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
                    $html .= "<a href='".$contents."'><button type='button' class='btn brand-btn mobile-block ".$active."' >".($title == 'Manager Approvals' ? $timesheet_manager_approvals : $title)."</button></a>";
                } else {
                    $html .= "<button type='button' class='btn disabled-btn mobile-block'>".$title."</button>&nbsp;";
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
		if(in_array($field, ['time_cards_id','business','projectid','ticketid','agendameetingid','staff','contact_staff','date','start_time','end_time','type_of_time','timeframe','time_slot','frequency','confirm','payment','email','phone','address','timer_start','total_hrs','timer_tracked','highlight','manager_highlight','comment_box','manager_name','date_manager','manager_signature','coordinator_name','date_coordinator','coordinator_signature','approv','location','customer','day','shift_tracked','day_tracking_type','created_by','clientid','deleted','name','paid'])) {
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
	if($timesheet = $_SERVER['DBC']->query("SELECT MAX(`time_cards_id`) `id`, `date`, `staff`, `type_of_time`, SUM(`total_hrs`) `hours`, SUM(`timer_tracked`) `timer`, GROUP_CONCAT(`comment_box` SEPARATOR '&lt;br /&gt;') COMMENTS, SUM(`highlight`) HIGHLIGHT, SUM(`manager_highlight`) MANAGER, GROUP_CONCAT(`projectid`) PROJECTS, GROUP_CONCAT(`clientid`) CLIENTS, GROUP_CONCAT(`business`) BUSINESS, `ticketid`, `start_time`, `end_time`, `coord_approvals`, `manager_approvals`, `manager_name`, `coordinator_name` FROM `time_cards` WHERE `deleted`=0 AND `date` BETWEEN '$start_date' AND '$end_date' $limits GROUP BY `type_of_time` $group ORDER BY `date`, IFNULL(STR_TO_DATE(`start_time`, '%l:%i %p'),STR_TO_DATE(`start_time`, '%H:%i')) ASC, IFNULL(STR_TO_DATE(`end_time`, '%l:%i %p'),STR_TO_DATE(`end_time`, '%H:%i')) ASC")) {
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

function get_ticket_labels($dbc, $date, $staff, $layout = '', $time_cards_id) {
	$ticket_labels = [];
	$sql = "SELECT `ticketid` FROM `time_cards` WHERE `date` = '$date' AND `staff` = '$staff'";
	if($layout == 'multi_line' && isset($time_cards_id)) {
		$sql .= " AND `time_cards_id` = '$time_cards_id'";
	}
	$query = mysqli_query($dbc, $sql);
	while($row = mysqli_fetch_assoc($query)) {
		$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '{$row['ticketid']}'"));
		$ticket_label = get_ticket_label($dbc, $ticket);
		if(!empty($ticket_label) && $ticket_label != '-') {
			$ticket_labels[] = get_ticket_label($dbc, $ticket);
		}
	}
	$ticket_labels = implode('<br />', $ticket_labels);
	return $ticket_labels;
}

function get_ticket_planned_hrs($dbc, $date, $staff, $layout = '', $time_cards_id) {
	$planned_hrs = [];
	$sql = "SELECT ta.*, t.`start_time`, t.`end_time` FROM `tickets` t LEFT JOIN `ticket_attached` ta ON t.`ticketid` = ta.`ticketid` WHERE t.`deleted` = 0 AND ta.`deleted` = 0 AND ta.`src_table` IN ('Staff','Staff_Tasks') AND ta.`item_id` = '$staff' AND t.`to_do_date` = '$date'";
	if($layout == 'multi_line' && isset($time_cards_id)) {
		$ticketid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `ticketid` FROM `time_cards` WHERE `time_cards_id` = '$time_cards_id' AND '$time_cards_id' > 0"))['ticketid'];
		$sql .= " AND t.`ticketid` = '$ticketid' AND '$ticketid' > 0";
	}
	$query = mysqli_query($dbc, $sql);
	while($row = mysqli_fetch_assoc($query)) {
		$planned_hrs[] = $row['start_time'].' - '.$row['end_time'];
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
	$sql = "SELECT ta.*, t.`start_time`, t.`end_time` FROM `tickets` t LEFT JOIN `ticket_attached` ta ON t.`ticketid` = ta.`ticketid` WHERE t.`deleted` = 0 AND ta.`deleted` = 0 AND ta.`src_table` IN ('Staff','Staff_Tasks') AND ta.`item_id` = '$staff' AND t.`to_do_date` = '$date'";
	if($layout == 'multi_line' && isset($time_cards_id)) {
		$ticketid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `ticketid` FROM `time_cards` WHERE `time_cards_id` = '$time_cards_id' AND '$time_cards_id' > 0"))['ticketid'];
		$sql .= " AND t.`ticketid` = '$ticketid' AND '$ticketid' > 0";
	}
	$query = mysqli_query($dbc, $sql);
	while($row = mysqli_fetch_assoc($query)) {
		$tracked_hrs[] = $row['checked_in'].' - '.$row['checked_out'];
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
	$sql = "SELECT ta.*, t.`start_time`, t.`end_time` FROM `tickets` t LEFT JOIN `ticket_attached` ta ON t.`ticketid` = ta.`ticketid` WHERE t.`deleted` = 0 AND ta.`deleted` = 0 AND ta.`src_table` IN ('Staff','Staff_Tasks') AND ta.`item_id` = '$staff' AND t.`to_do_date` = '$date'";
	if($layout == 'multi_line' && isset($time_cards_id)) {
		$ticketid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `ticketid` FROM `time_cards` WHERE `time_cards_id` = '$time_cards_id' AND '$time_cards_id' > 0"))['ticketid'];
		$sql .= " AND t.`ticketid` = '$ticketid' AND '$ticketid' > 0";
	}
	$query = mysqli_query($dbc, $sql);
	while($row = mysqli_fetch_assoc($query)) {
		$curr_tracked_time = 0;
		if($row['hours_tracked'] > 0) {
			$curr_tracked_time = number_format($row['hours_tracked'],2);
		} else if(!empty($row['checked_out']) && !empty($row['checked_in'])) {
			$curr_tracked_time = number_format((strtotime(date('Y-m-d').' '.$row['checked_out']) - strtotime(date('Y-m-d').' '.$row['checked_in']))/3600,2);
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