<?php
/*
 * AJAX for POS Touch
 * pos_touch.php
 */

include ('../database_connection.php');
include ('../function.php');
include ('../global.php');
if(session_status() == PHP_SESSION_NONE) {
	session_start(['cookie_lifetime' => 518400]);
	$_SERVER['page_load_info'] .= 'Session Started: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
}
/* Customer selected */
if ( $_GET['fill'] == 'posTouchCustomerSelected' ) {
    $custid = preg_replace('/[^0-9]/', '', $_GET['custid']);

	$results = mysqli_query ( $dbc, "INSERT INTO `pos_touch_temp_order` (`custid`) VALUES ('$custid')" );
	$orderid = mysqli_insert_id($dbc);

	$before_change = '';
	$history = "Point of Sale Order Added. <br />";
	add_update_history($dbc, 'pos_history', $history, '', $before_change);

	// Set session to use on POS dashbaord
	$_SESSION['orderid'] = $orderid;
}


/* Appointment */
if ( $_GET['fill'] == 'posTouchAppointment' ) {
    $custid    = preg_replace('/[^0-9]/', '', $_GET['custid']);
    $staffid   = explode('*#*', filter_var(hex2bin($_GET['staffid']), FILTER_SANITIZE_STRING));
    $serviceid = explode('*#*', filter_var(hex2bin($_GET['serviceid']), FILTER_SANITIZE_STRING));

	$results = mysqli_query ( $dbc, "INSERT INTO `pos_touch_temp_order` (`custid`) VALUES ('$custid')" );
	$orderid = mysqli_insert_id($dbc);

	$before_change = '';
	$history = "Point of Sale Order Added. <br />";
	add_update_history($dbc, 'pos_history', $history, '', $before_change);

	// Set session to use on POS dashbaord
	$_SESSION['orderid'] = $orderid;

    // Get tax (GST/PST)
    $get_pos_tax	= get_config($dbc, 'pos_tax');
    $gst_rate		= '';
    $pst_rate		= '';

    if ( $get_pos_tax != '' ) {
        $pos_tax		= explode ( '*#*', $get_pos_tax );
        $total_count	= mb_substr_count ( $get_pos_tax, '*#*' );

        for ( $eq_loop=0; $eq_loop<=$total_count; $eq_loop++ ) {
            $pos_tax_name_rate = explode ( '**', $pos_tax[$eq_loop] );

            if ( strcasecmp ( $pos_tax_name_rate[0], 'gst' ) == 0 ) {
                $gst_rate = $pos_tax_name_rate[1];
            }

            if ( strcasecmp ( $pos_tax_name_rate[0], 'pst') == 0 ) {
                if ( $pos_tax_name_rate[3] == 'Yes' && $client_tax_exemption == 'Yes' ) {
                    $pst_rate = 0;
                } else {
                    $pst_rate = $pos_tax_name_rate[1];
                }
            }
        }

        $pst_rate = ( $pst_rate == '' ) ? 0 : $pst_rate;
    }

    // First set of items. No discounts without adding an item first.
    for ( $i=0; $i<count($serviceid); $i++ ) {
        $get_service    = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading`, `final_retail_price` FROM `services` WHERE `serviceid`='{$serviceid[$i]}'"));
        $service_name   = $get_service['heading'];
        $total          = $get_service['final_retail_price'];
        $sub_total		= $get_service['final_retail_price'];
        $gst_total		= $total * ( $gst_rate / 100 );
        $pst_total		= $total * ( $pst_rate / 100 );
        $total_tax		= number_format ( $gst_total + $pst_total, 2 );
        $order_total	= number_format ( $total + $total_tax, 2 );

        // Insert to `pos_touch_temp_order_products`
        $insert_temp_order_products = mysqli_query ( $dbc, "INSERT INTO `pos_touch_temp_order_products` (`orderid`, `staffid`, `serviceid`, `product_name`, `quantity`, `total`) VALUES ('$orderid', '{$staffid[$i]}', '{$serviceid[$i]}', '$service_name', '1', '$total')" );
				$before_change = '';
        $history = "Point of Sale Order Product Added. <br />";
        add_update_history($dbc, 'pos_history', $history, '', $before_change);

        $update_temp_order = mysqli_query($dbc, "UPDATE `pos_touch_temp_order` SET `sub_total_before_discount`=`sub_total_before_discount`+'$sub_total', `sub_total`=`sub_total`+'$sub_total', `gst_total`=IF(`gst_total` IS NULL, '$gst_total', `gst_total`+'$gst_total'), `pst_total`=IF(`pst_total` IS NULL, '$pst_total', `pst_total`+'$pst_total'), `total_tax`=`total_tax`+'$total_tax', `order_total`=`order_total`+'$order_total' WHERE `orderid`='$orderid'");

				$before_change = '';
				$history = "Point of Sale Order Updated. <br />";
				add_update_history($dbc, 'pos_history', $history, '', $before_change);
    }
}


/* Add Inventory Item */
if ( $_GET['fill'] == 'posTouchAddInventoryPrice' ) {
    $orderid		= $_GET['orderid'];
	$invid 			= $_GET['invid'];
	$inv_pricing	= $_GET['inv_pricing'];
    $quantity 		= $_GET['quantity'];
    $gst 			= $_GET['gst'];
    $pst	 		= $_GET['pst'];
	$total			= 0.00;
	$name			= '';

	$results = mysqli_query ( $dbc, "SELECT `inventoryid`, `name`, `$inv_pricing` AS `inv_price` FROM `inventory` WHERE `inventoryid`='$invid'" );
	while ( $row=mysqli_fetch_assoc($results) ) {
		$total	= $quantity * $row['inv_price'];
		$name	= $row['name'];
	}

	if ( $orderid != 0 ) {
		// Update the order
		$row				= mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT * FROM `pos_touch_temp_order` WHERE `orderid`='$orderid'" ) );
		$discount_percent	= ( empty($row['discount_percent']) || $row['discount_percent']==NULL ) ? 0 : $row['discount_percent'];
		$discount_amount	= ( empty($row['discount_amount']) || $row['discount_amount']==NULL ) ? 0 : $row['discount_amount'];

		if ( $discount_percent > 0 && $discount_amount > 0 ) {
			$discount_amount	= $row['discount_amount'] + ( $total * ( $discount_percent / 100 ) );
			$sub_total_before_discount = $row['sub_total_before_discount'] + $total;
			$sub_total			= number_format ( $sub_total_before_discount - $discount_amount, 2 );
		} else {
			$sub_total_before_discount = number_format ( $row['sub_total'] + $total, 2 );
			$sub_total = $sub_total_before_discount;
		}

		$gst_total			= $sub_total * ( $gst / 100 );
		$pst_total			= $sub_total * ( $pst / 100 );
		$total_tax			= number_format ( $gst_total + $pst_total, 2 );
		$order_total		= number_format ( $sub_total + $total_tax, 2 );

		$before_change = capture_before_change($dbc, 'pos_touch_temp_order', 'discount_amount', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'sub_total_before_discount', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'sub_total', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'gst_total', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'pst_total', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'total_tax', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'order_total', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'orderid', 'orderid', $orderid);

		$results = mysqli_query ( $dbc, "UPDATE `pos_touch_temp_order` SET `discount_amount`='$discount_amount', `sub_total_before_discount`='$sub_total_before_discount', `sub_total`='$sub_total', `gst_total`='$gst_total', `pst_total`='$pst_total', `total_tax`='$total_tax', `order_total`='$order_total' WHERE `orderid`='$orderid'" );

		$history = capture_after_change('discount_amount', $discount_amount);
		$history .= capture_after_change('sub_total_before_discount', $sub_total_before_discount);
		$history .= capture_after_change('sub_total', $sub_total);
		$history .= capture_after_change('gst_total', $gst_total);
		$history .= capture_after_change('pst_total', $pst_total);
		$history .= capture_after_change('total_tax', $total_tax);
		$history .= capture_after_change('order_total', $order_total);
		add_update_history($dbc, 'pos_history', $history, '', $before_change);

	} else {
		// First item. No discounts without adding an item first.
		$sub_total_before_discount = $total;
		$sub_total		= $sub_total_before_discount;
		$gst_total		= $total * ( $gst / 100 );
		$pst_total		= $total * ( $pst / 100 );
		$total_tax		= number_format ( $gst_total + $pst_total, 2 );
		$order_total	= number_format ( $total + $total_tax, 2 );

		$results = mysqli_query ( $dbc, "INSERT INTO `pos_touch_temp_order` (`sub_total_before_discount`, `sub_total`, `gst_total`, `pst_total`, `total_tax`, `order_total`) VALUES ('$sub_total_before_discount', '$sub_total', '$gst_total', '$pst_total', '$total_tax', '$order_total')" );
		$orderid = mysqli_insert_id($dbc);
		$before_change = '';
		$history = "Point of Sale Order Added. <br />";
		add_update_history($dbc, 'pos_history', $history, '', $before_change);

		/*
		 * Set session to use on POS dashbaord,
		 * if Customer display is disabled from Settings -> Choose Fields for POS.
		 * If Customer display is enabled, orderid is set in posTouchCustomerSelected.
		 */
		if ( !isset ( $_SESSION['orderid'] ) ) {
			$_SESSION['orderid'] = $orderid;
		}
	}

	// Insert to `pos_touch_temp_order_products`
	$results = mysqli_query ( $dbc, "INSERT INTO `pos_touch_temp_order_products` (`orderid`, `inventoryid`, `product_name`, `inventory_pricing`, `quantity`, `total`) VALUES ('$orderid', '$invid', '$name', '$inv_pricing', '$quantity', '$total')" );
	$before_change = '';
	$history = "Point of Sale Order products Added. <br />";
	add_update_history($dbc, 'pos_history', $history, '', $before_change);
}


/* Add product */
if ( $_GET['fill'] == 'posTouchAddPrice' ) {
    $orderid	= $_GET['orderid'];
	$name		= $_GET['name'];
	$prodid 	= $_GET['prodid'];
	$price		= $_GET['price'];
    $quantity 	= $_GET['quantity'];
    $gst 		= $_GET['gst'];
    $pst	 	= $_GET['pst'];

	$get_price = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `final_retail_price` FROM `products` WHERE `productid`='$prodid'" ) );
	$price		= $get_price['final_retail_price'];
	$total		= $price * $quantity;

	if ( $orderid != 0 ) {
		// Update the order
		$row				= mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT * FROM `pos_touch_temp_order` WHERE `orderid`='$orderid'" ) );
		$discount_percent	= ( empty($row['discount_percent']) || $row['discount_percent']==NULL ) ? 0 : $row['discount_percent'];
		$discount_amount	= ( empty($row['discount_amount']) || $row['discount_amount']==NULL ) ? 0 : $row['discount_amount'];

		if ( $discount_percent > 0 && $discount_amount > 0 ) {
			$discount_amount	= $row['discount_amount'] + ( $total * ( $discount_percent / 100 ) );
			$sub_total_before_discount = $row['sub_total_before_discount'] + $total;
			$sub_total			= number_format ( $sub_total_before_discount - $discount_amount, 2 );
		} else {
			$sub_total_before_discount = number_format ( $row['sub_total'] + $total, 2 );
			$sub_total = $sub_total_before_discount;
		}

		$gst_total			= $sub_total * ( $gst / 100 );
		$pst_total			= $sub_total * ( $pst / 100 );
		$total_tax			= number_format ( $gst_total + $pst_total, 2 );
		$order_total		= number_format ( $sub_total + $total_tax, 2 );

		$before_change = capture_before_change($dbc, 'pos_touch_temp_order', 'discount_amount', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'sub_total_before_discount', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'sub_total', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'gst_total', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'pst_total', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'total_tax', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'order_total', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'orderid', 'orderid', $orderid);

		$results = mysqli_query ( $dbc, "UPDATE `pos_touch_temp_order` SET `discount_amount`='$discount_amount', `sub_total_before_discount`='$sub_total_before_discount', `sub_total`='$sub_total', `gst_total`='$gst_total', `pst_total`='$pst_total', `total_tax`='$total_tax', `order_total`='$order_total' WHERE `orderid`='$orderid'" );

		$history = capture_after_change('discount_amount', $discount_amount);
		$history .= capture_after_change('sub_total_before_discount', $sub_total_before_discount);
		$history .= capture_after_change('sub_total', $sub_total);
		$history .= capture_after_change('gst_total', $gst_total);
		$history .= capture_after_change('pst_total', $pst_total);
		$history .= capture_after_change('total_tax', $total_tax);
		$history .= capture_after_change('order_total', $order_total);
		add_update_history($dbc, 'pos_history', $history, '', $before_change);

	} else {
		// First item. No discounts without adding an item first.
		$sub_total_before_discount = $total;
		$sub_total		= $sub_total_before_discount;
		$gst_total		= $total * ( $gst / 100 );
		$pst_total		= $total * ( $pst / 100 );
		$total_tax		= number_format ( $gst_total + $pst_total, 2 );
		$order_total	= number_format ( $total + $total_tax, 2 );

		$results = mysqli_query ( $dbc, "INSERT INTO `pos_touch_temp_order` (`sub_total_before_discount`, `sub_total`, `gst_total`, `pst_total`, `total_tax`, `order_total`) VALUES ('$sub_total_before_discount', '$sub_total', '$gst_total', '$pst_total', '$total_tax', '$order_total')" );
		$orderid = mysqli_insert_id($dbc);

		$before_change = '';
		$history = "Point of Sale Order products Added. <br />";
		add_update_history($dbc, 'pos_history', $history, '', $before_change);

		// Set session to use on POS dashbaord
		$_SESSION['orderid'] = $orderid;
	}

	// Insert to `pos_touch_temp_order_products`
	$results = mysqli_query ( $dbc, "INSERT INTO `pos_touch_temp_order_products` (`orderid`, `productid`, `product_name`, `quantity`, `total`) VALUES ('$orderid', '$prodid', '$name', '$quantity', '$total')" );
	$before_change = '';
	$history = "Point of Sale Order products Added. <br />";
	add_update_history($dbc, 'pos_history', $history, '', $before_change);
}


/* Add service */
if ( $_GET['fill'] == 'posTouchAddServicePrice' ) {
    $orderid	= $_GET['orderid'];
    $staffid    = $_GET['staffid'];
	$name		= hex2bin($_GET['name']);
    $price      = $_GET['price'];
	$servid 	= $_GET['servid'];
    $quantity 	= $_GET['quantity'];
    $gst 		= $_GET['gst'];
    $pst	 	= $_GET['pst'];

	if ( $price=='0' ) {
        $get_price  = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `final_retail_price` FROM `services` WHERE `serviceid`='$servid'" ) );
        $price		= $get_price['final_retail_price'];
    }
	$total = $price * $quantity;

	if ( $orderid != 0 ) {
		// Update the order
		$row				= mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT * FROM `pos_touch_temp_order` WHERE `orderid`='$orderid'" ) );
		$discount_percent	= ( empty($row['discount_percent']) || $row['discount_percent']==NULL ) ? 0 : $row['discount_percent'];
		$discount_amount	= ( empty($row['discount_amount']) || $row['discount_amount']==NULL ) ? 0 : $row['discount_amount'];

		if ( $discount_percent > 0 && $discount_amount > 0 ) {
			$discount_amount	= $row['discount_amount'] + ( $total * ( $discount_percent / 100 ) );
			$sub_total_before_discount = $row['sub_total_before_discount'] + $total;
			$sub_total			= number_format ( $sub_total_before_discount - $discount_amount, 2 );
		} else {
			$sub_total_before_discount = number_format ( $row['sub_total'] + $total, 2 );
			$sub_total = $sub_total_before_discount;
		}

		$gst_total			= $sub_total * ( $gst / 100 );
		$pst_total			= $sub_total * ( $pst / 100 );
		$total_tax			= number_format ( $gst_total + $pst_total, 2 );
		$order_total		= number_format ( $sub_total + $total_tax, 2 );

		$before_change = capture_before_change($dbc, 'pos_touch_temp_order', 'discount_amount', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'sub_total_before_discount', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'sub_total', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'gst_total', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'pst_total', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'total_tax', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'order_total', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'orderid', 'orderid', $orderid);

		$results = mysqli_query ( $dbc, "UPDATE `pos_touch_temp_order` SET `discount_amount`='$discount_amount', `sub_total_before_discount`='$sub_total_before_discount', `sub_total`='$sub_total', `gst_total`='$gst_total', `pst_total`='$pst_total', `total_tax`='$total_tax', `order_total`='$order_total' WHERE `orderid`='$orderid'" );

		$history = capture_after_change('discount_amount', $discount_amount);
		$history .= capture_after_change('sub_total_before_discount', $sub_total_before_discount);
		$history .= capture_after_change('sub_total', $sub_total);
		$history .= capture_after_change('gst_total', $gst_total);
		$history .= capture_after_change('pst_total', $pst_total);
		$history .= capture_after_change('total_tax', $total_tax);
		$history .= capture_after_change('order_total', $order_total);
		add_update_history($dbc, 'pos_history', $history, '', $before_change);

	} else {
		// First item. No discounts without adding an item first.
		$sub_total_before_discount = $total;
		$sub_total		= $sub_total_before_discount;
		$gst_total		= $total * ( $gst / 100 );
		$pst_total		= $total * ( $pst / 100 );
		$total_tax		= number_format ( $gst_total + $pst_total, 2 );
		$order_total	= number_format ( $total + $total_tax, 2 );

		$results = mysqli_query ( $dbc, "INSERT INTO `pos_touch_temp_order` (`sub_total_before_discount`, `sub_total`, `gst_total`, `pst_total`, `total_tax`, `order_total`) VALUES ('$sub_total_before_discount', '$sub_total', '$gst_total', '$pst_total', '$total_tax', '$order_total')" );
		$orderid = mysqli_insert_id($dbc);

		$before_change = '';
		$history = "Point of Sale Order Added. <br />";
		add_update_history($dbc, 'pos_history', $history, '', $before_change);

		// Set session to use on POS dashbaord
		$_SESSION['orderid'] = $orderid;
	}

	// Insert to `pos_touch_temp_order_products`
	$results = mysqli_query ( $dbc, "INSERT INTO `pos_touch_temp_order_products` (`orderid`, `staffid`, `serviceid`, `product_name`, `quantity`, `total`) VALUES ('$orderid', '$staffid', '$servid', '$name', '$quantity', '$total')" );

	$before_change = '';
	$history = "Point of Sale Order products Added. <br />";
	add_update_history($dbc, 'pos_history', $history, '', $before_change);
}


/* Remove a product */
if ( $_GET['fill'] == 'posTouchRemoveProduct' ) {
    $orderlistid	= $_GET['orderlistid'];
    $gst 			= $_GET['gst'];
    $pst	 		= $_GET['pst'];

	$row = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT o.*, p.* FROM `pos_touch_temp_order` AS o INNER JOIN `pos_touch_temp_order_products` AS p ON (o.`orderid` = p.`orderid`) WHERE p.`orderlistid`='$orderlistid'" ) );

	$orderid			= $row['orderid'];
	$discount_percent	= ( empty($row['discount_percent']) || $row['discount_percent']==NULL ) ? 0 : $row['discount_percent'];
	$discount_amount	= ( empty($row['discount_amount']) || $row['discount_amount']==NULL ) ? 0 : $row['discount_amount'];

	if ( $discount_percent > 0 && $discount_amount > 0 ) {
		$removed_discount	= $row['total'] * ( $discount_percent / 100 );
		$discount_amount	= $row['discount_amount'] - $removed_discount;
		$sub_total_before_discount = $row['sub_total_before_discount'] - $row['total'];
		$sub_total			= $sub_total_before_discount - $discount_amount;
	} else {
		$sub_total_before_discount = number_format ( $row['sub_total_before_discount'] - $row['total'], 2 );
		$sub_total			= $sub_total_before_discount;
	}

	$gst_total			= $sub_total * ( $gst / 100 );
	$pst_total			= $sub_total * ( $pst / 100 );
	$total_tax			= number_format ( $gst_total + $pst_total, 2 );
	$order_total		= number_format ( $sub_total + $total_tax, 2 );

	// Update temporary order
	$before_change = capture_before_change($dbc, 'pos_touch_temp_order', 'discount_amount', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'sub_total_before_discount', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'sub_total', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'gst_total', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'pst_total', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'total_tax', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'order_total', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'orderid', 'orderid', $orderid);

	$results = mysqli_query ( $dbc, "UPDATE `pos_touch_temp_order` SET `discount_amount`='$discount_amount', `sub_total_before_discount`='$sub_total_before_discount', `sub_total`='$sub_total', `gst_total`='$gst_total', `pst_total`='$pst_total', `total_tax`='$total_tax', `order_total`='$order_total' WHERE `orderid`='$orderid'" );

	$history = capture_after_change('discount_amount', $discount_amount);
	$history .= capture_after_change('sub_total_before_discount', $sub_total_before_discount);
	$history .= capture_after_change('sub_total', $sub_total);
	$history .= capture_after_change('gst_total', $gst_total);
	$history .= capture_after_change('pst_total', $pst_total);
	$history .= capture_after_change('total_tax', $total_tax);
	$history .= capture_after_change('order_total', $order_total);
	add_update_history($dbc, 'pos_history', $history, '', $before_change);

	if ( mysqli_affected_rows ($dbc) ) {
		// Delete product from temporary order products list
		$results = mysqli_query ( $dbc, "DELETE FROM `pos_touch_temp_order_products` WHERE `orderlistid`=$orderlistid" );

		// Reset `pos_touch_temp_order` values to 0, if there are no products in `pos_touch_temp_order_products`
		$results = mysqli_query ( $dbc, "SELECT * FROM `pos_touch_temp_order_products` WHERE `orderid`='$orderid'" );
		$num_rows = mysqli_num_rows($results);
		if ( $num_rows == 0 ) {
			$before_change = capture_before_change($dbc, 'pos_touch_temp_order', 'discount_percent', 'orderid', $orderid);
			$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'discount_amount', 'orderid', $orderid);
			$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'sub_total_before_discount', 'orderid', $orderid);
			$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'sub_total', 'orderid', $orderid);
			$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'gst_total', 'orderid', $orderid);
			$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'pst_total', 'orderid', $orderid);
			$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'total_tax', 'orderid', $orderid);
			$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'order_total', 'orderid', $orderid);

			$results = mysqli_query ( $dbc, "UPDATE `pos_touch_temp_order` SET `discount_percent`=NULL, `discount_amount`=NULL, `sub_total_before_discount`='0', `sub_total`='0', `gst_total`='0', `pst_total`='0', `total_tax`='0', `order_total`='0' WHERE `orderid`='$orderid'" );

			$history = capture_after_change('discount_percent', NULL);
			$history .= capture_after_change('discount_amount', NULL);
			$history .= capture_after_change('sub_total_before_discount', 0);
			$history .= capture_after_change('sub_total', 0);
			$history .= capture_after_change('gst_total', 0);
			$history .= capture_after_change('pst_total', 0);
			$history .= capture_after_change('total_tax', 0);
			$history .= capture_after_change('order_total', 0);
			add_update_history($dbc, 'pos_history', $history, '', $before_change);
		}

	} else {
		echo 'pos_touch_temp_order was not updated';
	}
}


/* Add discount */
if ( $_GET['fill'] == 'posTouchAddDiscount' ) {
    $orderid	= $_GET['orderid'];
	$disc_type	= $_GET['disc_type'];
	$discount	= $_GET['discount'];
	$gst		= $_GET['gst'];
	$pst		= $_GET['pst'];

	$row = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT * FROM `pos_touch_temp_order` WHERE `orderid`='$orderid'" ) );

	if ( $disc_type=='%' ) {
		$discount_amount = $row['sub_total'] * ( $discount / 100 );
	}
	if ( $disc_type=='$' ) {
		$discount_amount = $discount;
	}

	$sub_total			= $row['sub_total'] - $discount_amount;
	$gst_total			= $sub_total * ( $gst / 100 );
	$pst_total			= $sub_total * ( $pst / 100 );
	$total_tax			= $gst_total + $pst_total;
	$order_total		= $sub_total + $total_tax;

	// Update temporary order

	$before_change = capture_before_change($dbc, 'pos_touch_temp_order', 'discount_amount', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'discount_amount', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'sub_total', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'gst_total', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'pst_total', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'total_tax', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'order_total', 'orderid', $orderid);

	$results = mysqli_query ( $dbc, "UPDATE `pos_touch_temp_order` SET `discount_percent`='$discount', `discount_amount`='$discount_amount', `sub_total`='$sub_total', `gst_total`='$gst_total', `pst_total`='$pst_total', `total_tax`='$total_tax', `order_total`='$order_total' WHERE `orderid`='$orderid'" );

	$history = capture_after_change('discount_percent', $discount_percent);
	$history .= capture_after_change('discount_amount', $discount_amount);
	$history .= capture_after_change('sub_total', $sub_total);
	$history .= capture_after_change('gst_total', $gst_total);
	$history .= capture_after_change('pst_total', $pst_total);
	$history .= capture_after_change('total_tax', $total_tax);
	$history .= capture_after_change('order_total', $order_total);
	add_update_history($dbc, 'pos_history', $history, '', $before_change);
}

/* Apply Gift Card */
if ( $_GET['fill'] == 'posTouchAddGF' ) {
  $orderid	= $_GET['orderid'];
  $gf_number = $_GET['gf_number'];
  $gst		= $_GET['gst'];
	$pst		= $_GET['pst'];
  $today_date = date('Y-m-d');
  $gf_row = mysqli_fetch_assoc( mysqli_query( $dbc, "select value from pos_giftcards where giftcard_number = '$gf_number' and deleted = 0 and status = 0 and issue_date <= '$today_date' AND expiry_date >= '$today_date'"));
  if($gf_row['value'] == null || $gf_row['value'] == '') {
    echo "na";
  }
  else {
    $row = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT * FROM `pos_touch_temp_order` WHERE `orderid`='$orderid'" ) );

  	$discount_amount = $gf_row['value'];

  	$sub_total			= $row['sub_total'] - $discount_amount;
  	$gst_total			= $sub_total * ( $gst / 100 );
  	$pst_total			= $sub_total * ( $pst / 100 );
  	$total_tax			= $gst_total + $pst_total;
  	$order_total		= $sub_total + $total_tax;

  	// Update temporary order
		$before_change = capture_before_change($dbc, 'pos_touch_temp_order', 'discount_amount', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'sub_total', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'gst_total', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'pst_total', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'total_tax', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'order_total', 'orderid', $orderid);

  	$results = mysqli_query ( $dbc, "UPDATE `pos_touch_temp_order` SET `discount_amount`='$discount_amount', `sub_total`='$sub_total', `gst_total`='$gst_total', `pst_total`='$pst_total', `total_tax`='$total_tax', `order_total`='$order_total' WHERE `orderid`='$orderid'" );

		$history = capture_after_change('discount_amount', $discount_amount);
		$history .= capture_after_change('sub_total', $sub_total);
		$history .= capture_after_change('gst_total', $gst_total);
		$history .= capture_after_change('pst_total', $pst_total);
		$history .= capture_after_change('total_tax', $total_tax);
		$history .= capture_after_change('order_total', $order_total);
		add_update_history($dbc, 'pos_history', $history, '', $before_change);

    $gf_results = mysqli_query($dbc, "UPDATE `pos_giftcards` SET `status` = 1 where giftcard_number = '$gf_number'");
  }
}

/* Remove discount */
if ( $_GET['fill'] == 'posTouchRemoveDiscount' ) {
    $orderid	= $_GET['orderid'];
	$gst		= $_GET['gst'];
	$pst		= $_GET['pst'];

	$row = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT * FROM `pos_touch_temp_order` WHERE `orderid`='$orderid'" ) );

	$sub_total			= $row['sub_total'] + $row['discount_amount'];
	$gst_total			= $sub_total * ( $gst / 100 );
	$pst_total			= $sub_total * ( $pst / 100 );
	$total_tax			= $gst_total + $pst_total;
	$order_total		= $sub_total + $total_tax;

	// Update temporary order
	$before_change = capture_before_change($dbc, 'pos_touch_temp_order', 'discount_percent', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'discount_amount', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'sub_total', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'gst_total', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'pst_total', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'total_tax', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'order_total', 'orderid', $orderid);

	$results = mysqli_query ( $dbc, "UPDATE `pos_touch_temp_order` SET `discount_percent`=NULL, `discount_amount`=NULL, `sub_total`='$sub_total', `gst_total`='$gst_total', `pst_total`='$pst_total', `total_tax`='$total_tax', `order_total`='$order_total' WHERE `orderid`='$orderid'" );

	$history = capture_after_change('discount_percent', $discount_percent);
	$history .= capture_after_change('discount_amount', $discount_amount);
	$history .= capture_after_change('sub_total', $sub_total);
	$history .= capture_after_change('gst_total', $gst_total);
	$history .= capture_after_change('pst_total', $pst_total);
	$history .= capture_after_change('total_tax', $total_tax);
	$history .= capture_after_change('order_total', $order_total);
	add_update_history($dbc, 'pos_history', $history, '', $before_change);
}


/* Add coupon */
if ( $_GET['fill'] == 'posTouchAddCoupon' ) {
    $orderid	= $_GET['orderid'];
    $couponid	= $_GET['couponid'];
	$gst		= $_GET['gst'];
	$pst		= $_GET['pst'];

	$row_coupon		= mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `discount_type`, `discount` FROM `pos_touch_coupons` WHERE `couponid`='$couponid'" ) );
	$discount_type	= $row_coupon['discount_type'];
	$discount		= $row_coupon['discount'];

	$row = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT * FROM `pos_touch_temp_order` WHERE `orderid`='$orderid'" ) );

	// We take `sub_total` instead of `sub_total_before_discount` because a discount can be added separately
	if ( $discount_type=='%' ) {
		$coupon_value = $row['sub_total'] * ( $discount / 100 );
	}
	if ( $discount_type=='$' ) {
		$coupon_value = $discount;
	}

	$sub_total			= $row['sub_total'] - $coupon_value;
	$gst_total			= $sub_total * ( $gst / 100 );
	$pst_total			= $sub_total * ( $pst / 100 );
	$total_tax			= $gst_total + $pst_total;
	$order_total		= $sub_total + $total_tax;

	// Update temporary order
	$before_change = capture_before_change($dbc, 'pos_touch_temp_order', 'couponid', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'coupon_value', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'sub_total', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'gst_total', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'pst_total', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'total_tax', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'order_total', 'orderid', $orderid);

	$results = mysqli_query ( $dbc, "UPDATE `pos_touch_temp_order` SET `couponid`='$couponid', `coupon_value`='$coupon_value', `sub_total`='$sub_total', `gst_total`='$gst_total', `pst_total`='$pst_total', `total_tax`='$total_tax', `order_total`='$order_total' WHERE `orderid`='$orderid'" );

	$history = capture_after_change('couponid', $couponid);
	$history .= capture_after_change('coupon_value', $coupon_value);
	$history .= capture_after_change('sub_total', $sub_total);
	$history .= capture_after_change('gst_total', $gst_total);
	$history .= capture_after_change('pst_total', $pst_total);
	$history .= capture_after_change('total_tax', $total_tax);
	$history .= capture_after_change('order_total', $order_total);
	add_update_history($dbc, 'pos_history', $history, '', $before_change);

	// Update coupon used times
	$results = mysqli_query ( $dbc, "UPDATE `pos_touch_coupons` SET `used_times`=`used_times`+1 WHERE `couponid`='$couponid'" );
}


/* Remove coupon */
if ( $_GET['fill'] == 'posTouchRemoveCoupon' ) {
    $orderid	= $_GET['orderid'];
    $couponid	= $_GET['couponid'];
	$gst		= $_GET['gst'];
	$pst		= $_GET['pst'];

	$row = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT * FROM `pos_touch_temp_order` WHERE `orderid`='$orderid'" ) );

	$sub_total			= $row['sub_total'] + $row['coupon_value'];
	$gst_total			= $sub_total * ( $gst / 100 );
	$pst_total			= $sub_total * ( $pst / 100 );
	$total_tax			= $gst_total + $pst_total;
	$order_total		= $sub_total + $total_tax;

	// Update temporary order
	$before_change = capture_before_change($dbc, 'pos_touch_temp_order', 'couponid', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'coupon_value', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'sub_total', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'gst_total', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'pst_total', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'total_tax', 'orderid', $orderid);
	$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'order_total', 'orderid', $orderid);

	$results = mysqli_query ( $dbc, "UPDATE `pos_touch_temp_order` SET `couponid`=0, `coupon_value`='0', `sub_total`='$sub_total', `gst_total`='$gst_total', `pst_total`='$pst_total', `total_tax`='$total_tax', `order_total`='$order_total' WHERE `orderid`='$orderid'" );

	$history = capture_after_change('couponid', 0);
	$history .= capture_after_change('coupon_value', 0);
	$history .= capture_after_change('sub_total', $sub_total);
	$history .= capture_after_change('gst_total', $gst_total);
	$history .= capture_after_change('pst_total', $pst_total);
	$history .= capture_after_change('total_tax', $total_tax);
	$history .= capture_after_change('order_total', $order_total);
	add_update_history($dbc, 'pos_history', $history, '', $before_change);

	// Update coupon used times
	$results = mysqli_query ( $dbc, "UPDATE `pos_touch_coupons` SET `used_times`=`used_times`-1 WHERE `couponid`='$couponid'" );
}


/* Hold order */
if ( $_GET['fill'] == 'posTouchHoldOrder' ) {
	$orderid	= $_GET['orderid'];
	$comments	= $_GET['comments'];
	$order_date	= date('Y-m-d H:i:s');

	if ( isset($_SESSION['orderid']) && !empty($orderid) ) {
		$before_change = capture_before_change($dbc, 'pos_touch_temp_order', 'hold_order', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'date_time', 'orderid', $orderid);
		$before_change .= capture_before_change($dbc, 'pos_touch_temp_order', 'comments', 'orderid', $orderid);

		$results = mysqli_query ( $dbc, "UPDATE `pos_touch_temp_order` SET `hold_order`=1, `date_time`='$order_date', `comments`='$comments' WHERE `orderid`='$orderid'" );

		$history = capture_after_change('hold_order', 1);
		$history .= capture_after_change('date_time', $order_date);
		$history .= capture_after_change('comments', $comments);

		add_update_history($dbc, 'pos_history', $history, '', $before_change);

		if ( mysqli_affected_rows ($dbc) ) {
			unset ( $_SESSION['orderid'] );
		}
	}
}


/* Service held order */
if ( $_GET['fill'] == 'posTouchServiceHeldOrder' ) {
	$orderid = $_GET['orderid'];

	if ( !empty($orderid) ) {
		$_SESSION['orderid'] = $orderid;
	}
}


/* Cancel held order */
if ( $_GET['fill'] == 'posTouchCancelHeldOrder' ) {
	$orderid = $_GET['orderid'];

	if ( !empty($orderid) ) {
		$results = mysqli_query ( $dbc, "DELETE FROM `pos_touch_temp_order` WHERE `orderid`='$orderid'" );
		$results = mysqli_query ( $dbc, "DELETE FROM `pos_touch_temp_order_products` WHERE `orderid`='$orderid'" );
	}
}


/* Cancel or Complete order */
if ( $_GET['fill'] == 'posTouchCancelOrder' ) {

	if ( isset($_SESSION['orderid']) ) {
		$orderid = $_SESSION['orderid'];
		$results = mysqli_query ( $dbc, "DELETE FROM `pos_touch_temp_order` WHERE `orderid`='$orderid'" );
		$results = mysqli_query ( $dbc, "DELETE FROM `pos_touch_temp_order_products` WHERE `orderid`='$orderid'" );
		unset ( $_SESSION['orderid'] );
	}
}


/* Email Receipt */
if ( $_GET['fill'] == 'posTouchEmailReceipt' ) {
	$posid		= $_GET['posid'];
	$attachment	= $_GET['attachment'];
	$to_email	= $_GET['to_email'];
	$to_client	= $_GET['to_client'];

	if ( !empty($to_client) ) {
		$to_email = get_email($dbc, $to_client);
	}

	if ( !empty($to_email) && !empty($attachment) ) {
		send_email('', $to_email, '', '', 'Sales Invoice', 'Please find the invoice attached.', $attachment);
	}
}


/* Barcode Scanner Inventory */
if ( $_GET['fill'] == 'barcodeScanner' ) {
	$code     = filter_var($_GET['code'], FILTER_SANITIZE_STRING);
    $invid    = '';
    $category = '';

    $get_inv = mysqli_query($dbc, "SELECT `inventoryid`, `category` FROM `inventory` WHERE (`code`='$code' OR `part_no`='$code')");
    if ( $get_inv->num_rows > 0 ) {
        while ( $row=mysqli_fetch_assoc($get_inv) ) {
            $invid    = $row['inventoryid'];
            $category = $row['category'];
        }
    }

    echo $invid .'*#*'. $category;
}


/* Barcode Scanner Products */
if ( $_GET['fill'] == 'barcodeScannerProducts' ) {
	$code = filter_var($_GET['code'], FILTER_SANITIZE_STRING);
    $name = '';

    $get_inv = mysqli_query($dbc, "SELECT `productid`, `heading` FROM `products` WHERE `product_code`='$code'");
    if ( $get_inv->num_rows > 0 ) {
        while ( $row=mysqli_fetch_assoc($get_inv) ) {
            $name = $row['heading'];
        }
    }

    echo $name;
}
?>
