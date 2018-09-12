<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('passwords');
error_reporting(0);

if (isset($_POST['submit'])) {

    $password_category = filter_var($_POST['password_category'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='password_category'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$password_category' WHERE name='password_category'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('password_category', '$password_category')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $query = mysqli_query($dbc,"DELETE FROM `field_config_password`");

    $tabs_cat = get_config($dbc, 'password_category');
    $each_tab_cat = explode(',', $tabs_cat);
    $i = 0;
    foreach ($each_tab_cat as $cat_tab_cat) {
        $password_assign = ','.implode(',',$_POST['password_assign_'.$i]).',';
        $query_insert_config = "INSERT INTO `field_config_password` (`category`, `contactid`) VALUES ('$cat_tab_cat', '$password_assign')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
        $i++;
    }

    //
    $passwords = implode(',',$_POST['passwords']);
    $passwords_dashboard = implode(',',$_POST['passwords_dashboard']);

    if (strpos(','.$passwords.',',','.'Password Type,Category,Heading'.',') === false) {
        $passwords = 'Password Type,Category,Heading,'.$passwords;
    }
    if (strpos(','.$passwords_dashboard.',',','.'Password Type,Category,Heading'.',') === false) {
        $passwords_dashboard = 'Password Type,Category,Heading,'.$passwords_dashboard;
    }

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldconfigid) AS fieldconfigid FROM field_config"));
    if($get_field_config['fieldconfigid'] > 0) {
        $query_update_employee = "UPDATE `field_config` SET passwords = '$passwords', passwords_dashboard = '$passwords_dashboard' WHERE `fieldconfigid` = 1";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `field_config` (`passwords`, `passwords_dashboard`) VALUES ('$passwords', '$passwords_dashboard')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    echo '<script type="text/javascript"> window.location.replace("field_config_passwords.php"); </script>';

}
?>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
<div class="row">
<h1>Passwords</h1>
<div class="pad-left gap-top double-gap-bottom"><a href="passwords.php" class="btn config-btn">Back to Dashboard</a></div>
<!--<a href="#" class="btn config-btn" onclick="history.go(-1);return false;">Back</a>-->

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

<div class="panel-group" id="accordion2">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_cat" >
                    Category<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_cat" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">

            <div class="form-group">
                <label for="fax_number"	class="col-sm-4	control-label">Category:</label>
                <div class="col-sm-8">
                  <input name="password_category" type="text" value="<?php echo get_config($dbc, 'password_category'); ?>" class="form-control"/>
                </div>
            </div>

            <?php
                $tabs_cat = get_config($dbc, 'password_category');
                $each_tab_cat = explode(',', $tabs_cat);
                $i = 0;
                foreach ($each_tab_cat as $cat_tab_cat) {
                    $contactid = get_passwordconfig($dbc, $cat_tab_cat, 'contactid');
                    if($cat_tab_cat != '') {
                    ?>
                    <div class="form-group">
                        <label for="fax_number"	class="col-sm-4	control-label"><?php echo $cat_tab_cat; ?>:</label>
                        <div class="col-sm-8">
                            <select multiple name="password_assign_<?php echo $i; ?>[]" data-placeholder="Choose a User..." class="chosen-select-deselect form-control" width="380">
							  <?php
								$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status` > 0"),MYSQLI_ASSOC));
								foreach($query as $id) {
									$selected = '';
									$selected = strpos($contactid, ','.$id.',') !== false ? 'selected = "selected"' : '';
									echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
								}
							  ?>
                            </select>
                        </div>
                    </div>
                    <?php $i++;
                    }

                    }
                    ?>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field" >
                    Choose Fields for Passwords<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_field" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">
                <?php
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT passwords FROM field_config"));
                $value_config = ','.$get_field_config['passwords'].',';
                ?>

                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" disabled <?php if (strpos($value_config, ','."Password Type".',') !== FALSE) { echo " checked"; } ?> value="Password Type" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Password Type
                        </td>
                        <td>
                            <input type="checkbox" disabled <?php if (strpos($value_config, ','."Category".',') !== FALSE) { echo " checked"; } ?> value="Category" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Category
                        </td>
                        <td>
                            <input type="checkbox" disabled <?php if (strpos($value_config, ','."Heading".',') !== FALSE) { echo " checked"; } ?> value="Heading" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Heading
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Cost".',') !== FALSE) { echo " checked"; } ?> value="Cost" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Cost
                        </td>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Description".',') !== FALSE) { echo " checked"; } ?> value="Description" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Description
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Quote Description".',') !== FALSE) { echo " checked"; } ?> value="Quote Description" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Quote Description
                        </td>

                    </tr>

                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { echo " checked"; } ?> value="Final Retail Price" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Final Retail Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { echo " checked"; } ?> value="Admin Price" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Admin Price
                        </td>
                        <td>
                           <input type="checkbox" <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { echo " checked"; } ?> value="Wholesale Price" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Wholesale Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { echo " checked"; } ?> value="Commercial Price" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Commercial Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Client Price".',') !== FALSE) { echo " checked"; } ?> value="Client Price" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Client Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) { echo " checked"; } ?> value="Minimum Billable" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Minimum Billable
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) { echo " checked"; } ?> value="Estimated Hours" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Estimated Hours
                        </td>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Actual Hours".',') !== FALSE) { echo " checked"; } ?> value="Actual Hours" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Actual Hours
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."MSRP".',') !== FALSE) {
                            echo " checked"; } ?> value="MSRP" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;MSRP
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Password Code".',') !== FALSE) {
                            echo " checked"; } ?> value="Password Code" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Password Code
                        </td>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Invoice Description".',') !== FALSE) {
                            echo " checked"; } ?> value="Invoice Description" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Invoice Description
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Ticket Description".',') !== FALSE) {
                            echo " checked"; } ?> value="Ticket Description" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;<?= TICKET_NOUN ?> Description
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Name".',') !== FALSE) {
                            echo " checked"; } ?> value="Name" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Name
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Fee".',') !== FALSE) {
                            echo " checked"; } ?> value="Fee" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Fee
                        </td>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Unit Price".',') !== FALSE) { echo " checked"; } ?> value="Unit Price" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Unit Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Unit Cost".',') !== FALSE) { echo " checked"; } ?> value="Unit Cost" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Unit Cost
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Rent Price".',') !== FALSE) { echo " checked"; } ?> value="Rent Price" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Rent Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Rental Days".',') !== FALSE) { echo " checked"; } ?> value="Rental Days" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Rental Days
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Rental Weeks".',') !== FALSE) { echo " checked"; } ?> value="Rental Weeks" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Rental Weeks
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Rental Months".',') !== FALSE) { echo " checked"; } ?> value="Rental Months" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Rental Months
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Rental Years".',') !== FALSE) { echo " checked"; } ?> value="Rental Years" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Rental Years
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Reminder/Alert".',') !== FALSE) { echo " checked"; } ?> value="Reminder/Alert" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Reminder/Alert
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Daily".',') !== FALSE) { echo " checked"; } ?> value="Daily" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Daily
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Weekly".',') !== FALSE) { echo " checked"; } ?> value="Weekly" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Weekly
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Monthly".',') !== FALSE) { echo " checked"; } ?> value="Monthly" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Monthly
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Annually".',') !== FALSE) { echo " checked"; } ?> value="Annually" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Annually
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."#Of Days".',') !== FALSE) { echo " checked"; } ?> value="#Of Days" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;#Of Days
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."#Of Hours".',') !== FALSE) { echo " checked"; } ?> value="#Of Hours" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;#Of Hours
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."#Of Kilometers".',') !== FALSE) { echo " checked"; } ?> value="#Of Kilometers" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;#Of Kilometers
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."#Of Miles".',') !== FALSE) { echo " checked"; } ?> value="#Of Miles" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;#Of Miles
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Hourly Rate".',') !== FALSE) { echo " checked"; } ?> value="Hourly Rate" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Hourly Rate
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Business".',') !== FALSE) { echo " checked"; } ?> value="Business" style="height: 20px; width: 20px;" name="passwords[]">&nbsp;&nbsp;Business
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_dashboard" >
                    Choose Fields for Passwords Dashboard<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_dashboard" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">
                <?php
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT passwords_dashboard FROM field_config"));
                $value_config = ','.$get_field_config['passwords_dashboard'].',';
                ?>

                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" disabled <?php if (strpos($value_config, ','."Password Type".',') !== FALSE) { echo " checked"; } ?> value="Password Type" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Password Type
                        </td>
                        <td>
                            <input type="checkbox" disabled <?php if (strpos($value_config, ','."Category".',') !== FALSE) { echo " checked"; } ?> value="Category" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Category
                        </td>
                        <td>
                            <input type="checkbox" disabled <?php if (strpos($value_config, ','."Heading".',') !== FALSE) { echo " checked"; } ?> value="Heading" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Heading
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Cost".',') !== FALSE) { echo " checked"; } ?> value="Cost" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Cost
                        </td>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Description".',') !== FALSE) { echo " checked"; } ?> value="Description" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Description
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Quote Description".',') !== FALSE) { echo " checked"; } ?> value="Quote Description" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Quote Description
                        </td>

                    </tr>

                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { echo " checked"; } ?> value="Final Retail Price" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Final Retail Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { echo " checked"; } ?> value="Admin Price" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Admin Price
                        </td>
                        <td>
                           <input type="checkbox" <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { echo " checked"; } ?> value="Wholesale Price" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Wholesale Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { echo " checked"; } ?> value="Commercial Price" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Commercial Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Client Price".',') !== FALSE) { echo " checked"; } ?> value="Client Price" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Client Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) { echo " checked"; } ?> value="Minimum Billable" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Minimum Billable
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) { echo " checked"; } ?> value="Estimated Hours" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Estimated Hours
                        </td>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Actual Hours".',') !== FALSE) { echo " checked"; } ?> value="Actual Hours" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Actual Hours
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."MSRP".',') !== FALSE) {
                            echo " checked"; } ?> value="MSRP" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;MSRP
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Password Code".',') !== FALSE) {
                            echo " checked"; } ?> value="Password Code" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Password Code
                        </td>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Invoice Description".',') !== FALSE) {
                            echo " checked"; } ?> value="Invoice Description" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Invoice Description
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Ticket Description".',') !== FALSE) {
                            echo " checked"; } ?> value="Ticket Description" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;<?= TICKET_NOUN ?> Description
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Name".',') !== FALSE) {
                            echo " checked"; } ?> value="Name" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Name
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Fee".',') !== FALSE) {
                            echo " checked"; } ?> value="Fee" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Fee
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Hourly Rate".',') !== FALSE) { echo " checked"; } ?> value="Hourly Rate" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Hourly Rate
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Business".',') !== FALSE) { echo " checked"; } ?> value="Business" style="height: 20px; width: 20px;" name="passwords_dashboard[]">&nbsp;&nbsp;Business
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</div>

<div class="pull-left">
    <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to discard your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
    <a href="passwords.php" class="btn brand-btn btn-lg">Back</a>
		<!--<a href="#" class="btn config-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
</div>
<div class="pull-right">
    <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to submit your changes."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
    <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg">Submit</button>
</div>

</form>
</div>
</div>

<?php include ('../footer.php'); ?>
