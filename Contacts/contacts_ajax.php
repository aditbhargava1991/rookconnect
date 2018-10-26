<?php include('../include.php');
require_once('../phpsign/signature-to-image.php');
error_reporting(0);
checkAuthorised();
ob_clean();

if($_GET['action'] == 'setting_quick_icon') {
    $tab_list = $_GET['tab_list'];
	set_config($dbc, 'contact_quick_action_icons', filter_var($tab_list,FILTER_SANITIZE_STRING));
}

if($_GET['action'] == 'contact_highlight') {
	$contactid = $_GET['contactid'];
	$contactcolor = $_GET['contactcolor'];

    mysqli_query($dbc, "UPDATE `contacts` SET `flag_colour`='$contactcolor' WHERE `contactid`='$contactid'");
}

if($_GET['action'] == 'contact_fields') {
	$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
	$fields = filter_var($_POST['field_list'],FILTER_SANITIZE_STRING);
	$tile = filter_var($_POST['tile'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `field_config_contacts` (`tab`,`tile_name`,`subtab`) SELECT '$category', '$tile', '**no_subtab**' FROM (SELECT COUNT(*) rows FROM `field_config_contacts` WHERE `tab`='$category' AND `tile_name`='$tile' AND `subtab`='**no_subtab**' AND `subtab` != 'additions') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `field_config_contacts` SET `contacts`='$fields' WHERE `tab`='$category' AND `tile_name`='$tile' AND `subtab` = '**no_subtab**' AND `subtab` != 'additions'");
}
else if($_GET['action'] == 'contact_mandatory_fields') {
	$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
	$fields = filter_var($_POST['field_list'],FILTER_SANITIZE_STRING);
	$tile = filter_var($_POST['tile'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `field_config_contacts` (`tab`,`tile_name`,`subtab`,`mandatory`) SELECT '$category', '$tile', '**no_subtab**',1 FROM (SELECT COUNT(*) rows FROM `field_config_contacts` WHERE `tab`='$category' AND `tile_name`='$tile' AND `subtab`='**no_subtab**' AND `subtab` != 'additions' AND `mandatory` = 1) num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `field_config_contacts` SET `contacts`='$fields' WHERE `tab`='$category' AND `tile_name`='$tile' AND `subtab` = '**no_subtab**' AND `subtab` != 'additions' AND `mandatory` = 1");
}
else if($_GET['action'] == 'contacts_dashboards') {
	$tab_dashboard = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
	$contacts_dashboards = $_POST['field_list'];
	$tile = filter_var($_POST['tile'],FILTER_SANITIZE_STRING);
	if (strpos(','.$contacts_dashboards.',',','.'Category'.',') === false) {
		$contacts_dashboards = 'Category,'.$contacts_dashboards;
	}

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configcontactid) AS configcontactid FROM field_config_contacts WHERE `tile_name`='$tile' AND tab='$tab_dashboard' AND accordion IS NULL"));
	if($get_field_config['configcontactid'] > 0) {
		$query_update_employee = "UPDATE `field_config_contacts` SET contacts_dashboard = '$contacts_dashboards' WHERE tab='$tab_dashboard' AND `tile_name`='$tile'";
		$result_update_employee = mysqli_query($dbc, $query_update_employee);
	} else {
		$query_insert_config = "INSERT INTO `field_config_contacts` (`tab`, `contacts_dashboard`, `tile_name`) VALUES ('$tab_dashboard', '$contacts_dashboards', '$tile')";
		$result_insert_config = mysqli_query($dbc, $query_insert_config);
	}
} else if($_GET['action'] == 'contacts_summary') {
	$contacts_summary = $_POST['field_list'];
	$tile = filter_var($_POST['tile'],FILTER_SANITIZE_STRING);
    $heading = $tile.'_summary';

    set_config($dbc, $heading, $contacts_summary);

} else if($_GET['action'] == 'contact_configs') {
	$name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	$folder = filter_var($_POST['tile'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `field_config_contacts` (`tile_name`) SELECT '$folder' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `tile_name`='$folder') num WHERE num.rows = 0");
	mysqli_query($dbc, "UPDATE `field_config_contacts` SET `$name`='$value' WHERE `tile_name`='$folder'");
} else if($_GET['action'] == 'general_config') {
	$name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT '$name' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='$name') num WHERE num.rows = 0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$value' WHERE `name`='$name'");
	if(isset($_POST['business_category'])) {
		$category = filter_var($_POST['business_category'],FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'business_category' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='business_category') num WHERE num.rows = 0");
		mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$category' WHERE `name`='business_category'");
	}
} else if($_GET['action'] == 'classification_logos') {
    if(!file_exists('download')) {
        mkdir('download', 0777, true);
    }
	$name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
	$counter = filter_var($_POST['counter'],FILTER_SANITIZE_STRING);
	$folder_name = filter_var($_POST['folder_name'],FILTER_SANITIZE_STRING);
	$options = [];
	for ($i = 0; $i < $counter; $i++) {
		if(!empty($_FILES[$i]['name'])) {
			$filename = $basename = preg_replace('/[^a-z0-9.]*/','',strtolower($_FILES[$i]['name']));
	        $j = 0;
	        while(file_exists('download/'.$filename)) {
	            $filename = preg_replace('/(\.[a-z0-9]*)/', ' ('.++$j.')$1', $basename);
	        }
	        move_uploaded_file($_FILES[$i]['tmp_name'], 'download/'.$filename);
	        $options[] = WEBSITE_URL.'/Contacts/download/'.$filename;
	        echo WEBSITE_URL.'/Contacts/download/'.$filename;
		} else {
			$options[] = $_POST[$i];
		}
	}
	$options = implode('*#*', $options);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT '$name' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='$name') num WHERE num.rows = 0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$options' WHERE `name`='$name'");
} else if($_GET['action'] == 'contact_additions') {
	$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
	$fields = filter_var($_POST['field_list'],FILTER_SANITIZE_STRING);
	$tile = filter_var($_POST['tile'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `field_config_contacts` (`tab`,`tile_name`,`subtab`) SELECT '$category', '$tile', 'additions' FROM (SELECT COUNT(*) rows FROM `field_config_contacts` WHERE `tab`='$category' AND `tile_name`='$tile' AND `subtab`='additions') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `field_config_contacts` SET `contacts`='$fields' WHERE `tab`='$category' AND `tile_name`='$tile' AND `subtab`='additions'");
} else if($_GET['action'] == 'archive') {
	$contactid = $_GET['contactid'];
    $date_of_archival = date('Y-m-d');
	mysqli_query($dbc, "UPDATE `contacts` SET `deleted`='1', `date_of_archival` = '$date_of_archival' WHERE `contactid`='$contactid'");
	$before_change = "The contact was Active.";
	add_history($dbc, "This contact has been archived.", $contactid, $before_change);
} else if($_GET['action'] == 'status_change') {
	$contactid = $_GET['contactid'];
	$status = $_GET['new_status'];
	//$before_change = "$name is ". get_contact($dbc, $contactid, 'status') ."\n";
	mysqli_query($dbc, "UPDATE `contacts` SET `status`='$status' WHERE `contactid`='$contactid'");
	add_history($dbc, "Contact status set to ".($status > 0 ? 'Active' : 'Inactive'), $contactid);
} else if($_GET['action'] == 'contact_values') {
	$history = '';
	$user_id = $_SESSION['contactid'];
	$user_name = get_contact($dbc, $user_id);
	$field_name = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$field_category = filter_var($_POST['field_category'],FILTER_SANITIZE_STRING);
	$table_name = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
	$is_json = json_decode($_POST['value']);
	if (json_last_error() == JSON_ERROR_NONE) {
		$field_value = $is_json;
	} else {
		$field_value = filter_var(htmlentities($_POST['value']),FILTER_SANITIZE_STRING);
	}
	if(is_array($field_value)) {
		if(!empty($_POST['delimiter'])) {
			$field_value = implode($_POST['delimiter'],$field_value);
		} else {
			$field_value = implode(',',$field_value);
		}
	}
	if(!empty($_POST['append_last'])) {
		$field_value .= $_POST['append_last'];
	}
	$history_value = $field_value;

	//Create a record if it does not yet exist
	if($_POST['contactid'] > 0) {
		$contactid = $_POST['contactid'];
	} else {
		$folder_name = filter_var($_POST['tile_name'],FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`) VALUES ('".$folder_name."')");
		$contactid = mysqli_insert_id($dbc);
		echo $contactid;
		$history .= "New contact record created by $user_name.<br />\n";
	}
	$row_field = $row_id = $row_sql = $row_history = '';
	if(isset($_POST['row_id'])) {
		$row_field = filter_var($_POST['row_field'],FILTER_SANITIZE_STRING);
		if($_POST['row_id'] > 0) {
			$row_id = filter_var($_POST['row_id'],FILTER_SANITIZE_STRING);
		} else {
			if(isset($_POST['contactid_field'])) {
				$contactid_field = $_POST['contactid_field'];
				if(isset($_POST['contactid_category_field'])) {
					$contactid_category_field = $_POST['contactid_category_field'];
					$contactid_category = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `category` FROM `contacts` WHERE `contactid` = '$contactid'"))['category'];
					mysqli_query($dbc, "INSERT INTO `$table_name` (`$contactid_field`, `$contactid_category_field`) VALUES ('$contactid', '$contactid_category')");
				} else {
					mysqli_query($dbc, "INSERT INTO `$table_name` (`$contactid_field`) VALUES ('$contactid')");
				}
			} else {
				mysqli_query($dbc, "INSERT INTO `$table_name` (`contactid`) VALUES ('$contactid')");
			}
			$row_id = mysqli_insert_id($dbc);
			echo $row_id;
		}
		$row_sql = " AND `$row_field`='$row_id'";
		$row_history = "(In row $row_id.)";
	}

	//Encrypt fields that need to be encrypted
	if(in_array($field_name, ['name', 'first_name', 'last_name', 'prefer_name', 'password', 'office_phone', 'cell_phone', 'home_phone', 'email_address', 'second_email_address', 'office_email','company_email', 'business_street', 'business_city', 'business_state', 'business_country', 'business_zip', 'health_care_no'])) {
		$field_value = encryptIt($field_value);
	}
    if(isset($_POST['contact_category']) && isset($_POST['replicating_fieldname'])) {
        $folder_name = filter_var($_POST['tile_name'],FILTER_SANITIZE_STRING);
        $contact_category = filter_var($_POST['contact_category'],FILTER_SANITIZE_STRING);
        $replicating_fieldname = filter_var($_POST['replicating_fieldname'],FILTER_SANITIZE_STRING);
        mysqli_query($dbc, "INSERT INTO `$table_name` (`tile_name`, `category`, `$field_name`) VALUES ('$folder_name', '$contact_category', '$field_value')");
        $new_contactid = mysqli_insert_id($dbc);
        mysqli_query($dbc, "UPDATE `$table_name` SET `$replicating_fieldname`='$new_contactid' WHERE `contactid`='$contactid'");
    }

    /* Common */
    $folder_name = filter_var($_POST['tile_name'],FILTER_SANITIZE_STRING);
    $row = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contactid`, `name`, `site_name` FROM `contacts` WHERE `contactid`='$contactid'"));
    $id_field = '';
    if ( !empty($row['name']) ) {
        $id_field = 'businessid';
    } elseif ( !empty($row['site_name']) ) {
        $id_field = 'siteid';
    }

    /* Assign selected contact to Business or Site */
    if (isset($_POST['contact_id'])) {
        $contact_id = filter_var($_POST['contact_id'],FILTER_SANITIZE_STRING);
        mysqli_query($dbc, "UPDATE `$table_name` SET `$id_field`='$contactid' WHERE `contactid`='$contact_id'");
    }

    /* Add new contact */
    if(isset($_SESSION['new_contactid'])) {
        if (isset($_POST['new_category'])) {
            $new_category = filter_var($_POST['new_category'],FILTER_SANITIZE_STRING);
            mysqli_query($dbc, "UPDATE `$table_name` SET `category`='$new_category' WHERE `contactid`={$_SESSION['new_contactid']}");
        } elseif (isset($_POST['new_first_name'])) {
            $new_first_name = filter_var($_POST['new_first_name'],FILTER_SANITIZE_STRING);
            $new_first_name = encryptIt($new_first_name);
            mysqli_query($dbc, "UPDATE `$table_name` SET `first_name`='$new_first_name' WHERE `contactid`={$_SESSION['new_contactid']}");
        } elseif (isset($_POST['new_last_name'])) {
            $new_last_name = filter_var($_POST['new_last_name'],FILTER_SANITIZE_STRING);
            $new_last_name = encryptIt($new_last_name);
            mysqli_query($dbc, "UPDATE `$table_name` SET `last_name`='$new_last_name' WHERE `contactid`={$_SESSION['new_contactid']}");
        }
        $row = mysqli_fetch_assoc($dbc,"SELECT `contactid`, `category`, `first_name`, `last_name` WHERE `contactid`={$_SESSION['new_contactid']}");
        if ( !empty($row['category']) && !empty($row['first_name']) && !empty($row['last_name']) ) {
			if(session_status() == PHP_SESSION_NONE) {
				session_start(['cookie_lifetime' => 518400]);
				$_SERVER['page_load_info'] .= 'Session Started: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
			}
            unset($_SESSION['new_contactid']);
			session_write_close();
        }
    } elseif (isset($_POST['new_category'])) {
        $new_category = filter_var($_POST['new_category'],FILTER_SANITIZE_STRING);
        mysqli_query($dbc, "INSERT INTO `$table_name` (`tile_name`, `category`) VALUES ('$folder_name', '$new_category')");
		if(session_status() == PHP_SESSION_NONE) {
			session_start(['cookie_lifetime' => 518400]);
			$_SERVER['page_load_info'] .= 'Session Started: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
		}
        $_SESSION['new_contactid'] = mysqli_insert_id($dbc);
		session_write_close();
    } elseif (isset($_POST['new_first_name'])) {
        $new_first_name = filter_var($_POST['new_first_name'],FILTER_SANITIZE_STRING);
        $new_first_name = encryptIt($new_first_name);
        mysqli_query($dbc, "INSERT INTO `$table_name` (`tile_name`, `first_name`) VALUES ('$folder_name', '$new_first_name')");
		if(session_status() == PHP_SESSION_NONE) {
			session_start(['cookie_lifetime' => 518400]);
			$_SERVER['page_load_info'] .= 'Session Started: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
		}
        $_SESSION['new_contactid'] = mysqli_insert_id($dbc);
		session_write_close();
    } elseif (isset($_POST['new_last_name'])) {
        $new_last_name = filter_var($_POST['new_last_name'],FILTER_SANITIZE_STRING);
        $new_last_name = encryptIt($new_last_name);
        mysqli_query($dbc, "INSERT INTO `$table_name` (`tile_name`, `last_name`) VALUES ('$folder_name', '$new_last_name')");
		if(session_status() == PHP_SESSION_NONE) {
			session_start(['cookie_lifetime' => 518400]);
			$_SERVER['page_load_info'] .= 'Session Started: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
		}
        $_SESSION['new_contactid'] = mysqli_insert_id($dbc);
		session_write_close();
    }

	if($field_value == 'upload') {
		if (!file_exists('../'.ucfirst($_POST['tile_name']).'/download')) {
			mkdir('../'.ucfirst($_POST['tile_name']).'/download', 0777, true);
		}
		$history_value = filter_var($_FILES['file']['name'],FILTER_SANITIZE_STRING);
		$basename = $filename = $_FILES['file']['name'];
		$i = 0;
		while(file_exists('../'.ucfirst($_POST['tile_name']).'/download/'.$filename)) {
			$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.++$i.')$1', $basename);
		}
		move_uploaded_file($_FILES['file']['tmp_name'],'../'.ucfirst($_POST['tile_name']).'/download/'.$filename);
		$field_value = $filename;
	} else if($field_name == 'billable_hours') {
		$field_value = time_time2decimal($field_value);
	}

	//Store the value and record the history
	if(isset($_POST['contactid_field'])) {
		$contactid_field = $_POST['contactid_field'];
		if(isset($_POST['contactid_category_field'])) {
			$contactid_category_field = $_POST['contactid_category_field'];
			$contactid_category = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `category` FROM `contacts` WHERE `contactid` = '$contactid'"))['category'];
			mysqli_query($dbc, "INSERT INTO `$table_name` (`$contactid_field`, `$contactid_category_field`) SELECT '$contactid', '$contactid_category' FROM (SELECT COUNT(*) rows FROM `$table_name` WHERE `$contactid_field`='$contactid') num WHERE num.rows=0");
		} else {
			mysqli_query($dbc, "INSERT INTO `$table_name` (`$contactid_field`) SELECT '$contactid' FROM (SELECT COUNT(*) rows FROM `$table_name` WHERE `$contactid_field`='$contactid') num WHERE num.rows=0");
		}
		mysqli_query($dbc, "UPDATE `$table_name` SET `$field_name`='$field_value' WHERE `$contactid_field`='$contactid' $row_sql");
	} else {
		if(isset($_POST['no_contactid']) && $_POST['no_contactid'] == 'true') {
			mysqli_query($dbc, "UPDATE `$table_name` SET `$field_name`='$field_value' WHERE 1 $row_sql");
		} else{
			mysqli_query($dbc, "INSERT INTO `$table_name` (`contactid`) SELECT '$contactid' FROM (SELECT COUNT(*) rows FROM `$table_name` WHERE `contactid`='$contactid') num WHERE num.rows=0");
			// echo "UPDATE `$table_name` SET `$field_name`='$field_value' WHERE `contactid`='$contactid' $row_sql";
			mysqli_query($dbc, "UPDATE `$table_name` SET `$field_name`='$field_value' WHERE `contactid`='$contactid' $row_sql");
			if($table_name != 'contacts') {
				try {
					mysqli_query($dbc, "UPDATE `$table_name` SET `category`='$field_category' WHERE `contactid`='$contactid' $row_sql");
				} catch(Exception $e) { }
			}

			// Update all contacts that are synced with this one
			$contacts_sync = get_contact($dbc, $contactid, 'contacts_sync');
			foreach(explode(',',$contacts_sync) as $contact_sync) {
				if($contact_sync != $contactid) {
					mysqli_query($dbc, "UPDATE `$table_name` SET `$field_name`='$field_value' WHERE `contactid`='$contact_sync' $row_sql");
				}
			}
		}
	}
	if($field_name == 'password') {
		mysqli_query($dbc, "UDPATE `contacts` SET `password_update`=0, `password_date`=CURRENT_TIMESTAMP WHERE `contactid`='$contactid'");
		$history_value = '********';
	}
	$history .= $_POST['label']." set to '$history_value' for contact record [$contactid] by $user_name. $row_history<br />\n";
	$before_change = $_POST['label'] . " is " . get_contact($dbc, $contactid, $field_name) ."<br \>\n";
	add_history($dbc, $history, $contactid, $before_change);

	// Create or Sync Site if selected
	if(in_array($field_name, ['business_address','business_street','business_city','business_state','business_zip','business_country','business_site_sync'])) {
		$site_id = $dbc->query("SELECT `contactid` FROM `contacts` WHERE `category`='".SITES_CAT."' AND `businessid`='$contactid' AND `deleted`=0 AND `status` > 0")->fetch_assoc()['contactid'];
		if($site_id > 0) {
			$dbc->query("UPDATE `contacts` `s` LEFT JOIN `contacts` `c` ON `c`.`contactid`=`s`.`businessid` SET `s`.`business_address`=`c`.`business_address`, `s`.`business_street`=`c`.`business_street`, `s`.`business_city`=`c`.`business_city`, `s`.`business_state`=`c`.`business_state`, `s`.`business_zip`=`c`.`business_zip`, `s`.`business_country`=`c`.`business_country` WHERE `s`.`contactid`='$site_id' AND `c`.`business_site_sync` > 0");
		} else {
			$site_name = get_contact($dbc, $contactid, 'name_company').' Site';
			$dbc->query("INSERT INTO `contacts` (`businessid`, `site_name`, `category`, `business_address`, `business_street`, `business_city`, `business_state`, `business_zip`, `business_country`) SELECT `contactid`, '$site_name', '".SITES_CAT."' `business_address`, `business_street`, `business_city`, `business_state`, `business_zip`, `business_country` FROM `contacts` WHERE `contactid`='$contactid' AND `business_site_sync` > 0");
			$site_id = mysqli_insert_id($dbc);
		}
		echo '#'.$site_id;
	} else if(in_array($field_name, ['mailing_address','ship_to_address','ship_city','ship_state','ship_zip','ship_country','mailing_site_sync',])) {
		$site_id = $dbc->query("SELECT `contactid` FROM `contacts` WHERE `category`='".SITES_CAT."' AND `businessid`='$contactid' AND `deleted`=0 AND `status` > 0")->fetch_assoc()['contactid'];
		if($site_id > 0) {
			$dbc->query("UPDATE `contacts` `s` LEFT JOIN `contacts` `c` ON `c`.`contactid`=`s`.`businessid` SET `s`.`mailing_address`=`c`.`mailing_address`, `s`.`ship_to_address`=`c`.`ship_to_address`, `s`.`ship_city`=`c`.`ship_city`, `s`.`ship_state`=`c`.`ship_state`, `s`.`ship_zip`=`c`.`ship_zip`, `s`.`ship_country`=`c`.`ship_country` WHERE `s`.`contactid`='$site_id' AND `c`.`mailing_site_sync` > 0");
		} else {
			$site_name = get_contact($dbc, $contactid, 'name_company').' Site';
			$dbc->query("INSERT INTO `contacts` (`businessid`, `site_name`, `category`, `mailing_address`, `ship_to_address`, `ship_city`, `ship_state`, `ship_zip`, `ship_country`) SELECT `contactid`, '$site_name', '".SITES_CAT."', `mailing_address`, `ship_to_address`, `ship_city`, `ship_state`, `ship_zip`, `ship_country` FROM `contacts` WHERE `contactid`='$contactid' AND `mailing_site_sync` > 0");
			$site_id = mysqli_insert_id($dbc);
		}
		echo '#'.$site_id;
	} else if(in_array($field_name, ['address','city','postal_code','state','country','address_site_sync'])) {
		$site_id = $dbc->query("SELECT `contactid` FROM `contacts` WHERE `category`='".SITES_CAT."' AND `businessid`='$contactid' AND `deleted`=0 AND `status` > 0")->fetch_assoc()['contactid'];
		if($site_id > 0) {
			$dbc->query("UPDATE `contacts` `s` LEFT JOIN `contacts` `c` ON `c`.`contactid`=`s`.`businessid` SET `s`.`address`=`c`.`address`, `s`.`city`=`c`.`city`, `s`.`state`=`c`.`state`, `s`.`postal_code`=`c`.`postal_code`, `s`.`country`=`c`.`country`, `s`.`key_number` = `c`.`key_number`, `s`.`door_code_number` = `c`.`door_code_number`, `s`.`alarm_code_number` = `c`.`alarm_code_number` WHERE `s`.`contactid`='$site_id' AND `c`.`address_site_sync` > 0");
		} else {
			$site_name = get_contact($dbc, $contactid, 'name_company').' Site';
			$dbc->query("INSERT INTO `contacts` (`businessid`, `site_name`, `category`, `address`, `city`, `state`, `postal_code`, `country`, `key_number`, `door_code_number`, `alarm_code_number`) SELECT `contactid`, '$site_name', '".SITES_CAT."', `address`, `city`, `state`, `postal_code`, `country`, `key_number`, `door_code_number`, `alarm_code_number` FROM `contacts` WHERE `contactid`='$contactid' AND `address_site_sync` > 0");
			$site_id = mysqli_insert_id($dbc);
		}
		echo '#'.$site_id;
	} else if(in_array($field_name, ['first_name','last_name','name'])) {
		$site_id = $dbc->query("SELECT `contactid` FROM `contacts` WHERE `category`='".SITES_CAT."' AND `businessid`='$contactid' AND `deleted`=0 AND `status` > 0")->fetch_assoc()['contactid'];
		if($site_id > 0) {
			$site_name = get_contact($dbc, $contactid, 'name_company').' Site';
			$dbc->query("UPDATE `contacts` `s` LEFT JOIN `contacts` `c` ON `c`.`contactid`=`s`.`businessid` SET `s`.`site_name` = '$site_name' WHERE `s`.`contactid`='$site_id' AND `c`.`address_site_sync` > 0");
		}
		echo '#'.$site_id;
	}
} else if($_GET['action'] == 'table_locks') {
	$user_id = filter_var($_POST['session_id'],FILTER_SANITIZE_STRING);
	$table_row = filter_var($_POST['contactid'],FILTER_SANITIZE_STRING);

	//Check if anybody is using the currently requested section
	$locked = [];
	$messages = [];
	foreach($_POST['section'] as $section_name) {
		$section_name = filter_var($section_name,FILTER_SANITIZE_STRING);
		$current_locks = mysqli_fetch_array(mysqli_query($dbc, "SELECT `user_id` FROM `table_locks` WHERE TIMEDIFF(CURRENT_TIMESTAMP,`locked_at`) < '00:10:00' AND `table_name`='contacts' AND `tab_name`='$section_name' AND `table_row_id`='$table_row' AND `user_id` != '$user_id'"));
		if($current_locks['user_id'] > 0) {
			$locked[] = $section_name;
			$messages[] = get_contact($dbc, $current_locks['user_id'])." has a lock on the $section_name tab.\n";
		} else {
			//Create a row for the user if it doesn't exist
			mysqli_query($dbc, "INSERT INTO `table_locks` (`user_id`, `tab_name`) SELECT '$user_id', '$section_name' FROM (SELECT COUNT(*) rows FROM `table_locks` WHERE `user_id`='$user_id' AND `tab_name`='$section_name') num WHERE num.rows=0");
			//Mark the section as locked by the current user
			mysqli_query($dbc, "UPDATE `table_locks` SET `locked_at`=CURRENT_TIMESTAMP, `table_name`='contacts', `tab_name`='$section_name', `table_row_id`='$table_row' WHERE `user_id`='$user_id' AND `tab_name`='$section_name'");
		}
	}
	echo implode(',',$locked).'#*#'.implode('',$messages);
} else if($_GET['action'] == 'unlock_table') {
	//Clear the lock on the section of the table
	$user_id = filter_var($_POST['session_id'],FILTER_SANITIZE_STRING);
	$table_row = filter_var($_POST['contactid'],FILTER_SANITIZE_STRING);
	$section = filter_var($_POST['section'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "DELETE FROM `table_locks` WHERE `table_name`='contacts' AND `user_id`='$user_id' AND `table_row_id`='$table_row'");
} else if($_GET['action'] == 'delete_medication_upload') {
	$meduploadid = $_POST['meduploadid'];
	mysqli_query($dbc, "DELETE FROM `medication_uploads` WHERE `meduploadid` = '$meduploadid'");
} else if($_GET['action'] == 'contacts_regions') {
	// $tile_name = $_POST['tile_name'];
	// $regionid = $_POST['regionid'];
	// $region_name = $_POST['region_name'];
	// if(!empty($regionid)) {
	// 	mysqli_query($dbc, "UPDATE `contacts_regions` SET `name` = '$region_name' WHERE `regionid` = '$regionid'");
	// } else {
	// 	mysqli_query($dbc, "INSERT INTO `contacts_regions` (`tile_name`, `name`) VALUES ('$tile_name', '$region_name')");
	// 	$regionid = mysqli_insert_id($dbc);
	// }
	// echo $regionid;
} else if($_GET['action'] == 'contacts_regions_remove') {

}

if($_GET['action'] == 'send_alert') {
	$to      = $_POST['to'];
	$from    = filter_var($_POST['from'],FILTER_SANITIZE_EMAIL);
	$subject = filter_var($_POST['subject'],FILTER_SANITIZE_STRING);
	$body    = filter_var($_POST['body'],FILTER_SANITIZE_STRING);
	$schedule    = filter_var($_POST['schedule'],FILTER_SANITIZE_STRING);
	$result = '';
    $errors        = '';
    $error_send    = '';
    $error_noemail = '';

    foreach( $to as $user ) {
		if($user > 0) {
			$contact = get_contact($dbc, $user);
		} else {
			$contact = get_securitylevel($dbc, $user);
		}
		if($schedule == '' && $user > 0) {
			$user_email = get_email($dbc, $user);
			if($user_email != '') {
				try {
					send_email($from, $user_email, '', '', $subject, $body, '');
					$result .= "Alert sent to ".$contact."\n";
				} catch(Exception $e) {
					$result .= "Unable to email ".$contact.". Please try again later.\n";
				}
			} else {
				$result .= 'No email address are available for '.$contact."\n";
			}
		} else if($schedule == '') {
			foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `first_name`, `last_name`, `category`, `email_address` FROM `contacts` WHERE `deleted`=0 AND `status` > 0 AND CONCAT(',',`role`,',') LIKE '%,$user,%' AND '$user' != '' AND IFNULL(`email_address`,'') != ''")) as $contact) {
				try {
					send_email($from, $contact['email_address'], '', '', $subject, $body, '');
					$result .= "Alert sent to ".$contact['first_name'].' '.$contact['last_name']."\n";
				} catch(Exception $e) {
					$result .= "Unable to email ".$contact['first_name'].' '.$contact['last_name'].". Please try again later.\n";
				}
			}
		} else {
			if(!$dbc->query("INSERT INTO `reminders` (`".($user > 0 ? 'contactid' : 'recipient')."`,`reminder_date`,`reminder_type`,`subject`,`body`,`sender`) VALUES ('".($user > 0 ? $user : 'LEVEL:'.$user)."','$schedule','Contact','$subject','".htmlentities($body)."','$from')")) {
				$result .= "Unable to schedule alert for $schedule for $contact.\n";
			} else {
				$result .= "Alert scheduled for $schedule for $contact.\n";
			}
		}
    }

    if ( empty($result) ) {
        echo 'No User Selected.';
    } else {
        echo $result;
    }
}

function add_history($dbc, $history, $contactid, $before_change='') {
	$user_name = get_contact($dbc, $_SESSION['contactid']);
	$history = filter_var(htmlentities($history),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `contacts_history` (`updated_by`, `contactid`) SELECT '$user_name', '$contactid' FROM (SELECT COUNT(*) rows FROM `contacts_history` WHERE `updated_by`='$user_name' AND `contactid`='$contactid' AND TIMEDIFF(CURRENT_TIMESTAMP,`updated_at`) < '00:30:00') num WHERE num.rows = 0");
	mysqli_query($dbc, "UPDATE `contacts_history` SET `before_change`=CONCAT(IFNULL(`before_change`,''),'$before_change'), `description`=CONCAT(IFNULL(`description`,''),'$history'), `updated_at` = now() WHERE `updated_by`='$user_name' AND `contactid`='$contactid' AND TIMEDIFF(CURRENT_TIMESTAMP,`updated_at`) < '00:30:00'");
}

if($_GET['action'] == 'save_guardian_tabs') {
    $tab = filter_var($_POST['tab'], FILTER_SANITIZE_STRING);
    $tab_updated = '#*#' . $tab;

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) AS `configid`, `value` FROM `general_configuration` WHERE `name`='guardian_type_tabs'"));

    if($get_config['configid'] > 0) {
        if ( strpos($get_config['value'], $tab ) === false ) {
            $tabs = $get_config['value'] . $tab_updated;
            $query_update = "UPDATE `general_configuration` SET `value`='$tabs' WHERE `name`='guardian_type_tabs'";
            $result_update = mysqli_query($dbc, $query_update);
        }
    } else {
        $query_insert = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('guardian_type_tabs', '$tab')";
        $result_insert = mysqli_query($dbc, $query_insert);
    }
}

if($_GET['action'] == 'add_marsheet_row') {
	$marsheetid = $_GET['marsheetid'];

	mysqli_query($dbc, "INSERT INTO `marsheet_row` (`marsheetid`) VALUES ('$marsheetid')");
	$marsheetrowid = mysqli_insert_id($dbc);

	echo $marsheetrowid;
} else if($_GET['action'] == 'delete_marsheet_row') {
	$marsheetrowid = $_GET['marsheetrowid'];
        $date_of_archival = date('Y-m-d');

	mysqli_query($dbc, "UPDATE `marsheet_row` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `marsheetrowid` = '$marsheetrowid'");
} else if($_GET['action'] == 'add_marsheet') {
	$medicationid = $_POST['medicationid'];
	if(!is_array($medicationid)) {
		$medicationid = [$medicationid];
	}
	foreach($medicationid as $key => $medid) {
		if ($medid == 'NEW_MED') {
			$medication_name = $_POST['new_medication'];
			mysqli_query($dbc, "INSERT INTO `medication` (`clientid`, `title`) VALUES ('$contactid', '$medication_name')");
			$medid = mysqli_insert_id($dbc);
			$medicationid[$key] = $medid;
		}
	}
	$medicationid = implode(',', $medicationid);
	$month = sprintf('%02d', $_POST['month']);
	$year = $_POST['year'];
	$contactid = $_POST['contactid'];
	mysqli_query($dbc, "INSERT INTO `marsheet` (`contactid`, `medicationid`, `month`, `year`) VALUES ('$contactid', '$medicationid', '$month', '$year')");
	$marsheetid = mysqli_insert_id($dbc);

    $row_headings = explode(',',get_config($dbc, "marsheet_row_headings"));
    foreach ($row_headings as $row_heading) {
    	mysqli_query($dbc, "INSERT INTO `marsheet_row` (`marsheetid`, `heading`) VALUES ('$marsheetid', '$row_heading')");
    }
} else if($_GET['action'] == 'delete_marsheet') {
	$marsheetid = $_GET['marsheetid'];
        $date_of_archival = date('Y-m-d');
	mysqli_query($dbc, "UPDATE `marsheet` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `marsheetid` = '$marsheetid'");
} else if($_GET['action'] == 'delete_marsheet_medication') {
	$marsheetid = $_GET['marsheetid'];
	$medicationid = $_GET['medicationid'];
	$marsheet = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `marsheet` WHERE `marsheetid` = '$marsheetid'"));
	$marsheet_meds = array_filter(explode(',',$marsheet['medicationid']));
	foreach($marsheet_meds as $key => $marsheet_med) {
		if($marsheet_med == $medicationid) {
			unset($marsheet_meds[$key]);
		}
	}
	$marsheet_meds = implode(',', $marsheet_meds);
	mysqli_query($dbc, "UPDATE `marsheet` SET `medicationid` = '$marsheet_meds' WHERE `marsheetid` = '$marsheetid'");
} else if($_GET['action'] == 'add_marsheet_medication') {
	$medicationid = $_POST['medicationid'];
	if ($medicationid == 'NEW_MED') {
		$medication_name = $_POST['new_medication'];
		mysqli_query($dbc, "INSERT INTO `medication` (`clientid`, `title`) VALUES ('$contactid', '$medication_name')");
		$medicationid = mysqli_insert_id($dbc);
	}

	$marsheetid = $_POST['marsheetid'];
	$marsheet = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `marsheet` WHERE `marsheetid` = '$marsheetid'"));
	$marsheet_meds = array_filter(explode(',',$marsheet['medicationid']));
	if(!in_array($medicationid, $marsheet_meds)) {
		$marsheet_meds[] = $medicationid;
	}
	$marsheet_meds = implode(',', $marsheet_meds);
	mysqli_query($dbc, "UPDATE `marsheet` SET `medicationid` = '$marsheet_meds' WHERE `marsheetid` = '$marsheetid'");
}

if($_GET['action'] == 'seizure_record_chart') {
	$contactid = $_POST['contactid'];
	$id = $_POST['id'];
	$date = $_POST['date'];
	$start_time = $_POST['start_time'];
	$end_time = $_POST['end_time'];
	$form = $_POST['form'];
	$note = $_POST['note'];
	$staff = $_POST['staff'];
	$history = $_POST['history'];

	if(empty($id)) {
		$sql = "INSERT INTO `seizure_record` (`client`, `date`, `start_time`, `end_time`, `form`, `note`, `staff`, `history`) VALUES ('$contactid', '$date', '$start_time', '$end_time', '$form', '$note', '$staff', '$history')";
		mysqli_query($dbc, $sql);
		$id = mysqli_insert_id($dbc);
	} else {
		$sql = "UPDATE `seizure_record` SET `client` = '$contactid', `date` = '$date', `start_time` = '$start_time', `end_time` = '$end_time', `form` = '$form', `note` = '$note', `staff` = '$staff', `history` = '$history' WHERE `seizure_record_id` = '$id'";
		mysqli_query($dbc, $sql);
	}

	echo $id;
} else if($_GET['action'] == 'seizure_record_chart_delete') {
	$id = $_POST['id'];
        $date_of_archival = date('Y-m-d');

	mysqli_query($dbc, "UPDATE `seizure_record` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `seizure_record_id` = '$id'");
}

if($_GET['action'] == 'bowel_movement_chart') {
	$contactid = $_POST['contactid'];
	$id = $_POST['id'];
	$date = $_POST['date'];
	$time = $_POST['time'];
	$bm = $_POST['bm'];
	$size = $_POST['size'];
	$form = $_POST['form'];
	$note = $_POST['note'];
	$staff = $_POST['staff'];
	$history = $_POST['history'];

	if(empty($id)) {
		$sql = "INSERT INTO `bowel_movement` (`client`, `date`, `time`, `bm`, `size`, `form`, `note`, `staff`, `history`) VALUES ('$contactid', '$date', '$time', '$bm', '$size', '$form', '$note', '$staff', '$history')";
		mysqli_query($dbc, $sql);
		$id = mysqli_insert_id($dbc);
	} else {
		$sql = "UPDATE `bowel_movement` SET `client` = '$contactid', `date` = '$date', `time` = '$time', `bm` = '$bm', `size` = '$size', `form` = '$form', `note` = '$note', `staff` = '$staff', `history` = '$history' WHERE `bowel_movement_id` = '$id'";
		mysqli_query($dbc, $sql);
	}

	echo $id;
} else if($_GET['action'] == 'bowel_movement_chart_delete') {
	$id = $_POST['id'];
        $date_of_archival = date('Y-m-d');

	mysqli_query($dbc, "UPDATE `bowel_movement` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `bowel_movement_id` = '$id'");
}

if($_GET['action'] == 'water_temp_chart') {
	$contactid = $_POST['contactid'];
	$id = $_POST['id'];
	$date = $_POST['date'];
	$time = $_POST['time'];
	$water_temp = $_POST['water_temp'];
	$note = $_POST['note'];
	$staff = $_POST['staff'];
	$history = $_POST['history'];

	if(empty($id)) {
		$sql = "INSERT INTO `daily_water_temp` (`client`, `date`, `time`, `water_temp`, `note`, `staff`, `history`) VALUES ('$contactid', '$date', '$time', '$water_temp', '$note', '$staff', '$history')";
		mysqli_query($dbc, $sql);
		$id = mysqli_insert_id($dbc);
	} else {
		$sql = "UPDATE `daily_water_temp` SET `client`='$contactid', `date`='$date', `time`='$time', `water_temp`='$water_temp', `note`='$note', `staff`='$staff', `history`='$history' WHERE `daily_water_temp_id`='$id'";
		mysqli_query($dbc, $sql);
	}

	echo $id;
} else if($_GET['action'] == 'water_temp_chart_delete') {
	$id = $_POST['id'];
    $date_of_archival = date('Y-m-d');

	mysqli_query($dbc, "UPDATE `daily_water_temp` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `daily_water_temp_id`='$id'");
}

if($_GET['action'] == 'blood_glucose_chart') {
	$contactid = $_POST['contactid'];
	$id = $_POST['id'];
	$date = $_POST['date'];
	$time = $_POST['time'];
	$bg = $_POST['bg'];
	$note = $_POST['note'];
	$staff = $_POST['staff'];
	$history = $_POST['history'];

	if(empty($id)) {
		$sql = "INSERT INTO `blood_glucose` (`client`, `date`, `time`, `bg`, `note`, `staff`, `history`) VALUES ('$contactid', '$date', '$time', '$bg', '$note', '$staff', '$history')";
		mysqli_query($dbc, $sql);
		$id = mysqli_insert_id($dbc);
	} else {
		$sql = "UPDATE `blood_glucose` SET `client`='$contactid', `date`='$date', `time`='$time', `bg`='$bg', `note`='$note', `staff`='$staff', `history`='$history' WHERE `blood_glucose_id`='$id'";
		mysqli_query($dbc, $sql);
	}

	echo $id;
} else if($_GET['action'] == 'blood_glucose_chart_delete') {
	$id = $_POST['id'];
    $date_of_archival = date('Y-m-d');

	mysqli_query($dbc, "UPDATE `blood_glucose` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `blood_glucose_id`='$id'");
}

if($_GET['action'] == 'water_temp_chart_bus') {
	$contactid = $_POST['contactid'];
	$id = $_POST['id'];
	$date = $_POST['date'];
	$time = $_POST['time'];
	$location = $_POST['location'];
	$water_temp = $_POST['water_temp'];
	$note = $_POST['note'];
	$ai_done = $_POST['ai_done'];
	$staff = $_POST['staff'];
	$history = $_POST['history'];

	if(empty($id)) {
		$sql = "INSERT INTO `daily_water_temp_bus` (`business`, `date`, `time`, `location`, `water_temp`, `note`, `ai_done`, `staff`, `history`) VALUES ('$contactid', '$date', '$time', '$location', '$water_temp', '$note', '$ai_done', '$staff', '$history')";
		mysqli_query($dbc, $sql);
		$id = mysqli_insert_id($dbc);
	} else {
		$sql = "UPDATE `daily_water_temp_bus` SET `business`='$contactid', `date`='$date', `time`='$time', `location`='$location', `water_temp`='$water_temp', `note`='$note', `ai_done`='$ai_done', `staff`='$staff', `history`='$history' WHERE `daily_water_temp_bus_id`='$id'";
		mysqli_query($dbc, $sql);
	}

	echo $id;
} else if($_GET['action'] == 'water_temp_chart_bus_delete') {
	$id = $_POST['id'];
    $date_of_archival = date('Y-m-d');

	mysqli_query($dbc, "UPDATE `daily_water_temp_bus` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `daily_water_temp_bus_id`='$id'");
}

if($_GET['action'] == 'daily_fridge_temp') {
	$contactid = $_POST['contactid'];
	$id = $_POST['id'];
	$date = $_POST['date'];
	$time = $_POST['time'];
	$fridge = $_POST['fridge'];
	$temp = $_POST['temp'];
	$note = $_POST['note'];
	$staff = $_POST['staff'];
	$history = $_POST['history'];

	if(empty($id)) {
		$sql = "INSERT INTO `daily_fridge_temp` (`business`, `date`, `time`, `fridge`, `temp`, `note`, `staff`, `history`) VALUES ('$contactid', '$date', '$time', '$fridge', '$temp', '$note', '$staff', '$history')";
		mysqli_query($dbc, $sql);
		$id = mysqli_insert_id($dbc);
	} else {
		$sql = "UPDATE `daily_fridge_temp` SET `business`='$contactid', `date`='$date', `time`='$time', `fridge`='$fridge', `temp`='$temp', `note`='$note', `staff`='$staff', `history`='$history' WHERE `daily_fridge_temp_id`='$id'";
		mysqli_query($dbc, $sql);
	}

	echo $id;
} else if($_GET['action'] == 'daily_fridge_temp_delete') {
	$id = $_POST['id'];
    $date_of_archival = date('Y-m-d');

	mysqli_query($dbc, "UPDATE `daily_fridge_temp` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `daily_fridge_temp_id`='$id'");
}

if($_GET['action'] == 'daily_freezer_temp') {
	$contactid = $_POST['contactid'];
	$id = $_POST['id'];
	$date = $_POST['date'];
	$time = $_POST['time'];
	$freezer = $_POST['freezer'];
	$temp = $_POST['temp'];
	$note = $_POST['note'];
	$staff = $_POST['staff'];
	$history = $_POST['history'];

	if(empty($id)) {
		$sql = "INSERT INTO `daily_freezer_temp` (`business`, `date`, `time`, `freezer`, `temp`, `note`, `staff`, `history`) VALUES ('$contactid', '$date', '$time', '$freezer', '$temp', '$note', '$staff', '$history')";
		mysqli_query($dbc, $sql);
		$id = mysqli_insert_id($dbc);
	} else {
		$sql = "UPDATE `daily_freezer_temp` SET `business`='$contactid', `date`='$date', `time`='$time', `freezer`='$freezer', `temp`='$temp', `note`='$note', `staff`='$staff', `history`='$history' WHERE `daily_freezer_temp_id`='$id'";
		mysqli_query($dbc, $sql);
	}

	echo $id;
} else if($_GET['action'] == 'daily_freezer_temp_delete') {
	$id = $_POST['id'];
    $date_of_archival = date('Y-m-d');

	mysqli_query($dbc, "UPDATE `daily_freezer_temp` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `daily_freezer_temp_id`='$id'");
}

if($_GET['action'] == 'daily_dishwasher_temp') {
	$contactid = $_POST['contactid'];
	$id = $_POST['id'];
	$date = $_POST['date'];
	$time = $_POST['time'];
	$water_temp = $_POST['water_temp'];
	$note = $_POST['note'];
	$staff = $_POST['staff'];
	$history = $_POST['history'];

	if(empty($id)) {
		$sql = "INSERT INTO `daily_dishwasher_temp` (`business`, `date`, `time`, `water_temp`, `note`, `staff`, `history`) VALUES ('$contactid', '$date', '$time', '$water_temp', '$note', '$staff', '$history')";
		mysqli_query($dbc, $sql);
		$id = mysqli_insert_id($dbc);
	} else {
		$sql = "UPDATE `daily_dishwasher_temp` SET `business`='$contactid', `date`='$date', `time`='$time', `water_temp`='$water_temp', `note`='$note', `staff`='$staff', `history`='$history' WHERE `daily_dishwasher_temp_id`='$id'";
		mysqli_query($dbc, $sql);
	}

	echo $id;
} else if($_GET['action'] == 'daily_dishwasher_temp_delete') {
	$id = $_POST['id'];
    $date_of_archival = date('Y-m-d');

	mysqli_query($dbc, "UPDATE `daily_dishwasher_temp` SET `deleted`=1, `date_of_archival` = '$date_of_archival', `date_of_archival` = '$date_of_archival' WHERE `daily_dishwasher_temp_id`='$id'");
}

if($_GET['action'] == 'contacts_security_settings') {
	$tile_name = $_POST['tile_name'];
	$category = $_POST['category'];
	$security_level = $_POST['security_level'];
	$subtabs_hidden = implode(',', $_POST['subtabs_hidden']);
	$subtabs_viewonly = implode(',', $_POST['subtabs_viewonly']);
	$fields_hidden = implode(',', $_POST['fields_hidden']);
	$fields_viewonly = implode(',', $_POST['fields_viewonly']);
	$profile_access = $_POST['profile_access'];

	$num_rows = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `field_config_contacts_security` WHERE `tile_name` = '$tile_name' AND `category` = '$category' AND `security_level` = '$security_level'"))['num_rows'];
	if($num_rows > 0) {
		mysqli_query($dbc, "UPDATE `field_config_contacts_security` SET `subtabs_hidden` = '$subtabs_hidden', `subtabs_viewonly` = '$subtabs_viewonly', `fields_hidden` = '$fields_hidden', `fields_viewonly` = '$fields_viewonly', `profile_access` = '$profile_access' WHERE `tile_name` = '$tile_name' AND `category` = '$category' AND `security_level` = '$security_level'");
	} else {
		mysqli_query($dbc, "INSERT INTO `field_config_contacts_security` (`tile_name`, `category`, `security_level`, `subtabs_hidden`, `subtabs_viewonly`, `fields_hidden`, `fields_viewonly`, `profile_access`) VALUES ('$tile_name', '$category', '$security_level', '$subtabs_hidden', '$subtabs_viewonly', '$fields_hidden', '$fields_viewonly', '$profile_access')");
	}
}

if($_GET['action'] == 'set_initials') {
	$contactid = $_POST['contactid'];
	$contact = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '$contactid'"));
	if($contact['category'] == 'Business' || $contact['category'] == 'Corporation') {
		$name_arr = explode(' ',decryptIt($contact['name']));
		$initials = '';
		foreach ($name_arr as $name_string) {
			$initials .= substr($name_string,0,1);
		}
		$sql = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT `initials` FROM `contacts` WHERE `deleted` = 0 AND `category` = '".$contact['category']."' AND `initials` LIKE '$initials%'"),MYSQLI_ASSOC);
		$initials_list = [];
		foreach ($sql as $contact_initials) {
			$initials_list[] = $contact_initials['initials'];
		}
		$counter = 0;
		while(in_array($initials.$counter, $initials_list)) {
			$counter++;
		}
		$initials = $initials.$counter;
	} else {
		$initials = substr(decryptIt($contact['first_name']),0,1).substr(decryptIt($contact['last_name']),0,1);
	}
	mysqli_query($dbc, "UPDATE `contacts` SET `initials` = '$initials' WHERE `contactid` = '$contactid'");
	echo $initials;
}

if($_GET['action'] == 'isp_submit_signature') {
	$individualsupportplanid = $_POST['ispid'];
	$field = $_POST['field'];
	$sig = $_POST['sig'];
	$sig_name = $_POST['sig_name'];
	$sig_date = $_POST['sig_date'];

    if (!file_exists('../Individual Support Plan/download')) {
        mkdir('../Individual Support Plan/download', 0777, true);
    }

    $get_isp = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `individual_support_plan` WHERE `individualsupportplanid` = '$individualsupportplanid'"));

    $signatures = explode('*#*', $get_isp[$field]);
    $signatures_name = explode('*#*', $get_isp[$field.'_name']);
    $signatures_date = explode('*#*', $get_isp[$field.'_date']);
    $sig_count = count($signatures);
    if(empty($get_isp[$field])) {
    	$sig_count = 0;
    	$signatures = '';
    	$signatures_name = '';
    	$signatures_date = '';
    }
    $img = sigJsonToImage($sig);
    $file_name = $field.'_'.$individualsupportplanid.'_'.$sig_count.'.png';
    imagepng($img, '../Individual Support Plan/download/'.$file_name);
	$signatures[] = $file_name;
	$signatures_name[] = $sig_name;
	$signatures_date[] = $sig_date;

	$signatures = implode('*#*', $signatures);
	$signatures_name = implode('*#*', $signatures_name);
	$signatures_date = implode('*#*', $signatures_date);
	mysqli_query($dbc, "UPDATE `individual_support_plan` SET `".$field."` = '$signatures', `".$field."_name` = '$signatures_name', `".$field."_date` = '$signatures_date' WHERE `individualsupportplanid` = '$individualsupportplanid'");

	$img_html = '<img src="../Individual Support Plan/download/'.$file_name.'"><br>';
	$img_html .= 'Name: '.$sig_name.'<br>';
	$img_html .= 'Date: '.$sig_date.'<br><br>';

	echo $img_html;
}

if($_GET['action'] == 'email_login_credentials') {
	$contactid = $_POST['contactid'];
	$sender_name = $_POST['sender_name'];
	$sender = $_POST['sender'];
	$recipient = $_POST['recipient'];
	$subject = $_POST['subject'];
	$body = $_POST['body'];

	$body = str_replace(['[USERNAME]','[PASSWORD]'], [get_contact($dbc, $contactid, 'user_name'),get_contact($dbc, $contactid, 'password')],$body);

	try {
		send_email([$sender=>$sender_name], $recipient, '', '', $subject, $body, '');
		$alert_msg = 'Successfully sent.';
	} catch(Exception $e) {
		$alert_msg = "Unable to send e-mail: ".$e->getMessage();
	}
	echo $alert_msg;
}

if($_GET['action'] == 'add_contact_reminder') {
    $staffid = filter_var($_POST['staffid'], FILTER_SANITIZE_NUMBER_INT);
	$contactid = filter_var($_POST['contactid'], FILTER_SANITIZE_NUMBER_INT);
	$reminder_subject = filter_var($_POST['reminder_subject'], FILTER_SANITIZE_STRING);
	$reminder_date = $_POST['reminder_date'];
    $reminder_folder = $_POST['reminder_folder'];

    mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_type`, `subject`, `src_table`, `src_tableid`) VALUES ('$staffid', '$reminder_date', '$reminder_folder', '$reminder_subject', 'contacts', '$contactid')");
} else if($_GET['action'] == 'loadServices') {
	$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
	$type = filter_var($_POST['type'],FILTER_SANITIZE_STRING);
	$target = filter_var($_POST['target'],FILTER_SANITIZE_STRING);
	if($category != '' && $target == 'type') {
		$rows = $dbc->query("SELECT `service_type` FROM `services` WHERE `deleted`=0 AND `category`='$category' GROUP BY `service_type`");
		echo "<option />\n";
		while($row = $rows->fetch_assoc()) {
			echo "<option value='{$row['service_type']}'>{$row['service_type']}</option>\n";
		}
	} else if($category != '' && $target == 'heading') {
		$rows = $dbc->query("SELECT `serviceid`, `heading`, `category`, `service_type` FROM `services` WHERE `deleted`=0 AND `category`='$category'");
		echo "<option />\n";
		while($row = $rows->fetch_assoc()) {
			echo "<option data-category='{$row['category']}' data-service-type='{$row['service_type']}' value='{$row['serviceid']}'>{$row['heading']}</option>\n";
		}
	} else if($type != '' && $target == 'heading') {
		$rows = $dbc->query("SELECT `serviceid`, `heading`, `category`, `service_type` FROM `services` WHERE `deleted`=0 AND `service_type`='$type'");
		echo "<option />\n";
		while($row = $rows->fetch_assoc()) {
			echo "<option data-category='{$row['category']}' data-service-type='{$row['service_type']}' value='{$row['serviceid']}'>{$row['heading']}</option>\n";
		}
	}
} else if($_GET['action'] == 'getServiceTimeEstimate') {
	$serviceid = $_GET['serviceid'];
	$service = $dbc->query("SELECT * FROM `services` WHERE `serviceid`='$serviceid'")->fetch_assoc();
	$service_time_estimate = !empty($service['estimated_hours']) ? $service['estimated_hours'] : '00:00';
	echo $service_time_estimate;
} else if($_GET['action'] == 'update_contacts_services') {
	$contactid = $_POST['contactid'];
	$serviceid = $_POST['serviceid'];
	if($contactid > 0 && $serviceid > 0) {
		$num_rooms = $_POST['num_rooms'] > 0 ? $_POST['num_rooms'] : 1;
		mysqli_query($dbc, "INSERT INTO `contacts_services` (`contactid`, `serviceid`) SELECT '$contactid', '$serviceid' FROM (SELECT COUNT(`contactserviceid`) rows FROM `contacts_services` WHERE `contactid` = '$contactid' AND `serviceid` = '$serviceid') as num WHERE num.rows = 0");
		mysqli_query($dbc, "UPDATE `contacts_services` SET `num_rooms` = '$num_rooms' WHERE `contactid` = '$contactid' AND `serviceid` = '$serviceid'");
		echo $num_rooms;
	}
} else if($_GET['action'] == 'update_contacts_services_group') {
	$contactid = $_POST['contactid'];
	$services = $_POST['services'];
	$num_rooms = $_POST['num_rooms'] > 0 ? $_POST['num_rooms'] : 1;
	if(!is_array($services)) {
		$services = [$services];
	}
	if($contactid > 0) {
		foreach($services as $serviceid) {
			if($serviceid > 0) {
				mysqli_query($dbc, "INSERT INTO `contacts_services` (`contactid`, `serviceid`) SELECT '$contactid', '$serviceid' FROM (SELECT COUNT(`contactserviceid`) rows FROM `contacts_services` WHERE `contactid` = '$contactid' AND `serviceid` = '$serviceid') as num WHERE num.rows = 0");
				mysqli_query($dbc, "UPDATE `contacts_services` SET `num_rooms` = '$num_rooms' WHERE `contactid` = '$contactid' AND `serviceid` = '$serviceid'");
			}
		}
	}
	echo $num_rooms;
}

if($_GET['action'] == 'multiple_categories') {
	$contactid = $_POST['contactid'];
	$other_contactid = $_POST['other_contactid'];
	$category = $_POST['category'];

	if(!empty($category)) {
		if($other_contactid > 0) {
			mysqli_query($dbc, "UPDATE `contacts` SET `category` = '$category' WHERE `contactid` = '$other_contactid'");
		} else {
			mysqli_query($dbc, "INSERT INTO `contacts` (`category`) VALUES ('$category')");
			$other_contactid = mysqli_insert_id($dbc);
			$initial_add = true;
			echo $other_contactid;
		}

		copy_data($dbc, $contactid, $other_contactid);

		$contact = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '$contactid'"));
		$contacts_sync = explode(',', $contact['contacts_sync']);
		if(!in_array($contactid, $contacts_sync)) {
			$contacts_sync[] = $contactid;
		}
		if(!in_array($other_contactid, $contacts_sync)) {
			$contacts_sync[] = $other_contactid;
		}
		$contacts_sync = implode(',', array_filter($contacts_sync));

		foreach(explode(',',$contacts_sync) as $contact_id) {
			mysqli_query($dbc, "UPDATE `contacts` SET `contacts_sync` = '$contacts_sync' WHERE `contactid` = '$contact_id'");
		}
	}
}

if($_GET['action'] == 'remove_multiple_categories') {
	$contactid = $_POST['contactid'];
	$other_contactid = $_POST['other_contactid'];

	$contact = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '$contactid'"));
	$contacts_sync = explode(',', $contact['contacts_sync']);
	foreach($contacts_sync as $i => $contact_sync) {
		if($contact_sync == $other_contactid) {
			unset($contacts_sync[$i]);
		}
	}
	$contacts_sync = implode(',', array_filter($contacts_sync));

	foreach(explode(',',$contacts_sync) as $contact_id) {
		mysqli_query($dbc, "UPDATE `contacts` SET `contacts_sync` = '$contacts_sync' WHERE `contactid` = '$contact_id'");
	}
	mysqli_query($dbc, "UPDATE `contacts` SET `contacts_sync` = '' WHERE `contactid` = '$other_contactid'");

	$delete_contact = $_POST['delete_contact'];
	if($delete_contact == 1) {
        $date_of_archival = date('Y-m-d');
		mysqli_query($dbc, "UPDATE `contacts` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `contactid` = '$other_contactid'");
	}
}

if($_GET['action'] == 'copy_contact') {
	$contactid = $_POST['contactid'];
	$category = $_POST['category'];

	mysqli_query($dbc, "INSERT INTO `contacts` (`category`) VALUES ('$category')");
	$other_contactid = mysqli_insert_id($dbc);
	copy_data($dbc, $contactid, $other_contactid);

	echo $other_contactid;
}

if($_GET['action'] == 'add_another_site') {
	$contactid = $_POST['contactid'];
	$another_site_id = $_POST['another_site_id'];

	mysqli_query($dbc, "UPDATE `contacts` SET `businessid` = '$contactid' WHERE `contactid` = '$another_site_id'");
}

if($_GET['action'] == 'set_main_site') {
	$contactid = $_POST['contactid'];
	$site_id = $_POST['site_id'];

	mysqli_query($dbc, "UPDATE `contacts` SET `main_siteid` = '$site_id' WHERE `contactid` = '$contactid'");
}

if($_GET['action'] == 'update_total_estimated_hours') {
	$ratecardid = $_GET['ratecardid'];
	$hours = $_GET['hours'];

	$hours = time_time2decimal($hours);
	mysqli_query($dbc, "UPDATE `rate_card` SET `total_estimated_hours` = '$hours' WHERE `ratecardid` = '$ratecardid'");
	echo "UPDATE `rate_card` SET `total_estimated_hours` = '$hours' WHERE `ratecardid` = '$ratecardid'";
}

if($_GET['action'] == 'archive_contact_form') {
	$pdf_id = $_GET['pdf_id'];
	mysqli_query($dbc, "UPDATE `user_form_pdf` SET `deleted` = 1 WHERE `pdf_id` = '$pdf_id'");
}

if($_GET['action'] == 'update_url_get_preview') {
	$body = $_POST['body'];
	$expiry_date = $_POST['expiry_date'];

	$body = str_replace(['[FULL_NAME]','[EXPIRY_DATE]'],[get_contact($dbc, $_SESSION['contactid']),$expiry_date],$body).'<br /><br />Click <a href="?">here</a> to access your profile.';
	echo $body;
}

if($_GET['action'] == 'update_url_send_email') {
	$folder_name = $_POST['folder_name'];
	$categories = $_POST['categories'];
	$contacts = $_POST['contacts'];
	$security_level = $_POST['security_level'];
	$expiry_date = $_POST['expiry_date'];
	$subject = $_POST['subject'];
	$body = $_POST['body'];

	if(!empty($categories) || !empty($contacts)) {
		$tabs = explode(',',get_config($dbc, $folder_name.'_tabs'));
		$staff = array_search('Staff',$tabs);
        if($staff !== FALSE) {
            unset($tabs[$staff]);
        }
		$query = "SELECT * FROM `contacts` WHERE IFNULL(`email_address`,'') != '' AND `deleted` = 0 AND `status` > 0 AND `show_hide_user` = 1 AND `category` IN ('".implode("','", $tabs)."')";
		if(!empty($categories)) {
			$query .= " AND `category` IN ('".implode("','", $categories)."')";
		}
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

			$new_body = str_replace(['[FULL_NAME]','[EXPIRY_DATE]'],[$row['full_name'],$expiry_date],$body).'<br /><br />Click <a href="'.WEBSITE_URL.'/'.ucfirst($folder_name).'/contacts_inbox.php?edit='.$row['contactid'].'&update_url=1&url_key='.$url_key.'">here</a> to access your profile.';

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

if($_GET['action'] == 'get_contact_name') {
	echo $contact_name = array_shift(sort_contacts_query(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '".$_GET['contactid']."'")))['full_name'];
}

function copy_data($dbc, $contactid, $other_contactid) {
	$contacts_tables = ['contacts','contacts_cost','contacts_dates','contacts_description','contacts_medical','contacts_upload'];

	foreach($contacts_tables as $contacts_table) {
		mysqli_query($dbc, "INSERT INTO `$contacts_table` (`contactid`) SELECT '$other_contactid' FROM (SELECT COUNT(*) num FROM `$contacts_table` WHERE `contactid` = '$other_contactid') rows WHERE rows.num=0");
		$columns = mysqli_fetch_all(mysqli_query($dbc, "SHOW COLUMNS from `$contacts_table` where `Field` NOT IN('contactid','category','contactcostid','contactdateid','contactdescid','contactmedicalid','contactuploadid')"),MYSQLI_ASSOC);

		$contact = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `$contacts_table` WHERE `contactid` = '$contactid'"));
		$query_update = [];
		foreach($columns as $column) {
			$query_update[] = "`".$column['Field']."` = '".$contact[$column['Field']]."'";
		}
		$query_update = implode(',', $query_update);

		mysqli_query($dbc, "UPDATE `$contacts_table` SET $query_update WHERE `contactid` = '$other_contactid'");
	}
}
