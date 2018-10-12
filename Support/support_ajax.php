<?php include_once('config.php');
if($user == 'ROOK Connect' && $url == 'https://ffm.rookconnect.com') {
	$user = $_SESSION['contactid'];
	$user_name = get_contact($dbc, $user);
	$user_category = get_contact($dbc, $user, 'category');
} else {
	$user = mysqli_fetch_array(mysqli_query($dbc_support, "SELECT * FROM `contacts` WHERE ('".WEBSITE_URL."' LIKE CONCAT('%',`website`) AND IFNULL(`website`,'') NOT LIKE '') OR `name`='".encryptIt($user)."' ORDER BY `name`='".encryptIt($user)."'"))['contactid'];
	$user_category = 'REMOTE_'.get_contact($dbc, $_SESSION['contactid'], 'category');
	if($user_category != 'REMOTE_Staff') {
		$user_category = 'USER_CUSTOMER';
		$user = $_SESSION['contactid'];
		$name = get_contact($dbc, $user);
		$user_name = ($name == '' ? get_client($dbc, $user) : $name);
		$dbc_support = $dbc;
	}
}
//$dbc_support = mysqli_connect('localhost', 'root', '', 'local_rookconnect_db');
ob_clean();

if($_GET['fill'] == 'assign') {
	$id = $_POST['id'];
	$staff = implode(',',$_POST['staff']);
	if(mysqli_query($dbc, "UPDATE `support` SET `assigned`='$staff' WHERE `supportid`='$id'")) {
		echo $staff;
	} else {
		echo "Unable to assign staff.";
	}
}
else if($_GET['fill'] == 'create_ticket') {
	$id = $_POST['id'];
	$support = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `support` WHERE `supportid`='$id'"));
	$message = htmlentities("<p>".TICKET_NOUN." created from ".$support['support_type'].".<br />
		Customer: ".$support['company_name']." (".$support['software_url'].")<br />
		Name: ".$support['name']."</p>
		<h4>Original Message:</h4>
		".html_entity_decode($support['message'])."
		<p>Support Date: ".$support['current_date']."</p>");
	$insert = "INSERT INTO `tickets` (`businessid`, `heading`, `assign_work`, `contactid`, `to_do_date`, `to_do_end_date`, `created_by`, `created_date`, `status`)
		VALUES ('".$support['businessid']."', 'Support Request for ".$support['heading']."', '$message', ',".$support['assigned'].",', '".date('Y-m-d')."', '".date('Y-m-d')."', '".$_SESSION['contactid']."', '".date('Y-m-d')."', 'Time Estimate Needed')";
	mysqli_query($dbc, $insert);
	$ticketid = mysqli_insert_id($dbc);
	mysqli_query($dbc, "INSERT INTO `ticket_document` (`ticketid`, `type`, `link`, `document`, `created_date`, `created_by`)
		VALUES ('$ticketid', 'Customer Support Request', '".WEBSITE_URL."/Support/customer_support.php?tab=requests&type=active#$id', '', '".date('Y-m-d')."', '".$_SESSION['contactid']."')");
	mysqli_query($dbc, "UPDATE `support` SET `ticketid`='$ticketid' WHERE `supportid`='$id'");
	echo $ticketid;
}
else if($_GET['fill'] == 'priorities') {
	$type = $_POST['type'];
	$priorities = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`priority`) list, MAX(`priority`) high FROM `support_services` WHERE `type`='$type'"));
	$priority_list = explode(',',$priorities['list']);
	echo "<option></option>";
	for($i = 0; $i <= $priorities['high']; $i++) {
		echo "<option ".(in_array($i+1,$priority_list) ? 'disabled' : '')." value='".($i+1)."'>".($i+1)."</option>";
	}
}
else if($_GET['fill'] == 'archive') {
	$id = $_GET['supportid'];
	$date = date('Y-m-d');
    $date_of_archival = date('Y-m-d');
	mysqli_query($dbc_support, "UPDATE `support` SET `deleted`=1, `date_of_archival` = '$date_of_archival', `archived_date`='$date' WHERE `supportid`='$id'");
}
else if($_GET['fill'] == 'flag') {
	$item_id = $_POST['id'];
	$colour = mysqli_fetch_array(mysqli_query($dbc_support, "SELECT `flag_colour` FROM support WHERE supportid = '$item_id'"))['flag_colour'];
	$colour_list = explode(',', mysqli_fetch_array(mysqli_query($dbc_support, "SELECT `flag_colours` FROM `field_config_checklist`"))['flag_colours']);
	$colour_key = array_search($colour, $colour_list);
	$new_colour = ($colour_key === FALSE ? $colour_list[0] : ($colour_key + 1 < count($colour_list) ? $colour_list[$colour_key + 1] : ''));
	$result = mysqli_query($dbc, "UPDATE `support` SET `flag_colour`='$new_colour' WHERE `supportid` = '$item_id'");
	echo $new_colour;
}
else if($_GET['fill'] == 'email') {
	$item_id = $_POST['id'];
	$user_category = $_POST['user_category'];
	$row = mysqli_fetch_array(mysqli_query($dbc_support, "SELECT * FROM support WHERE supportid = '$item_id'"));
	$subject = $row['support_type']." #$item_id Alert";
	$body = "This is an alert regarding ".$row['support_type']." #$item_id.<br />\n<br />
		<a href='[URL]/Support/customer_support.php?tab=requests&type=".($row['deleted'] == 1 ? 'closed' : ($row['support_type'] == 'Support Request' ? 'requests' : ($row['support_type'] == 'Critical Incident' ? 'critical' : 'feedback')))."#".$row['supportid']."'>Click here</a> to go to the ".$row['support_type'].".";
	if($user_category == 'Staff') {
		try {
			send_email('info@rookconnect.com', $row['email'], '', '', $subject, str_replace('[URL]',$row['software_url'],$body), '');
		} catch(Exception $e) { }
		foreach(explode(',',$row['cc_email']) as $email) {
			if($email != '') {
				try {
					send_email('info@rookconnect.com', $email, '', '', $subject, str_replace('[URL]',$row['software_url'],$body), '');
				} catch(Exception $e) { }
			}
		}
	} else {
		if($row['assigned'] == '') {
			send_email('info@rookconnect.com', 'jonathanhurdman@freshfocusmedia.com', '', '', $subject, str_replace('[URL]','https://ffm.rookconnect.com',$body), '');
			send_email('info@rookconnect.com', 'dayanapatel@freshfocusmedia.com', '', '', $subject, str_replace('[URL]','https://ffm.rookconnect.com',$body), '');
			send_email('info@rookconnect.com', 'jaylahiru@freshfocusmedia.com', '', '', $subject, str_replace('[URL]','https://ffm.rookconnect.com',$body), '');
			send_email('info@rookconnect.com', 'jenniferhardy@freshfocusmedia.com', '', '', $subject, str_replace('[URL]','https://ffm.rookconnect.com',$body), '');
			send_email('info@rookconnect.com', 'kaylavaltins@freshfocusmedia.com', '', '', $subject, str_replace('[URL]','https://ffm.rookconnect.com',$body), '');
			send_email('info@rookconnect.com', 'kennethbond@freshfocusmedia.com', '', '', $subject, str_replace('[URL]','https://ffm.rookconnect.com',$body), '');
		} else {
			foreach(explode(',',$row['assigned']) as $id) {
				$email = get_email($dbc_support, $id);
				try {
					send_email('info@rookconnect.com', $email, '', '', $subject, str_replace('[URL]','https://ffm.rookconnect.com',$body), '');
				} catch(Exception $e) { }
			}
		}
	}
}
else if($_GET['fill'] == 'reply') {
	$id = $_POST['id'];
	$reply = filter_var(htmlentities('<p>'.$_POST['reply'].'</p>'),FILTER_SANITIZE_STRING);
	$query = "UPDATE `support` SET `message`=CONCAT(`message`,'$reply') WHERE `supportid`='$id'";
	$result = mysqli_query($dbc_support,$query);
}
else if($_GET['fill'] == 'reply_plan') {
	$id = $_POST['id'];
	$reply = filter_var(htmlentities('<p>'.$_POST['reply'].'</p>'),FILTER_SANITIZE_STRING);
	$query = "UPDATE `support` SET `critical_plan`=CONCAT(`critical_plan`,'$reply') WHERE `supportid`='$id'";
	$result = mysqli_query($dbc_support,$query);
}
else if($_GET['fill'] == 'reply_discovery') {
	$id = $_POST['id'];
	$reply = filter_var(htmlentities('<p>'.$_POST['reply'].'</p>'),FILTER_SANITIZE_STRING);
	$query = "UPDATE `support` SET `critical_discovery`=CONCAT(`critical_discovery`,'$reply') WHERE `supportid`='$id'";
	$result = mysqli_query($dbc_support,$query);
}
else if($_GET['fill'] == 'reply_action') {
	$id = $_POST['id'];
	$reply = filter_var(htmlentities('<p>'.$_POST['reply'].'</p>'),FILTER_SANITIZE_STRING);
	$query = "UPDATE `support` SET `critical_action`=CONCAT(`critical_action`,'$reply') WHERE `supportid`='$id'";
	$result = mysqli_query($dbc_support,$query);
}
else if($_GET['fill'] == 'reply_check') {
	$id = $_POST['id'];
	$reply = filter_var(htmlentities('<p>'.$_POST['reply'].'</p>'),FILTER_SANITIZE_STRING);
	$query = "UPDATE `support` SET `critical_check`=CONCAT(`critical_check`,'$reply') WHERE `supportid`='$id'";
	$result = mysqli_query($dbc_support,$query);
}
else if($_GET['fill'] == 'reply_adjust') {
	$id = $_POST['id'];
	$reply = filter_var(htmlentities('<p>'.$_POST['reply'].'</p>'),FILTER_SANITIZE_STRING);
	$query = "UPDATE `support` SET `critical_adjustments`=CONCAT(`critical_adjustments`,'$reply') WHERE `supportid`='$id'";
	$result = mysqli_query($dbc_support,$query);
}
else if($_GET['fill'] == 'upload') {
	$id = $_GET['id'];
	$filename = $_FILES['file']['name'];
	if($filename != '') {
		if (!file_exists('download')) {
			mkdir('download', 0777, true);
		}
		$basefilename = $filename = preg_replace('/[^A-Za-z0-9\.]/','_',$filename);
		$i = 0;
		while(file_exists('download/'.$filename)) {
			$filename = preg_replace('/(\.[A-Za-z0-9]*)/', '('.++$i.')$1', $basefilename);
		}
		if(!move_uploaded_file($_FILES['file']['tmp_name'], 'download/'.$filename)) {
			echo "Error Saving Attachment: ".$filename."\n";
		}
		if(!mysqli_query($dbc_support, "INSERT INTO `support_uploads` (`supportid`, `document`, `created_by`) VALUES ('$id', '".WEBSITE_URL."/Support/download/$filename', '".get_contact($dbc, $_SESSION['contactid'])."')")) {
			echo "Error Recording Attachment: ".mysqli_error($dbc_support)."\n";
		}
	}
}
else if($_GET['fill'] == 'checklistupload') {
	$id = $_GET['id'];
	$table = $_GET['table_name'];
	$filename = $_FILES['file']['name'];
	if($filename != '') {
		if (!file_exists('../Tasks_Updated/download')) {
			mkdir('../Tasks_Updated/download', 0777, true);
		}
		$basefilename = $filename = preg_replace('/[^A-Za-z0-9\.]/','_',$filename);
		$i = 0;
		while(file_exists('../Tasks_Updated/download/'.$filename)) {
			$filename = preg_replace('/(\.[A-Za-z0-9]*)/', '('.++$i.')$1', $basefilename);
		}
		if(!move_uploaded_file($_FILES['file']['tmp_name'], '../Tasks_Updated/download/'.$filename)) {
			echo "Error Saving Attachment: ".$filename."\n";
		}
		if(!mysqli_query($dbc_support, "INSERT INTO `tasklist_document` (`tasklistid`, `document`, `created_by`) VALUES ('$id', '$filename', '".get_contact($dbc, $_SESSION['contactid'])."')")) {
			echo "Error Recording Attachment: ".mysqli_error($dbc_support)."\n";
		}
	}
}
else if($_GET['fill'] == 'checklistflag') {
	$item_id = $_POST['id'];
	$table = $_POST['table_name'];
	$colour = mysqli_fetch_array(mysqli_query($dbc_support, "SELECT `flag_colour` FROM `tasklist` WHERE `tasklistid` = '$item_id'"))['flag_colour'];
	$colour_list = explode(',', mysqli_fetch_array(mysqli_query($dbc_support, "SELECT `flag_colours` FROM `field_config_checklist`"))['flag_colours']);
	$colour_key = array_search($colour, $colour_list);
	$new_colour = ($colour_key === FALSE ? $colour_list[0] : ($colour_key + 1 < count($colour_list) ? $colour_list[$colour_key + 1] : ''));
	$result = mysqli_query($dbc, "UPDATE `tasklist` SET `flag_colour`='$new_colour' WHERE `tasklistid` = '$item_id'");
	echo $new_colour;
}
else if($_GET['fill'] == 'checklistreply') {
	$id = $_POST['id'];
	$table = $_POST['table_name'];
	$reply = filter_var(htmlentities('<p>'.$_POST['reply'].'</p>'),FILTER_SANITIZE_STRING);
	$query = "UPDATE `tasklist` SET `task`=CONCAT(`task`,'$reply') WHERE `tasklistid`='$id'";
	$result = mysqli_query($dbc_support,$query);
}
else if($_GET['action'] == 'comm_settings') {
	set_config($dbc, 'support_recipients_all', implode(';',$_POST['all_recips']));
	set_config($dbc, 'support_recipients_default', implode(';',$_POST['default_recips']));

	foreach($_POST['types'] as $i => $type_id) {
		set_config($dbc, 'support_recipients_'.$type_id, implode(';',$_POST['recipients'][$i]));
		set_config($dbc, 'support_alert_'.$type_id, $_POST['alerts'][$i]);
		set_config($dbc, 'support_note_'.$type_id, $_POST['notes'][$i]);
	}
}
else if($_GET['action'] == 'tab_settings') {
	set_config($dbc, 'cust_support_tab_list', implode(',',$_POST['tab_list']));
}