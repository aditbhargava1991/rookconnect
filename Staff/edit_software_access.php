<?php if(!isset($_GET['mobile_view'])) {
	include_once ('../include.php');
} else {
	include_once ('../database_connection.php');
	include_once ('../global.php');
	include_once ('../function.php');
	include_once ('../output_functions.php');
	include_once ('../email.php');
	include_once ('../user_font_settings.php');
}
checkAuthorised('staff');
$rookconnect = get_software_name();
error_reporting(0); ?>
</head>
<script type="text/javascript" src="staff.js"></script>
<body>
<?php if(!isset($_GET['mobile_view'])) { include_once ('../navigation.php'); }

if (isset($_POST['contactid'])) {
	if($_POST['contactid'] != '') {
		$contacts_prior = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` LEFT JOIN `contacts_security` ON `contacts`.`contactid`=`contacts_security`.`contactid` WHERE `contacts`.`contactid` = '{$_POST['contactid']}'"));
	}
	$user_name = filter_var($_POST['user_name'],FILTER_SANITIZE_STRING);
	$password = encryptIt($_POST['password']);
    $role = filter_var(','.trim(implode(',',$_POST['role']),',').',',FILTER_SANITIZE_STRING);
	$region_access = filter_var(implode('#*#', $_POST['region_access']),FILTER_SANITIZE_STRING);
	$location_access = filter_var(implode('#*#', $_POST['location_access']),FILTER_SANITIZE_STRING);

    if(empty($_POST['contactid'])) {
        $query_insert_inventory = "INSERT INTO `contacts` (`category`, `user_name`, `password`, `password_date`, `role`, `show_hide_user`)
			VALUES 		('Staff', '$user_name', '$password', CURRENT_TIMESTAMP, '$role', '$show_hide_user')";

        $result_insert_inventory = mysqli_query($dbc, $query_insert_inventory);
        $contactid = mysqli_insert_id($dbc);
		$_GET['contactid'] = $contactid;
        $url = 'Added';
    } else {
        $contactid = $_POST['contactid'];
        $query_update_inventory = "UPDATE `contacts` SET `user_name` = '$user_name', `password` = '$password', `password_date` = CURRENT_TIMESTAMP, `password_update` = 0, `role` = '$role' WHERE `contactid` = '$contactid'";
		// if(strpos($_SERVER['HTTP_HOST'],'highland') !== FALSE) {
			// send_email('', 'jonathanhurdman@freshfocusmedia.com', '', '', 'Security Level Change: Staff', get_contact($dbc, $_SESSION['contactid'])." changed the security level for $contactid to '$role'.<br />".WEBSITE_URL."<br />$query_update_inventory", '');
		// }
        $result_update_inventory	= mysqli_query($dbc, $query_update_inventory);
		$_GET['contactid'] = $contactid;
		if(!empty($_POST['email_software_access'])) {
			$email_list = explode(',',$_POST['email_software_access']);
			$staff_name = get_staff($dbc, $contactid);
			$body = str_replace(['[STAFF_NAME]','[USERNAME]','[PASSWORD]'], [$staff_name, $user_name, $_POST['password']], $_POST['body']);
			foreach($email_list as $email) {
				try {
					send_email([$_POST['sender']=>$_POST['sender_name']], trim($email), '', '', $_POST['subject'], $body);
				} catch(Exception $e) {
					echo "<script> alert('Unable to send e-mail to $email, please try again later.');</script>";
				}
			}
		}

        $url = 'Updated';
    }

	// Update the contacts_security table
	mysqli_query($dbc, "INSERT INTO `contacts_security` (`contactid`) SELECT '$contactid' FROM (SELECT COUNT(*) rows FROM `contacts_security` WHERE `contactid`='$contactid') num WHERE num.rows = 0");
	mysqli_query($dbc, "UPDATE `contacts_security` SET `region_access`='$region_access' WHERE `contactid`='$contactid'");
	mysqli_query($dbc, "UPDATE `contacts_security` SET `location_access`='$location_access' WHERE `contactid`='$contactid'");

	// Record the history of the change
	$contacts_after = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` LEFT JOIN `contacts_security` ON `contacts`.`contactid`=`contacts_security`.`contactid` WHERE `contacts`.`contactid` = '$contactid'"));
	$user = $_SESSION['first_name'].' '.$_SESSION['last_name'];
	$change_log = '';
	if($_POST['contactid'] != '') {
		foreach($contacts_after as $name => $value) {
			if(str_replace(['0000-00-00','0'], '', $contacts_prior[$name]) != str_replace(['0000-00-00','0'], '', $value)) {
				if($name == 'password') {
					$value = '************';
				}
				$change_log .= "$name set from '{$contacts_prior[$name]}' to '$value'.\n";
			}
		}
	}
	else {
		foreach($_POST as $name => $value) {
			if(trim($value) != '') {
				$change_log .= "$name set to '$value'.\n";
			}
		}
	}
	$change_log = filter_var($change_log,FILTER_SANITIZE_STRING);
	if(trim($change_log) != '') {
		$query = "INSERT INTO contacts_history (`updated_by`, `description`, `contactid`) VALUES ('$user', '$change_log', '$contactid')";
		mysqli_query($dbc, $query);
	}
	?>
	<?php if(!empty($_POST['subtab'])) {
		$action_page = 'staff_edit.php?contactid='.$_GET['contactid'];
		if($_POST['subtab'] == 'software_access') {
			$action_page = 'edit_software_access.php?contactid='.$_GET['contactid'];
		} else if($_POST['subtab'] == 'certificates') {
			$action_page = 'certificate.php?contactid='.$_GET['contactid'];
		} else if($_POST['subtab'] == 'history') {
			$action_page = 'staff_history.php?contactid='.$_GET['contactid'];
		} else if($_POST['subtab'] == 'reminders') {
			$action_page = 'staff_reminder.php?contactid='.$_GET['contactid'];
		}?>
		<form action="<?php echo $action_page; ?>" method="post" id="change_page">
			<input type="hidden" name="subtab" value="<?php echo $_POST['subtab']; ?>">
		</form>
		<script type="text/javascript"> document.getElementById('change_page').submit(); </script>
	<?php }
}

$role = '';
$user_name = '';
$password = '';
$show_hide_user = 0;
$subtab = 'software_access';
if (!empty($_POST['subtab'])) {
    $subtab = $_POST['subtab'];
}

if(!empty($_GET['contactid']))	{
	$contactid = $_GET['contactid'];
	$get_contact =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	contacts WHERE	contactid='$contactid'"));

	$user_name = $get_contact['user_name'];
	$password = decryptIt($get_contact['password']);
	$role = $get_contact['role'];
	$show_hide_user = $get_contact['show_hide_user'];
}
if(strpos(','.ROLE.',',',super,') === false) {
	foreach($role as $role_current)
	$security_sql = "SELECT COUNT(*) numrows FROM `subtab_config` WHERE `tile`='staff' AND `subtab`='software_access_".$role_current."' AND ',".trim(ROLE,',').",' LIKE CONCAT('%,',`security_level`,',%') ORDER BY IF(`status` like '%turn_off%', 0, 1)";
	$security_result = mysqli_fetch_array(mysqli_query($dbc, $security_sql));
	if($security_result['numrows'] == 0) {
		mysqli_query($dbc, "INSERT INTO `subtab_config` (`tile`, `subtab`, `security_level`, `status`) VALUES ('staff', 'software_access_".$role_current."', '".trim(ROLE,',')."', '*turn_off*')");
	}
}
foreach($role as $role_current) {
	if(check_subtab_persmission($dbc, 'staff', ROLE, 'software_access_'.$role_current) == false || strpos(WEBSITE_URL, '.clinicace.') !== FALSE) {
		exit('<h2>Access denied, please contact your system administrator</h2>');
	}
}
$field_tabs = ','.get_config($dbc, 'staff_field_subtabs').',';
$contact_security = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts_security` WHERE `contactid`='$contactid'"));
?>
<div class="container">
	<div class="row">
		<!-- <div id="no-more-tables" class="main-screen contacts-list"> -->
		<div class="main-screen contacts-list">
            <!-- Tile Header -->
            <div class="tile-header">
                <div class="col-xs-12 col-sm-4">
                    <h1>
                        <span class="pull-left" style="margin-top: -5px;"><a href="staff.php?tab=active" class="default-color">Staff</a></span>
                        <span class="clearfix"></span>
                    </h1>
                </div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->

            <div class="tile-container">

				<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

	                <!-- Sidebar -->
	                <div class="collapsible tile-sidebar set-section-height">
	                	<?php include('tile_sidebar.php'); ?>
	                </div><!-- .tile-sidebar -->

					<!-- Main Screen -->
	                <div class="fill-to-gap tile-content set-section-height" style="padding: 0;">
						<div class="main-screen-details">

							<?php $value_config = ',Role,User Name,Password,Show/Hide User,';
							$edit_config = $value_config;
							if($_SESSION['contactid'] == $contactid) {
								$edit_config = ',User Name,Password,Show/Hide User,';
							}
							?>
							<h4>Software Access</h4>

							<?php include ('../Contacts/add_contacts_basic_info.php');
							$contact_regions = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0])));
							if(count($contact_regions) > 0) {
								echo '<div class="form-group"><label class="col-sm-4 control-label">Regions Allowed:</label><div class="col-sm-8">';
								$allowed_regions = array_filter(explode('#*#', $contact_security['region_access']));
								if(count($allowed_regions) == 0) {
									$allowed_regions = $contact_regions;
								}
								foreach($contact_regions as $region_name) { ?>
									<label class="form-checkbox"><input type="checkbox" name="region_access[]" <?= in_array($region_name, $allowed_regions) ? 'checked' : '' ?> value="<?= $region_name ?>"><?= $region_name ?></label>
								<?php }
								echo '</div></div>';
							}
							$contact_locations = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `con_locations` SEPARATOR ',') FROM `field_config_contacts`"))[0])));
							if(count($contact_locations) > 0) {
								echo '<div class="form-group"><label class="col-sm-4 control-label">Locations Allowed:</label><div class="col-sm-8">';
								$allowed_locations = array_filter(explode('#*#', $contact_security['location_access']));
								if(count($allowed_locations) == 0) {
									$allowed_locations = $contact_locations;
								}
								foreach($contact_locations as $location_name) {
									$location_arr = explode('*#*', $location_name); ?>
									<label class="form-checkbox"><input type="checkbox" name="location_access[]" <?= in_array($location_name, $allowed_locations) ? 'checked' : '' ?> value="<?= $location_name ?>"><?= $location_arr[0] ?></label>
								<?php }
								echo '</div></div>';
							}

							?>
							<h4>Email Software Access</h4>

							<div class="form-group">
								<label for="company_name" class="col-sm-4 control-label">Recipient Email Addresses:<br />
									<small>The email will send once you enter the username and password in the email body and click submit. To enter multiple email addresses, separate each email address with a comma.</small></label>
								<div class="col-sm-8">
									<input name="email_software_access" value="" type="text" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Email Sender's Name:</label>
								<div class="col-sm-8">
									<input type="text" name="sender_name" class="form-control" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Email Sender's Address:</label>
								<div class="col-sm-8">
									<input type="text" name="sender" class="form-control" value="<?= get_email($dbc, $_SESSION['contactid']) ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Email Subject:</label>
								<div class="col-sm-8">
									<input type="text" name="subject" class="form-control" value="New User Account Created">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Email Body:</label>
								<div class="col-sm-8">
									<textarea name="body" class="form-control">Hello [STAFF_NAME],<br /><br />
									The administrator for <a href="<?php echo WEBSITE_URL; ?>"><?php echo WEBSITE_URL; ?></a> has created a user account for you. You will need the following information to log in:<br /><br />
									Username: [USERNAME]<br />
									Password: [PASSWORD]<br /><br />
									Thank you.</textarea>
								</div>
							</div>
							<button type='submit' name='contactid' value='<?php echo $contactid; ?>' class="btn brand-btn pull-right">Submit</button>
							<a href="staff.php" class="btn brand-btn pull-right">Back</a>
						</div>
					</div>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php include_once ('../footer.php'); ?>
