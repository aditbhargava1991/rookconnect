<?php include_once('../include.php');
/*
Software Styling
*/
if($_GET['subtab'] == 'software' && !check_subtab_persmission($dbc, 'software_config', ROLE, 'notifications_software')) {
	$_GET['subtab'] = '';
} ?>

<script type="text/javascript">
$(document).ready(function() {
	$('#dash_noti_options').find('input').change(saveConfig);
});
function saveConfig() {
	var enabled = $('[name="desktop_notification_enabled"]').val();
	if($('[name="desktop_notification_enabled"]').is(':checked')){
		enabled = '1';
	}else{
		enabled = '';
	}
	var contactid = '<?= $_SESSION['contactid'] ?>';
	var data = { enabled: enabled,contactid:contactid};
	$.ajax({
		url: '../Settings/settings_ajax.php?fill=desktop_notifications',
		method: 'POST',
		data: data,
		success: function(response) {

		}
	});
}
</script>

<div class="notice double-gap-bottom popover-examples">
    <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
    <div class="col-sm-11">
        <span class="notice-name">NOTE:</span>
        Enable/Disable Notification Alert setting will only be visible at user's end once it gets enabled from here.
    </div>
    <div class="clearfix"></div>
</div>

<div class="col-md-12" id="dash_noti_options">
	<?php
	$enb = get_config($dbc, 'desktop_notification_enabled');
	?>

	<div class="form-group">
		<label class="col-sm-4 control-label">Email Address:</label>
		<div class="col-sm-8"><?= get_email($dbc, $_SESSION['contactid']) ?></div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Desktop Notification Setting:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" name="desktop_notification_enabled" value="1" <?= $enb == 1 ? 'checked' : '' ?>> Enable</label>
		</div>
	</div>
	
</div>