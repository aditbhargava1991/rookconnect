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
		url: 'sales_ajax_all.php?action=setting_lead_status&sales_lead_status='+$('[name=sales_lead_status]').val()+'&lead_status_won='+$('[name=lead_status_won]').val()+'&lead_status_lost='+$('[name=lead_status_lost]').val()+'&lead_status_retained='+$('[name=lead_status_retained]').val()+'&lead_convert_to='+$('[name=lead_convert_to]').val(),
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
	});
}
</script>

<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <div class="gap-top">

         <div class="form-group"><?php
            $lead_statuses = explode(",", get_config($dbc, 'sales_lead_status')); ?>
            <div class="col-sm-8 col-sm-offset-4">
                Add tabs separated by a comma in the order you want them on the dashboard:
            </div>
            <label for="company_name" class="col-sm-4 control-label">Lead Status:</label>
            <div class="col-sm-8">
              <input name="sales_lead_status" value="<?= get_config($dbc, 'sales_lead_status'); ?>" type="text" class="form-control">
            </div>

            <label for="company_name" class="col-sm-4 control-label"><span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Select the Lead Status that will be used for won/successfully closed sales leads."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Won/Successfully Closed Status:</label>
            <div class="col-sm-8"><?php
                $get_config_won_status = get_config($dbc, 'lead_status_won'); ?>
                <select name="lead_status_won" class="form-control">
                    <option value="">Select Status</option><?php
                    foreach($lead_statuses as $value):
                        $selected = ($get_config_won_status == $value) ? 'selected="selected"' : ''; ?>
                        <option <?= $selected; ?> value="<?= $value; ?>"><?= $value; ?></option><?php
                    endforeach; ?>
                </select>
            </div>

            <label for="company_name" class="col-sm-4 control-label"><span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Select the Lead Status that will be used for lost/abandonded sales leads."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Lost/Abandoned Status:</label>
            <div class="col-sm-8"><?php
                $get_config_lost_status = get_config($dbc, 'lead_status_lost'); ?>
                <select name="lead_status_lost" class="form-control">
                    <option value="">Select Status</option><?php
                    foreach($lead_statuses as $value):
                        $selected = ($get_config_lost_status == $value) ? 'selected="selected"' : ''; ?>
                        <option <?= $selected; ?> value="<?= $value; ?>"><?= $value; ?></option><?php
                    endforeach; ?>
                </select>
            </div>

            <label for="company_name" class="col-sm-4 control-label"><span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Select the Lead Status that will be used for successful sales leads that will be retained for future use."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Retained Successful <?= SALES_NOUN ?> Status:</label>
            <div class="col-sm-8"><?php
                $get_config_retained = get_config($dbc, 'lead_status_retained'); ?>
                <select name="lead_status_retained" class="form-control">
                    <option value="">Select Status</option><?php
                    foreach($lead_statuses as $value):
                        $selected = ($get_config_retained == $value) ? 'selected="selected"' : ''; ?>
                        <option <?= $selected; ?> value="<?= $value; ?>"><?= $value; ?></option><?php
                    endforeach; ?>
                </select>
            </div>

            <label for="company_name" class="col-sm-4 control-label"><span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Select the Contact category a Sales Lead will convert to upon successful closure."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Successful Sales Lead:</label>
            <div class="col-sm-8"><?php
                $contacts_tabs = explode(',',get_config($dbc, 'contacts_tabs'));
                $lead_convert_to = get_config($dbc, 'lead_convert_to'); ?>
                <select name="lead_convert_to" class="form-control">
                    <option value="">Select Contact Category</option><?php
                    foreach($contacts_tabs as $contacts_tab):
                        $selected = ($lead_convert_to == $contacts_tab) ? 'selected="selected"' : ''; ?>
                        <option <?= $selected; ?> value="<?= $contacts_tab; ?>"><?= $contacts_tab; ?></option><?php
                    endforeach; ?>
                </select>
            </div>
        </div>

    </div>
</form>