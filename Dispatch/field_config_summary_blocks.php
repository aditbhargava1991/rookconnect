<?php
/*
Dispatch Field Config - Tile Settings
*/
include ('../include.php');
checkAuthorised('dispatch'); ?>

<script type="text/javascript">
function summary_block_change() {

	var summary_blocks = [];
	$('[name="dispatch_summary_blocks[]"]:checked').not(':disabled').each(function() {
		summary_blocks.push(this.value);
	});

	$.ajax({    //create an ajax request to ajax_all.php
		type: "GET",
		url: "../Dispatch/ajax.php?action=dispatch_blocks&summary_blocks="+summary_blocks,
		dataType: "html"
	});

}
</script>

<?php $dispatch_summary_block = get_config($dbc, 'dispatch_summary_blocks');
    ?>
    <div class="form-group summary_block">
        <label class="col-sm-4 control-label">Block Name:</label>
        <div class="col-sm-7">
            <label class="form-checkbox"><input onchange="summary_block_change(this);" type="checkbox" name="dispatch_summary_blocks[]" value="on_time_summary" <?= (strpos($dispatch_summary_block, 'on_time_summary') !== FALSE ? 'checked' : '') ?>> On Time Summary</label>

            <label class="form-checkbox"><input onchange="summary_block_change(this);" type="checkbox" name="dispatch_summary_blocks[]" value="status_summary" <?= (strpos($dispatch_summary_block, 'status_summary') !== FALSE ? 'checked' : '') ?>> Status Summary</label>

            <label class="form-checkbox"><input onchange="summary_block_change(this);" type="checkbox" name="dispatch_summary_blocks[]" value="star_ratings" <?= (strpos($dispatch_summary_block, 'star_ratings') !== FALSE ? 'checked' : '') ?>> Star Ratings</label>

            <label class="form-checkbox"><input onchange="summary_block_change(this);" type="checkbox" name="dispatch_summary_blocks[]" value="status_count" <?= (strpos($dispatch_summary_block, 'status_count') !== FALSE ? 'checked' : '') ?>> Status List Summary</label>

        </div>
    </div>