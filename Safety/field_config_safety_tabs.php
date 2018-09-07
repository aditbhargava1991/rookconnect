<script>
$(document).ready(function() {
	setSave();
	$('.main-screen').sortable({
		handle: '.drag-handle',
		items: '.type_option',
		update: saveTabs
	});
});
function setSave() {
    $('.block-group input,.block-group textarea').change(saveFields);

	$('[name=custom_subtabs],[name=subtabs],[name=bypass_tabs]').change(function() {
		saveTabs();
	});
}
function saveFields() {
	$.ajax({
		url: 'safety_ajax.php?action=settings_config',
		method: 'POST',
		data: {
			name: this.name,
			value: this.value
		}
	});
}
function saveTabs() {
	tabs = [];
	$('[name=subtabs]:checked').each(function() { tabs.push(this.value); });
	$('[name=custom_subtabs]').each(function() { tabs.push(this.value); });
	bypass_tabs = [];
	$('[name=bypass_tabs]:checked').each(function() { bypass_tabs.push(this.value); });
	$.post('safety_ajax.php?action=setting_tabs', { subtabs: tabs, bypass: bypass_tabs });
}
function addType() {
	var clone = $('.type_option').last().clone();
	clone.find('input').val('').removeAttr('checked');
	$('.type_option').last().after(clone);
	setSave();
}
function removeType(a) {
	if($('.type_option').length <= 1) {
		addType();
	}
	$(a).closest('.type_option').remove();
	saveTabs();
}
</script>
<h3>Enable Safety Tabs</h3>
<div class="block-group">
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Driving Log',$categories) ? ' checked' : '' ?> value="Driving Log" name="subtabs"> Driving Log</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('FLHA',$categories) ? ' checked' : '' ?> value="FLHA" name="subtabs"> FLHA</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Toolbox',$categories) ? ' checked' : '' ?> value="Toolbox" name="subtabs"> Toolbox</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Tailgate',$categories) ? ' checked' : '' ?> value="Tailgate" name="subtabs"> Tailgate</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Form',$categories) ? ' checked' : '' ?> value="Form" name="subtabs"> Forms</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Manuals',$categories) ? ' checked' : '' ?> value="Manuals" name="subtabs"> Manuals</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Incident Reports',$categories) ? ' checked' : '' ?> value="Incident Reports" name="subtabs"> Incident Reports</label>
</div>
<h3>Custom Safety Tabs</h3>
<div class="block-group">
	<?php $custom_cats = [];
	foreach($categories as $custom_cat) {
		if(!in_array($custom_cat, ['Driving Log','FLHA','Toolbox','Tailgate','Form','Manuals','Incident Reports','Pinned','Favourites'])) {
			$custom_cats[] = $custom_cat;
		}
	}
	if(empty($custom_cats)) {
		$custom_cats = [''];
	}
	foreach($custom_cats as $custom_cat) { ?>
		<div class="form-group type_option">
			<label class="col-sm-2 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Your category cannot contain a comma."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span>Tab:</label>
			<div class="col-sm-8">
				<input type="text" name="custom_subtabs" class="form-control" value="<?= $custom_cat ?>">
			</div>
			<div class="col-sm-2">
				<img src="../img/icons/drag_handle.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right drag-handle">
				<img src="../img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right" onclick="addType();">
				<img src="../img/remove.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right" onclick="removeType(this);">
			</div>
			<div class="clearfix"></div>
		</div>
	<?php } ?>
</div>
<h3>Bypass Safety Tabs</h3>
<p>The following tabs, if selected, will skip the list if there is only one form in the list.</p>
<div class="block-group">
	<label class="form-checkbox"><input type="checkbox" <?= in_array('FLHA',$bypass_cat) ? ' checked' : '' ?> value="FLHA" name="bypass_tabs"> Bypass FLHA List</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Toolbox',$bypass_cat) ? ' checked' : '' ?> value="Toolbox" name="bypass_tabs"> Bypass Toolbox List</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Tailgate',$bypass_cat) ? ' checked' : '' ?> value="Tailgate" name="bypass_tabs"> Bypass Tailgate List</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Forms',$bypass_cat) ? ' checked' : '' ?> value="Forms" name="bypass_tabs"> Bypass Forms List</label>
</div>

<div class="block-group">
	<h3>Email Settings</h3>
	<div class="form-group">
		<label class="col-sm-4 control-label">Submission Email Recipient:</label>
		<div class="col-sm-8">
			<input class="form-control" name="safety_manual_completed_email" value="<?= !empty(get_config($dbc, "safety_manual_completed_email")) ? get_config($dbc, "safety_manual_completed_email") : EMAIL_ADDRESS ?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Submission Email Subject:</label>
		<div class="col-sm-8">
			<input class="form-control" name="safety_manual_subject_completed" value="<?= get_config($dbc, "safety_manual_subject_completed") ?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Submission Email Body:<br /><em>You can use [COMMENT]</em></label>
		<div class="col-sm-8">
			<textarea name="safety_manual_body_completed"><?= html_entity_decode(get_config($dbc, "safety_manual_body_completed")) ?></textarea>
		</div>
	</div>
</div>