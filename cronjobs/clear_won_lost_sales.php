<?php $guest_access = true;
/* Auto archive won and lost Sales Leads */
error_reporting(0);
include(substr(dirname(__FILE__), 0, -8).'database_connection.php');
include(substr(dirname(__FILE__), 0, -8).'function.php');
ob_clean();

/* $date = date('Y-m-d');
if(date('d', strtotime($date)) === '01') {
    $lead_status_won = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE name='lead_status_won'"))['value'];
    $lead_status_lost = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE name='lead_status_lost'"))['value'];
    $result = mysqli_query($dbc, "UPDATE `sales` SET `deleted`='1' WHERE (`status`='$lead_status_won' OR `status`='$lead_status_lost') AND (`created_date` BETWEEN '". date('Y-m-d', strtotime('first day of previous month')) ."' AND '". date('Y-m-d', strtotime('last day of previous month')) ."')");
} */

$sales_auto_archive = get_config($dbc, 'sales_auto_archive');

if ( $sales_auto_archive==1 ) {
    $sales_auto_archive_days = get_config($dbc, 'sales_auto_archive_days');
    if ( $sales_auto_archive_days > 0 ) {
        $lead_status_won = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE name='lead_status_won'"))['value'];
        $lead_status_lost = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE name='lead_status_lost'"))['value'];
        $today_date = date('Y-m-d', strtotime(date('Y-m-d').' - '.$sales_auto_archive_days.' days'));
        $old_sales = mysqli_fetch_all(mysqli_query($dbc, "SELECT `salesid` FROM `sales` WHERE ((`status`='$lead_status_won' AND '$lead_status_won' != '') OR (`status`='$lead_status_lost' AND '$lead_status_lost' != '')) AND `status_date`<='$today_date' AND `deleted`=0"), MYSQLI_ASSOC);
        foreach ($old_sales as $old_sale) {
            mysqli_query($dbc, "UPDATE `sales` SET `deleted`=1 WHERE `salesid`='". $old_sale['salesid'] ."'");
			mysqli_query($dbc, "INSERT INTO `sales_history` (`salesid`,`history`) VALUES (". $old_sale['salesid'] .",'Sales Lead Automatically Archived')");
        }
    }
}
?>
