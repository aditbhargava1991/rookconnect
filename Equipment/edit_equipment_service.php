<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');

if(isset($_POST['submit'])) {
	$equipmentid = $_GET['edit'];
	$last_oil_filter_change_date = filter_var($_POST['last_oil_filter_change_date'],FILTER_SANITIZE_STRING);
	$last_oil_filter_change = filter_var($_POST['last_oil_filter_change'],FILTER_SANITIZE_STRING);
	$last_oil_filter_change_hrs = filter_var($_POST['last_oil_filter_change_hrs'],FILTER_SANITIZE_STRING);
	$next_oil_filter_change_date = filter_var($_POST['next_oil_filter_change_date'],FILTER_SANITIZE_STRING);
	$next_oil_filter_change = filter_var($_POST['next_oil_filter_change'],FILTER_SANITIZE_STRING);
	$next_oil_filter_change_hrs = filter_var($_POST['next_oil_filter_change_hrs'],FILTER_SANITIZE_STRING);
	$last_insp_tune_up_date = filter_var($_POST['last_insp_tune_up_date'],FILTER_SANITIZE_STRING);
	$last_insp_tune_up = filter_var($_POST['last_insp_tune_up'],FILTER_SANITIZE_STRING);
	$last_insp_tune_up_hrs = filter_var($_POST['last_insp_tune_up_hrs'],FILTER_SANITIZE_STRING);
	$next_insp_tune_up_date = filter_var($_POST['next_insp_tune_up_date'],FILTER_SANITIZE_STRING);
	$next_insp_tune_up = filter_var($_POST['next_insp_tune_up'],FILTER_SANITIZE_STRING);
	$next_insp_tune_up_hrs = filter_var($_POST['next_insp_tune_up_hrs'],FILTER_SANITIZE_STRING);
	$last_tire_rotation_date = filter_var($_POST['last_tire_rotation_date'],FILTER_SANITIZE_STRING);
	$last_tire_rotation = filter_var($_POST['last_tire_rotation'],FILTER_SANITIZE_STRING);
	$last_tire_rotation_hrs = filter_var($_POST['last_tire_rotation_hrs'],FILTER_SANITIZE_STRING);
	$next_tire_rotation_date = filter_var($_POST['next_tire_rotation_date'],FILTER_SANITIZE_STRING);
	$next_tire_rotation = filter_var($_POST['next_tire_rotation'],FILTER_SANITIZE_STRING);
	$next_tire_rotation_hrs = filter_var($_POST['next_tire_rotation_hrs'],FILTER_SANITIZE_STRING);
	$mileage = filter_var($_POST['mileage'],FILTER_SANITIZE_STRING);
	$status = filter_var($_POST['status'],FILTER_SANITIZE_STRING);
	$hours_operated = filter_var($_POST['hours_operated'],FILTER_SANITIZE_STRING);
	$service_staff = filter_var($_POST['service_staff'],FILTER_SANITIZE_STRING);
	
	$sql = "UPDATE `equipment` SET `last_oil_filter_change_date`='$last_oil_filter_change_date', `last_oil_filter_change`='$last_oil_filter_change', `last_oil_filter_change_hrs`='$last_oil_filter_change_hrs', `next_oil_filter_change_date`='$next_oil_filter_change_date', `next_oil_filter_change`='$next_oil_filter_change', `next_oil_filter_change_hrs`='$next_oil_filter_change_hrs', `last_insp_tune_up_date`='$last_insp_tune_up_date', `last_insp_tune_up`='$last_insp_tune_up', `last_insp_tune_up_hrs`='$last_insp_tune_up_hrs', `next_insp_tune_up_date`='$next_insp_tune_up_date', `next_insp_tune_up`='$next_insp_tune_up', `next_insp_tune_up_hrs`='$next_insp_tune_up_hrs', `last_tire_rotation_date`='$last_tire_rotation_date', `last_tire_rotation`='$last_tire_rotation', `last_tire_rotation_hrs`='$last_tire_rotation_hrs', `next_tire_rotation_date`='$next_tire_rotation_date', `next_tire_rotation`='$next_tire_rotation', `next_tire_rotation_hrs`='$next_tire_rotation_hrs', `hours_operated`='$hours_operated', `mileage`='$mileage', `status`='$status', `service_staff`='$service_staff' WHERE `equipmentid`='$equipmentid'";
	mysqli_query($dbc, $sql);

	$before_change = '';
	$history = "Equipment is been updated. <br />";
	add_update_history($dbc, 'equipment_history', $history, '', $before_change);
} ?>
<script>
$(document).ready(function() {
	$('[name=category],[name=make],[name=model],[name=unit_number]').prop('disabled','disabled').trigger('change.select2');

	// Active tabs
	$('[data-tab-target]').click(function() {
		$('.main-screen .main-screen').scrollTop($('#tab_section_'+$(this).data('tab-target')).offset().top + $('.main-screen .main-screen').scrollTop() - $('.main-screen .main-screen').offset().top);
		return false;
	});
	setTimeout(function() {
		$('.main-screen .main-screen').scroll(function() {
			var screenTop = $('.main-screen .main-screen').offset().top + 10;
			var screenHeight = $('.main-screen .main-screen').innerHeight();
			$('.active.blue').removeClass('active blue');
			$('.tab-section').filter(function() { return $(this).offset().top + this.clientHeight > screenTop && $(this).offset().top < screenTop + screenHeight; }).each(function() {
				$('[data-tab-target='+$(this).attr('id').replace('tab_section_','')+']').find('li').addClass('active blue');
			});
		});
		$('.main-screen .main-screen').scroll();
	}, 500);
});
</script>
<?php $equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
$equipmentid = filter_var($_GET['edit'],FILTER_SANITIZE_STRING);
$get_equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='".$equipmentid."'"));

$unit_number = $get_equipment['unit_number'];
$category = $get_equipment['category'];
$make = $get_equipment['make'];
$model = $get_equipment['model'];
$last_oil_filter_change_date = $get_equipment['last_oil_filter_change_date'];
$last_oil_filter_change = $get_equipment['last_oil_filter_change'];
$last_oil_filter_change_hrs = $get_equipment['last_oil_filter_change_hrs'];
$next_oil_filter_change_date = $get_equipment['next_oil_filter_change_date'];
$next_oil_filter_change = $get_equipment['next_oil_filter_change'];
$next_oil_filter_change_hrs = $get_equipment['next_oil_filter_change_hrs'];
$last_insp_tune_up_date = $get_equipment['last_insp_tune_up_date'];
$last_insp_tune_up = $get_equipment['last_insp_tune_up'];
$last_insp_tune_up_hrs = $get_equipment['last_insp_tune_up_hrs'];
$next_insp_tune_up_date = $get_equipment['next_insp_tune_up_date'];
$next_insp_tune_up = $get_equipment['next_insp_tune_up'];
$next_insp_tune_up_hrs = $get_equipment['next_insp_tune_up_hrs'];
$last_tire_rotation_date = $get_equipment['last_tire_rotation_date'];
$last_tire_rotation = $get_equipment['last_tire_rotation'];
$last_tire_rotation_hrs = $get_equipment['last_tire_rotation_hrs'];
$next_tire_rotation_date = $get_equipment['next_tire_rotation_date'];
$next_tire_rotation = $get_equipment['next_tire_rotation'];
$next_tire_rotation_hrs = $get_equipment['next_tire_rotation_hrs'];
$mileage = $get_equipment['mileage'];
$hours_operated = $get_equipment['hours_operated'];
$status = $get_equipment['status'];
$service_staff = $get_equipment['service_staff']; ?>

<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
	<ul>
		<a href="?category=Top"><li>Back to Dashboard</li></a>
		<a href="" data-tab-target="staff"><li class="active blue">Staff</li></a>
		<a href="" data-tab-target="equip"><li>Equipment Details</li></a>
		<a href="" data-tab-target="oil"><li>Oil Change</li></a>
		<a href="" data-tab-target="tire"><li>Tire Rotation</li></a>
		<a href="" data-tab-target="tune"><li>Tune Up</li></a>
		<a href="" data-tab-target="status"><li>Status</li></a>
	</ul>
</div>

<div class="scale-to-fill has-main-screen" style="overflow: hidden;">
	<div class="main-screen standard-body form-horizontal">
		<div class="standard-body-title">
 			<h3>Equipment Unit #<?= $unit_number ?>: Service Schedule</h3>
		</div>

		<div class="standard-body-content" style="padding: 0.5em;">

			<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
				<div id="tab_section_staff" class="tab-section col-sm-12">
					<h4>Staff</h4>
					<?php $value_config = ',Service Staff,';
					include('add_equipment_fields.php'); ?>
					<div class="clearfix"></div><hr>
				</div>

				<div id="tab_section_equip" class="tab-section col-sm-12">
					<h4>Equipment Details</h4>
					<?php $value_config = ',Category,Make,Model,Unit #,';
					include('add_equipment_fields.php'); ?>
					<div class="clearfix"></div><hr>
				</div>

				<div id="tab_section_oil" class="tab-section col-sm-12">
					<h4>Oil Change</h4>
					<?php $value_config = ',Last Oil Filter Change (date),Last Oil Filter Change (km),Last Oil Filter Change (hrs),Next Oil Filter Change (date),Next Oil Filter Change (km),Next Oil Filter Change (hrs),';
					include('add_equipment_fields.php'); ?>
					<div class="clearfix"></div><hr>
				</div>

				<div id="tab_section_tire" class="tab-section col-sm-12">
					<h4>Tire Rotation</h4>
					<?php $value_config = ',Last Tire Rotation (date),Last Tire Rotation (km),Last Tire Rotation (hrs),Next Tire Rotation (date),Next Tire Rotation (km),Next Tire Rotation (hrs),';
					include('add_equipment_fields.php'); ?>
					<div class="clearfix"></div><hr>
				</div>

				<div id="tab_section_tune" class="tab-section col-sm-12">
					<h4>Tune Up</h4>
					<?php $value_config = ',Last Inspection & Tune Up (date),Last Inspection & Tune Up (km),Last Inspection & Tune Up (hrs),Next Inspection & Tune Up (date),Next Inspection & Tune Up (km),Next Inspection & Tune Up (hrs),';
					include('add_equipment_fields.php'); ?>
					<div class="clearfix"></div><hr>
				</div>

				<div id="tab_section_status" class="tab-section col-sm-12">
					<h4>Status</h4>
					<?php $value_config = ',Hours Operated,Mileage,Status,';
					include('add_equipment_fields.php'); ?>
					<div class="clearfix"></div><hr>
				</div>

				<div class="form-group">
					<div class="col-sm-6">
						<p><span class="brand-color"><em>Required Fields *</em></span></p>
					</div>
					<div class="col-sm-6">
						<div class="pull-right">
							<a href="?category=Top" class="btn brand-btn">Back</a>
							<button	type="submit" name="submit"	value="Submit" class="btn brand-btn">Submit</button>
						</div>
					</div>
				</div>

			</form>
		</div>
	</div>
</div>
