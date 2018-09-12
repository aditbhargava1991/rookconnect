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
				$query = mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='Business' AND deleted=0 ORDER BY category");
				while($row = mysqli_fetch_array($query)) {
					if ($businessid== $row['contactid']) {
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}
					echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['name']).'</option>';
				}
				?>
			</select>
		</div><?php
	} ?>
</div>

<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Contact<span class="brand-color">*</span>:</label>
    <div class="col-sm-8">
        <select name="projectclientid[]" multiple <?php echo $disable_client; ?> id="projectclientid" data-placeholder="Select a Contact..." class="chosen-select-deselect form-control" width="380">
            <?php
            $cat = '';
			$cat_list = [];
			$category_group = [];

			$query = mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE businessid='$businessid' AND deleted=0 AND status=1 ORDER BY `category`");
			if ( mysqli_num_rows($query) == 0 ) {
				/*
				 * Projects created from Intake tile does not have a businessid.
				 * So we get the contactid.
				 */
				$query = mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE contactid='$businessid' AND deleted=0 AND status=1 ORDER BY `category`");
			}

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
					echo "<option ".(in_array($id,$client_list) ? 'selected' : '')." value='$id'>".$name.'</option>';
				}
			} ?>
        </select>
    </div>
</div>

<div class="form-group clearfix">
    <label for="first_name" class="col-sm-4 control-label text-right">Rate Card:</label>
    <div class="col-sm-8">
        <select name="ratecardid" <?php echo $disable_rc; ?> id="ratecardid" data-placeholder="Select a Rate Card..." class="chosen-select-deselect form-control" width="380">
            <option value=''></option>
            <?php
            $query = mysqli_query($dbc,"SELECT ratecardid, rate_card_name FROM rate_card WHERE on_off=1");
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

<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Type<span class="brand-color">*</span>:</label>
    <div class="col-sm-8">

        <input type="hidden" name='projecttype' value="<?php echo $_GET['type']; ?>" />

        <select name="projecttype" id="projecttype" data-placeholder="Select a Type..." class="chosen-select-deselect form-control" width="380">
            <option value=''></option>
			<?php $jobs_tabs = get_config($dbc, 'jobs_tabs');
			$jobs_tabs = explode(',',$jobs_tabs);
			foreach($jobs_tabs as $item) {
				$var_name = preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)));
				if($var_name == 'client' || check_subtab_persmission($dbc, 'project', ROLE, $var_name) == 1) {
					echo "<option ".($projecttype == $var_name ? ' selected' : '')." value='$var_name'>$item</option>";
				}
			} ?>
        </select>
    </div>
</div>

<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right"><?php echo (JOBS_TILE == 'Projects' ? 'Project' : (JOBS_TILE == 'Jobs' ? 'Job' : JOBS_TILE)); ?> Short Name<span class="brand-color">*</span>:</label>
    <div class="col-sm-8">
        <input name="project_name" value="<?php echo $project_name; ?>" id="project_name" type="text" class="form-control"></p>
    </div>
</div>