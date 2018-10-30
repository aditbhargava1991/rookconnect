<?php error_reporting(0);
include_once('../include.php');
include_once('../Ticket/field_list.php') ?>
<script>
$(document).ready(function() {
	$('[data-id],[name="project_admin_multiday_tickets"],[name="project_admin_fields"],[name="project_admin_display_completed"],[name="tickets[]"]').change(saveFields);
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
				signature: $(this).find('[name^=signature]:checked').val(),
				precedence: $(this).find('[name^=precedence]:checked').val(),
				status: $(this).find('[name=status]').val(),
				options: $(this).find('[name=options]:checked').map(function() { return this.value; }).get().join(','),
				action_items: $(this).find('[name=action_items]').map(function() { return this.value; }).get().join(','),
				region: $(this).find('[name=region]').val(),
				location: $(this).find('[name=location]').val(),
				classification: $(this).find('[name=classification]').val(),
				customer: $(this).find('[name=customer]').val(),
				staff: $(this).find('[name=staff]').val(),
				fields: $(this).find('.accordions_sortable input[type=checkbox]').not(':checked').length > 0 ? $(this).find('[name="tickets[]"]:checked').map(function() { return this.value; }).get().join(',') : '',
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
function resetFields(id) {
    $.post('../Project/projects_ajax.php?action=project_fields', {
        id: id, id_field: 'id', table: 'field_config_project_admin', field: 'unlocked_fields', value: ''
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
var new_group = 100000000;
function addGroup(img) {
	var block = $(img).closest('.multi_block');
	destroyInputs();
	var clone = block.clone();
	clone.find('input,select').val('');
	clone.find('[data-id]').data('id','');
    clone.find('a[href^=#collapse_field_group]').prop('href','#collapse_field_group_'+new_group);
    clone.find('.panel-collapse[id^=collapse_field_group]').prop('id','collapse_field_group_'+new_group);
    clone.find('[name^=signature]').prop('name','signature_'+new_group);
    clone.find('[name^=precedence]').prop('name','precedence_'+new_group);
	$('.multi_block').last().after(clone).after('<hr>');
	initInputs();
	$('[data-id]').off('change',saveFields).change(saveFields);
    new_group++;
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
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Approve All" <?= strpos($project_admin_fields,',Approve All,') !== FALSE ? 'checked' : '' ?>> Allow Approve All</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Delivery Rows" <?= strpos($project_admin_fields,',Delivery Rows,') !== FALSE ? 'checked' : '' ?>> Subdivide by Scheduled Stops</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Customer" <?= strpos($project_admin_fields,',Customer,') !== FALSE ? 'checked' : '' ?>> Customer</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Schedule" <?= strpos($project_admin_fields,',Schedule,') !== FALSE ? 'checked' : '' ?>> Scheduled Date and Time</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Status Summary" <?= strpos($project_admin_fields,',Status Summary,') !== FALSE ? 'checked' : '' ?>> Display Status</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Status Edit" <?= strpos($project_admin_fields,',Status Edit,') !== FALSE ? 'checked' : '' ?>> Editable Status</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Services" <?= strpos($project_admin_fields,',Services,') !== FALSE ? 'checked' : '' ?>> Services</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Additional KM Charge" <?= strpos($project_admin_fields,',Additional KM Charge,') !== FALSE ? 'checked' : '' ?>> Additional KM Charge - Based on Services & Staff Time Travel</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Sub Totals per Service" <?= strpos($project_admin_fields,',Sub Totals per Service,') !== FALSE ? 'checked' : '' ?>> Sub Totals per Service</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Staff Tasks" <?= strpos($project_admin_fields,',Staff Tasks,') !== FALSE ? 'checked' : '' ?>> Staff <?= TASK_TILE ?></label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Inventory" <?= strpos($project_admin_fields,',Inventory,') !== FALSE ? 'checked' : '' ?>> Inventory</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Materials" <?= strpos($project_admin_fields,',Materials,') !== FALSE ? 'checked' : '' ?>> Materials</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Misc Item" <?= strpos($project_admin_fields,',Misc Item,') !== FALSE ? 'checked' : '' ?>> Miscellaneous</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Non-Billable" <?= strpos($project_admin_fields,',Non-Billable,') !== FALSE ? 'checked' : '' ?>> Mark Services Non-Billable</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Extra Billing" <?= strpos($project_admin_fields,',Extra Billing,') !== FALSE ? 'checked' : '' ?>> Extra Billing</label>
		<label class="form-checkbox"><input type="checkbox" name="project_admin_fields" value="Notes" <?= strpos($project_admin_fields,',Notes,') !== FALSE ? 'checked' : '' ?>> Notes</label>
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
$ticket_type_list = explode(',',get_config($dbc,'ticket_tabs'));
$status_list = explode(',',get_config($dbc,'ticket_status'));
$admin_groups = $dbc->query("SELECT * FROM `field_config_project_admin` WHERE `deleted`=0 ORDER BY `name`");
$group = $admin_groups->fetch_assoc();
do { ?>
	<div class="multi_block form-horizontal block-panels panel-group">
		<div class="form-group">
			<label class="col-sm-4 control-label">Administration Group Name:</label>
			<div class="col-sm-7">
				<input type="text" name="name" data-id="<?= $group['id'] ?>" value="<?= $group['name'] ?>" class="form-control">
			</div>
			<div class="col-sm-1">
                <img class="inline-img cursor-hand pull-right" src="../img/remove.png" onclick="remGroup(this);">
                <img class="inline-img cursor-hand pull-right" src="../img/icons/ROOK-add-icon.png" onclick="addGroup(this);">
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
					<img class="inline-img cursor-hand pull-right" src="../img/remove.png" onclick="remRow(this);">
					<img class="inline-img cursor-hand pull-right" src="../img/icons/ROOK-add-icon.png" onclick="addRow(this);">
				</div>
			</div>
		<?php } ?>
		<div class="form-group">
			<label class="col-sm-4 control-label"><span class="popover-examples"><a style="margin:5px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="" data-original-title="If a signature is required, then a signature box will pop up when the Manager approves the Action Item."><img src="../img/info.png" width="20"></a></span>Signature Required:</label>
			<div class="col-sm-8">
				<label class="form-checkbox"><input type="radio" name="signature_<?= $group['id'] ?>" data-id="<?= $group['id'] ?>" value="0" <?= $group['signature'] ? '' : 'checked' ?> class="form-control">No</label>
				<label class="form-checkbox"><input type="radio" name="signature_<?= $group['id'] ?>" data-id="<?= $group['id'] ?>" value="1" <?= $group['signature'] ? 'checked' : '' ?> class="form-control">Yes</label>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><span class="popover-examples"><a style="margin:5px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="" data-original-title="Any means that if any of the selected Managers approve the item, it will be considered approved. All means that every manager selected must approve the Item. Precedential means that the first manager will take precedence over subsequent managers, and once the top manager has signed, other manager's signatures will not be required."><img src="../img/info.png" width="20"></a></span>Manager Approvals:</label>
			<div class="col-sm-8">
				<label class="form-checkbox"><input type="radio" name="precedence_<?= $group['id'] ?>" data-id="<?= $group['id'] ?>" value="0" <?= $group['precedence'] == 0 ? 'checked' : '' ?> class="form-control">Any</label>
				<label class="form-checkbox"><input type="radio" name="precedence_<?= $group['id'] ?>" data-id="<?= $group['id'] ?>" value="1" <?= $group['precedence'] == 1 ? 'checked' : '' ?> class="form-control">Precedential</label>
				<label class="form-checkbox"><input type="radio" name="precedence_<?= $group['id'] ?>" data-id="<?= $group['id'] ?>" value="2" <?= $group['precedence'] == 2 ? 'checked' : '' ?> class="form-control">All</label>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><span class="popover-examples"><a style="margin:5px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="" data-original-title="Selecting a status here will first require the user to submit the <?= TICKET_NOUN ?> for approval before it will appear in the list to approve. It will then not be available until it has been approved."><img src="../img/info.png" width="20"></a></span>Submitted Status:</label>
			<div class="col-sm-8">
				<select name="status" data-id="<?= $group['id'] ?>" data-placeholder="Select Status for Submitted <?= TICKET_TILE ?>" class="chosen-select-deselect"><option />
					<option <?= !in_array($group['status'],$status_list) ? 'selected' : '' ?> value="NA">No Submitting Required</option>
					<?php foreach($status_list as $ticket_status) { ?>
						<option <?= $group['status'] == $ticket_status ? 'selected' : '' ?> value="<?= $ticket_status ?>"><?= $ticket_status ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<?php foreach(explode(',',$group['action_items']) as $i => $action) { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Action Items:</label>
				<div class="col-sm-7">
					<select name="action_items" data-id="<?= $group['id'] ?>" data-placeholder="Select Action Items" class="chosen-select-deselect"><option />
						<option <?= $action == 'Tickets' ? 'selected' : '' ?> value="Tickets">All <?= TICKET_TILE ?></option>
						<?php foreach($ticket_type_list as $ticket_type) {
							$ticket_type_id = config_safe_str($ticket_type); ?>
							<option <?= $action == 'ticket_type_'.$ticket_type_id ? 'selected' : '' ?> value="ticket_type_<?= $ticket_type_id ?>"><?= $ticket_type ?></option>
						<?php } ?>
						<option <?= $action == 'Tasks' ? 'selected' : '' ?> value="Tasks">Tasks</option>
					</select>
				</div>
				<div class="col-sm-1">
					<img class="inline-img cursor-hand pull-right" src="../img/remove.png" onclick="remRow(this);">
					<img class="inline-img cursor-hand pull-right" src="../img/icons/ROOK-add-icon.png" onclick="addRow(this);">
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
				<select name="customer" data-id="<?= $group['id'] ?>" data-placeholder="Select Customer" class="chosen-select-deselect"><option />
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
		<?php include_once('../Ticket/field_list.php');
		$value_config = explode(',',get_field_config($dbc, 'tickets'));
		$group_types = explode(',',$group['action_items']);
		if(!isset($ticket_tab_list)) {
			$ticket_tab_list = array_filter(explode(',',get_config($dbc, 'ticket_tabs')));
		}
		foreach($ticket_tab_list as $ticket_tab) {
			$ticket_tab = config_safe_str($ticket_tab);
			if(in_array('ticket_type_'.$ticket_tab,$group_types) || in_array('Tickets',$group_types)) {
				$value_config = array_merge($value_config,explode(',',get_config($dbc, 'ticket_fields_'.$ticket_tab)));
			}
		}
		$all_config_fields = $value_config_fields = $sort_order = array_unique($value_config);
		$value_config = empty($group['unlocked_fields']) ? $value_config_fields : explode(',',$group['unlocked_fields']);

		//Reset merged_config_fields
		$merged_config_fields = $value_config_fields; ?>
        <div class="panel panel-default">
            <div class="panel-heading no_load">
                <h4 class="no-margin">
                    <span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to configure what Fields can be seen when the <?= TICKET_NOUN ?> has not been approved. Only Fields that are turned on will be displayed here."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field_group_<?= $group['id'] ?>">Initial Fields<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_field_group_<?= $group['id'] ?>" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="notice double-gap-bottom popover-examples">
                        <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
                        <div class="col-sm-11">
                            <span class="notice-name">NOTE:</span>
                            Configure what Fields can be seen when the <?= TICKET_NOUN ?> has not been approved. Only Fields that are turned on will be displayed here.
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <button class="btn brand-btn pull-right" onclick="resetFields(<?= $group['id'] ?>); $(this).text('All Fields Unlocked!'); $(this).closest('.panel-body').find('.form-group,.accordions_sortable,h4').hide(); return false;">Unlock All Fields</button>
                    <?php $unlock_mode = true;
                    include('../Ticket/field_config_field_list.php'); ?>
                </div>
            </div>
        </div>
		<div class="clearfix"></div>
        <input type="hidden" name="deleted" value="0">
	</div>
	<hr>
<?php } while($group = $admin_groups->fetch_assoc()); ?>
<div class="clearfix"></div>