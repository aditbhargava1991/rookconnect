<?php
/*
Add	Inventory
*/
include ('../include.php');
error_reporting(0); ?>
</head>

<body>
<?php include_once ('../navigation.php');
$equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
checkAuthorised('equipment');
include_once('../Equipment/region_location_access.php'); ?>
<div class="container">
  <div class="row">

		<div class="col-sm-10"><h1>Equipment: Inspections</h1></div>
		<div class="col-sm-2 double-gap-top">
			<?php
			if(config_visible_function($dbc, 'equipment') == 1) {
				echo '<a href="field_config_equipment.php?type=tab" class="mobile-block pull-right "><img style="width:45px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
                echo '<span class="popover-examples pull-right" style="margin:10px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the Settings within this tile. Any changes will appear on your dashboard."><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span>';
			} ?>
		</div>
		<div class="clearfix double-gap-bottom"></div>

		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

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
                    <a href="inspections.php"><button type="button" class="btn brand-btn mobile-block active_tab">Inspections</button></a>
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
		
		<?php $search_staff = $_SESSION['contactid'];
		$search_date_from = date('Y-m-d',strtotime('-1 month'));
		$search_date_to = date('Y-m-d');
		$search_type = '';
		if(search_visible_function($dbc, 'field_job') == 1 && isset($_POST['search_staff'])) {
			$search_staff = $_POST['search_staff'];
		}
		if(isset($_POST['search_date_from'])) {
			$search_date_from = $_POST['search_date_from'];
		}
		if(isset($_POST['search_date_to'])) {
			$search_date_to = $_POST['search_date_to'];
		}
		if(isset($_POST['search_type'])) {
			$search_type = $_POST['search_type'];
		}
		if (isset($_POST['display_all_inventory'])) {
			$search_staff = $_SESSION['contactid'];
			$search_date_from = date('Y-m-d',strtotime('-1 month'));
			$search_date_to = date('Y-m-d');
			$search_type = '';
		}
		$query = "SELECT * FROM `equipment_inspections` LEFT JOIN `equipment` ON `equipment`.`equipmentid` = `equipment_inspections`.`equipmentid` WHERE (`equipment_inspections`.`staffid`='$search_staff' OR '$search_staff'='ALL') $access_query";
		if(!empty($search_type)) {
			$query .= " AND `type`='$search_type'";
		}
		if(!empty($search_date_from)) {
			$query .= " AND `date` >= '$search_date_from'";
		}
		if(!empty($search_date_to)) {
			$query .= " AND `date` <= '$search_date_to 23:59:59'";
		}
		$result = mysqli_query($dbc, $query); ?>

		<div class="search-group">
			<div class="form-group col-lg-9 col-md-8 col-sm-12 col-xs-12">
				<?php if(search_visible_function($dbc, 'equipment') == 1) { ?>
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="col-sm-4">
							<label for="site_name" class="control-label">
								<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to see all staff that have created inspections."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								Search By Staff:</label>
						</div>
						<div class="col-sm-8">
							<select data-placeholder="Select a Staff" name="search_staff" class="chosen-select-deselect form-control">
								<option></option>
								<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `contactid` IN (SELECT `staffid` FROM `equipment_inspections`)"), MYSQLI_ASSOC));
								foreach($query as $staffid) { ?>
									<option <?= ($staffid == $search_staff ? "selected" : '') ?> value='<?php echo  $staffid; ?>' ><?php echo get_contact($dbc, $staffid); ?></option><?php
								} ?>
								<option value="ALL">Display All</option>
							</select>
						</div>
					</div>
				<?php } ?>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="col-sm-4">
						<label for="site_name" class="control-label">
							<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to see the inspection types."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							Search By Inspection Type:</label>
					</div>
					<div class="col-sm-8">
						<select data-placeholder="Select an Inspection Type" name="search_type" class="chosen-select-deselect form-control">
							<option value=""></option>
							<option <?= ('Pre Trip' == $search_type ? "selected" : '') ?> value="Pre Trip">Pre Trip</option>
							<option <?= ('Post Trip' == $search_type ? "selected" : '') ?> value="Post Trip">Post Trip</option>
							<option <?= ('Maintenance' == $search_type ? "selected" : '') ?> value="Maintenance">Maintenance</option>
							<option <?= ('Evaluation' == $search_type ? "selected" : '') ?> value="Evaluation">Evaluation</option>
						</select>
					</div>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="col-sm-4">
						<label for="site_name" class="control-label">
							<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to select the earliest date of inspection you want to see."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							Search From Date:</label>
					</div>
					<div class="col-sm-8">
						<input type="text" class="form-control datepicker" name="search_date_from" value="<?= $search_date_from ?>">
					</div>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="col-sm-4">
						<label for="site_name" class="control-label">
							<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to select the latest date of inspection you want to see."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							Search To Date:</label>
					</div>
					<div class="col-sm-8">
						<input type="text" class="form-control datepicker" name="search_date_to" value="<?= $search_date_to ?>">
					</div>
				</div>
			</div>
			<div class="form-group col-lg-3 col-md-4 col-sm-12 col-xs-12">
				<div style="display:inline-block; padding: 0 0.5em;">
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here after you have made your customer selection."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click to refresh the page and see all projects within this tab."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
				</div>
			</div><!-- .form-group -->
			<div class="clearfix"></div>
		</div>

		<div class="pull-right">
            <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a new Inspection."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a href="add_inspection.php" class="btn brand-btn">Add Inspection</a>
        </div>
        
		<div class="clearfix"></div>
		
		<div id="no-more-tables">
			<?php if(mysqli_num_rows($result) > 0) { ?>
				<table class="table table-bordered">
					<tr class="hidden-xs hidden-sm">
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Staff performing the equipment inspection."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Staff Name</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Type of equipment inspection."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Inspection Type</th>
						<th>Date</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Category of this item of equipment."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Category</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Make of this item of equipment as set in the equipment profile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Make</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Model of this item of equipment as set in the equipment profile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Model</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Unit # of this item of equipment as set in the equipment profile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Unit #</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Indicates if service has been requested for this item of equipment."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Service Requested</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View the Inspection report in PDF form."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Inspection</th>
					</tr>
					<?php while($row = mysqli_fetch_array($result)) {
						$equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='".$row['equipmentid']."'")); ?>
						<tr>
							<td data-title="Staff Name"><?= get_contact($dbc, $row['staffid']) ?></td>
							<td data-title="Inspection Type"><?= $row['type'] ?></td>
							<td data-title="Date &amp; Time"><?= date('Y-m-d g:i A', strtotime($row['date'])) ?></td>
							<td data-title="Category"><?= $equipment['category'] ?></td>
							<td data-title="Make"><?= $equipment['make'] ?></td>
							<td data-title="Model"><?= $equipment['model'] ?></td>
							<td data-title="Unit #"><?= $equipment['unit_number'] ?></td>
							<td data-title="Service Requested?"><?= $row['immediate'] ? 'Yes' : 'No' ?></td>
							<td data-title="Inspection Report"><a href="download/inspection_report_<?= $row['inspectionid'] ?>.pdf">View Report</a></td>
						</tr>
					<?php } ?>
				</table>
			<?php } else {
				echo "<h2>No Inspections Found</h2>";
			} ?>
		</div>

		<div class="pull-right">
            <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a new Inspection."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a href="add_inspection.php" class="btn brand-btn">Add Inspection</a>
        </div>
	</div>
</div>

<?php include('../footer.php'); ?>