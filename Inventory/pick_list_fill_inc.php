<?php include_once('../include.php');
$strict_view = strictview_visible_function($dbc, 'inventory');
$tile_security = get_security($dbc, 'inventory');
if($strict_view > 0) {
	$tile_security['edit'] = 0;
	$tile_security['config'] = 0;
}
$match_business = '';
if(!empty(MATCH_CONTACTS)) {
	$match_business = " AND `tickets`.`businessid` IN (".MATCH_CONTACTS.")";
}
if(!empty($_POST['save'])) {
	$picklistid = filter_var($_POST['picklistid'],FILTER_SANITIZE_STRING);
	if($_POST['save'] == 'complete') {
		if(file_exists('download/signature_'.$picklistid.'.png')) {
			unlink('download/signature_'.$picklistid.'.png');
		}
		$signature = filter_var($_POST['signature'],FILTER_SANITIZE_STRING);
		$dbc->query("UPDATE `pick_lists` SET `completed`=1, `completed_by`='{$_SESSION['contactid']}', `signature`='$signature' WHERE `id`='$picklistid'");
	}
	foreach($_POST['id'] as $i => $id) {
		if($id > 0) {
			$filled = filter_var($_POST['filled'][$i],FILTER_SANITIZE_STRING);
			$inv = $dbc->query("SELECT `inventory`.`inventoryid`, `inventory`.`quantity`, `inventory`.`assigned_qty`, `pick_list_items`.`filled` FROM `pick_list_items` LEFT JOIN `inventory` ON `pick_list_items`.`inventoryid`=`inventory`.`inventoryid` WHERE `pick_list_items`.`id`='$id'")->fetch_assoc();
			$qty_diff = $filled - $inv['filled'];
			$dbc->query("UPDATE `inventory` SET `assigned_qty`=`assigned_qty` - $qty_diff, `quantity`=`quantity` - $qty_diff WHERE `inventoryid`='{$inv['inventoryid']}'");
			$before_change = '';
			$temp_invid = $inv['inventoryid'];
      $history = "Inventory with id $temp_invid is been Updated. <br />";
	    add_update_history($dbc, 'inventory_history', $history, '', $before_change);
			$dbc->query("INSERT INTO `inventory_change_log` (`inventoryid`,`contactid`,`location_of_change`,`date_time`,`old_inventory`,`changed_quantity`,`new_inventory`,`change_comment`) SELECT `inventoryid`,'{$_SESSION['contactid']}','Pick List',NOW(),'".$inv['quantity']."','$qty_diff','".($inv['quantity'] - $qty_diff)."','$qty_diff Filled in Pick List' FROM `inventory` WHERE `inventoryid`='{$inv['inventoryid']}'");
			$dbc->query("UPDATE `pick_list_items` SET `filled`='$filled' WHERE `id`='$id'");
		}
	}
	echo "<script>window.location.replace('inventory.php');</script>";
}
?>
<script type="text/javascript" src="inventory.js"></script>
<script>
$(document).ready(function() {
	$('[name=projectid]').change(function() {
		if($(this).find('option:selected').data('business') > 0) {
			$('[name=businessid]').val($(this).find('option:selected').data('business')).trigger('change.select2');
		}
	});
	$('[name=businessid]').change(function() {
		var business = this.value;
		$('[name=projectid] option').each(function() {
			if(!(business > 0) || $(this).data('business') == business) {
				$(this).show();
			} else {
				$(this).hide();
			}
		}).trigger('change.select2');
	});
	$('[name=category]').change(populate_inventory);
});
function addRow() {
	destroyInputs();
	var clone = $('.inv_list .form-group:visible').last().clone();
	clone.find('input,select').val('');
	$('.inv_list .form-group').last().after(clone);
	initInputs();
	$('[name=category]').off('change',populate_inventory).change(populate_inventory);
}
function remRow(item) {
	if($('.inv_list .form-group:visible').length == 1) {
		addRow();
	}

	$(item).closest('.form-group').hide().find('[name="deleted[]"]').val(1);
}
function populate_inventory() {
	var group = $(this).closest('.form-group');
	$.post('inventory_ajax.php?action=category_list',{category: this.value}, function(response) {
		group.find('[name="inventoryid[]"]').empty().html(response).trigger('change.select2');
	});
}
</script>
<?php $list_name = 'New List';
$picklistid = 0;
if($_GET['id'] > 0) {
	$picklistid = $_GET['id'];
	$pick_list = $dbc->query("SELECT * FROM `pick_lists` WHERE `id`='$picklistid'")->fetch_assoc();
	$list_name = $pick_list['name'];
} ?>
<?php if($picklistid > 0) { ?>
	<small><em>Created by <?= get_contact($dbc, $pick_list['created_by']) ?> on <?= date('Y-m-d',strtotime($pick_list['created_date'])) ?></em></small><?php } ?></h2>
<form class="form-horizontal" method="POST" action="">
	<input type="hidden" name="picklistid" value="<?= $picklistid ?>">
	<div class="form-group">
		<?php $pick_list_filters = array_filter(explode(',',get_config($dbc, 'pick_list_filters')));
		$filter_cols = floor(12 / (count($pick_list_filters) + 1)); ?>
		<h4><?= INVENTORY_NOUN ?> List</h4>
		<div class="col-sm-12 inv_list">
			<div class="hide-titles-mob">
				<div class="col-sm-10">
					<?php if(in_array('category',$pick_list_filters)) { ?>
						<div class="col-sm-<?= $filter_cols ?>"><?= INVENTORY_NOUN ?> Tab</div>
					<?php } ?>
					<?php if(in_array('ticket_po',$pick_list_filters)) { ?>
						<div class="col-sm-<?= $filter_cols ?>">Purchase Order #</div>
					<?php } ?>
					<?php if(in_array('po_line',$pick_list_filters)) { ?>
						<div class="col-sm-<?= $filter_cols ?>">Line Item #</div>
					<?php } ?>
					<?php if(in_array('ticket',$pick_list_filters)) { ?>
						<div class="col-sm-<?= $filter_cols ?>"><?= TICKET_NOUN ?></div>
					<?php } ?>
					<?php if(in_array('ticket_customer_order',$pick_list_filters)) { ?>
						<div class="col-sm-<?= $filter_cols ?>">Customer Order #</div>
					<?php } ?>
					<?php if(in_array('detail_customer_order',$pick_list_filters)) { ?>
						<div class="col-sm-<?= $filter_cols ?>">Customer Order #</div>
					<?php } ?>
					<?php if(in_array('pallet',$pick_list_filters)) { ?>
						<div class="col-sm-<?= $filter_cols ?>">Pallet #</div>
					<?php } ?>
					<div class="col-sm-<?= 12 - ($filter_cols * count($pick_list_filters)) ?>"><?= INVENTORY_NOUN ?></div>
				</div>
				<div class="col-sm-1">Quantity</div>
				<div class="col-sm-1">Filled<?php if(in_array('fill_max',$pick_list_filters)) { ?> <button class="btn brand-btn pull-right" onclick="$('[name^=filled]').each(function() { this.value = this.max; }); return false;">Fill Max Qty</button><?php } ?></div>
			</div>
			<?php $items = $dbc->query("SELECT `pick_list_items`.*, `inventory`.`quantity` `available`, `inventory`.`category`, `inventory`.`product_name`, `inventory`.`name`, `inventory`.`pallet`, `tickets`.`ticket_label`, `tickets`.`po_number`, `tickets`.`customer_order_num`, `tickets`.`po_line`, `tickets`.`position` FROM `pick_list_items` LEFT JOIN `inventory` ON `pick_list_items`.`inventoryid`=`inventory`.`inventoryid` LEFT JOIN (SELECT `tickets`.`ticketid`, `ticket_label`, `item_id`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_number`, `customer_order_num`, `tickets`.`businessid`, `ticket_attached`.`po_line`, `ticket_attached`.`position` FROM `ticket_attached` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_attached`.`deleted`=0 AND `tickets`.`deleted`=0 AND `ticket_attached`.`src_table` IN ('inventory','inventory_detailed')) `tickets` ON `inventory`.`inventoryid`=`tickets`.`item_id` WHERE `pick_list_items`.`pick_list`='$picklistid' AND `pick_list_items`.`deleted`=0 AND `pick_list_items`.`pick_list` > 0 $match_business");
			while($item = $items->fetch_assoc()) { ?>
				<div class="form-group">
					<input type="hidden" name="id[]" value="<?= $item['id'] ?>">
					<div class="col-sm-10">
						<?php if(in_array('category',$pick_list_filters)) { ?>
							<div class="col-sm-<?= $filter_cols ?>">
								<span class="show-on-mob"><?= INVENTORY_NOUN ?> Tab: </span>
								<?= $item['category'] ?>
							</div>
						<?php } ?>
						<?php if(in_array('ticket_po',$pick_list_filters)) { ?>
							<div class="col-sm-<?= $filter_cols ?>">
								<span class="show-on-mob">Purchase Order #: </span>
								<?= implode('<br />',array_filter(explode('#*#',$item['po_number']))) ?>
							</div>
						<?php } ?>
						<?php if(in_array('po_line',$pick_list_filters)) { ?>
							<div class="col-sm-<?= $filter_cols ?>">
								<span class="show-on-mob">Line Item #: </span>
								<?= $item['po_line'] ?>
							</div>
						<?php } ?>
						<?php if(in_array('ticket',$pick_list_filters)) { ?>
							<div class="col-sm-<?= $filter_cols ?>">
								<span class="show-on-mob"><?= TICKET_NOUN ?>: </span>
								<?= $item['ticket_label'] ?>
							</div>
						<?php } ?>
						<?php if(in_array('ticket_customer_order',$pick_list_filters)) { ?>
							<div class="col-sm-<?= $filter_cols ?>">
								<span class="show-on-mob">Customer Order #: </span>
								<?= implode('<br />',array_filter(explode('#*#',$item['customer_order_num']))) ?>
							</div>
						<?php } ?>
						<?php if(in_array('detail_customer_order',$pick_list_filters)) { ?>
							<div class="col-sm-<?= $filter_cols ?>">
								<span class="show-on-mob">Customer Order #: </span>
								<?= implode('<br />',array_filter(explode('#*#',$item['position']))) ?>
							</div>
						<?php } ?>
						<?php if(in_array('pallet',$pick_list_filters)) { ?>
							<div class="col-sm-<?= $filter_cols ?>">
								<span class="show-on-mob">Pallet #: </span>
								<?= $item['pallet'] ?>
							</div>
						<?php } ?>
						<div class="col-sm-<?= 12 - ($filter_cols * count($pick_list_filters)) ?>">
							<span class="show-on-mob"><?= INVENTORY_NOUN ?>: </span>
							<?= $item['product_name'].' '.$item['name'] ?>
						</div>
					</div>
					<div class="col-sm-1"><span class="show-on-mob">Quantity: </span><?= $item['quantity'] ?></div>
					<div class="col-sm-1"><span class="show-on-mob">Filled: </span><?= $strict_view > 0 ? $item['filled'] : '<input type="number" min=0 max='.$item['quantity'].' step="any" name="filled[]" value="'.$item['filled'].'" class="form-control">' ?></div>
				</div>
			<?php } ?>
		</div>
	</div>
	<?php if(!($strict_view > 0)) { ?>
	<div class="form-group">
		<label class="col-sm-4 control-label">Signature:</label>
		<div class="col-sm-8">
			<?php $output_name = 'signature';
			include('../phpsign/sign_multiple.php'); ?>
		</div>
	</div>
	<button class="btn brand-btn pull-right" name="save" value="complete">Complete</button>
	<button class="btn brand-btn pull-right" name="save" value="save">Save</button>
	<?php } ?>
</form>
