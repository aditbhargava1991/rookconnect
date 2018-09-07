<?php if (strpos($value_config, ','."Category".',') !== FALSE) { ?>
<script>
$(document).on('change', 'select[name="category"]', function() { filterMake(this.value); });
$(document).on('change', 'select[name="region[]"]', function() { filterClassifications(); });
function filterMake(cat) {
	$('[name=make] option').hide();
	$('[name=make] option').filter(function() { return ($(this).data('category') == cat || $(this).val() == 'Other'); }).show();
	$('[name=make]').trigger('change.select2');
}
function addRegion() {
  var clone = $('.region-group').first().clone();
  clone.find('option:selected').removeAttr('selected');
  resetChosen(clone.find('.chosen-select-deselect'))
  $('.region-group').last().after(clone);
}
function addLocation() {
  var clone = $('.location-group').first().clone();
  clone.find('option:selected').removeAttr('selected');
  resetChosen(clone.find('.chosen-select-deselect'));
  $('.location-group').last().after(clone);
}
function addClassification() {
  var clone = $('.classification-group').first().clone();
  clone.find('option:selected').removeAttr('selected');
  resetChosen(clone.find('.chosen-select-deselect'));
  $('.classification-group').last().after(clone);
}
function removeRegion(btn) {
  if($('.region-group').length <= 1) {
    addRegion();
  }
  $(btn).closest('.region-group').remove();
  filterClassifications();
}
function removeLocation(btn) {
  if($('.location-group').length <= 1) {
    addRegion();
  }
  $(btn).closest('.location-group').remove();
}
function removeClassification(btn) {
  if($('.classification-group').length <= 1) {
    addClassification();
  }
  $(btn).closest('.classification-group').remove();
}
function filterClassifications() {
  var regions = [];
  $('select[name="region[]"]').each(function () {
    regions.push(this.value);
  });

  $('select[name="classification[]"] option').each(function() {
    var classification = this;
    var class_regions = $(this).data('regions');
    $(this).show();
    class_regions.forEach(function(class_region) {
      if(regions.indexOf(class_region) < 0 && regions.length > 0) {
        $(classification).hide();
      }
    });
  });
  $('select[name="classification[]"]').trigger('change.select2');
}
</script>
<div class="form-group">
<label for="phone_number" class="col-sm-4 control-label">Category<span class="brand-color">*</span>:</label>
<div class="col-sm-8">
  <select id="category" name="category" class="chosen-select-deselect form-control" width="380">
  <option value=''></option>
  <?php
    $each_tab = explode(',', get_config($dbc, 'equipment_tabs'));
    foreach ($each_tab as $cat_tab) {
        if ($category == $cat_tab) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
        echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
    }
  ?>
  </select>

  <!--
  <select id="category" name="category" class="chosen-select-deselect form-control" width="380">
      <option value=''></option>
      <option value='Excavators' <?php if ($category=='Excavators') echo 'selected="selected"';?> >Excavators</option>
      <option value='Transport' <?php if ($category=='Transport') echo 'selected="selected"';?> >Transport</option>
      <option value='Loaders/Graders/Dozers' <?php if ($category=='Loaders/Graders/Dozers') echo 'selected="selected"';?> >Loaders/Graders/Dozers</option>
      <option value='Rollers' <?php if ($category=='Rollers') echo 'selected="selected"';?> >Rollers</option>
      <option value='Labour' <?php if ($category=='Labour') echo 'selected="selected"';?> >Labour</option>
      <option value='Skid Steers' <?php if ($category=='Skid Steers') echo 'selected="selected"';?> >Skid Steers</option>
      <option value='Mob Equipment' <?php if ($category=='Mob Equipment') echo 'selected="selected"';?> >Mob Equipment</option>
      <option value='Truck' <?php if ($category=='Truck') echo 'selected="selected"';?> >Truck</option>
      <option value='Trailer' <?php if ($category=='Trailer') echo 'selected="selected"';?> >Trailer</option>
  </select>
  -->

</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Description".',') !== FALSE) { ?>
<div class="form-group">
<label for="first_name[]" class="col-sm-4 control-label">Description:</label>
<div class="col-sm-8">
  <textarea name="equ_description" rows="5" cols="50" class="form-control"><?php echo $equ_description; ?></textarea>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Type".',') !== FALSE) { ?>
<div class="form-group">
<label for="phone_number" class="col-sm-4 control-label">Type<span class="brand-color">*</span>:</label>
<div class="col-sm-8">
  <select id="type" name="type" class="chosen-select-deselect form-control" width="380">
      <option value=''></option>
	  <?php
		$result = mysqli_query($dbc, "SELECT distinct(type) FROM equipment");
		while($row = mysqli_fetch_assoc($result)) {
			if ($type == $row['type']) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			echo "<option ".$selected." value = '".$row['type']."'>".$row['type']."</option>";
		}
	  ?>
      <option value='Other'>Other</option>
  </select>
</div>
</div>

<div class="form-group" id="type_name" style="display: none;">
<label for="travel_task" class="col-sm-4 control-label">Other Type<span class="text-red">*</span>:</label>
<div class="col-sm-8">
	<input name="type_name" type="text" class="form-control" />
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Make".',') !== FALSE) { ?>
<div class="form-group">
<label for="travel_task" class="col-sm-4 control-label">Make:</label>
<div class="col-sm-8">
  <select id="make" name="make" class="chosen-select-deselect form-control" width="380">
  <option value=''></option>
      <?php
        $result = mysqli_query($dbc, "SELECT `make`, `category` FROM `equipment` GROUP BY `make`, `category` ORDER BY `make`");
        while($row = mysqli_fetch_assoc($result)) {
            if ($make == $row['make'] && $category == $row['category']) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            echo "<option ".$selected." data-category='".$row['category']."' value='".$row['make']."' style='".($category != $row['category'] ? 'style="display:none;"' : '')."'>".$row['make']."</option>";
        }
      ?>
      <option value = 'Other'>Other</option>
  </select>
</div>
</div>

<div class="form-group" style="display: none;">
<label for="travel_task" class="col-sm-4 control-label"></label>
<div class="col-sm-8">
    <input name="make_name" id="make_name" type="text" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Model".',') !== FALSE) { ?>
<div class="form-group">
<label for="travel_task" class="col-sm-4 control-label">Model:</label>
<div class="col-sm-8">
  <select id="model" name="model" class="chosen-select-deselect form-control" width="380">
  <option value=''></option>
      <?php
        $result = mysqli_query($dbc, "SELECT distinct(model) FROM equipment order by model");
        while($row = mysqli_fetch_assoc($result)) {
            if ($model == $row['model']) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            echo "<option ".$selected." value = '".$row['model']."'>".$row['model']."</option>";
        }
      ?>
      <option value = 'Other'>Other</option>
  </select>
</div>
</div>

<div class="form-group" style="display: none;">
<label for="travel_task" class="col-sm-4 control-label"></label>
<div class="col-sm-8">
    <input name="model" id="model_other" disabled type="text" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Unit of Measure".',') !== FALSE) { ?>
<div class="form-group">
<label for="phone_number" class="col-sm-4 control-label">Unit of Measure:</label>
<div class="col-sm-8">
  <input name="submodel" type="text" value="<?php echo $submodel; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Model Year".',') !== FALSE) { ?>
<div class="form-group">
	<label for="phone_number" class="col-sm-4 control-label">Model Year:</label>
	<div class="col-sm-8">
		<select id="model_year" name="model_year" class="chosen-select-deselect form-control" width="380">
			<option value=''></option>
			<?php for($i = intval(date('Y')) + 1; $i > 1950; $i--) {
				echo "<option ".($model_year == $i ? 'selected' : '')." value = '".$i."'>".$i."</option>";
			} ?>
		</select>
	</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Label".',') !== FALSE) { ?>
<div class="form-group">
<label for="label" class="col-sm-4 control-label">Equipment Label:</label>
<div class="col-sm-8">
  <input name="label" type="text" value="<?php echo $label; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Total Kilometres".',') !== FALSE) { ?>
<div class="form-group">
<label for="phone_number" class="col-sm-4 control-label">Total Kilometres:</label>
<div class="col-sm-8">
  <input name="total_kilometres" type="text" value="<?php echo $total_kilometres; ?>" class="form-control"/>
</div>
</div>
<?php } ?>
<?php if (strpos($value_config, ','."Leased".',') !== FALSE) { ?>
<div class="form-group">
<label for="phone_number" class="col-sm-4 control-label">Leased:</label>
<div class="col-sm-8">
  <label class="control-checkbox"><input name="leased" type="radio" value="1" <?= $leased > 0 ? 'checked' : '' ?>> Yes</label>
  <label class="control-checkbox"><input name="leased" type="radio" value="0" <?= $leased > 0 ? '' : 'checked' ?>> No</label>
</div>
</div>
<?php } ?>
<?php if (strpos($value_config, ','."Style".',') !== FALSE) { ?>
<div class="form-group">
<label for="phone_number" class="col-sm-4 control-label">Style:</label>
<div class="col-sm-8">
  <input name="style" type="text" value="<?php echo $style; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Vehicle Size".',') !== FALSE) { ?>
<div class="form-group">
<label for="phone_number" class="col-sm-4 control-label">Vehicle Size:</label>
<div class="col-sm-8">
  <input name="vehicle_size" type="text" value="<?php echo $vehicle_size; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Color".',') !== FALSE) { ?>
<div class="form-group">
<label for="travel_task" class="col-sm-4 control-label">Colour:</label>
<div class="col-sm-8">
  <select id="color" name="color" class="chosen-select-deselect form-control" width="380">
  <option value=''></option>
      <?php
        $result = mysqli_query($dbc, "SELECT distinct(color) FROM equipment order by color");
        while($row = mysqli_fetch_assoc($result)) {
            if ($color == $row['color']) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            echo "<option ".$selected." value = '".$row['color']."'>".$row['color']."</option>";
        }
      ?>
      <option value = 'Other'>Other</option>
  </select>
</div>
</div>
<div class="form-group" style="display: none;">
<label for="travel_task" class="col-sm-4 control-label"></label>
<div class="col-sm-8">
    <input name="color" id="color_other" disabled type="text" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Trim".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="phone_number" class="col-sm-4 control-label">Trim:</label>
    <div class="col-sm-8">
      <input name="trim" type="text" value="<?php echo $trim; ?>" class="form-control"/>
    </div>
  </div>
<?php } ?>

<?php if (strpos($value_config, ','."Fuel Type".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="phone_number" class="col-sm-4 control-label">Fuel Type:</label>
    <div class="col-sm-8">
      <input name="fuel_type" type="text" value="<?php echo $fuel_type; ?>" class="form-control"/>
    </div>
  </div>
<?php } ?>

<?php if (strpos($value_config, ','."Tire Type".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Tire Type:</label>
<div class="col-sm-8">
  <input name="tire_type" type="text" value="<?php	echo $tire_type; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Drive Train".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Drive Train:</label>
<div class="col-sm-8">
  <input name="drive_train" type="text" value="<?php	echo $drive_train; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Serial #".',') !== FALSE) { ?>
<div class="form-group">
<label for="phone_number" class="col-sm-4 control-label">Serial #<span class="brand-color">*</span>:</label>
<div class="col-sm-8">
  <input name="serial_number" type="text" value="<?php echo $serial_number; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Unit #".',') !== FALSE) { ?>
<div class="form-group">
<label for="phone_number" class="col-sm-4 control-label">Unit #<span class="brand-color">*</span>:</label>
<div class="col-sm-8">
  <input name="unit_number" type="text" value="<?php echo $unit_number; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."VIN #".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="phone_number" class="col-sm-4 control-label">VIN #<span class="brand-color">*</span>:</label>
    <div class="col-sm-8">
      <input name="vin_number" type="text" value="<?php echo $vin_number; ?>" class="form-control vinnumber"/>
    </div>
  </div>
<?php } ?>

<?php if (strpos($value_config, ','."Licence Plate".',') !== FALSE) { ?>
<div class="form-group">
<label for="phone_number" class="col-sm-4 control-label">Licence Plate #<span class="brand-color">*</span>:</label>
<div class="col-sm-8">
  <input name="licence_plate" type="text" value="<?php echo $licence_plate; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

 <?php if (strpos($value_config, ','."Nickname".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="fax_number"	class="col-sm-4	control-label">Nickname:</label>
    <div class="col-sm-8">
      <input name="nickname" type="text" value="<?php	echo $nickname; ?>" class="form-control"/>
    </div>
  </div>
<?php } ?>

<?php if (strpos($value_config, ','."Staff".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="fax_number" class="col-sm-4 control-label">Staff:</label>
    <div class="col-sm-8">
      <select name="staff[]" multiple data-placeholder="Select Staff" class="chosen-select-deselect form-control">
        <?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`,`last_name`,`first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `email_address` != '' AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
        foreach($staff_list as $staff_id) {
          echo '<option '.(strpos(','.$staff.',', ','.$staff_id.',') !== FALSE ? 'selected' : '').' value="'.$staff_id.'">'.get_contact($dbc, $staff_id).'</option>';
        } ?>
      </select>
    </div>
  </div>
<?php } ?>

<?php if (strpos($value_config, ','."Region Dropdown".',') !== FALSE) {
$region = explode('*#*', $region);
foreach ($region as $single_region) { ?>
<div class="form-group region-group">
<label for="fax_number" class="col-sm-4 control-label">Region:</label>
<div class="col-sm-7">
  <select name="region[]" data-placeholder="Select Region" class="chosen-select-deselect form-control">
    <option></option>
    <?php $region_list = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0])));
    foreach ($region_list as $con_region) {
      if(in_array($con_region, $allowed_regions)) {
        echo "<option ".($con_region == $single_region ? 'selected' : '')." value='$con_region'>$con_region</option>";
      }
    } ?>
  </select>
</div>
<div class="col-sm-1">
  <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addRegion();">
  <img src="../img/remove.png" class="inline-img pull-right" onclick="removeRegion(this);">
</div>
</div>
<?php } ?>
<?php } ?>

<?php if (strpos($value_config, ','."Location Dropdown".',') !== FALSE) {
$location = explode('*#*', $location);
foreach ($location as $single_location) { ?>
<div class="form-group location-group">
<label for="fax_number" class="col-sm-4 control-label">Location:</label>
<div class="col-sm-7">
  <select name="location[]" data-placeholder="Select Location" class="chosen-select-deselect form-control">
    <option></option>
    <?php $location_list = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `con_locations` SEPARATOR ',') FROM `field_config_contacts`"))[0])));
    foreach ($location_list as $con_location) {
      if(in_array($con_location, $allowed_locations)) {
        echo "<option ".($con_location == $single_location ? 'selected' : '')." value='$con_location'>$con_location</option>";
      }
    } ?>
  </select>
</div>
<div class="col-sm-1">
  <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addLocation();">
  <img src="../img/remove.png" class="inline-img pull-right" onclick="removeLocation(this);">
</div>
</div>
<?php } ?>
<?php } ?>

<?php if (strpos($value_config, ','."Classification Dropdown".',') !== FALSE) {
$classification = explode('*#*', $classification);
foreach ($classification as $single_classification) { ?>
<div class="form-group classification-group">
<label for="fax_number" class="col-sm-4 control-label">Classification:</label>
<div class="col-sm-7">
  <select name="classification[]" data-placeholder="Select Classification" class="chosen-select-deselect form-control">
    <option data-regions="[]"></option>
    <?php $class_regions = explode(',',get_config($dbc, '%_class_regions', true, ','));
    $contact_classifications = [];
    $classification_regions = [];
    foreach(explode(',',get_config($dbc, '%_classification', true, ',')) as $i => $contact_classification) {
      $row = array_search($contact_classification, $contact_classifications);
      if($class_regions[$i] == 'ALL') {
        $class_regions[$i] = '';
      }
      if($row !== FALSE && $class_regions[$i] != '') {
        $classification_regions[$row][] = $class_regions[$i];
      } else {
        $contact_classifications[] = $contact_classification;
        $classification_regions[] = array_filter([$class_regions[$i]]);
      }
    }
    foreach ($contact_classifications as $i => $con_classification) {
      $hidden_classification = '';
      if(!empty($region) && !empty($get_equipment['region']) && empty(array_intersect($classification_regions[$i], $region)) && !empty($classification_regions[$i])) {
        $hidden_classification = 'style="display:none;"';
      }
      echo "<option ".($con_classification == $single_classification ? 'selected' : '')." data-regions='".json_encode($classification_regions[$i])."' value='$con_classification' $hidden_classification>$con_classification</option>";
    } ?>
  </select>
</div>
<div class="col-sm-1">
  <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addClassification();">
  <img src="../img/remove.png" class="inline-img pull-right" onclick="removeClassification(this);">
</div>
</div>
<?php } ?>
<?php } ?>

<?php if (strpos($value_config, ','."Year Purchased".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Year Purchased:</label>
<div class="col-sm-8">
  <input name="year_purchased" type="text" value="<?php	echo $year_purchased; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Mileage".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Mileage:</label>
<div class="col-sm-8">
  <input name="mileage" type="text" value="<?php	echo $mileage; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Hours Operated".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Hours Operated:</label>
<div class="col-sm-8">
  <input name="hours_operated" type="text" value="<?php	echo $hours_operated; ?>" class="form-control hrs_oprtd"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Cost".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Cost:</label>
<div class="col-sm-8">
  <input name="cost" value="<?php echo $cost; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."CDN Cost Per Unit".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">CDN Cost Per Unit:</label>
<div class="col-sm-8">
  <input name="cdn_cost_per_unit" value="<?php echo $cdn_cost_per_unit; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."USD Cost Per Unit".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">USD Cost Per Unit:</label>
<div class="col-sm-8">
  <input name="usd_cost_per_unit" value="<?php echo $usd_cost_per_unit; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>
<?php if (strpos($value_config, ','."Finance".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Finance:</label>
<div class="col-sm-8">
  <input name="finance" value="<?php echo $finance; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Lease".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Lease:</label>
<div class="col-sm-8">
  <input name="lease" value="<?php echo $lease; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Insurance".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Insurance Provider:</label>
<div class="col-sm-8">
  <input name="insurance" type="text" value="<?php	echo $insurance; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Insurance Contact".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Insurance Contact Name:</label>
<div class="col-sm-8">
  <input name="insurance_contact" type="text" value="<?php	echo $insurance_contact; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Insurance Phone".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Insurance Contact Phone #:</label>
<div class="col-sm-8">
  <input name="insurance_phone" type="text" value="<?php	echo $insurance_phone; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Insurance Card".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Insurance Card:</label>
    <div class="col-sm-8">
    <?php if($insurance_card != '') {
		echo '<a href="download/'.$insurance_card.'" target="_blank">View</a>'; ?>
		<input type="hidden" name="insurance_card_hidden" value="<?php echo $insurance_card; ?>" />
	<?php } ?>
    <input name="insurance_card" type="file" data-filename-placement="inside" class="form-control" />
      </div>
 </div>
<?php } ?>

<?php if (strpos($value_config, ','."Hourly Rate".',') !== FALSE) { ?>
<div class="form-group">
<label for="phone_number" class="col-sm-4 control-label">Hourly Rate:</label>
<div class="col-sm-8">
  <input name="hourly_rate" type="text" value="<?php echo $hourly_rate; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Daily Rate".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Daily Rate:</label>
<div class="col-sm-8">
  <input name="daily_rate" value="<?php echo $daily_rate; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Semi Monthly Rate".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Semi Monthly Rate:</label>
<div class="col-sm-8">
  <input name="semi_monthly_rate" value="<?php echo $semi_monthly_rate; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Monthly Rate".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Monthly Rate:</label>
<div class="col-sm-8">
  <input name="monthly_rate" value="<?php echo $monthly_rate; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Field Day Cost".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Field Day Cost:</label>
<div class="col-sm-8">
  <input name="field_day_cost" value="<?php echo $field_day_cost; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Field Day Billable".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">Field Day Billable:</label>
<div class="col-sm-8">
  <input name="field_day_billable" value="<?php echo $field_day_billable; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."HR Rate Work".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">HR Rate Work:</label>
<div class="col-sm-8">
  <input name="hr_rate_work" value="<?php echo $hr_rate_work; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."HR Rate Travel".',') !== FALSE) { ?>
<div class="form-group">
<label for="company_name" class="col-sm-4 control-label">HR Rate Travel:</label>
<div class="col-sm-8">
  <input name="hr_rate_travel" value="<?php echo $hr_rate_travel; ?>" type="text" class="form-control">
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Next Service Date".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Next Service Date:</label>
    <div class="col-sm-8">
        <input name="next_service_date" value="<?php echo $next_service_date; ?>" type="text" class="form-control datepicker"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Next Service Hours".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
<label for="first_name" class="col-sm-4 control-label text-right">Next Service Hours:</label>
<div class="col-sm-8">
<p class='triangle-isosceles' title='Click on me to close this notice.'>This service is due!</p>
    <input name="next_service" id='nsh' type="number" value="<?php	echo $next_service; ?>" class="form-control nxt-srv"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Next Service Description".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
<label for="first_name" class="col-sm-4 control-label text-right">Next Service Description:</label>
<div class="col-sm-8">
    <input name="next_serv_desc" type="text" value="<?php	echo $next_serv_desc; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Service Location".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
<label for="first_name" class="col-sm-4 control-label text-right">Service Location:</label>
<div class="col-sm-8">
    <input name="service_location" type="text" value="<?php	echo $service_location; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Last Oil Filter Change (date)".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Last Oil Filter Change (date):</label>
<div class="col-sm-8">
  <input name="last_oil_filter_change_date" type="text" value="<?php	echo $last_oil_filter_change_date; ?>" class="form-control last_oil_filter_change"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Last Oil Filter Change (km)".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Last Oil Filter Change (km):</label>
<div class="col-sm-8">
  <input name="last_oil_filter_change" type="text" value="<?php	echo $last_oil_filter_change; ?>" class="form-control last_oil_filter_change"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Next Oil Filter Change (date)".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Next Oil Filter Change (date):</label>
<div class="col-sm-8">
  <input name="next_oil_filter_change_date" type="text" value="<?php	echo $next_oil_filter_change_date; ?>" class="form-control next_oil_filter_change"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Next Oil Filter Change (km)".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Next Oil Filter Change (km):</label>
<div class="col-sm-8">
  <input name="next_oil_filter_change" type="text" value="<?php	echo $next_oil_filter_change; ?>" class="form-control next_oil_filter_change"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Last Inspection & Tune Up (date)".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Last Inspection & Tune Up (date):</label>
<div class="col-sm-8">
  <input name="last_insp_tune_up_date" type="text" value="<?php	echo $last_insp_tune_up_date; ?>" class="form-control last_insp_tune_up"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Last Inspection & Tune Up (km)".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Last Inspection & Tune Up (km):</label>
<div class="col-sm-8">
  <input name="last_insp_tune_up" type="text" value="<?php	echo $last_insp_tune_up; ?>" class="form-control last_insp_tune_up"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Next Inspection & Tune Up (date)".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Next Inspection & Tune Up (date):</label>
<div class="col-sm-8">
  <input readonly name="next_insp_tune_up_date" type="text" value="<?php	echo $next_insp_tune_up_date; ?>" class="form-control next_insp_tune_up"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Next Inspection & Tune Up (km)".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Next Inspection & Tune Up (km):</label>
<div class="col-sm-8">
  <input readonly name="next_insp_tune_up" type="text" value="<?php	echo $next_insp_tune_up; ?>" class="form-control next_insp_tune_up"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Tire Condition".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Tire Condition:</label>
<div class="col-sm-8">
  <input name="tire_condition" type="text" value="<?php	echo $tire_condition; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Last Tire Rotation (date)".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Last Tire Rotation (date):</label>
<div class="col-sm-8">
  <input name="last_tire_rotation_date" type="text" value="<?php	echo $last_tire_rotation_date; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Last Tire Rotation (km)".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Last Tire Rotation (km):</label>
<div class="col-sm-8">
  <input name="last_tire_rotation" type="text" value="<?php	echo $last_tire_rotation; ?>" class="form-control last_tire_rotation"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Next Tire Rotation (date)".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Next Tire Rotation (date):</label>
<div class="col-sm-8">
  <input name="next_tire_rotation_date" type="text" value="<?php	echo $next_tire_rotation_date; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Next Tire Rotation (km)".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Next Tire Rotation (km):</label>
<div class="col-sm-8">
  <input name="next_tire_rotation" type="text" value="<?php	echo $next_tire_rotation; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Registration Card".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="file" class="col-sm-4 control-label">Registration Card:</label>
    <div class="col-sm-8">
    <?php if($registration_card != '') { ?>
		<a href="download/<?= $registration_card ?>" target="_blank">View</a>
		<input type="hidden" name="registration_card_hidden" value="<?= $registration_card ?>" />
	<?php } ?>
    <input name="registration_card" type="file" data-filename-placement="inside" class="form-control" />
      </div>
 </div>
<?php } ?>

<?php if (strpos($value_config, ','."Registration Renewal date".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Registration Renewal Date:</label>
    <div class="col-sm-8">
        <input name="reg_renewal_date" value="<?php echo $reg_renewal_date; ?>" type="text" class="form-control datepicker"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Registration Reminder".',') !== FALSE) { ?>
<div class="form-group clearfix"><?php
    $verify     = 'equipment#*#reg_renewal_date#*#equipmentid#*#'.$equipmentid;
	$reminder   = mysqli_query ( $dbc, "SELECT * FROM `reminders` WHERE `verify` LIKE '".$verify."%' AND `sent`=0 ORDER BY `reminder_date` ASC" );
	$staff      = [];
    $ins_reminder_date = '';

    if ( mysqli_num_rows($reminder) > 0 ) {
		$reminder = mysqli_fetch_array($reminder);
        $ins_reminder_date = $reminder['reminder_date'];
        $staff    = explode ( '<br>', get_multiple_contact($dbc, $reminder['contactid']) );
    } ?>

    <!--
    <label for="first_name" class="col-sm-4 control-label text-right">Upcoming Scheduled Reminder:</label>
    <div class="col-sm-8">
        Date: <?php //$reminder['reminder_date'] ?><br />Staff: <?php //get_multiple_contact($dbc, $reminder['contactid']) ?>
    </div>
    -->

    <label for="first_name" class="col-sm-4 control-label text-right">Registration Reminder Date:</label>
    <div class="col-sm-8">
        <input name="reg_reminder_date" value="<?= $ins_reminder_date; ?>" type="text" class="form-control datepicker">
    </div>
    <label for="first_name" class="col-sm-4 control-label text-right">Staff Assigned Reminder:</label>
    <div class="col-sm-8">
        <select name="reg_reminder_staff[]" data-placeholder="Select Staff" multiple class="chosen-select-deselect">
            <?php
            $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`,`last_name`,`first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `email_address` != '' AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
            foreach($staff_list as $staff_id) {
                $staff_name  = get_contact($dbc, $staff_id);
                $staff_email = get_email($dbc, $staff_id);
                $selected    = in_array($staff_name, $staff) ? 'selected="selected"' : '';
                echo "<option value='$staff_id' $selected>$staff_name: $staff_email</option>\n";
            } ?>
        </select>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Insurance Renewal Date".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Insurance Renewal Date:</label>
    <div class="col-sm-8">
        <input name="insurance_renewal" value="<?php echo $insurance_renewal; ?>" type="text" class="form-control datepicker"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Insurance Reminder".',') !== FALSE) { ?>
<div class="form-group clearfix"><?php
    $verify     = 'equipment#*#insurance_renewal#*#equipmentid#*#'.$equipmentid;
	$reminder   = mysqli_query($dbc, "SELECT * FROM `reminders` WHERE `verify` LIKE '".$verify."%' AND `sent`=0 ORDER BY `reminder_date` ASC");
	$staff      = [];
    $ins_reminder_date = '';

    if ( mysqli_num_rows($reminder) > 0 ) {
		$reminder = mysqli_fetch_array($reminder);
        $ins_reminder_date = $reminder['reminder_date'];
        $staff    = explode ( '<br>', get_multiple_contact($dbc, $reminder['contactid']) );
    } ?>

    <!--
    <label for="first_name" class="col-sm-4 control-label text-right">Upcoming Scheduled Reminder:</label>
    <div class="col-sm-8">
        Date: <?php //$reminder['reminder_date'] ?><br />Staff: <?php //get_multiple_contact($dbc, $reminder['contactid']) ?>
    </div>
    -->

    <label for="first_name" class="col-sm-4 control-label text-right">Insurance Reminder Date:</label>
    <div class="col-sm-8">
        <input name="ins_reminder_date" value="<?= $ins_reminder_date; ?>" type="text" class="form-control datepicker">
    </div>
    <label for="first_name" class="col-sm-4 control-label text-right">Staff Assigned Reminder:</label>
    <div class="col-sm-8">
        <select name="ins_reminder_staff[]" data-placeholder="Select Staff" multiple class="chosen-select-deselect">
            <?php
            $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`,`last_name`,`first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `email_address` != '' AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
            foreach($staff_list as $staff_id) {
                $staff_name  = get_contact($dbc, $staff_id);
                $staff_email = get_email($dbc, $staff_id);
                $selected    = in_array($staff_name, $staff) ? 'selected="selected"' : '';
                echo "<option value='$staff_id' $selected>$staff_name: $staff_email</option>\n";
            } ?>
        </select>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Location".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Location:</label>
<div class="col-sm-8">
  <input name="location" type="text" value="<?php	echo $location; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Current Address".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">Current Address <em>(Street, City, Province, Postal Code)</em>:</label>
<div class="col-sm-8">
  <input name="current_address" type="text" value="<?php	echo $current_address; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."LSD".',') !== FALSE) { ?>
<div class="form-group">
<label for="fax_number"	class="col-sm-4	control-label">LSD:</label>
<div class="col-sm-8">
  <input name="lsd" type="text" value="<?php	echo $lsd; ?>" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Status".',') !== FALSE) { ?>
<div class="form-group">
<label for="phone_number" class="col-sm-4 control-label">Status<span class="brand-color">*</span>:</label>
<div class="col-sm-8">
  <select id="status" name="status" class="chosen-select-deselect form-control" width="380">
      <option value=''></option>
      <option value='Active' <?php if ($status=='Active') echo 'selected="selected"';?> >Active</option>
      <option value='In Service' <?php if ($status=='In Service') echo 'selected="selected"';?> >In Service</option>
      <option value='Service Required' <?php if ($status=='Service Required') echo 'selected="selected"';?> >Service Required</option>
      <option value='On Site' <?php if ($status=='On Site') echo 'selected="selected"';?> >On Site</option>
      <option value='Inactive' <?php if ($status=='Inactive') echo 'selected="selected"';?> >Inactive</option>
      <option value='Sold' <?php if ($status=='Sold') echo 'selected="selected"';?> >Sold</option>
  </select>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Volume".',') !== FALSE) { ?>
<div class="form-group">
<label for="first_name[]" class="col-sm-4 control-label">Equipment Volume (<?= get_config($dbc, 'volume_units') ?>):</label>
<div class="col-sm-8">
  <input type="number" min="0" name="volume" class="form-control" value="<?php echo $volume; ?>">
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Ownership Status".',') !== FALSE) { ?>
<div class="form-group">
<label for="travel_task" class="col-sm-4 control-label">Ownership Status:</label>
<div class="col-sm-8">
  <select id="ownership_status" name="ownership_status" class="chosen-select-deselect form-control" width="380">
  <option value=''></option>
      <?php
        $result = mysqli_query($dbc, "SELECT distinct(ownership_status) FROM equipment order by ownership_status");
        while($row = mysqli_fetch_assoc($result)) {
            if ($ownership_status == $row['ownership_status']) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            echo "<option ".$selected." value = '".$row['ownership_status']."'>".$row['ownership_status']."</option>";
        }
      ?>
      <option value = 'Other'>Other</option>
  </select>
</div>
</div>

<div class="form-group" id="new_ownership_status" style="display: none;">
<label for="travel_task" class="col-sm-4 control-label">Ownership Status Name:</label>
<div class="col-sm-8">
    <input name="new_ownership_status" type="text" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Assigned Status".',') !== FALSE) { ?>
	<script>
	$(document).ready(function() {
		$('[name=assigned_status]').first().change(function() {
			var new_block = $('#new_assigned_status');
			if(this.value == 'Other') {
				new_block.show();
				new_block.find('[name=assigned_status]').removeAttr('disabled');
			} else {
				new_block.hide();
				new_block.find('[name=assigned_status]').prop('disabled','disabled');
			}
		});
	});
	</script>
	<div class="form-group">
		<label for="travel_task" class="col-sm-4 control-label">Assigned Status:</label>
		<div class="col-sm-8">
			<select name="assigned_status" class="chosen-select-deselect form-control">
			<option value=''></option>
				<?php $result = mysqli_query($dbc, "SELECT DISTINCT(assigned_status) FROM equipment order by assigned_status");
				while($row = mysqli_fetch_assoc($result)) {
					echo "<option ".($assigned_status == $row['assigned_status'] ? 'selected' : '')." value = '".$row['assigned_status']."'>".$row['assigned_status']."</option>";
				} ?>
				<option value = 'Other'>Other</option>
			</select>
		</div>
	</div>

	<div class="form-group" id="new_assigned_status" style="display: none;">
		<label for="assigned_status" class="col-sm-4 control-label">Other Assigned Status:</label>
		<div class="col-sm-8">
			<input name="assigned_status" disabled type="text" class="form-control"/>
		</div>
	</div>
<?php } ?>


<?php if (strpos($value_config, ','."Quote Description".',') !== FALSE) { ?>
<div class="form-group">
<label for="first_name[]" class="col-sm-4 control-label">Quote Description:</label>
<div class="col-sm-8">
  <textarea name="quote_description" rows="5" cols="50" class="form-control"><?php echo $quote_description; ?></textarea>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Notes".',') !== FALSE) { ?>
<div class="form-group">
<label for="first_name[]" class="col-sm-4 control-label">Notes:</label>
<div class="col-sm-8">
  <textarea name="notes" rows="5" cols="50" class="form-control"><?php echo $notes; ?></textarea>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."CVIP Ticket Renewal Date".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">CVIP Ticket Renewal Date:</label>
    <div class="col-sm-8">
        <input name="cviprenewal" value="<?php echo $cviprenewal; ?>" type="text" class="form-control datepicker"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Service Staff".',') !== FALSE) { ?>
<div class="form-group clearfix">
    <label for="first_name" class="col-sm-4 control-label text-right">Service Staff:</label>
    <div class="col-sm-8">
        <select name="service_staff" data-placeholder="Select Staff" class="chosen-select-deselect"><option></option>
			<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`,`last_name`,`first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `email_address` != '' AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
			foreach($staff_list as $staff_id) {
				$staff_name = get_contact($dbc, $staff_id);
				echo "<option ".($service_staff == $staff_id ? 'selected' : '')." value='$staff_id'>$staff_name</option>\n";
			} ?>
		</select>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Vehicle Access Code".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Vehicle Access Code:</label>
    <div class="col-sm-8">
        <input name="vehicle_access_code" value="<?php echo $vehicle_access_code; ?>" type="text" class="form-control"></p>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Cargo".',') !== FALSE) { ?>
<div class="form-group">
<label for="travel_task" class="col-sm-4 control-label">Cargo:</label>
<div class="col-sm-8">
  <select id="cargo" name="cargo" class="chosen-select-deselect form-control" width="380">
  <option value=''></option>
      <?php
        $result = mysqli_query($dbc, "SELECT distinct(cargo) FROM equipment order by cargo");
        while($row = mysqli_fetch_assoc($result)) {
            if ($cargo == $row['cargo']) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            echo "<option ".$selected." value = '".$row['cargo']."'>".$row['cargo']."</option>";
        }
      ?>
      <option value = 'Other'>Other</option>
  </select>
</div>
</div>

<div class="form-group" id="new_cargo" style="display: none;">
<label for="travel_task" class="col-sm-4 control-label">New Cargo:</label>
<div class="col-sm-8">
    <input name="new_cargo" type="text" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Lessor".',') !== FALSE) { ?>
<div class="form-group">
<label for="travel_task" class="col-sm-4 control-label">Lessor:</label>
<div class="col-sm-8">
  <select id="lessor" name="lessor" class="chosen-select-deselect form-control" width="380">
  <option value=''></option>
      <?php
        $result = mysqli_query($dbc, "SELECT distinct(lessor) FROM equipment order by lessor");
        while($row = mysqli_fetch_assoc($result)) {
            if ($lessor == $row['lessor']) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            echo "<option ".$selected." value = '".$row['lessor']."'>".$row['lessor']."</option>";
        }
      ?>
      <option value = 'Other'>Other</option>
  </select>
</div>
</div>

<div class="form-group" id="new_lessor" style="display: none;">
<label for="travel_task" class="col-sm-4 control-label">New Lessor:</label>
<div class="col-sm-8">
    <input name="new_lessor" type="text" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Group".',') !== FALSE) { ?>
<div class="form-group">
<label for="travel_task" class="col-sm-4 control-label">Group:</label>
<div class="col-sm-8">
  <select id="group" name="group" class="chosen-select-deselect form-control" width="380">
  <option value=''></option>
      <?php
        $result = mysqli_query($dbc, "SELECT distinct(`group`) FROM equipment order by `group`");
        while($row = mysqli_fetch_assoc($result)) {
            if ($group == $row['group']) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            echo "<option ".$selected." value = '".$row['group']."'>".$row['group']."</option>";
        }
      ?>
      <option value = 'Other'>Other</option>
  </select>
</div>
</div>

<div class="form-group" id="new_group" style="display: none;">
<label for="travel_task" class="col-sm-4 control-label">New Group:</label>
<div class="col-sm-8">
    <input name="new_group" type="text" class="form-control"/>
</div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Use".',') !== FALSE) { ?>
<div class="form-group">
<label for="travel_task" class="col-sm-4 control-label">Use:</label>
<div class="col-sm-8">
  <select id="use" name="use" class="chosen-select-deselect form-control" width="380">
  <option value=''></option>
      <?php
        $result = mysqli_query($dbc, "SELECT distinct(`use`) FROM equipment order by `use`");
        while($row = mysqli_fetch_assoc($result)) {
            if ($use == $row['use']) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            echo "<option ".$selected." value = '".$row['use']."'>".$row['use']."</option>";
        }
      ?>
      <option value = 'Other'>Other</option>
  </select>
</div>
</div>

<div class="form-group" id="new_use" style="display: none;">
<label for="travel_task" class="col-sm-4 control-label">New Use:</label>
<div class="col-sm-8">
    <input name="new_use" type="text" class="form-control"/>
</div>
</div>
<?php } ?>