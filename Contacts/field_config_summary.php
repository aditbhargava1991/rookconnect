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

function save_options() {
	var field_list = '';

	$('[name="contacts_summary[]"]:checked').each(function() {
		field_list += this.value + ',';
	});
	$.ajax({
		url: '../Contacts/contacts_ajax.php?action=contacts_summary',
		method: 'POST',
		data: { field_list: field_list, tile: '<?= FOLDER_NAME ?>' },
		response: 'html',
		success: function(response) {
			console.log(response);
		}
	});
}
$(document).ready(function() {
	$('input[type=checkbox]:checked').closest('.block-group').show();
});
</script>
<div class="standard-dashboard-body-title">
    <h3>Settings - Summary</h3>
</div>
<div class="standard-dashboard-body-content full-height">
    <div class="dashboard-item dashboard-item2 full-height">
        <form class="form-horizontal">
        <div class="form-group block-group block-group-noborder">

            <div class="form-group">
                <label class="col-sm-4 control-label">Summary:</label>
                <div class="col-sm-8">
                    <div class="block-group">

                        <?php
                        $heading = FOLDER_NAME.'_summary';
                        $contacts_summary_config = get_config($dbc, $heading); ?>

                        <label class="form-checkbox"><input type="checkbox" <?php if(strpos($contacts_summary_config,'Per Category') !== false) { echo 'checked'; } ?> name="contacts_summary[]" value="Per Category" onchange="save_options();">Per Category</label>
                        <label class="form-checkbox"><input type="checkbox" <?php if(strpos($contacts_summary_config,'Per Business') !== false) { echo 'checked'; } ?> name="contacts_summary[]" value="Per Business" onchange="save_options();">Per Business</label>
                        <label class="form-checkbox"><input type="checkbox" <?php if(strpos($contacts_summary_config,'Per Gender') !== false) { echo 'checked'; } ?> name="contacts_summary[]" value="Per Gender" onchange="save_options();">Per Gender</label>
                        <label class="form-checkbox"><input type="checkbox" <?php if(strpos($contacts_summary_config,'Per Classification') !== false) { echo 'checked'; } ?> name="contacts_summary[]" value="Per Classification" onchange="save_options();">Per Classification</label>
                        <label class="form-checkbox"><input type="checkbox" <?php if(strpos($contacts_summary_config,'Per City') !== false) { echo 'checked'; } ?> name="contacts_summary[]" value="Per City" onchange="save_options();">Per City</label>

                        <label class="form-checkbox"><input type="checkbox" <?php if(strpos($contacts_summary_config,'Per Archived Data') !== false) { echo 'checked'; } ?> name="contacts_summary[]" value="Per Archived Data" onchange="save_options();">Per Archived Data</label>



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