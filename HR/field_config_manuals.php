<?php $field_config = explode(',',get_config($dbc, 'hr_fields')); ?>
<script>
$(document).ready(function() {
	$('.block-group input,.block-group textarea').change(saveFields);
});
function saveFields() {
	$.ajax({
		url: 'hr_ajax.php?action=settings_config',
		method: 'POST',
		data: {
			name: this.name,
			value: this.value
		}
	});
}
</script>
<div class="standard-body-title">
    <h1>PDF Settings</h1>
</div>
<div class="standard-body-content">
	<div class="form-group">
		<label class="col-sm-4 control-label">PDF Header:</label>
		<div class="col-sm-8">
			<textarea name="manual_header"><?= html_entity_decode(get_config($dbc, "manual_header")) ?></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">PDF Footer:</label>
		<div class="col-sm-8">
			<textarea name="manual_footer"><?= html_entity_decode(get_config($dbc, "manual_footer")) ?></textarea>
		</div>
	</div>
	<h1>Email Settings</h1>
	<div class="form-group">
		<label class="col-sm-4 control-label">Submission Email Recipient:</label>
		<div class="col-sm-8">
			<input class="form-control" name="manual_completed_email" value="<?= !empty(get_config($dbc, "manual_completed_email")) ? get_config($dbc, "manual_completed_email") : EMAIL_ADDRESS ?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Submission Email Subject:</label>
		<div class="col-sm-8">
			<input class="form-control" name="manual_subject_completed" value="<?= get_config($dbc, "manual_subject_completed") ?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Submission Email Body:<br /><em>You can use [CATEGORY], [HEADING], [USER], [COMMENT]</em></label>
		<div class="col-sm-8">
			<textarea name="manual_body_completed"><?= html_entity_decode(get_config($dbc, "manual_body_completed")) ?></textarea>
		</div>
	</div>
</div>