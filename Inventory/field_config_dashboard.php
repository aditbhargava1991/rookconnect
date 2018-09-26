<?php
if (isset($_POST['submit'])) {
    $inventory_dashboard = implode(',',$_POST['inventory_dashboard']);
    $tab_dashboard       = filter_var($_POST['tab_dashboard'],FILTER_SANITIZE_STRING);
    //if (strpos(','.$inventory_dashboard.',',','.'Category'.',') === false) {
    //    $inventory_dashboard = 'Category,'.$inventory_dashboard;
    //}
    
    if ( empty($tab_dashboard) ) {
        // A Category was not selected from the Tabs dropdown.
        // So we take it as the default for Display All and Last 25 Added dashboards
        $get_field_config = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `inventory_dashboard` FROM `field_config_inventory` WHERE `tab`='Top'" ) );
        
        if ( $get_field_config['configinvid'] > 0 ) {
            $query_update_config  = "UPDATE `field_config_inventory` SET `inventory_dashboard`='$inventory_dashboard' WHERE `tab`='Top'";
            $result_update_config = mysqli_query($dbc, $query_update_employee);
        } else {
            $query_insert_config = "INSERT INTO `field_config_inventory` (`tab`, `inventory_dashboard`) VALUES ('Top', '$inventory_dashboard')";
            $result_insert_config = mysqli_query($dbc, $query_insert_config);
        }
    
    } else {
        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configinvid) AS configinvid FROM field_config_inventory WHERE tab='$tab_dashboard' AND accordion IS NULL"));
        
        if($get_field_config['configinvid'] > 0) {
            $query_update_employee = "UPDATE `field_config_inventory` SET inventory_dashboard = '$inventory_dashboard' WHERE tab='$tab_dashboard'";
            $result_update_employee = mysqli_query($dbc, $query_update_employee);
        } else {
            $query_insert_config = "INSERT INTO `field_config_inventory` (`tab`, `inventory_dashboard`) VALUES ('$tab_dashboard', '$inventory_dashboard')";
            $result_insert_config = mysqli_query($dbc, $query_insert_config);
        }
    }
}
$invtype = $_GET['tab'];
$accr = $_GET['accr'];
$type = $_GET['type'];

$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT inventory FROM field_config_inventory WHERE tab='$invtype' AND accordion='$accr'"));
$inventory_config = ','.$get_field_config['inventory'].',';

$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT inventory_dashboard FROM field_config_inventory WHERE tab='$invtype' AND accordion IS NULL"));
$inventory_dashboard_config = ','.$get_field_config['inventory_dashboard'].',';
$get_field_order = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT GROUP_CONCAT(`order` SEPARATOR ',') AS all_order FROM field_config_inventory WHERE tab='$invtype'"));
?>

<div class="gap-top">
    <div class="form-group">
        <label for="fax_number" class="col-sm-4 control-label">
            <span class="popover-examples list-inline" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Click here to choose your desired tab. These will be visible to you on the Inventory Dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            Tabs:
        </label>
        <div class="col-sm-8">
            <select data-placeholder="Choose a Tab..." id="tab_dashboard" name="tab_dashboard" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <?php
                $tabs = get_config($dbc, 'inventory_tabs');
                
                $inventory_setting = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `value` FROM `inventory_setting` WHERE `inventorysettingid` = 1"));
                $set_check_value = $inventory_setting['value'];
                $tab_po = strpos(','.$set_check_value.',', ",purchaseorders") !== false ? '#*#Purchase Orders' : '';
                $tabs .= !empty($tab_po) ? $tab_po : '';
                $tab_co = strpos(','.$set_check_value.',', ",customerorders") !== false ? '#*#Customer Orders' : '';
                $tabs .= !empty($tab_co) ? $tab_co : '';
                $tab_pa = strpos(','.$set_check_value.',', ",pallet") !== false ? '#*#Pallet #' : '';
                $tabs .= !empty($tab_pa) ? $tab_pa : '';
                
                $each_tab = explode('#*#', $tabs);
                foreach ($each_tab as $cat_tab) {
                    $url_tab = preg_replace('/[^a-z]/','',strtolower($cat_tab));
                    if ($invtype == $url_tab) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option ".$selected." value='". $url_tab."'>".$cat_tab.'</option>';
                }
                
                $cat_list = explode('#*#',get_config($dbc, 'inventory_tabs'));
                foreach($cat_list as $each_cat) {
                    if($invtype == preg_replace('/[^a-z]/','',strtolower($each_cat))) {
                        $invtype = filter_var($each_cat,FILTER_SANITIZE_STRING);
                    }
                }
              ?>
            </select>
        </div>
    </div>

    <h3>Dashboard</h3>
    <div class="panel-group" id="accordion2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_1" >
                        Description<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_1" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Description".',') !== false) { echo " checked"; } ?> value="Description" name="inventory_dashboard[]">&nbsp;&nbsp;Description
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Category".',') !== false) { echo " checked"; } ?> value="Category" name="inventory_dashboard[]">&nbsp;&nbsp;Tab
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Subcategory".',') !== false) { echo " checked"; } ?> value="Subcategory" name="inventory_dashboard[]">&nbsp;&nbsp;Subtab
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Name".',') !== false) { echo " checked"; } ?> value="Name" name="inventory_dashboard[]">&nbsp;&nbsp;Name
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Product Name".',') !== false) { echo " checked"; } ?> value="Product Name" name="inventory_dashboard[]">&nbsp;&nbsp;Product Name
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Type".',') !== false) { echo " checked"; } ?> value="Type" name="inventory_dashboard[]">&nbsp;&nbsp;Tab
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Color".',') !== false) { echo " checked"; } ?> value="Color" name="inventory_dashboard[]">&nbsp;&nbsp;Color

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_2" >
                        Unique Identifier<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_2" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Code".',') !== false) { echo " checked"; } ?> value="Code" name="inventory_dashboard[]">&nbsp;&nbsp;Code
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."ID #".',') !== false) { echo " checked"; } ?> value="ID #" name="inventory_dashboard[]">&nbsp;&nbsp;ID #
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Item SKU".',') !== false) { echo " checked"; } ?> value="Item SKU" name="inventory_dashboard[]">&nbsp;&nbsp;Item SKU
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Part #".',') !== false) { echo " checked"; } ?> value="Part #" name="inventory_dashboard[]">&nbsp;&nbsp;Part #
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_3" >
                        Product Cost<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_3" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Average Cost".',') !== false) { echo " checked"; } ?> value="Average Cost" name="inventory_dashboard[]">&nbsp;&nbsp;Average Cost
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Unit Cost".',') !== false) { echo " checked"; } ?> value="Unit Cost" name="inventory_dashboard[]">&nbsp;&nbsp;Unit Cost
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Cost".',') !== false) { echo " checked"; } ?> value="Cost" name="inventory_dashboard[]">&nbsp;&nbsp;Cost
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."CDN Cost Per Unit".',') !== false) { echo " checked"; } ?> value="CDN Cost Per Unit" name="inventory_dashboard[]">&nbsp;&nbsp;CDN Cost Per Unit
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."USD Cost Per Unit".',') !== false) { echo " checked"; } ?> value="USD Cost Per Unit" name="inventory_dashboard[]">&nbsp;&nbsp;USD Cost Per Unit
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Drum Unit Cost".',') !== false) { echo " checked"; } ?> value="Drum Unit Cost" name="inventory_dashboard[]">&nbsp;&nbsp;Drum Unit Cost
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Tote Unit Cost".',') !== false) { echo " checked"; } ?> value="Tote Unit Cost" name="inventory_dashboard[]">&nbsp;&nbsp;Tote Unit Cost
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."COGS".',') !== false) { echo " checked"; } ?> value="COGS" name="inventory_dashboard[]">&nbsp;&nbsp;COGS GL Code
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."USD Invoice".',') !== false) { echo " checked"; } ?> value="USD Invoice" name="inventory_dashboard[]">&nbsp;&nbsp;USD Invoice

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_4" >
                        Purchase Info<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_4" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Vendor".',') !== false) { echo " checked"; } ?> value="Vendor" name="inventory_dashboard[]">&nbsp;&nbsp;Vendor
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Purchase Cost".',') !== false) { echo " checked"; } ?> value="Purchase Cost" name="inventory_dashboard[]">&nbsp;&nbsp;Purchase Cost
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Date Of Purchase".',') !== false) { echo " checked"; } ?> value="Date Of Purchase" name="inventory_dashboard[]">&nbsp;&nbsp;Date Of Purchase

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_5" >
                        Shipping & Receiving<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_5" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Shipping Rate".',') !== false) { echo " checked"; } ?> value="Shipping Rate" name="inventory_dashboard[]">&nbsp;&nbsp;Shipping Rate
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Shipping Cash".',') !== false) { echo " checked"; } ?> value="Shipping Cash" name="inventory_dashboard[]">&nbsp;&nbsp;Shipping Cash
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Freight Charge".',') !== false) { echo " checked"; } ?> value="Freight Charge" name="inventory_dashboard[]">&nbsp;&nbsp;Freight Charge
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Exchange Rate".',') !== false) { echo " checked"; } ?> value="Exchange Rate" name="inventory_dashboard[]">&nbsp;&nbsp;Exchange Rate
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Exchange $".',') !== false) { echo " checked"; } ?> value="Exchange $" name="inventory_dashboard[]">&nbsp;&nbsp;Exchange $
					
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Ticket PO".',') !== false) { echo " checked"; } ?> value="Ticket PO" name="inventory_dashboard[]">&nbsp;&nbsp;Purchase Order with Line Item (from <?= TICKET_NOUN ?>)
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Ticket PO Only".',') !== false) { echo " checked"; } ?> value="Ticket PO Only" name="inventory_dashboard[]">&nbsp;&nbsp;Purchase Order (from <?= TICKET_NOUN ?>)
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Ticket PO Line".',') !== false) { echo " checked"; } ?> value="Ticket PO Line" name="inventory_dashboard[]">&nbsp;&nbsp;PO Line Item (from <?= TICKET_NOUN ?>)
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Customer Order".',') !== false) { echo " checked"; } ?> value="Customer Order" name="inventory_dashboard[]">&nbsp;&nbsp;Customer Order (from <?= TICKET_NOUN ?>)
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Pallet Num".',') !== false) { echo " checked"; } ?> value="Pallet Num" name="inventory_dashboard[]">&nbsp;&nbsp;Pallet #
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Ticket Label BOL".',') !== false) { echo " checked"; } ?> value="Ticket Label BOL" name="inventory_dashboard[]">&nbsp;&nbsp;Bill of Lading

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_6" >
                        Pricing<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_6" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Sell Price".',') !== false) { echo " checked"; } ?> value="Sell Price" name="inventory_dashboard[]">&nbsp;&nbsp;Sell Price
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Final Retail Price".',') !== false) { echo " checked"; } ?> value="Final Retail Price" name="inventory_dashboard[]">&nbsp;&nbsp;Final Retail Price
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Wholesale Price".',') !== false) { echo " checked"; } ?> value="Wholesale Price" name="inventory_dashboard[]">&nbsp;&nbsp;Wholesale Price
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Commercial Price".',') !== false) { echo " checked"; } ?> value="Commercial Price" name="inventory_dashboard[]">&nbsp;&nbsp;Commercial Price
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Client Price".',') !== false) { echo " checked"; } ?> value="Client Price" name="inventory_dashboard[]">&nbsp;&nbsp;Client Price
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Preferred Price".',') !== false) { echo " checked"; } ?> value="Preferred Price" name="inventory_dashboard[]">&nbsp;&nbsp;Preferred Price
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Admin Price".',') !== false) { echo " checked"; } ?> value="Admin Price" name="inventory_dashboard[]">&nbsp;&nbsp;Admin Price
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Web Price".',') !== false) { echo " checked"; } ?> value="Web Price" name="inventory_dashboard[]">&nbsp;&nbsp;Web Price
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Clearance Price".',') !== false) { echo " checked"; } ?> value="Clearance Price" name="inventory_dashboard[]">&nbsp;&nbsp;Clearance Price
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Commission Price".',') !== false) { echo " checked"; } ?> value="Commission Price" name="inventory_dashboard[]">&nbsp;&nbsp;Commission Price
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."MSRP".',') !== false) { echo " checked"; } ?> value="MSRP" name="inventory_dashboard[]">&nbsp;&nbsp;MSRP
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Unit Price".',') !== false) { echo " checked"; } ?> value="Unit Price" name="inventory_dashboard[]">&nbsp;&nbsp;Unit Price
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Purchase Order Price".',') !== false) { echo " checked"; } ?> value="Purchase Order Price" name="inventory_dashboard[]">&nbsp;&nbsp;Purchase Order Price
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Sales Order Price".',') !== false) { echo " checked"; } ?> value="Sales Order Price" name="inventory_dashboard[]">&nbsp;&nbsp;<?= SALES_ORDER_NOUN ?> Price
                    <input type="checkbox" <?php if (strpos($value_config, ','."Drum Unit Cost".',') !== false) { echo " checked"; } ?> value="Drum Unit Cost" name="inventory_dashboard[]">&nbsp;&nbsp;Drum Unit Cost
                    <input type="checkbox" <?php if (strpos($value_config, ','."Drum Unit Price".',') !== false) { echo " checked"; } ?> value="Drum Unit Price" name="inventory_dashboard[]">&nbsp;&nbsp;Drum Unit Price
                    <input type="checkbox" <?php if (strpos($value_config, ','."Tote Unit Cost".',') !== false) { echo " checked"; } ?> value="Tote Unit Cost" name="inventory_dashboard[]">&nbsp;&nbsp;Tote Unit Cost
                    <input type="checkbox" <?php if (strpos($value_config, ','."Tote Unit Price".',') !== false) { echo " checked"; } ?> value="Tote Unit Price" name="inventory_dashboard[]">&nbsp;&nbsp;Tote Unit Price
                    <input type="checkbox" <?php if (strpos($value_config, ','."WCB Price".',') !== false) { echo " checked"; } ?> value="WCB Price" name="inventory_dashboard[]">&nbsp;&nbsp;WCB Price
                    <input type="checkbox" <?php if (strpos($value_config, ','."Suggested Retail Price".',') !== false) { echo " checked"; } ?> value="Suggested Retail Price" name="inventory_dashboard[]">&nbsp;&nbsp;Suggested Retail Price
                    <input type="checkbox" <?php if (strpos($value_config, ','."Rush Price".',') !== false) { echo " checked"; } ?> value="Rush Price" name="inventory_dashboard[]">&nbsp;&nbsp;Rush Price

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_7" >
                        Markup<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_7" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Markup By $".',') !== false) { echo " checked"; } ?> value="Markup By $" name="inventory_dashboard[]">&nbsp;&nbsp;Markup By $
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Markup By %".',') !== false) { echo " checked"; } ?> value="Markup By %" name="inventory_dashboard[]">&nbsp;&nbsp;Markup By %

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_8" >
                        Stock<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_8" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Current Stock".',') !== false) { echo " checked"; } ?> value="Current Stock" name="inventory_dashboard[]">&nbsp;&nbsp;Current Stock
                    <!-- Taken out to remove confusion between quantity and current inventory <input type="checkbox" <?php //if (strpos($inventory_dashboard_config, ','."Current Inventory".',') !== false) { echo " checked"; } ?> value="Current Inventory" name="inventory_dashboard[]">&nbsp;&nbsp;Current Inventory-->
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Quantity".',') !== false) { echo " checked"; } ?> value="Quantity" name="inventory_dashboard[]">&nbsp;&nbsp;Quantity
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Expected".',') !== false) { echo " checked"; } ?> value="Expected" name="inventory_dashboard[]">&nbsp;&nbsp;Expected
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Received".',') !== false) { echo " checked"; } ?> value="Received" name="inventory_dashboard[]">&nbsp;&nbsp;Received
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Discrepancy".',') !== false) { echo " checked"; } ?> value="Discrepancy" name="inventory_dashboard[]">&nbsp;&nbsp;Discrepancy
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Variance".',') !== false) { echo " checked"; } ?> value="Variance" name="inventory_dashboard[]">&nbsp;&nbsp;GL Code
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Write-offs".',') !== false) { echo " checked"; } ?> value="Write-offs" name="inventory_dashboard[]">&nbsp;&nbsp;Write-offs

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Buying Units".',') !== false) { echo " checked"; } ?> value="Buying Units" name="inventory_dashboard[]">&nbsp;&nbsp;Buying Units
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Selling Units".',') !== false) { echo " checked"; } ?> value="Selling Units" name="inventory_dashboard[]">&nbsp;&nbsp;Selling Units
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Stocking Units".',') !== false) { echo " checked"; } ?> value="Stocking Units" name="inventory_dashboard[]">&nbsp;&nbsp;Stocking Units

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_9" >
                        Location<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_9" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Location".',') !== false) { echo " checked"; } ?> value="Location" name="inventory_dashboard[]">&nbsp;&nbsp;Location
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."LSD".',') !== false) { echo " checked"; } ?> value="LSD" name="inventory_dashboard[]">&nbsp;&nbsp;LSD

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_10" >
                        Dimensions<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_10" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Size".',') !== false) { echo " checked"; } ?> value="Size" name="inventory_dashboard[]">&nbsp;&nbsp;Size
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Weight".',') !== false) { echo " checked"; } ?> value="Weight" name="inventory_dashboard[]">&nbsp;&nbsp;Weight

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_11" >
                        Alerts<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_11" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Min Max".',') !== false) { echo " checked"; } ?> value="Min Max" name="inventory_dashboard[]">&nbsp;&nbsp;Min Max
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Min Bin".',') !== false) { echo " checked"; } ?> value="Min Bin" name="inventory_dashboard[]">&nbsp;&nbsp;Min Bin

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_12" >
                        Time Allocation<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_12" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Estimated Hours".',') !== false) { echo " checked"; } ?> value="Estimated Hours" name="inventory_dashboard[]">&nbsp;&nbsp;Estimated Hours
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Actual Hours".',') !== false) { echo " checked"; } ?> value="Actual Hours" name="inventory_dashboard[]">&nbsp;&nbsp;Actual Hours

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_13" >
                        Admin Fees<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_13" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Minimum Billable".',') !== false) { echo " checked"; } ?> value="Minimum Billable" name="inventory_dashboard[]">&nbsp;&nbsp;Minimum Billable
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."GL Revenue".',') !== false) { echo " checked"; } ?> value="GL Revenue" name="inventory_dashboard[]">&nbsp;&nbsp;GL Revenue

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."GL Assets".',') !== false) { echo " checked"; } ?> value="GL Assets" name="inventory_dashboard[]">&nbsp;&nbsp;GL Assets

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_14" >
                        Quote<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_14" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Quote Description".',') !== false) { echo " checked"; } ?> value="Quote Description" name="inventory_dashboard[]">&nbsp;&nbsp;Quote Description

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_15" >
                        Status<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_15" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Status".',') !== false) { echo " checked"; } ?> value="Status" name="inventory_dashboard[]">&nbsp;&nbsp;Status

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_16" >
                        Display On Website<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_16" class="panel-collapse collapse">
                <div class="panel-body">
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Display On Website".',') !== false) { echo " checked"; } ?> value="Display On Website" name="inventory_dashboard[]">&nbsp;&nbsp;Display On Website
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Featured On Website".',') !== false) { echo " checked"; } ?> value="Featured On Website" name="inventory_dashboard[]">&nbsp;&nbsp;Featured On Website
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."New Item".',') !== false) { echo " checked"; } ?> value="New Item" name="inventory_dashboard[]">&nbsp;&nbsp;Display Item As New On Website
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Item On Sale".',') !== false) { echo " checked"; } ?> value="Item On Sale" name="inventory_dashboard[]">&nbsp;&nbsp;Display Item As On Sale On Website
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Item On Clearance".',') !== false) { echo " checked"; } ?> value="Item On Clearance" name="inventory_dashboard[]">&nbsp;&nbsp;Display Item As Clearance On Website
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_17" >
                        General<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_17" class="panel-collapse collapse">
                <div class="panel-body">
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Main Image".',') !== false) { echo " checked"; } ?> value="Main Image" name="inventory_dashboard[]">&nbsp;&nbsp;Main Image
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Spec Sheet".',') !== false) { echo " checked"; } ?> value="Spec Sheet" name="inventory_dashboard[]">&nbsp;&nbsp;Spec Sheet
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Notes".',') !== false) { echo " checked"; } ?> value="Notes" name="inventory_dashboard[]">&nbsp;&nbsp;Notes
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Comments".',') !== false) { echo " checked"; } ?> value="Comments" name="inventory_dashboard[]">&nbsp;&nbsp;Comments

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_18" >
                        Rental<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_18" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Rent Price".',') !== false) { echo " checked"; } ?> value="Rent Price" name="inventory_dashboard[]">&nbsp;&nbsp;Rent Price
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Rental Days".',') !== false) { echo " checked"; } ?> value="Rental Days" name="inventory_dashboard[]">&nbsp;&nbsp;Rental Days
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Rental Weeks".',') !== false) { echo " checked"; } ?> value="Rental Weeks" name="inventory_dashboard[]">&nbsp;&nbsp;Rental Weeks
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Rental Months".',') !== false) { echo " checked"; } ?> value="Rental Months" name="inventory_dashboard[]">&nbsp;&nbsp;Rental Months
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Rental Years".',') !== false) { echo " checked"; } ?> value="Rental Years" name="inventory_dashboard[]">&nbsp;&nbsp;Rental Years
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Reminder/Alert".',') !== false) { echo " checked"; } ?> value="Reminder/Alert" name="inventory_dashboard[]">&nbsp;&nbsp;Reminder/Alert

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_51" >
                        Day/Week/Month/Year<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_51" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Daily".',') !== false) { echo " checked"; } ?> value="Daily" name="inventory_dashboard[]">&nbsp;&nbsp;Daily
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Weekly".',') !== false) { echo " checked"; } ?> value="Weekly" name="inventory_dashboard[]">&nbsp;&nbsp;Weekly
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Monthly".',') !== false) { echo " checked"; } ?> value="Monthly" name="inventory_dashboard[]">&nbsp;&nbsp;Monthly
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Annually".',') !== false) { echo " checked"; } ?> value="Annually" name="inventory_dashboard[]">&nbsp;&nbsp;Annually
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."#Of Days".',') !== false) { echo " checked"; } ?> value="#Of Days" name="inventory_dashboard[]">&nbsp;&nbsp;#Of Days
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."#Of Hours".',') !== false) { echo " checked"; } ?> value="#Of Hours" name="inventory_dashboard[]">&nbsp;&nbsp;#Of Hours

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_53" >
                        Vehicle<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_53" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."#Of Kilometers".',') !== false) { echo " checked"; } ?> value="#Of Kilometers" name="inventory_dashboard[]">&nbsp;&nbsp;#Of Kilometers
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."#Of Miles".',') !== false) { echo " checked"; } ?> value="#Of Miles" name="inventory_dashboard[]">&nbsp;&nbsp;#Of Miles

                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_ordersandpos" >
                        Inclusion<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_ordersandpos" class="panel-collapse collapse">
                <div class="panel-body">
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Include in P.O.S.".',') !== false) {
                        echo " checked"; } ?> value="Include in P.O.S." name="inventory_dashboard[]">&nbsp;&nbsp;Include in Point of Sale
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Include in Purchase Orders".',') !== false) {
                        echo " checked"; } ?> value="Include in Purchase Orders" name="inventory_dashboard[]">&nbsp;&nbsp;Include in Purchase Orders
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Include in Sales Orders".',') !== false) {
                        echo " checked"; } ?> value="Include in Sales Orders" name="inventory_dashboard[]">&nbsp;&nbsp;Include in <?= SALES_ORDER_TILE ?>
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Include in Product".',') !== false) {
                        echo " checked"; } ?> value="Include in Product" name="inventory_dashboard[]">&nbsp;&nbsp;Include in Product
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_amount" >
                        Amount<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_amount" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Min Amount".',') !== false) { echo " checked"; } ?> value="Min Amount" name="inventory_dashboard[]">&nbsp;&nbsp;Min Amount
                    <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Max Amount".',') !== false) { echo " checked"; } ?> value="Max Amount" name="inventory_dashboard[]">&nbsp;&nbsp;Max Amount

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_history" >
                        History<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_history" class="panel-collapse collapse">
                <div class="panel-body">
                    <input type="checkbox" <?php echo (strpos($inventory_dashboard_config, ','."History".',') !== false ? " checked" : ''); ?> value="History" name="inventory_dashboard[]">&nbsp;&nbsp;Inventory Full Change Log
                    <input type="checkbox" <?php echo (strpos($inventory_dashboard_config, ','."Change Cost".',') !== false ? " checked" : ''); ?> value="Change Cost" name="inventory_dashboard[]">&nbsp;&nbsp;Inventory Cost Change Log
                    <input type="checkbox" <?php echo (strpos($inventory_dashboard_config, ','."Change Qty".',') !== false ? " checked" : ''); ?> value="Change Qty" name="inventory_dashboard[]">&nbsp;&nbsp;Inventory Quantity Change Log
                    <input type="checkbox" <?php echo (strpos($inventory_dashboard_config, ','."Change Comment".',') !== false ? " checked" : ''); ?> value="Change Comment" name="inventory_dashboard[]">&nbsp;&nbsp;Inventory Change Details
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</div>