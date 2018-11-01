<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment'); ?>
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
<?php $equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
$status = (empty($_GET['status']) ? 'Active' : $_GET['status']);
$equipmentid = 'ALL';
include_once ('../Equipment/region_location_access.php'); ?>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

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
<div id="no-more-tables">
	<?php if(mysqli_num_rows($result) > 0) {
		echo '<div class="pagination_links">';
		echo display_pagination($dbc, $query_count, $pageNum, $rowsPerPage);
		echo '</div>'; ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The tab of the item of equipment."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Category</th>
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
					<td data-title="Tab"><?= $row['category'] ?></td>
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
					<td data-title="Function"><a href="?edit=<?= $row['equipmentid'] ?>&subtab=balance_sheet">Edit</a> | <a href="?edit=<?= $row['equipmentid'] ?>&subtab=balance_sheet&archive=true&archiveid=<?= $row['equipmentid'] ?>">Archive Equipment</a></td>
				</tr>
			<?php } ?>
		</table>
		<?php 
		echo '<div class="pagination_links">';
		echo display_pagination($dbc, $query_count, $pageNum, $rowsPerPage);
		echo '</div>';
	} else if($pageNum > 1) {
		echo "<script> window.location.replace('?tab=balance'); </script>";
	} else {
		echo "<h2>No Equipment Found</h2>";
	} ?>
</div>
</form>