<?php
/*
 * Point of Sale
 * Tax exemption is applied to Businesses that has Tax Exemption enabled and having a Tax Exemption Number
*/
include ('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

// Get software name
$rookconnect = get_software_name();
if ( $_SERVER['SERVER_NAME']=='sea-alberta.rookconnect.com' ) {
    $dbc_led = mysqli_connect('localhost', 'led_rook_usr', 'pUnaibiS!273', 'led_rook_db');
}
if ( $rookconnect=='led' ) {
    $dbc_sea_ab = mysqli_connect('localhost', 'sea_software_use', 'dRagonflY!306', 'sea_alberta_db');
}

$get_invoice =	mysqli_query($dbc,"SELECT `posid` FROM `point_of_sell` WHERE `invoice_date` + INTERVAL 30 DAY < NOW() AND status!='Completed' AND `status`!='Void'");
$num_rows = mysqli_num_rows($get_invoice);
if($num_rows > 0) {
    while($row = mysqli_fetch_array( $get_invoice )) {
        $posid = $row['posid'];

    $before_change = capture_before_change($dbc, 'point_of_sell', 'status', 'posid', $posid);

		$query_update_project = "UPDATE `point_of_sell` SET status = 'Posted Past Due' WHERE `posid` = '$posid'";
		$result_update_project = mysqli_query($dbc, $query_update_project);

    $history = capture_after_change('status', 'Posted Past Due');
    add_update_history($dbc, 'pos_history', $history, '', $before_change);
    }
}

if (isset($_POST['submit_pos'])) {
    $invoice_date = $_POST['invoice_date'];

    if($invoice_date == '') {
        $invoice_date = date('Y-m-d');
    }
    $contactid = $_POST['contactid'];

    $productpricing = $_POST['productpricing'];

    $sub_total = $_POST['sub_total'];

    $discount_value = '0';
    if($_POST['discount_value'] != 0) {
        $discount_type = $_POST['discount_type'];
        $discount_value = $_POST['discount_value'];
        if($discount_type == '%') {
            $d_value =  $discount_value.'%';
        }
        if($discount_type == '$') {
            $d_value =  '$'.$discount_value;
        }

		$total_after_discount = $_POST['total_after_discount'];
    } else {
		$total_after_discount = '0.00';
	}

    //$total_after_discount = $_POST['total_after_discount'];
    $delivery = filter_var($_POST['delivery'],FILTER_SANITIZE_STRING);
    $assembly = filter_var($_POST['assembly'],FILTER_SANITIZE_STRING);
	$assembly = ( !empty ($assembly) || $assembly != NULL ) ? $assembly : '0.00';

    $delivery_type = filter_var($_POST['delivery_type'],FILTER_SANITIZE_STRING);
    $delivery_address = filter_var($_POST['delivery_address'],FILTER_SANITIZE_STRING);

    $contractorid = filter_var($_POST['contractorid'],FILTER_SANITIZE_STRING);
	$contractorid = ( !empty ($contractorid) || $contractorid != NULL ) ? $contractorid : '0';

    $total_before_tax = $_POST['total_before_tax'];

    $tax_exemption_number = $_POST['tax_exemption_number'];
    $client_tax_exemption = $_POST['client_tax_exemption'];
    $total_tax = $_POST['tax_rate'];

	$addorselect2 = $_POST['addorselect2'];
	if($addorselect2 == '2') {
			$customer_name = encryptIt($_POST['customer_name']);
			$customer_phone = encryptIt($_POST['cusphone']);
			$email = encryptIt($_POST['email']);
			$cuscateg1 = $_POST['cuscateg1'];
			$reference = $_POST['reference'];
             $query5_in = "INSERT INTO `contacts` (`name`, `category`, `office_phone`, `email_address`, `referred_by`, `client_tax_exemption`, `tax_exemption_number`) VALUES ('$customer_name', '$cuscateg1', '$customer_phone', '$email', '$reference', '$client_tax_exemption', '$tax_exemption_number')";
             $results_in = mysqli_query($dbc, $query5_in);
             $contactid = mysqli_insert_id($dbc);
	} else {
        $reference  = $_POST['referred_by'];
        $query_ref  = "UPDATE `contacts` SET `referred_by`='$reference' WHERE `contactid`='$contactid'";
        $result_ref = mysqli_query($dbc, $query_ref);
    }

    // GST PST
    $get_pos_tax = get_config($dbc, 'pos_tax');
    $pdf_tax = '';
    $gst_total = 0;
    $pst_total = 0;
    if($get_pos_tax != '') {
        $pos_tax = explode('*#*',$get_pos_tax);

        $total_count = mb_substr_count($get_pos_tax,'*#*');
        for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
            $pos_tax_name_rate = explode('**',$pos_tax[$eq_loop]);

            if (strcasecmp($pos_tax_name_rate[0], 'gst') == 0) {
                $gst_total = number_format((($total_before_tax*$pos_tax_name_rate[1])/100), 2);
            }

            if (strcasecmp($pos_tax_name_rate[0], 'pst') == 0) {
                if($pos_tax_name_rate[3] == 'Yes' && $client_tax_exemption == 'Yes') {
                    $pst_total = 0;
                } else {
                    $pst_total = number_format((($total_before_tax*$pos_tax_name_rate[1])/100), 2);
                }
            }
        }
    }
    // GST PST

    if($_POST['gf_number'] != '' && $_POST['gf_number'] != 0) {
      $giftcard_number = $_POST['gf_number'];
      mysqli_query($dbc, "update pos_giftcards set status = 1 where giftcard_number = '$giftcard_number' ");
    }

    $total_price = $_POST['total_price'];
	$dep_paid = round($_POST['deposit_paid'],2);
	$updatedtotal = round($total_price - $dep_paid,2);
    $payment_type = $_POST['payment_type'];

	$status = 'Completed';
    //if($payment_type == 'Net 30 Days' || $payment_type == 'On Account' || ($payment_type !== 'Pay Now' && $payment_type !== '' && $payment_type !== NULL)  ) {
	if ( strpos ( $payment_type, 'Net ' ) !== FALSE || $payment_type == 'On Account' ) {
        $status = 'Posted';
    }
if (strpos($value_config, ','."Send Outbound Invoice".',') !== FALSE)
    $pdf_product	= '';
    $created_by		= $_SESSION['contactid'];
    $comment		= filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);
    $ship_date		= $_POST['ship_date'];
	$due_date		= $_POST['due_date'];

    if ( ( isset ( $_GET[ 'edit' ] ) && $_GET[ 'edit' ] == 'return' ) && ( isset ( $_GET[ 'posid' ] ) && $_GET[ 'posid' ] != '' )  ) {
		// Create Return Invoice
		$posid = trim ( $_GET['posid'] );
		$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `point_of_sell` WHERE `posid`='$posid'"));
		$return_sum = $row['total_price'] - $total_price;
		$return_sub_total = $sub_total - $row['sub_total'];
		$return_gst = $gst_total - $row['gst'];
		$return_pst = $pst_total - $row['pst'];
		$return_before_tax = $total_before_tax - $row['total_before_tax'];
		$return_total = $total_price - $row['total_price'];
		if($row['return_ids'] != '') {
			$return_list = mysqli_query($dbc, "SELECT * FROM `point_of_sell` WHERE `posid` IN (".$row['return_ids'].") ORDER BY `posid` ASC");
			while($row = mysqli_fetch_array($return_list)) {
				$return_sub_total -= $row['sub_total'];
				$return_gst -= $row['gst'];
				$return_pst -= $row['pst'];
				$return_before_tax -= $row['total_before_tax'];
				$return_total -= $row['total_price'];
			}
		}
		$query = mysqli_query($dbc, "INSERT INTO `point_of_sell` (`invoice_date`, `contactid`, `productpricing`, `sub_total`, `discount_type`, `gst`, `pst`, `total_before_tax`, `client_tax_exemption`, `tax_exemption_number`, `total_price`, `payment_type`, `created_by`, `status`)
			VALUES ('".date('Y-m-d')."', '".$row['contactid']."', '".$row['productpricing']."', '$return_sub_total', '".$row['discount_type']."', '$return_gst', '$return_pst', '$return_before_tax', '".$row['client_tax_exemption']."', '".$row['tax_exemption_number']."', '$return_total', '$payment_type', '".$_SESSION['contactid']."', 'Returns')");
		$return_invoice = mysqli_insert_id($dbc);

    $before_change = '';
    $history = "Point of Sale entry Added. <br />";
    add_update_history($dbc, 'pos_history', $history, '', $before_change);

		// Return items
		$posid = trim ( $_GET['posid'] );
		$comment = htmlentities("<br /><br />Return<br />").$comment;
		$update_invoice_query = "UPDATE `point_of_sell` LEFT JOIN `contacts` ON `point_of_sell`.`contactid`=`contacts`.`contactid` SET `returned_amt`='$return_sum', `contacts`.amount_credit=IF(IFNULL(`amount_owing`,0) > 0, IF(`amount_owing` > (`total_price` - '$total_price'), `amount_credit`, `total_price` - '$total_price' - `amount_owing` + `amount_credit`), `amount_credit` + `total_price` - '$total_price'), `amount_owing`=IF(IFNULL(`amount_owing`,0) > 0, IF(`amount_owing` > (`total_price` - '$total_price'), `amount_owing` - `total_price` + '$total_price', 0), 0), `comment`=CONCAT(`comment`,'$comment'), `gst`='$gst_total', `pst`='$pst_total', `edit_id`=(`edit_id`+1), `return_ids`=CONCAT(IF(`return_ids`='','',CONCAT(`return_ids`,',')),'$return_invoice') WHERE `posid`='$posid'";
		$update_invoice_results = mysqli_query ( $dbc, $update_invoice_query );

    $before_change = '';
    $history = "Point of Sell Upadted. <br />";
    add_update_history($dbc, 'pos_history', $history, '', $before_change);

		// Update Inventory items
		for ($i=0; $i<count($_POST['inventoryid']); $i++) {
			$inventoryid	= $_POST['inventoryid'][$i];
			$price			= $_POST['price'][$i];
			$quantity_old	= $_SESSION['quantity'][$i];
			$returned		= $_POST['returned_qty'][$i];
			$return_diff = intval(mysqli_fetch_array(mysqli_query($dbc, "SELECT `returned_qty` FROM `point_of_sell_product` WHERE `posid`='$posid' AND `inventoryid`='$inventoryid' AND `type_category`='inventory'"))['returned_qty']) - $returned;

			if ( $inventoryid != '' ) {
        $before_change = capture_before_change($dbc, 'point_of_sell_product', 'returned_qty', 'posid', $posid, 'inventoryid', $inventoryid, 'type_category', 'inventory');

				$update_product_query = "UPDATE `point_of_sell_product` SET `returned_qty`='$returned' WHERE `posid`='$posid' AND `inventoryid`='$inventoryid' AND `type_category`='inventory'";
				$update_product_results = mysqli_query ( $dbc, $update_product_query );

        $history = capture_after_change('returned_qty', $returned);
        add_update_history($dbc, 'pos_history', $history, '', $before_change);

				//Update Inventory table to adjust the quantity
				$update_inventory_query = "UPDATE `inventory` SET `quantity`=(`quantity` + $returned) WHERE `inventoryid`='$inventoryid'";
				$update_inventory_results = mysqli_query ( $dbc, $update_inventory_query );

                //Update LED Edmonton Inventory table to adjust the quantity when SEA Alberta Inventory updates
                if ( $dbc_led ) {
                    $get_partno = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `part_no` FROM `inventory` WHERE `inventoryid`='$inventoryid'"));
                    $sea_part_no = $get_partno['part_no'];
                    if ( !empty($sea_part_no) ) {
                        $update_inventory_query = "UPDATE `inventory` SET `quantity`=(`quantity` + $returned) WHERE `part_no`='$sea_part_no'";
                        $update_inventory_results = mysqli_query ( $dbc_led, $update_inventory_query );
                    }
                }
                //Update SEA Alberta Inventory table to adjust the quantity when LED Edmonton Inventory updates
                if ( $dbc_sea_ab ) {
                    $get_partno = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `part_no` FROM `inventory` WHERE `inventoryid`='$inventoryid'"));
                    $led_part_no = $get_partno['part_no'];
                    if ( !empty($led_part_no) ) {
                        $update_inventory_query = "UPDATE `inventory` SET `quantity`=(`quantity` + $returned) WHERE `part_no`='$led_part_no'";
                        $update_inventory_results = mysqli_query ( $dbc_sea_ab, $update_inventory_query );
                    }
                }

				//Add Return Invoice Line Items
				if($return_diff != 0) {
					mysqli_query($dbc, "INSERT INTO `point_of_sell_product` (`posid`, `inventoryid`, `quantity`, `price`, `type_category`) VALUES ('$return_invoice', '$inventoryid', '$return_diff', '$price', 'inventory')");
          $before_change = '';
          $history = "Point of Sale Product Added. <br />";
          add_update_history($dbc, 'pos_history', $history, '', $before_change);
				}
			}
		}

		// Update Products
		for ($i=0; $i<count($_POST['prodinventoryid']); $i++) {
			$inventoryid	= $_POST['prodinventoryid'][$i];
			$price			= $_POST['prodprice'][$i];
			$returned		= $_POST['prodreturned'][$i];

			if ( $inventoryid != '' ) {
        $before_change = capture_before_change($dbc, 'point_of_sell_product', 'returned_qty', 'posid', $posid, 'inventoryid', $inventoryid, 'type_category', 'product');

				$query_insert_invoice = "UPDATE `point_of_sell_product` SET `returned_qty`='$returned' WHERE `posid`='$posid' AND `inventoryid`='$inventoryid' AND `type_category`='product'";
				$results_are_in = mysqli_query($dbc, $query_insert_invoice);

        $history = capture_after_change('returned_qty', $returned);
        add_update_history($dbc, 'pos_history', $history, '', $before_change);

				//Add Return Invoice Line Items
				if($returned != 0) {
					$returned = $returned * (-1);
					mysqli_query($dbc, "INSERT INTO `point_of_sell_product` (`posid`, `inventoryid`, `quantity`, `price`, `type_category`) VALUES ('$return_invoice', '$inventoryid', '$returned', '$price', 'product')");
          $before_change = '';
          $history = "Point of Sale Product Added. <br />";
          add_update_history($dbc, 'pos_history', $history, '', $before_change);
				}
			}
		}

		// Update Services
		for ($i=0; $i<count($_POST['servinventoryid']); $i++) {
			$inventoryid	= $_POST['servinventoryid'][$i];
			$price			= $_POST['servprice'][$i];
			$returned		= $_POST['servreturned'][$i];

			if ( $inventoryid != '' ) {
        $before_change = capture_before_change($dbc, 'point_of_sell_product', 'returned', 'posid', $posid, 'inventoryid', $inventoryid, 'type_category', 'service');

        $update_service_results = mysqli_query ( $dbc, "UPDATE `point_of_sell_product` SET `returned`='$returned' WHERE `posid`='$posid' AND `inventoryid`='$inventoryid' AND `type_category`='service'" );

        $history = capture_after_change('returned', $returned);
        add_update_history($dbc, 'pos_history', $history, '', $before_change);
				//Add Return Invoice Line Items
				if($returned != 0) {
					$returned = $returned * (-1);
					mysqli_query($dbc, "INSERT INTO `point_of_sell_product` (`posid`, `inventoryid`, `quantity`, `price`, `type_category`) VALUES ('$return_invoice', '$inventoryid', '$returned', '$price', 'service')");
          $before_change = '';
          $history = "Point of Sale Product Added. <br />";
          add_update_history($dbc, 'pos_history', $history, '', $before_change);
				}
			}
		}

		// Update Miscellaneous items
		for ($i=0; $i<count($_POST['misc_product']); $i++) {
			$misc_product	= filter_var($_POST['misc_product'][$i],FILTER_SANITIZE_STRING);
			$misc_price		= $_POST['misc_price'][$i];
			$returned	= $_POST['misc_returned'][$i];

			if ($misc_product != '' AND $returned != '0') {
        $before_change = capture_before_change($dbc, 'point_of_sell_product', 'returned_qty', 'posid', $posid, 'misc_product', $misc_product, 'type_category', 'misc product');

				$update_misc_results = mysqli_query ( $dbc, "UPDATE `point_of_sell_product` SET `returned_qty`='$returned' WHERE `posid`='$posid' AND `misc_product`='$misc_product' AND `type_category`='misc product'" );

        $history = capture_after_change('returned_qty', $returned);
        add_update_history($dbc, 'pos_history', $history, '', $before_change);

				//Add Return Invoice Line Items
				if($returned != 0) {
					$returned = $returned * (-1);
					mysqli_query($dbc, "INSERT INTO `point_of_sell_product` (`posid`, `misc_product`, `quantity`, `price`, `type_category`) VALUES ('$return_invoice', '$misc_product', '$returned', '$misc_price', 'misc product')");
          $before_change = '';
          $history = "Point of Sale Product Added. <br />";
          add_update_history($dbc, 'pos_history', $history, '', $before_change);
				}
			}
		}
	} else {
		// New POS
		$query_insert_invoice = "INSERT INTO `point_of_sell` (`invoice_date`, `contactid`, `productpricing`, `sub_total`, `discount_type`, `discount_value`, `total_after_discount`, `delivery`, `assembly`, `total_before_tax`, `client_tax_exemption`, `tax_exemption_number`, `total_price`, `payment_type`, `created_by`, `comment`, `ship_date`, `due_date`, `status`, `gst`, `pst`, `delivery_type`, `delivery_address`, `contractorid`, `deposit_paid`, `updatedtotal`, `edit_id`) VALUES ('$invoice_date', '$contactid', '$productpricing', '$sub_total', '$discount_type', '$discount_value', '$total_after_discount', '$delivery', '$assembly', '$total_before_tax', '$client_tax_exemption', '$tax_exemption_number', '$total_price', '$payment_type', '$created_by', '$comment', '$ship_date', '$due_date', '$status', '$gst_total', '$pst_total', '$delivery_type', '$delivery_address', '$contractorid', '$dep_paid', '$updatedtotal', '0')";
		$results_are_in = mysqli_query($dbc, $query_insert_invoice);

		$posid = mysqli_insert_id($dbc);

    $before_change = '';
    $history = "Point of Sale entry Added. <br />";
    add_update_history($dbc, 'pos_history', $history, '', $before_change);

		// ADD Column in Table for PDF //
		$col = "SELECT `type_category` FROM point_of_sell_product";
		$result = mysqli_query($dbc, $col);

		if (!$result){
			$colcreate = "ALTER TABLE `point_of_sell_product` ADD COLUMN `type_category` VARCHAR(555) NULL";
			$result = mysqli_query($dbc, $colcreate);
		}

		// Add Inventory items
		for($i=0; $i<count($_POST['inventoryid']); $i++) {
			$inventoryid = $_POST['inventoryid'][$i];
			$price = $_POST['price'][$i];
			$quantity = $_POST['quantity'][$i];

			if($inventoryid != '') {
				$query_insert_invoice = "INSERT INTO `point_of_sell_product` (`posid`, `inventoryid`, `quantity`, `price`, `type_category`) VALUES ('$posid', '$inventoryid', '$quantity', '$price', 'inventory')";
				$results_are_in = mysqli_query($dbc, $query_insert_invoice);

        $before_change = '';
        $history = "Point of Sale Product Added. <br />";
        add_update_history($dbc, 'pos_history', $history, '', $before_change);

				//Update Inventory table to reduce the quantity
				$query_update_inventory = "UPDATE `inventory` SET `quantity`=(`quantity`-'$quantity') WHERE `inventoryid`='$inventoryid'";
				$results_are_in = mysqli_query ( $dbc, $query_update_inventory );

                //Update LED Edmonton Inventory table to adjust the quantity when SEA Alberta Inventory updates
                if ( $dbc_led ) {
                    $get_partno = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `part_no` FROM `inventory` WHERE `inventoryid`='$inventoryid'"));
                    $sea_part_no = $get_partno['part_no'];
                    if ( !empty($sea_part_no) ) {
                        $query_update_inventory = "UPDATE `inventory` SET `quantity`=(`quantity`-'$quantity') WHERE `part_no`='$sea_part_no'";
                        $results_are_in = mysqli_query ( $dbc_led, $query_update_inventory );
                    }
                }
                //Update SEA Alberta Inventory table to adjust the quantity when LED Edmonton Inventory updates
                if ( $dbc_sea_ab ) {
                    $get_partno = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `part_no` FROM `inventory` WHERE `inventoryid`='$inventoryid'"));
                    $led_part_no = $get_partno['part_no'];
                    if ( !empty($led_part_no) ) {
                        $query_update_inventory = "UPDATE `inventory` SET `quantity`=(`quantity`-'$quantity') WHERE `part_no`='$led_part_no'";
                        $results_are_in = mysqli_query ( $dbc_sea_ab, $query_update_inventory );
                    }
                }
			}
		}

		// Add Products
		for($i=0; $i<count($_POST['prodinventoryid']); $i++) {
			$inventoryid = $_POST['prodinventoryid'][$i];
			$price = $_POST['prodprice'][$i];
			$quantity = $_POST['prodquantity'][$i];

			if($inventoryid != '') {
				$query_insert_invoice = "INSERT INTO `point_of_sell_product` (`posid`, `inventoryid`, `quantity`, `price`, `type_category`) VALUES ('$posid', '$inventoryid', '$quantity', '$price', 'product')";
				$results_are_in = mysqli_query($dbc, $query_insert_invoice);

        $before_change = '';
        $history = "Point of Sale Product Added. <br />";
        add_update_history($dbc, 'pos_history', $history, '', $before_change);
			}
		}

		// Add Services
		for($i=0; $i<count($_POST['servinventoryid']); $i++) {
			$inventoryid = $_POST['servinventoryid'][$i];
			$price = $_POST['servprice'][$i];
			$quantity = $_POST['servquantity'][$i];

			if($inventoryid != '') {
				$query_insert_invoice = "INSERT INTO `point_of_sell_product` (`posid`, `inventoryid`, `quantity`, `price`, `type_category`) VALUES ('$posid', '$inventoryid', '$quantity', '$price', 'service')";
				$results_are_in = mysqli_query($dbc, $query_insert_invoice);

        $before_change = '';
        $history = "Point of Sale Product Added. <br />";
        add_update_history($dbc, 'pos_history', $history, '', $before_change);
			}
		}

		// Add Miscellaneous items
		for($i=0; $i<count($_POST['misc_product']); $i++) {
			$misc_product	= filter_var($_POST['misc_product'][$i],FILTER_SANITIZE_STRING);
			$misc_price		= $_POST['misc_price'][$i];
			$misc_quantity	= $_POST['misc_quantity'][$i];

			if($misc_product != '') {
				$query_insert_invoice = "INSERT INTO `point_of_sell_product` (`posid`, `misc_product`, `price`, `quantity`, `type_category`) VALUES ('$posid', '$misc_product', '$misc_price', '$misc_quantity', 'misc product')";
				$results_are_in = mysqli_query($dbc, $query_insert_invoice);

        $before_change = '';
        $history = "Point of Product Coupon Added. <br />";
        add_update_history($dbc, 'pos_history', $history, '', $before_change);
			}
		}
	}

	$get_editid_results = mysqli_query ( $dbc, "SELECT `posid`, `edit_id` FROM `point_of_sell` WHERE `posid`='$posid'" );
	while ( $row = mysqli_fetch_assoc ( $get_editid_results ) ) {
		$edit_id = $row[ 'edit_id' ];
	}

    include ('create_pos_pdf.php');
    $pos_design = get_config($dbc, 'pos_design');

    if($pos_design == 1) {
		echo create_pos1_pdf($dbc,$posid,$d_value,$_POST['comment'], $gst_total, $pst_total, $rookconnect, $edit_id);
    }

	if($pos_design == 2) {
        echo create_pos2_pdf($dbc,$posid,$d_value,$_POST['comment'], $gst_total, $pst_total, $rookconnect, $edit_id);
    }

	if($pos_design == 3) {
        echo create_pos3_pdf($dbc,$posid,$d_value,$_POST['comment'], $gst_total, $pst_total, $rookconnect, $edit_id, $_POST['company_software_name']);
    }

	if($pos_design == 5) {
        echo create_pos5_pdf($dbc,$posid,$d_value,$_POST['comment'], $gst_total, $pst_total, $rookconnect, $edit_id);
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

    if($payment_type == 'Net 30 Days' || $payment_type == 'Net 30') {
        $send_invoice = $_POST['send_invoice'];
        if($send_invoice == 1) {
            $send_email = get_config($dbc, 'invoice_outbound_email');
            $arr_email=explode(",",$send_email);
            $attachment = 'download/invoice_'.$posid.'.pdf';
            //send_email('', $arr_email, '', '', 'Outbound Invoice', 'Please see Attachment for Outbound Invoice', $attachment);
        }
    }

    echo '
        <script type="text/javascript">
            window.location.replace("add_point_of_sell.php");
            window.open("download/invoice_'.$posid.$edited.'.pdf", "fullscreen=yes");
        </script>';
}
?>
<script type="text/javascript">

$(document).ready(function() {

	 $("#addorselect").click(function() {
        if($(this).val() == 'Add a New Customer') {
                $( ".new_client" ).show();
                $( "#customerg" ).hide();
                $( "#addorselect2" ).val('2');
                $( "#addorselect" ).val( 'Select an Existing Customer');
                $( "#cusref" ).show();
        } else if($( "#addorselect" ).val() == 'Select an Existing Customer') {
                $( ".new_client" ).hide();
                $( "#addorselect2" ).val('1');
                $( "#customerg" ).show();
                $( "#addorselect" ).val( 'Add a New Customer');
                //$( "#cusref1" ).hide();
                //$( "#cusref" ).hide();
        }
    });
    $("#form1").submit(function( event ) {
        var sub_total = $("input[name=sub_total]").val();
        var gst = $("input[name=gst]").val();
        var total_price = $("input[name=total_price]").val();
		if($('#addorselect2').val() == '1') {
			var customerid = $("#customerid").val();
		} else {
			var customerid = 'x';
			if($('#customer_name1').val() == '' || $('#cuscateg1').val() == '') {
				alert("Please make sure you have given a name and category to your new customer.");
				return false;
			}
		}
        var productpricing = $("#productpricing").val();
        var payment_type = $("#payment_type").val();


        if (customerid == '') {
			$('[name=contactid]').closest('.form-group').find('label').css('color','red');
			$('[name=contactid]').closest('.form-group').find('a').css('border-color','red');
		}
        if (productpricing == '') {
			$('[name=productpricing]').closest('.form-group').find('label').css('color','red');
			$('[name=productpricing]').closest('.form-group').find('a').css('border-color','red');
		}
        if (sub_total == '') {
			$('.hide-titles-mob').find('label').css('color','red');
		}
        if (payment_type == '') {
			$('[name=payment_type]').closest('.form-group').find('label').css('color','red');
			$('[name=payment_type]').closest('.form-group').find('a').css('border-color','red');
		}
        if (customerid == '') {
            alert("Please select a Customer.");
			$('[name=contactid]').parent()[0].scrollIntoView();
			$('[name=contactid]').trigger('chosen:activate');
            return false;
        }
        if (productpricing == '') {
            alert("Please select a Product Pricing option.");
			$('[name=productpricing]').parent()[0].scrollIntoView();
			$('[name=productpricing]').trigger('chosen:activate');
            return false;
        }
        if (sub_total == '0' || total_price == '0') {
            alert("Please add at least one item.");
			$('[name="category[]"]').first().parent()[0].scrollIntoView();
			$('[name="category[]"]').first().trigger('chosen:activate');
            return false;
        }
        if (payment_type == '') {
            alert("Please select a Payment Type.");
			$('[name=payment_type]').parent()[0].scrollIntoView();
			$('[name=payment_type]').trigger('chosen:activate');
            return false;
        }
    });

    $('.price').attr('readonly', true);
	//$('.servprice').attr('readonly', true);
	$('.prodprice').attr('readonly', true);

    var count = 1;
    $('#deleteservices_0').hide();
	 var servcount = 1;
    $('#servdeleteservices_0').hide();
	 var prodcount = 1;
    $('#proddeleteservices_0').hide();

    //Inventory clone
    $('#add_position_button').on( 'click', function () {
        $('#deleteservices_0').show();

        var clone = $('.additional_position').clone();
        clone.find('.form-control').val('');
        clone.find('.price').val('0');
        clone.find('.quantity').val('0');
        //clone.find(".product").html('');
        //clone.find(".product").trigger("change.select2");

        clone.find('.product').attr('id', 'product_dd_'+count);
        clone.find('.price').attr('id', 'price_dd_'+count);
        clone.find('.part').attr('id', 'part_dd_'+count);
        clone.find('.quantity').attr('id', 'qty_dd_'+count);
        clone.find('.category').attr('id', 'category_dd_'+count);

        clone.find('#services_0').attr('id', 'services_'+count);
        clone.find('#deleteservices_0').attr('id', 'deleteservices_'+count);
        $('#deleteservices_0').hide();

        clone.find('.form-control').trigger("change.select2");
        clone.removeClass("additional_position");
        $('#add_here_new_position').append(clone);
		resetChosen($("#category_dd_"+count));
		resetChosen($("#product_dd_"+count));
		resetChosen($("#part_dd_"+count));

        count++;
        return false;
    });

	// END INVENTORY and BEGIN Product

	$('#add_position_buttonprod').on( 'click', function () {
        $('#proddeleteservices_0').show();

        var clone = $('.additional_positionprod').clone();
        clone.find('.form-control').val('');
        clone.find('.prodprice').val('0');
        clone.find('.prodquantity').val('0');
        //clone.find(".product").html('');
        //clone.find(".product").trigger("change.select2");

        clone.find('.prodproduct').attr('id', 'prodproduct_dd_'+prodcount);
        clone.find('.prodprice').attr('id', 'prodprice_dd_'+prodcount);
        clone.find('.prodpart').attr('id', 'prodpart_dd_'+prodcount);
        clone.find('.prodquantity').attr('id', 'prodqty_dd_'+prodcount);
        clone.find('.prodcategory').attr('id', 'prodcategory_dd_'+prodcount);

        clone.find('#prodservices_0').attr('id', 'prodservices_'+prodcount);
        clone.find('#proddeleteservices_0').attr('id', 'proddeleteservices_'+prodcount);
        $('#proddeleteservices_0').hide();

        clone.find('.form-control').trigger("change.select2");
        clone.removeClass("additional_positionprod");
        $('#add_here_new_positionprod').append(clone);
		resetChosen($("#prodcategory_dd_"+prodcount));
		resetChosen($("#prodproduct_dd_"+prodcount));
		resetChosen($("#prodpart_dd_"+prodcount));

        prodcount++;
        return false;
    });

	// END PRODUCT BEGIN Service

	$('#add_position_buttonserv').on( 'click', function () {
        $('#servdeleteservices_0').show();

        var clone = $('.additional_positionserv').clone();
        clone.find('.form-control').val('');
        clone.find('.servprice').val('0');
        clone.find('.servquantity').val('0');
        //clone.find(".product").html('');
        //clone.find(".product").trigger("change.select2");

        clone.find('.servproduct').attr('id', 'servproduct_dd_'+servcount);
        clone.find('.servprice').attr('id', 'servprice_dd_'+servcount);
        clone.find('.servpart').attr('id', 'servpart_dd_'+servcount);
        clone.find('.servquantity').attr('id', 'servqty_dd_'+servcount);
        clone.find('.servcategory').attr('id', 'servcategory_dd_'+servcount);

        clone.find('#servservices_0').attr('id', 'servservices_'+servcount);
        clone.find('#servdeleteservices_0').attr('id', 'servdeleteservices_'+servcount);
        $('#servdeleteservices_0').hide();

        clone.find('.form-control').trigger("change.select2");
        clone.removeClass("additional_positionserv");
        $('#add_here_new_positionserv').append(clone);
		resetChosen($("#servcategory_dd_"+servcount));
		resetChosen($("#servproduct_dd_"+servcount));
		resetChosen($("#servpart_dd_"+servcount));
        servcount++;

        return false;
    });

	// END SERVICE

    var misccount = 1;
    $('#deletemisc_0').hide();
    $('#add_misc_button').on( 'click', function () {
        $('#deletemisc_0').show();

        var clone = $('.additional_misc').clone();
        clone.find('.form-control').val('');
        clone.find('.misc_price').val('0');
        clone.find('.misc_quantity').val('0');

        clone.find('.misc_product').attr('id', 'misc_product_'+misccount);
        clone.find('.misc_price').attr('id', 'misc_price_dd_'+misccount);
        clone.find('.misc_quantity').attr('id', 'misc_quantity_dd_'+misccount);

        clone.find('#misc_0').attr('id', 'misc_'+misccount);
        clone.find('#deletemisc_0').attr('id', 'deletemisc_'+misccount);
        $('#deletemisc_0').hide();

        clone.find('.form-control').trigger("change.select2");
        clone.removeClass("additional_misc");
        $('#add_here_new_misc').append(clone);
        misccount++;
        return false;
    });

	countPOSTotal();

    $('select#customerid, select#cuscateg1').on('change.select2', function() { changeClient(this); });
    $('select#productpricing').on('change.select2', function() { selectProductPricing(this); });
});

//Select2 with clone
$(document).on('change.select2', 'select.category', function() { selectCategory(this); });
$(document).on('change.select2', 'select.part, select.product', function() { selectProduct(this); });
$(document).on('change.select2', 'select.prodcategory', function() { prodselectCategory(this); });
$(document).on('change.select2', 'select.prodpart, select.prodproduct', function() { prodselectProduct(this); });
$(document).on('change.select2', 'select.prodlineprice', function() { selectProductLinePricing(this); });
$(document).on('change.select2', 'select.servcategory', function() { servselectCategory(this); });
$(document).on('change.select2', 'select.servproduct', function() { servselectProduct(this); });

// FOR INVENTORY VV
function selectCategory(sel) {
    var productPrice = $("#productpricing").val();
    if(productPrice == '') {
        alert('Error : Please select Pricing First.');
		return false;
    }
    var end = sel.value;
    var typeId = sel.id;

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=posFromCategory&name="+end,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var result = response.split('*#*');

            var arr = typeId.split('_');
            $("#part_dd_"+arr[2]).html(result[0]);
            //$("#part_dd_"+arr[2]).trigger("change.select2");

            $("#product_dd_"+arr[2]).html(result[1]);
            //$("#product_dd_"+arr[2]).trigger("change.select2");

            $("#price_dd_"+arr[2]).val('0');
            $("#qty_dd_"+arr[2]).val('0');
        }
    });
}

// END FOR INVENTORY & BEGIN FOR Product

function prodselectCategory(sel) {
    var productPrice = $("#prodproductpricing").val();
    if(productPrice == '') {
        alert('Error : Please select Pricing First.');
		return false;
    }
    var end = sel.value;
    var typeId = sel.id;

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=posFromCategoryprod&name="+end,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var result = response.split('*#*');

            var arr = typeId.split('_');
            $("#prodpart_dd_"+arr[2]).html(result[0]);
            //$("#prodpart_dd_"+arr[2]).trigger("change.select2");

            $("#prodproduct_dd_"+arr[2]).html(result[1]);
            //$("#prodproduct_dd_"+arr[2]).trigger("change.select2");

            $("#prodprice_dd_"+arr[2]).val('0');
            $("#prodqty_dd_"+arr[2]).val('0');
        }
    });
}
// END FOR Product & BEGIN FOR Services

function servselectCategory(sel) {
    var productPrice = $("#productpricing").val();
    if(productPrice == '' || productPrice == null) {
        alert('Error : Please select Pricing First.');
		return false;
	}
    var end = sel.value;
    var typeId = sel.id;

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=posFromCategoryserv&name="+end,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var result = response.split('*#*');

            var arr = typeId.split('_');
            $("#servpart_dd_"+arr[2]).html(result[0]);
            //$("#servpart_dd_"+arr[2]).trigger("change.select2");

            $("#servproduct_dd_"+arr[2]).html(result[1]);
            //$("#servproduct_dd_"+arr[2]).trigger("change.select2");

            $("#servprice_dd_"+arr[2]).val('0');
            $("#servqty_dd_"+arr[2]).val('0');
        }
    });
}

// END SERVICES & BEGIN FOR INVENTORY

function selectProduct(sel) {
    var end = sel.value;
    var typeId = sel.id;
    var arr = typeId.split('_');
    var productPrice = $("#productpricing").val();
    if(productPrice == '' || productPrice == null) {
        alert('Error : Please select Pricing First.');
		return false;
	}
    var category = $("#category_dd_"+arr[2]).val();

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=posUpProductFromProduct&inventoryid="+end+"&productPrice="+productPrice+"&category="+category,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var result = response.split('**##**');

            $("#category_dd_"+arr[2]).html(result[0]);
            $("#part_dd_"+arr[2]).html(result[1]);
            $("#product_dd_"+arr[2]).html(result[2]);
            //$("#category_dd_"+arr[2]).trigger("change.select2");
            //$("#part_dd_"+arr[2]).trigger("change.select2");
            //$("#product_dd_"+arr[2]).trigger("change.select2");

            $("#price_dd_"+arr[2]).val(result[3]);
            $("#qty_dd_"+arr[2]).val('0');
        }
    });
}

// END INVENTORY & BEGIN FOR PROD

function prodselectProduct(sel) {
    var end = sel.value;
    var typeId = sel.id;
    var arr = typeId.split('_');
    var productPrice = $("#productpricing").val();
	if(productPrice == '' || productPrice == null) {
        alert('Error : Please select Pricing First.');
		return false;
	}
    var category = $("#prodcategory_dd_"+arr[2]).val();

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=posUpProductFromProductprod&inventoryid="+end+"&productPrice="+productPrice+"&category="+category,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var result = response.split('**##**');

            $("#prodcategory_dd_"+arr[2]).html(result[0]);
            $("#prodpart_dd_"+arr[2]).html(result[1]);
            $("#prodproduct_dd_"+arr[2]).html(result[2]);
            //$("#prodcategory_dd_"+arr[2]).trigger("change.select2");
            //$("#prodpart_dd_"+arr[2]).trigger("change.select2");
            //$("#prodproduct_dd_"+arr[2]).trigger("change.select2");

            $("#prodprice_dd_"+arr[2]).val(result[3]);
            $("#prodqty_dd_"+arr[2]).val('0');
        }
    });
}

// END PROD & BEGIN FOR SERVICES


function servselectProduct(sel) {
    var end = sel.value;
    var typeId = sel.id;
    var arr = typeId.split('_');
    var productPrice = $("#productpricing").val();
	if(productPrice == '' || productPrice == null) {
        alert('Error : Please select Pricing First.');
		return false;
	}
    var category = $("#servcategory_dd_"+arr[2]).val();

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=posUpProductFromProductserv&inventoryid="+end+"&productPrice="+productPrice+"&category="+category,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var result = response.split('**##**');

            $("#servcategory_dd_"+arr[2]).html(result[0]);
            $("#servpart_dd_"+arr[2]).html(result[1]);
            $("#servproduct_dd_"+arr[2]).html(result[2]);
            //$("#servcategory_dd_"+arr[2]).trigger("change.select2");
            //$("#servpart_dd_"+arr[2]).trigger("change.select2");
            //$("#servproduct_dd_"+arr[2]).trigger("change.select2");

            $("#servprice_dd_"+arr[2]).val(result[3]);
            $("#servqty_dd_"+arr[2]).val('0');
        }
    });
}

// END SERVICES

function changeClient(sel) {
	var clientid = sel.value;
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "pos_ajax_all.php?fill=POSclient&clientid="+clientid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('**');
            if (result[0].indexOf("Client Price") >= 0) {
                var price_val = "client_price";
            }
            if (result[0].indexOf("Admin Price") >= 0) {
                var price_val = "admin_price";
                $('.price').attr('readonly', false);
            }
            if (result[0].indexOf("Commercial Price") >= 0) {
                var price_val = "commercial_price";
            }
            if (result[0].indexOf("Wholesale Price") >= 0) {
                var price_val = "wholesale_price";
            }
            if (result[0].indexOf("Final Retail Price") >= 0) {
                var price_val = "final_retail_price";
            }
            if (result[0].indexOf("Preferred Price") >= 0) {
                var price_val = "preferred_price";
            }
            if (result[0].indexOf("Web Price") >= 0) {
                var price_val = "web_price";
            }

            $("#client_tax_exemption").val(result[1]);
            var sum = 0;
            $('.pos_tax').each(function () {
                sum += +$(this).val() || 0;
            });
            var yes_tax_exemption = $("#yes_tax_exemption").val();
            if(result[1] == 'Yes' && yes_tax_exemption == 1) {
                $("#tax_exemption_fillup").html("Exemption Number : "+result[2]);

				var not_count_pos_tax = 0;
				$('.not_count_pos_tax').each(function () {
					not_count_pos_tax += +$(this).val() || 0;
				});

                var final_tax = parseFloat(sum - not_count_pos_tax);
                $("#tax_rate").val(final_tax);
                $("#tax_exemption_number").val(result[2]);
            } else {
                $("#tax_exemption_fillup").html('');
                $("#tax_exemption_number").val('');
                $("#tax_rate").val(sum);
            }
			$("#productpricing").val(price_val);
            $("#productpricing").trigger("change.select2");
            $("#delivery_address_fillup").val(result[3]);
		}
	});
}

function selectProductPricing(sel) {
    /* $(".category").val('');
    //$(".category").trigger("change.select2");
    $(".product").val('');
    //$(".product").trigger("change.select2");
    $(".part").val('');
    //$(".part").trigger("change.select2");

	$(".servcategory").val('');
    //$(".servcategory").trigger("change.select2");
    $(".servproduct").val('');
    //$(".servproduct").trigger("change.select2");
    $(".servpart").val('');
    //$(".servpart").trigger("change.select2");

	$(".prodcategory").val('');
    //$(".prodcategory").trigger("change.select2");
    $(".prodproduct").val('');
    //$(".prodproduct").trigger("change.select2");
    $(".prodpart").val('');
    //$(".prodpart").trigger("change.select2"); */

    $('.price').val('0');
    $('.quantity').val('0');

	$('.servprice').val('0');
    $('.servquantity').val('0');

	$('.prodprice').val('0');
    $('.prodquantity').val('0');

    $('#sub_total').val('0');
    $('#gst').val('0.00');
    $('#total_price').val('0');

    var productPrice = $("#productpricing").val();
    if(productPrice == 'admin_price') {
        $('.price').attr('readonly', false);
		//$('.servprice').attr('readonly', false);
		$('.prodprice').attr('readonly', false);
    } else {
        $('.price').attr('readonly', true);
		//$('.servprice').attr('readonly', true);
		$('.prodprice').attr('readonly', true);
    }
    countPOSTotal(sel);
}

function countPOSTotal(sel) {
    var productPrice = $("#productpricing").val();

	if(sel != undefined) {
		var current_id = sel.id;
		var result = current_id.split('_');

		var qty = $("#qty_dd_"+result[2]).val();
		var pro = $("#product_dd_"+result[2]).val();
		var ret = $("#returned_dd_"+result[2]).val();

		var servqty = $("#servqty_dd_"+result[2]).val();
		var servpro = $("#servproduct_dd_"+result[2]).val();
		var servret = $("#servret_dd_"+result[2]).val();

		var prodqty = $("#prodqty_dd_"+result[2]).val();
		var prodpro = $("#prodproduct_dd_"+result[2]).val();
		var prodret = $("#prodret_dd_"+result[2]).val();
	} else {
		result = ['na','na','na'];
		var pro = 0;
		var qty = 0;
		var ret = 0;
	}

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=posPromotion&pro="+pro+"&qty="+(qty-ret)+"&productPrice="+productPrice,
        dataType: "html",   //expect html to be returned
        success: function(response){
            if(current_id == "qty_dd_"+result[2]) {
                if(response != '') {
                    $("#price_dd_"+result[2]).val(response);
                }
            }
        },
        complete: function(){
			var c = 0;
			var i;
			var price = 0;

			var numQty = $('.quantity').length;
            for(i=0; i<numQty; i++) {
                var qty = $("#qty_dd_"+i).val();
                var price = $("#price_dd_"+i).val();
                var ret = $("#returned_dd_"+i).val()||0;
				if(price !== '' && price !== null && price > 0) {
					c += parseFloat(price*(qty-ret));
				}
            }

            var nummisc = $('.misc_price').length;
            var m;
            for(m=0; m<nummisc; m++) {
                var price = $("#misc_price_dd_"+m).val();
				var qty = $("#misc_quantity_dd_"+m).val();
				var ret = $("#misc_returned_dd_"+m).val()||0;
                if(price !== '' && price !== null) {
					c += parseFloat(price*(qty-ret));
				}
            }

			var servnumQty = $('.servquantity').length;
			var ggxy;
            for(ggxy=0; ggxy<servnumQty; ggxy++) {
                var qty = $("#servqty_dd_"+ggxy).val();
                var price = $("#servprice_dd_"+ggxy).val();
                var ret = $("#servret_dd_"+ggxy).val()||0;
                if(price !== '' && price !== null && price > 0) {
					c += parseFloat(price*(qty-ret));
				}
            }

			var prodnumQty = $('.prodquantity').length;
			var ggxx;
            for(ggxx=0; ggxx<prodnumQty; ggxx++) {
				var qty = $("#prodqty_dd_"+ggxx).val();
                var price = $("#prodprice_dd_"+ggxx).val();
                var ret = $("#prodret_dd_"+ggxx).val()||0;
                if(price !== '' && price !== null && price > 0) {
					c += parseFloat(price*(qty-ret));
				}
            }



            $("#sub_total").val((c).toFixed(2));
            $("#total_before_tax").val((c).toFixed(2));

            var discount_type = $('input:radio[name=discount_type]:checked').val();
            var discount_value = $("#discount_value").val();

            var total_after_dis = c;

            if(discount_type == '%') {
                var discount = (c*(discount_value/100));
                var total_after_dis = parseFloat(c) - parseFloat(discount);
            }
            if(discount_type == '$') {
                var total_after_dis = parseFloat(c) - parseFloat(discount_value);
            }

            $('#total_after_discount').val((total_after_dis).toFixed(2));

			/* Start Coupon */
			var total_after_coupon	= total_after_dis.toFixed(2);
			var coupon_type			= $('#couponid').find(':selected').data('type');
			var coupon_amount		= $('#couponid').find(':selected').data('amount');
			var sub_total_c			= parseFloat ( $("#sub_total").val() );
			var coupon_value		= 0;

			if ( coupon_type == '' || typeof coupon_type === "undefined" ) {
				// No coupon selected or no coupons available
				$('#total_after_coupon').val((total_after_dis).toFixed(2));
			} else {
				if ( coupon_type == '%' ) {
					coupon_value = parseFloat(sub_total_c) * parseFloat(coupon_amount/100);
				}
				if ( coupon_type == '$' ) {
					coupon_value = parseFloat(coupon_amount);
				}
				total_after_coupon = total_after_dis - coupon_value;

				$('#total_after_coupon').val((total_after_coupon).toFixed(2));
			}
			/* End Coupon */

      /* Start Gift Card */
      var gf_number = $("#gf_number").val();
      var total_after_gf = 0;
      var sub_total_c			= parseFloat ( $("#sub_total").val() );
      if(gf_number != 0 && gf_number && gf_number != null && gf_number != '') {
        $.ajax({    //create an ajax request to load_page.php
            type: "GET",
            url: "../Point of Sale/pos_ajax_all.php?fill=posGF&gf_number="+gf_number,
            dataType: "html",   //expect html to be returned
            success: function(response) {
                if(response == 'na') {
                  gf_value = 0;
                  alert("Invalid Gift card or already been used.")
                }
                else {
                  gf_value = response;
                }
                  var total_after_gf = total_after_coupon - gf_value;
                  $('#total_after_gf').val((total_after_gf).toFixed(2));
                  var delivery = $("#delivery").val();
                  var assembly = $("#assembly").val();
                  if(delivery == '' || typeof delivery === "undefined") {
                      delivery = 0;
                  }
                  if(assembly == '' || typeof assembly === "undefined") {
                      assembly = 0;
                  }

                  var shipping_total = parseFloat(total_after_gf) + parseFloat(delivery) + parseFloat(assembly);
                  var final_total = shipping_total.toFixed(2);

                  $('#total_before_tax').val(final_total);

                  var tax_rate = $("#tax_rate").val();

                        if (typeof tax_rate === "undefined") {
                            $('#total_price').val(final_total);
            				$('#updatedtotal').val(final_total-$('#deposit_paid').val());
                        } else {
                            var tax_rate_value = (final_total*tax_rate)/100;
                            $('#tax_price').val(tax_rate_value.toFixed(2));
                            var total_after_gst = parseFloat(final_total) + parseFloat(tax_rate_value);
                            $('#total_price').val(total_after_gst.toFixed(2));
            				$('#updatedtotal').val(final_total-$('#deposit_paid').val());
                        }
                }
            });
      }
      else {
        var delivery = $("#delivery").val();
        var assembly = $("#assembly").val();
        if(delivery == '' || typeof delivery === "undefined") {
            delivery = 0;
        }
        if(assembly == '' || typeof assembly === "undefined") {
            assembly = 0;
        }

        var shipping_total = parseFloat(total_after_coupon) + parseFloat(delivery) + parseFloat(assembly);
        var final_total = shipping_total.toFixed(2);

        $('#total_before_tax').val(final_total);

        var tax_rate = $("#tax_rate").val();

              if (typeof tax_rate === "undefined") {
                  $('#total_price').val(final_total);
  				$('#updatedtotal').val(final_total-$('#deposit_paid').val());
              } else {
                  var tax_rate_value = (final_total*tax_rate)/100;
                  $('#tax_price').val(tax_rate_value.toFixed(2));
                  var total_after_gst = parseFloat(final_total) + parseFloat(tax_rate_value);
                  $('#total_price').val(total_after_gst.toFixed(2));
  				$('#updatedtotal').val(final_total-$('#deposit_paid').val());
              }

      }
      /* End Gift Card */



        }
    });

}

function selectShippingtype() {
    var delivery_type = $("#delivery_type").val();
    if(delivery_type == 'Drop Ship' || delivery_type == 'Shipping') {
        $("#contractorid").show();
        $("#delivery_address").show();
        $("#delivery_div").show();
    } else if(delivery_type == 'Company Delivery') {
        $("#contractorid").hide();
        $("#delivery_div").hide();
        $("#delivery_address").show();
    } else {
        $("#contractorid").hide();
        $("#delivery_address").hide();
        $("#delivery_div").show();
    }
}
function seleteService(sel, hide, blank) {
	var typeId = sel.id;
	var arr = typeId.split('_');

    $("#"+hide+arr[1]).hide();
    $("#"+blank+arr[1]).val("");

    countPOSTotal();
}

function numericFilter(txb) {
   txb.value = txb.value.replace(/[^\0-9]/ig, "");
}

function returnFilter(input) {
   var ret = input.value;
   var qty = $(input).closest(".form-group").find('[name*=quantity]').val();
   if(ret > qty) {
	   $(input).val(qty);
   }
}
</script>
<style>
@media(max-width:991px) {
.hide-titles-mob {
	display:none;
}
.show-on-mob {
	text-align:left !important;
	display:inline-block;
}
.expand-mobile {
	width:90% !important;
	display:block !important;
}
.m-top-mbl {
	margin-top:20px;
}
}
@media(min-width:992px) {
.show-on-mob {
	display:none;
}
.form-inline .form-group {

    vertical-align: top;
}
}
</style>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('pos');
?>
<div class="container triple-pad-bottom">
    <div class="row">
		<div class="col-sm-10">
			<h1><?= POS_ADVANCE_TILE ?> Dashboard</h1>
		</div>
		<div class="col-sm-2 double-gap-top">
			<?php
				if(config_visible_function($dbc, 'pos') == 1) {
					echo '<a href="field_config_pos.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
				}
			?>
        </div>

		<div class="clearfix double-gap-bottom"></div>

		<?php
			//Check if POS is in returns mode
			$return = false;
			if(!empty($_GET['edit']) && $_GET['edit'] == 'return') {
				$return = true;
			}
			//Check if POS is in editing mode
			if ( isset ( $_GET[ 'posid' ] ) ) {
				$posid = trim ( $_GET[ 'posid' ] );

				if ( $results = mysqli_query ( $dbc, "SELECT * FROM `point_of_sell` WHERE `posid`='$posid'" ) ) {
					while ( $row = mysqli_fetch_assoc( $results ) ) {
						$invoice_date			= $row[ 'invoice_date' ];
						$contactid				= $row[ 'contactid' ];
						$customer				= get_client( $dbc, $contactid );
						$productpricing			= $row[ 'productpricing' ];
						$sub_total				= $row[ 'sub_total' ];
						$discount_type			= $row[ 'discount_type' ];
						$discount_value			= $row[ 'discount_value' ];
						$gst					= $row[ 'gst' ];
						$pst					= $row[ 'pst' ];
						$delivery				= $row[ 'delivery' ];
						$delivery_type			= $row[ 'delivery_type' ];
						$delivery_address		= $row[ 'delivery_address' ];
						$contractorid			= $row[ 'contractorid' ];
						$assembly				= $row[ 'assembly' ];
						$total_before_tax		= $row[ 'total_before_tax' ];
						$client_tax_exemption	= $row[ 'client_tax_exemption' ];
						$tax_exemption_number	= $row[ 'tax_exemption_number' ];
						$total_price			= $row[ 'total_price' ];
						$payment_type			= $row[ 'payment_type' ];
						$created_by				= get_staff( $dbc, $row['created_by'] );
						$comment				= html_entity_decode ( $row[ 'comment' ] );
						$ship_date				= $row[ 'ship_date' ];
						$total_after_discount	= $row[ 'total_after_discount' ];
						$deposit_paid			= $row[ 'deposit_paid' ];
						$due_date				= $row[ 'due_date' ];
						$couponid				= $row[ 'couponid' ];
					}

					$total_tax_amount = $gst + $pst;
				}

				// Get Inventory items
				if ( $results = mysqli_query ( $dbc, "SELECT prod.*, inv.`category`, inv.`part_no`, inv.`name` FROM `point_of_sell_product` prod JOIN `inventory` inv ON (prod.`inventoryid`=inv.`inventoryid`) WHERE prod.`posid`='$posid' AND prod.`type_category`='inventory' AND inv.`deleted`='0'" ) ) {
					$count			= 0;
					$posproductid	= array();
					$inventoryid	= array();
					$quantity		= array();
					$returned		= array();
					$price			= array();
					$category		= array();
					$part_no		= array();
					$name			= array();

					while ( $row = mysqli_fetch_assoc( $results ) ) {
						$posproductid[]	= $row[ 'posproductid' ];
						$inventoryid[]	= $row[ 'inventoryid' ];
						$quantity[]		= $row[ 'quantity' ];
						$returned[]		= $row[ 'returned_qty' ];
						$price[]		= $row[ 'price' ];
						$category[]		= $row[ 'category' ];
						$part_no[]		= $row[ 'part_no' ];
						$name[]			= $row[ 'name' ];
						$count++;
					}
					if(session_status() == PHP_SESSION_NONE) {
						session_start(['cookie_lifetime' => 518400]);
						$_SERVER['page_load_info'] .= 'Session Started: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
					}
					$_SESSION[ 'quantity' ] = $quantity; //To be used to update inventory quantities upon submission
					session_write_close();
				}

				// Get Miscellaneous items
				if ( $results = mysqli_query ( $dbc, "SELECT * FROM `point_of_sell_product` WHERE `posid`='$posid' AND `type_category`='misc product'" ) ) {
					$count_misc		= 0;
					$misc_productid	= array();
					$misc_desc		= array();
					$misc_quantity	= array();
					$misc_returned	= array();
					$misc_price		= array();

					while ( $row = mysqli_fetch_assoc( $results ) ) {
						$misc_productid[]	= $row[ 'posproductid' ];
						$misc_desc[]		= $row[ 'misc_product' ];
						$misc_quantity[]	= $row[ 'quantity' ];
						$misc_returned[]	= $row[ 'returned_qty' ];
						$misc_price[]		= $row[ 'price' ];
						$count_misc++;
					}
				}

			}
		?>

		<div class="gap-left tab-container mobile-100-container"><?php
			if ( vuaed_visible_function ( $dbc, 'pos' ) == 1 ) { ?>
				<?php
					$pos_layout	= get_config($dbc, 'pos_layout');

					if ( $pos_layout=='keyboard' ) { ?>
						<a href="add_point_of_sell.php"><button type="button" class="btn brand-btn mobile-block mobile-100 active_tab">Sell</button></a><?php
					} elseif  ( $pos_layout=='touch' ) { ?>
						<a href="pos_touch.php"><button type="button" class="btn brand-btn mobile-block mobile-100 active_tab">Sell</button></a><?php
					} else { ?>
						<a href="add_point_of_sell.php"><button type="button" class="btn brand-btn mobile-block mobile-100 active_tab">Sell - Keyboard Input</button></a>
						<a href="pos_touch.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Sell - Touch Input</button></a><?php
					}
				?><?php
			} else {
				echo '<script>
						alert("You do not have access to this page, please consult your software administrator (or settings) to gain access to this page.");
						window.location.replace("point_of_sell.php");
					</script>';
				//header('Location: point_of_sell.php');
			} ?>

			<?php if ( check_subtab_persmission($dbc, 'pos', ROLE, 'invoices') === TRUE ) { ?>
				<a href="point_of_sell.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Invoices</button></a>
			<?php } else { ?>
				<button type="button" class="btn disabled-btn mobile-block mobile-100">Invoices</button>
			<?php } ?>

			<?php if ( check_subtab_persmission($dbc, 'pos', ROLE, 'returns') === TRUE ) { ?>
				<a href="returns.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Returns</button></a>
			<?php } else { ?>
				<button type="button" class="btn disabled-btn mobile-block mobile-100">Returns</button>
			<?php } ?>

			<?php if ( check_subtab_persmission($dbc, 'pos', ROLE, 'unpaid') === TRUE ) { ?>
				<a href="unpaid_invoice.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Accounts Receivable</button></a>
			<?php } else { ?>
				<button type="button" class="btn disabled-btn mobile-block mobile-100">Accounts Receivable</button>
			<?php } ?>

			<?php if ( check_subtab_persmission($dbc, 'pos', ROLE, 'voided') === TRUE ) { ?>
				<a href="voided.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Voided Invoices</button></a>
			<?php } else { ?>
				<button type="button" class="btn disabled-btn mobile-block mobile-100">Voided Invoices</button>
			<?php } ?>

			<?php if ( vuaed_visible_function ( $dbc, 'pos' ) == 1 ) { ?>
				<a href="coupons.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Coupons</button></a>
			<?php } else {
				echo '<script>
						alert("You do not have access to this page, please consult your software administrator (or settings) to gain access to this page.");
						window.location.replace("point_of_sell.php");
					</script>';
			} ?>

      <?php if ( vuaed_visible_function ( $dbc, 'pos' ) == 1 ) { ?>
				<a href="giftcards.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Gift Cards</button></a>
			<?php } else {
				echo '<script>
						alert("You do not have access to this page, please consult your software administrator (or settings) to gain access to this page.");
						window.location.replace("point_of_sell.php");
					</script>';
			} ?>
		</div><!-- .mobile-100-container -->

		<br /><br />
        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal myform22" role="form">

        <input name="tax_exemption_number" id="tax_exemption_number" type="hidden" class="form-control" />
        <input name="client_tax_exemption" id="client_tax_exemption" type="hidden" class="form-control" />

        <?php
        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT pos FROM field_config"));
        $value_config = ','.$get_field_config['pos'].',';
        ?>

        <?php if (strpos($value_config, ','."Send Outbound Invoice".',') !== FALSE) { ?>
            <input name="send_invoice" value='1' type="hidden" class="form-control" />
        <?php } else { ?>
            <input name="send_invoice" value='0' type="hidden" class="form-control" />
        <?php } ?>
		<input type="hidden" name="return" value="<?php echo ($return ? 'return' : 'new'); ?>">

		<?php if($return) { ?>
			<div class="notice double-gap-bottom popover-examples">
				<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
				<div class="col-sm-11"><span class="notice-name">NOTE:</span>
				Because you are accessing this invoice in returns mode, you cannot change this invoice. You can only return inventory and products, and add comments.</div>
				<div class="clearfix"></div>
			</div>
		<?php } ?>

        <?php if (strpos($value_config, ','."Invoice Date".',') !== FALSE) { ?>
        <div class="form-group">
            <label for="first_name" class="col-sm-3 control-label text-right">Order Date:</label>
            <div class="col-sm-9">
                <input <?php echo ($return ? 'readonly' : ''); ?> name="invoice_date" value="<?= ($invoice_date) ? $invoice_date : date('Y-m-d'); ?>" type="text" class="datepicker form-control" style='width:162px;'></p>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Customer".',') !== FALSE) { ?>
          <!--<div class="form-group">
            <label for="travel_task" class="col-sm-3 control-label">Customer<span class="brand-color">*</span>:<br><em><span id="tax_exemption_fillup"></em></span></label>
            <div class="col-sm-9">
              <select id="customerid" onchange="changeClient(this)" name="contactid" data-placeholder="Select Customer..." class="chosen-select-deselect form-control" width="380">
              <option value=''></option>
                    <?php /*
                    $result = mysqli_query($dbc, "SELECT contactid, name FROM contacts WHERE (category='Business' or category='Customer' or category='Client') AND deleted=0");
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<option value = '".$row['contactid']."'>".$row['name']."</option>";
                    } */
                   ?>
              </select>
            </div>
          </div>-->
				  <div class="form-group" id='customerg'>
					<label for="travel_task" class="col-sm-3 control-label">Customer<span class="brand-color">*</span>:<br><em><span id="tax_exemption_fillup"></em></span></label>
					<div class="col-sm-9">
					  <select id="customerid" name="contactid" data-placeholder="Select Customer..." class="chosen-select-deselect form-control" width="380">
					  <option value=''></option>
							<?php
							$result = mysqli_query($dbc, "SELECT contactid, name FROM contacts WHERE (category NOT IN (".STAFF_CATS.") AND category != 'Employee') AND deleted=0 ORDER BY IF(name RLIKE '^[a-z]', 1, 2), name");
							while($row = mysqli_fetch_assoc($result)) {
								if ( $contactid == $row[ 'contactid' ] ) {
									$selected = 'selected="selected"';
								} else {
									$selected = '';
								}
								echo "<option " . $selected . " value=" . $row['contactid'] . ">" . decryptIt($row['name']) . "</option>";
							}
						   ?>
					  </select>
					</div>
                    <div class="clearfix"></div><br />
                    <label for="referred_by" class="col-sm-3 control-label">Reference</label>
                    <div class="col-sm-9"><input type="text" name="referred_by" value="" class="form-control" /></div>
				  </div>
                   <div class="form-group">
                   <label for="travel_task" id="" style="" class="col-sm-3 control-label"></label>
                   <div class="col-sm-8" <?php echo ($return ? 'style="display:none;"' : ''); ?>>
                        <input name="addorselect" id="addorselect" value="Add a New Customer" type="button" class="form-control" style="display: block; width:240px;"/>
                        <input name="addorselect2" id="addorselect2" value="1" type="hidden" class="form-control" style="width:240px;"/>
                  </div>
                  </div>

                  <div class="form-group new_client" style="display: none;">
                    <label for="travel_task" id="customer_name" class="col-sm-3 control-label">Customer Name:</label>
                    <div class="col-sm-9">
                        <input name="customer_name" id="customer_name1" type="text" class="form-control" />
                    </div>
                  </div>

				  <div class="form-group new_client" style="display: none;">
                    <label for="travel_task" id="cuscateg" class="col-sm-3 control-label">Customer Category:</label>
                    <div class="col-sm-9">
						<?php if($return) { ?>
							<input readonly name="cuscateg1" id="cuscateg1" type="text" class="form-control" />
						<?php } else { ?>
							<select id="cuscateg1" name="cuscateg1" data-placeholder="Select Category..." class="chosen-select-deselect form-control" width="380">
							<option value=''></option>
								<?php
									$result = mysqli_query($dbc, "SELECT `category` FROM `contacts` GROUP BY `category`");
									while ( $row = mysqli_fetch_assoc($result) ) {
										if ( $rookconnect == 'sea' ) {
											if ( $row['category'] == 'Client' ) {
												echo "<option value = " . $row['category'] . " selected>" . $row['category'] . "</option>";
											} else {
												echo "<option value = " . $row['category'] . ">" . $row['category'] . "</option>";
											}

										} else {
											echo "<option value = " . $row['category'] . ">" . $row['category'] . "</option>";
										}
									}
								?>
							</select>
						<?php } ?>
                    </div>
                  </div>

                  <div class="form-group new_client" style="display: none;">
                    <label for="site_name" id="cusphone1" class="col-sm-3 control-label">Customer Phone:</label>
                    <div class="col-sm-9">
                        <input <?php echo ($return ? 'readonly' : ''); ?> name="cusphone" id="cusphone" type="text" class="form-control" />
                    </div>
                  </div>

                  <div class="form-group new_client" style="display: none;">
                    <label for="site_name" id="cusemail1" class="col-sm-3 control-label">Customer Email:</label>
                    <div class="col-sm-9">
                        <input <?php echo ($return ? 'readonly' : ''); ?> name="email" id="cusemail" type="text" class="form-control"/>
                    </div>
                  </div>

                  <div class="form-group new_client" style="display: none;">
                    <label for="site_name" id="cusref1" class="col-sm-3 control-label">How Did They Hear About Us?</label>
                    <div class="col-sm-9">
                        <input <?php echo ($return ? 'readonly' : ''); ?> name="reference" id="cusref" type="text" class="form-control" />
                    </div>
                  </div>
          <?php } ?>

          <?php if (strpos($value_config, ','."Product Pricing".',') !== FALSE) { ?>
          <div class="form-group">
            <label for="site_name" class="col-sm-3 control-label">Product Pricing<span class="brand-color">*</span>:</label>
            <div class="col-sm-9">
				<select data-placeholder="Select Pricing..." id="productpricing" name="productpricing" class="chosen-select-deselect form-control" width="380">
                    <option value="">Please Select</option><?php
                    if (strpos($value_config, ','."Admin Price".',') !== FALSE) {
                        $selected = ($productpricing == 'admin_price') ? 'selected="selected"' : ''; ?>
                        <option value="admin_price" <?= $selected; ?>>Admin Price</option><?php
                    }
                    if (strpos($value_config, ','."Client Price".',') !== FALSE) {
                        $selected = ($productpricing == 'client_price') ? 'selected="selected"' : ''; ?>
                        <option value="client_price" <?= $selected; ?>>Client Price</option><?php
                    }
                    if (strpos($value_config, ','."Commercial Price".',') !== FALSE) {
                        $selected = ($productpricing == 'commercial_price') ? 'selected="selected"' : ''; ?>
                        <option value="commercial_price" <?= $selected; ?>>Commercial Price</option><?php
                    }
                    if (strpos($value_config, ','."Distributor Price".',') !== FALSE) {
                        $selected = ($productpricing == 'distributor_price') ? 'selected="selected"' : ''; ?>
                        <option value="distributor_price" <?= $selected; ?>>Distributor Price</option><?php
                    }
                    if (strpos($value_config, ','."Drum Unit Cost".',') !== FALSE) {
                        $selected = ($productpricing == 'drum_unit_cost') ? 'selected="selected"' : ''; ?>
                        <option value="drum_unit_cost" <?= $selected; ?>>Drum Unit Cost</option><?php
                    }
                    if (strpos($value_config, ','."Drum Unit Price".',') !== FALSE) {
                        $selected = ($productpricing == 'drum_unit_price') ? 'selected="selected"' : ''; ?>
                        <option value="drum_unit_price" <?= $selected; ?>>Drum Unit Price</option><?php
                    }
                    if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) {
                        $selected = ($productpricing == 'final_retail_price') ? 'selected="selected"' : ''; ?>
                        <option value="final_retail_price" <?= $selected; ?>>Final Retail Price</option><?php
                    }
                    if (strpos($value_config, ','."Preferred Price".',') !== FALSE) {
                        $selected = ($productpricing == 'preferred_price') ? 'selected="selected"' : ''; ?>
                        <option value="preferred_price" <?= $selected; ?>>Preferred Price</option><?php
                    }
                    if (strpos($value_config, ','."Purchase Order Price".',') !== FALSE) {
                        $selected = ($productpricing == 'purchase_order_price') ? 'selected="selected"' : ''; ?>
                        <option value="purchase_order_price" <?= $selected; ?>>Purchase Order Price</option><?php
                    }
                    if (strpos($value_config, ','."Sales Order Price".',') !== FALSE) {
                        $selected = ($productpricing == 'sales_order_price') ? 'selected="selected"' : ''; ?>
                        <option value="sales_order_price" <?= $selected; ?>><?= SALES_ORDER_NOUN ?> Price</option><?php
                    }
                    if (strpos($value_config, ','."Tote Unit Cost".',') !== FALSE) {
                        $selected = ($productpricing == 'tote_unit_cost') ? 'selected="selected"' : ''; ?>
                        <option value="tote_unit_cost" <?= $selected; ?>>Tote Unit Cost</option><?php
                    }
                    if (strpos($value_config, ','."Tote Unit Price".',') !== FALSE) {
                        $selected = ($productpricing == 'tote_unit_price') ? 'selected="selected"' : ''; ?>
                        <option value="tote_unit_price" <?= $selected; ?>>Tote Unit Price</option><?php
                    }
                    if (strpos($value_config, ','."Web Price".',') !== FALSE) {
                        $selected = ($productpricing == 'web_price') ? 'selected="selected"' : ''; ?>
                        <option value="web_price" <?= $selected; ?>>Web Price</option><?php
                    }
                    if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) {
                        $selected = ($productpricing == 'wholesale_price') ? 'selected="selected"' : ''; ?>
                        <option value="wholesale_price" <?= $selected; ?>>Wholesale Price</option><?php
                    } ?>
                </select>
            </div>
          </div>
          <?php } ?>
		  <div class='inventory-margin' style='margin-left:15%;'>
			<!-- INVENTORY -->
			<?php if (strpos($value_config, ','."Products".',') !== FALSE) { ?>
            <div class="form-group clearfix">
			<label class="col-sm-12 text-center" style="width:20%;"><h4>Inventory</h4></label>
			</div>
			<div class="form-group clearfix hide-titles-mob">
                <?php if (strpos($value_config, ','."Category".',') !== FALSE) { ?>
                    <label class="col-sm-3  text-center" style="width:20%;">Category</label>
                <?php } ?>
                <?php if (strpos($value_config, ','."Part#".',') !== FALSE) { ?>
                    <label class="col-sm-3 text-center" style="width:20%;">Part#</label>
                <?php } ?>
                <?php if (strpos($value_config, ','."Name".',') !== FALSE) { ?>
                    <label class="col-sm-3 text-center" style="position:relative;width:20%">Product</label>
                <?php } ?>
                <?php if (strpos($value_config, ','."Price".',') !== FALSE) { ?>
                    <label class="col-sm-1 text-center" style="position:relative;width:10%">Price</label>
                <?php } ?>
                <?php if (strpos($value_config, ','."Quantity".',') !== FALSE) { ?>
                    <label class="col-sm-1 text-center" style="position:relative;width:10%">Quantity</label>
                <?php } ?>
				<?php if($return) { ?>
                    <label class="col-sm-1 text-center" style="position:relative;width:10%">Returned</label>
				<?php } ?>
            </div>


        <div class="additional_position">
            <div class="clearfix"></div><?php

			if ( $count != 0 || $count != '' || $return ) {
				for ( $i=0; $i<($return ? $count : $count + 1); $i++ ) { ?>
					<div class="form-group clearfix" id="services_<?= $i; ?>" width="100%"><?php
						if ( strpos ( $value_config, ',' . "Category" . ',' ) !== FALSE ) { ?>
							<label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
							<div class="col-sm-3 expand-mobile type" style="width: 20%; display:inline-block; position:relative;" id="category_<?= $i; ?>">
								<select data-placeholder="Select a Category..." id="category_dd_<?= $i; ?>" name="category[]" class="chosen-select-deselect form-control category">
									<option value="<?= $category[$i]; ?>"><?= $category[$i]; ?></option><?php
									$query = mysqli_query ( $dbc, "SELECT DISTINCT `category` FROM `inventory` ORDER BY `category`" );
									while ( $row = mysqli_fetch_assoc ( $query ) ) { ?>
										<option id="<?= $row['category']; ?>" value="<?= $row['category']; ?>"><?= $row['category']; ?></option><?php
									} ?>
								</select>
							</div><?php
						}

						if ( strpos ( $value_config, ',' . "Part#" . ',' ) !== FALSE ) { ?>
							<label for="company_name" class="col-sm-4 show-on-mob control-label">Part #:</label>
							<div class="expand-mobile col-sm-3"  style="width: 20%;  display:inline-block; position:relative;" id="part_<?= $i; ?>">
								<select data-placeholder="Select a Part#..." id="part_dd_<?= $i; ?>" name="part_no[]" class="chosen-select-deselect form-control part">
									<option value="<?= $inventoryid[$i]; ?>"><?= $part_no[$i]; ?></option><?php
									$query = mysqli_query ( $dbc, "SELECT `inventoryid`, `part_no` FROM `inventory` WHERE `deleted`=0 ORDER BY `part_no`" );
									while ( $row = mysqli_fetch_array ( $query ) ) { ?>
										<option value="<?= $row['inventoryid']; ?>"><?= $row['part_no']; ?></option><?php
									} ?>
								</select>
							</div><?php
						}

						if ( strpos ( $value_config, ',' . "Name" . ',' ) !== FALSE ) { ?>
							<label for="company_name" class="col-sm-4 show-on-mob control-label">Product:</label>
							<div class="col-sm-3 expand-mobile" id="product_<?= $i; ?>" style="width:20%; position:relative; display:inline-block;">
								<select data-placeholder="Select a Product..." name="inventoryid[]" id="product_dd_<?= $i; ?>" class="chosen-select-deselect  form-control product" style="position:relative;">
									<option value="<?= $inventoryid[$i]; ?>"><?= $name[$i]; ?></option><?php
									$query = mysqli_query($dbc,"SELECT `inventoryid`, `name` FROM `inventory` WHERE `deleted`=0 ORDER BY `name`");
									while ( $row = mysqli_fetch_array ( $query ) ) { ?>
										<option value="<?= $row['inventoryid']; ?>"><?= $row['name']; ?></option><?php
									} ?>
								</select>
							</div><?php
						}

						if ( strpos ( $value_config, ',' . "Price" . ',' ) !== FALSE ) { ?>
							<div class="col-sm-1 expand-mobile" id="price_<?= $i; ?>" style="width:10%; position:relative; display:inline-block;">
								<label for="company_name" class="col-sm-4 show-on-mob control-label">Price:</label>
								<input <?php echo ($return ? 'readonly' : ''); ?> data-placeholder="Select a Product..." name="price[]" id="price_dd_<?= $i; ?>" value="<?= ($price[$i]) ? $price[$i] : 0; ?>" style="width:100% !important;" onkeyup="countPOSTotal(this);" type="text" class="expand-mobile form-control price" />
							</div><?php
						}

						if ( strpos ( $value_config, ',' . "Quantity" . ',' ) !== FALSE ) { ?>
							<div class="col-sm-3 qt expand-mobile" id="qty_<?= $i; ?>" style="width:10%; position:relative; display:inline-block;">
								<label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
								<input <?php echo ($return ? 'readonly' : ''); ?> data-placeholder="Select a Product..." name="quantity[]" id="qty_dd_<?= $i; ?>" onkeyup="numericFilter(this); countPOSTotal(this);" value="<?= ($quantity[$i]) ? $quantity[$i] : 0; ?>" style="width:100% !important;" type="text" class="expand-mobile form-control quantity" />
							</div><?php
						} ?>
						<?php if($return) { ?>
							<div class="col-sm-3 qt expand-mobile" id="qty_<?= $i; ?>" style="width:10%; position:relative; display:inline-block;">
								<label for="company_name" class="col-sm-4 show-on-mob control-label">Returned:</label>
								<input data-placeholder="Quantity Returned..." name="returned_qty[]" id="returned_dd_<?= $i; ?>" onchange="returnFilter(this); countPOSTotal(this);" value="<?php echo $returned[$i]; ?>" style="width:100% !important;" type="number" class="expand-mobile form-control invreturned" />
							</div>
						<?php } ?>

						<div class="col-sm-1 m-top-mbl" <?php echo ($return ? 'style="display:none;"' : ''); ?> >
							<a href="#" onclick="seleteService(this,'services_','qty_dd_'); return false;" id="deleteservices_<?= $i; ?>" class="btn brand-btn">Delete</a>
						</div>
					</div><!-- #services_0 --><?php
				} //for loop

			} else { ?>
				<div class="form-group clearfix" id="services_0" width="100%"><?php
					if ( strpos ( $value_config, ',' . "Category" . ',' ) !== FALSE ) { ?>
						<label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
						<div class="col-sm-3 expand-mobile type"  style="width: 20%; display:inline-block; position:relative;" id="category_0">
							<select data-placeholder="Select a Category..." id="category_dd_0" name="category[]" class="chosen-select-deselect form-control category">
								<option value=""></option><?php
								$query = mysqli_query ( $dbc, "SELECT DISTINCT category FROM inventory order by category" );
								while ( $row = mysqli_fetch_array ( $query ) ) { ?>
									<option id="<?php echo $row['category']; ?>" value="<?= $row['category']; ?>"><?= $row['category']; ?></option><?php
								} ?>
							</select>
						</div><?php
					}

					if ( strpos ( $value_config, ',' . "Part#" . ',' ) !== FALSE ) { ?>
						<label for="company_name" class="col-sm-4 show-on-mob control-label">Part #:</label>
						<div class="expand-mobile col-sm-3"  style="width: 20%;  display:inline-block; position:relative;" id="part_0">
							<select data-placeholder="Select a Part#..." id="part_dd_0" name="part_no[]" class="chosen-select-deselect form-control part">
								<option value=""></option><?php
								$query = mysqli_query ( $dbc, "SELECT inventoryid, part_no FROM inventory WHERE deleted=0 order by part_no" );
								while ( $row = mysqli_fetch_array ( $query ) ) { ?>
									<option value="<?= $row['inventoryid']; ?>"><?= $row['part_no']; ?></option><?php
								} ?>
							</select>
						</div><?php
					}

					if ( strpos ( $value_config, ',' . "Name" . ',' ) !== FALSE ) { ?>
						<label for="company_name" class="col-sm-4 show-on-mob control-label">Product:</label>
						<div class="col-sm-3 expand-mobile" id="product_0" style="width:20%; position:relative; display:inline-block;">
							<select data-placeholder="Select a Product..." name="inventoryid[]" id="product_dd_0" class="chosen-select-deselect  form-control product" style="position:relative;">
								<option value=""></option><?php
								$query = mysqli_query($dbc,"SELECT inventoryid, name FROM inventory WHERE deleted=0 order by name");
								while ( $row = mysqli_fetch_array ( $query ) ) { ?>
									<option value="<?= $row['inventoryid']; ?>"><?= $row['name']; ?></option><?php
								} ?>
							</select>
						</div><?php
					}

					if ( strpos ( $value_config, ',' . "Price" . ',' ) !== FALSE ) { ?>
						<div class="col-sm-1 expand-mobile" id="price_0" style="width:10%; position:relative; display:inline-block;">
							<label for="company_name" class="col-sm-4 show-on-mob control-label">Price:</label>
							<input data-placeholder="Select a Product..." name="price[]" id="price_dd_0" value="0" style="width:100% !important;" onkeyup="countPOSTotal(this);" type="text" class="expand-mobile form-control price" />
						</div><?php
					}

					if ( strpos ( $value_config, ',' . "Quantity" . ',' ) !== FALSE ) { ?>
						<div class="col-sm-3 qt expand-mobile" id="qty_0" style="width:10%; position:relative; display:inline-block;">
							<label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
							<input <?php echo ($return ? 'readonly' : ''); ?> data-placeholder="Select a Product..." name="quantity[]" id="qty_dd_0" onkeyup="numericFilter(this); countPOSTotal(this);" value="0" style="width:100% !important;" type="text" class="expand-mobile form-control quantity" />
						</div><?php
					} ?>

					<div class="col-sm-1 m-top-mbl" >
						<a href="#" onclick="seleteService(this,'services_','qty_dd_'); return false;" id="deleteservices_0" class="btn brand-btn">Delete</a>
					</div>
				</div><!-- else --><?php
			} ?>


		</div><!-- .additional_position -->

            <div id="add_here_new_position"></div>

            <div class="col-sm-12  triple-gap-bottom" <?php echo ($return ? 'style="display:none;"' : ''); ?> >
                <button id="add_position_button" class="btn brand-btn mobile-block">Add</button>
            </div>
            <?php } ?>
				<!-- END INVENTORY -->

				<!-- Products -->
            <?php if (strpos($value_config, ','."prodProducts".',') !== FALSE) { ?>

            <div class="form-group clearfix">
			<label class="col-sm-1  text-center" style="width:20%;"><h4>Product(s)</h4></label>
			</div>
			  <div class="form-group clearfix hide-titles-mob">
                <?php if (strpos($value_config, ','."prodCategory".',') !== FALSE) { ?>
                    <label class="col-sm-1 text-center" style="width:20%;">Category</label>
                <?php } ?>
                <?php if (strpos($value_config, ','."prodProduct Type".',') !== FALSE) { ?><!--
                    <label class="col-sm-1 text-center" style="width:15%;">Product Type</label>-->
                <?php } ?>
                <?php if (strpos($value_config, ','."prodHeading".',') !== FALSE) { ?>
                    <label class="col-sm-3 text-center" style="position:relative;width:20%">Heading</label>
                <?php } ?>
                <?php if (strpos($value_config, ','."Pricing by Line Item".',') !== FALSE) { ?>
                    <label class="col-sm-2 text-center" style="position:relative;width:15%">Pricing</label>
                <?php } ?>
                <?php if (strpos($value_config, ','."prodPrice".',') !== FALSE) { ?>
                    <label class="col-sm-1 text-center" style="position:relative;width:10%">Price</label>
                <?php } ?>
                <?php if (strpos($value_config, ','."prodQuantity".',') !== FALSE) { ?>
                    <label class="col-sm-1 text-center" style="position:relative;width:10%">Quantity</label>
                <?php } ?>
                <?php if ($return) { ?>
                    <label class="col-sm-1 text-center" style="position:relative;width:10%">Returned</label>
                <?php } ?>
            </div>

          <div class="additional_positionprod">
            <div class="clearfix"></div>
            <div class="form-group clearfix" id="prodservices_0" width="100%">

                <?php if (strpos($value_config, ','."prodCategory".',') !== FALSE) { ?>
                <div class="col-sm-1 expand-mobile prodtype"  style="width:20%; display:inline-block; position:relative;" id="prodcategory_0">
				<label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select data-placeholder="Select a Category..." id="prodcategory_dd_0" name="prodcategory[]" class="chosen-select-deselect form-control prodcategory">
                        <option value=""></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT DISTINCT category FROM products order by category");
                        while($row = mysqli_fetch_array($query)) {
                            ?><option id='<?php echo $row['category'];?>' value='<?php echo $row['category'];?>'><?php echo $row['category'];?></option><?php
                        }
                        ?>
                    </select>
                </div>
                <?php } ?>
                <?php if (strpos($value_config, ','."prodProduct Type".',') !== FALSE) { ?>
                <div class="col-sm-1 expand-mobile"  style="width:15%; display:inline-block; position:relative;" id="prodpart_0">
				<label for="company_name" class="col-sm-4 show-on-mob control-label">Product Type:</label>
                <select data-placeholder="Select a Type..." id="prodpart_dd_0" name="prodpart_no[]" class="chosen-select-deselect form-control prodpart">
                    <option value=""></option>
                    <?php
                    $query = mysqli_query($dbc,"SELECT productid, product_type FROM products WHERE deleted=0 order by product_type");
                    while($row = mysqli_fetch_array($query)) {
                        ?><option value='<?php echo $row['productid'];?>'><?php echo $row['product_type'];?></option><?php
                    }
                    ?>
                </select>
                </div>
                <?php } ?>

                <?php if (strpos($value_config, ','."prodHeading".',') !== FALSE) { ?>
                <div class="col-sm-3 expand-mobile" id="prodproduct_0" style="width:20%; position:relative; display:inline-block;">
				<label for="company_name" class="col-sm-4 show-on-mob control-label">Heading:</label>
                    <select data-placeholder="Select a Heading..." name="prodinventoryid[]" id="prodproduct_dd_0" class="chosen-select-deselect form-control prodproduct" style="position:relative;">
                        <option value=""></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT productid, heading FROM products WHERE deleted=0 order by heading");
                        while($row = mysqli_fetch_array($query)) {
                            ?><option value='<?php echo $row['productid'];?>'><?php echo $row['heading'];?></option><?php
                        }
                        ?>
                    </select>
                </div>
                <?php } ?>

                <?php if (strpos($value_config, ','."Pricing by Line Item".',') !== FALSE) { ?>
                <div class="col-sm-3 expand-mobile" id="prodlineprice_0" style="width:15%; position:relative; display:inline-block;">
				<label for="company_name" class="col-sm-4 show-on-mob control-label">Pricing:</label>
                    <select data-placeholder="Select Pricing..." id="prodlineprice_dd_0" name="productlinepricing[]" class="chosen-select-deselect form-control prodlineprice" width="380">
                        <option value="">Please Select</option>
                        <?php if (strpos($value_config, ','."Client Price".',') !== FALSE) { ?>
                        <option value="client_price">Client Price</option>
                        <?php } ?>
                        <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { ?>
                        <option value="admin_price">Admin Price</option>
                        <?php } ?>
                        <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { ?>
                        <option value="commercial_price">Commercial Price</option>
                        <?php } ?>
                        <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { ?>
                        <option value="wholesale_price">Wholesale Price</option>
                        <?php } ?>
                        <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { ?>
                        <option value="final_retail_price">Final Retail Price</option>
                        <?php } ?>
                        <?php if (strpos($value_config, ','."Preferred Price".',') !== FALSE) { ?>
                        <option value="preferred_price">Preferred Price</option>
                        <?php } ?>
                        <?php if (strpos($value_config, ','."Web Price".',') !== FALSE) { ?>
                        <option value="web_price">Web Price</option>
                        <?php } ?>
                        <?php if (strpos($value_config, ','."Purchase Order Price".',') !== FALSE) { ?>
                        <option value="purchase_order_price">Purchase Order Price</option>
                        <?php } ?>
                        <?php if (strpos($value_config, ','."Sales Order Price".',') !== FALSE) { ?>
                        <option value="sales_order_price"><?= SALES_ORDER_NOUN ?> Price</option>
                        <?php } ?>
                        <?php if (strpos($value_config, ','."Drum Unit Cost".',') !== FALSE) { ?>
                        <option value="drum_unit_cost">Drum Unit Cost</option>
                        <?php } ?>
                        <?php if (strpos($value_config, ','."Drum Unit Price".',') !== FALSE) { ?>
                        <option value="drum_unit_price">Drum Unit Price</option>
                        <?php } ?>
                        <?php if (strpos($value_config, ','."Tote Unit Cost".',') !== FALSE) { ?>
                        <option value="tote_unit_cost">Tote Unit Cost</option>
                        <?php } ?>
                        <?php if (strpos($value_config, ','."Tote Unit Price".',') !== FALSE) { ?>
                        <option value="tote_unit_price">Tote Unit Price</option>
                        <?php } ?>
                    </select>
                </div>
                <?php } ?>

                <?php if (strpos($value_config, ','."prodPrice".',') !== FALSE) { ?>
                <div class="col-sm-1 expand-mobile" id="prodprice_0" style="width:10%; position:relative; display:inline-block;">
				<label for="company_name" class="col-sm-4 show-on-mob control-label">Price:</label>
                    <input <?php echo ($return ? 'readonly' : ''); ?> data-placeholder="Select a Product..." name="prodprice[]" id="prodprice_dd_0" value="0" style="" onkeyup="countPOSTotal(this);" type="text" class="form-control prodprice" />
                </div>
                <?php } ?>

                <?php if (strpos($value_config, ','."prodQuantity".',') !== FALSE) { ?>
                <div class="col-sm-3 expand-mobile prodqt" id="prodqty_0" style="width:10%; position:relative; display:inline-block;">
				<label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
                    <input <?php echo ($return ? 'readonly' : ''); ?> data-placeholder="Select a Product..." name="prodquantity[]" id="prodqty_dd_0" onkeyup="numericFilter(this); countPOSTotal(this);" value="0" style="" type="text" class="form-control prodquantity" />
                </div>
                <?php } ?>

				<?php if($return) { ?>
					<div class="col-sm-3 expand-mobile prodqt" id="prodqty_0" style="width:10%; position:relative; display:inline-block;">
					<label for="company_name" class="col-sm-4 show-on-mob control-label">Returned:</label>
						<input <?php echo ($return ? 'readonly' : ''); ?> data-placeholder="Quantity Returned" name="prodreturned[]" id="prodret_dd_0" onchange="returnFilter(this); countPOSTotal(this);" value="0" style="" type="text" class="form-control prodreturned" />
					</div>
				<?php } ?>

                <div class="col-sm-1 m-top-mbl" >
                    <a href="#" onclick="seleteService(this,'prodservices_','prodqty_dd_'); return false;" id="proddeleteservices_0" class="btn brand-btn">Delete</a>
                </div>
            </div>

            </div>

            <div id="add_here_new_positionprod"></div>

            <div class="col-sm-12  triple-gap-bottom" <?php echo ($return ? 'style="display:none;"' : ''); ?>>
                <button id="add_position_buttonprod" class="btn brand-btn mobile-block">Add</button>
            </div>
            <?php } ?>
				<!-- END Products -->

				<!-- Services -->
            <?php if (strpos($value_config, ','."servServices".',') !== FALSE) { ?>

            <div class="form-group clearfix">
			<label class="col-sm-1 text-center" style="width:20%;"><h4>Service(s)</h4></label>
			</div>
			  <div class="form-group clearfix hide-titles-mob">
                <?php if (strpos($value_config, ','."servCategory".',') !== FALSE) { ?>
                    <label class="col-sm-1 text-center" style="width:20%;">Category</label>
                <?php } ?>
                <?php if (strpos($value_config, ','."servService Type".',') !== FALSE) { ?>
                    <!--<label class="col-sm-1 text-center" style="width:15%;">Service Type</label>-->
                <?php } ?>
                <?php if (strpos($value_config, ','."servHeading".',') !== FALSE) { ?>
                    <label class="col-sm-3 text-center" style="position:relative;width:20%">Heading</label>
                <?php } ?>
                <?php if (strpos($value_config, ','."servPrice".',') !== FALSE) { ?>
                    <label class="col-sm-1 text-center" style="position:relative;width:10%">Price</label>
                <?php } ?>
                <?php if (strpos($value_config, ','."servQuantity".',') !== FALSE) { ?>
                    <label class="col-sm-1 text-center" style="position:relative;width:10%">Quantity</label>
                <?php } ?>
                <?php if ($return) { ?>
                    <label class="col-sm-1 text-center" style="position:relative;width:10%">Returned</label>
                <?php } ?>
            </div>


          <div class="additional_positionserv">
            <div class="clearfix"></div>
            <div class="form-group clearfix" id="servservices_0" width="100%">

                <?php if (strpos($value_config, ','."servCategory".',') !== FALSE) { ?>
                <div class="col-sm-1 servtype expand-mobile"  style="width:20%; display:inline-block; position:relative;" id="servcategory_0">
				<label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
                    <select data-placeholder="Select a Category..." id="servcategory_dd_0" name="servcategory[]" class="chosen-select-deselect form-control servcategory">
                        <option value=""></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT DISTINCT category FROM services WHERE include_in_pos != '0' order by category");
                        while($row = mysqli_fetch_array($query)) {
                            ?><option id='<?php echo $row['category'];?>' value='<?php echo $row['category'];?>'><?php echo $row['category'];?></option><?php
                        }
                        ?>
                    </select>
                </div>
                <?php } /* ?>

                <?php if (strpos($value_config, ','."servService Type".',') !== FALSE) { ?>
                <div class="col-sm-1"  style="width:15%; display:inline-block; position:relative;" id="servpart_0">
                <select data-placeholder="Select a Type..." onchange="servselectProduct(this)"  id="servpart_dd_0" name="servpart_no[]" class="chosen-select-deselect form-control servpart">
                    <option value=""></option>
                    <?php
                    $query = mysqli_query($dbc,"SELECT serviceid, service_type FROM services WHERE deleted=0");
                    while($row = mysqli_fetch_array($query)) {
                        ?><option value='<?php echo $row['serviceid'];?>'><?php echo $row['service_type'];?></option><?php
                    }
                    ?>
                </select>
                </div>
                <?php }  */?>

                <?php if (strpos($value_config, ','."servHeading".',') !== FALSE) { ?>
                <div class="col-sm-3 expand-mobile" id="servproduct_0" style="width:20%; position:relative; display:inline-block;">
				<label for="company_name" class="col-sm-4 show-on-mob control-label">Heading:</label>
                    <select data-placeholder="Select a Heading..." name="servinventoryid[]" id="servproduct_dd_0" class="chosen-select-deselect form-control servproduct" style="position:relative;">
                        <option value=""></option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT serviceid, heading FROM services WHERE deleted=0 order by heading");
                        while($row = mysqli_fetch_array($query)) {
                            ?><option value='<?php echo $row['serviceid'];?>'><?php echo $row['heading'];?></option><?php
                        }
                        ?>
                    </select>
                </div>
                <?php } ?>

                <?php if (strpos($value_config, ','."servPrice".',') !== FALSE) { ?>
                    <div class="col-sm-1 expand-mobile" id="servprice_0" style="width:10%; position:relative; display:inline-block;">
                    <label for="company_name" class="col-sm-4 show-on-mob control-label">Price:</label>
                        <input <?= ( $return || strpos($value_config, ',servPriceEdit,')===false ) ? 'readonly' : ''; ?> data-placeholder="Select a Product..." name="servprice[]" id="servprice_dd_0" value="0" style="" onkeyup="countPOSTotal(this);" type="text" class="form-control servprice" />
                    </div>
                <?php } ?>

                <?php if (strpos($value_config, ','."servQuantity".',') !== FALSE) { ?>
				<label for="company_name expand-mobile" class="col-sm-4 show-on-mob control-label">Quantity:</label>
                <div class="col-sm-3 servqt expand-mobile" id="servqty_0" style="width:10%; position:relative; display:inline-block;">
                    <input <?php echo ($return ? 'readonly' : ''); ?> data-placeholder="Select a Product..." name="servquantity[]" id="servqty_dd_0" onkeyup="numericFilter(this); countPOSTotal(this);" value="0" style="" type="text" class="form-control servquantity" />
                </div>
                <?php } ?>

                <?php if ($return) { ?>
				<label for="company_name expand-mobile" class="col-sm-4 show-on-mob control-label">Returned:</label>
                <div class="col-sm-3 servqt expand-mobile" id="servqty_0" style="width:10%; position:relative; display:inline-block;">
                    <input data-placeholder="Quantity Returned" name="servreturned[]" id="servret_dd_0" onchange="returnFilter(this); countPOSTotal(this);" value="0" style="" type="text" class="form-control servreturned" />
                </div>
                <?php } ?>
                <div class="col-sm-1 m-top-mbl" >
                    <a href="#" onclick="seleteService(this,'servservices_','servqty_dd_'); return false;" id="servdeleteservices_0" class="btn brand-btn">Delete</a>
                </div>
            </div>

            </div>

            <div id="add_here_new_positionserv"></div>

            <div class="col-sm-12 triple-gap-bottom" <?php echo ($return ? 'style="display:none;"' : ''); ?>>
                <button id="add_position_buttonserv" class="btn brand-btn mobile-block">Add</button>
            </div>
            <?php } ?>
				<!-- END Services -->

            <?php if (strpos($value_config, ','."Misc Item".',') !== FALSE) { ?>
            <div class="form-group clearfix hide-titles-mob">
                <label class="col-sm-3 text-center" style="position:relative;width:20%">Misc Product</label>
                <label class="col-sm-1 text-center" style="position:relative;width:10%">Price</label>
                <label class="col-sm-1 text-center" style="position:relative;width:10%">Quantity</label>
				<?php if($return) { ?>
					<label class="col-sm-1 text-center" style="position:relative;width:10%">Returned</label>
				<?php } ?>
            </div><?php

			if ( $count_misc != 0 || $count_misc != '' || $return) {
				for ( $i=0; $i< ($return ? $count_misc : $count_misc + 1); $i++ ) { ?>
					<div class="additional_misc">
						<div class="clearfix"></div>
						<div class="form-group clearfix" id="misc_<?= $i; ?>" width="100%">
							<div class="col-sm-1 expand-mobile" id="miscproduct_<?= $i; ?>" style="width:20%; position:relative; display:inline-block;">
							<label for="company_name" class="col-sm-4 show-on-mob control-label">Misc Product:</label>
								<input <?php echo ($return ? 'readonly' : ''); ?> data-placeholder="Select a Product..." id="misc_product_<?= $i; ?>" name="misc_product[]" type="text" class="form-control misc_product" value="<?= ( $misc_desc[$i] ) ? $misc_desc[$i] : 0; ?>" />
							</div>

							<div class="col-sm-1 expand-mobile" id="price_<?= $i; ?>" style="width:10%; position:relative; display:inline-block;">
							<label for="company_name" class="col-sm-4 show-on-mob control-label">Price:</label>
								<input <?php echo ($return ? 'readonly' : ''); ?> data-placeholder="Select a Product..." name="misc_price[]" id="misc_price_dd_<?= $i; ?>" value="<?= ( $misc_price[$i] ) ? $misc_price[$i] : 0; ?>" style="" onkeyup="countPOSTotal(this);" type="text" class="form-control misc_price" />
							</div>

							<div class="col-sm-1 expand-mobile" id="quantity_<?= $i; ?>" style="width:10%; position:relative; display:inline-block;">
							<label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
								<input <?php echo ($return ? 'readonly' : ''); ?> data-placeholder="Select Quantity..." name="misc_quantity[]" id="misc_quantity_dd_<?= $i; ?>" value="<?= ( $misc_quantity[$i] ) ? $misc_quantity[$i] : 0; ?>" style="" onkeyup="numericFilter(this); countPOSTotal(this);" type="text" class="form-control misc_quantity" />
							</div>

							<?php if($return) { ?>
							<div class="col-sm-1 expand-mobile" id="quantity_<?= $i; ?>" style="width:10%; position:relative; display:inline-block;">
							<label for="company_name" class="col-sm-4 show-on-mob control-label">Returned:</label>
								<input data-placeholder="Quantity Returned" name="misc_returned[]" id="misc_returned_dd_<?= $i; ?>" value="<?= ( $misc_returned[$i] ) ? $misc_returned[$i] : 0; ?>" style="" onchange="returnFilter(this); countPOSTotal(this);" type="text" class="form-control misc_return" />
							</div>
							<?php } ?>

							<div class="col-sm-1 m-top-mbl" >
								<a href="#" onclick="seleteService(this,'misc_','misc_quantity_dd_'); return false;" id="deletemisc_<?= $i; ?>" class="btn brand-btn">Delete</a>
							</div>
						</div>

					</div><!-- . additional_misc --><?php
				}

			} else { ?>
				<div class="additional_misc">
					<div class="clearfix"></div>
					<div class="form-group clearfix" id="misc_0" width="100%">
						<div class="col-sm-1 expand-mobile" id="miscproduct_0" style="width:20%; position:relative; display:inline-block;">
						<label for="company_name" class="col-sm-4 show-on-mob control-label">Misc Product:</label>
							<input data-placeholder="Select a Product..." id="misc_product_0" name="misc_product[]" type="text" class="form-control misc_product" />
						</div>

						<div class="col-sm-1 expand-mobile" id="price_0" style="width:10%; position:relative; display:inline-block;">
						<label for="company_name" class="col-sm-4 show-on-mob control-label">Price:</label>
							<input data-placeholder="Select a Product..." name="misc_price[]" id="misc_price_dd_0" value="0" style="" onkeyup="countPOSTotal(this);" type="text" class="form-control misc_price" />
						</div>

						<div class="col-sm-1 expand-mobile" id="quantity_0" style="width:10%; position:relative; display:inline-block;">
						<label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
							<input data-placeholder="Select Quantity..." name="misc_quantity[]" id="misc_quantity_dd_0" value="0" style="" onkeyup="numericFilter(this); countPOSTotal(this);" type="text" class="form-control misc_quantity" />
						</div>

						<div class="col-sm-1 m-top-mbl" >
							<a href="#" onclick="seleteService(this,'misc_','misc_quantity_dd_'); return false;" id="deletemisc_0" class="btn brand-btn">Delete</a>
						</div>
					</div>

				</div><!-- . additional_misc --><?php
			} ?>

            <div id="add_here_new_misc"></div>

            <div class="col-sm-12 triple-gap-bottom">
                <button id="add_misc_button" class="btn brand-btn mobile-block">Add</button>
            </div>
             <?php } ?>
			</div>

            <?php if (strpos($value_config, ','."Total Price".',') !== FALSE) { ?>
              <div class="form-group">
                <label for="site_name" class="col-sm-3 control-label">Sub-Total<span class="brand-color">*</span>:</label>
                <div class="col-sm-9">
                  <input <?php echo ($return ? 'readonly' : ''); ?> name="sub_total" id="sub_total" value="<?= ($sub_total) ? $sub_total : 0; ?>" type="text" class="form-control" />
                </div>
              </div>
              <?php } ?>

            <?php if (strpos($value_config, ','."Discount".',') !== FALSE) { ?>
                <div class="form-group">
                    <label for="site_name" class="col-sm-3 control-label">Discount Type:</label>
                    <div class="col-sm-9">
                      <div class="radio">
                        <label class="double-pad-right"><input <?php echo ($return ? 'onclick="return false;"' : ''); ?> type="radio" style="height:20px; width:20px; margin-right:20px;" name="discount_type" value="%" <?= ($discount_type=='%') ? 'selected="selected"' : ''; ?>>%</label>
                        <label class="pad-right"><input <?php echo ($return ? 'onclick="return false;"' : ''); ?> type="radio" style="height:20px;width:20px; margin-right:20px;" name="discount_type" value="$" <?= ($discount_type=='$') ? 'selected="selected"' : ''; ?>>$</label>
                      </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="site_name" class="col-sm-3 control-label">Discount Value:</label>
                    <div class="col-sm-9">
                      <input <?php echo ($return ? 'readonly' : ''); ?> name="discount_value" onkeyup="countPOSTotal(this);" id="discount_value" value="<?= ($discount_value) ? $discount_value : 0; ?>" type="text" class="form-control" />
                    </div>
                </div>

                <div class="form-group">
                <label for="site_name" class="col-sm-3 control-label">Total After Discount:</label>
                    <div class="col-sm-9">
                        <input <?php echo ($return ? 'readonly' : ''); ?> name="total_after_discount" id="total_after_discount" value="<?= ($total_after_discount) ? $total_after_discount : 0; ?>" type="text" class="form-control" />
                    </div>
                </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Coupon".',') !== FALSE) { ?>
                <div class="form-group">
					<label for="couponid" class="col-sm-3 control-label">Coupon:</label>
                    <div class="col-sm-9">
						<select data-placeholder="Select a Coupon..." name="couponid" id="couponid"  onchange="countPOSTotal(this);" class="chosen-select-deselect form-control product" style="position:relative;">
                            <option value=""></option><?php
                            $result = mysqli_query ( $dbc, "SELECT `couponid`, `title`, `discount_type`, `discount` FROM `pos_touch_coupons` WHERE `deleted`=0 ORDER BY `title`" );
							while ( $row=mysqli_fetch_assoc($result) ) {
								$selected = ( $couponid == $row[ 'couponid' ] ) ? 'selected="selected"' : '';
								if ( $row['discount_type']=='%' ) {
									$coupon_value_display = $row['discount'] . '% Off';
									$coupon_type	= ' data-type="%"';
									$coupon_amount	= ' data-amount="'. $row['discount'] . '"';
								} else {
									$coupon_value_display = '$' . number_format ( $row['discount'], 2 ) . 'Off';
									$coupon_type	= ' data-type="$"';
									$coupon_amount	= ' data-amount="'. $row['discount'] . '"';
								}
								echo "<option " . $selected . " value=" . $row['couponid'] . '"' . $coupon_type . $coupon_amount . ">" . $row['title'] . ' - ' . $coupon_value_display . "</option>";
							} ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
					<label for="site_name" class="col-sm-3 control-label">Total After Coupon:</label>
                    <div class="col-sm-9">
                        <input <?php echo ($return ? 'readonly' : ''); ?> name="total_after_coupon" id="total_after_coupon" value="<?= ($total_after_coupon) ? $total_after_coupon : 0; ?>" type="text" class="form-control" />
                    </div>
                </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Gift Card".',') !== FALSE) { ?>
                <div class="form-group">
                    <label for="site_name" class="col-sm-3 control-label">Gift Card Number:</label>
                    <div class="col-sm-9">
                      <input type="text" <?php echo ($return ? 'readonly' : ''); ?> name="gf_number" onblur="countPOSTotal(this);" id="gf_number" value="<?= ($gf_number) ? $gf_number : ''; ?>" type="text" class="form-control" />
                    </div>
                </div>

                <div class="form-group">
                <label for="site_name" class="col-sm-3 control-label">Total After Gift Card Discount:</label>
                    <div class="col-sm-9">
                        <input <?php echo ($return ? 'readonly' : ''); ?> name="total_after_gf" id="total_after_gf" value="<?= ($total_after_gf_discount) ? $total_after_gf_discount : 0; ?>" type="text" class="form-control" />
                    </div>
                </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Delivery".',') !== FALSE) { ?>
                <div class="form-group">
                <label for="site_name" class="col-sm-3 control-label">Delivery Option:</label>
                    <div class="col-sm-9">
						<select data-placeholder="Select a Type..." name="delivery_type" id="delivery_type"  onchange="selectShippingtype()" class="chosen-select-deselect form-control product" style="position:relative;">
                            <option value=""></option>
                            <option value="Pick-Up" <?= ($delivery_type=='Pick-Up') ? 'selected="selected"' : ''; ?>>Pick-Up</option>
                            <option value="Company Delivery" <?= ($delivery_type=='Company Delivery') ? 'selected="selected"' : ''; ?>>Company Delivery</option>
                            <option value="Drop Ship" <?= ($delivery_type=='Drop Ship') ? 'selected="selected"' : ''; ?>>Drop Ship</option>
                            <option value="Shipping" <?= ($delivery_type=='Shipping') ? 'selected="selected"' : ''; ?>>Shipping</option>
                            <option value="Shipping on Customer Account" <?= ($delivery_type=='Shipping on Customer Account') ? 'selected="selected"' : ''; ?>>Shipping on Customer Account</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="display: none;" id="delivery_address">
                <label for="site_name" class="col-sm-3 control-label">Confirm Delivery Address:</label>
                    <div class="col-sm-9">
                        <input <?php echo ($return ? 'readonly' : ''); ?> name="delivery_address" id="delivery_address_fillup" type="text" class="form-control" value="<?= ($delivery_address) ? $delivery_address : ''; ?>" />
                    </div>
                </div>

              <div class="form-group" style="display: none;" id="contractorid">
                <label for="travel_task" class="col-sm-3 control-label">Contractor:</label>
                <div class="col-sm-9">
                  <select name="contractorid" data-placeholder="Select Contractor..." class="chosen-select-deselect form-control" width="380">
                  <option value=''></option>
					   <?php
							$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Contractor' AND deleted=0 AND `status`=1"),MYSQLI_ASSOC));
							foreach($query as $id) {
								$selected = '';
								$selected = $id == $contractorid ? 'selected = "selected"' : '';
								echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id,'name').'</option>';
							}
						  ?>
                  </select>
                </div>
              </div>

                <div class="form-group" id="delivery_div">
                <label for="site_name" class="col-sm-3 control-label">Delivery/Shipping Amount:</label>
                    <div class="col-sm-9">
                        <input <?php echo ($return ? 'readonly' : ''); ?> name="delivery" onkeyup="countPOSTotal(this);" id="delivery" value="<?= ($delivery) ? $delivery : 0; ?>" type="text" class="form-control" />
                    </div>
                </div>

            <?php } ?>

            <?php if (strpos($value_config, ','."Assembly".',') !== FALSE) { ?>
                <div class="form-group">
                <label for="site_name" class="col-sm-3 control-label">Assembly Amount:</label>
                    <div class="col-sm-9">
                        <input <?php echo ($return ? 'readonly' : ''); ?> name="assembly" onkeyup="countPOSTotal(this);" id="assembly" value="<?= ($assembly) ? $assembly : 0; ?>" type="text" class="form-control" />
                    </div>
                </div>
            <?php } ?>

            <div class="form-group">
            <label for="site_name" class="col-sm-3 control-label">Total Before Tax:</label>
                <div class="col-sm-9">
                    <input <?php echo ($return ? 'readonly' : ''); ?> name="total_before_tax" id="total_before_tax" value="<?= ($total_before_tax) ? $total_before_tax : 0; ?>" type="text" class="form-control" />
                </div>
            </div>

            <?php if (strpos($value_config, ','."Tax".',') !== FALSE) { ?>
                <?php
                $pos_tax_value = get_config($dbc, 'pos_tax');
                $pos_tax = explode('*#*',$pos_tax_value);

                $total_count = mb_substr_count($pos_tax_value,'*#*');
                $tax_rate = 0;
                $tax_exe = 0;



				for ( $eq_loop=0; $eq_loop<=$total_count; $eq_loop++ ) {
                    $pos_tax_name_rate = explode('**',$pos_tax[$eq_loop]);

					if ( $pos_tax_name_rate[3] == 'No' ) {
						$tax_rate += $pos_tax_name_rate[1];
					}

					if ( $pos_tax_name_rate[3] == 'Yes' ) {
						DEFINE('TAX_EXEMPTION', $pos_tax_name_rate[0]); ?>
						<input class="not_count_pos_tax" value="<?= $pos_tax_name_rate[1]; ?>" type="hidden" />
						<input class="not_count_pos_tax_number" value="<?= $pos_tax_name_rate[2]; ?>" type="hidden" /><?php
                        $tax_exe = 1;
					} ?>

                    <div class="clearfix"></div>

					<div class="form-group">
						<label for="site_name" class="col-sm-3 control-label"><?php echo $pos_tax_name_rate[0];?> (%):<br><em>[<?php echo $pos_tax_name_rate[2];?>]</em></label>
						<div class="col-sm-9">
							<input name="pos_tax" readonly value='<?php echo $pos_tax_name_rate[1];?>' type="text" class="form-control pos_tax" />
						</div>
					</div><?php
                }

                if ( $tax_exe == 0 ) { ?>
					<input <?php echo ($return ? 'readonly' : ''); ?> id="yes_tax_exemption" value="0" type="hidden" /><?php
				} else { ?>
					<input <?php echo ($return ? 'readonly' : ''); ?> id="yes_tax_exemption" value="1" type="hidden" /><?php
				}

                echo '<input type="hidden" name="tax_rate" id="tax_rate" value="'.$tax_rate.'" />';
			} ?>

              <div class="form-group">
                <label for="site_name" class="col-sm-3 control-label">Tax Price ($):</label>
                <div class="col-sm-9">
                  <input name="tax_price" id="tax_price" readonly type="text" value="<?= ($total_tax_amount) ? $total_tax_amount : 0; ?>" class="form-control" />
                </div>
              </div>

              <div class="form-group">
                <label for="site_name" class="col-sm-3 control-label">Total Price<span class="brand-color">*</span>:</label>
                <div class="col-sm-9">
                  <input <?php echo ($return ? 'readonly' : ''); ?> name="total_price" id="total_price" type="text" value="<?= ($total_price) ? $total_price : 0; ?>" class="form-control" />
                </div>
              </div>

              <?php if (strpos($value_config, ','."Payment Type".',') !== FALSE) { ?>
              <div class="form-group">
                <label for="site_name" class="col-sm-3 control-label">Payment Type<span class="brand-color">*</span>:</label>
                <div class="col-sm-9">
                  <select id="payment_type" name="payment_type" data-placeholder="Select a Type..." class="chosen-select-deselect form-control" width="380">
                      <option value=""></option>
                      <?php
						//$tabs = get_config($dbc, 'po_invoice_payment_types');
                        $tabs = get_config($dbc, 'invoice_payment_types');
                        $each_tab = explode(',', $tabs);
						 //if (is_array($each_tab) && count($each_tab) > 0) {
                        foreach ($each_tab as $cat_tab) {
                            if ( $invtype == $cat_tab || strpos ( $cat_tab, $payment_type ) !== FALSE ) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
                        }
						 //} else {
						//	 echo "<option value='Pay Now'>Pay Now</option>";
						//	 echo "<option value='Net 30'>Net 30</option>";
						//	 echo "<option value='Net 60'>Net 60</option>";
						//	 echo "<option value='Net 90'>Net 90</option>";
						//	 echo "<option value='Net 120'>Net 120</option>";
						// }
                      ?>
                  </select>
                </div>
              </div>
              <?php } ?>

              <div class="form-group" <?php if (strpos($value_config, ','."Deposit Paid".',') !== FALSE) { } else { echo 'style="display:none;"'; } ?>>
                <label for="site_name" class="col-sm-3 control-label">Deposit Paid ($)<span class="brand-color"></span>:</label>
                <div class="col-sm-9">
                  <input <?php echo ($return ? 'readonly' : ''); ?> name="deposit_paid" onkeyup="countPOSTotal(this);" id="deposit_paid" value="<?= ($deposit_paid) ? $deposit_paid : 0; ?>" type="text" class="form-control" />
				  <input type='hidden' name='updatedtotal' id='updatedtotal' class='form-control'>
                </div>
              </div>

              <?php if (strpos($value_config, ','."Comment".',') !== FALSE) { ?>
              <div class="form-group">
                <label for="site_name" class="col-sm-3 control-label"><?php echo ($return ? 'Return ' : ''); ?>Comment:</label>
                <div class="col-sm-9">
                  <textarea name="comment" rows="4" cols="50" class="form-control"><?= (!$return && $comment) ? $comment : ''; ?></textarea>
                </div>
              </div>
              <?php } ?>

              <?php if (strpos($value_config, ','."Created/Sold By".',') !== FALSE) { ?>
              <div class="form-group">
                <label for="site_name" class="col-sm-3 control-label">Created/Sold By:</label>
                <div class="col-sm-9">
                  <input name="created_by" readonly value="<?= ($created_by) ? $created_by : decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']); ?>" type="text" value=0 class="form-control" />
                </div>
              </div>
              <?php } ?>

            <?php if (strpos($value_config, ','."Ship Date".',') !== FALSE && !$return) { ?>
            <div class="form-group">
                <label for="first_name" class="col-sm-3 control-label text-right">Ship Date:</label>
                <div class="col-sm-9">
                    <?php $ship_date = ( $ship_date == '0000-00-00' ) ? $invoice_date : $ship_date; ?>
					<input name="ship_date" value="<?= ($ship_date) ? $ship_date : date('Y-m-d'); ?>" type="text" class="datepicker form-control" style='width:162px;'></p>
                </div>
            </div>
            <?php } ?>

			<?php if (strpos($value_config, ','."Due Date".',') !== FALSE && !$return) { ?>
            <div class="form-group">
                <label for="first_name" class="col-sm-3 control-label text-right">Due Date:</label>
                <div class="col-sm-9">
                    <input name="due_date" value="<?= ($due_date) ? $due_date : date('Y-m-d'); ?>" type="text" class="datepicker form-control" style='width:162px;'></p>
                </div>
            </div>
            <?php } ?>
			<input type='hidden' name='company_software_name' value='<?PHP echo COMPANY_SOFTWARE_NAME; ?>'>
            <div class="form-group">
                <div class="col-sm-3">
                    <p><span class="empire-red pull-right"><em>Required Fields *</em></span></p>
                </div>
                <div class="col-sm-9"></div>
            </div>

              <div class="form-group">
                <div class="col-sm-12 clearfix">
                    <!--<a href="pending.php" class="btn brand-btn btn-lg pull-right">Back</a>
					<a href="#" class="btn brand-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
					<?php if(!empty($_GET['edit'])) { ?>
						<a href="<?php echo (empty($_GET['from_url']) ? 'point_of_sell.php' : $_GET['from_url']); ?>" class="btn brand-btn">Back</a>
					<?php } ?>
                    <button type="submit" name="submit_pos" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
                </div>
              </div>

            <script type="text/javascript">
              var config = {
                '.chosen-select'           : {},
                '.chosen-select-deselect'  : {allow_single_deselect:true},
                '.chosen-select-no-single' : {disable_search_threshold:10},
                '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
                '.chosen-select-width'     : {width:"95%"}
              }
			  <?php if($return) { ?>
				  $("select").each(function() { $(this).data('current',$(this).val()); });
				  $('select').off('change','*');
				  $("select").click(function() { $(this).blur(); return false; });
				  $("select").attr('readonly','readonly');
			  <?php } else { ?>
              for (var selector in config) {
                $(selector).select2({
                	width: '100%'
                });
              }
			  <?php } ?>
            </script>

        </form>

	</div>
</div>

<?php include ('../footer.php'); ?>
