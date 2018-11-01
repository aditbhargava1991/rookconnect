<script>
$(document).ready(function() {
	$('.form-group label [type=checkbox],[name=slider_button],.standard-body-content input[type=number]').change(saveFields);
	$('input[type=file]').change(uploadStamp);
});
function uploadStamp() {
	var files = new FormData();
	files.append('file',this.files[0]);
	$.ajax({
		url: 'ticket_ajax_all.php?action=set_stamp',
		method: 'POST',
		processData: false,
		contentType: false,
		data: files,
		success: function(response) {
			$('[name=stamp_image]').before('<div class="stamp_links"><a href="download/'+response+'" target="_blank">View</a> | <a class="cursor-hand" onclick="remStamp();">Delete</a></div>');
		}
	});
}
function remStamp() {
	$.get('ticket_ajax_all.php?action=set_stamp');
	$('.stamp_links').remove();
}
function saveFields() {
	var tickets_manifests = [];
	$('[name="tickets_manifests[]"]:checked').each(function() {
		tickets_manifests.push(this.value);
	});
	$.post('ticket_ajax_all.php?action=ticket_field_config', { field_name: 'ticket_manifest_fields', fields: tickets_manifests });
	$.post('ticket_ajax_all.php?action=ticket_field_config', { field_name: 'recent_manifests', fields: $('[name=recent_manifests]').val() });
	$.post('ticket_ajax_all.php?action=ticket_field_config', { field_name: 'recent_inventory', fields: $('[name=recent_inventory]').val() });
}
</script>
<?php $manifest_fields = explode(',',get_config($dbc, 'ticket_manifest_fields')); ?>
<div class="form-group">
	<label class="col-sm-4 control-label">Fields to Display on Manifests:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="checkbox" <?= in_array('sort_top', $manifest_fields) ? 'checked' : '' ?> value="sort_top" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Show Most Recent Inventory</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('sort_project', $manifest_fields) ? 'checked' : '' ?> value="sort_project" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Sort by <?= PROJECT_NOUN ?> Tabs</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('pdf_collapse', $manifest_fields) ? 'checked' : '' ?> value="pdf_collapse" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Hide Empty Columns on PDF</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('file', $manifest_fields) ? 'checked' : '' ?> value="file" style="height: 20px; width: 20px;" name="tickets_manifests[]"> File #</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('po', $manifest_fields) ? 'checked' : '' ?> value="po" style="height: 20px; width: 20px;" name="tickets_manifests[]"> PO #</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('vendor', $manifest_fields) ? 'checked' : '' ?> value="vendor" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Vendor / Shipper</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('line', $manifest_fields) ? 'checked' : '' ?> value="line" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Line Item #</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('qty', $manifest_fields) ? 'checked' : '' ?> value="qty" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Quantity</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('manual qty', $manifest_fields) ? 'checked' : '' ?> value="manual qty" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Manual Quantity</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('weight', $manifest_fields) ? 'checked' : '' ?> value="weight" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Weight</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('weight convert kg to lb', $manifest_fields) ? 'checked' : '' ?> value="weight convert kg to lb" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Weight - Convert KG to LB</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('total weight lb', $manifest_fields) ? 'checked' : '' ?> value="total weight lb" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Total Weight in LB</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('max qty', $manifest_fields) ? 'checked' : '' ?> value="max qty" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Default to Max Qty</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('hide qty', $manifest_fields) ? 'checked' : '' ?> value="hide qty" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Hide 0 Qty</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('group pieces', $manifest_fields) ? 'checked' : '' ?> value="group pieces" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Group Pieces by <?= TICKET_NOUN ?></label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('group pieces po', $manifest_fields) ? 'checked' : '' ?> value="group pieces po" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Group Pieces by PO</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('notes', $manifest_fields) ? 'checked' : '' ?> value="notes" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Line Item Notes</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('site', $manifest_fields) ? 'checked' : '' ?> value="site" style="height: 20px; width: 20px;" name="tickets_manifests[]"> <?= SITES_CAT ?> on Manifest</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('revert site', $manifest_fields) ? 'checked' : '' ?> value="revert site" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Revert <?= SITES_CAT ?> for Unused Pieces</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('req site', $manifest_fields) ? 'checked' : '' ?> value="req site" style="height: 20px; width: 20px;" name="tickets_manifests[]"> <?= SITES_CAT ?> are Mandatory</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('stamp_sign', $manifest_fields) ? 'checked' : '' ?> value="stamp_sign" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Stamp in Place of Signature</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('edit', $manifest_fields) ? 'checked' : '' ?> value="edit" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Edit Manifests</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('ticket_sort', $manifest_fields) ? 'checked' : '' ?> value="ticket_sort" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Sort by <?= TICKET_NOUN ?></label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('ticket_search', $manifest_fields) ? 'checked' : '' ?> value="ticket_search" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Search by <?= TICKET_NOUN ?></label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('mark_unused', $manifest_fields) ? 'checked' : '' ?> value="mark_unused" style="height: 20px; width: 20px;" name="tickets_manifests[]"> Removing Piece Marks As Unused</label>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Stamp Upload:</label>
	<div class="col-sm-8">
		<?php $stamp = get_config($dbc, 'stamp_upload');
		if(!empty($stamp) && file_exists('download/'.$stamp)) { ?>
			<div class="stamp_links"><a href="download/<?= $stamp ?>" target="_blank">View</a> | <a class="cursor-hand" onclick="remStamp();">Delete</a></div>
		<?php } ?>
		<input type="file" name="stamp_image" class="form-control">
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Recent Number of Items:</label>
	<div class="col-sm-4">
		<label class="col-sm-4">Manifests:</label>
		<div class="col-sm-8">
			<input type="number" min=0 step=25 value="<?= get_config($dbc, 'recent_manifests') ?>" name="recent_manifests" class="form-control">
		</div>
	</div>
	<div class="col-sm-4">
		<label class="col-sm-4">Inventory:</label>
		<div class="col-sm-8">
			<input type="number" min=0 step=25 value="<?= get_config($dbc, 'recent_inventory') ?>" name="recent_inventory" class="form-control">
		</div>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> to Include on Manifests:</label>
	<div class="col-sm-8">
		<?php foreach($ticket_tabs as $ticket_type) {
			$type_id = config_safe_str($ticket_type); ?>
			<label class="form-checkbox"><input type="checkbox" <?= in_array('type '.$type_id, $manifest_fields) || !in_array_starts('type ',$manifest_fields) ? 'checked' : '' ?> value="type <?= $type_id ?>" name="tickets_manifests[]"> <?= $ticket_type ?></label>
		<?php } ?>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label"><?= PROJECT_NOUN ?> Tabs to Display For Manifests:</label>
	<div class="col-sm-8">
		<?php foreach(explode(',',get_config($dbc, 'project_tabs')) as $project_type) {
			$type_id = config_safe_str($project_type); ?>
			<label class="form-checkbox"><input type="checkbox" <?= in_array('project_type '.$type_id, $manifest_fields) || !in_array_starts('project_type ',$manifest_fields) ? 'checked' : '' ?> value="project_type <?= $type_id ?>" name="tickets_manifests[]"> <?= $project_type ?></label>
		<?php } ?>
	</div>
</div>