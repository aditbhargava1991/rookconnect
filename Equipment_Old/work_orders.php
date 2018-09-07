<?php
/*
Add	Inventory
*/
include ('../include.php');
error_reporting(0); ?>
<script>
function set_status(id, status) {
	$.ajax({
		url: 'equipment_ajax.php?fill=update_workorder_status&id='+id+'&status='+status,
		method: 'GET'
	});
}
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('equipment');
$equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
$tab = (empty($_GET['tab']) ? 'Pending' : filter_var($_GET['tab'],FILTER_SANITIZE_STRING));
$edit_access = vuaed_visible_function($dbc, 'equipment'); ?>
<div class="container">
  <div class="row">

		<div class="col-sm-10"><h1>Equipment: Work Orders</h1></div>
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
                    <a href="work_orders.php"><button type="button" class="btn brand-btn mobile-block active_tab">Work Orders</button></a>
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

		<div class="tab-container">
			<div class="tab pull-left">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View all Pending Work Orders."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="work_orders.php?tab=Pending" class="btn brand-btn <?= $tab == 'Pending' ? 'active_tab' : '' ?>">Pending</a>
            </div>
            <div class="tab pull-left">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View all current Work Orders."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="work_orders.php?tab=Doing" class="btn brand-btn <?= $tab == 'Doing' ? 'active_tab' : '' ?>">Doing</a>
            </div>
            <div class="tab pull-left">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View all completed Work Orders."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="work_orders.php?tab=Done" class="btn brand-btn <?= $tab == 'Done' ? 'active_tab' : '' ?>">Done</a>
            </div>
            <div class="clearfix"></div>
		</div>
		
		<?php $search_equipment = '';
		if(isset($_POST['search_equipment'])) {
			$search_equipment = $_POST['search_equipment'];
		}
		if (isset($_POST['display_all_inventory'])) {
			$search_equipment = '';
		}
		$query = "SELECT * FROM `equipment_work_orders` WHERE `status`='$tab'";
		if(!empty($search_equipment)) {
			$query .= " AND `equipmentid` IN (SELCT `equipmentid` FROM `equipment` WHERE `unit_number` LIKE  '%$search_equipment%' OR `category` LIKE  '%$search_equipment%' OR `make` LIKE  '%$search_equipment%' OR `model` LIKE  '%$search_equipment%' OR `equ_description` LIKE  '%$search_equipment%' OR `vin_number` LIKE  '%$search_equipment%' OR `licence_plate` LIKE  '%$search_equipment%' OR `nickname` LIKE  '%$search_equipment%')";
		}
		$result = mysqli_query($dbc, $query); ?>

		<div class="search-group">
			<div class="form-group col-lg-9 col-md-8 col-sm-12 col-xs-12">
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="col-sm-4">
						<label for="site_name" class="control-label">
							<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to see the inspection types."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							Search by Equipment:</label>
					</div>
					<div class="col-sm-8">
						<input type="text" name="search_equipment" class="form-control" value="<?= $search_equipment ?>">
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
		
		<div class="clearfix"></div>
		<?php if($edit_access == 1) { ?>
			<div class="pull-right">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a New Work Order."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a class="btn brand-btn" href="add_work_order.php?equipmentid=<?= $_GET['equipmentid'] ?>">Add Work Order</a>
            </div>
		<?php } ?>
		<div id="no-more-tables">
			<?php if(mysqli_num_rows($result) > 0) { ?>
				<table class="table table-bordered">
					<tr class="hidden-sm hidden-xs">
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Work Order #, as set when creating it was created."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> WO#</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Date the work order was created on."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Date Created</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Service(s) outlined in the work order."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Service</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Category of the equipment used in the work order."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Category</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Make of this item of equipment as set in the equipment profile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Make</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Model of this item of equipment as set in the equipment profile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Model</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Unit # for this item of equipment as set in the equipment profile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Unit #</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View the equipment inspection report(s)."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Inspections</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View any work crder Comments."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Comments</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Status of the work order."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Status</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Edit or Archive this work order."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Function</th>
					</tr>
					<?php while($row = mysqli_fetch_array($result)) {
						$equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='".$row['equipmentid']."'")); ?>
						<tr>
							<td data-title="Work Order #"><?= $row['workorderid'] ?></td>
							<td data-title="Date Created"><?= $row['date'] ?></td>
							<td data-title="Service Category &amp; Service Heading"><?= $service['category'].' - '.$service['heading'] ?></td>
							<td data-title="Category"><?= $equipment['category'] ?></td>
							<td data-title="Make"><?= $equipment['make'] ?></td>
							<td data-title="Model"><?= $equipment['model'] ?></td>
							<td data-title="Unit #"><?= $equipment['unit_no'] ?></td>
							<td data-title="Inspections"><?php $inspections = mysqli_query($dbc, "SELECT * FROM `equipment_inspections` WHERE `equipmentid`='".$row['equipmentid']."'");
								while($inspection = mysqli_fetch_array($inspections)) {
									echo "<a href='download/inspection_report_".$inspection['inspectionid'].".pdf'>Report #".$inspection['inspectionid']." <img src='".WEBSITE_URL."/img/pdf.png'></a><br />";
								} ?></td>
							<td data-title="Comments"><?= html_entity_decode(mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`comments` SEPARATOR '') comments FROM `equipment_inspections` WHERE `equipmentid`='".$row['equipmentid']."'"))['comments']) ?></td>
							<td data-title="Status"><select name="status" onchange="set_status('<?= $row['workorderid'] ?>', this.value);" class="chosen-select-deselect form-control">
								<option <?= $row['status'] == 'Pending' ? 'selected' : '' ?> value="Pending">Pending</option>
								<option <?= $row['status'] == 'Doing' ? 'selected' : '' ?> value="Doing">Doing</option>
								<option <?= $row['status'] == 'Done' ? 'selected' : '' ?> value="Done">Done</option></select></td>
							<td data-title="Function"><?= ($edit_access == 1 ? '<a href="add_work_order.php?workorderid='.$row['workorderid'].'">Edit</a> | <a href="?archiveid='.$row['workorderid'].'">Archive</a>' : '') ?></td>
						</tr>
					<?php } ?>
				</table>
			<?php } else {
				echo "<h2>No Work Orders Found</h2>";
			} ?>
		</div>
		<?php if($edit_access == 1) { ?>
			<div class="pull-right">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a New Work Order."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a class="btn brand-btn" href="add_work_order.php?equipmentid=<?= $_GET['equipmentid'] ?>">Add Work Order</a>
            </div>
		<?php } ?>
	</div>
</div>

<?php include('../footer.php'); ?>