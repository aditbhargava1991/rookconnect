<?php
include ('../database_connection.php');
include ('../function.php');
date_default_timezone_set('America/Denver');

if($_GET['fill'] == 'update_comm_status') {
    $email_communicationid = $_GET['email_communicationid'];
    $status = $_GET['status'];
    $query_update_employee = "UPDATE `email_communication` SET status = '$status' WHERE email_communicationid='$email_communicationid'";
    $result_update_employee = mysqli_query($dbc, $query_update_employee);
}
if($_GET['fill'] == 'update_comm_followup') {
    $email_communicationid = $_GET['email_communicationid'];
    $by = $_GET['by'];
    $query_update_employee = "UPDATE `email_communication` SET follow_up_by = '$by' WHERE email_communicationid='$email_communicationid'";
    $result_update_employee = mysqli_query($dbc, $query_update_employee);
}
if($_GET['fill'] == 'update_comm_followup_date') {
    $email_communicationid = $_GET['email_communicationid'];
    $date = $_GET['date'];
    $query_update_employee = "UPDATE `email_communication` SET `follow_up_date` = '$date' WHERE email_communicationid='$email_communicationid'";
    $result_update_employee = mysqli_query($dbc, $query_update_employee);
}
if($_GET['fill'] == 'starttimer') {
	$comm_id = $_GET['comm_id'];
    $start_time = time();

    $start_timer_time = date('g:i A');
	$created_date = date('Y-m-d');
    $created_by = $_GET['login_contactid'];

    $query_insert_client_doc = "INSERT INTO `email_communication_timer` (`communication_id`, `timer_type`, `start_time`, `created_date`, `created_by`, `start_timer_time`) VALUES ('$comm_id', 'Work', '$start_timer_time', '$created_date', '$created_by', '$start_time')";
    $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
}

if($_GET['fill'] == 'pausetimer') {
	$comm_id = $_GET['comm_id'];
    $timer = $_GET['timer_value'];
    $start_time = time();
    $created_date = date('Y-m-d');
    $created_by = $_GET['login_contactid'];
    $end_time = date('g:i A');
    if($timer != '0' && $timer != '00:00:00' && $timer != '') {
        $query_update_ticket = "UPDATE `email_communication_timer` SET `end_time` = '$end_time', `timer` = '$timer' WHERE `communication_id` = '$comm_id' AND created_by='$created_by' AND created_date='$created_date' AND timer_type='Work' AND end_time IS NULL";
        $result_update_ticket = mysqli_query($dbc, $query_update_ticket);

        $query_insert_client_doc = "INSERT INTO `email_communication_timer` (`communication_id`, `timer_type`, `start_time`, `created_date`, `created_by`, `start_timer_time`) VALUES ('$comm_id', 'Break', '$end_time', '$created_date', '$created_by', '$start_time')";
        $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
		insert_day_overview($dbc, $created_by, 'Communication', date('Y-m-d'), '', 'Added time to Communication #'.$comm_id.' - '.$timer);
    }
}

if($_GET['fill'] == 'resumetimer') {
	$comm_id = $_GET['comm_id'];
    $timer = $_GET['timer_value'];
    $start_time = time();
    $created_date = date('Y-m-d');
    $created_by = $_GET['login_contactid'];
    $end_time = date('g:i A');
    if($timer != '0' && $timer != '00:00:00' && $timer != '') {
        $query_update_ticket = "UPDATE `email_communication_timer` SET `end_time` = '$end_time', `timer` = '$timer' WHERE `communication_id` = '$comm_id' AND created_by='$created_by' AND created_date='$created_date' AND timer_type='Break' AND end_time IS NULL";
        $result_update_ticket = mysqli_query($dbc, $query_update_ticket);

        $query_insert_client_doc = "INSERT INTO `email_communication_timer` (`communication_id`, `timer_type`, `start_time`, `created_date`, `created_by`, `start_timer_time`) VALUES ('$comm_id', 'Work', '$end_time', '$created_date', '$created_by', '$start_time')";
        $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
    }
}

/* Save Settings */
if($_GET['fill'] == 'save_settings_internal_fields') {
    $internal_communication = filter_var(implode(',', $_GET['internal_communication']),FILTER_SANITIZE_STRING);
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`fieldconfigid`) AS `fieldconfigid` FROM `field_config`"));
    if($get_field_config['fieldconfigid'] > 0) {
        mysqli_query($dbc, "UPDATE `field_config` SET internal_communication = '$internal_communication' WHERE `fieldconfigid` = 1");
    } else {
        mysqli_query($dbc, "INSERT INTO `field_config` (`internal_communication`) VALUES ('$internal_communication')");
    }
}
if($_GET['fill'] == 'save_settings_external_fields') {
    $external_communication = filter_var(implode(',', $_GET['external_communication']),FILTER_SANITIZE_STRING);
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`fieldconfigid`) AS `fieldconfigid` FROM `field_config`"));
    if($get_field_config['fieldconfigid'] > 0) {
        mysqli_query($dbc, "UPDATE `field_config` SET external_communication = '$external_communication' WHERE `fieldconfigid` = 1");
    } else {
        mysqli_query($dbc, "INSERT INTO `field_config` (`external_communication`) VALUES ('$external_communication')");
    }
}
if($_GET['fill'] == 'save_settings_internal_dashboard') {
    $internal_communication_dashboard = filter_var(implode(',', $_GET['internal_communication_dashboard']),FILTER_SANITIZE_STRING);
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`fieldconfigid`) AS `fieldconfigid` FROM `field_config`"));
    if($get_field_config['fieldconfigid'] > 0) {
        mysqli_query($dbc, "UPDATE `field_config` SET internal_communication_dashboard = '$internal_communication_dashboard' WHERE `fieldconfigid` = 1");
    } else {
        mysqli_query($dbc, "INSERT INTO `field_config` (`internal_communication_dashboard`) VALUES ('$internal_communication_dashboard')");
    }
}
if($_GET['fill'] == 'save_settings_external_dashboard') {
    $external_communication_dashboard = filter_var(implode(',', $_GET['external_communication_dashboard']),FILTER_SANITIZE_STRING);
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`fieldconfigid`) AS `fieldconfigid` FROM `field_config`"));
    if($get_field_config['fieldconfigid'] > 0) {
        mysqli_query($dbc, "UPDATE `field_config` SET external_communication_dashboard = '$external_communication_dashboard' WHERE `fieldconfigid` = 1");
    } else {
        mysqli_query($dbc, "INSERT INTO `field_config` (`external_communication_dashboard`) VALUES ('$external_communication_dashboard')");
    }
}
if($_GET['fill'] == 'save_settings_log_dashboard') {
    $log_communication_dashboard = filter_var(implode(',', $_GET['log_communication_dashboard']),FILTER_SANITIZE_STRING);
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`fieldconfigid`) AS `fieldconfigid` FROM `field_config`"));
    if($get_field_config['fieldconfigid'] > 0) {
        mysqli_query($dbc, "UPDATE `field_config` SET log_communication_dashboard = '$log_communication_dashboard' WHERE `fieldconfigid` = 1");
    } else {
        mysqli_query($dbc, "INSERT INTO `field_config` (`log_communication_dashboard`) VALUES ('$log_communication_dashboard')");
    }
}
?>