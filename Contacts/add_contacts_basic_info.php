    <?php $temp_field = 'Employee ID'; ?>
    <?php if(isset($_GET['contactid']) && strpos($value_config,'Employee ID') !== false) { ?>
	  <div class="form-group">
		<label for="employeeid" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Employee ID</label>
		<div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
		  <input name="employeeid" readonly type="text" style="width:150px" value="<?php echo $employeeid; ?>" id="employeeid" class="form-control <?php echo $required; ?>" />
		</div>
	  </div>
	<?php } ?>

    <?php $temp_field = 'Agent ID'; ?>
    <?php if (strpos($value_config,'Agent ID') !== false) { ?>
	  <div class="form-group">
		<label for="agent_id" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Agent #</label>
		<div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
		  <input name="agent_id" <?= !empty($agent_id) ? 'readonly' : '' ?> type="text" style="width:150px" value="<?php echo $agent_id; ?>" id="agent_id" class="form-control <?php echo $required; ?>" />
		</div>
	  </div>
	<?php } ?>

  <?php $temp_field = 'Business'; ?>
  <?php if (strpos($value_config, ','."Business".',') !== FALSE) { ?>
    <div class="form-group clearfix completion_date">
        <label for="first_name" class="col-sm-4 control-label text-right"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?><?php echo (get_software_name() == 'breakthebarrier' ? 'Program/Site' : 'Business'); ?>:</label>
        <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
			<?php if(isset($_GET['businessid'])): ?>
				<?php $businessid = $_GET['businessid']; ?>
				<input type="hidden" name="related_businessid" value="<?php echo $businessid; ?>">
			<?php else: ?>
				<?php if(strpos($edit_config, ',Business,') !== FALSE) { ?>
					<select name="businessid" data-placeholder="Choose a <?php echo (get_software_name() == 'breakthebarrier' ? 'Program/Site' : 'Business'); ?>..." data-no_results_text="Add a <?php echo (get_software_name() == 'breakthebarrier' ? 'Program/Site' : 'Business'); ?>:" width="380" id="businesssiteid" class="chosen-select-deselect <?php echo $required; ?>">
						<option value=''></option>
					</select>
				<?php } else { ?>
					<input type="hidden" name="businessid" value="<?= $businessid ?>">
					<input type="text" readonly name="bussinessid_label" value="<?= get_client($dbc, $businessid) ?>" class="form-control <?php echo $required; ?>">
				<?php } ?>
			<?php endif; ?>
        </div>
    </div>
    <?php } ?>

  <?php $temp_field = 'Contact Category'; ?>
  <?php if (strpos($value_config, ','."Contact Category".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="position[]" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Contact Category:</label>
    <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
		<?php if(strpos($edit_config, ',Contact Category,') !== FALSE) { ?>
			<select  data-placeholder="Choose a Category..." id="contact_category" name="category_contact" width="380" class="chosen-select-deselect <?php echo $required; ?>">
			  <option value=""></option>
			  <?php
				$query = mysqli_query($dbc,"SELECT distinct(category_contact) FROM contacts");
				while($row = mysqli_fetch_array($query)) {
					if ($category_contact == $row['category_contact']) {
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}
					echo "<option ".$selected." value='". $row['category_contact']."'>".$row['category_contact'].'</option>';
				}
				//echo "<option value = 'Other'>New Category</option>";
			  ?>
			</select>

		<?php } else { ?>
			<input type="text" readonly name="contact_category" value="<?= $category_contact ?>" class="form-control <?php echo $required; ?>">
		<?php } ?>    </div>
  </div>

<div class="form-group" id="new_category" style="display: none;">
    <label for="travel_task" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>New Category: </label>
    <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
        <input name="new_category" type="text" class="form-control <?php echo $required; ?>"/>
    </div>
</div>

  <?php } ?>

  <?php $temp_field = 'Staff Category'; ?><?php if (strpos($value_config, ','."Staff Category".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="position[]" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
        <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Choose the staff category from the dropdown."><img src="../img/info.png" width="20"></a></span>
        Staff Category:
    </label>
    <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
		<?php if(strpos($edit_config, ',Staff Category,') !== FALSE) { ?>
            <select data-placeholder="Choose a Category..." class='chosen-select-deselect <?php echo $required; ?> form-control <?php echo $required; ?>' id="staff_category" name="staff_category[]" multiple width="380">
              <?php
				if($_GET['contactid']) {
					$contactid = $_GET['contactid'];
					$staff_category_result = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT staff_category FROM contacts where contactid=$contactid"));
				}

                $category = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT categories FROM field_config_contacts where categories is not null"));
				$categories = explode(",", $category['categories']);
                foreach($categories as $indCategory) {
					$selected = '';
					if ($_GET['contactid'] && strpos(','.$staff_category_result['staff_category'].',', ','.$indCategory.',') !== FALSE) {
							$selected = 'selected="selected"';
					}

                    echo "<option ".$selected." value='". $indCategory."'>".$indCategory.'</option>';
                }
                //echo "<option value = 'Other'>New Category</option>";
              ?>
            </select>
		<?php } else { ?>
			<input type="text" readonly name="staff_category" value="<?= $indCategory ?>" class="form-control <?php echo $required; ?>">
		<?php } ?>
    </div>
  </div>

   <?php } ?>



		<?php $temp_field = 'Business Sites'; ?><?php if (strpos($value_config, ','."Business Sites".',') !== FALSE) { ?>
		<div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Sites:</label>
		  <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
			<?php if(strpos($edit_config, ',Business Sites,') !== FALSE) { ?>
			<select data-placeholder="Choose a Sites..." name="siteid" id="siteid"  width="580" class="chosen-select-deselect <?php echo $required; ?>">
			  <option value=""></option>
              <?php
                    $query = mysqli_query($dbc,"SELECT contactid, site_name FROM contacts WHERE category='Sites'");
                    echo '<option value=""></option>';
                    while($row = mysqli_fetch_array($query)) {
                        echo "<option ".($siteid == $row['contactid'] ? 'selected' : '')." value='".$row['contactid']."'>".$row['site_name'].'</option>';
                    }
              ?>
			</select>
			<?php } else { ?>
				<input type="hidden" name="siteid" value="<?= $siteid ?>">
				<input type="text" readonly name="siteid_label" value="<?= get_contact($dbc, $siteid, 'site_name') ?>" class="form-control <?php echo $required; ?>">
			<?php } ?>
		  </div>
		</div>
        <?php } ?>

        <?php $temp_field = 'Site LSD'; ?><?php if (strpos($value_config, ','."Site LSD".',') !== FALSE) { ?>
		<div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Site LSD:</label>
		  <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
			<input type="text" name="lsd" value="<?= $lsd ?>" class="form-control <?php echo $required; ?>">
		  </div>
		</div>
        <?php } ?>

        <?php $temp_field = 'Name'; ?><?php if (strpos($value_config, ','."Name".',') !== FALSE) { ?>
			<?php
			if(isset($_GET['name']) && $name == '') {
				$name = mysqli_real_escape_string($dbc,urldecode($_GET['name']));
			} ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?><?php echo $url_category;?> Name:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Name".',') === false ? 'readonly' : ''); ?> name="name" value="<?php echo $name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Customer(Client/Customer/Business)'; ?><?php if (strpos($value_config, ','."Customer(Client/Customer/Business)".',') !== FALSE) { ?>
        <div class="form-group">
            <label for="site_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Customer:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
			<?php if(strpos($edit_config, ',Customer(Client/Customer/Business),') !== FALSE) { ?>
            <select data-placeholder="Choose a Customer..." name="siteclientid" width="380" class="chosen-select-deselect <?php echo $required; ?>">
              <option value=""></option>
              <?php
                $query = mysqli_query($dbc,"SELECT distinct(name), contactid FROM contacts WHERE category='Client' OR category='Customer' OR category='Business' ORDER BY name");
                while($row = mysqli_fetch_array($query)) {
                    if ($siteclientid == $row['contactid']) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['name']).'</option>';
                }
              ?>
            </select>
			<?php } else { ?>
				<input type="hidden" name="siteclientid" value="<?= $siteclientid ?>">
				<input type="text" readonly name="siteclientid_label" value="<?= get_client($dbc, $siteclientid) ?>" class="form-control <?php echo $required; ?>">
			<?php } ?>
            </div>
        </div>
        <?php } ?>

        <?php $temp_field = 'Site Number'; ?><?php if (strpos($value_config, ','."Site Number".',') !== FALSE) { ?>
          <div class="form-group">
          <label for="site_number" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Site #:</label>
          <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
          <input <?php echo (strpos($edit_config, ','."Site Number".',') === false ? 'readonly' : ''); ?> name="site_number" value="<?php echo $site_number; ?>" type="text" class="form-control <?php echo $required; ?>">
          </div>
          </div>
        <?php } ?>

        <?php $temp_field = 'Site Name (Location)'; ?><?php if (strpos($value_config,'Site Name (Location)') !== false) { ?>
          <div class="form-group">
            <label for="site_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Site Name	(Location):</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input name="site_name" type="text" value="<?php echo $site_name; ?>" id="site_name" class="form-control <?php echo $required; ?>" />
            </div>
          </div>
        <?php } ?>

		<?php $temp_field = 'Contact Prefix'; ?><?php if (strpos($value_config, ','."Contact Prefix".',') !== FALSE) { ?>
            <div class="form-group">
				<label for="prefix" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Prefix <font color="red">*</font>:</label>
				<div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				  <?php
				  $prefix = '';
				  if(isset($_GET['contactid'])) {
					  $contactid=$_GET['contactid'];
					  $query_prefix = mysqli_fetch_row(mysqli_query($dbc,"SELECT prefix FROM contacts WHERE contactid=$contactid"));
					  $prefix = $query_prefix[0];
		          }
				  ?>
				<?php if(strpos($edit_config, ',Contact Prefix,') !== FALSE) { ?>
				  <select id="prefix" class='form-control <?php echo $required; ?> chosen-select-deselect <?php echo $required; ?>' style="width:100px" data-placeholder="Choose a Prefix..." name="prefix">
						<option value=''></option>
					<?php if($prefix == 'mr'): ?>
						<option selected value="mr">Mr.</option>
						<option value="mrs">Mrs.</option>
						<option value="miss">Miss</option>
						<option value="other">Other</option>
					<?php elseif($prefix == 'mrs'): ?>
						<option value="mr">Mr.</option>
						<option selected value="mrs">Mrs.</option>
						<option value="miss">Miss</option>
						<option value="other">Other</option>
					<?php elseif($prefix == 'miss'): ?>
						<option value="mr">Mr.</option>
						<option value="mrs">Mrs.</option>
						<option selected value="miss">Miss</option>
						<option value="other">Other</option>
					<?php elseif($prefix == 'other'): ?>
						<option value="mr">Mr.</option>
						<option value="mrs">Mrs.</option>
						<option value="miss">Miss</option>
						<option selected value="other">Other</option>
					<?php else: ?>
						<option value="mr">Mr.</option>
						<option value="mrs">Mrs.</option>
						<option value="miss">Miss</option>
						<option value="other">Other</option>
					<?php endif; ?>
				  </select>
				<?php } else { ?>
					<input type="text" readonly name="prefix" value="<?= $prefix ?>" class="form-control <?php echo $required; ?>">
				<?php } ?>
				</div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Contact Filters'; ?><?php if (strpos($value_config, ','."Contact Filters".',') !== FALSE) { ?>
		<div class="form-group">
			<label for="region" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Region:</label>
			<div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				<select data-placeholder="Choose a Region..." class='form-control <?php echo $required; ?> chosen-select-deselect <?php echo $required; ?>' id="con_regions" name="con_regions" width="380">
				  <option value=""></option>
				  <?php
					if($_GET['contactid']) {
						$contactid = $_GET['contactid'];
						$staff_category_result = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT con_regions FROM contacts where contactid=$contactid"));
					}

					$category = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT con_regions FROM field_config_contacts where con_regions is not null"));
					$con_regions = explode(",", $category['con_regions']);
					foreach($con_regions as $con_region) {
						$selected = '';
						if ($_GET['contactid'] && $con_region == $staff_category_result['con_regions']) {
								$selected = 'selected="selected"';
						}

						echo "<option ".$selected." value='". $con_region."'>".$con_region.'</option>';
					}
					//echo "<option value = 'Other'>New Category</option>";
				  ?>
				</select>

			</div>
	  </div>



	  <div class="form-group">
			<label for="classifications[]" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Classifications:</label>
			<div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				<select data-placeholder="Choose a Classifications..." class='form-control <?php echo $required; ?> chosen-select-deselect <?php echo $required; ?>' id="con_classifications" name="con_classifications" width="380">
				  <option value=""></option>
				  <?php
					if($_GET['contactid']) {
						$contactid = $_GET['contactid'];
						$staff_category_result = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT con_classifications FROM contacts where contactid=$contactid"));
					}

					$category = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT con_classifications FROM field_config_contacts where con_classifications is not null"));
					$con_classifications = explode(",", $category['con_classifications']);
					foreach($con_classifications as $con_classification) {
						$selected = '';
						if ($_GET['contactid'] && $con_classification == $staff_category_result['con_classifications']) {
								$selected = 'selected="selected"';
						}

						echo "<option ".$selected." value='". $con_classification."'>".$con_classification.'</option>';
					}
					//echo "<option value = 'Other'>New Category</option>";
				  ?>
				</select>
			</div>
	  </div>

	  <div class="form-group">
			<label for="locations[]" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Locations:</label>
			<div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				<select data-placeholder="Choose a Locations..." class='form-control <?php echo $required; ?> chosen-select-deselect <?php echo $required; ?>' id="con_locations" name="con_locations" width="380">
				  <option value=""></option>
				  <?php
					if($_GET['contactid']) {
						$contactid = $_GET['contactid'];
						$staff_category_result = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT con_locations FROM contacts where contactid=$contactid"));
					}

					$category = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `con_locations` SEPARATOR ',') FROM `field_config_contacts`"))[0])));
					$con_locations = explode(",", $category);
					foreach($con_locations as $con_location) {
						$selected = '';
						if ($_GET['contactid'] && $con_location == $staff_category_result['con_locations']) {
								$selected = 'selected="selected"';
						}

						echo "<option ".$selected." value='". $con_location."'>".$con_location.'</option>';
					}
					//echo "<option value = 'Other'>New Category</option>";
				  ?>
				</select>
			</div>
	  </div>

	  <div class="form-group">
			<label for="title[]" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Title:</label>
			<div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				<select data-placeholder="Choose a Category..." class='form-control <?php echo $required; ?> chosen-select-deselect <?php echo $required; ?>' id="con_title" name="con_title" width="380">
				  <option value=""></option>
				  <?php
					if($_GET['contactid']) {
						$contactid = $_GET['contactid'];
						$staff_category_result = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT con_title FROM contacts where contactid=$contactid"));
					}

					$category = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT con_title FROM field_config_contacts where con_title is not null"));
					$con_title = explode(",", $category['con_title']);
					foreach($con_title as $con_titl) {
						$selected = '';
						if ($_GET['contactid'] && $con_titl == $staff_category_result['con_title']) {
								$selected = 'selected="selected"';
						}

						echo "<option ".$selected." value='". $con_titl."'>".$con_titl.'</option>';
					}
					//echo "<option value = 'Other'>New Category</option>";
				  ?>
				</select>
			</div>
	  </div>
		<?php } ?>
		<?php $temp_field = 'First Name'; ?><?php if (strpos($value_config, ','."First Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input first name."><img src="../img/info.png" width="20"></a></span>
                First Name:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."First Name".',') === false ? 'readonly' : ''); ?> name="first_name" value="<?php echo $first_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Last Name'; ?><?php if (strpos($value_config, ','."Last Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input last name."><img src="../img/info.png" width="20"></a></span>
                Last Name:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Last Name".',') === false ? 'readonly' : ''); ?> name="last_name" value="<?php echo $last_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Preferred Name'; ?><?php if (strpos($value_config, ','."Preferred Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="prefer_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input preferred name (if different from actual name)."><img src="../img/info.png" width="20"></a></span>
                Preferred Name:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Preferred Name".',') === false ? 'readonly' : ''); ?> name="prefer_name" value="<?php echo $prefer_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Preferred Pronoun'; ?><?php if (strpos($value_config, ','."Preferred Pronoun".',') !== FALSE) { ?>
          <div class="form-group">
            <label for="preferred_pronoun" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Choose preferred pronoun from the dropdown."><img src="../img/info.png" width="20"></a></span>
                Preferred Pronoun:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <select data-placeholder="Choose a Pronoun..." name="preferred_pronoun" class="form-control <?php echo $required; ?> chosen-select-deselect <?php echo $required; ?>">
                <option value=""></option>
                <option <?php echo $preferred_pronoun == 1 ? 'selected' : ''; ?> value="1">She/Her</option>
                <option <?php echo $preferred_pronoun == 2 ? 'selected' : ''; ?> value="2">He/Him</option>
                <option <?php echo $preferred_pronoun == 3 ? 'selected' : ''; ?> value="3">They/Them</option>
                <option <?php echo $preferred_pronoun == 4 ? 'selected' : ''; ?> value="4">Just use my name</option>
              </select>
            </div>
          </div>
        <?php } ?>

      <?php $temp_field = 'Nick Name'; ?><?php if (strpos($value_config, ','."Nick Name".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Nick Name:</label>
        <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
          <input name="nick_name" value="<?php echo $nick_name; ?>" type="text" class="form-control <?php echo $required; ?>">
        </div>
      </div>
      <?php } ?>

      <?php $temp_field = 'Gender'; ?><?php if (strpos($value_config, ','."Gender".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="office_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Gender:</label>
        <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
          <input <?php echo (strpos($edit_config, ','."Gender".',') === false ? 'onclick="return false;"' : ''); ?> <?php if ($gender == 'Male') { echo " checked"; } ?> name="gender" type="radio" value="Male" class="form" /> Male
          <input <?php echo (strpos($edit_config, ','."Gender".',') === false ? 'onclick="return false;"' : ''); ?> <?php if ($gender == 'Female') { echo " checked"; } ?> name="gender" type="radio" value="Female" class="form" /> Female
        </div>
      </div>
      <?php } ?>

      <?php $temp_field = 'License'; ?><?php if (strpos($value_config, ','."License".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>License#:</label>
        <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
          <input name="license" value="<?php echo $license; ?>" type="text" class="form-control <?php echo $required; ?>">
        </div>
      </div>
      <?php } ?>

      <?php $temp_field = 'Insurer'; ?><?php if (strpos($value_config, ','."Insurer".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="position[]" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Insurer:</label>
        <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
		<?php if(strpos($edit_config, ',Insurer,') !== FALSE) { ?>
            <select data-placeholder="Choose a Insurer..." name="name" width="380" class="chosen-select-deselect <?php echo $required; ?>">
              <option value=""></option>
              <?php
                $result_insurers = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT `contactid`, `name` FROM contacts WHERE category='Insurer' AND deleted=0"),MYSQLI_ASSOC));
				foreach($result_insurers as $insurer_id) {
					$insurer_name = get_client($dbc, $insurer_id);
                    echo "<option ".($name == $insurer_name ? 'selected' : '')." value='". $insurer_name."'>".$insurer_name.'</option>';
                }
              ?>
            </select>
		<?php } else { ?>
			<input type="text" readonly name="name" value="<?= $name ?>" class="form-control <?php echo $required; ?>">
		<?php } ?>
        </div>
      </div>
      <?php } ?>

		<?php $temp_field = 'Assigned Staff'; ?><?php if (strpos($value_config, ','."Assigned Staff".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Assigned Staff:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				<?php if(strpos($edit_config, ',Assigned Staff,') !== FALSE) { ?>
				<select name="assign_staff" class="form-control <?php echo $required; ?> chosen-select-deselect <?php echo $required; ?>"><option></option>
					<?php $result_staff = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT `contactid`, `first_name`, `last_name` FROM contacts WHERE category='Staff' AND deleted=0"),MYSQLI_ASSOC));
					foreach($result_staff as $resultstaffid) {
						echo "<option ".($assign_staff == $resultstaffid ? 'selected' : '')." value='". $resultstaffid."'>".get_contact($dbc, $resultstaffid).'</option>';
					} ?>
				</select>
				<?php } else { ?>
					<input type="hidden" name="assign_staff" value="<?= $assign_staff ?>">
					<input type="text" readonly name="assign_staff_label" value="<?= get_contact($dbc, $assign_staff) ?>" class="form-control <?php echo $required; ?>">
				<?php } ?>
            </div>
            </div>
        <?php } ?>

      <?php $temp_field = 'Credential'; ?><?php if (strpos($value_config, ','."Credential".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Credentials:</label>
        <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
            <input name="credential" type="text" maxlength="50" value="<?php echo $credential; ?>" class="form-control <?php echo $required; ?> last-name" />
        </div>
      </div>
      <?php } ?>

      <?php $temp_field = 'Alberta Health Care No'; ?><?php if (strpos($value_config, ','."Alberta Health Care No".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Alberta Health Care No:</label>
        <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
          <input name="health_care_no" value="<?php echo $health_care_no; ?>" type="text" class="form-control <?php echo $required; ?>">
        </div>
      </div>
      <?php } ?>

   <?php $temp_field = 'Scheduled Days/Hours'; ?><?php if (strpos($value_config, ','."Scheduled Days/Hours".',') !== FALSE && !empty($_GET['contactid'])) { ?>
  <div class="form-group">
    <label for="office_country" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Scheduled Days/Hours:</label>
    <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
      <input name="schedule_days[]" id='day_0' onclick="selectHour(this);" type="checkbox" value="0" <?php if (strpos($schedule_days, '0') !== false) { echo  'checked="checked"'; } ?> class="form-control <?php echo $required; ?>"  style="display:inline-block; width:auto; height: auto;"/> <span style="display:inline-block;">Sunday&nbsp;</span>&nbsp;&nbsp;
      <input name="scheduled_hours[]" id='hour_0' placeholder="Ex. 9AM-5PM, 3PM-5PM" type="text" value="<?php echo $scheduled_hours[0]; ?>" class="form-control <?php echo $required; ?>1"/><br>
      <input name="schedule_days[]" id='day_1' onclick="selectHour(this);" type="checkbox" value="1" <?php if (strpos($schedule_days, '1') !== false) { echo  'checked="checked"'; } ?> class="form-control <?php echo $required; ?>"  style="display:inline-block; width:auto; height: auto;"/> <span style="display:inline-block;">Monday&nbsp;</span>&nbsp;&nbsp;
      <input name="scheduled_hours[]" id='hour_1' placeholder="Ex. 9AM-5PM, 3PM-5PM" type="text" value="<?php echo $scheduled_hours[1]; ?>" class="form-control <?php echo $required; ?>1"/><br>
      <input name="schedule_days[]" id='day_2' onclick="selectHour(this);" type="checkbox" value="2" <?php if (strpos($schedule_days, '2') !== false) { echo  'checked'; } ?> class="form-control <?php echo $required; ?>" style="display:inline-block; width:auto; height: auto;"/> <span style="display:inline-block;">Tuesday&nbsp;</span>&nbsp;&nbsp;
      <input name="scheduled_hours[]" id='hour_2' placeholder="Ex. 9AM-5PM, 3PM-5PM" type="text" value="<?php echo $scheduled_hours[2]; ?>" class="form-control <?php echo $required; ?>1"/><br>
      <input name="schedule_days[]" id='day_3' onclick="selectHour(this);" type="checkbox" value="3" <?php if (strpos($schedule_days, '3') !== false) { echo  'checked'; } ?> class="form-control <?php echo $required; ?>" style="display:inline-block; width:auto; height: auto;"/> <span style="display:inline-block;">Wednesday&nbsp;</span>&nbsp;&nbsp;
      <input name="scheduled_hours[]" id='hour_3' placeholder="Ex. 9AM-5PM, 3PM-5PM" type="text" value="<?php echo $scheduled_hours[3]; ?>" class="form-control <?php echo $required; ?>1"/><br>
      <input name="schedule_days[]" id='day_4' onclick="selectHour(this);" type="checkbox" value="4" <?php if (strpos($schedule_days, '4') !== false) { echo  'checked'; } ?> class="form-control <?php echo $required; ?>" style="display:inline-block; width:auto; height: auto;"/> <span style="display:inline-block;">Thursday&nbsp;</span>&nbsp;&nbsp;
      <input name="scheduled_hours[]" id='hour_4' placeholder="Ex. 9AM-5PM, 3PM-5PM" type="text" value="<?php echo $scheduled_hours[4]; ?>" class="form-control <?php echo $required; ?>1"/><br>
      <input name="schedule_days[]" id='day_5' onclick="selectHour(this);" type="checkbox" value="5" class="form-control <?php echo $required; ?>" <?php if (strpos($schedule_days, '5') !== false) { echo  'checked'; } ?> style="display:inline-block; width:auto; height: auto;"/> <span style="display:inline-block;">Friday&nbsp;</span>&nbsp;&nbsp;
      <input name="scheduled_hours[]" id='hour_5' placeholder="Ex. 9AM-5PM, 3PM-5PM" type="text" value="<?php echo $scheduled_hours[5]; ?>" class="form-control <?php echo $required; ?>1"/><br>
      <input name="schedule_days[]" id='day_6' onclick="selectHour(this);" type="checkbox" value="6" <?php if (strpos($schedule_days, '6') !== false) { echo  'checked'; } ?> class="form-control <?php echo $required; ?>"  style="display:inline-block; width:auto; height: auto;"/> <span style="display:inline-block;">Saturday&nbsp;</span>&nbsp;&nbsp;
      <input name="scheduled_hours[]" id='hour_6' placeholder="Ex. 9AM-5PM, 3PM-5PM" type="text" value="<?php echo $scheduled_hours[6]; ?>" class="form-control <?php echo $required; ?>1"/>
    </div>
  </div>
  <?php } ?>

    <?php $temp_field = 'Profile Link'; ?><?php if (strpos($value_config, ','."Profile Link".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Profile Link:</label>
    <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
      <input name="profile_link" value="<?php echo $profile_link; ?>" type="text" class="form-control <?php echo $required; ?>">
    </div>
  </div>
  <?php } ?>

      <?php $temp_field = 'Correspondence Language'; ?><?php if (strpos($value_config, ','."Correspondence Language".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Correspondence Language:</label>
        <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
          <input name="correspondence_language" value="<?php echo $correspondence_language; ?>" type="text" class="form-control <?php echo $required; ?>">
        </div>
      </div>
      <?php } ?>

       <?php $temp_field = 'Accepts to receive emails'; ?><?php if (strpos($value_config, ','."Accepts to receive emails".',') !== FALSE) { ?>
        <div class="form-group">
            <label for="phone_number" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Agrees to receive emails:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				<?php if(strpos($edit_config, ',Accepts to receive emails,') !== FALSE) { ?>
                <select data-placeholder="Choose a Option..."  name="accepts_receive_emails" id="equipmentid" width="380" class="chosen-select-deselect <?php echo $required; ?>">
                    <option value=""></option>
                    <option <?php if ($accepts_receive_emails == "Yes") { echo " selected"; } ?> value="Yes">Yes</option>
                    <option <?php if ($accepts_receive_emails == "No") { echo " selected"; } ?> value="No">No</option>
                </select>
				<?php } else { ?>
					<input type="text" readonly name="accepts_receive_emails" value="<?= $accepts_receive_emails ?>" class="form-control <?php echo $required; ?>">
				<?php } ?>
            </div>
        </div>

        <?php } ?>

		<?php $temp_field = 'Role'; ?><?php if (strpos($value_config, ','."Role".',') !== FALSE) { ?>
			<?php if(strpos(','.$role.',',',super,') !== false): ?>
				<input type="hidden" name="role[]" value="<?php echo $role; ?>">
			<?php else: ?>
				<div class="form-group">
					<label for="travel_task" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                        <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Select the staff security level from the dropdown."><img src="../img/info.png" width="20"></a></span>
                        Select a Security Level:
                    </label>
					<div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
					<?php if(strpos($edit_config, ',Role,') !== FALSE) { ?>
					<select name="role[]" multiple data-placeholder="Select a Security Level" width="380" class="chosen-select-deselect <?php echo $required; ?>">
						<?php $on_security = get_security_levels($dbc);
						foreach($on_security as $category => $value)  {
							$select_value = get_securitylevel($dbc, $value);
							if(strpos(','.$role.',',','.$value.',') !== false) {
								$selected = ' selected';
							} else {
								$selected = '';
							}
						  ?>
						  <option <?php echo $selected; ?> value="<?php echo $value; ?>"><?php echo $select_value; ?></option>
						<?php } ?>
					</select>
					<?php } else { ?>
						<?php foreach(array_filter(array_unique(explode(',',$role))) as $role_level) { ?>
							<input type="hidden" name="role[]" value="<?= $role_level ?>">
							<input type="text" readonly name="bussinessid_label" value="<?= get_securitylevel($dbc, $role_level) ?>" class="form-control <?php echo $required; ?>">
						<?php } ?>
					<?php } ?>
				  </div>
				</div>
			<?php endif; ?>
        <?php } ?>

    <?php $temp_field = 'Region'; ?><?php if (strpos($value_config, ','."Region".',') !== FALSE) { ?>
            <div class="form-group">
                <label for="fax_number" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                    <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Choose your region from the dropdown."><img src="../img/info.png" width="20"></a></span>
                    Region:
                </label>
                <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
          <?php if(strpos($edit_config, ',Region,') !== FALSE) { ?>
                    <select data-placeholder="Choose a region..." name="region" width="380" class="chosen-select-deselect <?php echo $required; ?>">
                      <option value=""></option>
                      <?php
						if(FOLDER_NAME == 'staff') {
							$each_tab = array_unique(explode(',',mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0]));
						} else {
							$each_tab = explode(',',get_config($dbc, FOLDER_NAME.'_region'));
						}
                        foreach ($each_tab as $cat_tab) {
                            if ($region == $cat_tab) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
                        }
                      ?>
                    </select>
          <?php } else { ?>
            <input type="text" readonly name="region" value="<?= $region ?>" class="form-control <?php echo $required; ?>">
          <?php } ?>
                </div>
            </div>

        <?php } ?>

		<?php $temp_field = 'Division'; ?><?php if (strpos($value_config, ','."Division".',') !== FALSE) { ?>
            <div class="form-group">
                <label for="fax_number"	class="col-sm-4	control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                    <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Choose your division from the dropdown."><img src="../img/info.png" width="20"></a></span>
                    Division:
                </label>
                <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
					<?php if(strpos($edit_config, ',Division,') !== FALSE) { ?>
                    <select data-placeholder="Choose a classification..." name="classification" width="380" class="chosen-select-deselect <?php echo $required; ?>">
                      <option value=""></option>
                      <?php
                        $tabs = get_config($dbc, FOLDER_NAME.'_classification');
                        $each_tab = explode(',', $tabs);
                        foreach ($each_tab as $cat_tab) {
                            if ($classification == $cat_tab) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
                        }
                      ?>
                    </select>
					<?php } else { ?>
						<input type="text" readonly name="classification" value="<?= $classification ?>" class="form-control <?php echo $required; ?>">
					<?php } ?>
                </div>
            </div>

        <?php } ?>

		<?php $temp_field = 'User Name'; ?><?php if (strpos($value_config, ','."User Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input username."><img src="../img/info.png" width="20"></a></span>
                Username:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."User Name".',') === false ? 'readonly' : ''); ?> name="user_name" value="<?php echo $user_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Password'; ?><?php if (strpos($value_config, ','."Password".',') !== FALSE) { ?>
			<input type="text" name="nosubmitusernamefield" value="" style="display:none;"><input type="password" name="nosubmitpasswordfield" value="" style="display:none;">
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input password."><img src="../img/info.png" width="20"></a></span>
                Password:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Password".',') === false ? 'readonly' : ''); ?> name="password" value="<?php echo $password; ?>" type="password" class="form-control <?php echo $required; ?>" autocomplete="new-password">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Name on Account'; ?><?php if (strpos($value_config, ','."Name on Account".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Name on Account:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Name on Account".',') === false ? 'readonly' : ''); ?> name="name_on_account" value="<?php echo $name_on_account; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Operating As'; ?><?php if (strpos($value_config, ','."Operating As".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Operating As:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Operating As".',') === false ? 'readonly' : ''); ?> name="operating_as" value="<?php echo $operating_as; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Rating'; ?><?php if (strpos($value_config, ','."Rating".',') !== FALSE) { ?>
            <div class="form-group">
                <label for="fax_number"	class="col-sm-4	control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Rating:</label>
                <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				<?php if(strpos($edit_config, ',Rating,') !== FALSE) { ?>
                    <select data-placeholder="Choose a Rating..." name="rating" width="380" class="chosen-select-deselect <?php echo $required; ?>">
                      <option value=""></option>
                      <?php
                        $selected = '';
                        if ($rating == 'Bronze') {
                            $selected = 'selected="selected"';
                        }
                        if (strpos($value_config, ','."Bronze Rating".',') !== FALSE) {
                            echo "<option style='background-color:#9B886C' ".$selected." value='Bronze'>Bronze</option>";
                        }
                        $selected = '';
                        if ($rating == 'Silver') {
                            $selected = 'selected="selected"';
                        }
                        if (strpos($value_config, ','."Silver Rating".',') !== FALSE) {
                            echo "<option style='background-color:silver' ".$selected." value='Silver'>Silver</option>";
                        }
                        $selected = '';
                        if ($rating == 'Gold') {
                            $selected = 'selected="selected"';
                        }
                        if (strpos($value_config, ','."Gold Rating".',') !== FALSE) {
                            echo "<option style='background-color:#D1B85F' ".$selected." value='Gold'>Gold</option>";
                        }
                        $selected = '';
                        if ($rating == 'Platinum') {
                            $selected = 'selected="selected"';
                        }
                        if (strpos($value_config, ','."Platinum Rating".',') !== FALSE) {
                            echo "<option style='background-color:#ABA9AC' ".$selected." value='Platinum'>Platinum</option>";
                        }
                        $selected = '';
                        if ($rating == 'Diamond') {
                            $selected = 'selected="selected"';
                        }
                        if (strpos($value_config, ','."Diamond Rating".',') !== FALSE) {
                            echo "<option style='background-color:#b9f2ff' ".$selected." value='Diamond'>Diamond</option>";
                        }
                        $selected = '';
                        if ($rating == 'Green') {
                            $selected = 'selected="selected"';
                        }
                        if (strpos($value_config, ','."Green Rating".',') !== FALSE) {
                            echo "<option style='background-color:#228B22' ".$selected." value='Green'>Green</option>";
                        }
                        $selected = '';
                        if ($rating == 'Yellow') {
                            $selected = 'selected="selected"';
                        }
                        if (strpos($value_config, ','."Yellow Rating".',') !== FALSE) {
                            echo "<option style='background-color:#ffff00' ".$selected." value='Yellow'>Yellow</option>";
                        }
                        $selected = '';
                        if ($rating == 'Light blue') {
                            $selected = 'selected="selected"';
                        }
                        if (strpos($value_config, ','."Light blue Rating".',') !== FALSE) {
                            echo "<option style='background-color:#ADD8E6' ".$selected." value='Light blue'>Light blue</option>";
                        }
                        $selected = '';
                        if ($rating == 'Dark blue') {
                            $selected = 'selected="selected"';
                        }
                        if (strpos($value_config, ','."Dark blue Rating".',') !== FALSE) {
                            echo "<option style='background-color:#1E90FF' ".$selected." value='Dark blue'>Dark blue</option>";
                        }
                        $selected = '';
                        if ($rating == 'Red') {
                            $selected = 'selected="selected"';
                        }
                        if (strpos($value_config, ','."Red Rating".',') !== FALSE) {
                            echo "<option style='background-color:#ff0000' ".$selected." value='Red'>Red</option>";
                        }
                        $selected = '';
                        if ($rating == 'Pink') {
                            $selected = 'selected="selected"';
                        }
                        if (strpos($value_config, ','."Pink Rating".',') !== FALSE) {
                            echo "<option style='background-color:#FF69B4' ".$selected." value='Pink'>Pink</option>";
                        }
                        $selected = '';
                        if ($rating == 'Purple') {
                            $selected = 'selected="selected"';
                        }
                        if (strpos($value_config, ','."Purple Rating".',') !== FALSE) {
                            echo "<option style='background-color:#BF00FE' ".$selected." value='Purple'>Purple</option>";
                        }
                      ?>
                    </select>
				<?php } else { ?>
					<input type="text" readonly name="rating" value="<?= $rating ?>" class="form-control <?php echo $required; ?>">
				<?php } ?>
                </div>
            </div>

        <?php } ?>

		<?php $temp_field = 'Emergency Contact'; ?><?php if (strpos($value_config, ','."Emergency Contact".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Emergency Contact:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Emergency Contact".',') === false ? 'readonly' : ''); ?> name="emergency_contact" value="<?php echo $emergency_contact; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Occupation'; ?><?php if (strpos($value_config, ','."Occupation".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Occupation:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Occupation".',') === false ? 'readonly' : ''); ?> name="occupation" value="<?php echo $occupation; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Office Phone'; ?><?php if (strpos($value_config, ','."Office Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input office phone # in this format - 403.123.4567"><img src="../img/info.png" width="20"></a></span>
                Office Phone:<br><em>(Check box if contact is primary)</em>
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="checkbox" value="O" <?php if (strpos($primary_contact, 'O') !== FALSE) { echo " checked"; } ?> <?php echo (strpos($edit_config, ','."Office Phone".',') === false ? 'onclick="return false;"' : ''); ?> name="primary_contact[]"><input <?php echo (strpos($edit_config, ','."Office Phone".',') === false ? 'readonly' : ''); ?> name="office_phone" value="<?php echo $office_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Cell Phone'; ?><?php if (strpos($value_config, ','."Cell Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input cell phone # in this format - 403.123.4567"><img src="../img/info.png" width="20"></a></span>
                Cell Phone:<br><em>(Check box if contact is primary)</em>
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="checkbox" value="C" <?php if (strpos($primary_contact, 'C') !== FALSE) { echo " checked"; } ?> <?php echo (strpos($edit_config, ','."Cell Phone".',') === false ? 'onclick="return false;"' : ''); ?> name="primary_contact[]"><input <?php echo (strpos($edit_config, ','."Cell Phone".',') === false ? 'readonly' : ''); ?> name="cell_phone" value="<?php echo $cell_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Home Phone'; ?><?php if (strpos($value_config, ','."Home Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input home phone # in this format - 403.123.4567"><img src="../img/info.png" width="20"></a></span>
                Home Phone:<br><em>(Check box if contact is primary)</em>
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="checkbox" value="H" <?php if (strpos($primary_contact, 'H') !== FALSE) { echo " checked"; } ?> <?php echo (strpos($edit_config, ','."Home Phone".',') === false ? 'onclick="return false;"' : ''); ?> name="primary_contact[]"><input <?php echo (strpos($edit_config, ','."Home Phone".',') === false ? 'readonly' : ''); ?> name="home_phone" value="<?php echo $home_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Fax'; ?><?php if (strpos($value_config, ','."Fax".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Fax:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Fax".',') === false ? 'readonly' : ''); ?> name="fax" value="<?php echo $fax; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Email Address'; ?><?php if (strpos($value_config, ','."Email Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input email address (personal)."><img src="../img/info.png" width="20"></a></span>
                Email Address:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Email Address".',') === false ? 'readonly' : ''); ?> name="email_address" value="<?php echo $email_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Company Email Address'; ?><?php if (strpos($value_config, ','."Company Email Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="office_email" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input company email address."><img src="../img/info.png" width="20"></a></span>
                Company Email Address:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Company Email Address".',') === false ? 'readonly' : ''); ?> name="office_email" value="<?php echo $office_email; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Website'; ?><?php if (strpos($value_config, ','."Website".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Copy and paste URL from company website."><img src="../img/info.png" width="20"></a></span>
                Website:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Website".',') === false ? 'readonly' : ''); ?> name="website" value="<?php echo $website; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Display Name'; ?><?php if (strpos($value_config, ','."Display Name".',') !== FALSE) { ?>
          <div class="form-group">
            <label for="site_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Display Name:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input name="display_name" type="text" value="<?php echo $display_name; ?>" class="form-control <?php echo $required; ?>" />
            </div>
          </div>
        <?php } ?>

		<?php $temp_field = 'Customer Address'; ?><?php if (strpos($value_config, ','."Customer Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Customer Address:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Customer Address".',') === false ? 'readonly' : ''); ?> name="customer_address" value="<?php echo $customer_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Referred By'; ?><?php if (strpos($value_config, ','."Referred By".',') !== FALSE) { ?>
            <div class="form-group">
                <label for="phone_number" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Referred By:</label>
                <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				<?php if(strpos($edit_config, ',Referred By,') !== FALSE) { ?>
                    <select data-placeholder="Choose a Referred By..."  name="referred_by" id="equipmentid" width="380" class="chosen-select-deselect <?php echo $required; ?>">
                        <option value=""></option>
                        <option <?php if ($referred_by == "Referral") { echo " selected"; } ?> value="Referral">Referral</option>
                        <option <?php if ($referred_by == "Doctor") { echo " selected"; } ?> value="Doctor">Doctor</option>
                        <option <?php if ($referred_by == "Friend") { echo " selected"; } ?> value="Friend">Friend</option>
                        <option <?php if ($referred_by == "Patient") { echo " selected"; } ?> value="Patient">Patient</option>
                        <option <?php if ($referred_by == "Insurance") { echo " selected"; } ?> value="Insurance">Insurance</option>
                        <option <?php if ($referred_by == "Staff") { echo " selected"; } ?> value="Staff">Staff</option>
                        <option <?php if ($referred_by == "Business Lead") { echo " selected"; } ?> value="Business Lead">Business Lead</option>
                        <option <?php if ($referred_by == "Cold Call") { echo " selected"; } ?> value="Cold Call">Cold Call</option>
                        <option <?php if ($referred_by == "Tradeshow") { echo " selected"; } ?> value="Tradeshow">Tradeshow</option>
                        <option <?php if ($referred_by == "Website") { echo " selected"; } ?> value="Website">Website</option>
                        <option <?php if ($referred_by == "Social Media") { echo " selected"; } ?> value="Social Media">Social Media</option>
                        <option <?php if ($referred_by == "Print Media") { echo " selected"; } ?> value="Print Media">Print Media</option>
                        <option <?php if ($referred_by == "Radio") { echo " selected"; } ?> value="Radio">Radio</option>
                        <option <?php if ($referred_by == "Online") { echo " selected"; } ?> value="Online">Online</option>
                        <option <?php if ($referred_by == "Mail Out") { echo " selected"; } ?> value="Mail Out">Mail Out</option>
                        <option <?php if ($referred_by == "NP - Non-specific") { echo " selected"; } ?> value="NP - Non-specific">NP - Non-specific</option>
                        <option <?php if ($referred_by == "NP - Specific") { echo " selected"; } ?> value="NP - Specific">NP - Specific</option>
                        <option <?php if ($referred_by == "RP - Non-Specific") { echo " selected"; } ?> value="RP - Non-Specific">RP - Non-Specific</option>
                        <option <?php if ($referred_by == "RP - Specific") { echo " selected"; } ?> value="RP - Specific">RP - Specific</option>
                    </select>
				<?php } else { ?>
					<input type="text" readonly name="referred_by" value="<?= $referred_by ?>" class="form-control <?php echo $required; ?>">
				<?php } ?>
                </div>
            </div>

            <div class="form-group clearfix orientation_date">
                <label for="first_name" class="col-sm-4 control-label text-right">Referred By Name:</label>
                <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
                    <input name="referred_by_name" type="text" class="form-control <?php echo $required; ?>" value="<?php echo $referred_by_name; ?>"></p>
                </div>
            </div>


        <?php } ?>

		<?php $temp_field = 'Company'; ?><?php if (strpos($value_config, ','."Company".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Company:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Company".',') === false ? 'readonly' : ''); ?> name="company" value="<?php echo $company; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Position'; ?><?php if (strpos($value_config, ','."Position".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input <?= strtolower(FOLDER_NAME) ?> position."><img src="../img/info.png" width="20"></a></span>
                Position:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				<?php if(strpos($edit_config, ',Position,') === false) { ?>
					<input readonly name="position" value="<?php echo $position; ?>" type="text" class="form-control <?php echo $required; ?>">
				<?php } else { ?>
					<select name="position" class="chosen-select-deselect <?php echo $required; ?>"><option />
						<?php $position_list = $dbc->query("SELECT `position_id`, `name` FROM `positions` WHERE `deleted`=0 ORDER BY `name`");
						while($pos_row = $position_list->fetch_assoc()) { ?>
							<option <?= $position == $pos_row['position_id'] || $position == $pos_row['name'] ? 'selected' : '' ?> value="<?= $pos_row['position_id'] ?>"><?= $pos_row['name'] ?></option>
						<?php } ?>
					</select>
				<?php } ?>
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Positions Allowed'; ?><?php if (strpos($value_config, ','."Positions Allowed".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Allowed Positions:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				<?php $positions_allowed = explode(',',$positions_allowed);
				if(strpos($edit_config, ',Positions Allowed,') === false) { ?>
					<?php foreach($positions_allowed as $pos_allow) {
						echo get_field_value('name', 'positions', 'position_id', $pos_allow)."<br />";
					} ?>
				<?php } else { ?>
					<?php $position_list = $dbc->query("SELECT `position_id`, `name`, `description` FROM `positions` WHERE `deleted`=0");
					while($position_row = $position_list->fetch_assoc()) { ?>
						<label class="form-checkbox no-toggle" title="<?= $position_row['description'] ?>"><input name="positions_allowed[]" type="checkbox" <?= in_array($position_row['position_id'],$positions_allowed) || count(array_filter($positions_allowed)) == 0 ? 'checked' : '' ?> value="<?= $position_row['position_id'] ?>"><?= $position_row['name'] ?></label>
					<?php } ?>
				<?php } ?>
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Title'; ?><?php if (strpos($value_config, ','."Title".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Title:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Title".',') === false ? 'readonly' : ''); ?> name="title" value="<?php echo $title; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Show/Hide User'; ?><?php if (strpos($value_config, ','."Show/Hide User".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="show_hide_user" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Choose whether the user is shown or hidden."><img src="../img/info.png" width="20"></a></span>
                Show/Hide User:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Show/Hide User".',') === FALSE ? 'onclick="return false;"' : ''); ?>  name="show_hide_user" <?php echo '1' == $show_hide_user ? "checked " : "checked "; ?> value="1" type="radio"> Show
              <input <?php echo (strpos($edit_config, ','."Show/Hide User".',') === FALSE ? 'onclick="return false;"' : ''); ?>  name="show_hide_user" <?php echo '0' == $show_hide_user ? "checked " : ""; ?> value="0" type="radio"> Hide
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'LinkedIn'; ?><?php if (strpos($value_config, ','."LinkedIn".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Copy and paste URL from LinkedIn page."><img src="../img/info.png" width="20"></a></span>
                LinkedIn:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."LinkedIn".',') === false ? 'readonly' : ''); ?> name="linkedin" value="<?php echo $linkedin; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>
		<?php $temp_field = 'Facebook'; ?><?php if (strpos($value_config, ','."Facebook".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="facebook" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Copy and paste URL from Facebook page."><img src="../img/info.png" width="20"></a></span>
                Facebook:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Facebook".',') === false ? 'readonly' : ''); ?> name="facebook" value="<?php echo $facebook; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>
		<?php $temp_field = 'Twitter'; ?><?php if (strpos($value_config, ','."Twitter".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Copy and paste URL from Twitter page."><img src="../img/info.png" width="20"></a></span>
                Twitter:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Twitter".',') === false ? 'readonly' : ''); ?> name="twitter" value="<?php echo $twitter; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>
		<?php $temp_field = 'Instagram'; ?><?php if (strpos($value_config, ','."Instagram".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Copy and paste URL from Instagram page."><img src="../img/info.png" width="20"></a></span>
                Instagram:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Twitter".',') === false ? 'readonly' : ''); ?> name="instagram" value="<?php echo $instagram; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>
		<?php $temp_field = 'Pinterest'; ?><?php if (strpos($value_config, ','."Pinterest".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Copy and paste URL from Pinterest page."><img src="../img/info.png" width="20"></a></span>
                Pinterest:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Twitter".',') === false ? 'readonly' : ''); ?> name="pinterest" value="<?php echo $pinterest; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Client Tax Exemption'; ?><?php if (strpos($value_config, ','."Client Tax Exemption".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client Tax Exemption:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				<!--<input name="client_tax_exemption" value="<?php //echo $client_tax_exemption; ?>" type="text" class="form-control <?php echo $required; ?>">-->

				<?php if(strpos($edit_config, ',Client Tax Exemption,') !== FALSE) { ?>
                <select data-placeholder="Choose an Option..." name="client_tax_exemption" width="380" class="chosen-select-deselect <?php echo $required; ?>">
					<option value=""></option><?php
					if ( $client_tax_exemption == 'Yes' ) {
						$yes = 'selected="selected"';
						$no = '';
					} else {
						$yes = '';
						$no = 'selected="selected"';
					} ?>
					<option value="Yes" <?= $yes; ?>>Yes</option>
					<option value="No" <?= $no; ?>>No</option>
                </select>
				<?php } else { ?>
					<input type="text" readonly name="client_tax_exemption" value="<?= $client_tax_exemption ?>" class="form-control <?php echo $required; ?>">
				<?php } ?>

            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Tax Exemption Number'; ?><?php if (strpos($value_config, ','."Tax Exemption Number".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client Tax Exemption Number:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Tax Exemption Number".',') === false ? 'readonly' : ''); ?> name="tax_exemption_number" value="<?php echo $tax_exemption_number; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'DUNS'; ?><?php if (strpos($value_config, ','."DUNS".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>DUNS:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."DUNS".',') === false ? 'readonly' : ''); ?> name="duns" value="<?php echo $duns; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'CAGE'; ?><?php if (strpos($value_config, ','."CAGE".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>CAGE:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."CAGE".',') === false ? 'readonly' : ''); ?> name="cage" value="<?php echo $cage; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'SIN'; ?><?php if (strpos($value_config, ','."SIN".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="sin" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input SIN (Social Insurance Number), 9 digits."><img src="../img/info.png" width="20"></a></span>
                SIN:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."SIN".',') === false ? 'readonly' : ''); ?> name="sin" value="<?php echo $sin; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Employee Number'; ?><?php if (strpos($value_config, ','."Employee Number".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="employee_num" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Employee #:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Employee Number".',') === false ? 'readonly' : ''); ?> name="employee_num" value="<?php echo $employee_num; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Self Identification'; ?><?php if (strpos($value_config, ','."Self Identification".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Self Identification:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				<?php if(strpos($edit_config, ',Self Identification,') !== FALSE) { ?>
                <select data-placeholder="Choose an Option..." id="self_identification" name="self_identification" width="380" class="chosen-select-deselect <?php echo $required; ?>">
                  <option value=""></option>
                  <?php
                    $query = mysqli_query($dbc,"SELECT distinct(self_identification) FROM contacts ORDER BY self_identification");
                    while($row = mysqli_fetch_array($query)) {
                        if ($self_identification == $row['self_identification']) {
                            $selected = 'selected="selected"';
                        } else {
                            $selected = '';
                        }
                        echo "<option ".$selected." value='". $row['self_identification']."'>".$row['self_identification'].'</option>';
                    }
                    echo "<option value = 'Other'>New Self Identification</option>";
                  ?>
                </select>
				<?php } else { ?>
					<input type="text" readonly name="self_identification" value="<?= $self_identification ?>" class="form-control <?php echo $required; ?>">
				<?php } ?>
            </div>
            </div>

           <div class="form-group" id="new_self_identification" style="display: none;">
            <label for="travel_task" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
                <input <?php echo (strpos($edit_config, ','."Self Identification".',') === false ? 'readonly' : ''); ?> name="new_self_identification" type="text" class="form-control <?php echo $required; ?>"/>
            </div>
          </div>
        <?php } ?>

		<?php $temp_field = 'AISH Card#'; ?><?php if (strpos($value_config, ','."AISH Card#".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>AISH Card#:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."AISH Card#".',') === false ? 'readonly' : ''); ?> name="aish_card_no" value="<?php echo $aish_card_no; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'License Plate #'; ?><?php if (strpos($value_config, ','."License Plate #".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>License Plate #:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."License Plate #".',') === false ? 'readonly' : ''); ?> name="license_plate_no" value="<?php echo $license_plate_no; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'CARFAX'; ?><?php if (strpos($value_config, ','."CARFAX".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>CARFAX:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."CARFAX".',') === false ? 'readonly' : ''); ?> name="carfax" value="<?php echo $carfax; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Address'; ?><?php if (strpos($value_config, ','."Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="address" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Street Address:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Address".',') === false ? 'readonly' : ''); ?> name="address" value="<?php echo $address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Mailing Address'; ?><?php if (strpos($value_config, ','."Mailing Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input mailing address."><img src="../img/info.png" width="20"></a></span>
                Mailing Address:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Mailing Address".',') === false ? 'readonly' : ''); ?> name="mailing_address" value="<?php echo $mailing_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Business Address'; ?><?php if (strpos($value_config, ','."Business Address".',') !== FALSE) { ?>
            <!--
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Business Address:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Business Address".',') === false ? 'readonly' : ''); ?> name="business_address" value="<?php echo $business_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
            -->

          <div class="form-group">
            <label for="business_street" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Street:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input name="business_street" maxlength="200" type="text" value="<?php echo $business_street; ?>" id="office_street" class="form-control <?php echo $required; ?> office_street" />
            </div>
          </div>

          <div class="form-group">
            <label for="business_city" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>City:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input name="business_city" maxlength="15" id="office_city" value="<?php echo $business_city; ?>" type="text" class="form-control <?php echo $required; ?> office_city" />
            </div>
          </div>

          <div class="form-group">
            <label for="business_state" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>State / Province:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input name="business_state" maxlength="15" id="office_state" value="<?php echo $business_state; ?>" type="text" class="form-control <?php echo $required; ?> office_state" />
            </div>
          </div>

          <div class="form-group">
            <label for="" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Country:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input name="business_country" maxlength="15" id="office_country" value="<?php echo $business_country; ?>" type="text" class="form-control <?php echo $required; ?> office_country" />
            </div>
          </div>

          <div class="form-group">
            <label for="business_zip" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Zip / Postal Code:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input name="business_zip" maxlength="10" id="office_zip" value="<?php echo $business_zip; ?>" type="text" class="form-control <?php echo $required; ?> office_zip" />
            </div>
          </div>

        <?php } ?>

		<?php $temp_field = 'Zip Code'; ?><?php if (strpos($value_config, ','."Zip Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Zip/Postal Code:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Zip Code".',') === false ? 'readonly' : ''); ?> name="zip_code" value="<?php echo $zip_code; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'City'; ?><?php if (strpos($value_config, ','."City".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input city."><img src="../img/info.png" width="20"></a></span>
                City:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."City".',') === false ? 'readonly' : ''); ?> name="city" value="<?php echo $city; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Province'; ?><?php if (strpos($value_config, ','."Province".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input province."><img src="../img/info.png" width="20"></a></span>
                Province:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Province".',') === false ? 'readonly' : ''); ?> name="province" value="<?php echo $province; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'State'; ?><?php if (strpos($value_config, ','."State".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input state."><img src="../img/info.png" width="20"></a></span>
                State:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."State".',') === false ? 'readonly' : ''); ?> name="state" value="<?php echo $state; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Country'; ?><?php if (strpos($value_config, ','."Country".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input country."><img src="../img/info.png" width="20"></a></span>
                Country:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Country".',') === false ? 'readonly' : ''); ?> name="country" value="<?php echo $country; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

    <?php $temp_field = 'Postal Code'; ?><?php if (strpos($value_config, ','."Postal Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input postal code."><img src="../img/info.png" width="20"></a></span>
                Postal Code:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Postal Code".',') === false ? 'readonly' : ''); ?> name="postal_code" value="<?php echo $postal_code; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Ship To Address'; ?><?php if (strpos($value_config, ','."Ship To Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Ship To Address:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Ship To Address".',') === false ? 'readonly' : ''); ?> name="ship_to_address" value="<?php echo $ship_to_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Ship City'; ?><?php if (strpos($value_config, ','."Ship City".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Ship City:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Ship City".',') === false ? 'readonly' : ''); ?> name="ship_city" value="<?php echo $ship_city; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>
		<?php $temp_field = 'Ship State'; ?><?php if (strpos($value_config, ','."Ship State".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Ship State:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Ship State".',') === false ? 'readonly' : ''); ?> name="ship_state" value="<?php echo $ship_state; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>
		<?php $temp_field = 'Ship Zip'; ?><?php if (strpos($value_config, ','."Ship Zip".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Ship Zip:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Ship Zip".',') === false ? 'readonly' : ''); ?> name="ship_zip" value="<?php echo $ship_zip; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>
		<?php $temp_field = 'Ship Country'; ?><?php if (strpos($value_config, ','."Ship Country".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Ship Country:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Ship Country".',') === false ? 'readonly' : ''); ?> name="ship_country" value="<?php echo $ship_country; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Google Maps Address'; ?><?php if (strpos($value_config, ','."Google Maps Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Google Maps Address:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Google Maps Address".',') === false ? 'readonly' : ''); ?> name="google_maps_address" value="<?php echo $google_maps_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'City Part'; ?><?php if (strpos($value_config, ','."City Part".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>City Part:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."City Part".',') === false ? 'readonly' : ''); ?> name="city_part" value="<?php echo $city_part; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Account Number'; ?><?php if (strpos($value_config, ','."Account Number".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Account Number:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Account Number".',') === false ? 'readonly' : ''); ?> name="account_number" value="<?php echo $account_number; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Payment Type'; ?><?php if (strpos($value_config, ','."Payment Type".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Payment Type:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Payment Type".',') === false ? 'readonly' : ''); ?> name="payment_type" value="<?php echo $payment_type; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Payment Name'; ?><?php if (strpos($value_config, ','."Payment Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Payment Name:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Payment Name".',') === false ? 'readonly' : ''); ?> name="payment_name" value="<?php echo $payment_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Payment Address'; ?><?php if (strpos($value_config, ','."Payment Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Payment Address:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Payment Address".',') === false ? 'readonly' : ''); ?> name="payment_address" value="<?php echo $payment_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Payment City'; ?><?php if (strpos($value_config, ','."Payment City".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Payment City:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Payment City".',') === false ? 'readonly' : ''); ?> name="payment_city" value="<?php echo $payment_city; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Payment State'; ?><?php if (strpos($value_config, ','."Payment State".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Payment State:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Payment State".',') === false ? 'readonly' : ''); ?> name="payment_state" value="<?php echo $payment_state; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Payment Postal Code'; ?><?php if (strpos($value_config, ','."Payment Postal Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Payment Postal Code:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Payment Postal Code".',') === false ? 'readonly' : ''); ?> name="payment_postal_code" value="<?php echo $payment_postal_code; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Payment Zip Code'; ?><?php if (strpos($value_config, ','."Payment Zip Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Payment Zip/Postal Code:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Payment Zip Code".',') === false ? 'readonly' : ''); ?> name="payment_zip_code" value="<?php echo $payment_zip_code; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'GST #'; ?><?php if (strpos($value_config, ','."GST #".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>GST #:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."GST #".',') === false ? 'readonly' : ''); ?> name="gst_no" value="<?php echo $gst_no; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'PST #'; ?><?php if (strpos($value_config, ','."PST #".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>PST #:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."PST #".',') === false ? 'readonly' : ''); ?> name="pst_no" value="<?php echo $pst_no; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Vendor GST #'; ?><?php if (strpos($value_config, ','."Vendor GST #".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Vendor GST #:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Vendor GST #".',') === false ? 'readonly' : ''); ?> name="vendor_gst_no" value="<?php echo $vendor_gst_no; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Payment Information'; ?><?php if (strpos($value_config, ','."Payment Information".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Payment Information:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Payment Information".',') === false ? 'readonly' : ''); ?> name="payment_information" value="<?php echo $payment_information; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Pricing Level'; ?><?php if (strpos($value_config, ','."Pricing Level".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Pricing Level:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				<?php if(strpos($edit_config, ',Pricing Level,') !== FALSE) { ?>
                <select data-placeholder="Choose Pricing..." name="pricing_level" width="380" class="chosen-select-deselect <?php echo $required; ?>">
                    <option value="">Please Select</option>
                    <option <?php if ($pricing_level == 'Client Price') { echo " selected"; } ?> value="Client Price">Client Price</option>
                    <option <?php if ($pricing_level == 'Admin Price') { echo " selected"; } ?> value="Admin Price">Admin Price</option>
                    <option <?php if ($pricing_level == 'Commercial Price') { echo " selected"; } ?> value="Commercial Price">Commercial Price</option>
                    <option <?php if ($pricing_level == 'Wholesale Price') { echo " selected"; } ?> value="Wholesale Price">Wholesale Price</option>
                    <option <?php if ($pricing_level == 'Final Retail Price') { echo " selected"; } ?> value="Final Retail Price">Final Retail Price</option>
                    <option <?php if ($pricing_level == 'Preferred Price') { echo " selected"; } ?> value="Preferred Price">Preferred Price</option>
                    <option <?php if ($pricing_level == 'Web Price') { echo " selected"; } ?> value="Web Price">Web Price</option>
                </select>
				<?php } else { ?>
					<input type="text" readonly name="pricing_level" value="<?= $pricing_level ?>" class="form-control <?php echo $required; ?>">
				<?php } ?>
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Unit #'; ?><?php if (strpos($value_config, ','."Unit #".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Unit #:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Unit #".',') === false ? 'readonly' : ''); ?> name="unit_no" value="<?php echo $unit_no; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Bay #'; ?><?php if (strpos($value_config, ','."Bay #".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Bay #:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Bay #".',') === false ? 'readonly' : ''); ?> name="bay_no" value="<?php echo $bay_no; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Option to Renew'; ?><?php if (strpos($value_config, ','."Option to Renew".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Option to Renew:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Option to Renew".',') === false ? 'readonly' : ''); ?> name="option_to_renew" value="<?php echo $option_to_renew; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Lease Term - # of years'; ?><?php if (strpos($value_config, ','."Lease Term - # of years".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Lease Term - # of years:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Lease Term - # of years".',') === false ? 'readonly' : ''); ?> name="lease_term_no_of_years" value="<?php echo $lease_term_no_of_years; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Commercial Insurer'; ?><?php if (strpos($value_config, ','."Commercial Insurer".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Commercial Insurer:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Commercial Insurer".',') === false ? 'readonly' : ''); ?> name="commercial_insurer" value="<?php echo $commercial_insurer; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Residential Insurer'; ?><?php if (strpos($value_config, ','."Residential Insurer".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Residential Insurer:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Residential Insurer".',') === false ? 'readonly' : ''); ?> name="residential_insurer" value="<?php echo $residential_insurer; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'WCB #'; ?><?php if (strpos($value_config, ','."WCB #".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>WCB #:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."WCB #".',') === false ? 'readonly' : ''); ?> name="wcb_no" value="<?php echo $wcb_no; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Status'; ?><?php if (strpos($value_config, ','."Status".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="status" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Status:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
        <?php if($get_contact['category'] == 'Staff') {
			$staff_tabs = explode(',',get_config($dbc, 'staff_tabs')); ?>
			 <input type="radio" <?php echo (strpos($edit_config, ','."Status".',') === false ? 'onclick="return false;"' : ''); ?> name="status" value="1" <?php if($status==1){ echo 'checked'; } ?>> Active
			 <?php if(in_array('probation',$staff_tabs)) { ?><input type="radio" <?php echo (strpos($edit_config, ','."Status".',') === false ? 'onclick="return false;"' : ''); ?> name="status" value="2" <?php if($status==2){ echo 'checked'; } ?>> On Probation<?php } ?>
			 <?php if(in_array('suspended',$staff_tabs)) { ?><input type="radio" <?php echo (strpos($edit_config, ','."Status".',') === false ? 'onclick="return false;"' : ''); ?> name="status" value="0" <?php if($status==0){ echo 'checked'; } ?>> Suspended<?php } ?>
		<?php } else if (!isset($_GET['contactid'])) { ?>
            <input type="radio" <?php echo (strpos($edit_config, ','."Status".',') === false ? 'onclick="return false;"' : ''); ?> name="status" value="1" <?php if($status==1 || !isset($_GET['contactid'])){ echo 'checked'; } ?>> Active
            <input type="radio" <?php echo (strpos($edit_config, ','."Status".',') === false ? 'onclick="return false;"' : ''); ?> name="status" value="0" <?php if($status==0){ echo 'checked'; } ?>> Inactive
        <?php } else { ?>
			 <input type="radio" <?php echo (strpos($edit_config, ','."Status".',') === false ? 'onclick="return false;"' : ''); ?> name="status" value="1" <?php if($status==1){ echo 'checked'; } ?>> Active
			 <input type="radio" <?php echo (strpos($edit_config, ','."Status".',') === false ? 'onclick="return false;"' : ''); ?> name="status" value="0" <?php if($status==0){ echo 'checked'; } ?>> Inactive
        <?php } ?>
            </div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Credit Card on File'; ?><?php if (strpos($value_config, ',' . "Credit Card on File" . ',' ) !== FALSE ) { ?>
            <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Credit Card on File:</label>
				<div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				<?php if(strpos($edit_config, ',Credit Card on File,') !== FALSE) { ?>
					<select data-placeholder="Choose One..." name="cc_on_file" width="380" class="chosen-select-deselect <?php echo $required; ?>">
						<option <?php if ( $cc_on_file == 'Yes') { echo " selected"; } ?> value="Yes">Yes</option>
						<option <?php if ( $cc_on_file == 'No') { echo " selected"; } ?> value="No">No</option>
					</select>
				<?php } else { ?>
					<input type="hidden" name="businessid" value="<?= $businessid ?>">
					<input type="text" readonly name="cc_on_file" value="<?= $cc_on_file ?>" class="form-control <?php echo $required; ?>">
				<?php } ?>
				</div>
            </div>
        <?php } ?>

		<?php $temp_field = 'Intake Form'; ?><?php if (strpos($value_config, ',' . "Intake Form" . ',') !== FALSE ) { ?>
            <div class="form-group">
				<label for="intakeid" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Intake Form ID:</label>
				<div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
					<input class="form-control <?php echo $required; ?>" type="text" name="intakeid" value="<?php echo $intakeid; ?>" />
				</div>
            </div>
        <?php } ?>

        <?php // Client Information ?>
        <?php $temp_field = 'Client First Name'; ?><?php if (strpos($value_config, ','."Client First Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_first_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client First Name:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Client First Name".',') === false ? 'readonly' : ''); ?> name="client_first_name" value="<?php echo $client_first_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Client Last Name'; ?><?php if (strpos($value_config, ','."Client Last Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_last_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client Last Name:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Client Last Name".',') === false ? 'readonly' : ''); ?> name="client_last_name" value="<?php echo $client_last_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Client Work Phone'; ?><?php if (strpos($value_config, ','."Client Work Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_work_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client Work Phone:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Client Work Phone".',') === false ? 'readonly' : ''); ?> name="client_work_phone" value="<?php echo $client_work_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Client Home Phone'; ?><?php if (strpos($value_config, ','."Client Home Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_home_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client Home Phone:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Client Home Phone".',') === false ? 'readonly' : ''); ?> name="client_home_phone" value="<?php echo $client_home_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Client Cell Phone'; ?><?php if (strpos($value_config, ','."Client Cell Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_cell_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client Cell Phone:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Client Cell Phone".',') === false ? 'readonly' : ''); ?> name="client_cell_phone" value="<?php echo $client_cell_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Client Fax'; ?><?php if (strpos($value_config, ','."Client Fax".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_fax" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client Fax:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Client Fax".',') === false ? 'readonly' : ''); ?> name="client_fax" value="<?php echo $client_fax; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Client Email Address'; ?><?php if (strpos($value_config, ','."Client Email Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_email_address" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client Email Address:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Client Email Address".',') === false ? 'readonly' : ''); ?> name="client_email_address" value="<?php echo $client_email_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Client Date of Birth'; ?><?php if (strpos($value_config, ','."Client Date of Birth".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_date_of_birth" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client Date of Birth:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Client Date of Birth".',') === false ? 'readonly' : ''); ?> name="client_date_of_birth" value="<?php echo $client_date_of_birth; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Client Height'; ?><?php if (strpos($value_config, ','."Client Height".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_height" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client Height:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Client Height".',') === false ? 'readonly' : ''); ?> name="client_height" value="<?php echo $client_height; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Client Weight'; ?><?php if (strpos($value_config, ','."Client Weight".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_weight" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client Weight:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Client Weight".',') === false ? 'readonly' : ''); ?> name="client_weight" value="<?php echo $client_weight; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Client SIN'; ?><?php if (strpos($value_config, ','."Client SIN".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_sin" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client SIN:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Business".',') === false ? 'readonly' : ''); ?> name="client_sin" value="<?php echo $client_sin; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Client Client ID'; ?><?php if (strpos($value_config, ','."Client Client ID".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_client_id" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client Client ID:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Business".',') === false ? 'readonly' : ''); ?> name="client_client_id" value="<?php echo $client_client_id; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php // Client Address ?>

        <?php $temp_field = 'Client Address'; ?><?php if (strpos($value_config, ','."Client Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_address" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client Address:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Business".',') === false ? 'readonly' : ''); ?> name="client_address" value="<?php echo $client_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Client Zip Code'; ?><?php if (strpos($value_config, ','."Client Zip Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_zip_code" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client Postal/Zip Code:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Client Zip Code".',') === false ? 'readonly' : ''); ?> name="client_zip_code" value="<?php echo $client_zip_code; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Client City'; ?><?php if (strpos($value_config, ','."Client City".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_city" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client City:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Client City".',') === false ? 'readonly' : ''); ?> name="client_city" value="<?php echo $client_city; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Client Province'; ?><?php if (strpos($value_config, ','."Client Province".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_province" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client Province:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Client Province".',') === false ? 'readonly' : ''); ?> name="client_province" value="<?php echo $client_province; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Client Country'; ?><?php if (strpos($value_config, ','."Client Country".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_country" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client Country:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Client Country".',') === false ? 'readonly' : ''); ?> name="client_country" value="<?php echo $client_country; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Client Program Address'; ?><?php if (strpos($value_config, ','."Client Program Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_program_address" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client Home/Program Address:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Client Program Address".',') === false ? 'readonly' : ''); ?> name="client_program_address" value="<?php echo $client_program_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>


        <?php // Client Division ?>

        <?php $temp_field = 'Division Group Home 1'; ?><?php if (strpos($value_config, ','."Division Group Home 1".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="classification_group_home_1" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Division Group Home 1:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Division Group Home 1".',') === false ? 'readonly' : ''); ?> name="classification_group_home_1" value="<?php echo $classification_group_home_1; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Extended Health Benefits'; ?><?php if (strpos($value_config, ','."Extended Health Benefits".',') !== FALSE) {             ?>
            <h3>Extended Health Benefits</h3>
                  <div class="form-group">
                    <label for="alt_phone[]" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Insurer 1:<br></label>
                    <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
					<?php if(strpos($edit_config, ',Extended Health Benefits,') !== FALSE) { ?>
                      <select class="chosen-select-deselect <?php echo $required; ?>" name="insurerid[]" width="380">
                        <option value=""></option>
                            <?php $result_insurers = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT `contactid`, `name` FROM contacts WHERE category='Insurer' AND deleted=0"),MYSQLI_ASSOC));
							foreach($result_insurers as $insurer_id) {
								$insurer_name = get_client($dbc, $insurer_id);
								echo "<option ".($insurerid[0] == $insurer_id ? 'selected' : '')." value='". $insurer_id."'>".$insurer_name.'</option>';
							} ?>
                        </select>
					<?php } else { ?>
						<input type="hidden" name="insurerid[]" value="<?= $insurerid[0] ?>">
						<input type="text" readonly name="insurerid_label" value="<?= get_client($dbc,$insurerid[0]) ?>" class="form-control <?php echo $required; ?>">
					<?php } ?>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Plan/Acct 1#:</label>
                    <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
                      <input name="plan_acctno[]" type="text" value="<?php echo $plan_acctno[0]; ?>" id="office_street" class="form-control <?php echo $required; ?>" />
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="alt_phone[]" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Insurer 2:<br></label>
                    <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
					<?php if(strpos($edit_config, ',Extended Health Benefits,') !== FALSE) { ?>
                      <select class="chosen-select-deselect <?php echo $required; ?>" name="insurerid[]" width="380">
                        <option value=""></option>
                            <?php $result_insurers = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT `contactid`, `name` FROM contacts WHERE category='Insurer' AND deleted=0"),MYSQLI_ASSOC));
							foreach($result_insurers as $insurer_id) {
								$insurer_name = get_client($dbc, $insurer_id);
								echo "<option ".($insurerid[1] == $insurer_id ? 'selected' : '')." value='". $insurer_id."'>".$insurer_name.'</option>';
							} ?>
                        </select>
					<?php } else { ?>
						<input type="hidden" name="insurerid[]" value="<?= $insurerid[1] ?>">
						<input type="text" readonly name="insurerid_label" value="<?= get_client($dbc,$insurerid[1]) ?>" class="form-control <?php echo $required; ?>">
					<?php } ?>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Plan/Acct 2#:</label>
                    <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
                      <input name="plan_acctno[]" type="text" value="<?php echo $plan_acctno[1]; ?>" id="office_street" class="form-control <?php echo $required; ?>" />
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="alt_phone[]" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Insurer 3:<br></label>
                    <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
					<?php if(strpos($edit_config, ',Extended Health Benefits,') !== FALSE) { ?>
                      <select class="chosen-select-deselect <?php echo $required; ?>" name="insurerid[]" width="380">
                        <option value=""></option>
                            <?php $result_insurers = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT `contactid`, `name` FROM contacts WHERE category='Insurer' AND deleted=0"),MYSQLI_ASSOC));
							foreach($result_insurers as $insurer_id) {
								$insurer_name = get_client($dbc, $insurer_id);
								echo "<option ".($insurerid[2] == $insurer_id ? 'selected' : '')." value='". $insurer_id."'>".$insurer_name.'</option>';
							} ?>
                        </select>
					<?php } else { ?>
						<input type="hidden" name="insurerid[]" value="<?= $insurerid[2] ?>">
						<input type="text" readonly name="insurerid_label" value="<?= get_client($dbc,$insurerid[2]) ?>" class="form-control <?php echo $required; ?>">
					<?php } ?>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Plan/Acct 3#:</label>
                    <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
                      <input name="plan_acctno[]" type="text" value="<?php echo $plan_acctno[2]; ?>" id="office_street" class="form-control <?php echo $required; ?>" />
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="alt_phone[]" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Insurer 4:<br></label>
                    <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
					<?php if(strpos($edit_config, ',Extended Health Benefits,') !== FALSE) { ?>
                      <select class="chosen-select-deselect <?php echo $required; ?>" name="insurerid[]" width="380">
                        <option value=""></option>
                            <?php $result_insurers = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT `contactid`, `name` FROM contacts WHERE category='Insurer' AND deleted=0"),MYSQLI_ASSOC));
							foreach($result_insurers as $insurer_id) {
								$insurer_name = get_client($dbc, $insurer_id);
								echo "<option ".($insurerid[3] == $insurer_id ? 'selected' : '')." value='". $insurer_id."'>".$insurer_name.'</option>';
							} ?>
                        </select>
					<?php } else { ?>
						<input type="hidden" name="insurerid[]" value="<?= $insurerid[3] ?>">
						<input type="text" readonly name="insurerid_label" value="<?= get_client($dbc,$insurerid[3]) ?>" class="form-control <?php echo $required; ?>">
					<?php } ?>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Plan/Acct 4#:</label>
                    <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
                      <input name="plan_acctno[]" type="text" value="<?php echo $plan_acctno[3]; ?>" id="office_street" class="form-control <?php echo $required; ?>" />
                    </div>
                  </div>

        <?php } ?>

        <?php $temp_field = 'Division Group Home 2'; ?><?php if (strpos($value_config, ','."Division Group Home 2".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="classification_group_home_2" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Division Group Home 2:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Division Group Home 2".',') === false ? 'readonly' : ''); ?> name="classification_group_home_2" value="<?php echo $classification_group_home_2; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Division Day Program 1'; ?><?php if (strpos($value_config, ','."Division Day Program 1".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="classification_day_program_1" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Division Day Program 1:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Division Day Program 1".',') === false ? 'readonly' : ''); ?> name="classification_day_program_1" value="<?php echo $classification_day_program_1; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Division Day Program 2'; ?><?php if (strpos($value_config, ','."Division Day Program 2".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="classification_day_program_2" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Division Day Program 2:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Division Day Program 2".',') === false ? 'readonly' : ''); ?> name="classification_day_program_2" value="<?php echo $classification_day_program_2; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php //Members Profile ?>

        <?php $temp_field = 'FSCD Number'; ?><?php if (strpos($value_config, ','."FSCD Number".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_client_id" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>FSCD Number:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."FSCD Number".',') === false ? 'readonly' : ''); ?> name="client_client_id" value="<?php echo $client_client_id; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Date of Birth'; ?><?php if (strpos($value_config, ','."Date of Birth".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="birth_date" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Date of Birth:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Date of Birth".',') === false ? 'readonly' : ''); ?> name="birth_date" value="<?php echo $birth_date; ?>" type="text" class="datepicker form-control <?php echo $required; ?>"></p>
            </div>
            </div>
        <?php } ?>

        <?php
        $temp_field = 'Days Per Week'; ?><?php if (strpos($value_config, ','."Days Per Week".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="days_per_week" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?># of Days Per Week:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Days Per Week".',') === false ? 'readonly' : ''); ?> name="days_per_week" value="<?php echo $days_per_week; ?>" type="number" class="form-control <?php echo $required; ?>"></p>
            </div>
            </div>
        <?php } ?>

        <?php
        $temp_field = 'Hours Per Week'; ?><?php if (strpos($value_config, ','."Hours Per Week".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="hours_per_week" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?># of Hours Per Week:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Hours Per Week".',') === false ? 'readonly' : ''); ?> name="hours_per_week" value="<?php echo $hours_per_week; ?>" type="number" class="form-control <?php echo $required; ?>"></p>
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'School'; ?><?php if (strpos($value_config, ','."School".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="school" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>School:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."School".',') === false ? 'readonly' : ''); ?> name="school" value="<?php echo $school; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Hear About'; ?><?php if (strpos($value_config, ','."Hear About".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="hear_about" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>How did you hear about AAFS?:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Hear About".',') === false ? 'readonly' : ''); ?> name="hear_about" value="<?php echo $hear_about; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>


        <?php //Transportation ?>


        <?php $temp_field = 'Transportation Mode of Transportation'; ?><?php if (strpos($value_config, ','."Transportation Mode of Transportation".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="transportation_mode_of_transportation" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Mode of Transportation</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Transportation Mode of Transportation".',') === false ? 'readonly' : ''); ?> name="transportation_mode_of_transportation" value="<?php echo $transportation_mode_of_transportation; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Transportation Transit Access'; ?><?php if (strpos($value_config, ','."Transportation Transit Access".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="transportation_transit_access" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Transit Access:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Transportation Transit Access".',') === false ? 'readonly' : ''); ?> name="transportation_transit_access" value="<?php echo $transportation_transit_access; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Transportation Access Password'; ?><?php if (strpos($value_config, ','."Transportation Access Password".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="transportation_access_password" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Access Password:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Transportation Access Password".',') === false ? 'readonly' : ''); ?> name="transportation_access_password" value="<?php echo $transportation_access_password; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Transportation Drivers License'; ?><?php if (strpos($value_config, ','."Transportation Drivers License".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="transportation_drivers_license" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input driver’s licence #"><img src="../img/info.png" width="20"></a></span>
                Driver's Licence #:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Transportation Drivers License".',') === false ? 'readonly' : ''); ?> name="transportation_drivers_license" value="<?php echo $transportation_drivers_license; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Drivers License Class'; ?><?php if (strpos($value_config, ','."Drivers License Class".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="transportation_drivers_class" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input what class of licence you have."><img src="../img/info.png" width="20"></a></span>
                Driver's Licence Class:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Drivers License Class".',') === false ? 'readonly' : ''); ?> name="transportation_drivers_class" value="<?php echo $transportation_drivers_class; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Drive Manual Transmission'; ?><?php if (strpos($value_config, ','."Drive Manual Transmission".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="transportation_drivers_transmission" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Select yes or no."><img src="../img/info.png" width="20"></a></span>
                Driver Can Operate a Standard Transmission Vehicle:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Drive Manual Transmission".',') === false ? 'onclick="return false;"' : ''); ?> name="transportation_drivers_transmission" <?php echo 'Yes' == $transportation_drivers_transmission ? "checked " : ""; ?>value="Yes" type="radio"> Yes
              <input <?php echo (strpos($edit_config, ','."Drive Manual Transmission".',') === false ? 'onclick="return false;"' : ''); ?> name="transportation_drivers_transmission" <?php echo 'No' == $transportation_drivers_transmission ? "checked " : ""; ?>value="No" type="radio"> No
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Transportation Drivers Glasses'; ?><?php if (strpos($value_config, ','."Transportation Drivers Glasses".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="transportation_drivers_glasses" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Select yes or no."><img src="../img/info.png" width="20"></a></span>
                Driver Requires Glasses/Contacts by Law:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Transportation Drivers Glasses".',') === false ? 'onclick="return false;"' : ''); ?> name="transportation_drivers_glasses" <?php echo 'Yes' == $transportation_drivers_glasses ? "checked " : ""; ?>value="Yes" type="radio"> Yes
              <input <?php echo (strpos($edit_config, ','."Transportation Drivers Glasses".',') === false ? 'onclick="return false;"' : ''); ?> name="transportation_drivers_glasses" <?php echo 'No' == $transportation_drivers_glasses ? "checked " : ""; ?>value="No" type="radio"> No
            </div>
            </div>
        <?php } ?>

		<?php // Financial Information ?>

        <?php $temp_field = 'Bank Name'; ?><?php if (strpos($value_config, ','."Bank Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="bank_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Input your bank's name."><img src="../img/info.png" width="20"></a></span>
                Bank Name:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Bank Name".',') === false ? 'readonly' : ''); ?> name="bank_name" value="<?php echo $bank_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Bank Institution Number'; ?><?php if (strpos($value_config, ','."Bank Institution Number".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="bank_institution_number" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Input your bank institution number (3 digits)."><img src="../img/info.png" width="20"></a></span>
                Bank Institution Number:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Bank Institution Number".',') === false ? 'readonly' : ''); ?> name="bank_institution_number" value="<?php echo $bank_institution_number; ?>" type="number" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Bank Transit Number'; ?><?php if (strpos($value_config, ','."Bank Transit Number".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="bank_transit" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Input your bank transit number (5 digits)."><img src="../img/info.png" width="20"></a></span>
                Bank Transit Number:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Bank Transit Number".',') === false ? 'readonly' : ''); ?> name="bank_transit" value="<?php echo $bank_transit; ?>" type="number" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Bank Account Number'; ?><?php if (strpos($value_config, ','."Bank Account Number".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="bank_account_number" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Input your bank account number."><img src="../img/info.png" width="20"></a></span>
                Bank Account Number:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Bank Account Number".',') === false ? 'readonly' : ''); ?> name="bank_account_number" value="<?php echo $bank_account_number; ?>" type="number" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php //Emergency Contacts ?>

        <?php $temp_field = 'Primary Emergency Contact First Name'; ?><?php if (strpos($value_config, ','."Primary Emergency Contact First Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="pri_emergency_first_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input primary emergency contact first name."><img src="../img/info.png" width="20"></a></span>
                Primary Emergency Contact First Name:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Primary Emergency Contact First Name".',') === false ? 'readonly' : ''); ?> name="pri_emergency_first_name" value="<?php echo $pri_emergency_first_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Primary Emergency Contact Last Name'; ?><?php if (strpos($value_config, ','."Primary Emergency Contact Last Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="pri_emergency_last_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input primary emergency contact last name."><img src="../img/info.png" width="20"></a></span>
                Primary Emergency Contact Last Name:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Primary Emergency Contact Last Name".',') === false ? 'readonly' : ''); ?> name="pri_emergency_last_name" value="<?php echo $pri_emergency_last_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Primary Emergency Contact Cell Phone'; ?><?php if (strpos($value_config, ','."Primary Emergency Contact Cell Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="pri_emergency_cell_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input primary emergency contact cell phone #"><img src="../img/info.png" width="20"></a></span>
                Primary Emergency Contact Cell Phone:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Primary Emergency Contact Cell Phone".',') === false ? 'readonly' : ''); ?> name="pri_emergency_cell_phone" value="<?php echo $pri_emergency_cell_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Primary Emergency Contact Home Phone'; ?><?php if (strpos($value_config, ','."Primary Emergency Contact Home Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="pri_emergency_home_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input primary emergency contact home phone #"><img src="../img/info.png" width="20"></a></span>
                Primary Emergency Contact Home Phone:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Primary Emergency Contact Home Phone".',') === false ? 'readonly' : ''); ?> name="pri_emergency_home_phone" value="<?php echo $pri_emergency_home_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Primary Emergency Contact Email'; ?><?php if (strpos($value_config, ','."Primary Emergency Contact Email".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="pri_emergency_email" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input primary emergency contact email."><img src="../img/info.png" width="20"></a></span>
                Primary Emergency Contact Email:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Primary Emergency Contact Email".',') === false ? 'readonly' : ''); ?> name="pri_emergency_email" value="<?php echo $pri_emergency_email; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Primary Emergency Contact Relationship'; ?><?php if (strpos($value_config, ','."Primary Emergency Contact Relationship".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="pri_emergency_relation" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input primary emergency contact relationship."><img src="../img/info.png" width="20"></a></span>
                Primary Emergency Contact Relationship:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Primary Emergency Contact Relationship".',') === false ? 'readonly' : ''); ?> name="pri_emergency_relation" value="<?php echo $pri_emergency_relation; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Secondary Emergency Contact First Name'; ?><?php if (strpos($value_config, ','."Secondary Emergency Contact First Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="sec_emergency_first_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input secondary emergency contact first name."><img src="../img/info.png" width="20"></a></span>
                Secondary Emergency Contact First Name:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Secondary Emergency Contact First Name".',') === false ? 'readonly' : ''); ?> name="sec_emergency_first_name" value="<?php echo $sec_emergency_first_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Secondary Emergency Contact Last Name'; ?><?php if (strpos($value_config, ','."Secondary Emergency Contact Last Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="sec_emergency_last_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input secondary emergency contact last name."><img src="../img/info.png" width="20"></a></span>
                Secondary Emergency Contact Last Name:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Secondary Emergency Contact Last Name".',') === false ? 'readonly' : ''); ?> name="sec_emergency_last_name" value="<?php echo $sec_emergency_last_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Secondary Emergency Contact Cell Phone'; ?><?php if (strpos($value_config, ','."Secondary Emergency Contact Cell Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="sec_emergency_cell_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input secondary emergency contact cell phone #"><img src="../img/info.png" width="20"></a></span>
                Secondary Emergency Contact Cell Phone:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Secondary Emergency Contact Cell Phone".',') === false ? 'readonly' : ''); ?> name="sec_emergency_cell_phone" value="<?php echo $sec_emergency_cell_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Secondary Emergency Contact Home Phone'; ?><?php if (strpos($value_config, ','."Secondary Emergency Contact Home Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="sec_emergency_home_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input secondary emergency contact home phone #"><img src="../img/info.png" width="20"></a></span>
                Secondary Emergency Contact Home Phone:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Secondary Emergency Contact Home Phone".',') === false ? 'readonly' : ''); ?> name="sec_emergency_home_phone" value="<?php echo $sec_emergency_home_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Secondary Emergency Contact Email'; ?><?php if (strpos($value_config, ','."Secondary Emergency Contact Email".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="sec_emergency_email" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input secondary emergency contact email."><img src="../img/info.png" width="20"></a></span>
                Secondary Emergency Contact Email:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Secondary Emergency Contact Email".',') === false ? 'readonly' : ''); ?> name="sec_emergency_email" value="<?php echo $sec_emergency_email; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Secondary Emergency Contact Relationship'; ?><?php if (strpos($value_config, ','."Secondary Emergency Contact Relationship".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="sec_emergency_relation" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input secondary emergency contact relationship."><img src="../img/info.png" width="20"></a></span>
                Secondary Emergency Contact Relationship:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Secondary Emergency Contact Relationship".',') === false ? 'readonly' : ''); ?> name="sec_emergency_relation" value="<?php echo $sec_emergency_relation; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Emergency Contact Multiple'; ?><?php if (strpos($value_config, ','."Emergency Contact Multiple".',') !== FALSE) { ?>
        	<script type="text/javascript">
				function addMultiple(div) {
					var row = $('.'+div).last();
					var clone = row.clone();
					clone.find('input').val('');
					clone.find('input,select,textarea').off('change', saveField).change(saveField);
					row.after(clone);
				}
				function removeMultiple(div, btn) {
					var row = $('.'+div).first();
					if($('.'+div).length <= 0) {
						addMultiple(div);
					}
					$(btn).closest('.'+div).remove();
					row.find('input').each(function() {saveField(this); });
				}
        	</script>
            <input type="hidden" name="emergency_multiple_enabled" value="1">
            <div class="emergency_multiple clearfix">
        <?php } ?>

        <?php
            $arr_size  = 1;
            if (strpos($value_config, ','."Emergency Contact Multiple".',') !== FALSE && sizeof($emergency_first_name_arr) != 0) {
              $arr_size = sizeof($emergency_first_name_arr);
            }
            for ($i = 0; $i < $arr_size; $i++) {
              $emergency_first_name = $emergency_first_name_arr[$i];
              $emergency_last_name = $emergency_last_name_arr[$i];
              $emergency_contact_number = $emergency_contact_number_arr[$i];
              $emergency_relationship = $emergency_relationship_arr[$i];
        ?>

        <?php $temp_field = 'Emergency Contact First Name'; ?><?php if (strpos($value_config, ','."Emergency Contact First Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="emergency_first_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input other emergency contact first name."><img src="../img/info.png" width="20"></a></span>
                Emergency Contact First Name:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Emergency Contact First Name".',') === false ? 'readonly' : ''); ?> name="emergency_first_name[]" value="<?php echo $emergency_first_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Emergency Contact Last Name'; ?><?php if (strpos($value_config, ','."Emergency Contact Last Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="emergency_last_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input other emergency contact last name."><img src="../img/info.png" width="20"></a></span>
                Emergency Contact Last Name:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Emergency Contact Last Name".',') === false ? 'readonly' : ''); ?> name="emergency_last_name[]" value="<?php echo $emergency_last_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Emergency Contact Contact Number'; ?><?php if (strpos($value_config, ','."Emergency Contact Contact Number".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="emergency_contact_number" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input other emergency contact phone #"><img src="../img/info.png" width="20"></a></span>
                Emergency Contact Contact Number:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Emergency Contact Contact Number".',') === false ? 'readonly' : ''); ?> name="emergency_contact_number[]" value="<?php echo $emergency_contact_number; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Emergency Contact Relationship'; ?><?php if (strpos($value_config, ','."Emergency Contact Relationship".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="emergency_relationship" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input other emergency contact relationship."><img src="../img/info.png" width="20"></a></span>
                Emergency Contact Relationship:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Emergency Contact Relationship".',') === false ? 'readonly' : ''); ?> name="emergency_relationship[]" value="<?php echo $emergency_relationship; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Emergency Contact Multiple'; ?><?php if (strpos($value_config, ','."Emergency Contact Multiple".',') !== FALSE) { ?>
            <div class="form-group">
              <div class="col-sm-offset-4 col-sm-8">
				<div class="pull-right">
					<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addMultiple('emergency_multiple');">
					<img src="../img/remove.png" class="inline-img pull-right" onclick="removeMultiple('emergency_multiple', this);">
				</div>
              </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Emergency Contact Multiple'; ?><?php if (strpos($value_config, ','."Emergency Contact Multiple".',') !== FALSE) {
            if ($i == 0) {
              echo '</div>';
            }
        }} ?>

        <?php //Health Care & Insurance ?>

        <?php $temp_field = 'Health Care Number'; ?><?php if (strpos($value_config, ','."Health Care Number".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="health_care_num" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input health care number."><img src="../img/info.png" width="20"></a></span>
                Health Care #:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Health Care Number".',') === false ? 'readonly' : ''); ?> name="health_care_num" value="<?php echo $health_care_num; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Health Concerns'; ?><?php if (strpos($value_config, ','."Health Concerns".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="health_concerns" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Do you have any health concerns that you want to make the company aware of?</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				<textarea name="health_concerns" rows="5" cols="50" <?php echo (strpos($edit_config, ','."Health Concerns".',') === false ? 'class="form-control <?php echo $required; ?> noMceEditor" readonly style="display:none;"' : 'class="form-control <?php echo $required; ?>"'); ?>><?php echo $health_concerns; ?></textarea>
				<div <?php echo (strpos($edit_config, ','."Health Concerns".',') === false ? 'class="col-sm-12"' : 'style="display:none;"'); ?>><?php echo html_entity_decode($health_concerns); ?></div>
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Emergency Procedure'; ?><?php if (strpos($value_config, ','."Emergency Procedure".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="health_emergency_procedure" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Do you have any special emergency health procedures that you want the company to be aware of?</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				<textarea name="health_emergency_procedure" rows="5" cols="50" <?php echo (strpos($edit_config, ','."Emergency Procedure".',') === false ? 'class="form-control <?php echo $required; ?> noMceEditor" readonly style="display:none;"' : 'class="form-control <?php echo $required; ?>"'); ?>><?php echo $health_emergency_procedure; ?></textarea>
				<div <?php echo (strpos($edit_config, ','."Emergency Procedure".',') === false ? 'class="col-sm-12"' : 'style="display:none;"'); ?>><?php echo html_entity_decode($health_emergency_procedure); ?></div>
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Medications'; ?><?php if (strpos($value_config, ','."Medications".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="health_medications" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Are you on any medications you want to make the company aware of?</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				<textarea name="health_medications" rows="5" cols="50" <?php echo (strpos($edit_config, ','."Medications".',') === false ? 'class="form-control <?php echo $required; ?> noMceEditor" readonly style="display:none;"' : 'class="form-control <?php echo $required; ?>"'); ?>><?php echo $health_medications; ?></textarea>
				<div <?php echo (strpos($edit_config, ','."Medications".',') === false ? 'class="col-sm-12"' : 'style="display:none;"'); ?>><?php echo html_entity_decode($health_medications); ?></div>
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Allergies'; ?><?php if (strpos($value_config, ','."Allergies".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="health_allergens" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Do you have any allergies you wish to make the company aware of?</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				<textarea name="health_allergens" rows="5" cols="50" <?php echo (strpos($edit_config, ','."Allergies".',') === false ? 'class="form-control <?php echo $required; ?> noMceEditor" readonly style="display:none;"' : 'class="form-control <?php echo $required; ?>"'); ?>><?php echo $health_allergens; ?></textarea>
				<div <?php echo (strpos($edit_config, ','."Allergies".',') === false ? 'class="col-sm-12"' : 'style="display:none;"'); ?>><?php echo html_entity_decode($health_allergens); ?></div>
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Allergy Procedure'; ?><?php if (strpos($value_config, ','."Allergy Procedure".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="health_allergens_procedure" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Do you have any special procedures you want to make the company aware of should you require aid?</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
				<textarea name="health_allergens_procedure" rows="5" cols="50" <?php echo (strpos($edit_config, ','."Allergy Procedure".',') === false ? 'class="form-control <?php echo $required; ?> noMceEditor" readonly style="display:none;"' : 'class="form-control <?php echo $required; ?>"'); ?>><?php echo $health_allergens_procedure; ?></textarea>
				<div <?php echo (strpos($edit_config, ','."Allergy Procedure".',') === false ? 'class="col-sm-12"' : 'style="display:none;"'); ?>><?php echo html_entity_decode($health_allergens_procedure); ?></div>
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Insurance Alberta Health Care'; ?><?php if (strpos($value_config, ','."Insurance Alberta Health Care".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="insurance_alberta_health_care" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Alberta Health Care:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Insurance Alberta Health Care".',') === false ? 'readonly' : ''); ?> name="insurance_alberta_health_care" value="<?php echo $insurance_alberta_health_care; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Insurance AISH Entrance Date'; ?><?php if (strpos($value_config, ','."Insurance AISH Entrance Date".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="insurance_aish_entrance_date" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>AISH Entrance Date:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Insurance AISH Entrance Date".',') === false ? 'readonly' : ''); ?> name="insurance_aish_entrance_date" value="<?php echo $insurance_aish_entrance_date; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Insurance AISH #'; ?><?php if (strpos($value_config, ','."Insurance AISH #".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="insurance_aish" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>AISH #:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Insurance AISH #".',') === false ? 'readonly' : ''); ?> name="insurance_aish" value="<?php echo $insurance_aish; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Insurance Client ID'; ?><?php if (strpos($value_config, ','."Insurance Client ID".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="insurance_client_id" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client ID:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Insurance Client ID".',') === false ? 'readonly' : ''); ?> name="insurance_client_id" value="<?php echo $insurance_client_id; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php //Guardians ?>

        <?php $temp_field = 'Guardians Family Guardian'; ?><?php if (strpos($value_config, ','."Guardians Family Guardian".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_family_guardian" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Family Guardian</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="radio" <?php echo (strpos($edit_config, ','."Guardians Family Guardian".',') === false ? 'onclick="return false;"' : ''); ?> name="guardians_family_guardian" value="1" <?php if($guardians_family_guardian==1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Guardians Family Guardian".',') === false ? 'onclick="return false;"' : ''); ?> name="guardians_family_guardian" value="0" <?php if($guardians_family_guardian!=1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Family Appointed Guardian'; ?><?php if (strpos($value_config, ','."Guardians Family Appointed Guardian".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_family_appointed_guardian" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Family Appointed Guardian</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="radio" <?php echo (strpos($edit_config, ','."Guardians Family Appointed Guardian".',') === false ? 'onclick="return false;"' : ''); ?> name="guardians_family_appointed_guardian" value="1" <?php if($guardians_family_appointed_guardian == 1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Guardians Family Appointed Guardian".',') === false ? 'onclick="return false;"' : ''); ?> name="guardians_family_appointed_guardian" value="0" <?php if($guardians_family_appointed_guardian != 1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Public Guardian'; ?><?php if (strpos($value_config, ','."Guardians Public Guardian".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_public_guardian" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Guardians Public Guardian</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="radio" <?php echo (strpos($edit_config, ','."Guardians Public Guardian".',') === false ? 'onclick="return false;"' : ''); ?> name="guardians_public_guardian" value="1" <?php if($guardians_public_guardian == 1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Guardians Public Guardian".',') === false ? 'onclick="return false;"' : ''); ?> name="guardians_public_guardian" value="0" <?php if($guardians_public_guardian != 1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Court Appointed Guardian'; ?><?php if (strpos($value_config, ','."Guardians Court Appointed Guardian".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_court_appointed_guardian" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Court Appointed Guardian</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="radio" <?php echo (strpos($edit_config, ','."Guardians Court Appointed Guardian".',') === false ? 'onclick="return false;"' : ''); ?> name="guardians_court_appointed_guardian" value="1" <?php if($guardians_court_appointed_guardian == 1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Guardians Court Appointed Guardian".',') === false ? 'onclick="return false;"' : ''); ?> name="guardians_court_appointed_guardian" value="0" <?php if($guardians_court_appointed_guardian != 1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Office of the Public Guardian'; ?><?php if (strpos($value_config, ','."Guardians Office of the Public Guardian".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_office_public_guardian" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Office of the Public Guardian (OPG)</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="radio" <?php echo (strpos($edit_config, ','."Guardians Office of the Public Guardian".',') === false ? 'onclick="return false;"' : ''); ?> name="guardians_office_public_guardian" value="1" <?php if($guardians_office_public_guardian == 1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Guardians Office of the Public Guardian".',') === false ? 'onclick="return false;"' : ''); ?> name="guardians_office_public_guardian" value="0" <?php if($guardians_office_public_guardian != 1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Personal Directive'; ?><?php if (strpos($value_config, ','."Guardians Personal Directive".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_personal_directive" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Personal Directive</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="radio" <?php echo (strpos($edit_config, ','."Guardians Personal Directive".',') === false ? 'onclick="return false;"' : ''); ?> name="guardians_personal_directive" value="1" <?php if($guardians_personal_directive == 1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Guardians Personal Directive".',') === false ? 'onclick="return false;"' : ''); ?> name="guardians_personal_directive" value="0" <?php if($guardians_personal_directive != 1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Multiple'; ?><?php if (strpos($value_config, ','."Guardians Multiple".',') !== FALSE) { ?>
            <input type="hidden" name="guardians_multiple_enabled" value="1">
            <div class="additional_guardian clearfix">
        <?php } ?>

        <?php
            $arr_size  = 1;
            if (strpos($value_config, ','."Guardians Multiple".',') !== FALSE && sizeof($guardians_first_name_arr) != 0) {
              $arr_size = sizeof($guardians_first_name_arr);
            }
            for ($i = 0; $i < $arr_size; $i++) {
              $guardians_first_name = $guardians_first_name_arr[$i];
              $guardians_last_name = $guardians_last_name_arr[$i];
              $guardians_work_phone = $guardians_work_phone_arr[$i];
              $guardians_home_phone = $guardians_home_phone_arr[$i];
              $guardians_cell_phone = $guardians_cell_phone_arr[$i];
              $guardians_fax = $guardians_fax_arr[$i];
              $guardians_email_address = $guardians_email_address_arr[$i];
              $guardians_address = $guardians_address_arr[$i];
              $guardians_zip_code = $guardians_zip_code_arr[$i];
              $guardians_town = $guardians_town_arr[$i];
              $guardians_province = $guardians_province_arr[$i];
              $guardians_country = $guardians_country_arr[$i];
              $guardians_relationship = $guardians_relationship_arr[$i];

              if (strpos($value_config, ','."Guardians Multiple".',') !== FALSE) { ?>
                <div class="form group">
                <div class="col-sm-offset-4 col-sm-8">
                  <label for="guardians_information" class="control-label" style="color: #ffffff; padding-bottom: 1em;">Guardian Information</label>
                </div>
                </div>
              <?php }
        ?>

        <?php $temp_field = 'Guardians First Name'; ?><?php if (strpos($value_config, ','."Guardians First Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_first_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Guardians First Name</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Guardians First Name".',') === false ? 'readonly' : ''); ?> name="guardians_first_name[]" value="<?php echo $guardians_first_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Last Name'; ?><?php if (strpos($value_config, ','."Guardians Last Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_last_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Guardians Last Name</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Guardians Last Name".',') === false ? 'readonly' : ''); ?> name="guardians_last_name[]" value="<?php echo $guardians_last_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Relationship'; ?><?php if (strpos($value_config, ','."Guardians Relationship".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_relationship" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Guardians Relationship</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Guardians Relationship".',') === false ? 'readonly' : ''); ?> name="guardians_relationship[]" value="<?php echo $guardians_relationship; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Work Phone'; ?><?php if (strpos($value_config, ','."Guardians Work Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_work_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Guardians Work Phone</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Guardians Work Phone".',') === false ? 'readonly' : ''); ?> name="guardians_work_phone[]" value="<?php echo $guardians_work_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Home Phone'; ?><?php if (strpos($value_config, ','."Guardians Home Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_home_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Guardians Home Phone</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Guardians Home Phone".',') === false ? 'readonly' : ''); ?> name="guardians_home_phone[]" value="<?php echo $guardians_home_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Cell Phone'; ?><?php if (strpos($value_config, ','."Guardians Cell Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_cell_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Guardians Cell Phone</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Guardians Cell Phone".',') === false ? 'readonly' : ''); ?> name="guardians_cell_phone[]" value="<?php echo $guardians_cell_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Fax'; ?><?php if (strpos($value_config, ','."Guardians Fax".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_fax" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Guardians Fax</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Guardians Fax".',') === false ? 'readonly' : ''); ?> name="guardians_fax[]" value="<?php echo $guardians_fax; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Email Address'; ?><?php if (strpos($value_config, ','."Guardians Email Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_email_address" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Guardians Email Address</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Guardians Email Address".',') === false ? 'readonly' : ''); ?> name="guardians_email_address[]" value="<?php echo $guardians_email_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Address'; ?><?php if (strpos($value_config, ','."Guardians Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_address" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Guardians Address</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Guardians Address".',') === false ? 'readonly' : ''); ?> name="guardians_address[]" value="<?php echo $guardians_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Zip Code'; ?><?php if (strpos($value_config, ','."Guardians Zip Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_zip_code" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Guardians Postal/Zip Code</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Guardians Zip Code".',') === false ? 'readonly' : ''); ?> name="guardians_zip_code[]" value="<?php echo $guardians_zip_code; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Town'; ?><?php if (strpos($value_config, ','."Guardians Town".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_town" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Guardians Town</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Guardians Town".',') === false ? 'readonly' : ''); ?> name="guardians_town[]" value="<?php echo $guardians_town; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Province'; ?><?php if (strpos($value_config, ','."Guardians Province".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_province" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Guardians Province</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Guardians Province".',') === false ? 'readonly' : ''); ?> name="guardians_province[]" value="<?php echo $guardians_province; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Country'; ?><?php if (strpos($value_config, ','."Guardians Country".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_country" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Guardians Country</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Guardians Country".',') === false ? 'readonly' : ''); ?> name="guardians_country[]" value="<?php echo $guardians_country; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Multiple'; ?><?php if (strpos($value_config, ','."Guardians Multiple".',') !== FALSE) {
            if ($i == 0) {
              echo '</div>';
            }
        }} ?>

        <?php $temp_field = 'Guardians Multiple'; ?><?php if (strpos($value_config, ','."Guardians Multiple".',') !== FALSE) { ?>
            <div id="add_here_new_guardian"></div>
            <div class="form-group">
              <div class="col-sm-offset-4 col-sm-8">
                <button id="add_row_guardian" class="btn brand-btn pull-right" onclick="return false;">Add Guardian</button>
              </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Guardians Siblings'; ?><?php if (strpos($value_config, ','."Guardians Siblings".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_siblings" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Siblings</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Guardians Siblings".',') === false ? 'readonly' : ''); ?> name="guardians_siblings" value="<?php echo $guardians_siblings; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php //Trustee ?>

        <?php $temp_field = 'Trustee Family Trustee'; ?><?php if (strpos($value_config, ','."Trustee Family Trustee".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_family_guardian" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Family Trustee</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Family Trustee".',') === false ? 'onclick="return false;"' : ''); ?> name="trustee_family_guardian" value="1" <?php if($trustee_family_guardian==1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Family Trustee".',') === false ? 'onclick="return false;"' : ''); ?> name="trustee_family_guardian" value="0" <?php if($trustee_family_guardian!=1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Trustee Family Appointed Trustee'; ?><?php if (strpos($value_config, ','."Trustee Family Appointed Trustee".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_family_appointed_guardian" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Family Appointed Trustee</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Family Appointed Trustee".',') === false ? 'onclick="return false;"' : ''); ?> name="trustee_family_appointed_guardian" value="1" <?php if($trustee_family_appointed_guardian == 1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Family Appointed Trustee".',') === false ? 'onclick="return false;"' : ''); ?> name="trustee_family_appointed_guardian" value="0" <?php if($trustee_family_appointed_guardian != 1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Trustee Public Trustee'; ?><?php if (strpos($value_config, ','."Trustee Public Trustee".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_public_guardian" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Trustee Public Trustee</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Public Trustee".',') === false ? 'onclick="return false;"' : ''); ?> name="trustee_public_guardian" value="1" <?php if($trustee_public_guardian == 1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Public Trustee".',') === false ? 'onclick="return false;"' : ''); ?> name="trustee_public_guardian" value="0" <?php if($trustee_public_guardian != 1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Trustee Court Appointed Trustee'; ?><?php if (strpos($value_config, ','."Trustee Court Appointed Trustee".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_court_appointed_guardian" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Court Appointed Trustee</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Court Appointed Trustee".',') === false ? 'onclick="return false;"' : ''); ?> name="trustee_court_appointed_guardian" value="1" <?php if($trustee_court_appointed_guardian == 1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Court Appointed Trustee".',') === false ? 'onclick="return false;"' : ''); ?> name="trustee_court_appointed_guardian" value="0" <?php if($trustee_court_appointed_guardian != 1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Trustee Office of the Public Guardian'; ?><?php if (strpos($value_config, ','."Trustee Office of the Public Guardian".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_office_public_guardian" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Office of the Public Guardian (OPG)</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Office of the Public Guardian".',') === false ? 'onclick="return false;"' : ''); ?> name="trustee_office_public_guardian" value="1" <?php if($trustee_office_public_guardian == 1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Office of the Public Guardian".',') === false ? 'onclick="return false;"' : ''); ?> name="trustee_office_public_guardian" value="0" <?php if($trustee_office_public_guardian != 1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Trustee Personal Directive'; ?><?php if (strpos($value_config, ','."Trustee Personal Directive".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_personal_directive" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Personal Directive</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Personal Directive".',') === false ? 'onclick="return false;"' : ''); ?> name="trustee_personal_directive" value="1" <?php if($trustee_personal_directive == 1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Personal Directive".',') === false ? 'onclick="return false;"' : ''); ?> name="trustee_personal_directive" value="0" <?php if($trustee_personal_directive != 1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Trustee First Name'; ?><?php if (strpos($value_config, ','."Trustee First Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_first_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Trustee First Name</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Trustee First Name".',') === false ? 'readonly' : ''); ?> name="trustee_first_name" value="<?php echo $trustee_first_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Trustee Last Name'; ?><?php if (strpos($value_config, ','."Trustee Last Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_last_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Trustee Last Name</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Trustee Last Name".',') === false ? 'readonly' : ''); ?> name="trustee_last_name" value="<?php echo $trustee_last_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Trustee Work Phone'; ?><?php if (strpos($value_config, ','."Trustee Work Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_work_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Trustee Work Phone</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Trustee Work Phone".',') === false ? 'readonly' : ''); ?> name="trustee_work_phone" value="<?php echo $trustee_work_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Trustee Home Phone'; ?><?php if (strpos($value_config, ','."Trustee Home Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_home_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Trustee Home Phone</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Trustee Home Phone".',') === false ? 'readonly' : ''); ?> name="trustee_home_phone" value="<?php echo $trustee_home_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Trustee Cell Phone'; ?><?php if (strpos($value_config, ','."Trustee Cell Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_cell_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Trustee Cell Phone</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Trustee Cell Phone".',') === false ? 'readonly' : ''); ?> name="trustee_cell_phone" value="<?php echo $trustee_cell_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Trustee Fax'; ?><?php if (strpos($value_config, ','."Trustee Fax".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_fax" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Trustee Fax</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Trustee Fax".',') === false ? 'readonly' : ''); ?> name="trustee_fax" value="<?php echo $trustee_fax; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Trustee Email Address'; ?><?php if (strpos($value_config, ','."Trustee Email Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_email_address" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Trustee Email Address</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Trustee Email Address".',') === false ? 'readonly' : ''); ?> name="trustee_email_address" value="<?php echo $trustee_email_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Trustee Address'; ?><?php if (strpos($value_config, ','."Trustee Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_address" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Trustee Address</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Trustee Address".',') === false ? 'readonly' : ''); ?> name="trustee_address" value="<?php echo $trustee_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Trustee Zip Code'; ?><?php if (strpos($value_config, ','."Trustee Zip Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_zip_code" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Trustee Postal/Zip Code</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Trustee Zip Code".',') === false ? 'readonly' : ''); ?> name="trustee_zip_code" value="<?php echo $trustee_zip_code; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Trustee Town'; ?><?php if (strpos($value_config, ','."Trustee Town".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_town" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Trustee Town</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Trustee Town".',') === false ? 'readonly' : ''); ?> name="trustee_town" value="<?php echo $trustee_town; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Trustee Province'; ?><?php if (strpos($value_config, ','."Trustee Province".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_province" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Trustee Province</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Trustee Province".',') === false ? 'readonly' : ''); ?> name="trustee_province" value="<?php echo $trustee_province; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Trustee Country'; ?><?php if (strpos($value_config, ','."Trustee Country".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_country" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Trustee Country</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Trustee Country".',') === false ? 'readonly' : ''); ?> name="trustee_country" value="<?php echo $trustee_country; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php //Family Doctor ?>

        <?php $temp_field = 'Family Doctor Multiple'; ?><?php if (strpos($value_config, ','."Family Doctor Multiple".',') !== FALSE) { ?>
            <input type="hidden" name="family_doctor_multiple_enabled" value="1">
            <div class="additional_family_doctor clearfix">
        <?php } ?>

        <?php
            $arr_size  = 1;
            if (strpos($value_config, ','."Family Doctor Multiple".',') !== FALSE && sizeof($family_doctor_first_name_arr) != 0) {
              $arr_size = sizeof($family_doctor_first_name_arr);
            }
            for ($i = 0; $i < $arr_size; $i++) {
              $family_doctor_first_name = $family_doctor_first_name_arr[$i];
              $family_doctor_last_name = $family_doctor_last_name_arr[$i];
              $family_doctor_work_phone = $family_doctor_work_phone_arr[$i];
              $family_doctor_home_phone = $family_doctor_home_phone_arr[$i];
              $family_doctor_cell_phone = $family_doctor_cell_phone_arr[$i];
              $family_doctor_fax = $family_doctor_fax_arr[$i];
              $family_doctor_email_address = $family_doctor_email_address_arr[$i];
              $family_doctor_address = $family_doctor_address_arr[$i];
              $family_doctor_zip_code = $family_doctor_zip_code_arr[$i];
              $family_doctor_town = $family_doctor_town_arr[$i];
              $family_doctor_province = $family_doctor_province_arr[$i];
              $family_doctor_country = $family_doctor_country_arr[$i];

              if (strpos($value_config, ','."Family Doctor Multiple".',') !== FALSE) { ?>
                <div class="form group">
                <div class="col-sm-offset-4 col-sm-8">
                  <label for="family_doctor_information" class="control-label" style="color: #ffffff; padding-bottom: 1em;">Family Doctor Information</label>
                </div>
                </div>
              <?php }
        ?>

        <?php $temp_field = 'Family Doctor First Name'; ?><?php if (strpos($value_config, ','."Family Doctor First Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_first_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Family Doctor First Name</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Family Doctor First Name".',') === false ? 'readonly' : ''); ?> name="family_doctor_first_name[]" value="<?php echo $family_doctor_first_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Family Doctor Last Name'; ?><?php if (strpos($value_config, ','."Family Doctor Last Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_last_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Family Doctor Last Name</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Family Doctor Last Name".',') === false ? 'readonly' : ''); ?> name="family_doctor_last_name[]" value="<?php echo $family_doctor_last_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Family Doctor Work Phone'; ?><?php if (strpos($value_config, ','."Family Doctor Work Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_work_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Family Doctor Work Phone</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Family Doctor Work Phone".',') === false ? 'readonly' : ''); ?> name="family_doctor_work_phone[]" value="<?php echo $family_doctor_work_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Family Doctor Home Phone'; ?><?php if (strpos($value_config, ','."Family Doctor Home Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_home_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Family Doctor Home Phone</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Family Doctor Home Phone".',') === false ? 'readonly' : ''); ?> name="family_doctor_home_phone[]" value="<?php echo $family_doctor_home_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Family Doctor Cell Phone'; ?><?php if (strpos($value_config, ','."Family Doctor Cell Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_cell_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Family Doctor Cell Phone</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Family Doctor Cell Phone".',') === false ? 'readonly' : ''); ?> name="family_doctor_cell_phone[]" value="<?php echo $family_doctor_cell_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Family Doctor Fax'; ?><?php if (strpos($value_config, ','."Family Doctor Fax".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_fax" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Family Doctor Fax</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Family Doctor Fax".',') === false ? 'readonly' : ''); ?> name="family_doctor_fax[]" value="<?php echo $family_doctor_fax; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Family Doctor Email Address'; ?><?php if (strpos($value_config, ','."Family Doctor Email Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_email_address" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Family Doctor Email Address</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Family Doctor Email Address".',') === false ? 'readonly' : ''); ?> name="family_doctor_email_address[]" value="<?php echo $family_doctor_email_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Family Doctor Address'; ?><?php if (strpos($value_config, ','."Family Doctor Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_address" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Family Doctor Address</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Family Doctor Address".',') === false ? 'readonly' : ''); ?> name="family_doctor_address[]" value="<?php echo $family_doctor_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Family Doctor Zip Code'; ?><?php if (strpos($value_config, ','."Family Doctor Zip Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_zip_code" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Family Doctor Postal/Zip Code</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Family Doctor Zip Code".',') === false ? 'readonly' : ''); ?> name="family_doctor_zip_code[]" value="<?php echo $family_doctor_zip_code; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Family Doctor Town'; ?><?php if (strpos($value_config, ','."Family Doctor Town".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_town" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Family Doctor Town</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Family Doctor Town".',') === false ? 'readonly' : ''); ?> name="family_doctor_town[]" value="<?php echo $family_doctor_town; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Family Doctor Province'; ?><?php if (strpos($value_config, ','."Family Doctor Province".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_province" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Family Doctor Province</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Family Doctor Province".',') === false ? 'readonly' : ''); ?> name="family_doctor_province[]" value="<?php echo $family_doctor_province; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Family Doctor Country'; ?><?php if (strpos($value_config, ','."Family Doctor Country".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_country" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Family Doctor Country</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Family Doctor Country".',') === false ? 'readonly' : ''); ?> name="family_doctor_country[]" value="<?php echo $family_doctor_country; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Family Doctor Multiple'; ?><?php if (strpos($value_config, ','."Family Doctor Multiple".',') !== FALSE) {
            if ($i == 0) {
              echo '</div>';
            }
        }} ?>

        <?php $temp_field = 'Family Doctor Multiple'; ?><?php if (strpos($value_config, ','."Family Doctor Multiple".',') !== FALSE) { ?>
            <div id="add_here_new_family_doctor"></div>
            <div class="form-group">
              <div class="col-sm-offset-4 col-sm-8">
                <button id="add_row_family_doctor" class="btn brand-btn pull-right" onclick="return false;">Add Family Doctor</button>
              </div>
            </div>
        <?php } ?>

        <?php //Dentist ?>

        <?php $temp_field = 'Dentist First Name'; ?><?php if (strpos($value_config, ','."Dentist First Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_first_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Dentist First Name</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Dentist First Name".',') === false ? 'readonly' : ''); ?> name="dentist_first_name" value="<?php echo $dentist_first_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Dentist Last Name'; ?><?php if (strpos($value_config, ','."Dentist Last Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_last_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Dentist Last Name</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Dentist Last Name".',') === false ? 'readonly' : ''); ?> name="dentist_last_name" value="<?php echo $dentist_last_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Dentist Work Phone'; ?><?php if (strpos($value_config, ','."Dentist Work Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_work_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Dentist Work Phone</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Dentist Work Phone".',') === false ? 'readonly' : ''); ?> name="dentist_work_phone" value="<?php echo $dentist_work_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Dentist Home Phone'; ?><?php if (strpos($value_config, ','."Dentist Home Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_home_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Dentist Home Phone</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Dentist Home Phone".',') === false ? 'readonly' : ''); ?> name="dentist_home_phone" value="<?php echo $dentist_home_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Dentist Cell Phone'; ?><?php if (strpos($value_config, ','."Dentist Cell Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_cell_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Dentist Cell Phone</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Dentist Cell Phone".',') === false ? 'readonly' : ''); ?> name="dentist_cell_phone" value="<?php echo $dentist_cell_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Dentist Fax'; ?><?php if (strpos($value_config, ','."Dentist Fax".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_fax" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Dentist Fax</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Dentist Fax".',') === false ? 'readonly' : ''); ?> name="dentist_fax" value="<?php echo $dentist_fax; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Dentist Email Address'; ?><?php if (strpos($value_config, ','."Dentist Email Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_email_address" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Dentist Email Address</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Dentist Email Address".',') === false ? 'readonly' : ''); ?> name="dentist_email_address" value="<?php echo $dentist_email_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Dentist Address'; ?><?php if (strpos($value_config, ','."Dentist Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_address" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Dentist Address</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Dentist Address".',') === false ? 'readonly' : ''); ?> name="dentist_address" value="<?php echo $dentist_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Dentist Zip Code'; ?><?php if (strpos($value_config, ','."Dentist Zip Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_zip_code" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Dentist Postal/Zip Code</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Dentist Zip Code".',') === false ? 'readonly' : ''); ?> name="dentist_zip_code" value="<?php echo $dentist_zip_code; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Dentist Town'; ?><?php if (strpos($value_config, ','."Dentist Town".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_town" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Dentist Town</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Dentist Town".',') === false ? 'readonly' : ''); ?> name="dentist_town" value="<?php echo $dentist_town; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Dentist Province'; ?><?php if (strpos($value_config, ','."Dentist Province".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_province" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Dentist Province</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Dentist Province".',') === false ? 'readonly' : ''); ?> name="dentist_province" value="<?php echo $dentist_province; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Dentist Country'; ?><?php if (strpos($value_config, ','."Dentist Country".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_country" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Dentist Country</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Dentist Country".',') === false ? 'readonly' : ''); ?> name="dentist_country" value="<?php echo $dentist_country; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php //Specialists ?>

        <?php $temp_field = 'Specialists Multiple'; ?><?php if (strpos($value_config, ','."Specialists Multiple".',') !== FALSE) { ?>
            <input type="hidden" name="specialists_multiple_enabled" value="1">
            <div class="additional_specialist clearfix">
        <?php } ?>

        <?php
            $arr_size  = 1;
            if (strpos($value_config, ','."Specialists Multiple".',') !== FALSE && sizeof($specialists_first_name_arr) != 0) {
              $arr_size = sizeof($specialists_first_name_arr);
            }
            for ($i = 0; $i < $arr_size; $i++) {
              $specialists_first_name = $specialists_first_name_arr[$i];
              $specialists_last_name = $specialists_last_name_arr[$i];
              $specialists_work_phone = $specialists_work_phone_arr[$i];
              $specialists_home_phone = $specialists_home_phone_arr[$i];
              $specialists_cell_phone = $specialists_cell_phone_arr[$i];
              $specialists_fax = $specialists_fax_arr[$i];
              $specialists_email_address = $specialists_email_address_arr[$i];
              $specialists_address = $specialists_address_arr[$i];
              $specialists_zip_code = $specialists_zip_code_arr[$i];
              $specialists_town = $specialists_town_arr[$i];
              $specialists_province = $specialists_province_arr[$i];
              $specialists_country = $specialists_country_arr[$i];

              if (strpos($value_config, ','."Specialists Multiple".',') !== FALSE) { ?>
                <div class="form group">
                <div class="col-sm-offset-4 col-sm-8">
                  <label for="specialists_information" class="control-label" style="color: #ffffff; padding-bottom: 1em;">Specialists Information</label>
                </div>
                </div>
              <?php }
        ?>

        <?php $temp_field = 'Specialists First Name'; ?><?php if (strpos($value_config, ','."Specialists First Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_first_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Specialists First Name</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Specialists First Name".',') === false ? 'readonly' : ''); ?> name="specialists_first_name[]" value="<?php echo $specialists_first_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Specialists Last Name'; ?><?php if (strpos($value_config, ','."Specialists Last Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_last_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Specialists Last Name</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Specialists Last Name".',') === false ? 'readonly' : ''); ?> name="specialists_last_name[]" value="<?php echo $specialists_last_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Specialists Work Phone'; ?><?php if (strpos($value_config, ','."Specialists Work Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_work_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Specialists Work Phone</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Specialists Work Phone".',') === false ? 'readonly' : ''); ?> name="specialists_work_phone[]" value="<?php echo $specialists_work_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Specialists Home Phone'; ?><?php if (strpos($value_config, ','."Specialists Home Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_home_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Specialists Home Phone</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Specialists Home Phone".',') === false ? 'readonly' : ''); ?> name="specialists_home_phone[]" value="<?php echo $specialists_home_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Specialists Cell Phone'; ?><?php if (strpos($value_config, ','."Specialists Cell Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_cell_phone" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Specialists Cell Phone</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Specialists Cell Phone".',') === false ? 'readonly' : ''); ?> name="specialists_cell_phone[]" value="<?php echo $specialists_cell_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Specialists Fax'; ?><?php if (strpos($value_config, ','."Specialists Fax".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_fax" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Specialists Fax</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Specialists Fax".',') === false ? 'readonly' : ''); ?> name="specialists_fax[]" value="<?php echo $specialists_fax; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Specialists Email Address'; ?><?php if (strpos($value_config, ','."Specialists Email Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_email_address" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Specialists Email Address</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Specialists Email Address".',') === false ? 'readonly' : ''); ?> name="specialists_email_address[]" value="<?php echo $specialists_email_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Specialists Address'; ?><?php if (strpos($value_config, ','."Specialists Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_address" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Specialists Address</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Specialists Address".',') === false ? 'readonly' : ''); ?> name="specialists_address[]" value="<?php echo $specialists_address; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Specialists Zip Code'; ?><?php if (strpos($value_config, ','."Specialists Zip Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_zip_code" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Specialists Postal/Zip Code</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Specialists Zip Code".',') === false ? 'readonly' : ''); ?> name="specialists_zip_code[]" value="<?php echo $specialists_zip_code; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Specialists Town'; ?><?php if (strpos($value_config, ','."Specialists Town".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_town" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Specialists Town</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Specialists Town".',') === false ? 'readonly' : ''); ?> name="specialists_town[]" value="<?php echo $specialists_town; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Specialists Province'; ?><?php if (strpos($value_config, ','."Specialists Province".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_province" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Specialists Province</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Specialists Province".',') === false ? 'readonly' : ''); ?> name="specialists_province[]" value="<?php echo $specialists_province; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Specialists Country'; ?><?php if (strpos($value_config, ','."Specialists Country".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_country" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Specialists Country</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Specialists Country".',') === false ? 'readonly' : ''); ?> name="specialists_country[]" value="<?php echo $specialists_country; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Specialists Multiple'; ?><?php if (strpos($value_config, ','."Specialists Multiple".',') !== FALSE) {
            if ($i == 0) {
              echo '</div>';
            }
        }} ?>

        <?php $temp_field = 'Specialists Multiple'; ?><?php if (strpos($value_config, ','."Specialists Multiple".',') !== FALSE) { ?>
            <div id="add_here_new_specialist"></div>
            <div class="form-group">
              <div class="col-sm-offset-4 col-sm-8">
                <button id="add_row_specialist" class="btn brand-btn pull-right" onclick="return false;">Add Specialist</button>
              </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Medications Start Time'; ?><?php if (strpos($value_config, ','."Medications Start Time".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="medications_start_time" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Start Time</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Medications Start Time".',') === false ? 'readonly' : ''); ?> name="medications_start_time" value="<?php echo $medications_start_time; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Medications End Time'; ?><?php if (strpos($value_config, ','."Medications End Time".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="medications_end_time" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>End Time</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Medications End Time".',') === false ? 'readonly' : ''); ?> name="medications_end_time" value="<?php echo $medications_end_time; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Medications Completed By'; ?><?php if (strpos($value_config, ','."Medications Completed By".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="medications_completed_by" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Completed By</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Medications Completed By".',') === false ? 'readonly' : ''); ?> name="medications_completed_by" value="<?php echo $medications_completed_by; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Medications Signature Box'; ?><?php if (strpos($value_config, ','."Medications Signature Box".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="medications_signature_box" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Medications Signature Box</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Medications Signature Box".',') === false ? 'readonly' : ''); ?> name="medications_signature_box" value="<?php echo $medications_signature_box; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Medications Management Completed By'; ?><?php if (strpos($value_config, ','."Medications Management Completed By".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="medications_management_completed_by" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Management Completed By</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Medications Management Completed By".',') === false ? 'readonly' : ''); ?> name="medications_management_completed_by" value="<?php echo $medications_management_completed_by; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>


        <?php $temp_field = 'Medications Management Signature Box'; ?><?php if (strpos($value_config, ','."Medications Management Signature Box".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="medications_management_signature_box" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Management Signature Box</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Medications Management Signature Box".',') === false ? 'readonly' : ''); ?> name="medications_management_signature_box" value="<?php echo $medications_management_signature_box; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php //Membership ?>

        <?php $temp_field = 'Membership Status'; ?><?php if (strpos($value_config, ','."Membership Status".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="membership_status" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Membership Status:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <select <?php echo (strpos($edit_config, ','."Membership Status".',') === false ? 'readonly' : ''); ?> data-placeholder="Choose a Status..." name="membership_status" class="chosen-select-deselect <?php echo $required; ?> form-control <?php echo $required; ?>" width="380">
                <option value=""></option>
                <option <?php if ($membership_status == 'Active') { echo " selected"; } ?> value="Active">Active</option>
                <option <?php if ($membership_status == 'Pending Renewal') { echo " selected"; } ?> value="Pending Renewal">Pending Renewal</option>
                <option <?php if ($membership_status == 'Inactive') { echo " selected"; } ?> value="Inactive">Inactive</option>
              </select>
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Membership Level'; ?><?php if (strpos($value_config, ','."Membership Level".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="membership_level" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Membership Level:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <select <?php echo (strpos($edit_config, ','."Membership Level".',') === false ? 'readonly' : ''); ?> data-placeholder="Choose a Level..." name="membership_level" class="chosen-select-deselect <?php echo $required; ?> form-control <?php echo $required; ?>" width="380">
                <option value=""></option>
                <option <?php if ($membership_level == 'AAFS Family Membership') { echo " selected"; } ?> value="AAFS Family Membership">AAFS Family Membership</option>
                <option <?php if ($membership_level == 'AAFS Community Partner') { echo " selected"; } ?> value="AAFS Community Partner">AAFS Community Partner</option>
              </select>
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Membership Since'; ?><?php if (strpos($value_config, ','."Membership Since".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="membership_since" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Has been a Member since:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Membership Since".',') === false ? 'readonly' : ''); ?> name="membership_since" value="<?php echo $membership_since; ?>" type="text" class="datepicker form-control <?php echo $required; ?>"></p>
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Membership Renewal Date'; ?><?php if (strpos($value_config, ','."Membership Renewal Date".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="membership_renewal_date" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Membership Renewal Date:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Membership Renewal Date".',') === false ? 'readonly' : ''); ?> name="membership_renewal_date" value="<?php echo $membership_renewal_date; ?>" type="text" class="datepicker form-control <?php echo $required; ?>"></p>
            </div>
            </div>
        <?php } ?>

        <?php //Programs ?>

        <?php $temp_field = 'Programs Female Only Programs'; ?><?php if (strpos($value_config, ','."Programs Female Only Programs".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="programs_female_only_programs" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Female Only Programs:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="checkbox" value="Programs Female Only Programs" <?php if (strpos(','.$programs.',', ',Programs Female Only Programs,') !== FALSE) { echo " checked"; } ?> <?php echo (strpos($edit_config, ','."Programs Female Only Programs".',') === false ? 'readonly' : ''); ?> name="programs[]">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Programs Male Only Programs'; ?><?php if (strpos($value_config, ','."Programs Male Only Programs".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="programs_male_only_programs" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Male Only Programs:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="checkbox" value="Programs Male Only Programs" <?php if (strpos(','.$programs.',', ',Programs Male Only Programs,') !== FALSE) { echo " checked"; } ?> <?php echo (strpos($edit_config, ','."Programs Male Only Programs".',') === false ? 'readonly' : ''); ?> name="programs[]">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Programs LAAFS Program'; ?><?php if (strpos($value_config, ','."Programs LAAFS Program".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="programs_laafs_program" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>LAAFS Program:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="checkbox" value="Programs LAAFS Program" <?php if (strpos(','.$programs.',', ',Programs LAAFS Program,') !== FALSE) { echo " checked"; } ?> <?php echo (strpos($edit_config, ','."Programs LAAFS Program".',') === false ? 'readonly' : ''); ?> name="programs[]">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Programs AAFS Program'; ?><?php if (strpos($value_config, ','."Programs AAFS Program".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="programs_aafs_program" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>AAFS Program:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="checkbox" value="Programs AAFS Program" <?php if (strpos(','.$programs.',', ',Programs AAFS Program,') !== FALSE) { echo " checked"; } ?> <?php echo (strpos($edit_config, ','."Programs AAFS Program".',') === false ? 'readonly' : ''); ?> name="programs[]">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Programs Over 16 Program'; ?><?php if (strpos($value_config, ','."Programs Over 16 Program".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="programs_over_16_program" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Over 16 Program:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="checkbox" value="Programs Over 16 Program" <?php if (strpos(','.$programs.',', ',Programs Over 16 Program,') !== FALSE) { echo " checked"; } ?> <?php echo (strpos($edit_config, ','."Programs Over 16 Program".',') === false ? 'readonly' : ''); ?> name="programs[]">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Programs Over 18 Program'; ?><?php if (strpos($value_config, ','."Programs Over 18 Program".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="programs_over_18_program" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Adults/Over 18 Program:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="checkbox" value="Programs Over 18 Program" <?php if (strpos(','.$programs.',', ',Programs Over 18 Program,') !== FALSE) { echo " checked"; } ?> <?php echo (strpos($edit_config, ','."Programs Over 18 Program".',') === false ? 'readonly' : ''); ?> name="programs[]">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Programs LAAFS'; ?><?php if (strpos($value_config, ','."Programs LAAFS".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="programs_laafs" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>LAAFS:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="checkbox" value="LAAFS" <?php if (strpos(','.$programs.',', ',LAAFS,') !== FALSE) { echo " checked"; } ?> <?php echo (strpos($edit_config, ','."Programs LAAFS".',') === false ? 'readonly' : ''); ?> name="programs[]">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Programs AAFS Youth'; ?><?php if (strpos($value_config, ','."Programs AAFS Youth".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="programs_aafs_youth" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>AAFS Youth:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="checkbox" value="AAFS Youth" <?php if (strpos(','.$programs.',', ',AAFS Youth,') !== FALSE) { echo " checked"; } ?> <?php echo (strpos($edit_config, ','."Programs AAFS Youth".',') === false ? 'readonly' : ''); ?> name="programs[]">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Programs Fellowship 16'; ?><?php if (strpos($value_config, ','."Programs Fellowship 16".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="programs_fellowship_16" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Fellowship 16:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="checkbox" value="Fellowship 16" <?php if (strpos(','.$programs.',', ',Fellowship 16,') !== FALSE) { echo " checked"; } ?> <?php echo (strpos($edit_config, ','."Programs Fellowship 16".',') === false ? 'readonly' : ''); ?> name="programs[]">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Programs Fellowship 18'; ?><?php if (strpos($value_config, ','."Programs Fellowship 18".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="programs_fellowship_18" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Fellowship 18:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input type="checkbox" value="Fellowship 18" <?php if (strpos(','.$programs.',', ',Fellowship 18,') !== FALSE) { echo " checked"; } ?> <?php echo (strpos($edit_config, ','."Programs Fellowship 18".',') === false ? 'readonly' : ''); ?> name="programs[]">
            </div>
            </div>
        <?php } ?>

        <?php //Funding ?>

        <?php $temp_field = 'Funding FSCD'; ?><?php if (strpos($value_config, ','."Funding FSCD".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="funding_fscd" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Do you have funding through Family Support for Children with Disabilities (FSCD)?</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <select <?php echo (strpos($edit_config, ','."Funding FSCD".',') === false ? 'readonly' : ''); ?> data-placeholder="Yes/No" name="funding_fscd" class="chosen-select-deselect <?php echo $required; ?> form-control <?php echo $required; ?>" width="380">
                <option value=""></option>
                <option <?php if ($funding_fscd == 'Yes') { echo " selected"; } ?> value="Yes">Yes</option>
                <option <?php if ($funding_fscd == 'No') { echo " selected"; } ?> value="No">No</option>
              </select>
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Funding FSCD Worker Name'; ?><?php if (strpos($value_config, ','."Funding FSCD Worker Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="funding_fscd_worker_name" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>FSCD Worker Name:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Funding FSCD Worker Name".',') === false ? 'readonly' : ''); ?> name="funding_fscd_worker_name" value="<?php echo $funding_fscd_worker_name; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Funding FSCD File ID'; ?><?php if (strpos($value_config, ','."Funding FSCD File ID".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="funding_fscd_file_id" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>FSCD Fild ID#:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Funding FSCD File ID".',') === false ? 'readonly' : ''); ?> name="funding_fscd_file_id" value="<?php echo $funding_fscd_file_id; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Funding FSCD Renewal Date'; ?><?php if (strpos($value_config, ','."Funding FSCD Renewal Date".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="funding_fscd_renewal_date" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>FSCD Renewal Date:</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Funding FSCD Renewal Date".',') === false ? 'readonly' : ''); ?> name="funding_fscd_renewal_date" value="<?php echo $funding_fscd_renewal_date; ?>" type="text" class="datepicker form-control <?php echo $required; ?>"></p>
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Funding PDD'; ?><?php if (strpos($value_config, ','."Funding PDD".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="funding_pdd" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Do you have funding through Persons with Developmental Disabilities (PDD)?</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <select <?php echo (strpos($edit_config, ','."Funding PDD".',') === false ? 'readonly' : ''); ?> data-placeholder="Yes/No" name="funding_pdd" class="chosen-select-deselect <?php echo $required; ?> form-control <?php echo $required; ?>" width="380">
                <option value=""></option>
                <option <?php if ($funding_pdd == 'Yes') { echo " selected"; } ?> value="Yes">Yes</option>
                <option <?php if ($funding_pdd == 'No') { echo " selected"; } ?> value="No">No</option>
              </select>
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'PDD Key Contact'; ?><?php if (strpos($value_config, ','."PDD Key Contact".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="funding_pdd" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Key Contact</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
                <input <?php echo (strpos($edit_config, ','."PDD Key Contact".',') === false ? 'readonly' : ''); ?> name="pdd_key_contact" value="<?php echo $pdd_key_contact; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>
        <?php $temp_field = 'PDD Client ID'; ?><?php if (strpos($value_config, ','."PDD Client ID".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="funding_pdd" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Client ID</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
                <input <?php echo (strpos($edit_config, ','."PDD Client ID".',') === false ? 'readonly' : ''); ?> name="pdd_client_id" value="<?php echo $pdd_client_id; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>
        <?php $temp_field = 'PDD Phone'; ?><?php if (strpos($value_config, ','."PDD Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="funding_pdd" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Phone</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
                <input <?php echo (strpos($edit_config, ','."PDD Phone".',') === false ? 'readonly' : ''); ?> name="pdd_phone" value="<?php echo $pdd_phone; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>
        <?php $temp_field = 'PDD Fax'; ?><?php if (strpos($value_config, ','."PDD Fax".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="funding_pdd" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Fax</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
                <input <?php echo (strpos($edit_config, ','."PDD Fax".',') === false ? 'readonly' : ''); ?> name="pdd_fax" value="<?php echo $pdd_fax; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>
        <?php $temp_field = 'PDD Email'; ?><?php if (strpos($value_config, ','."PDD Email".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="funding_pdd" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Email</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
                <input <?php echo (strpos($edit_config, ','."PDD Email".',') === false ? 'readonly' : ''); ?> name="pdd_email" value="<?php echo $pdd_email; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>
        <?php $temp_field = 'PDD AISH'; ?><?php if (strpos($value_config, ','."PDD AISH".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="funding_pdd" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>AISH #</label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
                <input <?php echo (strpos($edit_config, ','."PDD AISH".',') === false ? 'readonly' : ''); ?> name="pdd_aish_no" value="<?php echo $pdd_aish_no; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php //Profile Documents ?>

        <?php $temp_field = 'Profile Documents'; ?><?php if (strpos($value_config, ','."Profile Documents".',') !== FALSE) { ?>
          <div class="form-group">
              <label for="additional_note" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>Document(s)
              </label>
              <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
                  <?php

                      $get_doc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(uploadid) AS total_id FROM contacts_patient_document WHERE contactid='$contactid' AND subtab='Profile' AND deleted = 0 AND upload IS NOT NULL"));

                      if($get_doc['total_id'] > 0) {
                          $result = mysqli_query($dbc, "SELECT upload, uploadid, `label` FROM contacts_patient_document WHERE  contactid='$contactid' AND subtab='Profile' AND deleted = 0 AND upload IS NOT NULL");

                          echo '<ul>';
                          $i=0;
                          while($row = mysqli_fetch_array($result)) {
                              $document = $row['upload'];
                              if($document != '') {
                                  echo '<li><a href="download/'.$document.'" target="_blank">'.($row['label'] == '' ? $document : $row['label']).'</a>';
                                  echo '</li>';
                              }
                          }
                          echo '</ul>';
                      }
                  ?>
                  <div class="form-group clearfix">
                      <label class="col-sm-5 text-center">Document</label>
                      <label class="col-sm-7 text-center">Label</label>
                  </div>
                  <div class="enter_cost additional_doc clearfix">
                      <div class="clearfix"></div>

                      <div class="form-group clearfix">
                          <div class="col-sm-5">
                              <input name="profile_document[]" type="file" data-filename-placement="inside" class="form-control <?php echo $required; ?>" />
                          </div>
                          <div class="col-sm-7">
                              <input name="profile_document_label[]" type="text" placeholder="Document Label" class="form-control <?php echo $required; ?>">
                          </div>
                      </div>

                  </div>

                  <div id="add_here_new_doc"></div>

                  <div class="form-group triple-gapped clearfix">
                      <div class="col-sm-offset-4 col-sm-8">
                          <button id="add_row_doc" class="btn brand-btn pull-left">Add Another Document</button>
                      </div>
                  </div>
              </div>
          </div>
        <?php } ?>

        <?php $temp_field = 'Profile Picture'; ?><?php if (strpos($value_config, ','."Profile Picture".',') !== FALSE) {
          $preset_images =
                      array
                      (
                        'alien.png' => 'Alien',
                        'baseball.png' => 'Baseball',
                        'basketeball.png' => 'Basketeball',
                        'bird.png' => 'Bird',
                        'BreakTheBarrier.png' => 'BreakTheBarrier',
                        'cactus.png' => 'Cactus',
                        'cat.png' => 'Cat',
                        'ClinicAce.png' => 'ClinicAce',
                        'cloud.png' => 'Cloud',
                        'cupcake.png' => 'Cupcake',
                        'daschund.png' => 'Daschund',
                        'dragon.png' => 'Dragon',
                        'football.png' => 'Football',
                        'FreshFocusMedia.png' => 'FreshFocusMedia',
                        'gladiator.png' => 'Gladiator',
                        'green-monster.png' => 'Green Monster',
                        'hawk.png' => 'Hawk',
                        'Lollipop.png' => 'Lollipop',
                        'mapleleaf.png' => 'maple-leaf',
                        'narwhale.png' => 'Narwhale',
                        'ninja.png' => 'Ninja',
                        'PinkyGhost.png' => 'Pink Ghost',
                        'PrecisionWorkflow.png' => 'PrecisionWorkflow',
                        'redcar.png' => 'Red Car',
                        'robot.png' => 'Robot',
                        'rocket.png' => 'Rocket',
                        'Rook.png' => 'Rook',
                        'shark.png' => 'Shark',
                        'sheriffbadge.png' => 'Sheriff Badge',
                        'sun.png' => 'Sun',
                        'target.png' => 'Target',
                        'wolf.png' => 'Wolf'
                      );
          $user_preset = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `preset_profile_picture` FROM `user_settings` WHERE `contactid` = '$contactid'"))['preset_profile_picture'];
          if(file_exists('../Profile/download/profile_pictures/'.$contactid.'.jpg')) { ?>
          <div class="form-group text-center">
            <?php
            $profile_picture = '../Profile/download/profile_pictures/'.$contactid.'.jpg';

            echo "<img id='current-avatar' src='$profile_picture'>";
            ?>
          </div>
          <?php } else if(!empty($user_preset)) {
          	echo '<div class="form-group text-center" style="margin-right:100px">';
          	echo "<img id='current-avatar' src='../img/avatars/$user_preset'>";
          	echo '</div>';
          } ?>
          <script type="text/javascript">
		  $(document).on('change', 'select[name="preset_profile_picture"]', function() { changePresetPicture(this); });
          </script>
          <div class="form-group">
          <label for="file" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
            <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Select a profile picture from the displayed icons, or upload your own image."><img src="../img/info.png" width="20"></a></span>
            Profile Picture:
          </label>
          <div class="col-sm-3 avatar">
            <ul>
          		<?php foreach($preset_images as $key => $preset_image) {
                $class="";
                if($key == $user_preset) { $class = "hover"; } ?>
          			<li class="li-avatar"><img class="<?php echo $class; ?>" id="<?php echo str_replace(' ', '_',$preset_image); ?>" src = "../img/avatars/<?php echo $key; ?>" value="<?php echo $key; ?>" title="<?php echo $preset_image; ?>" width="50" height="40"></li>
              <?php } ?>
            </ul>
            <input type="hidden" id="preset_profile_picture" name="preset_profile_picture" value="">
          </div>
          <div class="col-sm-5">&nbsp;or&nbsp;&nbsp;
            <input type="button" <?php echo (strpos($edit_config, ','."Profile Picture".',') === false ? 'readonly' : ''); ?> name="profile_picture_upload" onclick="uploadImageClick()" value="Upload Image" />
            <input type="file" onchange="uploadImage($(this))" name="profile_picture" style="display: none;" />
            <?php if(file_exists('../Profile/download/profile_pictures/'.$contactid.'.jpg') || !empty($user_preset)) { ?>
            <a href="" onclick="deleteImage(); return false;">Delete</a>
            <?php } ?>
          </div>
          </div>
          <div id="profile_picture_container" style="position: relative; overflow-y: auto;" class="form-group text-center profile_picture_div" hidden>
            <img id="profile_picture_crop">
            <input type="hidden" name="profile_picture_name" value="" />
            <input type="hidden" name="x1" value="" />
            <input type="hidden" name="y1" value="" />
            <input type="hidden" name="x2" value="" />
            <input type="hidden" name="y2" value="" />
            <input type="hidden" name="image_width" value="" />
            <input type="hidden" name="image_height" value="" />
          </div>
          <div id="profile_picture_preset_container" class="form-group text-center" hidden>
          	<img id="profile_picture_preset">
          </div>
        <?php } ?>

        <?php $temp_field = 'Initials'; ?><?php if (strpos($value_config, ','."Initials".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="initials" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Input initials."><img src="../img/info.png" width="20"></a></span>
                Initials:
            </label>
            <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
              <input <?php echo (strpos($edit_config, ','."Initials".',') === false ? 'readonly' : ''); ?> name="initials" value="<?php echo $initials; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Calendar Color'; ?><?php if (strpos($value_config, ','."Calendar Color".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="calendar_color" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?>
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Choose a calendar color by clicking on the color block, selecting the color and clicking OK."><img src="../img/info.png" width="20"></a></span>
                Calendar Color:
            </label>
            <div class="col-sm-1">
              <input onchange="colorCodeChange(this)" <?php echo (strpos($edit_config, ','."Calendar Color".',') === false ? 'onclick="return false;"' : ''); ?> class="form-control <?php echo $required; ?>" type="color" name="calendar_color_picker" value="<?php echo $calendar_color; ?>">
            </div>
            <div class="col-sm-7">
              <input <?php echo (strpos($edit_config, ','."Calendar Color".',') === false ? 'readonly' : ''); ?> name="calendar_color" value="<?php echo $calendar_color; ?>" type="text" class="form-control <?php echo $required; ?>">
            </div>
            </div>
        <?php } ?>

        <?php $temp_field = 'Forms'; ?><?php if (strpos($value_config, ','."Forms".',') !== FALSE) { ?>
            <div class="form-group">
                <table class="table table-bordered">
                    <tr>
                        <th>Staff</th>
                        <th>Topic</th>
                        <th>Heading</th>
                        <th>Sub Section Heading</th>
                        <th>PDF</th>
                    </tr>
                    <?php $forms_query = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `hr_attendance` WHERE `assign_staffid` = '".$contactid."' AND `done` = 1"),MYSQLI_ASSOC);
                    foreach($forms_query as $row) {
                        $hr_form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `hr` WHERE `hrid` = '".$row['hrid']."'"));
                        $form = get_hr($dbc, $row['hrid'], 'form');
                        $user_form_id = get_hr($dbc, $row['hrid'], 'user_form_id');

                        if($user_form_id > 0) {
                            $user_pdf = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_pdf` WHERE `pdf_id` = '".$row['fieldlevelriskid']."'"));
                            $pdf_path = 'download/'.$user_pdf['generated_file'];
                        } else {
                            if($form == 'AVS Near Miss') {
                                $pdf_path = 'avs_near_miss/download/hr_'.$row['fieldlevelriskid'].'.pdf';
                            }

                            if($form == 'Employee Information Form') {
                                $pdf_path = 'employee_information_form/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Employee Driver Information Form') {
                                $pdf_path = 'employee_driver_information_form/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Time Off Request') {
                                $get_staff = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT fieldlevelriskid FROM hr_time_off_request WHERE hrid='$hrid' AND (contactid='$contactid' OR attendance_staff LIKE '%," . $login_user . ",%') AND DATE(today_date) = CURDATE() AND status='New'"));

                                $pdf_path = 'time_off_request/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Confidential Information') {
                                $pdf_path = 'confidential_information/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Work Hours Policy') {
                                $pdf_path = 'work_hours_policy/download/hr_'.$row['fieldlevelriskid'].'.pdf';
                            }
                            if($form == 'Direct Deposit Information') {
                                $pdf_path = 'direct_deposit_information/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Employee Substance Abuse Policy') {
                                $pdf_path = 'employee_substance_abuse_policy/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Employee Right to Refuse Unsafe Work') {
                                $pdf_path = 'employee_right_to_refuse_unsafe_work/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Shop Yard and Office Orientation') {
                                $pdf_path = 'employee_shop_yard_office_orientation/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == "Copy of Drivers Licence and Safety Tickets") {
                                $pdf_path = 'copy_of_drivers_licence_safety_tickets/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'PPE Requirements') {
                                $pdf_path = 'ppe_requirements/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Verbal Training in Emergency Response Plan') {
                                $pdf_path = 'verbal_training_in_emergency_response_plan/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Eligibility for General Holidays and General Holiday Pay') {
                                $pdf_path = 'eligibility_for_general_holidays_general_holiday_pay/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Maternity Leave and Parental Leave') {
                                $pdf_path = 'maternity_leave_parental_leave/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Employment Verification Letter') {
                                $pdf_path = 'employment_verification_letter/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Background Check Authorization') {
                                $pdf_path = 'background_check_authorization/download/hr_'.$row['fieldlevelriskid'].'.pdf';
                            }
                            if($form == 'Disclosure of Outside Clients') {
                                $pdf_path = 'disclosure_of_outside_clients/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Employment Agreement') {
                                $pdf_path = 'employment_agreement/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Independent Contractor Agreement') {
                                $pdf_path = 'independent_contractor_agreement/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Letter of Offer') {
                                $pdf_path = 'letter_of_offer/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Employee Non-Disclosure Agreement') {
                                $pdf_path = 'employee_nondisclosure_agreement/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Employee Self Evaluation') {
                                $pdf_path = 'employee_self_evaluation/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'HR Complaint') {
                                $pdf_path = 'hr_complaint/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Exit Interview') {
                                $pdf_path = 'exit_interview/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Employee Expense Reimbursement') {
                                $pdf_path = 'employee_expense_reimbursement/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Absence Report') {
                                $pdf_path = 'absence_report/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Employee Accident Report Form') {
                                $pdf_path = 'employee_accident_report_form/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Trucking Information') {
                                $pdf_path = 'trucking_information/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Contractor Orientation') {
                                $pdf_path = 'contractor_orientation/download/hr_'.$row['fieldlevelriskid'].'.pdf';
                            }
                            if($form == 'Contract Welder Inspection Checklist') {
                                $get_staff = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT fieldlevelriskid FROM hr_contract_welder_inspection_checklist WHERE hrid='$hrid' AND (contactid='$contactid' OR attendance_staff LIKE '%," . $login_user . ",%') AND DATE(today_date) = CURDATE() AND status='New'"));

                                $pdf_path = 'avs_near_miss/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Contractor Pay Agreement') {
                                $get_staff = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT fieldlevelriskid FROM hr_contractor_pay_agreement WHERE hrid='$hrid' AND (contactid='$contactid' OR attendance_staff LIKE '%," . $login_user . ",%') AND DATE(today_date) = CURDATE() AND status='New'"));

                                $pdf_path = 'avs_near_miss/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Employee Holiday Request Form') {
                                $get_staff = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT fieldlevelriskid FROM hr_employee_holiday_request_form WHERE hrid='$hrid' AND (contactid='$contactid' OR attendance_staff LIKE '%," . $login_user . ",%') AND DATE(today_date) = CURDATE() AND status='New'"));

                                $pdf_path = 'avs_near_miss/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                            if($form == 'Employee Coaching Form') {
                                $get_staff = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT fieldlevelriskid FROM hr_employee_coaching_form WHERE hrid='$hrid' AND (contactid='$contactid' OR attendance_staff LIKE '%," . $login_user . ",%') AND DATE(today_date) = CURDATE() AND status='New'"));

                                $pdf_path = 'avs_near_miss/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                            }
                        } ?>
                    <tr>
                        <td><?= get_contact($dbc, $row['assign_staffid']) ?></td>
                        <td><?= $hr_form['category'] ?></td>
                        <td><?= $hr_form['heading'] ?></td>
                        <td><?= $hr_form['sub_heading'] ?></td>
                        <td><a href="../HR/<?= $pdf_path ?>" target="_blank">View</a></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } ?>

        <?php $temp_field = 'Manuals'; ?><?php if (strpos($value_config, ','."Manuals".',') !== FALSE) { ?>
            <div class="form-group">
                <table class="table table-bordered">
                    <tr>
                        <th>Staff</th>
                        <th>Topic</th>
                        <th>Heading</th>
                        <th>Sub Section Heading</th>
                        <th>PDF</th>
                    </tr>
                    <?php $manuals_query = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `manuals_staff` LEFT JOIN `manuals` ON `manuals_staff`.`manualtypeid`=`manuals`.`manualtypeid` WHERE `done`=1 AND `staffid`='$contactid' ORDER BY `today_date` DESC"),MYSQLI_ASSOC);
                    foreach($manuals_query as $row) {
                        $pdf_path = file_exists('../HR/download/'.config_safe_str(trim(trim($row['third_heading_number'].' '.$row['third_heading']) == '' ? (trim($row['sub_heading_number'].' '.$row['sub_heading']) == '' ? $row['heading_number'].' '.$row['heading'] : substr_replace($row['sub_heading_number'].' '.$row['sub_heading'],'',strpos($row['sub_heading_number'].' '.$row['sub_heading'],' '),1)) : $row['third_heading_number'].' '.$row['third_heading']).'_'.str_replace('-','_',$row['today_date'])).'.pdf') ? '../HR/download/'.config_safe_str(trim(trim($row['third_heading_number'].' '.$row['third_heading']) == '' ? (trim($row['sub_heading_number'].' '.$row['sub_heading']) == '' ? $row['heading_number'].' '.$row['heading'] : substr_replace($row['sub_heading_number'].' '.$row['sub_heading'],'',strpos($row['sub_heading_number'].' '.$row['sub_heading'],' '),1)) : $row['third_heading_number'].' '.$row['third_heading']).'_'.str_replace('-','_',$row['today_date'])).'.pdf' : (file_exists('../HR/download/manual_'.$row['manualtypeid'].'_signed_'.$row['manualstaffid'].'_'.$row['today_date'].'.pdf') ? '../HR/download/manual_'.$row['manualtypeid'].'_signed_'.$row['manualstaffid'].'_'.$row['today_date'].'.pdf' : '../Manuals/download/manual_'.$row['manualtypeid'].'_signed_'.$row['manualstaffid'].'_'.$row['today_date'].'.pdf');
                    ?>
                    <tr>
                        <td><?= get_contact($dbc, $row['staffid']) ?></td>
                        <td><?= $row['category'] ?></td>
                        <td><?= $row['heading'] ?></td>
                        <td><?= $row['sub_heading'] ?></td>
                        <td><a href="<?= $pdf_path ?>" target="_blank">View</a></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } ?>

        <?php $temp_field = 'Treatment Charts'; ?><?php if (strpos($value_config, ','."Treatment Charts".',') !== FALSE) { ?>
            <div class="form-group">
                <table class="table table-bordered">
                    <tr>
                        <th></th>
                        <th>Heading</th>
                        <th>Sub Section Heading</th>
                        <?php $temp_field = 'Treatment Charts Injury'; ?><?php if (strpos($value_config, ','."Treatment Charts Injury".',') !== FALSE) { ?>
                        <th>Injury</th>
                        <?php } ?>
                        <?php $temp_field = 'Treatment Charts Staff'; ?><?php if (strpos($value_config, ','."Treatment Charts Staff".',') !== FALSE) { ?>
                        <th>Filled by Staff</th>
                        <?php } ?>
                        <?php $temp_field = 'Treatment Charts Date'; ?><?php if (strpos($value_config, ','."Treatment Charts Date".',') !== FALSE) { ?>
                        <th>Date</th>
                        <?php } ?>
                        <th>PDF</th>
                    </tr>
                    <?php $treatment_query = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `patientform_pdf` WHERE `patientid` = '".$contactid."' ORDER BY `today_date` DESC"),MYSQLI_ASSOC);
                    foreach($treatment_query as $row) {
                        $patientform = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `patientform` WHERE `patientformid` = '".$row['patientformid']."'"));
                    ?>
                    <tr>
                        <td><?= get_contact($dbc, $row['patientid']) ?></td>
                        <td><?= $patientform['heading'] ?></td>
                        <td><?= $patientform['sub_heading'] ?></td>
                        <?php $temp_field = 'Treatment Charts Injury'; ?><?php if (strpos($value_config, ','."Treatment Charts Injury".',') !== FALSE) {
                        $injury = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `injury` WHERE `injuryid` = '".$patientform['injuryid']."'")); ?>
                        <td><?= $injury['injury_name'] ?> - <?= $injury['injury_date'] ?></td>
                        <?php } ?>
                        <?php $temp_field = 'Treatment Charts Staff'; ?><?php if (strpos($value_config, ','."Treatment Charts Staff".',') !== FALSE) { ?>
                        <td><?= get_contact($dbc, $row['staffid']) ?></td>
                        <?php } ?>
                        <?php $temp_field = 'Treatment Charts Date'; ?><?php if (strpos($value_config, ','."Treatment Charts Date".',') !== FALSE) { ?>
                        <td><?= $row['filled_date'] ?></td>
                        <?php } ?>
                        <td><a href="../Treatment/<?= $row['pdf_path'] ?>" target="_blank">View</a></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } ?>

        <?php $temp_field = 'Staff Documents'; ?><?php if (strpos($value_config, ','."Staff Documents".',') !== FALSE) { ?>
            <div class="form-group">
				<?php $sd_value_config = ','.mysqli_fetch_assoc(mysqli_query($dbc,"SELECT staff_documents_dashboard FROM field_config"))['staff_documents_dashboard'].',';
				$doc_list = mysqli_query($dbc, "SELECT * FROM `staff_documents` WHERE `contactid` = '$contactid' AND `deleted` = 0");
				if(mysqli_num_rows($doc_list) > 0) { ?>
					<table class="table table-bordered">
						<tr class="hidden-sm hidden-xs">
			            	<?php
								if (strpos($sd_value_config, ','."Staff".',') !== FALSE) {
									echo '<th>Staff</th>';
								}
								if (strpos($sd_value_config, ','."Staff Documents Code".',') !== FALSE) {
									echo '<th>Staff Documents Code</th>';
								}
								if (strpos($sd_value_config, ','."Staff Documents Type".',') !== FALSE) {
									echo '<th>Staff Document Type</th>';
								}
								if (strpos($sd_value_config, ','."Category".',') !== FALSE) {
									echo '<th>Category</th>';
								}
								if (strpos($sd_value_config, ','."Title".',') !== FALSE) {
									echo '<th>Title</th>';
								}
								if (strpos($sd_value_config, ','."Uploader".',') !== FALSE) {
									echo '<th>Documents</th>';
								}
								if (strpos($sd_value_config, ','."Link".',') !== FALSE) {
									echo '<th>Link</th>';
								}
								if (strpos($sd_value_config, ','."Heading".',') !== FALSE) {
									echo '<th>Heading</th>';
								}
								if (strpos($sd_value_config, ','."Name".',') !== FALSE) {
									echo '<th>Name</th>';
								}
								if (strpos($sd_value_config, ','."Fee".',') !== FALSE) {
									echo '<th>Fee</th>';
								}
								if (strpos($sd_value_config, ','."Cost".',') !== FALSE) {
									echo '<th>Cost</th>';
								}
								if (strpos($sd_value_config, ','."Description".',') !== FALSE) {
									echo '<th>Description</th>';
								}
								if (strpos($sd_value_config, ','."Quote Description".',') !== FALSE) {
									echo '<th>Quote Description</th>';
								}
								if (strpos($sd_value_config, ','."Invoice Description".',') !== FALSE) {
									echo '<th>Invoice Description</th>';
								}
								if (strpos($sd_value_config, ','."Ticket Description".',') !== FALSE) {
									echo '<th>'.TICKET_NOUN.' Description</th>';
								}
								if (strpos($sd_value_config, ','."Final Retail Price".',') !== FALSE) {
									echo '<th>Final Retail Price</th>';
								}
								if (strpos($sd_value_config, ','."Admin Price".',') !== FALSE) {
									echo '<th>Admin Price</th>';
								}
								if (strpos($sd_value_config, ','."Wholesale Price".',') !== FALSE) {
									echo '<th>Wholesale Price</th>';
								}
								if (strpos($sd_value_config, ','."Commercial Price".',') !== FALSE) {
									echo '<th>Commercial Price</th>';
								}
								if (strpos($sd_value_config, ','."Staff Price".',') !== FALSE) {
									echo '<th>Staff Price</th>';
								}
								if (strpos($sd_value_config, ','."Minimum Billable".',') !== FALSE) {
									echo '<th>Minimum Billable</th>';
								}
								if (strpos($sd_value_config, ','."Estimated Hours".',') !== FALSE) {
									echo '<th>Estimated Hours</th>';
								}
								if (strpos($sd_value_config, ','."Actual Hours".',') !== FALSE) {
									echo '<th>Actual Hours</th>';
								}
								if (strpos($sd_value_config, ','."MSRP".',') !== FALSE) {
									echo '<th>MSRP</th>';
								}
								echo '<th>Function</th>';
							?>
						</tr>
						<?php while($row = mysqli_fetch_array($doc_list)) { ?>
							<tr>
				            	<?php
									$staff_documentsid = $row['staff_documentsid'];
									if (strpos($sd_value_config, ','."Staff".',') !== FALSE) {
										echo '<td data-title="Staff"><a href="'.WEBSITE_URL.'/Staff/staff_edit.php?contactid=' . $row['contactid'] . '&from=' . urlencode(WEBSITE_URL . $_SERVER['REQUEST_URI']) . '">' . get_contact($dbc, $row['contactid']) . '</a></td>';
									}
									if (strpos($sd_value_config, ','."Staff Documents Code".',') !== FALSE) {
										echo '<td data-title="Doc. Code">' . $row['staff_documents_code'] . '</td>';
									}
									if (strpos($sd_value_config, ','."Staff Documents Type".',') !== FALSE) {
										echo '<td data-title="Doc. Type">' . $row['staff_documents_type'] . '</td>';
									}
									if (strpos($sd_value_config, ','."Category".',') !== FALSE) {
										echo '<td data-title="Category">' . $row['category'] . '</td>';
									}

									if (strpos($sd_value_config, ','."Title".',') !== FALSE) {
										echo '<td data-title="Title">' . $row['title'] . '</td>';
									}
									if (strpos($sd_value_config, ','."Uploader".',') !== FALSE) {
										echo '<td data-title="Upload">';
										$staff_documents_uploads1 = "SELECT * FROM staff_documents_uploads WHERE staff_documentsid='$staff_documentsid' AND type = 'Document' ORDER BY certuploadid DESC";
										$result1 = mysqli_query($dbc, $staff_documents_uploads1);
										$num_rows1 = mysqli_num_rows($result1);
										if($num_rows1 > 0) {
											while($row1 = mysqli_fetch_array($result1)) {
												if(strpos($row1['document_link'], ',') !== FALSE) {
													$new_document_link = str_replace(',','',$row1['document_link']);
													rename('../Staff Documents/download/'.$row1['document_link'], '../Staff Documents/download/'.$new_document_link);
													$row1['document_link'] = $new_document_link;
													mysqli_query($dbc, "UPDATE `staff_documents_uploads` SET `document_link` = '{$row1['document_link']}' WHERE `certuploadid` = '{$row1['certuploadid']}'");
												}
												echo '<ul>';
												echo '<li><a href="'.WEBSITE_URL.'/Staff Documents/download/'.$row1['document_link'].'" target="_blank">'.$row1['document_link'].'</a></li>';
												echo '</ul>';
											}
										}
										echo '</td>';
									}
									if (strpos($sd_value_config, ','."Link".',') !== FALSE) {
										echo '<td data-title="Link">';
										$staff_documents_uploads2 = "SELECT * FROM staff_documents_uploads WHERE staff_documentsid='$staff_documentsid' AND type = 'Link' ORDER BY certuploadid DESC";
										$result2 = mysqli_query($dbc, $staff_documents_uploads2);
										$num_rows2 = mysqli_num_rows($result2);
										if($num_rows2 > 0) {
											$link_no = 1;
											while($row2 = mysqli_fetch_array($result2)) {
												echo '<ul>';
												echo '<li><a target="_blank" href=\''.$row2['document_link'].'\'">Link '.$link_no.'</a></li>';
												echo '</ul>';
												$link_no++;
											}
										}
										echo '</td>';
									}

									if (strpos($sd_value_config, ','."Heading".',') !== FALSE) {
										echo '<td data-title="Heading">' . $row['heading'] . '</td>';
									}
									if (strpos($sd_value_config, ','."Name".',') !== FALSE) {
										echo '<td data-title="Name">' . $row['name'] . '</td>';
									}
									if (strpos($sd_value_config, ','."Fee".',') !== FALSE) {
										echo '<td data-title="Fee">' . $row['fee'] . '</td>';
									}
									if (strpos($sd_value_config, ','."Cost".',') !== FALSE) {
										echo '<td data-title="Cost">' . $row['cost'] . '</td>';
									}
									if (strpos($sd_value_config, ','."Description".',') !== FALSE) {
										echo '<td data-title="Description">' . html_entity_decode($row['description']) . '</td>';
									}
									if (strpos($sd_value_config, ','."Quote Description".',') !== FALSE) {
										echo '<td data-title="Quote Desc.">' . html_entity_decode($row['quote_description']) . '</td>';
									}
									if (strpos($sd_value_config, ','."Invoice Description".',') !== FALSE) {
										echo '<td data-title="Invoice Desc.">' . html_entity_decode($row['invoice_description']) . '</td>';
									}
									if (strpos($sd_value_config, ','."Ticket Description".',') !== FALSE) {
										echo '<td data-title="'.TICKET_NOUN.' Desc">' . html_entity_decode($row['ticket_description']) . '</td>';
									}
									if (strpos($sd_value_config, ','."Final Retail Price".',') !== FALSE) {
										echo '<td data-title="Retail">' . $row['final_retail_price'] . '</td>';
									}
									if (strpos($sd_value_config, ','."Admin Price".',') !== FALSE) {
										echo '<td data-title="Admin Price">' . $row['admin_price'] . '</td>';
									}
									if (strpos($sd_value_config, ','."Wholesale Price".',') !== FALSE) {
										echo '<td data-title="Wholesale">' . $row['wholesale_price'] . '</td>';
									}
									if (strpos($sd_value_config, ','."Commercial Price".',') !== FALSE) {
										echo '<td data-title="Comm. Price">' . $row['commercial_price'] . '</td>';
									}
									if (strpos($sd_value_config, ','."Staff Price".',') !== FALSE) {
										echo '<td data-title="Staff Price">' . $row['staff_price'] . '</td>';
									}
									if (strpos($sd_value_config, ','."Minimum Billable".',') !== FALSE) {
										echo '<td data-title="Min. Billable">' . $row['minimum_billable'] . '</td>';
									}
									if (strpos($sd_value_config, ','."Estimated Hours".',') !== FALSE) {
										echo '<td data-title="Est. Hours">' . $row['estimated_hours'] . '</td>';
									}
									if (strpos($sd_value_config, ','."Actual Hours".',') !== FALSE) {
										echo '<td data-title="Actual Hours">' . $row['actual_hours'] . '</td>';
									}
									if (strpos($sd_value_config, ','."MSRP".',') !== FALSE) {
										echo '<td data-title="MSRP">' . $row['msrp'] . '</td>';
									}

									echo '<td data-title="Function">';
									if(vuaed_visible_function($dbc, 'staff_documents') == 1) {
									echo '<a href=\''.WEBSITE_URL.'/Staff Documents/add_staff_documents.php?staff_documentsid='.$staff_documentsid.'\'>Edit</a> | ';
									echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&staff_documentsid='.$staff_documentsid.'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
									}
									echo '</td>';
								?>
							</tr>
						<?php } ?>
					</table>
				<?php } else {
					echo 'No Records Found.';
				} ?>
            </div>
        <?php } ?>
        <?php $temp_field = 'Treatment Charts'; ?><?php if (strpos($value_config, ','."Treatment Charts".',') !== FALSE) { ?>
            <div class="form-group">
                <table class="table table-bordered">
                    <tr>
                        <th></th>
                        <th>Heading</th>
                        <th>Sub Section Heading</th>
                        <?php $temp_field = 'Treatment Charts Injury'; ?><?php if (strpos($value_config, ','."Treatment Charts Injury".',') !== FALSE) { ?>
                        <th>Injury</th>
                        <?php } ?>
                        <?php $temp_field = 'Treatment Charts Staff'; ?><?php if (strpos($value_config, ','."Treatment Charts Staff".',') !== FALSE) { ?>
                        <th>Filled by Staff</th>
                        <?php } ?>
                        <?php $temp_field = 'Treatment Charts Date'; ?><?php if (strpos($value_config, ','."Treatment Charts Date".',') !== FALSE) { ?>
                        <th>Date</th>
                        <?php } ?>
                        <th>PDF</th>
                    </tr>
                    <?php $treatment_query = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `patientform_pdf` WHERE `patientid` = '".$contactid."' ORDER BY `today_date` DESC"),MYSQLI_ASSOC);
                    foreach($treatment_query as $row) {
                        $patientform = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `patientform` WHERE `patientformid` = '".$row['patientformid']."'"));
                    ?>
                    <tr>
                        <td><?= get_contact($dbc, $row['patientid']) ?></td>
                        <td><?= $patientform['heading'] ?></td>
                        <td><?= $patientform['sub_heading'] ?></td>
                        <?php $temp_field = 'Treatment Charts Injury'; ?><?php if (strpos($value_config, ','."Treatment Charts Injury".',') !== FALSE) {
                        $injury = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `injury` WHERE `injuryid` = '".$patientform['injuryid']."'")); ?>
                        <td><?= $injury['injury_name'] ?> - <?= $injury['injury_date'] ?></td>
                        <?php } ?>
                        <?php $temp_field = 'Treatment Charts Staff'; ?><?php if (strpos($value_config, ','."Treatment Charts Staff".',') !== FALSE) { ?>
                        <td><?= get_contact($dbc, $row['staffid']) ?></td>
                        <?php } ?>
                        <?php $temp_field = 'Treatment Charts Date'; ?><?php if (strpos($value_config, ','."Treatment Charts Date".',') !== FALSE) { ?>
                        <td><?= $row['filled_date'] ?></td>
                        <?php } ?>
                        <td><a href="../Treatment/<?= $row['pdf_path'] ?>" target="_blank">View</a></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } ?>

        <?php $temp_field = 'Incident Reports'; ?><?php if (strpos($value_config, ','."Incident Reports".',') !== FALSE) { ?>
            <div class="form-group">
				<?php
				$display_contact = $contactid;
				$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT incident_report_dashboard FROM field_config_incident_report"));
				$value_config_ir = ','.$get_field_config['incident_report_dashboard'].',';
				$query_check_credentials = "SELECT * FROM incident_report WHERE (CONCAT(',',`contactid`,',') LIKE '%,$display_contact,%' AND `contactid` != '') OR (CONCAT(',',`clientid`,',') LIKE '%,$display_contact,%' AND `clientid` != '') OR (CONCAT(',',`memberid`,',') LIKE '%,$display_contact,%' AND `memberid` != '') OR `programid` = '$display_contact' ORDER BY incidentreportid DESC";
				$result = mysqli_query($dbc, $query_check_credentials);
				$num_rows = mysqli_num_rows($result);

				if($num_rows > 0) {
			        echo "<table id='no-more-tables' class='table table-bordered'>";
			        echo "<tr class='hidden-xs hidden-sm'>";
			            if (strpos($value_config_ir, ','."Project".',') !== FALSE) {
			                echo '<th>'.PROJECT_NOUN.'</th>';
			            }
			            if (strpos($value_config_ir, ','."Ticket".',') !== FALSE) {
			                echo '<th>'.TICKET_NOUN.'</th>';
			            }
			            if (strpos($value_config_ir, ','."Program".',') !== FALSE) {
			                echo '<th>Program</th>';
			            }
			            if (strpos($value_config_ir, ','."Member".',') !== FALSE) {
			                echo '<th>Member</th>';
			            }
			            if (strpos($value_config_ir, ','."Client".',') !== FALSE) {
			                echo '<th>Client</th>';
			            }
			            if (strpos($value_config_ir, ','."Type".',') !== FALSE) {
			                echo '<th>Type</th>';
			            }
			            if (strpos($value_config_ir, ','."Staff".',') !== FALSE) {
			                echo '<th>Staff</th>';
			            }
			            if (strpos($value_config_ir, ','."Follow Up".',') !== FALSE) {
			                echo '<th>Follow Up</th>';
			            }
			            if (strpos($value_config_ir, ','."Date of Happening".',') !== FALSE) {
			                echo '<th>Date of Happening</th>';
			            }
			            if (strpos($value_config_ir, ','."Date Created".',') !== FALSE) {
			                echo '<th>Date Created</th>';
			            }
			            if (strpos($value_config_ir, ','."Location".',') !== FALSE) {
			                echo '<th>Location</th>';
			            }
			            if (strpos($value_config_ir, ','."PDF".',') !== FALSE) {
			                echo '<th>View</th>';
			            }
			        echo "</tr>";

				    while($row = mysqli_fetch_array( $result ))
				    {
				        $contact_list = [];
				        if ($row['contactid'] != '') {
				            $contact_list[$row['contactid']] = get_staff($dbc, $row['contactid']);
				        }
				        $attendance_list = [];
				        if ($row['attendance_staff'] != '') {
				            $attendance_list = explode(',', $row['attendance_staff']);
				        }
				        foreach($attendance_list as $attendee) {
				            $contact_list[] = $attendee;
				        }
				        if ($row['completed_by'] != '') {
				            $contact_list[] = get_contact($dbc, $row['completed_by']);
				        }
				        $contact_list = array_unique($contact_list);
				        $contact_list = implode(', ', $contact_list);

			            echo "<tr>";

			            if (strpos($value_config_ir, ','."Project".',') !== FALSE) {
			                $project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '".$row['projectid']."'"));
			                echo '<td data-title="'.PROJECT_NOUN.'">'.get_project_label($dbc, $project).'</td>';
			            }
			            if (strpos($value_config_ir, ','."Ticket".',') !== FALSE) {
			                $ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$row['ticketid']."'"));
			                echo '<td data-title="'.TICKET_NOUN.'">'.get_ticket_label($dbc, $ticket).'</td>';
			            }
			            if (strpos($value_config_ir, ','."Program".',') !== FALSE) {
			                echo '<td data-title="Program">'.(!empty(get_client($dbc, $row['programid'])) ? get_client($dbc, $row['programid']) : get_contact($dbc, $row['programid'])).'</td>';
			            }
			            if (strpos($value_config_ir, ','."Member".',') !== FALSE) {
			                echo '<td data-title="Member">';
			                    $member_list = [];
			                    foreach(explode(',',$row['memberid']) as $member) {
			                        if($member != '') {
			                            $member_list[] = !empty(get_client($dbc, $member)) ? get_client($dbc, $member) : get_contact($dbc, $member);
			                        }
			                    }
			                    echo implode(', ',$member_list) . '</td>';
			            }
			            if (strpos($value_config_ir, ','."Client".',') !== FALSE) {
			                echo '<td data-title="Client">';
			                    $client_list = [];
			                    foreach(explode(',',$row['clientid']) as $client) {
			                        if($client != '') {
			                            $client_list[] = !empty(get_client($dbc, $client)) ? get_client($dbc, $client) : get_contact($dbc, $client);
			                        }
			                    }
			                    echo implode(', ',$client_list) . '</td>';
			            }
			            if (strpos($value_config_ir, ','."Type".',') !== FALSE) {
			                echo '<td data-title="Type">' . $row['type'] . '</td>';
			            }
			            if (strpos($value_config_ir, ','."Staff".',') !== FALSE) {
			                echo '<td data-title="Staff">' . $contact_list . '</td>';
			            }
			            if (strpos($value_config_ir, ','."Follow Up".',') !== FALSE) {
			                if($row['type'] == 'Near Miss') {
			                    echo '<td data-title="Follow Up">N/A</td>';
			                } else {
			                    echo '<td data-title="Follow Up">' . $row['ir14'] . '</td>';
			                }
			            }
			            if (strpos($value_config_ir, ','."Date of Happening".',') !== FALSE) {
			                echo '<td data-title="Date of Happening">' . $row['date_of_happening'] . '</td>';
			            }
			            if (strpos($value_config_ir, ','."Date Created".',') !== FALSE) {
			                echo '<td data-title="Date Created">' . $row['today_date'] . '</td>';
			            }
			            if (strpos($value_config_ir, ','."Locaiton".',') !== FALSE) {
			                echo '<td data-title="Location">' . $row['location'] . '</td>';
			            }
			            if (strpos($value_config_ir, ','."PDF".',') !== FALSE) {
			                $name_of_file = 'incident_report_'.$row['incidentreportid'].'.pdf';
							echo '<td data-title="PDF"><a href="'.WEBSITE_URL.'/Incident Report/download/'.$name_of_file.'" target="_blank" ><img src="'.WEBSITE_URL.'/img/pdf.png" width="16" height="16" border="0" alt="View">View</a>';
			                if ($row['revision_number'] > 0) {
			                    $revision_dates = explode('*#*', $row['revision_date']);
			                    for ($i = 0; $i < $row['revision_number']; $i++) {
			                        $name_of_file = 'incident_report_'.$row['incidentreportid'].'_'.($i+1).'.pdf';
			                        echo '<br /><a href="'.WEBSITE_URL.'/Incident Report/download/'.$name_of_file.'" target="_blank" ><img src="'.WEBSITE_URL.'/img/pdf.png" width="16" height="16" border="0" alt="view">View R'.($i+1).': '.$revision_dates[$i].'</a>';
			                    }
			                }
			                echo '</td>';
			            }

			            echo "</tr>";
				    }
				    echo '</table>';
				} else {
					echo 'No Records Found.';
				} ?>
            </div>
        <?php } ?>

        <?php /*if (strpos($value_config, ','."Medications Client Profile".',') !== FALSE) { ?>
            <div>Client Profile</div>
        <?php }*/ ?>


  <?php
  $html = array(
    'protocols_start_time' => 'Protocols Start Time',
    'protocols_end_time' => 'Protocols End Time',
    'protocols_completed_by' => 'Protocols Completed By',
    'protocols_signature_box' => 'Protocols Signature Box',
    'protocols_management_completed_by' => 'Protocols Management Completed By',
    'protocols_management_signature_box' => 'Protocols Management Signature Box',

    'routines_start_time' => 'Routines Start Time',
    'routines_end_time' => 'Routines End Time',
    'routines_completed_by' => 'Routines Completed By',
    'routines_signature_box' => 'Routines Signature Box',
    'routines_management_completed_by' => 'Routines Management Completed By',
    'routines_management_signature_box' => 'Routines Management Signature Box',

    'communication_start_time' => 'Communication Start Time',
    'communication_end_time' => 'Communication End Time',
    'communication_completed_by' => 'Communication Completed By',
    'communication_signature_box' => 'Communication Signature Box',
    'communication_management_completed_by' => 'Communication Management Completed By',
    'communication_management_signature_box' => 'Communication Management Signature Box',

    'activities_start_time' => 'Activities Start Time',
    'activities_end_time' => 'Activities End Time',
    'activities_completed_by' => 'Activities Completed By',
    'activities_signature_box' => 'Activities Signature Box',
    'activities_management_completed_by' => 'Activities Management Completed By',
    'activities_management_signature_box' => 'Activities Management Signature Box',

    );
  ?>



<?php foreach($html as $field => $title) { ?>
    <?php $temp_field = 'Business'; ?><?php if (strpos($value_config, ','.$title.',') !== FALSE) { ?>
        <div class="form-group">
        <label for="<?php echo $field; ?>" class="col-sm-4 control-label"><?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { echo "<font color='red'>* </font>"; } ?><?php echo $title; ?></label>
        <div class="col-sm-8"> <?php if (strpos($contacts_mandatory_config, ','."$temp_field".',') !== FALSE) { $required = 'required'; } else { $required = ''; } ?>
          <input <?php echo (strpos($edit_config, ','.$title.',') === false ? 'readonly' : ''); ?> name="<?php echo $field; ?>" value="<?php echo $$field; ?>" type="text" class="form-control <?php echo $required; ?>">
        </div>
        </div>
    <?php } ?>
<?php } ?>

<script type="text/javascript">
  function changePresetPicture(sel) {
  	$('#profile_picture_container').hide();
  	$('#profile_picture_preset_container').show();
  	$('#profile_picture_preset').attr('src','../img/avatars/'+$(sel).val());
  	$('[name="profile_picture"]').val('');
  	$('[name="profile_picture_name"]').val('');
  }

  function colorCodeChange(sel) {
    $('[name="calendar_color"]').val(sel.value);
  }

  function uploadImageClick() {
    $('[name="profile_picture"]').trigger ("click");
  }

  function uploadImage(sel) {
    var files = new FormData();
    files.append("newimage", sel[0].files[0]);

    $.ajax({
      type: "POST",
      url: "<?php echo WEBSITE_URL; ?>/Profile/profile_ajax.php?fill=uploadimage",
      data: files,
      processData: false,
      contentType: false,
      dataType: "html",
      success: function(response) {
        $('[name="profile_picture_name"]').val(response);
        $('[name="profile_picture_name"]').append('<input type="hidden" name="files_to_delete[]" value="' + response + '">');
        $('.profile_picture_div').show();
        $('#profile_picture_crop').prop("src", response);
        $('#profile_picture_crop').load(function() {
          var image_width = $('#profile_picture_crop').width();
          var image_height = $('#profile_picture_crop').height();
          if (image_width > image_height) {
            image_width = image_height;
          } else {
            image_height = image_width;
          }
          $('input[name="x1"]').val(0);
          $('input[name="y1"]').val(0);
          $('input[name="x2"]').val(image_width);
          $('input[name="y2"]').val(image_height);
          $('input[name="image_width"]').val(image_width);
          $('input[name="image_height"]').val(image_height);
          $('#profile_picture_crop').imgAreaSelect({
          	instance: true,
            aspectRatio: '1:1',
            handles: true,
            persistent: true,
            x1: 0,
            y1: 0,
            x2: image_width,
            y2: image_height,
            parent: '#profile_picture_container',
            onSelectEnd: function (img, selection) {
              $('input[name="x1"]').val(selection.x1);
              $('input[name="y1"]').val(selection.y1);
              $('input[name="x2"]').val(selection.x2);
              $('input[name="y2"]').val(selection.y2);
              $('input[name="image_width"]').val(selection.width);
              $('input[name="image_height"]').val(selection.height);
            }
          });
        });
        $('#profile_picture_preset_container').hide();
        $('[name="preset_profile_picture"]').val('');
        $('[name="preset_profile_picture"]').trigger('change.select2');
      }
    });
  }

  function deleteImage() {
    var contactid = <?php echo empty($contactid) ? "''" : $contactid; ?>;
    if (confirm('Are you sure you want to delete this profile picture?')) {
      $.ajax({
        type: 'GET',
        url: '<?php echo WEBSITE_URL; ?>/Profile/profile_ajax.php?fill=deleteimage&contactid=' + contactid,
        success: function(response) {
          window.location.href = 'my_profile.php';
        }
      })
    } else {
      return false;
    }
  }
</script>
<script>
$(function() {
  $(".li-avatar img").click(function() {
    $(".li-avatar img").not(this).removeClass("hover");
    $("#"+this.id).addClass("hover");
    $("#preset_profile_picture").val(jQuery("#"+this.id).attr("value"));
    $("#current-avatar").attr("src", "../img/avatars/"+jQuery("#"+this.id).attr("value"));
  });
});
</script>
<script>
function presave() {
  var flag = 0;
  var firsttarget = '';
  $('.required').each(function() {
      var target = this;
        if($(target).val() != null && $(target).val().length === 0) {
          if(flag == 0) {
            $firsttarget = $(this);
          }

          if($(target).is('select')) {
            var select2 = $(target).next('.select2');
            $(select2).find('.select2-selection').css('background-color', 'red');
            $(select2).find('.select2-selection__placeholder').css('color', 'white');
          } else {
            $(target).css('background-color', 'red');
          }

          flag = 1;
      }
      else {
        if($(target).is('select')) {
          var select2 = $(target).next('.select2');
          $(select2).find('.select2-selection').css('background-color', 'white');
        } else {
          $(target).css('background-color', 'white');
        }
      }
  });

  var currenttop = 30;
  if(flag == 1) {
      alert("Please fill in the required fields");
      $('.main-screen .main-screen').scrollTop($('.standard-body-content').scrollTop() + currenttop - 10 - 30);
      return false;
  }

  return true;
}
</script>
<style>
  .avatar ul { list-style: none; }
  .avatar ul li { display: inline; }
  .avatar ul li img { border: 2px solid white; cursor: pointer; }
  .avatar ul li img:hover { border: 2px solid black; }
  .avatar ul li img.hover { border: 2px solid black; }
</style>
