<?php
/*
 * This should be called from everywhere there is a quick action to add reminders
 * Accept the Tile name in a $_GET['tile']
 */

include_once('include.php');
checkAuthorised();
$html = '';

//$id = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
if(isset($_POST['submit'])) {
	$contactid = $_SESSION['contactid'];
	$tile = filter_var($_POST['tile'],FILTER_SANITIZE_STRING);
    $id = preg_replace('/[^0-9]/', '', $_POST['id']);
	$timer_value = filter_var($_POST['timer_value'],FILTER_SANITIZE_STRING);
    $timer_date = date('Y-m-d');

    switch ($tile) {
        case 'projects':
            $projectid = $id;

            if($timer_value != '0' && $timer_value != '00:00:00' && $timer_value != '') {
                mysqli_query($dbc, "INSERT INTO `project_timer` (`projectid`, `staff`, `today_date`, `timer_value`) VALUES ('$projectid', '$contactid', '$timer_date', '$timer_value')");
                mysqli_query($dbc, "INSERT INTO `time_cards` (`projectid`,`staff`,`date`,`type_of_time`,`total_hrs`,`timer_tracked`,`comment_box`) VALUES ('$projectid','$contactid','$timer_date','Regular Hrs.','".((strtotime($timer_value) - strtotime('00:00:00')) / 3600)."','0','Time Added on Project #$projectid')");
                insert_day_overview($dbc, $contactid, 'Project', $timer_date, '', "Updated Project #$projectid - Added Time : $timer_value");
            }
            break;

        case 'tasks':
            $taskid = $id;
            $end_time = date('h:i A');

            if($timer_value != '0' && $timer_value != '00:00:00' && $timer_value != '') {
                //$query_add_time = "INSERT INTO `tasklist_time` (`tasklistid`, `work_time`, `src`, `contactid`, `timer_date`) VALUES ('$taskid', '$timer_value', 'A', '$contactid', '$timer_date')";

                $query_add_time = "UPDATE `tasklist_time` SET `end_time` = '$end_time', `work_time`='$timer_value' WHERE `tasklistid`='$taskid' AND `contactid` = '$contactid' AND `timer_date` = '$timer_date' AND `src` = 'A' AND `start_time` IS NOT NULL AND `end_time` IS NULL";
                $result_add_time = mysqli_query($dbc, $query_add_time);

                insert_day_overview($dbc, $contactid, 'Task', date('Y-m-d'), '', "Updated Task #$taskid - Added Time : $timer_value");

                $query_update_time = "UPDATE `tasklist` SET `work_time`=ADDTIME(`work_time`,'$timer_value') WHERE `tasklistid`='$taskid'";
                $result_update_time = mysqli_query($dbc, $query_update_time);

                $query_add_time = "INSERT INTO `time_cards` (`staff`, `date`, `type_of_time`, `total_hrs`, `comment_box`) VALUES ('$contactid', '$timer_date', 'Regular Hrs.', '".((strtotime($timer_value) - strtotime('00:00:00'))/3600)."', 'Time Added on Task #$taskid')";
                $result_add_time = mysqli_query($dbc, $query_add_time);

			    $note = '<em>Time added by '.get_contact($dbc, $_SESSION['contactid']).' [PROFILE '.$_SESSION['contactid'].']: '.$timer_value.'</em>';
			    mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `comment`, `created_by`, `created_date`) VALUES ('$refid','".filter_var(htmlentities($note),FILTER_SANITIZE_STRING)."','".$_SESSION['contactid']."','".date('Y-m-d')."')");

                $contactid = $_SESSION['contactid'];
                $created_date = date('Y-m-d');
                $reply = 'Tracked time: '.$timer_value;
                $insert = mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `created_by`, `created_date`, `comment`) VALUES ('$taskid', '$contactid', '$created_date', '$reply')");
            }

            break;

        case 'email':
            $communication_id = $id;

            if($timer_value != '0' && $timer_value != '00:00:00' && $timer_value != '') {
                mysqli_query($dbc, "INSERT INTO `email_communication_timer` (`communication_id`, `timer`, `timer_type`, `created_by`, `created_date`) VALUES ('$communication_id', '$timer_value', 'Work', '$contactid', '$timer_date')");
                mysqli_query($dbc, "INSERT INTO `time_cards` (`staff`, `date`, `type_of_time`, `total_hrs`, `timer_tracked`, `comment_box`) VALUES ('$contactid', '$timer_date', 'Regular Hrs.', '".((strtotime($timer_value) - strtotime('00:00:00')) / 3600)."', '0', 'Time Tracked on Email')");
                insert_day_overview($dbc, $contactid, 'Email Communication', $timer_date, '', "Tracked Time : $timer_value");
            }
            break;

        case 'sales':
            $salesid = $id;
            $end_time = date('h:i A');

            if($timer_value != '0' && $timer_value != '00:00:00' && $timer_value != '') {
                $id = filter_var($salesid ,FILTER_SANITIZE_STRING);
                $time = filter_var($timer_value ,FILTER_SANITIZE_STRING);
                $dbc->query("INSERT INTO `time_cards` (`salesid`,`staff`,`date`,`total_hrs`,`type_of_time`,`comment_box`) VALUES ('$id','{$_SESSION['contactid']}',DATE(NOW()),TIME_TO_SEC('$time')/3600,'Regular Hrs.','Time added from Sales Lead $id')");

        case 'ticket':
            $ticketid = $id;
            $end_time = date('h:i A');

            if($timer_value != '0' && $timer_value != '00:00:00' && $timer_value != '') {

                $query_add_time = "UPDATE `ticket_time` SET `end_time` = '$end_time', `timer`='$timer_value' WHERE `ticketid`='$ticketid' AND `contactid` = '$contactid' AND `timer_date` = '$timer_date' AND `src` = 'A' AND `start_time` IS NOT NULL AND `end_time` IS NULL";
                $result_add_time = mysqli_query($dbc, $query_add_time);

                $query_add_time = "INSERT INTO `time_cards` (`staff`, `date`, `type_of_time`, `total_hrs`, `comment_box`) VALUES ('$contactid', '$timer_date', 'Regular Hrs.', '".((strtotime($timer_value) - strtotime('00:00:00'))/3600)."', 'Time Added on Ticket #$ticketid')";
                $result_add_time = mysqli_query($dbc, $query_add_time);

                $note = '<em>Time added by '.get_contact($dbc, $_SESSION['contactid']).' [PROFILE '.$_SESSION['contactid'].']: '.$timer_value.'</em>';
                mysqli_query($dbc, "INSERT INTO `ticket_comment` (`ticketid`, `comment`, `created_by`, `created_date`) VALUES ('$ticketid','".filter_var(htmlentities($note),FILTER_SANITIZE_STRING)."','".$_SESSION['contactid']."','".date('Y-m-d')."')");

            }

            break;

        default:
            break;
    }
} ?>

<script>
$(document).ready(function() {

    /* Timer */
    $('.start-timer-btn').on('click', function() {
        $(this).closest('div').find('.timer').timer({
            editable: true
        });
        $(this).addClass('hidden');
        $(this).next('.stop-timer-btn').removeClass('hidden');
    });


    $('.start-timer-btn').on('click', function() {
        var taskid = $(this).data('id');
	    var tile_name = $('[name="tile"]').val();

        if(tile_name == 'tasks') {
            var contactid = '<?= $_SESSION['contactid'] ?>';
            if (taskid!='' && typeof taskid!='undefined') {
                $.ajax({
                    type: "GET",
                    url: "Tasks_Updated/task_ajax_all.php?fill=start_timer&taskid="+taskid+"&contactid="+contactid,
                    dataType: "html",
                });
            }
        }
        if(tile_name == 'ticket') {
        	var id = $('input[name="id"]').val()
            var contactid = '<?= $_SESSION['contactid'] ?>';
            if ( id!='' && typeof id!='undefined') {
                $.ajax({
                    type: "GET",
                    url: "Ticket/ticket_ajax_all.php?fill=start_timer&ticketid="+id+"&contactid="+contactid,
                    dataType: "html",
                });
            }
        }

    });

    /* Timer */
});
</script>
<div class="container">
	<div class="row">
        <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <?php
            $id = $_GET['id'];
            ?>
        	<h3 class="inline">Track Time</h3>
            <div class="pull-right gap-top"><a href=""><img src="../img/icons/cancel.png" alt="Close" title="Close" class="inline-img" /></a></div>
            <div class="clearfix"></div>
            <hr />
            <input type="hidden" name="tile" value="<?= $_GET['tile'] ?>" />
            <input type="hidden" name="id" value="<?= preg_replace('/[^0-9]/', '', $_GET['id']); ?>" />

                <div class="form-group">
                    <label class="col-sm-3 control-label">Timer:</label>
                    <div class="col-sm-9">
                        <input type="text" name="timer_value" id="timer_value" style="float:left; max-width:56%;" class="form-control timer" placeholder="0 sec" />&nbsp;&nbsp;
                        <a class="btn btn-success start-timer-btn brand-btn mobile-block" data-id="<?= $id ?>">Start</a>
                        <button type="submit" name="submit" value="submit" class="btn brand-btn stop-timer-btn hidden mobile-block" data-id="<?= $id ?>">Stop</button>
                         <!--<a class="btn stop-timer-btn hidden brand-btn mobile-block" data-id="<?= $id ?>">Stop</a>-->
                    </div>
                </div>

        </form>
    </div>
</div>