<div id="no-more-tables">
	<?php if(search_visible_function($dbc, 'equipment') == 1) {
		$result = mysqli_query($dbc, "SELECT * FROM `equipment_inspections` WHERE `equipmentid`='$equipmentid' ORDER BY `inspectionid` DESC");
	} else {
		$result = mysqli_query($dbc, "SELECT * FROM `equipment_inspections` WHERE `equipmentid`='$equipmentid' AND `staffid`='".$_SESSION['contactid']."' ORDER BY `inspectionid` DESC");
	}
	if(mysqli_num_rows($result)) { ?>
		<table class="table table-bordered">
			<tr class="hidden-xs hidden-sm">
				<th>Staff Name</th>
				<th>Inspection Tab</th>
				<th>Date</th>
				<th>Tab</th>
				<th>Make</th>
				<th>Model</th>
				<th>Unit Number</th>
				<th>Service Requested</th>
				<th>Inspection</th>
			</tr>
			<?php while($row = mysqli_fetch_array($result)) {
				$equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='".$row['equipmentid']."'")); ?>
				<tr>
					<td data-title="Staff Name"><?= get_contact($dbc, $row['staffid']) ?></td>
					<td data-title="Inspection Tab"><?= $row['type'] ?></td>
					<td data-title="Date &amp; Time"><?= date('Y-m-d g:i A', strtotime($row['date'])) ?></td>
					<td data-title="Tab"><?= $equipment['category'] ?></td>
					<td data-title="Make"><?= $equipment['make'] ?></td>
					<td data-title="Model"><?= $equipment['model'] ?></td>
					<td data-title="Unit Number"><?= $equipment['unit_number'] ?></td>
					<td data-title="Service Requested?"><?= $row['immediate'] ? 'Yes' : 'No' ?></td>
					<td data-title="Inspection Report"><a href="download/inspection_report_<?= $row['inspectionid'] ?>.pdf">View Report</a></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo "<h2>No Inspections Found</h2>";
	} ?>
</div>