<?php //Form Builder Main ?>
<script type="text/javascript">
$(document).ready(function() {
	$('[name="form_name"]').change(function() {
		var formid = $('#formid').val();
		var is_template = $('#is_template').val();
		var form_name = this.value;
		$.ajax({
			url: '../Form Builder/form_ajax.php?fill=form_name',
			type: 'POST',
			data: { formid: formid, form_name: form_name, is_template: is_template },
			success: function(response) {
				if (formid == '' || formid == undefined || formid == 0) {
					$('#formid').val(response);
				}
			}
		});
	});
});
function loadTemplate() {
	if($('#formid').val() != '') {
		overlayIFrameSlider('<?= WEBSITE_URL ?>/Form Builder/load_template.php?formid='+$('#formid').val(), 'auto', false, true, $('.main-screen').height());
	} else {
		alert ('Please choose a Name before loading a Template in.');
	}
}
</script>
<div class="standard-collapsible tile-sidebar" style="height: 100%;">
	<ul class="sidebar">
		<a href="" onclick="return false;"><li class="active">Form Builder Information</li></a>
	</ul>
</div>
<div class="scale-to-fill has-main-screen">
	<div class="main-screen">
		<div class="form-horizontal col-sm-12">
			<h3>Form Builder Information</h3>
			<div class="form-group">
				<label class="col-sm-4 control-label">Name:</label>
				<div class="col-sm-8">
					<input type="text" name="form_name" value="<?= $form['name'] ?>" class="form-control">
				</div>
			</div>
			<?php $use_templates = mysqli_fetch_array(mysqli_query($dbc, "SELECT `use_templates` FROM `field_config_user_forms`"))['use_templates'];
			if ($is_template != 1 && $use_templates == 1) { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label">Template:</label>
					<div class="col-sm-8">
						<a href="" onclick="loadTemplate(); return false;" class="btn brand-btn">Load Template</a>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>