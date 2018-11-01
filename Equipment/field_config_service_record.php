<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');

if (isset($_POST['service_record_btn'])) {
	$service_record = implode(',',$_POST['service_record']);
	$service_record_dashboard = implode(',',$_POST['service_record_dashboard']);

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configinvid) AS configinvid FROM field_config_equipment WHERE tab='service_record'"));
	if($get_field_config['configinvid'] > 0) {
		$query_update_employee = "UPDATE `field_config_equipment` SET equipment = '$service_record', equipment_dashboard = '$service_record_dashboard' WHERE tab='service_record'";
		$result_update_employee = mysqli_query($dbc, $query_update_employee);
	} else {
		$query_insert_config = "INSERT INTO `field_config_equipment` (`tab`, `equipment`, `equipment_dashboard`) VALUES ('service_record', '$service_record', '$service_record_dashboard')";
		$result_insert_config = mysqli_query($dbc, $query_insert_config);
	}

	echo '<script type="text/javascript"> window.location.replace("?settings=service_record"); </script>';
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

	<h4>Choose Fields for Service Record</h4>
	<?php
	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment FROM field_config_equipment WHERE tab='service_record'"));
	$value_config_equipment = ','.$get_field_config['equipment'].',';
	?>

	<table border='2' cellpadding='10' class='table'>
		<tr>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Service Date".',') !== FALSE) { echo " checked"; } ?> value="Service Date" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Service Date
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Advised Service Date".',') !== FALSE) { echo " checked"; } ?> value="Advised Service Date" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Advised Service Date
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Equipment".',') !== FALSE) { echo " checked"; } ?> value="Equipment" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Equipment
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Inventory".',') !== FALSE) { echo " checked"; } ?> value="Inventory" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Inventory
			</td>

			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Description of Job".',') !== FALSE) { echo " checked"; } ?> value="Description of Job" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Description of Job
			</td>

			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Service Record Mileage".',') !== FALSE) { echo " checked"; } ?> value="Service Record Mileage" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Service Record Mileage
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Hours".',') !== FALSE) { echo " checked"; } ?> value="Hours" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Hours
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Completed".',') !== FALSE) { echo " checked"; } ?> value="Completed" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Completed
			</td>
		</tr>

		<tr>

			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Staff".',') !== FALSE) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Staff
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Vendor".',') !== FALSE) { echo " checked"; } ?> value="Vendor" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Vendor
			</td>

			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Service Record Cost".',') !== FALSE) { echo " checked"; } ?> value="Service Record Cost" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Service Record Cost
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Service Type".',') !== FALSE) { echo " checked"; } ?> value="Service Type" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Service Tab
			</td>

			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Kilometers".',') !== FALSE) { echo " checked"; } ?> value="Kilometers" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Kilometers
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Recommended Next Service Mileage".',') !== FALSE) { echo " checked"; } ?> value="Recommended Next Service Mileage" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Recommended Next Service Mileage
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Receipt/Document".',') !== FALSE) { echo " checked"; } ?> value="Receipt/Document" style="height: 20px; width: 20px;" name="service_record[]">&nbsp;&nbsp;Receipt/Document
			</td>

		</tr>

	</table>

	<hr>

	<h4>Choose Fields for Service Record Dashboard</h4>
	<?php
	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment_dashboard FROM field_config_equipment WHERE tab='service_record'"));
	$value_config_equipment_dashboard = ','.$get_field_config['equipment_dashboard'].',';
	?>

	<table border='2' cellpadding='10' class='table'>
		<tr>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Service Date".',') !== FALSE) { echo " checked"; } ?> value="Service Date" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Service Date
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Advised Service Date".',') !== FALSE) { echo " checked"; } ?> value="Advised Service Date" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Advised Service Date
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Equipment".',') !== FALSE) { echo " checked"; } ?> value="Equipment" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Equipment
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Inventory".',') !== FALSE) { echo " checked"; } ?> value="Inventory" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Inventory
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Description of Job".',') !== FALSE) { echo " checked"; } ?> value="Description of Job" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Description of Job
			</td>

			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Service Record Mileage".',') !== FALSE) { echo " checked"; } ?> value="Service Record Mileage" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Service Record Mileage
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Hours".',') !== FALSE) { echo " checked"; } ?> value="Hours" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Hours
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Completed".',') !== FALSE) { echo " checked"; } ?> value="Completed" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Completed
			</td>
		</tr>

		<tr>

			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Staff".',') !== FALSE) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Staff
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Vendor".',') !== FALSE) { echo " checked"; } ?> value="Vendor" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Vendor
			</td>

			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Service Record Cost".',') !== FALSE) { echo " checked"; } ?> value="Service Record Cost" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Service Record Cost
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Service Type".',') !== FALSE) { echo " checked"; } ?> value="Service Type" style="height: 20px; width: 20px;" name="service_record_dashboard[]">&nbsp;&nbsp;Service Tab
			</td>
		</tr>

	</table>

	<hr>

	<div class="form-group pull-right">
		<a href="?category=Top" class="btn brand-btn">Back</a>
		<button	type="submit" name="service_record_btn" value="service_record_btn" class="btn brand-btn">Submit</button>
	</div>

</form>