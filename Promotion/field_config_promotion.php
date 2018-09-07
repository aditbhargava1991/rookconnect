<?php
/*
 * Settings
 */
include ('../include.php');
error_reporting(0);

if (isset($_POST['submit'])) {
    $promotion = implode(',',$_POST['promotion']);
    $promotion_dashboard = implode(',',$_POST['promotion_dashboard']);

    //if (strpos(','.$promotion.',',','.'Service Type,Category,Heading'.',') === false) {
    //    $promotion = 'Service Type,Category,Heading,'.$promotion;
    //}
    //if (strpos(','.$promotion_dashboard.',',','.'Service Type,Category,Heading'.',') === false) {
    //    $promotion_dashboard = 'Service Type,Category,Heading,'.$promotion_dashboard;
    //}

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldconfigid) AS fieldconfigid FROM field_config"));
    if($get_field_config['fieldconfigid'] > 0) {
        $query_update_employee = "UPDATE `field_config` SET promotion = '$promotion', promotion_dashboard = '$promotion_dashboard' WHERE `fieldconfigid` = 1";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `field_config` (`promotion`, `promotion_dashboard`) VALUES ('$promotion', '$promotion_dashboard')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    echo '<script type="text/javascript"> window.location.replace("field_config_promotion.php"); </script>';
}
?>
<style>input[type="checkbox"] { height:20px; width:20px; }</style>
<script>
$(document).ready(function(){
    $("#selectall").change(function(){
        $(".all_check").prop('checked', $(this).prop("checked"));
    });
});
</script>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
<div class="row">
<h1>Promotions</h1>
<div class="pad-left gap-top double-gap-bottom"><a href="promotion.php" class="btn config-btn">Back to Dashboard</a></div>
<!--<a href="#" class="btn config-btn" onclick="history.go(-1);return false;">Back</a>-->

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

<div class="panel-group" id="accordion2">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field" >
                    Choose Fields for Promotion<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_field" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">
                <?php
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT promotion FROM field_config"));
                $value_config = ','.$get_field_config['promotion'].',';
                ?>
				<span style='font-weight:bold;padding:10px;'>General Fields</span>
                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Service Type".',') !== FALSE) { echo " checked"; } ?> value="Service Type" name="promotion[]">&nbsp;&nbsp;Service Type
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Category".',') !== FALSE) { echo " checked"; } ?> value="Category" name="promotion[]">&nbsp;&nbsp;Category
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Heading".',') !== FALSE) { echo " checked"; } ?> value="Heading" name="promotion[]">&nbsp;&nbsp;Heading
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Cost".',') !== FALSE) { echo " checked"; } ?> value="Cost" name="promotion[]">&nbsp;&nbsp;Cost
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Description".',') !== FALSE) { echo " checked"; } ?> value="Description" name="promotion[]">&nbsp;&nbsp;Description
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Quote Description".',') !== FALSE) { echo " checked"; } ?> value="Quote Description" name="promotion[]">&nbsp;&nbsp;Quote Description
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { echo " checked"; } ?> value="Final Retail Price" name="promotion[]">&nbsp;&nbsp;Final Retail Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { echo " checked"; } ?> value="Admin Price" name="promotion[]">&nbsp;&nbsp;Admin Price
                        </td>
                        <td>
                           <input type="checkbox" <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { echo " checked"; } ?> value="Wholesale Price" name="promotion[]">&nbsp;&nbsp;Wholesale Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { echo " checked"; } ?> value="Commercial Price" name="promotion[]">&nbsp;&nbsp;Commercial Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Client Price".',') !== FALSE) { echo " checked"; } ?> value="Client Price" name="promotion[]">&nbsp;&nbsp;Client Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) { echo " checked"; } ?> value="Minimum Billable" name="promotion[]">&nbsp;&nbsp;Minimum Billable
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) { echo " checked"; } ?> value="Estimated Hours" name="promotion[]">&nbsp;&nbsp;Estimated Hours
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Actual Hours".',') !== FALSE) { echo " checked"; } ?> value="Actual Hours" name="promotion[]">&nbsp;&nbsp;Actual Hours
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."MSRP".',') !== FALSE) {
                            echo " checked"; } ?> value="MSRP" name="promotion[]">&nbsp;&nbsp;MSRP
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Invoice Description".',') !== FALSE) {
                            echo " checked"; } ?> value="Invoice Description" name="promotion[]">&nbsp;&nbsp;Invoice Description
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Ticket Description".',') !== FALSE) {
                            echo " checked"; } ?> value="Ticket Description" name="promotion[]">&nbsp;&nbsp;<?= TICKET_NOUN ?> Description
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Expiry Date".',') !== FALSE) {
                            echo " checked"; } ?> value="Expiry Date" name="promotion[]">&nbsp;&nbsp;Expiry Date
                        </td>
                    </tr>
                </table>
				<span style='font-weight:bold;padding:10px;'>Inventory Fields</span>
				<table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Inventory Part Number".',') !== FALSE) { echo " checked"; } ?> value="Inventory Part Number" name="promotion[]">&nbsp;&nbsp;Part Number
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_accordion" >
                    Create Accordion<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_accordion" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">
                <input type="checkbox" id="selectall"/> Select All
                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Assign Services".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Assign Services" name="promotion[]">&nbsp;&nbsp;Services
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Assign Clients".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Assign Clients" name="promotion[]">&nbsp;&nbsp;Clients
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Assign Customer".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Assign Customer" name="promotion[]">&nbsp;&nbsp;Customer
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Assign Vendor".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Assign Vendor" name="promotion[]">&nbsp;&nbsp;Vendor
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Assign Inventory".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Assign Inventory" name="promotion[]">&nbsp;&nbsp;Inventory
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Assign Equipment".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Assign Equipment" name="promotion[]">&nbsp;&nbsp;Equipment
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Assign Equipment by Category".',') !== FALSE) { echo " checked"; } ?> value="Assign Equipment by Category" name="promotion[]">&nbsp;&nbsp;Equipment by Category
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Assign Staff".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Assign Staff" name="promotion[]">&nbsp;&nbsp;Staff
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Assign Staff Position".',') !== FALSE) { echo " checked"; } ?> value="Assign Staff Position" name="promotion[]">&nbsp;&nbsp;Staff Position
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Assign Contractor".',') !== FALSE) { echo " checked"; } ?> class="all_check" value="Assign Contractor" name="promotion[]">&nbsp;&nbsp;Contractor
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
                    Choose Fields for Promotion Dashboard<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_dashboard" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">
                <?php
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT promotion_dashboard FROM field_config"));
                $value_config = ','.$get_field_config['promotion_dashboard'].',';
                ?>

                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Service Type".',') !== FALSE) { echo " checked"; } ?> value="Service Type" name="promotion_dashboard[]">&nbsp;&nbsp;Service Type
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Category".',') !== FALSE) { echo " checked"; } ?> value="Category" name="promotion_dashboard[]">&nbsp;&nbsp;Category
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Heading".',') !== FALSE) { echo " checked"; } ?> value="Heading" name="promotion_dashboard[]">&nbsp;&nbsp;Heading
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Cost".',') !== FALSE) { echo " checked"; } ?> value="Cost" name="promotion_dashboard[]">&nbsp;&nbsp;Cost
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Description".',') !== FALSE) { echo " checked"; } ?> value="Description" name="promotion_dashboard[]">&nbsp;&nbsp;Description
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Quote Description".',') !== FALSE) { echo " checked"; } ?> value="Quote Description" name="promotion_dashboard[]">&nbsp;&nbsp;Quote Description
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { echo " checked"; } ?> value="Final Retail Price" name="promotion_dashboard[]">&nbsp;&nbsp;Final Retail Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { echo " checked"; } ?> value="Admin Price" name="promotion_dashboard[]">&nbsp;&nbsp;Admin Price
                        </td>
                        <td>
                           <input type="checkbox" <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { echo " checked"; } ?> value="Wholesale Price" name="promotion_dashboard[]">&nbsp;&nbsp;Wholesale Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { echo " checked"; } ?> value="Commercial Price" name="promotion_dashboard[]">&nbsp;&nbsp;Commercial Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Client Price".',') !== FALSE) { echo " checked"; } ?> value="Client Price" name="promotion_dashboard[]">&nbsp;&nbsp;Client Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) { echo " checked"; } ?> value="Minimum Billable" name="promotion_dashboard[]">&nbsp;&nbsp;Minimum Billable
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) { echo " checked"; } ?> value="Estimated Hours" name="promotion_dashboard[]">&nbsp;&nbsp;Estimated Hours
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Actual Hours".',') !== FALSE) { echo " checked"; } ?> value="Actual Hours" name="promotion_dashboard[]">&nbsp;&nbsp;Actual Hours
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."MSRP".',') !== FALSE) {
                            echo " checked"; } ?> value="MSRP" name="promotion_dashboard[]">&nbsp;&nbsp;MSRP
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Invoice Description".',') !== FALSE) {
                            echo " checked"; } ?> value="Invoice Description" name="promotion_dashboard[]">&nbsp;&nbsp;Invoice Description
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Ticket Description".',') !== FALSE) {
                            echo " checked"; } ?> value="Ticket Description" name="promotion_dashboard[]">&nbsp;&nbsp;<?= TICKET_NOUN ?> Description
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Expiry Date".',') !== FALSE) {
                            echo " checked"; } ?> value="Expiry Date" name="promotion_dashboard[]">&nbsp;&nbsp;Expiry Date
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Times Used".',') !== FALSE) {
                            echo " checked"; } ?> value="Times Used" name="promotion_dashboard[]">&nbsp;&nbsp;# of Times Used
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</div>

<div class="form-group">
    <div class="col-sm-6">
        <a href="promotion.php" class="btn config-btn btn-lg">Back</a>
		<!--<a href="#" class="btn config-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
	</div>
	<div class="col-sm-6">
        <button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>
    </div>
	<div class="clearfix"></div>
</div>

</form>
</div>
</div>

<?php include ('../footer.php'); ?>