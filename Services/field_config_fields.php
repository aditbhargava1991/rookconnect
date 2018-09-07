<?php
/*
Dashboard
*/
include_once ('../include.php');
checkAuthorised('services');

if (isset($_POST['submit'])) {
    $services = implode(',',$_POST['services']);

    if (strpos(','.$services.',',','.'Category,Heading'.',') === false) {
        $services = 'Category,Heading,'.$services;
    }

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldconfigid) AS fieldconfigid FROM field_config"));
    if($get_field_config['fieldconfigid'] > 0) {
        $query_update_employee = "UPDATE `field_config` SET services = '$services' WHERE `fieldconfigid` = 1";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `field_config` (`services`) VALUES ('$services')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    echo '<script type="text/javascript"> window.location.replace("field_config.php?tab=fields"); </script>';

}
?>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

    <?php
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT services FROM field_config"));
    $value_config = ','.$get_field_config['services'].',';
    ?>

    <div id="no-more-tables">
        <table class="table table-bordered">
            <tr>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Service Type".',') !== FALSE) { echo " checked"; } ?> value="Service Type" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Service Type
                </td>
                <td>
                    <input type="checkbox" disabled <?php if (strpos($value_config, ','."Category".',') !== FALSE) { echo " checked"; } ?> value="Category" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Category
                </td>
                <td>
                    <input type="checkbox" disabled <?php if (strpos($value_config, ','."Heading".',') !== FALSE) { echo " checked"; } ?> value="Heading" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Heading
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Cost".',') !== FALSE) { echo " checked"; } ?> value="Cost" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Cost
                </td>

                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Description".',') !== FALSE) { echo " checked"; } ?> value="Description" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Description
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Quote Description".',') !== FALSE) { echo " checked"; } ?> value="Quote Description" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Quote Description
                </td>

            </tr>

            <tr>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { echo " checked"; } ?> value="Final Retail Price" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Final Retail Price
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { echo " checked"; } ?> value="Admin Price" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Admin Price
                </td>
                <td>
                   <input type="checkbox" <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { echo " checked"; } ?> value="Wholesale Price" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Wholesale Price
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { echo " checked"; } ?> value="Commercial Price" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Commercial Price
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Client Price".',') !== FALSE) { echo " checked"; } ?> value="Client Price" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Client Price
                </td>

                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) { echo " checked"; } ?> value="Minimum Billable" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Minimum Billable
                </td>
            </tr>

            <tr>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) { echo " checked"; } ?> value="Estimated Hours" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Estimated Hours
                </td>

                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Actual Hours".',') !== FALSE) { echo " checked"; } ?> value="Actual Hours" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Actual Hours
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."MSRP".',') !== FALSE) {
                    echo " checked"; } ?> value="MSRP" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;MSRP
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Service Code".',') !== FALSE) {
                    echo " checked"; } ?> value="Service Code" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Service Code
                </td>

                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Invoice Description".',') !== FALSE) {
                    echo " checked"; } ?> value="Invoice Description" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Invoice Description
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Ticket Description".',') !== FALSE) {
                    echo " checked"; } ?> value="Ticket Description" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;<?= TICKET_NOUN ?> Description
                </td>
            </tr>

            <tr>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Name".',') !== FALSE) {
                    echo " checked"; } ?> value="Name" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Name
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Fee".',') !== FALSE) {
                    echo " checked"; } ?> value="Fee" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Fee
                </td>

                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Unit Price".',') !== FALSE) { echo " checked"; } ?> value="Unit Price" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Unit Price
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Unit Cost".',') !== FALSE) { echo " checked"; } ?> value="Unit Cost" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Unit Cost
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Rent Price".',') !== FALSE) { echo " checked"; } ?> value="Rent Price" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Rent Price
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Rental Days".',') !== FALSE) { echo " checked"; } ?> value="Rental Days" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Rental Days
                </td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Rental Weeks".',') !== FALSE) { echo " checked"; } ?> value="Rental Weeks" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Rental Weeks
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Rental Months".',') !== FALSE) { echo " checked"; } ?> value="Rental Months" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Rental Months
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Rental Years".',') !== FALSE) { echo " checked"; } ?> value="Rental Years" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Rental Years
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Reminder/Alert".',') !== FALSE) { echo " checked"; } ?> value="Reminder/Alert" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Reminder/Alert
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Daily".',') !== FALSE) { echo " checked"; } ?> value="Daily" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Daily
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Weekly".',') !== FALSE) { echo " checked"; } ?> value="Weekly" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Weekly
                </td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Monthly".',') !== FALSE) { echo " checked"; } ?> value="Monthly" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Monthly
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Annually".',') !== FALSE) { echo " checked"; } ?> value="Annually" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Annually
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."#Of Days".',') !== FALSE) { echo " checked"; } ?> value="#Of Days" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;# Of Days
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."#Of Hours".',') !== FALSE) { echo " checked"; } ?> value="#Of Hours" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;# Of Hours
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."#Of Kilometers".',') !== FALSE) { echo " checked"; } ?> value="#Of Kilometers" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;# Of Kilometers
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."#Of Miles".',') !== FALSE) { echo " checked"; } ?> value="#Of Miles" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;# Of Miles
                </td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Hourly Rate".',') !== FALSE) { echo " checked"; } ?> value="Hourly Rate" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Hourly Rate
                </td>
    			<td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Purchase Order Price".',') !== FALSE) { echo " checked"; } ?> value="Purchase Order Price" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Purchase Order Price
                </td>
    			<td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Sales Order Price".',') !== FALSE) { echo " checked"; } ?> value="Sales Order Price" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;<?= SALES_ORDER_NOUN ?> Price
                </td>
    			<td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Include in P.O.S.".',') !== FALSE) {
                    echo " checked"; } ?> value="Include in P.O.S." style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Include in <?= POS_ADVANCE_TILE ?>
                </td>
    			<td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Include in Purchase Orders".',') !== FALSE) {
                    echo " checked"; } ?> value="Include in Purchase Orders" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Include in Purchase Orders
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Include in Sales Orders".',') !== FALSE) {
                    echo " checked"; } ?> value="Include in Sales Orders" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Include in <?= SALES_ORDER_TILE ?>
                </td>
            </tr>

            <tr>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."GST exempt".',') !== FALSE) { echo " checked"; } ?> value="GST exempt" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;GST Exempt
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Appointment Type".',') !== FALSE) { echo " checked"; } ?> value="Appointment Type" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Appointment Type
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Quantity".',') !== FALSE) { echo " checked"; } ?> value="Quantity" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Quantity
                </td>
                <td>
                    <input type="checkbox" <?php if (strpos($value_config, ','."Checklist".',') !== FALSE) { echo " checked"; } ?> value="Checklist" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Checklist
                </td>
                <td>
                    <input type="checkbox" <?= strpos($value_config, ',Service Create Ticket,') !== FALSE ? "checked" : '' ?> value="Service Create Ticket" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Create <?= TICKET_NOUN ?> From Service
                </td>
                <td>
                    <input type="checkbox" <?= strpos($value_config, ',Service Image,') !== FALSE ? "checked" : '' ?> value="Service Image" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Service Image
                </td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" <?= strpos($value_config, ',Contacts # of Rooms,') !== FALSE ? "checked" : '' ?> value="Contacts # of Rooms" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Contacts # of Rooms
                </td>
                <td>
                    <input type="checkbox" <?= strpos($value_config, ',Rate Card,') !== FALSE ? "checked" : '' ?> value="Rate Card" style="height: 20px; width: 20px;" name="services[]">&nbsp;&nbsp;Rate Card
                </td>
            </tr>

        </table>
    </div>

    <div class="form-group pull-right">
    	<a href="index.php" class="btn brand-btn">Back</a>
        <button	type="submit" name="submit"	value="Submit" class="btn brand-btn">Submit</button>
    </div>

</form>