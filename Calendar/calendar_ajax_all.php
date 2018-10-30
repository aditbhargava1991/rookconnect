<?php
error_reporting(0);
include ('../include.php');
include ('../Calendar/calendar_functions_inc.php');
ob_clean();

$offline_table = [];
$offline_tableid = [];
$offline_table_field = [];
$offline_fields = [];
$offline_values = [];
$user = $_SESSION['contactid'];
$online = ($_GET['offline'] > 0 ? false : true);

if($_GET['fill'] == 'ticket_calendar') {
    $ticketid = $_GET['ticketid'];
    $date = $_GET['to_do_date'];
    $offline_values[] = $date;
	$offline_table[] = 'tickets';
	$offline_tableid[] = $ticketid;
	$offline_table_field[] = 'ticketid';

	$created_date = date('Y-m-d');
    $status = get_tickets($dbc, $ticketid, 'status');
    if($status == 'Internal QA') {
	    $query_update_project = "UPDATE `tickets` SET  internal_qa_date='$date' WHERE `ticketid` = '$ticketid'";
		$offline_fields[] = 'internal_qa_date';
    } else if($status == 'Customer QA') {
	    $query_update_project = "UPDATE `tickets` SET  deliverable_date='$date' WHERE `ticketid` = '$ticketid'";
		$offline_fields[] = 'deliverable_date';
    } else {
	    $query_update_project = "UPDATE `tickets` SET  to_do_date='$date', to_do_end_date='$date' WHERE `ticketid` = '$ticketid'";
		$offline_fields[] = 'to_do_date';
		$offline_values[] = $_GET['to_do_date'];
		$offline_fields[] = 'to_do_end_date';
		$offline_table[] = 'tickets';
		$offline_tableid[] = $ticketid;
		$offline_table_field[] = 'ticketid';
    }
	if($online) {
		$result_update_project = mysqli_query($dbc, $query_update_project);
	}
}
if($_GET['fill'] == 'task_calendar') {
    $tasklistid = $_GET['tasklistid'];
    $task_tododate = $_GET['task_tododate'];

	$query_update_project = "UPDATE `tasklist` SET  task_tododate='$task_tododate' WHERE `tasklistid` = '$tasklistid'";
	$offline_table[] = 'tasklist';
	$offline_tableid[] = $tasklistid;
	$offline_table_field[] = 'tasklistid';
	$offline_fields[] = 'task_tododate';
	$offline_values[] = $task_tododate;
	if($online) {
		$result_update_project = mysqli_query($dbc, $query_update_project);
	}
}

if($_GET['fill'] == 'list_view') {
    $value = $_GET['value'];
    $contactid = $_GET['contactid'];

	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(contactid) AS contactid FROM user_settings WHERE contactid='$contactid'"));
    if($get_config['contactid'] > 0) {
		$query_update_employee = "UPDATE `user_settings` SET calendar_list_view = '$value' WHERE contactid='$contactid'";
		$result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
		$query_insert_config = "INSERT INTO `user_settings` (`contactid`, `calendar_list_view`) VALUES ('$contactid', '$value')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
}
if($_GET['fill'] == 'ticketsendnote') {
	$item_id = $_POST['id'];
	$user = $_SESSION['contactid'];
	$note = filter_var(htmlentities('<p>'.$_POST['note'].'</p>'),FILTER_SANITIZE_STRING);
	$query_insert_note = "INSERT INTO `ticket_comment` (`ticketid`, `comment`, `created_date`, `created_by`, `type`, `note_heading`) VALUES ('$item_id', '$note', CURDATE(), '$user', 'note', 'Quick Note')";
	$result = mysqli_query($dbc, $query_insert_note);
}
if($_GET['fill'] == 'ticketsendemail') {
	$item_id = $_POST['id'];
	$sender = [get_email($dbc, $_SESSION['contactid'])=>get_contact($dbc, $_SESSION['contactid'])];
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
if($_GET['fill'] == 'ticketsendreminder') {
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
if($_GET['fill'] == 'ticketsendalert') {
	$ticketid = $_POST['id'];
	$user = $_POST['user'];
	$link = WEBSITE_URL."/Ticket/index.php?edit=".$ticketid;
	$text = TICKET_NOUN;
	$date = date('Y/m/d');
	$sql = mysqli_query($dbc, "INSERT INTO `alerts` (`alert_date`, `alert_link`, `alert_text`, `alert_user`) VALUES ('$date', '$link', '$text', '$user')");
}
if($_GET['fill'] == 'ticketsendupload') {
	$ticketid = $_GET['id'];
	$user = $_SESSION['contactid'];
	$filename = htmlspecialchars($_FILES['file']['name'], ENT_QUOTES);
	$file = $_FILES['file']['tmp_name'];
    if (!file_exists('../Ticket/download')) {
        mkdir('..Ticket/download', 0777, true);
    }
	move_uploaded_file($file, '../Ticket/download/'.$filename);
	$query_insert = "INSERT INTO `ticket_document` (`ticketid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$ticketid', 'Support Document', 'download/$filename', CURDATE(), '$user')";
	$result_insert = mysqli_query($dbc, $query_insert);echo $query_insert;
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
if($_GET['fill'] == 'ticketquickarchive') {
	$ticketid = $_GET['id'];
	$query_archive = "UPDATE `tickets` SET status = 'Archive', `status_date`=CURDATE() WHERE ticketid='$ticketid'";
	$result = mysqli_query($dbc, $query_archive);
}
if($_GET['fill'] == 'ticketquicktime') {
	$ticketid = $_POST['id'];
	$time = strtotime($_POST['time']);
	$current_time = strtotime(mysqli_fetch_array(mysqli_query($dbc, "SELECT `spent_time` FROM `tickets` WHERE `ticketid`='$ticketid'"))['spent_time']);
	$total_time = date('H:i:s', $time + $current_time - strtotime('00:00:00'));
	$query_time = "UPDATE `tickets` SET `spent_time` = '$total_time' WHERE ticketid='$ticketid'";
	$result = mysqli_query($dbc, $query_time);
	insert_day_overview($dbc, $_SESSION['contactid'], 'Ticket', date('Y-m-d'), '', "Updated ".TICKET_NOUN." #$ticketid - Manually Added Time : ".$_POST['time']);
}
if($_GET['fill'] == 'selected_staff') {
	$staff_list = implode(',',$_POST['staff']);
	$team_list = implode(',',$_POST['teams']);
	$region_list = implode(',',$_POST['regions']);
	$client_list = implode(',',$_POST['clients']);
	$tickettype_list = implode(',',$_POST['tickettype']);
	mysqli_query($dbc, "INSERT INTO `user_settings` (`contactid`) SELECT '' FROM (SELECT COUNT(*) rows FROM `user_settings` WHERE `contactid`='".$_SESSION['contactid']."') num WHERE num.rows = 0");
	mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_staff`='$staff_list' WHERE `contactid`='".$_SESSION['contactid']."'");
	mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_teams`='$team_list' WHERE `contactid`='".$_SESSION['contactid']."'");
	mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_regions`='$region_list' WHERE `contactid`='".$_SESSION['contactid']."'");
	mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_clients`='$client_list' WHERE `contactid`='".$_SESSION['contactid']."'");
	mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_tickettypes`='$tickettype_list' WHERE `contactid`='".$_SESSION['contactid']."'");
	if(isset($_POST['equipment'])) {
		$equipment_list = implode(',',$_POST['equipment']);
		mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_equipment`='$equipment_list' WHERE `contactid`='".$_SESSION['contactid']."'");
	}
}
if($_GET['fill'] == 'selected_contacts') {
	$contact_list = implode(',',$_POST['contacts']);
	$team_list = implode(',',$_POST['teams']);
	$staff_list = implode(',',$_POST['staff']);
	$client_list = implode(',',$_POST['clients']);
	$region_list = implode(',',$_POST['regions']);
	$location_list = implode(',',$_POST['locations']);
	$classification_list = implode(',',$_POST['classifications']);
	$tickettype_list = implode(',',$_POST['tickettype']);
	if($_POST['category'] == 'client') {
		mysqli_query($dbc, "INSERT INTO `user_settings` (`contactid`) SELECT '".$_SESSION['contactid']."' FROM (SELECT COUNT(*) rows FROM `user_settings` WHERE `contactid`='".$_SESSION['contactid']."') num WHERE num.rows = 0");
		mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_contacts`='$contact_list' WHERE `contactid`='".$_SESSION['contactid']."'");
	} else if($_POST['category'] == 'equipment') {
		mysqli_query($dbc, "INSERT INTO `user_settings` (`contactid`) SELECT '".$_SESSION['contactid']."' FROM (SELECT COUNT(*) rows FROM `user_settings` WHERE `contactid`='".$_SESSION['contactid']."') num WHERE num.rows = 0");
		if(isset($_POST['contacts'])) {
			mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_equipment`='$contact_list' WHERE `contactid`='".$_SESSION['contactid']."'");
		}
		if(isset($_POST['staff'])) {
			mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_staff`='$staff_list' WHERE `contactid`='".$_SESSION['contactid']."'");
		}
		mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_clients`='$client_list' WHERE `contactid`='".$_SESSION['contactid']."'");
		mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_regions`='$region_list' WHERE `contactid`='".$_SESSION['contactid']."'");
		mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_locations`='$location_list' WHERE `contactid`='".$_SESSION['contactid']."'");
		mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_classifications`='$classification_list' WHERE `contactid`='".$_SESSION['contactid']."'");
	} else {
		mysqli_query($dbc, "INSERT INTO `user_settings` (`contactid`) SELECT '".$_SESSION['contactid']."' FROM (SELECT COUNT(*) rows FROM `user_settings` WHERE `contactid`='".$_SESSION['contactid']."') num WHERE num.rows = 0");
		mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_staff`='$contact_list' WHERE `contactid`='".$_SESSION['contactid']."'");
		if(isset($_POST['equipment'])) {
			$equipment_list = implode(',',$_POST['equipment']);
			mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_equipment`='$equipment_list' WHERE `contactid`='".$_SESSION['contactid']."'");
		}
	}
	mysqli_query($dbc, "INSERT INTO `user_settings` (`contactid`) SELECT '".$_SESSION['contactid']."' FROM (SELECT COUNT(*) rows FROM `user_settings` WHERE `contactid`='".$_SESSION['contactid']."') num WHERE num.rows = 0");
	mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_teams`='$team_list' WHERE `contactid`='".$_SESSION['contactid']."'");
	mysqli_query($dbc, "UPDATE `user_settings` SET `appt_calendar_tickettypes`='$tickettype_list' WHERE `contactid`='".$_SESSION['contactid']."'");
}
if($_GET['fill'] == 'selected_projects') {
	$project_list = implode(',',$_POST['projects']);
	mysqli_query($dbc, "INSERT INTO `user_settings` (`contactid`) SELECT '' FROM (SELECT COUNT(*) rows FROM `user_settings` WHERE `contactid`='".$_SESSION['contactid']."') num WHERE num.rows = 0");
	mysqli_query($dbc, "UPDATE `user_settings` SET `events_calendar_projects`='$project_list' WHERE `contactid`='".$_SESSION['contactid']."'");
}
if($_GET['fill'] == 'move_appt') {
	$calendar_type = $_POST['calendar_type'];
	$td_blocktype = $_POST['td_blocktype'];
	$start_time = date('Y-m-d H:i:s', strtotime($_POST['time_slot']));
	$duration = $_POST['duration'];
	$end_time = date('Y-m-d H:i:s', strtotime($start_time) + $duration);
	$contact = $_POST['contact'];
	$old_contact = $_POST['old_contact'];
	if($_POST['item'] == 'workorder') {
		$workorderid = $_POST['workorder'];
		$max_time = gmdate('H:i:s', $duration);
		$to_do_time = date('h:i a', strtotime($start_time));
		$sql = "UPDATE `workorder` SET `to_do_time` = '$to_do_time', `max_time` = '$max_time' WHERE `workorderid` = '$workorderid'";
		$offline_table[] = 'workorder';
		$offline_tableid[] = $workorderid;
		$offline_table_field[] = 'workorderid';
		$offline_fields[] = 'to_do_time';
		$offline_values[] = $to_do_time;
		$offline_table[] = 'workorder';
		$offline_tableid[] = $workorderid;
		$offline_table_field[] = 'workorderid';
		$offline_fields[] = 'max_time';
		$offline_values[] = $max_time;
		if($online) {
			mysqli_query($dbc, $sql);
		}
	} else if($_POST['item'] == 'ticket') {
		$ticketid = $_POST['ticket'];
		if($online) {
			mysqli_query($dbc, "UPDATE `tickets` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
			mysqli_query($dbc, "UPDATE `ticket_attached` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
			mysqli_query($dbc, "UPDATE `ticket_schedule` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
			mysqli_query($dbc, "UPDATE `ticket_comment` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
		}
		$status = $_POST['ticket_status'];
		$start_time = date('h:i a', strtotime($_POST['time_slot']));
		$end_time = date('h:i a', strtotime($start_time) + $duration);
		$start_date = date('Y-m-d', strtotime($_POST['time_slot']));
		if ($calendar_type == 'event') {
			$projectid = $_POST['contact'];
			$offline_table[] = 'tickets';
			$offline_tableid[] = $ticketid;
			$offline_table_field[] = 'ticketid';
			$offline_fields[] = 'member_start_time';
			$offline_values[] = $start_time;
			$offline_table[] = 'tickets';
			$offline_tableid[] = $ticketid;
			$offline_table_field[] = 'ticketid';
			$offline_fields[] = 'member_end_time';
			$offline_values[] = $end_time;
			$offline_table[] = 'tickets';
			$offline_tableid[] = $ticketid;
			$offline_table_field[] = 'ticketid';
			$offline_fields[] = 'to_do_date';
			$offline_values[] = $start_date;
			$offline_table[] = 'tickets';
			$offline_tableid[] = $ticketid;
			$offline_table_field[] = 'ticketid';
			$offline_fields[] = 'projectid';
			$offline_values[] = $projectid;
			if($online) {
				mysqli_query($dbc, "UPDATE `tickets` SET `member_start_time` = '$start_time', `member_end_time` = '$end_time', `to_do_date` = '$start_date', `projectid` = '$projectid' WHERE `ticketid` = '$ticketid'");
			}
		} else if ($calendar_type == 'schedule') {
			$updated_fields = [];
			if($_POST['blocktype'] == 'dispatch_staff') {
				$contact_id = $_POST['contact'];
				$teams = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `teamid` SEPARATOR ',') as teams_list FROM `teams_staff` WHERE `contactid` = '$contact_id' AND `deleted` = 0"));
				if(!empty($teams['teams_list'])) {
					$teams_query = 'OR `teamid` IN ('.$teams['teams_list'].')';
				} else {
					$teams_query = '';
				}
				$equip_assign = mysqli_fetch_array(mysqli_query($dbc, "SELECT ea.* FROM `equipment_assignment` ea LEFT JOIN `equipment_assignment_staff` eas ON ea.`equipment_assignmentid` = eas.`equipment_assignmentid` WHERE ea.`deleted` = 0 AND DATE(`start_date`) <= '$start_date' AND DATE(ea.`end_date`) >= '$start_date' AND CONCAT(',',ea.`hide_days`,',') NOT LIKE '%,$start_date,%' AND ((eas.`contactid` = '$contact_id' AND eas.`deleted` = 0) $teams_query) ORDER BY ea.`start_date` DESC, ea.`end_date` ASC"));
				if(!empty($equip_assign)) {
					$_POST['equipassign'] = $equip_assign['equipment_assignmentid'];
					$equipmentid = $equip_assign['equipmentid'];
				} else {
					$dispatch_staff_query = ", `contactid` = ',$contact_id,'";
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'contactid';
					$offline_values[] = ','.$contact_id.',';
					$updated_fields['contactid'] = ",$contact_id,";
				}
			} else {
				$equipmentid = $_POST['contact'];
			}
			if(!empty($_POST['equipassign'])) {
				$equipment_assignmentid = $_POST['equipassign'];
				$equipassign = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'"));
				$teamid = $equipassign['teamid'];
				$region = explode('*#*',$equipassign['region'])[0];
				$location = explode('*#*',$equipassign['location'])[0];
				$classification = explode('*#*',$equipassign['classification'])[0];
				$equipassign_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '$equipment_assignmentid' AND `deleted` = 0"),MYSQLI_ASSOC);
			    $equipassign_hide_staff = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'"))['hide_staff']);
				$contact = [];
				foreach ($equipassign_staff as $staffid) {
					if(!in_array($staffid['contactid'], $contact) && !in_array($staffid['contactid'], $equipassign_hide_staff)) {
						$contact[] = $staffid['contactid'];
					}
				}
				$team_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC);
				foreach ($team_staff as $staffid) {
					if(!in_array($staffid['contactid'], $contact) && !in_array($staffid['contactid'], $equipassign_hide_staff)) {
						$contact[] = $staffid['contactid'];
					}
				}
				$contact = implode(',',$contact);
				$equipassign_query = ", `contactid` = ',$contact,', `teamid` = '$teamid', `region` = '$region', `con_location` = '$location', `classification` = '$classification'";
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'contactid';
				$offline_values[] = $contact;
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'teamid';
				$offline_values[] = $teamid;
				$updated_fields['contactid'] = ",$contact,";
			} else {
				$equipassign_query = "";
				if(!empty($equipmentid)) {
					$equipment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '$equipmentid'"));
					$region = explode('*#*',$equipment['region'])[0];
					$location = explode('*#*',$equipment['location'])[0];
					$classification = explode('*#*',$equipment['classification'])[0];
					if(!empty($region)) {
						$equipassign_query .= ", `region` = '$region'";
					}
					if(!empty($location)) {
						$equipassign_query .= ", `con_location` = '$location'";
					}
					if(!empty($classification)) {
						$equipassign_query .= ", `classification` = '$classification'";
					}
				}
			}
			$updated_fields['equipmentid'] = $equipmentid;
			$updated_fields['equipment_assignmentid'] = $equipment_assignmentid;
			$updated_fields['teamid'] = empty($teamid) ? 0 : $teamid;
			$updated_fields['region'] = $region;
			$updated_fields['con_location'] = $location;
			$updated_fields['classification'] = $classification;
			
			$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
			if ($_POST['move_type'] == 'resize') {
				$sql = "UPDATE `tickets` SET `equipmentid` = '$equipmentid', `equipment_assignmentid` = '$equipment_assignmentid', `to_do_end_time` = '$end_time' $equipassign_query $dispatch_staff_query WHERE `ticketid` = '$ticketid' AND `to_do_end_date` = '".date('Y-m-d', strtotime($_POST['time_slot']))."'";
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'equipmentid';
				$offline_values[] = $equipmentid;
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'equipment_assignmentid';
				$offline_values[] = $equipment_assignmentid;
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'to_do_end_time';
				$offline_values[] = $end_time;
				$updated_fields['to_do_end_time'] = $end_time;
				if($online) {
					mysqli_query($dbc, $sql);
				}
			}
			if ($_POST['move_type'] == 'move') {
				$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT *, IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) `to_do_end_date` FROM `tickets` WHERE `ticketid` = '$ticketid'"));
				$date_diff = strtotime($start_date) - strtotime($ticket['to_do_date']);
				$end_date = strtotime($ticket['to_do_end_date']) + $date_diff;
				$end_date = date('Y-m-d', $end_date);

				$sql = "UPDATE `tickets` SET `equipmentid` = '$equipmentid', `equipment_assignmentid` = '$equipment_assignmentid', `to_do_start_time` = '$start_time', `to_do_date` = '$start_date', `to_do_end_date` = '$end_date' $equipassign_query $dispatch_staff_query WHERE `ticketid` = '$ticketid'";
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'equipmentid';
				$offline_values[] = $equipmentid;
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'equipment_assignmentid';
				$offline_values[] = $equipment_assignmentid;
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'to_do_start_time';
				$offline_values[] = $start_time;
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'to_do_date';
				$offline_values[] = $start_date;
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'to_do_end_date';
				$offline_values[] = $end_date;
				$updated_fields['to_do_start_time'] = $start_time;
				$updated_fields['to_do_date'] = $start_date;
				$updated_fields['to_do_end_date'] = $end_date;
				if($online) {
					mysqli_query($dbc, $sql);
				}
			}
			$sql = "UPDATE `tickets` SET `equipmentid` = '$equipmentid', `equipment_assignmentid` = '$equipment_assignmentid', `to_do_start_time` = '$start_time', `to_do_end_time` = '$end_time' $equipassign_query $dispatch_staff_query WHERE `ticketid` = '$ticketid' AND `to_do_date` = '".date('Y-m-d', strtotime($_POST['time_slot']))."' AND `to_do_end_date` = '".date('Y-m-d', strtotime($_POST['time_slot']))."'";
			$offline_table[] = 'tickets';
			$offline_tableid[] = $ticketid;
			$offline_table_field[] = 'ticketid';
			$offline_fields[] = 'equipmentid';
			$offline_values[] = $equipmentid;
			$offline_table[] = 'tickets';
			$offline_tableid[] = $ticketid;
			$offline_table_field[] = 'ticketid';
			$offline_fields[] = 'equipment_assignmentid';
			$offline_values[] = $equipment_assignmentid;
			$offline_table[] = 'tickets';
			$offline_tableid[] = $ticketid;
			$offline_table_field[] = 'ticketid';
			$offline_fields[] = 'to_do_start_time';
			$offline_values[] = $start_time;
			$offline_table[] = 'tickets';
			$offline_tableid[] = $ticketid;
			$offline_table_field[] = 'ticketid';
			$offline_fields[] = 'to_do_end_time';
			$offline_values[] = $end_time;
			if($online) {
				mysqli_query($dbc, $sql);

				//Record history
				$ticket_histories = [];
				foreach($updated_fields as $key => $updated_field) {
					if($ticket[$key] != $updated_field) {
						$ticket_histories[$key] = "$key updated to $updated_field";
					}
				}
				if($ticket['equipment_assignmentid'] != $equipment_assignmentid) {
					$ea_contacts = [];
					foreach(explode(',', $contact) as $ea_contact) {
						if($ea_contact > 0) {
							$ea_contacts[] = get_contact($dbc, $ea_contact);
						}
					}
					$ticket_histories['equipment_assignmentid'] = "equipment_assignmentid updated to $equipment_assignmentid (".implode(', ',$ea_contacts).")";
				}
				if(!empty($ticket_histories)) {
					mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `src`, `description`) VALUES ('{$ticket['ticketid']}','{$_SESSION['contactid']}','calendar','Row #$ticketid of tickets updated: ".implode(', ',$ticket_histories)."')");
				}
			}
		} else {
			$contact_query = '';
			$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
			if($_POST['add_staff'] == 1) {
				$add_staff = $_POST['add_staff'];
			} else if($old_contact != $contact) {
				if($td_blocktype == 'team') {
					$teamid = $contact;
					$contacts = [];
					$team_contacts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC);
					foreach($team_contacts as $team_contact) {
						if(strtolower(get_contact($dbc, $team_contact['contactid'], 'category')) == 'staff') {
							$contacts[] = $team_contact['contactid'];
						}
					}
				} else {
					$contacts = [$contact];
				}

				if($add_staff == 1) {
					$contacts = array_filter(array_unique(array_merge(explode(',',$ticket['contactid']))));
				}
		        $date_of_archival = date('Y-m-d');
            	mysqli_query($dbc, "UPDATE `ticket_attached` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `ticketid` = '$ticketid' AND `src_table` = 'Staff' AND `item_id` NOT IN ('".implode("','",$contacts)."')");
			}
			if ($status == 'Internal QA') {
				if($old_contact != $contact && $add_staff == 1) {
					$internal_qa_contactid = array_filter(explode(',',$ticket['internal_qa_contactid']));
					if(!in_array($contact, $internal_qa_contactid)) {
						$internal_qa_contactid[] = $contact;
					}
					$internal_qa_contactid = ','.implode(',',$internal_qa_contactid).',';
					$contact_query = "`internal_qa_contactid` = '$internal_qa_contactid',";
				} else if($old_contact != $contact) {
					$contact_query = "`internal_qa_contactid` = ',$contact,',";
				}
				if($_POST['mode'] == 'client') {
					if($old_contact != $contact) {
						$contact_query = "`clientid` = ',$contact,',";
					} else {
						$contact_query = '';
					}
				}
				$sql = "UPDATE `tickets` SET $contact_query `internal_qa_start_time` = '$start_time', `internal_qa_end_time` = '$end_time', `internal_qa_date` = '$start_date' WHERE `ticketid` = '$ticketid'";
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'internal_qa_contactid';
				$offline_values[] = ','.$contact.',';
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'internal_qa_start_time';
				$offline_values[] = $start_time;
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'internal_qa_end_time';
				$offline_values[] = $end_time;
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'internal_qa_date';
				$offline_values[] = $start_date;
			} else if ($status == 'Customer QA') {
				if($old_contact != $contact && $add_staff == 1) {
					$deliverable_contactid = array_filter(explode(',',$ticket['deliverable_contactid']));
					if(!in_array($contact, $deliverable_contactid)) {
						$deliverable_contactid[] = $contact;
					}
					$deliverable_contactid = ','.implode(',',$deliverable_contactid).',';
					$contact_query = "`deliverable_contactid` = '$deliverable_contactid',";
				} else if($old_contact != $contact) {
					$contact_query = "`deliverable_contactid` = ',$contact,',";
				}
				if($_POST['mode'] == 'client') {
					if($old_contact != $contact) {
						$contact_query = "`clientid` = ',$contact,',";
					} else {
						$contact_query = '';
					}
				}
				$sql = "UPDATE `tickets` SET $contact_query `deliverable_start_time` = '$start_time', `deliverable_end_time` = '$end_time', `deliverable_date` = '$start_date' WHERE `ticketid` = '$ticketid'";
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'deliverable_contactid';
				$offline_values[] = ','.$contact.',';
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'deliverable_start_time';
				$offline_values[] = $start_time;
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'deliverable_end_time';
				$offline_values[] = $end_time;
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'deliverable_date';
				$offline_values[] = $start_date;
			} else {
				if($td_blocktype == 'team') {
					$teamid = $contact;
					$contacts = [];
					$team_contacts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC);
					foreach($team_contacts as $team_contact) {
						if(strtolower(get_contact($dbc, $team_contact['contactid'], 'category')) == 'staff') {
							$contacts[] = $team_contact['contactid'];
						}
					}
				} else {
					$contacts = [$contact];
				}

				if($add_staff == 1) {
					$contacts = array_filter(array_unique(array_merge($contacts,explode(',',$ticket['contactid']))));
				} else if($old_contact == $contact) {
					$contacts = array_filter(array_unique(explode(',',$ticket['contactid'])));
				}
				$contact_query = "`contactid` = ',".implode(',',$contacts).",', ";
				if($_POST['mode'] == 'client') {
					$contacts = '';
					if($old_contact != $contact) {
						$contact_query = "`clientid` = ',$contact,',";
					} else {
						$contact_query = '';
					}
				}
				foreach($contacts as $contact) {
					if(strtolower(get_contact($dbc, $contact, 'category')) == 'staff') {
						if($td_blocktype == 'team') {
							$position = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `contactid` = '$contact' AND `deleted` = 0"))['contact_position'];
						} else {
							$position = '';
						}
						mysqli_query($dbc, "INSERT INTO `ticket_attached` (`ticketid`, `src_table`, `item_id`, `position`) SELECT '$ticketid', 'Staff', '$contact', '$position' FROM (SELECT COUNT(*) rows FROM `ticket_attached` WHERE `ticketid` = '$ticketid' AND `src_table` = 'Staff' ANd `item_id` = '$contact' AND `deleted` = 0) num WHERE num.rows=0");
					}
				}
				if ($_POST['move_type'] == 'resize') {
					$sql = "UPDATE `tickets` SET $contact_query `to_do_end_time` = '$end_time' WHERE `ticketid` = '$ticketid' AND `to_do_end_date` = '".date('Y-m-d', strtotime($_POST['time_slot']))."'";
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'contactid';
					if($td_blocktype == 'team') {
						$offline_values[] = ','.implode(',',$contacts).',';
					} else {
						$offline_values[] = ','.$contact.',';
					}
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'to_do_end_time';
					$offline_values[] = $end_time;
					if($online) {
						mysqli_query($dbc, $sql);
					}
				}
				if ($_POST['move_type'] == 'move') {
					$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT *, IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) `to_do_end_date` FROM `tickets` WHERE `ticketid` = '$ticketid'"));
					$date_diff = strtotime($start_date) - strtotime($ticket['to_do_date']);
					$end_date = strtotime($ticket['to_do_end_date']) + $date_diff;
					$end_date = date('Y-m-d', $end_date);

					$sql = "UPDATE `tickets` SET $contact_query `to_do_start_time` = '$start_time', `to_do_date` = '$start_date', `to_do_end_date` = '$end_date' WHERE `ticketid` = '$ticketid'";
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'contactid';
					if($td_blocktype == 'team') {
						$offline_values[] = ','.implode(',',$contacts).',';
					} else {
						$offline_values[] = ','.$contact.',';
					}
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'to_do_start_time';
					$offline_values[] = $start_time;
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'to_do_date';
					$offline_values[] = $start_date;
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'to_do_end_date';
					$offline_values[] = $end_date;
					if($online) {
						mysqli_query($dbc, $sql);
					}
				}
				$sql = "UPDATE `tickets` SET $contact_query `to_do_start_time` = '$start_time', `to_do_end_time` = '$end_time' WHERE `ticketid` = '$ticketid' AND `to_do_date` = '".date('Y-m-d', strtotime($_POST['time_slot']))."' AND `to_do_end_date` = '".date('Y-m-d', strtotime($_POST['time_slot']))."'";
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'contactid';
					if($td_blocktype == 'team') {
						$offline_values[] = ','.implode(',',$contacts).',';
					} else {
						$offline_values[] = ','.$contact.',';
					}
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'to_do_start_time';
				$offline_values[] = $start_time;
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'to_do_end_time';
				$offline_values[] = $end_time;
			}
			if($online) {
				mysqli_query($dbc, $sql);
			}
			$all_contacts_old = $ticket['contactid'].','.$ticket['deliverable_contactid'].','.$ticket['internal_qa_contactid'];
			$all_contacts_old = array_filter(explode(',', $all_contacts_old));
			$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
			$all_contacts = $ticket['contactid'].','.$ticket['deliverable_contactid'].','.$ticket['internal_qa_contactid'];
			$all_contacts = array_merge(array_filter(explode(',', $all_contacts)),$all_contacts_old);
			if($_POST['mode'] == 'client') {
				echo json_encode([$old_contact,$contact]);
			} else {
				echo json_encode($all_contacts);
			}
		}
	} else if($_POST['item'] == 'ticket_schedule') {
		$id = $_POST['id'];
		$schedule_ticketid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `ticketid` FROM `ticket_schedule` WHERE `id` = '$id'"))['ticketid'];
		if($online) {
			mysqli_query($dbc, "UPDATE `tickets` SET `is_recurrence` = 0 WHERE `ticketid` = '$schedule_ticketid'");
			mysqli_query($dbc, "UPDATE `ticket_attached` SET `is_recurrence` = 0 WHERE `ticketid` = '$schedule_ticketid'");
			mysqli_query($dbc, "UPDATE `ticket_schedule` SET `is_recurrence` = 0 WHERE `ticketid` = '$schedule_ticketid'");
			mysqli_query($dbc, "UPDATE `ticket_comment` SET `is_recurrence` = 0 WHERE `ticketid` = '$schedule_ticketid'");
		}
		$start_time = date('H:i:s', strtotime($_POST['time_slot']));
		$end_time = date('H:i:s', strtotime($start_time) + $duration);
		$start_date = date('Y-m-d', strtotime($_POST['time_slot']));
		$end_date = date('Y-m-d', strtotime($_POST['time_slot']));
		if ($calendar_type == 'schedule') {
			$updated_fields = [];
			if($_POST['blocktype'] == 'dispatch_staff') {
				$contact_id = $_POST['contact'];
				$teams = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `teamid` SEPARATOR ',') as teams_list FROM `teams_staff` WHERE `contactid` = '$contact_id' AND `deleted` = 0"));
				if(!empty($teams['teams_list'])) {
					$teams_query = 'OR `teamid` IN ('.$teams['teams_list'].')';
				} else {
					$teams_query = '';
				}
				$equip_assign = mysqli_fetch_array(mysqli_query($dbc, "SELECT ea.* FROM `equipment_assignment` ea LEFT JOIN `equipment_assignment_staff` eas ON ea.`equipment_assignmentid` = eas.`equipment_assignmentid` WHERE ea.`deleted` = 0 AND DATE(`start_date`) <= '$start_date' AND DATE(ea.`end_date`) >= '$start_date' AND CONCAT(',',ea.`hide_days`,',') NOT LIKE '%,$start_date,%' AND ((eas.`contactid` = '$contact_id' AND eas.`deleted` = 0) $teams_query) ORDER BY ea.`start_date` DESC, ea.`end_date` ASC"));
				if(!empty($equip_assign)) {
					$_POST['equipassign'] = $equip_assign['equipment_assignmentid'];
					$equipmentid = $equip_assign['equipmentid'];
				} else {
					$dispatch_staff_query = ", `contactid` = ',$contact_id,'";
					$offline_table[] = 'ticket_schedule';
					$offline_tableid[] = $id;
					$offline_table_field[] = 'id';
					$offline_fields[] = 'contactid';
					$offline_values[] = ','.$contact_id.',';
					$updated_fields['contactid'] = ",$contact_id,";
				}
			} else {
				$equipmentid = $_POST['contact'];
			}
			if(!empty($_POST['equipassign'])) {
				$equipment_assignmentid = $_POST['equipassign'];
				$equipassign = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'"));
				$teamid = $equipassign['teamid'];
				$region = explode('*#*',$equipassign['region'])[0];
				$location = explode('*#*',$equipassign['location'])[0];
				$classification = explode('*#*',$equipassign['classification'])[0];
				$equipassign_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '$equipment_assignmentid' AND `deleted` = 0"),MYSQLI_ASSOC);
			    $equipassign_hide_staff = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'"))['hide_staff']);
				$contact = [];
				foreach ($equipassign_staff as $staffid) {
					if(!in_array($staffid['contactid'], $contact) && !in_array($staffid['contactid'], $equipassign_hide_staff)) {
						$contact[] = $staffid['contactid'];
					}
				}
				$team_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC);
				foreach ($team_staff as $staffid) {
					if(!in_array($staffid['contactid'], $contact) && !in_array($staffid['contactid'], $equipassign_hide_staff)) {
						$contact[] = $staffid['contactid'];
					}
				}
				$contact = implode(',',$contact);
				$equipassign_query = ", `contactid` = ',$contact,', `teamid` = '$teamid', `region` = '$region', `con_location` = '$location', `classification` = '$classification'";
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'contactid';
				$offline_values[] = $contact;
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'teamid';
				$offline_values[] = $teamid;
				$updated_fields['contactid'] = ",$contact,";
			} else {
				$equipassign_query = "";
				if(!empty($equipmentid)) {
					$equipment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '$equipmentid'"));
					$region = explode('*#*',$equipment['region'])[0];
					$location = explode('*#*',$equipment['location'])[0];
					$classification = explode('*#*',$equipment['classification'])[0];
					if(!empty($region)) {
						$equipassign_query .= ", `region` = '$region'";
					}
					if(!empty($location)) {
						$equipassign_query .= ", `con_location` = '$location'";
					}
					if(!empty($classification)) {
						$equipassign_query .= ", `classification` = '$classification'";
					}
				}
			}
			$updated_fields['equipmentid'] = $equipmentid;
			$updated_fields['equipment_assignmentid'] = $equipment_assignmentid;
			$updated_fields['teamid'] = empty($teamid) ? 0 : $teamid;
			$updated_fields['region'] = $region;
			$updated_fields['con_location'] = $location;
			$updated_fields['classification'] = $classification;

			$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `id` = '$id'"));
			if ($_POST['move_type'] == 'resize') {
				$sql = "UPDATE `ticket_schedule` SET `equipmentid` = '$equipmentid', `equipment_assignmentid` = '$equipment_assignmentid', `to_do_end_time` = '$end_time' $equipassign_query $dispatch_staff_query WHERE `id` = '$id' AND `to_do_date` = '".date('Y-m-d', strtotime($_POST['time_slot']))."'";
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'equipmentid';
				$offline_values[] = $equipmentid;
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'equipment_assignmentid';
				$offline_values[] = $equipment_assignmentid;
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'to_do_end_time';
				$offline_values[] = $end_time;
				$updated_fields['to_do_end_time'] = $end_time;
				if($online) {
					mysqli_query($dbc, $sql);
				}
			}
			if ($_POST['move_type'] == 'move') {
				$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `id` = '$id'"));
				$date_diff = strtotime($start_date) - strtotime($ticket['to_do_date']);
				// $end_date = strtotime($ticket['to_do_end_date']) + $date_diff;
				// $end_date = date('Y-m-d', $end_date);

				$sql = "UPDATE `ticket_schedule` SET `equipmentid` = '$equipmentid', `equipment_assignmentid` = '$equipment_assignmentid', `to_do_start_time` = IF(`scheduled_lock` > 0,`to_do_start_time`,'$start_time'), `to_do_date` = '$start_date', `to_do_end_date` = '$end_date' $equipassign_query $dispatch_staff_query WHERE `id` = '$id'";
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'equipmentid';
				$offline_values[] = $equipmentid;
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'equipment_assignmentid';
				$offline_values[] = $equipment_assignmentid;
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'to_do_start_time';
				$offline_values[] = $start_time;
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'to_do_date';
				$offline_values[] = $start_date;
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'to_do_end_date';
				$offline_values[] = $end_date;
				$updated_fields['to_do_start_time'] = $start_time;
				$updated_fields['to_do_date'] = $start_date;
				$updated_fields['to_do_end_date'] = $end_date;
				if($online) {
					mysqli_query($dbc, $sql);
				}
			}
			$sql = "UPDATE `ticket_schedule` SET `equipmentid` = '$equipmentid', `equipment_assignmentid` = '$equipment_assignmentid', `to_do_start_time` = IF(`scheduled_lock` > 0,`to_do_start_time`,'$start_time'), `to_do_end_time` = IF(`scheduled_lock` > 0,`to_do_end_time`,'$end_time') $equipassign_query $dispatch_staff_query WHERE `id` = '$id' AND `to_do_date` = '".date('Y-m-d', strtotime($_POST['time_slot']))."' AND `to_do_date` = '".date('Y-m-d', strtotime($_POST['time_slot']))."'";
			$offline_table[] = 'ticket_schedule';
			$offline_tableid[] = $id;
			$offline_table_field[] = 'id';
			$offline_fields[] = 'equipmentid';
			$offline_values[] = $equipmentid;
			$offline_table[] = 'ticket_schedule';
			$offline_tableid[] = $id;
			$offline_table_field[] = 'id';
			$offline_fields[] = 'equipment_assignmentid';
			$offline_values[] = $equipment_assignmentid;
			$offline_table[] = 'ticket_schedule';
			$offline_tableid[] = $id;
			$offline_table_field[] = 'id';
			$offline_fields[] = 'to_do_start_time';
			$offline_values[] = $start_time;
			$offline_table[] = 'ticket_schedule';
			$offline_tableid[] = $id;
			$offline_table_field[] = 'id';
			$offline_fields[] = 'to_do_end_time';
			$offline_values[] = $end_time;
			if($online) {
				mysqli_query($dbc, $sql);

				//Record history
				$ticket_histories = [];
				foreach($updated_fields as $key => $updated_field) {
					if($ticket[$key] != $updated_field) {
						$ticket_histories[$key] = "$key updated to $updated_field";
					}
				}
				if($ticket['equipment_assignmentid'] != $equipment_assignmentid) {
					$ea_contacts = [];
					foreach(explode(',', $contact) as $ea_contact) {
						if($ea_contact > 0) {
							$ea_contacts[] = get_contact($dbc, $ea_contact);
						}
					}
					$ticket_histories['equipment_assignmentid'] = "equipment_assignmentid updated to $equipment_assignmentid (".implode(', ',$ea_contacts).")";
				}
				if(!empty($ticket_histories)) {
					mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `src`, `description`) VALUES ('{$ticket['ticketid']}','{$_SESSION['contactid']}','calendar','Row #$id of ticket_schedule updated: ".implode(', ',$ticket_histories)."')");
				}
			}
		} else {
			if ($status == 'Internal QA') {
				$sql = "UPDATE `ticket_schedule` SET `internal_qa_contactid` = ',$contact,', `internal_qa_start_time` = '$start_time', `internal_qa_end_time` = '$end_time', `internal_qa_date` = '$start_date' WHERE `id` = '$id'";
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'internal_qa_contactid';
				$offline_values[] = ','.$contact.',';
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'internal_qa_start_time';
				$offline_values[] = $start_time;
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'internal_qa_end_time';
				$offline_values[] = $end_time;
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'internal_qa_date';
				$offline_values[] = $start_date;
			} else if ($status == 'Customer QA') {
				$sql = "UPDATE `ticket_schedule` SET `deliverable_contactid` = ',$contact,', `deliverable_start_time` = '$start_time', `deliverable_end_time` = '$end_time', `deliverable_date` = '$start_date' WHERE `id` = '$id'";
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'deliverable_contactid';
				$offline_values[] = ','.$contact.',';
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'deliverable_start_time';
				$offline_values[] = $start_time;
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'deliverable_end_time';
				$offline_values[] = $end_time;
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'deliverable_date';
				$offline_values[] = $start_date;
			} else {
				if ($_POST['move_type'] == 'resize') {
					$sql = "UPDATE `ticket_schedule` SET `contactid` = ',$contact,', `to_do_end_time` = '$end_time' WHERE `id` = '$id' AND `to_do_end_date` = '".date('Y-m-d', strtotime($_POST['time_slot']))."'";
					$offline_table[] = 'ticket_schedule';
					$offline_tableid[] = $id;
					$offline_table_field[] = 'id';
					$offline_fields[] = 'contactid';
					$offline_values[] = ','.$contact.',';
					$offline_table[] = 'ticket_schedule';
					$offline_tableid[] = $id;
					$offline_table_field[] = 'id';
					$offline_fields[] = 'to_do_end_time';
					$offline_values[] = $end_time;
					if($online) {
						mysqli_query($dbc, $sql);
					}
				}
				if ($_POST['move_type'] == 'move') {
					$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `id` = '$id'"));
					$date_diff = strtotime($start_date) - strtotime($ticket['to_do_date']);
					// $end_date = strtotime($ticket['to_do_end_date']) + $date_diff;
					// $end_date = date('Y-m-d', $end_date);

					$sql = "UPDATE `ticket_schedule` SET `contactid` = ',$contact,', `to_do_start_time` = IF(`scheduled_lock` > 0,`to_do_start_time`,'$start_time'), `to_do_date` = '$start_date', `to_do_end_date` = '$end_date' WHERE `id` = '$id'";
					$offline_table[] = 'ticket_schedule';
					$offline_tableid[] = $id;
					$offline_table_field[] = 'id';
					$offline_fields[] = 'contactid';
					$offline_values[] = ','.$contact.',';
					$offline_table[] = 'ticket_schedule';
					$offline_tableid[] = $id;
					$offline_table_field[] = 'id';
					$offline_fields[] = 'to_do_start_time';
					$offline_values[] = $start_time;
					$offline_table[] = 'ticket_schedule';
					$offline_tableid[] = $id;
					$offline_table_field[] = 'id';
					$offline_fields[] = 'to_do_date';
					$offline_values[] = $start_date;
					$offline_table[] = 'ticket_schedule';
					$offline_tableid[] = $id;
					$offline_table_field[] = 'id';
					$offline_fields[] = 'to_do_end_date';
					$offline_values[] = $end_date;
					if($online) {
						mysqli_query($dbc, $sql);
					}
				}
				$sql = "UPDATE `ticket_schedule` SET `contactid` = ',$contact,', `to_do_start_time` = IF(`scheduled_lock` > 0,`to_do_start_time`,'$start_time'), `to_do_end_time` = '$end_time' WHERE `id` = '$id' AND `to_do_date` = '".date('Y-m-d', strtotime($_POST['time_slot']))."' AND `to_do_end_date` = '".date('Y-m-d', strtotime($_POST['time_slot']))."'";
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'contactid';
				$offline_values[] = ','.$contact.',';
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'to_do_start_time';
				$offline_values[] = $start_time;
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $id;
				$offline_table_field[] = 'id';
				$offline_fields[] = 'to_do_end_time';
				$offline_values[] = $end_time;
			}
			if($online) {
				mysqli_query($dbc, $sql);
			}
		}
	} else if($_POST['item'] == 'shift') {
		$shiftid = $_POST['shift'];
		$start_time = date('h:i a', strtotime($_POST['time_slot']));
		$end_time = date('h:i a', strtotime($start_time) + $duration);
		$start_date = date('Y-m-d', strtotime($_POST['time_slot']));
		$shift = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts_shifts` WHERE `shiftid` = '$shiftid'"));
		if ($_POST['move_type'] == 'resize') {
			$edit_type = $_POST['edit_type'];
			switch ($edit_type) {
				case 'once':
					$hide_days = $shift['hide_days'];
					$hide_days .= ','.$start_date;
					$hide_days = trim($hide_days, ',');
					$sql = "UPDATE `contacts_shifts` SET `hide_days` = '$hide_days' WHERE `shiftid` = '$shiftid'";
					$offline_table[] = 'contacts_shifts';
					$offline_tableid[] = $shiftid;
					$offline_table_field[] = 'shiftid';
					$offline_fields[] = 'hide_days';
					$offline_values[] = $hide_days;
					if($online) {
						mysqli_query($dbc, $sql);
					}

					$sql = "INSERT INTO `contacts_shifts` (`heading`, `contactid`, `security_level`, `clientid`, `startdate`, `enddate`, `starttime`, `endtime`, `dayoff_type`, `break_starttime`, `break_endtime`, `notes`)
						SELECT `heading`, `contactid`, `security_level`, `clientid`, '$start_date', '$start_date', `starttime`, '$end_time', `dayoff_type`, `break_starttime`, `break_endtime`, `notes` FROM `contacts_shifts` WHERE `shiftid` = '$shiftid'";
					if($online) {
						mysqli_query($dbc, $sql);
					}
					break;
				case 'following':
					$end_date = date('Y-m-d', strtotime($start_date.' - 1 day'));
					$sql = "UPDATE `contacts_shifts` SET `enddate` = '$end_date' WHERE `shiftid` = '$shiftid'";
					$offline_table[] = 'contacts_shifts';
					$offline_tableid[] = $shiftid;
					$offline_table_field[] = 'shiftid';
					$offline_fields[] = 'enddate';
					$offline_values[] = $end_date;
					if($online) {
						mysqli_query($dbc, $sql);
					}

					$end_date = $shift['enddate'];
					$sql = "INSERT INTO `contacts_shifts` (`heading`, `contactid`, `security_level` `clientid`, `startdate`, `enddate`, `starttime`, `endtime`, `dayoff_type`, `break_starttime`, `break_endtime`, `repeat_days`, `notes`, `repeat_type`, `repeat_interval`, `hide_days`) SELECT `heading`, `contactid`, `security_level`, `clientid`, '$start_date', '$end_date', `starttime`, '$end_time', `dayoff_type`, `break_starttime`, `break_endtime`, `repeat_days`, `notes`, `repeat_type`, `repeat_interval`, `hide_days` FROM `contacts_shifts` WHERE `shiftid` = '$shiftid'";
					if($online) {
						mysqli_query($dbc, $sql);
					}
					break;
				case 'all':
				default:
					$sql = "UPDATE `contacts_shifts` SET `endtime` = '$end_time' WHERE `shiftid` = '$shiftid'";
					$offline_table[] = 'contacts_shifts';
					$offline_tableid[] = $shiftid;
					$offline_table_field[] = 'shiftid';
					$offline_fields[] = 'endtime';
					$offline_values[] = $end_time;
					if($online) {
						mysqli_query($dbc, $sql);
					}
					break;
			}
		}
		if ($_POST['move_type'] == 'move') {
			$old_date = $_POST['old_date'];
			$recurring = $_POST['recurring'];
			if($recurring == 'yes') {
				$hide_days = $shift['hide_days'];
				$hide_days .= ','.$start_date;
				$hide_days = trim($hide_days, ',');
				$sql = "UPDATE `contacts_shifts` SET `hide_days` = '$hide_days' WHERE `shiftid` = '$shiftid'";
				$offline_table[] = 'contacts_shifts';
				$offline_tableid[] = $shiftid;
				$offline_table_field[] = 'shiftid';
				$offline_fields[] = 'hide_days';
				$offline_values[] = $hide_days;
				if($online) {
					mysqli_query($dbc, $sql);
				}

				$sql = "INSERT INTO `contacts_shifts` (`heading`, `contactid`, `security_level`, `clientid`, `startdate`, `enddate`, `starttime`, `endtime`, `dayoff_type`, `break_starttime`, `break_endtime`, `notes`) SELECT `heading`, ".($_POST['mode'] == 'client' ? '`contactid`' : "'$contact'").", `security_level`, ".($_POST['mode'] == 'client' ? "'$contact'" : "`clientid`").", '$start_date', '$start_date', '$start_time', '$end_time', `dayoff_type`, `break_starttime`, `break_endtime`, `notes` FROM `contacts_shifts` WHERE `shiftid` = '$shiftid'";
				if($online) {
					mysqli_query($dbc, $sql);
				}
			} else {
				$sql = "UPDATE `contacts_shifts` SET ".($_POST['mode'] == 'client' ? '`clientid`' : '`contactid`')." = '$contact', `startdate` = '$start_date', `enddate` = '$start_date', `starttime` = '$start_time', `endtime` = '$end_time' WHERE `shiftid` = '$shiftid'";
				$offline_table[] = 'contacts_shifts';
				$offline_tableid[] = $shiftid;
				$offline_table_field[] = 'shiftid';
				$offline_fields[] = ($_POST['mode'] == 'client' ? 'clientid' : 'contactid');
				$offline_values[] = $contact;
				$offline_table[] = 'contacts_shifts';
				$offline_tableid[] = $shiftid;
				$offline_table_field[] = 'shiftid';
				$offline_fields[] = 'startdate';
				$offline_values[] = $start_date;
				$offline_table[] = 'contacts_shifts';
				$offline_tableid[] = $shiftid;
				$offline_table_field[] = 'shiftid';
				$offline_fields[] = 'enddate';
				$offline_values[] = $start_date;
				$offline_table[] = 'contacts_shifts';
				$offline_tableid[] = $shiftid;
				$offline_table_field[] = 'shiftid';
				$offline_fields[] = 'starttime';
				$offline_values[] = $start_time;
				$offline_table[] = 'contacts_shifts';
				$offline_tableid[] = $shiftid;
				$offline_table_field[] = 'shiftid';
				$offline_fields[] = 'endtime';
				$offline_values[] = $end_time;
				if($online) {
					mysqli_query($dbc, $sql);
				}
			}
		}
	} else {
		$bookingid = $_POST['appointment'];
		$booking = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `booking` WHERE `bookingid` = '$bookingid'"));
		$start_date = date('Y-m-d', strtotime($_POST['time_slot']));
		$date_diff = strtotime($start_date) - strtotime(date('Y-m-d', strtotime($booking['appoint_date'])));
		$end_date = strtotime(date('Y-m-d', strtotime($booking['end_appoint_date']))) + $date_diff;
		if ($_POST['move_type'] == 'resize' && date('Y-m-d', strtotime($booking['end_appoint_date'])) == $start_date) {
			$sql = "UPDATE `booking` SET `".($_POST['mode'] == 'client' ? 'patientid' : 'therapistsid')."`='$contact', `end_appoint_date` = '$end_time' WHERE `bookingid` = '$bookingid'";
			$offline_table[] = 'booking';
			$offline_tableid[] = $bookingid;
			$offline_table_field[] = 'bookingid';
			$offline_fields[] = ($_POST['mode'] == 'client' ? 'patientid' : 'therapistsid');
			$offline_values[] = $contact;
			$offline_table[] = 'booking';
			$offline_tableid[] = $bookingid;
			$offline_table_field[] = 'bookingid';
			$offline_fields[] = 'end_appoint_date';
			$offline_values[] = $end_time;
			if($online) {
				mysqli_query($dbc, $sql);
			}
		}
		if ($_POST['move_type'] == 'move') {
			$end_time = date('Y-m-d', $end_date).' '.date('H:i:s', strtotime($booking['end_appoint_date']));

			$sql = "UPDATE `booking` SET `".($_POST['mode'] == 'client' ? 'patientid' : 'therapistsid')."`='$contact', `appoint_date` = '$start_time', `end_appoint_date` = '$end_time' WHERE `bookingid` = '$bookingid'";
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = ($_POST['mode'] == 'client' ? 'patientid' : 'therapistsid');
				$offline_values[] = $contact;
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'appoint_date';
				$offline_values[] = $start_time;
				$offline_table[] = 'tickets';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'end_appoint_date';
				$offline_values[] = $end_time;
		 	if($online) {
				mysqli_query($dbc, $sql);
			}
		}

		// $sql = "UPDATE `booking` SET `appoint_date`='$start_time', `end_appoint_date`='$end_time', `".($_POST['mode'] == 'client' ? 'patientid' : 'therapistsid')."`='$contact' WHERE `bookingid`='$bookingid'";
		// mysqli_query($dbc, $sql);
	}
}
if($_GET['fill'] == 'schedule_unbooked') {
	$td_blocktype = $_POST['td_blocktype'];
	$time = date('Y-m-d', strtotime($_POST['time_slot']));
	$contact = $_POST['contact'];
	$id = $_POST['id'];
	if($_POST['item'] == 'ticket') {
		if($_POST['blocktable'] == 'ticket_schedule') {
			$main_ticketid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `ticketid` FROM `ticket_schedule` WHERE `id` = '$id'"))['ticketid'];
		} else {
			$main_ticketid = $id;
		}
		mysqli_query($dbc, "UPDATE `tickets` SET `is_recurrence` = 0 WHERE `ticketid` = '$main_ticketid'");
		mysqli_query($dbc, "UPDATE `ticket_attached` SET `is_recurrence` = 0 WHERE `ticketid` = '$main_ticketid'");
		mysqli_query($dbc, "UPDATE `ticket_schedule` SET `is_recurrence` = 0 WHERE `ticketid` = '$main_ticketid'");
		mysqli_query($dbc, "UPDATE `ticket_comment` SET `is_recurrence` = 0 WHERE `ticketid` = '$main_ticketid'");
		$calendar_type = $_POST['calendar_type'];
		$status = $_POST['ticket_status'];
		$start_time = date('h:i a', strtotime($_POST['time_slot']));
		if($calendar_type == 'schedule') {
			if($_POST['blocktable'] == 'ticket_schedule') {
				$updated_fields = [];
				$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `id` = '$id'"));
				$start_time = date('H:i:s', strtotime($_POST['time_slot']));
				if($_POST['blocktype'] == 'dispatch_staff') {
					$contact_id = $_POST['contact'];
					$teams = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `teamid` SEPARATOR ',') as teams_list FROM `teams_staff` WHERE `contactid` = '$contact_id' AND `deleted` = 0"));
					if(!empty($teams['teams_list'])) {
						$teams_query = 'OR `teamid` IN ('.$teams['teams_list'].')';
					} else {
						$teams_query = '';
					}
					$equip_assign = mysqli_fetch_array(mysqli_query($dbc, "SELECT ea.* FROM `equipment_assignment` ea LEFT JOIN `equipment_assignment_staff` eas ON ea.`equipment_assignmentid` = eas.`equipment_assignmentid` WHERE ea.`deleted` = 0 AND DATE(`start_date`) <= '$start_date' AND DATE(ea.`end_date`) >= '$start_date' AND CONCAT(',',ea.`hide_days`,',') NOT LIKE '%,$start_date,%' AND ((eas.`contactid` = '$contact_id' AND eas.`deleted` = 0) $teams_query) ORDER BY ea.`start_date` DESC, ea.`end_date` ASC"));
					if(!empty($equip_assign)) {
						$_POST['equipassign'] = $equip_assign['equipment_assignmentid'];
						$equipmentid = $equip_assign['equipmentid'];
					} else {
						$dispatch_staff_query = ", `contactid` = ',$contact_id,'";
						$updated_fields['contactid'] = ",$contact_id,";
					}
				} else {
					$equipmentid = $_POST['contact'];
				}
				if(!empty($_POST['equipassign'])) {
					$equipment_assignmentid = $_POST['equipassign'];
					$equipassign = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'"));
					$teamid = $equipassign['teamid'];
					$region = explode('*#*',$equipassign['region'])[0];
					$location = explode('*#*',$equipassign['location'])[0];
					$classification = explode('*#*',$equipassign['classification'])[0];
					$equipassign_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '$equipment_assignmentid' AND `deleted` = 0"),MYSQLI_ASSOC);
				    $equipassign_hide_staff = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'"))['hide_staff']);
					$contact = [];
					foreach ($equipassign_staff as $staffid) {
						if(!in_array($staffid['contactid'], $equipassign_staff) && !in_array($staffid['contactid'], $equipassign_hide_staff)) {
							$contact[] = $staffid['contactid'];
						}
					}
					$team_staff = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC);
					foreach ($team_staff as $staffid) {
						if(!in_array($staffid['contactid'], $equipassign_staff) && !in_array($staffid['contactid'], $equipassign_hide_staff)) {
							$contact[] = $staffid['contactid'];
						}
					}
					$contact = implode(',',$contact);
					$equipassign_query = ", `contactid` = ',$contact,', `teamid` = '$teamid', `region` = '$region', `con_location` = '$location', `classification` = '$classification'";
					$updated_fields['contactid'] = ",$contact,";
				} else {
					$equipassign_query = "";
					if(!empty($equipmentid)) {
						$equipment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '$equipmentid'"));
						$region = explode('*#*',$equipment['region'])[0];
						$location = explode('*#*',$equipment['location'])[0];
						$classification = explode('*#*',$equipment['classification'])[0];
						if(!empty($region)) {
							$equipassign_query .= ", `region` = '$region'";
						}
						if(!empty($location)) {
							$equipassign_query .= ", `con_location` = '$location'";
						}
						if(!empty($classification)) {
							$equipassign_query .= ", `classification` = '$classification'";
						}
					}
				}
				$updated_fields['equipmentid'] = $equipmentid;
				$updated_fields['equipment_assignmentid'] = $equipment_assignmentid;
				$updated_fields['teamid'] = empty($teamid) ? 0 : $teamid;
				$updated_fields['region'] = $region;
				$updated_fields['con_location'] = $location;
				$updated_fields['classification'] = $classification;
				if (empty($status)) {
					$ticket_status = 'Active';
					$offline_table[] = 'ticket_schedule';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'id';
					$offline_fields[] = 'equipmentid';
					$offline_values[] = $equipmentid;
					$offline_table[] = 'ticket_schedule';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'id';
					$offline_fields[] = 'equipment_assignmentid';
					$offline_values[] = $equipment_assignmentid;
					$offline_table[] = 'ticket_schedule';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'id';
					$offline_fields[] = 'to_do_date';
					$offline_values[] = $time;
					$offline_table[] = 'ticket_schedule';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'id';
					$offline_fields[] = 'to_do_end_date';
					$offline_values[] = $time;
					$offline_table[] = 'ticket_schedule';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'id';
					$offline_fields[] = 'to_do_start_time';
					$offline_values[] = $start_time;
					$offline_table[] = 'ticket_schedule';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'id';
					$offline_fields[] = 'status';
					$offline_values[] = $ticket_status;
					if($online) {
						mysqli_query($dbc, "UPDATE `ticket_schedule` SET `equipmentid`='$equipmentid', `equipment_assignmentid` = '$equipment_assignmentid', `to_do_date`='$time', `to_do_end_date`='$time', `to_do_start_time` = IF(`scheduled_lock` > 0,`to_do_start_time`,'$start_time') WHERE `id`='$id'");
					}
				} else {
					$offline_table[] = 'ticket_schedule';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'id';
					$offline_fields[] = 'equipmentid';
					$offline_values[] = $equipmentid;
					$offline_table[] = 'ticket_schedule';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'id';
					$offline_fields[] = 'equipment_assignmentid';
					$offline_values[] = $equipment_assignmentid;
					$offline_table[] = 'ticket_schedule';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'id';
					$offline_fields[] = 'to_do_date';
					$offline_values[] = $time;
					$offline_table[] = 'ticket_schedule';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'id';
					$offline_fields[] = 'to_do_end_date';
					$offline_values[] = $time;
					$offline_table[] = 'ticket_schedule';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'id';
					$offline_fields[] = 'to_do_start_time';
					$offline_values[] = $start_time;
					if($online) {
						mysqli_query($dbc, "UPDATE `ticket_schedule` SET `equipmentid`='$equipmentid', `equipment_assignmentid` = '$equipment_assignmentid', `to_do_date`='$time', `to_do_end_date`='$time', `to_do_start_time` = IF(`scheduled_lock` > 0,`to_do_start_time`,'$start_time') WHERE `id`='$id'");
					}
				}
				if($online) {
					$updated_fields['to_do_date'] = $time;
					$updated_fields['to_do_end_date'] = $time;
					$updated_fields['to_do_start_time'] = ($ticket['scheduled_lock'] > 0 ? $ticket['to_do_start_time'] : $start_time);

					//Record history
					$ticket_histories = [];
					foreach($updated_fields as $key => $updated_field) {
						if($ticket[$key] != $updated_field) {
							$ticket_histories[$key] = "$key updated to $updated_field";
						}
					}
					if($ticket['equipment_assignmentid'] != $equipment_assignmentid) {
						$ea_contacts = [];
						foreach(explode(',', $contact) as $ea_contact) {
							if($ea_contact > 0) {
								$ea_contacts[] = get_contact($dbc, $ea_contact);
							}
						}
						$ticket_histories['equipment_assignmentid'] = "equipment_assignmentid updated to $equipment_assignmentid (".implode(', ',$ea_contacts).")";
					}
					if(!empty($ticket_histories)) {
						mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `src`, `description`) VALUES ('{$ticket['ticketid']}','{$_SESSION['contactid']}','calendar','Row #$id of ticket_schedule updated: ".implode(', ',$ticket_histories)."')");
					}
				}
			} else {
				$updated_fields = [];
				$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$id'"));
				if($_POST['blocktype'] == 'dispatch_staff') {
					$contact_id = $_POST['contact'];
					$teams = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `teamid` SEPARATOR ',') as teams_list FROM `teams_staff` WHERE `contactid` = '$contact_id' AND `deleted` = 0"));
					if(!empty($teams['teams_list'])) {
						$teams_query = 'OR `teamid` IN ('.$teams['teams_list'].')';
					} else {
						$teams_query = '';
					}
					$equip_assign = mysqli_fetch_array(mysqli_query($dbc, "SELECT ea.* FROM `equipment_assignment` ea LEFT JOIN `equipment_assignment_staff` eas ON ea.`equipment_assignmentid` = eas.`equipment_assignmentid` WHERE ea.`deleted` = 0 AND DATE(`start_date`) <= '$start_date' AND DATE(ea.`end_date`) >= '$start_date' AND CONCAT(',',ea.`hide_days`,',') NOT LIKE '%,$start_date,%' AND ((eas.`contactid` = '$contact_id' AND eas.`deleted` = 0) $teams_query) ORDER BY ea.`start_date` DESC, ea.`end_date` ASC"));
					if(!empty($equip_assign)) {
						$_POST['equipassign'] = $equip_assign['equipment_assignmentid'];
						$equipmentid = $equip_assign['equipmentid'];
					} else {
						$dispatch_staff_query = ", `contactid` = ',$contact_id,'";
						$updated_fields['contactid'] = ",$contact_id,";
					}
				} else {
					$equipmentid = $_POST['contact'];
				}
				if(!empty($_POST['equipassign'])) {
					$equipment_assignmentid = $_POST['equipassign'];
					$equipassign = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'"));
					$teamid = $equipassign['teamid'];
					$region = explode('*#*',$equipassign['region'])[0];
					$location = explode('*#*',$equipassign['location'])[0];
					$classification = explode('*#*',$equipassign['classification'])[0];
					$equipassign_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '$equipment_assignmentid' AND `deleted` = 0"),MYSQLI_ASSOC);
				    $equipassign_hide_staff = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'"))['hide_staff']);
					$contact = [];
					foreach ($equipassign_staff as $staffid) {
						if(!in_array($staffid['contactid'], $equipassign_staff) && !in_array($staffid['contactid'], $equipassign_hide_staff)) {
							$contact[] = $staffid['contactid'];
						}
					}
					$team_staff = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC);
					foreach ($team_staff as $staffid) {
						if(!in_array($staffid['contactid'], $equipassign_staff) && !in_array($staffid['contactid'], $equipassign_hide_staff)) {
							$contact[] = $staffid['contactid'];
						}
					}
					$contact = implode(',',$contact);
					$equipassign_query = ", `contactid` = ',$contact,', `teamid` = '$teamid', `region` = '$region', `con_location` = '$location', `classification` = '$classification'";
					$updated_fields['contactid'] = ",$contact,";
				} else {
					$equipassign_query = "";
					if(!empty($equipmentid)) {
						$equipment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '$equipmentid'"));
						$region = explode('*#*',$equipment['region'])[0];
						$location = explode('*#*',$equipment['location'])[0];
						$classification = explode('*#*',$equipment['classification'])[0];
						if(!empty($region)) {
							$equipassign_query .= ", `region` = '$region'";
						}
						if(!empty($location)) {
							$equipassign_query .= ", `con_location` = '$location'";
						}
						if(!empty($classification)) {
							$equipassign_query .= ", `classification` = '$classification'";
						}
					}
				}
				$updated_fields['equipmentid'] = $equipmentid;
				$updated_fields['equipment_assignmentid'] = $equipment_assignmentid;
				$updated_fields['teamid'] = empty($teamid) ? 0 : $teamid;
				$updated_fields['region'] = $region;
				$updated_fields['con_location'] = $location;
				$updated_fields['classification'] = $classification;
				if (empty($status)) {
					$ticket_status = 'Active';
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'equipmentid';
					$offline_values[] = $equipmentid;
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'equipment_assignmentid';
					$offline_values[] = $equipment_assignmentid;
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'to_do_date';
					$offline_values[] = $time;
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'to_do_end_date';
					$offline_values[] = $time;
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'to_do_start_time';
					$offline_values[] = $start_time;
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'status';
					$offline_values[] = $ticket_status;
					if($online) {
						mysqli_query($dbc, "UPDATE `tickets` SET `equipmentid`='$equipmentid', `equipment_assignmentid` = '$equipment_assignmentid', `to_do_date`='$time', `to_do_end_date`='$time', `to_do_start_time` = '$start_time', `status`='$ticket_status' $equipassign_query $dispatch_staff_query WHERE `ticketid`='$id'");
					}
				} else {
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'equipmentid';
					$offline_values[] = $equipmentid;
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'equipment_assignmentid';
					$offline_values[] = $equipment_assignmentid;
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'to_do_date';
					$offline_values[] = $time;
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'to_do_end_date';
					$offline_values[] = $time;
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'to_do_start_time';
					$offline_values[] = $start_time;
					if($online) {
						mysqli_query($dbc, "UPDATE `tickets` SET `equipmentid`='$equipmentid', `equipment_assignmentid` = '$equipment_assignmentid', `to_do_date`='$time', `to_do_end_date`='$time', `to_do_start_time` = '$start_time' $equipassign_query $dispatch_staff_query WHERE `ticketid`='$id'");
					}
				}
				if($online) {
					$updated_fields['to_do_date'] = $time;
					$updated_fields['to_do_end_date'] = $time;
					$updated_fields['to_do_start_time'] = ($ticket['scheduled_lock'] > 0 ? $ticket['to_do_start_time'] : $start_time);

					//Record history
					$ticket_histories = [];
					foreach($updated_fields as $key => $updated_field) {
						if($ticket[$key] != $updated_field) {
							$ticket_histories[$key] = "$key updated to $updated_field";
						}
					}
					if($ticket['equipment_assignmentid'] != $equipment_assignmentid) {
						$ea_contacts = [];
						foreach(explode(',', $contact) as $ea_contact) {
							if($ea_contact > 0) {
								$ea_contacts[] = get_contact($dbc, $ea_contact);
							}
						}
						$ticket_histories['equipment_assignmentid'] = "equipment_assignmentid updated to $equipment_assignmentid (".implode(', ',$ea_contacts).")";
					}
					if(!empty($ticket_histories)) {
						mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `src`, `description`) VALUES ('{$ticket['ticketid']}','{$_SESSION['contactid']}','calendar','Row #$id of tickets updated: ".implode(', ',$ticket_histories)."')");
					}
				}
			}
		} else {
			if($_POST['blocktable'] == 'ticket_schedule') {
				$ticketid = $id;
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'contactid';
				$offline_values[] = ','.$contact.',';
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'to_do_date';
				$offline_values[] = $time;
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'to_do_end_date';
				$offline_values[] = $time;
				$offline_table[] = 'ticket_schedule';
				$offline_tableid[] = $ticketid;
				$offline_table_field[] = 'ticketid';
				$offline_fields[] = 'to_do_start_time';
				$offline_values[] = $start_time;
				if($online) {
					mysqli_query($dbc, "UPDATE `ticket_schedule` SET `contactid` = ',$contact,', `to_do_date` = '$time', `to_do_end_date` = '$time', `to_do_start_time` = IF(`scheduled_lock` > 0,`to_do_start_time`,'$start_time') WHERE `ticketid` = '$id'");
				}
			} else {
				$ticketid = $id;
				$contact_query = '';
				$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
				if($_POST['add_staff'] == 1) {
					$add_staff = $_POST['add_staff'];
				} else if($old_contact != $contact) {
					if($td_blocktype == 'team') {
						$teamid = $contact;
						$contacts = [];
						$team_contacts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC);
						foreach($team_contacts as $team_contact) {
							if(strtolower(get_contact($dbc, $team_contact['contactid'], 'category')) == 'staff') {
								$contacts[] = $team_contact['contactid'];
							}
						}
					} else {
						$contacts = [$contact];
					}

					if($add_staff == 1) {
						$contacts = array_filter(array_unique(array_merge(explode(',',$ticket['contactid']))));
					}
			        $date_of_archival = date('Y-m-d');
	            	mysqli_query($dbc, "UPDATE `ticket_attached` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `ticketid` = '$ticketid' AND `src_table` = 'Staff' AND `item_id` NOT IN ('".implode("','",$contacts)."')");
				}
				if ($status == 'Internal QA') {
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'internal_qa_contactid';
					$offline_values[] = ','.$contact.',';
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'internal_qa_date';
					$offline_values[] = $time;
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'internal_qa_start_time';
					$offline_values[] = $start_time;
					if($online) {
						if($add_staff == 1) {
							$internal_qa_contactid = array_filter(explode(',',$ticket['internal_qa_contactid']));
							if(!in_array($contact, $internal_qa_contactid)) {
								$internal_qa_contactid[] = $contact;
							}
							$internal_qa_contactid = ','.implode(',',$internal_qa_contactid).',';
							$contact_query = "`internal_qa_contactid` = '$internal_qa_contactid',";
						} else {
							$contact_query = "`internal_qa_contactid` = ',$contact,',";
						}
						mysqli_query($dbc, "UPDATE `tickets` SET $contact_query `internal_qa_date` = '$time', `internal_qa_start_time` = '$start_time' WHERE `ticketid` = '$id'");
					}
				} else if ($status == 'Customer QA') {
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'deliverable_contactid';
					$offline_values[] = ','.$contact.',';
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'deliverable_date';
					$offline_values[] = $time;
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'deliverable_start_time';
					$offline_values[] = $start_time;
					if($online) {
						if($add_staff == 1) {
							$deliverable_contactid = array_filter(explode(',',$ticket['deliverable_contactid']));
							if(!in_array($contact, $deliverable_contactid)) {
								$deliverable_contactid[] = $contact;
							}
							$deliverable_contactid = ','.implode(',',$deliverable_contactid).',';
							$contact_query = "`deliverable_contactid` = '$deliverable_contactid',";
						} else {
							$contact_query = "`deliverable_contactid` = ',$contact,',";
						}
						mysqli_query($dbc, "UPDATE `tickets` SET $contact_query `deliverable_date` = '$time', `deliverable_start_time` = '$start_time' WHERE `ticketid` = '$id'");
					}
				} else {
					if(empty($status)) {
						$ticket_status = 'Scheduled/To Do';
						mysqli_query($dbc, "UPDATE `tickets` SET `status`='$ticket_status' WHERE `ticketid`='$id'");
					}
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'contactid';
					$offline_values[] = ','.$contact.',';
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'to_do_date';
					$offline_values[] = $time;
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'to_do_end_date';
					$offline_values[] = $time;
					$offline_table[] = 'tickets';
					$offline_tableid[] = $ticketid;
					$offline_table_field[] = 'ticketid';
					$offline_fields[] = 'to_do_start_time';
					$offline_values[] = $start_time;
					if($online) {
						if($td_blocktype == 'team') {
							$teamid = $contact;
							$contacts = [];
							$team_contacts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC);
							foreach($team_contacts as $team_contact) {
								if(strtolower(get_contact($dbc, $team_contact['contactid'], 'category')) == 'staff') {
									$contacts[] = $team_contact['contactid'];
								}
							}
						} else {
							$contacts = [$contact];
						}

						if($add_staff == 1) {
							$contacts = array_filter(array_unique(array_merge($contacts,explode(',',$ticket['contactid']))));
						} else if($old_contact == $contact) {
							$contacts = array_filter(array_unique(explode(',',$ticket['contactid'])));
						}
						$contact_query = "`contactid` = ',".implode(',',$contacts).",', ";
						foreach($contacts as $contact) {
							if(strtolower(get_contact($dbc, $contact, 'category')) == 'staff') {
								if($td_blocktype == 'team') {
									$position = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `contactid` = '$contact' AND `deleted` = 0"))['contact_position'];
								} else {
									$position = '';
								}
								mysqli_query($dbc, "INSERT INTO `ticket_attached` (`ticketid`, `src_table`, `item_id`, `position`) SELECT '$ticketid', 'Staff', '$contact', '$position' FROM (SELECT COUNT(*) rows FROM `ticket_attached` WHERE `ticketid` = '$ticketid' AND `src_table` = 'Staff' ANd `item_id` = '$contact' AND `deleted` = 0) num WHERE num.rows=0");
							}
						}
						
						mysqli_query($dbc, "UPDATE `tickets` SET $contact_query `to_do_date` = '$time', `to_do_end_date` = '$time', `to_do_start_time` = '$start_time' WHERE `ticketid` = '$id'");
					}
				}

				//Check staff capacity
				$staff_capacity = mysqli_fetch_array(mysqli_query($dbc, "SELECT `staff_capacity` FROM `tickets` WHERE `ticketid` = '$id'"))['staff_capacity'];
				if($staff_capacity > 0) {
					$current_capacity = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`id`) num_rows FROM `ticket_attached` WHERE `ticketid` = '$id' AND `src_table` = 'Staff' AND `deleted` = 0"))['num_rows'];
					if($staff_capacity > $current_capacity) {
						echo date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' + 10 seconds'));
					}
				}
			}
		}
	} else if($_POST['item'] == 'workorder') {
		$starttime = date('h:i a', strtotime($_POST['time_slot']));
		$calendar_type = $_POST['calendar_type'];
		if($calendar_type == 'schedule') {
			$equip_assignid = mysqli_fetch_array(mysqli_query($dbc, "SELECT ea.*, e.*,ea.`notes` FROM `equipment_assignment` ea LEFT JOIN `equipment` e ON ea.`equipmentid` = e.`equipmentid` WHERE e.`equipmentid` = '".$contact."' AND ea.`deleted` = 0 AND DATE(ea.`end_date`) >= DATE(CURDATE()) ORDER BY e.`category`, e.`unit_number`"))['equipment_assignmentid'];
			$offline_table[] = 'tickets';
			$offline_tableid[] = $ticketid;
			$offline_table_field[] = 'ticketid';
			$offline_fields[] = 'assign_equip_assignid';
			$offline_values[] = $equip_assignid;
			$offline_table[] = 'tickets';
			$offline_tableid[] = $ticketid;
			$offline_table_field[] = 'ticketid';
			$offline_fields[] = 'to_do_date';
			$offline_values[] = $time;
			$offline_table[] = 'tickets';
			$offline_tableid[] = $ticketid;
			$offline_table_field[] = 'ticketid';
			$offline_fields[] = 'to_do_time';
			$offline_values[] = $starttime;
			if($online) {
				mysqli_query($dbc, "UPDATE `workorder` SET `assign_equip_assignid`='$equip_assignid', `to_do_date`='$time', `to_do_time` = '$starttime' WHERE `workorderid`='$id'");
			}
		} else {
			$offline_table[] = 'tickets';
			$offline_tableid[] = $ticketid;
			$offline_table_field[] = 'ticketid';
			$offline_fields[] = 'contactid';
			$offline_values[] = ','.$contact.',';
			$offline_table[] = 'tickets';
			$offline_tableid[] = $ticketid;
			$offline_table_field[] = 'ticketid';
			$offline_fields[] = 'to_do_date';
			$offline_values[] = $time;
			$offline_table[] = 'tickets';
			$offline_tableid[] = $ticketid;
			$offline_table_field[] = 'ticketid';
			$offline_fields[] = 'to_do_time';
			$offline_values[] = $starttime;
			if($online) {
				mysqli_query($dbc, "UPDATE `workorder` SET `contactid`=',$contact,', `to_do_date`='$time', `to_do_time` = '$starttime' WHERE `workorderid`='$id'");
			}
		}
	} else if($_POST['item'] == 'appt') {
		$time = date('Y-m-d H:i:s', strtotime($_POST['time_slot']));
		$end_time = date('Y-m-d H:i:s', strtotime($time.' + '.$_POST['duration'].' seconds'));
		$client = 0;
		$staff = 0;
		if($_POST['mode'] == 'client') {
			$staff = $_POST['id'];
			$client = $_POST['contact'];
		} else {
			$client = $_POST['id'];
			$staff = $_POST['contact'];
		}
		if($online) {
			$offline_table[] = 'booking';
			$offline_tableid[] = 0;
			$offline_table_field[] = 'bookingid';
			$offline_fields[] = 'today_date';
			$offline_values[] = date('Y-m-d');
			$offline_table[] = 'booking';
			$offline_tableid[] = 0;
			$offline_table_field[] = 'bookingid';
			$offline_fields[] = 'create_by';
			$offline_values[] = get_contact($dbc, $_SESSION['contactid']);
			$offline_table[] = 'booking';
			$offline_tableid[] = 0;
			$offline_table_field[] = 'bookingid';
			$offline_fields[] = 'appoint_date';
			$offline_values[] = $time;
			$offline_table[] = 'booking';
			$offline_tableid[] = 0;
			$offline_table_field[] = 'bookingid';
			$offline_fields[] = 'end_appoint_date';
			$offline_values[] = $end_time;
			$offline_table[] = 'booking';
			$offline_tableid[] = 0;
			$offline_table_field[] = 'bookingid';
			$offline_fields[] = 'patientid';
			$offline_values[] = $client;
			$offline_table[] = 'booking';
			$offline_tableid[] = 0;
			$offline_table_field[] = 'bookingid';
			$offline_fields[] = 'therapistsid';
			$offline_values[] = $staff;
			mysqli_query($dbc, "INSERT INTO `booking` (`today_date`, `create_by`, `appoint_date`, `end_appoint_date`, `patientid`, `therapistsid`)
				VALUES ('".date('Y-m-d')."', '".get_contact($dbc, $_SESSION['contactid'])."', '$time', '$end_time', '$client', '$staff')");
		}
	} else if($_POST['item'] == 'shift') {
		$start_time = date('h:i a', strtotime($_POST['time_slot']));
		$end_time = date('h:i a', strtotime($start_time.' + '.$_POST['duration'].' seconds'));
		if($_POST['mode'] == 'client') {
			$staff = $_POST['id'];
			$client = $_POST['contact'];
		} else {
			$client = $_POST['id'];
			$staff = $_POST['contact'];
		}
		$offline_table[] = 'contacts_shifts';
		$offline_tableid[] = 0;
		$offline_table_field[] = 'shiftid';
		$offline_fields[] = 'contactid';
		$offline_values[] = $staff;
		$offline_table[] = 'contacts_shifts';
		$offline_tableid[] = 0;
		$offline_table_field[] = 'shiftid';
		$offline_fields[] = 'clientid';
		$offline_values[] = $client;
		$offline_table[] = 'contacts_shifts';
		$offline_tableid[] = 0;
		$offline_table_field[] = 'shiftid';
		$offline_fields[] = 'startdate';
		$offline_values[] = $time;
		$offline_table[] = 'contacts_shifts';
		$offline_tableid[] = 0;
		$offline_table_field[] = 'shiftid';
		$offline_fields[] = 'enddate';
		$offline_values[] = $time;
		$offline_table[] = 'contacts_shifts';
		$offline_tableid[] = 0;
		$offline_table_field[] = 'shiftid';
		$offline_fields[] = 'starttime';
		$offline_values[] = $start_time;
		$offline_table[] = 'contacts_shifts';
		$offline_tableid[] = 0;
		$offline_table_field[] = 'shiftid';
		$offline_fields[] = 'endtime';
		$offline_values[] = $end_time;
		if($online) {
			mysqli_query($dbc, "INSERT INTO `contacts_shifts` (`contactid`, `clientid`, `startdate`, `enddate`, `starttime`, `endtime`) VALUES ('$staff', '$client', '$time', '$time', '$start_time', '$end_time')");
		}
	} else if($_POST['item'] == 'waitlist') {
		$waitlist = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `waitlist` WHERE `waitlistid`='$id'"));
		$offline_table[] = 'waitlist';
		$offline_tableid[] = $id;
		$offline_table_field[] = 'waitlistid';
		$offline_fields[] = 'deleted';
		$offline_values[] = 1;
		if($online) {
            	    $date_of_archival = date('Y-m-d');
			mysqli_query($dbc, "UPDATE `waitlist` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `waitlistid`='$id'");
		}
		$time = date('Y-m-d H:i:s', strtotime($_POST['time_slot']));
		$end_time = date('Y-m-d H:i:s', strtotime($time.' + '.(intval($_POST['duration']) * 2).' seconds'));
		$staff = $_POST['contact'];
		$offline_table[] = 'booking';
		$offline_tableid[] = 0;
		$offline_table_field[] = 'bookingid';
		$offline_fields[] = 'today_date';
		$offline_values[] = date('Y-m-d');
		$offline_table[] = 'booking';
		$offline_tableid[] = 0;
		$offline_table_field[] = 'bookingid';
		$offline_fields[] = 'create_by';
		$offline_values[] = get_contact($dbc, $_SESSION['contactid']);
		$offline_table[] = 'booking';
		$offline_tableid[] = 0;
		$offline_table_field[] = 'bookingid';
		$offline_fields[] = 'appoint_date';
		$offline_values[] = $time;
		$offline_table[] = 'booking';
		$offline_tableid[] = 0;
		$offline_table_field[] = 'bookingid';
		$offline_fields[] = 'end_appoint_date';
		$offline_values[] = $end_time;
		$offline_table[] = 'booking';
		$offline_tableid[] = 0;
		$offline_table_field[] = 'bookingid';
		$offline_fields[] = 'patientid';
		$offline_values[] = $waitlist['patientid'];
		$offline_table[] = 'booking';
		$offline_tableid[] = 0;
		$offline_table_field[] = 'bookingid';
		$offline_fields[] = 'therapistsid';
		$offline_values[] = $staff;
		$offline_table[] = 'booking';
		$offline_tableid[] = 0;
		$offline_table_field[] = 'bookingid';
		$offline_fields[] = 'injuryid';
		$offline_values[] = $waitlist['injuryid'];
		$offline_table[] = 'booking';
		$offline_tableid[] = 0;
		$offline_table_field[] = 'bookingid';
		$offline_fields[] = 'type';
		$offline_values[] = $waitlist['appt_type'];
		if($online) {
			mysqli_query($dbc, "INSERT INTO `booking` (`today_date`, `create_by`, `appoint_date`, `end_appoint_date`, `patientid`, `therapistsid`, `injuryid`, `type`)
				VALUES ('".date('Y-m-d')."', '".get_contact($dbc, $_SESSION['contactid'])."', '$time', '$end_time', '{$waitlist['patientid']}', '$staff', '{$waitlist['injuryid']}', '{$waitlist['appt_type']}')");
		}
	}
}
if($_GET['fill'] == 'retrieve_injuries') {
	$patientid = $_GET['patientid'];
	$injury_html = '';

    $query = "SELECT * FROM `patient_injury` WHERE `contactid` = '$patientid' AND `discharge_date` IS NULL AND `deleted` = 0";
    $result = mysqli_query($dbc, $query);
    while ($row = mysqli_fetch_array($result)) {
        $injury_html .= '<option value="'.$row['injuryid'].'">'.$row['injury_name'].' : '.$row['injury_type'].' - '.$row['injury_date'].'</option>';
    }

    echo $injury_html;
}
if($_GET['fill'] == 'calendar_notes') {
	$contact = $_POST['contact_id'];
	$new_date = $_POST['date'];
	$notes = filter_var(htmlentities($_POST['notes']),FILTER_SANITIZE_STRING);

	$calendar_type = $_POST['calendar_type'];
	$calendar_mode = $_POST['calendar_mode'];
	if($calendar_type == 'schedule' && $calendar_mode != 'staff') {
		// $equip_assignid = mysqli_fetch_array(mysqli_query($dbc, "SELECT ea.*, e.*,ea.`notes` FROM `equipment_assignment` ea LEFT JOIN `equipment` e ON ea.`equipmentid` = e.`equipmentid` WHERE e.`equipmentid` = '".$contact."' AND ea.`deleted` = 0 AND DATE(ea.`end_date`) >= DATE(CURDATE()) ORDER BY e.`category`, e.`unit_number`"))['equipment_assignmentid'];
		// $offline_table[] = 'equipment_assignment';
		// $offline_tableid[] = $equip_assignid;
		// $offline_table_field[] = 'equipment_assignmentid';
		// $offline_fields[] = 'notes';
		// $offline_values[] = $notes;
		// if($online) {
		// 	mysqli_query($dbc, "UPDATE `equipment_assignment` SET `notes`='$notes' WHERE `equipment_assignmentid`='$equip_assignid'");
		// }
		$offline_table[] = 'calendar_notes';
		$offline_tableid[] = $contact."' AND `date`='".$new_date;
		$offline_table_field[] = 'contactid';
		$offline_fields[] = 'note';
		$offline_values[] = $notes;
		if($online) {
			mysqli_query($dbc, "INSERT INTO `calendar_notes` (`contactid`, `date`, `note`, `is_equipment`) SELECT '$contact', '$new_date', '$notes', 1 FROM (SELECT COUNT(*) rows FROM `calendar_notes` WHERE `contactid`='$contact' AND `date` = '$new_date' AND `deleted` = 0) num WHERE num.rows = 0");
			mysqli_query($dbc, "UPDATE `calendar_notes` SET `note` = '$notes' WHERE `contactid` = '$contact' AND `date` = '$new_date' AND `is_equipment` = 1");
		}
	} else {
		$offline_table[] = 'calendar_notes';
		$offline_tableid[] = $contact."' AND `date`='".$new_date;
		$offline_table_field[] = 'contactid';
		$offline_fields[] = 'note';
		$offline_values[] = $notes;
		if($online) {
			mysqli_query($dbc, "INSERT INTO `calendar_notes` (`contactid`, `date`, `note`) SELECT '$contact', '$new_date', '$notes' FROM (SELECT COUNT(*) rows FROM `calendar_notes` WHERE `contactid`='$contact' AND `date` = '$new_date' AND `deleted` = 0) num WHERE num.rows = 0");
			mysqli_query($dbc, "UPDATE `calendar_notes` SET `note` = '$notes' WHERE `contactid` = '$contact' AND `date` = '$new_date' AND `is_equipment` = 0");
		}
	}

	echo html_entity_decode($_POST['notes']);
}
if($_GET['fill'] == 'move_appt_month') {
	$new_date = $_POST['new_date'];
	$item_type = $_POST['item_type'];
	$contactid = $_POST['contact'];
	$old_contact = $_POST['old_contact'];
	$old_td_blocktype = $_POST['old_td_blocktype'];
	$new_td_blocktype = $_POST['new_td_blocktype'];

	if($item_type == 'shift') {
		$shiftid = $_POST['shift'];
		$old_date = $_POST['old_date'];

		$shift = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts_shifts` WHERE `shiftid` = '$shiftid'"));
		if(empty($contactid)) {
			$contactid = $shift['contactid'];
		}
		if($shift['startdate'] != $shift['enddate']) {
			$hide_days = $shift['hide_days'];
			$hide_days .= ','.$old_date;
			$hide_days = trim($hide_days, ',');
			$sql = "UPDATE `contacts_shifts` SET `hide_days` = '$hide_days' WHERE `shiftid` = '$shiftid'";
			$offline_table[] = 'contacts_shifts';
			$offline_tableid[] = $shiftid;
			$offline_table_field[] = 'shiftid';
			$offline_fields[] = 'hide_days';
			$offline_values[] = $hide_days;
			if($online) {
				mysqli_query($dbc, $sql);
			}

			$sql = "INSERT INTO `contacts_shifts` (`heading`, `contactid`, `security_level`, `clientid`, `startdate`, `enddate`, `starttime`, `endtime`, `dayoff_type`, `break_starttime`, `break_endtime`, `notes`)
				SELECT `heading`, '$contactid', `security_level`, `clientid`, '$new_date', '$new_date', `starttime`, `endtime`, `dayoff_type`, `break_starttime`, `break_endtime`, `notes` FROM `contacts_shifts` WHERE `shiftid` = '$shiftid'";
			if($online) {
				mysqli_query($dbc, $sql);
			}
			$shiftid = mysqli_insert_id($dbc);
		} else {
			$sql = "UPDATE `contacts_shifts` SET `startdate` = '$new_date', `enddate` = '$new_date', `contactid` = '$contactid' WHERE `shiftid` = '$shiftid'";
			$offline_table[] = 'contacts_shifts';
			$offline_tableid[] = $shiftid;
			$offline_table_field[] = 'shiftid';
			$offline_fields[] = 'startdate';
			$offline_values[] = $new_date;
			$offline_table[] = 'contacts_shifts';
			$offline_tableid[] = $shiftid;
			$offline_table_field[] = 'shiftid';
			$offline_fields[] = 'enddate';
			$offline_values[] = $new_date;
			$offline_table[] = 'contacts_shifts';
			$offline_tableid[] = $shiftid;
			$offline_table_field[] = 'shiftid';
			$offline_fields[] = 'contactid';
			$offline_values[] = $contactid;
			if($online) {
				mysqli_query($dbc, $sql);
			}
		}

		echo $shiftid;
	} else if($item_type == 'appt') {
		$bookingid = $_POST['appt'];
		$booking = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `booking` WHERE `bookingid` = '$bookingid'"));
		if(empty($contactid)) {
			$contactid = $booking['therapistsid'];
		}
		$date_diff = strtotime($new_date) - strtotime(date('Y-m-d', strtotime($booking['appoint_date'])));
		$end_date = strtotime(date('Y-m-d', strtotime($booking['end_appoint_date']))) + $date_diff;
		$appoint_date = date('Y-m-d', strtotime($new_date)).' '.date('H:i:s', strtotime($booking['appoint_date']));
		$end_appoint_date = date('Y-m-d', $end_date).' '.date('H:i:s', strtotime($booking['end_appoint_date']));

		$sql = "UPDATE `booking` SET `appoint_date` = '$appoint_date', `end_appoint_date` = '$end_appoint_date', `therapistsid` = '$contactid' WHERE `bookingid` = '$bookingid'";
		$offline_table[] = 'booking';
		$offline_tableid[] = $bookingid;
		$offline_table_field[] = 'bookingid';
		$offline_fields[] = 'appoint_date';
		$offline_values[] = $appoint_date;
		$offline_table[] = 'booking';
		$offline_tableid[] = $bookingid;
		$offline_table_field[] = 'bookingid';
		$offline_fields[] = 'end_appoint_date';
		$offline_values[] = $end_appoint_date;
		$offline_table[] = 'booking';
		$offline_tableid[] = $bookingid;
		$offline_table_field[] = 'bookingid';
		$offline_fields[] = 'therapistsid';
		$offline_values[] = $contactid;
		if($online) {
			mysqli_query($dbc, $sql);
		}
	} else if($item_type == 'ticket') {
		$ticketid = $_POST['ticket'];
		mysqli_query($dbc, "UPDATE `tickets` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
		mysqli_query($dbc, "UPDATE `ticket_attached` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
		mysqli_query($dbc, "UPDATE `ticket_schedule` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
		mysqli_query($dbc, "UPDATE `ticket_comment` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
		$add_staff = $_POST['add_staff'];
		$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT *, IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) `to_do_end_date` FROM `tickets` WHERE `ticketid` = '$ticketid'"));
		$status = $ticket['status'];
		if($_POST['mode'] == 'client') {
			$contacts = '';
			if ($status == 'Internal QA') {
				$contactid = trim($ticket['internal_qa_contactid'],',');
			} else if ($status == 'Customer QA') {
				$contactid = trim($ticket['deliverable_contactid'],',');
			} else {
				$contactid = trim($ticket['contactid'],',');
			}
		} else if($add_staff == 1 && !empty($contactid)) {
			$td_blocktype = $new_td_blocktype;
			if($td_blocktype == 'team') {
				$teamid = $contactid;
				$contacts = [];
				$team_contacts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC);
				foreach($team_contacts as $team_contact) {
					if(strtolower(get_contact($dbc, $team_contact['contactid'], 'category')) == 'staff') {
						$contacts[] = $team_contact['contactid'];
					}
				}
			} else {
				$contacts = [$contactid];
			}
			if ($status == 'Internal QA') {
				$internal_qa_contactid = array_filter(explode(',',$ticket['internal_qa_contactid']));
				foreach($contacts as $contactid) {
					if(!in_array($contactid, $internal_qa_contactid)) {
						$internal_qa_contactid[] = $contactid;
					}
				}
				$internal_qa_contactid = ','.implode(',',$internal_qa_contactid).',';
				$contactid = $internal_qa_contactid;
			} else if ($status == 'Customer QA') {
				$deliverable_contactid = array_filter(explode(',',$ticket['deliverable_contactid']));
				foreach($contacts as $contactid) {
					if(!in_array($contactid, $deliverable_contactid)) {
						$deliverable_contactid[] = $contactid;
					}
				}
				$deliverable_contactid = ','.implode(',',$deliverable_contactid).',';
				$contactid = $deliverable_contactid;
			} else {
				$new_contactid = array_filter(explode(',',$ticket['contactid']));
				foreach($contacts as $contactid) {
					if(!in_array($contactid, $new_contactid)) {
						$new_contactid[] = $contactid;
					}
				}
				$new_contactid = ','.implode(',',$new_contactid).',';
				$contactid = $new_contactid;
			}
		} else if($old_contact != $contactid && !empty($contactid)) {
			$td_blocktype = $new_td_blocktype;
			if($td_blocktype == 'team') {
				$teamid = $contactid;
				$contacts = [];
				$team_contacts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC);
				foreach($team_contacts as $team_contact) {
					if(strtolower(get_contact($dbc, $team_contact['contactid'], 'category')) == 'staff') {
						$contacts[] = $team_contact['contactid'];
					}
				}
			} else {
				$contacts = [$contactid];
			}
	        $date_of_archival = date('Y-m-d');
			mysqli_query($dbc, "UPDATE `ticket_attached` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `ticketid` = '$ticketid' AND `src_table` = 'Staff' AND `item_id` NOT IN ('".implode("','",$contacts)."')");

			$contactid = ','.implode(',',$contacts).',';
		} else {
			$td_blocktype = $old_td_blocktype;
			if ($status == 'Internal QA') {
				$contactid = $ticket['internal_qa_contactid'];
			} else if ($status == 'Customer QA') {
				$contactid = $ticket['deliverable_contactid'];
			} else {
				$contactid = $ticket['contactid'];
			}
		}

		if ($status == 'Internal QA') {
			if($online) {
				mysqli_query($dbc, "UPDATE `tickets` SET `internal_qa_contactid` = ',$contactid,', `internal_qa_date` = '$new_date' WHERE `ticketid` = '$ticketid'");
			}
		} else if ($status == 'Customer QA') {
			if($online) {
				mysqli_query($dbc, "UPDATE `tickets` SET `deliverable_contactid` = ',$contactid,', `deliverable_date` = '$new_date' WHERE `ticketid` = '$ticketid'");
			}
		} else {
			$date_diff = strtotime($new_date) - strtotime(date('Y-m-d', strtotime($ticket['to_do_date'])));
			$end_date = strtotime(date('Y-m-d', strtotime($ticket['to_do_end_date']))) + $date_diff;
			$new_end_date = date('Y-m-d', $end_date);
			if($online) {
				mysqli_query($dbc, "UPDATE `tickets` SET `contactid` = ',$contactid,', `to_do_date` = '$new_date', `to_do_end_date` = '$new_end_date' WHERE `ticketid` = '$ticketid'");
			}
		}

		foreach(explode(',',$contacts) as $contactid) {
			if($contactid > 0 && strtolower(get_contact($dbc, $contactid, 'category')) == 'staff') {
				if($td_blocktype == 'team') {
					$position = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `contactid` = '$contact' AND `deleted` = 0"))['contact_position'];
				} else {
					$position = '';
				}
				mysqli_query($dbc, "INSERT INTO `ticket_attached` (`ticketid`, `src_table`, `item_id`) SELECT '$ticketid', 'Staff', '$contactid' FROM (SELECT COUNT(*) rows FROM `ticket_attached` WHERE `ticketid` = '$ticketid' AND `src_table` = 'Staff' AND `item_id` = '$contactid' AND `deleted` = 0) num WHERE num.rows=0");
			}
		}
	} else if($item_type == 'task') {
		$tasklistid = $_POST['task'];
		$tasklist = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `tasklistid` = '$tasklistid'"));
		if(empty($contactid)) {
			$contactid = $tasklist['contactid'];
		}
		if($online) {
			mysqli_query($dbc, "UPDATE `tasklist` SET `contactid` = '$contactid', `task_tododate` = '$new_date' WHERE `tasklistid` = '$tasklistid'");
		}
	} else if($item_type == 'swo') {
		$workorderid = $_POST['swo'];
		$swo = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `site_work_orders` WHERE `workorderid` = '$workorderid'"));
		$date_diff = strtotime($new_date) - strtotime(date('Y-m-d', strtotime($swo['work_start_date'])));
		$end_date = strtotime(date('Y-m-d', strtotime($swo['work_end_date']))) + $date_diff;
		$new_end_date = date('Y-m-d', $end_date);
		if($online) {
			mysqli_query($dbc, "UPDATE `site_work_orders` SET `work_start_date` = '$new_date', `work_end_date` = '$new_end_date' WHERE `workorderid` = '$workorderid'");
		}
	} else if($item_type == 'ticket_equip') {
		$ticketid = $_POST['ticket'];
		if($_POST['ticket_table'] == 'ticket_schedule') {
			$ticket_scheduleid = $_POST['ticket_scheduleid'];
			$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT *, IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) `to_do_end_date` FROM `ticket_schedule` WHERE `id` = '$ticket_scheduleid'"));
			mysqli_query($dbc, "UPDATE `tickets` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket['ticketid']."'");
			mysqli_query($dbc, "UPDATE `ticket_attached` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket['ticketid']."'");
			mysqli_query($dbc, "UPDATE `ticket_schedule` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket['ticketid']."'");
			mysqli_query($dbc, "UPDATE `ticket_comment` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket['ticketid']."'");
		} else {
			$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT *, IFNULL(NULLIF(`to_do_end_date`,'0000-00-00'),`to_do_date`) `to_do_end_date` FROM `tickets` WHERE `ticketid` = '$ticketid'"));
			mysqli_query($dbc, "UPDATE `tickets` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
			mysqli_query($dbc, "UPDATE `ticket_attached` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
			mysqli_query($dbc, "UPDATE `ticket_schedule` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
			mysqli_query($dbc, "UPDATE `ticket_comment` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
		}
		$updated_fields = [];
		if($_POST['blocktype'] == 'dispatch_staff') {
			$contact_id = $_POST['contact'];
			if(empty($contact_id)) {
				$contact_id = $_POST['old_staff'];
			}
			$teams = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `teamid` SEPARATOR ',') as teams_list FROM `teams_staff` WHERE `contactid` = '$contact_id' AND `deleted` = 0"));
			if(!empty($teams['teams_list'])) {
				$teams_query = 'OR `teamid` IN ('.$teams['teams_list'].')';
			} else {
				$teams_query = '';
			}
			$equip_assign = mysqli_fetch_array(mysqli_query($dbc, "SELECT ea.* FROM `equipment_assignment` ea LEFT JOIN `equipment_assignment_staff` eas ON ea.`equipment_assignmentid` = eas.`equipment_assignmentid` WHERE ea.`deleted` = 0 AND DATE(`start_date`) <= '$new_date' AND DATE(ea.`end_date`) >= '$new_date' AND CONCAT(',',ea.`hide_days`,',') NOT LIKE '%,$new_date,%' AND ((eas.`contactid` = '$contact_id' AND eas.`deleted` = 0) $teams_query) ORDER BY ea.`start_date` DESC, ea.`end_date` ASC"));
			if(!empty($equip_assign)) {
				$_POST['equipassign'] = $equip_assign['equipment_assignmentid'];
				$equipmentid = $equip_assign['equipmentid'];
			} else {
				$dispatch_staff_query = ", `contactid` = ',$contact_id,'";
				$updated_fields['contactid'] = ",$contact_id,";
			}
		} else {
			$equipmentid = $_POST['contact'];
			if(empty($equipmentid)) {
				$equipmentid = $ticket['equipmentid'];
			}
		}
		if(!empty($_POST['equipassign'])) {
			$equipment_assignmentid = $_POST['equipassign'];
			$equipassign = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'"));
			$teamid = $equipassign['teamid'];
			$region = explode('*#*',$equipassign['region'])[0];
			$location = explode('*#*',$equipassign['location'])[0];
			$classification = explode('*#*',$equipassign['classification'])[0];
			$equipassign_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '$equipment_assignmentid' AND `deleted` = 0"),MYSQLI_ASSOC);
		    $equipassign_hide_staff = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'"))['hide_staff']);
			$contact = [];
			foreach ($equipassign_staff as $staffid) {
				if(!in_array($staffid['contactid'], $contact) && !in_array($staffid['contactid'], $equipassign_hide_staff)) {
					$contact[] = $staffid['contactid'];
				}
			}
			$team_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC);
			foreach ($team_staff as $staffid) {
				if(!in_array($staffid['contactid'], $contact) && !in_array($staffid['contactid'], $equipassign_hide_staff)) {
					$contact[] = $staffid['contactid'];
				}
			}
			$contact = implode(',',$contact);
			$equipassign_query = ", `contactid` = ',$contact,', `teamid` = '$teamid', `region` = '$region', `con_location` = '$location', `classification` = '$classification'";
			$updated_fields['contactid'] = ",$contact,";
		} else {
			$equipassign_query = "";
			if(!empty($equipmentid)) {
				$equipment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '$equipmentid'"));
				$region = explode('*#*',$equipment['region'])[0];
				$location = explode('*#*',$equipment['location'])[0];
				$classification = explode('*#*',$equipment['classification'])[0];
				if(!empty($region)) {
					$equipassign_query .= ", `region` = '$region'";
				}
				if(!empty($location)) {
					$equipassign_query .= ", `con_location` = '$location'";
				}
				if(!empty($classification)) {
					$equipassign_query .= ", `classification` = '$classification'";
				}
			}
		}
		$updated_fields['equipmentid'] = $equipmentid;
		$updated_fields['equipment_assignmentid'] = $equipment_assignmentid;
		$updated_fields['teamid'] = empty($teamid) ? 0 : $teamid;
		$updated_fields['region'] = $region;
		$updated_fields['con_location'] = $location;
		$updated_fields['classification'] = $classification;
		$date_diff = strtotime($new_date) - strtotime(date('Y-m-d', strtotime($ticket['to_do_date'])));
		$end_date = strtotime(date('Y-m-d', strtotime($ticket['to_do_end_date']))) + $date_diff;
		$new_end_date = date('Y-m-d', $end_date);
		if($online) {
			if($_POST['ticket_table'] == 'ticket_schedule') {
				$ticket_scheduleid = $_POST['ticket_scheduleid'];
				mysqli_query($dbc, "UPDATE `ticket_schedule` SET `equipmentid` = '$equipmentid', `to_do_date` = '$new_date', `to_do_end_date` = '$new_end_date' $equipassign_query $dispatch_staff_query WHERE `id` = '$ticket_scheduleid'");
			} else {
				mysqli_query($dbc, "UPDATE `tickets` SET `equipmentid` = '$equipmentid', `to_do_date` = '$new_date', `to_do_end_date` = '$new_end_date' $equipassign_query $dispatch_staff_query WHERE `ticketid` = '$ticketid'");
			}
			$updated_fields['to_do_date'] = $new_date;
			$updated_fields['to_do_end_date'] = $new_end_date;

			//Record history
			$ticket_histories = [];
			foreach($updated_fields as $key => $updated_field) {
				if($ticket[$key] != $updated_field) {
					$ticket_histories[$key] = "$key updated to $updated_field";
				}
			}
			if($ticket['equipment_assignmentid'] != $equipment_assignmentid) {
				$ea_contacts = [];
				foreach(explode(',', $contact) as $ea_contact) {
					if($ea_contact > 0) {
						$ea_contacts[] = get_contact($dbc, $ea_contact);
					}
				}
				$ticket_histories['equipment_assignmentid'] = "equipment_assignmentid updated to $equipment_assignmentid (".implode(', ',$ea_contacts).")";
			}
			if(!empty($ticket_histories)) {
				mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `src`, `description`) VALUES ('{$ticket['ticketid']}','{$_SESSION['contactid']}','calendar','Row #".($_POST['ticket_table'] == 'ticket_schedule' ? $ticket_scheduleid : $ticketid)." of ".($_POST['ticket_table'] == 'ticket_schedule' ? 'ticket_schedule' : 'tickets')." updated: ".implode(', ',$ticket_histories)."')");
			}
		}
	} else if($item_type == 'estimate') {
		$estimate_actionid = $_POST['estimateaction'];
		$estimate_action = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `estimate_actions` WHERE `id` = '$estimate_actionid'"));
		if(empty($contactid)) {
			$contactid = $estimate_action['contactid'];
		}
		if($online) {
			mysqli_query($dbc, "UPDATE `estimate_actions` SET `due_date` = '$new_date', `contactid` = '$contactid' WHERE `id` = '$estimate_actionid'");
		}
	} else if($item_type == 'ticket_event') {
		$ticketid = $_POST['ticket'];
		mysqli_query($dbc, "UPDATE `tickets` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
		mysqli_query($dbc, "UPDATE `ticket_attached` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
		mysqli_query($dbc, "UPDATE `ticket_schedule` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
		mysqli_query($dbc, "UPDATE `ticket_comment` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
		$projectid = $_POST['contact'];
		$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
		if(empty($projectid)) {
			$projectid = $ticket['projectid'];
		}
		if($online) {
			mysqli_query($dbc, "UPDATE `tickets` SET `to_do_date` = '$new_date', `projectid` = '$projectid' WHERE `ticketid` = '$ticketid'");
		}
	}
}
if($_GET['fill'] == 'delete_shift') {
	$shiftid = $_GET['shiftid'];
	$shifts = $_GET['shifts'];
	if($shifts == 'once') {
		$current_day = $_GET['current_day'];

		$shift = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts_shifts` WHERE `shiftid` = '$shiftid'"));
		$hide_days = $shift['hide_days'];
		$hide_days .= ','.$current_day;
		$hide_days = trim($hide_days, ',');
		$sql = "UPDATE `contacts_shifts` SET `hide_days` = '$hide_days' WHERE `shiftid` = '$shiftid'";
		$offline_table[] = 'contacts_shifts';
		$offline_tableid[] = $shiftid;
		$offline_table_field[] = 'shiftid';
		$offline_fields[] = 'hide_days';
		$offline_values[] = $hide_days;
		if($online) {
			mysqli_query($dbc, $sql);
		}
	} else {
        $date_of_archival = date('Y-m-d');
		$sql = "UPDATE `contacts_shifts` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `shiftid` = '$shiftid'";
		$offline_table[] = 'contacts_shifts';
		$offline_tableid[] = $shiftid;
		$offline_table_field[] = 'shiftid';
		$offline_fields[] = 'deleted';
		$offline_values[] = 1;
		if($online) {
			mysqli_query($dbc, $sql);
		}
	}
} else if($_GET['fill'] == 'mapping_address') {
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	$equipment = filter_var($_POST['equipment'],FILTER_SANITIZE_STRING);
	$tickets = mysqli_query($dbc, "SELECT `delivery_start_address`, `delivery_end_address` FROM `tickets` WHERE `to_do_date`='$date' AND `equipmentid` = '$equipment' AND `deleted`=0 AND (`delivery_start_address` NOT LIKE '' OR `delivery_end_address` NOT LIKE '')");
	if(mysqli_num_rows($tickets) > 0) {
		$ticket = mysqli_fetch_assoc($tickets);
		echo $ticket['delivery_start_address']."\n".$ticket['delivery_end_address'];
	} else {
		echo mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `current_address` FROM `equipment` WHERE `equipmentid` = '$equipment'"))['current_address'];
	}
} else if($_GET['fill'] == 'get_sortable_tickets') {
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	$equipment = filter_var($_POST['equipment'],FILTER_SANITIZE_STRING);
    $default_status = get_config($dbc, 'ticket_default_status');
	$address = ['init_address'];
	$tickets = mysqli_query($dbc, "SELECT `t`.`ticketid`, IF((IFNULL(NULLIF(CONCAT(IFNULL(`sched`.`address`,''),IFNULL(`sched`.`city`,'')),''),CONCAT(IFNULL(`t`.`address`,''),IFNULL(`t`.`city`,''))) NOT IN (SELECT CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')) FROM `contacts` WHERE `category`='Warehouses') OR `sched`.`type`='warehouse'),'warehouse','') `sched_type`, `sched`.`id`, IFNULL(`sched`.`to_do_date`,`t`.`to_do_date`) `date`, IFNULL(`sched`.`to_do_start_time`,`t`.`to_do_start_time`) `time`, IFNULL(`sched`.`address`,`t`.`pickup_address`) `address`, IFNULL(`sched`.`city`,`t`.`pickup_city`) `city`, IFNULL(`sched`.`province`,'') `province`, IFNULL(`sched`.`postal_code`,`t`.`pickup_postal_code`) `postal_code`, IFNULL(`sched`.`status`,`t`.`status`) `status` FROM `tickets` `t` LEFT JOIN `ticket_schedule` `sched` ON `t`.`ticketid`=`sched`.`ticketid` AND `sched`.`deleted`=0 WHERE IFNULL(NULLIF(`sched`.`equipmentid`,0),`t`.`equipmentid`)='$equipment' AND IFNULL(NULLIF(`sched`.`to_do_date`,'0000-00-00'),`t`.`to_do_date`)='$date' ORDER BY IFNULL(`sched`.`to_do_start_time`,`t`.`to_do_start_time`)");
	while($ticket = mysqli_fetch_assoc($tickets)) {
		if($ticket['address'].$ticket['city'].$ticket['province'].$ticket['postal_code'] != '' && ($_POST['include_complete'] != false || ($ticket['status'] != $default_status && $ticket['status'] != ''))) {
			$addresses[] = $ticket['ticketid'].'#*#'.$ticket['id'].'#*#'.trim($ticket['address'].' '.$ticket['city'].' '.$ticket['province'].' '.$ticket['postal_code']);
		} else {
			$addresses[0] = $ticket['ticketid'].'#*#'.$ticket['id'].'#*#'.trim($ticket['address'].' '.$ticket['city'].' '.$ticket['province'].' '.$ticket['postal_code']);
        }
	}
	echo implode("\n",$addresses);
} else if($_GET['fill'] == 'get_ticket_addresses') {
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	$equipment = filter_var($_POST['equipment'],FILTER_SANITIZE_STRING);
    $default_status = get_config($dbc, 'ticket_default_status');
	$address = [];
	$tickets = mysqli_query($dbc, "SELECT `t`.`ticketid`, IFNULL(`sched`.`to_do_date`,`t`.`to_do_date`) `date`, IFNULL(`sched`.`to_do_start_time`,`t`.`to_do_start_time`) `time`, IFNULL(`sched`.`address`,`t`.`pickup_address`) `address`, IFNULL(`sched`.`city`,`t`.`pickup_city`) `city`, IFNULL(`sched`.`province`,'') `province`, IFNULL(`sched`.`postal_code`,`t`.`pickup_postal_code`) `postal_code`, IFNULL(`sched`.`status`,`t`.`status`) `status` FROM `tickets` `t` LEFT JOIN `ticket_schedule` `sched` ON `t`.`ticketid`=`sched`.`ticketid` AND `sched`.`deleted`=0 WHERE IFNULL(NULLIF(`sched`.`equipmentid`,0),`t`.`equipmentid`)='$equipment' AND IFNULL(NULLIF(`sched`.`to_do_date`,'0000-00-00'),`t`.`to_do_date`)='$date' ORDER BY IFNULL(`sched`.`to_do_start_time`,`t`.`to_do_start_time`)");
	while($ticket = mysqli_fetch_assoc($tickets)) {
		if($ticket['address'].$ticket['city'].$ticket['province'].$ticket['postal_code'] != '' && (trim($ticket['address'].' '.$ticket['city'].' '.$ticket['province'].' '.$ticket['postal_code'])) != $prev_address && ($_POST['include_complete'] !== false || ($ticket['status'] != $default_status && $ticket['status'] != ''))) {
			$prev_address = trim($ticket['address'].' '.$ticket['city'].' '.$ticket['province'].' '.$ticket['postal_code']);
      $addresses[] = trim($ticket['address'].' '.$ticket['city'].' '.$ticket['province'].' '.$ticket['postal_code']);
		}
	}
	echo implode("\n",$addresses);
} else if($_GET['fill'] == 'sort_tickets') {
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	$equipment = filter_var($_POST['equipment'],FILTER_SANITIZE_STRING);
	$start_address = filter_var($_POST['start_address'],FILTER_SANITIZE_STRING);
	$end_address = filter_var($_POST['end_address'],FILTER_SANITIZE_STRING);
    $warehouses = $dbc->query("SELECT `tickets`.`ticketid`, `ticket_schedule`.`id` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE `tickets`.`deleted`=0 AND ((`tickets`.`to_do_date` = '".$date."' OR '".$date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR `ticket_schedule`.`to_do_date`='".$date."' OR '".$date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) IN ('$equipment') AND IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) > 0)) AND (REPLACE(REPLACE(IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''),IFNULL(`tickets`.`city`,''))),' ',''),'-','') IN (SELECT REPLACE(REPLACE(CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')),' ',''),'-','') FROM `contacts` WHERE `category`='Warehouses') OR `ticket_schedule`.`type`='warehouse') ORDER BY `ticket_schedule`.`to_do_date`,`ticket_schedule`.`to_do_start_time`");
    $auto_duration = strtotime(get_config($dbc, 'scheduling_increments').' minutes') - strtotime('0 minutes');
	$available_increment = get_config($dbc, 'delivery_timeframe_default');
    if($warehouses->num_rows > 0 && $_POST['from_current'] == false) {
        $start_time = get_config($dbc, 'ticket_warehouse_start_time');
        while($warehouse = $warehouses->fetch_assoc()) {
			$warehouse_hours = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts`.`hours_of_operation` FROM `ticket_schedule` LEFT JOIN `contacts` ON IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),'') = CONCAT(IFNULL(`contacts`.`address`,''),IFNULL(`contacts`.`city`,'')) AND `contacts`.`category`='Warehouses' WHERE `ticket_schedule`.`id` = '".$warehouse['id']."'"))['hours_of_operation'];
			$day_i = date('w',strtotime($date));
			$hours = explode('-',explode(',',$warehouse_hours)[$day_i]);
			$hop_start_time = !empty($hours[0]) ? date('H:i', strtotime($hours[0])) : '';
			$hop_end_time = !empty($hours[1]) ? date('H:i', strtotime($hours[1])) : '';
			if(!empty($hop_start_time) && !empty($hop_end_time)) {
				if(strtotime($start_time) < strtotime($hop_start_time)) {
					$start_time = date('h:i a',strtotime($hop_start_time));
				}
				if(strtotime($start_time) > strtotime($hop_end_time)) {
					$start_time = date('h:i a',strtotime($hop_end_time));
				}
			}
            if($warehouse['id'] > 0) {
                $length = get_field_value('est_time','ticket_schedule','id',$warehouse['id']);
                if(empty($length)) {
                    $length = get_field_value('cust_time','ticket_schedule','id',$warehouse['id']);
                }
                if(empty($length)) {
                    $length = get_field_value('est_time','tickets','ticketid',$warehouse['ticketid']);
                }
                if(empty($length)) {
                    $length = get_field_value('max_time','tickets','ticketid',$warehouse['ticketid']);
                }
                $end_time = date('H:i:s',strtotime($start_time.' + '.(floor($length) * 1).' hour '.floor($length * 60).' minute '.($length % 60).' second'));
                $dbc->query("UPDATE `ticket_schedule` SET `to_do_start_time`='$start_time', `to_do_end_time`='$end_time', `sort_status`=1 WHERE `id`='".$warehouse['id']."'");
            } else {
                $length = time_time2decimal(get_field_value('est_time','tickets','ticketid',$warehouse['ticketid']));
                if(empty($length)) {
                    $length = get_field_value('max_time','tickets','ticketid',$warehouse['ticketid']);
                }
                $end_time = date('H:i:s',strtotime($start_time.' + '.(floor($length) * 1).' hour '.floor($length * 60).' minute '.($length % 60).' second'));
                $dbc->query("UPDATE `tickets` SET `to_do_start_time`='$start_time', `to_do_end_time`='$end_time', `sort_status`=1 WHERE `ticketid`='".$warehouse['ticketid']."'");
            }
        }
		$start_time = date('H:i:s',strtotime($start_time) + ($_POST['drive_time'][0] > 0 ? filter_var($_POST['drive_time'][$i+1],FILTER_SANITIZE_STRING) : $auto_duration * ceil((strtotime($length) - strtotime('00:00')) / $auto_duration)));
    } else if(!empty($_POST['start_of_day'])) {
        $start_time = date('H:i', strtotime(filter_var($_POST['start_of_day'],FILTER_SANITIZE_STRING)));
    } else if($_POST['from_current'] == true && $date == date('Y-m-d')) {
        $start_time = date('H:i', time() + ($_POST['drive_time'][0] > 0 ? filter_var($_POST['drive_time'][$i+1],FILTER_SANITIZE_STRING) : 0));
    } else {
        $start_time = get_config($dbc, 'scheduling_day_start');
    }
	foreach($_POST['ticket_sort'] as $i => $ticketid) {
		$ticketid = filter_var($ticketid,FILTER_SANITIZE_STRING);
		$stopid = filter_var($_POST['stop_sort'][$i],FILTER_SANITIZE_STRING);
        $est_drive = filter_var($_POST['drive_time'][$i+1],FILTER_SANITIZE_STRING);
        $start_available = $start_time;
		$end_available = date('H:i',strtotime($start_time.' + '.$available_increment.' hours'));
		if($online) {
            if($stopid > 0) {
                $length = get_field_value('est_time','ticket_schedule','id',$stopid);
                if(empty($length)) {
                    $length = time_time2decimal(get_field_value('est_time','tickets','ticketid',$ticketid));
                }
                if(empty($length)) {
                    $length = get_field_value('max_time','tickets','ticketid',$ticketid);
                }
                $end_time = date('H:i:s',strtotime($start_time) + ($length * 3600));
                echo "UPDATE `ticket_schedule` SET `to_do_start_time`='$start_time', `to_do_end_time`='$end_time', `start_available`='$start_available', `end_available`='$end_available', `sort_status`=1 WHERE `ticketid`='$ticketid' AND `id`='$stopid'";
                mysqli_query($dbc, "UPDATE `ticket_schedule` SET `to_do_start_time`='$start_time', `to_do_end_time`='$end_time', `start_available`='$start_available', `end_available`='$end_available', `sort_status`=1 WHERE `ticketid`='$ticketid' AND `id`='$stopid'");
            } else {
                $length = time_time2decimal(get_field_value('est_time','tickets','ticketid',$ticketid));
                if(empty($length)) {
                    $length = get_field_value('max_time','tickets','ticketid',$ticketid);
                }
                $end_time = date('H:i:s',strtotime($start_time) + ($length * 3600));
                mysqli_query($dbc, "UPDATE `tickets` SET `to_do_start_time`='$start_time', `to_do_end_time`='$end_time', `pickup_date`=CONCAT(`to_do_date`,' $start_time'), `delivery_start_address`='$start_address', `delivery_end_address`='$end_address', `pickup_start_available`='$start_available', `pickup_end_available`='$end_available', `sort_status`=1 WHERE `ticketid`='$ticketid'");
                mysqli_query($dbc, "UPDATE `tickets` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
                mysqli_query($dbc, "UPDATE `ticket_attached` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
                mysqli_query($dbc, "UPDATE `ticket_schedule` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
                mysqli_query($dbc, "UPDATE `ticket_comment` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
            }
		}
        // echo $est_drive.'/'.$auto_duration.' ('.$length.'): '.$start_time.' - '.$end_time.'; ';
		$start_time = date('H:i:s',strtotime($end_time) + ($est_drive > 0 ? $est_drive : $auto_duration * ceil((strtotime($length) - strtotime('00:00')) / $auto_duration)));
	}
	$warehouse_end_time = $start_time;
	$dbc->query("UPDATE `ticket_schedule` SET `to_do_start_time` = '$warehouse_end_time' WHERE `id` IN (SELECT `id` FROM (SELECT `id` FROM `ticket_schedule` `main_table` WHERE `equipmentid`='$equipment' AND `to_do_date`='$date' AND `deleted`=0 AND (`type` = 'warehouse' OR IFNULL(NULLIF(CONCAT(IFNULL(`main_table`.`address`,''),IFNULL(`main_table`.`city`,'')),''),'') IN (SELECT CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')) FROM `contacts` WHERE `category`='Warehouses')) AND (SELECT COUNT(`id`) FROM `ticket_schedule` WHERE `equipmentid` = '$equipment' AND `to_do_date`='$date' AND `ticketid`=`main_table`.`ticketid` AND `deleted` = 0 AND (`type` = 'warehouse' OR IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),'') IN (SELECT CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')) FROM `contacts` WHERE `category`='Warehouses'))) > 1 AND `id` = (SELECT `id` FROM `ticket_schedule` WHERE `equipmentid` = '$equipment' AND `to_do_date`='$date' AND `ticketid`=`main_table`.`ticketid` AND `deleted` = 0 AND (`type` = 'warehouse' OR IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),'') IN (SELECT CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')) FROM `contacts` WHERE `category`='Warehouses')) ORDER BY `id` DESC LIMIT 1)) `warehouse_stops`)");
}
if($_GET['fill'] == 'delete_appt') {
	$bookingid = $_GET['bookingid'];
	$offline_table[] = 'booking';
	$offline_tableid[] = $bookingid;
	$offline_table_field[] = 'bookingid';
	$offline_fields[] = 'deleted';
	$offline_values[] = 1;
	if($online) {
        $date_of_archival = date('Y-m-d');
		mysqli_query($dbc, "UPDATE `booking` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `bookingid` = '$bookingid'");
	}
}
if($_GET['fill'] == 'equip_assign_draggable') {
	$blocktype = $_POST['blocktype'];
	$equipmentid = $_POST['equipmentid'];
	$equipment_assignmentid = $_POST['equipment_assignid'];
	$contractor = $_POST['contractor'];
	$date = $_POST['date'];
	$equipment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '$equipmentid'"));
	$region = explode('*#*',$equipment['region'])[0];
	$location = explode('*#*',$equipment['location'])[0];
	$classification = explode('*#*',$equipment['classification'])[0];

	if(!empty($equipment_assignmentid)) {
		$old_equipment_assignmentid = $equipment_assignmentid;
		$equipment_assignment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'"));
		if($equipment_assignment['start_date'] != $equipment_assignment['end_date']) {
			$equipment_assignment_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '$equipment_assignmentid' AND `deleted` = 0"),MYSQLI_ASSOC);
			if($online) {
				mysqli_query($dbc, "INSERT INTO `equipment_assignment` (`equipmentid`, `teamid`, `region`, `classification`, `location`, `notes`, `clientid`) SELECT `equipmentid`, `teamid`, `region`, `classification`, `location`, `notes`, `clientid` FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'");
			}
			$equipment_assignmentid = mysqli_insert_id($dbc);
			if($online) {
				mysqli_query($dbc, "UPDATE `equipment_assignment` SET `start_date` = '$date', `end_date` = '$date' WHERE `equipment_assignmentid` = '$equipment_assignmentid'");
			}
			foreach ($equipment_assignment_staff as $staff) {
				$staffid = $staff['id'];
				if($online) {
					mysqli_query($dbc, "INSERT INTO `equipment_assignment_staff` (`contactid`, `contact_position`, `contractor`) SELECT `contactid`, `contact_position`, `contractor` FROM `equipment_assignment_staff` WHERE `id` = '$staffid'");
					$equip_staffid = mysqli_insert_id($dbc);
					mysqli_query($dbc, "UPDATE `equipment_assignment_staff` SET `equipment_assignmentid` = '$equipment_assignmentid' WHERE `id` = '$equip_staffid'");
				}
			}

			//Add this day as a hidden day so it won't be displayed on the calendar (can clash in month view or if we changed the equipment for a single day, etc.)
			$hide_days = $equipment_assignment['hide_days'];
			$hide_days .= ','.$date;
			$hide_days = trim($hide_days, ',');
			if($online) {
				mysqli_query($dbc, "UPDATE `equipment_assignment` SET `hide_days` = '$hide_days' WHERE `equipment_assignmentid` = '$old_equipment_assignmentid'");
			}
		}
	} else {
		if($online) {
			mysqli_query($dbc, "INSERT INTO `equipment_assignment` (`equipmentid`, `start_date`, `end_date`, `region`, `location`, `classification`) VALUES ('$equipmentid', '$date', '$date', '$region', '$location', '$classification')");
			$equipment_assignmentid = mysqli_insert_id($dbc);
		}
	}
	$equipment_assignment_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '$equipment_assignmentid' AND `deleted` = 0"),MYSQLI_ASSOC);
	if($blocktype == 'team') {
		$teamid = $_POST['teamid'];
		if($online) {
			mysqli_query($dbc, "UPDATE `equipment_assignment` SET `teamid` = '$teamid' WHERE `equipment_assignmentid` = '$equipment_assignmentid'");
		}
		$team_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid'"),MYSQLI_ASSOC);

		//Check each existing staff to see if they are part of the new team so their positions won't get wiped
		foreach($equipment_assignment_staff as $equip_key => $equip_staff) {
			$in_team = false;
			foreach ($team_staff as $key => $staff) {
				if ($equip_staff['contactid'] == $staff['contactid']) {
					$in_team = true;
					unset($team_staff[$key]);
					unset($equipment_assignment_staff[$equip_key]);
					break;
				}
			}
			//Remove staff from the Equipment Assignment if they are not found in the new team
			if(!$in_team) {
				if($online) {
	        $date_of_archival = date('Y-m-d');
				mysqli_query($dbc, "UPDATE `equipment_assignment_staff` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `id` = '".$equip_staff['id']."'");
				}
			}
		}

		//Insert the rest of the team members that weren't found
		foreach($team_staff as $staff) {
			$staffid = $staff['contactid'];
			if($online) {
				mysqli_query($dbc, "INSERT INTO `equipment_assignment_staff` (`equipment_assignmentid`, `contactid`) VALUES ('$equipment_assignmentid', '$staffid')");
			}
		}
	} else if($blocktype == 'client') {
		$clientid = $_POST['clientid'];
		if($online) {
			mysqli_query($dbc, "UPDATE `equipment_assignment` SET `clientid` = '$clientid' WHERE `equipment_assignmentid` = '$equipment_assignmentid'");
		}
	} else if($blocktype == 'staff') {
		$staffid = $_POST['staffid'];
		$num_rows = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) FROM `equipment_assignment_staff` WHERE `contactid` = '$staffid' AND `equipment_assignmentid` = '$equipment_assignmentid' AND `deleted` = 0"))['num_rows'];
		if($num_rows == 0) {
			if($online) {
				if(get_config($dbc, 'equip_multi_assign_staff_disallow') > 0) {
					mysqli_query($dbc, "UPDATE `equipment_assignment_staff` LEFT JOIN `equipment_assignment` ON `equipment_assignment`.`equipment_assignmentid`=`equipment_assignment_staff`.`equipment_assignmentid` SET `equipment_assignment_staff`.`deleted`=1, `equipment_assignment_staff`.`date_of_archival`=DATE(NOW()) WHERE `equipment_assignment`.`start_date`='$date' AND `equipment_assignment`.`end_date`='$date' AND `equipment_assignment_staff`.`contactid`='$staffid' AND `equipment_assignment_staff`.`deleted`=0");
				}
				mysqli_query($dbc, "INSERT INTO `equipment_assignment_staff` (`equipment_assignmentid`, `contactid`, `contractor`) VALUES ('$equipment_assignmentid', '$staffid', '$contractor')");
			    $equipassign_hide_staff = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'"))['hide_staff']);
			    $hide_staff = [];
			    foreach($equipassign_hide_staff as $hide_staffid) {
			    	if($hide_staffid != $staffid) {
			    		$hide_staff[] = $hide_staffid;
			    	}
			    }
			    $hide_staff = implode(',',$hide_staff);
			    mysqli_query($dbc, "UPDATE `equipment_assignment` SET `hide_staff` = '$hide_staff' WHERE `equipment_assignmentid` = '$equipment_assignmentid'");
			}
		}
	} else if($blocktype == 'equipment') {
		$new_equipmentid = $_POST['new_equipmentid'];
		if($online) {
			mysqli_query($dbc, "UPDATE `equipment_assignment` SET `equipmentid` = '$new_equipmentid', `region` = '$region', `location` = '$location', `classification` = '$classification' WHERE `equipment_assignmentid` = '$equipment_assignmentid'");
		}
	}

    //Update ticket list retrieved from above to the new data
	$equipment_assignment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'"));
	$old_equipmentid = $equipmentid;
	$equipmentid = $equipment_assignment['equipmentid'];
	$teamid = $equipment_assignment['teamid'];
	$region = explode('*#*',$equipment_assignment['region'])[0];
	$location = explode('*#*',$equipment_assignment['location'])[0];
	$classification = explode('*#*',$equipment_assignment['classification'])[0];
	$start_date = $equipment_assignment['start_date'];
	$end_date = $equipment_assignment['end_date'];

	$updated_fields = ['equipmentid'=>$equipmentid, 'teamid'=>(empty($teamid) ? 0 : $teamid), 'region'=>$region, 'con_location'=>$location, 'classification'=>$classification];

    //Retrieve existing Tickets with this equipmentid and lands between the start and end dates
    $all_tickets_sql = "SELECT 'tickets' `ticket_table`, `ticketid` `t_id` FROM `tickets` WHERE (`equipmentid` = '$equipmentid' OR `equipmentid` = '$old_equipmentid') AND `equipmentid` > 0 AND DATE(`to_do_date`) >= '$start_date' AND (DATE(`to_do_date`) <= '$end_date' OR '$end_date' = '0000-00-00' OR '$end_date' = '') AND `deleted` = 0 UNION
    	SELECT 'ticket_schedule' `ticket_table`, `id` `t_id` FROM `ticket_schedule` WHERE (`equipmentid` = '$equipmentid' OR `equipmentid` = '$old_equipmentid') AND `equipmentid` > 0 AND DATE(`to_do_date`) >= '$start_date' AND (DATE(`to_do_date`) <= '$end_date' OR '$end_date' = '0000-00-00' OR '$end_date' = '') AND `deleted` = 0";
    $equipassign_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '$equipment_assignmentid' AND `deleted` = 0"),MYSQLI_ASSOC);
    $equipassign_hide_staff = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'"))['hide_staff']);
    $tickets = mysqli_fetch_all(mysqli_query($dbc, $all_tickets_sql),MYSQLI_ASSOC);

    $contact = [];
    foreach ($equipassign_staff as $staffid) {
        if(!in_array($staffid['contactid'], $contact) && !in_array($staffid['contactid'], $equipassign_hide_staff)) {
            $contact[] = $staffid['contactid'];
        }
    }
    $team_staff = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC);
    foreach ($team_staff as $staffid) {
        if(!in_array($staffid['contactid'], $contact) && !in_array($staffid['contactid'], $equipassign_hide_staff)) {
            $contact[] = $staffid['contactid'];
        }
    }
    $contact = implode(',',$contact);
    foreach ($tickets as $ticket) {
        if($online) {
        	if($ticket['ticket_table'] == 'tickets') {
				$ticket_details = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '{$ticket['t_id']}'"));
				mysqli_query($dbc, "UPDATE `tickets` SET `equipment_assignmentid` = '$equipment_assignmentid', `equipmentid` = '$equipmentid', `teamid` = '$teamid', `contactid` = ',$contact,', `region` = '$region', `con_location` = '$location', `classification` = '$classification' WHERE `ticketid` = '".$ticket['t_id']."'");
        	} else {
				$ticket_details = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `id` = '{$ticket['t_id']}'"));
				mysqli_query($dbc, "UPDATE `ticket_schedule` SET `equipment_assignmentid` = '$equipment_assignmentid', `equipmentid` = '$equipmentid', `teamid` = '$teamid', `contactid` = ',$contact,', `region` = '$region', `con_location` = '$location', `classification` = '$classification' WHERE `id` = '".$ticket['t_id']."'");
        	}
			mysqli_query($dbc, "UPDATE `tickets` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket_details['ticketid']."'");
			mysqli_query($dbc, "UPDATE `ticket_attached` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket_details['ticketid']."'");
			mysqli_query($dbc, "UPDATE `ticket_schedule` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket_details['ticketid']."'");
			mysqli_query($dbc, "UPDATE `ticket_comment` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket_details['ticketid']."'");

			//Record history
			$ticket_histories = [];
			foreach($updated_fields as $key => $updated_field) {
				if($ticket_details[$key] != $updated_field) {
					$ticket_histories[$key] = "$key updated to $updated_field";
				}
			}
			$ea_contacts = [];
			foreach(explode(',', $contact) as $ea_contact) {
				if($ea_contact > 0) {
					$ea_contacts[] = get_contact($dbc, $ea_contact);
				}
			}
			$ticket_histories['equipment_assignmentid'] = "equipment_assignmentid updated to $equipment_assignmentid (".implode(', ',$ea_contacts).")";
			if(!empty($ticket_histories)) {
				mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `src`, `description`) VALUES ('{$ticket['t_id']}','{$_SESSION['contactid']}','calendar','Row #{$ticket['t_id']} of {$ticket['ticket_table']} updated: ".implode(', ',$ticket_histories)."')");
			}
		}
    }
}
if($_GET['fill'] == 'equip_assign_remove_staff') {
	$staffid = $_POST['contactid'];
	$equipment_assignmentid = $_POST['equipment_assignid'];
	$date = $_POST['date'];

	$equipment_assignment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'"));
	if($equipment_assignment['start_date'] != $equipment_assignment['end_date']) {
		$old_equipment_assignmentid = $equipment_assignmentid;
		$equipment_assignment_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '$equipment_assignmentid' AND `deleted` = 0"),MYSQLI_ASSOC);
		if($online) {
			mysqli_query($dbc, "INSERT INTO `equipment_assignment` (`equipmentid`, `teamid`, `region`, `classification`, `location`, `notes`, `clientid`) SELECT `equipmentid`, `teamid`, `region`, `classification`, `location`, `notes`, `clientid` FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'");
			$equipment_assignmentid = mysqli_insert_id($dbc);
			mysqli_query($dbc, "UPDATE `equipment_assignment` SET `start_date` = '$date', `end_date` = '$date' WHERE `equipment_assignmentid` = '$equipment_assignmentid'");
		}
		foreach ($equipment_assignment_staff as $staff) {
			$staff_id = $staff['id'];
			if($online) {
				mysqli_query($dbc, "INSERT INTO `equipment_assignment_staff` (`contactid`, `contact_position`, `contractor`) SELECT `contactid`, `contact_position`, `contractor` FROM `equipment_assignment_staff` WHERE `id` = '$staff_id'");
				$equip_staffid = mysqli_insert_id($dbc);
				mysqli_query($dbc, "UPDATE `equipment_assignment_staff` SET `equipment_assignmentid` = '$equipment_assignmentid' WHERE `id` = '$equip_staffid'");
			}
		}

		//Add this day as a hidden day so it won't be displayed on the calendar (can clash in month view or if we changed the equipment for a single day, etc.)
		$hide_days = $equipment_assignment['hide_days'];
		$hide_days .= ','.$date;
		$hide_days = trim($hide_days, ',');
		if($online) {
			mysqli_query($dbc, "UPDATE `equipment_assignment` SET `hide_days` = '$hide_days' WHERE `equipment_assignmentid` = '$old_equipment_assignmentid'");
		}
	}

	//Set this staff to be hidden so they don't show up if they are part of the team assigned to the equipment assignment
	$equipment_assignment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'"));
	$equipmentid = $equipment_assignment['equipmentid'];
	$teamid = $equipment_assignment['teamid'];
	$region = explode('*#*',$equipment_assignment['region'])[0];
	$location = explode('*#*',$equipment_assignment['location'])[0];
	$classification = explode('*#*',$equipment_assignment['classification'])[0];
	$start_date = $equipment_assignment['start_date'];
	$end_date = $equipment_assignment['end_date'];
	$hide_staff = $equipment_assignment['hide_staff'];
	$hide_staff .= ','.$staffid;
	$hide_staff = trim($hide_staff, ',');
	if($online) {
		mysqli_query($dbc, "UPDATE `equipment_assignment` SET `hide_staff` = '$hide_staff' WHERE `equipment_assignmentid` = '$equipment_assignmentid'");
        $date_of_archival = date('Y-m-d');
		mysqli_query($dbc, "UPDATE `equipment_assignment_staff` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `contactid` = '$staffid' AND `equipment_assignmentid` = '$equipment_assignmentid'");
	}

    //Retrieve existing Tickets with this equipmentid and lands between the start and end dates
    // $all_tickets_sql = "SELECT * FROM `tickets` WHERE `equipmentid` = '$equipmentid' AND DATE(`to_do_date`) >= '$start_date' AND (DATE(`to_do_date`) <= '$end_date' OR '$end_date' = '0000-00-00' OR '$end_date' = '') AND `deleted` = 0";
    $all_tickets_sql = "SELECT 'tickets' `ticket_table`, `ticketid` `t_id` FROM `tickets` WHERE (`equipmentid` = '$equipmentid' OR `equipmentid` = '$old_equipmentid') AND `equipmentid` > 0 AND DATE(`to_do_date`) >= '$start_date' AND (DATE(`to_do_date`) <= '$end_date' OR '$end_date' = '0000-00-00' OR '$end_date' = '') AND `deleted` = 0 UNION
    	SELECT 'ticket_schedule' `ticket_table`, `id` `t_id` FROM `ticket_schedule` WHERE (`equipmentid` = '$equipmentid' OR `equipmentid` = '$old_equipmentid') AND `equipmentid` > 0 AND DATE(`to_do_date`) >= '$start_date' AND (DATE(`to_do_date`) <= '$end_date' OR '$end_date' = '0000-00-00' OR '$end_date' = '') AND `deleted` = 0";
    $equipassign_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '$equipment_assignmentid' AND `deleted` = 0"),MYSQLI_ASSOC);
    $equipassign_hide_staff = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'"))['hide_staff']);
    $tickets = mysqli_fetch_all(mysqli_query($dbc, $all_tickets_sql),MYSQLI_ASSOC);

    $contact = [];
    foreach ($equipassign_staff as $staffid) {
        if(!in_array($staffid['contactid'], $contact) && !in_array($staffid['contactid'], $equipassign_hide_staff)) {
            $contact[] = $staffid['contactid'];
        }
    }
    $team_staff = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC);
    foreach ($team_staff as $staffid) {
        if(!in_array($staffid['contactid'], $contact) && !in_array($staffid['contactid'], $equipassign_hide_staff)) {
            $contact[] = $staffid['contactid'];
        }
    }
    $contact = implode(',',$contact);
    foreach ($tickets as $ticket) {
        if($online) {
        	if($ticket['ticket_table'] == 'tickets') {
				$ticket_details = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '{$ticket['t_id']}'"));
				mysqli_query($dbc, "UPDATE `tickets` SET `equipment_assignmentid` = '$equipment_assignmentid', `equipmentid` = '$equipmentid', `teamid` = '$teamid', `contactid` = ',$contact,', `region` = '$region', `con_location` = '$location', `classification` = '$classification' WHERE `ticketid` = '".$ticket['t_id']."'");
        	} else {
				$ticket_details = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `id` = '{$ticket['t_id']}'"));
				mysqli_query($dbc, "UPDATE `ticket_schedule` SET `equipment_assignmentid` = '$equipment_assignmentid', `equipmentid` = '$equipmentid', `teamid` = '$teamid', `contactid` = ',$contact,', `region` = '$region', `con_location` = '$location', `classification` = '$classification' WHERE `id` = '".$ticket['t_id']."'");
        	}
			mysqli_query($dbc, "UPDATE `tickets` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket_details['ticketid']."'");
			mysqli_query($dbc, "UPDATE `ticket_attached` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket_details['ticketid']."'");
			mysqli_query($dbc, "UPDATE `ticket_schedule` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket_details['ticketid']."'");
			mysqli_query($dbc, "UPDATE `ticket_comment` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket_details['ticketid']."'");

			//Record history
			$ticket_histories = [];
			$ea_contacts = [];
			foreach(explode(',', $contact) as $ea_contact) {
				if($ea_contact > 0) {
					$ea_contacts[] = get_contact($dbc, $ea_contact);
				}
			}
			$ticket_histories['equipment_assignmentid'] = "equipment_assignmentid updated to $equipment_assignmentid (".implode(', ',$ea_contacts).")";
			if(!empty($ticket_histories)) {
				mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `src`, `description`) VALUES ('{$ticket['t_id']}','{$_SESSION['contactid']}','calendar','Row #{$ticket['t_id']} of {$ticket['ticket_table']} updated: ".implode(', ',$ticket_histories)."')");
			}
		}
    }
}
if($_GET['fill'] == 'team_assign_draggable') {
	$staff_id = $_POST['staffid'];
	$teamid = $_POST['teamid'];
	$date = $_POST['date'];

	$all_tickets = getTeamTickets($dbc, $date, $teamid);
	foreach($all_tickets as $ticket) {
		mysqli_query($dbc, "UPDATE `tickets` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket['ticketid']."'");
		mysqli_query($dbc, "UPDATE `ticket_attached` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket['ticketid']."'");
		mysqli_query($dbc, "UPDATE `ticket_schedule` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket['ticketid']."'");
		mysqli_query($dbc, "UPDATE `ticket_comment` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket['ticketid']."'");
		if(!in_array($staff_id,explode(',',$ticket['contactid']))) {
			$ticket_contacts = array_filter(explode(',',$ticket['contactid']));
			$ticket_contacts[] = $staff_id;
			$ticket_contacts = ','.implode(',',$ticket_contacts).',';
			mysqli_query($dbc, "UPDATE `tickets` SET `contactid` = '$ticket_contacts' WHERE `ticketid` = '".$ticket['ticketid']."'");
		}
		mysqli_query($dbc, "INSERT INTO `ticket_attached` (`src_table`,`item_id`,`ticketid`) SELECT 'Staff', '$staff_id', '".$ticket['ticketid']."' FROM (SELECT COUNT(*) rows FROM `ticket_attached` WHERE `src_table` = 'Staff' AND `item_id` = '$staff_id' AND `ticketid` = '".$ticket['ticketid']."') num WHERE num.rows=0");
	}
	$team = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `teams` WHERE `teamid` = '$teamid'"));
	if($team['start_date'] == $date && $team['end_date'] == $date) {
		mysqli_query($dbc, "INSERT INTO `teams_staff` (`teamid`, `contactid`) SELECT '$teamid', '$staff_id' FROM (SELECT COUNT(*) rows FROM `teams_staff` WHERE `contactid` = '$staff_id' AND `teamid` = '$teamid') num WHERE num.rows=0");
	} else {
		$team_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `deleted` = 0 AND `teamid` = '$teamid'"),MYSQLI_ASSOC);
		mysqli_query($dbc, "UPDATE `teams` SET `hide_days` = CONCAT(`hide_days`,',','$date') WHERE `teamid` = '$teamid'");

		mysqli_query($dbc, "INSERT INTO `teams` (`region`,`location`,`classification`,`start_date`,`end_date`,`notes`) SELECT `region`,`location`,`classification`,'$date','$date',`notes` FROM `teams` WHERE `teamid` = '$teamid'");
		$teamid = mysqli_insert_id($dbc);

		foreach($team_staff as $staff) {
			mysqli_query($dbc, "INSERT INTO `teams_staff` (`teamid`, `contactid`, `contact_position`) VALUES ('$teamid', '".$staff['contactid']."', '".$staff['contact_position']."')");
		}
		mysqli_query($dbc, "INSERT INTO `teams_staff` (`teamid`, `contactid`) SELECT '$teamid', '$staff_id' FROM (SELECT COUNT(*) rows FROM `teams_staff` WHERE `contactid` = '$staff_id' AND `teamid` = '$teamid') num WHERE num.rows=0");
	}
	echo $teamid;
}
if($_GET['fill'] == 'team_assign_remove_staff') {
	$contactid = $_POST['contactid'];
	$teamid = $_POST['teamid'];
	$date = $_POST['date'];

	$all_tickets = getTeamTickets($dbc, $date, $teamid);
	foreach($all_tickets as $ticket) {
		mysqli_query($dbc, "UPDATE `tickets` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket['ticketid']."'");
		mysqli_query($dbc, "UPDATE `ticket_attached` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket['ticketid']."'");
		mysqli_query($dbc, "UPDATE `ticket_schedule` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket['ticketid']."'");
		mysqli_query($dbc, "UPDATE `ticket_comment` SET `is_recurrence` = 0 WHERE `ticketid` = '".$ticket['ticketid']."'");
		if(in_array($staff_id,explode(',',$ticket['contactid']))) {
			$ticket_contacts = array_filter(explode(',',$ticket['contactid']));
			foreach($ticket_contacts as $key => $ticket_contact) {
				if($ticket_contact == $contactid) {
					unset($ticket_contacts[$key]);
				}
			}
			$ticket_contacts = ','.implode(',',$ticket_contacts).',';
			mysqli_query($dbc, "UPDATE `tickets` SET `contactid` = '$ticket_contacts' WHERE `ticketid` = '".$ticket['ticketid']."'");
		}
		mysqli_query($dbc, "UPDATE `ticket_attached` SET `deleted` = 1 WHERE `ticketid` = '".$ticket['ticketid']."' AND `src_table` = 'Staff' AND `item_id` = '$contactid'");
	}
	$team = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `teams` WHERE `teamid` = '$teamid'"));
	if($team['start_date'] == $date && $team['end_date'] == $date) {
		mysqli_query($dbc, "UPDATE `teams_staff` SET `deleted` = 1 WHERE `teamid` = '$teamid' AND `contactid` = '$contactid'");
	} else {
		$team_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `deleted` = 0 AND `teamid` = '$teamid'"),MYSQLI_ASSOC);
		mysqli_query($dbc, "UPDATE `teams` SET `hide_days` = CONCAT(`hide_days`,',','$date') WHERE `teamid` = '$teamid'");

		mysqli_query($dbc, "INSERT INTO `teams` (`region`,`location`,`classification`,`start_date`,`end_date`,`notes`) SELECT `region`,`location`,`classification`,'$date','$date',`notes` FROM `teams` WHERE `teamid` = '$teamid'");
		$teamid = mysqli_insert_id($dbc);

		foreach($team_staff as $staff) {
			if($staff['contactid'] != $contactid) {
				mysqli_query($dbc, "INSERT INTO `teams_staff` (`teamid`, `contactid`, `contact_position`) VALUES ('$teamid', '".$staff['contactid']."', '".$staff['contact_position']."')");
			}
		}
	}
	echo $teamid;
}
if($_GET['action'] == 'finish_edits') {
	$inserted = '';
	$updates = mysqli_query($dbc, "SELECT * FROM `calendar_offline_edits` WHERE `contactid`='$user'");
	while($update = mysqli_fetch_assoc($updates)) {
		$table = filter_var($update['table_name'],FILTER_SANITIZE_STRING);
		$id = filter_var($update['tableid'],FILTER_SANITIZE_STRING);
		$id_field = filter_var($update['table_field'],FILTER_SANITIZE_STRING);
		$field = filter_var($update['field_name'],FILTER_SANITIZE_STRING);
		$value = filter_var($update['value'],FILTER_SANITIZE_STRING);
		if($id == 0 && $inserted == $table) {
			$inserted = $table;
			mysqli_query($dbc, "INSERT INTO `$table` () VALUES ()");
			$id = mysqli_insert_id($dbc);
		} else if($id > 0) {
			$inserted = '';
		}
		mysqli_query($dbc, "UPDATE `$table` SET `$field`='$value' WHERE `id_field`='$id'");
	}
}
if(!$online) {
	foreach($offline_fields as $i => $field) {
		$table = filter_var($offline_tables[$i], FILTER_SANITIZE_STRING);
		$tableid = filter_var($offline_tableid[$i], FILTER_SANITIZE_STRING);
		$table_field = filter_var($table_field[$i], FILTER_SANITIZE_STRING);
		$value = filter_var(htmlentities($offline_values[$i]),FILTER_SANITIZE_STRING);
		if($table != '' && $field != '') {
			if($online) {
				mysqli_query($dbc, "INSERT INTO `calendar_offline_edits` (`contactid`, `table_name`, `tableid`, `table_field`, `field_name`, `value`) VALUES ('$user', '$table', '$tableid', '$table_field', '$field', '$value')");
			}
		}
	}
}
if($_GET['fill'] == 'retrieve_equipment_info') {
	$equipmentid = $_GET['equipmentid'];
	$equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '$equipmentid'"));
	$region = explode('*#*',$equipment['region'])[0];
	$location = explode('*#*',$equipment['location'])[0];
	$classification = explode('*#*',$equipment['classification'])[0];
	echo $region.'*#*'.$location.'*#*'.$classification;
}
if($_GET['fill'] == 'export_shifts') {
	set_time_limit(0);
	$today_date = date('Y-m-d_h-i-s-a', time());
	if(!file_exists('download')) {
		mkdir('download', 0777, true);
	}
	$FileName = "download/shifts_export_".$today_date.".csv";
	$file = fopen($FileName, "w");

	$field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_shifts`"));
	$enabled_fields = ','.$field_config['enabled_fields'].',';

	$csv_headers = ['shiftid','contactid'];
    if (strpos($enabled_fields, ',security_level,') !== FALSE) {
    	$csv_headers[] = 'security_level';
    }
	if(!empty($field_config['contact_category'])) {
		$csv_headers[] = 'clientid';
	}
    if (strpos($enabled_fields, ',heading,') !== FALSE) {
    	$csv_headers[] = 'heading';
    }
	if (strpos($enabled_fields, ',dates,') !== FALSE) {
		$csv_headers[] = 'startdate';
		$csv_headers[] = 'enddate';
	}
	if (strpos($enabled_fields, ',time,') !== FALSE) {
		$csv_headers[] = 'starttime';
		$csv_headers[] = 'endtime';
	}
	if (strpos($enabled_fields, ',dayoff_type,') !== FALSE) {
		$csv_headers[] = 'dayoff_type';
	}
	if (strpos($enabled_fields, ',repeat_days,') !== FALSE) {
		$csv_headers[] = 'repeat_days';
		$csv_headers[] = 'repeat_type';
		$csv_headers[] = 'repeat_interval';
	}
	if (strpos($enabled_fields, ',breaks,') !== FALSE) {
		$csv_headers[] = 'break_starttime';
		$csv_headers[] = 'break_endtime';
	}
	if (strpos($enabled_fields, ',notes,') !== FALSE) {
		$csv_headers[] = 'notes';
	}
	fputcsv($file, $csv_headers);

	if(!isset($_GET['empty'])) {
		if(!empty($_GET['contactid'])) {
			$staff_query = " AND `contactid` = '".$_GET['contactid']."'";
		} else {
			$staff_query = '';
		}
		$shift_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts_shifts` WHERE `deleted` = 0 $staff_query"),MYSQLI_ASSOC);
		foreach ($shift_list as $shift) {
			$csv = [];
			foreach ($csv_headers as $csv_header) {
				$csv[] = $shift[$csv_header];
			}
			fputcsv($file, $csv);
		}
	}
	fclose($file);
	echo WEBSITE_URL.'/Calendar/'.$FileName;
}
if($_GET['fill'] == 'check_call_before_booking') {
	$call_before_booking = [];
	$bookings = json_decode($_POST['bookings']);
	foreach ($bookings as $booking) {
		$booking = json_decode(json_encode($booking), true);
		$staff = $booking['staff'];
		$startdate = explode(' ', $booking['startdate'])[0];
		$enddate = explode(' ', $booking['enddate'])[0];
		$staff_call_needed = false;

		if(!empty($startdate) && strtotime($startdate) >= strtotime(date('Y-m-d'))) {
			if(empty($enddate)) {
				$enddate = $startdate;
			}
			for($i = strtotime($startdate); ($i <= strtotime($enddate) && !$staff_call_needed); $i = strtotime(date('Y-m-d', $i).' + 1 day')) {
				$shifts = checkShiftIntervals($dbc, $staff, date('l', $i), date('Y-m-d', $i));
				foreach ($shifts as $shift) {
					if($shift['availability'] == 'Call Before Booking') {
						$call_before_booking[] = $staff;
						$staff_call_needed = true;
						break;
					}
				}
			}
		}
	}
	$call_before_booking = array_unique(array_filter($call_before_booking));
	$alert_text = '';
	foreach ($call_before_booking as $staff) {
		$home_phone = get_contact($dbc, $staff, 'home_phone');
		$cell_phone = get_contact($dbc, $staff, 'cell_phone');
		$office_phone = get_contact($dbc, $staff, 'office_phone');
		$alert_text .= "Please call ".get_contact($dbc, $staff)." before booking.\n";
		if(!empty($home_phone)) {
			$alert_text .= "     H: ".$home_phone."\n";
		}
		if(!empty($cell_phone)) {
			$alert_text .= "     C: ".$cell_phone."\n";
		}
		if(!empty($office_phone)) {
			$alert_text .= "     O: ".$office_phone."\n";
		}
		$alert_text .= "\n";
	}
	echo $alert_text;
}
if($_GET['fill'] == 'retrieve_classification_users') {
	$classification = $_POST['classification'];
	if(!empty($classification)) {
		$active_users = [];
		$class_users = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE CONCAT(',',`classification`,',') LIKE '%,$classification,%' AND `deleted` = 0"),MYSQLI_ASSOC));
		$class_users = implode(',',$class_users);
		$past_15_mins = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'- 15 minutes'));
		$active_users_sql = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts_last_active` WHERE `contactid` IN ($class_users) AND `last_active` >= '$past_15_mins' ORDER BY `last_active` DESC"),MYSQLI_ASSOC);
		foreach ($active_users_sql as $active_user_sql) {
			$active_users[$active_user_sql['contactid']] = (!empty(get_client($dbc, $active_user_sql['contactid'])) ? get_client($dbc, $active_user_sql['contactid']) : get_contact($dbc, $active_user_sql['contactid']));
		}
		$class_html = '<span><b>Active Users:</b><br>';
		foreach ($active_users as $active_userid => $active_user) {
			$class_html .= profile_id($dbc, $active_userid, false).' '.$active_user.'<br>';
		}
		$class_html .= '</span>';
		echo count($active_users).'*#*'.$class_html;
	} else {
		echo '0*#*';
	}
}
if($_GET['fill'] == 'check_ticket_last_updated') {
	$ticketid = $_POST['ticketid'];
	$ticket_table = $_POST['ticket_table'];
	$ticket_scheduleid = $_POST['ticket_scheduleid'];
	$timestamp = convert_timestamp_mysql($dbc, $_POST['timestamp']);
	if($ticket_table == 'ticket_schedule') {
		$last_updated = mysqli_fetch_array(mysqli_query($dbc, "SELECT `last_updated_time` FROM `ticket_schedule` WHERE `id` = '$ticket_scheduleid'"))['last_updated_time'];
	} else {
		$last_updated = mysqli_fetch_array(mysqli_query($dbc, "SELECT `last_updated_time` FROM `tickets` WHERE `ticketid` = '$ticketid'"))['last_updated_time'];
	}
	if(strtotime($last_updated) - 30 > strtotime($timestamp)) {
		echo 1;
	} else {
		echo 0;
	}
}
if($_GET['fill'] == 'get_calendar_dates') {
	$config_type = $_POST['config_type'];
	$calendar_start = $_POST['date'];
	if($calendar_start == '') {
		$calendar_start = date('Y-m-d');
	} else {
		$calendar_start = date('Y-m-d', strtotime($calendar_start));
	}

	//If next or previous is clicked, find
	if($_POST['type'] == 'next') {
		if($_POST['view'] == 'monthly') {
			$calendar_start = date('Y-m-d', strtotime(date('Y-m-01',strtotime($calendar_start)).'+ 1 month'));
		} else if($_POST['view'] == 'weekly') {
			$calendar_start = date('Y-m-d', strtotime($calendar_start.'+ 1 week'));
		} else {
			$calendar_start = date('Y-m-d', strtotime($calendar_start.'+ 1 day'));
		}
	} else if($_POST['type'] == 'prev') {
		if($_POST['view'] == 'monthly') {
			$calendar_start = date('Y-m-d', strtotime(date('Y-m-01',strtotime($calendar_start)).'- 1 month'));
		} else if($_POST['view'] == 'weekly') {
			$calendar_start = date('Y-m-d', strtotime($calendar_start.'- 1 week'));
		} else {
			$calendar_start = date('Y-m-d', strtotime($calendar_start.'- 1 day'));
		}
	}

	//New values
	if($_POST['view'] == 'monthly') {
	    $monthly_days = explode(',', get_config($dbc, $config_type.'_monthly_days'));
		$search_month = date('F', strtotime($calendar_start));
		$search_year = date('Y', strtotime($calendar_start));
		$calendar_month = date("n", strtotime($search_month));
		$calendar_year = $search_year;
		$days_in_month = date('t',mktime(0,0,0,$calendar_month,1,$calendar_year));

		for($list_day = 1; $list_day <= $days_in_month; $list_day++) {
		    $today_date = $list_day.'-'.$calendar_month.'-'.$calendar_year;
		    $new_today_date = date_format(date_create_from_format('j-n-Y', $today_date), 'Y-m-d');
		    $day_of_week = date('l', strtotime($new_today_date));

		    if(in_array($day_of_week, $monthly_days)) {
			    $calendar_dates[] = $new_today_date;
			}
		}

        $date_string = date('F Y', strtotime($calendar_start));
	} else if($_POST['view'] == 'weekly') {
		$weekly_start = get_config($dbc, $config_type.'_weekly_start');
	    $weekly_days = explode(',', get_config($dbc, $config_type.'_weekly_days'));
	    if($weekly_start == 'Sunday') {
	        $weekly_start = 1;
	    } else {
	        $weekly_start = 0;
	    }
	    $day = date('w', strtotime($calendar_start));
	    $week_start_date = date('F j', strtotime($calendar_start.' -'.($day - 1 + $weekly_start).' days'));
	    $week_end_date = date('F j, Y', strtotime($calendar_start.' -'.($day - 7 + $weekly_start).' days'));
	    $date_string = date('M d', strtotime($week_start_date)).' - '.date('M d, Y', strtotime($week_end_date));

		$day = date('w', strtotime($calendar_start));
		$week_start_date_check = date('Y-m-d', strtotime($calendar_start.' -'.($day - 1 + $weekly_start).' days'));
		$week_end_date_check = date('Y-m-d', strtotime($calendar_start.' -'.($day - 7 + $weekly_start).' days'));

		$calendar_dates = [];
		for($i = 1; $i <= 7; $i++) {
		    $calendar_date = date('Y-m-d', strtotime($calendar_start.' -'.($day - $i + $weekly_start).' days'));
		    $day_of_week = date('l', strtotime($calendar_date));
		    if(in_array($day_of_week, $weekly_days)) {
		    	$calendar_dates[] = $calendar_date;
			}
		}
	} else {
        $date_string = date('F d, Y', strtotime($calendar_start));
    	$calendar_dates = [$calendar_start];
	}

	echo json_encode([$calendar_start, $date_string, $calendar_dates]);
}
if($_GET['fill'] == 'check_shift_conflicts') {
	$shiftid = $_POST['shiftid'];
	$contact_id = $_POST['contactid'];
	$startdate = $_POST['startdate'];
	$enddate = $_POST['enddate'];
	$starttime = $_POST['starttime'];
	$endtime = $_POST['endtime'];
	$repeat_type = $_POST['repeat_type'];
	$repeat_days = $_POST['repeat_days'];
	$interval = $_POST['repeat_interval'];
	if(!empty($shiftid)) {
		$hide_days = array_filter(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts_shifts` WHERE `shiftid` = '$shiftid'"))['hide_days']));
	}

	if(!empty($starttime) && !empty($endtime)) {
		$check_startdate = (empty($startdate) || strtotime(date('Y-m-d')) > strtotime($startdate)) ? date('Y-m-d') : $startdate;
		$check_enddate =  empty($enddate) ? date(strtotime(date('Y-m-d').' + 1 month')) : $enddate;

		for($current_date = $check_startdate; strtotime($current_date) <= strtotime($check_enddate); $current_date = date('Y-m-d', strtotime($current_date.' + 1 day'))) {
			$is_shift = false;
			if(!in_array($current_date, $hide_days)) {
				switch($repeat_type) {
					case 'weekly':
						$repeat_type = 'W';
						$start_date = date('Y-m-d', strtotime('next Sunday -1 week', strtotime($startdate)));
						$start_date = new DateTime($start_date);
						$start_date->modify($day_of_week);
						$end_date = new DateTime(date('Y-m-d', strtotime($calendar_date.' + 1 week')));
						break;
					case 'daily':
						$repeat_type = 'D';
						$start_date = date('Y-m-d', strtotime($startdate));
						$start_date = new DateTime($start_date);
						$end_date = new DateTime(date('Y-m-d', strtotime($calendar_date.' + 1 day')));
						break;
					case 'monthly':
						$repeat_type = 'M';
						$start_date = date('Y-m-d', strtotime($startdate));
						$start_date = new DateTime($start_date);
						$end_date = new DateTime(date('Y-m-d', strtotime($calendar_date.' + 1 month')));
						break;
				}
				if($interval > 1) {
					$interval = new DateInterval("P{$interval}{$repeat_type}");
					$period = new DatePeriod($start_date, $interval, $end_date);
					foreach($period as $period_date) {
						if (date('Y-m-d', strtotime($calendar_date)) == $period_date->format('Y-m-d')) {
							$is_shift = true;
						}
					}
				} else {
					$is_shift = true;
				}
				if($is_shift) {
					$shift_conflicts = getShiftConflicts($dbc, $contact_id, $current_date, $starttime, $endtime, $shiftid);
					if(!empty($shift_conflicts)) {
						echo 1;
					}
				}
			}
		}
	}
}
if($_GET['fill'] == 'shiftsDeleteLogo') {
    $logo = $_POST['logo'];

    if($logo == 'header') {
        mysqli_query($dbc, "UPDATE `field_config_contacts_shifts_pdf` SET `header_logo` = ''");
    } else if($logo == 'footer') {
        mysqli_query($dbc, "UPDATE `field_config_contacts_shifts_pdf` SET `footer_logo` = ''");
    }
}
if($_GET['fill'] == 'get_ticket_staff') {
	$ticketid = $_POST['ticketid'];
	$ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
	$contactids = array_unique(array_filter(explode(',',$ticket['contactid'])));
	$assigned_staff = [];
	foreach($contactids as $contactid) {
		$assigned_staff[] = get_contact($dbc, $contactid);
	}
	$assigned_staff = implode('<br>',$assigned_staff);
	echo '<b>Assigned Staff:</b><br>'.$assigned_staff;
}
if($_GET['fill'] == 'get_client_staff') {
	$type = $_POST['type'];
	$clientid = $_POST['clientid'];
	$calendar_dates = json_decode($_POST['calendar_dates']);
	$start_date = $calendar_dates[0];
	$end_date = array_pop($calendar_dates);

	if($type == 'shift') {
		$staffids = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `contactid` SEPARATOR ',') `staffids` FROM `contacts_shifts` WHERE `deleted` = 0 AND `clientid` = '$clientid' AND ((`startdate` >= '$start_date' AND `startdate` <= '$end_date') OR (`enddate` >= '$end_date' AND `enddate` <= '$end_date') OR '$start_date' BETWEEN `startdate` AND IFNULL(NULLIF(`enddate`,'0000-00-00'),'9999-12-31') OR '$end_date' BETWEEN `startdate` AND IFNULL(NULLIF(`enddate`,'0000-00-00'),'9999-12-31'))"))['staffids'];
		echo json_encode(explode(',',$staffids));
	} else if($type == 'appt') {
		$staffids = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `therapistsid` SEPARATOR ',') `staffids` FROM `booking` WHERE `deleted` = 0 AND `patientid` = '$clientid' AND (DATE(`appoint_date`) BETWEEN '$start_date' AND '$end_date' OR DATE(`end_appoint_date`) BETWEEN '$start_date' AND '$end_date')"))['staffids'];
		echo json_encode(explode(',',$staffids));
	}
}
if($_GET['fill'] == 'get_equipment_assignment_block') {
	$equipmentid = $_POST['equipmentid'];
	$date = $_POST['date'];
	$view = $_POST['view'];

	echo getEquipmentAssignmentBlock($dbc, $equipmentid, $view, $date);
}
if($_GET['fill'] == 'get_ticket_scheduled_time') {
	$ticket_table = $_POST['ticket_table'];
	$ticketid = $_POST['ticketid'];
	$ticket_scheduleid = $_POST['ticket_scheduleid'];

	if($ticket_table == 'ticket_schedule') {
		$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `id` = '$ticket_scheduleid'"));
	} else {
		$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
	}
	echo $ticket['to_do_date'].'#*#'.date('H:i a',strtotime(date('Y-m-d').' '.$ticket['to_do_start_time'])).'#*#'.date('H:i a',strtotime(date('Y-m-d').' '.$ticket['to_do_end_time']));
}
if($_GET['fill'] == 'update_ticket_scheduled_time') {
	$ticket_table = $_POST['ticket_table'];
	$id = $_POST['id'];
    if($ticket_table == 'ticket_schedule') {
        $id_field = 'id';
    } else {
        $id_field = 'ticketid';
    }

	$query = [];
	$history = [];
	if(!empty($_POST['to_do_date'])) {
		$to_do_date = $_POST['to_do_date'];
		$query[] = "`to_do_date` = '$to_do_date'";
		$history[] = "Scheduled Date to ".$to_do_date;
	}
	if(!empty($_POST['to_do_start_time'])) {
		$to_do_start_time = date('H:i:s', strtotime(date('Y-m-d').' '.$_POST['to_do_start_time']));
		$query[] = "`to_do_start_time` = '$to_do_start_time'";
		$history[] = "Sheduled Start Time to ".$_POST['to_do_start_time'];
	}
	if(!empty($_POST['to_do_end_time'])) {
		$to_do_end_time = date('H:i:s', strtotime(date('Y-m-d').' '.$_POST['to_do_end_time']));
		$query[] = "`to_do_end_time` = '$to_do_end_time'";
		$history[] = "Sheduled End Time to ".$to_do_end_time;
	} else if(!empty($_POST['to_do_start_time'])) {
        $to_do_end_time = date('H:i:s', strtotime($to_do_start_time) + strtotime(get_field_value('to_do_end_time',$ticket_table,$id_field,$id)) - strtotime(get_field_value('to_do_start_time',$ticket_table,$id_field,$id)));
		$query[] = "`to_do_end_time` = '$to_do_end_time'";
		$history[] = "Sheduled End Time to ".$to_do_end_time;
    }

	if($id > 0 && !empty($query)) {
		$query = implode(', ', $query);
		$history = htmlentities(get_contact($dbc, $_SESSION['contactid']).' updated '.implode(', ',$history).'<br>');
		if($ticket_table == 'ticket_schedule') {
			$ticketid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `ticketid` FROM `ticket_schedule` WHERE `id` = '$id'"))['ticketid'];
		} else {
			$ticketid = $id;
		}
		mysqli_query($dbc, "UPDATE `tickets` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
		mysqli_query($dbc, "UPDATE `ticket_attached` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
		mysqli_query($dbc, "UPDATE `ticket_schedule` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
		mysqli_query($dbc, "UPDATE `ticket_comment` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
		mysqli_query($dbc, "UPDATE `$ticket_table` SET $query, `calendar_history` = CONCAT(IFNULL(`calendar_history`,''),'$history') WHERE `$id_field` = '$id'");
	}
}
if($_GET['fill'] == 'quick_add_shift') {
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	$staff = filter_var($_POST['staff'],FILTER_SANITIZE_STRING);
	$client = filter_var($_POST['client'],FILTER_SANITIZE_STRING);
	$time = filter_var($_POST['time'],FILTER_SANITIZE_STRING);

	$time = explode('-', $time);
	$time[0] = trim($time[0]);
	$time[1] = trim($time[1]);
	$starttime =  date('h:i a', strtotime($time[0]));
	$endtime = date('h:i a', strtotime($time[1]));

	if(strtotime($starttime) > strtotime($endtime)) {
		mysqli_query($dbc, "INSERT INTO `contacts_shifts` (`contactid`, `clientid`, `startdate`, `enddate`, `starttime`, `endtime`) VALUES ('$staff', '$client', '$date', '$date', '$starttime', '11:59 pm')");
		$date = date('Y-m-d', strtotime($date.' + 1 day'));
		mysqli_query($dbc, "INSERT INTO `contacts_shifts` (`contactid`, `clientid`, `startdate`, `enddate`, `starttime`, `endtime`) VALUES ('$staff', '$client', '$date', '$date', '12:00 am', '$endtime')");
	} else {
		mysqli_query($dbc, "INSERT INTO `contacts_shifts` (`contactid`, `clientid`, `startdate`, `enddate`, `starttime`, `endtime`) VALUES ('$staff', '$client', '$date', '$date', '$starttime', '$endtime')");
	}
}
if($_GET['fill'] == 'archive_team') {
	$teamid = $_GET['teamid'];
	mysqli_query($dbc, "UPDATE `teams` SET `deleted` = 1 WHERE `teamid` = '$teamid'");
}
if($_GET['fill'] == 'get_ticket_client_frequency') {
	$staff = json_decode($_POST['staff']);
	$clients = json_decode($_POST['clients']);
	$client_freq = [];

	foreach($staff as $staffid) {
		$html = '';
		foreach($clients as $clientid) {
			$count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) num_rows FROM `tickets` WHERE (CONCAT(',',`clientid`,',') LIKE ',%$clientid,%' OR `businessid` = '$clientid') AND CONCAT(',',`contactid`,',') LIKE '%,$staffid,%'"))['num_rows'];
			$html .= '<li>'.(!empty(get_client($dbc, $clientid)) ? get_client($dbc, $clientid) : get_contact($dbc, $clientid)).': '.$count.'</li>';
		}
		$client_freq[] = ['staffid' => $staffid, 'html' => $html];
	}
	echo json_encode($client_freq);
}
if($_GET['fill'] == 'book_client_ticket') {
	$clientid = $_POST['clientid'];
	$contact = $_POST['contact'];
	$blocktype = $_POST['blocktype'];
	$start_time = $_POST['start_time'];
	$start_date = $_POST['start_date'];
	$ticket_type = get_config($dbc, 'default_ticket_type');
	$is_recurring = $_POST['is_recurring'];
	if($is_recurring == 1) {
		$status = get_config($dbc, 'ticket_recurring_status');
	} else {
		$status = get_config($dbc, 'ticket_default_status');
	}

	if($blocktype == 'team') {
		$team_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '".$contact."' AND `deleted` = 0"),MYSQLI_ASSOC);
		$contact = [];
		foreach ($team_staff as $team_contact) {
			if(strtolower(get_contact($dbc, $team_contact['contactid'], 'category')) == 'staff') {
				$contact[] = $team_contact['contactid'];
			}
		}
		$contact = implode(',',array_filter(array_unique($contact)));
	}
	$contact = ','.$contact.',';
	$main_siteid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `main_siteid` FROM `contacts` WHERE `contactid` = '$clientid'"))['main_siteid'];
	$siteid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `category`='".SITES_CAT."' AND `businessid`='$clientid' AND `deleted` = 0 AND `contactid` = '$main_siteid' UNION SELECT `contactid` FROM `contacts` WHERE `category`='".SITES_CAT."' AND `businessid`='$clientid' AND `deleted` = 0 AND `contactid` != '$main_siteid'"))['contactid'];

	mysqli_query($dbc, "INSERT INTO `tickets` (`ticket_type`, `clientid`, `contactid`, `to_do_date`, `to_do_start_time`, `status`, `siteid`) VALUES ('$ticket_type', '$clientid', '$contact', '$start_date', '$start_time', '$status', '$siteid')");
	$ticketid = mysqli_insert_id($dbc);

	foreach(array_filter(array_unique(explode(',', $contact))) as $contact_id) {
		mysqli_query($dbc, "INSERT INTO `ticket_attached` (`ticketid`, `src_table`, `item_id`) VALUES ('$ticketid', 'Staff', '$contact_id')");
	}
	echo $ticketid;
}
if($_GET['fill'] == 'check_ticket_booking_conflicts') {
	$shiftid = $_POST['shiftid'];
	$contact_id = $_POST['contactid'];
	$startdate = $_POST['startdate'];
	$enddate = $_POST['enddate'];
	$repeat_type = $_POST['repeat_type'];
	$repeat_days = $_POST['repeat_days'];
	$interval = $_POST['repeat_interval'];
	if(!empty($shiftid)) {
		$hide_days = array_filter(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts_shifts` WHERE `shiftid` = '$shiftid'"))['hide_days']));
	}

	$conflicts = checkTicketBookingConflicts($dbc, $contact_id, $startdate, $enddate, $repeat_type, $repeat_days, $interval, $hide_days);
	$ticket_conflicts = $conflicts['ticket'];
	$booking_conflicts = $conflicts['booking'];

	$conflict_message = [];
	if(!empty($ticket_conflicts)) {
		$conflict_message[] = "The following ".TICKET_TILE." land on the same days as your Days Off:\n".implode("\n", $ticket_conflicts)."\n";
	}
	if(!empty($booking_conflicts)) {
		$conflict_message[] = "The following Appointments land on the same days as your Days Off:\n".implode("\n", $booking_conflicts)."\n";
	}
	$conflict_message = implode("\n", $conflict_message);
	echo $conflict_message;
}
if($_GET['fill'] == 'unassign_ticket_booking_conflicts') {
	$contact_id = $_POST['contactid'];
	$startdate = $_POST['startdate'];
	$enddate = $_POST['enddate'];

	$conflicts = checkTicketBookingConflicts($dbc, $contact_id, $startdate, $enddate, '', '', '', '');

	foreach($conflicts['ticket'] as $ticketid => $ticket_label) {
		$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
		$internal_qa_contactid = explode(',', $ticket['internal_qa_contactid']);
		foreach($internal_qa_contactid as $key => $contactid) {
			if($contact_id == $contactid) {
				unset($internal_qa_contactid[$key]);
				$new_internal_qa_contactid = implode(',', $internal_qa_contactid);
				mysqli_query($dbc, "UPDATE `tickets` SET `internal_qa_contactid` = '$new_internal_qa_contactid' WHERE `ticketid` = '$ticketid'");
			}
		}
		$deliverable_contactid = explode(',', $ticket['deliverable_contactid']);
		foreach($deliverable_contactid as $key => $contactid) {
			if($contact_id == $contactid) {
				unset($deliverable_contactid[$key]);
				$new_deliverable_contactid = implode(',', $deliverable_contactid);
				mysqli_query($dbc, "UPDATE `tickets` SET `deliverable_contactid` = '$new_deliverable_contactid' WHERE `ticketid` = '$ticketid'");
			}
		}
		$ticket_contactid = explode(',', $ticket['contactid']);
		foreach($ticket_contactid as $key => $contactid) {
			if($contact_id == $contactid) {
				unset($ticket_contactid[$key]);
				$new_ticket_contactid = implode(',', $ticket_contactid);
				mysqli_query($dbc, "UPDATE `tickets` SET `contactid` = '$new_ticket_contactid' WHERE `ticketid` = '$ticketid'");
			}
		}
		mysqli_query($dbc, "UPDATE `ticket_attached` SET `deleted` = 1 WHERE `ticketid` = '$ticketid' AND `src_table` LIKE 'Staff%' AND `item_id` = '$contact_id'");
	}

	foreach($conflicts['booking'] as $bookingid => $booking_label) {
		$booking = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `booking` WHERE `bookingid` = '$bookingid'"));
		$therapistsid = explode('*#*', $booking['therapistsid']);
		$appoint_date = explode('*#*', $booking['appoint_date']);
		$end_appoint_date = explode('*#*', $booking['end_appoint_date']);
		$type = explode('*#*', $booking['type']);
		$serviceid = explode('*#*', $booking['serviceid']);
		foreach($therapistsid as $key => $staffid) {
			if($contact_id == $staffid) {
				unset($therapistsid[$key]);
				unset($appoint_date[$key]);
				unset($end_appoint_date[$key]);
				unset($type[$key]);
				unset($serviceid[$key]);
				$new_therapistsid = implode('*#*', $therapistsid);
				$new_appoint_date = implode('*#*', $appoint_date);
				$new_end_appoint_date = implode('*#*', $end_appoint_date);
				$new_type = implode('*#*', $type);
				$new_serviceid = implode('*#*', $serviceid);
				mysqli_query($dbc, "UPDATE `booking` SET `therapistsid` = '$new_ticket_contactid', `appoint_date` = '$new_appoint_date', `end_appoint_date` = '$new_end_appoint_date', `type` = '$new_type', `serviceid` = '$new_serviceid' WHERE `bookingid` = '$bookingid'");
			}
		}
	}
}
if($_GET['fill'] == 'delete_logo') {
	set_config($dbc, $_POST['logo'], '');
}
if($_GET['fill'] == 'set_last_active') {
	$type = $_POST['type'];
	$contactid = $_POST['contactid'];
    $last_active = date('Y-m-d H:i:s');
	if($contactid > 0) {
	    mysqli_query($dbc, "INSERT INTO `calendar_last_active` (`type`, `contactid`) SELECT '$type', '$contactid' FROM (SELECT COUNT(*) rows FROM `calendar_last_active` WHERE `contactid` = '$contactid' AND `type` = '$type') num WHERE num.rows = 0");
	    mysqli_query($dbc, "UPDATE `calendar_last_active` SET `last_active` = '$last_active' WHERE `contactid` = '$contactid' AND `type` ='$type'");
	}
}
if($_GET['fill'] == 'get_last_active') {
	$type = $_POST['type'];
	$past_15_mins = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').'- 15 minutes'));
	$active_users_sql = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `calendar_last_active` WHERE `type` = '$type' AND `last_active` >= '$past_15_mins' ORDER BY `last_active` DESC"),MYSQLI_ASSOC);
	foreach ($active_users_sql as $active_user_sql) {
		$active_users[$active_user_sql['contactid']] = (!empty(get_client($dbc, $active_user_sql['contactid'])) ? get_client($dbc, $active_user_sql['contactid']) : get_contact($dbc, $active_user_sql['contactid']));
	}
	$active_html = '<span><b>Active Users:</b><br>';
	foreach ($active_users as $active_userid => $active_user) {
		$active_html .= profile_id($dbc, $active_userid, false).' '.$active_user.'<br>';
	}
	if(empty($active_users)) {
		$active_html .= 'No Active Users';
	}
	$active_html .= '</span>';
	echo count($active_users).'*#*'.$active_html;
}
if($_GET['action'] == 'removeSchedule') {
    $ticketid = filter_var($_POST['ticketid'],FILTER_SANITIZE_STRING);
    $stopid = filter_var($_POST['stopid'],FILTER_SANITIZE_STRING);
    if($stopid > 0) {
        $dbc->query("UPDATE `ticket_schedule` SET `equipmentid`='0',`contactid`='0' WHERE `ticketid`='$ticketid' AND `id`='$stopid'");
    } else {
        $dbc->query("UPDATE `tickets` SET `equipmentid`='0',`contactid`='0' WHERE `ticketid`='$ticketid'");
    }
}
?>