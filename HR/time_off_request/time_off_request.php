<?php
/*
Add	Sheet
*/
include_once ('../database_connection.php');
error_reporting(0);
?>
</head>
<script type="text/javascript">
$(document).on('change', 'select[name="fields_0"]', function() { if(this.value == 'Other') { $('[name=fields_1]').show().focus(); } });
</script>
<body>

<?php
$today_date = date('Y-m-d');
$contactid = $contactid > 0 ? $contactid : $_SESSION['contactid'];
$fields = '';
$desc = '';

if(!empty($_GET['formid'])) {
    $formid = $_GET['formid'];
	echo '<input type="hidden" name="fieldlevelriskid" value="'.$formid.'">';
	$get_field_level = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM hr_time_off_request WHERE fieldlevelriskid='$formid'"));
	$today_date = $get_field_level['today_date'];
    $contactid = $get_field_level['contactid'];
    $fields = explode('**FFM**', $get_field_level['fields']);
	$desc = $get_field_level['desc'];
}

$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `hr` WHERE tab LIKE '$tab' AND form='$form'"));$form_config = ','.$get_field_config['fields'].',';
?>

<script type="text/javascript">
$(document).ready(function() {
    window.checkedTicketBookingConflicts = false;
    $('button[name="submit"],button[name="time_off_request"]').on('click', function() {
    	var button = $(this);
    	if(!checkedTicketBookingConflicts) {
    		var contactid = '<?= $contactid ?>';
    		var startdate = $('[name="fields_2"]').val();
    		var enddate = $('[name="fields_3"]').val();
    		$.ajax({
    			url: '../Calendar/calendar_ajax_all.php?fill=check_ticket_booking_conflicts',
    			method: 'POST',
    			data: { contactid: contactid, startdate: startdate, enddate: enddate },
    			success: function(response) {
    				if(response != '') {
    					if(confirm(response)) {
						    window.checkedTicketBookingConflicts = true;
	                        $(button).trigger('click');
    					}
    				} else {
					    window.checkedTicketBookingConflicts = true;
                        $(button).trigger('click');
    				}
    			}
    		});
    		return false;
    	} else {
    		return true;
    	}
    });
});
</script>

<?php if (strpos($form_config, ','."fields1".',') !== FALSE) { ?>
	<h3>Type of Absence</h3>
	<div class="form-group">
		<label for="business_street" class="col-sm-4 control-label">Type of Absence Requested:</label>
		<div class="col-sm-8">
			<select name="fields_0" class="chosen-select-deselect form-control">
				<option></option>
				<option <?= $fields[0] == 'Vacation' ? 'selected' : '' ?> value="Vacation">Vacation</option>
				<option <?= $fields[0] == 'Bereavement' ? 'selected' : '' ?> value="Bereavement">Bereavement</option>
				<option <?= $fields[0] == 'Jury Duty' ? 'selected' : '' ?> value="Jury Duty">Jury Duty</option>
				<option <?= $fields[0] == 'Maternity/Paternity' ? 'selected' : '' ?> value="Maternity/Paternity">Maternity/Paternity</option>
				<option <?= $fields[0] == 'Sick Leave' ? 'selected' : '' ?> value="Sick Leave">Sick Leave</option>
				<option <?= $fields[0] == 'Leave of Absence' ? 'selected' : '' ?> value="Leave of Absence">Leave of Absence</option>
				<option <?= $fields[0] == 'Other' ? 'selected' : '' ?> value="Other">Other</option>
			</select>
			<input type="text" name="fields_1" value="<?php echo $fields[1]; ?>"   class="form-control" style="<?= $fields[0] == 'Other' ? '' : 'display: none;' ?>" />
		</div>
	</div>
<?php } ?>

<?php if (strpos($form_config, ','."fields2".',') !== FALSE) { ?>
	<h3>Dates of Absence</h3>
	<div class="form-group">
		<label for="business_street" class="col-sm-4 control-label">Start Date:</label>
		<div class="col-sm-8">
			<input type="text" name="fields_2" value="<?php echo $fields[2]; ?>" class="datepicker" />
		</div>
	</div>

	<div class="form-group">
		<label for="business_street" class="col-sm-4 control-label">End Date:</label>
		<div class="col-sm-8">
			<input type="text" name="fields_3" value="<?php echo $fields[3]; ?>" class="datepicker" />
		</div>
	</div>
<?php } ?>

<?php if (strpos($form_config, ','."fields3".',') !== FALSE) { ?>
	<h3>Reason for Absence</h3>
	<div class="form-group">
		<label for="business_street" class="col-sm-4 control-label">Reason for Absence:</label>
		<div class="col-sm-8">
			<textarea name="desc" rows="3" cols="50" class="form-control"><?php echo $desc; ?></textarea>
		</div>
	</div>
<?php } ?>

<h3>Validation</h3>
You must submit requests for absences two days prior to the first day you will be absent. Absences for 5 or more days require a minimum of 1 month notice prior to the first day you will be absent.<br><br>

<label class="form-checkbox any-width"><input type="checkbox" value="1" required name="agenda_send_email"> Information which I have provided here is true and correct. I have read and understand the content of this policy.</label><br><br>
Date : <?php echo date('Y-m-d'); ?><br>
Person : <?= get_contact($dbc, $contactid); ?>
<input type="hidden" name="contactid" value="<?= $contactid ?>"><br><br>
<?php include ('../phpsign/sign.php'); ?>