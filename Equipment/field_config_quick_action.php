<script>
$(document).ready(function() {
	$('input').off('change',saveGroups).change(saveGroups);
});
function saveGroups() {
	var quick_action_icons = [];
	$('[name="quick_action_icons[]"]:checked').each(function() {
		quick_action_icons.push(this.value);
	});
	$.ajax({
		url: '../Equipment/equipment_ajax.php?fill=quick_action_settings',
		method: 'POST',
		data: {
			quick_action_icons: quick_action_icons.join(',')
		}
	});
}
</script>
<?php $quick_action_icons = explode(',',get_config($dbc, 'quick_action_icons')); ?>
<div class="form-group">
	<label class="col-sm-4 control-label">Quick Action Icons</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('edit',$quick_action_icons) ? 'checked' : '' ?> value="edit"> <img class="inline-img" src="../img/icons/ROOK-edit-icon.png"> Edit</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('reminder',$quick_action_icons) ? 'checked' : '' ?> value="reminder"> <img class="inline-img" src="../img/icons/ROOK-reminder-icon.png"> Reminders</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('archive',$quick_action_icons) ? 'checked' : '' ?> value="archive"> <img class="inline-img" src="../img/icons/ROOK-trash-icon.png"> Archive</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('hide_all',$quick_action_icons) ? 'checked' : '' ?> value="hide_all" onclick="$('[name^=quick_action_icons]').not('[value=hide_all]').removeAttr('checked');"> Disable All</label>
	</div>
</div>