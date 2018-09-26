<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Miscellaneous</h3>') ?>
<?php if(strpos($value_config,',Miscellaneous Inline,') !== FALSE) { ?>
	<?php if(strpos($value_config,',Miscellaneous Name,') !== FALSE) { ?>
		<div class="col-sm-6 hide-titles-mob text-center">Description</div>
	<?php } ?>
	<?php if(strpos($value_config,',Miscellaneous Price,') !== FALSE) { ?>
		<div class="col-sm-2 hide-titles-mob text-center">Price</div>
	<?php } ?>
	<?php if(strpos($value_config,',Miscellaneous Quantity,') !== FALSE) { ?>
		<div class="col-sm-1 hide-titles-mob text-center">Quantity</div>
	<?php } ?>
	<?php if(strpos($value_config,',Miscellaneous Total,') !== FALSE) { ?>
		<div class="col-sm-2 hide-titles-mob text-center">Total</div>
	<?php } ?>
	<div class="col-sm-1 hide-titles-mob text-center"></div>
<?php } ?>
<?php $misc_list = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`src_table`='misc_item' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily);
$misc_item = mysqli_fetch_assoc($misc_list);
do {
	if($access_all > 0) { ?>
		<div class="multi-block">
			<?php foreach($field_sort_order as $field_sort_field) { ?>
				<?php if(strpos($value_config,',Miscellaneous Inline,') !== FALSE && $field_sort_field == 'Miscellaneous Inline') { ?>
					<?php if(strpos($value_config,',Miscellaneous Name,') !== FALSE) { ?>
						<div class="col-sm-6">
							<label class="control-label show-on-mob">Description:</label>
							<input type="text" name="description" data-table="ticket_attached" data-id="<?= $misc_item['id'] ?>" data-id-field="id" data-type="misc_item" data-type-field="src_table" class="form-control" value="<?= $misc_item['description'] ?>">
						</div>
					<?php } ?>
					<?php if(strpos($value_config,',Miscellaneous Price,') !== FALSE) { ?>
						<div class="col-sm-2">
							<label class="control-label show-on-mob">Price:</label>
							<input type="number" min=0 step="any" name="rate" data-table="ticket_attached" data-id="<?= $misc_item['id'] ?>" data-id-field="id" data-type="misc_item" data-type-field="src_table" class="form-control" value="<?= $misc_item['rate'] ?>">
						</div>
					<?php } ?>
					<?php if(strpos($value_config,',Miscellaneous Quantity,') !== FALSE) { ?>
						<div class="col-sm-1">
							<label class="control-label show-on-mob">Quantity:</label>
							<input type="number" min=0 step="1" name="qty" data-table="ticket_attached" data-id="<?= $misc_item['id'] ?>" data-id-field="id" data-type="misc_item" data-type-field="src_table" class="form-control" value="<?= $misc_item['qty'] ?>">
						</div>
					<?php } ?>
					<?php if(strpos($value_config,',Miscellaneous Total,') !== FALSE) { ?>
						<div class="col-sm-2">
							<label class="control-label show-on-mob">Total:</label>
							<input type="text" readonly name="total" class="form-control" value="<?= $misc_item['qty']*$misc_item['rate'] ?>">
						</div>
					<?php } ?>
					<div class="col-sm-1">
						<input type="hidden" name="deleted" data-table="ticket_attached" data-id="<?= $misc_item['id'] ?>" data-id-field="id" data-type="misc_item" data-type-field="src_table" value="0">
						<img class="inline-img pull-right" data-history-label="Miscellaneous" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
						<img class="inline-img pull-right" data-history-label="Miscellaneous" onclick="remMulti(this);" src="../img/remove.png">
					</div>
				<?php } ?>
				<?php if(strpos($value_config,',Miscellaneous Inline,') === FALSE && strpos($value_config,',Miscellaneous Name,') !== FALSE && $field_sort_field == 'Miscellaneous Name') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Description:</label>
						<div class="col-sm-8">
							<input type="text" name="description" data-table="ticket_attached" data-id="<?= $misc_item['id'] ?>" data-id-field="id" data-type="misc_item" data-type-field="src_table" class="form-control" value="<?= $misc_item['description'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Miscellaneous Inline,') === FALSE && strpos($value_config,',Miscellaneous Price,') !== FALSE && $field_sort_field == 'Miscellaneous Price') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Price:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="any" name="rate" data-table="ticket_attached" data-id="<?= $misc_item['id'] ?>" data-id-field="id" data-type="misc_item" data-type-field="src_table" class="form-control" value="<?= $misc_item['rate'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Miscellaneous Inline,') === FALSE && strpos($value_config,',Miscellaneous Quantity,') !== FALSE && $field_sort_field == 'Miscellaneous Quantity') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Quantity:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="1" name="qty" data-table="ticket_attached" data-id="<?= $misc_item['id'] ?>" data-id-field="id" data-type="misc_item" data-type-field="src_table" class="form-control" value="<?= $misc_item['qty'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Miscellaneous Inline,') === FALSE && strpos($value_config,',Miscellaneous Total,') !== FALSE && $field_sort_field == 'Miscellaneous Total') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Total:</label>
						<div class="col-sm-8">
							<input type="text" readonly name="total" class="form-control" value="<?= $misc_item['qty']*$misc_item['rate'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
			<?php } ?>
		</div>
	<?php } else { ?>
		<?php foreach($field_sort_order as $field_sort_field) { ?>
			<?php if(strpos($value_config,',Miscellaneous Inline,') !== FALSE && $field_sort_field == 'Miscellaneous Inline') { ?>
				<?php if(strpos($value_config,',Miscellaneous Name,') !== FALSE) { ?>
					<div class="col-sm-6">
						<label class="control-label show-on-mob">Description:</label>
						<?= $misc_item['description'] ?>
					</div>
					<?php $pdf_contents[] = ['Description', $misc_item['description']]; ?>
				<?php } ?>
				<?php if(strpos($value_config,',Miscellaneous Price,') !== FALSE) { ?>
					<div class="col-sm-2">
						<label class="control-label show-on-mob">Price:</label>
						<?= $misc_item['rate'] ?>
					</div>
					<?php $pdf_contents[] = ['Price', $misc_item['rate']]; ?>
				<?php } ?>
				<?php if(strpos($value_config,',Miscellaneous Quantity,') !== FALSE) { ?>
					<div class="col-sm-1">
						<label class="control-label show-on-mob">Quantity:</label>
						<?= $misc_item['qty'] ?>
					</div>
					<?php $pdf_contents[] = ['Quantity', $misc_item['qty']]; ?>
				<?php } ?>
				<?php if(strpos($value_config,',Miscellaneous Total,') !== FALSE) { ?>
					<div class="col-sm-2">
						<label class="control-label show-on-mob">Total:</label>
						<?= $misc_item['qty']*$misc_item['rate'] ?>
					</div>
					<?php $pdf_contents[] = ['Total', $misc_item['qty']*$misc_item['rate']]; ?>
				<?php } ?>
				<div class="col-sm-1">
				</div>
			<?php } ?>
			<?php if(strpos($value_config,',Miscellaneous Inline,') === FALSE && strpos($value_config,',Miscellaneous Name,') !== FALSE && $field_sort_field == 'Miscellaneous Name') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Description:</label>
					<div class="col-sm-8">
						<?= $misc_item['description'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Description', $misc_item['description']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Miscellaneous Inline,') === FALSE && strpos($value_config,',Miscellaneous Price,') !== FALSE && $field_sort_field == 'Miscellaneous Price') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Price:</label>
					<div class="col-sm-8">
						<?= $misc_item['rate'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Price', $misc_item['rate']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Miscellaneous Inline,') === FALSE && strpos($value_config,',Miscellaneous Quantity,') !== FALSE && $field_sort_field == 'Miscellaneous Quantity') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Quantity:</label>
					<div class="col-sm-8">
						<?= $misc_item['qty'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Quantity', $misc_item['qty']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Miscellaneous Inline,') === FALSE && strpos($value_config,',Miscellaneous Total,') !== FALSE && $field_sort_field == 'Miscellaneous Total') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Total:</label>
					<div class="col-sm-8">
						<?= $misc_item['qty']*$misc_item['rate'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Total', $misc_item['qty']*$misc_item['rate']]; ?>
			<?php } ?>
		<?php } ?>
	<?php }
} while($misc_item = mysqli_fetch_assoc($misc_list)); ?>
<?php if(strpos($value_config,',Miscellaneous Billing,') !== FALSE) { ?>
	<div class="misc_billing_summary"></div>
<?php } ?>