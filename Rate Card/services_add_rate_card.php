<?php // Edit Category Rate Card
if (isset($_POST['submit'])) {
	require_once('../include.php');
	$id = $_POST['submit'];
	$serviceid = filter_var($_POST['serviceid'],FILTER_SANITIZE_STRING);
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
	$history = 'Service rate card '.($id == '' ? 'Added' : 'Edited').' by '.get_contact($dbc, $_SESSION['contactid']).' on '.date('Y-m-d h:i:s');
	$sql = '';
	if($id == '') {
		$sql = "INSERT INTO `company_rate_card` (`rate_card_name`,`item_id`,`tile_name`,`start_date`,`end_date`,`daily`,`hourly`,`uom`,`cost`,`cust_price`,`history`,`created_by`,`alert_date`,`alert_staff`) VALUES
			('$rate_card','$serviceid','Services','$start_date','$end_date','$daily','$hourly','$uom','$cost','$unit_price','$history','".$_SESSION['contactid']."','$alert_date','$alert_staff')";
	}
	else {
		$sql = "UPDATE `company_rate_card` SET `rate_card_name`='$rate_card',`item_id`=$serviceid,`start_date`='$start_date',`end_date`='$end_date',`cost`='$cost',`cust_price`='$unit_price',`uom`='$uom',`daily`='$daily',`hourly`='$hourly',`history`=IFNULL(CONCAT(`history`,'<br />\n','$history'),'$history'),`alert_date`='$alert_date',`alert_staff`='$alert_staff' WHERE `companyrcid`='$id'";
	}
	if($serviceid > 0 && $cost > 0) {
		$dbc->query("UPDATE `services` SET `cost`='$cost' WHERE `serviceid`='$serviceid'");
	}
	$result = mysqli_query($dbc, $sql);

    if($id == '') {
        $id = $dbc->insert_id;
    }
	echo '<script type="text/javascript"> window.location.replace("?card=services&type=services&t='.$_GET['t'].'"); </script>';
} ?>
<div class='main_frame' id='no-more-tables'><form id="services_rate" name="services_rate" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<script>
	$(document).ready(function() {
		$('select').change(function() {
			if(this.name == 'uom') {
				if(this.value == 'NEW_UOM') {
					$('input[name=uom]').removeAttr('disabled').show().focus();
				} else {
					$('input[name=uom]').prop('disabled',true).hide();
				}
			}
		});
		$('input').change(function() {
			if(this.name == 'cost' || this.name == 'service_rate') {
				var cost = $('[name=cost]').val();
				var rate = $('[name=service_rate]').val();
				if(cost > 0 && rate > 0) {
					$('[name=profit]').val(round2Fixed(rate - cost));
					$('[name=margin]').val(round2Fixed((rate - cost) / cost * 100));
				}
			} else if(this.name == 'profit') {
				var cost = $('[name=cost]').val() * 1;
				if(cost > 0 && this.value > 0) {
					$('[name=service_rate]').val(round2Fixed((this.value * 1) + cost));
					$('[name=margin]').val(round2Fixed(this.value / cost * 100));
				}
			} else if(this.name == 'margin') {
				var cost = $('[name=cost]').val() * 1;
				if(cost > 0 && this.value > 0) {
					var profit = (this.value / 100) * cost;
					$('[name=profit]').val(round2Fixed(profit));
					$('[name=service_rate]').val(round2Fixed(profit + cost));
				}
			}
		});
	});
	</script>
	<?php $id = $_GET['id'];
	$result = mysqli_query($dbc,"SELECT * FROM `company_rate_card` WHERE `companyrcid`='$id'"); ?>
	<h3>Rate Card Information</h3>
	<?php $rates_sql = "SELECT `rate_card_name` FROM `company_rate_card` WHERE `deleted`=0 GROUP BY `rate_card_name` ORDER BY `rate_card_name`";
	$rate_results = mysqli_query($dbc, $rates_sql);
	$row = mysqli_fetch_array($result); ?>
	<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Rate Card:</label>
		<div class='col-sm-8'>
			<select name='rate_card' data-placeholder='Select Rate Card' class='chosen-select-deselect form-control'><option></option>
				<?php while($rate_name = mysqli_fetch_array($rate_results)) {
					echo "<option".($rate_name['rate_card_name'] == $row['rate_card_name'] ? ' selected' : '')." value='{$rate_name['rate_card_name']}' title='{$rate_name['rate_card_name']}'>{$rate_name['rate_card_name']}</option>";
				} ?>
			</select>
		</div>
	</div>
	<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Service:</label>
		<div class='col-sm-8'>
			<select name='serviceid' data-placeholder='Choose a Category' class='chosen-select-deselect form-control'><option></option>
			<?php
			$categories = mysqli_query($dbc, "SELECT serviceid, service_code, service_type, category, heading FROM `services`");
			while($cat = mysqli_fetch_array($categories)) {
				$service_text = '';
				if($cat['service_code'] != '') {
					$service_text .= $cat['service_code'].' : ';
				}
				if($cat['service_type'] != '') {
					$service_text .= $cat['service_type'].' : ';
				}
				if($cat['category'] != '') {
					$service_text .= $cat['category'].' : ';
				}
				if($cat['heading'] != '') {
					$service_text .= $cat['heading'];
				}
				echo "<option".($cat['serviceid'] == $row['item_id'] || (!isset($_GET['id']) && $cat['serviceid'] == $_GET['service'])  ? ' selected' : '')." value='".$cat['serviceid']."'>".$service_text."</option>";
			} ?>
			</select>
		</div>
	</div>
	<?php $field_config = ','.get_config($dbc, 'services_rate_fields').','; ?>
	<div class='form-group clearfix'><label class='col-sm-4 control-label text-right'>Effective Start Date:</label>
	<div class='col-sm-8'><input class='form-control datepicker' type='text' name='start_date' value='<?php echo $row['start_date']; ?>'></div></div>

	<div class='form-group clearfix'><label class='col-sm-4 control-label text-right'>Effective End Date:</label>
	<div class='col-sm-8'><input class='form-control datepicker' type='text' name='end_date' value='<?php echo $row['end_date']; ?>'></div></div>

	<?php if(strpos($field_config, ',reminder_alerts,') !== false) { ?>
		<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Alert Date:</label>
		<div class='col-sm-8'><input class='form-control datepicker' type='text' name='alert_date' value='<?php echo $row['alert_date']; ?>'></div></div>
		<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Alert Staff:</label>
		<div class='col-sm-8'>
			<select name="alert_staff[]" multiple data-placeholder="Select Staff..." class="form-control chosen-select-deselect">
				<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1"),MYSQLI_ASSOC));
				foreach($staff_list as $staffid) {
					echo '<option value="'.$staffid.'" '.(strpos(','.$row['alert_staff'].',',','.$staffid.',') !== FALSE ? 'selected' : '').'>'.get_contact($dbc, $staffid).'</option>';
				} ?>
			</select>
		</div></div>
	<?php } ?>

	<?php if(strpos($field_config, ',cost,') !== false) {
		if(!($row['cost'] > 0) && ($_GET['service'] > 0 || $row['serviceid'] > 0)) {
			$row['cost'] = $dbc->query("SELECT `cost` FROM `services` WHERE `serviceid` IN ('".filter_var($_GET['service'],FILTER_SANITIZE_STRING)."','{$row['serviceid']}')")->fetch_assoc()['cost'];
		} ?>
		<div class='form-group clearfix'><label class='col-sm-4 control-label text-right'>Cost:</label>
		<div class='col-sm-8'><input class='form-control' type='text' name='cost' value='<?php echo $row['cost']; ?>'></div></div>
	<?php } ?>

	<?php if(strpos($field_config, ',margin,') !== false) { ?>
		<div class='form-group clearfix'><label class='col-sm-4 control-label text-right'>Margin %:</label>
		<div class='col-sm-8'><input class='form-control' type='text' name='margin' value='<?php echo $row['margin']; ?>'></div></div>
	<?php } ?>

	<?php if(strpos($field_config, ',profit,') !== false) { ?>
		<div class='form-group clearfix'><label class='col-sm-4 control-label text-right'>Profit $:</label>
		<div class='col-sm-8'><input class='form-control' type='text' name='profit' value='<?php echo $row['profit']; ?>'></div></div>
	<?php } ?>

	<?php if(strpos($field_config, ',uom,') !== false) { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">UoM:</label>
			<div class="col-sm-8">
				<select name="uom" data-placeholder="Select Unit of Measure..." class="chosen-select-deselect form-control">
					<option></option>
					<option value="NEW_UOM">Add New UoM</option>
					<?php $uom_list = mysqli_query($dbc, "SELECT `uom` FROM (SELECT `uom` FROM `company_rate_card` WHERE `deleted` = 0 AND IFNULL(`uom`,'') != '' UNION SELECT 'Hourly' `uom` UNION SELECT 'Daily' `uom`) `uoms` GROUP BY `uom` ORDER BY `uom`");
					while($uom = mysqli_fetch_array($uom_list)) { ?>
						<option value="<?= $uom['uom'] ?>" <?= $uom['uom'] == $row['uom'] ? 'selected' : '' ?>><?= $uom['uom'] ?></option>
					<?php } ?>
				</select>
				<input type="text" name="uom" disabled class="form-control" style="display: none;">
			</div>
		</div>
	<?php } ?>

	<div class='form-group clearfix'><label class='col-sm-4 control-label text-right'>Rate:</label>
	<div class='col-sm-8'><input class='form-control' type='text' name='unit_price' value='<?php echo $row['cust_price']; ?>'></div></div>

	<div class='form-group clearfix'><label class='col-sm-4 control-label text-right'>Admin Fee:</label>
	<div class='col-sm-8'><input class='form-control' type='text' name='admin_fee' value='<?php echo $row['admin_fee']; ?>'></div></div>

	<div class='form-group clearfix'><label class='col-sm-4 control-label text-right'>Editable in Invoice</label>
	<div class='col-sm-8'><label class="form-checkbox"><input type='checkbox' name='editable' value='1' <?= $row['editable'] == 1 ? 'checked' : '' ?>> Allow</label></div></div>
	<button type='submit' name='submit' value='<?php echo $id; ?>' class="btn brand-btn btn-lg pull-right">Submit</button>
</div>