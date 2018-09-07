<?php
error_reporting(0);
include ('../database_connection.php');
include ('../function.php');
include ('../global.php');
include ('../phpmailer.php');

if($_GET['fill'] == 'project_path_milestone') {
    $project_path = $_GET['project_path'];
	echo '<option value=""></option>';
    $each_tab = explode('#*#', get_project_path_milestone($dbc, $project_path, 'milestone'));
    $timeline = explode('#*#', get_project_path_milestone($dbc, $project_path, 'timeline'));
    $j=0;
    foreach ($each_tab as $cat_tab) {
        echo "<option value='". $cat_tab."'>".$cat_tab.' : '.$timeline[$j].'</option>';
        $j++;
    }
}

if($_GET['fill'] == 'delete_task') {
    $tasklistid = $_GET['taskid'];
	    $date_of_archival = date('Y-m-d');
$query_update_project = "UPDATE `tasklist` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `tasklistid` = '$tasklistid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);
	echo "deleted";
}
if($_GET['fill'] == 'delete_board') {
    $boardid = $_GET['boardid'];
	if($boardid > 0) {
    $date_of_archival = date('Y-m-d');
		$query_update_project = "UPDATE `task_board` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `taskboardid` = '$boardid'";
		$result_update_project = mysqli_query($dbc, $query_update_project);
	}
	echo "deleted";
}

if($_GET['fill'] == 'tasklist') {
    $tasklistid = $_GET['tasklistid'];
    $table = $_GET['table'];
    $id_field = $_GET['id_field'];
    $task_milestone_timeline = $_GET['task_milestone_timeline'];
	$task_milestone_timeline = str_replace("FFMEND","&",$task_milestone_timeline);
    $task_milestone_timeline = str_replace("FFMSPACE"," ",$task_milestone_timeline);
    $task_milestone_timeline = str_replace("FFMHASH","#",$task_milestone_timeline);

	$query_update_project = "UPDATE `$table` SET  task_milestone_timeline='$task_milestone_timeline' WHERE `$id_field` = '$tasklistid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);

    if($task_category = 'Zen Earth Corp' || $task_category = 'Green Earth Energy' || $task_category = 'Green Life Can') {

        if (strpos(WEBSITE_URL, 'zenearthcorp.rookconnect.com') !== FALSE || strpos(WEBSITE_URL, 'greenearthenergysolutions.rookconnect.com') !== FALSE || strpos(WEBSITE_URL, 'greenlifecan.rookconnect.com') !== FALSE) {

            $zenearth_rook_db = @mysqli_connect('localhost', 'zen_rook_user', 'R0bot587tw3ak', 'zenearth_rook_db');
            $gees_rook_db = @mysqli_connect('localhost', 'zen_rook_user', 'R0bot587tw3ak', 'gees_rook_db');
            $glcllc_rook_db = @mysqli_connect('localhost', 'zen_rook_user', 'R0bot587tw3ak', 'glcllc_rook_db');

            $result_update_project = mysqli_query($zenearth_rook_db, $query_update_project);
            $result_update_project = mysqli_query($gees_rook_db, $query_update_project);
            $result_update_project = mysqli_query($glcllc_rook_db, $query_update_project);
        }
    }
}

if($_GET['fill'] == 'add_task') {

    $task_milestone_timeline = $_GET['task_milestone_timeline'];
    $sales_milestone = $_GET['sales_milestone'];
    $task_path = $_GET['task_path'];
    $heading = $_GET['heading'];
    $taskboardid = $_GET['taskboardid'];
    $salesid = $_GET['salesid'];

    $contactid = $_SESSION['contactid'];

	$task_milestone_timeline = str_replace("FFMEND","&",$task_milestone_timeline);
    $task_milestone_timeline = str_replace("FFMSPACE"," ",$task_milestone_timeline);
    $task_milestone_timeline = str_replace("FFMHASH","#",$task_milestone_timeline);

	$heading = str_replace("FFMEND","&",$heading);
    $heading = str_replace("FFMSPACE"," ",$heading);
    $heading = str_replace("FFMHASH","#",$heading);

    $heading = filter_var($heading,FILTER_SANITIZE_STRING);
    $created_date = date('Y-m-d');

    if($heading != '') {
        $query_insert_log = "INSERT INTO `tasklist` (`task_milestone_timeline`, `sales_milestone`, `task_path`, `heading`, `contactid`, `task_board`, `salesid`, `created_date`) VALUES ('$task_milestone_timeline', '$sales_milestone', '$task_path', '$heading', '$contactid', '$taskboardid', '$salesid', '$created_date')";
        $result_insert_log = mysqli_query($dbc, $query_insert_log);
    }
}
if($_GET['fill'] == 'ticket') {
    $ticketid = $_GET['ticketid'];
    $task_status = $_GET['status'];
	$task_status = str_replace("FFMEND","&",$task_status);
    $task_status = str_replace("FFMSPACE"," ",$task_status);
    $task_status = str_replace("FFMHASH","#",$task_status);

	$query_update_project = "UPDATE `tickets` SET  status='$task_status' WHERE `ticketid` = '$ticketid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);
}

if($_GET['fill'] == 'trellotable') {
    $contactid = $_GET['contactid'];
	$value = $_GET['value'];
	if($value !== '1') {
		$value = NULL;
	}
    $query_update_project = "UPDATE `contacts` SET horizontal_communication='$value' WHERE `contactid` = '$contactid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);
}

if($_GET['fill'] == 'fillcontact') {
	$businessid = $_GET['businessid'];
	$query = mysqli_query($dbc,"SELECT contactid, first_name, last_name, email_address FROM contacts WHERE businessid = '$businessid'");
	echo '<option value="">Please Select</option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['contactid']."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' : '.decryptIt($row['email_address']).'</option>';
	}
    echo '*FFM*';

	$project_tabs = get_config($dbc, 'project_tabs');
	if($project_tabs == '') {
		$project_tabs = 'Client,SR&ED,Internal,R&D,Business Development,Process Development,Addendum,Addition,Marketing,Manufacturing,Assembly';
	}
	$project_tabs = explode(',',$project_tabs);
	$project_vars = [];
	foreach($project_tabs as $item) {
		$project_vars[] = preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)));
	}

    $query = mysqli_query($dbc,"SELECT * FROM (SELECT projectid, projecttype, project_name FROM project WHERE businessid='$businessid' and deleted=0 UNION SELECT CONCAT('C',`projectid`), 'CLIENT', `project_name` FROM `client_project` WHERE `clientid`='$businessid' AND `deleted`=0) PROJECTS order by project_name");
	echo "<option></option>";
    while($row = mysqli_fetch_array($query)) {
		if(substr($row['projectid'],0,1) == 'C') {
			echo "<option value='". $row['projectid']."'>Client Project :  ".$row['project_name'].'</option>';
		}
		foreach($project_vars as $key => $type_name) {
			if($type_name == $row['projecttype']) {
				echo "<option value='". $row['projectid']."'>".$project_tabs[$key].' :  '.$row['project_name'].'</option>';
			}
		}

    }
}

if($_GET['fill'] == 'filltaskboards') {
	$userid = $_GET['user'];
	$query = mysqli_query($dbc,"SELECT * FROM task_board WHERE company_staff_sharing LIKE '%," . $userid . ",%'");
	echo '<option></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value=".$row['taskboardid'].' >'.$row['board_name'].'</option>';
	}
}

if($_GET['fill'] == 'taskreply') {
	$id = $_POST['taskid'];
	//$reply = filter_var(htmlentities('<p>'.$_POST['reply'].'</p>'),FILTER_SANITIZE_STRING);
    $reply = filter_var(htmlentities($_POST['reply']),FILTER_SANITIZE_STRING);
	//$query = "UPDATE `tasklist` SET `task`=CONCAT(`task`,'$reply') WHERE `tasklistid`='$id'";
	//$result = mysqli_query($dbc,$query);

    if ( isset($_POST['user']) ) {
        $user = preg_replace('/[^0-9]/', '', $_POST['user']);
        $reply .= get_contact($dbc, $user);
    }

    $contactid = $_SESSION['contactid'];
    $created_date = date('Y-m-d');
    $insert = mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `created_by`, `created_date`, `comment`) VALUES ('$id', '$contactid', '$created_date', '$reply')");
    echo mysqli_error($dbc);
}
if($_GET['fill'] == 'addtaskreply') {
	$id = preg_replace('/[^0-9]/', '', $_POST['taskid']);
    $reply = filter_var(htmlentities($_POST['reply']),FILTER_SANITIZE_STRING);
    $contactid = $_SESSION['contactid'];
    $created_date = date('Y-m-d');
    $insert = mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `created_by`, `created_date`, `comment`) VALUES ('$id', '$contactid', '$created_date', '$reply')");
    //echo mysqli_error($dbc);
    $last_id = mysqli_insert_id($dbc);

    $comments = mysqli_query($dbc, "SELECT `created_by`, `created_date`, `comment` FROM `task_comments` WHERE `taskcommid`='$last_id'");
    if ( $comments->num_rows > 0 ) {
        $output .= '<div class="col-sm-12 double-gap-top">';
            while ( $row_comment=mysqli_fetch_assoc($comments) ) {
                $output .= '<div class="note_block row gap-bottom">
                    <div class="col-xs-1">'. profile_id($dbc, $row_comment['created_by']) .'</div>
                    <div class="col-xs-11">
                        <div>'. html_entity_decode($row_comment['comment']) .'</div>
                        <div class="gap-top"><em>Added by '. get_contact($dbc, $row_comment['created_by']) .' on '. $row_comment['created_date'] .'</em></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <hr />';
            }
        $output .= '</div><div class="clearfix"></div>';
    } else {
        $output .= 'Not added.';
    }
    echo $output;
}
if($_GET['fill'] == 'taskalert') {
	$item_id = $_POST['id'];
	$type = $_POST['type'];
	$user = $_POST['user'];
	if($type == 'task') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `list`.`task_board`, `board`.`board_security` FROM `tasklist` AS `list` JOIN `task_board` AS `board` ON (`list`.`task_board`=`board`.`taskboardid`) WHERE `list`.`tasklistid`='$item_id'"));
		$id = $result['task_board'];
        $tab = $result['board_security'];
	}
	else {
		$id = $item_id;
        $tab = '';
	}
	$link = WEBSITE_URL."/Tasks/index.php?category=".$id."&tab=".$tab;
	$text = "Task";
	$date = date('Y/m/d');
	$sql = mysqli_query($dbc, "INSERT INTO `alerts` (`alert_date`, `alert_link`, `alert_text`, `alert_user`) VALUES ('$date', '$link', '$text', '$user')");
}
if($_GET['fill'] == 'taskemail') {
	$item_id = $_POST['id'];
	$type = $_POST['type'];
	$user = $_POST['user'];
	$subject = '';
	$title = '';
	if($type == 'task') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `list`.`task_board`, `list`.`heading`, `board`.`board_security` FROM `tasklist` AS `list` JOIN `task_board` AS `board` ON (`list`.`task_board`=`board`.`taskboardid`) WHERE `list`.`tasklistid`='$item_id'"));
		$id = $result['task_board'];
		$title = $result['heading'];
        $tab = $result['board_security'];
		$subject = "A reminder about the $title on the task board";
	}
	else {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM task_board WHERE taskboardid = '$item_id'"));
		$id = $item_id;
		$title = $result['board_name'];
        $tab = $result['board_security'];
		$subject = "A reminder about the $title task board";
	}
	$contacts = mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$user'");
	while($row = mysqli_fetch_array($contacts)) {
		$email_address = get_email($dbc, $row['contactid']);
		if(trim($email_address) != '') {
			$body = "Hi ".decryptIt($row['first_name'])."<br />\n<br />
				This is a reminder about the $title.<br />\n<br />
				<a href='".WEBSITE_URL."/Tasks/index.php?category=$id&tab=&tab'>Click here</a> to see the task board.";
			send_email('', $email_address, '', '', $subject, $body, '');
		}
	}
}
if($_GET['fill'] == 'taskreminder') {
	$item_id = $_POST['id'];
	$sender = get_email($dbc, $_SESSION['contactid']);
	$date = $_POST['schedule'];
	$type = $_POST['type'];
	$to = $_POST['user'];
	$subject = '';
	$title = '';
	if($type == 'task') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `list`.`task_board`, `list`.`heading`, `board`.`board_security` FROM `tasklist` AS `list` JOIN `task_board` AS `board` ON (`list`.`task_board`=`board`.`taskboardid`) WHERE `list`.`tasklistid`='$item_id'"));
		$id = $result['task_board'];
		$title = $result['heading'];
        $tab = $result['board_security'];
		$subject = "A reminder about the $title task";
		$body = htmlentities("This is a reminder about the $title task.<br />\n<br />
			<a href=\"".WEBSITE_URL."/Tasks/index.php?category=$id&tab=$tab\">Click here</a> to see the task board.");
	}
	else {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM task_board WHERE taskboardid = '$item_id'"));
		$id = $item_id;
		$title = $result['board_name'];
        $tab = $result['board_security'];
		$subject = "A reminder about the $title task board";
		$body = htmlentities("This is a reminder about the $title Task Board.<br />\n<br />
			<a href=\"".WEBSITE_URL."/Tasks/index.php?category=$id&tab=$tab\">Click here</a> to see the task board.");
	}
    mysqli_query($dbc, "UPDATE `reminders` SET `done` = 1 WHERE `contactid` = '$to' AND `src_table` = 'task_board' AND `src_tableid` = '$id'");
	$result = mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_time`, `reminder_type`, `subject`, `body`, `sender`, `src_table`, `src_tableid`)
		VALUES ('$to', '$date', '08:00:00', 'QUICK', '$subject', '$body', '$sender', 'task_board', '$id')");
}
if($_GET['fill'] == 'taskflagmanual') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	$label = filter_var($_POST['label'],FILTER_SANITIZE_STRING);
	$start = filter_var($_POST['start'],FILTER_SANITIZE_STRING);
	$end = filter_var($_POST['end'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "UPDATE `tasklist` SET `flag_colour`='$value',`flag_label`='$label',`flag_start`='$start',`flag_end`='$end' WHERE `tasklistid`='$id'");
}
if($_GET['fill'] == 'taskflag') {
	$item_id = $_POST['id'];
	$type = $_POST['type'];
	if($type == 'task') {
        $colour = mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colour` FROM tasklist WHERE tasklistid = '$item_id'"))['flag_colour'];
        if ( $colour=='' ) {
            $colour = $_POST['colour'];
        }
		$colour_list = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colours` FROM `task_dashboard`"))['flag_colours']);
		$colour_key = array_search($colour, $colour_list);
		$new_colour = ($colour_key === FALSE ? $colour_list[0] : ($colour_key + 1 < count($colour_list) ? $colour_list[$colour_key + 1] : ''));
		$result = mysqli_query($dbc, "UPDATE `tasklist` SET `flag_colour`='$new_colour' WHERE `tasklistid` = '$item_id'");
		echo $new_colour;
	}
	else {
		$colour = mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colour` FROM task_board WHERE taskboardid = '$item_id'"))['flag_colour'];
		$colour_list = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colours` FROM `task_dashboard`"))['flag_colours']);
		$colour_key = array_search($colour, $colour_list);
		$new_colour = ($colour_key === FALSE ? $colour_list[0] : ($colour_key + 1 < count($colour_list) ? $colour_list[$colour_key + 1] : ''));
		$result = mysqli_query($dbc, "UPDATE `task_board` SET `flag_colour`='$new_colour' WHERE `taskboardid` = '$item_id'");
		echo $new_colour;
	}
}
if($_GET['fill'] == 'task_upload') {
	$id = $_GET['id'];
	$type = $_GET['type'];
	$filename = htmlspecialchars($_FILES['file']['name'], ENT_QUOTES);
	$file = $_FILES['file']['tmp_name'];
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
	move_uploaded_file($file, "download/".$filename);
	if($type == 'task') {
		$query_insert = "INSERT INTO `task_document` (`tasklistid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$id', 'Support Document', '$filename', '".date('Y/m/d')."', '".$_SESSION['contactid']."')";
		$result_insert = mysqli_query($dbc, $query_insert);
	}
	else if($type == 'task_board') {
		$query_insert = "INSERT INTO `task_board_document` (`taskboardid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$id', 'Support Document', '$filename', '".date('Y/m/d')."', '".$_SESSION['contactid']."')";
		$result_insert = mysqli_query($dbc, $query_insert);

	}
}
if($_GET['fill'] == 'task_quick_time') {
	$taskid = $_POST['id'];
	$time = strtotime($_POST['time']);
	$current_time = strtotime(mysqli_fetch_array(mysqli_query($dbc, "SELECT `work_time` FROM `tasklist` WHERE `tasklistid`='$taskid'"))['work_time']);
	$total_time = date('H:i:s', $time + $current_time - strtotime('00:00:00'));
	$query_time = "UPDATE `tasklist` SET `work_time` = '$total_time' WHERE tasklistid='$taskid'";
    mysqli_query($dbc, "INSERT INTO `tasklist_time` (`tasklistid`, `work_time`, `src`, `contactid`, `timer_date`) VALUES ('$taskid', '".$_POST['time']."', 'M', '".$_SESSION['contactid']."', '".date('Y-m-d')."')");
	mysqli_query($dbc, "INSERT INTO `time_cards` (`staff`,`date`,`type_of_time`,`total_hrs`,`timer_tracked`,`comment_box`) VALUES ('".$_SESSION['contactid']."','".date('Y-m-d')."','Regular Hrs.','".(($time - strtotime('00:00:00')) / 3600)."','0','Time Added on Task #$taskid')");
	$result = mysqli_query($dbc, $query_time);
	insert_day_overview($dbc, $_SESSION['contactid'], 'Task', date('Y-m-d'), '', "Updated Task #$taskid - Added Time : ".$_POST['time']);
	echo 'Added '.$_POST['time']." - $total_time total";
}

if($_GET['fill'] == 'mark_done') {
	$taskid = preg_replace('/[^0-9]/', '', $_GET['taskid']);
    $status = filter_var($_GET['status'], FILTER_SANITIZE_STRING);
	$result = mysqli_query($dbc, "UPDATE `tasklist` SET `status`='$status' WHERE `tasklistid`='$taskid'");
	if (mysqli_affected_rows($dbc) > 0) {
        $contactid = $_SESSION['contactid'];
        $created_date = date('Y-m-d');
        $reply = 'Task marked as '. $status;
        $insert = mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `created_by`, `created_date`, `comment`) VALUES ('$taskid', '$contactid', '$created_date', '$reply')");
    }
}
if($_GET['fill'] == 'taskexternal') {
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$assigned = 1;
	if($value == 'unassign') {
		$value = '';
		$assigned = '';
	}
	$sql = mysqli_query($dbc, "UPDATE `tasklist` SET `external`='".$value."', `assign_client`='".$assigned."' WHERE `tasklistid`='$id'");
	$note = "<em><small>Assigned to $value on External Path</small></em>";
	echo $note."<br /><em>Added by ".get_contact($dbc, $_SESSION['contactid'])." on ".date('Y-m-d')."</em>";
	mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `comment`, `created_by`, `created_date`) VALUES ('$id','".filter_var(htmlentities($note),FILTER_SANITIZE_STRING)."','".$_SESSION['contactid']."','".date('Y-m-d')."')");
}
if($_GET['fill'] == 'clear_completed') {
	$task_board_id = filter_var($_GET['task_board_id'],FILTER_SANITIZE_STRING);
    $date_of_archival = date('Y-m-d');
    $status = filter_var($_GET['status'],FILTER_SANITIZE_STRING);
	$result = mysqli_query($dbc, "UPDATE `tasklist` SET `deleted`='1', `date_of_archival` = '$date_of_archival' WHERE `task_board`='$task_board_id' AND `status`='$status'");
}
if($_GET['fill'] == 'clear_completed_auto') {
    $status = filter_var($_GET['status'],FILTER_SANITIZE_STRING);
    $date_of_archival = date('Y-m-d');
	$result = mysqli_query($dbc, "UPDATE `tasklist` SET `deleted`='1', `date_of_archival` = '$date_of_archival' WHERE `task_board`='$task_board_id' AND `status`='$status'");
}

if($_GET['fill'] == 'insert_fields') {
    $table = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
    $field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
    if ( is_array($_POST['field_value']) ) {
        $field_value = implode(',', $_POST['field_value']);
    } else {
        $field_value = $_POST['field_value'];
    }
    $created_by = preg_replace('/[^0-9]/', '', $_POST['created_by']);
    $created_date = date('Y-m-d');

    $result = mysqli_query($dbc, "INSERT INTO `$table` (`$field`, `created_date`, `created_by`) VALUES ('$field_value', '$created_date', '$created_by')");
    echo mysqli_insert_id($dbc);
}
if($_GET['fill'] == 'update_fields') {
    $table = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
    $field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
    if ( is_array($_POST['field_value']) ) {
        $field_value = implode(',', $_POST['field_value']);
    } else {
        $field_value = $_POST['field_value'];
    }
    $id_field = filter_var($_POST['id_field'],FILTER_SANITIZE_STRING);
    $id_value = preg_replace('/[^0-9]/', '', $_POST['id_value']);
    $updated_by = preg_replace('/[^0-9]/', '', $_POST['created_by']);
    $updated_date = date('Y-m-d');

    $result = mysqli_query($dbc, "UPDATE `$table` SET `$field`='$field_value', `updated_by`='$updated_by', `updated_date`='$updated_date' WHERE `$id_field`='$id_value'");
}


if($_GET['fill'] == 'stop_timer') {
    $taskid = filter_var($_GET['taskid'],FILTER_SANITIZE_STRING);
    $timer_value = filter_var($_GET['timer_value'],FILTER_SANITIZE_STRING);
    $contactid = filter_var($_GET['contactid'],FILTER_SANITIZE_STRING);
    $timer_date = date('Y-m-d');

    if($timer_value != '0' && $timer_value != '00:00:00' && $timer_value != '') {
        $query_add_time = "INSERT INTO `tasklist_time` (`tasklistid`, `work_time`, `src`, `contactid`, `timer_date`) VALUES ('$taskid', '$timer_value', 'A', '$contactid', '$timer_date')";
        $result_add_time = mysqli_query($dbc, $query_add_time);

        insert_day_overview($dbc, $contactid, 'Task', date('Y-m-d'), '', "Updated Task #$taskid - Added Time : $timer_value");

        $query_update_time = "UPDATE `tasklist` SET `work_time`=ADDTIME(`work_time`,'$timer_value') WHERE `tasklistid`='$taskid'";
        $result_update_time = mysqli_query($dbc, $query_update_time);
        
        $query_add_time = "INSERT INTO `time_cards` (`staff`, `date`, `type_of_time`, `total_hrs`, `comment_box`) VALUES ('$contactid', '$timer_date', 'Regular Hrs.', '".((strtotime($timer_value) - strtotime('00:00:00'))/3600)."', 'Time Added on Task #$taskid')";
        $result_add_time = mysqli_query($dbc, $query_add_time);
    }
} else if($_GET['action'] == 'milestone_edit') {
	if($_POST['id'] > 0) {
		$id = $_POST['id'];
		$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
		$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
		$table = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
		$dbc->query("UPDATE `$table` SET `$field`='$value' WHERE `id`='$id'");
	} else if($_POST['field'] == 'sort') {
		$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
		$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
		$table = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
		$taskboard = filter_var($_POST['taskboard'],FILTER_SANITIZE_STRING);
		$dbc->query("INSERT INTO `$table` (`$field`,`taskboard`) VALUES ('$value','$taskboard')");
		$id = $dbc->insert_id;
		echo $id;
		$dbc->query("UPDATE `$table` SET `milestone`='milestone.$id', `label`='New Milestone' WHERE `id`='$id'");
	}
}

if($_GET['fill'] == 'stop_timer_project') {
    $taskid = filter_var($_GET['taskid'],FILTER_SANITIZE_STRING);
    $projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
    $timer_value_project = filter_var($_GET['timer_value_project'],FILTER_SANITIZE_STRING);
    $time_arr = explode(':', $timer_value_project);
    $time_dec = number_format(($time_arr[0] + ($time_arr[1]/60) + ($time_arr[2]/3600)), 4);
    $contactid = filter_var($_GET['contactid'],FILTER_SANITIZE_STRING);
    $timer_date = date('Y-m-d');

    if($timer_value_project != '0' && $timer_value_project != '00:00:00' && $timer_value_project != '') {
        $query_add_time = "INSERT INTO `time_cards` (`projectid`, `staff`, `date`, `type_of_time`, `total_hrs`, `timer_tracked`, `comment_box`) VALUES ('$projectid', '$contactid', '$timer_date', 'Regular Hrs.', '$time_dec', '$time_dec', 'Time Tracked To Project #$projectid on Task #$taskid')";
        $result_add_time = mysqli_query($dbc, $query_add_time);

        insert_day_overview($dbc, $contactid, 'Task', date('Y-m-d'), '', "Updated Task #$taskid - Tracked Time To Project #$projectid: $timer_value_project");

        $query_update_time = "UPDATE `tasklist` SET `work_time`=ADDTIME(`work_time`,'$timer_value_project') WHERE `tasklistid`='$taskid'";
        $result_update_time = mysqli_query($dbc, $query_update_time);
    }
}

if($_GET['fill'] == 'stop_timer_contact') {
    $taskid = filter_var($_GET['taskid'],FILTER_SANITIZE_STRING);
    $businessid = filter_var($_GET['businessid'],FILTER_SANITIZE_STRING);
    $clientid = filter_var($_GET['clientid'],FILTER_SANITIZE_STRING);
    $timer_value_contact = filter_var($_GET['timer_value_contact'],FILTER_SANITIZE_STRING);
    $time_arr = explode(':', $timer_value_contact);
    $time_dec = number_format(($time_arr[0] + ($time_arr[1]/60) + ($time_arr[2]/3600)), 4);
    $contactid = filter_var($_GET['contactid'],FILTER_SANITIZE_STRING);
    $timer_date = date('Y-m-d');

    if($timer_value_contact != '0' && $timer_value_contact != '00:00:00' && $timer_value_contact != '') {
        $query_add_time = "INSERT INTO `time_cards` (`business`, `customer`, `staff`, `date`, `type_of_time`, `total_hrs`, `timer_tracked`, `comment_box`) VALUES ('$businessid', '$clientid', '$contactid', '$timer_date', 'Regular Hrs.', '$time_dec', '$time_dec', 'Time Tracked To Contact on Task #$taskid')";
        $result_add_time = mysqli_query($dbc, $query_add_time);

        insert_day_overview($dbc, $contactid, 'Task', date('Y-m-d'), '', "Updated Task #$taskid - Tracked Time To Contact: $timer_value_contact");

        $query_update_time = "UPDATE `tasklist` SET `work_time`=ADDTIME(`work_time`,'$timer_value_contact') WHERE `tasklistid`='$taskid'";
        $result_update_time = mysqli_query($dbc, $query_update_time);
    }
}

if($_GET['fill'] == 'manual_add_time') {
    $taskid = filter_var($_POST['taskid'],FILTER_SANITIZE_STRING);
    $projectid = filter_var($_POST['projectid'],FILTER_SANITIZE_STRING);
    $businessid = filter_var($_POST['businessid'],FILTER_SANITIZE_STRING);
    $clientid = filter_var($_POST['clientid'],FILTER_SANITIZE_STRING);
    $timer = filter_var($_POST['timer'],FILTER_SANITIZE_STRING);
    $time = filter_var($_POST['time'],FILTER_SANITIZE_STRING);
    $time_arr = explode(':', $time);
    $time_dec = number_format(($time_arr[0] + ($time_arr[1]/60) + ($time_arr[2]/3600)), 4);
    $contactid = filter_var($_POST['contactid'],FILTER_SANITIZE_STRING);
    $timer_date = date('Y-m-d');

    if($time != '0' && $time != '00:00:00' && $time != '') {
        if ( !empty($projectid) ) {
            $query_add_time = "INSERT INTO `time_cards` (`projectid`, `staff`, `date`, `type_of_time`, `total_hrs`, `timer_tracked`, `comment_box`) VALUES ('$projectid', '$contactid', '$timer_date', 'Regular Hrs.', '$time_dec', '$time_dec', 'Time Added To Project #$projectid on Task #$taskid')";
        }
        if ( !empty($businessid) || !empty($clientid) ) {
            $query_add_time = "INSERT INTO `time_cards` (`business`, `customer`, `staff`, `date`, `type_of_time`, `total_hrs`, `timer_tracked`, `comment_box`) VALUES ('$businessid', '$clientid', '$contactid', '$timer_date', 'Regular Hrs.', '$time_dec', '$time_dec', 'Time Added To Contact on Task #$taskid')";
        }
        $result_add_time = mysqli_query($dbc, $query_add_time);

        $description = 'Updated Task #'.$taskid.' - Added Time To '.(!empty($projectid) ? 'Project #'.$projectid : 'Contact').' : '.$time;
        insert_day_overview($dbc, $contactid, 'Task', date('Y-m-d'), '', $description);

        $query_update_time = "UPDATE `tasklist` SET `work_time`=ADDTIME(`work_time`,'$time') WHERE `tasklistid`='$taskid'";
        $result_update_time = mysqli_query($dbc, $query_update_time);
    }
}

if($_GET['fill'] == 'update_assigned_staff') {
    $taskid = filter_var($_GET['taskid'],FILTER_SANITIZE_STRING);
    $staff_list = $_GET['staff_list'];
    $updated_date = date('Y-m-d');
    $updated_by = $_SESSION['contactid'];
    mysqli_query($dbc, "UPDATE tasklist SET contactid='$staff_list', updated_date='$updated_date', updated_by='$updated_by' WHERE tasklistid='$taskid'");
}
if($_GET['action'] == 'set_path_name') {
	$name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
	$taskboard = filter_var($_POST['taskboard'],FILTER_SANITIZE_STRING);
	$dbc->query("UPDATE `task_board` SET `task_path_name`='$name' WHERE `taskboardid`='$taskboard'");
}

//Checklist quick action
if ( $_GET['fill']=='checklistFlagItem' ) {
	$checklistid = $_POST['id'];

	$colours = explode(',', get_config($dbc, "ticket_colour_flags"));
	$labels = explode('#*#', get_config($dbc, "ticket_colour_flag_names"));

	$value = mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colour` FROM `checklist` WHERE `checklistid` = '$checklistid'"))['flag_colour'];

	$colour_key = array_search($value, $colours);
	$new_colour = ($colour_key === FALSE ? $colours[0] : ($colour_key + 1 < count($colours) ? $colours[$colour_key + 1] : ''));
	$label = ($colour_key === FALSE ? $labels[0] : ($colour_key + 1 < count($colours) ? $labels[$colour_key + 1] : ''));
	echo $new_colour;
	mysqli_query($dbc, "UPDATE `checklist` SET `flag_colour`='$new_colour' WHERE `checklistid`='$checklistid'");
}
if ( $_GET['fill']=='checklistReminder') {
    $taskboardid = $_POST['taskboardid'];
	$checklistid = $_POST['id'];
	$value = $_POST['value'];

	$sender = get_email($dbc, $_SESSION['contactid']);
	$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid` = '$checklistid'"));
	$id = $result['checklistid'];
    $task_board = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `task_board` WHERE `taskboardid` = '$taskboardid'"));
    $board_name = $task_board['board_name'];
    $tab = $task_board['board_security'];
	$milestone = $result['task_milestone_timeline'];
    $subject = "A reminder about Checklist #".$checklistid.": ".$result['checklist_name']." in $board_name task board  $milestone";
	foreach($_POST['users'] as $i => $user) {
		$user = filter_var($user,FILTER_SANITIZE_STRING);
		$contacts = mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$user'");
		$body = filter_var(htmlentities("This is a reminder about Checklist #".$checklistid.": ".$result['checklist_name']." in $board_name task board  $milestone<br />\n<br />
			<a href='".WEBSITE_URL."/Sales/sales.php?p=preview&id=$salesid'>Click here</a> to see the Sales.<br />\n"), FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "UPDATE `reminders` SET `done` = 1 WHERE `contactid` = '$user' AND `src_table` = 'task_board' AND `src_tableid` = '$taskboardid' AND `src_table` != '' AND `src_table` IS NOT NULL");
		$result = mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_time`, `reminder_type`, `subject`, `body`, `sender`, `src_table`, `src_tableid`)
			VALUES ('$user', '$value', '08:00:00', 'QUICK', '$subject', '$body', '$sender', 'task_board', '$taskboardid')");
	}
}
if ( $_GET['fill']=='checklistArchive' ) {
	$checklistid = $_POST['id'];
        $date_of_archival = date('Y-m-d');
	echo "UPDATE `checklist` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `checklistid` = '$checklistid'";
	mysqli_query($dbc, "UPDATE `checklist` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `checklistid` = '$checklistid'");
}
?>
