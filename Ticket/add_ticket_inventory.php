<script type="text/javascript">
$(document).on('change','select.inventory_part',function() { changeInventoryPart(this); });
function changeInventoryPart(sel) {
	var block = $(sel).closest('.multi-block');
	$(block).find('[name="inv_category"]').val($(sel).data('category')).trigger('change.select2');
	$(block).find('[name="inv_sub"]').val($(sel).data('category')).trigger('change.select2');
	$(block).find('[name="item_id"]').val($(sel).val()).trigger('change');
}
</script>
<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Inventory</h3>') ?>
<?php if(strpos($value_config,',Inventory Basic Inline,') !== FALSE) { ?>
	<?php if(strpos($value_config,',Inventory Basic Category,') !== FALSE) { ?>
		<div class="col-sm-3 hide-titles-mob text-center">Tab</div>
		<?php $sub_cats = mysqli_query($dbc, "SELECT `category`, `sub_category` FROM `inventory` WHERE IFNULL(`sub_category`,'') != '' GROUP BY `sub_category` ORDER BY `sub_category`");
		if(mysqli_num_rows($sub_cats) > 0) { ?>
			<div class="col-sm-1 hide-titles-mob text-center">Sub-Tab</div>
		<?php }
	} ?>
	<?php if(strpos($value_config,',Inventory Basic Part,') !== FALSE) { ?>
		<div class="col-sm-2 hide-titles-mob text-center">Part #</div>
	<?php } ?>
	<?php if(strpos($value_config,',Inventory Basic Inventory,') !== FALSE) { ?>
		<div class="col-sm-3 hide-titles-mob text-center">Name</div>
	<?php } ?>
	<?php if(strpos($value_config,',Inventory Basic Price,') !== FALSE) { ?>
		<div class="col-sm-1 hide-titles-mob text-center">Price</div>
	<?php } ?>
	<?php if(strpos($value_config,',Inventory Basic Quantity,') !== FALSE) { ?>
		<div class="col-sm-1 hide-titles-mob text-center">Quantity</div>
	<?php } ?>
	<?php if(strpos($value_config,',Inventory Basic Total,') !== FALSE) { ?>
		<div class="col-sm-1 hide-titles-mob text-center">Total</div>
	<?php } ?>
	<div class="col-sm-1 hide-titles-mob text-center"></div>
	<div class="clearfix"></div>
<?php } ?>
<?php $inventory_list = mysqli_query($dbc, "SELECT `ticket_attached`.`id`, `ticket_attached`.`item_id`, IFNULL(NULLIF(`ticket_attached`.`rate`,0),`inventory`.`final_retail_price`) `rate`, `ticket_attached`.`qty`, `ticket_attached`.`received`, `ticket_attached`.`used`, `ticket_attached`.`description`, `ticket_attached`.`status`, `ticket_attached`.`po_line`, `ticket_attached`.`piece_num`, `ticket_attached`.`piece_type`, `ticket_attached`.`used`, `ticket_attached`.`weight`, `ticket_attached`.`weight_units`, `ticket_attached`.`dimensions`, `ticket_attached`.`dimension_units`, `ticket_attached`.`discrepancy`, `ticket_attached`.`backorder`, `ticket_attached`.`position`, `ticket_attached`.`notes`, `ticket_attached`.`contact_info`, `inventory`.`category`, `inventory`.`sub_category`, `inventory`.`part_no`, `inventory`.`final_retail_price`, `inventory`.`preferred_price`, `inventory`.`web_price`, `inventory`.`sell_price`, `inventory`.`admin_price`, `inventory`.`wholesale_price`, `inventory`.`commercial_price`, `inventory`.`client_price`, `inventory`.`clearance_price`, `inventory`.`distributor_price`, `inventory`.`unit_price` FROM `ticket_attached` LEFT JOIN `inventory` ON `ticket_attached`.`item_id`=`inventory`.`inventoryid` WHERE `ticket_attached`.`src_table`='inventory' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily); // Removed Validation clause: (`ticket_attached`.`item_id` > 0 OR `ticket_attached`.`description` != '' OR `ticket_attached`.`weight` > 0) AND
$inventory = mysqli_fetch_assoc($inventory_list);
$piece_types = array_filter(explode(',',get_config($dbc, 'piece_types')));
$inventory_units = $dbc->query("SELECT `category`, `sub_category`,  `part_no`, `final_retail_price`, `preferred_price`, `web_price`, `sell_price`, `admin_price`, `wholesale_price`, `commercial_price`, `client_price`, `clearance_price`, `distributor_price`, `unit_price`, CONCAT(IFNULL(`product_name`,''),' ',IFNULL(`name`,'')) `label`, `inventoryid` FROM `inventory` WHERE `deleted`=0 ORDER BY `category`, `sub_category`, `label`")->fetch_all(MYSQLI_ASSOC);
do {
	if($inventory['dimensions'] == '') {
		$inventory['dimensions'] = ' x x ';
	}
	$inventory_price = 0;
	if($access_all > 0) { ?>
		<div class="multi-block">
			<?php foreach($field_sort_order as $field_sort_field) { ?>
				<?php if(strpos($value_config,',Inventory Basic Inline,') !== FALSE && $field_sort_field == 'Inventory Basic Inline') { ?>
					<?php if(strpos($value_config,',Inventory Basic Category,') !== FALSE) { ?>
						<div class="select-div">
							<div class="col-sm-3">
								<label class="control-label show-on-mob">Tab:</label>
								<select name="inv_category" data-placeholder="Select a Category" class="chosen-select-deselect"><option></option>
									<?php $groups = mysqli_query($dbc, "SELECT `category` FROM `inventory` WHERE `deleted`=0 GROUP BY `category` ORDER BY `category`");
									while($category = mysqli_fetch_assoc($groups)) { ?>
										<option <?= $inventory['category'] == $category['category'] ? 'selected' : '' ?> value="<?= $category['category'] ?>"><?= $category['category'] ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<?php $sub_cats = mysqli_query($dbc, "SELECT `category`, `sub_category` FROM `inventory` WHERE IFNULL(`sub_category`,'') != '' GROUP BY `sub_category` ORDER BY `sub_category`");
						if(mysqli_num_rows($sub_cats) > 0) { ?>
							<div class="select-div">
								<div class="col-sm-1">
									<label class="control-label show-on-mob">Sub-Tab:</label>
									<select name="inv_sub" data-placeholder="Select a Sub-Tab" class="chosen-select-deselect"><option></option>
										<?php while($sub_cat = mysqli_fetch_assoc($sub_cats)) { ?>
											<option <?= $inventory['sub_category'] == $sub_cat['sub_category'] ? 'selected' : '' ?> data-category="<?= $sub_cat['category'] ?>" style="<?= $inventory['category'] != '' && $sub_cat['category'] != $inventory['category'] ? 'display:none;' : '' ?>" value="<?= $sub_cat['sub_category'] ?>"><?= $sub_cat['sub_category'] ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						<?php }
					} ?>
					<?php if(strpos($value_config,',Inventory Basic Part,') !== FALSE) { ?>
						<div class="col-sm-2">
							<label class="control-label show-on-mob">Part #:</label>
							<select name="part" data-placeholder="Select a Part #" class="chosen-select-deselect inventory_part"><option></option>
								<?php foreach($inventory_units as $units) {
									if($units['part_no'] != '') { ?>
										<option data-category="<?= $units['category'] ?>" data-sub-category="<?= $units['sub_category'] ?>" value="<?= $units['inventoryid'] ?>" <?= $inventory['item_id'] == $units['inventoryid'] ? 'selected' : '' ?>><?= $units['part_no'] ?></option>
									<?php } ?>
								<?php } ?>
								<option value="MANUAL">Add New</option>
							</select>
						</div>
					<?php } ?>
					<?php if(strpos($value_config,',Inventory Basic Inventory,') !== FALSE) { ?>
						<div class="col-sm-3 select-div">
							<label class="control-label show-on-mob">Name:</label>
							<select name="item_id" data-placeholder="Select an Inventory Item" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="chosen-select-deselect"><option></option>
								<?php foreach($inventory_units as $units) { ?>
									<option data-category="<?= $units['category'] ?>" data-sub-category="<?= $units['sub_category'] ?>" data-price='<?= json_encode(array_values(array_filter([$units['final_retail_price'],$units['unit_price'],$units['web_price'],$units['sell_price'],$units['preferred_price'],$units['commercial_price'],$units['client_price'],$units['distributor_price'],$units['wholesale_price'],$units['clearance_price'],$units['admin_price']],function($num) { return $num > 0 || $el < 0; }))) ?>' <?= $inventory['item_id'] == $units['inventoryid'] ? 'selected' : '' ?> data-part="<?= $inventory['part_no'] ?>" value="<?= $units['inventoryid'] ?>"><?= (strpos($value_config,',Inventory Detail Category,') !== FALSE ? '' : ($units['category'] != '' ? $units['category'].($units['sub_category'] != '' ? ' '.$units['sub_category'] : '').': ' : '')).$units['label'].(strpos($value_config,',Inventory Basic Part,') === FALSE ? ' '.$units['part_no'] : '') ?></option>
								<?php } ?>
								<option value="MANUAL">Add New</option>
							</select>
						</div>
						<div class="col-sm-3 manual-div" style="display:none;">
							<input name="name" data-table="inventory" data-id="" data-id-field="inventoryid" class="col-sm-6 form-control" placeholder="Description">
							<input name="part_no" data-table="inventory" data-id="" data-id-field="inventoryid" class="col-sm-6 form-control" placeholder="Part Number">
						</div>
					<?php } ?>
					<?php if(strpos($value_config,',Inventory Basic Price,') !== FALSE) { ?>
						<div class="col-sm-1">
							<label class="control-label show-on-mob">Price:</label>
							<?php $price_list = array_filter([$inventory['final_retail_price'],$inventory['unit_price'],$inventory['web_price'],$inventory['sell_price'],$inventory['preferred_price'],$inventory['commercial_price'],$inventory['client_price'],$inventory['distributor_price'],$inventory['wholesale_price'],$inventory['clearance_price'],$inventory['admin_price']],function($num) { return $num > 0 || $el < 0; }); ?>
							<select <?= count($price_list) > 1 ? '' : 'disabled' ?> data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" name="rate" class="form-control" value="<?= $inventory['rate'] ?>">
								<?php foreach($price_list as $price_point) { ?>
									<option <?= $inventory['rate'] == $price_point ? 'selected' : '' ?> value="<?= $price_point ?>"><?= $price_point ?></option>
								<?php } ?>
							</select>
						</div>
					<?php } ?>
					<?php if(strpos($value_config,',Inventory Basic Quantity,') !== FALSE) { ?>
						<div class="col-sm-1">
							<label class="control-label show-on-mob">Quantity:</label>
							<input type="number" min=0 step="1" name="qty" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="form-control" value="<?= $inventory['qty'] ?>">
						</div>
					<?php } ?>
					<?php if(strpos($value_config,',Inventory Basic Total,') !== FALSE) { ?>
						<div class="col-sm-1">
							<label class="control-label show-on-mob">Total:</label>
							<input type="text" readonly name="total" class="form-control" value="<?= $inventory['qty']*$inventory['rate'] ?>">
						</div>
					<?php } ?>
					<div class="col-sm-1">
						<input type="hidden" name="deleted" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" value="0">
						<img class="inline-img pull-right" data-history-label="Inventory" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
						<img class="inline-img pull-right" data-history-label="Inventory" onclick="remMulti(this);" src="../img/remove.png">
					</div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Inline,') === FALSE && strpos($value_config,',Inventory Basic Category,') !== FALSE && $field_sort_field == 'Inventory Basic Category') { ?>
					<div class="form-group select-div">
						<label class="control-label col-sm-4">Tab:</label>
						<div class="col-sm-8">
							<select name="inv_category" data-placeholder="Select a Tab" class="chosen-select-deselect"><option></option>
								<?php $groups = mysqli_query($dbc, "SELECT `category` FROM `inventory` WHERE `deleted`=0 GROUP BY `category` ORDER BY `category`");
								while($category = mysqli_fetch_assoc($groups)) { ?>
									<option <?= $inventory['category'] == $category['category'] ? 'selected' : '' ?> value="<?= $category['category'] ?>"><?= $category['category'] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<?php $sub_cats = mysqli_query($dbc, "SELECT `category`, `sub_category` FROM `inventory` WHERE IFNULL(`sub_category`,'') != '' GROUP BY `sub_category` ORDER BY `sub_category`");
					if(mysqli_num_rows($sub_cats) > 0) { ?>
						<div class="form-group select-div">
							<label class="control-label col-sm-4">Sub-Tab:</label>
							<div class="col-sm-8">
								<select name="inv_sub" data-placeholder="Select a Sub-Tab" class="chosen-select-deselect"><option></option>
									<?php while($sub_cat = mysqli_fetch_assoc($sub_cats)) { ?>
										<option <?= $inventory['sub_category'] == $sub_cat['sub_category'] ? 'selected' : '' ?> data-category="<?= $sub_cat['category'] ?>" style="<?= $inventory['category'] != '' && $sub_cat['category'] != $inventory['category'] ? 'display:none;' : '' ?>" value="<?= $sub_cat['sub_category'] ?>"><?= $sub_cat['sub_category'] ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					<?php }
				} ?>
				<?php if(strpos($value_config,',Inventory Basic Inline,') === FALSE && strpos($value_config,',Inventory Basic Inventory,') !== FALSE && $field_sort_field == 'Inventory Basic Inventory') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Inventory:</label>
						<div class="col-sm-7 select-div">
							<select name="item_id" data-placeholder="Select an Inventory Item" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="chosen-select-deselect"><option></option>
								<?php foreach($inventory_units as $units) { ?>
									<option data-category="<?= $units['category'] ?>" data-sub-category="<?= $units['sub_category'] ?>" data-price='<?= json_encode(array_values(array_filter([$units['final_retail_price'],$units['unit_price'],$units['web_price'],$units['sell_price'],$units['preferred_price'],$units['commercial_price'],$units['client_price'],$units['distributor_price'],$units['wholesale_price'],$units['clearance_price'],$units['admin_price']],function($num) { return $num > 0 || $el < 0; }))) ?>' data-part="<?= $inventory['part_no'] ?>" <?= $inventory['item_id'] == $units['inventoryid'] ? 'selected' : '' ?> value="<?= $units['inventoryid'] ?>"><?= (strpos($value_config,',Inventory Detail Category,') !== FALSE ? '' : ($units['category'] != '' ? $units['category'].($units['sub_category'] != '' ? ' '.$units['sub_category'] : '').': ' : '')).$units['label'].(strpos($value_config,',Inventory Basic Part,') === FALSE ? ' '.$units['part_no'] : '') ?></option>
								<?php } ?>
								<option value="MANUAL">Add New</option>
							</select>
						</div>
						<div class="col-sm-7 manual-div" style="display:none;">
							<input name="name" data-table="inventory" data-id="" data-id-field="inventoryid" class="col-sm-6 form-control" placeholder="Description">
							<input name="part_no" data-table="inventory" data-id="" data-id-field="inventoryid" class="col-sm-6 form-control" placeholder="Part Number">
						</div>
						<div class="col-sm-1">
							<input type="hidden" name="deleted" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" value="0">
							<img class="inline-img pull-right" data-history-label="Inventory" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
							<img class="inline-img pull-right" data-history-label="Inventory" onclick="remMulti(this);" src="../img/remove.png">
							<a href="" onclick="$(this).closest('.form-group').find('select').val('MANUAL').change(); return false;"><img class="inline-img pull-left" data-history-label="New Inventory" src="../img/icons/ROOK-add-icon.png"></a>
						</div>
						<div class="clearfix"></div>
					</div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Inline,') === FALSE && strpos($value_config,',Inventory Basic Part,') !== FALSE && $field_sort_field == 'Inventory Basic Part') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Part #:</label>
						<div class="col-sm-8">
							<select name="part" data-placeholder="Select a Part #" class="chosen-select-deselect"><option></option>
								<?php foreach($inventory_units as $units) {
									if($units['part_no'] != '') { ?>
										<option data-category="<?= $units['category'] ?>" data-sub-category="<?= $units['sub_category'] ?>" value="<?= $units['inventoryid'] ?>" <?= $inventory['item_id'] == $units['inventoryid'] ? 'selected' : '' ?>><?= $units['part_no'] ?></option>
									<?php } ?>
								<?php } ?>
								<option value="MANUAL">Add New</option>
							</select>
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Inline,') === FALSE && strpos($value_config,',Inventory Basic Price,') !== FALSE && $field_sort_field == 'Inventory Basic Price') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Price:</label>
						<div class="col-sm-8">
							<?php $price_list = array_filter([$inventory['final_retail_price'],$inventory['unit_price'],$inventory['web_price'],$inventory['sell_price'],$inventory['preferred_price'],$inventory['commercial_price'],$inventory['client_price'],$inventory['distributor_price'],$inventory['wholesale_price'],$inventory['clearance_price'],$inventory['admin_price']],function($num) { return $num > 0 || $el < 0; }); ?>
							<select <?= count($price_list) > 1 ? '' : 'disabled' ?> data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" name="rate" class="form-control" value="<?= $inventory['rate'] ?>">
								<?php foreach($price_list as $price_point) { ?>
									<option <?= $inventory['rate'] == $price_point ? 'selected' : '' ?> value="<?= $price_point ?>"><?= $price_point ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Inline,') === FALSE && strpos($value_config,',Inventory Basic Quantity,') !== FALSE && $field_sort_field == 'Inventory Basic Quantity') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Quantity:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="1" name="qty" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="form-control" value="<?= $inventory['qty'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Inline,') === FALSE && strpos($value_config,',Inventory Basic Total,') !== FALSE && $field_sort_field == 'Inventory Basic Total') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Total:</label>
						<div class="col-sm-8">
							<input type="text" readonly name="total" class="form-control" value="<?= $inventory['qty']*$inventory['rate'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Piece Type,') !== FALSE && $field_sort_field == 'Inventory Basic Piece Type') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Piece Tab:</label>
						<div class="col-sm-8">
							<?php if(count($piece_types) > 0) { ?>
								<select name="piece_type" data-placeholder="Select Type" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="chosen-select-deselect"><option></option>
									<?php foreach($piece_types as $piece_type_name) { ?>
										<option <?= $inventory['piece_type'] == $piece_type_name ? 'selected' : '' ?> value="<?= $piece_type_name ?>"><?= $piece_type_name ?></option>
									<?php } ?>
									<?php if(!in_array($inventory['piece_type'],$piece_types)) { ?>
										<option selected value="<?= $inventory['piece_type'] ?>"><?= $inventory['piece_type'] ?></option>
									<?php } ?>
								</select>
							<?php } else { ?>
								<input type="text" name="piece_type" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="form-control" value="<?= $inventory['piece_type]'] ?>">
							<?php } ?>
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic PO Line,') !== FALSE && $field_sort_field == 'Inventory Basic PO Line') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">PO Line #:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="1" name="po_line" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="form-control" value="<?= $inventory['po_line'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Vendor,') !== FALSE && $field_sort_field == 'Inventory Basic Vendor') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Vendor:</label>
						<div class="col-sm-8">
							<input type="text" name="contact_info" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="form-control" value="<?= $inventory['contact_info'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Weight,') !== FALSE && $field_sort_field == 'Inventory Basic Weight') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Weight:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="1" name="weight" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="form-control" value="<?= $inventory['weight'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Units,') !== FALSE && $field_sort_field == 'Inventory Basic Units') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Unit of Weight:</label>
						<div class="col-sm-8">
							<select name="weight_units" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="chosen-select-deselect">
								<option></option>
								<option <?= $inventory['weight_units'] == 'kg' ? 'selected' : '' ?> value="kg">kg</option>
								<option <?= $inventory['weight_units'] == 'lbs' ? 'selected' : '' ?> value="lbs">lbs</option>
							</select>
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Dimensions,') !== FALSE && $field_sort_field == 'Inventory Basic Dimensions') { ?>
					<?php $inv_dim_units = explode('#*#',$inventory['dimension_units']);
					foreach(explode('#*#',$inventory['dimensions']) as $id => $inv_dimension) {
						$inv_dimensions = explode('x',$inv_dimension);
						$inv_dim_unit_list = explode('x',$inv_dim_units[$id]); ?>
						<div class="form-group multi_dimensions" <?= strpos($inventory['dimensions'],'#*#') !== FALSE ? '' : 'style="display:none;"' ?>>
							<label class="control-label col-sm-4">Piece Dimension (LxWxH):</label>
							<div class="col-sm-8">
								<input type="hidden" name="dimensions" data-concat="#*#" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" value="<?= $inv_dimension ?>">
								<input type="hidden" name="dimension_units" data-concat="#*#" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" value="<?= $inv_dim_units[$id] ?>">
								<div class="col-sm-2">
									<input placeholder="Length" type="number" min=0 value="<?= trim(explode('x', $inv_dimension)[0]) ?>" class="dim_l form-control">
								</div>
								<div class="col-sm-2">
									<select data-placeholder="Units" class="dimunit_l chosen-select-deselect">
										<option></option>
										<option <?= in_array(strtolower($inv_dim_unit_list[0]),['mm','mms','millimeter','millimetre','millimeters','millimetres']) ? 'selected' : '' ?> value="mm">mm</option>
										<option <?= in_array(strtolower($inv_dim_unit_list[0]),['cm','cms','centimeter','centimetre','centimeters','centimetres']) ? 'selected' : '' ?> value="cm">cm</option>
										<option <?= in_array(strtolower($inv_dim_unit_list[0]),['m','meter','metre','meters','metres']) ? 'selected' : '' ?> value="m">m</option>
										<option <?= in_array(strtolower($inv_dim_unit_list[0]),['in','inch','inches']) ? 'selected' : '' ?> value="in">in</option>
										<option <?= in_array(strtolower($inv_dim_unit_list[0]),['ft','feet','foot']) ? 'selected' : '' ?> value="ft">ft</option>
									</select>
								</div>
								<div class="col-sm-2">
									<input placeholder="Width" type="number" min=0 value="<?= trim(explode('x', $inv_dimension)[1]) ?>" class="dim_w form-control">
								</div>
								<div class="col-sm-2">
									<select data-placeholder="Units" class="dimunit_w chosen-select-deselect">
										<option></option>
										<option <?= in_array(strtolower($inv_dim_unit_list[1]),['mm','mms','millimeter','millimetre','millimeters','millimetres']) ? 'selected' : '' ?> value="mm">mm</option>
										<option <?= in_array(strtolower($inv_dim_unit_list[1]),['cm','cms','centimeter','centimetre','centimeters','centimetres']) ? 'selected' : '' ?> value="cm">cm</option>
										<option <?= in_array(strtolower($inv_dim_unit_list[1]),['m','meter','metre','meters','metres']) ? 'selected' : '' ?> value="m">m</option>
										<option <?= in_array(strtolower($inv_dim_unit_list[1]),['in','inch','inches']) ? 'selected' : '' ?> value="in">in</option>
										<option <?= in_array(strtolower($inv_dim_unit_list[1]),['ft','feet','foot']) ? 'selected' : '' ?> value="ft">ft</option>
									</select>
								</div>
								<div class="col-sm-2">
									<input placeholder="Height" type="number" min=0 value="<?= trim(explode('x', $inv_dimension)[2]) ?>" class="dim_h form-control">
								</div>
								<div class="col-sm-2">
									<select data-placeholder="Units" class="dimunit_h chosen-select-deselect">
										<option></option>
										<option <?= in_array(strtolower($inv_dim_unit_list[2]),['mm','mms','millimeter','millimetre','millimeters','millimetres']) ? 'selected' : '' ?> value="mm">mm</option>
										<option <?= in_array(strtolower($inv_dim_unit_list[2]),['cm','cms','centimeter','centimetre','centimeters','centimetres']) ? 'selected' : '' ?> value="cm">cm</option>
										<option <?= in_array(strtolower($inv_dim_unit_list[2]),['m','meter','metre','meters','metres']) ? 'selected' : '' ?> value="m">m</option>
										<option <?= in_array(strtolower($inv_dim_unit_list[2]),['in','inch','inches']) ? 'selected' : '' ?> value="in">in</option>
										<option <?= in_array(strtolower($inv_dim_unit_list[2]),['ft','feet','foot']) ? 'selected' : '' ?> value="ft">ft</option>
									</select>
								</div>
							</div>
						</div>
					<?php } ?>
					<div class="form-group single_dimensions" <?= strpos($inventory['dimensions'],'#*#') !== FALSE ? 'style="display:none;"' : '' ?>>
						<label class="control-label col-sm-4">Piece Dimension (LxWxH):</label>
						<div class="col-sm-8">
							<input type="text" name="dimensions<?= strpos($inventory['dimensions'],'#*#') !== FALSE ? '_halt_add' : '' ?>" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="form-control" value="<?= $inventory['dimensions'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Dimension Units,') !== FALSE && $field_sort_field == 'Inventory Basic Dimension Units') { ?>
					<div class="form-group single_dimensions" <?= strpos($inventory['dimensions'],'#*#') !== FALSE ? 'style="display:none;"' : '' ?>>
						<label class="control-label col-sm-4">Dimension Units:</label>
						<div class="col-sm-8">
							<select name="dimension_units<?= strpos($inventory['dimensions'],'#*#') !== FALSE ? '_halt_add' : '' ?>" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="chosen-select-deselect">
								<option></option>
								<option <?= in_array(strtolower($inventory['dimension_units']),['mm','mms','millimeter','millimetre','millimeters','millimetres']) ? 'selected' : '' ?> value="mm">mm</option>
								<option <?= in_array(strtolower($inventory['dimension_units']),['cm','cms','centimeter','centimetre','centimeters','centimetres']) ? 'selected' : '' ?> value="cm">cm</option>
								<option <?= in_array(strtolower($inventory['dimension_units']),['m','meter','metre','meters','metres']) ? 'selected' : '' ?> value="m">m</option>
								<option <?= in_array(strtolower($inventory['dimension_units']),['in','inch','inches']) ? 'selected' : '' ?> value="in">in</option>
								<option <?= in_array(strtolower($inventory['dimension_units']),['ft','feet','foot']) ? 'selected' : '' ?> value="ft">ft</option>
							</select>
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Used,') !== FALSE && $field_sort_field == 'Inventory Basic Used') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Picked:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="1" name="used" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="form-control" value="<?= $inventory['used'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Received,') !== FALSE && $field_sort_field == 'Inventory Basic Received') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Received:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="1" name="received" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="form-control" value="<?= $inventory['received'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Discrepancy,') !== FALSE && $field_sort_field == 'Inventory Basic Discrepancy') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Discrepancy:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="1" name="discrepancy" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="form-control" value="<?= $inventory['discrepancy'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Back Order,') !== FALSE && $field_sort_field == 'Inventory Basic Back Order') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Back Order:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="1" name="backorder" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="form-control" value="<?= $inventory['backorder'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Location,') !== FALSE && $field_sort_field == 'Inventory Basic Location') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Location:</label>
						<div class="col-sm-8">
							<input type="text" name="description" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="form-control" value="<?= $inventory['description'] ?>">
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
			<?php } ?>
		</div>
	<?php } else { ?>
		<?php foreach($field_sort_order as $field_sort_field) { ?>
			<?php if(strpos($value_config,',Inventory Basic Inline,') !== FALSE && $field_sort_field == 'Inventory Basic Inline') { ?>
				<?php if(strpos($value_config,',Inventory Basic Category,') !== FALSE) { ?>
					<div class="select-div">
						<div class="col-sm-3">
							<label class="control-label show-on-mob">Tab:</label>
							<?= $inventory['category'] ?>
						</div>
					</div>
					<?php $pdf_contents[] = ['Category', $inventory['category']]; ?>
					<?php $sub_cats = mysqli_query($dbc, "SELECT `category`, `sub_category` FROM `inventory` WHERE IFNULL(`sub_category`,'') != '' GROUP BY `sub_category` ORDER BY `sub_category`");
					if(mysqli_num_rows($sub_cats) > 0) { ?>
						<div class="select-div">
							<div class="col-sm-1">
								<label class="control-label show-on-mob">Sub-Tab:</label>
								<?= $inventory['sub_category'] ?>
							</div>
						</div>
						<?php $pdf_contents[] = ['Sub-Category', $inventory['sub_category']]; ?>
					<?php }
				} ?>
				<?php if(strpos($value_config,',Inventory Basic Part,') !== FALSE) { ?>
					<div class="col-sm-2">
						<label class="control-label show-on-mob">Part #:</label>
						<?= $inventory['part_no'] ?>
					</div>
					<?php $pdf_contents[] = ['Part #', $inventory['part_no']]; ?>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Inventory,') !== FALSE) {
					$inv_label = mysql_fetch_assoc(mysqli_query($dbc, "SELECT `category`, `sub_category`, CONCAT(IFNULL(`product_name`,''),' ',IFNULL(`name`,''),' ',IFNULL(`part_no`,'')) `label`, `inventoryid` FROM `inventory` WHERE `deleted`=0 AND `inventoryid`='".$inventory['item_id']."'"))['label']; ?>
					<div class="col-sm-3 select-div">
						<label class="control-label show-on-mob">Inventory:</label>
						<?= $inv_label ?>
					</div>
					<?php $pdf_contents[] = ['Inventory', $inv_label]; ?>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Price,') !== FALSE) { ?>
					<div class="col-sm-1">
						<label class="control-label show-on-mob">Price:</label>
						<?= $inventory['rate'] ?>
					</div>
					<?php $pdf_contents[] = ['Price', $inventory['rate']]; ?>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Quantity,') !== FALSE) { ?>
					<div class="col-sm-1">
						<label class="control-label show-on-mob">Quantity:</label>
						<?= $inventory['qty'] ?>
					</div>
					<?php $pdf_contents[] = ['Quantity', $inventory['qty']]; ?>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory Basic Total,') !== FALSE) { ?>
					<div class="col-sm-1">
						<label class="control-label show-on-mob">Total:</label>
						<?= $inventory['qty']*$inventory['rate'] ?>
					</div>
					<?php $pdf_contents[] = ['Total', $inventory['qty']*$inventory['rate']]; ?>
				<?php } ?>
				<div class="col-sm-1">
				</div>
			<?php } ?>
			<?php if(strpos($value_config,',Inventory Basic Inline,') === FALSE && strpos($value_config,',Inventory Basic Category,') !== FALSE && $field_sort_field == 'Inventory Basic Category') { ?>
				<div class="form-group select-div">
					<label class="control-label col-sm-4">Tab:</label>
					<div class="col-sm-8">
							<?= $inventory['category'] ?>
					</div>
				</div>
				<?php $pdf_contents[] = ['Category', $inventory['category']]; ?>
				<?php $sub_cats = mysqli_query($dbc, "SELECT `category`, `sub_category` FROM `inventory` WHERE IFNULL(`sub_category`,'') != '' GROUP BY `sub_category` ORDER BY `sub_category`");
				if(mysqli_num_rows($sub_cats) > 0) { ?>
					<div class="form-group select-div">
						<label class="control-label col-sm-4">Sub-Tab:</label>
						<div class="col-sm-8">
							<?= $inventory['sub_category'] ?>
						</div>
					</div>
					<?php $pdf_contents[] = ['Sub-Tab', $inventory['sub_category']]; ?>
				<?php }
			} ?>
			<?php if(strpos($value_config,',Inventory Basic Inline,') === FALSE && strpos($value_config,',Inventory Basic Inventory,') !== FALSE && $field_sort_field == 'Inventory Basic Inventory') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Inventory:</label>
					<div class="col-sm-8">
						<?= mysql_fetch_assoc(mysqli_query($dbc, "SELECT `category`, `sub_category`, CONCAT(IFNULL(`product_name`,''),' ',IFNULL(`name`,''),' ',IFNULL(`part_no`,'')) `label`, `inventoryid` FROM `inventory` WHERE `deleted`=0 AND `inventoryid`='".$inventory['item_id']."'"))['label'] ?>
					</div>
					<div class="clearfix"></div>
				</div>
				<?php $pdf_contents[] = ['Inventory', mysql_fetch_assoc(mysqli_query($dbc, "SELECT `category`, `sub_category`, CONCAT(IFNULL(`product_name`,''),' ',IFNULL(`name`,''),' ',IFNULL(`part_no`,'')) `label`, `inventoryid` FROM `inventory` WHERE `deleted`=0 AND `inventoryid`='".$inventory['item_id']."'"))['label']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Inventory Basic Inline,') === FALSE && strpos($value_config,',Inventory Basic Part,') !== FALSE && $field_sort_field == 'Inventory Basic Part') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Part #:</label>
					<div class="col-sm-8">
						<?= $inventory['part_no'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Part #', $inventory['part_no']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Inventory Basic Inline,') === FALSE && strpos($value_config,',Inventory Basic Price,') !== FALSE && $field_sort_field == 'Inventory Basic Price') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Price:</label>
					<div class="col-sm-8">
						<?= $inventory['rate'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Price', $inventory['rate']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Inventory Basic Inline,') === FALSE && strpos($value_config,',Inventory Basic Quantity,') !== FALSE && $field_sort_field == 'Inventory Basic Quantity') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Quantity:</label>
					<div class="col-sm-8">
						<?= $inventory['qty'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Quantity', $inventory['qty']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Inventory Basic Inline,') === FALSE && strpos($value_config,',Inventory Basic Total,') !== FALSE && $field_sort_field == 'Inventory Basic Total') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Total:</label>
					<div class="col-sm-8">
						<?= $inventory['qty']*$inventory['rate'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Total', $inventory['qty']*$inventory['rate']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Inventory Basic Piece Type,') !== FALSE && $field_sort_field == 'Inventory Basic Piece Type') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Piece Tab:</label>
					<div class="col-sm-8">
						<?= $inventory['piece_type'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Piece Type', $inventory['piece_type']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Inventory Basic PO Line,') !== FALSE && $field_sort_field == 'Inventory Basic PO Line') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">PO Line #:</label>
					<div class="col-sm-8">
						<?= $inventory['po_line'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['PO Line #', $inventory['po_line']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Inventory Basic Vendor,') !== FALSE && $field_sort_field == 'Inventory Basic Vendor') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Vendor:</label>
					<div class="col-sm-8">
						<?= $inventory['contact_info'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Vendor', $inventory['contact_info']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Inventory Basic Weight,') !== FALSE && $field_sort_field == 'Inventory Basic Weight') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Weight:</label>
					<div class="col-sm-8">
						<?= $inventory['weight'].$inventory['weight_units'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Weight', $inventory['weight'].$inventory['weight_units']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Inventory Basic Dimensions,') !== FALSE && $field_sort_field == 'Inventory Basic Dimensions') { ?>
				<label class="control-label col-sm-4">Piece Dimension (LxWxH):</label>
				<?php $inv_dim_units = explode('#*#',$inventory['dimension_units']);
				$echo_dimensions = '';
				foreach(explode('#*#',$inventory['dimensions']) as $id => $inv_dimension) {
					$inv_dimensions = explode('x',$inv_dimension);
					$inv_dim_unit_list = explode('x',$inv_dim_units[$id]);
					$echo_dimensions .= $inv_dimension[0].$inv_dim_unit_list[0].'x'.$inv_dimension[1].$inv_dim_unit_list[1].'x'.$inv_dimension[2].$inv_dim_unit_list[2].'<br />';
				}
				echo $echo_dimensions ?>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Piece Dimension (LxWxH)', (1 === preg_match('~[0-9]~', $echo_dimensions) ? $echo_dimensions : '')]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Inventory Basic Used,') !== FALSE && $field_sort_field == 'Inventory Basic Used') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Picked:</label>
					<div class="col-sm-8">
						<?= $inventory['used'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Picked', $inventory['used']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Inventory Basic Received,') !== FALSE && $field_sort_field == 'Inventory Basic Received') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Received:</label>
					<div class="col-sm-8">
						<?= $inventory['received'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Received', $inventory['received']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Inventory Basic Discrepancy,') !== FALSE && $field_sort_field == 'Inventory Basic Discrepancy') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Discrepancy:</label>
					<div class="col-sm-8">
						<?= $inventory['discrepancy'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Discrepancy', $inventory['discrepancy']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Inventory Basic Back Order,') !== FALSE && $field_sort_field == 'Inventory Basic Back Order') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Back Order:</label>
					<div class="col-sm-8">
						<?= $inventory['backorder'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Back Order', $inventory['backorder']]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Inventory Basic Location,') !== FALSE && $field_sort_field == 'Inventory Basic Location') { ?>
				<div class="form-group">
					<label class="control-label col-sm-4">Location:</label>
					<div class="col-sm-8">
						<?= $inventory['description'] ?>
					</div>
				</div>
				<div class="clearfix"></div>
				<?php $pdf_contents[] = ['Location', $inventory['description']]; ?>
			<?php } ?>
		<?php } ?>
	<?php }
} while($inventory = mysqli_fetch_assoc($inventory_list)); ?>
<?php if(strpos($value_config,',Inventory Basic Billing,') !== FALSE) { ?>
	<div class="inventory_billing_summary"></div>
<?php } ?>