<?php // Edit Staff Rate Card
if (isset($_POST['submit'])) {
	error_reporting(0);
	require_once('../include.php');
	$id = $_POST['submit'];
	$staffid = filter_var($_POST['staff_id'],FILTER_SANITIZE_STRING);
	$rate_card = filter_var($_POST['rate_card'],FILTER_SANITIZE_STRING);
	$start_date = filter_var($_POST['start_date'],FILTER_SANITIZE_STRING);
	$end_date = filter_var($_POST['end_date'],FILTER_SANITIZE_STRING);
	$alert_date = filter_var($_POST['alert_date'],FILTER_SANITIZE_STRING);
	$alert_staff = filter_var(implode(',',$_POST['alert_staff']),FILTER_SANITIZE_STRING);
	$daily = filter_var($_POST['daily'],FILTER_SANITIZE_STRING);
	$hourly = filter_var($_POST['hourly'],FILTER_SANITIZE_STRING);
	$unit_price = filter_var($_POST['unit_price'],FILTER_SANITIZE_STRING);
	$cost = filter_var($_POST['cost'],FILTER_SANITIZE_STRING);
	$uom = filter_var($_POST['uom'],FILTER_SANITIZE_STRING);
	$history = 'Staff rate card '.($id == '' ? 'Added' : 'Edited').' by '.get_contact($dbc, $_SESSION['contactid']).' on '.date('Y-m-d h:i:s');
	$sql = '';
	if($id == '') {
		$sql = "INSERT INTO `company_rate_card` (`rate_card_name`,`item_id`,`tile_name`,`start_date`,`end_date`,`daily`,`hourly`,`uom`,`cost`,`cust_price`,`history`,`created_by`,`alert_date`,`alert_staff`) VALUES
			('$rate_card','$staffid','Staff','$start_date','$end_date','$daily','$hourly','$uom','$cost','$unit_price','$history','".$_SESSION['contactid']."','$alert_date','$alert_staff')";
		$id = mysqli_insert_id($dbc);
	}
	else {
		$sql = "UPDATE `company_rate_card` SET `rate_card_name`='$rate_card',`description`=$category,`start_date`='$start_date',`end_date`='$end_date',`cost`='$cost',`cust_price`='$unit_price',`uom`='$uom',`daily`='$daily',`hourly`='$hourly',`history`=IFNULL(CONCAT(`history`,'<br />\n','$history'),'$history'),`alert_date`='$alert_date',`alert_staff`='$alert_staff' WHERE `rate_id`='$id'";
	}
	$result = mysqli_query($dbc, $sql);

    echo '<script type="text/javascript"> window.location.replace("?card=staff&type=staff"); </script>';
} ?>
<script>
$(document).ready(function() {
	$('[name="staff_id[]"]').change(function() {
		if(this.value == 'ALL_STAFF') {
			$(this).find('option').attr('selected', 'selected');
			$(this).find('option[value=ALL_STAFF]').removeAttr('selected');
			$(this).trigger('change.select2');
		}
	});

	$('[name="color_code_picker"]').change(function() {
		$('[name="color_code"]').val($(this).val());
	});

	$('[name="category"]').change(function() {
		var category_name = $(this).val();
		var rateid = '&rateid=<?php echo $_GET['id']; ?>';
		$.ajax({
			type: "GET",
			url: "ratecard_ajax_all.php?fill=staff_rate_order&category=" + category_name + rateid,
			dataType: "html",
			success: function(response) {
				console.log(response);
				$('[name="sort_order"]').html(response);
				$('[name="sort_order"]').trigger("change.select2");
			}
		});
	});
});
$(document).on('change', 'select#category_select', function() { if(this.value == 'ADD_NEW') { $('.category_input').show(); $('.category_select').hide(); } else { $('[name=category]').val(this.value); } });
</script>
<div class='main_frame' id='no-more-tables'><form id="staff_rate" name="staff_rate" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<?php
	$id = false;
	if(isset($_GET['id']))
		$id = $_GET['id'] > 0 ? $_GET['id'] : false;

	$sql = "SELECT * FROM staff_rate_table WHERE rate_id='$id'";
	$result = mysqli_query($dbc, $sql);
	if($id === false || mysqli_num_rows($result) > 0):
		$staff_sql = "SELECT `contactid`, `last_name`, `first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status` > 0";
		$staff_result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, $staff_sql),MYSQLI_ASSOC));$rates_sql = "SELECT `rate_card_name` FROM `company_rate_card` WHERE `deleted`=0 GROUP BY `rate_card_name` ORDER BY `rate_card_name`";
		$rate_results = mysqli_query($dbc, $rates_sql);
		$row = mysqli_fetch_array($result); ?>
		<h3>Rate Card Info</h3>
		<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Rate Card:</label>
		<div class='col-sm-8'><select name='rate_card' data-placeholder='Select Rate Card' class='chosen-select-deselect form-control'><option></option>
		<?php while($rate_name = mysqli_fetch_array($rate_results)) {
			echo "<option".($rate_name['rate_card_name'] == $row['rate_card_name'] ? ' selected' : '')." value='{$rate_name['rate_card_name']}' title='{$rate_name['rate_card_name']}'>{$rate_name['rate_card_name']}</option>";
		} ?>
		</select></div></div>
		<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Staff Member:</label>
		<div class='col-sm-8'><select name='staff_id[]' multiple data-placeholder='Choose a Staff Member' class='chosen-select-deselect form-control'><option></option><option value="ALL_STAFF">Select All Staff</option>
		<?php $row = mysqli_fetch_array($result);
		foreach($staff_result as $staff_id) {
			echo "<option ".(strpos(','.$row['staff_id'].',', ','.$staff_id.',') !== FALSE ? 'selected' : '')." value='$staff_id'>".get_contact($dbc,$staff_id)."</option>";
		} ?>
		</select></div></div>
		<?php $field_config = get_config($dbc, 'staff_rate_fields');
		if(str_replace(',','',$field_config) == '') {
			$field_config = ",annual,monthly,hourly,";
		}
		if(strpos($field_config, ',category,') !== false) { ?>
			<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Category</label>
			<div class='col-sm-8'>
				<span class="category_select"><select class="chosen-select-deselect" id="category_select">
					<option></option><option value="ADD_NEW">New Category</option>
					<?php $rate_categories = mysqli_query($dbc, "SELECT DISTINCT(`category`) FROM `staff_rate_table` WHERE `deleted`=0 ORDER BY `category`");
					while($category_row = mysqli_fetch_array($rate_categories)) { ?>
						<option <?= ($category_row['category'] == $row['category'] ? 'selected' : '') ?> value="<?= $category_row['category'] ?>"><?= $category_row['category'] ?></option>
					<?php } ?>
					</select></span>
				<span class="category_input" style="display:none;"><input class='form-control col-sm-11' type='text' name='category' value='<?php echo $row['category']; ?>' style="width:calc(100% - 2.5em);">
					<img src="<?= WEBSITE_URL ?>/img/remove.png" style="margin-left: 1em; width:1.5em;" title="Select Existing Category" onclick="$('.category_input').hide();$('.category_select').show();$('#category_select').val($('[name=category]').val()).trigger('change.select2');"></span></div></div>
		<?php }
		if(strpos($field_config, ',start_end_dates,') !== false) { ?>
			<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Start Date</label>
			<div class='col-sm-8'><input class='form-control datepicker' type='text' name='start_date' value='<?php echo $row['start_date']; ?>'></div></div>
			<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>End Date</label>
			<div class='col-sm-8'><input class='form-control datepicker' type='text' name='end_date' value='<?php echo $row['end_date']; ?>'></div></div>
		<?php }
		if(strpos($field_config, ',reminder_alerts,') !== false) { ?>
			<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Alert Date</label>
			<div class='col-sm-8'><input class='form-control datepicker' type='text' name='alert_date' value='<?php echo $row['alert_date']; ?>'></div></div>
			<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Alert Staff</label>
			<div class='col-sm-8'>
				<select name="alert_staff[]" multiple data-placeholder="Select Staff..." class="form-control chosen-select-deselect">
					<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1"),MYSQLI_ASSOC));
					foreach($staff_list as $staffid) {
						echo '<option value="'.$staffid.'" '.(strpos(','.$row['alert_staff'].',',','.$staffid.',') !== FALSE ? 'selected' : '').'>'.get_contact($dbc, $staffid).'</option>';
					} ?>
				</select>
			</div></div>
		<?php }
		if(strpos($field_config, ',daily,') !== false) { ?>
			<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Daily Rate</label>
			<div class='col-sm-8'><input class='form-control' type='number' name='daily' value='<?php echo $row['daily']; ?>' min='0' step='any'></div></div>
		<?php }
		if(strpos($field_config, ',hourly,') !== false) { ?>
			<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Hourly Rate</label>
			<div class='col-sm-8'><input class='form-control' type='number' name='hourly' value='<?php echo $row['hourly']; ?>' min='0' step='any'></div></div>
		<?php }
		if(strpos($field_config, ',cost,') !== false) { ?>
			<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Cost</label>
			<div class='col-sm-8'><input class='form-control' type='number' name='cost' value='<?php echo $row['cost']; ?>' min='0' step='any'></div></div>
		<?php }
		if(strpos($field_config, ',unit_price,') !== false) { ?>
			<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Price</label>
			<div class='col-sm-8'><input class='form-control' type='number' name='unit_price' value='<?php echo $row['cust_price']; ?>' min='0' step='any'></div></div>
		<?php }
		if(strpos($field_config, ',uom,') !== false) { ?>
			<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>UoM</label>
			<div class='col-sm-8'>
				<select name="uom" data-placeholder="Select a UOM..." class="chosen-select-deselect form-control" onchange="if(this.value == 'NEW_UOM') { $(this).closest('div').find('input').removeAttr('disabled').show().focus(); } else { $(this).closest('div').find('input').prop('disabled',true).hide(); }">
					<option></option>
					<option value="NEW_UOM">Add New UOM</option>
					<?php $query = mysqli_query($dbc, "SELECT DISTINCT(`uom`) FROM `company_rate_card` WHERE `deleted` = 0 AND IFNULL(`uom`,'') != '' ORDER BY `uom`");
					while($uom = mysqli_fetch_array($query)) { ?>
						<option value="<?= $uom['uom'] ?>" <?= $uom['uom'] == $row['uom'] ? 'selected' : '' ?>><?= $uom['uom'] ?></option>
					<?php } ?>
				</select>
				<input type="text" name="uom" disabled class="form-control" style="display: none;">
		<?php } ?>
	<?php endif; ?>
	<button type='submit' name='submit' value='<?php echo $id; ?>' class="btn brand-btn btn-lg pull-right">Submit</button>
</form></div>