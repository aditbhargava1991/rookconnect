<?php
// include ('../database_connection.php');
// include ('../global.php');
// include ('../function.php');
include('../include.php');
ob_clean();

if ($_GET['fill'] == "uploadimage") {
    $basename = $filename = htmlspecialchars($_FILES["newimage"]["name"], ENT_QUOTES);
    $file = htmlspecialchars($_FILES["newimage"]["tmp_name"], ENT_QUOTES);

    if (!file_exists('../Profile/download')) {
        mkdir('../Profile/download/', 0777, true);
    }
    $folderpath = "../Profile/download/";

    $j = 0;
    while (file_exists($folderpath . $filename)) {
        $filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.++$j.')$1', $basename);
    }

    $allowed_filetypes = array('.jpg','.gif','.bmp','.png');
    $ext = substr($filename, strpos($filename,'.'), strlen($filename)-1);

    if (in_array($ext, $allowed_filetypes)) {
        if (move_uploaded_file($file, $folderpath . $filename)) {
            $body_html = $folderpath.$filename;
            echo $body_html;
        }
    }
}

if($_GET['fill'] == "deleteimage") {
    $contactid = $_GET['contactid'];
    mysqli_query($dbc, "UPDATE `user_settings` SET `preset_profile_picture` = '' WHERE `contactid` = '$contactid'");
    echo "UPDATE `user_settings` SET `preset_profile_picture` = '' WHERE `contactid` = '$contactid'";
    unlink ("../Profile/download/profile_pictures/" . $contactid . ".jpg");
}

if($_GET['fill'] == "daysheet_reminders") {
    $daysheetreminderid = $_POST['daysheetreminderid'];
    $done = $_POST['done'];
    mysqli_query($dbc, "UPDATE `daysheet_reminders` SET `done` = '$done' WHERE `daysheetreminderid` = '$daysheetreminderid'");
}

if($_GET['fill'] == "daysheet_notepad_add") {
    $date = filter_var($_POST['date'], FILTER_SANITIZE_STRING);
    $contactid = filter_var($_POST['contactid'], FILTER_SANITIZE_STRING);
    $notes = filter_var(htmlentities($_POST['notes']), FILTER_SANITIZE_STRING);
    
	if($notes != '') {
		mysqli_query($dbc, "INSERT INTO `daysheet_notepad` (`contactid`,`date`,`notes`) VALUES ('$contactid','$date','$notes')");
	}
}
if($_GET['fill'] == "daysheet_notepad") {
    $date = filter_var($_POST['date'], FILTER_SANITIZE_STRING);
    $contactid = filter_var($_POST['contactid'], FILTER_SANITIZE_STRING);
    $notes = filter_var(htmlentities($_POST['notes']), FILTER_SANITIZE_STRING);

    mysqli_query($dbc, "INSERT INTO `daysheet_notepad` (`contactid`, `date`, `notes`) SELECT '$contactid', '$date', '$notes' FROM (SELECT COUNT(*) rows FROM `daysheet_notepad` WHERE `contactid` = '$contactid' AND `date` = '$date') num WHERE num.rows = 0");
    mysqli_query($dbc, "UPDATE `daysheet_notepad` SET `notes` = '$notes' WHERE `contactid` = '$contactid' AND `date` = '$date'");
}

if($_GET['fill'] == "daysheet_config") {
    $field_name = $_POST['field_name'];
    $daysheet_styling = $_POST['daysheet_styling'];
    $daysheet_ticket_slider = $_POST['ticket_slider'];
    $daysheet_fields_config = trim($_POST['field_list'], ',');
    $daysheet_weekly_config = trim($_POST['day_list'], ',');
    $daysheet_button_config = trim($_POST['button_list'], ',');
    $daysheet_rightside_views = trim($_POST['daysheet_rightside_views'], ',');
    if(empty($daysheet_rightside_views)) {
        $daysheet_rightside_views = '**ALL_OFF**';
    }
    $daysheet_ticket_default_mode = $_POST['daysheet_ticket_default_mode'];
    $quick_action_icons = $_POST['quick_action_icons'];
    $contactid = $_POST['settings_contactid'];

    if($contactid == 'software') {
        if($field_name == 'daysheet_ticket_fields[]') {
            set_config($dbc, 'daysheet_ticket_fields', trim($_POST['daysheet_ticket_fields'],','));
        } else if($field_name == 'daysheet_styling') {
            set_config($dbc, 'daysheet_styling', $daysheet_styling);
        } else if($field_name == 'daysheet_ticket_slider') {
            set_config($dbc, 'daysheet_ticket_slider', $daysheet_ticket_slider);
        } else if($field_name == 'daysheet_fields_config[]') {
            set_config($dbc, 'daysheet_fields_config', $daysheet_fields_config);
        } else if($field_name == 'daysheet_weekly_config[]') {
            set_config($dbc, 'daysheet_weekly_config', $daysheet_weekly_config);
        } else if($field_name == 'daysheet_button_config[]') {
            set_config($dbc, 'daysheet_button_config', $daysheet_button_config);
        } else if($field_name == 'daysheet_rightside_views[]') {
            set_config($dbc, 'daysheet_rightside_views', $daysheet_rightside_views);
        } else if($field_name == 'daysheet_ticket_default_mode') {
            set_config($dbc, 'daysheet_ticket_default_mode', $daysheet_ticket_default_mode);
        } else if($field_name == 'quick_action_icons[]') {
            set_config($dbc, 'daysheet_quick_action_icons', $quick_action_icons);
        }
    } else {
        if($field_name == 'daysheet_styling') {
            set_user_settings($dbc, 'daysheet_styling', $daysheet_styling);
        } else if($field_name == 'daysheet_fields_config[]') {
            set_user_settings($dbc, 'daysheet_fields_config', $daysheet_fields_config);
        } else if($field_name == 'daysheet_weekly_config[]') {
            set_user_settings($dbc, 'daysheet_weekly_config', $daysheet_weekly_config);
        } else if($field_name == 'daysheet_button_config[]') {
            set_user_settings($dbc, 'daysheet_button_config', $daysheet_button_config);
        } else if($field_name == 'daysheet_rightside_views[]') {
            set_user_settings($dbc, 'daysheet_rightside_views', $daysheet_rightside_views);
        }
    }
}

if($_GET['action'] == 'setStatus') {
	$status = filter_var($_POST['status'],FILTER_SANITIZE_STRING);
	if($_POST['stopid'] > 0) {
		$dbc->query("UPDATE `ticket_schedule` SET `status`='$status' WHERE `id`='{$_POST['stopid']}'");
	} else if($_POST['ticketid'] > 0) {
		$dbc->query("UPDATE `tickets` SET `status`='$status' WHERE `ticketid`='{$_POST['stopid']}'");
	}
}

if($_GET['action'] == 'contacts_untag') {
    $id = $_GET['id'];
    if($id > 0) {
        mysqli_query($dbc, "UPDATE `contacts_tagging` SET `deleted` = 1 WHERE `id` = '$id'");
    }
}
?>