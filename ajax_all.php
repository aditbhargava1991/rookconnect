<?php
//Ajax//
include ('include.php');
ob_clean();
error_reporting(0);

$history = '';
$contactid = (isset($_GET['contactid']) ? $_GET['contactid'] : 0);
$name = (isset($_GET['name']) ? $_GET['name'] : 0);

if($_GET['fill'] == 'daysheet_report') {
    $contactid = $_SESSION['contactid'];
    $today_date = date('Y-m-d');
    $admin_SQL = "INSERT INTO `daysheet_report` (`contactid`, `today_date`) VALUES ('$contactid', '$today_date')";
	$admin_result = mysqli_query($dbc,$admin_SQL);
}

if($_GET['fill'] == 'tile_config' || $_GET['fill'] == 'admin_tile_config')
{
	$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid'");
	$namex = 'Admin';
	$level = '';
    while($row = mysqli_fetch_assoc($result)) {
		$namex = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' (ID: '.$row['contactid'].')';
		$level = ','.$row['role'].',';
    }
	date_default_timezone_set('America/Denver');
	$date = date('m/d/Y', time());
	$time = date("g:i a", time());
	$history = (!empty($_GET['turnOn']) ? 'Turned on' : 'Turned off')." $name by $namex on $date at $time.";

	// Hide newly turned on tile for all users except current security level by default
	if(!empty($_GET['turnOn'])) {
		$name = $_GET['name'];
		$value = '*hide*';
		foreach(get_security_levels($dbc) as $level_name) {
			$sql = "INSERT INTO `security_privileges` (`tile`, `level`, `privileges`) SELECT '$name', '$level_name', '$value' FROM (SELECT COUNT(*) num FROM `security_privileges` WHERE `tile`='$name' AND `level`='$level_name') rows WHERE rows.num=0";
			$result = mysqli_query($dbc, $sql);
		}

    $before_change = '';
    $history = "Security Privileges have been added. <br />";
    add_update_history($dbc, 'security_history', $history, '', $before_change);
	}
}
if($_GET['fill'] == 'admin_tile_config') {
    $name = $_GET['name'];
    $value = $_GET['value'];
    $turn_off_value = 'turn_off';
    $turn_on_value = 'turn_on';
    $turnOn = $_GET['turnOn'];
    $turnoff = $_GET['turnoff'];

	// Remove security privileges that are no longer needed
    if(!empty($turnoff)) {
        $query = mysqli_query($dbc,"DELETE FROM security_privileges WHERE tile='$name'");
    }

	// Prepare the SQL statements
    mysqli_query($dbc,"INSERT INTO `tile_security` (`tile_name`) SELECT '$name' FROM (SELECT COUNT(*) rows FROM `tile_security` WHERE `tile_name`='$name') num WHERE num.rows=0");
	if(!empty($turnOn) || $value == 'turn_on') {
		$tile_SQL = "UPDATE `tile_security` SET `admin_enabled`='1', `admin_history`=CONCAT(IFNULL(CONCAT(`admin_history`,'<br />'),''),'$history'), `user_enabled`='1', `tile_history`=CONCAT(IFNULL(CONCAT(`tile_history`,'<br />'),''),'$history') WHERE `tile_name`='$name'";
	} else {
		$tile_SQL = "UPDATE `tile_security` SET `admin_enabled`='0', `admin_history`=CONCAT(IFNULL(CONCAT(`admin_history`,'<br />'),''),'$history'), `user_enabled`='0', `tile_history`=CONCAT(IFNULL(CONCAT(`tile_history`,'<br />'),''),'$history') WHERE `tile_name`='$name'";
	}

	// Run the SQL statements
	$result = mysqli_query($dbc,$tile_SQL);
	$new_status = mysqli_fetch_array(mysqli_query($dbc, "SELECT `admin_enabled`, `user_enabled` FROM `tile_security` WHERE `tile_name`='$name'"));
	echo $tile_SQL.'#*#';
	echo ($new_status['user_enabled'] == 1 ? 'Active - In Use' : ($new_status['admin_enabled'] == 1 ? 'Active' : 'Inactive - Contact Support')).'#*#';
	echo $new_status['admin_enabled'];
}
if($_GET['fill'] == 'tile_config') {
    $name = $_GET['name'];
    $value = $_GET['value'];
    $turn_off_value = 'turn_off';
    $turn_on_value = 'turn_on';
    $turnOn = $_GET['turnOn'];
    $turnoff = $_GET['turnoff'];

	// Remove security privileges that are no longer needed
    if(!empty($turnoff)) {
        $query = mysqli_query($dbc,"DELETE FROM security_privileges WHERE tile='$name'");
    }

	// Prepare the SQL statements
    mysqli_query($dbc,"INSERT INTO `tile_security` (`tile_name`) SELECT '$name' FROM (SELECT COUNT(*) rows FROM `tile_security` WHERE `tile_name`='$name') num WHERE num.rows=0");
	if(!empty($turnOn) || $value == 'turn_on') {
		$tile_SQL = "UPDATE `tile_security` SET `user_enabled`='1', `tile_history`=CONCAT(IFNULL(CONCAT(`tile_history`,'<br />'),''),'$history') WHERE `tile_name`='$name'";
    $before_change = '';
    $history = "User has been enable for tile $name. <br />";
    add_update_history($dbc, 'security_history', $history, '', $before_change);
	} else {
		$tile_SQL = "UPDATE `tile_security` SET `user_enabled`='0', `tile_history`=CONCAT(IFNULL(CONCAT(`tile_history`,'<br />'),''),'$history') WHERE `tile_name`='$name'";
    $before_change = '';
    $history = "User has been disabled for tile $name. <br />";
    add_update_history($dbc, 'security_history', $history, '', $before_change);
	}

	// Run the SQL statements
	$result = mysqli_query($dbc, $tile_SQL);

	// Hide the tile for all enabled security levels if it was just turned on except for the level of the user
	if(!empty($_GET['turnOn'])) {
		foreach(get_security_levels($dbc) as $field => $value) {
			if(strpos($level,','.$field.',') === false) {
				$sql = "INSERT INTO `security_privileges` (`tile`, `level`, `privileges`) SELECT '$name', '$field', '*hide*' FROM (SELECT COUNT(*) rows FROM `security_privileges` WHERE `tile`='$name' AND `level`='$field') num WHERE num.rows=0";
				$result = mysqli_query($dbc, $sql);
			}
		}

    $before_change = '';
    $history = "Security Privileges have been added. <br />";
    add_update_history($dbc, 'security_history', $history, '', $before_change);
	}
	$new_status = mysqli_fetch_array(mysqli_query($dbc, "SELECT `admin_enabled`, `user_enabled` FROM `tile_security` WHERE `tile_name`='$name'"));
	echo $tile_SQL.'#*#';
	echo ($new_status['user_enabled'] == 1 ? 'Active - In Use' : ($new_status['admin_enabled'] == 1 ? 'Active' : 'Inactive - Contact Support')).'#*#';
	echo $new_status['admin_enabled'];
}
if($_GET['fill'] == 'tile_enable_section') {
	$sql = "INSERT INTO `general_configuration` (`name`) SELECT 'tile_enable_section' FROM (SELECT COUNT(*) numrows FROM `general_configuration` WHERE `name`='tile_enable_section') rows WHERE numrows=0";
	mysqli_query($dbc, $sql);

	if(!mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$_POST['value']."' WHERE `name`='tile_enable_section'")) {
		echo mysqli_error($dbc);
	}
	if(mysqli_affected_rows($dbc) == 0) {
		echo "No changes made.";
	} else {
		echo "Configuration Saved.";
	}
}

/*
 * Title:       Subtab Configuration
 * File:        software_config_subtabs.php
 * Function:    Save the selected subtab statuses to the database
 */
if($_GET['fill'] == 'subtab_config') {
    $tile           = $_GET[ 'tile' ];
    $level          = $_GET[ 'level' ];
    $subtab         = $_GET[ 'subtab' ];
    $value          = $_GET[ 'value' ] . '*#*' . date('Y-m-d');
    $turnOn         = $_GET[ 'turnOn' ];
    $turnOff        = $_GET[ 'turnOff' ];
    $turn_off_value = 'turn_off*#*' . date('Y-m-d');
    $turn_on_value  = 'turn_on*#*'  . date('Y-m-d');

    if ( !empty ( $turnOff ) ) {
        $query = mysqli_query ( $dbc, "DELETE FROM subtab_config WHERE subtab='$subtab'" );
    }

    $get_config = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `id`, COUNT(`id`) AS `total_ids` FROM `subtab_config` WHERE `tile`='$tile' AND `security_level`='$level' AND `subtab`='$subtab'" ) );

    if ( $get_config[ 'total_ids' ] == 0 ) {
        //No entries found so we insert a record
        $query      = "INSERT INTO `subtab_config` (`tile`, `security_level`, `subtab`, `status`) VALUES ('$tile', '$level', '$subtab', '$value')";
        $results    = mysqli_query ( $dbc, $query );

    } else {
        if ( !empty ( $turnOff ) ) {
            $results = mysqli_query ( $dbc, "UPDATE `subtab_config` SET `status`='$turn_off_value'  WHERE `tile`='$tile' AND `security_level`='$level' AND `subtab`='$subtab'" );
        } elseif ( !empty ( $turnOn ) ) {
            $results = mysqli_query ( $dbc, "UPDATE `subtab_config` SET `status`='$turn_on_value'   WHERE `tile`='$tile' AND `security_level`='$level' AND `subtab`='$subtab'" );
        } else {
            $results = mysqli_query ( $dbc, "UPDATE `subtab_config` SET `status`='$value'           WHERE `tile`='$tile' AND `security_level`='$level' AND `subtab`='$subtab'" );
        }
    }
}

if($_GET['fill'] == 'subtab_staff_config') {
    $tile           = $_GET[ 'tile' ];
    $level          = $_GET[ 'level' ];
    $subtab         = $_GET[ 'subtab' ];
    $value          = $_GET[ 'value' ] . '*#*' . date('Y-m-d');
    $turnOn         = $_GET[ 'turnOn' ];
    $turnOff        = $_GET[ 'turnOff' ];
    $turn_off_value = 'turn_off*#*' . date('Y-m-d');
    $turn_on_value  = 'turn_on*#*'  . date('Y-m-d');

    if ( !empty ( $turnOff ) ) {
        $query = mysqli_query ( $dbc, "DELETE FROM subtab_staff_config WHERE subtab='$subtab'" );
    }

    $get_config = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `id`, COUNT(`id`) AS `total_ids` FROM `subtab_staff_config` WHERE `tile`='$tile' AND `security_level`='$level' AND `subtab`='$subtab'" ) );

    if ( $get_config[ 'total_ids' ] == 0 ) {
        //No entries found so we insert a record
        $query      = "INSERT INTO `subtab_staff_config` (`tile`, `security_level`, `subtab`, `status`) VALUES ('$tile', '$level', '$subtab', '$value')";
        $results    = mysqli_query ( $dbc, $query );

    } else {
        if ( !empty ( $turnOff ) ) {
            $results = mysqli_query ( $dbc, "UPDATE `subtab_config` SET `status`='$turn_off_value'  WHERE `tile`='$tile' AND `security_level`='$level' AND `subtab`='$subtab'" );
        } elseif ( !empty ( $turnOn ) ) {
            $results = mysqli_query ( $dbc, "UPDATE `subtab_config` SET `status`='$turn_on_value'   WHERE `tile`='$tile' AND `security_level`='$level' AND `subtab`='$subtab'" );
        } else {
            $results = mysqli_query ( $dbc, "UPDATE `subtab_config` SET `status`='$value'           WHERE `tile`='$tile' AND `security_level`='$level' AND `subtab`='$subtab'" );
        }
    }
}

/*
 * Title:       Dashboard Permission Configuration
 * File:        software_config_dashboard.php
 * Function:    Save the selected dashboard statuses to the database
 */
if($_GET['fill'] == 'dashboard_permission_config') {
    $tile           = $_GET[ 'tile' ];
    $level          = $_GET[ 'level' ];
    $field         = $_GET[ 'field' ];
    $value          = $_GET[ 'value' ] . '*#*' . date('Y-m-d');
    $turnOn         = $_GET[ 'turnOn' ];
    $turnOff        = $_GET[ 'turnOff' ];
    $turn_off_value = 'turn_off*#*' . date('Y-m-d');
    $turn_on_value  = 'turn_on*#*'  . date('Y-m-d');

    if ( !empty ( $turnOff ) ) {
        $query = mysqli_query ( $dbc, "DELETE FROM dashboard_permission_config WHERE field='$field'" );
    }

    $get_config = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `id`, COUNT(`id`) AS `total_ids` FROM `dashboard_permission_config` WHERE `tile`='$tile' AND `security_level`='$level' AND `field`='$field'" ) );

    if ( $get_config[ 'total_ids' ] == 0 ) {
        //No entries found so we insert a record
        $query      = "INSERT INTO `dashboard_permission_config` (`tile`, `security_level`, `field`, `status`) VALUES ('$tile', '$level', '$field', '$value')";
        $results    = mysqli_query ( $dbc, $query );

    } else {
        if ( !empty ( $turnOff ) ) {
            $results = mysqli_query ( $dbc, "UPDATE `dashboard_permission_config` SET `status`='$turn_off_value'  WHERE `tile`='$tile' AND `security_level`='$level' AND `field`='$field'" );
        } elseif ( !empty ( $turnOn ) ) {
            $results = mysqli_query ( $dbc, "UPDATE `dashboard_permission_config` SET `status`='$turn_on_value'   WHERE `tile`='$tile' AND `security_level`='$level' AND `field`='$field'" );
        } else {
            $results = mysqli_query ( $dbc, "UPDATE `dashboard_permission_config` SET `status`='$value'           WHERE `tile`='$tile' AND `security_level`='$level' AND `field`='$field'" );
        }
    }
}

if($_GET['fill'] == 'security_level') {
	date_default_timezone_set('America/Denver');
    $name = $_GET['name'];
	$label = $_GET['label'];
	if(empty($_GET['label'])) {
		$label = get_securitylevel($dbc, $name);
	}
	if($name == '') {
		$name = 'FFMCUST_'.config_safe_str($label);
	}
    $value = $_GET['value'];
	$history = (strpos($value,'turn_on') !== false ? "Turned on" : "Turned off")." by {$_SESSION['first_name']} {$_SESSION['last_name']} at ";
	$history .= date('Y-m-d H:i:s').".<br />\n";

	// Suspend all users from logging in if their security level has been deactivated
	if(strpos($value,'turn_off') !== false) {
		$sql = "UPDATE `contacts` SET `status`=0 WHERE `role`='$name' and user_name !=''";
		mysqli_query($dbc, $sql);
	}

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(*) AS total_id FROM security_level_names WHERE `identifier`='$name'"));

    if($get_config['total_id'] == 0) {
		    $query_insert_customer = "INSERT INTO `security_level_names` (`label`, `identifier`, `active`, `history`) VALUES ('$label', '$name', '".(strpos($value,'turn_on') !== FALSE ? 1 : 0)."', '$history')";
        $result_insert_customer = mysqli_query($dbc, $query_insert_customer);
        $before_change = '';
        $history = "Security Leven names have been added. <br />";
        add_update_history($dbc, 'security_history', $history, '', $before_change);
    } else {
        $before_change = capture_before_change($dbc, 'security_level_names', 'active', 'identifier', $name);
        $before_change .= capture_before_change($dbc, 'security_level_names', 'label', 'identifier', $name);
        $query_rate_card = "UPDATE `security_level_names` SET `active` = '".(strpos($value,'turn_on') !== FALSE ? 1 : 0)."', `label`='$label', history = concat(IFNULL(`history`,''),'$history') WHERE `identifier` = '$name'";
        $result_rate_card	= mysqli_query($dbc, $query_rate_card);
        $active = strpos($value,'turn_on') !== FALSE ? 1 : 0;
        $history = capture_after_change('active', $active);
        $history .= capture_after_change('label', $label);
        add_update_history($dbc, 'security_history', $history, '', $before_change);
    }
}
if($_GET['fill'] == 'security_level_order') {
    $current = $_GET['current'];
    $previous = $_GET['next'];
    $next = $_GET['previous'];

    if($current_order < $previous_order) {
      for($i = $current_order+1; $i <= $previous_order; $i++) {
        $custom_count = $i - 1;
        mysqli_query($dbc, "update security_level_names set custom_order = $custom_count where custom_order = $i");
      }

      mysqli_query($dbc, "update security_level_names set custom_order = $previous_order where identifier = '$current'");
    }
    elseif($current_order > $previous_order) {
      for($i = $next_order; $i <= $current_order; $i++) {
        $custom_count = $i + 1;
        mysqli_query($dbc, "update security_level_names set custom_order = $custom_count where custom_order = $i");
      }

      mysqli_query($dbc, "update security_level_names set custom_order = $next_order where identifier = '$current'");
    }
}
if($_GET['fill'] == 'privileges_config') {
    $tile = $_GET['name'];
    $value = $_GET['value'];
    $level = $_GET['level'];

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(privilegesid) AS total_id FROM security_privileges WHERE tile='$tile' AND level='$level'"));

    if($get_config['total_id'] == 0) {
        $query_insert_customer = "INSERT INTO `security_privileges` (`tile`, `level`, `privileges`) VALUES ('$tile', '$level', '$value')";
        $result_insert_customer = mysqli_query($dbc, $query_insert_customer);
        $before_change = '';
        $history = "Security Privileges have been added. <br />";
        add_update_history($dbc, 'security_history', $history, '', $before_change);
    } else {
        $before_change = capture_before_change($dbc, 'security_privileges', 'privileges', 'tile', $tile, 'level', $level);
        $query_rate_card = "UPDATE `security_privileges` SET `privileges` = '$value' WHERE tile='$tile' AND level='$level'";
        $result_rate_card	= mysqli_query($dbc, $query_rate_card);
        $history = capture_after_change('privileges', $value);
        add_update_history($dbc, 'security_history', $history, '', $before_change);
    }

    /*
    if($value != '*view_use*view_use_add_edit_delete*') {
        $uncheck_staff = $_GET['uncheck_staff'];
        if(!empty($uncheck_staff)) {
            $query = mysqli_query($dbc,"UPDATE `security_privileges` SET `privileges` = '*view_use*' WHERE tile='$tile' AND level='$level'");
        }

        $check_staff = $_GET['check_staff'];
        if(!empty($check_staff)) {
            $query = mysqli_query($dbc,"UPDATE `security_privileges` SET `privileges` = '*hide*' WHERE tile='$tile' AND level='$level'");
        }
    }
    */

}
if($_GET['fill'] == 'privileges_config_staff') {
    $tile = $_GET['name'];
    $value = $_GET['value'];
    $staffid = $_GET['level'];

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(privilegesid) AS total_id FROM security_privileges_staff WHERE tile='$tile' AND staff='$staffid'"));

    if($get_config['total_id'] == 0) {
        $query_insert_customer = "INSERT INTO `security_privileges_staff` (`tile`, `staff`, `privileges`) VALUES ('$tile', '$staffid', '$value')";
        $result_insert_customer = mysqli_query($dbc, $query_insert_customer);
        $before_change = '';
        $history = "Security Privileges have been added. <br />";
        add_update_history($dbc, 'security_history', $history, '', $before_change);
    } else {
        $before_change = capture_before_change($dbc, 'security_privileges_staff', 'privileges', 'tile', $tile, 'staff', $staffid);
        $query_rate_card = "UPDATE `security_privileges_staff` SET `privileges` = '$value' WHERE tile='$tile' AND staff='$staffid'";
        $result_rate_card	= mysqli_query($dbc, $query_rate_card);
        $history = capture_after_change('privileges', $value);
        add_update_history($dbc, 'security_history', $history, '', $before_change);
    }
}
if($_GET['fill'] == 'privileges_config_log') {
    $tile = $_GET['name'];
    $value = $_GET['value'];
    $level = $_GET['level'];
	$contactid = $_GET['contactid'];
	$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid'");
    while($row = mysqli_fetch_assoc($result)) {
		$name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' ('.$row['contactid'].')';
    }
	date_default_timezone_set('America/Denver');
	$date = date('m/d/Y h:i:s a', time());
        $query_insert_customer = "INSERT INTO `security_privileges_log` (`tile`, `level`, `privileges`,`contact`, `date_time`) VALUES ('$tile', '$level', '$value','$name', '$date')";
        $result_insert_customer = mysqli_query($dbc, $query_insert_customer);
        $before_change = '';
        $history = "Security Privileges have been added. <br />";
        add_update_history($dbc, 'security_history', $history, '', $before_change);
}

if($_GET['fill'] == 'privileges_config_staff_log') {
    $tile = $_GET['name'];
    $value = $_GET['value'];
    $level = $_GET['level'];
	$contactid = $_GET['contactid'];
	$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid'");
    while($row = mysqli_fetch_assoc($result)) {
		$name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' ('.$row['contactid'].')';
    }
	date_default_timezone_set('America/Denver');
	$date = date('m/d/Y h:i:s a', time());
        $query_insert_customer = "INSERT INTO `security_privileges_log` (`tile`, `level`, `privileges`,`contact`, `date_time`, `type`) VALUES ('$tile', '$level', '$value','$name', '$date', 'staff')";
        $result_insert_customer = mysqli_query($dbc, $query_insert_customer);
        $before_change = '';
        $history = "Security Privileges have been added. <br />";
        add_update_history($dbc, 'security_history', $history, '', $before_change);
}

if($_GET['fill'] == 'cost_quote_followup') {
	$estimateid = $_GET['id'];
    $follow_up_date = $_GET['name'];
    $fname = $_GET['fname'];
    $lname = $_GET['lname'];
    $history = $fname.' '.$lname.' Set Follow up Date on '.date('Y-m-d H:i:s').'<br>';
	$query_update_project = "UPDATE `cost_estimate` SET follow_up_date='$follow_up_date', `history` = CONCAT(history,'$history') WHERE `estimateid` = '$estimateid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);

    $estimate_name = get_cost_estimate($dbc, $estimateid, 'estimate_name');
    $contactid = $_GET['contactid'];
    echo insert_day_overview($dbc, $contactid, 'Customer Cost Estimate', date('Y-m-d'), '', 'Set Follow up Date '.$follow_up_date.' for  '.$estimate_name);
}

if($_GET['fill'] == 'quote_followup') {
	$estimateid = $_GET['id'];
    $follow_up_date = $_GET['name'];
    $fname = $_GET['fname'];
    $lname = $_GET['lname'];
    $history = $fname.' '.$lname.' Set Follow up Date on '.date('Y-m-d H:i:s').'<br>';
	$query_update_project = "UPDATE `estimate` SET follow_up_date='$follow_up_date', `history` = CONCAT(history,'$history') WHERE `estimateid` = '$estimateid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);

    $estimate_name = get_estimate($dbc, $estimateid, 'estimate_name');
    $contactid = $_GET['contactid'];
    echo insert_day_overview($dbc, $contactid, 'Quote', date('Y-m-d'), '', 'Set Follow up Date '.$follow_up_date.' for  '.$estimate_name);
}

if($_GET['fill'] == 'cost_quote_status') {
	$estimateid = $_GET['estimateid'];
    $status = $_GET['status'];

    $estimate_name = get_cost_estimate($dbc, $estimateid, 'estimate_name');
    $contactid = $_GET['contactid'];
    echo insert_day_overview($dbc, $contactid, 'Customer Cost Estimate', date('Y-m-d'), '', 'Set Status '.$status.' for  '.$estimate_name);

    $fname = $_GET['fname'];
    $lname = $_GET['lname'];
    $deleted=0;
    if($status == 'Archive/Delete') {
        $deleted = 1;
    }

    $history = $fname.' '.$lname.' Set Status to '.$status.' on '.date('Y-m-d H:i:s').'<br>';

    if($status == 'Move To Estimate') {
        $status = 'Submitted';
    }

	$query_update_project = "UPDATE `cost_estimate` SET deleted='$deleted', status='$status', `history` = CONCAT(history,'$history') WHERE `estimateid` = '$estimateid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);

    $start_date = date('Y-m-d');

    if($status == 'Approved Quote') {
        $query_insert_invoice = "INSERT INTO `project` (`estimateid`, `businessid`, `clientid`, `projecttype`, `status`, `ratecardid`, `project_name`, `package`,`promotion`,`material`,`services`,`sred`,`labour`,`client`,`customer`,`inventory`, `equipment`, `staff`,`contractor`,`expense`,`vendor`,`custom`,`other_detail`, `created_date`, `created_by`, `start_date`, `approved_date`)
        SELECT  $estimateid,
                businessid,
                clientid,
                'client',
                'Approve as Project',
                ratecardid,
                estimate_name,
                package,
                promotion, material, services, sred, labour, client, customer, inventory, equipment, staff, contractor, expense, vendor, custom, other_detail, '$start_date', '".$_SESSION['contactid']."', '$start_date', '$start_date'
        from cost_estimate WHERE estimateid = '$estimateid'";
        $result_insert_invoice = mysqli_query($dbc, $query_insert_invoice);
        $projectid = mysqli_insert_id($dbc);

        $query_insert_detail = "INSERT INTO `project_detail` (`projectid`) VALUES ('$projectid')";
        $result_insert_detail = mysqli_query($dbc, $query_insert_detail);


	    $query_update_project = "UPDATE `temp_ticket` SET projectid='$projectid' WHERE `costestimateid` = '$estimateid'";
	    $result_update_project = mysqli_query($dbc, $query_update_project);

        //$query_insert_customer = "INSERT INTO `project` (`estimateid`, `clientid`, `projecttype`, `status`) VALUES ('$estimateid', '$clientid', 'Client', 'Approved')";
        //$result_insert_customer = mysqli_query($dbc, $query_insert_customer);
    }
}

if($_GET['fill'] == 'quote_status') {
	$estimateid = $_GET['estimateid'];
    $status = $_GET['status'];

    $estimate_name = get_estimate($dbc, $estimateid, 'estimate_name');
    $contactid = $_GET['contactid'];
    echo insert_day_overview($dbc, $contactid, 'Quote', date('Y-m-d'), '', 'Set Status '.$status.' for  '.$estimate_name);

    $fname = $_GET['fname'];
    $lname = $_GET['lname'];
    $deleted=0;
    if($status == 'Archive/Delete') {
        $deleted = 1;
    }

    $history = $fname.' '.$lname.' Set Status to '.$status.' on '.date('Y-m-d H:i:s').'<br>';

    if($status == 'Move To Estimate') {
        $status = 'Submitted';
    }

	$query_update_project = "UPDATE `estimate` SET deleted='$deleted', status='$status', `history` = CONCAT(history,'$history') WHERE `estimateid` = '$estimateid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);

    $start_date = date('Y-m-d');

    if($status == 'Approved Quote') {
        $query_insert_invoice = "INSERT INTO `project` (`estimateid`, `businessid`, `clientid`, `projecttype`, `status`, `ratecardid`, `project_name`, `package`,`promotion`,`material`,`services`,`sred`,`labour`,`client`,`customer`,`inventory`, `equipment`, `staff`,`contractor`,`expense`,`vendor`,`custom`,`other_detail`, `created_date`, `created_by`, `start_date`, `approved_date`)
        SELECT  $estimateid,
                businessid,
                clientid,
                'client',
                'Approve as Project',
                ratecardid,
                estimate_name,
                package,
                promotion, material, services, sred, labour, client, customer, inventory, equipment, staff, contractor, expense, vendor, custom, other_detail, '$start_date', '".$_SESSION['contactid']."', '$start_date', '$start_date'
        from estimate WHERE estimateid = '$estimateid'";
        $result_insert_invoice = mysqli_query($dbc, $query_insert_invoice);
        $projectid = mysqli_insert_id($dbc);

        $query_insert_detail = "INSERT INTO `project_detail` (`projectid`) VALUES ('$projectid')";
        $result_insert_detail = mysqli_query($dbc, $query_insert_detail);


	    $query_update_project = "UPDATE `temp_ticket` SET projectid='$projectid' WHERE `quoteid` = '$estimateid'";
	    $result_update_project = mysqli_query($dbc, $query_update_project);

        //$query_insert_customer = "INSERT INTO `project` (`estimateid`, `clientid`, `projecttype`, `status`) VALUES ('$estimateid', '$clientid', 'Client', 'Approved')";
        //$result_insert_customer = mysqli_query($dbc, $query_insert_customer);
    }
}
if($_GET['fill'] == 'project_status') {
	$projectid = $_GET['projectid'];
    $status = $_GET['status'];

    $contactid = $_GET['contactid'];
    $project_name = get_project($dbc, $projectid, 'project_name');
    $contactid = $_GET['contactid'];
    echo insert_day_overview($dbc, $contactid, 'Project', date('Y-m-d'), '', 'Set Status '.$status.' for  '.$project_name);

    $deleted=0;
    if($status == 'Archive/Delete') {
        $deleted = 1;
    }

    $history = 'Status changed to '.$status.' on '.date('Y-m-d H:i:s').'<br>';

	$query_update_project = "UPDATE `project` SET deleted='$deleted', status='$status' WHERE `projectid` = '$projectid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);
	$user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
	mysqli_query($dbc, "INSERT INTO `project_history` (`updated_by`, `description`, `projectid`) VALUES ('$user', '$history', '$projectid')");
}
if($_GET['fill'] == 'client_project_status') {
	$projectid = $_GET['projectid'];
    $status = $_GET['status'];

    $contactid = $_GET['contactid'];
    $project_name = get_client_project($dbc, $projectid, 'project_name');
    $contactid = $_GET['contactid'];
    echo insert_day_overview($dbc, $contactid, 'Client Project', date('Y-m-d'), '', 'Set Status '.$status.' for  '.$project_name);

    $deleted=0;
    if($status == 'Archive/Delete') {
        $deleted = 1;
    }

    $history = 'Status changed to '.$status.' on '.date('Y-m-d H:i:s').'<br>';

	$query_update_project = "UPDATE `client_project` SET deleted='$deleted', status='$status', `history` = CONCAT(history,'$history') WHERE `projectid` = '$projectid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);
}
if($_GET['fill'] == 'project_etaquote') {
	$projectid = $_GET['id'];
    $eta_quote = $_GET['name'];
    $history = 'Set ETA Quote on '.date('Y-m-d H:i:s').'<br>';
	$query_update_project = "UPDATE `project` SET eta_quote='$eta_quote' WHERE projectid = '$projectid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);
	$user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
	mysqli_query($dbc, "INSERT INTO `project_history` (`updated_by`, `description`, `projectid`) VALUES ('$user', '$history', '$projectid')");
}
if($_GET['fill'] == 'client_project_etaquote') {
	$projectid = $_GET['id'];
    $eta_quote = $_GET['name'];
    $history = 'Set ETA Quote on '.date('Y-m-d H:i:s').'<br>';
	$query_update_project = "UPDATE `client_project` SET eta_quote='$eta_quote', `history` = CONCAT(history,'$history') WHERE projectid = '$projectid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);
}

if($_GET['fill'] == 'invoice') {
    if(!empty($_GET['serviceid'])) {
        $serviceid = $_GET['serviceid'];
        $invoiceid = $_GET['invoiceid'];

        if($invoiceid == 0) {
            $invoice_date = date('Y-m-d');
        } else {
            $invoice_date = get_all_from_invoice($dbc, $invoiceid, 'invoice_date');
        }

        $result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT r.cust_price, s.gst_exempt, s.service_code, r.editable FROM services s, company_rate_card r WHERE r.deleted=0 AND s.serviceid='$serviceid' AND s.serviceid = r.item_id AND r.tile_name='Services' AND '$invoice_date' >= r.start_date AND (IFNULL(r.end_date,'0000-00-00') = '0000-00-00' OR r.end_date >= '$invoice_date')"));
        echo $result['cust_price'].'**'.$result['gst_exempt'];
        echo '**';
        if (strpos($result['service_code'],'WCB') !== false) {
            echo '
            <option selected value="General">General</option>
            <option value="WCB">WCB</option>';
        } else if (strpos($result['service_code'],'MVA') !== false) {
            echo '
            <option selected value="General">General</option>
            <option value="MVA">MVA</option>';
        } else {
            echo '
            <option selected value="General">General</option>';
        }
    }

    if(!empty($_GET['category'])) {
        $category = $_GET['category'];
        $app_type = $_GET['app_type'];
        $invoiceid = $_GET['invoiceid'];
        $serviceid = $_GET['sid'];

        if($invoiceid > 0) {
            $invoice_date = get_all_from_invoice($dbc, $invoiceid, 'invoice_date');
        } else {
            $invoice_date = date('Y-m-d');
        }

        if($app_type == '') {
            //without rate card
            //$query = mysqli_query($dbc,"SELECT serviceid, heading, category, fee FROM services WHERE category='$category'");

            //with rate card
            $query = mysqli_query($dbc,"SELECT s.serviceid, s.heading, r.cust_price, r.editable FROM services s,  company_rate_card r WHERE s.deleted=0 AND r.deleted=0 AND s.category='$category' AND s.serviceid = r.item_id AND '$invoice_date' >= r.start_date AND (IFNULL(r.end_date,'0000-00-00') = '0000-00-00' OR r.end_date >= '$invoice_date')");
        } else {
            //without rate card
            //$query = mysqli_query($dbc,"SELECT serviceid, heading, category, fee FROM services WHERE category='$category' AND (appointment_type = '' OR appointment_type='$app_type')");

            //with rate card
            $query = mysqli_query($dbc,"SELECT s.serviceid, s.heading, r.cust_price, r.editable FROM services s,  company_rate_card r WHERE s.deleted=0 AND r.deleted=0 AND s.category='$category' AND s.serviceid = r.item_id AND (s.appointment_type = '' OR s.appointment_type='$app_type') AND '$invoice_date' >= r.start_date AND (IFNULL(r.end_date,'0000-00-00') = '0000-00-00' OR r.end_date >= '$invoice_date')");
        }
        echo '<option value=""></option>';
        while($row = mysqli_fetch_array($query)) {
           echo "<option data-editable='".$row['editable']."' ".($serviceid==$row['serviceid'] ? 'selected' : '')." value='". $row['serviceid']."'>".$row['heading'].'</option>';
        }
    }

    if(!empty($_GET['inventoryid'])) {
        $inventoryid = $_GET['inventoryid'];
        $type = $_GET['type'];
        echo get_all_from_inventory($dbc, $inventoryid, $type).'#*#'.get_all_from_inventory($dbc, $inventoryid, 'gst_exempt');
    }

    if(!empty($_GET['injuryid'])) {
        $injuryid = $_GET['injuryid'];
        echo get_all_from_injury($dbc, $injuryid, 'mva_claim_price');
    }

    if(!empty($_GET['promotionid'])) {
        $promotionid = $_GET['promotionid'];
        $result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT cost FROM promotion WHERE promotionid='$promotionid'"));
        echo $result['cost'];
    }

    if(!empty($_GET['insurer'])) {
        $invoiceid = $_GET['insurer'];
	    $query_update_es = "UPDATE `invoice` SET `paid` = 'Waiting on Insurer' WHERE `invoiceid` = '$invoiceid'";
	    $result_update_es = mysqli_query($dbc, $query_update_es);

	    $query_update_es = "UPDATE `invoice_insurer` SET `paid` = 'Waiting on Insurer' WHERE `invoiceid` = '$invoiceid'";
	    $result_update_es = mysqli_query($dbc, $query_update_es);
    }
}

if($_GET['fill'] == 'startdl') {
    $driverid = $_GET['driverid'];

    $result = mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND contactid NOT IN($driverid)");

    echo "<option value=''></option>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<option value = '".$row['contactid']."'>".$row['first_name'].' ' .$row['last_name']."</option>";
    }
}
if($_GET['fill'] == 'tile_menu_choice') {
    $software_styler_choice = $_GET['value'];
	$contactid = $_GET['contactid'];
        $query_update_employee = "UPDATE `contacts` SET software_tile_menu_choice = '$software_styler_choice' WHERE contactid='$contactid'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);

}

/*
 * Title:		Settings
 * File:		menu_config.php
 * Function:	Changes the format of the software
 */
if($_GET['fill'] == 'tile_menu_settings') {
    $software_styler_choice = $_GET['value'];
	$settingtype = $_GET['settingtype'];
	$contactid = $_GET['contactid'];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(contactid) AS contactid FROM user_settings WHERE contactid='$contactid'"));
    if($get_config['contactid'] > 0) {
		if($settingtype == 'classic') {
			$query_update_employee = "UPDATE `user_settings` SET classic_menu_size = '$software_styler_choice' WHERE contactid='$contactid'";
		} else if($settingtype == 'tilesize') {
			$query_update_employee = "UPDATE `user_settings` SET tile_size = '$software_styler_choice' WHERE contactid='$contactid'";
		} else if($settingtype == 'newsboardredirect') {
			$query_update_employee = "UPDATE `user_settings` SET newsboard_redirect = '$software_styler_choice' WHERE contactid='$contactid'";
        } else if($settingtype == 'calendarredirect') {
            $query_update_employee = "UPDATE `user_settings` SET calendar_redirect = '$software_styler_choice' WHERE contactid='$contactid'";
        } else if($settingtype == 'daysheetredirect') {
            $query_update_employee = "UPDATE `user_settings` SET daysheet_redirect = '$software_styler_choice' WHERE contactid='$contactid'";
		} else if($settingtype == 'alerticon') {
            $query_update_employee = "UPDATE `user_settings` SET alert_icon = '$software_styler_choice' WHERE contactid='$contactid'";
        } else {
			$query_update_employee = "UPDATE `user_settings` SET dropdown_menu_size = '$software_styler_choice' WHERE contactid='$contactid'";
		}
		echo 'Query: '. $query_update_employee.' x Setting type: x'. $settingtype.'x';
		if($settingtype == 'tilesize') {
			echo 'do this';
		} else {
			echo 'dont do this';
		}
		$result_update_employee = mysqli_query($dbc, $query_update_employee) or die(mysqli_error($dbc));
    } else {
		if($settingtype == 'classic') {
			$query_insert_config = "INSERT INTO `user_settings` (`contactid`, `classic_menu_size`) VALUES ('$contactid', '$software_styler_choice')";
		} else if($settingtype == 'tilesize') {
			$query_insert_config = "INSERT INTO `user_settings` (`contactid`, `tile_size`) VALUES ('$contactid', '$software_styler_choice')";
        } else if($settingtype == 'newsboardredirect') {
            $query_insert_config = "INSERT INTO `user_settings` (`contactid`, `newsboard_redirect`) VALUES ('$contactid', '$software_styler_choice')";
        } else if($settingtype == 'calendarredirect') {
            $query_insert_config = "INSERT INTO `user_settings` (`contactid`, `calendar_redirect`) VALUES ('$contactid', '$software_styler_choice')";
        } else if($settingtype == 'daysheetredirect') {
            $query_insert_config = "INSERT INTO `user_settings` (`contactid`, `daysheet_redirect`) VALUES ('$contactid', '$software_styler_choice')";
		} else if($settingtype == 'alerticon') {
            $query_insert_config = "INSERT INTO `user_settings` (`contactid`, `alert_icon`) VALUES ('$contactid', '$software_styler_choice')";
        } else {
			$query_insert_config = "INSERT INTO `user_settings` (`contactid`, `dropdown_menu_size`) VALUES ('$contactid', '$software_styler_choice')";
		}

        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

}
if($_GET['fill'] == 'toggle_tile_menu') {
    $toggler = $_GET['val'];
	$contactid = $_GET['contactid'];
        $query_update_employee = "UPDATE `contacts` SET toggle_tile_menu = '$toggler' WHERE contactid='$contactid'";

        $result_update_employee = mysqli_query($dbc, $query_update_employee);

}

if ( $_GET['fill'] == 'newsboard_menu_choice' ) {
    $newsboard_menu_choice	= $_GET['value'];
	$contactid				= $_GET['contactid'];
	$query_update_employee	= "UPDATE `contacts` SET newsboard_menu_choice = '$newsboard_menu_choice' WHERE contactid = '$contactid'";
	$result_update_employee	= mysqli_query ( $dbc, $query_update_employee );
	if(session_status() == PHP_SESSION_NONE) {
		session_start(['cookie_lifetime' => 518400]);
		$_SERVER['page_load_info'] .= 'Session Started: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
	}
	$_SESSION[ 'newsboard_menu_choice' ] = $newsboard_menu_choice;
	session_write_close();
}

if($_GET['fill'] == 'styler_configuration') {
    $software_styler_choice = $_GET['value'];
    if($_GET['subtab'] == 'security') {
        $level = $_GET['level'];
        if(!empty($level)) {
            mysqli_query($dbc, "INSERT INTO `field_config_security_level_theme` (`security_level`,`theme`) SELECT '$level','$software_styler_choice' FROM (SELECT COUNT(*) rows FROM `field_config_security_level_theme` WHERE `security_level`='$level') num WHERE num.rows=0");
            mysqli_query($dbc, "UPDATE `field_config_security_level_theme` SET `theme`='$software_styler_choice' WHERE `security_level`='$level'");
        }
    } else if($_GET['subtab'] == 'software') {
        set_config($dbc, 'software_default_theme', $software_styler_choice);
    } else {
        $contactid = $_GET['contactid'];
            $query_update_employee = "UPDATE `contacts` SET software_styler_choice = '$software_styler_choice' WHERE contactid='$contactid'";
            $result_update_employee = mysqli_query($dbc, $query_update_employee);
    }

}
if($_GET['fill'] == 'include_in_orders') {
    $id = $_GET['status'];
	$type = $_GET['type'];
	$name = $_GET['name'];
	$value = $_GET['value'];
	if($name == 'product') {
		$table = 'products';
		$queryid = 'productid';
	} else if($name == 'inventory') {
		$table = 'inventory';
		$queryid = 'inventoryid';
	} else if($name == 'vpl') {
		$table = 'vendor_price_list';
		$queryid = 'inventoryid';
	} else if($name == 'services') {
		$table = 'services';
		$queryid = 'serviceid';
	} else if($name == 'orderlist') {
		$table = 'order_lists';
		$queryid = 'order_id';
	}
	if($type == 'po') {
		$query_update_employee = "UPDATE `".$table."` SET include_in_po = '$value' WHERE ".$queryid." = '$id'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
	} else if($type == 'so') {
		$query_update_employee = "UPDATE `".$table."` SET include_in_so = '$value' WHERE ".$queryid." = '$id'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
	} else if($type == 'pos') {
		$query_update_employee = "UPDATE `".$table."` SET include_in_pos = '$value' WHERE ".$queryid." = '$id'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
	} else if($type == 'inventory') {
		$query_update_employee = "UPDATE `".$table."` SET include_in_inventory = '$value' WHERE ".$queryid." = '$id'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);

        if($value == 1) {
        $query_insert_invoice = "INSERT INTO inventory (`productid`, `product_type`, `category`, `product_code`, `heading`, `cost`, `description`, `quote_description`, `invoice_description`, `ticket_description`, `final_retail_price`, `admin_price`, `wholesale_price`, `commercial_price`, `client_price`, `purchase_order_price`, `sales_order_price`, `minimum_billable`, `hourly_rate`, `estimated_hours`, `actual_hours`, `msrp`, `name`, `fee`, `unit_price`, `unit_cost`, `rent_price`, `drum_unit_cost`, `drum_unit_price`, `tote_unit_cost`, `tote_unit_price`, `rental_days`, `rental_weeks`, `rental_months`, `rental_years`, `reminder_alert`, `daily`,`weekly`, `monthly`, `annually`,  `total_days`, `total_hours`, `total_km`, `total_miles`, `include_in_so`,`include_in_po`,`include_in_pos`, `include_in_inventory`) SELECT productid, product_type, category, product_code, heading, cost, description, quote_description, invoice_description, ticket_description, final_retail_price, admin_price, wholesale_price, commercial_price, client_price, purchase_order_price, sales_order_price, minimum_billable, hourly_rate, estimated_hours, actual_hours, msrp, name, fee, unit_price, unit_cost, rent_price, drum_unit_cost, drum_unit_price, tote_unit_cost, tote_unit_price, rental_days, rental_weeks, rental_months, rental_years, reminder_alert, daily, weekly, monthly, annually, total_days, total_hours, total_km, total_miles, include_in_so, include_in_po, include_in_pos, include_in_inventory from `products` WHERE `productid` = '$id'";
        $result_insert_invoice = mysqli_query($dbc, $query_insert_invoice);
        }
        if($value == 0) {
            $query = mysqli_query($dbc,"DELETE FROM inventory WHERE `productid`='$id'");
        }
	} else if($type == 'product') {
		$query_update_employee = "UPDATE `".$table."` SET include_in_product = '$value' WHERE ".$queryid." = '$id'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);

        if($value == 1) {
        $query_insert_invoice = "INSERT INTO `products` (`inventoryid`, `code`, `category`, `sub_category`, `part_no`,	`description`, `comment`, `question`, `request`, `display_website`, `vendorid`, `size`, `weight`, `type`, `name`, `date_of_purchase`, `purchase_cost`, `sell_price`, `markup`, `freight_charge`, `min_bin`, `current_stock`, `final_retail_price`, `admin_price`, `wholesale_price`, `commercial_price`, `client_price`, `purchase_order_price`, `sales_order_price`, `minimum_billable`, `estimated_hours`, `actual_hours`, `msrp`, `quote_description`, `usd_invoice`, `shipping_rate`, `shipping_cash`, `exchange_rate`, `exchange_cash`, `cdn_cpu`, `cogs_total`, `location`, `inv_variance`, `average_cost`, `asset`, `revenue`, `buying_units`, `selling_units`, `stocking_units`, `preferred_price`, `web_price`, `id_number`, `operator`, `lsd`, `quantity`, `product_name`, `cost`, `usd_cpu`, `commission_price`, `markup_perc`, `current_inventory`, `write_offs`, `min_max`, `status`, `note`, `unit_price`, `unit_cost`, `rent_price`, `rental_days`, `rental_weeks`, `rental_months`, `rental_years`, `reminder_alert`, `daily`,`weekly`, `monthly`, `annually`,  `total_days`, `total_hours`, `total_km`, `total_miles`, `bill_of_material`, `include_in_so`,`include_in_po`,`include_in_pos`, `drum_unit_cost`, `drum_unit_price`, `tote_unit_cost`, `tote_unit_price`) SELECT inventoryid, code, category, sub_category, part_no, description, comment, question, request, display_website, vendorid, size, weight, type, name, date_of_purchase, purchase_cost, sell_price, markup, freight_charge, min_bin, current_stock, final_retail_price, admin_price, wholesale_price, commercial_price, client_price, purchase_order_price, sales_order_price, minimum_billable, estimated_hours, actual_hours, msrp, quote_description, usd_invoice, shipping_rate, shipping_cash, exchange_rate, exchange_cash, cdn_cpu, cogs_total, location, inv_variance, average_cost, asset, revenue, buying_units, selling_units, stocking_units, preferred_price, web_price, id_number, operator, lsd, quantity, product_name, cost, usd_cpu, commission_price, markup_perc, current_inventory, write_offs, min_max, status, note, unit_price, unit_cost, rent_price, rental_days, rental_weeks, rental_months, rental_years, reminder_alert, daily, weekly, monthly, annually, total_days, total_hours, total_km, total_miles, bill_of_material, include_in_so, include_in_po, include_in_pos, drum_unit_cost, drum_unit_price, tote_unit_cost, tote_unit_price from `inventory` WHERE `inventoryid` = '$id'";
        $result_insert_invoice = mysqli_query($dbc, $query_insert_invoice);
        }
        if($value == 0) {
            $query = mysqli_query($dbc,"DELETE FROM products WHERE `inventoryid`='$id'");
        }
	}
}

if ( $_GET['fill'] == 'info_toggle_state' ) {
	$_SESSION[ 'info_toggle' ] = $_GET['state'];
}
if ( $_GET['fill'] == 'fullscreen' ) {
	session_start(['cookie_lifetime' => 518400]);
    $_SESSION['fullscreen'] = $_GET['state'];
    /* if ( isset($_SESSION['fullscreen']) ) {
        echo $_SESSION['fullscreen'];
    } else {
        echo 'Not set';
    } */
    session_write_close();
}

if($_GET['fill'] == 'show_impexp_contact') {
	$value = $_GET['value'];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_impexp_contact'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$value' WHERE name='show_impexp_contact'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('show_impexp_contact', '$value')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
}

if ( $_GET[ 'fill' ] == 'display_on_website' ) {
	$display	= $_GET['display'];
	$invid		= $_GET['invid'];

	$query_update_display	= "UPDATE `inventory` SET `display_website` = '$display' WHERE `inventoryid` = '$invid'";
	$result_update_display	= mysqli_query($dbc, $query_update_display);

	/*
	$selected = $_GET['selected'];
	$unSelected = $_GET['unselected'];

	//if ( !empty($selected) ) {
		/*for ( $i=0; $i < count($selected); $i++ ) {
			//$query_update_display	= "UPDATE inventory SET display_website = 'Yes' WHERE inventoryid = '$selected[i]'";
			//$result_update_display	= mysqli_query($dbc, $query_update_display);
			//echo $selected[$i] . '<br>';
			echo $i . '<br>';
		}*/
		//echo 'selected ' . count($selected);
		//echo 'Update, yay!';
	//}

	//if ( !empty($unSelected) ) {
		//echo 'unselected ' . count($unSelected);
	//}*/
}
?>
<?php
//Clinic Ace

if($_GET['fill'] == 'treatment') {
    $patientid = $_GET['patientid'];
    $injuryid = $_GET['injuryid'];
    echo get_email($dbc, $patientid).'#*#';

	$query = mysqli_query($dbc,"SELECT injuryid, injury_name, injury_type, injury_date FROM patient_injury WHERE contactid = '$patientid' AND discharge_date IS NULL");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option ".($injuryid == $row['injuryid'] ? 'selected' : '')." value='".$row['injuryid']."'>".$row['injury_type'].' : '.$row['injury_name'].' : '.$row['injury_date'].'</option>';
	}
}

if(!empty($_GET['patient_type'])) {
	echo $_GET['patient_type'];
}

if($_GET['fill'] == 'injury') {
	$insurer = $_GET['insurer'];

	$insurer = str_replace("__","&",$insurer);

	$query = mysqli_query($dbc,"SELECT distinct(category) FROM services WHERE REPLACE(`service_type`, ' ', '') = '$insurer'");

	$query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE REPLACE(`name`, ' ', '') = '$insurer' AND category='Adjuster'");
    echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['contactid']."'>".$row['first_name'].' '.$row['last_name']. '</option>';
	}
}

if($_GET['fill'] == 'mrbs') {
	$patient = $_GET['patient'];

    $table_name = strtolower($patient[0]);

    $result = mysqli_query($dbc, "SELECT first_name, last_name, contactid FROM contacts_fn_".$table_name." WHERE deleted = 0");

    echo '<select id="f_patient" name="f_patient" onkeyup="selectPatient(this)" class="chosen-select-deselect"><option value=""></option>';
	while($row = mysqli_fetch_array($result)) {
        $first_name_data = decryptIt($row['first_name']);
        $last_name_data = decryptIt($row['last_name']);

        if ((stripos($first_name_data,$patient) !== false) || (stripos($last_name_data,$patient) !== false)) {
		    echo "<option value='".$row['contactid']."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']). '</option>';
        }
	}
    echo '</select>';
}

if($_GET['fill'] == 'patient') {
	$patientid = $_GET['patientid'];
	$query = mysqli_query($dbc,"SELECT injuryid, injury_type, injury_name, injury_date FROM patient_injury WHERE patientid = '$patientid'");
    echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['injuryid']."'>".$row['injury_type'].' : '.$row['injury_name'].' : '.$row['injury_date'].'</option>';
	}
}

if($_GET['fill'] == 'compensation') {
    $category = $_GET['category'];

    $query = mysqli_query($dbc,"SELECT serviceid, service_type, category FROM services WHERE category='$category'");
    echo '<option value=""></option>';
    while($row = mysqli_fetch_array($query)) {
        echo "<option value='". $row['serviceid']."'>".$row['service_type'].'</option>';
    }
}

if($_GET['fill'] == 'treatmentnotes') {
	$patientid = $_GET['patientid'];
    $injuryid = $_GET['injuryid'];
	$query = mysqli_query($dbc,"SELECT * FROM treatment WHERE patientid = '$patientid' AND injuryid = '$injuryid' ORDER BY treatmentid DESC LIMIT 1");
    $notes = '';
    $subjective = '';
    $objective = '';
    $assessment = '';
    $plan = '';
	while($row = mysqli_fetch_array($query)) {
        if($row['notes'] != '') {
		    $notes .= $row['notes'].'==================';
        }
        if($row['subjective'] != '') {
		    $subjective .= $row['subjective'].'==================';
        }
        if($row['objective'] != '') {
		    $objective .= $row['objective'].'==================';
        }
        if($row['assessment'] != '') {
		    $assessment .= $row['assessment'].'==================';
        }
        if($row['plan'] != '') {
		    $plan .= $row['plan'].'==================';
        }
	}
    echo $notes.'**##5th##**'.$subjective.'**##5th##**'.$objective.'**##5th##**'.$assessment.'**##5th##**'.$plan;
}

if($_GET['fill'] == 'booking_appoint') {
	$bookingid = $_GET['id'];
    $name = $_GET['name'];
	$query_update_es = "UPDATE `booking` SET `appoint_date` = '$name' WHERE `bookingid` = '$bookingid'";
	$result_update_es = mysqli_query($dbc, $query_update_es);
}

if($_GET['fill'] == 'booking_followup') {
	$bookingid = $_GET['id'];
    $name = $_GET['name'];
	$query_update_es = "UPDATE `booking` SET `follow_up_call_date` = '$name' WHERE `bookingid` = '$bookingid'";
	$result_update_es = mysqli_query($dbc, $query_update_es);
}

if($_GET['fill'] == 'referral_followup') {
	$referralid = $_GET['id'];
    $name = $_GET['name'];
	$query_update_es = "UPDATE `crm_referrals` SET `follow_up_call_date` = '$name' WHERE `referralid` = '$referralid'";
	$result_update_es = mysqli_query($dbc, $query_update_es);
}

if($_GET['fill'] == 'bookingstatus') {
	$bookingid = $_GET['id'];
    $name = $_GET['name'];
	$query_update_es = "UPDATE `booking` SET `follow_up_call_status` = '$name' WHERE `bookingid` = '$bookingid'";
	$result_update_es = mysqli_query($dbc, $query_update_es);

    $calid = get_calid_from_bookingid($dbc, $bookingid);
    $query_update_cal = "UPDATE `mrbs_entry` SET `patientstatus` = '$name' WHERE `id` = '$calid'";
    $result_update_cal = mysqli_query($dbc, $query_update_cal);
}

/*if($_GET['fill'] == 'client_booking_followup') {
	$bookingid = $_GET['id'];
    $name = $_GET['name'];
	$query_update_es = "UPDATE `booking` SET `client_follow_up_date` = '$name' WHERE `bookingid` = '$bookingid'";
	$result_update_es = mysqli_query($dbc, $query_update_es);
}
*/
if($_GET['fill'] == 'follow_up_status') {
	$followupcallid = $_GET['id'];
    $name = filter_var($_GET['name'],FILTER_SANITIZE_STRING);
	$query_update_es = "UPDATE `follow_up_calls` SET `follow_up_status` = '$name' WHERE `followupcallid` = '$followupcallid'";
	$result_update_es = mysqli_query($dbc, $query_update_es);
}

if($_GET['fill'] == 'survey') {
	$surveyid = $_GET['name'];
    $value = $_GET['value'];
	$query_update_es = "UPDATE `crm_feedback_survey_form` SET `deleted` = '$value' WHERE `surveyid` = '$surveyid'";
	$result_update_es = mysqli_query($dbc, $query_update_es);
}

if($_GET['fill'] == 'insurer') {
    $invoiceid = $_GET['invoiceid'];

	$query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT cost FROM report_insurer WHERE invoiceid = '$invoiceid'"));
	echo $query['cost'];
}
if($_GET['fill'] == 'tile_menu_choice') {
    $software_styler_choice = $_GET['value'];
	$contactid = $_GET['contactid'];
        $query_update_employee = "UPDATE `contacts` SET software_tile_menu_choice = '$software_styler_choice' WHERE contactid='$contactid'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);

}

if($_GET['fill'] == 'contact_maintenance') {
    $contactid = $_GET['contactid'];

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT maintenance, first_name, last_name FROM contacts WHERE contactid='$contactid'"));

    if($get_field_config['maintenance'] == 1) {
        $main = 0;
    } else {
        $main = 1;
    }

    $first_name = decryptIt($get_field_config['first_name']);
    $table_name = strtolower($first_name[0]);

    $result_insert_vendor = mysqli_query($dbc, "UPDATE `contacts_fn_".$table_name."` SET `maintenance` = '$main' WHERE `contactid` = '$contactid'");

    $last_name = decryptIt($get_field_config['last_name']);
    $table_name = strtolower($last_name[0]);

    $result_insert_vendor = mysqli_query($dbc, "UPDATE `contacts_ln_".$table_name."` SET `maintenance` = '$main' WHERE `contactid` = '$contactid'");

    $query_update_employee = "UPDATE `contacts` SET maintenance = '$main' WHERE contactid='$contactid'";
    $result_update_employee = mysqli_query($dbc, $query_update_employee);
}

if($_GET['fill'] == 'arcollection') {
    $invoiceinsurerid = $_GET['invoiceinsurerid'];
    $query_update_employee = "UPDATE `invoice_insurer` SET collection = 1 WHERE invoiceinsurerid='$invoiceinsurerid'";
    $result_update_employee = mysqli_query($dbc, $query_update_employee);
}
if($_GET['fill'] == 'insurerAR') {
    $invoiceinsurerid = preg_replace('/[^0-9]/', '', $_GET['invoiceinsurerid']);
    $status = preg_replace('/[^0-9]/', '', $_GET['status']);
    $query_update = "UPDATE `invoice_insurer` SET new='$status' WHERE invoiceinsurerid='$invoiceinsurerid'";
    $result_update = mysqli_query($dbc, $query_update);
}
if($_GET['fill'] == 'drop_off_analysis_dc') {
    $injuryid = $_GET['injuryid'];
    $query_update_employee = "UPDATE `patient_injury` SET drop_off_analysis_dc = 1 WHERE injuryid='$injuryid'";
    $result_update_employee = mysqli_query($dbc, $query_update_employee);
}
if($_GET['fill'] == 'drop_off_analysis_cc') {
    $injuryid = $_GET['injuryid'];
    $query_update_employee = "UPDATE `patient_injury` SET drop_off_analysis_cc = 1 WHERE injuryid='$injuryid'";
    $result_update_employee = mysqli_query($dbc, $query_update_employee);
}
if($_GET['fill'] == 'drop_off_analysis_status') {
    $injuryid = $_GET['injuryid'];
    $d_status = $_GET['d_status'];
    $query_update_employee = "UPDATE `patient_injury` SET drop_off_analysis_status = '$d_status' WHERE injuryid='$injuryid'";
    $result_update_employee = mysqli_query($dbc, $query_update_employee);
}

if($_GET['fill'] == 'business_sites') {
    $businesssiteid = $_GET['businesssiteid'];

	$query = mysqli_query($dbc,"SELECT contactid, site_name FROM contacts WHERE businessid = '$businesssiteid' AND category='Sites'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option ".($businesssiteid == $row['contactid'] ? 'selected' : '')." value='".$row['contactid']."'>".$row['site_name'].'</option>';
	}
}
if($_GET['fill'] == 'assessment_followup') {
	$booking = $_POST['booking'];
	$status = $_POST['followup'];
	if($status == 'Complete') {
		$query = "UPDATE `booking` SET `assessment_followup_date`=CURRENT_TIMESTAMP WHERE `bookingid`='$booking'";
	} else {
		$query = "UPDATE `booking` SET `assessment_followup_date`=NULL WHERE `bookingid`='$booking'";
	}
	mysqli_query($dbc, $query);
}

if($_GET['fill'] == 'appointments_history') {
	$result_no  = $_GET['result_no'];
    $contactid  = $_GET['contactid'];
    $history_response = '';

    $result = mysqli_query ( $dbc, "SELECT `bookingid`, `patientid`, `therapistsid`, `appoint_date` FROM `booking` WHERE `patientid`='$contactid' ORDER BY `appoint_date` DESC LIMIT $result_no,999999" );

    if ( mysqli_num_rows($result) > 0 ) {
        while ( $row=mysqli_fetch_assoc($result) ) {
            $therapist  = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `first_name`, `last_name` FROM `contacts` WHERE `contactid`='{$row['therapistsid']}'" ) );
            $history_response .= '<div class="overview-blue assessment-history">Assessment with ' . decryptIt($therapist['first_name']) . ' ' . decryptIt($therapist['last_name']) . '</div>';
            $history_response .= '<div>' . date('l, F j, Y', strtotime($row['appoint_date'])) . ' at ' . date('h:i A', strtotime($row['appoint_date'])) . '</div>';
        }
    }

    echo $history_response;
}

if($_GET['fill'] == 'contact_field_category') {
	$category = strtolower($_GET['category']);
    $folder_name = $_GET['folder_name'];
    echo get_config($dbc, $folder_name.'_'.$category.'_field_subtabs');
}
if($_GET['fill'] == 'page_options') {
	$field = filter_var($_POST['field']);
	$value = filter_var($_POST['value']);
	$path = urldecode(filter_var($_POST['path']));
	$contactid = $_SESSION['contactid'];
	mysqli_query($dbc, "INSERT INTO `page_options` (`contactid`, `php_self`) SELECT '$contactid', '$path' FROM (SELECT COUNT(*) rows FROM `page_options` WHERE `php_self`='$path' AND `contactid`='$contactid') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `page_options` SET `$field`='$value' WHERE `php_self`='$path' AND `contactid`='$contactid'");
} else if($_GET['fill'] == 'ajax_save_general') {
	$name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT '$name' FROM (SELECT COUNT(*) numrows FROM `general_configuration` WHERE `name`='$name') rows WHERE numrows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$value' WHERE `name`='$name'");
}
if($_GET['fill'] == 'get_scale_width') {
    $php_self = $_GET['php_self'];
    $scale_style = '';
    $page_options = mysqli_query($dbc, "SELECT * FROM `page_options` WHERE `php_self`='".filter_var($php_self,FILTER_SANITIZE_STRING)."' AND `contactid`='".$_SESSION['contactid']."'");
    if($page_options && $page_options = mysqli_fetch_assoc($page_options)) {
        if($page_options['scale_width'] > 0) {
            $scale_style = 'width:'.$page_options['scale_width'].'%;';
        }
    }
    $scale_style = rtrim(explode(':',$scale_style)[1],';');
    echo $scale_style;
} else if($_GET['fill'] == 'send_email') {
	if($_POST['send_every_email'] != 'true') {
		$last_time = get_config($dbc, 'last_sent_email_'.config_safe_str($_POST['subject']));
		if($last_time != date('Y-m-d')) {echo "Sending Once...";
			try {
				send_email('', $_POST['send_to'], '', '', $_POST['subject'], $_POST['body'], '');
				mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'last_sent_email_".config_safe_str($_POST['subject'])."' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='last_sent_email_".config_safe_str($_POST['subject'])."') num WHERE num.rows=0");
				mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".date('Y-m-d')."' WHERE `name`='last_sent_email_".config_safe_str($_POST['subject'])."'");
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
	} else {
		try {
			send_email('', $_POST['send_to'], '', '', $_POST['subject'], $_POST['body'], '');
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
}
/* Apply Gift Card */
if ( $_GET['fill'] == 'posGF' ) {
  $gf_number = $_GET['gf_number'];
  $today_date = date('Y-m-d');
  $gf_row = mysqli_fetch_assoc( mysqli_query( $dbc, "select * from pos_giftcards where giftcard_number = '$gf_number' and deleted = 0 and issue_date <= '$today_date' AND (expiry_date >= '$today_date' OR IFNULL(`expiry_date`,'0000-00-00')='0000-00-00')"));
  if($gf_row['value'] == null || $gf_row['value'] == '') {
    echo "na";
  }
  else if($gf_row['value'] - $gf_row['used_value'] == 0) {
    echo "used";
  }
  else {
  	echo $gf_row['value'] - $gf_row['used_value'];
  }
}

// Save Text Editor Template Fields
else if($_GET['action'] == 'text_template_field') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$value = filter_var(htmlentities($_POST['value']),FILTER_SANITIZE_STRING);
	if($id > 0) {
		mysqli_query($dbc, "UPDATE `text_templates` SET `$field`='$value' WHERE `id`='$id'");
	} else {
		$tile = filter_var($_POST['tile'],FILTER_SANITIZE_STRING);
		$tab = filter_var($_POST['tab'],FILTER_SANITIZE_STRING);
		$template_field = filter_var($_POST['template_field'],FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "INSERT INTO `text_templates` (`tile`, `tab`, `field`, `$field`, `sort`) SELECT '$tile', '$tab', '$template_field', '$value', COUNT(*) FROM `text_templates` WHERE `tile`='$tile' AND `tab`='$tab' AND `field`='$field'");
		echo mysqli_insert_id($dbc);
	}
}
// Save Text Editor Template Sort
else if($_GET['action'] == 'text_template_sort') {
	foreach($_POST['template_id'] as $i => $id) {
		$id = filter_var($id,FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "UPDATE `text_templates` SET `sort`='$i' WHERE `id`='$id'");
	}
}
else if($_GET['action'] == 'summary_block_sort') {
    $id = $_POST['template_id'];
    $id_val = implode('||*||',$id);

    $columnExist = mysqli_query($dbc, "SELECT `name` from `general_configuration` WHERE `name` = 'summary_block_sort' ");
    if($columnExist->num_rows>0){
        mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$id_val."' WHERE `name`='summary_block_sort'");
    }else{
        $mm = mysqli_query($dbc, "INSERT INTO `general_configuration` (`configid`, `name`, `value`, `calllog_schedule_status`) VALUES (NULL, 'summary_block_sort', '$id_val', NULL)");
    }
}
// Get  User's Profile Image
else if($_GET['action'] == 'user_profile_id') {
	$id = $_GET['user'];
	if($id > 0) {
		echo profile_id($dbc, $id);
	}
}
// Save a Config Option
else if($_GET['action'] == 'general_config') {
	set_config($dbc, $_POST['name'], $_POST['value']);
}
// Sync Data from Live to Demo
else if($_GET['action'] == 'sync_data') {
	$db_all = @mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD);
	$tables = [];
	switch($_GET['table']) {
		case 'project':
			$tables = ['project','project_actions','project_billable','project_comment','project_custom_details','project_deliverables_output','project_detail','project_document','project_form','project_history','project_invoice','project_path_custom_milestones','project_payments','project_scope','project_path_milestone','project_milestone_checklist','project_milestone_checklist_time','project_milestone_document'];
			break;
		case 'tickets':
			$tables = ['tickets','ticket_attached','ticket_checklist','ticket_checklist_uploads','ticket_comment','ticket_deliverables','ticket_document','ticket_history','ticket_manifests','ticket_notifications','ticket_pdf','ticket_pdf_field_values','ticket_pdf_fields','ticket_purchase_orders','ticket_schedule','ticket_service_checklist','ticket_service_checklist_history','ticket_time_list','ticket_timer'];
			break;
		case 'agenda_meeting':
			$tables = ['agenda_meeting','agenda_meeting_timer','agenda_meeting_upload'];
			break;
		case 'budget':
			$tables = ['budget','budget_category','budget_comment','budget_expense'];
			break;
		case 'booking':
			$tables = ['booking','calendar_notes','waitlist','contacts_shifts','teams','teams_staff'];
			break;
		case 'certificate':
			$tables = ['certificate','certificate_uploads'];
			break;
		case 'checklist':
			$tables = ['checklist','checklist_actions','checklist_document','checklist_name','checklist_name_document','checklist_name_time','checklist_report','checklist_subtab'];
			break;
		case 'rate_card':
			$tables = ['rate_card','company_rate_card','rate_card_breakdown','rate_card_estimate_scope_lines','rate_card_estimate_scopes','rate_card_holiday_pay'];
			break;
		case 'contracts':
			$tables = ['contracts contracts_completed contracts_staff contracts_upload'];
			break;
		case 'communication':
			$tables = ['email_communication','email_communication_timer','email_communicationid_upload','email_status','phone_communication'];
			break;
		case 'equipment':
			$tables = ['equipment','equipment_assignment','equipment_assignment_staff','equipment_expenses','equipment_history','equipment_inspections','equipment_inventory','equipment_purchase_order_items','equipment_rate_table','equipment_service_record','equipment_service_request','equipment_wo_checklist','equipment_wo_checklist_uploads','equipment_work_orders'];
			break;
		case 'estimate':
			$tables = ['estimate','estimate_actions','estimate_company_rate_card','estimate_content_page','estimate_detail','estimate_document','estimate_misc','estimate_notes','estimate_pdf_setting','estimate_scope','estimate_tab','estimate_template_headings','estimate_template_lines','estimate_templates','estimated_gantt_chart'];
			break;
		case 'expense':
			$tables = ['expense','expense_categories','expense_filters','expense_policy'];
			break;
		case 'field_jobs':
			$tables = ['field_jobs','field_foreman_sheet','field_invoice','field_payroll','field_po','field_sites','field_work_ticket','follow_up_calls','followup_deactivated_contacts','followup_notifications'];
			break;
		case 'fund_development_funder':
			$tables = ['fund_development_funder fund_development_funding'];
			break;
		case 'hr':
			$tables = ['hr','hr_2016_alberta_personal','hr_2016_alberta_personal1','hr_absence_report','hr_attendance','hr_background_check_authorization','hr_confidential_information','hr_contract_welder_inspection_checklist','hr_contractor_orientation','hr_contractor_pay_agreement','hr_copy_of_drivers_licence_safety_tickets','hr_direct_deposit_information','hr_disclosure_of_outside_clients','hr_driver_abstract_statement_of_intent','hr_driver_consent_form','hr_eligibility_for_general_holidays_general_holida...','hr_employee_accident_report_form','hr_employee_coaching_form','hr_employee_driver_information_form','hr_employee_expense_reimbursement','hr_employee_holiday_request_form','hr_employee_information_form','hr_employee_nondisclosure_agreement','hr_employee_personal_and_emergency_information','hr_employee_right_to_refuse_unsafe_work','hr_employee_self_evaluation','hr_employee_shop_yard_office_orientation','hr_employee_substance_abuse_policy','hr_employment_agreement','hr_employment_verification_letter','hr_exit_interview','hr_hr_complaint','hr_independent_contractor_agreement','hr_letter_of_offer','hr_maternity_leave_parental_leave','hr_personal_protective_equipment_policy','hr_police_information_check','hr_policy_and_procedure_notice_of_understanding_an...','hr_ppe_requirements','hr_staff','hr_time_off_request','hr_trucking_information','hr_upload','hr_verbal_training_in_emergency_response_plan','hr_work_hours_policy'];
			break;
		case 'manuals':
			$tables = ['manuals','manuals_staff','manuals_upload'];
			break;
		case 'social_story':
			$tables = ['social_story_activities','social_story_communication','social_story_protocols','social_story_routines'];
			break;
		case 'infogathering':
			$tables = ['infogathering','info_blog','info_branding_questionnaire','info_business_case_format','info_client_business_introduction','info_client_reviews','info_gap_analysis','info_lesson_plan','info_marketing_information','info_marketing_plan_information_gathering','info_marketing_strategies_review','info_product_service_outline','info_social_media_info_gathering','info_social_media_start_up_questionnaire','info_swot','info_website_information_gathering_form','infogathering_avs_near_miss','infogathering_pdf','infogathering_pdf_setting','infogathering_upload'];
			break;
		case 'time_cards':
			$tables = ['time_cards','time_cards_signature','time_tracking','time_tracking_labour'];
			break;
		case 'tasklist':
			$tables = ['task_board','task_additional_milestones','task_board_document','task_comments','task_dashboard','task_document','task_types','taskboard_path_custom_milestones','taskboard_seen','tasklist','tasklist_time'];
			break;
		case 'sales_order':
			$tables = ['sales_order','sales_order_history','sales_order_notes','sales_order_pdf','sales_order_product','sales_order_product_details','sales_order_product_details_temp','sales_order_product_temp','sales_order_temp','sales_order_template','sales_order_template_product','sales_order_upload','sales_order_upload_temp','sales_path','sales_path_custom_milestones'];
			break;
		case 'sales':
			$tables = ['sales','sales_document','sales_notes'];
			break;
		case 'inventory':
			$tables = ['inventory','inventory_change_log','inventory_images','inventory_pdf_setting','inventory_setting','inventory_templates','inventory_templates_headings','bill_of_material_log'];
			break;
		case 'invoice':
			$tables = ['invoice','invoice_compensation','invoice_insurer','invoice_lines','invoice_nonpatient','invoice_patient','invoice_payment','invoice_refund','invoice_unpaid_report','pos_giftcards'];
			break;
		case 'marketing_material':
			$tables = ['marketing_material','marketing_material_uploads'];
			break;
		case 'marsheet':
			$tables = ['marsheet','marsheet_medication','marsheet_row'];
			break;
		case 'medication':
			$tables = ['medication','medication_history','medication_uploads'];
			break;
		case 'orientation':
			$tables = ['orientation','orientation_copy_of_driver_lic_safety_tickets','orientation_direct_deposit_info','orientation_emp_driver_info_form','orientation_emp_info_medical_form','orientation_pay_agreement','orientation_staff'];
			break;
		case 'pick_lists':
			$tables = ['pick_lists','pick_list_items'];
			break;
		case 'purchase_orders':
			$tables = ['purchase_orders','purchase_orders_product'];
			break;
		case 'safety':
			$tables = ['safety','safety_attendance','safety_avs_hazard_identification','safety_avs_near_miss','safety_confined_space_entry_log','safety_confined_space_entry_permit','safety_confined_space_entry_pre_entry_checklist','safety_daily_equipment_inspection_checklist','safety_dangerous_goods_shipping_document','safety_emergency_response_transportation_plan','safety_employee_equipment_training_record','safety_employee_misconduct_form','safety_equipment_inspection_checklist','safety_fall_protection_plan','safety_field_level_risk_assessment','safety_follow_up_incident_report','safety_full_body_harness_inspection_checklist_log','safety_general_office_safety_inspection','safety_general_site_safety_inspection','safety_hazard_id_report','safety_incident_investigation_report','safety_journey_management_trip_tracking','safety_lanyards_inspection_checklist_log','safety_monthly_health_and_safety_summary','safety_monthly_office_safety_inspection','safety_monthly_site_office_safety_inspection','safety_monthly_site_safety_inspection','safety_on_the_job_training_record','safety_pre_job_hazard_assessment','safety_safe_work_permit','safety_safety_meeting_minutes','safety_site_inspection_hazard_assessment','safety_site_specificpre_job','safety_spill_incident_report','safety_staff','safety_tailgate_safety_meeting','safety_toolbox_safety_meeting','safety_trailer_inspection_checklist','safety_upload','safety_vehicle_damage_report','safety_vehicle_inspection_checklist','safety_weekly_planned_inspection_checklist','safety_weekly_safety_meeting'];
			break;
		case 'contacts':
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contact_document` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contact_order_numbers` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contact_package_sold` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_cost` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_dates` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_description` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_history` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_last_active` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_medical` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_patient_be` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_patient_be` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_patient_th` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_security` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_services` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_subtab` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_tile_sort` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_upload` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`user_settings` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts` SELECT * FROM `".DATABASE_NAME2."`.`contacts` WHERE `category` != 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contact_document` SELECT * FROM `".DATABASE_NAME2."`.`contact_document` WHERE `category` != 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contact_order_numbers` SELECT * FROM `".DATABASE_NAME2."`.`contact_order_numbers` WHERE `category` != 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contact_package_sold` SELECT * FROM `".DATABASE_NAME2."`.`contact_package_sold` WHERE `category` != 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_cost` SELECT * FROM `".DATABASE_NAME2."`.`contacts_cost` WHERE `category` != 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_dates` SELECT * FROM `".DATABASE_NAME2."`.`contacts_dates` WHERE `category` != 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_description` SELECT * FROM `".DATABASE_NAME2."`.`contacts_description` WHERE `category` != 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_history` SELECT * FROM `".DATABASE_NAME2."`.`contacts_history` WHERE `category` != 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_last_active` SELECT * FROM `".DATABASE_NAME2."`.`contacts_last_active` WHERE `category` != 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_medical` SELECT * FROM `".DATABASE_NAME2."`.`contacts_medical` WHERE `category` != 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_patient_be` SELECT * FROM `".DATABASE_NAME2."`.`contacts_patient_be` WHERE `category` != 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_patient_th` SELECT * FROM `".DATABASE_NAME2."`.`contacts_patient_th` WHERE `category` != 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_security` SELECT * FROM `".DATABASE_NAME2."`.`contacts_security` WHERE `category` != 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_services` SELECT * FROM `".DATABASE_NAME2."`.`contacts_services` WHERE `category` != 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_subtab` SELECT * FROM `".DATABASE_NAME2."`.`contacts_subtab` WHERE `category` != 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_tile_sort` SELECT * FROM `".DATABASE_NAME2."`.`contacts_tile_sort` WHERE `category` != 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_upload` SELECT * FROM `".DATABASE_NAME2."`.`contacts_upload` WHERE `category` != 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`user_settings` SELECT * FROM `".DATABASE_NAME2."`.`user_settings` WHERE `category` != 'Staff'");
			break;
		case 'staff':
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contact_document` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` = 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contact_order_numbers` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` = 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contact_package_sold` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` = 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_cost` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` = 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_dates` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` = 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_description` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` = 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_history` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` = 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_last_active` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` = 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_medical` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` = 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_patient_be` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` = 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_patient_th` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` = 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_security` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` = 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_services` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` = 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_subtab` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` = 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_tile_sort` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` = 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts_upload` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` = 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`user_settings` WHERE `contactid` IN (SELECT `contactid` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` = 'Staff')");
			$db_all->query("DELETE FROM `".DATABASE_NAME."`.`contacts` WHERE `category` = 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts` SELECT * FROM `".DATABASE_NAME2."`.`contacts` WHERE `category` = 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contact_document` SELECT * FROM `".DATABASE_NAME2."`.`contact_document` WHERE `category` = 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contact_order_numbers` SELECT * FROM `".DATABASE_NAME2."`.`contact_order_numbers` WHERE `category` = 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contact_package_sold` SELECT * FROM `".DATABASE_NAME2."`.`contact_package_sold` WHERE `category` = 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_cost` SELECT * FROM `".DATABASE_NAME2."`.`contacts_cost` WHERE `category` = 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_dates` SELECT * FROM `".DATABASE_NAME2."`.`contacts_dates` WHERE `category` = 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_description` SELECT * FROM `".DATABASE_NAME2."`.`contacts_description` WHERE `category` = 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_history` SELECT * FROM `".DATABASE_NAME2."`.`contacts_history` WHERE `category` = 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_last_active` SELECT * FROM `".DATABASE_NAME2."`.`contacts_last_active` WHERE `category` = 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_medical` SELECT * FROM `".DATABASE_NAME2."`.`contacts_medical` WHERE `category` = 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_patient_be` SELECT * FROM `".DATABASE_NAME2."`.`contacts_patient_be` WHERE `category` = 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_patient_th` SELECT * FROM `".DATABASE_NAME2."`.`contacts_patient_th` WHERE `category` = 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_security` SELECT * FROM `".DATABASE_NAME2."`.`contacts_security` WHERE `category` = 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_services` SELECT * FROM `".DATABASE_NAME2."`.`contacts_services` WHERE `category` = 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_subtab` SELECT * FROM `".DATABASE_NAME2."`.`contacts_subtab` WHERE `category` = 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_tile_sort` SELECT * FROM `".DATABASE_NAME2."`.`contacts_tile_sort` WHERE `category` = 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`contacts_upload` SELECT * FROM `".DATABASE_NAME2."`.`contacts_upload` WHERE `category` = 'Staff'");
			$db_all->query("INSERT INTO `".DATABASE_NAME."`.`user_settings` SELECT * FROM `".DATABASE_NAME2."`.`user_settings` WHERE `category` = 'Staff'");
			$tables = ['positions'];
			break;
		case 'admin_tile_config':
		    $tables = ['admin_tile_config'];
		    break;
		case 'dashboard_permission_config':
		    $tables = ['dashboard_permission_config'];
		    break;
		case 'exercise_config':
		    $tables = ['exercise_config'];
		    break;
		case 'field_config':
		    $tables = ['field_config'];
		    break;
		case 'field_config_agendas_meetings':
		    $tables = ['field_config_agendas_meetings'];
		    break;
		case 'field_config_asset':
		    $tables = ['field_config_asset'];
		    break;
		case 'field_config_bid':
		    $tables = ['field_config_bid'];
		    break;
		case 'field_config_budget':
		    $tables = ['field_config_budget'];
		    break;
		case 'field_config_calendar_booking':
		    $tables = ['field_config_calendar_booking'];
		    break;
		case 'field_config_calendar_security':
		    $tables = ['field_config_calendar_security'];
		    break;
		case 'field_config_calllog':
		    $tables = ['field_config_calllog'];
		    break;
		case 'field_config_charts_pdf_times':
		    $tables = ['field_config_charts_pdf_times'];
		    break;
		case 'field_config_checklist':
		    $tables = ['field_config_checklist'];
		    break;
		case 'field_config_client_project':
		    $tables = ['field_config_client_project'];
		    break;
		case 'field_config_communication':
		    $tables = ['field_config_communication'];
		    break;
		case 'field_config_contact':
		    $tables = ['field_config_contact'];
		    break;
		case 'field_config_contacts':
		    $tables = ['field_config_contacts'];
		    break;
		case 'field_config_contacts__20170517_160755':
		    $tables = ['field_config_contacts__20170517_160755'];
		    break;
		case 'field_config_contacts_security':
		    $tables = ['field_config_contacts_security'];
		    break;
		case 'field_config_contacts_shifts':
		    $tables = ['field_config_contacts_shifts'];
		    break;
		case 'field_config_contacts_shifts_pdf':
		    $tables = ['field_config_contacts_shifts_pdf'];
		    break;
		case 'field_config_contracts':
		    $tables = ['field_config_contracts'];
		    break;
		case 'field_config_cost_estimate':
		    $tables = ['field_config_cost_estimate'];
		    break;
		case 'field_config_custom_charts':
		    $tables = ['field_config_custom_charts'];
		    break;
		case 'field_config_custom_charts_lines':
		    $tables = ['field_config_custom_charts_lines'];
		    break;
		case 'field_config_custom_charts_settings':
		    $tables = ['field_config_custom_charts_settings'];
		    break;
		case 'field_config_custom_documents':
		    $tables = ['field_config_custom_documents'];
		    break;
		case 'field_config_email_alerts':
		    $tables = ['field_config_email_alerts'];
		    break;
		case 'field_config_equip_assign':
		    $tables = ['field_config_equip_assign'];
		    break;
		case 'field_config_equipment':
		    $tables = ['field_config_equipment'];
		    break;
		case 'field_config_equipment_inspection':
		    $tables = ['field_config_equipment_inspection'];
		    break;
		case 'field_config_estimate':
		    $tables = ['field_config_estimate'];
		    break;
		case 'field_config_expense':
		    $tables = ['field_config_expense'];
		    break;
		case 'field_config_field_jobs':
		    $tables = ['field_config_field_jobs'];
		    break;
		case 'field_config_holidays':
		    $tables = ['field_config_holidays'];
		    break;
		case 'field_config_hr':
		    $tables = ['field_config_hr'];
		    break;
		case 'field_config_hr_manuals':
		    $tables = ['field_config_hr_manuals'];
		    break;
		case 'field_config_incident_report':
		    $tables = ['field_config_incident_report'];
		    break;
		case 'field_config_infogathering':
		    $tables = ['field_config_infogathering'];
		    break;
		case 'field_config_inventory':
		    $tables = ['field_config_inventory'];
		    break;
		case 'field_config_jobs':
		    $tables = ['field_config_jobs'];
		    break;
		case 'field_config_manuals':
		    $tables = ['field_config_manuals'];
		    break;
		case 'field_config_password':
		    $tables = ['field_config_password'];
		    break;
		case 'field_config_patientform':
		    $tables = ['field_config_patientform'];
		    break;
		case 'field_config_performance_reviews':
		    $tables = ['field_config_performance_reviews'];
		    break;
		case 'field_config_project':
		    $tables = ['field_config_project'];
		    break;
		case 'field_config_project_admin':
		    $tables = ['field_config_project_admin'];
		    break;
		case 'field_config_project_custom_details':
		    $tables = ['field_config_project_custom_details'];
		    break;
		case 'field_config_project_form':
		    $tables = ['field_config_project_form'];
		    break;
		case 'field_config_project_manage':
		    $tables = ['field_config_project_manage'];
		    break;
		case 'field_config_ratecard':
		    $tables = ['field_config_ratecard'];
		    break;
		case 'field_config_safety':
		    $tables = ['field_config_safety'];
		    break;
		case 'field_config_security_contact_categories':
		    $tables = ['field_config_security_contact_categories'];
		    break;
		case 'field_config_security_level_theme':
		    $tables = ['field_config_security_level_theme'];
		    break;
		case 'field_config_services':
		    $tables = ['field_config_services'];
		    break;
		case 'field_config_so':
		    $tables = ['field_config_so'];
		    break;
		case 'field_config_so_contacts':
		    $tables = ['field_config_so_contacts'];
		    break;
		case 'field_config_so_pdf':
		    $tables = ['field_config_so_pdf'];
		    break;
		case 'field_config_so_security':
		    $tables = ['field_config_so_security'];
		    break;
		case 'field_config_staff_security':
		    $tables = ['field_config_staff_security'];
		    break;
		case 'field_config_supervisor':
		    $tables = ['field_config_supervisor'];
		    break;
		case 'field_config_teams':
		    $tables = ['field_config_teams'];
		    break;
		case 'field_config_ticket_accordion_names':
		    $tables = ['field_config_ticket_accordion_names'];
		    break;
		case 'field_config_ticket_delivery_color':
		    $tables = ['field_config_ticket_delivery_color'];
		    break;
		case 'field_config_ticket_fields':
		    $tables = ['field_config_ticket_fields'];
		    break;
		case 'field_config_ticket_fields_action':
		    $tables = ['field_config_ticket_fields_action'];
		    break;
		case 'field_config_ticket_headings':
		    $tables = ['field_config_ticket_headings'];
		    break;
		case 'field_config_ticket_log':
		    $tables = ['field_config_ticket_log'];
		    break;
		case 'field_config_ticket_security':
		    $tables = ['field_config_ticket_security'];
		    break;
		case 'field_config_ticket_status_color':
		    $tables = ['field_config_ticket_status_color'];
		    break;
		case 'field_config_treatment_presets':
		    $tables = ['field_config_treatment_presets'];
		    break;
		case 'field_config_user_forms':
		    $tables = ['field_config_user_forms'];
		    break;
		case 'field_config_vendors':
		    $tables = ['field_config_vendors'];
		    break;
		case 'field_config_vpl':
		    $tables = ['field_config_vpl'];
		    break;
		case 'general_configuration':
		    $tables = ['general_configuration'];
		    break;
		case 'subtab_config':
		    $tables = ['subtab_config'];
		    break;
		case 'tile_config':
		    $tables = ['tile_config'];
		    break;
		default:
			$tables = [filter_var($_GET['table'],FILTER_SANITIZE_STRING)];
			break;
	}
  if (!file_exists('database_table_backup')) {
    mkdir('database_table_backup', 0777, true);
  }

  $current_time = date("Y-m-d-H-i-s");
  if (!file_exists('database_table_backup/'.$current_time)) {
    mkdir('database_table_backup/'.$current_time, 0777, true);
  }

	foreach(array_filter($tables) as $table_name) {

    // Backup code //
    $backupFile = __DIR__.'/database_table_backup/'.$current_time.'/'.$table_name.'.sql';
    $backup_query = "SELECT * INTO OUTFILE '$backupFile' FROM $table_name";
    $backup_result = mysqli_query($dbc, $backup_query);
    // Back up code end //

		$db_all->query("TRUNCATE `".DATABASE_NAME."`.`$table_name`");
		$db_all->query("INSERT INTO `".DATABASE_NAME."`.`$table_name` SELECT * FROM `".DATABASE_NAME2."`.`$table_name`");
	}

  $before_change = '';
  $all_tables = implode(",", $tables);
  $history = "Tables been transfered from Live to Demo -> $all_tables";
  add_update_history($dbc, 'db_backup_history', $history, '', $before_change);
	echo 'Sync Complete!';
}
