<?php $warehouse_list = sort_contacts_query($dbc->query("SELECT `contactid`, `name`, `first_name`, `last_name`, `address`, `city`, `postal_code` FROM `contacts` WHERE `category`='Warehouses' AND `deleted`=0 AND `status` > 0")); ?>
<script>
$(document).ready(function() {
	setSave();
});
function setSave() {
	$('.form-group input,.form-group select').off('change',saveWarehouses).change(saveWarehouses);
}
function saveWarehouses() {
	var list = [];
	$('.form-group').each(function() {
		list.push($(this).find('input[name="city"]').val()+'|'+$(this).find('select[name="warehouse"]').val());
	});
	$.post('optimize_ajax.php?action=bb_macro_warehouse_assignments', { value: list });
}
function addRow() {
	var block = $('.form-group').last();
	destroyInputs();
	var clone = block.clone();
	clone.find('input,select').val('');
	block.after(clone);
	initInputs();
	setSave();
}
function remRow(img) {
	if($('.form-group').length == 1) {
		addRow();
	}
	$(img).closest('.form-group').remove();
	saveWarehouses();
}
</script>
<div class="hide-title-mob">
	<div class="col-sm-6">City</div>
	<div class="col-sm-5">Warehouse</div>
</div>
<?php $warehouse_assignments = explode('#*#', get_config($dbc, 'bb_macro_warehouse_assignments'));
foreach($warehouse_assignments as $warehouse_assignment) {
	$warehouse_assignment = explode('|', $warehouse_assignment);
	$city = $warehouse_assignment[0];
	$warehouseid = $warehouse_assignment[1]; ?>
	<div class="form-group">
		<div class="col-sm-6">
			<span class="show-on-mob">City:</span>
			<input type="text" class="form-control" name="city" value="<?= $city ?>">
		</div>
		<div class="col-sm-5">
			<span class="show-on-mob">Warehouse:</span>
			<select class="chosen-select-deselect" name="warehouse" data-placeholder="Select Type..."><option />
				<?php foreach($warehouse_list as $warehouse) { ?>
					<option <?= $warehouse['contactid'] == $warehouseid ? 'selected' : '' ?> value="<?= $warehouse['contactid'] ?>"><?= $warehouse['full_name'] ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="col-sm-1">
			<img class="cursor-hand inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="addRow()">
			<img class="cursor-hand inline-img pull-right" src="../img/remove.png" onclick="remRow(this)">
		</div>
	</div>
<?php }