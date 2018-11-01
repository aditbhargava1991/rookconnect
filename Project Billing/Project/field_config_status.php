<?php error_reporting(0);
include_once('../include.php');
$status_pending = get_config($dbc, 'project_status_pending');
$project_status = explode('#*#',get_config($dbc, 'project_status')); ?>
<script>
$(document).ready(function() {
	$('input').change(saveStatus);
	$('.main-screen').sortable({
		handle: '.drag-handle',
		items: '.status-option',
		update: saveStatus
	});
});
function saveStatus() {
	var status_list = [];
	var summarized = [];
	$('[name="project_status[]"]').each(function() {
		status_list.push(this.value);
		if($(this).closest('.form-group').find('input[type=checkbox]:visible').is(':checked')) {
			summarized.push(this.value);
		}
	});
	$.ajax({
		url: 'projects_ajax.php?action=setting_status',
		method: 'POST',
		data: {
			project_status_pending: $('[name=project_status_pending]:checked').val(),
			status: status_list,
			summary: summarized
		}
	});
}
function addStatus() {
	var clone = $('.status-option').last().clone();
	clone.find('input').val('').removeAttr('checked');
	$('.status-option').last().after(clone);
	
	$('input').off('change').change(saveStatus);
	$('[name="project_status[]"]').last().focus();
}
function removeStatus(a) {
	if($('.status-option').length <= 1) {
		addStatus();
	}
	$(a).closest('.status-option').remove();
	saveStatus();
}
</script>
<h3><?= PROJECT_TILE ?> Status</h3>
<div class="form-group status-option">
	<label class="col-sm-4">Pending Status:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="project_status_pending" <?= $status_pending == '' ? 'checked' : '' ?> class="form-control" value="">Use Pending Status</label>
		<label class="form-checkbox"><input type="radio" name="project_status_pending" <?= $status_pending == 'disable' ? 'checked' : '' ?> class="form-control" value="disable">Disable Pending Status</label>
	</div>
	<div class="clearfix"></div>
</div>
<?php foreach($project_status as $status) { ?>
	<div class="form-group status-option">
		<label class="col-sm-4">Status Name:</label>
		<div class="col-sm-7">
			<input type="text" name="project_status[]" class="form-control" value="<?= $status ?>">
		</div>
		<div class="col-sm-1">
			<img src="../img/icons/drag_handle.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right drag-handle">
			<img src="../img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right" onclick="addStatus();">
			<img src="../img/remove.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right" onclick="removeStatus(this);">
		</div>
		<div class="clearfix"></div>
	</div>
<?php } ?>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_status.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>