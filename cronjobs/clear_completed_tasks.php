<?php $guest_access = true;
/* Auto archive old tasks */
error_reporting(0);
include(substr(dirname(__FILE__), 0, -8).'database_connection.php');
include(substr(dirname(__FILE__), 0, -8).'function.php');
ob_clean();

/* $date = date('Y-m-d');
if(date('d', strtotime($date)) === '01') {
    $task_statuses = explode(',',get_config($dbc, 'task_status'));
    $status_complete = $task_statuses[count($task_statuses) - 1];
    $result = mysqli_query($dbc, "UPDATE `tasklist` SET `deleted`='1' WHERE `status`='$status_complete'");
} */

$tasklist_auto_archive = get_config($dbc, 'tasklist_auto_archive');

if($tasklist_auto_archive == 1) {
    $tasklist_auto_archive_days = get_config($dbc, 'tasklist_auto_archive_days');
    if($tasklist_auto_archive_days > 0) {
        $task_statuses = explode(',',get_config($dbc, 'task_status'));
        $status_complete = $task_statuses[count($task_statuses) - 1];
        $today_date = date('Y-m-d', strtotime(date('Y-m-d').' - '.$tasklist_auto_archive_days.' days'));
        $old_tasks = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `status` = '$status_complete' AND `status_date` <= '$today_date' AND `status_date` != '0000-00-00' AND `deleted` = 0"),MYSQLI_ASSOC);
        foreach ($old_tasks as $old_task) {
           $date_of_archival = date('Y-m-d');
         mysqli_query($dbc, "UPDATE `tasklist` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `tasklistid` = '".$old_task['tasklistid']."'");
        }
    }
}
?>
