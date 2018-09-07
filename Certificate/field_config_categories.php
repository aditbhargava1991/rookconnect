<?php error_reporting(0);
include_once('../include.php');
checkAuthorised('certificate');
$certificate_categories = array_filter(explode('#*#',get_config($dbc, 'certificate_categories')));
if(empty($certificate_categories)) {
	$certificate_categories = [''];
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
	$('[name="certificate_categories"]').each(function() {
		if(this.value != '') {
			options.push(this.value);
		}
	});
	$.ajax({
		url: '../Certificate/certificate_ajax.php?action=update_config',
		method: 'POST',
		data: {
			name: 'certificate_categories',
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
<h3>Settings - Categories</h3>
<div class="content-block main-screen-white" style="padding: 0.5em;">
	<?php foreach($certificate_categories as $certificate_category) { ?>
		<div class="form-group type_block">
			<label class="col-sm-4">Category:</label>
			<div class="col-sm-6">
				<input name="certificate_categories" type="text" value="<?= $certificate_category ?>" class="form-control" onchange="save_options();">
			</div>
			<div class="col-sm-2">
                <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_option();">
                <img src="../img/remove.png" class="inline-img pull-right" onclick="remove_option(this);">
                <img src="../img/icons/drag_handle.png" class="inline-img drag-handle pull-right">
			</div>
		</div>
	<?php } ?>
</div>