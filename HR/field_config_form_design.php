<?php $default_id = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `hrid` FROM `hr` WHERE `form`='default_hr_form_settings'"))['hrid'];
$default_id = $default_id > 0 ? $default_id : 0;
$hrid = $_GET['hrid'] > 0 ? $_GET['hrid'] : $default_id;
$hr_details = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `pdf_logo`, `pdf_header`,`pdf_footer`,`email_subject`,`email_message`,`completed_recipient`,`completed_subject`,`completed_message`,`approval_subject`,`approval_message`,`rejected_subject`,`rejected_message` FROM `hr` WHERE `hrid` IN ('$hrid',0) ORDER BY `hrid` DESC")); ?>
<script>
$(document).ready(function() {
	$('[data-id]').change(saveFields);
});
function saveFields() {
	if(this.type == 'file') {
		var input = this;
		var file = new FormData();
		var filename = this.files[0].name;
		file.append('file',this.files[0]);
		file.append('name',this.name);
		file.append('hrid',$(this).data('id'));
		$.ajax({
			url: 'hr_ajax.php?action=hr_upload',
			method: 'POST',
			processData: false,
			contentType: false,
			data: file,
			xhr: function() {
				var num_label = i;
				$(input).hide().after('<div class="hr_logo_progress" style="background-color:#000;height:1.5em;padding:0;position:relative;width:100%;"><div style="background-color:#444;height:1.5em;left:0;position:absolute;top:0;" id="progress_'+num_label+'"></div><span id="label_'+num_label+'" style="color:#fff;left:0;position:absolute;text-align:center;top:0;width:100%;z-index:1;">'+filename+': 0%</span></div><div class="clearfix"></div>');
				var xhr = new window.XMLHttpRequest();
				xhr.upload.addEventListener("progress", function(e){
					var percentComplete = Math.round(e.loaded / e.total * 100);
					$('#label_'+num_label).text(filename+': '+percentComplete+'%');
					$('#progress_'+num_label).css('width',percentComplete+'%');
					console.log(filename+': '+percentComplete+'%');
				}, false);

				return xhr;
			},
			success: function(response) {
				$('a.hr_logo').attr('href','download/'+response).text(response);
				$('.hr_logo_progress').remove();
				$(input).val('').show();
			}
		});
	} else {
		$.ajax({
			url: 'hr_ajax.php?action=form_layout',
			method: 'POST',
			data: {
				hrid: $(this).data('id'),
				form: $('[name=form_id] option:selected').data('form'),
				name: this.name,
				value: this.value
			},
			success: function(response) {
				if(response > 0) {
					$('[name=pdf_logo]').closest('.form-group').show();
					$('[data-id]').data('id',response);
				}
			}
		});
	}
}
</script>
<div class="standard-body-title">
    <h1>Form Design</h1>
</div>
<div class="standard-body-content">
	<div class="form-group">
		<label class="col-sm-4 control-label">Form:</label>
		<div class="col-sm-8">
			<select name="form_id" class="chosen-select-deselect" onchange="window.location.replace('?settings=form_design&hrid='+this.value);">
				<option <?= $hrid == $default_id ? 'selected' : '' ?> data-form="default_hr_form_settings" value="<?= $default_id ?>">Default Form Settings</option>
				<?php $forms = mysqli_query($dbc, "SELECT `hrid`, `form`, `category`, `heading_number`, `heading`, `sub_heading_number`, `sub_heading`, `third_heading_number`, `third_heading` FROM `hr` ORDER BY `category`, LPAD(`heading_number`, 100, 0), LPAD(`sub_heading_number`, 100, 0), LPAD(`third_heading_number`, 100, 0)");
				while($form = mysqli_fetch_assoc($forms)) { ?>
					<option <?= $form['hrid'] == $_GET['hrid'] ? 'selected' : '' ?> data-form="<?= $form['form'] ?>" value="<?= $form['hrid'] ?>"><?= ($form['third_heading_number'] != '' ? $form['third_heading_number'].' '.$form['third_heading'] : ($form['sub_heading_number'] != '' ? $form['sub_heading_number'].' '.$form['sub_heading'] : $form['heading_number'].' '.$form['heading'])).' ('.$form['category'].')' ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<h2>PDF Settings</h2>
	<div class="form-group" style="<?= $hrid > 0 ? '' : 'display:none;' ?>">
		<label class="col-sm-4 control-label">PDF Logo:</label>
		<div class="col-sm-8">
			<input type="file" name="pdf_logo" data-id="<?= $hrid ?>">
			<?= $hr_details['pdf_logo'] != '' ? '<a href="download/'.$hr_details['pdf_logo'].'" target="_blank" class="hr_logo">'.$hr_details['pdf_logo'].'</a>' : '<a class="hr_logo"></a>' ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">PDF Header:</label>
		<div class="col-sm-8">
			<textarea name="pdf_header" data-id="<?= $hrid ?>"><?= html_entity_decode($hr_details['pdf_header']) ?></textarea>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">PDF Footer:</label>
		<div class="col-sm-8">
			<textarea name="pdf_footer" data-id="<?= $hrid ?>"><?= html_entity_decode($hr_details['pdf_footer']) ?></textarea>
		</div>
	</div>
	<h2>Assignment Email</h2>
	<div class="form-group">
		<label class="col-sm-4 control-label">Subject:</label>
		<div class="col-sm-8">
			<input class="form-control" data-id="<?= $hrid ?>" name="email_subject" value="<?= $hr_details['email_subject'] ?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Body:</label>
		<div class="col-sm-8">
			<textarea name="email_message" data-id="<?= $hrid ?>"><?= html_entity_decode($hr_details['email_message']) ?></textarea>
		</div>
	</div>
	<h2>Submission Email</h2>
	<div class="form-group">
		<label class="col-sm-4 control-label">Recipient:</label>
		<div class="col-sm-8">
			<select class="chosen-select-deselect form-control" data-placeholder="Select Staff" name="completed_recipient"data-id="<?= $hrid ?>" ><option></option>
				<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `email_address` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `email_address` != ''")) as $contact) { ?>
					<option <?= $contact['contactid'] == $hr_details['completed_recipient'] ? 'selected' : '' ?> value="<?= $contact['contactid'] ?>"><?= $contact['first_name'].' '.$contact['last_name'].' ('.$contact['email_address'].')' ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Subject:</label>
		<div class="col-sm-8">
			<input class="form-control" data-id="<?= $hrid ?>" name="completed_subject" value="<?= $hr_details['completed_subject'] ?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Body:</label>
		<div class="col-sm-8">
			<textarea name="completed_message" data-id="<?= $hrid ?>"><?= html_entity_decode($hr_details['completed_message']) ?></textarea>
		</div>
	</div>
	<h2>Approval Email</h2>
	<div class="form-group">
		<label class="col-sm-4 control-label">Subject:</label>
		<div class="col-sm-8">
			<input class="form-control" data-id="<?= $hrid ?>" name="approval_subject" value="<?= $hr_details['approval_subject'] ?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Body:</label>
		<div class="col-sm-8">
			<textarea name="approval_message" data-id="<?= $hrid ?>"><?= html_entity_decode($hr_details['approval_message']) ?></textarea>
		</div>
	</div>
	<h2>Rejection Email</h2>
	<div class="form-group">
		<label class="col-sm-4 control-label">Subject:</label>
		<div class="col-sm-8">
			<input class="form-control" data-id="<?= $hrid ?>" name="rejected_subject" value="<?= $hr_details['rejected_subject'] ?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Body:</label>
		<div class="col-sm-8">
			<textarea name="rejected_message" data-id="<?= $hrid ?>"><?= html_entity_decode($hr_details['rejected_message']) ?></textarea>
		</div>
	</div>
</div>