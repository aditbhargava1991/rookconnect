<?php $this_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($ticket_type) ? 'tickets' : 'tickets_'.$ticket_type)."' AND `accordion` = 'Transport Destination'"))['accordion_name']; ?>
<h3><?= !empty($this_accordion) ? $this_accordion : (!empty($renamed_accordion) ? $renamed_accordion : 'Transport Log').' - Destination' ?></h3>
<?php $destination = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `ticketid`='$ticketid' AND `deleted`=0 AND `type`='destination'"));
if($access_all > 0) {
	$destination_save = 'data-table="ticket_schedule" data-id="'.$destination['id'].'" data-id-field="id" data-type="destination" data-type-field="type"';
} else {
	$destination_save = "readonly";
}
$transport_types = array_filter(explode(',',get_config($dbc, 'transport_types')));
$default_contact_category = get_config($dbc, 'transport_destination_contact');
$contact_category = ($ticket_type == '' ? $default_contact_category : get_config($dbc, 'transport_destination_contact_'.$ticket_type));
if($contact_category == '') {
	$contact_category = $default_contact_category;
}
$transport_contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `name`, `first_name`, `last_name` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `category`='$contact_category'"),'first_name');
$warehouse_contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `name`, `first_name`, `last_name` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `category`='Warehouse'"),'first_name'); ?>
<?php foreach ($field_sort_order as $field_sort_field) { ?>
	<?php if (strpos($value_config, ','."Transport Destination Contact".',') !== FALSE && $field_sort_field == 'Transport Destination Contact') { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?= $contact_category ?> / Company Name:</label>
			<?php if($destination_save == 'readonly') {
				echo ($destination['vendor'] > 0 ? get_contact($dbc, $destination['vendor'], 'name') : $destination['vendor']);
			} else { ?>
				<div class="col-sm-7 select-div" style="<?= $destination['vendor'] > 0 || $destination['vendor'] == '' ? '' : 'display:none;' ?>">
					<select name="vendor" class="chosen-select-deselect" <?= $destination_save ?> data-category="<?= $contact_category ?>" data-set-address="data-type=destination" data-placeholder="Name of the place where you are delivering to..."><option></option>
						<?php foreach($transport_contact_list as $transport_contact) { ?>
							<option <?= $transport_contact['contactid'] == $destination['vendor'] || trim($transport_contact['name'].' '.$transport_contact['first_name'].' '.$transport_contact['last_name']) == $destination['vendor'] ? 'selected' : '' ?> value="<?= $transport_contact['contactid'] ?>"><?= $transport_contact['name'].' '.$transport_contact['first_name'].' '.$transport_contact['last_name'] ?></option>
						<?php } ?>
						<option value="ADD_NEW">Add New <?= $contact_category ?></option>
						<option value="MANUAL">One Time <?= $contact_category ?></option>
					</select>
				</div>
				<div class="col-sm-1 select-div" style="<?= $destination['vendor'] > 0 || $destination['vendor'] == '' ? '' : 'display:none;' ?>">
					<a href="" onclick="overlayIFrameSlider('../Contacts/contacts_inbox.php?fields=all_fields&edit='+$(this).closest('.form-group').find('select').val(), '75%', true, true);return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					<a href="" onclick="$(this).closest('.form-group').find('select').val('ADD_NEW').change(); return false;"><img class="inline-img pull-right" data-history-label="New <?= $contact_category ?> / Company Name" src="../img/icons/ROOK-add-icon.png"></a>
				</div>
				<div class="col-sm-8 manual-div" style="<?= $destination['vendor'] > 0 || $destination['vendor'] == '' ? 'display:none;' : '' ?>">
					<input type="text" name="vendor" class="form-control" data-one-time="true" data-category="<?= $contact_category ?>" <?= $destination_save ?> value="<?= $destination['vendor'] > 0 ? '' : $destination['vendor'] ?>">
					<label class="form-checkbox"><input checked type="checkbox" name="one_time" onchange="if(!this.checked) { $(this).closest('.form-group').find('.select-div').show(); $(this).closest('.manual-div').hide(); }"> One Time Only Contact</label>
				</div>
			<?php } ?>
		</div>
		<?php $pdf_contents[] = [$contact_category.' / Company Name', ($destination['vendor'] > 0 ? get_contact($dbc, $destination['vendor'], 'name') : $destination['vendor'])]; ?>
	<?php } ?>
	<?php if (strpos($value_config, ',Transport Destination Name,') !== FALSE && $field_sort_field == 'Transport Destination Name') { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Shipment Destination:</label>
			<div class="col-sm-8">
				<?php if($destination_save == 'readonly') {
					echo $destination['location_name'];
				} else { ?>
					<input type="text" name="location_name" class="form-control" <?= $destination_save ?> value="<?= $destination['location_name'] ?>">
				<?php } ?>
			</div>
		</div>
		<?php $pdf_contents[] = ['Shipment Destination', $destination['location_name']]; ?>
	<?php } ?>
	<?php if (strpos($value_config, ',Transport Destination,') !== FALSE && $field_sort_field == 'Transport Destination') { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Address:</label>
			<div class="col-sm-<?= strpos($value_config,',Transport Destination Save Contact,') !== FALSE ? '7' : '8' ?>">
				<?php if($destination_save == 'readonly') {
					echo $destination['address'];
				} else { ?>
					<input type="text" name="address" class="form-control destination_address" <?= strpos($value_config,',Transport Destination Save Contact,') !== FALSE ? 'readonly' : '' ?> <?= $destination_save ?> value="<?= $destination['address'] ?>" placeholder="The physical address of the pickup...">
				<?php } ?>
			</div>
			<?php if(strpos($value_config,',Transport Destination Save Contact,') !== FALSE) { ?>
				<div class="col-sm-1">
					<?php if($access_all > 0) { ?>
						<a href="" onclick="<?= $access_all > 0 ? "$('.destination_address_btn').show();$('.destination_address').removeAttr('readonly');" : '' ?>return false;"><img class="inline-img pull-right" src="../img/icons/ROOK-edit-icon.png" /></a>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
		<?php $pdf_contents[] = ['Address', $destination['address']]; ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">City:</label>
			<div class="col-sm-8">
				<?php if($destination_save == 'readonly') {
					echo $destination['city'];
				} else { ?>
					<input type="text" name="city" class="form-control destination_address" <?= strpos($value_config,',Transport Destination Save Contact,') !== FALSE ? 'readonly' : '' ?> <?= $destination_save ?> value="<?= $destination['city'] ?>" placeholder="The city of the physical address...">
				<?php } ?>
			</div>
		</div>
		<?php $pdf_contents[] = ['City', $destination['city']]; ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Province / State:</label>
			<div class="col-sm-8">
				<?php if($destination_save == 'readonly') {
					echo $destination['province'];
				} else { ?>
					<input type="text" name="province" class="form-control destination_address" <?= strpos($value_config,',Transport Destination Save Contact,') !== FALSE ? 'readonly' : '' ?> <?= $destination_save ?> value="<?= $destination['province'] ?>" placeholder="The province / state of the physical address...">
				<?php } ?>
			</div>
		</div>
		<?php $pdf_contents[] = ['Province / State', $destination['province']]; ?>
		<?php if (strpos($value_config, ',Transport Destination Country,') !== FALSE) { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Country:</label>
				<div class="col-sm-8">
					<?php if($destination_save == 'readonly') {
						echo $destination['country'];
					} else { ?>
						<input type="text" name="country" class="form-control destination_address" <?= strpos($value_config,',Transport Destination Save Contact,') !== FALSE ? 'readonly' : '' ?> <?= $destination_save ?> value="<?= $destination['country'] ?>" placeholder="The country of the physical address...">
					<?php } ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Country', $destination['country']]; ?>
		<?php } ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Postal Code / ZIP Code:</label>
			<div class="col-sm-8">
				<?php if($destination_save == 'readonly') {
					echo $destination['postal_code'];
				} else { ?>
					<input type="text" name="postal_code" class="form-control destination_address" <?= strpos($value_config,',Transport Destination Save Contact,') !== FALSE ? 'readonly' : '' ?> <?= $destination_save ?> value="<?= $destination['postal_code'] ?>" placeholder="Postal code / ZIP code of the physical address...">
				<?php } ?>
			</div>
		</div>
		<?php $pdf_contents[] = ['Postal Code / ZIP Code', $destination['postal_code']]; ?>
		<?php if (strpos($value_config, ',Transport Destination Link,') !== FALSE) { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Google Maps Link:</label>
				<div class="col-sm-8">
					<?php if($destination_save == 'readonly') {
						echo $destination['map_link'];
					} else { ?>
						<input type="text" name="map_link" class="form-control" <?= strpos($value_config,',Transport Destination Save Contact,') !== FALSE ? 'readonly' : '' ?> <?= $destination_save ?> value="<?= $destination['map_link'] ?>">
					<?php } ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Google Maps Link', $destination['map_link']]; ?>
		<?php } ?>
		<?php if(strpos($value_config,',Transport Destination Save Contact,') !== FALSE) { ?>
			<div class="clearfix destination_address_btn" style="display:none;">
				<button class="pull-right btn brand-btn" onclick="$('.destination_address_btn').hide();$('.destination_address').attr('readonly',true);saveAddress('.destination_address',$('select[name=vendor][data-set-address$=destination]').val());return false;">Save Mailing Address to <?= $contact_category ?> and <?= TICKET_NOUN ?></button>
				<button class="pull-right btn brand-btn" onclick="$('.destination_address_btn').hide();$('.destination_address').attr('readonly',true);return false;">Save only to <?= TICKET_NOUN ?></button>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if (strpos($value_config, ',Transport Destination Arrival,') !== FALSE && $field_sort_field == 'Transport Destination Arrival') { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Arrival Time:</label>
			<div class="col-sm-8">
				<?php if($destination_save == 'readonly') {
					echo $destination['checked_in'];
				} else { ?>
					<input type="text" name="checked_in" class="form-control datetimepicker<?= $calendar_window > 0 ? '-'.$calendar_window : '' ?>" <?= $destination_save ?> value="<?= $destination['checked_in'] ?>">
				<?php } ?>
			</div>
		</div>
		<?php $pdf_contents[] = ['Arrival Time', $destination['checked_in']]; ?>
	<?php } ?>
	<?php if (strpos($value_config, ',Transport Destination Departure,') !== FALSE && $field_sort_field == 'Transport Destination Departure') { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Departure Time:</label>
			<div class="col-sm-8">
				<?php if($destination_save == 'readonly') {
					echo $destination['checked_out'];
				} else { ?>
					<input type="text" name="checked_out" class="form-control datetimepicker<?= $calendar_window > 0 ? '-'.$calendar_window : '' ?>" <?= $destination_save ?> value="<?= $destination['checked_out'] ?>">
				<?php } ?>
			</div>
		</div>
		<?php $pdf_contents[] = ['Departure Time', $destination['checked_out']]; ?>
	<?php } ?>
	<?php if (strpos($value_config, ','."Transport Destination Warehouse".',') !== FALSE && $field_sort_field == 'Transport Destination Warehouse') { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Warehouse Location:</label>
			<?php if($destination_save == 'readonly') {
				echo ($destination['warehouse_location'] > 0 ? get_contact($dbc, $destination['warehouse_location'], 'name') : $destination['warehouse_location']);
			} else { ?>
				<div class="col-sm-7 select-div" style="<?= $destination['warehouse_location'] > 0 || $destination['warehouse_location'] == '' ? '' : 'display:none;' ?>">
					<select name="warehouse_location" class="chosen-select-deselect" <?= $destination_save ?> data-category="Warehouse" data-placeholder="Location where the inventory is being stored..."><option />
						<?php foreach($warehouse_contact_list as $warehouse_contact) { ?>
							<option <?= $warehouse_contact['contactid'] == $destination['warehouse_location'] || trim($warehouse_contact['name'].' '.$warehouse_contact['first_name'].' '.$warehouse_contact['last_name']) == $destination['warehouse_location'] ? 'selected' : '' ?> value="<?= $warehouse_contact['contactid'] ?>"><?= $warehouse_contact['name'].' '.$warehouse_contact['first_name'].' '.$warehouse_contact['last_name'] ?></option>
						<?php } ?>
						<option value="ADD_NEW">Add New Warehouse</option>
					</select>
				</div>
				<div class="col-sm-1 select-div" style="<?= $destination['warehouse_location'] > 0 || $destination['warehouse_location'] == '' ? '' : 'display:none;' ?>">
					<a href="" onclick="if($(this).closest('.form-group').find('select').val() > 0) { overlayIFrameSlider('../Contacts/contacts_inbox.php?fields=all_fields&edit='+$(this).closest('.form-group').find('select').val(), '75%', true, true) };return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					<a href="" onclick="$(this).closest('.form-group').find('select').val('ADD_NEW').change(); return false;"><img class="inline-img pull-right" data-history-label="New Warehouse Location" src="../img/icons/ROOK-add-icon.png"></a>
				</div>
			<?php } ?>
		</div>
		<?php $pdf_contents[] = ['Warehouse Location', ($destination['warehouse_location'] > 0 ? get_contact($dbc, $destination['warehouse_location'], 'name') : $destination['warehouse_location'])]; ?>
	<?php } ?>
<?php } ?>