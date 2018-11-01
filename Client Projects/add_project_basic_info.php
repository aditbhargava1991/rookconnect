<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Contact<span class="brand-color">*</span>:</label>
    <div class="col-sm-8">
        <select name="projectclientid[]" multiple <?php echo $disable_client; ?> id="projectclientid" data-placeholder="Select a Contact..." class="chosen-select-deselect form-control" width="380">
            <?php
            $cat = '';
			$cat_list = [];
			$category_group = [];

			// Select contacts if they are attached to the selected business, or they are the businessid contact as  from the Intake tile
			$query = mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE deleted=0 AND status=1 ORDER BY `category`");

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

<input type="hidden" name='projecttype' value="Client" />

<div class="form-group">
    <label for="first_name" class="col-sm-4 control-label text-right">Project Short Name<span class="brand-color">*</span>:</label>
    <div class="col-sm-8">
        <input name="project_name" value="<?php echo $project_name; ?>" id="project_name" type="text" class="form-control"></p>
    </div>
</div>