<?php
/*
Dashboard
*/
include_once ('../include.php');
checkAuthorised('sales');

?>
<script>
$(document).ready(function() {
	$('input,select,textarea').change(saveFields);
});

function saveFields() {
	var this_field_name = this.name;
	var ticket_fields = [];
	$('[name="sales[]"]:checked').not(':disabled').each(function() {
		ticket_fields.push(this.value);
	});
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: 'sales_ajax_all.php?action=setting_fields&ticket_fields='+ticket_fields+'&sales_lead_source='+$('[name=sales_lead_source]').val(),
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
	});
}
</script>

<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <div class="gap-top">

        <div class="form-group">
            <div class="col-sm-8 col-sm-offset-4">
                Add tabs separated by a comma in the order you want them on the dashboard:
            </div>
            <label for="company_name" class="col-sm-4 control-label">Lead Source:</label>
            <div class="col-sm-8">
              <input name="sales_lead_source" value="<?php echo get_config($dbc, 'sales_lead_source'); ?>" type="text" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">Lead Source Fields:</label>
            <div class="col-sm-8">
                <input type="checkbox" <?php if (strpos($value_config, ','."Lead Source Dropdown".',') !== FALSE) { echo " checked"; } ?> value="Lead Source Dropdown" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Lead Source Dropdown&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Lead Source Business".',') !== FALSE) { echo " checked"; } ?> value="Lead Source Business" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Lead Source Business&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Lead Source Contact".',') !== FALSE) { echo " checked"; } ?> value="Lead Source Contact" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Lead Source Contact&nbsp;&nbsp;
                <input type="checkbox" <?php if (strpos($value_config, ','."Lead Source Other".',') !== FALSE) { echo " checked"; } ?> value="Lead Source Other" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Lead Source Other&nbsp;&nbsp;
            </div>
        </div>

    </div>
</form>