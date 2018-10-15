
<?php if($subtab == 'staff_information') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } else if($subtab == 'staff_bio') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } else if($subtab == 'staff_address') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } else if($subtab == 'position') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } else if($subtab == 'employee_information') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } else if($subtab == 'driver_information') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } else if($subtab == 'direct_deposit_information') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } else if($subtab == 'software_id') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } else if($subtab == 'software_access') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } else if($subtab == 'social_media') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } else if($subtab == 'emergency') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } else if($subtab == 'health') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } else if($subtab == 'health_concerns') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } else if($subtab == 'allergies') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } else if($subtab == 'company_benefits') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } else if($subtab == 'schedule') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } else if($subtab == 'hr') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } else if($subtab == 'staff_docs') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } else if($subtab == 'incident_reports') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } else if($subtab == 'hidden') { ?>
	<?php $contact_config_array = array_filter(explode(",", $contacts_config)); ?>
	<?php foreach($contact_config_array as $config_field): ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($contacts_mandatory_config, ','."$config_field".',') !== FALSE) { echo " checked"; } ?> <?php if (strpos($main_contacts_config, ','."$config_field".',') !== FALSE)
		{ echo " checked disabled"; } ?> value="<?php echo $config_field; ?>" name="contacts"> <?php echo $config_field; ?></label>
	<?php endforeach; ?>
<?php } ?>
