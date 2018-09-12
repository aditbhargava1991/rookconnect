<?php if (isset($_POST['submit'])) {
	$quick_action_icons = filter_var(implode(',', $_POST['quick_action_icons']),FILTER_SANITIZE_STRING);
	set_config($dbc, 'inc_rep_quick_action_icons', $quick_action_icons);

    echo '<script type="text/javascript"> window.location.replace(""); </script>';
} ?>

<?php $quick_action_icons = explode(',',get_config($dbc, 'inc_rep_quick_action_icons')); ?>
<div class="form-group">
	<label class="col-sm-4 control-label">Quick Action Icons</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('tagging',$quick_action_icons) ? 'checked' : '' ?> value="tagging"> <img class="inline-img" src="../img/icons/tagging.png"> Tagging</label>
		<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('hide_all',$quick_action_icons) ? 'checked' : '' ?> value="hide_all" onclick="$('[name^=quick_action_icons]').not('[value=hide_all]').removeAttr('checked');"> Disable All</label>
	</div>
</div>