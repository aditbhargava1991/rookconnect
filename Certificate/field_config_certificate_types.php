<?php error_reporting(0);
include_once('../include.php');
checkAuthorised('certificate');
$certificate_types = array_filter(explode('#*#',get_config($dbc, 'certificate_types')));
if(empty($certificate_types)) {
	$certificate_types = [''];
} ?>
<script>
$(document).ready(function() {
	$('.content-block').sortable({
		connectWith: '.content-block',
		handle: '.drag-handle',
		items: '.type_block',
		update: save_options
	});
});
function save_options() {
	var options = [];
	$('[name="certificate_type"]').each(function() {
		if(this.value != '') {
			options.push(this.value);
		}
	});
	$.ajax({
		url: '../Certificate/certificate_ajax.php?action=update_config',
		method: 'POST',
		data: {
			name: 'certificate_types',
			value: options.join('#*#')
		},
		success: function(response) {

		}
	});
}
function add_option() {
	var block = $('.type_block').last();
	var clone = $(block).clone();

	clone.find('input').val('');
	$(block).after(clone);
}
function remove_option(img) {
	if($('.type_block').length <= 1) {
		add_option();
	}

	$(img).closest('.type_block').remove();
	save_options();
}
</script>
<h3>Settings - Certificate Types</h3>
<div class="content-block main-screen-white" style="padding: 0.5em;">
	<?php foreach($certificate_types as $certificate_type) { ?>
		<div class="form-group type_block">
			<label class="col-sm-4">Certificate Type:</label>
			<div class="col-sm-6">
				<input name="certificate_type" type="text" value="<?= $certificate_type ?>" class="form-control" onchange="save_options();">
			</div>
			<div class="col-sm-2">
                <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_option();">
                <img src="../img/remove.png" class="inline-img pull-right" onclick="remove_option(this);">
                <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
			</div>
		</div>
	<?php } ?>
</div>