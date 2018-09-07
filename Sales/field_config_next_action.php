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

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: 'sales_ajax_all.php?action=setting_next_action&sales_next_action='+$('[name=sales_next_action]').val(),
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
            <label for="company_name" class="col-sm-4 control-label">Next Action:</label>
            <div class="col-sm-8">
              <input name="sales_next_action" value="<?php echo get_config($dbc, 'sales_next_action'); ?>" type="text" class="form-control">
            </div>
        </div>

    </div>
</form>