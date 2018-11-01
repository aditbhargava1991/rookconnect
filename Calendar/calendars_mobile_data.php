<?php
//Settings
$calendar_type = $wait_list;
if($_GET['type'] == 'event') {
	$calendar_type = 'events';
}
$calendar_start = date('Y-m-d');
$search_month = date('F');
$search_year = date('Y');
$search_month_num = date('m');
if(!empty($_GET['date'])) {
	$calendar_start = $_GET['date'];
	$search_month = date('F', strtotime($_GET['date']));
	$search_year = date('Y', strtotime($_GET['date']));
	$search_month_num = date('m', strtotime($_GET['date']));
}
$calendar_month = date("n", strtotime($search_month));
$calendar_year = $search_year;

$first_day = date('w', strtotime(date($search_year.'-'.$search_month_num.'-01')));
$first_day = date('Y-m-d', strtotime(date($search_year.'-'.$search_month_num.'-01').'-'.$first_day.' days'));
$last_day = date('w', strtotime(date($search_year.'-'.$search_month_num.'-t')));
$last_day = date('Y-m-d', strtotime(date($search_year.'-'.$search_month_num.'-t').'+'.(6-$last_day).' days'));

//Add button variables
$mobile_calendar_add = [];
if(($_GET['type'] == 'event' && vuaed_visible_function($dbc, 'calendar_rook')) || ($wait_list == 'ticket' && $new_ticket_button !== '')) {
    $mobile_calendar_add[] = 'ticket';
}
if($wait_list == 'ticket') {
    $mobile_calendar_add[] = 'ticket';
}
if($wait_list == 'appt') {
    $mobile_calendar_add[] = 'appt';
}
if(strpos(','.$wait_list.',', ',ticket,') !== FALSE) {
    $mobile_calendar_add[] = 'ticket';
}
if(strpos(','.$wait_list.',', ',appt,') !== FALSE) {
    $mobile_calendar_add[] = 'appt';
}
if($use_shifts !== '') {
    $mobile_calendar_add[] = 'shift';
}
if($add_reminder !== '') {
    $mobile_calendar_add[] = 'reminder';
}
if($teams !== '') {
    $mobile_calendar_add[] = 'team';
}
if($equipment_assignment !== '') {
    $mobile_calendar_add[] = 'equip_assign';
}
if($wait_list == 'ticket' && $use_shift_tickets !== '') {
    $mobile_calendar_add[] = 'shift_tickets';
}
$mobile_calendar_add = array_filter(array_unique($mobile_calendar_add));

//Contacts
$all_contacts = [];
if($_GET['mode'] == 'client') {
	if($_GET['type'] == 'staff') {
		$contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category`='".$staff_schedule_client_type."' AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1".$region_query),MYSQLI_ASSOC));
		foreach ($contact_list as $contact_id) {
			if(empty($mobile_calendar_contact)) {
				$mobile_calendar_contact = $contact_id;
			}
			$all_contacts[$contact_id] = [get_contact($dbc, $contact_id), get_contact($dbc, $contact_id, 'region')];
		}
		if(isset($_GET['contactid'])) {
		    $mobile_calendar_contact = $_GET['contactid'];
		}
		$mobile_calendar_contact_cat = $staff_schedule_client_type;
		$mobile_calendar_contact_label = get_contact($dbc, $mobile_calendar_contact);
	} else if($_GET['type'] == 'shift') {
		$contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category`='".$shift_client_type."' AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1".$region_query),MYSQLI_ASSOC));
		foreach ($contact_list as $contact_id) {
			if(empty($mobile_calendar_contact)) {
				$mobile_calendar_contact = $contact_id;
			}
			$all_contacts[$contact_id] = [get_contact($dbc, $contact_id), get_contact($dbc, $contact_id, 'region')];
		}
		if(isset($_GET['contactid'])) {
		    $mobile_calendar_contact = $_GET['contactid'];
		}
		$mobile_calendar_contact_cat = $shift_client_type;
		$mobile_calendar_contact_label = get_contact($dbc, $mobile_calendar_contact);
	}
} else if($_GET['type'] == 'event') {
	$project_tabs = get_config($dbc, 'project_tabs');
	if($project_tabs == '') {
		$project_tabs = 'Client,SR&ED,Internal,R&D,Business Development,Process Development,Addendum,Addition,Marketing,Manufacturing,Assembly';
	}
	$project_tabs = explode(',',$project_tabs);
	$project_vars = [];
	foreach($project_tabs as $item) {
		$project_vars[] = preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)));
	}
	$active_projects = array_filter(explode(',',get_user_settings()['events_calendar_projects']));
	
	foreach($project_tabs as $project_i => $project_tab) {
		if(!check_subtab_persmission($dbc, 'project', ROLE, $project_vars[$project_i])) {
			unset($project_tabs[$project_i]);
			unset($project_vars[$project_i]);
		}
	}

	foreach($project_tabs as $project_i => $project_tab) {
		$project_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projecttype` = '".$project_vars[$project_i]."' AND `deleted` = 0"),MYSQLI_ASSOC);
		foreach ($project_list as $project) {
			if(empty($mobile_calendar_contact)) {
				$mobile_calendar_contact = $project['projectid'];
			}
			$all_contacts[$project_tab][$project['projectid']] = get_project_label($dbc, $project);
		}
	}
	if(isset($_GET['contactid'])) {
	    $mobile_calendar_contact = $_GET['contactid'];
	}
	$mobile_calendar_contact_cat = TICKET_NOUN;
	$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '$mobile_calendar_contact' AND `deleted` = 0"));
	$mobile_calendar_contact_label = get_project_label($dbc, $project);
} else if($_GET['type'] == 'schedule' && $_GET['mode'] != 'staff') {
	$equip_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT *, CONCAT(`category`, ' #', `unit_number`) label FROM `equipment` WHERE `deleted`=0".$equip_cat_query),MYSQLI_ASSOC);
	foreach ($equip_list as $equipment) {
		$equip_assign = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `clientid` SEPARATOR ',') as client_list, GROUP_CONCAT(DISTINCT `region` SEPARATOR '*#*') as region_list, GROUP_CONCAT(DISTINCT `location` SEPARATOR '*#*') as location_list, GROUP_CONCAT(DISTINCT `classification` SEPARATOR '*#*') as classification_list FROM `equipment_assignment` WHERE `equipmentid` = '".$equipment['equipmentid']."' AND `deleted` = 0 AND (DATE(`start_date`) BETWEEN '$first_day' AND '$last_day' OR DATE(`end_date`) BETWEEN '$first_day' AND '$last_day')"));
		$equip_regions = $equipment['region'].'*#*'.$equip_assign['region_list'];
		$equip_regions = implode('*#*', array_filter(array_unique(explode('*#*', $equip_regions))));
		if(empty($mobile_calendar_contact)) {
			$mobile_calendar_contact = $equipment['equipmentid'];
		}
		$all_contacts[$equipment['equipmentid']] = [$equipment['label'],$equip_regions];
	}
	if(isset($_GET['contactid'])) {
		$mobile_calendar_contact = $_GET['contactid'];
	}
	$mobile_calendar_contact_cat = $equipment_category;
	$mobile_calendar_contact_label = $all_contacts[$mobile_calendar_contact][0];
	$equipment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `equipmentid`, `unit_number`, `make`, `model`, `category`, CONCAT(`category`, ' #', `unit_number`) label FROM `equipment` WHERE `equipmentid` = '$mobile_calendar_contact'"));
} else {
	$contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND IFNULL(`calendar_enabled`,1)=1".$region_query),MYSQLI_ASSOC));
	foreach ($contact_list as $contact_id) {
		$all_contacts[$contact_id] = [get_contact($dbc, $contact_id), get_contact($dbc, $contact_id, 'region')];
	}
	$mobile_calendar_contact = $_SESSION['contactid'];
	if(isset($_GET['contactid'])) {
	    $mobile_calendar_contact = $_GET['contactid'];
	}
	$mobile_calendar_contact_cat = 'Staff';
	$mobile_calendar_contact_label = get_contact($dbc, $mobile_calendar_contact);
}

if($_GET['type'] == 'schedule') {
	if($_GET['mode'] == 'staff') {
		$_GET['block_type'] = 'dispatch_staff';
	} else {
		$_GET['block_type'] = 'equipment';
	}
}

//Table data
$contact_id = $mobile_calendar_contact;
$column_id = 0;
for($cur_day = date('Y-m-d',strtotime($first_day.'- 1 day')); strtotime($cur_day) <= strtotime($last_day); $cur_day = date('Y-m-d', strtotime($cur_day.'+ 1 day'))) {
    $calendar_date = date('Y-m-d', strtotime($cur_day));
    $day_of_week = date('l', strtotime($calendar_date));
    $_POST['config_type'] = $config_type;
    $_POST['calendar_date'] = $calendar_date;
    $_POST['contact_id'] = $contact_id;
    $is_mobile_view = true;
    include('../Calendar/load_calendar_item.php');
	$column['total_tickets'] = $calendar_table[$cur_day][$contact_id]['total_tickets'];
	$column['total_appt'] = $calendar_table[$cur_day][$contact_id]['total_appt'];
	$column['total_shifts'] = $calendar_table[$cur_day][$contact_id]['total_shifts'];
	$column['total_dayoff'] = $calendar_table[$cur_day][$contact_id]['total_dayoff'];
	$column['total_estimates'] = $calendar_table[$cur_day][$contact_id]['total_estimates'];
    $calendar_table[$calendar_date][$contact_id] = $column;
  //   if($_GET['type'] == 'estimates') {
  //   	include('../Calendar/estimates_blocks.php');
  //   } else {
		// include('../Calendar/appointment_blocks.php');
  //   }
}
$day_start = get_config($dbc, $calendar_config.'_day_start');
$day_end = get_config($dbc, $calendar_config.'_day_end');
$day_period = get_config($dbc, $calendar_config.'_increments');
$current_row = strtotime($day_start);
$appointment_calendar = 'mobile';
$calendar_table[0][0] = [];
$calendar_table[0][0]['title'] = "Time";
if(!empty($use_shifts)) {
	$calendar_table[0][0]['shifts'] = "Shifts";
}
if(get_config($dbc, $calendar_config.'_calendar_notes') == '1') { $calendar_table[0][0]['notes'] = "Notes"; }
if(get_config($dbc, $calendar_config.'_reminders') == '1') { $calendar_table[0][0]['reminders'] = "Reminders"; }
$calendar_table[0][0]['warnings'] = "Warnings";
while($current_row <= strtotime($day_end)) {
	$calendar_table[0][0][] = date('g:i a', $current_row);
	$current_row = strtotime('+'.$day_period.' minutes', $current_row);
}
unset($calendar_table[date('Y-m-d',strtotime($first_day.'- 1 day'))]);
?>