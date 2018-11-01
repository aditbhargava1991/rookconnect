<?php
include ('../database_connection.php');
include ('../function.php');
include ('../global.php');
include ('../email.php');
$fill = empty($_GET['fill']) ? '' : $_GET['fill'];
$user_id = $_SESSION['contactid'];
$user_full_name = get_contact($dbc, $user_id);

if($fill == 'tile_sort') {
	$current = $_POST['current'];
	$previous = $_POST['previous'];
	$tile_sort = '*#*'.(mysqli_fetch_array(mysqli_query($dbc,"SELECT `tile_sort` FROM `contacts_tile_sort` WHERE `contactid`='".$_SESSION['contactid']."'"))['tile_sort']).'*#*';
	$tile_sort = str_replace('*#*'.$current,'',$tile_sort);
	if($previous != '') {
		$tile_sort = trim(str_replace('*#*'.$previous.'*#*','*#*'.$previous.'*#*'.$current.'*#*','*#*'.$tile_sort),'*#*');
	}
	else {
		$tile_sort = trim('*#*'.$current.'*#*'.$tile_sort,'*#*');
	}
	$sql = "UPDATE `contacts_tile_sort` SET `tile_sort`='$tile_sort' WHERE `contactid`='".$_SESSION['contactid']."'";
	$result = mysqli_query($dbc, $sql);
}
else if($fill == 'tile_save') {
	$tile_sort = trim(filter_var($_POST['tile_list'],FILTER_SANITIZE_STRING),'*#*');
	$sql = "INSERT INTO `contacts_tile_sort` (`contactid`) SELECT '".$_SESSION['contactid']."' FROM (SELECT COUNT(*) `num_rows` FROM `contacts_tile_sort` WHERE `contactid`='".$_SESSION['contactid']."') ROWS WHERE ROWS.num_rows = '0'";
	$result = mysqli_query($dbc, $sql);
	$sql = "UPDATE `contacts_tile_sort` SET `tile_sort`='$tile_sort' WHERE `contactid`='".$_SESSION['contactid']."'";
	$result = mysqli_query($dbc, $sql);
}
else if($fill == 'dashboard_sort') {
	$dashboard = $_POST['dashboard'];
	$current = $_POST['current'];
	$previous = $_POST['previous'];
	$tile_sort = '*#*'.(mysqli_fetch_array(mysqli_query($dbc,"SELECT `tile_sort` FROM `tile_dashboards` WHERE `dashboard_id`='$dashboard'"))['tile_sort']).'*#*';
	$tile_sort = str_replace('*#*'.$current,'',$tile_sort);
	if($previous != '') {
		$tile_sort = trim(str_replace('*#*'.$previous.'*#*','*#*'.$previous.'*#*'.$current.'*#*','*#*'.$tile_sort),'*#*');
	} else {
		$tile_sort = trim('*#*'.$current.'*#*'.$tile_sort,'*#*');
	}
	$history = "";
	if($source == 'all') {
		$history = "$current tile added by $user_full_name.<br />";
	}
	$result = mysqli_query($dbc, "UPDATE `tile_dashboards` SET `tile_sort`='$tile_sort', `history`=CONCAT(IFNULL(`history`,''), '$history') WHERE `dashboard_id`='$dashboard'");

	$source = $_POST['source'];
	if($source != 'all' && $source != $dashboard) {
		$tile_sort = '*#*'.(mysqli_fetch_array(mysqli_query($dbc,"SELECT `tile_sort` FROM `tile_dashboards` WHERE `dashboard_id`='$source'"))['tile_sort']).'*#*';
		$tile_sort = trim(str_replace('*#*'.$current,'',$tile_sort),'*#*');
		$result = mysqli_query($dbc, "UPDATE `tile_dashboards` SET `tile_sort`='$tile_sort' WHERE `dashboard_id`='$source'");
	}
}
else if($fill == 'dashboard_add') {
	$name = filter_var($_POST['dashboard'], FILTER_SANITIZE_STRING);
	switch($name) {
		case 'Admin':
			$tile_sort = 'software_config*#*staff*#*security*#*passwords';
			break;
		case 'HR':
			$tile_sort = 'hr*#*certificate*#*orientation*#*ops_manual*#*policy_procedure';
			break;
		case 'Sales':
			$tile_sort = 'sales*#*calllog*#*sales_order*#*marketing_material*#*infogathering';
			break;
		default:
			$tile_sort = '';
			break;
	}
	mysqli_query($dbc, "INSERT INTO `tile_dashboards` (`name`, `tile_sort`, `history`) VALUES ('$name', '$tile_sort', 'Added by $user_full_name.<br />')");
	if($_POST['assigned'] == 'true') {
		$id = mysqli_insert_id($dbc);
		mysqli_query($dbc, "UPDATE `tile_dashboards` SET `assigned_users`='".$_SESSION['contactid']."' WHERE `dashboard_id`='$id'");
	}
}
else if($fill == 'dashboard_remove') {
    $date_of_archival = date('Y-m-d');
	$dashboard = $_POST['dashboard'];
	mysqli_query($dbc, "UPDATE `tile_dashboards` SET `deleted`=1, `date_of_archival` = '$date_of_archival', `history`=CONCAT(IFNULL(`history`,''), 'Removed by $user_full_name.<br />') WHERE `dashboard_id`='$dashboard'");
}
else if($fill == 'dashboard_rename') {
	$dashboard = $_POST['dashboard'];
	$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "UPDATE `tile_dashboards` SET `history`=CONCAT(IFNULL(`history`,''), 'Renamed from ',`name`,' to $name by $user_full_name.<br />'), `name`='$name' WHERE `dashboard_id`='$dashboard'");
}
else if($fill == 'dashboard_default_levels') {
	$dashboard = $_POST['dashboard'];
	$levels = $_POST['levels'];
	mysqli_query($dbc, "UPDATE `tile_dashboards` SET `default_levels`=',$levels,' WHERE `dashboard_id`='$dashboard'");
}
else if($fill == 'dashboard_restrict') {
	$dashboard = $_POST['dashboard'];
	$levels = $_POST['levels'];
	mysqli_query($dbc, "UPDATE `tile_dashboards` SET `restrict_levels`=',$levels,' WHERE `dashboard_id`='$dashboard'");
}
else if($fill == 'show_all_tiles_level') {
	$levels = $_POST['levels'];
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'show_all_tiles_level' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='show_all_tiles_level') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`=',$levels,' WHERE `name`='show_all_tiles_level'");
}
else if($fill == 'dashboard_default') {
	$dashboard = $_POST['dashboard'];
	$tile_sort = trim(filter_var($_POST['tile_list'],FILTER_SANITIZE_STRING),'*#*');
	mysqli_query($dbc, "INSERT INTO `contacts_tile_sort` (`contactid`, `tile_sort`) SELECT '$user_id', '$tile_sort' FROM (SELECT COUNT(*) `num_rows` FROM `contacts_tile_sort` WHERE `contactid`='$user_id') ROWS WHERE ROWS.num_rows = '0'");
	mysqli_query($dbc, "UPDATE `contacts_tile_sort` SET `default_dashboard`='$dashboard' WHERE `contactid`='$user_id'");
}
else if($fill == 'display_pref') {
	set_user_settings($dbc, $_GET['name'], $_GET['value']);
}
else if($fill == 'system_display') {
	set_config($dbc, $_POST['name'], $_POST['value']);
}
else if($fill == 'font_type') {
	$font_type = filter_var(htmlspecialchars($_POST['value']),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `user_settings` (`contactid`, `font_type`) SELECT '$user_id', '$font_type' FROM (SELECT COUNT(*) `num_rows` FROM `user_settings` WHERE `contactid`='$user_id') ROWS WHERE ROWS.num_rows = '0'");
	mysqli_query($dbc, "UPDATE `user_settings` SET `font_type` = '$font_type' WHERE `contactid` = '$user_id'");
}
else if($fill == 'font_size') {
	$font_size = filter_var(htmlspecialchars($_POST['value']),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `user_settings` (`contactid`, `font_size`) SELECT '$user_id', '$font_size' FROM (SELECT COUNT(*) `num_rows` FROM `user_settings` WHERE `contactid`='$user_id') ROWS WHERE ROWS.num_rows = '0'");
	mysqli_query($dbc, "UPDATE `user_settings` SET `font_size` = '$font_size' WHERE `contactid` = '$user_id'");
}
else if($fill == 'ticket_slider') {
	$field_name = $_POST['field_name'];
	$value = $_POST['value'];
	$contactid = $_POST['contactid'];
	set_user_settings($dbc, $field_name, $value);
}
else if($fill == 'notifications') {
	$enabled = filter_var($_POST['enabled'],FILTER_SANITIZE_STRING);
	$frequency = filter_var($_POST['frequency'],FILTER_SANITIZE_STRING);
	$alert_hour = filter_var($_POST['alert_hour'],FILTER_SANITIZE_STRING);
	$alert_days = filter_var($_POST['alert_days'],FILTER_SANITIZE_STRING);
	$alerts = filter_var($_POST['alerts'],FILTER_SANITIZE_STRING);
	$software_default = filter_var($_POST['software_default'],FILTER_SANITIZE_STRING);
	$contactid = filter_var($_POST['contactid'],FILTER_SANITIZE_STRING);

	if($software_default == 1) {
		mysqli_query($dbc, "INSERT INTO `field_config_email_alerts` (`software_default`) SELECT 1 FROM (SELECT COUNT(*) rows FROM `field_config_email_alerts` WHERE `software_default` = 1) num WHERE num.rows=0");
		$query_filter = " `software_default` = 1";
		
		mysqli_query($dbc, "UPDATE `journal_notifications` SET `email_sent` = 1 WHERE `contactid` NOT IN (SELECT `contactid` FROM `field_config_email_alerts` WHERE `contactid` > 0)");
	} else if($contactid > 0) {
		mysqli_query($dbc, "INSERT INTO `field_config_email_alerts` (`contactid`) SELECT '$contactid' FROM (SELECT COUNT(*) rows FROM `field_config_email_alerts` WHERE `contactid` = '$contactid') num WHERE num.rows=0");
		$query_filter = " `contactid` = '$contactid'";

		mysqli_query($dbc, "UPDATE `journal_notifications` SET `email_sent` = 1 WHERE `contactid` = '$contactid'");
	}

	if(!empty($query_filter)) {
		echo $query_update = "UPDATE `field_config_email_alerts` SET `enabled` = '$enabled', `frequency` = '$frequency', `alert_hour` = '$alert_hour', `alert_days` = '$alert_days', `alerts` = '$alerts' WHERE ".$query_filter;
		mysqli_query($dbc, $query_update);
	}
}else if($fill == 'desktop_notifications') {
    $enabled = filter_var($_POST['enabled'],FILTER_SANITIZE_STRING);
    mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$enabled' WHERE `name`='desktop_notification_enabled'");
}else if($fill == 'desktop_notifications_user') {
    $enabled = filter_var($_POST['enabled'],FILTER_SANITIZE_STRING);
    $contactid = filter_var($_POST['contactid'],FILTER_SANITIZE_STRING);
    mysqli_query($dbc, "UPDATE `contacts` SET `desktop_notification`='$enabled' WHERE `contactid`='$contactid'");
}