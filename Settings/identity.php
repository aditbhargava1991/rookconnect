<?php
/*
Software Identity
*/
include_once('../include.php');

if(!defined('EMAIL_SERVER')) {
	$temp_config_email_server = get_config($_SERVER['DBC'], 'main_email_server');
	DEFINE('EMAIL_SERVER', ($temp_config_email_server == '' ? 'smtp.gmail.com' : $temp_config_email_server));
	$temp_config_email_port = get_config($_SERVER['DBC'], 'main_email_port');
	DEFINE('EMAIL_PORT', ($temp_config_email_port == '' ? 465 : $temp_config_email_port));
	$temp_config_email_mode = get_config($_SERVER['DBC'], 'main_email_mode');
	DEFINE('EMAIL_MODE', ($temp_config_email_mode == '' ? 'ssl' : $temp_config_email_mode));
	$temp_config_email_user = get_config($_SERVER['DBC'], 'main_email_user');
	DEFINE('EMAIL_USER', ($temp_config_email_user == '' ? 'info@rookconnect.com' : $temp_config_email_user));
	$temp_config_email_pass = get_config($_SERVER['DBC'], 'main_email_pass');
	DEFINE('EMAIL_PASS', ($temp_config_email_pass == '' ? decryptIt('OkUMtNluu/hXFA8EQrFtk2WalhiO8v1RDccUUWaGoeI=') : decryptIt($temp_config_email_pass)));
	$temp_config_email_address = get_config($_SERVER['DBC'], 'main_email_address');
	DEFINE('EMAIL_ADDRESS', ($temp_config_email_address == '' ? 'info@rookconnect.com' : $temp_config_email_address));
	$temp_config_email_name = get_config($_SERVER['DBC'], 'main_email_name');
	DEFINE('EMAIL_NAME', ($temp_config_email_name == '' ? 'ROOK Connect' : $temp_config_email_name));
}
if (isset($_POST['add_general'])) {
	// Company Name
    $company_name = filter_var($_POST['company_name'],FILTER_SANITIZE_STRING);
    if(mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='company_name'"))['configid'] > 0) {
        $result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$company_name' WHERE name='company_name'");
    } else {
        $result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('company_name', '$company_name')");
    }
    // Company Phone Number
    $company_phone_number = filter_var($_POST['company_phone_number'],FILTER_SANITIZE_STRING);
    if(mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='company_phone_number'"))['configid'] > 0) {
        $result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$company_phone_number' WHERE name='company_phone_number'");
    } else {
        $result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('company_phone_number', '$company_phone_number')");
    }
    // Company Address
    $company_address = filter_var($_POST['company_address'],FILTER_SANITIZE_STRING);
    if(mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='company_address'"))['configid'] > 0) {
        $result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$company_address' WHERE name='company_address'");
    } else {
        $result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('company_address', '$company_address')");
    }

	// Email Settings
    $value = filter_var($_POST['main_email_server'],FILTER_SANITIZE_STRING);
    if(mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='main_email_server'"))['configid'] > 0) {
        $result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$value' WHERE name='main_email_server'");
    } else {
        $result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('main_email_server', '$value')");
    }
    $value = filter_var($_POST['main_email_port'],FILTER_SANITIZE_STRING);
    if(mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='main_email_port'"))['configid'] > 0) {
        $result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$value' WHERE name='main_email_port'");
    } else {
        $result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('main_email_port', '$value')");
    }
    $value = filter_var($_POST['main_email_mode'],FILTER_SANITIZE_STRING);
    if(mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='main_email_mode'"))['configid'] > 0) {
        $result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$value' WHERE name='main_email_mode'");
    } else {
        $result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('main_email_mode', '$value')");
    }
    $value = filter_var($_POST['main_email_user'],FILTER_SANITIZE_STRING);
    if(mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='main_email_user'"))['configid'] > 0) {
        $result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$value' WHERE name='main_email_user'");
    } else {
        $result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('main_email_user', '$value')");
    }
    $value = filter_var(encryptIt($_POST['main_email_pass']),FILTER_SANITIZE_STRING);
    if(mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='main_email_pass'"))['configid'] > 0) {
        $result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$value' WHERE name='main_email_pass'");
    } else {
        $result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('main_email_pass', '$value')");
    }
    $value = filter_var($_POST['main_email_address'],FILTER_SANITIZE_STRING);
    if(mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='main_email_address'"))['configid'] > 0) {
        $result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$value' WHERE name='main_email_address'");
    } else {
        $result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('main_email_address', '$value')");
    }
    $value = filter_var($_POST['main_email_name'],FILTER_SANITIZE_STRING);
    if(mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='main_email_name'"))['configid'] > 0) {
        $result_update_employee = mysqli_query($dbc, "UPDATE `general_configuration` SET value = '$value' WHERE name='main_email_name'");
    } else {
        $result_insert_config = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('main_email_name', '$value')");
    }

	if($_POST['add_general'] == 'test_email') {
		try {
			send_email('', $_POST['test_email_address'], '', '', 'Test Email', 'This is a test email from the software at <a href="'.WEBSITE_URL.'">'.WEBSITE_URL.'</a>.<br />You have configured the software properly to send email.');
			echo "<script> alert('A test email has been sent to ".$_POST['test_email_address'].".'); </script>";
		} catch(Exception $e) {
			echo "<script> alert('Unable to send the test message: ".str_replace(["\r","\n"],'',$e->getMessage())."'); </script>";
		}
	}
	echo "<script> window.location.replace(''); </script>";
}

$notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='setting_software_identity'"));
$note = $notes['note'];
    
if ( !empty($note) ) { ?>
    <div class="notice double-gap-bottom popover-examples">
        <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
        <div class="col-sm-11">
            <span class="notice-name">NOTE:</span>
            <?= $note; ?>
        </div>
        <div class="clearfix"></div>
    </div><?php
} ?>

<div class="clearfix"></div>

<!--<h2>General Software Settings</h2>-->
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<h3>Company Information</h3>
	<div class="form-group">
		<label for="fax_number"	class="col-sm-4	control-label">Your Company's Name:</label>
		<div class="col-sm-8">
		  <input name="company_name" placeholder="Fresh Focus Media Inc." type="text" value="<?php echo get_config($dbc, 'company_name'); ?>" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label for="fax_number"	class="col-sm-4	control-label">Company Phone Number:</label>
		<div class="col-sm-8">
		  <input name="company_phone_number" placeholder="Enter your Company Phone Number" type="text" value="<?php echo get_config($dbc, 'company_phone_number'); ?>" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label for="fax_number"	class="col-sm-4	control-label">Company Address:</label>
		<div class="col-sm-8">
		  <input name="company_address" placeholder="Enter your Company Address" type="text" value="<?php echo get_config($dbc, 'company_address'); ?>" class="form-control"/>
		</div>
	</div>
	<h3>Email Settings</h3>
	<div class="form-group">
		<label class="col-sm-4	control-label">Email User Account:</label>
		<div class="col-sm-8">
			<input name="main_email_user" placeholder="info@rookconnect.com" type="text" value="<?= EMAIL_USER ?>" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4	control-label">Email User Password:</label>
		<div class="col-sm-8">
			<input type="text" name="nosubmitusernamefield" value="" style="display:none;"><input type="password" name="nosubmitpasswordfield" value="" style="display:none;">
			<input name="main_email_pass" type="password" value="<?= EMAIL_PASS ?>" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4	control-label">Email Reply To Address:<br /><em>This is the address that the recipient will see on the email, and to which replies will be sent</em></label>
		<div class="col-sm-8">
			<input name="main_email_address" placeholder="info@rookconnect.com" type="text" value="<?= EMAIL_ADDRESS ?>" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4	control-label">Email Name:<br /><em>This is the name that the recipient will see on the email</em></label>
		<div class="col-sm-8">
			<input name="main_email_name" placeholder="ROOK Connect" type="text" value="<?= EMAIL_NAME ?>" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4	control-label">Email Server:</em></label>
		<div class="col-sm-8">
			<input name="main_email_server" placeholder="smtp.gmail.com" type="text" value="<?= EMAIL_SERVER ?>" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4	control-label">Email Server Port:</em></label>
		<div class="col-sm-8">
			<input name="main_email_port" placeholder="465" type="number" value="<?= EMAIL_PORT ?>" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4	control-label">Email Server Mode:</em></label>
		<div class="col-sm-8">
			<input name="main_email_mode" placeholder="ssl" type="text" value="<?= EMAIL_MODE ?>" class="form-control"/>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-12">
			<button	type="submit" name="add_general" value="add_general" class="btn config-btn btn-lg	pull-right">Submit</button>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4	control-label">Test Message Recipient:</em></label>
		<div class="col-sm-8">
			<input name="test_email_address" placeholder="info@rookconnect.com" type="text" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-12">
			<button	type="submit" name="add_general" value="test_email" class="btn config-btn btn-lg pull-right">Test Email Settings</button>
		</div>
	</div>
</form>