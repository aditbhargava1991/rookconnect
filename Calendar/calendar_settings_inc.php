<?php
$contactid = $_SESSION['contactid'];
$contact_regions = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0])));
$region_colours = explode(',',get_config($dbc, '%_region_colour', true));
$contact_security = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts_security` WHERE `contactid`='$contactid'"));
$allowed_regions = array_filter(explode('#*#',$contact_security['region_access']));
if(count($allowed_regions) == 0) {
    $allowed_regions = $contact_regions;
}
$class_regions = explode(',',get_config($dbc, '%_class_regions', true, ','));
$class_logos = explode('*#*',get_config($dbc, '%_class_logos', true, '*#*'));
$contact_classifications = [];
$classification_regions = [];
$classification_logos = [];
$allowed_classifications = array_filter(explode('#*#',$contact_security['classification_access']));
foreach(explode(',',get_config($dbc, '%_classification', true, ',')) as $i => $contact_classification) {
    if(in_array($contact_classification, $allowed_classifications) || empty($allowed_classifications)) {
    	$row = array_search($contact_classification, $contact_classifications);
    	if($class_regions[$i] == 'ALL') {
    		$class_regions[$i] = '';
    	}
    	if($row !== FALSE && $class_regions[$i] != '') {
    		$classification_regions[$row][] = $class_regions[$i];
            $classification_logos[$row] = $class_logos[$i];
    	} else {
    		$contact_classifications[] = $contact_classification;
    		$classification_regions[] = array_filter([$class_regions[$i]]);
            $classification_logos[] = $class_logos[$i];
    	}
    }
}
$allowed_classifications = $contact_classifications;
$contact_locations = array_filter(explode(',', mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `con_locations` FROM `field_config_contacts` WHERE `con_locations` IS NOT NULL"))['con_locations']));
$allowed_locations = array_filter(explode('#*#',$contact_security['location_access']));
if(count($allowed_locations) == 0) {
    $allowed_locations = $contact_locations;
}
$allowed_equipment = $contact_security['equipment_access'];
$allowed_equipment_query = '';
if(!empty($allowed_equipment)) {
    $allowed_equipment_query = " AND `equipmentid` IN ($allowed_equipment)";
}
$allowed_dispatch_staff = count($contact_security['dispatch_staff_access']) > 0 ? $contact_security['dispatch_staff_access'] : 1;
$allowed_dispatch_team = count($contact_security['dispatch_team_access']) > 0 ? $contact_security['dispatch_team_access'] : 1;
$allowed_dispatch_contractors = count($contact_security['dispatch_contractor_access']) > 0 ? $contact_security['dispatch_contractor_access'] : 1;

$calendar_checkmark_tickets = get_config($dbc, 'calendar_checkmark_tickets');
$calendar_checkmark_status = get_config($dbc, 'calendar_checkmark_status');
if(empty($calendar_checkmark_status)) {
    $calendar_checkmark_status = ['Complete'];
} else {
    $calendar_checkmark_status = explode('*#*', $calendar_checkmark_status);
}
$calendar_highlight_tickets = get_config($dbc, 'calendar_highlight_tickets');
$calendar_completed_colors = explode('*#*',get_config($dbc, 'calendar_completed_color'));
foreach ($calendar_checkmark_status as $i => $checkmark_status) {
    if(empty($calendar_completed_colors[$i])) {
        $calendar_completed_colors[$i] = '#00ff00';
    }
    $calendar_completed_color[$checkmark_status] = $calendar_completed_colors[$i];
}
if(empty($calendar_completed_color)) {
    $calendar_completed_color = '#00ff00';
}
$calendar_incomplete_status = get_config($dbc, 'calendar_incomplete_status');
if(empty($calendar_incomplete_status)) {
    $calendar_incomplete_status = ['Incomplete'];
} else {
    $calendar_incomplete_status = explode('*#*', $calendar_incomplete_status);
}
$calendar_highlight_incomplete_tickets = get_config($dbc, 'calendar_highlight_incomplete_tickets');
$calendar_incomplete_colors = explode('*#*',get_config($dbc, 'calendar_incomplete_color'));
foreach($calendar_incomplete_status as $i => $incomplete_status) {
    if(empty($calendar_incomplete_colors[$i])) {
        $calendar_incomplete_colors[$i] = '#ff0000';
    }
    $calendar_incomplete_color[$incomplete_status] = $calendar_incomplete_colors[$i];
}
if(empty($calendar_incomplete_color)) {
    $calendar_incomplete_color = '#ff0000';
}
$calendar_ticket_color_code_tabs = get_config($dbc, 'calendar_ticket_color_code_tabs');
$ticket_status_color_code = get_config($dbc, 'ticket_status_color_code');
if($ticket_status_color_code == 1) {
    $status_color_codes = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_status_color`"),MYSQLI_ASSOC);
    foreach ($status_color_codes as $status_color_code) {
        $ticket_status_color[$status_color_code['status']] = $status_color_code['color'];
    }
}
$ticket_status_color_code_legend = get_config($dbc, 'ticket_status_color_code_legend');
if($ticket_status_color_code_legend == 1) {
    $ticket_statuses = explode(',', get_config($dbc, 'ticket_status'));
    $ticket_status_legend = '<b>Status Color Code:</b><br>';
    foreach ($ticket_statuses as $ticket_status) {
        $ticket_status_color_detail = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_status_color` WHERE `status` = '$ticket_status'"))['color'];
        $ticket_status_legend .= '<label><div class="ticket-status-color" style="background-color: '.$ticket_status_color_detail.';"></div>'.$ticket_status.'</label><br />';
    }
	/*$ticket_status_color_detail = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_status_color` WHERE `status` = 'Today'"))['color'];
	if($ticket_status_color_detail != '') {*/
		$ticket_status_legend .= '<label><img class="inline-img smaller" src="../img/block/green.png"> Today + Following Day</label><br />';
	/*}
	$ticket_status_color_detail = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_status_color` WHERE `status` = 'Recent'"))['color'];
	if($ticket_status_color_detail != '') {*/
		$ticket_status_legend .= '<label><img class="inline-img smaller" src="../img/block/orange	.png"> Last 2 Days</label><br />';
	/*}
	$ticket_status_color_detail = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_status_color` WHERE `status` = 'Old'"))['color'];
	if($ticket_status_color_detail != '') {*/
		$ticket_status_legend .= '<label><img class="inline-img smaller" src="../img/block/red.png"> Older than 2 Day</label><br />';
	/*}*/
}
$shift_conflicts_button = !empty(mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_shifts` WHERE CONCAT(',',`enabled_fields`,',') LIKE '%,conflicts_button,%'"))) ? 1 : 0;
$shift_client_color = !empty(mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_shifts` WHERE CONCAT(',',`enabled_fields`,',') LIKE '%,client_calendar_color,%'"))) ? 1 : 0;
$calendar_ticket_diff_label = get_config($dbc, 'calendar_ticket_diff_label');
$calendar_ticket_label = '';
if($calendar_ticket_diff_label == 1) {
    $calendar_ticket_label = get_config($dbc, 'calendar_ticket_label');
}
$calendar_ticket_status_icon = get_config($dbc, 'calendar_ticket_status_icon');
$calendar_ticket_card_fields = explode(',',get_config($dbc, 'calendar_ticket_card_fields'));
$calendar_ticket_staff_summary_fields = explode(',',get_config($dbc, 'calendar_ticket_staff_summary_fields'));
$lock_date = get_staff_schedule_lock_date($dbc);
if($_GET['type'] == '' || $_GET['view'] == '') {
    $default = get_config($dbc, 'calendar_default');
    $user_default = mysqli_fetch_array(mysqli_query($dbc, "SELECT IFNULL(`calendar_view`,'default') view FROM `user_settings` WHERE `contactid`='{$_SESSION['contactid']}'"))['view'];
    if($user_default != 'default') {
        $default = $user_default;
    }
    $default = explode('_',$default);
    if($_GET['type'] == '') {
        switch($default[0]) {
            case 'uni': $_GET['type'] = 'uni'; break;
            case 'appt': $_GET['type'] = 'appt'; break;
            case 'sched': $_GET['type'] = 'schedule'; break;
            case 'estimates': $_GET['type'] = 'estimates'; break;
            case 'ticket': $_GET['type'] = 'ticket'; break;
            case 'shift': $_GET['type'] = 'shift'; break;
            case 'event': $_GET['type'] = 'event'; break;
            case 'staff': $_GET['type'] = 'staff'; break;
            case 'my':
            default: $_GET['type'] = 'my'; break;
        }
    }
    if($_GET['view'] == '') {
        switch($default[1]) {
            case 'wk': $_GET['view'] = 'weekly'; break;
            case 'mon': $_GET['view'] = 'monthly'; break;
            case '30': $_GET['view'] = '30day'; break;
            case 'day':
            default: $_GET['view'] = 'daily'; break;
        }
    }
}
if(basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']) == 'calendars_mobile.php') {
    $_GET['view'] = 'daily';
    $_GET['region'] = 'Display All';
}
switch($_GET['type']) {
    case 'my' : $calendar_label = 'My Calendar'; $calendar_config = 'my'; break;
    case 'uni' : $calendar_label = 'Universal Calendar'; $calendar_config = 'uni'; break;
    case 'appt' : $calendar_label = 'Appointment Calendar'; $calendar_config = 'appt'; break;
    case 'staff' : $calendar_label = 'Staff Schedule Calendar'; $calendar_config = 'staff_schedule'; break;
    case 'schedule' : $calendar_label = 'Dispatch Calendar'; $calendar_config = 'scheduling'; break;
    case 'estimates' : $calendar_label = 'Sales '.ESTIMATE_TILE.' Calendar'; $calendar_config = 'estimates'; break;
    case 'ticket' : $calendar_label = TICKET_NOUN.' Calendar'; $calendar_config = 'ticket'; break;
    case 'shift' : $calendar_label = 'Shift Calendar'; $calendar_config = 'shift'; break;
    case 'event' : $calendar_label = 'Events Calendar'; $calendar_config = 'event'; break;
}
if($_GET['mode'] == '') {
    if($_GET['type'] == 'schedule') {
        $_GET['mode'] = 'schedule';
    } else if($_GET['type'] != 'uni' && $_GET['type'] != 'my') {
        $_GET['mode'] = 'staff';
    }
}
if(empty($_GET['region'])) {
    $_GET['region'] = 'Display All';
}
$region_query = '';
if(($_GET['region'] == 'Display All' && $allowed_regions != $contact_regions) || $_GET['type'] == 'schedule' && $allowed_regions != $contact_regions) {
    $all_allowed = "'".trim(implode("','", $allowed_regions), "','")."'";
    $region_query = " AND IFNULL(`region`,'') IN (".$all_allowed.",'')";
} else if($_GET['region'] != 'Display All' && count($contact_regions) > 0 && $_GET['type'] != 'schedule') {
    $region_query = " AND IFNULL(`region`,'') IN ('".$_GET['region']."','')";
}
asort($contact_regions);
asort($allowed_regions);
asort($contact_locations);
asort($allowed_locations);
asort($contact_classifications);

$page_query = $_GET;

$use_shifts = '';
$wait_list = '';
$offline_mode = '';
$use_unbooked = '';
$add_reminder = '';
$teams = '';
$equipment_assignment = '';
$weekly_start = '';
$weekly_days = '';
switch($_GET['type']) {
    case 'my':
        $config_type = 'my';
        $use_shifts = get_config($dbc, 'my_use_shifts');
        $default_view = get_config($dbc, 'my_default_view');
        if(empty($_GET['mode']) && $use_shifts != '' && $default_view == 'shifts') {
            $_GET['mode'] = 'shift';
        } else if(empty($_GET['mode'])) {
            $_GET['mode'] = 'staff';
        }
        $wait_list = get_config($dbc, 'my_wait_list');
        $combine_shift_items = get_config($dbc, 'my_combine_shift_items');
        $use_shift_tickets = get_config($dbc, 'my_use_shift_tickets');
        if($_GET['view'] != 'monthly') {
            $use_unbooked = get_config($dbc, 'my_use_unbooked');
        }
        $offline_mode = get_config($dbc, 'my_offline');
        $add_reminder = get_config($dbc, 'my_reminders');
        $teams = '';
        $equipment_assignment = '';
        $weekly_start = get_config($dbc, 'my_weekly_start');
        $weekly_days = explode(',', get_config($dbc, 'my_weekly_days'));
        $monthly_numdays = get_config($dbc, 'my_monthly_numdays');
        $monthly_start = get_config($dbc, 'my_monthly_start');
        $monthly_days = explode(',', get_config($dbc, 'my_monthly_days'));
        $ticket_summary = get_config($dbc, 'my_ticket_summary');
        $ticket_summary_deleted = get_config($dbc, 'my_ticket_summary_deleted');
        $availability_indication = get_config($dbc, 'my_availability_indication');
        $sidebar_file = 'my_sidebar.php';
        $all_tickets_button = '';

        $mobile_calendar_views = ['staff'=>'Staff'];
        if($use_shifts != '') {
            $mobile_calendar_views['shift'] = 'Shifts';
        }
        $mobile_calendar_view = $mobile_calendar_views[$_GET['mode']];
        break;
    case 'uni':
        $config_type = 'uni';
        $use_shifts = get_config($dbc, 'uni_use_shifts');
        $default_view = get_config($dbc, 'uni_default_view');
        if(empty($_GET['mode']) && $use_shifts != '' && $default_view == 'shifts') {
            $_GET['mode'] = 'shift';
        } else if(empty($_GET['mode'])) {
            $_GET['mode'] = 'staff';
        }
        $wait_list = get_config($dbc, 'uni_wait_list');
        $combine_shift_items = get_config($dbc, 'uni_combine_shift_items');
        $use_shift_tickets = get_config($dbc, 'uni_use_shift_tickets');
        if($_GET['view'] != 'monthly') {
            $use_unbooked = get_config($dbc, 'uni_use_unbooked');
        }
        $offline_mode = get_config($dbc, 'uni_offline');
        $add_reminder = get_config($dbc, 'uni_reminders');
        $teams = get_config($dbc, 'uni_teams');
        $equipment_assignment = '';
        $weekly_start = get_config($dbc, 'uni_weekly_start');
        $weekly_days = explode(',', get_config($dbc, 'uni_weekly_days'));
        $monthly_numdays = get_config($dbc, 'uni_monthly_numdays');
        $monthly_start = get_config($dbc, 'uni_monthly_start');
        $monthly_days = explode(',', get_config($dbc, 'uni_monthly_days'));
        $ticket_summary = get_config($dbc, 'uni_ticket_summary');
        $ticket_summary_deleted = get_config($dbc, 'uni_ticket_summary_deleted');
        $availability_indication = get_config($dbc, 'uni_availability_indication');
        $sidebar_file = 'uni_sidebar.php';
        $all_tickets_button = '';
        $staff_split_security = get_config($dbc, 'uni_staff_split_security');
        $client_staff_freq = get_config($dbc, 'uni_client_staff_freq');
        $client_draggable = get_config($dbc, 'uni_client_draggable');
        $staff_summary = get_config($dbc, 'uni_staff_summary');
        $day_summary_tab = get_config($dbc, 'uni_day_summary_tab');

        $mobile_calendar_views = ['staff'=>'Staff'];
        if($use_shifts != '') {
            $mobile_calendar_views['shift'] = 'Shifts';
        }
        $mobile_calendar_view = $mobile_calendar_views[$_GET['mode']];
        break;
    case 'appt':
        $config_type = 'appt';
        $use_shifts = get_config($dbc, 'appt_use_shifts');
        $wait_list = get_config($dbc, 'appt_wait_list');
        $use_shift_tickets = get_config($dbc, 'appt_use_shift_tickets');
        if($_GET['view'] != 'monthly') {
            $use_unbooked = get_config($dbc, 'appt_use_unbooked');
        }
        $offline_mode = get_config($dbc, 'appt_offline');
        $add_reminder = get_config($dbc, 'appt_reminders');
        $teams = get_config($dbc, 'appt_teams');
        $equipment_assignment = get_config($dbc, 'appt_equip_assign');
        $weekly_start = get_config($dbc, 'appt_weekly_start');
        $weekly_days = explode(',', get_config($dbc, 'appt_weekly_days'));
        $monthly_numdays = get_config($dbc, 'appt_monthly_numdays');
        $monthly_start = get_config($dbc, 'appt_monthly_start');
        $monthly_days = explode(',', get_config($dbc, 'appt_monthly_days'));
        $ticket_summary = get_config($dbc, 'appt_ticket_summary');
        $availability_indication = get_config($dbc, 'appt_availability_indication');
        $sidebar_file = 'appointment_sidebar.php';
        $all_tickets_button = '';

        $mobile_calendar_views = [''=>'Staff'];
        $mobile_calendar_view = 'Staff';
        break;
    case 'staff':
        $config_type = 'staff_schedule';
        if($_GET['mode'] != 'client' && $_GET['mode'] != 'tickets') {
            $use_shifts = get_config($dbc, 'staff_schedule_use_shifts');
        }
        $wait_list = get_config($dbc, 'staff_schedule_wait_list');
        $use_shift_tickets = get_config($dbc, 'staff_schedule_use_shift_tickets');
        if($_GET['view'] != 'monthly') {
            $use_unbooked = get_config($dbc, 'staff_schedule_use_unbooked');
        }
        $offline_mode = get_config($dbc, 'staff_schedule_offline');
        $add_reminder = get_config($dbc, 'staff_schedule_reminders');
        $teams = get_config($dbc, 'staff_schedule_teams');
        $equipment_assignment = get_config($dbc, 'staff_schedule_equip_assign');
        $weekly_start = get_config($dbc, 'staff_schedule_weekly_start');
        $weekly_days = explode(',', get_config($dbc, 'staff_schedule_weekly_days'));
        $monthly_numdays = get_config($dbc, 'staff_schedule_monthly_numdays');
        $monthly_start = get_config($dbc, 'staff_schedule_monthly_start');
        $monthly_days = explode(',', get_config($dbc, 'staff_schedule_monthly_days'));
        $staff_schedule_client_type = get_config($dbc, 'staff_schedule_client_type');
        $staff_schedule_client_type = ($staff_schedule_client_type != '' ? $staff_schedule_client_type : 'Clients');
        $ticket_summary = get_config($dbc, 'staff_schedule_ticket_summary');
        $availability_indication = get_config($dbc, 'staff_schedule_availability_indication');
        $all_tickets_button = get_config($dbc, 'staff_schedule_use_all_tickets');
        $sidebar_file = 'staff_sidebar.php';

        $mobile_calendar_views = [''=>'Staff','client'=>$staff_schedule_client_type];
        $mobile_calendar_view = 'Staff';
        if($_GET['mode'] == 'client') {
            $mobile_calendar_view = $staff_schedule_client_type;
        }
        break;
    case 'schedule':
        $config_type = 'scheduling';
        $use_shifts = '';
        $wait_list = get_config($dbc, 'scheduling_wait_list');
		if($wait_list == 'ticket_multi') {
			$wait_list = 'ticket';
		}
        $use_shift_tickets = get_config($dbc, 'scheduling_use_shift_tickets');
        if($_GET['view'] != 'monthly') {
            $use_unbooked = get_config($dbc, 'scheduling_use_unbooked');
        }
        $offline_mode = get_config($dbc, 'scheduling_offline');
        $add_reminder = '';
        $teams = get_config($dbc, 'scheduling_teams');
        $equipment_assignment = 1;
        $weekly_start = get_config($dbc, 'scheduling_weekly_start');
        $weekly_days = explode(',', get_config($dbc, 'scheduling_weekly_days'));
        $monthly_numdays = get_config($dbc, 'scheduling_monthly_numdays');
        $monthly_start = get_config($dbc, 'scheduling_monthly_start');
        $monthly_days = explode(',', get_config($dbc, 'scheduling_monthly_days'));
        $equipment_category = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"))['equipment_category'];
        $equipment_categories = array_filter(explode(',', $equipment_category));
        if(empty($equipment_categories)) {
            $equipment_categories = array_filter(explode(',',get_config($dbc, 'equipment_tabs')));
        }
        echo '<input type="hidden" name="equipment_category_label" value="'.$equipment_category.'">';
        $dispatch_filters = get_config($dbc, 'scheduling_filters');
        $new_ticket_button = get_config($dbc, 'scheduling_new_ticket_button');
        $ticket_summary = get_config($dbc, 'scheduling_ticket_summary');
        $scheduling_item_filters = get_config($dbc, 'scheduling_item_filters');
        $filter_condition = [];
        if(strpos(",$scheduling_item_filters,",",Region,") !== FALSE) {
            $filter_condition[] = '(regions.indexOf(this_region) == -1 && regions.length > 0)';
        }
        if(strpos(",$scheduling_item_filters,",",Location,") !== FALSE) {
            $filter_condition[] = '(locations.indexOf(this_location) == -1 && locations.length > 0)';
        }
        if(strpos(",$scheduling_item_filters,",",Classification,") !== FALSE) {
            $filter_condition[] = '(classifications.indexOf(this_classification) == -1 && classifications.length > 0)';
        }
        $filter_condition = implode(' || ', $filter_condition);
        if(empty($filter_condition)) {
            $filter_condition = '1 != 1';
        }
        $scheduling_classification_loggedin = get_config($dbc, 'scheduling_classification_loggedin');
        $sidebar_file = 'schedule_sidebar.php';
        $contact_category = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"))['contact_category'];
        $contractor_category = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"))['contractor_category'];
        $all_tickets_button = '';
        $multi_class_admin = get_config($dbc, 'scheduling_multi_class_admin');

        $mobile_calendar_views = [''=>'Schedule','staff'=>'Staff'];
        $mobile_calendar_view = 'Schedule';
        if($_GET['mode'] == 'staff') {
            $mobile_calendar_view = 'Staff';
        }
        $combine_warehouses = get_config($dbc, 'scheduling_combine_warehouse');
        $combine_pickups = get_config($dbc, 'scheduling_combine_pickup');
        $combine_time = get_config($dbc, 'scheduling_combine_time');
        $scheduling_summary_view = get_config($dbc, 'scheduling_summary_view');
        $warning_num_tickets = get_config($dbc, 'scheduling_warning_num_tickets');
        $equip_display_classification = get_config($dbc, 'scheduling_equip_classification');
        $equip_display_classification_ticket = get_config($dbc, 'scheduling_equip_classification_ticket');
        $service_followup = get_config($dbc, 'scheduling_service_followup');
        $service_date = get_config($dbc, 'scheduling_service_date');
        $passed_service = get_config($dbc, 'scheduling_passed_service');
        $columns_group_regions = get_config($dbc, 'scheduling_columns_group_regions');
        $staff_split_security = get_config($dbc, 'scheduling_staff_split_security');
        $contractor_split_security = get_config($dbc, 'scheduling_contractor_split_security');
        $drag_multiple = get_config($dbc, 'scheduling_drag_multiple');
        $customer_roles = array_filter(explode(',',get_config($dbc, 'scheduling_customer_roles')));
        $is_customer = false;
        foreach(array_filter(explode(',',ROLE)) as $session_role) {
            if(in_array($session_role,$customer_roles)) {
                $is_customer = true;
            }
        }
        $export_time_table = get_config($dbc, 'scheduling_export_time_table');
        $dont_count_warehouse = get_config($dbc, 'scheduling_dont_count_warehouse');
        $restrict_ticket_types = array_filter(explode(',',get_config($dbc, 'scheduling_restrict_ticket_types')));
        break;
    case 'estimates':
        $config_type = 'estimates';
        $use_shifts = '';
        $wait_list = '';
        $use_shift_tickets = '';
        $use_unbooked = '';
        $offline_mode = '';
        $add_reminder = get_config($dbc, 'estimates_reminders');
        $teams = '';
        $equipment_assignment = '';
        $weekly_start = get_config($dbc, 'estimates_weekly_start');
        $weekly_days = explode(',', get_config($dbc, 'estimates_weekly_days'));
        $monthly_numdays = get_config($dbc, 'estimates_monthly_numdays');
        $monthly_start = get_config($dbc, 'estimates_monthly_start');
        $monthly_days = explode(',', get_config($dbc, 'estimates_monthly_days'));
        $ticket_summary = '';
        $sidebar_file = 'estimates_sidebar.php';
        $all_tickets_button = '';

        $mobile_calendar_views = [''=>'Staff'];
        $mobile_calendar_view = 'Staff';
        break;
    case 'ticket':
        $config_type = 'ticket';
        $use_shifts = get_config($dbc, 'ticket_use_shifts');
        $wait_list = get_config($dbc, 'ticket_wait_list');
        $use_shift_tickets = get_config($dbc, 'ticket_use_shift_tickets');
        if($_GET['view'] != 'monthly') {
            $use_unbooked = get_config($dbc, 'ticket_use_unbooked');
        }
        $offline_mode = get_config($dbc, 'ticket_offline');
        $add_reminder = get_config($dbc, 'ticket_reminders');
        $teams = get_config($dbc, 'ticket_teams');
        $equipment_assignment = get_config($dbc, 'ticket_equip_assign');
        $weekly_start = get_config($dbc, 'ticket_weekly_start');
        $weekly_days = explode(',', get_config($dbc, 'ticket_weekly_days'));
        $monthly_numdays = get_config($dbc, 'ticket_monthly_numdays');
        $monthly_start = get_config($dbc, 'ticket_monthly_start');
        $monthly_days = explode(',', get_config($dbc, 'ticket_monthly_days'));
        $ticket_summary = get_config($dbc, 'ticket_ticket_summary');
        $ticket_summary_deleted = get_config($dbc, 'ticket_ticket_summary_deleted');
        $availability_indication = get_config($dbc, 'ticket_availability_indication');
        $sidebar_file = 'tickets_sidebar.php';
        $all_tickets_button = get_config($dbc, 'ticket_use_all_tickets');
        $staff_split_security = get_config($dbc, 'ticket_staff_split_security');
        $client_staff_freq = get_config($dbc, 'ticket_client_staff_freq');
        $client_draggable = get_config($dbc, 'ticket_client_draggable');
        $staff_summary = get_config($dbc, 'ticket_staff_summary');
        $ticket_summary_tab = get_config($dbc, 'ticket_ticket_summary_tab');
        $ticket_summary_tab_deleted = get_config($dbc, 'ticket_ticket_summary_tab_deleted');
        $client_tab = get_config($dbc, 'ticket_client_tab');
        $sidebar_filters = array_filter(explode(',',get_config($dbc, 'ticket_sidebar_filters')));
        $restrict_ticket_types = array_filter(explode(',',get_config($dbc, 'ticket_restrict_ticket_types')));
        $use_equipment = get_config($dbc, 'ticket_use_equipment');
        $equipment_category = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"))['equipment_category'];
        $equipment_categories = array_filter(explode(',', $equipment_category));
        if(empty($equipment_categories)) {
            $equipment_categories = array_filter(explode(',',get_config($dbc, 'equipment_tabs')));
        }
        if($use_equipment == 1) {
            $retrieve_collapse2 = 'collapse_equipment';
            $retrieve_contact2 = 'equipment';
        }

        $mobile_calendar_views = [''=>'Staff'];
        $mobile_calendar_view = 'Staff';
        break;
    case 'shift':
        $config_type = 'shift';
        $use_shifts = 1;
        $wait_list = 'shifts';
        $use_shift_tickets = '';
        $use_unbooked = '';
        $offline_mode = get_config($dbc, 'shift_offline');
        if($_GET['view'] != 'monthly') {
            $add_reminder = get_config($dbc, 'shift_reminders');
        }
        $teams = '';
        $equipment_assignment = '';
        $weekly_start = get_config($dbc, 'shift_weekly_start');
        $weekly_days = explode(',', get_config($dbc, 'shift_weekly_days'));
        $monthly_numdays = get_config($dbc, 'shift_monthly_numdays');
        $monthly_start = get_config($dbc, 'shift_monthly_start');
        $monthly_days = explode(',', get_config($dbc, 'shift_monthly_days'));
        $ticket_summary = '';
        $sidebar_file = 'shift_sidebar.php';
        $all_tickets_button = '';

        $mobile_calendar_views = [''=>'Staff'];
        $shift_client_type = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_shifts`"))['contact_category'];
        if(!empty($shift_client_type)) {
            $mobile_calendar_views['client'] = $shift_client_type;
        }
        $mobile_calendar_view = 'Staff';
        $selected_staff_icons = get_config($dbc, 'shift_selected_staff_icons');
        $selected_client_icons = get_config($dbc, 'shift_selected_client_icons');
        break;
    case 'event':
        $config_type = 'event';
        $use_shifts = '';
        $wait_list = '';
        $use_shift_tickets = '';
        $use_unbooked = '';
        $offline_mode = get_config($dbc, 'event_offline');
        $add_reminder = '';
        $teams = '';
        $equipment_assignment = '';
        $weekly_start = get_config($dbc, 'event_weekly_start');
        $weekly_days = explode(',', get_config($dbc, 'event_weekly_days'));
        $monthly_numdays = get_config($dbc, 'event_monthly_numdays');
        $monthly_start = get_config($dbc, 'event_monthly_start');
        $monthly_days = explode(',', get_config($dbc, 'event_monthly_days'));
        $ticket_summary = get_config($dbc, 'event_ticket_summary');
        $sidebar_file = 'event_sidebar.php';
        $all_tickets_button = '';

        $mobile_calendar_views = [''=>TICKET_TILE];
        $mobile_calendar_view = TICKET_TILE;
        break;
}
$calendar_start = (!empty($_GET['date']) ? $_GET['date'] : date('Y-m-d'));
switch($_GET['view']) {
    case 'daily':
        $date_string = date('F d, Y', strtotime($calendar_start));
        break;
    case 'weekly':
        if($weekly_start == 'Sunday') {
            $weekly_start = 1;
        } else {
            $weekly_start = 0;
        }
        $day = date('w', strtotime($calendar_start));
        $week_start_date = date('F j', strtotime($calendar_start.' -'.($day - 1 + $weekly_start).' days'));
        $week_end_date = date('F j, Y', strtotime($calendar_start.' -'.($day - 7 + $weekly_start).' days'));
        $date_string = date('M d', strtotime($week_start_date)).' - '.date('M d, Y', strtotime($week_end_date));
        break;
    case 'monthly':
        $date_string = date('F Y', strtotime($calendar_start));
        break;
}

$allowed_roles = [];
$allowed_ticket_types = [];
foreach(explode(',', ROLE) as $session_role) {
    $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_calendar_security` WHERE `role` = '$session_role' AND `calendar_type` ='".$_GET['type']."'"));
    $allowed_roles = array_merge($allowed_roles, explode(',', $field_config['allowed_roles']));
    $allowed_ticket_types = array_merge($allowed_ticket_types, explode(',', $field_config['allowed_ticket_types']));
}
if(!empty(array_filter($allowed_ticket_types)) && !empty($restrict_ticket_types)) {
    $allowed_ticket_types = array_intersect($allowed_ticket_types, $restrict_ticket_types);
} else if(!empty($restrict_ticket_types)) {
    $allowed_ticket_types = $restrict_ticket_types;
}
$allowed_roles = array_unique(array_filter($allowed_roles));
$allowed_ticket_types = array_unique(array_filter($allowed_ticket_types));
$allowed_roles_query = '';
$allowed_ticket_types_query = '';
if(!empty($allowed_roles)) {
    $allowed_roles_query = " AND (CONCAT(',',`role`,',') LIKE '%,".implode(",%' OR CONCAT(',',`role`,',') LIKE '%,", $allowed_roles).",%')";
}
if(!empty($allowed_ticket_types)) {
    if(in_array(get_config($dbc, 'default_ticket_type'), $allowed_ticket_types)) {
        $allowed_ticket_types[] = '';
    }
    $allowed_ticket_types_query = " AND IFNULL(`tickets`.`ticket_type`,'') IN ('".implode("','", $allowed_ticket_types)."')";
}

$calendar_types = explode(',',get_config($dbc, 'calendar_types'));
$edit_access = vuaed_visible_function($dbc, 'calendar_rook');
if($is_customer) {
    $edit_access = 0;
}
$ticket_view_access = tile_visible($dbc, 'ticket');
echo '<input type="hidden" name="edit_access" value="'.$edit_access.'">';
echo '<input type="hidden" name="ticket_view_access" value="'.$ticket_view_access.'">';
// CALENDAR DATES
$calendar_start = $_GET['date'];
if($calendar_start == '') {
    $calendar_start = date('Y-m-d');
} else {
    $calendar_start = date('Y-m-d', strtotime($calendar_start));
}

$day = date('w', strtotime($calendar_start));
$week_start_date_check = date('Y-m-d', strtotime($calendar_start.' -'.($day - 1 + $weekly_start).' days'));
$week_end_date_check = date('Y-m-d', strtotime($calendar_start.' -'.($day - 7 + $weekly_start).' days'));

$calendar_dates = [];
if($_GET['view'] == 'weekly') {
    for($i = 1; $i <= 7; $i++) {
        $calendar_date = date('Y-m-d', strtotime($calendar_start.' -'.($day - $i + $weekly_start).' days'));
        $day_of_week = date('l', strtotime($calendar_date));
        if(in_array($day_of_week, $weekly_days)) {
            $calendar_dates[] = $calendar_date;
        }
    }
} else if($_GET['view'] == 'daily') {
    $calendar_dates = [$calendar_start];
}

// COLLAPSE AND DATA VARIABLE FOR CONTACTID
if($_GET['type'] == 'schedule') {
    if($_GET['mode'] == 'staff') {
        $retrieve_collapse = 'collapse_staff';
        $retrieve_block_type = 'dispatch_staff';
        $retrieve_contact = 'staff';
    } else if($_GET['mode'] == 'contractors') {
        $retrieve_collapse = 'collapse_contractors';
        $retrieve_block_type = 'dispatch_staff';
        $retrieve_contact = 'staff';
    } else {
        $retrieve_collapse = 'collapse_equipment';
        $retrieve_block_type = 'equipment';
        $retrieve_contact = 'equipment';
    }
} else if($_GET['type'] == 'event') {
    $retrieve_collapse = 'category_accordions';
    $retrieve_block_type = '';
    $retrieve_contact = 'projectid';
} else if($_GET['type'] == 'shift' || $_GET['type'] == 'staff') {
    $retrieve_collapse = 'collapse_contact';
    $retrieve_block_type = '';
    $retrieve_contact = 'contact';
} else if($_GET['type'] == 'ticket' && $_GET['mode'] == 'client') {
    $retrieve_collapse = 'collapse_clients';
    $retrieve_block_type = '';
    $retrieve_contact = 'client';
} else {
    $retrieve_collapse = 'collapse_staff';
    $retrieve_block_type = '';
    $retrieve_contact = 'staff';
}
?>
<input type="hidden" id="retrieve_collapse" value="<?= $retrieve_collapse ?>">
<input type="hidden" id="retrieve_collapse2" value="<?= $retrieve_collapse2 ?>">
<input type="hidden" id="retrieve_block_type" value="<?= $retrieve_block_type ?>">
<input type="hidden" id="retrieve_contact" value="<?= $retrieve_contact ?>">
<input type="hidden" id="retrieve_contact2" value="<?= $retrieve_contact2 ?>">
<input type="hidden" id="calendar_view" value="<?= $_GET['view'] ?>">
<input type="hidden" id="calendar_mode" value="<?= $_GET['mode'] ?>">
<input type="hidden" id="calendar_start" value="<?= $calendar_start ?>">
<input type="hidden" id="calendar_dates" value='<?= json_encode($calendar_dates); ?>'>
<input type="hidden" id="calendar_type" value="<?= $_GET['type'] ?>">
<input type="hidden" id="calendar_config_type" value="<?= $config_type ?>">
<input type="hidden" id="calendar_auto_refresh" value="<?= $calendar_auto_refresh ?>">
<input type="hidden" id="calendar_check_shifts" value="<?= get_config($dbc, 'calendar_ticket_check_shifts') ?>">
<input type="hidden" id="calendar_check_days_off" value="<?= get_config($dbc, 'calendar_ticket_check_days_off') ?>">

<?php $ticket_config = ','.get_field_config($dbc, 'tickets').',';
$ticket_tabs_color_config = explode(',',get_config($dbc, 'ticket_tabs_color'));
$ticket_tabs_color = [];
foreach(explode(',',get_config($dbc, 'ticket_tabs')) as $type_i => $ticket_type) {
    $ticket_types[config_safe_str($ticket_type)] = $ticket_type;
    if($ticket_tabs_color_config[$type_i] == '#000000') {
        $ticket_tabs_color_config[$type_i] = '';
    }
    $ticket_tabs_color[config_safe_str($ticket_type)] = $ticket_tabs_color_config[$type_i];
}
foreach($ticket_types as $type_i => $type_label) {
    $ticket_config .= get_config($dbc, 'ticket_fields_'.$type_i).',';
}
?>
<input type="hidden" id="tickets_have_recurrence" value="<?= strpos($ticket_config, ',Create Recurrence Button,') !== FALSE ? 1 : 0 ?>">