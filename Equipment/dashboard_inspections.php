<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');

include_once('../Equipment/region_location_access.php'); ?>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

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
					<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to see the inspection tabs."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					Search By Inspection Tab:</label>
			</div>
			<div class="col-sm-8">
				<select data-placeholder="Select an Inspection Tab" name="search_type" class="chosen-select-deselect form-control">
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
    <a href="?edit_inspection=1" class="btn brand-btn">Add Inspection</a>
</div>

<div class="clearfix"></div>

<div id="no-more-tables">
	<?php if(mysqli_num_rows($result) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-xs hidden-sm">
				<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Staff performing the equipment inspection."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Staff Name</th>
				<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Tab of equipment inspection."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Inspection Tab</th>
				<th>Date</th>
				<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Tab of this item of equipment."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Category</th>
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
					<td data-title="Inspection Tab"><?= $row['type'] ?></td>
					<td data-title="Date &amp; Time"><?= date('Y-m-d g:i A', strtotime($row['date'])) ?></td>
					<td data-title="Tab"><?= $equipment['category'] ?></td>
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
    <a href="?edit_inspection=1" class="btn brand-btn">Add Inspection</a>
</div>
</form>