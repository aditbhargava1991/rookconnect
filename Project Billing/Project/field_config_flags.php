<script>
$(document).ready(function() {
	$('input').off('change',saveGroups).change(saveGroups);
});
function saveGroups() {
	var flags = [];
	$('[name="flag_colours[]"]:checked').each(function() {
		flags.push(this.value);
	});
	var names = [];
	$('[name="flag_name[]"]').each(function() {
		names.push(this.value);
	});
	var quick_action_icons = [];
	$('[name="quick_action_icons[]"]:checked').each(function() {
		quick_action_icons.push(this.value);
	});
	$.ajax({
		url: 'projects_ajax.php?action=quick_action_settings',
		method: 'POST',
		data: {
			quick_action_icons: quick_action_icons.join(','),
			flags: flags.join(','),
			names: names.join('#*#')
		}
	});
}
</script>
<?php $quick_action_icons = explode(',',get_config($dbc, 'quick_action_icons')); ?>
<div class="form-group">
	<label class="col-sm-4 control-label">Quick Action Icons</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('edit',$quick_action_icons) ? 'checked' : '' ?> value="edit"> <img class="inline-img" src="../img/icons/ROOK-edit-icon.png"> Edit</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('sync',$quick_action_icons) ? 'checked' : '' ?> value="sync"> <img class="inline-img" src="../img/icons/ROOK-sync-icon.png"> Sync External Path</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= !in_array('flag_manual',$quick_action_icons) && in_array('flag',$quick_action_icons) ? 'checked' : '' ?> value="flag" onclick="$('[name^=quick_action_icons][value=flag_manual]').removeAttr('checked');"> <img class="inline-img" src="../img/icons/ROOK-flag-icon.png"> Flag</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('flag_manual',$quick_action_icons) ? 'checked' : '' ?> value="flag_manual" onclick="$('[name^=quick_action_icons][value=flag]').removeAttr('checked');"> <img class="inline-img" src="../img/icons/ROOK-flag-icon.png"> Manually Flag with Label</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('reply',$quick_action_icons) ? 'checked' : '' ?> value="reply"> <img class="inline-img" src="../img/icons/ROOK-reply-icon.png"> Reply</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('attach',$quick_action_icons) ? 'checked' : '' ?> value="attach"> <img class="inline-img" src="../img/icons/ROOK-attachment-icon.png"> Attach</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('alert',$quick_action_icons) ? 'checked' : '' ?> value="alert"> <img class="inline-img" src="../img/icons/ROOK-alert-icon.png"> Alerts</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('email',$quick_action_icons) ? 'checked' : '' ?> value="email"> <img class="inline-img" src="../img/icons/ROOK-email-icon.png"> Email</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('reminder',$quick_action_icons) ? 'checked' : '' ?> value="reminder"> <img class="inline-img" src="../img/icons/ROOK-reminder-icon.png"> Reminders</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('time',$quick_action_icons) ? 'checked' : '' ?> value="time"> <img class="inline-img" src="../img/icons/ROOK-timer-icon.png"> Add Time</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('timer',$quick_action_icons) ? 'checked' : '' ?> value="timer"> <img class="inline-img" src="../img/icons/ROOK-timer2-icon.png"> Track Time</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('archive',$quick_action_icons) ? 'checked' : '' ?> value="archive"> <img class="inline-img" src="../img/icons/ROOK-trash-icon.png"> Archive</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('hide_all',$quick_action_icons) ? 'checked' : '' ?> value="hide_all" onclick="$('[name^=quick_action_icons]').not('[value=hide_all]').removeAttr('checked');"> Disable All</label>
	</div>
</div>
<?php $flag_colours = get_config($dbc, 'ticket_colour_flags');
$flag_names = explode('#*#', get_config($dbc, 'ticket_colour_flag_names')); ?>
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