<?php

/*
Equipment Listing
*/
include ('../include.php');
error_reporting(0);
if(!empty($_FILES['upload']['name'])) {
	include('upload_csv.php');
}
?>
<script>
$(document).on('change', 'select[name="search_category"]', function() { location = this.value; });
function send_csv() {
	$('[name=upload]').change(function() {
		$('form').submit();
	});
	$('[name=upload]').click();
}
</script>
</head>

<body>

<?php include_once ('../navigation.php');
checkAuthorised('equipment');
$status = (empty($_GET['status']) ? 'Active' : $_GET['status']);
$equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
include_once('../Equipment/region_location_access.php');

// Add Default Configurations for the default Tabs if nothing is configured
$inspection_list = filter_var(implode('#*#', ["Oil","Coolant - Rad","Coolant Overflow","Hydraulic Oil","Hydraulic Oil - Leaks","Transmission Oil","Air Filters","Belts","Track SAG","Brake Emergency","Planetaries","Brake Pedal","Hydraulic Brake Fluid","Parking Brake","Defroster & Heaters","Emergency Equipment","Engine","Exhaust System","Fire Extinguisher","Fuel System","Generator/Alternator","Horn","Lights & Reflectors","Head - Stop Lights","Tail - Dash Lights","Blade","Bucket","Body Damage","Doors","Mirrors (Adjustment & Condition)","Oil Pressure","Radiator","Driver's Seat Belt & Seat Security","Cutting Edges","Ripper Teeth","Towing & Coupling Devices","Windshield & Windows","Windshield Washer & Wipers"]),FILTER_SANITIZE_STRING);
mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`,`value`) SELECT 'equipment_tabs', 'Truck,Trailer' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='equipment_tabs') num WHERE num.rows=0");
mysqli_query($dbc, "INSERT INTO `field_config_equipment` (`tab`, `equipment_dashboard`, `inspection_list`) SELECT 'Truck', 'Make,Model,Unit #,Service History Link,Registration Renewal date,Insurance Renewal Date,Service History Link,Status', '$inspection_list' FROM (SELECT COUNT(*) rows FROM `field_config_equipment` WHERE `equipment_dashboard` IS NOT NULL AND `tab`='Truck') num WHERE num.rows=0");
mysqli_query($dbc, "INSERT INTO `field_config_equipment` (`tab`, `accordion`, `order`, `equipment`) SELECT `tab`, `accordion`, `sort`, `field_names` FROM (SELECT 'Truck' `tab`, 'Equipment Information' `accordion`, '1' `sort`, 'Category,Make,Model,Model Year,Color,Unit #,VIN #' `field_names` UNION SELECT 'Truck', 'Registration', '2', 'Licence Plate,Registration Card,Registration Renewal date,Registration Reminder' UNION SELECT 'Truck', 'Insurance', '3', 'Insurance,Insurance Contact,Insurance Phone,Insurance Card,Insurance Renewal Date,Insurance Reminder' UNION SELECT 'Truck', 'Status', '4', 'Status') defaults INNER JOIN (SELECT COUNT(*) rows FROM `field_config_equipment` WHERE `equipment` IS NOT NULL AND `tab`='Truck') num WHERE num.rows=0");
mysqli_query($dbc, "INSERT INTO `field_config_equipment` (`tab`, `equipment_dashboard`, `inspection_list`) SELECT 'Trailer', 'Make,Model,Unit #,Registration Renewal date,Insurance Renewal Date,Service History Link,Status', '$inspection_list' FROM (SELECT COUNT(*) rows FROM `field_config_equipment` WHERE `equipment_dashboard` IS NOT NULL AND `tab`='Trailer') num WHERE num.rows=0");
mysqli_query($dbc, "INSERT INTO `field_config_equipment` (`tab`, `accordion`, `order`, `equipment`) SELECT `tab`, `accordion`, `sort`, `field_names` FROM (SELECT 'Trailer' `tab`, 'Equipment Information' `accordion`, '1' `sort`, 'Category,Make,Model,Model Year,Color,Unit #,VIN #' `field_names` UNION SELECT 'Trailer', 'Registration', '2', 'Licence Plate,Registration Card,Registration Renewal date,Registration Reminder' UNION SELECT 'Trailer', 'Insurance', '3', 'Insurance,Insurance Contact,Insurance Phone,Insurance Card,Insurance Renewal Date,Insurance Reminder' UNION SELECT 'Trailer', 'Status', '4', 'Status') defaults INNER JOIN (SELECT COUNT(*) rows FROM `field_config_equipment` WHERE `equipment` IS NOT NULL AND `tab`='Trailer') num WHERE num.rows=0");
?>
<div class="container">
    <div class="row">

    <div class="col-sm-10"><h1>Equipment: Assigned Equipment</h1></div>
	<div class="col-sm-2 double-gap-top">
		<?php
		if(config_visible_function($dbc, 'equipment') == 1) {
			echo '<a href="field_config_equipment.php?type=tab" class="mobile-block pull-right "><img style="width:45px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
            echo '<span class="popover-examples pull-right" style="margin:10px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the Settings within this tile. Any changes will appear on your dashboard."><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span>';
		} ?>
	</div>
	<div class="clearfix double-gap-bottom"></div>

	<div class="tab-container">
		<?php if ( in_array('Equipment',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'equipment') === TRUE ) { ?>
			<div class="tab pull-left">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and edit all Active Equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="equipment.php?category=Top&status=Active"><button type="button" class="btn brand-btn mobile-block">Active Equipment</button></a>
            </div>
		<?php } ?>
		<?php if ( in_array('Equipment',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'equipment') === TRUE ) { ?>
			<div class="tab pull-left">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and edit Inactive Equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="equipment.php?category=Top&status=Inactive"><button type="button" class="btn brand-btn mobile-block">Inactive Equipment</button></a>
            </div>
		<?php } ?>
		<?php if ( in_array('Inspection',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'inspection') === TRUE ) { ?>
			<div class="tab pull-left">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View all past and scheduled equipment inspections."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="inspections.php"><button type="button" class="btn brand-btn mobile-block">Inspections</button></a>
            </div>
		<?php } ?>
		<?php if ( in_array('Assign',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'assign') === TRUE ) { ?>
			<div class="tab pull-left">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and edit all Assigned equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="assign_equipment.php"><button type="button" class="btn brand-btn mobile-block active_tab">Assigned Equipment</button></a>
            </div>
		<?php } ?>
		<?php if ( in_array('Work Order',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'work_orders') === TRUE ) { ?>
			<div class="tab pull-left">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View the status of all Work Orders."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="work_orders.php"><button type="button" class="btn brand-btn mobile-block">Work Orders</button></a>
            </div>
		<?php } ?>
		<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
			<div class="tab pull-left">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View all Equipment Expenses."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="expenses.php"><button type="button" class="btn brand-btn mobile-block">Expenses</button></a>
            </div>
		<?php } ?>
        <?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
            <div class="tab pull-left">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and edit all Balance Sheets relating to a specific item of equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="balance.php"><button type="button" class="btn brand-btn mobile-block">Balance Sheets</button></a>
            </div>
        <?php } ?>
		<?php if ( in_array('Schedules',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'schedules') === TRUE ) { ?>
			<div class="tab pull-left">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View the scheduled service dates for equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="service_schedules.php"><button type="button" class="btn brand-btn mobile-block">Service Schedules</button></a>
            </div>
		<?php } ?>
		<?php if ( in_array('Requests',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'requests') === TRUE ) { ?>
			<div class="tab pull-left">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and add Services Requests for equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="service_request.php?category=Top"><button type="button" class="btn brand-btn mobile-block">Service Requests</button></a>
            </div>
		<?php } ?>
		<?php if ( in_array('Records',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'records') === TRUE ) { ?>
			<div class="tab pull-left">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and add Service Records for equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="service_record.php?category=Top"><button type="button" class="btn brand-btn mobile-block">Service Records</button></a>
            </div>
		<?php } ?>
		<?php if ( in_array('Checklists',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'checklist') === TRUE ) { ?>
			<div class="tab pull-left">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View, add and edit Equipment Checklists."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="equipment_checklist.php"><button type="button" class="btn brand-btn mobile-block">Checklists</button></a>
            </div>
		<?php } ?>
        <div class="clearfix"></div>
    </div>
    
    <div class="notice double-gap-bottom popover-examples">
        <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
        <div class="col-sm-11">
            <span class="notice-name">NOTE:</span>
            Here you can add and edit all equipment assignments
        </div>
        <div class="clearfix"></div>
    </div>
	
    <div class="gap-left tab-container col-sm-10">
		<?php
		$category = $_GET['category'];
		$each_tab = explode(',', get_config($dbc, 'equipment_tabs'));

        if (get_config($dbc, 'show_category_dropdown_equipment') == '1') { ?>
            <div class="row">
				<label class="control-label col-sm-2">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Filter equipment by Category."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    Category:
                </label>
				<div class="col-sm-4">
					<select name="search_category" class="chosen-select-deselect form-control mobile-100-pull-right category_actual">
						<option value="?category=Top">Top 25</option>
						<?php
							foreach ($each_tab as $cat_tab) {
								echo "<option ".(!empty($_GET['category']) && $_GET['category'] == $cat_tab ? 'selected' : '')." value='?category=".$cat_tab."'>".$cat_tab."</option>";
							}
						?>
					</select>
				</div>
            </div>
        <?php } else {
			echo "<a href='?category=Top'><button type='button' class='btn brand-btn mobile-block ".(empty($_GET['category']) || $_GET['category'] == 'Top' ? 'active_tab' : '')."' >Top 25</button></a>&nbsp;&nbsp;";
    		foreach ($each_tab as $cat_tab) {
    			echo "<a href='?category=".$cat_tab."'><button type='button' class='btn brand-btn mobile-block ".(!empty($_GET['category']) && $_GET['category'] == $cat_tab ? 'active_tab' : '')."' >".$cat_tab."</button></a>&nbsp;&nbsp;";
    		}
        } ?>
	</div>

    <?php if(vuaed_visible_function($dbc, 'equipment') == 1) {
		echo '<div class="gap-bottom pull-right">';
            echo '<span class="popover-examples" style="margin:0 2px;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a new equipment Assignment."><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span>';
            echo '<a href="add_assigned_equipment.php" class="btn brand-btn mobile-block">Add Assignment</a>';
        echo '</div>';
	} ?>

	<div class="clearfix double-gap-top"></div>

    <div id="no-more-tables"><?php
	$rowsPerPage = 25;
	$pageNum = 1;

	if(isset($_GET['page'])) {
		$pageNum = $_GET['page'];
	}

	$offset = ($pageNum - 1) * $rowsPerPage;
	$query = "SELECT COUNT(*) numrows FROM equipment WHERE deleted = 0 AND (`equipmentid` IN (SELECT `assign_to_equip` FROM `equipment`) OR `assigned_staff` > 0) AND (`category` = '".$_GET['category']."' OR '".$_GET['category']."' IN ('Top','')) $access_query";
	$query_check_credentials = "SELECT * FROM equipment WHERE deleted = 0 AND (`equipmentid` IN (SELECT `assign_to_equip` FROM `equipment`) OR `assigned_staff` > 0) AND (`category` = '".$_GET['category']."' OR '".$_GET['category']."' IN ('Top','')) $access_query LIMIT $offset, $rowsPerPage";
	$result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0) {
		// Added Pagination //
		echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
		// Pagination Finish //
		
		echo "<table class='table table-bordered'><tr>";
		echo "<th>Equipment</th>";
		echo "<th>Staff</th>";
		echo "<th>Assigned</th>";
		echo "<th>Function</th>";
		echo "</tr>";
        while($row = mysqli_fetch_array( $result )) {
			echo "<tr>";
			echo "<td data-title='Equipment'>".$row['category']." ".$row['make']." ".$row['model']." ".$row['unit_number']."</td>";
			echo "<td data-title='Staff'>".get_contact($dbc, $row['assigned_staff'])."</td>";
			echo "<td data-title='Assigned'>";
			$equipment_list = mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `assign_to_equip`='".$row['equipmentid']."'");
			while($equip_row = mysqli_fetch_array($equipment_list)) {
				echo $equip_row['category']." ".$equip_row['make']." ".$equip_row['model']." ".$equip_row['unit_number']."<br />";
			}
			echo "</td>";
			echo "<td data-title='Function'><a href='add_assigned_equipment.php?equipmentid=".$row['equipmentid']."'>Edit</a></td>";
			echo "</tr>";
		}
		echo '</table>';

		// Added Pagination //
		echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
		// Pagination Finish //
    } else {
		echo "<h2>No Assignments Found</h2>";
	}

    if(vuaed_visible_function($dbc, 'equipment') == 1) {
        echo '<div class="gap-bottom pull-right">';
            echo '<span class="popover-examples" style="margin:0 2px;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a new equipment Assignment."><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span>';
            echo '<a href="add_assigned_equipment.php" class="btn brand-btn mobile-block">Add Assignment</a>';
        echo '</div>';
	} ?>

</div>
</div>
</div>


<?php include ('../footer.php'); ?>
