<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment'); ?>
<script>
function set_status(id, status) {
	$.ajax({
		url: 'equipment_ajax.php?fill=update_workorder_status&id='+id+'&status='+status,
		method: 'GET'
	});
}
</script>
<?php $equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
$subtab = (empty($_GET['subtab']) ? 'Pending' : filter_var($_GET['subtab'],FILTER_SANITIZE_STRING));
$edit_access = vuaed_visible_function($dbc, 'equipment'); ?>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

<?php $search_equipment = '';
if(isset($_POST['search_equipment'])) {
	$search_equipment = $_POST['search_equipment'];
}
if (isset($_POST['display_all_inventory'])) {
	$search_equipment = '';
}
$query = "SELECT * FROM `equipment_work_orders` WHERE `status`='$subtab'";
if(!empty($search_equipment)) {
	$query .= " AND `equipmentid` IN (SELCT `equipmentid` FROM `equipment` WHERE `unit_number` LIKE  '%$search_equipment%' OR `category` LIKE  '%$search_equipment%' OR `make` LIKE  '%$search_equipment%' OR `model` LIKE  '%$search_equipment%' OR `equ_description` LIKE  '%$search_equipment%' OR `vin_number` LIKE  '%$search_equipment%' OR `licence_plate` LIKE  '%$search_equipment%' OR `nickname` LIKE  '%$search_equipment%')";
}
$result = mysqli_query($dbc, $query); ?>

<div class="search-group">
	<div class="form-group col-lg-9 col-md-8 col-sm-12 col-xs-12">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="col-sm-4">
				<label for="site_name" class="control-label">
					<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to see the inspection tabs."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
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
        <a class="btn brand-btn" href="?edit_work_order=1&edit=<?= $_GET['edit'] ?>">Add Work Order</a>
    </div>
    <div class="clearfix"></div>
<?php } ?>
<div id="no-more-tables">
	<?php if(mysqli_num_rows($result) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Work Order #, as set when creating it was created."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> WO#</th>
				<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Date the work order was created on."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Date Created</th>
				<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Service(s) outlined in the work order."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Service</th>
				<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Tab of the equipment used in the work order."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Category</th>
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
					<td data-title="Service Tab &amp; Service Heading"><?= $service['category'].' - '.$service['heading'] ?></td>
					<td data-title="Tab"><?= $equipment['category'] ?></td>
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
					<td data-title="Function"><?= ($edit_access == 1 ? '<a href="?edit_work_order=1&workorderid='.$row['workorderid'].'">Edit</a> | <a href="?archiveid='.$row['workorderid'].'">Archive</a>' : '') ?></td>
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
        <a class="btn brand-btn" href="?edit_work_order=1&edit=<?= $_GET['edit'] ?>">Add Work Order</a>
    </div>
<?php } ?>
</form>