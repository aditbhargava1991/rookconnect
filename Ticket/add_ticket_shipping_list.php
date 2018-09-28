<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Shipping List</h3>') ?>
<?php $shipping_list = mysqli_query($dbc, "SELECT `ticket_attached`.`id`, `ticket_attached`.`item_id`, `ticket_attached`.`rate`, `ticket_attached`.`qty`, `ticket_attached`.`volume`, `ticket_attached`.`description`, `ticket_attached`.`status`, `ticket_attached`.`class`, `ticket_attached`.`subclass`, `ticket_attached`.`unit`, `ticket_attached`.`pg` FROM `ticket_attached` WHERE `ticket_attached`.`description` != '' AND `ticket_attached`.`src_table`='shipping_list' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily);
$shipping = mysqli_fetch_assoc($shipping_list);
do {
	if($access_all > 0) { ?>
		<div class="multi-block">
			<?php foreach($field_sort_order as $field_sort_field) { ?>
				<?php if(strpos($value_config,',Shipping List Type,') !== FALSE && $field_sort_field == 'Shipping List Type') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Tab:</label>
						<div class="col-sm-7 select-div">
							<select name="item_id" data-table="ticket_attached" data-id="<?= $shipping['id'] ?>" data-id-field="id" data-type="shipping_list" data-type-field="src_table" class="chosen-select-deselect"><option></option>
								<?php $shipping_descriptions = mysqli_query($dbc, "SELECT `description` FROM `ticket_attached` WHERE `deleted`=0 AND `src_table`='shipping_list' GROUP BY `description` ORDER BY `description`");
								while($shipping_description = mysqli_fetch_assoc($shipping_descriptions)) { ?>
									<option <?= $shipping['description'] == $shipping_description['description'] ? 'selected' : '' ?> value="<?= $shipping_description['description'] ?>"><?= $shipping_description['description'] ?></option>
								<?php } ?>
								<option value="MANUAL">Add New</option>
							</select>
						</div>
						<div class="col-sm-7 manual-div" style="display:none;">
							<input name="description" data-table="ticket_attached" data-id="<?= $shipping['id'] ?>" data-id-field="id" data-type="shipping_list" data-type-field="src_table" class="form-control" value="<?= $shipping['description'] ?>">
						</div>
						<div class="col-sm-1">
							<input type="hidden" name="deleted" data-table="ticket_attached" data-id="<?= $shipping['id'] ?>" data-id-field="id" data-type="shipping_list" data-type-field="src_table" value="0">
							<img class="inline-img pull-right" data-history-label="Shipping List" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
							<img class="inline-img pull-right" data-history-label="Shipping List" onclick="remMulti(this);" src="../img/remove.png">
						</div>
						<div class="clearfix"></div>
					</div>
				<?php } ?>
				<?php if(strpos($value_config,',Shipping List Class,') !== FALSE && $field_sort_field == 'Shipping List Class') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Class:</label>
						<div class="col-sm-8">
							<input type="text" name="class" data-table="ticket_attached" data-id="<?= $shipping['id'] ?>" data-id-field="id" data-type="shipping_list" data-type-field="src_table" class="form-control" value="<?= $shipping['class'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Shipping List Subclass,') !== FALSE && $field_sort_field == 'Shipping List Subclass') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Subclass:</label>
						<div class="col-sm-8">
							<input type="text" name="subclass" data-table="ticket_attached" data-id="<?= $shipping['id'] ?>" data-id-field="id" data-type="shipping_list" data-type-field="src_table" class="form-control" value="<?= $shipping['subclass'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Shipping List Unit,') !== FALSE && $field_sort_field == 'Shipping List Unit') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Unit:</label>
						<div class="col-sm-8">
							<input type="text" name="unit" data-table="ticket_attached" data-id="<?= $shipping['id'] ?>" data-id-field="id" data-type="shipping_list" data-type-field="src_table" class="form-control" value="<?= $shipping['unit'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Shipping List PG,') !== FALSE && $field_sort_field == 'Shipping List PG') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">PG:</label>
						<div class="col-sm-8">
							<input type="text" name="pg" data-table="ticket_attached" data-id="<?= $shipping['id'] ?>" data-id-field="id" data-type="shipping_list" data-type-field="src_table" class="form-control" value="<?= $shipping['pg'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Shipping List Quantity,') !== FALSE && $field_sort_field == 'Shipping List Quantity') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Quantity:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="0.01" name="qty" data-table="ticket_attached" data-id="<?= $shipping['id'] ?>" data-id-field="id" data-type="shipping_list" data-type-field="src_table" class="form-control" value="<?= $shipping['qty'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Shipping List Volume,') !== FALSE && $field_sort_field == 'Shipping List Volume') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Volume:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="0.01" name="volume" data-table="ticket_attached" data-id="<?= $shipping['id'] ?>" data-id-field="id" data-type="shipping_list" data-type-field="src_table" class="form-control" value="<?= $shipping['volume'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Shipping List Rate,') !== FALSE && $field_sort_field == 'Shipping List Rate') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Unit Price:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="0.01" name="rate" data-table="ticket_attached" data-id="<?= $shipping['id'] ?>" data-id-field="id" data-type="shipping_list" data-type-field="src_table" class="form-control" value="<?= $shipping['rate'] ?>">
						</div>
						<div class="clearfix"></div>
					</div>
				<?php } ?>
			<?php } ?>
		</div>
	<?php } else if($shipping['description'] != '') { ?>
		<div class="multi-block">
			<?php foreach($field_sort_order as $field_sort_field) { ?>
				<?php if(strpos($value_config,',Shipping List Type,') !== FALSE && $field_sort_field == 'Shipping List Type') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Tab:</label>
						<div class="col-sm-8"><?= $shipping['description'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Type', $shipping['description']]; ?>
				<?php } ?>
				<?php if(strpos($value_config,',Shipping List Class,') !== FALSE && $field_sort_field == 'Shipping List Class') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Class:</label>
						<div class="col-sm-8">
							<?= $shipping['class'] ?>
						</div>
					</div>
					<div class="clearfix"></div>
					<?php $pdf_contents[] = ['Class', $shipping['class']]; ?>
				<?php } ?>
				<?php if(strpos($value_config,',Shipping List Subclass,') !== FALSE && $field_sort_field == 'Shipping List Subclass') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Subclass:</label>
						<div class="col-sm-8">
							<?= $shipping['subclass'] ?>
						</div>
					</div>
					<div class="clearfix"></div>
					<?php $pdf_contents[] = ['Subclass', $shipping['subclass']]; ?>
				<?php } ?>
				<?php if(strpos($value_config,',Shipping List Unit,') !== FALSE && $field_sort_field == 'Shipping List Unit') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Unit:</label>
						<div class="col-sm-8">
							<?= $shipping['unit'] ?>
						</div>
					</div>
					<div class="clearfix"></div>
					<?php $pdf_contents[] = ['Unit', $shipping['unit']]; ?>
				<?php } ?>
				<?php if(strpos($value_config,',Shipping List PG,') !== FALSE && $field_sort_field == 'Shipping List PG') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">PG:</label>
						<div class="col-sm-8">
							<?= $shipping['pg'] ?>
						</div>
					</div>
					<div class="clearfix"></div>
					<?php $pdf_contents[] = ['PG', $shipping['pg']]; ?>
				<?php } ?>
				<?php if(strpos($value_config,',Shipping List Quantity,') !== FALSE && $field_sort_field == 'Shipping List Quantity') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Quantity:</label>
						<div class="col-sm-8">
							<?= $shipping['qty'] ?>
						</div>
					</div>
					<div class="clearfix"></div>
					<?php $pdf_contents[] = ['Quantity', $shipping['qty']]; ?>
				<?php } ?>
				<?php if(strpos($value_config,',Shipping List Quantity,') !== FALSE && $field_sort_field == 'Shipping List Quantity') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Volume:</label>
						<div class="col-sm-8">
							<?= $shipping['volume'] ?>
						</div>
					</div>
					<div class="clearfix"></div>
					<?php $pdf_contents[] = ['Volume', $shipping['volume']]; ?>
				<?php } ?>
				<?php if(strpos($value_config,',Shipping List Rate,') !== FALSE && $field_sort_field == 'Shipping List Rate') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Unit Price:</label>
						<div class="col-sm-8">
							<?= $shipping['rate'] ?>
						</div>
						<div class="clearfix"></div>
					</div>
					<?php $pdf_contents[] = ['Unit Price', $shipping['rate']]; ?>
				<?php } ?>
			<?php } ?>
		</div>
	<?php }
} while($shipping = mysqli_fetch_assoc($shipping_list)); ?>