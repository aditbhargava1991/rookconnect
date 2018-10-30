<?php $guest_access = true;
/* Auto archive Closed and Abandoned Estimates */
error_reporting(0);
include(substr(dirname(__FILE__), 0, -8).'database_connection.php');
include(substr(dirname(__FILE__), 0, -8).'function.php');

/* $date = date('Y-m-d');
if(date('d', strtotime($date)) === '01') {
    $lead_status_won = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE name='lead_status_won'"))['value'];
    $lead_status_lost = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE name='lead_status_lost'"))['value'];
    $result = mysqli_query($dbc, "UPDATE `sales` SET `deleted`='1' WHERE (`status`='$lead_status_won' OR `status`='$lead_status_lost') AND (`created_date` BETWEEN '". date('Y-m-d', strtotime('first day of previous month')) ."' AND '". date('Y-m-d', strtotime('last day of previous month')) ."')");
} */

$estimate_auto_archive = get_config($dbc, 'estimate_auto_archive');

if ( $estimate_auto_archive==1 ) {
    $estimate_auto_archive_days = get_config($dbc, 'estimate_auto_archive_days');
    if ( $estimate_auto_archive_days > 0 ) {
        $estimate_status_closed = get_config($dbc, 'estimate_status_closed');
        $estimate_status_closed_lower = strtolower(str_replace(' ', '', $estimate_status_closed));
        $estimate_status_abandoned = get_config($dbc, 'estimate_status_abandoned');
        $estimate_status_abandoned_lower = strtolower(str_replace(' ', '', $estimate_status_abandoned));
        $today_date = date('Y-m-d', strtotime(date('Y-m-d').' - '.$estimate_auto_archive_days.' days'));
        $old_estimates = mysqli_fetch_all(mysqli_query($dbc, "SELECT `estimateid` FROM `estimate` WHERE (`status`='$estimate_status_closed' OR `status`='$estimate_status_abandoned' OR `status`='$estimate_status_closed_lower' OR `status`='$estimate_status_abandoned_lower') AND `created_date`<='$today_date' AND `created_date`!='0000-00-00' AND `deleted`=0"), MYSQLI_ASSOC);
        foreach ($old_estimates as $old_estimate) {
            mysqli_query($dbc, "UPDATE `estimate` SET `deleted`=1 WHERE `estimateid`='". $old_estimate['estimateid'] ."'");
        }
    }
}
?>
