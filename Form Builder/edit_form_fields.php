<?php //Form Builder Fields
include_once('../Form Builder/field_values.php');
?>
<script type="text/javascript" src="formbuilder.js"></script>
<script type="text/javascript">
function deleteField(img) {
	if(confirm('Are you sure you want to delete this field?')) {
		var field_id = $(img).closest('.field_sortable').data('fieldid');
		$.ajax({
			url: '../Form Builder/form_ajax.php?fill=delete_field',
			type: 'POST',
			data: { field_id: field_id },
			success: function(response) {
				$(img).closest('.field_sortable').remove();
				sortFields();
			}
		});
	}
}
</script>
<div class="standard-collapsible tile-sidebar" style="height: 100%;">
	<ul class="sidebar field_sidebar">
		<?php foreach($field_types as $field_type => $field_desc) { ?>
			<div class="block-item field_draggable" data-fieldtype="<?= $field_type ?>" data-description="<?= $field_desc ?>">
				<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="<?= $field_note[$field_type] ?>"><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<?= $field_desc ?>
			</div>
		<?php } ?>
	</ul>
</div>
<div class="scale-to-fill has-main-screen">
	<div class="main-screen">
		<div class="form-horizontal col-sm-12">
			<h3>Form Fields (Drag fields here)</h3>
			<div class="field_div" style="padding-bottom: 10em;">
				<?php $form_fields = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `deleted` = 0 AND `type` != 'OPTION' AND `form_id` = '$formid' ORDER BY `sort_order` ASC"),MYSQLI_ASSOC);
					foreach ($form_fields as $form_field) { ?>
						<div class="block-item field_sortable" data-fieldid="<?= $form_field['field_id'] ?>">
							<img src="../img/remove.png" class="inline-img" onclick="deleteField(this);" style="cursor: pointer;">
							<?php if($form_field['type'] == 'HR') {
								echo $field_types[$form_field['type']];
							} else { ?>
								<a href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Form Builder/edit_form_field_details.php?field_id=<?= $form_field['field_id'] ?>', 'auto', true, true, $('.main-screen').height()); return false;"><?= ($form_field['type'] == 'SELECT_CUS' ? $field_types['SELECT'] : $field_types[$form_field['type']]) ?>: <?= $form_field['label'] ?></a>&nbsp;
							<?php } ?>
							<img class='drag-handle' src='<?= WEBSITE_URL ?>/img/icons/drag_handle.png' style='float: right; width: 2em;'>
						</div>
					<?php }
				?>
			</div>
		</div>
	</div>
</div>