<?php if(!isset($general_item)) {
	$general_list = $dbc->query("SELECT `id`, `piece_type`, `piece_num` FROM `ticket_attached` WHERE `src_table`='inventory_general' AND `deleted`=0 AND `ticketid`='$ticketid'");
	$general_item = $general_list->fetch_assoc();
	$no_fetch = false;
} else {
	$no_fetch = true;
	$renamed_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($ticket_type) ? 'tickets' : 'tickets_'.$ticket_type)."' AND `accordion` = 'Inventory Detail'"))['accordion_name'];
} ?>
<h3><?= ($no_fetch && !isset($general_item['pallet']) ? 'Shipment Piece '.$general_line_item.': '.$general_item['piece_type'].' ' : '').(!empty($renamed_accordion) ? $renamed_accordion.'' : 'Detailed Cargo / Inventory Information') ?></h3>
<?php $piece_types = array_filter(explode(',',get_config($dbc, 'piece_types')));
$include_blanks = true;
$row_item = 0;
$inventory_fields = explode(',',$dbc->query("SELECT GROUP_CONCAT(DISTINCT `inventory` SEPARATOR ',') `fields` FROM `field_config_inventory`")->fetch_assoc()['fields']);
do {
	if(strpos($value_config,',Inventory General Weight Convert KG to LB,') !== FALSE) {
		$general_inventory['weight_units'] = explode('#*#',$general_inventory['weight_units']);
		$general_inventory['weight'] = explode('#*#',$general_inventory['weight']);
		foreach($general_inventory['weight'] as $id => $general_weight) {
			if($general_inventory['weight_units'][$id] == 'kg' && $general_weight > 0) {
				$general_inventory['weight'][$id] = number_format(($general_inventory['weight'][$id]*2.20462262185),2);
				$general_inventory['weight_units'][$id] = 'lbs';
			}
		}
		$general_inventory['weight_units'] = implode('#*#',$general_inventory['weight_units']);
		$general_inventory['weight'] = implode('#*#',$general_inventory['weight']);
	}
	if(!($general_item['piece_num'] > 0)) {
		$general_item['piece_num'] = 1;
	}
	for($gen_i = 0; $gen_i < $general_item['piece_num']; $gen_i++) { ?>
		<?= $row_item++ > 0 ? '<hr />' : '' ?>
		<div class="tab-section" id="tab_section_detail_inventory_<?= config_safe_str($general_item['piece_type'].'_'.$i) ?>">
			<?php if(!$no_fetch) { ?>
				<h4>Detailed Inventory - <?= $general_item['piece_type'].($general_item['piece_num'] > 1 ? " #".($i+1) : '') ?></h4>
			<?php } ?>
			<?php $inventory_list = mysqli_query($dbc, "SELECT `ticket_attached`.`id`, `ticket_attached`.`item_id`, `ticket_attached`.`siteid`, `ticket_attached`.`rate`, `ticket_attached`.`qty`, `ticket_attached`.`received`, `ticket_attached`.`used`, `ticket_attached`.`description`, `ticket_attached`.`status`, `ticket_attached`.`po_num`, `ticket_attached`.`po_line`, `ticket_attached`.`line_id`, `ticket_attached`.`piece_num`, `ticket_attached`.`piece_type`, `ticket_attached`.`used`, `ticket_attached`.`weight`, `ticket_attached`.`weight_units`, `ticket_attached`.`net_weight`, `ticket_attached`.`net_units`, `ticket_attached`.`gross_weight`, `ticket_attached`.`gross_units`, `ticket_attached`.`dimensions`, `ticket_attached`.`dimension_units`, `ticket_attached`.`discrepancy`, `ticket_attached`.`backorder`, `ticket_attached`.`position`, `ticket_attached`.`notes`, `ticket_attached`.`contact_info`, `inventory`.`category`, `inventory`.`sub_category`, `inventory`.`name`, `inventory`.`product_name`, `inventory`.`brand`, `inventory`.`description`, `inventory`.`code`, `inventory`.`id_number`, `inventory`.`item_sku`, `inventory`.`part_no`, `inventory`.`pallet` FROM `ticket_attached` LEFT JOIN `inventory` ON `ticket_attached`.`item_id`=`inventory`.`inventoryid` WHERE `ticket_attached`.`src_table`='inventory' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0 AND `ticket_attached`.`piece_num` = '".$i."' AND ".(isset($general_item['pallet']) ? "'".$general_item['pallet']."'=IFNULL(NULLIF(`inventory`.`pallet`,''),'*UNDEFINED*')" : "`ticket_attached`.`line_id` IN ('".$general_item['id']."'".($include_blanks ? ",'0'" : '').')').$query_daily.(strpos($value_config,',Inventory Detail PO Sort,') !== FALSE ? ' ORDER BY LPAD(`ticket_attached`.`po_num`, 20, 0), LPAD(`ticket_attached`.`po_line`, 100, 0)' : ''));
			$include_blanks = false;
			$inventory = mysqli_fetch_assoc($inventory_list);
			$line_item = 0;
			do {
				if(strpos($value_config,',Inventory General Weight Convert KG to LB,') !== FALSE) {
					if($inventory['weight_units'] == 'kg' && $inventory['weight'] > 0) {
						$inventory['weight'] = number_format(($inventory['weight']*2.20462262185),2);
						$inventory['weight_units'] = 'lbs';
					}
					if($inventory['net_units'] == 'kg' && $inventory['net_weight'] > 0) {
						$inventory['net_weight'] = number_format(($inventory['net_weight']*2.20462262185),2);
						$inventory['net_units'] = 'lbs';
					}
					if($inventory['gross_units'] == 'kg' && $inventory['gross_weight'] > 0) {
						$inventory['gross_weight'] = number_format(($inventory['gross_weight']*2.20462262185),2);
						$inventory['gross_units'] = 'lbs';
					}
				}
				if($line_item++ > 0) {
					echo '<hr />';
				}
				if($inventory['dimensions'] == '') {
					$inventory['dimensions'] = ' x x ';
				}
				if($access_all > 0) { ?>
					<div class="multi-block">
						<?php foreach($field_sort_order as $field_sort_field) { ?>
							<?php if(strpos($value_config,',Inventory Detail Category,') !== FALSE && $field_sort_field == 'Inventory Detail Category') { ?>
								<div class="form-group select-div" <?= $general_inventory['description'] == '' || $inventory['category'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Tab:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<select name="inv_category" data-placeholder="Select a Tab" class="chosen-select-deselect"><option></option>
											<?php $groups = mysqli_query($dbc, "SELECT `category` FROM `inventory` WHERE `deleted`=0 GROUP BY `category` ORDER BY `category`");
											while($category = mysqli_fetch_assoc($groups)) { ?>
												<option <?= $inventory['category'] == $category['category'] ? 'selected' : '' ?> value="<?= $category['category'] ?>"><?= $category['category'] ?></option>
											<?php } ?>
										</select>
									</div></div>
								</div>
								<?php $sub_cats = mysqli_query($dbc, "SELECT `category`, `sub_category` FROM `inventory` WHERE IFNULL(`sub_category`,'') != '' GROUP BY `sub_category` ORDER BY `sub_category`");
								if(mysqli_num_rows($sub_cats) > 0) { ?>
									<div class="form-group select-div" <?= $general_inventory['description'] == '' || $inventory['sub_category'] != '' ? '' : 'style="display:none;"' ?>>
										<label class="control-label col-sm-4">Sub-Tab:</label>
										<div class="col-sm-8"><div class="col-sm-12">
											<select name="inv_sub" data-placeholder="Select a Sub-Category" class="chosen-select-deselect"><option></option>
												<?php while($sub_cat = mysqli_fetch_assoc($sub_cats)) { ?>
													<option <?= $inventory['sub_category'] == $sub_cat['sub_category'] ? 'selected' : '' ?> data-category="<?= $sub_cat['category'] ?>" style="<?= $inventory['category'] != '' && $sub_cat['category'] != $inventory['category'] ? 'display:none;' : '' ?>" value="<?= $sub_cat['sub_category'] ?>"><?= $sub_cat['sub_category'] ?></option>
												<?php } ?>
											</select>
										</div></div>
									</div>
								<?php }
							} ?>
							<?php if(strpos($value_config,',Inventory Detail Unique,') !== FALSE && $field_sort_field == 'Inventory Detail Unique') { ?>
								<input type="hidden" name="item_id" value="<?= $inventory['item_id'] ?>" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num">
								<?php if(in_array('Name',$inventory_fields)) { ?>
									<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['name'] != '' ? '' : 'style="display:none;"' ?>>
										<label class="control-label col-sm-4">Inventory:</label>
										<div class="col-sm-8"><div class="col-sm-12">
											<input name="name" value="<?= $inventory['name'] ?>" data-table="inventory" data-id="<?= $inventory['item_id'] ?>" data-id-field="inventoryid" class="form-control" placeholder="Enter Name...">
										</div></div>
									</div>
								<?php } ?>
								<?php if(in_array('Product Name',$inventory_fields)) { ?>
									<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['product_name'] != '' ? '' : 'style="display:none;"' ?>>
										<label class="control-label col-sm-4">Product Name:</label>
										<div class="col-sm-8"><div class="col-sm-12">
											<input name="product_name" value="<?= $inventory['product_name'] ?>" data-table="inventory" data-id="<?= $inventory['item_id'] ?>" data-id-field="inventoryid" class="form-control" placeholder="Enter Product Name...">
										</div></div>
									</div>
								<?php } ?>
								<?php if(in_array('Brand',$inventory_fields)) { ?>
									<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['brand'] != '' ? '' : 'style="display:none;"' ?>>
										<label class="control-label col-sm-4">Brand:</label>
										<div class="col-sm-8"><div class="col-sm-12">
											<input name="brand" value="<?= $inventory['brand'] ?>" data-table="inventory" data-id="<?= $inventory['item_id'] ?>" data-id-field="inventoryid" class="form-control" placeholder="Enter Brand...">
										</div></div>
									</div>
								<?php } ?>
								<?php if(in_array('Category',$inventory_fields)) { ?>
									<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['category'] != '' ? '' : 'style="display:none;"' ?>>
										<label class="control-label col-sm-4">Tab:</label>
										<div class="col-sm-8"><div class="col-sm-12">
											<select name="category" data-placeholder="Select a Tab..." data-table="inventory" data-id="<?= $inventory['item_id'] ?>" data-id-field="inventoryid" class="chosen-select-deselect"><option></option>
												<?php $groups = mysqli_query($dbc, "SELECT `category` FROM `inventory` WHERE `deleted`=0 GROUP BY `category` ORDER BY `category`");
												while($category = mysqli_fetch_assoc($groups)) { ?>
													<option <?= $inventory['category'] == $category['category'] ? 'selected' : '' ?> value="<?= $category['category'] ?>"><?= $category['category'] ?></option>
												<?php } ?>
											</select>
										</div></div>
									</div>
								<?php } ?>
								<?php if(in_array('Description',$inventory_fields)) { ?>
									<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['description'] != '' ? '' : 'style="display:none;"' ?>>
										<label class="control-label col-sm-4">Description:</label>
										<div class="col-sm-8"><div class="col-sm-12">
											<textarea name="description" data-table="inventory" data-id="<?= $inventory['item_id'] ?>" data-id-field="inventoryid" class="form-control"><?= $inventory['description'] ?></textarea>
										</div></div>
									</div>
								<?php } ?>
								<?php if(in_array('Code',$inventory_fields)) { ?>
									<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['code'] != '' ? '' : 'style="display:none;"' ?>>
										<label class="control-label col-sm-4">Code:</label>
										<div class="col-sm-8"><div class="col-sm-12">
											<input name="code" value="<?= $inventory['code'] ?>" data-table="inventory" data-id="<?= $inventory['item_id'] ?>" data-id-field="inventoryid" class="form-control" placeholder="Enter Code...">
										</div></div>
									</div>
								<?php } ?>
								<?php if(in_array('ID #',$inventory_fields)) { ?>
									<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['id_number'] != '' ? '' : 'style="display:none;"' ?>>
										<label class="control-label col-sm-4">ID #:</label>
										<div class="col-sm-8"><div class="col-sm-12">
											<input name="id_number" value="<?= $inventory['id_number'] ?>" data-table="inventory" data-id="<?= $inventory['item_id'] ?>" data-id-field="inventoryid" class="form-control" placeholder="Enter ID #...">
										</div></div>
									</div>
								<?php } ?>
								<?php if(in_array('Item SKU',$inventory_fields)) { ?>
									<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['item_sku'] != '' ? '' : 'style="display:none;"' ?>>
										<label class="control-label col-sm-4">Item SKU:</label>
										<div class="col-sm-8"><div class="col-sm-12">
											<input name="item_sku" value="<?= $inventory['item_sku'] ?>" data-table="inventory" data-id="<?= $inventory['item_id'] ?>" data-id-field="inventoryid" class="form-control" placeholder="Enter Item SKU...">
										</div></div>
									</div>
								<?php } ?>
								<?php if(in_array('Part #',$inventory_fields)) { ?>
									<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['part_no'] != '' ? '' : 'style="display:none;"' ?>>
										<label class="control-label col-sm-4">Part #:</label>
										<div class="col-sm-8"><div class="col-sm-12">
											<input name="part_no" value="<?= $inventory['part_no'] ?>" data-table="inventory" data-id="<?= $inventory['item_id'] ?>" data-id-field="inventoryid" class="form-control" placeholder="Enter Part #...">
										</div></div>
									</div>
								<?php } ?>
								<?php if(in_array('Pallet Num',$inventory_fields)) { ?>
									<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['pallet'] != '' || isset($general_item['pallet']) ? '' : 'style="display:none;"' ?>>
										<label class="control-label col-sm-4">Pallet #:</label>
										<div class="col-sm-8"><div class="col-sm-12 pallet_select">
											<select class="chosen-select-deselect" name="pallet" value="<?= $inventory['pallet'] ?>" data-table="inventory" data-id="<?= $inventory['item_id'] ?>" data-id-field="inventoryid" data-placeholder="Select Pallet #..." onchange="if(this.value == 'ADD_NEW') { $(this).closest('.form-group').find('.pallet_select').hide(); $(this).closest('.form-group').find('.pallet_input').show().find('input').focus(); return false; }"><option />
												<?php if(!isset($pallet_list)) {
													$pallet_list = $dbc->query("SELECT `pallet` FROM `inventory` WHERE `deleted`=0 AND IFNULL(`pallet`,'') != '' GROUP BY `pallet` ORDER BY `pallet`")->fetch_all(MYSQLI_ASSOC);
												}
												foreach($pallet_list as $pallet_num) { ?>
													<option <?= $pallet_num['pallet'] == $inventory['pallet'] ? 'selected' : '' ?> value="<?= $pallet_num['pallet'] ?>"><?= $pallet_num['pallet'] ?></option>
												<?php } ?>
												<option value="ADD_NEW">New Pallet #</option>
											</select>
										</div>
										<div class="col-sm-12 pallet_input" style="display:none;">
											<input class="form-control" name="pallet" value="<?= $inventory['pallet'] ?>" data-table="inventory" data-id="<?= $inventory['item_id'] ?>" data-id-field="inventoryid" placeholder="Enter Pallet #...">
										</div></div>
									</div>
								<?php } ?>
							<?php } else if($field_sort_order == 'Inventory Detail Category') { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['item_id'] > 0 ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Inventory:</label>
									<div class="col-sm-8">
											<div class="col-sm-11 select-div">
												<select name="item_id" data-placeholder="Select an Inventory Item" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="chosen-select-deselect"><option></option>
													<?php $groups = mysqli_query($dbc, "SELECT `category`, `sub_category`, CONCAT(IFNULL(`product_name`,''),' ',IFNULL(`name`,''),' ',IFNULL(`part_no`,'')) `label`, `inventoryid` FROM `inventory` WHERE `deleted`=0 ORDER BY `category`, `sub_category`, `label`");
													while($units = mysqli_fetch_assoc($groups)) { ?>
														<option data-category="<?= $units['category'] ?>" data-sub-category="<?= $units['sub_category'] ?>" <?= $inventory['item_id'] == $units['inventoryid'] ? 'selected' : '' ?> value="<?= $units['inventoryid'] ?>"><?= (strpos($value_config,',Inventory Detail Category,') !== FALSE ? '' : ($units['category'] != '' ? $units['category'].($units['sub_category'] != '' ? ' '.$units['sub_category'] : '').': ' : '')).$units['label'] ?></option>
													<?php } ?>
													<option value="MANUAL">Add New</option>
												</select>
											</div>
											<div class="col-sm-11 manual-div" style="display:none;">
												<?php if(in_array('Name',$inventory_fields)) { ?>
													<input name="name" data-table="inventory" data-id="" data-id-field="inventoryid" class="form-control" placeholder="Enter Name...">
												<?php } ?>
												<?php if(in_array('Product Name',$inventory_fields)) { ?>
													<input name="product_name" data-table="inventory" data-id="" data-id-field="inventoryid" class="form-control" placeholder="Enter Product Name...">
												<?php } ?>
												<?php if(in_array('Brand',$inventory_fields)) { ?>
													<input name="brand" data-table="inventory" data-id="" data-id-field="inventoryid" class="form-control" placeholder="Enter Brand...">
												<?php } ?>
												<?php if(in_array('Category',$inventory_fields)) { ?>
													<input name="category" data-table="inventory" data-id="" data-id-field="inventoryid" class="form-control" placeholder="Enter Tab">
												<?php } ?>
												<?php if(in_array('Description',$inventory_fields)) { ?>
													<input name="description" data-table="inventory" data-id="" data-id-field="inventoryid" class="form-control" placeholder="Enter Description...">
												<?php } ?>
												<?php if(in_array('Code',$inventory_fields)) { ?>
													<input name="code" data-table="inventory" data-id="" data-id-field="inventoryid" class="form-control" placeholder="Enter Code...">
												<?php } ?>
												<?php if(in_array('ID #',$inventory_fields)) { ?>
													<input name="id_number" data-table="inventory" data-id="" data-id-field="inventoryid" class="form-control" placeholder="Enter ID #...">
												<?php } ?>
												<?php if(in_array('Item SKU',$inventory_fields)) { ?>
													<input name="item_sku" data-table="inventory" data-id="" data-id-field="inventoryid" class="form-control" placeholder="Enter Item SKU...">
												<?php } ?>
												<?php if(in_array('Part #',$inventory_fields)) { ?>
													<input name="part_no" data-table="inventory" data-id="" data-id-field="inventoryid" class="form-control" placeholder="Enter Part #...">
												<?php } ?>
												<?php if(in_array('Pallet #',$inventory_fields)) { ?>
													<input name="pallet" data-table="inventory" data-id="" data-id-field="inventoryid" class="form-control" placeholder="Enter Pallet #...">
												<?php } ?>
											</div>
											<div class="col-sm-1">
												<input type="hidden" name="deleted" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" value="0">
												<a href="" onclick="$(this).closest('.form-group').find('select').val('MANUAL').change(); return false;"><img class="inline-img pull-left" data-history-label="New Inventory" src="../img/icons/ROOK-add-icon.png"></a>
											</div>
										<div class="clearfix"></div>
									</div>
								</div>
							<?php } ?>
							<?php if(strpos($value_config,',Inventory Detail Quantity,') !== FALSE && $field_sort_field == 'Inventory Detail Quantity') { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['qty'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Quantity:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<input type="number" min=0 step="1" name="qty" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="form-control" value="<?= $inventory['qty'] ?>" placeholder="Quantity of above inventory items...">
									</div></div>
								</div>
								<div class="clearfix"></div>
							<?php } ?>
							<?php if(strpos($value_config,',Inventory Detail Piece Type,') !== FALSE && $field_sort_field == 'Inventory Detail Piece Type') { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['piece_type'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Piece Tab:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<?php if(count($piece_types) > 0) { ?>
											<select name="piece_type" data-placeholder="Package details (e.g. box/skid etc.)..." data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="chosen-select-deselect"><option></option>
												<?php foreach($piece_types as $piece_type_name) { ?>
													<option <?= $inventory['piece_type'] == $piece_type_name ? 'selected' : '' ?> value="<?= $piece_type_name ?>"><?= $piece_type_name ?></option>
												<?php } ?>
												<?php if(!in_array($inventory['piece_type'],$piece_types)) { ?>
													<option selected value="<?= $inventory['piece_type'] ?>"><?= $inventory['piece_type'] ?></option>
												<?php } ?>
											</select>
										<?php } else { ?>
											<input type="text" name="piece_type" placeholder="Package details (e.g. box/skid etc.)..." data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="form-control" value="<?= $inventory['piece_type]'] ?>">
										<?php } ?>
									</div></div>
								</div>
								<div class="clearfix"></div>
							<?php } ?>
							<?php if(strpos($value_config,',Inventory Detail Site,') !== FALSE && $field_sort_field == 'Inventory Detail Site') { ?>
								<div class="site_group">
									<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['siteid'] != '' ? '' : 'style="display:none;"' ?>>
										<label class="control-label col-sm-4"><?= SITES_CAT ?>:</label>
										<div class="col-sm-8">
											<div class="col-sm-10">
												<select name="siteid[]" multiple data-concat="," data-placeholder="Select <?= SITES_CAT ?>" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" class="chosen-select-deselect">
													<?php if(!isset($site_list)) {
														$site_list = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, site_name, `display_name`, businessid FROM `contacts` WHERE `category`='".SITES_CAT."' AND deleted=0 ORDER BY IFNULL(NULLIF(`display_name`,''),`site_name`)"));
													}
													foreach($site_list as $site_row) { ?>
														<option <?= $inventory['siteid'] == $site_row['contactid'] ? 'selected' : '' ?> value="<?= $site_row['contactid'] ?>"><?= $site_row['full_name'] ?></option>
													<?php } ?>
													<option value="MANUAL">Add New Site</option>
												</select>
											</div>
											<div class="col-sm-2">
												<a href="" onclick="viewSite(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
												<a href="" onclick="$(this).closest('.form-group').find('select').val('MANUAL').change(); return false;"><img class="inline-img pull-right" data-history-label="New <?= SITES_CAT ?>" src="../img/icons/ROOK-add-icon.png"></a>
											</div>
										</div>
									</div>
									<div class="form-group clearfix site_name" style="display:none;">
										<label class="control-label col-sm-4">Name of Location:</label>
										<div class="col-sm-8">
											<div class="col-sm-12">
												<input type="text" name="site_name" data-table="contacts" data-id="" data-id-field="contactid" data-attach="Sites" data-attach-field="category" class="form-control">
											</div>
										</div>
									</div>
									<div class="clearfix"></div>
								</div>
							<?php } ?>
							<?php if(strpos($value_config,',Inventory Detail Customer Order,') !== FALSE && $field_sort_field == 'Inventory Detail Customer Order') { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['position'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Customer Order #:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<input type="text" name="position" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="form-control" value="<?= $inventory['position'] ?>">
									</div></div>
								</div>
								<div class="clearfix"></div>
							<?php } ?>
							<?php if(strpos($value_config,',Inventory Detail PO Num,') !== FALSE && $field_sort_field == 'Inventory Detail PO Num') { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['po_num'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Purchase Order #:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<input type="number" min=0 step="1" name="po_num" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="form-control" value="<?= $inventory['po_num'] ?>">
									</div></div>
								</div>
								<div class="clearfix"></div>
							<?php } ?>
							<?php if(strpos($value_config,',Inventory Detail PO Line,') !== FALSE && $field_sort_field == 'Inventory Detail PO Line') { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['po_line'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">PO Line #:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<input type="number" min=0 step="1" name="po_line" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="form-control" value="<?= $inventory['po_line'] ?>">
									</div></div>
								</div>
								<div class="clearfix"></div>
							<?php } ?>
							<?php if(strpos($value_config,',Inventory Detail PO Read,') !== FALSE && $field_sort_field == 'Inventory Detail PO Read') { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['po_line'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Purchase Order Line Item:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<input type="number" min=0 step="1" name="po_line" readonly class="form-control" value="<?= $inventory['po_line'] ?>">
									</div></div>
								</div>
								<div class="clearfix"></div>
							<?php } ?>
							<?php if(strpos($value_config,',Inventory Detail PO Dropdown,') !== FALSE && $field_sort_field == 'Inventory Detail PO Dropdown') { ?>
								<div class="form-group">
									<label class="control-label col-sm-4">Purchase Order Line Item:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<select name="po_line" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="chosen-select-deselect"><option />
											<?php $line_num = $dbc->query("SELECT MAX(`po_line`) FROM `ticket_attached` WHERE `deleted`=0 AND `src_table`='inventory'")->fetch_array(MYSQLI_NUM)[0]; ?>
											<?php for($i = 10; $i <= 550; $i += 10) { ?>
												<option <?= $inventory['po_line'] == $i ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?></option>
											<?php } ?>
										</select>
									</div></div>
								</div>
								<div class="clearfix"></div>
							<?php } ?>
							<?php if(strpos($value_config,',Inventory Detail Vendor,') !== FALSE && $field_sort_field == 'Inventory Detail Vendor') { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['contact_info'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Vendor:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<input type="text" name="contact_info" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="form-control" value="<?= $inventory['contact_info'] ?>">
									</div></div>
								</div>
								<div class="clearfix"></div>
							<?php } ?>
							<?php if(strpos($value_config,',Inventory Detail Weight,') !== FALSE && $field_sort_field == 'Inventory Detail Weight') { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['weight'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="col-sm-4 control-label">Weight:</label>
									<div class="col-sm-8">
										<div class="<?= strpos($value_config,',Inventory Detail Units,') !== FALSE ? 'col-sm-6' : 'col-sm-12' ?>">
											<input type="number" min=0 step="1" name="weight" placeholder="Weight" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="form-control" value="<?= $inventory['weight'] ?>">
										</div>
										<?php if(strpos($value_config,',Inventory Detail Units,') !== FALSE) { ?>
											<div class="col-sm-6">
												<select name="weight_units" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-placeholder="Select Units" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="chosen-select-deselect">
													<option></option>
													<option <?= $inventory['weight_units'] == 'kg' ? 'selected' : '' ?> value="kg">kg</option>
													<option <?= $inventory['weight_units'] == 'lbs' ? 'selected' : '' ?> value="lbs">lbs</option>
												</select>
											</div>
											<div class="clearfix"></div>
										<?php } ?>
									</div>
								</div>
								<div class="clearfix"></div>
							<?php } ?>
							<?php if(strpos($value_config,',Inventory Detail Net Weight,') !== FALSE && $field_sort_field == 'Inventory Detail Net Weight') { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['net_weight'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="col-sm-4 control-label">Net Weight:</label>
									<div class="col-sm-8">
										<div class="<?= strpos($value_config,',Inventory Detail Net Units,') !== FALSE ? 'col-sm-6' : 'col-sm-12' ?>">
											<input type="number" min=0 step="1" name="net_weight" placeholder="Net Weight" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="form-control" value="<?= $inventory['net_weight'] ?>">
										</div>
										<?php if(strpos($value_config,',Inventory Detail Net Units,') !== FALSE) { ?>
											<div class="col-sm-6">
												<select name="net_units" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-placeholder="Select Units" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="chosen-select-deselect">
													<option></option>
													<option <?= $inventory['net_units'] == 'kg' ? 'selected' : '' ?> value="kg">kg</option>
													<option <?= $inventory['net_units'] == 'lbs' ? 'selected' : '' ?> value="lbs">lbs</option>
												</select>
											</div>
											<div class="clearfix"></div>
										<?php } ?>
									</div>
								</div>
								<div class="clearfix"></div>
							<?php } ?>
							<?php if(strpos($value_config,',Inventory Detail Gross Weight,') !== FALSE && $field_sort_field == 'Inventory Detail Gross Weight') { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['gross_weight'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="col-sm-4 control-label">Gross Weight:</label>
									<div class="col-sm-8">
										<div class="<?= strpos($value_config,',Inventory Detail Gross Units,') !== FALSE ? 'col-sm-6' : 'col-sm-12' ?>">
											<input type="number" min=0 step="1" name="gross_weight" placeholder="Gross Weight" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="form-control" value="<?= $inventory['gross_weight'] ?>">
										</div>
										<?php if(strpos($value_config,',Inventory Detail Gross Units,') !== FALSE) { ?>
											<div class="col-sm-6">
												<select name="gross_units" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-placeholder="Select Units" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="chosen-select-deselect">
													<option></option>
													<option <?= $inventory['gross_units'] == 'kg' ? 'selected' : '' ?> value="kg">kg</option>
													<option <?= $inventory['gross_units'] == 'lbs' ? 'selected' : '' ?> value="lbs">lbs</option>
												</select>
											</div>
											<div class="clearfix"></div>
										<?php } ?>
									</div>
								</div>
								<div class="clearfix"></div>
							<?php } ?>
							<?php if(strpos($value_config,',Inventory Detail Dimensions,') !== FALSE && $field_sort_field == 'Inventory Detail Dimensions') { ?>
								<?php $inv_dim_units = explode('#*#',$inventory['dimension_units']);
								foreach(explode('#*#',$inventory['dimensions']) as $id => $inv_dimension) {
									$inv_dimensions = explode('x',$inv_dimension);
									$inv_dim_unit_list = explode('x',$inv_dim_units[$id]); ?>
									<div class="form-group multi_dimensions <?= strpos($inventory['dimensions'],'#*#') !== FALSE ? '' : 'hidden' ?>" <?= $general_inventory['description'] == '' || trim($inventory['dimensions'],'x #*') != '' ? '' : 'style="display:none;"' ?>>
										<label class="control-label col-sm-4">Piece Dimension (LxWxH):</label>
										<div class="col-sm-8">
											<input type="hidden" name="dimensions" data-concat="#*#" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" value="<?= $inv_dimension ?>">
											<input type="hidden" name="dimension_units" data-concat="#*#" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" value="<?= $inv_dim_units[$id] ?>">
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
								<div class="form-group single_dimensions <?= strpos($inventory['dimensions'].$inventory['weight'],'#*#') !== FALSE ? 'hidden' : '' ?>" <?= $general_inventory['description'] == '' || trim($inventory['dimensions'],'x #*') != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Piece Dimension (LxWxH):</label>
									<div class="col-sm-8">
										<div class="col-sm-<?= strpos($value_config,',Inventory Detail Dimension Units,') !== FALSE ? '3' : '4' ?>">
											<input type="text" name="dimensions<?= strpos($inventory['dimensions'].$inventory['weight'],'#*#') !== FALSE ? '_halt_add' : '' ?>" placeholder="Length" data-concat="x" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="form-control" value="<?= trim(explode('x',explode('#*#',$inventory['dimensions'])[0])[0]) ?>">
										</div>
										<div class="col-sm-<?= strpos($value_config,',Inventory Detail Dimension Units,') !== FALSE ? '3' : '4' ?>">
											<input type="text" name="dimensions<?= strpos($inventory['dimensions'].$inventory['weight'],'#*#') !== FALSE ? '_halt_add' : '' ?>" placeholder="Width" data-concat="x" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="form-control" value="<?= trim(explode('x',explode('#*#',$inventory['dimensions'])[0])[1]) ?>">
										</div>
										<div class="col-sm-<?= strpos($value_config,',Inventory Detail Dimension Units,') !== FALSE ? '3' : '4' ?>">
											<input type="text" name="dimensions<?= strpos($inventory['dimensions'].$inventory['weight'],'#*#') !== FALSE ? '_halt_add' : '' ?>" placeholder="Height" data-concat="x" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="form-control" value="<?= trim(explode('x',explode('#*#',$inventory['dimensions'])[0])[2]) ?>">
										</div>
										<?php if(strpos($value_config,',Inventory Detail Dimension Units,') !== FALSE) { ?>
											<div class="col-sm-3">
												<select name="dimension_units<?= strpos($inventory['dimensions'].$inventory['weight'],'#*#') !== FALSE ? '_halt_add' : '' ?>" data-placeholder="Select Units" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" class="chosen-select-deselect">
													<option></option>
													<option <?= in_array(strtolower($inventory['dimension_units']),['mm','mms','millimeter','millimetre','millimeters','millimetres']) ? 'selected' : '' ?> value="mm">mm</option>
													<option <?= in_array(strtolower($inventory['dimension_units']),['cm','cms','centimeter','centimetre','centimeters','centimetres']) ? 'selected' : '' ?> value="cm">cm</option>
													<option <?= in_array(strtolower($inventory['dimension_units']),['m','meter','metre','meters','metres']) ? 'selected' : '' ?> value="m">m</option>
													<option <?= in_array(strtolower($inventory['dimension_units']),['in','inch','inches']) ? 'selected' : '' ?> value="in">in</option>
													<option <?= in_array(strtolower($inventory['dimension_units']),['ft','feet','foot']) ? 'selected' : '' ?> value="ft">ft</option>
												</select>
											</div>
										<?php } ?>
									</div>
								</div>
								<div class="clearfix"></div>
							<?php } ?>
							<?php if(strpos($value_config,',Inventory Detail Used,') !== FALSE && $field_sort_field == 'Inventory Detail Used') { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['used'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Picked:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<input type="number" min=0 step="1" name="used" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="form-control" value="<?= $inventory['used'] ?>">
									</div></div>
								</div>
								<div class="clearfix"></div>
							<?php } ?>
							<?php if(strpos($value_config,',Inventory Detail Received,') !== FALSE && $field_sort_field == 'Inventory Detail Received') { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['received'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Received:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<input type="number" min=0 step="1" name="received" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="form-control" value="<?= $inventory['received'] ?>" placeholder="Actual received items vs ordered...">
									</div></div>
								</div>
								<div class="clearfix"></div>
							<?php } ?>
							<?php if(strpos($value_config,',Inventory Detail Discrepancy,') !== FALSE && $field_sort_field == 'Inventory Detail Discrepancy') { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['discrepancy'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Discrepancy:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<input type="number" min=0 step="1" name="discrepancy" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="form-control" value="<?= $inventory['discrepancy'] ?>" placeholder="Any discrepancy on this specific item...">
									</div></div>
								</div>
								<div class="clearfix"></div>
							<?php } ?>
							<?php if(strpos($value_config,',Inventory Detail Discrepancy Yes No,') !== FALSE && $field_sort_field == 'Inventory Detail Discrepancy Yes No') { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['discrepancy'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Discrepancy:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<?= $inventory['qty'] == $inventory['received'] ? 'No' : '<b>Yes</b>'; ?>
									</div></div>
								</div>
								<div class="clearfix"></div>
							<?php } ?>
							<?php if(strpos($value_config,',Inventory Detail Back Order,') !== FALSE && $field_sort_field == 'Inventory Detail Back Order') { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['backorder'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Back Order:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<input type="number" min=0 step="1" name="backorder" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="form-control" value="<?= $inventory['backorder'] ?>">
									</div></div>
								</div>
								<div class="clearfix"></div>
							<?php } ?>
							<?php if(strpos($value_config,',Inventory Detail Location,') !== FALSE && $field_sort_field == 'Inventory Detail Location') { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['description'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Location:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<input type="text" name="description" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num" class="form-control" value="<?= $inventory['description'] ?>">
									</div></div>
								</div>
								<div class="clearfix"></div>
							<?php } ?>
						<?php } ?>
						<?php if(!$no_fetch || strpos($value_config,',Inventory General Manual Add Pieces,') === FALSE || strpos($value_config,',Inventory Detail Manual Add,') !== FALSE) { ?>
							<img class="inline-img pull-right" data-history-label="Inventory" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
							<img class="inline-img pull-right" data-history-label="Inventory" onclick="remMulti(this);" src="../img/remove.png">
						<?php } ?>
					</div>
				<?php } else { ?>
					<?php foreach($field_sort_order as $field_sort_field) { ?>
						<?php if(strpos($value_config,',Inventory Detail Category,') !== FALSE && $field_sort_field == 'Inventory Detail Category') { ?>
							<div class="form-group select-div" <?= $general_inventory['description'] == '' || $inventory['category'] != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Tab:</label>
								<div class="col-sm-8">
										<?= $inventory['category'] ?>
								</div>
							</div>
							<?php $pdf_contents[] = ['Category', $inventory['category']]; ?>
							<?php $sub_cats = mysqli_query($dbc, "SELECT `category`, `sub_category` FROM `inventory` WHERE IFNULL(`sub_category`,'') != '' GROUP BY `sub_category` ORDER BY `sub_category`");
							if(mysqli_num_rows($sub_cats) > 0) { ?>
								<div class="form-group select-div" <?= $general_inventory['description'] == '' || $inventory['sub_category'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Sub-Tab:</label>
									<div class="col-sm-8">
										<?= $inventory['sub_category'] ?>
									</div>
								</div>
								<?php $pdf_contents[] = ['Sub-Category', $inventory['sub_category']]; ?>
							<?php }
						} ?>
						<?php if(strpos($value_config,',Inventory Detail Unique,') !== FALSE && $field_sort_field == 'Inventory Detail Unique') { ?>
							<input type="hidden" name="item_id" value="<?= $inventory['item_id'] ?>" data-table="ticket_attached" data-id="<?= $inventory['id'] ?>" data-id-field="id" data-type="inventory" data-type-field="src_table" data-attach="<?= $general_item['id'] ?>" data-attach-field="line_id" data-detail="<?= $i ?>" data-detail-field="piece_num">
							<?php if(in_array('Name',$inventory_fields)) { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['name'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Inventory:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<?= $inventory['name'] ?>
									</div></div>
								</div>
								<?php $pdf_contents[] = ['Inventory', $inventory['name']]; ?>
							<?php } ?>
							<?php if(in_array('Product Name',$inventory_fields)) { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['product_name'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Product Name:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<?= $inventory['product_name'] ?>
									</div></div>
								</div>
								<?php $pdf_contents[] = ['Product Name', $inventory['product_name']]; ?>
							<?php } ?>
							<?php if(in_array('Brand',$inventory_fields)) { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['brand'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Brand:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<?= $inventory['brand'] ?>
									</div></div>
								</div>
								<?php $pdf_contents[] = ['Brand', $inventory['brand']]; ?>
							<?php } ?>
							<?php if(in_array('Category',$inventory_fields)) { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['category'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Tab:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<?= $inventory['category'] ?>
									</div></div>
								</div>
								<?php $pdf_contents[] = ['Category', $inventory['category']]; ?>
							<?php } ?>
							<?php if(in_array('Description',$inventory_fields)) { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['description'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Description:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<?= $inventory['description'] ?>
									</div></div>
								</div>
								<?php $pdf_contents[] = ['Description', $inventory['description']]; ?>
							<?php } ?>
							<?php if(in_array('Code',$inventory_fields)) { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['code'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Code:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<?= $inventory['code'] ?>
									</div></div>
								</div>
								<?php $pdf_contents[] = ['Code', $inventory['code']]; ?>
							<?php } ?>
							<?php if(in_array('ID #',$inventory_fields)) { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['id_number'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">ID #:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<?= $inventory['id_number'] ?>
									</div></div>
								</div>
								<?php $pdf_contents[] = ['ID #', $inventory['id_number']]; ?>
							<?php } ?>
							<?php if(in_array('Item SKU',$inventory_fields)) { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['item_sku'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Item SKU:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<?= $inventory['item_sku'] ?>
									</div></div>
								</div>
								<?php $pdf_contents[] = ['Item SKU', $inventory['item_sku']]; ?>
							<?php } ?>
							<?php if(in_array('Part #',$inventory_fields)) { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['part_no'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Part #:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<?= $inventory['part_no'] ?>
									</div></div>
								</div>
								<?php $pdf_contents[] = ['Part #', $inventory['part_no']]; ?>
							<?php } ?>
							<?php if(in_array('Pallet Num',$inventory_fields)) { ?>
								<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['pallet'] != '' ? '' : 'style="display:none;"' ?>>
									<label class="control-label col-sm-4">Pallet #:</label>
									<div class="col-sm-8"><div class="col-sm-12">
										<?= $inventory['pallet'] ?>
									</div></div>
								</div>
								<?php $pdf_contents[] = ['Pallet #', $inventory['pallet']]; ?>
							<?php } ?>
						<?php } else if($field_sort_order == 'Inventory Detail Category')  { ?>
							<div class="form-group"  <?= $general_inventory['description'] == '' || $inventory['item_id'] > 0 ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Inventory:</label>
								<div class="col-sm-8">
									<?= mysql_fetch_assoc(mysqli_query($dbc, "SELECT `category`, `sub_category`, CONCAT(IFNULL(`product_name`,''),' ',IFNULL(`name`,''),' ',IFNULL(`part_no`,'')) `label`, `inventoryid` FROM `inventory` WHERE `deleted`=0 AND `inventoryid`='".$inventory['item_id']."'"))['label'] ?>
								</div>
								<div class="clearfix"></div>
							</div>
							<?php $pdf_contents[] = ['Inventory', mysql_fetch_assoc(mysqli_query($dbc, "SELECT `category`, `sub_category`, CONCAT(IFNULL(`product_name`,''),' ',IFNULL(`name`,''),' ',IFNULL(`part_no`,'')) `label`, `inventoryid` FROM `inventory` WHERE `deleted`=0 AND `inventoryid`='".$inventory['item_id']."'"))['label']]; ?>
						<?php } ?>
						<?php if(strpos($value_config,',Inventory Detail Quantity,') !== FALSE && $field_sort_field == 'Inventory Detail Quantity') { ?>
							<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['qty'] != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Quantity:</label>
								<div class="col-sm-8">
									<?= $inventory['qty'] ?>
								</div>
							</div>
							<div class="clearfix"></div>
							<?php $pdf_contents[] = ['Quantity', $inventory['qty']]; ?>
						<?php } ?>
						<?php if(strpos($value_config,',Inventory Detail Site,') !== FALSE && $field_sort_field == 'Inventory Detail Site') { ?>
							<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['siteid'] != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4"><?= SITES_CAT ?>:</label>
								<div class="col-sm-8">
									<?php $site_names = [];
									foreach(array_filter(explode(',',$inventory['siteid'])) as $site_id) {
										$query = mysqli_query($dbc,"SELECT contactid, site_name, `display_name`, businessid FROM `contacts` WHERE `category`='Sites' AND deleted=0 AND `contactid`='".$site_id."' ORDER BY IFNULL(NULLIF(`display_name`,''),`site_name`)");
										$row = mysqli_fetch_array($query);
										$site_names[] = ($row['display_name'] == '' ? $row['site_name'] : $row['display_name']);
									}
									$site_names = implode(', ',$site_names);
									echo $site_names; ?>
								</div>
							</div>
							<div class="clearfix"></div>
							<?php $pdf_contents[] = [SITES_CAT, $site_names]; ?>
						<?php } ?>
						<?php if(strpos($value_config,',Inventory Detail Piece Type,') !== FALSE && $field_sort_field == 'Inventory Detail Piece Type') { ?>
							<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['piece_type'] != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Piece Tab:</label>
								<div class="col-sm-8">
									<?= $inventory['piece_type'] ?>
								</div>
							</div>
							<div class="clearfix"></div>
							<?php $pdf_contents[] = ['Piece Type', $inventory['piece_type']]; ?>
						<?php } ?>
						<?php if(strpos($value_config,',Inventory Detail PO Num,') !== FALSE && $field_sort_field == 'Inventory Detail PO Num') { ?>
							<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['po_num'] != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Purchase Order #:</label>
								<div class="col-sm-8">
									<?= $inventory['po_num'] ?>
								</div>
							</div>
							<div class="clearfix"></div>
							<?php $pdf_contents[] = ['Purchase Order #', $inventory['po_num']]; ?>
						<?php } ?>
						<?php if(strpos($value_config,',Inventory Detail PO Line,') !== FALSE && $field_sort_field == 'Inventory Detail PO Line') { ?>
							<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['po_line'] != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">PO Line #:</label>
								<div class="col-sm-8">
									<?= $inventory['po_line'] ?>
								</div>
							</div>
							<div class="clearfix"></div>
							<?php $pdf_contents[] = ['PO Line #', $inventory['po_line']]; ?>
						<?php } ?>
						<?php if(strpos($value_config,',Inventory Detail Customer Order,') !== FALSE && $field_sort_field == 'Inventory Detail Customer Order') { ?>
							<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['position'] != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Customer Order #:</label>
								<div class="col-sm-8">
									<?= $inventory['position'] ?>
								</div>
							</div>
							<div class="clearfix"></div>
							<?php $pdf_contents[] = ['Customer Order #', $inventory['position']]; ?>
						<?php } ?>
						<?php if((strpos($value_config,',Inventory Detail PO Read,') !== FALSE && $field_sort_field == 'Inventory Detail PO Read') || (strpos($value_config,',Inventory Detail PO Dropdown,') !== FALSE && $field_sort_field == 'Inventory Detail PO Dropdown')) { ?>
							<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['po_line'] != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Purchase Order Line Item:</label>
								<div class="col-sm-8">
									<?= $inventory['po_line'] ?>
								</div>
							</div>
							<div class="clearfix"></div>
							<?php $pdf_contents[] = ['Purchase Order Line Item', $inventory['po_line']]; ?>
						<?php } ?>
						<?php if(strpos($value_config,',Inventory Detail Vendor,') !== FALSE && $field_sort_field == 'Inventory Detail Vendor') { ?>
							<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['contact_info'] != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Vendor:</label>
								<div class="col-sm-8">
									<?= $inventory['contact_info'] ?>
								</div>
							</div>
							<div class="clearfix"></div>
							<?php $pdf_contents[] = ['Vendor', $inventory['contact_info']]; ?>
						<?php } ?>
						<?php if(strpos($value_config,',Inventory Detail Weight,') !== FALSE && $field_sort_field == 'Inventory Detail Weight') { ?>
							<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['weight'] != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Weight:</label>
								<div class="col-sm-8">
									<?= $inventory['weight'].$inventory['weight_units'] ?>
								</div>
							</div>
							<div class="clearfix"></div>
							<?php $pdf_contents[] = ['Weight', $inventory['weight'].$inventory['weight_units']]; ?>
						<?php } ?>
						<?php if(strpos($value_config,',Inventory Detail Net Weight,') !== FALSE && $field_sort_field == 'Inventory Detail Net Weight') { ?>
							<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['net_weight'] != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Net Weight:</label>
								<div class="col-sm-8">
									<?= $inventory['net_weight'].$inventory['net_units'] ?>
								</div>
							</div>
							<div class="clearfix"></div>
							<?php $pdf_contents[] = ['Net Weight', $inventory['net_weight'].$inventory['net_units']]; ?>
						<?php } ?>
						<?php if(strpos($value_config,',Inventory Detail Gross Weight,') !== FALSE && $field_sort_field == 'Inventory Detail Gross Weight') { ?>
							<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['gross_weight'] != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Gross Weight:</label>
								<div class="col-sm-8">
									<?= $inventory['gross_weight'].$inventory['gross_units'] ?>
								</div>
							</div>
							<div class="clearfix"></div>
							<?php $pdf_contents[] = ['Gross Weight', $inventory['gross_weight'].$inventory['gross_units']]; ?>
						<?php } ?>
						<?php if(strpos($value_config,',Inventory Detail Dimensions,') !== FALSE && $field_sort_field == 'Inventory Detail Dimensions') { ?>
							<?php $inv_dim_units = explode('#*#',$inventory['dimension_units']);
							$echo_dimensions = '';
							foreach(explode('#*#',$inventory['dimensions']) as $id => $inv_dimension) {
								$inv_dimensions = explode('x',$inv_dimension);
								$inv_dim_unit_list = explode('x',$inv_dim_units[$id]);
								$echo_dimensions .= $inv_dimension[0].$inv_dim_unit_list[0].'x'.$inv_dimension[1].$inv_dim_unit_list[1].'x'.$inv_dimension[2].$inv_dim_unit_list[2].'<br />';
							} ?>
							<div class="form-group" <?= $general_inventory['description'] == '' || $dimensions != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Piece Dimension (LxWxH):</label>
								<?= $echo_dimensions ?>
							</div>
							<div class="clearfix"></div>
							<?php $pdf_contents[] = ['Piece Dimension (LxWxH)', (1 === preg_match('~[0-9]~', $echo_dimensions) ? $echo_dimensions : '')]; ?>
						<?php } ?>
						<?php if(strpos($value_config,',Inventory Detail Used,') !== FALSE && $field_sort_field == 'Inventory Detail Used') { ?>
							<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['used'] != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Picked:</label>
								<div class="col-sm-8">
									<?= $inventory['used'] ?>
								</div>
							</div>
							<div class="clearfix"></div>
							<?php $pdf_contents[] = ['Picked', $inventory['used']]; ?>
						<?php } ?>
						<?php if(strpos($value_config,',Inventory Detail Received,') !== FALSE && $field_sort_field == 'Inventory Detail Received') { ?>
							<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['received'] != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Received:</label>
								<div class="col-sm-8">
									<?= $inventory['received'] ?>
								</div>
							</div>
							<div class="clearfix"></div>
							<?php $pdf_contents[] = ['Received', $inventory['received']]; ?>
						<?php } ?>
						<?php if(strpos($value_config,',Inventory Detail Discrepancy,') !== FALSE && $field_sort_field == 'Inventory Detail Discrepancy') { ?>
							<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['discrepancy'] != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Discrepancy:</label>
								<div class="col-sm-8">
									<?= $inventory['discrepancy'] ?>
								</div>
							</div>
							<div class="clearfix"></div>
							<?php $pdf_contents[] = ['Discrepancy', $inventory['discrepancy']]; ?>
						<?php } ?>
						<?php if(strpos($value_config,',Inventory Detail Discrepancy Yes No,') !== FALSE && $field_sort_field == 'Inventory Detail Discrepancy Yes No') { ?>
							<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['discrepancy'] != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Discrepancy:</label>
								<div class="col-sm-8">
									<?= $inventory['qty'] == $inventory['received'] ? 'No' : '<b>Yes</b>'; ?>
								</div>
							</div>
							<div class="clearfix"></div>
							<?php $pdf_contents[] = ['Discrepancy', $inventory['used'] == $inventory['received'] ? 'No' : '<b>Yes</b>' ]; ?>
						<?php } ?>
						<?php if(strpos($value_config,',Inventory Detail Back Order,') !== FALSE && $field_sort_field == 'Inventory Detail Back Order') { ?>
							<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['backorder'] != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Back Order:</label>
								<div class="col-sm-8">
									<?= $inventory['backorder'] ?>
								</div>
							</div>
							<div class="clearfix"></div>
							<?php $pdf_contents[] = ['Back Order', $inventory['backorder']]; ?>
						<?php } ?>
						<?php if(strpos($value_config,',Inventory Detail Location,') !== FALSE && $field_sort_field == 'Inventory Detail Location') { ?>
							<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['description'] != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Location:</label>
								<div class="col-sm-8">
									<?= $inventory['description'] ?>
								</div>
							</div>
							<div class="clearfix"></div>
							<?php $pdf_contents[] = ['Location', $inventory['description']]; ?>
						<?php } ?>
					<?php } ?>
				<?php } ?>
			<?php } while($inventory = mysqli_fetch_assoc($inventory_list)); ?>
		</div>
	<?php }
} while(!$no_fetch && $general_item = $general_list->fetch_assoc()); ?>