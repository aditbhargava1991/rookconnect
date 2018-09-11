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
<?php $tab = (empty($_GET['tab']) ? 'Pending' : filter_var($_GET['tab'],FILTER_SANITIZE_STRING));
$equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
$edit_access = vuaed_visible_function($dbc, 'equipment');
$equipmentid = filter_var($_GET['edit'],FILTER_SANITIZE_STRING);
$unit_number = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='".$equipmentid."'"))['unit_number']; ?>

<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
    <ul>
        <a href="?category=Top"><li>Back to Dashboard</li></a>
        <a href="?edit=<?= $_GET['edit'] ?>&subtab=work_orders&tab=Pending"><li <?= $tab == 'Pending' ? 'class="active blue"' : '' ?>>Pending</li></a>
        <a href="?edit=<?= $_GET['edit'] ?>&subtab=work_orders&tab=Doing"><li <?= $tab == 'Doing' ? 'class="active blue"' : '' ?>>Doing</li></a>
        <a href="?edit=<?= $_GET['edit'] ?>&subtab=work_orders&tab=Done"><li <?= $tab == 'Done' ? 'class="active blue"' : '' ?>>Done</li></a>
    </ul>
</div>

<div class="scale-to-fill has-main-screen" style="overflow: hidden;">
    <div class="main-screen standard-body form-horizontal">
        <div class="standard-body-title">
            <h3>Equipment Unit #<?= $unit_number ?>: Work Orders</h3>
        </div>

        <div class="standard-body-content" style="padding: 0.5em;">
			<form id="form1" name="form1" method="post"	action="add_equipment.php?equipmentid=<?= $_GET['edit'] ?>" enctype="multipart/form-data" class="form-horizontal" role="form">

				<div class="gap-left tab-container show-on-mob">
					<a href="?subtab=work_orders&tab=Pending&edit=<?= $_GET['edit'] ?>" class="btn brand-btn <?= $tab == 'Pending' ? 'active_tab' : '' ?>">Pending</a>
					<a href="?subtab=work_orders&tab=Doing&edit=<?= $_GET['edit'] ?>" class="btn brand-btn <?= $tab == 'Doing' ? 'active_tab' : '' ?>">Doing</a>
					<a href="?subtab=work_orders&tab=Done&edit=<?= $_GET['edit'] ?>" class="btn brand-btn <?= $tab == 'Done' ? 'active_tab' : '' ?>">Done</a>
				</div>
				
				<div class="clearfix"></div>
				<?php if($edit_access == 1) { ?>
					<a class="btn brand-btn pull-right double-gap-bottom" href="?edit_work_order=1&edit=<?= $_GET['edit'] ?>">Add Work Order</a>
				<?php } ?>
				<div class="clearfix"></div>
				<div id="no-more-tables">
					<?php $result = mysqli_query($dbc, "SELECT * FROM `equipment_work_orders` WHERE `equipmentid`='$equipmentid' AND `status`='$tab'");
					if(mysqli_num_rows($result) > 0) { ?>
						<table class="table table-bordered">
							<tr class="hidden-sm hidden-xs">
								<th>WO#</th>
								<th>Date Created</th>
								<th>Service</th>
								<th>Tab</th>
								<th>Make</th>
								<th>Model</th>
								<th>Unit #</th>
								<th>Inspections</th>
								<th>Comments</th>
								<th>Status</th>
								<th>Function</th>
							</tr>
							<?php while($row = mysqli_fetch_array($result)) {
								$equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='".$row['equipmentid']."'"));
								$service = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid`='".$row['serviceid']."'"));
								?>
								<script type="text/javascript">
								$(document).on('change', 'select#status_<?= $row['workorderid'] ?>', function() { set_status('<?= $row['workorderid'] ?>', this.value); });
								</script>
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
									<td data-title="Status"><select id="status_<?= $row['workorderid'] ?>" name="status" class="chosen-select-deselect form-control">
										<option <?= $row['status'] == 'Pending' ? 'selected' : '' ?> value="Pending">Pending</option>
										<option <?= $row['status'] == 'Doing' ? 'selected' : '' ?> value="Doing">Doing</option>
										<option <?= $row['status'] == 'Done' ? 'selected' : '' ?> value="Done">Done</option></select></td>
									<td data-title="Function"><?= ($edit_access == 1 ? '<a href="?edit_work_order=1&workorderid='.$row['workorderid'].'">Edit</a> | <a href="?edit_work_order=1&archiveid='.$row['workorderid'].'">Archive</a>' : '') ?></td>
								</tr>
							<?php } ?>
						</table>
					<?php } else {
						echo "<h2>No Work Orders Found</h2>";
					} ?>
				</div>
				<?php if($edit_access == 1) { ?>
					<a class="btn brand-btn pull-right gap-bottom" href="?edit_work_order=1&edit=<?= $_GET['edit'] ?>">Add Work Order</a>
				<?php } ?>
			</form>
		</div>
	</div>
</div>