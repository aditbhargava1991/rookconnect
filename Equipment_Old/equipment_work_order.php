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
$tab = (empty($_GET['tab']) ? 'Pending' : filter_var($_GET['tab'],FILTER_SANITIZE_STRING));
$equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
$edit_access = vuaed_visible_function($dbc, 'equipment');
$equipmentid = filter_var($_GET['equipmentid'],FILTER_SANITIZE_STRING);
$unit_number = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='".$equipmentid."'"))['unit_number']; ?>
<div class="container">
  <div class="row">

		<h1>Equipment Unit #<?= $unit_number ?>: Work Orders</h1>

		<div class="pad-left gap-top double-gap-bottom"><a href="equipment.php?category=<?php echo $category; ?>" class="btn brand-btn">Back to Dashboard</a></div>

		<form id="form1" name="form1" method="post"	action="add_equipment.php?equipmentid=<?= $_GET['equipmentid'] ?>" enctype="multipart/form-data" class="form-horizontal" role="form">

		<div class="gap-left tab-container">
			<a href="add_equipment.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Equipment</a>
			<?php if ( in_array('Inspection',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'inspection') === TRUE ) { ?>
				<a href="equipment_inspections.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Inspections</a>
			<?php } ?>
			<?php if ( in_array('Work Order',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'work_order') === TRUE ) { ?>
				<a href="equipment_work_order.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn active_tab">Work Orders</a>
			<?php } ?>
			<?php if ( in_array('Schedules',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'schedules') === TRUE ) { ?>
				<a href="equipment_service.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Service Schedule</a>
			<?php } ?>
			<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
				<a href="equipment_expenses.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Expenses</a>
			<?php } ?>
			<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
				<a href="equipment_balance.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Balance Sheet</a>
			<?php } ?>
            <?php if ( in_array('Equipment Assignment',$equipment_main_tabs) && check_subtab_persmission($dbc, 'eqipment', ROLE, 'equip_assign') === TRUE ) { ?>
                <a href="equipment_assignment.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Equipment Assignment</a>
            <?php } ?>
		</div>

		<div class="gap-left tab-container">
			<a href="equipment_work_order.php?tab=Pending&equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn <?= $tab == 'Pending' ? 'active_tab' : '' ?>">Pending</a>
			<a href="equipment_work_order.php?tab=Doing&equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn <?= $tab == 'Doing' ? 'active_tab' : '' ?>">Doing</a>
			<a href="equipment_work_order.php?tab=Done&equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn <?= $tab == 'Done' ? 'active_tab' : '' ?>">Done</a>
		</div>
		
		<div class="clearfix"></div>
		<?php if($edit_access == 1) { ?>
			<a class="btn brand-btn pull-right" href="add_work_order.php?equipmentid=<?= $_GET['equipmentid'] ?>">Add Work Order</a>
		<?php } ?>
		<div id="no-more-tables">
			<?php $result = mysqli_query($dbc, "SELECT * FROM `equipment_work_orders` WHERE `equipmentid`='$equipmentid' AND `status`='$tab'");
			if(mysqli_num_rows($result) > 0) { ?>
				<table class="table table-bordered">
					<tr class="hidden-sm hidden-xs">
						<th>WO#</th>
						<th>Date Created</th>
						<th>Service</th>
						<th>Category</th>
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
							<td data-title="Status"><select id="status_<?= $row['workorderid'] ?>" name="status" class="chosen-select-deselect form-control">
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
			<a class="btn brand-btn pull-right" href="add_work_order.php?equipmentid=<?= $_GET['equipmentid'] ?>">Add Work Order</a>
		<?php } ?>
	</div>
</div>

<?php include('../footer.php'); ?>