<?php
/*
Inventory Listing
*/
include ('../include.php');
checkAuthorised('staff');
error_reporting(0);

if(empty($_GET['target'])) {
	$sql = "SELECT `first_name`, `last_name`, `contactid` FROM `contacts` WHERE `category`='Staff' AND `deleted`=0 AND `status`=1";
}
else if($_GET['target'] == 'alert') {
	$sql = "SELECT `first_name`, `last_name`, `contactid` FROM `contacts` WHERE `category`='Staff' AND `deleted`=0 AND `status`=1 AND `user_name` != '' AND status = 1";
}
else if($_GET['target'] == 'email') {
	$sql = "SELECT `first_name`, `last_name`, `contactid` FROM `contacts` WHERE `category`='Staff' AND `deleted`=0 AND `status`=1 AND `email_address` != ''";
}
else if($_GET['target'] == 'reminder') {
	$sql = "SELECT `first_name`, `last_name`, `contactid` FROM `contacts` WHERE `category`='Staff' AND `deleted`=0 AND `status`=1 AND `email_address` != ''";
}
if(isset($_GET['multiple']) && $_GET['multiple'] == 'true') {
	$multiple = 'multiple';
	$isarray = '[]';
} else {
	$multiple = '';
	$isarray = '';
}
$selected = [];
if($_GET['id'] > 0 && $_GET['type'] == 'checklist') {
	$selected = explode(',',mysqli_fetch_array(mysqli_query($dbc, "SELECT `alerts_enabled` FROM `checklist_name` WHERE `checklistnameid`='{$_GET['id']}'"))['alerts_enabled']);
} else if($_GET['id'] > 0 && $_GET['type'] == 'checklist board') {
	$selected = explode(',',mysqli_fetch_array(mysqli_query($dbc, "SELECT `alerts_enabled` FROM `checklist` WHERE `checklistid`='{$_GET['id']}'"))['alerts_enabled']);
} else if($_GET['id'] > 0 && $_GET['type'] == 'task') {
} else if($_GET['id'] > 0 && $_GET['type'] == 'task board') {
} else if($_GET['id'] > 0 && $_GET['type'] == 'ticket') {
} else if($_GET['id'] > 0 && $_GET['type'] == 'equipment') {
} else if($_GET['id'] > 0 && $_GET['type'] == 'equipment board') {
} else if($_GET['id'] > 0 && $_GET['type'] == 'inventory') {
} else if($_GET['id'] > 0 && $_GET['type'] == 'inventory board') {
}
?>
<div class="form-group">
	<label class="col-sm-4 control-label">Select User:</label>
	<div class="col-sm-8">
		<select <?php echo $multiple; ?> name="staff_select<?php echo $isarray; ?>" data-placeholder="Select Users..." class="chosen-select-deselect form-control change_staff_onchange">
		<?php if(isset($_GET['multiple']) && $_GET['multiple'] == 'true') { ?>
			<option value='ALL'>Assign All Staff</option>
		<?php }
		foreach(sort_contacts_query(mysqli_query($dbc, $sql)) as $row) {
			echo "<option ".(in_array($row['contactid'],$selected) ? 'selected' : '')." value='".$row['contactid']."'>".get_contact($dbc, $row['contactid']).(($_GET['target'] == 'email' || $_GET['target'] == 'reminder') ? ': '.get_email($dbc, $row['contactid']) : '')."</option>";
		} ?>
		</select>
	</div>
</div>
<div class="form-group">
	<button class='btn brand-btn pull-right'>Send</button>
</div>
<script type="text/javascript">
$(document).on('change', 'select.change_staff_onchange', function() { changeAssignedStaff(this); });
function changeAssignedStaff(sel) {
	if($(sel).find('option[value="ALL"]').is(':selected')) {
		$(sel).find('option').attr('selected','selected');
		$(sel).find('option[value="ALL"]').removeAttr('selected');
		$(sel).trigger('change.select2');
	}
}
</script>