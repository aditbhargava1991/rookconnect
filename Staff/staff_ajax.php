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
}
else if($_GET['action'] == 'mandatory_field_config') {
	$tab = filter_var($_POST['tab'],FILTER_SANITIZE_STRING);
	$subtab = filter_var($_POST['subtab'],FILTER_SANITIZE_STRING);
	$accordion = filter_var($_POST['accordion'],FILTER_SANITIZE_STRING);
	$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	echo "INSERT INTO `field_config_mandatory_contacts` (`tab`,`subtab`,`accordion`) SELECT '$tab', '$subtab', '$accordion' FROM (SELECT COUNT(*) rows FROM `field_config_mandatory_contacts` WHERE `tab`='$tab' AND `subtab`='$subtab' AND `accordion`='$accordion') num WHERE num.rows = 0";
	mysqli_query($dbc, "INSERT INTO `field_config_mandatory_contacts` (`tab`,`subtab`,`accordion`) SELECT '$tab', '$subtab', '$accordion' FROM (SELECT COUNT(*) rows FROM `field_config_mandatory_contacts` WHERE `tab`='$tab' AND `subtab`='$subtab' AND `accordion`='$accordion') num WHERE num.rows = 0");
	mysqli_query($dbc, "UPDATE `field_config_mandatory_contacts` SET `$field`='$value' WHERE `tab`='$tab' AND `subtab`='$subtab' AND `accordion`='$accordion'");
	echo "UPDATE `field_config_mandatory_contacts` SET `$field`='$value' WHERE `tab`='$tab' AND `subtab`='$subtab' AND `accordion`='$accordion'";
}
 else if($_GET['action'] == 'add_section') {
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
	$staff_schedule_autolock_override_security = filter_var(implode(',',$_POST['staff_schedule_autolock_override_security']),FILTER_SANITIZE_STRING);
	set_config($dbc, 'staff_schedule_autolock_override_security', $staff_schedule_autolock_override_security);

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

}else if($_GET['action'] == 'add_update_staff'){
    $contactid = $_POST['contact_id_val'];
    $first_name = encryptIt(filter_var($_POST['first_name'],FILTER_SANITIZE_STRING));
    $last_name = encryptIt(filter_var($_POST['last_name'],FILTER_SANITIZE_STRING));
    $prefer_name = encryptIt(filter_var($_POST['prefer_name'],FILTER_SANITIZE_STRING));
    $preferred_pronoun = filter_var($_POST['preferred_pronoun'],FILTER_SANITIZE_STRING);
    $home_phone = encryptIt(filter_var($_POST['home_phone'],FILTER_SANITIZE_STRING));
    $office_phone = encryptIt(filter_var($_POST['office_phone'],FILTER_SANITIZE_STRING));
    $cell_phone = encryptIt(filter_var($_POST['cell_phone'],FILTER_SANITIZE_STRING));
    $email_address = encryptIt(filter_var($_POST['email_address'],FILTER_SANITIZE_STRING));
    $office_email = encryptIt(filter_var($_POST['office_email'],FILTER_SANITIZE_STRING));
    $birth_date = filter_var($_POST['birth_date'],FILTER_SANITIZE_STRING);
    
    $website = filter_var($_POST['website'],FILTER_SANITIZE_STRING);
    $bio = filter_var(htmlentities($_POST['bio']),FILTER_SANITIZE_STRING);
    $contactimage = htmlspecialchars($_FILES["contactimage"]["name"], ENT_QUOTES);
    if($_FILES['contactimage']['tmp_name'] != '') {
        list($width, $height) = getimagesize($_FILES['contactimage']['tmp_name']);
        if($width > 800) {
            $ratio = $width / 800;
            $file = imagecreatefromstring( file_get_contents( $_FILES['contactimage']['tmp_name'] ) );
            $compressed = imagecreatetruecolor( $width / $ratio, $height / $ratio );
            imagecopyresampled( $compressed, $file, 0, 0, 0, 0, $width / $ratio, $height / $ratio, $width, $height );
            imagedestroy( $file );
            imagepng( $compressed, "download/".$contactimage ); // adjust format as needed
            imagedestroy( $compressed );
        } else {
            move_uploaded_file($_FILES["contactimage"]["tmp_name"], "download/".$contactimage) ;
        }
    }
    
    $mailing_address = filter_var($_POST['mailing_address'],FILTER_SANITIZE_STRING);
    $city = filter_var($_POST['city'],FILTER_SANITIZE_STRING);
    $province = filter_var($_POST['province'],FILTER_SANITIZE_STRING);
    $country = filter_var($_POST['country'],FILTER_SANITIZE_STRING);
    $postal_code = filter_var($_POST['postal_code'],FILTER_SANITIZE_STRING);
    $position = filter_var($_POST['position'],FILTER_SANITIZE_STRING);
    
    $rating = filter_var($_POST['rating'],FILTER_SANITIZE_STRING);
    $employee_num = filter_var($_POST['employee_num'],FILTER_SANITIZE_STRING);
    $start_date = filter_var($_POST['start_date'],FILTER_SANITIZE_STRING);
    
    $transportation_drivers_license = filter_var($_POST['transportation_drivers_license'],FILTER_SANITIZE_STRING);
    $transportation_drivers_class = filter_var($_POST['transportation_drivers_class'],FILTER_SANITIZE_STRING);
    $transportation_drivers_transmission = filter_var($_POST['transportation_drivers_transmission'],FILTER_SANITIZE_STRING);
    $transportation_drivers_glasses = filter_var($_POST['transportation_drivers_glasses'],FILTER_SANITIZE_STRING);
    $upload_drivers_license = htmlspecialchars($_FILES["upload_drivers_license"]["name"], ENT_QUOTES);
    if(!empty($upload_drivers_license)){
        move_uploaded_file($_FILES["upload_drivers_license"]["tmp_name"], "download/".$upload_drivers_license) ;
    }
    
    $sin = filter_var($_POST['sin'],FILTER_SANITIZE_STRING);
    $bank_name = filter_var($_POST['bank_name'],FILTER_SANITIZE_STRING);
    $bank_institution_number = filter_var($_POST['bank_institution_number'],FILTER_SANITIZE_STRING);
    $bank_transit = filter_var($_POST['bank_transit'],FILTER_SANITIZE_STRING);
    $bank_account_number = filter_var($_POST['bank_account_number'],FILTER_SANITIZE_STRING);
    $upload_blank_cheque = htmlspecialchars($_FILES["upload_blank_cheque"]["name"], ENT_QUOTES);
    if(!empty($upload_blank_cheque)){
        move_uploaded_file($_FILES["upload_blank_cheque"]["tmp_name"], "download/".$upload_blank_cheque) ;
    }
    
    $staff_category = filter_var(implode(',',$_POST['staff_category']),FILTER_SANITIZE_STRING);
    $region = filter_var($_POST['region'],FILTER_SANITIZE_STRING);
    $classification = filter_var($_POST['classification'],FILTER_SANITIZE_STRING);
    $status=$_POST['status'];
    $stored_signature = filter_var(htmlentities($_POST['stored_signature'] != '' ? $_POST['stored_signature'] : $_POST['stored_signature_initial']),FILTER_SANITIZE_STRING);
    
    $initials = filter_var($_POST['initials'],FILTER_SANITIZE_STRING);
    $calendar_color = filter_var($_POST['calendar_color'],FILTER_SANITIZE_STRING);
    
    $linkedin = filter_var($_POST['linkedin'],FILTER_SANITIZE_STRING);
    $facebook = filter_var($_POST['facebook'],FILTER_SANITIZE_STRING);
    $twitter = filter_var($_POST['twitter'],FILTER_SANITIZE_STRING);
    $instagram = filter_var($_POST['instagram'],FILTER_SANITIZE_STRING);
    $pinterest = filter_var($_POST['pinterest'],FILTER_SANITIZE_STRING);
    
    $pri_emergency_first_name = filter_var($_POST['pri_emergency_first_name'],FILTER_SANITIZE_STRING);
    $pri_emergency_last_name = filter_var($_POST['pri_emergency_last_name'],FILTER_SANITIZE_STRING);
    $pri_emergency_cell_phone = filter_var($_POST['pri_emergency_cell_phone'],FILTER_SANITIZE_STRING);
    $pri_emergency_home_phone = filter_var($_POST['pri_emergency_home_phone'],FILTER_SANITIZE_STRING);
    $pri_emergency_email = filter_var($_POST['pri_emergency_email'],FILTER_SANITIZE_STRING);
    $pri_emergency_relation = filter_var($_POST['pri_emergency_relation'],FILTER_SANITIZE_STRING);
    
    $sec_emergency_first_name = filter_var($_POST['sec_emergency_first_name'],FILTER_SANITIZE_STRING);
    $sec_emergency_last_name = filter_var($_POST['sec_emergency_last_name'],FILTER_SANITIZE_STRING);
    $sec_emergency_cell_phone = filter_var($_POST['sec_emergency_cell_phone'],FILTER_SANITIZE_STRING);
    $sec_emergency_home_phone = filter_var($_POST['sec_emergency_home_phone'],FILTER_SANITIZE_STRING);
    $sec_emergency_email = filter_var($_POST['sec_emergency_email'],FILTER_SANITIZE_STRING);
    $sec_emergency_relation = filter_var($_POST['sec_emergency_relation'],FILTER_SANITIZE_STRING);
    
    $emergency_first_name = implode('*#*', $_POST['emergency_first_name']);
    $emergency_last_name = implode('*#*', $_POST['emergency_last_name']);
    $emergency_contact_number = implode('*#*', $_POST['emergency_contact_number']);
    $emergency_relationship = implode('*#*', $_POST['emergency_relationship']);
    
    $health_care_num = filter_var($_POST['health_care_num'],FILTER_SANITIZE_STRING);
    
    $schedule_days = implode(',',$_POST['schedule_days']);
    $scheduled_hours = implode('*',$_POST['scheduled_hours']);
    
    $duns = filter_var($_POST['duns'],FILTER_SANITIZE_STRING);
    $wholesale_price = filter_var($_POST['wholesale_price'],FILTER_SANITIZE_STRING);
    
    $health_concerns = filter_var($_POST['health_concerns'],FILTER_SANITIZE_STRING);
    $health_emergency_procedure = filter_var($_POST['health_emergency_procedure'],FILTER_SANITIZE_STRING);
    $health_medications = filter_var($_POST['health_medications'],FILTER_SANITIZE_STRING);
    
    $health_allergens = filter_var($_POST['health_allergens'],FILTER_SANITIZE_STRING);
    $health_allergens_procedure = filter_var($_POST['health_allergens_procedure'],FILTER_SANITIZE_STRING);
    
    $company_benefit_start_date = filter_var($_POST['company_benefit_start_date'],FILTER_SANITIZE_STRING);
    
    $role = filter_var(','.trim(implode(',',$_POST['role']),',').',',FILTER_SANITIZE_STRING);
    $user_name = $_POST['user_name'];
    $password = encryptIt($_POST['password']);
    $show_hide_user =  filter_var($_POST['show_hide_user'],FILTER_SANITIZE_STRING);
    $region_access = filter_var(implode('#*#', $_POST['region_access']),FILTER_SANITIZE_STRING);
    $location_access = filter_var(implode('#*#', $_POST['location_access']),FILTER_SANITIZE_STRING);
    $classification_access = filter_var(implode('#*#', $_POST['classification_access']),FILTER_SANITIZE_STRING);
    
    $fields = '';
    for($i=0; $i<=62; $i++) {
        $fields .= $_POST['fields_'.$i].'**FFM**';
    }
    $fields = filter_var(htmlentities($fields),FILTER_SANITIZE_STRING);
    $today_date = date('Y-m-d');
    
    if($contactid == ''){ //insert
        $query_insert_inventory = "INSERT INTO `contacts` (`category`, `businessid`, `name`, `first_name`, `last_name`, `prefer_name`, `preferred_pronoun`, `classification`, `region`, `office_phone`, `cell_phone`, `home_phone`, `email_address`, `office_email`, `website`, `position`, `linkedin`, `facebook`, `twitter`, `employee_num`, `sin`, `duns`, `mailing_address`, `business_address`, `postal_code`, `city`, `province`, `country`, `rating`, `schedule_days`, `scheduled_hours`, `birth_date`, `tile_name`, `profile_link`, `staff_category`, `initials`, `calendar_color`, `instagram`,`pinterest`, `bank_name`, `bank_transit`, `bank_institution_number`, `bank_account_number`, `status`, `role`, `show_hide_user`)
		VALUES		('Staff', '', '', '$first_name', '$last_name', '$prefer_name', '$preferred_pronoun', '$classification', '$region', '$office_phone', '$cell_phone', '$home_phone', '$email_address', '$office_email', '$website', '$position', '$linkedin', '$facebook', '$twitter', '$employee_num', '$sin', '$duns', '$mailing_address', '$business_address', '$postal_code', '$city', '$province', '$country', '$rating', '$schedule_days', '$scheduled_hours', '$birth_date','".FOLDER_NAME."', '$profile_link', '$staff_category', '$initials', '$calendar_color', '$instagram','$pinterest','$bank_name','$bank_transit','$bank_institution_number','$bank_account_number', '$status', '$role', '$show_hide_user')";
        $result_insert_inventory = mysqli_query($dbc, $query_insert_inventory);
        $contactid = mysqli_insert_id($dbc);
    }else{ //update
        $query_update_inventory = "UPDATE `contacts` SET `businessid` = '$businessid', `name` = '$name', `first_name` = '$first_name', `last_name` = '$last_name', `prefer_name` = '$prefer_name', `preferred_pronoun` = '$preferred_pronoun', `classification` = '$classification', `region` = '$region', `office_phone` = '$office_phone', `cell_phone` = '$cell_phone', `home_phone` = '$home_phone', `email_address` = '$email_address', `office_email` = '$office_email', `website` = '$website', `position` = '$position', `linkedin` = '$linkedin', `facebook` = '$facebook', `twitter` = '$twitter', `employee_num` = '$employee_num', `sin` = '$sin', `duns` = '$duns', `mailing_address` = '$mailing_address', `postal_code` = '$postal_code', `zip_code` = '$zip_code', `city` = '$city', `province` = '$province', `state` = '$state', `country` = '$country', `rating` = '$rating', `schedule_days` = '$schedule_days', `scheduled_hours` = '$scheduled_hours', `birth_date` = '$birth_date', `staff_category`='$staff_category', `initials`='$initials', `calendar_color`='$calendar_color', `school`='$school', `instagram` = '$instagram', `pinterest` = '$pinterest', `bank_name` = '$bank_name', `bank_transit` = '$bank_transit', `bank_institution_number` = '$bank_institution_number', `bank_account_number` = '$bank_account_number', `status` = '$status' WHERE `contactid` = '$contactid'";
        $result_update_inventory	= mysqli_query($dbc, $query_update_inventory);
    }
    
    if(!empty($_POST['profile_picture_name'])) {
        $original_image = $_POST['profile_picture_name'];
        $new_image = '../Profile/download/profile_pictures/' . $contactid . '.jpg';
        $image_quality = '100';
        
        if (!file_exists('../Profile/download/profile_pictures')) {
            mkdir('../Profile/download/profile_pictures', 0777, true);
        }
        
        list($current_width, $current_height) = getimagesize($original_image);
        
        $x1 = empty($_POST['x1']) ? 0 : $_POST['x1'];
        $y1 = empty($_POST['y1']) ? 0 : $_POST['y1'];
        $x2 = $_POST['x2'];
        $y2 = $_POST['y2'];
        $width = $_POST['image_width'];
        $height = $_POST['image_height'];
        
        $crop_width = 200;
        $crop_height = 200;
        
        $new = imagecreatetruecolor($crop_width, $crop_height);
        $ext = substr($original_image, strrpos($original_image,'.'), strlen($original_image)-1);
        switch ($ext) {
            case '.jpg':
                $current_image = imagecreatefromjpeg($original_image);
                break;
                
            case '.gif':
                $current_image = imagecreatefromgif($original_image);
                $color = imagecolorallocate($new, 255, 255, 255);
                imagefill($new, 0, 0, $color);
                break;
                
            case '.png':
                $current_image = imagecreatefrompng($original_image);
                $color = imagecolorallocate($new, 255, 255, 255);
                imagefill($new, 0, 0, $color);
                break;
        }
        
        imagecopyresampled($new, $current_image, 0, 0, $x1, $y1, $crop_width, $crop_height, $width, $height);
        imagejpeg($new, $new_image, $image_quality);
        
        foreach ($_POST['files_to_delete'] as $deleted_file) {
            unlink ($deleted_file);
        }
        if(!empty($contactid)){
            mysqli_query($dbc, "UPDATE `user_settings` SET `preset_profile_picture` = '' WHERE `contactid` = '$contactid'");
        }
    } else if(!empty($_POST['preset_profile_picture'])) {
        $preset_image = $_POST['preset_profile_picture'];
        if(!empty($contactid)){
            mysqli_query($dbc, "INSERT INTO `user_settings` (`contactid`, `preset_profile_picture`) SELECT '$contactid', '$preset_image' FROM (SELECT COUNT(*) `num_rows` FROM `user_settings` WHERE `contactid`='$contactid') ROWS WHERE ROWS.num_rows = '0'");
            mysqli_query($dbc, "UPDATE `user_settings` SET `preset_profile_picture` = '$preset_image' WHERE `contactid` = '$contactid'");
            unlink ("../Profile/download/profile_pictures/" . $contactid . ".jpg");
        }
    }
    
    $get_desc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(contactdescid) AS contactdescid FROM contacts_description WHERE contactid='$contactid'"));
    if($get_desc['contactdescid'] > 0) {
        $query_update_desc = "UPDATE `contacts_description` SET `bio` = '$bio', `stored_signature`='$stored_signature' WHERE `contactid` = '$contactid'";
        $result_update_desc	= mysqli_query($dbc, $query_update_desc);
    } else {
        $query_insert_desc = "INSERT INTO `contacts_description` (`contactid`, `bio`, `stored_signature`) VALUES ('$contactid', '$bio', '$stored_signature')";
        $result_insert_desc= mysqli_query($dbc, $query_insert_desc);
    }
    
    $get_upload = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(contactuploadid) AS contactuploadid FROM contacts_upload WHERE contactid='$contactid'"));
    if($get_upload['contactuploadid'] > 0) {
        $query_update_doc = "UPDATE `contacts_upload` SET `contactimage` = '$contactimage',`upload_drivers_license` = '$upload_drivers_license', `upload_blank_cheque` = '$upload_blank_cheque' WHERE `contactid` = '$contactid'";
        $result_insert_doc= mysqli_query($dbc, $query_update_doc);
    }else{
        $query_insert_doc = "INSERT INTO `contacts_upload` (`contactid`, `contactimage`, `upload_drivers_license`, `upload_blank_cheque`) VALUES ('$contactid', '$contactimage', '$upload_drivers_license', '$upload_blank_cheque' )";
        $result_insert_doc= mysqli_query($dbc, $query_insert_doc);
    }
    
    $get_dates = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(contactdateid) AS contactdateid FROM contacts_dates WHERE contactid='$contactid'"));
    if($get_dates['contactdateid'] > 0) {
        $query_update_date = "UPDATE `contacts_dates` SET `start_date` = '$start_date', `company_benefit_start_date` = '$company_benefit_start_date' WHERE `contactid` = '$contactid'";
        $result_update_date	= mysqli_query($dbc, $query_update_date);
    } else {
        $query_insert_date = "INSERT INTO `contacts_dates` (`contactid`, `start_date`, `company_benefit_start_date`) VALUES ('$contactid', '$start_date', '$company_benefit_start_date')";
        $result_insert_date = mysqli_query($dbc, $query_insert_date);
    }
    
    $get_cost = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(contactmedicalid) AS contactmedicalid FROM contacts_medical WHERE contactid='$contactid'"));
    if($get_cost['contactmedicalid'] > 0) {
        $query_update_cost = "UPDATE `contacts_medical` SET `transportation_drivers_license` = '$transportation_drivers_license', `transportation_drivers_class` = '$transportation_drivers_class', `transportation_drivers_transmission` = '$transportation_drivers_transmission', `transportation_drivers_glasses` = '$transportation_drivers_glasses', `health_care_num` = '$health_care_num', `health_concerns` = '$health_concerns', `health_emergency_procedure` = '$health_emergency_procedure', `health_medications` = '$health_medications', `health_allergens` = '$health_allergens', `health_allergens_procedure` = '$health_allergens_procedure', `pri_emergency_first_name` = '$pri_emergency_first_name', `pri_emergency_last_name` = '$pri_emergency_last_name', `pri_emergency_cell_phone` = '$pri_emergency_cell_phone', `pri_emergency_home_phone` = '$pri_emergency_home_phone', `pri_emergency_email` = '$pri_emergency_email', `pri_emergency_relation` = '$pri_emergency_relation', `sec_emergency_first_name` = '$sec_emergency_first_name', `sec_emergency_last_name` = '$sec_emergency_last_name', `sec_emergency_cell_phone` = '$sec_emergency_cell_phone', `sec_emergency_home_phone` = '$sec_emergency_home_phone', `sec_emergency_email` = '$sec_emergency_email', `sec_emergency_relation` = '$sec_emergency_relation', `emergency_first_name` = '$emergency_first_name', `emergency_last_name` = '$emergency_last_name', `emergency_contact_number` = '$emergency_contact_number', `emergency_relationship` = '$emergency_relationship' WHERE `contactid` = '$contactid'";
        $result_update_cost	= mysqli_query($dbc, $query_update_cost);
    } else {
        $query_insert_cost = "INSERT INTO `contacts_medical` (`contactid`,  `transportation_drivers_license`, `transportation_drivers_class`, `transportation_drivers_transmission`, `transportation_drivers_glasses`, `health_care_num`, `health_concerns`, `health_emergency_procedure`, `health_medications`, `health_allergens`, `health_allergens_procedure`, `pri_emergency_first_name`, `pri_emergency_last_name`, `pri_emergency_cell_phone`, `pri_emergency_home_phone`, `pri_emergency_email`, `pri_emergency_relation`, `sec_emergency_first_name`, `sec_emergency_last_name`, `sec_emergency_cell_phone`, `sec_emergency_home_phone`, `sec_emergency_email`, `sec_emergency_relation`, `emergency_first_name`, `emergency_last_name`, `emergency_contact_number`, `emergency_relationship`, ) VALUES ('$contactid', '$transportation_drivers_license', '$transportation_drivers_class', '$transportation_drivers_transmission', '$transportation_drivers_glasses', '$health_care_num', '$health_concerns', '$health_emergency_procedure', '$health_medications', '$health_allergens', '$health_allergens_procedure', '$pri_emergency_first_name', '$pri_emergency_last_name', '$pri_emergency_cell_phone', '$pri_emergency_home_phone', '$pri_emergency_email', '$pri_emergency_relation', '$sec_emergency_first_name', '$sec_emergency_last_name', '$sec_emergency_cell_phone', '$sec_emergency_home_phone', '$sec_emergency_email', '$sec_emergency_relation', '$emergency_first_name', '$emergency_last_name', '$emergency_contact_number', '$emergency_relationship')";
        $result_insert_cost = mysqli_query($dbc, $query_insert_cost);
    }
    
    $get_cost = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(contactcostid) AS contactcostid FROM contacts_cost WHERE contactid='$contactid'"));
    if($get_cost['contactcostid'] > 0) {
        $query_update_cost = "UPDATE `contacts_cost` SET `wholesale_price` = '$wholesale_price' WHERE `contactid` = '$contactid'";
        $result_update_cost	= mysqli_query($dbc, $query_update_cost);
    } else {
        $query_insert_cost = "INSERT INTO `contacts_cost` (`contactid`, `wholesale_price`) VALUES ('$contactid', '$wholesale_price')";
        $result_insert_cost = mysqli_query($dbc, $query_insert_cost);
    }
    
    // Update the contacts_security table
    mysqli_query($dbc, "INSERT INTO `contacts_security` (`contactid`) SELECT '$contactid' FROM (SELECT COUNT(*) rows FROM `contacts_security` WHERE `contactid`='$contactid') num WHERE num.rows = 0");
    mysqli_query($dbc, "UPDATE `contacts_security` SET `region_access`='$region_access' WHERE `contactid`='$contactid'");
    mysqli_query($dbc, "UPDATE `contacts_security` SET `location_access`='$location_access' WHERE `contactid`='$contactid'");
    mysqli_query($dbc, "UPDATE `contacts_security` SET `classification_access`='$classification_access' WHERE `contactid`='$contactid'");
    
    $get_risk = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldlevelriskid) AS fieldlevelriskidcnt, fieldlevelriskid FROM hr_absence_report WHERE contactid='$contactid'"));
    if($get_risk['fieldlevelriskidcnt'] > 0) {
        $fieldlevelriskid = $get_risk['fieldlevelriskid'];
        $query_update_employee = "UPDATE `hr_absence_report` SET `fields` = '$fields' WHERE fieldlevelriskid='$fieldlevelriskid'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_site = "INSERT INTO `hr_absence_report` (`today_date`, `contactid`, `fields`) VALUES	('$today_date', '$contactid', '$fields')";
        $result_insert_site	= mysqli_query($dbc, $query_insert_site);
    }
    if(!empty($_POST['output'])){
        $img = sigJsonToImage($_POST['output']);
        imagepng($img, 'absence_report/download/hr_'.$_SESSION['contactid'].'.png');
    }
    
    echo $contactid;
}

}

