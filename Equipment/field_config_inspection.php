<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');

if (isset($_POST['inspection'])) {
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

	echo '<script type="text/javascript"> window.location.replace("?settings=inspection"); </script>';
}
?>
<script type="text/javascript">
$(document).ready(function() {
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
		<label for="fax_number"	class="col-sm-4	control-label">Current Equipment Tabs:<br /><em>These are the tab for which this Inspection List is currently applying. To change this list, remove tabs from the Inspection List accordion.</em></label>
		<div class="col-sm-8">
			<select data-placeholder="Select a Tab..." id="tab_inspect" name="tab_inspect_current[]" multiple class="chosen-select-deselect form-control" width="380">
				<?php $each_tab = explode(',', get_config($dbc, 'equipment_tabs'));
				foreach ($each_tab as $cat_tab) {
					echo "<option ".(in_array($cat_tab, $inv_type) ? 'selected' : '')." value='". $cat_tab."'>".$cat_tab.'</option>';
				} ?>
			</select>
		</div>
	</div>

	<hr>

	<h4>Service Alerts</h4>
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

	<hr>

	<h4>Inspection Checklist</h4>
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

	<hr>

	<div class="form-group pull-right">
		<a href="?category=Top" class="btn brand-btn">Back</a>
		<button	type="submit" name="inspection" value="inspection" class="btn brand-btn">Submit</button>
	</div>
</form>