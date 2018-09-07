<?php
/*
Add	Inventory
*/
include ('../include.php');
error_reporting(0); ?>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('equipment');
$equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
$equipmentid = 'ALL'; ?>
<div class="container">
  <div class="row">

		<div class="col-sm-10"><h1>Equipment Expenses</h1></div>
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
                    <a href="expenses.php"><button type="button" class="btn brand-btn mobile-block active_tab">Expenses</button></a>
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
	<?php
	if (isset($_POST['search_submit'])) {
		$search_month = substr($_POST['search_month'],0,7);
		if($search_month == 0000-00-00) {
			$search_month = date('Y-m');
		}
		$search_staff = isset($_POST['search_staff']) ? $_POST['search_staff'] : $_SESSION['contactid'];
	} else {
		$search_month = date('Y-m');
		$search_staff = $_SESSION['contactid'];
	}
	?>
	<br>
	<div class="search-group">
		<div class="form-group col-lg-9 col-md-8 col-sm-12 col-xs-12">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="col-sm-4">
					<span class="popover-examples list-inline">
						<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Selecting a date will display all expenses for the month in which the date occurs. For example, selecting January 5, 2017 will display January 1, 2017 to January 31, 2017."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a>
					</span> <label for="site_name" class="control-label">Display Month:</label>
				</div>
				<div class="col-sm-8">
					<input type="text" class="form-control datepicker" name="search_month" value="<?php echo $search_month; ?>-01">
				</div>
			</div>
			<?php if(search_visible_function($dbc, 'equipment') == 1) { ?>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="col-sm-4">
						<label for="site_name" class="control-label">Expensed For:</label>
					</div>
					<div class="col-sm-8">
						<select data-placeholder="Select a Staff Member..." name="search_staff" class="chosen-select-deselect form-control" width="380">
							<option value=""></option>
							  <?php
								$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
								foreach($query as $id) {
									$selected = '';
									$selected = $id == $search_staff ? 'selected = "selected"' : '';
									echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
								}
							  ?>
						</select>
					</div>
				</div>
			<?php } ?>
		</div>
		<div class="form-group col-lg-3 col-md-4 col-sm-12 col-xs-12">
			<div style="display:inline-block; padding: 0 0.5em;">
				<button type="submit" name="search_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
			</div>
			<div style="display:inline-block; padding: 0 0.5em;">
				<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click to refresh the page and see the current month for the currently logged in staff."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<button type="submit" name="display_all" value="Display All" class="btn brand-btn mobile-block">Current Month</button>
			</div>
		</div><!-- .form-group -->
		<div class="clearfix"></div>
	</div><br />
		<?php $query_expenses = "SELECT * FROM equipment_expenses WHERE LEFT(ex_date,7) = '$search_month' AND '$search_staff' IN ('', `staff`) AND `deleted`=0";

		include('expense_list.php'); ?>
	</div>
</div>

<?php include('../footer.php'); ?>