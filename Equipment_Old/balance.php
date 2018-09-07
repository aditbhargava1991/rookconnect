<?php
/*
Add	Inventory
*/
include ('../include.php');
error_reporting(0); ?>
<script>
$(document).ready(function() {
	$('.close_iframer').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	});

	$('iframe').load(function() {
		this.contentWindow.document.body.style.overflow = 'hidden';
		this.contentWindow.document.body.style.minHeight = '0';
		this.contentWindow.document.body.style.paddingBottom = '5em';
		this.style.height = (this.contentWindow.document.body.offsetHeight + 80) + 'px';
	});
});
$(document).on('change', 'select[name="status"]', function() { set_status(this); });

function view_summary(link) {
	$('#iframe_instead_of_window').attr('src', 'balance_summary.php?equipmentid='+$(link).data('equip'));
	$('.iframe_title').text('Balance Sheet Summary');
	$('.iframe_holder').show();
	$('.hide_on_iframe').hide();
	return false;
}
function set_status(dropdown) {
	$.ajax({
		url: 'equipment_ajax.php?fill=equipment_status',
		method: 'POST',
		data: { id: $(dropdown).data('equip'), status: dropdown.value },
		success: function(response) {
			console.log(response);
		}
	});
}
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('equipment');
$equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
$status = (empty($_GET['status']) ? 'Active' : $_GET['status']);
$equipmentid = 'ALL';
include_once ('../Equipment/region_location_access.php'); ?>
<div class="container">
	<div class='iframe_holder' style='display:none;'>
		<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframer' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
		<span class='iframe_title' style='color:white; font-weight:bold; position: relative;top:58px; left: 20px; font-size: 30px;'></span>
		<iframe id="iframe_instead_of_window" style='width: 100%; overflow: hidden;' height="200px; border:0;" src=""></iframe>
    </div>
    <div class="row hide_on_iframe">

		<div class="col-sm-10"><h1>Equipment Balance Sheets</h1></div>
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
                    <a href="expenses.php"><button type="button" class="btn brand-btn mobile-block">Expenses</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and edit all Balance Sheets relating to a specific item of equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="balance.php"><button type="button" class="btn brand-btn mobile-block active_tab">Balance Sheets</button></a>
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
			<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and edit all Balance Sheets relating to Active equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="balance.php?status=Active"><button type="button" class="btn brand-btn mobile-block <?= $status != 'Inactive' ? 'active_tab' : '' ?>">Active Equipment</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and edit all Balance Sheets relating to Inactive equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="balance.php?status=Inactive"><button type="button" class="btn brand-btn mobile-block <?= $status == 'Inactive' ? 'active_tab' : '' ?>">Inactive Equipment</button></a>
                </div>
			<?php } ?>
            <div class="clearfix"></div>
		</div>
		
		<?php $search_equipment = '';
		if(isset($_POST['search_equipment'])) {
			$search_equipment = $_POST['search_equipment'];
		}
		if (isset($_POST['display_all_inventory'])) {
			$search_equipment = '';
		}
		$query = "FROM `equipment` WHERE `equipment`.`deleted`=0 $access_query";
		if($status == 'Active') {
			$query .= " AND IFNULL(`status`,'') NOT IN ('Inactive')";
		} else {
			$query .= " AND IFNULL(`status`,'') IN ('Inactive')";
		}
		if(!empty($search_equipment)) {
			$query .= " AND `equipment`.`equipmentid` IN (SELECT `equipmentid` FROM `equipment` WHERE `unit_number` LIKE  '%$search_equipment%' OR `category` LIKE  '%$search_equipment%' OR `make` LIKE  '%$search_equipment%' OR `model` LIKE  '%$search_equipment%' OR `equ_description` LIKE  '%$search_equipment%' OR `vin_number` LIKE  '%$search_equipment%' OR `licence_plate` LIKE  '%$search_equipment%' OR `nickname` LIKE  '%$search_equipment%')";
		}
		$rowsPerPage = 25;
		$pageNum = 1;
		if(isset($_GET['page'])) {
			$pageNum = $_GET['page'];
		}
		$offset = ($pageNum - 1) * $rowsPerPage;
		$query_count = "SELECT COUNT(*) numrows FROM (SELECT `equipment`.`equipmentid` ".$query.") num";
		$query = "SELECT `equipment`.`equipmentid`, `category`, `make`, `model`, `unit_number`, `status`, 0 expense, 0 billings, 0 pl ".$query." ORDER BY `equipment`.`unit_number` LIMIT $offset, $rowsPerPage";
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
		<div id="no-more-tables">
			<?php if(mysqli_num_rows($result) > 0) {
				echo display_pagination($dbc, $query_count, $pageNum, $rowsPerPage); ?>
				<table class="table table-bordered">
					<tr class="hidden-sm hidden-xs">
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The category of the item of equipment."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Category</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Make of this item of equipment as set in the equipment profile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Make</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Model of this item of equipment as set in the equipment profile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Model</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Unit # of this item of equipment as set in the equipment profile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Unit #</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Total Expenses related to this item of equipment."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Total Expenses</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Total Billings related to this item of equipment."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Total Billings</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Profit and Loss amounts related to this item of equipment."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> P&amp;L</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Summary of this item of equipment."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Summary</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Status of this item of equipment."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Status</th>
						<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Edit or archive this item of equipment."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Function</th>
					</tr>
					<?php while($row = mysqli_fetch_array($result)) { ?>
						<tr>
							<td data-title="Category"><?= $row['category'] ?></td>
							<td data-title="Make"><?= $row['make'] ?></td>
							<td data-title="Model"><?= $row['model'] ?></td>
							<td data-title="Unit #"><?= $row['unit_number'] ?></td>
							<td data-title="Total Expenses">$<?= number_format($row['expense'],2) ?></td>
							<td data-title="Total Billings">$<?= number_format($row['billings'],2) ?></td>
							<td data-title="Profit &amp; Loss">$<?= number_format($row['pl'],2) ?></td>
							<td data-title="Summary"><a href="" onclick="return view_summary(this);" data-equip="<?= $row['equipmentid'] ?>">View Table</a></td>
							<td data-title="Equipment Status"><select id="status" name="status" class="chosen-select form-control" width="380" data-equip="<?= $row['equipmentid'] ?>"><option></option>
									<option value='Active' <?php if ($row['status']=='Active') echo 'selected="selected"';?> >Active</option>
									<option value='In Service' <?php if ($row['status']=='In Service') echo 'selected="selected"';?> >In Service</option>
									<option value='Service Required' <?php if ($row['status']=='Service Required') echo 'selected="selected"';?> >Service Required</option>
									<option value='On Site' <?php if ($row['status']=='On Site') echo 'selected="selected"';?> >On Site</option>
									<option value='Inactive' <?php if ($row['status']=='Inactive') echo 'selected="selected"';?> >Inactive</option>
									<option value='Sold' <?php if ($row['status']=='Sold') echo 'selected="selected"';?> >Sold</option>
								</select></td>
							<td data-title="Function"><a href="equipment_balance.php?equipmentid=<?= $row['equipmentid'] ?>">Edit</a> | <a href="equipment_balance.php?archive=true&archiveid=<?= $row['equipmentid'] ?>">Archive Equipment</a></td>
						</tr>
					<?php } ?>
				</table>
				<?php echo display_pagination($dbc, $query_count, $pageNum, $rowsPerPage);
			} else if($pageNum > 1) {
				echo "<script> window.location.replace('?'); </script>";
			} else {
				echo "<h2>No Equipment Found</h2>";
			} ?>
		</div>
	</div>
</div>

<?php include('../footer.php'); ?>