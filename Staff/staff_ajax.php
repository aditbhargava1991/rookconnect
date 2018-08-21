<?php include('../include.php');
ob_clean();

if($_GET['action'] == 'field_config') {
	$tab = filter_var($_POST['tab'],FILTER_SANITIZE_STRING);
	$subtab = filter_var($_POST['subtab'],FILTER_SANITIZE_STRING);
	$accordion = filter_var($_POST['accordion'],FILTER_SANITIZE_STRING);
	$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `field_config_contacts` (`tab`,`subtab`,`accordion`) SELECT '$tab', '$subtab', '$accordion' FROM (SELECT COUNT(*) rows FROM `field_config_contacts` WHERE `tab`='$tab' AND `subtab`='$subtab' AND `accordion`='$accordion') num WHERE num.rows = 0");
	mysqli_query($dbc, "UPDATE `field_config_contacts` SET `$field`='$value' WHERE `tab`='$tab' AND `subtab`='$subtab' AND `accordion`='$accordion'");
	echo "UPDATE `field_config_contacts` SET `$field`='$value' WHERE `tab`='$tab' AND `subtab`='$subtab' AND `accordion`='$accordion'";
} else if($_GET['action'] == 'add_section') {
	$tab = filter_var($_POST['tab'],FILTER_SANITIZE_STRING);
	$subtab = filter_var($_POST['subtab'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `field_config_contacts` (`tab`,`subtab`,`accordion`) VALUES ('$tab','$subtab','New Section')");
} else if($_GET['action'] == 'dashboard_fields') {
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "UPDATE `field_config_contacts` SET `contacts_dashboard`='$value' WHERE `tab`='Staff' AND `contacts_dashboard` IS NOT NULL");
} else if($_GET['action'] == 'id_card_fields') {
	set_config($dbc, 'staff_id_card_fields', filter_var($_POST['value'],FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'settings') {
	set_config($dbc, $_POST['name'], filter_var($_POST['value'],FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'db_tabs') {
	set_config($dbc, 'staff_tabs', filter_var($_POST['value'],FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'staff_tabs') {
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS rows FROM general_configuration WHERE name='staff_field_subtabs'"));
	if($get_config['rows'] > 0) {
		$result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$value' WHERE name='staff_field_subtabs'");
	} else {
		$result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('staff_field_subtabs', '$value')");
	}
} else if($_GET['action'] == 'positions') {
	$add_count = 0;
	foreach($_POST['positions'] as $pos) {
		$user = mysqli_fetch_array(mysqli_query($dbc, "SELECT CONCAT(`first_name`,' ',`last_name`) `name` FROM `contacts` WHERE `contactid`='{$_SESSION['contactid']}'"));
		$time = date('Y-m-d H:i:s');
		$count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`position_id`) `positions` FROM `positions` WHERE `name`='$pos' and deleted=0"));
		if($count['positions'] == 0) {
			$sql = "INSERT INTO positions (name, history) VALUES ('$pos', 'Position added from Defaults by {$user['name']} at $time.<br />\n')";
			mysqli_query($dbc, $sql);
			$add_count++;
		}
	}
	if($add_count > 0) {
		echo $add_count." default position(s) added.";
	}
} else if($_GET['action'] == 'positions_fields') {
	$db_config = is_array($_POST['db_config']) ? implode(',',$_POST['db_config']) : $_POST['db_config'];
	set_config($dbc, 'positions_db_config', filter_var($db_config, FILTER_SANITIZE_STRING));
	$field_config = is_array($_POST['field_config']) ? implode(',',$_POST['field_config']) : $_POST['field_config'];
	set_config($dbc, 'positions_field_config', filter_var($field_config, FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'staff_categories') {
	$value = filter_var($_POST['categories'],FILTER_SANITIZE_STRING);
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT configcontactid FROM field_config_contacts WHERE tab='Staff' AND (`categories` IS NOT NULL OR `contacts_dashboard` IS NOT NULL) ORDER BY `categories` IS NULL"));
	if($get_config['configcontactid'] != '') {
		$result_cont_config = mysqli_query($dbc, "UPDATE `field_config_contacts` SET categories = '$value' WHERE configcontactid='{$get_config['configcontactid']}'");
	} else {
		$result_insert_config = mysqli_query($dbc, "INSERT INTO field_config_contacts (`tab`, `categories`) VALUES ('Staff', '$value')");
	}
	set_config($dbc, 'staff_categories_hide', $_POST['categories_hide']);
	set_config($dbc, 'staff_assign_categories', $_POST['assignable']);
} else if($_GET['action'] == 'staff_schedule_lock_fields') {
	//Auto-Lock Settings
	$staff_schedule_autolock = filter_var($_POST['staff_schedule_autolock'],FILTER_SANITIZE_STRING);
	set_config($dbc, 'staff_schedule_autolock', $staff_schedule_autolock);
	$staff_schedule_autolock_month = filter_var($_POST['staff_schedule_autolock_month'],FILTER_SANITIZE_STRING);
	set_config($dbc, 'staff_schedule_autolock_month', $staff_schedule_autolock_month);
	$staff_schedule_autolock_dayofmonth = filter_var($_POST['staff_schedule_autolock_dayofmonth'],FILTER_SANITIZE_STRING);
	set_config($dbc, 'staff_schedule_autolock_dayofmonth', $staff_schedule_autolock_dayofmonth);
	$staff_schedule_autolock_numdays = filter_var($_POST['staff_schedule_autolock_numdays'],FILTER_SANITIZE_STRING);
	set_config($dbc, 'staff_schedule_autolock_numdays', $staff_schedule_autolock_numdays);

	//Auto-Lock Reminder Emails
	$staff_schedule_reminder_emails = filter_var($_POST['staff_schedule_reminder_emails'],FILTER_SANITIZE_STRING);
	set_config($dbc, 'staff_schedule_reminder_emails', $staff_schedule_reminder_emails);
	$staff_schedule_reminder_dates = is_array($_POST['staff_schedule_reminder_dates']) ? implode(',',$_POST['staff_schedule_reminder_dates']) : $_POST['staff_schedule_reminder_dates'];
	$staff_schedule_secondary_reminder_dates = is_array($_POST['staff_schedule_secondary_reminder_dates']) ? implode(',',$_POST['staff_schedule_secondary_reminder_dates']) : $_POST['staff_schedule_secondary_reminder_dates'];
	set_config($dbc, 'staff_schedule_secondary_reminder_dates', $staff_schedule_secondary_reminder_dates);
	set_config($dbc, 'staff_schedule_reminder_dates', is_array($staff_schedule_reminder_dates) ? implode(',',$staff_schedule_reminder_dates) : $staff_schedule_reminder_dates);
	$staff_schedule_reminder_from = filter_var($_POST['staff_schedule_reminder_from'],FILTER_SANITIZE_STRING);
	set_config($dbc, 'staff_schedule_reminder_from', $staff_schedule_reminder_from);
	$staff_schedule_reminder_subject = filter_var($_POST['staff_schedule_reminder_subject'],FILTER_SANITIZE_STRING);
	set_config($dbc, 'staff_schedule_reminder_subject', $staff_schedule_reminder_subject);
	$staff_schedule_reminder_body = filter_var(htmlentities($_POST['staff_schedule_reminder_body']),FILTER_SANITIZE_STRING);
	set_config($dbc, 'staff_schedule_reminder_body', $staff_schedule_reminder_body);
	//Limit Staff
	$staff_schedule_limit_staff = filter_var($_POST['staff_schedule_limit_staff'],FILTER_SANITIZE_STRING);
	set_config($dbc, 'staff_schedule_limit_staff', $staff_schedule_limit_staff);
	$staff_schedule_limit_by_staff = filter_var($_POST['staff_schedule_limit_by_staff'],FILTER_SANITIZE_STRING);
	set_config($dbc, 'staff_schedule_limit_by_staff', $staff_schedule_limit_by_staff);
	$staff_schedule_limit_by_security = filter_var($_POST['staff_schedule_limit_by_security'],FILTER_SANITIZE_STRING);
	set_config($dbc, 'staff_schedule_limit_by_security', $staff_schedule_limit_by_security);


	//Lock Alerts
	$staff_schedule_lock_alert_send = filter_var($_POST['staff_schedule_lock_alert_send'],FILTER_SANITIZE_STRING);
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS rows FROM general_configuration WHERE name='staff_schedule_lock_alert_send'"));
	if($get_config['rows'] > 0) {
		$result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$staff_schedule_lock_alert_send' WHERE name='staff_schedule_lock_alert_send'");
	} else {
		$result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('staff_schedule_lock_alert_send', '$staff_schedule_lock_alert_send')");
	}
	$staff_schedule_lock_alert_from = filter_var($_POST['staff_schedule_lock_alert_from'],FILTER_SANITIZE_STRING);
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS rows FROM general_configuration WHERE name='staff_schedule_lock_alert_from'"));
	if($get_config['rows'] > 0) {
		$result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$staff_schedule_lock_alert_from' WHERE name='staff_schedule_lock_alert_from'");
	} else {
		$result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('staff_schedule_lock_alert_from', '$staff_schedule_lock_alert_from')");
	}
	$staff_schedule_lock_alert_subject = filter_var($_POST['staff_schedule_lock_alert_subject'],FILTER_SANITIZE_STRING);
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS rows FROM general_configuration WHERE name='staff_schedule_lock_alert_subject'"));
	if($get_config['rows'] > 0) {
		$result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$staff_schedule_lock_alert_subject' WHERE name='staff_schedule_lock_alert_subject'");
	} else {
		$result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('staff_schedule_lock_alert_subject', '$staff_schedule_lock_alert_subject')");
	}
	$staff_schedule_lock_alert_body = filter_var(htmlentities($_POST['staff_schedule_lock_alert_body']),FILTER_SANITIZE_STRING);
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS rows FROM general_configuration WHERE name='staff_schedule_lock_alert_body'"));
	if($get_config['rows'] > 0) {
		$result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$staff_schedule_lock_alert_body' WHERE name='staff_schedule_lock_alert_body'");
	} else {
		$result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('staff_schedule_lock_alert_body', '$staff_schedule_lock_alert_body')");
	}
} else if($_GET['action'] == 'set_config') {
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	$name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS rows FROM general_configuration WHERE name='$name'"));
	if($get_config['rows'] > 0) {
		$result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$value' WHERE name='$name'");
	} else {
		$result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('$name', '$value')");
	}
} else if($_GET['action'] == 'set_lock') {
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'staff_schedule_lock_date' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='staff_schedule_lock_date') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$date' WHERE `name`='staff_schedule_lock_date'");
	if(get_config($dbc, 'staff_schedule_lock_alert_send') == 'send') {
		$from = get_config($dbc, 'staff_schedule_lock_alert_from');
		$subject = str_replace('[DATE]',$date,get_config($dbc, 'staff_schedule_lock_alert_subject'));
		$body = str_replace('[DATE]',$date,get_config($dbc, 'staff_schedule_lock_alert_body'));
		$staff = mysqli_query($dbc, "SELECT `email_address`,`office_email` FROM `contacts` WHERE `email_address` != '' AND `user_name` != '' AND `category`='Staff' AND `status`=1 AND `deleted`=0");
		while($email = decryptIt(mysqli_fetch_assoc($staff)[STAFF_EMAIL_FIELD])) {
			try {
				send_email($from,$email,'','',$subject,$body,'');
			} catch (Excpetion $e) {}
		}
	}
} else if($_GET['action'] == 'delete_section') {
	$configcontactid = $_POST['configcontactid'];
	mysqli_query($dbc, "DELETE FROM `field_config_contacts` WHERE `configcontactid` = '$configcontactid'");
} else if($_GET['action'] == 'sort_fields') {
    $all_fields = json_decode($_POST['field_order']);
    foreach ($all_fields as $order => $field_id) {
        mysqli_query($dbc, "UPDATE `field_config_contacts` SET `order` = '$order' WHERE `configcontactid` = '$field_id'");
    }
} else if($_GET['action'] == 'update_url_get_preview') {
	$body = $_POST['body'];
	$expiry_date = $_POST['expiry_date'];

	$body = str_replace(['[FULL_NAME]','[EXPIRY_DATE]'],[get_contact($dbc, $_SESSION['contactid']),$expiry_date],$body).'<br /><br />Click <a href="?">here</a> to access your profile.';
	echo $body;
} else if($_GET['action'] == 'update_url_send_email') {
	$folder_name = $_POST['folder_name'];
	$contacts = $_POST['contacts'];
	$security_level = $_POST['security_level'];
	$expiry_date = $_POST['expiry_date'];
	$subject = $_POST['subject'];
	$body = $_POST['body'];

	if(!empty($categories) || !empty($contacts)) {
		$query = "SELECT * FROM `contacts` WHERE IFNULL(`email_address`,'') != '' AND `deleted` = 0 AND `status` > 0 AND `show_hide_user` = 1 AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY;
		if(!in_array('ALL_CONTACTS',$contacts)) {
			$query .= " AND `contactid` IN (".implode(',', $contacts).")";
		}

		$result = sort_contacts_query(mysqli_query($dbc, $query));
		$error = '';
		foreach($result as $row) {
		    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		    $url_key = '';
		    for ($i = 0; $i < 8; $i++) {
		        $rng = rand(0, strlen($alphabet));
		        $url_key .= substr($alphabet, $rng, 1);
		    }
			$url_key = preg_replace('/[^\p{L}\p{N}\s]/u', '', encryptIt($url_key));
			mysqli_query($dbc, "UPDATE `contacts` SET `update_url_key` = '$url_key', `update_url_expiry` = '$expiry_date', `update_url_role` = '$security_level' WHERE `contactid` = '".$row['contactid']."'");

			$new_body = str_replace(['[FULL_NAME]','[EXPIRY_DATE]'],[$row['full_name'],$expiry_date],$body).'<br /><br />Click <a href="'.WEBSITE_URL.'/Staff/staff_edit.php?contactid='.$row['contactid'].'&update_url=1&url_key='.$url_key.'">here</a> to access your profile.';

			$email = get_email($dbc, $row['contactid']);
			try {
				send_email('', $email, '', '', $subject, $new_body, '');
			} catch (Exception $e) {
                $error .= "Unable to send email: ".$e->getMessage()."\n";
			}
		}
	}

	echo (empty($error) ? 'Successfully sent.' : $error);
}