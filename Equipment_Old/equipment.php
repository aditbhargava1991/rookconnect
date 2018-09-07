<?php

/*
Equipment Listing
*/
include ('../include.php');
error_reporting(0);
if($_GET['export']) {
  $file_name = "report_equipment_" . date("Y-m-d_m") . '.csv';

  ob_end_clean();
  $fp = fopen('php://output','w');
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename='.$file_name);

  $total = 0;
  $data[] = 'Category,Description,Type,Make,Model,Unit of Measure,Model Year,Equipment Label,Total Kilometres,Style,Vehicle Size,Color,Trim,Staff,Fuel Type,
	Tire Type,Drive Train,Serial #,Unit #,VIN #,Licence Plate,Nickname,Year Purchased,Mileage,Hours Operated,Cost,CDN Cost Per Unit,USD Cost Per Unit,
	inance,Lease,Insurance Renewal Date,Insurance Company,Insurance Contact,Insurance Phone,Hourly Rate,Daily Rate,Semi Monthly Rate,Monthly Rate,
	Field Day Cost,Field Day Billable,HR Rate Work,HR Rate Travel,Next Service Date,Next Service Hours,Next Service Description,Service Location,
	Last Oil Filter Change (date),Last Oil Filter Change (km),Next Oil Filter Change (date),Next Oil Filter Change (km),Last Inspection & Tune Up (date),
	Last Inspection & Tune Up (km),Next Inspection & Tune Up (date),Next Inspection & Tune Up (km),Tire Condition,Last Tire Rotation (date),
	Last Tire Rotation (km),Next Tire Rotation (date),Next Tire Rotation (km),Registration Renewal Date,Location,LSD,Status,Ownership Status,
	Quote Description,Notes,CVIP Ticket Renewal Date,Vehicle Access Code,Cargo,Lessor,Group,Use';

  $report_validation = mysqli_query($dbc,"SELECT * FROM equipment");

  $num_rows = mysqli_num_rows($report_validation);
  while($row_report = mysqli_fetch_array($report_validation)) {
      $row = '';
				$row .= $row_report['category'] . ',';
				$row .= $row_report['equ_description'] . ',';
				$row .= $row_report['type'] . ',';
				$row .= $row_report['make'] . ',';
				$row .= $row_report['model'] . ',';
				$row .= $row_report['submodel'] . ',';
				$row .= $row_report['model_year'] . ',';
				$row .= $row_report['label'] . ',';
				$row .= $row_report['total_kilometres'] . ',';
				$row .= $row_report['leased'] . ',';
				$row .= $row_report['style'] . ',';
				$row .= $row_report['vehicle_size'] . ',';
				 $row .= $row_report['color'] . ',';
				 $row .= $row_report['trim'] . ',';
				 $row .= $row_report['staffid'] . ',';
				 $row .= $row_report['fuel_type'] . ',';
				 $row .= $row_report['tire_type'] . ',';
				 $row .= $row_report['drive_train'] . ',';
				 $row .= $row_report['serial_number'] . ',';
				 $row .= $row_report['unit_number'] . ',';
				 $row .= $row_report['vin_number'] . ',';
				 $row .= $row_report['licence_plate'] . ',';
				 $row .= $row_report['nickname'] . ',';
				 $row .= $row_report['year_purchased'] . ',';
				 $row .= $row_report['mileage'] . ',';
				 $row .= $row_report['hours_operated'] . ',';
				 $row .= $row_report['cost'] . ',';
				 $row .= $row_report['cnd_cost_per_unit'] . ',';
				 $row .= $row_report['usd_cost_per_unit'] . ',';
				 $row .= $row_report['finance'] . ',';
				 $row .= $row_report['lease'] . ',';
				 $row .= $row_report['insurance'] . ',';
				 $row .= $row_report['insurance_contact'] . ',';
				 $row .= $row_report['insurance_phone'] . ',';
				 $row .= $row_report['hourly_rate'] . ',';
				 $row .= $row_report['daily_rate'] . ',';
				 $row .= $row_report['semi_monthly_rate'] . ',';
				 $row .= $row_report['monthly_rate'] . ',';
				 $row .= $row_report['field_day_cost'] . ',';
				 $row .= $row_report['field_day_billable'] . ',';
				 $row .= $row_report['hr_rate_work'] . ',';
				 $row .= $row_report['hr_rate_travel'] . ',';
				 $row .= $row_report['next_service_date'] . ',';
				 $row .= $row_report['next_service'] . ',';
				 $row .= $row_report['next_serv_desc'] . ',';
				 $row .= $row_report['service_location'] . ',';
				 $row .= $row_report['last_oil_filter_change_date'] . ',';
				 $row .= $row_report['last_oil_filter_change'] . ',';
				 $row .= $row_report['next_oil_filter_change_date'] . ',';
				 $row .= $row_report['next_oil_filter_change'] . ',';
				 $row .= $row_report['last_insp_tune_up_date'] . ',';
				 $row .= $row_report['last_insp_tune_up'] . ',';
				 $row .= $row_report['next_insp_tune_up_date'] . ',';
				 $row .= $row_report['next_insp_tune_up'] . ',';
				 $row .= $row_report['tire_condition'] . ',';
				 $row .= $row_report['last_tire_rotation_date'] . ',';
				 $row .= $row_report['last_tire_rotation'] . ',';
				 $row .= $row_report['next_tire_rotation_date'] . ',';
				 $row .= $row_report['next_tire_rotation'] . ',';
				 $row .= $row_report['reg_renewal_date'] . ',';
				 $row .= $row_report['insurance_renewal'] . ',';
				 $row .= $row_report['location'] . ',';
				 $row .= $row_report['lsd'] . ',';
				 $row .= $row_report['status'] . ',';
				 $row .= $row_report['ownership_status'] . ',';
				 $row .= $row_report['quote_description'] . ',';
				 $row .= $row_report['notes'] . ',';
				 $row .= $row_report['cvip_renewal_date'] . ',';
				 $row .= $row_report['vehicle_access_code'] . ',';
				 $row .= $row_report['cargo'] . ',';
				 $row .= $row_report['lessor'] . ',';
				 $row .= $row_report['group'] . ',';
				 $row .= $row_report['use'] . ',';
				 $data[] = $row;
  }

  foreach ($data as $line) {
      $val = explode(",", $line);
      fputcsv($fp, $val);
  }
  exit();
}
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

    <div class="col-sm-10"><h1><?= $status ?> Equipment</h1></div>
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
                <a href="equipment.php?category=Top&status=Active"><button type="button" class="btn brand-btn mobile-block <?= $status != 'Inactive' ? 'active_tab' : '' ?>">Active Equipment</button></a>
            </div>
		<?php } ?>
		<?php if ( in_array('Equipment',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'equipment') === TRUE ) { ?>
			<div class="tab pull-left">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and edit Inactive Equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="equipment.php?category=Top&status=Inactive"><button type="button" class="btn brand-btn mobile-block <?= $status == 'Inactive' ? 'active_tab' : '' ?>">Inactive Equipment</button></a>
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
                <a href="assign_equipment.php"><button type="button" class="btn brand-btn mobile-block">Assigned Equipment</button></a>
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
					<select name="search_category" class="chosen-select-deselect form-control mobile-100-pull-right category_actual" onchange="location = this.value;">
						<option value="?category=Top">Last 25 Added</option>
						<?php
							foreach ($each_tab as $cat_tab) {
								echo "<option ".(!empty($_GET['category']) && $_GET['category'] == $cat_tab ? 'selected' : '')." value='?status=".$_GET['status']."&category=".$cat_tab."'>".$cat_tab."</option>";
							}
						?>
					</select>
				</div>
            </div>
        <?php } else {
			echo "<a href='?category=Top'><button type='button' class='btn brand-btn mobile-block ".(empty($_GET['category']) || $_GET['category'] == 'Top' ? 'active_tab' : '')."' >Last 25 Added</button></a>&nbsp;&nbsp;";
    		foreach ($each_tab as $cat_tab) {
    			echo "<a href='?status=".$_GET['status']."&category=".$cat_tab."'><button type='button' class='btn brand-btn mobile-block ".(!empty($_GET['category']) && $_GET['category'] == $cat_tab ? 'active_tab' : '')."' >".$cat_tab."</button></a>&nbsp;&nbsp;";
    		}
        } ?>
	</div>
	<div class="clearfix"></div>

	<div class="notice double-gap-bottom popover-examples">
		<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
		<div class="col-sm-11"><span class="notice-name">NOTE:</span>
		Tracking and maintaining equipment is an essential element for every business. Through this section you’ll be able to Add/Edit/Archive equipment you wish to use throughout projects or capture essential data on.</div>
		<div class="clearfix"></div>
	</div>
	<div>
    <form name="form_sites" method="post" action="" class="form-inline" role="form" enctype="multipart/form-data">
        <center>
        <div class="form-group">
            <label for="site_name" class="col-sm-5 control-label">Search By Any:</label>
            <div class="col-sm-6">
			<?php if(isset($_POST['search_equipment_submit'])) { ?>
				<input type="text" name="search_equipment" value="<?php echo $_POST['search_equipment']?>" class="form-control">
			<?php } else { ?>
				<input type="text" name="search_equipment" class="form-control">
			<?php } ?>
            </div>
        </div>
        &nbsp;
            <button type="submit" name="search_equipment_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
            <button type="submit" name="display_all_equipment" value="Display All" class="btn brand-btn mobile-block">Display All</button>
        </center>

    <?php

    if(vuaed_visible_function($dbc, 'equipment') == 1) {
        if($_GET['category'] != 'Top') {
            echo '<div class="gap-bottom pull-right">';
                echo '<span class="popover-examples" style="margin:0 2px 0 8px;"><a data-toggle="tooltip" data-placement="top" title="Click here to add New Equipment."><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span>';
                echo '<a href="add_equipment.php?category='.$category.'" class="btn brand-btn mobile-block">Add Equipment</a>';
            echo '</div>';
        }
		echo '<input type="file" name="upload" style="display:none;" />';
		echo '<button type="button" name="send_upload" value="export" class="btn brand-btn mobile-block gap-bottom pull-right" onclick="export_csv();">Export CSV <img src="../img/csv.png" style="height:1em;"></button>';
		echo '<button type="submit" name="send_upload" value="upload" class="btn brand-btn mobile-block gap-bottom pull-right" onclick="send_csv(); return false;">Upload CSV <img src="../img/csv.png" style="height:1em;"></button>';
		echo "<a href='template.csv' class='pull-right'>Uploader <br />Template </a>";
		echo '<span class="popover-examples list-inline hide-on-mobile pull-right"><a style="margin:0 0 0 15px;" data-toggle="tooltip" data-placement="top" title="You can upload several pieces of equipment at once by entering them into the Template, and then uploading them using the Upload CSV button to the right. Note that not all fields in the spreadsheet need to be filled in."><img src="'.WEBSITE_URL.'/img/info.png" style="width:1em;"></a></span>';
    } ?>
	</form>
</div>
	<div class="clearfix double-gap-top"></div>

    <div id="no-more-tables"> <?php

 // Display Pager

    $equipment = '';

    if (isset($_POST['search_equipment_submit'])) {
        $equipment = $_POST['search_equipment'];

        if (!empty($_POST['search_equipment'])) {
            $equipment = $_POST['search_equipment'];
        }
        if (!empty($_POST['search_category'])) {
            $equipment = $_POST['search_category'];
        }
    }

    if (isset($_POST['display_all_equipment'])) {
        $equipment = '';
    }

    /* Pagination Counting */
    $rowsPerPage = 25;
    $pageNum = 1;

    if(isset($_GET['page'])) {
        $pageNum = $_GET['page'];
    }

    $offset = ($pageNum - 1) * $rowsPerPage;

	$status_search = '';
	if($status == 'Active') {
		$status_search = " AND IFNULL(`status`,'') NOT IN ('Inactive')";
	} else {
		$status_search = " AND IFNULL(`status`,'') IN ('Inactive')";
	}
    if($equipment != '') {
    	if(!empty($_GET['category'])) {
    		$category_query = " AND category='$category'";
    	}
        $query_check_credentials = "SELECT * FROM equipment WHERE deleted=0 $status_search AND (unit_number LIKE '%" . $equipment . "%' OR type LIKE '%" . $equipment . "%' OR category LIKE '%" . $equipment . "%' OR ownership_status LIKE '%" . $equipment . "%' OR make LIKE '%" . $equipment . "%' OR model LIKE '%" . $equipment . "%' OR model_year LIKE '%" . $equipment . "%' OR cost LIKE '%" . $equipment . "%' OR region LIKE '%" . $equipment . "%' OR location LIKE '%" . $equipment . "%' OR classification LIKE '%" . $equipment . "%') $category_query $access_query ORDER BY ABS(unit_number) LIMIT $offset, $rowsPerPage";
        $query = "SELECT count(*) as numrows FROM equipment WHERE deleted=0 $status_search AND (unit_number LIKE '%" . $equipment . "%' OR type LIKE '%" . $equipment . "%' OR category LIKE '%" . $equipment . "%' OR ownership_status LIKE '%" . $equipment . "%' OR make LIKE '%" . $equipment . "%' OR model LIKE '%" . $equipment . "%' OR model_year LIKE '%" . $equipment . "%' OR cost LIKE '%" . $equipment . "%' OR region LIKE '%" . $equipment . "%' OR location LIKE '%" . $equipment . "%' OR classification LIKE '%" . $equipment . "%') $category_query $access_query ORDER BY ABS(unit_number)";
    } else {
        if((empty($_GET['category'])) || ($_GET['category'] == 'Top')) {
            $query_check_credentials = "SELECT * FROM equipment WHERE deleted = 0 $status_search $access_query ORDER BY ABS(unit_number) DESC LIMIT 25";
        } else {
            $category = $_GET['category'];
            $query_check_credentials = "SELECT * FROM equipment WHERE deleted = 0 $status_search AND category='$category' $access_query ORDER BY ABS(unit_number) LIMIT $offset, $rowsPerPage";
            $query = "SELECT count(*) as numrows FROM equipment WHERE deleted = 0 $status_search AND category='$category'".$access_query;
        }
    }

    $result = mysqli_query($dbc, $query_check_credentials);

    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0) {
        if(empty($_GET['category']) || $_GET['category'] == 'Top') {
            $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment_dashboard FROM field_config_equipment WHERE `tab` NOT IN ('service_record','service_request') AND equipment_dashboard IS NOT NULL"));
            $value_config = ',Category,'.$get_field_config['equipment_dashboard'].',';
        } else {
            $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment_dashboard FROM field_config_equipment WHERE tab='$category' AND accordion IS NULL UNION SELECT equipment_dashboard FROM field_config_equipment WHERE `tab` NOT IN ('service_record','service_request') AND equipment_dashboard IS NOT NULL"));
			$value_config = ','.$get_field_config['equipment_dashboard'].',';
        }

        // Added Pagination //
        if(isset($query))
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
        // Pagination Finish //

        echo "<table class='table table-bordered'>";
        echo "<tr class='hidden-xs hidden-sm'>";
        if (strpos($value_config, ','."Unit #".',') !== FALSE) {
            echo '<th>Unit #</th>';
        }
        if (strpos($value_config, ','."VIN #".',') !== FALSE) {
            echo '<th>VIN #</th>';
        }
        if (strpos($value_config, ','."Serial #".',') !== FALSE) {
            echo '<th>Serial #</th>';
        }
        if (strpos($value_config, ','."Description".',') !== FALSE) {
            echo '<th>Description</th>';
        }
        if (strpos($value_config, ','."Category".',') !== FALSE) {
			echo '<th>Category</th>';
        } if (strpos($value_config, ','."Type".',') !== FALSE) {
            echo '<th>Type</th>';
        }
        if (strpos($value_config, ','."Make".',') !== FALSE) {
            echo '<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Make of this item of equipment as set in the equipment profile."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> Make</th>';
        }
        if (strpos($value_config, ','."Model".',') !== FALSE) {
            echo '<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Model of this item of equipment as set in the equipment profile."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> Model</th>';
        }
        if (strpos($value_config, ','."Unit of Measure".',') !== FALSE) {
            echo '<th>Unit of Measure</th>';
        }
        if (strpos($value_config, ','."Model Year".',') !== FALSE) {
            echo '<th>Model Year</th>';
        }
        if (strpos($value_config, ','."Label".',') !== FALSE) {
            echo '<th>Equipment Label</th>';
        }
		if (strpos($value_config, ','."Total Kilometres".',') !== FALSE) {
            echo '<th>Total Kilometres</th>';
        }
        if (strpos($value_config, ','."Style".',') !== FALSE) {
            echo '<th>Style</th>';
        }
        if (strpos($value_config, ','."Vehicle Size".',') !== FALSE) {
            echo '<th>Vehicle Size</th>';
        }
        if (strpos($value_config, ','."Color".',') !== FALSE) {
            echo '<th>Color</th>';
        }
        if (strpos($value_config, ','."Trim".',') !== FALSE) {
            echo '<th>Trim</th>';
        }
        if (strpos($value_config, ','."Staff".',') !== FALSE) {
            echo '<th>Staff</th>';
        }
        if (strpos($value_config, ','."Fuel Type".',') !== FALSE) {
            echo '<th>Fuel Type</th>';
        }
        if (strpos($value_config, ','."Tire Type".',') !== FALSE) {
            echo '<th>Tire Type</th>';
        }
        if (strpos($value_config, ','."Drive Train".',') !== FALSE) {
            echo '<th>Drive Train</th>';
        }
        if (strpos($value_config, ','."Truck Lease".',') !== FALSE) {
            echo '<th>Truck Lease</th>';
        }
        if (strpos($value_config, ','."Insurance".',') !== FALSE) {
            echo '<th>Insurance</th>';
        }

        if (strpos($value_config, ','."Licence Plate".',') !== FALSE) {
            echo '<th>Licence Plate</th>';
        }
        if (strpos($value_config, ','."Nickname".',') !== FALSE) {
            echo '<th>Nickname</th>';
        }
        if (strpos($value_config, ','."Year Purchased".',') !== FALSE) {
            echo '<th>Year Purchased</th>';
        }
        if (strpos($value_config, ','."Mileage".',') !== FALSE) {
            echo '<th>Mileage</th>';
        }
        if (strpos($value_config, ','."Hours Operated".',') !== FALSE) {
            echo '<th>Hours Operated</th>';
        }
        if (strpos($value_config, ','."Cost".',') !== FALSE) {
            echo '<th>Cost</th>';
        }
        if (strpos($value_config, ','."CDN Cost Per Unit".',') !== FALSE) {
            echo '<th>CDN Cost Per Unit</th>';
        }
        if (strpos($value_config, ','."USD Cost Per Unit".',') !== FALSE) {
            echo '<th>USD Cost Per Unit</th>';
        }
        if (strpos($value_config, ','."Finance".',') !== FALSE) {
            echo '<th>Finance</th>';
        }
        if (strpos($value_config, ','."Lease".',') !== FALSE) {
            echo '<th>Lease</th>';
        }
        if (strpos($value_config, ','."Insurance".',') !== FALSE) {
            echo '<th>Insurance</th>';
        }
        if (strpos($value_config, ','."Hourly Rate".',') !== FALSE) {
            echo '<th>Hourly Rate</th>';
        }
        if (strpos($value_config, ','."Daily Rate".',') !== FALSE) {
            echo '<th>Daily Rate</th>';
        }
        if (strpos($value_config, ','."Semi-monthly Rate".',') !== FALSE) {
            echo '<th>Semi-monthly Rate</th>';
        }
        if (strpos($value_config, ','."Monthly Rate".',') !== FALSE) {
            echo '<th>Monthly Rate</th>';
        }
        if (strpos($value_config, ','."Field Day Cost".',') !== FALSE) {
            echo '<th>Field Day Cost</th>';
        }
        if (strpos($value_config, ','."Field Day Billable".',') !== FALSE) {
            echo '<th>Field Day Billable</th>';
        }
        if (strpos($value_config, ','."HR Rate Work".',') !== FALSE) {
            echo '<th>HR Rate Work</th>';
        }
        if (strpos($value_config, ','."HR Rate Travel".',') !== FALSE) {
            echo '<th>HR Rate Travel</th>';
        }
        if (strpos($value_config, ','."Next Service Date".',') !== FALSE) {
            echo '<th>Next Service Date</th>';
        }
        if (strpos($value_config, ','."Next Service Hours".',') !== FALSE) {
            echo '<th>Next Service Hours</th>';
        }
        if (strpos($value_config, ','."Next Service Description".',') !== FALSE) {
            echo '<th>Next Service Description</th>';
        }
        if (strpos($value_config, ','."Service Location".',') !== FALSE) {
            echo '<th>Service Location</th>';
        }
        if (strpos($value_config, ','."Last Oil Filter Change (date)".',') !== FALSE) {
            echo '<th>Last Oil Filter Change (date)</th>';
        }
        if (strpos($value_config, ','."Last Oil Filter Change (km)".',') !== FALSE) {
            echo '<th>Last Oil Filter Change (km)</th>';
        }
        if (strpos($value_config, ','."Next Oil Filter Change (date)".',') !== FALSE) {
            echo '<th>Next Oil Filter Change (date)</th>';
        }

        if (strpos($value_config, ','."Next Oil Filter Change (km)".',') !== FALSE) {
            echo '<th>Next Oil Filter Change (km)</th>';
        }
        if (strpos($value_config, ','."Last Inspection & Tune Up (date)".',') !== FALSE) {
            echo '<th>Last Inspection & Tune Up (date)</th>';
        }
        if (strpos($value_config, ','."Last Inspection & Tune Up (km)".',') !== FALSE) {
            echo '<th>Last Inspection & Tune Up (km)</th>';
        }
        if (strpos($value_config, ','."Next Inspection & Tune Up (date)".',') !== FALSE) {
                    echo '<th>Next Inspection & Tune Up (date)</th>';
        }
        if (strpos($value_config, ','."Next Inspection & Tune Up (km)".',') !== FALSE) {
            echo '<th>Next Inspection & Tune Up (km)</th>';
        }
        if (strpos($value_config, ','."Tire Condition".',') !== FALSE) {
            echo '<th>Tire Condition</th>';
        }
        if (strpos($value_config, ','."Last Tire Rotation (date)".',') !== FALSE) {
            echo '<th>Last Tire Rotation (date)</th>';
        }
        if (strpos($value_config, ','."Last Tire Rotation (km)".',') !== FALSE) {
            echo '<th>Last Tire Rotation (km)</th>';
        }
        if (strpos($value_config, ','."Next Tire Rotation (date)".',') !== FALSE) {
            echo '<th>Next Tire Rotation (date)</th>';
        }
        if (strpos($value_config, ','."Next Tire Rotation (km)".',') !== FALSE) {
            echo '<th>Next Tire Rotation (km)</th>';
        }
        if (strpos($value_config, ','."Registration Renewal date".',') !== FALSE) {
            echo '<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Registration Rewnewal Date for this item of equipment as set in the equipment profile."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> Registration Renewal Date</th>';
        }
        if (strpos($value_config, ','."Insurance Renewal Date".',') !== FALSE) {
            echo '<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Insurance Renewal Date for this item of equipment as set in the equipment profile."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> Insurance Renewal Date</th>';
        }
		if (strpos($value_config, ','."CVIP Ticket Renewal Date".',') !== FALSE) {
            echo '<th>CVIP Ticket Renewal Date</th>';
        }
        if (strpos($value_config, ','."Region Dropdown".',') !== FALSE) {
        	echo '<th>Region</th>';
        }
        if (strpos($value_config, ','."Location Dropdown".',') !== FALSE) {
        	echo '<th>Location</th>';
        }
        if (strpos($value_config, ','."Classification Dropdown".',') !== FALSE) {
        	echo '<th>Classification</th>';
        }
        if (strpos($value_config, ','."Location".',') !== FALSE) {
            echo '<th>Location</th>';
        }
        if (strpos($value_config, ','."LSD".',') !== FALSE) {
            echo '<th>LSD</th>';
        }
        if (strpos($value_config, ','."Service History Link".',') !== FALSE) {
            echo '<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View the Service History for this item of equipment as set in the equipment profile."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> Service History</th>';
        }
        if (strpos($value_config, ','."Status".',') !== FALSE) {
            echo '<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Status of this item of equipment."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> Status</th>';
        }
        if (strpos($value_config, ','."Ownership Status".',') !== FALSE) {
            echo '<th>Ownership Status</th>';
        }
        if (strpos($value_config, ','."Assigned Status".',') !== FALSE) {
            echo '<th>Assigned Status</th>';
        }
        if (strpos($value_config, ','."Quote Description".',') !== FALSE) {
            echo '<th>Quote Description</th>';
        }
        if (strpos($value_config, ','."Notes".',') !== FALSE) {
            echo '<th>Notes</th>';
        }
            echo '<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Edit or Archive this item of equipment."><img src="'. WEBSITE_URL .'/img/info-w.png" width="18"></a></span> Function</th>';
            echo "</tr>";
        } else {
            echo "<h2>No Record Found.</h2>";
        }

        while($row = mysqli_fetch_array( $result )) {

        $color = '';
        if($row['status'] == 'In equipment') {
            $color = 'style="color: white;"';
        }
        if($row['status'] == 'In transit from vendor') {
            $color = 'style="color: red;"';
        }
        if($row['status'] == 'In transit between yards') {
            $color = 'style="color: blue;"';
        }
        if($row['status'] == 'Not confirmed in yard by equipment check') {
            $color = 'style="color: yellow;"';
        }
        if($row['status'] == 'Assigned to job') {
            $color = 'style="color: green;"';
        }
        if($row['status'] == 'In transit and assigned') {
            $color = 'style="color: purple;"';
        }

        echo '<tr '.$color.'>';
        if (strpos($value_config, ','."Unit #".',') !== FALSE) {
        echo '<td data-title="Unit #">' . $row['unit_number'] . '</td>';
        }
        if (strpos($value_config, ','."VIN #".',') !== FALSE) {
        echo '<td data-title="VIN #">' . $row['vin_number'] . '</td>';
        }
        if (strpos($value_config, ','."Serial #".',') !== FALSE) {
        echo '<td data-title="Serial #">' . $row['serial_number'] . '</td>';
        }
        if (strpos($value_config, ','."Description".',') !== FALSE) {
        echo '<td data-title="Description">' . $row['equ_description'] . '</td>';
        }
        if (strpos($value_config, ','."Category".',') !== FALSE) {
        echo '<td data-title="Category">' . $row['category'] . '</td>';
        }
        if (strpos($value_config, ','."Type".',') !== FALSE) {
        echo '<td data-title="Type">' . $row['type'] . '</td>';
        }
        if (strpos($value_config, ','."Make".',') !== FALSE) {
        echo '<td data-title="Make">' . $row['make'] . '</td>';
        }
        if (strpos($value_config, ','."Model".',') !== FALSE) {
        echo '<td data-title="Model">' . $row['model'] . '</td>';
        }
        if (strpos($value_config, ','."Unit of Measure".',') !== FALSE) {
        echo '<td data-title="Unit of Measure">' . $row['submodel'] . '</td>';
        }
        if (strpos($value_config, ','."Model Year".',') !== FALSE) {
        echo '<td data-title="Model Year">' . $row['model_year'] . '</td>';
        }
        if (strpos($value_config, ','."Label".',') !== FALSE) {
        echo '<td data-title="Equipment Label">' . $row['label'] . '</td>';
        }
		if (strpos($value_config, ','."Total Kilometres".',') !== FALSE) {
        echo '<td data-title="Total Kilometres">' . $row['total_kilometres'] . '</td>';
        }
		if (strpos($value_config, ','."Total Kilometres".',') !== FALSE) {
        echo '<td data-title="Leased">' . ($row['leased'] > 0 ? 'Yes' : 'No') . '</td>';
        }
        if (strpos($value_config, ','."Style".',') !== FALSE) {
        echo '<td data-title="Style">' . $row['style'] . '</td>';
        }
        if (strpos($value_config, ','."Vehicle Size".',') !== FALSE) {
        echo '<td data-title="Vehicle Size">' . $row['vehicle_size'] . '</td>';
        }
        if (strpos($value_config, ','."Color".',') !== FALSE) {
        echo '<td data-title="Color">' . $row['color'] . '</td>';
        }
        if (strpos($value_config, ','."Trim".',') !== FALSE) {
        echo '<td data-title="Trim">' . $row['trim'] . '</td>';
        }
        if (strpos($value_config, ','."Staff".',') !== FALSE) {
        $staff_list = [];
        foreach(explode(',', $row['staffid']) as $staff_id) {
        	if($staff_id > 0) {
        		$staff_list[] = get_contact($dbc, $staff_id);
        	}
        }
        echo '<td data-title="Staff">' . implode(', ', $staff_list) . '</td>';
        }

        if (strpos($value_config, ','."Fuel Type".',') !== FALSE) {
        echo '<td data-title="Fuel Type">' . $row['fuel_type'] . '</td>';
        }

        if (strpos($value_config, ','."Tire Type".',') !== FALSE) {
        echo '<td data-title="Tire Type">' . $row['tire_type'] . '</td>';
        }
        if (strpos($value_config, ','."Drive Train".',') !== FALSE) {
        echo '<td data-title="Drive Train">' . $row['drive_train'] . '</td>';
        }
        if (strpos($value_config, ','."Licence Plate".',') !== FALSE) {
        echo '<td data-title="Licence Plate">' . $row['licence_plate'] . '</td>';
        }

        if (strpos($value_config, ','."Nickname".',') !== FALSE) {
        echo '<td data-title="Nickname">' . $row['nickname'] . '</td>';
        }
        if (strpos($value_config, ','."Year Purchased".',') !== FALSE) {
        echo '<td data-title="Year Purchased">' . $row['year_purchased'] . '</td>';

        }

        if (strpos($value_config, ','."Mileage".',') !== FALSE) {
        echo '<td data-title="Mileage">' . $row['mileage'] . '</td>';
        }
        if (strpos($value_config, ','."Hours Operated".',') !== FALSE) {
        echo '<td data-title="Hours Operated">' . $row['hours_operated'] . '</td>';
        }

        if (strpos($value_config, ','."Cost".',') !== FALSE) {
        echo '<td data-title="Cost">' . $row['cost'] . '</td>';
        }
        if (strpos($value_config, ','."CDN Cost Per Unit".',') !== FALSE) {
        echo '<td data-title="CDN Cost Per Unit">' . $row['cnd_cost_per_unit'] . '</td>';
        }

        if (strpos($value_config, ','."USD Cost Per Unit".',') !== FALSE) {
        echo '<td data-title="USD Cost Per Unit">' . $row['usd_cost_per_unit'] . '</td>';
        }
        if (strpos($value_config, ','."Finance".',') !== FALSE) {
        echo '<td data-title="Finance">' . $row['finance'] . '</td>';
        }
        if (strpos($value_config, ','."Lease".',') !== FALSE) {

        echo '<td data-title="Lease">' . $row['lease'] . '</td>';
        }
        if (strpos($value_config, ','."Insurance".',') !== FALSE) {

        echo '<td data-title="Insurance">' . $row['insurance'] . '</td>';
        }
        if (strpos($value_config, ','."Hourly Rate".',') !== FALSE) {
        echo '<td data-title="Hourly Rate">' . $row['hourly_rate'] . '</td>';
        }
        if (strpos($value_config, ','."Daily Rate".',') !== FALSE) {
        echo '<td data-title="Daily Rate">' . $row['daily_rate'] . '</td>';
        }
        if (strpos($value_config, ','."Semi-monthly Rate".',') !== FALSE) {
        echo '<td data-title="Semi-monthly Rate">' . $row['semi_monthly_rate'] . '</td>';
        }
        if (strpos($value_config, ','."Monthly Rate".',') !== FALSE) {
        echo '<td data-title="Monthly Rate">' . $row['monthly_rate'] . '</td>';
        }
        if (strpos($value_config, ','."Field Day Cost".',') !== FALSE) {
        echo '<td data-title="Field Day Cost">' . $row['field_day_cost'] . '</td>';
        }
        if (strpos($value_config, ','."Field Day Billable".',') !== FALSE) {
        echo '<td data-title="Field Day Billable">' . $row['field_day_billable'] . '</td>';
        }
        if (strpos($value_config, ','."HR Rate Work".',') !== FALSE) {
        echo '<td data-title="HR Rate Work">' . $row['hr_rate_work'] . '</td>';
        }
        if (strpos($value_config, ','."HR Rate Travel".',') !== FALSE) {
        echo '<td data-title="HR Rate Travel">' . $row['hr_rate_travel'] . '</td>';
        }
        if (strpos($value_config, ','."Next Service Date".',') !== FALSE) {
        echo '<td data-title="Next Service Date">' . $row['next_service_date'] . '</td>';
        }
        if (strpos($value_config, ','."Next Service Hours".',') !== FALSE) {
        echo '<td data-title="Next Service Hours">' . $row['next_service'] . '</td>';
        }
        if (strpos($value_config, ','."Next Service Description".',') !== FALSE) {
        echo '<td data-title="Next Service Description">' . $row['next_serv_desc'] . '</td>';
        }
        if (strpos($value_config, ','."Service Location".',') !== FALSE) {
        echo '<td data-title="Service Location">' . $row['service_location'] . '</td>';

        }

        if (strpos($value_config, ','."Last Oil Filter Change (date)".',') !== FALSE) {
        echo '<td data-title="Last Oil Filter Change (date)">' . $row['last_oil_filter_change_date'] . '</td>';
        }
        if (strpos($value_config, ','."Last Oil Filter Change (km)".',') !== FALSE) {
        echo '<td data-title="Last Oil Filter Change (km)">' . $row['last_oil_filter_change'] . '</td>';
        }

        if (strpos($value_config, ','."Next Oil Filter Change (date)".',') !== FALSE) {
        echo '<td data-title="Next Oil Filter Change (date)">' . $row['next_oil_filter_change_date'] . '</td>';
        }

        if (strpos($value_config, ','."Next Oil Filter Change (km)".',') !== FALSE) {

        echo '<td data-title="Next Oil Filter Change (km)">' . $row['next_oil_filter_change'] . '</td>';

        }
        if (strpos($value_config, ','."Last Inspection & Tune Up (date)".',') !== FALSE) {
                    echo '<td data-title="Last Inspection & Tune Up (date)">' . $row['last_insp_tune_up_date'] . '</td>';
        }

        if (strpos($value_config, ','."Last Inspection & Tune Up (km)".',') !== FALSE) {
        echo '<td data-title="Last Inspection & Tune Up (km)">' . $row['last_insp_tune_up'] . '</td>';
        }
        if (strpos($value_config, ','."Next Inspection & Tune Up (date)".',') !== FALSE) {
                    echo '<td data-title="Next Inspection & Tune Up (date)">' . $row['next_insp_tune_up_date'] . '</td>';
        }
        if (strpos($value_config, ','."Next Inspection & Tune Up (km)".',') !== FALSE) {
        echo '<td data-title="Next Inspection & Tune Up (km)">' . $row['next_insp_tune_up'] . '</td>';
        }
        if (strpos($value_config, ','."Tire Condition".',') !== FALSE) {
        echo '<td data-title="Tire Condition">' . $row['tire_condition'] . '</td>';
        }
        if (strpos($value_config, ','."Last Tire Rotation (date)".',') !== FALSE) {
        echo '<td data-title="Last Tire Rotation (date)">' . $row['last_tire_rotation_date'] . '</td>';
        }
        if (strpos($value_config, ','."Last Tire Rotation (km)".',') !== FALSE) {
        echo '<td data-title="Last Tire Rotation (km)">' . $row['last_tire_rotation'] . '</td>';
        }
        if (strpos($value_config, ','."Next Tire Rotation (date)".',') !== FALSE) {
        echo '<td data-title="Next Tire Rotation (date)">' . $row['next_tire_rotation_date'] . '</td>';
 }
        if (strpos($value_config, ','."Next Tire Rotation (km)".',') !== FALSE) {
        echo '<td data-title="Next Tire Rotation (km)">' . $row['next_tire_rotation'] . '</td>';
        }
        if (strpos($value_config, ','."Registration Renewal date".',') !== FALSE) {
        echo '<td data-title="Registration Renewal date"><a href="add_equipment.php?equipmentid='.$row['equipmentid'].'&target_field=reg_renewal_date">' . $row['reg_renewal_date'] . '</a></td>';
        }
        if (strpos($value_config, ','."Insurance Renewal Date".',') !== FALSE) {
        echo '<td data-title="Insurance Renewal Date"><a href="add_equipment.php?equipmentid='.$row['equipmentid'].'&target_field=insurance_renewal">' . $row['insurance_renewal'] . '</a></td>';
        }
		if (strpos($value_config, ','."CVIP Ticket Renewal Date".',') !== FALSE) {
             echo '<td data-title="CVIP Ticket Renewal Date">' . $row['cvip_renewal_date'] . '</td>';
        }
        if (strpos($value_config, ','."Region Dropdown".',') !== FALSE) {
        echo '<td data-title="Region">' . implode(', ',explode('*#*',$row['region'])) . '</td>';
        }
        if (strpos($value_config, ','."Location Dropdown".',') !== FALSE) {
        echo '<td data-title="Location">' . implode(', ',explode('*#*',$row['location'])) . '</td>';
        }
        if (strpos($value_config, ','."Classification Dropdown".',') !== FALSE) {
        echo '<td data-title="Classification">' . implode(', ',explode('*#*',$row['classification'])) . '</td>';
        }
        if (strpos($value_config, ','."Location".',') !== FALSE) {
        echo '<td data-title="Location">' . $row['location'] . '</td>';
        }

        if (strpos($value_config, ','."LSD".',') !== FALSE) {
        echo '<td data-title="LSD">' . $row['lsd'] . '</td>';
        }
        if (strpos($value_config, ','."Service History Link".',') !== FALSE) {
        echo '<td data-title="Service History"><a href="equipment_service.php?equipmentid='.$row['equipmentid'].'">View</a></td>';
        }
        if (strpos($value_config, ','."Status".',') !== FALSE) {
        echo '<td data-title="status">' . $row['status'] . '</td>';
        }
        if (strpos($value_config, ','."Ownership Status".',') !== FALSE) {
        echo '<td data-title="Ownership Status">' . $row['ownership_status'] . '</td>';
        }
        if (strpos($value_config, ','."Assigned Status".',') !== FALSE) {
        echo '<td data-title="Assigned Status">' . $row['assigned_status'] . '</td>';
        }
        if (strpos($value_config, ','."Quote Description".',') !== FALSE) {
        echo '<td data-title="Quote Description">' . html_entity_decode($row['quote_description']) . '</td>';
        }
        if (strpos($value_config, ','."Notes".',') !== FALSE) {
        echo '<td data-title="Notes">' . $row['notes'] . '</td>';
        }
        echo '<td data-title="Function">';

        if(vuaed_visible_function($dbc, 'equipment') == 1) {
            echo '<a href=\'add_equipment.php?equipmentid='.$row['equipmentid'].'\'>Edit</a> | '; echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&equipmentid='.$row['equipmentid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
        }
        echo '</td>';

        echo "</tr>";
    }
    echo '</table>';

    // Added Pagination //
    if(isset($query))
        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
    // Pagination Finish //

    if(vuaed_visible_function($dbc, 'equipment') == 1) {
        if($_GET['category'] != 'Top') {
            echo '<div class="gap-bottom pull-right">';
                echo '<span class="popover-examples" style="margin:0 2px;"><a data-toggle="tooltip" data-placement="top" title="Click here to add New Equipment."><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span>';
                echo '<a href="add_equipment.php?category='.$category.'" class="btn brand-btn mobile-block">Add Equipment</a>';
            echo '</div>';
        }

    }

    ?>

</div>
</div>
</div>
<script type="text/javascript">
	function export_csv()
	{
		//var hreflocation = window.location.href;
		window.location.href += "&export=yes";
	}
</script>


<?php include ('../footer.php'); ?>
