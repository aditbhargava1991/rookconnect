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
	<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'contacts_dashboard_config_fields.php') { ?>
		contact_type = type_name;
		loadPanel();
	<?php } else { ?>
		window.location.href = 'contacts_inbox.php?settings=dashboard&type='+type_name;
	<?php } ?>
}
function set_accordion(checkbox) {
	$(checkbox).closest('div').find('.block-group').toggle();
	$(checkbox).closest('div').find('input[type=checkbox]').prop('checked',checkbox.checked);
	save_options();
}
function save_options() {
	var field_list = '';
	//$('[name="accordion_option[]"]:checked').each(function() {
	//	field_list += this.value + ',';
	//});
	$('[name="contacts_dashboard[]"]:checked').each(function() {
		field_list += this.value + ',';
	});
	$.ajax({
		url: '../Contacts/contacts_ajax.php?action=contacts_dashboards',
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
    <h3>Settings - Dashboard</h3>
</div>
<div class="standard-dashboard-body-content full-height">
    <div class="dashboard-item dashboard-item2 full-height">
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

                        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT contacts_dashboard FROM field_config_contacts WHERE tile_name = '".FOLDER_NAME."' AND tab='$current_type' AND accordion IS NULL"));
                        $contacts_dashboard_config = explode(",",$get_field_config['contacts_dashboard']);
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Sort Fields:</label>
                <div class="col-sm-8">
                    <div class="block-group">
                        <label class="form-checkbox"><input type="checkbox" <?= in_array('Sort Match Staff', $contacts_dashboard_config) ? 'checked' : '' ?> name="contacts_dashboard[]" value="Sort Match Staff" onchange="save_options();">Match Staff</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Fields:</label>
                <div class="col-sm-8">
                    <div class="block-group">
                        <label class="form-checkbox"><input type="checkbox" <?= in_array('Home Phone', $contacts_dashboard_config) ? 'checked' : '' ?> name="contacts_dashboard[]" value="Home Phone" onchange="save_options();">Home Phone</label>
                        <label class="form-checkbox"><input type="checkbox" <?= in_array('Office Phone', $contacts_dashboard_config) ? 'checked' : '' ?> name="contacts_dashboard[]" value="Office Phone" onchange="save_options();">Business Phone</label>
                        <label class="form-checkbox"><input type="checkbox" <?= in_array('Cell Phone', $contacts_dashboard_config) ? 'checked' : '' ?> name="contacts_dashboard[]" value="Cell Phone" onchange="save_options();">Cell Phone</label>
                        <label class="form-checkbox"><input type="checkbox" <?= in_array('Email Address', $contacts_dashboard_config) ? 'checked' : '' ?> name="contacts_dashboard[]" value="Email Address" onchange="save_options();">Email Address</label>
                        <label class="form-checkbox"><input type="checkbox" <?= in_array('Business', $contacts_dashboard_config) ? 'checked' : '' ?> name="contacts_dashboard[]" value="Business" onchange="save_options();">Business Name</label>
                        <label class="form-checkbox"><input type="checkbox" <?= in_array('Address', $contacts_dashboard_config) ? 'checked' : '' ?> name="contacts_dashboard[]" value="Address" onchange="save_options();">Address</label>
                        <label class="form-checkbox"><input type="checkbox" <?= in_array('Pronoun', $contacts_dashboard_config) ? 'checked' : '' ?> name="contacts_dashboard[]" value="Pronoun" onchange="save_options();">Preferred Pronoun</label>
                        <label class="form-checkbox"><input type="checkbox" <?= in_array('Birthdate', $contacts_dashboard_config) ? 'checked' : '' ?> name="contacts_dashboard[]" value="Birthdate" onchange="save_options();">Date of Birth / Age</label>
                        <label class="form-checkbox"><input type="checkbox" <?= in_array('Social', $contacts_dashboard_config) ? 'checked' : '' ?> name="contacts_dashboard[]" value="Social" onchange="save_options();">Social Media Links</label>
                        <label class="form-checkbox"><input type="checkbox" <?= in_array('Website', $contacts_dashboard_config) ? 'checked' : '' ?> name="contacts_dashboard[]" value="Website" onchange="save_options();">Website</label>
                        <label class="form-checkbox"><input type="checkbox" <?= in_array('Description', $contacts_dashboard_config) ? 'checked' : '' ?> name="contacts_dashboard[]" value="Description" onchange="save_options();">Description</label>
                        <label class="form-checkbox"><input type="checkbox" <?= in_array('Site', $contacts_dashboard_config) ? 'checked' : '' ?> name="contacts_dashboard[]" value="Site" onchange="save_options();">Site</label>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div><!-- .dashboard-item -->
</div><!-- .standard-dashboard-body-content -->
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'contacts_dashboard_config_fields.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>