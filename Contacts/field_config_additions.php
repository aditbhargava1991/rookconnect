<?php error_reporting(0);
include_once('../include.php');
$folder = FOLDER_NAME;
if(!empty($_POST['folder'])) {
	$folder = $_POST['folder'];
}
$current_type = $_GET['type'];
if(!empty($_POST['type'])) {
	$current_type = $_POST['type'];
}
$admin_access = tile_visible($dbc, 'security'); ?>
<script>
function change_type(type_name) {
	<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_additions.php') { ?>
		contact_type = type_name;
		loadPanel();
	<?php } else { ?>
		window.location.href = 'contacts_inbox.php?settings=additions&type='+type_name;
	<?php } ?>
}
function setTileEnabled(checkbox) {
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../ajax_all.php?fill=tile_config&name="+checkbox.value+"&value="+(checkbox.checked ? 'turn_on' : 'turn_off')+"&turnoff="+(checkbox.checked ? '' : 'off')+"&turnOn="+(checkbox.checked ? 'on' : '')+"&contactid=<?= $_SESSION['contactid'] ?>",
		dataType: "html",   //expect html to be returned
		success: function(response){
			response = response.split('#*#');
			console.log(response[0]);
			$(sel).closest('tr').find('td[data-title=Status]').html(response[1]);
			if(response[2] == '1') {
				$(sel).closest('tr').find('input[value=turn_on]').attr('checked','checked');
			} else {
				$(sel).closest('tr').find('input[value=turn_off]').attr('checked','checked');
			}
		}
	});
}
function save_options() {
	var field_list = '';
	//$('[name="accordion_option[]"]:checked').each(function() {
	//	field_list += this.value + ',';
	//});
	$('[name="contact_field[]"]:checked').each(function() {
		field_list += this.value + ',';
	});
	$.ajax({
		url: '../Contacts/contacts_ajax.php?action=contact_additions',
		method: 'POST',
		data: { category: $('[name=contact_type]').val(), field_list: field_list, tile: '<?= FOLDER_NAME ?>' },
		response: 'html',
		success: function(response) {
			console.log(response);
		}
	});
}
$(document).ready(function() {
	$('input[type=checkbox]:checked').closest('.form-group').find('[name="accordion_option[]"]').prop('checked','checked');
	$('input[type=checkbox]:checked').closest('.block-group').show();
});
$(document).on('change', 'select[name="contact_type"]', function() { change_type(this.value); });
</script>
<div class="standard-dashboard-body-title">
    <h3>Settings - Fields:</h3>
</div>
<div class="standard-dashboard-body-content">
    <div class="dashboard-item dashboard-item2">
        <form class="form-horizontal">
        <div class="form-group block-group block-group-noborder">
            <div class="form-group">
                <label class="col-sm-4 control-label">Contact Type:</label>
                <div class="col-sm-8">
                    <select name="contact_type" data-placeholder="Select a Contact Type" class="chosen-select-deselect">
                        <?php $contact_types = explode(',', get_config($dbc, $folder."_tabs"));
                        $staff = array_search('Staff',$contact_types);
                        if($staff !== FALSE) {
                            unset($contact_types[$staff]);
                        }
                        foreach($contact_types as $type_name) {
                            if($current_type == '') {
                                $current_type = $type_name;
                            }
                            echo "<option ".($current_type == $type_name ? 'selected' : '')." value='$type_name'>$type_name</option>";
                        }
                        $field_config = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tile_name`='".FOLDER_NAME."' AND `tab`='$current_type' AND `subtab`='additions' GROUP BY `contacts`"))[0]);
                        if(in_array($current_type,['Sales Lead','Sales Leads'])) {
                            $field_config[] = 'Sales Lead';
                        } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Profile Additions:</label>
                <div class="col-sm-8">
                    <div class="hide-titles-mob">
                        <div class="col-sm-4">Tile Name</div>
                        <div class="col-sm-4">Enable in Profile</div>
                        <div class="col-sm-4">Enabled in Software</div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="block-group">
                        <?php $tile_enabled = tile_enabled($dbc, 'check_out'); ?>
                        <div class="col-sm-4">Account Statement (from Check Out)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Account Statement" name="contact_field[]" <?= in_array('Account Statement', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="check_out" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'posadvanced'); ?>
                        <div class="col-sm-4">Account Statement (from Point of Sale)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Account Statement" name="contact_field[]" <?= in_array('Account Statement', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="posadvanced" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'individual_support_plan'); ?>
                        <div class="col-sm-4">Individual Service Plan (ISP)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Client Support Plan" name="contact_field[]" <?= in_array('Client Support Plan', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="individual_support_plan" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'medication'); ?>
                        <div class="col-sm-4">Medication Details</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Medication Details" name="contact_field[]" <?= in_array('Medication Details', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="medication" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">MAR Sheet</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="MAR Sheet" name="contact_field[]" <?= in_array('MAR Sheet', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'charts'); ?>
                        <div class="col-sm-4">Charts</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Client Medical Charts" name="contact_field[]" <?= in_array('Client Medical Charts', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="charts" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">Bowel Movement Chart (from Charts)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Client Bowel Movement Chart" name="contact_field[]" <?= in_array('Client Bowel Movement Chart', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">Client Water Temp Chart (from Charts)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Water Temp Chart" name="contact_field[]" <?= in_array('Water Temp Chart', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"></div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">Blood Glucose Chart (from Charts)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Blood Glucose Chart" name="contact_field[]" <?= in_array('Blood Glucose Chart', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"></div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">Seizure Record Chart (from Charts)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Client Seizure Record Chart" name="contact_field[]" <?= in_array('Client Seizure Record Chart', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">Program Water Temp Chart (from Charts)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Business Water Temp Chart" name="contact_field[]" <?= in_array('Business Water Temp Chart', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">Daily Fridge Temp (from Charts)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Daily Fridge Temp" name="contact_field[]" <?= in_array('Daily Fridge Temp', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">Daily Freezer Temp (from Charts)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Daily Freezer Temp" name="contact_field[]" <?= in_array('Daily Freezer Temp', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">Daily Dishwasher Temp (from Charts)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Daily Dishwasher Temp" name="contact_field[]" <?= in_array('Daily Dishwasher Temp', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'social_story'); ?>
                        <div class="col-sm-4">Activities (from Social Story)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Client Activities Social Story" name="contact_field[]" <?= in_array('Client Activities Social Story', $field_config) ? 'checked' : '' ?> <?= $tile_enabled['user_enabled'] == 0 ? '' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="social_story" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'social_story'); ?>
                        <div class="col-sm-4">Communication (from Social Story)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Client Communication Social Story" name="contact_field[]" <?= in_array('Client Communication Social Story', $field_config) ? 'checked' : '' ?> <?= $tile_enabled['user_enabled'] == 0 ? '' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="social_story" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'social_story'); ?>
                        <div class="col-sm-4">Routines (from Social Story)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Client Routines Social Story" name="contact_field[]" <?= in_array('Client Routines Social Story', $field_config) ? 'checked' : '' ?> <?= $tile_enabled['user_enabled'] == 0 ? '' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="social_story" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'social_story'); ?>
                        <div class="col-sm-4">Protocols (from Social Story)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Client Protocols Social Story" name="contact_field[]" <?= in_array('Client Protocols Social Story', $field_config) ? 'checked' : '' ?> <?= $tile_enabled['user_enabled'] == 0 ? '' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="social_story" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'social_story'); ?>
                        <div class="col-sm-4">Key Methodologies (from Social Story)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Client Key Methodologies Social Story" name="contact_field[]" <?= in_array('Client Key Methodologies Social Story', $field_config) ? 'checked' : '' ?> <?= $tile_enabled['user_enabled'] == 0 ? '' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="social_story" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'social_story'); ?>
                        <div class="col-sm-4">Key Methodologies as Member Support (from Social Story)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Client Key Methodologies Social Story Member Support" name="contact_field[]" <?= in_array('Client Key Methodologies Social Story Member Support', $field_config) ? 'checked' : '' ?> <?= $tile_enabled['user_enabled'] == 0 ? '' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="social_story" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'daily_log_notes'); ?>
                        <div class="col-sm-4">Daily Log Notes</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Client Daily Log Notes" name="contact_field[]" <?= in_array('Client Daily Log Notes', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="daily_log_notes" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'incident_report'); ?>
                        <div class="col-sm-4"><?= INC_REP_TILE ?></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Incident Reports" name="contact_field[]" <?= in_array('Incident Reports', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="incident_report" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'booking'); ?>
                        <div class="col-sm-4">Patient Block Booking (from Booking)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Patient Block Booking" name="contact_field[]" <?= in_array('Patient Block Booking', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="booking" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <!-- <?php $tile_enabled = tile_enabled($dbc, 'members'); ?>
                        <div class="col-sm-4">Medications (from Members)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Medical Details Medications" name="contact_field[]" <?= in_array('Medical Details Medications', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="members" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div> -->
                        <?php $tile_enabled = tile_enabled($dbc, 'checklist'); ?>
                        <div class="col-sm-4">Checklist</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Checklist" name="contact_field[]" <?= in_array('Checklist', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="checklist" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'project'); ?>
                        <div class="col-sm-4"><?= PROJECT_TILE ?></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Project Addition" name="contact_field[]" <?= in_array('Project Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="project" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'ticket'); ?>
                        <div class="col-sm-4"><?= TICKET_TILE ?></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Ticket Addition" name="contact_field[]" <?= in_array('Ticket Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="ticket" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'ticket'); ?>
                        <div class="col-sm-4"><?= TICKET_NOUN ?> Notes</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Ticket Notes Addition" name="contact_field[]" <?= in_array('Ticket Notes Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="ticket" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'tasks'); ?>
                        <div class="col-sm-4"><?= TASK_TILE ?></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Tasks Addition" name="contact_field[]" <?= in_array('Tasks Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="tasks" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'estimate'); ?>
                        <div class="col-sm-4"><?= ESTIMATE_TILE ?></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Estimates Addition" name="contact_field[]" <?= in_array('Estimates Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="estimate" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'sales_order'); ?>
                        <div class="col-sm-4"><?= SALES_ORDER_TILE ?></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Sales Order Addition" name="contact_field[]" <?= in_array('Sales Order Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="sales_order" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">Appointments Count (from Calendar)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Appointments Addition" name="contact_field[]" <?= in_array('Appointments Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">Upcoming Appointments (from Calendar)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Upcoming Appointments Addition" name="contact_field[]" <?= in_array('Upcoming Appointments Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'posadvanced'); ?>
                        <div class="col-sm-4">Point of Sale</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="POSAdvanced Addition" name="contact_field[]" <?= in_array('POSAdvanced Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="posadvanced" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'purchase_order'); ?>
                        <div class="col-sm-4">Purchase Orders</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Purchase Orders Addition" name="contact_field[]" <?= in_array('Purchase Orders Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="purchase_order" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'sales'); ?>
                        <div class="col-sm-4">Sales</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Sales Addition" name="contact_field[]" <?= in_array('Sales Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="sales" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'agenda_meeting'); ?>
                        <div class="col-sm-4">Agendas &amp; Meetings</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Agenda Meeting Addition" name="contact_field[]" <?= in_array('Agenda Meeting Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="agenda_meeting" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'expense'); ?>
                        <div class="col-sm-4">Expenses</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Expense Addition" name="contact_field[]" <?= in_array('Expense Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="expense" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'email_communication'); ?>
                        <div class="col-sm-4">Email Communication</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Email Communication Addition" name="contact_field[]" <?= in_array('Email Communication Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="email_communication" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'phone_communication'); ?>
                        <div class="col-sm-4">Phone Communication</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Phone Communication Addition" name="contact_field[]" <?= in_array('Phone Communication Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="phone_communication" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'hr'); ?>
                        <div class="col-sm-4">Forms</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Forms Addition" name="contact_field[]" <?= in_array('Forms Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="hr" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'manual'); ?>
                        <div class="col-sm-4">Manuals</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Manuals Addition" name="contact_field[]" <?= in_array('Manuals Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="manual" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'contracts'); ?>
                        <div class="col-sm-4">Contracts</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Contracts Addition" name="contact_field[]" <?= in_array('Contracts Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="contracts" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'client_documents'); ?>
                        <div class="col-sm-4">Client Documents</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Client Documents Addition" name="contact_field[]" <?= in_array('Client Documents Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="client_documents" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'staff_documents'); ?>
                        <div class="col-sm-4">Staff Documents</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Staff Documents Addition" name="contact_field[]" <?= in_array('Staff Documents Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="staff_documents" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'certificate'); ?>
                        <div class="col-sm-4">Certificates</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Certificates Addition" name="contact_field[]" <?= in_array('Certificates Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="certificate" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'service_queue'); ?>
                        <div class="col-sm-4">Service Queue</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Service Queue Addition" name="contact_field[]" <?= in_array('Service Queue Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="service_queue" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'injury'); ?>
                        <div class="col-sm-4">Injury</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Injury Addition" name="contact_field[]" <?= in_array('Injury Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="injury" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'exercise_library'); ?>
                        <div class="col-sm-4">Exercise Library</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Exercise Library Addition" name="contact_field[]" <?= in_array('Exercise Library Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="exercise_library" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'treatment_charts'); ?>
                        <div class="col-sm-4">Treatment Charts</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Treatment Charts Addition" name="contact_field[]" <?= in_array('Treatment Charts Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="treatment_charts" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'match'); ?>
                        <div class="col-sm-4">Match</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Match Addition" name="contact_field[]" <?= in_array('Match Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="match" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'rate_card'); ?>
                        <div class="col-sm-4">Customer Rate Card (from Rate Cards)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Customer Rate Card Addition" name="contact_field[]" <?= in_array('Customer Rate Card Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="rate_card" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'rate_card'); ?>
                        <div class="col-sm-4">Contact Service Rates (from Rate Cards)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Customer Rate Card Fields" name="contact_field[]" <?= in_array('Customer Rate Card Fields', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="rate_card" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'rate_card'); ?>
                        <div class="col-sm-4">Contact Service Rates with Totals (from Rate Cards)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Customer Rate Card Totalled" name="contact_field[]" <?= in_array('Customer Rate Card Totalled', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="rate_card" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'rate_card'); ?>
                        <div class="col-sm-4">Contact Service Rates with Totals (from Rate Cards, group by Category/Type)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Customer Rate Card Totalled Group Cat Type" name="contact_field[]" <?= in_array('Customer Rate Card Totalled Group Cat Type', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="rate_card" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'intake'); ?>
                        <div class="col-sm-4">Intake Forms</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Intake Forms Addition" name="contact_field[]" <?= in_array('Intake Forms Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="intake" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'confirmation'); ?>
                        <div class="col-sm-4">Ticket Notifications (From Notifications)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Ticket Notifications Addition" name="contact_field[]" <?= in_array('Ticket Notifications Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="confirmation" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'confirmation'); ?>
                        <div class="col-sm-4">Appointment Confirmations (From Notifications)</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Appointment Confirmations Addition" name="contact_field[]" <?= in_array('Appointment Confirmations Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="confirmation" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'vendors'); ?>
                        <div class="col-sm-4">Order Lists</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Order Lists Addition" name="contact_field[]" <?= in_array('Order Lists Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="vendors" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">Vendor Price Lists</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Vendor Price Lists Addition" name="contact_field[]" <?= in_array('Vendor Price Lists Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="vendors" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'purchase_order'); ?>
                        <div class="col-sm-4">Purchase Order #s</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="PO Addition" name="contact_field[]" <?= in_array('PO Addition', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="purchase_order" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'form_builder'); ?>
                        <div class="col-sm-4">Attached Contact Forms as Subtabs:</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Attached Contact Forms as Subtabs" name="contact_field[]" <?= in_array('Attached Contact Forms as Subtabs', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="form_builder" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
                        <?php $tile_enabled = tile_enabled($dbc, 'sales'); ?>
                        <div class="col-sm-4"><?= SALES_NOUN ?> Details:</div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="Sales Lead Details" name="contact_field[]" <?= in_array('Sales Lead Details', $field_config) ? 'checked' : '' ?> onchange="save_options();"> Enable <span class="show-on-mob"> in Profile</span></label></div>
                        <div class="col-sm-4"><label class="form-checkbox"><input type="checkbox" value="sales" <?= $tile_enabled['user_enabled'] == 1 ? 'checked' : '' ?> <?= $tile_enabled['admin_enabled'] == 0 || $admin_access == 0 ? 'disabled' : '' ?> onchange="setTileEnabled(this);"> <?= $tile_enabled['admin_enabled'] == 0 ? 'Contact Support to Enable This Tile' : 'Enable <span class="show-on-mob">d in Software</span>' ?></label></div>
                        <div class="clearfix"></div>
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