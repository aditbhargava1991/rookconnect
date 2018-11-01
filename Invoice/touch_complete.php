<?php
/* ----- Complete The Order ----- */
if ( $complete===TRUE ) {
	$invoice_date		= date('Y-m-d');
	$discount_type		= '%';
	$discount_amount = -1;
	
	$created_by		= $_SESSION['contactid'];
	$today = date('Y-m-d');

	// Get GST Rate
	$get_pos_tax	= get_config($dbc, 'invoice_tax');
	$pdf_tax		= '';
	$gst_rate		= 0;
	$pst_rate		= 0;
	
	if ( $get_pos_tax != '' ) {
		foreach(explode('*#*',$get_pos_tax) as $pos_tax) {
			$pos_tax = explode('**',$pos_tax);
			if(strtolower($pos_tax[0]) == 'gst') {
				$gst_rate = $pos_tax[1];
			} else if(strtolower($pos_tax[0]) == 'pst' && ($pos_tax[3] != 'Yes' || $client_tax_exemption != 'Yes')) {
				$pst_rate = $pos_tax[1];
			}
		}
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

	// Create Invoice Number
	mysqli_query($dbc, "INSERT INTO `invoice` (`invoice_date`) VALUES ('$today')");
	$invoiceid = mysqli_insert_id($dbc);
	$staff = 0;
	$service_ids = [];
	$service_fees = [];
	$service_admin = [];
	$service_paid = [];
	$inventory_ids = [];
	$inventory_price = [];
	$inventory_qty = [];
	$inventory_paid = [];
	$product_ids = [];
	$product_price = [];
	$product_qty = [];
	$product_total = [];
	$product_paid = [];
	
	$get_values = mysqli_query ( $dbc, "SELECT `o`.*, `p`.* FROM `pos_touch_temp_order` AS `o` INNER JOIN `pos_touch_temp_order_products` AS `p` ON (`o`.`orderid` = `p`.`orderid`) WHERE `o`.`orderid`='$orderid'" );
	$items = mysqli_num_rows($get_values);
	while ( $row=mysqli_fetch_assoc($get_values) ) {
		$staffid			= $row['staffid'];
		$custid				= $row['custid'];
		$bookingid			= $row['bookingid'];
		$productpricing		= $row['inventory_pricing'];
		$sub_total_before_discount = $row['sub_total_before_discount'];
		if($discount_amount < 0) {
			$discount_amount	= $row['discount_amount'];
		}
		$couponid			= $row['couponid'];
		$coupon_value		= $row['coupon_value'];
		$promoid			= $row['promoid'];
		$promo_value		= $row['promo_value'];
		$giftcardid 		= $row['giftcardid'];
		$giftcard_value 	= $row['giftcard_value'];
		$sub_total			= $row['sub_total'];
		$gst_total			= $row['gst_total'];
		$pst_total			= $row['pst_total'];
		$order_total		= $row['order_total'];
		$payment_amt		= $order_total;
		
		$payments			= [[$payment_type,$order_total]];
		if($giftcard_value > 0) {
			$payment_type 	= 'Gift Card,'.$payment_type;
			$order_total	+= $giftcard_value;
			$payment_amt	= $giftcard_value.','.$payment_amt;
			$payments[]		= ['Gift Card',$giftcard_value];
		}
		$payment_type		= $payment_type.'#*#'.$payment_amt;
        $unit_price         = ( $row['quantity'] > 0 ) ? $row['total'] / $row['quantity'] : $row['total'];
		
		if($row['serviceid'] > 0) {
			$service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` LEFT JOIN `company_rate_card` ON `services`.`serviceid`=`company_rate_card`.`item_id` AND `company_rate_card`.`tile_name` LIKE 'Services' WHERE `services`.`serviceid`='".$row['serviceid']."' AND `services`.`deleted`=0 AND IFNULL(NULLIF(`include_in_pos`,''),1) > 0 AND `company_rate_card`.`deleted`=0 AND (`company_rate_card`.`end_date` >= NOW() OR `company_rate_card`.`end_date` = '0000-00-00')"));
			$service_ids[] = $service['serviceid'];
			$service_fees[] = $row['total'];
			$service_admin[] = $service['admin_fee'];
			$service_paid[] = $row['total'];
			$gst = ($service['gst_exempt'] > 0 ? 0 : ($row['total'] - ($discount_amount / $items)) * $gst_rate / 100);
			$pst = ($service['gst_exempt'] > 0 ? 0 : ($row['total'] - ($discount_amount / $items)) * $pst_rate / 100);
			$total = $row['total'] + $gst + $pst;
			mysqli_query($dbc, "INSERT INTO `invoice_lines` (`invoiceid`, `item_id`, `type`, `description`, `category`, `heading`, `compensation`, `quantity`, `unit_price`, `admin_fee`, `tax_exempt`, `sub_total`, `pst`, `gst`, `total`) VALUES ('$invoiceid', '".$service['serviceid']."', 'General', '".$service['heading']."', 'service', '', '".($staffid > 0 ? 1 : 0)."', '".$row['quantity']."', '$unit_price', '".$service['admin_fee']."', '".$service['gst_exempt']."', '".$row['total']."', '$pst', '$gst', '$total')");
			$line_id = mysqli_insert_id($dbc);
			foreach($payments as $i => $payment) {
				if($payment[1] > 0 && $total > 0) {
					$paid = ($total > $payment[1] ? $payment[1] : $total);
					$gst_paid = $gst * ($paid / $total);
					mysqli_query($dbc, "INSERT INTO `invoice_patient` (`invoiceid`, `invoice_date`, `patientid`, `sub_total`, `gst_amt`, `patient_price`, `service_category`, `service_name`, `line_id`, `paid`, `paid_date`) VALUES ('$invoiceid', '$today', '$custid', '".$row['total']."', '$gst_paid', '$paid', '".$service['category']."', '".$service['heading']."', '$line_id', '".$payment[0]."', '$today')");
					$payments[$i][1] -= $paid;
					$total -= $paid;
					$gst -= $gst_paid;
				}
			}
			if($staffid > 0) {
                $comp_rate = $dbc->query("SELECT `comp_percent` FROM `rate_compensation` LEFT JOIN `rate_card` ON `rate_compensation`.`rate_card`=`rate_card`.`ratecardid` WHERE `rate_card`.`clientid`='$staffid' AND `rate_card`.`clientid` > 0 AND `rate_card`.`deleted`=0 AND `rate_card`.`on_off` > 0 AND `rate_compensation`.`item_type`='services' AND `rate_compensation`.`deleted`=0 AND DATE(NOW()) BETWEEN `rate_card`.`start_date` AND `rate_card`.`end_date` UNION SELECT '100' `comp_percent`")->fetch_assoc()['comp_percent'];
                $comp_amt = ($service['cust_price'] - $service['admin_fee']) * ($comp_rate / 100);
				mysqli_query($dbc, "INSERT INTO `invoice_compensation` (`invoiceid`, `contactid`, `item_type`, `item_id`, `fee`, `admin_fee`, `comp_percent`, `compensation`, `qty`, `service_date`) VALUES ('$invoiceid', '$staffid', 'services', '".$service['serviceid']."', '".$service['cust_price']."', '".$service['admin_fee']."', '$comp_rate', '$comp_amt', '1', '$today')");
				$staff = $staffid;
			}
		}
		if($row['inventoryid'] > 0) {
			$inventory = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `inventory` WHERE `inventoryid`='".$row['inventoryid']."' AND `deleted`=0"));
			$inventory_ids[] = $row['inventoryid'];
			$inventory_price[] = $row['total'] / $row['quantity'];
			$inventory_qty[] = $row['quantity'];
			$inventory_paid[] = $row['total'];
			$gst = ($inventory['gst_exempt'] > 0 ? 0 : ($row['total'] - ($discount_amount / $items)) * $gst_rate / 100);
			$pst = ($inventory['gst_exempt'] > 0 ? 0 : ($row['total'] - ($discount_amount / $items)) * $pst_rate / 100);
			$total = $row['total'] + $gst + $pst;
			mysqli_query($dbc, "INSERT INTO `invoice_lines` (`invoiceid`, `item_id`, `type`, `description`, `category`, `heading`, `compensation`, `quantity`, `unit_price`, `admin_fee`, `tax_exempt`, `sub_total`, `pst`, `gst`, `total`) VALUES ('$invoiceid', '".$row['inventoryid']."', 'General', '".$inventory['product_name']."', 'inventory', '', '0', '".$row['quantity']."', '$unit_price', '0', '".$inventory['gst_exempt']."', '".$row['total']."', '$pst', '$gst', '$total')");
			$line_id = mysqli_insert_id($dbc);
			foreach($payments as $i => $payment) {
				if($payment[1] > 0 && $total > 0) {
					$paid = ($total > $payment[1] ? $payment[1] : $total);
					$gst_paid = $gst * ($paid / $total);
					mysqli_query($dbc, "INSERT INTO `invoice_patient` (`invoiceid`, `invoice_date`, `patientid`, `sub_total`, `gst_amt`, `patient_price`, `service_category`, `service_name`, `line_id`, `paid`, `paid_date`) VALUES ('$invoiceid', '$today', '$custid', '".$row['total']."', '$gst_paid', '$paid', '".$service['category']."', '".$service['heading']."', '$line_id', '".$payment[0]."', '$today')");
					$payments[$i][1] -= $paid;
					$total -= $paid;
					$gst -= $gst_paid;
				}
			}
			if($staffid > 0) {
                $comp_rate = $dbc->query("SELECT `comp_percent` FROM `rate_compensation` LEFT JOIN `rate_card` ON `rate_compensation`.`rate_card`=`rate_card`.`ratecardid` WHERE `rate_card`.`clientid`='$staffid' AND `rate_card`.`clientid` > 0 AND `rate_card`.`deleted`=0 AND `rate_card`.`on_off` > 0 AND `rate_compensation`.`item_type`='inventory' AND `rate_compensation`.`deleted`=0 AND DATE(NOW()) BETWEEN `rate_card`.`start_date` AND `rate_card`.`end_date` UNION SELECT '100' `comp_percent`")->fetch_assoc()['comp_percent'];
                $comp_amt = ($row['total']) * ($comp_rate / 100);
				mysqli_query($dbc, "INSERT INTO `invoice_compensation` (`invoiceid`, `contactid`, `item_type`, `item_id`, `fee`, `admin_fee`, `comp_percent`, `compensation`, `qty`, `service_date`) VALUES ('$invoiceid', '$staffid', 'inventory', '".$row['inventoryid']."', '".$row['total']."', '0', '$comp_rate', '$comp_amt', '".$row['quantity']."', '$today')");
				$staff = $staffid;
			}
			
			//Update the inventory stock
			$query_update_inventory = "UPDATE `inventory` SET `quantity`=(`quantity`-'$quantity') WHERE `inventoryid`='$inventoryid'";
			$results_are_in = mysqli_query ( $dbc, $query_update_inventory );
		}
		if($row['productid'] > 0) {
			$product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `products` WHERE `productid`='".$row['productid']."' AND `deleted`=0"));
			$product_ids[] = $row['productid'];
			$product_price[] = $row['total'] / $row['quantity'];
			$product_qty[] = $row['quantity'];
			$product_paid[] = $row['total'];
			$gst = ($product['gst_exempt'] > 0 ? 0 : ($row['total'] - ($discount_amount / $items)) * $gst_rate / 100);
			$pst = ($product['gst_exempt'] > 0 ? 0 : ($row['total'] - ($discount_amount / $items)) * $pst_rate / 100);
			$product_total[] = $row['total'] + $gst + $pst;
			mysqli_query($dbc, "INSERT INTO `invoice_lines` (`invoiceid`, `item_id`, `type`, `description`, `category`, `heading`, `compensation`, `quantity`, `unit_price`, `admin_fee`, `tax_exempt`, `sub_total`, `pst`, `gst`, `total`) VALUES ('$invoiceid', '".$row['productid']."', 'General', '".$product['heading']."', 'product', '', '0', '".$row['quantity']."', '$unit_price', '0', '".$product['gst_exempt']."', '".$row['total']."', '$pst', '$gst', '".($row['total'] + $gst)."')");
			$line_id = mysqli_insert_id($dbc);
			foreach($payments as $i => $payment) {
				if($payment[1] > 0 && $total > 0) {
					$paid = ($row['total'] + $gst > $payment[1] ? $payment[1] : $row['total'] + $gst);
					$gst_paid = $gst * ($paid / $row['total'] + $gst);
					mysqli_query($dbc, "INSERT INTO `invoice_patient` (`invoiceid`, `invoice_date`, `patientid`, `sub_total`, `gst_amt`, `patient_price`, `service_category`, `service_name`, `line_id`, `paid`, `paid_date`) VALUES ('$invoiceid', '$today', '$custid', '".$row['total']."', '$gst_paid', '$paid', '".$service['category']."', '".$service['heading']."', '$line_id', '".$payment[0]."', '$today')");
					$payments[$i][1] -= $paid;
					$total -= $paid;
					$gst -= $gst_paid;
				}
			}
			if($staffid > 0) {
                $comp_rate = $dbc->query("SELECT `comp_percent` FROM `rate_compensation` LEFT JOIN `rate_card` ON `rate_compensation`.`rate_card`=`rate_card`.`ratecardid` WHERE `rate_card`.`clientid`='$staffid' AND `rate_card`.`clientid` > 0 AND `rate_card`.`deleted`=0 AND `rate_card`.`on_off` > 0 AND `rate_compensation`.`item_type`='product' AND `rate_compensation`.`deleted`=0 AND DATE(NOW()) BETWEEN `rate_card`.`start_date` AND `rate_card`.`end_date` UNION SELECT '100' `comp_percent`")->fetch_assoc()['comp_percent'];
                $comp_amt = ($row['total']) * ($comp_rate / 100);
				mysqli_query($dbc, "INSERT INTO `invoice_compensation` (`invoiceid`, `contactid`, `item_type`, `item_id`, `fee`, `admin_fee`, `comp_percent`, `compensation`, `qty`, `service_date`) VALUES ('$invoiceid', '$staffid', 'product', '".$row['productid']."', '".$row['total']."', '0', '$comp_rate', '$comp_amt', '".$row['quantity']."', '$today')");
				$staff = $staffid;
			}
		}
	}
	
	// Update Invoice
	$result_invoice = mysqli_query($dbc, "UPDATE `invoice` SET `bookingid`='$bookingid', `patientid`='$custid', `therapistsid`='$staff', `service_date`='$today', `pricing`='$productpricing', `serviceid`='".implode(',',$service_ids)."', `fee`='".implode(',',$service_fees)."', `admin_fee`='".implode(',',$service_admin)."', `service_patient`='".implode(',',$service_paid)."', `inventoryid`='".implode(',',$inventory_ids)."', `sell_price`='".implode(',',$inventory_price)."', `quantity`='".implode(',',$inventory_qty)."', `inventory_patient`='".implode(',',$inventory_paid)."', `productid`='".implode(',',$product_ids)."', `product_price`='".implode(',',$product_price)."', `product_qty`='".implode(',',$product_qty)."', `product_total`='".implode(',',$product_total)."', `product_patient`='".implode(',',$product_paid)."', `total_price`='$sub_total', `pst_amt`='$pst_total', `gst_amt`='$gst_total', `promotionid`='$promoid', `giftcardid`='$giftcardid', `discount`='$discount_amount', `final_price`='$order_total', `paid`='Yes', `payment_type`='$payment_type' WHERE `invoiceid`='$invoiceid'");

	// Gift Cards
	$giftcard = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `pos_giftcards` WHERE `posgiftcardsid` = '$giftcardid'"));
	$giftcard_used_value = $giftcard['used_value'] + $giftcard_value;
	$giftcard_paid = $giftcard_value;
	mysqli_query($dbc, "UPDATE `pos_giftcards` SET `used_value` = '$giftcard_used_value' WHERE `posgiftcardsid` = '$giftcardid'");
	// mysqli_query($dbc, "INSERT INTO `invoice_patient` (`invoiceid`, `invoice_date`, `patientid`, `sub_total`, `gst_amt`, `gratuity_portion`, `patient_price`, `service_category`, `service_name`, `product_name`, `paid`, `paid_date`) VALUES ('$invoiceid', '$today', '$custid', '$giftcard_paid', '0', '', '$giftcard_paid', '', '', '', 'Gift Card', '$today')");
	
	// Add Payment
	$result_payment = mysqli_query($dbc, "INSERT INTO `invoice_payment` (`invoiceid`, `contactid`, `payer_id`, `gst`, `amount`, `paid`, `payment_method`, `date_paid`) VALUES ('$invoiceid', '$custid', '$custid', '$gst_total', '$order_total', 1, '$payment_type', '$today')");
	
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
	$invoice_design = get_config($dbc, 'invoice_design');
	switch($invoice_design) {
		case 2:
			include('pos_invoice_2.php');
			break;
		case 3:
			include('pos_invoice_3.php');
			break;
		case 4:
			include ('patient_invoice_pdf.php');
			if($insurerid != '') {
				include ('insurer_invoice_pdf.php');
			}
			break;
		case 5:
            include('pos_invoice_small.php');
			break;
		case 'service':
            include('pos_invoice_service.php');
			break;
		case 'pink':
			include ('pos_invoice_pink.php');
			break;
		case 'cnt1':
			include ('pos_invoice_contractor_1.php');
			break;
		case 'cnt2':
			include ('pos_invoice_contractor_2.php');
			break;
		case 'cnt3':
			include ('pos_invoice_contractor_3.php');
			break;
        case 'custom_ticket':
            include ('pos_invoice_custom_ticket.php');
            break;
		case 1:
        default:
			include('pos_invoice_1.php');
			break;
	}

	if ( $edit_id == '0' ) {
		$edited = '';
	} else {
		$edited = '_' . $edit_id;
	}

	if ( $rookconnect == 'washtech') {
		$to_email = 'troy@washtech.ca';
		$attachment = 'download/invoice_' . $invoiceid . '.pdf';
		send_email('', $to_email, '', '', 'Washtech Invoice', 'Please see Attachment for Invoice', $attachment);
	}
	
	$attachment = 'download/invoice_' . $invoiceid . '.pdf';

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
		window.open("download/invoice_' . $invoiceid . '.pdf", "fullscreen=yes");
		// window.location.replace("touch_main.php?email_reciept=yes&posid=' . $invoiceid . '&customerid=' . $custid . '");
		cancelOrder();
	</script>';
	
	$complete = FALSE;
}
?>