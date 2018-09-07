<?php
include ('../include.php');
checkAuthorised();
ob_clean();

if($_GET['fill'] == 'checklist') {
    $checklistid = $_GET['checklistid'];
    $checked = $_GET['checked'];
	$equipmentid = $_GET['unit'];
    $updated_by = $_SESSION['contactid'];
    $updated_date = date('Y-m-d');
	$note = ($checked == 1 ? 'Marked done' : 'Unchecked').' by '.get_contact($dbc, $updated_by).' at '.date('Y-m-d, g:i:s A');

	$query = "INSERT INTO `item_checklist_unit` (`checklistlineid`, `item_type`, `item_id`, `checked_by`, `notes`) VALUES ('$checklistid', 'equipment', '$equipmentid', '$updated_by', '$note')";
	$result = mysqli_query($dbc, $query);
}

if($_GET['fill'] == 'checklist_priority') {print_r($_GET);
    $lineid = $_GET['lineid'];
    $afterid = $_GET['afterid'];
    $checklistid = mysqli_fetch_array(mysqli_query($dbc, "SELECT `checklistid` FROM `item_checklist_line` WHERE `checklistlineid`='$lineid'"))['checklistid'];
    $line_priority = mysqli_fetch_array(mysqli_query($dbc, "SELECT `priority` FROM `item_checklist_line` WHERE `checklistlineid`='$lineid'"))['priority'];
    $after_priority = mysqli_fetch_array(mysqli_query($dbc, "SELECT `priority` FROM `item_checklist_line` WHERE `checklistlineid`='$afterid'"))['priority'];

	$query = "UPDATE `item_checklist_line` SET  `priority`=`priority`+1 WHERE `priority` > '$after_priority' AND `priority` < '$line_priority' AND `checklistid` = '$checklistid'";
	$result = mysqli_query($dbc, $query);echo $query;

	$query = "UPDATE `item_checklist_line` SET  `priority`='".($after_priority + 1)."' WHERE `checklistlineid` = '$checklistid'";
	$result = mysqli_query($dbc, $query);echo $query;

}

if($_GET['fill'] == 'add_checklist') {
	$checklistid = $_POST['checklist'];
	$checklist = filter_var($_POST['line'],FILTER_SANITIZE_STRING);
	$query_insert = "INSERT INTO `item_checklist_line` (`checklistid`, `checklist`, `priority`) SELECT '$checklistid', '$checklist', (IFNULL(MAX(`priority`),1)+1) FROM `item_checklist_line` WHERE `checklistid`='$checklistid'";
	mysqli_query($dbc, $query_insert);
}

if($_GET['fill'] == 'delete_checklist') {
	$id = $_GET['checklistid'];
    $date_of_archival = date('Y-m-d');
	$query = "UPDATE `item_checklist_line` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `checklistlineid`='$id'";
	$result = mysqli_query($dbc,$query);
}
if($_GET['fill'] == 'checklistreply') {
	$id = $_POST['id'];
	$reply = filter_var(htmlentities('<p>'.$_POST['reply'].'</p>'),FILTER_SANITIZE_STRING);
	$query = "UPDATE `item_checklist_line` SET `checklist`=CONCAT(`checklist`,'$reply') WHERE `checklistlineid`='$id'";
	$result = mysqli_query($dbc,$query);
}
if($_GET['fill'] == 'checklistalert') {
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
	$link = WEBSITE_URL."/Equipment/equipment_checklist.php";
	$text = "Checklist";
	$date = date('Y/m/d');
	$sql = mysqli_query($dbc, "INSERT INTO `alerts` (`alert_date`, `alert_link`, `alert_text`, `alert_user`) VALUES ('$date', '$link', '$text', '$user')");
}
if($_GET['fill'] == 'checklistemail') {
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
				This is a reminder about the $title on the equipment checklist.<br />\n<br />
				<a href='".WEBSITE_URL."/Equipment/equipment_checklist.php\">Click here</a> to see the checklists page.";
			send_email('', $email_address, '', '', $subject, $body, '');
		}
	}
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
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM item_checklist_line WHERE checklistlineid='$item_id'"));
		$id = $result['checklistid'];
		$title = explode('<p>',html_entity_decode($result['checklist']))[0];
		$subject = "A reminder about the $title on the equipment checklist";
		$body = htmlentities("This is a reminder about the $title on the equipment checklist.<br />\n<br />
			<a href=\"".WEBSITE_URL."/Equipment/equipment_checklist.php\">Click here</a> to see the checklists page.");
	}
	else {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM checklist WHERE checklistid = '$item_id'"));
		$id = $item_id;
		$title = $result['item_checklist_line'];
		$subject = "A reminder about the $title checklist";
		$body = htmlentities("This is a reminder about the $title checklist.<br />\n<br />
			<a href=\"".WEBSITE_URL."/Equipment/equipment_checklist.php\">Click here</a> to see the checklists page.");
	}
	$result = mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_time`, `reminder_type`, `subject`, `body`, `sender`)
		VALUES ('$to', '$date', '08:00:00', 'QUICK', '$subject', '$body', '$sender')");
}
if($_GET['fill'] == 'checklistflag') {
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
if($_GET['fill'] == 'checklist_upload') {
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
if($_GET['fill'] == 'checklist_quick_time') {
	$checklistid = $_POST['id'];
	$time = $_POST['time'];
	$query_time = "INSERT INTO `item_checklist_time` (`checklistlineid`, `work_time`, `contactid`, `timer_date`) VALUES ('$checklistid', '$time', '".$_SESSION['contactid']."', '".date('Y-m-d')."')";
	$result = mysqli_query($dbc, $query_time);
	insert_day_overview($dbc, $_SESSION['contactid'], 'Checklist', date('Y-m-d'), '', "Updated Checklist Item #$checklistid - Added Time : $time");
}
else if($_GET['fill'] == 'wo_add_checklist') {
	$query = mysqli_query($dbc, "INSERT INTO `equipment_wo_checklist` (`workorderid`, `checklist`, `sort`, `created_by`)
		SELECT '".$_POST['workorderid']."', '".filter_var($_POST['new_item'],FILTER_SANITIZE_STRING)."', IFNULL(MAX(sort),0) + 1, '".$_SESSION['contactid']."' FROM `equipment_wo_checklist` WHERE `workorderid`='".$_POST['workorderid']."'");
	echo mysqli_insert_id($dbc);
}
else if($_GET['fill'] == 'wo_delete_checklist') {
    $date_of_archival = date('Y-m-d');
	$query = mysqli_query($dbc, "UPDATE `equipment_wo_checklist` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `checklistid`='".$_GET['id']."'");
}
else if($_GET['fill'] == 'wo_checklist_flag') {
	$item_id = $_POST['id'];
	$colour = mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colour` FROM `equipment_wo_checklist` WHERE `checklistid` = '$item_id'"))['flag_colour'];
	$colour_list = explode('#*#', get_config($dbc, 'general_flag_colours'));
	$colour_key = key(preg_grep("/^$colour*#*.*/",$colour_list));
	$new_colour = explode('*#*',($colour_key === NULL ? $colour_list[0] : ($colour_key + 1 < count($colour_list) ? $colour_list[$colour_key + 1] : '')))[0];
	$result = mysqli_query($dbc, "UPDATE `equipment_wo_checklist` SET `flag_colour`='$new_colour' WHERE `checklistid` = '$item_id'");
	echo $new_colour;
}
else if($_GET['fill'] == 'wo_checklist_reply') {
	$checklistid = $_POST['id'];
	$reply = filter_var(htmlentities('<p>'.$_POST['reply'].'</p>'),FILTER_SANITIZE_STRING);
	$query = "UPDATE `equipment_wo_checklist` SET  `checklist`=CONCAT(`checklist`,'$reply') WHERE `checklistid` = '$checklistid'";
	$result = mysqli_query($dbc, $query);
}
else if($_GET['fill'] == 'wo_checklist_priority') {
	$workorderid = $_GET['workorderid'];
    $id = $_GET['id'];
    $prior = $_GET['afterid'];
    $prior_sort = mysqli_fetch_array(mysqli_query($dbc, "SELECT `sort`+1 FROM `equipment_wo_checklist` WHERE `checklistid`='$prior'"))[0];
	$result = mysqli_query($dbc, "UPDATE `equipment_wo_checklist` SET  `sort`=`sort`+1 WHERE `sort` >= '$prior_sort' AND `workorderid` = '$workorderid'");
	$result = mysqli_query($dbc, "UPDATE `equipment_wo_checklist` SET  `sort`='$prior_sort' WHERE `checklistid` = '$id'");
}
else if($_GET['fill'] == 'wo_checklist_upload') {
	$id = $_GET['id'];
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
	$basename = $_FILES['file']['name'];
	if($basename != '') {
		$basename = $filename = preg_replace('/[^A-Za-z0-9\.]/','_',$basename);
		$i = 0;
		while(file_exists('download/'.$filename)) {
			$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.++$i.')$1', $basename);
		}
		move_uploaded_file($_FILES['file']['tmp_name'], 'download/'.$filename);
		mysqli_query($dbc, "INSERT INTO `equipment_wo_checklist_uploads` (`checklistid`, `type`, `link`, `created_date`, `created_by`) VALUES ('$id', 'Support Document', '$filename', '".date('Y/m/d')."', '".$_SESSION['contactid']."')");
		echo $filename;
	}
}
else if($_GET['fill'] == 'get_equipment_inspections') {
	$equipmentid = $_GET['equipment'];
	include('add_work_order_inspection.php');
}
else if($_GET['fill'] == 'wo_remove_inventory') {
	$line_id = $_GET['line'];
	mysqli_query($dbc, "UPDATE `equipment_inventory` SET `deleted`=0 WHERE `lineid`='$line_id'");
}
else if($_GET['fill'] == 'wo_remove_po') {
	$poid = $_GET['poid'];
	mysqli_query($dbc, "UPDATE `equipment_purchase_order_items` SET `deleted`=0 WHERE `poid`='$poid'");
}
else if($_GET['fill'] == 'update_workorder_status') {
	$id = $_GET['id'];
	$status = $_GET['status'];
	mysqli_query($dbc, "UPDATE `equipment_work_orders` SET `status`='$status' WHERE `workorderid`='$id'");
}
else if($_GET['fill'] == 'expense_delete') {
	$id = $_POST['expenseid'];
	mysqli_query($dbc, "UPDATE `equipment_expenses` SET `deleted`=0 WHERE `expenseid`='$id'");
}
else if($_GET['fill'] == 'expense_approve') {
	$id = $_POST['expenseid'];
	mysqli_query($dbc, "UPDATE `equipment_expenses` SET `status`='Approved' WHERE `expenseid`='$id'");
}
else if($_GET['fill'] == 'expense_pay') {
	$id = $_POST['expenseid'];
	mysqli_query($dbc, "UPDATE `equipment_expenses` SET `status`='Paid' WHERE `expenseid`='$id'");
}
else if($_GET['fill'] == 'expense_reject') {
	$id = $_POST['expenseid'];
	mysqli_query($dbc, "UPDATE `equipment_expenses` SET `status`='Rejected' WHERE `expenseid`='$id'");
}
else if($_GET['fill'] == 'equipment_status') {
	$id = $_POST['id'];
	$status = $_POST['status'];
	mysqli_query($dbc, "UPDATE `equipment` SET `status`='$status' WHERE `equipmentid`='$id'");
}
else if($_GET['fill'] == 'archive_equipment_assignment') {
	if($_GET['equipment_assignmentid'] > 0) {
	        $date_of_archival = date('Y-m-d');
    	mysqli_query($dbc, "UPDATE `equipment_assignment` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `equipment_assignmentid` = '".$_GET['equipment_assignmentid']."'");
	}
}
?>