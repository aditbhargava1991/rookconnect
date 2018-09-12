<?php include_once('../include.php');
if($_POST['add'] == 'add') {
	if($_POST['picklistid'] > 0) {
		foreach($_POST['inventoryid'] as $i => $inventory) {
			if($inventory > 0 && in_array($inventory, $_POST['inventory_checked'])) {
				$qty = filter_var($_POST['qty'][$i],FILTER_SANITIZE_STRING);
				$dbc->query("INSERT INTO `pick_list_items` (`pick_list`, `inventoryid`, `quantity`) VALUES ('{$_POST['picklistid']}', '$inventory', '$qty')");
			}
		}
	}
	echo "<script>window.top.location.replace(window.top.location.href+(window.top.location.href.indexOf('?') >= 0 ? '&' : '?')+'edit=".$_POST['picklistid']."');</script>";
} else {
	$pick_list_filters = array_filter(explode(',',get_config($dbc, 'pick_list_filters')));
	$options = json_decode($_GET['filters'], true);
	foreach($options as $i => $option) {
		$options[$i] = filter_var($option,FILTER_SANITIZE_STRING);
	} ?>
	<div class="col-sm-12">
		<h3>Inventory<a class="smaller pull-right" href="../blank_loading_page.php"><img class="slider-close" src="../img/icons/cancel.png"></a><br />&nbsp;<div class="clearfix"></div></h3>
		<form class="form-horizontal" action="" method="POST">
			<table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th><?= INVENTORY_NOUN ?> <button class="btn brand-btn pull-right" onclick="$('[name^=inventory_checked]').prop('checked',true); return false;">Select All</button></th>
                        <?php if(in_array('category',$pick_list_filters)) { ?><th><?= INVENTORY_NOUN ?> Tab</th><?php } ?>
                        <?php if(in_array('ticket_po',$pick_list_filters)) { ?><th>Purchase Order #</th><?php } ?>
                        <?php if(in_array('po_line',$pick_list_filters)) { ?><th>Line Item #</th><?php } ?>
                        <?php if(in_array('ticket',$pick_list_filters)) { ?><th><?= TICKET_NOUN ?></th><?php } ?>
                        <?php if(in_array('ticket_customer_order',$pick_list_filters)) { ?><th>Customer Order #</th><?php } ?>
                        <?php if(in_array('detail_customer_order',$pick_list_filters)) { ?><th>Customer Order #</th><?php } ?>
                        <?php if(in_array('pallet',$pick_list_filters)) { ?><th>Pallet #</th><?php } ?>
                        <th>Available</th>
                        <th>Quantity<?php if(in_array('fill_max',$pick_list_filters)) { ?> <button class="btn brand-btn pull-right" onclick="$('[name^=qty]').each(function() { this.value = this.max; }); return false;">Fill Max Qty</button><?php } ?></th>
                    </tr>
                </thead>
				<input type="hidden" name="picklistid" value="<?= $_GET['id'] ?>">
				<?php $list = $dbc->query("SELECT `inventory`.`inventoryid`, `inventory`.`product_name`, `inventory`.`name`, `inventory`.`category`, `inventory`.`pallet`, `inventory`.`quantity` - CAST(`inventory`.`assigned_qty` AS SIGNED INT) `available`, `tickets`.`ticketid`, `tickets`.`ticket_label`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_number`, `tickets`.`customer_order_num`, `ticket_attached`.`po_line`, `ticket_attached`.`position` FROM `inventory` LEFT JOIN `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` AND `ticket_attached`.`src_table`='inventory' LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `inventory`.`deleted`=0 AND '{$options['category']}' IN ('',`inventory`.`category`) AND ('{$options['po']}'='' OR CONCAT('#*#',IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`),'#*#') LIKE '%#*#{$options['po']}#*#%') AND '{$options['po_line']}' IN ('',`ticket_attached`.`po_line`) AND '{$options['detail_customer_order']}' IN ('',`ticket_attached`.`position`) AND '{$options['ticket']}' IN ('',`tickets`.`ticketid`) AND ('{$options['customer_order']}'='' OR CONCAT('#*#',`tickets`.`customer_order_num`,'#*#') LIKE '%#*#{$options['customer_order']}#*#%') AND '{$options['pallet']}' IN ('',`inventory`.`pallet`) AND (IFNULL(`product_name`,'') != '' OR IFNULL(`name`,'') != '') ORDER BY `inventory`.`category`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`), `ticket_attached`.`po_line`, `inventory`.`name`");
				$items = [];
				while($item = $list->fetch_assoc()) { ?>
					<tr>
						<input type="hidden" name="inventoryid[]" value="<?= $item['inventoryid'] ?>">
						<td data-title="<?= INVENTORY_NOUN ?>"><label class="form-checkbox full-width"><input type="checkbox" name="inventory_checked[]" value="<?= $item['inventoryid'] ?>"><?= $item['product_name'].' '.$item['name'] ?></label></td>
						<?php if(in_array('category',$pick_list_filters)) { ?><td data-title="<?= INVENTORY_NOUN ?> Tab"><?= $item['category'] ?></td><?php } ?>
						<?php if(in_array('ticket_po',$pick_list_filters)) { ?><td data-title="Purchase Order #"><?= $item['po_number'] ?></td><?php } ?>
						<?php if(in_array('po_line',$pick_list_filters)) { ?><td data-title="Line Item #"><?= $item['po_line'] ?></td><?php } ?>
						<?php if(in_array('ticket',$pick_list_filters)) { ?><td data-title="<?= TICKET_NOUN ?>"><?= $item['ticket_label'] ?></td><?php } ?>
						<?php if(in_array('ticket_customer_order',$pick_list_filters)) { ?><td data-title="Customer Order #"><?= $item['customer_order_num'] ?></td><?php } ?>
						<?php if(in_array('detail_customer_order',$pick_list_filters)) { ?><td data-title="Customer Order #"><?= $item['position'] ?></td><?php } ?>
						<?php if(in_array('pallet',$pick_list_filters)) { ?><td data-title="Pallet #"><?= $item['pallet'] ?></td><?php } ?>
						<td data-title="Available"><?= $item['available'] ?></label></td>
						<td data-title="Quantity"><input class="form-control" type="number" value="0" step="any" max="<?= $item['available'] ?>" name="qty[]"></td>
					</tr>
					<?php echo ''; ?>
				<?php } ?>
			</table>
			<a href="../blank_loading_page.php" class="btn brand-btn pull-left">Cancel</a>
			<button class="btn brand-btn pull-right" name="add" value="add">Add Items</button>
		</form>
	</div>
<?php } ?>