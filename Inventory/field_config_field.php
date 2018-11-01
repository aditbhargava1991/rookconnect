<?php
if (isset($_POST['submit'])) {
    $tab_field = filter_var($_POST['tab_field'],FILTER_SANITIZE_STRING);
    $accordion = filter_var($_POST['accordion'],FILTER_SANITIZE_STRING);
    $order     = filter_var($_POST['order'],FILTER_SANITIZE_STRING);
    if (!empty ($order) ) {
        $order_update_query = ", `order`='$order'";
    } else {
        $order_update_query = '';
    }

    $inventory = implode(',',$_POST['inventory']);

    //if (strpos(','.$inventory.',',','.'Category'.',') === false) {
    //    $inventory = 'Category,'.$inventory;
    //}

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configinvid) AS configinvid FROM field_config_inventory WHERE tab='$tab_field' AND accordion='$accordion'"));
    if($get_field_config['configinvid'] > 0) {
        $query_update_employee = "UPDATE `field_config_inventory` SET inventory = '$inventory' " . $order_update_query . " WHERE tab='$tab_field' AND accordion='$accordion'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `field_config_inventory` (`tab`, `accordion`, `inventory`, `order`) VALUES ('$tab_field', '$accordion', '$inventory', '$order')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
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
            <span class="popover-examples list-inline" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Click here to choose your desired tab."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            Tabs:
        </label>
        <div class="col-sm-8">
            <select data-placeholder="Choose a Tab..." id="tab_field" name="tab_field" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <?php
                $tabs = get_config($dbc, 'inventory_tabs');
                $each_tab = explode('#*#', $tabs);
                foreach ($each_tab as $cat_tab) {
                    $url_tab = preg_replace('/[^a-z]/','',strtolower($cat_tab));
                    echo "<option ".($invtype == $url_tab ? 'selected' : '')." value='". $url_tab."'>".$cat_tab.'</option>';
                }
                
                //$cat_list = explode('#*#',get_config($dbc, 'inventory_tabs'));
                //foreach($cat_list as $each_cat) {
                //  if($invtype == preg_replace('/[^a-z]/','',strtolower($each_cat))) {
                //      $invtype = filter_var($each_cat,FILTER_SANITIZE_STRING);
                //  }
                //}
              ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="fax_number" class="col-sm-4 control-label">
            <span class="popover-examples list-inline" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Click here first to choose your desired fields that will be in the selected tab. Make sure to choose the order in which you would like these to be viewed in the second drop down."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            Accordion:
        </label>
        <div class="col-sm-8">
            <select data-placeholder="Choose an Accordion..." id="acc" name="accordion" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <option <?php if ($accr == "Description") { echo " selected"; } ?> value="Description"><?= get_field_config_inventory($dbc, 'Description', 'order', $invtype) ?> : Description</option>
              <option <?php if ($accr == "Unique Identifier") { echo " selected"; } ?> value="Unique Identifier"><?php echo get_field_config_inventory($dbc, 'Unique Identifier', 'order', $invtype); ?> : Unique Identifier</option>
              <option <?php if ($accr == "Product Cost") { echo " selected"; } ?> value="Product Cost"><?php echo get_field_config_inventory($dbc, 'Product Cost', 'order', $invtype); ?> : Product Cost</option>
              <option <?php if ($accr == "Purchase Info") { echo " selected"; } ?> value="Purchase Info"><?php echo get_field_config_inventory($dbc, 'Purchase Info', 'order', $invtype); ?> : Purchase Info</option>
              <option <?php if ($accr == "Shipping Receiving") { echo " selected"; } ?> value="Shipping Receiving"><?php echo get_field_config_inventory($dbc, 'Shipping Receiving', 'order', $invtype); ?> : Shipping Receiving</option>
              <option <?php if ($accr == "Pricing") { echo " selected"; } ?> value="Pricing"><?php echo get_field_config_inventory($dbc, 'Pricing', 'order', $invtype); ?> : Pricing</option>
              <option <?php if ($accr == "Markup") { echo " selected"; } ?> value="Markup"><?php echo get_field_config_inventory($dbc, 'Markup', 'order', $invtype); ?> : Markup</option>
              <option <?php if ($accr == "Stock") { echo " selected"; } ?> value="Stock"><?php echo get_field_config_inventory($dbc, 'Stock', 'order', $invtype); ?> : Stock</option>
              <option <?php if ($accr == "Location") { echo " selected"; } ?> value="Location"><?php echo get_field_config_inventory($dbc, 'Location', 'order', $invtype); ?> : Location</option>
              <option <?php if ($accr == "Dimensions") { echo " selected"; } ?> value="Dimensions"><?php echo get_field_config_inventory($dbc, 'Dimensions', 'order', $invtype); ?> : Dimensions</option>
              <option <?php if ($accr == "Alerts") { echo " selected"; } ?> value="Alerts"><?php echo get_field_config_inventory($dbc, 'Alerts', 'order', $invtype); ?> : Alerts</option>
              <option <?php if ($accr == "Time Allocation") { echo " selected"; } ?> value="Time Allocation"><?php echo get_field_config_inventory($dbc, 'Time Allocation', 'order', $invtype); ?> : Time Allocation</option>
              <option <?php if ($accr == "Admin Fees") { echo " selected"; } ?> value="Admin Fees"><?php echo get_field_config_inventory($dbc, 'Admin Fees', 'order', $invtype); ?> : Admin Fees</option>
              <option <?php if ($accr == "Quote") { echo " selected"; } ?> value="Quote"><?php echo get_field_config_inventory($dbc, 'Quote', 'order', $invtype); ?> : Quote</option>
              <option <?php if ($accr == "Status") { echo " selected"; } ?> value="Status"><?php echo get_field_config_inventory($dbc, 'Status', 'order', $invtype); ?> : Status</option>
              <option <?php if ($accr == "Display On Website") { echo " selected"; } ?> value="Display On Website"><?php echo get_field_config_inventory($dbc, 'Display On Website', 'order', $invtype); ?> : Display On Website</option>
              <option <?php if ($accr == "General") { echo " selected"; } ?> value="General"><?php echo get_field_config_inventory($dbc, 'General', 'order', $invtype); ?> : General</option>
              <option <?php if ($accr == "Rental") { echo " selected"; } ?> value="Rental"><?php echo get_field_config_inventory($dbc, 'Rental', 'order', $invtype); ?> : Rental</option>
              <option <?php if ($accr == "Day/Week/Month/Year") { echo " selected"; } ?> value="Day/Week/Month/Year"><?php echo get_field_config_inventory($dbc, 'Day/Week/Month/Year', 'order', $invtype); ?> : Day/Week/Month/Year</option>
              <option <?php if ($accr == "Vehicle") { echo " selected"; } ?> value="Vehicle"><?php echo get_field_config_inventory($dbc, 'Vehicle', 'order', $invtype); ?> : Vehicle</option>
              <option <?php if ($accr == "Bill of Material") { echo " selected"; } ?> value="Bill of Material"><?php echo get_field_config_inventory($dbc, 'Bill of Material', 'order', $invtype); ?> : Bill of Material</option>
              <option <?php if ($accr == "Supplimentary Products") { echo " selected"; } ?> value="Supplimentary Products"><?php echo get_field_config_inventory($dbc, 'Supplimentary Products', 'order', $invtype); ?> : Supplimentary Products</option>
              <option <?php if ($accr == "Inventory History") { echo " selected"; } ?> value="Inventory History"><?php echo get_field_config_inventory($dbc, 'Inventory History', 'order', $invtype); ?> : Inventory History</option>
            </select>
            <select data-placeholder="Choose an Order..." name="order" class="chosen-select-deselect form-control" width="380">
                <option value=""></option>
                <?php
                for($m=1;$m<=30;$m++) { ?>
                    <option <?php if (get_field_config_inventory($dbc, $accr, 'order', $invtype) == $m) { echo  'selected="selected"'; } else if (strpos(','.$get_field_order['all_order'].',', ','.$m.',') !== false) { echo " disabled"; } ?> value="<?php echo $m;?>"><?php echo $m;?></option>
                <?php }
                ?>
            </select>
        </div>
    </div>

    <h3>Fields</h3>
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

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Name".',') !== false) { echo " checked"; } ?> value="Name" name="inventory[]">&nbsp;&nbsp;Name&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Name On Website".',') !== false) { echo " checked"; } ?> value="Name On Website" name="inventory[]">&nbsp;&nbsp;Name On Website&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Product Name".',') !== false) { echo " checked"; } ?> value="Product Name" name="inventory[]">&nbsp;&nbsp;Product Name&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Brand".',') !== false) { echo " checked"; } ?> value="Brand" name="inventory[]">&nbsp;&nbsp;Brand&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Category".',') !== false) { echo " checked"; } ?> value="Category" name="inventory[]">&nbsp;&nbsp;Tab&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Subcategory".',') !== false) { echo " checked"; } ?> value="Subcategory" name="inventory[]">&nbsp;&nbsp;Subtab&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Type".',') !== false) { echo " checked"; } ?> value="Type" name="inventory[]">&nbsp;&nbsp;Tab&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Color".',') !== false) { echo " checked"; } ?> value="Color" name="inventory[]">&nbsp;&nbsp;Color&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Description".',') !== false) { echo " checked"; } ?> value="Description" name="inventory[]">&nbsp;&nbsp;Description&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Application".',') !== false) { echo " checked"; } ?> value="Application" name="inventory[]">&nbsp;&nbsp;Application
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."GST Exempt".',') !== false) { echo " checked"; } ?> value="GST Exempt" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;GST Exempt

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

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Code".',') !== false) { echo " checked"; } ?> value="Code" name="inventory[]">&nbsp;&nbsp;Code
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."ID #".',') !== false) { echo " checked"; } ?> value="ID #" name="inventory[]">&nbsp;&nbsp;ID #
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Item SKU".',') !== false) { echo " checked"; } ?> value="Item SKU" name="inventory[]">&nbsp;&nbsp;Item SKU
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Part #".',') !== false) { echo " checked"; } ?> value="Part #" name="inventory[]">&nbsp;&nbsp;Part #
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."GTIN".',') !== false) { echo " checked"; } ?> value="GTIN" name="inventory[]">&nbsp;&nbsp;GTIN
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

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Cost".',') !== false) { echo " checked"; } ?> value="Cost" name="inventory[]">&nbsp;&nbsp;Cost
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."CDN Cost Per Unit".',') !== false) { echo " checked"; } ?> value="CDN Cost Per Unit" name="inventory[]">&nbsp;&nbsp;CDN Cost Per Unit
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."USD Cost Per Unit".',') !== false) { echo " checked"; } ?> value="USD Cost Per Unit" name="inventory[]">&nbsp;&nbsp;USD Cost Per Unit
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."COGS".',') !== false) { echo " checked"; } ?> value="COGS" name="inventory[]">&nbsp;&nbsp;COGS GL Code
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Average Cost".',') !== false) { echo " checked"; } ?> value="Average Cost" name="inventory[]">&nbsp;&nbsp;Average Cost
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."USD Invoice".',') !== false) { echo " checked"; } ?> value="USD Invoice" name="inventory[]">&nbsp;&nbsp;USD Invoice

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

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Vendor".',') !== false) { echo " checked"; } ?> value="Vendor" name="inventory[]">&nbsp;&nbsp;Vendor
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Purchase Cost".',') !== false) { echo " checked"; } ?> value="Purchase Cost" name="inventory[]">&nbsp;&nbsp;Purchase Cost
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Date Of Purchase".',') !== false) { echo " checked"; } ?> value="Date Of Purchase" name="inventory[]">&nbsp;&nbsp;Date Of Purchase

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

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Shipping Rate".',') !== false) { echo " checked"; } ?> value="Shipping Rate" name="inventory[]">&nbsp;&nbsp;Shipping Rate
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Shipping Cash".',') !== false) { echo " checked"; } ?> value="Shipping Cash" name="inventory[]">&nbsp;&nbsp;Shipping Cash
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Freight Charge".',') !== false) { echo " checked"; } ?> value="Freight Charge" name="inventory[]">&nbsp;&nbsp;Freight Charge
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Exchange Rate".',') !== false) { echo " checked"; } ?> value="Exchange Rate" name="inventory[]">&nbsp;&nbsp;Exchange Rate
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Exchange $".',') !== false) { echo " checked"; } ?> value="Exchange $" name="inventory[]">&nbsp;&nbsp;Exchange $
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Pallet Num".',') !== false) { echo " checked"; } ?> value="Pallet Num" name="inventory[]">&nbsp;&nbsp;Pallet #
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Purchase Order".',') !== false) { echo " checked"; } ?> value="Purchase Order" name="inventory[]">&nbsp;&nbsp;Purchase Order
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."PO Line".',') !== false) { echo " checked"; } ?> value="PO Line" name="inventory[]">&nbsp;&nbsp;PO Line Item

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
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Sell Price".',') !== false) { echo " checked"; } ?> value="Sell Price" name="inventory[]">&nbsp;&nbsp;Sell Price
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Final Retail Price".',') !== false) { echo " checked"; } ?> value="Final Retail Price" name="inventory[]">&nbsp;&nbsp;Final Retail Price
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Wholesale Price".',') !== false) { echo " checked"; } ?> value="Wholesale Price" name="inventory[]">&nbsp;&nbsp;Wholesale Price
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Commercial Price".',') !== false) { echo " checked"; } ?> value="Commercial Price" name="inventory[]">&nbsp;&nbsp;Commercial Price
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Client Price".',') !== false) { echo " checked"; } ?> value="Client Price" name="inventory[]">&nbsp;&nbsp;Client Price
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Preferred Price".',') !== false) { echo " checked"; } ?> value="Preferred Price" name="inventory[]">&nbsp;&nbsp;Preferred Price
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Admin Price".',') !== false) { echo " checked"; } ?> value="Admin Price" name="inventory[]">&nbsp;&nbsp;Admin Price
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Web Price".',') !== false) { echo " checked"; } ?> value="Web Price" name="inventory[]">&nbsp;&nbsp;Web Price
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Clearance Price".',') !== false) { echo " checked"; } ?> value="Clearance Price" name="inventory[]">&nbsp;&nbsp;Clearance Price
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Commission Price".',') !== false) { echo " checked"; } ?> value="Commission Price" name="inventory[]">&nbsp;&nbsp;Commission Price
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."MSRP".',') !== false) { echo " checked"; } ?> value="MSRP" name="inventory[]">&nbsp;&nbsp;MSRP
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Unit Price".',') !== false) { echo " checked"; } ?> value="Unit Price" name="inventory[]">&nbsp;&nbsp;Unit Price
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Unit Cost".',') !== false) { echo " checked"; } ?> value="Unit Cost" name="inventory[]">&nbsp;&nbsp;Unit Cost
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Purchase Order Price".',') !== false) { echo " checked"; } ?> value="Purchase Order Price" name="inventory[]">&nbsp;&nbsp;Purchase Order Price
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Sales Order Price".',') !== false) { echo " checked"; } ?> value="Sales Order Price" name="inventory[]">&nbsp;&nbsp;<?= SALES_ORDER_NOUN ?> Price
                    <input type="checkbox" <?php if (strpos($value_config, ','."Drum Unit Cost".',') !== false) { echo " checked"; } ?> value="Drum Unit Cost" name="inventory[]">&nbsp;&nbsp;Drum Unit Cost
                    <input type="checkbox" <?php if (strpos($value_config, ','."Drum Unit Price".',') !== false) { echo " checked"; } ?> value="Drum Unit Price" name="inventory[]">&nbsp;&nbsp;Drum Unit Price
                    <input type="checkbox" <?php if (strpos($value_config, ','."Tote Unit Cost".',') !== false) { echo " checked"; } ?> value="Tote Unit Cost" name="inventory[]">&nbsp;&nbsp;Tote Unit Cost
                    <input type="checkbox" <?php if (strpos($value_config, ','."Tote Unit Price".',') !== false) { echo " checked"; } ?> value="Tote Unit Price" name="inventory[]">&nbsp;&nbsp;Tote Unit Price
                    <input type="checkbox" <?php if (strpos($value_config, ','."WCB Price".',') !== false) { echo " checked"; } ?> value="WCB Price" name="inventory[]">&nbsp;&nbsp;WCB Price
                    <input type="checkbox" <?php if (strpos($value_config, ','."Suggested Retail Price".',') !== false) { echo " checked"; } ?> value="Suggested Retail Price" name="inventory[]">&nbsp;&nbsp;Suggested Retail Price
                    <input type="checkbox" <?php if (strpos($value_config, ','."Rush Price".',') !== false) { echo " checked"; } ?> value="Rush Price" name="inventory[]">&nbsp;&nbsp;Rush Price
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Distributor Price".',') !== false) { echo " checked"; } ?> value="Distributor Price" name="inventory[]">&nbsp;&nbsp;Distributor Price
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

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Markup By $".',') !== false) { echo " checked"; } ?> value="Markup By $" name="inventory[]">&nbsp;&nbsp;Markup By $
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Markup By %".',') !== false) { echo " checked"; } ?> value="Markup By %" name="inventory[]">&nbsp;&nbsp;Markup By %

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

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Current Stock".',') !== false) { echo " checked"; } ?> value="Current Stock" name="inventory[]">&nbsp;&nbsp;Current Stock
                    <!-- Taken out to remove confusion between quantity and current inventory <input type="checkbox" <?php // if (strpos($inventory_config, ','."Current Inventory".',') !== false) { echo " checked"; } ?> value="Current Inventory" name="inventory[]">&nbsp;&nbsp;Current Inventory-->
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Quantity".',') !== false) { echo " checked"; } ?> value="Quantity" name="inventory[]">&nbsp;&nbsp;Quantity
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Expected".',') !== false) { echo " checked"; } ?> value="Expected" name="inventory[]">&nbsp;&nbsp;Expected
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Received".',') !== false) { echo " checked"; } ?> value="Received" name="inventory[]">&nbsp;&nbsp;Received
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Variance".',') !== false) { echo " checked"; } ?> value="Variance" name="inventory[]">&nbsp;&nbsp;GL Code
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Write-offs".',') !== false) { echo " checked"; } ?> value="Write-offs" name="inventory[]">&nbsp;&nbsp;Write-offs

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Buying Units".',') !== false) { echo " checked"; } ?> value="Buying Units" name="inventory[]">&nbsp;&nbsp;Buying Units
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Selling Units".',') !== false) { echo " checked"; } ?> value="Selling Units" name="inventory[]">&nbsp;&nbsp;Selling Units
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Stocking Units".',') !== false) { echo " checked"; } ?> value="Stocking Units" name="inventory[]">&nbsp;&nbsp;Stocking Units
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

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Warehouse".',') !== false) { echo " checked"; } ?> value="Warehouse" name="inventory[]">&nbsp;&nbsp;Warehouse
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Location".',') !== false) { echo " checked"; } ?> value="Location" name="inventory[]">&nbsp;&nbsp;Location
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."LSD".',') !== false) { echo " checked"; } ?> value="LSD" name="inventory[]">&nbsp;&nbsp;LSD

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

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Size".',') !== false) { echo " checked"; } ?> value="Size" name="inventory[]">&nbsp;&nbsp;Size&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Weight".',') !== false) { echo " checked"; } ?> value="Weight" name="inventory[]">&nbsp;&nbsp;Weight&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Length".',') !== false) { echo " checked"; } ?> value="Length" name="inventory[]">&nbsp;&nbsp;Length&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Gauge".',') !== false) { echo " checked"; } ?> value="Gauge" name="inventory[]">&nbsp;&nbsp;Gauge&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Pressure".',') !== false) { echo " checked"; } ?> value="Pressure" name="inventory[]">&nbsp;&nbsp;Pressure

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

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Min Max".',') !== false) { echo " checked"; } ?> value="Min Max" name="inventory[]">&nbsp;&nbsp;Min Max
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Min Bin".',') !== false) { echo " checked"; } ?> value="Min Bin" name="inventory[]">&nbsp;&nbsp;Min Bin

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

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Estimated Hours".',') !== false) { echo " checked"; } ?> value="Estimated Hours" name="inventory[]">&nbsp;&nbsp;Estimated Hours
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Actual Hours".',') !== false) { echo " checked"; } ?> value="Actual Hours" name="inventory[]">&nbsp;&nbsp;Actual Hours

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

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Minimum Billable".',') !== false) { echo " checked"; } ?> value="Minimum Billable" name="inventory[]">&nbsp;&nbsp;Minimum Billable
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."GL Revenue".',') !== false) { echo " checked"; } ?> value="GL Revenue" name="inventory[]">&nbsp;&nbsp;GL Revenue

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."GL Assets".',') !== false) { echo " checked"; } ?> value="GL Assets" name="inventory[]">&nbsp;&nbsp;GL Assets

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

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Quote Description".',') !== false) { echo " checked"; } ?> value="Quote Description" name="inventory[]">&nbsp;&nbsp;Quote Description

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

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Status".',') !== false) { echo " checked"; } ?> value="Status" name="inventory[]">&nbsp;&nbsp;Status

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
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Display On Website".',') !== false) { echo " checked"; } ?> value="Display On Website" name="inventory[]">&nbsp;&nbsp;Display On Website
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Featured On Website".',') !== false) { echo " checked"; } ?> value="Featured On Website" name="inventory[]">&nbsp;&nbsp;Featured On Website
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."New Item".',') !== false) { echo " checked"; } ?> value="New Item" name="inventory[]">&nbsp;&nbsp;Display Item As New On Website
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Item On Sale".',') !== false) { echo " checked"; } ?> value="Item On Sale" name="inventory[]">&nbsp;&nbsp;Display Item As On Sale On Website
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Item On Clearance".',') !== false) { echo " checked"; } ?> value="Item On Clearance" name="inventory[]">&nbsp;&nbsp;Display Item As Clearance On Website
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
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Main Image".',') !== false) { echo " checked"; } ?> value="Main Image" name="inventory[]">&nbsp;&nbsp;Main Image
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Additional Images".',') !== false) { echo " checked"; } ?> value="Additional Images" name="inventory[]">&nbsp;&nbsp;Additional Images
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Spec Sheet".',') !== false) { echo " checked"; } ?> value="Spec Sheet" name="inventory[]">&nbsp;&nbsp;Spec Sheet
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Notes".',') !== false) { echo " checked"; } ?> value="Notes" name="inventory[]">&nbsp;&nbsp;Notes
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Comments".',') !== false) { echo " checked"; } ?> value="Comments" name="inventory[]">&nbsp;&nbsp;Comments
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

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Rent Price".',') !== false) { echo " checked"; } ?> value="Rent Price" name="inventory[]">&nbsp;&nbsp;Rent Price
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Rental Days".',') !== false) { echo " checked"; } ?> value="Rental Days" name="inventory[]">&nbsp;&nbsp;Rental Days
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Rental Weeks".',') !== false) { echo " checked"; } ?> value="Rental Weeks" name="inventory[]">&nbsp;&nbsp;Rental Weeks
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Rental Months".',') !== false) { echo " checked"; } ?> value="Rental Months" name="inventory[]">&nbsp;&nbsp;Rental Months
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Rental Years".',') !== false) { echo " checked"; } ?> value="Rental Years" name="inventory[]">&nbsp;&nbsp;Rental Years
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Reminder/Alert".',') !== false) { echo " checked"; } ?> value="Reminder/Alert" name="inventory[]">&nbsp;&nbsp;Reminder/Alert

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

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Daily".',') !== false) { echo " checked"; } ?> value="Daily" name="inventory[]">&nbsp;&nbsp;Daily
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Weekly".',') !== false) { echo " checked"; } ?> value="Weekly" name="inventory[]">&nbsp;&nbsp;Weekly
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Monthly".',') !== false) { echo " checked"; } ?> value="Monthly" name="inventory[]">&nbsp;&nbsp;Monthly
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Annually".',') !== false) { echo " checked"; } ?> value="Annually" name="inventory[]">&nbsp;&nbsp;Annually
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."#Of Days".',') !== false) { echo " checked"; } ?> value="#Of Days" name="inventory[]">&nbsp;&nbsp;#Of Days
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."#Of Hours".',') !== false) { echo " checked"; } ?> value="#Of Hours" name="inventory[]">&nbsp;&nbsp;#Of Hours

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

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."#Of Kilometers".',') !== false) { echo " checked"; } ?> value="#Of Kilometers" name="inventory[]">&nbsp;&nbsp;#Of Kilometers
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."#Of Miles".',') !== false) { echo " checked"; } ?> value="#Of Miles" name="inventory[]">&nbsp;&nbsp;#Of Miles

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_bom" >
                        Bill of Material<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_bom" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Bill of Material".',') !== false) { echo " checked"; } ?> value="Bill of Material" name="inventory[]">&nbsp;&nbsp;Bill of Material

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_supplimentary" >
                        Supplimentary Products<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_supplimentary" class="panel-collapse collapse">
                <div class="panel-body">

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Supplimentary Products".',') !== false) { echo " checked"; } ?> value="Supplimentary Products" name="inventory[]">&nbsp;&nbsp;Supplimentary Products

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
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Include in P.O.S.".',') !== false) {
                        echo " checked"; } ?> value="Include in P.O.S." name="inventory[]">&nbsp;&nbsp;Include in Point of Sale
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Include in Purchase Orders".',') !== false) {
                        echo " checked"; } ?> value="Include in Purchase Orders" name="inventory[]">&nbsp;&nbsp;Include in Purchase Orders
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Include in Sales Orders".',') !== false) {
                        echo " checked"; } ?> value="Include in Sales Orders" name="inventory[]">&nbsp;&nbsp;Include in <?= SALES_ORDER_TILE ?>
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Include in Product".',') !== false) {
                        echo " checked"; } ?> value="Include in Product" name="inventory[]">&nbsp;&nbsp;Include in Product
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

                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Min Amount".',') !== false) { echo " checked"; } ?> value="Min Amount" name="inventory[]">&nbsp;&nbsp;Min Amount
                    <input type="checkbox" <?php if (strpos($inventory_config, ','."Max Amount".',') !== false) { echo " checked"; } ?> value="Max Amount" name="inventory[]">&nbsp;&nbsp;Max Amount

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_history" >
                        Inventory History<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_history" class="panel-collapse collapse">
                <div class="panel-body">
                    <input type="checkbox" <?php echo (strpos($inventory_config, ','."Change Log".',') !== false ? " checked" : ''); ?> value="Change Log" name="inventory[]">&nbsp;&nbsp;Inventory Full Change Log
                    <input type="checkbox" <?php echo (strpos($inventory_config, ','."Change Cost".',') !== false ? " checked" : ''); ?> value="Change Cost" name="inventory[]">&nbsp;&nbsp;Inventory Cost Change Log
                    <input type="checkbox" <?php echo (strpos($inventory_config, ','."Change Qty".',') !== false ? " checked" : ''); ?> value="Change Qty" name="inventory[]">&nbsp;&nbsp;Inventory Quantity Change Log
                    <input type="checkbox" <?php echo (strpos($inventory_config, ','."Change Comment".',') !== false ? " checked" : ''); ?> value="Change Comment" name="inventory[]">&nbsp;&nbsp;Inventory Change Details
                </div>
            </div>
        </div>
    </div>

</div>