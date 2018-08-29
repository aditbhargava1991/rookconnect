<?php if (strpos($value_config, ','."Date of Meeting".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Date of Meeting:</label>
    <div class="col-sm-8">
        <input name="date_of_meeting" value="<?php echo $date_of_meeting; ?>" type="text" class="datepicker form-control">
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Time of Meeting".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Start Time of Meeting:</label>
    <div class="col-sm-8">
        <input type="text" name="time_of_meeting" value="<?php echo $time_of_meeting; ?>"  class="datetimepicker form-control">
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."End Time of Meeting".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">End Time of Meeting:</label>
    <div class="col-sm-8">
        <input type="text" name="end_time_of_meeting" value="<?php echo $end_time_of_meeting; ?>"  class="datetimepicker form-control">
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Location".',') !== FALSE) { ?>
<div class="form-group clearfix">
    <label for="first_name" class="col-sm-4 control-label text-right">Location:</label>
    <div class="col-sm-8">
        <select name="location" data-placeholder="Select a Location..." class="location chosen-select-deselect form-control" width="380">
            <option value=''></option>
            <option <?php if ($location == 'Fresh Focus Media') { echo  'selected="selected"'; } ?> value='Fresh Focus Media'>Fresh Focus Media</option>
            <option <?php if ($location == $business_address) { echo  'selected="selected"'; } ?> value='<?php echo $business_address; ?>'><?php echo $business_address; ?></option>
            <option <?php if (($location != $business_address) && ($location != 'Fresh Focus Media') && ($location != '')) { echo  'selected="selected"'; } ?> value='Other'>Other <?php echo $location; ?></option>
        </select>
    </div>
</div>

<div class="form-group clearfix other_location">
    <label for="first_name" class="col-sm-4 control-label text-right">Other Location:</label>
    <div class="col-sm-8">
        <input type="text" name="other_location" value="<?php echo $other_location; ?>"  class="form-control">
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Meeting Requested by".',') !== FALSE) { ?>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Meeting Requested By:</label>
    <div class="col-sm-8">
        <select name="meeting_requested_by" <?php echo $disable_client; ?> data-placeholder="Select a Contact..." class="chosen-select-deselect form-control" width="380">
            <option value=''></option>
            <?php
            $cat = '';
			$cat_list = [];
			$this_list = [];
            $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name, category FROM contacts WHERE ((businessid='$businessid' OR '$businessid' = '') OR `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.") AND `deleted`=0 AND `status`=1 AND `category` NOT IN ('Business','Sites') ORDER BY category");
            while($row = mysqli_fetch_array($query)) {
                if($cat != $row['category']) {
					$cat_list[$cat] = sort_contacts_array($this_list);
                    $cat = $row['category'];
					$this_list = [];
                }
				$this_list[] = [ 'contactid' => $row['contactid'], 'last_name' => $row['last_name'], 'first_name' => $row['first_name'] ];
            }
			$cat_list[$cat] = sort_contacts_array($this_list);
			foreach($cat_list as $cat => $id_list) {
				echo '<optgroup label="'.$cat.'">';
				foreach($id_list as $id) {
					$name = get_contact($dbc, $id);
					echo "<option ".($meeting_requested_by == $id ? 'selected' : '')." value='".$id."'>".get_contact($dbc, $id).'</option>';
				}
			} ?>
        </select>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Meeting Objective".',') !== FALSE) { ?>
<div class="form-group clearfix">
    <label for="first_name" class="col-sm-4 control-label text-right">Meeting Objective:</label>
    <div class="col-sm-8">
        <input type="text" name="meeting_objective" value="<?php echo $meeting_objective; ?>"  class="form-control">
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Items to Bring".',') !== FALSE) { ?>
<div class="form-group clearfix">
    <label for="first_name" class="col-sm-4 control-label text-right">Items to Bring:</label>
    <div class="col-sm-8">
        <input type="text" name="items_to_bring" value="<?php echo $items_to_bring; ?>"  class="form-control">
    </div>
</div>
<?php } ?>
