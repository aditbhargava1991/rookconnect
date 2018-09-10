<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');

if (isset($_POST['add_tab'])) {
	// Add and update Tab Settings
	$equipment_main_tabs = filter_var(implode(',',$_POST['equipment_main_tabs']),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'equipment_main_tabs' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='equipment_main_tabs') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$equipment_main_tabs' WHERE `name`='equipment_main_tabs'");
	$equipment_tabs = filter_var($_POST['equipment_tabs'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'equipment_tabs' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='equipment_tabs') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$equipment_tabs' WHERE `name`='equipment_tabs'");

	// Add and update Category Dropdown Settings
	if (isset($_POST['show_category_dropdown_equipment'])) {
		$value = '1';
	} else {
		$value = '';
	}
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_category_dropdown_equipment'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$value' WHERE name='show_category_dropdown_equipment'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('show_category_dropdown_equipment', '$value')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	// Set the Volume Unit field
	set_config($dbc, 'volume_units', $_POST['volume_units']);

	// Use Mass Updates
	$category = filter_var($_POST['mass_update_category'],FILTER_SANITIZE_STRING);
	$field = filter_var($_POST['mass_update_field'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['mass_update_value'],FILTER_SANITIZE_STRING);
	if($category != '' && $field != '' && $value != '') {
		$before_change = capture_before_change($dbc, 'equipment', $field, 'deleted', 0, 'category', $category);
		mysqli_query($dbc, "UPDATE `equipment` SET `$field`='$value' WHERE `deleted`=0 AND `category`='$category'");
		$history = capture_after_change($field, $value);
		add_update_history($dbc, 'equipment_history', $history, '', $before_change);
	}

	// Add and update E-mail Reminder Settings
	$remind_sender = filter_var($_POST['equipment_remind_sender'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'equipment_remind_sender' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='equipment_remind_sender') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$remind_sender' WHERE `name`='equipment_remind_sender'");
	$remind_subject = filter_var($_POST['equipment_remind_subject'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'equipment_remind_subject' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='equipment_remind_subject') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$remind_subject' WHERE `name`='equipment_remind_subject'");
	$remind_body = filter_var(htmlentities($_POST['equipment_remind_body']),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'equipment_remind_body' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='equipment_remind_body') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$remind_body' WHERE `name`='equipment_remind_body'");

    // Update reminder recipient(s)
    if ( !empty($_POST['equipment_remind_admin']) ) {
		$contactid  = implode( ',', $_POST['equipment_remind_admin'] );
		mysqli_query($dbc, "UPDATE `reminders` SET `recipient`='$contactid' WHERE `reminder_type`='Equipment Registration' OR `reminder_type`='Equipment Insurance'");
	}

    // Include Region Information
    if (isset($_POST['assign_equip_region_field'])) {
    	$value = '1';
    } else {
    	$value = '2';
    }
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='assign_equip_region_field'"));
    if($get_config['configid'] > 0) {
        $query = "UPDATE `general_configuration` SET value = '$value' WHERE name='assign_equip_region_field'";
        $result = mysqli_query($dbc, $query);
    } else {
        $query = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('assign_equip_region_field', '$value')";
        $result = mysqli_query($dbc, $query);
    }

    // Include Location Information
    if (isset($_POST['assign_equip_location_field'])) {
    	$value = '1';
    } else {
    	$value = '2';
    }
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='assign_equip_location_field'"));
    if($get_config['configid'] > 0) {
        $query = "UPDATE `general_configuration` SET value = '$value' WHERE name='assign_equip_location_field'";
        $result = mysqli_query($dbc, $query);
    } else {
        $query = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('assign_equip_location_field', '$value')";
        $result = mysqli_query($dbc, $query);
    }


	echo '<script type="text/javascript"> window.location.replace("field_config_equipment.php?type=tab"); </script>';
}
else if (isset($_POST['inspection'])) {
	$tab_name = implode(',',$_POST['tab_inspect']);
	if($tab_name != '') {
		$inspections = filter_var(implode('#*#',array_filter($_POST['inspection_list'])),FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "DELETE FROM `field_config_equipment` WHERE `inspection_list` IS NOT NULL AND (',$tab_name,' LIKE CONCAT(',',`tab`,',') OR CONCAT('%,',`tab`,',%') LIKE '%,$tab_name,%') AND (SELECT COUNT(*) FROM `field_config_equipment` WHERE `inspection_list` IS NOT NULL AND (',$tab_name,' LIKE CONCAT('%,',`tab`,',%') OR CONCAT(',',`tab`,',') LIKE '%,$tab_name,%')) > 1");
		mysqli_query($dbc, "INSERT INTO `field_config_equipment` (`tab`, `inspection_list`) SELECT '$tab_name', '' FROM (SELECT COUNT(*) rows FROM `field_config_equipment` WHERE (',$tab_name,' LIKE CONCAT('%,',`tab`,',%') OR CONCAT(',',`tab`,',') LIKE '%,$tab_name,%') AND `inspection_list` IS NOT NULL) num WHERE num.rows=0");
		mysqli_query($dbc, "UPDATE `field_config_equipment` SET `tab`='$tab_name', `inspection_list`='$inspections' WHERE (',$tab_name,' LIKE CONCAT('%,',`tab`,',%') OR CONCAT(',',`tab`,',') LIKE '%,$tab_name,%') AND `inspection_list` IS NOT NULL");

		foreach ($_POST['tab_inspect'] as $single_tab) {
			foreach ($_POST['inspection_list'] as $i => $row) {
				$inspection_checklist = $_POST['inspection_checklist'][intval($i)];
				$inspection_details = isset($_POST['inspection_details'][intval($i)]) ? 1 : 0;
				if (!empty($inspection_checklist) || $inspection_details != 0) {
					mysqli_query($dbc, "INSERT INTO `field_config_equipment_inspection` (`tab`, `inspection_name`) SELECT '$single_tab', '$row' FROM (SELECT COUNT(*) rows FROM `field_config_equipment_inspection` WHERE `tab` = '$single_tab' AND `inspection_name` = '$row') num WHERE num.rows=0");
					mysqli_query($dbc, "UPDATE `field_config_equipment_inspection` SET `inspection_checklist` = '$inspection_checklist', `inspection_details` = '$inspection_details' WHERE `tab` = '$single_tab' AND `inspection_name` = '$row'");
				}
			}
		}
	}

	$equipment_service_alert = filter_var(implode(',',$_POST['equipment_service_alert']),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'equipment_service_alert' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='equipment_service_alert') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$equipment_service_alert' WHERE `name`='equipment_service_alert'");
	$equipment_service_header = filter_var(htmlentities($_POST['equipment_service_header']),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'equipment_service_header' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='equipment_service_header') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$equipment_service_header' WHERE `name`='equipment_service_header'");
	$equipment_service_footer = filter_var(htmlentities($_POST['equipment_service_footer']),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'equipment_service_footer' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='equipment_service_footer') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$equipment_service_footer' WHERE `name`='equipment_service_footer'");
	$equipment_service_sender_name = filter_var($_POST['equipment_service_sender_name'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'equipment_service_sender_name' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='equipment_service_sender_name') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$equipment_service_sender_name' WHERE `name`='equipment_service_sender_name'");
	$equipment_service_sender = filter_var($_POST['equipment_service_sender'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'equipment_service_sender' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='equipment_service_sender') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$equipment_service_sender' WHERE `name`='equipment_service_sender'");
	$equipment_service_subject = filter_var($_POST['equipment_service_subject'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'equipment_service_subject' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='equipment_service_subject') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$equipment_service_subject' WHERE `name`='equipment_service_subject'");
	$equipment_service_body = filter_var(htmlentities($_POST['equipment_service_body']),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'equipment_service_body' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='equipment_service_body') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$equipment_service_body' WHERE `name`='equipment_service_body'");

	if(!empty($_FILES['equipment_service_logo']['name'])) {
		$filename = $_FILES['equipment_service_logo']['name'];
		$file = $_FILES['equipment_service_logo']['tmp_name'];
		if (!file_exists('download')) {
			mkdir('download', 0777, true);
		}
		$basefilename = $filename = preg_replace('/[^A-Za-z0-9\.]/','_',$filename);
		$i = 0;
		while(file_exists('download/'.$filename)) {
			$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.++$i.')$1', $basefilename);
		}
		move_uploaded_file($file, "download/".$filename);
		$equipment_service_logo = $filename;
		mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'equipment_service_logo' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='equipment_service_logo') num WHERE num.rows=0");
		mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$equipment_service_logo' WHERE `name`='equipment_service_logo'");
	}

	echo '<script type="text/javascript"> window.location.replace("field_config_equipment.php?type=inspection"); </script>';
}
else if(isset($_POST['expenses'])) {
	$equipment_expense_fields = implode(',', $_POST['equipment_expense_fields']);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'equipment_expense_fields' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='equipment_expense_fields') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$equipment_expense_fields' WHERE `name`='equipment_expense_fields'");

	echo "<script> window.location.replace('?type=expenses') </script>";
}
else if($_POST['equip_class_details'] == 'submit') {
	foreach($_POST['class'] as $i => $class_name) {
		$address['address'] = filter_var($_POST['address'][$i],FILTER_SANITIZE_STRING);
		$address['address2'] = filter_var($_POST['address2'][$i],FILTER_SANITIZE_STRING);
		$address['city'] = filter_var($_POST['city'][$i],FILTER_SANITIZE_STRING);
		$address['province'] = filter_var($_POST['province'][$i],FILTER_SANITIZE_STRING);
		$address['postal_code'] = filter_var($_POST['postal_code'][$i],FILTER_SANITIZE_STRING);
		$address['country'] = filter_var($_POST['country'][$i],FILTER_SANITIZE_STRING);
		set_config($dbc, 'equip_class_'.$class_name.'_address_start', json_encode($address));
	}
	echo '<script type="text/javascript"> window.location.replace("field_config_equipment.php?type=classification"); </script>';
}

if (isset($_POST['inv_dashboard'])) {
	$tab_dashboard = filter_var($_POST['tab_dashboard'],FILTER_SANITIZE_STRING);
	$equipment_dashboard = implode(',',$_POST['equipment_dashboard']);

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configinvid) AS configinvid FROM field_config_equipment WHERE tab='$tab_dashboard' AND equipment_dashboard IS NOT NULL AND accordion IS NULL"));
	if($get_field_config['configinvid'] > 0) {
		$query_update_employee = "UPDATE `field_config_equipment` SET equipment_dashboard = '$equipment_dashboard' WHERE tab='$tab_dashboard'";
		$result_update_employee = mysqli_query($dbc, $query_update_employee);
	} else {
		$query_insert_config = "INSERT INTO `field_config_equipment` (`tab`, `equipment_dashboard`) VALUES ('$tab_dashboard', '$equipment_dashboard')";
		$result_insert_config = mysqli_query($dbc, $query_insert_config);
	}
	echo '<script type="text/javascript"> window.location.replace("field_config_equipment.php?type=dashboard&tab='.$tab_dashboard.'"); </script>';
}

if (isset($_POST['inv_field'])) {
	$tab_field = filter_var($_POST['tab_field'],FILTER_SANITIZE_STRING);
	$accordion = filter_var($_POST['accordion'],FILTER_SANITIZE_STRING);
	$equipment = implode(',',$_POST['equipment']);
	$order = filter_var($_POST['order'],FILTER_SANITIZE_STRING);

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configinvid) AS configinvid FROM field_config_equipment WHERE tab='$tab_field' AND accordion='$accordion'"));
	if($get_field_config['configinvid'] > 0) {
		$query_update_employee = "UPDATE `field_config_equipment` SET equipment = '$equipment', `order` = '$order' WHERE tab='$tab_field' AND accordion='$accordion'";
		$result_update_employee = mysqli_query($dbc, $query_update_employee);
	} else {
		$query_insert_config = "INSERT INTO `field_config_equipment` (`tab`, `accordion`, `equipment`, `order`) VALUES ('$tab_field', '$accordion', '$equipment', '$order')";
		$result_insert_config = mysqli_query($dbc, $query_insert_config);
	}

	echo '<script type="text/javascript"> window.location.replace("field_config_equipment.php?type=field&tab='.$tab_field.'&accr='.$accordion.'"); </script>';
}

if (isset($_POST['service_request_btn'])) {
	$service_request = implode(',',$_POST['service_request']);
	$service_request_dashboard = implode(',',$_POST['service_request_dashboard']);

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configinvid) AS configinvid FROM field_config_equipment WHERE tab='service_request'"));
	if($get_field_config['configinvid'] > 0) {
		$query_update_employee = "UPDATE `field_config_equipment` SET equipment = '$service_request', equipment_dashboard = '$service_request_dashboard' WHERE tab='service_request'";
		$result_update_employee = mysqli_query($dbc, $query_update_employee);
	} else {
		$query_insert_config = "INSERT INTO `field_config_equipment` (`tab`, `equipment`, `equipment_dashboard`) VALUES ('service_request', '$service_request', '$service_request_dashboard')";
		$result_insert_config = mysqli_query($dbc, $query_insert_config);
	}

	echo '<script type="text/javascript"> window.location.replace("field_config_equipment.php?type=service_request"); </script>';
}

if (isset($_POST['service_record_btn'])) {
	$service_record = implode(',',$_POST['service_record']);
	$service_record_dashboard = implode(',',$_POST['service_record_dashboard']);

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configinvid) AS configinvid FROM field_config_equipment WHERE tab='service_record'"));
	if($get_field_config['configinvid'] > 0) {
		$query_update_employee = "UPDATE `field_config_equipment` SET equipment = '$service_record', equipment_dashboard = '$service_record_dashboard' WHERE tab='service_record'";
		$result_update_employee = mysqli_query($dbc, $query_update_employee);
	} else {
		$query_insert_config = "INSERT INTO `field_config_equipment` (`tab`, `equipment`, `equipment_dashboard`) VALUES ('service_record', '$service_record', '$service_record_dashboard')";
		$result_insert_config = mysqli_query($dbc, $query_insert_config);
	}

	echo '<script type="text/javascript"> window.location.replace("field_config_equipment.php?type=service_record"); </script>';
}

if (isset($_POST['equip_assign_btn'])) {
	$equip_assign_fields = filter_var(implode(',',$_POST['equip_assign_fields']),FILTER_SANITIZE_STRING);
	set_config($dbc, 'equipment_equip_assign_fields', $equip_assign_fields);

	echo '<script type="text/javascript"> window.location.replace("field_config_equipment.php?type=equip_assign"); </script>';
}

?>
<script type="text/javascript">
$(document).ready(function() {
	$("#tab_dashboard").change(function() {
		window.location = '?settings=dashboard&tab='+this.value;
	});
	$("#tab_field").change(function() {
		window.location = '?settings=field&tab='+this.value;
	});
	$("#acc").change(function() {
		var tabs = $("#tab_field").val();
		window.location = '?settings=field&tab='+tabs+'&accr='+this.value;
	});

	$("#tab_inspect").change(function() {
		window.location = '?settings=inspection&tab='+$(this).val();
	});
});
</script>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<?php
	$invtype = $_GET['tab'];
	$accr = $_GET['accr'];
	$type = $_GET['type'];

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment FROM field_config_equipment WHERE tab='$invtype' AND accordion='$accr'"));
	$equipment_config = ','.$get_field_config['equipment'].',';

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment_dashboard FROM field_config_equipment WHERE tab='$invtype' AND equipment_dashboard IS NOT NULL"));
	$equipment_dashboard_config = ','.$get_field_config['equipment_dashboard'].',';

	$get_field_order = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT GROUP_CONCAT(`order` SEPARATOR ',') AS all_order FROM field_config_equipment WHERE tab='$invtype'"));

	if($_GET['type'] == 'tab') {
		$equipment_main_tabs = explode(',',get_config($dbc,'equipment_main_tabs')); ?>
		<div class="panel-group" id="accordion2">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_tabs" >
							Equipment Tabs<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_tabs" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group">
							<label for="fax_number"	class="col-sm-4	control-label">Select Equipment Tabs:</label>
							<div class="col-sm-8">
								<label class="form-checkbox"><input type="checkbox" <?= (in_array('Equipment', $equipment_main_tabs) ? 'checked' : '') ?> name="equipment_main_tabs[]" value="Equipment"> Equipment Lists</label>
								<label class="form-checkbox"><input type="checkbox" <?= (in_array('Inspection', $equipment_main_tabs) ? 'checked' : '') ?> name="equipment_main_tabs[]" value="Inspection"> Inspection Checklist</label>
								<label class="form-checkbox"><input type="checkbox" <?= (in_array('Assign', $equipment_main_tabs) ? 'checked' : '') ?> name="equipment_main_tabs[]" value="Assign"> Assign Equipment</label>
								<label class="form-checkbox"><input type="checkbox" <?= (in_array('Work Order', $equipment_main_tabs) ? 'checked' : '') ?> name="equipment_main_tabs[]" value="Work Order"> Work Orders</label>
								<label class="form-checkbox"><input type="checkbox" <?= (in_array('Expenses', $equipment_main_tabs) ? 'checked' : '') ?> name="equipment_main_tabs[]" value="Expenses"> Expenses &amp; Balance Sheets</label>
								<label class="form-checkbox"><input type="checkbox" <?= (in_array('Schedules', $equipment_main_tabs) ? 'checked' : '') ?> name="equipment_main_tabs[]" value="Schedules"> Service Schedules</label>
								<label class="form-checkbox"><input type="checkbox" <?= (in_array('Requests', $equipment_main_tabs) ? 'checked' : '') ?> name="equipment_main_tabs[]" value="Requests"> Service Requests</label>
								<label class="form-checkbox"><input type="checkbox" <?= (in_array('Records', $equipment_main_tabs) ? 'checked' : '') ?> name="equipment_main_tabs[]" value="Records"> Service Records</label>
								<label class="form-checkbox"><input type="checkbox" <?= (in_array('Checklists', $equipment_main_tabs) ? 'checked' : '') ?> name="equipment_main_tabs[]" value="Checklists"> Checklists</label>
								<label class="form-checkbox"><input type="checkbox" <?= (in_array('Equipment Assignment', $equipment_main_tabs) ? 'checked' : '') ?> name="equipment_main_tabs[]" value="Equipment Assignment"> Equipment Assignment</label>
							</div>
						</div>
						<div class="form-group">
							<label for="fax_number"	class="col-sm-4	control-label">Add Equipment Tabs:<br /><em>Separate the tabs by commas. These will display on the dashboard as tabs to separate the Tabs of Equipment.</em></label>
							<div class="col-sm-8">
								<input name="equipment_tabs" type="text" value="<?php echo get_config($dbc, 'equipment_tabs'); ?>" class="form-control"/>
							</div>
						</div>
						<div class="form-group">
							<label for="fax_number" class="col-sm-4 control-label">Use Tab Drop Down Menu:</label>
							<div class="col-sm-8">
							<?php
							$checked = '';
							$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_category_dropdown_equipment'"));
							if($get_config['configid'] > 0) {
								$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='show_category_dropdown_equipment'"));
								if($get_config['value'] == '1') {
									$checked = 'checked';
								}
							}
							?>
				              <input type='checkbox' style='width:20px; height:20px;' <?php echo $checked; ?>  name='show_category_dropdown_equipment' class='show_category_dropdown_equipment' value='1'>
				            </div>
						</div>
						<div class="form-group">
							<label for="fax_number"	class="col-sm-4	control-label">Volume Units:<br /><em>Enter the units by which volume is measured. This will affect the entire software, including equipment volume, if in use.</em></label>
							<div class="col-sm-8">
								<input name="volume_units" type="text" value="<?= get_config($dbc, 'volume_units') ?>" class="form-control"/>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_reminder" >
							Reminder Emails<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_reminder" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group">
							<label for="fax_number"	class="col-sm-4	control-label">Sending Email Address:<br /><em>Leaving this blank will use the current user's email address.</em></label>
							<div class="col-sm-8">
								<input name="equipment_remind_sender" type="text" value="<?php echo get_config($dbc, 'equipment_remind_sender'); ?>" class="form-control"/>
							</div>
						</div>
						<div class="form-group">
							<label for="equipment_remind_admin"	class="col-sm-4	control-label">Recipient Email Address(es):<br /><em>Reminder emails will also be sent to these user(s), if selected.</em></label>
							<div class="col-sm-8">
								<select name="equipment_remind_admin[]" data-placeholder="Select Staff" multiple class="chosen-select-deselect">
                                    <?php
                                    $recipient   = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `recipient` FROM `reminders` WHERE `reminder_type`='Equipment Registration' OR `reminder_type`='Equipment Insurance'" ) );
                                    $staff      = explode ( '<br>', get_multiple_contact($dbc, $recipient['recipient']) );
                                    $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`,`last_name`,`first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `email_address`!='' AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
                                    foreach($staff_list as $staff_id) {
                                        $staff_name  = get_contact($dbc, $staff_id);
                                        $staff_email = get_email($dbc, $staff_id);
                                        $selected    = in_array ( $staff_name, $staff ) ? 'selected="selected"' : '';
                                        echo "<option value='$staff_id' $selected>$staff_name: $staff_email</option>\n";
                                    } ?>
                                </select>
							</div>
						</div>
						<div class="form-group">
							<label for="fax_number"	class="col-sm-4	control-label">Email Subject:</label>
							<div class="col-sm-8">
								<input name="equipment_remind_subject" type="text" value="<?php echo get_config($dbc, 'equipment_remind_subject'); ?>" class="form-control"/>
							</div>
						</div>
						<div class="form-group">
							<label for="fax_number"	class="col-sm-4	control-label">Email Body:<br /><em>Your email body will have a link to the specific equipment appended to it.</em></label>
							<div class="col-sm-8">
								<textarea name="equipment_remind_body" class="form-control"><?php echo html_entity_decode(get_config($dbc, 'equipment_remind_body')); ?></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php $assign_equip_region_field = get_config($dbc, 'assign_equip_region_field');
				$assign_equip_location_field = get_config($dbc, 'assign_equip_location_field'); ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_assign_equip" >
							Assigned Equipment Tab<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_assign_equip" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group">
							<label for="assign_equip_region_field" class="col-sm-4 control-label">Include Region:</label>
							<div class="col-sm-8">
								<input type='checkbox' style='width:20px; height:20px;' <?php echo $assign_equip_region_field == 1 ? 'checked' : ''; ?>  name='assign_equip_region_field' class='assign_equip_region_field' value='1'>
							</div>
						</div>
						<div class="form-group">
							<label for="assign_equip_location_field" class="col-sm-4 control-label">Include Location:</label>
							<div class="col-sm-8">
								<input type='checkbox' style='width:20px; height:20px;' <?php echo $assign_equip_location_field == 1 ? 'checked' : ''; ?>  name='assign_equip_location_field' class='assign_equip_location_field' value='1'>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_mass" >
							Mass Equipment Update<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_mass" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group">
							<label for="mass_update_field" class="col-sm-4 control-label">Tab to Update:</label>
							<div class="col-sm-8">
								<select name="mass_update_category" class="chosen-select-deselect" data-placeholder="Select a Tab"><option></option>
									<?php foreach(explode(',',get_config($dbc, 'equipment_tabs')) as $cat) { ?>
										<option value="<?= $cat ?>"><?= $cat ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="mass_update_field" class="col-sm-4 control-label">Field to Update:</label>
							<div class="col-sm-8">
								<select name="mass_update_field" class="chosen-select-deselect" data-placeholder="Select a Field"><option></option>
									<option value="make">Make</option>
									<option value="model">Model</option>
									<option value="cost">Cost</option>
									<option value="color">Colour</option>
									<option value="next_service_date">Next Service Date</option>
									<option value="status">Equipment Status</option>
									<option value="volume">Equipment Volume (<?= get_config($dbc, 'volume_units') ?>)</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="mass_update_value" class="col-sm-4 control-label">Update Value:</label>
							<div class="col-sm-8">
								<input type='text' name='mass_update_value' class='form-control' value=''>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-6">
				<a href="equipment.php?category=Top" class="btn brand-btn btn-lg">Back</a>
				<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
			</div>
			<div class="col-sm-6">
				<button	type="submit" name="add_tab" value="add_tab" class="btn brand-btn btn-lg pull-right">Submit</button>
			</div>
		</div>
	<?php }

	if($_GET['type'] == 'expenses') { ?>
		<script>
		$(document).ready(function() {
			$('.sortable').sortable({
			  connectWith: '.sortable',
			  items: 'label'
			});
		});
		</script>
		<style>
		.sortable label {
			background-color: RGBA(255,255,255,0.2);
			margin: 0.25em;
		}
		</style>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field2" >
						Choose Fields for Expenses<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_field2" class="panel-collapse collapse">
				<div class="panel-body">
					<div id='no-more-tables'>
					Move the fields around to change the display order.
					<div class='sortable' style='border:solid 1px black;'>
						<?php $equipment_expense_fields = explode(',',trim(get_config($dbc, 'equipment_expense_fields'),','));
						$equipment_expense_fields_arr = array_filter(array_unique(array_merge($equipment_expense_fields,explode(',','Description,Country,Province,Date,Receipt,Amount,HST,PST,GST,Total'))));
						foreach($equipment_expense_fields_arr as $field) {
							echo '<label class="form-checkbox"><input type="checkbox" '.(in_array($field,$equipment_expense_fields) ? 'checked' : '').' value="'.$field.'" name="equipment_expense_fields[]"> ';
							if($field == 'Description') {
								echo 'Description';
							} else if($field == 'Country') {
								echo 'Country of Expense';
							} else if($field == 'Province') {
								echo 'Province of Expense';
							} else if($field == 'Date') {
								echo 'Expense Date';
							} else if($field == 'Receipt') {
								echo 'Receipt';
							} else if($field == 'Amount') {
								echo 'Amount';
							} else if($field == 'HST') {
								echo 'HST';
							} else if($field == 'PST') {
								echo 'PST';
							} else if($field == 'GST') {
								echo 'GST';
							} else if($field == 'Total') {
								echo 'Total';
							}
							echo '</label>';
						}
						?>
					</div>
				   </div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-6">
				<a href="equipment.php?category=Top" class="btn brand-btn btn-lg">Back</a>
				<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
			</div>
			<div class="col-sm-6">
				<button	type="submit" name="expenses" value="expenses" class="btn brand-btn btn-lg pull-right">Submit</button>
			</div>
		</div>
	<?php }

	if($_GET['type'] == 'inspection') {
		$inspection_list = [];
		$inv_type = explode(',',$invtype);
		$i = 0;
		if($invtype != '') {
			$inspection_list = '';
			while($inpsection_list == '' && $i < count($inv_type)) {
				$inspection_list = mysqli_fetch_array(mysqli_query($dbc, "SELECT `inspection_list`, `tab` FROM `field_config_equipment` WHERE CONCAT(',',`tab`,',') LIKE '%,{$inv_type[$i++]},%' AND (`inspection_list` IS NOT NULL)"));
			}
			$inv_type = explode(',',$inspection_list['tab'].','.$invtype);
			$inspection_list = explode('#*#', $inspection_list['inspection_list']);
		} ?>
		<script>
		function add_inspection() {
			var counter = parseInt($('#checklist_counter').val());
			var newCounter = counter + 1;
			var last = $('div.add-inspection-row').last();
			var clone = last.clone();
			clone.find('input').val('').removeAttr('checked');
			clone.find('[name="inspection_list[' + counter + ']"]').attr("name", 'inspection_list[' + newCounter + ']');
			clone.find('[name="inspection_checklist[' + counter + ']"]').attr("name", 'inspection_checklist[' + newCounter + ']');
			clone.find('[name="inspection_details[' + counter + ']"]').attr("name", 'inspection_details[' + newCounter + ']');
			last.after(clone);
			$('#checklist_counter').val(newCounter);
		}
		</script>
		<div class="form-group">
			<label for="fax_number"	class="col-sm-4	control-label">Current Equipment Tabs:<br /><em>These are the tabs for which this Inspection List is currently applying. To change this list, remove tabs from the Inspection List accordion.</em></label>
			<div class="col-sm-8">
				<select data-placeholder="Select a Tab..." id="tab_inspect" name="tab_inspect_current[]" multiple class="chosen-select-deselect form-control" width="380">
					<?php $each_tab = explode(',', get_config($dbc, 'equipment_tabs'));
					foreach ($each_tab as $cat_tab) {
						echo "<option ".(in_array($cat_tab, $inv_type) ? 'selected' : '')." value='". $cat_tab."'>".$cat_tab.'</option>';
					} ?>
				</select>
			</div>
		</div>

		<div class="panel-group" id="accordion2">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_alerts" >
							Service Alerts<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_alerts" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group">
							<label for="fax_number"	class="col-sm-4	control-label">Service Alert Staff:<br /><em>Select all staff that should receive an alert when service is requested from the checklist.</em></label>
							<div class="col-sm-8">
								<select name="equipment_service_alert[]" data-placeholder="Select Staff..." multiple class="chosen-select-deselect form-control">
									<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `last_name`, `first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
									$selected = explode(',',get_config($dbc, 'equipment_service_alert'));
									foreach($staff_list as $id) {
										echo "<option ".(in_array($id,$selected) ? 'selected' : '')." value='$id'>".get_contact($dbc, $id)."</option>";
									} ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="fax_number"	class="col-sm-4	control-label">PDF Logo:</label>
							<div class="col-sm-8">
								<?php $filename = get_config($dbc, 'equipment_service_logo');
								if($filename != '') {
									echo "<a href='download/$filename'>View Logo</a><br />";
								} ?>
								<input type="file" name="equipment_service_logo" data-filename-placement="inside" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="fax_number"	class="col-sm-4	control-label">PDF Header:</label>
							<div class="col-sm-8">
								<textarea name="equipment_service_header"><?= get_config($dbc, 'equipment_service_header') ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="fax_number"	class="col-sm-4	control-label">PDF Footer:</label>
							<div class="col-sm-8">
								<textarea name="equipment_service_footer"><?= get_config($dbc, 'equipment_service_footer') ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="fax_number"	class="col-sm-4	control-label">Sending Email Name:<br /><em>Leaving this blank will use the current user's name.</em></label>
							<div class="col-sm-8">
								<input type="text" name="equipment_service_sender_name" class="form-control" value="<?= get_config($dbc, 'equipment_service_sender_name') ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="fax_number"	class="col-sm-4	control-label">Sending Email Address:<br /><em>Leaving this blank will use the current user's email address.</em></label>
							<div class="col-sm-8">
								<input type="text" name="equipment_service_sender" class="form-control" value="<?= get_config($dbc, 'equipment_service_sender') ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="fax_number"	class="col-sm-4	control-label">Email Subject:</label>
							<div class="col-sm-8">
								<input type="text" name="equipment_service_subject" class="form-control" value="<?= get_config($dbc, 'equipment_service_subject') ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="fax_number"	class="col-sm-4	control-label">Email Body:<br /><em>Your email body will have a link to the specific equipment appended to it.</em></label>
							<div class="col-sm-8">
								<textarea name="equipment_service_body"><?= get_config($dbc, 'equipment_service_body') ?></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_inspect" >
							Inspection Checklist<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_inspect" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group">
							<label for="fax_number"	class="col-sm-4	control-label">Equipment Tabs to which this checklist applies:</label>
							<div class="col-sm-8">
								<select data-placeholder="Select a Tab..." name="tab_inspect[]" multiple class="chosen-select-deselect form-control" width="380">
									<?php $each_tab = explode(',', get_config($dbc, 'equipment_tabs'));
									foreach ($each_tab as $cat_tab) {
										echo "<option ".(in_array($cat_tab, $inv_type) ? 'selected' : '')." value='". $cat_tab."'>".$cat_tab.'</option>';
									} ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="fax_number"	class="col-sm-4	control-label">Inspection Checklist:</label>
							<div class="col-sm-8">
								<?php if($invtype == '') {
									echo "Please select a tab before configuring the inspection.";
								} else { ?>
									<div class="form-group">
										<label class="col-sm-4 text-center">Inspection Name</label>
										<label class="col-sm-7 text-center">Custom Values (Comma separated)</label>
										<label class="col-sm-1 text-center">Details</label>
									</div>
								<?php
									$inspection_array = ["Oil","Coolant - Rad","Coolant Overflow","Hydraulic Oil","Hydraulic Oil - Leaks","Transmission Oil","Air Filters","Belts","Track SAG","Brake Emergency","Planetaries","Brake Pedal","Hydraulic Brake Fluid","Parking Brake","Defroster & Heaters","Emergency Equipment","Engine","Exhaust System","Fire Extinguisher","Fuel System","Generator/Alternator","Horn","Lights & Reflectors","Head - Stop Lights","Tail - Dash Lights","Blade","Bucket","Body Damage","Doors","Mirrors (Adjustment & Condition)","Oil Pressure","Radiator","Driver&#39;s Seat Belt & Seat Security","Cutting Edges","Ripper Teeth","Towing & Coupling Devices","Windshield & Windows","Windshield Washer & Wipers"];
									$counter = 0;
									foreach ($inspection_array as $row) {
										$custom_options = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equipment_inspection` WHERE `tab` = '".$invtype."' AND `inspection_name` = '$row'")); ?>
										<div class="form-group">
											<label class="col-sm-4"><input type="checkbox" <?= (in_array($row, $inspection_list) ? 'checked' : '') ?> name="inspection_list[<?= $counter ?>]" value="<?= $row ?>"><?= $row ?></label>
											<div class="col-sm-7"><input type="text" class="form-control" name="inspection_checklist[<?= $counter ?>]" value="<?= $custom_options['inspection_checklist'] ?>"></div>
											<label class="col-sm-1" style="text-align: center;"><input type="checkbox" <?= ($custom_options['inspection_details'] == 1 ? 'checked' : '') ?> name="inspection_details[<?= $counter ?>]" value="1"></label>
										</div>
										<?php
											$counter++;
										} ?>

									<h3>Additional Inspection Items</h3>
									<?php $inspection_custom = array_filter($inspection_list, function($value) { return $value != '' && !in_array($value, ["Oil","Coolant - Rad","Coolant Overflow","Hydraulic Oil","Hydraulic Oil - Leaks","Transmission Oil","Air Filters","Belts","Track SAG","Brake Emergency","Planetaries","Brake Pedal","Hydraulic Brake Fluid","Parking Brake","Defroster & Heaters","Emergency Equipment","Engine","Exhaust System","Fire Extinguisher","Fuel System","Generator/Alternator","Horn","Lights & Reflectors","Head - Stop Lights","Tail - Dash Lights","Blade","Bucket","Body Damage","Doors","Mirrors (Adjustment & Condition)","Oil Pressure","Radiator","Driver&#39;s Seat Belt & Seat Security","Cutting Edges","Ripper Teeth","Towing & Coupling Devices","Windshield & Windows","Windshield Washer & Wipers"]); });
									foreach($inspection_custom as $item) {
										$custom_options = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equipment_inspection` WHERE `tab` = '".$invtype."' AND `inspection_name` = '$item'"));
										echo "<div class='form-group'>";
										echo "<div class='col-sm-4'><input type='text' name='inspection_list[$counter]' class='form-control' value='$item'></div>";
										echo "<div class='col-sm-7'><input type='text' name='inspection_checklist[$counter]' class='form-control' value='".$custom_options['inspection_checklist']."'></div>";
										echo "<label class='col-sm-1' style='text-align: center;'><input ".($custom_options['inspection_details'] == 1 ? 'checked' : '')." type='checkbox' name='inspection_details[$counter]' value='1'></label>";
										echo "</div>";
										$counter++;
									} ?>
									<div class="form-group add-inspection-row">
										<div class="col-sm-4"><input type="text" name="inspection_list[<?= $counter ?>]" class="form-control"></div>
										<div class="col-sm-7"><input type="text" name="inspection_checklist[<?= $counter ?>]" class="form-control"></div>
										<label class="col-sm-1" style="text-align: center;"><input type="checkbox" name="inspection_details[<?= $counter ?>]" value="1"></label>
									</div>
									<button class="btn brand-btn" name="add_inspect" onclick="add_inspection(); return false;">Add Item</button>
									<input type="hidden" id="checklist_counter" value="<?= $counter ?>">
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-6">
				<a href="equipment.php?category=Top" class="btn brand-btn btn-lg">Back</a>
				<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
			</div>
			<div class="col-sm-6">
				<button	type="submit" name="inspection" value="inspection" class="btn brand-btn btn-lg pull-right">Submit</button>
			</div>
		</div>
	<?php }

	if($_GET['type'] == 'field') {
		?>
		<div class="form-group">
			<label for="fax_number"	class="col-sm-4	control-label">Tabs:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select a Tab..." id="tab_field" name="tab_field" class="chosen-select-deselect form-control" width="380">
					<option value=""></option>
					<?php
					$tabs = get_config($dbc, 'equipment_tabs');
					$each_tab = explode(',', $tabs);
					foreach ($each_tab as $cat_tab) {
						if ($invtype == $cat_tab) {
							$selected = 'selected="selected"';
						} else {
							$selected = '';
						}
						echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
					}
					?>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label for="fax_number"	class="col-sm-4	control-label">Accordion:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select Accordion..." id="acc" name="accordion" class="chosen-select-deselect form-control" width="380">
					<option value=""></option>

					<option <?php if ($accr == "Equipment Information") { echo " selected"; } ?> value="Equipment Information"><?php echo get_field_config_equipment($dbc, 'Equipment Information', 'order', $invtype); ?> : Equipment Information</option>
					<option <?php if ($accr == "Description") { echo " selected"; } ?> value="Description"><?php echo get_field_config_equipment($dbc, 'Description', 'order', $invtype); ?> : Description</option>
					<option <?php if ($accr == "Unique Identifier") { echo " selected"; } ?> value="Unique Identifier"><?php echo get_field_config_equipment($dbc, 'Unique Identifier', 'order', $invtype); ?> : Unique Identifier</option>
					<option <?php if ($accr == "Purchase Info") { echo " selected"; } ?> value="Purchase Info"><?php echo get_field_config_equipment($dbc, 'Purchase Info', 'order', $invtype); ?> : Purchase Info</option>
					<option <?php if ($accr == "Product Cost") { echo " selected"; } ?> value="Product Cost"><?php echo get_field_config_equipment($dbc, 'Product Cost', 'order', $invtype); ?> : Product Cost</option>
					<option <?php if ($accr == "Pricing") { echo " selected"; } ?> value="Pricing"><?php echo get_field_config_equipment($dbc, 'Pricing', 'order', $invtype); ?> : Pricing</option>
					<option <?php if ($accr == "Service & Alerts") { echo " selected"; } ?> value="Service & Alerts"><?php echo get_field_config_equipment($dbc, 'Service & Alerts', 'order', $invtype); ?> : Service & Alerts</option>
					<option <?php if ($accr == "Location") { echo " selected"; } ?> value="Location"><?php echo get_field_config_equipment($dbc, 'Location', 'order', $invtype); ?> : Location</option>
					<option <?php if ($accr == "Status") { echo " selected"; } ?> value="Status"><?php echo get_field_config_equipment($dbc, 'Status', 'order', $invtype); ?> : Status</option>
					<option <?php if ($accr == "Registration") { echo " selected"; } ?> value="Registration"><?php echo get_field_config_equipment($dbc, 'Registration', 'order', $invtype); ?> : Registration</option>
					<option <?php if ($accr == "Insurance") { echo " selected"; } ?> value="Insurance"><?php echo get_field_config_equipment($dbc, 'Insurance', 'order', $invtype); ?> : Insurance</option>
					<option <?php if ($accr == "Quote Description") { echo " selected"; } ?> value="Quote Description"><?php echo get_field_config_equipment($dbc, 'Quote Description', 'order', $invtype); ?> : Quote Description</option>
					<option <?php if ($accr == "General") { echo " selected"; } ?> value="General"><?php echo get_field_config_equipment($dbc, 'General', 'order', $invtype); ?> : General</option>
				</select>
				<select data-placeholder="Select Accordion Order..." name="order" class="chosen-select-deselect form-control" width="380">
					<option value=""></option>
					<?php
					for($m=1;$m<=30;$m++) { ?>
						<option <?php if (get_field_config_equipment($dbc, $accr, 'order', $invtype) == $m) { echo	'selected="selected"'; } else if (strpos(','.$get_field_order['all_order'].',', ','.$m.',') !== FALSE) { echo " disabled"; } ?> value="<?php echo $m;?>"><?php echo $m;?></option>
					<?php }
					?>
				</select>
			</div>
		</div>

		<h3>Fields</h3>
		<div class="panel-group" id="accordion2">

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_1" >
							Description<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_1" class="panel-collapse collapse">
					<div class="panel-body">

						<input type="checkbox" <?php if (strpos($equipment_config, ','."Description".',') !== FALSE) { echo " checked"; } ?> value="Description" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Description
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Category".',') !== FALSE) { echo " checked"; } ?> value="Category" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Tab
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Type".',') !== FALSE) { echo " checked"; } ?> value="Type" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Type
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Make".',') !== FALSE) { echo " checked"; } ?> value="Make" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Make
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Model".',') !== FALSE) { echo " checked"; } ?> value="Model" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Model
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Unit of Measure".',') !== FALSE) { echo " checked"; } ?> value="Unit of Measure" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Unit of Measure
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Model Year".',') !== FALSE) { echo " checked"; } ?> value="Model Year" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Model Year
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Style".',') !== FALSE) { echo " checked"; } ?> value="Style" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Style
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Vehicle Size".',') !== FALSE) { echo " checked"; } ?> value="Vehicle Size" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Vehicle Size
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Color".',') !== FALSE) { echo " checked"; } ?> value="Color" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Color
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Trim".',') !== FALSE) { echo " checked"; } ?> value="Trim" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Trim
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Fuel Type".',') !== FALSE) { echo " checked"; } ?> value="Fuel Type" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Fuel Tab
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Tire Type".',') !== FALSE) { echo " checked"; } ?> value="Tire Type" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Tire Tab
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Drive Train".',') !== FALSE) { echo " checked"; } ?> value="Drive Train" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Drive Train
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Total Kilometres".',') !== FALSE) { echo " checked"; } ?> value="Total Kilometres" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Total Kilometres
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Leased".',') !== FALSE) { echo " checked"; } ?> value="Leased" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Leased
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Vehicle Access Code".',') !== FALSE) { echo " checked"; } ?> value="Vehicle Access Code" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Vehicle Access Code
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Cargo".',') !== FALSE) { echo " checked"; } ?> value="Cargo" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Cargo
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Lessor".',') !== FALSE) { echo " checked"; } ?> value="Lessor" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Lessor
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Group".',') !== FALSE) { echo " checked"; } ?> value="Group" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Group
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Use".',') !== FALSE) { echo " checked"; } ?> value="Use" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Use
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Staff".',') !== FALSE) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Staff
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_2" >
							Unique Identifier<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_2" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_config, ','."Serial #".',') !== FALSE) { echo " checked"; } ?> value="Serial #" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Serial #
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Unit #".',') !== FALSE) { echo " checked"; } ?> value="Unit #" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Unit #
					<input type="checkbox" <?php if (strpos($equipment_config, ','."VIN #".',') !== FALSE) { echo " checked"; } ?> value="VIN #" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;VIN #
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Licence Plate".',') !== FALSE) { echo " checked"; } ?> value="Licence Plate" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Licence Plate
						<input type="checkbox" <?php if (strpos($equipment_config, ','."Label".',') !== FALSE) { echo " checked"; } ?> value="Label" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Equipment Label
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Nickname".',') !== FALSE) { echo " checked"; } ?> value="Nickname" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Nickname

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_3" >
							Purchase Info<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_3" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_config, ','."Year Purchased".',') !== FALSE) { echo " checked"; } ?> value="Year Purchased" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Year Purchased
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Mileage".',') !== FALSE) { echo " checked"; } ?> value="Mileage" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Mileage
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Hours Operated".',') !== FALSE) { echo " checked"; } ?> value="Hours Operated" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Hours Operated

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_4" >
							Product Cost<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_4" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_config, ','."Cost".',') !== FALSE) { echo " checked"; } ?> value="Cost" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Cost
					<input type="checkbox" <?php if (strpos($equipment_config, ','."CDN Cost Per Unit".',') !== FALSE) { echo " checked"; } ?> value="CDN Cost Per Unit" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;CDN Cost Per Unit
					<input type="checkbox" <?php if (strpos($equipment_config, ','."USD Cost Per Unit".',') !== FALSE) { echo " checked"; } ?> value="USD Cost Per Unit" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;USD Cost Per Unit
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Finance".',') !== FALSE) { echo " checked"; } ?> value="Finance" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Finance
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Lease".',') !== FALSE) { echo " checked"; } ?> value="Lease" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Lease
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Insurance".',') !== FALSE) { echo " checked"; } ?> value="Insurance" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Insurance

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_5" >
							Pricing<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_5" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_config, ','."Hourly Rate".',') !== FALSE) { echo " checked"; } ?> value="Hourly Rate" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Hourly Rate
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Daily Rate".',') !== FALSE) { echo " checked"; } ?> value="Daily Rate" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Daily Rate
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Semi-monthly Rate".',') !== FALSE) { echo " checked"; } ?> value="Semi-monthly Rate" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Semi-monthly Rate
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Monthly Rate".',') !== FALSE) { echo " checked"; } ?> value="Monthly Rate" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Monthly Rate
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Field Day Cost".',') !== FALSE) { echo " checked"; } ?> value="Field Day Cost" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Field Day Cost
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Field Day Billable".',') !== FALSE) { echo " checked"; } ?> value="Field Day Billable" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Field Day Billable
					<input type="checkbox" <?php if (strpos($equipment_config, ','."HR Rate Work".',') !== FALSE) { echo " checked"; } ?> value="HR Rate Work" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;HR Rate Work
					<input type="checkbox" <?php if (strpos($equipment_config, ','."HR Rate Travel".',') !== FALSE) { echo " checked"; } ?> value="HR Rate Travel" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;HR Rate Travel

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_6" >
							Service & Alerts<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_6" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Service Date".',') !== FALSE) { echo " checked"; } ?> value="Next Service Date" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Service Date
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Service Hours".',') !== FALSE) { echo " checked"; } ?> value="Next Service Hours" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Service Hours
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Service Description".',') !== FALSE) { echo " checked"; } ?> value="Next Service Description" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Service Description
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Service Location".',') !== FALSE) { echo " checked"; } ?> value="Service Location" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Service Location
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Last Oil Filter Change (date)".',') !== FALSE) { echo " checked"; } ?> value="Last Oil Filter Change (date)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Last Oil Filter Change (date)
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Last Oil Filter Change (km)".',') !== FALSE) { echo " checked"; } ?> value="Last Oil Filter Change (km)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Last Oil Filter Change (km)
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Oil Filter Change (date)".',') !== FALSE) { echo " checked"; } ?> value="Next Oil Filter Change (date)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Oil Filter Change (date)
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Oil Filter Change (km)".',') !== FALSE) { echo " checked"; } ?> value="Next Oil Filter Change (km)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Oil Filter Change (km)
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Last Inspection & Tune Up (date)".',') !== FALSE) { echo " checked"; } ?> value="Last Inspection & Tune Up (date)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Last Inspection & Tune Up (date)
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Last Inspection & Tune Up (km)".',') !== FALSE) { echo " checked"; } ?> value="Last Inspection & Tune Up (km)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Last Inspection & Tune Up (km)
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Inspection & Tune Up (date)".',') !== FALSE) { echo " checked"; } ?> value="Next Inspection & Tune Up (date)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Inspection & Tune Up (date)
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Inspection & Tune Up (km)".',') !== FALSE) { echo " checked"; } ?> value="Next Inspection & Tune Up (km)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Inspection & Tune Up (km)
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Tire Condition".',') !== FALSE) { echo " checked"; } ?> value="Tire Condition" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Tire Condition
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Last Tire Rotation (date)".',') !== FALSE) { echo " checked"; } ?> value="Last Tire Rotation (date)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Last Tire Rotation (date)
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Last Tire Rotation (km)".',') !== FALSE) { echo " checked"; } ?> value="Last Tire Rotation (km)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Last Tire Rotation (km)
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Tire Rotation (date)".',') !== FALSE) { echo " checked"; } ?> value="Next Tire Rotation (date)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Tire Rotation (date)
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Tire Rotation (km)".',') !== FALSE) { echo " checked"; } ?> value="Next Tire Rotation (km)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Tire Rotation (km)
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Registration Renewal date".',') !== FALSE) { echo " checked"; } ?> value="Registration Renewal date" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Registration Renewal date
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Insurance Renewal Date".',') !== FALSE) { echo " checked"; } ?> value="Insurance Renewal Date" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Insurance Renewal Date
					<input type="checkbox" <?php if (strpos($equipment_config, ','."CVIP Ticket Renewal Date".',') !== FALSE) { echo " checked"; } ?> value="CVIP Ticket Renewal Date" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;CVIP Ticket Renewal Date
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_7" >
							Location<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_7" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_config, ','."Classification Dropdown".',') !== FALSE) { echo " checked"; } ?> value="Classification Dropdown" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Classification Dropdown
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Location Dropdown".',') !== FALSE) { echo " checked"; } ?> value="Location Dropdown" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Location Dropdown
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Region Dropdown".',') !== FALSE) { echo " checked"; } ?> value="Region Dropdown" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Region Dropdown
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Location".',') !== FALSE) { echo " checked"; } ?> value="Location" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Location
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Current Address".',') !== FALSE) { echo " checked"; } ?> value="Current Address" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Current Address
					<input type="checkbox" <?php if (strpos($equipment_config, ','."LSD".',') !== FALSE) { echo " checked"; } ?> value="LSD" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;LSD

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_reg" >
							Registration Information<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_reg" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_config, ','."Registration Card".',') !== FALSE) { echo " checked"; } ?> value="Registration Card" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Registration Card
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Registration Renewal date".',') !== FALSE) { echo " checked"; } ?> value="Registration Renewal date" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Registration Renewal Date
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Registration Reminder".',') !== FALSE) { echo " checked"; } ?> value="Registration Reminder" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Registration Reminder

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_ins" >
							Insurance Information<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_ins" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_config, ','."Insurance Company".',') !== FALSE) { echo " checked"; } ?> value="Insurance Company" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Insurance Provider
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Insurance Contact".',') !== FALSE) { echo " checked"; } ?> value="Insurance Contact" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Insurance Contact Name
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Insurance Phone".',') !== FALSE) { echo " checked"; } ?> value="Insurance Phone" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Insurance Phone Number
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Insurance Card".',') !== FALSE) { echo " checked"; } ?> value="Insurance Card" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Insurance Card
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Insurance Renewal Date".',') !== FALSE) { echo " checked"; } ?> value="Insurance Renewal Date" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Insurance Renewal Date
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Insurance Reminder".',') !== FALSE) { echo " checked"; } ?> value="Insurance Reminder" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Insurance Reminder

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_8" >
							Status<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_8" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_config, ','."Status".',') !== FALSE) { echo " checked"; } ?> value="Status" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Status
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Ownership Status".',') !== FALSE) { echo " checked"; } ?> value="Ownership Status" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Ownership Status
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Assigned Status".',') !== FALSE) { echo " checked"; } ?> value="Assigned Status" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Assigned Status

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_9" >
							Quote Description<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_9" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_config, ','."Quote Description".',') !== FALSE) { echo " checked"; } ?> value="Quote Description" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Quote Description

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_10" >
							General<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_10" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_config, ','."Volume".',') !== FALSE) { echo " checked"; } ?> value="Volume" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Equipment Volume (<?= get_config($dbc, 'volume_units') ?>)
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Notes".',') !== FALSE) { echo " checked"; } ?> value="Notes" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Notes

					</div>
				</div>
			</div>

		</div>

		<div class="form-group">
			<div class="col-sm-6">
				<a href="equipment.php?category=Top" class="btn brand-btn btn-lg">Back</a>
				<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
			</div>
			<div class="col-sm-6">
				<button	type="submit" name="inv_field"	value="inv_field" class="btn brand-btn btn-lg	pull-right">Submit</button>
			</div>
		</div>

	<?php }
	?>

	<?php if($_GET['type'] == 'dashboard') { ?>
		<div class="form-group">
			<label for="fax_number"	class="col-sm-4	control-label">Tabs:</label>
			<div class="col-sm-8">
				<select data-placeholder="Choose a Vendor..." id="tab_dashboard" name="tab_dashboard" class="chosen-select-deselect form-control" width="380">
					<option value=""></option>
					<?php
					$tabs = get_config($dbc, 'equipment_tabs');
					$each_tab = explode(',', $tabs);
					foreach ($each_tab as $cat_tab) {
						if ($invtype == $cat_tab) {
							$selected = 'selected="selected"';
						} else {
							$selected = '';
						}
						echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
					}
					?>
				</select>
			</div>
		</div>

		<h3>Dashboard</h3>
		<div class="panel-group" id="accordion2">

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_1" >
							Description<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_1" class="panel-collapse collapse">
					<div class="panel-body">
						<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Description".',') !== FALSE) { echo " checked"; } ?> value="Description" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Description
						<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Category".',') !== FALSE) { echo " checked"; } ?> value="Category" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Tab
						<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Type".',') !== FALSE) { echo " checked"; } ?> value="Type" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Type
						<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Make".',') !== FALSE) { echo " checked"; } ?> value="Make" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Make
						<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Model".',') !== FALSE) { echo " checked"; } ?> value="Model" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Model
						<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Unit of Measure".',') !== FALSE) { echo " checked"; } ?> value="Unit of Measure" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Unit of Measure
						<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Model Year".',') !== FALSE) { echo " checked"; } ?> value="Model Year" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Model Year
						<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Style".',') !== FALSE) { echo " checked"; } ?> value="Style" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Style
						<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Vehicle Size".',') !== FALSE) { echo " checked"; } ?> value="Vehicle Size" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Vehicle Size
						<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Color".',') !== FALSE) { echo " checked"; } ?> value="Color" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Color
						<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Trim".',') !== FALSE) { echo " checked"; } ?> value="Trim" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Trim
						<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Fuel Type".',') !== FALSE) { echo " checked"; } ?> value="Fuel Type" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Fuel Tab
						<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Tire Type".',') !== FALSE) { echo " checked"; } ?> value="Tire Type" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Tire Tab
						<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Drive Train".',') !== FALSE) { echo " checked"; } ?> value="Drive Train" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Drive Train
						<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Total Kilometres".',') !== FALSE) { echo " checked"; } ?> value="Total Kilometres" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Total Kilometres
						<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ',Leased,') !== FALSE) { echo " checked"; } ?> value="Leased" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Leased
						<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ',Staff,') !== FALSE) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Staff

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_2" >
							Unique Identifier<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_2" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Serial #".',') !== FALSE) { echo " checked"; } ?> value="Serial #" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Serial #
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Unit #".',') !== FALSE) { echo " checked"; } ?> value="Unit #" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Unit #
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."VIN #".',') !== FALSE) { echo " checked"; } ?> value="VIN #" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;VIN #
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Licence Plate".',') !== FALSE) { echo " checked"; } ?> value="Licence Plate" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Licence Plate
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Label".',') !== FALSE) { echo " checked"; } ?> value="Label" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Label
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Nickname".',') !== FALSE) { echo " checked"; } ?> value="Nickname" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Nickname

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_3" >
							Purchase Info<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_3" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Year Purchased".',') !== FALSE) { echo " checked"; } ?> value="Year Purchased" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Year Purchased
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Mileage".',') !== FALSE) { echo " checked"; } ?> value="Mileage" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Mileage
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Hours Operated".',') !== FALSE) { echo " checked"; } ?> value="Hours Operated" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Hours Operated

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_4" >
							Product Cost<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_4" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Cost".',') !== FALSE) { echo " checked"; } ?> value="Cost" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Cost
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."CDN Cost Per Unit".',') !== FALSE) { echo " checked"; } ?> value="CDN Cost Per Unit" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;CDN Cost Per Unit
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."USD Cost Per Unit".',') !== FALSE) { echo " checked"; } ?> value="USD Cost Per Unit" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;USD Cost Per Unit
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Finance".',') !== FALSE) { echo " checked"; } ?> value="Finance" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Finance
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Lease".',') !== FALSE) { echo " checked"; } ?> value="Lease" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Lease
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Insurance".',') !== FALSE) { echo " checked"; } ?> value="Insurance" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Insurance

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_5" >
							Pricing<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_5" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Hourly Rate".',') !== FALSE) { echo " checked"; } ?> value="Hourly Rate" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Hourly Rate
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Daily Rate".',') !== FALSE) { echo " checked"; } ?> value="Daily Rate" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Daily Rate
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Semi-monthly Rate".',') !== FALSE) { echo " checked"; } ?> value="Semi-monthly Rate" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Semi-monthly Rate
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Monthly Rate".',') !== FALSE) { echo " checked"; } ?> value="Monthly Rate" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Monthly Rate
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Field Day Cost".',') !== FALSE) { echo " checked"; } ?> value="Field Day Cost" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Field Day Cost
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Field Day Billable".',') !== FALSE) { echo " checked"; } ?> value="Field Day Billable" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Field Day Billable
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."HR Rate Work".',') !== FALSE) { echo " checked"; } ?> value="HR Rate Work" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;HR Rate Work
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."HR Rate Travel".',') !== FALSE) { echo " checked"; } ?> value="HR Rate Travel" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;HR Rate Travel

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_6" >
							Service & Alerts<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_6" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Next Service Date".',') !== FALSE) { echo " checked"; } ?> value="Next Service Date" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Next Service Date
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Next Service Hours".',') !== FALSE) { echo " checked"; } ?> value="Next Service Hours" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Next Service Hours
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Next Service Description".',') !== FALSE) { echo " checked"; } ?> value="Next Service Description" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Next Service Description
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Service Location".',') !== FALSE) { echo " checked"; } ?> value="Service Location" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Service Location
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Last Oil Filter Change (date)".',') !== FALSE) { echo " checked"; } ?> value="Last Oil Filter Change (date)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Last Oil Filter Change (date)
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Last Oil Filter Change (km)".',') !== FALSE) { echo " checked"; } ?> value="Last Oil Filter Change (km)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Last Oil Filter Change (km)
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Next Oil Filter Change (date)".',') !== FALSE) { echo " checked"; } ?> value="Next Oil Filter Change (date)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Next Oil Filter Change (date)
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Next Oil Filter Change (km)".',') !== FALSE) { echo " checked"; } ?> value="Next Oil Filter Change (km)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Next Oil Filter Change (km)
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Last Inspection & Tune Up (date)".',') !== FALSE) { echo " checked"; } ?> value="Last Inspection & Tune Up (date)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Last Inspection & Tune Up (date)
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Last Inspection & Tune Up (km)".',') !== FALSE) { echo " checked"; } ?> value="Last Inspection & Tune Up (km)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Last Inspection & Tune Up (km)
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Next Inspection & Tune Up (date)".',') !== FALSE) { echo " checked"; } ?> value="Next Inspection & Tune Up (date)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Next Inspection & Tune Up (date)
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Next Inspection & Tune Up (km)".',') !== FALSE) { echo " checked"; } ?> value="Next Inspection & Tune Up (km)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Next Inspection & Tune Up (km)
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Tire Condition".',') !== FALSE) { echo " checked"; } ?> value="Tire Condition" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Tire Condition
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Last Tire Rotation (date)".',') !== FALSE) { echo " checked"; } ?> value="Last Tire Rotation (date)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Last Tire Rotation (date)
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Last Tire Rotation (km)".',') !== FALSE) { echo " checked"; } ?> value="Last Tire Rotation (km)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Last Tire Rotation (km)
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Next Tire Rotation (date)".',') !== FALSE) { echo " checked"; } ?> value="Next Tire Rotation (date)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Next Tire Rotation (date)
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Next Tire Rotation (km)".',') !== FALSE) { echo " checked"; } ?> value="Next Tire Rotation (km)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Next Tire Rotation (km)
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."CVIP Ticket Renewal Date".',') !== FALSE) { echo " checked"; } ?> value="CVIP Ticket Renewal Date" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;CVIP Ticket Renewal Date
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Service History Link".',') !== FALSE) { echo " checked"; } ?> value="Service History Link" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Service History Link

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_7" >
							Location<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_7" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Classification Dropdown".',') !== FALSE) { echo " checked"; } ?> value="Classification Dropdown" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Classification
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Location Dropdown".',') !== FALSE) { echo " checked"; } ?> value="Location Dropdown" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Location (From Dropdown)
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Region Dropdown".',') !== FALSE) { echo " checked"; } ?> value="Region Dropdown" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Region
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Location".',') !== FALSE) { echo " checked"; } ?> value="Location" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Location
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."LSD".',') !== FALSE) { echo " checked"; } ?> value="LSD" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;LSD

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_reg" >
							Registration Information<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_reg" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Registration Card".',') !== FALSE) { echo " checked"; } ?> value="Registration Card" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Registration Card
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Registration Renewal date".',') !== FALSE) { echo " checked"; } ?> value="Registration Renewal date" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Registration Renewal Date

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_ins" >
							Insurance Information<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_ins" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Insurance Company".',') !== FALSE) { echo " checked"; } ?> value="Insurance Company" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Insurance Provider
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Insurance Contact".',') !== FALSE) { echo " checked"; } ?> value="Insurance Contact" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Insurance Contact Name
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Insurance Phone".',') !== FALSE) { echo " checked"; } ?> value="Insurance Phone" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Insurance Phone Number
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Insurance Card".',') !== FALSE) { echo " checked"; } ?> value="Insurance Card" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Insurance Card
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Insurance Renewal Date".',') !== FALSE) { echo " checked"; } ?> value="Insurance Renewal Date" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Insurance Renewal Date

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_8" >
							Status<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_8" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Status".',') !== FALSE) { echo " checked"; } ?> value="Status" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Status
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Ownership Status".',') !== FALSE) { echo " checked"; } ?> value="Ownership Status" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Ownership Status
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Assigned Status".',') !== FALSE) { echo " checked"; } ?> value="Assigned Status" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Assigned Status

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_9" >
							Quote Description<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_9" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Quote Description".',') !== FALSE) { echo " checked"; } ?> value="Quote Description" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Quote Description

					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_10" >
							General<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_10" class="panel-collapse collapse">
					<div class="panel-body">

					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Notes".',') !== FALSE) { echo " checked"; } ?> value="Notes" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Notes

					</div>
				</div>
			</div>

		</div>

		<br>

		<div class="form-group">
			<div class="col-sm-6">
				<a href="equipment.php?category=Top" class="btn brand-btn btn-lg">Back</a>
				<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
			</div>
			<div class="col-sm-6">
				<button	type="submit" name="inv_dashboard"	value="inv_dashboard" class="btn brand-btn btn-lg	pull-right">Submit</button>
			</div>
		</div>

	<?php } ?>

	<?php if($_GET['type'] == 'service_request') { ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field" >
					Choose Fields for Service Request<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_field" class="panel-collapse collapse">
			<div class="panel-body" id="no-more-tables">
				<?php
				$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment FROM field_config_equipment WHERE tab='service_request'"));
				$value_config_equipment = ','.$get_field_config['equipment'].',';
				?>

				<table border='2' cellpadding='10' class='table'>
					<tr>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Equipment".',') !== FALSE) { echo " checked"; } ?> value="Equipment" style="height: 20px; width: 20px;" name="service_request[]">&nbsp;&nbsp;Equipment
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Service Record".',') !== FALSE) { echo " checked"; } ?> value="Service Record" style="height: 20px; width: 20px;" name="service_request[]">&nbsp;&nbsp;Service Record
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Defects".',') !== FALSE) { echo " checked"; } ?> value="Defects" style="height: 20px; width: 20px;" name="service_request[]">&nbsp;&nbsp;Defects
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Comment".',') !== FALSE) { echo " checked"; } ?> value="Comment" style="height: 20px; width: 20px;" name="service_request[]">&nbsp;&nbsp;Comment
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field1" >
					Choose Fields for Service Request Dashboard<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_field1" class="panel-collapse collapse">
			<div class="panel-body" id="no-more-tables">
				<?php
				$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment_dashboard FROM field_config_equipment WHERE tab='service_request'"));
				$value_config_equipment_dashboard = ','.$get_field_config['equipment_dashboard'].',';
				?>

				<table border='2' cellpadding='10' class='table'>
					<tr>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Equipment".',') !== FALSE) { echo " checked"; } ?> value="Equipment" style="height: 20px; width: 20px;" name="service_request_dashboard[]">&nbsp;&nbsp;Equipment
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Service Record".',') !== FALSE) { echo " checked"; } ?> value="Service Record" style="height: 20px; width: 20px;" name="service_request_dashboard[]">&nbsp;&nbsp;Service Record
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Defects".',') !== FALSE) { echo " checked"; } ?> value="Defects" style="height: 20px; width: 20px;" name="service_request_dashboard[]">&nbsp;&nbsp;Defects
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Comment".',') !== FALSE) { echo " checked"; } ?> value="Comment" style="height: 20px; width: 20px;" name="service_request_dashboard[]">&nbsp;&nbsp;Comment
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<div class="form-group double-gap-top">
		<div class="col-sm-6">
			<a href="equipment.php?category=Top" class="btn brand-btn btn-lg">Back</a>
			<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
		</div>
		<div class="col-sm-6">
			<button	type="submit" name="service_request_btn" value="service_request_btn" class="btn brand-btn btn-lg	pull-right">Submit</button>
		</div>
	</div>
	<?php } ?>

	<?php if($_GET['type'] == 'service_record') { ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field12" >
					Choose Fields for Service Record<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_field12" class="panel-collapse collapse">
			<div class="panel-body" id="no-more-tables">
				<?php
				$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment FROM field_config_equipment WHERE tab='service_record'"));
				$value_config_equipment = ','.$get_field_config['equipment'].',';
				?>

				<table border='2' cellpadding='10' class='table'>
					<tr>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Service Date".',') !== FALSE) { echo " checked"; } ?> value="Service Date" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Service Date
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Advised Service Date".',') !== FALSE) { echo " checked"; } ?> value="Advised Service Date" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Advised Service Date
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Equipment".',') !== FALSE) { echo " checked"; } ?> value="Equipment" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Equipment
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Inventory".',') !== FALSE) { echo " checked"; } ?> value="Inventory" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Inventory
						</td>

						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Description of Job".',') !== FALSE) { echo " checked"; } ?> value="Description of Job" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Description of Job
						</td>

						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Service Record Mileage".',') !== FALSE) { echo " checked"; } ?> value="Service Record Mileage" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Service Record Mileage
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Hours".',') !== FALSE) { echo " checked"; } ?> value="Hours" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Hours
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Completed".',') !== FALSE) { echo " checked"; } ?> value="Completed" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Completed
						</td>
					</tr>

					<tr>

						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Staff".',') !== FALSE) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Staff
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Vendor".',') !== FALSE) { echo " checked"; } ?> value="Vendor" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Vendor
						</td>

						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Service Record Cost".',') !== FALSE) { echo " checked"; } ?> value="Service Record Cost" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Service Record Cost
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Service Type".',') !== FALSE) { echo " checked"; } ?> value="Service Type" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Service Tab
						</td>

						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Kilometers".',') !== FALSE) { echo " checked"; } ?> value="Kilometers" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Kilometers
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Recommended Next Service Mileage".',') !== FALSE) { echo " checked"; } ?> value="Recommended Next Service Mileage" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Recommended Next Service Mileage
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Receipt/Document".',') !== FALSE) { echo " checked"; } ?> value="Receipt/Document" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Receipt/Document
						</td>

					</tr>

				</table>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field132" >
					Choose Fields for Service Record Dashboard<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_field132" class="panel-collapse collapse">
			<div class="panel-body" id="no-more-tables">
				<?php
				$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment_dashboard FROM field_config_equipment WHERE tab='service_record'"));
				$value_config_equipment_dashboard = ','.$get_field_config['equipment_dashboard'].',';
				?>

				<table border='2' cellpadding='10' class='table'>
					<tr>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Service Date".',') !== FALSE) { echo " checked"; } ?> value="Service Date" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Service Date
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Advised Service Date".',') !== FALSE) { echo " checked"; } ?> value="Advised Service Date" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Advised Service Date
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Equipment".',') !== FALSE) { echo " checked"; } ?> value="Equipment" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Equipment
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Inventory".',') !== FALSE) { echo " checked"; } ?> value="Inventory" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Inventory
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Description of Job".',') !== FALSE) { echo " checked"; } ?> value="Description of Job" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Description of Job
						</td>

						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Service Record Mileage".',') !== FALSE) { echo " checked"; } ?> value="Service Record Mileage" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Service Record Mileage
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Hours".',') !== FALSE) { echo " checked"; } ?> value="Hours" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Hours
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Completed".',') !== FALSE) { echo " checked"; } ?> value="Completed" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Completed
						</td>
					</tr>

					<tr>

						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Staff".',') !== FALSE) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Staff
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Vendor".',') !== FALSE) { echo " checked"; } ?> value="Vendor" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Vendor
						</td>

						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Service Record Cost".',') !== FALSE) { echo " checked"; } ?> value="Service Record Cost" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Service Record Cost
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Service Type".',') !== FALSE) { echo " checked"; } ?> value="Service Tab" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Service Type
						</td>
					</tr>

				</table>
			</div>
		</div>
	</div>

	<div class="form-group double-gap-top">
		<div class="col-sm-6">
			<a href="equipment.php?category=Top" class="btn brand-btn btn-lg">Back</a>
			<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
		</div>
		<div class="col-sm-6">
			<button	type="submit" name="service_record_btn" value="service_record_btn" class="btn brand-btn btn-lg	pull-right">Submit</button>
		</div>
	</div>
	<?php } ?>

	<?php if($_GET['type'] == 'equip_assign') { ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_equip_assign" >
					Choose Fields for Equipment Assignment<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_equip_assign" class="panel-collapse collapse">
			<div class="panel-body" id="no-more-tables">
				<?php $equip_assign_fields = ','.get_config($dbc, 'equipment_equip_assign_fields').',';
				$client_type = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"))['client_type']; ?>

				<table border='2' cellpadding='10' class='table'>
					<tr>
						<td>
							<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."equipment_assignmentid".',') !== FALSE) { echo " checked"; } ?> value="equipment_assignmentid" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;Equipment Assignment #
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."client".',') !== FALSE) { echo " checked"; } ?> value="client" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;<?= empty($client_type) ? 'Customer' : $client_type ?>
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."staff".',') !== FALSE) { echo " checked"; } ?> value="staff" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;Staff
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."contractors".',') !== FALSE) { echo " checked"; } ?> value="contractors" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;Contractors
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."region".',') !== FALSE) { echo " checked"; } ?> value="region" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;Region
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."location".',') !== FALSE) { echo " checked"; } ?> value="location" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;Location
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."classification".',') !== FALSE) { echo " checked"; } ?> value="classification" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;Classification
						</td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."start_date".',') !== FALSE) { echo " checked"; } ?> value="start_date" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;Start Date
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."end_date".',') !== FALSE) { echo " checked"; } ?> value="end_date" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;End Date
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."notes".',') !== FALSE) { echo " checked"; } ?> value="notes" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;Notes
						</td>
					</tr>

				</table>
			</div>
		</div>
	</div>

	<div class="form-group double-gap-top">
		<div class="col-sm-6">
			<a href="equipment.php?category=Top" class="btn brand-btn btn-lg">Back</a>
			<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
		</div>
		<div class="col-sm-6">
			<button	type="submit" name="equip_assign_btn" value="equip_assign_btn" class="btn brand-btn btn-lg	pull-right">Submit</button>
		</div>
	</div>
	<?php } ?>


	<?php if($_GET['type'] == 'service_record') { ?>


	<?php } ?>

	<?php if($_GET['type'] == 'classification') { ?>
		<div id="accordion2">
			<?php foreach(array_filter(array_unique(explode(',',get_config($dbc, '%_classification', true, ',')))) as $i => $classification) { ?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_class_<?= $i ?>" >
								Equipment Classification Details: <?= $classification ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_class_<?= $i ?>" class="panel-collapse collapse">
						<div class="panel-body" id="no-more-tables">
							<h3>Default Start Address</h3>
							<?php $address = json_decode(html_entity_decode(get_config($dbc, 'equip_class_'.config_safe_str($classification).'_address_start')),true); ?>
							<input type="hidden" name="class[]" value="<?= config_safe_str($classification) ?>">
							<div class="form-group">
								<label class="col-sm-4 control-label">Address:</label>
								<div class="col-sm-8">
									<input type="text" name="address[]" value="<?= $address['address'] ?>" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Address 2:</label>
								<div class="col-sm-8">
									<input type="text" name="address2[]" value="<?= $address['address2'] ?>" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">City / Town:</label>
								<div class="col-sm-8">
									<input type="text" name="city[]" value="<?= $address['city'] ?>" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Province:</label>
								<div class="col-sm-8">
									<input type="text" name="province[]" value="<?= $address['province'] ?>" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Postal Code:</label>
								<div class="col-sm-8">
									<input type="text" name="postal_code[]" value="<?= $address['postal_code'] ?>" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Country:</label>
								<div class="col-sm-8">
									<input type="text" name="country[]" value="<?= $address['country'] ?>" class="form-control">
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
		<div class="form-group double-gap-top">
			<div class="col-sm-6">
				<a href="equipment.php?category=Top" class="btn brand-btn btn-lg">Back</a>
				<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
			</div>
			<div class="col-sm-6">
				<button	type="submit" name="equip_class_details" value="submit" class="btn brand-btn btn-lg	pull-right">Submit</button>
			</div>
		</div>
	<?php } ?>
</form>
