<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('products');
error_reporting(0);

if (isset($_POST['submit'])) {
    $products = implode(',',$_POST['products']);
    $products_dashboard = implode(',',$_POST['products_dashboard']);

    if (strpos(','.$products.',',','.'Product Type,Category,Heading'.',') === false) {
        $products = 'Product Type,Category,Heading,'.$products;
    }
    if (strpos(','.$products_dashboard.',',','.'Product Type,Category,Heading'.',') === false) {
        $products_dashboard = 'Product Type,Category,Heading,'.$products_dashboard;
    }

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldconfigid) AS fieldconfigid FROM field_config"));
    if($get_field_config['fieldconfigid'] > 0) {
        $query_update_employee = "UPDATE `field_config` SET products = '$products', products_dashboard = '$products_dashboard' WHERE `fieldconfigid` = 1";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `field_config` (`products`, `products_dashboard`) VALUES ('$products', '$products_dashboard')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    echo '<script type="text/javascript"> window.location.replace("field_config_products.php"); </script>';

}
?>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
<div class="row">
<h1>Products</h1>
<div class="pad-left gap-top double-gap-bottom"><a href="products.php" class="btn config-btn">Back to Dashboard</a></div>
<!--<a href="#" class="btn config-btn" onclick="history.go(-1);return false;">Back</a>-->

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

<div class="panel-group" id="accordion2">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Click here to add or remove the fields that will be visible only when you add Products."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field" >
                    Choose Fields for Products<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_field" class="panel-collapse collapse">
            <div class="panel-body">
                <?php
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT products FROM field_config"));
                $value_config = ','.$get_field_config['products'].',';
                ?>
				<div id='no-more-tables'>
                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" disabled <?php if (strpos($value_config, ','."Product Type".',') !== FALSE) { echo " checked"; } ?> value="Product Type" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Product Type
                        </td>
                        <td>
                            <input type="checkbox" disabled <?php if (strpos($value_config, ','."Category".',') !== FALSE) { echo " checked"; } ?> value="Category" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Category
                        </td>
                        <td>
                            <input type="checkbox" disabled <?php if (strpos($value_config, ','."Heading".',') !== FALSE) { echo " checked"; } ?> value="Heading" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Heading
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Cost".',') !== FALSE) { echo " checked"; } ?> value="Cost" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Cost
                        </td>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Description".',') !== FALSE) { echo " checked"; } ?> value="Description" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Description
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Quote Description".',') !== FALSE) { echo " checked"; } ?> value="Quote Description" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Quote Description
                        </td>

                    </tr>

                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { echo " checked"; } ?> value="Final Retail Price" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Final Retail Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { echo " checked"; } ?> value="Admin Price" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Admin Price
                        </td>
                        <td>
                           <input type="checkbox" <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { echo " checked"; } ?> value="Wholesale Price" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Wholesale Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { echo " checked"; } ?> value="Commercial Price" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Commercial Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Client Price".',') !== FALSE) { echo " checked"; } ?> value="Client Price" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Client Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) { echo " checked"; } ?> value="Minimum Billable" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Minimum Billable
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) { echo " checked"; } ?> value="Estimated Hours" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Estimated Hours
                        </td>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Actual Hours".',') !== FALSE) { echo " checked"; } ?> value="Actual Hours" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Actual Hours
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."MSRP".',') !== FALSE) {
                            echo " checked"; } ?> value="MSRP" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;MSRP
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Product Code".',') !== FALSE) {
                            echo " checked"; } ?> value="Product Code" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Product Code
                        </td>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Invoice Description".',') !== FALSE) {
                            echo " checked"; } ?> value="Invoice Description" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Invoice Description
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Ticket Description".',') !== FALSE) {
                            echo " checked"; } ?> value="Ticket Description" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;<?= TICKET_NOUN ?> Description
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Unit of Measure".',') !== FALSE) {
                            echo " checked"; } ?> value="Unit of Measure" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Unit of Measure
                        </td>


                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Unit Price".',') !== FALSE) { echo " checked"; } ?> value="Unit Price" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Unit Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Unit Cost".',') !== FALSE) { echo " checked"; } ?> value="Unit Cost" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Unit Cost
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Rent Price".',') !== FALSE) { echo " checked"; } ?> value="Rent Price" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Rent Price
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Purchase Order Price".',') !== FALSE) { echo " checked"; } ?> value="Purchase Order Price" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Purchase Order Price
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Sales Order Price".',') !== FALSE) { echo " checked"; } ?> value="Sales Order Price" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;<?= SALES_ORDER_NOUN ?> Price
                        </td>

                    </tr>
                    <tr>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Fee".',') !== FALSE) {
                            echo " checked"; } ?> value="Fee" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Fee
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Rental Days".',') !== FALSE) { echo " checked"; } ?> value="Rental Days" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Rental Days
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Rental Weeks".',') !== FALSE) { echo " checked"; } ?> value="Rental Weeks" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Rental Weeks
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Rental Months".',') !== FALSE) { echo " checked"; } ?> value="Rental Months" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Rental Months
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Rental Years".',') !== FALSE) { echo " checked"; } ?> value="Rental Years" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Rental Years
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Daily".',') !== FALSE) { echo " checked"; } ?> value="Daily" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Daily
                        </td>

                    </tr>
                    <tr>
					<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Weekly".',') !== FALSE) { echo " checked"; } ?> value="Weekly" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Weekly
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Monthly".',') !== FALSE) { echo " checked"; } ?> value="Monthly" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Monthly
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Annually".',') !== FALSE) { echo " checked"; } ?> value="Annually" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Annually
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."#Of Days".',') !== FALSE) { echo " checked"; } ?> value="#Of Days" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;#Of Days
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."#Of Hours".',') !== FALSE) { echo " checked"; } ?> value="#Of Hours" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;#Of Hours
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."#Of Kilometers".',') !== FALSE) { echo " checked"; } ?> value="#Of Kilometers" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;#Of Kilometers
                        </td>

                    </tr>
                    <tr>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."#Of Miles".',') !== FALSE) { echo " checked"; } ?> value="#Of Miles" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;#Of Miles
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Hourly Rate".',') !== FALSE) { echo " checked"; } ?> value="Hourly Rate" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Hourly Rate
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Reminder/Alert".',') !== FALSE) { echo " checked"; } ?> value="Reminder/Alert" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Reminder/Alert
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Include in P.O.S.".',') !== FALSE) { echo " checked"; } ?> value="Include in P.O.S." style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Include in <?= POS_ADVANCE_TILE ?>.
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Include in Sales Orders".',') !== FALSE) { echo " checked"; } ?> value="Include in Sales Orders" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Include in <?= SALES_ORDER_TILE ?>
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Include in Purchase Orders".',') !== FALSE) { echo " checked"; } ?> value="Include in Purchase Orders" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Include in Purchase Orders
                        </td>
                    </tr>
                    <tr>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Drum Unit Cost".',') !== FALSE) { echo " checked"; } ?> value="Drum Unit Cost" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Drum Unit Cost
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Drum Unit Price".',') !== FALSE) { echo " checked"; } ?> value="Drum Unit Price" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Drum Unit Price
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Tote Unit Cost".',') !== FALSE) { echo " checked"; } ?> value="Tote Unit Cost" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Tote Unit Cost
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Tote Unit Price".',') !== FALSE) { echo " checked"; } ?> value="Tote Unit Price" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Tote Unit Price
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Include in Inventory".',') !== FALSE) { echo " checked"; } ?> value="Include in Inventory" style="height: 20px; width: 20px;" name="products[]">&nbsp;&nbsp;Include in Inventory
                        </td>
                    </tr>
                </table></div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Click here to add or remove the fields that will be visible on the Products Dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_dashboard" >
                    Choose Fields for Products Dashboard<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_dashboard" class="panel-collapse collapse">
            <div class="panel-body">
                <?php
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT products_dashboard FROM field_config"));
                $value_config = ','.$get_field_config['products_dashboard'].',';
                ?>
				<div id='no-more-tables'>
                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" disabled <?php if (strpos($value_config, ','."Product Type".',') !== FALSE) { echo " checked"; } ?> value="Product Type" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Product Type
                        </td>
                        <td>
                            <input type="checkbox" disabled <?php if (strpos($value_config, ','."Category".',') !== FALSE) { echo " checked"; } ?> value="Category" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Category
                        </td>
                        <td>
                            <input type="checkbox" disabled <?php if (strpos($value_config, ','."Heading".',') !== FALSE) { echo " checked"; } ?> value="Heading" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Heading
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Cost".',') !== FALSE) { echo " checked"; } ?> value="Cost" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Cost
                        </td>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Description".',') !== FALSE) { echo " checked"; } ?> value="Description" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Description
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Quote Description".',') !== FALSE) { echo " checked"; } ?> value="Quote Description" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Quote Description
                        </td>

                    </tr>

                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { echo " checked"; } ?> value="Final Retail Price" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Final Retail Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { echo " checked"; } ?> value="Admin Price" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Admin Price
                        </td>
                        <td>
                           <input type="checkbox" <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { echo " checked"; } ?> value="Wholesale Price" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Wholesale Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { echo " checked"; } ?> value="Commercial Price" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Commercial Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Client Price".',') !== FALSE) { echo " checked"; } ?> value="Client Price" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Client Price
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Purchase Order Price".',') !== FALSE) { echo " checked"; } ?> value="Purchase Order Price" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Purchase Order Price
                        </td>


                    </tr>

                    <tr>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Sales Order Price".',') !== FALSE) { echo " checked"; } ?> value="Sales Order Price" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Sales Order Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) { echo " checked"; } ?> value="Estimated Hours" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Estimated Hours
                        </td>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Actual Hours".',') !== FALSE) { echo " checked"; } ?> value="Actual Hours" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Actual Hours
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."MSRP".',') !== FALSE) {
                            echo " checked"; } ?> value="MSRP" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;MSRP
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Product Code".',') !== FALSE) {
                            echo " checked"; } ?> value="Product Code" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Product Code
                        </td>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Invoice Description".',') !== FALSE) {
                            echo " checked"; } ?> value="Invoice Description" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Invoice Description
                        </td>

                    </tr>
                    <tr>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Ticket Description".',') !== FALSE) {
                            echo " checked"; } ?> value="Ticket Description" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;<?= TICKET_NOUN ?> Description
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Unit of Measure".',') !== FALSE) {
                            echo " checked"; } ?> value="Unit of Measure" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Unit of Measure
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Fee".',') !== FALSE) {
                            echo " checked"; } ?> value="Fee" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Fee
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Hourly Rate".',') !== FALSE) { echo " checked"; } ?> value="Hourly Rate" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Hourly Rate
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) { echo " checked"; } ?> value="Minimum Billable" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Minimum Billable
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Include in P.O.S.".',') !== FALSE) {
                            echo " checked"; } ?> value="Include in P.O.S." style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Include in <?= POS_ADVANCE_TILE ?>
                        </td>
                    </tr>
					 <tr>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Include in Purchase Orders".',') !== FALSE) {
                            echo " checked"; } ?> value="Include in Purchase Orders" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Include in Purchase Orders
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Include in Sales Orders".',') !== FALSE) {
                            echo " checked"; } ?> value="Include in Sales Orders" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Include in Sales Orders
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Drum Unit Cost".',') !== FALSE) { echo " checked"; } ?> value="Drum Unit Cost" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Drum Unit Cost
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Drum Unit Price".',') !== FALSE) { echo " checked"; } ?> value="Drum Unit Price" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Drum Unit Price
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Tote Unit Cost".',') !== FALSE) { echo " checked"; } ?> value="Tote Unit Cost" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Tote Unit Cost
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Tote Unit Price".',') !== FALSE) { echo " checked"; } ?> value="Tote Unit Price" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Tote Unit Price
                        </td>
                    </tr>
                    <tr>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Include in Inventory".',') !== FALSE) { echo " checked"; } ?> value="Include in Inventory" style="height: 20px; width: 20px;" name="products_dashboard[]">&nbsp;&nbsp;Include in Inventory
                        </td>
                    </tr>
                </table>
				</div>
            </div>
        </div>
    </div>

</div>

<div class="form-group">
    <div class="col-sm-6">
         <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Clicking this will discard your Products settings."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
		<a href="products.php" class="btn config-btn btn-lg">Back</a>
		<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
    </div>
    <div class="col-sm-6">
        <button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>
		<span class="popover-examples pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click this to finalize your Products settings."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
    </div>
</div>

</form>
</div>
</div>

<?php include ('../footer.php'); ?>