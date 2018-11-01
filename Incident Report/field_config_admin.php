<?php if (isset($_POST['submit'])) {
	//Administration Settings
	set_config($dbc, 'incident_report_admin_fields', filter_var(implode(',', array_filter($_POST['admin_fields'])),FILTER_SANITIZE_STRING));

	//Administration Access
	set_config($dbc, 'incident_report_admin_security', filter_var(implode(',', array_filter($_POST['admin_security'])),FILTER_SANITIZE_STRING));
	set_config($dbc, 'incident_report_admin_staff', filter_var(implode(',', array_filter($_POST['admin_staff'])),FILTER_SANITIZE_STRING));

	//Manager Approvals
	set_config($dbc, 'incident_report_manager_approvals', filter_var($_POST['manager_approvals']));
	set_config($dbc, 'incident_report_manager_approvals_tab', filter_var($_POST['manager_approvals_tab']));
	set_config($dbc, 'incident_report_manager_security', filter_var(implode(',', array_filter($_POST['manager_security'])),FILTER_SANITIZE_STRING));
	set_config($dbc, 'incident_report_manager_staff', filter_var(implode(',', array_filter($_POST['manager_staff'])),FILTER_SANITIZE_STRING));
} ?>
<script type="text/javascript">
function add_option(copy_class) {
	destroyInputs('.'+copy_class);
	var block = $('.'+copy_class).last();
	var clone = $(block).clone();

	clone.find('select').val('');

	$(block).after(clone);
	initInputs('.'+copy_class);
}
function remove_option(img, copy_class) {
	if($('.'+copy_class).length <= 1) {
		add_option(copy_class);
	}
	$(img).closest('.'+copy_class).remove();
}
</script>
<div class="gap-top">
	<?php $admin_fields = explode(',',get_config($dbc, 'incident_report_admin_fields')); ?>
	<h4>Administration Settings</h4>
	<div class="form-group">
		<label class="col-sm-4 control-label">Administration Fields:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" name="admin_fields[]" value="notes" <?= in_array('notes',$admin_fields) ? 'checked' : ''?>> Notes</label>
		</div>
	</div>
	<hr />

	<?php $admin_securitys = explode(',',get_config($dbc, 'incident_report_admin_security'));
	$admin_staffs = explode(',',get_config($dbc, 'incident_report_admin_staff')); ?>
	<h4>Administration Access</h4>
	<?php foreach($admin_securitys as $admin_security) { ?>
		<div class="form-group admin_security">
			<label for="tile_name" class="col-sm-4 control-label">By Security Level:</label>
			<div class="col-sm-7">
				<select name="admin_security[]" class="chosen-select-deselect" data-placeholder="Select Security Level">
					<option />
					<?php foreach(get_security_levels($dbc) as $sec_level_name => $sec_level) { ?>
						<option <?= $sec_level == $admin_security ? 'selected' : '' ?> value="<?= $sec_level ?>"><?= $sec_level_name ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-sm-1">
		        <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_option('admin_security');">
		        <img src="../img/remove.png" class="inline-img pull-right" onclick="remove_option(this, 'admin_security');">
			</div>
		</div>
	<?php } ?>
	<?php foreach($admin_staffs as $admin_staff) { ?>
		<div class="form-group admin_staff">
			<label for="tile_name" class="col-sm-4 control-label">By Staff:</label>
			<div class="col-sm-7">
				<select name="admin_staff[]" class="chosen-select-deselect" data-placeholder="Select Staff">
					<option />
					<?php foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `first_name`, `last_name`, `name` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `category` IN (".STAFF_CATS.")")) as $user) { ?>
						<option <?= $user['contactid'] == $admin_staff ? 'selected' : '' ?> value="<?= $user['contactid'] ?>"><?= $user['full_name'] ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-sm-1">
		        <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_option('admin_staff');">
		        <img src="../img/remove.png" class="inline-img pull-right" onclick="remove_option(this, 'admin_staff');">
			</div>
		</div>
	<?php } ?>
	<hr />
	
	<?php $manager_approvals = get_config($dbc, 'incident_report_manager_approvals');
	$manager_approvals_tab = !empty(get_config($dbc, 'incident_report_manager_approvals_tab')) ? get_config($dbc, 'incident_report_manager_approvals_tab') : 'Manager Approvals';
	$manager_securitys = explode(',',get_config($dbc, 'incident_report_manager_security'));
	$manager_staffs = explode(',',get_config($dbc, 'incident_report_manager_staff')); ?>
	<h4>Manager Approvals</h4>
	<div class="form-group">
		<label class="col-sm-4 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="This will require a Manager to approve a <?= INC_REP_NOUN ?> before going to the Administration."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
			</span> Enable Manager Approvals:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" name="manager_approvals" value="1" <?= $manager_approvals == 1 ? 'checked' : '' ?> onchange="if($(this).is(':checked')) { $('.manager_options').show(); } else { $('.manager_options').hide(); }"></label>
		</div>
	</div>
	<div class="manager_options" <?= $manager_approvals != 1 ? 'style="display:none;"' : '' ?>>
		<div class="form-group">
			<label class="control-label col-sm-4">Tab Name:</label>
			<div class="col-sm-8">
				<input type="text" name="manager_approvals_tab" class="form-control" value="<?= $manager_approvals_tab ?>">
			</div>
		</div>
		<?php foreach($manager_securitys as $manager_security) { ?>
			<div class="form-group manager_security">
				<label for="tile_name" class="col-sm-4 control-label">By Security Level:</label>
				<div class="col-sm-7">
					<select name="manager_security[]" class="chosen-select-deselect" data-placeholder="Select Security Level">
						<option />
						<?php foreach(get_security_levels($dbc) as $sec_level_name => $sec_level) { ?>
							<option <?= $sec_level == $manager_security ? 'selected' : '' ?> value="<?= $sec_level ?>"><?= $sec_level_name ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col-sm-1">
			        <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_option('manager_security');">
			        <img src="../img/remove.png" class="inline-img pull-right" onclick="remove_option(this, 'manager_security');">
				</div>
			</div>
		<?php } ?>
		<?php foreach($manager_staffs as $manager_staff) { ?>
			<div class="form-group manager_staff">
				<label for="tile_name" class="col-sm-4 control-label">By Staff:</label>
				<div class="col-sm-7">
					<select name="manager_staff[]" class="chosen-select-deselect" data-placeholder="Select Security Level">
						<option />
						<?php foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `first_name`, `last_name`, `name` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `category` IN (".STAFF_CATS.")")) as $user) { ?>
							<option <?= $user['contactid'] == $manager_staff ? 'selected' : '' ?> value="<?= $user['contactid'] ?>"><?= $user['full_name'] ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col-sm-1">
			        <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_option('manager_staff');">
			        <img src="../img/remove.png" class="inline-img pull-right" onclick="remove_option(this, 'manager_staff');">
				</div>
			</div>
		<?php } ?>
	</div>
</div>
