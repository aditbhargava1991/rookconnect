<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');

if (isset($_POST['inv_dashboard'])) {
	$tab_dashboard = filter_var($_POST['tab_dashboard'],FILTER_SANITIZE_STRING);
	$equipment_dashboard = implode(',',$_POST['equipment_dashboard']);

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configinvid) AS configinvid FROM field_config_equipment WHERE tab='$tab_dashboard' AND equipment_dashboard IS NOT NULL AND accordion IS NULL"));
	if($get_field_config['configinvid'] > 0) {
		$query_update_employee = "UPDATE `field_config_equipment` SET equipment_dashboard = '$equipment_dashboard' WHERE tab='$tab_dashboard'";
		$result_update_employee = mysqli_query($dbc, $query_update_employee);
	} else {
		$query_insert_config = "INSERT INTO `field_config_equipment` (`tab`, `equipment_dashboard`) VALUES ('$tab_dashboard', '$equipment_dashboard')";
		$result_insert_config = mysqli_query($dbc, $query_insert_config);
	}
	echo '<script type="text/javascript"> window.location.replace("?settings=dashboard&tab='.$tab_dashboard.'"); </script>';
}
?>
<script type="text/javascript">
$(document).ready(function() {
	$("#tab_dashboard").change(function() {
		window.location = '?settings=dashboard&tab='+this.value;
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
			<select data-placeholder="Choose a Vendor..." id="tab_dashboard" name="tab_dashboard" class="chosen-select-deselect form-control" width="380">
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

	<h4>Dashboard</h4>
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
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Description".',') !== FALSE) { echo " checked"; } ?> value="Description" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Description
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Category".',') !== FALSE) { echo " checked"; } ?> value="Category" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Category
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Type".',') !== FALSE) { echo " checked"; } ?> value="Type" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Tab
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Make".',') !== FALSE) { echo " checked"; } ?> value="Make" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Make
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Model".',') !== FALSE) { echo " checked"; } ?> value="Model" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Model
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Unit of Measure".',') !== FALSE) { echo " checked"; } ?> value="Unit of Measure" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Unit of Measure
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Model Year".',') !== FALSE) { echo " checked"; } ?> value="Model Year" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Model Year
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Style".',') !== FALSE) { echo " checked"; } ?> value="Style" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Style
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Vehicle Size".',') !== FALSE) { echo " checked"; } ?> value="Vehicle Size" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Vehicle Size
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Color".',') !== FALSE) { echo " checked"; } ?> value="Color" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Color
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Trim".',') !== FALSE) { echo " checked"; } ?> value="Trim" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Trim
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Fuel Type".',') !== FALSE) { echo " checked"; } ?> value="Fuel Type" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Fuel Tab
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Tire Type".',') !== FALSE) { echo " checked"; } ?> value="Tire Type" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Tire Tab
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Drive Train".',') !== FALSE) { echo " checked"; } ?> value="Drive Train" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Drive Train
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Total Kilometres".',') !== FALSE) { echo " checked"; } ?> value="Total Kilometres" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Total Kilometres
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ',Leased,') !== FALSE) { echo " checked"; } ?> value="Leased" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Leased
					<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ',Staff,') !== FALSE) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Staff

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

				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Serial #".',') !== FALSE) { echo " checked"; } ?> value="Serial #" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Serial #
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Unit #".',') !== FALSE) { echo " checked"; } ?> value="Unit #" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Unit #
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."VIN #".',') !== FALSE) { echo " checked"; } ?> value="VIN #" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;VIN #
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Licence Plate".',') !== FALSE) { echo " checked"; } ?> value="Licence Plate" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Licence Plate
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Label".',') !== FALSE) { echo " checked"; } ?> value="Label" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Label
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Nickname".',') !== FALSE) { echo " checked"; } ?> value="Nickname" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Nickname

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

				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Year Purchased".',') !== FALSE) { echo " checked"; } ?> value="Year Purchased" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Year Purchased
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Mileage".',') !== FALSE) { echo " checked"; } ?> value="Mileage" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Mileage
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Hours Operated".',') !== FALSE) { echo " checked"; } ?> value="Hours Operated" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Hours Operated

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

				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Cost".',') !== FALSE) { echo " checked"; } ?> value="Cost" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Cost
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."CDN Cost Per Unit".',') !== FALSE) { echo " checked"; } ?> value="CDN Cost Per Unit" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;CDN Cost Per Unit
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."USD Cost Per Unit".',') !== FALSE) { echo " checked"; } ?> value="USD Cost Per Unit" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;USD Cost Per Unit
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Finance".',') !== FALSE) { echo " checked"; } ?> value="Finance" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Finance
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Lease".',') !== FALSE) { echo " checked"; } ?> value="Lease" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Lease
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Insurance".',') !== FALSE) { echo " checked"; } ?> value="Insurance" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Insurance

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

				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Hourly Rate".',') !== FALSE) { echo " checked"; } ?> value="Hourly Rate" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Hourly Rate
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Daily Rate".',') !== FALSE) { echo " checked"; } ?> value="Daily Rate" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Daily Rate
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Semi-monthly Rate".',') !== FALSE) { echo " checked"; } ?> value="Semi-monthly Rate" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Semi-monthly Rate
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Monthly Rate".',') !== FALSE) { echo " checked"; } ?> value="Monthly Rate" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Monthly Rate
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Field Day Cost".',') !== FALSE) { echo " checked"; } ?> value="Field Day Cost" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Field Day Cost
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Field Day Billable".',') !== FALSE) { echo " checked"; } ?> value="Field Day Billable" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Field Day Billable
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."HR Rate Work".',') !== FALSE) { echo " checked"; } ?> value="HR Rate Work" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;HR Rate Work
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."HR Rate Travel".',') !== FALSE) { echo " checked"; } ?> value="HR Rate Travel" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;HR Rate Travel

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

				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Billing Rate".',') !== FALSE) { echo " checked"; } ?> value="Billing Rate" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Billing Rate
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Billed Hours".',') !== FALSE) { echo " checked"; } ?> value="Billed Hours" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Total Billed Time
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Billed Total".',') !== FALSE) { echo " checked"; } ?> value="Billed Total" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Total Billed Amount
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Expense Total".',') !== FALSE) { echo " checked"; } ?> value="Expense Total" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Total Expenses
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Profit Total".',') !== FALSE) { echo " checked"; } ?> value="Profit Total" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Total Profit

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

				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Next Service Date".',') !== FALSE) { echo " checked"; } ?> value="Next Service Date" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Next Service Date
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Next Service Hours".',') !== FALSE) { echo " checked"; } ?> value="Next Service Hours" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Next Service Hours
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Next Service Description".',') !== FALSE) { echo " checked"; } ?> value="Next Service Description" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Next Service Description
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Service Location".',') !== FALSE) { echo " checked"; } ?> value="Service Location" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Service Location
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Last Oil Filter Change (date)".',') !== FALSE) { echo " checked"; } ?> value="Last Oil Filter Change (date)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Last Oil Filter Change (date)
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Last Oil Filter Change (km)".',') !== FALSE) { echo " checked"; } ?> value="Last Oil Filter Change (km)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Last Oil Filter Change (km)
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Next Oil Filter Change (date)".',') !== FALSE) { echo " checked"; } ?> value="Next Oil Filter Change (date)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Next Oil Filter Change (date)
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Next Oil Filter Change (km)".',') !== FALSE) { echo " checked"; } ?> value="Next Oil Filter Change (km)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Next Oil Filter Change (km)
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Last Inspection & Tune Up (date)".',') !== FALSE) { echo " checked"; } ?> value="Last Inspection & Tune Up (date)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Last Inspection & Tune Up (date)
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Last Inspection & Tune Up (km)".',') !== FALSE) { echo " checked"; } ?> value="Last Inspection & Tune Up (km)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Last Inspection & Tune Up (km)
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Next Inspection & Tune Up (date)".',') !== FALSE) { echo " checked"; } ?> value="Next Inspection & Tune Up (date)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Next Inspection & Tune Up (date)
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Next Inspection & Tune Up (km)".',') !== FALSE) { echo " checked"; } ?> value="Next Inspection & Tune Up (km)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Next Inspection & Tune Up (km)
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Tire Condition".',') !== FALSE) { echo " checked"; } ?> value="Tire Condition" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Tire Condition
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Last Tire Rotation (date)".',') !== FALSE) { echo " checked"; } ?> value="Last Tire Rotation (date)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Last Tire Rotation (date)
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Last Tire Rotation (km)".',') !== FALSE) { echo " checked"; } ?> value="Last Tire Rotation (km)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Last Tire Rotation (km)
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Next Tire Rotation (date)".',') !== FALSE) { echo " checked"; } ?> value="Next Tire Rotation (date)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Next Tire Rotation (date)
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Next Tire Rotation (km)".',') !== FALSE) { echo " checked"; } ?> value="Next Tire Rotation (km)" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Next Tire Rotation (km)
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."CVIP Ticket Renewal Date".',') !== FALSE) { echo " checked"; } ?> value="CVIP Ticket Renewal Date" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;CVIP Ticket Renewal Date
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Service History Link".',') !== FALSE) { echo " checked"; } ?> value="Service History Link" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Service History Link

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

				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Classification Dropdown".',') !== FALSE) { echo " checked"; } ?> value="Classification Dropdown" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Classification
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Location Dropdown".',') !== FALSE) { echo " checked"; } ?> value="Location Dropdown" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Location (From Dropdown)
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Region Dropdown".',') !== FALSE) { echo " checked"; } ?> value="Region Dropdown" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Region
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Location".',') !== FALSE) { echo " checked"; } ?> value="Location" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Location
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."LSD".',') !== FALSE) { echo " checked"; } ?> value="LSD" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;LSD

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

				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Registration Card".',') !== FALSE) { echo " checked"; } ?> value="Registration Card" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Registration Card
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Registration Renewal date".',') !== FALSE) { echo " checked"; } ?> value="Registration Renewal date" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Registration Renewal Date

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

				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Insurance Company".',') !== FALSE) { echo " checked"; } ?> value="Insurance Company" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Insurance Provider
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Insurance Contact".',') !== FALSE) { echo " checked"; } ?> value="Insurance Contact" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Insurance Contact Name
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Insurance Phone".',') !== FALSE) { echo " checked"; } ?> value="Insurance Phone" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Insurance Phone Number
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Insurance Card".',') !== FALSE) { echo " checked"; } ?> value="Insurance Card" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Insurance Card
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Insurance Renewal Date".',') !== FALSE) { echo " checked"; } ?> value="Insurance Renewal Date" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Insurance Renewal Date

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

				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Status".',') !== FALSE) { echo " checked"; } ?> value="Status" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Status
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Ownership Status".',') !== FALSE) { echo " checked"; } ?> value="Ownership Status" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Ownership Status
				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Assigned Status".',') !== FALSE) { echo " checked"; } ?> value="Assigned Status" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Assigned Status

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

				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Quote Description".',') !== FALSE) { echo " checked"; } ?> value="Quote Description" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Quote Description

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

				<input type="checkbox" <?php if (strpos($equipment_dashboard_config, ','."Notes".',') !== FALSE) { echo " checked"; } ?> value="Notes" style="height: 20px; width: 20px;" name="equipment_dashboard[]">&nbsp;&nbsp;Notes

				</div>
			</div>
		</div>

	</div>

	<br>

	<div class="form-group pull-right">
		<a href="?category=Top" class="btn brand-btn">Back</a>
		<button	type="submit" name="inv_dashboard"	value="inv_dashboard" class="btn brand-btn">Submit</button>
	</div>
</form>