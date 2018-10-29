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
    
    // Set the Overview Tab option
	set_config($dbc, 'show_equipment_overview', $_POST['show_equipment_overview']);

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


	echo '<script type="text/javascript"> window.location.replace("?settings=tab"); </script>';
}
?>
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

	$equipment_main_tabs = explode(',',get_config($dbc,'equipment_main_tabs')); ?>
	<h4>Equipment Tabs</h4>
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
          <label class="form-checkbox"><input type='checkbox' <?php echo $checked; ?>  name='show_category_dropdown_equipment' class='show_category_dropdown_equipment' value='1'>Enable</label>
        </div>
	</div>
	<div class="form-group">
		<label class="col-sm-4">Show Equipment Overview:</label>
		<div class="col-sm-8">
            <label class="form-checkbox"><input type='checkbox' <?= get_config($dbc,'show_equipment_overview') > 0 ? 'checked' : '' ?>  name='show_equipment_overview' value='1'>Enable</label>
        </div>
	</div>
	<div class="form-group">
		<label for="fax_number"	class="col-sm-4	control-label">Volume Units:<br /><em>Enter the units by which volume is measured. This will affect the entire software, including equipment volume, if in use.</em></label>
		<div class="col-sm-8">
			<input name="volume_units" type="text" value="<?= get_config($dbc, 'volume_units') ?>" class="form-control"/>
		</div>
	</div>

	<hr>

	<h4>Reminder Emails</h4>
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

	<hr>

	<h4>Assigned Equipment Tab</h4>
	<?php $assign_equip_region_field = get_config($dbc, 'assign_equip_region_field');
		$assign_equip_location_field = get_config($dbc, 'assign_equip_location_field'); ?>
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

	<hr>

	<h4>Mass Equipment Update</h4>
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

	<hr>

	<div class="form-group pull-right">
		<a href="?category=Top" class="btn brand-btn">Back</a>
		<button	type="submit" name="add_tab" value="add_tab" class="btn brand-btn">Submit</button>
	</div>

</form>
