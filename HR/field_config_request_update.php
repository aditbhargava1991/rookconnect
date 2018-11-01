<?php include_once('../include.php');
checkAuthorised('hr'); ?>
<script>
$(document).ready(function() {
	$('input,select').change(saveRequestUpdate);
});
function saveRequestUpdate() {
	var hr_request_update_security = [];
	$('[name="hr_request_update_security[]"]').each(function() {
		hr_request_update_security.push(this.value);
	});
	var hr_request_update_staff = [];
	$('[name="hr_request_update_staff[]"]').each(function() {
		hr_request_update_staff.push(this.value);
	});
	var hr_include_request_update = $('[name="hr_include_request_update"]:checked').val();
	$.ajax({
		url: 'hr_ajax.php?action=settings_request_update',
		method: 'POST',
		data: {
			hr_request_update_security: hr_request_update_security,
			hr_request_update_staff: hr_request_update_staff,
			hr_include_request_update: hr_include_request_update
		}
	});
}
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
<div class="standard-body-title">
    <h3>Request an Update</h3>
</div>
<div class="standard-body-content" style="padding: 0.5em;">
    <div class="form-group">
        <label class="col-sm-4 control-label">Include Request an Update Tab:</label>
        <div class="col-sm-8">
            <?php $hr_include_request_update = get_config($dbc, 'hr_include_request_update'); ?>
            <label class="form-checkbox"><input type="checkbox" name="hr_include_request_update" <?= $hr_include_request_update == 1 ? 'checked' : '' ?> value="1"></label>
        </div>
    </div>
    <?php $hr_request_update_security = explode(',',get_config($dbc, 'hr_request_update_security'));
    $hr_request_update_staff = explode(',',get_config($dbc, 'hr_request_update_staff'));
    foreach($hr_request_update_security as $request_security) { ?>
	    <div class="form-group request_security">
	        <label class="col-sm-4 control-label">Emails - By Security Level:</label>
	        <div class="col-sm-7">
				<select name="hr_request_update_security[]" class="chosen-select-deselect" data-placeholder="Select Security Level">
					<option />
					<?php foreach(get_security_levels($dbc) as $sec_level_name => $sec_level) { ?>
						<option <?= $sec_level == $request_security ? 'selected' : '' ?> value="<?= $sec_level ?>"><?= $sec_level_name ?></option>
					<?php } ?>
				</select>
	        </div>
			<div class="col-sm-1">
		        <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_option('request_security');">
		        <img src="../img/remove.png" class="inline-img pull-right" onclick="remove_option(this, 'request_security');">
			</div>
	    </div>
	<?php } ?>
    <?php foreach($hr_request_update_staff as $request_staff) { ?>
	    <div class="form-group request_staff">
	        <label class="col-sm-4 control-label">Emails - By Staff:</label>
	        <div class="col-sm-7">
				<select name="hr_request_update_staff[]" class="chosen-select-deselect" data-placeholder="Select Staff">
					<option />
					<?php foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `first_name`, `last_name`, `name` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `category` IN (".STAFF_CATS.")")) as $user) { ?>
						<option <?= $user['contactid'] == $request_staff ? 'selected' : '' ?> value="<?= $user['contactid'] ?>"><?= $user['full_name'] ?></option>
					<?php } ?>
				</select>
	        </div>
			<div class="col-sm-1">
		        <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_option('request_staff');">
		        <img src="../img/remove.png" class="inline-img pull-right" onclick="remove_option(this, 'request_staff');">
			</div>
	    </div>
	<?php } ?>
</div>