<?php
/* ----- Complete The Order ----- */
if ( $complete===TRUE ) {
	$invoice_date		= date('Y-m-d');
	$discount_type		= '%';

	$get_values = mysqli_query ( $dbc, "SELECT `o`.*, `p`.* FROM `pos_touch_temp_order` AS `o` INNER JOIN `pos_touch_temp_order_products` AS `p` ON (`o`.`orderid` = `p`.`orderid`) WHERE `o`.`orderid`='$orderid'" );

	while ( $row=mysqli_fetch_assoc($get_values) ) {
		$custid				= $row['custid'];
		$productpricing		= $row['inventory_pricing'];
		$sub_total_before_discount = $row['sub_total_before_discount'];
		$discount_amount	= $row['discount_amount'];
		$couponid			= $row['couponid'];
		$coupon_value		= $row['coupon_value'];
		$sub_total			= $row['sub_total'];
		$gst_total			= $row['gst_total'];
		$pst_total			= $row['pst_total'];
		$order_total		= $row['order_total'];
	}

	$payment_type = '';
	if ( isset ( $_GET['payment_type'] ) ) {
		if ( trim ( $_GET['payment_type'] ) == 'cash' ) {
			$payment_type = 'Cash';
		}
		if ( trim ( $_GET['payment_type'] ) == 'debit' ) {
			$payment_type = 'Debit';
		}
		if ( trim ( $_GET['payment_type'] ) == 'visa' ) {
			$payment_type = 'Visa';
		}
		if ( trim ( $_GET['payment_type'] ) == 'master' ) {
			$payment_type = 'Mastercard';
		}
		if ( trim ( $_GET['payment_type'] ) == 'amex' ) {
			$payment_type = 'American Express';
		}
	}

	$status = 'Completed';

	//if ( strpos($value_config, ','."Send Outbound Invoice".',') !== FALSE ) {
		$pdf_product	= '';
		$created_by		= $_SESSION['contactid'];

		// Add new POS
		$query_insert_invoice = "INSERT INTO `point_of_sell` (`invoice_date`, `contactid`, `productpricing`, `sub_total`, `discount_type`, `discount_value`, `total_after_discount`, `couponid`, `coupon_value`, `gst`, `pst`, `total_before_tax`, `total_price`, `payment_type`, `created_by`, `comment`, `status`, `updatedtotal`) VALUES ('$invoice_date', $custid, '$productpricing', '$sub_total_before_discount', '$discount_type', '$discount_amount', '$sub_total', '$couponid', '$coupon_value', '$gst_total', '$pst_total', '$sub_total', '$order_total', '$payment_type', '$created_by', '', '$status', '$order_total')";
		$results_are_in = mysqli_query($dbc, $query_insert_invoice);

		$posid = mysqli_insert_id($dbc);

		$before_change = '';
		$history = "Point of Sale entry Added. <br />";
		add_update_history($dbc, 'pos_history', $history, '', $before_change);

		// Add order to Service Queue
		if ( strpos ( $value_config, ',Service Queue,') !== FALSE ) {
			$query_service	= "INSERT INTO `service_queue` (`posid`, `inv_date`) VALUES ('$posid', '$invoice_date')";
			$result_service	= mysqli_query($dbc, $query_service);
		}

		// ADD Column in Table for PDF
		$col = "SELECT `type_category` FROM `point_of_sell_product`";
		$result = mysqli_query($dbc, $col);

		if (!$result){
			$colcreate = "ALTER TABLE `point_of_sell_product` ADD COLUMN `type_category` VARCHAR(555) NULL";
			$result = mysqli_query($dbc, $colcreate);
		}

		/* Reduce the Inventory quantity, if the product is created as a Bill of Material */
		$orderid_q = $_SESSION['orderid'];
		$result = mysqli_query ( $dbc, "SELECT `to`.`orderlistid`, `to`.`productid`, `to`.`quantity` AS `order_quantity`, `inv`.`inventoryid`, `inv`.`bill_of_material` FROM `pos_touch_temp_order_products` AS `to` JOIN `products` AS `p` ON (`to`.`productid`=`p`.`productid`) JOIN `inventory` AS `inv` ON (`p`.`inventoryid`=`inv`.`inventoryid`) WHERE `to`.`orderid`='$orderid_q'" );
		$num_rows = mysqli_num_rows($result);

		if ( $num_rows>0 ) {
			// Assembled product
			while ( $row=mysqli_fetch_assoc($result) ) {
				$bill_of_material	= $row['bill_of_material']; // Contains comma separated Inventory IDs
				$bill_of_material	= explode( ',', $bill_of_material );
				$order_quantity		= $row['order_quantity'];
				$count				= count($bill_of_material);
				for ( $i=0; $i<$count; $i++ ) {
					$each_bom = $bill_of_material[$i];
					$update_result = mysqli_query ( $dbc, "UPDATE `inventory` SET `quantity`=(`quantity` - '$order_quantity') WHERE `inventoryid`='$each_bom'" );
				}
			}
		}


		$results = mysqli_query( $dbc, "SELECT * FROM `pos_touch_temp_order_products` WHERE `orderid`='$orderid'" );

		if ( $results->num_rows > 0 ) {
			while ( $row = mysqli_fetch_assoc($results) ) {
				$inventoryid	= $row['inventoryid'];
				$productid		= $row['productid'];
				$serviceid		= $row['serviceid'];

				// Add Inventory items
				if ( !empty( $inventoryid ) || $inventoryid != NULL ) {
					$quantity		= $row['quantity'];
					$price			= number_format ( $row['total'], 2 );
					$query_insert_invoice = "INSERT INTO `point_of_sell_product` (`posid`, `inventoryid`, `quantity`, `price`, `type_category`) VALUES ('$posid', '$inventoryid', '$quantity', '$price', 'inventory')";
					$results_are_in = mysqli_query($dbc, $query_insert_invoice);

					$before_change = '';
	        $history = "Point of Sale products Added. <br />";
	        add_update_history($dbc, 'pos_history', $history, '', $before_change);

					//Update Inventory table to reduce the quantity
					$query_update_inventory = "UPDATE `inventory` SET `quantity`=(`quantity`-'$quantity') WHERE `inventoryid`='$inventoryid'";
					$results_are_in = mysqli_query ( $dbc, $query_update_inventory );
				}

				// Add Products
				if ( !empty( $productid ) || $productid != NULL ) {
					$quantity		= $row['quantity'];
					$price			= number_format ( $row['total'], 2 );
					$query_insert_invoice = "INSERT INTO `point_of_sell_product` (`posid`, `inventoryid`, `quantity`, `price`, `type_category`) VALUES ('$posid', '$productid', '$quantity', '$price', 'product')";
					$results_are_in = mysqli_query($dbc, $query_insert_invoice);

					$before_change = '';
	        $history = "Point of Sale products Added. <br />";
	        add_update_history($dbc, 'pos_history', $history, '', $before_change);
				}

				// Add Services
				if ( !empty( $serviceid ) || $serviceid != NULL ) {
					$quantity		= $row['quantity'];
					$price			= number_format ( $row['total'], 2 );
					$query_insert_invoice = "INSERT INTO `point_of_sell_product` (`posid`, `inventoryid`, `quantity`, `price`, `type_category`) VALUES ('$posid', '$serviceid', '$quantity', '$price', 'service')";
					$results_are_in = mysqli_query($dbc, $query_insert_invoice);

					$before_change = '';
	        $history = "Point of Sale products Added. <br />";
	        add_update_history($dbc, 'pos_history', $history, '', $before_change);
				}
			}
		}

		// Add Services
		/*
		for ( $i=0; $i<count($_POST['servinventoryid']); $i++ ) {
			$inventoryid	= $_POST['servinventoryid'][$i];
			$price			= $_POST['servprice'][$i];
			$quantity		= $_POST['servquantity'][$i];

			if($inventoryid != '') {
				$query_insert_invoice = "INSERT INTO `point_of_sell_product` (`posid`, `inventoryid`, `quantity`, `price`, `type_category`) VALUES ('$posid', '$inventoryid', '$quantity', '$price', 'service')";
				$results_are_in = mysqli_query($dbc, $query_insert_invoice);
			}
		}
		*/

		// Add Miscellaneous items
		/*
		for ( $i=0; $i<count($_POST['misc_product']); $i++ ) {
			$misc_product	= filter_var($_POST['misc_product'][$i],FILTER_SANITIZE_STRING);
			$misc_price		= $_POST['misc_price'][$i];
			$misc_quantity	= $_POST['misc_quantity'][$i];

			if($misc_product != '') {
				$query_insert_invoice = "INSERT INTO `point_of_sell_product` (`posid`, `misc_product`, `price`, `quantity`, `type_category`) VALUES ('$posid', '$misc_product', '$misc_price', '$misc_quantity', 'misc product')";
				$results_are_in = mysqli_query($dbc, $query_insert_invoice);
			}
		}
		*/
	//}

	$comments		= '';
	$edit_id		= 0;
	$rookconnect	= get_software_name();
	$company_software_name = '';

	switch($rookconnect) {
		case 'sea':
			$company_software_name = 'Smart Energy Alternates';
		case 'washtech':
			$company_software_name = 'Washtech';
		case 'highland':
			$company_software_name = 'Highland Projects';
		case 'breakthebarrier':
			$company_software_name = 'Break The Barrier Innovation';
		case 'beirut':
			$company_software_name = 'Beirut Street Food';
		default:
			$company_software_name = 'ROOK Connect';
	}

	// Generate PDF Invoice
	// Make sure the logo and design is selected from POS Settings
	include ('create_pos_pdf.php');
	$pos_design = get_config($dbc, 'pos_design');

	if ( $pos_design == 1 ) {
		echo create_pos1_pdf($dbc, $posid, $discount_amount, $comments, $gst_total, $pst_total, $rookconnect, $edit_id);
	}
	if ( $pos_design == 2 ) {
		echo create_pos2_pdf($dbc, $posid, $discount_amount, $comments, $gst_total, $pst_total, $rookconnect, $edit_id);
	}
	if ( $pos_design == 3 ) {
		echo create_pos3_pdf($dbc, $posid, $discount_amount, $comments, $gst_total, $pst_total, $rookconnect, $edit_id, $company_software_name);
	}

	if ( $edit_id == '0' ) {
		$edited = '';
	} else {
		$edited = '_' . $edit_id;
	}

	if ( $rookconnect == 'washtech') {
		$to_email = 'troy@washtech.ca';
		$attachment = 'download/invoice_' . $posid . $edited . '.pdf';
		send_email('', $to_email, '', '', 'Washtech Invoice', 'Please see Attachment for Invoice', $attachment);
	}

	$attachment = 'download/invoice_' . $posid . $edited . '.pdf';

	/*
	if($payment_type == 'Net 30 Days' || $payment_type == 'Net 30') {
		$send_invoice = $_POST['send_invoice'];
		if($send_invoice == 1) {
			$send_email = get_config($dbc, 'invoice_outbound_email');
			$arr_email=explode(",",$send_email);
			$attachment = 'download/invoice_'.$posid.'.pdf';
			//send_email('', $arr_email, '', '', 'Outbound Invoice', 'Please see Attachment for Outbound Invoice', $attachment);
		}
	}
	*/

	echo '
	<script type="text/javascript">
		window.open("download/invoice_' . $posid . $edited . '.pdf", "fullscreen=yes");
		window.location.replace("pos_touch.php?email_reciept=yes&posid=' . $posid . '&customerid=' . $custid . '");
		cancelOrder();
	</script>';

	$complete = FALSE;
}
?>
