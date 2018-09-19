<?php error_reporting(0);
include_once('../include.php');
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
if(!isset($projectid)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
		if($tile == 'project' || $tile == config_safe_str($type_name)) {
			$project_tabs[config_safe_str($type_name)] = $type_name;
		}
	}
}
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
$project_security = get_security($dbc, 'project'); ?>
<script>
$(document).ready(function() {
	<?php if($project['invoice_sender'] == '') { ?>
		$('[name=invoice_sender]').change();
	<?php } ?>
	<?php if($project['invoice_email'] == '') { ?>
		$('[name=invoice_email]').change();
	<?php } ?>
	<?php if($project['invoice_subject'] == '') { ?>
		$('[name=invoice_subject]').change();
	<?php } ?>
	<?php if($project['invoice_body'] == '') { ?>
		$('[name=invoice_body]').change();
	<?php } ?>
});
</script>
<h3>Recurring Billing Reminders</h3>
<div class="notice double-gap-bottom popover-examples">
	<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
	<div class="col-sm-11"><span class="notice-name">NOTE:</span>
	Using this feature will not automatically send any outstanding items on the <?= PROJECT_NOUN ?> to the recipient. It will send a reminder to a chosen contact that there are unbilled items on this <?= PROJECT_NOUN ?> on the frequency shown. This can be used to ensure that items are billed regularly, such as having billings every two weeks, or it can be used to ensure that things are not forgotten by setting an annual billing date. Reminders will not be sent while the <?= PROJECT_NOUN ?> is Pending or once the <?= PROJECT_NOUN ?> has been Archived.</div>
	<div class="clearfix"></div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Recurring Billing Frequency:</label>
	<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
		<select class="chosen-select-deselect" name="invoice_freq" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid"><option></option>
			<option <?= $project['invoice_freq'] == '' ? 'selected' : '' ?> value=''>None</option>
			<option <?= $project['invoice_freq'] == 'weekly' ? 'selected' : '' ?> value="weekly">Weekly</option>
			<option <?= $project['invoice_freq'] == 'bi-weekly' ? 'selected' : '' ?> value="bi-weekly">Bi-Weekly</option>
			<option <?= $project['invoice_freq'] == 'semi-month' ? 'selected' : '' ?> value="semi-month">Semi-Monthly</option>
			<option <?= $project['invoice_freq'] == 'monthly' ? 'selected' : '' ?> value="monthly">Monthly</option>
			<option <?= $project['invoice_freq'] == 'annual' ? 'selected' : '' ?> value="annual">Annually</option>
		</select>
	</div>
</div>
<?php if(in_array('WCB Invoices',$tab_config) && in_array('Billing',$tab_config)) { ?>
	<div class="form-group">
		<label class="col-sm-4 control-label">Invoice Type:<br /><em>Indicate whether the reminder will be to create WCB invoices, or regular billings.</em></label>
		<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
			<label class="form-checkbox"><input type="radio" class="form-control" name="invoice_type" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" <?= $project['invoice_type'] == 'WCB' ? 'checked' : '' ?> value="WCB">WCB Invoice</label>
			<label class="form-checkbox"><input type="radio" class="form-control" name="invoice_type" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" <?= $project['invoice_type'] == 'WCB' ? '' : 'checked' ?> value="NEW">New Billing</label>
			<script>
			function switch_billing_type() {
				if($('[name=invoice_type]:checked').val() == 'WCB') {
					tinyMCE.get('invoice_body').setContent($('[name=invoice_body]').val().replace('tab=billing_new','tab=wcb_invoice'));
				} else {
					tinyMCE.get('invoice_body').setContent($('[name=invoice_body]').val().replace('tab=wcb_invoice','tab=billing_new'));
				}
				$('[name=invoice_body]').change();
			}
			</script>
		</div>
	</div>
<?php } else if(in_array('WCB Invoices',$tab_config)) {
	$project['invoice_type'] = 'WCB'; ?>
	<input type="hidden" name="invoice_type" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" value="WCB">
	<script>
	$(document).ready(function() {
		$('[name=invoice_type]').change();
		tinyMCE.get('invoice_body').setContent($('[name=invoice_body]').val().replace('tab=billing_new','tab=wcb_invoice'));
		$('[name=invoice_body]').change();
	});
	</script>
<?php } ?>
<div class="form-group">
	<label class="col-sm-4 control-label">Start Date:<br /><em>This is the date against which the recurrence will be calculated. (e.g. Semi-Monthly from September 22nd would bill on the 7th and 22nd of each month.)</em></label>
	<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
		<input type="text" class="datepicker form-control" name="invoice_start_date" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" value="<?= $project['invoice_start_date'] ?>">
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Recipient Name:</label>
	<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
		<input type="text" class="form-control" name="invoice_recip_name" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" value="<?= $project['invoice_recip_name'] ?>">
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Recipient Email Address:</label>
	<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
		<input type="text" class="form-control" name="invoice_recip_address" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" value="<?= $project['invoice_recip_address'] ?>">
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Sending Email Name:</label>
	<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
		<input type="text" class="form-control" name="invoice_sender" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" value="<?= $project['invoice_sender'] != '' ? $project['invoice_sender'] : get_contact($dbc, $_SESSION['contactid']) ?>">
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Sending Email Address:</label>
	<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
		<input type="text" class="form-control" name="invoice_email" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" value="<?= $project['invoice_email'] != '' ? $project['invoice_email'] : get_contact($dbc, $_SESSION['contactid'], 'email_address') ?>">
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Sending Email Subject:</label>
	<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
		<input type="text" class="form-control" name="invoice_subject" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" value="<?= $project['invoice_subject'] != '' ? $project['invoice_subject'] : 'Reminder to Create Billing for '.PROJECT_NOUN.' #'.$project['projectid'] ?>">
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Sending Email Body:</label>
	<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
		<textarea name="invoice_body" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid"><?= html_entity_decode($project['invoice_body'] != '' ? $project['invoice_body'] : '<p>This is a reminder that there are unbilled items for '.PROJECT_NOUN.' #'.$project['projectid'].': '.$project['project_name'].'.</p><p>Please <a href="'.WEBSITE_URL.'/Project/projects.php?edit='.$project['projectid'].'&tab='.(in_array('Billing',$tab_config) && $project['invoice_type'] == 'NEW' ? 'new_billing' : 'wcb_invoice').'">log in</a> to create a new billing for the '.PROJECT_NOUN.'.</p>') ?></textarea>
	</div>
</div>
<?php include('next_buttons.php'); ?>