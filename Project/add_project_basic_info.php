<script type="text/javascript">
$(document).ready(function() {
    updateContactList();
});
$(document).on('change', 'select[name="contact_region"]', function() { updateContactList(); });
$(document).on('change', 'select[name="contact_location"]', function() { updateContactList(); });
$(document).on('change', 'select[name="contact_classification"]', function() { updateContactList(); });
$(document).on('change', 'select[name="projectclientid[]"]', function() { updateContactFilters(); });
function updateContactList() {
    var selected_region = $("#contact_region").val();
    var selected_location = $("#contact_location").val();
    var selected_classification = $("#contact_classification").val();
    $('[name="projectclientid[]"]').find('option').each(function() {
        if($(this).data('region') != selected_region && $(this).data('region') && selected_region) {
            $(this).hide();
        } else if($(this).data('location') != selected_location && $(this).data('location') && selected_location) {
            $(this).hide();
        } else if($(this).data('classification') != selected_classification && $(this).data('classification') && selected_classification) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });
    $('[name="projectclientid[]"]').trigger('change.select2');
}
</script>

<?php if (strpos($value_config, ','."Information Contact Region".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Region<span class="brand-color"></span>:</label>
    <div class="col-sm-8">
        <select name="contact_region" id="contact_region" data-placeholder="Select a Region..." class="chosen-select-deselect form-control" width="380">
            <option value=''></option>
            <?php
            $contact_regions = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0])));
            $selected_region = '';
            if(!empty($_GET['clientid'])) {
                $selected_region = get_contact($dbc, $_GET['clientid'], 'region');
            } else {
                $client_list = explode(',',$clientid);
                foreach ($client_list as $id) {
                    $selected_region = get_contact($dbc, $id, 'region');
                    if (!empty($selected_region)) {
                        break;
                    }
                }
            }
            foreach ($contact_regions as $contact_region) {
                if ($selected_region == $contact_region) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                echo "<option ".$selected." value='".$contact_region."'>".$contact_region.'</option>';
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Information Contact Location".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Location<span class="brand-color"></span>:</label>
    <div class="col-sm-8">
        <select name="contact_location" id="contact_location" data-placeholder="Select a Location..." class="chosen-select-deselect form-control" width="380">
            <option value=''></option>
            <?php
            $contact_locations = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `con_locations` SEPARATOR ',') FROM `field_config_contacts`"))[0])));
            $selected_location = '';
            if(!empty($_GET['clientid'])) {
                $selected_location = get_contact($dbc, $_GET['clientid'], 'con_locations');
            } else {
                $client_list = explode(',',$clientid);
                foreach ($client_list as $id) {
                    $selected_location = get_contact($dbc, $id, 'con_locations');
                    if (!empty($selected_location)) {
                        break;
                    }
                }
            }
            foreach ($contact_locations as $contact_location) {
                if ($selected_location == $contact_location) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                echo "<option ".$selected." value='".$contact_location."'>".$contact_location.'</option>';
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Information Contact Classification".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Classification<span class="brand-color"></span>:</label>
    <div class="col-sm-8">
        <select name="contact_classification" id="contact_classification" data-placeholder="Select a Location..." class="chosen-select-deselect form-control" width="380">
            <option value=''></option>
            <?php
            $contact_classifications = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_locations'"))[0])));
            $selected_classification = '';
            if(!empty($_GET['clientid'])) {
                $selected_classification = get_contact($dbc, $_GET['clientid'], 'classification');
            } else {
                $client_list = explode(',',$clientid);
                foreach ($client_list as $id) {
                    $selected_classification = get_contact($dbc, $id, 'classification');
                    if (!empty($selected_classification)) {
                        break;
                    }
                }
            }
            foreach ($contact_classifications as $contact_classification) {
                if ($selected_classification == $contact_classification) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                echo "<option ".$selected." value='".$contact_classification."'>".$contact_classification.'</option>';
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Information Business".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date"><?php
	if ( !empty($intakeid) && !empty($contactid_intake) ) {
		/*
		 * Use the Contact as the Business when creating a Project from Intake tile
		 * Hide the Business section
		 */
		$row = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `contactid` FROM `contacts` WHERE `contactid`='$contactid_intake' AND `deleted`=0" ) ); ?>
		<input type="hidden" name="businessid" id="businessid" value="<?php echo $row['contactid']; ?>" /><?php

	} else { ?>

		<label for="first_name" class="col-sm-4 control-label text-right">Business<span class="brand-color">*</span>:</label>
		<div class="col-sm-8">
			<select name="businessid" <?php echo $disable_business; ?> id="businessid" data-placeholder="Select a Business..." class="chosen-select-deselect form-control" width="380">
				<option value=''></option>
				<?php
				$query = mysqli_query($dbc,"SELECT contactid, name, region, con_locations, classification FROM contacts WHERE category='Business' AND deleted=0 ORDER BY category");
				while($row = mysqli_fetch_array($query)) {
					if ($businessid== $row['contactid']) {
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}
					echo "<option ".$selected." value='". $row['contactid']."' data-region='".$row['region']."' data-location='".$row['con_locations']."' data-classification='".$row['classification']."'>".decryptIt($row['name']).'</option>';
				}
				?>
			</select>
		</div>
        <?php
	} ?>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Information Contact".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Contact<span class="brand-color">*</span>:</label>
    <div class="col-sm-8">
        <select name="projectclientid[]" multiple <?php echo $disable_client; ?> id="projectclientid" data-placeholder="Select a Contact..." class="chosen-select-deselect form-control" width="380">
            <?php
            $cat = '';
			$cat_list = [];
			$category_group = [];

			$query = mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE (businessid='$businessid' OR `contactid`='$businessid' OR '$businessid'='') AND deleted=0 AND status=1 AND `category` NOT IN ('Business') ORDER BY `category`");
				/*
				 * Projects created from Intake tile does not have a businessid.
				 * So we get the contactid.
				 */

			$client_list = explode(',',$clientid);
            while($row = mysqli_fetch_array($query)) {
                if($cat != $row['category']) {
					$cat_list[$cat] = sort_contacts_array($category_group);
                    $cat = $row['category'];
					$category_group = [];
                }
				$category_group[] = [ 'contactid' => $row['contactid'], 'last_name' => $row['last_name'], 'first_name' => $row['first_name'] ];
            }
			$cat_list[$cat] = sort_contacts_array($category_group);
			foreach($cat_list as $cat => $id_list) {
				echo '<optgroup label="'.$cat.'">';
				foreach($id_list as $id) {
					$name = get_client($dbc, $id);
					$name = ($name == '' ? get_contact($dbc, $id) : $name);
					echo "<option ".(in_array($id,$client_list) ? 'selected' : '')." value='$id' data-region='".get_contact($dbc, $id, 'region')."' data-location='".get_contact($dbc, $id, 'con_locations')."' data-classification='".get_contact($dbc, $id, 'classification')."'>".$name.'</option>';
				}
			} ?>
        </select>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Information Rate Card".',') !== FALSE) { ?>
<div class="form-group clearfix">
    <label for="first_name" class="col-sm-4 control-label text-right">Rate Card:</label>
    <div class="col-sm-8">
        <select name="ratecardid" <?php echo $disable_rc; ?> id="ratecardid" data-placeholder="Select a Rate Card..." class="chosen-select-deselect form-control" width="380">
            <option value=''></option>
            <?php
            $query = mysqli_query($dbc,"SELECT ratecardid, rate_card_name FROM rate_card WHERE on_off=1 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
            while($row = mysqli_fetch_array($query)) {
                if ($ratecardid == $row['ratecardid']) {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                echo "<option ".$selected." value='". $row['ratecardid']."'>".$row['rate_card_name'].'</option>';
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Information Project Type".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Type<span class="brand-color">*</span>:</label>
    <div class="col-sm-8">

        <input type="hidden" name='projecttype' value="<?php echo $_GET['type']; ?>" />

        <select name="projecttype" id="projecttype" data-placeholder="Select a Type..." class="chosen-select-deselect form-control" width="380">
            <option value=''></option>
			<?php $project_tabs = get_config($dbc, 'project_tabs');
			$project_tabs = explode(',',$project_tabs);
			foreach($project_tabs as $item) {
				$var_name = preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)));
				if($var_name == 'client' || check_subtab_persmission($dbc, 'project', ROLE, $var_name) == 1) {
					echo "<option ".($projecttype == $var_name ? ' selected' : '')." value='$var_name'>$item</option>";
				}
			} ?>
        </select>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Information Project Short Name".',') !== FALSE) { ?>
<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right"><?php echo (PROJECT_TILE == 'Projects' ? 'Project' : (PROJECT_TILE == 'Jobs' ? 'Job' : PROJECT_TILE)); ?> Short Name<span class="brand-color">*</span>:</label>
    <div class="col-sm-8">
        <input name="project_name" value="<?php echo $project_name; ?>" id="project_name" type="text" class="form-control"></p>
    </div>
</div>
<?php } ?>
