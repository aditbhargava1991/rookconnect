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
			<?php foreach(get_teams($dbc, " AND IF(`end_date` = '0000-00-00','9999-12-31',`end_date`) >= '".date('Y-m-d')."'") as $team) {
				$team_staff = get_team_contactids($dbc, $team['teamid']);
				if(count($team_staff) > 1) {
					echo "<option value='group' data-members='".json_encode($team_staff)."'>Staff Group: ".get_team_name($dbc, $team['teamid']).(!empty($team['team_name']) ? " (".get_team_name($dbc, $team['teamid'], ', ', 1).")" : '')."</option>";
				}
			}
            $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
			foreach($staff_list as $id) { ?>
                <option <?php if (strpos(','.$assign_staffid.',', ','.$id.',') !== FALSE) { echo " selected"; } ?> value="<?= $id ?>"><?= get_contact($dbc, $id) ?></option>
            <?php } ?>
        </select>
    </div>
</div>
