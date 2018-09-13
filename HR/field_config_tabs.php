<?php include_once('../include.php');
checkAuthorised('hr');
$hr_tabs = explode(',',get_config($dbc, 'hr_tabs'));
$hr_tiles = explode(',',get_config($dbc, 'hr_tiles')); ?>
<script>
$(document).ready(function() {
	$('input').change(saveTypes);
	$('.main-screen').sortable({
		handle: '.drag-handle',
		items: '.type_option',
		update: saveTypes
	});
});
function saveTypes() {
	var old_type_list = [];
	var type_list = [];
	$('[name=hr_tabs]').each(function() {
		old_type_list.push($(this).data('category'));
		console.log($(this).data('category'));
		type_list.push(this.value);
		$(this).data('category', this.value);
	});
	var tile_list = [];
	$('[name=hr_tiles]:checked').each(function() {
		tile_list.push($(this).closest('.type_option').find('[type=text]').val());
	});
	var hr_include_profile = $('[name="hr_include_profile"]:checked').val();
	$.ajax({
		url: 'hr_ajax.php?action=settings_tabs',
		method: 'POST',
		data: {
			old_types: old_type_list,
			types: type_list,
			tiles: tile_list,
			hr_include_profile: hr_include_profile
		}
	});
}
function addType() {
	var clone = $('.type_option').last().clone();
	clone.find('input').val('').removeAttr('checked');
	clone.find('[name=hr_tabs]').attr('data-category', '');
	$('.type_option').last().after(clone);
	
	$('input').off('change',saveTypes).change(saveTypes);
}
function removeType(a) {
	if($('.type_option').length <= 1) {
		addType();
	}
	$(a).closest('.type_option').remove();
	saveTypes();
}
</script>
<div class="standard-body-title">
    <h3>HR Categories</h3>
</div>
<div class="standard-body-content">
    <?php foreach($hr_tabs as $type) { ?>
        <div class="form-group type_option">
            <label class="col-sm-2 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Your category cannot contain a comma."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span>Category:</label>
            <div class="col-sm-6">
                <input type="text" name="hr_tabs" class="form-control" data-category="<?= $type ?>" value="<?= $type ?>">
            </div>
            <div class="col-sm-2">
                <label class="form-checkbox-any"><input type="checkbox" name="hr_tiles" <?= in_array($type, $hr_tiles) ? 'checked' : '' ?>> Use as Tile</label>
            </div>
            <div class="col-sm-2">
                <img src="../img/icons/drag_handle.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right drag-handle">
                <img src="../img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right" onclick="addType();">
                <img src="../img/remove.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right" onclick="removeType(this);">
            </div>
            <div class="clearfix"></div>
        </div>
    <?php } ?>
    <hr>
    <div class="form-group">
        <label class="col-sm-4 control-label">Include Profile With Completion % As Subtab:</label>
        <div class="col-sm-8">
            <?php $hr_include_profile = get_config($dbc, 'hr_include_profile'); ?>
            <label class="form-checkbox"><input type="checkbox" name="hr_include_profile" <?= $hr_include_profile == 1 ? 'checked' : '' ?> value="1"></label>
        </div>
    </div>
    <div class="clearfix"></div>
</div>