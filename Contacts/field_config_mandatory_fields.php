<?php error_reporting(1);
include_once('../include.php');
$folder = FOLDER_NAME;
if(!empty($_POST['folder'])) {
	$folder = $_POST['folder'];
}
$current_type = $_GET['type'];
if(!empty($_POST['type'])) {
	$current_type = $_POST['type'];
} ?>
<script>
function change_type(type_name) {
	<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_fields.php') { ?>
		contact_type = type_name;
		loadPanel();
	<?php } else { ?>
		window.location.href = 'contacts_inbox.php?settings=fields&type='+type_name;
	<?php } ?>
}
function set_accordion(checkbox) {
	$(checkbox).closest('div').find('.block-group').toggle();
	$(checkbox).closest('div').find('input[type=checkbox]').prop('checked',checkbox.checked);
	save_options();
}
function set_sub_accordion(checkbox) {
	$(checkbox).closest('div').find('.sub-block-group').toggle();
	//$(checkbox).closest('div').find('input[type=checkbox]').prop('checked',checkbox.checked);
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
		url: '../Contacts/contacts_ajax.php?action=contact_mandatory_fields',
		method: 'POST',
		data: { category: $('[name=contact_type]').val(), field_list: field_list, tile: '<?= FOLDER_NAME ?>' },
		response: 'html',
		success: function(response) { }
	});
}
function save_property_types() {
    var property_types = $('[name="contact_property_types"]').val();
    var contact_type = $('[name="contact_type"]').val();
    $.ajax({
        url: '../Contacts/contacts_ajax.php?action=general_config',
        method: 'POST',
        data: { name: '<?= $folder ?>_'+contact_type+'_property_types', value: property_types },
        success: function(response) { }
    });
}
function save_allocated_hours_types() {
    var allocated_hours_types = $('[name="contact_allocated_hours_types"]').val();
    var contact_type = $('[name="contact_type"]').val();
    $.ajax({
        url: '../Contacts/contacts_ajax.php?action=general_config',
        method: 'POST',
        data: { name: '<?= $folder ?>_'+contact_type+'_allocated_hours_types', value: allocated_hours_types },
        success: function(response) { }
    });
}
function add_guardian_tab() {
    var clone = $('.guardian-tabs .form-group').last().clone();
    clone.find('input').val('');
    $('.guardian-tabs').append(clone);
    $('.guardian-tabs input').last().focus();
}
function rem_guardian_tab(link) {
    $(link).closest('.form-group').remove();
}
function save_guardian_tabs(tab) {
    var guardian_tab = tab.value;
	$.ajax({
		url: '../Contacts/contacts_ajax.php?action=save_guardian_tabs',
		method: 'POST',
		data: { tab: guardian_tab },
		response: 'html',
		success: function(response) {
			console.log(response);
		}
	});
    //var guardian_tabs = '<?php filter_var(implode('#*#',array_filter($_POST['guardian_tabs'])),FILTER_SANITIZE_STRING); ?>';
}
function save_notes_label(input) {
    var label = $(input).val();
    var contact_type = $('[name="contact_type"]').val();
    $.ajax({
        url: '../Contacts/contacts_ajax.php?action=general_config',
        method: 'POST',
        data: { name: 'contacts_notes_label_'+contact_type, value: label },
        success: function(response) { }
    });
}
$(document).ready(function() {
	$('input[type=checkbox]:checked').closest('.form-group .sort_group_blocks').each(function() {
		$(this).find('[name="contact_field[]"]').first().prop('checked','checked');
	});
	$('input[type=checkbox]:checked').closest('.block-group').show();
	$('.sortable_group').sortable({
		items: "label:not(.no-sort)",
		update: function( event, ui ) {
			save_options();
		}
	});
	$('.form-horizontal').sortable({
		items: ".sort_accordion_blocks",
		update: function( event, ui ) {
			save_options();
		}
	});
    $('.sort_accordion_blocks').each(function() {
        var content = $(this).find('.panel-body').text();
        content = content.trim();
        if(content == undefined || content == '') {
            $(this).remove();
        }
    });
});
$(document).on('change', 'select[name="contact_type"]', function() { change_type(this.value); });
</script>
<div class="standard-dashboard-body-title">
    <h3>Settings - Mandatory Fields:</h3>
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
                        $field_config = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tile_name`='".FOLDER_NAME."' AND `tab`='$current_type' AND `subtab`='**no_subtab**'"))[0]);
												$field_config_mandate = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tile_name`='".FOLDER_NAME."' AND `tab`='$current_type' AND `subtab`='**no_subtab**' AND `mandatory` = 1"))[0]); ?>

                    </select>
                </div>
            </div>

            <div id="contacts_fields" class="panel-group standard-body-content">
                <?php $tab_list_names = [];
                include('../Contacts/edit_fields.php');
                foreach($tab_list as $tab_field_list) {
                    $tab_list_names[] = 'acc_'.$tab_field_list[0];
                }
                $i = 0;
                foreach(array_unique(array_merge($field_config,$tab_list_names)) as $tab_name) {
                    $label = '';
                    foreach($tab_list as $tab_label => $tab_field_list) {
                        if(explode('acc_',$tab_name)[1] == $tab_field_list[0]) {
                            $label = $tab_label;
                            break;
                        }
                    }

                    if(!empty($label)) { ?>
                        <div class="panel panel-default sort_accordion_blocks">
                            <div class="panel-heading no_load">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
                                            <?= $label ?><span class="glyphicon glyphicon-plus"></span>
                                    </a>
                                </h4>
                            </div>

                            <div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <?php switch($tab_name) {
                                        case 'acc_contact_description': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Contact Description:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Business','Site','Ref Contact','Contact','Employee ID','Contact Image','Contact Prefix','Contact Filters','First Name','Last Name','Middle','Preferred Name','Name on Account','Name','Initials','Title','Credential','Home Phone','Office Phone','Cell Phone','Phone Carrier','Fax','Email Address','Second Email Address','Preferred Contact Method','Website','Position','Preferred Staff','Intake Form','Region','Location','Classification','LinkedIn','Facebook','Twitter','Google+','Instagram','Pinterest','YouTube','Blog','Profile Priority','Rating','Next Follow Up Date','Follow Up Staff','Status','Profile Documents','Upload Docs','History','Background Check','Start Date','Business Hours'])) as $field_option): ?>
																														<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_personal_information': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Personal Information:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Date of Birth','School','FSCD Number','Gender','Preferred Pronoun','Height','Weight','SIN','Client ID','Personal Email','Insurance AISH Entrance Date','AISH #','Health Care Number','Insurance Alberta Health Care','Assigned Staff','Strengths','Interests','Client Support Documents'])) as $field_option) { ?>
																														<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_contact_profile': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Member Profile:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Profile ID','Profile First Name','Profile Last Name','Profile Preferred Name','Profile Home Phone','Profile Office Phone','Profile Cell Phone','Profile Fax','Profile Email Address','Profile Intake Form','Profile Region','Profile Location','Profile Classification','Profile LinkedIn','Profile Facebook','Profile Twitter','Profile Google+','Profile Instagram','Profile Pinterest','Profile YouTube','Profile Blog','Profile Status','Profile Date of Birth','Profile School','Profile FSCD Number','Profile Gender','Profile Preferred Pronoun','Profile Height','Profile Weight','Profile SIN','Profile AISH #','Profile Health Care Number','Profile Insurance Alberta Health Care','Profile Assigned Staff','Profile Client Support Documents'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
	                                                        <?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_marketing': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Marketing:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Referred By','Referred By Name','Hear About','Contact Since','Date of Last Contact'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_memberships': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Memberships:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Membership Type','Membership Status','Membership Level','Membership Level Dropdown','Membership Since','Membership Renewal Date','Membership Reminder Email Date','Membership Reminder Email'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_programs': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Programs:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Program Business','Program Type','Program Status','Program Level','Program Since','Program Renewal Date','Program Reminder Email Date','Program Reminder Email'])) as $field_option) {
																														?>
																																<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																														<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_funding': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Funding:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Funding FSCD','Funding FSCD Worker Name','Funding FSCD File ID','Funding FSCD Renewal Date','Funding Support Documents','Funding PDD','PDD Key Contact','PDD Client ID','PDD Phone','PDD Fax','PDD Email','PDD AISH','Multiple PDD Contacts'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_notes': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Notes:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Comments','Comments Attachment','Description','Description Attachment','General Comments','General Comments Attachment','Notes','Notes Attachment','Service Notes'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                        <div class="form-group block-group clearfix">
                                                            <label class="col-sm-4 control-label">Different Notes Label:</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" name="contacts_notes_label_<?= config_safe_str($_current_type) ?>" value="<?= get_config($dbc, 'contacts_notes_label_'. config_safe_str($current_type)) ?>" class="form-control" onchange="save_notes_label(this);">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_address': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Address:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Address Sync To Site','Synced Site Hide Address','Address Default Sync On','Address Create Site','Full Address','Address','City Quadrant','City','County','Province','Country','Postal Code','Google Maps Address','Key Number','Door Code Number','Alarm Code Number'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_second_address': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Second Address:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Second Full Address','Second Address','Second City Quadrant','Second City','Second County','Second Province','Second Country','Second Postal Code','Second Google Maps Address'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_business_address': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Business Address:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Business Sync To Site','Business Create Site','Business Full Address','Business Address','Business City Quadrant','Business City','Business County','Business Province','Business Country','Business Postal Code','Business Google Maps Address','Business Website'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_mailing_address': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Mailing / Shipping Address:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Mailing Lock Address','Mailing Sync Address','Mailing Sync To Site','Mailing Create Site','Mailing Full Address','Ship To Address','Ship City Quadrant','Ship City','Ship County','Ship State','Ship Country','Ship Zip','Ship Google Maps Address'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_emergency_contacts': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Emergency Contacts:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Emergency Contact First Name','Emergency Contact Last Name','Emergency Contact Contact Number','Emergency Contact Relationship','Emergency Contact Work Phone','Emergency Contact Home Phone','Emergency Contact Cell Phone','Emergency Contact Fax','Emergency Contact Address','Emergency Contact Postal Code','Emergency Contact City','Emergency Contact County','Emergency Contact Province','Emergency Contact Country','Primary Emergency Contact First Name','Primary Emergency Contact Last Name','Primary Emergency Contact Relationship','Primary Emergency Contact Home Phone','Primary Emergency Contact Cell Phone','Primary Emergency Contact Email','Secondary Emergency Contact First Name','Secondary Emergency Contact Last Name','Secondary Emergency Contact Relationship','Secondary Emergency Contact Home Phone','Secondary Emergency Contact Cell Phone','Secondary Emergency Contact Email','Emergency Contact Multiple'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_emergency_support_docs': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Emergency Contact Support Documents:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Emergency Contact Support Documents'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_guardian_type': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Guardian Type:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Guardians Type','Guardians Family Guardian','Guardians Family Appointed Guardian','Guardians Public Guardian','Guardians Court Appointed Guardian'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_guardian_information': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Guardian Information:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Guardians Self','Guardians First Name','Guardians Last Name','Guardians Relationship','Guardians Work Phone','Guardians Home Phone','Guardians Cell Phone','Guardians Fax','Guardians Email Address','Guardians Address','Guardians Postal Code','Guardians City','Guardians County','Guardians Province','Guardians Country','Guardians Multiple'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_guardian_support_docs': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Guardian Support Documents:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Guardians Support Documents'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Guardians Support Documents': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians Support Documents', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Support Documents" onchange="save_options();">Support Documents</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_sibling_information': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Sibling Information:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Guardians Siblings','Siblings First Name','Siblings Last Name','Siblings Cell Phone','Siblings Home Phone','Siblings Address','Siblings City','Siblings Province','Siblings Postal Code','Siblings Country','Siblings Multiple'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_trustee': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Trustee:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Trustee Type','Trustee Family Trustee','Trustee Family Appointed Trustee','Trustee Public Trustee','Trustee Court Appointed Trustee','Trustee First Name','Trustee Last Name','Trustee Relationship','Trustee Work Phone','Trustee Home Phone','Trustee Cell Phone','Trustee Fax','Trustee Address','Trustee Postal Code','Trustee City','Trustee County','Trustee Province','Trustee Country','Trustee Support Documents'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_doctors': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Doctors:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Family Doctor Type','Family Doctor First Name','Family Doctor Last Name','Family Doctor Relationship','Family Doctor Work Phone','Family Doctor Home Phone','Family Doctor Cell Phone','Family Doctor Fax','Family Doctor Address','Family Doctor Postal Code','Family Doctor City','Family Doctor County','Family Doctor Province','Family Doctor Country','Family Doctor Multiple'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_doctors_support_docs': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Doctor Support Documents:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Family Doctor Support Documents'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_dentist': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Dentist:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Dentist Type','Dentist First Name','Dentist Last Name','Dentist Relationship','Dentist Work Phone','Dentist Home Phone','Dentist Cell Phone','Dentist Fax','Dentist Address','Dentist Postal Code','Dentist City','Dentist County','Dentist Province','Dentist Country','Dentist Support Documents'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_specialist': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Specialist:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Specialists Type','Specialists First Name','Specialists Last Name','Specialists Relationship','Specialists Work Phone','Specialists Home Phone','Specialists Cell Phone','Specialists Fax','Specialists Address','Specialists Postal Code','Specialists City','Specialists County','Specialists Province','Specialists Country','Specialists Multiple'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_specialist_support_docs': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Specialist Support Documents:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Specialists Support Documents'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_insurer': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Insurer:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Insurer','Insurer First Name','Insurer Last Name','Insurer Relationship','Insurer Work Phone','Insurer Home Phone','Insurer Cell Phone','Insurer Fax','Insurer Address','Insurer Postal Code','Insurer City','Insurer County','Insurer Province','Insurer Country','Insurer Support Documents'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_payment_information': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Payment &amp; Billing Information:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Max KM','Max Pieces','Account Number','Payment Type','Payment Name','Payment Sync Address','Payment Address','Payment City Quadrant','Payment City','Payment State','Payment Country','Payment Postal Code','GST #','PST #','Vendor GST #','Payment Information','Condo Fees','Total Monthly Rate','Total Annual Rate','Pricing Level','Budget','Preferred Payment Info','Global Discount Type','Global Discount Value','Payment Frequency','Total Bill Amount'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_financial': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Financial:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Void Cheque','Bank Name','Bank Institution Number','Bank Transit Number','Bank Account Number','EFT'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_account_details': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Account Details:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Billable Hours','Billable Dollars','Hours Tracked','Hours Billed','Accounts Receivable/Credit on Account','Patient Accounts Receivable','Insurer Accounts Receivable for Patient','All Patient Invoices','All Insurer Invoices for Patient'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_insurer_payment_information': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Insurer Payment Information:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Insurer Payer','Insurer Plan'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_medical_information': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Medical Information:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Medical Details Diagnosis','Medical Diagnosis Concerns','Medical Diagnosis Procedures','Diagnosis Support Documents','Medical Details Allergies','Medical Allergy Concerns','Medical Allergy Procedures','Allergies Support Documents','Medical Details Seizure','Medical Seizure Concerns','Medical Seizure Procedures','Medical Details Equipment','Medical Equipment Concerns','Medical Equipment Procedures','Equipment Support Documents','Medical Details Goals','Medical Goals Concerns','Medical Goals Procedures','Goals Support Documents','Medical Support Documents'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_projects': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Projects:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Estimates','Deposit','Damage Deposit','Quote Description'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_transportation': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Transportation:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Transportation Mode of Transportation','Transportation Transit Access','Transportation Access Password','Transportation Drivers License','Drivers License Class','Drive Manual Transmission','Transportation Drivers Glasses','Transportation Upload License','Transportation Support Documents'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_vehicle_description': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Vehicle Description:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['License Plate #','Upload License Plate','CARFAX'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_protocols_details': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Protocols Details:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Seizure Protocol Details','Seizure Protocol Upload','Slip Fall Protocol Details','Slip Fall Protocol Upload','Transfer Protocol Details','Transfer Protocol Upload','Toileting Protocol Details','Toileting Protocol Upload','Bathing Protocol Details','Bathing Protocol Upload','G-Tube Protocol Details','G-Tube Protocol Upload','Food Preferences','Oxygen Protocol Details','Oxygen Protocol Upload','First Aid CPR Details','SRC Details','SRC Upload'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_day_program': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Day Program:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Day Program Name','Day Program Address','Day Program Phone','Day Program Key Worker'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_protocol_log_notes': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Protocol Log Notes:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Protocols Daily Log Notes','Protocols Completed Date','Protocols Start Time','Protocols End Time','Protocols Completed By','Protocols Signature Box','Protocols Management Comments','Protocols Management Completed Date','Protocols Management Completed By','Protocols Management Signature Box'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_routine_log_notes': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Routine Log Notes:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Routines Daily Log Notes','Routines Completed Date','Routines Start Time','Routines End Time','Routines Completed By','Routines Signature Box','Routines Management Comments','Routines Management Completed Date','Routines Management Completed By','Routines Management Signature Box'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_communication_log_notes': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Communication Log Notes:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Communication Daily Log Notes','Communication Completed Date','Communication Start Time','Communication End Time','Communication Completed By','Communication Signature Box','Communication Management Comments','Communication Management Completed Date','Communication Management Completed By','Communication Management Signature Box'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_daily_log_notes_activities_details': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Daily Log Notes Activities Details:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Activities Daily Log Notes','Activities Completed Date','Activities Start Time','Activities End Time','Activities Completed By','Activities Signature Box','Activities Management Comments','Activities Management Completed Date','Activities Management Completed By','Activities Management Signature Box'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_new_hire_package': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">New Hire Package:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Upload Application','Start Date','Expiry Date','Renewal Date'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_orientation': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Orientation:</label>
                                                <div class="col-sm-8"><label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_orientation">Enable</label>
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Orientation Email Address'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_human_resources': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Human Resources:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['HR'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_software_login': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Software Login:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Role','User Name','Password','Auto-Generate Using Email','Email Credentials'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_security_access': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Security Access:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Region Access','Location Access','Classification Access','Equipment Access','Dispatch Staff Access','Dispatch Team Access'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_property_description': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Property Description:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Property Size','Property Type','Property Information','Upload Property Information','Property Instructions','Unit #','Condo Fees Property','Base Rent','Base Rent/Sq. Ft.','CAC','CAC/Sq. Ft.','Property Tax','Property Tax/Sq. Ft.','Upload Inspection','Bay #','Location Square Footage','Location Num Bathrooms','Location Alarm','Location Pets'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                        <div class="property_types_div" <?= !in_array('Property Type', $field_config) ? 'style=";"' : '' ?>>
                                                            <h3>Property Types</h3>
                                                            <div class="form-group">
                                                                <label class="col-sm-4 control-label">Property Types:<br><em>Enter property types separated by a comma.</em></label>
                                                                <div class="col-sm-8">
                                                                    <?php $property_types = get_config($dbc, $folder.'_'.$current_type.'_property_types'); ?>
                                                                    <input type="text" name="contact_property_types" value="<?= $property_types ?>" onchange="save_property_types();" class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_contract': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Contract:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Upload Letter of Intent','Upload Vendor Documents','Upload Marketing Material','Upload Purchase Contract','Upload Support Contract','Upload Support Terms','Upload Rental Contract','Upload Management Contract','Upload Articles of Incorporation','Option to Renew','Contract Allocated Hours','Contract Allocated Hours Multiple Types','Contract Total Value','Contract Start Date','Contract End Date'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                        <div class="allocated_hours_types_div" <?= !in_array('Contract Allocated Hours Multiple Types', $field_config) ? 'style=";"' : '' ?>>
                                                            <h3>Allocated Hours Types</h3>
                                                            <div class="form-group">
                                                                <label class="col-sm-4 control-label">Allocated Hours Types:<br><em>Enter allocated hours types separated by a comma.</em></label>
                                                                <div class="col-sm-8">
                                                                    <?php $allocated_hours_types = get_config($dbc, $folder.'_'.$current_type.'_allocated_hours_types'); ?>
                                                                    <input type="text" name="contact_allocated_hours_types" value="<?= $allocated_hours_types ?>" onchange="save_allocated_hours_types();" class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_contract_workers': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Contract Workers:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Contract Worker Sheet','Contract Worker List','Contract Worker Abstract','Contract Worker Licences','Contract Worker Criminal Record','Contract Worker Criminal Record Auth','Contract Worker Bank Info','Contract Worker Business Registration','Contract Workers Reminders'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_contract_policies': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Contract Policies:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Contract Policies Agreement','Contract Policies Non Compete','Contract Policies Non Solicitation','Contract Policies Confidentiality','Contract Policies Uniforms','Contract Policies Leasing','Contract Policies Fuel Card','Contract Policies Reminders'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_contract_wcb': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Contract WCB:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Contract WCB Clearance','Contract WCB Good Standing','Contract WCB Insurance','Contract WCB Reminders'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_contract_rates': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Contract Rates:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Contract Rates Signed','Contract Rates Reminders'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_contract_vehicles': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Contract Vehicles:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Contract Vehicles Make','Contract Vehicles Licence Plate','Contract Vehicles Registration','Contract Vehicles Reminders'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_reminders': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Reminders:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Reminders'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_site_information': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Site Information:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Customer(Client/Customer/Business)','Attached Contact','Site Name (Location)','Display Name','Business Sites','Site LSD','Site Bottom Hole','Site Alias','Site Website'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_emergency_plan': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Emergency Plan:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Emergency Police','Emergency Poison','Emergency Non','Emergency Contact','Emergency Notes'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_strategies': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Strategies:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Strategies Levels of Communication','Strategies Types of Supports','Strategies Likes','Strategies Dislikes','Strategies Required Accommodations'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_alerts': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Alerts:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Alert Staff','Alert Sending Email Address','Alert Email Subject','Alert Email Body'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_notifications': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Notifications:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Notification Type'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_wcb_information': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">WCB:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['WCB Claim Number','WCB Date of Accident','WCB Add Multiple'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_booking_information': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Booking Information:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Preferred Booking Time','Booking Extra'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_driver': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Driver:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Name of Drivers License','Drivers License Number','Drivers License','Drivers Abstract'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_cor_fields': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">COR:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['COR Certified','COR Number'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_calendar_settings': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Calendar Settings:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Calendar Color'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_subtab_config': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Subtab Configuration:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Visibility Options'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;



                                        case 'acc_upcoming_appointments_addition': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Upcoming Appointments:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Upcoming Appointments'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;


                                        case 'acc_ticket_tile_notes': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label"><?php echo TICKET_NOUN.' Notes'; ?>:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Session Notes'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Session Notes': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Session Notes', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Session Notes" onchange="save_options();">Session Notes</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;

                                        case 'acc_match_tile': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Match:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Match'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;

                                        case 'acc_intake_tile': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Intake Forms:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['Intake Forms'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_vendor_price_lists': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Vendor Price List:</label>
                                                <div class="col-sm-8">
                                                    <div class="block-group sortable_group" style=";">
                                                        <?php foreach(array_unique(array_intersect($field_config,['VPL Import/Export','VPL Description','Color','Category','Description','Product Name','Subcategory','Type','VPL Unique Identifier','Code','ID #','Item SKU','Part #','VPL Product Cost','Average Cost','CDN Cost Per Unit','COGS','Cost','USD Cost Per Unit','USD Invoice','VPL Purchase Info','Date Of Purchase','Purchase Cost','Vendor','VPL Shipping Receiving','Exchange $','Exchange Rate','Freight Charge','Shipping Cash','Shipping Rate','VPL Pricing','Admin Price','Client Price','Commercial Price','Commission Price','Final Retail Price','MSRP','Preferred Price','Purchase Order Price','Rush Price','Sales Order Price','Sell Price','Suggested Retail Price','Unit Cost','Unit Price','Web Price','Wholesale Price','VPL Markup','Markup By $','Markup By %','VPL Stock','Buying Units','Current Stock','Variance','Quantity','Selling Units','Stocking Units','Write-offs','VPL Location','Location','LSD','VPL Dimensions','Size','Weight','VPL Alerts','Min Bin','Min Max','VPL Time Allocation','Actual Hours','Estimated Hours','VPL Admin Fees','GL Assets','GL Revenue','Minimum Billable','VPL Quote','Quote Description','VPL Status','Status','VPL Display On Website','Display On Website','VPL General','Comments','Notes','VPL Rental','Reminder/Alert','Rent Price','Rental Days','Rental Weeks','Rental Months','Rental Years','VPL Day/Week/Month/Year','#Of Hours','#Of Days','Daily','Weekly','Monthly','Annually','VPL Vehicle','#Of Kilometers','VPL Inclusion','Include in P.O.S.','Include in Purchase Orders','Include in Sales Orders','VPL Amount','Min Amount','Max Amount'])) as $field_option) {
																													?>
																															<label class="form-checkbox"><input type="checkbox" <?= in_array($field_option, $field_config_mandate) ? 'checked' : '' ?> name="contact_field[]" value="<?php echo $field_option; ?>" onchange="save_options();"><?php echo $field_option; ?></label>
																													<?php
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                    } ?>
                                </div>
                            </div>
                        </div>
                    <?php }
                } ?>
            </div>
            <br />
            <br />
            <div class="clearfix"></div>
        </div>
        </form>
    </div><!-- .dashboard-item -->
</div><!-- .standard-dashboard-body-content -->
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_fields.php') { ?>
	<div style=";"><?php include('../footer.php'); ?></div>
<?php } ?>
