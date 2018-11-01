<?php include_once('../include.php');
checkAuthorised('calendar_rook');
include_once('../Calendar/calendar_functions_inc.php');
if(isset($_POST['submit_reminder'])) {
	$reminder_date = filter_var($_POST['reminder_date'],FILTER_SANITIZE_STRING);
	$reminder_time = filter_var($_POST['reminder_time'],FILTER_SANITIZE_STRING);
	$reminder_sending_email = filter_var($_POST['reminder_sending_email'],FILTER_SANITIZE_STRING);
	$reminder_subject = filter_var($_POST['reminder_subject'],FILTER_SANITIZE_STRING);
	$reminder_body = filter_var(htmlentities($_POST['reminder_body']),FILTER_SANITIZE_STRING);

	foreach ($_POST['reminder_staff'] as $reminder_staff) {
		mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_type`, `reminder_date`, `reminder_time`, `subject`, `body`, `sender`, `src_table`) VALUES ('$reminder_staff', 'CALENDAR', '$reminder_date', '$reminder_time', '$reminder_subject', '$reminder_body', '$reminder_sending_email', 'calendar')");
	}

	$query = $_GET;
	unset($query['add_reminder']);
    echo '<script>window.location.replace("?'.http_build_query($query).'");</script>';
}
?>
<script type="text/javascript">
$(document).ready(function() {
	$('[name="reminder_staff[]"]').change(function() {
		$(this).find('[value=select_all_staff]:selected').each(function() {
			$('[name="reminder_staff[]"] option').attr('selected',true);
			$(this).removeAttr('selected')
		});
		$(this).trigger('change.select2');
	});
});
</script>
<h3>Add Reminder</h3>
<div class="block-group" style="height: calc(100% - 4.5em); overflow-y: auto;">
    <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
		<label class="super-label">Staff Receiving Reminder:
			<select multiple name="reminder_staff[]" data-placeholder="Select a Staff" class="chosen-select-deselect">
				<option value="select_all_staff">Remind All Staff</option>
				<?php
					$staff_list_side = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 AND `status` = 1"),MYSQLI_ASSOC));
					foreach($staff_list_side as $staff_id) {
						echo '<option value="'.$staff_id.'">'.get_contact($dbc, $staff_id).'</option>';
					}
				?>
			</select>
		</label>
		<label class="super-label">Reminder Date:
			<input type="text" name="reminder_date" class="datepicker form-control" value="<?= date('Y-m-d', strtotime($_GET['date'])) ?>">
		</label>
		<label class="super-label">Reminder Time:
			<input type="text" name="reminder_time" class="timepicker form-control" value="08:00">
		</label>
		<label class="super-label">Sending Email Address:
			<input type="text" name="reminder_sending_email" class="form-control" value="<?= get_email($dbc, $_SESSION['contactid']) ?>">
		</label>
		<label class="super-label">Email Subject:
			<input type="text" name="reminder_subject" class="form-control">
		</label>
		<label class="super-label">Email Body:
			<textarea name="reminder_body" class="form-control"></textarea>
		</label>
	    <a href="?<?= http_build_query($page_query) ?>" class="btn brand-btn pull-right">Cancel</a>
		<button type="submit" name="submit_reminder" class="btn brand-btn pull-right">Submit</button>
	    <?php
	        unset($page_query['teamid']);
	        unset($page_query['subtab']);
	        unset($page_query['unbooked']);
	        unset($page_query['equipment_assignmentid']);
	        unset($page_query['shiftid']);
	        unset($page_query['action']);
	        unset($page_query['bookingid']);
	        unset($page_query['appoint_date']);
	        unset($page_query['end_appoint_date']);
	        unset($page_query['therapistsid']);
	        unset($page_query['equipmentid']);
	        unset($page_query['add_reminder']);
	    ?>
	</form>
</div>