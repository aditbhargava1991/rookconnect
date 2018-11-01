<?php
include('../include.php');
include 'config.php';
include_once('../Timesheet/reporting_functions.php');

$search_staff = filter_var($_GET['search_staff'],FILTER_SANITIZE_STRING);
$search_start_date = filter_var($_GET['search_start_date'],FILTER_SANITIZE_STRING);
$search_end_date = filter_var($_GET['search_end_date'],FILTER_SANITIZE_STRING);
$search_position = filter_var($_GET['search_position'],FILTER_SANITIZE_STRING);
$search_project = filter_var($_GET['search_project'],FILTER_SANITIZE_STRING);
$search_ticket = filter_var($_GET['search_ticket'],FILTER_SANITIZE_STRING);
$override_value_config = '';
if(!empty($_GET['value_config'])) {
    $override_value_config = $_GET['value_config'];
}

echo get_hours_report($dbc, $search_staff, $search_start_date, $search_end_date, $search_position, $search_project, $search_ticket, 'to_xls', $config['hours_types'], $override_value_config);

header('Content-type: application/excel');
header("Content-Disposition: attachment; filename=timesheet_reporting_".date('d-m-Y').".xls");
