<?php include_once('../include.php');
checkAuthorised('calendar_rook');
include_once('calendar_settings_inc.php');
include_once('calendar_functions_inc.php');
$calendar_start = $_GET['date'];
if($calendar_start == '') {
	$calendar_start = date('Y-m-d');
} else {
	$calendar_start = date('Y-m-d', strtotime($calendar_start));
}
$equipment_category = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"))['equipment_category'];
$equipment_categories = array_filter(explode(',', $equipment_category));
if(empty($equipment_categories) || count($equipment_categories) > 1) {
    $equipment_category = 'Equipment';
}
$equip_cat_query = '';
if(count($equipment_categories) > 0) {
    $equip_cat_query = " AND `equipment`.`category` IN ('".implode("','", $equipment_categories)."')";
}
$client_type = get_config($dbc, 'scheduling_client_type');
$calendar_type = get_config($dbc, 'scheduling_wait_list');
if($calendar_type == 'ticket_multi') {
	$calendar_type = 'ticket';
}

$weekly_start = get_config($dbc, 'scheduling_weekly_start');
if($weekly_start == 'Sunday') {
	$weekly_start = 1;
} else {
	$weekly_start = 0;
}
$day = date('w', strtotime($calendar_start));
$week_start_date = date('F j', strtotime($calendar_start.' -'.($day - 1 + $weekly_start).' days'));
$week_end_date = date('F j, Y', strtotime($calendar_start.' -'.($day - 7 + $weekly_start).' days'));

$weekly_days = explode(',',get_config($dbc, 'scheduling_weekly_days'));

?>

<script src="appointments.js"></script>
<input type="hidden" name="calendar_date" value="<?= date('Y-m-d', strtotime($calendar_start)) ?>">
<?php $calendar_table = [];
$day_start = get_config($dbc, 'scheduling_day_start');
$day_end = get_config($dbc, 'scheduling_day_end');
$day_period = get_config($dbc, 'scheduling_increments');
$current_row = strtotime($day_start);
$calendar_table[0][0]['title'] = "Time";
if(get_config($dbc, 'scheduling_calendar_notes') == '1') { $calendar_table[0][0]['notes'] = "Notes"; }
$calendar_table[0][0]['warnings'] = "Warnings";
$appointment_calendar = 'weekly';
while($current_row <= strtotime($day_end)) {
	$calendar_table[0][0][] = date('g:i a', $current_row);
	$current_row = strtotime('+'.$day_period.' minutes', $current_row);
}
$column_id = 0;
if($_GET['mode'] == 'staff') {
	$calendar_type = 'dispatch_staff';
}
$contact_id = '';
include('load_calendar_table.php');
include('load_calendar_table_display.php');