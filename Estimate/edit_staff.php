<?php include_once('../include.php');
checkAuthorised('estimate');
if(!isset($estimate)) {
	$estimateid = filter_var($estimateid,FILTER_SANITIZE_STRING);
	$estimate = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `estimate` WHERE `estimateid`='$estimateid'"));
}
$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `last_name`, `first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0 AND `show_hide_user`=1")); ?>
<script>
function addStaff() {
	var row = $('[name="assign_staffid[]"]').last().closest('.form-group');
	var clone = row.clone();
	resetChosen(clone.find("select[class*=chosen]"));
	row.after(clone);
	$('input,select').off('change', saveField).change(saveField);
}
function removeStaff(img) {
	if($('[name="assign_staffid[]"]').length <= 1) {
		addStaff();
	}
	$(img).closest('.form-group').remove();
	$('[name="assign_staffid[]"]').last().change();
}
function assignGroup(group) {
	group_list = $(group).data('group');
	group_list.forEach(function(staff_id) {
		if($('[name="assign_staffid[]"]').filter(function() { return $(this).val() == staff_id; }).length == 0) {
			var empty_select = $('[name="assign_staffid[]"]').filter(function() { return $(this).val() == undefined || $(this).val() == ''; }).first();
			if(empty_select.length > 0) {
				$(empty_select).val(staff_id);
				$(empty_select).trigger('change.select2');
			} else {
				addStaff();
				$('[name="assign_staffid[]"]').last().val(staff_id).trigger('change.select2');
			}
		}
	});
	$('[name="assign_staffid[]"]').last().change();
}
</script>
<div class="form-horizontal col-sm-12" data-tab-name="staff">
	<h3>Staff Collaboration</h3>
	<?php $staff_assigned = array_filter(explode(',',$estimate['assign_staffid']));
	if(count($staff_assigned) == 0) {
		$staff_assigned[] = 0;
	}
	foreach($staff_assigned as $staff) { ?>
		<div class="form-group">
			<label class="col-sm-3">Staff:</label>
			<div class="col-sm-8">
				<select name="assign_staffid[]" class="chosen-select-deselect" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>">
					<option></option><?php
					foreach($staff_list as $staff_id) { ?>
						<option <?= $staff_id['contactid'] == $staff ? 'selected' : '' ?> value="<?= $staff_id['contactid'] ?>"><?= $staff_id['first_name'].' '.$staff_id['last_name'] ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-sm-1">
				<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addStaff(this);">
				<img src="../img/remove.png" class="inline-img pull-right" onclick="removeStaff(this);">
			</div>
		</div>
	<?php } ?>
	<?php foreach(get_teams($dbc, " AND IF(`end_date` = '0000-00-00','9999-12-31',`end_date`) >= '".date('Y-m-d')."'") as $team) {
		$team_staff = get_team_contactids($dbc, $team['teamid']);
		if(count($team_staff) > 1) { ?>
			<div class="form-group">
				<button class="btn brand-btn pull-right" data-group='<?= json_encode($team_staff) ?>' onclick="assignGroup(this);">Assign <?= get_team_name($dbc, $team['teamid']).(!empty($team['team_name']) ? ': '.get_team_name($dbc, $team['teamid'], ', ', 1) : '') ?></button>
			</div>
		<?php }
	} ?>
    <hr />
</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit_staff.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>