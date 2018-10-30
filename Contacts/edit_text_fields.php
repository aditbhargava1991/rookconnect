<?php if($field_option == 'Employee ID' || $field_option == 'Profile ID') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> ID #:</label>
	<div class="col-sm-8">
		<input type="text" readonly name="contactid" value="<?= $contactid > 0 ? $contactid : '' ?>" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Business' || $field_option == 'Program Business') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> <?= BUSINESS_CAT ?>:</label>
	<div class="col-sm-8">
		<select name="businessid" data-table="contacts" data-field="businessid" data-placeholder="Select a <?= BUSINESS_CAT ?>..." data-no_results_text="Add a <?= BUSINESS_CAT ?>:" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
			<option value=''></option>
            <option value='*NEW_VALUE*'>New <?= BUSINESS_CAT; ?></option>
			<?php $contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `name` FROM `contacts` WHERE `category`='".BUSINESS_CAT."' AND `status`=1 AND `deleted`=0"),MYSQLI_ASSOC));
			foreach($contact_list as $contactbusinessid) { ?>
				<option <?= $contactbusinessid == $contact['businessid'] || $contact['businessid'] == '' && $contactbusinessid == $_GET['businessid'] ? 'selected' : '' ?> value="<?= $contactbusinessid ?>"><?= get_client($dbc, $contactbusinessid) ?></option>
			<?php } ?>
		</select>
		<?php if($contact['businessid'] == '' && $_GET['businessid'] > 0) { ?>
			<script>
			$(document).ready(function() {
				$('select[name=businessid]').first().change();
			});
			</script>
		<?php } ?>
        <input type="text" name="businessid" data-table="contacts" data-field="name" data-replicating-fieldname="businessid" data-contact-category="<?= BUSINESS_CAT; ?>" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" style="display:none;" />
	</div>
<?php } else if($field_option == 'Site') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Site:</label>
	<div class="col-sm-8">
		<select name="siteid" data-table="contacts" data-field="siteid" data-placeholder="Select a Site..." class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
			<option value=''></option>
            <option value='*NEW_VALUE*'>New Site</option>
			<?php $query = mysqli_query($dbc,"SELECT `contactid`, `site_name` FROM `contacts` WHERE `category`='Sites' AND `deleted`=0 ORDER BY `site_name`");
			while($row = mysqli_fetch_array($query)) {
				echo "<option ".($contact['siteid'] == $row['contactid'] ? 'selected' : '')." value='".$row['contactid']."'>".$row['site_name'].'</option>';
			} ?>
		</select>
        <input type="text" name="siteid" data-table="contacts" data-field="site_name" data-replicating-fieldname="siteid" data-contact-category="Sites" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" style="display:none;" />
	</div>
<?php } else if($field_option == 'Contact') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Contact:</label>
	<div class="col-sm-8">
		<select name="contact_id" data-table="contacts" data-field="contact_id" data-contact-id="yes" data-placeholder="Select a Contact..." class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
			<option value=''></option>
            <option value='*NEW_VALUE*'>New Contact</option>
			<?php $contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` != 'Staff' ORDER BY `first_name`"),MYSQLI_ASSOC));
			foreach($contact_list as $contact_id) {
                if ( get_contact($dbc, $contact_id) != '-' ) {
                    echo "<option ".($contactid == $contact_id ? 'selected' : '')." value='".$contact_id."'>".get_contact($dbc, $contact_id).'</option>';
                }
            } ?>
		</select>
        <div class="newfield" style="display:none;">
            <select name="new_category" data-table="contacts" data-field="new_category" data-new-category="yes" data-placeholder="Select a Contact Type..." class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
                <option value=''></option>
                <?php if(FOLDER_NAME == 'staff') {
                    $each_tab = ['Staff'];
                } else {
                    $each_tab = explode(',',get_config($dbc, FOLDER_NAME.'_tabs'));
                }
                foreach ($each_tab as $cat_tab) {
                    echo "<option value='". $cat_tab."'>".$cat_tab.'</option>';
                } ?>
            </select>
            <div><input type="text" name="new_first_name" placeholder="First Name" data-table="contacts" data-field="new_first_name" data-new-firstname="yes" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" /></div>
            <div><input type="text" name="new_last_name" placeholder="Last Name" data-table="contacts" data-field="new_last_name" data-new-lastname="yes" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" /></div>
        </div>
	</div>
<?php } else if($field_option == 'Ref Contact') { ?>
	<?php $contact_list = sort_contacts_query(mysqli_query($dbc,"SELECT `contactid`, `category`, `first_name`, `last_name`, `name` FROM `contacts` WHERE `category` != 'Staff' AND `deleted`=0 AND `status` > 0")); ?>
	<?php foreach(explode(',', $contact['ref_contact']) as $ref_contact) { ?>
		<div class="ref_contact_div">
			<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Contact:</label>
			<div class="col-sm-7">
				<select name="ref_contact[]" data-table="contacts" data-field="ref_contact" data-placeholder="Select <?= CONTACTS_TILE ?>" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
					<option value=''></option>
					<?php foreach($contact_list as $row) { ?>
						<option <?= $row['contactid'] == $ref_contact ? 'selected' : '' ?> value="<?= $row['contactid'] ?>"><?= $row['full_name'] ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-sm-1 pull-right">
				<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addMultiple('ref_contact_div');">
				<img src="../img/remove.png" class="inline-img pull-right" onclick="removeMultiple('ref_contact_div', this);">
			</div>
		</div>
	<?php } ?>
<?php } else if($field_option == 'Contact Prefix') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Prefix:</label>
	<div class="col-sm-8">
		<select name="contact_prefix" data-field="prefix" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect"><option></option>
			<option <?= $contact['prefix'] == 'mr' ? 'selected' : '' ?> value="mr">Mr.</option>
			<option <?= $contact['prefix'] == 'mrs' ? 'selected' : '' ?> value="mrs">Mrs.</option>
			<option <?= $contact['prefix'] == 'miss' ? 'selected' : '' ?> value="miss">Miss</option>
			<option <?= $contact['prefix'] == 'ms' ? 'selected' : '' ?> value="ms">Ms.</option>
			<option <?= $contact['prefix'] == 'dr' ? 'selected' : '' ?> value="dr">Dr.</option>
			<option <?= $contact['prefix'] == 'other' ? 'selected' : '' ?> value="other">Other</option>
		</select>
	</div>
<?php } else if($field_option == 'First Name' || $field_option == 'Profile First Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> First Name:</label>
	<div class="col-sm-8">
		<input type="text" name="first_name" value="<?= decryptIt($contact['first_name']) ?>" data-field="first_name" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Last Name' || $field_option == 'Profile Last Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Last Name:</label>
	<div class="col-sm-8">
		<input type="text" name="last_name" value="<?= decryptIt($contact['last_name']) ?>" data-field="last_name" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Preferred Name' || $field_option == 'Profile Preferred Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Preferred Name:</label>
	<div class="col-sm-8">
		<input type="text" name="prefer_name" value="<?= decryptIt($contact['prefer_name']) ?>" data-field="prefer_name" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Middle') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Middle:</label>
	<div class="col-sm-8">
		<input type="text" name="middle" value="<?= decryptIt($contact['middle']) ?>" data-field="middle" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> <?= $current_type ?> Name:</label>
	<div class="col-sm-8">
		<input type="text" name="Name" value="<?= decryptIt($contact['name']) ?>" data-field="name" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Title') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Title:</label>
	<div class="col-sm-8">
		<input type="text" name="title" value="<?= $contact['title'] ?>" data-field="title" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Credential') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Credentials:</label>
	<div class="col-sm-8">
		<input type="text" name="credentials" value="<?= $contact['credential'] ?>" data-field="credential" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Home Phone' || $field_option == 'Profile Home Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Home Phone:<br><em>(Check box if contact is primary)</em></label>
	<div class="col-sm-8">
		<input type="checkbox" name="primary_contact" value="H" <?= ($contact['primary_contact'] == 'H' ? 'checked' : '') ?> data-field="primary_contact" data-table="contacts">
		<input type="text" name="home_phone" value="<?= decryptIt($contact['home_phone']) ?>" data-field="home_phone" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Office Phone' || $field_option == 'Profile Office Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Business Phone:<br><em>(Check box if contact is primary)</em></label>
	<div class="col-sm-8">
		<input type="checkbox" name="primary_contact" value="O" <?= ($contact['primary_contact'] == 'O' ? 'checked' : '') ?> data-field="primary_contact" data-table="contacts">
		<input type="text" name="office_phone" value="<?= decryptIt($contact['office_phone']) ?>" data-field="office_phone" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Cell Phone' || $field_option == 'Profile Cell Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Cell Phone:<br><em>(Check box if contact is primary)</em></label>
	<div class="col-sm-8">
		<input type="checkbox" name="primary_contact" value="C" <?= ($contact['primary_contact'] == 'C' ? 'checked' : '') ?> data-field="primary_contact" data-table="contacts">
		<input type="text" name="cell_phone" value="<?= decryptIt($contact['cell_phone']) ?>" data-field="cell_phone" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Phone Carrier') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Phone Carrier:</label>
	<div class="col-sm-8">
		<input type="text" name="phone_carrier" value="<?= $contact['phone_carrer'] ?>" data-field="phone_carrier" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Fax' || $field_option == 'Profile Fax') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Fax #:</label>
	<div class="col-sm-8">
		<input type="text" name="fax" value="<?= $contact['fax'] ?>" data-field="fax" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Email Address' || $field_option == 'Profile Email Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Email Address:</label>
	<div class="col-sm-8">
		<input type="text" name="email" value="<?= decryptIt($contact['email_address']) ?>" data-field="email_address" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Second Email Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Second Email Address:</label>
	<div class="col-sm-8">
		<input type="text" name="second_email" value="<?= decryptIt($contact['second_email_address']) ?>" data-field="second_email_address" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Preferred Contact Method') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Preferred Method of Contact:</label>
	<div class="col-sm-8">
		<input type="text" name="preferred_contact_method" value="<?= decryptIt($contact['preferred_contact_method']) ?>" data-field="preferred_contact_method" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Website' || $field_option == 'Site Website') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Website:</label>
	<div class="col-sm-8">
		<input type="text" name="website" value="<?= $contact['website'] ?>" data-field="website" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Region' || $field_option == 'Profile Region') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Region:</label>
	<div class="col-sm-8">
		<select name="region[]" multiple data-field="region" data-table="contacts" data-delimiter="," data-exact-name="1" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect"><option></option>
			<?php $each_tab = array_unique(explode(',',mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0]));
			foreach ($each_tab as $cat_tab) {
				echo "<option ".(in_array($cat_tab, explode(',',$contact['region'])) ? 'selected' : '')." value='". $cat_tab."'>".$cat_tab.'</option>';
			} ?>
		</select>
	</div>
<?php } else if($field_option == 'Location' || $field_option == 'Profile Location') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Location:</label>
	<div class="col-sm-8">
		<select name="con_locations" data-field="con_locations" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect"><option></option>
			<?php $each_tab = array_filter(array_unique(explode(',',mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `con_locations` SEPARATOR ',') FROM `field_config_contacts`"))[0])));
			foreach ($each_tab as $cat_tab) {
				echo "<option ".($contact['con_locations'] == $cat_tab ? 'selected' : '')." value='". $cat_tab."'>".$cat_tab.'</option>';
			} ?>
		</select>
	</div>
<?php } else if($field_option == 'Classification' || $field_option == 'Profile Classification') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Classification:</label>
	<div class="col-sm-8">
		<select name="classification[]" multiple data-field="classification" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect"><option></option>
			<?php $each_tab = array_unique(explode(',',mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_classification'"))[0]));
			foreach ($each_tab as $cat_tab) {
				echo "<option ".(in_array($cat_tab, explode(',',$contact['classification'])) ? 'selected' : '')." value='". $cat_tab."'>".$cat_tab.'</option>';
			} ?>
		</select>
	</div>
<?php } else if($field_option == 'Service') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Service:</label>
	<div class="col-sm-8">
		<select name="serviceid" data-field="serviceid" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect"><option></option>
                <?php
                $query = mysqli_query($dbc,"SELECT serviceid, category, heading FROM services WHERE deleted=0");
                while($row = mysqli_fetch_array($query)) {
                    if ($contact['serviceid'] == $row['serviceid']) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option ".$selected." value='". $row['serviceid']."'>".$row['category'].' : '.$row['heading'].'</option>';
                } ?>
		</select>
	</div>
<?php } else if($field_option == 'LinkedIn' || $field_option == 'Profile LinkedIn') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> LinkedIn:</label>
	<div class="col-sm-8">
		<input type="text" name="linkedin" value="<?= $contact['linkedin'] ?>" data-field="linkedin" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Facebook' || $field_option == 'Profile Facebook') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Facebook:</label>
	<div class="col-sm-8">
		<input type="text" name="facebook" value="<?= $contact['facebook'] ?>" data-field="facebook" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Twitter' || $field_option == 'Profile Twitter') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Twitter:</label>
	<div class="col-sm-8">
		<input type="text" name="twitter" value="<?= $contact['twitter'] ?>" data-field="twitter" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Google+' || $field_option == 'Profile Google+') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Google+:</label>
	<div class="col-sm-8">
		<input type="text" name="google_plus" value="<?= $contact['google_plus'] ?>" data-field="google_plus" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Instagram' || $field_option == 'Profile Instagram') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Instagram:</label>
	<div class="col-sm-8">
		<input type="text" name="instagram" value="<?= $contact['instagram'] ?>" data-field="instagram" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Pinterest' || $field_option == 'Profile Pinterest') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Pinterest:</label>
	<div class="col-sm-8">
		<input type="text" name="pinterest" value="<?= $contact['pinterest'] ?>" data-field="pinterest" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'YouTube' || $field_option == 'Profile YouTube') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> YouTube:</label>
	<div class="col-sm-8">
		<input type="text" name="youtube" value="<?= $contact['youtube'] ?>" data-field="youtube" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Blog' || $field_option == 'Profile Blog') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Blog:</label>
	<div class="col-sm-8">
		<input type="text" name="blog" value="<?= $contact['blog'] ?>" data-field="blog" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Profile Priority') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Profile Priority:</label>
	<div class="col-sm-8">
		<input type="text" name="priority" value="<?= $contact['priority'] ?>" data-field="priority" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Rating') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Rating:</label>
	<div class="col-sm-8">
		<select name="rating" data-field="rating" data-table="contacts" class="chosen-select-deselect <?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control"><option></option>
			<?php if(in_array("Bronze Rating", $field_config)) { ?><option style="background-color: #9B886C;" <?= $contact['rating'] == 'Bronze' ? 'selected' : '' ?> value="Bronze">Bronze</option><?php } ?>
			<?php if(in_array("Silver Rating", $field_config)) { ?><option style="background-color: silver;" <?= $contact['rating'] == 'Silver' ? 'selected' : '' ?> value="Silver">Silver</option><?php } ?>
			<?php if(in_array("Gold Rating", $field_config)) { ?><option style="background-color: #D1B85F;" <?= $contact['rating'] == 'Gold' ? 'selected' : '' ?> value="Gold">Gold</option><?php } ?>
			<?php if(in_array("Platinum Rating", $field_config)) { ?><option style="background-color: #ABA9AC;" <?= $contact['rating'] == 'Platinum' ? 'selected' : '' ?> value="Platinum">Platinum</option><?php } ?>
			<?php if(in_array("Diamond Rating", $field_config)) { ?><option style="background-color: #b9f2ff;" <?= $contact['rating'] == 'Diamond' ? 'selected' : '' ?> value="Diamond">Diamond</option><?php } ?>
			<?php if(in_array("Green Rating", $field_config)) { ?><option style="background-color: #228B22;" <?= $contact['rating'] == 'Green' ? 'selected' : '' ?> value="Green">Green</option><?php } ?>
			<?php if(in_array("Yellow Rating", $field_config)) { ?><option style="background-color: #ffff00;" <?= $contact['rating'] == 'Yellow' ? 'selected' : '' ?> value="Yellow">Yellow</option><?php } ?>
			<?php if(in_array("Light blue Rating", $field_config)) { ?><option style="background-color: #ADD8E6;" <?= $contact['rating'] == 'Light blue' ? 'selected' : '' ?> value="Light blue">Light Blue</option><?php } ?>
			<?php if(in_array("Dark blue Rating", $field_config)) { ?><option style="background-color: #1E90FF;" <?= $contact['rating'] == 'Dark blue' ? 'selected' : '' ?> value="Dark blue">Dark Blue</option><?php } ?>
			<?php if(in_array("Red Rating", $field_config)) { ?><option style="background-color: #ff0000;" <?= $contact['rating'] == 'Red' ? 'selected' : '' ?> value="Red">Red</option><?php } ?>
			<?php if(in_array("Pink Rating", $field_config)) { ?><option style="background-color: #FF69B4;" <?= $contact['rating'] == 'Pink' ? 'selected' : '' ?> value="Pink">Pink</option><?php } ?>
			<?php if(in_array("Purple Rating", $field_config)) { ?><option style="background-color: #BF00FE;" <?= $contact['rating'] == 'Purple' ? 'selected' : '' ?> value="Purple">Purple</option><?php } ?>
		</select>
	</div>
<?php } else if($field_option == 'Status' || $field_option == 'Profile Status') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Status:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="status" value="1" <?= $contact['status'] == 1 ? 'checked' : '' ?> data-field="status" data-table="contacts"> Active</label>
		<label class="form-checkbox"><input type="radio" name="status" value="0" <?= $contact['status'] == 0 ? 'checked' : '' ?> data-field="status" data-table="contacts"> Inactive</label>
	</div>
<?php } else if($field_option == 'History' && $contactid > 0) { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> History:</label>
	<div class="col-sm-8">
		<a href="" onclick="view_history(); return false;">Click Here</a>
	</div>
<?php } else if($field_option == 'Background Check') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Background Check:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="background_check" value="1" <?= $contact['background_check'] == 1 ? 'checked' : '' ?> data-field="background_check" data-table="contacts"> Yes</label>
		<label class="form-checkbox"><input type="radio" name="background_check" value="0" <?= $contact['background_check'] == 0 ? 'checked' : '' ?> data-field="background_check" data-table="contacts"> No</label>
	</div>
<?php } else if($field_option == 'Contact Category') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Contact Category:</label>
	<div class="col-sm-8">
		<select name="category_contact" data-field="category_contact" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
			<option></option><option value="*NEW_VALUE*">New Category</option>
			<?php $query = mysqli_query($dbc,"SELECT distinct(category_contact) FROM contacts");
			while($row = mysqli_fetch_array($query)) {
				echo "<option ".($contact['category_contact'] == $row['category_contact'] ? 'selected' : '')." value='". $row['category_contact']."'>".$row['category_contact'].'</option>';
			} ?>
		</select>
		<input type="text" name="category_contact" value="<?= $contact['category_contact'] ?>" data-field="category_contact" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" style="display:none;">
	</div>
<?php } else if($field_option == 'Staff Category') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Staff Category:</label>
	<div class="col-sm-8">
		<select name="staff_category" data-field="staff_category" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
			<option></option><option value="*NEW_VALUE*">New Category</option>
			<?php $query = mysqli_query($dbc,"SELECT distinct(staff_category) FROM contacts");
			while($row = mysqli_fetch_array($query)) {
				echo "<option ".($contact['staff_category'] == $row['staff_category'] ? 'selected' : '')." value='". $row['staff_category']."'>".$row['staff_category'].'</option>';
			} ?>
		</select>
		<input type="text" name="staff_category" value="<?= $contact['staff_category'] ?>" data-field="staff_category" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" style="display:none;">
	</div>
<?php } else if($field_option == 'Business Sites') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Site:</label>
	<div class="col-sm-8">
		<select name="siteid" data-field="siteid" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
			<option></option>
			<?php $query = mysqli_query($dbc,"SELECT `contactid`, `site_name` FROM `contacts` WHERE `category`='Sites' ORDER BY `site_name`");
			while($row = mysqli_fetch_array($query)) {
				echo "<option ".($contact['siteid'] == $row['contactid'] ? 'selected' : '')." value='".$row['contactid']."'>".$row['site_name'].'</option>';
			} ?>
		</select>
	</div>
<?php } else if($field_option == 'Site LSD') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Site LSD:</label>
	<div class="col-sm-8">
		<input type="text" name="lsd" value="<?= $contact['lsd'] ?>" data-field="lsd" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Site Bottom Hole') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Bottom Hole (UWI):</label>
	<div class="col-sm-8">
		<input type="text" name="bottom_hole" value="<?= $contact['bottom_hole'] ?>" data-field="bottom_hole" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Site Alias') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Alias:</label>
	<div class="col-sm-8">
		<input type="text" name="site_alias" value="<?= $contact['site_alias'] ?>" data-field="site_alias" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Police') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Police Contact:</label>
	<div class="col-sm-8">
		<input type="text" name="police_contact" value="<?= $contact['police_contact'] ?>" data-field="police_contact" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Poison') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Poison Control:</label>
	<div class="col-sm-8">
		<input type="text" name="poison_control" value="<?= $contact['poison_control'] ?>" data-field="poison_control" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Non') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Non-Emergency Contact:</label>
	<div class="col-sm-8">
		<input type="text" name="non_emergency" value="<?= $contact['non_emergency'] ?>" data-field="non_emergency" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Contact') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Emergency Contact:</label>
	<div class="col-sm-8">
		<input type="text" name="site_emergency_contact" value="<?= $contact['site_emergency_contact'] ?>" data-field="site_emergency_contact" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Customer(Client/Customer/Business)') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Customer:</label>
	<div class="col-sm-8">
		<select name="siteclientid" value="<?= $contact['siteclientid'] ?>" data-field="siteclientid" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
			<option></option>
			<?php $customer_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `name`, MAX(`contactid`) FROM `contacts` WHERE `deleted`=0 AND `status`=1 AND `category` IN ('Customer','Business') GROUP BY `name`"),MYSQLI_ASSOC));
			foreach($customer_list as $customer) { ?>
				<option <?= $customer == $contact['siteclientid'] ? 'selected' : '' ?> value="<?= $customer ?>"><?= get_client($dbc, $customer) ?></option>
			<?php } ?>
		</select>
	</div>
<?php } else if($field_option == 'Attached Contact') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Attached Contact:</label>
	<div class="col-sm-8">
		<select name="businessid" value="<?= $contact['businessid'] ?>" data-field="businessid" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
			<option></option>
			<?php $customer_list = sort_contacts_query(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `deleted`=0 AND `status`>0"));
			foreach($customer_list as $customer) {
				if(!empty($customer['full_name']) && $customer['full_name'] != '-') { ?>
					<option <?= $customer['contactid'] == $contact['businessid'] ? 'selected' : '' ?> value="<?= $customer['contactid'] ?>"><?= $customer['full_name'] ?></option>
				<?php }
			} ?>
		</select>
	</div>
<?php } else if($field_option == 'Site Number') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Site #:</label>
	<div class="col-sm-8">
		<input type="text" name="site_number" value="<?= $contact['site_number'] ?>" data-field="site_number" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Site Name (Location)') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Site Name (Location):</label>
	<div class="col-sm-8">
		<input type="text" name="site_name" value="<?= $contact['site_name'] ?>" data-field="site_name" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Preferred Pronoun' || $field_option == 'Profile Preferred Pronoun') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Preferred Pronoun:</label>
	<div class="col-sm-8">
		<select name="preferred_pronoun" data-field="preferred_pronoun" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
			<option></option>
			<option <?= $contact['preferred_pronoun'] == 1 ? 'selected' : '' ?> value="1">She/Her</option>
			<option <?= $contact['preferred_pronoun'] == 2 ? 'selected' : '' ?> value="2">He/Him</option>
			<option <?= $contact['preferred_pronoun'] == 3 ? 'selected' : '' ?> value="3">They/Them</option>
			<option <?= $contact['preferred_pronoun'] == 4 ? 'selected' : '' ?> value="4">Just use my name</option>
		</select>
	</div>
<?php } else if($field_option == 'Nick Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Nick Name:</label>
	<div class="col-sm-8">
		<input type="text" name="nick_name" value="<?= $contact['nick_name'] ?>" data-field="nick_name" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Gender' || $field_option == 'Profile Gender') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Gender:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" <?= $contact['gender'] == 'Female' ? 'checked' : '' ?> name="gender" value="Female" data-field="gender" data-table="contacts"> Female</label>
		<label class="form-checkbox"><input type="radio" <?= $contact['gender'] == 'Male' ? 'checked' : '' ?> name="gender" value="Male" data-field="gender" data-table="contacts"> Male</label>
	</div>
<?php } else if($field_option == 'License') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Licence #:</label>
	<div class="col-sm-8">
		<input type="text" name="license" value="<?= $contact['license'] ?>" data-field="license" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Alberta Health Care No') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Alberta Health Care #:</label>
	<div class="col-sm-8">
		<input type="text" name="health_care_num" value="<?= $contact['health_care_num'] ?>" data-field="health_care_num" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Insurer') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Insurer:</label>
	<div class="col-sm-8">
		<select name="insurerid" data-table="contacts" data-field="insurerid" data-placeholder="Select an Insurer" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
			<option value=''></option>
			<?php $contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `name` FROM `contacts` WHERE `category`='Insurer' AND `status`=1 AND `deleted`=0"),MYSQLI_ASSOC));
			foreach($contact_list as $contactid) { ?>
				<option <?= $contactid == $contact['insurerid'] ? 'selected' : '' ?> value="<?= $contactid ?>"><?= get_client($dbc, $contactid) ?></option>
			<?php } ?>
		</select>
	</div>
<?php } else if($field_option == 'Assigned Staff' || $field_option == 'Profile Assigned Staff' || $field_option == 'Preferred Staff') { ?>
	<?php foreach(explode(',', $contact['assign_staff']) as $assign_staff) { ?>
		<div class="perferred_staff_div">
			<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> <?= $field_option == 'Preferred Staff' ? 'Preferred Staff' : 'Assigned Staff' ?>:</label>
			<div class="col-sm-7">
				<select name="assign_staff[]" data-table="contacts" data-field="assign_staff" data-placeholder="Select Staff" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
					<option value=''></option>
					<?php $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category`='Staff' AND `status`=1 AND `deleted`=0"));
					foreach($contact_list as $row) { ?>
						<option <?= $row['contactid'] == $assign_staff ? 'selected' : '' ?> value="<?= $row['contactid'] ?>"><?= $row['first_name'].' '.$row['last_name'] ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-sm-1 pull-right">
				<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addMultiple('perferred_staff_div');">
				<img src="../img/remove.png" class="inline-img pull-right" onclick="removeMultiple('perferred_staff_div', this);">
			</div>
		</div>
	<?php } ?>
<?php } else if($field_option == 'Scheduled Days/Hours') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Scheduled Days/Hours:</label>
	<div class="col-sm-8">
		<input type="text" name="***" value="<?= $contact['***'] ?>" data-field="***" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Business Hours') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Hours:</label>
	<div class="col-sm-8">
		<input type="text" name="scheduled_hours" value="<?= $contact['scheduled_hours'] ?>" data-field="scheduled_hours" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Profile Link') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Profile Link:</label>
	<div class="col-sm-8">
		<input type="text" name="profile_link" value="<?= $contact['profile_link'] ?>" data-field="profile_link" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Correspondence Language') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Correspondence Language:</label>
	<div class="col-sm-8">
		<input type="text" name="correspondence_language" value="<?= $contact['correspondence_language'] ?>" data-field="correspondence_language" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Accepts to receive emails') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Agrees to receive emails:</label>
	<div class="col-sm-8">
		<input type="text" name="accepts_receive_emails" value="<?= $contact['accepts_receive_emails'] ?>" data-field="accepts_receive_emails" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Role') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Security Level:</label>
	<div class="col-sm-8">
		<?php if(strpos(",".$contact['role'].",", ',super,') !== FALSE) {
			echo "Super Admin";
		} else { ?>
			<select name="role[]" multiple data-placeholder="Select a Security Level" data-table="contacts" data-field="role" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
				<option value=''></option>
				<?php foreach(get_security_levels($dbc) as $select_value => $value)  { ?>
					<option <?= (strpos(','.$contact['role'].',',','.$value.',') !== false ? 'selected' : '') ?> value="<?php echo $value; ?>"><?php echo $select_value; ?></option>
				<?php } ?>
			</select>
		<?php } ?>
	</div>
<?php } else if($field_option == 'Division') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Division:</label>
	<div class="col-sm-8">
		<select name="classification" data-table="contacts" data-field="classification" data-placeholder="Select a Division" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
			<option value=''></option>
			<?php $options = get_config($dbc, FOLDER_NAME.'_classification');
			foreach($options as $option) { ?>
				<option <?= $option == $contact['classification'] ? 'selected' : '' ?> value="<?= $option ?>"><?= $option ?></option>
			<?php } ?>
		</select>
	</div>
<?php } else if($field_option == 'User Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Username:</label>
	<div class="col-sm-8">
		<input type="text" name="user_name" value="<?= $contact['user_name'] ?>" data-field="user_name" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Password') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Password:</label>
	<div class="col-sm-8">
		<input type="text" style="display:none;"><input type="password" style="display:none;">
		<input type="password" name="password" value="<?= decryptIt($contact['password']) ?>" data-field="password" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" autocomplete="new-password">
	</div>
<?php } else if($field_option == 'Auto-Generate Using Email') { ?>
	<div class="col-sm-12">
		<a href="" onclick="autoGenerateUsingEmail(); return false;" class="btn brand-btn pull-right">Auto-Generate Using Email</a>
	</div>
<?php } else if($field_option == 'Email Credentials') { ?>
	<div class="col-sm-12">
		<a href="" onclick="emailCredentialsDialog(); return false;" class="btn brand-btn pull-right">Email Credentials</a>
	</div>
<?php } else if($field_option == 'Software URL') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Software URL:</label>
	<div class="col-sm-8">
		<input type="text" name="software_url" value="<?= $contact['software_url'] ?>" data-field="software_url" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Region Access') {
	$allowed_regions = array_filter(explode('#*#', $contact_security['region_access']));
	if(count($allowed_regions) == 0) {
		$allowed_regions = $contact_regions;
	} ?>
	<script type="text/javascript">
		$(document).ready(function() {
			filterEquipmentAccess(true);
			filterClassificationAccess(true);
		});
		$(document).on('change', 'select[name="region_access[]"]', function() { filterEquipmentAccess(); filterClassificationAccess(); });
		function filterEquipmentAccess(no_save) {
			var regions = [];
			$('[name="region_access[]"] option:selected').each(function() {
				regions.push($(this).val());
			});
			if(regions.length > 0) {
				$('[name="equipment_access[]"] option').hide();
				$('[name="equipment_access[]"] option').each(function() {
					if($(this).data('region') != undefined && $(this).data('region') != '') {
						var region_pass = false;
						var equip_region = $(this).data('region').split('*#*');
						equip_region.forEach(function(this_region) {
							if(regions.indexOf(this_region) > -1) {
								region_pass = true;
							}
						});
						if(region_pass) {
							$(this).show();
						} else {
							$(this).prop('selected', false);
						}
					} else {
						$(this).show();
					}
				});
			} else {
				$('[name="equipment_access[]"] option').show();
			}
			if(!no_save) {
				$('[name="equipment_access[]"]').trigger('change');
			}
		}
		function filterClassificationAccess(no_save) {
			var regions = [];
			$('[name="region_access[]"] option:selected').each(function() {
				regions.push($(this).val());
			});
			if(regions.length > 0) {
				$('[name="classification_access[]"] option').hide();
				$('[name="classification_access[]"] option').each(function() {
					if($(this).data('region') != undefined && $(this).data('region') != '') {
						var class_region = $(this).data('region');
						if(regions.indexOf(class_region) > -1) {
							$(this).show();
						} else {
							$(this).prop('selected', false);
						}
					} else {
						$(this).show();
					}
				});
			} else {
				$('[name="classification_access[]"] option').show();
			}
			if(!no_save) {
				$('[name="classification_access[]"]').trigger('change');
			}
		}
	</script>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Region Access:</label>
	<div class="col-sm-8">
		<select multiple name="region_access[]" class="chosen-select-deselect <?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" data-field="region_access" data-table="contacts_security" data-delimiter="#*#">
			<option></option>
			<?php foreach($contact_regions as $region_name) { ?>
				<option value="<?= $region_name ?>" <?= in_array($region_name, $allowed_regions) ? 'selected' : '' ?>><?= $region_name ?></option>
			<?php } ?>
		</select>
	</div>
<?php } else if($field_option == 'Location Access') {
	$allowed_locations = array_filter(explode('#*#', $contact_security['location_access']));
	if(count($allowed_locations) == 0) {
		$allowed_locations = $contact_locations;
	} ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Location Access:</label>
	<div class="col-sm-8">
		<select multiple name="location_access[]" class="chosen-select-deselect <?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" data-field="location_access" data-table="contacts_security" data-delimiter="#*#">
			<option></option>
			<?php foreach($contact_locations as $location_name) {
				$location_arr = explode('*#*', $location_name); ?>
				<option value="<?= $location_name ?>" <?= in_array($location_name, $allowed_locations) ? 'selected' : '' ?>><?= $location_arr[0] ?></option>
			<?php } ?>
		</select>
	</div>
<?php } else if($field_option == 'Classification Access') {
	$allowed_classifications = array_filter(explode('#*#', $contact_security['classification_access']));
	if(count($allowed_classifications) == 0) {
		$allowed_classifications = $contact_classifications;
	} ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Classification Access:</label>
	<div class="col-sm-8">
		<select multiple name="classification_access[]" class="chosen-select-deselect <?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" data-field="classification_access" data-table="contacts_security" data-delimiter="#*#">
			<option></option>
			<?php foreach($contact_classifications as $class_i => $classification_name) { ?>
				<option data-region="<?= $classification_regions[$class_i] ?>" value="<?= $classification_name ?>" <?= in_array($classification_name, $allowed_classifications) ? 'selected' : '' ?>><?= $classification_name ?></option>
			<?php } ?>
		</select>
	</div>
<?php } else if($field_option == 'Equipment Access') {
	$allowed_equipment = array_filter(explode(',', $contact_security['equipment_access']));
	$equipment_category = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"))['equipment_category'];
	if (!empty($equipment_category)) {
	    $equipment_category = 'Truck';
	}
	$equip_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT `equipmentid`, `unit_number`, `make`, `model`, `category`, `region`, `location`, `classification`, CONCAT(`category`, ' #', `unit_number`) label FROM `equipment` WHERE `category`='".$equipment_category."' AND `deleted`=0 ORDER BY `label`"),MYSQLI_ASSOC);
	?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> <?= $equipment_category ?> Access:</label>
	<div class="col-sm-8">
		<select multiple name="equipment_access[]" class="chosen-select-deselect <?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" data-field="equipment_access" data-table="contacts_security">
			<option></option>
			<?php foreach($equip_list as $equipment) { ?>
				<option data-region="<?= $equipment['region'] ?>" value="<?= $equipment['equipmentid'] ?>" <?= (in_array($equipment['equipmentid'], $allowed_equipment) || count($allowed_equipment) == 0) ? 'selected' : '' ?>><?= $equipment['label'] ?></option>
			<?php } ?>
		</select>
	</div>
<?php } else if($field_option == 'Dispatch Staff Access') {
	$allowed_dispatch_staff = count($contact_security['dispatch_staff_access']) > 0 ? $contact_security['dispatch_staff_access'] : 1;
	?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Dispatch Calendar Staff Access:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="checkbox" data-ischeckbox="1" name="dispatch_staff_access" value="1" <?= $allowed_dispatch_staff == 1 ? 'checked' : '' ?> data-field="dispatch_staff_access" data-table="contacts_security"> Enable</label>
	</div>
<?php } else if($field_option == 'Dispatch Team Access') {
	$allowed_dispatch_team = count($contact_security['dispatch_team_access']) > 0 ? $contact_security['dispatch_team_access'] : 1;
	?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Dispatch Calendar Team Access:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="checkbox" data-ischeckbox="1" name="dispatch_team_access" value="1" <?= $allowed_dispatch_team == 1 ? 'checked' : '' ?> data-field="dispatch_team_access" data-table="contacts_security"> Enable</label>
	</div>
<?php } else if($field_option == 'Name on Account') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Name on Account:</label>
	<div class="col-sm-8">
		<input type="text" name="name_on_account" value="<?= $contact['name_on_account'] ?>" data-field="name_on_account" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Operating As') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Operating As:</label>
	<div class="col-sm-8">
		<input type="text" name="operating_as" value="<?= $contact['operating_as'] ?>" data-field="operating_as" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Contact') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Emergency Contact:</label>
	<div class="col-sm-8">
		<input type="text" name="emergency_contact" value="<?= $contact['emergency_contact'] ?>" data-field="emergency_contact" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Occupation') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Occupation:</label>
	<div class="col-sm-8">
		<input type="text" name="occupation" value="<?= $contact['occupation'] ?>" data-field="occupation" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Company Email Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Company Email Address:</label>
	<div class="col-sm-8">
		<input type="text" name="company_email" value="<?= decryptIt($contact['company_email']) ?>" data-field="company_email" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Display Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Display Name:</label>
	<div class="col-sm-8">
		<input type="text" name="display_name" value="<?= $contact['display_name'] ?>" data-field="display_name" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Customer Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Customer Address:</label>
	<div class="col-sm-8">
		<input type="text" name="customer_address" value="<?= $contact['customer_address'] ?>" data-field="customer_address" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Referred By') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Referred By:</label>
	<div class="col-sm-8">
		<select name="referred_by" data-field="referred_by" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
			<option></option>
			<option <?php if ($contact['referred_by'] == "Referral") { echo " selected"; } ?> value="Referral">Referral</option>
			<option <?php if ($contact['referred_by'] == "Doctor") { echo " selected"; } ?> value="Doctor">Doctor</option>
			<option <?php if ($contact['referred_by'] == "Friend") { echo " selected"; } ?> value="Friend">Friend</option>
			<option <?php if ($contact['referred_by'] == "Patient") { echo " selected"; } ?> value="Patient">Patient</option>
			<option <?php if ($contact['referred_by'] == "Insurance") { echo " selected"; } ?> value="Insurance">Insurance</option>
			<option <?php if ($contact['referred_by'] == "Staff") { echo " selected"; } ?> value="Staff">Staff</option>
			<option <?php if ($contact['referred_by'] == "Business Lead") { echo " selected"; } ?> value="Business Lead">Business Lead</option>
			<option <?php if ($contact['referred_by'] == "Cold Call") { echo " selected"; } ?> value="Cold Call">Cold Call</option>
			<option <?php if ($contact['referred_by'] == "Tradeshow") { echo " selected"; } ?> value="Tradeshow">Tradeshow</option>
			<option <?php if ($contact['referred_by'] == "Website") { echo " selected"; } ?> value="Website">Website</option>
			<option <?php if ($contact['referred_by'] == "Social Media") { echo " selected"; } ?> value="Social Media">Social Media</option>
			<option <?php if ($contact['referred_by'] == "Print Media") { echo " selected"; } ?> value="Print Media">Print Media</option>
			<option <?php if ($contact['referred_by'] == "Radio") { echo " selected"; } ?> value="Radio">Radio</option>
			<option <?php if ($contact['referred_by'] == "Online") { echo " selected"; } ?> value="Online">Online</option>
			<option <?php if ($contact['referred_by'] == "Mail Out") { echo " selected"; } ?> value="Mail Out">Mail Out</option>
			<option <?php if ($contact['referred_by'] == "NP - Non-specific") { echo " selected"; } ?> value="NP - Non-specific">NP - Non-specific</option>
			<option <?php if ($contact['referred_by'] == "NP - Specific") { echo " selected"; } ?> value="NP - Specific">NP - Specific</option>
			<option <?php if ($contact['referred_by'] == "RP - Non-Specific") { echo " selected"; } ?> value="RP - Non-Specific">RP - Non-Specific</option>
			<option <?php if ($contact['referred_by'] == "RP - Specific") { echo " selected"; } ?> value="RP - Specific">RP - Specific</option>
		</select>
		<input type="text" name="referred_by_name" value="<?= $contact['referred_by_name'] ?>" data-field="referred_by_name" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Company') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Company:</label>
	<div class="col-sm-8">
		<input type="text" name="company" value="<?= $contact['company'] ?>" data-field="company" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Position') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Position:</label>
	<div class="col-sm-8">
		<input type="text" name="position" value="<?= $contact['position'] ?>" data-field="position" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Show/Hide User') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Show/Hide User:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="show_hide_user" value="1" <?= $contact['show_hide_user'] == 1 ? 'checked' : '' ?> data-field="show_hide_user" data-table="contacts"> Show</label>
		<label class="form-checkbox"><input type="radio" name="show_hide_user" value="0" <?= $contact['show_hide_user'] == 0 ? 'checked' : '' ?> data-field="show_hide_user" data-table="contacts"> Hide</label>
	</div>
<?php } else if($field_option == 'Client Tax Exemption') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client Tax Exemption:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="client_tax_exemption" value="Yes" <?= $contact['client_tax_exemption'] == 'Yes' ? 'checked' : '' ?> data-field="client_tax_exemption" data-table="contacts"> Yes</label>
		<label class="form-checkbox"><input type="radio" name="client_tax_exemption" value="No" <?= $contact['client_tax_exemption'] == 'No' ? 'checked' : '' ?> data-field="client_tax_exemption" data-table="contacts"> No</label>
	</div>
<?php } else if($field_option == 'Tax Exemption Number') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Tax Exemption #:</label>
	<div class="col-sm-8">
		<input type="text" name="tax_exemption_number" value="<?= $contact['tax_exemption_number'] ?>" data-field="tax_exemption_number" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'DUNS') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> DUNS:</label>
	<div class="col-sm-8">
		<input type="text" name="duns" value="<?= $contact['duns'] ?>" data-field="duns" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'CAGE') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> CAGE:</label>
	<div class="col-sm-8">
		<input type="text" name="cage" value="<?= $contact['cage'] ?>" data-field="cage" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'SIN' || $field_option == 'Profile SIN') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> SIN:</label>
	<div class="col-sm-8">
		<input type="number" name="sin" value="<?= $contact['sin'] ?>" data-field="sin" data-table="contacts" min="0" max="1000000000" step="1" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Employee Number') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Employee #:</label>
	<div class="col-sm-8">
		<input type="text" name="employee_num" value="<?= $contact['employee_num'] ?>" data-field="employee_num" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Self Identification') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Self Identification:</label>
	<div class="col-sm-8">
		<select name="self_identification" data-field="self_identification" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
			<option></option><option value="*NEW_VALUE*">New Identification</option>
			<?php $query = mysqli_query($dbc,"SELECT distinct(self_identification) FROM contacts");
			while($row = mysqli_fetch_array($query)) {
				echo "<option ".($contact['self_identification'] == $row['self_identification'] ? 'selected' : '')." value='". $row['category_contact']."'>".$row['category_contact'].'</option>';
			} ?>
		</select>
		<input type="text" name="self_identification" value="<?= $contact['self_identification'] ?>" data-field="self_identification" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" style="display:none;">
	</div>
<?php } else if($field_option == 'AISH Card#') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> AISH Card #:</label>
	<div class="col-sm-8">
		<input type="text" name="aish_card_no" value="<?= $contact['aish_card_no'] ?>" data-field="aish_card_no" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'License Plate #') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Licence Plate #:</label>
	<div class="col-sm-8">
		<input type="text" name="license_plate_no" value="<?= $contact['license_plate_no'] ?>" data-field="license_plate_no" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'CARFAX') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> CARFAX:</label>
	<div class="col-sm-8">
		<input type="text" name="carfax" value="<?= $contact['carfax'] ?>" data-field="carfax" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Payment Sync Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> </label>
	<div class="col-sm-8">
		<label class="form-checkbox any-width"><input type="checkbox" name="sync_payment_address" value="1" <?= $contact['payment_address']==decryptIt($contact['business_street']) && $contact['payment_city']==decryptIt($contact['business_city']) && $contact['payment_state']==decryptIt($contact['business_state']) && $contact['payment_postal_code']==decryptIt($contact['business_zip']) ? 'checked' : '' ?> data-field="address" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">Same as Main Address</label>
	</div>
<?php } else if($field_option == 'Mailing Sync Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> </label>
	<div class="col-sm-8">
		<label class="form-checkbox any-width"><input type="checkbox" name="sync_mail_address" value="1" <?= $contact['ship_to_address']==decryptIt($contact['business_street']) && $contact['ship_city']==decryptIt($contact['business_city']) && $contact['ship_state']==decryptIt($contact['business_state']) && $contact['ship_zip']==decryptIt($contact['business_zip']) ? 'checked' : '' ?> data-field="address" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">Same as Main Address</label>
	</div>
<?php } else if($field_option == 'Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Street Address:</label>
	<div class="col-sm-8">
		<input type="text" name="address" value="<?= $contact['address'] ?>" data-field="address" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Address Create Site') { ?>
	<button name="address_site_create" class="btn brand-btn pull-right">Create <?= SITES_CAT ?> From Address</button>
	<div class="clearfix"></div>
<?php } else if($field_option == 'Address Sync To Site') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> </label>
	<div class="col-sm-8">
		<label class="control-checkbox any-width"><input type="checkbox" name="address_site_sync" data-ischeckbox="true" value="1" <?= $contact['address_site_sync'] > 0 ? 'checked' : '' ?> data-field="address_site_sync" data-table="contacts"> Make this address a <?= rtrim(SITES_CAT,'s') ?></label>
	</div>
<?php } else if($field_option == 'Address Default Sync On') { ?>
	<input type="hidden" name="address_default_site_sync" value="1">
<?php } else if($field_option == 'Second Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Street Address:</label>
	<div class="col-sm-8">
		<input type="text" name="second_address" value="<?= $contact['second_address'] ?>" data-field="second_address" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Mailing Lock Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> </label>
	<div class="col-sm-8">
		<script>
		function lockMailingAddress(toggle) {
			if(toggle) {
				$('[data-field*=ship],[data-field*=mailing]').closest('div').css('pointer-events','').css('opacity','');
			} else {
				$('[data-field*=ship],[data-field*=mailing]').closest('div').css('pointer-events','none').css('opacity','0.5');
			}
		}
		$(document).ready(function() {
			lockMailingAddress($('#mail_address_lock').is(':checked'));
		});
		</script>
		<style>
		#mail_address_lock {
			display:none;
		}
		#mail_address_lock:checked+label {
			background-image: url(/img/icons/lock-open.png);
		}
		#mail_address_lock+label {
			background-image: url(/img/icons/lock.png);
			background-repeat: no-repeat;
			background-size: 1.5em;
		}
		</style>
		<input type="checkbox" onchange="lockMailingAddress(this.checked);" id="mail_address_lock"><label class="form-checkbox" for="mail_address_lock">Lock Mailing Address</label>
	</div>
<?php } else if($field_option == 'Mailing Full Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Mailing Address:</label>
	<div class="col-sm-8">
		<input type="text" name="mailing_address" value="<?= $contact['mailing_address'] ?>" data-field="mailing_address" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'City') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> City:</label>
	<div class="col-sm-8">
		<input type="text" name="city" value="<?= $contact['city'] ?>" data-field="city" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Second City') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> City:</label>
	<div class="col-sm-8">
		<input type="text" name="second_city" value="<?= $contact['second_city'] ?>" data-field="second_city" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Province') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Province:</label>
	<div class="col-sm-8">
		<input type="text" name="province" value="<?= $contact['province'] ?>" data-field="province" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Second Province') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Province:</label>
	<div class="col-sm-8">
		<input type="text" name="second_province" value="<?= $contact['second_province'] ?>" data-field="second_province" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'State') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> State:</label>
	<div class="col-sm-8">
		<input type="text" name="state" value="<?= $contact['state'] ?>" data-field="state" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Country') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Country:</label>
	<div class="col-sm-8">
		<input type="text" name="country" value="<?= $contact['country'] ?>" data-field="country" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Second Country') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Country:</label>
	<div class="col-sm-8">
		<input type="text" name="second_country" value="<?= $contact['second_country'] ?>" data-field="second_country" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Postal Code') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Postal Code:</label>
	<div class="col-sm-8">
		<input type="text" name="postal_code" value="<?= $contact['postal_code'] ?>" data-field="postal_code" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Second Postal Code') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Postal Code:</label>
	<div class="col-sm-8">
		<input type="text" name="second_postal_code" value="<?= $contact['second_postal_code'] ?>" data-field="second_postal_code" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Zip Code') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Zip Code:</label>
	<div class="col-sm-8">
		<input type="text" name="zip_code" value="<?= $contact['zip_code'] ?>" data-field="zip_code" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Full Address' || $field_option == 'Business Full Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Address:</label>
	<div class="col-sm-8">
		<input type="text" name="business_address" value="<?= $contact['business_address'] ?>" data-field="business_address" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Second Full Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Address:</label>
	<div class="col-sm-8">
		<input type="text" name="second_business_address" value="<?= $contact['second_business_address'] ?>" data-field="second_business_address" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Business Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Business Address:</label>
	<div class="col-sm-8">
		<input type="text" name="business_street" value="<?= decryptIt($contact['business_street']) ?>" data-field="business_street" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Business City') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Business City:</label>
	<div class="col-sm-8">
		<input type="text" name="business_city" value="<?= decryptIt($contact['business_city']) ?>" data-field="business_city" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Business Province') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Business Province:</label>
	<div class="col-sm-8">
		<input type="text" name="business_state" value="<?= decryptIt($contact['business_state']) ?>" data-field="business_state" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Business Country') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Business Country:</label>
	<div class="col-sm-8">
		<input type="text" name="business_country" value="<?= decryptIt($contact['business_country']) ?>" data-field="business_country" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Business Postal Code') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Business Postal Code:</label>
	<div class="col-sm-8">
		<input type="text" name="business_zip" value="<?= decryptIt($contact['business_zip']) ?>" data-field="business_zip" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Business Create Site') { ?>
	<button name="business_site_create" class="btn brand-btn pull-right">Create <?= SITES_CAT ?> From Address</button>
	<div class="clearfix"></div>
<?php } else if($field_option == 'Business Sync To Site') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> </label>
	<div class="col-sm-8">
		<label class="control-checkbox any-width"><input type="checkbox" name="business_site_sync" data-ischeckbox="true" value="1" <?= $contact['business_site_sync'] > 0 ? 'checked' : '' ?> data-field="business_site_sync" data-table="contacts"> Make this address a <?= rtrim(SITES_CAT,'s') ?></label>
	</div>
<?php } else if($field_option == 'Ship To Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Mailing Address:</label>
	<div class="col-sm-8">
		<input type="text" name="ship_to_address" value="<?= $contact['ship_to_address'] ?>" data-field="ship_to_address" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Mailing Create Site') { ?>
	<button name="mailing_site_create" class="btn brand-btn pull-right">Create <?= SITES_CAT ?> From Address</button>
	<div class="clearfix"></div>
<?php } else if($field_option == 'Mailing Sync To Site') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> </label>
	<div class="col-sm-8">
		<label class="control-checkbox any-width"><input type="checkbox" name="mailing_site_sync" data-ischeckbox="true" value="1" <?= $contact['mailing_site_sync'] > 0 ? 'checked' : '' ?> data-field="mailing_site_sync" data-table="contacts"> Make this address a <?= rtrim(SITES_CAT,'s') ?></label>
	</div>
<?php } else if($field_option == 'Ship City') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> City:</label>
	<div class="col-sm-8">
		<input type="text" name="ship_city" value="<?= $contact['ship_city'] ?>" data-field="ship_city" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Ship State') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Province:</label>
	<div class="col-sm-8">
		<input type="text" name="ship_state" value="<?= $contact['ship_state'] ?>" data-field="ship_state" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Ship Zip') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Postal Code:</label>
	<div class="col-sm-8">
		<input type="text" name="ship_zip" value="<?= $contact['ship_zip'] ?>" data-field="ship_zip" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Ship Country') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Country:</label>
	<div class="col-sm-8">
		<input type="text" name="ship_country" value="<?= $contact['ship_country'] ?>" data-field="ship_country" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Ship Google Maps Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Google Maps Link:</label>
	<div class="col-sm-8">
		<input type="text" name="ship_google_link" value="<?= $contact['ship_google_link'] ?>" data-field="ship_google_link" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Google Maps Address' || $field_option == 'Business Google Maps Address' || $field_option == 'Mailing Google Maps Address' ) { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Google Maps Address:</label>
	<div class="col-sm-8">
		<input type="text" name="google_maps_address" value="<?= $contact['google_maps_address'] ?>" data-field="google_maps_address" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Second Google Maps Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Google Maps Address:</label>
	<div class="col-sm-8">
		<input type="text" name="second_google_maps_address" value="<?= $contact['second_google_maps_address'] ?>" data-field="second_google_maps_address" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'City Part') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> City Part:</label>
	<div class="col-sm-8">
		<input type="text" name="city_part" value="<?= $contact['city_part'] ?>" data-field="city_part" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'City Part') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> City Part:</label>
	<div class="col-sm-8">
		<input type="text" name="city_part" value="<?= $contact['city_part'] ?>" data-field="city_part" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Account Number') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Account #:</label>
	<div class="col-sm-8">
		<input type="text" name="account_number" value="<?= $contact['account_number'] ?>" data-field="account_number" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Payment Type') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Payment Type:</label>
	<div class="col-sm-8">
		<!--<input type="text" name="payment_type" value="<?php //$contact['payment_type'] ?>" data-field="payment_type" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">-->
        <select name="payment_type" data-placeholder="Select a Payment Type" data-field="payment_type" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
			<option></option>
			<option <?= $contact['payment_type'] == 'Net 15' ? 'selected' : '' ?> value="Net 15">Net 15</option>
			<option <?= $contact['payment_type'] == 'Net 30' ? 'selected' : '' ?> value="Net 30">Net 30</option>
			<option <?= $contact['payment_type'] == 'Net 60' ? 'selected' : '' ?> value="Net 60">Net 60</option>
			<option <?= $contact['payment_type'] == 'Due On Receipt' ? 'selected' : '' ?> value="Due On Receipt">Due On Receipt</option>
		</select>
	</div>
<?php } else if($field_option == 'Payment Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Payment Name:</label>
	<div class="col-sm-8">
		<input type="text" name="payment_name" value="<?= $contact['payment_name'] ?>" data-field="payment_name" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Payment Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Payment Address:</label>
	<div class="col-sm-8">
		<input type="text" name="payment_address" value="<?= $contact['payment_address'] ?>" data-field="payment_address" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Payment City') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Payment City:</label>
	<div class="col-sm-8">
		<input type="text" name="payment_city" value="<?= $contact['payment_city'] ?>" data-field="payment_city" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Payment State') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Payment Province:</label>
	<div class="col-sm-8">
		<input type="text" name="payment_state" value="<?= $contact['payment_state'] ?>" data-field="payment_state" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Payment Postal Code') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Payment Postal Code:</label>
	<div class="col-sm-8">
		<input type="text" name="payment_postal_code" value="<?= $contact['payment_postal_code'] ?>" data-field="payment_postal_code" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Payment Zip Code') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Payment Zip Code:</label>
	<div class="col-sm-8">
		<input type="text" name="payment_zip_code" value="<?= $contact['payment_zip_code'] ?>" data-field="payment_zip_code" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'GST #') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> GST #:</label>
	<div class="col-sm-8">
		<input type="text" name="gst_no" value="<?= $contact['gst_no'] ?>" data-field="gst_no" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'PST #') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> PST #:</label>
	<div class="col-sm-8">
		<input type="text" name="pst_no" value="<?= $contact['pst_no'] ?>" data-field="pst_no" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Vendor GST #') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Vendor GST #:</label>
	<div class="col-sm-8">
		<input type="text" name="vendor_gst_no" value="<?= $contact['vendor_gst_no'] ?>" data-field="vendor_gst_no" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Payment Information') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Payment Information:</label>
	<div class="col-sm-8">
		<input type="text" name="payment_information" value="<?= $contact['payment_information'] ?>" data-field="payment_information" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Pricing Level') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Pricing Level:</label>
	<div class="col-sm-8">
		<select name="pricing_level" data-placeholder="Select a Pricing Level" data-field="pricing_level" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
			<option></option>
			<option <?= $contact['pricing_level'] == 'Client Price' ? 'selected' : '' ?> value="Client Price">Client Price</option>
			<option <?= $contact['pricing_level'] == 'Admin Price' ? 'selected' : '' ?> value="Admin Price">Admin Price</option>
			<option <?= $contact['pricing_level'] == 'Commercial Price' ? 'selected' : '' ?> value="Commercial Price">Commercial Price</option>
			<option <?= $contact['pricing_level'] == 'Wholesale Price' ? 'selected' : '' ?> value="Wholesale Price">Wholesale Price</option>
			<option <?= $contact['pricing_level'] == 'Final Retail Price' ? 'selected' : '' ?> value="Final Retail Price">Final Retail Price</option>
			<option <?= $contact['pricing_level'] == 'Preferred Price' ? 'selected' : '' ?> value="Preferred Price">Preferred Price</option>
			<option <?= $contact['pricing_level'] == 'Web Price' ? 'selected' : '' ?> value="Web Price">Web Price</option>
		</select>
	</div>
<?php } else if($field_option == 'Unit #') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Unit #:</label>
	<div class="col-sm-8">
		<input type="text" name="unit_no" value="<?= $contact['unit_no'] ?>" data-field="unit_no" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Bay #') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Bay #:</label>
	<div class="col-sm-8">
		<input type="text" name="bay_no" value="<?= $contact['bay_no'] ?>" data-field="bay_no" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Option to Renew') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Option to Renew:</label>
	<div class="col-sm-8">
		<input type="text" name="option_to_renew" value="<?= $contact['option_to_renew'] ?>" data-field="option_to_renew" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Contract Allocated Hours') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Total Allocated Hours:</label>
	<div class="col-sm-8">
		<input type="text" name="contract_allocated_hours" value="<?= $contact['contract_allocated_hours'] ?>" data-field="contract_allocated_hours" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Contract Allocated Hours Multiple Types') { ?>
	<?php $total_allocated_hours = 0;
	foreach(explode('#*#',$contact['contract_allocated_hours']) as $counter => $contract_allocated_hours) {
		$total_allocated_hours += floatval($contract_allocated_hours); ?>
		<div class="allocated_hours_div">
			<div class="form-group">
				<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Allocated Hours Type:</label>
				<div class="col-sm-8">
					<select name="type_contract_allocated_hours[]" class="chosen-select-deselect <?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" value="<?= explode('#*#', $contact['contract_allocated_hours_type'])[$counter] ?>" data-field="contract_allocated_hours_type" data-table="contacts" data-delimiter="#*#"><option></option>
						<?php $allocated_hours_types = get_config($dbc, FOLDER_NAME.'_'.$_GET['category'].'_allocated_hours_types');
						foreach(explode(',',$allocated_hours_types) as $allocated_hours_type) {
							echo '<option value="'.$allocated_hours_type.'" '.($allocated_hours_type == explode('#*#',$contact['contract_allocated_hours_type'])[$counter] ? 'selected' : '').'>'.$allocated_hours_type.'</option>';
						} ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Allocated Hours:</label>
				<div class="col-sm-8">
					<input type="text" name="contract_allocated_hours[]" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" value="<?= explode('#*#', $contact['contract_allocated_hours'])[$counter] ?>" data-field="contract_allocated_hours" data-table="contacts" data-delimiter="#*#" onchange="calculateAllocatedHours();">
				</div>
			</div>
			<div class="form-group pull-right">
				<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addMultiple('allocated_hours_div');">
				<img src="../img/remove.png" class="inline-img pull-right" onclick="removeMultiple('allocated_hours_div', this);">
			</div>
			<div class="clearfix"></div>
		</div>
	<?php } ?>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Total Allocated Hours:</label>
		<div class="col-sm-8">
			<input type="text" name="total_allocated_hours_calc" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" value="<?= $total_allocated_hours ?>" disabled>
		</div>
	</div>
<?php } else if($field_option == 'Lease Term - # of years') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Lease Term - # of years:</label>
	<div class="col-sm-8">
		<input type="text" name="lease_term_no_of_years" value="<?= $contact['lease_term_no_of_years'] ?>" data-field="lease_term_no_of_years" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Commercial Insurer') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Commercial Insurer:</label>
	<div class="col-sm-8">
		<input type="text" name="commercial_insurer" value="<?= $contact['commercial_insurer'] ?>" data-field="commercial_insurer" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Residential Insurer') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Residential Insurer:</label>
	<div class="col-sm-8">
		<input type="text" name="residential_insurer" value="<?= $contact['residential_insurer'] ?>" data-field="residential_insurer" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'WCB #') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> WCB #:</label>
	<div class="col-sm-8">
		<input type="text" name="wcb_no" value="<?= $contact['wcb_no'] ?>" data-field="wcb_no" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Credit Card on File') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Credit Card on File:</label>
	<div class="col-sm-8">
		<input type="text" name="cc_on_file" value="<?= $contact['cc_on_file'] ?>" data-field="cc_on_file" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Intake Form' || $field_option == 'Profile Intake Form') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Intake Form:</label>
	<div class="col-sm-8">
		<input type="text" name="***" value="<?= $intakeid ?>" data-field="***" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Client First Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client First Name:</label>
	<div class="col-sm-8">
		<input type="text" name="client_first_name" value="<?= $contact['client_first_name'] ?>" data-field="client_first_name" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Client Last Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client Last Name:</label>
	<div class="col-sm-8">
		<input type="text" name="client_last_name" value="<?= $contact['client_last_name'] ?>" data-field="client_last_name" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Client Work Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client Work Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="client_work_phone" value="<?= $contact['client_work_phone'] ?>" data-field="client_work_phone" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Client Home Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client Home Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="client_home_phone" value="<?= $contact['client_home_phone'] ?>" data-field="client_home_phone" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Client Cell Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client Cell Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="client_cell_phone" value="<?= $contact['client_cell_phone'] ?>" data-field="client_cell_phone" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Client Fax') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client Fax:</label>
	<div class="col-sm-8">
		<input type="text" name="client_fax" value="<?= $contact['client_fax'] ?>" data-field="client_fax" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Client Email Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client Email Address:</label>
	<div class="col-sm-8">
		<input type="text" name="client_email_address" value="<?= $contact['client_email_address'] ?>" data-field="client_email_address" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Personal Email') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Personal Email:</label>
	<div class="col-sm-8">
		<input type="text" name="client_email_address" value="<?= $contact['client_email_address'] ?>" data-field="client_email_address" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Client Date of Birth') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client Date of Birth:</label>
	<div class="col-sm-8">
		<input type="text" name="client_date_of_birth" value="<?= $contact['client_date_of_birth'] ?>" data-field="client_date_of_birth" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Display Age Value') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Age:</label>
	<div class="col-sm-8">
		<input type="text" name="client_date_of_birth" value="<?= ( $contact['birth_date']=='0000-00-00' || empty($contact['birth_date']) ) ? '' : ' Age: '.date_diff(date_create($contact['birth_date']), date_create('now'))->y ?>" readonly class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Height' || $field_option == 'Profile Height') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Height:</label>
	<div class="col-sm-8">
		<input type="text" name="client_height" value="<?= $contact['client_height'] ?>" data-field="client_height" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Weight' || $field_option == 'Profile Weight') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Weight:</label>
	<div class="col-sm-8">
		<input type="text" name="client_weight" value="<?= $contact['client_weight'] ?>" data-field="client_weight" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Client Height') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client Height:</label>
	<div class="col-sm-8">
		<input type="text" name="client_height" value="<?= $contact['client_height'] ?>" data-field="client_height" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Client Weight') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client Weight:</label>
	<div class="col-sm-8">
		<input type="text" name="client_weight" value="<?= $contact['client_weight'] ?>" data-field="client_weight" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Client SIN') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client SIN:</label>
	<div class="col-sm-8">
		<input type="text" name="client_sin" value="<?= $contact['client_sin'] ?>" data-field="client_sin" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Client ID') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client ID:</label>
	<div class="col-sm-8">
		<input type="text" name="client_client_id" value="<?= $contact['client_client_id'] ?>" data-field="client_client_id" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Client Client ID') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client Client ID:</label>
	<div class="col-sm-8">
		<input type="text" name="client_client_id" value="<?= $contact['client_client_id'] ?>" data-field="client_client_id" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Client Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client Address:</label>
	<div class="col-sm-8">
		<input type="text" name="client_address" value="<?= $contact['client_address'] ?>" data-field="client_address" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Client Zip Code') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client Zip Code:</label>
	<div class="col-sm-8">
		<input type="text" name="client_zip_code" value="<?= $contact['client_zip_code'] ?>" data-field="client_zip_code" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Client City') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client City:</label>
	<div class="col-sm-8">
		<input type="text" name="client_city" value="<?= $contact['client_city'] ?>" data-field="client_city" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Client Province') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client Province:</label>
	<div class="col-sm-8">
		<input type="text" name="client_province" value="<?= $contact['client_province'] ?>" data-field="client_province" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Client Country') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client Country:</label>
	<div class="col-sm-8">
		<input type="text" name="client_country" value="<?= $contact['client_country'] ?>" data-field="client_country" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Client Program Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client Home/Program Address:</label>
	<div class="col-sm-8">
		<input type="text" name="client_program_address" value="<?= $contact['client_program_address'] ?>" data-field="client_program_address" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Division Group Home 1') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Division Group Home 1:</label>
	<div class="col-sm-8">
		<input type="text" name="classification_group_home_1" value="<?= $contact['classification_group_home_1'] ?>" data-field="classification_group_home_1" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Division Group Home 2') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Division Group Home 2:</label>
	<div class="col-sm-8">
		<input type="text" name="classification_group_home_2" value="<?= $contact['classification_group_home_2'] ?>" data-field="classification_group_home_2" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Division Day Program 1') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Division Day Program 1:</label>
	<div class="col-sm-8">
		<input type="text" name="classification_day_program_1" value="<?= $contact['classification_day_program_1'] ?>" data-field="classification_day_program_1" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Division Day Program 2') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Division Day Program 2:</label>
	<div class="col-sm-8">
		<input type="text" name="classification_day_program_2" value="<?= $contact['classification_day_program_2'] ?>" data-field="classification_day_program_2" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'FSCD Number') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> FSCD Number:</label>
	<div class="col-sm-8">
		<input type="text" name="funding_fscd" value="<?= $contact['funding_fscd'] ?>" data-field="funding_fscd" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Extended Health Benefits') {
	$insurers = explode(',',$contact['insurerid']);
	$plans = explode(',',$contact['plan_acctno']); ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> First Insurer:</label>
	<div class="col-sm-8">
		<input type="text" name="insurerid[]" value="<?= $insurers[0] ?>" data-field="insurerid" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> First Plan/Acct #:</label>
	<div class="col-sm-8">
		<input type="text" name="plan_acctno[]" value="<?= $plans[0] ?>" data-field="plan_acctno" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> First Insurer:</label>
	<div class="col-sm-8">
		<input type="text" name="insurerid[]" value="<?= $insurers[1] ?>" data-field="insurerid" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> First Plan/Acct #:</label>
	<div class="col-sm-8">
		<input type="text" name="plan_acctno[]" value="<?= $plans[1] ?>" data-field="plan_acctno" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> First Insurer:</label>
	<div class="col-sm-8">
		<input type="text" name="insurerid[]" value="<?= $insurers[2] ?>" data-field="insurerid" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> First Plan/Acct #:</label>
	<div class="col-sm-8">
		<input type="text" name="plan_acctno[]" value="<?= $plans[2] ?>" data-field="plan_acctno" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> First Insurer:</label>
	<div class="col-sm-8">
		<input type="text" name="insurerid[]" value="<?= $insurers[3] ?>" data-field="insurerid" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> First Plan/Acct #:</label>
	<div class="col-sm-8">
		<input type="text" name="plan_acctno[]" value="<?= $plans[3] ?>" data-field="plan_acctno" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'School' || $field_option == 'Profile School') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> School:</label>
	<div class="col-sm-8">
		<input type="text" name="school" value="<?= $contact['school'] ?>" data-field="school" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Hear About') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> How Did You Hear Hear About Us:</label>
	<div class="col-sm-8">
		<input type="text" name="hear_about" value="<?= $contact['hear_about'] ?>" data-field="hear_about" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Transportation Mode of Transportation') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Mode of Transportation:</label>
	<div class="col-sm-8">
		<input type="text" name="transportation_mode_of_transportation" value="<?= $contact['transportation_mode_of_transportation'] ?>" data-field="transportation_mode_of_transportation" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Transportation Transit Access') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Transit Access:</label>
	<div class="col-sm-8">
		<input type="text" name="transportation_transit_access" value="<?= $contact['transportation_transit_access'] ?>" data-field="transportation_transit_access" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Transportation Access Password') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Access Password:</label>
	<div class="col-sm-8">
		<input type="text" name="transportation_access_password" value="<?= $contact['transportation_access_password'] ?>" data-field="transportation_access_password" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Transportation Drivers License') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Drivers Licence:</label>
	<div class="col-sm-8">
		<input type="text" name="transportation_drivers_license" value="<?= $contact['transportation_drivers_license'] ?>" data-field="transportation_drivers_license" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Drivers License Class') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Drivers License Class:</label>
	<div class="col-sm-8">
		<input type="text" name="transportation_drivers_class" value="<?= $contact['transportation_drivers_class'] ?>" data-field="transportation_drivers_class" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Drive Manual Transmission') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Driver Can Operate a Standard Transmission Vehicle:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="transportation_drivers_transmission" value="Yes" <?= $contact['transportation_drivers_transmission'] == 'Yes' ? 'checked' : '' ?> data-field="transportation_drivers_transmission" data-table="contacts_medical"> Yes</label>
		<label class="form-checkbox"><input type="radio" name="transportation_drivers_transmission" value="No" <?= $contact['transportation_drivers_transmission'] == 'Yes' ? '' : 'checked' ?> data-field="transportation_drivers_transmission" data-table="contacts_medical"> No</label>
	</div>
<?php } else if($field_option == 'Transportation Drivers Glasses') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Driver Requires Glasses/Contacts by Law:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="transportation_drivers_glasses" value="Yes" <?= $contact['transportation_drivers_glasses'] == 'Yes' ? 'checked' : '' ?> data-field="transportation_drivers_glasses" data-table="contacts_medical"> Yes</label>
		<label class="form-checkbox"><input type="radio" name="transportation_drivers_glasses" value="No" <?= $contact['transportation_drivers_glasses'] == 'Yes' ? '' : 'checked' ?> data-field="transportation_drivers_glasses" data-table="contacts_medical"> No</label>
	</div>
<?php } else if($field_option == 'Bank Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Bank Name:</label>
	<div class="col-sm-8">
		<input type="text" name="bank_name" value="<?= $contact['bank_name'] ?>" data-field="bank_name" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Bank Institution Number') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Bank Institution Number:</label>
	<div class="col-sm-8">
		<input type="text" name="bank_institution_number" value="<?= $contact['bank_institution_number'] ?>" data-field="bank_institution_number" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Bank Transit Number') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Bank Transit Number:</label>
	<div class="col-sm-8">
		<input type="text" name="bank_transit" value="<?= $contact['bank_transit'] ?>" data-field="bank_transit" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Bank Account Number') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Bank Account Number:</label>
	<div class="col-sm-8">
		<input type="text" name="bank_account_number" value="<?= $contact['bank_account_number'] ?>" data-field="bank_account_number" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Contact First Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> First Name:</label>
	<div class="col-sm-8">
		<input type="text" name="emergency_first_name[]" value="<?= explode('*#*', $contact['emergency_first_name'])[$counter] ?>" data-field="emergency_first_name" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Contact Last Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Last Name:</label>
	<div class="col-sm-8">
		<input type="text" name="emergency_last_name[]" value="<?= explode('*#*', $contact['emergency_last_name'])[$counter] ?>" data-field="emergency_last_name" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Contact Contact Number') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Contact Number:</label>
	<div class="col-sm-8">
		<input type="text" name="emergency_contact_number[]" value="<?= explode('*#*', $contact['emergency_contact_number'])[$counter] ?>" data-field="emergency_contact_number" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Contact Work Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Office Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="emergency_work_phone[]" value="<?= explode('*#*', $contact['emergency_work_phone'])[$counter] ?>" data-field="emergency_work_phone" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Contact Cell Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Cell Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="emergency_cell_phone[]" value="<?= explode('*#*', $contact['emergency_cell_phone'])[$counter] ?>" data-field="emergency_cell_phone" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Contact Home Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Home Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="emergency_home_phone[]" value="<?= explode('*#*', $contact['emergency_home_phone'])[$counter] ?>" data-field="emergency_home_phone" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Contact Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Address:</label>
	<div class="col-sm-8">
		<input type="text" name="emergency_address[]" value="<?= explode('*#*', $contact['emergency_address'])[$counter] ?>" data-field="emergency_address" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Contact Postal Code') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Postal Code:</label>
	<div class="col-sm-8">
		<input type="text" name="emergency_postal_code[]" value="<?= explode('*#*', $contact['emergency_postal_code'])[$counter] ?>" data-field="emergency_postal_code" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Contact City') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> City:</label>
	<div class="col-sm-8">
		<input type="text" name="emergency_city[]" value="<?= explode('*#*', $contact['emergency_city'])[$counter] ?>" data-field="emergency_city" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Contact County') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> County:</label>
	<div class="col-sm-8">
		<input type="text" name="emergency_county[]" value="<?= explode('*#*', $contact['emergency_county'])[$counter] ?>" data-field="emergency_county" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Contact Province') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Province:</label>
	<div class="col-sm-8">
		<input type="text" name="emergency_province[]" value="<?= explode('*#*', $contact['emergency_province'])[$counter] ?>" data-field="emergency_province" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Contact Country') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Country:</label>
	<div class="col-sm-8">
		<input type="text" name="emergency_country[]" value="<?= explode('*#*', $contact['emergency_country'])[$counter] ?>" data-field="emergency_country" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Contact Email') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Email:</label>
	<div class="col-sm-8">
		<input type="text" name="emergency_email[]" value="<?= explode('*#*', $contact['emergency_email'])[$counter] ?>" data-field="emergency_email" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Contact Relationship') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Relationship:</label>
	<div class="col-sm-8">
		<input type="text" name="emergency_relationship[]" value="<?= explode('*#*', $contact['emergency_relationship'])[$counter] ?>" data-field="emergency_relationship" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Primary Emergency Contact First Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Primary Emergency Contact First Name:</label>
	<div class="col-sm-8">
		<input type="text" name="pri_emergency_first_name[]" value="<?= explode('*#*', $contact['pri_emergency_first_name'])[$counter] ?>" data-field="pri_emergency_first_name" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Primary Emergency Contact Last Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Primary Emergency Contact Last Name:</label>
	<div class="col-sm-8">
		<input type="text" name="pri_emergency_last_name[]" value="<?= explode('*#*', $contact['pri_emergency_last_name'])[$counter] ?>" data-field="pri_emergency_last_name" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Primary Emergency Contact Cell Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Primary Emergency Contact Cell Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="pri_emergency_cell_phone[]" value="<?= explode('*#*', $contact['pri_emergency_cell_phone'])[$counter] ?>" data-field="pri_emergency_cell_phone" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Primary Emergency Contact Home Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Primary Emergency Contact Home Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="pri_emergency_home_phone[]" value="<?= explode('*#*', $contact['pri_emergency_home_phone'])[$counter] ?>" data-field="pri_emergency_home_phone" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Primary Emergency Contact Email') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Primary Emergency Contact Email:</label>
	<div class="col-sm-8">
		<input type="text" name="pri_emergency_email[]" value="<?= explode('*#*', $contact['pri_emergency_email'])[$counter] ?>" data-field="pri_emergency_email" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Primary Emergency Contact Relationship') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Primary Emergency Contact Relationship:</label>
	<div class="col-sm-8">
		<input type="text" name="pri_emergency_relation[]" value="<?= explode('*#*', $contact['pri_emergency_relation'])[$counter] ?>" data-field="pri_emergency_relation" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Secondary Emergency Contact First Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Secondary Emergency Contact First Name:</label>
	<div class="col-sm-8">
		<input type="text" name="sec_emergency_first_name[]" value="<?= explode('*#*', $contact['sec_emergency_first_name'])[$counter] ?>" data-field="sec_emergency_first_name" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Secondary Emergency Contact Last Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Secondary Emergency Contact Last Name:</label>
	<div class="col-sm-8">
		<input type="text" name="sec_emergency_last_name[]" value="<?= explode('*#*', $contact['sec_emergency_last_name'])[$counter] ?>" data-field="sec_emergency_last_name" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Secondary Emergency Contact Cell Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Secondary Emergency Contact Cell Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="sec_emergency_cell_phone[]" value="<?= explode('*#*', $contact['sec_emergency_cell_phone'])[$counter] ?>" data-field="sec_emergency_cell_phone" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Secondary Emergency Contact Home Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Secondary Emergency Contact Home Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="sec_emergency_home_phone[]" value="<?= explode('*#*', $contact['sec_emergency_home_phone'])[$counter] ?>" data-field="sec_emergency_home_phone" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Secondary Emergency Contact Email') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Secondary Emergency Contact Email:</label>
	<div class="col-sm-8">
		<input type="text" name="sec_emergency_email[]" value="<?= explode('*#*', $contact['sec_emergency_email'])[$counter] ?>" data-field="sec_emergency_email" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Secondary Emergency Contact Relationship') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Secondary Emergency Contact Relationship:</label>
	<div class="col-sm-8">
		<input type="text" name="sec_emergency_relation[]" value="<?= explode('*#*', $contact['sec_emergency_relation'])[$counter] ?>" data-field="sec_emergency_relation" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Emergency Contact Multiple') { ?>
	<div class="pull-right">
		<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addMultiple('emergency_contact_multiple');">
		<img src="../img/remove.png" class="inline-img pull-right" onclick="removeMultiple('emergency_contact_multiple', this);">
	</div>
<?php } else if($field_option == 'Health Care Number' || $field_option == 'Profile Health Care Number') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Health Care Number:</label>
	<div class="col-sm-8">
		<input type="text" name="health_care_num" value="<?= $contact['health_care_num'] ?>" data-field="health_care_num" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Insurance Alberta Health Care' || $field_option == 'Profile Insurance Alberta Health Care') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Alberta Health Care:</label>
	<div class="col-sm-8">
		<input type="text" name="insurance_alberta_health_care" value="<?= $contact['insurance_alberta_health_care'] ?>" data-field="insurance_alberta_health_care" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Insurance AISH Entrance Date') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> AISH Entrance Date:</label>
	<div class="col-sm-8">
		<input type="text" name="insurance_aish_entrance_date" value="<?= $contact['insurance_aish_entrance_date'] ?>" data-field="insurance_aish_entrance_date" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Insurance AISH #') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> AISH #:</label>
	<div class="col-sm-8">
		<input type="text" name="insurance_aish" value="<?= $contact['insurance_aish'] ?>" data-field="insurance_aish" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'AISH #' || $field_option == 'Profile AISH #') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> AISH #:</label>
	<div class="col-sm-8">
		<input type="text" name="insurance_aish" value="<?= $contact['insurance_aish'] ?>" data-field="insurance_aish" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Insurance Client ID') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Client ID:</label>
	<div class="col-sm-8">
		<input type="text" name="insurance_client_id" value="<?= $contact['insurance_client_id'] ?>" data-field="insurance_client_id" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Guardians Type') {
    $guardians_tab_list = explode('#*#', get_config($dbc, 'guardian_type_tabs')); ?>
    <label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Guardian Type:</label>
    <div class="col-sm-8">
        <select name="" data-table="contacts_medical" data-field="guardians_type" data-placeholder="Choose Guardian Type..." class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
            <option value=""></option><?php
            $guardians_type = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `guardians_type` FROM `contacts_medical` WHERE `contactid`='$contactid'"));
            foreach($guardians_tab_list as $guardian_tab) {
                $selected = ( $guardians_type['guardians_type']==$guardian_tab ) ? 'selected="selected"' : '' ; ?>
                <option value="<?= $guardian_tab; ?>" <?= $selected; ?>><?= $guardian_tab; ?></option><?php
            } ?>
        </select>
    </div>

<?php } else if($field_option == 'Guardians Family Guardian') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Family Guardian:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="guardians_family_guardian" value="1" <?= $contact['guardians_family_guardian'] == '1' ? 'checked' : '' ?> data-field="guardians_family_guardian" data-table="contacts_medical"> Yes</label>
		<label class="form-checkbox"><input type="radio" name="guardians_family_guardian" value="0" <?= $contact['guardians_family_guardian'] == '1' ? '' : 'checked' ?> data-field="guardians_family_guardian" data-table="contacts_medical"> No</label>
	</div>
<?php } else if($field_option == 'Guardians Self' && $contact['birth_date'] != '0000-00-00' && !empty($contact['birth_date']) && date_diff(date_create($contact['birth_date']), date_create('now'))->y >= 18) { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Is this individual his/her own guardian? (Age 18+):</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="guardians_self" value="1" <?= $contact['guardians_self'] == '1' ? 'checked' : '' ?> data-field="guardians_self" data-table="contacts_medical"> Yes</label>
		<label class="form-checkbox"><input type="radio" name="guardians_self" value="0" <?= $contact['guardians_self'] == '1' ? '' : 'checked' ?> data-field="guardians_self" data-table="contacts_medical"> No</label>
	</div>
<?php } else if($field_option == 'Guardians Family Appointed Guardian') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Family Appointed Guardian:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="guardians_family_appointed_guardian" value="1" <?= $contact['guardians_family_appointed_guardian'] == '1' ? 'checked' : '' ?> data-field="guardians_family_appointed_guardian" data-table="contacts_medical"> Yes</label>
		<label class="form-checkbox"><input type="radio" name="guardians_family_appointed_guardian" value="0" <?= $contact['guardians_family_appointed_guardian'] == '1' ? '' : 'checked' ?> data-field="guardians_family_appointed_guardian" data-table="contacts_medical"> No</label>
	</div>
<?php } else if($field_option == 'Guardians Public Guardian') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Public Guardian:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="guardians_public_guardian" value="1" <?= $contact['guardians_public_guardian'] == '1' ? 'checked' : '' ?> data-field="guardians_public_guardian" data-table="contacts_medical"> Yes</label>
		<label class="form-checkbox"><input type="radio" name="guardians_public_guardian" value="0" <?= $contact['guardians_public_guardian'] == '1' ? '' : 'checked' ?> data-field="guardians_public_guardian" data-table="contacts_medical"> No</label>
	</div>
<?php } else if($field_option == 'Guardians Court Appointed Guardian') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Court Appointed Guardian:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="guardians_court_appointed_guardian" value="1" <?= $contact['guardians_court_appointed_guardian'] == '1' ? 'checked' : '' ?> data-field="guardians_court_appointed_guardian" data-table="contacts_medical"> Yes</label>
		<label class="form-checkbox"><input type="radio" name="guardians_court_appointed_guardian" value="0" <?= $contact['guardians_court_appointed_guardian'] == '1' ? '' : 'checked' ?> data-field="guardians_court_appointed_guardian" data-table="contacts_medical"> No</label>
	</div>
<?php } else if($field_option == 'Guardians First Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> First Name:</label>
	<div class="col-sm-8">
		<input type="text" name="guardians_first_name[]" value="<?= explode('*#*', $contact['guardians_first_name'])[$counter] ?>" data-field="guardians_first_name" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Guardians Last Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Last Name:</label>
	<div class="col-sm-8">
		<input type="text" name="guardians_last_name[]" value="<?= explode('*#*', $contact['guardians_last_name'])[$counter] ?>" data-field="guardians_last_name" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Guardians Relationship') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Relationship:</label>
	<div class="col-sm-8">
		<input type="text" name="guardians_relationship[]" value="<?= explode('*#*', $contact['guardians_relationship'])[$counter] ?>" data-field="guardians_relationship" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Guardians Work Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Office Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="guardians_work_phone[]" value="<?= explode('*#*', $contact['guardians_work_phone'])[$counter] ?>" data-field="guardians_work_phone" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Guardians Home Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Home Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="guardians_home_phone[]" value="<?= explode('*#*', $contact['guardians_home_phone'])[$counter] ?>" data-field="guardians_home_phone" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Guardians Cell Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Cell Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="guardians_cell_phone[]" value="<?= explode('*#*', $contact['guardians_cell_phone'])[$counter] ?>" data-field="guardians_cell_phone" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Guardians Fax') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Fax:</label>
	<div class="col-sm-8">
		<input type="text" name="guardians_fax[]" value="<?= explode('*#*', $contact['guardians_fax'])[$counter] ?>" data-field="guardians_fax" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Guardians Email Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Email Address:</label>
	<div class="col-sm-8">
		<input type="text" name="guardians_email_address[]" value="<?= explode('*#*', $contact['guardians_email_address'])[$counter] ?>" data-field="guardians_email_address" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Guardians Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Address:</label>
	<div class="col-sm-8">
		<input type="text" name="guardians_address[]" value="<?= explode('*#*', $contact['guardians_address'])[$counter] ?>" data-field="guardians_address" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Guardians Zip Code') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Zip Code:</label>
	<div class="col-sm-8">
		<input type="text" name="guardians_zip_code[]" value="<?= explode('*#*', $contact['guardians_zip_code'])[$counter] ?>" data-field="guardians_zip_code" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Guardians Town') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Town:</label>
	<div class="col-sm-8">
		<input type="text" name="guardians_town[]" value="<?= explode('*#*', $contact['guardians_town'])[$counter] ?>" data-field="guardians_town" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Guardians City') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> City:</label>
	<div class="col-sm-8">
		<input type="text" name="guardians_town[]" value="<?= explode('*#*', $contact['guardians_town'])[$counter] ?>" data-field="guardians_town" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Guardians Postal Code') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Postal Code:</label>
	<div class="col-sm-8">
		<input type="text" name="guardians_zip_code[]" value="<?= explode('*#*', $contact['guardians_zip_code'])[$counter] ?>" data-field="guardians_zip_code" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Guardians County') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> County:</label>
	<div class="col-sm-8">
		<input type="text" name="guardians_county[]" value="<?= explode('*#*', $contact['guardians_county'])[$counter] ?>" data-field="guardians_county" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Guardians Province') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Province:</label>
	<div class="col-sm-8">
		<input type="text" name="guardians_province[]" value="<?= explode('*#*', $contact['guardians_province'])[$counter] ?>" data-field="guardians_province" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Guardians Country') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Country:</label>
	<div class="col-sm-8">
		<input type="text" name="guardians_country[]" value="<?= explode('*#*', $contact['guardians_country'])[$counter] ?>" data-field="guardians_country" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Guardians Multiple') { ?>
	<div class="pull-right">
		<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addMultiple('guardians_multiple');">
		<img src="../img/remove.png" class="inline-img pull-right" onclick="removeMultiple('guardians_multiple', this);">
	</div>
<?php } else if($field_option == 'Guardians Siblings') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Siblings:</label>
	<div class="col-sm-8">
		<input type="text" name="guardians_siblings" value="<?= $contact['guardians_siblings'] ?>" data-field="guardians_siblings" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Siblings First Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> First Name:</label>
	<div class="col-sm-8">
		<input type="text" name="siblings_first[]" value="<?= explode('*#*', $contact['siblings_first'])[$counter] ?>" data-field="siblings_first" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Siblings Last Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Last Name:</label>
	<div class="col-sm-8">
		<input type="text" name="siblings_last[]" value="<?= explode('*#*', $contact['siblings_last'])[$counter] ?>" data-field="siblings_last" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Siblings Home Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Home Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="siblings_home[]" value="<?= explode('*#*', $contact['siblings_home'])[$counter] ?>" data-field="siblings_home" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Siblings Cell Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Cell Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="siblings_cell[]" value="<?= explode('*#*', $contact['siblings_cell'])[$counter] ?>" data-field="siblings_cell" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Siblings Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Address:</label>
	<div class="col-sm-8">
		<input type="text" name="siblings_address[]" value="<?= explode('*#*', $contact['siblings_address'])[$counter] ?>" data-field="siblings_address" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Siblings City') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> City:</label>
	<div class="col-sm-8">
		<input type="text" name="siblings_city[]" value="<?= explode('*#*', $contact['siblings_city'])[$counter] ?>" data-field="siblings_city" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Siblings Postal Code') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Postal Code:</label>
	<div class="col-sm-8">
		<input type="text" name="siblings_postal[]" value="<?= explode('*#*', $contact['siblings_postal'])[$counter] ?>" data-field="siblings_postal" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Siblings Province') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Province:</label>
	<div class="col-sm-8">
		<input type="text" name="siblings_province[]" value="<?= explode('*#*', $contact['siblings_province'])[$counter] ?>" data-field="siblings_province" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Siblings Country') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Country:</label>
	<div class="col-sm-8">
		<input type="text" name="siblings_country[]" value="<?= explode('*#*', $contact['siblings_country'])[$counter] ?>" data-field="siblings_country" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Siblings Multiple') { ?>
	<div class="pull-right">
		<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addMultiple('siblings_multiple');">
		<img src="../img/remove.png" class="inline-img pull-right" onclick="removeMultiple('siblings_multiple', this);">
	</div>
<?php } else if($field_option == 'Trustee Family Trustee') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Trustee Family Trustee:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="trustee_family_guardian" value="1" <?= $contact['trustee_family_guardian'] == '1' ? 'checked' : '' ?> data-field="trustee_family_guardian" data-table="contacts_medical"> Yes</label>
		<label class="form-checkbox"><input type="radio" name="trustee_family_guardian" value="0" <?= $contact['trustee_family_guardian'] == '1' ? '' : 'checked' ?> data-field="trustee_family_guardian" data-table="contacts_medical"> No</label>
	</div>
<?php } else if($field_option == 'Trustee Family Appointed Trustee') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Trustee Family Appointed Trustee:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="trustee_family_appointed_guardian" value="1" <?= $contact['trustee_family_appointed_guardian'] == '1' ? 'checked' : '' ?> data-field="trustee_family_appointed_guardian" data-table="contacts_medical"> Yes</label>
		<label class="form-checkbox"><input type="radio" name="trustee_family_appointed_guardian" value="0" <?= $contact['trustee_family_appointed_guardian'] == '1' ? '' : 'checked' ?> data-field="trustee_family_appointed_guardian" data-table="contacts_medical"> No</label>
	</div>
<?php } else if($field_option == 'Trustee Public Trustee') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Trustee Public Trustee:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="trustee_public_guardian" value="1" <?= $contact['trustee_public_guardian'] == '1' ? 'checked' : '' ?> data-field="trustee_public_guardian" data-table="contacts_medical"> Yes</label>
		<label class="form-checkbox"><input type="radio" name="trustee_public_guardian" value="0" <?= $contact['trustee_public_guardian'] == '1' ? '' : 'checked' ?> data-field="trustee_public_guardian" data-table="contacts_medical"> No</label>
	</div>
<?php } else if($field_option == 'Trustee Court Appointed Trustee') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Trustee Court Appointed Trustee:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="trustee_court_appointed_guardian" value="1" <?= $contact['trustee_court_appointed_guardian'] == '1' ? 'checked' : '' ?> data-field="trustee_court_appointed_guardian" data-table="contacts_medical"> Yes</label>
		<label class="form-checkbox"><input type="radio" name="trustee_court_appointed_guardian" value="0" <?= $contact['trustee_court_appointed_guardian'] == '1' ? '' : 'checked' ?> data-field="trustee_court_appointed_guardian" data-table="contacts_medical"> No</label>
	</div>
<?php } else if($field_option == 'Trustee First Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Trustee First Name:</label>
	<div class="col-sm-8">
		<input type="text" name="trustee_first_name" value="<?= $contact['trustee_first_name'] ?>" data-field="trustee_first_name" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Trustee Last Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Trustee Last Name:</label>
	<div class="col-sm-8">
		<input type="text" name="trustee_last_name" value="<?= $contact['trustee_last_name'] ?>" data-field="trustee_last_name" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Trustee Work Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Trustee Work Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="trustee_work_phone" value="<?= $contact['trustee_work_phone'] ?>" data-field="trustee_work_phone" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Trustee Home Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Trustee Home Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="trustee_home_phone" value="<?= $contact['trustee_home_phone'] ?>" data-field="trustee_home_phone" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Trustee Cell Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Trustee Cell Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="trustee_cell_phone" value="<?= $contact['trustee_cell_phone'] ?>" data-field="trustee_cell_phone" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Trustee Fax') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Trustee Fax:</label>
	<div class="col-sm-8">
		<input type="text" name="trustee_fax" value="<?= $contact['trustee_fax'] ?>" data-field="trustee_fax" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Trustee Email Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Trustee Email Address:</label>
	<div class="col-sm-8">
		<input type="text" name="trustee_email_address" value="<?= $contact['trustee_email_address'] ?>" data-field="trustee_email_address" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Trustee Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Trustee Address:</label>
	<div class="col-sm-8">
		<input type="text" name="trustee_address" value="<?= $contact['trustee_address'] ?>" data-field="trustee_address" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Trustee Zip Code') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Trustee Zip Code:</label>
	<div class="col-sm-8">
		<input type="text" name="trustee_zip_code" value="<?= $contact['trustee_zip_code'] ?>" data-field="trustee_zip_code" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Trustee Town') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Trustee Town:</label>
	<div class="col-sm-8">
		<input type="text" name="trustee_town" value="<?= $contact['trustee_town'] ?>" data-field="trustee_town" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Trustee Province') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Trustee Province:</label>
	<div class="col-sm-8">
		<input type="text" name="trustee_province" value="<?= $contact['trustee_province'] ?>" data-field="trustee_province" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Trustee Country') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Trustee Country:</label>
	<div class="col-sm-8">
		<input type="text" name="trustee_country" value="<?= $contact['trustee_country'] ?>" data-field="trustee_country" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Family Doctor First Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Family Doctor First Name:</label>
	<div class="col-sm-8">
		<input type="text" name="family_doctor_first_name[]" value="<?= explode('*#*', $contact['family_doctor_first_name'])[$counter] ?>" data-field="family_doctor_first_name" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Family Doctor Last Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Family Doctor Last Name:</label>
	<div class="col-sm-8">
		<input type="text" name="family_doctor_last_name[]" value="<?= explode('*#*', $contact['family_doctor_last_name'])[$counter] ?>" data-field="family_doctor_last_name" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Family Doctor Work Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Family Doctor Work Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="family_doctor_work_phone[]" value="<?= explode('*#*', $contact['family_doctor_work_phone'])[$counter] ?>" data-field="family_doctor_work_phone" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Family Doctor Home Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Family Doctor Home Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="family_doctor_home_phone[]" value="<?= explode('*#*', $contact['family_doctor_home_phone'])[$counter] ?>" data-field="family_doctor_home_phone" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Family Doctor Cell Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Family Doctor Cell Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="family_doctor_cell_phone[]" value="<?= explode('*#*', $contact['family_doctor_cell_phone'])[$counter] ?>" data-field="family_doctor_cell_phone" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Family Doctor Fax') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Family Doctor Fax:</label>
	<div class="col-sm-8">
		<input type="text" name="family_doctor_fax[]" value="<?= explode('*#*', $contact['family_doctor_fax'])[$counter] ?>" data-field="family_doctor_fax" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Family Doctor Email Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Family Doctor Email Address:</label>
	<div class="col-sm-8">
		<input type="text" name="family_doctor_email_address[]" value="<?= explode('*#*', $contact['family_doctor_email_address'])[$counter] ?>" data-field="family_doctor_email_address" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Family Doctor Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Family Doctor Address:</label>
	<div class="col-sm-8">
		<input type="text" name="family_doctor_address[]" value="<?= explode('*#*', $contact['family_doctor_address'])[$counter] ?>" data-field="family_doctor_address" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Family Doctor Zip Code') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Family Doctor Zip Code:</label>
	<div class="col-sm-8">
		<input type="text" name="family_doctor_zip_code[]" value="<?= explode('*#*', $contact['family_doctor_zip_code'])[$counter] ?>" data-field="family_doctor_zip_code" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Family Doctor Postal Code') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Family Doctor Postal Code:</label>
	<div class="col-sm-8">
		<input type="text" name="family_doctor_zip_code[]" value="<?= explode('*#*', $contact['family_doctor_zip_code'])[$counter] ?>" data-field="family_doctor_zip_code" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Family Doctor Town') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Family Doctor Town:</label>
	<div class="col-sm-8">
		<input type="text" name="family_doctor_town[]" value="<?= explode('*#*', $contact['family_doctor_town'])[$counter] ?>" data-field="family_doctor_town" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Family Doctor City') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Family Doctor City:</label>
	<div class="col-sm-8">
		<input type="text" name="family_doctor_town[]" value="<?= explode('*#*', $contact['family_doctor_town'])[$counter] ?>" data-field="family_doctor_town" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Family Doctor County') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Family Doctor County:</label>
	<div class="col-sm-8">
		<input type="text" name="family_doctor_county[]" value="<?= explode('*#*', $contact['family_doctor_county'])[$counter] ?>" data-field="family_doctor_county" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Family Doctor Province') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Family Doctor Province:</label>
	<div class="col-sm-8">
		<input type="text" name="family_doctor_province[]" value="<?= explode('*#*', $contact['family_doctor_province'])[$counter] ?>" data-field="family_doctor_province" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Family Doctor Country') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Family Doctor Country:</label>
	<div class="col-sm-8">
		<input type="text" name="family_doctor_country[]" value="<?= explode('*#*', $contact['family_doctor_country'])[$counter] ?>" data-field="family_doctor_country" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Family Doctor Multiple') { ?>
	<div class="pull-right">
		<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addMultiple('doctors_multiple');">
		<img src="../img/remove.png" class="inline-img pull-right" onclick="removeMultiple('doctors_multiple', this);">
	</div>
<?php } else if($field_option == 'Dentist First Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Dentist First Name:</label>
	<div class="col-sm-8">
		<input type="text" name="dentist_first_name" value="<?= $contact['dentist_first_name'] ?>" data-field="dentist_first_name" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Dentist Last Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Dentist Last Name:</label>
	<div class="col-sm-8">
		<input type="text" name="dentist_last_name" value="<?= $contact['dentist_last_name'] ?>" data-field="dentist_last_name" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Dentist Work Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Dentist Work Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="dentist_work_phone" value="<?= $contact['dentist_work_phone'] ?>" data-field="dentist_work_phone" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Dentist Home Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Dentist Home Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="dentist_home_phone" value="<?= $contact['dentist_home_phone'] ?>" data-field="dentist_home_phone" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Dentist Cell Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Dentist Cell Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="dentist_cell_phone" value="<?= $contact['dentist_cell_phone'] ?>" data-field="dentist_cell_phone" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Dentist Fax') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Dentist Fax:</label>
	<div class="col-sm-8">
		<input type="text" name="dentist_fax" value="<?= $contact['dentist_fax'] ?>" data-field="dentist_fax" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Dentist Email Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Dentist Email Address:</label>
	<div class="col-sm-8">
		<input type="text" name="dentist_email_address" value="<?= $contact['dentist_email_address'] ?>" data-field="dentist_email_address" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Dentist Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Dentist Address:</label>
	<div class="col-sm-8">
		<input type="text" name="dentist_address" value="<?= $contact['dentist_address'] ?>" data-field="dentist_address" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Dentist Zip Code') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Dentist Zip Code:</label>
	<div class="col-sm-8">
		<input type="text" name="dentist_zip_code" value="<?= $contact['dentist_zip_code'] ?>" data-field="dentist_zip_code" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Dentist Town') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Dentist Town:</label>
	<div class="col-sm-8">
		<input type="text" name="dentist_town" value="<?= $contact['dentist_town'] ?>" data-field="dentist_town" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Dentist Province') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Dentist Province:</label>
	<div class="col-sm-8">
		<input type="text" name="dentist_province" value="<?= $contact['dentist_province'] ?>" data-field="dentist_province" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Dentist Country') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Dentist Country:</label>
	<div class="col-sm-8">
		<input type="text" name="dentist_country" value="<?= $contact['dentist_country'] ?>" data-field="dentist_country" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Specialists First Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Specialists First Name:</label>
	<div class="col-sm-8">
		<input type="text" name="specialists_first_name[]" value="<?= explode('*#*', $contact['specialists_first_name'])[$counter] ?>" data-field="specialists_first_name" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Specialists Last Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Specialists Last Name:</label>
	<div class="col-sm-8">
		<input type="text" name="specialists_last_name[]" value="<?= explode('*#*', $contact['specialists_last_name'])[$counter] ?>" data-field="specialists_last_name" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Specialists Work Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Specialists Work Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="specialists_work_phone[]" value="<?= explode('*#*', $contact['specialists_work_phone'])[$counter] ?>" data-field="specialists_work_phone" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Specialists Home Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Specialists Home Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="specialists_home_phone[]" value="<?= explode('*#*', $contact['specialists_home_phone'])[$counter] ?>" data-field="specialists_home_phone" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Specialists Cell Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Specialists Cell Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="specialists_cell_phone[]" value="<?= explode('*#*', $contact['specialists_cell_phone'])[$counter] ?>" data-field="specialists_cell_phone" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Specialists Fax') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Specialists Fax:</label>
	<div class="col-sm-8">
		<input type="text" name="specialists_fax[]" value="<?= explode('*#*', $contact['specialists_fax'])[$counter] ?>" data-field="specialists_fax" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Specialists Email Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Specialists Email Address:</label>
	<div class="col-sm-8">
		<input type="text" name="specialists_email_address[]" value="<?= explode('*#*', $contact['specialists_email_address'])[$counter] ?>" data-field="specialists_email_address" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Specialists Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Specialists Address:</label>
	<div class="col-sm-8">
		<input type="text" name="specialists_address[]" value="<?= explode('*#*', $contact['specialists_address'])[$counter] ?>" data-field="specialists_address" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Specialists Zip Code') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Specialists Zip Code:</label>
	<div class="col-sm-8">
		<input type="text" name="specialists_zip_code[]" value="<?= explode('*#*', $contact['specialists_zip_code'])[$counter] ?>" data-field="specialists_zip_code" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Specialists Town') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Specialists Town:</label>
	<div class="col-sm-8">
		<input type="text" name="specialists_town[]" value="<?= explode('*#*', $contact['specialists_town'])[$counter] ?>" data-field="specialists_town" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Specialists Province') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Specialists Province:</label>
	<div class="col-sm-8">
		<input type="text" name="specialists_province[]" value="<?= explode('*#*', $contact['specialists_province'])[$counter] ?>" data-field="specialists_province" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Specialists Country') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Specialists Country:</label>
	<div class="col-sm-8">
		<input type="text" name="specialists_country[]" value="<?= explode('*#*', $contact['specialists_country'])[$counter] ?>" data-field="specialists_country" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Specialists Multiple') { ?>
	<div class="pull-right">
		<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addMultiple('specialists_multiple');">
		<img src="../img/remove.png" class="inline-img pull-right" onclick="removeMultiple('specialists_multiple', this);">
	</div>
<?php } else if($field_option == 'Medications Start Time') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Medications Start Time:</label>
	<div class="col-sm-8">
		<input type="text" name="medications_start_time" value="<?= $contact['medications_start_time'] ?>" data-field="medications_start_time" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Medications End Time') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Medications End Time:</label>
	<div class="col-sm-8">
		<input type="text" name="medications_end_time" value="<?= $contact['medications_end_time'] ?>" data-field="medications_end_time" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Medications Completed By') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Medications Completed By:</label>
	<div class="col-sm-8">
		<input type="text" name="medications_completed_by" value="<?= $contact['medications_completed_by'] ?>" data-field="medications_completed_by" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Medications Signature Box') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Medications Signature Box:</label>
	<div class="col-sm-8">
		<input type="text" name="medications_signature_box" value="<?= $contact['medications_signature_box'] ?>" data-field="medications_signature_box" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Medications Management Completed By') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Medications Management Completed By:</label>
	<div class="col-sm-8">
		<input type="text" name="medications_management_completed_by" value="<?= $contact['medications_management_completed_by'] ?>" data-field="medications_management_completed_by" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Medications Management Signature Box') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Medications Management Signature Box:</label>
	<div class="col-sm-8">
		<input type="text" name="medications_management_signature_box" value="<?= $contact['medications_management_signature_box'] ?>" data-field="medications_management_signature_box" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Membership Status') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Membership Status:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="membership_status" value="Active" <?= $contact['membership_status'] == 'Active' ? 'checked' : '' ?> data-field="membership_status" data-table="contacts_medical"> Active</label>
		<label class="form-checkbox"><input type="radio" name="membership_status" value="Pending Renewal" <?= $contact['membership_status'] == 'Pending Renewal' ? 'checked' : '' ?> data-field="membership_status" data-table="contacts_medical"> Pending Renewal</label>
		<label class="form-checkbox"><input type="radio" name="membership_status" value="Inactive" <?= $contact['membership_status'] == 'Inactive' ? 'checked' : '' ?> data-field="membership_status" data-table="contacts_medical"> Inactive</label>
	</div>
<?php } else if($field_option == 'Membership Level') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Membership Level:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="membership_level" value="AAFS Family Membership" <?= $contact['membership_level'] == 'AAFS Family Membership' ? 'checked' : '' ?> data-field="membership_level" data-table="contacts_medical"> AAFS Family Membership</label>
		<label class="form-checkbox"><input type="radio" name="membership_level" value="AAFS Community Partner" <?= $contact['membership_level'] == 'AAFS Community Partner' ? 'checked' : '' ?> data-field="membership_level" data-table="contacts_medical"> AAFS Community Partner</label>
	</div>
<?php } else if($field_option == 'Membership Level Dropdown') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Membership Level:</label>
	<div class="col-sm-8">
		<select data-placeholder="Select a Membership Level" name="membership_level" class="chosen-select-deselect" data-field="membership_level" data-table="contacts_medical">
			<option></option>
			<?php $service_types = array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT(`service_type`) `service_type` FROM `services` WHERE `deleted` = 0 ORDER BY `service_type`"),MYSQLI_ASSOC),'service_type');
	        $service_types = array_filter(array_unique(array_merge($service_types, explode(',', get_config($dbc, 'service_types')))));
	        asort($service_types);
	        foreach($service_types as $service_type) { ?>
	        	<option value="<?= $service_type ?>" <?= $contact['membership_level'] == $service_type ? 'selected' : '' ?>><?= $service_type ?></option>
	        <?php } ?>
		</select>
	</div>
<?php } else if($field_option == 'Programs Female Only Programs') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Female Only Programs:</label>
	<div class="col-sm-8">
		<input type="checkbox" name="programs[]" value="Programs Female Only Programs" <?= strpos(','.$contact['programs'].',',',Programs Female Only Programs,') !== FALSE ? 'checked' : '' ?> data-field="programs" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Programs Male Only Programs') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Male Only Programs:</label>
	<div class="col-sm-8">
		<input type="checkbox" name="programs[]" value="Programs Male Only Programs" <?= strpos(','.$contact['programs'].',',',Programs Male Only Programs,') !== FALSE ? 'checked' : '' ?> data-field="programs" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Program Type') { ?>
	<?php if(in_array("Programs LAAFS Program", $field_config)) { ?>
		<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> LAAFS Program:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" name="programs[]" value="Programs LAAFS Program" <?= strpos(','.$contact['programs'].',',',Programs LAAFS Program,') !== FALSE ? 'checked' : '' ?> data-field="programs" data-table="contacts_medical"></label>
		</div>
	<?php } ?>
	<?php if(in_array("Programs AAFS Program", $field_config)) { ?>
		<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> AAFS Program:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" name="programs[]" value="Programs AAFS Program" <?= strpos(','.$contact['programs'].',',',Programs AAFS Program,') !== FALSE ? 'checked' : '' ?> data-field="programs" data-table="contacts_medical"></label>
		</div>
	<?php } ?>
	<?php if(in_array("Programs Over 16 Program", $field_config)) { ?>
		<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Fellowship 16 Program:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" name="programs[]" value="Programs Over 16 Program" <?= strpos(','.$contact['programs'].',',',Programs Over 16 Program,') !== FALSE ? 'checked' : '' ?> data-field="programs" data-table="contacts_medical"></label>
		</div>
	<?php } ?>
	<?php if(in_array("Programs Over 18 Program", $field_config)) { ?>
		<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Fellowship 18 Program:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" name="programs[]" value="Programs Over 18 Program" <?= strpos(','.$contact['programs'].',',',Programs Over 18 Program,') !== FALSE ? 'checked' : '' ?> data-field="programs" data-table="contacts_medical"></label>
		</div>
	<?php } ?>
	<?php if(in_array("Programs Developmental Aide", $field_config)) { ?>
		<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Developmental Aide:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" name="programs[]" value="Programs Developmental Aide" <?= strpos(','.$contact['programs'].',',',Programs Developmental Aide,') !== FALSE ? 'checked' : '' ?> data-field="programs" data-table="contacts_medical"></label>
		</div>
	<?php } ?>
	<?php if(in_array("Programs Specialized Services", $field_config)) { ?>
		<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Specialized Services:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" name="programs[]" value="Programs Specialized Services" <?= strpos(','.$contact['programs'].',',',Programs Specialized Services,') !== FALSE ? 'checked' : '' ?> data-field="programs" data-table="contacts_medical"></label>
		</div>
	<?php } ?>
	<?php if(in_array("Programs Private", $field_config)) { ?>
		<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Private:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" name="programs[]" value="Programs Private" <?= strpos(','.$contact['programs'].',',',Programs Private,') !== FALSE ? 'checked' : '' ?> data-field="programs" data-table="contacts_medical"></label>
		</div>
	<?php } ?>
	<?php if(in_array("Programs Other", $field_config)) { ?>
		<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Other:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" name="programs[]" value="Programs Other" <?= strpos(','.$contact['programs'].',',',Programs Other,') !== FALSE ? 'checked' : '' ?> data-field="programs" data-table="contacts_medical"></label>
		</div>
	<?php } ?>
<?php } else if($field_option == 'Funding FSCD' || $field_option == 'Profile Funding FSCD') { ?>
	<div class="clone_exception_block">
        <label class="col-sm-4 control-label" style="white-space:normal;">Do you have funding through Family Support for Children with Disabilities (FSCD)?</label>
        <div class="col-sm-8">
            <label class="form-checkbox"><input type="radio" name="funding_fscd" value="Yes" <?= $contact['funding_fscd'] == 'Yes' ? 'checked' : '' ?> data-field="funding_fscd" data-table="contacts_medical"> Yes</label>
            <label class="form-checkbox"><input type="radio" name="funding_fscd" value="No" <?= $contact['funding_fscd'] == 'No' ? 'checked' : '' ?> data-field="funding_fscd" data-table="contacts_medical"> No</label>
        </div>
    </div>
<?php } else if($field_option == 'Funding FSCD Worker Name') { ?>
	<div class="clone_exception_block">
        <label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> FSCD Worker Name:</label>
        <div class="col-sm-8">
            <input type="text" name="funding_fscd_worker_name" value="<?= $contact['funding_fscd_worker_name'] ?>" data-field="funding_fscd_worker_name" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
        </div>
    </div>
<?php } else if($field_option == 'Funding FSCD File ID') { ?>
	<div class="clone_exception_block">
        <label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> FSCD File ID #:</label>
        <div class="col-sm-8">
            <input type="text" name="funding_fscd_file_id" value="<?= $contact['funding_fscd_file_id'] ?>" data-field="funding_fscd_file_id" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
        </div>
    </div>
<?php } else if($field_option == 'Funding FSCD Renewal Date') { ?>
	<div class="clone_exception_block">
        <label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> FSCD Renewal Date:</label>
        <div class="col-sm-8">
            <input type="text" name="funding_fscd_renewal_date" value="<?= $contact['funding_fscd_renewal_date'] ?>" data-field="funding_fscd_renewal_date" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control datepicker">
        </div>
    </div>
<?php } else if($field_option == 'Funding PDD') { ?>
    <div class="clone_exception_block">
        <label class="col-sm-4 control-label" style="white-space:normal;">Do you have funding through Persons with Developmental Disabilities (PDD)?</label>
        <div class="col-sm-8">
            <label class="form-checkbox"><input type="radio" name="funding_pdd" value="Yes" <?= $contact['funding_pdd'] == 'Yes' ? 'checked' : '' ?> data-field="funding_pdd" data-table="contacts_medical"> Yes</label>
            <label class="form-checkbox"><input type="radio" name="funding_pdd" value="No" <?= $contact['funding_pdd'] == 'No' ? 'checked' : '' ?> data-field="funding_pdd" data-table="contacts_medical"> No</label>
        </div>
    </div>
<?php } else if($field_option == 'PDD Key Contact') { ?>
	<label class="col-sm-4 control-label" style="white-space:normal;">Key Contact:</label>
	<div class="col-sm-8">
		<input type="text" name="pdd_key_contact[]" value="<?= explode('*#*', $contact['pdd_key_contact'])[$counter] ?>" data-field="pdd_key_contact" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'PDD Client ID') { ?>
	<label class="col-sm-4 control-label" style="white-space:normal;">Client ID:</label>
	<div class="col-sm-8">
		<input type="text" name="pdd_client_id[]" value="<?= explode('*#*', $contact['pdd_client_id'])[$counter] ?>" data-field="pdd_client_id" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'PDD Phone') { ?>
	<label class="col-sm-4 control-label" style="white-space:normal;">Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="pdd_phone[]" value="<?= explode('*#*', $contact['pdd_phone'])[$counter] ?>" data-field="pdd_phone" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'PDD Fax') { ?>
	<label class="col-sm-4 control-label" style="white-space:normal;">Fax:</label>
	<div class="col-sm-8">
		<input type="text" name="pdd_fax[]" value="<?= explode('*#*', $contact['pdd_fax'])[$counter] ?>" data-field="pdd_fax" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'PDD Email') { ?>
	<label class="col-sm-4 control-label" style="white-space:normal;">Email:</label>
	<div class="col-sm-8">
		<input type="text" name="pdd_email[]" value="<?= explode('*#*', $contact['pdd_email'])[$counter] ?>" data-field="pdd_email" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'PDD AISH') { ?>
	<label class="col-sm-4 control-label" style="white-space:normal;">AISH #:</label>
	<div class="col-sm-8">
		<input type="text" name="pdd_aish_no[]" value="<?= explode('*#*', $contact['pdd_aish_no'])[$counter] ?>" data-field="pdd_aish_no" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Multiple PDD Contacts') { ?>
	<div class="pull-right">
		<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addMultiple('pdd_contact_multiple');">
		<img src="../img/remove.png" class="inline-img pull-right" onclick="removeMultiple('pdd_contact_multiple', this);">
	</div>
<?php } else if($field_option == 'Initials') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Initials:</label>
	<div class="col-sm-8">
		<input type="text" name="initials" value="<?= $contact['initials'] ?>" data-field="initials" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" <?= $_GET['edit'] > 0 && !empty($contact['initials']) && ($contact['category'] == 'Business' || $contact['category'] == 'Corporation') ? 'readonly' : '' ?>>
	</div>
<?php } else if($field_option == 'Calendar Color') { ?>
	<script type="text/javascript">
		function colorCodeChange(input) {
		    $('[name="calendar_color"]').val(input.value).change();
		}
		function colorCodeDisplayChange(input) {
			$('[name="calendar_color_picker"]').val(input.value);
		}
	</script>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Calendar Color:</label>
	<div class="col-sm-1">
		<input onchange="colorCodeChange(this);" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" type="color" name="calendar_color_picker" value="<?= $contact['calendar_color'] ?>">
	</div>
	<div class="col-sm-7">
		<input onchange="colorCodeDisplayChange(this);" name="calendar_color" value="<?= $contact['calendar_color'] ?>" type="text" data-field="calendar_color" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Protocols Start Time') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Protocols Start Time:</label>
	<div class="col-sm-8">
		<input type="text" name="protocols_start_time" value="<?= $contact['protocols_start_time'] ?>" data-field="protocols_start_time" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Protocols End Time') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Protocols End Time:</label>
	<div class="col-sm-8">
		<input type="text" name="protocols_end_time" value="<?= $contact['protocols_end_time'] ?>" data-field="protocols_end_time" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Protocols Completed By') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Protocols Completed By:</label>
	<div class="col-sm-8">
		<input type="text" name="protocols_completed_by" value="<?= $contact['protocols_completed_by'] ?>" data-field="protocols_completed_by" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Protocols Signature Box') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Protocols Signature Box:</label>
	<div class="col-sm-8">
		<input type="text" name="protocols_signature_box" value="<?= $contact['protocols_signature_box'] ?>" data-field="protocols_signature_box" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Protocols Management Completed By') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Protocols Management Completed By:</label>
	<div class="col-sm-8">
		<input type="text" name="protocols_management_completed_by" value="<?= $contact['protocols_management_completed_by'] ?>" data-field="protocols_management_completed_by" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Protocols Management Signature Box') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Protocols Management Signature Box:</label>
	<div class="col-sm-8">
		<input type="text" name="protocols_management_signature_box" value="<?= $contact['protocols_management_signature_box'] ?>" data-field="protocols_management_signature_box" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Routines Start Time') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Routines Start Time:</label>
	<div class="col-sm-8">
		<input type="text" name="routines_start_time" value="<?= $contact['routines_start_time'] ?>" data-field="routines_start_time" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Routines End Time') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Routines End Time:</label>
	<div class="col-sm-8">
		<input type="text" name="routines_end_time" value="<?= $contact['routines_end_time'] ?>" data-field="routines_end_time" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Routines Completed By') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Routines Completed By:</label>
	<div class="col-sm-8">
		<input type="text" name="routines_completed_by" value="<?= $contact['routines_completed_by'] ?>" data-field="routines_completed_by" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Routines Signature Box') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Routines Signature Box:</label>
	<div class="col-sm-8">
		<input type="text" name="routines_signature_box" value="<?= $contact['routines_signature_box'] ?>" data-field="routines_signature_box" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Routines Management Completed By') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Routines Management Completed By:</label>
	<div class="col-sm-8">
		<input type="text" name="routines_management_completed_by" value="<?= $contact['routines_management_completed_by'] ?>" data-field="routines_management_completed_by" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Routines Management Signature Box') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Routines Management Signature Box:</label>
	<div class="col-sm-8">
		<input type="text" name="routines_management_signature_box" value="<?= $contact['routines_management_signature_box'] ?>" data-field="routines_management_signature_box" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Communication Start Time') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Communication Start Time:</label>
	<div class="col-sm-8">
		<input type="text" name="communication_start_time" value="<?= $contact['communication_start_time'] ?>" data-field="communication_start_time" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Communication End Time') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Communication End Time:</label>
	<div class="col-sm-8">
		<input type="text" name="communication_end_time" value="<?= $contact['communication_end_time'] ?>" data-field="communication_end_time" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Communication Completed By') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Communication Completed By:</label>
	<div class="col-sm-8">
		<input type="text" name="communication_completed_by" value="<?= $contact['communication_completed_by'] ?>" data-field="communication_completed_by" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Communication Signature Box') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Communication Signature Box:</label>
	<div class="col-sm-8">
		<input type="text" name="communication_signature_box" value="<?= $contact['communication_signature_box'] ?>" data-field="communication_signature_box" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Communication Management Completed By') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Communication Management Completed By:</label>
	<div class="col-sm-8">
		<input type="text" name="communication_management_completed_by" value="<?= $contact['communication_management_completed_by'] ?>" data-field="communication_management_completed_by" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Communication Management Signature Box') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Communication Management Signature Box:</label>
	<div class="col-sm-8">
		<input type="text" name="communication_management_signature_box" value="<?= $contact['communication_management_signature_box'] ?>" data-field="communication_management_signature_box" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Activities Start Time') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Activities Start Time:</label>
	<div class="col-sm-8">
		<input type="text" name="activities_start_time" value="<?= $contact['activities_start_time'] ?>" data-field="activities_start_time" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Activities End Time') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Activities End Time:</label>
	<div class="col-sm-8">
		<input type="text" name="activities_end_time" value="<?= $contact['activities_end_time'] ?>" data-field="activities_end_time" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Activities Completed By') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Activities Completed By:</label>
	<div class="col-sm-8">
		<input type="text" name="activities_completed_by" value="<?= $contact['activities_completed_by'] ?>" data-field="activities_completed_by" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Activities Signature Box') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Activities Signature Box:</label>
	<div class="col-sm-8">
		<input type="text" name="activities_signature_box" value="<?= $contact['activities_signature_box'] ?>" data-field="activities_signature_box" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Activities Management Completed By') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Activities Management Completed By:</label>
	<div class="col-sm-8">
		<input type="text" name="activities_management_completed_by" value="<?= $contact['activities_management_completed_by'] ?>" data-field="activities_management_completed_by" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Activities Management Signature Box') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Activities Management Signature Box:</label>
	<div class="col-sm-8">
		<input type="text" name="activities_management_signature_box" value="<?= $contact['activities_management_signature_box'] ?>" data-field="activities_management_signature_box" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Day Program Name') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Name:</label>
	<div class="col-sm-8">
		<input type="text" name="day_program_name" value="<?= $contact['day_program_name'] ?>" data-field="day_program_name" data-table="contacts_description" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Day Program Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Address:</label>
	<div class="col-sm-8">
		<input type="text" name="day_program_address" value="<?= html_entity_decode($contact['day_program_address']) ?>" data-field="day_program_address" data-table="contacts_description" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Day Program Phone') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Phone:</label>
	<div class="col-sm-8">
		<input type="text" name="day_program_phone" value="<?= $contact['day_program_phone'] ?>" data-field="day_program_phone" data-table="contacts_description" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Day Program Key Worker') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Key Worker:</label>
	<div class="col-sm-8">
		<input type="text" name="day_program_key_worker" value="<?= $contact['day_program_key_worker'] ?>" data-field="day_program_key_worker" data-table="contacts_description" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Strategies Levels of Communication') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Levels of Communication:</label>
	<div class="col-sm-8">
		<textarea name="strategies_communication" rows="5" cols="50" data-field="strategies_communication" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control"><?= $contact['strategies_communication'] ?></textarea>
	</div>
<?php } else if($field_option == 'Strategies Types of Supports') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Types of Supports:</label>
	<div class="col-sm-8">
		<textarea name="strategies_supports" rows="5" cols="50" data-field="strategies_supports" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control"><?= $contact['strategies_supports'] ?></textarea>
	</div>
<?php } else if($field_option == 'Strategies Likes') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Likes:</label>
	<div class="col-sm-8">
		<textarea name="strategies_likes" rows="5" cols="50" data-field="strategies_likes" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control"><?= $contact['strategies_likes'] ?></textarea>
	</div>
<?php } else if($field_option == 'Strategies Dislikes') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Dislikes:</label>
	<div class="col-sm-8">
		<textarea name="strategies_dislikes" rows="5" cols="50" data-field="strategies_dislikes" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control"><?= $contact['strategies_dislikes'] ?></textarea>
	</div>

<?php } else if($field_option == 'Alert Staff') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Staff:</label>
	<div class="col-sm-8">
        <select id="alert_staff" name="alert_staff[]" data-placeholder="Choose Staff..." class="chosen-select-deselect <?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" multiple width="380">
            <option value=""></option><?php foreach(get_security_levels($dbc) as $security_level_label => $security_level_name) { ?>
				<option value="<?= $security_level_name ?>"><?= $security_level_label ?></option>
			<?php }
			foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `position`, `positions_allowed` FROM `contacts` WHERE (`category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0) OR `contactid`='$contactid'")) as $row) {
                if ( $contactid==$row['contactid'] ) { ?>
                    <option value="<?= $contactid ?>" selected>Include <?= $contact['category'] ?>: <?= $row['first_name'].' '.$row['last_name'] ?></option><?php
                } else { ?>
                    <option value="<?= $row['contactid']; ?>"><?= $row['first_name'].' '.$row['last_name'] ?></option><?php
                }
            } ?>
        </select>
	</div>
<?php } else if($field_option == 'Alert Sending Email Address') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Sending Email Address:</label>
	<div class="col-sm-8">
		<input type="text" id="alert_sending_email_address" name="alert_sending_email_address" value="<?= decryptIt($_SESSION[STAFF_EMAIL_FIELD]); ?>" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Alert Email Subject') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Email Subject:</label>
	<div class="col-sm-8">
		<input type="text" id="alert_email_subject" name="alert_email_subject" value="Profile Updated - <?= decryptIt($contact['first_name']) . ' ' . decryptIt($contact['last_name']); ?>" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Alert Email Body') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Email Body:</label>
	<div class="col-sm-8"><?php
		$alert_body = 'Dear '.decryptIt($contact['first_name']).',<br /><br />Your Profile was updated. <a target="_blank" href="'.WEBSITE_URL.'/'.FOLDER_NAME.'/contacts_inbox.php?category='.$_GET['category'].'&edit='.$_GET['edit'].'">Click here</a> to see your updated Profile.<br />'; ?>
        <textarea id="alert_email_body" name="alert_email_body" rows="5" cols="50" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control"><?= $alert_body ?></textarea>
	</div>
<?php } else if($field_option == 'Notification Type') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Notification Type:</label>
	<div class="col-sm-8">
		<input type="text" name="notification_type" value="<?= $contact['notification_type'] ?>" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" data-field="notification_type" data-table="contacts">
	</div>
<?php } else if($field_option == 'WCB Claim Number') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> WCB Claim Number:</label>
	<div class="col-sm-8">
		<input type="text" name="wcb_claim_number[]" value="<?= explode('*#*', $contact['wcb_claim_number'])[$counter] ?>" data-field="wcb_claim_number" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'WCB Date of Accident') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Date of Accident:</label>
	<div class="col-sm-8">
		<input type="text" name="wcb_accident_date[]" value="<?= explode('*#*', $contact['wcb_accident_date'])[$counter] ?>" data-field="wcb_accident_date" data-table="contacts_medical" data-delimiter="*#*" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control datepicker">
	</div>
<?php } else if($field_option == 'WCB Add Multiple') { ?>
	<div class="pull-right">
		<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addMultiple('wcb_multiple');">
		<img src="../img/remove.png" class="inline-img pull-right" onclick="removeMultiple('wcb_multiple', this);">
	</div>
<?php } else if($field_option == 'Budget') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Budget:</label>
	<div class="col-sm-8">
		<input type="text" name="budget" value="<?= $contact['budget'] ?>" data-field="budget" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Preferred Payment Info') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Preferred Payment Info:</label>
	<div class="col-sm-8">
		<select name="preferred_payment" data-placeholder="Select a Preferred Payment" data-field="preferred_payment" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control chosen-select-deselect">
			<option></option>
			<option <?= $contact['preferred_payment'] == 'American Express' ? 'selected' : '' ?> value="American Express">American Express</option>
			<option <?= $contact['preferred_payment'] == 'Cash' ? 'selected' : '' ?> value="Cash">Cash</option>
			<option <?= $contact['preferred_payment'] == 'Cheque' ? 'selected' : '' ?> value="Cheque">Cheque</option>
			<option <?= $contact['preferred_payment'] == 'Direct Payment' ? 'selected' : '' ?> value="Direct Payment">Direct Payment</option>
			<option <?= $contact['preferred_payment'] == 'Electronic Funds Transfer' ? 'selected' : '' ?> value="Electronic Funds Transfer">Electronic Funds Transfer</option>
			<option <?= $contact['preferred_payment'] == 'Interact Debit' ? 'selected' : '' ?> value="Interact Debit">Interact Debit</option>
			<option <?= $contact['preferred_payment'] == 'Mastercard' ? 'selected' : '' ?> value="Mastercard">Mastercard</option>
			<option <?= $contact['preferred_payment'] == 'Pay Pay' ? 'selected' : '' ?> value="Pay Pay">Pay Pay</option>
			<option <?= $contact['preferred_payment'] == 'Square' ? 'selected' : '' ?> value="Square">Square</option>
			<option <?= $contact['preferred_payment'] == 'Visa' ? 'selected' : '' ?> value="Visa">Visa</option>
		</select>
	</div>
<?php } else if($field_option == 'Preferred Booking Time') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Preferred Booking Time:</label>
	<div class="col-sm-8">
		<textarea name="preferred_booking_time" rows="5" cols="50" data-field="preferred_booking_time" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control"><?= $contact['preferred_booking_time'] ?></textarea>
	</div>
<?php } else if($field_option == 'Booking Extra') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Extra Information:</label>
	<div class="col-sm-8">
		<textarea name="booking_extra" rows="5" cols="50" data-field="booking_extra" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control"><?= $contact['booking_extra'] ?></textarea>
	</div>
<?php } else if($field_option == 'Location Square Footage') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Square Footage:</label>
	<div class="col-sm-8">
		<input type="text" name="location_square_footage" value="<?= $contact['location_square_footage'] ?>" data-field="location_square_footage" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Location Num Bathrooms') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Number of Bathrooms:</label>
	<div class="col-sm-8">
		<input type="text" name="location_num_bathrooms" value="<?= $contact['location_num_bathrooms'] ?>" data-field="location_num_bathrooms" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Location Alarm') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Alarm System Information:</label>
	<div class="col-sm-8">
		<textarea name="location_alarm" rows="5" cols="50" data-field="location_alarm" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control"><?= $contact['location_alarm'] ?></textarea>
	</div>
<?php } else if($field_option == 'Location Pets') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Pets:</label>
	<div class="col-sm-8">
		<textarea name="location_pets" rows="5" cols="50" data-field="location_pets" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control"><?= $contact['location_pets'] ?></textarea>
	</div>
<?php } else if($field_option == 'Strengths') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Strengths:</label>
	<div class="col-sm-8">
		<textarea name="strengths" rows="5" cols="50" data-field="strengths" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control"><?= $contact['strengths'] ?></textarea>
	</div>
<?php } else if($field_option == 'Interests') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Interests:</label>
	<div class="col-sm-8">
		<textarea name="interests" rows="5" cols="50" data-field="interests" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control"><?= $contact['interests'] ?></textarea>
	</div>
<?php } else if($field_option == 'Strategies Required Accommodations') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Required Accommodations:</label>
	<div class="col-sm-8">
		<textarea name="strategies_required_accommodations" rows="5" cols="50" data-field="strategies_required_accommodations" data-table="contacts_medical" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control"><?= $contact['strategies_required_accommodations'] ?></textarea>
	</div>
<?php } else if($field_option == 'Name of Drivers License') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Name of Drivers License:</label>
	<div class="col-sm-8">
		<input type="text" name="name_of_drivers_license" data-field="name_of_drivers_license" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" value="<?= $contact['name_of_drivers_license'] ?>" />
	</div>
<?php } else if($field_option == 'Drivers License Number') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Drivers License #:</label>
	<div class="col-sm-8">
		<input type="text" name="drivers_license_number" data-field="drivers_license_number" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" value="<?= $contact['drivers_license_number'] ?>" />
	</div>
<?php } else if($field_option == 'Drivers License') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Drivers License:</label>
	<div class="col-sm-8">
		<input type="text" name="drivers_license" data-field="drivers_license" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" value="<?= $contact['drivers_license'] ?>" />
	</div>
<?php } else if($field_option == 'COR Certified') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> COR Certified?</label>
	<div class="col-sm-8">
		<label class="control-checkbox"><input type="radio" name="cor_certified" data-field="cor_certified" data-table="contacts" value="1" <?= $contact['cor_certified'] > 0 ? 'checked' : '' ?> /> Yes</label>
		<label class="control-checkbox"><input type="radio" name="cor_certified" data-field="cor_certified" data-table="contacts" value="0" <?= $contact['cor_certified'] > 0 ? '' : 'checked' ?> /> No</label>
	</div>
<?php } else if($field_option == 'COR Number') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> COR Number:</label>
	<div class="col-sm-8">
		<input type="text" name="cor_number" data-field="cor_number" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" value="<?= $contact['cor_number'] ?>" />
	</div>
<?php } else if($field_option == 'Reminders') { ?>
	<div class="col-sm-12"><?php
		$reminders_list = mysqli_query($dbc, "SELECT `reminderid`, `reminder_date`, `subject` FROM `reminders` WHERE `src_tableid`='{$contact[0]}' AND `reminder_type`='$security_folder' AND `deleted`=0 ORDER BY `reminder_date`");
        if ($reminders_list->num_rows > 0) { ?>
            <div class="col-sm-4">Reminders:</div>
            <div class="col-sm-8">
                <ul><?php
                    while($row=mysqli_fetch_assoc($reminders_list)) {
                        echo '<li>'. $row['reminder_date'] .': '. $row['subject'] .'</li>';
                    } ?>
                </ul>
            </div><?php
        } ?>
        <a href="" onclick="contactReminderDialog('<?= $security_folder ?>'); return false;" class="btn brand-btn pull-right">Add Reminder</a>
	</div>
<?php } else if($field_option == 'Contract Workers Reminders') { ?>
	<div class="col-sm-12"><?php
		$reminders_list = mysqli_query($dbc, "SELECT `reminderid`, `reminder_date`, `subject` FROM `reminders` WHERE `src_tableid`='{$contact[0]}' AND `reminder_type`='$security_folder Contract Workers' AND `deleted`=0 ORDER BY `reminder_date`");
        if ($reminders_list->num_rows > 0) { ?>
            <div class="col-sm-4">Contract Workers Reminders:</div>
            <div class="col-sm-8">
                <ul><?php
                    while($row=mysqli_fetch_assoc($reminders_list)) {
                        echo '<li>'. $row['reminder_date'] .': '. $row['subject'] .'</li>';
                    } ?>
                </ul>
            </div><?php
        } ?>
        <a href="" onclick="contactReminderDialog('<?= $security_folder ?> Contract Workers','Reminder to Update the List of Workers',true); return false;" class="btn brand-btn pull-right">Add Reminder</a>
	</div>
<?php } else if($field_option == 'Contract Policies Reminders') { ?>
	<div class="col-sm-12"><?php
		$reminders_list = mysqli_query($dbc, "SELECT `reminderid`, `reminder_date`, `subject` FROM `reminders` WHERE `src_tableid`='{$contact[0]}' AND `reminder_type`='$security_folder Contract Policies' AND `deleted`=0 ORDER BY `reminder_date`");
        if ($reminders_list->num_rows > 0) { ?>
            <div class="col-sm-4">Contract Policies Reminders:</div>
            <div class="col-sm-8">
                <ul><?php
                    while($row=mysqli_fetch_assoc($reminders_list)) {
                        echo '<li>'. $row['reminder_date'] .': '. $row['subject'] .'</li>';
                    } ?>
                </ul>
            </div><?php
        } ?>
        <a href="" onclick="contactReminderDialog('<?= $security_folder ?> Contract Policies','Reminder to Update the Contract Policies',true); return false;" class="btn brand-btn pull-right">Add Reminder</a>
	</div>
<?php } else if($field_option == 'Contract WCB Reminders') { ?>
	<div class="col-sm-12"><?php
		$reminders_list = mysqli_query($dbc, "SELECT `reminderid`, `reminder_date`, `subject` FROM `reminders` WHERE `src_tableid`='{$contact[0]}' AND `reminder_type`='$security_folder Contract WCB' AND `deleted`=0 ORDER BY `reminder_date`");
        if ($reminders_list->num_rows > 0) { ?>
            <div class="col-sm-4">Contractor WCB Reminders:</div>
            <div class="col-sm-8">
                <ul><?php
                    while($row=mysqli_fetch_assoc($reminders_list)) {
                        echo '<li>'. $row['reminder_date'] .': '. $row['subject'] .'</li>';
                    } ?>
                </ul>
            </div><?php
        } ?>
        <a href="" onclick="contactReminderDialog('<?= $security_folder ?> Contract WCB','Reminder to Update the Contractor WCB Status',true); return false;" class="btn brand-btn pull-right">Add Reminder</a>
	</div>
<?php } else if($field_option == 'Contract Rates Reminders') { ?>
	<div class="col-sm-12"><?php
		$reminders_list = mysqli_query($dbc, "SELECT `reminderid`, `reminder_date`, `subject` FROM `reminders` WHERE `src_tableid`='{$contact[0]}' AND `reminder_type`='$security_folder Contract Rates' AND `deleted`=0 ORDER BY `reminder_date`");
        if ($reminders_list->num_rows > 0) { ?>
            <div class="col-sm-4">Contractor Rate Sheet Reminders:</div>
            <div class="col-sm-8">
                <ul><?php
                    while($row=mysqli_fetch_assoc($reminders_list)) {
                        echo '<li>'. $row['reminder_date'] .': '. $row['subject'] .'</li>';
                    } ?>
                </ul>
            </div><?php
        } ?>
        <a href="" onclick="contactReminderDialog('<?= $security_folder ?> Contract Rates','Reminder to Update the Contractor Rate Sheet',true); return false;" class="btn brand-btn pull-right">Add Reminder</a>
	</div>
<?php } else if($field_option == 'Contract Vehicles Reminders') { ?>
	<div class="col-sm-12"><?php
		$reminders_list = mysqli_query($dbc, "SELECT `reminderid`, `reminder_date`, `subject` FROM `reminders` WHERE `src_tableid`='{$contact[0]}' AND `reminder_type`='$security_folder Contract Vehicles' AND `deleted`=0 ORDER BY `reminder_date`");
        if ($reminders_list->num_rows > 0) { ?>
            <div class="col-sm-4">Contractor Vehicle Reminders:</div>
            <div class="col-sm-8">
                <ul><?php
                    while($row=mysqli_fetch_assoc($reminders_list)) {
                        echo '<li>'. $row['reminder_date'] .': '. $row['subject'] .'</li>';
                    } ?>
                </ul>
            </div><?php
        } ?>
        <a href="" onclick="contactReminderDialog('<?= $security_folder ?> Contract Vehicles','Reminder to Update the Contractor Vehicle Information',true); return false;" class="btn brand-btn pull-right">Add Reminder</a>
	</div>
<?php } else if($field_option == 'Global Discount Type') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Discount Type:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="discount_type" data-field="discount_type" data-table="contacts" value="" <?= empty($contact['discount_type']) ? 'checked' : '' ?>> $</label>
		<label class="form-checkbox"><input type="radio" name="discount_type" data-field="discount_type" data-table="contacts" value="%" <?= $contact['discount_type'] == '%' ? 'checked' : '' ?>> %</label>
	</div>
<?php } else if($field_option == 'Global Discount Value') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Discount Value:</label>
	<div class="col-sm-8">
		<input type="number" name="discount_value" data-field="discount_value" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control" value="<?= $contact['discount_value'] ?>" step="0.01" min="0.00" />
	</div>
<?php } else if($field_option == 'Key Number') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Key Number:</label>
	<div class="col-sm-8">
		<input type="text" name="key_number" value="<?= $contact['key_number'] ?>" data-field="key_number" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Door Code Number') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Door Code Number:</label>
	<div class="col-sm-8">
		<input type="text" name="door_code_number" value="<?= $contact['door_code_number'] ?>" data-field="door_code_number" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Alarm Code Number') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Alarm Code Number:</label>
	<div class="col-sm-8">
		<input type="text" name="alarm_code_number" value="<?= $contact['alarm_code_number'] ?>" data-field="alarm_code_number" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Contract WCB Good Standing') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> WCB in Good Standing?</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="radio" name="wcb_status" value="1" <?= $contact['wcb_status'] == 1 ? 'checked' : '' ?> data-field="wcb_status" data-table="contacts"> Yes</label>
		<label class="form-checkbox"><input type="radio" name="wcb_status" value="0" <?= $contact['wcb_status'] === 0 ? 'checked' : '' ?> data-field="wcb_status" data-table="contacts"> No</label>
	</div>
<?php } else if($field_option == 'Contract Vehicles Make') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Make / Model:</label>
	<div class="col-sm-8">
		<input type="text" name="vehicle_make" value="<?= $contact['vehicle_make'] ?>" data-field="vehicle_make" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Contract Vehicles Licence Plate') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Licence Plate:</label>
	<div class="col-sm-8">
		<input type="text" name="vehicle_licence_plate" value="<?= $contact['vehicle_licence_plate'] ?>" data-field="vehicle_licence_plate" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Contract Vehicles Registration') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Registration:</label>
	<div class="col-sm-8">
		<input type="text" name="vehicle_registration" value="<?= $contact['vehicle_registration'] ?>" data-field="vehicle_registration" data-table="contacts" class="<?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control">
	</div>
<?php } else if($field_option == 'Property Type') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Property Type:</label>
	<div class="col-sm-8">
		<select name="property_type" data-field="property_type" data-table="contacts" class="chosen-select-deselect <?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control"><option></option>
			<?php $property_types = get_config($dbc, FOLDER_NAME.'_'.$_GET['category'].'_property_types');
			foreach(explode(',',$property_types) as $property_type) {
				echo '<option value="'.$property_type.'" '.($property_type == $contact['property_type'] ? 'selected' : '' ).'>'.$property_type.'</option>';
			} ?>
		</select>
	</div>
<?php } else if($field_option == 'Property Size') { ?>
	<label class="col-sm-4 control-label"><?php echo (in_array($field_option, $field_config_mandate) ? '<font color="red">*</font>' : ''); ?> Property Type:</label>
	<div class="col-sm-8">
		<select name="property_type" data-field="property_type" data-table="contacts" class="chosen-select-deselect <?php echo (in_array($field_option, $field_config_mandate) ? 'required' : ''); ?> form-control"><option></option>
			<?php $property_types = get_config($dbc, FOLDER_NAME.'_'.$_GET['category'].'_property_types');
			foreach(explode(',',$property_types) as $property_type) {
				echo '<option value="'.$property_type.'" '.($property_type == $contact['property_type'] ? 'selected' : '' ).'>'.$property_type.'</option>';
			} ?>
		</select>
	</div>
<?php } else if($field_option == 'Hours of Operation') {
	$days_of_week = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
	$hours_of_operation = explode(',', $contact['hours_of_operation']); ?>
	<script type="text/javascript">
	function setHoursOfOperation(input) {
		var table = $(input).closest('.hours_of_operation');
		var hours_of_operation = [];
		$(table).find('.day_row').each(function() {
			if($(this).find('[name="day_hours_closed"]').is(':checked')) {
				hours_of_operation.push('closed');
				$(this).find('[name="day_hours_start"],[name="day_hours_end"]').val('').prop('disabled', true);
			} else {
				hours_of_operation.push($(this).find('[name="day_hours_start"]').val()+'-'+$(this).find('[name="day_hours_end"]').val());
				$(this).find('[name="day_hours_start"],[name="day_hours_end"]').prop('disabled', false);
			}
		});
		hours_of_operation = hours_of_operation.join(',');
		$('[name="hours_of_operation"]').val(hours_of_operation).change();
	}
	</script>
	<label class="col-sm-4 control-label">Hours of Operation:</label>
	<div class="col-sm-8">
		<div id="no-more-tables">
			<table class="table table-bordered hours_of_operation">
				<input type="hidden" name="hours_of_operation" data-field="hours_of_operation" data-table="contacts" value="<?= $contact['hours_of_operation'] ?>">
				<tr class="hidden-xs">
					<th>Day</th>
					<th>Closed</th>
					<th>Start Time</th>
					<th>End Time</th>
				</tr>
				<?php for($day_i = 0; $day_i < 7; $day_i++) { ?>
					<tr class="day_row">
						<input type="hidden" name="day_hours" value="<?= $hours_of_operation[$day_i] ?>">
						<td data-title="Day"><?= $days_of_week[$day_i] ?></td>
						<td data-title="Closed"><label class="form-checkbox"><input type="checkbox" name="day_hours_closed" value="closed" <?= $hours_of_operation[$day_i] == 'closed' ? 'checked' : '' ?> onchange="setHoursOfOperation(this);"></label></td>
						<td data-title="Start Time"><input type="text" name="day_hours_start" class="form-control datetimepicker" value="<?= $hours_of_operation[$day_i] == 'closed' ? '' : explode('-',$hours_of_operation[$day_i])[0] ?>" onchange="setHoursOfOperation(this);" <?= $hours_of_operation[$day_i] == 'closed' ? 'disabled' : '' ?>></td>
						<td data-title="End Time"><input type="text" name="day_hours_end" class="form-control datetimepicker" value="<?= $hours_of_operation[$day_i] == 'closed' ? '' : explode('-',$hours_of_operation[$day_i])[1] ?>" onchange="setHoursOfOperation(this);" <?= $hours_of_operation[$day_i] == 'closed' ? 'disabled' : '' ?>></td>
					</tr>
				<?php } ?>
			</table>
		</div>
	</div>
<?php } ?>
