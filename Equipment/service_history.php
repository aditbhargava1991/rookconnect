<?php include('../include.php');
checkAuthorised('equipment');
error_reporting(0);
$equipmentid = $_GET['equipmentid']; ?>
<h2>Service History</h2>
<div id="no-more-tables">
	<?php $service_records = mysqli_query($dbc, "SELECT * FROM equipment_service_record WHERE `equipmentid`='$equipmentid'");
	$service_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment_dashboard FROM field_config_equipment WHERE tab='service_record' AND equipment_dashboard IS NOT NULL"))['equipment_dashboard'];
	if(mysqli_num_rows($service_records) > 0) {
		echo "<table class='table table-bordered'>";
		echo "<tr class='hidden-xs hidden-sm'>";
		if (strpos($service_config, ','."Service Date".',') !== FALSE) {
			echo '<th>Service Date</th>';
		}
		if (strpos($service_config, ','."Advised Service Date".',') !== FALSE) {
			echo '<th>Advised Service Date</th>';
		}
		if (strpos($service_config, ','."Service Type".',') !== FALSE) {
			echo '<th>Service Tab</th>';
		}
		if (strpos($service_config, ','."Inventory".',') !== FALSE) {
			echo '<th>Inventory</th>';
		}
		if (strpos($service_config, ','."Description of Job".',') !== FALSE) {
			echo '<th>Description of Job</th>';
		}
		if (strpos($service_config, ','."Service Record Mileage".',') !== FALSE) {
			echo '<th>Service Record Mileage</th>';
		}
		if (strpos($service_config, ','."Hours".',') !== FALSE) {
			echo '<th>Hours</th>';
		}
		if (strpos($service_config, ','."Completed".',') !== FALSE) {
			echo '<th>Completed</th>';
		}
		if (strpos($service_config, ','."Staff".',') !== FALSE) {
			echo '<th>Staff</th>';
		}
		if (strpos($service_config, ','."Vendor".',') !== FALSE) {
			echo '<th>Vendor</th>';
		}
		if (strpos($service_config, ','."Service Record Cost".',') !== FALSE) {
			echo '<th>Service Record Cost</th>';
		}
		echo "</tr>";
		while($service_row = mysqli_fetch_array( $service_records ))
		{
			echo '<tr>';
			$equipment = $service_row['unit_number'];
			$equipment .= ' : '.$service_row['serial_number'];
			$equipment .= ' : '.$service_row['type'];
			$equipment .= ' : '.$service_row['category'];
			$equipment .= ' : '.$service_row['make'];
			$equipment .= ' : '.$service_row['model'];
			$equipment .= ' : '.$service_row['year_purchased'];
			$equipment .= ' : '.$service_row['mileage'];

			if (strpos($service_config, ','."Service Date".',') !== FALSE) {
				echo '<td data-title="Srv. Date">' . $service_row['service_date'] . '</td>';
			}
			if (strpos($service_config, ','."Advised Service Date".',') !== FALSE) {
				echo '<td data-title="Advised Srv. Date">' . $service_row['advised_service_date'] . '</td>';
			}
			if (strpos($service_config, ','."Service Type".',') !== FALSE) {
				echo '<td data-title="Service Tab">' . $service_row['service_type'] . '</td>';
			}

			if (strpos($service_config, ','."Inventory".',') !== FALSE) {
				$inventoryid = $service_row['inventoryid'];
				$inventory = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT inventoryid, code, category, sub_category FROM inventory WHERE inventoryid='$inventoryid'"));
				if($inventoryid != '') {
					echo '<td data-title="Inventory Item">' . $inventory['code'].' : '.$inventory['category']. ' : '.$inventory['sub_category'] . '</td>';
				} else {
					echo '<td>-</td>';
				}
			}

			if (strpos($service_config, ','."Description of Job".',') !== FALSE) {
				echo '<td data-title="Job Desc">' . $service_row['description_of_job'] . '</td>';
			}
			if (strpos($service_config, ','."Service Record Mileage".',') !== FALSE) {
				echo '<td data-title="Srv Rec. Mileage">' . $service_row['service_record_mileage'] . '</td>';
			}
			if (strpos($service_config, ','."Hours".',') !== FALSE) {
				echo '<td data-title="Hours">' . $service_row['service_record_hours'] . '</td>';
			}
			if (strpos($service_config, ','."Completed".',') !== FALSE) {
				echo '<td data-title="Completed">' . $service_row['completed'] . '</td>';
			}

			if (strpos($service_config, ','."Staff".',') !== FALSE) {
				$contactid = $service_row['contactid'];
				$staff = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT first_name, last_name FROM contacts WHERE contactid='$contactid'"));
				if($contactid != '') {
					echo '<td data-title="Staff">' . decryptIt($staff['first_name']).' '.decryptIt($staff['last_name']) . '</td>';
				} else {
					echo '<td>-</td>';
				}
			}
			if (strpos($service_config, ','."Vendor".',') !== FALSE) {
				$vendorid = $service_row['vendorid'];
				$vendor = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT name FROM contacts WHERE contactid='$vendorid'"));
				if($vendorid != '') {
					echo '<td data-title="Vendor">' . decryptIt($vendor['name']) . '</td>';
				} else {
					echo '<td>-</td>';
				}
			}
			if (strpos($service_config, ','."Service Record Cost".',') !== FALSE) {
				echo '<td data-title="Srv. Cost">' . $service_row['cost'] . '</td>';
			}

			echo "</tr>";
		}

		echo '</table>';
	} else {
		echo "<h2>No Record Found.</h2>";
	} ?>
</div>