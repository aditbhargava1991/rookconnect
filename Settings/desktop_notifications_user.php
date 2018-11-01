<?php include_once('../include.php');
/*
Software Styling
*/
if($_GET['subtab'] == 'software' && !check_subtab_persmission($dbc, 'software_config', ROLE, 'notifications_software')) {
	$_GET['subtab'] = '';
} ?>

<script type="text/javascript">
$(document).ready(function() {
	$('#dash_noti_user_options').find('input').change(saveConfig);
});
function saveConfig() {
	var enabled = $('[name="desktop_notification_user_enabled"]').val();
	if($('[name="desktop_notification_user_enabled"]').is(':checked')){
		enabled = '1';
	}else{
		enabled = '';
	}
	var contactid = '<?= $_SESSION['contactid'] ?>';
	var data = { enabled: enabled,contactid:contactid};
	$.ajax({
		url: '../Settings/settings_ajax.php?fill=desktop_notifications_user',
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
        You can enable or disable desktop notifications alerts from here.
    </div>
    <div class="clearfix"></div>
</div>

<div class="col-md-12" id="dash_noti_user_options">
	<?php
	$enb = get_field_value('desktop_notification','contacts','contactid',$_SESSION['contactid']);
	?>
	<div class="form-group">
		<label class="col-sm-4 control-label">Desktop Notification:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" name="desktop_notification_user_enabled" value="1" <?= $enb == 1 ? 'checked' : '' ?>> Enable</label>
		</div>
	</div>
	
</div>