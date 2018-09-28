<?php include_once('../include.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
	$('.color_block input').change(saveFields);
});
function colorCodeChange(sel) {
	if(sel.name == 'delivery_color[]') {
	    $(sel).closest('.color_block').find('.color_hex_visual').val(sel.value);
	} else {
	    $(sel).closest('.color_block').find('.color_hex').val(sel.value);
	}
}
</script>
<?php $delivery_types = get_config($dbc, 'delivery_types');
if(!empty($delivery_types)) { ?>
	<div class="block-group">
		<h3>Delivery Tab Calendar/Planner Colors</h3>
		<?php foreach(explode(',',$delivery_types) as $delivery_type) {
			$delivery_color = mysqli_fetch_array(mysqli_query($dbc, "SELECT `color` FROM `field_config_ticket_delivery_color` WHERE `delivery` = '$delivery_type' AND `delivery` != 'Warehouse'"))['color']; ?>
			<div class="form-group color_block">
				<label class="col-sm-4"><?= $delivery_type ?>:</label>
				<div class="col-sm-1">
	                <input onchange="colorCodeChange(this);" class="form-control color_hex_visual" type="color" name="delivery_color_visual[]" value="<?= !empty($delivery_color) ? $delivery_color : '#dddddd' ?>">
				</div>
				<div class="col-sm-7">
	                <input type="text" data-delivery="<?= $delivery_type ?>" name="delivery_color[]" onchange="colorCodeChange(this);" class="form-control color_hex" value="<?= !empty($delivery_color) ? $delivery_color : '#dddddd' ?>">
				</div>
			</div>
		<?php }
		$delivery_color = mysqli_fetch_array(mysqli_query($dbc, "SELECT `color` FROM `field_config_ticket_delivery_color` WHERE `delivery` = 'Warehouse'"))['color']; ?>
		<div class="form-group color_block">
			<label class="col-sm-4">Warehouses:</label>
			<div class="col-sm-1">
                <input onchange="colorCodeChange(this);" class="form-control color_hex_visual" type="color" name="delivery_color_visual[]" value="<?= !empty($delivery_color) ? $delivery_color : '#dddddd' ?>">
			</div>
			<div class="col-sm-7">
                <input type="text" data-delivery="Warehouse" name="delivery_color[]" onchange="colorCodeChange(this);" class="form-control color_hex" value="<?= !empty($delivery_color) ? $delivery_color : '#dddddd' ?>">
			</div>
		</div>
	</div>
<?php } ?>