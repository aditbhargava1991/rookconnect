<?php if($_GET['status'] == 'unbilled') { ?>
	<h4>Create Invoices</h4>
	<script>
	function create_invoice(id) {
		$.post('ticket_ajax_all.php?action=ticket_invoice',{ticketid:id}, function(response) {
			window.location = response;
		});
	}
	function multi_invoice() {
		var id_list = [];
		$('.invoice_ticket:checked').each(function() {
			id_list.push(this.value);
		});
		create_invoice(id_list.join(','));
	}
	function revert_to_admin(link) {
		$.post('ticket_ajax_all.php?action=revert_to_admin', {ticketid: $(link).closest('td').data('id')});
		$(link).closest('tr').hide();
	}
	</script>
	<?php $invoice_list = $dbc->query("SELECT `tickets`.* FROM `tickets` LEFT JOIN `invoice` ON CONCAT(',',`invoice`.`ticketid`,',') LIKE CONCAT('%,',`tickets`.`ticketid`,',%') WHERE `tickets`.`ticket_type` IN ('".implode("','",$ticket_conf_list)."') `invoice`.`invoiceid` IS NULL AND `tickets`.`deleted`=0 ".(in_array('Administration',$db_config) ?"AND `approvals` IS NOT NULL" : ''));
	if($invoice_list->num_rows > 0) { ?>
		<button class="btn brand-btn pull-right" onclick="multi_invoice(); return false;">Create Invoice for Selected</button>
		<table class="table table-bordered">
			<tr>
				<th><?= TICKET_NOUN ?></th>
				<th style="width: 20em;">Invoice</th>
			</tr>
			<?php while($invoice = $invoice_list->fetch_assoc()) {
				$pdf_name = '../Invoice/Download/invoice_'.$invoice['invoiceid'].'.pdf'; ?>
				<tr>
					<td data-title="<?= TICKET_NOUN ?>"><?php if($tile_security['edit'] > 0) { ?><a href="index.php?edit=<?= $invoice['ticketid'] ?>" onclick="overlayIFrameSlider(this.href+'&calendar_view=true','auto',true,true); return false;"><?= get_ticket_label($dbc, $invoice) ?></a><?php } else { echo get_ticket_label($dbc, $invoice); } ?></td>
					<td data-title="Invoice" data-id="<?= $invoice['ticketid'] ?>"><a href="" onclick="create_invoice($(this).closest('td').data('id')); return false;">Create</a> <label class="form-checkbox any-width"><input type="checkbox" class="invoice_ticket" name="ticketid" value="<?= $invoice['ticketid'] ?>">Select</label><?= in_array('Administration',$db_config) ? ' <a href="" onclick="revert_to_admin(this); return false;">Back to Admin</a>' : '' ?></td>
				</tr>
			<?php } ?>
		</table>
		<button class="btn brand-btn pull-right" onclick="multi_invoice(); return false;">Create Invoice for Selected</button>
	<?php } else {
		echo '<h3>No Unbilled '.TICKET_TILE.' Found</h3>';
	} ?>
<?php } else { ?>
	<?php $invoice_list = $dbc->query("SELECT `tickets`.*, `invoice`.`invoiceid`, `invoice`.`invoice_date`, `invoice`.`status` `inv_status`, `invoice`.`final_price` FROM `invoice` LEFT JOIN `tickets` ON CONCAT(',',`invoice`.`ticketid`,',') LIKE CONCAT('%,',`tickets`.`ticketid`,',%') WHERE `invoice`.`deleted`=0 AND `tickets`.`deleted`=0 ORDER BY `invoiceid` DESC LIMIT 0,25");
	if($invoice_list->num_rows > 0) { ?>
		<h3>Top 25 <?= TICKET_NOUN ?> Invoices</h3>
		<h4>To see more Invoices, go to the <?= tile_visible($dbc, 'posadvanced') ? '<a href="../POSAdvanced/invoice_main.php">'.POS_ADVANCE_TILE.'</a>' : (tile_visible($dbc, 'check_out') ? '<a href="../Invoice/invoice_main.php">Check Out</a>' : 'Point of Sale') ?> tile.</h4>
		<table class="table table-bordered">
			<tr>
				<th><?= TICKET_NOUN ?></th>
				<th>Invoice #</th>
				<th>Status</th>
				<th>Total Price</th>
				<th>Invoice</th>
			</tr>
			<?php while($invoice = $invoice_list->fetch_assoc()) {
				$pdf_name = '../Invoice/Download/invoice_'.$invoice['invoiceid'].'.pdf'; ?>
				<tr>
					<td data-title="<?= TICKET_NOUN ?>"><?php if($tile_security['edit'] > 0) { ?><a href="index.php?edit=<?= $invoice['ticketid'] ?>" onclick="overlayIFrameSlider(this.href+'&calendar_view=true','auto',true,true); return false;"><?= get_ticket_label($dbc, $invoice) ?></a><?php } else { echo get_ticket_label($dbc, $invoice); } ?></td>
					<td data-title="Invoice #">#<?= $invoice['invoiceid'].' '.$invoice['invoice_date'] ?></td>
					<td data-title="Status"><?= $invoice['inv_status'] ?></td>
					<td data-title="Total Price"><?= $invoice['final_price'] ?></td>
					<td data-title="Invoice"><?php if(file_exists($pdf_name)) { ?><a href="<?= $pdf_name ?>" target="_blank"><img src="../img/pdf.png" class="inline-img">Invoice #<?= $invoice['invoiceid'] ?></a><?php } ?></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo '<h3>No Invoices Found</h3>';
	} ?>
<?php } ?>