<?php if (isset($_POST['submit'])) {
	set_config($dbc, 'incident_report_summary_'.$_POST['security_level'], implode(',',$_POST['incident_report_summary']));
} ?>
<div class="form-group gap-top">
	<label for="tile_name" class="col-sm-4 control-label">Security Level / User:</label>
	<div class="col-sm-8">
		<select name="security_level" class="chosen-select-deselect" data-placeholder="Select Security Level or User" onchange="window.location.replace('?tab=summary&security='+this.value);">
			<option />
			<?php foreach(get_security_levels($dbc) as $sec_level_name => $sec_level) {
				$level_name = 'seclevel_'.config_safe_str($sec_level); ?>
				<option <?= $_GET['security'] == $level_name ? 'selected' : '' ?> value="<?= $level_name ?>"><?= $sec_level_name ?></option>
			<?php } ?>
			<?php foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `first_name`, `last_name`, `name` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND IFNULL(`user_name`,'') != ''")) as $user) { ?>
				<option <?= $_GET['security'] == 'userid_'.$user['contactid'] ? 'selected' : '' ?> value="userid_<?= $user['contactid'] ?>"><?= $user['full_name'] ?></option>
			<?php } ?>
		</select>
	</div>
</div>
<?php if(!empty($_GET['security'])) { ?>
	<div class="form-group">
		<label for="office_country" class="col-sm-4 control-label">Summary Options:</label>
		<div class="col-sm-8">
			<?php $summary = explode(',',get_config($dbc, 'incident_report_summary_'.$_GET['security'])); ?>
			<label class="form-checkbox"><input type="checkbox" <?= in_array('Types',$summary) ? 'checked' : '' ?> name="incident_report_summary[]" value="Types"> Summary by Type</label>
			<label class="form-checkbox"><input type="checkbox" <?= in_array('Complete',$summary) ? 'checked' : '' ?> name="incident_report_summary[]" value="Complete"> Summary by Completed</label>
			<label class="form-checkbox"><input type="checkbox" <?= in_array('Staff Only',$summary) ? 'checked' : '' ?> name="incident_report_summary[]" value="Staff Only"> Staff <?= INC_REP_TILE ?> Only</label>
		</div>
	</div>
<?php } ?>