<?php // Edit Labour Rate Card
if (isset($_POST['submit'])) {
	$id = $_POST['submit'];
	$labourid = filter_var($_POST['labourid'],FILTER_SANITIZE_STRING);
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
	$history = 'Labour rate card '.($id == '' ? 'Added' : 'Edited').' by '.get_contact($dbc, $_SESSION['contactid']).' on '.date('Y-m-d h:i:s');
	$sql = '';
	if($id == '') {
		$sql = "INSERT INTO `company_rate_card` (`rate_card_name`,`item_id`,`tile_name`,`start_date`,`end_date`,`daily`,`hourly`,`uom`,`cost`,`cust_price`,`history`,`created_by`,`alert_date`,`alert_staff`) VALUES
			('$rate_card','$labourid','Labour','$start_date','$end_date','$daily','$hourly','$uom','$cost','$unit_price','$history','".$_SESSION['contactid']."','$alert_date','$alert_staff')";
		$id = mysqli_insert_id($dbc);
	}
	else {
		$sql = "UPDATE `company_rate_card` SET `rate_card_name`='$rate_card',`description`=$category,`start_date`='$start_date',`end_date`='$end_date',`cost`='$cost',`cust_price`='$unit_price',`uom`='$uom',`daily`='$daily',`hourly`='$hourly',`history`=IFNULL(CONCAT(`history`,'<br />\n','$history'),'$history'),`alert_date`='$alert_date',`alert_staff`='$alert_staff' WHERE `rate_id`='$id'";
	}
	$result = mysqli_query($dbc, $sql);

	$result = mysqli_query($dbc, $sql);
	echo '<script type="text/javascript"> window.location.replace("?card=labour&type=labour"); </script>';
} ?>
<script type="text/javascript">
$(document).ready(function() {
	$('[name="labour_type"],[name="category"],[name="labourid"]').each(function() {
		if($(this).val() != undefined && $(this).val() != '') {
			filterLabour(this);
		}
	});
});
$(document).on('change', 'select[name="uom"]', function() { addNewUom(this); });
$(document).on('change', 'select[name="labour_type"],select[name="category"],select[name="labourid"]', function() { filterLabour(this); });
$(document).on('change', '.price_controls', function() { calculatePrices(this); });
function addNewUom(sel) {
	if($(sel).val() == 'NEW_UOM') {
		$('[name="uom_new"]').show();
	} else {
		$('[name="uom_new"]').hide();
	}
}
function filterLabour(sel) {
	var labour_type = $('[name="labour_type"]');
	var labour_type_filter = '';
	if($(labour_type).val() != undefined && $(labour_type).val() != '') {
		labour_type_filter = '[data-labour-type="'+$(labour_type).val()+'"]';
	}
	var category = $('[name="category"]');
	var category_filter = '';
	if($(category).val() != undefined && $(category).val() != '') {
		category_filter = '[data-category="'+$(category).val()+'"]';
	}
	var labourid = $('[name="labourid"]');
	if(sel.name == 'labour_type') {
		$('[name="category"] option').hide();
		$('[name="category"] option'+labour_type_filter+category_filter).show();
		$('[name="category"]').trigger('change.select2');

		$('[name="labourid"] option').hide();
		$('[name="labourid"] option'+labour_type_filter+category_filter).show();
		$('[name="labourid"]').trigger('change.select2');
	} else if(sel.name == 'category') {
		$('[name="labour_type"]').val($(category).find('option:selected').data('labour-type'));
		$('[name="labour_type"]').trigger('change.select2');

		$('[name="labourid"] option').hide();
		$('[name="labourid"] option'+labour_type_filter+category_filter).show();
		$('[name="labourid"]').trigger('change.select2');
	} else if(sel.name == 'labourid') {
		$('[name="labour_type"]').val($(labourid).find('option:selected').data('labour-type'));
		$('[name="labour_type"]').trigger('change.select2');

		$('[name="category"]').val($(labourid).find('option:selected').data('category'));
		$('[name="category"]').trigger('change.select2');
	}
}
function calculatePrices(input) {
	var cost = $('[name="cost"]').val();
	var profit_percent = $('[name="profit_percent"]').val() > 0 ? $('[name="profit_percent"]').val() : 0;
	var profit_dollar = $('[name="profit_dollar"]').val() > 0 ? $('[name="profit_dollar"]').val() : 0;
	var price = $('[name="price"]').val() > 0 ? $('[name="price"]').val() : 0;
	if(cost != undefined && cost > 0) {
		if(input.name == 'cost') {
			if(profit_percent > 0 && !(profit_dollar > 0)) {
				profit_dollar = parseFloat(cost) * parseFloat(profit_percent) / 100;
				$('[name="profit_dollar"]').val(parseFloat(profit_dollar).toFixed(2));
			}
			price = parseFloat(cost) + parseFloat(profit_dollar);
			$('[name="price"]').val(parseFloat(price).toFixed(2));
		} else if(input.name == 'profit_percent') {
			profit_dollar = parseFloat(cost) * parseFloat(profit_percent) / 100;
			$('[name="profit_dollar"]').val(parseFloat(profit_dollar).toFixed(2));

			price = parseFloat(cost) + parseFloat(profit_dollar);
			$('[name="price"]').val(parseFloat(price).toFixed(2));
		} else if(input.name == 'profit_dollar') {
			profit_percent = parseFloat(profit_dollar) / parseFloat(cost) * 100;
			$('[name="profit_percent"]').val(parseFloat(profit_percent).toFixed(2));

			price = parseFloat(cost) + parseFloat(profit_dollar);
			$('[name="price"]').val(parseFloat(price).toFixed(2));
		} else if(input.name == 'price') {
			if(profit_percent > 0 && !(profit_dollar > 0)) {
				profit_dollar = parseFloat(price) * parseFloat(profit_percent) / 100;
			}
			cost = parseFloat(price) - parseFloat(profit_dollar);
			$('[name="cost"]').val(parseFloat(cost).toFixed(2));
		}
	}
}
</script>

<div class='main_frame' id='no-more-tables'><form id="labour_rate" name="labour_rate" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <?php
    $id = $_GET['id'];
	if($id > 0) {
		$ratecard = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `company_rate_card` WHERE `companyrcid`='$id'"));
	} else {
		$ratecard = ['item_id'=>filter_var($_GET['labourid'],FILTER_SANITIZE_STRING)];
	}
    $labour = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `labour` WHERE `labourid`='{$ratecard['item_id']}'"));

    $field_config = ',labour_type,heading,'.get_config($dbc, 'labour_rate_fields').',';
	$rates_sql = "SELECT `rate_card_name` FROM `company_rate_card` WHERE `deleted`=0 GROUP BY `rate_card_name` ORDER BY `rate_card_name`";
	$rate_results = mysqli_query($dbc, $rates_sql); ?>
	<h3>Rate Card Info</h3>
	<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Rate Card:</label>
	<div class='col-sm-8'><select name='rate_card' data-placeholder='Select Rate Card' class='chosen-select-deselect form-control'><option></option>
	<?php while($rate_name = mysqli_fetch_array($rate_results)) {
		echo "<option".($rate_name['rate_card_name'] == $ratecard['rate_card_name'] ? ' selected' : '')." value='{$rate_name['rate_card_name']}' title='{$rate_name['rate_card_name']}'>{$rate_name['rate_card_name']}</option>";
	} ?>
	</select></div></div>

	<?php if(strpos($field_config, ',labour_type,') !== FALSE) { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Labour Type:</label>
			<div class="col-sm-8">
				<select name="labour_type" data-placeholder="Select a Labour Type..." class="chosen-select-deselect form-control">
					<option></option>
					<?php $query = mysqli_query($dbc, "SELECT DISTINCT(`labour_type`) FROM `labour` WHERE `deleted` = 0 ORDER BY `labour_type`");
					while($row = mysqli_fetch_array($query)) { ?>
						<option value="<?= $row['labour_type'] ?>" <?= $labour['labour_type'] == $row['labour_type'] ? 'selected' : '' ?>><?= $row['labour_type'] ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	<?php } ?>

	<?php if(strpos($field_config, ',category,') !== FALSE) { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Category:</label>
			<div class="col-sm-8">
				<select name="category" data-placeholder="Select a Category..." class="chosen-select-deselect form-control">
					<option></option>
					<?php $query = mysqli_query($dbc, "SELECT DISTINCT(`category`) FROM `labour` WHERE `deleted` = 0 ORDER BY `category`");
					while($row = mysqli_fetch_array($query)) { ?>
						<option data-labour-type="<?= $row['labour_type'] ?>" value="<?= $row['category'] ?>" <?= $labour['category'] == $row['category'] ? 'selected' : '' ?>><?= $row['category'] ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	<?php } ?>

	<?php if(strpos($field_config, ',heading,') !== FALSE) { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Heading:</label>
			<div class="col-sm-8">
				<select name="labourid" data-placeholder="Select a Heading..." class="chosen-select-deselect form-control">
					<option></option>
					<?php $query = mysqli_query($dbc, "SELECT * FROM `labour` WHERE `deleted` = 0 ORDER BY `heading`");
					while($row = mysqli_fetch_array($query)) { ?>
						<option data-labour-type="<?= $row['labour_type'] ?>" data-category="<?= $row['category'] ?>" value="<?= $row['labourid'] ?>" <?= $labour['labourid'] == $row['labourid'] ? 'selected' : '' ?>><?= $row['heading'] ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	<?php }
	if(strpos($field_config, ',start_end_dates,') !== false) { ?>
		<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Start Date</label>
		<div class='col-sm-8'><input class='form-control datepicker' type='text' name='start_date' value='<?php echo $ratecard['start_date']; ?>'></div></div>
		<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>End Date</label>
		<div class='col-sm-8'><input class='form-control datepicker' type='text' name='end_date' value='<?php echo $ratecard['end_date']; ?>'></div></div>
	<?php }
	if(strpos($field_config, ',reminder_alerts,') !== false) { ?>
		<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Alert Date</label>
		<div class='col-sm-8'><input class='form-control datepicker' type='text' name='alert_date' value='<?php echo $ratecard['alert_date']; ?>'></div></div>
		<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Alert Staff</label>
		<div class='col-sm-8'>
			<select name="alert_staff[]" multiple data-placeholder="Select Staff..." class="form-control chosen-select-deselect">
				<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1"),MYSQLI_ASSOC));
				foreach($staff_list as $staffid) {
					echo '<option value="'.$staffid.'" '.(strpos(','.$ratecard['alert_staff'].',',','.$staffid.',') !== FALSE ? 'selected' : '').'>'.get_contact($dbc, $staffid).'</option>';
				} ?>
			</select>
		</div></div>
	<?php }
	if(strpos($field_config, ',daily,') !== false) { ?>
		<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Daily Rate</label>
		<div class='col-sm-8'><input class='form-control' type='number' name='daily' value='<?php echo $ratecard['daily']; ?>' min='0' step='any'></div></div>
	<?php }
	if(strpos($field_config, ',hourly,') !== false) { ?>
		<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Hourly Rate</label>
		<div class='col-sm-8'><input class='form-control' type='number' name='hourly' value='<?php echo $ratecard['hourly']; ?>' min='0' step='any'></div></div>
	<?php }
	if(strpos($field_config, ',cost,') !== false) { ?>
		<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Cost</label>
		<div class='col-sm-8'><input class='form-control' type='number' name='cost' value='<?php echo $ratecard['cost']; ?>' min='0' step='any'></div></div>
	<?php }
	if(strpos($field_config, ',unit_price,') !== false) { ?>
		<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Price</label>
		<div class='col-sm-8'><input class='form-control' type='number' name='unit_price' value='<?php echo $ratecard['cust_price']; ?>' min='0' step='any'></div></div>
	<?php }
	if(strpos($field_config, ',uom,') !== false) { ?>
		<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>UoM</label>
		<div class='col-sm-8'>
			<select name="uom" data-placeholder="Select a UOM..." class="chosen-select-deselect form-control" onchange="if(this.value == 'NEW_UOM') { $(this).closest('div').find('input').removeAttr('disabled').show().focus(); } else { $(this).closest('div').find('input').prop('disabled',true).hide(); }">
				<option></option>
				<option value="NEW_UOM">Add New UOM</option>
				<?php $query = mysqli_query($dbc, "SELECT DISTINCT(`uom`) FROM `company_rate_card` WHERE `deleted` = 0 AND IFNULL(`uom`,'') != '' ORDER BY `uom`");
				while($uom = mysqli_fetch_array($query)) { ?>
					<option value="<?= $uom['uom'] ?>" <?= $uom['uom'] == $ratecard['uom'] ? 'selected' : '' ?>><?= $uom['uom'] ?></option>
				<?php } ?>
			</select>
			<input type="text" name="uom" disabled class="form-control" style="display: none;">
	<?php } ?>
	<button type='submit' name='submit' value='<?php echo $id; ?>' class="btn brand-btn btn-lg pull-right">Submit</button>
</div>