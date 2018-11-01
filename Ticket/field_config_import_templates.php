<?php error_reporting(0);
include_once('../include.php');
$ticket_tabs = explode(',',get_config($dbc, 'ticket_tabs'));
$ticket_fields = explode(',',get_field_config($dbc, 'tickets'));
$field_configs = $dbc->query("SELECT `value` FROM `general_configuration` WHERE `name` LIKE 'ticket_fields_%'");
while($row = $field_configs->fetch_assoc()['value']) {
	$ticket_fields = array_merge($ticket_fields,explode(',',$row));
}
$ticket_fields = array_filter(array_unique($ticket_fields)); ?>
<script>
$(document).ready(function() {
	$('[data-col]').change(saveTypes);
	$('[name=ticket_import_bus]').change(function() {
		$.post('ticket_ajax_all.php?action=ticket_import_bus', { value: this.value });
	});
	$('[name=ticket_import_filters]').change(function() {
		$.post('ticket_ajax_all.php?action=ticket_import_filters', { value: this.value });
	});
});
function saveTypes() {
	var columns = [];
	$('[name=column]').each(function() {
		columns.push($(this).data('col')+'-*-'+this.value);
	});
	$.post('ticket_ajax_all.php?action=import_templates', {
		business: '<?= $_GET['businessid'] ?>',
		column_list: columns.join('#*#')
	});
}
</script>
<div class="form-group">
	<label class="control-label col-sm-4">Display <?= BUSINESS_CAT ?>:<br /><em>This controls which <?= BUSINESS_CAT ?> will show up in the dropdown in the Import tab so you can hide any <?= BUSINESS_CAT ?> that does not have a template.</em></label>
	<div class="col-sm-8">
		<?php $ticket_import_bus = get_config($dbc, 'ticket_import_bus'); ?>
		<label class="form-checkbox"><input type="radio" name="ticket_import_bus" value="" <?= $ticket_import_bus == '' ? 'checked' : '' ?>>All <?= BUSINESS_CAT ?></label>
		<label class="form-checkbox"><input type="radio" name="ticket_import_bus" value="template_only" <?= $ticket_import_bus == '' ? '' : 'checked' ?>>Only <?= BUSINESS_CAT ?> With Templates</label>
	</div>
</div>
<div class="form-group">
	<label class="control-label col-sm-4">Import File Line Items:<br /><em>This allows you to specify that you do not want to import a line in the import file if particular fields are missing. This affects all templates.</em></label>
	<div class="col-sm-8">
		<?php $ticket_import_filters = get_config($dbc, 'ticket_import_filters'); ?>
		<label class="form-checkbox"><input type="radio" name="ticket_import_filters" value="" <?= $ticket_import_filters == '' ? 'checked' : '' ?>>All Line Items</label>
		<label class="form-checkbox"><input type="radio" name="ticket_import_filters" value="ticketid" <?= $ticket_import_filters == 'ticketid' ? 'checked' : '' ?>>Require <?= TICKET_NOUN ?> Identifier</label>
		<label class="form-checkbox"><input type="radio" name="ticket_import_filters" value="ticketid_valid" <?= $ticket_import_filters == 'ticketid_valid' ? 'checked' : '' ?>>Require Existing Valid <?= TICKET_NOUN ?></label>
		<label class="form-checkbox"><input type="radio" name="ticket_import_filters" value="ticket_group" <?= $ticket_import_filters == 'ticket_group' ? 'checked' : '' ?>>Require <?= TICKET_NOUN ?> Grouping Field</label>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="You will need to select this business for the Import to use this template."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= BUSINESS_CAT ?>:</label>
	<div class="col-sm-8">
		<select name="businessid" class="chosen-select-deselect" onchange="window.location.replace('?settings=importing&businessid='+this.value);" data-placeholder="Select <?= BUSINESS_CAT ?>"><option />
			<?php foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `name`, `first_name`, `last_name` FROM `contacts` WHERE `category`='".BUSINESS_CAT."' AND `deleted`=0 AND `status` > 0")) as $business) { ?>
				<option <?= $business['contactid'] == $_GET['businessid'] ? 'selected' : '' ?> value="<?= $business['contactid'] ?>"><?= $business['full_name'] ?></option>
			<?php } ?>
		</select>
	</div>
</div>
<?php if($_GET['businessid'] > 0) {
	$template = explode('#*#',get_config($dbc, 'ticket_import_'.$_GET['businessid'])); ?>
	<h4>Listed below are the columns that can be imported. Enter the name of each column as it will appear in the Spreadsheet that will be imported. Leaving it blank will exclude it from the import. Any column not specified here will not be imported.</h4>
	<?php foreach($template as $temp_line) {
		$temp_line = explode('-*-',$temp_line);
		if($temp_line[0] == 'ticket_identifier') {
			$col = $temp_line[1];
		}
	} ?>
	<div class="form-group">
		<label class="control-label col-sm-4"><?= TICKET_NOUN ?> Identifier:<br /><em>The identified field will be matched to either the <?= TICKET_NOUN ?> ID, or the designated label. If there is no match, then a new <?= TICKET_NOUN ?> will be created, otherwise, the <?= TICKET_NOUN ?> will be updated.</em></label>
		<div class="col-sm-8">
			<input typ="text" name="column" data-col="ticket_identifier" value="<?= $col ?>" class="form-control">
		</div>
	</div>
	<?php foreach($template as $temp_line) {
		$temp_line = explode('-*-',$temp_line);
		if($temp_line[0] == 'ticket_grouping') {
			$col = $temp_line[1];
		}
	} ?>
	<div class="form-group">
		<label class="control-label col-sm-4"><?= TICKET_NOUN ?> Grouping Field:<br /><em>The identified field will be used to group multiple lines into a single <?= TICKET_NOUN ?>, so that all rows with the same value will be imported as a single <?= TICKET_NOUN ?></em></label>
		<div class="col-sm-8">
			<input typ="text" name="column" data-col="ticket_grouping" value="<?= $col ?>" class="form-control">
		</div>
	</div>
	<?php foreach($template as $temp_line) {
		$temp_line = explode('-*-',$temp_line);
		if($temp_line[0] == 'ticket_label') {
			$col = $temp_line[1];
		}
	} ?>
	<div class="form-group">
		<label class="control-label col-sm-4"><?= TICKET_NOUN ?> Label Field:<br /><em>The identified field will be used as the label until an update to the <?= TICKET_NOUN ?> overrides it.</em></label>
		<div class="col-sm-8">
			<input typ="text" name="column" data-col="ticket_label" value="<?= $col ?>" class="form-control">
		</div>
	</div>
	<?php foreach($template as $temp_line) {
		$temp_line = explode('-*-',$temp_line);
		if($temp_line[0] == 'ticket_type') {
			$col = $temp_line[1];
		}
	} ?>
	<div class="form-group">
		<label class="control-label col-sm-4"><?= TICKET_NOUN ?> Tab:<br /><em>If this is set, this type will be assigned to all <?= TICKET_TILE ?> imported for this template.</em></label>
		<div class="col-sm-8">
			<select name="column" data-col="ticket_type" data-placeholder="Select Tab..." value="<?= $col ?>" class="chosen-select-deselect"><option />
				<?php foreach($ticket_tabs as $type_name) {
					$type_id = config_safe_str($type_name); ?>
					<option <?= $type_id == $col ? 'selected' : '' ?> value="<?= $type_id ?>"><?= $type_name ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<?php include_once('ticket_field_list.php');
	foreach($ticket_fields as $field_config_str) {
		foreach(ticket_field_name($field_config_str) as $arr) {
			$col = '';
			foreach($template as $temp_line) {
				$temp_line = explode('-*-',$temp_line);
				if($temp_line[0] == $arr[1]) {
					$col = $temp_line[1];
				}
			} ?>
			<div class="form-group">
				<label class="control-label col-sm-4"><?= $arr[1] ?></label>
				<div class="col-sm-8">
					<input typ="text" name="column" data-col="<?= $arr[1] ?>" value="<?= $col ?>" class="form-control">
				</div>
			</div>
		<?php }
	} ?>
<?php } ?>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_import_templates.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>