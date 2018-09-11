<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');

if (isset($_POST['submit'])) {
	//Work Order
	$workorderid = $_POST['workorderid'];
	$staffid = $_POST['staffid'];
	$date = date('Y-m-d');
	$date_scheduled = $_POST['date_scheduled'];
	$equipmentid = $_POST['unit'];
	$serviceid = implode(',',$_POST['service_heading']);
	$misc = filter_var(htmlentities(implode('FFMSPLIT',$_POST['misc'])),FILTER_SANITIZE_STRING);
	$service_description = filter_var(htmlentities($_POST['service_description']),FILTER_SANITIZE_STRING);
	$last_oil_km = $_POST['last_oil_km'];
	$last_oil_date = $_POST['last_oil_date'];
	$next_oil_km = $_POST['next_oil_km'];
	$next_oil_date = $_POST['next_oil_date'];
	$last_tire_rotate_km = $_POST['last_tire_rotation_km'];
	$last_tire_rotate_date = $_POST['last_tire_rotation_date'];
	$next_tire_rotate_km = $_POST['next_tire_rotation_km'];
	$next_tire_rotate_date = $_POST['next_tire_rotation_date'];
	$last_tune_up_km = $_POST['last_tune_up_km'];
	$last_tune_up_date = $_POST['last_tune_up_date'];
	$next_tune_up_km = $_POST['next_tune_up_km'];
	$next_tune_up_date = $_POST['next_tune_up_date'];

	if(empty($_POST['workorderid'])) {
		$sql = "INSERT INTO `equipment_work_orders` (`staffid`, `date`, `date_scheduled`, `equipmentid`, `serviceid`, `misc`, `service_description`, `last_oil_km`, `last_oil_date`, `next_oil_km`, `next_oil_date`, `last_tire_rotate_km`, `last_tire_rotate_date`, `next_tire_rotate_km`, `next_tire_rotate_date`, `last_tune_up_km`, `last_tune_up_date`, `next_tune_up_km`, `next_tune_up_date`, `status`) VALUES ('$staffid', '$date', '$date_scheduled', '$equipmentid', '$serviceid', '$misc', '$service_description', '$last_oil_km', '$last_oil_date', '$next_oil_km', '$next_oil_date', '$last_tire_rotate_km', '$last_tire_rotate_date', '$next_tire_rotate_km', '$next_tire_rotate_date', '$last_tune_up_km', '$last_tune_up_date', '$next_tune_up_km', '$next_tune_up_date', 'Pending')";
		mysqli_query($dbc, $sql);
		$workorderid = mysqli_insert_id($dbc);
		$before_change = '';
		$history = "New equipment work order Added. <br />";
		add_update_history($dbc, 'equipment_history', $history, '', $before_change);
		$sql = "UPDATE `equipment_wo_checklist` SET `workorderid`='$workorderid' WHERE `workorderid`='0' AND `created_by`='".$_SESSION['contactid']."'";
		mysqli_query($dbc, $sql);
		$before_change = '';
		$history = "Equipment Workorder checklist updated. <br />";
		add_update_history($dbc, 'equipment_history', $history, '', $before_change);
	} else {
		$sql = "UPDATE `equipment_work_orders` SET `staffid`='$staffid', `date`='$date', `date_scheduled`='$date_scheduled', `equipmentid`='$equipmentid', `serviceid`='$serviceid', `misc`='$misc', `service_description`='$service_description', `last_oil_km`='$last_oil_km', `last_oil_date`='$last_oil_date', `next_oil_km`='$next_oil_km', `next_oil_date`='$next_oil_date', `last_tire_rotate_km`='$last_tire_rotate_km', `last_tire_rotate_date`='$last_tire_rotate_date', `next_tire_rotate_km`='$next_tire_rotate_km', `next_tire_rotate_date`='$next_tire_rotate_date', `last_tune_up_km`='$last_tune_up_km', `last_tune_up_date`='$last_tune_up_date', `next_tune_up_km`='$next_tune_up_km', `next_tune_up_date`='$next_tune_up_date' WHERE `workorderid`='".$_POST['workorderid']."'";
		mysqli_query($dbc, $sql);
		$before_change = '';
		$history = "Equipment Workorder updated. <br />";
		add_update_history($dbc, 'equipment_history', $history, '', $before_change);
	}
	mysqli_query($dbc, $sql);

	//Inventory
	foreach($_POST['in_part_no'] as $i => $inventoryid) {
		$lineid = $_POST['in_lineid'][$i];
		$qty = $_POST['in_qty'][$i];
		if($qty > 0 && $inventoryid > 0) {
			$old_qty = 0;
			$unit_cost = $_POST['in_unit_cost'][$i];
			$unit_total = $_POST['in_unit_total'][$i];
			if($lineid > 0) {
				$old_qty = mysqli_fetch_array(mysqli_query($dbc, "SELECT `qty` FROM `equipment_inventory` WHERE `lineid`='$lineid'"))['qty'];
				mysqli_query($dbc, "UPDATE `equipment_inventory` SET `workorderid`='$workorderid', `inventoryid`='$inventoryid', `qty`='$qty', `unit_cost`='$unit_cost', `unit_total`='$unit_total' WHERE `lineid`='$lineid'");
				$before_change = '';
				$history = "Equipment inventory updated. <br />";
				add_update_history($dbc, 'equipment_history', $history, '', $before_change);
			} else {
				mysqli_query($dbc, "INSERT INTO `equipment_inventory` (`workorderid`, `inventoryid`, `qty`, `unit_cost`, `unit_total`) VALUES('$workorderid', '$inventoryid', '$qty', '$unit_cost', '$unit_total')");
				$before_change = '';
        $history = "New equipment inventory Added. <br />";
        add_update_history($dbc, 'equipment_history', $history, '', $before_change);
			}
			$diff = $qty - $old_qty;
			mysqli_query($dbc, "UPDATE `inventory` SET `quantity`=`quantity` - $diff WHERE `inventoryid`='$inventoryid'");
		}
	}

	//Purchase Order
	foreach($_POST['poid'] as $i => $poid) {
		$detail = filter_var($_POST['po_detail'][$i],FILTER_SANITIZE_STRING);
		$qty = $_POST['po_qty'][$i];
		$uom = filter_var($_POST['po_uom'][$i],FILTER_SANITIZE_STRING);
		$unit_price = $_POST['po_unit_price'][$i];
		$unit_tax = $_POST['po_unit_tax'][$i];
		$unit_total = $_POST['po_unit_total'][$i];
		if($detail != '' || $qty != '') {
			$file = $_POST['po_file_exist'][$i];
			if($_FILES['po_file']['name'][$i] != '') {
				if (!file_exists('download')) {
					mkdir('download', 0777, true);
				}
				$basename = $file = preg_replace('/[^A-Za-z0-9\.]/','_',$_FILES['po_file']['name'][$i]);
				$i = 0;
				while(file_exists('download/'.$file)) {
					$file = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.++$i.')$1', $basename);
				}
				move_uploaded_file($_FILES['po_file']['tmp_name'][$i], 'download/'.$file);
			}
			if($poid > 0) {
				mysqli_query($dbc, "UPDATE `equipment_purchase_order_items` SET `workorderid`='$workorderid', `file`='$file', `qty`='$qty', `uom`='$uom', `detail`='$detail', `unit_price`='$unit_price', `unit_tax`='$unit_tax', `unit_total`='$unit_total' WHERE `poid`='$poid'");
				$before_change = '';
				$history = "Equipment purchase order items updated. <br />";
				add_update_history($dbc, 'equipment_history', $history, '', $before_change);
			} else {
				mysqli_query($dbc, "INSERT INTO `equipment_purchase_order_items` (`workorderid`, `file`, `qty`, `uom`, `detail`, `unit_price`, `unit_tax`, `unit_total`) VALUES ('$workorderid', '$file', '$qty', '$uom', '$detail', '$unit_price', '$unit_tax', '$unit_total')");
				$before_change = '';
        $history = "New equipment purchse order items Added. <br />";
        add_update_history($dbc, 'equipment_history', $history, '', $before_change);
			}
		}
	}
	if(empty($_GET['edit'])) {
		echo "<script> window.location.replace('?tab=work_orders'); </script>";
	} else {
		echo "<script> window.location.replace('?edit=".$equipmentid."&subtab=work_orders'); </script>";
	}
} ?>
<script type="text/javascript">
$(document).ready(function () {
	$("[name=category]").change(function() {
		$('[name=make]').find('option').hide();
		$('[name=make]').find('[data-category="'+this.value+'"]').show();
		$('[name=make]').trigger('change.select2');
		$('[name=model]').find('option').hide();
		$('[name=model]').find('[data-category="'+this.value+'"]').show();
		$('[name=model]').trigger('change.select2');
		$('[name=unit]').find('option').hide();
		$('[name=unit]').find('[data-category="'+this.value+'"]').show();
		$('[name=unit]').trigger('change.select2');
	});
	$("[name=make]").change(function() {
		if(this.value != '') {
			$('[name=category]').val($(this).find('option:selected').data('category')).trigger('change.select2');
		}
		$('[name=model]').find('option').hide();
		$('[name=model]').find('[data-make="'+this.value+'"]').show();
		$('[name=model]').trigger('change.select2');
		$('[name=unit]').find('option').hide();
		$('[name=unit]').find('[data-make="'+this.value+'"]').show();
		$('[name=unit]').trigger('change.select2');
	});
	$("[name=model]").change(function() {
		if(this.value != '') {
			$('[name=category]').val($(this).find('option:selected').data('category')).trigger('change.select2');
			$('[name=make]').val($(this).find('option:selected').data('make')).trigger('change.select2');
		}
		$('[name=unit]').find('option').hide();
		$('[name=unit]').find('[data-model="'+this.value+'"]').show();
		$('[name=unit]').trigger('change.select2');
	});
	$("[name=unit]").change(function() {
		if(this.value != '') {
			$('[name=category]').val($(this).find('option:selected').data('category')).trigger('change.select2');
			$('[name=make]').val($(this).find('option:selected').data('make')).trigger('change.select2');
			$('[name=model]').val($(this).find('option:selected').data('model')).trigger('change.select2');
			$('#status').val($(this).find('option:selected').data('status')).trigger('change.select2');
			$.ajax({
				url: 'equipment_ajax.php?fill=get_equipment_inspections&equipment='+this.value,
				method: 'GET',
				success: function(response) {
					$('#collapse_inspection .panel-body').html(response);
				}
			});
		}
	});

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
<?php include_once('../Equipment/region_location_access.php');

$staff = $_SESSION['contactid'];
$date_created = date('Y-m-d');
$date_scheduled = '';
$equipmentid = $_GET['edit'];
$serviceid = 0;
$misc = '';
$service_description = '';
$last_oil_km = 0;
$last_oil_date = '';
$next_oil_km = 0;
$next_oil_date = '';
$last_tire_rotation_km = 0;
$last_tire_rotation_date = '';
$next_tire_rotation_km = 0;
$next_tire_rotation_date = '';
$last_tune_up_km = 0;
$last_tune_up_date = '';
$next_tune_up_km = 0;
$next_tune_up_date = '';
$status = 'Pending';

if(!empty($equipmentid)) {
	$equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='$equipmentid'"));
	$last_oil_km = $equipment['last_oil_filter_change'];
	$last_oil_date = $equipment['last_oil_filter_change_date'];
	$next_oil_km = $equipment['next_oil_filter_change'];
	$next_oil_date = $equipment['next_oil_filter_change_date'];
	$last_tire_rotation_km = $equipment['last_tire_rotation'];
	$last_tire_rotation_date = $equipment['last_tire_rotation_date'];
	$next_tire_rotation_km = $equipment['next_tire_rotation'];
	$next_tire_rotation_date = $equipment['next_tire_rotation_date'];
	$last_tune_up_km = $equipment['last_insp_tune_up'];
	$last_tune_up_date = $equipment['last_insp_tune_up_date'];
	$next_tune_up_km = $equipment['next_insp_tune_up'];
	$next_tune_up_date = $equipment['next_insp_tune_up_date'];
}

if(!empty($_GET['category'])) {
	$category = $_GET['category'];
}
else if(!empty($_GET['workorderid'])) {
	$workorderid = $_GET['workorderid'];
	$workorder = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment_work_orders` WHERE `workorderid`='$workorderid'"));
	$staff = $workorder['staffid'];
	$date_created = $workorder['date'];
	$date_scheduled = $workorder['date_scheduled'];
	$equipmentid = $workorder['equipmentid'];
	$serviceid = $workorder['serviceid'];
	$misc = $workorder['misc'];
	$service_description = $workorder['service_description'];
	$last_oil_km = $workorder['last_oil_km'];
	$last_oil_date = $workorder['last_oil_date'];
	$next_oil_km = $workorder['next_oil_km'];
	$next_oil_date = $workorder['next_oil_date'];
	$last_tire_rotation_km = $workorder['last_tire_rotate_km'];
	$last_tire_rotation_date = $workorder['last_tire_rotate_date'];
	$next_tire_rotation_km = $workorder['next_tire_rotate_km'];
	$next_tire_rotation_date = $workorder['next_tire_rotate_date'];
	$last_tune_up_km = $workorder['last_tune_up_km'];
	$last_tune_up_date = $workorder['last_tune_up_date'];
	$next_tune_up_km = $workorder['next_tune_up_km'];
	$next_tune_up_date = $workorder['next_tune_up_date'];
	$status = $workorder['status'];
}
if(!empty($equipmentid)) {
	$equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='$equipmentid'"));
	$category = $equipment['category'];
	$make = $equipment['make'];
	$model = $equipment['model'];
	$equipment_status = $equipment['status'];
} ?>

<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
	<ul>
		<a href="?<?= empty($equipmentid) ? 'tab=work_orders' : 'edit='.$equipmentid.'&subtab=work_orders' ?>"><li>Back to Dashboard</li></a>
		<a href="" data-tab-target="info"><li class="active blue">Work Order Details</li></a>
		<a href="" data-tab-target="equipment"><li>Equipment</li></a>
		<a href="" data-tab-target="inspection"><li>Inspection History</li></a>
		<a href="" data-tab-target="service"><li>Service Description</li></a>
		<a href="" data-tab-target="inventory"><li>Use Inventory</li></a>
		<a href="" data-tab-target="po"><li>Purchase Order</li></a>
		<a href="" data-tab-target="oil"><li>Oil Change</li></a>
		<a href="" data-tab-target="rotate"><li>Tire Rotation</li></a>
		<a href="" data-tab-target="tune_up"><li>Tune Up</li></a>
		<a href="" data-tab-target="status"><li>Status</li></a>
	</ul>
</div>

<div class="scale-to-fill has-main-screen" style="overflow: hidden;">
	<div class="main-screen standard-body form-horizontal">
		<div class="standard-body-title">
 			<h3><?= ($_GET['workorderid'] > 0 ? 'Edit' : 'Add') ?> Work Order</h3>
		</div>

		<div class="standard-body-content">

			<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

				<input type="hidden" name="workorderid" value="<?= $workorderid ?>">

				<div id="tab_section_info" class="tab-section col-sm-12">
					<h4>Work Order Details</h4>
					<div class="form-group">
						<label for="fax_number"	class="col-sm-4	control-label">Work Order #:</label>
						<div class="col-sm-8">
							<?= $_GET['workorderid'] > 0 ? $_GET['workorderid'] : 'New' ?>
						</div>
					</div>
					<div class="form-group">
						<label for="fax_number"	class="col-sm-4	control-label">Staff:</label>
						<div class="col-sm-8">
							<select name="staffid" data-placeholder="Select Staff" class="chosen-select-deselect form-control"><option></option>
								<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `last_name`, `first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
								foreach($staff_list as $id) {
									echo "<option ".($id == $staff ? 'selected' : '')." value='$id'>".get_contact($dbc, $id)."</option>";
								} ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="fax_number"	class="col-sm-4	control-label">Date Created:</label>
						<div class="col-sm-8">
							<input type="text" name="date" readonly value="<?= $date_created ?>" class="form-control datepicker">
						</div>
					</div>
					<div class="form-group">
						<label for="fax_number"	class="col-sm-4	control-label">Date Scheduled:</label>
						<div class="col-sm-8">
							<input type="text" name="date_scheduled" value="<?= $date_scheduled ?>" class="form-control datepicker">
						</div>
					</div>
					<div class="clearfix"></div><hr>
				</div>

				<div id="tab_section_equipment" class="tab-section col-sm-12">
					<h4>Equipment</h4>
					<div class="form-group">
						<label for="fax_number"	class="col-sm-4	control-label">Tab:</label>
						<div class="col-sm-8">
							<select name="category" data-placeholder="Select a Tab" class="chosen-select-deselect form-control"><option></option>
								<?php $list = mysqli_query($dbc, "SELECT `category` FROM `equipment` WHERE `deleted`=0 $access_query GROUP BY `category`");
								while($row = mysqli_fetch_array($list)) {
									echo "<option ".($category == $row['category'] ? 'selected' : '')." value='".$row['category']."'>".$row['category']."</option>";
								} ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="fax_number"	class="col-sm-4	control-label">Make:</label>
						<div class="col-sm-8">
							<select name="make" data-placeholder="Select a Make" class="chosen-select-deselect form-control"><option></option>
								<?php $list = mysqli_query($dbc, "SELECT `category`, `make` FROM `equipment` WHERE `deleted`=0 $access_query GROUP BY `make`");
								while($row = mysqli_fetch_array($list)) {
									echo "<option ".($make == $row['make'] ? 'selected' : ($category != $row['category'] ? 'style="display:none;"' : ''))." value='".$row['make']."' data-category='".$row['category']."'>".$row['make']."</option>";
								} ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="fax_number"	class="col-sm-4	control-label">Model:</label>
						<div class="col-sm-8">
							<select name="model" data-placeholder="Select a Model" class="chosen-select-deselect form-control"><option></option>
								<?php $list = mysqli_query($dbc, "SELECT `category`, `make`, `model` FROM `equipment` WHERE `deleted`=0 $access_query GROUP BY `model`");
								while($row = mysqli_fetch_array($list)) {
									echo "<option ".($model == $row['model'] ? 'selected' : ($category != $row['category'] ? 'style="display:none;"' : ''))." value='".$row['model']."' data-category='".$row['category']."' data-make='".$row['make']."'>".$row['model']."</option>";
								} ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="fax_number"	class="col-sm-4	control-label">Unit #:</label>
						<div class="col-sm-8">
							<select name="unit" data-placeholder="Select a Unit #" class="chosen-select-deselect form-control"><option></option>
								<?php $list = mysqli_query($dbc, "SELECT `category`, `make`, `model`, `unit_number`, `equipmentid`, `status` FROM `equipment` WHERE `deleted`=0 $access_query");
								while($row = mysqli_fetch_array($list)) {
									echo "<option ".($equipmentid == $row['equipmentid'] ? 'selected' : ($category != $row['category'] ? 'style="display:none;"' : ''))." value='".$row['equipmentid']."' data-category='".$row['category']."' data-make='".$row['make']."' data-model='".$row['model']."' data-status='".$row['status']."'>".$row['unit_number']."</option>";
								} ?>
							</select>
						</div>
					</div>
					<div class="clearfix"></div><hr>
				</div>

				<div id="tab_section_inspection" class="tab-section col-sm-12">
					<h4>Inspection History</h4>
					<?php include('add_work_order_inspection.php'); ?>
					<div class="clearfix"></div><hr>
				</div>

				<div id="tab_section_service" class="tab-section col-sm-12">
					<h4>Service Description</h4>
					<script>
					$(document).ready(function() {
						$('[name=add_service]').click(function() {
							var service = $('[name=service_list]').last().clone();
							service.find('.form-control').val('');
							resetChosen(service.find("select[class^=chosen]"));
							service.find('input[type=checkbox]').removeAttr('checked');
							service.find('[name=service_category]').change(change_serv_cat);
							service.find('[name="service_heading[]"]').change(change_serv_head);
							$(this).before(service);
						});
						$('[name=service_category]').change(change_serv_cat);
						$('[name="service_heading[]"]').change(change_serv_head);
						function change_serv_cat() {
							var heading = $(this).closest('[name=service_list]').find('[name="service_heading[]"]');
							if(this.value == '') {
								heading.find('option').show();
							} else {
								heading.find('option').hide();
								heading.find('[data-category="'+this.value+'"]').show();
								heading.trigger('change.select2');
							}
						}
						function change_serv_head() {
							if(this.value != '') {
								$(this).closest('[name=service_list]').find('[name="service_category"]').val($(this).find('option:selected').data('category')).trigger('change.select2');
							}
						}
					});
					function set_description(checkbox) {
						if(checkbox.checked) {
							tinyMCE.get('service_description').setContent(tinyMCE.get('service_description').getContent()+$(checkbox).closest('[name=service_list]').find('[name="service_heading[]"] option:selected').data('description'));
						}
					}
					function set_checklist(checkbox) {
						if(checkbox.checked) {
							var checklist = $(checkbox).closest('[name=service_list]').find('[name="service_heading[]"] option:selected').data('checklist').split('#*#');
							checklist.forEach(function(item) {
								$('.new_task_box input').val(item).change();
							});
						}
					}
					</script>
					<?php foreach(explode(',',$serviceid) as $i => $service) { ?>
						<div name="service_list">
							<?php $service = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `services` WHERE `serviceid`='$service'")); ?>
							<div class="form-group">
								<label class="col-sm-4 control-label">Service Category:</label>
								<div class="col-sm-8">
									<select data-placeholder="Select a Category..." name="service_category" class="chosen-select-deselect form-control" width="380">
										<option value=""></option>
										<?php $query = mysqli_query($dbc,"SELECT DISTINCT(category) FROM services WHERE deleted=0 ORDER BY category");
										while($row = mysqli_fetch_array($query)) {
											echo "<option ".($service['category'] == $row['category'] ? 'selected' : '')." value='". $row['category']."'>".$row['category'].'</option>';
										} ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Service Heading:</label>
								<div class="col-sm-8">
									<select data-placeholder="Select a Heading..." name="service_heading[]" class="chosen-select-deselect form-control" width="380">
										<option data-description=""></option>
										<?php $query = mysqli_query($dbc,"SELECT `serviceid`, `category`, `heading`, `description`, `checklist` FROM services WHERE deleted=0 ORDER BY `heading`");
										while($row = mysqli_fetch_array($query)) {
											echo "<option ".($service['heading'] == $row['heading'] ? 'selected' : '')." data-category='".$row['category']."' data-description='".$row['description']."' data-checklist='".$row['checklist']."' value='". $row['serviceid']."'>".$row['heading'].'</option>';
										} ?>
									</select>
									<label class="form-checkbox"><input type="checkbox" name="description_checkbox" onclick="set_description(this);"> Use Service Description</label>
									<label class="form-checkbox"><input type="checkbox" name="checklist_checkbox" onclick="set_checklist(this);"> Use Service Checklist</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Misc:</label>
								<div class="col-sm-8">
									<textarea name="misc[]" class="form-control"><?= explode('FFMSPLIT', $misc)[$i] ?></textarea>
								</div>
							</div>
						</div>
					<?php } ?>
					<button type="button" class="btn brand-btn pull-right" name="add_service" onclick="return false;">Add Another Series</button><div class="clearfix"></div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Service Description:</label>
						<div class="col-sm-8">
							<textarea name="service_description"><?= $service_description ?></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Checklist:</label>
						<div class="col-sm-8">
							<?php include('add_work_order_checklist.php'); ?>
						</div>
					</div>
					<div class="clearfix"></div><hr>
				</div>

				<div id="tab_section_inventory" class="tab-section col-sm-12">
					<h4>Use Inventory</h4>
					<?php include('add_work_order_inventory.php'); ?>
					<div class="clearfix"></div><hr>
				</div>

				<div id="tab_section_po" class="tab-section col-sm-12">
					<h4>Purchase Order</h4>
					<?php include('add_work_order_po.php'); ?>
					<div class="clearfix"></div><hr>
				</div>

				<div id="tab_section_oil" class="tab-section col-sm-12">
					<h4>Oil Change</h4>
					<div class="form-group">
						<label class="col-sm-4 control-label">Last Oil & Filter Change (KMs):</label>
						<div class="col-sm-8">
							<input type="number" min=0 name="last_oil_km" value="<?= $last_oil_km ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Last Oil & Filter Change Date:</label>
						<div class="col-sm-8">
							<input type="text" name="last_oil_date" value="<?= $last_oil_date ?>" class="form-control datepicker">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Next Oil & Filter Change (KMs):</label>
						<div class="col-sm-8">
							<input type="number" min=0 name="next_oil_km" value="<?= $next_oil_km ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Next Oil & Filter Change Date:</label>
						<div class="col-sm-8">
							<input type="text" name="next_oil_date" value="<?= $next_oil_date ?>" class="form-control datepicker">
						</div>
					</div>
					<div class="clearfix"></div><hr>
				</div>

				<div id="tab_section_rotate" class="tab-section col-sm-12">
					<h4>Tire Rotation</h4>
					<div class="form-group">
						<label class="col-sm-4 control-label">Last Tire Rotation (KMs):</label>
						<div class="col-sm-8">
							<input type="number" min=0 name="last_tire_rotation_km" value="<?= $last_tire_rotation_km ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Last Tire Rotation Date:</label>
						<div class="col-sm-8">
							<input type="text" name="last_tire_rotation_date" value="<?= $last_tire_rotation_date ?>" class="form-control datepicker">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Next Tire Rotation (KMs):</label>
						<div class="col-sm-8">
							<input type="number" min=0 name="next_tire_rotation_km" value="<?= $next_tire_rotation_km ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Next Tire Rotation Date:</label>
						<div class="col-sm-8">
							<input type="text" name="next_tire_rotation_date" value="<?= $next_tire_rotation_date ?>" class="form-control datepicker">
						</div>
					</div>
				</div>

				<div id="tab_section_tune_up" class="tab-section col-sm-12">
					<h4>Tune Up</h4>
					<div class="form-group">
						<label class="col-sm-4 control-label">Last Tune Up (KMs):</label>
						<div class="col-sm-8">
							<input type="number" min=0 name="last_tune_up_km" value="<?= $last_tune_up_km ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Last Tune Up Date:</label>
						<div class="col-sm-8">
							<input type="text" name="last_tune_up_date" value="<?= $last_tune_up_date ?>" class="form-control datepicker">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Next Tune Up (KMs):</label>
						<div class="col-sm-8">
							<input type="number" min=0 name="next_tune_up_km" value="<?= $next_tune_up_km ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Next Tune Up Date:</label>
						<div class="col-sm-8">
							<input type="text" name="next_tune_up_date" value="<?= $next_tune_up_date ?>" class="form-control datepicker">
						</div>
					</div>
					<div class="clearfix"></div><hr>
				</div>

				<div id="tab_section_status" class="tab-section col-sm-12">
					<h4>Status</h4>
					<div class="form-group">
						<label for="phone_number" class="col-sm-4 control-label">Equipment Status:</label>
						<div class="col-sm-8">
							<select id="status" name="equipment_status" class="chosen-select-deselect form-control" width="380">
							<option value=''></option>
							<option value='Active' <?php if ($equipment_status=='Active') echo 'selected="selected"';?> >Active</option>
							<option value='In Service' <?php if ($equipment_status=='In Service') echo 'selected="selected"';?> >In Service</option>
							<option value='Service Required' <?php if ($equipment_status=='Service Required') echo 'selected="selected"';?> >Service Required</option>
							<option value='On Site' <?php if ($equipment_status=='On Site') echo 'selected="selected"';?> >On Site</option>
							<option value='Inactive' <?php if ($equipment_status=='Inactive') echo 'selected="selected"';?> >Inactive</option>
							<option value='Sold' <?php if ($equipment_status=='Sold') echo 'selected="selected"';?> >Sold</option>
							</select>
						</div>
					</div>
					<div class="clearfix"></div><hr>
				</div>

				<div class="form-group">
					<div class="col-sm-6">
						<p><span class="brand-color"><em>Required Fields *</em></span></p>
					</div>
					<div class="col-sm-6">
						<div class="pull-right">
							<a href="?<?= empty($equipmentid) ? 'tab=workorders' : 'edit='.$equipmentid.'&subtab=work_orders' ?>" class="btn brand-btn">Back</a>
							<button	type="submit" name="submit"	value="Submit" class="btn brand-btn">Submit</button>
						</div>
					</div>
				</div>

			</form>
		</div>
	</div>
</div>
