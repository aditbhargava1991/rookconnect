<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');

if (isset($_POST['equip_assign_btn'])) {
	$equip_assign_fields = filter_var(implode(',',$_POST['equip_assign_fields']),FILTER_SANITIZE_STRING);
	set_config($dbc, 'equipment_equip_assign_fields', $equip_assign_fields);

	echo '<script type="text/javascript"> window.location.replace("?settings=equip_assign"); </script>';
}
?>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<?php
	$invtype = $_GET['tab'];
	$accr = $_GET['accr'];
	$type = $_GET['type'];

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment FROM field_config_equipment WHERE tab='$invtype' AND accordion='$accr'"));
	$equipment_config = ','.$get_field_config['equipment'].',';

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment_dashboard FROM field_config_equipment WHERE tab='$invtype' AND equipment_dashboard IS NOT NULL"));
	$equipment_dashboard_config = ','.$get_field_config['equipment_dashboard'].',';

	$get_field_order = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT GROUP_CONCAT(`order` SEPARATOR ',') AS all_order FROM field_config_equipment WHERE tab='$invtype'"));
	?>

	<h4>Choose Fields for Equipment Assignment</h4>
	<?php $equip_assign_fields = ','.get_config($dbc, 'equipment_equip_assign_fields').',';
	$client_type = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"))['client_type']; ?>

	<table border='2' cellpadding='10' class='table'>
		<tr>
			<td>
				<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."equipment_assignmentid".',') !== FALSE) { echo " checked"; } ?> value="equipment_assignmentid" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;Equipment Assignment #
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."client".',') !== FALSE) { echo " checked"; } ?> value="client" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;<?= empty($client_type) ? 'Customer' : $client_type ?>
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."staff".',') !== FALSE) { echo " checked"; } ?> value="staff" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;Staff
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."contractors".',') !== FALSE) { echo " checked"; } ?> value="contractors" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;Contractors
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."region".',') !== FALSE) { echo " checked"; } ?> value="region" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;Region
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."location".',') !== FALSE) { echo " checked"; } ?> value="location" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;Location
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."classification".',') !== FALSE) { echo " checked"; } ?> value="classification" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;Classification
			</td>
		</tr>
		<tr>
			<td>
				<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."start_date".',') !== FALSE) { echo " checked"; } ?> value="start_date" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;Start Date
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."end_date".',') !== FALSE) { echo " checked"; } ?> value="end_date" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;End Date
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($equip_assign_fields, ','."notes".',') !== FALSE) { echo " checked"; } ?> value="notes" style="height: 20px; width: 20px;" name="equip_assign_fields[]">&nbsp;&nbsp;Notes
			</td>
		</tr>

	</table>

	<hr>
	
	<div class="form-group pull-right">
		<a href="?category=Top" class="btn brand-btn">Back</a>
		<button	type="submit" name="equip_assign_btn" value="equip_assign_btn" class="btn brand-btn">Submit</button>
	</div>

</form>