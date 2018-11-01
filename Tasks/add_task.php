<?php
/*
 * Add Task
 * Called From
 *  - index.php
 *  - Project/edit_project_path.php
 *  - Project/edit_project_path_scrum.php
 *  - Project/edit_project_scope_tasks.php
 *  - Project/edit_project_search.php
 *  - Project/projects_ajax.php
 */
?>
<style>
    .ui-datepicker-current:empty { display:none; }
</style>
<?php
include ('../include.php');
error_reporting(0);

$task_statuses = explode(',',get_config($dbc, 'task_status'));
$status_complete = $task_statuses[count($task_statuses) - 1];
$status_incomplete = $task_statuses[0];

if(IFRAME_PAGE) {
    $slider_layout = !empty(get_config($dbc, 'tasks_slider_layout')) ? get_config($dbc, 'tasks_slider_layout') : 'accordion';
}

//echo $_SERVER[REQUEST_URI];


if (isset($_POST['tasklist'])) {
	$project_history = '';
    $supportid = $_POST['supportid'];
    if($supportid != '') {
	    $query_update_project = "UPDATE `support` SET  status='Task' WHERE `supportid` = '$supportid'";
	    $result_update_project = mysqli_query($dbc, $query_update_project);
    }
	$created_date = date('Y-m-d');
    $task_businessid = $_POST['task_businessid'];
    $created_by = $_SESSION['contactid'];
    $task_clientid = $_POST['task_clientid'];
    $task_salesid = $_POST['task_salesid'];
	$task_projectid = $_POST['task_projectid'];
	$task_client_projectid = '';
	if(substr($task_projectid,0,1) == 'C') {
		$task_client_projectid = substr($task_projectid,1);
		$task_projectid = '';
	}
    $task_contactid = implode(',', $_POST['task_userid']);
    if($task_contactid == '') {
        $task_contactid = $_SESSION['contactid'];
    }
    $ticketid = $_POST['ticketid'];
    $task_path = $_POST['task_path'];
    $task_board_type = filter_var($_POST['task_board_type'], FILTER_SANITIZE_STRING);
    $new_task_board = filter_var($_POST['new_task_board'], FILTER_SANITIZE_STRING);
    if ( !empty($new_task_board) ) {
        mysqli_query($dbc, "INSERT INTO task_board (board_name, board_security, company_staff_sharing, task_path) VALUES ('$new_task_board', '$task_board_type', ',".$_SESSION['contactid'].",', '$task_path')");
        $new_taskboardid = mysqli_insert_id($dbc);
    }
    $task_board = ( !empty($new_taskboardid) ) ? $new_taskboardid : $_POST['task_board'];
    if ( empty($task_board) && !empty($task_projectid) ) {
        $get_task_board = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT taskboardid, count(taskboardid) count FROM task_board WHERE board_security='Project' AND board_name='Project Paths'"));
        if ( $get_task_board['count']==0 ) {
            mysqli_query($dbc, "INSERT INTO task_board (board_name, board_security, company_staff_sharing, task_path) VALUES ('Project Paths', 'Project', ',".$_SESSION['contactid'].",', '$task_path')");
            $task_board = mysqli_insert_id($dbc);
        } else {
            $task_board = $get_task_board['taskboardid'];
        }
    }
    $task_board_name = filter_var($_POST['task_board'], FILTER_SANITIZE_STRING);
    $task_milestone_timeline = filter_var($_POST['task_milestone_timeline'],FILTER_SANITIZE_STRING);
    $task_external = filter_var($_POST['external'],FILTER_SANITIZE_STRING);
	$project_milestone = filter_var($_POST['project_milestone'],FILTER_SANITIZE_STRING);
    if ( empty($task_milestone_timeline) && !empty($task_projectid) ) {
        $get_task_milestone = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT ppm.project_path_milestone, ppm.milestone FROM project_path_milestone ppm, project p WHERE p.projectid='$task_projectid' AND p.project_path=ppm.project_path_milestone"));
        $milestones_list = explode('#*#', $get_task_milestone['milestone']);
        $task_milestone_timeline = $milestones_list[0];
    }
    $task_heading = filter_var($_POST['task_heading'],FILTER_SANITIZE_STRING);
    $task = filter_var(htmlentities($_POST['task']),FILTER_SANITIZE_STRING);
	$alerts_enabled = implode(',',$_POST['alerts_enabled']);
    $task_tododate = $_POST['task_tododate'];
    $task_status = $_POST['task_status'];
    if($task_status == '') {
        $task_status = 'To Do';
    }
    if($task_status == 'Archived') {
        $archived_date = date('Y-m-d');
    }
    $task_category = $_POST['task_category'];

    $task_work_time = $_POST['task_work_time'];

    $task_from_tasktile = $_POST['task_from_tasktile'];
	$current_task = [];

    $flag_colour = $_POST['flag'];

    if(empty($_POST['tasklistid'])) {
        $query_insert_ca = "INSERT INTO `tasklist` (`ticketid`, `businessid`, `clientid`, `salesid`, `projectid`, `project_milestone`, `client_projectid`, `task`, `contactid`, `alerts_enabled`, `created_date`, `created_by`, `task_tododate`, `status`, `category`, `heading`, `work_time`, `task_path`, `task_board`, `task_milestone_timeline`, `external`, `flag_colour`) VALUES ('$ticketid', '$task_businessid', '$task_clientid', '$task_salesid', '$task_projectid', '$project_milestone', '$task_client_projectid', '$task', '$task_contactid', '$alerts_enabled', '$created_date', '$created_by', '$task_tododate', '$task_status', '$task_category', '$task_heading', '$task_work_time', '$task_path', '$task_board', '$task_milestone_timeline', '$task_external', '$flag_colour')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
		$tasklistid = mysqli_insert_id($dbc);

        if($task_category = 'Zen Earth Corp' || $task_category = 'Green Earth Energy' || $task_category = 'Green Life Can') {
            if (strpos(WEBSITE_URL, 'zenearthcorp.rookconnect.com') !== FALSE || strpos(WEBSITE_URL, 'greenearthenergysolutions.rookconnect.com') !== FALSE || strpos(WEBSITE_URL, 'greenlifecan.rookconnect.com') !== FALSE) {

                $zenearth_rook_db = @mysqli_connect('localhost', 'zen_rook_user', 'R0bot587tw3ak', 'zenearth_rook_db');
                $gees_rook_db = @mysqli_connect('localhost', 'zen_rook_user', 'R0bot587tw3ak', 'gees_rook_db');
                $glcllc_rook_db = @mysqli_connect('localhost', 'zen_rook_user', 'R0bot587tw3ak', 'glcllc_rook_db');

                $result_insert_ca = mysqli_query($zenearth_rook_db, $query_insert_ca);
                $result_insert_ca = mysqli_query($gees_rook_db, $query_insert_ca);
                $result_insert_ca = mysqli_query($glcllc_rook_db, $query_insert_ca);
            }
        }
		$project_history .= ($project_history == '' ? '' : '<br />').get_contact($dbc, $_SESSION['contactid']).' added Task #'.$tasklistid.' for this project at '.date('Y-m-d H:i');
        insert_day_overview($dbc, $_SESSION['contactid'], 'Task', date('Y-m-d'), '', 'Created Task #'.$tasklistid.(!empty($task_heading) ? ': '.$task_heading : ''), $tasklistid);

    } else {
        $tasklistid = $_POST['tasklistid'];
		$current_task = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `tasklistid`='$tasklistid'"));
        $query_update_vendor = "UPDATE `tasklist` SET `businessid` = '$task_businessid', `clientid` = '$task_clientid', `salesid` = '$task_salesid', `projectid` = '$task_projectid', `project_milestone`='$project_milestone', `client_projectid` = '$task_client_projectid', `task` = '$task', `contactid` = '$task_contactid', `alerts_enabled` = '$alerts_enabled', `task_tododate` = '$task_tododate', `status` = '$task_status', `category` = '$task_category', `heading` = '$task_heading', `work_time` = '$task_work_time', `task_path` = '$task_path', `task_board` = '$task_board', `task_milestone_timeline` = '$task_milestone_timeline', `external` = '$task_external', `archived_date` = '$archived_date' WHERE `tasklistid` = '$tasklistid'";

        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);

        if($task_category = 'Zen Earth Corp' || $task_category = 'Green Earth Energy' || $task_category = 'Green Life Can') {

        if (strpos(WEBSITE_URL, 'zenearthcorp.rookconnect.com') !== FALSE || strpos(WEBSITE_URL, 'greenearthenergysolutions.rookconnect.com') !== FALSE || strpos(WEBSITE_URL, 'greenlifecan.rookconnect.com') !== FALSE) {
                $zenearth_rook_db = @mysqli_connect('localhost', 'zen_rook_user', 'R0bot587tw3ak', 'zenearth_rook_db');
                $gees_rook_db = @mysqli_connect('localhost', 'zen_rook_user', 'R0bot587tw3ak', 'gees_rook_db');
                $glcllc_rook_db = @mysqli_connect('localhost', 'zen_rook_user', 'R0bot587tw3ak', 'glcllc_rook_db');

                $result_update_vendor = mysqli_query($zenearth_rook_db, $query_update_vendor);
                $result_update_vendor = mysqli_query($gees_rook_db, $query_update_vendor);
                $result_update_vendor = mysqli_query($glcllc_rook_db, $query_update_vendor);
            }
        }
		$project_history .= ($project_history == '' ? '' : '<br />').get_contact($dbc, $_SESSION['contactid']).' updated Task #'.$tasklistid.' for this project at '.date('Y-m-d H:i');
        insert_day_overview($dbc, $_SESSION['contactid'], 'Task', date('Y-m-d'), '', 'Updated Task #'.$tasklistid.(!empty($task_heading) ? ': '.$task_heading : ''), $tasklistid);
    }

    // Email Staff
    $emails_enabled = implode(',', $_POST['emails_enabled']);
    foreach ( explode(',', $emails_enabled) as $staffid ) {
        $staff = mysqli_query($dbc, "SELECT first_name, last_name, email_address, office_email FROM `contacts` WHERE `contactid`='$staffid'");
        while ( $row=mysqli_fetch_array($staff) ) {
            $email_address = get_email($dbc, $row['contactid']);
            if(trim($email_address) != '') {
                $body = "Hi ".decryptIt($row['first_name'])."<br />\n<br />
                    This is a reminder about the $task_board_name.<br />\n<br />
                    <a href='".WEBSITE_URL."/Tasks/index.php?category=$task_board&tab=$task_board_type'>Click here</a> to see the task board.";
                send_email('', $email_address, '', '', $subject, $body, '');
                $comment = 'Email sent to '. decryptIt($row['first_name']) .' '. decryptIt($row['last_name']);
                mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `created_by`, `created_date`, `comment`) VALUES ('$tasklistid', '{$_SESSION['contactid']}', '$created_date', '$comment')");
            }
        }
    }

    // Schedule Reminder
    $schedule_reminder = $_POST['schedule_reminder'];
    if ( !empty($schedule_reminder) ) {
		$subject = "A reminder about the $title task";
		$body = htmlentities("This is a reminder about the $task_board_name task.<br />\n<br />
			<a href=\"".WEBSITE_URL."/Tasks/index.php?category=$task_board&tab=$task_board_type\">Click here</a> to see the task board.");
        mysqli_query($dbc, "UPDATE `reminders` SET `done`=1 WHERE `contactid`='{$_SESSION['contactid']}' AND `src_table`='task_board' AND `src_tableid`='$task_board'");
        mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_time`, `reminder_type`, `subject`, `body`, `sender`, `src_table`, `src_tableid`)
		VALUES ('{$_SESSION['contactid']}', '$schedule_reminder', '08:00:00', 'QUICK', '$subject', '$body', '{$_SESSION['contactid']}', 'task_board', '$task_board')");
	}

    // Track Time
    $track_time = $_POST['track_time'];
    if( $track_time!='0' && $track_time!='00:00:00' && $track_time!='' ) {
        mysqli_query($dbc, "INSERT INTO `tasklist_time` (`tasklistid`, `work_time`, `src`, `contactid`, `timer_date`) VALUES ('$tasklistid', '$track_time', 'A', '{$_SESSION['contactid']}', '$created_date')");
        insert_day_overview($dbc, $_SESSION['contactid'], 'Task', date('Y-m-d'), '', "Updated Task #$tasklistid - Added Time : $track_time");
        mysqli_query($dbc, "UPDATE `tasklist` SET `work_time`=ADDTIME(`work_time`,'$track_time') WHERE `tasklistid`='$tasklistid'");
    }

	$update_task = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `tasklistid`='$tasklistid'"));
	$changes = [];
	foreach($update_task as $field => $value) {
		if($field == 'external' && $value != $current_task[$field]) {
			$changes[] = "External Path set to $value";
		} else if($field == 'alerts_enabled' && $value != $current_task[$field]) {
			$users = [];
			foreach(explode(',',$value) as $user) {
				if($user > 0) {
					$users[] = get_contact($dbc, $user);
				}
			}
			$changes[] = "Alerts Activated for ".implode(', ',$users);
		} else if($field == 'work_time' && $value != $current_task[$field]) {
			$added_time = date('H:i', strtotime($value) - strtotime($current_task[$field]) + strtotime('today'));
			$changes[] = "Added Time: ".$added_time;
			insert_day_overview($dbc, $_SESSION['contactid'], 'Task', date('Y-m-d'), '', 'Added time to Task #'.$tasklistid.': '.$added_time, $tasklistid);
			mysqli_query($dbc, "INSERT INTO `tasklist_time` (`tasklistid`, `work_time`, `contactid`, `timer_date`) VALUES ('$tasklistid', '$added_time', '".$_SESSION['contactid']."', '".date('Y-m-d')."')");
		}
	}

    //Document
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    for($i = 0; $i < count($_FILES['upload_document']['name']); $i++) {
        $document = htmlspecialchars($_FILES["upload_document"]["name"][$i], ENT_QUOTES);

        move_uploaded_file($_FILES["upload_document"]["tmp_name"][$i], "download/".$_FILES["upload_document"]["name"][$i]);

        if($document != '') {
			$changes[] = "Attached file: $document";
            $query_insert_client_doc = "INSERT INTO `task_document` (`tasklistid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$tasklistid', 'Support Document', '$document', '$created_date', '$created_by')";
            $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
        }
    }

	// Record Changes
	$changes = htmlentities(implode('<br />',$changes));
    $task_comment = htmlentities($_POST['task_comment']);
    if ( !empty($task_comment) ) {
        mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `created_by`, `created_date`, `comment`) VALUES ('$tasklistid', '".$_SESSION['contactid']."', DATE(NOW()), '$task_comment')");
    }

    $url = $_POST['from'];

	// Save Project History
	if($task_projectid != '') {
		$user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
		mysqli_query($dbc, "INSERT INTO `project_history` (`updated_by`, `description`, `projectid`) VALUES ('$user', '".htmlentities($project_history)."', '$task_projectid')");
	}
	else if($task_client_projectid != '') {
		$project_history_result = mysqli_query($dbc, "UPDATE `client_project` SET `history`=CONCAT(IFNULL(CONCAT(`history`,'<br />'),''),'".htmlentities($project_history)."') WHERE `projectid` = '$task_client_projectid'");
	}

    echo '<script type="text/javascript"> window.location.replace("'.$url.'"); </script>';
}

?>

<script type="text/javascript">
$(document).ready(function () {
    $('[data-table]').change(function() {
        var table_name = $(this).data('table');
        var field_name = $(this).data('field');
        var field_value = $(this).val();
        var id_value = 0;
        var url_fill = '';

        if ( typeof $('[name=tasklistid]').val() != 'undefined' ) {
            id_value = $('[name=tasklistid]').val();
        }

        if ( id_value == 0 ) {
            url_fill = 'insert_fields';
        } else {
            url_fill = 'update_fields';
        }

        $.ajax({
            url: 'task_ajax_all.php?fill='+url_fill,
            method: 'POST',
            data: {
                table: table_name,
                field: field_name,
                field_value: field_value,
                id_field: 'tasklistid',
                id_value: id_value,
                created_by: <?= $_SESSION['contactid']; ?>,
            },
            success: function(response) {
                if (response > 0) {
                    $('[name=tasklistid]').val(response);
                    $('h3').text('Edit Task #'+response);
					<?php if($_GET['tab'] == 'sales') { ?>
						$('[name=task_salesid]').change();
						$('[name=sales_milestone]').change();
					<?php } ?>
                }
            }
        });
    });

    $('[name=task_board]').on('change', function(){
        var board_name = $(this).val();
        if ( board_name=='NEW' ) {
            $('.new-board-name').css('display', 'block');
        } else {
            $('.new-board-name').css('display', 'none');
        }
    });

	$('.delete_task').click(function(){
		var result = confirm("Are you sure you want to delete this task?");
		if (result) {
			$.ajax({    //create an ajax request to load_page.php
				type: "GET",
				url: "task_ajax_all.php?fill=delete_task&taskid=<?php echo $_GET['tasklistid']; ?>",
				dataType: "html",   //expect html to be returned
				success: function(response){
					alert('You have successfully deleted this task.');
					window.location.href = "add_task.php";

				}
			});
		}
	});

	$("#task_path").change(function() {
		var task_path = $("#task_path").val();
		$.ajax({
			type: "GET",
			url: "task_ajax_all.php?fill=project_path_milestone&project_path="+task_path,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('#task_milestone_timeline').html(response);
				$("#task_milestone_timeline").trigger("change.select2");<?php
                if ( isset($_GET['project_milestone']) && !empty($_GET['project_milestone']) ) {
                    $task_milestone_timeline = $_GET['project_milestone']; ?>
                    $("#task_milestone_timeline option[value='<?=$task_milestone_timeline?>']").prop('selected', true);
                    $("#task_milestone_timeline").trigger("change.select2");<?php
                } ?>
			}
		});
	});

    $('#add_row_doc').on( 'click', function () {
        var clone = $('.additional_doc').clone();
        clone.find('.form-control').val('');
        clone.removeClass("additional_doc");
        $('#add_here_new_doc').append(clone);
        return false;
    });
    $("[name=task_userid]").change(function() {
		var userid = this.value;
        $.ajax({    //create an ajax request to load_page.php
            type: "GET",
            url: "task_ajax_all.php?fill=filltaskboards&user="+userid,
            dataType: "html",   //expect html to be returned
            success: function(response){
				$('[name=task_board]').html(response).trigger("change.select2");
            }
        });
	});
    $("#task_businessid").change(function() {
		var businessid = this.value;
        $.ajax({    //create an ajax request to load_page.php
            type: "GET",
            url: "task_ajax_all.php?fill=fillcontact&businessid="+businessid,
            dataType: "html",   //expect html to be returned
            success: function(response){
                var arr = response.split('*FFM*');
				$('#checklist_clientid').html(arr[0]);
				$("#checklist_clientid").trigger("change.select2");

				$('#task_projectid').html(arr[1]);
				$("#task_projectid").trigger("change.select2");
            }
        });
	});

    $('#task_comment').blur(function() {
		var task_id = $('[name=tasklistid').val();
		var save_reply = $(this).val();
		this.value = '';
		if(save_reply != '') {
			$.ajax({
				type: 'POST',
				url: 'task_ajax_all.php?fill=addtaskreply',
				dataType: 'html',
				data: { taskid: task_id, reply: save_reply },
				success: function(response) {
					$('#load_comments').load('task_comment_list.php?tasklistid='+task_id);
				}
			});
		}
	});

    /* Timer */
    $('.start-timer-btn').on('click', function() {
        $(this).closest('div').find('.timer').timer({
            editable: true
        });
        $(this).addClass('hidden');
        $(this).next('.stop-timer-btn').removeClass('hidden');
    });
    $('.stop-timer-btn').on('click', function() {
		$(this).closest('div').find('.timer').timer('stop');
		$(this).addClass('hidden');
		$(this).prev('.start-timer-btn').removeClass('hidden');

        var taskid = $(this).data('id');
        var timertaskid = $(this).data('taskid');

        var projectid = '';
        if (typeof taskid == 'undefined') {
            projectid = $(this).data('projectid'); alert(projectid);
            if ( projectid.toString().substring(0,1)=='C' ) {
                projectid = '';
            }
        }

        var businessid = '';
        var clientid = '';
        if (typeof taskid=='undefined' && typeof projectid=='undefined') {
            businessid = $(this).data('businessid');
            clientid = $(this).data('clientid');
            if ( businessid=='' && clientid=='' ) {
                businessid = $('#task_businessid option:selected').val();
                clientid = $('#checklist_clientid option:selected').val();
            }
        }

        var timer_value = $(this).closest('div').find('#timer_value').val();
        var timer_value_project = $(this).closest('div').find('#timer_value_project').val();
        var timer_value_contact = $(this).closest('div').find('#timer_value_contact').val();

        var contactid = '<?= $_SESSION['contactid'] ?>';
		$(this).closest('div').find('.timer').timer('remove');
        $('.timer_block_'+taskid).toggle();

		if ( taskid!='' && typeof taskid!='undefined' && timer_value!='' ) {
            $.ajax({
                type: "GET",
                url: "task_ajax_all.php?fill=stop_timer&taskid="+taskid+"&timer_value="+timer_value+"&contactid="+contactid,
                dataType: "html",
                success: function(response) {
                    $.ajax({
                        method: 'POST',
                        url: 'task_ajax_all.php?fill=taskreply',
                        data: { taskid: taskid, reply: 'Tracked time: '+timer_value },
                        success: function(result) {
                            $('.added-time').append('Tracked time: '+timer_value);
                        }
                    });
                }
            });
        } /* else {
            $('input[name="track_time"]').val(timer_value);
        } */

        if ( projectid!='' && typeof projectid!='undefined' && timer_value_project!='' ) {
            $.ajax({
                type: "GET",
                url: "task_ajax_all.php?fill=stop_timer_project&taskid="+timertaskid+"&projectid="+projectid+"&timer_value_project="+timer_value_project+"&contactid="+contactid,
                dataType: "html",
                success: function(response) {
                    $.ajax({
                        method: 'POST',
                        url: 'task_ajax_all.php?fill=taskreply',
                        data: { taskid: timertaskid, reply: 'Time tracked to Project # '+projectid+' '+timer_value_project },
                        success: function(result) {
                            $('.added-time-project').append('Tracked time: '+timer_value_project);
                        }
                    });
                }
            });
        }

        if ( (businessid!='' || clientid!='') && timer_value_contact!='' ) {
            $.ajax({
                type: "GET",
                url: "task_ajax_all.php?fill=stop_timer_contact&taskid="+timertaskid+"&businessid="+businessid+"&clientid="+clientid+"&timer_value_contact="+timer_value_contact+"&contactid="+contactid,
                dataType: "html",
                success: function(response) {
                    $.ajax({
                        method: 'POST',
                        url: 'task_ajax_all.php?fill=taskreply',
                        data: { taskid: timertaskid, reply: 'Time tracked to contact '+timer_value_contact },
                        success: function(result) {
                            $('.added-time-contact').append('Tracked time: '+timer_value_contact);
                        }
                    });
                }
            });
        }
	});

    //$('#task_path').trigger('change');

});

function quick_add_time(task) {
    task_id = $('[name=tasklistid]').val();
	$(task).timepicker('option', 'onClose', function(time) {
        var time = $(task).val();
		if(time != '' && time != '00:00') {
			$.ajax({
				method: 'POST',
				url: 'task_ajax_all.php?fill=task_quick_time',
				data: { id: task_id, time: time+':00' },
				complete: function(result) {
                    $.ajax({
                        method: 'POST',
                        url: 'task_ajax_all.php?fill=taskreply',
                        data: { taskid: task_id, reply: 'Time added '+time+':00' },
                        complete: function(result) {}
                    });
                }
			});
		}
	});
	$(task).timepicker('show');
}

function manual_add_time(task) {
	taskid = $(task).data('taskid');
    timer = $(task).attr('name');
    var projectid = '';
    var businessid = '';
    var clientid = '';
    var contactid = '<?= $_SESSION['contactid'] ?>';
    var type = '';

    if ( $(task).attr('name')=='task_work_time_project' ) {
        projectid = $(task).data('projectid');
        type = 'project';
        if ( typeof projectid != 'undefined' ) {
            if ( projectid.toString().substring(0,1)=='C' ) {
                projectid = '';
            }
        }
    }

    if ( $(task).attr('name')=='task_work_time_contact' ) {
        businessid = $(task).data('businessid');
        clientid = $(task).data('clientid');
        type = 'contact';
    }

    $('[name='+timer+']').timepicker('option', 'onClose', function(time) {
        var time = $(this).val();
        if( time!='' && time!='00:00') {
			$.ajax({
				method: 'POST',
				url: 'task_ajax_all.php?fill=manual_add_time',
				data: { taskid: taskid, time: time+':00', timer: timer, projectid: projectid, businessid: businessid, clientid: clientid, contactid: contactid },
				success: function(response) {
                    $.ajax({
                        method: 'POST',
                        url: 'task_ajax_all.php?fill=taskreply',
                        data: { taskid: taskid, reply: 'Time added to '+type+' '+time+':00' },
                        success: function(result) {
                            $('.'+timer).append('Added time: '+time);
                        }
                    });
                }
			});
		}
    });
}

function closePopup(){
    window.opener.location.reload();
    window.close();
}

function flag_item(task) {
	task_id = $(task).data('tasklistid');
    if ( task_id=='' ) {
        colour = $('input[name="flag"]').val();
    } else {
        colour = '';
    }
	$.ajax({
		method: "POST",
		url: "task_ajax_all.php?fill=taskflag",
		data: { type:'task', id:task_id, colour:colour },
		complete: function(result) {
			console.log(result.responseText);
            $(task).closest('form').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
            $('input[name="flag"]').val(result.responseText);
		}
	});
}

function mark_done(sel) {
    var task_id = sel.value;
    var status = '';
    if ( $(sel).is(':checked') ) {
        status = '<?= $status_complete ?>';
    } else {
        status = '<?= $status_incomplete ?>';
    }

    $.ajax({
        type: "GET",
        url: "task_ajax_all.php?fill=mark_done&taskid="+task_id+'&status='+status,
        dataType: "html",
        success: function(response){}
    });
}
</script>

</head>
<body>
<?php
    include_once ('../navigation.php');
checkAuthorised('tasks');
    $back_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<div class="container">
	<div class="row">
        <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form"><?php
            if(!empty($_GET['supportid'])) {
                $supportid = $_GET['supportid'];
                $company_name = get_support($dbc, $supportid, 'company_name');
                $get_task =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT contactid FROM	contacts WHERE	name='$company_name'"));
                $task_businessid = $get_task['contactid'];
                $task_heading = get_support($dbc, $supportid, 'heading');
                $task = html_entity_decode(get_support($dbc, $supportid, 'message'));
                $task_status = 'To Do';
                echo '<input type="hidden" name="supportid" value="'.$_GET['supportid'].'" />';
            }

            if(!empty($_GET['projectid'])) {
                $task_projectid = $_GET['projectid'];
            }

            if(!empty($_GET['task_path'])) {
                $task_path = $_GET['task_path'];
            }

            if(!empty($_GET['task_milestone_timeline'])) {
                $task_milestone_timeline = $_GET['task_milestone_timeline'];
            }

            if(!empty($_GET['project_milestone'])) {
                $project_milestone = $_GET['project_milestone'];
            }

            if(!empty($_GET['task_board'])) {
                $task_board = $_GET['task_board'];
            }

            $task_contactid = $_SESSION['contactid'];

            $contact_section_display = 'display:none;';
            $project_section_display = 'display:none;';

            if(!empty($_GET['tasklistid'])) {
                $tasklistid = $_GET['tasklistid'];
                $get_task = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM tasklist WHERE tasklistid='$tasklistid'"));
                $task_clientid = $get_task['clientid'];
                $task = $get_task['task'];
                $task_businessid = $get_task['businessid'];
                $task_contactid = $get_task['contactid'];
                $task_salesid = $get_task['salesid'];
                $task_projectid = (empty($get_task['projectid']) ? 'C'.$get_task['client_projectid'] : $get_task['projectid']);
                $task_heading = $get_task['heading'];
                $task_work_time = date('H:i',strtotime($get_task['work_time']));
                $task_category = $get_task['category'];
                $task_status = $get_task['status'];
                $task_tododate = $get_task['task_tododate'];
                $task_path = $get_task['task_path'];
                $project_milestone = $get_task['project_milestone'];
                $task_board = $get_task['task_board'];
                $task_milestone_timeline = $get_task['task_milestone_timeline'];

                $get_taskboard = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT board_security FROM task_board WHERE taskboardid='$task_board'"));
                $board_security = $get_taskboard['board_security'];
                if ( $board_security=='Client' ) {
                    $contact_section_display = 'display:block;';
                } else if ( $board_security=='Project' ) {
                    $project_section_display = 'display:block;';
                }

            } else if(!empty($_GET['projectid'])) {
                $task_projectid = $_GET['projectid'];
                $project = mysqli_fetch_array(mysqli_query($dbc, "SELECT `businessid`, `clientid`, `project_path` FROM `project` WHERE `projectid`='$task_projectid'"));
                $task_businessid = $project['businessid'];
                $task_contactid = $project['clientid'];
                $task_path = $project['project_path'];
                $project_section_display = 'display:block;';
                $board_security = 'Project';

            } else if ($_GET['tab'] == 'sales') {
				$task_salesid = $_GET['salesid'];
				$sales_milestone = $_GET['sales_milestone_timeline'];
			} else if ( !empty($_GET['category']) ) {
                $url_cat = filter_var($_GET['category'], FILTER_VALIDATE_INT);
                $url_tab = filter_var($_GET['tab'], FILTER_SANITIZE_STRING);
                $get_task_board = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT taskboardid, board_name, board_security, task_path, businessid, contactid FROM task_board WHERE taskboardid='$url_cat'"));
                $board_name = $get_task_board['board_name'];
                $board_security = $get_task_board['board_security'];
                $task_board = $get_task_board['taskboardid'];
                $taskboardid = $get_task_board['taskboardid'];
                $task_path = $get_task_board['task_path'];
                $task_businessid = $get_task_board['businessid'];
                $task_clientid = $get_task_board['contactid'];

                if ( $board_security=='Client' ) {
                    $contact_section_display = 'display:block;';
                } else if ( $board_security=='Project' ) {
                    $project_section_display = 'display:block;';
                }

            } else if ( empty($_GET['category']) && !empty($_GET['tab']) ) {
                $url_tab = filter_var($_GET['tab'], FILTER_SANITIZE_STRING);
                $board_security = $url_tab;

                if ( $board_security=='Client' ) {
                    $contact_section_display = 'display:block;';
                } else if ( $board_security=='Project' ) {
                    $project_section_display = 'display:block;';
                }
            }

            if(!empty($_GET['tasklistid'])) {
                echo '<input type="hidden" name="tasklistid" value="'.$_GET['tasklistid'].'" />';
            } else {
                echo '<input type="hidden" name="tasklistid" value="" />';
            }

            $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT task FROM field_config"));
            $value_config = ','.$get_field_config['task'].','; ?>

            <!--<h3><?php
                /* $allowed_heading = array('Private','Company','Project','Client');
                $url_tab = in_array($_GET['tab'], $allowed_heading) ? filter_var($_GET['tab'], FILTER_SANITIZE_STRING) : ''; ?>
                <?= (!empty($_GET['tasklistid']) ? 'Edit' : 'Add a') ?> <?= $url_tab ?> Task <?= ( !empty($tasklistid) ) ? '#'.$tasklistid : ''; */ ?>
            </h3>-->
            <h3 class="inline"><?= !empty($_GET['tasklistid']) ? 'Edit' : 'Add' ?> Task<?= !empty($_GET['tasklistid']) ? ' #'.$_GET['tasklistid'].': '.$task_heading : '' ?></h3>
            <div class="pull-right"><a href=""><img src="../img/icons/ROOK-status-rejected.jpg" alt="Close" title="Close" class="inline-img" /></a></div>
            <?php if(!empty($_GET['tasklistid'])) { ?><button name="" type='button' value="" class="delete_task pull-right image-btn" style="margin-top:3px;"><img class="no-margin small" src="../img/icons/ROOK-trash-icon.png" alt="Delete Task" width="25"></button><?php } ?>

            <div class="clearfix"></div>

            <hr />

            <?php if($slider_layout == 'accordion') { ?>
                <div id="accordion_tabs" class="sidebar panel-group block-panels main-screen" style="background-color: #fff; padding: 0; margin-left: 0.5em; width: calc(100% - 1em);">
            <?php } ?>

            <?php if($slider_layout == 'accordion') { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion_tabs" href="#collapse_task_board">
                                Task Board<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse_task_board" class="panel-collapse collapse">
                        <div class="panel-body">
            <?php } ?>

            <div class="form-group">
                <?= $slider_layout != 'accordion' ? '<h4>Task Board</h4>' : '' ?>
                <label for="site_name" class="col-sm-4 control-label">Task Board Type:</label>
                <div class="col-sm-8">
                    <select data-placeholder="Select a Task Board Type..." name="task_board_type" id="task_board_type" class="chosen-select-deselect form-control" data-table="task_board" data-field="board_security" width="380">
                        <option></option>
                        <option value="Private" <?= $board_security=='Private' ? 'selected' : '' ?>>Private</option><?php
                        $all_board_types = mysqli_fetch_array(mysqli_query($dbc, "SELECT task_dashboard_tile FROM task_dashboard"));
                        foreach(explode(',', $all_board_types['task_dashboard_tile']) as $board_type) {
                            $board_type = str_replace(' Tasks', '', $board_type);
                            if ( $board_type=='Client' ) {
                                $board_name = (substr(CONTACTS_TILE, -1)=='s' && substr(CONTACTS_TILE, -2) !='ss') ? rtrim(CONTACTS_TILE, 's') : CONTACTS_TILE;
                            } elseif ( $board_type=='Company' ) {
                                $board_name = 'Shared';
                            } else {
                                $board_name = $board_type;
                            }
                            if ( $board_type!='Community' && $board_type!='Business' && $board_type!='Reporting' ) { ?>
                                <option value="<?= $board_type ?>" <?= $board_security==$board_type ? 'selected' : '' ?>><?= $board_name ?></option><?php
                            }
                        } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="site_name" class="col-sm-4 control-label">Task Board Name:</label>
                <div class="col-sm-8">
                    <select data-placeholder="Select a Task Board..." name="task_board" class="chosen-select-deselect form-control" data-table="task_board" data-field="board_name" width="380">
                        <option></option>
                        <option value="NEW">Add New Task Board</option><?php
                        $query = mysqli_query($dbc, "SELECT * FROM task_board WHERE company_staff_sharing LIKE '%,". $_SESSION['contactid'] .",%'");
                        while($row = mysqli_fetch_array($query)) { ?>
                            <option <?= ($row['taskboardid']==$task_board || $row['taskboardid']==$taskboardid) ? 'selected' : '' ?> value="<?= $row['taskboardid'] ?>"><?= $row['board_name'] ?></option><?php
                        } ?>
                    </select>
                </div>
            </div>
            <div class="form-group clearfix new-board-name" style="display:none;">
                <label for="first_name" class="col-sm-4 control-label text-right">New Task Board Name:</label>
                <div class="col-sm-8">
                    <input type="text" name="new_task_board" value="" data-table="task_board" data-field="board_name" class="form-control" width="380" />
                </div>
            </div>
            <?php if($slider_layout == 'accordion') { ?>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <hr />
            <?php } ?>

            <?php if($slider_layout == 'accordion') { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion_tabs" href="#collapse_task_path">
                                Task Path<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse_task_path" class="panel-collapse collapse">
                        <div class="panel-body">
            <?php } ?>

            <div class="form-group">
                <?= $slider_layout != 'accordion' ? '<h4>Task Path</h4>' : '' ?>
                <label for="site_name" class="col-sm-4 control-label">Task Path:</label>
                <div class="col-sm-8">
                    <select data-placeholder="Select a Task Path..." id="task_path" name="task_path" data-table="tasklist" data-field="task_path" class="chosen-select-deselect form-control" width="380">
                        <option value=""></option><?php
                        $query = mysqli_query($dbc,"SELECT project_path_milestone, project_path FROM project_path_milestone");
                        while($row = mysqli_fetch_array($query)) { ?>
                            <option <?php if ($row['project_path_milestone'] == $task_path) { echo " selected"; } ?> value='<?php echo  $row['project_path_milestone']; ?>' ><?php echo $row['project_path']; ?></option><?php
                        } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="site_name" class="col-sm-4 control-label">Milestone & Timeline:</label>
                <div class="col-sm-8">
                    <select data-placeholder="Select a Milestone & Timeline..." name="task_milestone_timeline" id="task_milestone_timeline" data-table="tasklist" data-field="task_milestone_timeline"  class="chosen-select-deselect form-control" width="580">
                        <option value=""></option>
                        <?php
                        $each_tab = explode('#*#', get_project_path_milestone($dbc, $task_path, 'milestone'));
                        $timeline = explode('#*#', get_project_path_milestone($dbc, $task_path, 'timeline'));

                        $j=0;
                        foreach ($each_tab as $cat_tab) { ?>
                            <option <?php if ($cat_tab == $task_milestone_timeline) { echo " selected"; } ?> value='<?php echo  $cat_tab; ?>' ><?php echo $cat_tab.' : '.$timeline[$j]; ?></option>
                           <?php
                           $j++;
                        }

                        /*
                        $additional_milestones_query = mysqli_query($dbc, "SELECT milestone FROM task_additional_milestones WHERE task_board_id='$task_board'");
                        if ( $additional_milestones_query->num_rows>0 ) {
                            while ( $row_milestone=mysqli_fetch_assoc($additional_milestones_query) ) {
                                $each_tab[] = $row_milestone['milestone'];
                            }
                        }
						$prior_sort = 0;
						foreach($each_tab as $i => $milestone) {
							$milestone_rows = $dbc->query("SELECT `sort` FROM `taskboard_path_custom_milestones` WHERE `taskboard`='$task_board' AND `milestone`='$milestone'");
							if($milestone_rows->num_rows > 0) {
								$prior_sort = $milestone_rows->fetch_assoc()['sort'];
							} else {
								$dbc->query("INSERT INTO `taskboard_path_custom_milestones` (`taskboard`,`milestone`,`label`,`sort`) VALUES ('$task_board','$milestone','$milestone','$prior_sort')");
							}
						}
						$milestones = $dbc->query("SELECT `milestone`, `label` FROM `taskboard_path_custom_milestones` WHERE `deleted`=0 AND `taskboard`='$task_board' AND `taskboard` > 0");
						while($milestone = $milestones->fetch_assoc()) {
							$cat_tab = $milestone['milestone'];
							$j = array_search($cat_tab,$each_tab);
                            if($cat_tab != '') {
                                if ($task_milestone_timeline == $cat_tab) {
                                    $selected = 'selected="selected"';
                                } else {
                                    $selected = '';
                                }
                                echo "<option ".$selected." value='". $cat_tab."'>".$milestone['label'].' : '.$timeline[$j].'</option>';
                            }
                        }
                        */
                      ?>
                    </select>
                </div>
            </div>
			<?php if($get_task['projectid'] > 0) {
				$external_path = array_filter(array_unique(explode('#*#',mysqli_fetch_assoc(mysqli_query($dbc, "SELECT GROUP_CONCAT(`path`.`milestone` SEPARATOR '#*#') `milestones` FROM `project` LEFT JOIN `project_path_milestone` `path` ON CONCAT(',',`external_path`,',') LIKE CONCAT('%,',`path`.`project_path_milestone`,',%') WHERE `projectid`='".$get_task['projectid']."'"))['milestones'])));
				if(count($external_path) > 0) { ?>
					<div class="form-group">
						<label for="site_name" class="col-sm-4 control-label"><img class="inline-img" src="../img/icons/ROOK-sync-icon.png">External Path Milestone:</label>
						<div class="col-sm-8">
							<select data-placeholder="Select a Milestone..." name="task_milestone_timeline" id="task_milestone_timeline" data-table="tasklist" data-field="task_milestone_timeline" class="chosen-select-deselect form-control" width="580">
								<option value=""></option>
								<?php foreach ($external_path as $cat_tab) {
									echo "<option ".($cat_tab == $get_task['external'] ? 'selected' : '')." value='". $cat_tab."'>".$cat_tab.'</option>';
								}
							  ?>
							</select>
						</div>
					</div>
				<?php }
			} ?>

            <?php if($slider_layout == 'accordion') { ?>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <hr />
            <?php } ?>

            <?php if($slider_layout == 'accordion') { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion_tabs" href="#collapse_contacts">
                                <?= CONTACTS_TILE ?><span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse_contacts" class="panel-collapse collapse">
                        <div class="panel-body">
            <?php } ?>

            <div class="contact-section">
                <div class="form-group clearfix">
                    <?= $slider_layout != 'accordion' ? '<h4>'.CONTACTS_TILE.'</h4>' : '' ?>
                    <label for="first_name" class="col-sm-4 control-label text-right">Business:</label>
                    <div class="col-sm-8">
                        <select data-placeholder="Select a Business..." name="task_businessid" data-table="tasklist" data-field="businessid" id="task_businessid" class="chosen-select-deselect form-control" width="380">
                            <option value=""></option><?php
                            $query = mysqli_query($dbc,"SELECT name, contactid FROM contacts WHERE category='Business' AND deleted=0 ORDER BY name");
                            while($row = mysqli_fetch_array($query)) {
                                if ($task_businessid == $row['contactid']) {
                                    $selected = 'selected="selected"';
                                } else {
                                    $selected = '';
                                }
                                echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['name']).'</option>';
                            } ?>
                        </select>
                    </div>
                </div><?php

                if($task_clientid != '') { ?>
                    <div class="form-group clearfix">
                        <label for="first_name" class="col-sm-4 control-label text-right">Contact:</label>
                        <div class="col-sm-8">
                            <select data-placeholder="Select a Client..." id="checklist_clientid" name="task_clientid" data-table="tasklist" data-field="clientid" class="chosen-select-deselect form-control" width="380">
                                <option value=""></option><?php
                                $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE businessid='$task_businessid'"),MYSQLI_ASSOC));
                                foreach($query as $id) {
                                    $selected = '';
                                    $selected = $task_clientid == $id ? 'selected = "selected"' : '';
                                    echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
                                } ?>
                            </select>
                        </div>
                    </div><?php
                } else { ?>
                    <div class="form-group clearfix">
                        <label for="first_name" class="col-sm-4 control-label text-right">Contact:</label>
                        <div class="col-sm-8">
                            <select data-placeholder="Select a Client..." id="checklist_clientid" name="task_clientid" data-table="tasklist" data-field="clientid" class="chosen-select-deselect form-control" width="380">
                              <option value=""></option>
                            </select>
                        </div>
                    </div><?php
                } ?>

                <?php if($slider_layout != 'accordion') { ?>
                    <hr />
                <?php } ?>
            </div>
            <?php if($slider_layout == 'accordion') { ?>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if($slider_layout == 'accordion') { ?>
                <div class="panel panel-default" style="<?= $project_section_display ?>">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion_tabs" href="#collapse_project">
                                <?= PROJECT_TILE ?><span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse_project" class="panel-collapse collapse">
                        <div class="panel-body">
            <?php } ?>
            <div class="project-section" style="<?= $project_section_display ?>">
                <div class="form-group clearfix">
                    <?= $slider_layout != 'accordion' ? '<h4><?= PROJECT_TILE ?></h4>' : '' ?>
                    <label for="first_name" class="col-sm-4 control-label text-right"><?= PROJECT_TILE ?>:</label>
                    <div class="col-sm-8">
                        <select data-placeholder="Select <?= PROJECT_NOUN ?>..." name="task_projectid" data-table="tasklist" data-field="projectid" class="chosen-select-deselect form-control" id="task_projectid" width="380">
                            <option></option><?php
                            $query = "SELECT * FROM (SELECT `projectid`, `project_name` FROM `project` WHERE ('$task_businessid'='' OR `businessid`='$task_businessid') AND `deleted`=0 UNION SELECT CONCAT('C',`projectid`), `project_name` FROM `client_project` WHERE (`clientid`='$taskbusinessid' OR '$task_businessid'='') AND `deleted`=0) PROJECTS ORDER BY `project_name`";
                            echo "<!--$query-->";
                            $query = mysqli_query($dbc,$query);
                            while($row = mysqli_fetch_array($query)) {
                                if ($task_projectid == $row['projectid']) {
                                    $selected = 'selected="selected"';
                                } else {
                                    $selected = '';
                                }
                                echo "<option ".$selected." value='". $row['projectid']."'>".$row['project_name'].'</option>';
                            } ?>
                        </select>
                    </div>
                </div>
                <input type="hidden" name="project_milestone" value="<?= $project_milestone ?>">

                <?php if($slider_layout != 'accordion') { ?>
                    <hr />
                <?php } ?>
            </div>
            <?php if($slider_layout == 'accordion') { ?>
                        </div>
                    </div>
                </div>
            <?php } ?>

			<?php if(tile_enabled($dbc, 'Sales')) { ?>
                <?php if($slider_layout == 'accordion') { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion_tabs" href="#collapse_sales">
                                    <?= SALES_TILE ?><span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_sales" class="panel-collapse collapse">
                            <div class="panel-body">
                <?php } ?>
				<div class="sales-section">
					<div class="form-group clearfix">
						<?= $slider_layout != 'accordion' ? '<h4>'.SALES_TILE.'</h4>' : '' ?>
						<label for="first_name" class="col-sm-4 control-label text-right"><?= SALES_NOUN ?>:</label>
						<div class="col-sm-8">
							<select data-placeholder="Select <?= SALES_NOUN ?>..." name="task_salesid" data-table="tasklist" data-field="salesid" class="chosen-select-deselect form-control" id="task_salesid" width="380">
								<option></option><?php foreach(sort_contacts_query($dbc->query("SELECT `sales`.`salesid`, `contacts`.`first_name`, `contacts`.`last_name`, `bus`.`name` FROM `sales` LEFT JOIN `contacts` ON `sales`.`contactid`=`contacts`.`contactid` LEFT JOIN `contacts` `bus` ON `sales`.`businessid`=`bus`.`contactid` WHERE `sales`.`deleted`=0")) as $lead) {
									echo "<option ".($lead['salesid'] == $task_salesid ? 'selected' : '')." value='".$lead['salesid']."'>".$lead['name'].($lead['name'] != '' && $lead['first_name'].$lead['last_name'] != '' ? ': ' : '').$lead['first_name'].' '.$lead['last_name']."</option>";
								} ?>
							</select>
						</div>
					</div>
					<input type="hidden" name="sales_milestone" data-table="tasklist" data-field="sales_milestone" value="<?= $sales_milestone ?>">

                    <?php if($slider_layout != 'accordion') { ?>
                        <hr />
                    <?php } ?>
				</div>
                <?php if($slider_layout == 'accordion') { ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
			<?php } ?>

            <?php if($slider_layout == 'accordion') { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion_tabs" href="#collapse_task_details">
                                Task<?= ( !empty($tasklistid) ) ? ' #'.$tasklistid : ':'; ?> Details<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse_task_details" class="panel-collapse collapse">
                        <div class="panel-body">
            <?php } ?>

            <div class="form-group clearfix">
                <label for="first_name" class="col-sm-4 control-label text-right">Status:</label>
                <div class="col-sm-8">
                    <select data-placeholder="Select a Status..." name="status" data-table="tasklist" data-field="status" class="chosen-select-deselect form-control" width="380">
                        <option value=""></option>
					  <?php
						$tabs = get_config($dbc, 'ticket_status');
						$each_tab = explode(',', $tabs);
						foreach ($each_tab as $cat_tab) {
							if ($task_status == $cat_tab) {
								$selected = 'selected="selected"';
							} else {
								$selected = '';
							}
							echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
						}
					  ?>
                    </select>
                </div>
            </div>

            <!--
            <div class="form-group clearfix">
                <?= $slider_layout != 'accordion' ? '<h4>Task'. ( !empty($tasklistid) ) ? ' #'.$tasklistid : ':'.' Details</h4>' : '' ?>
                <label for="first_name" class="col-sm-4 control-label text-right">Completed:</label>
                <div class="col-sm-8">
                    <input type="checkbox" name="status" value="<?= $tasklistid ?>" class="form-checkbox no-margin" onchange="mark_done(this);" <?= ($task_status==$status_complete) ? 'checked' : '' ?> />
                </div>
            </div>
            -->

            <div class="form-group clearfix">
                <label for="first_name" class="col-sm-4 control-label text-right">
                    <img src="../img/icons/ROOK-edit-icon.png" class="inline-img" /> Task Name:
                </label>
                <div class="col-sm-8">
					<?php $groups = $dbc->query("SELECT `category` FROM `task_types` WHERE `deleted`=0 GROUP BY `category` ORDER BY MIN(`sort`), MIN(`id`)");
					if($groups->num_rows > 0) { ?>
						<select name="heading_src" onchange="if(this.value != '' && this.value != undefined) { $('[name=task_heading]').val(this.value).change(); }" class="chosen-select-deselect"><option />
							<?php while($task_group = $groups->fetch_assoc()) { ?>
								<optgroup label="<?= $task_group['category'] ?>">
									<?php $task_names = $dbc->query("SELECT `id`, `description` FROM `task_types` WHERE `deleted`=0 AND `category`='{$task_group['category']}' ORDER BY `sort`, `id`");
									while($task_name = $task_names->fetch_assoc()) { ?>
										<option value="<?= $task_name['description'] ?>"><?= $task_name['description'] ?></option>
									<?php } ?>
								</optgroup>
							<?php } ?>
						</select>
					<?php } ?>
                    <input type="text" name="task_heading" value="<?= $task_heading ?>" data-table="tasklist" data-field="heading" class="form-control" width="380" />
                </div>
            </div>

            <div class="form-group clearfix">
                <label for="first_name" class="col-sm-4 control-label text-right">To Do Date:</label>
                <div class="col-sm-8">
                    <input name="task_tododate" value="<?php echo $task_tododate; ?>" type="text" data-table="tasklist" data-field="task_tododate" class="datepicker form-control">
                </div>
            </div>

            <div class="form-group">
                <label for="site_name" class="col-sm-4 control-label">Assign Staff:</label>
                <div class="col-sm-8">
                    <select data-placeholder="Select Users" multiple name="task_userid[]" data-table="tasklist" data-field="contactid" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
                        <?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
                        foreach($staff_list as $staff_id) { ?>
                            <!-- <option <?//= ($staff_id == $_SESSION['contactid'] ? "selected" : '') ?> value='<?//=  $staff_id; ?>' ><?//= get_contact($dbc, $staff_id) ?></option> -->
                            <option <?= (strpos(','.$task_contactid.',', ','.$staff_id.',') !== false) ? ' selected' : ''; ?> value="<?= $staff_id; ?>"><?= get_contact($dbc, $staff_id); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="form-group clearfix">
                <label for="first_name" class="col-sm-4 control-label">
                    <img src="../img/icons/ROOK-flag-icon.png" class="inline-img" /> Flag This:
                </label>
                <div class="col-sm-8">
                    <a class="btn brand-btn" data-tasklistid="<?= $tasklistid ?>" onclick="flag_item(this);">Flag</a>
                    <input type="hidden" name="flag" value="" />
                </div>
            </div>

			<div class="form-group">
				<label for="site_name" class="col-sm-4 control-label">
                    <img src="../img/icons/ROOK-alert-icon.png" class="inline-img" /> Send Alert:
                </label>
				<div class="col-sm-8">
					<select data-placeholder="Select Staff..." multiple name="alerts_enabled[]" data-table="tasklist" data-field="alerts_enabled" class="chosen-select-deselect form-control" width="380">
						<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
						foreach($staff_list as $staff_id) { ?>
							<option <?= (strpos(','.$get_task['alerts_enabled'].',', ','.$staff_id.',') !== false) ? ' selected' : ''; ?> value="<?= $staff_id; ?>"><?= get_contact($dbc, $staff_id); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="site_name" class="col-sm-4 control-label">
                    <img src="../img/icons/ROOK-email-icon.png" class="inline-img" /> Send Email:
                </label>
				<div class="col-sm-8">
					<select data-placeholder="Select Staff..." multiple name="emails_enabled[]" class="chosen-select-deselect form-control" width="380">
						<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
						foreach($staff_list as $staff_id) { ?>
							<option value="<?= $staff_id; ?>"><?= get_contact($dbc, $staff_id); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="site_name" class="col-sm-4 control-label">
                    <img src="../img/icons/ROOK-reminder-icon.png" class="inline-img" /> Schedule Reminder:
                </label>
				<div class="col-sm-8">
					<input type="text" class="form-control datepicker" name="schedule_reminder" />
				</div>
			</div>
            <div class="form-group">
                <label for="additional_note" class="col-sm-4 control-label">
                    <img src="../img/icons/ROOK-attachment-icon.png" class="inline-img" /> Attach File(s):
                    <span class="popover-examples list-inline">&nbsp;
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                    </span>
                </label>
                <div class="col-sm-8">
                    <div class="enter_cost additional_doc clearfix">
                        <div class="clearfix"></div>
                        <div class="form-group clearfix">
                            <div class="col-xs-11">
                                <input name="upload_document[]" multiple type="file" data-filename-placement="inside" class="form-control" />
                            </div>
                            <div class="col-xs-1">
                                <img src="../img/icons/ROOK-add-icon.png" id="add_row_doc" class="cursor-hand" style="height:20px; margin-top:6px;" />
                            </div>
                        </div>
                    </div>

                    <div id="add_here_new_doc"></div>
                </div>
                <div class="col-sm-4"></div>
                <div class="col-sm-8"><?php
                    if(!empty($_GET['tasklistid'])) {
                        $query_check_credentials = "SELECT * FROM task_document WHERE tasklistid='$tasklistid' ORDER BY taskdocid DESC";
                        $result = mysqli_query($dbc, $query_check_credentials);
                        $num_rows = mysqli_num_rows($result);
                        if($num_rows > 0) {
                            echo "<table class='table table-bordered'>
                            <tr class='hidden-xs hidden-sm'>
                            <th>Document</th>
                            <th>Date</th>
                            <th>Uploaded By</th>
                            </tr>";
                            while($row = mysqli_fetch_array($result)) {
                                $by = $row['created_by'];
                                echo '<tr>';
                                echo '<td data-title="Document"><a href="download/'.$row['document'].'" target="_blank">'.$row['document'].'</a></td>';
                                echo '<td data-title="Date">'.$row['created_date'].'</td>';
                                echo '<td data-title="Uploaded By">'.get_staff($dbc, $by).'</td>';
                                //echo '<td data-title="Schedule"><a href=\'delete_restore.php?action=delete&ticketdocid='.$row['ticketdocid'].'&ticketid='.$row['ticketid'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a></td>';
                                echo '</tr>';
                            }
                            echo '</table>';
                        }
                    } ?>
                </div>
            </div>

            <!--
            <div class="form-group clearfix">
                <label for="first_name" class="col-sm-4 control-label text-right">Task:</label>
                <div class="col-sm-8">
                    <textarea name="task" rows="3" cols="50" class="form-control"><?php //echo html_entity_decode($task); ?></textarea>
                </div>
            </div>
            -->
            <div class="form-group clearfix">
                <label for="task_comment" class="col-sm-4 control-label text-right">
                    <img src="../img/icons/ROOK-reply-icon.png" class="inline-img" /> Comments:
                </label>
                <div class="col-sm-8">
                    <!-- <input type="text" name="task_comment" id="task_comment" class="form-control" width="65536" /> -->
                    <textarea name="task_comment" id="task_comment" class="form-control"></textarea>
                </div>
			</div>
            <div id="load_comments" class="form-group clearfix">
                <?php include('task_comment_list.php'); ?>
                <?php /*$url_tasklistid = preg_replace('/[^0-9]/', '', $_GET['tasklistid']);
                $comments = mysqli_query($dbc, "SELECT `created_by`, `created_date`, `comment` FROM `task_comments` WHERE `tasklistid`='$url_tasklistid' AND `deleted`=0 ORDER BY `taskcommid` DESC");
                if ( $comments->num_rows > 0 ) { ?>
                    <div class="col-sm-12 double-gap-top">
                        <?php
                        while ( $row_comment=mysqli_fetch_assoc($comments) ) { ?>
                            <div class="note_block row gap-bottom">
                                <div class="col-xs-1"><?= profile_id($dbc, $row_comment['created_by']); ?></div>
                                <div class="col-xs-11">
                                    <div><?= html_entity_decode($row_comment['comment']); ?></div>
                                    <div class="gap-top"><em>Added by <?= get_contact($dbc, $row_comment['created_by']); ?> on <?= $row_comment['created_date']; ?></em></div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <hr /><?php
                        } ?>
                    </div>
                    <div class="clearfix"></div><?php
                }*/ ?>
            </div>
            <?php if($slider_layout == 'accordion') { ?>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <hr />
            <?php } ?>

            <?php if($slider_layout == 'accordion') { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion_tabs" href="#collapse_time_tracking">
                                Time Tracking<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse_time_tracking" class="panel-collapse collapse">
                        <div class="panel-body">
            <?php } ?>
            <div class="form-group clearfix">
                <?= $slider_layout != 'accordion' ? '<h4>Time Tracking</h4>' : '' ?>
                <h5>Track Time To Task</h5>
                <label for="first_name" class="col-xs-3 control-label text-right"><img src="../img/icons/ROOK-timer-icon.png" class="inline-img" /> Add Time:</label>
                <div class="col-xs-3">
                    <!-- <input name="task_work_time" type="text" value="00:00" data-table="tasklist" data-field="work_time" class="timepicker form-control" /> -->
                    <input name="task_work_time" type="text" value="00:00" class="timepicker form-control" onchange="quick_add_time(this);" />
                </div>
                <label for="first_name" class="col-xs-3 control-label text-right"><img src="../img/icons/ROOK-timer2-icon.png" class="inline-img" /> Track Time:</label>
                <div class="col-xs-3">
                    <input type="text" name="timer_<?= $tasklistid ?>" id="timer_value" class="form-control timer" placeholder="0 sec" />
                    <a class="btn btn-success start-timer-btn brand-btn mobile-block">Start</a>
                    <a class="btn stop-timer-btn hidden brand-btn mobile-block" data-id="<?= $tasklistid ?>">Stop</a><br />
                    <input type="hidden" value="" name="track_time" />
                    <span class="added-time"></span>
                </div>
            </div>

            <!--
            <div class="form-group clearfix">
                <h5>Track Time To Project</h5>
                <?php
                    if ( empty($task_projectid) || substr($task_projectid,0,1) == 'C' ) {
                        echo '<div class="alert alert-info">No project is attached to this task. Time will not be tracked here.</div>';
                    }
                ?>
                <label class="col-xs-3 control-label text-right"><img src="../img/icons/ROOK-timer-icon.png" class="inline-img" /> Add Time:</label>
                <div class="col-xs-3">
                    <input name="task_work_time_project" value="<?= $task_work_time_project; ?>" type="text" value="00:00" data-taskid="<?= $tasklistid ?>" data-projectid="<?= $task_projectid ?>" class="timepicker form-control" onclick="manual_add_time(this); return false;" />
                    <span class="task_work_time_project"></span>
                </div>
                <label class="col-xs-3 control-label text-right"><img src="../img/icons/ROOK-timer-icon.png" class="inline-img" /> Track Time:</label>
                <div class="col-xs-3">
                    <input type="text" name="timer_project_<?= $tasklistid ?>" id="timer_value_project" class="form-control timer" placeholder="0 sec" />
                    <a class="btn btn-success start-timer-btn brand-btn mobile-block">Start</a>
                    <a class="btn stop-timer-btn hidden brand-btn mobile-block" data-taskid="<?= $tasklistid ?>" data-projectid="<?= $task_projectid ?>">Stop</a><br />
                    <input type="hidden" value="" name="track_time" />
                    <span class="added-time-project"></span>
                </div>
            </div>

            <div class="form-group clearfix">
                <h5>Track Time To Contact</h5>
                <?php
                    if ( empty($task_businessid) || empty($task_clientid) ) {
                        echo '<div class="alert alert-info">No contact is attached to this task. Time will not be tracked here.</div>';
                    }
                ?>
                <label class="col-xs-3 control-label text-right"><img src="../img/icons/ROOK-timer-icon.png" class="inline-img" /> Add Time:</label>
                <div class="col-xs-3">
                    <input name="task_work_time_contact" value="<?= $task_work_time_contact; ?>" type="text" value="00:00" data-taskid="<?= $tasklistid ?>" data-businessid="<?= $task_businessid ?>" data-clientid="<?= $task_clientid ?>" class="timepicker form-control" onclick="manual_add_time(this); return false;" />
                    <span class="task_work_time_contact"></span>
                </div>
                <label class="col-xs-3 control-label text-right"><img src="../img/icons/ROOK-timer-icon.png" class="inline-img" /> Track Time:</label>
                <div class="col-xs-3">
                    <input type="text" name="timer_contact_<?= $tasklistid ?>" id="timer_value_contact" class="form-control timer" placeholder="0 sec" />
                    <a class="btn btn-success start-timer-btn brand-btn mobile-block">Start</a>
                    <a class="btn stop-timer-btn hidden brand-btn mobile-block" data-taskid="<?= $tasklistid ?>" data-businessid="<?= $task_businessid ?>" data-clientid="<?= $task_clientid ?>">Stop</a><br />
                    <input type="hidden" value="" name="track_time" />
                    <span class="added-time-contact"></span>
                </div>
            </div>
            -->

            <?php if($slider_layout == 'accordion') { ?>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if($slider_layout == 'accordion') { ?>
                </div>
            <?php } ?>

            <div class="form-group pull-right">
                <a href="<?php echo $back_url; ?>" class="btn brand-btn pull-left">Cancel</a>
                <button name="tasklist" value="tasklist" class="btn brand-btn pull-right">Submit</button>
                <?php if(!empty($_GET['tasklistid'])) { ?><button name="" type='button' value="" class="delete_task pull-right image-btn"><img class="no-margin small" src="../img/icons/ROOK-trash-icon.png" alt="Delete Task" width="30"></button><?php } ?>
                <div class="clearfix"></div>
            </div>
        </form>

    </div><!-- .row -->
</div><!-- .container -->
