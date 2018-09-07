<?php
/*
Dashboard
*/
include_once ('../include.php');
checkAuthorised('sales');

if (isset($_POST['submit'])) {
    set_config($dbc, 'sales_dashboard_users', implode(',',$_POST['dashboard_users']));

    echo '<script type="text/javascript"> window.location.replace("field_config.php?tab=dashboards"); </script>';
}
?>
<script>
$(document).ready(function() {
	$('input,select,textarea').change(saveFields);
});

function saveFields() {
	var this_field_name = this.name;

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: 'sales_ajax_all.php?action=setting_auto_archive&sales_auto_archive='+$('[name=sales_auto_archive]').val()+'&sales_auto_archive_days='+$('[name=sales_auto_archive_days]').val(),
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
	});
}
</script>

<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <div class="gap-top">

        <?php
        $sales_auto_archive = get_config($dbc, 'sales_auto_archive');
        $sales_auto_archive_days = get_config($dbc, 'sales_auto_archive_days'); ?>
        <div class="form-group">
            <label class="col-sm-4 control-label">
                <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Update the Won/Successfully Closed and Lost/Abandoned statuses under Lead Status accordion for this to work."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                Auto Archive Won/Lost Sales Leads:
            </label>
            <div class="col-sm-8">
                <input type="checkbox" name="sales_auto_archive" value="<?= $sales_auto_archive==1 ? 1 : 0 ?>" <?= $sales_auto_archive==1 ? 'checked' : '' ?> /> Enable
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Auto Archive Won/Lost Sales Leads After # of Days:</label>
            <div class="col-sm-8">
                <input type="number" name="sales_auto_archive_days" class="form-control" value="<?= !empty($sales_auto_archive_days) ? $sales_auto_archive_days : '30' ?>" min="1" step="1" />
            </div>
        </div>

    </div>
</form>