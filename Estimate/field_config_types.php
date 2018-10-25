<?php error_reporting(0);
include_once('../include.php');
checkAuthorised('estimate');
$estimate_types = explode(',',get_config($dbc, 'project_tabs')); ?>
<script>
$(document).ready(function() {
    $('[name=estimate_tabs]').change(saveTypes);
});
function saveTypes() {
	var estimate_tabs = [];
	$('[name=estimate_tabs]:checked').each(function() {
		this.value = this.value.replace(',','');
		estimate_tabs.push(this.value);
	});
	$.ajax({
		url: 'estimates_ajax.php?action=setting_types',
		method: 'POST',
		data: {
			estimate_tabs: estimate_tabs
		}
	});
}
</script>
<h3><?= rtrim(ESTIMATE_TILE,'s') ?> Types</h3>
<div class="col-sm-12"><em>Select which types to use for <?= ESTIMATE_TILE ?>.</em></div>
<?php $estimate_tabs = explode(',',get_config($dbc, 'estimate_tabs'));
foreach(explode(',',get_config($dbc, 'project_tabs')) as $type_name) { ?>
    <label class="form-checkbox"><input type="checkbox" name="estimate_tabs" <?= in_array($type_name,$estimate_tabs) ? 'checked' : '' ?> value="<?= $type_name ?>"><?= PROJECT_NOUN.': '.$type_name ?></label>
<?php }
foreach(explode(',',get_config($dbc, 'ticket_tabs')) as $type_name) { ?>
    <label class="form-checkbox"><input type="checkbox" name="estimate_tabs" <?= in_array('ticket_est_'.$type_name,$estimate_tabs) ? 'checked' : '' ?> value="ticket_est_<?= $type_name ?>"><?= TICKET_NOUN.': '.$type_name ?></label>
<?php } ?>
<label class="form-checkbox"><input type="checkbox" name="estimate_tabs" <?= in_array('one_time_est_type',$estimate_tabs) ? 'checked' : '' ?> value="one_time_est_type">One Time <?= rtrim(ESTIMATE_TILE,'s') ?></label>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_types.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>