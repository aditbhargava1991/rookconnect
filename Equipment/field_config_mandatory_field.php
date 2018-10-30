<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');

if (isset($_POST['inv_field'])) {
	$tab_field = filter_var($_POST['tab_field'],FILTER_SANITIZE_STRING);
	$accordion = filter_var(urldecode($_POST['accordion']),FILTER_SANITIZE_STRING);
	$equipment = implode(',',$_POST['equipment']);
	$order = filter_var($_POST['order'],FILTER_SANITIZE_STRING);

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configinvid) AS configinvid FROM field_config_equipment WHERE tab='$tab_field' AND accordion='$accordion' AND mandatory=1"));
	if($get_field_config['configinvid'] > 0) {
		$query_update_employee = "UPDATE `field_config_equipment` SET equipment = '$equipment', `order` = '$order' WHERE tab='$tab_field' AND accordion='$accordion' AND mandatory=1";
		$result_update_employee = mysqli_query($dbc, $query_update_employee);
	} else {
		$query_insert_config = "INSERT INTO `field_config_equipment` (`tab`, `accordion`, `equipment`, `order`,`mandatory`) VALUES ('$tab_field', '$accordion', '$equipment', '$order',1)";
		$result_insert_config = mysqli_query($dbc, $query_insert_config);
	}

	echo '<script type="text/javascript"> window.location.replace("?settings=mandatory_fields&tab='.$tab_field.'&accr='.urlencode($accordion).'"); </script>';
}
?>
<script type="text/javascript">
$(document).ready(function() {
	$("#tab_field").change(function() {
		window.location = '?settings=mandatory_fields&tab='+this.value;
	});
	$("#acc").change(function() {
		var tabs = $("#tab_field").val();
		window.location = '?settings=mandatory_fields&tab='+tabs+'&accr='+this.value;
	});
});
</script>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<?php
	$invtype = $_GET['tab'];
	$accr = $_GET['accr'];
	$type = $_GET['type'];

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment FROM field_config_equipment WHERE tab='$invtype' AND accordion='$accr'"));
	$equipment_config = ','.$get_field_config['equipment'].',';

	$get_mandatory_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment FROM field_config_equipment WHERE tab='$invtype' AND accordion='$accr' AND mandatory=1"));
	$equipment_mandatory_config = ','.$get_mandatory_field_config['equipment'].',';

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment_dashboard FROM field_config_equipment WHERE tab='$invtype' AND equipment_dashboard IS NOT NULL"));
	$equipment_dashboard_config = ','.$get_field_config['equipment_dashboard'].',';

	$get_field_order = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT GROUP_CONCAT(`order` SEPARATOR ',') AS all_order FROM field_config_equipment WHERE tab='$invtype'"));
	?>
	<div class="form-group">
		<label for="fax_number"	class="col-sm-4	control-label">Tabs:</label>
		<div class="col-sm-8">
			<select data-placeholder="Select a Tab..." id="tab_field" name="tab_field" class="chosen-select-deselect form-control" width="380">
				<option value=""></option>
				<?php
				$tabs = get_config($dbc, 'equipment_tabs');
				$each_tab = explode(',', $tabs);
				foreach ($each_tab as $cat_tab) {
					if ($invtype == $cat_tab) {
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}
					echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
				}
				?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label for="fax_number"	class="col-sm-4	control-label">Accordion:</label>
		<div class="col-sm-8">
			<select data-placeholder="Select Accordion..." id="acc" name="accordion" class="chosen-select-deselect form-control" width="380">
				<option value=""></option>

				<option <?php if ($accr == "Equipment Information") { echo " selected"; } ?> value="Equipment Information"><?php echo get_field_config_equipment($dbc, 'Equipment Information', 'order', $invtype); ?> : Equipment Information</option>
				<option <?php if ($accr == "Description") { echo " selected"; } ?> value="Description"><?php echo get_field_config_equipment($dbc, 'Description', 'order', $invtype); ?> : Description</option>
				<option <?php if ($accr == "Unique Identifier") { echo " selected"; } ?> value="Unique Identifier"><?php echo get_field_config_equipment($dbc, 'Unique Identifier', 'order', $invtype); ?> : Unique Identifier</option>
				<option <?php if ($accr == "Purchase Info") { echo " selected"; } ?> value="Purchase Info"><?php echo get_field_config_equipment($dbc, 'Purchase Info', 'order', $invtype); ?> : Purchase Info</option>
				<option <?php if ($accr == "Product Cost") { echo " selected"; } ?> value="Product Cost"><?php echo get_field_config_equipment($dbc, 'Product Cost', 'order', $invtype); ?> : Product Cost</option>
				<option <?php if ($accr == "Pricing") { echo " selected"; } ?> value="Pricing"><?php echo get_field_config_equipment($dbc, 'Pricing', 'order', $invtype); ?> : Pricing</option>
				<option <?php if ($accr == "Service & Alerts") { echo " selected"; } ?> value="<?= urlencode('Service & Alerts') ?>"><?php echo get_field_config_equipment($dbc, 'Service & Alerts', 'order', $invtype); ?> : Service &amp; Alerts</option>
				<option <?php if ($accr == "Location") { echo " selected"; } ?> value="Location"><?php echo get_field_config_equipment($dbc, 'Location', 'order', $invtype); ?> : Location</option>
				<option <?php if ($accr == "Status") { echo " selected"; } ?> value="Status"><?php echo get_field_config_equipment($dbc, 'Status', 'order', $invtype); ?> : Status</option>
				<option <?php if ($accr == "Registration") { echo " selected"; } ?> value="Registration"><?php echo get_field_config_equipment($dbc, 'Registration', 'order', $invtype); ?> : Registration</option>
				<option <?php if ($accr == "Insurance") { echo " selected"; } ?> value="Insurance"><?php echo get_field_config_equipment($dbc, 'Insurance', 'order', $invtype); ?> : Insurance</option>
				<option <?php if ($accr == "Quote Description") { echo " selected"; } ?> value="Quote Description"><?php echo get_field_config_equipment($dbc, 'Quote Description', 'order', $invtype); ?> : Quote Description</option>
				<option <?php if ($accr == "General") { echo " selected"; } ?> value="General"><?php echo get_field_config_equipment($dbc, 'General', 'order', $invtype); ?> : General</option>
			</select>
			<select data-placeholder="Select Accordion Order..." name="order" class="chosen-select-deselect form-control" width="380">
				<option value=""></option>
				<?php
				for($m=1;$m<=30;$m++) { ?>
					<option <?php if (get_field_config_equipment($dbc, $accr, 'order', $invtype) == $m) { echo	'selected="selected"'; } else if (strpos(','.$get_field_order['all_order'].',', ','.$m.',') !== FALSE) { echo " disabled"; } ?> value="<?php echo $m;?>"><?php echo $m;?></option>
				<?php }
				?>
			</select>
		</div>
	</div>

	<h3>Fields</h3>
	<div class="panel-group" id="accordion2">

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_1" >
						Description<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_1" class="panel-collapse collapse">
				<div class="panel-body">
					<?php $description_array=array("Description","Category","Type","Make","Model","Unit of Measure","Model Year","Style","Vehicle Size","Color","Trim","Fuel Type","Tire Type","Drive Train","Total Kilometres","Leased","Vehicle Access Code","Cargo","Lessor","Group","Use","StaffEquipment Image"); ?>
					<?php $equipment_config_array = array_intersect($description_array, array_filter(explode(",", $equipment_config))); ?>
					<?php foreach($equipment_config_array as $equipment_field): ?>
						<input type="checkbox" <?php if (strpos($equipment_mandatory_config, ','.$equipment_field.',') !== FALSE) { echo " checked"; } ?> value="<?php echo $equipment_field; ?>" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;<?php echo $equipment_field; ?>
					<?php endforeach; ?>
					</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_2" >
						Unique Identifier<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_2" class="panel-collapse collapse">
				<div class="panel-body">
					<?php $description_array=array("Serial #","Part Serial","Unit #","VIN #","Licence Plate","Label","Nickname"); ?>
					<?php $equipment_config_array = array_intersect($description_array, array_filter(explode(",", $equipment_config))); ?>
					<?php foreach($equipment_config_array as $equipment_field): ?>
						<input type="checkbox" <?php if (strpos($equipment_mandatory_config, ','.$equipment_field.',') !== FALSE) { echo " checked"; } ?> value="<?php echo $equipment_field; ?>" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;<?php echo $equipment_field; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_3" >
						Purchase Info<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_3" class="panel-collapse collapse">
				<div class="panel-body">
					<?php $description_array=array("Year Purchased","Mileage","Hours Operated"); ?>
					<?php $equipment_config_array = array_intersect($description_array, array_filter(explode(",", $equipment_config))); ?>
					<?php foreach($equipment_config_array as $equipment_field): ?>
						<input type="checkbox" <?php if (strpos($equipment_mandatory_config, ','.$equipment_field.',') !== FALSE) { echo " checked"; } ?> value="<?php echo $equipment_field; ?>" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;<?php echo $equipment_field; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_4" >
						Product Cost<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_4" class="panel-collapse collapse">
				<div class="panel-body">
					<?php $description_array=array("Cost","CDN Cost Per Unit","USD Cost Per Unit","Finance","Lease","Insurance"); ?>
					<?php $equipment_config_array = array_intersect($description_array, array_filter(explode(",", $equipment_config))); ?>
					<?php foreach($equipment_config_array as $equipment_field): ?>
						<input type="checkbox" <?php if (strpos($equipment_mandatory_config, ','.$equipment_field.',') !== FALSE) { echo " checked"; } ?> value="<?php echo $equipment_field; ?>" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;<?php echo $equipment_field; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_5" >
						Pricing<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_5" class="panel-collapse collapse">
				<div class="panel-body">
					<?php $description_array=array("Hourly Rate","Daily Rate","Semi-monthly Rate","Monthly Rate","Lease","Field Day Cost","Field Day Billable","HR Rate Work","HR Rate Travel"); ?>
					<?php $equipment_config_array = array_intersect($description_array, array_filter(explode(",", $equipment_config))); ?>
					<?php foreach($equipment_config_array as $equipment_field): ?>
						<input type="checkbox" <?php if (strpos($equipment_mandatory_config, ','.$equipment_field.',') !== FALSE) { echo " checked"; } ?> value="<?php echo $equipment_field; ?>" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;<?php echo $equipment_field; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_6" >
						Profit &amp; Loss<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_6" class="panel-collapse collapse">
				<div class="panel-body">
					<?php $description_array=array("Billing Rate","Billed Hours","Billed Total","Monthly Rate","Expense Total","Profit Total"); ?>
					<?php $equipment_config_array = array_intersect($description_array, array_filter(explode(",", $equipment_config))); ?>
					<?php foreach($equipment_config_array as $equipment_field): ?>
						<input type="checkbox" <?php if (strpos($equipment_mandatory_config, ','.$equipment_field.',') !== FALSE) { echo " checked"; } ?> value="<?php echo $equipment_field; ?>" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;<?php echo $equipment_field; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_7" >
						Service & Alerts<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_7" class="panel-collapse collapse">
				<div class="panel-body">
					<?php $description_array=array("Follow Up Date","Follow Up Staff","Next Service Date","Next Service Hours","Next Service Description","Service Location","Last Oil Filter Change (date)","Last Oil Filter Change (km)","Last Oil Filter Change (hrs)","Next Oil Filter Change (date)","Next Oil Filter Change (km)","Next Oil Filter Change (hrs)","Last Inspection & Tune Up (date)","Last Inspection & Tune Up (km)","Last Inspection & Tune Up (hrs)","Next Inspection & Tune Up (date)","Next Inspection & Tune Up (km)","Next Inspection & Tune Up (hrs)","Tire Condition","Last Tire Rotation (date)","Last Tire Rotation (km)","Last Tire Rotation (hrs)","Next Tire Rotation (date)", "Next Tire Rotation (km)","Next Tire Rotation (hrs)","Registration Renewal date","Insurance Renewal Date","CVIP Ticket Renewal Date"); ?>
					<?php $equipment_config_array = array_intersect($description_array, array_filter(explode(",", $equipment_config))); ?>
					<?php foreach($equipment_config_array as $equipment_field): ?>
						<input type="checkbox" <?php if (strpos($equipment_mandatory_config, ','.$equipment_field.',') !== FALSE) { echo " checked"; } ?> value="<?php echo $equipment_field; ?>" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;<?php echo $equipment_field; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_8" >
						Location<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_8" class="panel-collapse collapse">
				<div class="panel-body">
					<?php $description_array=array("Classification Dropdown","Location Dropdown","Region Dropdown","Location","Location Cookie","Current Address","LSD"); ?>
					<?php $equipment_config_array = array_intersect($description_array, array_filter(explode(",", $equipment_config))); ?>
					<?php foreach($equipment_config_array as $equipment_field): ?>
						<input type="checkbox" <?php if (strpos($equipment_mandatory_config, ','.$equipment_field.',') !== FALSE) { echo " checked"; } ?> value="<?php echo $equipment_field; ?>" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;<?php echo $equipment_field; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_reg" >
						Registration Information<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_reg" class="panel-collapse collapse">
				<div class="panel-body">
					<?php $description_array=array("Registration Card","Registration Renewal date","Registration Reminder"); ?>
					<?php $equipment_config_array = array_intersect($description_array, array_filter(explode(",", $equipment_config))); ?>
					<?php foreach($equipment_config_array as $equipment_field): ?>
						<input type="checkbox" <?php if (strpos($equipment_mandatory_config, ','.$equipment_field.',') !== FALSE) { echo " checked"; } ?> value="<?php echo $equipment_field; ?>" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;<?php echo $equipment_field; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_ins" >
						Insurance Information<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_ins" class="panel-collapse collapse">
				<div class="panel-body">
					<?php $description_array=array("Insurance Company","Insurance Contact","Insurance Phone","Insurance Card","Insurance Renewal Date","Insurance Reminder"); ?>
					<?php $equipment_config_array = array_intersect($description_array, array_filter(explode(",", $equipment_config))); ?>
					<?php foreach($equipment_config_array as $equipment_field): ?>
						<input type="checkbox" <?php if (strpos($equipment_mandatory_config, ','.$equipment_field.',') !== FALSE) { echo " checked"; } ?> value="<?php echo $equipment_field; ?>" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;<?php echo $equipment_field; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_9" >
						Status<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_9" class="panel-collapse collapse">
				<div class="panel-body">
					<?php $description_array=array("Status","Ownership Status","Assigned Status"); ?>
					<?php $equipment_config_array = array_intersect($description_array, array_filter(explode(",", $equipment_config))); ?>
					<?php foreach($equipment_config_array as $equipment_field): ?>
						<input type="checkbox" <?php if (strpos($equipment_mandatory_config, ','.$equipment_field.',') !== FALSE) { echo " checked"; } ?> value="<?php echo $equipment_field; ?>" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;<?php echo $equipment_field; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_10" >
						Quote Description<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_10" class="panel-collapse collapse">
				<div class="panel-body">
					<?php $description_array=array("Quote Description"); ?>
					<?php $equipment_config_array = array_intersect($description_array, array_filter(explode(",", $equipment_config))); ?>
					<?php foreach($equipment_config_array as $equipment_field): ?>
						<input type="checkbox" <?php if (strpos($equipment_mandatory_config, ','.$equipment_field.',') !== FALSE) { echo " checked"; } ?> value="<?php echo $equipment_field; ?>" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;<?php echo $equipment_field; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_11" >
						General<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_11" class="panel-collapse collapse">
				<div class="panel-body">
					<?php $description_array=array("Volume","Notes"); ?>
					<?php $equipment_config_array = array_intersect($description_array, array_filter(explode(",", $equipment_config))); ?>
					<?php foreach($equipment_config_array as $equipment_field): ?>
						<input type="checkbox" <?php if (strpos($equipment_mandatory_config, ','.$equipment_field.',') !== FALSE) { echo " checked"; } ?> value="<?php echo $equipment_field; ?>" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;<?php echo $equipment_field; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

	</div>

	<div class="form-group pull-right">
		<a href="?category=Top" class="btn brand-btn">Back</a>
		<button	type="submit" name="inv_field"	value="inv_field" class="btn brand-btn">Submit</button>
	</div>

</form>
