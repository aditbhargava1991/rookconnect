<?php include('../Ticket/ticket_log_templates/template_a_fields.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
	$('#template_a_config').find('input,select,textarea').change(function() {
		saveTemplateFields();
	});
});
function saveTemplateFields() {
	var field_data = new FormData();
	field_data.append('template', 'template_a');
	field_data.append('header_logo', $('[name="header_logo"]')[0].files[0]);
	field_data.append('header', $('[name="header"]').val());
	field_data.append('header_logo_align', $('[name="header_logo_align"] option:selected').val());
	field_data.append('footer', $('[name="footer"]').val());
	var fields = [];
	$('[name="log_fields[]"]').each(function() {
		if($(this).is(':checked')) {
			fields.push(this.value);
		}
	});
	fields = JSON.stringify(fields);
	field_data.append('fields', fields);

	$.ajax({
		processData: false,
		contentType: false,
		url: '../Ticket/ticket_ajax_all.php?action=ticket_log_config',
		type: 'POST',
		data: field_data,
		success: function(response) {
			response_arr = response.split('*#*');
			if(response_arr[0] == 'header_logo') {
				$('[name="header_logo"]').val('');
				$('.header_logo_url').html('<a href="'+response_arr[1]+'" target="_blank">View</a> | <a href="" onclick="deleteLogo(\'header\'); return false;">Delete</a>');
			}
		}
	});
	reloadPreview();
}
function deleteLogo(logo) {
	if(confirm('Are you sure you want to delete this logo?')) {
		var template = 'template_a';
		$.ajax({
			url: '../Ticket/ticket_ajax_all.php?action=ticket_log_delete_logo',
			type: 'POST',
			data: { template: template, logo: logo },
			success: function(response) {
				if(logo == 'header') {
					$('.header_logo_url').html('');
				}
			}
		});
	}
}
</script>
<div id="template_a_config">
	<form class="form-horizontal" action="" method="POST">
		<h4>Header &amp; Footer</h4>
		<div class="form-group">
			<label class="col-sm-4 control-label">Header Logo:</label>
			<div class="col-sm-8">
				<div class="header_logo_url">
					<?php if(!empty($header_logo) && file_exists('download/'.$header_logo)) { ?>
						<a href="download/<?= $header_logo ?>" target="_blank">View</a> | <a href="" onclick="deleteLogo('header'); return false;">Delete</a>
					<?php } ?>
				</div>
				<input type="file" name="header_logo" data-filename-placement="inside" class="form-control" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Header Logo Align:</label>
			<div class="col-sm-8">
                <select name="header_logo_align" class="chosen-select-deselect form-control">
                    <option></option>
                    <option <?= $header_logo_align == 'L' ? 'selected' : '' ?> value="L">Left</option>
                    <option <?= $header_logo_align == 'C' ? 'selected' : '' ?> value="C">Center</option>
                    <option <?= $header_logo_align == 'R' ? 'selected' : '' ?> value="R">Right</option>
                </select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Header Text:</label>
			<label class="col-sm-8"><small><em>Enter [NAME] for <?= TICKET_NOUN ?> Name. Enter [DATE] for date. Enter [DROPOFF] for Drop Off Time. Enter [PICKUP] for Pick Up Time.</em></small></label>
			<div class="col-sm-12">
				<textarea name="header"><?= html_entity_decode($header) ?></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Footer Text:</label>
			<div class="col-sm-12">
				<textarea name="footer"><?= html_entity_decode($footer) ?></textarea>
			</div>
		</div>
		<h4>Fields</h4>
		<div class="form-group">
			<label class="col-sm-4 control-label">Program Notes:</label>
			<div class="col-sm-8">
				<label class="form-checkbox"><input type="checkbox" name="log_fields[]" value="Program Notes" <?= strpos($fields, ',Program Notes,') !== FALSE ? 'checked' : '' ?> /> Enable</label>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Members Table:</label>
			<div class="col-sm-8">
				<label class="form-checkbox"><input type="checkbox" name="log_fields[]" value="Members Table" <?= strpos($fields, ',Members Table,') !== FALSE ? 'checked' : '' ?> /> Enable</label>
				<div class="block-group">
					<label class="form-checkbox"><input type="checkbox" name="log_fields[]" value="Members Last Name" <?= strpos($fields, ',Members Last Name,') !== FALSE ? 'checked' : '' ?> /> Last Name</label>
					<label class="form-checkbox"><input type="checkbox" name="log_fields[]" value="Members First Name" <?= strpos($fields, ',Members First Name,') !== FALSE ? 'checked' : '' ?> /> First Name</label>
					<label class="form-checkbox"><input type="checkbox" name="log_fields[]" value="Members Contact Numbers" <?= strpos($fields, ',Members Contact Numbers,') !== FALSE ? 'checked' : '' ?> /> Contact Numbers</label>
					<label class="form-checkbox"><input type="checkbox" name="log_fields[]" value="Members Drop Off" <?= strpos($fields, ',Members Drop Off,') !== FALSE ? 'checked' : '' ?> /> Drop Off</label>
					<label class="form-checkbox"><input type="checkbox" name="log_fields[]" value="Members Pick Up" <?= strpos($fields, ',Members Pick Up,') !== FALSE ? 'checked' : '' ?> /> Pick Up</label>
					<label class="form-checkbox"><input type="checkbox" name="log_fields[]" value="Members Hours" <?= strpos($fields, ',Members Hours,') !== FALSE ? 'checked' : '' ?> /> Hours</label>
					<label class="form-checkbox"><input type="checkbox" name="log_fields[]" value="Members Notes" <?= strpos($fields, ',Members Notes,') !== FALSE ? 'checked' : '' ?> /> Participant Notes</label>
					<label class="form-checkbox"><input type="checkbox" name="log_fields[]" value="Members Age" <?= strpos($fields, ',Members Age,') !== FALSE ? 'checked' : '' ?> /> Age</label>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Staff Table:</label>
			<div class="col-sm-8">
				<label class="form-checkbox"><input type="checkbox" name="log_fields[]" value="Staff Table" <?= strpos($fields, ',Staff Table,') !== FALSE ? 'checked' : '' ?> /> Enable</label>
				<div class="block-group">
					<label class="form-checkbox"><input type="checkbox" name="log_fields[]" value="Staff Name" <?= strpos($fields, ',Staff Name,') !== FALSE ? 'checked' : '' ?> /> Name</label>
					<label class="form-checkbox"><input type="checkbox" name="log_fields[]" value="Staff Duties" <?= strpos($fields, ',Staff Duties,') !== FALSE ? 'checked' : '' ?> /> Duties</label>
					<label class="form-checkbox"><input type="checkbox" name="log_fields[]" value="Staff Time In" <?= strpos($fields, ',Staff Time In,') !== FALSE ? 'checked' : '' ?> /> Time In</label>
					<label class="form-checkbox"><input type="checkbox" name="log_fields[]" value="Staff Time Out" <?= strpos($fields, ',Staff Time Out,') !== FALSE ? 'checked' : '' ?> /> Time Out</label>
					<label class="form-checkbox"><input type="checkbox" name="log_fields[]" value="Staff Hours" <?= strpos($fields, ',Staff Hours,') !== FALSE ? 'checked' : '' ?> /> Hours</label>
					<label class="form-checkbox"><input type="checkbox" name="log_fields[]" value="Staff Emergency Number" <?= strpos($fields, ',Staff Emergency Number,') !== FALSE ? 'checked' : '' ?> /> Emergency Number</label>
					<label class="form-checkbox"><input type="checkbox" name="log_fields[]" value="Staff PC Initial" <?= strpos($fields, ',Staff PC Initial,') !== FALSE ? 'checked' : '' ?> /> PC Initial</label>
					<label class="form-checkbox"><input type="checkbox" name="log_fields[]" value="Staff Notes" <?= strpos($fields, ',Staff Notes,') !== FALSE ? 'checked' : '' ?> /> Medical Information/Special Comments</label>
				</div>
			</div>
		</div>
	</form>
</div>