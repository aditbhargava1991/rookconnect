 <div class="form-group">
  <label for="ship_country" class="col-sm-4 control-label">Lead Created by:</label>
  <div class="col-sm-8">
        <?php echo decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']); ?>
  </div>
</div>

<div class="form-group">
  <label for="ship_country" class="col-sm-4 control-label">Primary Staff:</label>
  <div class="col-sm-8">
        <select data-placeholder="Select a Staff Member..." name="primary_staff" class="chosen-select-deselect form-control" width="380">
          <option value=""></option>
			<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND status > 0"),MYSQLI_ASSOC));
			foreach($query as $id) { ?>
				<option <?php if ($id == $primary_staff) { echo " selected"; } ?> value='<?php echo  $id; ?>' ><?php echo get_contact($dbc, $id); ?></option>
			<?php } ?>
        </select>

  </div>
</div>

<div class="form-group">
  <label for="ship_country" class="col-sm-4 control-label">Share Lead:</label>
  <div class="col-sm-8">
        <select data-placeholder="Select a Staff Member..." multiple name="share_lead[]" class="chosen-select-deselect form-control" width="380">
			<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND status > 0"),MYSQLI_ASSOC));
			foreach($query as $id) { ?>
				<option <?php if (strpos(','.$share_lead.',', ','.$id.',') !== false) { echo " selected"; } ?> value='<?php echo  $id; ?>' ><?php echo get_contact($dbc, $id); ?></option>
			<?php } ?>
        </select>

  </div>
</div>
