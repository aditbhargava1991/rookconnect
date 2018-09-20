<?php
include_once('../database_connection.php');
include_once('../function.php');
include_once('../global.php');
include_once('../phpmailer.php');

if($_GET['fill'] == 'checklist') {
    $checklistid = $_GET['checklistid'];
    $checked = $_GET['checked'];
    $updated_by = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
    $updated_date = date('Y-m-d');

	$query_update_project = "UPDATE `checklist_name` SET  `checked`='$checked', `updated_date`='$updated_date', `updated_by`='$updated_by'  WHERE `checklistnameid` = '$checklistid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);

    $checklist_item = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `checklist` FROM `checklist_name` WHERE `checklistnameid` = '$checklistid'"))['checklist'];
    $checklist_item = explode('&lt;p&gt;', $checklist_item)[0];
    if($checked > 0) {
        $day_overview_comment = 'Completed Checklist Item '.$checklist_item;
    } else {
        $day_overview_comment = 'Unchecked Checklist Item '.$checklist_item;
    }

    //Check or uncheck checklist_actions and reminders for this checklist item
    mysqli_query($dbc, "UPDATE `checklist_actions` SET `done` = '$checked' WHERE `checklistnameid` = '$checklistid'");
    mysqli_query($dbc, "UPDATE `reminders` SET `done` = '1' WHERE `src_table` = 'checklist_name' AND `src_tableid` = '$checklistid'");

    //Insert into day overview table
    $parent_checklistid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `checklistid` FROM `checklist_name` WHERE `checklistnameid` = '$checklistid'"))['checklistid'];
    $checklist_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `checklist_name` FROM `checklist` WHERE `checklistid` = '$parent_checklistid'"))['checklist_name'];
    insert_day_overview($dbc, $_SESSION['contactid'], 'Checklist', date('Y-m-d'), '', 'Updated Checklist '.$checklist_name.' - '.$day_overview_comment, $parent_checklistid);

    $get_subtab = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid` = '$checklistid'"));
    $subtabid = $get_subtab['subtabid'];
    $checklist_name = $get_subtab['checklist_name'];
    $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Updated Checklist Items in <b>'.$checklist_name.'</b> on '.date('Y-m-d');
    $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '$checklist_name', '', '', '$checklistid', '$subtabid')";
    $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
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

        $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Added Checklist Item in <b>'.$checklist_name.'</b> on '.date('Y-m-d');
        $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '$checklist_name', '', '', '$checklistid', '$subtabid')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

    }
}
if($_GET['fill'] == 'checklistalert') {
    $item_id = $_POST['id'];
    $type = $_POST['type'];
    $user = $_POST['user'];
    if($type == 'checklist') {
        $result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM tickets WHERE ticketid='$item_id'"));
        $id = $result['ticketid'];
    }
    $link = WEBSITE_URL."/Ticket/index.php?edit=".$id;
    $text = TICKET_NOUN;
    $date = date('Y/m/d');
    foreach ((array)$user as $singleuser) {
        $sql = mysqli_query($dbc, "INSERT INTO `alerts` (`alert_date`, `alert_link`, `alert_text`, `alert_user`) VALUES ('$date', '$link', '$text', '$singleuser')");
    }
}
if($_GET['fill'] == 'checklistemail') {
    $item_id = $_POST['id'];
    $type = $_POST['type'];
    $user = $_POST['user'];
    $subject = '';
    $title = '';
    if($type == 'checklist') {
        $result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM tickets WHERE ticketid='$item_id'"));
        $id = $result['ticketid'];
        $title = '#' . $result['ticketid'].' : '.$result['service_type'].' : '.$result['heading'].' : '.$result['status'];
        $subject = "A reminder about the $title on the ".TICKET_NOUN;
    }
    foreach ((array)$user as $singleuser) {
        $contacts = mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$singleuser'");
        while($row = mysqli_fetch_array($contacts)) {
            $email_address = get_email($dbc, $row['contactid']);
            if(trim($email_address) != '') {
                $body = "Hi ".decryptIt($row['first_name'])."<br />\n<br />
                    This is a reminder about the $title on the ".TICKET_NOUN.".<br />\n<br />
                    <a href='".WEBSITE_URL."/Ticket/index.php?edit=$id'>Click here</a> to see the ".TICKET_NOUN.".";
                send_email('', $email_address, '', '', $subject, $body, '');
            }
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
        $result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM tickets WHERE ticketid='$item_id'"));
        $id = $result['ticketid'];
        $title = '#' . $result['ticketid'].' : '.$result['service_type'].' : '.$result['heading'].' : '.$result['status'];
        $subject = "A reminder about the $title on the ".TICKET_NOUN;
        $body = htmlentities("This is a reminder about the $title on the ".TICKET_NOUN.".<br />\n<br />
            <a href=\"".WEBSITE_URL."/Ticket/index.php?edit=$id\">Click here</a> to see the ".TICKET_NOUN.".");
    }

    foreach ((array)$to as $singleto) {
        $result = mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_time`, `reminder_type`, `subject`, `body`, `sender`, `src_table`, `src_tableid`)
        VALUES ('$singleto', '$date', '08:00:00', 'QUICK', '$subject', '$body', '$sender', 'tickets', '$id')");
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
    move_uploaded_file($file, "download/".$filename);
    if($type == 'checklist') {
        $query_insert = "INSERT INTO `ticket_document` (`ticketid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$id', 'Support Document', '$filename', '".date('Y/m/d')."', '".$_SESSION['contactid']."')";
        $result_insert = mysqli_query($dbc, $query_insert);
    }
}
if($_GET['fill'] == 'checklistflag') {
    $item_id = $_POST['id'];
    $type = $_POST['type'];
    if($type == 'checklist') {
        $colour = mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colour` FROM tickets WHERE ticketid = '$item_id'"))['flag_colour'];
        $colour_list = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colours` FROM `field_config_checklist`"))['flag_colours']);
        $colour_key = array_search($colour, $colour_list);
        $new_colour = ($colour_key === FALSE ? $colour_list[0] : ($colour_key + 1 < count($colour_list) ? $colour_list[$colour_key + 1] : ''));
        $result = mysqli_query($dbc, "UPDATE `tickets` SET `flag_colour`='$new_colour' WHERE `ticketid` = '$item_id'");
        echo $new_colour;
    }
}
if($_GET['fill'] == 'delete_checklist') {
    $id = $_GET['checklistid'];
    $date_of_archival = date('Y-m-d');
    $query = "UPDATE `tickets` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `ticketid`=$id";
    $result = mysqli_query($dbc,$query);
}

?>
