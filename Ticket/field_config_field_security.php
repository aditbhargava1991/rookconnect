<?php error_reporting(0);
include_once('../include.php');

$project_information = ['PI Business','PI Name','PI Project','PI Pieces','PI Sites','PI AFE','PI Rate Card','PI Customer Order','PI Sales Order','PI Order','PI Purchase Order','PI WTS Order','PI Status'];
$project_details = ['Detail Business','Detail Project','Detail Contact','Detail Rate Card','Detail Heading','Detail Date',' Detail Staff'];
$path_milestone = ['Path & Milestone'];
$fees = ['Fees'];
$checkin = ['Checkin Hide All Button','Checkin Staff','Checkin Staff_Tasks','Checkin Delivery','Checkin Clients','Checkin Members','Checkin material'];
$services = ['Service Category','Service Type','Service Heading','Details Heading','Service Description'];
$checklist = ['Checklist Items'];
$timer = ['Time Tracking Estimate Complete','Time Tracking Estimate QA','Time Tracking Time Allotted','Time Tracking Current Time','Time Tracking Timer','Time Tracking Timer Manual'];
$docs = ['Documents Docs','Documents Links'];
$deliverable = ['Deliverable Status','Deliverable To Do','Deliverable Internal','Deliverable Customer'];
$custom_notes = ['Custom Notes'];
$notes = ['Notes'];

$folder = FOLDER_NAME;
if(!empty($_POST['folder'])) {
	$folder = $_POST['folder'];
}
$current_type = $_GET['type'];
$security_level = $_GET['security_level'];
if(!empty($_POST['type'])) {
	$contact_type_arr = explode('*#*', $_POST['type']);
	$current_type = $contact_type_arr[0];
	$security_level = $contact_type_arr[1];
}
$field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT `tickets` FROM `field_config`"));
$security_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_security` WHERE `security_level`='$security_level'"));
$subtabs_hidden = explode(',', $security_config['subtabs_hidden']);
$subtabs_viewonly = explode(',', $security_config['subtabs_viewonly']);
$fields_hidden = explode(',', $security_config['fields_hidden']);
$fields_viewonly = explode(',', $security_config['fields_viewonly']);
?>
<script>
function save_options(input) {
	if($(input).hasClass('contact_subtabs')) {
		if($(input).data('option') != 'disable') {
			$(input).closest('.subtab_block').find('.subtab_fields').show();
		} else {
			$(input).closest('.subtab_block').find('.subtab_fields').hide();
		}
	}
	security_level = $('[name="security_level"]').val();
	subtabs_hidden = $('.contact_subtabs').map(function() {
		if($(this).is(':checked') && $(this).data('option') == 'disable') {
			return this.value;
		}
	}).get();
	subtabs_viewonly = $('.contact_subtabs').map(function() {
		if($(this).is(':checked') && $(this).data('option') == 'viewonly') {
			return this.value;
		}
	}).get();
	fields_hidden = $('.contact_fields').map(function() {
		if($(this).is(':checked') && $(this).data('option') == 'disable') {
			return this.value;
		}
	}).get();
	fields_viewonly = $('.contact_fields').map(function() {
		if($(this).is(':checked') && $(this).data('option') == 'viewonly') {
			return this.value;
		}
	}).get();

	$.ajax({
		url: 'ticket_ajax_all.php?fill=ticket_security_settings',
		type: 'POST',
		data: {security_level: security_level, subtabs_hidden: subtabs_hidden, subtabs_viewonly: subtabs_viewonly, fields_hidden: fields_hidden, fields_viewonly: fields_viewonly },
		success: function(response) {
		}
	});
}
function change_type() {
	security_level = $('[name="security_level"]').val();
	<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_field_security.php') { ?>
		contact_type = $('[name="contact_type"]').val()+'*#*'+$('[name="security_level"]').val();
		loadPanel();
	<?php } else { ?>
		window.location.href = 'index.php?settings=field_security&security_level='+security_level+(tile_group == '' ? '' : '&tile_group='+tile_group)+(tile_name == '' ? '' : '&tile_name='+tile_name);
	<?php } ?>
}
$(document).on('change', 'select[name="security_level"]', function() { change_type(); });
</script>
<div class="standard-dashboard-body-title">
    <h3>Settings - Security:</h3>
</div>
<div class="standard-dashboard-body-content full-height">
    <div class="dashboard-item dashboard-item2 full-height">
        <form class="form-horizontal">
        <div class="form-group block-group block-group-noborder">

            <div class="form-group">
                <label class="col-sm-4 control-label">Security Level:</label>
                <div class="col-sm-8">
                    <select name="security_level" data-placeholder="Select a Security Level" class="chosen-select-deselect">
                        <?php $on_security = get_security_levels($dbc);
                        foreach($on_security as $security_name => $value) {
                            if($security_level == '') {
                                $security_level = $value;
                            }
                            echo "<option ".($security_level == $value ? 'selected' : '')." value='$value'>$security_name</option>";
                        } ?>
                    </select>
                </div>
            </div>

                            <div class="form-group subtab_block">
                                <label class="col-sm-4 control-label">Project Information:</label>
                                <div class="col-sm-8">
                                    <label><input type="radio" name="subtab_project_information" data-option="edit" value="project_information" <?= (!in_array('project_information', $subtabs_hidden) && !in_array('project_information', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Editable</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_project_information" data-option="disable" value="project_information" <?= (in_array('project_information', $subtabs_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Disabled</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_project_information" data-option="viewonly" value="project_information" <?= (in_array('project_information', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">View Only</label>&nbsp;&nbsp;
                                    <div class="clearfix"></div>
                                    <div class="block-group subtab_fields" <?= (!in_array('project_information', $subtabs_hidden) ? '' : 'style="display:none;"') ?>>

                                        <?php
                                        foreach($project_information as $field_option) {
                                                    if(($field_option != '') && (strpos($field_config['tickets'], $field_option) !== FALSE)) {
                                            ?>
                                                <div class="col-sm-3"><?= $field_option ?></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="edit" value="<?= $field_option ?>" <?= (!in_array($field_option, $fields_hidden) && !in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Editable</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="disable" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Disabled</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="viewonly" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">View Only</label></div>
                                                <div class="clearfix"></div>
                                            <?php
                                                }
                                        } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group subtab_block">
                                <label class="col-sm-4 control-label">Project Details:</label>
                                <div class="col-sm-8">
                                    <label><input type="radio" name="subtab_project_details" data-option="edit" value="project_details" <?= (!in_array('project_details', $subtabs_hidden) && !in_array('project_details', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Editable</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_project_details" data-option="disable" value="project_details" <?= (in_array('project_details', $subtabs_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Disabled</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_project_details" data-option="viewonly" value="project_details" <?= (in_array('project_details', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">View Only</label>&nbsp;&nbsp;
                                    <div class="clearfix"></div>
                                    <div class="block-group subtab_fields" <?= (!in_array('project_details', $subtabs_hidden) ? '' : 'style="display:none;"') ?>>

                                        <?php
                                        foreach($project_details as $field_option) {
                                                    if(($field_option != '') && (strpos($field_config['tickets'], $field_option) !== FALSE)) {
                                            ?>
                                                <div class="col-sm-3"><?= $field_option ?></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="edit" value="<?= $field_option ?>" <?= (!in_array($field_option, $fields_hidden) && !in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Editable</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="disable" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Disabled</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="viewonly" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">View Only</label></div>
                                                <div class="clearfix"></div>
                                            <?php
                                                }
                                        } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group subtab_block">
                                <label class="col-sm-4 control-label">Path & Milestone:</label>
                                <div class="col-sm-8">
                                    <label><input type="radio" name="subtab_path_milestone" data-option="edit" value="path_milestone" <?= (!in_array('path_milestone', $subtabs_hidden) && !in_array('path_milestone', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Editable</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_path_milestone" data-option="disable" value="path_milestone" <?= (in_array('path_milestone', $subtabs_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Disabled</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_path_milestone" data-option="viewonly" value="path_milestone" <?= (in_array('path_milestone', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">View Only</label>&nbsp;&nbsp;
                                    <div class="clearfix"></div>
                                    <div class="block-group subtab_fields" <?= (!in_array('path_milestone', $subtabs_hidden) ? '' : 'style="display:none;"') ?>>

                                        <?php
                                        foreach($path_milestone as $field_option) {
                                                    if(($field_option != '') && (strpos($field_config['tickets'], $field_option) !== FALSE)) {
                                            ?>
                                                <div class="col-sm-3"><?= $field_option ?></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="edit" value="<?= $field_option ?>" <?= (!in_array($field_option, $fields_hidden) && !in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Editable</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="disable" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Disabled</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="viewonly" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">View Only</label></div>
                                                <div class="clearfix"></div>
                                            <?php
                                                }
                                        } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group subtab_block">
                                <label class="col-sm-4 control-label">Check In::</label>
                                <div class="col-sm-8">
                                    <label><input type="radio" name="subtab_checkin" data-option="edit" value="checkin" <?= (!in_array('checkin', $subtabs_hidden) && !in_array('checkin', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Editable</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_checkin" data-option="disable" value="checkin" <?= (in_array('checkin', $subtabs_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Disabled</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_checkin" data-option="viewonly" value="checkin" <?= (in_array('checkin', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">View Only</label>&nbsp;&nbsp;
                                    <div class="clearfix"></div>
                                    <div class="block-group subtab_fields" <?= (!in_array('checkin', $subtabs_hidden) ? '' : 'style="display:none;"') ?>>

                                        <?php
                                        foreach($checkin as $field_option) {
                                                    if(($field_option != '') && (strpos($field_config['tickets'], $field_option) !== FALSE)) {
                                            ?>
                                                <div class="col-sm-3"><?= $field_option ?></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="edit" value="<?= $field_option ?>" <?= (!in_array($field_option, $fields_hidden) && !in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Editable</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="disable" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Disabled</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="viewonly" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">View Only</label></div>
                                                <div class="clearfix"></div>
                                            <?php
                                                }
                                        } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group subtab_block">
                                <label class="col-sm-4 control-label">Services:</label>
                                <div class="col-sm-8">
                                    <label><input type="radio" name="subtab_services" data-option="edit" value="services" <?= (!in_array('services', $subtabs_hidden) && !in_array('services', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Editable</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_services" data-option="disable" value="services" <?= (in_array('services', $subtabs_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Disabled</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_services" data-option="viewonly" value="services" <?= (in_array('services', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">View Only</label>&nbsp;&nbsp;
                                    <div class="clearfix"></div>
                                    <div class="block-group subtab_fields" <?= (!in_array('services', $subtabs_hidden) ? '' : 'style="display:none;"') ?>>

                                        <?php
                                        foreach($services as $field_option) {
                                                    if(($field_option != '') && (strpos($field_config['tickets'], $field_option) !== FALSE)) {
                                            ?>
                                                <div class="col-sm-3"><?= $field_option ?></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="edit" value="<?= $field_option ?>" <?= (!in_array($field_option, $fields_hidden) && !in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Editable</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="disable" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Disabled</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="viewonly" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">View Only</label></div>
                                                <div class="clearfix"></div>
                                            <?php
                                                }
                                        } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group subtab_block">
                                <label class="col-sm-4 control-label">Checklist:</label>
                                <div class="col-sm-8">
                                    <label><input type="radio" name="subtab_checklist" data-option="edit" value="checklist" <?= (!in_array('checklist', $subtabs_hidden) && !in_array('checklist', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Editable</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_checklist" data-option="disable" value="checklist" <?= (in_array('checklist', $subtabs_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Disabled</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_checklist" data-option="viewonly" value="checklist" <?= (in_array('checklist', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">View Only</label>&nbsp;&nbsp;
                                    <div class="clearfix"></div>
                                    <div class="block-group subtab_fields" <?= (!in_array('checklist', $subtabs_hidden) ? '' : 'style="display:none;"') ?>>

                                        <?php
                                        foreach($checklist as $field_option) {
                                                    if(($field_option != '') && (strpos($field_config['tickets'], $field_option) !== FALSE)) {
                                            ?>
                                                <div class="col-sm-3"><?= $field_option ?></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="edit" value="<?= $field_option ?>" <?= (!in_array($field_option, $fields_hidden) && !in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Editable</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="disable" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Disabled</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="viewonly" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">View Only</label></div>
                                                <div class="clearfix"></div>
                                            <?php
                                                }
                                        } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group subtab_block">
                                <label class="col-sm-4 control-label">Timer:</label>
                                <div class="col-sm-8">
                                    <label><input type="radio" name="subtab_timer" data-option="edit" value="timer" <?= (!in_array('timer', $subtabs_hidden) && !in_array('timer', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Editable</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_timer" data-option="disable" value="timer" <?= (in_array('timer', $subtabs_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Disabled</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_timer" data-option="viewonly" value="timer" <?= (in_array('timer', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">View Only</label>&nbsp;&nbsp;
                                    <div class="clearfix"></div>
                                    <div class="block-group subtab_fields" <?= (!in_array('timer', $subtabs_hidden) ? '' : 'style="display:none;"') ?>>

                                        <?php
                                        foreach($timer as $field_option) {
                                                    if(($field_option != '') && (strpos($field_config['tickets'], $field_option) !== FALSE)) {
                                            ?>
                                                <div class="col-sm-3"><?= $field_option ?></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="edit" value="<?= $field_option ?>" <?= (!in_array($field_option, $fields_hidden) && !in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Editable</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="disable" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Disabled</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="viewonly" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">View Only</label></div>
                                                <div class="clearfix"></div>
                                            <?php
                                                }
                                        } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group subtab_block">
                                <label class="col-sm-4 control-label">Docs:</label>
                                <div class="col-sm-8">
                                    <label><input type="radio" name="subtab_docs" data-option="edit" value="docs" <?= (!in_array('docs', $subtabs_hidden) && !in_array('docs', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Editable</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_docs" data-option="disable" value="docs" <?= (in_array('docs', $subtabs_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Disabled</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_docs" data-option="viewonly" value="docs" <?= (in_array('docs', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">View Only</label>&nbsp;&nbsp;
                                    <div class="clearfix"></div>
                                    <div class="block-group subtab_fields" <?= (!in_array('docs', $subtabs_hidden) ? '' : 'style="display:none;"') ?>>

                                        <?php
                                        foreach($docs as $field_option) {
                                                    if(($field_option != '') && (strpos($field_config['tickets'], $field_option) !== FALSE)) {
                                            ?>
                                                <div class="col-sm-3"><?= $field_option ?></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="edit" value="<?= $field_option ?>" <?= (!in_array($field_option, $fields_hidden) && !in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Editable</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="disable" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Disabled</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="viewonly" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">View Only</label></div>
                                                <div class="clearfix"></div>
                                            <?php
                                                }
                                        } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group subtab_block">
                                <label class="col-sm-4 control-label">Deliverable:</label>
                                <div class="col-sm-8">
                                    <label><input type="radio" name="subtab_deliverable" data-option="edit" value="deliverable" <?= (!in_array('deliverable', $subtabs_hidden) && !in_array('deliverable', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Editable</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_deliverable" data-option="disable" value="deliverable" <?= (in_array('deliverable', $subtabs_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Disabled</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_deliverable" data-option="viewonly" value="deliverable" <?= (in_array('deliverable', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">View Only</label>&nbsp;&nbsp;
                                    <div class="clearfix"></div>
                                    <div class="block-group subtab_fields" <?= (!in_array('deliverable', $subtabs_hidden) ? '' : 'style="display:none;"') ?>>

                                        <?php
                                        foreach($deliverable as $field_option) {
                                                    if(($field_option != '') && (strpos($field_config['tickets'], $field_option) !== FALSE)) {
                                            ?>
                                                <div class="col-sm-3"><?= $field_option ?></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="edit" value="<?= $field_option ?>" <?= (!in_array($field_option, $fields_hidden) && !in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Editable</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="disable" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Disabled</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="viewonly" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">View Only</label></div>
                                                <div class="clearfix"></div>
                                            <?php
                                                }
                                        } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group subtab_block">
                                <label class="col-sm-4 control-label">Notes:</label>
                                <div class="col-sm-8">
                                    <label><input type="radio" name="subtab_notes" data-option="edit" value="notes" <?= (!in_array('notes', $subtabs_hidden) && !in_array('notes', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Editable</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_notes" data-option="disable" value="notes" <?= (in_array('notes', $subtabs_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">Disabled</label>&nbsp;&nbsp;
                                    <label><input type="radio" name="subtab_notes" data-option="viewonly" value="notes" <?= (in_array('notes', $subtabs_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_subtabs">View Only</label>&nbsp;&nbsp;
                                    <div class="clearfix"></div>
                                    <div class="block-group subtab_fields" <?= (!in_array('notes', $subtabs_hidden) ? '' : 'style="display:none;"') ?>>

                                        <?php
                                        foreach($notes as $field_option) {
                                                    if(($field_option != '') && (strpos($field_config['tickets'], $field_option) !== FALSE)) {
                                            ?>
                                                <div class="col-sm-3"><?= $field_option ?></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="edit" value="<?= $field_option ?>" <?= (!in_array($field_option, $fields_hidden) && !in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Editable</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="disable" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_hidden) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">Disabled</label></div>
                                                <div class="col-sm-3"><label class="form-checkbox"><input type="radio" name="field_<?= $field_option ?>" data-option="viewonly" value="<?= $field_option ?>" <?= (in_array($field_option, $fields_viewonly) ? 'checked' : '') ?> onchange="save_options(this);" class="contact_fields">View Only</label></div>
                                                <div class="clearfix"></div>
                                            <?php
                                                }
                                        } ?>
                                    </div>
                                </div>
                                </div>


        </div>
        </form>
    </div><!-- .dashboard-item -->
</div><!-- .standard-dashboard-body-content -->
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_additions.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>