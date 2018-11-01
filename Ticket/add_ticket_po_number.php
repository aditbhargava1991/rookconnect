<?= !$custom_accordion ? (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Purchase Orders</h3>') : '' ?>
<?php $ticket_po_list = array_filter(explode('#*#',$get_ticket['purchase_order']));
$po_numbers = $dbc->query("SELECT `po_num` FROM `ticket_attached` WHERE `deleted`=0 AND `ticketid` > 0 AND `src_table` IN ('inventory_general','inventory') AND `ticketid`='$ticketid' AND IFNULL(`po_num`,'') != '' GROUP BY `po_num`");
$po_line_list = [];
while($po_num_line = $po_numbers->fetch_assoc()) {
	$po_num_line['po_num'] = explode('#*#', $po_num_line['po_num']);
	$po_line_list = array_merge($po_line_list, $po_num_line['po_num']);
}
$po_list = array_filter(array_unique(array_merge($po_line_list,$ticket_po_list)));
sort($po_list);
foreach($field_sort_order as $field_sort_field) {
	if($access_project == TRUE) { ?>
		<?php if ( strpos($value_config, ',PO List,') !== false && $field_sort_field == 'PO List' ) {
			$ticket_po_list[] = '';
			$po_list[] = '';
			foreach($po_list as $po_num_line) { ?>
				<?php if($po_num_line == '' || in_array($po_num_line,$ticket_po_list)) { ?>
					<div class="multi-block form-group">
					  <label for="site_name" class="col-sm-4 control-label">Purchase Order #:</label>
						<div class="col-sm-7">
							<input type="text" name="purchase_order" id="purchase_order" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="#*#" class="form-control" value="<?= $po_num_line ?>" placeholder="The PO# provided by the customer">
						</div>
						<div class="col-sm-1">
							<?php if(strpos($value_config,',PO Slider Icons,') !== FALSE) { ?>
								<a href="line_item_views.php?po=<?= $po_num_line ?>" onclick="overlayIFrameSlider('line_item_views.php?po='+$(this).closest('.form-group').find('[name=purchase_order]').val(),'auto',true,true); return false;"><img class="inline-img pull-right" src="../img/icons/eyeball.png"></a>
							<?php } ?>
							<img class="inline-img pull-right" data-history-label="Purchase Order #" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
							<img class="inline-img pull-right" data-remove="1" data-history-label="Purchase Order #" onclick="remMulti(this);" src="../img/remove.png">
						</div>
					</div>
				<?php } else { ?>
					<div class="form-group">
					  <label for="site_name" class="col-sm-4 control-label">Purchase Order #:</label>
						<div class="col-sm-8">
							<?= $po_num_line ?>
							<?php if(strpos($value_config,',PO Slider Icons,') !== FALSE) { ?>
								<a href="line_item_views.php?po=<?= $po_num_line ?>" onclick="overlayIFrameSlider(this.href,'auto',true,true); return false;"><img class="inline-img pull-right" src="../img/icons/eyeball.png"></a>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
			<?php } ?>
		<?php } ?>
	<?php } else { ?>
		<?php if ( strpos($value_config, ',PO List,') !== false && $field_sort_field == 'PO List' ) {
			foreach($po_list as $po_num_line) { ?>
				<div class="form-group">
				  <label for="site_name" class="col-sm-4 control-label">Purchase Order #:</label>
				  <div class="col-sm-8">
					<?= $po_num_line ?>
					<?php if(strpos($value_config,',PO Slider Icons,') !== FALSE) { ?>
						<a href="line_item_views.php?po=<?= $po_num_line ?>" onclick="overlayIFrameSlider(this.href,'auto',true,true); return false;"><img class="inline-img pull-right" src="../img/icons/eyeball.png"></a>
					<?php } ?>
				  </div>
				</div>
				<?php $pdf_contents[] = ['Purchase Order #', $po_num_line]; ?>
			<?php }
		} ?>
	<?php }
} ?>
