<?php error_reporting(0);
include_once('../include.php');
checkAuthorised('estimate');
$estimate_status = explode('#*#',get_config($dbc, 'estimate_status'));
$summarized = explode('#*#',get_config($dbc, 'estimate_summarize'));
$project_status = get_config($dbc, 'estimate_project_status'); ?>
<script>
$(document).ready(function() {
	$('input').change(saveStatus);
	$('.main-screen').sortable({
		handle: '.drag-handle',
		items: '.status-option',
		update: saveStatus
	});
    $('select[name="estimate_status_closed"]').change(function() {
        status = $(this).val();
        saveStatusForArchive('closed', status);
    });
    $('select[name="estimate_status_abandoned"]').change(function() {
        status = $(this).val();
        saveStatusForArchive('abandoned', status);
    });
});
function saveStatus() {
	var status_list = [];
	var summarized = [];
	var project_status = '';
	$('[name="estimate_status[]"]').each(function() {
		status_list.push(this.value);
		if($(this).closest('.form-group').find('input[type=checkbox]:visible').is(':checked')) {
			summarized.push(this.value);
		}
		if($(this).closest('.form-group').find('input[type=radio]:visible').is(':checked')) {
			project_status = this.value;
		}
	});
	$.ajax({
		url: 'estimates_ajax.php?action=setting_status',
		method: 'POST',
		data: {
			status: status_list,
			summary: summarized,
			projects: project_status
		}
	});
}
function addStatus() {
	var clone = $('.status-option').last().clone();
	clone.find('input').val('').removeAttr('checked');
	$('.status-option').last().after(clone);

	$('input').off('change', saveStatus).change(saveStatus);
	$('[name="estimate_status[]"]').last().focus();
}
function removeStatus(a) {
	if($('.status-option').length <= 1) {
		addStatus();
	}
	$(a).closest('.status-option').remove();
	saveStatus();
}
function saveStatusForArchive(status_type, status) {
    $.ajax({
		url: 'estimates_ajax.php?action=save_status_for_archive',
		method: 'POST',
		data: {
            status_type: status_type,
			status: status
		},
        success: function(response){
            console.log(response);
        }
	});
}
</script>
<h3><?= ESTIMATE_TILE ?> Status</h3>
<label class="col-sm-8 text-center hide-titles-mob">Status Name</label>
<label class="col-sm-1 hide-titles-mob">Use for <?= PROJECT_TILE ?></label>
<label class="col-sm-3 hide-titles-mob">Summarize on Dashboard</label>
<?php foreach($estimate_status as $status) { ?>
	<div class="form-group status-option">
		<div class="col-sm-8">
			<label class="show-on-mob">Status Name:</label>
			<input type="text" name="estimate_status[]" class="form-control" value="<?= $status ?>">
		</div>
		<div class="col-sm-1">
			<label class="form-checkbox hide-titles-mob"><input type="radio" <?= $status == $project_status ? 'checked="checked"' : '' ?> name="project_status_normal"></label>
			<label class="form-checkbox show-on-mob"><input type="radio" <?= $status == $project_status ? 'checked="checked"' : '' ?> name="project_status_mobile"> Once <?= PROJECT_TILE ?> created, use this status</label>
		</div>
		<div class="col-sm-3">
			<label><input type="checkbox" <?= in_array($status, $summarized) ? 'checked' : '' ?> name="summarize[]" class="hide-titles-mob"></label>
			<label class="form-checkbox show-on-mob"><input type="checkbox" <?= in_array($status, $summarized) ? 'checked' : '' ?> name="summarize[]"> Summarize on Dashboard</label>
			<img src="../img/icons/drag_handle.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right drag-handle no-toggle" title="Drag">
			<img src="../img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right" onclick="addStatus();">
			<img src="../img/remove.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right" onclick="removeStatus(this);">
		</div>
		<div class="clearfix"></div>
	</div>
<?php } ?>
<button onclick="addStatus(); return false;" class="btn brand-btn pull-right">Add Status</button>

<div class="clearfix"></div>

<div class="form-group double-gap-top double-gap-bottom">
    <label class="col-sm-4 control-label"><span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Select the Estimate Status that will be used for successfully closed estimates."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Estimate Closed Status:</label>
    <div class="col-sm-8"><?php
        $get_config_closed_status = get_config($dbc, 'estimate_status_closed'); ?>
        <select name="estimate_status_closed" class="form-control">
            <option value="">Select Status</option><?php
            foreach($estimate_status as $status):
                $selected = ($get_config_closed_status == $status) ? 'selected="selected"' : ''; ?>
                <option <?= $selected; ?> value="<?= $status; ?>"><?= $status; ?></option><?php
            endforeach; ?>
        </select>
    </div>
    
    <div class="clearfix"></div>
    
    <label class="col-sm-4 control-label"><span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Select the Estimate Status that will be used for abandonded estimates."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Estimate Abandoned Status:</label>
    <div class="col-sm-8"><?php
        $get_config_abandoned_status = get_config($dbc, 'estimate_status_abandoned'); ?>
        <select name="estimate_status_abandoned" class="form-control">
            <option value="">Select Status</option><?php
            foreach($estimate_status as $status):
                $selected = ($get_config_abandoned_status == $status) ? 'selected="selected"' : ''; ?>
                <option <?= $selected; ?> value="<?= $status; ?>"><?= $status; ?></option><?php
            endforeach; ?>
        </select>
    </div>
    
    <div class="clearfix"></div>
</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_status.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>