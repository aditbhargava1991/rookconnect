<script>
$(document).ready(function() {
	$('input').off('change',saveGroups).change(saveGroups);
});
function saveGroups() {
	var quick_action_icons = []; var flag_colours = []; var flag_name = [];
	$('[name="quick_action_icons[]"]:checked').each(function() {
		quick_action_icons.push(this.value);
	});
	$('[name="flag_colours[]"]:checked').each(function() {
		flag_colours.push(this.value);
	});
	$('[name="flag_name[]"]').each(function() {
		flag_name.push(this.value);
	});
	$.ajax({
		url: '../Equipment/equipment_ajax.php?fill=quick_action_settings',
		method: 'POST',
		data: {
			quick_action_icons: quick_action_icons.join(','),
			flag_colours: flag_colours.join(','),
			flag_name: flag_name.join('#*#')
		}
	});
}
</script>
<?php $quick_action_icons = explode(',',get_config($dbc, 'equipment_quick_action_icons'));
$get_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equipment`"));
$flag_colours = $get_config['flag_colours'];
$flag_names = explode('#*#', $get_config['flag_names']);
?>
<div class="form-group">
	<label class="col-sm-4 control-label">Quick Action Icons</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('preview',$quick_action_icons) ? 'checked' : '' ?> value="preview"> <img class="inline-img" src="../img/icons/ROOK-edit-icon.png"> View</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('edit',$quick_action_icons) ? 'checked' : '' ?> value="edit"> <img class="inline-img" src="../img/icons/ROOK-edit-icon.png"> Edit</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('favorite',$quick_action_icons) ? 'checked' : '' ?> value="favorite"> <img class="inline-img" src="../img/icons/ROOK-star-icon.png">Favorite </label>

		<label class="form-checkbox" onClick="$('[name^=quick_action_icons][value=flag_manual]').removeAttr('checked');" ><input type="checkbox" name="quick_action_icons[]" <?= !in_array('flag_manual',$quick_action_icons) && in_array('flag',$quick_action_icons) ? 'checked' : '' ?> value="flag"> <img class="inline-img" src="../img/icons/ROOK-flag-icon.png"> Flag</label>
		<label onClick="$('[name^=quick_action_icons][value=flag]').removeAttr('checked');" class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('flag_manual',$quick_action_icons) ? 'checked' : '' ?> value="flag_manual"> <img class="inline-img" src="../img/icons/ROOK-flag-icon.png"> Manually Flag with Label</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('reminder',$quick_action_icons) ? 'checked' : '' ?> value="reminder"> <img class="inline-img" src="../img/icons/ROOK-reminder-icon.png"> Reminders</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('archive',$quick_action_icons) ? 'checked' : '' ?> value="archive"> <img class="inline-img" src="../img/icons/ROOK-trash-icon.png"> Archive</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('hide_all',$quick_action_icons) ? 'checked' : '' ?> value="hide_all" onclick="$('[name^=quick_action_icons]').not('[value=hide_all]').removeAttr('checked');"> Disable All</label>
	</div>
</div>
<div class="form-group">
	<label for="file[]" class="col-sm-4 control-label">Flag Colours to Use<span class="popover-examples list-inline">&nbsp;
	<a  data-toggle="tooltip" data-placement="top" title="The selected colours will be cycled through when you flag an entry."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
	</span>:</label>
	<div class="col-sm-8">
		<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'FF6060') !== false ? 'checked' : ''); ?> value="FF6060" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
		<div style="border: 1px solid black; border-radius: 0.25em; background-color: #FF6060; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
		<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[0]; ?>" class="form-control"></div><div class="clearfix"></div>
		<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'DEBAA6') !== false ? 'checked' : ''); ?> value="DEBAA6" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
		<div style="border: 1px solid black; border-radius: 0.25em; background-color: #DEBAA6; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
		<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[1]; ?>" class="form-control"></div><div class="clearfix"></div>
		<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'FFAEC9') !== false ? 'checked' : ''); ?> value="FFAEC9" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
		<div style="border: 1px solid black; border-radius: 0.25em; background-color: #FFAEC9; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
		<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[2]; ?>" class="form-control"></div><div class="clearfix"></div>
		<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'FFC90E') !== false ? 'checked' : ''); ?> value="FFC90E" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
		<div style="border: 1px solid black; border-radius: 0.25em; background-color: #FFC90E; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
		<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[3]; ?>" class="form-control"></div><div class="clearfix"></div>
		<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'EFE4B0') !== false ? 'checked' : ''); ?> value="EFE4B0" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
		<div style="border: 1px solid black; border-radius: 0.25em; background-color: #EFE4B0; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
		<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[4]; ?>" class="form-control"></div><div class="clearfix"></div>
		<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'B5E61D') !== false ? 'checked' : ''); ?> value="B5E61D" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
		<div style="border: 1px solid black; border-radius: 0.25em; background-color: #B5E61D; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
		<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[5]; ?>" class="form-control"></div><div class="clearfix"></div>
		<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, '99D9EA') !== false ? 'checked' : ''); ?> value="99D9EA" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
		<div style="border: 1px solid black; border-radius: 0.25em; background-color: #99D9EA; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
		<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[6]; ?>" class="form-control"></div><div class="clearfix"></div>
		<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'D0E1F7') !== false ? 'checked' : ''); ?> value="D0E1F7" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
		<div style="border: 1px solid black; border-radius: 0.25em; background-color: #D0E1F7; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
		<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[7]; ?>" class="form-control"></div><div class="clearfix"></div>
		<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'C8BFE7') !== false ? 'checked' : ''); ?> value="C8BFE7" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
		<div style="border: 1px solid black; border-radius: 0.25em; background-color: #C8BFE7; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
		<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[8]; ?>" class="form-control"></div><div class="clearfix"></div>
	</div>
</div>