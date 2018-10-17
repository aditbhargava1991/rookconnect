<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('staff');

error_reporting(0);

if (isset($_POST['inv_field'])) {
	if($_POST['inv_field'] == 'inv_field') {
		$accordion = filter_var($_POST['accordion'],FILTER_SANITIZE_STRING);
		if($_POST['del_accr'] == 'TRUE') {
			$query = "DELETE FROM `field_config_contacts` WHERE `tab`='Staff' AND `accordion`='$accordion'";
			mysqli_query($dbc, $query);
			echo "<script>alert('$accordion Accordion Deleted');window.location.replace('field_config_staff.php?tab=staff');</script>";
			exit();
		}
		$subtab = filter_var($_POST['subtab'],FILTER_SANITIZE_STRING);
		$order = filter_var($_POST['order'],FILTER_SANITIZE_STRING);
		$fields = ','.implode(',',$_POST['contacts']).',';

		$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configcontactid) AS configcontactid FROM field_config_contacts WHERE tab='Staff' AND subtab='$subtab' AND accordion='$accordion'"));
		$query = "";
		if($get_field_config['configcontactid'] > 0) {
			$query = "UPDATE `field_config_contacts` SET `contacts` = '$fields', `order` = '$order' WHERE tab='Staff' AND subtab='$subtab' AND accordion='$accordion'";
		} else {
			$query = "INSERT INTO `field_config_contacts` (`tab`, `subtab`, `accordion`, `contacts`, `order`) VALUES ('Staff', '$subtab', '$accordion', '$fields', '$order')";
		}
		$result = mysqli_query($dbc, $query);

		//echo '<script type="text/javascript"> window.location.replace("field_config_staff.php?tab=Staff&subtab='.$subtab.'&accr='.$accordion.'"); </script>';
	}
} else if (isset($_POST['submit'])) {
	if($_POST['submit'] == 'inv_field') {
		$accordion = filter_var($_POST['accordion'],FILTER_SANITIZE_STRING);
		if($_POST['del_accr'] == 'TRUE') {
			$query = "DELETE FROM `field_config_contacts` WHERE `tab`='Staff' AND `accordion`='$accordion'";
			mysqli_query($dbc, $query);
			echo "<script>alert('$accordion Accordion Deleted');window.location.replace('field_config_staff.php?tab=staff');</script>";
			exit();
		}
		$subtab = filter_var($_POST['subtab'],FILTER_SANITIZE_STRING);
		$order = filter_var($_POST['order'],FILTER_SANITIZE_STRING);
		$fields = ','.implode(',',$_POST['contacts']).',';echo $fields;

		$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configcontactid) AS configcontactid FROM field_config_contacts WHERE tab='Staff' AND subtab='$subtab' AND accordion='$accordion'"));
		$query = "";
		if($get_field_config['configcontactid'] > 0) {
			$query = "UPDATE `field_config_contacts` SET `contacts` = '$fields', `order` = '$order' WHERE tab='Staff' AND subtab='$subtab' AND accordion='$accordion'";
		} else {
			$query = "INSERT INTO `field_config_contacts` (`tab`, `subtab`, `accordion`, `contacts`, `order`) VALUES ('Staff', '$subtab', '$accordion', '$fields', '$order')";
		}
		$result = mysqli_query($dbc, $query);

		//echo '<script type="text/javascript"> window.location.replace("field_config_staff.php?tab=Staff&subtab='.$subtab.'&accr='.$accordion.'"); </script>';
	}
	else if($_POST['submit'] == 'profile') {
		foreach($_POST as $name => $value) {
			if(substr($name,0,8) == 'contacts') {
				$panel = substr($name,8);
				$subtab = $_POST['subtab'.$panel];
				$accordion = $_POST['accordion'.$panel];
				$count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) numrows FROM `field_config_contacts` WHERE `tab`='Profile' AND `subtab`='$subtab' AND `accordion`='$accordion'"));
				$query = "";
				$contacts = ','.implode(',',$value).',';
				if($count['numrows'] > 0) {
					$query = "UPDATE `field_config_contacts` SET `contacts`='$contacts' WHERE `tab`='Profile' AND `subtab`='$subtab' AND `accordion`='$accordion'";
				}
				else {
					$query = "INSERT INTO `field_config_contacts` (`contacts`,`tab`,`subtab`,`accordion`) VALUES ('$contacts','Profile','$subtab','$accordion')";
				}
				mysqli_query($dbc, $query);
			}
		}
	}
	else if($_POST['submit'] == 'positions') {
		$add_count = 0;
		foreach($_POST['positions'] as $pos) {
			$user = mysqli_fetch_array(mysqli_query($dbc, "select concat(first_name,' ',last_name) name from contacts where contactid='{$_SESSION['contactid']}'"));
			$time = date('Y-m-d H:i:s');
			$count = mysqli_fetch_array(mysqli_query($dbc, "select count(position_id) positions from positions where name='$pos' and deleted=0"));
			if($count['positions'] == 0) {
				$sql = "INSERT INTO positions (name, history) VALUES ('$pos', 'Position added from Defaults by {$user['name']} at $time.<br />\n')";
				mysqli_query($dbc, $sql);
				$add_count++;
			}
		}
		if($add_count > 0) {
			echo "<script>alert('$add_count default position(s) added.');</script>";
		}
	}
	else if($_POST['submit'] == 'reminders') {

	}
	else if($_POST['submit'] == 'dashboard') {
		$db_config = ','.implode(',',$_POST['dashboard']).',';

		$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configcontactid) AS configcontactid FROM field_config_contacts WHERE tab='Staff' AND `contacts_dashboard` IS NOT NULL"));
		$query = "";
		if($get_config['configcontactid'] > 0) {
			$query = "UPDATE `field_config_contacts` SET `contacts_dashboard` = '$db_config' WHERE tab='Staff' AND `contacts_dashboard` IS NOT NULL";
		} else {
			$query = "INSERT INTO `field_config_contacts` (`tab`, `contacts_dashboard`) VALUES ('Staff', '$db_config')";
		}
		mysqli_query($dbc, $query);

		$field_tabs = implode(',',array_filter($_POST['staff_field_subtabs']));
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='staff_field_subtabs'"));
		if($get_config['configid'] > 0) {
			$query_update_employee = "UPDATE `general_configuration` SET value = '$field_tabs' WHERE name='staff_field_subtabs'";
			$result_update_employee = mysqli_query($dbc, $query_update_employee);
		} else {
			$query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('staff_field_subtabs', '$field_tabs')";
			$result_insert_config = mysqli_query($dbc, $query_insert_config);
		}
	}
	else if($_POST['submit'] == 'categories') {
		$con_categories = $_POST['con_categories'];
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT configcontactid FROM field_config_contacts WHERE tab='Staff' AND `categories` IS NOT NULL"));
		if($get_config['configcontactid'] != '') {
			$configcontactid = $get_config['configcontactid'];
			$query_update_cont_config = "UPDATE `field_config_contacts` SET categories = '$con_categories' WHERE configcontactid=$configcontactid";
			$result_cont_config = mysqli_query($dbc, $query_update_cont_config);
		} else {
			$query_insert_config = "INSERT INTO field_config_contacts (`tab`, `categories`) VALUES ('Staff', '$con_categories')";
			$result_insert_config = mysqli_query($dbc, $query_insert_config);
		}
	}
	else if($_POST['submit'] == 'import_contacts') {
		if(!empty($_FILES['file']['tmp_name'])) {
			$i = 0;
			$error = false;
			$staff = get_contact($dbc, $_SESSION['contactid']);
			$file = $_FILES['file']['tmp_name'];
			$handle = fopen($file, "r");
			$headers = fgetcsv($handle, 0, ",");
			$c = 0;
			while(($row = fgetcsv($handle, 0, ",")) !== false)
			{
				$values = [];
				foreach($headers as $i => $col) {
					$values[filter_var($col,FILTER_SANITIZE_STRING)] = filter_var(htmlentities(isEncrypted($col) ? encryptIt($row[$i]) : $row[$i],FILTER_SANITIZE_STRING));
				}

				$contactid = $values['contactid'];
				if(empty($contactid)) {
					mysqli_query($dbc, "INSERT INTO `contacts` VALUES ()");
					$contactid = mysqli_insert_id($dbc);
					$values['contactid'] = $contactid;
				}
				$values['category'] = 'Staff';
				$values['tile_name'] = 'staff';
				mysqli_query($dbc, "INSERT INTO `contacts_cost` (`contactid`) SELECT '$contactid' FROM (SELECT COUNT(*) num FROM `contacts_cost` WHERE `contactid` = '$contactid') rows WHERE rows.num=0");
				mysqli_query($dbc, "INSERT INTO `contacts_dates` (`contactid`) SELECT '$contactid' FROM (SELECT COUNT(*) num FROM `contacts_dates` WHERE `contactid` = '$contactid') rows WHERE rows.num=0");
				mysqli_query($dbc, "INSERT INTO `contacts_description` (`contactid`) SELECT '$contactid' FROM (SELECT COUNT(*) num FROM `contacts_description` WHERE `contactid` = '$contactid') rows WHERE rows.num=0");
				mysqli_query($dbc, "INSERT INTO `contacts_medical` (`contactid`) SELECT '$contactid' FROM (SELECT COUNT(*) num FROM `contacts_medical` WHERE `contactid` = '$contactid') rows WHERE rows.num=0");

				if($contactid > 0) {
					$original = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` LEFT JOIN `contacts_cost` ON `contacts`.`contactid`=`contacts_cost`.`contactid` LEFT JOIN `contacts_dates` ON `contacts`.`contactid`=`contacts_dates`.`contactid` LEFT JOIN `contacts_description` ON `contacts`.`contactid`=`contacts_description`.`contactid` LEFT JOIN `contacts_medical` ON `contacts`.`contactid`=`contacts_medical`.`contactid` WHERE `contacts`.`contactid` = '$contactid'"));
					$updates = [];
					$history = '';
					foreach($values as $field => $value) {
						if($field != '' && $field != 'contactid') {
							$updates[] = "`$field`='$value'";
							if($value != $original[$field]) {
								if(isEncrypted($field) && $field != 'password') {
									$history .= "$field updated from ".decryptIt($original[$field])." to ".decryptIt($value)."<br />\n";
								} else {
									$history .= "$field updated from ".$original[$field]." to ".$value."<br />\n";
								}
							}
						}
					}
					$sql = "UPDATE `contacts` LEFT JOIN `contacts_cost` ON `contacts`.`contactid`=`contacts_cost`.`contactid` LEFT JOIN `contacts_dates` ON `contacts`.`contactid`=`contacts_dates`.`contactid` LEFT JOIN `contacts_description` ON `contacts`.`contactid`=`contacts_description`.`contactid` LEFT JOIN `contacts_medical` ON `contacts`.`contactid`=`contacts_medical`.`contactid` SET ".implode(',',$updates)." WHERE `contacts`.`contactid`='$contactid'";
					if(!mysqli_query($dbc, $sql)) {
						echo "Error on contactid $contactid: ".mysqli_error($dbc)."<br />\n";
						$error = true;
					} else {
						mysqli_query($dbc, "INSERT INTO `contacts_history` (`updated_by`, `description`, `contactid`) VALUES ('$staff', '".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."', '$contactid')");
						mysqli_query($dbc, "INSERT INTO `import_export_log` (table_name, type, description, date_time, contact) VALUES ('Contacts', 'Edit', '".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."', '$today_date', '".decryptIt($values['first_name']).' '.decryptIt($values['last_name'])." ($contactid)')");
					}
				}
			}
			fclose($handle);
		    echo '<script type="text/javascript"> alert("'.($error ? 'Some rows had errors. Please review the notes and make any corrections to the data to upload the data.' : 'Successfully imported CSV file. Please check the dashboard to view your updated records.').'"); </script>';
		}
	}
}
if(isset($_GET['tab'])) {
	switch($_GET['tab']) {
		case 'positions': $tab = 'positions'; break;
		case 'dashboard': $tab = 'dashboard'; break;
		case 'profile': $tab = 'profile'; break;
		case 'reminders': $tab = 'reminders'; break;
		case 'categories': $tab = 'categories'; break;
		case 'import' : $tab = 'import'; break;
		case 'business_card': $tab = 'business_card'; break;
		default: $tab = 'staff'; break;
	}
} else {
	$tab = 'staff';
}
$tab_list = mysqli_fetch_all(mysqli_query($dbc,"SELECT DISTINCT `subtab` FROM `field_config_contacts` WHERE `tab`='Staff'"));
if(!empty($_GET['subtab'])) {
	$subtab = $_GET['subtab'];
} else {
	$subtab = 'staff';
}

?>
<script type="text/javascript">
var subtab = '<?php echo $subtab; ?>';
var tab = '<?php echo $tab; ?>';
var accordion = '<?php echo $_GET['accr']; ?>';
$(document).ready(function() {
	$("#accr").change(function() {
		accordion = this.value;
        set_fields();
	});
	$("#subtab").change(function() {
		accordion = '';
		subtab = this.value;
        set_fields();
	});
	var selected = $('[name=accordion]').val();
	var options = $('[name=accordion]').find('option');
	options.sort(function(a,b) {
		if(b.text == '')
			return 0;
		if(a.text.substring(0,1) == ':')
			return 0;
		if(a.text.toUpperCase() > b.text.toUpperCase())
			return 1;
		if(a.text.toUpperCase() < b.text.toUpperCase())
			return -1;
		return 0;
	});
	$('[name=accordion]').empty().append(options);
	$('[name=accordion]').val(selected).trigger('change.select2');

	if($('#accr').val() == '') {
		$('input[type=checkbox]').attr('disabled','disabled');
		$('button[type=submit]').attr('disabled','disabled');
	}
});
function set_fields() {
	window.location = 'field_config_staff.php?tab='+tab+'&subtab='+subtab+'&accr='+accordion;
}
function delete_accordion() {
	$('[name=del_accr]').val('TRUE');
	return true;
}
</script>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
<div class="row">
<h1>Staff Settings</h1>
<?php if($tab == 'staff'): ?>
	<a href="staff.php?tab=active" class="btn config-btn">Back to Dashboard</a>
	<br><br>
	<a href="?tab=dashboard"><button class="btn brand-btn">Dashboard & Configuration</button></a>
	<a href="?tab=staff"><button class="btn brand-btn active_tab">Staff</button></a>
	<a href="?tab=profile"><button class="btn brand-btn">Profile</button></a>
	<a href="?tab=positions"><button class="btn brand-btn">Positions</button></a>
	<a href="?tab=reminders"><button class="btn brand-btn">Reminders</button></a>
	<a href="?tab=categories"><button class="btn brand-btn">Categories</button></a>
	<a href="?tab=import"><button class="btn brand-btn">Import Contacts</button></a>
	<a href="?tab=business_card"><button class="btn brand-btn">Business Card Templates</button></a>
	<br /><br />
	<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

	<div class="panel-group" id="accordion2">

		<?php
		$contype = "Staff";
		$accr = $_GET['accr'];
		$type = $_GET['type'];

		$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `contacts`, `order` FROM field_config_contacts WHERE tab='$contype' AND subtab='$subtab' AND accordion='$accr'"));
		$contacts_config = ','.$get_field_config['contacts'].',';
		$accr_order = $get_field_config['order'];

		$get_field_order = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT GROUP_CONCAT(`order` SEPARATOR ',') AS all_order FROM field_config_contacts WHERE tab='$contype'"));

		$active_tab = '';
		$active_field = '';
		$active_dashboard = '';
		$active_general = '';
		?>

		<div class="clearfix"></div>
			<div class="form-group">
				<label for="fax_number"	class="col-sm-4	control-label">Tabs:</label>
				<div class="col-sm-8">
					<select data-placeholder="Choose a Tab..." id="subtab" name="subtab" class="chosen-select-deselect form-control" width="380">
					  <option value=""></option>
					  <option <?php echo "staff_information"==$subtab ? "selected " : ""; ?>value="staff_information">Staff Information</option>
					  <option <?php echo "staff_address"==$subtab ? "selected " : ""; ?>value="staff_address">Staff Address</option>
					  <option <?php echo "employee_information"==$subtab ? "selected " : ""; ?>value="emplpoyee_information">Employee Information</option>
					  <option <?php echo "driver_inforamtion"==$subtab ? "selected " : ""; ?>value="driver_information">Driver Information</option>
					  <option <?php echo "direct_deposit_information"==$subtab ? "selected " : ""; ?>value="direct_deposit_information">Direct Deposit Information</option>
					  <option <?php echo "software_id"==$subtab ? "selected " : ""; ?>value="software_id">Software ID</option>
					  <option <?php echo "social_media"==$subtab ? "selected " : ""; ?>value="social_media">Social Media</option>
					  <!-- <option <?php echo "staff"==$subtab ? "selected " : ""; ?>value="staff">Staff</option>
					  <option <?php echo "profile"==$subtab ? "selected " : ""; ?>value="profile">Profile</option> -->
					  <option <?php echo "emergency"==$subtab ? "selected " : ""; ?>value="emergency">Emergency</option>
					  <option <?php echo "health"==$subtab ? "selected " : ""; ?>value="health">Health & Safety</option>
					  <option <?php echo "schedule"==$subtab ? "selected " : ""; ?>value="schedule">Staff Schedule</option>
					  <option <?php echo "hr"==$subtab ? "selected " : ""; ?>value="hr">HR Record</option>
					</select>
				</div>
			</div>
			<button name="inv_field" type="submit" value="inv_field" onclick="delete_accordion();" class="btn config-btn pull-right">Delete Accordion</button>
			<div class="clearfix"></div>
			<div class="form-group">
				<label for="fax_number"	class="col-sm-4	control-label"><span class="popover-examples list-inline" style="margin:0 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="This is the vertically stacked list of items seen below. Click to determine which list of items you would like to make changes to. This must be selected before you can select fields."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Accordion:</label>
				<div class="col-sm-8">
					<input type="hidden" value="FALSE" name="del_accr">
					<select data-placeholder="Choose an Accordion..." id="accr" name="accordion" class="chosen-select-deselect form-control" width="380">
						<?php include('../Contacts/config_accordion_list.php'); ?>
					</select>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label" for="accr_order">Accordion Order:</label>
					<div class="col-sm-8">
						<select data-placeholder="Choose an Order..." name="order" class="chosen-select-deselect form-control" width="380">
							<option value=""></option>
							<?php
							for($m=1;$m<=30;$m++) { ?>
								<option <?php if ($accr_order == $m) { echo  'selected'; } else if (strpos(','.$get_field_order['all_order'].',', ','.$m.',') !== FALSE) { echo " disabled"; } ?>
									value="<?php echo $m;?>"><?php echo $m;?></option>
							<?php }
							?>
						</select>
					</div>
				</div>
			</div>

			<h3>Fields</h3>
			<div class="panel-group" id="accordion2">
				<?php include('../Contacts/config_field_list.php'); ?>
			</div>

			<div class="pull-left">
	            <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to discard your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<a href="staff.php" class="btn brand-btn btn-lg">Back</a>
			</div>
	        <div class="pull-right">
	            <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to submit your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<button	type="submit" name="inv_field" value="inv_field" class="btn brand-btn btn-lg">Submit</button>
			</div>

	</form>
<?php elseif($tab == 'positions'): ?>
	<a href="staff.php?tab=positions" class="btn config-btn">Back to Dashboard</a>
	<br><br>
	<a href="?tab=dashboard"><button class="btn brand-btn">Dashboard & Configuration</button></a>
	<a href="?tab=staff"><button class="btn brand-btn">Staff</button></a>
	<a href="?tab=profile"><button class="btn brand-btn">Profile</button></a>
	<a href="?tab=positions"><button class="btn brand-btn active_tab">Positions</button></a>
	<a href="?tab=reminders"><button class="btn brand-btn">Reminders</button></a>
	<a href="?tab=categories"><button class="btn brand-btn">Categories</button></a>
	<a href="?tab=import"><button class="btn brand-btn">Import Contacts</button></a>
	<a href="?tab=business_card"><button class="btn brand-btn">Business Card Templates</button></a>
	<br /><br />
	<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

	<div class="panel-group" id="accordion2">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to add default positions. This will not remove existing positions."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<a data-toggle="collapse" data-parent="#accordion2" href="#default_positions" >
					   Default Positions<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<?php $result = mysqli_query($dbc, "select distinct name from positions where deleted=0");
			$positions = [];
			while($row = mysqli_fetch_array($result)) {
				$positions[] = $row['name'];
			} ?>
			<div id="default_positions" class="panel-collapse collapse">
				<div class="panel-body">
					<label style="width:10em;"><input type="checkbox" value="Manager" <?php echo in_array("Manager", $positions) ? "checked " : ""; ?>style="margin: 0.5em;" name="positions[]">Manager</label>
					<label style="width:10em;"><input type="checkbox" value="Supervisor" <?php echo in_array("Supervisor", $positions) ? "checked " : ""; ?>style="margin: 0.5em;" name="positions[]">Supervisor</label>
					<label style="width:10em;"><input type="checkbox" value="CRW 40" <?php echo in_array("CRW 40", $positions) ? "checked " : ""; ?>style="margin: 0.5em;" name="positions[]">CRW 40</label>
					<label style="width:10em;"><input type="checkbox" value="CRW 50" <?php echo in_array("CRW 50", $positions) ? "checked " : ""; ?>style="margin: 0.5em;" name="positions[]">CRW 50</label>
					<label style="width:10em;"><input type="checkbox" value="CRW 20" <?php echo in_array("CRW 20", $positions) ? "checked " : ""; ?>style="margin: 0.5em;" name="positions[]">CRW 20</label>
					<label style="width:10em;"><input type="checkbox" value="AON 40" <?php echo in_array("AON 40", $positions) ? "checked " : ""; ?>style="margin: 0.5em;" name="positions[]">AON 40</label>
					<label style="width:10em;"><input type="checkbox" value="AON 30" <?php echo in_array("AON 30", $positions) ? "checked " : ""; ?>style="margin: 0.5em;" name="positions[]">AON 30</label>
					<label style="width:10em;"><input type="checkbox" value="Subsistence Pay" <?php echo in_array("Subsistence Pay", $positions) ? "checked " : ""; ?>style="margin: 0.5em;" name="positions[]">Subsistence Pay</label>
					<label style="width:10em;"><input type="checkbox" value="Relief" <?php echo in_array("Relief", $positions) ? "checked " : ""; ?>style="margin: 0.5em;" name="positions[]">Relief</label>
				</div>
			</div>
		</div>
	</div>

	<div class="pull-left">
        <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to discard your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
		<a href="staff.php?tab=positions" class="btn brand-btn btn-lg">Back</a>
	</div>
    <div class="pull-right">
        <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to submit your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
		<button	type="submit" name="submit" value="positions" class="btn brand-btn btn-lg">Submit</button>
	</div>

	</form>
<?php elseif($tab == 'profile'): ?>
	<a href="staff.php?tab=active" class="btn config-btn">Back to Dashboard</a>
	<br><br>
	<a href="?tab=dashboard"><button class="btn brand-btn">Dashboard & Configuration</button></a>
	<a href="?tab=staff"><button class="btn brand-btn">Staff</button></a>
	<a href="?tab=profile"><button class="btn brand-btn active_tab">Profile</button></a>
	<a href="?tab=positions"><button class="btn brand-btn">Positions</button></a>
	<a href="?tab=reminders"><button class="btn brand-btn">Reminders</button></a>
	<a href="?tab=categories"><button class="btn brand-btn">Categories</button></a>
	<a href="?tab=import"><button class="btn brand-btn">Import Contacts</button></a>
	<a href="?tab=business_card"><button class="btn brand-btn">Business Card Templates</button></a>
	<br /><br />
	<div class="notice double-gap-bottom double-gap-top popover-examples">
		<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
		<div class="col-sm-16"><span class="notice-name">NOTE:</span>
		All of these fields are visible in the My Profile tile. Checking or unchecking them here will make them editable through the My Profile tile by the currently logged in Staff Member.</div>
	</div>
	<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

		<div class="panel-group" id="accordion2">
			<?php $query_all_fields = "SELECT `subtab`, `accordion`, `contacts`, `order` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab` != '' AND `subtab` != '**no_subtab**'
				ORDER BY `order`";
			$result_all_fields = mysqli_query($dbc, $query_all_fields);
			$i = 0;
			while($row_fields = mysqli_fetch_assoc($result_all_fields)) {
				$i++;
				$row_edit = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Profile' AND `subtab` = '{$row_fields['subtab']}' AND `accordion` = '{$row_fields['accordion']}'")); ?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_<?php echo $i; ?>">
							   <?php echo ucwords($row_fields['subtab']).' - '.ucwords($row_fields['accordion']); ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>
					<div id="collapse_<?php echo $i; ?>" class="panel-collapse collapse">
						<div class="panel-body">
							<input type="hidden" name="subtab_<?php echo $i; ?>" value="<?php echo $row_fields['subtab']; ?>">
							<input type="hidden" name="accordion_<?php echo $i; ?>" value="<?php echo $row_fields['accordion']; ?>">
							<?php $field_list = explode(',', trim($row_fields['contacts'],','));
							foreach($field_list as $field): ?>
								<label style="margin:0.5em;">
								<input type="checkbox" style="margin:0.5em;" name="contacts<?php echo "_$i"; ?>[]" <?php echo strpos($row_edit['contacts'], ','.$field.',') !== false ? 'checked ' : ''; ?>value="<?php echo $field; ?>">
								<?php echo $field; ?></label>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>

		<div class="pull-left">
	        <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to discard your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a href="staff.php?tab=active" class="btn brand-btn btn-lg">Back</a>
		</div>
	    <div class="pull-right">
	        <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to submit your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<button	type="submit" name="submit" value="profile" class="btn brand-btn btn-lg">Submit</button>
		</div>
	</form>
<?php elseif($tab == 'reminders'): ?>
	<a href="staff.php?tab=staff" class="btn config-btn">Back to Dashboard</a>
	<br><br>
	<a href="?tab=dashboard"><button class="btn brand-btn">Dashboard & Configuration</button></a>
	<a href="?tab=staff"><button class="btn brand-btn">Staff</button></a>
	<a href="?tab=profile"><button class="btn brand-btn">Profile</button></a>
	<a href="?tab=positions"><button class="btn brand-btn">Positions</button></a>
	<a href="?tab=reminders"><button class="btn brand-btn active_tab">Reminders</button></a>
	<a href="?tab=categories"><button class="btn brand-btn">Categories</button></a>
	<a href="?tab=import"><button class="btn brand-btn">Import Contacts</button></a>
	<a href="?tab=business_card"><button class="btn brand-btn">Business Card Templates</button></a>
	<br /><br />
	Coming Soon!
<?php elseif($tab == 'dashboard'): ?>
	<a href="staff.php?tab=staff" class="btn config-btn">Back to Dashboard</a>
	<br><br>
	<a href="?tab=dashboard"><button class="btn brand-btn active_tab">Dashboard & Configuration</button></a>
	<a href="?tab=staff"><button class="btn brand-btn">Staff</button></a>
	<a href="?tab=profile"><button class="btn brand-btn">Profile</button></a>
	<a href="?tab=positions"><button class="btn brand-btn">Positions</button></a>
	<a href="?tab=reminders"><button class="btn brand-btn">Reminders</button></a>
	<a href="?tab=categories"><button class="btn brand-btn">Categories</button></a>
	<a href="?tab=import"><button class="btn brand-btn">Import Contacts</button></a>
	<a href="?tab=business_card"><button class="btn brand-btn">Business Card Templates</button></a>
	<br /><br />
	<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

		<div class="panel-group" id="accordion2">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_dashboard">
						   Dashboard Fields<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>
				<div id="collapse_dashboard" class="panel-collapse collapse in">
					<div class="panel-body">
						<?php $db_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts_dashboard` FROM `field_config_contacts` WHERE `tab`='Staff' AND `contacts_dashboard` IS NOT NULL"),MYSQLI_NUM); ?>
						<label class="form-checkbox"><input type="checkbox" name="dashboard[]" <?= strpos($db_config[0], ',Business,') !== false ? 'checked' : '' ?> value="Business">
						Business</label>
						<label class="form-checkbox"><input type="checkbox" name="dashboard[]" <?= strpos($db_config[0], ',Employee ID,') !== false ? 'checked' : '' ?> value="Employee ID">
						Employee ID</label>
						<label class="form-checkbox"><input type="checkbox" name="dashboard[]" <?= strpos($db_config[0], ',Employee #,') !== false ? 'checked' : '' ?> value="Employee #">
						Employee #</label>
						<label class="form-checkbox"><input type="checkbox" name="dashboard[]" <?= strpos($db_config[0], ',Category,') !== false ? 'checked' : '' ?> value="Category">
						Category</label>
						<label class="form-checkbox"><input type="checkbox" name="dashboard[]" <?= strpos($db_config[0], ',Staff Category,') !== false ? 'checked' : '' ?> value="Staff Category">
						Staff Category</label>
						<label class="form-checkbox"><input type="checkbox" name="dashboard[]" <?= strpos($db_config[0], ',License,') !== false ? 'checked' : '' ?> value="License">
						License#</label>
						<label class="form-checkbox"><input type="checkbox" name="dashboard[]" <?= strpos($db_config[0], ',Login,') !== false ? 'checked' : '' ?> value="Login">
						Login</label>
						<label class="form-checkbox"><input type="checkbox" name="dashboard[]" <?= strpos($db_config[0], ',Email,') !== false ? 'checked' : '' ?> value="Email">
						Email</label>
						<label class="form-checkbox"><input type="checkbox" name="dashboard[]" <?= strpos($db_config[0], ',Home Phone,') !== false ? 'checked' : '' ?> value="Home Phone">
						Home Phone</label>
						<label class="form-checkbox"><input type="checkbox" name="dashboard[]" <?= strpos($db_config[0], ',Office Phone,') !== false ? 'checked' : '' ?> value="Office Phone">
						Office Phone</label>
						<label class="form-checkbox"><input type="checkbox" name="dashboard[]" <?= strpos($db_config[0], ',Cell Phone,') !== false ? 'checked' : '' ?> value="Cell Phone">
						Cell Phone</label>
						<label class="form-checkbox"><input type="checkbox" name="dashboard[]" <?= strpos($db_config[0], ',Address,') !== false ? 'checked' : '' ?> value="Address">
						Address</label>
						<label class="form-checkbox"><input type="checkbox" name="dashboard[]" <?= strpos($db_config[0], ',Position,') !== false ? 'checked' : '' ?> value="Position">
						Position</label>
						<label class="form-checkbox"><input type="checkbox" name="dashboard[]" <?= strpos($db_config[0], ',Pronoun,') !== false ? 'checked' : '' ?> value="Pronoun">
						Preferred Pronoun</label>
						<label class="form-checkbox"><input type="checkbox" name="dashboard[]" <?= strpos($db_config[0], ',Birthdate,') !== false ? 'checked' : '' ?> value="Birthdate">
						Date of Birth / Age</label>
						<label class="form-checkbox"><input type="checkbox" name="dashboard[]" <?= strpos($db_config[0], ',Social,') !== false ? 'checked' : '' ?> value="Social">
						Social Media Links</label>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_tabs">
						   Tab Configuration<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>
				<div id="collapse_tabs" class="panel-collapse collapse">
					<div class="panel-body">
						<!--Add Field Subtab separated by a comma in the order you want them on the Edit page:<br />
						<br />
						<input name="staff_field_subtabs" type="text" value="<?php echo get_config($dbc, 'staff_field_subtabs'); ?>" class="form-control"/><br />-->
						<?php $staff_field_subtabs = ','.get_config($dbc, 'staff_field_subtabs').','; ?>
						<!-- <label style="margin:0.5em;"><input type="checkbox" style="margin:0.5em;" name="staff_field_subtabs[]" <?php echo strpos($staff_field_subtabs, ',ID Card,') !== false ? 'checked ' : ''; ?>value="ID Card">
						ID Card</label> -->
						<label style="margin:0.5em;"><input type="checkbox" style="margin:0.5em;" name="staff_field_subtabs[]" <?php echo strpos($staff_field_subtabs, ',Staff Information,') !== false ? 'checked ' : ''; ?>value="Staff Information">
						Staff Information</label>
						<label style="margin:0.5em;"><input type="checkbox" style="margin:0.5em;" name="staff_field_subtabs[]" <?php echo strpos($staff_field_subtabs, ',Staff Address,') !== false ? 'checked ' : ''; ?>value="Staff Address">
						Staff Address</label>
						<label style="margin:0.5em;"><input type="checkbox" style="margin:0.5em;" name="staff_field_subtabs[]" <?php echo strpos($staff_field_subtabs, ',Employee Information,') !== false ? 'checked ' : ''; ?>value="Employee Information">
						Employee Information</label>
						<label style="margin:0.5em;"><input type="checkbox" style="margin:0.5em;" name="staff_field_subtabs[]" <?php echo strpos($staff_field_subtabs, ',Driver Information,') !== false ? 'checked ' : ''; ?>value="Driver Information">
						Driver Information</label>
						<label style="margin:0.5em;"><input type="checkbox" style="margin:0.5em;" name="staff_field_subtabs[]" <?php echo strpos($staff_field_subtabs, ',Direct Deposit Information,') !== false ? 'checked ' : ''; ?>value="Direct Deposit Information">
						Direct Deposit Information</label>
						<label style="margin:0.5em;"><input type="checkbox" style="margin:0.5em;" name="staff_field_subtabs[]" <?php echo strpos($staff_field_subtabs, ',Software ID,') !== false ? 'checked ' : ''; ?>value="Software ID">
						Software ID</label>
						<label style="margin:0.5em;"><input type="checkbox" style="margin:0.5em;" name="staff_field_subtabs[]" <?php echo strpos($staff_field_subtabs, ',Social Media,') !== false ? 'checked ' : ''; ?>value="Social Media">
						Social Media</label>
						<label style="margin:0.5em;"><input type="checkbox" style="margin:0.5em;" name="staff_field_subtabs[]" <?php echo strpos($staff_field_subtabs, ',Emergency,') !== false ? 'checked ' : ''; ?>value="Emergency">
						Emergency</label>
						<label style="margin:0.5em;"><input type="checkbox" style="margin:0.5em;" name="staff_field_subtabs[]" <?php echo strpos($staff_field_subtabs, ',Health,') !== false ? 'checked ' : ''; ?>value="Health">
						Health & Safety</label>
						<label style="margin:0.5em;"><input type="checkbox" style="margin:0.5em;" name="staff_field_subtabs[]" <?php echo strpos($staff_field_subtabs, ',Schedule,') !== false ? 'checked ' : ''; ?>value="Schedule">
						Staff Schedule</label>
						<label style="margin:0.5em;"><input type="checkbox" style="margin:0.5em;" name="staff_field_subtabs[]" <?php echo strpos($staff_field_subtabs, ',Certificates,') !== false ? 'checked ' : ''; ?>value="Certificates">
						Certificates</label>
						<label style="margin:0.5em;"><input type="checkbox" style="margin:0.5em;" name="staff_field_subtabs[]" <?php echo strpos($staff_field_subtabs, ',HR,') !== false ? 'checked ' : ''; ?>value="HR">
						HR Record</label>
						<label style="margin:0.5em;"><input type="checkbox" style="margin:0.5em;" name="staff_field_subtabs[]" <?php echo strpos($staff_field_subtabs, ',Time Off,') !== false ? 'checked ' : ''; ?>value="Time Off">
						Time Off Requests</label>
					</div>
				</div>
			</div>
		</div>

        <div class="pull-left">
            <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to discard your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a href="staff.php?tab=active" class="btn brand-btn btn-lg">Back</a>
		</div>
        <div class="pull-right">
            <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to submit your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<button	type="submit" name="submit" value="dashboard" class="btn brand-btn btn-lg">Submit</button>
		</div>
	</form>
<?php elseif($tab == 'categories'): ?>
	<a href="staff.php?tab=staff" class="btn config-btn">Back to Dashboard</a>
	<br><br>
	<a href="?tab=dashboard"><button class="btn brand-btn">Dashboard & Configuration</button></a>
	<a href="?tab=staff"><button class="btn brand-btn">Staff</button></a>
	<a href="?tab=profile"><button class="btn brand-btn">Profile</button></a>
	<a href="?tab=positions"><button class="btn brand-btn">Positions</button></a>
	<a href="?tab=reminders"><button class="btn brand-btn">Reminders</button></a>
	<a href="?tab=categories"><button class="btn brand-btn active_tab">Categories</button></a>
	<a href="?tab=import"><button class="btn brand-btn">Import Contacts</button></a>
	<a href="?tab=business_card"><button class="btn brand-btn">Business Card Templates</button></a>
	<br /><br />
	<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

		<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to add your own tabs."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						<a data-toggle="collapse" data-parent="#accordion_tabs" href="#collapse_2">
							Add Categories<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>
				<div id="collapse_2" class="panel-collapse collapse in">
					<div class="panel-body">
						Add categories separated by a comma in the order you want them on the category listing:<br />
						<br />
						<?php
							$con_categories = $_POST['con_categories'];
							$get_config_values = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT categories FROM field_config_contacts WHERE tab='Staff' AND `categories` IS NOT NULL"));
						?>
						<input name="con_categories" type="text" value="<?php echo str_replace(',,',',',str_replace('Staff','',$get_config_values['categories'])); ?>" class="form-control"/><br />
					</div>
				</div>
			</div><!-- .panel .panel-default -->
		<div class="pull-left">
            <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to discard your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a href="staff.php?tab=active" class="btn brand-btn btn-lg">Back</a>
		</div>
		<div class="pull-right">
            <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to discard your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<button	type="submit" name="submit" value="categories" class="btn brand-btn btn-lg">Submit</button>
		</div>
	</form>
<?php elseif($tab == 'import'): ?>
	<a href="staff.php?tab=staff" class="btn config-btn">Back to Dashboard</a>
	<br><br>
	<a href="?tab=dashboard"><button class="btn brand-btn">Dashboard & Configuration</button></a>
	<a href="?tab=staff"><button class="btn brand-btn">Staff</button></a>
	<a href="?tab=profile"><button class="btn brand-btn">Profile</button></a>
	<a href="?tab=positions"><button class="btn brand-btn">Positions</button></a>
	<a href="?tab=reminders"><button class="btn brand-btn">Reminders</button></a>
	<a href="?tab=categories"><button class="btn brand-btn">Categories</button></a>
	<a href="?tab=import"><button class="btn brand-btn active_tab">Import Contacts</button></a>
	<a href="?tab=business_card"><button class="btn brand-btn">Business Card Templates</button></a>
	<br /><br />
	<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
		<div class="notice">Steps to Import Contacts in the <?= FOLDER_URL ?> tile:<br><Br>
			<b>1.</b> Please download the Excel (CSV) file from the dashboard page.<br><br>
			<b>2.</b> Make your desired changes inside of the Excel file. To add new contacts, leave the first column (contactid) blank.<br>
			<span style='color:pink;'><img src='../img/warning.png' style='width:25px;'> NOTE</span>: Do not change/move/delete any of the column titles in the first row. Also, do not change the data in the first column (contactid) unless you are adding a new contact, or else the edits may not go through properly. <br><span style='color:lightgreen'><b>Hint:</b></span> press CTRL+F on your keyboard to find the fields you would like to populate; this will help you locate them faster.<br><br>
			<b>3.</b> After you are done editing the data, save your Excel (CSV) file, upload the CSV file below, and hit submit.<br><br>
			<b>4.</b> Please look for your edited Contacts in the <?= FOLDER_URL ?> dashboard!<br><br>
			<input class="form-control" type="file" name="file" /><br />
		</div>
		<div class="pull-left">
            <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to discard your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a href="staff.php?tab=active" class="btn brand-btn btn-lg">Back</a>
		</div>
		<div class="pull-right">
            <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to discard your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<button	type="submit" name="submit" value="import_contacts" class="btn brand-btn btn-lg">Submit</button>
		</div>
	</form>
<?php elseif($tab == 'business_card'): ?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#template').change(function() {
				location = '?tab=business_card&template='+$(this).val();
				// window.location.href = location;
			});
		});
	</script>
	<a href="staff.php?tab=staff" class="btn config-btn">Back to Dashboard</a>
	<br><br>
	<a href="?tab=dashboard"><button class="btn brand-btn">Dashboard & Configuration</button></a>
	<a href="?tab=staff"><button class="btn brand-btn">Staff</button></a>
	<a href="?tab=profile"><button class="btn brand-btn">Profile</button></a>
	<a href="?tab=positions"><button class="btn brand-btn">Positions</button></a>
	<a href="?tab=reminders"><button class="btn brand-btn">Reminders</button></a>
	<a href="?tab=categories"><button class="btn brand-btn">Categories</button></a>
	<a href="?tab=import"><button class="btn brand-btn">Import Contacts</button></a>
	<a href="?tab=business_card"><button class="btn brand-btn active_tab">Business Card Templates</button></a>
	<br /><br />

	<?php
	if(isset($_GET['template'])) {
		$template = $_GET['template'];
	} ?>

	<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

		<div class="clearfix"></div>
		<div class="form-group">
			<label for="fax_number"	class="col-sm-4	control-label">Template:</label>
			<div class="col-sm-8">
				<select data-placeholder="Choose a Template..." id="template" name="template" class="chosen-select-deselect form-control" width="380">
				  <option value=""></option>
				  <option <?php echo "template_a"==$template ? "selected " : ""; ?>value="template_a">Template A</option>
				</select>
			</div>
		</div>

	<?php if(isset($_GET['template'])) { ?>
		<div style="width: 100%; text-align: center;"><iframe style="width: 300px; height: 400px" src="../Staff/business_card_templates/<?= $template ?>_pdf.php?preview_template=true" type="application/pdf"></iframe></div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to add your own tabs."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<a data-toggle="collapse" data-parent="#accordion_tabs" href="#collapse_2">
						Choose Fields<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_2" class="panel-collapse collapse in">
				<div class="panel-body">
					<?php include('../Staff/business_card_templates/'.$template.'.php'); ?>
				</div>
			</div>
		</div><!-- .panel .panel-default -->
		<div class="pull-left">
            <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to discard your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a href="staff.php?tab=active" class="btn brand-btn btn-lg">Back</a>
		</div>
		<div class="pull-right">
            <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to discard your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<button	type="submit" name="submit" value="templates" class="btn brand-btn btn-lg">Submit</button>
		</div>
	<?php } ?>
	</form>
<?php endif; ?>
</div>
</div>

<?php include ('../footer.php'); ?>
