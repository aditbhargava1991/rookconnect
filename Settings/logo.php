<?php
/*
Customer Listing
*/
include_once('../include.php');

if (isset($_POST['add_favicon'])) {
	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}
    //Logo
	$pos_logo = $_FILES["fileToUpload"]["name"];

	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='logo_upload'"));
	if($get_config['configid'] > 0) {
		if($pos_logo == '') {
			$logo_update = $_POST['logo_file'];
		} else {
			$logo_update = $pos_logo;
		}
		move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],"download/" . $logo_update);
		$target_file = "download/" . $logo_update;
		$query_update_employee = "UPDATE `general_configuration` SET value = '$logo_update' WHERE name='logo_upload'";
		$result_update_employee = mysqli_query($dbc, $query_update_employee);
	} else {
		move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], "download/" . $_FILES["fileToUpload"]["name"]) ;
		$query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('logo_upload', '$pos_logo')";
		$result_insert_config = mysqli_query($dbc, $query_insert_config);
	}

    //Logo Icon
	$pos_logo_icon = $_FILES["icon_fileToUpload"]["name"];

	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='logo_upload_icon'"));
	if($get_config['configid'] > 0) {
		if($pos_logo_icon == '') {
			$icon_logo_update = $_POST['icon_logo_file'];
		} else {
			$icon_logo_update = $pos_logo_icon;
		}
		move_uploaded_file($_FILES["icon_fileToUpload"]["tmp_name"],"download/" . $icon_logo_update);
		$target_file = "download/" . $icon_logo_update;
		$query_update_employee = "UPDATE `general_configuration` SET value = '$icon_logo_update' WHERE name='logo_upload_icon'";
		$result_update_employee = mysqli_query($dbc, $query_update_employee);
	} else {
		move_uploaded_file($_FILES["icon_fileToUpload"]["tmp_name"], "download/" . $_FILES["icon_fileToUpload"]["name"]) ;
		$query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('logo_upload_icon', '$pos_logo_icon')";
		$result_insert_config = mysqli_query($dbc, $query_insert_config);
	}
    
    //CA Logo
	$ca_pos_logo = $_FILES["ca_fileToUpload"]["name"];

	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='ca_logo_upload'"));
	if($get_config['configid'] > 0) {
		if($ca_pos_logo == '') {
			$ca_logo_update = $_POST['ca_logo_file'];
		} else {
			$ca_logo_update = $ca_pos_logo;
		}
		move_uploaded_file($_FILES["ca_fileToUpload"]["tmp_name"],"download/" . $ca_logo_update);
		$ca_target_file = "download/" . $ca_logo_update;
		$query_update_employee = "UPDATE `general_configuration` SET value = '$ca_logo_update' WHERE name='ca_logo_upload'";
		$result_update_employee = mysqli_query($dbc, $query_update_employee);
	} else {
		move_uploaded_file($_FILES["ca_fileToUpload"]["tmp_name"], "download/" . $_FILES["ca_fileToUpload"]["name"]) ;
		$query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ca_logo_upload', '$ca_pos_logo')";
		$result_insert_config = mysqli_query($dbc, $query_insert_config);
	}
}

if (isset($_POST['delete_favicon'])) {
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='logo_upload'"));
	if($get_config['configid'] > 0) {
		$query_update_employee = "UPDATE `general_configuration` SET value = '' WHERE name='logo_upload'";
		$result_update_employee = mysqli_query($dbc, $query_update_employee);
	}
}

$notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='setting_logo'"));
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

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<div class="form-group">
		<label for="fax_number"	class="col-sm-4	control-label">Upload Logo Image:</label>
		<div class="col-sm-8">
		 <?php
			$logo_upload = get_config($dbc, 'logo_upload');
			if($logo_upload != '') {
				echo '<a href="download/'.$logo_upload.'" target="_blank">View Logo Image</a> &nbsp;&nbsp;';
			}
		  ?>
		  <input type="hidden" name="logo_file" value="<?php echo $logo_upload; ?>" />
		  <input type="file" accept="image/*" name="fileToUpload" id="fileToUpload">
		</div>
	</div>
	<br>
    <div class="form-group">
		<label for="fax_number"	class="col-sm-4	control-label">Upload Logo Icon Image:</label>
		<div class="col-sm-8">
		 <?php
			$logo_upload_icon = get_config($dbc, 'logo_upload_icon');
			if($logo_upload_icon != '') {
				echo '<a href="download/'.$logo_upload_icon.'" target="_blank">View Logo Icon Image</a> &nbsp;&nbsp;';
			}
		  ?>
		  <input type="hidden" name="icon_logo_file" value="<?php echo $logo_upload_icon; ?>" />
		  <input type="file" accept="image/*" name="icon_fileToUpload" id="icon_fileToUpload" />
		</div>
	</div>
    <br>
	<div class="form-group">
		<label for="fax_number"	class="col-sm-4	control-label">Upload Clinic Ace Appointment Calendar Logo Image:</label>
		<div class="col-sm-8">
		 <?php
			$ca_logo_upload = get_config($dbc, 'ca_logo_upload');
			if($ca_logo_upload != '') {
				echo '<a href="download/'.$ca_logo_upload.'" target="_blank">View Logo Image</a> &nbsp;&nbsp;';
			}
		  ?>
		  <input type="hidden" name="ca_logo_file" value="<?php echo $ca_logo_upload; ?>" />
		  <input type="file" accept="image/*" name="ca_fileToUpload" id="ca_fileToUpload">
		</div>
	</div>

	<div class="form-group">
		<!--<div class="col-sm-4 clearfix">
			<a href="contacts.php?category=Business&filter=Top" class="btn config-btn pull-right">Back</a>--
			<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>
		</div>-->
		<div class="col-sm-12">
			<button	type="submit" name="add_favicon" value="add_favicon" class="btn config-btn btn-lg pull-right">Submit</button>
		</div>
	</div>
</form>
