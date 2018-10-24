<?php error_reporting(0);
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
		url: '../Contacts/contacts_ajax.php?action=contact_fields',
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
                        $field_config = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tile_name`='".FOLDER_NAME."' AND `tab`='$current_type' AND `subtab`='**no_subtab**'"))[0]); ?>
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
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_contact_description">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Business', 'Service', 'Site','Ref Contact','Contact','Employee ID','Contact Image','Contact Prefix','Contact Filters','First Name','Last Name','Middle','Preferred Name','Name on Account','Name','Initials','Title','Credential','Home Phone','Office Phone','Cell Phone','Phone Carrier','Fax','Email Address','Second Email Address','Preferred Contact Method','Website','Position','Preferred Staff','Intake Form','Region','Location','Classification','LinkedIn','Facebook','Twitter','Google+','Instagram','Pinterest','YouTube','Blog','Profile Priority','Rating','Next Follow Up Date','Follow Up Staff','Status','Profile Documents','Upload Docs','History','Background Check','Start Date','Business Hours','Hours of Operation'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Business': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Business', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Business" onchange="save_options();">Business</label><?php break;
                                                                case 'Service': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Service', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Service" onchange="save_options();">Service</label><?php break;
                                                                case 'Site': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Site', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Site" onchange="save_options();">Site</label><?php break;
                                                                case 'Ref Contact': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Ref Contact', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Ref Contact" onchange="save_options();">Contact (Reference)</label><?php break;
                                                                case 'Contact': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contact', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contact" onchange="save_options();">Contact</label><?php break;
                                                                case 'Employee ID': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Employee ID', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Employee ID" onchange="save_options();">ID #</label><?php break;
                                                                case 'Contact Image': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contact Image', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contact Image" onchange="save_options();">Profile Image</label><?php break;
                                                                case 'Contact Prefix': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contact Prefix', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contact Prefix" onchange="save_options();">Prefix</label><?php break;
                                                                case 'Contact Filters': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contact Filters', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contact Filters" onchange="save_options();">Filters</label><?php break;
                                                                case 'First Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('First Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="First Name" onchange="save_options();">First Name</label><?php break;
                                                                case 'Last Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Last Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Last Name" onchange="save_options();">Last Name</label><?php break;
                                                                case 'Middle': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Middle', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Middle" onchange="save_options();">Middle</label><?php break;
                                                                case 'Preferred Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Preferred Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Preferred Name" onchange="save_options();">Preferred Name</label><?php break;
                                                                case 'Name on Account': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Name on Account', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Name on Account" onchange="save_options();">Name on Account</label><?php break;
                                                                case 'Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Name" onchange="save_options();"><?= $current_type ?> Name</label><?php break;
                                                                case 'Initials': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Initials', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Initials" onchange="save_options();">Initials</label><?php break;
                                                                case 'Title': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Title', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Title" onchange="save_options();">Title</label><?php break;
                                                                case 'Credential': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Credential', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Credential" onchange="save_options();">Credentials</label><?php break;
                                                                case 'Home Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Home Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Home Phone" onchange="save_options();">Home Phone</label><?php break;
                                                                case 'Office Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Office Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Office Phone" onchange="save_options();">Business Phone</label><?php break;
                                                                case 'Cell Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Cell Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Cell Phone" onchange="save_options();">Cell Phone</label><?php break;
                                                                case 'Phone Carrier': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Phone Carrier', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Phone Carrier" onchange="save_options();">Phone Carrier</label><?php break;
                                                                case 'Fax': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Fax', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Fax" onchange="save_options();">Fax #</label><?php break;
                                                                case 'Email Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Email Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Email Address" onchange="save_options();">Email Address</label><?php break;
                                                                case 'Second Email Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Second Email Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Second Email Address" onchange="save_options();">Second Email Address</label><?php break;
                                                                case 'Preferred Contact Method': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Preferred Contact Method', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Preferred Contact Method" onchange="save_options();">Preferred Method of Contact</label><?php break;
                                                                case 'Website': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Website', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Website" onchange="save_options();">Website</label><?php break;
                                                                case 'Position': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Position', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Position" onchange="save_options();">Position</label><?php break;
                                                                case 'Preferred Staff': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Preferred Staff', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Preferred Staff" onchange="save_options();">Preferred Staff</label><?php break;
                                                                case 'Intake Form': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Intake Form', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Intake Form" onchange="save_options();">Intake Form</label><?php break;
                                                                case 'Region': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Region', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Region" onchange="save_options();">Region</label><?php break;
                                                                case 'Location': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Location', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Location" onchange="save_options();">Location</label><?php break;
                                                                case 'Classification': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Classification', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Classification" onchange="save_options();">Classification</label><?php break;
                                                                case 'LinkedIn': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('LinkedIn', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="LinkedIn" onchange="save_options();">LinkedIn</label><?php break;
                                                                case 'Facebook': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Facebook', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Facebook" onchange="save_options();">Facebook</label><?php break;
                                                                case 'Twitter': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Twitter', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Twitter" onchange="save_options();">Twitter</label><?php break;
                                                                case 'Google+': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Google+', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Google+" onchange="save_options();">Google+</label><?php break;
                                                                case 'Instagram': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Instagram', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Instagram" onchange="save_options();">Instagram</label><?php break;
                                                                case 'Pinterest': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Pinterest', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Pinterest" onchange="save_options();">Pinterest</label><?php break;
                                                                case 'YouTube': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('YouTube', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="YouTube" onchange="save_options();">YouTube</label><?php break;
                                                                case 'Blog': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Blog', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Blog" onchange="save_options();">Blog</label><?php break;
                                                                case 'Profile Priority': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Priority', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Priority" onchange="save_options();">Profile Priority</label><?php break;
                                                                case 'Rating': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('Rating', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Rating" onchange="save_options();">Rating
                                                                    <div class="block-group">Rating Colours:<br />
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Bronze Rating', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Bronze Rating" onchange="save_options();">Bronze</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Silver Rating', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Silver Rating" onchange="save_options();">Silver</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Gold Rating', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Gold Rating" onchange="save_options();">Gold</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Platinum Rating', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Platinum Rating" onchange="save_options();">Platinum</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Diamond Rating', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Diamond Rating" onchange="save_options();">Diamond</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Green Rating', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Green Rating" onchange="save_options();">Green</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Yellow Rating', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Yellow Rating" onchange="save_options();">Yellow</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Light blue Rating', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Light blue Rating" onchange="save_options();">Light Blue</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Dark blue Rating', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Dark blue Rating" onchange="save_options();">Dark Blue</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Red Rating', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Red Rating" onchange="save_options();">Red</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Pink Rating', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Pink Rating" onchange="save_options();">Pink</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Purple Rating', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Purple Rating" onchange="save_options();">Purple</label>
                                                                    </div></label><?php break;
                                                                case 'Next Follow Up Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Next Follow Up Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Next Follow Up Date" onchange="save_options();">Next Follow Up Date</label><?php break;
                                                                case 'Follow Up Staff': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Follow Up Staff', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Follow Up Staff" onchange="save_options();">Follow Up Staff</label><?php break;
                                                                case 'Status': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Status', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Status" onchange="save_options();">Status</label><?php break;
                                                                case 'Profile Documents': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Documents', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Documents" onchange="save_options();">Profile Documents</label><?php break;
                                                                case 'Upload Docs': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Upload Docs', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Upload Docs" onchange="save_options();">Uploaded Documents</label><?php break;
                                                                case 'History': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('History', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="History" onchange="save_options();">History</label><?php break;
                                                                case 'Background Check': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Background Check', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Background Check" onchange="save_options();">Background Check</label><?php break;
                                                                case 'Start Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Start Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Start Date" onchange="save_options();">Start Date</label><?php break;
                                                                case 'Business Hours': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Business Hours', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Business Hours" onchange="save_options();">Hours</label><?php break;
                                                                case 'Hours of Operation': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Hours of Operation', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Hours of Operation" onchange="save_options();">Hours of Operation</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_personal_information': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Personal Information:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_personal_information">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Date of Birth','School','FSCD Number','Gender','Preferred Pronoun','Height','Weight','SIN','Client ID','Personal Email','Insurance AISH Entrance Date','AISH #','Health Care Number','Insurance Alberta Health Care','Assigned Staff','Strengths','Interests','Client Support Documents'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Date of Birth': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Date of Birth', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Date of Birth" onchange="save_options();">Date of Birth</label><?php break;
                                                                case 'School': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('School', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="School" onchange="save_options();">School</label><?php break;
                                                                case 'FSCD Number': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('FSCD Number', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="FSCD Number" onchange="save_options();">FSCD #</label><?php break;
                                                                case 'Gender': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Gender', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Gender" onchange="save_options();">Gender</label><?php break;
                                                                case 'Preferred Pronoun': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Preferred Pronoun', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Preferred Pronoun" onchange="save_options();">Preferred Pronoun</label><?php break;
                                                                case 'Height': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Height', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Height" onchange="save_options();">Height</label><?php break;
                                                                case 'Weight': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Weight', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Weight" onchange="save_options();">Weight</label><?php break;
                                                                case 'SIN': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('SIN', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="SIN" onchange="save_options();">SIN</label><?php break;
                                                                case 'Client ID': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Client ID', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Client ID" onchange="save_options();">Client ID #</label><?php break;
                                                                case 'Personal Email': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Personal Email', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Personal Email" onchange="save_options();">Personal Email</label><?php break;
                                                                case 'Insurance AISH Entrance Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurance AISH Entrance Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurance AISH Entrance Date" onchange="save_options();">AISH Entrance Date</label><?php break;
                                                                case 'AISH #': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('AISH #', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="AISH #" onchange="save_options();">AISH #</label><?php break;
                                                                case 'Health Care Number': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Health Care Number', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Health Care Number" onchange="save_options();">Health Care #</label><?php break;
                                                                case 'Insurance Alberta Health Care': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurance Alberta Health Care', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurance Alberta Health Care" onchange="save_options();">Alberta Health Care</label><?php break;
                                                                case 'Assigned Staff': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Assigned Staff', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Assigned Staff" onchange="save_options();">Assigned Staff</label><?php break;
                                                                case 'Strengths': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Strengths', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Strengths" onchange="save_options();">Strengths</label><?php break;
                                                                case 'Interests': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Interests', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Interests" onchange="save_options();">Interests</label><?php break;
                                                                case 'Client Support Documents': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Client Support Documents', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Client Support Documents" onchange="save_options();">Support Documents</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_contact_profile': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Member Profile:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_contact_profile">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Profile ID','Profile First Name','Profile Last Name','Profile Preferred Name','Profile Home Phone','Profile Office Phone','Profile Cell Phone','Profile Fax','Profile Email Address','Profile Intake Form','Profile Region','Profile Location','Profile Classification','Profile LinkedIn','Profile Facebook','Profile Twitter','Profile Google+','Profile Instagram','Profile Pinterest','Profile YouTube','Profile Blog','Profile Status','Profile Date of Birth','Profile School','Profile FSCD Number','Profile Gender','Profile Preferred Pronoun','Profile Height','Profile Weight','Profile SIN','Profile AISH #','Profile Health Care Number','Profile Insurance Alberta Health Care','Profile Assigned Staff','Profile Client Support Documents'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Profile ID': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile ID', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile ID" onchange="save_options();">ID #</label><?php break;
                                                                case 'Profile First Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile First Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile First Name" onchange="save_options();">First Name</label><?php break;
                                                                case 'Profile Last Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Last Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Last Name" onchange="save_options();">Last Name</label><?php break;
                                                                case 'Profile Preferred Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Preferred Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Preferred Name" onchange="save_options();">Preferred Name</label><?php break;
                                                                case 'Profile Home Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Home Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Home Phone" onchange="save_options();">Home Phone</label><?php break;
                                                                case 'Profile Office Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Office Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Office Phone" onchange="save_options();">Business Phone</label><?php break;
                                                                case 'Profile Cell Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Cell Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Cell Phone" onchange="save_options();">Cell Phone</label><?php break;
                                                                case 'Profile Fax': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Fax', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Fax" onchange="save_options();">Fax #</label><?php break;
                                                                case 'Profile Email Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Email Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Email Address" onchange="save_options();">Email Address</label><?php break;
                                                                case 'Profile Intake Form': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Intake Form', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Intake Form" onchange="save_options();">Intake Form</label><?php break;
                                                                case 'Profile Region': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Region', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Region" onchange="save_options();">Region</label><?php break;
                                                                case 'Profile Location': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Location', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Location" onchange="save_options();">Location</label><?php break;
                                                                case 'Profile Classification': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Classification', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Classification" onchange="save_options();">Classification</label><?php break;
                                                                case 'Profile LinkedIn': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile LinkedIn', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile LinkedIn" onchange="save_options();">LinkedIn</label><?php break;
                                                                case 'Profile Facebook': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Facebook', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Facebook" onchange="save_options();">Facebook</label><?php break;
                                                                case 'Profile Twitter': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Twitter', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Twitter" onchange="save_options();">Twitter</label><?php break;
                                                                case 'Profile Google+': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Google+', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Google+" onchange="save_options();">Google+</label><?php break;
                                                                case 'Profile Instagram': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Instagram', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Instagram" onchange="save_options();">Instagram</label><?php break;
                                                                case 'Profile Pinterest': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Pinterest', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Pinterest" onchange="save_options();">Pinterest</label><?php break;
                                                                case 'Profile YouTube': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile YouTube', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile YouTube" onchange="save_options();">YouTube</label><?php break;
                                                                case 'Profile Blog': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Blog', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Blog" onchange="save_options();">Blog</label><?php break;
                                                                case 'Profile Status': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Status', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Status" onchange="save_options();">Status</label><?php break;
                                                                case 'Profile Date of Birth': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Date of Birth', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Date of Birth" onchange="save_options();">Date of Birth</label><?php break;
                                                                case 'Profile School': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile School', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile School" onchange="save_options();">School</label><?php break;
                                                                case 'Profile FSCD Number': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile FSCD Number', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile FSCD Number" onchange="save_options();">FSCD #</label><?php break;
                                                                case 'Profile Gender': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Gender', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Gender" onchange="save_options();">Gender</label><?php break;
                                                                case 'Profile Preferred Pronoun': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Preferred Pronoun', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Preferred Pronoun" onchange="save_options();">Preferred Pronoun</label><?php break;
                                                                case 'Profile Height': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Height', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Height" onchange="save_options();">Height</label><?php break;
                                                                case 'Profile Weight': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Weight', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Weight" onchange="save_options();">Weight</label><?php break;
                                                                case 'Profile SIN': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile SIN', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile SIN" onchange="save_options();">SIN</label><?php break;
                                                                case 'Profile AISH #': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile AISH #', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile AISH #" onchange="save_options();">AISH #</label><?php break;
                                                                case 'Profile Health Care Number': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Health Care Number', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Health Care Number" onchange="save_options();">Health Care #</label><?php break;
                                                                case 'Profile Insurance Alberta Health Care': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Insurance Alberta Health Care', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Insurance Alberta Health Care" onchange="save_options();">Alberta Health Care</label><?php break;
                                                                case 'Profile Assigned Staff': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Assigned Staff', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Assigned Staff" onchange="save_options();">Assigned Staff</label><?php break;
                                                                case 'Profile Client Support Documents': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Profile Client Support Documents', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Profile Client Support Documents" onchange="save_options();">Support Documents</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_marketing': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Marketing:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_marketing">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Referred By','Referred By Name','Hear About','Contact Since','Date of Last Contact'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Referred By': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Referred By', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Referred By" onchange="save_options();">Referred By</label><?php break;
                                                                case 'Referred By Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Referred By Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Referred By Name" onchange="save_options();">Referred By Name</label><?php break;
                                                                case 'Hear About': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Hear About', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Hear About" onchange="save_options();">How Did You Hear About Us?</label><?php break;
                                                                case 'Contact Since': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contact Since', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contact Since" onchange="save_options();">Contact Since</label><?php break;
                                                                case 'Date of Last Contact': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Date of Last Contact', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Date of Last Contact" onchange="save_options();">Date of Last Contact</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_memberships': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Memberships:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_memberships">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Membership Type','Membership Status','Membership Level','Membership Level Dropdown','Membership Since','Membership Renewal Date','Membership Reminder Email Date','Membership Reminder Email'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Membership Type': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Membership Type', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Membership Type" onchange="save_options();">Membership Type</label><?php break;
                                                                case 'Membership Status': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Membership Status', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Membership Status" onchange="save_options();">Membership Status</label><?php break;
                                                                case 'Membership Level': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Membership Level', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Membership Level" onchange="save_options();">Membership Level</label><?php break;
                                                                case 'Membership Level Dropdown': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Membership Level Dropdown', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Membership Level Dropdown" onchange="save_options();">Membership Level Dropdown (Service Types)</label><?php break;
                                                                case 'Membership Since': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Membership Since', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Membership Since" onchange="save_options();">Membership Since</label><?php break;
                                                                case 'Membership Renewal Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Membership Renewal Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Membership Renewal Date" onchange="save_options();">Membership Renewal Date</label><?php break;
                                                                case 'Membership Reminder Email Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Membership Reminder Email Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Membership Reminder Email Date" onchange="save_options();">Reminder Email Date</label><?php break;
                                                                case 'Membership Reminder Email': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Membership Reminder Email', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Membership Reminder Email" onchange="save_options();">Reminder Email</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_programs': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Programs:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_programs">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Program Business','Program Type','Program Status','Program Level','Program Since','Program Renewal Date','Program Reminder Email Date','Program Reminder Email'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Program Business': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Program Business', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Program Business" onchange="save_options();"><?= BUSINESS_CAT ?></label><?php break;
                                                                case 'Program Type': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('Program Type', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Program Type" onchange="save_options();">Program Type
                                                                    <div class="block-group">Program Types:<br />
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Programs LAAFS Program', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Programs LAAFS Program" onchange="save_options();">LAAFS Program</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Programs AAFS Program', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Programs AAFS Program" onchange="save_options();">AAFS Program</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Programs Over 16 Program', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Programs Over 16 Program" onchange="save_options();">Fellowship 16 Program</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Programs Over 18 Program', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Programs Over 18 Program" onchange="save_options();">Fellowship 18 Program</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Programs Developmental Aide', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Programs Developmental Aide" onchange="save_options();">Developmental Aide</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Programs Specialized Services', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Programs Specialized Services" onchange="save_options();">Specialized Services</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Programs Private', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Programs Private" onchange="save_options();">Private</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Programs Other', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Programs Other" onchange="save_options();">Other</label>
                                                                    </div></label><?php break;
                                                                case 'Program Status': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Program Status', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Program Status" onchange="save_options();">Program Status</label><?php break;
                                                                case 'Program Level': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Program Level', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Program Level" onchange="save_options();">Program Level</label><?php break;
                                                                case 'Program Since': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Program Since', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Program Since" onchange="save_options();">Program Since</label><?php break;
                                                                case 'Program Renewal Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Program Renewal Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Program Renewal Date" onchange="save_options();">Program Renewal Date</label><?php break;
                                                                case 'Program Reminder Email Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Program Reminder Email Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Program Reminder Email Date" onchange="save_options();">Reminder Email Date</label><?php break;
                                                                case 'Program Reminder Email': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Program Reminder Email', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Program Reminder Email" onchange="save_options();">Reminder Email</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_funding': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Funding:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_funding">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Funding FSCD','Funding FSCD Worker Name','Funding FSCD File ID','Funding FSCD Renewal Date','Funding Support Documents','Funding PDD','PDD Key Contact','PDD Client ID','PDD Phone','PDD Fax','PDD Email','PDD AISH','Multiple PDD Contacts'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Funding FSCD': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Funding FSCD', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Funding FSCD" onchange="save_options();">Family Support for Children with Disabilities (FSCD)</label><?php break;
                                                                case 'Funding FSCD Worker Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Funding FSCD Worker Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Funding FSCD Worker Name" onchange="save_options();">FSCD Worker Name</label><?php break;
                                                                case 'Funding FSCD File ID': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Funding FSCD File ID', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Funding FSCD File ID" onchange="save_options();">FSCD File ID</label><?php break;
                                                                case 'Funding FSCD Renewal Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Funding FSCD Renewal Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Funding FSCD Renewal Date" onchange="save_options();">FSCD Renewal Date</label><?php break;
                                                                case 'Funding Support Documents': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Funding Support Documents', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Funding Support Documents" onchange="save_options();">Persons with Developmental Disabilities (PDD) Support Documents</label><?php break;
                                                                case 'Funding PDD': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Funding PDD', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Funding PDD" onchange="save_options();">Persons with Developmental Disabilities (PDD)</label><?php break;
                                                                case 'PDD Key Contact': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('PDD Key Contact', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="PDD Key Contact" onchange="save_options();">PDD Key Contact</label><?php break;
                                                                case 'PDD Client ID': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('PDD Client ID', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="PDD Client ID" onchange="save_options();">PDD Client ID</label><?php break;
                                                                case 'PDD Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('PDD Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="PDD Phone" onchange="save_options();">PDD Phone</label><?php break;
                                                                case 'PDD Fax': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('PDD Fax', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="PDD Fax" onchange="save_options();">PDD Fax</label><?php break;
                                                                case 'PDD Email': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('PDD Email', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="PDD Email" onchange="save_options();">PDD Email</label><?php break;
                                                                case 'PDD AISH': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('PDD AISH', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="PDD AISH" onchange="save_options();">PDD AISH #</label><?php break;
                                                                case 'Multiple PDD Contacts': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Multiple PDD Contacts', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Multiple PDD Contacts" onchange="save_options();">Multiple PDD Contacts</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_notes': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Notes:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_notes">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Comments','Comments Attachment','Description','Description Attachment','General Comments','General Comments Attachment','Notes','Notes Attachment','Service Notes'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Comments': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Comments', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Comments" onchange="save_options();">Comments</label><?php break;
                                                                case 'Comments Attachment': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Comments Attachment', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Comments Attachment" onchange="save_options();">Comments Attachment</label><?php break;
                                                                case 'Description': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Description', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Description" onchange="save_options();">Description</label><?php break;
                                                                case 'Description Attachment': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Description Attachment', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Description Attachment" onchange="save_options();">Description Attachment</label><?php break;
                                                                case 'General Comments': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('General Comments', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="General Comments" onchange="save_options();">General Comments</label><?php break;
                                                                case 'General Comments Attachment': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('General Comments Attachment', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="General Comments Attachment" onchange="save_options();">General Comments Attachment</label><?php break;
                                                                case 'Notes': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Notes', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Notes" onchange="save_options();">Notes</label><?php break;
                                                                case 'Notes Attachment': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Notes Attachment', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Notes Attachment" onchange="save_options();">Notes Attachment</label><?php break;
                                                                case 'Service Notes': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Service Notes', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Service Notes" onchange="save_options();">Service Notes</label><?php break;
                                                            }
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
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_address">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Address Sync To Site','Synced Site Hide Address','Address Default Sync On','Address Create Site','Full Address','Address','City Quadrant','City','County','Province','Country','Postal Code','Google Maps Address','Key Number','Door Code Number','Alarm Code Number'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Address Sync To Site': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Address Sync To Site', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Address Sync To Site" onchange="save_options();">Sync Address to <?= SITES_CAT ?></label><?php break;
                                                                case 'Address Default Sync On': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Address Default Sync On', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Address Default Sync On" onchange="save_options();">Default Sync On For New Contacts</label><?php break;
                                                                case 'Synced Site Hide Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Synced Site Hide Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Synced Site Hide Address" onchange="save_options();">Synced <?= SITES_CAT ?> Hides Address</label><?php break;
                                                                case 'Address Create Site': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Address Create Site', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Address Create Site" onchange="save_options();">Create <?= SITES_CAT ?> From Address</label><?php break;
                                                                case 'Full Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Full Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Full Address" onchange="save_options();">Full Address</label><?php break;
                                                                case 'Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Address" onchange="save_options();">Address</label><?php break;
                                                                case 'City Quadrant': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('City Quadrant', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="City Quadrant" onchange="save_options();">City Quadrant</label><?php break;
                                                                case 'City': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('City', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="City" onchange="save_options();">City / Town</label><?php break;
                                                                case 'County': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('County', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="County" onchange="save_options();">County</label><?php break;
                                                                case 'Province': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Province', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Province" onchange="save_options();">Province / State</label><?php break;
                                                                case 'Country': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Country', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Country" onchange="save_options();">Country</label><?php break;
                                                                case 'Postal Code': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Postal Code', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Postal Code" onchange="save_options();">Postal Code / Zip Code</label><?php break;
                                                                case 'Google Maps Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Google Maps Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Google Maps Address" onchange="save_options();">Google Maps Address</label><?php break;
                                                                case 'Key Number': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Key Number', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Key Number" onchange="save_options();">Key Number</label><?php break;
                                                                case 'Door Code Number': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Door Code Number', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Door Code Number" onchange="save_options();">Door Code Number</label><?php break;
                                                                case 'Alarm Code Number': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Alarm Code Number', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Alarm Code Number" onchange="save_options();">Alarm Code Number</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_second_address': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Second Address:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_second_address">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Second Full Address','Second Address','Second City Quadrant','Second City','Second County','Second Province','Second Country','Second Postal Code','Second Google Maps Address'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Second Full Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Second Full Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Second Full Address" onchange="save_options();">Full Address</label><?php break;
                                                                case 'Second Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Second Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Second Address" onchange="save_options();">Address</label><?php break;
                                                                case 'Second City Quadrant': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Second City Quadrant', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Second City Quadrant" onchange="save_options();">City Quadrant</label><?php break;
                                                                case 'Second City': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Second City', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Second City" onchange="save_options();">City / Town</label><?php break;
                                                                case 'Second County': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Second County', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Second County" onchange="save_options();">County</label><?php break;
                                                                case 'Second Province': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Second Province', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Second Province" onchange="save_options();">Province / State</label><?php break;
                                                                case 'Second Country': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Second Country', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Second Country" onchange="save_options();">Country</label><?php break;
                                                                case 'Second Postal Code': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Second Postal Code', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Second Postal Code" onchange="save_options();">Postal Code / Zip Code</label><?php break;
                                                                case 'Second Google Maps Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Second Google Maps Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Second Google Maps Address" onchange="save_options();">Google Maps Address</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_business_address': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Business Address:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_business_address">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Business Sync To Site','Business Create Site','Business Full Address','Business Address','Business City Quadrant','Business City','Business County','Business Province','Business Country','Business Postal Code','Business Google Maps Address','Business Website'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Business Sync To Site': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Business Sync To Site', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Business Sync To Site" onchange="save_options();">Sync Address to <?= SITES_CAT ?></label><?php break;
                                                                case 'Business Create Site': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Business Create Site', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Business Create Site" onchange="save_options();">Create <?= SITES_CAT ?> From Address</label><?php break;
                                                                case 'Business Full Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Business Full Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Business Full Address" onchange="save_options();">Full Address</label><?php break;
                                                                case 'Business Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Business Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Business Address" onchange="save_options();">Address</label><?php break;
                                                                case 'Business City Quadrant': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Business City Quadrant', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Business City Quadrant" onchange="save_options();">City Quadrant</label><?php break;
                                                                case 'Business City': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Business City', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Business City" onchange="save_options();">City / Town</label><?php break;
                                                                case 'Business County': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Business County', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Business County" onchange="save_options();">County</label><?php break;
                                                                case 'Business Province': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Business Province', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Business Province" onchange="save_options();">Province / State</label><?php break;
                                                                case 'Business Country': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Business Country', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Business Country" onchange="save_options();">Country</label><?php break;
                                                                case 'Business Postal Code': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Business Postal Code', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Business Postal Code" onchange="save_options();">Postal Code / Zip Code</label><?php break;
                                                                case 'Business Google Maps Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Business Google Maps Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Business Google Maps Address" onchange="save_options();">Google Maps Address</label><?php break;
                                                                case 'Business Website': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Business Website', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Business Website" onchange="save_options();">Business Website</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_mailing_address': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Mailing / Shipping Address:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_mailing_address">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Mailing Lock Address','Mailing Sync Address','Mailing Sync To Site','Mailing Create Site','Mailing Full Address','Ship To Address','Ship City Quadrant','Ship City','Ship County','Ship State','Ship Country','Ship Zip','Ship Google Maps Address'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Mailing Lock Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Mailing Lock Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Mailing Lock Address" onchange="save_options();">Lock Address by Default</label><?php break;
                                                                case 'Mailing Sync Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Mailing Sync Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Mailing Sync Address" onchange="save_options();">Same as Main Address</label><?php break;
                                                                case 'Mailing Sync To Site': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Mailing Sync To Site', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Mailing Sync To Site" onchange="save_options();">Sync Address to <?= SITES_CAT ?></label><?php break;
                                                                case 'Mailing Create Site': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Mailing Create Site', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Mailing Create Site" onchange="save_options();">Create <?= SITES_CAT ?> From Address</label><?php break;
                                                                case 'Mailing Full Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Mailing Full Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Mailing Full Address" onchange="save_options();">Full Address</label><?php break;
                                                                case 'Ship To Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Ship To Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Ship To Address" onchange="save_options();">Address</label><?php break;
                                                                case 'Ship City Quadrant': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Ship City Quadrant', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Ship City Quadrant" onchange="save_options();">City Quadrant</label><?php break;
                                                                case 'Ship City': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Ship City', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Ship City" onchange="save_options();">City / Town</label><?php break;
                                                                case 'Ship County': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Ship County', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Ship County" onchange="save_options();">County</label><?php break;
                                                                case 'Ship State': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Ship State', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Ship State" onchange="save_options();">Province / State</label><?php break;
                                                                case 'Ship Country': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Ship Country', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Ship Country" onchange="save_options();">Country</label><?php break;
                                                                case 'Ship Zip': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Ship Zip', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Ship Zip" onchange="save_options();">Postal Code / Zip Code</label><?php break;
                                                                case 'Ship Google Maps Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Ship Google Maps Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Ship Google Maps Address" onchange="save_options();">Google Maps Address</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_emergency_contacts': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Emergency Contacts:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_emergency_contacts">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Emergency Contact First Name','Emergency Contact Last Name','Emergency Contact Contact Number','Emergency Contact Relationship','Emergency Contact Work Phone','Emergency Contact Home Phone','Emergency Contact Cell Phone','Emergency Contact Fax','Emergency Contact Address','Emergency Contact Postal Code','Emergency Contact City','Emergency Contact County','Emergency Contact Province','Emergency Contact Country','Primary Emergency Contact First Name','Primary Emergency Contact Last Name','Primary Emergency Contact Relationship','Primary Emergency Contact Home Phone','Primary Emergency Contact Cell Phone','Primary Emergency Contact Email','Secondary Emergency Contact First Name','Secondary Emergency Contact Last Name','Secondary Emergency Contact Relationship','Secondary Emergency Contact Home Phone','Secondary Emergency Contact Cell Phone','Secondary Emergency Contact Email','Emergency Contact Multiple'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Emergency Contact First Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Contact First Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Contact First Name" onchange="save_options();">First Name</label><?php break;
                                                                case 'Emergency Contact Last Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Contact Last Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Contact Last Name" onchange="save_options();">Last Name</label><?php break;
                                                                case 'Emergency Contact Contact Number': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Contact Contact Number', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Contact Contact Number" onchange="save_options();">Contact Number</label><?php break;
                                                                case 'Emergency Contact Relationship': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Contact Relationship', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Contact Relationship" onchange="save_options();">Relationship</label><?php break;
                                                                case 'Emergency Contact Work Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Contact Work Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Contact Work Phone" onchange="save_options();">Work Phone</label><?php break;
                                                                case 'Emergency Contact Home Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Contact Home Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Contact Home Phone" onchange="save_options();">Home Phone</label><?php break;
                                                                case 'Emergency Contact Cell Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Contact Cell Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Contact Cell Phone" onchange="save_options();">Cell Phone</label><?php break;
                                                                case 'Emergency Contact Fax': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Contact Fax', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Contact Fax" onchange="save_options();">Fax #</label><?php break;
                                                                case 'Emergency Contact Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Contact Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Contact Address" onchange="save_options();">Emergency Contact Address</label><?php break;
                                                                case 'Emergency Contact Postal Code': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Contact Postal Code', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Contact Postal Code" onchange="save_options();">Postal Code / Zip Code</label><?php break;
                                                                case 'Emergency Contact City': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Contact City', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Contact City" onchange="save_options();">City / Town</label><?php break;
                                                                case 'Emergency Contact County': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Contact County', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Contact County" onchange="save_options();">County</label><?php break;
                                                                case 'Emergency Contact Province': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Contact Province', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Contact Province" onchange="save_options();">Province / State</label><?php break;
                                                                case 'Emergency Contact Country': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Contact Country', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Contact Country" onchange="save_options();">Country</label><?php break;
                                                                case 'Primary Emergency Contact First Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Primary Emergency Contact First Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Primary Emergency Contact First Name" onchange="save_options();">Primary Emergency Contact First Name</label><?php break;
                                                                case 'Primary Emergency Contact Last Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Primary Emergency Contact Last Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Primary Emergency Contact Last Name" onchange="save_options();">Primary Emergency Contact Last Name</label><?php break;
                                                                case 'Primary Emergency Contact Relationship': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Primary Emergency Contact Relationship', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Primary Emergency Contact Relationship" onchange="save_options();">Primary Emergency Contact Relationship</label><?php break;
                                                                case 'Primary Emergency Contact Home Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Primary Emergency Contact Home Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Primary Emergency Contact Home Phone" onchange="save_options();">Primary Emergency Contact Home Phone</label><?php break;
                                                                case 'Primary Emergency Contact Cell Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Primary Emergency Contact Cell Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Primary Emergency Contact Cell Phone" onchange="save_options();">Primary Emergency Contact Cell Phone</label><?php break;
                                                                case 'Primary Emergency Contact Email': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Primary Emergency Contact Email', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Primary Emergency Contact Email" onchange="save_options();">Primary Emergency Contact Email</label><?php break;
                                                                case 'Secondary Emergency Contact First Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Secondary Emergency Contact First Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Secondary Emergency Contact First Name" onchange="save_options();">Secondary Emergency Contact First Name</label><?php break;
                                                                case 'Secondary Emergency Contact Last Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Secondary Emergency Contact Last Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Secondary Emergency Contact Last Name" onchange="save_options();">Secondary Emergency Contact Last Name</label><?php break;
                                                                case 'Secondary Emergency Contact Relationship': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Secondary Emergency Contact Relationship', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Secondary Emergency Contact Relationship" onchange="save_options();">Secondary Emergency Contact Relationship</label><?php break;
                                                                case 'Secondary Emergency Contact Home Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Secondary Emergency Contact Home Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Secondary Emergency Contact Home Phone" onchange="save_options();">Secondary Emergency Contact Home Phone</label><?php break;
                                                                case 'Secondary Emergency Contact Cell Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Secondary Emergency Contact Cell Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Secondary Emergency Contact Cell Phone" onchange="save_options();">Secondary Emergency Contact Cell Phone</label><?php break;
                                                                case 'Secondary Emergency Contact Email': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Secondary Emergency Contact Email', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Secondary Emergency Contact Email" onchange="save_options();">Secondary Emergency Contact Email</label><?php break;
                                                                case 'Emergency Contact Multiple': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Contact Multiple', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Contact Multiple" onchange="save_options();">Multiple Emergency Contacts</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_emergency_support_docs': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Emergency Contact Support Documents:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_emergency_support_docs">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Emergency Contact Support Documents'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Emergency Contact Support Documents': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Contact Support Documents', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Contact Support Documents" onchange="save_options();">Support Documents</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_guardian_type': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Guardian Type:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_guardian_type">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Guardians Type','Guardians Family Guardian','Guardians Family Appointed Guardian','Guardians Public Guardian','Guardians Court Appointed Guardian'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Guardians Type': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('Guardians Type', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Type" onchange="set_sub_accordion(this); save_options();">Guardian Type
                                                                    <div class="sub-block-group guardian-tabs" <?= in_array('Guardians Type', $field_config) ? '' : 'style="display:none;"'; ?>><?php
                                                                        $get_guardian_type_tabs = get_config($dbc, 'guardian_type_tabs');
                                                                        if ( empty($get_guardian_type_tabs) ) {
                                                                            mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('guardian_type_tabs', 'Family Guardian#*#Personal Directive#*#Public Guardian#*#Own Guardian#*#Not Applicable')");
                                                                        }
                                                                        $guardian_tab_list = explode('#*#', get_config($dbc, 'guardian_type_tabs'));
                                                                        foreach($guardian_tab_list as $guardian_tab) { ?>
                                                                            <div class="form-group gap-bottom">
                                                                                <div class="col-sm-10"><input name="guardian_tabs[]" type="text" value="<?= $guardian_tab; ?>" class="form-control" onblur="save_guardian_tabs(this);" /></div>
                                                                                <div class="col-sm-2">
                                                                                    <img src="<?= WEBSITE_URL ?>/img/plus.png" style="height:1.5em; margin:0.25em; width:1.5em;" class="pull-right" onclick="add_guardian_tab();" />
                                                                                    <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height:1.5em; margin:0.25em; width:1.5em;" class="pull-right" onclick="rem_guardian_tab(this);" />
                                                                                </div>
                                                                            </div><?php
                                                                        } ?>
                                                                    </div></label><?php break;
                                                                case 'Guardians Family Guardian': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians Family Guardian', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Family Guardian" onchange="save_options();">Family Guardian</label><?php break;
                                                                case 'Guardians Family Appointed Guardian': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians Family Appointed Guardian', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Family Appointed Guardian" onchange="save_options();">Family Appointed Guardian</label><?php break;
                                                                case 'Guardians Public Guardian': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians Public Guardian', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Public Guardian" onchange="save_options();">Public Guardian</label><?php break;
                                                                case 'Guardians Court Appointed Guardian': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians Court Appointed Guardian', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Court Appointed Guardian" onchange="save_options();">Court Appointed Guardian</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_guardian_information': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Guardian Information:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_guardian_information">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Guardians Self','Guardians First Name','Guardians Last Name','Guardians Relationship','Guardians Work Phone','Guardians Home Phone','Guardians Cell Phone','Guardians Fax','Guardians Email Address','Guardians Address','Guardians Postal Code','Guardians City','Guardians County','Guardians Province','Guardians Country','Guardians Multiple'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Guardians Self': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians Self', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Self" onchange="save_options();">Can be own Guardian (Age 18+)</label><?php break;
                                                                case 'Guardians First Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians First Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians First Name" onchange="save_options();">First Name</label><?php break;
                                                                case 'Guardians Last Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians Last Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Last Name" onchange="save_options();">Last Name</label><?php break;
                                                                case 'Guardians Relationship': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians Relationship', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Relationship" onchange="save_options();">Relationship</label><?php break;
                                                                case 'Guardians Work Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians Work Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Work Phone" onchange="save_options();">Work Phone</label><?php break;
                                                                case 'Guardians Home Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians Home Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Home Phone" onchange="save_options();">Home Phone</label><?php break;
                                                                case 'Guardians Cell Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians Cell Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Cell Phone" onchange="save_options();">Cell Phone</label><?php break;
                                                                case 'Guardians Fax': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians Fax', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Fax" onchange="save_options();">Fax #</label><?php break;
                                                                case 'Guardians Email Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians Email Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Email Address" onchange="save_options();">Email Address</label><?php break;
                                                                case 'Guardians Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Address" onchange="save_options();">Guardians Address</label><?php break;
                                                                case 'Guardians Postal Code': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians Postal Code', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Postal Code" onchange="save_options();">Postal Code / Zip Code</label><?php break;
                                                                case 'Guardians City': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians City', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians City" onchange="save_options();">City / Town</label><?php break;
                                                                case 'Guardians County': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians County', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians County" onchange="save_options();">County</label><?php break;
                                                                case 'Guardians Province': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians Province', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Province" onchange="save_options();">Province / State</label><?php break;
                                                                case 'Guardians Country': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians Country', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Country" onchange="save_options();">Country</label><?php break;
                                                                case 'Guardians Multiple': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians Multiple', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Multiple" onchange="save_options();">Multiple Guardians</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_guardian_support_docs': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Guardian Support Documents:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_guardian_support_docs">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Guardians Support Documents'])) as $field_option) {
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
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_sibling_information">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Guardians Siblings','Siblings First Name','Siblings Last Name','Siblings Cell Phone','Siblings Home Phone','Siblings Address','Siblings City','Siblings Province','Siblings Postal Code','Siblings Country','Siblings Multiple'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Guardians Siblings': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Guardians Siblings', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Guardians Siblings" onchange="save_options();">Siblings</label><?php break;
                                                                case 'Siblings First Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Siblings First Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Siblings First Name" onchange="save_options();">Sibling First Name</label><?php break;
                                                                case 'Siblings Last Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Siblings Last Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Siblings Last Name" onchange="save_options();">Sibling Last Name</label><?php break;
                                                                case 'Siblings Cell Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Siblings Cell Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Siblings Cell Phone" onchange="save_options();">Sibling Cell Phone</label><?php break;
                                                                case 'Siblings Home Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Siblings Home Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Siblings Home Phone" onchange="save_options();">Sibling Home Phone</label><?php break;
                                                                case 'Siblings Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Siblings Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Siblings Address" onchange="save_options();">Sibling Address</label><?php break;
                                                                case 'Siblings City': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Siblings City', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Siblings City" onchange="save_options();">Sibling City</label><?php break;
                                                                case 'Siblings Province': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Siblings Province', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Siblings Province" onchange="save_options();">Sibling Province</label><?php break;
                                                                case 'Siblings Postal Code': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Siblings Postal Code', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Siblings Postal Code" onchange="save_options();">Sibling Postal Code</label><?php break;
                                                                case 'Siblings Country': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Siblings Country', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Siblings Country" onchange="save_options();">Sibling Country</label><?php break;
                                                                case 'Siblings Multiple': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Siblings Multiple', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Siblings Multiple" onchange="save_options();">Siblings Multiple</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_trustee': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Trustee:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_trustee">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Trustee Type','Trustee Family Trustee','Trustee Family Appointed Trustee','Trustee Public Trustee','Trustee Court Appointed Trustee','Trustee First Name','Trustee Last Name','Trustee Relationship','Trustee Work Phone','Trustee Home Phone','Trustee Cell Phone','Trustee Fax','Trustee Address','Trustee Postal Code','Trustee City','Trustee County','Trustee Province','Trustee Country','Trustee Support Documents'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Trustee Type': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Trustee Type', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Trustee Type" onchange="save_options();">Trustee Type</label><?php break;
                                                                case 'Trustee Family Trustee': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Trustee Family Trustee', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Trustee Family Trustee" onchange="save_options();">Family Trustee</label><?php break;
                                                                case 'Trustee Family Appointed Trustee': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Trustee Family Appointed Trustee', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Trustee Family Appointed Trustee" onchange="save_options();">Family Appointed Trustee</label><?php break;
                                                                case 'Trustee Public Trustee': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Trustee Public Trustee', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Trustee Public Trustee" onchange="save_options();">Public Trustee</label><?php break;
                                                                case 'Trustee Court Appointed Trustee': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Trustee Court Appointed Trustee', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Trustee Court Appointed Trustee" onchange="save_options();">Court Appointed Trustee</label><?php break;
                                                                case 'Trustee First Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Trustee First Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Trustee First Name" onchange="save_options();">First Name</label><?php break;
                                                                case 'Trustee Last Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Trustee Last Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Trustee Last Name" onchange="save_options();">Last Name</label><?php break;
                                                                case 'Trustee Relationship': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Trustee Relationship', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Trustee Relationship" onchange="save_options();">Relationship</label><?php break;
                                                                case 'Trustee Work Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Trustee Work Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Trustee Work Phone" onchange="save_options();">Work Phone</label><?php break;
                                                                case 'Trustee Home Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Trustee Home Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Trustee Home Phone" onchange="save_options();">Home Phone</label><?php break;
                                                                case 'Trustee Cell Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Trustee Cell Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Trustee Cell Phone" onchange="save_options();">Cell Phone</label><?php break;
                                                                case 'Trustee Fax': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Trustee Fax', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Trustee Fax" onchange="save_options();">Fax #</label><?php break;
                                                                case 'Trustee Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Trustee Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Trustee Address" onchange="save_options();">Trustee Address</label><?php break;
                                                                case 'Trustee Postal Code': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Trustee Postal Code', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Trustee Postal Code" onchange="save_options();">Postal Code / Zip Code</label><?php break;
                                                                case 'Trustee City': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Trustee City', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Trustee City" onchange="save_options();">City / Town</label><?php break;
                                                                case 'Trustee County': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Trustee County', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Trustee County" onchange="save_options();">County</label><?php break;
                                                                case 'Trustee Province': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Trustee Province', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Trustee Province" onchange="save_options();">Province / State</label><?php break;
                                                                case 'Trustee Country': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Trustee Country', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Trustee Country" onchange="save_options();">Country</label><?php break;
                                                                case 'Trustee Support Documents': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Trustee Support Documents', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Trustee Support Documents" onchange="save_options();">Support Documents</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_doctors': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Doctors:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_doctors">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Family Doctor Type','Family Doctor First Name','Family Doctor Last Name','Family Doctor Relationship','Family Doctor Work Phone','Family Doctor Home Phone','Family Doctor Cell Phone','Family Doctor Fax','Family Doctor Address','Family Doctor Postal Code','Family Doctor City','Family Doctor County','Family Doctor Province','Family Doctor Country','Family Doctor Multiple'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Family Doctor Type': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Family Doctor Type', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Family Doctor Type" onchange="save_options();">Doctor Type</label><?php break;
                                                                case 'Family Doctor First Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Family Doctor First Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Family Doctor First Name" onchange="save_options();">First Name</label><?php break;
                                                                case 'Family Doctor Last Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Family Doctor Last Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Family Doctor Last Name" onchange="save_options();">Last Name</label><?php break;
                                                                case 'Family Doctor Relationship': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Family Doctor Relationship', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Family Doctor Relationship" onchange="save_options();">Relationship</label><?php break;
                                                                case 'Family Doctor Work Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Family Doctor Work Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Family Doctor Work Phone" onchange="save_options();">Work Phone</label><?php break;
                                                                case 'Family Doctor Home Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Family Doctor Home Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Family Doctor Home Phone" onchange="save_options();">Home Phone</label><?php break;
                                                                case 'Family Doctor Cell Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Family Doctor Cell Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Family Doctor Cell Phone" onchange="save_options();">Cell Phone</label><?php break;
                                                                case 'Family Doctor Fax': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Family Doctor Fax', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Family Doctor Fax" onchange="save_options();">Fax #</label><?php break;
                                                                case 'Family Doctor Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Family Doctor Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Family Doctor Address" onchange="save_options();">Family Doctor Address</label><?php break;
                                                                case 'Family Doctor Postal Code': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Family Doctor Postal Code', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Family Doctor Postal Code" onchange="save_options();">Postal Code / Zip Code</label><?php break;
                                                                case 'Family Doctor City': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Family Doctor City', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Family Doctor City" onchange="save_options();">City / Town</label><?php break;
                                                                case 'Family Doctor County': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Family Doctor County', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Family Doctor County" onchange="save_options();">County</label><?php break;
                                                                case 'Family Doctor Province': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Family Doctor Province', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Family Doctor Province" onchange="save_options();">Province / State</label><?php break;
                                                                case 'Family Doctor Country': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Family Doctor Country', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Family Doctor Country" onchange="save_options();">Country</label><?php break;
                                                                case 'Family Doctor Multiple': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Family Doctor Multiple', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Family Doctor Multiple" onchange="save_options();">Multiple Doctors</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_doctors_support_docs': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Doctor Support Documents:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_doctors_support_docs">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Family Doctor Support Documents'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Family Doctor Support Documents': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Family Doctor Support Documents', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Family Doctor Support Documents" onchange="save_options();">Support Documents</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_dentist': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Dentist:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_dentist">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Dentist Type','Dentist First Name','Dentist Last Name','Dentist Relationship','Dentist Work Phone','Dentist Home Phone','Dentist Cell Phone','Dentist Fax','Dentist Address','Dentist Postal Code','Dentist City','Dentist County','Dentist Province','Dentist Country','Dentist Support Documents'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Dentist Type': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Dentist Type', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Dentist Type" onchange="save_options();">Dentist Type</label><?php break;
                                                                case 'Dentist First Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Dentist First Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Dentist First Name" onchange="save_options();">First Name</label><?php break;
                                                                case 'Dentist Last Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Dentist Last Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Dentist Last Name" onchange="save_options();">Last Name</label><?php break;
                                                                case 'Dentist Relationship': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Dentist Relationship', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Dentist Relationship" onchange="save_options();">Relationship</label><?php break;
                                                                case 'Dentist Work Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Dentist Work Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Dentist Work Phone" onchange="save_options();">Work Phone</label><?php break;
                                                                case 'Dentist Home Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Dentist Home Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Dentist Home Phone" onchange="save_options();">Home Phone</label><?php break;
                                                                case 'Dentist Cell Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Dentist Cell Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Dentist Cell Phone" onchange="save_options();">Cell Phone</label><?php break;
                                                                case 'Dentist Fax': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Dentist Fax', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Dentist Fax" onchange="save_options();">Fax #</label><?php break;
                                                                case 'Dentist Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Dentist Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Dentist Address" onchange="save_options();">Dentist Address</label><?php break;
                                                                case 'Dentist Postal Code': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Dentist Postal Code', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Dentist Postal Code" onchange="save_options();">Postal Code / Zip Code</label><?php break;
                                                                case 'Dentist City': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Dentist City', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Dentist City" onchange="save_options();">City / Town</label><?php break;
                                                                case 'Dentist County': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Dentist County', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Dentist County" onchange="save_options();">County</label><?php break;
                                                                case 'Dentist Province': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Dentist Province', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Dentist Province" onchange="save_options();">Province / State</label><?php break;
                                                                case 'Dentist Country': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Dentist Country', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Dentist Country" onchange="save_options();">Country</label><?php break;
                                                                case 'Dentist Support Documents': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Dentist Support Documents', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Dentist Support Documents" onchange="save_options();">Support Documents</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_specialist': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Specialist:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_specialist">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Specialists Type','Specialists First Name','Specialists Last Name','Specialists Relationship','Specialists Work Phone','Specialists Home Phone','Specialists Cell Phone','Specialists Fax','Specialists Address','Specialists Postal Code','Specialists City','Specialists County','Specialists Province','Specialists Country','Specialists Multiple'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Specialists Type': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Specialists Type', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Specialists Type" onchange="save_options();">Specialist Type</label><?php break;
                                                                case 'Specialists First Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Specialists First Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Specialists First Name" onchange="save_options();">First Name</label><?php break;
                                                                case 'Specialists Last Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Specialists Last Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Specialists Last Name" onchange="save_options();">Last Name</label><?php break;
                                                                case 'Specialists Relationship': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Specialists Relationship', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Specialists Relationship" onchange="save_options();">Relationship</label><?php break;
                                                                case 'Specialists Work Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Specialists Work Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Specialists Work Phone" onchange="save_options();">Work Phone</label><?php break;
                                                                case 'Specialists Home Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Specialists Home Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Specialists Home Phone" onchange="save_options();">Home Phone</label><?php break;
                                                                case 'Specialists Cell Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Specialists Cell Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Specialists Cell Phone" onchange="save_options();">Cell Phone</label><?php break;
                                                                case 'Specialists Fax': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Specialists Fax', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Specialists Fax" onchange="save_options();">Fax #</label><?php break;
                                                                case 'Specialists Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Specialists Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Specialists Address" onchange="save_options();">Specialists Address</label><?php break;
                                                                case 'Specialists Postal Code': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Specialists Postal Code', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Specialists Postal Code" onchange="save_options();">Postal Code / Zip Code</label><?php break;
                                                                case 'Specialists City': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Specialists City', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Specialists City" onchange="save_options();">City / Town</label><?php break;
                                                                case 'Specialists County': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Specialists County', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Specialists County" onchange="save_options();">County</label><?php break;
                                                                case 'Specialists Province': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Specialists Province', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Specialists Province" onchange="save_options();">Province / State</label><?php break;
                                                                case 'Specialists Country': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Specialists Country', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Specialists Country" onchange="save_options();">Country</label><?php break;
                                                                case 'Specialists Multiple': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Specialists Multiple', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Specialists Multiple" onchange="save_options();">Multiple Specialists</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_specialist_support_docs': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Specialist Support Documents:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_specialist_support_docs">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Specialists Support Documents'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Specialists Support Documents': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Specialists Support Documents', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Specialists Support Documents" onchange="save_options();">Support Documents</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_insurer': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Insurer:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_insurer">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Insurer','Insurer First Name','Insurer Last Name','Insurer Relationship','Insurer Work Phone','Insurer Home Phone','Insurer Cell Phone','Insurer Fax','Insurer Address','Insurer Postal Code','Insurer City','Insurer County','Insurer Province','Insurer Country','Insurer Support Documents'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Insurer': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurer', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurer" onchange="save_options();">Insurer</label><?php break;
                                                                case 'Insurer First Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurer First Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurer First Name" onchange="save_options();">First Name</label><?php break;
                                                                case 'Insurer Last Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurer Last Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurer Last Name" onchange="save_options();">Last Name</label><?php break;
                                                                case 'Insurer Relationship': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurer Relationship', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurer Relationship" onchange="save_options();">Relationship</label><?php break;
                                                                case 'Insurer Work Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurer Work Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurer Work Phone" onchange="save_options();">Work Phone</label><?php break;
                                                                case 'Insurer Home Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurer Home Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurer Home Phone" onchange="save_options();">Home Phone</label><?php break;
                                                                case 'Insurer Cell Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurer Cell Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurer Cell Phone" onchange="save_options();">Cell Phone</label><?php break;
                                                                case 'Insurer Fax': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurer Fax', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurer Fax" onchange="save_options();">Fax #</label><?php break;
                                                                case 'Insurer Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurer Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurer Address" onchange="save_options();">Insurer Address</label><?php break;
                                                                case 'Insurer Postal Code': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurer Postal Code', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurer Postal Code" onchange="save_options();">Postal Code / Zip Code</label><?php break;
                                                                case 'Insurer City': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurer City', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurer City" onchange="save_options();">City / Town</label><?php break;
                                                                case 'Insurer County': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurer County', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurer County" onchange="save_options();">County</label><?php break;
                                                                case 'Insurer Province': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurer Province', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurer Province" onchange="save_options();">Province / State</label><?php break;
                                                                case 'Insurer Country': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurer Country', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurer Country" onchange="save_options();">Country</label><?php break;
                                                                case 'Insurer Support Documents': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurer Support Documents', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurer Support Documents" onchange="save_options();">Support Documents</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_payment_information': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Payment &amp; Billing Information:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_payment_information">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Max KM','Max Pieces','Account Number','Payment Type','Payment Name','Payment Sync Address','Payment Address','Payment City Quadrant','Payment City','Payment State','Payment Country','Payment Postal Code','GST #','PST #','Vendor GST #','Payment Information','Condo Fees','Total Monthly Rate','Total Annual Rate','Pricing Level','Budget','Preferred Payment Info','Global Discount Type','Global Discount Value','Payment Frequency','Total Bill Amount'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Max KM': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Max KM', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Max KM" onchange="save_options();">Allowable KMs</label><?php break;
                                                                case 'Max Pieces': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Max Pieces', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Max Pieces" onchange="save_options();">Allowable Pieces</label><?php break;
                                                                case 'Account Number': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Account Number', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Account Number" onchange="save_options();">Account #</label><?php break;
                                                                case 'Payment Type': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Payment Type', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Payment Type" onchange="save_options();">Payment Type</label><?php break;
                                                                case 'Payment Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Payment Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Payment Name" onchange="save_options();">Payment Name</label><?php break;
                                                                case 'Payment Sync Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Payment Sync Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Payment Sync Address" onchange="save_options();">Same as Main Address</label><?php break;
                                                                case 'Payment Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Payment Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Payment Address" onchange="save_options();">Payment Address</label><?php break;
                                                                case 'Payment City Quadrant': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Payment City Quadrant', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Payment City Quadrant" onchange="save_options();">Payment City Quadrant</label><?php break;
                                                                case 'Payment City': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Payment City', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Payment City" onchange="save_options();">Payment City / Town</label><?php break;
                                                                case 'Payment State': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Payment State', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Payment State" onchange="save_options();">Payment Province / State</label><?php break;
                                                                case 'Payment Country': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Payment Country', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Payment Country" onchange="save_options();">Payment Country</label><?php break;
                                                                case 'Payment Postal Code': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Payment Postal Code', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Payment Postal Code" onchange="save_options();">Payment Postal Code / Zip Code</label><?php break;
                                                                case 'GST #': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('GST #', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="GST #" onchange="save_options();">GST #</label><?php break;
                                                                case 'PST #': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('PST #', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="PST #" onchange="save_options();">PST #</label><?php break;
                                                                case 'Vendor GST #': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Vendor GST #', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Vendor GST #" onchange="save_options();">Vendor GST #</label><?php break;
                                                                case 'Payment Information': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Payment Information', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Payment Information" onchange="save_options();">Payment Information</label><?php break;
                                                                case 'Condo Fees': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Condo Fees', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Condo Fees" onchange="save_options();">Condo Fees</label><?php break;
                                                                case 'Total Monthly Rate': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Total Monthly Rate', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Total Monthly Rate" onchange="save_options();">Total Monthly Rate</label><?php break;
                                                                case 'Total Annual Rate': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Total Annual Rate', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Total Annual Rate" onchange="save_options();">Total Annual Rate</label><?php break;
                                                                case 'Pricing Level': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Pricing Level', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Pricing Level" onchange="save_options();">Pricing Level</label><?php break;
                                                                case 'Budget': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Budget', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Budget" onchange="save_options();">Budget</label><?php break;
                                                                case 'Preferred Payment Info': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Preferred Payment Info', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Preferred Payment Info" onchange="save_options();">Preferred Payment Info</label><?php break;
                                                                case 'Global Discount Type': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Global Discount Type', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Global Discount Type" onchange="save_options();"> Global Discount Type</label><?php break;
                                                                case 'Global Discount Value': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Global Discount Value', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Global Discount Value" onchange="save_options();"> Global Discount Value</label><?php break;
                                                                case 'Payment Frequency': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Payment Frequency', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Payment Frequency" onchange="save_options();"> Frequency</label><?php break;
                                                                case 'Total Bill Amount': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Total Bill Amount', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Total Bill Amount" onchange="save_options();"> Total Rate</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_financial': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Financial:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_financial">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Void Cheque','Bank Name','Bank Institution Number','Bank Transit Number','Bank Account Number','EFT'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Void Cheque': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Void Cheque', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Void Cheque" onchange="save_options();">Void Cheque</label><?php break;
                                                                case 'Bank Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Bank Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Bank Name" onchange="save_options();">Bank Name</label><?php break;
                                                                case 'Bank Institution Number': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Bank Institution Number', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Bank Institution Number" onchange="save_options();">Bank Institution #</label><?php break;
                                                                case 'Bank Transit Number': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Bank Transit Number', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Bank Transit Number" onchange="save_options();">Bank Transit #</label><?php break;
                                                                case 'Bank Account Number': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Bank Account Number', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Bank Account Number" onchange="save_options();">Bank Account #</label><?php break;
                                                                case 'EFT': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('EFT', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="EFT" onchange="save_options();">EFT #</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_account_details': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Account Details:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_account_details">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Billable Hours','Billable Dollars','Hours Tracked','Hours Billed','Accounts Receivable/Credit on Account','Patient Accounts Receivable','Insurer Accounts Receivable for Patient','All Patient Invoices','All Insurer Invoices for Patient'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Billable Hours': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Billable Hours', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Billable Hours" onchange="save_options();">Total Billable Hours</label><?php break;
                                                                case 'Billable Dollars': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Billable Dollars', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Billable Dollars" onchange="save_options();">Total Billable Dollars</label><?php break;
                                                                case 'Hours Tracked': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Hours Tracked', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Hours Tracked" onchange="save_options();">Hours Tracked to Date</label><?php break;
                                                                case 'Hours Billed': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Hours Billed', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Hours Billed" onchange="save_options();">Dollars Billed to Date</label><?php break;
                                                                case 'Accounts Receivable/Credit on Account': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Accounts Receivable/Credit on Account', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Accounts Receivable/Credit on Account" onchange="save_options();">Accounts Receivable/Credit on Account</label><?php break;
                                                                case 'Patient Accounts Receivable': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Patient Accounts Receivable', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Patient Accounts Receivable" onchange="save_options();">Patient Accounts Receivable</label><?php break;
                                                                case 'Insurer Accounts Receivable for Patient': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurer Accounts Receivable for Patient', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurer Accounts Receivable for Patient" onchange="save_options();">Insurer Accounts Receivable for Patient</label><?php break;
                                                                case 'All Patient Invoices': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('All Patient Invoices', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="All Patient Invoices" onchange="save_options();">All Patient Invoices</label><?php break;
                                                                case 'All Insurer Invoices for Patient': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('All Insurer Invoices for Patient', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="All Insurer Invoices for Patient" onchange="save_options();">All Insurer Invoices for Patient</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_insurer_payment_information': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Insurer Payment Information:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_insurer_payment_information">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Insurer Payer','Insurer Plan'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Insurer Payer': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurer Payer', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurer Payer" onchange="save_options();">Insurer</label><?php break;
                                                                case 'Insurer Plan': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Insurer Plan', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Insurer Plan" onchange="save_options();">Plan / Account #</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_medical_information': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Medical Information:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_medical_information">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Medical Details Diagnosis','Medical Diagnosis Concerns','Medical Diagnosis Procedures','Diagnosis Support Documents','Medical Details Allergies','Medical Allergy Concerns','Medical Allergy Procedures','Allergies Support Documents','Medical Details Seizure','Medical Seizure Concerns','Medical Seizure Procedures','Medical Details Equipment','Medical Equipment Concerns','Medical Equipment Procedures','Equipment Support Documents','Medical Details Goals','Medical Goals Concerns','Medical Goals Procedures','Goals Support Documents','Medical Support Documents'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Medical Details Diagnosis': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Medical Details Diagnosis', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Medical Details Diagnosis" onchange="save_options();">Diagnosis Details</label><?php break;
                                                                case 'Medical Diagnosis Concerns': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Medical Diagnosis Concerns', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Medical Diagnosis Concerns" onchange="save_options();">Diagnosis Health Concerns</label><?php break;
                                                                case 'Medical Diagnosis Procedures': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Medical Diagnosis Procedures', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Medical Diagnosis Procedures" onchange="save_options();">Diagnosis Emergency Procedures</label><?php break;
                                                                case 'Diagnosis Support Documents': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Diagnosis Support Documents', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Diagnosis Support Documents" onchange="save_options();">Diagnosis Support Documents</label><?php break;
                                                                case 'Medical Details Allergies': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Medical Details Allergies', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Medical Details Allergies" onchange="save_options();">Allergy Details</label><?php break;
                                                                case 'Medical Allergy Concerns': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Medical Allergy Concerns', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Medical Allergy Concerns" onchange="save_options();">Allergy Health Concerns</label><?php break;
                                                                case 'Medical Allergy Procedures': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Medical Allergy Procedures', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Medical Allergy Procedures" onchange="save_options();">Allergy Emergency Procedures</label><?php break;
                                                                case 'Allergies Support Documents': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Allergies Support Documents', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Allergies Support Documents" onchange="save_options();">Allergy Support Documents</label><?php break;
                                                                case 'Medical Details Seizure': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Medical Details Seizure', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Medical Details Seizure" onchange="save_options();">Seizure Details</label><?php break;
                                                                case 'Medical Seizure Concerns': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Medical Seizure Concerns', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Medical Seizure Concerns" onchange="save_options();">Seizure Health Concerns</label><?php break;
                                                                case 'Medical Seizure Procedures': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Medical Seizure Procedures', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Medical Seizure Procedures" onchange="save_options();">Seizure Emergency Procedures</label><?php break;
                                                                case 'Medical Details Equipment': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Medical Details Equipment', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Medical Details Equipment" onchange="save_options();">Equipment Details</label><?php break;
                                                                case 'Medical Equipment Concerns': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Medical Equipment Concerns', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Medical Equipment Concerns" onchange="save_options();">Equipment Health Concerns</label><?php break;
                                                                case 'Medical Equipment Procedures': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Medical Equipment Procedures', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Medical Equipment Procedures" onchange="save_options();">Equipment Emergency Procedures</label><?php break;
                                                                case 'Equipment Support Documents': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Equipment Support Documents', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Equipment Support Documents" onchange="save_options();">Equipment Support Documents</label><?php break;
                                                                case 'Medical Details Goals': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Medical Details Goals', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Medical Details Goals" onchange="save_options();">Goals of Care</label><?php break;
                                                                case 'Medical Goals Concerns': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Medical Goals Concerns', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Medical Goals Concerns" onchange="save_options();">Goal Health Concerns</label><?php break;
                                                                case 'Medical Goals Procedures': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Medical Goals Procedures', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Medical Goals Procedures" onchange="save_options();">Goal Emergency Procedures</label><?php break;
                                                                case 'Goals Support Documents': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Goals Support Documents', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Goals Support Documents" onchange="save_options();">Goal Support Documents</label><?php break;
                                                                case 'Medical Support Documents': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Medical Support Documents', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Medical Support Documents" onchange="save_options();">Medical Support Documents</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_projects': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Projects:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_projects">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Estimates','Deposit','Damage Deposit','Quote Description'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Estimates': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Estimates', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Estimates" onchange="save_options();"><?= ESTIMATE_TILE ?></label><?php break;
                                                                case 'Deposit': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Deposit', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Deposit" onchange="save_options();">Deposit</label><?php break;
                                                                case 'Damage Deposit': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Damage Deposit', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Damage Deposit" onchange="save_options();">Damage Deposit</label><?php break;
                                                                case 'Quote Description': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Quote Description', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Quote Description" onchange="save_options();">Quote Description</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_transportation': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Transportation:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_transportation">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Transportation Mode of Transportation','Transportation Transit Access','Transportation Access Password','Transportation Drivers License','Drivers License Class','Drive Manual Transmission','Transportation Drivers Glasses','Transportation Upload License','Transportation Support Documents'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Transportation Mode of Transportation': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Transportation Mode of Transportation', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Transportation Mode of Transportation" onchange="save_options();">Mode of Transportation</label><?php break;
                                                                case 'Transportation Transit Access': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Transportation Transit Access', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Transportation Transit Access" onchange="save_options();">Transit Access</label><?php break;
                                                                case 'Transportation Access Password': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Transportation Access Password', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Transportation Access Password" onchange="save_options();">Access Password</label><?php break;
                                                                case 'Transportation Drivers License': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Transportation Drivers License', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Transportation Drivers License" onchange="save_options();">Driver's Licence</label><?php break;
                                                                case 'Drivers License Class': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Drivers License Class', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Drivers License Class" onchange="save_options();">Driver's Licence Class</label><?php break;
                                                                case 'Drive Manual Transmission': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Drive Manual Transmission', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Drive Manual Transmission" onchange="save_options();">Manual Transmission</label><?php break;
                                                                case 'Transportation Drivers Glasses': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Transportation Drivers Glasses', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Transportation Drivers Glasses" onchange="save_options();">Glasses</label><?php break;
                                                                case 'Transportation Upload License': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Transportation Upload License', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Transportation Upload License" onchange="save_options();">Licence Upload</label><?php break;
                                                                case 'Transportation Support Documents': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Transportation Support Documents', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Transportation Support Documents" onchange="save_options();">Support Documents</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_vehicle_description': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Vehicle Description:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_vehicle_description">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['License Plate #','Upload License Plate','CARFAX'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'License Plate #': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('License Plate #', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="License Plate #" onchange="save_options();">Licence Plate #</label><?php break;
                                                                case 'Upload License Plate': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Upload License Plate', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Upload License Plate" onchange="save_options();">Upload Licence Plate</label><?php break;
                                                                case 'CARFAX': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('CARFAX', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="CARFAX" onchange="save_options();">CARFAX</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_protocols_details': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Protocols Details:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_protocols_details">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Seizure Protocol Details','Seizure Protocol Upload','Slip Fall Protocol Details','Slip Fall Protocol Upload','Transfer Protocol Details','Transfer Protocol Upload','Toileting Protocol Details','Toileting Protocol Upload','Bathing Protocol Details','Bathing Protocol Upload','G-Tube Protocol Details','G-Tube Protocol Upload','Food Preferences','Oxygen Protocol Details','Oxygen Protocol Upload','First Aid CPR Details','SRC Details','SRC Upload'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Seizure Protocol Details': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Seizure Protocol Details', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Seizure Protocol Details" onchange="save_options();">Seizure Protocol Details</label><?php break;
                                                                case 'Seizure Protocol Upload': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Seizure Protocol Upload', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Seizure Protocol Upload" onchange="save_options();">Seizure Protocol Upload</label><?php break;
                                                                case 'Slip Fall Protocol Details': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Slip Fall Protocol Details', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Slip Fall Protocol Details" onchange="save_options();">Slip &amp; Fall Protocol Details</label><?php break;
                                                                case 'Slip Fall Protocol Upload': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Slip Fall Protocol Upload', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Slip Fall Protocol Upload" onchange="save_options();">Slip &amp; Fall Protocol Upload</label><?php break;
                                                                case 'Transfer Protocol Details': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Transfer Protocol Details', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Transfer Protocol Details" onchange="save_options();">Transfer Protocol Details</label><?php break;
                                                                case 'Transfer Protocol Upload': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Transfer Protocol Upload', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Transfer Protocol Upload" onchange="save_options();">Transfer Protocol Upload</label><?php break;
                                                                case 'Toileting Protocol Details': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Toileting Protocol Details', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Toileting Protocol Details" onchange="save_options();">Toileting Protocol Details</label><?php break;
                                                                case 'Toileting Protocol Upload': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Toileting Protocol Upload', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Toileting Protocol Upload" onchange="save_options();">Toileting Protocol Upload</label><?php break;
                                                                case 'Bathing Protocol Details': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Bathing Protocol Details', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Bathing Protocol Details" onchange="save_options();">Bathing Protocol Details</label><?php break;
                                                                case 'Bathing Protocol Upload': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Bathing Protocol Upload', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Bathing Protocol Upload" onchange="save_options();">Bathing Protocol Upload</label><?php break;
                                                                case 'G-Tube Protocol Details': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('G-Tube Protocol Details', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="G-Tube Protocol Details" onchange="save_options();">G-Tube Protocol Details</label><?php break;
                                                                case 'G-Tube Protocol Upload': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('G-Tube Protocol Upload', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="G-Tube Protocol Upload" onchange="save_options();">G-Tube Protocol Upload</label><?php break;
                                                                case 'Food Preferences': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Food Preferences', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Food Preferences" onchange="save_options();">Food Preferences</label><?php break;
                                                                case 'Oxygen Protocol Details': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Oxygen Protocol Details', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Oxygen Protocol Details" onchange="save_options();">Oxygen Protocol Details</label><?php break;
                                                                case 'Oxygen Protocol Upload': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Oxygen Protocol Upload', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Oxygen Protocol Upload" onchange="save_options();">Oxygen Protocol Upload</label><?php break;
                                                                case 'First Aid CPR Details': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('First Aid CPR Details', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="First Aid CPR Details" onchange="save_options();">First Aid/CPR Details</label><?php break;
                                                                case 'SRC Details': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('SRC Details', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="SRC Details" onchange="save_options();">SRC</label><?php break;
                                                                case 'SRC Upload': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('SRC Upload', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="SRC Upload" onchange="save_options();">SRC Upload</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_day_program': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Day Program:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_day_program">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Day Program Name','Day Program Address','Day Program Phone','Day Program Key Worker'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Day Program Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Day Program Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Day Program Name" onchange="save_options();">Name</label><?php break;
                                                                case 'Day Program Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Day Program Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Day Program Address" onchange="save_options();">Address</label><?php break;
                                                                case 'Day Program Phone': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Day Program Phone', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Day Program Phone" onchange="save_options();">Phone</label><?php break;
                                                                case 'Day Program Key Worker': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Day Program Key Worker', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Day Program Key Worker" onchange="save_options();">Key Worker</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_protocol_log_notes': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Protocol Log Notes:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_protocol_log_notes">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Protocols Daily Log Notes','Protocols Completed Date','Protocols Start Time','Protocols End Time','Protocols Completed By','Protocols Signature Box','Protocols Management Comments','Protocols Management Completed Date','Protocols Management Completed By','Protocols Management Signature Box'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Protocols Daily Log Notes': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Protocols Daily Log Notes', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Protocols Daily Log Notes" onchange="save_options();">Protocol Log Notes</label><?php break;
                                                                case 'Protocols Completed Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Protocols Completed Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Protocols Completed Date" onchange="save_options();">Protocols Completed Date</label><?php break;
                                                                case 'Protocols Start Time': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Protocols Start Time', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Protocols Start Time" onchange="save_options();">Protocols Start Time</label><?php break;
                                                                case 'Protocols End Time': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Protocols End Time', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Protocols End Time" onchange="save_options();">Protocols End Time</label><?php break;
                                                                case 'Protocols Completed By': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Protocols Completed By', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Protocols Completed By" onchange="save_options();">Protocols Completed By</label><?php break;
                                                                case 'Protocols Signature Box': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Protocols Signature Box', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Protocols Signature Box" onchange="save_options();">Protocols Signature Box</label><?php break;
                                                                case 'Protocols Management Comments': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Protocols Management Comments', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Protocols Management Comments" onchange="save_options();">Management Comments</label><?php break;
                                                                case 'Protocols Management Completed Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Protocols Management Completed Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Protocols Management Completed Date" onchange="save_options();">Management Completed Date</label><?php break;
                                                                case 'Protocols Management Completed By': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Protocols Management Completed By', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Protocols Management Completed By" onchange="save_options();">Management Completed By</label><?php break;
                                                                case 'Protocols Management Signature Box': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Protocols Management Signature Box', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Protocols Management Signature Box" onchange="save_options();">Management Signature Box</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_routine_log_notes': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Routine Log Notes:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_routine_log_notes">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Routines Daily Log Notes','Routines Completed Date','Routines Start Time','Routines End Time','Routines Completed By','Routines Signature Box','Routines Management Comments','Routines Management Completed Date','Routines Management Completed By','Routines Management Signature Box'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Routines Daily Log Notes': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Routines Daily Log Notes', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Routines Daily Log Notes" onchange="save_options();">Routine Log Notes</label><?php break;
                                                                case 'Routines Completed Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Routines Completed Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Routines Completed Date" onchange="save_options();">Routines Completed Date</label><?php break;
                                                                case 'Routines Start Time': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Routines Start Time', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Routines Start Time" onchange="save_options();">Routines Start Time</label><?php break;
                                                                case 'Routines End Time': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Routines End Time', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Routines End Time" onchange="save_options();">Routines End Time</label><?php break;
                                                                case 'Routines Completed By': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Routines Completed By', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Routines Completed By" onchange="save_options();">Routines Completed By</label><?php break;
                                                                case 'Routines Signature Box': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Routines Signature Box', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Routines Signature Box" onchange="save_options();">Routines Signature Box</label><?php break;
                                                                case 'Routines Management Comments': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Routines Management Comments', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Routines Management Comments" onchange="save_options();">Management Comments</label><?php break;
                                                                case 'Routines Management Completed Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Routines Management Completed Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Routines Management Completed Date" onchange="save_options();">Management Completed Date</label><?php break;
                                                                case 'Routines Management Completed By': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Routines Management Completed By', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Routines Management Completed By" onchange="save_options();">Management Completed By</label><?php break;
                                                                case 'Routines Management Signature Box': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Routines Management Signature Box', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Routines Management Signature Box" onchange="save_options();">Management Signature Box</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_communication_log_notes': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Communication Log Notes:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_communication_log_notes">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Communication Daily Log Notes','Communication Completed Date','Communication Start Time','Communication End Time','Communication Completed By','Communication Signature Box','Communication Management Comments','Communication Management Completed Date','Communication Management Completed By','Communication Management Signature Box'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Communication Daily Log Notes': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Communication Daily Log Notes', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Communication Daily Log Notes" onchange="save_options();">Communication Log Notes</label><?php break;
                                                                case 'Communication Completed Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Communication Completed Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Communication Completed Date" onchange="save_options();">Communication Completed Date</label><?php break;
                                                                case 'Communication Start Time': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Communication Start Time', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Communication Start Time" onchange="save_options();">Communication Start Time</label><?php break;
                                                                case 'Communication End Time': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Communication End Time', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Communication End Time" onchange="save_options();">Communication End Time</label><?php break;
                                                                case 'Communication Completed By': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Communication Completed By', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Communication Completed By" onchange="save_options();">Communication Completed By</label><?php break;
                                                                case 'Communication Signature Box': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Communication Signature Box', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Communication Signature Box" onchange="save_options();">Communication Signature Box</label><?php break;
                                                                case 'Communication Management Comments': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Communication Management Comments', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Communication Management Comments" onchange="save_options();">Management Comments</label><?php break;
                                                                case 'Communication Management Completed Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Communication Management Completed Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Communication Management Completed Date" onchange="save_options();">Management Completed Date</label><?php break;
                                                                case 'Communication Management Completed By': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Communication Management Completed By', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Communication Management Completed By" onchange="save_options();">Management Completed By</label><?php break;
                                                                case 'Communication Management Signature Box': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Communication Management Signature Box', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Communication Management Signature Box" onchange="save_options();">Management Signature Box</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_daily_log_notes_activities_details': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Daily Log Notes Activities Details:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_daily_log_notes_activities_details">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Activities Daily Log Notes','Activities Completed Date','Activities Start Time','Activities End Time','Activities Completed By','Activities Signature Box','Activities Management Comments','Activities Management Completed Date','Activities Management Completed By','Activities Management Signature Box'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Activities Daily Log Notes': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Activities Daily Log Notes', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Activities Daily Log Notes" onchange="save_options();">Activities Log Notes</label><?php break;
                                                                case 'Activities Completed Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Activities Completed Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Activities Completed Date" onchange="save_options();">Activities Completed Date</label><?php break;
                                                                case 'Activities Start Time': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Activities Start Time', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Activities Start Time" onchange="save_options();">Activities Start Time</label><?php break;
                                                                case 'Activities End Time': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Activities End Time', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Activities End Time" onchange="save_options();">Activities End Time</label><?php break;
                                                                case 'Activities Completed By': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Activities Completed By', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Activities Completed By" onchange="save_options();">Activities Completed By</label><?php break;
                                                                case 'Activities Signature Box': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Activities Signature Box', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Activities Signature Box" onchange="save_options();">Activities Signature Box</label><?php break;
                                                                case 'Activities Management Comments': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Activities Management Comments', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Activities Management Comments" onchange="save_options();">Management Comments</label><?php break;
                                                                case 'Activities Management Completed Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Activities Management Completed Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Activities Management Completed Date" onchange="save_options();">Management Completed Date</label><?php break;
                                                                case 'Activities Management Completed By': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Activities Management Completed By', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Activities Management Completed By" onchange="save_options();">Management Completed By</label><?php break;
                                                                case 'Activities Management Signature Box': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Activities Management Signature Box', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Activities Management Signature Box" onchange="save_options();">Management Signature Box</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_new_hire_package': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">New Hire Package:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_new_hire_package">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Upload Application','Start Date','Expiry Date','Renewal Date'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Upload Application': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Upload Application', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Upload Application" onchange="save_options();">Upload Application</label><?php break;
                                                                case 'Start Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Start Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Start Date" onchange="save_options();">Start Date</label><?php break;
                                                                case 'FIELD': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Expiry Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Expiry Date" onchange="save_options();">Expiry Date</label><?php break;
                                                                case 'Expiry Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Renewal Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Renewal Date" onchange="save_options();">Renewal Date</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_orientation': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Orientation:</label>
                                                <div class="col-sm-8"><label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_orientation">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Orientation Email Address'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Orientation Email Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Orientation Email Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Orientation Email Address" onchange="save_options();">Email Address</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_human_resources': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Human Resources:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_human_resources">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['HR'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'HR': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('HR', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="HR" onchange="save_options();">Human Resources</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_software_login': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Software Login:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_software_login">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Role','User Name','Password','Auto-Generate Using Email','Email Credentials','Software URL'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Role': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Role', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Role" onchange="save_options();">Security Level</label><?php break;
                                                                case 'User Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('User Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="User Name" onchange="save_options();">Username</label><?php break;
                                                                case 'Password': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Password', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Password" onchange="save_options();">Password</label><?php break;
                                                                case 'Auto-Generate Using Email': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Auto-Generate Using Email', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Auto-Generate Using Email" onchange="save_options();">Auto-Generate Using Email Button</label><?php break;
                                                                case 'Email Credentials': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Email Credentials', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Email Credentials" onchange="save_options();">Email Credentials Button</label><?php break;
                                                                case 'Software URL': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Software URL', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Software URL" onchange="save_options();">Software URL</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_security_access': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Security Access:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_security_access">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Region Access','Location Access','Classification Access','Equipment Access','Dispatch Staff Access','Dispatch Team Access'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Region Access': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Region Access', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Region Access" onchange="save_options();">Region Access</label><?php break;
                                                                case 'Location Access': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Location Access', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Location Access" onchange="save_options();">Location Access</label><?php break;
                                                                case 'Classification Access': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Classification Access', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Classification Access" onchange="save_options();">Classification Access</label><?php break;
                                                                case 'Equipment Access': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Equipment Access', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Equipment Access" onchange="save_options();">Equipment Access</label><?php break;
                                                                case 'Dispatch Staff Access': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Dispatch Staff Access', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Dispatch Staff Access" onchange="save_options();">Dispatch Calendar Staff Access</label><?php break;
                                                                case 'Dispatch Team Access': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Dispatch Team Access', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Dispatch Team Access" onchange="save_options();">Dispatch Calendar Team Access</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_property_description': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Property Description:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_property_description">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Property Size','Property Type','Property Information','Upload Property Information','Property Instructions','Unit #','Condo Fees Property','Base Rent','Base Rent/Sq. Ft.','CAC','CAC/Sq. Ft.','Property Tax','Property Tax/Sq. Ft.','Upload Inspection','Bay #','Location Square Footage','Location Num Bathrooms','Location Alarm','Location Pets'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Property Size': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Property Size', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Property Size" onchange="save_options();">Property Size (Service Category)</label><?php break;
                                                                case 'Property Type': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Property Type', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Property Type" onchange="save_options(); if($(this).is(':checked')) { $('.property_types_div').show(); } else { $('.property_types_div').hide(); }">Property Type</label><?php break;
                                                                case 'Property Information': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Property Information', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Property Information" onchange="save_options();">Property Information</label><?php break;
                                                                case 'Property Instructions': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Property Instructions', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Property Instructions" onchange="save_options();">Property Instructions</label><?php break;
                                                                case 'Upload Property Information': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Upload Property Information', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Upload Property Information" onchange="save_options();">Upload Property Information</label><?php break;
                                                                case 'Unit #': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Unit #', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Unit #" onchange="save_options();">Unit #</label><?php break;
                                                                case 'Condo Fees Property': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Condo Fees Property', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Condo Fees Property" onchange="save_options();">Condo Fees</label><?php break;
                                                                case 'Base Rent': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Base Rent', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Base Rent" onchange="save_options();">Base Rent</label><?php break;
                                                                case 'Base Rent/Sq. Ft.': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Base Rent/Sq. Ft.', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Base Rent/Sq. Ft." onchange="save_options();">Base Rent/Sq. Ft.</label><?php break;
                                                                case 'CAC': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('CAC', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="CAC" onchange="save_options();">CAC</label><?php break;
                                                                case 'CAC/Sq. Ft.': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('CAC/Sq. Ft.', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="CAC/Sq. Ft." onchange="save_options();">CAC/Sq. Ft.</label><?php break;
                                                                case 'Property Tax': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Property Tax', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Property Tax" onchange="save_options();">Property Tax</label><?php break;
                                                                case 'Property Tax/Sq. Ft.': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Property Tax/Sq. Ft.', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Property Tax/Sq. Ft." onchange="save_options();">Property Tax/Sq. Ft.</label><?php break;
                                                                case 'Upload Inspection': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Upload Inspection', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Upload Inspection" onchange="save_options();">Upload Inspection</label><?php break;
                                                                case 'Bay #': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Bay #', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Bay #" onchange="save_options();">Bay #</label><?php break;
                                                                case 'Location Square Footage': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Location Square Footage', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Location Square Footage" onchange="save_options();">Square Footage</label><?php break;
                                                                case 'Location Num Bathrooms': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Location Num Bathrooms', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Location Num Bathrooms" onchange="save_options();">Number of Bathrooms</label><?php break;
                                                                case 'Location Alarm': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Location Alarm', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Location Alarm" onchange="save_options();">Alarm System Information</label><?php break;
                                                                case 'Location Pets': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Location Pets', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Location Pets" onchange="save_options();">Pets</label><?php break;
                                                            }
                                                        } ?>
                                                        <div class="property_types_div" <?= !in_array('Property Type', $field_config) ? 'style="display:none;"' : '' ?>>
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
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_contract">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Upload Letter of Intent','Upload Vendor Documents','Upload Marketing Material','Upload Purchase Contract','Upload Support Contract','Upload Support Terms','Upload Rental Contract','Upload Management Contract','Upload Articles of Incorporation','Option to Renew','Contract Allocated Hours','Contract Allocated Hours Multiple Types','Contract Total Value','Contract Start Date','Contract End Date'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Upload Letter of Intent': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Upload Letter of Intent', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Upload Letter of Intent" onchange="save_options();">Upload Letter of Intent</label><?php break;
                                                                case 'Upload Vendor Documents': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Upload Vendor Documents', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Upload Vendor Documents" onchange="save_options();">Upload Vendor Documents</label><?php break;
                                                                case 'Upload Marketing Material': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Upload Marketing Material', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Upload Marketing Material" onchange="save_options();">Upload Marketing Material</label><?php break;
                                                                case 'Upload Purchase Contract': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Upload Purchase Contract', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Upload Purchase Contract" onchange="save_options();">Upload Purchase Contract</label><?php break;
                                                                case 'Upload Support Contract': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Upload Support Contract', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Upload Support Contract" onchange="save_options();">Upload Support Contract</label><?php break;
                                                                case 'Upload Support Terms': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Upload Support Terms', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Upload Support Terms" onchange="save_options();">Upload Support Terms</label><?php break;
                                                                case 'Upload Rental Contract': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Upload Rental Contract', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Upload Rental Contract" onchange="save_options();">Upload Rental Contract</label><?php break;
                                                                case 'Upload Management Contract': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Upload Management Contract', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Upload Management Contract" onchange="save_options();">Upload Management Contract</label><?php break;
                                                                case 'Upload Articles of Incorporation': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Upload Articles of Incorporation', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Upload Articles of Incorporation" onchange="save_options();">Upload Articles of Incorporation</label><?php break;
                                                                case 'Option to Renew': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Option to Renew', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Option to Renew" onchange="save_options();">Option to Renew</label><?php break;
                                                                case 'Contract Allocated Hours': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Allocated Hours', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Allocated Hours" onchange="save_options();">Total Allocated Hours</label><?php break;
                                                                case 'Contract Allocated Hours Multiple Types': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Allocated Hours Multiple Types', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Allocated Hours Multiple Types" onchange="save_options(); if($(this).is(':checked')) { $('.allocated_hours_types_div').show(); } else { $('.allocated_hours_types_div').hide(); }">Multiple Total Allocated Hours With Types</label><?php break;
                                                                case 'Contract Total Value': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Total Value', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Total Value" onchange="save_options();">Total Dollar Value of Contract</label><?php break;
                                                                case 'Contract Start Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Start Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Start Date" onchange="save_options();">Contract Start Date</label><?php break;
                                                                case 'Contract End Date': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract End Date', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract End Date" onchange="save_options();">Contract End Date</label><?php break;
                                                            }
                                                        } ?>
                                                        <div class="allocated_hours_types_div" <?= !in_array('Contract Allocated Hours Multiple Types', $field_config) ? 'style="display:none;"' : '' ?>>
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
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_contract_workers">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Contract Worker Sheet','Contract Worker List','Contract Worker Abstract','Contract Worker Licences','Contract Worker Criminal Record','Contract Worker Criminal Record Auth','Contract Worker Bank Info','Contract Worker Business Registration','Contract Workers Reminders'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Contract Worker Sheet': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Worker Sheet', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Worker Sheet" onchange="save_options();">Contact Sheet</label><?php break;
                                                                case 'Contract Worker List': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Worker List', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Worker List" onchange="save_options();">List of Workers</label><?php break;
                                                                case 'Contract Worker Abstract': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Worker Abstract', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Worker Abstract" onchange="save_options();">Abstracts</label><?php break;
                                                                case 'Contract Worker Licences': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Worker Licences', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Worker Licences" onchange="save_options();">Licences</label><?php break;
                                                                case 'Contract Worker Criminal Record': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Worker Criminal Record', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Worker Criminal Record" onchange="save_options();">Criminal Record Check</label><?php break;
                                                                case 'Contract Worker Criminal Record Auth': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Worker Criminal Record Auth', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Worker Criminal Record Auth" onchange="save_options();">Criminal Record Check Authorization</label><?php break;
                                                                case 'Contract Worker Bank Info': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Worker Bank Info', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Worker Bank Info" onchange="save_options();">Bank Information</label><?php break;
                                                                case 'Contract Worker Business Registration': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Worker Business Registration', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Worker Business Registration" onchange="save_options();">Proof of Business Registration / Incorporation</label><?php break;
                                                                case 'Contract Workers Reminders': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Workers Reminders', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Workers Reminders" onchange="save_options();">Contract Workers Reminder</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_contract_policies': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Contract Policies:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_contract_policies">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Contract Policies Agreement','Contract Policies Non Compete','Contract Policies Non Solicitation','Contract Policies Confidentiality','Contract Policies Uniforms','Contract Policies Leasing','Contract Policies Fuel Card','Contract Policies Reminders'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Contract Policies Agreement': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Policies Agreement', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Policies Agreement" onchange="save_options();">Contractor Agreement</label><?php break;
                                                                case 'Contract Policies Non Compete': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Policies Non Compete', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Policies Non Compete" onchange="save_options();">Non-Compete Policy</label><?php break;
                                                                case 'Contract Policies Non Solicitation': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Policies Non Solicitation', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Policies Non Solicitation" onchange="save_options();">Non-Solicitation Policy</label><?php break;
                                                                case 'Contract Policies Confidentiality': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Policies Confidentiality', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Policies Confidentiality" onchange="save_options();">Confidentiality Policy</label><?php break;
                                                                case 'Contract Policies Uniforms': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Policies Uniforms', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Policies Uniforms" onchange="save_options();">Uniform Policy</label><?php break;
                                                                case 'Contract Policies Leasing': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Policies Leasing', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Policies Leasing" onchange="save_options();">Lease Agreements</label><?php break;
                                                                case 'Contract Policies Fuel Card': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Policies Fuel Card', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Policies Fuel Card" onchange="save_options();">Fuel Card Agreement</label><?php break;
                                                                case 'Contract Policies Reminders': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Policies Reminders', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Policies Reminders" onchange="save_options();">Policy Reminder</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_contract_wcb': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Contract WCB:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_contract_wcb">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Contract WCB Clearance','Contract WCB Good Standing','Contract WCB Insurance','Contract WCB Reminders'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Contract WCB Clearance': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract WCB Clearance', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract WCB Clearance" onchange="save_options();">WCB Clearance Letter</label><?php break;
                                                                case 'Contract WCB Good Standing': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract WCB Good Standing', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract WCB Good Standing" onchange="save_options();">WCB in Good Standing</label><?php break;
                                                                case 'Contract WCB Insurance': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract WCB Insurance', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract WCB Insurance" onchange="save_options();">Copy of Valid Insurance</label><?php break;
                                                                case 'Contract WCB Reminders': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract WCB Reminders', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract WCB Reminders" onchange="save_options();">WCB Reminder</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_contract_rates': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Contract Rates:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_contract_rates">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Contract Rates Signed','Contract Rates Reminders'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Contract Rates Signed': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Rates Signed', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Rates Signed" onchange="save_options();">Signed Rate Sheet</label><?php break;
                                                                case 'Contract Rates Reminders': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Rates Reminders', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Rates Reminders" onchange="save_options();">Rate Sheet Reminder</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_contract_vehicles': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Contract Vehicles:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_contract_vehicles">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Contract Vehicles Make','Contract Vehicles Licence Plate','Contract Vehicles Registration','Contract Vehicles Reminders'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Contract Vehicles Make': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Vehicles Make', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Vehicles Make" onchange="save_options();">Make / Model</label><?php break;
                                                                case 'Contract Vehicles Licence Plate': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Vehicles Licence Plate', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Vehicles Licence Plate" onchange="save_options();">Licence Plate #</label><?php break;
                                                                case 'Contract Vehicles Registration': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Vehicles Registration', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Vehicles Registration" onchange="save_options();">Registration</label><?php break;
                                                                case 'Contract Vehicles Reminders': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Contract Vehicles Reminders', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Contract Vehicles Reminders" onchange="save_options();">Vehicles Reminder</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_reminders': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Reminders:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_reminders">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Reminders'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Reminders': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Reminders', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Reminders" onchange="save_options();">Reminders</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_site_information': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Site Information:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_site_information">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Customer(Client/Customer/Business)','Attached Contact','Site Name (Location)','Display Name','Business Sites','Site LSD','Site Bottom Hole','Site Alias','Site Website'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Customer(Client/Customer/Business)': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Customer(Client/Customer/Business)', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Customer(Client/Customer/Business)" onchange="save_options();">Customer (Client / Customer / Business)</label><?php break;
                                                                case 'Attached Contact': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Attached Contact', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Attached Contact" onchange="save_options();">Attached Contact</label><?php break;
                                                                case 'Site Name (Location)': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Site Name (Location)', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Site Name (Location)" onchange="save_options();">Site Name (Location)</label><?php break;
                                                                case 'Display Name': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Display Name', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Display Name" onchange="save_options();">Display Name</label><?php break;
                                                                case 'Business Sites': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Business Sites', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Business Sites" onchange="save_options();">Business Sites</label><?php break;
                                                                case 'Site LSD': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Site LSD', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Site LSD" onchange="save_options();">Site LSD</label><?php break;
                                                                case 'Site Bottom Hole': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Site Bottom Hole', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Site Bottom Hole" onchange="save_options();">Bottom Hole (UWI)</label><?php break;
                                                                case 'Site Alias': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Site Alias', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Site Alias" onchange="save_options();">Alias</label><?php break;
                                                                case 'Site Website': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Site Website', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Site Website" onchange="save_options();">Website</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_emergency_plan': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Emergency Plan:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_emergency_plan">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Emergency Police','Emergency Poison','Emergency Non','Emergency Contact','Emergency Notes'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Emergency Police': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Police', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Police" onchange="save_options();">Police Contact</label><?php break;
                                                                case 'Emergency Poison': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Poison', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Poison" onchange="save_options();">Poison Control</label><?php break;
                                                                case 'Emergency Non': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Non', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Non" onchange="save_options();">Non-Emergency Contact</label><?php break;
                                                                case 'Emergency Contact': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Contact', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Contact" onchange="save_options();">Emergency Contact</label><?php break;
                                                                case 'Emergency Notes': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Emergency Notes', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Emergency Notes" onchange="save_options();">Notes</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_strategies': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Strategies:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_strategies">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Strategies Levels of Communication','Strategies Types of Supports','Strategies Likes','Strategies Dislikes','Strategies Required Accommodations'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Strategies Levels of Communication': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Strategies Levels of Communication', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Strategies Levels of Communication" onchange="save_options();">Levels of Communication</label><?php break;
                                                                case 'Strategies Types of Supports': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Strategies Types of Supports', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Strategies Types of Supports" onchange="save_options();">Types of Supports</label><?php break;
                                                                case 'Strategies Likes': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Strategies Likes', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Strategies Likes" onchange="save_options();">Likes</label><?php break;
                                                                case 'Strategies Dislikes': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Strategies Dislikes', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Strategies Dislikes" onchange="save_options();">Dislikes</label><?php break;
                                                                case 'Strategies Required Accommodations': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Strategies Required Accommodations', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Strategies Required Accommodations" onchange="save_options();">Required Accommodations</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_alerts': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Alerts:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_alerts">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Alert Staff','Alert Sending Email Address','Alert Email Subject','Alert Email Body'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Alert Staff': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Alert Staff', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Alert Staff" onchange="save_options();">Staff</label><?php break;
                                                                case 'Alert Sending Email Address': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Alert Sending Email Address', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Alert Sending Email Address" onchange="save_options();">Sending Email Address</label><?php break;
                                                                case 'Alert Email Subject': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Alert Email Subject', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Alert Email Subject" onchange="save_options();">Email Subject</label><?php break;
                                                                case 'Alert Email Body': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Alert Email Body', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Alert Email Body" onchange="save_options();">Email Body</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_notifications': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Notifications:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_notifications">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Notification Type'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Notification Type': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Notification Type', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Notification Type" onchange="save_options();">Notification Type</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_wcb_information': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">WCB:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_wcb_information">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['WCB Claim Number','WCB Date of Accident','WCB Add Multiple'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'WCB Claim Number': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('WCB Claim Number', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="WCB Claim Number" onchange="save_options();">WCB Claim Number</label><?php break;
                                                                case 'WCB Date of Accident': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('WCB Date of Accident', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="WCB Date of Accident" onchange="save_options();">Date of Accident</label><?php break;
                                                                case 'WCB Add Multiple': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('WCB Add Multiple', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="WCB Add Multiple" onchange="save_options();">Add Multiple</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_booking_information': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Booking Information:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_booking_information">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Preferred Booking Time','Booking Extra'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Preferred Booking Time': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Preferred Booking Time', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Preferred Booking Time" onchange="save_options();">Preferred Booking Time</label><?php break;
                                                                case 'Booking Extra': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Booking Extra', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Booking Extra" onchange="save_options();">Extra Information</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_driver': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Driver:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_driver">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Name of Drivers License','Drivers License Number','Drivers License','Drivers Abstract'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Name of Drivers License': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Name of Drivers License', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Name of Drivers License" onchange="save_options();">Name of Drivers License</label><?php break;
                                                                case 'Drivers License Number': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Drivers License Number', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Drivers License Number" onchange="save_options();">Drivers License #</label><?php break;
                                                                case 'Drivers License': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Drivers License', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Drivers License" onchange="save_options();">Drivers License</label><?php break;
                                                                case 'Drivers Abstract': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Drivers Abstract', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Drivers Abstract" onchange="save_options();">Drivers Abstract</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_cor_fields': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">COR:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_driver">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['COR Certified','COR Number'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'COR Certified': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('COR Certified', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="COR Certified" onchange="save_options();">COR Certified</label><?php break;
                                                                case 'COR Number': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('COR Number', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="COR Number" onchange="save_options();">COR Number</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_calendar_settings': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Calendar Settings:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_calendar">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Calendar Color'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Calendar Color': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Calendar Color', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Calendar Color" onchange="save_options();">Calendar Color</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'acc_subtab_config': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Subtab Configuration:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_subtab_config">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Visibility Options'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Visibility Options': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Visibility Options', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Visibility Options" onchange="save_options();">Visibility Options</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;



                                        case 'acc_upcoming_appointments_addition': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Upcoming Appointments:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_upcoming_appointments_addition">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Upcoming Appointments'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Upcoming Appointments': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Upcoming Appointments', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Upcoming Appointments" onchange="save_options();">Upcoming Appointments</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;


                                        case 'acc_ticket_tile_notes': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label"><?php echo TICKET_NOUN.' Notes'; ?>:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_ticket_tile_notes">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Session Notes'])) as $field_option) {
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
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_match_tile">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Match'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Match': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Match', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Match" onchange="save_options();">Match</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;

                                        case 'acc_intake_tile': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Intake Forms:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_intake_tile">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['Intake Forms'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'Intake Forms': ?><label class="form-checkbox"><input type="checkbox" <?= in_array('Intake Forms', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Intake Forms" onchange="save_options();">Intake Forms</label><?php break;
                                                            }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php break;






                                        case 'acc_vendor_price_lists': ?>
                                            <div class="form-group sort_group_blocks">
                                                <label class="col-sm-4 control-label">Vendor Price List:</label>
                                                <div class="col-sm-8">
                                                    <label class="form-checkbox"><input type="checkbox" name="contact_field[]" onchange="set_accordion(this);" value="acc_vendor_price_lists">Enable</label>
                                                    <div class="block-group sortable_group" style="display:none;">
                                                        <?php foreach(array_unique(array_merge($field_config,['VPL Import/Export','VPL Description','Color','Category','Description','Product Name','Subcategory','Type','VPL Unique Identifier','Code','ID #','Item SKU','Part #','VPL Product Cost','Average Cost','CDN Cost Per Unit','COGS','Cost','USD Cost Per Unit','USD Invoice','VPL Purchase Info','Date Of Purchase','Purchase Cost','Vendor','VPL Shipping Receiving','Exchange $','Exchange Rate','Freight Charge','Shipping Cash','Shipping Rate','VPL Pricing','Admin Price','Client Price','Commercial Price','Commission Price','Final Retail Price','MSRP','Preferred Price','Purchase Order Price','Rush Price','Sales Order Price','Sell Price','Suggested Retail Price','Unit Cost','Unit Price','Web Price','Wholesale Price','VPL Markup','Markup By $','Markup By %','VPL Stock','Buying Units','Current Stock','Variance','Quantity','Selling Units','Stocking Units','Write-offs','VPL Location','Location','LSD','VPL Dimensions','Size','Weight','VPL Alerts','Min Bin','Min Max','VPL Time Allocation','Actual Hours','Estimated Hours','VPL Admin Fees','GL Assets','GL Revenue','Minimum Billable','VPL Quote','Quote Description','VPL Status','Status','VPL Display On Website','Display On Website','VPL General','Comments','Notes','VPL Rental','Reminder/Alert','Rent Price','Rental Days','Rental Weeks','Rental Months','Rental Years','VPL Day/Week/Month/Year','#Of Hours','#Of Days','Daily','Weekly','Monthly','Annually','VPL Vehicle','#Of Kilometers','VPL Inclusion','Include in P.O.S.','Include in Purchase Orders','Include in Sales Orders','VPL Amount','Min Amount','Max Amount'])) as $field_option) {
                                                            switch($field_option) {
                                                                case 'VPL Import/Export': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Import/Export', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Import/Export" onchange="save_options();">Import/Export</label><?php break;
                                                                case 'VPL Description': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Description', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Description" onchange="save_options();">Description
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Color', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Color" onchange="save_options();">Color</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Category', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Category" onchange="save_options();">Category</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Description', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Description" onchange="save_options();">Description</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Product Name', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Product Name" onchange="save_options();">Product Name</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Subcategory', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Subcategory" onchange="save_options();">Subcategory</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Type', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Type" onchange="save_options();">Type</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Unique Identifier': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Unique Identifier', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Unique Identifier" onchange="save_options();">Unique Identifier
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Code', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Code" onchange="save_options();">Code</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('ID #', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="ID #" onchange="save_options();">ID #</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Item SKU', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Item SKU" onchange="save_options();">Item SKU</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Part #', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Part #" onchange="save_options();">Part #</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Product Cost': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Product Cost', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Product Cost" onchange="save_options();">Product Cost
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Average Cost', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Average Cost" onchange="save_options();">Average Cost</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('CDN Cost Per Unit', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="CDN Cost Per Unit" onchange="save_options();">CDN Cost Per Unit</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('COGS', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="COGS" onchange="save_options();">COGS GL Code</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Cost', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Cost" onchange="save_options();">Cost</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('USD Cost Per Unit', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="USD Cost Per Unit" onchange="save_options();">USD Cost Per Unit</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('USD Invoice', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="USD Invoice" onchange="save_options();">USD Invoice</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Purchase Info': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Purchase Info', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Purchase Info" onchange="save_options();">Purchase Info
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Date Of Purchase', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Date Of Purchase" onchange="save_options();">Date Of Purchase</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Purchase Cost', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Purchase Cost" onchange="save_options();">Purchase Cost</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Vendor', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Vendor" onchange="save_options();">Vendor</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Shipping Receiving': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Shipping Receiving', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Shipping Receiving" onchange="save_options();">Shipping &amp; Receiving
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Exchange $', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Exchange $" onchange="save_options();">Exchange $</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Exchange Rate', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Exchange Rate" onchange="save_options();">Exchange Rate</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Freight Charge', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Freight Charge" onchange="save_options();">Freight Charge</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Shipping Cash', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Shipping Cash" onchange="save_options();">Shipping Cash</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Shipping Rate', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Shipping Rate" onchange="save_options();">Shipping Rate</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Pricing': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Pricing', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Pricing" onchange="save_options();">Pricing
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Admin Price', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Admin Price" onchange="save_options();">Admin Price</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Client Price', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Client Price" onchange="save_options();">Client Price</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Commercial Price', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Commercial Price" onchange="save_options();">Commercial Price</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Commission Price', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Commission Price" onchange="save_options();">Commission Price</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Final Retail Price', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Final Retail Price" onchange="save_options();">Final Retail Price</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('MSRP', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="MSRP" onchange="save_options();">MSRP</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Preferred Price', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Preferred Price" onchange="save_options();">Preferred Price</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Purchase Order Price', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Purchase Order Price" onchange="save_options();">Purchase Order Price</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Rush Price', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Rush Price" onchange="save_options();">Rush Price</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Sales Order Price', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Sales Order Price" onchange="save_options();">Sales Order Price</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Sell Price', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Sell Price" onchange="save_options();">Sell Price</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Suggested Retail Price', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Suggested Retail Price" onchange="save_options();">Suggested Retail Price</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Unit Cost', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Unit Cost" onchange="save_options();">Unit Cost</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Unit Price', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Unit Price" onchange="save_options();">Unit Price</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Web Price', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Web Price" onchange="save_options();">Web Price</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Wholesale Price', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Wholesale Price" onchange="save_options();">Wholesale Price</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Markup': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Markup', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Markup" onchange="save_options();">Markup
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Markup By $', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Markup By $" onchange="save_options();">Markup By $</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Markup By %', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Markup By %" onchange="save_options();">Markup By %</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Stock': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Stock', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Stock" onchange="save_options();">Stock
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Buying Units', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Buying Units" onchange="save_options();">Buying Units</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Current Stock', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Current Stock" onchange="save_options();">Current Stock</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Variance', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Variance" onchange="save_options();">GL Code</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Quantity', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Quantity" onchange="save_options();">Quantity</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Selling Units', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Selling Units" onchange="save_options();">Selling Units</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Stocking Units', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Stocking Units" onchange="save_options();">Stocking Units</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Write-offs', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Write-offs" onchange="save_options();">Write-offs</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Location': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Location', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Location" onchange="save_options();">Location
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Location', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Location" onchange="save_options();">Location</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('LSD', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="LSD" onchange="save_options();">LSD</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Dimensions': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Dimensions', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Dimensions" onchange="save_options();">Dimensions
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Size', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Size" onchange="save_options();">Size</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Weight', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Weight" onchange="save_options();">Weight</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Alerts': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Alerts', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Alerts" onchange="save_options();">Alerts
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Min Bin', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Min Bin" onchange="save_options();">Min Bin</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Min Max', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Min Max" onchange="save_options();">Min Max</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Time Allocation': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Time Allocation', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Time Allocation" onchange="save_options();">Time Allocation
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Actual Hours', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Actual Hours" onchange="save_options();">Actual Hours</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Estimated Hours', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Estimated Hours" onchange="save_options();">Estimated Hours</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Admin Fees': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Admin Fees', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Admin Fees" onchange="save_options();">Admin Fees
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('GL Assets', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="GL Assets" onchange="save_options();">GL Assets</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('GL Revenue', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="GL Revenue" onchange="save_options();">GL Revenue</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Minimum Billable', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Minimum Billable" onchange="save_options();">Minimum Billable</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Quote': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Quote', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Quote" onchange="save_options();">Quote
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Quote Description', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Quote Description" onchange="save_options();">Quote Description</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Status': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Status', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Status" onchange="save_options();">Status
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Status', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Status" onchange="save_options();">Status</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Display On Website': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Display On Website', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Display On Website" onchange="save_options();">Display On Website
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Display On Website', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Display On Website" onchange="save_options();">Display On Website</label>
                                                                    </div></label><?php break;
                                                                case 'VPL General': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL General', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL General" onchange="save_options();">General
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Comments', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Comments" onchange="save_options();">Comments</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Notes', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Notes" onchange="save_options();">Notes</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Rental': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Rental', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Rental" onchange="save_options();">Rental
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Reminder/Alert', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Reminder/Alert" onchange="save_options();">Reminder/Alert</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Rent Price', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Rent Price" onchange="save_options();">Rent Price</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Rental Days', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Rental Days" onchange="save_options();">Rental Days</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Rental Weeks', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Rental Weeks" onchange="save_options();">Rental Weeks</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Rental Months', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Rental Months" onchange="save_options();">Rental Months</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Rental Years', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Rental Years" onchange="save_options();">Rental Years</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Day/Week/Month/Year': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Day/Week/Month/Year', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Day/Week/Month/Year" onchange="save_options();">Day/Week/Month/Year
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('#Of Hours', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="#Of Hours" onchange="save_options();"># Of Hours</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('#Of Days', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="#Of Days" onchange="save_options();"># Of Days</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Daily', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Daily" onchange="save_options();">Daily</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Weekly', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Weekly" onchange="save_options();">Weekly</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Monthly', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Monthly" onchange="save_options();">Monthly</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Annually', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Annually" onchange="save_options();">Annually</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Vehicle': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Vehicle', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Vehicle" onchange="save_options();">Vehicle
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('#Of Kilometers', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="#Of Kilometers" onchange="save_options();"># Of Kilometers</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('#Of Miles', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="#Of Miles" onchange="save_options();"># Of Miles</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Inclusion': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Inclusion', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Inclusion" onchange="save_options();">Inclusion
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Include in P.O.S.', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Include in P.O.S." onchange="save_options();">Include in Point of Sale.</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Include in Purchase Orders', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Include in Purchase Orders" onchange="save_options();">Include in Purchase Orders</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Include in Sales Orders', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Include in Sales Orders" onchange="save_options();">Include in Sales Orders</label>
                                                                    </div></label><?php break;
                                                                case 'VPL Amount': ?><label class="form-checkbox-any"><input type="checkbox" <?= in_array('VPL Amount', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="VPL Amount" onchange="save_options();">Amount
                                                                    <div class="block-group">
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Min Amount', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Min Amount" onchange="save_options();">Min Amount</label>
                                                                        <label class="form-checkbox no-sort"><input type="checkbox" <?= in_array('Max Amount', $field_config) && in_array('acc_vendor_price_lists', $field_config) ? 'checked' : '' ?> name="contact_field[]" value="Max Amount" onchange="save_options();">Max Amount</label>
                                                                    </div></label><?php break;
                                                            }
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
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>
