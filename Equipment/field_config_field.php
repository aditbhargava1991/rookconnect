<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');

if (isset($_POST['inv_field'])) {
	$tab_field = filter_var($_POST['tab_field'],FILTER_SANITIZE_STRING);
	$accordion = filter_var(urldecode($_POST['accordion']),FILTER_SANITIZE_STRING);
	$equipment = implode(',',$_POST['equipment']);
	$order = filter_var($_POST['order'],FILTER_SANITIZE_STRING);

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configinvid) AS configinvid FROM field_config_equipment WHERE tab='$tab_field' AND accordion='$accordion'"));
	if($get_field_config['configinvid'] > 0) {
		$query_update_employee = "UPDATE `field_config_equipment` SET equipment = '$equipment', `order` = '$order' WHERE tab='$tab_field' AND accordion='$accordion'";
		$result_update_employee = mysqli_query($dbc, $query_update_employee);
	} else {
		$query_insert_config = "INSERT INTO `field_config_equipment` (`tab`, `accordion`, `equipment`, `order`) VALUES ('$tab_field', '$accordion', '$equipment', '$order')";
		$result_insert_config = mysqli_query($dbc, $query_insert_config);
	}

	echo '<script type="text/javascript"> window.location.replace("?settings=field&tab='.$tab_field.'&accr='.urlencode($accordion).'"); </script>';
}
?>
<script type="text/javascript">
$(document).ready(function() {
	$("#tab_field").change(function() {
		window.location = '?settings=field&tab='+this.value;
	});
	$("#acc").change(function() {
		var tabs = $("#tab_field").val();
		window.location = '?settings=field&tab='+tabs+'&accr='+this.value;
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

					<input type="checkbox" <?php if (strpos($equipment_config, ','."Description".',') !== FALSE) { echo " checked"; } ?> value="Description" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Description
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Category".',') !== FALSE) { echo " checked"; } ?> value="Category" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Tab
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Type".',') !== FALSE) { echo " checked"; } ?> value="Type" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Type
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Make".',') !== FALSE) { echo " checked"; } ?> value="Make" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Make
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Model".',') !== FALSE) { echo " checked"; } ?> value="Model" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Model
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Unit of Measure".',') !== FALSE) { echo " checked"; } ?> value="Unit of Measure" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Unit of Measure
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Model Year".',') !== FALSE) { echo " checked"; } ?> value="Model Year" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Model Year
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Style".',') !== FALSE) { echo " checked"; } ?> value="Style" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Style
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Vehicle Size".',') !== FALSE) { echo " checked"; } ?> value="Vehicle Size" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Vehicle Size
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Color".',') !== FALSE) { echo " checked"; } ?> value="Color" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Color
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Trim".',') !== FALSE) { echo " checked"; } ?> value="Trim" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Trim
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Fuel Type".',') !== FALSE) { echo " checked"; } ?> value="Fuel Type" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Fuel Tab
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Tire Type".',') !== FALSE) { echo " checked"; } ?> value="Tire Type" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Tire Tab
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Drive Train".',') !== FALSE) { echo " checked"; } ?> value="Drive Train" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Drive Train
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Total Kilometres".',') !== FALSE) { echo " checked"; } ?> value="Total Kilometres" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Total Kilometres
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Leased".',') !== FALSE) { echo " checked"; } ?> value="Leased" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Leased
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Vehicle Access Code".',') !== FALSE) { echo " checked"; } ?> value="Vehicle Access Code" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Vehicle Access Code
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Cargo".',') !== FALSE) { echo " checked"; } ?> value="Cargo" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Cargo
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Lessor".',') !== FALSE) { echo " checked"; } ?> value="Lessor" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Lessor
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Group".',') !== FALSE) { echo " checked"; } ?> value="Group" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Group
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Use".',') !== FALSE) { echo " checked"; } ?> value="Use" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Use
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Staff".',') !== FALSE) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Staff
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Equipment Image".',') !== FALSE) { echo " checked"; } ?> value="Equipment Image" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Equipment Image
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

				<input type="checkbox" <?php if (strpos($equipment_config, ','."Serial #".',') !== FALSE) { echo " checked"; } ?> value="Serial #" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Serial #
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Part Serial".',') !== FALSE) { echo " checked"; } ?> value="Part Serial" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Part / Engine Serial #
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Unit #".',') !== FALSE) { echo " checked"; } ?> value="Unit #" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Unit #
				<input type="checkbox" <?php if (strpos($equipment_config, ','."VIN #".',') !== FALSE) { echo " checked"; } ?> value="VIN #" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;VIN #
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Licence Plate".',') !== FALSE) { echo " checked"; } ?> value="Licence Plate" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Licence Plate
					<input type="checkbox" <?php if (strpos($equipment_config, ','."Label".',') !== FALSE) { echo " checked"; } ?> value="Label" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Equipment Label
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Nickname".',') !== FALSE) { echo " checked"; } ?> value="Nickname" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Nickname

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

				<input type="checkbox" <?php if (strpos($equipment_config, ','."Year Purchased".',') !== FALSE) { echo " checked"; } ?> value="Year Purchased" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Year Purchased
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Mileage".',') !== FALSE) { echo " checked"; } ?> value="Mileage" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Mileage
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Hours Operated".',') !== FALSE) { echo " checked"; } ?> value="Hours Operated" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Hours Operated

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

				<input type="checkbox" <?php if (strpos($equipment_config, ','."Cost".',') !== FALSE) { echo " checked"; } ?> value="Cost" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Cost
				<input type="checkbox" <?php if (strpos($equipment_config, ','."CDN Cost Per Unit".',') !== FALSE) { echo " checked"; } ?> value="CDN Cost Per Unit" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;CDN Cost Per Unit
				<input type="checkbox" <?php if (strpos($equipment_config, ','."USD Cost Per Unit".',') !== FALSE) { echo " checked"; } ?> value="USD Cost Per Unit" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;USD Cost Per Unit
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Finance".',') !== FALSE) { echo " checked"; } ?> value="Finance" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Finance
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Lease".',') !== FALSE) { echo " checked"; } ?> value="Lease" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Lease
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Insurance".',') !== FALSE) { echo " checked"; } ?> value="Insurance" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Insurance

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

				<input type="checkbox" <?php if (strpos($equipment_config, ','."Hourly Rate".',') !== FALSE) { echo " checked"; } ?> value="Hourly Rate" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Hourly Rate
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Daily Rate".',') !== FALSE) { echo " checked"; } ?> value="Daily Rate" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Daily Rate
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Semi-monthly Rate".',') !== FALSE) { echo " checked"; } ?> value="Semi-monthly Rate" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Semi-monthly Rate
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Monthly Rate".',') !== FALSE) { echo " checked"; } ?> value="Monthly Rate" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Monthly Rate
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Field Day Cost".',') !== FALSE) { echo " checked"; } ?> value="Field Day Cost" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Field Day Cost
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Field Day Billable".',') !== FALSE) { echo " checked"; } ?> value="Field Day Billable" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Field Day Billable
				<input type="checkbox" <?php if (strpos($equipment_config, ','."HR Rate Work".',') !== FALSE) { echo " checked"; } ?> value="HR Rate Work" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;HR Rate Work
				<input type="checkbox" <?php if (strpos($equipment_config, ','."HR Rate Travel".',') !== FALSE) { echo " checked"; } ?> value="HR Rate Travel" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;HR Rate Travel

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

				<input type="checkbox" <?php if (strpos($equipment_config, ','."Billing Rate".',') !== FALSE) { echo " checked"; } ?> value="Billing Rate" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Billing Rate
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Billed Hours".',') !== FALSE) { echo " checked"; } ?> value="Billed Hours" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Total Billed Time
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Billed Total".',') !== FALSE) { echo " checked"; } ?> value="Billed Total" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Total Billed Amount
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Expense Total".',') !== FALSE) { echo " checked"; } ?> value="Expense Total" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Total Expenses
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Profit Total".',') !== FALSE) { echo " checked"; } ?> value="Profit Total" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Total Profit

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

				<input type="checkbox" <?php if (strpos($equipment_config, ','."Follow Up Date".',') !== FALSE) { echo " checked"; } ?> value="Follow Up Date" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Follow Up Date
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Follow Up Staff".',') !== FALSE) { echo " checked"; } ?> value="Follow Up Staff" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Follow Up Staff
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Service Date".',') !== FALSE) { echo " checked"; } ?> value="Next Service Date" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Service Date
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Service Hours".',') !== FALSE) { echo " checked"; } ?> value="Next Service Hours" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Service Hours
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Service Description".',') !== FALSE) { echo " checked"; } ?> value="Next Service Description" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Service Description
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Service Location".',') !== FALSE) { echo " checked"; } ?> value="Service Location" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Service Location
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Last Oil Filter Change (date)".',') !== FALSE) { echo " checked"; } ?> value="Last Oil Filter Change (date)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Last Oil Filter Change (date)
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Last Oil Filter Change (km)".',') !== FALSE) { echo " checked"; } ?> value="Last Oil Filter Change (km)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Last Oil Filter Change (km)
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Last Oil Filter Change (hrs)".',') !== FALSE) { echo " checked"; } ?> value="Last Oil Filter Change (hrs)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Last Oil Filter Change (hrs)
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Oil Filter Change (date)".',') !== FALSE) { echo " checked"; } ?> value="Next Oil Filter Change (date)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Oil Filter Change (date)
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Oil Filter Change (km)".',') !== FALSE) { echo " checked"; } ?> value="Next Oil Filter Change (km)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Oil Filter Change (km)
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Oil Filter Change (hrs)".',') !== FALSE) { echo " checked"; } ?> value="Next Oil Filter Change (hrs)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Oil Filter Change (hrs)
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Last Inspection & Tune Up (date)".',') !== FALSE) { echo " checked"; } ?> value="Last Inspection & Tune Up (date)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Last Inspection & Tune Up (date)
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Last Inspection & Tune Up (km)".',') !== FALSE) { echo " checked"; } ?> value="Last Inspection & Tune Up (km)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Last Inspection & Tune Up (km)
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Last Inspection & Tune Up (hrs)".',') !== FALSE) { echo " checked"; } ?> value="Last Inspection & Tune Up (hrs)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Last Inspection & Tune Up (hrs)
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Inspection & Tune Up (date)".',') !== FALSE) { echo " checked"; } ?> value="Next Inspection & Tune Up (date)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Inspection & Tune Up (date)
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Inspection & Tune Up (km)".',') !== FALSE) { echo " checked"; } ?> value="Next Inspection & Tune Up (km)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Inspection & Tune Up (km)
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Inspection & Tune Up (hrs)".',') !== FALSE) { echo " checked"; } ?> value="Next Inspection & Tune Up (hrs)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Inspection & Tune Up (hrs)
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Tire Condition".',') !== FALSE) { echo " checked"; } ?> value="Tire Condition" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Tire Condition
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Last Tire Rotation (date)".',') !== FALSE) { echo " checked"; } ?> value="Last Tire Rotation (date)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Last Tire Rotation (date)
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Last Tire Rotation (km)".',') !== FALSE) { echo " checked"; } ?> value="Last Tire Rotation (km)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Last Tire Rotation (km)
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Last Tire Rotation (hrs)".',') !== FALSE) { echo " checked"; } ?> value="Last Tire Rotation (hrs)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Last Tire Rotation (hrs)
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Tire Rotation (date)".',') !== FALSE) { echo " checked"; } ?> value="Next Tire Rotation (date)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Tire Rotation (date)
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Tire Rotation (km)".',') !== FALSE) { echo " checked"; } ?> value="Next Tire Rotation (km)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Tire Rotation (km)
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Next Tire Rotation (hrs)".',') !== FALSE) { echo " checked"; } ?> value="Next Tire Rotation (hrs)" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Next Tire Rotation (hrs)
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Registration Renewal date".',') !== FALSE) { echo " checked"; } ?> value="Registration Renewal date" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Registration Renewal date
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Insurance Renewal Date".',') !== FALSE) { echo " checked"; } ?> value="Insurance Renewal Date" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Insurance Renewal Date
				<input type="checkbox" <?php if (strpos($equipment_config, ','."CVIP Ticket Renewal Date".',') !== FALSE) { echo " checked"; } ?> value="CVIP Ticket Renewal Date" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;CVIP Ticket Renewal Date
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

				<input type="checkbox" <?php if (strpos($equipment_config, ','."Classification Dropdown".',') !== FALSE) { echo " checked"; } ?> value="Classification Dropdown" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Classification Dropdown
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Location Dropdown".',') !== FALSE) { echo " checked"; } ?> value="Location Dropdown" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Location Dropdown
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Region Dropdown".',') !== FALSE) { echo " checked"; } ?> value="Region Dropdown" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Region Dropdown
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Location".',') !== FALSE) { echo " checked"; } ?> value="Location" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Location
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Location Cookie".',') !== FALSE) { echo " checked"; } ?> value="Location Cookie" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Google Location Cookie
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Current Address".',') !== FALSE) { echo " checked"; } ?> value="Current Address" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Current Address
				<input type="checkbox" <?php if (strpos($equipment_config, ','."LSD".',') !== FALSE) { echo " checked"; } ?> value="LSD" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;LSD

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

				<input type="checkbox" <?php if (strpos($equipment_config, ','."Registration Card".',') !== FALSE) { echo " checked"; } ?> value="Registration Card" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Registration Card
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Registration Renewal date".',') !== FALSE) { echo " checked"; } ?> value="Registration Renewal date" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Registration Renewal Date
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Registration Reminder".',') !== FALSE) { echo " checked"; } ?> value="Registration Reminder" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Registration Reminder

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

				<input type="checkbox" <?php if (strpos($equipment_config, ','."Insurance Company".',') !== FALSE) { echo " checked"; } ?> value="Insurance Company" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Insurance Provider
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Insurance Contact".',') !== FALSE) { echo " checked"; } ?> value="Insurance Contact" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Insurance Contact Name
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Insurance Phone".',') !== FALSE) { echo " checked"; } ?> value="Insurance Phone" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Insurance Phone Number
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Insurance Card".',') !== FALSE) { echo " checked"; } ?> value="Insurance Card" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Insurance Card
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Insurance Renewal Date".',') !== FALSE) { echo " checked"; } ?> value="Insurance Renewal Date" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Insurance Renewal Date
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Insurance Reminder".',') !== FALSE) { echo " checked"; } ?> value="Insurance Reminder" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Insurance Reminder

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

				<input type="checkbox" <?php if (strpos($equipment_config, ','."Status".',') !== FALSE) { echo " checked"; } ?> value="Status" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Status
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Ownership Status".',') !== FALSE) { echo " checked"; } ?> onclick="if(this.checked) { $('[name^=equipment][value^=Vendor]').removeAttr('checked'); }" value="Ownership Status" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Ownership Status
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Vendor Ownership".',') !== FALSE) { echo " checked"; } ?> onclick="if(this.checked) { $('[name^=equipment][value^=Ownership]').removeAttr('checked'); }" value="Vendor Ownership" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Vendor Ownership
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Assigned Status".',') !== FALSE) { echo " checked"; } ?> value="Assigned Status" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Assigned Status

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

				<input type="checkbox" <?php if (strpos($equipment_config, ','."Quote Description".',') !== FALSE) { echo " checked"; } ?> value="Quote Description" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Quote Description

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

				<input type="checkbox" <?php if (strpos($equipment_config, ','."Volume".',') !== FALSE) { echo " checked"; } ?> value="Volume" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Equipment Volume (<?= get_config($dbc, 'volume_units') ?>)
				<input type="checkbox" <?php if (strpos($equipment_config, ','."Notes".',') !== FALSE) { echo " checked"; } ?> value="Notes" style="height: 20px; width: 20px;" name="equipment[]">&nbsp;&nbsp;Notes

				</div>
			</div>
		</div>

	</div>

	<div class="form-group pull-right">
		<a href="?category=Top" class="btn brand-btn">Back</a>
		<button	type="submit" name="inv_field"	value="inv_field" class="btn brand-btn">Submit</button>
	</div>

</form>