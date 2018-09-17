<?php
/*
Add	Inventory
*/
include ('../include.php');
error_reporting(0);
$rookconnect = get_software_name();

$dbc_led = '';
$sea_partno_edit = false;
$led = false;

/* Cannot use $rookconnect because this check is only for SEA Alberta.
 * When SEA Alberta updates inventory, it should update everywhere, including Global and LED Edmonton.
 * LED Edmonton prices are calculated based on SEA Alberta prices
 */
if ( $_SERVER['SERVER_NAME']=='sea-alberta.rookconnect.com' ) {
    //Connect to LED Edmonton ROOK
    $dbc_led = mysqli_connect('localhost', 'led_rook_usr', 'pUnaibiS!273', 'led_rook_db');
    $sea_partno_edit = true;
    $led = true;
}

if ( isset($_GET['action']) && $_GET['action']=='delete_main_image' && isset($_GET['inventoryid']) ) {
    $inventoryid = $_GET['inventoryid'];
    $delete_main_image = mysqli_query ( $dbc, "UPDATE `inventory` SET `main_image`=NULL WHERE `inventoryid`='$inventoryid'" );
    echo '
        <script>
            alert("Main image deleted.");
            window.location.replace("add_inventory.php?inventoryid='.$inventoryid.'");
        </script>';
}

if ( isset($_GET['action']) && $_GET['action']=='delete_image' && isset($_GET['imageid']) && isset($_GET['inventoryid']) ) {
    $inventoryid    = $_GET['inventoryid'];
    $imageid        = $_GET['imageid'];
    $delete_image   = mysqli_query ( $dbc, "DELETE FROM `inventory_images` WHERE `inventoryid`='$inventoryid' AND `imageid`='$imageid'" );
    echo '
        <script>
            alert("Image deleted.");
            window.location.replace("add_inventory.php?inventoryid='.$inventoryid.'");
        </script>';
}

if ( isset($_GET['action']) && $_GET['action']=='delete_spec_sheet' && isset($_GET['inventoryid']) ) {
    $inventoryid = $_GET['inventoryid'];
    $before_change = capture_before_change($dbc, 'inventory', 'spec_sheet', 'inventoryid', $inventoryid);
    $delete_spec_sheet = mysqli_query ( $dbc, "UPDATE `inventory` SET `spec_sheet`=NULL WHERE `inventoryid`='$inventoryid'" );
    $history = capture_after_change('spec_sheet', NULL);
    add_update_history($dbc, 'inventory_history', $history, '', $before_change);
    echo '
        <script>
            alert("Spec Sheet deleted.");
            window.location.replace("add_inventory.php?inventoryid='.$inventoryid.'");
        </script>';
}

if (isset($_POST['submit'])) {
    $brand = filter_var(htmlentities($_POST['brand']),FILTER_SANITIZE_STRING);
    $code = filter_var($_POST['code'],FILTER_SANITIZE_STRING);
    $description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);
    $application = filter_var(htmlentities($_POST['application']),FILTER_SANITIZE_STRING);
    $comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);
    $question = filter_var(htmlentities($_POST['question']),FILTER_SANITIZE_STRING);
    $request = filter_var(htmlentities($_POST['request']),FILTER_SANITIZE_STRING);
    $note = filter_var(htmlentities($_POST['note']),FILTER_SANITIZE_STRING);

    if($_POST['same_desc'] == 1) {
        $quote_description = $description;
    } else {
        $quote_description = filter_var(htmlentities($_POST['quote_description']),FILTER_SANITIZE_STRING);
    }

    $vendorid =	( empty ( $_POST['vendorid'] ) ) ? 0 : $_POST['vendorid'];
    $display_website = $_POST['display_website'];
    $featured = $_POST['featured'];
    $on_sale = $_POST['on_sale'];
    $on_clearance = $_POST['on_clearance'];
    $new_item = $_POST['new_item'];

    if ( $_FILES["upload_main_image"]["name"] ) {
        $main_image = $_FILES["upload_main_image"]["name"];
        move_uploaded_file($_FILES["upload_main_image"]["tmp_name"], "download/".$_FILES["upload_main_image"]["name"]) ;
    }

    if ( $_FILES["upload_additional_images"]["name"] ) {
        $additional_images = implode('*#*',$_FILES["upload_additional_images"]["name"]);
        for($i = 0; $i < count($_FILES['upload_additional_images']['name']); $i++) {
            move_uploaded_file($_FILES["upload_additional_images"]["tmp_name"][$i], "download/".$_FILES["upload_additional_images"]["name"][$i]) ;
        }
    }

    if ( $_FILES["spec_sheet"]["name"] ) {
        $spec_sheet = $_FILES["spec_sheet"]["name"];
        move_uploaded_file($_FILES["spec_sheet"]["tmp_name"], "download/".$_FILES["spec_sheet"]["name"]) ;
    }

    if($_POST['size'] == 'Other') {
        $size = filter_var($_POST['size_name'],FILTER_SANITIZE_STRING);
    } else {
        $size = filter_var($_POST['size'],FILTER_SANITIZE_STRING);
    }

    /* OLD weight dropdown
    if($_POST['weight'] == 'Other') {
        $weight = filter_var($_POST['weight_name'],FILTER_SANITIZE_STRING);
    } else {
        $weight = filter_var($_POST['weight'],FILTER_SANITIZE_STRING);
    }*/

    $weight   = filter_var($_POST['weight'],FILTER_SANITIZE_STRING);
    $gauge    =	filter_var($_POST['gauge'],FILTER_SANITIZE_STRING);
    $length   =	filter_var($_POST['length'],FILTER_SANITIZE_STRING);
    $pressure =	filter_var($_POST['pressure'],FILTER_SANITIZE_STRING);

    $type =	filter_var($_POST['type'],FILTER_SANITIZE_STRING);
    $date_of_purchase =	filter_var($_POST['date_of_purchase'],FILTER_SANITIZE_STRING);
	if ( empty ($date_of_purchase) ) { $date_of_purchase = '0000-00-00'; }
    $purchase_cost =	filter_var($_POST['purchase_cost'],FILTER_SANITIZE_STRING);
	if ( empty ($purchase_cost) ) { $purchase_cost = 0.00; }
    $sell_price =	filter_var($_POST['sell_price'],FILTER_SANITIZE_STRING);
	if ( empty ($sell_price) ) { $sell_price = 0.00; }
    $markup =	filter_var($_POST['markup'],FILTER_SANITIZE_STRING);
    $freight_charge =	filter_var($_POST['freight_charge'],FILTER_SANITIZE_STRING);
	if ( empty ($freight_charge) ) { $freight_charge = 0.00; }
    $min_bin =	filter_var($_POST['min_bin'],FILTER_SANITIZE_STRING);
	if ( empty ($min_bin) ) { $min_bin = 0.00; }
    $current_stock =	filter_var($_POST['current_stock'],FILTER_SANITIZE_STRING);
	if ( empty ($current_stock) ) { $current_stock = 0; }

	$stocking_units = filter_var($_POST['stocking_units'],FILTER_SANITIZE_STRING);
	$selling_units  = filter_var($_POST['selling_units'],FILTER_SANITIZE_STRING);
	$buying_units = filter_var($_POST['buying_units'],FILTER_SANITIZE_STRING);
	$warehouse = filter_var($_POST['warehouse'],FILTER_SANITIZE_STRING);
	$location = filter_var($_POST['location'],FILTER_SANITIZE_STRING);
	$asset = filter_var($_POST['asset'],FILTER_SANITIZE_STRING);
	$revenue = filter_var($_POST['revenue'],FILTER_SANITIZE_STRING);
	$inv_variance = filter_var($_POST['inv_variance'],FILTER_SANITIZE_STRING);
    $web_price = filter_var($_POST['web_price'],FILTER_SANITIZE_STRING);
	if ( empty ($web_price) ) { $web_price = 0.00; }
    $clearance_price = filter_var($_POST['clearance_price'],FILTER_SANITIZE_STRING);
	if ( empty ($clearance_price) ) { $clearance_price = 0.00; }
	$average_cost = filter_var($_POST['average_cost'],FILTER_SANITIZE_STRING);
	$preferred_price = filter_var($_POST['preferred_price'],FILTER_SANITIZE_STRING);
	if ( empty ($preferred_price) ) { $preferred_price = 0.00; }

    $id_number = filter_var($_POST['id_number'],FILTER_SANITIZE_STRING);
    $operator = filter_var($_POST['operator'],FILTER_SANITIZE_STRING);
    $lsd = filter_var($_POST['lsd'],FILTER_SANITIZE_STRING);

    $distributor_price = filter_var($_POST['distributor_price'],FILTER_SANITIZE_STRING);
	if ( empty ($distributor_price) ) { $distributor_price = 0.00; }
    $final_retail_price = filter_var($_POST['final_retail_price'],FILTER_SANITIZE_STRING);
	if ( empty ($final_retail_price) ) { $final_retail_price = 0.00; }
    $admin_price = filter_var($_POST['admin_price'],FILTER_SANITIZE_STRING);
	if ( empty ($admin_price) ) { $admin_price = 0.00; }
    $wholesale_price = filter_var($_POST['wholesale_price'],FILTER_SANITIZE_STRING);
	if ( empty ($wholesale_price) ) { $wholesale_price = 0.00; }
    $commercial_price = filter_var($_POST['commercial_price'],FILTER_SANITIZE_STRING);
	if ( empty ($commercial_price) ) { $commercial_price = 0.00; }
    $client_price = filter_var($_POST['client_price'],FILTER_SANITIZE_STRING);
	if ( empty ($client_price) ) { $client_price = 0.00; }
	$purchase_order_price = filter_var($_POST['purchase_order_price'],FILTER_SANITIZE_STRING);
	if ( empty ($purchase_order_price) ) { $purchase_order_price = 0.00; }
	$sales_order_price = filter_var($_POST['sales_order_price'],FILTER_SANITIZE_STRING);
	if ( empty ($sales_order_price) ) { $sales_order_price = 0.00; }
    $minimum_billable = filter_var($_POST['minimum_billable'],FILTER_SANITIZE_STRING);
    $estimated_hours = filter_var($_POST['estimated_hours'],FILTER_SANITIZE_STRING);
    $actual_hours = filter_var($_POST['actual_hours'],FILTER_SANITIZE_STRING);
    $msrp = filter_var($_POST['msrp'],FILTER_SANITIZE_STRING);
	if ( empty ($msrp) ) { $msrp = 0.00; }

    $product_name = filter_var($_POST['product_name'],FILTER_SANITIZE_STRING);
    $cost = filter_var($_POST['cost'],FILTER_SANITIZE_STRING);
    $usd_cpu = filter_var($_POST['usd_cpu'],FILTER_SANITIZE_STRING);
	if ( empty ($usd_cpu) ) { $usd_cpu = 0.00; }
    $commission_price = filter_var($_POST['commission_price'],FILTER_SANITIZE_STRING);
    $markup_perc = filter_var($_POST['markup_perc'],FILTER_SANITIZE_STRING);
    $current_inventory = filter_var($_POST['current_inventory'],FILTER_SANITIZE_STRING);
	if ( empty ($current_inventory) ) { $current_inventory = 0; }
    $write_offs = filter_var($_POST['write_offs'],FILTER_SANITIZE_STRING);
    $min_max = filter_var($_POST['min_max'],FILTER_SANITIZE_STRING);
    $status = filter_var($_POST['status'],FILTER_SANITIZE_STRING);
    $drum_unit_cost = filter_var($_POST['drum_unit_cost'],FILTER_SANITIZE_STRING);
	if ( empty ($drum_unit_cost) ) { $drum_unit_cost = 0.00; }
    $drum_unit_price = filter_var($_POST['drum_unit_price'],FILTER_SANITIZE_STRING);
	if ( empty ($drum_unit_price) ) { $drum_unit_price = 0.00; }
    $tote_unit_cost = filter_var($_POST['tote_unit_cost'],FILTER_SANITIZE_STRING);
	if ( empty ($tote_unit_cost) ) { $tote_unit_cost = 0.00; }
    $tote_unit_price = filter_var($_POST['tote_unit_price'],FILTER_SANITIZE_STRING);
	if ( empty ($tote_unit_price) ) { $tote_unit_price = 0.00; }
    $wcb_price = filter_var($_POST['wcb_price'],FILTER_SANITIZE_STRING);
	if ( empty ($wcb_price) ) { $wcb_price = 0.00; }
    $unit_price = filter_var($_POST['unit_price'],FILTER_SANITIZE_STRING);
	if ( empty ($unit_price) ) { $unit_price = 0.00; }
    $unit_cost = filter_var($_POST['unit_cost'],FILTER_SANITIZE_STRING);
	if ( empty ($unit_cost) ) { $unit_cost = 0.00; }
    $rent_price = filter_var($_POST['rent_price'],FILTER_SANITIZE_STRING);
	if ( empty ($rent_price) ) { $rent_price = 0.00; }
    $rental_days = filter_var($_POST['rental_days'],FILTER_SANITIZE_STRING);
    $rental_weeks = filter_var($_POST['rental_weeks'],FILTER_SANITIZE_STRING);
    $rental_months = filter_var($_POST['rental_months'],FILTER_SANITIZE_STRING);
    $rental_years = filter_var($_POST['rental_years'],FILTER_SANITIZE_STRING);
    $reminder_alert = filter_var($_POST['reminder_alert'],FILTER_SANITIZE_STRING);
    $daily = filter_var($_POST['daily'],FILTER_SANITIZE_STRING);
    $weekly = filter_var($_POST['weekly'],FILTER_SANITIZE_STRING);
    $monthly = filter_var($_POST['monthly'],FILTER_SANITIZE_STRING);
    $annually = filter_var($_POST['annually'],FILTER_SANITIZE_STRING);
    $total_days = filter_var($_POST['total_days'],FILTER_SANITIZE_STRING);
    $total_hours = filter_var($_POST['total_hours'],FILTER_SANITIZE_STRING);
    $total_km = filter_var($_POST['total_km'],FILTER_SANITIZE_STRING);
    $total_miles = filter_var($_POST['total_miles'],FILTER_SANITIZE_STRING);
	$include_in_pos = filter_var($_POST['include_in_pos'],FILTER_SANITIZE_STRING);
    $include_in_product = filter_var($_POST['include_in_product'],FILTER_SANITIZE_STRING);
	$include_in_po = filter_var($_POST['include_in_po'],FILTER_SANITIZE_STRING);
	$include_in_so = filter_var($_POST['include_in_so'],FILTER_SANITIZE_STRING);

    $item_sku = filter_var($_POST['item_sku'],FILTER_SANITIZE_STRING);
    $color = filter_var($_POST['color'],FILTER_SANITIZE_STRING);
    $suggested_retail_price = filter_var($_POST['suggested_retail_price'],FILTER_SANITIZE_STRING);
    $min_amount = filter_var($_POST['min_amount'],FILTER_SANITIZE_STRING);
    $max_amount = filter_var($_POST['max_amount'],FILTER_SANITIZE_STRING);
    $rush_price = filter_var($_POST['rush_price'],FILTER_SANITIZE_STRING);
    $distributor_price = filter_var($_POST['distributor_price'],FILTER_SANITIZE_STRING);

    if($_POST['category'] == 'Other') {
        $category = filter_var($_POST['category_name'],FILTER_SANITIZE_STRING);
    } else {
        //$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
        $category = filter_var($_POST['actual_category_name'],FILTER_SANITIZE_STRING);
    }

    if($_POST['sub_category'] == 'Other') {
        $sub_category = filter_var($_POST['sub_category_name'],FILTER_SANITIZE_STRING);
    } else {
        $sub_category = filter_var($_POST['sub_category'],FILTER_SANITIZE_STRING);
    }

    $part_no = filter_var($_POST['part_no'],FILTER_SANITIZE_STRING);
    $part_no_old = '';
    $gst_exempt = filter_var($_POST['gst_exempt'],FILTER_SANITIZE_STRING);
    $gtin = filter_var($_POST['gtin'],FILTER_SANITIZE_STRING);

    $name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
    $name_on_website = filter_var($_POST['name_on_website'],FILTER_SANITIZE_STRING);

    $usd_invoice = filter_var($_POST['usd_invoice'],FILTER_SANITIZE_STRING);
    $shipping_rate =	filter_var($_POST['shipping_rate'],FILTER_SANITIZE_STRING);
	if ( empty ($shipping_rate) ) { $shipping_rate = 0.00; }
    $shipping_cash =	filter_var($_POST['shipping_cash'],FILTER_SANITIZE_STRING);
	if ( empty ($shipping_cash) ) { $shipping_cash = 0.00; }
    $exchange_rate =	filter_var($_POST['exchange_rate'],FILTER_SANITIZE_STRING);
	if ( empty ($exchange_rate) ) { $exchange_rate = 0.00; }
    $exchange_cash =	filter_var($_POST['exchange_cash'],FILTER_SANITIZE_STRING);
	if ( empty ($exchange_cash) ) { $exchange_cash = 0.00; }
    $pallet =	filter_var($_POST['pallet'],FILTER_SANITIZE_STRING);
    $cdn_cpu =	filter_var($_POST['cdn_cpu'],FILTER_SANITIZE_STRING);
	if ( empty ($cdn_cpu) ) { $cdn_cpu = 0.00; }
    $cogs_total =	filter_var($_POST['cogs_total'],FILTER_SANITIZE_STRING);

	$quantity = filter_var($_POST['quantity'],FILTER_SANITIZE_STRING);
	$bill_of_material = filter_var($_POST['bill_of_material_hidden'],FILTER_SANITIZE_STRING);
			if($bill_of_material == '0') {
				$bill_of_material = filter_var(implode(',',$_POST['bill_of_material']),FILTER_SANITIZE_STRING);
			}
	if(!empty($_POST['inventoryid'])) {

		$resultw = mysqli_query($dbc, "SELECT * FROM inventory WHERE inventoryid= '".$_POST['inventoryid']."'");
		while($rww = mysqli_fetch_assoc($resultw)) {

			// Put in bill of material history log, if the user changed the bill of material of this inventory item. //
			if($rww['bill_of_material'] !== $bill_of_material) {
				$dater = date('Y/m/d h:i:s a', time());
				$contactid = $_SESSION['contactid'];
				$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid'");
				while($row = mysqli_fetch_assoc($result)) {
					$contact_name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' (ID: '.$row['contactid'].')';
				}
				$query_insert_inventory = "INSERT INTO `bill_of_material_log` (`pieces_of_inventoryid`, `old_pieces_of_inventoryid`, `inventoryid`, `date_time`, `contact`, `type`, `deleted`
    ) VALUES ('$bill_of_material', '".$rww['bill_of_material']."', '".$_POST['inventoryid']."', '$dater', '$contact_name', 'Edit', '0')";
				mysqli_query($dbc, $query_insert_inventory) or die(mysqli_error($dbc));
			}

			// Put in inventory quantity change log, if the user changed the quantity of this inventory item.
			$chng_qty = $quantity - $rww['quantity'];
			$old_inventory = $rww['quantity'];
			if($old_inventory == '' || $old_inventory == NULL) {
				$old_inventory = 0;
			}
			$new_inv = $quantity;
			$contactidd = $_SESSION['contactid'];
			$datetime = date('Y/m/d h:i:s a', time());
			$inv = $_POST['inventoryid'];
			$cur_inv = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `inventory` WHERE `inventoryid`='$inv'"));
			$old_cost = ($cur_inv['purchase_cost'] > 0 ? $cur_inv['purchase_cost'] : ($cur_inv['unit_cost'] > 0 ? $cur_inv['unit_cost'] : ($cur_inv['cost'] > 0 ? $cur_inv['cost'] : $cur_inv['average_cost'])));
			$current_cost = ($purchase_cost > 0 ? $purchase_cost : ($unit_cost > 0 ? $unit_cost : ($cost > 0 ? $cost : $average_cost)));
			$new_cost = ($chng_qty > 0 ? (($old_cost * $old_inventory) + ($current_cost * $chng_qty)) / $new_inv : $current_cost);
			$average_cost = ($average_cost > 0 && $average_cost != $cur_inv['average_cost'] ? $average_cost : $new_cost);
			$query_add_log = "INSERT INTO `inventory_change_log` (`inventoryid`, `contactid`, `location_of_change`, `old_inventory`, `old_cost`, `changed_quantity`, `current_cost`, `new_inventory`, `new_cost`, `date_time`, `deleted`) VALUES ('$inv', '$contactidd', 'Inventory Tile', '$old_inventory', '$old_cost', '$chng_qty', '$current_cost', '$new_inv', '$new_cost', '$datetime', '0' )";

            mysqli_query($dbc, $query_add_log) or die(mysqli_error($dbc));

            $part_no_old = $rww['part_no'];
		}
	}

    $supplimentary = filter_var(implode(',',$_POST['supplimentary']),FILTER_SANITIZE_STRING);

    if(empty($_POST['inventoryid'])) {
        // New Inventory Item
        $query_insert_inventory = "INSERT INTO `inventory` (`code`, `gtin`, `brand`, `category`, `sub_category`, `part_no`, `gst_exempt`, `description`, `application`, `supplimentary`, `comment`, `question`, `request`, `display_website`, `vendorid`, `size`, `gauge`, `weight`, `length`, `pressure`, `type`, `name`, `name_on_website`, `date_of_purchase`, `purchase_cost`, `sell_price`, `markup`, `freight_charge`, `min_bin`, `current_stock`, `final_retail_price`, `admin_price`, `wholesale_price`, `commercial_price`, `client_price`, `purchase_order_price`, `sales_order_price`, `distributor_price`, `minimum_billable`, `estimated_hours`, `actual_hours`, `msrp`, `quote_description`, `usd_invoice`, `shipping_rate`, `shipping_cash`, `exchange_rate`, `exchange_cash`, `pallet`, `cdn_cpu`, `cogs_total`, `warehouse`, `location`, `inv_variance`, `average_cost`, `asset`, `revenue`, `buying_units`, `selling_units`, `stocking_units`, `preferred_price`, `web_price`, `clearance_price`, `id_number`, `operator`, `lsd`, `quantity`, `product_name`, `cost`, `usd_cpu`, `commission_price`, `markup_perc`, `current_inventory`, `write_offs`, `min_max`, `status`, `note`, `unit_price`, `unit_cost`, `rent_price`, `rental_days`, `rental_weeks`, `rental_months`, `rental_years`, `reminder_alert`, `daily`,`weekly`, `monthly`, `annually`,  `total_days`, `total_hours`, `total_km`, `total_miles`, `bill_of_material`, `include_in_so`,`include_in_po`,`include_in_pos`, `drum_unit_cost`, `drum_unit_price`, `tote_unit_cost`, `tote_unit_price`, `include_in_product`, `wcb_price`, `spec_sheet`, `featured`, `sale`, `clearance`, `new`, `main_image`,`item_sku`,`color`,`suggested_retail_price`, `rush_price`, `min_amount`, `max_amount`)
			VALUES ('$code', '$gtin', '$brand', '$category', '$sub_category', '$part_no', '$gst_exempt', '$description', '$application', '$supplimentary', '$comment', '$question', '$request', '$display_website', '$vendorid', '$size', '$gauge', '$weight', '$length', '$pressure', '$type', '$name', '$name_on_website', '$date_of_purchase', '$purchase_cost', '$sell_price', '$markup', '$freight_charge', '$min_bin', '$current_stock', '$final_retail_price', '$admin_price', '$wholesale_price', '$commercial_price', '$client_price', '$purchase_order_price', '$sales_order_price', '$distributor_price', '$minimum_billable', '$estimated_hours', '$actual_hours', '$msrp', '$quote_description', '$usd_invoice', '$shipping_rate', '$shipping_cash', '$exchange_rate', '$exchange_cash', '$pallet', '$cdn_cpu', '$cogs_total', '$warehouse', '$location', '$inv_variance', '$average_cost', '$asset', '$revenue', '$buying_units', '$selling_units', '$stocking_units', '$preferred_price', '$web_price', '$clearance_price', '$id_number', '$operator', '$lsd', '$quantity', '$product_name', '$cost', '$usd_cpu', '$commission_price', '$markup_perc', '$current_inventory', '$write_offs', '$min_max', '$status', '$note', '$unit_price', '$unit_cost', '$rent_price', '$rental_days', '$rental_weeks', '$rental_months', '$rental_years', '$reminder_alert', '$daily', '$weekly', '$monthly', '$annually', '$total_days', '$total_hours', '$total_km', '$total_miles', '$bill_of_material', '$include_in_so', '$include_in_po', '$include_in_pos', '$drum_unit_cost', '$drum_unit_price', '$tote_unit_cost', '$tote_unit_price', '$include_in_product', '$wcb_price', '$spec_sheet', '$featured', '$on_sale', '$on_clearance', '$new_item', '$main_image', '$item_sku', '$color', '$suggested_retail_price', '$rush_price', '$min_amount', '$max_amount')";
        $result_insert_inventory = mysqli_query($dbc, $query_insert_inventory);
        $inventoryid = mysqli_insert_id($dbc);

        $before_change = '';
        $history = "New inventory Added. <br />";
		    add_update_history($dbc, 'inventory_history', $history, '', $before_change);

        // Insert the same record to led.rookconnect.com
        if ( $led ) {
            /* Change prices before inserting to LED
             * Final Retail Price = SEA Alberta Web Price
             * Preferred Price = CDN Cost + 50%
             * Distributor Price = CDN Cost + 32%
             */
            $final_retail_price_led = $web_price;
            $preferred_price_led = $cdn_cpu + ($cdn_cpu * 0.5);
            $distributor_price_led = $cdn_cpu + ($cdn_cpu * 0.32);
            $query_insert_inventory_led = "INSERT INTO `inventory` (`code`, `gtin`, `brand`, `category`, `sub_category`, `part_no`, `gst_exempt`, `description`, `application`, `supplimentary`, `comment`, `question`, `request`, `display_website`, `vendorid`, `size`, `gauge`, `weight`, `length`, `pressure`, `type`, `name`, `name_on_website`, `date_of_purchase`, `purchase_cost`, `sell_price`, `markup`, `freight_charge`, `min_bin`, `current_stock`, `final_retail_price`, `admin_price`, `wholesale_price`, `commercial_price`, `client_price`, `purchase_order_price`, `sales_order_price`, `distributor_price`, `minimum_billable`, `estimated_hours`, `actual_hours`, `msrp`, `quote_description`, `usd_invoice`, `shipping_rate`, `shipping_cash`, `exchange_rate`, `exchange_cash`, `pallet`, `cdn_cpu`, `cogs_total`, `warehouse`, `location`, `inv_variance`, `average_cost`, `asset`, `revenue`, `buying_units`, `selling_units`, `stocking_units`, `preferred_price`, `web_price`, `clearance_price`, `id_number`, `operator`, `lsd`, `quantity`, `product_name`, `cost`, `usd_cpu`, `commission_price`, `markup_perc`, `current_inventory`, `write_offs`, `min_max`, `status`, `note`, `unit_price`, `unit_cost`, `rent_price`, `rental_days`, `rental_weeks`, `rental_months`, `rental_years`, `reminder_alert`, `daily`,`weekly`, `monthly`, `annually`,  `total_days`, `total_hours`, `total_km`, `total_miles`, `bill_of_material`, `include_in_so`,`include_in_po`,`include_in_pos`, `drum_unit_cost`, `drum_unit_price`, `tote_unit_cost`, `tote_unit_price`, `include_in_product`, `wcb_price`, `spec_sheet`, `featured`, `sale`, `clearance`, `new`, `main_image`,`item_sku`,`color`,`suggested_retail_price`, `rush_price`, `min_amount`, `max_amount`)
                VALUES ('$code', '$gtin', '$brand', '$category', '$sub_category', '$part_no', '$gst_exempt', '$description', '$application', '$supplimentary', '$comment', '$question', '$request', '$display_website', '$vendorid', '$size', '$gauge', '$weight', '$length', '$pressure', '$type', '$name', '$name_on_website', '$date_of_purchase', '$purchase_cost', '$sell_price', '$markup', '$freight_charge', '$min_bin', '$current_stock', '$final_retail_price_led', '$admin_price', '$wholesale_price', '$commercial_price', '$client_price', '$purchase_order_price', '$sales_order_price', '$distributor_price_led', '$minimum_billable', '$estimated_hours', '$actual_hours', '$msrp', '$quote_description', '$usd_invoice', '$shipping_rate', '$shipping_cash', '$exchange_rate', '$exchange_cash', '$pallet', '$cdn_cpu', '$cogs_total', '$warehouse', '$location', '$inv_variance', '$average_cost', '$asset', '$revenue', '$buying_units', '$selling_units', '$stocking_units', '$preferred_price_led', '$web_price', '$clearance_price', '$id_number', '$operator', '$lsd', '$quantity', '$product_name', '$cost', '$usd_cpu', '$commission_price', '$markup_perc', '$current_inventory', '$write_offs', '$min_max', '$status', '$note', '$unit_price', '$unit_cost', '$rent_price', '$rental_days', '$rental_weeks', '$rental_months', '$rental_years', '$reminder_alert', '$daily', '$weekly', '$monthly', '$annually', '$total_days', '$total_hours', '$total_km', '$total_miles', '$bill_of_material', '$include_in_so', '$include_in_po', '$include_in_pos', '$drum_unit_cost', '$drum_unit_price', '$tote_unit_cost', '$tote_unit_price', '$include_in_product', '$wcb_price', '$spec_sheet', '$featured', '$on_sale', '$on_clearance', '$new_item', '$main_image', '$item_sku', '$color', '$suggested_retail_price', '$rush_price', '$min_amount', '$max_amount')";
            $result_insert_inventory_led = mysqli_query($dbc_led, $query_insert_inventory_led);
            $before_change = '';
            $history = "New inventory Added. <br />";
    		    add_update_history($dbc, 'inventory_history', $history, '', $before_change);
        } else {
            //Else we sync the Inventory with the configured db on database_connection.php More details in Admin Settings > Sync Inventory
            if ( $dbc_inventory ) {
                mysqli_query($dbc_inventory, $query_insert_inventory);
            }
        }

        if ( !empty($additional_images) ) {
            for($i = 0; $i < count($_FILES['upload_additional_images']['name']); $i++) {
                $additional_image = $_FILES["upload_additional_images"]["name"][$i];
                $query_insert_images = "INSERT INTO `inventory_images` (`inventoryid`, `image`) VALUES ('$inventoryid', '$additional_image')";
                $result_insert_images = mysqli_query($dbc, $query_insert_images);
            }

            $before_change = '';
            $history = "New inventory images Added. <br />";
    		    add_update_history($dbc, 'inventory_history', $history, '', $before_change);
        }

        $url = 'Added';

    } else {
        // Update Inventory Item
        $inventoryid = $_POST['inventoryid'];
        $update_spec_sheet = ( empty($spec_sheet) ) ? "" : " `spec_sheet`='$spec_sheet',";
        $update_main_image = ( empty($main_image) ) ? "" : ", `main_image`='$main_image'";
        $query_update_inventory = "UPDATE `inventory` SET `code`='$code', `gtin`='$gtin', `brand`='$brand', `category`='$category', `sub_category`='$sub_category', `part_no`='$part_no', `gst_exempt`='$gst_exempt', `description`='$description', `application`='$application', `supplimentary`='$supplimentary', `comment`='$comment', `question`='$question', `request`='$request', `display_website`='$display_website', `vendorid`='$vendorid', `size`='$size', `gauge`='$gauge', `weight`='$weight', `length`='$length', `pressure`='$pressure', `type`='$type', `name`='$name', `name_on_website`='$name_on_website', `date_of_purchase`='$date_of_purchase', `purchase_cost`='$purchase_cost', `sell_price`='$sell_price', `markup`='$markup', `freight_charge`='$freight_charge', `min_bin`='$min_bin', `current_stock`='$current_stock', `final_retail_price`='$final_retail_price', `admin_price`='$admin_price', `wholesale_price`='$wholesale_price', `commercial_price`='$commercial_price', `client_price`='$client_price', `purchase_order_price`='$purchase_order_price', `sales_order_price`='$sales_order_price', `distributor_price`='$distributor_price', `minimum_billable`='$minimum_billable', `estimated_hours`='$estimated_hours', `actual_hours`='$actual_hours', `msrp`='$msrp', `quote_description`='$quote_description', `usd_invoice`='$usd_invoice', `shipping_rate`='$shipping_rate', `shipping_cash`='$shipping_cash', `exchange_rate`='$exchange_rate', `exchange_cash`='$exchange_cash', `pallet`='$pallet', `cdn_cpu`='$cdn_cpu', `cogs_total`='$cogs_total', `warehouse`='$warehouse', `location`='$location', `inv_variance`='$inv_variance', `average_cost`='$average_cost', `asset`='$asset', `revenue`='$revenue', `buying_units`='$buying_units', `selling_units`='$selling_units', `stocking_units`='$stocking_units', `preferred_price`='$preferred_price', `web_price`='$web_price', `clearance_price`='$clearance_price', `id_number`='$id_number', `operator`='$operator', `lsd`='$lsd', `quantity`='$quantity', `product_name`='$product_name', `cost`='$cost', `usd_cpu`='$usd_cpu', `commission_price`='$commission_price', `markup_perc`='$markup_perc', `current_inventory`='$current_inventory', `write_offs`='$write_offs', `min_max`='$min_max', `status`='$status', `note`='$note', `unit_price`='$unit_price', `unit_cost`='$unit_cost', `rent_price`='$rent_price', `rental_days`='$rental_days', `rental_weeks`='$rental_weeks', `rental_months`='$rental_months', `rental_years`='$rental_years', `reminder_alert`='$reminder_alert', `daily`='$daily', `weekly`='$weekly', `monthly`='$monthly', `annually`='$annually', `total_days`='$total_days', `total_hours`='$total_hours', `total_km`='$total_km', `total_miles`='$total_miles', `bill_of_material`='$bill_of_material', `include_in_so`='$include_in_so', `include_in_po`='$include_in_po', `include_in_pos`='$include_in_pos', `drum_unit_cost`= '$drum_unit_cost', `drum_unit_price`='$drum_unit_price', `tote_unit_cost`='$tote_unit_cost', `tote_unit_price`='$tote_unit_price', `include_in_product`='$include_in_product', `wcb_price`='$wcb_price',". $update_spec_sheet ." `featured`='$featured', `sale`='$on_sale', `clearance`='$on_clearance', `new`='$new_item'". $update_main_image .", `item_sku` = '$item_sku', `color` = '$color', `suggested_retail_price` = '$suggested_retail_price', `rush_price` = '$rush_price', `min_amount` = '$min_amount', `max_amount` = '$max_amount' WHERE `inventoryid`='$inventoryid'";
        $result_update_inventory	= mysqli_query($dbc, $query_update_inventory);
        $before_change = '';
        $history = "Inventory with id $inventoryid is been Updated. <br />";
        add_update_history($dbc, 'inventory_history', $history, '', $before_change);

        if ( $led ) {
            /* Update the same record on led.rookconnect.com
             * Change prices before inserting to LED
             * Final Retail Price = SEA Alberta Web Price
             * Preferred Price = CDN Cost + 50%
             * Distributor Price = CDN Cost + 32%
             */
            if ( !empty($part_no_old) ) {
                $final_retail_price_led = $web_price;
                $preferred_price_led = $cdn_cpu + ($cdn_cpu * 0.5);
                $distributor_price_led = $cdn_cpu + ($cdn_cpu * 0.32);
                $query_update_inventory_led = "UPDATE `inventory` SET `code`='$code', `gtin`='$gtin', `brand`='$brand', `category`='$category', `sub_category`='$sub_category', `part_no`='$part_no', `gst_exempt`='$gst_exempt', `description`='$description', `application`='$application', `supplimentary`='$supplimentary', `comment`='$comment', `question`='$question', `request`='$request', `display_website`='$display_website', `vendorid`='$vendorid', `size`='$size', `gauge`='$gauge', `weight`='$weight', `length`='$length', `pressure`='$pressure', `type`='$type', `name`='$name', `name_on_website`='$name_on_website', `date_of_purchase`='$date_of_purchase', `purchase_cost`='$purchase_cost', `sell_price`='$sell_price', `markup`='$markup', `freight_charge`='$freight_charge', `min_bin`='$min_bin', `current_stock`='$current_stock', `final_retail_price`='$final_retail_price_led', `admin_price`='$admin_price', `wholesale_price`='$wholesale_price', `commercial_price`='$commercial_price', `client_price`='$client_price', `purchase_order_price`='$purchase_order_price', `sales_order_price`='$sales_order_price', `distributor_price`='$distributor_price_led', `minimum_billable`='$minimum_billable', `estimated_hours`='$estimated_hours', `actual_hours`='$actual_hours', `msrp`='$msrp', `quote_description`='$quote_description', `usd_invoice`='$usd_invoice', `shipping_rate`='$shipping_rate', `shipping_cash`='$shipping_cash', `exchange_rate`='$exchange_rate', `exchange_cash`='$exchange_cash', `pallet`='$pallet', `cdn_cpu`='$cdn_cpu', `cogs_total`='$cogs_total', `warehouse`='$warehouse', `location`='$location', `inv_variance`='$inv_variance', `average_cost`='$average_cost', `asset`='$asset', `revenue`='$revenue', `buying_units`='$buying_units', `selling_units`='$selling_units', `stocking_units`='$stocking_units', `preferred_price`='$preferred_price_led', `web_price`='$web_price', `clearance_price`='$clearance_price', `id_number`='$id_number', `operator`='$operator', `lsd`='$lsd', `quantity`='$quantity', `product_name`='$product_name', `cost`='$cost', `usd_cpu`='$usd_cpu', `commission_price`='$commission_price', `markup_perc`='$markup_perc', `current_inventory`='$current_inventory', `write_offs`='$write_offs', `min_max`='$min_max', `status`='$status', `note`='$note', `unit_price`='$unit_price', `unit_cost`='$unit_cost', `rent_price`='$rent_price', `rental_days`='$rental_days', `rental_weeks`='$rental_weeks', `rental_months`='$rental_months', `rental_years`='$rental_years', `reminder_alert`='$reminder_alert', `daily`='$daily', `weekly`='$weekly', `monthly`='$monthly', `annually`='$annually', `total_days`='$total_days', `total_hours`='$total_hours', `total_km`='$total_km', `total_miles`='$total_miles', `bill_of_material`='$bill_of_material', `include_in_so`='$include_in_so', `include_in_po`='$include_in_po', `include_in_pos`='$include_in_pos', `drum_unit_cost`= '$drum_unit_cost', `drum_unit_price`='$drum_unit_price', `tote_unit_cost`='$tote_unit_cost', `tote_unit_price`='$tote_unit_price', `include_in_product`='$include_in_product', `wcb_price`='$wcb_price',". $update_spec_sheet ." `featured`='$featured', `sale`='$on_sale', `clearance`='on_clearance', `new`='$new_item'". $update_main_image .", `item_sku` = '$item_sku', `color` = '$color', `suggested_retail_price` = '$suggested_retail_price', `rush_price` = '$rush_price', `min_amount` = '$min_amount', `max_amount` = '$max_amount' WHERE `part_no`='$part_no_old'";
                $result_update_inventory_led = mysqli_query($dbc_led, $query_update_inventory_led);
            }
        } else {
            //Else we sync the Inventory with the configured DB on database_connection.php More details in Admin Settings > Sync Inventory
            if ( $dbc_inventory ) {
                mysqli_query($dbc_inventory, $query_update_inventory);
            }
        }

            $before_change = '';
            $history = "Inventory with id $inventoryid has been Updated. <br />";
            add_update_history($dbc, 'inventory_history', $history, '', $before_change);

        //}

        // Update all SEA Software `code` & `part_no` - both the same. We do this only if the `part_no` is updated on SEA Alberta.
        if ( $sea_partno_edit==true && !empty($part_no_old) ) {
            $query_update_inventory = "UPDATE `inventory` SET `code`='$part_no', `gtin`='$gtin', `part_no`='$part_no' WHERE `part_no`='$part_no_old'";

            // Connect to each SEA Software as Cross Software doesn't work from SEA Alberta
            $dbc_global     = mysqli_connect('localhost', 'sea_software_use', 'dRagonflY!306', 'sea_devsoftware_db');
            $dbc_regina     = mysqli_connect('localhost', 'sea_software_use', 'dRagonflY!306', 'sea_regina_db');
            $dbc_saskatoon  = mysqli_connect('localhost', 'sea_software_use', 'dRagonflY!306', 'sea_saskatoon_db');
            $dbc_vancouver  = mysqli_connect('localhost', 'sea_software_use', 'dRagonflY!306', 'sea_vancouver_db');

            $result_update_inventory = mysqli_query ( $dbc, $query_update_inventory );
            $result_update_inventory = mysqli_query ( $dbc_global, $query_update_inventory );
            $result_update_inventory = mysqli_query ( $dbc_regina, $query_update_inventory );
            $result_update_inventory = mysqli_query ( $dbc_saskatoon, $query_update_inventory );
            $result_update_inventory = mysqli_query ( $dbc_vancouver, $query_update_inventory );

            mysqli_close($dbc_global);
            mysqli_close($dbc_regina);
            mysqli_close($dbc_saskatoon);
            mysqli_close($dbc_vancouver);
        }

        if ( !empty($additional_images) ) {
            for($i = 0; $i < count($_FILES['upload_additional_images']['name']); $i++) {
                $additional_image = $_FILES["upload_additional_images"]["name"][$i];
                $query_insert_images = "INSERT INTO `inventory_images` (`inventoryid`, `image`) VALUES ('$inventoryid', '$additional_image')";
                $result_insert_images = mysqli_query($dbc, $query_insert_images);
            }
        }

        $url = 'Updated';
    }

    if($include_in_product == 1) {
        $query_insert_invoice = "INSERT INTO `products` (`inventoryid`, `code`, `brand`, `category`, `sub_category`, `part_no`, `gst_exempt`, `description`, `application`, `supplimentary`, `comment`, `question`, `request`, `display_website`, `vendorid`, `size`, `gauge`, `weight`, `length`, `pressure`, `type`, `name`, `date_of_purchase`, `purchase_cost`, `sell_price`, `markup`, `freight_charge`, `min_bin`, `current_stock`, `final_retail_price`, `admin_price`, `wholesale_price`, `commercial_price`, `client_price`, `purchase_order_price`, `sales_order_price`, `distributor_price`, `minimum_billable`, `estimated_hours`, `actual_hours`, `msrp`, `quote_description`, `usd_invoice`, `shipping_rate`, `shipping_cash`, `exchange_rate`, `exchange_cash`, `cdn_cpu`, `cogs_total`, `location`, `inv_variance`, `average_cost`, `asset`, `revenue`, `buying_units`, `selling_units`, `stocking_units`, `preferred_price`, `web_price`, `clearance_price`, `id_number`, `operator`, `lsd`, `quantity`, `product_name`, `cost`, `usd_cpu`, `commission_price`, `markup_perc`, `current_inventory`, `write_offs`, `min_max`, `status`, `note`, `unit_price`, `unit_cost`, `rent_price`, `rental_days`, `rental_weeks`, `rental_months`, `rental_years`, `reminder_alert`, `daily`,`weekly`, `monthly`, `annually`,  `total_days`, `total_hours`, `total_km`, `total_miles`, `bill_of_material`, `include_in_so`,`include_in_po`,`include_in_pos`, `drum_unit_cost`, `drum_unit_price`, `tote_unit_cost`, `tote_unit_price`) SELECT inventoryid, code, brand, category, sub_category, part_no, gst_exempt, description, application, supplimentary, comment, question, request, display_website, vendorid, size, gauge, weight, length, pressure, type, name, date_of_purchase, purchase_cost, sell_price, markup, freight_charge, min_bin, current_stock, final_retail_price, admin_price, wholesale_price, commercial_price, client_price, purchase_order_price, sales_order_price, distributor_price, minimum_billable, estimated_hours, actual_hours, msrp, quote_description, usd_invoice, shipping_rate, shipping_cash, exchange_rate, exchange_cash, cdn_cpu, cogs_total, location, inv_variance, average_cost, asset, revenue, buying_units, selling_units, stocking_units, preferred_price, web_price, clearance_price, id_number, operator, lsd, quantity, product_name, cost, usd_cpu, commission_price, markup_perc, current_inventory, write_offs, min_max, status, note, unit_price, unit_cost, rent_price, rental_days, rental_weeks, rental_months, rental_years, reminder_alert, daily, weekly, monthly, annually, total_days, total_hours, total_km, total_miles, bill_of_material, include_in_so, include_in_po, include_in_pos, drum_unit_cost, drum_unit_price, tote_unit_cost, tote_unit_price from `inventory` WHERE `inventoryid`='$inventoryid'";
        $result_insert_invoice = mysqli_query($dbc, $query_insert_invoice);
        $before_change = '';
        $history = "New product Added. <br />";
		    add_update_history($dbc, 'inventory_history', $history, '', $before_change);
    } else {
        $query = mysqli_query($dbc,"DELETE FROM products WHERE `inventoryid`='$inventoryid'");
    }


    echo '<script type="text/javascript"> window.location.replace("inventory.php?category='.preg_replace('/[^a-z]/','',strtolower($category)).'"); </script>';

   // mysqli_close($dbc); //Close the DB Connection
}

?>
<script type="text/javascript" src="inventory.js"></script>
<script type="text/javascript">
$(document).ready(function () {

    $("#form1").submit(function( event ) {
        var category = $("#category").val();
        var sub_category = $("#sub_category").val();

        var code = $("input[name=code]").val();
        var name = $("input[name=name]").val();
        var category_name = $("input[name=category_name]").val();
        var sub_category_name = $("input[name=sub_category_name]").val();

        if (code == '' || category == '' || sub_category == '' || name == '') {
            //alert("Please make sure you have filled in all of the required fields.");
           // return false;
        }
        if(((category == 'Other') && (category_name == '')) || ((sub_category == 'Other') && (sub_category_name == ''))) {
            //alert("Please make sure you have filled in all of the required fields.");
            //return false;
        }
    });

    $("#category").change(function() {
        if($( "#category option:selected" ).text() == 'Other') {
                $( "#category_name" ).show();
        } else {
            $( "#category_name" ).hide();
        }
    });
    $("#size").change(function() {
        if($( "#size option:selected" ).text() == 'Other') {
                $( "#size_name" ).show();
        } else {
            $( "#size_name" ).hide();
        }
    });
    $("#weight").change(function() {
        if($( "#weight option:selected" ).text() == 'Other') {
                $( "#weight_name" ).show();
        } else {
            $( "#weight_name" ).hide();
        }
    });
    $("#sub_category").change(function() {
        if($( "#sub_category option:selected" ).text() == 'Other') {
                $( "#sub_category_name" ).show();
        } else {
            $( "#sub_category_name" ).hide();
        }
    });

    $('#add_additional_img').on( 'click', function () {
        var clone = $('.additional_img').clone();
        clone.find('.form-control').val('');
        clone.removeClass("additional_img");
        $('#add_additional_here').append(clone);
        return false;
    });

    if($('footer').length > 0) {
        scrollScreen();
    }
    $('.standard-body').scroll(scrollScreen);
});
$(document).on('change', 'select[name="sub_category"]', function() { selectSubCategory(this); });
$(document).on('change', 'select[name="category"]', function() { selectCategory(this); });
function selectCategory(sel) {
    window.location.href = '?category='+sel.value;
}
function scrollScreen() {
    $('.section_tab li').removeClass('active');
    var current_tab = [];
    $('.tab_section:visible').each(function() {
        if(this.getBoundingClientRect().top < $('.standard-body:visible').offset().top + $('.standard-body:visible').height() &&
            this.getBoundingClientRect().bottom > $('.standard-body:visible').offset().top) {
            current_tab.push($(this).data('tab'));
        }
    });
    current_tab.forEach(function(tab) {
        $('.section_tab[data-tab='+tab+'] li').addClass('active');
    });
}
function jumpTab(a) {
    $('.standard-body').last().scrollTop($('.tab_section[data-section='+$(a).data('tab')+']').last().offset().top + $('.standard-body').last().scrollTop() - $('.standard-body').last().offset().top);
    scrollScreen();
}
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('inventory');

if(!empty($_GET['iid'])) {
    echo '<input type="hidden" name="bill_of_material_hidden" value="'.$_GET['iid'].'" />';
} else {
    echo '<input type="hidden" name="bill_of_material_hidden" value="0" />';
}

$code = '';
$gtin = '';
$brand = '';
$category = $_GET['category'];
$sub_category = '';
$part_no = '';
$gst_exempt = '';
$description =  '';
$application =  '';
$supplimentary = '';
$comment = '';
$question = '';
$request = '';
$display_website = '';
$featured = '';
$on_sale = '';
$on_clearance = '';
$new_item = '';
$vendorid = '';
$size = '';
$gauge = '';
$weight = '';
$length = '';
$pressure = '';
$type   = '';
$name = '';
$name_on_website = '';
$date_of_purchase = '';
$purchase_cost = '';
$sell_price = '';
$markup = '';
$freight_charge = '';
$min_bin = '';
$current_stock = '';
$final_retail_price = '';
$admin_price = '';
$wholesale_price = '';
$commercial_price = '';
$client_price = '';
$purchase_order_price = '';
$sales_order_price = '';
$distributor_price = '';
$minimum_billable = '';
$estimated_hours = '';
$actual_hours = '';
$msrp = '';
$quote_description = '';

$id_number = '';
$operator = '';
$lsd = '';
$quantity = '';
$drum_unit_cost = '';
$drum_unit_price = '';
$tote_unit_cost = '';
$tote_unit_price = '';
$wcb_price = '';

$usd_invoice = '';
$shipping_rate =    '';
$shipping_cash =    '';
$exchange_rate =    '';
$exchange_cash =    '';
$pallet =   '';
$cdn_cpu =  '';
$cogs_total =   '';

$stocking_units = '';
$selling_units  = '';
$buying_units = '';
$warehouse = '';
$location = '';
$asset = '';
$revenue = '';
$inv_variance = '';
$web_price = '';
$clearance_price = '';
$average_cost = '';
$preferred_price = '';

$product_name = '';
$cost = '';
$usd_cpu = '';
$commission_price = '';
$markup_perc = '';
$current_inventory = '';
$write_offs = '';
$min_max = '';
$status = '';
$note = '';

$unit_price = '';
$unit_cost = '';
$rent_price = '';
$rental_days = '';
$rental_weeks = '';
$rental_months = '';
$rental_years = '';
$reminder_alert = '';
$daily = '';
$weekly = '';
$monthly = '';
$annually = '';
$total_days = '';
$total_hours = '';
$total_km = '';
$total_miles = '';
$include_in_po = '';
$include_in_so = '';
$include_in_pos = '';
$include_in_product = '';

$item_sku = '';
$color = '';
$suggested_retail_price = '';
$min_amount = '';
$max_amount = '';
$rush_price = '';
if(!empty($_GET['inventoryid'])) {

    $inventoryid = $_GET['inventoryid'];
    $get_inventory =    mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM inventory LEFT JOIN (SELECT `ticket_attached`.`item_id`, `tickets`.`businessid`, `tickets`.`clientid`, `tickets`.`purchase_order`, `tickets`.`ticket_label`, `ticket_attached`.`po_num`, `ticket_attached`.`po_line` FROM `ticket_attached` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `tickets`.`deleted`=0 AND `ticket_attached`.`deleted`=0 AND `src_table` IN ('inventory','inventory_detailed')) `tickets` ON `tickets`.`item_id`=`inventory`.`inventoryid` WHERE inventoryid='$inventoryid'"));
	if(!empty(MATCH_CONTACTS) && !empty(trim($get_inventory['clientid'].$get_inventory['businessid'],', ')) && !in_array($get_inventory['businessid'],explode(',',MATCH_CONTACTS)) && !in_array_any(array_filter(explode(',',$get_inventory['clientid'])),explode(',',MATCH_CONTACTS))) {
		ob_clean();
		header('Location: inventory.php');
		exit();
	}
    $code = $get_inventory['code'];
    $gtin = $get_inventory['gtin'];
    $brand = $get_inventory['brand'];
    //$category = $get_inventory['category'];
    $category = preg_replace('/[^a-z]/','',strtolower($get_inventory['category']));
    $actual_category_name = $get_inventory['category'];
    $sub_category   = $get_inventory['sub_category'];
    $part_no = $get_inventory['part_no'];
    $gst_exempt = $get_inventory['gst_exempt'];
    $description =  $get_inventory['description'];
    $application =  $get_inventory['application'];
    $supplimentary = $get_inventory['supplimentary'];
    $comment = $get_inventory['comment'];
    $question = $get_inventory['question'];
    $request = $get_inventory['request'];
    $display_website = $get_inventory['display_website'];
    $featured = $get_inventory['featured'];
    $on_sale = $get_inventory['sale'];
    $on_clearance = $get_inventory['clearance'];
    $new_item = $get_inventory['new'];
    $vendorid = $get_inventory['vendorid'];
    $size = $get_inventory['size'];
    $gauge = $get_inventory['gauge'];
    $weight = $get_inventory['weight'];
    $length = $get_inventory['length'];
    $pressure = $get_inventory['pressure'];
    $type = $get_inventory['type'];
    $name = $get_inventory['name'];
    $name_on_website = $get_inventory['name_on_website'];
    $date_of_purchase = $get_inventory['date_of_purchase'];
    $purchase_cost =    $get_inventory['purchase_cost'];
    $sell_price =   $get_inventory['sell_price'];
    $markup =   $get_inventory['markup'];
    $freight_charge =   $get_inventory['freight_charge'];
    $min_bin =  $get_inventory['min_bin'];
    $current_stock =    $get_inventory['current_stock'];
    $final_retail_price = $get_inventory['final_retail_price'];
    $admin_price = $get_inventory['admin_price'];
    $wholesale_price = $get_inventory['wholesale_price'];
    $commercial_price = $get_inventory['commercial_price'];
    $client_price = $get_inventory['client_price'];
    $purchase_order_price = $get_inventory['purchase_order_price'];
    $sales_order_price = $get_inventory['sales_order_price'];
    $distributor_price = $get_inventory['distributor_price'];
    $minimum_billable = $get_inventory['minimum_billable'];
    $estimated_hours = $get_inventory['estimated_hours'];
    $actual_hours = $get_inventory['actual_hours'];
    $msrp = $get_inventory['msrp'];
    $quote_description = $get_inventory['quote_description'];

    $id_number = $get_inventory['id_number'];
    $operator = $get_inventory['operator'];
    $lsd = $get_inventory['lsd'];
    $quantity = $get_inventory['quantity'];
    $drum_unit_cost = $get_inventory['drum_unit_cost'];
    $drum_unit_price = $get_inventory['drum_unit_price'];
    $tote_unit_cost = $get_inventory['tote_unit_cost'];
    $tote_unit_price = $get_inventory['tote_unit_price'];
    $wcb_price = $get_inventory['wcb_price'];

    $usd_invoice = $get_inventory['usd_invoice'];
    $shipping_rate =    $get_inventory['shipping_rate'];
    $shipping_cash =    $get_inventory['shipping_cash'];
    $exchange_rate =    $get_inventory['exchange_rate'];
    $exchange_cash =    $get_inventory['exchange_cash'];
    $pallet =   $get_inventory['pallet'];
    $cdn_cpu =  $get_inventory['cdn_cpu'];
    $cogs_total =   $get_inventory['cogs_total'];

    $stocking_units = $get_inventory['stocking_units'];
    $selling_units  = $get_inventory['selling_units'];
    $buying_units = $get_inventory['buying_units'];
    $location = $get_inventory['location'];
    $warehouse = $get_inventory['warehouse'];
    $asset = $get_inventory['asset'];
    $revenue = $get_inventory['revenue'];
    $inv_variance = $get_inventory['inv_variance'];
    $web_price = $get_inventory['web_price'];
    $clearance_price = $get_inventory['clearance_price'];
    $average_cost = $get_inventory['average_cost'];
    $preferred_price = $get_inventory['preferred_price'];
    $product_name = $get_inventory['product_name'];
    $cost = $get_inventory['cost'];
    $usd_cpu = $get_inventory['usd_cpu'];
    $commission_price = $get_inventory['commission_price'];
    $markup_perc = $get_inventory['markup_perc'];
    $current_inventory = $get_inventory['current_inventory'];
    $write_offs = $get_inventory['write_offs'];
    $min_max = $get_inventory['min_max'];
    $status = $get_inventory['status'];
    $note = $get_inventory['note'];

    $unit_price = $get_inventory['unit_price'];
    $unit_cost = $get_inventory['unit_cost'];
    $rent_price = $get_inventory['rent_price'];
    $rental_days = $get_inventory['rental_days'];
    $rental_weeks = $get_inventory['rental_weeks'];
    $rental_months = $get_inventory['rental_months'];
    $rental_years = $get_inventory['rental_years'];
    $reminder_alert = $get_inventory['reminder_alert'];
    $daily = $get_inventory['daily'];
    $weekly = $get_inventory['weekly'];
    $monthly = $get_inventory['monthly'];
    $annually = $get_inventory['annually'];
    $total_days = $get_inventory['total_days'];
    $total_hours = $get_inventory['total_hours'];
    $total_km = $get_inventory['total_km'];
    $total_miles = $get_inventory['total_miles'];

    $bill_of_material = $get_inventory['bill_of_material'];

    $include_in_po = $get_inventory['include_in_po'];
    $include_in_so = $get_inventory['include_in_so'];
    $include_in_pos = $get_inventory['include_in_pos'];
    $include_in_product = $get_inventory['include_in_product'];

    $item_sku = $get_inventory['item_sku'];
    $color = $get_inventory['color'];
    $suggested_retail_price = $get_inventory['suggested_retail_price'];
    $min_amount = $get_inventory['min_amount'];
    $max_amount = $get_inventory['max_amount'];
    $rush_price = $get_inventory['rush_price'];
} ?>
<div class="container" id="inventory_div">
  <div class="row">
        <div class="main-screen">
            <div class="tile-header standard-header">
                <?php include('../Inventory/tile_header.php'); ?>
            </div>

            <div class="tile-container" style="height: 100%;">
                <div class="standard-collapsible tile-sidebar set-section-height hide-titles-mob">
                    <ul class="sidebar" id="section_sidebar">
                        <?php $query_config = mysqli_query($dbc,"SELECT accordion, inventory FROM field_config_inventory WHERE    tab='".preg_replace('/[^a-z]/','',strtolower($category))."' AND accordion IS NOT NULL AND accordion != '' AND `order` IS NOT NULL ORDER BY `order`");
                        $previous_fields = '';
                        $j=0;
                        while($row = mysqli_fetch_array($query_config)) {
                            // Change General accordion name to Product Photos for prime.rookconnect.com
                            $accordion_title = '';
                            $rookconnect     = get_software_name();

                            if ( $rookconnect=='prime' && $row['accordion']=='General' ) {
                                $accordion_title = 'Product Photos';
                            } else {
                                $accordion_title = $row['accordion'];
                            } ?>
                            <a href="" onclick="jumpTab(this); return false;" class="section_tab" data-tab="<?= config_safe_str($row['accordion']) ?>" style="display: block;"><li><?= $accordion_title ?></li></a>
                        <?php } ?>
                    </ul>
                </div>

                <div class="scale-to-fill has-main-screen tile-content">
                    <div class="main-screen standard-body">
                        <div class="standard-body-title"><h3><?= $_GET['inventoryid'] > 0 ? 'Edit' : 'Add' ?> Inventory</h3></div>
                        <div class="standard-body-content pad-left pad-right pad-top">

                    		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
                                <input type="hidden" id="inventoryid"   name="inventoryid" value="<?php echo $inventoryid ?>" />

                    		<!-- <input type="hidden" id="category"	name="category" value="<?php echo $category ?>" /> -->

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Tab:</label>
                                <div class="col-sm-8">
                                    <select name="category" id="category" class="chosen-select-deselect form-control">
                                        <option></option>
                                        <?php $categories = explode('#*#', get_config($dbc, 'inventory_tabs'));

                                        foreach ($categories as $inv_tab) {
                                            if ( strtolower($category) == preg_replace('/[^a-z]/','',strtolower($inv_tab)) ) {
                                                $selected = 'selected';
                                                $actual_category_name = $inv_tab;
                                            } else {
                                                $selected = '';
                                            }
                                            //echo '<option value="'.$inv_tab.'" '.(strtolower($category) == strtolower($inv_tab) ? 'selected' : '').'>'.$inv_tab.'</option>';
                                            echo '<option value="'.preg_replace('/[^a-z]/','',strtolower($inv_tab)).'" data-category="'.$inv_tab.'" '.$selected.'>'.$inv_tab.'</option>';
                                        } ?>
                                    </select>
                                    <input type="hidden" name="actual_category_name" value="<?= $actual_category_name ?>" />
                                </div>
                            </div>

                            <?php
                            $query_config = mysqli_query($dbc,"SELECT accordion, inventory FROM field_config_inventory WHERE	tab='".preg_replace('/[^a-z]/','',strtolower($category))."' AND accordion IS NOT NULL AND accordion != '' AND `order` IS NOT NULL ORDER BY `order`");

                    		$previous_fields = '';
                            $j=0;
                            while($row = mysqli_fetch_array($query_config)) {
                                // Change General accordion name to Product Photos for prime.rookconnect.com
                                $accordion_title = '';
                                $rookconnect     = get_software_name();

                                if ( $rookconnect=='prime' && $row['accordion']=='General' ) {
                                    $accordion_title = 'Product Photos';
                                } else {
                                    $accordion_title = $row['accordion'];
                                } ?>
                                <div data-tab="<?= config_safe_str($row['accordion']) ?>" class="tab_section">
                                    <h4><?= $accordion_title ?></h4>
                                        <?php
                                        $accordion = $row['accordion'];
                                        $value_config = ','.$row['inventory'].',';

                    					foreach(explode(',',$value_config) as $field_name) {
                    						if(stripos($previous_fields, ','.$field_name.',') !== FALSE) {
                    							$value_config = str_replace(','.$field_name.',', ',', $value_config);
                    						}
                    					}
                    					$previous_fields .= $value_config;
                                        ?>

                                        <?php if (strpos($value_config, ','."Name".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">Name<span class="brand-color">*</span>:</label>
                                        <div class="col-sm-8">
                                          <input name="name" type="text" value="<?php echo $name; ?>" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Name On Website".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">Name On Website<span class="brand-color">*</span>:</label>
                                        <div class="col-sm-8">
                                          <input name="name_on_website" type="text" value="<?php echo $name_on_website; ?>" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Product Name".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">Product Name<span class="brand-color">*</span>:</label>
                                        <div class="col-sm-8">
                                          <input name="product_name" type="text" value="<?php echo $product_name; ?>" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Brand".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">Brand<span class="brand-color">*</span>:</label>
                                        <div class="col-sm-8">
                                          <input name="brand" type="text" value="<?php echo $brand; ?>" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <!-- <?php if (strpos($value_config, ','."Category".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="travel_task" class="col-sm-4 control-label">Tab<span class="brand-color">*</span>:</label>
                                        <div class="col-sm-8">
                                          <select id="category" name="category" class="chosen-select-deselect1 form-control" width="380">
                                          <option value=''></option>
                                          <?php
                                            $tabs = get_config($dbc, 'inventory_tabs');
                                            $each_tab = explode('#*#', $tabs);
                                            foreach ($each_tab as $cat_tab) {
                    							$url_tab = preg_replace('/[^a-z]/','',strtolower($cat_tab));
                                                if ($category == $url_tab) {
                                                    $selected = 'selected="selected"';
                                                } else {
                                                    $selected = '';
                                                }
                                                echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
                                            }
                                          ?>
                                          </select>
                                        </div>
                                        </div>
                                        <?php } ?> -->

                                        <?php if (strpos($value_config, ','."Subcategory".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="travel_task" class="col-sm-4 control-label">Subtab<span class="brand-color">*</span>:</label>
                                        <div class="col-sm-8">
                                          <select id="sub_category" name="sub_category" class="chosen-select-deselect form-control" width="380">
                                          <option value=''></option>
                                            <?php
                                                if ($sub_category == "15P 40 mil") { echo '<option selected value="15P 40 mil">15P 40 mil</option>'; }
                                                if ($sub_category == "15P 30 mil") { echo '<option selected value="15P 30 mil">15P 30 mil</option>'; }
                                                if ($sub_category == "13P 40 mil") { echo '<option selected value="13P 40 mil">13P 40 mil</option>'; }
                                                if ($sub_category == "13P 30 mil") { echo '<option selected value="13P 30 mil">13P 30 mil</option>'; }
                                                if ($sub_category == "10P 40 mil") { echo '<option selected value="10P 40 mil">10P 40 mil</option>'; }
                                                if ($sub_category == "10P 30 mil") { echo '<option selected value="10P 30 mil">10P 30 mil</option>'; }
                                                if ($sub_category == "15P 10oz") { echo '<option selected value="15P 10oz">15P 10oz</option>'; }
                                                if ($sub_category == "15P 6oz") { echo '<option selected value="15P 6oz">15P 6oz</option>'; }
                                                if ($sub_category == "13P 10oz") { echo '<option selected value="13P 10oz">13P 10oz</option>'; }
                                                if ($sub_category == "13P 6oz") { echo '<option selected value="13P 6oz">13P 6oz</option>'; }
                                                if ($sub_category == "10P 10oz") { echo '<option selected value="10P 10oz">10P 10oz</option>'; }
                                                if ($sub_category == "10P 6oz") { echo '<option selected value="10P 6oz">10P 6oz</option>'; }
                                                if ($sub_category == "15P") { echo '<option selected value="15P">15P</option>'; }
                                                if ($sub_category == "13P") { echo '<option selected value="13P">13P</option>'; }
                                                if ($sub_category == "10P") { echo '<option selected value="10P">10P</option>'; }

                                                $result1 = mysqli_query($dbc, "SELECT distinct(sub_category) FROM inventory WHERE sub_category NOT IN ('15P 40 mil','15P 30 mil','13P 40 mil','13P 30 mil','10P 40 mil','10P 30 mil','15P 10oz','15P 6oz','13P 10oz','13P 6oz','10P 10oz','10P 6oz','15P','13P','10P')");
                                                while($row1 = mysqli_fetch_assoc($result1)) {
                                                    if ($sub_category == $row1['sub_category']) {
                                                        $selected = 'selected="selected"';
                                                    } else {
                                                        $selected = '';
                                                    }
                                                    echo "<option ".$selected." value = '".$row1['sub_category']."'>".$row1['sub_category']."</option>";
                                                }
                                              ?>
                                              <option value = 'Other'>Other</option>
                                          </select>
                                        </div>
                                        </div>

                                        <div class="form-group">
                                        <label for="travel_task" class="col-sm-4 control-label"></label>
                                        <div class="col-sm-8">
                                            <input name="sub_category_name" id="sub_category_name" type="text" class="form-control" style="display: none;"/>
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Type".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="phone_number" class="col-sm-4 control-label">Tab:</label>
                                        <div class="col-sm-8">
                                            <select data-placeholder="Choose a Tab..." id="type" name="type" class="chosen-select-deselect form-control" width="380">
                                              <option value=""></option>
                                              <option <?php if ($type=='Project Inventory') echo 'selected="selected"';?> value="Project Inventory">Project Inventory</option>
                                              <option <?php if ($type=='Consumables') echo 'selected="selected"';?> value="Consumables">Consumables</option>
                                              <option <?php if ($type=='Inventory') echo 'selected="selected"';?> value="Inventory" >Inventory</option>
                                            </select>
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Color".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="phone_number" class="col-sm-4 control-label">Color:</label>
                                        <div class="col-sm-8">
                                          <input name="color" type="text" value="<?php echo $color; ?>" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Description".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="fax_number"	class="col-sm-4	control-label">Description:</label>
                                        <div class="col-sm-8">
                                            <textarea name="description" rows="5" cols="50" class="form-control"><?php echo $description; ?></textarea>
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ',Application,') !== FALSE) { ?>
                                            <div class="form-group">
                                                <label for="site_name" class="col-sm-4 control-label">Application<span class="brand-color">*</span>:</label>
                                                <div class="col-sm-8"><input name="application" type="text" value="<?php echo $application; ?>" class="form-control" /></div>
                                            </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Code".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">Code<span class="brand-color">*</span>:</label>
                                        <div class="col-sm-8">
                                          <input name="code" type="text" value="<?php echo $code; ?>" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."ID #".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">ID #:</label>
                                        <div class="col-sm-8">
                                          <input name="id_number" type="text" value="<?php echo $id_number; ?>" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Item SKU".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">Item SKU:</label>
                                        <div class="col-sm-8">
                                          <input name="item_sku" type="text" value="<?php echo $item_sku; ?>" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Part #".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="fax_number"	class="col-sm-4	control-label">Part #:</label>
                                        <div class="col-sm-8">
                                            <input name="part_no" type="text" value="<?php echo $part_no; ?>" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."GTIN".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="fax_number"	class="col-sm-4	control-label">GTIN:</label>
                                        <div class="col-sm-8">
                                            <input name="gtin" type="text" value="<?php echo $gtin; ?>" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."GST Exempt".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">GST Exempt:</label>
                                        <div class="col-sm-8">
                                            <input type="checkbox" <?php if ($gst_exempt == '1') { echo " checked"; } ?> value="1" style="height: 20px; width: 20px;" name="gst_exempt">
                                        </div>
                                      </div>
                                      <?php }

                    					/* Remove Kristi's (SEA) access to Product Costs */
                    					if ( $rookconnect == 'sea' && isset ( $_SESSION['user_name'] ) && $_SESSION['user_name'] == 'kristi' ) {
                    						// Show nothing
                    					} else {
                    						if (strpos($value_config, ','."Cost".',') !== FALSE) { ?>
                    							<div class="form-group">
                    								<label for="fax_number"	class="col-sm-4	control-label">Cost:</label>
                    								<div class="col-sm-8">
                    									<input name="cost" type="text" value="<?php echo $cost; ?>" class="form-control"/>
                    								</div>
                    							</div><?php
                    						}

                    						if (strpos($value_config, ','."CDN Cost Per Unit".',') !== FALSE) { ?>
                    							<div class="form-group">
                    								<label for="site_name" class="col-sm-4 control-label">CDN Cost Per Unit:</label>
                    								<div class="col-sm-8">
                    									<input name="cdn_cpu" type="text" id="cpu"	value="<?php echo $cdn_cpu; ?>" class="form-control" />
                    								</div>
                    							</div><?php
                    						}

                    						if (strpos($value_config, ','."USD Cost Per Unit".',') !== FALSE) { ?>
                    							<div class="form-group">
                    								<label for="site_name" class="col-sm-4 control-label">USD Cost Per Unit:</label>
                    								<div class="col-sm-8">
                    									<input name="usd_cpu" type="text" id="cpu"	value="<?php echo $usd_cpu; ?>" class="form-control" />
                    								</div>
                    							</div><?php
                    						}

                    						if (strpos($value_config, ','."Average Cost".',') !== FALSE) { ?>
                    							<div class="form-group">
                    								<label for="site_name" class="col-sm-4 control-label">Average Cost:</label>
                    								<div class="col-sm-8">
                    									<input name="average_cost" type="text" value="<?php echo $average_cost; ?>" class="form-control" />
                    								</div>
                    							</div><?php
                    						}

                    						if (strpos($value_config, ','."Purchase Cost".',') !== FALSE) { ?>
                    							<div class="form-group">
                    								<label for="fax_number"	class="col-sm-4	control-label">Purchase Cost:</label>
                    								<div class="col-sm-8">
                    									<input name="purchase_cost" type="text" value="<?php echo $purchase_cost; ?>" class="form-control"/>
                    								</div>
                    							</div><?php
                    						}

                    						if (strpos($value_config, ','."USD Invoice".',') !== FALSE) { ?>
                    							<div class="form-group">
                    								<label for="site_name" class="col-sm-4 control-label">USD Invoice:</label>
                    								<div class="col-sm-8">
                    									<input name="usd_invoice" type="text" id="usdinvoice" value="<?php echo $usd_invoice; ?>" class="form-control" />
                    								</div>
                    							</div><?php
                    						}
                    					} ?>

                                        <?php if (strpos($value_config, ','."COGS".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">COGS GL Code:</label>
                                        <div class="col-sm-8">
                                          <input name="cogs_total" type="text"	id="cogs" value="<?php echo $cogs_total; ?>" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                          <?php if (strpos($value_config, ','."Vendor".',') !== FALSE) { ?>
                                          <div class="form-group">
                                            <label for="fax_number"	class="col-sm-4	control-label">Vendor:</label>
                                            <div class="col-sm-8">
                                                <select data-placeholder="Choose a Vendor..." id="vendor" name="vendorid" class="chosen-select-deselect form-control" width="380">
                                                  <option value=""></option>
                    							  <?php
                    								$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Vendor' AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
                    								foreach($query as $id) {
                    									$selected = '';
                    									$selected = $id == $vendorid ? 'selected = "selected"' : '';
                    									echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id,'name').'</option>';
                    								}
                    							  ?>
                                                </select>
                                            </div>
                                          </div>
                                          <?php } ?>

                                        <?php if (strpos($value_config, ','."Date of Purchase".',') !== FALSE) { ?>
                                        <div class="form-group clearfix completion_date">
                                            <label for="first_name" class="col-sm-4 control-label text-right">Date of Purchase:</label>
                                            <div class="col-sm-8">
                                                <input name="date_of_purchase" value="<?php echo $date_of_purchase; ?>" type="text" class="datepicker"></p>
                                            </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Shipping Rate".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">Shipping Rate:</label>
                                        <div class="col-sm-8">
                                          <input name="shipping_rate" type="text" id='ship_rate_'	value="<?php echo $shipping_rate; ?>" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Shipping Cash".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">Shipping Cash:</label>
                                        <div class="col-sm-8">
                                          <input name="shipping_cash" type="text" value="<?php echo $shipping_cash; ?>" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Freight Charge".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="fax_number"	class="col-sm-4	control-label">Freight Charge:</label>
                                        <div class="col-sm-8">
                                          <input name="freight_charge" type="text" value="<?php echo $freight_charge; ?>" class="form-control"/>
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Exchange Rate".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">Exchange Rate:</label>
                                        <div class="col-sm-8">
                                          <input name="exchange_rate" type="text" id="exchangerate"	value="<?php echo $exchange_rate; ?>" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Exchange $".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">Exchange $:</label>
                                        <div class="col-sm-8">
                                          <input name="exchange_cash" type="text"	id="exchangecash" value="<?php echo $exchange_cash; ?>" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Pallet Num".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">Pallet #:</label>
                                        <div class="col-sm-8">
                                          <select class="chosen-select-deselect" data-placeholder="Select Pallet #" name="pallet" onchange="if(this.value == 'ADD_NEW') { $('#pallet_new').prop('name','pallet').closest('div').show(); $(this).closest('div').hide(); }"><option />
                    						<?php $pallet_nums = $dbc->query("SELECT `pallet` FROM `inventory` WHERE `deleted`=0 AND IFNULL(`pallet`,'') != '' GROUP BY `pallet` ORDER BY `pallet`");
                    						while($pallet_num = $pallet_nums->fetch_assoc()) { ?>
                    							<option <?= $pallet_num['pallet'] == $pallet ? 'selected' : '' ?> value="<?= $pallet_num['pallet'] ?>"><?= $pallet_num['pallet'] ?></option>
                    						<?php } ?>
                    						<option value="ADD_NEW">New Pallet #</option>
                    					  </select>
                    					</div>
                    					<div class="col-sm-8" style="display:none;">
                    					  <input name="pallet_new" type="text"	id="pallet_new" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Purchase Order".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="fax_number"	class="col-sm-4	control-label">Purchase Order #:</label>
                                        <div class="col-sm-8">
											<?php if($get_inventory['po_num'] != '') {
												echo $get_inventory['po_num'];
											} else {
												echo implode('<br />',array_filter(explode('#*#',$get_inventory['purchase_order'])));
											} ?>
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."PO Line".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="fax_number"	class="col-sm-4	control-label">PO Line Item #:</label>
                                        <div class="col-sm-8">
                                          <?= $get_inventory['po_line'] ?>
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Sell Price".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="fax_number"	class="col-sm-4	control-label">Sell Price:</label>
                                        <div class="col-sm-8">
                                          <input name="sell_price" type="text" value="<?php echo $sell_price; ?>" class="form-control"/>
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Final Retail Price:</label>
                                        <div class="col-sm-8">
                                          <input name="final_retail_price" value="<?php echo $final_retail_price; ?>" type="text" class="form-control">
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Wholesale Price:</label>
                                        <div class="col-sm-8">
                                          <input name="wholesale_price" value="<?php echo $wholesale_price; ?>" type="text" class="form-control">
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Commercial Price:</label>
                                        <div class="col-sm-8">
                                          <input name="commercial_price" value="<?php echo $commercial_price; ?>" type="text" class="form-control">
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Client Price".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Client Price:</label>
                                        <div class="col-sm-8">
                                          <input name="client_price" value="<?php echo $client_price; ?>" type="text" class="form-control">
                                        </div>
                                        </div>
                                        <?php } ?>

                    					<?php if (strpos($value_config, ','."Purchase Order Price".',') !== FALSE) { ?>
                    					  <div class="form-group">
                    						<label for="company_name" class="col-sm-4 control-label">Purchase Order Price:</label>
                    						<div class="col-sm-8">
                    						  <input name="purchase_order_price" value="<?php echo $purchase_order_price; ?>" type="text" class="form-control">
                    						</div>
                    					  </div>
                    					  <?php } ?>

                    							<?php if (strpos($value_config, ','."Sales Order Price".',') !== FALSE) { ?>
                    					  <div class="form-group">
                    						<label for="company_name" class="col-sm-4 control-label"><?= SALES_ORDER_NOUN ?> Price:</label>
                    						<div class="col-sm-8">
                    						  <input name="sales_order_price" value="<?php echo $sales_order_price; ?>" type="text" class="form-control">
                    						</div>
                    					  </div>
                    					  <?php } ?>

                                        <?php if (strpos($value_config, ','."Suggested Retail Price".',') !== FALSE) { ?>
                                          <div class="form-group">
                                            <label for="company_name" class="col-sm-4 control-label">Suggested Retail Price:</label>
                                            <div class="col-sm-8">
                                              <input name="suggested_retail_price" value="<?php echo $suggested_retail_price; ?>" type="text" class="form-control">
                                            </div>
                                          </div>
                                          <?php } ?>

                                        <?php if (strpos($value_config, ','."Rush Price".',') !== FALSE) { ?>
                                          <div class="form-group">
                                            <label for="company_name" class="col-sm-4 control-label">Rush Price:</label>
                                            <div class="col-sm-8">
                                              <input name="rush_price" value="<?php echo $rush_price; ?>" type="text" class="form-control">
                                            </div>
                                          </div>
                                          <?php } ?>

                                        <?php if (strpos($value_config, ','."Distributor Price".',') !== FALSE) { ?>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Distributor Price:</label>
                                                <div class="col-sm-8"><input name="distributor_price" value="<?php echo $distributor_price; ?>" type="text" class="form-control"></div>
                                            </div>
                                        <?php } ?>

                    					<?php if (strpos($value_config, ','."Include in Sales Orders".',') !== FALSE) { ?>
                    						  <div class="form-group">
                    							<label for="company_name" class="col-sm-4 control-label">Include in <?= SALES_ORDER_TILE ?>:</label>
                    							<div class="col-sm-8">
                    							  <input type='checkbox' style='width:20px; height:20px;' <?php if($include_in_so !== '' && $include_in_so !== NULL) { echo "checked"; } ?> name='include_in_so' class='' value='1'>
                    							</div>
                    						  </div>
                    						  <?php } ?>

                    						  <?php if (strpos($value_config, ','."Include in Purchase Orders".',') !== FALSE) { ?>
                    						  <div class="form-group">
                    							<label for="company_name" class="col-sm-4 control-label">Include in Purchase Orders:</label>
                    							<div class="col-sm-8">
                    							  <input type='checkbox' style='width:20px; height:20px;' <?php if($include_in_po !== '' && $include_in_po !== NULL) { echo "checked"; } ?> name='include_in_po' class='' value='1'>
                    							</div>
                    						  </div>
                    						  <?php } ?>

                    						  <?php if (strpos($value_config, ','."Include in P.O.S.".',') !== FALSE) { ?>
                    						  <div class="form-group">
                    							<label for="company_name" class="col-sm-4 control-label">Include in Point of Sale:</label>
                    							<div class="col-sm-8">
                    							  <input type='checkbox' style='width:20px; height:20px;' <?php if($include_in_pos !== '' && $include_in_pos !== NULL) { echo "checked"; } ?> name='include_in_pos' class='' value='1'>
                    							</div>
                    						  </div>
                    						  <?php } ?>

                    						  <?php if (strpos($value_config, ','."Include in Product".',') !== FALSE) { ?>
                    						  <div class="form-group">
                    							<label for="company_name" class="col-sm-4 control-label">Include in Product:</label>
                    							<div class="col-sm-8">
                    							  <input type='checkbox' style='width:20px; height:20px;' <?php if($include_in_product !== '' && $include_in_product !== NULL && $include_in_product == 1) { echo "checked"; } ?> name='include_in_product' class='' value='1'>
                    							</div>
                    						  </div>
                    						  <?php } ?>

                                        <?php if (strpos($value_config, ','."Preferred Price".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">Preferred Price</label>
                                        <div class="col-sm-8">
                                          <input name="preferred_price" type="text" id="preferred_price" value="<?php echo $preferred_price; ?>" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Admin Price:</label>
                                        <div class="col-sm-8">
                                          <input name="admin_price" value="<?php echo $admin_price; ?>" type="text" class="form-control">
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Web Price".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">Web Price:</label>
                                        <div class="col-sm-8">
                                          <input name="web_price" type="text" id="web_price" value="<?php echo $web_price; ?>" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Clearance Price".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">Clearance Price:</label>
                                        <div class="col-sm-8">
                                          <input name="clearance_price" type="text" id="clearance_price" value="<?php echo $clearance_price; ?>" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Commission Price".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">Commission Price:</label>
                                        <div class="col-sm-8">
                                          <input name="commission_price" type="text" id="commission_price" value="<?php echo $commission_price; ?>" class="form-control" />
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."MSRP".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">MSRP:</label>
                                        <div class="col-sm-8">
                                          <input name="msrp" value="<?php echo $msrp; ?>" type="text" class="form-control">
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Unit Price".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Unit Price:</label>
                                        <div class="col-sm-8">
                                          <input name="unit_price" value="<?php echo $unit_price; ?>" type="text" class="form-control">
                                        </div>
                                        </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Unit Cost".',') !== FALSE) { ?>
                                        <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Unit Cost:</label>
                                        <div class="col-sm-8">
                                          <input name="unit_cost" value="<?php echo $unit_cost; ?>" type="text" class="form-control">
                                        </div>
                                        </div>
                                        <?php } ?>

                                            <?php if (strpos($value_config, ','."Markup By $".',') !== FALSE) { ?>
                                            <div class="form-group">
                                            <label for="fax_number"	class="col-sm-4	control-label">Markup By $:</label>
                                            <div class="col-sm-8">
                                              <input name="markup" type="text" value="<?php echo $markup; ?>" class="form-control"/>
                                            </div>
                                            </div>
                                            <?php } ?>

                                            <?php if (strpos($value_config, ','."Markup By %".',') !== FALSE) { ?>
                                            <div class="form-group">
                                            <label for="fax_number"	class="col-sm-4	control-label">Markup By %:</label>
                                            <div class="col-sm-8">
                                              <input name="markup_perc" type="text" value="<?php echo $markup_perc; ?>" class="form-control"/>
                                            </div>
                                            </div>
                                            <?php } ?>

                                            <?php if (strpos($value_config, ','."GL Revenue".',') !== FALSE) { ?>
                                            <div class="form-group">
                                            <label for="fax_number"	class="col-sm-4	control-label">GL Revenue:</label>
                                            <div class="col-sm-8">
                                              <input name="revenue" type="text" value="<?php echo $revenue; ?>" class="form-control"/>
                                            </div>
                                            </div>
                                            <?php } ?>

                                            <?php if (strpos($value_config, ','."GL Assets".',') !== FALSE) { ?>
                                            <div class="form-group">
                                            <label for="fax_number"	class="col-sm-4	control-label">GL Assets:</label>
                                            <div class="col-sm-8">
                                              <input name="asset" type="text" value="<?php echo $asset; ?>" class="form-control"/>
                                            </div>
                                            </div>
                                            <?php } ?>

                                            <?php if (strpos($value_config, ','."Current Stock".',') !== FALSE) { ?>
                                            <div class="form-group">
                                            <label for="fax_number"	class="col-sm-4	control-label">Current Stock:</label>
                                            <div class="col-sm-8">
                                              <input name="current_stock" type="text" value="<?php echo $current_stock; ?>" class="form-control"/>
                                            </div>
                                            </div>
                                            <?php } ?>

                                            <?php if (strpos($value_config, ','."Current Inventory".',') !== FALSE) { ?>
                                            <div class="form-group">
                                            <label for="fax_number"	class="col-sm-4	control-label">Current Inventory:</label>
                                            <div class="col-sm-8">
                                              <input name="current_inventory" type="text" value="<?php echo $current_inventory; ?>" class="form-control"/>
                                            </div>
                                            </div>
                                            <?php } ?>

                                            <?php if (strpos($value_config, ','."Quantity".',') !== FALSE) { ?>
                                            <div class="form-group">
                                            <label for="site_name" class="col-sm-4 control-label">Quantity:</label>
                                            <div class="col-sm-8">
                                              <input name="quantity" type="text" value="<?php echo $quantity; ?>" class="form-control" />
                                            </div>
                                            </div>
                                            <?php } ?>

                                            <?php if (strpos($value_config, ','."Variance".',') !== FALSE) { ?>
                                            <div class="form-group">
                                            <label for="site_name" class="col-sm-4 control-label">GL Code:</label>
                                            <div class="col-sm-8">
                                              <input name="inv_variance" type="text" id="inv_variance" value="<?php echo $inv_variance; ?>" class="form-control" />
                                            </div>
                                            </div>
                                            <?php } ?>

                                            <?php if (strpos($value_config, ','."Write-offs".',') !== FALSE) { ?>
                                            <div class="form-group">
                                            <label for="site_name" class="col-sm-4 control-label">Write-offs:</label>
                                            <div class="col-sm-8">
                                              <input name="write_offs" type="text" value="<?php echo $write_offs; ?>" class="form-control" />
                                            </div>
                                            </div>
                                            <?php } ?>

                                            <?php if (strpos($value_config, ','."Buying Units".',') !== FALSE) { ?>
                                            <div class="form-group">
                                            <label for="site_name" class="col-sm-4 control-label">Buying Units:</label>
                                            <div class="col-sm-8">
                                              <input name="buying_units" type="text" value="<?php echo $buying_units; ?>" class="form-control" />
                                            </div>
                                            </div>
                                            <?php } ?>

                                            <?php if (strpos($value_config, ','."Selling Units".',') !== FALSE) { ?>
                                            <div class="form-group">
                                            <label for="site_name" class="col-sm-4 control-label">Selling Units:</label>
                                            <div class="col-sm-8">
                                              <input name="selling_units" type="text" value="<?php echo $selling_units; ?>" class="form-control" />
                                            </div>
                                            </div>
                                            <?php } ?>

                                            <?php if (strpos($value_config, ','."Stocking Units".',') !== FALSE) { ?>
                                            <div class="form-group">
                                            <label for="site_name" class="col-sm-4 control-label">Stocking Units:</label>
                                            <div class="col-sm-8">
                                              <input name="stocking_units" type="text" value="<?php echo $stocking_units; ?>" class="form-control" />
                                            </div>
                                            </div>
                                            <?php } ?>

                                      <?php if (strpos($value_config, ','."Location".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">Location:</label>
                                        <div class="col-sm-8">
                                            <!-- <select data-placeholder="Choose a Location..." name="location" class="chosen-select-deselect form-control" width="380">
                                              <option value=""></option>
                                              <option <?php if ($location == "Sundre") { echo " selected"; } ?> value="Sundre">Sundre</option>
                                              <option <?php if ($location == "Edson") { echo " selected"; } ?> value="Edson">Edson</option>
                                              <option <?php if ($location == "Grande Prairie") { echo " selected"; } ?> value="Grande Prairie">Grande Prairie</option>
                                              <option <?php if ($location == "Other") { echo " selected"; } ?> value="Other">Other</option>
                                            </select>
                                            -->
                                            <input name="location" type="text" value="<?php echo $location; ?>" class="form-control" />
                                        </div>
                                      </div>
                                      <?php } ?>
                                      <?php if (strpos($value_config, ','."Warehouse".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">Warehouse Location:</label>
                                        <div class="col-sm-8">
                                            <select data-placeholder="Select a Warehouse..." name="warehouse" class="chosen-select-deselect form-control">
                                              <option value=""></option>
                    						  <?php foreach(sort_contacts_query($dbc->query()) as $warehouse_name) { ?>
                    							  <option <?= ($warehouse == $warehouse_row['contactid'] ? 'selected' : '') ?> value="<?= $warehouse_row['contactid'] ?>"><?= $warehouse_row['name'] ?></option>
                    						  <?php } ?>
                                            </select>
                                        </div>
                                      </div>
                                      <?php } ?>
                                      <?php if (strpos($value_config, ','."LSD".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">LSD:</label>
                                        <div class="col-sm-8">
                                          <input name="lsd" type="text" value="<?php echo $lsd; ?>" class="form-control" />
                                        </div>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."Size".',') !== FALSE) {
                                      ?>
                                        <div class="form-group">
                                        <label for="travel_task" class="col-sm-4 control-label">Size:</label>
                                        <div class="col-sm-8">
                                          <select id="size" name="size" class="chosen-select-deselect form-control" width="380">
                                          <option value=''></option>
                                            <option <?php if ($size == "198&#39; x 198&#39;") { echo " selected"; } ?> value = "198' x 198'">198' x 198'</option>
                                            <option <?php if ($size == "176&#39; x 176&#39;") { echo " selected"; } ?> value = "176' x 176'">176' x 176'</option>
                                            <option <?php if ($size == "144&#39; x 144&#39;") { echo " selected"; } ?> value = "144' x 144'">144' x 144'</option>
                                            <option <?php if ($size == "168&#39; x 168&#39;") { echo " selected"; } ?> value = "168' x 168'">168' x 168'</option>
                                            <option <?php if ($size == "145&#39; x 145&#39;") { echo " selected"; } ?> value = "145' x 145'">145' x 145'</option>
                                            <option <?php if ($size == "116&#39; x 116&#39;") { echo " selected"; } ?> value = "116' x 116'">116' x 116'</option>
                                            <option <?php if ($size == "157&#39;") { echo " selected"; } ?> value = "157'">157'</option>
                                            <option <?php if ($size == "136&#39;") { echo " selected"; } ?> value = "136'">136'</option>
                                            <option <?php if ($size == "105&#39;") { echo " selected"; } ?> value = "105'">105'</option>
                                            <option <?php if ($size == "165&#39;") { echo " selected"; } ?> value = "165'">165'</option>
                                            <option <?php if ($size == "145&#39;") { echo " selected"; } ?> value = "145'">145'</option>
                                            <option <?php if ($size == "115&#39;") { echo " selected"; } ?> value = "115'">115'</option>
                                              <?php
                                                $result3 = mysqli_query($dbc, "SELECT distinct(size) FROM inventory WHERE size NOT IN ('176&#39; x 176&#39;','198&#39; x 198&#39;','144&#39; x 144&#39;','168&#39; x 168&#39;','145&#39; x 145&#39;','116&#39; x 116&#39;','157&#39;','136&#39;','105&#39;','165&#39;','145&#39;','115&#39;')");
                                                while($row3 = mysqli_fetch_assoc($result3)) {
                                                    if ($size == $row3['size']) {
                                                        $selected = 'selected="selected"';
                                                    } else {
                                                        $selected = '';
                                                    }
                                                    echo "<option ".$selected." value = '".$row3['size']."'>".$row3['size']."</option>";
                                                }
                                              ?>
                                              <option value = 'Other'>Other</option>
                                          </select>
                                        </div>
                                        </div>

                                        <div class="form-group">
                                        <label for="travel_task" class="col-sm-4 control-label"></label>
                                        <div class="col-sm-8">
                                            <input name="size_name" id="size_name" type="text" class="form-control" style="display: none;"/>
                                        </div>
                                        </div>

                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."Weight".',') !== FALSE) { ?>
                                        <div class="form-group">
                                            <label for="travel_task" class="col-sm-4 control-label">Weight:</label>
                                            <div class="col-sm-8"><input name="weight" type="text" value="<?= $weight; ?>" class="form-control" /></div>

                                            <!--
                                            OLD Dropdown
                                            <div class="col-sm-8">


                                            <select id="weight" name="weight" class="chosen-select-deselect form-control" width="380">
                                            <option value=''></option>
                                            <option <?php //if ($weight == "7000lbs") { echo " selected"; } ?> value = "7000lbs">7000lbs</option>
                                            <option <?php //if ($weight == "5075lbs") { echo " selected"; } ?> value = "5075lbs">5075lbs</option>
                                            <option <?php //if ($weight == "5560lbs") { echo " selected"; } ?> value = "5560lbs">5560lbs</option>
                                            <option <?php //if ($weight == "4030lbs") { echo " selected"; } ?> value = "4030lbs">4030lbs</option>
                                            <option <?php //if ($weight == "3890lbs") { echo " selected"; } ?> value = "3890lbs">3890lbs</option>
                                            <option <?php //if ($weight == "2842lbs") { echo " selected"; } ?> value = "2842lbs">2842lbs</option>
                                            <option <?php //if ($weight == "2070lbs") { echo " selected"; } ?> value = "2070lbs">2070lbs</option>
                                            <option <?php //if ($weight == "1200lbs") { echo " selected"; } ?> value = "1200lbs">1200lbs</option>
                                            <option <?php //if ($weight == "1600lbs") { echo " selected"; } ?> value = "1600lbs">1600lbs</option>
                                            <option <?php //if ($weight == "TBC") { echo " selected"; } ?> value = "TBC">TBC</option>
                                            <option <?php //if ($weight == "1005lbs") { echo " selected"; } ?> value = "1005lbs">1005lbs</option>
                                            <option <?php //if ($weight == "1100lbs") { echo " selected"; } ?> value = "1100lbs">1100lbs</option>
                                            <option <?php //if ($weight == "750lbs") { echo " selected"; } ?> value = "750lbs">750lbs</option>
                                            <option <?php //if ($weight == "500lbs") { echo " selected"; } ?> value = "500lbs">500lbs</option>
                                            <?php
                                            /*
                                            $result4 = mysqli_query($dbc, "SELECT distinct(weight) FROM inventory WHERE weight NOT IN ('7000lbs', '5075lbs','5560lbs','4030lbs','3890lbs','2842lbs','2070lbs','1200lbs','1600lbs','TBC','1005lbs','1100lbs','750lbs','500lbs')");
                                            while($row4 = mysqli_fetch_assoc($result4)) {
                                            if ($weight == $row4['weight']) {
                                            $selected = 'selected="selected"';
                                            } else {
                                            $selected = '';
                                            }
                                            echo "<option ".$selected." value = '".$row4['weight']."'>".$row4['weight']."</option>";
                                            }
                                            */
                                            ?>
                                            <option value = 'Other'>Other</option>
                                            </select>
                                            </div>
                                            -->
                                        </div>

                                        <!--
                                        OLD weight other
                                        <div class="form-group">
                                        <label for="travel_task" class="col-sm-4 control-label"></label>
                                        <div class="col-sm-8">
                                            <input name="weight_name" id="weight_name" type="text" class="form-control" style="display: none;"/>
                                        </div>
                                        </div>
                                        -->

                                      <?php } ?>

                                    <?php if (strpos($value_config, ',Gauge,') !== FALSE) { ?>
                                        <div class="form-group">
                                            <label for="gauge"	class="col-sm-4	control-label">Gauge:<br /><small>Maximum 15 characters</small></label>
                                            <div class="col-sm-8"><input name="gauge" type="text" value="<?= $gauge; ?>" class="form-control" maxlength="15" /></div>
                                        </div>
                                    <?php } ?>

                                    <?php if (strpos($value_config, ',Length,') !== FALSE) { ?>
                                        <div class="form-group">
                                            <label for="length"	class="col-sm-4	control-label">Length:<br /><small>Maximum 15 characters</small></label>
                                            <div class="col-sm-8"><input name="length" type="text" value="<?= $length; ?>" class="form-control" maxlength="15" /></div>
                                        </div>
                                    <?php } ?>

                                    <?php if (strpos($value_config, ',Pressure,') !== FALSE) { ?>
                                        <div class="form-group">
                                            <label for="pressure"	class="col-sm-4	control-label">Pressure:<br /><small>Maximum 15 characters</small></label>
                                            <div class="col-sm-8"><input name="pressure" type="text" value="<?= $pressure; ?>" class="form-control" maxlength="15" /></div>
                                        </div>
                                    <?php } ?>

                                       <?php if (strpos($value_config, ','."Min Amount".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="fax_number" class="col-sm-4 control-label">Min Amount:</label>
                                        <div class="col-sm-8">
                                          <input name="min_amount" type="text" value="<?php echo $min_amount; ?>" class="form-control"/>
                                        </div>
                                      </div>
                                      <?php } ?>

                                       <?php if (strpos($value_config, ','."Max Amount".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="fax_number" class="col-sm-4 control-label">Max Amount:</label>
                                        <div class="col-sm-8">
                                          <input name="max_amount" type="text" value="<?php echo $max_amount; ?>" class="form-control"/>
                                        </div>
                                      </div>
                                      <?php } ?>

                                       <?php if (strpos($value_config, ','."Min Max".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="fax_number"	class="col-sm-4	control-label">Min Max:</label>
                                        <div class="col-sm-8">
                                          <input name="min_max" type="text" value="<?php echo $min_max; ?>" class="form-control"/>
                                        </div>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."Min Bin".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="fax_number"	class="col-sm-4	control-label">Min Bin:</label>
                                        <div class="col-sm-8">
                                          <input name="min_bin" type="text" value="<?php echo $min_bin; ?>" class="form-control"/>
                                        </div>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Estimated Hours:</label>
                                        <div class="col-sm-8">
                                          <input name="estimated_hours" value="<?php echo $estimated_hours; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."Actual Hours".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Actual Hours:</label>
                                        <div class="col-sm-8">
                                          <input name="actual_hours" value="<?php echo $actual_hours; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Minimum Billable:</label>
                                        <div class="col-sm-8">
                                          <input name="minimum_billable" value="<?php echo $minimum_billable; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>

                                       <?php if (strpos($value_config, ','."Quote Description".',') !== FALSE) { ?>
                                       <div class="form-group">
                                        <label for="first_name[]" class="col-sm-4 control-label">Quote Description:</label>
                                        <div class="col-sm-8">
                                          <textarea name="quote_description" rows="5" cols="50" class="form-control"><?php echo $quote_description; ?></textarea>
                                        </div>
                                      </div>
                                      <?php } ?>

                                       <?php if (strpos($value_config, ','."Status".',') !== FALSE) { ?>
                                       <div class="form-group">
                                        <label for="site_name" class="col-sm-4 control-label">Status:</label>
                                        <div class="col-sm-8">

                                            <select data-placeholder="Choose a Status..." name="status" class="chosen-select-deselect form-control" width="380">
                                              <option value=""></option>
                                              <option <?php if ($status == "Active") { echo " selected"; } ?> value="Active">Active</option>
                                              <option <?php if ($status == "Inactive") { echo " selected"; } ?> value="Inactive">Inactive</option>
                                              <option <?php if ($status == "In inventory") { echo " selected"; } ?> value="In inventory">In inventory</option>
                                              <option <?php if ($status == "In transit from vendor") { echo " selected"; } ?> value="In transit from vendor">In transit from vendor</option>
                                              <option <?php if ($status == "In transit between yards") { echo " selected"; } ?> value="In transit between yards">In transit between yards</option>
                                              <option <?php if ($status == "Not confirmed in yard by inventory check") { echo " selected"; } ?> value="Not confirmed in yard by inventory check">Not confirmed in yard by inventory check</option>
                                              <option <?php if ($status == "Assigned to job") { echo " selected"; } ?> value="Assigned to job">Assigned to job</option>
                                              <option <?php if ($status == "In transit and assigned") { echo " selected"; } ?> value="In transit and assigned">In transit and assigned</option>
                                            </select>
                                        </div>
                                      </div>
                                      <?php } ?>

                                        <?php if (strpos($value_config, ','."Display On Website".',') !== FALSE) { ?>
                                            <div class="form-group">
                                                <label for="site_name" class="col-sm-4 control-label">Display On Website:</label>
                                                <div class="col-sm-8">
                                                    <label class="pad-right"><input type="radio" <?php if ($display_website == "Yes") { echo " checked"; } ?> name="display_website" value="Yes">Yes</label>
                                                    <label class="pad-right"><input type="radio" <?php if ($display_website == "No") { echo " checked"; } ?> name="display_website" value="No">No</label>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Featured On Website".',') !== FALSE) { ?>
                                            <div class="form-group">
                                                <label for="site_name" class="col-sm-4 control-label">Featured On Website:</label>
                                                <div class="col-sm-8">
                                                    <label class="pad-right"><input type="radio" <?php if ($featured == "1") { echo " checked"; } ?> name="featured" value="1">Yes</label>
                                                    <label class="pad-right"><input type="radio" <?php if ($featured == "0") { echo " checked"; } ?> name="featured" value="0">No</label>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."New Item".',') !== FALSE) { ?>
                                            <div class="form-group">
                                                <label for="site_name" class="col-sm-4 control-label">Display Item As New On Website:</label>
                                                <div class="col-sm-8">
                                                    <label class="pad-right"><input type="radio" <?php if ($new_item == "1") { echo " checked"; } ?> name="new_item" value="1">Yes</label>
                                                    <label class="pad-right"><input type="radio" <?php if ($new_item == "0") { echo " checked"; } ?> name="new_item" value="0">No</label>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Item On Sale".',') !== FALSE) { ?>
                                            <div class="form-group">
                                                <label for="site_name" class="col-sm-4 control-label">Display Item As On Sale On Website:</label>
                                                <div class="col-sm-8">
                                                    <label class="pad-right"><input type="radio" <?php if ($on_sale == "1") { echo " checked"; } ?> name="on_sale" value="1">Yes</label>
                                                    <label class="pad-right"><input type="radio" <?php if ($on_sale == "0") { echo " checked"; } ?> name="on_sale" value="0">No</label>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Item On Clearance".',') !== FALSE) { ?>
                                            <div class="form-group">
                                                <label for="site_name" class="col-sm-4 control-label">Display Item As Clearance On Website (Uses Clearance Price):</label>
                                                <div class="col-sm-8">
                                                    <label class="pad-right"><input type="radio" <?php if ($on_clearance == "1") { echo " checked"; } ?> name="on_clearance" value="1">Yes</label>
                                                    <label class="pad-right"><input type="radio" <?php if ($on_clearance == "0") { echo " checked"; } ?> name="on_clearance" value="0">No</label>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Main Image".',') !== FALSE) { ?>
                                            <div class="col-sm-6"><?php
                                                if(!empty($_GET['inventoryid'])) {
                                                    $inventoryid = $_GET['inventoryid'];
                                                    $main_image_db = '';
                                                    $result = mysqli_query ( $dbc, "SELECT `main_image` FROM `inventory` WHERE `inventoryid`='$inventoryid' AND `main_image` IS NOT NULL" );

                                                    if ( mysqli_num_rows($result) > 0 ) {
                                                        echo "
                                                            <table class='table table-bordered'>
                                                                <tr class='hidden-xs hidden-sm'>
                                                                    <th>Main Image</th>
                                                                    <th>Delete</th>
                                                                </tr>";

                                                            while($row = mysqli_fetch_array($result)) {
                                                                echo '
                                                                    <tr>
                                                                        <td data-title="Main Image"><a href="download/'.$row['main_image'].'" target="_blank">'.$row['main_image'].'</a></td>
                                                                        <td data-title="Delete"><a href="?inventoryid='.$inventoryid.'&action=delete_main_image" onclick="return confirm(\'Are you sure you want to delete the Main Image?\')">Delete</a></td>
                                                                    </tr>';

                                                                $main_image_db = $row['main_image'];
                                                            }

                                                        echo '</table>';
                                                    }
                                                } ?>

                                                <span class="popover-examples list-inline"><a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas. 650x650 pixels under 100Kb is recommended."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a></span>
                                                <b>Main Image</b><br />
                                                <input name="upload_main_image" type="file" value="<?= $main_image_db ?>" data-filename-placement="inside" class="form-control" />
                                            </div>
                                            <div class="clearfix double-gap-bottom"></div>
                                            <hr />
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Additional Images".',') !== FALSE) { ?>
                                            <div class="col-sm-6"><?php
                                                if(!empty($_GET['inventoryid'])) {
                                                    $inventoryid = $_GET['inventoryid'];
                                                    $result = mysqli_query ( $dbc, "SELECT * FROM `inventory_images` WHERE `inventoryid`='$inventoryid' AND `image` IS NOT NULL" );

                                                    if ( mysqli_num_rows($result) > 0 ) {
                                                        echo "
                                                            <table class='table table-bordered'>
                                                                <tr class='hidden-xs hidden-sm'>
                                                                    <th>Image</th>
                                                                    <th>Delete</th>
                                                                </tr>";

                                                            while($row = mysqli_fetch_array($result)) {
                                                                echo '
                                                                    <tr>
                                                                        <td data-title="Image"><a href="download/'.$row['image'].'" target="_blank">'.$row['image'].'</a></td>
                                                                        <td data-title="Delete"><a href="?inventoryid='.$inventoryid.'&action=delete_image&imageid='.$row['imageid'].'" onclick="return confirm(\'Are you sure you want to delete this image?\')">Delete</a></td>
                                                                    </tr>';
                                                            }

                                                        echo '</table>';
                                                    }
                                                } ?>

                                                <span class="popover-examples list-inline"><a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas. 650x650 pixels under 100Kb is recommended."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a></span>
                                                <b>Additional Images</b><br />
                                                <div class="additional_img"><input name="upload_additional_images[]" multiple type="file" data-filename-placement="inside" class="form-control" /></div>
                                                <div id="add_additional_here"></div>
                                                <button id="add_additional_img" class="btn brand-btn gap-top">Add Another Image</button>
                                            </div>
                                            <div class="clearfix double-gap-bottom"></div>
                                            <hr />
                                        <?php } ?>

                                        <?php if (strpos($value_config, ','."Spec Sheet".',') !== FALSE) { ?>
                                            <div class="col-sm-6"><?php
                                                if(!empty($_GET['inventoryid'])) {
                                                    $inventoryid = $_GET['inventoryid'];
                                                    $result = mysqli_query ( $dbc, "SELECT `spec_sheet` FROM `inventory` WHERE `inventoryid`='$inventoryid' AND `spec_sheet` IS NOT NULL" );

                                                    if ( mysqli_num_rows($result) > 0 ) {
                                                        echo "
                                                            <table class='table table-bordered'>
                                                                <tr class='hidden-xs hidden-sm'>
                                                                    <th>Spec Sheet</th>
                                                                    <th>Delete</th>
                                                                </tr>";

                                                            while($row = mysqli_fetch_array($result)) {
                                                                echo '
                                                                    <tr>
                                                                        <td data-title="Spec Sheet"><a href="download/'.$row['spec_sheet'].'" target="_blank">'.$row['spec_sheet'].'</a></td>
                                                                        <td data-title="Delete"><a href="?inventoryid='.$inventoryid.'&action=delete_spec_sheet" onclick="return confirm(\'Are you sure your want to delete the Spec Sheet?\')">Delete</a></td>
                                                                    </tr>';
                                                            }

                                                        echo '</table>';
                                                    }
                                                } ?>

                                                <span class="popover-examples list-inline"><a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a></span>
                                                <b>Spec Sheet</b><br />
                                                <div class="spec_sheet"><input name="spec_sheet" type="file" data-filename-placement="inside" class="form-control" /></div>
                                            </div>
                                            <div class="clearfix double-gap-bottom"></div>
                                        <?php } ?>

                                       <?php if (strpos($value_config, ','."Notes".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="fax_number"	class="col-sm-4	control-label">Notes:</label>
                                        <div class="col-sm-8">
                                            <textarea name="note" rows="5" cols="50" class="form-control"><?php echo $note; ?></textarea>
                                        </div>
                                      </div>
                                      <?php } ?>

                                       <?php if (strpos($value_config, ','."Comments".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="fax_number"	class="col-sm-4	control-label">Comment:</label>
                                        <div class="col-sm-8">
                                            <textarea name="comment" rows="5" cols="50" class="form-control"><?php echo $comment; ?></textarea>
                                        </div>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."Rent Price".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Rent Price:</label>
                                        <div class="col-sm-8">
                                          <input name="rent_price" value="<?php echo $rent_price; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."Drum Unit Cost".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Drum Unit Cost:</label>
                                        <div class="col-sm-8">
                                          <input name="drum_unit_cost" value="<?php echo $drum_unit_cost; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."Drum Unit Price".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Drum Unit Price:</label>
                                        <div class="col-sm-8">
                                          <input name="drum_unit_price" value="<?php echo $drum_unit_price; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."Tote Unit Cost".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Tote Unit Cost:</label>
                                        <div class="col-sm-8">
                                          <input name="tote_unit_cost" value="<?php echo $tote_unit_cost; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."Tote Unit Price".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Tote Unit Price:</label>
                                        <div class="col-sm-8">
                                          <input name="tote_unit_price" value="<?php echo $tote_unit_price; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."WCB Price".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">WCB Price:</label>
                                        <div class="col-sm-8">
                                          <input name="wcb_price" value="<?php echo $wcb_price; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."Rental Days".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Rental Days:</label>
                                        <div class="col-sm-8">
                                          <input name="rental_days" value="<?php echo $rental_days; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."Rental Weeks".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Rental Weeks:</label>
                                        <div class="col-sm-8">
                                          <input name="rental_weeks" value="<?php echo $rental_weeks; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>
                                      <?php if (strpos($value_config, ','."Rental Months".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Rental Months:</label>
                                        <div class="col-sm-8">
                                          <input name="rental_months" value="<?php echo $rental_months; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>
                                      <?php if (strpos($value_config, ','."Rental Years".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Rental Years:</label>
                                        <div class="col-sm-8">
                                          <input name="rental_years" value="<?php echo $rental_years; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>
                                      <?php if (strpos($value_config, ','."Reminder/Alert".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Reminder/Alert:</label>
                                        <div class="col-sm-8">
                                          <input name="reminder_alert" value="<?php echo $reminder_alert; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."Daily".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Daily:</label>
                                        <div class="col-sm-8">
                                          <input name="daily" value="<?php echo $daily; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."Weekly".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Weekly:</label>
                                        <div class="col-sm-8">
                                          <input name="weekly" value="<?php echo $weekly; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>
                                      <?php if (strpos($value_config, ','."Monthly".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Monthly:</label>
                                        <div class="col-sm-8">
                                          <input name="monthly" value="<?php echo $monthly; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>
                                      <?php if (strpos($value_config, ','."Annually".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Annually:</label>
                                        <div class="col-sm-8">
                                          <input name="annually" value="<?php echo $annually; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."#Of Days".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">#Of Days:</label>
                                        <div class="col-sm-8">
                                          <input name="total_days" value="<?php echo $total_days; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>
                                      <?php if (strpos($value_config, ','."#Of Hours".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">#Of Hours:</label>
                                        <div class="col-sm-8">
                                          <input name="total_hours" value="<?php echo $total_hours; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."#Of Kilometers".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">#Of Kilometers:</label>
                                        <div class="col-sm-8">
                                          <input name="total_km" value="<?php echo $total_km; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>
                                      <?php if (strpos($value_config, ','."#Of Miles".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">#Of Miles:</label>
                                        <div class="col-sm-8">
                                          <input name="total_miles" value="<?php echo $total_miles; ?>" type="text" class="form-control">
                                        </div>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."Bill of Material".',') !== FALSE) { ?>
                                      <div class="form-group">
                                        <label for="company_name" class="col-sm-4 control-label">Bill of Material:</label>
                                        <div class="col-sm-8">
                                            <select data-placeholder="Choose items" multiple name="bill_of_material[]" class="chosen-select-deselect form-control inventoryid" width="380">
                                                <?php
                                                $query = mysqli_query($dbc,"SELECT inventoryid, name FROM inventory ORDER BY name");
                                                while($row = mysqli_fetch_array($query)) {
                                                ?>
                                                    <option <?php if (strpos(','.$bill_of_material.',', ','.$row['inventoryid'].',') !== FALSE) {
                                                    echo " selected"; } ?>  value=<?php echo $row['inventoryid']; ?>><?php echo $row['name']; ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                      </div>
                    				  <!-- Display any inventory items that are using this inventory item in BoM -->
                    				  <ul style='border: 1px solid black;padding:10px;list-style:none;'>
                    				  <?php
                    				  $nmy = 0;
                    				  $query = mysqli_query($dbc,"SELECT * FROM inventory ORDER BY name");
                                                while($rw = mysqli_fetch_array($query)) {

                    								$HiddenProducts = $rw['bill_of_material'];
                    								$HiddenProducts = explode(',',$HiddenProducts);
                    								if (in_array($inventoryid, $HiddenProducts)) {
                    									$nmy++;
                    								  if($nmy == 1) {
                    									echo "This inventory item is being used as a bill of material item for the following inventory item(s):";
                    								  }
                    									$name = 'No name given';
                    									if($rw['name'] !== '' && $rw['name'] !== NULL) {
                    										$name = $rw['name'];
                    									} else if($rw['product_name'] !== '' && $rw['product_name'] !== NULL) {
                    										$name = $rw['product_name'];
                    									}
                    								  echo '<li><span title="Tab: '.$rw['category'].'">'.$name.' (ID: <a href="add_inventory.php?inventoryid='.$rw['inventoryid'].'&bomhist=true">'.$rw['inventoryid'].'</a>)</span></li>';
                    								}
                                                }
                    							if($nmy == 0) {
                    							echo 'This inventory item is not being used as a bill of material item for any other inventory items.';
                    							}
                    							echo '</ul>';
                    						}
                    						?>

                                    <?php if (strpos($value_config, ',Supplimentary Products,') !== FALSE) { ?>
                                        <div class="form-group">
                                            <label for="supplimentary" class="col-sm-4 control-label">Supplementary Products:</label>
                                            <div class="col-sm-8">
                                                <select data-placeholder="Choose items" multiple name="supplimentary[]" class="chosen-select-deselect form-control inventoryid" width="380">
                                                    <?php
                                                    $query = mysqli_query($dbc,"SELECT `inventoryid`, `name`, `part_no` FROM `inventory` ORDER BY `name`");

                                                    while ( $row = mysqli_fetch_array($query) ) { ?>
                                                        <option <?php if (strpos(','.$supplimentary.',', ','.$row['part_no'].',') !== FALSE) {
                                                        echo " selected"; } ?> value=<?php echo $row['part_no']; ?>><?php echo $row['name']; ?></option><?php
                                                    } ?>
                                                </select>
                                            </div>
                                        </div><?php
                                    } ?>

                                      <?php if (strpos($value_config, ','."Change Log".',') !== FALSE) { ?>
                                      <div class="form-group" id="no-more-tables">
                    					<h4>Log of changes to Inventory cost and quantity</h4>
                                        <table class="table table_bordered table-striped">
                                            <thead>
                                                <tr class="hidden-xs hidden-sm">
                                                    <th>User</th>
                                                    <th>Units Added</th>
                                                    <th>Cost of Units</th>
                                                    <th>Averaged Cost</th>
                                                    <th>Date</th>
                                                    <th>Source of Change</th>
                                                </tr>
                                            </thead>
                    						<?php $change_results = mysqli_query($dbc, "SELECT * FROM `inventory_change_log` WHERE `inventoryid` = '$inventoryid'");
                    						if(mysqli_num_rows($change_results) > 0) {
                    							while($change_row = mysqli_fetch_array($change_results)) { ?>
                    								<tr>
                    									<td data-title="User"><?php echo get_contact($dbc, $change_row['contactid']); ?></td>
                    									<td data-title="Units Added"><?php echo $change_row['changed_quantity']; ?></td>
                    									<td data-title="Cost of Units"><?php echo $change_row['current_cost']; ?></td>
                    									<td data-title="Averaged Cost"><?php echo $change_row['new_cost']; ?></td>
                    									<td data-title="Date"><?php echo $change_row['date_time']; ?></td>
                    									<td data-title="Source of Change"><?php echo ($change_row['location_of_change'] == 'Inventory Tile' ? 'Edited Inventory' :
                    										($change_row['location_of_change'] == 'Inventory Shipment' ? 'Received Shipment' : $change_row['location_of_change'])); ?></td>
                    								</tr>
                    							<?php }
                    						} else {
                    							echo "<tr><td colspan='6'>No changes found in the log.</td></tr>";
                    						} ?>
                    					</table>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."Change Cost".',') !== FALSE) { ?>
                                      <div class="form-group" id="no-more-tables">
                    					<h4>Log of changes to Inventory cost</h4>
                                        <table class="table table_bordered">
                    						<tr class="hidden-xs hidden-sm">
                    							<th>User</th>
                    							<th>Cost of Units</th>
                    							<th>Averaged Cost</th>
                    							<th>Date</th>
                    							<th>Source of Change</th>
                    						</tr>
                    						<?php $change_results = mysqli_query($dbc, "SELECT * FROM `inventory_change_log` WHERE `inventoryid` = '$inventoryid'");
                    						if(mysqli_num_rows($change_results) > 0) {
                    							while($change_row = mysqli_fetch_array($change_results)) { ?>
                    								<tr>
                    									<td data-title="User"><?php echo get_contact($dbc, $change_row['contactid']); ?></td>
                    									<td data-title="Cost of Units"><?php echo $change_row['current_cost']; ?></td>
                    									<td data-title="Averaged Cost"><?php echo $change_row['new_cost']; ?></td>
                    									<td data-title="Date"><?php echo $change_row['date_time']; ?></td>
                    									<td data-title="Source of Change"><?php echo ($change_row['location_of_change'] == 'Inventory Tile' ? 'Edited Inventory' :
                    										($change_row['location_of_change'] == 'Inventory Shipment' ? 'Received Shipment' : $change_row['location_of_change'])); ?></td>
                    								</tr>
                    							<?php }
                    						} else {
                    							echo "<tr><td colspan='6'>No changes found in the log.</td></tr>";
                    						} ?>
                    					</table>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."Change Qty".',') !== FALSE) { ?>
                                      <div class="form-group" id="no-more-tables">
                    					<h4>Log of changes to Inventory cost and quantity</h4>
                                        <table class="table table_bordered table-striped">
                    						<thead>
                                                <tr class="hidden-xs hidden-sm">
                                                    <th>User</th>
                                                    <th>Units Added</th>
                                                    <th>Date</th>
                                                    <th>Source of Change</th>
                                                </tr>
                                            </thead>
                    						<?php $change_results = mysqli_query($dbc, "SELECT * FROM `inventory_change_log` WHERE `inventoryid` = '$inventoryid'");
                    						if(mysqli_num_rows($change_results) > 0) {
                    							while($change_row = mysqli_fetch_array($change_results)) { ?>
                    								<tr>
                    									<td data-title="User"><?php echo get_contact($dbc, $change_row['contactid']); ?></td>
                    									<td data-title="Units Added"><?php echo $change_row['changed_quantity']; ?></td>
                    									<td data-title="Date"><?php echo $change_row['date_time']; ?></td>
                    									<td data-title="Source of Change"><?php echo ($change_row['location_of_change'] == 'Inventory Tile' ? 'Edited Inventory' :
                    										($change_row['location_of_change'] == 'Inventory Shipment' ? 'Received Shipment' : $change_row['location_of_change'])); ?></td>
                    								</tr>
                    							<?php }
                    						} else {
                    							echo "<tr><td colspan='6'>No changes found in the log.</td></tr>";
                    						} ?>
                    					</table>
                                      </div>
                                      <?php } ?>

                                      <?php if (strpos($value_config, ','."Change Comment".',') !== FALSE) { ?>
                                      <div class="form-group" id="no-more-tables">
                    					<h4>Log of changes to Inventory</h4>
                                        <table class="table table_bordered">
                    						<tr class="hidden-xs hidden-sm">
                    							<th>User</th>
                    							<th>Details</th>
                    							<th>Date</th>
                    							<th>Source of Change</th>
                    						</tr>
                    						<?php $change_results = mysqli_query($dbc, "SELECT * FROM `inventory_change_log` WHERE `inventoryid` = '$inventoryid'");
                    						if(mysqli_num_rows($change_results) > 0) {
                    							while($change_row = mysqli_fetch_array($change_results)) { ?>
                    								<tr>
                    									<td data-title="User"><?php echo get_contact($dbc, $change_row['contactid']); ?></td>
                    									<td data-title="Details"><?php echo $change_row['change_comment']; ?></td>
                    									<td data-title="Date"><?php echo $change_row['date_time']; ?></td>
                    									<td data-title="Source of Change"><?php echo ($change_row['location_of_change'] == 'Inventory Tile' ? 'Edited Inventory' :
                    										($change_row['location_of_change'] == 'Inventory Shipment' ? 'Received Shipment' : $change_row['location_of_change'])); ?></td>
                    								</tr>
                    							<?php }
                    						} else {
                    							echo "<tr><td colspan='6'>No changes found in the log.</td></tr>";
                    						} ?>
                    					</table>
                                      </div>
                                  <?php } ?>
                              </div>
                              <hr>
                            <?php $j++; } ?>
                    		<div class="form-group">
                    			<p><span class="brand-color"><em>Required Fields *</em></span></p>
                    		</div>

                    		<div class="form-group pull-right">
                				<a href="inventory.php?category=<?php echo preg_replace('/[^a-z]/','',strtolower($category)); ?>"	class="btn brand-btn">Back</a>
                				<!--<button	type="submit" name="submit"	value="Submit" class="btn brand-btn">Submit</button>-->
                    		</div>

                    		<div class="clearfix"></div>



                    		</form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
<script>
$(document).ready(function () {
     $("#form1").find('input').addClass('saveonajax');
     $("#form1").find('select').addClass('saveonajax');
     $("#form1").find('textarea').addClass('saveonajax');
});

 $(document).on('change', '.saveonajax', function(){
     addInventoryData();
 });

 /*$(".saveonajax").blur(function(){
   addStaffData();
 });*/
 function addInventoryData() {
   var formData = new FormData($("#form1")[0]);
   $.ajax({
     type: 'POST',
     url: 'inventory_ajax.php?action=add_update_inventory',
     //data: $('#form1').serialize(),
     data:formData,
     cache:false,
     processData: false,
         contentType: false,
     success: function(response) {
       if(response!=''){
         $('#contact_id_val').val(response);
       }
     }
   });
 }
</script>
<?php include ('../footer.php'); ?>
