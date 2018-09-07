<?php if (strpos($value_config, ','."Application".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Application:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($application != '') ) {
    echo '<a href="download/'.$application.'" target="_blank">View</a>';
		if(strpos($edit_config, ','."Application".',') !== false) {
			echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>'; ?>
			<input type="hidden" name="application_hidden" value="<?php echo $application; ?>" />
			<input name="application" type="file" data-filename-placement="inside" class="form-control" />
		<?php }
	} else if(strpos($edit_config, ','."Application".',') !== false) { ?>
    <input name="application" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Contact Image".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Contact Image:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($contactimage != '') ) {
    echo '<a href="download/'.$contactimage.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Contact Image".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="contactimage_hidden" value="<?php echo $contactimage; ?>" />
		<input <?php echo (strpos($edit_config, ','."Contact Image".',') === false ? 'readonly' : ''); ?> name="contactimage" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Contact Image".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Contact Image".',') === false ? 'readonly' : ''); ?> name="contactimage" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Upload License Plate".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Upload License Plate:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($upload_license_plate != '') ) {
    echo '<a href="download/'.$upload_license_plate.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Upload License Plate".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="upload_license_plate_hidden" value="<?php echo $upload_license_plate; ?>" />
		<input <?php echo (strpos($edit_config, ','."Upload License Plate".',') === false ? 'readonly' : ''); ?> name="upload_license_plate" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Upload License Plate".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Upload License Plate".',') === false ? 'readonly' : ''); ?> name="upload_license_plate" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Upload Property Information".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Upload Property Information:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($upload_property_information != '') ) {
    echo '<a href="download/'.$upload_property_information.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Upload Property Information".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="upload_property_information_hidden" value="<?php echo $upload_property_information; ?>" />
		<input <?php echo (strpos($edit_config, ','."Upload Property Information".',') === false ? 'readonly' : ''); ?> name="upload_property_information" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Upload Property Information".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Upload Property Information".',') === false ? 'readonly' : ''); ?> name="upload_property_information" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Upload Inspection".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Upload Inspection:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($upload_inspection != '') ) {
    echo '<a href="download/'.$upload_inspection.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Upload Inspection".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="upload_inspection_hidden" value="<?php echo $upload_inspection; ?>" />
		<input <?php echo (strpos($edit_config, ','."Upload Inspection".',') === false ? 'readonly' : ''); ?> name="upload_inspection" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Upload Inspection".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Upload Inspection".',') === false ? 'readonly' : ''); ?> name="upload_inspection" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Upload Letter of Intent".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Upload Letter of Intent:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($upload_letter_of_intent != '') ) {
    echo '<a href="download/'.$upload_letter_of_intent.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Upload Letter of Intent".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="upload_letter_of_intent_hidden" value="<?php echo $upload_letter_of_intent; ?>" />
		<input <?php echo (strpos($edit_config, ','."Upload Letter of Intent".',') === false ? 'readonly' : ''); ?> name="upload_letter_of_intent" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Upload Letter of Intent".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Upload Letter of Intent".',') === false ? 'readonly' : ''); ?> name="upload_letter_of_intent" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Upload Vendor Documents".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Upload <?= VENDOR_TILE ?> Documents:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($upload_vendor_documents != '') ) {
    echo '<a href="download/'.$upload_vendor_documents.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Upload Vendor Documents".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="upload_vendor_documents_hidden" value="<?php echo $upload_vendor_documents; ?>" />
		<input <?php echo (strpos($edit_config, ','."Upload Vendor Documents".',') === false ? 'readonly' : ''); ?> name="upload_vendor_documents" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Upload Vendor Documents".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Upload Vendor Documents".',') === false ? 'readonly' : ''); ?> name="upload_vendor_documents" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Upload Marketing Material".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Upload Marketing Material:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($upload_marketing_material != '') ) {
    echo '<a href="download/'.$upload_marketing_material.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Upload Marketing Material".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="upload_marketing_material_hidden" value="<?php echo $upload_marketing_material; ?>" />
		<input <?php echo (strpos($edit_config, ','."Upload Marketing Material".',') === false ? 'readonly' : ''); ?> name="upload_marketing_material" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Upload Marketing Material".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Upload Marketing Material".',') === false ? 'readonly' : ''); ?> name="upload_marketing_material" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Upload Purchase Contract".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Upload Purchase Contract:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($upload_purchase_contract != '') ) {
    echo '<a href="download/'.$upload_purchase_contract.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Upload Purchase Contract".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="upload_purchase_contract_hidden" value="<?php echo $upload_purchase_contract; ?>" />
		<input <?php echo (strpos($edit_config, ','."Upload Purchase Contract".',') === false ? 'readonly' : ''); ?> name="upload_purchase_contract" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Upload Purchase Contract".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Upload Purchase Contract".',') === false ? 'readonly' : ''); ?> name="upload_purchase_contract" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Upload Support Contract".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Upload Support Contract:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($upload_support_contract != '') ) {
    echo '<a href="download/'.$upload_support_contract.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Upload Support Contract".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="upload_support_contract_hidden" value="<?php echo $upload_support_contract; ?>" />
		<input <?php echo (strpos($edit_config, ','."Upload Support Contract".',') === false ? 'readonly' : ''); ?> name="upload_support_contract" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Upload Support Contract".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Upload Support Contract".',') === false ? 'readonly' : ''); ?> name="upload_support_contract" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Upload Support Terms".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Upload Support Terms:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($upload_support_terms != '')) {
    echo '<a href="download/'.$upload_support_terms.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Upload Support Terms".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="upload_support_terms_hidden" value="<?php echo $upload_support_terms; ?>" />
		<input <?php echo (strpos($edit_config, ','."Upload Support Terms".',') === false ? 'readonly' : ''); ?> name="upload_support_terms" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Upload Support Terms".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Upload Support Terms".',') === false ? 'readonly' : ''); ?> name="upload_support_terms" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Upload Rental Contract".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Upload Rental Contract:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($upload_rental_contract != '') ) {
    echo '<a href="download/'.$upload_rental_contract.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Upload Rental Contract".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="upload_rental_contract_hidden" value="<?php echo $upload_rental_contract; ?>" />
		<input <?php echo (strpos($edit_config, ','."Upload Rental Contract".',') === false ? 'readonly' : ''); ?> name="upload_rental_contract" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Upload Rental Contract".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Upload Rental Contract".',') === false ? 'readonly' : ''); ?> name="upload_rental_contract" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Upload Management Contract".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Upload Management Contract:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($upload_management_contract != '') ) {
    echo '<a href="download/'.$upload_management_contract.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Upload Management Contract".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="upload_management_contract_hidden" value="<?php echo $upload_management_contract; ?>" />
		<input <?php echo (strpos($edit_config, ','."Upload Management Contract".',') === false ? 'readonly' : ''); ?> name="upload_management_contract" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Upload Management Contract".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Upload Management Contract".',') === false ? 'readonly' : ''); ?> name="upload_management_contract" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Upload Articles of Incorporation".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Upload Articles of Incorporation:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($upload_articles_of_incorporation != '') ) {
    echo '<a href="download/'.$upload_articles_of_incorporation.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Upload Articles of Incorporation".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="upload_articles_of_incorporation_hidden" value="<?php echo $upload_articles_of_incorporation; ?>" />
		<input <?php echo (strpos($edit_config, ','."Upload Articles of Incorporation".',') === false ? 'readonly' : ''); ?> name="upload_articles_of_incorporation" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Upload Articles of Incorporation".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Upload Articles of Incorporation".',') === false ? 'readonly' : ''); ?> name="upload_articles_of_incorporation" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Upload Commercial Insurance".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Upload Commercial Insurance:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($upload_commercial_insurance != '') ) {
    echo '<a href="download/'.$upload_commercial_insurance.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Upload Commercial Insurance".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="upload_commercial_insurance_hidden" value="<?php echo $upload_commercial_insurance; ?>" />
		<input <?php echo (strpos($edit_config, ','."Upload Commercial Insurance".',') === false ? 'readonly' : ''); ?> name="upload_commercial_insurance" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Upload Commercial Insurance".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Upload Commercial Insurance".',') === false ? 'readonly' : ''); ?> name="upload_commercial_insurance" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Upload Residential Insurance".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Upload Residential Insurance:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($upload_residential_insurance != '') ) {
    echo '<a href="download/'.$upload_residential_insurance.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Upload Residential Insurance".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="upload_residential_insurance_hidden" value="<?php echo $upload_residential_insurance; ?>" />
		<input <?php echo (strpos($edit_config, ','."Upload Residential Insurance".',') === false ? 'readonly' : ''); ?> name="upload_residential_insurance" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Upload Residential Insurance".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Upload Residential Insurance".',') === false ? 'readonly' : ''); ?> name="upload_residential_insurance" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Upload WCB".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Upload WCB:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($upload_wcb != '') ) {
    echo '<a href="download/'.$upload_wcb.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Upload WCB".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="upload_wcb_hidden" value="<?php echo $upload_wcb; ?>" />
		<input <?php echo (strpos($edit_config, ','."Upload WCB".',') === false ? 'readonly' : ''); ?> name="upload_wcb" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Upload WCB".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Upload WCB".',') === false ? 'readonly' : ''); ?> name="upload_wcb" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php // Client Information ?>

  <?php if (strpos($value_config, ','."Client Support Documents".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Client Support Documents:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($client_support_documents != '') ) {
    echo '<a href="download/'.$client_support_documents.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Client Support Documents".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="client_support_documents_hidden" value="<?php echo $client_support_documents; ?>" />
		<input <?php echo (strpos($edit_config, ','."Client Support Documents".',') === false ? 'readonly' : ''); ?> name="client_support_documents" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Client Support Documents".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Client Support Documents".',') === false ? 'readonly' : ''); ?> name="client_support_documents" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Member Support Documents".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Member Support Documents:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($client_support_documents != '') ) {
    echo '<a href="download/'.$client_support_documents.'" target="_blank">View</a> - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
    <input type="hidden" name="client_support_documents_hidden" value="<?php echo $client_support_documents; ?>" />
    <input <?php echo (strpos($edit_config, ','."Member Support Documents".',') === false ? 'readonly ' : ''); ?>name="client_support_documents" type="file" data-filename-placement="inside" class="form-control" />
    <?php } else { ?>
    <input <?php echo (strpos($edit_config, ','."Member Support Documents".',') === false ? 'readonly ' : ''); ?>name="client_support_documents" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Transportation Support Documents".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Transportation Support Documents:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($transportation_support_documents != '') ) {
    echo '<a href="download/'.$transportation_support_documents.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Transportation Support Documents".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="transportation_support_documents_hidden" value="<?php echo $transportation_support_documents; ?>" />
		<input <?php echo (strpos($edit_config, ','."Transportation Support Documents".',') === false ? 'readonly' : ''); ?> name="transportation_support_documents" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Transportation Support Documents".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Transportation Support Documents".',') === false ? 'readonly' : ''); ?> name="transportation_support_documents" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Transportation Upload License".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Upload Driver's Licence:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($upload_drivers_license != '') ) {
    echo '<a href="download/'.$upload_drivers_license.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Transportation Upload License".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="upload_drivers_license_hidden" value="<?php echo $upload_drivers_license; ?>" />
		<input <?php echo (strpos($edit_config, ','."Transportation Upload License".',') === false ? 'readonly' : ''); ?> name="upload_drivers_license" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Transportation Upload License".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Transportation Upload License".',') === false ? 'readonly' : ''); ?> name="upload_drivers_license" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Void Cheque".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Upload Void Cheque:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['id'])) && ($cheque_upload != '') ) {
    echo '<a href="download/'.$cheque_upload.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Void Cheque".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="upload_blank_cheque_hidden" value="<?php echo $cheque_upload; ?>" />
		<input <?php echo (strpos($edit_config, ','."Void Cheque".',') === false ? 'readonly' : ''); ?> name="upload_blank_cheque" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Void Cheque".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Void Cheque".',') === false ? 'readonly' : ''); ?> name="upload_blank_cheque" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Insurance Support Documents".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Insurance Support Documents:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($insurance_support_documents != '') ) {
    echo '<a href="download/'.$insurance_support_documents.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Insurance Support Documents".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="insurance_support_documents_hidden" value="<?php echo $insurance_support_documents; ?>" />
		<input <?php echo (strpos($edit_config, ','."Insurance Support Documents".',') === false ? 'readonly' : ''); ?> name="insurance_support_documents" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Insurance Support Documents".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Insurance Support Documents".',') === false ? 'readonly' : ''); ?> name="insurance_support_documents" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Guardians Support Documents".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Guardians Support Documents:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($guardians_support_documents != '') ) {
    echo '<a href="download/'.$guardians_support_documents.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Guardians Support Documents".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="guardians_support_documents_hidden" value="<?php echo $guardians_support_documents; ?>" />
		<input <?php echo (strpos($edit_config, ','."Guardians Support Documents".',') === false ? 'readonly' : ''); ?> name="guardians_support_documents" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Guardians Support Documents".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Guardians Support Documents".',') === false ? 'readonly' : ''); ?> name="guardians_support_documents" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Trustee Support Documents".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Trustee Support Documents:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($trustee_support_documents != '') ) {
    echo '<a href="download/'.$trustee_support_documents.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Trustee Support Documents".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="trustee_support_documents_hidden" value="<?php echo $trustee_support_documents; ?>" />
		<input <?php echo (strpos($edit_config, ','."Trustee Support Documents".',') === false ? 'readonly' : ''); ?> name="trustee_support_documents" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Trustee Support Documents".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Trustee Support Documents".',') === false ? 'readonly' : ''); ?> name="trustee_support_documents" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Family Doctor Support Documents".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Family Doctor Support Documents:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($family_doctor_support_documents != '') ) {
    echo '<a href="download/'.$family_doctor_support_documents.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Family Doctor Support Documents".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="family_doctor_support_documents_hidden" value="<?php echo $family_doctor_support_documents; ?>" />
		<input <?php echo (strpos($edit_config, ','."Family Doctor Support Documents".',') === false ? 'readonly' : ''); ?> name="family_doctor_support_documents" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Family Doctor Support Documents".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Family Doctor Support Documents".',') === false ? 'readonly' : ''); ?> name="family_doctor_support_documents" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Dentist Support Documents".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Dentist Support Documents:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($dentist_support_documents != '') ) {
    echo '<a href="download/'.$dentist_support_documents.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Dentist Support Documents".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="dentist_support_documents_hidden" value="<?php echo $dentist_support_documents; ?>" />
		<input <?php echo (strpos($edit_config, ','."Dentist Support Documents".',') === false ? 'readonly' : ''); ?> name="dentist_support_documents" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Dentist Support Documents".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Dentist Support Documents".',') === false ? 'readonly' : ''); ?> name="dentist_support_documents" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Specialists Support Documents".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Specialists Support Documents:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($specialists_support_documents != '') ) {
    echo '<a href="download/'.$specialists_support_documents.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Specialists Support Documents".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="specialists_support_documents_hidden" value="<?php echo $specialists_support_documents; ?>" />
		<input <?php echo (strpos($edit_config, ','."Specialists Support Documents".',') === false ? 'readonly' : ''); ?> name="specialists_support_documents" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Specialists Support Documents".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Specialists Support Documents".',') === false ? 'readonly' : ''); ?> name="specialists_support_documents" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Diagnosis Support Documents".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Support Documents:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($diagnosis_support_documents != '') ) {
    echo '<a href="download/'.$diagnosis_support_documents.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Diagnosis Support Documents".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="diagnosis_support_documents_hidden" value="<?php echo $diagnosis_support_documents; ?>" />
		<input <?php echo (strpos($edit_config, ','."Diagnosis Support Documents".',') === false ? 'readonly' : ''); ?> name="diagnosis_support_documents" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Diagnosis Support Documents".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Diagnosis Support Documents".',') === false ? 'readonly' : ''); ?> name="diagnosis_support_documents" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Allergies Support Documents".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Support Documents:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($allergies_support_documents != '') ) {
    echo '<a href="download/'.$allergies_support_documents.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Allergies Support Documents".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="allergies_support_documents_hidden" value="<?php echo $allergies_support_documents; ?>" />
		<input <?php echo (strpos($edit_config, ','."Allergies Support Documents".',') === false ? 'readonly' : ''); ?> name="allergies_support_documents" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Allergies Support Documents".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Allergies Support Documents".',') === false ? 'readonly' : ''); ?> name="allergies_support_documents" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Equipment Support Documents".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Support Documents:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($equipment_support_documents != '') ) {
    echo '<a href="download/'.$equipment_support_documents.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Equipment Support Documents".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="equipment_support_documents_hidden" value="<?php echo $equipment_support_documents; ?>" />
		<input <?php echo (strpos($edit_config, ','."Equipment Support Documents".',') === false ? 'readonly' : ''); ?> name="equipment_support_documents" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Equipment Support Documents".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Equipment Support Documents".',') === false ? 'readonly' : ''); ?> name="equipment_support_documents" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Funding Support Documents".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Funding Support Documents:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($funding_support_documents != '') ) {
    echo '<a href="download/'.$funding_support_documents.'" target="_blank">View</a> - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
    <input type="hidden" name="funding_support_documents_hidden" value="<?php echo $funding_support_documents; ?>" />
    <input <?php echo (strpos($edit_config, ','."Funding Support Documents".',') === false ? 'readonly ' : ''); ?>name="funding_support_documents" type="file" data-filename-placement="inside" class="form-control" />
    <?php } else { ?>
    <input <?php echo (strpos($edit_config, ','."Funding Support Documents".',') === false ? 'readonly ' : ''); ?>name="funding_support_documents" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Medical Details First Aid/CPR".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Support Documents:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($medical_details_first_aid_cpr_documents != '') ) {
    echo '<a href="download/'.$medical_details_first_aid_cpr_documents.'" target="_blank">View</a>';
	if(strpos($edit_config, ','."Medical Details First Aid/CPR".',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="medical_details_first_aid_cpr_documents_hidden" value="<?php echo $medical_details_first_aid_cpr_documents; ?>" />
		<input <?php echo (strpos($edit_config, ','."Medical Details First Aid/CPR".',') === false ? 'readonly' : ''); ?> name="medical_details_first_aid_cpr_documents" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','."Medical Details First Aid/CPR".',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','."Medical Details First Aid/CPR".',') === false ? 'readonly' : ''); ?> name="medical_details_first_aid_cpr_documents" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>

  <?php
  $html = array(
    'medical_details_support_documents' => 'Medical Details Support Documents',
    'seizure_protocol_upload' => 'Seizure Protocol Upload',
    'slip_fall_protocol_upload' => 'Slip Fall Protocol Upload',
    'transfer_protocol_upload' => 'Transfer Protocol Upload',
    'toileting_protocol_upload' => 'Toileting Protocol Upload',
    'bathing_protocol_upload' => 'Bathing Protocol Upload',
    'gtube_protocol_upload' => 'G-Tube Protocol Upload',
    'oxygen_protocol_upload' => 'Oxygen Protocol Upload'
    );

  ?>

<?php foreach($html as $field => $title) { ?>
<?php if (strpos($value_config, ','.$title.',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Support Documents:
    <span class="popover-examples list-inline">&nbsp;
    <a  data-toggle="tooltip" data-placement="top" title="File name cannot contain commas or apostrophes"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
    </span>
    </label>
    <div class="col-sm-8">
    <?php if((!empty($_GET['contactid'])) && ($field != '') ) {
    echo '<a href="download/'.$field.'" target="_blank">View</a>';
	if(strpos($edit_config, ','.$title.',') !== false) {
		echo ' - <a href="add_contact.php?contactuploadid='.$contactuploadid.'&contactid='.$contactid.'"> Delete</a>' ?>
		<input type="hidden" name="<?php echo $field; ?>_hidden" value="<?php echo $field; ?>" />
		<input <?php echo (strpos($edit_config, ','.$title.',') === false ? 'readonly' : ''); ?> name="<?php echo $field; ?>" type="file" data-filename-placement="inside" class="form-control" />
    <?php }
	} else if(strpos($edit_config, ','.$title.',') !== false) { ?>
    <input <?php echo (strpos($edit_config, ','.$title.',') === false ? 'readonly' : ''); ?> name="<?php echo $field; ?>" type="file" data-filename-placement="inside" class="form-control" />
          <?php } ?>
      </div>
 </div>
  <?php } ?>
  <?php } ?>

