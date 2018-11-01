<?php include_once('../include.php');
checkAuthorised('hr');
$hr_summary = explode(',',get_config($dbc, 'hr_summary')); ?>
<script>
$(document).ready(function() {
	$('input').change(saveTypes);
});
function saveTypes() {
	var summaries = [];
	$('[name=hr_summary]:checked').each(function() {
		summaries.push(this.value);
	});
	$.ajax({
		url: 'hr_ajax.php?action=settings_config',
		method: 'POST',
		data: {
			name: 'hr_summary',
			value: summaries.join(',')
		}
	});
}
</script>
<div class="standard-body-title">
    <h3>Summary Dashboards</h3>
</div>
<div class="standard-body-content">
    <div class="form-group">
        <label class="col-sm-4">Summaries to Display:</label>
        <div class="col-sm-8">
            <label class="form-checkbox"><input type="checkbox" name="hr_summary" <?= in_array('individual_fave', $hr_summary) ? 'checked' : '' ?> value="individual_fave"> Individual: Favourites</label>
            <label class="form-checkbox"><input type="checkbox" name="hr_summary" <?= in_array('individual_pin', $hr_summary) ? 'checked' : '' ?> value="individual_pin"> Individual: Pinned</label>
            <label class="form-checkbox"><input type="checkbox" name="hr_summary" <?= in_array('individual', $hr_summary) ? 'checked' : '' ?> value="individual"> Individual: Progress Summary</label>
            <label class="form-checkbox"><input type="checkbox" name="hr_summary" <?= in_array('admin_recent', $hr_summary) ? 'checked' : '' ?> value="admin_recent"> Admin: Recent Forms</label>
            <label class="form-checkbox"><input type="checkbox" name="hr_summary" <?= in_array('admin_progress', $hr_summary) ? 'checked' : '' ?> value="admin_progress"> Admin: Individuals Summary</label>
        </div>
    </div>
</div>