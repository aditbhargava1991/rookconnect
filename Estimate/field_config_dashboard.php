<?php error_reporting(0);
include_once('../include.php');
checkAuthorised('estimate');
$dashboard_length = get_config($dbc, 'estimate_dashboard_length');
$summary_view = explode(',',get_config($dbc, 'estimate_summary_view'));
$estimate_status = explode('#*#',get_config($dbc, 'estimate_status')); ?>
<script>
$(document).ready(function() {
	$('[name=dashboard_length],[name=summary_view]').change(saveStatus);
});
function saveStatus() {
	var summary = [];
	$('[name=summary_view]:checked').each(function() {
		summary.push(this.value);
	});
	$.ajax({
		url: 'estimates_ajax.php?action=setting_dashboard',
		method: 'POST',
		data: {
			length: $('[name=dashboard_length]').val(),
			summary: summary.join(',')
		}
	});
}
</script>
<h3>Dashboard Settings</h3>
<div class="form-group status-option">
	<label class="col-sm-4">Dashboard Length:</label>
	<div class="col-sm-8">
		<input type="number" name="dashboard_length" min=1 step=1 max=50 class="form-control" value="<?= $dashboard_length ?>">
	</div>
	<div class="clearfix"></div>
</div>
<div class="form-group status-option">
	<label class="col-sm-4">Summary Options:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Total Estimates', $summary_view) ? 'checked' : '' ?> name="summary_view" value="Total Estimates"> Total <?= ESTIMATE_TILE ?></label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Total Value', $summary_view) ? 'checked' : '' ?> name="summary_view" value="Total Value"> Total Value</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('6 Month Value', $summary_view) ? 'checked' : '' ?> name="summary_view" value="6 Month Value"> <?= ESTIMATE_TILE ?> Values (6 Month)</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Current Year Value', $summary_view) ? 'checked' : '' ?> name="summary_view" value="Current Year Value"> <?= ESTIMATE_TILE ?> Values Current Year</label>
		<!-- <label class="form-checkbox"><input type="checkbox" <?= in_array('Service Cat $', $summary_view) ? 'checked' : '' ?> name="summary_view" value="Service Cat $"> Estimates by Service Cat ($)</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Service Cat Hr', $summary_view) ? 'checked' : '' ?> name="summary_view" value="Service Cat Hr"> Estimates by Service Cat (Hrs)</label> -->
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Estimate Type $', $summary_view) ? 'checked' : '' ?> name="summary_view" value="Estimate Type $"> <?= rtrim(ESTIMATE_TILE, 's') ?> by Type ($)</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Estimate Type Count', $summary_view) ? 'checked' : '' ?> name="summary_view" value="Estimate Type Count"> <?= ESTIMATE_TILE ?> by Type Count</label>
		<!-- <label class="form-checkbox"><input type="checkbox" <?= in_array('Estimate Type Hr', $summary_view) ? 'checked' : '' ?> name="summary_view" value="Estimate Type Hr"> Estimates by Type (Hrs)</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Estimates Month', $summary_view) ? 'checked' : '' ?> name="summary_view" value="Estimates Month"> Estimates by Month</label> -->
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Revenue Won', $summary_view) ? 'checked' : '' ?> name="summary_view" value="Revenue Won"> Revenue Won & Lost</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Revenue Won by Type', $summary_view) ? 'checked' : '' ?> name="summary_view" value="Revenue Won by Type"> Revenue Won/Lost by Type</label>
		<!-- <label class="form-checkbox"><input type="checkbox" <?= in_array('Revenue Won by Cat', $summary_view) ? 'checked' : '' ?> name="summary_view" value="Revenue Won by Cat"> Revenue Won/Lost by Svc Cat</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Revenue per Hour', $summary_view) ? 'checked' : '' ?> name="summary_view" value="Revenue per Hour"> Average Revenue per Hour</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Avg Rev Won 6 Month', $summary_view) ? 'checked' : '' ?> name="summary_view" value="Avg Rev Won 6 Month"> Avg Revenue/Hr Won (6 Month)</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Avg Rev Won Service', $summary_view) ? 'checked' : '' ?> name="summary_view" value="Avg Rev Won Service"> Avg Revenue/Hr by Service</label> -->
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Closing Rate', $summary_view) ? 'checked' : '' ?> name="summary_view" value="Closing Rate"> Closing Rate</label>
		<!-- <label class="form-checkbox"><input type="checkbox" <?= in_array('Closing Rate Svc', $summary_view) ? 'checked' : '' ?> name="summary_view" value="Closing Rate Svc"> Closing Rate by Service</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Closing Rate 6 Month', $summary_view) ? 'checked' : '' ?> name="summary_view" value="Closing Rate 6 Month"> Closing Rate Last 6 Months</label> -->
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Average Complete', $summary_view) ? 'checked' : '' ?> name="summary_view" value="Average Complete"> Average Time to Complete</label>
		<?php foreach($estimate_status as $status) { ?>
			<label class="form-checkbox"><input type="checkbox" <?= in_array('Report Status '.$status, $summary_view) ? 'checked' : '' ?> name="summary_view" value="Report Status <?= $status ?>"> <?= $status ?> Details</label>
		<?php } ?>
	</div>
	<div class="clearfix"></div>
</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_dashboard.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>