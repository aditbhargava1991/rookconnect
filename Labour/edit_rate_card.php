<?php include_once('../include.php');
checkAuthorised('labour');
if(!empty($_POST['submit_rate_card'])) {
	$labourid = $_POST['submit_rate_card'];
	include('../Labour/save_rate_card.php');
	echo '<script>parent.window.location.reload();</script>';
}
$field_config = ',labour_type,heading,start_date,end_date,price,'.get_config($dbc, 'labour_rate_fields').',';
$view_access = tile_visible($dbc, 'rate_card');
$edit_access = vuaed_visible_function($dbc, 'rate_card');
$subtab_access = check_subtab_persmission($dbc, 'rate_card', ROLE, 'labour');

$labourid = $_GET['edit'];
$labour_heading = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `labour` WHERE `labourid` = '$labourid'"))['heading'];
?>
<script type="text/javascript">
$(document).on('change', 'select[name="uom[]"]', function() { addNewUom(this); });
$(document).on('change', '.price_controls', function() { calculatePrices(this); });
function addNewUom(sel) {
	if($(sel).val() == 'NEW_UOM') {
		$(sel).closest('tr').find('[name="uom_new[]"]').show();
	} else {
		$(sel).closest('tr').find('[name="uom_new[]"]').hide();
	}
}
function calculatePrices(input) {
	var block = $(input).closest('tr');
	var cost = $(block).find('[name="cost[]"]').val();
	var profit_percent = $(block).find('[name="profit_percent[]"]').val() > 0 ? $(block).find('[name="profit_percent[]"]').val() : 0;
	var profit_dollar = $(block).find('[name="profit_dollar[]"]').val() > 0 ? $(block).find('[name="profit_dollar[]"]').val() : 0;
	var price = $(block).find('[name="price[]"]').val() > 0 ? $(block).find('[name="price[]"]').val() : 0;
	if(cost != undefined && cost > 0) {
		if(input.name == 'cost[]') {
			if(profit_percent > 0 && !(profit_dollar > 0)) {
				profit_dollar = parseFloat(cost) * parseFloat(profit_percent) / 100;
				$(block).find('[name="profit_dollar[]"]').val(parseFloat(profit_dollar).toFixed(2));
			}
			price = parseFloat(cost) + parseFloat(profit_dollar);
			$(block).find('[name="price[]"]').val(parseFloat(price).toFixed(2));
		} else if(input.name == 'profit_percent[]') {
			profit_dollar = parseFloat(cost) * parseFloat(profit_percent) / 100;
			$(block).find('[name="profit_dollar[]"]').val(parseFloat(profit_dollar).toFixed(2));

			price = parseFloat(cost) + parseFloat(profit_dollar);
			$(block).find('[name="price[]"]').val(parseFloat(price).toFixed(2));
		} else if(input.name == 'profit_dollar[]') {
			profit_percent = parseFloat(profit_dollar) / parseFloat(cost) * 100;
			$(block).find('[name="profit_percent[]"]').val(parseFloat(profit_percent).toFixed(2));

			price = parseFloat(cost) + parseFloat(profit_dollar);
			$(block).find('[name="price[]"]').val(parseFloat(price).toFixed(2));
		} else if(input.name == 'price[]') {
			if(profit_percent > 0 && !(profit_dollar > 0)) {
				profit_dollar = parseFloat(price) * parseFloat(profit_percent) / 100;
			}
			cost = parseFloat(price) - parseFloat(profit_dollar);
			$(block).find('[name="cost[]"]').val(parseFloat(cost).toFixed(2));
		}
	}
}
function addRateCard() {
	var cur_row_i = parseInt($('[name="cur_row_i"]').val());

	destroyInputs('.rate_card_table');
	var block = $('.rate_card_table .rate_card_row').last();
	var clone = $(block).clone();
	clone.find('input,select').val('');
	clone.find('[name^=alert_staff]').prop('name', 'alert_staff_'+cur_row_i+'[]');
	clone.find('[name="ratecard_row_i[]"]').val(cur_row_i);
	block.after(clone);
	initInputs('.rate_card_table');

	$('[name="cur_row_i"]').val((cur_row_i+1));
}
function removeRateCard(img) {
	if($('.rate_card_table .rate_card_row').length <= 1) {
		addRateCard();
	}
	$(img).closest('tr').remove();
}
</script>
<?php if($_GET['from_type'] == 'dashboard') { ?>
	<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
<?php } ?>
	<div class="rate_card_div" <?= $view_access > 0 && $subtab_access ? '' : 'style="display: none;"' ?>>
		<h3>Rate Card<?= !empty($labour_heading) ? ' - '.$labour_heading : '' ?></h3>
		<table id="no-more-tables" class="table table-bordered rate_card_table">
			<tr class="hidden-xs hidden-sm">
				<?php if(strpos($field_config, ',start_date,') !== FALSE) { ?>
					<th>Start Date</th>
				<?php } ?>
				<?php if(strpos($field_config, ',end_date,') !== FALSE) { ?>
					<th>End Date</th>
				<?php } ?>
				<?php if(strpos($field_config, ',reminder_alerts,') !== FALSE) { ?>
					<th>Alert Date</th>
					<th>Alert Staff</th>
				<?php } ?>
				<?php if(strpos($field_config, ',uom,') !== FALSE) { ?>
					<th>UOM</th>
				<?php } ?>
				<?php if(strpos($field_config, ',cost,') !== FALSE) { ?>
					<th>Cost</th>
				<?php } ?>
				<?php if(strpos($field_config, ',profit_percent,') !== FALSE) { ?>
					<th>Profit %</th>
				<?php } ?>
				<?php if(strpos($field_config, ',profit_dollar,') !== FALSE) { ?>
					<th>Profit $</th>
				<?php } ?>
				<?php if(strpos($field_config, ',price,') !== FALSE) { ?>
					<th>Price</th>
				<?php } ?>
				<?php if($edit_access > 0 && $subtab_access) { ?>
					<th>Function</th>
				<?php } ?>
			</tr>
			<?php
			$query = mysqli_query($dbc, "SELECT * FROM `tile_rate_card` WHERE `tile_name` = 'labour' AND `src_id` = '$labourid' AND `deleted` = 0");
			$row = mysqli_fetch_array($query);
			$row_i = 0;
			do { ?>
				<tr class="rate_card_row">
					<input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?> type="hidden" name="ratecardid[]" value="<?= $row['ratecardid'] ?>">
					<input type="hidden" name="ratecard_row_i[]" value="<?= $row_i ?>">
					<?php if(strpos($field_config, ',start_date,') !== FALSE) { ?>
						<td data-title="Start Date" <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?>>
							<input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?> type="text" name="start_date[]" class="form-control datepicker" value="<?= $row['start_date'] ?>">
						</td>
					<?php } ?>
					<?php if(strpos($field_config, ',end_date,') !== FALSE) { ?>
						<td data-title="End Date" <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?>>
							<input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?> type="text" name="end_date[]" class="form-control datepicker" value="<?= $row['end_date'] ?>">
						</td>
					<?php } ?>
					<?php if(strpos($field_config, ',reminder_alerts,') !== false) { ?>
						<td data-title="Alert Date" <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?>>
							<input class="form-control datepicker" type="text" name="alert_date[]" value="<?= $row['alert_date'] ?>">
						</td>
						<td data-title="Alert Staff" <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?>>
							<select name="alert_staff_<?= $row_i ?>[]" multiple data-placeholder="Select Staff..." class="form-control chosen-select-deselect">
								<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1"),MYSQLI_ASSOC));
								foreach($staff_list as $staffid) {
									echo '<option value="'.$staffid.'" '.(strpos(','.$row['alert_staff'].',',','.$staffid.',') !== FALSE ? 'selected' : '').'>'.get_contact($dbc, $staffid).'</option>';
								} ?>
							</select>
						</td>
					<?php } ?>
					<?php if(strpos($field_config, ',uom,') !== FALSE) { ?>
						<td data-title="UOM" <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?>>
							<select <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?> name="uom[]" data-placeholder="Select a UOM..." class="chosen-select-deselect form-control">
								<option></option>
								<option value="NEW_UOM">Add New UOM</option>
								<?php $query2 = mysqli_query($dbc, "SELECT DISTINCT(`uom`) FROM `tile_rate_card` WHERE `deleted` = 0 AND `tile_name` = 'labour' AND IFNULL(`uom`,'') != '' ORDER BY `uom`");
								while($row2 = mysqli_fetch_array($query2)) { ?>
									<option value="<?= $row2['uom'] ?>" <?= $row['uom'] == $row2['uom'] ? 'selected' : '' ?>><?= $row2['uom'] ?></option>
								<?php } ?>
							</select>
							<input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?> type="text" name="uom_new[]" class="form-control" style="display: none;">
						</td>
					<?php } ?>
					<?php if(strpos($field_config, ',cost,') !== FALSE) { ?>
						<td data-title="Cost" <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?>>
							<input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?> type="number" name="cost[]" class="form-control price_controls" value="<?= $row['cost'] ?>" min="0.00" step="0.01">
						</td>
					<?php } ?>
					<?php if(strpos($field_config, ',profit_percent,') !== FALSE) { ?>
						<td data-title="Profit %" <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?>>
							<input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?> type="number" name="profit_percent[]" class="form-control price_controls" value="<?= $row['profit_percent'] ?>" min="0.00" step="0.01">
						</td>
					<?php } ?>
					<?php if(strpos($field_config, ',profit_dollar,') !== FALSE) { ?>
						<td data-title="Profit $" <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?>>
							<input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?> type="number" name="profit_dollar[]" class="form-control price_controls" value="<?= $row['profit_dollar'] ?>" min="0.00" step="0.01">
						</td>
					<?php } ?>
					<?php if(strpos($field_config, ',price,') !== FALSE) { ?>
						<td data-title="Price" <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?>>
							<input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?> type="number" name="price[]" class="form-control price_controls" value="<?= $row['price'] ?>" min="0.00" step="0.01">
						</td>
					<?php } ?>
					<?php if($edit_access > 0 && $subtab_access) { ?>
						<td data-title="Function">
			                <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addRateCard();">
			                <img src="../img/remove.png" class="inline-img pull-right" onclick="removeRateCard(this);">
						</td>
					<?php } ?>
				</tr>
				<?php $row_i++;
			} while($row = mysqli_fetch_array($query)); ?>
		</table>

		<?php if($_GET['from_type'] == 'dashboard' && $edit_access > 0 && $subtab_access) { ?>
			<div class="form-group pull-right">
				<a href="?" class="btn brand-btn">Cancel</a>
				<button type="submit" name="submit_rate_card" value="<?= $_GET['edit'] ?>" class="btn brand-btn">Submit</button>
			</div>
		<?php } ?>
	</div>
<?php if($_GET['from_type'] == 'dashboard') { ?>
	</form>
<?php } ?>