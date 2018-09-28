<?php include_once('../include.php');
$strict_view = strictview_visible_function($dbc, 'inventory');
$tile_security = get_security($dbc, 'inventory');
if($strict_view > 0) {
    $tile_security['edit'] = 0;
    $tile_security['config'] = 0;
}
if (isset($_POST['submit'])) {
    $category = $_POST['category'];
    $inventoryid = implode(',',$_POST['inventoryid']);

    $url = "add_inventory.php?category=".$category."&iid=".$inventoryid;

    echo '<script type="text/javascript"> alert("Enter Information same like Inventory Item, It will create new Inventory item with sub items."); window.location.replace("'.$url.'"); </script>';
} ?>
<form name="form_sites" method="post" action="receive_shipment.php" class="form-horizontal double-gap-top" role="form">
    <?php if($tile_security['edit'] > 0) { ?>
    <div class="pull-right double-gap-top gap-right">
        <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Add a new received shipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
        <a href="add_receive_shipment.php" class="btn brand-btn">Add Receive Shipment</a>
    </div>
    <?php } ?>

    <?php
    $starttime = date('Y-m-d');
    $endtime = date('Y-m-d');
    if (isset($_POST['search_email_submit'])) {
        $starttime = $_POST['starttime'];
        $endtime = $_POST['endtime'];
    }
    ?>

    <center><div class="form-group">
        From: <input name="starttime" type="text" class="datepicker" value="<?php echo $starttime; ?>">
        &nbsp;&nbsp;&nbsp;
        Until: <input name="endtime" type="text" class="datepicker" value="<?php echo $endtime; ?>">
    <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button><button type="submit" name="display_all_email" value="Display All" class="btn brand-btn mobile-block">Display Today's</button></div></center>

    <?php
    if (isset($_POST['search_email_submit'])) {
        $starttime = $_POST['starttime'];
        $endtime = $_POST['endtime'];
    }
    if (isset($_POST['display_all_email'])) {
        $starttime = date('Y-m-d');
        $endtime = date('Y-m-d');
    }

    $query_check_credentials = "SELECT rs.*, i.name FROM receive_shipment rs, inventory i WHERE i.inventoryid = rs.inventoryid AND (rs.date_added >= '".$starttime."' AND rs.date_added <= '".$endtime."') ORDER BY shipmentid DESC";

    $result = mysqli_query($dbc, $query_check_credentials) or die(mysqli_error($dbc));
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT receive_shipment FROM field_config_inventory WHERE tab='receive_shipment' AND accordion='receive_shipment'"));
    $inventory_config = ','.$get_field_config['receive_shipment'].',';

    $num_rows = mysqli_num_rows($result);
    if($num_rows > 0) {
        echo "<table class='table table-bordered table-striped'>";
        echo "<thead>";
        echo "<tr class='hidden-xs hidden-sm'>
        <th>Date Added</th>
        <th>Added By</th>";
        if (strpos($inventory_config, ',Inventory,') !== FALSE) {
        echo "<th>Inventory</th>";
        }
        if (strpos($inventory_config, ',Quantity,') !== FALSE) {
        echo "<th>Quantity</th>";
        }
        if (strpos($inventory_config, ',Sell Price,') !== FALSE) {
        echo "<th>Sell Price</th>";
        }
        if (strpos($inventory_config, ',Final Retail Price,') !== FALSE) {
        echo "<th>Final Retail Price</th>";
        }
        if (strpos($inventory_config, ',Wholesale Price,') !== FALSE) {
        echo "<th>Wholesale Price</th>";
        }
        if (strpos($inventory_config, ',Commercial Price,') !== FALSE) {
        echo "<th>Commercial Price</th>";
        }
        if (strpos($inventory_config, ',Client Price,') !== FALSE) {
        echo "<th>Client Price</th>";
        }
        if (strpos($inventory_config, ',Preferred Price,') !== FALSE) {
        echo "<th>Preferred Price</th>";
        }
        if (strpos($inventory_config, ',Admin Price,') !== FALSE) {
        echo "<th>Admin Price</th>";
        }
        if (strpos($inventory_config, ',Web Price,') !== FALSE) {
        echo "<th>Web Price</th>";
        }
        if (strpos($inventory_config, ',Commission Price,') !== FALSE) {
        echo "<th>Commission Price</th>";
        }
        if (strpos($inventory_config, ',MSRP,') !== FALSE) {
        echo "<th>MSRP</th>";
        }
        if (strpos($inventory_config, ',Unit Price,') !== FALSE) {
        echo "<th>Unit Price</th>";
        }
        if (strpos($inventory_config, ',Unit Cost,') !== FALSE) {
        echo "<th>Unit Cost</th>";
        }
        if (strpos($inventory_config, ',Purchase Order Price,') !== FALSE) {
        echo "<th>Purchase Order Price</th>";
        }
        if (strpos($inventory_config, ',Sales Order Price,') !== FALSE) {
        echo "<th>".SALES_ORDER_NOUN." Price</th>";
        }
        echo "</tr>";
        echo "</thead>";
    } else {
        echo "<h2>No Record Found.</h2>";
    }

    while($row = mysqli_fetch_array( $result ))
    {
        echo "<tr>";

        //echo '<td>' . decryptIt($row['first_name']).' '.decryptIt($row['last_name']) . '</td>';
        echo '<td data-title="Tab">' . $row['date_added'] . '</td>';
        echo '<td data-title="Tab">' . get_staff($dbc, $row['who_added']) . '</td>';

        if (strpos($inventory_config, ',Inventory,') !== FALSE) {
        echo '<td data-title="Inventory">' . get_inventory($dbc, $row['inventoryid'], 'name') . '</td>';
        }
        if (strpos($inventory_config, ',Quantity,') !== FALSE) {
        echo '<td data-title="Quantity">' . $row['quantity'] . '</td>';
        }
        if (strpos($inventory_config, ',Sell Price,') !== FALSE) {
        echo '<td data-title="Sell Price">' . $row['sell_price'] . '</td>';
        }
        if (strpos($inventory_config, ',Final Retail Price,') !== FALSE) {
        echo '<td data-title="Final Retail Price">' . $row['final_retail_price'] . '</td>';
        }
        if (strpos($inventory_config, ',Wholesale Price,') !== FALSE) {
        echo '<td data-title="Wholesale Price">' . $row['wholesale_price'] . '</td>';
        }
        if (strpos($inventory_config, ',Commercial Price,') !== FALSE) {
        echo '<td data-title="Commercial Price">' . $row['commercial_price'] . '</td>';
        }
        if (strpos($inventory_config, ',Client Price,') !== FALSE) {
        echo '<td data-title="Client Price">' . $row['client_price'] . '</td>';
        }
        if (strpos($inventory_config, ',Preferred Price,') !== FALSE) {
        echo '<td data-title="Preferred Price">' . $row['preferred_price'] . '</td>';
        }
        if (strpos($inventory_config, ',Admin Price,') !== FALSE) {
        echo '<td data-title="Admin Price">' . $row['admin_price'] . '</td>';
        }
        if (strpos($inventory_config, ',Web Price,') !== FALSE) {
        echo '<td data-title="Web Price">' . $row['web_price'] . '</td>';
        }
        if (strpos($inventory_config, ',Commission Price,') !== FALSE) {
        echo '<td data-title="Commission Price">' . $row['commission_price'] . '</td>';
        }
        if (strpos($inventory_config, ',MSRP,') !== FALSE) {
        echo '<td data-title="MSRP">' . $row['msrp'] . '</td>';
        }
        if (strpos($inventory_config, ',Unit Price,') !== FALSE) {
        echo '<td data-title="Unit Price">' . $row['unit_price'] . '</td>';
        }
        if (strpos($inventory_config, ',Unit Cost,') !== FALSE) {
        echo '<td data-title="Unit Cost">' . $row['unit_cost'] . '</td>';
        }
        if (strpos($inventory_config, ',Purchase Order Price,') !== FALSE) {
        echo '<td data-title="Purchase Order Price">' . $row['purchase_order_price'] . '</td>';
        }
        if (strpos($inventory_config, ',Sales Order Price,') !== FALSE) {
        echo '<td data-title="'.SALES_ORDER_NOUN.' Price">' . $row['sales_order_price'] . '</td>';
        }
        echo "</tr>";
    }

    echo '</table>';
        if($tile_security['edit'] > 0) {
            echo '
                <div class="pull-right gap-right">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Add a new received shipment."><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span>
                    <a href="add_receive_shipment.php" class="btn brand-btn">Add Receive Shipment</a>
                </div>';
        }
    ?>

</form>