<?php // Edit Position Rate Card
if (isset($_POST['submit'])) {
	require_once('../include.php');
	$id = $_POST['submit'];
	$position_id = filter_var($_POST['position_id'],FILTER_SANITIZE_STRING);
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
	$history = 'Position rate card '.($id == '' ? 'Added' : 'Edited').' by '.get_contact($dbc, $_SESSION['contactid']).' on '.date('Y-m-d h:i:s');
	$sql = '';
	if($id == '') {
		$sql = "INSERT INTO `company_rate_card` (`rate_card_name`,`item_id`,`tile_name`,`start_date`,`end_date`,`daily`,`hourly`,`uom`,`cost`,`cust_price`,`history`,`created_by`,`alert_date`,`alert_staff`) VALUES
			('$rate_card','$position_id','Position','$start_date','$end_date','$daily','$hourly','$uom','$cost','$unit_price','$history','".$_SESSION['contactid']."','$alert_date','$alert_staff')";
		$id = mysqli_insert_id($dbc);
	}
	else {
		$sql = "UPDATE `company_rate_card` SET `rate_card_name`='$rate_card',`item_id`=$position_id,`start_date`='$start_date',`end_date`='$end_date',`cost`='$cost',`cust_price`='$unit_price',`uom`='$uom',`daily`='$daily',`hourly`='$hourly',`history`=IFNULL(CONCAT(`history`,'<br />\n','$history'),'$history'),`alert_date`='$alert_date',`alert_staff`='$alert_staff' WHERE `rate_id`='$id'";
	}
	$result = mysqli_query($dbc, $sql);

	echo "<!--".mysqli_error($dbc)."-->";
    echo '<script type="text/javascript"> window.location.replace("?card=position&type=position"); </script>';
} ?>
<div class='main_frame' id='no-more-tables'><form id="position_rate" name="position_rate" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<?php
	$id = false;
	if($id = $_GET['id']) {
		$sql = "SELECT * FROM `company_rate_card` WHERE `companyrcid`='$id'";
		$result = mysqli_query($dbc, $sql);
	}
	$position_sql = "SELECT `name`, `description`, `position_id` id FROM `positions` ORDER BY `name`";
	$position_results = mysqli_query($dbc, $position_sql);
	$rates_sql = "SELECT `rate_card_name` FROM `company_rate_card` WHERE `deleted`=0 GROUP BY `rate_card_name` ORDER BY `rate_card_name`";
	$rate_results = mysqli_query($dbc, $rates_sql);
	$row = mysqli_fetch_array($result); ?>
	<h3>Rate Card Info</h3>
	<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Rate Card:</label>
	<div class='col-sm-8'><select name='rate_card' data-placeholder='Select Rate Card' class='chosen-select-deselect form-control'><option></option>
	<?php while($rate_name = mysqli_fetch_array($rate_results)) {
		echo "<option".($rate_name['rate_card_name'] == $row['rate_card_name'] ? ' selected' : '')." value='{$rate_name['rate_card_name']}' title='{$rate_name['rate_card_name']}'>{$rate_name['rate_card_name']}</option>";
	} ?>
	</select></div></div>
	<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Position:</label>
	<div class='col-sm-8'><select name='position_id' data-placeholder='Select Position' class='chosen-select-deselect form-control'><option></option>
	<?php while($pos_row = mysqli_fetch_array($position_results)) {
		echo "<option".($pos_row['id'] == $row['item_id'] || (!($row['item_id'] > 0) && $row['description'] == $pos_row['name']) ? ' selected' : '')." value='{$pos_row['id']}' title='{$pos_row['description']}'>{$pos_row['name']}</option>";
	} ?>
	</select></div></div>
	<?php $field_config = get_config($dbc, 'position_rate_fields');
	if(str_replace(',','',$field_config) == '') {
		$field_config = ",cost,uom,unit_price,";
	}
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
	<button type='submit' name='submit' value='<?php echo $id; ?>' class="btn brand-btn btn-lg pull-right">Submit</button>
</div>