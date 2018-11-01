<?php
$confirmation_email_body = html_entity_decode(get_config($dbc, 'confirmation_email_body'));
$confirmation_email_subject = get_config($dbc, 'confirmation_email_subject');
?>

<script type="text/javascript">
function send_notification() {
	$('.notify_button').prop('disabled',true).html('Sending...');
	var list = [];
	$('[name=notify_list_item]:checked').each(function() { list.push(this.value); });
	$.ajax({
		url: 'ticket_ajax_all.php?action=send_notification',
		method: 'POST',
		data: {
			ticketid: ticketid,
			staff: $('[name="noti_staff[]"]').val(),
			business: $('[name=noti_businessid]').val(),
			contacts: $('[name="noti_contacts[]"]').val(),
			list: list,
			pdf: $('[name=notify_pdf_contents]').val(),
			sender_name: $('[name="noti_email_sender_name"]').val(),
			sender_email: $('[name="noti_email_sender"]').val(),
			subject: $('[name="noti_email_subject"]').val(),
			body: $('[name="noti_email_body"]').val(),
			send_date: $('[name="noti_send_date"]').val(),
			follow_up_date: $('[name="noti_followup_date"]').val()
		},
		success: function(response) {
			$('.notify_button').removeAttr('disabled').html('Send Notification');
			if(response != '') {
				alert(response);
				$('[name="noti_staff[]"]').val('').trigger('change.select2');
				$('[name=noti_businessid]').val('').trigger('change.select2');
				$('[name="noti_contacts[]"]').val('').trigger('change.select2');
				$('[name="noti_send_date"]').val('');
				$('[name="noti_followup_date"]').val('');
				$('[name=notify_list_item]:checked').removeAttr('checked');
			}
			$.ajax({
				url: '../Ticket/add_view_ticket_notifications_table.php?ticketid='+ticketid,
				method: 'GET',
				dataType: 'html',
				success: function(response) {
					$('.notification_table').html(response);
				}
			});
		}
	});
}
</script>
<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Notifications</h3>') ?>
<?php if($generate_pdf) {
	ob_clean();
} ?>
<div class="notification_table">
	<?php include('../Ticket/add_view_ticket_notifications_table.php'); ?>
</div>
<?php if($generate_pdf) {
	$pdf_contents[] = ['', ob_get_contents()];
} ?>
<?php if(($access_any > 0 || strpos($value_config, ',Notify Anyone Can Add,') !== FALSE) && !($strict_view > 0)) { ?>
	<?php foreach($field_sort_order as $field_sort_field) { ?>
		<?php if ( strpos($value_config, ',Notify Staff,') !== false && $field_sort_field == 'Notify Staff') { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Staff:</label>
				<div class="col-sm-8">
					<select data-placeholder="Select Staff..." name="noti_staff[]" multiple class="chosen-select-deselect form-control">
						<?php $staffs_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 AND `status` > 0 AND `show_hide_user` = 1"),MYSQLI_ASSOC));
						foreach ($staffs_list as $noti_staffid) {
							echo '<option value="'.$noti_staffid.'">'.get_contact($dbc, $noti_staffid).'</option>';
						} ?>
					</select>
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',Notify Business,') !== false && $field_sort_field == 'Notify Business') { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Business:</label>
				<div class="col-sm-8">
					<select name="noti_businessid" data-placeholder="Select a Business..." class="chosen-select-deselect form-control">
						<option></option><?php
						$query = mysqli_query($dbc, "SELECT `contactid`, `name` FROM `contacts` WHERE `category`='Business' AND `deleted`=0 ORDER BY `category`");
						while($row = mysqli_fetch_array($query)) {
							echo "<option value='". $row['contactid']."'>".decryptIt($row['name']).'</option>';
						} ?>
					</select>
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',Notify Client,') !== false && $field_sort_field == 'Notify Client') { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Contacts:</label>
				<div class="col-sm-8">
					<select data-placeholder="Select Contacts..." name="noti_contacts[]" multiple class="chosen-select-deselect form-control">
						<?php $contacts_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` NOT IN (".STAFF_CATS.") AND `deleted` = 0 AND `status` > 0 AND `show_hide_user` = 1"),MYSQLI_ASSOC));
						foreach ($contacts_list as $noti_contactid) {
							echo '<option '.(strpos(','.$get_ticket['clientid'].',',','.$noti_contactid.',') !== FALSE ? 'selected' : '').' value="'.$noti_contactid.'" data-businessid="'.get_contact($dbc, $noti_contactid, 'businessid').'">'.get_contact($dbc, $noti_contactid).'</option>';
						} ?>
					</select>
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',Notify List,') !== false && $field_sort_field == 'Notify List') { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?= get_config($dbc, 'ticket_notify_list') ?>:</label>
				<div class="col-sm-8">
					<?php foreach(explode('#*#',get_config($dbc, 'ticket_notify_list_items')) as $notify_list_item) { ?>
						<label class="form-checkbox full-width"><input type="checkbox" name="notify_list_item" value="<?= $notify_list_item ?>"><?= $notify_list_item ?></label>
					<?php } ?>
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',Notify PDF,') !== false && $field_sort_field == 'Notify PDF') { ?>
			<div class="form-group">
				<label class="col-sm-12 control-label">PDF Contents:</label>
				<div class="col-sm-12">
					<?php $notify_contacts = [];
					foreach(array_merge([$businessid], explode(',',$clientid)) as $notifycontactid) {
						if($notifycontactid > 0) {
							$notify_contacts[] = get_contact($dbc, $notifycontactid, 'name_company');
						}
					} ?>
					<textarea name="notify_pdf_contents"><?= html_entity_decode(str_replace(['[TICKET]','[CONTACT]','[DATE]'],[get_ticket_label($dbc, $get_ticket),implode(', ',$notify_contacts),date('Y-m-d')],get_config($dbc, 'ticket_notify_pdf_content'))) ?></textarea>
				</div>
			</div>
		<?php } ?>
	<?php } ?>

	<div class="form-group">
		<label class="col-sm-4 control-label">Email Sender's Name:</label>
		<div class="col-sm-8">
			<input type="text" name="noti_email_sender_name" class="form-control" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>">
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Email Sender's Address:</label>
		<div class="col-sm-8">
			<input type="text" name="noti_email_sender" class="form-control" value="<?= get_email($dbc, $_SESSION['contactid']) ?>">
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Email Subject:</label>
		<div class="col-sm-8">
			<input type="text" name="noti_email_subject" class="form-control" value="<?php echo $confirmation_email_subject; ?>">
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Email Body:</label>
		<div class="col-sm-12">
			<textarea name="noti_email_body" class="form-control"><?php echo $confirmation_email_body; ?></textarea>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Send Date:<br><i>(Leave blank to send now)</i></label>
		<div class="col-sm-8">
			<input type="text" name="noti_send_date" value="" class="form-control datepicker">
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Follow Up Date:</label>
		<div class="col-sm-8">
			<input type="text" name="noti_followup_date" value="" class="form-control datepicker">
		</div>
	</div>

	<div class="form-group">

	</div>

	<button class="btn brand-btn pull-right notify_button" data-history-label="Add Notification" onclick="send_notification(); return false;">Send Notification</button>
	<div class="clearfix"></div>
<?php } ?>