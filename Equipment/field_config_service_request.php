<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');

if (isset($_POST['service_request_btn'])) {
	$service_request = implode(',',$_POST['service_request']);
	$service_request_dashboard = implode(',',$_POST['service_request_dashboard']);

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configinvid) AS configinvid FROM field_config_equipment WHERE tab='service_request'"));
	if($get_field_config['configinvid'] > 0) {
		$query_update_employee = "UPDATE `field_config_equipment` SET equipment = '$service_request', equipment_dashboard = '$service_request_dashboard' WHERE tab='service_request'";
		$result_update_employee = mysqli_query($dbc, $query_update_employee);
	} else {
		$query_insert_config = "INSERT INTO `field_config_equipment` (`tab`, `equipment`, `equipment_dashboard`) VALUES ('service_request', '$service_request', '$service_request_dashboard')";
		$result_insert_config = mysqli_query($dbc, $query_insert_config);
	}

	echo '<script type="text/javascript"> window.location.replace("?settings=service_request"); </script>';
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

	<h4>Choose Fields for Service Request</h4>
	<?php
	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment FROM field_config_equipment WHERE tab='service_request'"));
	$value_config_equipment = ','.$get_field_config['equipment'].',';
	?>

	<table border='2' cellpadding='10' class='table'>
		<tr>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Equipment".',') !== FALSE) { echo " checked"; } ?> value="Equipment" style="height: 20px; width: 20px;" name="service_request[]">&nbsp;&nbsp;Equipment
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Service Record".',') !== FALSE) { echo " checked"; } ?> value="Service Record" style="height: 20px; width: 20px;" name="service_request[]">&nbsp;&nbsp;Service Record
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Defects".',') !== FALSE) { echo " checked"; } ?> value="Defects" style="height: 20px; width: 20px;" name="service_request[]">&nbsp;&nbsp;Defects
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment, ','."Comment".',') !== FALSE) { echo " checked"; } ?> value="Comment" style="height: 20px; width: 20px;" name="service_request[]">&nbsp;&nbsp;Comment
			</td>
		</tr>
	</table>

	<hr>

	<h4>Choose Fields for Service Request Dashboard</h4>
	<?php
	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment_dashboard FROM field_config_equipment WHERE tab='service_request'"));
	$value_config_equipment_dashboard = ','.$get_field_config['equipment_dashboard'].',';
	?>

	<table border='2' cellpadding='10' class='table'>
		<tr>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Equipment".',') !== FALSE) { echo " checked"; } ?> value="Equipment" style="height: 20px; width: 20px;" name="service_request_dashboard[]">&nbsp;&nbsp;Equipment
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Service Record".',') !== FALSE) { echo " checked"; } ?> value="Service Record" style="height: 20px; width: 20px;" name="service_request_dashboard[]">&nbsp;&nbsp;Service Record
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Defects".',') !== FALSE) { echo " checked"; } ?> value="Defects" style="height: 20px; width: 20px;" name="service_request_dashboard[]">&nbsp;&nbsp;Defects
			</td>
			<td>
				<input type="checkbox" <?php if (strpos($value_config_equipment_dashboard, ','."Comment".',') !== FALSE) { echo " checked"; } ?> value="Comment" style="height: 20px; width: 20px;" name="service_request_dashboard[]">&nbsp;&nbsp;Comment
			</td>
		</tr>
	</table>

	<hr>

	<div class="form-group pull-right">
		<a href="equipment.php?category=Top" class="btn brand-btn">Back</a>
		<button	type="submit" name="service_request_btn" value="service_request_btn" class="btn brand-btn">Submit</button>
	</div>
	
</form>