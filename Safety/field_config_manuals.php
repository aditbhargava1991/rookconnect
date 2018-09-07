<?php $manuals_fields = explode(',',get_config($dbc, 'safety_manuals_fields')); ?>
<script>
$(document).ready(function() {
	$('[name=safety_manuals_fields]').change(function() {
		fields = [];
		$('[name=safety_manuals_fields]:checked').each(function() { fields.push(this.value); });
		$.post('safety_ajax.php?action=settings_manuals', { fields: fields });
	});
});
</script>
<h3>Manuals Fields</h3>
<div class="block-group">
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Topic (Sub Tab)',$manuals_fields) ? ' checked' : '' ?> value="Topic (Sub Tab)" name="safety_manuals_fields"> Topic (Sub Tab)</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Section #',$manuals_fields) ? ' checked' : '' ?> value="Section #" name="safety_manuals_fields"> Section #</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Section Heading',$manuals_fields) ? ' checked' : '' ?> value="Section Heading" name="safety_manuals_fields"> Section Heading</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Sub Section #',$manuals_fields) ? ' checked' : '' ?> value="Sub Section #" name="safety_manuals_fields"> Sub Section #</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Sub Section Heading',$manuals_fields) ? ' checked' : '' ?> value="Sub Section Heading" name="safety_manuals_fields"> Sub Section Heading</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Third Tier Section #',$manuals_fields) ? ' checked' : '' ?> value="Third Tier Section #" name="safety_manuals_fields"> Third Tier Section #</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Third Tier Heading',$manuals_fields) ? ' checked' : '' ?> value="Third Tier Heading" name="safety_manuals_fields"> Third Tier Heading</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Document',$manuals_fields) ? ' checked' : '' ?> value="Document" name="safety_manuals_fields"> Document</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Link',$manuals_fields) ? ' checked' : '' ?> value="Link" name="safety_manuals_fields"> Link</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Videos',$manuals_fields) ? ' checked' : '' ?> value="Videos" name="safety_manuals_fields"> Videos</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Signature box',$manuals_fields) ? ' checked' : '' ?> value="Signature box" name="safety_manuals_fields"> Signature box</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Comments',$manuals_fields) ? ' checked' : '' ?> value="Comments" name="safety_manuals_fields"> Comments</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Staff',$manuals_fields) ? ' checked' : '' ?> value="Staff" name="safety_manuals_fields"> Staff</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Review Deadline',$manuals_fields) ? ' checked' : '' ?> value="Review Deadline" name="safety_manuals_fields"> Review Deadline</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Configure Email',$manuals_fields) ? ' checked' : '' ?> value="Configure Email" name="safety_manuals_fields"> Configure Email</label>
</div>