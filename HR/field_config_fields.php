<?php $field_config = explode(',',get_config($dbc, 'hr_fields')); ?>
<script>
$(document).ready(function() {
	$('input').change(saveFields);
});
function saveFields() {
	var field_list = [];
	$('[name=hr_fields]:checked').each(function() {
		field_list.push(this.value);
	});
	$.ajax({
		url: 'hr_ajax.php?action=settings_fields',
		method: 'POST',
		data: {
			fields: field_list
		}
	});
}
</script>
<div class="standard-body-title">
    <h1>Fields</h1>
</div>
<div class="standard-body-content">
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" checked disabled value="Category"> Category</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('First Name',$field_config) ? 'checked' : '' ?> value="First Name"> First Name</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Last Name',$field_config) ? 'checked' : '' ?> value="Last Name"> Last Name</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Birth Date',$field_config) ? 'checked' : '' ?> value="Birth Date"> Birth Date</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Employee Number',$field_config) ? 'checked' : '' ?> value="Employee Number"> Employee Number</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Address including Postal Code',$field_config) ? 'checked' : '' ?> value="Address including Postal Code"> Address including Postal Code</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Sub Section Heading',$field_config) ? 'checked' : '' ?> value="Sub Section Heading"> Sub Section Heading</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Third Tier Heading',$field_config) ? 'checked' : '' ?> value="Third Tier Heading"> Third Tier Heading</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Detail',$field_config) ? 'checked' : '' ?> value="Detail"> Detail</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Document',$field_config) ? 'checked' : '' ?> value="Document"> Document</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Link',$field_config) ? 'checked' : '' ?> value="Link"> Link</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Videos',$field_config) ? 'checked' : '' ?> value="Videos"> Videos</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Signature box',$field_config) ? 'checked' : '' ?> value="Signature box"> Signature box</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Comments',$field_config) ? 'checked' : '' ?> value="Comments"> Comments</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Staff',$field_config) ? 'checked' : '' ?> value="Staff"> Staff</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Review Deadline',$field_config) ? 'checked' : '' ?> value="Review Deadline"> Review Deadline</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Status',$field_config) ? 'checked' : '' ?> value="Status"> Status</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Configure Email',$field_config) ? 'checked' : '' ?> value="Configure Email"> Configure Email</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Form',$field_config) ? 'checked' : '' ?> value="Form"> Form</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Permissions by Position',$field_config) ? 'checked' : '' ?> value="Permissions by Position"> Permissions by Position</label>
    <label class="form-checkbox"><input type="checkbox" name="hr_fields" <?= in_array('Recurring Due Dates',$field_config) ? 'checked' : '' ?> value="Recurring Due Dates"> Recurring Due Dates</label>
</div>