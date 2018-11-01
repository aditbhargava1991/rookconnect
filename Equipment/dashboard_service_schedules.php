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

function view_history(link) {
	$('#iframe_instead_of_window').attr('src', 'service_history.php?equipmentid='+$(link).data('equip'));
	$('.iframe_title').text('Service History');
	$('.iframe_holder').show();
	$('.hide_on_iframe').hide();
	return false;
}
function set_status(id, status) {
	$.ajax({
		url: 'equipment_ajax.php?fill=update_workorder_status&id='+id+'&status='+status,
		method: 'GET'
	});
}
</script>
<?php $equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
$tab = (empty($_GET['tab']) ? 'Pending' : filter_var($_GET['tab'],FILTER_SANITIZE_STRING));
$edit_access = vuaed_visible_function($dbc, 'equipment');
include_once ('../Equipment/region_location_access.php'); ?>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">		
<?php $search_equipment = '';
if(isset($_POST['search_equipment'])) {
	$search_equipment = $_POST['search_equipment'];
}
if (isset($_POST['display_all_inventory'])) {
	$search_equipment = '';
}
$query = "FROM `equipment` LEFT JOIN `equipment_inspections` ON `equipment`.`equipmentid`=`equipment_inspections`.`equipmentid` $access_query_where GROUP BY `equipment`.`equipmentid`";
if(!empty($search_equipment)) {
	$query .= " AND `equipment`.`equipmentid` IN (SELCT `equipmentid` FROM `equipment` WHERE `unit_number` LIKE  '%$search_equipment%' OR `category` LIKE  '%$search_equipment%' OR `make` LIKE  '%$search_equipment%' OR `model` LIKE  '%$search_equipment%' OR `equ_description` LIKE  '%$search_equipment%' OR `vin_number` LIKE  '%$search_equipment%' OR `licence_plate` LIKE  '%$search_equipment%' OR `nickname` LIKE  '%$search_equipment%') $access_query";
}
$rowsPerPage = 25;
$pageNum = 1;
if(isset($_GET['page'])) {
	$pageNum = $_GET['page'];
}
$offset = ($pageNum - 1) * $rowsPerPage;
$query_count = "SELECT COUNT(*) numrows FROM (SELECT `equipment`.`equipmentid` ".$query.") num";
$query = "SELECT `equipment`.`equipmentid`, `make`, `model`, `unit_number`, IF(`next_oil_filter_change_date` < `next_tire_rotation_date` AND `next_oil_filter_change_date` < `next_insp_tune_up_date`, `next_oil_filter_change_date`, IF(`next_tire_rotation_date` < `next_insp_tune_up_date`, `next_tire_rotation_date`, `next_insp_tune_up_date`)) `next_date`, MAX(`equipment_inspections`.`date`) last_inspect ".$query." ORDER BY `next_date` LIMIT $offset, $rowsPerPage";
$result = mysqli_query($dbc, $query); ?>

<div class="search-group">
	<div class="form-group col-lg-9 col-md-8 col-sm-12 col-xs-12">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="col-sm-4">
				<label for="site_name" class="control-label">
					<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to see the inspection tabss."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
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
				<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Make of this item of equipment as set in the equipment profile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Make</th>
				<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Model of this item of equipment as set in the equipment profile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Model</th>
				<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Unit # for this item of equipment as set in the equipment profile."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Unit #</th>
				<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The date the Inspection has been scheduled for. If no date has been scheduled, this will be marked as Not Complete."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Inspection</th>
				<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="The Next Service Date for this item of equipment."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Next Service</th>
				<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View the Service History for this item of equipment."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> History</th>
				<th><span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Edit the service schedule for this item of equipment."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="18"></a></span> Function</th>
			</tr>
			<?php while($row = mysqli_fetch_array($result)) {
				$inspectionid = mysqli_fetch_array(mysqli_query($dbc,"SELECT MAX(`inspectionid`) inspectionid FROM `equipment_inspections` WHERE `date`='".$row['last_inspect']."' AND `equipmentid`='".$row['equipmentid']."'"))['inspectionid']; ?>
				<tr>
					<td data-title="Make"><?= $row['make'] ?></td>
					<td data-title="Model"><?= $row['model'] ?></td>
					<td data-title="Unit #"><?= $row['unit_number'] ?></td>
					<td data-title="Last Inspection"><?= ($inspectionid != '' ? '<a href="download/inspection_report_'.$inspectionid.'.pdf">'.date('Y-m-d', strtotime($row['last_inspect'])).'</a>' : 'Not Complete') ?></td>
					<td data-title="Next Service"><a href="?from_url=<?= urlencode('?tab=service_schedules') ?>&edit=<?= $row['equipmentid'] ?>&subtab=service_schedule"><?= $row['next_date'] ?></a></td>
					<td data-title="History"><a href="" onclick="return view_history(this);" data-equip="<?= $row['equipmentid'] ?>">View All</a></td>
					<td data-title="Function"><a href="?from_url=<?= urlencode('?tab=service_schedules') ?>&edit=<?= $row['equipmentid'] ?>&subtab=service_schedule">Edit</a></td>
				</tr>
			<?php } ?>
		</table>
		<?php 
		echo '<div class="pagination_links">';
		echo display_pagination($dbc, $query_count, $pageNum, $rowsPerPage);
		echo '</div>';
	} else {
		echo "<h2>No Work Orders Found</h2>";
	} ?>
</div>
</form>