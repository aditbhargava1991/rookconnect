<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Materials</h3>') ?>
<?php $material_list = mysqli_query($dbc, "SELECT `ticket_attached`.`id`, `ticket_attached`.`item_id`, `ticket_attached`.`rate`, `ticket_attached`.`qty`, `ticket_attached`.`volume`, `ticket_attached`.`description`, `ticket_attached`.`status`, `material`.`category`, `material`.`sub_category`, `material`.`name` FROM `ticket_attached` LEFT JOIN `material` ON `ticket_attached`.`item_id`=`material`.`materialid` WHERE (`ticket_attached`.`item_id` > 0 OR `ticket_attached`.`description` != '') AND `ticket_attached`.`src_table`='material' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily);
$material = mysqli_fetch_assoc($material_list);
$cols = (strpos($value_config,',Material Category,') !== FALSE ? 2 : 0)
 + (strpos($value_config,',Material Subcategory,') !== FALSE ? 2 : 0)
 + (strpos($value_config,',Material Quantity,') !== FALSE ? 2 : 0)
 + (strpos($value_config,',Material Volume,') !== FALSE ? 2 : 0)
 + (strpos($value_config,',Material Rate,') !== FALSE ? 2 : 0); ?>
<?php if(strpos($value_config,',Material Inline,') !== FALSE) { ?>
	<?php if(strpos($value_config,',Material Category,') !== FALSE) { ?>
		<div class="col-sm-2 hide-titles-mob text-center">Tab</div>
	<?php } ?>
	<?php if(strpos($value_config,',Material Subcategory,') !== FALSE) { ?>
		<div class="col-sm-2 hide-titles-mob text-center">Sub-Tab</div>
	<?php } ?>
	<?php if(strpos($value_config,',Material Type,') !== FALSE) { ?>
		<div class="col-sm-<?= 11 - $cols ?> hide-titles-mob text-center">Tab</div>
	<?php } ?>
	<?php if(strpos($value_config,',Material Manual,') !== FALSE) { ?>
		<div class="col-sm-<?= 11 - $cols ?> hide-titles-mob text-center">Material</div>
	<?php } ?>
	<?php if(strpos($value_config,',Material Quantity,') !== FALSE) { ?>
		<div class="col-sm-2 hide-titles-mob text-center">Quantity</div>
	<?php } ?>
	<?php if(strpos($value_config,',Material Volume,') !== FALSE) { ?>
		<div class="col-sm-2 hide-titles-mob text-center">Volume</div>
	<?php } ?>
	<?php if(strpos($value_config,',Material Rate,') !== FALSE) { ?>
		<div class="col-sm-1 hide-titles-mob text-center">Unit Price</div>
		<div class="col-sm-1 hide-titles-mob text-center">15% Markup</div>
	<?php } ?>
	<div class="col-sm-1 hide-titles-mob text-center"></div>
	<div class="clearfix"></div>
<?php } ?>
<?php do {
	if($access_all > 0) { ?>
		<div class="multi-block">
			<?php foreach($field_sort_order as $field_sort_field) { ?>
				<?php if(strpos($value_config,',Material Inline,') !== FALSE) { ?>
					<?php if(strpos($value_config,',Material Category,') !== FALSE && $field_sort_field == 'Material Category') { ?>
						<div class="select-div" style="<?= $material['description'] != '' ? 'display:none;' : '' ?>">
							<label class="control-label show-on-mob col-sm-4">Tab:</label>
							<div class="col-sm-2">
								<select name="mat_category" class="chosen-select-deselect"><option></option>
									<?php $groups = mysqli_query($dbc, "SELECT `category` FROM `material` WHERE `deleted`=0 GROUP BY `category` ORDER BY `category`");
									while($category = mysqli_fetch_assoc($groups)) { ?>
										<option <?= $material['category'] == $category['category'] ? 'selected' : '' ?> value="<?= $category['category'] ?>"><?= $category['category'] ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					<?php } ?>
					<?php if(strpos($value_config,',Material Subcategory,') !== FALSE && $field_sort_field == 'Material Subcategory') { ?>
						<div class="select-div" style="<?= $material['description'] != '' ? 'display:none;' : '' ?>">
							<label class="control-label show-on-mob col-sm-4">Sub-Tab:</label>
							<div class="col-sm-2">
								<select name="mat_sub" class="chosen-select-deselect"><option></option>
									<?php $groups = mysqli_query($dbc, "SELECT `sub_category` FROM `material` WHERE `deleted`=0 GROUP BY `sub_category` ORDER BY `sub_category`");
									while($sub_cat = mysqli_fetch_assoc($groups)) { ?>
										<option <?= $material['sub_category'] == $sub_cat['sub_category'] ? 'selected' : '' ?> value="<?= $sub_cat['sub_category'] ?>"><?= $sub_cat['sub_category'] ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					<?php } ?>
					<?php if(strpos($value_config,',Material Type,') !== FALSE && $field_sort_field == 'Material Type') { ?>
						<label class="control-label show-on-mob col-sm-4">Tab:</label>
						<div class="col-sm-<?= 11 - $cols ?> select-div" style="<?= $material['description'] != '' ? 'display:none;' : '' ?>">
							<select name="item_id" data-table="ticket_attached" data-id="<?= $material['id'] ?>" data-id-field="id" data-type="material" data-type-field="src_table" data-auto-checkin="<?= strpos($value_config, ',Auto Check In Materials,') !== FALSE ? 1 : 0 ?>" data-auto-checkout="<?= strpos($value_config, ',Auto Check Out Materials,') !== FALSE ? 1 : 0 ?>" class="chosen-select-deselect"><option></option>
								<?php $groups = mysqli_query($dbc, "SELECT `category`, `sub_category`, `name`, `materialid` FROM `material` WHERE `deleted`=0 ORDER BY `category`, `sub_category`, `name`");
								while($units = mysqli_fetch_assoc($groups)) { ?>
									<option data-category="<?= $units['category'] ?>" data-sub-category="<?= $units['sub_category'] ?>" <?= $material['item_id'] == $units['materialid'] ? 'selected' : '' ?> value="<?= $units['materialid'] ?>"><?= $units['name'] ?></option>
								<?php } ?>
								<option value="MANUAL">Add Custom</option>
							</select>
						</div>
						<div class="col-sm-<?= 11 - $cols ?> manual-div" style="<?= $material['description'] != '' ? '' : 'display:none;' ?>">
							<input name="description" data-table="ticket_attached" data-id="<?= $material['id'] ?>" data-id-field="id" data-type="material" data-type-field="src_table" data-auto-checkin="<?= strpos($value_config, ',Auto Check In Materials,') !== FALSE ? 1 : 0 ?>" data-auto-checkout="<?= strpos($value_config, ',Auto Check Out Materials,') !== FALSE ? 1 : 0 ?>" class="form-control" value="<?= $material['description'] ?>">
						</div>
					<?php } ?>
					<?php if(strpos($value_config,',Material Manual,') !== FALSE && $field_sort_field == 'Material Manual') { ?>
						<label class="control-label show-on-mob col-sm-4">Material:</label>
						<div class="col-sm-<?= 11 - $cols ?>">
							<input name="description" data-table="ticket_attached" data-id="<?= $material['id'] ?>" data-id-field="id" data-type="material" data-type-field="src_table" data-auto-checkin="<?= strpos($value_config, ',Auto Check In Materials,') !== FALSE ? 1 : 0 ?>" data-auto-checkout="<?= strpos($value_config, ',Auto Check Out Materials,') !== FALSE ? 1 : 0 ?>" class="form-control" value="<?= empty($material['description']) ? $material['name'] : $material['description'] ?>">
						</div>
					<?php } ?>
					<?php if(strpos($value_config,',Material Quantity,') !== FALSE && $field_sort_field == 'Material Quantity') { ?>
						<label class="control-label show-on-mob col-sm-4">Quantity:</label>
						<div class="col-sm-2">
							<input type="number" min=0 step="<?= !empty(get_config($dbc, 'ticket_material_increment')) ? get_config($dbc, 'ticket_material_increment') : 'any' ?>" name="qty" data-table="ticket_attached" data-id="<?= $material['id'] ?>" data-id-field="id" data-type="material" data-type-field="src_table" class="form-control" value="<?= $material['qty'] ?>" onchange="materialRate(this);">
						</div>
					<?php } ?>
					<?php if(strpos($value_config,',Material Volume,') !== FALSE && $field_sort_field == 'Material Volume') { ?>
						<label class="control-label show-on-mob col-sm-4">Volume:</label>
						<div class="col-sm-2">
							<input type="number" min=0 step="any" name="volume" data-table="ticket_attached" data-id="<?= $material['id'] ?>" data-id-field="id" data-type="material" data-type-field="src_table" class="form-control" value="<?= $material['volume'] ?>" onchange="materialRate(this);">
						</div>
					<?php } ?>
					<?php if(strpos($value_config,',Material Rate,') !== FALSE && $field_sort_field == 'Material Rate') { ?>
						<label class="control-label show-on-mob col-sm-4">Unit Price:</label>
						<div class="col-sm-1">
							<input type="number" min=0 step="any" name="rate" data-table="ticket_attached" data-id="<?= $material['id'] ?>" data-id-field="id" data-type="material" data-type-field="src_table" class="form-control" value="<?= $material['rate'] ?>" onchange="materialRate(this);">
						</div>
						<label class="control-label show-on-mob col-sm-4">Total (with 15% Markup):</label>
						<div class="col-sm-1">
							<input type="number" readonly name="material_markup" class="form-control" value="<?= number_format($material['qty'] * $material['rate'] * 1.15,2) ?>">
						</div>
					<?php } ?>
				<?php } else { ?>
					<?php if(strpos($value_config,',Material Category,') !== FALSE && $field_sort_field == 'Material Category') { ?>
						<div class="form-group select-div" style="<?= $material['description'] != '' ? 'display:none;' : '' ?>">
							<label class="control-label col-sm-4">Tab:</label>
							<div class="col-sm-8">
								<select name="mat_category" class="chosen-select-deselect"><option></option>
									<?php $groups = mysqli_query($dbc, "SELECT `category` FROM `material` WHERE `deleted`=0 GROUP BY `category` ORDER BY `category`");
									while($category = mysqli_fetch_assoc($groups)) { ?>
										<option <?= $material['category'] == $category['category'] ? 'selected' : '' ?> value="<?= $category['category'] ?>"><?= $category['category'] ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					<?php } ?>
					<?php if(strpos($value_config,',Material Subcategory,') !== FALSE && $field_sort_field == 'Material Subcategory') { ?>
						<div class="form-group select-div" style="<?= $material['description'] != '' ? 'display:none;' : '' ?>">
							<label class="control-label col-sm-4">Sub-Tab:</label>
							<div class="col-sm-8">
								<select name="mat_sub" class="chosen-select-deselect"><option></option>
									<?php $groups = mysqli_query($dbc, "SELECT `sub_category` FROM `material` WHERE `deleted`=0 GROUP BY `sub_category` ORDER BY `sub_category`");
									while($sub_cat = mysqli_fetch_assoc($groups)) { ?>
										<option <?= $material['sub_category'] == $sub_cat['sub_category'] ? 'selected' : '' ?> value="<?= $sub_cat['sub_category'] ?>"><?= $sub_cat['sub_category'] ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					<?php } ?>
					<?php if(strpos($value_config,',Material Type,') !== FALSE && $field_sort_field == 'Material Type') { ?>
						<div class="form-group">
							<label class="control-label col-sm-4">Tab:</label>
							<div class="col-sm-8 select-div" style="<?= $material['description'] != '' ? 'display:none;' : '' ?>">
								<select name="item_id" data-table="ticket_attached" data-id="<?= $material['id'] ?>" data-id-field="id" data-type="material" data-type-field="src_table" data-auto-checkin="<?= strpos($value_config, ',Auto Check In Materials,') !== FALSE ? 1 : 0 ?>" data-auto-checkout="<?= strpos($value_config, ',Auto Check Out Materials,') !== FALSE ? 1 : 0 ?>" class="chosen-select-deselect"><option></option>
									<?php $groups = mysqli_query($dbc, "SELECT `category`, `sub_category`, `name`, `materialid` FROM `material` WHERE `deleted`=0 ORDER BY `category`, `sub_category`, `name`");
									while($units = mysqli_fetch_assoc($groups)) { ?>
										<option data-category="<?= $units['category'] ?>" data-sub-category="<?= $units['sub_category'] ?>" <?= $material['item_id'] == $units['materialid'] ? 'selected' : '' ?> value="<?= $units['materialid'] ?>"><?= $units['name'] ?></option>
									<?php } ?>
									<option value="MANUAL">Add Custom</option>
								</select>
							</div>
							<div class="col-sm-8 manual-div" style="<?= $material['description'] != '' ? '' : 'display:none;' ?>">
								<input name="description" data-table="ticket_attached" data-id="<?= $material['id'] ?>" data-id-field="id" data-type="material" data-type-field="src_table" class="form-control" value="<?= $material['description'] ?>">
							</div>
							<div class="clearfix"></div>
						</div>
					<?php } ?>
					<?php if(strpos($value_config,',Material Manual,') !== FALSE && $field_sort_field == 'Material Manual') { ?>
						<div class="form-group">
							<label class="control-label col-sm-4">Material:</label>
							<div class="col-sm-8">
								<input name="description" data-table="ticket_attached" data-id="<?= $material['id'] ?>" data-id-field="id" data-type="material" data-type-field="src_table" class="form-control" value="<?= empty($material['description']) ? $material['name'] : $material['description'] ?>">
							</div>
							<div class="clearfix"></div>
						</div>
					<?php } ?>
					<?php if(strpos($value_config,',Material Quantity,') !== FALSE && $field_sort_field == 'Material Quantity') { ?>
						<div class="form-group">
							<label class="control-label col-sm-4">Quantity:</label>
							<div class="col-sm-8">
								<input type="number" min=0 step="<?= !empty(get_config($dbc, 'ticket_material_increment')) ? get_config($dbc, 'ticket_material_increment') : 'any' ?>" name="qty" data-table="ticket_attached" data-id="<?= $material['id'] ?>" data-id-field="id" data-type="material" data-type-field="src_table" class="form-control" value="<?= $material['qty'] ?>" onchange="materialRate(this);">
							</div>
						</div>
						<div class="clearfix"></div>
					<?php } ?>
					<?php if(strpos($value_config,',Material Volume,') !== FALSE && $field_sort_field == 'Material Volume') { ?>
						<div class="form-group">
							<label class="control-label col-sm-4">Volume:</label>
							<div class="col-sm-8">
								<input type="number" min=0 step="any" name="volume" data-table="ticket_attached" data-id="<?= $material['id'] ?>" data-id-field="id" data-type="material" data-type-field="src_table" class="form-control" value="<?= $material['volume'] ?>" onchange="materialRate(this);">
							</div>
						</div>
						<div class="clearfix"></div>
					<?php } ?>
					<?php if(strpos($value_config,',Material Rate,') !== FALSE && $field_sort_field == 'Material Rate') { ?>
						<div class="form-group">
							<label class="control-label col-sm-4">Unit Price:</label>
							<div class="col-sm-8">
								<input type="number" min=0 step="any" name="rate" data-table="ticket_attached" data-id="<?= $material['id'] ?>" data-id-field="id" data-type="material" data-type-field="src_table" class="form-control" value="<?= $material['rate'] ?>" onchange="materialRate(this);">
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-4">Total (with 15% Markup):</label>
							<div class="col-sm-8">
								<input type="number" readonly name="material_markup" class="form-control" value="<?= number_format($material['qty'] * $material['rate'] * 1.15,2) ?>">
							</div>
							<div class="clearfix"></div>
						</div>
					<?php } ?>
				<?php } ?>
			<?php } ?>
			<div class="form-group pull-right col-sm-1">
				<input type="hidden" name="deleted" data-table="ticket_attached" data-id="<?= $material['id'] ?>" data-id-field="id" data-type="material" data-type-field="src_table" value="0">
				<img class="inline-img pull-right" data-history-label="Material" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
				<img class="inline-img pull-right" data-history-label="Material" onclick="remMulti(this);" src="../img/remove.png">
			</div>
			<div class="clearfix"></div>
		</div>
		<hr>
	<?php } else if($material['materialid'] > 0) { ?>
		<div class="multi-block">
			<?php if(strpos($value_config,',Material Inline,') !== FALSE) { ?>

			<?php } else { ?>
				<?php foreach($field_sort_order as $field_sort_field) { ?>
					<?php if(strpos($value_config,',Material Category,') !== FALSE && $field_sort_field == 'Material Category') { ?>
						<div class="form-group select-div" style="<?= $material['description'] != '' ? 'display:none;' : '' ?>">
							<label class="control-label col-sm-4">Tab:</label>
							<div class="col-sm-8"><?= $material['category'] ?></div>
						</div>
						<?php $pdf_contents[] = ['Category', $material['category']]; ?>
						<div class="form-group select-div" style="<?= $material['description'] != '' ? 'display:none;' : '' ?>">
							<label class="control-label col-sm-4">Sub-Tab:</label>
							<div class="col-sm-8"><?= $material['sub_category'] ?></div>
						</div>
						<?php $pdf_contents[] = ['Sub-Tab', $material['sub_category']]; ?>
					<?php } ?>
					<?php if(strpos($value_config,',Material Type,') !== FALSE && $field_sort_field == 'Material Type') { ?>
						<div class="form-group">
							<label class="control-label col-sm-4">Tab:</label>
							<div class="col-sm-8"><?= $material['description'] != '' ? $material['description'] : $material['name'] ?></div>
						</div>
						<?php $pdf_contents[] = ['Type', $material['description'] != '' ? $material['description'] : $material['name']]; ?>
					<?php } ?>
					<?php if(strpos($value_config,',Material Manual,') !== FALSE && $field_sort_field == 'Material Manual') { ?>
						<div class="form-group">
							<label class="control-label col-sm-4">Material:</label>
							<div class="col-sm-8"><?= $material['description'] != '' ? $material['description'] : $material['name'] ?></div>
						</div>
						<?php $pdf_contents[] = ['Type', $material['description'] != '' ? $material['description'] : $material['name']]; ?>
					<?php } ?>
					<?php if(strpos($value_config,',Material Quantity,') !== FALSE && $field_sort_field == 'Material Quantity') { ?>
						<div class="form-group">
							<label class="control-label col-sm-4">Quantity:</label>
							<div class="col-sm-8">
								<?= round($material['qty'],4) ?>
							</div>
						</div>
						<div class="clearfix"></div>
						<?php $pdf_contents[] = ['Quantity', $material['qty']]; ?>
					<?php } ?>
					<?php if(strpos($value_config,',Material Volume,') !== FALSE && $field_sort_field == 'Material Volume') { ?>
						<div class="form-group">
							<label class="control-label col-sm-4">Volume:</label>
							<div class="col-sm-8">
								<?= round($material['volume'],4) ?>
							</div>
						</div>
						<div class="clearfix"></div>
						<?php $pdf_contents[] = ['Volume', $material['volume']]; ?>
					<?php } ?>
					<?php if(strpos($value_config,',Material Rate,') !== FALSE && $field_sort_field == 'Material Rate') { ?>
						<div class="form-group">
							<label class="control-label col-sm-4">Unit Price:</label>
							<div class="col-sm-8">
								<?= number_format($material['rate'],2) ?>
							</div>
							<div class="clearfix"></div>
						</div>
						<?php $pdf_contents[] = ['Unit Price', $material['rate']]; ?>
						<div class="form-group">
							<label class="control-label col-sm-4">Total (with 15% Markup):</label>
							<div class="col-sm-8">
								<?= number_format($material['qty'] * $material['rate'] * 1.15,2) ?>
							</div>
							<div class="clearfix"></div>
						</div>
						<?php $pdf_contents[] = ['Total (with 15% Markup) Price', number_format($material['qty'] * $material['rate'] * 1.15,2)]; ?>
					<?php } ?>
				<?php } ?>
			<?php } ?>
		</div>
	<?php }
} while($material = mysqli_fetch_assoc($material_list)); ?>