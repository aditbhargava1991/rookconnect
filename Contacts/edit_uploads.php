<?php if($field_option == 'Contact Image') { ?>
	<label class="col-sm-4 control-label">Profile Image:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['contactimage'] != '') {
			echo '<span><a href="download/'.$contact['contactimage'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="contactimage">Delete</a></span>';
		} ?>
		<input name="contactimage" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="contactimage" />
	</div>
<?php } else if($field_option == 'Profile Documents') { ?>
	<label class="col-sm-4 control-label">Profile Documents:</label>
	<div class="col-sm-8">
		<?php $documents = mysqli_query($dbc, "SELECT `label`, `upload`, `link`, 'contact_document' table_name, `uploadid` FROM `contact_document` WHERE `contactid`='{$_GET['edit']}' AND `deleted`=0 AND IFNULL(`category`,'') IN ('profile_document','') UNION
			SELECT `label`, `upload`, `link`, 'contacts_patient_document' table_name, `uploadid` FROM `contacts_patient_document` WHERE `contactid`='{$_GET['edit']}' AND `deleted`=0");
		while($document = mysqli_fetch_array($documents)) {
			$target = ($document['upload'] == '' ? $document['link'] : 'download/'.$document['upload']); ?>
			<li><a href="<?= $target ?>" target="_blank"><?= $document['label'] == '' ? $target : $document['label'] ?></a>
				- <a href="" onclick="$(this).closest('li').find('input').show().focus(); return false;">Rename</a>
				- <a href="" onclick="saveField($(this).change()); $(this).closest('li').remove(); return false;" data-table="<?= $document['table_name'] ?>" data-field="deleted" data-row-field="uploadid" data-row-id="<?= $document['uploadid'] ?>" data-value="1">Delete</a>
				<input name="a_label" type="text" class="pull-right form-control" placeholder="Enter text here to change the label." data-table="<?= $document['table_name'] ?>" data-field="label" data-row-field="uploadid" data-row-id="<?= $document['uploadid'] ?>" style="display: none;">
				<div class="clearfix"></div></li>
		<?php } ?>
		<input name="profile_document" type="file" data-filename-placement="inside" class="form-control" data-table="contact_document" data-field="upload" data-field-category="profile_document" data-row-field="uploadid" data-row-id="new" />
	</div>
<?php } else if($field_option == 'Upload Docs') { ?>
	<label class="col-sm-4 control-label">Upload Documents:</label>
	<div class="col-sm-8">
		<?php $documents = mysqli_query($dbc, "SELECT `label`, `upload`, `link`, 'uploaded_files' table_name, `uploadid` FROM `contact_document` WHERE `contactid`='{$_GET['edit']}' AND `deleted`=0 AND `category`='uploaded_files'");
		while($document = mysqli_fetch_array($documents)) {
			$target = ($document['upload'] == '' ? $document['link'] : 'download/'.$document['upload']); ?>
			<li><a href="<?= $target ?>" target="_blank"><?= $document['label'] == '' ? $target : $document['label'] ?></a>
				- <a href="" onclick="$(this).closest('li').find('input').show().focus(); return false;">Rename</a>
				- <a href="" onclick="saveField($(this).change()); $(this).closest('li').remove(); return false;" data-table="<?= $document['table_name'] ?>" data-field="deleted" data-row-field="uploadid" data-row-id="<?= $document['uploadid'] ?>" data-value="1">Delete</a>
				<input name="a_label" type="text" class="pull-right form-control" placeholder="Enter text here to change the label." data-table="<?= $document['table_name'] ?>" data-field="label" data-row-field="uploadid" data-row-id="<?= $document['uploadid'] ?>" style="display: none;">
				<div class="clearfix"></div></li>
		<?php } ?>
		<input name="uploaded_files" type="file" data-filename-placement="inside" class="form-control" data-table="contact_document" data-field="upload" data-field-category="uploaded_files" data-row-field="uploadid" data-row-id="new" />
	</div>
<?php } else if($field_option == 'Application') { ?>
	<label class="col-sm-4 control-label">Application:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['application'] != '') {
			echo '<span><a href="download/'.$contact['application'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="application">Delete</a></span>';
		} ?>
		<input name="application" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="application" />
	</div>
<?php } else if($field_option == 'Upload License Plate') { ?>
	<label class="col-sm-4 control-label">Upload Licence Plate:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['upload_license_plate'] != '') {
			echo '<span><a href="download/'.$contact['upload_license_plate'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="upload_license_plate">Delete</a></span>';
		} ?>
		<input name="upload_license_plate" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="upload_license_plate" />
	</div>
<?php } else if($field_option == 'Upload Property Information') { ?>
	<label class="col-sm-4 control-label">Upload Property Information:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['upload_property_information'] != '') {
			echo '<span><a href="download/'.$contact['upload_property_information'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="upload_property_information">Delete</a></span>';
		} ?>
		<input name="upload_property_information" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="upload_property_information" />
	</div>
<?php } else if($field_option == 'Upload Inspection') { ?>
	<label class="col-sm-4 control-label">Upload Inspection:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['upload_inspection'] != '') {
			echo '<span><a href="download/'.$contact['upload_inspection'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="upload_inspection">Delete</a></span>';
		} ?>
		<input name="upload_inspection" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="upload_inspection" />
	</div>
<?php } else if($field_option == 'Upload Letter of Intent') { ?>
	<label class="col-sm-4 control-label">Upload Letter of Intent:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['upload_letter_of_intent'] != '') {
			echo '<span><a href="download/'.$contact['upload_letter_of_intent'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="upload_letter_of_intent">Delete</a></span>';
		} ?>
		<input name="upload_letter_of_intent" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="upload_letter_of_intent" />
	</div>
<?php } else if($field_option == 'Upload Vendor Documents') { ?>
	<label class="col-sm-4 control-label">Upload Vendor Documents:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['upload_vendor_documents'] != '') {
			echo '<span><a href="download/'.$contact['upload_vendor_documents'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="upload_vendor_documents">Delete</a></span>';
		} ?>
		<input name="upload_vendor_documents" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="upload_vendor_documents" />
	</div>
<?php } else if($field_option == 'Upload Marketing Material') { ?>
	<label class="col-sm-4 control-label">Upload Marketing Material:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['upload_marketing_material'] != '') {
			echo '<span><a href="download/'.$contact['upload_marketing_material'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="upload_marketing_material">Delete</a></span>';
		} ?>
		<input name="upload_marketing_material" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="upload_marketing_material" />
	</div>
<?php } else if($field_option == 'Upload Purchase Contract') { ?>
	<label class="col-sm-4 control-label">Upload Purchase Contract:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['upload_purchase_contract'] != '') {
			echo '<span><a href="download/'.$contact['upload_purchase_contract'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="upload_purchase_contract">Delete</a></span>';
		} ?>
		<input name="upload_purchase_contract" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="upload_purchase_contract" />
	</div>
<?php } else if($field_option == 'Upload Support Contract') { ?>
	<label class="col-sm-4 control-label">Upload Support Contract:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['upload_support_contract'] != '') {
			echo '<span><a href="download/'.$contact['upload_support_contract'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="upload_support_contract">Delete</a></span>';
		} ?>
		<input name="upload_support_contract" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="upload_support_contract" />
	</div>
<?php } else if($field_option == 'Upload Support Terms') { ?>
	<label class="col-sm-4 control-label">Upload Support Terms:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['upload_support_terms'] != '') {
			echo '<span><a href="download/'.$contact['upload_support_terms'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="upload_support_terms">Delete</a></span>';
		} ?>
		<input name="upload_support_terms" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="upload_support_terms" />
	</div>
<?php } else if($field_option == 'Upload Rental Contract') { ?>
	<label class="col-sm-4 control-label">Upload Rental Contract:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['upload_rental_contract'] != '') {
			echo '<span><a href="download/'.$contact['upload_rental_contract'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="upload_rental_contract">Delete</a></span>';
		} ?>
		<input name="upload_rental_contract" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="upload_rental_contract" />
	</div>
<?php } else if($field_option == 'Upload Management Contract') { ?>
	<label class="col-sm-4 control-label">Upload Management Contract:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['upload_management_contract'] != '') {
			echo '<span><a href="download/'.$contact['upload_management_contract'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="upload_management_contract">Delete</a></span>';
		} ?>
		<input name="upload_management_contract" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="upload_management_contract" />
	</div>
<?php } else if($field_option == 'Upload Articles of Incorporation') { ?>
	<label class="col-sm-4 control-label">Upload Articles of Incorporation:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['upload_articles_of_incorporation'] != '') {
			echo '<span><a href="download/'.$contact['upload_articles_of_incorporation'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="upload_articles_of_incorporation">Delete</a></span>';
		} ?>
		<input name="upload_articles_of_incorporation" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="upload_articles_of_incorporation" />
	</div>
<?php } else if($field_option == 'Upload Commercial Insurance') { ?>
	<label class="col-sm-4 control-label">Upload Commercial Insurance:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['upload_commercial_insurance'] != '') {
			echo '<span><a href="download/'.$contact['upload_commercial_insurance'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="upload_commercial_insurance">Delete</a></span>';
		} ?>
		<input name="upload_commercial_insurance" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="upload_commercial_insurance" />
	</div>
<?php } else if($field_option == 'Upload Residential Insurance') { ?>
	<label class="col-sm-4 control-label">Upload Residential Insurance:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['upload_residential_insurance'] != '') {
			echo '<span><a href="download/'.$contact['upload_residential_insurance'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="upload_residential_insurance">Delete</a></span>';
		} ?>
		<input name="upload_residential_insurance" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="upload_residential_insurance" />
	</div>
<?php } else if($field_option == 'Upload WCB') { ?>
	<label class="col-sm-4 control-label">Upload WCB:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['upload_wcb'] != '') {
			echo '<span><a href="download/'.$contact['upload_wcb'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="upload_wcb">Delete</a></span>';
		} ?>
		<input name="upload_wcb" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="upload_wcb" />
	</div>
<?php } else if($field_option == 'Client Support Documents' || $field_option == 'Profile Client Support Documents') { ?>
	<label class="col-sm-4 control-label">Support Documents:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['client_support_documents'] != '') {
			echo '<span><a href="download/'.$contact['client_support_documents'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="client_support_documents">Delete</a></span>';
		} ?>
		<input name="client_support_documents" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="client_support_documents" />
	</div>
<?php } else if($field_option == 'Member Support Documents') { ?>
	<label class="col-sm-4 control-label">Member Support Documents:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['member_support_documents'] != '') {
			echo '<span><a href="download/'.$contact['member_support_documents'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="member_support_documents">Delete</a></span>';
		} ?>
		<input name="member_support_documents" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="member_support_documents" />
	</div>
<?php } else if($field_option == 'Transportation Support Documents') { ?>
	<label class="col-sm-4 control-label">Transportation Support Documents:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['transportation_support_documents'] != '') {
			echo '<span><a href="download/'.$contact['transportation_support_documents'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="transportation_support_documents">Delete</a></span>';
		} ?>
		<input name="transportation_support_documents" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="transportation_support_documents" />
	</div>
<?php } else if($field_option == 'Transportation Upload License') { ?>
	<label class="col-sm-4 control-label">Upload Driver's License:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['upload_drivers_license'] != '') {
			echo '<span><a href="download/'.$contact['upload_drivers_license'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="upload_drivers_license">Delete</a></span>';
		} ?>
		<input name="upload_drivers_license" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="upload_drivers_license" />
	</div>
<?php } else if($field_option == 'Void Cheque') { ?>
	<label class="col-sm-4 control-label">Upload Void Cheque:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['upload_blank_cheque'] != '') {
			echo '<span><a href="download/'.$contact['upload_blank_cheque'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="upload_blank_cheque">Delete</a></span>';
		} ?>
		<input name="upload_blank_cheque" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="upload_blank_cheque" />
	</div>
<?php } else if($field_option == 'Insurance Support Documents') { ?>
	<label class="col-sm-4 control-label">Insurance Support Documents:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['insurance_support_documents'] != '') {
			echo '<span><a href="download/'.$contact['insurance_support_documents'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="insurance_support_documents">Delete</a></span>';
		} ?>
		<input name="insurance_support_documents" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="insurance_support_documents" />
	</div>
<?php } else if($field_option == 'Guardians Support Documents') { ?>
	<label class="col-sm-4 control-label">Guardians Support Documents:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['guardians_support_documents'] != '') {
			echo '<span><a href="download/'.$contact['guardians_support_documents'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="guardians_support_documents">Delete</a></span>';
		} ?>
		<input name="guardians_support_documents" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="guardians_support_documents" />
	</div>
<?php } else if($field_option == 'Emergency Contact Support Documents') { ?>
	<label class="col-sm-4 control-label">Emergency Contact Support Documents:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['emergency_contact_support_documents'] != '') {
			echo '<span><a href="download/'.$contact['emergency_contact_support_documents'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="emergency_contact_support_documents">Delete</a></span>';
		} ?>
		<input name="emergency_contact_support_documents" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="emergency_contact_support_documents" />
	</div>
<?php } else if($field_option == 'Trustee Support Documents') { ?>
	<label class="col-sm-4 control-label">Trustee Support Documents:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['trustee_support_documents'] != '') {
			echo '<span><a href="download/'.$contact['trustee_support_documents'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="trustee_support_documents">Delete</a></span>';
		} ?>
		<input name="trustee_support_documents" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="trustee_support_documents" />
	</div>
<?php } else if($field_option == 'Family Doctor Support Documents') { ?>
	<label class="col-sm-4 control-label">Family Doctor Support Documents:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['family_doctor_support_documents'] != '') {
			echo '<span><a href="download/'.$contact['family_doctor_support_documents'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="family_doctor_support_documents">Delete</a></span>';
		} ?>
		<input name="family_doctor_support_documents" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="family_doctor_support_documents" />
	</div>
<?php } else if($field_option == 'Dentist Support Documents') { ?>
	<label class="col-sm-4 control-label">Dentist Support Documents:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['dentist_support_documents'] != '') {
			echo '<span><a href="download/'.$contact['dentist_support_documents'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="dentist_support_documents">Delete</a></span>';
		} ?>
		<input name="dentist_support_documents" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="dentist_support_documents" />
	</div>
<?php } else if($field_option == 'Specialists Support Documents') { ?>
	<label class="col-sm-4 control-label">Specialists Support Documents:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['specialists_support_documents'] != '') {
			echo '<span><a href="download/'.$contact['specialists_support_documents'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="specialists_support_documents">Delete</a></span>';
		} ?>
		<input name="specialists_support_documents" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="specialists_support_documents" />
	</div>
<?php } else if($field_option == 'Diagnosis Support Documents') { ?>
	<label class="col-sm-4 control-label">Diagnosis Support Documents:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['diagnosis_support_documents'] != '') {
			echo '<span><a href="download/'.$contact['diagnosis_support_documents'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="diagnosis_support_documents">Delete</a></span>';
		} ?>
		<input name="diagnosis_support_documents" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="diagnosis_support_documents" />
	</div>
<?php } else if($field_option == 'Allergies Support Documents') { ?>
	<label class="col-sm-4 control-label">Allergies Support Documents:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['allergies_support_documents'] != '') {
			echo '<span><a href="download/'.$contact['allergies_support_documents'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="allergies_support_documents">Delete</a></span>';
		} ?>
		<input name="allergies_support_documents" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="allergies_support_documents" />
	</div>
<?php } else if($field_option == 'Equipment Support Documents') { ?>
	<label class="col-sm-4 control-label">Equipment Support Documents:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['equipment_support_documents'] != '') {
			echo '<span><a href="download/'.$contact['equipment_support_documents'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="equipment_support_documents">Delete</a></span>';
		} ?>
		<input name="equipment_support_documents" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="equipment_support_documents" />
	</div>
<?php } else if($field_option == 'Goal Support Documents') { ?>
	<label class="col-sm-4 control-label">Support Documents:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['goal_support_documents'] != '') {
			echo '<span><a href="download/'.$contact['goal_support_documents'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="goal_support_documents">Delete</a></span>';
		} ?>
		<input name="goal_support_documents" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="goal_support_documents" />
	</div>
<?php } else if($field_option == 'Medical Support Documents') { ?>
	<label class="col-sm-4 control-label">Medical Support Documents:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['medical_support_documents'] != '') {
			echo '<span><a href="download/'.$contact['medical_support_documents'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="medical_support_documents">Delete</a></span>';
		} ?>
		<input name="medical_support_documents" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="medical_support_documents" />
	</div>
<?php } else if($field_option == 'Funding Support Documents') { ?>
	<div class="clone_exception_block">
        <label class="col-sm-4 control-label">Support Documents:</label>
        <div class="col-sm-8">
            <?php if(!empty($_GET['edit']) && $contact['funding_support_documents'] != '') {
                echo '<span><a href="download/'.$contact['funding_support_documents'].'" target="_blank">View</a> |
                <a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="funding_support_documents">Delete</a></span>';
            } ?>
            <input name="funding_support_documents" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="funding_support_documents" />
        </div>
    </div>
<?php } else if($field_option == 'Medical Details First Aid/CPR') { ?>
	<label class="col-sm-4 control-label">Support Documents:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['medical_details_first_aid_cpr'] != '') {
			echo '<span><a href="download/'.$contact['medical_details_first_aid_cpr'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="medical_details_first_aid_cpr">Delete</a></span>';
		} ?>
		<input name="medical_details_first_aid_cpr" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="medical_details_first_aid_cpr" />
	</div>
<?php } else if($field_option == 'Medical Details Support Documents') { ?>
	<label class="col-sm-4 control-label">Medical Details Support Documents:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['medical_details_support_documents'] != '') {
			echo '<span><a href="download/'.$contact['medical_details_support_documents'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="medical_details_support_documents">Delete</a></span>';
		} ?>
		<input name="medical_details_support_documents" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="medical_details_support_documents" />
	</div>
<?php } else if($field_option == 'Seizure Protocol Upload') { ?>
	<label class="col-sm-4 control-label">Seizure Protocol Upload:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['seizure_protocol_upload'] != '') {
			echo '<span><a href="download/'.$contact['seizure_protocol_upload'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="seizure_protocol_upload">Delete</a></span>';
		} ?>
		<input name="seizure_protocol_upload" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="seizure_protocol_upload" />
	</div>
<?php } else if($field_option == 'Slip Fall Protocol Upload') { ?>
	<label class="col-sm-4 control-label">Slip Fall Protocol Upload:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['slip_fall_protocol_upload'] != '') {
			echo '<span><a href="download/'.$contact['slip_fall_protocol_upload'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="slip_fall_protocol_upload">Delete</a></span>';
		} ?>
		<input name="slip_fall_protocol_upload" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="slip_fall_protocol_upload" />
	</div>
<?php } else if($field_option == 'Transfer Protocol Upload') { ?>
	<label class="col-sm-4 control-label">Transfer Protocol Upload:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['transfer_protocol_upload'] != '') {
			echo '<span><a href="download/'.$contact['transfer_protocol_upload'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="transfer_protocol_upload">Delete</a></span>';
		} ?>
		<input name="transfer_protocol_upload" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="transfer_protocol_upload" />
	</div>
<?php } else if($field_option == 'Toileting Protocol Upload') { ?>
	<label class="col-sm-4 control-label">Toileting Protocol Upload:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['toileting_protocol_upload'] != '') {
			echo '<span><a href="download/'.$contact['toileting_protocol_upload'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="toileting_protocol_upload">Delete</a></span>';
		} ?>
		<input name="toileting_protocol_upload" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="toileting_protocol_upload" />
	</div>
<?php } else if($field_option == 'Bathing Protocol Upload') { ?>
	<label class="col-sm-4 control-label">Bathing Protocol Upload:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['bathing_protocol_upload'] != '') {
			echo '<span><a href="download/'.$contact['bathing_protocol_upload'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="bathing_protocol_upload">Delete</a></span>';
		} ?>
		<input name="bathing_protocol_upload" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="bathing_protocol_upload" />
	</div>
<?php } else if($field_option == 'G-Tube Protocol Upload') { ?>
	<label class="col-sm-4 control-label">G-Tube Protocol Upload:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['gtube_protocol_upload'] != '') {
			echo '<span><a href="download/'.$contact['gtube_protocol_upload'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="gtube_protocol_upload">Delete</a></span>';
		} ?>
		<input name="gtube_protocol_upload" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="gtube_protocol_upload" />
	</div>
<?php } else if($field_option == 'Oxygen Protocol Upload') { ?>
	<label class="col-sm-4 control-label">Oxygen Protocol Upload:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['oxygen_protocol_upload'] != '') {
			echo '<span><a href="download/'.$contact['oxygen_protocol_upload'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="oxygen_protocol_upload">Delete</a></span>';
		} ?>
		<input name="oxygen_protocol_upload" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="oxygen_protocol_upload" />
	</div>
<?php } else if($field_option == 'SRC Upload') { ?>
	<label class="col-sm-4 control-label">SRC Upload:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['src_upload'] != '') {
			echo '<span><a href="download/'.$contact['src_upload'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="src_upload">Delete</a></span>';
		} ?>
		<input name="src_upload" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="src_upload" />
	</div>
<?php } else if($field_option == 'Contract Worker Sheet') { ?>
	<label class="col-sm-4 control-label">Contact Sheet:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['contract_contacts'] != '') {
			echo '<span><a href="download/'.$contact['contract_contacts'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="contract_contacts">Delete</a></span>';
		} ?>
		<input name="contract_contacts" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="contract_contacts" />
	</div>
<?php } else if($field_option == 'Contract Worker List') { ?>
	<label class="col-sm-4 control-label">List of Workers:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['contract_worker_list'] != '') {
			echo '<span><a href="download/'.$contact['contract_worker_list'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="contract_worker_list">Delete</a></span>';
		} ?>
		<input name="contract_worker_list" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="contract_worker_list" />
	</div>
<?php } else if($field_option == 'Contract Worker Abstract') { ?>
	<label class="col-sm-4 control-label">Abstracts:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['contract_abstract'] != '') {
			echo '<span><a href="download/'.$contact['contract_abstract'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="contract_abstract">Delete</a></span>';
		} ?>
		<input name="contract_abstract" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="contract_abstract" />
	</div>
<?php } else if($field_option == 'Contract Worker Licences') { ?>
	<label class="col-sm-4 control-label">Licences:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['contractor_licence'] != '') {
			echo '<span><a href="download/'.$contact['contractor_licence'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="contractor_licence">Delete</a></span>';
		} ?>
		<input name="contractor_licence" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="contractor_licence" />
	</div>
<?php } else if($field_option == 'Contract Worker Criminal Record') { ?>
	<label class="col-sm-4 control-label">Criminal Record Check:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['criminal_records'] != '') {
			echo '<span><a href="download/'.$contact['criminal_records'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="criminal_records">Delete</a></span>';
		} ?>
		<input name="criminal_records" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="criminal_records" />
	</div>
<?php } else if($field_option == 'Contract Worker Criminal Record Auth') { ?>
	<label class="col-sm-4 control-label">Criminal Record Check Authorization:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['criminal_check_auth'] != '') {
			echo '<span><a href="download/'.$contact['criminal_check_auth'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="criminal_check_auth">Delete</a></span>';
		} ?>
		<input name="criminal_check_auth" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="criminal_check_auth" />
	</div>
<?php } else if($field_option == 'Contract Worker Bank Info') { ?>
	<label class="col-sm-4 control-label">Bank Information:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['bank_info'] != '') {
			echo '<span><a href="download/'.$contact['bank_info'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="bank_info">Delete</a></span>';
		} ?>
		<input name="bank_info" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="bank_info" />
	</div>
<?php } else if($field_option == 'Contract Worker Business Registration') { ?>
	<label class="col-sm-4 control-label">Proof of Business Registration / Incorporation:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['proof_of_registration'] != '') {
			echo '<span><a href="download/'.$contact['proof_of_registration'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="proof_of_registration">Delete</a></span>';
		} ?>
		<input name="proof_of_registration" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="proof_of_registration" />
	</div>
<?php } else if($field_option == 'Contract Policies Agreement') { ?>
	<label class="col-sm-4 control-label">Contractor Agreement:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['contractor_agreement'] != '') {
			echo '<span><a href="download/'.$contact['contractor_agreement'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="contractor_agreement">Delete</a></span>';
		} ?>
		<input name="contractor_agreement" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="contractor_agreement" />
	</div>
<?php } else if($field_option == 'Contract Policies Non Compete') { ?>
	<label class="col-sm-4 control-label">Non-Compete Policy:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['non_compete'] != '') {
			echo '<span><a href="download/'.$contact['non_compete'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="non_compete">Delete</a></span>';
		} ?>
		<input name="non_compete" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="non_compete" />
	</div>
<?php } else if($field_option == 'Contract Policies Non Solicitation') { ?>
	<label class="col-sm-4 control-label">Non-Solicitation Policy:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['non_solicitation'] != '') {
			echo '<span><a href="download/'.$contact['non_solicitation'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="non_solicitation">Delete</a></span>';
		} ?>
		<input name="non_solicitation" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="non_solicitation" />
	</div>
<?php } else if($field_option == 'Contract Policies Confidentiality') { ?>
	<label class="col-sm-4 control-label">Confidentiality Policy:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['confidentiality'] != '') {
			echo '<span><a href="download/'.$contact['confidentiality'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="confidentiality">Delete</a></span>';
		} ?>
		<input name="confidentiality" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="confidentiality" />
	</div>
<?php } else if($field_option == 'Contract Policies Uniforms') { ?>
	<label class="col-sm-4 control-label">Uniform Policy:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['uniform_policy'] != '') {
			echo '<span><a href="download/'.$contact['uniform_policy'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="uniform_policy">Delete</a></span>';
		} ?>
		<input name="uniform_policy" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="uniform_policy" />
	</div>
<?php } else if($field_option == 'Contract Policies Leasing') { ?>
	<label class="col-sm-4 control-label">Lease Agreements:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['lease_agreement'] != '') {
			echo '<span><a href="download/'.$contact['lease_agreement'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="lease_agreement">Delete</a></span>';
		} ?>
		<input name="lease_agreement" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="lease_agreement" />
	</div>
<?php } else if($field_option == 'Contract Policies Fuel Card') { ?>
	<label class="col-sm-4 control-label">Fuel Card Agreement:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['fuel_card_agreement'] != '') {
			echo '<span><a href="download/'.$contact['fuel_card_agreement'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="fuel_card_agreement">Delete</a></span>';
		} ?>
		<input name="fuel_card_agreement" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="fuel_card_agreement" />
	</div>
<?php } else if($field_option == 'Contract WCB Clearance') { ?>
	<label class="col-sm-4 control-label">WCB Clearance Letter:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['wcb_clearance'] != '') {
			echo '<span><a href="download/'.$contact['wcb_clearance'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="wcb_clearance">Delete</a></span>';
		} ?>
		<input name="wcb_clearance" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="wcb_clearance" />
	</div>
<?php } else if($field_option == 'Contract WCB Insurance') { ?>
	<label class="col-sm-4 control-label">Copy of Valid Insurance:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['contract_insurance'] != '') {
			echo '<span><a href="download/'.$contact['contract_insurance'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="contract_insurance">Delete</a></span>';
		} ?>
		<input name="contract_insurance" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="contract_insurance" />
	</div>
<?php } else if($field_option == 'Contract Rates Signed') { ?>
	<label class="col-sm-4 control-label">Signed Rate Sheet:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['rate_sheet'] != '') {
			echo '<span><a href="download/'.$contact['rate_sheet'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="rate_sheet">Delete</a></span>';
		} ?>
		<input name="rate_sheet" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="rate_sheet" />
	</div>
<?php } else if($field_option == 'Comments Attachment') { ?>
	<label class="col-sm-4 control-label">Comments Attachment:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['comments_attachment'] != '') {
			echo '<span><a href="download/'.$contact['comments_attachment'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="comments_attachment">Delete</a></span>';
		} ?>
		<input name="comments_attachment" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="comments_attachment" />
	</div>
<?php } else if($field_option == 'Description Attachment') { ?>
	<label class="col-sm-4 control-label">Description Attachment:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['description_attachment'] != '') {
			echo '<span><a href="download/'.$contact['description_attachment'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="description_attachment">Delete</a></span>';
		} ?>
		<input name="description_attachment" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="description_attachment" />
	</div>
<?php } else if($field_option == 'General Comments Attachment') { ?>
	<label class="col-sm-4 control-label">General Comments Attachment:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['general_comments_attachment'] != '') {
			echo '<span><a href="download/'.$contact['general_comments_attachment'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="general_comments_attachment">Delete</a></span>';
		} ?>
		<input name="general_comments_attachment" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="general_comments_attachment" />
	</div>
<?php } else if($field_option == 'Notes Attachment') { ?>
	<label class="col-sm-4 control-label">Notes Attachment:</label>
	<div class="col-sm-8">
		<?php if(!empty($_GET['edit']) && $contact['notes_attachment'] != '') {
			echo '<span><a href="download/'.$contact['notes_attachment'].'" target="_blank">View</a> |
			<a href="" onclick="saveField($(this).change()); return false;" data-table="contacts_upload" data-field="notes_attachment">Delete</a></span>';
		} ?>
		<input name="notes_attachment" type="file" data-filename-placement="inside" class="form-control" data-table="contacts_upload" data-field="notes_attachment" />
	</div>
<?php }