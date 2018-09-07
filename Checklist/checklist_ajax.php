<?php
error_reporting(0);
include_once ('../include.php');
ob_clean();

if($_GET['fill'] == 'checklist') {
    $id = $_GET['checklistid'];
    $checked = $_GET['checked'];
	$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
    $updated_by = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
    $updated_date = date('Y-m-d');

	if($ticketid > 0 && $checked > 0) {
		$query_update_project = "UPDATE `checklist_name` SET  `ticket_checked`=CONCAT(IFNULL(`ticket_checked`,''),',".$ticketid."'), `updated_date`='$updated_date', `time_checked`=CURRENT_TIMESTAMP, `updated_by`='$updated_by'  WHERE `checklistnameid` = '$id'";
		$ticket_heading = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading` FROM `tickets` WHERE `ticketid` = '$ticketid'"))['heading'];
		$day_overview_comment = 'Completed Ticket #'.$ticketid.(!empty($ticket_heading) ? ': '.$ticket_heading : '');
	} else if($_GET['ticketid'] > 0) {
		$query_update_project = "UPDATE `checklist_name` SET  `ticket_checked`=REPLACE(REPLACE(CONCAT(',',IFNULL(`ticket_checked`,''),','),',".$ticketid.",',','),',,',','), `updated_date`='$updated_date', `time_checked`=CURRENT_TIMESTAMP, `updated_by`='$updated_by'  WHERE `checklistnameid` = '$id'";
		$ticket_heading = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading` FROM `tickets` WHERE `ticketid` = '$ticketid'"))['heading'];
		$day_overview_comment = 'Unchecked Ticket #'.$ticketid.(!empty($ticket_heading) ? ': '.$ticket_heading : '');
	} else {
		$query_update_project = "UPDATE `checklist_name` SET  `checked`='$checked', `updated_date`='$updated_date', `time_checked`=CURRENT_TIMESTAMP, `updated_by`='$updated_by'  WHERE `checklistnameid` = '$id'";
		$checklist_item = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `checklist` FROM `checklist_name` WHERE `checklistnameid` = '$id'"))['checklist'];
		$checklist_item = explode('&lt;p&gt;', $checklist_item)[0];
		if($checked > 0) {
			$day_overview_comment = 'Completed Checklist Item '.$checklist_item;
		} else {
			$day_overview_comment = 'Unchecked Checklist Item '.$checklist_item;
		}
	}echo $query_update_project;
	$result_update_project = mysqli_query($dbc, $query_update_project);

	//Check or uncheck checklist_actions and reminders for this checklist item
	mysqli_query($dbc, "UPDATE `checklist_actions` SET `done` = '$checked' WHERE `checklistnameid` = '$id'");
	mysqli_query($dbc, "UPDATE `reminders` SET `done` = '1' WHERE `src_table` = 'checklist_name' AND `src_tableid` = '$id'");

	//Insert into day overview table
	$parent_checklistid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `checklistid` FROM `checklist_name` WHERE `checklistnameid` = '$id'"))['checklistid'];
	$checklist_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `checklist_name` FROM `checklist` WHERE `checklistid` = '$parent_checklistid'"))['checklist_name'];
	insert_day_overview($dbc, $_SESSION['contactid'], 'Checklist', date('Y-m-d'), '', 'Updated Checklist '.$checklist_name.' - '.$day_overview_comment, $parent_checklistid);

    $get_item = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist_name` WHERE `checklistnameid`='$id'"));
	$checklistid = $get_item['checklistid'];
	$item = html_entity_decode($get_item['checklist']);
    $get_subtab = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid` = '$checklistid'"));
    $subtabid = $get_subtab['subtabid'];
    $checklist_name = $get_subtab['checklist_name'];
    $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' '.($checked ? 'Completed' : 'Marked Incomplete').' Checklist Item '.$item.' in <b>'.$checklist_name.'</b> on '.date('Y-m-d');
    $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '$checklist_name', '', '', '$checklistid', '$subtabid')";
    $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

    add_update_history($dbc, 'checklist_history', $report, '', $before_change);

	foreach(alerts_enabled($dbc, $id, 'checklist_name') as $user) {
		$link = WEBSITE_URL."/Checklist/checklist.php?view=".$checklistid;
		$text = "Checklist: $checklist_name";
		try {
			$sql = mysqli_query($dbc, "INSERT INTO `alerts` (`alert_date`, `alert_link`, `alert_text`, `alert_user`) VALUES ('$updated_date', '$link', '$text', '$user')");
			$to = get_email($dbc, $user);
			$subject = "Alert for Checklist $checklist_name";
			$body = "<p>An item on $checklist_name has been updated. To review this checklist, click <a href='$link'>here</a>. The item that was ".($checked ? 'completed' : 'marked incomplete')." was:<br />".explode('<p>', $item)[0]."</p>
				<p>You are receiving this email because alerts have been turned on for you for this item or checklist.</p>";
			send_email([get_email($dbc, $_SESSION['contactid'])=>get_contact($dbc, $_SESSION['contactid'])], $to, '', '', $subject, $body, '');
		} catch(Exception $e) { }
	}
}

if($_GET['fill'] == 'checklist_priority') {
    $checklistnameid = $_GET['checklistnameid'];
    $after_checklistnameid = $_GET['after_checklistnameid'];
    $checklistnameid_pri = get_checklist_name($dbc, $after_checklistnameid, 'priority')+1;
    $checklistid = get_checklist_name($dbc, $checklistnameid, 'checklistid');

	$query_update_project = "UPDATE `checklist_name` SET  `priority`=`priority`+1 WHERE `priority` >= '$checklistnameid_pri' AND `checklistid` = '$checklistid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);

	$query_update_project = "UPDATE `checklist_name` SET  `priority`='$checklistnameid_pri' WHERE `checklistnameid` = '$checklistnameid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);
}

if($_GET['fill'] == 'projectname') {
	$businessid = $_GET['businessid'];

    $query = mysqli_query($dbc,"SELECT projectid, project_name FROM project WHERE businessid = '$businessid' and deleted=0");
    echo '<option value="">Please Select</option>';
    while($row = mysqli_fetch_array($query)) {
        echo "<option value='".$row['projectid']."'>".$row['project_name']."</option>\n";
    }
}

if($_GET['fill'] == 'ticket_list') {
	$businessid = $_GET['businessid'];

    $query = mysqli_query($dbc,"SELECT ticketid, heading FROM tickets WHERE businessid = '$businessid' and deleted=0");
    echo '<option value=""></option>';
    while($row = mysqli_fetch_array($query)) {
        echo "<option value='".$row['ticketid']."'>".TICKET_NOUN."# ".$row['ticketid'].' '.$row['heading']."</option>\n";
    }
}

if($_GET['fill'] == 'add_checklist') {

    $checklistid = $_GET['checklistid'];
    $checklist = $_GET['checklist'];

    $contactid = $_SESSION['contactid'];

	$checklist = str_replace("FFMEND","&",$checklist);
    $checklist = str_replace("FFMSPACE"," ",$checklist);
    $checklist = str_replace("FFMHASH","#",$checklist);

    $checklist = filter_var($checklist,FILTER_SANITIZE_STRING);
    $checklist_name = get_checklist($dbc, $checklistid, 'checklist_name');

    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT MAX(priority) AS total_checklistnameid FROM	checklist_name WHERE checklistid='$checklistid'"));
    $max_checklist = $get_staff['total_checklistnameid']+1;

    $get_subtab = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid` = '$checklistid'"));
    $subtabid = $get_subtab['subtabid'];

    if($checklist != '') {
        $query_insert_log = "INSERT INTO `checklist_name` (`checklistid`, `checklist`, `priority`) VALUES ('$checklistid', '$checklist', '$max_checklist')";
        $result_insert_log = mysqli_query($dbc, $query_insert_log);

        $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' [PROFILE '.$_SESSION['contactid'].'] Added Checklist Item in <b>'.$checklist_name.'</b> on '.date('Y-m-d');
        $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '$checklist_name', '', '', '$checklistid', '$subtabid')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

        $before_change = '';
        $start_word = strpos($report, "Updated");
        $end_word = strpos($report, " on");
        $history = substr($report, $start_word, $end_word - $start_word) . "<br />";
        add_update_history($dbc, 'checklist_history', $history, '', $before_change);

    }
}

if($_GET['fill'] == 'delete_checklist') {
	$id = $_GET['checklistid'];
    $date_of_archival = date('Y-m-d');
    $before_change = capture_before_change($dbc, 'checklist_name', 'deleted', 'checklistnameid', $id);
    $before_change .= capture_before_change($dbc, 'checklist_name', 'date_of_archival', 'checklistnameid', $id);
	  $query = "UPDATE `checklist_name` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `checklistnameid`=$id";
	  $result = mysqli_query($dbc,$query);

    $get_item = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist_name` WHERE `checklistnameid`='$id'"));
	  $checklistid = $get_item['checklistid'];
	  $item = html_entity_decode($get_item['checklist']);
    $get_subtab = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid` = '$checklistid'"));
    $subtabid = $get_subtab['subtabid'];
    $checklist_name = $get_subtab['checklist_name'];
    $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Archived Checklist Item '.$item.' in <b>'.$checklist_name.'</b> on '.date('Y-m-d');
    $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '$checklist_name', '', '', '$checklistid', '$subtabid')";
    $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

    $start_word = strpos($report, "Updated");
    $end_word = strpos($report, " on");
    $history = substr($report, $start_word, $end_word - $start_word) . "<br />";
    add_update_history($dbc, 'checklist_history', $history, '', $before_change);

	foreach(alerts_enabled($dbc, $id, 'checklist_name') as $user) {
		$link = WEBSITE_URL."/Checklist/checklist.php?view=".$checklistid;
		$text = "Checklist: $checklist_name";
		try {
			$sql = mysqli_query($dbc, "INSERT INTO `alerts` (`alert_date`, `alert_link`, `alert_text`, `alert_user`) VALUES ('$updated_date', '$link', '$text', '$user')");
			$to = get_email($dbc, $user);
			$subject = "Alert for Checklist $checklist_name";
			$body = "<p>An item on $checklist_name has been archived. To review this checklist, click <a href='$link'>here</a>. The item that was archived was:<br />$item</p>
				<p>You are receiving this email because alerts have been turned on for you for this item or checklist.</p>";
			send_email([get_email($dbc, $_SESSION['contactid'])=>get_contact($dbc, $_SESSION['contactid'])], $to, '', '', $subject, $body, '');
		} catch(Exception $e) { }
	}
}
if($_GET['fill'] == 'checklistreply') {
	$id = $_POST['id'];
	$reply = filter_var(htmlentities('<p>'.$_POST['reply'].'</p>'),FILTER_SANITIZE_STRING);
  $before_change = capture_before_change($dbc, 'checklist_name', 'checklist', 'checklistnameid', $id);
	$query = "UPDATE `checklist_name` SET `checklist`=CONCAT(`checklist`,'$reply') WHERE `checklistnameid`='$id'";
	$result = mysqli_query($dbc,$query);

    $item_query = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist_name` WHERE `checklistnameid`='$id'"));
    $checklistid = $item_query['checklistid'];
    $get_subtab = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid` = '$checklistid'"));
    $subtabid = $get_subtab['subtabid'];
    $checklist_name = $get_subtab['checklist_name'];
    $item_name = explode('&lt;p&gt;', $item_query['checklist']);

    $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Replied to Checklist Item <b>'.$item_name[0].'</b> on '.date('Y-m-d');
    $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '$checklist_name', '', '', '$checklistid', '$subtabid')";
    $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

    $start_word = strpos($report, "Updated");
    $end_word = strpos($report, " on");
    $history = substr($report, $start_word, $end_word - $start_word) . "<br />";
    add_update_history($dbc, 'checklist_history', $history, '', $before_change);

	foreach(alerts_enabled($dbc, $id, 'checklist_name') as $user) {
		$link = WEBSITE_URL."/Checklist/checklist.php?view=".$checklistid;
		$text = "Checklist: $checklist_name";
		try {
			$sql = mysqli_query($dbc, "INSERT INTO `alerts` (`alert_date`, `alert_link`, `alert_text`, `alert_user`) VALUES ('$updated_date', '$link', '$text', '$user')");
			$to = get_email($dbc, $user);
			$subject = "Alert for Checklist $checklist_name";
			$body = "<p>An item on $checklist_name has had a reply added. To review this checklist, click <a href='$link'>here</a>. The item that received a reply was:<br />".html_entity_decode($item_query['checklist'])."</p>
				<p>You are receiving this email because alerts have been turned on for you for this item or checklist.</p>";
			send_email([get_email($dbc, $_SESSION['contactid'])=>get_contact($dbc, $_SESSION['contactid'])], $to, '', '', $subject, $body, '');
		} catch(Exception $e) { }
	}
}
if($_GET['fill'] == 'checklistedit') {
	$id = $_POST['id'];
	$line = $_POST['checklist'];
	$checklist = mysqli_fetch_array(mysqli_query($dbc, "SELECT `checklist` FROM `checklist_name` WHERE `checklistnameid`='$id'"))['checklist'];
	$checklist = explode('<p>',html_entity_decode($checklist));
	unset($checklist[0]);
	$checklist = filter_var(htmlentities($line.'<p>'.implode('</p></p>',$checklist).'</p>'),FILTER_SANITIZE_STRING);
  $before_change = capture_before_change($dbc, 'checklist_name', 'checklist', 'checklistnameid', $id);
	$query = "UPDATE `checklist_name` SET `checklist`='$checklist' WHERE `checklistnameid`='$id'";
	$result = mysqli_query($dbc,$query);

    $item_query = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist_name` WHERE `checklistnameid`='$id'"));
    $checklistid = $item_query['checklistid'];
    $get_subtab = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid` = '$checklistid'"));
    $subtabid = $get_subtab['subtabid'];
    $checklist_name = $get_subtab['checklist_name'];
    $item_name = explode('&lt;p&gt;', $item_query['checklist']);

    $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Updated Checklist Item <b>'.$item_name[0].'</b> on '.date('Y-m-d');
    $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '$checklist_name', '', '', '$checklistid', '$subtabid')";
    $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

    $start_word = strpos($report, "Updated");
    $end_word = strpos($report, " on");
    $history = substr($report, $start_word, $end_word - $start_word) . "<br />";
    add_update_history($dbc, 'checklist_history', $history, '', $before_change);


	foreach(alerts_enabled($dbc, $id, 'checklist_name') as $user) {
		$link = WEBSITE_URL."/Checklist/checklist.php?view=".$checklistid;
		$text = "Checklist: $checklist_name";
		try {
			$sql = mysqli_query($dbc, "INSERT INTO `alerts` (`alert_date`, `alert_link`, `alert_text`, `alert_user`) VALUES ('$updated_date', '$link', '$text', '$user')");
			$to = get_email($dbc, $user);
			$subject = "Alert for Checklist $checklist_name";
			$body = "<p>An item on $checklist_name has been updated. To review this checklist, click <a href='$link'>here</a>. The item that was edited was:<br />".html_entity_decode($item_name[0])."</p>
				<p>You are receiving this email because alerts have been turned on for you for this item or checklist.</p>";
			send_email([get_email($dbc, $_SESSION['contactid'])=>get_contact($dbc, $_SESSION['contactid'])], $to, '', '', $subject, $body, '');
		} catch(Exception $e) { }
	}
}
if($_GET['fill'] == 'checklistalert') {
	$item_id = $_POST['id'];
	$type = $_POST['type'];
	$user = $_POST['user'];
	$enabled_list = implode(',',$user);

  $before_change = capture_before_change($dbc, 'checklist', 'alerts_enabled', 'checklistid', $item_id);
	if($type == 'checklist') {
		$item = mysqli_fetch_array(mysqli_query($dbc, "SELECT `alerts_enabled`, `checklist` FROM `checklist_name` WHERE `checklistnameid`='$item_id'"));
		$previous = explode(',',$item['alerts_enabled']);
		$descript = $item['checklist'];
		$change = "<p><em>";
		foreach(explode(',',$user) as $assigned) {
			if($assigned > 0) {
				$change .= ($change == '<p><em>' ? '' : ', ').get_contact($dbc, $assigned);
			}
		}
		$change = " assigned to this item on ".date('Y-m-d')."</em></p>";
		mysqli_query($dbc, "UPDATE `checklist_name` SET `alerts_enabled`='$enabled_list', `checklist`=CONCAT(`checklist`,'".htmlentities($change)."') WHERE `checklistnameid`='$item_id'");
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM checklist_name WHERE checklistnameid='$item_id'"));
		$checklistid = $result['checklistid'];
	} else {
		$item = mysqli_fetch_array(mysqli_query($dbc, "SELECT `alerts_enabled`, `checklist_name` FROM `checklist` WHERE `checklistid`='$item_id'"));
		$previous = explode(',',$item['alerts_enabled']);
		$descript = $item['checklist_name'];
		mysqli_query($dbc, "UPDATE `checklist` SET `alerts_enabled`='$enabled_list' WHERE `checklistid`='$item_id'");
		$checklistid = $item_id;
	}
	//$link = WEBSITE_URL."/Checklist/my_checklist.php?checklistid=".$id;
	//$text = "Checklist";
	//$date = date('Y/m/d');
    //foreach ((array)$user as $singleuser) {
    //    $sql = mysqli_query($dbc, "INSERT INTO `alerts` (`alert_date`, `alert_link`, `alert_text`, `alert_user`) VALUES ('$date', '$link', '$text', '$singleuser')");
    //}

    //$item_query = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist_name` WHERE `checklistnameid`='$item_id'"));
    //$checklistid = $item_query['checklistid'];
    $get_subtab = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid` = '$checklistid'"));
    $subtabid = $get_subtab['subtabid'];
    $checklist_name = $get_subtab['checklist_name'];
	$checklist_type = $get_subtab['checklist_type'];
    $item_name = explode('&lt;p&gt;', $item_query['checklist']);

	foreach($previous as $disabled) {
		if(!in_array($disabled,$user)) {
			$report .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' disabled alerts to user #'.$disabled.' for Checklist Item <b>'.$descript.'</b> on '.date('Y-m-d').'<br />';
		}
	}
	foreach($user as $enabled) {
		if(!in_array($enabled,$previous)) {
			$report .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' enabled alerts to user #'.$enabled.' for Checklist Item <b>'.$descript.'</b> on '.date('Y-m-d').'<br />';
		}
	}
    $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '$checklist_name', '', '$checklist_type', '$checklistid', '$subtabid')";
    $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

    $start_word = strpos($report, "Updated");
    $end_word = strpos($report, " on");
    $history = substr($report, $start_word, $end_word - $start_word) . "<br />";
    add_update_history($dbc, 'checklist_history', $history, '', $before_change);
}
if($_GET['fill'] == 'checklistemail') {
	$item_id = $_POST['id'];
	$type = $_POST['type'];
	$user = $_POST['user'];
	$subject = '';
	$title = '';
	if($type == 'checklist') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM checklist_name WHERE checklistnameid='$item_id'"));
		$id = $result['checklistid'];
		$title = explode('<p>',html_entity_decode($result['checklist']))[0];
		$subject = "A reminder about the $title on the checklist";
	}
	else {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM checklist WHERE checklistid = '$item_id'"));
		$id = $item_id;
		$title = $result['checklist_name'];
		$subject = "A reminder about the $title checklist";
	}
    foreach ((array)$user as $singleuser) {
        $contacts = mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$singleuser'");
        while($row = mysqli_fetch_array($contacts)) {
            if(trim($row['email_address']) != '') {
                $body = "Hi ".decryptIt($row['first_name'])."<br />\n<br />
                    This is a reminder about the $title on the checklist.<br />\n<br />
                    <a href='".WEBSITE_URL."/Checklist/checklist.php?checklistid=$id'>Click here</a> to see the checklist.";
                send_email([get_email($dbc, $_SESSION['contactid'])=>get_contact($dbc, $_SESSION['contactid'])], decryptIt($row['email_address']), '', '', $subject, $body, '');
            }
        }
    }

    $item_query = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist_name` WHERE `checklistnameid`='$item_id'"));
    $checklistid = $item_query['checklistid'];
    $get_subtab = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid` = '$checklistid'"));
    $subtabid = $get_subtab['subtabid'];
    $checklist_name = $get_subtab['checklist_name'];
    $item_name = explode('&lt;p&gt;', $item_query['checklist']);

    $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Sent Email in Checklist Item <b>'.$item_name[0].'</b> on '.date('Y-m-d');
    $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '$checklist_name', '', '', '$checklistid', '$subtabid')";
    $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

    $before_change = '';
    $start_word = strpos($report, "Updated");
    $end_word = strpos($report, " on");
    $history = substr($report, $start_word, $end_word - $start_word) . "<br />";
    add_update_history($dbc, 'checklist_history', $history, '', $before_change);
}
if($_GET['fill'] == 'checklistreminder') {
	$item_id = $_POST['id'];
	$sender = get_email($dbc, $_SESSION['contactid']);
	$date = $_POST['schedule'];
	$type = $_POST['type'];
	$to = $_POST['user'];
	$subject = '';
	$title = '';
	if($type == 'checklist') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM checklist_name WHERE checklistnameid='$item_id'"));
		$id = $result['checklistid'];
		$title = explode('<p>',html_entity_decode($result['checklist']))[0];
		$subject = "A reminder about the $title on the checklist";
		$body = htmlentities("This is a reminder about the $title on the checklist.<br />\n<br />
			<a href=\"".WEBSITE_URL."/Checklist/checklist.php?checklistid=$id\">Click here</a> to see the checklist.");
	}
	else {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM checklist WHERE checklistid = '$item_id'"));
		$id = $item_id;
		$title = $result['checklist_name'];
		$subject = "A reminder about the $title checklist";
		$body = htmlentities("This is a reminder about the $title checklist.<br />\n<br />
			<a href=\"".WEBSITE_URL."/Checklist/checklist.php?checklistid=$id\">Click here</a> to see the checklist.");
	}
    foreach ((array)$to as $singleto) {
        mysqli_query($dbc, "UPDATE `reminders` SET `done` = 1 WHERE `contactid` = '$singleto' AND `src_table` = 'checklist_name' AND `src_tableid` = '$id'");
        $result = mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_time`, `reminder_type`, `subject`, `body`, `sender`, `src_table`, `src_tableid`)
        VALUES ('$singleto', '$date', '08:00:00', 'QUICK', '$subject', '$body', '$sender', 'checklist_name', '$id')");
        $result2 = mysqli_query($dbc, "INSERT INTO `checklist_actions` (`checklistnameid`, `contactid`, `action_date`) VALUES ('$item_id', '$singleto', '$date')");
    }

    $item_query = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist_name` WHERE `checklistnameid`='$item_id'"));
    $checklistid = $item_query['checklistid'];
    $get_subtab = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid` = '$checklistid'"));
    $subtabid = $get_subtab['subtabid'];
    $checklist_name = $get_subtab['checklist_name'];
    $item_name = explode('&lt;p&gt;', $item_query['checklist']);

    $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Scheduled Reminder in Checklist Item <b>'.$item_name[0].'</b> on '.date('Y-m-d');
    $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '$checklist_name', '', '', '$checklistid', '$subtabid')";
    $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

    $before_change = '';
    $start_word = strpos($report, "Updated");
    $end_word = strpos($report, " on");
    $history = substr($report, $start_word, $end_word - $start_word) . "<br />";
    add_update_history($dbc, 'checklist_history', $history, '', $before_change);

}
if($_GET['fill'] == 'checklistflagmanual') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	$label = filter_var($_POST['label'],FILTER_SANITIZE_STRING);
	$start = filter_var($_POST['start'],FILTER_SANITIZE_STRING);
	$end = filter_var($_POST['end'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "UPDATE `checklist_name` SET `flag_colour`='$value',`flag_label`='$label',`flag_start`='$start',`flag_end`='$end' WHERE `checklistnameid`='$id'");
}
if($_GET['fill'] == 'checklistflag') {
	$item_id = $_POST['id'];
	$type = $_POST['type'];
	if($type == 'checklist') {
		$colour = mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colour` FROM checklist_name WHERE checklistnameid = '$item_id'"))['flag_colour'];
		$colour_list = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colours` FROM `field_config_checklist`"))['flag_colours']);
		$colour_key = array_search($colour, $colour_list);
		$new_colour = ($colour_key === FALSE ? $colour_list[0] : ($colour_key + 1 < count($colour_list) ? $colour_list[$colour_key + 1] : ''));
		$result = mysqli_query($dbc, "UPDATE `checklist_name` SET `flag_colour`='$new_colour' WHERE `checklistnameid` = '$item_id'");
		echo $new_colour;
	}
	else {
		$colour = mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colour` FROM checklist WHERE checklistid = '$item_id'"))['flag_colour'];
		$colour_list = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colours` FROM `field_config_checklist`"))['flag_colours']);
		$colour_key = array_search($colour, $colour_list);
		$new_colour = ($colour_key === FALSE ? $colour_list[0] : ($colour_key + 1 < count($colour_list) ? $colour_list[$colour_key + 1] : ''));
		$result = mysqli_query($dbc, "UPDATE `checklist` SET `flag_colour`='$new_colour' WHERE `checklistid` = '$item_id'");
		echo $new_colour;
	}
}
if($_GET['fill'] == 'checklist_upload') {
	$id = $_GET['id'];
	$type = $_GET['type'];
	$filename = htmlspecialchars($_FILES['file']['name'], ENT_QUOTES);
	$file = $_FILES['file']['tmp_name'];
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
	move_uploaded_file($file, "download/".$filename);
	if($type == 'checklist') {
		$query_insert = "INSERT INTO `checklist_name_document` (`checklistnameid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$id', 'Support Document', '$filename', '".date('Y/m/d')."', '".$_SESSION['contactid']."')";
		$result_insert = mysqli_query($dbc, $query_insert);
	}
	else if($type == 'checklist_board') {
		$query_insert = "INSERT INTO `checklist_document` (`checklistid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$id', 'Support Document', '$filename', '".date('Y/m/d')."', '".$_SESSION['contactid']."')";
		$result_insert = mysqli_query($dbc, $query_insert);
	}

    $item_query = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist_name` WHERE `checklistnameid`='$id'"));
    $checklistid = $item_query['checklistid'];
    $get_subtab = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid` = '$checklistid'"));
    $subtabid = $get_subtab['subtabid'];
    $checklist_name = $get_subtab['checklist_name'];
    $item_name = explode('&lt;p&gt;', $item_query['checklist']);

    $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Added File in Checklist Item <b>'.$item_name[0].'</b> on '.date('Y-m-d');
    $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '$checklist_name', '', '', '$checklistid', '$subtabid')";
    $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

    $before_change = '';
    $start_word = strpos($report, "Updated");
    $end_word = strpos($report, " on");
    $history = substr($report, $start_word, $end_word - $start_word) . "<br />";
    add_update_history($dbc, 'checklist_history', $history, '', $before_change);

	foreach(alerts_enabled($dbc, $id, 'checklist_name') as $user) {
		$link = WEBSITE_URL."/Checklist/checklist.php?view=".$checklistid;
		$text = "Checklist: $checklist_name";
		try {
			$sql = mysqli_query($dbc, "INSERT INTO `alerts` (`alert_date`, `alert_link`, `alert_text`, `alert_user`) VALUES ('$updated_date', '$link', '$text', '$user')");
			$to = get_email($dbc, $user);
			$subject = "Alert for Checklist $checklist_name";
			$body = "<p>An item on $checklist_name had a file added. To review this checklist, click <a href='$link'>here</a>. The item that had a file added was:<br />".html_entity_decode($item_name[0])."</p>
				<p>You are receiving this email because alerts have been turned on for you for this item or checklist.</p>";
			send_email([get_email($dbc, $_SESSION['contactid'])=>get_contact($dbc, $_SESSION['contactid'])], $to, '', '', $subject, $body, '');
		} catch(Exception $e) { }
	}
}
if($_GET['fill'] == 'checklist_quick_time') {
	$checklistid = $_POST['id'];
	$time = $_POST['time'];
	$query_time = "INSERT INTO `checklist_name_time` (`checklist_id`, `work_time`, `contactid`, `timer_date`) VALUES ('$checklistid', '$time', '".$_SESSION['contactid']."', '".date('Y-m-d')."')";
	$result = mysqli_query($dbc, $query_time);
    mysqli_query($dbc, "INSERT INTO `time_cards` (`staff`,`date`,`type_of_time`,`total_hrs`,`timer_tracked`,`comment_box`) VALUES ('".$_SESSION['contactid']."','".date('Y-m-d')."','Regular Hrs.','".((strtotime($_POST['time']) - strtotime('00:00:00')) / 3600)."','0','Time Added on Checklist #$checklistid')");
	insert_day_overview($dbc, $_SESSION['contactid'], 'Checklist', date('Y-m-d'), '', "Updated Checklist Item #$checklistid - Added Time : $time");
}
if($_GET['fill'] == 'subtab_change') {
    $subtabid = $_GET['subtabid'];
    $query_subtab = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `checklist_subtab` WHERE `subtabid` = '$subtabid'"));
    $subtab_shared = $query_subtab['shared'];

    echo "<option value=''></option>";
    echo "<option value='ALL'>Share with Everyone</option>";

    $query_retrieve_subtabs = mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `category`, `email_address` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 ORDER BY `category`");
    // while($row = mysqli_fetch_array($query_retrieve_subtabs)) {
    //     echo "<option ".((strpos($subtab_shared, ','.$row['contactid'].',') !== false) ? 'selected' : '')." value='".$row['contactid']."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name'])."</option>";
    // }
    $result_retrieve_subtabs = sort_contacts_array(mysqli_fetch_all($query_retrieve_subtabs, MYSQLI_ASSOC));

    foreach($result_retrieve_subtabs as $row) {
        echo "<option ".((strpos($subtab_shared, ','.$row.',') !== false) ? 'selected' : '')." value='".$row."'>".get_contact($dbc, $row)."</option>";
    }
}
if($_GET['fill'] == 'export_pdf') {
	include('checklist_pdf.php');
}
if($_GET['fill'] == 'mark_favourite') {
	$faves = explode(',',mysqli_fetch_array(mysqli_query($dbc, "SELECT `checklist_fav` FROM `user_settings` WHERE `contactid`='".$_SESSION['contactid']."'"))[0]);
	$key = array_search($_GET['checklistid'], $faves);
	if($key !== FALSE) {
		unset($faves[$key]);
	}
	if($_GET['status'] == 'true') {
		$faves[] = $_GET['checklistid'];
	}
	$favourites = implode(',',$faves);
	mysqli_query($dbc, "UPDATE `user_settings` SET `checklist_fav`='$favourites' WHERE `contactid`='".$_SESSION['contactid']."'");
}
if($_GET['fill'] == 'mark_hidden') {
	$hidden = explode(',',mysqli_fetch_array(mysqli_query($dbc, "SELECT `checklist_hidden` FROM `user_settings` WHERE `contactid`='".$_SESSION['contactid']."'"))[0]);
	$key = array_search($_POST['category'], $hidden);
	if($key !== FALSE) {
		unset($hidden[$key]);
	} else {
		$hidden[] = $_POST['category'];
	}
	$hidden = implode(',',$hidden);
	mysqli_query($dbc, "UPDATE `user_settings` SET `checklist_hidden`='$hidden' WHERE `contactid`='".$_SESSION['contactid']."'");
}
if($_GET['fill'] == 'checklist_doc_remove') {
    $date_of_archival = date('Y-m-d');
	$docid = $_POST['doc'];
	mysqli_query($dbc, "UPDATE `checklist_document` SET `deleted`='1', `date_of_archival` = '$date_of_archival' WHERE `checklistdocid`='$docid'");
}

if($_GET['action'] == 'item_priority') {
    $lineid = $_GET['lineid'];
    $afterid = $_GET['afterid'];
    $checklistid = mysqli_fetch_array(mysqli_query($dbc, "SELECT `checklistid` FROM `item_checklist_line` WHERE `checklistlineid`='$lineid'"))['checklistid'];
    $line_priority = mysqli_fetch_array(mysqli_query($dbc, "SELECT `priority` FROM `item_checklist_line` WHERE `checklistlineid`='$lineid'"))['priority'];
    $after_priority = mysqli_fetch_array(mysqli_query($dbc, "SELECT `priority` FROM `item_checklist_line` WHERE `checklistlineid`='$afterid'"))['priority'];

	$query = "UPDATE `item_checklist_line` SET  `priority`=`priority`+1 WHERE `priority` > '$after_priority' AND `priority` < '$line_priority' AND `checklistid` = '$checklistid'";
	$result = mysqli_query($dbc, $query);

	$query = "UPDATE `item_checklist_line` SET  `priority`='".($after_priority + 1)."' WHERE `checklistlineid` = '$checklistid'";
	$result = mysqli_query($dbc, $query);
}
if($_GET['action'] == 'add_checklist_item') {
	$checklistid = $_POST['checklist'];
	$checklist = filter_var($_POST['line'],FILTER_SANITIZE_STRING);
	$query_insert = "INSERT INTO `item_checklist_line` (`checklistid`, `checklist`, `priority`) SELECT '$checklistid', '$checklist', (IFNULL(MAX(`priority`),1)+1) FROM `item_checklist_line` WHERE `checklistid`='$checklistid'";
	mysqli_query($dbc, $query_insert);
}
if($_GET['action'] == 'item_checklist') {
    $checklistid = $_GET['checklistid'];
    $checked = $_GET['checked'];
	$itemid = $_GET['unit'];
    $updated_by = $_SESSION['contactid'];
    $updated_date = date('Y-m-d');
	$note = ($checked == 1 ? 'Marked done' : 'Unchecked').' by '.get_contact($dbc, $updated_by).' at '.date('Y-m-d, g:i:s A');

	$query = "INSERT INTO `item_checklist_unit` (`checklistlineid`, `item_type`, `item_id`, `checked_by`, `notes`) SELECT `checklistlineid`, `checklist_item`, `item_id`, '$updated_by', '$note' FROM `item_checklist` LEFT JOIN `item_checklist_line` ON `item_checklist`.`checklistid`=`item_checklist_line`.`checklistid` WHERE `item_checklist_line`.`checklistlineid`='$checklistid'";
	$result = mysqli_query($dbc, $query);echo $query;
}
if($_GET['action'] == 'item_alert') {
	$item_id = $_POST['id'];
	$type = $_POST['type'];
	$user = $_POST['user'];
	if($type == 'checklist') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM item_checklist_line WHERE checklistlineid='$item_id'"));
		$id = $result['checklistid'];
	}
	else {
		$id = $item_id;
	}
	$link = WEBSITE_URL."/Checklist/checklist.php?item_view=".$id;
	$text = "Checklist";
	$date = date('Y/m/d');
	$sql = mysqli_query($dbc, "INSERT INTO `alerts` (`alert_date`, `alert_link`, `alert_text`, `alert_user`) VALUES ('$date', '$link', '$text', '$user')");
}
if($_GET['action'] == 'item_email') {
	$item_id = $_POST['id'];
	$type = $_POST['type'];
	$user = $_POST['user'];
	$subject = '';
	$title = '';
	if($type == 'checklist') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM item_checklist_line WHERE checklistlineid='$item_id'"));
		$id = $result['checklistid'];
		$title = explode('<p>',html_entity_decode($result['checklist']))[0];
		$subject = "A reminder about the $title on the checklist";
	}
	else {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM checklist WHERE checklistid = '$item_id'"));
		$id = $item_id;
		$title = $result['item_checklist_line'];
		$subject = "A reminder about the $title checklist";
	}
	$contacts = mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$user'");
	while($row = mysqli_fetch_array($contacts)) {
		$email_address = get_email($dbc, $row['contactid']);
		if(trim($email_address) != '') {
			$body = "Hi ".decryptIt($row['first_name'])."<br />\n<br />
				This is a reminder about the $title on a checklist.<br />\n<br />
				<a href='".WEBSITE_URL."/Checklist/checklist.php?item_view=".$id."\">Click here</a> to see the checklists page.";
			send_email('', $email_address, '', '', $subject, $body, '');
		}
	}
}
if($_GET['action'] == 'item_reminder') {
	$item_id = $_POST['id'];
	$sender = get_email($dbc, $_SESSION['contactid']);
	$date = $_POST['schedule'];
	$type = $_POST['type'];
	$to = $_POST['user'];
	$subject = '';
	$title = '';
	if($type == 'checklist') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM item_checklist_line WHERE checklistlineid='$item_id'"));
		$id = $result['checklistid'];
		$title = explode('<p>',html_entity_decode($result['checklist']))[0];
		$subject = "A reminder about the $title on a checklist";
		$body = htmlentities("This is a reminder about the $title on a checklist.<br />\n<br />
			<a href=\"".WEBSITE_URL."/Checklist/checklist.php?item_view=".$id."\">Click here</a> to see the checklists page.");
	}
	else {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM checklist WHERE checklistid = '$item_id'"));
		$id = $item_id;
		$title = $result['item_checklist_line'];
		$subject = "A reminder about the $title checklist";
		$body = htmlentities("This is a reminder about the $title checklist.<br />\n<br />
			<a href=\"".WEBSITE_URL."/Checklist/checklist.php?item_view=".$id."\">Click here</a> to see the checklists page.");
	}
	mysqli_query($dbc, "UPDATE `reminders` SET `done` = 1 WHERE `contactid` = '$to' AND `src_table` = 'checklist_name' AND `src_tableid` = '$id'");
	$result = mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_time`, `reminder_type`, `subject`, `body`, `sender`, `src_table`, `src_tableid`)
		VALUES ('$to', '$date', '08:00:00', 'QUICK', '$subject', '$body', '$sender', 'checklist_name', '$id')");
}
if($_GET['action'] == 'item_reply') {
	$id = $_POST['id'];
	$reply = filter_var(htmlentities('<p>'.$_POST['reply'].'</p>'),FILTER_SANITIZE_STRING);
	$query = "UPDATE `item_checklist_line` SET `checklist`=CONCAT(`checklist`,'$reply') WHERE `checklistlineid`='$id'";
	$result = mysqli_query($dbc,$query);
}
if($_GET['action'] == 'item_quick_time') {
	$checklistid = $_POST['id'];
	$time = $_POST['time'];
	$query_time = "INSERT INTO `item_checklist_time` (`checklistlineid`, `work_time`, `contactid`, `timer_date`) VALUES ('$checklistid', '$time', '".$_SESSION['contactid']."', '".date('Y-m-d')."')";
	$result = mysqli_query($dbc, $query_time);
	insert_day_overview($dbc, $_SESSION['contactid'], 'Checklist', date('Y-m-d'), '', "Updated Checklist Item #$checklistid - Added Time : $time");
}
if($_GET['action'] == 'item_upload') {
	$id = $_GET['id'];
	$type = $_GET['type'];
	$filename = $_FILES['file']['name'];
	$file = $_FILES['file']['tmp_name'];
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
	$basefilename = $filename = preg_replace('/[^A-Za-z0-9\.]/','_',$filename);
	$i = 0;
	while(file_exists('download/'.$filename)) {
		$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.++$i.')$1', $basefilename);
	}
	move_uploaded_file($file, "download/".$filename);
	if($type == 'checklist') {
		$query_insert = "INSERT INTO `item_checklist_document` (`checklistlineid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$id', 'Support Document', '$filename', '".date('Y/m/d')."', '".$_SESSION['contactid']."')";
		$result_insert = mysqli_query($dbc, $query_insert);
	}
	else if($type == 'checklist_board') {
		$query_insert = "INSERT INTO `item_checklist_document` (`checklistid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$id', 'Support Document', '$filename', '".date('Y/m/d')."', '".$_SESSION['contactid']."')";
		$result_insert = mysqli_query($dbc, $query_insert);

	}
}
if($_GET['action'] == 'item_flag') {
	$item_id = $_POST['id'];
	$type = $_POST['type'];
	if($type == 'checklist') {
		$colour = mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colour` FROM item_checklist_line WHERE checklistlineid = '$item_id'"))['flag_colour'];
		$colour_list = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colours` FROM `field_config_checklist`"))['flag_colours']);
		$colour_key = array_search($colour, $colour_list);
		$new_colour = ($colour_key === FALSE ? $colour_list[0] : ($colour_key + 1 < count($colour_list) ? $colour_list[$colour_key + 1] : ''));
		$result = mysqli_query($dbc, "UPDATE `item_checklist_line` SET `flag_colour`='$new_colour' WHERE `checklistlineid` = '$item_id'");
		echo $new_colour;
	}
	else {
		$colour = mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colour` FROM item_checklist WHERE checklistid = '$item_id'"))['flag_colour'];
		$colour_list = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colours` FROM `field_config_checklist`"))['flag_colours']);
		$colour_key = array_search($colour, $colour_list);
		$new_colour = ($colour_key === FALSE ? $colour_list[0] : ($colour_key + 1 < count($colour_list) ? $colour_list[$colour_key + 1] : ''));
		$result = mysqli_query($dbc, "UPDATE `item_checklist` SET `flag_colour`='$new_colour' WHERE `checklistid` = '$item_id'");
		echo $new_colour;
	}
}
if($_GET['action'] == 'item_delete') {
	$id = $_GET['checklistid'];
    $date_of_archival = date('Y-m-d');
	$query = "UPDATE `item_checklist_line` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `checklistlineid`='$id'";
	$result = mysqli_query($dbc,$query);
}
if($_GET['fill'] == 'delete_checklist_board') {
    $checklistid = $_GET['checklistid'];
	$checklist_type = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid`='$checklistid'"));
    $query_update = "UPDATE `checklist` SET deleted='1' WHERE checklistid='$checklistid'";
    $result_update = mysqli_query($dbc, $query_update);
} ?>
