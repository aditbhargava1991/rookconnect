<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Chemicals</h3>') ?>
<?php $chemical_details = $dbc->query("SELECT * FROM `ticket_attached` WHERE `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 AND `src_table`='chemical_detail'");
do { ?>
	<?php if($access_all > 0) { ?>
		<div class="multi-block">
			<?php foreach($field_sort_order as $field_sort_field) { ?>
				<?php if(strpos($value_config,',Chemical Location,') !== FALSE && $field_sort_field == 'Chemical Location') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Location:</label>
						<div class="col-sm-8">
							<input type="text" name="position" data-table="ticket_attached" data-id="<?= $chem_detail['id'] ?>" data-id-field="id" data-type="chemical_detail" data-type-field="src_table" class="form-control" value="<?= $chem_detail['position'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Chemical Hours,') !== FALSE && $field_sort_field == 'Chemical Hours') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Hours:</label>
						<div class="col-sm-8">
							<input type="text" name="quantity" data-table="ticket_attached" data-id="<?= $chem_detail['id'] ?>" data-id-field="id" data-type="chemical_detail" data-type-field="src_table" class="form-control" value="<?= $chem_detail['quantity'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Chemical Hrs Cost,') !== FALSE && $field_sort_field == 'Chemical Hrs Cost') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Time Cost:</label>
						<div class="col-sm-8">
							<input type="text" name="rate" data-table="ticket_attached" data-id="<?= $chem_detail['id'] ?>" data-id-field="id" data-type="chemical_detail" data-type-field="src_table" class="form-control" value="<?= $chem_detail['rate'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Chemical Volume,') !== FALSE && $field_sort_field == 'Chemical Volume') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Chemical Volume:</label>
						<div class="col-sm-8">
							<input type="text" name="volume" data-table="ticket_attached" data-id="<?= $chem_detail['id'] ?>" data-id-field="id" data-type="chemical_detail" data-type-field="src_table" class="form-control" value="<?= $chem_detail['volume'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Chemical Vol Cost,') !== FALSE && $field_sort_field == 'Chemical Vol Cost') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Cost per Liter:</label>
						<div class="col-sm-8">
							<input type="text" name="volume_units" data-table="ticket_attached" data-id="<?= $chem_detail['id'] ?>" data-id-field="id" data-type="chemical_detail" data-type-field="src_table" class="form-control" value="<?= $chem_detail['volume_units'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Chemical Total Cost,') !== FALSE && $field_sort_field == 'Chemical Total Cost') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Total Cost:</label>
						<div class="col-sm-8">
							<input type="text" name="total_cost" readonly class="form-control" value="<?= number_format($chem_detail['rate'] + ($chem_detail['volume'] * $chem_detail['volume_units']), 2) ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
			<?php }
			if($access_all > 0) { ?>
				<input type="hidden" name="deleted" data-table="ticket_attached" data-id="<?= $chem_detail['id'] ?>" data-id-field="id" data-type="chemical_detail" data-type-field="src_table" value="0">
				<img class="inline-img pull-right" data-history-label="Checmical" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
				<img class="inline-img pull-right" data-history-label="Chemical" onclick="remMulti(this);" src="../img/remove.png">
				<div class="clearfix"></div>
			<?php } ?>
		</div>
	<?php } else { ?>
		<?php foreach($field_sort_order as $field_sort_field) { ?>
			<?php if(strpos($value_config,',Chemical Location,') !== FALSE && $field_sort_field == 'Chemical Location') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Location:</label>
					<div class="col-sm-8">
						<?= $chem_detail['position'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Location', $chem_detail['position']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Chemical Hours,') !== FALSE && $field_sort_field == 'Chemical Hours') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Hours:</label>
					<div class="col-sm-8">
						<?= $chem_detail['quantity'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Hours', $chem_detail['quantity']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Chemical Hrs Cost,') !== FALSE && $field_sort_field == 'Chemical Hrs Cost') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Time Cost:</label>
					<div class="col-sm-8">
						<?= $chem_detail['rate'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Time Cost', $chem_detail['quantity']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Chemical Volume,') !== FALSE && $field_sort_field == 'Chemical Volume') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Chemical Volume:</label>
					<div class="col-sm-8">
						<?= $chem_detail['volume'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Chemical Volume', $chem_detail['volume']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Chemical Vol Cost,') !== FALSE && $field_sort_field == 'Chemical Vol Cost') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Cost per Liter:</label>
					<div class="col-sm-8">
						<?= $chem_detail['volume_units'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Cost per Liter', $chem_detail['volume_units']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Chemical Total Cost,') !== FALSE && $field_sort_field == 'Chemical Total Cost') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Total Cost:</label>
					<div class="col-sm-8">
						<?= number_format($chem_detail['rate'] + ($chem_detail['volume'] * $chem_detail['volume_units']), 2) ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Total Cost', number_format($chem_detail['rate'] + ($chem_detail['volume'] * $chem_detail['volume_units']), 2)]; ?>
			<?php } ?>
		<?php } ?>
	<?php }
} while($chem_detail = $chemical_details->fetch_assoc()); ?>