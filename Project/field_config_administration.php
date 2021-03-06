<?php error_reporting(0);
include_once('../include.php'); ?>
<script>
$(document).ready(function() {
	$('[data-id],[name="project_admin_multiday_tickets"],[name="project_admin_fields"],[name="project_admin_display_completed"]').change(saveFields);
});
function saveFields() {
	var blocks = $('.multi_block');
	if($(this).data('id') > 0) {
		block = $(this).closest('.multi_block');
	}
	blocks.each(function() {
		var block = this;
		$.post('../Project/projects_ajax.php?action=administration_settings',
			{
				id: $(this).find('[name=name]').data('id'),
				name: $(this).find('[name=name]').val(),
				contactid: $(this).find('[name=contactid]').map(function() { return this.value; }).get().join(','),
				signature: $(this).find('[name=signature]:checked').val(),
				precedence: $(this).find('[name=precedence]:checked').val(),
				action_items: $(this).find('[name=action_items]').map(function() { return this.value; }).get().join(','),
				region: $(this).find('[name=region]').val(),
				location: $(this).find('[name=location]').val(),
				classification: $(this).find('[name=classification]').val(),
				customer: $(this).find('[name=customer]').val(),
				staff: $(this).find('[name=staff]').val(),
				deleted: $(this).find('[name=deleted]').val()
			}, function(id) {
				$(block).find('[data-id]').data('id',id);
			});
	});
	var project_admin_multiday_tickets = '';
	if($('[name="project_admin_multiday_tickets"]').is(':checked')) {
		project_admin_multiday_tickets = $('[name="project_admin_multiday_tickets"]').val();
	}
	$.ajax({
		url: '../Project/projects_ajax.php?action=setting_tile',
		method: 'POST',
		data: {
			field: 'project_admin_multiday_tickets',
			value: project_admin_multiday_tickets
		}
	});
	var project_admin_fields = [];
	$('[name="project_admin_fields"]:checked').each(function() {
		project_admin_fields.push($(this).val());
	});
	project_admin_fields = project_admin_fields.join(',');
	$.ajax({
		url: '../Project/projects_ajax.php?action=setting_tile',
		method: 'POST',
		data: {
			field: 'project_admin_fields',
			value: project_admin_fields
		}
	});
	var project_admin_display_completed = '';
	if($('[name="project_admin_display_completed"]').is(':checked')) {
		project_admin_display_completed = $('[name="project_admin_display_completed"]').val();
	}
	$.ajax({
		url: '../Project/projects_ajax.php?action=setting_tile',
		method: 'POST',
		data: {
			field: 'project_admin_display_completed',
			value: project_admin_display_completed
		}
	});
}
function addRow(img) {
	var block = $(img).closest('.form-group');
	destroyInputs();
	var clone = block.clone();
	clone.find('input,select').val('');
	block.after(clone);
	initInputs();
	$('[data-id]').off('change',saveFields).change(saveFields);
}
function remRow(img) {
	var block = $(img).closest('.form-group');
	var label = block.find('label').text();
	if(block.closest('.multi_block').find('label').filter(function() { return $(this).text() == 'Manager:'; }).length <= 1) {
		addRow(img);
	}
	block.remove();
	saveFields();
}
function addGroup(img) {
	var block = $(img).closest('.multi_block');
	destroyInputs();
	var clone = block.clone();
	clone.find('input,select').val('');
	clone.find('[data-id]').data('id','');
	$('.multi_block').last().after(clone).after('<hr>');
	initInputs();
	$('[data-id]').off('change',saveFields).change(saveFields);
}
function remGroup(img) {
	var block = $(img).closest('.multi_block');
	if($('.multi_block').length <= 1) {
		addGroup(img);
	}
	block.find('[name=deleted]').val(1);
	block.hide().next('hr').remove();
	saveFields();
}
</script>
<h3>Administration</h3>
<div class="form-group">
	<label class="col-sm-4">Allow Multiple Days Per <?= TICKET_NOUN ?>:</label>
	<div class="col-sm-8">
		<?php $project_admin_multiday_tickets = get_config($dbc, 'project_admin_multiday_tickets'); ?>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_multiday_tickets" value="1" <?= $project_admin_multiday_tickets == 1 ? 'checked' : '' ?>> Enable</label>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4">Fields:</label>
	<div class="col-sm-8">
		<?php $project_admin_fields = get_config($dbc, 'project_admin_fields');
		if(empty($project_admin_fields)) {
			$project_admin_fields = ',Services,Sub Totals per Service,';
		}
		$project_admin_fields = ','.$project_admin_fields.','; ?>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Services" <?= strpos($project_admin_fields,',Services,') !== FALSE ? 'checked' : '' ?>> Services</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Sub Totals per Service" <?= strpos($project_admin_fields,',Sub Totals per Service,') !== FALSE ? 'checked' : '' ?>> Sub Totals per Service</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Staff Tasks" <?= strpos($project_admin_fields,',Staff Tasks,') !== FALSE ? 'checked' : '' ?>> Staff Tasks</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Inventory" <?= strpos($project_admin_fields,',Inventory,') !== FALSE ? 'checked' : '' ?>> Inventory</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Materials" <?= strpos($project_admin_fields,',Materials,') !== FALSE ? 'checked' : '' ?>> Materials</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Misc Item" <?= strpos($project_admin_fields,',Misc Item,') !== FALSE ? 'checked' : '' ?>> Miscellaneous</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Extra Billing" <?= strpos($project_admin_fields,',Extra Billing,') !== FALSE ? 'checked' : '' ?>> Extra Billing</label>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4">Only Display Completed <?= TICKET_TILE ?>:</label>
	<div class="col-sm-8">
		<?php $project_admin_display_completed = get_config($dbc, 'project_admin_display_completed'); ?>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_display_completed" value="1" <?= $project_admin_display_completed == 1 ? 'checked' : '' ?>> Enable</label>
	</div>
</div>
<div class="clearfix"></div>
<hr>
<?php $staff_list = sort_contacts_query($dbc->query("SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status` > 0"));
$customer_list = sort_contacts_query($dbc->query("SELECT `contactid`, `name`, `first_name`, `last_name` FROM `contacts` WHERE `category` NOT IN (".STAFF_CATS.") AND `deleted`=0 AND `status` > 0"));
$region_list = array_filter(array_unique(explode(',',get_config($dbc, '%region', true, ','))));
$location_list = array_filter(array_unique(explode(',',$dbc->query("SELECT GROUP_CONCAT(con_locations SEPARATOR ',') `locations` FROM field_config_contacts WHERE `con_locations` IS NOT NULL")->fetch_assoc()['locations'])));
$classification_list = array_filter(array_unique(explode(',',get_config($dbc, '%classification', true, ','))));
$admin_groups = $dbc->query("SELECT * FROM `field_config_project_admin` WHERE `deleted`=0 ORDER BY `name`");
$group = $admin_groups->fetch_assoc();
do { ?>
	<div class="multi_block form-horizontal">
		<div class="form-group">
			<label class="col-sm-4 control-label">Administration Group Name:</label>
			<div class="col-sm-8">
				<input type="text" name="name" data-id="<?= $group['id'] ?>" value="<?= $group['name'] ?>" class="form-control">
			</div>
		</div>
		<?php foreach(explode(',',$group['contactid']) as $i => $manager) { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Manager:</label>
				<div class="col-sm-7">
					<select name="contactid" data-id="<?= $group['id'] ?>" data-placeholder="Select Staff" class="chosen-select-deselect"><option />
						<?php foreach($staff_list as $row) { ?>
							<option <?= $manager == $row['contactid'] ? 'selected' : '' ?> value="<?= $row['contactid'] ?>"><?= $row['first_name'].' '.$row['last_name'] ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col-sm-1">
					<img class="inline-img cursor-hand" src="../img/remove.png" onclick="remRow(this);">
					<img class="inline-img cursor-hand" src="../img/icons/ROOK-add-icon.png" onclick="addRow(this);">
				</div>
			</div>
		<?php } ?>
		<div class="form-group">
			<label class="col-sm-4 control-label"><span class="popover-examples"><a style="margin:5px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="" data-original-title="If a signature is required, then a signature box will pop up when the Manager approves the Action Item."><img src="../img/info.png" width="20"></a></span>Signature Required:</label>
			<div class="col-sm-8">
				<label class="form-checkbox"><input type="radio" name="signature" data-id="<?= $group['id'] ?>" value="0" <?= $group['signature'] ? '' : 'checked' ?> class="form-control">No</label>
				<label class="form-checkbox"><input type="radio" name="signature" data-id="<?= $group['id'] ?>" value="1" <?= $group['signature'] ? 'checked' : '' ?> class="form-control">Yes</label>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><span class="popover-examples"><a style="margin:5px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="" data-original-title="Any means that if any of the selected Managers approve the item, it will be considered approved. All means that every manager selected must approve the Item. Precedential means that the first manager will take precedence over subsequent managers, and once the top manager has signed, other manager's signatures will not be required."><img src="../img/info.png" width="20"></a></span>Manager Approvals:</label>
			<div class="col-sm-8">
				<label class="form-checkbox"><input type="radio" name="precedence" data-id="<?= $group['id'] ?>" value="0" <?= $group['precedence'] == 0 ? 'checked' : '' ?> class="form-control">Any</label>
				<label class="form-checkbox"><input type="radio" name="precedence" data-id="<?= $group['id'] ?>" value="1" <?= $group['precedence'] == 1 ? 'checked' : '' ?> class="form-control">Precedential</label>
				<label class="form-checkbox"><input type="radio" name="precedence" data-id="<?= $group['id'] ?>" value="2" <?= $group['precedence'] == 2 ? 'checked' : '' ?> class="form-control">All</label>
			</div>
		</div>
		<?php foreach(explode(',',$group['action_items']) as $i => $action) { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Action Items:</label>
				<div class="col-sm-7">
					<select name="action_items" data-id="<?= $group['id'] ?>" data-placeholder="Select Action Items" class="chosen-select-deselect"><option />
						<option <?= $action == 'Tickets' ? 'selected' : '' ?> value="Tickets"><?= TICKET_TILE ?></option>
						<option <?= $action == 'Tasks' ? 'selected' : '' ?> value="Tasks">Tasks</option>
					</select>
				</div>
				<div class="col-sm-1">
					<img class="inline-img cursor-hand" src="../img/remove.png" onclick="remRow(this);">
					<img class="inline-img cursor-hand" src="../img/icons/ROOK-add-icon.png" onclick="addRow(this);">
				</div>
			</div>
			<?php } ?>
		<?php if(count($region_list) > 0) { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Region:</label>
				<div class="col-sm-8">
					<select name="region" data-id="<?= $group['id'] ?>" data-placeholder="Select Region" class="chosen-select-deselect"><option />
						<?php foreach($region_list as $row) { ?>
							<option <?= strpos(','.$group['region'].',',','.$row.',') !== FALSE ? 'selected' : '' ?> value="<?= $row ?>"><?= $row ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		<?php } ?>
		<?php if(count($location_list) > 0) { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Location:</label>
				<div class="col-sm-8">
					<select name="location" data-id="<?= $group['id'] ?>" data-placeholder="Select Location" class="chosen-select-deselect"><option />
						<?php foreach($location_list as $row) { ?>
							<option <?= strpos(','.$group['location'].',',','.$row.',') !== FALSE ? 'selected' : '' ?> value="<?= $row ?>"><?= $row ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		<?php } ?>
		<?php if(count($classification_list) > 0) { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Classification:</label>
				<div class="col-sm-8">
					<select name="classification" data-id="<?= $group['id'] ?>" data-placeholder="Select Classification" class="chosen-select-deselect"><option />
						<?php foreach($classification_list as $row) { ?>
							<option <?= strpos(','.$group['classification'].',',','.$row.',') !== FALSE ? 'selected' : '' ?> value="<?= $row ?>"><?= $row ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		<?php } ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Customer:</label>
			<div class="col-sm-8">
				<select name="customer" data-id="<?= $group['id'] ?>" data-placeholder="Select Action Items" class="chosen-select-deselect"><option />
					<?php foreach($staff_list as $row) { ?>
						<option <?= $group['customer'] == $row['contactid'] ? 'selected' : '' ?> value="<?= $row['contactid'] ?>"><?= $row['name'].($row['name'] != '' && $row['first_name'].$row['last_name'] != '' ? ': ' : '').$row['first_name'].' '.$row['last_name'] ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Staff:</label>
			<div class="col-sm-8">
				<select name="staff" data-id="<?= $group['id'] ?>" data-placeholder="Select Assigned Staff" class="chosen-select-deselect"><option />
					<?php foreach($staff_list as $row) { ?>
						<option <?= $group['staff'] == $row['contactid'] ? 'selected' : '' ?> value="<?= $row['contactid'] ?>"><?= $row['first_name'].' '.$row['last_name'] ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<input type="hidden" name="deleted" value="0">
		<img class="inline-img cursor-hand pull-right" src="../img/icons/ROOK-add-icon.png" onclick="addGroup(this);">
		<img class="inline-img cursor-hand pull-right" src="../img/remove.png" onclick="remGroup(this);">
		<div class="clearfix"></div>
	</div>
	<hr>
<?php } while($group = $admin_groups->fetch_assoc()); ?>
<div class="clearfix"></div>