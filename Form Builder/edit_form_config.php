<?php //Form Builder Configuration
$assigned_tiles = ','.(!empty($form['assigned_tile']) ? $form['assigned_tile'] : '').',';
$attached_contact_categories = ','.$form['attached_contact_categories'].',';
$attached_contacts = ','.$form['attached_contacts'].',';
$attached_contact_default_field = $form['attached_contact_default_field'];
$subtab = !empty($form['subtab']) ? $form['subtab'] : '';
$subtab_list = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_user_forms`"))['subtabs'];
$subtab_list = explode(',', $subtab_list);
$form_layout = !empty($form['form_layout']) ? $form['form_layout'] : 'Accordions';
?>
<script type="text/javascript">
$(document).ready(function() {
	$('.form-content input,select,textarea').on('change', function() { saveConfig(this); });
	filterAttachedContacts();
});
$(document).on('change', 'select[name="assigned_tile[]"]', function() { changeAssignedTile(); });
$(document).on('change', 'select[name="attached_contact_categories[]"]', function() { filterAttachedContacts(); });
function saveConfig(field) {
	var formid = $('[name="formid"]').val();
	var assigned_tiles = [];
	$('[name="assigned_tile[]"]').find('option:selected').each(function() {
		assigned_tiles.push($(this).val());
	});
	assigned_tiles = JSON.stringify(assigned_tiles);
	var attached_contacts = [];
	$('[name="attached_contacts[]"]').find('option:selected').each(function() {
		attached_contacts.push($(this).val());
	});
	attached_contacts = JSON.stringify(attached_contacts);
	var attached_contact_categories = [];
	$('[name="attached_contact_categories[]"]').find('option:selected').each(function() {
		attached_contact_categories.push($(this).val());
	});
	attached_contact_categories = JSON.stringify(attached_contact_categories);
	var attached_contact_default_field = $('[name="attached_contact_default_field"]').val();
	var intake_field = $('[name="intake_field"]').val();
	var subtab = $('[name="subtab"]').val();
	var form_layout = $('[name="form_layout"]:checked').val();

	var field_data = { formid: formid, assigned_tiles: assigned_tiles, attached_contacts: attached_contacts, attached_contact_categories: attached_contact_categories, intake_field: intake_field, subtab: subtab, form_layout: form_layout, attached_contact_default_field: attached_contact_default_field };
	$.ajax({
		url: '../Form Builder/form_ajax.php?fill=update_config',
		type: 'POST',
		data: field_data,
		success: function(response) {
			console.log(response);
		}
	});
}
function changeAssignedTile() {
	$('.intake_field').hide();
	$('.attached_contacts').hide();
	$('[name="assigned_tile[]"]').find('option:selected').each(function() {
		if(this.value == 'intake') {
			$('.intake_field').show();
		}
		if(this.value == 'attach_contact') {
			$('.attached_contacts').show();
		}
	});
}
function filterAttachedContacts() {
	if($('[name="attached_contact_categories[]"] option:selected').length == 0) {
		$('[name="attached_contacts[]"] option').show();
	} else {
		$('[name="attached_contacts[]"] option').hide();
		$('[name="attached_contact_categories[]"] option:selected').each(function() {
			var contact_cat = this.value;
			$('[name="attached_contacts[]"] option[data-category="'+contact_cat+'"]').show();
		});
	}
	$('[name="attached_contacts[]"] option[value="ALL_CONTACTS"]').show();
	$('[name="attached_contacts[]"]').trigger('change.select2');
}
</script>
<div class="standard-collapsible tile-sidebar" style="height: 100%;">
	<ul class="sidebar">
		<a href="" onclick="return false;"><li class="active">Form Builder Configuration</li></a>
	</ul>
</div>
<div class="scale-to-fill has-main-screen">
	<div class="main-screen form-content">
		<input type="hidden" name="formid" value="<?= $formid ?>">
		<div class="form-horizontal col-sm-12">
			<h3>Form Builder Configuration</h3>
			<div class="form-group">
				<label class="col-sm-4 control-label">Assigned Tiles:</label>
				<div class="col-sm-8">
					<select name="assigned_tile[]" multiple data-placeholder="Select Tiles..." class="form-control chosen-select-deselect">
						<option <?= strpos($assigned_tiles, ',attach_contact,') !== FALSE ? 'selected' : '' ?> value='attach_contact'>Attach to Contact</option>
						<option <?= strpos($assigned_tiles, ',contracts,') !== FALSE ? 'selected' : '' ?> value='contracts'>Contracts</option>
						<option <?= strpos($assigned_tiles, ',hr,') !== FALSE ? 'selected' : '' ?> value='hr'>HR</option>
						<option <?= strpos($assigned_tiles, ',incident_report,') !== FALSE ? 'selected' : '' ?> value='incident_report'><?= INC_REP_TILE ?></option>
						<option <?= strpos($assigned_tiles, ',infogathering,') !== FALSE ? 'selected' : '' ?> value='infogathering'>Information Gathering</option>
						<option <?= strpos($assigned_tiles, ',intake,') !== FALSE ? 'selected' : '' ?> value='intake'>Intake Forms</option>
						<option <?= strpos($assigned_tiles, ',project,') !== FALSE ? 'selected' : '' ?> value='project'>Projects</option>
						<option <?= strpos($assigned_tiles, ',performance_review,') !== FALSE ? 'selected' : '' ?> value='performance_review'>Performance Reviews</option>
						<option <?= strpos($assigned_tiles, ',safety,') !== FALSE ? 'selected' : '' ?> value='safety'>Safety</option>
						<option <?= strpos($assigned_tiles, ',treatment,') !== FALSE ? 'selected' : '' ?> value='treatment'>Treatment Charts</option>
					</select>
				</div>
			</div>
			<div class="form-group intake_field" <?= strpos($assigned_tiles, ',intake,') !== FALSE ? '' : 'style="display: none;"' ?>>
				<label class="col-sm-4 control-label">Intake Form Contact Field:</label>
				<div class="col-sm-8">
					<select name="intake_field" data-placeholder="Select a Field..." class="form-control chosen-select-deselect">
						<option></option>
						<?php $field_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$formid' AND `type` = 'CONTACTINFO' AND `deleted` = 0"),MYSQLI_ASSOC);
						foreach ($field_list as $contact_field) {
							echo '<option value="'.$contact_field['field_id'].'" '.($contact_field['field_id'] == $form['intake_field'] ? 'selected' : '').'>'.$contact_field['label'].'</option>';

						} ?>
					</select>
				</div>
			</div>
			<div class="form-group attached_contacts" <?= strpos($assigned_tiles, ',attach_contact,') !== FALSE ? '' : 'style="display:none;"' ?>>
				<label class="col-sm-4 control-label">Attached Contact Categories:</label>
				<div class="col-sm-8">
					<select name="attached_contact_categories[]" multiple data-placeholder="Select a Category" class="form-control chosen-select-deselect">
						<?php $contact_cats = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT `category` FROM `contacts` WHERE `deleted` = 0 AND `status` = 1 AND IFNULL(`category`,'') != '' ORDER BY `category`"),MYSQLI_ASSOC);
						foreach($contact_cats as $contact_cat) {
							echo '<option value="'.$contact_cat['category'].'" '.(strpos($attached_contact_categories, ','.$contact_cat['category'].',') !== FALSE ? 'selected' : '').'>'.$contact_cat['category'].'</option>';
						} ?>
					</select>
				</div>
			</div>
			<div class="form-group attached_contacts" <?= strpos($assigned_tiles, ',attach_contact,') !== FALSE ? '' : 'style="display:none;"' ?>>
				<label class="col-sm-4 control-label">Attached Contacts:</label>
				<div class="col-sm-8">
					<select name="attached_contacts[]" multiple data-placeholder="Select a Contact" class="form-control chosen-select-deselect">
						<option value="ALL_CONTACTS" <?= (strpos($attached_contacts, ',ALL_CONTACTS,') !== FALSE ? 'selected' : '') ?>>ALL CONTACTS</option>
						<?php $contacts_list = sort_contacts_query(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `deleted` = 0 AND `status` > 0"));
						foreach($contacts_list as $attached_contact) {
							if(!empty($attached_contact['full_name']) && $attached_contact['full_name'] != '-') {
								echo '<option data-category="'.$attached_contact['category'].'" value="'.$attached_contact['contactid'].'" '.(strpos($attached_contacts, ','.$attached_contact['contactid'].',') !== FALSE ? 'selected' : '').'>'.$attached_contact['full_name'].'</option>';
							}
						} ?>
					</select>
				</div>
			</div>
			<div class="form-group attached_contacts" <?= strpos($assigned_tiles, ',attach_contact,') !== FALSE ? '' : 'style="display:none;"' ?>>
				<label class="col-sm-4 control-label">Attached Contact Default Dropdown:</label>
				<div class="col-sm-8">
					<select name="attached_contact_default_field" data-placeholder="Select a Field" class="form-control chosen-select-deselect">
						<option></option>
						<?php $ref_sources = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$formid' AND `deleted` = 0 AND `type` = 'SELECT'"),MYSQLI_ASSOC);
						foreach($ref_sources as $ref_source) { ?>
							<option <?= $attached_contact_default_field == $ref_source['field_id'] ? 'selected' : '' ?> value="<?= $ref_source['field_id'] ?>"><?= $ref_source['name'].(!empty($ref_source['label']) ? ' ('.$ref_source['label'].')' : '') ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Subtab:</label>
				<div class="col-sm-8">
					<select name="subtab" data-placeholder="Select a Subtab..." class="form-control chosen-select-deselect">
						<option></option>
						<?php foreach ($subtab_list as $subtab_option) {
							echo '<option value="'.$subtab_option.'" '.($subtab == $subtab_option ? 'selected' : '').'>'.$subtab_option.'</option>';
						} ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Form Layout:</label>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="radio" name="form_layout" value="Accordions" <?= $form_layout == 'Accordions' ? 'checked' : '' ?>> Accordions</label>
					<label class="form-checkbox"><input type="radio" name="form_layout" value="Sidebar" <?= $form_layout == 'Sidebar' ? 'checked' : '' ?>>Sidebar Navigation</label>
				</div>
			</div>
		</div>
	</div>
</div>