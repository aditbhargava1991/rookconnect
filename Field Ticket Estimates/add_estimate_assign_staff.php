<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Staff<span class="brand-color">*</span>:</label>
    <div class="col-sm-8">
        <select multiple name="assign_staffid[]" id="assign_staffid" data-placeholder="Choose an Option..." class="chosen-select-deselect form-control" width="380">
			<?php
				$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Staff' AND deleted=0 AND `status`=1"),MYSQLI_ASSOC));
				foreach($query as $id) {
					$selected = '';
					$selected = strpos(','.$assign_staffid.',', ','.$id.',') !== FALSE ? 'selected = "selected"' : '';
					echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
				}
			  ?>
        </select>
    </div>
</div>
