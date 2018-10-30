<?php if(strpos($value_config, ',Location Filter By Client,') !== FALSE) { ?>
<script type="text/javascript">
$(document).ready(function() {
	// $('[name="clientid"]').off('change',filterSitesByClient).change(filterSitesByClient);
	if(ticketid > 0) {
		filterSitesByClient();
	}
});
function filterSitesByClient() {
	var ticketid = $('#ticketid').val();
	if(ticketid > 0) {
		$.ajax({
			url: '../Ticket/ticket_ajax_all.php?action=get_ticket_client&ticketid='+ticketid,
			method: 'GET',
			success: function(response) {
				if(response > 0) {
					var clientid = response;
					$('[name="siteid"] option').hide();
					if(clientid != undefined && clientid > 0) {
						$('[name="siteid"] option[data-businessid="'+clientid+'"]').show();
						if(($('[name="siteid"]').val() == undefined || $('[name="siteid"]').val() == '')) {
							$('[name="siteid"] option[data-businessid="'+clientid+'"]').first().prop('selected', true);
							$('[name="siteid"]').trigger('change.select2').change();
						}
					} else {
						$('[name="siteid"] option').show();
					}
				} else {
					$('[name="siteid"] option').show();
				}
			}
		});
	}
}
</script>
<?php } ?>
<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Site</h3>') ?>
<?php if($access_all > 0) { ?>
	<?php foreach($field_sort_order as $field_sort_field) { ?>
		<?php if (strpos($value_config, ','."Location Site".',') !== FALSE && $field_sort_field == 'Location Site') { ?>
			<div class="site_group">
				<div class="form-group">
					<label for="site_name" class="col-sm-4 control-label">Site:</label>
					<div class="col-sm-7">
						<select data-placeholder="Select Site..." name="siteid" id="siteid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control">
							<option value=""></option>
							<?php $query = mysqli_query($dbc,"SELECT `contacts`.contactid, site_name, display_name, lsd, google_maps_address, business_address, address, city, province, postal_code, country, `contacts_description`.notes, police_contact, poison_control, non_emergency, site_emergency_contact, emergency_notes, key_number, door_code_number, alarm_code_number, `businessid` FROM `contacts` LEFT JOIN `contacts_description` ON `contacts`.`contactid`=`contacts_description`.`contactid` WHERE `category`='Sites' AND deleted=0 ORDER BY IFNULL(NULLIF(`display_name`,''),`site_name`)");
							while($row = mysqli_fetch_array($query)) {
								echo "<option ".($get_ticket['siteid'] == $row['contactid'] ? 'selected' : '')." data-businessid='".$row['businessid']."' data-full-address='".decryptIt($row['business_address'])."' data-site='".$row['site_name']."' data-display='".$row['display_name']."' data-lsd='".$row['lsd']."' data-street='".$row['address']."' data-city='".$row['city']."' data-province='".$row['province']."' data-postal='".$row['postal_code']."' data-country='".$row['country']."' data-google='".$row['google_maps_address']."' data-notes='".$row['notes']."' data-police='".(empty($row['police_contact']) ? '911' : $row['police_contact'])."' data-poison='".$row['poison_control']."' data-non-emerg='".$row['non_emergency']."' data-emerg='".$row['site_emergency_contact']."' data-emerg-notes='".$row['emergency_notes']."' data-key-number='".$row['key_number']."' data-door-code-number='".$row['door_code_number']."' data-alarm-code-number='".$row['alarm_code_number']."' value='".$row['contactid']."'>".($row['display_name'] == '' ? $row['site_name'] : $row['display_name']).'</option>';
								if($row['contactid'] == $get_ticket['siteid']) {
									$get_ticket['location_site_name'] = $row['site_name'];
									$get_ticket['location_display_name'] = $row['display_name'];
									$get_ticket['location_lsd'] = $row['lsd'];
									$get_ticket['location_address'] = decryptIt($row['business_address']);
									$get_ticket['location_street'] = $row['address'];
									$get_ticket['location_city'] = $row['city'];
									$get_ticket['location_province'] = $row['province'];
									$get_ticket['location_country'] = $row['country'];
									$get_ticket['location_postal'] = $row['postal_code'];
									$get_ticket['location_google'] = $row['google_maps_address'];
									$get_ticket['location_notes'] = $row['notes'];
									$get_ticket['location_police_contact'] = empty($row['police_contact']) ? '911' : $row['police_contact'];
									$get_ticket['location_poison_control'] = $row['poison_control'];
									$get_ticket['location_non_emergency'] = $row['non_emergency'];
									$get_ticket['location_emergency_contact'] = $row['site_emergency_contact'];
									$get_ticket['location_emergency_notes'] = $row['emergency_notes'];
									$get_ticket['location_key_number'] = $row['key_number'];
									$get_ticket['location_door_code_number'] = $row['door_code_number'];
									$get_ticket['location_alarm_code_number'] = $row['alarm_code_number'];
								}
							} ?>
							<option value="MANUAL">Add New Site</option>
						</select>
					</div>
					<div class="col-sm-1">
						<a href="" onclick="viewSite(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
						<a href="" onclick="$(this).closest('.form-group').find('select').val('MANUAL').change(); return false;"><img class="inline-img pull-right"  data-history-label="New Site" src="../img/icons/ROOK-add-icon.png"></a>
					</div>
				</div>
				<div class="form-group clearfix site_name" style="display:none;">
					<label class="control-label col-sm-4">Name of Location:</label>
					<div class="col-sm-8">
						<input type="text" name="site_name" data-table="contacts" data-id="" data-id-field="contactid" data-attach="Sites" data-attach-field="category" class="form-control">
					</div>
				</div>
			</div>
		<?php } ?>
		<?php if (strpos($value_config, ','."Location Site Info".',') !== FALSE && $field_sort_field == 'Location Site Info') { ?>
			<?php $site_config = explode(',',mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Sites' AND `subtab`='**no_subtab**'"))['contacts']); ?>
			<?php if(in_array('Site Name (Location)',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Site Name:</label>
					<div class="col-sm-8">
						<input type="text" name="site_name" data-table="contacts" data-id="<?= $get_ticket['siteid'] ?>" data-id-field="contactid" value="<?= $get_ticket['location_site_name'] ?>" class="form-control">
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('Display Name',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Site Display Name:</label>
					<div class="col-sm-8">
						<input type="text" name="display_name" data-table="contacts" data-id="<?= $get_ticket['siteid'] ?>" data-id-field="contactid" value="<?= $get_ticket['location_display_name'] ?>" class="form-control">
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('Site LSD',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">LSD:</label>
					<div class="col-sm-8">
						<input type="text" name="lsd" data-table="contacts" data-id="<?= $get_ticket['siteid'] ?>" data-id-field="contactid" value="<?= $get_ticket['location_lsd'] ?>" class="form-control">
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('Full Address',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Address:</label>
					<div class="col-sm-8">
						<input type="text" name="business_address" data-table="contacts" data-id="<?= $get_ticket['siteid'] ?>" data-id-field="contactid" value="<?= $get_ticket['location_address'] ?>" class="form-control">
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('Address',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Street Address:</label>
					<div class="col-sm-8">
						<input type="text" name="mailing_address" data-table="contacts" data-id="<?= $get_ticket['siteid'] ?>" data-id-field="contactid" value="<?= $get_ticket['location_street'] ?>" class="form-control">
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('City',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">City:</label>
					<div class="col-sm-8">
						<input type="text" name="city" data-table="contacts" data-id="<?= $get_ticket['siteid'] ?>" data-id-field="contactid" value="<?= $get_ticket['location_city'] ?>" class="form-control">
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('Province',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Province:</label>
					<div class="col-sm-8">
						<input type="text" name="province" data-table="contacts" data-id="<?= $get_ticket['siteid'] ?>" data-id-field="contactid" value="<?= $get_ticket['location_province'] ?>" class="form-control">
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('Postal Code',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Postal Code:</label>
					<div class="col-sm-8">
						<input type="text" name="postal_code" data-table="contacts" data-id="<?= $get_ticket['siteid'] ?>" data-id-field="contactid" value="<?= $get_ticket['location_postal'] ?>" class="form-control">
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('Country',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Country:</label>
					<div class="col-sm-8">
						<input type="text" name="country" data-table="contacts" data-id="<?= $get_ticket['siteid'] ?>" data-id-field="contactid" value="<?= $get_ticket['location_country'] ?>" class="form-control">
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('Google Maps Address',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4"><?= $get_ticket['location_google'] != '' ? '<a href="'.$get_ticket['location_google'].'">' : '<a onclick="return false;">' ?>Google Maps</a>:</label>
					<div class="col-sm-8">
						<input type="text" name="google_maps_address" data-table="contacts" data-id="<?= $get_ticket['siteid'] ?>" data-id-field="contactid" data-concat="," value="<?= $get_ticket['location_google'] ?>" class="form-control">
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('Key Number',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Key Number:</label>
					<div class="col-sm-8">
						<input type="text" name="key_number" data-table="contacts" data-id="<?= $get_ticket['siteid'] ?>" data-id-field="contactid" data-concat="," value="<?= $get_ticket['location_key_number'] ?>" class="form-control">
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('Door Code Number',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Door Code Number:</label>
					<div class="col-sm-8">
						<input type="text" name="door_code_number" data-table="contacts" data-id="<?= $get_ticket['siteid'] ?>" data-id-field="contactid" data-concat="," value="<?= $get_ticket['location_door_code_number'] ?>" class="form-control">
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('Alarm Code Number',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Alarm Code Number:</label>
					<div class="col-sm-8">
						<input type="text" name="alarm_code_number" data-table="contacts" data-id="<?= $get_ticket['siteid'] ?>" data-id-field="contactid" data-concat="," value="<?= $get_ticket['location_alarm_code_number'] ?>" class="form-control">
					</div>
				</div>
			<?php } ?>
		<?php } ?>
		<?php if (strpos($value_config, ','."Location Notes".',') !== FALSE && $field_sort_field == 'Location Notes') { ?>
			<div class="form-group clearfix site_info">
				<label class="control-label col-sm-4">Site Notes:</label>
				<div class="col-sm-12">
					<textarea name="notes" data-table="contacts_description" data-id="<?= $get_ticket['siteid'] ?>" data-id-field="contactid" data-id-field="ticketid"><?= html_entity_decode($get_ticket['location_notes']) ?></textarea>
				</div>
			</div>
		<?php } ?>
		<?php if (strpos($value_config, ','."Emergency".',') !== FALSE && $field_sort_field == 'Emergency') { ?>
			<div class="form-group clearfix site_info">
				<label class="control-label col-sm-4">Police Contact:</label>
				<div class="col-sm-8">
					<input type="text" name="police_contact" data-table="contacts" data-id="<?= $get_ticket['siteid'] ?>" data-id-field="contactid" value="<?= $get_ticket['location_police_contact'] ?>" class="form-control">
				</div>
			</div>
			<div class="form-group clearfix site_info">
				<label class="control-label col-sm-4">Poison Control:</label>
				<div class="col-sm-8">
					<input type="text" name="poison_control" data-table="contacts" data-id="<?= $get_ticket['siteid'] ?>" data-id-field="contactid" value="<?= $get_ticket['location_poison_control'] ?>" class="form-control">
				</div>
			</div>
			<div class="form-group clearfix site_info">
				<label class="control-label col-sm-4">Non-Emergency Contact:</label>
				<div class="col-sm-8">
					<input type="text" name="non_emergency" data-table="contacts" data-id="<?= $get_ticket['siteid'] ?>" data-id-field="contactid" value="<?= $get_ticket['location_non_emergency'] ?>" class="form-control">
				</div>
			</div>
			<div class="form-group clearfix site_info">
				<label class="control-label col-sm-4">Emergency Contact:</label>
				<div class="col-sm-8">
					<input type="text" name="site_emergency_contact" data-table="contacts" data-id="<?= $get_ticket['siteid'] ?>" data-id-field="contactid" value="<?= $get_ticket['location_emergency_contact'] ?>" class="form-control">
				</div>
			</div>
			<div class="form-group clearfix site_info">
				<label class="control-label col-sm-4">Emergency Notes:</label>
				<div class="col-sm-12">
					<textarea name="emergency_notes" data-table="contacts" data-id="<?= $get_ticket['siteid'] ?>" data-id-field="contactid"><?= html_entity_decode($get_ticket['location_emergency_notes']) ?></textarea>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
<?php } else {
	$row = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `contacts`.contactid, site_name, display_name, lsd, google_maps_address, business_address, address, city, province, postal_code, country, `contacts_description`.notes, police_contact, poison_control, non_emergency, site_emergency_contact, emergency_notes, key_number, door_code_number, alarm_code_number FROM `contacts` LEFT JOIN `contacts_description` ON `contacts`.`contactid`=`contacts_description`.`contactid` WHERE `category`='Sites' AND deleted=0 AND `contacts`.`contactid`='".$get_ticket['siteid']."'"));
	$get_ticket['location_site_name'] = $row['site_name'];
	$get_ticket['location_display_name'] = $row['display_name'];
	$get_ticket['location_lsd'] = $row['lsd'];
	$get_ticket['location_address'] = decryptIt($row['business_address']);
	$get_ticket['location_street'] = $row['address'];
	$get_ticket['location_city'] = $row['city'];
	$get_ticket['location_province'] = $row['province'];
	$get_ticket['location_country'] = $row['country'];
	$get_ticket['location_postal'] = $row['postal_code'];
	$get_ticket['location_google'] = $row['google_maps_address'];
	$get_ticket['location_notes'] = $row['notes'];
	$get_ticket['location_police_contact'] = empty($row['police_contact']) ? '911' : $row['police_contact'];
	$get_ticket['location_poison_control'] = $row['poison_control'];
	$get_ticket['location_non_emergency'] = $row['non_emergency'];
	$get_ticket['location_emergency_contact'] = $row['site_emergency_contact'];
	$get_ticket['location_emergency_notes'] = $row['emergency_notes'];
	$get_ticket['location_key_number'] = $row['key_number'];
	$get_ticket['location_door_code_number'] = $row['door_code_number'];
	$get_ticket['location_alarm_code_number'] = $row['alarm_code_number']; ?>
	<?php foreach($field_sort_order as $field_sort_field) { ?>
		<?php if (strpos($value_config, ','."Location Site".',') !== FALSE && $field_sort_field == 'Location Site') { ?>
			<div class="form-group clearfix">
				<label class="control-label col-sm-4">Name of Location:</label>
				<div class="col-sm-8">
					<?= $get_ticket['location_site_name'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Name of Location', $get_ticket['location_site_name']]; ?>
		<?php } ?>
		<?php if (strpos($value_config, ','."Location Site Info".',') !== FALSE && $field_sort_field == 'Location Site Info') { ?>
			<?php $site_config = explode(',',mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Sites' AND `subtab`='**no_subtab**'"))['contacts']); ?>
			<?php if(in_array('Site Name (Location)',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Site Name:</label>
					<div class="col-sm-8">
						<?= $get_ticket['location_site_name'] ?>
					</div>
				</div>
				<?php $pdf_contents[] = ['Location Site', $get_ticket['location_site_name']]; ?>
			<?php } ?>
			<?php if(in_array('Display Name',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Site Display Name:</label>
					<div class="col-sm-8">
						<?= $get_ticket['location_display_name'] ?>
					</div>
				</div>
				<?php $pdf_contents[] = ['Location Name', $get_ticket['location_display_name']]; ?>
			<?php } ?>
			<?php if(in_array('Site LSD',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">LSD:</label>
					<div class="col-sm-8">
						<?= $get_ticket['location_lsd'] ?>
					</div>
				</div>
				<?php $pdf_contents[] = ['Location LSD', $get_ticket['location_lsd']]; ?>
			<?php } ?>
			<?php if(in_array('Full Address',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Address:</label>
					<div class="col-sm-8">
						<?= $get_ticket['location_address'] ?>
					</div>
				</div>
				<?php $pdf_contents[] = ['Location Address', $get_ticket['location_address']]; ?>
			<?php } ?>
			<?php if(in_array('Address',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Street Address:</label>
					<div class="col-sm-8">
						<?= $get_ticket['location_street'] ?>
					</div>
				</div>
				<?php $pdf_contents[] = ['Location Street Address', $get_ticket['location_street']]; ?>
			<?php } ?>
			<?php if(in_array('City',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">City:</label>
					<div class="col-sm-8">
						<?= $get_ticket['location_city'] ?>
					</div>
				</div>
				<?php $pdf_contents[] = ['Location City', $get_ticket['location_city']]; ?>
			<?php } ?>
			<?php if(in_array('Province',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Province:</label>
					<div class="col-sm-8">
						<?= $get_ticket['location_province'] ?>
					</div>
				</div>
				<?php $pdf_contents[] = ['Location Province', $get_ticket['location_province']]; ?>
			<?php } ?>
			<?php if(in_array('Postal Code',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Postal Code:</label>
					<div class="col-sm-8">
						<?= $get_ticket['location_postal'] ?>
					</div>
				</div>
				<?php $pdf_contents[] = ['Location Postal Code', $get_ticket['location_postal']]; ?>
			<?php } ?>
			<?php if(in_array('Country',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Country:</label>
					<div class="col-sm-8">
						<?= $get_ticket['location_country'] ?>
					</div>
				</div>
				<?php $pdf_contents[] = ['Location Country', $get_ticket['location_country']]; ?>
			<?php } ?>
			<?php if(in_array('Google Maps Address',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4"><?= $get_ticket['location_google'] != '' ? '<a href="'.$get_ticket['location_google'].'">' : '<a onclick="return false;">' ?>Google Maps</a>:</label>
					<div class="col-sm-8">
						<a target="_blank" href="<?= $get_ticket['location_google'] ?>"><?= $get_ticket['location_google'] ?></a>
					</div>
				</div>
				<?php $pdf_contents[] = ['Google Maps', '<a target="_blank" href="'.$get_ticket['location_google'].'">'.$get_ticket['location_google'].'</a>']; ?>
			<?php } ?>
			<?php if(in_array('Key Number',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Key Number:</label>
					<div class="col-sm-8">
						<?= $get_ticket['location_key_number'] ?>
					</div>
				</div>
				<?php $pdf_contents[] = ['Key Number', $get_ticket['location_key_number']]; ?>
			<?php } ?>
			<?php if(in_array('Door Code Number',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Door Code Number:</label>
					<div class="col-sm-8">
						<?= $get_ticket['location_door_code_number'] ?>
					</div>
				</div>
				<?php $pdf_contents[] = ['Door Code Number', $get_ticket['location_door_code_number']]; ?>
			<?php } ?>
			<?php if(in_array('Alarm Code Number',$site_config)) { ?>
				<div class="form-group clearfix site_info">
					<label class="control-label col-sm-4">Alarm Code Number:</label>
					<div class="col-sm-8">
						<?= $get_ticket['location_alarm_code_number'] ?>
					</div>
				</div>
				<?php $pdf_contents[] = ['Alarm Code Number', $get_ticket['location_alarm_code_number']]; ?>
			<?php } ?>
		<?php } ?>
		<?php if (strpos($value_config, ','."Location Notes".',') !== FALSE && $field_sort_field == 'Location Notes') { ?>
			<div class="form-group clearfix">
				<label class="control-label col-sm-4">Site Notes:</label>
				<div class="col-sm-8">
					<?= html_entity_decode($get_ticket['location_notes']) ?>
				</div>
			</div>
			<?php if(strpos($value_config, ','."Location Notes Anyone Can Add".',') !== FALSE && !$strict_view) { ?>
				<a class="pull-right no-toggle" href="" title="Add a Note" onclick="addSiteNote(this, '<?= $row['contactid'] ?>', 1); return false;"><img class="inline-img" data-history-label="Site Note" src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" /></a>
				<div class="clearfix"></div>
			<?php } ?>
			<?php $pdf_contents[] = ['Notes', html_entity_decode($get_ticket['location_notes'])]; ?>
		<?php } ?>
		<?php if (strpos($value_config, ','."Emergency".',') !== FALSE && $field_sort_field == 'Emergency') { ?>
			<div class="form-group clearfix site_info">
				<label class="control-label col-sm-4">Police Contact:</label>
				<div class="col-sm-8">
					<?= $get_ticket['location_police_contact'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Police Contact', $get_ticket['location_police_contact']]; ?>
			<div class="form-group clearfix site_info">
				<label class="control-label col-sm-4">Poison Control:</label>
				<div class="col-sm-8">
					<?= $get_ticket['location_poison_control'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Poison Control', $get_ticket['location_poison_control']]; ?>
			<div class="form-group clearfix site_info">
				<label class="control-label col-sm-4">Non-Emergency Contact:</label>
				<div class="col-sm-8">
					<?= $get_ticket['location_non_emergency'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Non-Emergency Contact', $get_ticket['location_non_emergency']]; ?>
			<div class="form-group clearfix site_info">
				<label class="control-label col-sm-4">Emergency Contact:</label>
				<div class="col-sm-8">
					<?= $get_ticket['location_site_emergency_contact'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Emergency Contact', $get_ticket['location_site_emergency_contact']]; ?>
			<div class="form-group clearfix site_info">
				<label class="control-label col-sm-4">Emergency Notes:</label>
				<div class="col-sm-8">
					<?= html_entity_decode($get_ticket['location_emergency_notes']) ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Emergency Notes', html_entity_decode($get_ticket['location_emergency_notes'])]; ?>
		<?php } ?>
	<?php } ?>
<?php } ?>