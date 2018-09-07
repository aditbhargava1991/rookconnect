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
	$('[name="sales_dashboard[]"]:checked').not(':disabled').each(function() {
		ticket_fields.push(this.value);
	});
	var dashboard_users = [];
	$('[name="dashboard_users[]"]:checked').not(':disabled').each(function() {
		dashboard_users.push(this.value);
	});
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: 'sales_ajax_all.php?action=setting_fields_dashboard&ticket_fields='+ticket_fields+'&dashboard_users='+dashboard_users,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
	});
}
</script>

<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <div class="gap-top">
    	<?php $dashboard_users = array_filter(explode(',',get_config($dbc, 'sales_dashboard_users'))); ?>

        <h4>Pipeline &amp; Schedule Dashboard</h4>
        <div class="form-group">
            <?php
            $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT sales_dashboard FROM field_config"));
            $value_config = ','.$get_field_config['sales_dashboard'].',';
            ?>

            <table border='2' cellpadding='10' class='table'>
                <tr>
                    <td>
                        <input type="checkbox" <?php if (strpos($value_config, ','."Lead".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Lead" style="height: 20px; width: 20px;" name="sales_dashboard[]">&nbsp;&nbsp;Lead#
                    </td>
                    <td>
                        <input type="checkbox" <?php if (strpos($value_config, ','."Business/Contact".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Business/Contact" style="height: 20px; width: 20px;" name="sales_dashboard[]">&nbsp;&nbsp;Business/Contact
                    </td>
                    <td>
                        <input type="checkbox" <?php if (strpos($value_config, ','."Phone/Email".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Phone/Email" style="height: 20px; width: 20px;" name="sales_dashboard[]">&nbsp;&nbsp;Phone/Email
                    </td>
                    <td>
                        <input type="checkbox" <?php if (strpos($value_config, ','."Next Action".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Next Action" style="height: 20px; width: 20px;" name="sales_dashboard[]">&nbsp;&nbsp;Next Action
                    </td>
                    <td>
                        <input type="checkbox" <?php if (strpos($value_config, ','."Reminder".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Reminder" style="height: 20px; width: 20px;" name="sales_dashboard[]">&nbsp;&nbsp;Reminder
                    </td>
                    <td>
                        <input type="checkbox" <?php if (strpos($value_config, ','."Status".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Status" style="height: 20px; width: 20px;" name="sales_dashboard[]">&nbsp;&nbsp;Status
                    </td>
                    <td>
                        <input type="checkbox" <?php if (strpos($value_config, ','."Notes".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Notes" style="height: 20px; width: 20px;" name="sales_dashboard[]">&nbsp;&nbsp;Notes
                    </td>
                </tr>
            </table>
        </div>

        <div class="form-group">
    		<label class="col-sm-4">Users:<br /><em>Select the Users that should have dashboards.</em></label>
    		<div class="col-sm-8">
				<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `last_name`, `first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0")) as $contact) { ?>
					<label class="form-checkbox"><input name="dashboard_users[]" <?= in_array($contact['contactid'],$dashboard_users) || empty($dashboard_users) ? 'checked' : '' ?> type="checkbox" value="<?= $contact['contactid'] ?>"><?= $contact['full_name'] ?></label>
				<?php } ?>
    		</div>
    	</div>

    </div>
</form>