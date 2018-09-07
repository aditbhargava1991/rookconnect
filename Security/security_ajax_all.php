<?php include ('../include.php');
ob_clean();

if ( $_GET['fill'] == 'change_role' ) {
	$user = $_SESSION['contactid'];
	$role		= $_GET['role'];
	$id			= $_GET['id'];
	$old_role = mysqli_fetch_array(mysqli_query($dbc, "SELECT `role` FROM `contacts` WHERE `contactid`='$id'"))['role'];
	if(stripos(','.$old_role.',',',super,') === FALSE) {
		$results	= mysqli_query ( $dbc, "UPDATE `contacts` SET `role`='$role' WHERE `contactid`='$id'" );
		$change_log = mysqli_query($dbc, "INSERT INTO `contacts_history` (`updated_by`, `description`, `contactid`) VALUES ('".get_contact($dbc, $user)."', 'Security Level updated from $old_role to $role', '$id')");
	}
}
else if($_GET['fill'] == 'change_regions' ) {
	$user = $_SESSION['contactid'];
	$regions = $_GET['regions'];
	$id			= $_GET['id'];

    $contact = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(contactid) AS total_contact FROM `contacts_security` WHERE contactid='$id'"));
    if($contact['total_contact'] > 0) {
        $results	= mysqli_query ( $dbc, "UPDATE `contacts_security` SET `region_access`='$regions' WHERE `contactid`='$id'" );
    } else {
        $change_log = mysqli_query($dbc, "INSERT INTO `contacts_security` (`contactid`, `region_access`) VALUES ('$id', '$regions')");
    }
}

else if($_GET['fill'] == 'change_locations' ) {
	$user = $_SESSION['contactid'];
	$locations = $_GET['locations'];
	$id			= $_GET['id'];

    $contact = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(contactid) AS total_contact FROM `contacts_security` WHERE contactid='$id'"));
    if($contact['total_contact'] > 0) {
        $results	= mysqli_query ( $dbc, "UPDATE `contacts_security` SET `location_access`='$locations' WHERE `contactid`='$id'" );
    } else {
        $change_log = mysqli_query($dbc, "INSERT INTO `contacts_security` (`contactid`, `location_access`) VALUES ('$id', '$locations')");
    }
}

else if($_GET['fill'] == 'change_classifications' ) {
	$user = $_SESSION['contactid'];
	$classifications = $_GET['classifications'];
	$id			= $_GET['id'];

    $contact = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(contactid) AS total_contact FROM `contacts_security` WHERE contactid='$id'"));
    if($contact['total_contact'] > 0) {
        $results	= mysqli_query ( $dbc, "UPDATE `contacts_security` SET `classification_access`='$classifications' WHERE `contactid`='$id'" );
    } else {
        $change_log = mysqli_query($dbc, "INSERT INTO `contacts_security` (`contactid`, `classification_access`) VALUES ('$id', '$classifications')");
    }
}
else if( $_GET['fill'] == 'change_positions' ) {
	$user = $_SESSION['contactid'];
	$positions_allowed = $_GET['positions'];
	$id			= $_GET['id'];

    $results	= mysqli_query ( $dbc, "UPDATE `contacts` SET `positions_allowed`='$positions_allowed' WHERE `contactid`='$id'" );
}
else if($_GET['fill'] == 'password_generate') {
	$contactid = filter_var($_GET['userid'],FILTER_SANITIZE_STRING);
	if($contactid > 0) {
		$contact = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contactid`, `user_name`, `first_name`, `last_name`, `email_address` FROM `contacts` WHERE `deleted`=0 AND `status`=1 AND IFNULL(`user_name`,'') != '' AND `contactid`='$contactid'"));
		$new_pass = '';
		for($i = 0; $i < 8; $i++) {
			$new_pass .= chr(rand(32,126));
		}
		$email_address = get_email($dbc, $contact['contactid']);
		if($email_address != '') {
			$body = "<p>".decryptIt($contact['first_name']).' '.decryptIt($contact['last_name'])."</p>
				<p>Your password has been reset on <a href='".WEBSITE_URL."'>".WEBSITE_URL."</a>. Your new credentials are as follows:</p>
				<p><pre>Username: ".$contact['user_name']."<br />Password: $new_pass</pre></p>";
			send_email('',$email_address,'','','Information from '.WEBSITE_URL,$body,'');
		}
		echo get_contact($dbc, $contactid)." now has a password of '$new_pass'.";
		$new_pass = encryptIt($new_pass);
		mysqli_query($dbc, "UPDATE `contacts` SET `password`='$new_pass', `password_date`=CURRENT_TIMESTAMP WHERE `contactid`='$contactid' AND `deleted`=0 AND `status`=1");
	} else {
		$contacts = mysqli_query($dbc, "SELECT `contactid`, `user_name`, `first_name`, `last_name`, `email_address` FROM `contacts` WHERE `deleted`=0 AND `status`=1 AND IFNULL(`user_name`,'') != ''");
		while($contact = mysqli_fetch_assoc($contacts)) {
			$new_pass = '';
			for($i = 0; $i < 8; $i++) {
				$new_pass .= chr(rand(32,126));
			}
			$email_address = get_email($dbc, $contact['contactid']);
			if($email_address != '') {
				$body = "<p>".decryptIt($contact['first_name']).' '.decryptIt($contact['last_name'])."</p>
					<p>Your password has been reset on <a href='".WEBSITE_URL."'>".WEBSITE_URL."</a>. Your new credentials are as follows:</p>
					<p><pre>Username: ".$contact['user_name']."<br />Password: $new_pass</pre></p>";
				send_email('',$email_address,'','','Information from '.WEBSITE_URL,$body,'');
			}
			echo decryptIt($contact['first_name']).' '.decryptIt($contact['last_name'])." now has a password of '$new_pass'.<br />\n";
			$new_pass = encryptIt($new_pass);
			mysqli_query($dbc, "UPDATE `contacts` SET `password`='$new_pass', `password_date`=CURRENT_TIMESTAMP WHERE `contactid`='".$contact['contactid']."' AND `deleted`=0 AND `status`=1");
			echo "UPDATE `contacts` SET `password`='$new_pass', `password_date`=CURRENT_TIMESTAMP WHERE `contactid`='".$contact['contactid']."' AND `deleted`=0 AND `status`=1";
		}
	}
}
else if($_GET['fill'] == 'password_require_update') {
	$contactid = filter_var($_GET['userid'],FILTER_SANITIZE_STRING);
	if($contactid > 0) {
		mysqli_query($dbc, "UPDATE `contacts` SET `password_update`=1 WHERE `contactid`='$contactid' AND `deleted`=0 AND `status`=1");
		echo get_contact($dbc, $contactid)." will be required to update password on next login.";
	} else {
		mysqli_query($dbc, "UPDATE `contacts` SET `password_update`=1 WHERE `deleted`=0 AND `status`=1 AND IFNULL(`user_name`,'') != ''");
		echo "All users will be required to update their passwords on their next login.";
	}
}
else if($_GET['fill'] == 'staff_security_settings') {
	$security_level = $_POST['security_level'];
	$subtabs_hidden = implode(',', $_POST['subtabs_hidden']);
	$subtabs_viewonly = implode(',', $_POST['subtabs_viewonly']);
	$fields_hidden = implode(',', $_POST['fields_hidden']);
	$fields_viewonly = implode(',', $_POST['fields_viewonly']);

	$num_rows = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `field_config_staff_security` WHERE `security_level` = '$security_level'"))['num_rows'];
	if($num_rows > 0) {
		mysqli_query($dbc, "UPDATE `field_config_staff_security` SET `subtabs_hidden` = '$subtabs_hidden', `subtabs_viewonly` = '$subtabs_viewonly', `fields_hidden` = '$fields_hidden', `fields_viewonly` = '$fields_viewonly' WHERE `security_level` = '$security_level'");
	} else {
		mysqli_query($dbc, "INSERT INTO `field_config_staff_security` (`security_level`, `subtabs_hidden`, `subtabs_viewonly`, `fields_hidden`, `fields_viewonly`) VALUES ('$security_level', '$subtabs_hidden', '$subtabs_viewonly', '$fields_hidden', '$fields_viewonly')");
	}
}
else if($_GET['fill'] == 'change_role_contact_cat') {
	$role = $_GET['role'];
	$category = $_GET['category'];

	if(!empty($category)) {
		$num_rows = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `field_config_security_contact_categories` WHERE `category` = '$category'"))['num_rows'];
		if($num_rows > 0) {
			mysqli_query($dbc, "UPDATE `field_config_security_contact_categories` SET `role` = '$role' WHERE `category` = '$category'");
		} else {
			mysqli_query($dbc, "INSERT INTO `field_config_security_contact_categories` (`category`, `role`) VALUES ('$category', '$role')");
		}
	}
}
?>