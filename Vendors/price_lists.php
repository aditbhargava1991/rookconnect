<form name="form_sites" method="post" action="" class="form-inline" role="form"><?php
    if ( isset($_GET['order_list'])) {
        if ($inventoryidorder !== '') {
            if($currentlist == 'on') {
                $active = 'active_tab';
            } else {
                $active = '';
            }
            echo "<a href='add_contacts.php?currentlist&category=Top".$order_list."'><button type='button' class='btn brand-btn mobile-block ".$active."' >Order List Items</button></a>&nbsp;&nbsp;";
        }
    }

    echo "<div class='mobile-100-container double-gap-bottom'>";

        $category = $_GET['category'];
        $active_all = '';
        $active_bom = '';
        if(empty($_GET['category']) || $_GET['category'] == 'Top' && $currentlist != 'on') {
            $active_all = 'active_tab';
        }

        echo "<div class='pull-left'><a href='add_contacts.php?category=Top".$order_list."'><button type='button' class='btn brand-btn mobile-block mobile-100 ".$active_all."'>Last 25 Added</button></a></div>"; ?>

        <div class="form-group mobile-100 cate_fitter pull-left gap-left" style="width:280px;">
            <select name="search_category" class="chosen-select-deselect form-control category_actual" onchange="location=this.value;">
                <option value="">Select a Category</option><?php
                $sql = mysqli_query($dbc, "SELECT * FROM `vendor_price_list` WHERE `deleted`=0 GROUP BY `category` ORDER BY IF(`category` RLIKE '^[a-z]', 1, 2), `category`, IF(`name` RLIKE '^[a-z]', 1, 2), `name`");
                while($row = mysqli_fetch_assoc($sql)){
                    $active_daily = '';
                    $cat_tab = $row['category'];
                    if((!empty($_GET['category'])) && ($_GET['category'] == $cat_tab) && (!isset($_GET['currentlist']))) {
                        $active_daily = 'selected';
                    }
                    echo '<option value="add_contacts.php?category='.$cat_tab.$order_list.'&contactid='.$contactid.'&subtab=Price Lists" '.$active_daily.'>'.$cat_tab.'</option>';
                }
              ?>
            </select>
        </div>

        <div class="clearfix"></div><?php

        if($_GET['category'] == 'bom') {
            $active_bom = ' active_tab';
        }
    echo '</div>'; ?>

    <center>
        <div class="form-group">
            <label for="site_name" class="col-sm-5 control-label">Search By Any:</label>
            <div class="col-sm-6">
                <?php if(isset($_POST['search_inventory_submit'])) { ?>
                    <input  type="text" name="search_inventory" value="<?php echo $_POST['search_inventory']?>" class="form-control">
                <?php } else { ?>
                    <input type="text" name="search_inventory" class="form-control">
                <?php } ?>
            </div>
        </div>
        <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Click this once you have typed in the Search By Any field."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
        <button type="submit" name="search_inventory_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
        <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="This refreshes the page to view all Vendor Price List."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
        <button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
    </center><?php

    $impexp_or_not ='';
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(`configid`) AS `configid` FROM `general_configuration` WHERE name='show_impexp_vpl'"));
    if($get_config['configid'] > 0) {
        $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `value` FROM `general_configuration` WHERE `name`='show_impexp_vpl'"));
        if($get_config['value'] == '1') {
            $impexp_or_not = 'true';
        }
    }

    if(vuaed_visible_function($dbc, 'inventory') == 1) {
        if($_GET['category'] != 'Top') {
            echo '<a href="add_contacts.php?category='.$category.$order_list.'" class="double-gap-top btn brand-btn mobile-block gap-bottom pull-right">Add Product</a>';
        }
        ?>
        <span class="popover-examples double-gap-top pull-right pad-5"><a data-toggle="tooltip" data-placement="top" title="You must be in a category before you can add a product."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php

        if($impexp_or_not == 'true') {
            echo '<a href="add_inventory_multiple.php?category='.$category.'" class="double-gap-top btn brand-btn mobile-block gap-bottom pull-right">Import/Export</a>'; ?>
            <span class="popover-examples double-gap-top pull-right pad-5"><a data-toggle="tooltip" data-placement="top" title="Click here to Import or Export a product(s)."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
        }
    }

    if (isset($_GET['order_list'])) {  ?>
        <div class="col-lg-1 col-md-3 col-sm-12 col-xs-12 pull-sm-right pull-xs-right" style="padding-right:12px;">
            <div class='selectall selectbutton sel2' title='This will select all PDFs on the current page.' style='cursor:pointer;text-decoration:underline;'>Select All</div>
        </div><?php
    } ?>

    <div id="no-more-tables"><?php
        // Display Pager
        $inventory = '';
        if (isset($_POST['search_inventory_submit'])) {
            $inventory = $_POST['search_inventory'];
            if (isset($_POST['search_inventory'])) {
                $inventory = $_POST['search_inventory'];
            }
        }
        if (isset($_POST['display_all_inventory'])) {
            $inventory = '';
        }
        $rowsPerPage = ITEMS_PER_PAGE;
        $pageNum = 1;

        if(isset($_GET['page'])) {
            $pageNum = $_GET['page'];
        }

        $offset = ($pageNum - 1) * $rowsPerPage;

        /* Pagination Counting */
        $rowsPerPage = 25;
        $pageNum = 1;

        if(isset($_GET['page'])) {
            $pageNum = $_GET['page'];
        }

        $offset = ($pageNum - 1) * $rowsPerPage;

        if ($inventory != '') {
            $query_check_credentials = "SELECT * FROM `vendor_price_list` WHERE `vendorid`='$contactid' AND `deleted`=0 AND (`name` LIKE '%" . $inventory . "%' OR `code` LIKE '%" . $inventory . "%' OR `part_no` LIKE '%" . $inventory . "%' OR `category`='$inventory' OR `sub_category` LIKE '%" . $inventory . "%' OR `description` LIKE '%" . $inventory . "%' OR `purchase_cost` LIKE '%" . $inventory . "%' OR `min_bin` LIKE '%" . $inventory . "%' OR `date_of_purchase` LIKE '%" . $inventory . "%') LIMIT $offset, $rowsPerPage";
            $query = "SELECT COUNT(*) as `numrows` FROM `vendor_price_list` WHERE `vendorid`='$contactid' AND `deleted`=0 AND (`name` LIKE '%" . $inventory . "%' OR `code` LIKE '%" . $inventory . "%' OR `part_no` LIKE '%" . $inventory . "%' OR `category`='$inventory' OR `sub_category` LIKE '%" . $inventory . "%' OR `description` LIKE '%" . $inventory . "%' OR `purchase_cost` LIKE '%" . $inventory . "%' OR `min_bin` LIKE '%" . $inventory . "%' OR `date_of_purchase` LIKE '%" . $inventory . "%')";
        } else {
            /*
            if(isset($_GET['filter'])) { $url_search = $_GET['filter']; } else { $url_search = ''; }
            if($url_search == 'Top') {
                $query_check_credentials = "SELECT * FROM vendor_price_list WHERE deleted = 0 ORDER BY inventoryid DESC LIMIT 10";
            } else if($url_search == 'All') {
                $query_check_credentials = "SELECT * FROM vendor_price_list WHERE deleted = 0 ORDER BY part_no";
            } else {
                $query_check_credentials = "SELECT * FROM vendor_price_list WHERE deleted = 0 AND part_no LIKE '" . $url_search . "%' ORDER BY part_no";
            }
            */

            //$query_check_credentials = "SELECT * FROM inventory WHERE deleted=0 LIMIT $offset, $rowsPerPage";
            if (isset($_GET['order_list']) ) {
                if ($currentlist == 'on' && $inventoryidorder !== '') {
                    $query_check_credentials = "SELECT * FROM `vendor_price_list` WHERE `vendorid`='$contactid' AND `inventoryid` IN (" . $inventoryidorder . ") LIMIT $offset, $rowsPerPage";
                    $query = "SELECT COUNT(*) AS `numrows` FROM `vendor_price_list` WHERE `vendorid`='$contactid' AND `inventoryid` IN (" . $inventoryidorder . ")";
                } else if((empty($_GET['category'])) || ($_GET['category'] == 'Top')) {
                    $query_check_credentials = "SELECT COUNT(*) AS `numrows` FROM `vendor_price_list` WHERE `vendorid`='$contactid' AND `deleted`=0 ORDER BY `inventoryid` DESC LIMIT 25";
                } else {
                    $category = $_GET['category'];
                    $query_check_credentials = "SELECT * FROM `vendor_price_list` WHERE `vendorid`='$contactid' AND `deleted`=0 AND `category`='$category' LIMIT $offset, $rowsPerPage";
                    $query = "SELECT COUNT(*) AS `numrows` FROM `vendor_price_list` WHERE `vendorid`='$contactid' AND `deleted`=0 AND `category`='$category'";
                }
            } else if((empty($_GET['category'])) || ($_GET['category'] == 'Top')) {
                $query_check_credentials = "SELECT * FROM `vendor_price_list` WHERE `vendorid`='$contactid' AND `deleted`=0 ORDER BY `inventoryid` DESC LIMIT $offset, $rowsPerPage";
                $query = "SELECT COUNT(*) AS `numrows` FROM `vendor_price_list` WHERE `vendorid`='$contactid' AND `deleted`=0";
            } else if((!empty($_GET['category'])) && ($_GET['category'] != 'Vendor')) {
                $url_cat = trim($_GET['category']);
                $query_check_credentials = "SELECT * FROM `vendor_price_list` WHERE `vendorid`='$contactid' AND `deleted`=0 AND `category`='$url_cat' ORDER BY `inventoryid` DESC LIMIT $offset, $rowsPerPage";
                $query = "SELECT COUNT(*) AS `numrows` FROM `vendor_price_list` WHERE `vendorid`='$contactid' AND `deleted`=0 AND `category`='$url_cat'";
            } else {
                $category = $_GET['category'];
                $query_check_credentials = "SELECT * FROM `vendor_price_list` WHERE `vendorid`='$contactid' AND `deleted`=0 LIMIT $offset, $rowsPerPage";
                $query = "SELECT COUNT(*) AS `numrows` FROM `vendor_price_list` WHERE `deleted`=0";
            }
        }

        $result = mysqli_query($dbc, $query_check_credentials);

        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {

            $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `dashboard` FROM `field_config_vendors` WHERE `subtab`='Price Lists' AND `fields` IS NOT NULL"));
            $value_config = ','.$get_field_config['dashboard'].',';

            /*
            if(empty($_GET['category']) || $_GET['category'] == 'Top') {
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `fields` FROM `field_config_vendors` WHERE `subtab`='Price Lists' AND `fields` IS NOT NULL"));
                $value_config = ','.$get_field_config['fields'].',';
            } else {
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `fields` FROM `field_config_vendors` WHERE `tab`='$category' AND `accordion` IS NULL"));
                $value_config = ','.$get_field_config['fields'].',';
            }
            */

            // Added Pagination //
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);

            echo "<table class='table table-bordered'>";
            echo "<tr class='hidden-xs hidden-sm'>";
                if (isset($_GET['order_list'])) {
                    echo '<th>Include in Order List';
                    echo "<div class='selectall selectbutton' title='This will select all PDFs on the current page.' style='text-decoration:underline;cursor:pointer;'>Select All</div>";
                    echo '</th>';
                }
                if (strpos($value_config, ',Part #,') !== false) {
                    echo '<th>Part #</th>';
                }
                if (strpos($value_config, ',ID #,') !== false) {
                    echo '<th>ID #</th>';
                }
                if (strpos($value_config, ',Code,') !== false) {
                    echo '<th>Code</th>';
                }
                if (strpos($value_config, ',Description,') !== false) {
                    echo '<th>Description</th>';
                }
                if (strpos($value_config, ',Category,') !== false) {
                    echo '<th>Category</th>';
                }
                if (strpos($value_config, ',Subcategory,') !== false) {
                    echo '<th>Subcategory</th>';
                }
                if (strpos($value_config, ',Name,') !== false) {
                    echo '<th>Name</th>';
                }
                if (strpos($value_config, ',Product Name,') !== false) {
                    echo '<th>Product Name</th>';
                }
                if (strpos($value_config, ',Type,') !== false) {
                    echo '<th>Type</th>';
                }
                if (strpos($value_config, ',Cost,') !== false) {
                    echo '<th>Cost</th>';
                }
                if (strpos($value_config, ',CDN Cost Per Unit,') !== false) {
                    echo '<th>CDN Cost Per Unit</th>';
                }
                if (strpos($value_config, ',USD Cost Per Unit,') !== false) {
                    echo '<th>USD Cost Per Unit</th>';
                }
                if (strpos($value_config, ',COGS,') !== false) {
                    echo '<th>COGS GL Code</th>';
                }
                if (strpos($value_config, ',Average Cost,') !== false) {
                    echo '<th>Average Cost</th>';
                }
                if (strpos($value_config, ',USD Invoice,') !== false) {
                    echo '<th>USD Invoice</th>';
                }
                if (strpos($value_config, ',Vendor,') !== false) {
                    echo '<th>'.VENDOR_TILE.'</th>';
                }
                if (strpos($value_config, ',Purchase Cost,') !== false) {
                    echo '<th>Purchase Cost</th>';
                }
                if (strpos($value_config, ',Date Of Purchase,') !== false) {
                    echo '<th>Date Of Purchase</th>';
                }
                if (strpos($value_config, ',Shipping Rate,') !== false) {
                    echo '<th>Shipping Rate</th>';
                }
                if (strpos($value_config, ',Shipping Cash,') !== false) {
                    echo '<th>Shipping Cash</th>';
                }
                if (strpos($value_config, ',Freight Charge,') !== false) {
                    echo '<th>Freight Charge</th>';
                }
                if (strpos($value_config, ',Exchange Rate,') !== false) {
                    echo '<th>Exchange Rate</th>';
                }
                if (strpos($value_config, ',Exchange $,') !== false) {
                    echo '<th>Exchange $</th>';
                }
                if (strpos($value_config, ',Sell Price,') !== false) {
                    echo '<th>Sell Price</th>';
                }
                if (strpos($value_config, ',Final Retail Price,') !== false) {
                    echo '<th>Final Retail Price</th>';
                }
                if (strpos($value_config, ',Wholesale Price,') !== false) {
                    echo '<th>Wholesale Price</th>';
                }
                if (strpos($value_config, ',Commercial Price,') !== false) {
                    echo '<th>Commercial Price</th>';
                }
                if (strpos($value_config, ',Client Price,') !== false) {
                    echo '<th>Client Price</th>';
                }
                if (strpos($value_config, ',Purchase Order Price,') !== false) {
                    echo '<th>Purchase Order Price</th>';
                }
                if (strpos($value_config, ',Sales Order Price,') !== false) {
                    echo '<th>'.SALES_ORDER_NOUN.' Price</th>';
                }
                if (strpos($value_config, ',Include in Sales Orders,') !== false) {
                    echo '<th>Include in '.SALES_ORDER_TILE.'</th>';
                }
                if (strpos($value_config, ',Include in P.O.S.,') !== false) {
                    echo '<th>Include in '.POS_ADVANCE_TILE.'</th>';
                }
                if (strpos($value_config, ',Include in Purchase Orders,') !== false) {
                    echo '<th>Include in Purchase Orders</th>';
                }
                if (strpos($value_config, ',Preferred Price,') !== false) {
                    echo '<th>Preferred Price</th>';
                }
                if (strpos($value_config, ',Admin Price,') !== false) {
                    echo '<th>Admin Price</th>';
                }
                if (strpos($value_config, ',Web Price,') !== false) {
                    echo '<th>Web Price</th>';
                }
                if (strpos($value_config, ',Commission Price,') !== false) {
                    echo '<th>Commission Price</th>';
                }
                if (strpos($value_config, ',MSRP,') !== false) {
                    echo '<th>MSRP</th>';
                }
                if (strpos($value_config, ',Unit Price,') !== false) {
                    echo '<th>Unit Price</th>';
                }
                if (strpos($value_config, ',Unit Cost,') !== false) {
                    echo '<th>Unit Cost</th>';
                }
                if (strpos($value_config, ',Rent Price,') !== false) {
                    echo '<th>Rent Price</th>';
                }
                if (strpos($value_config, ',Rental Days,') !== false) {
                    echo '<th>Rental Days</th>';
                }
                if (strpos($value_config, ',Rental Weeks,') !== false) {
                    echo '<th>Rental Weeks</th>';
                }
                if (strpos($value_config, ',Rental Months,') !== false) {
                    echo '<th>Rental Months</th>';
                }
                if (strpos($value_config, ',Rental Years,') !== false) {
                    echo '<th>Rental Years</th>';
                }
                if (strpos($value_config, ',Reminder/Alert,') !== false) {
                    echo '<th>Reminder/Alert</th>';
                }
                if (strpos($value_config, ',Daily,') !== false) {
                    echo '<th>Daily</th>';
                }
                if (strpos($value_config, ',Weekly,') !== false) {
                    echo '<th>Weekly</th>';
                }
                if (strpos($value_config, ',Monthly,') !== false) {
                    echo '<th>Monthly</th>';
                }
                if (strpos($value_config, ',Annually,') !== false) {
                    echo '<th>Annually</th>';
                }
                if (strpos($value_config, ',#Of Days,') !== false) {
                    echo '<th>#Of Days</th>';
                }
                if (strpos($value_config, ',#Of Hours,') !== false) {
                    echo '<th>#Of Hours</th>';
                }
                if (strpos($value_config, ',#Of Kilometers,') !== false) {
                    echo '<th>#Of Kilometers</th>';
                }
                if (strpos($value_config, ',#Of Miles,') !== false) {
                    echo '<th>#Of Miles</th>';
                }
                if (strpos($value_config, ',Markup By $,') !== false) {
                    echo '<th>Markup By $</th>';
                }
                if (strpos($value_config, ',Markup By %,') !== false) {
                    echo '<th>Markup By %</th>';
                }
                if (strpos($value_config, ',GL Revenue,') !== false) {
                    echo '<th>GL Revenue</th>';
                }
                if (strpos($value_config, ',GL Assets,') !== false) {
                    echo '<th>GL Assets</th>';
                }
                if (strpos($value_config, ',Current Stock,') !== false) {
                    echo '<th>Current Stock</th>';
                }
                if (strpos($value_config, ',Current Inventory,') !== false) {
                    echo '<th>Current Inventory</th>';
                }
                if (strpos($value_config, ',Quantity,') !== false) {
                    echo '<th>Quantity</th>';
                }
                if (strpos($value_config, ',Variance,') !== false) {
                    echo '<th>GL Code</th>';
                }
                if (strpos($value_config, ',Write-offs,') !== false) {
                    echo '<th>Write-offs</th>';
                }
                if (strpos($value_config, ',Buying Units,') !== false) {
                    echo '<th>Buying Units</th>';
                }
                if (strpos($value_config, ',Selling Units,') !== false) {
                    echo '<th>Selling Units</th>';
                }
                if (strpos($value_config, ',Stocking Units,') !== false) {
                    echo '<th>Stocking Units</th>';
                }
                if (strpos($value_config, ',Location,') !== false) {
                    echo '<th>Location</th>';
                }
                if (strpos($value_config, ',LSD,') !== false) {
                    echo '<th>LSD</th>';
                }
                if (strpos($value_config, ',Size,') !== false) {
                    echo '<th>Size</th>';
                }
                if (strpos($value_config, ',Weight,') !== false) {
                    echo '<th>Weight</th>';
                }
                if (strpos($value_config, ',Min Max,') !== false) {
                    echo '<th>Min Max</th>';
                }
                if (strpos($value_config, ',Min Bin,') !== false) {
                    echo '<th>Min Bin</th>';
                }
                if (strpos($value_config, ',Estimated Hours,') !== false) {
                    echo '<th>Estimated Hours</th>';
                }
                if (strpos($value_config, ',Actual Hours,') !== false) {
                    echo '<th>Actual Hours</th>';
                }
                if (strpos($value_config, ',Minimum Billable,') !== false) {
                    echo '<th>Minimum Billable</th>';
                }
                if (strpos($value_config, ',Quote Description,') !== false) {
                    echo '<th>Quote Description</th>';
                }
                if (strpos($value_config, ',Notes,') !== false) {
                    echo '<th>Notes</th>';
                }
                if (strpos($value_config, ',Comments,') !== false) {
                    echo '<th>Comments</th>';
                }
                echo '<th>Function</th>';
                echo "</tr>";
        } else{
            echo "<h2>No Record Found.</h2>";
        }
        while($row = mysqli_fetch_array( $result ))
        {
            $color = '';
            if($row['status'] == 'In inventory') {
                $color = 'style="color: white;"';
            }
            if($row['status'] == 'In transit from vendor') {
                $color = 'style="color: red;"';
            }
            if($row['status'] == 'In transit between yards') {
                $color = 'style="color: blue;"';
            }
            if($row['status'] == 'Not confirmed in yard by inventory check') {
                $color = 'style="color: yellow;"';
            }
            if($row['status'] == 'Assigned to job') {
                $color = 'style="color: green;"';
            }
            if($row['status'] == 'In transit and assigned') {
                $color = 'style="color: purple;"';
            }

            echo '<tr '.$color.'>';
            if (isset($_GET['order_list'])) {
                echo '<td data-title="Include in Order List">';
                $HiddenProducts = explode(',',$inventoryidorder);
                if (in_array($row['inventoryid'], $HiddenProducts)) {
                  $checked = 'Checked';
                } else {
                  $checked = '';
                }
                ?><input type='checkbox' style='width:20px; height:20px;' <?php echo $checked; ?> id='<?PHP echo $row['inventoryid']; ?>'  name='' class='order_list_includer' value='<?PHP echo $_GET['order_list']; ?>'><br>
                <?php
                echo '</td>';
            }
            if (strpos($value_config, ',Part #,') !== false) {
                echo '<td data-title="Part #">' . $row['part_no'] . '</td>';
            }
            if (strpos($value_config, ',ID #,') !== false) {
                echo '<td data-title="ID #">' . $row['id_number'] . '</td>';
            }
            if (strpos($value_config, ',Code,') !== false) {
                echo '<td data-title="Code">' . $row['code'] . '</td>';
            }
            if (strpos($value_config, ',Description,') !== false) {
                echo '<td data-title="Desc.">' . $row['part_no'] . '</td>';
            }
            if (strpos($value_config, ',Category,') !== false) {
                echo '<td data-title="Category">' . $row['category'] . '</td>';
            }
            if (strpos($value_config, ',Subcategory,') !== false) {
                echo '<td data-title="Sub Category">' . $row['sub_category'] . '</td>';
            }
            if (strpos($value_config, ',Name,') !== false) {
                echo '<td data-title="Name">' . $row['name'] . '</td>';
            }
            if (strpos($value_config, ',Product Name,') !== false) {
                echo '<td data-title="Prod. Name">' . $row['product_name'] . '</td>';
            }
            if (strpos($value_config, ',Type,') !== false) {
                echo '<td data-title="Type">' . $row['type'] . '</td>';
            }
            if (strpos($value_config, ',Cost,') !== false) {
                echo '<td data-title="Cost">' . $row['cost'] . '</td>';
            }
            if (strpos($value_config, ',CDN Cost Per Unit,') !== false) {
               echo '<td data-title="CAD/Unit">' . $row['cdn_cpu'] . '</td>';
            }
            if (strpos($value_config, ',USD Cost Per Unit,') !== false) {
                echo '<td data-title="USD/Unit">' . $row['usd_cpu'] . '</td>';
            }
            if (strpos($value_config, ',COGS,') !== false) {
                echo '<td data-title="COGS">' . $row['cogs_total'] . '</td>';
            }
            if (strpos($value_config, ',Average Cost,') !== false) {
                echo '<td data-title="Avg. Cost">' . $row['average_cost'] . '</td>';
            }
            if (strpos($value_config, ',USD Invoice,') !== false) {
                echo '<td data-title="USD Invoice">' . $row['usd_invoice'] . '</td>';
            }
            if (strpos($value_config, ',Vendor,') !== false) {
                $vendorid = $row['vendorid'];
                $get_vendor = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT  name FROM contacts WHERE contactid='$vendorid'"));
                echo '<td data-title="Vendor">' . decryptIt($get_vendor['name']) . '</td>';
            }
            if (strpos($value_config, ',Purchase Cost,') !== false) {
                echo '<td data-title="Purchase Cost">' . $row['purchase_cost'] . '</td>';
            }
            if (strpos($value_config, ',Date Of Purchase,') !== false) {
                echo '<td data-title="Purchase Date">' . $row['date_of_purchase'] . '</td>';
            }
            if (strpos($value_config, ',Shipping Rate,') !== false) {
                echo '<td data-title="Ship Rate">' . $row['shipping_rate'] . '</td>';
            }
            if (strpos($value_config, ',Shipping Cash,') !== false) {
                echo '<td data-title="Ship Cash">' . $row['shipping_cash'] . '</td>';
            }
            if (strpos($value_config, ',Freight Charge,') !== false) {
                echo '<td data-title="Freight">' . $row['freight_charge'] . '</td>';
            }
            if (strpos($value_config, ',Exchange Rate,') !== false) {
                echo '<td data-title="Exchange Rate">' . $row['exchange_rate'] . '</td>';
            }
            if (strpos($value_config, ',Exchange $,') !== false) {
                echo '<td data-title="Exchange $">' . $row['exchange_cash'] . '</td>';
            }
            if (strpos($value_config, ',Sell Price,') !== false) {
                echo '<td data-title="Sell Price">' . $row['sell_price'] . '</td>';
            }
            if (strpos($value_config, ',Final Retail Price,') !== false) {
                echo '<td data-title="Retail">' . $row['final_retail_price'] . '</td>';
            }
            if (strpos($value_config, ',Wholesale Price,') !== false) {
                echo '<td data-title="Wholesale">' . $row['wholesale_price'] . '</td>';
            }
            if (strpos($value_config, ',Commercial Price,') !== false) {
                echo '<td data-title="Comm. Price">' . $row['commercial_price'] . '</td>';
            }
            if (strpos($value_config, ',Client Price,') !== false) {
                echo '<td data-title="Client Price">' . $row['client_price'] . '</td>';
            }
            if (strpos($value_config, ',Purchase Order Price,') !== false) {
                echo '<td data-title="Purchase Order Price">' . $row['purchase_order_price'] . '</td>';
            }
            if (strpos($value_config, ',Sales Order Price,') !== false) {
                echo '<td data-title="'.SALES_ORDER_NOUN.' Price">' . $row['sales_order_price'] . '</td>';
            }
            if (strpos($value_config, ',Include in Sales Orders,') !== false) {
                    echo '<td data-title="Include in Sales'.SALES_ORDER_TILE.'">';
                    ?><input type='checkbox' style='width:20px; height:20px;' <?php if($row['include_in_so'] !== '' && $row['include_in_so'] !== NULL) { echo "checked"; } ?> id='<?PHP echo $row['inventoryid']; ?>'  name='' class='sales_order_includer' value='1'><br>
                    <?php
                    echo '</td>';
                }
                if (strpos($value_config, ',Include in P.O.S.,') !== false) {
                    echo '<td data-title="Include in P.O.S.">';
                    ?><input type='checkbox' style='width:20px; height:20px;' <?php if($row['include_in_pos'] !== '' && $row['include_in_pos'] !== NULL) { echo "checked"; } ?> id='<?PHP echo $row['inventoryid']; ?>'  name='' class='point_of_sale_includer' value='1'><br>
                    <?php
                    echo '</td>';
                }
                if (strpos($value_config, ',Include in Purchase Orders,') !== false) {
                    echo '<td data-title="Include in Purchase Orders">';
                    ?><input type='checkbox' style='width:20px; height:20px;' <?php if($row['include_in_po'] !== '' && $row['include_in_po'] !== NULL) { echo "checked"; } ?> id='<?PHP echo $row['inventoryid']; ?>'  name='' class='purchase_order_includer' value='1'><br>
                    <?php
                    echo '</td>';
                }
            if (strpos($value_config, ',Preferred Price,') !== false) {
                echo '<td data-title="Pref. Price">' . $row['preferred_price'] . '</td>';
            }

            if (strpos($value_config, ',Admin Price,') !== false) {
                echo '<td data-title="Admin Price">' . $row['admin_price'] . '</td>';
            }
            if (strpos($value_config, ',Web Price,') !== false) {
                echo '<td data-title="Web Price">' . $row['web_price'] . '</td>';
            }
            if (strpos($value_config, ',Commission Price,') !== false) {
                echo '<td data-title="Commission">' . $row['commission_price'] . '</td>';
            }
            if (strpos($value_config, ',MSRP,') !== false) {
                echo '<td data-title="MSRP">' . $row['msrp'] . '</td>';
            }
            if (strpos($value_config, ',Unit Price,') !== false) {
                echo '<td data-title="Unit Price">' . $row['unit_price'] . '</td>';
            }
            if (strpos($value_config, ',Unit Cost,') !== false) {
                echo '<td data-title="Unit Cost">' . $row['unit_cost'] . '</td>';
            }
            if (strpos($value_config, ',Rent Price,') !== false) {
                echo '<td data-title="Rent Price">' . $row['rent_price'] . '</td>';
            }
            if (strpos($value_config, ',Rental Days,') !== false) {
                echo '<td data-title="Rent Days">' . $row['rental_days'] . '</td>';
            }
            if (strpos($value_config, ',Rental Weeks,') !== false) {
                echo '<td data-title="Rent Weeks">' . $row['rental_weeks'] . '</td>';
            }
            if (strpos($value_config, ',Rental Months,') !== false) {
                echo '<td data-title="Rent Months">' . $row['rental_months'] . '</td>';
            }
            if (strpos($value_config, ',Rental Years,') !== false) {
                echo '<td data-title="Rent Years">' . $row['rental_years'] . '</td>';
            }
            if (strpos($value_config, ',Reminder/Alert,') !== false) {
                echo '<td data-title="Reminder">' . $row['reminder_alert'] . '</td>';
            }
            if (strpos($value_config, ',Daily,') !== false) {
                echo '<td data-title="Daily">' . $row['daily'] . '</td>';
            }
            if (strpos($value_config, ',Weekly,') !== false) {
                echo '<td data-title="Weekly">' . $row['weekly'] . '</td>';
            }
            if (strpos($value_config, ',Monthly,') !== false) {
                echo '<td data-title="Monthly">' . $row['monthly'] . '</td>';
            }
            if (strpos($value_config, ',Annually,') !== false) {
                echo '<td data-title="Annual">' . $row['annually'] . '</td>';
            }
            if (strpos($value_config, ',#Of Days,') !== false) {
                echo '<td data-title="# Days">' . $row['total_days'] . '</td>';
            }
            if (strpos($value_config, ',#Of Hours,') !== false) {
                echo '<td data-title="# Hours">' . $row['total_hours'] . '</td>';
            }
            if (strpos($value_config, ',#Of Kilometers,') !== false) {
                echo '<td data-title="# Kilometers">' . $row['total_km'] . '</td>';
            }
            if (strpos($value_config, ',#Of Miles,') !== false) {
                echo '<td data-title="# Miles">' . $row['total_miles'] . '</td>';
            }
            if (strpos($value_config, ',Markup By $,') !== false) {
                echo '<td data-title="Markup $">' . $row['markup'] . '</td>';
            }
            if (strpos($value_config, ',Markup By %,') !== false) {
                echo '<td data-title="Markup %">' . $row['markup_perc'] . '</td>';
            }

            if (strpos($value_config, ',GL Revenue,') !== false) {
                echo '<td data-title="GL Revenue">' . $row['revenue'] . '</td>';
            }
            if (strpos($value_config, ',GL Assets,') !== false) {
               echo '<td data-title="GL Assets">' . $row['asset'] . '</td>';
            }

            if (strpos($value_config, ',Current Stock,') !== false) {
                echo '<td data-title="Stock">' . $row['current_stock'] . '</td>';
            }

            if (strpos($value_config, ',Current Inventory,') !== false) {
                echo '<td data-title="Inventory">' . $row['current_inventory'] . '</td>';
            }
            if (strpos($value_config, ',Quantity,') !== false) {
                echo '<td data-title="Quantity">' . $row['quantity'] . '</td>';
            }
            if (strpos($value_config, ',Variance,') !== false) {
                echo '<td data-title="Variance">' . $row['inv_variance'] . '</td>';
            }
            if (strpos($value_config, ',Write-offs,') !== false) {
                echo '<td data-title="Write Offs">' . $row['write_offs'] . '</td>';
            }

            if (strpos($value_config, ',Buying Units,') !== false) {
                echo '<td data-title="Buying Units">' . $row['buying_units'] . '</td>';
            }
            if (strpos($value_config, ',Selling Units,') !== false) {
                echo '<td data-title="Selling Units">' . $row['selling_units'] . '</td>';
            }
            if (strpos($value_config, ',Stocking Units,') !== false) {
                echo '<td data-title="Stocking Units">' . $row['stocking_units'] . '</td>';
            }

            if (strpos($value_config, ',Location,') !== false) {
                echo '<td data-title="Location">' . $row['location'] . '</td>';
            }
            if (strpos($value_config, ',LSD,') !== false) {
                echo '<td data-title="LSD">' . $row['lsd'] . '</td>';
            }
            if (strpos($value_config, ',Size,') !== false) {
                echo '<td data-title="Size">' . $row['size'] . '</td>';
            }
            if (strpos($value_config, ',Weight,') !== false) {
                echo '<td data-title="Weight">' . $row['weight'] . '</td>';
            }
            if (strpos($value_config, ',Min Max,') !== false) {
                echo '<td data-title="Min / Max">' . $row['min_max'] . '</td>';
            }
            if (strpos($value_config, ',Min Bin,') !== false) {
                echo '<td data-title="Min Bin">' . $row['min_bin'] . '</td>';
            }
            if (strpos($value_config, ',Estimated Hours,') !== false) {
                echo '<td data-title="Est. Hours">' . $row['estimated_hours'] . '</td>';
            }
            if (strpos($value_config, ',Actual Hours,') !== false) {
                echo '<td data-title="Actual Hours">' . $row['actual_hours'] . '</td>';
            }
            if (strpos($value_config, ',Minimum Billable,') !== false) {
                echo '<td data-title="Min. Billable">' . $row['minimum_billable'] . '</td>';
            }
            if (strpos($value_config, ',Quote Description,') !== false) {
                echo '<td data-title="Quote Desc.">' . html_entity_decode($row['quote_description']) . '</td>';
            }
            if (strpos($value_config, ',Notes,') !== false) {
                echo '<td data-title="Notes">' . $row['note'] . '</td>';
            }
            if (strpos($value_config, ',Comments,') !== false) {
                echo '<td data-title="Comments">' . $row['comment'] . '</td>';
            }

            echo '<td data-title="Function">';
            if(vuaed_visible_function($dbc, 'vpl') == 1) {
            echo '<a href="add_inventory.php?inventoryid='.$row['inventoryid'].'&vendorid='.$row['vendorid'].'">Edit</a> | ';
            echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&vplid='.$row['inventoryid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
            }
            echo '</td>';

            echo "</tr>";
        }

        echo '</table>';

        // Added Pagination //
        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);

        if(vuaed_visible_function($dbc, 'vpl') == 1) {
            if($_GET['category'] != 'Top') {
                echo '<a href="add_contacts.php?category='.$category.'" class="btn brand-btn mobile-block gap-bottom pull-right">Add Product</a>';
            }
            ?>

            <span class="popover-examples pull-right pad-5"><a data-toggle="tooltip" data-placement="top" title="You must be in a category before you can add a product."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
        }
        //echo display_filter('add_contacts.php');

        ?>

    </div>
</form>