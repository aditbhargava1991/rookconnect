<?php error_reporting(0);
include_once('../include.php');
checkAuthorised('certificate'); ?>
<script>
$(document).on('change.select2', 'select[name="certificate_reminder_contact[]"]', function() { save_options(this); });
function save_options(field) {
	statusUnsaved(field);
	statusSaving();
	if(field.name.substr(-2) == '[]' && field.type == 'select-multiple') {
		var field_name = field.name.split('[]')[0];
		var values = [];
		$('[name="'+field.name+'"] option:selected').each(function() {
			values.push(this.value);
		});
		var value = values.join(',');
	} else {
		var field_name = field.name;
		var value = field.value;
	}
	$.ajax({
		url: 'certificate_ajax.php?action=update_config',
		method: 'POST',
		data: { name: field_name, value: value },
		response: 'html',
		success: function(response) {
			statusDone(field);
			console.log(response);
		}
	});
}
</script>
<h3>Settings - Dashboard</h3>
<div class="content-block main-screen-white">
	<div class="form-group">
		<label class="col-sm-4">Manager Email for Reminders:</label>
		<div class="col-sm-8">
			<?php $value = get_config($dbc, 'certificate_reminder_contact'); ?>
			<select name="certificate_reminder_contact[]" multiple class="chosen-select-deselect"><option></option>
				<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `first_name`, `last_name`, `contactid` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1")) as $contact) { ?>
					<option <?= strpos(','.$value.',', ','.$contact['contactid'].',') !== FALSE ? 'selected' : '' ?> value="<?= $contact['contactid'] ?>"><?= $contact['first_name'].' '.$contact['last_name'] ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4">Email Subject:</label>
		<div class="col-sm-8">
			<?php $value = get_config($dbc, 'certificate_reminder_subject');
			if($value == '') {
				$value = 'Reminder Email regarding expiry of [TITLE] on [EXPIRY]';
			} ?>
			<input name="certificate_reminder_subject" class="form-control" onchange="save_options(this);" value="<?= $value ?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4">Email Body:<br />
			<em>Use the following tags<br />
			Issue Date: [ISSUE]<br />
			Expiry Date: [EXPIRY]<br />
			Staff Name: [STAFF]<br />
			Certificate Type: [TYPE]<br />
			Certificate Title: [TITLE]<br />
			Description: [DESCRIPTION]</em></label>
		<div class="col-sm-8">
			<?php $value = get_config($dbc, 'certificate_reminder_body');
			if($value == '') {
				$value = '<p>[STAFF] received a [TITLE] on [ISSUE]. It will expire on [EXPIRY], and needs to be renewed by then. Please review the certificate through your ROOK Connect software.</p>
							<p>You have received this message because your email address is configured to receive reminders for this certificate.</p>';
			} ?>
			<textarea name="certificate_reminder_body" class="form-control" onchange="save_options(this);"><?= $value ?></textarea>
		</div>
	</div>
</div>