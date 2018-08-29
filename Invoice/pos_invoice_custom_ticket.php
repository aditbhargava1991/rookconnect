<?php include_once ('../include.php');
$point_of_sell = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM invoice WHERE invoiceid='$invoiceid'"));
if(empty($posid)) {
	$posid = $invoiceid;
}
$contactid		= $point_of_sell['patientid'];
$couponid		= $point_of_sell['couponid'];
$coupon_value	= $point_of_sell['coupon_value'];

// if ( $edit_id == '0' ) {
	// $edited = '';
// } else {
	// $edited = '_' . $edit_id;
// }

$customer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT name, first_name, last_name, office_phone, home_phone, cell_phone, email_address, business_address, city, state, country, zip_code, referred_by FROM contacts WHERE contactid='$contactid'"));

$customer_phone = '';

if ( decryptIt($customer['office_phone']) != '' || decryptIt($customer['office_phone']) != NULL ) {
	$customer_phone = decryptIt($customer['office_phone']);
} else {
	if ( decryptIt($customer['cell_phone']) != '' || decryptIt($customer['cell_phone']) != NULL ) {
		$customer_phone = decryptIt($customer['cell_phone']);
	}
}

//Tax
$point_of_sell_product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(gst) AS total_gst, SUM(pst) AS total_pst FROM invoice_lines WHERE invoiceid='$invoiceid'"));

$get_pos_tax = get_config($dbc, 'pos_tax');
$pdf_tax = '';
if($get_pos_tax != '') {
	$pos_tax = explode('*#*',$get_pos_tax);

	$total_count = mb_substr_count($get_pos_tax,'*#*');
	for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
		$pos_tax_name_rate = explode('**',$pos_tax[$eq_loop]);

		if (strcasecmp($pos_tax_name_rate[0], 'gst') == 0) {
			$taxrate_value = $point_of_sell['gst_amt'];
		}
		if (strcasecmp($pos_tax_name_rate[0], 'pst') == 0) {
			$taxrate_value = $point_of_sell['pst_amt'];
		}

		if($pos_tax_name_rate[3] == 'Yes' && $point_of_sell['client_tax_exemption'] == 'Yes') {

		} else {
			$pdf_tax .= $pos_tax_name_rate[0] .' : '.$pos_tax_name_rate[1].'% : $'.number_format($taxrate_value, 2).'<br>';
		}

		$pdf_tax_number .= $pos_tax_name_rate[0].' ['.$pos_tax_name_rate[2].'] <br>';

		if($pos_tax_name_rate[3] == 'Yes' && $point_of_sell['client_tax_exemption'] == 'Yes') {
			$client_tax_number = $pos_tax_name_rate[0].' ['.$tax_exemption_number.']';
		}
	}
}
//Tax

$invoice_footer = get_config($dbc, 'invoice_footer');

$logo = 'download/'.get_config($dbc, 'invoice_logo');
if(!file_exists($logo)) {
    $logo = '../POSAdvanced/'.$logo;
    if(!file_exists($logo)) {
        $logo = '';
    }
}
DEFINE('POS_LOGO', $logo);
DEFINE('INVOICE_FOOTER', $invoice_footer);

	// PDF

class MYPDF extends TCPDF {

	//Page header
	public function Header() {
		// Logo
		if(!empty(POS_LOGO)) {
			$image_file = POS_LOGO;
			if(file_get_contents($image_file)) {
				$image_file = $image_file;
			} else {
				$image_file = '../Point of Sale/'.$image_file;
			}
			$image_file = str_replace(' ', '%20', $image_file);
			if(file_get_contents($image_file)) {
				$this->Image($image_file, 10, 10, 51, '', '', '', 'T', false, 300, 'C', false, false, 0, false, false, false);
			}
		}
		$this->SetFont('helvetica', '', 8);
		$header_text = '';
		$this->writeHTMLCell(0, 0, '', '', $header_text, 0, 0, false, "L", "R",true);
	}

	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-25);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		// Page number
		$footer_text = INVOICE_FOOTER;
		$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
	}
}

$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
$pdf->setFooterData(array(0,64,0), array(0,64,128));

$pdf->SetMargins(PDF_MARGIN_LEFT, 30, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$pdf->AddPage();
$pdf->SetFont('helvetica', '', 9);

$html = '<br><br><br /><br /><center><div style="margin-top:10px; text-align:center;"><h1>Invoice  #'.$posid.'</h1></div></center>
<div style="font-size:10px;">
	<table style="padding:3px; text-align:center;" border="1px" class="table table-bordered">
<tr style="padding:3px;  text-align:center" >
	<th colspan="4" style="background-color:grey; color:black;">Customer Information</th>
</tr>
<tr style="padding:3px;  text-align:center; background-color:white; color:black;" >
	<td>Customer Name</td>
	<td>Customer Phone</td>
	<td>Email</td>
	<td>Reference</td>
</tr>
<tr style="background-color:lightgrey; color:black;">
	<td>'.( !empty($customer['name']) ? decryptIt($customer['name']).': ' : '') . decryptIt($customer['first_name']) .' '. decryptIt($customer['last_name']) .'</td>
	<td>'.$customer_phone.'</td>
	<td>'.decryptIt($customer['email_address']).'</td>
	<td>'.$customer['referred_by'].'</td>
</tr>';

if($client_tax_number != '') {
	$html .= '<tr style="padding:3px;  text-align:center" >
		<th colspan="4" style="background-color:white; color:black;">Tax Exemption : '.$point_of_sell['tax_exemption_number'].'</th>
	</tr>';
}
$html .= '
</table>
<br><br><br>
';

// START INVENTORY & MISC PRODUCTS
$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'inventory' AND item_id IS NOT NULL");
$result2 = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'misc product' AND `ticketid` = 0");
$return_result = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`returned_qty`) FROM `invoice_lines` WHERE `invoiceid`='$invoiceid'"))[0];
$num_rows = mysqli_num_rows($result);
$num_rows2 = mysqli_num_rows($result2);
if($num_rows > 0 && $num_rows2 > 0) {
	$titler = 'Inventory & Misc Products';
} else if ($num_rows > 0 && $num_rows2 == 0) {
	$titler = 'Inventory';
} else if($num_rows == 0 && $num_rows2 > 0) {
	$titler = 'Misc Products';
}
if($num_rows > 0 || $num_rows2 > 0) {
	$html .= '<h2>'.$titler.'</h2>
		<table border="1px" style="padding:3px; border:1px solid black;">
		<tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">';

	// Don't display Part# for SEA
	if ( $rookconnect !== 'sea' ) {
		$html .= '<th>Part#</th>';
	}

	$html .= '<th>Product</th><th>Quantity</th>';
	if($return_result > 0) {
		$html .= '<th>Returned</th>';
	}
	$html .= '<th>Price</th><th>Total</th></tr>';

	while($row = mysqli_fetch_array( $result )) {
		$inventoryid = $row['item_id'];
		$price = $row['unit_price'];
		$quantity = $row['quantity'];
		$returned = $row['returned_qty'];

		if($inventoryid != '') {
			$amount = $price*($quantity-$returned);

			$html .= '<tr>';

				// Don't display Part# for SEA
				if ( $rookconnect !== 'sea' ) {
					$html .= '<td>' . get_inventory ( $dbc, $inventoryid, 'part_no' ) . '</td>';
				}

				$html .= '<td>'.get_inventory($dbc, $inventoryid, 'name').'</td>';
				$html .= '<td>'.number_format($quantity,0).'</td>';
				if($return_result > 0) {
					$html .= '<td>'.$returned.'</td>';
				}
				$html .= '<td>$'.$price.'</td>';
				$html .= '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
			$html .= '</tr>';
		}
	}

	while($row = mysqli_fetch_array( $result2 )) {
		$misc_product = $row['description'];
		$price = $row['unit_price'];

		if($misc_product != '') {
			$html .= '<tr>';
			$html .=  '<td>Not Available</td>';
			$html .=  '<td>'.$misc_product.'</td>';
			$html .=  '<td>'.number_format($row['quantity'],0).'</td>';
			if($return_result > 0) {
				$html .= '<td>'.$row['returned_qty'].'</td>';
			}
			$html .=  '<td>$'.$price.'</td>';
			$html .=  '<td style="text-align:right;">$'.$price * ($row['quantity'] - $row['returned_qty']).'</td>';
			$html .= '</tr>';
		}
	}
	$html .= '</table>';
}
// END INVENTORY AND MISC PRODUCTS

// START PRODUCTS
$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'product' AND item_id IS NOT NULL");
$num_rows3 = mysqli_num_rows($result);
if($num_rows3 > 0) {
	if($num_rows > 0 || $num_rows2 > 0) { $html .= '<br>'; }
	$html .= '<h2>Product(s)</h2>
		<table border="1px" style="padding:3px; border:1px solid black;">
		<tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">
		<th>Category</th><th>Heading</th><th>Quantity</th>';
		if($return_result > 0) {
			$html .= '<th>Returned</th>';
		}
		$html .= '<th>Price</th><th>Total</th></tr>';
	while($row = mysqli_fetch_array( $result )) {
		$inventoryid = $row['inventoryid'];
		$price = $row['price'];
		$quantity = $row['quantity'];
		$returned		= $row['returned_qty'];

		if($inventoryid != '') {
			$amount = $price*($quantity-$returned);
			$html .= '<tr>';
			$html .=  '<td>'.get_products($dbc, $inventoryid, 'category').'</td>';
			$html .=  '<td>'.get_products($dbc, $inventoryid, 'heading').'</td>';
			$html .=  '<td>'.number_format($quantity,0).'</td>';
			if($return_result > 0) {
				$html .= '<td>'.$returned.'</td>';
			}
			$html .=  '<td>$'.$price.'</td>';
			$html .=  '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
			$html .= '</tr>';
		}
	}
	$html .= '</table>';
}
// END PRODUCTS

// START SERVICES
$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'service' AND item_id IS NOT NULL AND `ticketid` = 0");
$num_rows4 = mysqli_num_rows($result);
if($num_rows4 > 0) {
	if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0) { $html .= '<br>'; }
	$html .= '<h2>Service(s)</h2>
		<table border="1px" style="padding:3px; border:1px solid black;">
		<tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">
		<th>Category</th><th>Heading</th><th>Quantity</th>';
		if($return_result > 0) {
			$html .= '<th>Returned</th>';
		}
		$html .= '<th>Price</th><th>Total</th></tr>';
	while($row = mysqli_fetch_array( $result )) {
		$inventoryid = $row['item_id'];
		$price = $row['unit_price'];
		$quantity = $row['quantity'];
		$returned		= $row['returned_qty'];

		if($inventoryid != '') {
			$amount = $price*($quantity-$returned);
			$html .= '<tr>';
			$html .=  '<td>'.get_services($dbc, $inventoryid, 'category').'</td>';
			$html .=  '<td>'.get_services($dbc, $inventoryid, 'heading').'</td>';
			$html .=  '<td>'.number_format($quantity,0).'</td>';
			if($return_result > 0) {
				$html .= '<td>'.$returned.'</td>';
			}
			$html .=  '<td>$'.$price.'</td>';
			$html .=  '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
			$html .= '</tr>';
		}
	}
	$html .= '</table>';
}
// END SERVICES

// START VPL
$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'vpl' AND item_id IS NOT NULL");
$num_rows5 = mysqli_num_rows($result);
if($num_rows5 > 0) {
	if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0 || $num_rows4 > 0) { $html .= '<br>'; }

	$html .= '<h2>Vendor Price List Item(s)</h2>
		<table border="1px" style="padding:3px; border:1px solid grey;">
		<tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">';

	// Don't display Part# for SEA
	if ( $rookconnect !== 'sea' ) {
		$html .= '<th>Part#</th>';
	}

	$html .= '<th>Product</th><th>Quantity</th>';
	if($return_result > 0) {
		$html .= '<th>Returned</th>';
	}
	$html .= '<th>Price</th><th>Total</th></tr>';

	while($row = mysqli_fetch_array( $result )) {
		$inventoryid = $row['inventoryid'];
		$price = $row['price'];
		$quantity = $row['quantity'];
		$returned		= $row['returned_qty'];

		if($inventoryid != '') {
			$amount = $price*($quantity-$returned);

			$html .= '<tr>';
			$html .=  '<td>'.get_vpl($dbc, $inventoryid, 'part_no').'</td>';
			$html .=  '<td>'.get_vpl($dbc, $inventoryid, 'name').'</td>';
			$html .=  '<td>'.number_format($quantity,0).'</td>';
			if($return_result > 0) {
				$html .= '<td>'.$returned.'</td>';
			}
			$html .=  '<td>$'.$price.'</td>';
			$html .=  '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
			$html .= '</tr>';
		}
	}
	$html .= '</table>';
}
// END VPL

// START TIME SHEET
$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'time_cards' AND item_id IS NOT NULL");
$num_rows6 = mysqli_num_rows($result);
if($num_rows6 > 0) {
	if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0 || $num_rows4 > 0 || $num_rows5 > 0) { $html .= '<br>'; }

	$html .= '<h2>Time Sheets</h2>
		<table border="1px" style="padding:3px; border:1px solid grey;">
		<tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">';

	$html .= '<th>Heading</th><th>Quantity</th><th>Price</th><th>Total</th></tr>';

	while($row = mysqli_fetch_array( $result )) {
		$amount = $row['sub_total'];

		$html .= '<tr>';
		$html .=  '<td>'.$row['heading'].'</td>';
		$html .=  '<td>'.number_format($row['quantity'],0).'</td>';
		$html .=  '<td>$'.$row['unit_price'].'</td>';
		$html .=  '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
		$html .= '</tr>';
	}
	$html .= '</table>';
}
// START TIME SHEET

// START TICKETS
$result = mysqli_query($dbc, "SELECT DISTINCT(`ticketid`) FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'service' AND item_id IS NOT NULL AND `ticketid` > 0");
$num_rows7 = mysqli_num_rows($result);
if($num_rows7 > 0) {
	if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0 || $num_rows4 > 0 || $num_rows5 > 0|| $num_rows6 > 0) { $html .= '<br>'; }

	$invoice_custom_ticket = array_filter(explode(',',get_config($dbc, 'invoice_custom_ticket')));

	$html .= '<h2>'.TICKET_NOUN.'(s)</h2>
		<table border="1px" style="padding:3px; border:1px solid black;">
		<tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">
		<th width="10%">Date</th><th width="10%">'.TICKET_NOUN.'</th>';
		$service_widths = count($invoice_custom_ticket) + 1;
		$service_widths = 70 / $service_widths;
		foreach($invoice_custom_ticket as $service_id) {
			$service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid` = '$service_id'"));
			$html .= '<th width="'.$service_widths.'%">'.$service['heading'].'</th>';
		}
		$html .= '<th width="'.$service_widths.'%">All Other Items</th><th width="10%">Total</th></tr>';
	while($ticketid = mysqli_fetch_array( $result )) {
		$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$ticketid['ticketid']."'"));

		$line_total = 0;

		$html .= '<tr>';
		$html .= '<td>'.$ticket['to_do_date'].'</td>';
		$html .= '<td>'.get_ticket_label($dbc, $ticket).'</td>';

		foreach($invoice_custom_ticket as $service_id) {
			$html .= '<td>';
			$service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid` = '$service_id'"));
			$result2 = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'service' AND item_id = '$service_id' AND `ticketid` = '".$ticket['ticketid']."'");
			while($row = mysqli_fetch_assoc($result2)) {
				$inventoryid = $row['item_id'];
				$price = $row['unit_price'];
				$quantity = $row['quantity'];
				$returned		= $row['returned_qty'];

				if($inventoryid != '') {
					$amount = $price*($quantity-$returned);
					$line_total += $amount;

					$html .= '<table border="0"><tr>';
					$html .= '<td width="75%">'.$service['heading'].'<br />$'.number_format($price,2).' x '.$quantity.'</td>';
					$html .= '<td width="25%" style="text-align:right;">$'.number_format($amount,2).'</td>';
					$html .= '</tr></table><p style="font-size:1px;"></p>';
				}
			}
			$html .= '</td>';
		}
		$item_id_query = '';
		if(!empty($invoice_custom_ticket)) {
			$item_id_query = "AND item_id NOT IN ('".implode("','", $invoice_custom_ticket)."')";
		}
		$html .= '<td>';
		$result2 = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'service' AND `ticketid` = '".$ticket['ticketid']."' AND `item_id` IS NOT NULL ".$item_id_query);
		while($row = mysqli_fetch_assoc($result2)) {
			$inventoryid = $row['item_id'];
			$service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid` = '$inventoryid'"));
			$price = $row['unit_price'];
			$quantity = $row['quantity'];
			$returned		= $row['returned_qty'];

			if($inventoryid != '') {
				$amount = $price*($quantity-$returned);
				$line_total += $amount;

				$html .= '<table border="0"><tr>';
				$html .= '<td width="75%">'.$service['heading'].'<br />$'.number_format($price,2).' x '.$quantity.'</td>';
				$html .= '<td width="25%" style="text-align:right;">$'.number_format($amount,2).'</td>';
				$html .= '</tr></table><p style="font-size:1px;"></p>';
			}
		}
		$result2 = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'misc product' AND `ticketid` = '".$ticket['ticketid']."' AND `item_id` IS NOT NULL");
		while($row = mysqli_fetch_assoc($result2)) {
			$description = $row['description'];
			$price = $row['unit_price'];
			$quantity = $row['quantity'];
			$returned		= $row['returned_qty'];

			if($description != '' && $quantity > 0) {
				$amount = $price*($quantity-$returned);
				$line_total += $amount;

				$html .= '<table border="0"><tr>';
				$html .= '<td width="75%">'.$description.'<br />$'.number_format($price,2).' x '.$quantity.'</td>';
				$html .= '<td width="25%" style="text-align:right;">$'.number_format($amount,2).'</td>';
				$html .= '</tr></table><p style="font-size:1px;"></p>';
			}
		}
		$html .= '</td>';
		$html .= '<td style="text-align:right;">$'.number_format($line_total,2).'</td>';
		$html .= '</tr>';
	}
	$html .= '</table>';
}
// END TICKETS

$col_span = 4;

if ( !empty($couponid) || $coupon_value!=0 ) {
	$col_span += 1;
}

if($point_of_sell['discount'] != 0) {
	$col_span += 2;
}

if($point_of_sell['delivery'] != 0) {
	$col_span += 1;
}

if($point_of_sell['assembly'] != 0) {
	$col_span += 1;
}
if($point_of_sell['deposit_paid'] != 0 && $point_of_sell['deposit_paid'] != '') {
	$col_span += 2;
}

if($point_of_sell['delivery'] != 0 || $point_of_sell['assembly'] != 0) {
//    $col_span += 1;
}
if($pdf_tax == '') {
	$col_span -= 1;
}
if($point_of_sell['returned_amt'] != '') {
	$col_span += 1;
}
$html .= '
<br><br><br>
<table style="padding:3px;" border="1px" class="table table-bordered">
<tr style="padding:3px;  text-align:center" >
	<th colspan="'.$col_span.'" style="background-color:grey; color:black;">Payment Information</th>
</tr>
<tr style="padding:3px; text-align:center; background-color:white; color:black;" >
	<td>Payment Type</td>';
	if ( !empty($couponid) || $coupon_value!=0 ) {
		$html .= '<td>Coupon Value</td>';
	}
	if($point_of_sell['discount'] != 0) {
		$html .= '<td>Total Before Discount</td>
				  <td>Discount Value</td>
				  <td>Total After Discount</td>
				';
	} else {
		$html .= '<td>Sub Total</td>';
	}
	if($point_of_sell['delivery'] != 0) {
		$html .= '<td>Delivery</td>';
	}
	if($point_of_sell['assembly'] != 0) {
		$html .= '<td>Assembly</td>';
	}
	if($point_of_sell['delivery'] != 0 || $point_of_sell['assembly'] != 0) {
		//$html .= '<td>Final Total</td>';
	}
	if($pdf_tax != '') {
		$html .= '<td>Tax</td>';
	}
	if($point_of_sell['returned_amt'] != 0) {
		$html .= '<td>Returned Total (Including Tax)</td>';
	}

	$html .= '<td>Total</td>';
	if($point_of_sell['deposit_paid'] != 0 && $point_of_sell['deposit_paid'] != '') {
			$html .='<td>Deposit Paid</td>';
			$html .='<td>Updated Total</td>';
		}
	$html .= '
	</tr>
<tr style="background-color:lightgrey; color:black;">
	<td>'.explode('#*#',$point_of_sell['payment_type'])[0].'</td>';
	if ( !empty($couponid) || $coupon_value!=0 ) {
		$html .= '<td>$'.number_format($point_of_sell['coupon_value'], 2).'</td>';
	}
	if($point_of_sell['discount'] != 0) {
		$html .= '<td>$'.number_format($point_of_sell['total_price'], 2).'</td>
				  <td>$'.number_format($point_of_sell['discount'], 2).'</td>
				  <td>$'.number_format($point_of_sell['total_price']-$point_of_sell['discount'], 2).'</td>';
	} else {
		if ( !empty($couponid) || $coupon_value!=0 ) {
			$sub_total_coupon = $point_of_sell['total_price'] - $coupon_value;
			$html .= '<td>$'.number_format($sub_total_coupon, 2).'</td>';
		} else {
			$html .= '<td>$'.number_format($point_of_sell['total_price'], 2).'</td>';
		}
	}
	if($point_of_sell['delivery'] != 0) {
		$html .= '<td>$'.$point_of_sell['delivery'].'</td>';
	}
	if($point_of_sell['assembly'] != 0) {
		$html .= '<td>$'.$point_of_sell['assembly'].'</td>';
	}
	if($point_of_sell['delivery'] != 0 || $point_of_sell['assembly'] != 0) {
		//$html .= '<td>$'.$final_total.'</td>';
	}
	if($pdf_tax != '') {
		$html .= '<td>'.$pdf_tax.'</td>';
	}
	if($point_of_sell['returned_amt'] != '') {
		$html .= '<td>$'.$point_of_sell['returned_amt'].'</td>';
	}
	//$html .= '<td>$'.number_format($point_of_sell['total_price'] - $point_of_sell['returned_amt'], 2).'</td>';
    $html .= '<td>$'.number_format($point_of_sell['final_price'], 2).'</td>';

	if($point_of_sell['deposit_paid'] != 0 && $point_of_sell['deposit_paid'] != '') {
			$html .='<td>$'.$point_of_sell['deposit_paid'].'</td>';
			$html .='<td>$'.$point_of_sell['updatedtotal'].'</td>';
		}


	$html .='
</tr>
</table><br /><br />';

if($pdf_tax != '') {
	$html .= $pdf_tax_number.'<br /><br />';
}
if($point_of_sell['invoice_date'] !== '') {
				$tdduedate = '</tr><tr><td width="25%">Due Date : '.date('Y-m-d', strtotime($roww['invoice_date'] . "+30 days")).'</td>';
			} else { $tdduedate = '';  }
$html .= 'Comments: '.html_entity_decode($comment).'<br><br><br>
<table> <tr><td width="25%">Date: '.$point_of_sell['invoice_date'].'</td><td width="25%"></td>'.$tdduedate.'<td width="25%"></td><td width="25%"></td></tr><tr><td width="25%">Created By: '.decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).'</td><td width="25%"></td><td width="25%"></td><td width="25%"></td></tr></table>
<br><br><br>
<table width="400px" style="border-bottom:1px solid black;"><tr><td>
Signature</td></tr></table></div>
';

if (!file_exists('download')) {
	mkdir('download', 0777, true);
}

$pdf->writeHTML($html, true, false, true, false, '');
?><?php
$pdf->Output('download/invoice_'.$posid.$edited.'.pdf', 'F');