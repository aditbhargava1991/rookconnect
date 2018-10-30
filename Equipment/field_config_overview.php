<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');

if (isset($_POST['equip_overview'])) {
	$tab_overview = filter_var($_POST['tab_overview'],FILTER_SANITIZE_STRING);
	$equipment_overview = implode(',',$_POST['equipment_overview']);
    set_config($dbc, 'equipment_overview_'.config_safe_str($tab_overview), $equipment_overview);
    
	echo '<script type="text/javascript"> window.location.replace("?settings=overview&tab='.$tab_overview.'"); </script>';
}
?>
<script type="text/javascript">
$(document).ready(function() {
	$("#tab_overview").change(function() {
		window.location = '?settings=overview&tab='+this.value;
	});
});
</script>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<?php $equip_type = $_GET['tab']; ?>

	<div class="form-group">
		<label for="fax_number"	class="col-sm-4	control-label">Tabs:</label>
		<div class="col-sm-8">
			<select data-placeholder="Select Tab..." id="tab_overview" name="tab_overview" class="chosen-select-deselect form-control" width="380">
				<option value=""></option>
				<?php
				$tabs = get_config($dbc, 'equipment_tabs');
				$each_tab = explode(',', $tabs);
				foreach ($each_tab as $cat_tab) {
					if ($equip_type == $cat_tab) {
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

    <?php if(!empty($equip_type)) {
        $equipment_overview_config = get_config($dbc, 'equipment_overview_'.config_safe_str($equip_type)); ?>
        <h4>Overview</h4>
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
                        <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Description".',') !== FALSE) { echo " checked"; } ?> value="Description" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Description
                        <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Category".',') !== FALSE) { echo " checked"; } ?> value="Category" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Category
                        <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Type".',') !== FALSE) { echo " checked"; } ?> value="Type" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Tab
                        <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Make".',') !== FALSE) { echo " checked"; } ?> value="Make" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Make
                        <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Model".',') !== FALSE) { echo " checked"; } ?> value="Model" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Model
                        <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Unit of Measure".',') !== FALSE) { echo " checked"; } ?> value="Unit of Measure" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Unit of Measure
                        <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Model Year".',') !== FALSE) { echo " checked"; } ?> value="Model Year" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Model Year
                        <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Style".',') !== FALSE) { echo " checked"; } ?> value="Style" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Style
                        <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Vehicle Size".',') !== FALSE) { echo " checked"; } ?> value="Vehicle Size" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Vehicle Size
                        <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Color".',') !== FALSE) { echo " checked"; } ?> value="Color" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Color
                        <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Trim".',') !== FALSE) { echo " checked"; } ?> value="Trim" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Trim
                        <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Fuel Type".',') !== FALSE) { echo " checked"; } ?> value="Fuel Type" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Fuel Tab
                        <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Tire Type".',') !== FALSE) { echo " checked"; } ?> value="Tire Type" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Tire Tab
                        <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Drive Train".',') !== FALSE) { echo " checked"; } ?> value="Drive Train" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Drive Train
                        <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Total Kilometres".',') !== FALSE) { echo " checked"; } ?> value="Total Kilometres" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Total Kilometres
                        <input type="checkbox" <?php if (strpos($equipment_overview_config, ',Leased,') !== FALSE) { echo " checked"; } ?> value="Leased" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Leased
                        <input type="checkbox" <?php if (strpos($equipment_overview_config, ',Staff,') !== FALSE) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Staff

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

                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Serial #".',') !== FALSE) { echo " checked"; } ?> value="Serial #" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Serial #
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Unit #".',') !== FALSE) { echo " checked"; } ?> value="Unit #" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Unit #
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."VIN #".',') !== FALSE) { echo " checked"; } ?> value="VIN #" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;VIN #
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Licence Plate".',') !== FALSE) { echo " checked"; } ?> value="Licence Plate" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Licence Plate
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Label".',') !== FALSE) { echo " checked"; } ?> value="Label" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Label
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Nickname".',') !== FALSE) { echo " checked"; } ?> value="Nickname" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Nickname

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

                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Year Purchased".',') !== FALSE) { echo " checked"; } ?> value="Year Purchased" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Year Purchased
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Mileage".',') !== FALSE) { echo " checked"; } ?> value="Mileage" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Mileage
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Hours Operated".',') !== FALSE) { echo " checked"; } ?> value="Hours Operated" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Hours Operated

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

                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Cost".',') !== FALSE) { echo " checked"; } ?> value="Cost" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Cost
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."CDN Cost Per Unit".',') !== FALSE) { echo " checked"; } ?> value="CDN Cost Per Unit" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;CDN Cost Per Unit
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."USD Cost Per Unit".',') !== FALSE) { echo " checked"; } ?> value="USD Cost Per Unit" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;USD Cost Per Unit
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Finance".',') !== FALSE) { echo " checked"; } ?> value="Finance" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Finance
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Lease".',') !== FALSE) { echo " checked"; } ?> value="Lease" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Lease
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Insurance".',') !== FALSE) { echo " checked"; } ?> value="Insurance" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Insurance

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

                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Hourly Rate".',') !== FALSE) { echo " checked"; } ?> value="Hourly Rate" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Hourly Rate
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Daily Rate".',') !== FALSE) { echo " checked"; } ?> value="Daily Rate" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Daily Rate
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Semi-monthly Rate".',') !== FALSE) { echo " checked"; } ?> value="Semi-monthly Rate" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Semi-monthly Rate
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Monthly Rate".',') !== FALSE) { echo " checked"; } ?> value="Monthly Rate" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Monthly Rate
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Field Day Cost".',') !== FALSE) { echo " checked"; } ?> value="Field Day Cost" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Field Day Cost
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Field Day Billable".',') !== FALSE) { echo " checked"; } ?> value="Field Day Billable" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Field Day Billable
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."HR Rate Work".',') !== FALSE) { echo " checked"; } ?> value="HR Rate Work" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;HR Rate Work
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."HR Rate Travel".',') !== FALSE) { echo " checked"; } ?> value="HR Rate Travel" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;HR Rate Travel

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

                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Billing Rate".',') !== FALSE) { echo " checked"; } ?> value="Billing Rate" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Billing Rate
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Billed Hours".',') !== FALSE) { echo " checked"; } ?> value="Billed Hours" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Total Billed Time
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Billed Total".',') !== FALSE) { echo " checked"; } ?> value="Billed Total" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Total Billed Amount
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Expense Total".',') !== FALSE) { echo " checked"; } ?> value="Expense Total" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Total Expenses
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Profit Total".',') !== FALSE) { echo " checked"; } ?> value="Profit Total" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Total Profit

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

                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Next Service Date".',') !== FALSE) { echo " checked"; } ?> value="Next Service Date" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Next Service Date
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Next Service Hours".',') !== FALSE) { echo " checked"; } ?> value="Next Service Hours" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Next Service Hours
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Next Service Description".',') !== FALSE) { echo " checked"; } ?> value="Next Service Description" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Next Service Description
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Service Location".',') !== FALSE) { echo " checked"; } ?> value="Service Location" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Service Location
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Last Oil Filter Change (date)".',') !== FALSE) { echo " checked"; } ?> value="Last Oil Filter Change (date)" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Last Oil Filter Change (date)
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Last Oil Filter Change (km)".',') !== FALSE) { echo " checked"; } ?> value="Last Oil Filter Change (km)" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Last Oil Filter Change (km)
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Next Oil Filter Change (date)".',') !== FALSE) { echo " checked"; } ?> value="Next Oil Filter Change (date)" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Next Oil Filter Change (date)
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Next Oil Filter Change (km)".',') !== FALSE) { echo " checked"; } ?> value="Next Oil Filter Change (km)" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Next Oil Filter Change (km)
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Last Inspection & Tune Up (date)".',') !== FALSE) { echo " checked"; } ?> value="Last Inspection & Tune Up (date)" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Last Inspection & Tune Up (date)
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Last Inspection & Tune Up (km)".',') !== FALSE) { echo " checked"; } ?> value="Last Inspection & Tune Up (km)" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Last Inspection & Tune Up (km)
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Next Inspection & Tune Up (date)".',') !== FALSE) { echo " checked"; } ?> value="Next Inspection & Tune Up (date)" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Next Inspection & Tune Up (date)
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Next Inspection & Tune Up (km)".',') !== FALSE) { echo " checked"; } ?> value="Next Inspection & Tune Up (km)" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Next Inspection & Tune Up (km)
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Tire Condition".',') !== FALSE) { echo " checked"; } ?> value="Tire Condition" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Tire Condition
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Last Tire Rotation (date)".',') !== FALSE) { echo " checked"; } ?> value="Last Tire Rotation (date)" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Last Tire Rotation (date)
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Last Tire Rotation (km)".',') !== FALSE) { echo " checked"; } ?> value="Last Tire Rotation (km)" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Last Tire Rotation (km)
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Next Tire Rotation (date)".',') !== FALSE) { echo " checked"; } ?> value="Next Tire Rotation (date)" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Next Tire Rotation (date)
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Next Tire Rotation (km)".',') !== FALSE) { echo " checked"; } ?> value="Next Tire Rotation (km)" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Next Tire Rotation (km)
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."CVIP Ticket Renewal Date".',') !== FALSE) { echo " checked"; } ?> value="CVIP Ticket Renewal Date" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;CVIP Ticket Renewal Date
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Service History Link".',') !== FALSE) { echo " checked"; } ?> value="Service History Link" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Service History Link

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

                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Classification Dropdown".',') !== FALSE) { echo " checked"; } ?> value="Classification Dropdown" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Classification
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Location Dropdown".',') !== FALSE) { echo " checked"; } ?> value="Location Dropdown" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Location (From Dropdown)
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Region Dropdown".',') !== FALSE) { echo " checked"; } ?> value="Region Dropdown" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Region
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Location".',') !== FALSE) { echo " checked"; } ?> value="Location" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Location
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."LSD".',') !== FALSE) { echo " checked"; } ?> value="LSD" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;LSD

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

                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Registration Card".',') !== FALSE) { echo " checked"; } ?> value="Registration Card" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Registration Card
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Registration Renewal date".',') !== FALSE) { echo " checked"; } ?> value="Registration Renewal date" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Registration Renewal Date

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

                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Insurance Company".',') !== FALSE) { echo " checked"; } ?> value="Insurance Company" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Insurance Provider
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Insurance Contact".',') !== FALSE) { echo " checked"; } ?> value="Insurance Contact" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Insurance Contact Name
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Insurance Phone".',') !== FALSE) { echo " checked"; } ?> value="Insurance Phone" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Insurance Phone Number
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Insurance Card".',') !== FALSE) { echo " checked"; } ?> value="Insurance Card" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Insurance Card
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Insurance Renewal Date".',') !== FALSE) { echo " checked"; } ?> value="Insurance Renewal Date" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Insurance Renewal Date

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

                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Status".',') !== FALSE) { echo " checked"; } ?> value="Status" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Status
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Ownership Status".',') !== FALSE) { echo " checked"; } ?> value="Ownership Status" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Ownership Status
                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Assigned Status".',') !== FALSE) { echo " checked"; } ?> value="Assigned Status" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Assigned Status

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

                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Quote Description".',') !== FALSE) { echo " checked"; } ?> value="Quote Description" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Quote Description

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

                    <input type="checkbox" <?php if (strpos($equipment_overview_config, ','."Notes".',') !== FALSE) { echo " checked"; } ?> value="Notes" style="height: 20px; width: 20px;" name="equipment_overview[]">&nbsp;&nbsp;Notes

                    </div>
                </div>
            </div>

        </div>
    <?php } else { ?>
        <h4>Please select a tab</h4>
    <?php } ?>

	<br>

	<div class="form-group pull-right">
		<a href="?category=Top" class="btn brand-btn">Back</a>
		<button	type="submit" name="equip_overview"	value="equip_overview" class="btn brand-btn">Submit</button>
	</div>
</form>