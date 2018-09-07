<?php error_reporting(0);
include_once('../include.php');
checkAuthorised('estimate');
$estimate_types = explode(',',get_config($dbc, 'project_tabs')); ?>
<script>
$(document).ready(function() {
	$('input').change(saveType);
	$('.main-screen').sortable({
		handle: '.drag-handle',
		items: '.type-option',
		update: saveType
	});
});
function saveType() {
	var type_list = [];
	$('[name="estimate_type[]"]').each(function() {
		this.value = this.value.replace(',','');
		type_list.push(this.value);
	});
	$.ajax({
		url: 'estimates_ajax.php?action=setting_types',
		method: 'POST',
		data: {
			types: type_list
		}
	});
}
function addType() {
	var clone = $('.type-option').last().clone();
	clone.find('input').val('');
	$('.type-option').last().after(clone);

	$('input').off('change', saveType).change(saveType);
	$('[name="estimate_type[]"]').last().focus();
}
function removeType(a) {
	if($('.type-option').length <= 1) {
		addType();
	}
	$(a).closest('.type-option').remove();
	saveType();
}
</script>
<h3><?= rtrim(ESTIMATE_TILE, 's') ?> Types</h3>
<label class="col-sm-10 text-center hide-titles-mob">Type Name (cannot contain commas)</label>
<label class="col-sm-2 hide-titles-mob"></label>
<?php foreach($estimate_types as $type) { ?>
	<div class="form-group type-option">
		<div class="col-sm-10">
			<label class="show-on-mob">Type Name (cannot contain commas):</label>
			<input type="text" name="estimate_type[]" class="form-control" value="<?= $type ?>">
		</div>
		<div class="col-sm-2">
			<img src="../img/icons/drag_handle.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right drag-handle no-toggle" title="Drag">
			<img src="../img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right" onclick="addType();">
			<img src="../img/remove.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right" onclick="removeType(this);">
		</div>
		<div class="clearfix"></div>
	</div>
<?php } ?>
<button onclick="addType(); return false;" class="btn brand-btn pull-right">Add Type</button>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_types.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>