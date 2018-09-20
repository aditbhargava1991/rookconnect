<?php if (isset($_POST['submit'])) {
	set_config($dbc, 'incident_report_admin_security', filter_var(implode(',', array_filter($_POST['admin_security'])),FILTER_SANITIZE_STRING));
	set_config($dbc, 'incident_report_admin_staff', filter_var(implode(',', array_filter($_POST['admin_staff'])),FILTER_SANITIZE_STRING));
}
$admin_securitys = explode(',',get_config($dbc, 'incident_report_admin_security'));
$admin_staffs = explode(',',get_config($dbc, 'incident_report_admin_staff'));
?>
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
	<?php foreach($admin_securitys as $admin_security) { ?>
		<div class="form-group admin_security">
			<label for="tile_name" class="col-sm-4 control-label">By Security Level:</label>
			<div class="col-sm-7">
				<select name="admin_security[]" class="chosen-select-deselect" data-placeholder="Select Security Level">
					<option />
					<?php foreach(get_security_levels($dbc) as $sec_level_name => $sec_level) { ?>
						<option <?= $sec_level_name == $admin_security ? 'selected' : '' ?> value="<?= $sec_level_name ?>"><?= $sec_level_name ?></option>
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
				<select name="admin_staff[]" class="chosen-select-deselect" data-placeholder="Select Security Level">
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
</div>