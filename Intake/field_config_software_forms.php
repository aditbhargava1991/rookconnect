<?php include_once('../include.php');
checkAuthorised('intake');
if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit']) ) {
	$existing_forms = [];
	foreach ($_POST['intakeformid'] as $i => $intakeformid) {
		$user_form_id = filter_var($_POST['user_form_id'][$i],FILTER_SANITIZE_STRING);
		$form_category = filter_var($_POST['form_cat'][$i],FILTER_SANITIZE_STRING);
		$form_name = filter_var($_POST['form_name'][$i],FILTER_SANITIZE_STRING);
		$expiry_date = filter_var($_POST['expiry_date'][$i],FILTER_SANITIZE_STRING);
		if(empty($intakeformid)) {
			mysqli_query($dbc, "INSERT INTO `intake_forms` (`user_form_id`, `category`, `form_name`, `expiry_date`) VALUES ('$user_form_id', '$form_category', '$form_name', '$expiry_date')");
			$intakeformid = mysqli_insert_id($dbc);
			$before_change = "";
      $history = "Intake Form entry has been added. <br />";
	    add_update_history($dbc, 'intake_history', $history, '', $before_change);
		} else {
			mysqli_query($dbc, "UPDATE `intake_forms` SET `user_form_id` = '$user_form_id', `category` = '$form_category', `form_name` = '$form_name', `expiry_date` = '$expiry_date' WHERE `intakeformid` = '$intakeformid'");
			$before_change = "";
			$history = "Intake form entry has been updated for User form id - $user_form_id. <br />";
			add_update_history($dbc, 'intake_history', $history, '', $before_change);
		}
		$existing_forms[] = $intakeformid;
	}
        $date_of_archival = date('Y-m-d');

	$existing_forms = implode(',', $existing_forms);
	$before_change = "";
	mysqli_query($dbc, "UPDATE `intake_forms` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `intakeformid` NOT IN ($existing_forms)");
	$history = "Intake form entry has been updated. <br />";
	add_update_history($dbc, 'intake_history', $history, '', $before_change);

	echo '<script type="text/javascript"> window.location.replace("field_config.php?tab=software_forms");</script>';
}
?>

<script>
	function add_form() {
		destroyInputs('.intake_form_block');
		var block = $('.intake_form_block').last();
		var clone = block.clone();
		clone.find('.form-control').val('');
		clone.find('select').trigger('change.select2');
		block.after(clone);
		initInputs('.intake_form_block');
	}
	function remove_form(img) {
		if($('.intake_form_block').length <= 1) {
			add_form();
		}
		$(img).closest('.intake_form_block').remove();
	}
</script>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

	<div class="form-group">
		<h4>Configure Intake Forms</h4>
		<?php $form_categories = explode('*#*',get_config($dbc, 'intake_software_tabs'));
		$form_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `intake_forms` WHERE `deleted` = 0"),MYSQLI_ASSOC);
		if(empty($form_types)) {
			$form_types[] = '';
		}
		foreach ($form_types as $form_type) { ?>
			<div class="intake_form_block">
				<input type="hidden" name="intakeformid[]" value="<?= $form_type['intakeformid'] ?>" class="form-control">
				<div class="form-group">
					<label class="col-sm-4 control-label">Form:</label>
					<div class="col-sm-8">
						<select name="user_form_id[]" data-placeholder="Select a Form..." class="chosen-select-deselect form-control">
							<optioN></optioN>
							<?php $forms_list = mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE CONCAT(',', `assigned_tile`, ',') LIKE '%,intake,%' AND `deleted` = 0");
							while ($row = mysqli_fetch_array($forms_list)) {
								echo '<option value="'.$row['form_id'].'" '.($row['form_id'] == $form_type['user_form_id'] ? 'selected' : '').'>'.$row['name'].'</option>';
							} ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Category:<br><em>These are configured from the Form Categories tab.</em></label>
					<div class="col-sm-8">
						<select name="form_cat[]" data-placeholder="Select a Category" class="chosen-select-deselect form-control">
							<option></option>
							<?php foreach($form_categories as $form_cat) { ?>
								<option value="<?= $form_cat ?>" <?= $form_cat == $form_type['category'] ? 'selected' : '' ?>><?= $form_cat ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Form Name:</label>
					<div class="col-sm-8">
						<input type="text" name="form_name[]" class="form-control" value="<?= $form_type['form_name'] ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Expiry Date:</label>
					<div class="col-sm-8">
						<input type="text" name="expiry_date[]" class="form-control datepicker" value="<?= $form_type['expiry_date'] ?>">
					</div>
				</div>
				<div class="form-group">
					<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_form();">
					<img src="../img/remove.png" class="inline-img pull-right" onclick="remove_form(this);">
				</div>
			</div>
			<hr>
		<?php } ?>
	</div>

	<div class="form-group pull-right">
		<a href="intake.php" class="btn brand-btn">Back</a>
		<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
		<button	type="submit" name="submit"	value="Submit" class="btn brand-btn">Submit</button>
	</div>

</form>
