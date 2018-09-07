<script>
$(document).ready(function() {
	$('.multi_dimensions .col-sm-2 input,.multi_dimensions .col-sm-2 select').off('change',setMultiDimensions).change(setMultiDimensions);
	$('.tab-section[data-type=general]:first').find('[data-table]').off('change',copyPiece).change(copyPiece);
});
function copyPiece(event) {
	var field = event.target;
	if($(field).data('type') == 'inventory_general') {
		$('[data-table=ticket_attached][data-type=inventory_general][name=contact_info]:checked').each(function() {
			$(this).closest('.tab-section').find('[data-type=inventory_general][name="'+field.name+'"]').val(field.value).change();
		});
	}
}
function copyAllPiece() {
	var i = 0;
	$('.tab-section[data-type=general]:visible [data-type=inventory_general][name=contact_info]').each(function() {
		if(!this.checked) {
			$(this).prop('checked',true);
			current_fields.push(this);
		}
	});
	$('.tab-section[data-type=general]:first').find('[data-table=ticket_attached][data-type=inventory_general][name!=contact_info]').each(function() {
		var src = this;
		$('.tab-section[data-type=general]:visible').not(':first').each(function() {
			var field = $(this).find('[data-table=ticket_attached][data-type=inventory_general][name!=contact_info]').get(i);
			if($(src).val() != $(field).val()) {
				$(field).val($(src).val()).change();
			}
		});
		i++;
	});
}
function copyPieceOne(target) {
	var first_piece = $('.tab-section[data-type=general]:first').find('[data-table=ticket_attached][data-type=inventory_general][name!=contact_info]');
	var i = 0;
	$(target).closest('.tab-section').find('[data-table=ticket_attached][data-type=inventory_general][name!=contact_info]').each(function() {
		if($(this).closest('.form-group').is(':visible') && $(this).val() != first_piece.get(i).value) {
			$(this).val(first_piece.get(i).value).change();
		}
		i++;
	});
}
function multiPieces(input) {
	<?php if(strpos($value_config,',Inventory General Manual Add Pieces,') === FALSE) { ?>
		while($('#tab_section_ticket_inventory_general .multi-block[data-type=general]').length > input.value && $('#tab_section_ticket_inventory_general .multi-block[data-type=general]').length > 1) {
			remMulti($('#tab_section_ticket_inventory_general .multi-block h4').last().get(0));
		}
		while($('#tab_section_ticket_inventory_general .multi-block[data-type=general]').length < input.value) {
			addMulti($('#tab_section_ticket_inventory_general .multi-block h4').last().get(0));
		}
		setPieceNumbers();
		$('.tab-section[data-type=general]').data('triggered',0);
		triggerSaveGeneralPiece();
	<?php } ?>
}
function updatePieceCount() {
	$.post('ticket_ajax_all.php?action=update_piece_count', { ticket: ticketid }, function(response) {
		$('[name=qty][data-type=inventory_shipment]').first().val(response).change();
		$('[name=weight][data-type=inventory_shipment]').first().change();
	});
}
function setPieceNumbers() {
	var i = 1;
	$('#tab_section_ticket_inventory_general .multi-block[data-type=general] h4').each(function() {
		var val = $(this).closest('.multi-block').find('[name=piece_type]').val()
		$(this).text('Shipment Piece '+(i++)+(val != '' ? ': '+val : ''));
	});
}
function addPieces(button) {
	$(button).prop('disabled',true).text('Adding Pieces...');
	completed_last = function() {
		$.post('ticket_ajax_all.php?action=add_pieces',{ticketid:ticketid,count:$('[name=qty][data-table=ticket_attached][data-type=inventory_shipment]').val(),units:$('[name=weight_units][data-table=ticket_attached][data-type=inventory_shipment]').val()},function(response) {
			window.location.reload();
		});
	}
	if(current_fields.length == 0 && saving_field == null) {
		completed_last();
	}
}
function triggerSaveGeneralPiece() {
	if(!ticket_wait) {
		var trigger = $('.tab-section[data-type=general]').filter(function() { return $(this).data('triggered') == 0; }).first();
		trigger.data('triggered',1);
		trigger.find('[name=piece_type][data-type=inventory_general]').first().change();
		var units = $('[name=weight_units][data-table=ticket_attached][data-type=inventory_shipment]').val();
		if(units != '') {
			trigger.find('[name=weight_units][data-type=inventory_general]').val(units).change();
		}
	}
	if($('.tab-section[data-type=general]').filter(function() { return $(this).data('triggered') == 0; }).length > 0) {
		setTimeout(triggerSaveGeneralPiece, 100);
	}
}
function multiDimensions(input) {
	var block = $(input).closest('.multi-block');
	if(input.checked) {
		block.find('.single_dimensions,.single_weights').addClass('hidden').find('[data-table]').each(function() {
			$(this).attr('name',this.name+'_halt_add');
		});
		block.find('.multi_dimensions,.multi_weights').removeClass('hidden');
		var count_needed = block.find('[name=piece_num]').val();
		if(!(count_needed > 0)) {
			count_needed = 1;
		}
		while(block.find('.multi_dimensions').length > count_needed) {
			block.find('.multi_dimensions').last().remove();
		}
		if(!(count_needed > 0)) {
			count_needed = 1;
		}
		while(block.find('.multi_weights').length > count_needed) {
			block.find('.multi_weights').last().remove();
		}
		destroyInputs($('#tab_section_ticket_inventory_general'));
		for(var i = block.find('.multi_dimensions').length; i < count_needed; i++) {
			var clone = block.find('.multi_dimensions').last().clone();
			clone.find('input,select').val('');
			block.find('.multi_dimensions').last().after(clone);
		}
		for(var i = block.find('.multi_weights').length; i < count_needed; i++) {
			var clone = block.find('.multi_weights').last().clone();
			clone.find('input,select').val('');
			block.find('.multi_weights').last().after(clone);
		}
		initInputs('#tab_section_ticket_inventory_general');
		block.find('.multi_dimensions .col-sm-2 input,.multi_dimensions .col-sm-2 select').off('change',setMultiDimensions).change(setMultiDimensions);
		block.find('.multi_dimensions,.multi_weights').find('[data-table]').change();
	} else {
		block.find('.single_dimensions,.single_weights').removeClass('hidden').find('[data-table]').each(function() {
			$(this).attr('name',this.name.replace('_halt_add','')).change();
		});
		block.find('.multi_dimensions,.multi_weights').addClass('hidden');
	}
	setSave();
}
function setMultiDimensions() {
	var block = $(this).closest('.col-sm-8');
	block.find('[name=dimensions]').val(block.find('.dim_l').val()+'x'+block.find('.dim_w').val()+'x'+block.find('.dim_h').val()).change();
	block.find('[name=dimension_units]').val(block.find('.dimunit_l').val()+'x'+block.find('.dimunit_w').val()+'x'+block.find('.dimunit_h').val()).change();
}
</script>
<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>General Cargo / Inventory Information</h3>') ?>
<?php $shipment_count = 1;
$general_description = $dbc->query("SELECT `description` FROM `ticket_attached` WHERE `src_table`='inventory_general' AND `ticketid`='$ticketid'")->fetch_assoc()['description'];

if(strpos($value_config,',Inventory General Detail by Pallet,') !== FALSE) {
	$ticket_pallet_list = $dbc->query("SELECT `pallet`, COUNT(*) `items` FROM `inventory` LEFT JOIN `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` WHERE `ticket_attached`.`deleted`=0 AND `ticket_attached`.`src_table`='inventory' AND `ticket_attached`.`ticketid`='$ticketid' GROUP BY `inventory`.`pallet` ORDER BY IFNULL(`inventory`.`pallet`,'') = '', `inventory`.`pallet`");
	$pallet_line = $ticket_pallet_list->fetch_assoc();
} else {
	$pallet_line = ['pallet'=>'','items'=>0];
}
if(strpos($value_config,',Inventory General Shipment Count Weight,') !== FALSE) {
	$general_shipment = $dbc->query("SELECT `id`, `qty`, `weight`, `weight_units` FROM `ticket_attached` WHERE `ticketid`='$ticketid' AND `src_table`='inventory_shipment' AND `deleted`=0".$query_daily.(strpos($value_config,',Inventory General PO Line Sort,') !== FALSE ? ' ORDER BY `ticket_attached`.`po_line`' : ''))->fetch_assoc();
	$shipment_count = $general_shipment['qty'] * 1;
	if(strpos($value_config,',Inventory General Weight Convert KG to LB,') !== FALSE && $general_shipment['weight_units'] == 'kg') {
		$general_shipment['weight_units'] = 'lbs';
		if($general_shipment['weight'] > 0) {
			$general_shipment['weight'] = number_format(($general_shipment['weight']*2.20462262185),2);
		}
	}
	if($access_all > 0) { ?>
		<div class="form-group">
			<label class="control-label col-sm-4">Shipment Count &amp; Weight:</label>
			<div class="col-sm-8">
				<div class="col-sm-4">
					<input type="number" min="0" step="any" name="qty" placeholder="Shipment Count" data-table="ticket_attached" data-id="<?= $general_shipment['id'] ?>" data-id-field="id" data-type="inventory_shipment" data-type-field="src_table" class="form-control" value="<?= $general_shipment['qty'] ?>">
				</div>
				<div class="col-sm-4">
					<input type="number" min="0" step="any" name="weight" placeholder="Shipment Weight" data-table="ticket_attached" data-id="<?= $general_shipment['id'] ?>" data-id-field="id" data-type="inventory_shipment" data-type-field="src_table" class="form-control" value="<?= $general_shipment['weight'] ?>">
				</div>
				<div class="col-sm-4">
					<select name="weight_units" data-table="ticket_attached" data-id="<?= $general_shipment['id'] ?>" data-id-field="id" data-type="inventory_shipment" data-type-field="src_table" data-placeholder="Units" class="chosen-select-deselect">
						<option></option>
						<option <?= $general_shipment['weight_units'] == 'kg' ? 'selected' : '' ?> value="kg">kg</option>
						<option <?= $general_shipment['weight_units'] == 'lbs' ? 'selected' : '' ?> value="lbs">lbs</option>
					</select>
				</div>
				<?php if(strpos($value_config,',Inventory General Manual Add Pieces,') !== FALSE) { ?>
					<button class="btn brand-btn pull-right" onclick="addPieces(this); return false;">Add Pieces</button>
				<?php } ?>
			</div>
		</div>
		<div class="clearfix"></div>
	<?php } else { ?>
		<div class="form-group">
			<label class="control-label col-sm-4">Shipment Count &amp; Weight:</label>
			<div class="col-sm-8">
				<div class="col-sm-6">
					<?= $general_shipment['qty]'] ?>
				</div>
				<div class="col-sm-6">
					<?= $general_shipment['weight]'].' '.$general_shipment['weight_units'] ?>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<?php $pdf_contents[] = ['Shipment Count & Weight', $general_shipment['qty]'].'<br>'.$general_shipment['weight]'].' '.$general_shipment['weight_units']]; ?>
	<?php }
} ?>
<?php if(strpos($value_config,',Inventory General Total Count Weight,') !== FALSE) {
	$general_shipment = $dbc->query("SELECT `qty`, `weight`, `weight_units` FROM `ticket_attached` WHERE `ticketid`='$ticketid' AND `src_table`='inventory_total_ship' AND `deleted`=0".$query_daily)->fetch_assoc();
	if(strpos($value_config,',Inventory General Weight Convert KG to LB,') !== FALSE && $general_shipment['weight_units'] == 'kg') {
		$general_shipment['weight_units'] = 'lbs';
		if($general_shipment['weight'] > 0) {
			$general_shipment['weight'] = number_format(($general_shipment['weight']*2.20462262185),2);
		}
	}
	if($access_all > 0) { ?>
		<div class="form-group">
			<label class="control-label col-sm-4">Total Shipment Count &amp; Total Weight:</label>
			<div class="col-sm-8">
				<div class="col-sm-4">
					<input type="number" min="0" step="any" name="qty" placeholder="Total Shipment Count" data-table="ticket_attached" data-id="<?= $general_shipment['id'] ?>" data-id-field="id" data-type="inventory_total_ship" data-type-field="src_table" class="form-control" value="<?= $general_shipment['qty'] ?>">
				</div>
				<div class="col-sm-4">
					<input type="number" min="0" step="any" name="weight" placeholder="Total Shipment Weight" data-table="ticket_attached" data-id="<?= $general_shipment['id'] ?>" data-id-field="id" data-type="inventory_total_ship" data-type-field="src_table" class="form-control" value="<?= $general_shipment['weight'] ?>">
				</div>
				<div class="col-sm-4">
					<select name="weight_units" data-table="ticket_attached" data-id="<?= $general_shipment['id'] ?>" data-id-field="id" data-type="inventory_total_ship" data-type-field="src_table" data-placeholder="Units" class="chosen-select-deselect">
						<option></option>
						<option <?= $general_shipment['weight_units'] == 'kg' ? 'selected' : '' ?> value="kg">kg</option>
						<option <?= $general_shipment['weight_units'] == 'lbs' ? 'selected' : '' ?> value="lbs">lbs</option>
					</select>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	<?php } else { ?>
		<div class="form-group">
			<label class="control-label col-sm-4">Total Shipment Count &amp; Total Weight:</label>
			<div class="col-sm-8">
				<div class="col-sm-6">
					<?= $general_shipment['qty]'] ?>
				</div>
				<div class="col-sm-6">
					<?= $general_shipment['weight]'].' '.$general_shipment['weight_units'] ?>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<?php $pdf_contents[] = ['Total Shipment Count & Total Weight', $general_shipment['qty]'].'<br>'.$general_shipment['weight]'].' '.$general_shipment['weight_units']]; ?>
	<?php }
} ?>
<?php $general_inventory_list = mysqli_query($dbc, "SELECT `ticket_attached`.`id`, `ticket_attached`.`item_id`, `ticket_attached`.`rate`, `ticket_attached`.`siteid`, `ticket_attached`.`qty`, `ticket_attached`.`received`, `ticket_attached`.`used`, `ticket_attached`.`description`, `ticket_attached`.`status`, `ticket_attached`.`po_num`, `ticket_attached`.`po_line`, `ticket_attached`.`piece_num`, `ticket_attached`.`piece_type`, `ticket_attached`.`used`, `ticket_attached`.`weight`, `ticket_attached`.`weight_units`, `ticket_attached`.`dimensions`, `ticket_attached`.`dimension_units`, `ticket_attached`.`discrepancy`, `ticket_attached`.`backorder`, `ticket_attached`.`position`, `ticket_attached`.`notes`, `ticket_attached`.`contact_info`, `ticket_attached`.`completed`, `inventory`.`category`, `inventory`.`sub_category` FROM `ticket_attached` LEFT JOIN `inventory` ON `ticket_attached`.`item_id`=`inventory`.`inventoryid` WHERE `ticket_attached`.`src_table`='inventory_general' AND (IFNULL(`ticket_attached`.`description`,'') != 'import' OR IFNULL(`ticket_attached`.`piece_type`,'') != '') AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`ticketid` > 0 AND `ticket_attached`.`deleted`=0".$query_daily);
$general_inventory = mysqli_fetch_assoc($general_inventory_list);
$piece_types = array_filter(explode(',',get_config($dbc, 'piece_types')));
$general_line_item = 0;
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
	if($general_inventory['dimensions'] == '') {
		$general_inventory['dimensions'] = ' x x ';
	}
	if($general_line_item++ > 0) {
		echo '<hr />';
	}
	if($access_all > 0) { ?>
		<div id="tab_section_general_detail_<?= $general_inventory['id'] ?>" class="tab-section multi-block" data-type="general">
			<label class="form-checkbox" <?= $general_inventory['description'] == '' ? 'style="display:none;"' : '' ?>><input type="checkbox" name="description" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" value="" onchange="$(this).closest('label').hide(); $(this).closest('.tab-section').find('.form-group').show();"> Show All Fields</label>
			<?php if(strpos($value_config,',Inventory General Shipment Count Weight,') !== FALSE) { ?>
				<h4>Shipment Piece <?= $general_line_item.($general_inventory['piece_type'] != '' ? ': '.$general_inventory['piece_type'] : '') ?></h4>
			<?php } ?>
			<?php foreach($field_sort_order as $field_sort_field) { ?>
				<?php if($general_line_item > 1 && strpos($value_config,',Inventory General Piece Copy,') !== FALSE && $field_sort_field == 'Inventory General Piece Copy') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">&nbsp;</label>
						<div class="col-sm-8">
							<label class="form-checbox"><input type="checkbox" value="1" <?= $general_inventory['contact_info'] > 0 ? 'checked' : '' ?> name="contact_info" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" onchange="if(this.checked) { copyPieceOne(this); }">Same As Piece #1</label>
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } else if($general_line_item == 1 && strpos($value_config,',Inventory General All Copy,') !== FALSE && $field_sort_field == 'Inventory General All Copy') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">&nbsp;</label>
						<div class="col-sm-8">
							<button class="btn brand-btn" onclick="copyAllPiece(); return false;">Copy Details to Other Pieces</button>
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory General Piece Count Type,') !== FALSE && $field_sort_field == 'Inventory General Piece Count Type') { ?>
					<div class="form-group" <?= $general_inventory['description'] == '' || $general_inventory['piece_num'] > 0 || $general_inventory['piece_type'] != '' ? '' : 'style="display:none;"' ?>>
						<label class="control-label col-sm-4">Shipment Count:</label>
						<div class="col-sm-8">
							<div class="col-sm-6">
								<input type="number" min=0 step="1" name="piece_num" placeholder="Shipment Count" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" class="form-control" value="<?= $general_inventory['piece_num'] ?>">
							</div>
							<div class="col-sm-6">
								<?php if(count($piece_types) > 0) { ?>
									<select name="piece_type" data-placeholder="Select Piece Type" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" class="chosen-select-deselect"><option></option>
										<?php foreach($piece_types as $piece_type_name) { ?>
											<option <?= $general_inventory['piece_type'] == $piece_type_name ? 'selected' : '' ?> value="<?= $piece_type_name ?>"><?= $piece_type_name ?></option>
										<?php } ?>
										<?php if(!in_array($general_inventory['piece_type'],$piece_types)) { ?>
											<option selected value="<?= $general_inventory['piece_type'] ?>"><?= $general_inventory['piece_type'] ?></option>
										<?php } ?>
									</select>
								<?php } else { ?>
									<input type="text" name="piece_type" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" class="form-control" value="<?= $general_inventory['piece_type]'] ?>">
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory General Piece,') !== FALSE && $field_sort_field == 'Inventory General Piece') { ?>
					<div class="form-group" <?= $general_inventory['description'] == '' || $general_inventory['piece_num'] > 0 ? '' : 'style="display:none;"' ?>>
						<label class="control-label col-sm-4">Shipment Count:</label>
						<div class="col-sm-8">
							<input type="number" min=0 step="1" name="piece_num" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" class="form-control" value="<?= $general_inventory['piece_num'] ?>">
							<?php if((strpos($value_config,',Inventory General Weight') !== FALSE || strpos($value_config,',Inventory General Dimensions') !== FALSE)) { // && strpos($value_config,',Inventory General Piece Dim Weight') !== FALSE) {
								$label = [];
								if(strpos($value_config,',Inventory General Dimensions') !== FALSE) {
									$label[] = 'Dimensions';
								}
								if(strpos($value_config,',Inventory General Weight') !== FALSE) {
									$label[] = 'Weights';
								} ?>
								<label class="form-checkbox any-width"><input type="checkbox" class="setMultiDims" onchange="multiDimensions(this)" <?= strpos($general_inventory['dimensions'],'#*#') !== FALSE || strpos($general_inventory['weight'],'#*#') !== FALSE ? 'checked' : '' ?>>Enter <?= implode(' and ',$label) ?> per Piece</label>
							<?php } ?>
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory General Piece Type,') !== FALSE && $field_sort_field == 'Inventory General Piece Type') { ?>
					<div class="form-group" <?= $general_inventory['description'] == '' || $general_inventory['piece_type'] != '' ? '' : 'style="display:none;"' ?>>
						<label class="control-label col-sm-4">Piece Type:</label>
						<div class="col-sm-8"><div class="col-sm-12">
							<?php if(count($piece_types) > 0) { ?>
								<select name="piece_type" data-placeholder="Select Type" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" class="chosen-select-deselect"><option></option>
									<?php foreach($piece_types as $piece_type_name) { ?>
										<option <?= $general_inventory['piece_type'] == $piece_type_name ? 'selected' : '' ?> value="<?= $piece_type_name ?>"><?= $piece_type_name ?></option>
									<?php } ?>
									<?php if(!in_array($general_inventory['piece_type'],$piece_types)) { ?>
										<option selected value="<?= $general_inventory['piece_type'] ?>"><?= $general_inventory['piece_type'] ?></option>
									<?php } ?>
								</select>
							<?php } else { ?>
								<input type="text" name="piece_type" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" class="form-control" value="<?= $general_inventory['piece_type]'] ?>">
							<?php } ?>
						</div></div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory General Site,') !== FALSE && $field_sort_field == 'Inventory General Site') { ?>
					<div class="form-group" <?= $general_inventory['description'] == '' || $general_inventory['siteid'] != '' ? '' : 'style="display:none;"' ?>>
						<label class="control-label col-sm-4"><?= SITES_CAT ?>:</label>
						<div class="col-sm-8"><div class="col-sm-12">
							<select name="siteid[]" multiple data-concat="," data-placeholder="Select <?= SITES_CAT ?>" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" class="chosen-select-deselect">
								<?php if(!isset($site_list)) {
									$site_list = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, site_name, `display_name`, businessid FROM `contacts` WHERE `category`='".SITES_CAT."' AND deleted=0 ORDER BY IFNULL(NULLIF(`display_name`,''),`site_name`)"));
								}
								foreach($site_list as $site_row) { ?>
									<option <?= strpos(','.$general_inventory['siteid'].',',','.$site_row['contactid'].',') !== FALSE ? 'selected' : '' ?> value="<?= $site_row['contactid'] ?>"><?= $site_row['full_name'] ?></option>
								<?php } ?>
							</select>
						</div></div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory General PO Number,') !== FALSE && $field_sort_field == 'Inventory General PO Number') { ?>
					<div class="form-group" <?= $general_inventory['description'] == '' || $inventory['po_num'] != '' ? '' : 'style="display:none;"' ?>>
						<label class="control-label col-sm-4">Purchase Order #:</label>
						<div class="col-sm-8"><div class="col-sm-12">
							<input type="text" name="po_num" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" class="form-control" value="<?= $general_inventory['po_num'] ?>">
						</div></div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory General PO Line Read,') !== FALSE && $field_sort_field == 'Inventory General PO Line Read') { ?>
					<div class="form-group" <?= $general_inventory['description'] == '' || $general_inventory['po_line'] != '' ? '' : 'style="display:none;"' ?>>
						<label class="control-label col-sm-4">Purchase Order Line Item:</label>
						<div class="col-sm-8"><div class="col-sm-12">
							<input type="text" name="po_line" readonly class="form-control" value="<?= $general_inventory['po_line'] ?>">
						</div></div>
					</div>
					<div class="clearfix"></div>
				<?php } else if(strpos($value_config,',Inventory General PO Item,') !== FALSE && $field_sort_field == 'Inventory General PO Item') { ?>
					<div class="form-group" <?= $general_inventory['description'] == '' || $general_inventory['po_line'] != '' ? '' : 'style="display:none;"' ?>>
						<label class="control-label col-sm-4">Purchase Order Item:</label>
						<div class="col-sm-8"><div class="col-sm-12">
							<input type="text" name="po_line" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" class="form-control" value="<?= $general_inventory['po_line'] ?>">
						</div></div>
					</div>
					<div class="clearfix"></div>
				<?php } else if(strpos($value_config,',Inventory General PO Line Item,') !== FALSE && $field_sort_field == 'Inventory General PO Line Item') { ?>
					<div class="form-group" <?= $general_inventory['description'] == '' || $general_inventory['po_line'] != '' ? '' : 'style="display:none;"' ?>>
						<label class="control-label col-sm-4">Purchase Order Line Item:</label>
						<div class="col-sm-8"><div class="col-sm-12">
							<input type="text" name="po_line" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" class="form-control" value="<?= $general_inventory['po_line'] ?>">
						</div></div>
					</div>
					<div class="clearfix"></div>
				<?php } else if(strpos($value_config,',Inventory General PO Dropdown,') !== FALSE && $field_sort_field == 'Inventory General PO Dropdown') { ?>
					<div class="form-group">
						<label class="control-label col-sm-4">Purchase Order Line Item:</label>
						<div class="col-sm-8"><div class="col-sm-12">
							<select name="po_line" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" class="chosen-select-deselect"><option />
								<?php $line_num = $dbc->query("SELECT MAX(`po_line`) FROM `ticket_attached` WHERE `deleted`=0 AND `src_table`='inventory_general'")->fetch_array(MYSQLI_NUM)[0]; ?>
								<?php for($i = 10; $i <= 550; $i += 10) { ?>
									<option <?= $general_inventory['po_line'] == $i ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?></option>
								<?php } ?>
							</select>
						</div></div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory General Weight,') !== FALSE && $field_sort_field == 'Inventory General Weight') { ?>
					<?php $inv_weight_units = explode('#*#',$general_inventory['weight_units']);
					foreach(explode('#*#',$general_inventory['weight']) as $id => $inv_weight) {
						if($id == 0 || strpos($value_config,',Inventory General Piece Dim Weight,') !== FALSE) {
							$inv_dimensions = explode('x',$inv_dimension);
							$inv_dim_unit_list = explode('x',$inv_dim_units[$id]); ?>
							<div class="form-group multi_weights <?= strpos($general_inventory['dimensions'].$general_inventory['weight'],'#*#') !== FALSE ? '' : 'hidden' ?>" <?= $general_inventory['weight'] == '' || trim($general_inventory['dimensions'],' x#*') != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Piece Weight:</label>
								<div class="col-sm-8">
									<div class="col-sm-6">
										<input placeholder="Weight" type="number" name="weight" min=0 data-concat="#*#" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" value="<?= $inv_weight ?>" class="form-control">
									</div>
									<div class="col-sm-6">
										<select name="weight_units" data-concat="#*#" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" data-placeholder="Units" class="chosen-select-deselect">
											<option></option>
											<option <?= $inv_weight_units[$id] == 'kg' ? 'selected' : '' ?> value="kg">kg</option>
											<option <?= $inv_weight_units[$id] == 'lbs' ? 'selected' : '' ?> value="lbs">lbs</option>
										</select>
									</div>
								</div>
							</div>
						<?php }
					} ?>
					<div class="form-group single_dimensions <?= strpos($general_inventory['dimensions'].$general_inventory['weight'],'#*#') !== FALSE ? 'hidden' : '' ?>" <?= $general_inventory['description'] == '' || trim($general_inventory['weight'],' x') != '' ? '' : 'style="display:none;"' ?>>
						<label class="control-label col-sm-4">Weight:</label>
						<div class="col-sm-8">
							<div class="col-sm-<?= strpos($value_config,',Inventory General Units,') !== FALSE ? '6' : '12' ?>">
								<input type="number" min=0 step="1" name="weight<?= strpos($general_inventory['dimensions'].$general_inventory['weight'],'#*#') !== FALSE ? '_halt_add' : '' ?>" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" class="form-control" value="<?= explode('#*#',$general_inventory['weight'])[0] ?>">
							</div>
							<?php if(strpos($value_config,',Inventory General Units,') !== FALSE) { ?>
								<div class="col-sm-6">
									<select name="weight_units<?= strpos($general_inventory['dimensions'].$general_inventory['weight'],'#*#') !== FALSE ? '_halt_add' : '' ?>" data-placeholder="Select Units" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" class="chosen-select-deselect">
										<option></option>
										<option <?= explode('#*#',$general_inventory['weight_units'])[0] == 'kg' ? 'selected' : '' ?> value="kg">kg</option>
										<option <?= explode('#*#',$general_inventory['weight_units'])[0] == 'lbs' ? 'selected' : '' ?> value="lbs">lbs</option>
									</select>
								</div>
							<?php } ?>
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory General Dimensions,') !== FALSE && $field_sort_field == 'Inventory General Dimensions') {
					$inv_dim_units = explode('#*#',$general_inventory['dimension_units']);
					foreach(explode('#*#',$general_inventory['dimensions']) as $id => $inv_dimension) {
						if($id == 0 || strpos($value_config,',Inventory General Piece Dim Weight,') !== FALSE) {
							$inv_dimensions = explode('x',$inv_dimension); ?>
							<div class="form-group multi_dimensions <?= strpos($general_inventory['dimensions'].$general_inventory['weight'],'#*#') !== FALSE ? '' : 'hidden' ?>" <?= $general_inventory['description'] == '' || trim($general_inventory['dimensions'],' x#*') != '' ? '' : 'style="display:none;"' ?>>
								<label class="control-label col-sm-4">Piece Dimension (LxWxH):</label>
								<div class="col-sm-8">
									<input type="hidden" name="dimensions" data-concat="#*#" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" value="<?= $inv_dimension ?>">
									<input type="hidden" name="dimension_units" data-concat="#*#" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" value="<?= $inv_dim_units[$id] ?>">
									<div class="col-sm-3">
										<input placeholder="Length" type="number" min=0 value="<?= trim(explode('x', $inv_dimension)[0]) ?>" class="dim_l form-control">
									</div>
									<div class="col-sm-3">
										<input placeholder="Width" type="number" min=0 value="<?= trim(explode('x', $inv_dimension)[1]) ?>" class="dim_w form-control">
									</div>
									<div class="col-sm-3">
										<input placeholder="Height" type="number" min=0 value="<?= trim(explode('x', $inv_dimension)[2]) ?>" class="dim_h form-control">
									</div>
									<div class="col-sm-3">
										<select data-placeholder="Units" class="dimunit_h chosen-select-deselect">
											<option></option>
											<option <?= in_array(strtolower($inv_dim_units[$id]),['mm','mms','millimeter','millimetre','millimeters','millimetres']) ? 'selected' : '' ?> value="mm">mm</option>
											<option <?= in_array(strtolower($inv_dim_units[$id]),['cm','cms','centimeter','centimetre','centimeters','centimetres']) ? 'selected' : '' ?> value="cm">cm</option>
											<option <?= in_array(strtolower($inv_dim_units[$id]),['m','meter','metre','meters','metres']) ? 'selected' : '' ?> value="m">m</option>
											<option <?= in_array(strtolower($inv_dim_units[$id]),['in','inch','inches']) ? 'selected' : '' ?> value="in">in</option>
											<option <?= in_array(strtolower($inv_dim_units[$id]),['ft','feet','foot']) ? 'selected' : '' ?> value="ft">ft</option>
										</select>
									</div>
								</div>
							</div>
						<?php }
					} ?>
					<div class="form-group single_dimensions <?= strpos($general_inventory['dimensions'].$general_inventory['weight'],'#*#') !== FALSE ? 'hidden' : '' ?>" <?= $general_inventory['description'] == '' || trim($general_inventory['dimensions'],' x') != '' ? '' : 'style="display:none;"' ?>>
						<label class="control-label col-sm-4">Piece Dimension (LxWxH):</label>
						<div class="col-sm-8">
							<div class="col-sm-<?= strpos($value_config,',Inventory General Dimension Units,') !== FALSE ? '3' : '4' ?>">
								<input type="text" name="dimensions<?= strpos($general_inventory['dimensions'].$general_inventory['weight'],'#*#') !== FALSE ? '_halt_add' : '' ?>" placeholder="Length" data-concat="x" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" class="form-control" value="<?= trim(explode('x',explode('#*#',$general_inventory['dimensions'])[0])[0]) ?>">
							</div>
							<div class="col-sm-<?= strpos($value_config,',Inventory General Dimension Units,') !== FALSE ? '3' : '4' ?>">
								<input type="text" name="dimensions<?= strpos($general_inventory['dimensions'].$general_inventory['weight'],'#*#') !== FALSE ? '_halt_add' : '' ?>" placeholder="Width" data-concat="x" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" class="form-control" value="<?= trim(explode('x',explode('#*#',$general_inventory['dimensions'])[0])[1]) ?>">
							</div>
							<div class="col-sm-<?= strpos($value_config,',Inventory General Dimension Units,') !== FALSE ? '3' : '4' ?>">
								<input type="text" name="dimensions<?= strpos($general_inventory['dimensions'].$general_inventory['weight'],'#*#') !== FALSE ? '_halt_add' : '' ?>" placeholder="Height" data-concat="x" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" class="form-control" value="<?= trim(explode('x',explode('#*#',$general_inventory['dimensions'])[0])[2]) ?>">
							</div>
							<?php if(strpos($value_config,',Inventory General Dimension Units,') !== FALSE) { ?>
								<div class="col-sm-3">
									<select name="dimension_units<?= strpos($general_inventory['dimensions'].$general_inventory['weight'],'#*#') !== FALSE ? '_halt_add' : '' ?>" data-placeholder="Select Units" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" class="chosen-select-deselect">
										<option></option>
										<option <?= in_array(strtolower($general_inventory['dimension_units']),['mm','mms','millimeter','millimetre','millimeters','millimetres']) ? 'selected' : '' ?> value="mm">mm</option>
										<option <?= in_array(strtolower($general_inventory['dimension_units']),['cm','cms','centimeter','centimetre','centimeters','centimetres']) ? 'selected' : '' ?> value="cm">cm</option>
										<option <?= in_array(strtolower($general_inventory['dimension_units']),['m','meter','metre','meters','metres']) ? 'selected' : '' ?> value="m">m</option>
										<option <?= in_array(strtolower($general_inventory['dimension_units']),['in','inch','inches']) ? 'selected' : '' ?> value="in">in</option>
										<option <?= in_array(strtolower($general_inventory['dimension_units']),['ft','feet','foot']) ? 'selected' : '' ?> value="ft">ft</option>
									</select>
								</div>
							<?php } ?>
						</div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory General Complete,') !== FALSE && $field_sort_field == 'Inventory General Complete') { ?>
					<div class="form-group" <?= $general_inventory['description'] == '' || $general_inventory['completed'] > 0 ? '' : 'style="display:none;"' ?>>
						<label class="control-label col-sm-4">Completed:</label>
						<div class="col-sm-8">
							<label class="form-checkbox"><input type="checkbox" name="completed" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" value="1" <?= $general_inventory['completed'] > 0 ? 'checked' : '' ?> class="no_time"> Yes</label>
							<a href="" onclick="sendInventoryReminder($(this).closest('div').find('input').data('id')); return false;">Send Reminder</a>
						</div>
					</div>
				<?php } ?>
				<?php if(strpos($value_config,',Inventory General Notes,') !== FALSE && $field_sort_field == 'Inventory General Notes') { ?>
					<div class="form-group" <?= $general_inventory['description'] == '' || $general_inventory['siteid'] != '' ? '' : 'style="display:none;"' ?>>
						<label class="control-label col-sm-4">Notes:</label>
						<div class="col-sm-8"><div class="col-sm-12">
							<textarea name="notes" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table"><?= html_entity_decode($general_inventory['notes']) ?></textarea>
						</div></div>
					</div>
					<div class="clearfix"></div>
				<?php } ?>
			<?php } ?>
			<input type="hidden" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" name="deleted" value="0">
			<?php if(strpos($value_config,',Inventory General Manual Add Pieces,') === FALSE) { ?>
				<img class="inline-img pull-right" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
				<img class="inline-img pull-right" onclick="remMulti(this);" src="../img/remove.png">
			<?php } ?>
			<?php if(strpos($value_config,',Inventory General Manual Remove Pieces,') !== FALSE) { ?>
				<img class="inline-img pull-right" onclick="remMulti(this); setPieceNumbers(); updatePieceCount(); reload_sidebar();" src="../img/remove.png">
			<?php } ?>
			<?php if(strpos($value_config,',Inventory General Detail,') !== FALSE && $pallet_line['pallet'] == '' && $general_description != 'import') {
				$general_item = ['id'=>$general_inventory['id'],'piece_type'=>$general_inventory['piece_type'],'piece_num'=>$general_inventory['piece_num']]; ?>
				<div class="col-sm-4"></div>
				<div><label class="form-checkbox any-width"><input type="checkbox" name="position" data-table="ticket_attached" data-id="<?= $general_inventory['id'] ?>" data-id-field="id" data-type="inventory_general" data-type-field="src_table" value="1" <?= $general_inventory['position'] > 0 ? 'checked' : '' ?> onchange="if(this.checked) { $(this).closest('div').nextAll('.general_piece_details').show(); } else { $(this).closest('div').nextAll('.general_piece_details').hide(); }"> Add Piece Details</label></div>
				<div class="general_piece_details" style="<?= $general_inventory['position'] > 0 ? '' : 'display:none;' ?>">
					<hr />
					<?php $field_list = $accordion_list['Inventory Detail'];
					$field_sort_order = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_fields` WHERE `ticket_type` = '".(empty($ticket_type) ? 'tickets' : 'tickets_'.$ticket_type)."' AND `accordion` = 'Inventory Detail'"))['fields'];
					if(empty($field_sort_order)) {
						$field_sort_order = $value_config;
					}
					$field_sort_order = explode(',', $field_sort_order);
					foreach ($field_list as $default_field) {
						if(!in_array($default_field, $field_sort_order)) {
							$field_sort_order[] = $default_field;
						}
					}
					include('add_ticket_inventory_detailed.php'); ?>
					<div class="clearfix"></div>
				</div>
			<?php } ?>
			<div class="clearfix"></div>
		</div>
	<?php } else { ?>
		<?php if(strpos($value_config,',Inventory General Piece,') !== FALSE || strpos($value_config,',Inventory General Piece Count Type,') !== FALSE) { ?>
			<div class="form-group" <?= $general_inventory['description'] == '' || $general_inventory['piece_num'] != '' ? '' : 'style="display:none;"' ?>>
				<label class="control-label col-sm-4"># of Pieces:</label>
				<div class="col-sm-8">
					<?= $general_inventory['piece_num'] ?>
				</div>
			</div>
			<div class="clearfix"></div>
			<?php $pdf_contents[] = ['# of Pieces', $general_inventory['piece_num']]; ?>
		<?php } ?>
		<?php if(strpos($value_config,',Inventory General Site,') !== FALSE || strpos($value_config,',Inventory General Site,') !== FALSE) { ?>
			<div class="form-group" <?= $general_inventory['description'] == '' || $general_inventory['siteid'] != '' ? '' : 'style="display:none;"' ?>>
				<label class="control-label col-sm-4"><?= SITES_CAT ?>:</label>
				<div class="col-sm-8">
					<?= get_contact($dbc, $general_inventory['siteid']) ?>
				</div>
			</div>
			<div class="clearfix"></div>
			<?php $pdf_contents[] = [SITES_CAT, get_contact($dbc, $general_inventory['siteid'])]; ?>
		<?php } ?>
		<?php if(strpos($value_config,',Inventory General Piece Type,') !== FALSE || strpos($value_config,',Inventory General Piece Count Type,') !== FALSE) { ?>
			<div class="form-group" <?= $general_inventory['description'] == '' || $general_inventory['piece_type'] != '' ? '' : 'style="display:none;"' ?>>
				<label class="control-label col-sm-4">Piece Type:</label>
				<div class="col-sm-8">
					<?= $general_inventory['piece_type'] ?>
				</div>
			</div>
			<div class="clearfix"></div>
			<?php $pdf_contents[] = ['Piece Type', $general_inventory['piece_type']]; ?>
		<?php } ?>
		<?php if(strpos($value_config,',Inventory General PO Number,') !== FALSE) { ?>
			<div class="form-group" <?= $general_inventory['description'] == '' || $general_inventory['po_num'] != '' ? '' : 'style="display:none;"' ?>>
				<label class="control-label col-sm-4">Purchase Order Item:</label>
				<div class="col-sm-8">
					<?= $general_inventory['po_num'] ?>
				</div>
			</div>
			<div class="clearfix"></div>
			<?php $pdf_contents[] = ['Purchase Order Number', $general_inventory['po_num']]; ?>
		<?php } ?>
		<?php if(strpos($value_config,',Inventory General PO Item,') !== FALSE) { ?>
			<div class="form-group" <?= $general_inventory['description'] == '' || $general_inventory['po_line'] != '' ? '' : 'style="display:none;"' ?>>
				<label class="control-label col-sm-4">Purchase Order Item:</label>
				<div class="col-sm-8">
					<?= $general_inventory['po_line'] ?>
				</div>
			</div>
			<div class="clearfix"></div>
			<?php $pdf_contents[] = ['Purchase Order Item', $general_inventory['po_line']]; ?>
		<?php } else if(strpos($value_config,',Inventory General PO Line Item,') !== FALSE) { ?>
			<div class="form-group" <?= $general_inventory['description'] == '' || $general_inventory['po_line'] != '' ? '' : 'style="display:none;"' ?>>
				<label class="control-label col-sm-4">Purchase Order Line Item:</label>
				<div class="col-sm-8">
					<?= $general_inventory['po_line'] ?>
				</div>
			</div>
			<div class="clearfix"></div>
			<?php $pdf_contents[] = ['Purchase Order Line Item', $general_inventory['po_line']]; ?>
		<?php } else if(strpos($value_config,',Inventory General PO Line Read,') !== FALSE || strpos($value_config,',Inventory General PO Dropdown,') !== FALSE) { ?>
			<div class="form-group" <?= $general_inventory['description'] == '' || $general_inventory['po_line'] != '' ? '' : 'style="display:none;"' ?>>
				<label class="control-label col-sm-4">Purchase Order Line Item:</label>
				<div class="col-sm-8">
					<?= $general_inventory['po_line'] ?>
				</div>
			</div>
			<div class="clearfix"></div>
			<?php $pdf_contents[] = ['Purchase Order Line Item', $general_inventory['po_line']]; ?>
		<?php } ?>
		<?php if(strpos($value_config,',Inventory General Weight,') !== FALSE) { ?>
			<div class="form-group" <?= $general_inventory['description'] == '' || trim($general_inventory['weight'],' x#*') != '' ? '' : 'style="display:none;"' ?>>
				<label class="control-label col-sm-4">Weight:</label>
				<div class="col-sm-8">
					<?= $general_inventory['weight'].$general_inventory['weight_units'] ?>
				</div>
			</div>
			<div class="clearfix"></div>
			<?php $pdf_contents[] = ['Weight', $general_inventory['weight'].$general_inventory['weight_units']]; ?>
		<?php } ?>
		<?php if(strpos($value_config,',Inventory General Dimensions,') !== FALSE) { ?>
			<div class="form-group" <?= $general_inventory['description'] == '' || trim($general_inventory['dimensions'],' x#*') != '' ? '' : 'style="display:none;"' ?>>
				<label class="control-label col-sm-4">Piece Dimension (LxWxH):</label>
				<?php $inv_dim_units = explode('#*#',$general_inventory['dimension_units']);
				$echo_dimensions = '';
				foreach(explode('#*#',$general_inventory['dimensions']) as $id => $inv_dimension) {
					$inv_dimensions = explode('x',$inv_dimension);
					$inv_dim_unit_list = explode('x',$inv_dim_units[$id]);
					$echo_dimensions .= $inv_dimension[0].$inv_dim_unit_list[0].'x'.$inv_dimension[1].$inv_dim_unit_list[1].'x'.$inv_dimension[2].$inv_dim_unit_list[2].'<br />';
				}
				echo $echo_dimensions; ?>
			</div>
			<div class="clearfix"></div>
			<?php $pdf_contents[] = ['Piece Dimension (LxWxH)', (1 === preg_match('~[0-9]~', $echo_dimensions) ? $echo_dimensions : '')]; ?>
		<?php } ?>
		<?php if(strpos($value_config,',Inventory General Notes,') !== FALSE) { ?>
			<div class="form-group" <?= $general_inventory['description'] == '' || $general_inventory['notes'] != '' ? '' : 'style="display:none;"' ?>>
				<label class="control-label col-sm-4">Notes:</label>
				<?= html_entity_decode($general_inventory['notes']) ?>
			</div>
			<div class="clearfix"></div>
			<?php $pdf_contents[] = ['Piece Notes', html_entity_decode($general_inventory['notes'])]; ?>
		<?php } ?>
		<?php if(strpos($value_config,',Inventory General Complete,') !== FALSE) { ?>
			<div class="form-group" <?= $general_inventory['description'] == '' || $general_inventory['complete'] != '' ? '' : 'style="display:none;"' ?>>
				<label class="control-label col-sm-4">Completed:</label>
				<div class="col-sm-8"><?= $general_inventory['complete'] > 0 ? 'Yes' : 'No' ?></div>
			</div>
			<div class="clearfix"></div>
			<?php $pdf_contents[] = ['Completed', $general_inventory['complete'] > 0 ? 'Yes' : 'No']; ?>
		<?php } ?>
		<hr />
		<?php if(strpos($value_config,',Inventory General Detail,') !== FALSE && $pallet_line['pallet'] == '' && $general_description != 'import') {
			$general_item = ['id'=>$general_inventory['id'],'piece_type'=>$general_inventory['piece_type'],'piece_num'=>$general_inventory['piece_num']]; ?>
			<div class="general_piece_details" style="<?= $general_inventory['position'] > 0 ? '' : 'display:none;' ?>">
				<hr />
				<?php $field_list = $accordion_list['Inventory Detail'];
				$field_sort_order = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_fields` WHERE `ticket_type` = '".(empty($ticket_type) ? 'tickets' : 'tickets_'.$ticket_type)."' AND `accordion` = 'Inventory Detail'"))['fields'];
				if(empty($field_sort_order)) {
					$field_sort_order = $value_config;
				}
				$field_sort_order = explode(',', $field_sort_order);
				foreach ($field_list as $default_field) {
					if(!in_array($default_field, $field_sort_order)) {
						$field_sort_order[] = $default_field;
					}
				}
				include('add_ticket_inventory_detailed.php'); ?>
				<div class="clearfix"></div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php $total_shipment_weight += $general_inventory['weight'];
	$total_shipment_count += (trim($general_inventory['piece_type']) != '' ? 1 : 0);
	$total_shipment_weight_units = (explode('#*#',$general_inventory['weight_units'])[0] ?: $total_shipment_weight_units);
} while($general_inventory = mysqli_fetch_assoc($general_inventory_list)); ?>
<?php if(strpos($value_config,',Inventory General Total Summary,') !== FALSE) { ?>
	<div class="clearfix"></div>
	<hr />
	<?php if($access_all > 0) { ?>
		<div class="form-group">
			<label class="control-label col-sm-4">Total Shipment Count &amp; Total Weight: <a href="" class="small" onclick="$(this).closest('label').next('div').find('input:visible').removeAttr('readonly').first().focus(); return false;"><img class="inline-img" title="Edit" src="../img/icons/ROOK-edit-icon.png"></a></label>
			<div class="col-sm-8" data-append-note="Edited By <?= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']) ?>">
				<div class="col-sm-4">
					<input type="number" min="0" step="any" name="total_shipment_count" placeholder="Total Shipment Count" readonly class="form-control" value="<?= $total_shipment_count ?>">
					<?php if($general_shipment['qty'] != $total_shipment_count) { ?>
						<span class="text-red">The shipment count was <?= $general_shipment['qty'] ?></span>
					<?php } ?>
				</div>
				<div class="col-sm-4">
					<input type="number" min="0" step="any" name="total_shipment_weight" placeholder="Total Shipment Weight" readonly class="form-control" value="<?= $total_shipment_weight ?>">
					<?php if($general_shipment['weight'] != $total_shipment_weight) { ?>
						<span class="text-red">The shipment weight was <?= $general_shipment['weight'] ?></span>
					<?php } ?>
				</div>
				<div class="col-sm-4">
					<input name="total_shipment_weight_units" readonly class="form-control" value="<?= $total_shipment_weight_units ?>">
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	<?php } else { ?>
		<div class="form-group">
			<label class="control-label col-sm-4">Total Shipment Count &amp; Total Weight:</label>
			<div class="col-sm-8">
				<div class="col-sm-6">
					<?= $general_shipment['qty]'] ?>
				</div>
				<div class="col-sm-6">
					<?= $general_shipment['weight]'].' '.$general_shipment['weight_units'] ?>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<?php $pdf_contents[] = ['Total Shipment Count & Total Weight', $general_shipment['qty]'].'<br>'.$general_shipment['weight]'].' '.$general_shipment['weight_units']]; ?>
	<?php }
}
if(strpos($value_config,',Inventory General Detail by Pallet,') !== FALSE && $pallet_line['pallet'] != '' || $general_description == 'import') {
	$pallet_i = 0;
	do { ?>
		<div id="tab_section_general_pallet_<?= $pallet_i++ ?>" class="tab-section">
			<h4><?= ($pallet_line['pallet'] != '' ? $pallet_line['pallet'] : 'No Pallet Assigned').': '.$pallet_line['items'] ?></h4>
			<?php if(strpos($value_config,',Inventory General Pallet Default Locked,') === FALSE || in_array('inventory_general_pallet_'.config_safe_str($pallet_line['pallet']),$unlocked_tabs)) { ?>
				<?php $general_item = ['id'=>0,'pallet'=>($pallet_line['pallet'] != '' ? $pallet_line['pallet'] : '*UNDEFINED*')];
				$general_inventory['description'] = 'hidden'; ?>
				<div class="general_pallet_details">
					<hr />
					<?php $field_list = $accordion_list['Inventory Detail'];
					$field_sort_order = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_fields` WHERE `ticket_type` = '".(empty($ticket_type) ? 'tickets' : 'tickets_'.$ticket_type)."' AND `accordion` = 'Inventory Detail'"))['fields'];
					if(empty($field_sort_order)) {
						$field_sort_order = $value_config;
					}
					$field_sort_order = explode(',', $field_sort_order);
					foreach ($field_list as $default_field) {
						if(!in_array($default_field, $field_sort_order)) {
							$field_sort_order[] = $default_field;
						}
					}
					include('add_ticket_inventory_detailed.php'); ?>
					<div class="clearfix"></div>
					<input type="hidden" name="lock_tabs" value="<?= 'inventory_general_pallet_'.config_safe_str($pallet_line['pallet']) ?>" data-toggle="1">
				</div>
			<?php } else { ?>
				<em class="cursor-hand tab_lock_toggle" onclick="window.location.reload();">Click this section to unlock.<img class="inline-img" src="../img/icons/lock.png"></em>
				<input type="hidden" name="lock_tabs" value="<?= 'inventory_general_pallet_'.config_safe_str($pallet_line['pallet']) ?>" data-toggle="0">
			<?php } ?>
		</div>
	<?php } while($pallet_line = $ticket_pallet_list->fetch_assoc());
} ?>