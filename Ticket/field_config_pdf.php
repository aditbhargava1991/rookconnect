<script>
$(document).ready(function() {
	$('input,textarea,select').change(saveField);
	$('input[name="ticket_pdf_logo"]').change(function() {
		saveLogo();
	});
});
function saveField() {
	if(this.type == 'checkbox') {
		if($(this).is(':checked')) {
			field_value = $(this).val();
		} else {
			field_value = '';
		}
	} else {
		field_value = this.value;
	}
	$.ajax({
		url: 'ticket_ajax_all.php?action=ticket_text',
		method: 'POST',
		data: {
			config: this.name,
			text: field_value
		}
	});
}
function saveLogo() {
	var field_data = new FormData();
	field_data.append('ticket_pdf_logo', $('[name="ticket_pdf_logo"]')[0].files[0]);

	$.ajax({
		processData: false,
		contentType: false,
		url: 'ticket_ajax_all.php?action=ticket_pdf_logo',
		method: 'POST',
		data: field_data,
		success: function(response) {
			$('[name="ticket_pdf_logo"]').val('');
			$('.ticket_logo_url').html('<a href="'+response+'">View</a> | <a href="" onclick="deleteLogo(); return false;">Delete</a>');
		}
	});
}
function deleteLogo() {
	$.ajax({
		url: 'ticket_ajax_all.php?action=ticket_text',
		method: 'POST',
		data: {
			config: 'ticket_pdf_logo',
			text: ''
		},
		success: function(response) {
			$('.ticket_logo_url').html('');
		}
	});
}
</script>
<div class="form-group">
	<label class="col-sm-4 control-label">Page Orientation:</label>
	<div class="col-sm-8">
		<select name="ticket_pdf_orientation" class="chosen-select-deselect form-control">
			<?php $ticket_pdf_orientation = get_config($dbc, 'ticket_pdf_orientation');
			if(empty($ticket_pdf_orientation)) {
				$ticket_pdf_orientation = 'P';
			} ?>
			<option value="P" <?= $ticket_pdf_orientation == 'P' ? 'selected' : '' ?>>Portrait</option>
			<option value="L" <?= $ticket_pdf_orientation == 'L' ? 'selected' : '' ?>>Landscape</option>
		</select>
	</div>
</div>
<hr>
<div class="form-group">
	<label class="col-sm-4 control-label">Hide Blank Fields (Except Blank <?= TICKET_NOUN ?> Forms):</label>
	<div class="col-sm-8">
		<?php $ticket_pdf_hide_blank = get_config($dbc, 'ticket_pdf_hide_blank'); ?>
		<label class="form-checkbox"><input type="checkbox" <?php if ($ticket_pdf_hide_blank == 1) { echo " checked"; } ?> value="1" style="height: 20px; width: 20px;" name="ticket_pdf_hide_blank"> Enable</label>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Create <?= TICKET_NOUN ?> for Blank <?= TICKET_NOUN ?> Forms:<br /><em>Assign ID from <?= TICKET_TILE ?> and use for Blank <?= TICKET_NOUN ?> Forms</em></label>
	<div class="col-sm-8">
		<?php $ticket_pdf_blank_new_id = get_config($dbc, 'ticket_pdf_blank_new_id'); ?>
		<label class="form-checkbox"><input type="checkbox" <?php if ($ticket_pdf_blank_new_id == 1) { echo " checked"; } ?> value="1" style="height: 20px; width: 20px;" name="ticket_pdf_blank_new_id"> Enable</label>
	</div>
</div>
<hr>
<div class="form-group">
	<label class="col-sm-4 control-label">Left Header:</label>
	<div class="col-sm-12">
		<textarea name="ticket_pdf_header_left"><?= get_config($dbc, 'ticket_pdf_header_left') ?></textarea>
	</div>
</div>
<hr>
<div class="form-group">
	<label class="col-sm-4 control-label">Center Header:</label>
	<div class="col-sm-12">
		<textarea name="ticket_pdf_header_center"><?= get_config($dbc, 'ticket_pdf_header_center') ?></textarea>
	</div>
</div>
<hr>
<div class="form-group">
	<label class="col-sm-4 control-label">Right Header:</label>
	<div class="col-sm-12">
		<textarea name="ticket_pdf_header_right"><?= get_config($dbc, 'ticket_pdf_header_right') ?></textarea>
	</div>
</div>
<hr>
<div class="form-group">
	<label class="col-sm-4 control-label">Footer:</label>
	<div class="col-sm-12">
		<textarea name="ticket_pdf_footer"><?= get_config($dbc, 'ticket_pdf_footer') ?></textarea>
	</div>
</div>
<hr>
<div class="form-group">
	<label class="col-sm-4 control-label">Logo:</label>
	<div class="col-sm-8">
		<div class="ticket_logo_url">
			<?php $pdf_logo = get_config($dbc, 'ticket_pdf_logo');
			if($pdf_logo != '' && file_exists('download/'.$pdf_logo)) { ?>
				<a href="download/<?= $pdf_logo ?>" target="_blank">View</a> | <a href="" onclick="deleteLogo(); return false;">Delete</a>
			<?php } ?>
		</div>
		<input type="file" class="form-control" name="ticket_pdf_logo">
	</div>
</div>
<hr>
<div class="form-group">
	<label class="col-sm-4 control-label">Logo Align:</label>
	<div class="col-sm-8">
		<?php $ticket_pdf_logo_align = get_config($dbc, 'ticket_pdf_logo_align');
		if(empty($ticket_pdf_logo_align)) {
			$ticket_pdf_logo_align = 'C';
		} ?>
        <select name="ticket_pdf_logo_align" class="chosen-select-deselect form-control">
	        <option></option>
	        <option <?= $ticket_pdf_logo_align == 'L' ? 'selected' : '' ?> value="L">Left</option>
	        <option <?= $ticket_pdf_logo_align == 'C' ? 'selected' : '' ?> value="C">Center</option>
	        <option <?= $ticket_pdf_logo_align == 'R' ? 'selected' : '' ?> value="R">Right</option>
	    </select>
	</div>
</div>