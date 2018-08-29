<?php $project_history = '';

$ticket_type = filter_var($_POST['ticket_type'],FILTER_SANITIZE_STRING);
$businessid = filter_var($_POST['businessid'],FILTER_SANITIZE_STRING);
$clientid = filter_var(implode(',',$_POST['clientid']),FILTER_SANITIZE_STRING);

$service_type = filter_var($_POST['service_type'],FILTER_SANITIZE_STRING);

$service = filter_var($_POST['service'],FILTER_SANITIZE_STRING);
$sub_heading = filter_var($_POST['sub_heading'],FILTER_SANITIZE_STRING);
$heading = filter_var($_POST['heading'],FILTER_SANITIZE_STRING);
$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
$created_date = date('Y-m-d');
$created_by = $_SESSION['contactid'];
$contactid = ','.implode(',',$_POST['contactid']).',';

$a_work = htmlentities($_POST['assign_work']);
$assign_work = filter_var($a_work,FILTER_SANITIZE_STRING);

$to_do_date = $_POST['to_do_date'];

$to_do_end_date = $_POST['to_do_end_date'];
$internal_qa_contactid = ','.implode(',',$_POST['internal_qa_contactid']).',';
$deliverable_contactid = ','.implode(',',$_POST['deliverable_contactid']).',';

$internal_qa_date = $_POST['internal_qa_date'];
$deliverable_date = $_POST['deliverable_date'];

$to_do_start_time = filter_var($_POST['to_do_start_time'],FILTER_SANITIZE_STRING);
$to_do_end_time = filter_var($_POST['to_do_end_time'],FILTER_SANITIZE_STRING);
$internal_qa_start_time = filter_var($_POST['internal_qa_start_time'],FILTER_SANITIZE_STRING);
$internal_qa_end_time = filter_var($_POST['internal_qa_end_time'],FILTER_SANITIZE_STRING);
$deliverable_start_time = filter_var($_POST['deliverable_start_time'],FILTER_SANITIZE_STRING);
$deliverable_end_time = filter_var($_POST['deliverable_end_time'],FILTER_SANITIZE_STRING);

$max_time = $_POST['max_time_hour'].':'.$_POST['max_time_minute'].':00';
$max_qa_time = $_POST['max_qa_hour'].':'.$_POST['max_qa_minute'].':00';
$spent_time = $_POST['spent_time'];

$total_days = $_POST['total_days'];
$projectid = $_POST['projectid'];
$client_projectid = '';
if(substr($projectid,0,1) == 'C') {
	$client_projectid = substr($projectid,1);
	$projectid = '';
}
$project_path = get_project($dbc, $projectid, 'project_path');
$piece_work = filter_var($_POST['piece_work'],FILTER_SANITIZE_STRING);

if($piece_work != '') {
		$project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(projectid) `num`, projectid FROM `project` WHERE `project_name` = 'Piece Work Project' AND `deleted` = 0"));
		if($project['num'] > 0) {
            $projectid = $project['projectid'];
        } else {
			$sql = "INSERT INTO `project` (`project_name`,`status`) VALUES ('Piece Work Project','Active Project')";
		    $result_insert_ticket = mysqli_query($dbc, $sql);
            $projectid = mysqli_insert_id($dbc);
        }
}

$status = $_POST['status'];

$milestone_timeline = filter_var($_POST['milestone_timeline'],FILTER_SANITIZE_STRING);

if(empty($_POST['ticketid'])) {
	if($_POST['contactid'] == '' || $_POST['add_to_helpdesk'] == '1') {
		$name = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
		$document = implode('*#*',$_FILES["upload_document"]["name"]);
		$priority = 'Medium';
		$company_name = get_contact($dbc, $businessid, 'name');

		for($i = 0; $i < count($_FILES['upload_document']['name']); $i++) {
			move_uploaded_file($_FILES["upload_document"]["tmp_name"][$i], "download/".$_FILES["upload_document"]["name"][$i]) ;
		}

		$query_insert_support = "INSERT INTO `support` (`name`, `message`, `document`, `current_date`, `support_type`, `priority`, `heading`, `company_name`) VALUES ('$name', '$assign_work', '$document', '$created_date', '$service_type', '$priority', '$heading', '$company_name')";
		$result_insert_ticket = mysqli_query($dbc, $query_insert_support);
	} else {
		$query_insert_ca = "INSERT INTO `tickets` (`ticket_type`, `businessid`, `clientid`, `contactid`, `service_type`, `service`, `sub_heading`, `heading`, `category`, `created_date`, `created_by`, `assign_work`, `to_do_date`, `to_do_end_date`, `internal_qa_contactid`, `deliverable_contactid`, `internal_qa_date`, `deliverable_date`, `projectid`, `client_projectid`, `status`, `project_path`, `milestone_timeline`, `piece_work`, `max_time`, `max_qa_time`, `to_do_start_time`, `to_do_end_time`, `internal_qa_start_time`, `internal_qa_end_time`, `deliverable_start_time`, `deliverable_end_time`) VALUES ('$ticket_type', '$businessid', '$clientid', '$contactid', '$service_type', '$service', '$sub_heading', '$heading', '$category', '$created_date', '$created_by', '$assign_work', '$to_do_date', '$to_do_end_date', '$internal_qa_contactid', '$deliverable_contactid', '$internal_qa_date', '$deliverable_date', '$projectid', '$client_projectid', '$status', '$project_path', '$milestone_timeline', '$piece_work', '$max_time', '$max_qa_time', '$to_do_start_time', '$to_do_end_time', '$internal_qa_start_time', '$internal_qa_end_time', '$deliverable_start_time', '$deliverable_end_time')";
		$result_insert_ticket = mysqli_query($dbc, $query_insert_ca);
	}
	if(!$result_insert_ticket) {
		die("<script> alert('Failed to add ticket - ".mysqli_error($dbc)."'); window.history.back(); </script>");
	}
	$ticketid = mysqli_insert_id($dbc);
	echo "<script> alert('Successfully added ".TICKET_NOUN." #$ticketid.'); </script>";

	$supportid = $_POST['supportid'];
	if($supportid != 0) {
		$query_update_project = "UPDATE `support` SET  status='Ticket' WHERE `supportid` = '$supportid'";
		$result_update_project = mysqli_query($dbc, $query_update_project);

		$document = get_support($dbc, $supportid, 'document');
		$file_names = explode('*#*', $document);
		$i=0;
		foreach($file_names as $file_name) {
			if($file_name != '') {
				$query_insert_client_doc = "INSERT INTO `ticket_document` (`ticketid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$ticketid', 'Support Document', '$file_name', '$created_date', '$created_by')";
				$result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
			}
			$i++;
		}
	}

	$project_history .= ($project_history == '' ? '' : '<br />').get_contact($dbc, $_SESSION['contactid']).' created '.TICKET_NOUN.' #'.$ticketid.' at '.date('Y-m-d H:i');
	echo insert_day_overview($dbc, $created_by, 'Ticket', date('Y-m-d'), '', 'Created '.TICKET_NOUN.' #'.$ticketid);
} else {
	$ticketid = $_POST['ticketid'];
	$project_history .= ($project_history == '' ? '' : '<br />').get_contact($dbc, $_SESSION['contactid']).' updated '.TICKET_NOUN.' #'.$ticketid.' at '.date('Y-m-d H:i');
	$overview = 'Updated '.TICKET_NOUN.' #'.$ticketid;
	if(!empty($_POST['timer']) && $_POST['timer'] != '') {
		$overview .= ' - Added Time : '.$_POST['timer'];
	}
	echo insert_day_overview($dbc, $created_by, 'Ticket', date('Y-m-d'), '', $overview);

	$query_update_ticket = "UPDATE `tickets` SET `ticket_type`='$ticket_type', `businessid` = '$businessid', `projectid` = '$projectid', `client_projectid` = '$client_projectid', `to_do_date` = '$to_do_date', `to_do_end_date` = '$to_do_end_date', `internal_qa_contactid` = '$internal_qa_contactid', `deliverable_contactid` = '$deliverable_contactid', `internal_qa_date` = '$internal_qa_date', `deliverable_date` = '$deliverable_date', `max_time` = '$max_time', `max_qa_time` = '$max_qa_time', `spent_time` = '$spent_time', `total_days` = '$total_days', `project_path` = '$project_path', `milestone_timeline` = '$milestone_timeline', `service_type` = '$service_type', `service` = '$service', `sub_heading` = '$sub_heading', `heading` = '$heading', `assign_work` = '$assign_work', `to_do_start_time` = '$to_do_start_time', `to_do_end_time` = '$to_do_end_time', `internal_qa_start_time` = '$internal_qa_start_time', `internal_qa_end_time` = '$internal_qa_end_time', `deliverable_start_time` = '$deliverable_start_time', `deliverable_end_time` = '$deliverable_end_time' WHERE `ticketid` = '$ticketid'";
	$result_update_ticket = mysqli_query($dbc, $query_update_ticket);

	//Ticket Comment
	foreach($_POST['ticket_comment'] as $i => $ticket_comment) {
		$type = filter_var($_POST['ticket_comment_type'][$i],FILTER_SANITIZE_STRING);
		if($type == 'note' && $_POST['day_end_note'] != '') {
			$type = 'day';
			$ticket_comment = $_POST['day_end_note'];
		}
		$t_comment = filter_var(htmlentities($ticket_comment),FILTER_SANITIZE_STRING);
		if($t_comment != '') {
			$email_comment = $_POST['email_comment'][$i];

				$query_insert_ca = "INSERT INTO `ticket_comment` (`ticketid`, `comment`, `email_comment`, `created_date`, `created_by`, `type`, `note_heading`) VALUES ('$ticketid', '$t_comment', '$email_comment', '$created_date', '$created_by', '$type', '$note_heading')";
				$result_insert_ca = mysqli_query($dbc, $query_insert_ca);

			if($_POST['send_email_on_comment'][$i] == 'Yes') {
				$email = $_POST['ticket_comment_email_sender'][$i];
				$email_name_result = mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `email_address` = '".encryptIt($email)."'");
				if($email != '' && $email_name_id == mysqli_fetch_array($email_name_result)) {
					$from = [$email => get_contact($dbc, $email_name_id['contactid'])];
				} else if($email != '') {
					$from = [$email => get_contact($dbc, $_SESSION['contactid'])];
				} else {
					$from = '';
				}

				$subject = $_POST['ticket_comment_email_subject'][$i];
				$message = str_replace(['[NOTE]','[TICKETID]','[CLIENT]','[HEADING]','[STATUS]'], [$_POST['ticket_comment'],$ticketid,get_client($dbc,$businessid),$heading,$status], $_POST['ticket_comment_email_body'][$i]).get_contact($dbc, $email_name_id['contactid'], 'email_address');
				$email = get_email($dbc, $email_comment);
				try {
					send_email($from, $email, '', '', $subject, $message, '');
				} catch(Exception $e) {
					echo "<script>alert('Unable to send email. Please try again later.');console.log('".$e->getMessage()."');</script>";
				}
			}
		}
	}

	//Project Comment
	$timer = $_POST['timer'];
	$end_time = date('g:i A');

	$start_time = 0;
	if($timer != '0' && $timer != '00:00:00' && $timer != '') {
		$query_update_ticket = "UPDATE `ticket_timer` SET `end_time` = '$end_time', `start_timer_time` = '$start_time' WHERE `ticketid` = '$ticketid' AND created_by='$created_by' AND created_date='$created_date' AND end_time IS NULL";
		$result_update_ticket = mysqli_query($dbc, $query_update_ticket);

		$query_update_ticket = "UPDATE `tickets` SET `start_time` = '0' WHERE `ticketid` = '$ticketid'";
		$result_update_ticket = mysqli_query($dbc, $query_update_ticket);
	}
}

//deliverables
if($_POST['status'] != '') {
	$status = $_POST['status'];

	if(!empty($_POST['ticketid'])) {
		$status_result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `status`,`history`,`contactid` FROM `tickets` WHERE `ticketid`='$ticketid'"));
		$status_change = "";
		$history = $status_result['history'];
		$change_contactid = $_SESSION['contactid'];
		$name = get_contact($dbc, $change_contactid);
		if($name == '' || $name == '-') {
			$name = 'Admin';
		}

		if($status_result['status'] !== $status) {
			$old_status = $status_result['status'];
			$status_change = "`status_date` = NOW(),";
			$history .= "$name has changed Status from <b>$old_status</b> to <b>$status</b> on <b>" . date("Y-m-d h:i:s a") . "</b>.<br>";

		}

		$assgined_contacts = explode(',', $contactid);

		$assgined_contacts = array_filter($assgined_contacts);
		$assigned_names = array();
		if(!empty($assgined_contacts)) {
			foreach($assgined_contacts as $assgined_contact)
				if (strpos($status_result['contactid'], $assgined_contact.',') === FALSE)
					$assigned_names[] = get_contact($dbc, $assgined_contact);
		}

		if(!empty($assigned_names)) {
			$assigned_name = implode(',', $assigned_names);
			$history .= "$name has assigned ticket to <b>$assigned_name</b> on <b>" . date("Y-m-d h:i:s a") . "</b>.<br>";
		}

		$query_update_ticket = "UPDATE `tickets` SET $status_change `contactid` = '$contactid', `status` = '$status', `history`= '$history' WHERE `ticketid` = '$ticketid'";
		$result_update_ticket = mysqli_query($dbc, $query_update_ticket);
	}

	//Mail
	if($_POST['doing_email'] == 1) {
		$email = $_POST['doing_email_sender'];
		$email_name_result = mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `email_address` = '".encryptIt($email)."'");
		if($email != '' && $email_name_id == mysqli_fetch_array($email_name_result)) {
			$from = [$email => get_contact($dbc, $email_name_id['contactid'])];
		} else if($email != '') {
			$from = [$email => get_contact($dbc, $_SESSION['contactid'])];
		} else {
			$from = '';
		}

		$subject = $_POST['doing_email_subject'];
		$message = str_replace(['[DATE]','[TICKETID]','[CLIENT]','[HEADING]','[STATUS]'], [$_POST['to_do_date'],$ticketid,get_client($dbc,$businessid),$heading,$status], $_POST['doing_email_body']);
		foreach($_POST['contactid'] as $each_contactid)  {
			$to = get_email($dbc, $each_contactid);
			try {
				send_email($from, $to, '', '', $subject, $message, '');
			} catch(Exception $e) {
				echo "<script>alert('Unable to send email. Please try again later.');</script>";
			}
		}
	}
	if($_POST['internal_qa_email'] == 1) {
		$email = $_POST['internal_email_sender'];
		$email_name_result = mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `email_address` = '".encryptIt($email)."'");
		if($email != '' && $email_name_id == mysqli_fetch_array($email_name_result)) {
			$from = [$email => get_contact($dbc, $email_name_id['contactid'])];
		} else if($email != '') {
			$from = [$email => get_contact($dbc, $_SESSION['contactid'])];
		} else {
			$from = '';
		}

		$subject = $_POST['internal_email_subject'];
		$message = str_replace(['[DATE]','[TICKETID]','[CLIENT]','[HEADING]','[STATUS]'], [$_POST['internal_qa_date'],$ticketid,get_client($dbc,$businessid),$heading,$status], $_POST['internal_email_body']);
		foreach($_POST['internal_qa_contactid'] as $each_contactid)  {
			$to = get_email($dbc, $each_contactid);
			try {
				send_email($from, $to, '', '', $subject, $message, '');
			} catch(Exception $e) {
				echo "<script>alert('Unable to send email. Please try again later.');</script>";
			}
		}
	}
	if($_POST['client_qa_email'] == 1) {
		$email = $_POST['client_email_sender'];
		$email_name_result = mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `email_address` = '".encryptIt($email)."'");
		if($email != '' && $email_name_id == mysqli_fetch_array($email_name_result)) {
			$from = [$email => get_contact($dbc, $email_name_id['contactid'])];
		} else if($email != '') {
			$from = [$email => get_contact($dbc, $_SESSION['contactid'])];
		} else {
			$from = '';
		}

		$subject = $_POST['client_email_subject'];
		$message = str_replace(['[DATE]','[TICKETID]','[CLIENT]','[HEADING]','[STATUS]'], [$_POST['client_qa_date'],$ticketid,get_client($dbc,$businessid),$heading,$status], $_POST['client_email_body']);
		foreach($_POST['deliverable_contactid'] as $each_contactid)  {
			$to = get_email($dbc, $each_contactid);
			try {
				send_email($from, $to, '', '', $subject, $message, '');
			} catch(Exception $e) {
				echo "<script>alert('Unable to send email. Please try again later.');</script>";
			}
		}
	}
	//Mail
}

//Document
if (!file_exists('download')) {
	mkdir('download', 0777, true);
}
for($i = 0; $i < count($_FILES['upload_document']['name']); $i++) {
	$document = htmlspecialchars($_FILES["upload_document"]["name"][$i], ENT_QUOTES);

	move_uploaded_file($_FILES["upload_document"]["tmp_name"][$i], "download/".$_FILES["upload_document"]["name"][$i]) ;

	if($document != '') {
		$query_insert_client_doc = "INSERT INTO `ticket_document` (`ticketid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$ticketid', 'Support Document', '$document', '$created_date', '$created_by')";
		$result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
	}
}

for($i = 0; $i < count($_POST['support_link']); $i++) {
	$support_link = $_POST['support_link'][$i];

	if($support_link != '') {
		$query_insert_client_doc = "INSERT INTO `ticket_document` (`ticketid`, `type`, `link`, `created_date`, `created_by`) VALUES ('$ticketid', 'Support Document', '$support_link', '$created_date', '$created_by')";
		$result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
	}
}

for($i = 0; $i < count($_FILES['review_upload_document']['name']); $i++) {
	$review_document = htmlspecialchars($_FILES["review_upload_document"]["name"][$i], ENT_QUOTES);

	move_uploaded_file($_FILES["review_upload_document"]["tmp_name"][$i], "download/".$_FILES["review_upload_document"]["name"][$i]) ;

	if($review_document != '') {
		$query_insert_client_doc = "INSERT INTO `ticket_document` (`ticketid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$ticketid', 'Review Document', '$review_document', '$created_date', '$created_by')";
		$result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
	}
}

for($i = 0; $i < count($_POST['support_review_link']); $i++) {
	$support_review_link = $_POST['support_review_link'][$i];

	if($support_review_link != '') {
		$query_insert_client_doc = "INSERT INTO `ticket_document` (`ticketid`, `type`, `link`, `created_date`, `created_by`) VALUES ('$ticketid', 'Review Document', '$support_review_link', '$created_date', '$created_by')";
		$result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
	}
}

// Save Project History
if($projectid != '') {
	$project_history = htmlentities($project_history);
	$user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
	mysqli_query($dbc, "INSERT INTO `project_history` (`updated_by`, `description`, `projectid`) VALUES ('$user', '$project_history', '$projectid')");
} else if($client_projectid != '') {
	$project_history_result = mysqli_query($dbc, "UPDATE `client_project` SET `history`=CONCAT(IFNULL(CONCAT(`history`,'<br />'),''),'".htmlentities($project_history)."') WHERE `projectid` = '$client_projectid'");
}

if(!empty($_POST['from'])) {
	$url = $_POST['from'];
} else {
	if(empty($_POST['ticketid'])) {
		$url = 'add_tickets.php';
	} else {
		$url = 'add_tickets.php?ticketid='.$ticketid;
	}
}

echo '<script type="text/javascript"> window.location.replace("'.$url.'"); </script>';
?>
