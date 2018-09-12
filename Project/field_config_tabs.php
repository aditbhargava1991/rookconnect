<script>
$(document).ready(function() {
	$('input').change(saveTabs);
	$('.user_form_block input,.user_form_block select').change(saveUserForms);
	saveTabs();
});
$(document).on('change', 'select[name="project_type"]', function() { window.location.href='?settings=tabs&type='+this.value; });
function saveTabs() {
	var tab_list = [];
	$('[name="project_tabs[]"]:checked').not(':disabled').each(function() {
		tab_list.push(this.value);
	});
	$.ajax({
		url: 'projects_ajax.php?action=setting_tabs',
		method: 'POST',
		data: {
			projects: $('[name=project_type]').val(),
			tabs: tab_list
		}
	});
}
function saveUserForms() {
	var user_forms = [];
	$('.user_form_block:not(.readonly-block)').each(function() {
		var block = this;
		$.ajax({
			url: '../Project/projects_ajax.php?action=save_user_form',
			method: 'POST',
			data: {
				project_type: $('[name=project_type]').val(),
				project_form_id: $(this).find('[name="project_form_id"]').val(),
				project_heading: $(this).find('[name="project_heading"]').val(),
				user_form_id: $(this).find('[name="user_form_id"]').val(),
				subtab_name: $(this).find('[name="subtab_name"]').val()
			},
			success: function(response) {
				if(response > 0) {
					$(block).find('[name="project_form_id"]').val(response);
				}
			}
		});
	});
}
function addUserForm(target) {
	var parent = $(target).closest('.block-group');
	destroyInputs('.block-group');
	var row = $(parent).find('.user_form_block').last();
	var clone = row.clone();
	clone.find('input').not('[name="project_heading"]').val('');
	clone.find('select').val('').trigger('change.select2');
	clone.find('*').removeClass('readonly-block');
	row.after(clone);
	$('.user_form_block input,.user_form_block select').change(saveUserForms);
	initInputs('.block-group');
}
function removeUserForm(target) {
	var parent = $(target).closest('.block-group');
	var project_form_id = $(parent).find('[name="project_form_id"]').val();
	if($(parent).find('.user_form_block').length <= 1) {
		addUserForm(target);
	}
	$.ajax({
		url: '../Project/projects_ajax.php?action=delete_user_form',
		method: 'POST',
		data: {
			project_form_id: project_form_id
		},
		success: function(response) {
			$(target).closest('.user_form_block').remove();
		}
	});
}
</script>
<h3>Activate Tabs</h3>
<?php $projecttype = filter_var($_GET['type'],FILTER_SANITIZE_STRING); ?>
<?php $projecttype = (empty($projecttype) ? 'ALL' : $projecttype); ?>
<?php if($projecttype == 'ALL') { ?>
	<label class="col-sm-4">Show tabs at top:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Scrum Tile',$tab_config) ? 'checked' : '' ?> name="project_tabs[]" value="Scrum Tile">Scrum</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Ticket Tile',$tab_config) ? 'checked' : '' ?> name="project_tabs[]" value="Ticket Tile"><?= TICKET_TILE ?></label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Daysheet Tile',$tab_config) ? 'checked' : '' ?> name="project_tabs[]" value="Daysheet Tile">Planner</label>
	</div>
<?php } ?>
<label class="col-sm-4"><?= PROJECT_NOUN ?> Type</label>
<div class="col-sm-8">
	<select name="project_type" class="chosen-select-deselect">
		<option></option>
		<option <?= $projecttype == 'ALL' ? 'selected' : '' ?> value="ALL">Activate Tabs for All <?= PROJECT_TILE ?></option>
		<?php foreach(explode(',',get_config($dbc, 'project_tabs')) as $type_name) {
			$type = preg_replace('/[^a-z_,\']/','',str_replace(' ','_',strtolower($type_name))); ?>
			<option <?= $projecttype == $type ? 'selected' : '' ?> value="<?= $type ?>"><?= $type_name ?></option>
		<?php } ?>
	</select>
</div>
<div class="clearfix"></div>
<?php $user_forms = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE CONCAT(',',`assigned_tile`,',') LIKE '%,project,%'"),MYSQLI_ASSOC);
$tab_config = array_filter(array_unique(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_tabs` FROM field_config_project WHERE type='$projecttype'"))['config_tabs'])));
$all_config = array_filter(array_unique(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_tabs` FROM field_config_project WHERE type='ALL' AND '$projecttype' != 'ALL'"))['config_tabs'])));
if(count($tab_config) == 0 && count($all_config) == 0) {
	$tab_config = explode(',','Path,Information,Details,Documents,Dates,Scope,Estimates,Tickets,Work Orders,Tasks,Checklists,Email,Phone,Reminders,Agendas,Meetings,Gantt,Profit,Report Checklist,Billing,Field Service Tickets,Purchase Orders,Invoices');
} ?>
<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= PROJECT_TILE ?> Summary<img class="pull-right black-color inline-img" src="../img/icons/dropdown-arrow.png"></label>
<div class="block-group col-sm-8">
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Summary', $all_config) ? 'checked disabled' : (in_array('Summary',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Summary">Summary</label>
	<div class="block-group">
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Summary Estimated', $all_config) ? 'checked disabled' : (in_array('Summary Estimated',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Summary Estimated">Estimated Time</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Summary Tracked', $all_config) ? 'checked disabled' : (in_array('Summary Tracked',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Summary Tracked">Tracked Time</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Summary Projections', $all_config) ? 'checked disabled' : (in_array('Summary Projections',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Summary Projections">Projections</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Summary Contact', $all_config) ? 'checked disabled' : (in_array('Summary Contact',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Summary Contact">Point of Contact</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Summary Details', $all_config) ? 'checked disabled' : (in_array('Summary Details',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Summary Details"><?= PROJECT_NOUN ?> Details</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Summary Tickets', $all_config) ? 'checked disabled' : (in_array('Summary Tickets',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Summary Tickets"><?= TICKET_NOUN ?> Summary</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Summary Tasks', $all_config) ? 'checked disabled' : (in_array('Summary Tasks',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Summary Tasks">Task Summary</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Summary Checklist', $all_config) ? 'checked disabled' : (in_array('Summary Checklist',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Summary Checklist">Checklists</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Summary Communications', $all_config) ? 'checked disabled' : (in_array('Summary Communications',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Summary Communications">Communications</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Summary Notes', $all_config) ? 'checked disabled' : (in_array('Summary Notes',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Summary Notes">Notes</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Summary Reporting', $all_config) ? 'checked disabled' : (in_array('Summary Reporting',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Summary Reporting">Reporting</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Summary Billing', $all_config) ? 'checked disabled' : (in_array('Summary Billing',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Summary Billing">Billing</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Summary Payments', $all_config) ? 'checked disabled' : (in_array('Summary Payments',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Summary Payments">Payments</label>
	</div>
</div>
<div class="clearfix"></div>
<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= PROJECT_TILE ?> Path<img class="pull-right black-color counterclockwise inline-img" src="../img/icons/dropdown-arrow.png"></label>
<div class="block-group col-sm-8" style="display:none;">
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Scrum Board', $all_config) ? 'checked disabled' : (in_array('Scrum Board',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Scrum Board">Scrum Boards</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Path', $all_config) ? 'checked disabled' : (in_array('Path',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Path"><?= PROJECT_NOUN ?> Path</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('External Path', $all_config) ? 'checked disabled' : (in_array('External Path',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="External Path">External <?= PROJECT_NOUN ?> Path</label>
	<?php $project_heading = 'project_path';
	include('../Project/field_config_tabs_user_forms.php'); ?>
</div>
<div class="clearfix"></div>
<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= PROJECT_NOUN ?> Details<img class="pull-right black-color counterclockwise inline-img" src="../img/icons/dropdown-arrow.png"></label>
<div class="block-group col-sm-8" style="display:none;">
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Information', $all_config) ? 'checked disabled' : (in_array('Information',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Information"><?= PROJECT_NOUN ?> Information</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Estimate Info', $all_config) ? 'checked disabled' : (in_array('Estimate Info',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Estimate Info">Estimate Information</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Staff', $all_config) ? 'checked disabled' : (in_array('Staff',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Staff">Staff</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Details Path', $all_config) ? 'checked disabled' : (in_array('Details Path',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Details Path"><?= PROJECT_NOUN ?> Path Templates</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Notes', $all_config) ? 'checked disabled' : (in_array('Notes',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Notes">Notes</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Details', $all_config) ? 'checked disabled' : (in_array('Details',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Details"><?= PROJECT_NOUN ?> Details</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Documents', $all_config) ? 'checked disabled' : (in_array('Documents',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Documents">Documents</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Dates', $all_config) ? 'checked disabled' : (in_array('Dates',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Dates">Dates</label>
	<?php $project_heading = 'project_details';
	include('../Project/field_config_tabs_user_forms.php'); ?>
</div>
<div class="clearfix"></div>
<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');">Scope of Work<img class="pull-right black-color counterclockwise inline-img" src="../img/icons/dropdown-arrow.png"></label>
<div class="block-group col-sm-8" style="display:none;">
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Scope', $all_config) ? 'checked disabled' : (in_array('Scope',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Scope"><?= PROJECT_NOUN ?> Scope</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Scope Types', $all_config) ? 'checked disabled' : (in_array('Scope Types',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Scope Types"><?= PROJECT_NOUN ?> Scope by Line Type</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Estimates', $all_config) ? 'checked disabled' : (in_array('Estimates',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Estimates">Estimate Scopes</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Sales Orders', $all_config) ? 'checked disabled' : (in_array('Sales Orders',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Sales Orders"><?= SALES_ORDER_TILE ?></label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Intake', $all_config) ? 'checked disabled' : (in_array('Intake',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Intake">Intake Forms</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Info Gathering', $all_config) ? 'checked disabled' : (in_array('Info Gathering',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Info Gathering">Information Gathering</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Incident Reports', $all_config) ? 'checked disabled' : (in_array('Incident Reports',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Incident Reports"><?= INC_REP_TILE ?></label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Time Sheets', $all_config) ? 'checked disabled' : (in_array('Time Sheets',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Time Sheets">Time Sheets</label>
	<?php $project_heading = 'scope_of_work';
	include('../Project/field_config_tabs_user_forms.php'); ?>
</div>
<div class="clearfix"></div>
<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');">Action Item<img class="pull-right black-color counterclockwise inline-img" src="../img/icons/dropdown-arrow.png"></label>
<div class="block-group col-sm-8" style="display:none;">
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Tickets', $all_config) ? 'checked disabled' : (in_array('Tickets',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Tickets"><?= TICKET_TILE ?></label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Custom PDF', $all_config) ? 'checked disabled' : (in_array('Custom PDF',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Custom PDF">Custom PDFs</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Work Orders', $all_config) ? 'checked disabled' : (in_array('Work Orders',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Work Orders">Work Orders</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Tasks', $all_config) || in_array('Checklists', $all_config) ? 'checked disabled' : (in_array('Tasks',$tab_config) || in_array('Checklists',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Tasks"><?= TASK_TILE ?></label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Checklists In Path', $all_config) ? 'checked disabled' : (in_array('Checklists In Path',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Checklists In Path">Checklists In <?= PROJECT_NOUN ?> Path</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Time Clock', $all_config) ? 'checked disabled' : (in_array('Time Clock',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Time Clock">Time Clock</label>
	<label class="form-checkbox-any-width pad-horiz-2"><input type="checkbox" <?= in_array('Unassigned Hide', $all_config) ? 'checked disabled' : (in_array('Unassigned Hide',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Unassigned Hide">Combine Unassigned and Scheduled <?= TICKET_TILE ?></label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Report Action Items', $all_config) ? 'checked disabled' : (in_array('Report Action Items',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Report Action Items">Status Report</label>
	<?php $project_heading = 'action_item';
	include('../Project/field_config_tabs_user_forms.php'); ?>
</div>
<div class="clearfix"></div>
<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');">Administration<img class="pull-right black-color counterclockwise inline-img" src="../img/icons/dropdown-arrow.png"></label>
<div class="block-group col-sm-8" style="display:none;">
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Administration', $all_config) ? 'checked disabled' : (in_array('Administration',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Administration">Administration</label>
	<?php $project_heading = 'administration';
	include('../Project/field_config_tabs_user_forms.php'); ?>
</div>
<div class="clearfix"></div>
<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');">Communications<img class="pull-right black-color counterclockwise inline-img" src="../img/icons/dropdown-arrow.png"></label>
<div class="block-group col-sm-8" style="display:none;">
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Email', $all_config) ? 'checked disabled' : (in_array('Email',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Email">Email</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Phone', $all_config) ? 'checked disabled' : (in_array('Phone',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Phone">Phone</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Agendas', $all_config) ? 'checked disabled' : (in_array('Agendas',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Agendas">Agendas</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Meetings', $all_config) ? 'checked disabled' : (in_array('Meetings',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Meetings">Meetings</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Comm Log', $all_config) ? 'checked disabled' : (in_array('Comm Log',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Comm Log">Communication History</label>
	<?php $project_heading = 'communications';
	include('../Project/field_config_tabs_user_forms.php'); ?>
</div>
<div class="clearfix"></div>
<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');">Accounting<img class="pull-right black-color counterclockwise inline-img" src="../img/icons/dropdown-arrow.png"></label>
<div class="block-group col-sm-8" style="display:none;">
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Timesheets', $all_config) ? 'checked disabled' : (in_array('Timesheets',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Timesheets">Time Sheets</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Payroll', $all_config) ? 'checked disabled' : (in_array('Payroll',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Payroll">Payroll</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Expenses', $all_config) ? 'checked disabled' : (in_array('Expenses',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Expenses">Expenses</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Payables', $all_config) ? 'checked disabled' : (in_array('Payables',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Payables">Accounts Payable</label>
	<?php $project_heading = 'accounting';
	include('../Project/field_config_tabs_user_forms.php'); ?>
</div>
<div class="clearfix"></div>
<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');">Reporting<img class="pull-right black-color counterclockwise inline-img" src="../img/icons/dropdown-arrow.png"></label>
<div class="block-group col-sm-8" style="display:none;">
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Deliverables', $all_config) ? 'checked disabled' : (in_array('Deliverables',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Deliverables">Deliverables</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Gantt', $all_config) ? 'checked disabled' : (in_array('Gantt',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Gantt">Gantt Chart</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Profit', $all_config) ? 'checked disabled' : (in_array('Profit',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Profit">Profit & Loss</label>
	<!--<label class="form-checkbox"><input type="checkbox" <?= in_array('Report Checklist', $all_config) ? 'checked disabled' : (in_array('Report Checklist',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Report Checklist">Checklist</label>-->
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Reminders', $all_config) ? 'checked disabled' : (in_array('Reminders',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Reminders">Reminders</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Estimated Time', $all_config) ? 'checked disabled' : (in_array('Estimated Time',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Estimated Time">Estimated Time</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Tracked Time', $all_config) ? 'checked disabled' : (in_array('Tracked Time',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Tracked Time">Tracked Time</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Time Tracked', $all_config) ? 'checked disabled' : (in_array('Time Tracked',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Time Tracked">Total Time Tracked</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('History', $all_config) ? 'checked disabled' : (in_array('History',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="History">Activity History</label>
	<?php $project_heading = 'reporting';
	include('../Project/field_config_tabs_user_forms.php'); ?>
</div>
<div class="clearfix"></div>
<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');">Billing<img class="pull-right black-color counterclockwise inline-img" src="../img/icons/dropdown-arrow.png"></label>
<div class="block-group col-sm-8" style="display:none;">
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Billing', $all_config) ? 'checked disabled' : (in_array('Billing',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Billing">Create New</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Payment Schedule', $all_config) ? 'checked disabled' : (in_array('Payment Schedule',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Payment Schedule">Payment Schedule</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Field Service Tickets', $all_config) ? 'checked disabled' : (in_array('Field Service Tickets',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Field Service Tickets">Field Service Tickets</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Purchase Orders', $all_config) ? 'checked disabled' : (in_array('Purchase Orders',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Purchase Orders">Purchase Orders</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Work Tickets', $all_config) ? 'checked disabled' : (in_array('Work Tickets',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Work Tickets">Work Tickets</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Invoices', $all_config) ? 'checked disabled' : (in_array('Invoices',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Invoices">Invoices</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('WCB Invoices', $all_config) ? 'checked disabled' : (in_array('WCB Invoices',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="WCB Invoices">WCB Invoices</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Outstanding', $all_config) ? 'checked disabled' : (in_array('Outstanding',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Outstanding">Outstanding</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Paid', $all_config) ? 'checked disabled' : (in_array('Paid',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Paid">Paid</label>
	<label class="form-checkbox"><input type="checkbox" <?= in_array('Invoice Reminders', $all_config) ? 'checked disabled' : (in_array('Invoice Reminders',$tab_config) ? 'checked' : '') ?> name="project_tabs[]" value="Invoice Reminders">Billing Reminders</label>
	<?php $project_heading = 'billing';
	include('../Project/field_config_tabs_user_forms.php'); ?>
</div>
<div class="clearfix"></div>