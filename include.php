<?php // Include Here Everything
error_reporting(0);
$_SERVER['page_load_info'] = 'Start of Script: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
session_start(['cookie_lifetime' => 518400]);
$_SERVER['page_load_info'] .= 'Session Started: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
include_once ('database_connection.php');
$_SERVER['page_load_info'] .= 'Database Connected: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
include_once ('function.php');
$_SERVER['page_load_info'] .= 'Functions Loaded: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
include_once ('global.php');
$_SERVER['page_load_info'] .= 'Globals Declared: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
session_write_close();
if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
	include_once ('header.php');
} else { ?>
	<script>
		$(document).ready(function() { 
			initInputs();
		});
	</script>
<?php }
$_SERVER['page_load_info'] .= 'Header Loaded: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
include_once ('output_functions.php');
$_SERVER['page_load_info'] .= 'Output Declared: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
include_once ('pagination.php');
$_SERVER['page_load_info'] .= 'Pagination Declared: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
include_once ('filter.php');
$_SERVER['page_load_info'] .= 'Filters Built: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
include_once ('email.php');
$_SERVER['page_load_info'] .= 'Email Initialized: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
if ( FOLDER_NAME=='reports' ) {
    include_once ('Reports/report_tiles_function.php');
}
include_once ('mobile_detect.php');
$_SERVER['page_load_info'] .= 'Mobile Detected: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
if($_SESSION['user_preferences']['loaded_time'] + 3600 < time()) {
	session_start(['cookie_lifetime' => 518400]);
	$_SESSION['user_preferences'] = get_user_settings();
	$_SERVER['page_load_info'] .= 'User Settings Updated: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
	$_SESSION['user_preferences']['loaded_time'] = time();
	session_write_close();
}
include_once ('user_font_settings.php');
$_SERVER['page_load_info'] .= 'User Font Initialized: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
if(!($_SESSION['tile_list_updated'] > 0)) {
	$no_display = true;
	include_once('tiles.php');
	unset($no_display);
	$_SERVER['page_load_info'] .= 'Updated Tile List: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
}
if($_SESSION['contactid'] > 0) {
	set_last_active($dbc, $_SESSION['contactid']);
	$_SERVER['page_load_info'] .= 'Activity Noted: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
}
include_once ('reload_cache.php');
$_SERVER['page_load_info'] .= 'Cache Loaded: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";

$rookconnect = get_software_name();
$scale_style = '';
$page_options = mysqli_query($dbc, "SELECT * FROM `page_options` WHERE `php_self`='".filter_var($_SERVER['PHP_SELF'],FILTER_SANITIZE_STRING)."' AND `contactid`='".$_SESSION['contactid']."'");
if(mysqli_num_rows($page_options) > 0) {
	if($page_options = mysqli_fetch_assoc($page_options)) {
		if($page_options['scale_width'] > 0) {
			$scale_style = 'width:'.$page_options['scale_width'].'%;';
		}
	}
}

// Get User's time zone offset
$_SESSION['time_offset'] = '';
$user_regions = get_contact($dbc, $_SESSION['contactid'], 'region');
$user_classes = get_contact($dbc, $_SESSION['contactid'], 'classification');
foreach(array_filter(explode(',',$user_regions)) as $user_region) {
    foreach(explode(',',get_config($dbc, 'contacts_region')) as $i => $region_name) {
        if($region_name == $user_region && $region_name != '') {
            $_SESSION['time_offset'] = explode(',',get_config($dbc, 'region_time_offset'))[$i];
        }
    }
}
foreach(array_filter(explode(',',$user_classes)) as $user_class) {
    foreach(explode(',',get_config($dbc, 'contacts_classification')) as $i => $class_name) {
        if($class_name == $user_class && $class_name != '') {
            $_SESSION['time_offset'] = explode(',',get_config($dbc, 'classification_time_offset'))[$i];
        }
    }
}
$offset = new DateTime();
if(empty($_SESSION['time_offset'])) {
    $_SESSION['time_offset'] = $offset->getOffset();
} else {
    $_SESSION['time_offset'] = get_offset_from_zone($_SESSION['time_offset']);
}
$offset = sprintf('%d:%02d', $offset->getOffset() / 3600, ($offset->getOffset() / 60) % 60);
$offset = $dbc->prepare("SET time_zone='$offset'");
$offset->execute();

if(strpos_any !== FALSE)
$_SERVER['page_load_info'] .= 'Start of Page: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
if(get_config($dbc, 'timesheet_force_end_midnight') > 0 && strtotime(get_config($dbc, 'timesheet_midnight_last_ended')) < strtotime(date('Y-m-d'))) {
	include_once('../Calendar/calendar_functions_inc.php');
	$midnight_end = true;
	$end_time_list = mysqli_query($dbc, "SELECT `contacts`.`contactid`, `time_cards`.`date`, `time_cards`.`time_cards_id` FROM `contacts` LEFT JOIN `time_cards` ON `contacts`.`contactid` = `time_cards`.`staff` WHERE `contacts`.`category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `contacts`.`status`>0 AND `contacts`.`deleted`=0 AND `timer_start` > 0 AND `time_cards`.`deleted`=0 AND `time_cards`.`staff` > 0 AND `type_of_time`='day_tracking' AND `date` != '".date('Y-m-d')."'");
	while($row = mysqli_fetch_assoc($end_time_list)) {
		$date = $row['date'];
		$day_of_week = date('l', strtotime($date));
		$time = strtotime($date.' 23:59:59');
		$hour = date('H:i', $time);
		$comment = 'Auto checked out after Midnight.';
		$time_minimum = get_config($dbc, 'ticket_min_hours');
		$time_interval = get_config($dbc, 'timesheet_hour_intervals');
		$highlight_query = ", `highlight`=1";
		$midnight_query = " AND `date` = '".$date."'";
		$id_query = " AND `time_cards_id` = '".$row['time_cards_id']."'";

		$staff = $row['contactid'];
		include('../Timesheet/end_day.php');
	}
	set_config($dbc, 'timesheet_midnight_last_ended', date('Y-m-d'));
}
?>