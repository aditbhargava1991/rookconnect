<?php
/*
Dashboard
*/
include_once ('../include.php');
checkAuthorised('sales');
?>
<script>
$(document).ready(function() {
	$('input,select').change(saveField);
});
function saveField() {
	if(this.name == 'sales_tile_name' || this.name == 'sales_tile_noun') {
		$.ajax({
			url: 'sales_ajax_all.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'sales_tile_name',
				value: $('[name=sales_tile_name]').val()+'#*#'+($('[name=sales_tile_noun]').val() == '' ? $('[name=sales_tile_name]').val() : $('[name=sales_tile_noun]').val())
			}
		});
	}
}
</script>

<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <div class="gap-top">

        <div class="form-group">
    		<label class="col-sm-4">Tile Name:<br /><em>Enter the name you would like the Sales tile to be labelled as.</em></label>
    		<div class="col-sm-8">
    			<input type="text" name="sales_tile_name" class="form-control" value="<?= $sales_tile ?>">
    		</div>
    	</div>
        <div class="form-group">
    		<label class="col-sm-4">Tile Noun:<br /><em>Enter the name you would like individual Sales Leads to be labelled as.</em></label>
    		<div class="col-sm-8">
    			<input type="text" name="sales_tile_noun" class="form-control" value="<?= $sales_noun ?>">
    		</div>
    	</div>

    </div>
</form>