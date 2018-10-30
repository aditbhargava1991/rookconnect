<script>
/*function add_new_po() {
	$('.po-btn').hide();
	$('#iframe_new_po');
	$('#new_po_iframe_div').show();
	$('#iframe_new_po').off('load').load(function() {
		$('#iframe_new_po').off('load');
		$('#iframe_new_po').load(function() {
			close_new_po();
		});
	});
	$('#iframe_new_po').attr('src', 'new_po.php?workorderid=<?= $workorderid ?>');
}
function close_new_po() {
	$('.po-btn').show();
	$('#new_po_iframe_div').hide();
}*/
function new_po_added(id, total) {
	$('.po_table').find('.no_order_msg').remove();
	$('.po_table').append('<tr><td data-title=""><label style="width:100%;"><input type="checkbox" name="po_id" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," checked value="'+id+'"></label></td>'+
			'<td data-title="PO">#'+id+'</td>'+
			'<td data-title="3rd Party Invoice #"><input type="text" class="form-control" name="invoice_number" data-table="ticket_purchase_orders" data-id="'+id+'" data-id-field="id" value=""></td>'+
			'<td data-title="Invoice"><a href="" onclick="po_attach_invoice(this); return false;" data-id="'+id+'"><img class="inline-img" src="../img/icons/ROOK-attachment-icon.png"></a></td>'+
			'<td data-title="Total Price">'+total+'</td>'+
			'<td data-title="Mark Up"><input type="text" name="mark_up[]" value="0" class="form-control" onchange="markup(this, '+total+');"></td>'+
			'<td data-title="Total"><input type="text" name="marked_up_total" value="'+total+'" class="form-control"></td></tr>');
	setSave();
	$('.po_table [name=po_id]').last().change();
}
function po_attach_invoice(button) {
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
				url: "ticket_ajax_all.php?action=po_invoice",
				data: fileData,
				success: function(response) {
					$(button).before('<a href="download/'+response+'" target="_blank">View</a> ');
				}
			});
		}
	}).click();
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
	<?php $pos = mysqli_query($dbc, "SELECT * FROM `ticket_purchase_orders` WHERE (SELECT IFNULL(GROUP_CONCAT(`po_id`),'') FROM `tickets` WHERE `deleted`=0 AND `po_id` != '' AND `ticketid` != '$ticketid') NOT LIKE CONCAT(',',`id`,',')");
	if(mysqli_num_rows($pos) > 0) {
		while($po = mysqli_fetch_array($pos)) { ?>
			<tr>
				<td data-title=""><label style="width:100%;"><input type="checkbox" name="po_id" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," <?= (in_array($po['id'],explode(',',$get_ticket['po_id'])) ? 'checked' : '') ?> value="<?= $po['id'] ?>"></label></td>
				<td data-title="PO">#<?= $po['id'] ?></td>
				<td data-title="3rd Party Invoice #"><input type="text" class="form-control" name="invoice_number" data-table="ticket_purchase_orders" data-id="<?= $po['id'] ?>" data-id-field="id" value="<?= $po['invoice_number'] ?>"></td>
				<td data-title="Invoice"><?= ($po['invoice'] != '' && file_exists('download/'.$po['invoice']) ? '<a href="download/'.$po['invoice'].'" target="_blank">View</a>' : '') ?>
					<a href="" onclick="po_attach_invoice(this); return false;" data-history-label="Attach PO Invoice" data-id="<?= $po['id'] ?>"><img class="inline-img" src="../img/icons/ROOK-attachment-icon.png"></a></td>
				<td data-title="Total Price"><?= $po['final_total'] ?></td>
				<td data-title="Mark Up"><input type="text" name="mark_up[]" value="0" class="form-control" onchange="markup(this, <?= $po['final_total'] ?>);"></td>
				<td data-title="Total"><input type="text" name="marked_up_total" value="<?= $po['final_total'] ?>" class="form-control"></td>
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
<button class="btn brand-btn pull-right po-btn" data-history-label="New PO" onclick="overlayIFrameSlider('new_po.php?workorderid=<?= $ticketid ?>','auto',true,true); return false;">Create PO</button>