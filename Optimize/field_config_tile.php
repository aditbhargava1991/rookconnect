<script>
$(document).ready(function() {
	setSave();
});
function setSave() {
	$('.form-group input,.form-group select').off('change',saveTile).change(saveTile);
}
function saveTile() {
	var optimize_dont_count_warehouse = '';
	if($('[name="optimize_dont_count_warehouse"]').is(':checked')) {
		optimize_dont_count_warehouse = 1;
	}

	$.ajax({
		url: '../Optimize/optimize_ajax.php?action=tile_settings',
		method: 'POST',
		data: { optimize_dont_count_warehouse: optimize_dont_count_warehouse },
		success: function(response) {

		}
	});
}
</script>

<div class="form-group">
	<label class="col-sm-4">Don't Count Warehouse As Delivery Stop:</label>
	<div class="col-sm-8">
		<?php $optimize_dont_count_warehouse = get_config($dbc, 'optimize_dont_count_warehouse'); ?>
		<label class="form-checkbox"><input type="checkbox" name="optimize_dont_count_warehouse" value="1" <?= $optimize_dont_count_warehouse == 1 ? 'checked' : '' ?>> Enable</label>
	</div>
</div>