<?php
include_once ('../include.php');
ob_clean();

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

if($_GET['fill'] == 'move_ticket') {
    $ticketid = $_GET['ticketid'];
    $status = $_GET['status'];
	$status = str_replace("FFMEND","&",$status);
    $status = str_replace("FFMSPACE"," ",$status);
    $status = str_replace("FFMHASH","#",$status);

	$query_update_project = "UPDATE `tickets` SET  status='$status', `status_date`=NOW() WHERE `ticketid` = '$ticketid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);
}

if($_GET['fill'] == 'add_scrum') {

    $status = $_GET['status'];
    $project_path = $_GET['project_path'];
    $heading = $_GET['heading'];

    $contactid = ','.$_SESSION['contactid'].',';

	$status = str_replace("FFMEND","&",$status);
    $status = str_replace("FFMSPACE"," ",$status);
    $status = str_replace("FFMHASH","#",$status);

	$heading = str_replace("FFMEND","&",$heading);
    $heading = str_replace("FFMSPACE"," ",$heading);
    $heading = str_replace("FFMHASH","#",$heading);

    $heading = filter_var($heading,FILTER_SANITIZE_STRING);

    if($heading != '') {
        $query_insert_log = "INSERT INTO `tickets` (`project_path`, `status`, `heading`, `contactid`) VALUES ('$project_path', '$status', '$heading', '$contactid')";
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
}
if($_GET['fill'] == 'sendnote') {
	$item_id = $_POST['id'];
	$user = $_SESSION['contactid'];
	$note = filter_var(htmlentities('<p>'.$_POST['note'].'</p>'),FILTER_SANITIZE_STRING);
	$query_insert_note = "INSERT INTO `ticket_comment` (`ticketid`, `comment`, `created_date`, `created_by`, `type`, `note_heading`) VALUES ('$item_id', '$note', CURDATE(), '$user', 'note', 'Quick Note')";
	$result = mysqli_query($dbc, $query_insert_note);
}
if($_GET['fill'] == 'sendemail') {
	$item_id = $_POST['id'];
	$sender = get_email($dbc, $_SESSION['contactid']);
	$to = get_email($dbc, $_POST['user']);
	$ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM tickets WHERE ticketid='$item_id'"));
	$subject = "A reminder about ".TICKET_NOUN." #$item_id";
	$body = 'Please review the following ticket.<br/><br/>
			Client: '.get_client($dbc,$ticket['businessid']).'<br>
			'.TICKET_NOUN.' Heading: '.$ticket['heading'].'<br>
			Status: '.$ticket['status'].'<br>
			<a target="_blank" href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].'">'.TICKET_NOUN.' #'.$ticket['ticketid'].'</a><br/><br/><br/>
			<img src="'.WEBSITE_URL.'/img/ffm-signature.png" width="154" height="77" border="0" alt="">';
	send_email($sender, $to, '', '', $subject, $body);
}
if($_GET['fill'] == 'sendreminder') {
	$item_id = $_POST['id'];
	$sender = get_email($dbc, $_SESSION['contactid']);
	$date = $_POST['schedule'];
	$to = $_POST['user'];
	$ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM tickets WHERE ticketid='$item_id'"));
	$subject = "A reminder about ".TICKET_NOUN." #$item_id";
	$body = htmlentities('Please review the following ticket.<br/><br/>
			Client: '.get_client($dbc,$ticket['businessid']).'<br>
			'.TICKET_NOUN.' Heading: '.$ticket['heading'].'<br>
			Status: '.$ticket['status'].'<br>
			<a target="_blank" href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].'">'.TICKET_NOUN.' #'.$ticket['ticketid'].'</a><br/><br/><br/>
			<img src="'.WEBSITE_URL.'/img/ffm-signature.png" width="154" height="77" border="0" alt="">');
    mysqli_query($dbc, "UPDATE `reminders` SET `done` = 1 WHERE `contactid` = '$to' AND `src_table` = 'tickets' AND `src_tableid` = '$item_id'");
	$result = mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_time`, `reminder_type`, `subject`, `body`, `sender`, `src_table`, `src_tableid`)
		VALUES ('$to', '$date', '08:00:00', 'QUICK', '$subject', '$body', '$sender', 'tickets', '$item_id')");
}
if($_GET['fill'] == 'sendalert') {
	$ticketid = $_POST['id'];
	$user = $_POST['user'];
	$link = WEBSITE_URL."/Ticket/index.php?edit=".$ticketid;
	$text = TICKET_NOUN;
	$date = date('Y/m/d');
	$sql = mysqli_query($dbc, "INSERT INTO `alerts` (`alert_date`, `alert_link`, `alert_text`, `alert_user`) VALUES ('$date', '$link', '$text', '$user')");
}
if($_GET['fill'] == 'sendupload') {
	$ticketid = $_GET['id'];
	$user = $_SESSION['contactid'];
	$filename = htmlspecialchars($_FILES['file']['name'], ENT_QUOTES);
	$file = $_FILES['file']['tmp_name'];
    if (!file_exists('../Ticket/download')) {
        mkdir('..Ticket/download', 0777, true);
    }
	move_uploaded_file($file, '../Ticket/download/'.$filename);
	$query_insert = "INSERT INTO `ticket_document` (`ticketid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$ticketid', 'Support Document', 'download/$filename', CURDATE(), '$user')";
	$result_insert = mysqli_query($dbc, $query_insert);
}
if($_GET['fill'] == 'ticketflag') {
	$item_id = $_POST['id'];
	$colour = mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colour` FROM tickets WHERE ticketid = '$item_id'"))['flag_colour'];
	$colour_list = explode(',', get_config($dbc, "ticket_colour_flags"));
	$colour_key = array_search($colour, $colour_list);
	$new_colour = ($colour_key === FALSE ? $colour_list[0] : ($colour_key + 1 < count($colour_list) ? $colour_list[$colour_key + 1] : ''));
	$result = mysqli_query($dbc, "UPDATE `tickets` SET `flag_colour`='$new_colour' WHERE `ticketid` = '$item_id'");
	echo $new_colour;
}
if($_GET['fill'] == 'quickarchive') {
	$ticketid = $_GET['id'];
	$query_archive = "UPDATE `tickets` SET status = 'Archive', `status_date`=CURDATE() WHERE ticketid='$ticketid'";
	$result = mysqli_query($dbc, $query_archive);
}
if($_GET['fill'] == 'quicktime') {
	$ticketid = $_POST['id'];
	$time = strtotime($_POST['time']);
    $tile = filter_var($_POST['tile'], FILTER_SANITIZE_STRING);
    if ( $tile=='ticket' ) {
        $current_time = strtotime(mysqli_fetch_array(mysqli_query($dbc, "SELECT `spent_time` FROM `tickets` WHERE `ticketid`='$ticketid'"))['spent_time']);
        $total_time = date('H:i:s', $time + $current_time - strtotime('00:00:00'));
        $query_time = "UPDATE `tickets` SET `spent_time` = '$total_time' WHERE ticketid='$ticketid'";
        $result = mysqli_query($dbc, $query_time);
        mysqli_query($dbc, "INSERT INTO `ticket_time_list` (`ticketid`, `time_type`, `time_length`, `created_by`) VALUES ('$ticketid', 'Manual Time', '".$_POST['time']."', '".$_SESSION['contactid']."')");
        mysqli_query($dbc, "INSERT INTO `time_cards` (`ticketid`,`staff`,`date`,`type_of_time`,`total_hrs`,`timer_tracked`,`comment_box`) VALUES ('$ticketid','".$_SESSION['contactid']."','".date('Y-m-d')."','Regular Hrs.','".(($time - strtotime('00:00:00')) / 3600)."','0','Time Added on Ticket #$ticketid')");
        insert_day_overview($dbc, $_SESSION['contactid'], 'Ticket', date('Y-m-d'), '', "Updated ".TICKET_NOUN." #$ticketid - Manually Added Time: ".$_POST['time']);
    }
    if ( $tile=='task' ) {
        mysqli_query($dbc, "UPDATE `tasklist` SET `work_time`=ADDTIME(`work_time`,'$time') WHERE `tasklistid`='$ticketid'");
        mysqli_query($dbc, "INSERT INTO `tasklist_time` (`tasklistid`, `work_time`, `src`, `contactid`, `timer_date`) VALUES ('$ticketid', '".$_POST['time']."', 'M', '".$_SESSION['contactid']."', '".date('Y-m-d')."')");
        mysqli_query($dbc, "INSERT INTO `time_cards` (`staff`,`date`,`type_of_time`,`total_hrs`,`timer_tracked`,`comment_box`) VALUES ('".$_SESSION['contactid']."','".date('Y-m-d')."','Regular Hrs.','".(($time - strtotime('00:00:00')) / 3600)."','0','Time Added on Task #$ticketid')");
        insert_day_overview($dbc, $_SESSION['contactid'], 'Task', date('Y-m-d'), '', "Updated Task #$ticketid - Added Time : ". $_POST['time']);
    }
}
else if($_GET['action'] == 'scrum_notes_load') {
	ob_clean();
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	echo html_entity_decode($dbc->query("SELECT `notes` FROM `daysheet_notepad` WHERE `contactid`=0 AND `date`='$date' AND `date` != ''")->fetch_assoc()['notes'],ENT_QUOTES);
}
else if($_GET['action'] == 'scrum_notes_save') {
	$notes = filter_var(htmlentities($_POST['notes']),FILTER_SANITIZE_STRING);
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	$dbc->query("INSERT INTO `daysheet_notepad` (`contactid`, `date`) SELECT 0, '$date' FROM (SELECT COUNT(*) `rows` FROM `daysheet_notepad` WHERE `date`='$date' AND `contactid`=0) `num` WHERE `num`.`rows`=0");
	$dbc->query("UPDATE `daysheet_notepad` SET `notes`='$notes' WHERE `contactid`=0 AND `date`='$date' AND `date` != ''");
}
else if($_GET['action'] == 'scrum_staff_save') {
	$staff = filter_var($_POST['staff'],FILTER_SANITIZE_STRING);
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	$dbc->query("INSERT INTO `daysheet_notepad` (`contactid`, `date`) SELECT 0, '$date' FROM (SELECT COUNT(*) `rows` FROM `daysheet_notepad` WHERE `date`='$date' AND `contactid`=0) `num` WHERE `num`.`rows`=0");
	$dbc->query("UPDATE `daysheet_notepad` SET `assigned`='$staff' WHERE `contactid`=0 AND `date`='$date' AND `date` != ''");
}
else if($_GET['action'] == 'scrum_timer_start') {
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	$time = date('H:i');
	$timer = time();
	$dbc->query("INSERT INTO `daysheet_notepad` (`contactid`, `date`) SELECT 0, '$date' FROM (SELECT COUNT(*) `rows` FROM `daysheet_notepad` WHERE `date`='$date' AND `contactid`=0) `num` WHERE `num`.`rows`=0");
	$dbc->query("UPDATE `daysheet_notepad` SET `timer_start`='$timer', `start_time`='$time' WHERE `contactid`=0 AND `date`='$date' AND `date` != ''");
}
else if($_GET['action'] == 'scrum_timer_stop') {
	$time = date('H:i');
	$timer = time();
	$dbc->query("UPDATE `daysheet_notepad` SET `end_time`='$time', `timer`=TIME_FORMAT(SEC_TO_TIME($timer - `timer_start` + TIME_TO_SEC(`timer`)),'%H:%i:%s'), `timer_start`=0 WHERE `contactid`=0 AND `timer_start` > 0");
}
?>