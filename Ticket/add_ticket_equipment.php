<input type="hidden" name="equipmentid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $get_ticket['equipmentid'] ?>">
<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Equipment</h3>') ?>
<?php
if($ticketid > 0) {
	foreach(explode(',',$get_ticket['equipmentid']) as $ticket_equipmentid) {
		if($ticket_equipmentid > 0) {
			mysqli_query($dbc, "INSERT INTO `ticket_attached` (`src_table`, `ticketid`, `item_id`) SELECT 'equipment', '$ticketid', '$ticket_equipmentid' FROM (SELECT COUNT(*) rows FROM `ticket_attached` WHERE `src_table` = 'equipment' AND `ticketid` = '$ticketid' AND `item_id` = '$ticket_equipmentid') num WHERE num.rows=0");
		}
	}
}
$equipment_list = mysqli_query($dbc, "SELECT `ticket_attached`.`id`, `ticket_attached`.`item_id`, `ticket_attached`.`rate`, `ticket_attached`.`status`, `ticket_attached`.`qty`, `ticket_attached`.`hours_estimated`, `equipment`.* FROM `ticket_attached` LEFT JOIN `equipment` ON `ticket_attached`.`src_table`='equipment' AND `ticket_attached`.`item_id`=`equipment`.`equipmentid` WHERE `ticket_attached`.`item_id` > 0 AND `ticket_attached`.`src_table`='equipment' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily.(strpos($value_config, ',Equipment Limit One,') !== FALSE ? ' LIMIT 1' : ''));
if($_GET['new_ticket_calendar'] == 'true' && empty($_GET['edit']) && !($_GET['ticketid'] > 0)) {
	if(!empty($_GET['equipmentid'])) {
		$equipmentid = $_GET['equipmentid'];
	}
	$equipment_query = "SELECT '$equipmentid' `item_id`";
	$query = mysqli_query($dbc, $equipment_query);
	$equipment = mysqli_fetch_assoc($query);
} else {
	$equipment = mysqli_fetch_assoc($equipment_list);
}
do {
	$daily_rate = $hourly_rate = 0; ?>
	<div class="multi-block">
		<?php foreach($field_sort_order as $field_sort_field) {
			if($access_all > 0) { ?>
				<?php if ( strpos($value_config, ',Equipment Category,') !== false && $field_sort_field == 'Equipment Category' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Tab:</label>
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
								<?php $groups = mysqli_query($dbc, "SELECT `equipment`.`category`, `equipment`.`make`, `equipment`.`model`, `equipment`.`unit_number`, `equipment`.`equipmentid`,`hourly_rate`.`hourly`,`daily_rate`.`daily` FROM `equipment` LEFT JOIN (SELECT `description`,`item_id`,MAX(IFNULL(NULLIF(`cust_price`,0),`hourly`)) `hourly` FROM `company_rate_card` WHERE `tile_name`='Equipment' AND (`rate_card_types`='Hourly' OR `rate_card_types`!='Daily') AND `deleted`=0 AND '$rate_card' IN (`rate_card_name`,'') GROUP BY `description`,`item_id`) `hourly_rate` ON `equipment`.`equipmentid`=`hourly_rate`.`item_id` OR (`equipment`.`type`=`hourly_rate`.`description` AND `hourly_rate`.`item_id`=0) LEFT JOIN (SELECT `description`,`item_id`,MAX(IFNULL(NULLIF(`cust_price`,0),`daily`)) `daily` FROM `company_rate_card` WHERE `tile_name`='Equipment' AND (`rate_card_types`='Daily' OR `rate_card_types`!='Daily') AND `deleted`=0 AND '$rate_card' IN (`rate_card_name`,'') GROUP BY `description`,`item_id`) `daily_rate` ON `equipment`.`equipmentid`=`daily_rate`.`item_id` OR (`equipment`.`type`=`daily_rate`.`description` AND `daily_rate`.`item_id`=0) WHERE `deleted`=0 ORDER BY `category`, `make`, `model`, `unit_number`");
								while($units = mysqli_fetch_assoc($groups)) { ?>
									<option data-category="<?= $units['category'] ?>" data-make="<?= $units['make'] ?>" data-model="<?= $units['model'] ?>" data-hourly="<?= $units['hourly'] ?>" data-daily="<?= $units['daily'] ?>" <?= $equipment['item_id'] == $units['equipmentid'] ? 'selected' : '' ?> value="<?= $units['equipmentid'] ?>"><?= $units['unit_number'] ?></option>
									<?php if($equipment['item_id'] == $units['equipmentid']) {
										$daily_rate = $units['daily'];
										$hourly_rate = $units['hourly'];
									}
								} ?>
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
				<?php if ( strpos($value_config, ',Equipment Rate Options,') !== false && $field_sort_field == 'Equipment Rate Options' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Rate:</label>
						<div class="col-sm-8">
							<select name="rate" data-table="ticket_attached" data-id="<?= $equipment['id'] ?>" data-id-field="id" data-type="equipment" data-type-field="src_table" class="chosen-select-deselect"><option />
								<option <?= $equipment['rate'] == $daily_rate ? 'selected' : '' ?> data-type="daily" value="<?= $daily_rate ?>">Daily</option>
								<option <?= $equipment['rate'] == $hourly_rate ? 'selected' : '' ?> data-type="hourly" value="<?= $hourly_rate ?>">Hourly</option>
							</select>
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
				<?php if ( strpos($value_config, ',Equipment Category,') !== false && $field_sort_field == 'Equipment Category' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Tab:</label>
						<div class="col-sm-8"><?= $equipment['category'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Category', $equipment['category'] ]; ?>
				<?php } ?>
				<?php if ( strpos($value_config, ',Equipment Make,') !== false && $field_sort_field == 'Equipment Make' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Make:</label>
						<div class="col-sm-8"><?= $equipment['make'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Make', $equipment['make']]; ?>
				<?php } ?>
				<?php if ( strpos($value_config, ',Equipment Model,') !== false && $field_sort_field == 'Equipment Model' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Model:</label>
						<div class="col-sm-8"><?= $equipment['model'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Model', $equipment['model']]; ?>
				<?php } ?>
				<?php if ( strpos($value_config, ',Equipment Unit,') !== false && $field_sort_field == 'Equipment Unit' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Unit #:</label>
						<div class="col-sm-8"><?= $equipment['unit_number'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Unit #', $equipment['unit_number']]; ?>
				<?php } ?>
				<?php if ( strpos($value_config, ',Equipment Residue,') !== false && $field_sort_field == 'Equipment Residue' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Residue:</label>
						<div class="col-sm-8"><?= $equipment['residue'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Residue', $equipment['residue']]; ?>
				<?php } ?>
				<?php if ( strpos($value_config, ',Equipment Hours,') !== false && $field_sort_field == 'Equipment Hours' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Hours:</label>
						<div class="col-sm-8"><?= $equipment['hours_estimated'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Hours', $equipment['hours_estimated']]; ?>
				<?php } ?>
				<?php if ( strpos($value_config, ',Equipment Volume,') !== false && $field_sort_field == 'Equipment Volume' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Volume:</label>
						<div class="col-sm-8"><?= $equipment['qty'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Volume', $equipment['qty']]; ?>
				<?php } ?>
				<?php if ( strpos($value_config, ',Equipment Rate,') !== false && $field_sort_field == 'Equipment Rate' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Rate:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="0.01" name="rate" readonly class="form-control" value="<?= $equipment['rate'] ?>">
						</div>
					</div>
					<?php $pdf_contents[] = ['Rate', $equipment['rate']]; ?>
				<?php } ?>
				<?php if ( strpos($value_config, ',Equipment Cost,') !== false && $field_sort_field == 'Equipment Cost' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Cost:</label>
						<div class="col-sm-8"><?= $equipment['rate'] * $equipment['hours_estimated'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Cost', $equipment['rate'] * $equipment['hours_estimated']]; ?>
				<?php } ?>
				<?php if ( strpos($value_config, ',Equipment Status,') !== false && $field_sort_field == 'Equipment Status' ) { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Status:</label>
						<div class="col-sm-8"><?= $equipment['status'] ?></div>
					</div>
					<?php $pdf_contents[] = ['Status', $equipment['status']]; ?>
				<?php } ?>
			<?php }
		}
		if($access_all > 0 && strpos($value_config, ',Equipment Limit One,') === FALSE) { ?>
			<input type="hidden" name="deleted" data-table="ticket_attached" data-id="<?= $equipment['id'] ?>" data-id-field="id" data-type="equipment" data-type-field="src_table" value="0">
			<img class="inline-img pull-right" data-history-label="Equipment" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
			<img class="inline-img pull-right" data-history-label="Equipment" onclick="remMulti(this);" src="../img/remove.png">
			<div class="clearfix"></div>
		<?php } ?>
	</div>
<?php } while($equipment = mysqli_fetch_assoc($equipment_list)); ?>