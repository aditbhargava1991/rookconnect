<?php
/*
Add	Inventory
*/
include ('../include.php');
error_reporting(0);
if(isset($_POST['submit'])) {
	$equipmentid = $_GET['equipmentid'];
	$last_oil_filter_change_date = filter_var($_POST['last_oil_filter_change_date'],FILTER_SANITIZE_STRING);
	$last_oil_filter_change = filter_var($_POST['last_oil_filter_change'],FILTER_SANITIZE_STRING);
	$next_oil_filter_change_date = filter_var($_POST['next_oil_filter_change_date'],FILTER_SANITIZE_STRING);
	$next_oil_filter_change = filter_var($_POST['next_oil_filter_change'],FILTER_SANITIZE_STRING);
	$last_insp_tune_up_date = filter_var($_POST['last_insp_tune_up_date'],FILTER_SANITIZE_STRING);
	$last_insp_tune_up = filter_var($_POST['last_insp_tune_up'],FILTER_SANITIZE_STRING);
	$next_insp_tune_up_date = filter_var($_POST['next_insp_tune_up_date'],FILTER_SANITIZE_STRING);
	$next_insp_tune_up = filter_var($_POST['next_insp_tune_up'],FILTER_SANITIZE_STRING);
	$last_tire_rotation_date = filter_var($_POST['last_tire_rotation_date'],FILTER_SANITIZE_STRING);
	$last_tire_rotation = filter_var($_POST['last_tire_rotation'],FILTER_SANITIZE_STRING);
	$next_tire_rotation_date = filter_var($_POST['next_tire_rotation_date'],FILTER_SANITIZE_STRING);
	$next_tire_rotation = filter_var($_POST['next_tire_rotation'],FILTER_SANITIZE_STRING);
	$status = filter_var($_POST['status'],FILTER_SANITIZE_STRING);
	$service_staff = filter_var($_POST['service_staff'],FILTER_SANITIZE_STRING);
	
	$sql = "UPDATE `equipment` SET `last_oil_filter_change_date`='$last_oil_filter_change_date', `last_oil_filter_change`='$last_oil_filter_change', `next_oil_filter_change_date`='$next_oil_filter_change_date', `next_oil_filter_change`='$next_oil_filter_change', `next_oil_filter_change`='$next_oil_filter_change', `last_insp_tune_up_date`='$last_insp_tune_up_date', `last_insp_tune_up`='$last_insp_tune_up', `next_insp_tune_up_date`='$next_insp_tune_up_date', `next_insp_tune_up`='$next_insp_tune_up', `last_tire_rotation_date`='$last_tire_rotation_date', `last_tire_rotation`='$last_tire_rotation', `next_tire_rotation_date`='$next_tire_rotation_date', `next_tire_rotation`='$next_tire_rotation', `status`='$status', `service_staff`='$service_staff' WHERE `equipmentid`='$equipmentid'";
	mysqli_query($dbc, $sql);
} ?>
<script>
$(document).ready(function() {
	$('[name=category],[name=make],[name=model],[name=unit_number]').prop('disabled','disabled').trigger('change.select2');
});
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('equipment');
$equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
$equipmentid = filter_var($_GET['equipmentid'],FILTER_SANITIZE_STRING);
$get_equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='".$equipmentid."'"));

$unit_number = $get_equipment['unit_number'];
$category = $get_equipment['category'];
$make = $get_equipment['make'];
$model = $get_equipment['model'];
$last_oil_filter_change_date = $get_equipment['last_oil_filter_change_date'];
$last_oil_filter_change = $get_equipment['last_oil_filter_change'];
$next_oil_filter_change_date = $get_equipment['next_oil_filter_change_date'];
$next_oil_filter_change = $get_equipment['next_oil_filter_change'];
$last_insp_tune_up_date = $get_equipment['last_insp_tune_up_date'];
$last_insp_tune_up = $get_equipment['last_insp_tune_up'];
$next_insp_tune_up_date = $get_equipment['next_insp_tune_up_date'];
$next_insp_tune_up = $get_equipment['next_insp_tune_up'];
$last_tire_rotation_date = $get_equipment['last_tire_rotation_date'];
$last_tire_rotation = $get_equipment['last_tire_rotation'];
$next_tire_rotation_date = $get_equipment['next_tire_rotation_date'];
$next_tire_rotation = $get_equipment['next_tire_rotation'];
$status = $get_equipment['status'];
$service_staff = $get_equipment['service_staff']; ?>
<div class="container">
  <div class="row">

		<h1>Equipment Unit #<?= $unit_number ?>: Service Schedule</h1>

		<div class="pad-left gap-top double-gap-bottom"><a href="<?= empty($_GET['from_url']) ? 'equipment.php?category='.$category : $_GET['from_url']; ?>" class="btn brand-btn">Back to Dashboard</a></div>

		<form id="form1" name="form1" method="post"	action="add_equipment.php?equipmentid=<?= $_GET['equipmentid'] ?>" enctype="multipart/form-data" class="form-horizontal" role="form">

		<div class="gap-left tab-container">
			<a href="add_equipment.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Equipment</a>
			<?php if ( in_array('Inspection',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'inspection') === TRUE ) { ?>
				<a href="equipment_inspections.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Inspections</a>
			<?php } ?>
			<?php if ( in_array('Work Order',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'work_order') === TRUE ) { ?>
				<a href="equipment_work_order.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Work Orders</a>
			<?php } ?>
			<?php if ( in_array('Schedules',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'schedules') === TRUE ) { ?>
				<a href="equipment_service.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn active_tab">Service Schedule</a>
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
		
		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
			<div class="panel-group" id="accordion2">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_staff" >
								Staff<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_staff" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $value_config = ',Service Staff,';
							include('add_equipment_fields.php'); ?>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_equip" >
								Equipment Details<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_equip" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $value_config = ',Category,Make,Model,Unit #,';
							include('add_equipment_fields.php'); ?>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_oil" >
								Oil Change<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_oil" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $value_config = ',Last Oil Filter Change (date),Last Oil Filter Change (km),Next Oil Filter Change (date),Next Oil Filter Change (km),';
							include('add_equipment_fields.php'); ?>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_tire" >
								Tire Rotation<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_tire" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $value_config = ',Last Tire Rotation (date),Last Tire Rotation (km),Next Tire Rotation (date),Next Tire Rotation (km),';
							include('add_equipment_fields.php'); ?>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_tune" >
								Tune Up<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_tune" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $value_config = ',Last Inspection & Tune Up (date),Last Inspection & Tune Up (km),Next Inspection & Tune Up (date),Next Inspection & Tune Up (km),';
							include('add_equipment_fields.php'); ?>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_status" >
								Status<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_status" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $value_config = ',Status,';
							include('add_equipment_fields.php'); ?>
						</div>
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<div class="col-sm-6">
					<a href="equipment.php?category=<?php echo $category; ?>"	class="btn brand-btn btn-lg">Back</a>
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button>
				</div>
			</div>
		</form>
		
	</div>
</div>

<?php include('../footer.php'); ?>