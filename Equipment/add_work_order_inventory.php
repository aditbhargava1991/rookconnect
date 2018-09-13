<script>
$(document).on('change', 'select[name="in_category[]"]', function() { inventory_cat(this); });
$(document).on('change', 'select[name="in_part_no[]"]', function() { inventory_item(this); });
function add_inventory() {
	var line = $('.inventory_line').last();
	var inv = line.clone();
	inv.find('.form-control').val('');
	resetChosen(inv.find("select[class^=chosen]"));
	inv.find('input[type=checkbox]').removeAttr('checked');
	line.after(inv);
}
function rem_inventory(btn) {
	var line = $(btn).closest('.inventory_line');
	var id = line.find('[name="in_lineid[]"]').val();
	if(id > 0) {
		$.ajax({
			url: 'equipment_ajax.php?fill=wo_remove_inventory&line='+id,
			method: 'GET'
		});
	}
	line.remove();
}
function inventory_cat(select) {
	var part = $(select).closest('.inventory_line').find('[name="in_part_no[]"]');
	part.find('option').hide();
	part.find('option [data-category="'+select.value+'"]').show().trigger('change.select2');
}
function inventory_item(select) {
	var line = $(select).closest('.inventory_line');
	var option = $(select).find('option:selected');
	if(select.value != '') {
		line.find('[name="in_category[]"]').val(option.data('category')).trigger('change.select2');
		line.find('[name="in_unit_cost[]"]').val(option.data('cost'));
		var max = option.data('qty');
		if(max < 0) {
			max = 0;
		}
		max += parseFloat(line.find('[name="in_qty[]"]').data('used'));
		line.find('[name="in_qty[]"]').attr('max',max);
	} else {
		line.find('[name="in_unit_cost[]"]').val('');
		line.find('[name="in_unit_total[]"]').val('');
	}
}
function inventory_total(text) {
	var line = $(text).closest('.inventory_line');
	var qty = line.find('[name="in_qty[]"]').val();
	var cost = line.find('[name="in_unit_cost[]"]').val();
	line.find('[name="in_unit_total[]"]').val(qty * cost);
}
</script>
<div class="form-group clearfix hide-titles-mob">
	<label class="col-sm-2  text-center">Tab</label>
	<label class="col-sm-3 text-center">Part #</label>
	<label class="col-sm-2 text-center">Qty</label>
	<label class="col-sm-2 text-center">Cost</label>
	<label class="col-sm-2 text-center">Total</label>
</div>
<?php $inventory_used = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_inventory` WHERE `workorderid`='$workorderid'"),MYSQLI_ASSOC);
$inventory_used[] = ['NEW'];
$cost_field = get_config($dbc,'inventory_cost');
foreach($inventory_used as $line) {
	$inventory = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `inventory` WHERE `inventoryid`='".$line['inventoryid']."'")); ?>
	<div class="form-group clearfix inventory_line" width="100%">
		<input type="hidden" name="in_lineid[]" value="<?= $line['lineid'] ?>">
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Tab:</label>
		<div class="col-sm-2 expand-mobile type">
			<select data-placeholder="Select a Tab..." name="in_category[]" class="chosen-select-deselect form-control category">
				<option value="<?= $inventory['category']; ?>"><?= $inventory['category']; ?></option><?php
				$query = mysqli_query ( $dbc, "SELECT DISTINCT `category` FROM `inventory` ORDER BY `category`" );
				while ( $row = mysqli_fetch_assoc ( $query ) ) { ?>
					<option <?= $row['category'] == $inventory['category'] ? 'selected' : '' ?> value="<?= $row['category']; ?>"><?= $row['category']; ?></option><?php
				} ?>
			</select>
		</div>
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Part #:</label>
		<div class="expand-mobile col-sm-3">
			<select data-placeholder="Select a Part#..." name="in_part_no[]" class="chosen-select-deselect form-control part">
				<option value="<?= $inventoryid[$i]; ?>"><?= $part_no[$i]; ?></option><?php
				$query = mysqli_query ( $dbc, "SELECT `inventoryid`, `part_no`, `name`, `category`, `quantity`, `$cost_field` FROM `inventory` WHERE `deleted`=0 ORDER BY `part_no`" );
				while ( $row = mysqli_fetch_array ( $query ) ) { ?>
					<option <?= $row['inventoryid'] == $line['inventoryid'] ? 'selected' : '' ?> value="<?= $row['inventoryid']; ?>" data-category="<?= $row['category'] ?>" data-qty="<?= $row['quantity'] ?>" data-cost="<?= $row[$cost_field] ?>"><?= $row['part_no'].' '.$row['name']; ?></option><?php
				} ?>
			</select>
		</div>
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
		<div class="col-sm-2 expand-mobile">
			<input name="in_qty[]" value="<?= $line['qty']; ?>" onchange="inventory_total(this);" type="number" class="expand-mobile form-control" min="0" data-used="<?= $line['qty'] ?>" max="<?= $inventory['quantity']+$line['qty'] ?>" step="any" />
		</div>
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Cost:</label>
		<div class="col-sm-2 expand-mobile">
			<input name="in_unit_cost[]" readonly value="<?= $line['unit_cost']; ?>" type="text" class="expand-mobile form-control" />
		</div>
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Total:</label>
		<div class="col-sm-2 expand-mobile">
			<input name="in_unit_total[]" readonly value="<?= $line['unit_total']; ?>" type="text" class="expand-mobile form-control" />
		</div>

		<div class="col-sm-1 m-top-mbl">
			<button type="button" onclick="rem_inventory(this); return false;" class="btn brand-btn form-control">Delete</button>
		</div>
	</div>
<?php } ?>
<button type="button" class="btn brand-btn pull-right" onclick="add_inventory(); return false;">Add</button>