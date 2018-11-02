<?php error_reporting(0);
include_once('../include.php'); ?>
<script>
$(document).ready(function() {
	$('input').change(saveField);
	$('select').change(saveField);
	$('[name="project_slider"]').change(function() {
		if($('[name="project_slider"]:checked').val() == 'button') {
			$('.project_slider_label').show();
		} else {
			$('.project_slider_label').hide();
		}
	});
});
function saveField() {
	if(this.name == 'tile_name' || this.name == 'project_noun') {
		$.ajax({
			url: 'projects_ajax.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'project_tile_name',
				value: $('[name=tile_name]').val()+'#*#'+($('[name=project_noun]').val() == '' ? $('[name=tile_name]').val() : $('[name=project_noun]').val())
			}
		});
	} else if(this.name == 'project_classify') {
		var project_classify = [];
		$('[name=project_classify]:checked').each(function() {
			project_classify.push($(this).val());
		});
		$.ajax({
			url: 'projects_ajax.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'project_classify',
				value: project_classify.join(',')
			}
		});
	} else if(this.name == 'project_sorting') {
		$.ajax({
			url: 'projects_ajax.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'project_sorting',
				value: $('[name=project_sorting]:checked').val()
			}
		});
	} else if(this.name == 'ticket_sorting') {
		$.ajax({
			url: 'projects_ajax.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'ticket_sorting',
				value: $('[name=ticket_sorting]:checked').val()
			}
		});
	} else if(this.name == 'ticket_status_search') {
		$.ajax({
			url: 'projects_ajax.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'ticket_status_search',
				value: $('[name=ticket_status_search]:checked').val()
			}
		});
	} else if(this.name == 'project_subtab') {
		$.ajax({
			url: 'projects_ajax.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'project_subtab',
				value: $('[name=project_subtab]').val()
			}
		});
	} else if(this.name == 'project_label') {
		$.ajax({
			url: 'projects_ajax.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'project_label',
				value: this.value
			}
		});
	} else if(this.name == 'ticket_label') {
		$.ajax({
			url: 'projects_ajax.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'ticket_label',
				value: this.value
			}
		});
	} else if(this.name == 'project_ticket_bypass') {
		$.ajax({
			url: 'projects_ajax.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'project_ticket_bypass',
				value: this.checked ? this.value : ''
			}
		});
	} else if(this.name == 'project_services_add_to_rates') {
		$.ajax({
			url: 'projects_ajax.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'project_services_add_to_rates',
				value: this.checked ? this.value : ''
			}
		});
	} else if(this.name == 'ticket_project_function') {
		$.ajax({
			url: 'projects_ajax.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'ticket_project_function',
				value: this.checked ? this.value : ''
			}
		});
	} else if(this.name == 'project_sort_fields') {
		var project_sort_fields = [];
		$('[name=project_sort_fields]:checked').each(function() {
			project_sort_fields.push($(this).val());
		});
		$.ajax({
			url: 'projects_ajax.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'project_sort_fields',
				value: project_sort_fields.join(',')
			}
		});
	} else if(this.name == 'project_slider') {
		$.ajax({
			url: 'projects_ajax.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'project_slider',
				value: $('[name="project_slider"]:checked').val()
			}
		});
	} else if(this.name == 'project_slider_label') {
		$.ajax({
			url: 'projects_ajax.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'project_slider_label',
				value: $('[name="project_slider_label"]').val()
			}
		});
	}
}
</script>
<h3>Project Tile Name</h3>
<div class="form-group type-option">
	<label class="col-sm-4">Tile Name:<br /><em>Enter the name you would like the Projects tile to be labelled as.</em></label>
	<div class="col-sm-8">
		<input type="text" name="tile_name" class="form-control" value="<?= PROJECT_TILE ?>">
	</div>
	<div class="clearfix"></div>
	<label class="col-sm-4">Tile Noun:<br /><em>Enter the name you would like individual Projects to be labelled as.</em></label>
	<div class="col-sm-8">
		<input type="text" name="project_noun" class="form-control" value="<?= PROJECT_NOUN ?>">
	</div>
	<div class="clearfix"></div>
</div>
<div class="form-group type-option">
	<label class="col-sm-4"><?= PROJECT_NOUN ?> Label:<br /><em>Enter how you want a <?= PROJECT_NOUN ?> to appear. You can enter [PROJECT_NOUN], [PROJECTID], [PROJECT_NAME], [PROJECT_TYPE], [PROJECT_TYPE_CODE], [PROJECT_START_DATE], [YYYY], [YY], [YY-MM], [YYYY-MM], [BUSINESS], [CONTACT].</em></label>
	<div class="col-sm-8">
		<input type="text" name="project_label" value="<?= get_config($dbc, "project_label") ?>" class="form-control">
	</div>
	<div class="clearfix"></div>
</div>
<hr>
<h3>Sort Settings</h3>
<div class="form-group type-option">
	<label class="col-sm-4"><?= PROJECT_NOUN ?> Classifications:<br /><em>Choose how you want to classify your <?= PROJECT_TILE ?> on the dashboard.</em></label>
	<div class="col-sm-8">
		<?php $project_classify = explode(',',get_config($dbc, "project_classify")); ?>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('All',$project_classify) ? 'checked' : '' ?> name="project_classify" value="All"> All <?= PROJECT_TILE ?></label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Types',$project_classify) ? 'checked' : '' ?> name="project_classify" value="Types"> <?= PROJECT_NOUN ?> Type</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Regions',$project_classify) ? 'checked' : '' ?> name="project_classify" value="Regions"> Regions</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Classifications',$project_classify) ? 'checked' : '' ?> name="project_classify" value="Classifications"> Classifications</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Business',$project_classify) ? 'checked' : '' ?> name="project_classify" value="Business"> <?= BUSINESS_CAT ?></label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Contact',$project_classify) ? 'checked' : '' ?> name="project_classify" value="Contact"> Contact Name</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Lead',$project_classify) ? 'checked' : '' ?> name="project_classify" value="Lead"> <?= PROJECT_NOUN ?> Lead</label>
        <h5>Sort Options</h5>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Alpha Types',$project_classify) ? 'checked' : '' ?> name="project_classify" value="Alpha Types"> Alpha Sort <?= PROJECT_NOUN ?> Types</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Alpha Regions',$project_classify) ? 'checked' : '' ?> name="project_classify" value="Alpha Regions"> Alpha Sort Regions</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Alpha Classifications',$project_classify) ? 'checked' : '' ?> name="project_classify" value="Alpha Classifications"> Alpha Sort Classifications</label>
	</div>
	<div class="clearfix"></div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4"><?= PROJECT_NOUN ?> Sort Fields:<br /><em>Choose which fields you want to use to further filter out <?= PROJECT_TILE ?>.</em></label>
	<div class="col-sm-8">
		<?php $project_sort_fields = explode(',',get_config($dbc, "project_sort_fields")); ?>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Business',$project_sort_fields) ? 'checked' : '' ?> name="project_sort_fields" value="Business"> <?= BUSINESS_CAT ?></label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Site',$project_sort_fields) ? 'checked' : '' ?> name="project_sort_fields" value="Site"> Site</label>
	</div>
	<div class="clearfix"></div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4"><?= PROJECT_NOUN ?> Sorting:<br /><em>Choose how you want to sort your <?= PROJECT_TILE ?> on the dashboard.</em></label>
	<div class="col-sm-8">
		<?php $project_sorting = get_config($dbc, "project_sorting"); ?>
		<label class="form-checkbox"><input type="radio" <?= ('newest' == $project_sorting || $project_sorting == '') ? 'checked' : '' ?> name="project_sorting" value="newest"> Newest First</label>
		<label class="form-checkbox"><input type="radio" <?= ('oldest' == $project_sorting) ? 'checked' : '' ?> name="project_sorting" value="oldest"> Oldest First</label>
		<label class="form-checkbox"><input type="radio" <?= ('project' == $project_sorting) ? 'checked' : '' ?> name="project_sorting" value="project"> Project Name (A - Z)</label>
		<label class="form-checkbox"><input type="radio" <?= ('business' == $project_sorting) ? 'checked' : '' ?> name="project_sorting" value="business"> Business Name (A - Z)</label>
		<label class="form-checkbox"><input type="radio" <?= ('sites' == $project_sorting) ? 'checked' : '' ?> name="project_sorting" value="sites"> Site Name (A - Z)</label>
		<label class="form-checkbox"><input type="radio" <?= ('contact' == $project_sorting) ? 'checked' : '' ?> name="project_sorting" value="contact"> Contact Name (A - Z)</label>
	</div>
	<div class="clearfix"></div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4"><?= TICKET_NOUN ?> Label:<br /><em>Enter how you want <?= TICKET_TILE ?> to appear. You can enter [PROJECT_NOUN], [PROJECTID], [PROJECT_NAME], [PROJECT_TYPE], [PROJECT_TYPE_CODE], [TICKET_NOUN], [TICKETID], [TICKET_HEADING], [TICKET_DATE], [BUSINESS], [CONTACT], [SITE_NAME], [TICKET_TYPE], [STOP_LOCATION], [STOP_CLIENT], [ORDER_NUM].</em></label>
	<div class="col-sm-8">
		<input type="text" name="ticket_label" value="<?= get_config($dbc, "ticket_label") ?>" class="form-control">
	</div>
	<div class="clearfix"></div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4"><?= TICKET_TILE ?> to <?= PROJECT_TILE ?> Functionality:</label>
	<div class="col-sm-8">
		<?php $ticket_project_function = get_config($dbc, "ticket_project_function"); ?>
		<label class="form-checkbox"><input type="radio" <?= 'manual' == $ticket_project_function ? 'checked' : '' ?> name="ticket_project_function" value="manual"> Manually Select <?= PROJECT_TILE ?></label>
		<label class="form-checkbox"><input type="radio" <?= 'business_project' == $ticket_project_function ? 'checked' : '' ?> name="ticket_project_function" value="business_project"> Force <?= PROJECT_NOUN ?> to <?= BUSINESS_CAT ?></label>
		<label class="form-checkbox"><input type="radio" <?= 'contact_project' == $ticket_project_function ? 'checked' : '' ?> name="ticket_project_function" value="contact_project"> Force <?= PROJECT_NOUN ?> to Contact</label>
	</div>
	<div class="clearfix"></div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4"><?= PROJECT_NOUN ?> Default Subtab:<br /></label>
	<div class="col-sm-8">
		<?php $project_subtab = get_config($dbc, "project_subtab"); ?>
		<select name="project_subtab" class="form-control">
			<?php if($project_subtab == 'summary' || $project_subtab == ''): ?>
				<option selected="selected" value="summary">Summary</option>
			<?php else: ?>
				<option value="summary">Summary</option>
			<?php endif; ?>
			<?php if($project_subtab == 'path'): ?>
				<option selected="selected" value="path">Project Path</option>
			<?php else: ?>
				<option value="path">Project Path</option>
			<?php endif; ?>
			<?php if($project_subtab == 'info'): ?>
				<option selected="selected" value="info">Project Details</option>
			<?php else: ?>
				<option value="info">Project Details</option>
			<?php endif; ?>
			<?php if($project_subtab == 'scope'): ?>
				<option selected="selected" value="scope">Scope Of Work</option>
			<?php else: ?>
				<option value="scope">Scope Of Work</option>
			<?php endif; ?>
			<?php if($project_subtab == 'tickets'): ?>
				<option selected="selected" value="tickets">Action Items</option>
			<?php else: ?>
				<option value="tickets">Action Items</option>
			<?php endif; ?>
			<?php if($project_subtab == 'email'): ?>
				<option selected="selected" value="email">Communication</option>
			<?php else: ?>
				<option value="email">Communication</option>
			<?php endif; ?>
			<?php if($project_subtab == 'timesheet'): ?>
				<option selected="selected" value="timesheet">Accounting</option>
			<?php else: ?>
				<option value="timesheet">Accounting</option>
			<?php endif; ?>
			<?php if($project_subtab == 'gantt'): ?>
				<option selected="selected" value="gantt">Reporting</option>
			<?php else: ?>
				<option value="gantt">Reporting</option>
			<?php endif; ?>
			<?php if($project_subtab == 'billing_new'): ?>
				<option selected="selected" value="billing_new">Billing</option>
			<?php else: ?>
				<option value="billing_new">Billing</option>
			<?php endif; ?>
		</select>
	</div>
	<div class="clearfix"></div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4"><?= TICKET_NOUN ?> Tab Functionality:</label>
	<div class="col-sm-8">
		<?php $project_ticket_bypass = get_config($dbc, "project_ticket_bypass"); ?>
		<label class="form-checkbox-any"><input type="checkbox" <?= ($project_ticket_bypass == 'bypass') ? 'checked' : '' ?> name="project_ticket_bypass" value="bypass"> Bypass a list of only one <?= TICKET_NOUN ?> if this is the only tab active, there is only one <?= TICKET_NOUN ?> attached to the current <?= PROJECT_NOUN ?> and they do not have access to add <?= TICKET_TILE ?>.</label>
	</div>
	<div class="clearfix"></div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4"><?= PROJECT_NOUN ?> Scope Push to Rate Card:</label>
	<div class="col-sm-8">
		<?php $project_services_add_to_rates = get_config($dbc, "project_services_add_to_rates"); ?>
		<label class="form-checkbox-any"><input type="checkbox" <?= ($project_services_add_to_rates == 'true') ? 'checked' : '' ?> name="project_services_add_to_rates" value="true"> When adding services to the <?= PROJECT_NOUN ?> scope, push services into the rate card for the relevant Contact.</label>
	</div>
	<div class="clearfix"></div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4"><?= PROJECT_NOUN ?> Sign In Button:</label>
	<div class="col-sm-8">
		<?php $project_slider = get_config($dbc, "project_slider"); ?>
		<label class="form-checkbox"><input type="radio" name="project_slider" <?= empty($project_slider) ? 'checked' : '' ?> value=""> No Sign In Button</label>
		<label class="form-checkbox"><input type="radio" name="project_slider" <?= $project_slider == 'button' ? 'checked' : '' ?> value="button"> Enable</label>
		<label class="form-checkbox"><input type="radio" name="project_slider" <?= $project_slider == 'icon' ? 'checked' : '' ?> value="icon"> Use Eyeball Icon</label>
	</div>
	<div class="clearfix"></div>
</div>
<div class="form-group type-option project_slider_label" <?= $project_slider != 'button' ? 'style="display:none;"' : '' ?>>
	<label class="col-sm-4"><?= PROJECT_NOUN ?> Sign In Button Label:</label>
	<div class="col-sm-8">
		<?php $project_slider_label = get_config($dbc, "project_slider_label"); ?>
		<input type="text" name="project_slider_label" class="form-control" value="<?= $project_slider_label ?>">
	</div>
	<div class="clearfix"></div>
</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_tile.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>
