<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Staff:</label>
    <div class="col-sm-8">
        <select name="followup_by"  id="followup_by" data-placeholder="Select a Staff..." class="chosen-select-deselect form-control" width="380">
            <option value=""></option>
			  <?php
				$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status` > 0"),MYSQLI_ASSOC));
				foreach($query as $id) {
					$selected = '';
					$selected = ($id == $followup_by || $id == $contactid) ? 'selected = "selected"' : '';
					echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
				}
			  ?>
        </select>
    </div>
</div>

<div class="form-group clearfix completion_date">
    <label for="followup_date" class="col-sm-4 control-label text-right">Date:</label>
    <div class="col-sm-8">
        <input type="text" name="followup_date" id="followup_date" value="<?php echo $followup_date; ?>" class="datepicker form-control">
    </div>
</div>