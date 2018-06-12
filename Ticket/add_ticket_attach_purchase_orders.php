<script>
function new_po_added(id, total) {
	$('.po_table').find('.no_order_msg').remove();
	$('.po_table').append('<tr><td data-title=""><label style="width:100%;"><input type="checkbox" name="po_id" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," checked value="'+id+'"></label></td>'+
			'<td data-title="PO">#'+id+'</td>'+
			'<td data-title="3rd Party Invoice #"><input type="text" class="form-control" name="invoice_number" data-table="purchase_orders" data-id="'+id+'" data-id-field="id" value=""></td>'+
			'<td data-title="Invoice"><a href="" onclick="po_attach_invoice(this); return false;" data-id="'+id+'"><img class="inline-img" src="../img/icons/ROOK-attachment-icon.png"></a></td>'+
			'<td data-title="Total Price">'+total+'</td>'+
			'<td data-title="Mark Up"><input type="text" name="mark_up[]" value="0" class="form-control" onchange="markup(this, '+total+');"></td>'+
			'<td data-title="Total"><input type="text" name="marked_up_total" value="'+total+'" class="form-control"></td></tr>');
	setSave();
	$('.po_table [name=po_id]').last().change();
}
function attach_po_attach_invoice(button) {
	var id = $(button).data('id');
	$('[name=po_attach_invoice]').off('change').change(function() {
		if(this.files[0] != '') {
			$(button).prevAll('a').remove();
			var fileData = new FormData();
			fileData.append('file',$(this)[0].files[0]);
			fileData.append('id',id);
			$.ajax({
				contentType: false,
				processData: false,
				method: "POST",
				url: "ticket_ajax_all.php?action=attach_po_invoice",
				data: fileData,
				success: function(response) {
					$(button).before('<a href="../Purchase Order/download/'+response+'" target="_blank">View</a> ');
				}
			});
		}
	}).click();
}
function attach_markup(input, total) {
	var markedup = input.value * total / 100 + total;
	$(input).closest('tr').find('[name=mark_up_total]').val(round2Fixed(markedup)).change();
}
$(document).ready(function() {
	$('form').submit(function(event) {
		if($('#iframe_new_po').is(':visible')) {
			$('#iframe_new_po').contents().find('form button[type=submit]:contains("Submit")').click();
			return false;
		}
	});
});
</script>
<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Purchase Orders</h3>') ?>
<?php if($generate_pdf) { ob_clean(); } ?>
<div id="no-more-tables">
	<input type="file" style="display:none;" name="po_attach_invoice">
	<table class='table table-bordered po_table'><tr class='hidden-xs hidden-sm'><th>Attach</th><th>PO</th><th>3rd Party Invoice #</th><th>Invoice</th><th>Total Price</th><th>Mark Up</th><th>Total</th></tr>
	<?php $pos = mysqli_query($dbc, "SELECT * FROM `purchase_orders` WHERE (SELECT IFNULL(GROUP_CONCAT(`po_id`),'') FROM `tickets` WHERE `deleted`=0 AND `po_id` != '' AND `ticketid` != '$ticketid') NOT LIKE CONCAT(',',`posid`,',')");
	if(mysqli_num_rows($pos) > 0) {
		while($po = mysqli_fetch_array($pos)) { ?>
			<tr>
				<td data-title=""><label style="width:100%;"><input type="checkbox" name="po_id" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," <?= (in_array($po['posid'],explode(',',$get_ticket['po_id'])) ? 'checked' : '') ?> value="<?= $po['id'] ?>"></label></td>
				<td data-title="PO">#<?= empty($po['name']) ? $po['posid'] : $po['name'] ?></td>
				<td data-title="3rd Party Invoice #"><input type="text" class="form-control" name="invoice_number" data-table="purchase_orders" data-id="<?= $po['posid'] ?>" data-id-field="id" value="<?= $po['invoice_number'] ?>"></td>
				<td data-title="Invoice"><?= ($po['upload'] != '' && file_exists('download/'.$po['upload']) ? '<a href="../Purchase Order/download/'.$po['upload'].'" target="_blank">View</a>' : '') ?>
					<a href="" onclick="attach_po_attach_invoice(this); return false;" data-id="<?= $po['posid'] ?>"><img class="inline-img" src="../img/icons/ROOK-attachment-icon.png"></a></td>
				<td data-title="Total Price"><?= $po['total_price'] ?></td>
				<td data-title="Mark Up"><input type="text" name="mark_up" data-table="purchase_orders" data-id="<?= $po['posid'] ?>" data-id-field="id" value="<?= $po['markup'] ?>" class="form-control" onchange="attach_markup(this, <?= $po['total_price'] ?>);"></td>
				<td data-title="Total"><input type="text" name="mark_up_total" data-table="purchase_orders" data-id="<?= $po['posid'] ?>" data-id-field="id" value="<?= $po['mark_up_total'] ?>" class="form-control"></td>
			</tr>
		<?php }
	} else {
		echo "<tr class='no_order_msg'><td colspan='7'>No Purchase Orders to Attach</td></tr>";
	} ?>
	</table>
</div>
<?php if($generate_pdf) { $pdf_contents[] = ['', ob_get_contents()]; } ?>
<div id="new_po_iframe_div" style="display:none">
	<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' onclick="close_new_po();" width="45px" style='position:relative; right: 1em; top:1em; float:right; cursor:pointer;'>
	<span class='iframe_title' style='color:white; font-weight:bold; position: relative; left:1em; top:0.25em; font-size:3em;'>Add New PO</span>
	<iframe name="iframe_new_po" id="iframe_new_po" style="border: 1em solid gray; border-top: 5em solid gray; margin-top: -4em; width: 100%;" src=""></iframe>
</div>
<button class="btn brand-btn pull-right po-btn" onclick="overlayIFrameSlider('../Purchase Order/index.php?tab=create&workorderid=<?= $ticketid ?>','75%',true,true); return false;">Create PO</button>