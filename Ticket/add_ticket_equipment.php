<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Equipment</h3>') ?>
<?php $equipment_list = mysqli_query($dbc, "SELECT `ticket_attached`.`id`, `ticket_attached`.`item_id`, `ticket_attached`.`rate`, `ticket_attached`.`status`, `ticket_attached`.`qty`, `ticket_attached`.`hours_estimated`, `equipment`.* FROM `ticket_attached` LEFT JOIN `equipment` ON `ticket_attached`.`src_table`='equipment' AND `ticket_attached`.`item_id`=`equipment`.`equipmentid` WHERE `ticket_attached`.`item_id` > 0 AND `ticket_attached`.`src_table`='equipment' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily);
$equipment = mysqli_fetch_assoc($equipment_list);
do { ?>
	<div class="multi-block">
		<?php foreach($field_sort_order as $field_sort_field) {
			if($access_all > 0) { ?>
				<?php if ( strpos($value_config, ',Equipment Category,') !== false && $field_sort_field == 'Equipment Category' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Category:</label>
						<div class="col-sm-8">
							<select name="eq_category" class="chosen-select-deselect"><option></option>
								<?php $groups = mysqli_query($dbc, "SELECT `category` FROM `equipment` WHERE `deleted`=0 GROUP BY `category` ORDER BY `category`");
								while($category = mysqli_fetch_assoc($groups)) { ?>
									<option <?= $equipment['category'] == $category['category'] ? 'selected' : '' ?> value="<?= $category['category'] ?>"><?= $category['category'] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if ( strpos($value_config, ',Equipment Make,') !== false && $field_sort_field == 'Equipment Make' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Make:</label>
						<div class="col-sm-8">
							<select name="eq_make" class="chosen-select-deselect"><option></option>
								<?php $groups = mysqli_query($dbc, "SELECT `make` FROM `equipment` WHERE `deleted`=0 GROUP BY `make` ORDER BY `make`");
								while($make = mysqli_fetch_assoc($groups)) { ?>
									<option <?= $equipment['make'] == $make['make'] ? 'selected' : '' ?> value="<?= $make['make'] ?>"><?= $make['make'] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if ( strpos($value_config, ',Equipment Model,') !== false && $field_sort_field == 'Equipment Model' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Model:</label>
						<div class="col-sm-8">
							<select name="eq_model" class="chosen-select-deselect"><option></option>
								<?php $groups = mysqli_query($dbc, "SELECT `model` FROM `equipment` WHERE `deleted`=0 GROUP BY `model` ORDER BY `model`");
								while($model = mysqli_fetch_assoc($groups)) { ?>
									<option <?= $equipment['model'] == $model['model'] ? 'selected' : '' ?> value="<?= $model['model'] ?>"><?= $model['model'] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if ( strpos($value_config, ',Equipment Unit,') !== false && $field_sort_field == 'Equipment Unit' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Unit #:</label>
						<div class="col-sm-8">
							<select name="item_id" data-table="ticket_attached" data-id="<?= $equipment['id'] ?>" data-id-field="id" data-type="equipment" data-type-field="src_table" class="chosen-select-deselect"><option></option>
								<?php $groups = mysqli_query($dbc, "SELECT `category`, `make`, `model`, `unit_number`, `equipmentid` FROM `equipment` WHERE `deleted`=0 ORDER BY `category`, `make`, `model`, `unit_number`");
								while($units = mysqli_fetch_assoc($groups)) { ?>
									<option data-category="<?= $units['category'] ?>" data-make="<?= $units['make'] ?>" data-model="<?= $units['model'] ?>" <?= $equipment['item_id'] == $units['equipmentid'] ? 'selected' : '' ?> value="<?= $units['equipmentid'] ?>"><?= $units['unit_number'] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if ( strpos($value_config, ',Equipment Residue,') !== false && $field_sort_field == 'Equipment Residue' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Residue:</label>
						<div class="col-sm-8">
							<input type="text" name="residue" data-table="ticket_attached" data-id="<?= $equipment['id'] ?>" data-id-field="id" data-type="equipment" data-type-field="src_table" class="form-control" value="<?= $equipment['residue'] ?>">
						</div>
					</div>
				<?php } ?>
				<?php if ( strpos($value_config, ',Equipment Hours,') !== false && $field_sort_field == 'Equipment Hours' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Hours:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="0.01" name="hours_estimated" data-table="ticket_attached" data-id="<?= $equipment['id'] ?>" data-id-field="id" data-type="equipment" data-type-field="src_table" class="form-control" value="<?= $equipment['hours_estimated'] ?>">
						</div>
					</div>
				<?php } ?>
				<?php if ( strpos($value_config, ',Equipment Volume,') !== false && $field_sort_field == 'Equipment Volume' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Volume:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="0.01" name="qty" data-table="ticket_attached" data-id="<?= $equipment['id'] ?>" data-id-field="id" data-type="equipment" data-type-field="src_table" class="form-control" value="<?= $equipment['qty'] ?>">
						</div>
					</div>
				<?php } ?>
				<?php if ( strpos($value_config, ',Equipment Rate,') !== false && $field_sort_field == 'Equipment Rate' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Rate:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="0.01" name="rate" data-table="ticket_attached" data-id="<?= $equipment['id'] ?>" data-id-field="id" data-type="equipment" data-type-field="src_table" class="form-control" value="<?= $equipment['rate'] ?>">
						</div>
					</div>
				<?php } ?>
				<?php if ( strpos($value_config, ',Equipment Cost,') !== false && $field_sort_field == 'Equipment Cost' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Cost:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="0.01" readonly name="cost" class="form-control" value="<?= $equipment['rate'] * $equipment['hours_estimated'] ?>">
						</div>
					</div>
				<?php } ?>
				<?php if ( strpos($value_config, ',Equipment Status,') !== false && $field_sort_field == 'Equipment Status' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Status:</label>
						<div class="col-sm-8">
							<select name="status" data-table="ticket_attached" data-id="<?= $equipment['id'] ?>" data-id-field="id" data-type="equipment" data-type-field="src_table" class="chosen-select-deselect"><option></option>
								<option value='Active' <?php if ($equipment['status']=='Active') echo 'selected="selected"';?> >Active</option>
								<option value='In Service' <?php if ($equipment['status']=='In Service') echo 'selected="selected"';?> >In Service</option>
								<option value='Service Required' <?php if ($equipment['status']=='Service Required') echo 'selected="selected"';?> >Service Required</option>
								<option value='On Site' <?php if ($equipment['status']=='On Site') echo 'selected="selected"';?> >On Site</option>
								<option value='Inactive' <?php if ($equipment['status']=='Inactive') echo 'selected="selected"';?> >Inactive</option>
								<option value='Sold' <?php if ($equipment['status']=='Sold') echo 'selected="selected"';?> >Sold</option>
							</select>
						</div>
					</div>
				<?php } ?>
			<?php } else if ($equipment['equipmentid'] > 0) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Category:</label>
						<div class="col-sm-8"><?= $equipment['category'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Category', $equipment['category'] ]; ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Make:</label>
						<div class="col-sm-8"><?= $equipment['make'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Make', $equipment['make']]; ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Model:</label>
						<div class="col-sm-8"><?= $equipment['model'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Model', $equipment['model']]; ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Unit #:</label>
						<div class="col-sm-8"><?= $equipment['unit_number'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Unit #', $equipment['unit_number']]; ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Residue:</label>
						<div class="col-sm-8"><?= $equipment['residue'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Residue', $equipment['residue']]; ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Hours:</label>
						<div class="col-sm-8"><?= $equipment['hours_estimated'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Hours', $equipment['hours_estimated']]; ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Volume:</label>
						<div class="col-sm-8"><?= $equipment['qty'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Volume', $equipment['qty']]; ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Rate:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="0.01" name="rate" readonly class="form-control" value="<?= $equipment['rate'] ?>">
						</div>
					</div>
					<?php $pdf_contents[] = ['Rate', $equipment['rate']]; ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Cost:</label>
						<div class="col-sm-8"><?= $equipment['rate'] * $equipment['hours_estimated'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Cost', $equipment['rate'] * $equipment['hours_estimated']]; ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Status:</label>
						<div class="col-sm-8"><?= $equipment['status'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Status', $equipment['status']]; ?>
			<?php }
		}
		if($access_all > 0) { ?>
			<input type="hidden" name="deleted" data-table="ticket_attached" data-id="<?= $equipment['id'] ?>" data-id-field="id" data-type="equipment" data-type-field="src_table" value="0">
			<img class="inline-img pull-right" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
			<img class="inline-img pull-right" onclick="remMulti(this);" src="../img/remove.png">
			<div class="clearfix"></div>
		<?php } ?>
	</div>
<?php } while($equipment = mysqli_fetch_assoc($equipment_list)); ?>