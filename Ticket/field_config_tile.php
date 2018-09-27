<?php error_reporting(0);
include_once('../include.php'); ?>
<script>
$(document).ready(function() {
	$('input,select').change(saveField);
});
function saveFieldMethod(field) {
	if(field.name == 'ticket_tile' || field.name == 'ticket_noun') {
		$.ajax({
			url: 'ticket_ajax_all.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'ticket_tile_name',
				value: $('[name=ticket_tile]').val()+'#*#'+($('[name=ticket_noun]').val() == '' ? $('[name=ticket_tile]').val() : $('[name=ticket_noun]').val())
			}
		});
	} else if(field.name == 'ticket_sorting') {
		$.ajax({
			url: 'ticket_ajax_all.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'ticket_sorting',
				value: $('[name=ticket_sorting]:checked').val()
			}
		});
	} else if(field.name == 'ticket_label') {
		$.ajax({
			url: 'ticket_ajax_all.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'ticket_label',
				value: field.value
			}
		});
	} else if(field.name == 'default_status') {
		$.ajax({
			url: 'ticket_ajax_all.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'ticket_default_status',
				value: field.value
			}
		});
	} else if(field.name == 'auto_archive') {
		$.ajax({
			url: 'ticket_ajax_all.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'auto_archive',
				value: field.value
			}
		});
	} else if(field.name == 'ticket_layout') {
		$.ajax({
			url: 'ticket_ajax_all.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'ticket_layout',
				value: field.value
			}
		});
	} else if(field.name == 'ticket_exclude_archive') {
		$.ajax({
			url: 'ticket_ajax_all.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'ticket_exclude_archive',
				value: field.value
			}
		});
	} else if(field.name == 'ticket_project_function') {
		$.ajax({
			url: 'ticket_ajax_all.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'ticket_project_function',
				value: field.value
			}
		});
	} else if(field.name == 'ticket_summary_urls') {
		$.ajax({
			url: 'ticket_ajax_all.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'ticket_summary_urls',
				value: field.value
			}
		});
	} else if(field.name == 'ticket_slider_layout') {
		$.ajax({
			url: 'ticket_ajax_all.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'ticket_slider_layout',
				value: field.value
			}
		});
	} else if(field.name == 'ticket_textarea_style') {
		$.ajax({
			url: 'ticket_ajax_all.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'ticket_textarea_style',
				value: field.value
			}
		});
	} else if(field.name == 'ticket_unassigned_status') {
		$.ajax({
			url: 'ticket_ajax_all.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'ticket_unassigned_status',
				value: field.value
			}
		});
	} else if(field.name == 'ticket_uneditable_status[]') {
		var statuses = [];
		$(field).find('option:selected').each(function () {
			statuses.push(field.value);
		});
		$.ajax({
			url: 'ticket_ajax_all.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'ticket_uneditable_status',
				value: statuses.join(',')
			}
		});
	} else if(['auto_archive_unbooked_day','auto_archive_unbooked_time','ticket_default_session_user','ticket_archive_status','ticket_invoice_status'].indexOf(field.name) >= 0) {
        var value = '';
        if(field.type == 'select-multiple') {
            var list = [];
            $(field).find('option:selected').each(function () {
                list.push(this.value);
            });
            value = list.join('#*#');
        } else {
            value = field.value;
        }
		$.post('ticket_ajax_all.php?action=setting_tile', {
            field: field.name,
            value: value
        });
	}
    doneSaving();
}
</script>
<h3>Tickets Tile Name</h3>
<div class="form-group type-option">
	<label class="col-sm-4">Tile Name:<br /><em>Enter the name you would like the Tickets tile to be labelled as.</em></label>
	<div class="col-sm-8">
		<input type="text" name="ticket_tile" class="form-control" value="<?= TICKET_TILE ?>">
	</div>
	<div class="clearfix"></div>
	<label class="col-sm-4">Tile Noun:<br /><em>Enter the name you would like individual Tickets to be labelled as.</em></label>
	<div class="col-sm-8">
		<input type="text" name="ticket_noun" class="form-control" value="<?= TICKET_NOUN ?>">
	</div>
	<div class="clearfix"></div>
</div>
<div class="form-group type-option">
	<label class="col-sm-4"><?= TICKET_NOUN ?> Label:<br /><em>Enter how you want a <?= TICKET_NOUN ?> to appear. You can enter [PROJECT_NOUN], [PROJECTID], [PROJECT_NAME], [PROJECT_TYPE], [PROJECT_TYPE_CODE], [TICKET_NOUN], [TICKETID], [TICKET_HEADING], [TICKET_DATE], [BUSINESS], [CONTACT], [SITE_NAME], [TICKET_TYPE], [STOP_LOCATION], [STOP_CLIENT], [ORDER_NUM].</em></label>
	<div class="col-sm-8">
		<input type="text" name="ticket_label" value="<?= get_config($dbc, "ticket_label") ?>" class="form-control">
	</div>
	<div class="clearfix"></div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4"><?= TICKET_NOUN ?> Sorting:<br /><em>Choose how you want to sort your <?= TICKET_TILE ?>.</em></label>
	<div class="col-sm-8">
		<?php $ticket_sorting = get_config($dbc, "ticket_sorting"); ?>
		<label class="form-checkbox"><input type="radio" <?= ('newest' == $ticket_sorting || $ticket_sorting == '') ? 'checked' : '' ?> name="ticket_sorting" value="newest"> Newest First</label>
		<label class="form-checkbox"><input type="radio" <?= ('oldest' == $ticket_sorting) ? 'checked' : '' ?> name="ticket_sorting" value="oldest"> Oldest First</label>
		<label class="form-checkbox"><input type="radio" <?= ('project' == $ticket_sorting) ? 'checked' : '' ?> name="ticket_sorting" value="project"> <?= PROJECT_NOUN ?> Name (A - Z)</label>
		<label class="form-checkbox"><input type="radio" <?= ('label' == $ticket_sorting) ? 'checked' : '' ?> name="ticket_sorting" value="label"> <?= TICKET_NOUN ?> Label (A - Z)</label>
		<label class="form-checkbox"><input type="radio" <?= ('to_do_date_desc' == $ticket_sorting) ? 'checked' : '' ?> name="ticket_sorting" value="to_do_date_desc"> Scheduled Date (Descending)</label>
		<label class="form-checkbox"><input type="radio" <?= ('to_do_date_asc' == $ticket_sorting) ? 'checked' : '' ?> name="ticket_sorting" value="to_do_date_asc"> Scheduled Date (Ascending)</label>
	</div>
	<div class="clearfix"></div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4">Default <?= TICKET_NOUN ?> Status:</label>
	<div class="col-sm-8">
		<?php $status = get_config($dbc, "ticket_default_status");
		$status = empty($status) ? 'To Be Scheduled' : $status; ?>
		<select name="default_status" class="chosen-select-deselect">
			<?php foreach(explode(',',get_config($dbc, 'ticket_status')) as $status_option) { ?>
				<option <?= $status == $status_option ? 'selected' : '' ?> value="<?= $status_option ?>"><?= $status_option ?></option>
			<?php } ?>
		</select>
	</div>
	<div class="clearfix"></div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4">Uneditable Statuses:</label>
	<div class="col-sm-8">
		<?php $status = ','.get_config($dbc, "ticket_uneditable_status").','; ?>
		<select name="ticket_uneditable_status[]" multiple class="chosen-select-deselect">
			<?php foreach(explode(',',get_config($dbc, 'ticket_status')) as $status_option) { ?>
				<option <?= strpos($status, ','.$status_option.',') !== FALSE ? 'selected' : '' ?> value="<?= $status_option ?>"><?= $status_option ?></option>
			<?php } ?>
		</select>
	</div>
	<div class="clearfix"></div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4">Auto Archive Statuses:</label>
	<div class="col-sm-8">
		<?php $status_list = explode('#*#',get_config($dbc, "ticket_archive_status")); ?>
		<select name="ticket_archive_status" multiple data-placeholder="Select Statuses" class="chosen-select-deselect">
			<?php foreach(explode(',',get_config($dbc, 'ticket_status')) as $status_option) { ?>
				<option <?= in_array($status_option,$status_list) ? 'selected' : '' ?> value="<?= $status_option ?>"><?= $status_option ?></option>
			<?php } ?>
		</select>
	</div>
	<div class="clearfix"></div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4">Display Status:</label>
	<div class="col-sm-8">
		<?php $invoice_status = get_config($dbc, "ticket_invoice_status"); ?>
        <label class="form-checkbox"><input type="radio" name="ticket_invoice_status" value="1" <?= $invoice_status == '1' ? 'checked' : '' ?>>Status from Attached Invoice</label>
        <label class="form-checkbox"><input type="radio" name="ticket_invoice_status" value="" <?= $invoice_status == '1' ? '' : 'checked' ?>><?= TICKET_NOUN ?> Status</label>
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
	<label class="col-sm-4">Auto-Archive Completed <?= TICKET_TILE ?>:</label>
	<div class="col-sm-8">
		<?php $auto_archive = get_config($dbc, 'auto_archive'); ?>
		<label><input name="auto_archive" type="radio" value="auto_archive" <?= $auto_archive == 'auto_archive' ? 'checked' : '' ?> class="form-control"/> Yes</label>
		<label><input name="auto_archive" type="radio" value="" <?= $auto_archive == 'auto_archive' ? '' : 'checked' ?> class="form-control"/> No</label>
	</div>
	<div class="clearfix"></div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4">Auto-Archive Unbooked <?= TICKET_TILE ?>:</label>
	<div class="col-sm-4">
		<?php $auto_archive_unbooked_day = get_config($dbc, 'auto_archive_unbooked_day'); ?>
        <select class="chosen-select-deselect" name="auto_archive_unbooked_day" data-placeholder="Select Day">
            <option <?= trim($auto_archive_unbooked_day) === '' ? 'selected' : '' ?> value=" ">Never</option>
            <?php foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day_name) { ?>
                <option <?= $auto_archive_unbooked_day === $day_name ? 'selected' : '' ?> value="<?= $day_name ?>"><?= $day_name ?></option>
            <?php }
            for($i = 1; $i < 29; $i++) { ?>
                <option <?= $auto_archive_unbooked_day === $i ? 'selected' : '' ?> value="<?= $i ?>">Day <?= $i ?> of Month</option>
            <?php } ?>
        </select>
	</div>
	<div class="col-sm-4">
		<?php $auto_archive_unbooked_time = get_config($dbc, 'auto_archive_unbooked_time'); ?>
        <select class="chosen-select-deselect" name="auto_archive_unbooked_time" data-placeholder="Select Hour">
            <option <?= trim($auto_archive_unbooked_time) === '' ? 'selected' : '' ?> value=" ">Never</option>
            <?php for($i = 0; $i < 24; $i++) { ?>
                <option <?= $auto_archive_unbooked_time === "$i" ? 'selected' : '' ?> value="<?= $i ?>"><?= date('h:i a',strtotime($i.':00')) ?></option>
            <?php } ?>
        </select>
	</div>
	<div class="clearfix"></div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4">Hide Archived <?= TICKET_TILE ?>:</label>
	<div class="col-sm-8">
		<?php $ticket_exclude_archive = get_config($dbc, 'ticket_exclude_archive'); ?>
		<label><input name="ticket_exclude_archive" type="radio" value="true" <?= $ticket_exclude_archive == 'true' ? 'checked' : '' ?> class="form-control"/> Yes</label>
		<label><input name="ticket_exclude_archive" type="radio" value="" <?= $ticket_exclude_archive == 'true' ? '' : 'checked' ?> class="form-control"/> No</label>
	</div>
	<div class="clearfix"></div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4"><?= TICKET_NOUN ?> Layout:</label>
	<div class="col-sm-8">
		<?php $ticket_layout = get_config($dbc, 'ticket_layout'); ?>
		<label><input name="ticket_layout" type="radio" value="" <?= $ticket_layout != 'Accordions' ? 'checked' : '' ?> class="form-control"/> Sidebar Navigation</label>
		<label><input name="ticket_layout" type="radio" value="Accordions" <?= $ticket_layout == 'Accordions' ? 'checked' : '' ?> class="form-control"/> Accordions</label>
	</div>
	<div class="clearfix"></div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4">Summary URLs:</label>
	<div class="col-sm-8">
		<?php $ticket_summary_urls = get_config($dbc, 'ticket_summary_urls'); ?>
		<label><input name="ticket_summary_urls" type="radio" value="" <?= $ticket_summary_urls != 'slider' ? 'checked' : '' ?>>Opens as New Link</label>
		<label><input name="ticket_summary_urls" type="radio" value="slider" <?= $ticket_summary_urls == 'slider' ? 'checked' : '' ?>>Opens as Slider</label>
	</div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4">Slider Default Layout:</label>
	<div class="col-sm-8">
		<?php $ticket_slider_layout = get_config($dbc, 'ticket_slider_layout'); ?>
		<label><input name="ticket_slider_layout" type="radio" value="full" <?= $ticket_slider_layout == 'full' ? 'checked' : '' ?>>Full View</label>
		<label><input name="ticket_slider_layout" type="radio" value="accordion" <?= $ticket_slider_layout != 'full' ? 'checked' : '' ?>>Accordion View</label>
	</div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4">Text Box Style:</label>
	<div class="col-sm-8">
		<?php $ticket_textarea_style = get_config($dbc, 'ticket_textarea_style'); ?>
		<label><input name="ticket_textarea_style" type="radio" value="" <?= $ticket_textarea_style != 'no_editor' ? 'checked' : '' ?>>Default</label>
		<label><input name="ticket_textarea_style" type="radio" value="no_editor" <?= $ticket_textarea_style == 'no_editor' ? 'checked' : '' ?>>No Edit Tools</label>
	</div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4">Set Unassigned <?= TICKET_TILE?> Status to "Unassigned":</label>
	<div class="col-sm-8">
		<?php $ticket_unassigned_status = get_config($dbc, 'ticket_unassigned_status'); ?>
		<label><input name="ticket_unassigned_status" type="radio" value="1" <?= $ticket_unassigned_status == '1' ? 'checked' : '' ?>>Yes</label>
		<label><input name="ticket_unassigned_status" type="radio" value="" <?= $ticket_unassigned_status != '1' ? 'checked' : '' ?>>No</label>
	</div>
</div>
<hr>
<div class="form-group type-option">
	<label class="col-sm-4">Default New <?= TICKET_TILE?> to Logged In User:</label>
	<div class="col-sm-8">
		<?php $ticket_default_session_user = get_config($dbc, 'ticket_default_session_user'); ?>
		<label><input name="ticket_default_session_user" type="radio" value="" <?= $ticket_default_session_user != 'no_user' ? 'checked' : '' ?>>Yes</label>
		<label><input name="ticket_default_session_user" type="radio" value="no_user" <?= $ticket_default_session_user == 'no_user' ? 'checked' : '' ?>>No</label>
	</div>
</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_tile.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>