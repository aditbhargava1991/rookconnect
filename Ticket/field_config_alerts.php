<?php error_reporting(0);
include_once('../include.php');
$ticket_tabs = explode(',',get_config($dbc, 'ticket_tabs')); ?>
<script>
$(document).ready(function() {
	$('input,select:not([name="ticket_tab"])').change(saveAlerts);
});
$(document).on('change', 'select[name="ticket_tab"]', function() {
	window.location.href = "?settings=alerts&ticket_tab="+this.value;
});
function saveAlerts() {
	var enabled = '';
	if($('[name="enabled"]').is(':checked')) {
		enabled = 1;
	}
	var status = $('[name="status"]').val();
	var staffid = [];
	$('[name="staffid"]').each(function() {
		if($(this).val() != undefined && $(this).val() != '' && $(this).val() != 0) {
			staffid.push($(this).val());
		}
	});
	staffid = staffid.join(',');
	$.ajax({
		url: 'ticket_ajax_all.php?action=ticket_alerts',
		method: 'POST',
		data: {
			ticket_tab: $('[name="ticket_tab"]').val(),
			enabled: enabled,
			status: status,
			subject: $('[name=subject]').val(),
			body: $('[name=body]').val(),
			staffid: staffid
		}
	});
}
function addStaff() {
	destroyInputs('.staff_div');
	var clone = $('.staff_div').last().clone();
	clone.find('select').val('');

	$('.staff_div').last().after(clone);
	$('input,select:not([name="ticket_tab"])').change(saveAlerts);
	initInputs('.staff_div');
}
function removeStaff(a) {
	if($('.staff_div').length <= 1) {
		addStaff();
	}
	$(a).closest('.staff_div').remove();
	saveAlerts();
}
</script>
<div class="form-group">
	<label class="col-sm-4"><?= TICKET_NOUN ?> Tab:</label>
	<div class="col-sm-8">
		<select name="ticket_tab" class="chosen-select-deselect">
			<option></option>
			<?php foreach($ticket_tabs as $type) { ?>
				<option value="<?= config_safe_str($type) ?>" <?= $_GET['ticket_tab'] == config_safe_str($type) ? 'selected' : '' ?>><?= $type ?></option>
			<?php } ?>
		</select>
	</div>
</div>
<?php if(!empty($_GET['ticket_tab'])) {
	$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND `deleted`=0 AND `status`=1"));
	$field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_alerts` WHERE `ticket_type` = '".$_GET['ticket_tab']."'")); ?>
	<hr />
	<div class="form-group">
		<label class="col-sm-4"><img src="<?= WEBSITE_URL ?>/img/icons/alarm-1.png" class="no-toggle inline-img" title="Enable Alerts"> Enable Alerts:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" name="enabled" class="form-control" value="1" <?= $field_config['enabled'] == 1 ? 'checked' : '' ?>></label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4">Subject:<br /><em>You can add [TICKET] to have the <?= TICKET_NOUN ?> label populated into the subject.</em></label>
		<div class="col-sm-8">
			<input type="text" name="subject" class="form-control" value="<?= $field_config['subject'] ?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4">Body:<br /><em>You can add [TICKET] to have the <?= TICKET_NOUN ?> label populated into the body.</em></label>
		<div class="col-sm-8">
			<textarea name="body" class="form-control"><?= $field_config['body'] ?></textarea>
            <em>The following will be added to the end of your email:</em><br />
            To review this <?= TICKET_NOUN ?>, <a href=''>click here</a>.
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="Select a Status that the <?= TICKET_NOUN ?> needs to be before sending out alerts. Leave blank if any Status should trigger the alert."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span> Status:</label>
		<div class="col-sm-8">
			<?php $ticket_status = explode(',',get_config($dbc, 'ticket_status')); ?>
			<select name="status" class="chosen-select-deselect">
				<option></option>
				<?php foreach($ticket_status as $status) { ?>
					<option value="<?= $status ?>" <?= $field_config['status'] == $status ? 'selected' : '' ?>><?= $status ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4">Staff:</label>
		<div class="col-sm-8">
			<?php foreach(explode(',', $field_config['contactid']) as $staffid) { ?>
				<div class="staff_div">
					<div class="col-sm-10" style="padding: 0;">
						<select name="staffid" class="chosen-select-deselect">
							<option></option>
							<?php foreach($staff_list as $staff) { ?>
								<option value="<?= $staff['contactid'] ?>" <?= $staff['contactid'] == $staffid ? 'selected' : '' ?>><?= $staff['full_name'] ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-sm-2 pull-right" style="padding: 0;">
						<img src="../img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right" onclick="addStaff();">
						<img src="../img/remove.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right" onclick="removeStaff(this);">
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
<?php } ?>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_alerts.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>