<?php
include_once('../include.php');
ob_clean();

if($_GET['action'] == 'save_config') {
	$field = $_POST['field'];
	$value = filter_var(htmlentities($_POST['value']),FILTER_SANITIZE_STRING);
	set_config($dbc, $field, $value);
} else if($_GET['action'] == 'ticket_status_color') {
	$status = $_POST['status'];
    $color_code = $_POST['color'];
    mysqli_query($dbc, "INSERT INTO `field_config_ticket_status_color` (`status`) SELECT '$status' FROM (SELECT COUNT(*) rows FROM `field_config_ticket_status_color` WHERE `status` = '$status') num WHERE num.rows = 0");
    mysqli_query($dbc, "UPDATE `field_config_ticket_status_color` SET `color` = '$color_code' WHERE `status` = '$status'");
}