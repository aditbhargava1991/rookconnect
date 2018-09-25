<?php $this_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($ticket_type) ? 'tickets' : 'tickets_'.$ticket_type)."' AND `accordion` = 'Transport Carrier'"))['accordion_name']; ?>
<?php $origin = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `ticketid`='$ticketid' AND `deleted`=0 AND `type`='origin'"));
if($access_all > 0) {
	$origin_save = 'data-table="ticket_schedule" data-id="'.$origin['id'].'" data-id-field="id" data-type="origin" data-type-field="type"';
} else {
	$origin_save = "readonly";
}
$transport_types = array_filter(explode(',',get_config($dbc, 'transport_types')));
$default_contact_category = get_config($dbc, 'transport_carrier_category');
$contact_category = ($ticket_type == '' ? $default_contact_category : get_config($dbc, 'transport_carrier_category_'.$ticket_type));
if($contact_category == '') {
	$contact_category = $default_contact_category;
}
$carriers = sort_contacts_query($dbc->query("SELECT `first_name`, `last_name`, `name`, `contactid` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `category`='$contact_category'"),'first_name'); ?>
<h3><?= !empty($this_accordion) ? $this_accordion : (!empty($renamed_accordion) ? $renamed_accordion.' - '.$contact_category : $contact_category.' Details') ?></h3>
<?php foreach ($field_sort_order as $field_sort_field) { ?>
	<?php if (strpos($value_config, ','."Transport Carrier".',') !== FALSE && $field_sort_field == 'Transport Carrier') { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?= $contact_category ?> Name:</label>
			<?php if($origin_save == 'readonly') {
				echo ($origin['carrier'] > 0 ? get_contact($dbc, $origin['carrier'], 'name') : $origin['carrier']);
			} else { ?>
				<div class="col-sm-7 select-div" style="<?= $origin['carrier'] > 0 || $origin['carrier'] == '' ? '' : 'display:none;' ?>">
					<select name="carrier" class="chosen-select-deselect form-control" data-placeholder="The company responsible for movement of the shipment..." data-category="<?= $contact_category ?>" <?= $origin_save ?>>
						<option></option>
						<?php foreach($carriers as $carrier) { ?>
							<option <?= $origin['carrier'] == $carrier['contactid'] ? 'selected' : '' ?> value="<?= $carrier['contactid'] ?>"><?= $carrier['name'].' '.$carrier['first_name'].' '.$carrier['last_name'] ?></option>
						<?php } ?>
						<option value="ADD_NEW">Add New <?= $contact_category ?></option>
						<option value="MANUAL">One Time <?= $contact_category ?></option>
					</select>
				</div>
					<div class="col-sm-1 select-div" style="<?= $origin['carrier'] > 0 || $origin['carrier'] == '' ? '' : 'display:none;' ?>">
						<a href="" onclick="overlayIFrameSlider('../Contacts/contacts_inbox.php?fields=all_fields&edit='+$(this).closest('.form-group').find('select').val(), '75%', true, true);return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
						<a href="" onclick="$(this).closest('.form-group').find('select').val('ADD_NEW').change(); return false;"><img class="inline-img pull-right" data-history-label="New <?= $contact_category ?>" src="../img/icons/ROOK-add-icon.png"></a>
					</div>
				<div class="col-sm-8 manual-div" style="<?= $origin['carrier'] > 0 || $origin['carrier'] == '' ? 'display:none;' : '' ?>">
					<input type="text" name="carrier" class="form-control" data-one-time="true" <?= $origin_save ?> value="<?= $origin['carrier'] > 0 ? '' : $origin['carrier'] ?>">
					<label class="form-checkbox"><input checked type="checkbox" name="one_time" onchange="if(!this.checked) { $(this).closest('.form-group').find('.select-div').show(); $(this).closest('.manual-div').hide(); }"> One Time Only Contact</label>
				</div>
			<?php } ?>
		</div>
		<?php $pdf_contents[] = [$contact_category.' Name', ($origin['carrier'] > 0 ? get_contact($dbc, $origin['carrier'], 'name') : $origin['carrier'])]; ?>
	<?php } ?>
	<?php if (strpos($value_config, ','."Transport Type".',') !== FALSE && $field_sort_field == 'Transport Type') { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Shipment Tab:</label>
			<div class="col-sm-8">
				<?php if($origin_save == 'readonly') {
					echo $origin['details'];
				} else { ?><?php if(count($transport_types) > 0) { ?>
					<select name="details" data-placeholder="Tabs of shipments (e.g. local cartage)..." class="chosen-select-deselect" <?= $origin_save ?>><option></option>
						<?php foreach(explode(',',get_config($dbc, 'transport_types')) as $transport_type_name) { ?>
							<option <?= $origin['details'] == $transport_type_name ? 'selected' : '' ?> value="<?= $transport_type_name ?>"><?= $transport_type_name ?></option>
						<?php }
						if(!in_array($origin['details'],explode(',',get_config($dbc,'transport_types')))) { ?>
							<option selected value="<?= $origin['details'] ?>"><?= $origin['details'] ?></option>
						<?php } ?>
					</select>
				<?php } else { ?>
						<input type="text" name="details" class="form-control" <?= $origin_save ?> value="<?= $origin['details'] ?>" placeholder="Tabs of shipments (e.g. local cartage)...">
					<?php } ?>
				<?php } ?>
			</div>
		</div>
		<?php $pdf_contents[] = ['Shipment Type', $origin['details']]; ?>
	<?php } ?>
	<?php if (strpos($value_config, ','."Transport Number".',') !== FALSE && $field_sort_field == 'Transport Number') { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Bill/AWB Lading #:</label>
			<div class="col-sm-8">
				<?php if($origin_save == 'readonly') {
					echo $origin['lading_number'];
				} else { ?>
					<input type="text" name="lading_number" class="form-control" <?= $origin_save ?> value="<?= $origin['lading_number'] ?>" placeholder="The bill of lading # provided by above carrier...">
				<?php } ?>
			</div>
		</div>
		<?php $pdf_contents[] = ['Bill of Lading #', $origin['lading_number']]; ?>
	<?php } ?>
	<?php if (strpos($value_config, ','."Transport Billed".',') !== FALSE && $field_sort_field == 'Transport Billed') { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Has This File Been Billed:</label>
			<div class="col-sm-8">
				<?php if($origin_save == 'readonly') {
					echo $origin['map_link'] > 0 ? 'No' : 'Yes';
				} else { ?>
					<select name="billed" class="chosen-select-deselect form-control" <?= $origin_save ?>>
						<option></option>
						<option value="0" <?= $origin['billed'] != 1 ? 'selected' : '' ?>>No</option>
						<option value="1" <?= $origin['billed'] == 1 ? 'selected' : '' ?>>Yes</option>
					</select>
				<?php } ?>
			</div>
		</div>
		<?php $pdf_contents[] = ['Has This File Been Billed', $origin['map_link'] > 0 ? 'No' : 'Yes']; ?>
	<?php } ?>
	<?php if (strpos($value_config, ','."Transport Container".',') !== FALSE && $field_sort_field == 'Transport Container') { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Container #:</label>
			<div class="col-sm-8">
				<?php if($origin_save == 'readonly') {
					echo $origin['container'];
				} else { ?>
					<input type="text" name="container" class="form-control" <?= $origin_save ?> value="<?= $origin['container'] ?>" placeholder="The container #...">
				<?php } ?>
			</div>
		</div>
		<?php $pdf_contents[] = ['Container #', $origin['container']]; ?>
	<?php } ?>
	<?php if (strpos($value_config, ','."Transport Manifest".',') !== FALSE && $field_sort_field == 'Transport Manifest') { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Manifest #:</label>
			<div class="col-sm-8">
				<?php if($origin_save == 'readonly') {
					echo $origin['manifest_num'];
				} else { ?>
					<input type="text" name="manifest_num" class="form-control" <?= $origin_save ?> value="<?= $origin['manifest_num'] ?>" placeholder="Cross docking bill of lading #...">
				<?php } ?>
			</div>
		</div>
		<?php $pdf_contents[] = ['Manifest #', $origin['manifest_num']]; ?>
	<?php } ?>
	<?php if (strpos($value_config, ','."Transport Ship Date".',') !== FALSE && $field_sort_field == 'Transport Ship Date') { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Date of Shipment:</label>
			<div class="col-sm-8">
				<?php if($origin_save == 'readonly') {
					echo $origin['to_do_date'];
				} else { ?>
					<input type="text" name="to_do_date" class="form-control datepicker" <?= $origin_save ?> value="<?= $origin['to_do_date'] ?>">
				<?php } ?>
			</div>
		</div>
		<?php $pdf_contents[] = ['Date of Shipment', $origin['to_do_date']]; ?>
	<?php } ?>
	<?php if (strpos($value_config, ','."Transport Arrive Date".',') !== FALSE && $field_sort_field == 'Transport Arrive Date') { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Date of Arrival:</label>
			<div class="col-sm-8">
				<?php if($destination_save == 'readonly') {
					echo $destination['to_do_date'];
				} else { ?>
					<input type="text" name="to_do_date" class="form-control datepicker" <?= $destination_save ?> value="<?= $destination['to_do_date'] ?>">
				<?php } ?>
			</div>
		</div>
		<?php $pdf_contents[] = ['Date of Arrival', $destination['to_do_date']]; ?>
	<?php } ?>
	<?php if (strpos($value_config, ','."Transport Warehouse".',') !== FALSE && $field_sort_field == 'Transport Warehouse') { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Warehouse Location:</label>
			<?php if($origin_save == 'readonly') {
				echo ($origin['warehouse_location'] > 0 ? get_contact($dbc, $origin['warehouse_location'], 'name') : $origin['warehouse_location']);
			} else { ?>
				<div class="col-sm-7 select-div" style="<?= $origin['warehouse_location'] > 0 || $origin['warehouse_location'] == '' ? '' : 'display:none;' ?>">
					<select name="warehouse_location" class="chosen-select-deselect" <?= $origin_save ?> data-category="Warehouse" data-placeholder="Select Warehouse"><option />
						<?php foreach($warehouse_contact_list as $warehouse_contact) { ?>
							<option <?= $warehouse_contact['contactid'] == $origin['warehouse_location'] || trim($warehouse_contact['name'].' '.$warehouse_contact['first_name'].' '.$warehouse_contact['last_name']) == $origin['warehouse_location'] ? 'selected' : '' ?> value="<?= $warehouse_contact['contactid'] ?>"><?= $warehouse_contact['name'].' '.$warehouse_contact['first_name'].' '.$warehouse_contact['last_name'] ?></option>
						<?php } ?>
						<option value="ADD_NEW">Add New Warehouse</option>
					</select>
				</div>
				<div class="col-sm-1 select-div" style="<?= $origin['warehouse_location'] > 0 || $origin['warehouse_location'] == '' ? '' : 'display:none;' ?>">
					<a href="" onclick="if($(this).closest('.form-group').find('select').val() > 0) { overlayIFrameSlider('../Contacts/contacts_inbox.php?fields=all_fields&edit='+$(this).closest('.form-group').find('select').val(), '75%', true, true) };return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					<a href="" onclick="$(this).closest('.form-group').find('select').val('ADD_NEW').change(); return false;"><img class="inline-img pull-right" data-history-label="Warehouse Location" src="../img/icons/ROOK-add-icon.png"></a>
				</div>
			<?php } ?>
		</div>
		<?php $pdf_contents[] = ['Warehouse Location', ($origin['warehouse_location'] > 0 ? get_contact($dbc, $origin['warehouse_location'], 'name') : $origin['warehouse_location'])]; ?>
	<?php } ?>
<?php } ?>