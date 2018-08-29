<?php error_reporting(0);
include_once('../include.php');

if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
if(!isset($projectid)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	$projecttype = filter_var($_GET['projecttype'],FILTER_SANITIZE_STRING);
	foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
		if($tile == 'project' || $tile == config_safe_str($type_name)) {
			$project_tabs[config_safe_str($type_name)] = $type_name;
		}
	}
	$value_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype'"))[0]),explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL'"))[0]))));
}
$lead_cats = explode(',',get_config($dbc, 'project_lead_cats'));
$co_cats = explode(',',get_config($dbc, 'project_co_lead_cats'));
$team_cats = explode(',',get_config($dbc, 'project_team_cats'));
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid' AND '$projectid' > 0")); ?>
<input type="hidden" name="projectid" value="<?= $projectid ?>">
<div id="head_staff">
	<h3><?= PROJECT_NOUN ?> Staff</h3>
	<?php if (in_array("Information Assign",$value_config)) { ?>
		<div class="form-group">
			<label class="col-sm-4">Project Lead:</label>
			<div class="col-sm-6 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
				<select name="project_lead" data-placeholder="Select Staff..." data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" class="chosen-select-deselect form-control">
					<option></option>
					<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE (category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." ".(empty($lead_cats) ? '' : "AND (`category` IN ('".implode("','",$lead_cats)."') OR `staff_category` IN ('".implode("','",$lead_cats)."'))")." AND deleted=0 AND `status` > 0) OR `contactid`='{$project['project_lead']}'")) as $contact) {
						echo "<option ".($project['project_lead'] == $contact['contactid'] ? 'selected' : '')." value='". $contact['contactid']."' data-region='".$contact['region']."' data-location='".$contact['con_locations']."' data-classification='".$contact['classification']."'>".$contact['first_name'].' '.$contact['last_name'].'</option>';
					} ?>
				</select>
			</div>
            <div class="col-sm-2">
                <img class="inline-img pull-right no-toggle cursor-hand current" src="../img/icons/eyeball.png" title="View this <?= CONTACTS_NOUN ?>'s scheduled reminders" onclick="viewReminders(this);" style="<?= $clientid > 0 ? '' : 'display:none;' ?>">
                <img class="inline-img pull-right no-toggle cursor-hand current" src="../img/icons/ROOK-reminder-icon.png" title="Schedule a reminder for this <?= CONTACTS_NOUN ?>" onclick="addReminder(this);" style="<?= $clientid > 0 ? '' : 'display:none;' ?>">
                <img class="inline-img pull-right no-toggle cursor-hand current" src="../img/person.PNG" title="View this <?= CONTACTS_NOUN ?>'s profile" onclick="viewProfile(this, '%');" style="<?= $clientid > 0 ? '' : 'display:none;' ?>">
            </div>
		</div>
	<?php } ?>

	<?php if (in_array("Information Colead",$value_config)) { ?>
		<div class="form-group">
			<label class="col-sm-4">Project Co-Lead:</label>
			<div class="col-sm-6 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
				<select name="project_colead" data-placeholder="Select Staff..." data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" class="chosen-select-deselect form-control">
					<option></option>
					<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE (category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." ".(empty($co_cats) ? '' : "AND (`category` IN ('".implode("','",$co_cats)."') OR `staff_category` IN ('".implode("','",$co_cats)."'))")." AND deleted=0 AND `status` > 0) OR `contactid`='{$project['project_colead']}'")) as $contact) {
						echo "<option ".($project['project_colead'] == $contact['contactid'] ? 'selected' : '')." value='". $contact['contactid']."' data-region='".$contact['region']."' data-location='".$contact['con_locations']."' data-classification='".$contact['classification']."'>".$contact['first_name'].' '.$contact['last_name'].'</option>';
					} ?>
				</select>
			</div>
            <div class="col-sm-2">
                <img class="inline-img pull-right no-toggle cursor-hand current" src="../img/icons/eyeball.png" title="View this <?= CONTACTS_NOUN ?>'s scheduled reminders" onclick="viewReminders(this);" style="<?= $clientid > 0 ? '' : 'display:none;' ?>">
                <img class="inline-img pull-right no-toggle cursor-hand current" src="../img/icons/ROOK-reminder-icon.png" title="Schedule a reminder for this <?= CONTACTS_NOUN ?>" onclick="addReminder(this);" style="<?= $clientid > 0 ? '' : 'display:none;' ?>">
                <img class="inline-img pull-right no-toggle cursor-hand current" src="../img/person.PNG" title="View this <?= CONTACTS_NOUN ?>'s profile" onclick="viewProfile(this, '%');" style="<?= $clientid > 0 ? '' : 'display:none;' ?>">
            </div>
		</div>
	<?php } ?>

	<?php if (in_array("Information Team",$value_config)) { ?>
        <?php foreach(explode(',',$project['project_team']) as $project_team) { ?>
            <div class="form-group">
                <label class="col-sm-4">Project Team:</label>
                <div class="col-sm-6 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
                    <select name="project_team[]" data-placeholder="Select Staff..." data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" class="chosen-select-deselect form-control">
                        <option></option>
                        <?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE (category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." ".(empty($team_cats) ? '' : "AND (`category` IN ('".implode("','",$team_cats)."') OR `staff_category` IN ('".implode("','",$team_cats)."'))")." AND deleted=0 AND `status` > 0) OR `contactid`='$project_team'")) as $contact) {
                            echo "<option ".($project_team == $contact['contactid'] ? 'selected' : '')." value='". $contact['contactid']."' data-region='".$contact['region']."' data-location='".$contact['con_locations']."' data-classification='".$contact['classification']."'>".$contact['first_name'].' '.$contact['last_name'].'</option>';
                        } ?>
                    </select>
				</div>
				<div class="col-sm-2">
                <img class="inline-img pull-right no-toggle cursor-hand current" src="../img/icons/eyeball.png" title="View this <?= CONTACTS_NOUN ?>'s scheduled reminders" onclick="viewReminders(this);" style="<?= $clientid > 0 ? '' : 'display:none;' ?>">
                <img class="inline-img pull-right no-toggle cursor-hand current" src="../img/icons/ROOK-reminder-icon.png" title="Schedule a reminder for this <?= CONTACTS_NOUN ?>" onclick="addReminder(this);" style="<?= $clientid > 0 ? '' : 'display:none;' ?>">
					<img class="inline-img pull-right no-toggle cursor-hand current" src="../img/person.PNG" title="View this <?= CONTACTS_NOUN ?>'s profile" onclick="viewProfile(this, '%');" style="<?= $clientid > 0 ? '' : 'display:none;' ?>">
					<?php if($security['edit'] > 0) { ?>
						<img class="inline-img pull-right no-toggle cursor-hand" src="../img/remove.png" title="Remove this <?= CONTACTS_NOUN ?> from the team" onclick="removeTeam(this);">
						<img class="inline-img pull-right no-toggle cursor-hand current" src="../img/icons/ROOK-add-icon.png" title="Select an additional <?= CONTACTS_NOUN ?> on the Team for this <?= PROJECT_NOUN ?>" onclick="addTeam();" style="<?= $clientid > 0 ? '' : 'display:none;' ?>">
					<?php } ?>
                </div>
            </div>
        <?php } ?>
		<script>
		function addTeam() {
			var last = $('[name="project_team[]"]').last().closest('.form-group');
			var clone = last.clone();
			clone.find('select').val('');
			resetChosen(clone.find('.chosen-select-deselect'));
			last.after(clone);
			$('[data-table]').change(saveField);
		}
		function removeTeam(img) {
			if($('[name="project_team[]"]').length <= 1) {
				addClient();
			}
			$(img).closest('.form-group').remove();
			$('[name="project_team[]"]').last().change();
		}
		</script>
	<?php } ?>
</div>