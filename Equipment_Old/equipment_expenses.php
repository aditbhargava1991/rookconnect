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
$equipmentid = filter_var($_GET['equipmentid'],FILTER_SANITIZE_STRING);
$unit_number = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='".$equipmentid."'"))['unit_number'];?>
<div class="container">
  <div class="row">

		<h1>Equipment Unit #<?= $unit_number ?>: Expenses</h1>

		<div class="pad-left gap-top double-gap-bottom"><a href="equipment.php?category=<?php echo $category; ?>" class="btn brand-btn">Back to Dashboard</a></div>

		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

		<div class="gap-left tab-container">
			<a href="add_equipment.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Equipment</a>
			<?php if ( in_array('Inspection',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'inspection') === TRUE ) { ?>
				<a href="equipment_inspections.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Inspections</a>
			<?php } ?>
			<?php if ( in_array('Work Order',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'work_order') === TRUE ) { ?>
				<a href="equipment_work_order.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Work Orders</a>
			<?php } ?>
			<?php if ( in_array('Schedules',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'schedules') === TRUE ) { ?>
				<a href="equipment_service.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Service Schedule</a>
			<?php } ?>
			<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
				<a href="equipment_expenses.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn active_tab">Expenses</a>
			<?php } ?>
			<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
				<a href="equipment_balance.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Balance Sheet</a>
			<?php } ?>
            <?php if ( in_array('Equipment Assignment',$equipment_main_tabs) && check_subtab_persmission($dbc, 'eqipment', ROLE, 'equip_assign') === TRUE ) { ?>
                <a href="equipment_assignment.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Equipment Assignment</a>
            <?php } ?>
		</div><form name="form_sites" method="post" action="" class="form-inline" role="form">
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
		<?php $query_expenses = "SELECT * FROM equipment_expenses WHERE LEFT(ex_date,7) = '$search_month' AND '$search_staff' IN ('', `staff`) AND `deleted`=0 AND `equipmentid`='$equipmentid'";

		include('expense_list.php'); ?>
	</div>
</div>

<?php include('../footer.php'); ?>