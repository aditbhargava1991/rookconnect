<script>
$(document).ready(function() {
	setSave();
});
function setSave() {
	$('.form-group input,.form-group select').not('[name=stop_types]').off('change',saveTile).change(saveTile);
	$('[name=stop_types]').off('change',saveStops).change(saveStops);
}
function saveTile() {
	var optimize_dont_count_warehouse = '';
	if($('[name="optimize_dont_count_warehouse"]').is(':checked')) {
		optimize_dont_count_warehouse = 1;
	}

	$.ajax({
		url: '../Optimize/optimize_ajax.php?action=tile_settings',
		method: 'POST',
		data: {
            optimize_dont_count_warehouse: optimize_dont_count_warehouse
        },
		success: function(response) {

		}
	});
}
function saveStops() {
    var optimize_stop_types = [];
    $('[name=stop_types]:checked').each(function() {
        optimize_stop_types.push(this.value);
    });

	$.ajax({
		url: '../Optimize/optimize_ajax.php?action=stop_types',
		method: 'POST',
		data: {
            optimize_stop_types: optimize_stop_types.join(',')
        },
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

<div class="form-group">
	<label class="col-sm-4">Delivery Stops to Count:</label>
	<div class="col-sm-8">
		<?php $optimize_stop_types = explode(',',get_config($dbc, 'optimize_stop_types'));
        $stop_types = explode(',',get_config($dbc, 'delivery_types'));
        foreach($stop_types as $type) { ?>
            <label class="form-checkbox"><input type="checkbox" name="stop_types" value="<?= $type ?>" <?= in_array($type, $optimize_stop_types) ? 'checked' : '' ?>> <?= $type ?></label>
        <?php } ?>
	</div>
</div>