<script>
function add_po() {
	var line = $('.po_line').last();
	var inv = line.clone();
	inv.find('.form-control').val('');
	line.after(inv);
}
function rem_po(btn) {
	var line = $(btn).closest('.po_line');
	var id = line.find('[name="poid[]"]').val();
	if(id > 0) {
		$.ajax({
			url: 'equipment_ajax.php?fill=wo_remove_po&poid='+id,
			method: 'GET'
		});
	}
	line.remove();
}
function calc_po(line) {
	line = $(line).closest('.po_line');
	var total = (line.find('[name="po_qty[]"]').val() * line.find('[name="po_unit_price[]"]').val()) + 1 * line.find('[name="po_unit_tax[]"]').val();
	line.find('[name="po_unit_total[]"]').val(total.toFixed(2));
	
	tax = 0;
	$('[name="po_unit_tax[]"]').each(function() { tax += parseFloat($(this).val()) || 0; });
	total = 0;
	$('[name="po_unit_total[]"]').each(function() { total += parseFloat($(this).val()) || 0; });
	
	$('.po-tax-total').html('$'+tax.toFixed(2));
	$('.po-total').html('$'+total.toFixed(2));
}
</script>
<div class="form-group clearfix hide-titles-mob">
	<label class="col-sm-3  text-center">Detail</label>
	<label class="col-sm-2 text-center">Receipt</label>
	<label class="col-sm-1 text-center">Qty</label>
	<label class="col-sm-1 text-center">UoM</label>
	<label class="col-sm-1 text-center">Price</label>
	<label class="col-sm-1 text-center">Tax</label>
	<label class="col-sm-2 text-center">Total</label>
</div>
<?php $wo_pos = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_purchase_order_items` WHERE `workorderid`='$workorderid'"),MYSQLI_ASSOC);
$wo_pos[] = ['NEW'];
$po_tax = 0;
$po_total = 0;
foreach($wo_pos as $line) {
	$po_tax += $line['unit_tax'];
	$po_total += $line['unit_total']; ?>
	<div class="form-group po_line">
		<input type="hidden" name="poid[]" value="<?= $line['poid'] ?>">
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Detail:</label>
		<div class="col-sm-3 expand-mobile">
			<input name="po_detail[]" value="<?= $line['detail']; ?>" type="text" class="expand-mobile form-control" />
		</div>
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Receipt:</label>
		<div class="col-sm-2 expand-mobile">
			<?php if($line['file'] != '') {
				echo '<a href="download/'.$line['file'].'">View</a><input type="hidden" name="po_file_exist[]" value="'.$line['file'].'">';
			} ?>
			<input name="po_file[]" type="file" data-filename-placement="inside" class="form-control" />
		</div>
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
		<div class="col-sm-1 expand-mobile">
			<input name="po_qty[]" value="<?= $line['qty']; ?>" onchange="calc_po(this);" type="number" class="expand-mobile form-control" min="0" step="any" />
		</div>
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Unit of Measure:</label>
		<div class="col-sm-1 expand-mobile">
			<input name="po_uom[]" value="<?= $line['uom']; ?>" type="text" class="expand-mobile form-control" />
		</div>
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Price:</label>
		<div class="col-sm-1 expand-mobile">
			<input name="po_unit_price[]" value="<?= $line['unit_price']; ?>" onchange="calc_po(this);" type="number" class="expand-mobile form-control" min="0" step="any" />
		</div>
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Tax:</label>
		<div class="col-sm-1 expand-mobile">
			<input name="po_unit_tax[]" value="<?= $line['unit_tax']; ?>" onchange="calc_po(this);" type="number" class="expand-mobile form-control" min="0" step="any" />
		</div>
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Total:</label>
		<div class="col-sm-2 expand-mobile">
			<input name="po_unit_total[]" value="<?= $line['unit_total']; ?>" type="number" class="expand-mobile form-control" min="0" step="any" />
		</div>
		<div class="col-sm-1">
			<button type="button" class="btn brand-btn form-control" onclick="rem_po(this); return false;">Delete</button>
		</div>
	</div>
<?php } ?>
<div class="form-group">
	<div class="col-sm-8"></div>
	<div class="col-sm-1 text-center po-tax-total">$<?= number_format($po_tax,2) ?></div>
	<div class="col-sm-2 text-center po-total">$<?= number_format($po_total,2) ?></div>
</div>
<button type="button" class="btn brand-btn pull-right" onclick="add_po(); return false;">Add</button>