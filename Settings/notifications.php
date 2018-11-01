<?php include_once('../include.php');
/*
Software Styling
*/
if($_GET['subtab'] == 'software' && !check_subtab_persmission($dbc, 'software_config', ROLE, 'notifications_software')) {
	$_GET['subtab'] = '';
} ?>

<script type="text/javascript">
$(document).ready(function() {
	$('#noti_options').find('input,select').change(saveConfig);
	$('[name="frequency"]').change(function() {
		$('.frequency_daily,.frequency_weekly').hide();
		if(this.value == 'weekly') {
			$('.frequency_weekly').show();
		}
		if(this.value == 'daily') {
			$('.frequency_daily').show();
		}
	});
});
function saveConfig() {
	var enabled = '';
	if($('[name="enabled"]').is(':checked')) {
		enabled = $('[name="enabled"]').val();
	}
	var frequency = $('[name="frequency"]').val();
	var alert_hour = $('[name="alert_hour"]').val();
	var alert_days = [];
	$('[name="alert_days[]"]:checked').each(function() {
		alert_days.push(this.value);
	});
	alert_days = alert_days.join(',');
	var alerts = [];
	$('[name="alerts[]"]:checked').each(function() {
		alerts.push(this.value);
	});
	alerts = alerts.join(',');
	var software_default = '<?= $_GET['subtab'] == 'software' ? 1 : 0 ?>';
	var contactid = '<?= $_SESSION['contactid'] ?>';

	var data = { enabled: enabled, frequency: frequency, alert_hour: alert_hour, alert_days: alert_days, alerts: alerts, software_default: software_default, contactid: contactid };

	$.ajax({
		url: '../Settings/settings_ajax.php?fill=notifications',
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
        Email Alerts will send emails with all enabled Reminder types consolidated into one email, and will send based on the set frequency. Emails will only be sent if there are new Reminder Alerts.
    </div>
    <div class="clearfix"></div>
</div>

<div class="col-md-12" id="noti_options">
	<div class="gap-bottom">
		<a href='settings.php?tab=notifications'><button type='button' class='btn brand-btn mobile-block mobile-100<?php echo (empty($_GET['subtab']) ? ' active_tab' : ''); ?>' >User Email Alerts</button></a>
		<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'notifications_software')) { ?>
			<a href='settings.php?tab=notifications&subtab=software'><button type='button' class='btn brand-btn mobile-block mobile-100<?php echo ($_GET['subtab'] == 'software' ? ' active_tab' : ''); ?>' >Software Default Settings</button></a>
		<?php } ?>
	</div>
	<?php if($_GET['subtab'] == 'software') {
		$noti_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_email_alerts` WHERE `software_default` = 1"));
	} else {
		$noti_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_email_alerts` WHERE `contactid` = '".$_SESSION['contactid']."'"));
		if(empty($noti_config)) {
			$noti_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_email_alerts` WHERE `software_default` = 1"));			
		}
	}
	$enabled_alerts = ','.$noti_config['alerts'].',';
	if(empty($noti_config['alert_hour'])) {
		$noti_config['alert_hour'] = 8;
	}
	if(empty($noti_config['frequency'])) {
		$noti_config['frequency'] = 'daily';
	}
	?>
	<?php if($_GET['subtab'] != 'software') { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Email Address:</label>
			<div class="col-sm-8"><?= get_email($dbc, $_SESSION['contactid']) ?></div>
		</div>
	<?php } ?>
	<div class="form-group">
		<label class="col-sm-4 control-label">Email Alerts:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" name="enabled" value="1" <?= $noti_config['enabled'] == 1 ? 'checked' : '' ?>> Enable</label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Frequency:</label>
		<div class="col-sm-8">
			<select name="frequency" class="chosen-select-deselect">
				<option value="hourly" <?= $noti_config['frequency'] == 'hourly' ? 'selected' : '' ?>>Hourly</option>
				<option value="daily" <?= $noti_config['frequency'] == 'daily' ? 'selected' : '' ?>>Daily</option>
				<option value="weekly" <?= $noti_config['frequency'] == 'weekly' ? 'selected' : '' ?>>Weekly</option>
			</select>
		</div>
	</div>
	<div class="form-group frequency_daily frequency_weekly" <?= in_array($noti_config['frequency'], ['daily','weekly']) ? '' : 'style="display: none;"' ?>>
		<label class="col-sm-4 control-label">Alert Hour:</label>
		<div class="col-sm-8">
			<select name="alert_hour" class="chosen-select-deselect">
				<?php for($i = 0; $i <= 23; $i++) {
					$label = sprintf('%02d', ($i%12 == 0 ? 12 : $i%12)).' '.($i < 12 ? 'am' : 'pm');
					echo '<option value="'.$i.'" '.($noti_config['alert_hour'] == $i ? 'selected' : '').'>'.$label.'</option>';
				} ?>
			</select>
		</div>
	</div>
	<div class="form-group frequency_weekly" <?= $noti_config['frequency'] == 'weekly' ? '' : 'style="display: none;"' ?>>
		<label class="col-sm-4 control-label">Alert Days:</label>
		<div class="col-sm-8">
            <?php $days_of_week = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            foreach ($days_of_week as $day_of_week_label) {
                echo '<label style="padding-right: 0.5em; "><input type="checkbox" name="alert_days[]" value="'.$day_of_week_label.'" '.(strpos(','.$noti_config['alert_days'].',', ','.$day_of_week_label.',') !== FALSE ? 'checked' : '').'>'.$day_of_week_label.'</label>';
            } ?>
		</div>
	</div>
	<div id="no-more-tables">
		<table class="table table-bordered table-striped" id="email_alerts">
            <thead>
                <tr class="hidden-xs">
                    <th>Email Alerts</th>
                    <th>Activate</th>
                </tr>
            </thead>
			<?php include('../Settings/notification_fields.php');
			foreach($notification_fields as $noti_value => $noti_field) { ?>
				<tr>
					<td data-title="Email Alert"><?= $noti_field ?></td>
					<td data-title="Activate"><label class="form-checkbox"><input type="checkbox" name="alerts[]" value="<?= $noti_value ?>" <?= strpos($enabled_alerts, ','.$noti_value.',') !== FALSE ? 'checked' : '' ?>> Enable</label></td>
				</tr>
			<?php } ?>
		</table>
	</div>
</div>