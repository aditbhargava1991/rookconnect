<script>
$(document).ready(function() {
	$('#assign_staffid').change(function() {
		$('#assign_staffid option:selected').each(function() {
			if($(this).val() == 'group') {
				$(this).removeAttr('selected');
				var members = $(this).data('members');
				$(members).each(function() {
					if(!isNaN(this)) {
						$('#assign_staffid option[value='+this.valueOf()+']').prop('selected',true);
					}
				});
				$('#assign_staffid').trigger('change.select2');
			}
		});
	});
});
</script>
<div class="form-group clearfix completion_date">
    <label for="first_name" class="col-sm-4 control-label text-right">Staff<span class="brand-color">*</span>:</label>
    <div class="col-sm-8">
        <select multiple name="assign_staffid[]" id="assign_staffid" data-placeholder="Select Staff" class="chosen-select-deselect form-control" width="380">
            <?php
			$groups = mysqli_fetch_array(mysqli_query($dbc,"SELECT `estimate_groups` FROM `field_config_cost_estimate`"));
			$groups = explode('#*#',$groups['estimate_groups']);
			foreach($groups as $group) {
				if($group != '') {
					$group = explode(',',$group);
					echo "<option value='group' data-members='".json_encode($group)."'>Staff Group: ".$group[0]." (";
					$group_names = '';
					foreach($group as $individual) {
						$group_names .= (is_numeric($individual) ? get_contact($dbc, $individual).', ' : '');
					}
					echo trim($group_names, ',');
					echo ")</option>";
				}
			}
            $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Staff' AND deleted=0 AND `status`=1"),MYSQLI_ASSOC));
			foreach($staff_list as $id) { ?>
                <option <?php if (strpos(','.$assign_staffid.',', ','.$id.',') !== FALSE) { echo " selected"; } ?> value="<?= $id ?>"><?= get_contact($dbc, $id) ?></option>
            <?php } ?>
        </select>
    </div>
</div>
