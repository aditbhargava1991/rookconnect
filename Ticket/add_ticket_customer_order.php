<?= !$custom_accordion ? (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Customer Orders</h3>') : '' ?>
<?php $ticket_co_list = array_filter(explode('#*#',$get_ticket['customer_order_num']));
$co_numbers = $dbc->query("SELECT `position` FROM `ticket_attached` WHERE `deleted`=0 AND `ticketid` > 0 AND `src_table`='inventory' AND `ticketid`='$ticketid' AND IFNULL(`position`,'') != '' GROUP BY `position`");
$co_line_list = [];
while($co_num_line = $co_numbers->fetch_assoc()) {
	$co_line_list[] = $co_num_line['position'];
}
$co_list = array_unique(array_merge($co_line_list,$ticket_co_list));
sort($co_list);
foreach($field_sort_order as $field_sort_field) {
	if($access_project == TRUE) { ?>
		<?php if ( strpos($value_config, ',CO List,') !== false && $field_sort_field == 'CO List' ) {
			$ticket_co_list[] = '';
			$co_list[] = '';
			foreach($co_list as $co_num_line) { ?>
				<?php if($co_num_line == '' || in_array($co_num_line,$ticket_co_list)) { ?>
					<div class="multi-block form-group">
					  <label for="site_name" class="col-sm-4 control-label">Customer Order #:</label>
						<div class="col-sm-7">
							<input type="text" name="customer_order_num" id="customer_order_num" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="#*#" class="form-control" value="<?= $co_num_line ?>" placeholder="The Customer Order # provided by the customer">
						</div>
						<div class="col-sm-1">
							<?php if(strpos($value_config,',CO Slider Icons,') !== FALSE) { ?>
								<a href="line_item_views.php?co=<?= $co_num_line ?>" onclick="overlayIFrameSlider('line_item_views.php?co='+$(this).closest('.form-group').find('[name=customer_order_num]').val(),'auto',true,true); return false;"><img class="inline-img pull-right" src="../img/icons/eyeball.png"></a>
							<?php } ?>
							<img class="inline-img pull-right" data-history-label="Customer Order #" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
							<img class="inline-img pull-right" data-history-label="Customer Order #" onclick="remMulti(this);" src="../img/remove.png">
						</div>
					</div>
				<?php } else { ?>
					<div class="form-group">
					  <label for="site_name" class="col-sm-4 control-label">Customer Order #:</label>
						<div class="col-sm-8">
							<?= $co_num_line ?>
							<?php if(strpos($value_config,',CO Slider Icons,') !== FALSE) { ?>
								<a href="line_item_views.php?co=<?= $co_num_line ?>" onclick="overlayIFrameSlider(this.href,'auto',true,true); return false;"><img class="inline-img pull-right" src="../img/icons/eyeball.png"></a>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
			<?php } ?>
		<?php } ?>
	<?php } else { ?>
		<?php if ( strpos($value_config, ',CO List,') !== false && $field_sort_field == 'CO List' ) {
			foreach($co_list as $co_num_line) { ?>
				<div class="form-group">
				  <label for="site_name" class="col-sm-4 control-label">Customer Order #:</label>
				  <div class="col-sm-8">
					<?= $co_num_line ?>
					<?php if(strpos($value_config,',CO Slider Icons,') !== FALSE) { ?>
						<a href="line_item_views.php?co=<?= $co_num_line ?>" onclick="overlayIFrameSlider(this.href,'auto',true,true); return false;"><img class="inline-img pull-right" src="../img/icons/eyeball.png"></a>
					<?php } ?>
				  </div>
				</div>
				<?php $pdf_contents[] = ['Customer Order #', $co_num_line]; ?>
			<?php }
		} ?>
	<?php }
} ?>
