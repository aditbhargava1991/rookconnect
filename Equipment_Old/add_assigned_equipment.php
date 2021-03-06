<?php
/*
Add	Inventory
*/
include ('../include.php');
include('../phpsign/signature-to-image.php');
error_reporting(0);

if (isset($_POST['submit'])) {
	$user = get_contact($dbc,$_SESSION['contactid']);
	$equipmentid = $_POST['unit'];
	$staff = $_POST['staffid'];
	$assigned_region = $_POST['assigned_region'];
	$assigned_location = $_POST['assigned_location'];
	mysqli_query($dbc, "UPDATE `equipment` SET `assigned_staff`='$staff', `assigned_region` = '$assigned_region', `assigned_location` = '$assigned_location' WHERE `equipmentid`='$equipmentid'");
	foreach($_POST['assigned'] as $assignedid) {
		mysqli_query($dbc, "UPDATE `equipment` SET `assign_to_equip`='$equipmentid', `history`=CONCAT(IFNULL(CONCAT(`history`,'<br /> '),''),'Assigned to Equipment ID $equipmentid by $user at ".date('Y-m-d H:i')."') WHERE `equipmentid`='$assignedid' AND `assign_to_equip`!='$equipmentid'");
	}
	$assign_list = implode(',',$_POST['assigned']);
	if(!isset($_POST['assigned'])) {
		$assign_list = 0;
	}
	mysqli_query($dbc, "UPDATE `equipment` SET `assign_to_equip`=0, `history`=CONCAT(IFNULL(CONCAT(`history`,'<br /> '),''),'Removed from Equipment ID $equipmentid by $user at ".date('Y-m-d H:i')."') WHERE `equipmentid` NOT IN ($assign_list) AND `assign_to_equip`='$equipmentid'");

	echo "<script> window.location.replace('assign_equipment.php'); </script>";
} ?>
<script type="text/javascript">
$(document).ready(function () {
	var assigned_region = $('[name=assigned_region]').val();
	var assigned_location = $('[name=assigned_location]').val();
	// $('[name=assigned_location]').find('option').each(function() {
	// 	if(assigned_region) {
	// 		if($(this).data('region') != assigned_region) {
	// 			$(this).hide();
	// 		}
	// 	}
	// });
	// $('[name=assigned_location]').trigger('change.select2');
	$('[name=staffid]').find('option').each(function() {
		if(assigned_region) {
			if($(this).data('region') != assigned_region) {
				$(this).hide();
			}
		}
		if(assigned_location) {
			if($(this).data('location') != assigned_location) {
				$(this).hide();
			}
		}
	});
	$('[name=staffid]').trigger('change.select2');

	//Equipment Details
	$("[name=category]").change(function() {
		window.location.replace('add_assigned_equipment.php?category='+this.value);
	});
	$("[name=label]").change(function() {
		$('[name=make]').val('');
		$('[name=make]').find('option').hide();
		$('[name=model]').val('');
		$('[name=model]').find('option').hide();
		$('[name=unit]').val('');
		$('[name=unit]').find('option').hide();
		if(this.value != '') {
			$('[name=category]').val($(this).find('option:selected').data('category')).trigger('change.select2');
			$('[name=make]').find('[data-label="'+this.value+'"][data-category="'+$('[name=category]').val()+'"]').show();
			$('[name=model]').find('[data-label="'+this.value+'"][data-category="'+$('[name=category]').val()+'"]').show();
			$('[name=unit]').find('[data-label="'+this.value+'"][data-category="'+$('[name=category]').val()+'"]').show();
		} else {
			$('[name=make]').find('[data-category="'+$('[name=category]').val()+'"]').show();
			$('[name=model]').find('[data-category="'+$('[name=category]').val()+'"]').show();
			$('[name=unit]').find('[data-category="'+$('[name=category]').val()+'"]').show();
		}
		$('[name=make]').trigger('change.select2');
		$('[name=model]').trigger('change.select2');
		$('[name=unit]').trigger('change.select2');
	});
	$("[name=make]").change(function() {
		$('[name=model]').val('');
		$('[name=model]').find('option').hide();
		$('[name=unit]').val('');
		$('[name=unit]').find('option').hide();
		if(this.value != '') {
			$('[name=category]').val($(this).find('option:selected').data('category')).trigger('change.select2');
			$('[name=label]').val($(this).find('option:selected').data('label')).trigger('change.select2');
			$('[name=model]').find('[data-make="'+this.value+'"][data-label="'+$('[name=label]').val()+'"][data-category="'+$('[name=category]').val()+'"]').show();
			$('[name=unit]').find('[data-make="'+this.value+'"][data-label="'+$('[name=label]').val()+'"][data-category="'+$('[name=category]').val()+'"]').show();
		} else {
			$('[name=model]').find('[data-label="'+$('[name=label]').val()+'"][data-category="'+$('[name=category]').val()+'"]').show();
			$('[name=unit]').find('[data-label="'+$('[name=label]').val()+'"][data-category="'+$('[name=category]').val()+'"]').show();
		}
		$('[name=model]').trigger('change.select2');
		$('[name=unit]').trigger('change.select2');
	});
	$("[name=model]").change(function() {
		$('[name=unit]').val('');
		$('[name=unit]').find('option').hide();
		if(this.value != '') {
			$('[name=category]').val($(this).find('option:selected').data('category')).trigger('change.select2');
			$('[name=label]').val($(this).find('option:selected').data('label')).trigger('change.select2');
			$('[name=make]').val($(this).find('option:selected').data('make')).trigger('change.select2');
			$('[name=unit]').find('[data-model="'+this.value+'"][data-make="'+$('[name=make]').val()+'"][data-label="'+$('[name=label]').val()+'"][data-category="'+$('[name=category]').val()+'"]').show();
		} else {
			$('[name=unit]').find('[data-make="'+$('[name=make]').val()+'"][data-label="'+$('[name=label]').val()+'"][data-category="'+$('[name=category]').val()+'"]').show();
		}
		$('[name=unit]').trigger('change.select2');
	});
	$("[name=unit]").change(function() {
		if(this.value != '') {
			$('[name=category]').val($(this).find('option:selected').data('category')).trigger('change.select2');
			$('[name=label]').val($(this).find('option:selected').data('label')).trigger('change.select2');
			$('[name=make]').val($(this).find('option:selected').data('make')).trigger('change.select2');
			$('[name=model]').val($(this).find('option:selected').data('model')).trigger('change.select2');
		}
	});

	//Assignment Information
	$("[name=assigned_region]").change(function() {
		$('[name=staffid]').val('');
		// $('[name=assigned_location]').val('');
		if(this.value != '') {
			$('[name=staffid]').find('option').hide();
			$('[name=staffid]').find('[data-region="'+this.value+'"]').show();
			// $('[name=assigned_location]').find('option').hide();
			// $('[name=assigned_location]').find('[data-region="'+this.value+'"]').show();
		} else {
			$('[name=staffid]').find('option').show();
			// $('[name=assigned_location]').find('option').show();
		}
		$('[name=staffid]').trigger('change.select2');
		// $('[name=assigned_location]').trigger('change.select2');
	});
	$("[name=assigned_location]").change(function() {
		$('[name=staffid]').val('');
		if(this.value != '') {
			$('[name=staffid]').find('option').hide();
			$('[name=staffid]').find('[data-location="'+this.value+'"]').show();
			// $('[name=assigned_region]').val($(this).find('option:selected').data('region')).trigger('change.select2');
		} else {
			$('[name=staffid]').find('option').show();
		}
		$('[name=staffid]').trigger('change.select2');
	});
	$("[name=staffid]").change(function() {
		if(this.value != '') {
			$('[name=assigned_region]').val($(this).find('option:selected').data('region')).trigger('change.select2');
			$('[name=assigned_location]').val($(this).find('option:selected').data('location')).trigger('change.select2');
		}
	});
});

function addStaffEquipment() {
	var clone = $('.additional_doc').first().clone();
	clone.find('option:selected').removeAttr('selected');
	resetChosen(clone.find('.chosen-select-deselect'));
	$('#add_here_new_doc').append(clone);
}
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('equipment');
include_once('../Equipment/region_location_access.php'); ?>
<div class="container">
  <div class="row">

		<h1>Equipment Assignments</h1>

		<div class="pad-left gap-top double-gap-bottom"><a href="assign_equipment.php" class="btn brand-btn">Back to Dashboard</a></div>

		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
		
		<?php $equipmentid = '';
		$category = '';
		$label = '';
		$make = '';
		$model = '';
		$assigned_staff = '';
		$assigned_region = '';
		$assigned_location = '';

		if(!empty($_GET['equipmentid'])) {
			$equipmentid = $_GET['equipmentid'];
			$equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `label`, `make`, `model`, `assigned_staff`, `assigned_region`, `assigned_location` FROM `equipment` WHERE `equipmentid`='$equipmentid'"));
			$category = $equipment['category'];
			$label = $equipment['label'];
			$make = $equipment['make'];
			$model = $equipment['model'];
			$assigned_staff = $equipment['assigned_staff'];
			$assigned_region = $equipment['assigned_region'];
			$assigned_location = $equipment['assigned_location'];
		}
		else if(!empty($_GET['category'])) {
			$category = $_GET['category'];
		}
		$get_field_config = ','.mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`equipment` SEPARATOR ',') as all_fields FROM `field_config_equipment` WHERE `tab` = '$category'"))['all_fields'].',';
		?>

        <div class="panel-group" id="accordion2">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_equipment" >
							Equipment Details<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_equipment" class="panel-collapse collapse <?= !empty($_GET['category']) ? 'in' : '' ?>">
					<div class="panel-body">
						<div class="form-group">
							<label for="fax_number"	class="col-sm-4	control-label">Category:</label>
							<div class="col-sm-8">
								<select name="category" data-placeholder="Select a Category" class="chosen-select-deselect form-control"><option></option>
									<?php $list = mysqli_query($dbc, "SELECT `category` FROM `equipment` WHERE `deleted`=0 $access_query GROUP BY `category`");
									while($row = mysqli_fetch_array($list)) {
										echo "<option ".($category == $row['category'] ? 'selected' : '')." value='".$row['category']."'>".$row['category']."</option>";
									} ?>
								</select>
							</div>
						</div>
						<?php  if (strpos($get_field_config, ',Label,') !== FALSE) { ?>
							<div class="form-group">
								<label for="fax_number"	class="col-sm-4	control-label">Label:</label>
								<div class="col-sm-8">
									<select name="label" data-placeholder="Select a Label" class="chosen-select-deselect form-control"><option></option>
										<?php $list = mysqli_query($dbc, "SELECT `category`, `label` FROM `equipment` WHERE `deleted`=0 $access_query GROUP BY `label`");
										while($row = mysqli_fetch_array($list)) {
											echo "<option ".($label == $row['label'] ? 'selected' : ($category != $row['category'] ? 'style="display:none;"' : ''))." value='".$row['label']."' data-category='".$row['category']."'>".$row['label']."</option>";
										} ?>
									</select>
								</div>
							</div>
						<?php } ?>
						<?php  if (strpos($get_field_config, ',Make,') !== FALSE) { ?>
							<div class="form-group">
								<label for="fax_number"	class="col-sm-4	control-label">Make:</label>
								<div class="col-sm-8">
									<select name="make" data-placeholder="Select a Make" class="chosen-select-deselect form-control"><option></option>
										<?php $list = mysqli_query($dbc, "SELECT `category`, `label`, `make` FROM `equipment` WHERE `deleted`=0 $access_query GROUP BY `make`");
										while($row = mysqli_fetch_array($list)) {
											echo "<option ".($make == $row['make'] ? 'selected' : ($category != $row['category'] ? 'style="display:none;"' : ''))." value='".$row['make']."' data-category='".$row['category']."' data-label='".$row['label']."'>".$row['make']."</option>";
										} ?>
									</select>
								</div>
							</div>
						<?php } ?>
						<?php  if (strpos($get_field_config, ',Model,') !== FALSE) { ?>
							<div class="form-group">
								<label for="fax_number"	class="col-sm-4	control-label">Model:</label>
								<div class="col-sm-8">
									<select name="model" data-placeholder="Select a Model" class="chosen-select-deselect form-control"><option></option>
										<?php $list = mysqli_query($dbc, "SELECT `category`, `label`, `make`, `model` FROM `equipment` WHERE `deleted`=0 $access_query GROUP BY `model`");
										while($row = mysqli_fetch_array($list)) {
											echo "<option ".($model == $row['model'] ? 'selected' : ($category != $row['category'] ? 'style="display:none;"' : ''))." value='".$row['model']."' data-category='".$row['category']."' data-label='".$row['label']."' data-make='".$row['make']."'>".$row['model']."</option>";
										} ?>
									</select>
								</div>
							</div>
						<?php } ?>
						<?php  if (strpos($get_field_config, ',Unit #,') !== FALSE) { ?>
							<div class="form-group">
								<label for="fax_number"	class="col-sm-4	control-label">Unit Number:</label>
								<div class="col-sm-8">
									<select name="unit" data-placeholder="Select a Unit Number" class="chosen-select-deselect form-control"><option></option>
										<?php $list = mysqli_query($dbc, "SELECT `category`, `label`, `make`, `model`, `unit_number`, `equipmentid` FROM `equipment` WHERE `deleted`=0 $access_query");
										while($row = mysqli_fetch_array($list)) {
											echo "<option ".($equipmentid == $row['equipmentid'] ? 'selected' : ($category != $row['category'] ? 'style="display:none;"' : ''))." value='".$row['equipmentid']."' data-category='".$row['category']."' data-label='".$row['label']."' data-make='".$row['make']."' data-model='".$row['model']."'>".$row['unit_number']."</option>";
										} ?>
									</select>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_info" >
							Assignment Information<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_info" class="panel-collapse collapse">
					<div class="panel-body">
						<?php if (get_config($dbc, 'assign_equip_region_field') == 1) { ?>
							<div class="form-group">
								<label for="fax_number"	class="col-sm-4	control-label">Region:</label>
								<div class="col-sm-8">
									<select name="assigned_region" data-placeholder="Select Region" class="chosen-select-deselect form-control"><option></option>
										<?php $region_list = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0])));
										foreach ($region_list as $region) {
											if(in_array($region, $allowed_regions)) {
												echo "<option ".($region == $assigned_region ? 'selected' : '')." value='$region'>$region</option>";
											}
										} ?>
									</select>
								</div>
							</div>
						<?php } ?>

						<?php if (get_config($dbc, 'assign_equip_location_field') == 1) { ?>
							<div class="form-group">
								<label for="fax_number"	class="col-sm-4	control-label">Location:</label>
								<div class="col-sm-8">
									<select name="assigned_location" data-placeholder="Select Location" class="chosen-select-deselect form-control"><option></option>
										<?php $location_list = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `con_locations` SEPARATOR ',') FROM `field_config_contacts`"))[0])));
										foreach ($location_list as $location) {
											if (in_array($location, $allowed_locations)) {
												echo "<option ".($location == $assigned_location ? 'selected' : '')." value='$location'>$location</option>";
											}
										} ?>
									</select>
								</div>
							</div>
						<?php } ?>

						<div class="form-group">
							<label for="fax_number"	class="col-sm-4	control-label">Staff:</label>
							<div class="col-sm-8">
								<select name="staffid" data-placeholder="Select Staff" class="chosen-select-deselect form-control"><option></option>
									<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `last_name`, `first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1"),MYSQLI_ASSOC));
									foreach($staff_list as $id) {
										echo "<option ".($id == $assigned_staff ? 'selected' : '')." value='$id' data-location='".get_contact($dbc, $id, 'con_locations')."' data-region='".get_contact($dbc, $id, 'region')."'>".get_contact($dbc, $id)."</option>";
									} ?>
								</select>
							</div>
						</div>
						<?php $equipment_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `assign_to_equip`='".$equipmentid."' AND `assign_to_equip` != 0"),MYSQLI_ASSOC);
						for ($i = 0; $i < count($equipment_list) || $i < 1; $i++) { ?>
						<div class="form-group additional_doc">
							<label for="fax_number"	class="col-sm-4	control-label">Equipment Assigned:</label>
							<div class="col-sm-8">
								<select name="assigned[]" data-placeholder="Select Equipment" class="chosen-select-deselect form-control"><option></option>
									<?php $equip_list = mysqli_query($dbc, "SELECT `equipmentid`, `unit_number`, `category`, `label`, `make`, `model`, `assign_to_equip` FROM `equipment` WHERE `deleted`=0 $access_query ORDER BY `category`, `make`, `model`, `unit_number`");
									while($equip_row = mysqli_fetch_array($equip_list)) {
										$assigned_to = mysqli_fetch_array(mysqli_query($dbc, "SELECT `unit_number`, `category`, `label`, `make`, `model` FROM `equipment` WHERE `equipmentid`='".$equip_row['assign_to_equip']."'"));
										echo "<option ".($equipment_list[$i]['equipmentid'] == $equip_row['equipmentid'] ? 'selected' : '')." value='".$equip_row['equipmentid']."'>".$equip_row['category']." ".$equip_row['make']." ".$equip_row['model']." ".$equip_row['unit_number'].($equip_row['assign_to_equip'] > 0 && $equip_row['assign_to_equip'] != $equipmentid ? ' (Assigned to '.$assigned_to['category']." ".$assigned_to['make']." ".$assigned_to['model']." ".$assigned_to['unit_number'].')' : '')."</option>";
									} ?>
								</select>
							</div>
						</div>
						<?php } ?>
						<div id="add_here_new_doc"></div>
						<button id="add_row_doc" class="btn brand-btn pull-right" onclick="addStaffEquipment(); return false;">Add Equipment</button>

					</div>
				</div>
			</div>
        </div>

		<div class="form-group">
			<p><span class="brand-color"><em>Required	Fields *</em></span></p>
		</div>

		  <div class="form-group">
			<div class="col-sm-6">
				<a href="assign_equipment.php"	class="btn brand-btn btn-lg">Back</a>
			</div>
			<div class="col-sm-6">
				<button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button>
			</div>
		  </div>

		</form>

	</div>
  </div>

<?php include ('../footer.php'); ?>