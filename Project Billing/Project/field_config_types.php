<?php error_reporting(0);
include_once('../include.php');
$project_tabs = explode(',',get_config($dbc, 'project_tabs'));
$type_codes = explode(',',get_config($dbc, 'project_type_codes'));
$type_color = explode(',',get_config($dbc, 'project_type_color'));
?>
<script>
$(document).ready(function() {
	$('input').change(saveTypes);
    $('[name="project_color[]"]').change(saveTypes);
	$('.main-screen').sortable({
		handle: '.drag-handle',
		items: '.type-option',
		update: saveTypes
	});
});
function saveTypes() {
	var type_list = [];
	var summarized = [];
	$('[name="project_tabs[]"]').each(function() {
		type_list.push(this.value);
		if($(this).closest('.form-group').find('input[type=checkbox]:visible').is(':checked')) {
			summarized.push(this.value);
		}
	});
	var code_list = [];
	$('[name="project_codes[]"]').each(function() {
		code_list.push(this.value);
	});

	var color_list = [];
	$('[name="project_color[]"]').each(function() {
        color_list.push(this.value);
	});

    var hide_tiles = ($('[name=project_type_tiles]').is(':checked') ? '' : 'HIDE');
	$.ajax({
		url: 'projects_ajax.php?action=setting_types',
		method: 'POST',
		data: {
			types: type_list,
			codes: code_list,
            color: color_list,
			tiles: hide_tiles
		}
	});
}
function addType() {
	var clone = $('.type-option').last().clone();
	clone.find('input').val('').removeAttr('checked');
	$('.type-option').last().after(clone);

	$('input').off('change').change(saveTypes);
    $('[name="project_color[]"]').change(saveTypes);
	$('[name="project_tabs[]"]').last().focus();
    saveTypes();
}
function removeType(a) {
	if($('.type-option').length <= 1) {
		addType();
	}
	$(a).closest('.type-option').remove();
	saveTypes();
}
</script>
<h3><?= PROJECT_NOUN ?> Types</h3>
<div class="hide-titles-mob">
	<label class="col-sm-4"><?= PROJECT_NOUN ?> Type</label>
	<label class="col-sm-4">Type Code</label>
    <label class="col-sm-1">Colour Code</label>
    <label class="col-sm-1">Action</label>
</div>
<?php $k = 0; ?>
<?php foreach($project_tabs as $i => $type) { ?>
	<div class="form-group type-option">
		<div class="col-sm-4">
			<label class="show-on-mob"><?= PROJECT_NOUN ?> Type:</label>
			<input type="text" name="project_tabs[]" class="form-control" value="<?= $type ?>">
		</div>
		<div class="col-sm-4">
			<label class="show-on-mob">Type Code:</label>
			<input type="text" name="project_codes[]" class="form-control" value="<?= $type_codes[$i] ?>">
		</div>
		<div class="col-sm-1">
			<label class="show-on-mob">Colour Code:</label>
                <input type="color" name="project_color[]" class="form-control" value="<?= $type_color[$i] ?>">

            <!-- <select name="project_color[]" class="form-control">
                <option value=""></option>
                <option <?php if ($type_color[$k] == "Red") { echo " selected"; } ?> value="Red" style="background:Red">Red</option>
                <option <?php if ($type_color[$k] == "Yellow") { echo " selected"; } ?> value="Yellow" style="background:Yellow">Yellow</option>
                <option <?php if ($type_color == "Purple") { echo " selected"; } ?> value="Purple" style="background:Purple">Purple</option>
                <option <?php if ($type_color[$k] == "AntiqueWhite") { echo " selected"; } ?> value="AntiqueWhite" style="background:AntiqueWhite ">AntizqueWhite </option>
                <option <?php if ($type_color[$k] == "Aquamarine") { echo " selected"; } ?> value="Aquamarine" style="background:Aquamarine ">Aquamarine </option>
                <option <?php if ($type_color[$k] == "Aqua") { echo " selected"; } ?> value="Aqua" style="background:Aqua">Aqua </option>
                <option <?php if ($type_color[$k] == "Blue") { echo " selected"; } ?> value="Blue" style="background:Blue">Blue</option>
                <option <?php if ($type_color[$k] == "BlueViolet") { echo " selected"; } ?> value="BlueViolet" style="background:BlueViolet">BlueViolet</option>
                <option <?php if ($type_color[$k] == "Brown") { echo " selected"; } ?> value="Brown" style="background:Brown">Brown</option>
                <option <?php if ($type_color[$k] == "CadetBlue") { echo " selected"; } ?> value="CadetBlue" style="background:CadetBlue">CadetBlue</option>
                <option <?php if ($type_color[$k] == "Chartreuse") { echo " selected"; } ?> value="Chartreuse" style="background:Chartreuse">Chartreuse</option>
                <option <?php if ($type_color[$k] == "Coral") { echo " selected"; } ?> value="Coral" style="background:Coral">Coral</option>
                <option <?php if ($type_color[$k] == "DarkMagenta") { echo " selected"; } ?> value="DarkMagenta" style="background:DarkMagenta">DarkMagenta</option>
                <option <?php if ($type_color[$k] == "DarkSlateGray") { echo " selected"; } ?> value="DarkSlateGray" style="background:DarkSlateGray">DarkSlateGray </option>
                <option <?php if ($type_color[$k] == "DeepSkyBlue") { echo " selected"; } ?> value="DeepSkyBlue" style="background:DeepSkyBlue ">DeepSkyBlue </option>
            </select>
            -->

		</div>
		<div class="col-sm-1">
			<img src="../img/icons/drag_handle.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right drag-handle">
			<img src="../img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right" onclick="addType();">
			<img src="../img/remove.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right" onclick="removeType(this);">
		</div>
		<div class="clearfix"></div>
	</div>
<?php $k++; } ?>
<hr>
<label class="col-sm-4">Additional <?= PROJECT_NOUN ?> Tiles:</label>
<label class="form-checkbox"><input name="project_type_tiles" type="checkbox" value="" <?php echo (get_config($dbc, 'project_type_tiles') == 'HIDE' ? '' : 'checked'); ?>> Include <?= PROJECT_NOUN ?> Types on Menus</label>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_types.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>
