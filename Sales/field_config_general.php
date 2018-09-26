<?php
/*
Dashboard
*/
include_once ('../include.php');
checkAuthorised('sales');

$sales_tile = SALES_TILE;
$sales_noun = SALES_NOUN;

/*
if (isset($_POST['submit'])) {
    $sales = implode(',',$_POST['sales']);
    $active_leads = implode(",", $_POST['leadstatus']);
    $sales_dashboard = implode(',',$_POST['sales_dashboard']);

	// Sales Tile Name
    set_config($dbc, 'sales_tile_name', $_POST['sales_tile_name'].'#*#'.$_POST['sales_tile_noun']);
	$sales_tile = $_POST['sales_tile_name'];
	$sales_noun = $_POST['sales_tile_noun'];

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldconfigid) AS fieldconfigid FROM field_config"));
    if($get_field_config['fieldconfigid'] > 0) {
        $query_update_employee = "UPDATE `field_config` SET `sales`='$sales', `sales_dashboard`='$sales_dashboard' WHERE `fieldconfigid`=1";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `field_config` (`sales`, `sales_dashboard`) VALUES ('$sales', '$sales_dashboard')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $sales_lead_source = filter_var($_POST['sales_lead_source'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='sales_lead_source'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$sales_lead_source' WHERE name='sales_lead_source'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('sales_lead_source', '$sales_lead_source')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $sales_next_action = filter_var($_POST['sales_next_action'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='sales_next_action'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$sales_next_action' WHERE name='sales_next_action'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('sales_next_action', '$sales_next_action')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }


    $sales_lead_status = filter_var($_POST['sales_lead_status'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='sales_lead_status'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$sales_lead_status' WHERE name='sales_lead_status'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('sales_lead_status', '$sales_lead_status')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $lead_status_won = filter_var($_POST['lead_status_won'], FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='lead_status_won'"));
    if($get_config['configid'] > 0) {
        $query_update = "UPDATE `general_configuration` SET value='$lead_status_won' WHERE name='lead_status_won'";
        $result_update = mysqli_query($dbc, $query_update);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('lead_status_won', '$lead_status_won')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $lead_status_lost = filter_var($_POST['lead_status_lost'], FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='lead_status_lost'"));
    if($get_config['configid'] > 0) {
        $query_update = "UPDATE `general_configuration` SET value='$lead_status_lost' WHERE name='lead_status_lost'";
        $result_update = mysqli_query($dbc, $query_update);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('lead_status_lost', '$lead_status_lost')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $lead_convert_to = filter_var($_POST['lead_convert_to'], FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='lead_convert_to'"));
    if($get_config['configid'] > 0) {
        $query_update = "UPDATE `general_configuration` SET value='$lead_convert_to' WHERE name='lead_convert_to'";
        $result_update = mysqli_query($dbc, $query_update);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('lead_convert_to', '$lead_convert_to')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $sales_auto_archive = isset($_POST['sales_auto_archive']) ? 1 : 0;
    $sales_auto_archive_days = filter_var($_POST['sales_auto_archive_days'],FILTER_SANITIZE_STRING);
    set_config($dbc, 'sales_auto_archive', $sales_auto_archive);
    set_config($dbc, 'sales_auto_archive_days', $sales_auto_archive_days);
    $limit_staff_cat = filter_var(implode(',',$_POST['limit_staff_cat']),FILTER_SANITIZE_STRING);
    set_config($dbc, 'sales_limit_staff_cat', $limit_staff_cat);

    echo '<script type="text/javascript"> window.location.replace("field_config.php"); </script>';
}
*/
?>
<script>
$(document).ready(function(){
    $("#selectall").change(function(){
      $(".all_check").prop('checked', $(this).prop("checked"));
    });
});
</script>

<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <div class="gap-top">
    	<?php
    		$get_field_config	= mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `sales` FROM `field_config`" ) );
    		$value_config		= ',' . $get_field_config['sales'] . ',';
    	?>

        <h4><?= SALES_TILE ?> Tile Settings</h4>
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

        <hr>

        <h4>Choose Fields for Pipeline Sub Tab</h4>
        <div class="form-group">
            <?php
    			// Get Lead Statuses added in Settings->Lead Status accordion
    			$statuses = get_config ( $dbc, 'sales_lead_status' );

    			// Check if the array is empty and remove empty values
    			foreach ( $statuses as $key => $value ) {
    				if ( empty ( $value ) ) {
    				   unset ( $statuses[$key] );
    				}
    			}

    			$each_status	= explode ( ',', $statuses );
    			$count			= count( $each_status );
    		?>

            <table border="2" cellpadding="10" class="table">
                <?php
    				if ( $count>0 && !empty($statuses) ) {
    					for ( $i=0; $i<$count; $i++ ) {
    						echo ( $i%4 == 0 ) ? '<tr>' : '';
    							$checked = ( strpos ( $value_config, ',' . $each_status[$i] . ',' ) !== FALSE ) ? ' checked' : '';
    							echo '<td><input type="checkbox"' . $checked . ' value="' . $each_status[$i] . '" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;' . $each_status[$i];
    						echo ( $i%4 == 3 ) ? '</tr>' : '';
    					}
    				} else {
    					echo 'Please add the desired Lead Statuses first in Lead Status accordion below.';
    				}
    			?>
            </table>
        </div>

        <hr>

        <h4>Choose Fields for Schedule Sub Tab</h4>
        <div class="form-group">
            <table border="2" cellpadding="10" class="table">
                <tr>
                    <td>
                        <input type="checkbox" <?php if (strpos($value_config, ','.'Today'.',') !== FALSE) { echo ' checked'; } ?> value="Today" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Today
                    </td>
                    <td>
                        <input type="checkbox" <?php if (strpos($value_config, ','.'This Week'.',') !== FALSE) { echo ' checked'; } ?> value="This Week" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;This Week
                    </td>
                    <td>
                        <input type="checkbox" <?php if (strpos($value_config, ','.'This Month'.',') !== FALSE) { echo ' checked'; } ?> value="This Month" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;This Month
                    </td>
                    <td>
                        <input type="checkbox" <?php if (strpos($value_config, ','.'Custom'.',') !== FALSE) { echo ' checked'; } ?> value="Custom" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Custom
                    </td>
                </tr>
            </table>
        </div>

        <hr>

        <h4>Pipeline &amp; Schedule Accordions</h4>
        <div class="form-group">
            <input type="checkbox" id="selectall"/> Select All
    		<div id='no-more-tables'>
                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Sales Path".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Sales Path" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;<?= SALES_NOUN ?> Path
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Staff Information".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Staff Information" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Staff Information
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Lead Information".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Lead Information" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Lead Information
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Service".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Service" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Service
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Products".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Products" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Products
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Lead Source".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Lead Source" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Lead Source
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Reference Documents".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Reference Documents" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Reference Documents
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Marketing Material".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Marketing Material" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Marketing Material
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Information Gathering".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Information Gathering" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Information Gathering
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Estimate".',') !== FALSE) { echo " checked"; } ?> value="Estimate" class="all_check" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Estimate
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Quote".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Quote" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Quote
                        </td>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Next Action".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Next Action" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Next Action
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Lead Notes".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Lead Notes" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Lead Notes
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Lead Status".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Lead Status" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Lead Status
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Tasks".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Tasks" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Tasks
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Deliverable".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Deliverable" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Deliverable
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Time".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Time" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Time Tracking
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."History".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="History" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;History
                        </td>
                    </tr>

                </table>
    	    </div>
        </div>

        <hr>

        <h4>Staff Information Accordion</h4>
        <div class="form-group">
            <label class="col-sm-4">Limit Staff Categories:</label>
            <div class="col-sm-8">
                <select name="limit_staff_cat[]" multiple class="chosen-select-deselect form-control">
                    <?php $sales_limit_staff_cat = ','.get_config($dbc, 'sales_limit_staff_cat').',';
                    $get_config_values = array_filter(explode(',',str_replace(',,',',',str_replace('Staff','',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT categories FROM field_config_contacts WHERE tab='Staff' AND `categories` IS NOT NULL"))['categories']))));
                    foreach($get_config_values as $category) { ?>
                        <option value="<?= $category ?>" <?= strpos($sales_limit_staff_cat, ','.$category.',') !== FALSE ? 'selected' : '' ?>><?= $category ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <hr>

        <h4><?= SALES_TILE ?> Path Accordion</h4>
        <div class="form-group">
            <input type="checkbox" <?php if (strpos($value_config, ','."Sales Lead Path Intake".',') !== FALSE) { echo " checked"; } ?> value="Sales Lead Path Intake" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Intake Forms&nbsp;&nbsp;
            <input type="checkbox" <?php if (strpos($value_config, ','."Sales Lead Path Checklists".',') !== FALSE) { echo " checked"; } ?> value="Sales Lead Path Checklists" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Checklists&nbsp;&nbsp;
        </div>

        <hr>

        <h4>Lead Information Accordion</h4>
        <div class="form-group">
            <input type="checkbox" <?php if (strpos($value_config, ','."Lead Information Lead Value".',') !== FALSE) { echo " checked"; } ?> value="Lead Information Lead Value" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Lead Value&nbsp;&nbsp;
        </div>

        <hr>

        <h4>Services Accordion</h4>
        <div class="form-group">
            <input type="checkbox" <?php if (strpos($value_config, ','."Services Service Type".',') !== FALSE) { echo " checked"; } ?> value="Services Service Type" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Service Type&nbsp;&nbsp;
            <input type="checkbox" <?php if (strpos($value_config, ','."Services Category".',') !== FALSE) { echo " checked"; } ?> value="Services Category" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
            <input type="checkbox" <?php if (strpos($value_config, ','."Services Heading".',') !== FALSE) { echo " checked"; } ?> value="Services Heading" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;
        </div>

        <hr>

        <h4>Products Accordion</h4>
        <div class="form-group">
            <input type="checkbox" <?php if (strpos($value_config, ','."Products Product Type".',') !== FALSE) { echo " checked"; } ?> value="Products Product Type" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Product Type&nbsp;&nbsp;
            <input type="checkbox" <?php if (strpos($value_config, ','."Products Category".',') !== FALSE) { echo " checked"; } ?> value="Products Category" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
            <input type="checkbox" <?php if (strpos($value_config, ','."Products Heading".',') !== FALSE) { echo " checked"; } ?> value="Products Heading" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;
        </div>

        <hr>

        <h4>Marketing Material Accordion</h4>
        <div class="form-group">
            <input type="checkbox" <?php if (strpos($value_config, ','."Marketing Material Material Type".',') !== FALSE) { echo " checked"; } ?> value="Marketing Material Material Type" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Material Type&nbsp;&nbsp;
            <input type="checkbox" <?php if (strpos($value_config, ','."Marketing Material Category".',') !== FALSE) { echo " checked"; } ?> value="Marketing Material Category" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
            <input type="checkbox" <?php if (strpos($value_config, ','."Marketing Material Heading".',') !== FALSE) { echo " checked"; } ?> value="Marketing Material Heading" style="height: 20px; width: 20px;" name="sales[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;
        </div>

        <hr>

        <h4>Pipeline &amp; Schedule Dashboard</h4>
        <div class="form-group">
            <?php
            $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT sales_dashboard FROM field_config"));
            $value_config = ','.$get_field_config['sales_dashboard'].',';
            ?>

            <table border='2' cellpadding='10' class='table'>
                <tr>
                    <td>
                        <input type="checkbox" <?php if (strpos($value_config, ','."Lead#".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Lead#" style="height: 20px; width: 20px;" name="sales_dashboard[]">&nbsp;&nbsp;Lead#
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

        <hr>

        <h4>Lead Source</h4>
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

        <hr>

        <h4>Next Action</h4>
        <div class="form-group">
            <div class="col-sm-8 col-sm-offset-4">
                Add tabs separated by a comma in the order you want them on the dashboard:
            </div>
            <label for="company_name" class="col-sm-4 control-label">Next Action:</label>
            <div class="col-sm-8">
              <input name="sales_next_action" value="<?php echo get_config($dbc, 'sales_next_action'); ?>" type="text" class="form-control">
            </div>
        </div>

        <hr>

        <h4>Lead Status</h4>
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

        <hr>

        <h4>Auto Archive</h4>
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

        <div class="pull-right gap-top gap-bottom">
            <a href="index.php" class="btn brand-btn">Back</a>
            <button	type="submit" name="submit"	value="Submit" class="btn brand-btn">Submit</button>
        </div>
    </div>
</form>