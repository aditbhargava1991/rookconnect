<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Other Products</h3>') ?>
<?php $other_list = mysqli_query($dbc, "SELECT `ticket_attached`.`id`, `ticket_attached`.`item_id`, `ticket_attached`.`rate`, `ticket_attached`.`qty`, `ticket_attached`.`volume`, `ticket_attached`.`description`, `ticket_attached`.`status` FROM `ticket_attached` WHERE `ticket_attached`.`description` != '' AND `ticket_attached`.`src_table`='other_list' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily);
$other = mysqli_fetch_assoc($other_list);
do {
	if($access_all > 0) { ?>
		<div class="multi-block">
			<?php foreach($field_sort_order as $field_sort_field) { ?>
				<?php if(strpos($value_config,',Other Type,') !== FALSE && $field_sort_field == 'Other Type') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Tab:</label>
						<div class="col-sm-7 select-div">
							<select name="item_id" data-table="ticket_attached" data-id="<?= $other['id'] ?>" data-id-field="id" data-type="other_list" data-type-field="src_table" class="chosen-select-deselect"><option></option>
								<?php $other_descriptions = mysqli_query($dbc, "SELECT `description` FROM `ticket_attached` WHERE `deleted`=0 AND `src_table`='other_list' GROUP BY `description` ORDER BY `description`");
								while($other_description = mysqli_fetch_assoc($other_descriptions)) { ?>
									<option <?= $other['description'] == $other_description['description'] ? 'selected' : '' ?> value="<?= $other_description['description'] ?>"><?= $other_description['description'] ?></option>
								<?php } ?>
								<option value="MANUAL">Add New</option>
							</select>
						</div>
						<div class="col-sm-7 manual-div" style="display:none;">
							<input name="description" data-table="ticket_attached" data-id="<?= $other['id'] ?>" data-id-field="id" data-type="other_list" data-type-field="src_table" class="form-control" value="<?= $other['description'] ?>">
						</div>
						<div class="col-sm-1">
							<input type="hidden" name="deleted" data-table="ticket_attached" data-id="<?= $other['id'] ?>" data-id-field="id" data-type="other_list" data-type-field="src_table" value="0">
							<img class="inline-img pull-right" data-history-label="Other Products" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
							<img class="inline-img pull-right" data-history-label="Other Products" onclick="remMulti(this);" src="../img/remove.png">
						</div>
						<div class="clearfix"></div>
					</div>
				<?php } ?>
				<?php if(strpos($value_config,',Other Quantity,') !== FALSE && $field_sort_field == 'Other Quantity') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Quantity:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="0.01" name="qty" data-table="ticket_attached" data-id="<?= $other['id'] ?>" data-id-field="id" data-type="other_list" data-type-field="src_table" class="form-control" value="<?= $other['qty'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Other Volume,') !== FALSE && $field_sort_field == 'Other Volume') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Volume:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="0.01" name="volume" data-table="ticket_attached" data-id="<?= $other['id'] ?>" data-id-field="id" data-type="other_list" data-type-field="src_table" class="form-control" value="<?= $other['volume'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Other Rate,') !== FALSE && $field_sort_field == 'Other Rate') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Unit Price:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="0.01" name="rate" data-table="ticket_attached" data-id="<?= $other['id'] ?>" data-id-field="id" data-type="other_list" data-type-field="src_table" class="form-control" value="<?= $other['rate'] ?>">
						</div>
						<div class="clearfix"></div>
					</div>
				<?php } ?>
			<?php } ?>
		</div>
	<?php } else if($other['description'] != '') { ?>
		<div class="multi-block">
			<?php foreach($field_sort_order as $field_sort_field) { ?>
				<?php if(strpos($value_config,',Other Type,') !== FALSE && $field_sort_field == 'Other Type') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Tab:</label>
						<div class="col-sm-8"><?= $other['description'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Type', $other['description']]; ?>
				<?php } ?>
				<?php if(strpos($value_config,',Other Quantity,') !== FALSE && $field_sort_field == 'Other Quantity') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Quantity:</label>
						<div class="col-sm-8">
							<?= $other['qty'] ?>
						</div>
					</div>
					<div class="clearfix"></div>
					<?php $pdf_contents[] = ['Quantity', $other['qty']]; ?>
				<?php } ?>
				<?php if(strpos($value_config,',Other Quantity,') !== FALSE && $field_sort_field == 'Other Quantity') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Volume:</label>
						<div class="col-sm-8">
							<?= $other['volume'] ?>
						</div>
					</div>
					<div class="clearfix"></div>
					<?php $pdf_contents[] = ['Volume', $other['volume']]; ?>
				<?php } ?>
				<?php if(strpos($value_config,',Other Rate,') !== FALSE && $field_sort_field == 'Other Rate') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Unit Price:</label>
						<div class="col-sm-8">
							<?= $other['rate'] ?>
						</div>
						<div class="clearfix"></div>
					</div>
					<?php $pdf_contents[] = ['Unit Price', $other['rate']]; ?>
				<?php } ?>
			<?php } ?>
		</div>
	<?php }
} while($other = mysqli_fetch_assoc($other_list)); ?>