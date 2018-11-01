    <?php if (strpos($value_config, ','."Business".',') !== FALSE) { ?>
    <div class="form-group clearfix completion_date">
        <label for="first_name" class="col-sm-4 control-label text-right"><?php echo (get_software_name() == 'breakthebarrier' ? 'Program/Site' : 'Business'); ?><span class="brand-color">*</span>:</label>
        <div class="col-sm-8">
            <select <?php echo (strpos($edit_config, ','."Business".',') === false ? 'readonly ' : ''); ?>name="businessid" data-placeholder="Choose a <?php echo (get_software_name() == 'breakthebarrier' ? 'Program/Site' : 'Business'); ?>..." data-no_results_text="Add a <?php echo (get_software_name() == 'breakthebarrier' ? 'Program/Site' : 'Business'); ?>:" class="chosen-select-deselect form-control" width="380" id="businesssiteid">
                <option value=''></option>
            </select>
        </div>
    </div>
    <?php } ?>

  <?php if (strpos($value_config, ','."Contact Category".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="position[]" class="col-sm-4 control-label">Contact Category:</label>
    <div class="col-sm-8">
            <select data-placeholder="Choose a Category..." id="contact_category" name="category_contact" class="chosen-select-deselect form-control" width="380">
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
    </div>
  </div>

<div class="form-group" id="new_category" style="display: none;">
    <label for="travel_task" class="col-sm-4 control-label">New Category: </label>
    <div class="col-sm-8">
        <input name="new_category" type="text" class="form-control"/>
    </div>
</div>

  <?php } ?>

        <?php if (strpos($value_config, ','."Business Sites".',') !== FALSE) { ?>
		<div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label">Sites:</label>
		  <div class="col-sm-8">
			<select data-placeholder="Choose a Sites..." name="siteid" id="siteid"  class="chosen-select-deselect form-control" width="580">
			  <option value=""></option>
              <?php
                    $query = mysqli_query($dbc,"SELECT contactid, site_name FROM contacts WHERE category='Sites'");
                    echo '<option value=""></option>';
                    while($row = mysqli_fetch_array($query)) {
                        echo "<option ".($siteid == $row['contactid'] ? 'selected' : '')." value='".$row['contactid']."'>".$row['site_name'].'</option>';
                    }
              ?>
			</select>
		  </div>
		</div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Site LSD".',') !== FALSE) { ?>
		<div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label">Site LSD:</label>
		  <div class="col-sm-8">
			<input type="text" name="lsd" value="<?= $lsd ?>" class="form-control">
		  </div>
		</div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Name".',') !== FALSE) { ?>
			<?php
			if(isset($_GET['name']) && $name == '') {
				$name = mysqli_real_escape_string($dbc,urldecode($_GET['name']));
			} ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label"><?php echo $url_category;?> Name:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Name".',') === false ? 'readonly ' : ''); ?>name="name" value="<?php echo $name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Customer(Client/Customer/Business)".',') !== FALSE) { ?>
        <div class="form-group">
            <label for="site_name" class="col-sm-4 control-label">Customer:</label>
            <div class="col-sm-8">
            <select data-placeholder="Choose a Customer..." name="siteclientid" class="chosen-select-deselect form-control" width="380">
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
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Site Number".',') !== FALSE) { ?>
          <div class="form-group">
          <label for="site_number" class="col-sm-4 control-label">Site #:</label>
          <div class="col-sm-8">
          <input <?php echo (strpos($edit_config, ','."Site Number".',') === false ? 'readonly' : ''); ?> name="site_number" value="<?php echo $site_number; ?>" type="text" class="form-control">
          </div>
          </div>
        <?php } ?>


          <?php if(strpos($value_config,'Site Name (Location)') !== false) { ?>
          <div class="form-group">
            <label for="site_name" class="col-sm-4 control-label">Site Name	(Location):</label>
            <div class="col-sm-8">
              <input name="site_name" type="text" value="<?php echo $site_name; ?>" id="site_name" class="form-control" />
            </div>
          </div>
          <?php } ?>

		<?php if (strpos($value_config, ','."First Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">First Name:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."First Name".',') === false ? 'readonly ' : ''); ?>name="first_name" value="<?php echo $first_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Last Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Last Name:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Last Name".',') === false ? 'readonly ' : ''); ?>name="last_name" value="<?php echo $last_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Preferred Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="prefer_name" class="col-sm-4 control-label">Preferred Name:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Preferred Name".',') === false ? 'readonly ' : ''); ?>name="prefer_name" value="<?php echo $prefer_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

      <?php if (strpos($value_config, ','."Nick Name".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Nick Name:</label>
        <div class="col-sm-8">
          <input name="nick_name" value="<?php echo $nick_name; ?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Gender".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="office_phone" class="col-sm-4 control-label">Gender:</label>
        <div class="col-sm-8">
          <input <?php if ($gender == 'Male') { echo " checked"; } ?> name="gender" type="radio" value="Male" class="form" /> Male
          <input <?php if ($gender == 'Female') { echo " checked"; } ?> name="gender" type="radio" value="Female" class="form" /> Female
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."License".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">License#:</label>
        <div class="col-sm-8">
          <input name="license" value="<?php echo $license; ?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Insurer".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="position[]" class="col-sm-4 control-label">Insurer:</label>
        <div class="col-sm-8">
            <select data-placeholder="Choose a Insurer..." name="name" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <?php
                $result_insurers = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT `contactid`, `name` FROM contacts WHERE category='Insurer' AND deleted=0"),MYSQLI_ASSOC));
				foreach($result_insurers as $insurer_id) {
					$insurer_name = get_client($dbc, $insurer_id);
                    echo "<option ".($name == $insurer_name ? 'selected' : '')." value='". $insurer_name."'>".$insurer_name.'</option>';
                }
              ?>
            </select>
        </div>
      </div>
      <?php } ?>

		<?php if (strpos($value_config, ','."Assigned Staff".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Assigned Staff:</label>
            <div class="col-sm-8">
				<select name="assign_staff" class="form-control chosen-select-deselect"><option></option>
					<?php $result_staff = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT `contactid`, `first_name`, `last_name` FROM contacts WHERE category='Staff' AND deleted=0"),MYSQLI_ASSOC));
					foreach($result_staff as $resultstaffid) {
						echo "<option ".($assign_staff == $resultstaffid ? 'selected' : '')." value='". $resultstaffid."'>".get_contact($dbc, $resultstaffid).'</option>';
					} ?>
				</select>
            </div>
            </div>
        <?php } ?>

      <?php if (strpos($value_config, ','."Credential".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Credentials:</label>
        <div class="col-sm-8">
            <input name="credential" type="text" maxlength="50" value="<?php echo $credential; ?>" class="form-control last-name" />
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Alberta Health Care No".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Alberta Health Care No:</label>
        <div class="col-sm-8">
          <input name="health_care_no" value="<?php echo $health_care_no; ?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

   <?php if (strpos($value_config, ','."Scheduled Days/Hours".',') !== FALSE && !empty($_GET['contactid'])) { ?>
  <div class="form-group">
    <label for="office_country" class="col-sm-4 control-label">Scheduled Days/Hours:</label>
    <div class="col-sm-8">
      <input name="schedule_days[]" id='day_0' onclick="selectHour(this);" type="checkbox" value="0" <?php if (strpos($schedule_days, '0') !== false) { echo  'checked="checked"'; } ?> class="form-control"  style="display:inline-block; width:auto; height: auto;"/> <span style="display:inline-block;">Sunday&nbsp;</span>&nbsp;&nbsp;
      <input name="scheduled_hours[]" id='hour_0' placeholder="Ex. 9AM-5PM, 3PM-5PM" type="text" value="<?php echo $scheduled_hours[0]; ?>" class="form-control1"/><br>
      <input name="schedule_days[]" id='day_1' onclick="selectHour(this);" type="checkbox" value="1" <?php if (strpos($schedule_days, '1') !== false) { echo  'checked="checked"'; } ?> class="form-control"  style="display:inline-block; width:auto; height: auto;"/> <span style="display:inline-block;">Monday&nbsp;</span>&nbsp;&nbsp;
      <input name="scheduled_hours[]" id='hour_1' placeholder="Ex. 9AM-5PM, 3PM-5PM" type="text" value="<?php echo $scheduled_hours[1]; ?>" class="form-control1"/><br>
      <input name="schedule_days[]" id='day_2' onclick="selectHour(this);" type="checkbox" value="2" <?php if (strpos($schedule_days, '2') !== false) { echo  'checked'; } ?> class="form-control" style="display:inline-block; width:auto; height: auto;"/> <span style="display:inline-block;">Tuesday&nbsp;</span>&nbsp;&nbsp;
      <input name="scheduled_hours[]" id='hour_2' placeholder="Ex. 9AM-5PM, 3PM-5PM" type="text" value="<?php echo $scheduled_hours[2]; ?>" class="form-control1"/><br>
      <input name="schedule_days[]" id='day_3' onclick="selectHour(this);" type="checkbox" value="3" <?php if (strpos($schedule_days, '3') !== false) { echo  'checked'; } ?> class="form-control" style="display:inline-block; width:auto; height: auto;"/> <span style="display:inline-block;">Wednesday&nbsp;</span>&nbsp;&nbsp;
      <input name="scheduled_hours[]" id='hour_3' placeholder="Ex. 9AM-5PM, 3PM-5PM" type="text" value="<?php echo $scheduled_hours[3]; ?>" class="form-control1"/><br>
      <input name="schedule_days[]" id='day_4' onclick="selectHour(this);" type="checkbox" value="4" <?php if (strpos($schedule_days, '4') !== false) { echo  'checked'; } ?> class="form-control" style="display:inline-block; width:auto; height: auto;"/> <span style="display:inline-block;">Thursday&nbsp;</span>&nbsp;&nbsp;
      <input name="scheduled_hours[]" id='hour_4' placeholder="Ex. 9AM-5PM, 3PM-5PM" type="text" value="<?php echo $scheduled_hours[4]; ?>" class="form-control1"/><br>
      <input name="schedule_days[]" id='day_5' onclick="selectHour(this);" type="checkbox" value="5" class="form-control" <?php if (strpos($schedule_days, '5') !== false) { echo  'checked'; } ?> style="display:inline-block; width:auto; height: auto;"/> <span style="display:inline-block;">Friday&nbsp;</span>&nbsp;&nbsp;
      <input name="scheduled_hours[]" id='hour_5' placeholder="Ex. 9AM-5PM, 3PM-5PM" type="text" value="<?php echo $scheduled_hours[5]; ?>" class="form-control1"/><br>
      <input name="schedule_days[]" id='day_6' onclick="selectHour(this);" type="checkbox" value="6" <?php if (strpos($schedule_days, '6') !== false) { echo  'checked'; } ?> class="form-control"  style="display:inline-block; width:auto; height: auto;"/> <span style="display:inline-block;">Saturday&nbsp;</span>&nbsp;&nbsp;
      <input name="scheduled_hours[]" id='hour_6' placeholder="Ex. 9AM-5PM, 3PM-5PM" type="text" value="<?php echo $scheduled_hours[6]; ?>" class="form-control1"/>
    </div>
  </div>
  <?php } ?>

    <?php if (strpos($value_config, ','."Profile Link".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Profile Link:</label>
    <div class="col-sm-8">
      <input name="profile_link" value="<?php echo $profile_link; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>

      <?php if (strpos($value_config, ','."Correspondence Language".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Correspondence Language:</label>
        <div class="col-sm-8">
          <input name="correspondence_language" value="<?php echo $correspondence_language; ?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

       <?php if (strpos($value_config, ','."Accepts to receive emails".',') !== FALSE) { ?>
        <div class="form-group">
            <label for="phone_number" class="col-sm-4 control-label">Agrees to receive emails:</label>
            <div class="col-sm-8">
                <select data-placeholder="Choose a Option..."  name="accepts_receive_emails" id="equipmentid" class="chosen-select-deselect form-control" width="380">
                    <option value=""></option>
                    <option <?php if ($accepts_receive_emails == "Yes") { echo " selected"; } ?> value="Yes">Yes</option>
                    <option <?php if ($accepts_receive_emails == "No") { echo " selected"; } ?> value="No">No</option>
                </select>
            </div>
        </div>

        <?php } ?>

		<?php if (strpos($value_config, ','."Role".',') !== FALSE) { ?>
			<?php if(strpos(','.$role.',',',super,') !== false): ?>
				<input type="hidden" name="role[]" value="<?php echo $role; ?>">
			<?php else: ?>
				<div class="form-group">
					<label for="travel_task" class="col-sm-4 control-label">Select a Security Level:</label>
					<div class="col-sm-8">
					<select <?php echo (strpos($edit_config, ','."Role".',') === false ? 'readonly ' : ''); ?>name="role[]" multiple data-placeholder="Select a Security Level" class="chosen-select-deselect form-control" width="380">
						<?php
						$selected = '';
						$sql=mysqli_query($dbc,"SELECT * FROM  security_level");
						$on_security = '';

						while ($fieldinfo=mysqli_fetch_field($sql))
						{
							$field_name = $fieldinfo->name;
							$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM security_level WHERE $field_name LIKE '%*turn_on*%'"));
							if($get_config[$field_name]) {
								$on_security[] = $field_name;
							}
						}
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
				  </div>
				</div>
			<?php endif; ?>
        <?php } ?>

    <?php if (strpos($value_config, ','."Region".',') !== FALSE) { ?>
            <div class="form-group">
                <label for="fax_number" class="col-sm-4 control-label">Region:</label>
                <div class="col-sm-8">
          <?php if(strpos($edit_config, ',Region,') !== FALSE) { ?>
                    <select data-placeholder="Choose a region..." name="region" width="380" class="chosen-select-deselect">
                      <option value=""></option>
                      <?php
                        $tabs = get_config($dbc, FOLDER_NAME.'_region');
                        $each_tab = explode(',', $tabs);
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
            <input type="text" readonly name="region" value="<?= $region ?>" class="form-control">
          <?php } ?>
                </div>
            </div>

        <?php } ?>

		<?php if (strpos($value_config, ','."Division".',') !== FALSE) { ?>
            <div class="form-group">
                <label for="fax_number"	class="col-sm-4	control-label">Division:</label>
                <div class="col-sm-8">
                    <select data-placeholder="Choose a classification..." <?php echo (strpos($edit_config, ','."Division".',') === false ? 'readonly ' : ''); ?>name="classification" class="chosen-select-deselect form-control" width="380">
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
                </div>
            </div>

        <?php } ?>

		<?php if (strpos($value_config, ','."User Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Username:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."User Name".',') === false ? 'readonly ' : ''); ?>name="user_name" value="<?php echo $user_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Password".',') !== FALSE) { ?>
			<input type="text" name="nosubmitusernamefield" value="" style="display:none;"><input type="password" name="nosubmitpasswordfield" value="" style="display:none;">
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Password:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Password".',') === false ? 'readonly ' : ''); ?>name="password" value="<?php echo $password; ?>" type="password" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Name on Account".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Name on Account:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Name on Account".',') === false ? 'readonly ' : ''); ?>name="name_on_account" value="<?php echo $name_on_account; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Operating As".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Operating As:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Operating As".',') === false ? 'readonly ' : ''); ?>name="operating_as" value="<?php echo $operating_as; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Rating".',') !== FALSE) { ?>
            <div class="form-group">
                <label for="fax_number"	class="col-sm-4	control-label">Rating:</label>
                <div class="col-sm-8">
                    <select data-placeholder="Choose a Rating..." <?php echo (strpos($edit_config, ','."Rating".',') === false ? 'readonly ' : ''); ?>name="rating" class="chosen-select-deselect form-control" width="380">
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
                </div>
            </div>

        <?php } ?>

		<?php if (strpos($value_config, ','."Emergency Contact".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Emergency Contact:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Emergency Contact".',') === false ? 'readonly ' : ''); ?>name="emergency_contact" value="<?php echo $emergency_contact; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Occupation".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Occupation:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Occupation".',') === false ? 'readonly ' : ''); ?>name="occupation" value="<?php echo $occupation; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Office Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Office Phone:<br><em>(Check box if contact is primary)</em></label>
            <div class="col-sm-8">
              <input type="checkbox" value="O" <?php if (strpos($primary_contact, 'O') !== FALSE) { echo " checked"; } ?> <?php echo (strpos($edit_config, ','."Office Phone".',') === false ? 'readonly ' : ''); ?>name="primary_contact[]"><input <?php echo (strpos($edit_config, ','."Office Phone".',') === false ? 'readonly ' : ''); ?>name="office_phone" value="<?php echo $office_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Cell Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Cell Phone:<br><em>(Check box if contact is primary)</em></label>
            <div class="col-sm-8">
              <input type="checkbox" value="C" <?php if (strpos($primary_contact, 'C') !== FALSE) { echo " checked"; } ?> <?php echo (strpos($edit_config, ','."Cell Phone".',') === false ? 'readonly ' : ''); ?>name="primary_contact[]"><input <?php echo (strpos($edit_config, ','."Cell Phone".',') === false ? 'readonly ' : ''); ?>name="cell_phone" value="<?php echo $cell_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Home Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Home Phone:<br><em>(Check box if contact is primary)</em></label>
            <div class="col-sm-8">
              <input type="checkbox" value="H" <?php if (strpos($primary_contact, 'H') !== FALSE) { echo " checked"; } ?> <?php echo (strpos($edit_config, ','."Home Phone".',') === false ? 'readonly ' : ''); ?>name="primary_contact[]"><input <?php echo (strpos($edit_config, ','."Home Phone".',') === false ? 'readonly ' : ''); ?>name="home_phone" value="<?php echo $home_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Fax".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Fax:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Fax".',') === false ? 'readonly ' : ''); ?>name="fax" value="<?php echo $fax; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Email Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Email Address:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Email Address".',') === false ? 'readonly ' : ''); ?>name="email_address" value="<?php echo $email_address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Company Email Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="office_email" class="col-sm-4 control-label">Company Email Address:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Company Email Address".',') === false ? 'readonly ' : ''); ?>name="office_email" value="<?php echo $office_email; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Website".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Website:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Website".',') === false ? 'readonly ' : ''); ?>name="website" value="<?php echo $website; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Display Name".',') !== FALSE) { ?>
          <div class="form-group">
            <label for="site_name" class="col-sm-4 control-label">Display Name:</label>
            <div class="col-sm-8">
              <input name="display_name" type="text" value="<?php echo $display_name; ?>" class="form-control" />
            </div>
          </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Customer Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Customer Address:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Customer Address".',') === false ? 'readonly ' : ''); ?>name="customer_address" value="<?php echo $customer_address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Referred By".',') !== FALSE) { ?>
            <div class="form-group">
                <label for="phone_number" class="col-sm-4 control-label">Referred By:</label>
                <div class="col-sm-8">
                    <select data-placeholder="Choose a Referred By..."  name="referred_by" id="equipmentid" class="chosen-select-deselect form-control" width="380">
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
                </div>
            </div>

            <div class="form-group clearfix orientation_date">
                <label for="first_name" class="col-sm-4 control-label text-right">Referred By Name:</label>
                <div class="col-sm-8">
                    <input name="referred_by_name" type="text" class="form-control" value="<?php echo $referred_by_name; ?>"></p>
                </div>
            </div>


        <?php } ?>

		<?php if (strpos($value_config, ','."Company".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Company:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Company".',') === false ? 'readonly ' : ''); ?>name="company" value="<?php echo $company; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Position".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Position:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Position".',') === false ? 'readonly ' : ''); ?>name="position" value="<?php echo $position; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Title".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Title:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Title".',') === false ? 'readonly ' : ''); ?>name="title" value="<?php echo $title; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Show/Hide User".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="show_hide_user" class="col-sm-4 control-label">Show/Hide User:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Show/Hide User".',') === FALSE ? 'readonly ' : ''); ?> name="show_hide_user" <?php echo '1' == $show_hide_user ? "checked " : "checked "; ?> value="1" type="radio"> Show
              <input <?php echo (strpos($edit_config, ','."Show/Hide User".',') === FALSE ? 'readonly ' : ''); ?> name="show_hide_user" <?php echo '0' == $show_hide_user ? "checked " : ""; ?> value="0" type="radio"> Hide
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."LinkedIn".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">LinkedIn:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."LinkedIn".',') === false ? 'readonly ' : ''); ?>name="linkedin" value="<?php echo $linkedin; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>
		<?php if (strpos($value_config, ','."Facebook".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="facebook" class="col-sm-4 control-label">Facebook:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Facebook".',') === false ? 'readonly ' : ''); ?>name="facebook" value="<?php echo $facebook; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>
		<?php if (strpos($value_config, ','."Twitter".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Twitter:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Twitter".',') === false ? 'readonly ' : ''); ?>name="twitter" value="<?php echo $twitter; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Client Tax Exemption".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Client Tax Exemption:</label>
            <div class="col-sm-8">
				<!--<input name="client_tax_exemption" value="<?php //echo $client_tax_exemption; ?>" type="text" class="form-control">-->

                <select data-placeholder="Choose an Option..." <?php echo (strpos($edit_config, ','."Client Tax Exemption".',') === false ? 'readonly ' : ''); ?>name="client_tax_exemption" class="chosen-select-deselect form-control" width="380">
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

            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Tax Exemption Number".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Client Tax Exemption Number:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Tax Exemption Number".',') === false ? 'readonly ' : ''); ?>name="tax_exemption_number" value="<?php echo $tax_exemption_number; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."DUNS".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">DUNS:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."DUNS".',') === false ? 'readonly ' : ''); ?>name="duns" value="<?php echo $duns; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."CAGE".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">CAGE:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."CAGE".',') === false ? 'readonly ' : ''); ?>name="cage" value="<?php echo $cage; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."SIN".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="sin" class="col-sm-4 control-label">SIN:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."SIN".',') === false ? 'readonly ' : ''); ?>name="sin" value="<?php echo $sin; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Employee Number".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="employee_num" class="col-sm-4 control-label">Employee #:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Employee Number".',') === false ? 'readonly ' : ''); ?>name="employee_num" value="<?php echo $employee_num; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Self Identification".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Self Identification:</label>
            <div class="col-sm-8">
                <select data-placeholder="Choose an Option..." id="self_identification" <?php echo (strpos($edit_config, ','."Self Identification".',') === false ? 'readonly ' : ''); ?>name="self_identification" class="chosen-select-deselect form-control" width="380">
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
            </div>
            </div>

           <div class="form-group" id="new_self_identification" style="display: none;">
            <label for="travel_task" class="col-sm-4 control-label">
            </label>
            <div class="col-sm-8">
                <input <?php echo (strpos($edit_config, ','."Self Identification".',') === false ? 'readonly ' : ''); ?>name="new_self_identification" type="text" class="form-control"/>
            </div>
          </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."AISH Card#".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">AISH Card#:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."AISH Card#".',') === false ? 'readonly ' : ''); ?>name="aish_card_no" value="<?php echo $aish_card_no; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."License Plate #".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">License Plate #:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."License Plate #".',') === false ? 'readonly ' : ''); ?>name="license_plate_no" value="<?php echo $license_plate_no; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."CARFAX".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">CARFAX:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."CARFAX".',') === false ? 'readonly ' : ''); ?>name="carfax" value="<?php echo $carfax; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="address" class="col-sm-4 control-label">Address:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Address".',') === false ? 'readonly ' : ''); ?>name="address" value="<?php echo $address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Mailing Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Mailing Address:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Mailing Address".',') === false ? 'readonly ' : ''); ?>name="mailing_address" value="<?php echo $mailing_address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Business Address".',') !== FALSE) { ?>
            <!--
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Business Address:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Business Address".',') === false ? 'readonly ' : ''); ?>name="business_address" value="<?php echo $business_address; ?>" type="text" class="form-control">
            </div>
            </div>
            -->

          <div class="form-group">
            <label for="business_street" class="col-sm-4 control-label">Street:</label>
            <div class="col-sm-8">
              <input name="business_street" maxlength="200" type="text" value="<?php echo $business_street; ?>" id="office_street" class="form-control office_street" />
            </div>
          </div>

          <div class="form-group">
            <label for="business_city" class="col-sm-4 control-label">City:</label>
            <div class="col-sm-8">
              <input name="business_city" maxlength="15" id="office_city" value="<?php echo $business_city; ?>" type="text" class="form-control office_city" />
            </div>
          </div>

          <div class="form-group">
            <label for="business_state" class="col-sm-4 control-label">State / Province:</label>
            <div class="col-sm-8">
              <input name="business_state" maxlength="15" id="office_state" value="<?php echo $business_state; ?>" type="text" class="form-control office_state" />
            </div>
          </div>

          <div class="form-group">
            <label for="" class="col-sm-4 control-label">Country:</label>
            <div class="col-sm-8">
              <input name="business_country" maxlength="15" id="office_country" value="<?php echo $business_country; ?>" type="text" class="form-control office_country" />
            </div>
          </div>

          <div class="form-group">
            <label for="business_zip" class="col-sm-4 control-label">Zip / Postal Code:</label>
            <div class="col-sm-8">
              <input name="business_zip" maxlength="10" id="office_zip" value="<?php echo $business_zip; ?>" type="text" class="form-control office_zip" />
            </div>
          </div>

        <?php } ?>

		<?php if (strpos($value_config, ','."Postal Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Postal Code:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Postal Code".',') === false ? 'readonly ' : ''); ?>name="postal_code" value="<?php echo $postal_code; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Zip Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Zip/Postal Code:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Zip Code".',') === false ? 'readonly ' : ''); ?>name="zip_code" value="<?php echo $zip_code; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."City".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">City:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."City".',') === false ? 'readonly ' : ''); ?>name="city" value="<?php echo $city; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Province".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Province:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Province".',') === false ? 'readonly ' : ''); ?>name="province" value="<?php echo $province; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."State".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">State:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."State".',') === false ? 'readonly ' : ''); ?>name="state" value="<?php echo $state; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Country".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Country:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Country".',') === false ? 'readonly ' : ''); ?>name="country" value="<?php echo $country; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Ship To Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Ship To Address:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Ship To Address".',') === false ? 'readonly ' : ''); ?>name="ship_to_address" value="<?php echo $ship_to_address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Ship City".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Ship City:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Ship City".',') === false ? 'readonly ' : ''); ?>name="ship_city" value="<?php echo $ship_city; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>
		<?php if (strpos($value_config, ','."Ship State".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Ship State:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Ship State".',') === false ? 'readonly ' : ''); ?>name="ship_state" value="<?php echo $ship_state; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>
		<?php if (strpos($value_config, ','."Ship Zip".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Ship Zip:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Ship Zip".',') === false ? 'readonly ' : ''); ?>name="ship_zip" value="<?php echo $ship_zip; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>
		<?php if (strpos($value_config, ','."Ship Country".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Ship Country:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Ship Country".',') === false ? 'readonly ' : ''); ?>name="ship_country" value="<?php echo $ship_country; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Google Maps Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Google Maps Address:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Google Maps Address".',') === false ? 'readonly ' : ''); ?>name="google_maps_address" value="<?php echo $google_maps_address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."City Part".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">City Part:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."City Part".',') === false ? 'readonly ' : ''); ?>name="city_part" value="<?php echo $city_part; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Account Number".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Account Number:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Account Number".',') === false ? 'readonly ' : ''); ?>name="account_number" value="<?php echo $account_number; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Payment Type".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Payment Type:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Payment Type".',') === false ? 'readonly ' : ''); ?>name="payment_type" value="<?php echo $payment_type; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Payment Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Payment Name:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Payment Name".',') === false ? 'readonly ' : ''); ?>name="payment_name" value="<?php echo $payment_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Payment Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Payment Address:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Payment Address".',') === false ? 'readonly ' : ''); ?>name="payment_address" value="<?php echo $payment_address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Payment City".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Payment City:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Payment City".',') === false ? 'readonly ' : ''); ?>name="payment_city" value="<?php echo $payment_city; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Payment State".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Payment State:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Payment State".',') === false ? 'readonly ' : ''); ?>name="payment_state" value="<?php echo $payment_state; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Payment Postal Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Payment Postal Code:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Payment Postal Code".',') === false ? 'readonly ' : ''); ?>name="payment_postal_code" value="<?php echo $payment_postal_code; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Payment Zip Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Payment Zip/Postal Code:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Payment Zip Code".',') === false ? 'readonly ' : ''); ?>name="payment_zip_code" value="<?php echo $payment_zip_code; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."GST #".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">GST #:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."GST #".',') === false ? 'readonly ' : ''); ?>name="gst_no" value="<?php echo $gst_no; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."PST #".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">PST #:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."PST #".',') === false ? 'readonly ' : ''); ?>name="pst_no" value="<?php echo $pst_no; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Vendor GST #".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Vendor GST #:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Vendor GST #".',') === false ? 'readonly ' : ''); ?>name="vendor_gst_no" value="<?php echo $vendor_gst_no; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Payment Information".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Payment Information:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Payment Information".',') === false ? 'readonly ' : ''); ?>name="payment_information" value="<?php echo $payment_information; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Pricing Level".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Pricing Level:</label>
            <div class="col-sm-8">
                <select data-placeholder="Choose Pricing..." <?php echo (strpos($edit_config, ','."Pricing Level".',') === false ? 'readonly ' : ''); ?>name="pricing_level" class="chosen-select-deselect form-control" width="380">
                    <option value="">Please Select</option>
                    <option <?php if ($pricing_level == 'Client Price') { echo " selected"; } ?> value="Client Price">Client Price</option>
                    <option <?php if ($pricing_level == 'Admin Price') { echo " selected"; } ?> value="Admin Price">Admin Price</option>
                    <option <?php if ($pricing_level == 'Commercial Price') { echo " selected"; } ?> value="Commercial Price">Commercial Price</option>
                    <option <?php if ($pricing_level == 'Wholesale Price') { echo " selected"; } ?> value="Wholesale Price">Wholesale Price</option>
                    <option <?php if ($pricing_level == 'Final Retail Price') { echo " selected"; } ?> value="Final Retail Price">Final Retail Price</option>
                    <option <?php if ($pricing_level == 'Preferred Price') { echo " selected"; } ?> value="Preferred Price">Preferred Price</option>
                    <option <?php if ($pricing_level == 'Web Price') { echo " selected"; } ?> value="Web Price">Web Price</option>
                </select>
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Unit #".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Unit #:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Unit #".',') === false ? 'readonly ' : ''); ?>name="unit_no" value="<?php echo $unit_no; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Bay #".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Bay #:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Bay #".',') === false ? 'readonly ' : ''); ?>name="bay_no" value="<?php echo $bay_no; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Option to Renew".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Option to Renew:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Option to Renew".',') === false ? 'readonly ' : ''); ?>name="option_to_renew" value="<?php echo $option_to_renew; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Lease Term - # of years".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Lease Term - # of years:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Lease Term - # of years".',') === false ? 'readonly ' : ''); ?>name="lease_term_no_of_years" value="<?php echo $lease_term_no_of_years; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Commercial Insurer".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Commercial Insurer:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Commercial Insurer".',') === false ? 'readonly ' : ''); ?>name="commercial_insurer" value="<?php echo $commercial_insurer; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Residential Insurer".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">Residential Insurer:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Residential Insurer".',') === false ? 'readonly ' : ''); ?>name="residential_insurer" value="<?php echo $residential_insurer; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."WCB #".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="company_name" class="col-sm-4 control-label">WCB #:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."WCB #".',') === false ? 'readonly ' : ''); ?>name="wcb_no" value="<?php echo $wcb_no; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

		<?php if (strpos($value_config, ','."Status".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="status" class="col-sm-4 control-label">Status:</label>
            <div class="col-sm-8">
			  <input type="radio" <?php echo (strpos($edit_config, ','."Status".',') === false ? 'readonly ' : ''); ?>name="status" value="1" <?php if($status==1){ echo 'checked'; } ?>> Active
			  <input type="radio" <?php echo (strpos($edit_config, ','."Status".',') === false ? 'readonly ' : ''); ?>name="status" value="0" <?php if($status==0){ echo 'checked'; } ?>> Deactive
            </div>
            </div>
        <?php } ?>

		<?php if ( strpos ( $value_config, ',' . "Credit Card on File" . ',' ) !== FALSE ) { ?>
            <div class="form-group">
				<label for="company_name" class="col-sm-4 control-label">Credit Card on File:</label>
				<div class="col-sm-8">
					<select data-placeholder="Choose One..." <?php echo (strpos($edit_config, ','."Credit Card on File".',') === false ? 'readonly ' : ''); ?>name="cc_on_file" class="chosen-select-deselect form-control" width="380">
						<option <?php if ( $cc_on_file == 'Yes') { echo " selected"; } ?> value="Yes">Yes</option>
						<option <?php if ( $cc_on_file == 'No') { echo " selected"; } ?> value="No">No</option>
					</select>
				</div>
            </div>
        <?php } ?>

		<?php if ( strpos ($value_config, ',' . "Intake Form" . ',') !== FALSE ) { ?>
            <div class="form-group">
				<label for="intakeid" class="col-sm-4 control-label">Intake Form ID:</label>
				<div class="col-sm-8">
					<input class="form-control" type="text" name="intakeid" value="<?php echo $intakeid; ?>" />
				</div>
            </div>
        <?php } ?>

        <?php // Client Information ?>

        <?php if (strpos($value_config, ','."Client First Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_first_name" class="col-sm-4 control-label">Client First Name:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Client First Name".',') === false ? 'readonly ' : ''); ?>name="client_first_name" value="<?php echo $client_first_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Client Last Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_last_name" class="col-sm-4 control-label">Client Last Name:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Client Last Name".',') === false ? 'readonly ' : ''); ?>name="client_last_name" value="<?php echo $client_last_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Client Work Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_work_phone" class="col-sm-4 control-label">Client Work Phone:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Client Work Phone".',') === false ? 'readonly ' : ''); ?>name="client_work_phone" value="<?php echo $client_work_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Client Home Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_home_phone" class="col-sm-4 control-label">Client Home Phone:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Client Home Phone".',') === false ? 'readonly ' : ''); ?>name="client_home_phone" value="<?php echo $client_home_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Client Cell Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_cell_phone" class="col-sm-4 control-label">Client Cell Phone:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Client Cell Phone".',') === false ? 'readonly ' : ''); ?>name="client_cell_phone" value="<?php echo $client_cell_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Client Fax".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_fax" class="col-sm-4 control-label">Client Fax:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Client Fax".',') === false ? 'readonly ' : ''); ?>name="client_fax" value="<?php echo $client_fax; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Client Email Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_email_address" class="col-sm-4 control-label">Client Email Address:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Client Email Address".',') === false ? 'readonly ' : ''); ?>name="client_email_address" value="<?php echo $client_email_address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Client Date of Birth".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_date_of_birth" class="col-sm-4 control-label">Client Date of Birth:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Client Date of Birth".',') === false ? 'readonly ' : ''); ?>name="client_date_of_birth" value="<?php echo $client_date_of_birth; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Client Height".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_height" class="col-sm-4 control-label">Client Height:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Client Height".',') === false ? 'readonly ' : ''); ?>name="client_height" value="<?php echo $client_height; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Client Weight".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_weight" class="col-sm-4 control-label">Client Weight:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Client Weight".',') === false ? 'readonly ' : ''); ?>name="client_weight" value="<?php echo $client_weight; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Client SIN".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_sin" class="col-sm-4 control-label">Client SIN:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Business".',') === false ? 'readonly ' : ''); ?>name="client_sin" value="<?php echo $client_sin; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Client Client ID".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_client_id" class="col-sm-4 control-label">Client Client ID:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Business".',') === false ? 'readonly ' : ''); ?>name="client_client_id" value="<?php echo $client_client_id; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php // Client Address ?>

        <?php if (strpos($value_config, ','."Client Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_address" class="col-sm-4 control-label">Client Address:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Business".',') === false ? 'readonly ' : ''); ?>name="client_address" value="<?php echo $client_address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Client Zip Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_zip_code" class="col-sm-4 control-label">Client Postal/Zip Code:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Client Zip Code".',') === false ? 'readonly ' : ''); ?>name="client_zip_code" value="<?php echo $client_zip_code; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Client City".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_city" class="col-sm-4 control-label">Client City:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Client City".',') === false ? 'readonly ' : ''); ?>name="client_city" value="<?php echo $client_city; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Client Province".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_province" class="col-sm-4 control-label">Client Province:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Client Province".',') === false ? 'readonly ' : ''); ?>name="client_province" value="<?php echo $client_province; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Client Country".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_country" class="col-sm-4 control-label">Client Country:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Client Country".',') === false ? 'readonly ' : ''); ?>name="client_country" value="<?php echo $client_country; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Client Program Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="client_program_address" class="col-sm-4 control-label">Client Home/Program Address:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Client Program Address".',') === false ? 'readonly ' : ''); ?>name="client_program_address" value="<?php echo $client_program_address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>


        <?php // Client Division ?>

        <?php if (strpos($value_config, ','."Division Group Home 1".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="classification_group_home_1" class="col-sm-4 control-label">Division Group Home 1:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Division Group Home 1".',') === false ? 'readonly ' : ''); ?>name="classification_group_home_1" value="<?php echo $classification_group_home_1; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Extended Health Benefits".',') !== FALSE) {             ?>
            <h3>Extended Health Benefits</h3>
                  <div class="form-group">
                    <label for="alt_phone[]" class="col-sm-4 control-label">Insurer 1:<br></label>
                    <div class="col-sm-8">
                      <select name="insurerid[]" class="chosen-select-deselect form-control" width="380">
                        <option value=""></option>
                            <?php $result_insurers = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT `contactid`, `name` FROM contacts WHERE category='Insurer' AND deleted=0"),MYSQLI_ASSOC));
							foreach($result_insurers as $insurer_id) {
								$insurer_name = get_client($dbc, $insurer_id);
								echo "<option ".($insurerid[0] == $insurer_id ? 'selected' : '')." value='". $insurer_id."'>".$insurer_name.'</option>';
							} ?>
                        </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Plan/Acct 1#:</label>
                    <div class="col-sm-8">
                      <input name="plan_acctno[]" type="text" value="<?php echo $plan_acctno[0]; ?>" id="office_street" class="form-control" />
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="alt_phone[]" class="col-sm-4 control-label">Insurer 2:<br></label>
                    <div class="col-sm-8">
                      <select name="insurerid[]" class="chosen-select-deselect form-control" width="380">
                        <option value=""></option>
                            <?php $result_insurers = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT `contactid`, `name` FROM contacts WHERE category='Insurer' AND deleted=0"),MYSQLI_ASSOC));
							foreach($result_insurers as $insurer_id) {
								$insurer_name = get_client($dbc, $insurer_id);
								echo "<option ".($insurerid[1] == $insurer_id ? 'selected' : '')." value='". $insurer_id."'>".$insurer_name.'</option>';
							} ?>
                        </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Plan/Acct 2#:</label>
                    <div class="col-sm-8">
                      <input name="plan_acctno[]" type="text" value="<?php echo $plan_acctno[1]; ?>" id="office_street" class="form-control" />
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="alt_phone[]" class="col-sm-4 control-label">Insurer 3:<br></label>
                    <div class="col-sm-8">
                      <select name="insurerid[]" class="chosen-select-deselect form-control" width="380">
                        <option value=""></option>
                            <?php $result_insurers = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT `contactid`, `name` FROM contacts WHERE category='Insurer' AND deleted=0"),MYSQLI_ASSOC));
							foreach($result_insurers as $insurer_id) {
								$insurer_name = get_client($dbc, $insurer_id);
								echo "<option ".($insurerid[2] == $insurer_id ? 'selected' : '')." value='". $insurer_id."'>".$insurer_name.'</option>';
							} ?>
                        </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Plan/Acct 3#:</label>
                    <div class="col-sm-8">
                      <input name="plan_acctno[]" type="text" value="<?php echo $plan_acctno[2]; ?>" id="office_street" class="form-control" />
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="alt_phone[]" class="col-sm-4 control-label">Insurer 4:<br></label>
                    <div class="col-sm-8">
                      <select name="insurerid[]" class="chosen-select-deselect form-control" width="380">
                        <option value=""></option>
                            <?php $result_insurers = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT `contactid`, `name` FROM contacts WHERE category='Insurer' AND deleted=0"),MYSQLI_ASSOC));
							foreach($result_insurers as $insurer_id) {
								$insurer_name = get_client($dbc, $insurer_id);
								echo "<option ".($insurerid[3] == $insurer_id ? 'selected' : '')." value='". $insurer_id."'>".$insurer_name.'</option>';
							} ?>
                        </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="business_street" class="col-sm-4 control-label">Plan/Acct 4#:</label>
                    <div class="col-sm-8">
                      <input name="plan_acctno[]" type="text" value="<?php echo $plan_acctno[3]; ?>" id="office_street" class="form-control" />
                    </div>
                  </div>

        <?php } ?>

        <?php if (strpos($value_config, ','."Division Group Home 2".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="classification_group_home_2" class="col-sm-4 control-label">Division Group Home 2:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Division Group Home 2".',') === false ? 'readonly ' : ''); ?>name="classification_group_home_2" value="<?php echo $classification_group_home_2; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Division Day Program 1".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="classification_day_program_1" class="col-sm-4 control-label">Division Day Program 1:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Division Day Program 1".',') === false ? 'readonly ' : ''); ?>name="classification_day_program_1" value="<?php echo $classification_day_program_1; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Division Day Program 2".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="classification_day_program_2" class="col-sm-4 control-label">Division Day Program 2:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Division Day Program 2".',') === false ? 'readonly ' : ''); ?>name="classification_day_program_2" value="<?php echo $classification_day_program_2; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>


        <?php //Transportation ?>


        <?php if (strpos($value_config, ','."Transportation Mode of Transportation".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="transportation_mode_of_transportation" class="col-sm-4 control-label">Mode of Transportation</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Transportation Mode of Transportation".',') === false ? 'readonly ' : ''); ?>name="transportation_mode_of_transportation" value="<?php echo $transportation_mode_of_transportation; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Transportation Transit Access".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="transportation_transit_access" class="col-sm-4 control-label">Transit Access:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Transportation Transit Access".',') === false ? 'readonly ' : ''); ?>name="transportation_transit_access" value="<?php echo $transportation_transit_access; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Transportation Access Password".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="transportation_access_password" class="col-sm-4 control-label">Access Password:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Transportation Access Password".',') === false ? 'readonly ' : ''); ?>name="transportation_access_password" value="<?php echo $transportation_access_password; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Transportation Drivers License".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="transportation_drivers_license" class="col-sm-4 control-label">Driver's Licence #:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Transportation Drivers License".',') === false ? 'readonly ' : ''); ?>name="transportation_drivers_license" value="<?php echo $transportation_drivers_license; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Drivers License Class".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="transportation_drivers_class" class="col-sm-4 control-label">Driver's Licence Class:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Drivers License Class".',') === false ? 'readonly ' : ''); ?>name="transportation_drivers_class" value="<?php echo $transportation_drivers_class; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Drive Manual Transmission".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="transportation_drivers_transmission" class="col-sm-4 control-label">Driver Can Operate a Standard Transmission Vehicle:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Drive Manual Transmission".',') === false ? 'readonly ' : ''); ?>name="transportation_drivers_transmission" <?php echo 'Yes' == $transportation_drivers_transmission ? "checked " : ""; ?>value="Yes" type="radio"> Yes
              <input <?php echo (strpos($edit_config, ','."Drive Manual Transmission".',') === false ? 'readonly ' : ''); ?>name="transportation_drivers_transmission" <?php echo 'No' == $transportation_drivers_transmission ? "checked " : ""; ?>value="No" type="radio"> No
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Transportation Drivers Glasses".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="transportation_drivers_glasses" class="col-sm-4 control-label">Driver Requires Glasses/Contacts by Law:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Transportation Drivers Glasses".',') === false ? 'readonly ' : ''); ?>name="transportation_drivers_glasses" <?php echo 'Yes' == $transportation_drivers_glasses ? "checked " : ""; ?>value="Yes" type="radio"> Yes
              <input <?php echo (strpos($edit_config, ','."Transportation Drivers Glasses".',') === false ? 'readonly ' : ''); ?>name="transportation_drivers_glasses" <?php echo 'No' == $transportation_drivers_glasses ? "checked " : ""; ?>value="No" type="radio"> No
            </div>
            </div>
        <?php } ?>

		<?php // Financial Information ?>

        <?php if (strpos($value_config, ','."Bank Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="bank_name" class="col-sm-4 control-label">Bank Name:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Bank Name".',') === false ? 'readonly ' : ''); ?>name="bank_name" value="<?php echo $bank_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Bank Institution Number".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="bank_institution_number" class="col-sm-4 control-label">Bank Institution Number:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Bank Institution Number".',') === false ? 'readonly ' : ''); ?>name="bank_institution_number" value="<?php echo $bank_institution_number; ?>" type="number" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Bank Transit Number".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="bank_transit" class="col-sm-4 control-label">Bank Transit Number:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Bank Transit Number".',') === false ? 'readonly ' : ''); ?>name="bank_transit" value="<?php echo $bank_transit; ?>" type="number" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Bank Account Number".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="bank_account_number" class="col-sm-4 control-label">Bank Account Number:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Bank Account Number".',') === false ? 'readonly ' : ''); ?>name="bank_account_number" value="<?php echo $bank_account_number; ?>" type="number" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php //Emergency Contacts ?>

        <?php if (strpos($value_config, ','."Primary Emergency Contact First Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="pri_emergency_first_name" class="col-sm-4 control-label">Primary Emergency Contact First Name:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Primary Emergency Contact First Name".',') === false ? 'readonly ' : ''); ?>name="pri_emergency_first_name" value="<?php echo $pri_emergency_first_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Primary Emergency Contact Last Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="pri_emergency_last_name" class="col-sm-4 control-label">Primary Emergency Contact Last Name:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Primary Emergency Contact Last Name".',') === false ? 'readonly ' : ''); ?>name="pri_emergency_last_name" value="<?php echo $pri_emergency_last_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Primary Emergency Contact Cell Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="pri_emergency_cell_phone" class="col-sm-4 control-label">Primary Emergency Contact Cell Phone:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Primary Emergency Contact Cell Phone".',') === false ? 'readonly ' : ''); ?>name="pri_emergency_cell_phone" value="<?php echo $pri_emergency_cell_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Primary Emergency Contact Home Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="pri_emergency_home_phone" class="col-sm-4 control-label">Primary Emergency Contact Home Phone:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Primary Emergency Contact Home Phone".',') === false ? 'readonly ' : ''); ?>name="pri_emergency_home_phone" value="<?php echo $pri_emergency_home_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Primary Emergency Contact Email".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="pri_emergency_email" class="col-sm-4 control-label">Primary Emergency Contact Email:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Primary Emergency Contact Email".',') === false ? 'readonly ' : ''); ?>name="pri_emergency_email" value="<?php echo $pri_emergency_email; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Primary Emergency Contact Relationship".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="pri_emergency_relation" class="col-sm-4 control-label">Primary Emergency Contact Relationship:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Primary Emergency Contact Relationship".',') === false ? 'readonly ' : ''); ?>name="pri_emergency_relation" value="<?php echo $pri_emergency_relation; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Secondary Emergency Contact First Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="sec_emergency_first_name" class="col-sm-4 control-label">Secondary Emergency Contact First Name:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Secondary Emergency Contact First Name".',') === false ? 'readonly ' : ''); ?>name="sec_emergency_first_name" value="<?php echo $sec_emergency_first_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Secondary Emergency Contact Last Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="sec_emergency_last_name" class="col-sm-4 control-label">Secondary Emergency Contact Last Name:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Secondary Emergency Contact Last Name".',') === false ? 'readonly ' : ''); ?>name="sec_emergency_last_name" value="<?php echo $sec_emergency_last_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Secondary Emergency Contact Cell Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="sec_emergency_cell_phone" class="col-sm-4 control-label">Secondary Emergency Contact Cell Phone:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Secondary Emergency Contact Cell Phone".',') === false ? 'readonly ' : ''); ?>name="sec_emergency_cell_phone" value="<?php echo $sec_emergency_cell_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Secondary Emergency Contact Home Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="sec_emergency_home_phone" class="col-sm-4 control-label">Secondary Emergency Contact Home Phone:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Secondary Emergency Contact Home Phone".',') === false ? 'readonly ' : ''); ?>name="sec_emergency_home_phone" value="<?php echo $sec_emergency_home_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Secondary Emergency Contact Email".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="sec_emergency_email" class="col-sm-4 control-label">Secondary Emergency Contact Email:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Secondary Emergency Contact Email".',') === false ? 'readonly ' : ''); ?>name="sec_emergency_email" value="<?php echo $sec_emergency_email; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Secondary Emergency Contact Relationship".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="sec_emergency_relation" class="col-sm-4 control-label">Secondary Emergency Contact Relationship:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Secondary Emergency Contact Relationship".',') === false ? 'readonly ' : ''); ?>name="sec_emergency_relation" value="<?php echo $sec_emergency_relation; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php //Health Care & Insurance ?>

        <?php if (strpos($value_config, ','."Health Care Number".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="health_care_num" class="col-sm-4 control-label">Health Care #:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Health Care Number".',') === false ? 'readonly ' : ''); ?>name="health_care_num" value="<?php echo $health_care_num; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Health Concerns".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="health_concerns" class="col-sm-4 control-label">Do you have any health concerns that you want to make the company aware of?</label>
            <div class="col-sm-8">
              <textarea <?php echo (strpos($edit_config, ','."Health Concerns".',') === false ? 'readonly ' : ''); ?>name="health_concerns" rows="5" cols="50" class="form-control"><?php echo $health_concerns; ?></textarea>
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Emergency Procedure".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="health_emergency_procedure" class="col-sm-4 control-label">Do you have any special emergency health procedures that you want the company to be aware of?</label>
            <div class="col-sm-8">
              <textarea <?php echo (strpos($edit_config, ','."Emergency Procedure".',') === false ? 'readonly ' : ''); ?>name="health_emergency_procedure" rows="5" cols="50" class="form-control"><?php echo $health_emergency_procedure; ?></textarea>
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Medications".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="health_medications" class="col-sm-4 control-label">Are you on any medications you want to make the company aware of?</label>
            <div class="col-sm-8">
              <textarea <?php echo (strpos($edit_config, ','."Medications".',') === false ? 'readonly ' : ''); ?>name="health_medications" rows="5" cols="50" class="form-control"><?php echo $health_medications; ?></textarea>
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Allergies".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="health_allergens" class="col-sm-4 control-label">Do you have any allergies you wish to make the company aware of?</label>
            <div class="col-sm-8">
              <textarea <?php echo (strpos($edit_config, ','."Allergies".',') === false ? 'readonly ' : ''); ?>name="health_allergens" rows="5" cols="50" class="form-control"><?php echo $health_allergens; ?></textarea>
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Allergy Procedure".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="health_allergens_procedure" class="col-sm-4 control-label">Do you have any special procedures you want to make the company aware of should you require aid?</label>
            <div class="col-sm-8">
              <textarea <?php echo (strpos($edit_config, ','."Allergy Procedure".',') === false ? 'readonly ' : ''); ?>name="health_allergens_procedure" rows="5" cols="50" class="form-control"><?php echo $health_allergens_procedure; ?></textarea>
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Insurance Alberta Health Care".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="insurance_alberta_health_care" class="col-sm-4 control-label">Alberta Health Care:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Insurance Alberta Health Care".',') === false ? 'readonly ' : ''); ?>name="insurance_alberta_health_care" value="<?php echo $insurance_alberta_health_care; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Insurance AISH Entrance Date".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="insurance_aish_entrance_date" class="col-sm-4 control-label">AISH Entrance Date:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Insurance AISH Entrance Date".',') === false ? 'readonly ' : ''); ?>name="insurance_aish_entrance_date" value="<?php echo $insurance_aish_entrance_date; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Insurance AISH #".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="insurance_aish" class="col-sm-4 control-label">AISH #:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Insurance AISH #".',') === false ? 'readonly ' : ''); ?>name="insurance_aish" value="<?php echo $insurance_aish; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Insurance Client ID".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="insurance_client_id" class="col-sm-4 control-label">Client ID:</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Insurance Client ID".',') === false ? 'readonly ' : ''); ?>name="insurance_client_id" value="<?php echo $insurance_client_id; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php //Guardians ?>

        <?php if (strpos($value_config, ','."Guardians Family Guardian".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_family_guardian" class="col-sm-4 control-label">Family Guardian</label>
            <div class="col-sm-8">
              <input type="radio" <?php echo (strpos($edit_config, ','."Guardians Family Guardian".',') === false ? 'readonly ' : ''); ?>name="guardians_family_guardian" value="1" <?php if($guardians_family_guardian==1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Business".',') === false ? 'readonly ' : ''); ?>name="guardians_family_guardian" value="0" <?php if($guardians_family_guardian!=1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Guardians Family Appointed Guardian".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_family_appointed_guardian" class="col-sm-4 control-label">Family Appointed Guardian</label>
            <div class="col-sm-8">
              <input type="radio" <?php echo (strpos($edit_config, ','."Guardians Family Appointed Guardian".',') === false ? 'readonly ' : ''); ?>name="guardians_family_appointed_guardian" value="1" <?php if($guardians_family_appointed_guardian == 1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Guardians Family Appointed Guardian".',') === false ? 'readonly ' : ''); ?>name="guardians_family_appointed_guardian" value="0" <?php if($guardians_family_appointed_guardian != 1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Guardians Public Guardian".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_public_guardian" class="col-sm-4 control-label">Guardians Public Guardian</label>
            <div class="col-sm-8">
              <input type="radio" <?php echo (strpos($edit_config, ','."Guardians Public Guardian".',') === false ? 'readonly ' : ''); ?>name="guardians_public_guardian" value="1" <?php if($guardians_public_guardian == 1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Guardians Public Guardian".',') === false ? 'readonly ' : ''); ?>name="guardians_public_guardian" value="0" <?php if($guardians_public_guardian != 1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Guardians Court Appointed Guardian".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_court_appointed_guardian" class="col-sm-4 control-label">Court Appointed Guardian</label>
            <div class="col-sm-8">
              <input type="radio" <?php echo (strpos($edit_config, ','."Guardians Court Appointed Guardian".',') === false ? 'readonly ' : ''); ?>name="guardians_court_appointed_guardian" value="1" <?php if($guardians_court_appointed_guardian == 1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Guardians Court Appointed Guardian".',') === false ? 'readonly ' : ''); ?>name="guardians_court_appointed_guardian" value="0" <?php if($guardians_court_appointed_guardian != 1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Guardians First Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_first_name" class="col-sm-4 control-label">Guardians First Name</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Guardians First Name".',') === false ? 'readonly ' : ''); ?>name="guardians_first_name" value="<?php echo $guardians_first_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Guardians Last Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_last_name" class="col-sm-4 control-label">Guardians Last Name</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Guardians Last Name".',') === false ? 'readonly ' : ''); ?>name="guardians_last_name" value="<?php echo $guardians_last_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Guardians Work Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_work_phone" class="col-sm-4 control-label">Guardians Work Phone</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Guardians Work Phone".',') === false ? 'readonly ' : ''); ?>name="guardians_work_phone" value="<?php echo $guardians_work_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Guardians Home Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_home_phone" class="col-sm-4 control-label">Guardians Home Phone</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Guardians Home Phone".',') === false ? 'readonly ' : ''); ?>name="guardians_home_phone" value="<?php echo $guardians_home_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Guardians Cell Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_cell_phone" class="col-sm-4 control-label">Guardians Cell Phone</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Guardians Cell Phone".',') === false ? 'readonly ' : ''); ?>name="guardians_cell_phone" value="<?php echo $guardians_cell_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Guardians Fax".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_fax" class="col-sm-4 control-label">Guardians Fax</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Guardians Fax".',') === false ? 'readonly ' : ''); ?>name="guardians_fax" value="<?php echo $guardians_fax; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Guardians Email Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_email_address" class="col-sm-4 control-label">Guardians Email Address</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Guardians Email Address".',') === false ? 'readonly ' : ''); ?>name="guardians_email_address" value="<?php echo $guardians_email_address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Guardians Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_address" class="col-sm-4 control-label">Guardians Address</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Guardians Address".',') === false ? 'readonly ' : ''); ?>name="guardians_address" value="<?php echo $guardians_address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Guardians Zip Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_zip_code" class="col-sm-4 control-label">Guardians Postal/Zip Code</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Guardians Zip Code".',') === false ? 'readonly ' : ''); ?>name="guardians_zip_code" value="<?php echo $guardians_zip_code; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Guardians Town".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_town" class="col-sm-4 control-label">Guardians Town</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Guardians Town".',') === false ? 'readonly ' : ''); ?>name="guardians_town" value="<?php echo $guardians_town; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Guardians Province".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_province" class="col-sm-4 control-label">Guardians Province</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Guardians Province".',') === false ? 'readonly ' : ''); ?>name="guardians_province" value="<?php echo $guardians_province; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Guardians Country".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="guardians_country" class="col-sm-4 control-label">Guardians Country</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Guardians Country".',') === false ? 'readonly ' : ''); ?>name="guardians_country" value="<?php echo $guardians_country; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php //Trustee ?>

        <?php if (strpos($value_config, ','."Trustee Family Trustee".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_family_trustee" class="col-sm-4 control-label">Family Trustee</label>
            <div class="col-sm-8">
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Family Trustee".',') === false ? 'readonly ' : ''); ?>name="trustee_family_trustee" value="1" <?php if($trustee_family_trustee==1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Family Trustee".',') === false ? 'readonly ' : ''); ?>name="trustee_family_trustee" value="0" <?php if($trustee_family_trustee!=1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Trustee Family Appointed Trustee".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_family_appointed_trustee" class="col-sm-4 control-label">Family Appointed Trustee</label>
            <div class="col-sm-8">
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Family Appointed Trustee".',') === false ? 'readonly ' : ''); ?>name="trustee_family_appointed_trustee" value="1" <?php if($trustee_family_appointed_trustee == 1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Family Appointed Trustee".',') === false ? 'readonly ' : ''); ?>name="trustee_family_appointed_trustee" value="0" <?php if($trustee_family_appointed_trustee != 1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Trustee Public Trustee".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_public_trustee" class="col-sm-4 control-label">Trustee Public Trustee</label>
            <div class="col-sm-8">
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Public Trustee".',') === false ? 'readonly ' : ''); ?>name="trustee_public_trustee" value="1" <?php if($trustee_public_trustee == 1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Public Trustee".',') === false ? 'readonly ' : ''); ?>name="trustee_public_trustee" value="0" <?php if($trustee_public_trustee != 1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Trustee Court Appointed Trustee".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_court_appointed_trustee" class="col-sm-4 control-label">Court Appointed Trustee</label>
            <div class="col-sm-8">
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Court Appointed Trustee".',') === false ? 'readonly ' : ''); ?>name="trustee_court_appointed_trustee" value="1" <?php if($trustee_court_appointed_trustee == 1){ echo 'checked'; } ?>> Yes
              <input type="radio" <?php echo (strpos($edit_config, ','."Trustee Court Appointed Trustee".',') === false ? 'readonly ' : ''); ?>name="trustee_court_appointed_trustee" value="0" <?php if($trustee_court_appointed_trustee != 1){ echo 'checked'; } ?>> No
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Trustee First Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_first_name" class="col-sm-4 control-label">Trustee First Name</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Trustee First Name".',') === false ? 'readonly ' : ''); ?>name="trustee_first_name" value="<?php echo $trustee_first_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Trustee Last Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_last_name" class="col-sm-4 control-label">Trustee Last Name</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Trustee Last Name".',') === false ? 'readonly ' : ''); ?>name="trustee_last_name" value="<?php echo $trustee_last_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Trustee Work Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_work_phone" class="col-sm-4 control-label">Trustee Work Phone</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Trustee Work Phone".',') === false ? 'readonly ' : ''); ?>name="trustee_work_phone" value="<?php echo $trustee_work_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Trustee Home Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_home_phone" class="col-sm-4 control-label">Trustee Home Phone</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Trustee Home Phone".',') === false ? 'readonly ' : ''); ?>name="trustee_home_phone" value="<?php echo $trustee_home_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Trustee Cell Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_cell_phone" class="col-sm-4 control-label">Trustee Cell Phone</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Trustee Cell Phone".',') === false ? 'readonly ' : ''); ?>name="trustee_cell_phone" value="<?php echo $trustee_cell_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Trustee Fax".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_fax" class="col-sm-4 control-label">Trustee Fax</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Trustee Fax".',') === false ? 'readonly ' : ''); ?>name="trustee_fax" value="<?php echo $trustee_fax; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Trustee Email Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_email_address" class="col-sm-4 control-label">Trustee Email Address</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Trustee Email Address".',') === false ? 'readonly ' : ''); ?>name="trustee_email_address" value="<?php echo $trustee_email_address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Trustee Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_address" class="col-sm-4 control-label">Trustee Address</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Trustee Address".',') === false ? 'readonly ' : ''); ?>name="trustee_address" value="<?php echo $trustee_address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Trustee Zip Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_zip_code" class="col-sm-4 control-label">Trustee Postal/Zip Code</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Trustee Zip Code".',') === false ? 'readonly ' : ''); ?>name="trustee_zip_code" value="<?php echo $trustee_zip_code; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Trustee Town".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_town" class="col-sm-4 control-label">Trustee Town</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Trustee Town".',') === false ? 'readonly ' : ''); ?>name="trustee_town" value="<?php echo $trustee_town; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Trustee Province".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_province" class="col-sm-4 control-label">Trustee Province</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Trustee Province".',') === false ? 'readonly ' : ''); ?>name="trustee_province" value="<?php echo $trustee_province; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Trustee Country".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="trustee_country" class="col-sm-4 control-label">Trustee Country</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Trustee Country".',') === false ? 'readonly ' : ''); ?>name="trustee_country" value="<?php echo $trustee_country; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php //Family Doctor ?>

        <?php if (strpos($value_config, ','."Family Doctor First Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_first_name" class="col-sm-4 control-label">Family Doctor First Name</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Family Doctor First Name".',') === false ? 'readonly ' : ''); ?>name="family_doctor_first_name" value="<?php echo $family_doctor_first_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Family Doctor Last Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_last_name" class="col-sm-4 control-label">Family Doctor Last Name</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Family Doctor Last Name".',') === false ? 'readonly ' : ''); ?>name="family_doctor_last_name" value="<?php echo $family_doctor_last_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Family Doctor Work Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_work_phone" class="col-sm-4 control-label">Family Doctor Work Phone</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Family Doctor Work Phone".',') === false ? 'readonly ' : ''); ?>name="family_doctor_work_phone" value="<?php echo $family_doctor_work_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Family Doctor Home Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_home_phone" class="col-sm-4 control-label">Family Doctor Home Phone</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Family Doctor Home Phone".',') === false ? 'readonly ' : ''); ?>name="family_doctor_home_phone" value="<?php echo $family_doctor_home_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Family Doctor Cell Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_cell_phone" class="col-sm-4 control-label">Family Doctor Cell Phone</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Family Doctor Cell Phone".',') === false ? 'readonly ' : ''); ?>name="family_doctor_cell_phone" value="<?php echo $family_doctor_cell_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Family Doctor Fax".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_fax" class="col-sm-4 control-label">Family Doctor Fax</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Family Doctor Fax".',') === false ? 'readonly ' : ''); ?>name="family_doctor_fax" value="<?php echo $family_doctor_fax; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Family Doctor Email Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_email_address" class="col-sm-4 control-label">Family Doctor Email Address</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Family Doctor Email Address".',') === false ? 'readonly ' : ''); ?>name="family_doctor_email_address" value="<?php echo $family_doctor_email_address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Family Doctor Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_address" class="col-sm-4 control-label">Family Doctor Address</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Family Doctor Address".',') === false ? 'readonly ' : ''); ?>name="family_doctor_address" value="<?php echo $family_doctor_address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Family Doctor Zip Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_zip_code" class="col-sm-4 control-label">Family Doctor Postal/Zip Code</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Family Doctor Zip Code".',') === false ? 'readonly ' : ''); ?>name="family_doctor_zip_code" value="<?php echo $family_doctor_zip_code; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Family Doctor Town".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_town" class="col-sm-4 control-label">Family Doctor Town</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Family Doctor Town".',') === false ? 'readonly ' : ''); ?>name="family_doctor_town" value="<?php echo $family_doctor_town; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Family Doctor Province".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_province" class="col-sm-4 control-label">Family Doctor Province</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Family Doctor Province".',') === false ? 'readonly ' : ''); ?>name="family_doctor_province" value="<?php echo $family_doctor_province; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Family Doctor Country".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="family_doctor_country" class="col-sm-4 control-label">Family Doctor Country</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Family Doctor Country".',') === false ? 'readonly ' : ''); ?>name="family_doctor_country" value="<?php echo $family_doctor_country; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php //Dentist ?>

        <?php if (strpos($value_config, ','."Dentist First Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_first_name" class="col-sm-4 control-label">Dentist First Name</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Dentist First Name".',') === false ? 'readonly ' : ''); ?>name="dentist_first_name" value="<?php echo $dentist_first_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Dentist Last Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_last_name" class="col-sm-4 control-label">Dentist Last Name</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Dentist Last Name".',') === false ? 'readonly ' : ''); ?>name="dentist_last_name" value="<?php echo $dentist_last_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Dentist Work Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_work_phone" class="col-sm-4 control-label">Dentist Work Phone</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Dentist Work Phone".',') === false ? 'readonly ' : ''); ?>name="dentist_work_phone" value="<?php echo $dentist_work_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Dentist Home Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_home_phone" class="col-sm-4 control-label">Dentist Home Phone</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Dentist Home Phone".',') === false ? 'readonly ' : ''); ?>name="dentist_home_phone" value="<?php echo $dentist_home_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Dentist Cell Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_cell_phone" class="col-sm-4 control-label">Dentist Cell Phone</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Dentist Cell Phone".',') === false ? 'readonly ' : ''); ?>name="dentist_cell_phone" value="<?php echo $dentist_cell_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Dentist Fax".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_fax" class="col-sm-4 control-label">Dentist Fax</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Dentist Fax".',') === false ? 'readonly ' : ''); ?>name="dentist_fax" value="<?php echo $dentist_fax; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Dentist Email Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_email_address" class="col-sm-4 control-label">Dentist Email Address</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Dentist Email Address".',') === false ? 'readonly ' : ''); ?>name="dentist_email_address" value="<?php echo $dentist_email_address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Dentist Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_address" class="col-sm-4 control-label">Dentist Address</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Dentist Address".',') === false ? 'readonly ' : ''); ?>name="dentist_address" value="<?php echo $dentist_address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Dentist Zip Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_zip_code" class="col-sm-4 control-label">Dentist Postal/Zip Code</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Dentist Zip Code".',') === false ? 'readonly ' : ''); ?>name="dentist_zip_code" value="<?php echo $dentist_zip_code; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Dentist Town".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_town" class="col-sm-4 control-label">Dentist Town</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Dentist Town".',') === false ? 'readonly ' : ''); ?>name="dentist_town" value="<?php echo $dentist_town; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Dentist Province".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_province" class="col-sm-4 control-label">Dentist Province</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Dentist Province".',') === false ? 'readonly ' : ''); ?>name="dentist_province" value="<?php echo $dentist_province; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Dentist Country".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="dentist_country" class="col-sm-4 control-label">Dentist Country</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Dentist Country".',') === false ? 'readonly ' : ''); ?>name="dentist_country" value="<?php echo $dentist_country; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php //Specialists ?>

        <?php if (strpos($value_config, ','."Specialists First Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_first_name" class="col-sm-4 control-label">Specialists First Name</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Specialists First Name".',') === false ? 'readonly ' : ''); ?>name="specialists_first_name" value="<?php echo $specialists_first_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Specialists Last Name".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_last_name" class="col-sm-4 control-label">Specialists Last Name</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Specialists Last Name".',') === false ? 'readonly ' : ''); ?>name="specialists_last_name" value="<?php echo $specialists_last_name; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Specialists Work Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_work_phone" class="col-sm-4 control-label">Specialists Work Phone</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Specialists Work Phone".',') === false ? 'readonly ' : ''); ?>name="specialists_work_phone" value="<?php echo $specialists_work_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Specialists Home Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_home_phone" class="col-sm-4 control-label">Specialists Home Phone</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Specialists Home Phone".',') === false ? 'readonly ' : ''); ?>name="specialists_home_phone" value="<?php echo $specialists_home_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Specialists Cell Phone".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_cell_phone" class="col-sm-4 control-label">Specialists Cell Phone</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Specialists Cell Phone".',') === false ? 'readonly ' : ''); ?>name="specialists_cell_phone" value="<?php echo $specialists_cell_phone; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Specialists Fax".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_fax" class="col-sm-4 control-label">Specialists Fax</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Specialists Fax".',') === false ? 'readonly ' : ''); ?>name="specialists_fax" value="<?php echo $specialists_fax; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Specialists Email Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_email_address" class="col-sm-4 control-label">Specialists Email Address</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Specialists Email Address".',') === false ? 'readonly ' : ''); ?>name="specialists_email_address" value="<?php echo $specialists_email_address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Specialists Address".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_address" class="col-sm-4 control-label">Specialists Address</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Specialists Address".',') === false ? 'readonly ' : ''); ?>name="specialists_address" value="<?php echo $specialists_address; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Specialists Zip Code".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_zip_code" class="col-sm-4 control-label">Specialists Postal/Zip Code</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Specialists Zip Code".',') === false ? 'readonly ' : ''); ?>name="specialists_zip_code" value="<?php echo $specialists_zip_code; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Specialists Town".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_town" class="col-sm-4 control-label">Specialists Town</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Specialists Town".',') === false ? 'readonly ' : ''); ?>name="specialists_town" value="<?php echo $specialists_town; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Specialists Province".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_province" class="col-sm-4 control-label">Specialists Province</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Specialists Province".',') === false ? 'readonly ' : ''); ?>name="specialists_province" value="<?php echo $specialists_province; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Specialists Country".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="specialists_country" class="col-sm-4 control-label">Specialists Country</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Specialists Country".',') === false ? 'readonly ' : ''); ?>name="specialists_country" value="<?php echo $specialists_country; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Medications Start Time".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="medications_start_time" class="col-sm-4 control-label">Start Time</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Medications Start Time".',') === false ? 'readonly ' : ''); ?>name="medications_start_time" value="<?php echo $medications_start_time; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Medications End Time".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="medications_end_time" class="col-sm-4 control-label">End Time</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Medications End Time".',') === false ? 'readonly ' : ''); ?>name="medications_end_time" value="<?php echo $medications_end_time; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Medications Completed By".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="medications_completed_by" class="col-sm-4 control-label">Completed By</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Medications Completed By".',') === false ? 'readonly ' : ''); ?>name="medications_completed_by" value="<?php echo $medications_completed_by; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Medications Signature Box".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="medications_signature_box" class="col-sm-4 control-label">Medications Signature Box</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Medications Signature Box".',') === false ? 'readonly ' : ''); ?>name="medications_signature_box" value="<?php echo $medications_signature_box; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Medications Management Completed By".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="medications_management_completed_by" class="col-sm-4 control-label">Management Completed By</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Medications Management Completed By".',') === false ? 'readonly ' : ''); ?>name="medications_management_completed_by" value="<?php echo $medications_management_completed_by; ?>" type="text" class="form-control">
            </div>
            </div>
        <?php } ?>


        <?php if (strpos($value_config, ','."Medications Management Signature Box".',') !== FALSE) { ?>
            <div class="form-group">
            <label for="medications_management_signature_box" class="col-sm-4 control-label">Management Signature Box</label>
            <div class="col-sm-8">
              <input <?php echo (strpos($edit_config, ','."Medications Management Signature Box".',') === false ? 'readonly ' : ''); ?>name="medications_management_signature_box" value="<?php echo $medications_management_signature_box; ?>" type="text" class="form-control">
            </div>
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
    <?php if (strpos($value_config, ','.$title.',') !== FALSE) { ?>
        <div class="form-group">
        <label for="<?php echo $field; ?>" class="col-sm-4 control-label"><?php echo $title; ?></label>
        <div class="col-sm-8">
          <input <?php echo (strpos($edit_config, ','.$title.',') === false ? 'readonly ' : ''); ?>name="<?php echo $field; ?>" value="<?php echo $$field; ?>" type="text" class="form-control">
        </div>
        </div>
    <?php } ?>
<?php } ?>