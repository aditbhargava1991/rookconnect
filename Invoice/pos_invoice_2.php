<?php include_once ('../database_connection.php');

$get_invoice = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM invoice WHERE invoiceid='$invoiceid'"));
$contactid		= $get_invoice['patientid'];
$couponid		= $get_invoice['couponid'];
$coupon_value	= $get_invoice['coupon_value'];

// if ( $edit_id == '0' ) {
	// $edited = '';
// } else {
	// $edited = '_' . $edit_id;
// }

$customer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT name, first_name, last_name, home_phone, cell_phone, email_address, business_address, city, state, country, zip_code, referred_by FROM contacts WHERE contactid='$contactid'"));

//Tax
$invoice_lines = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(gst) AS total_gst, SUM(pst) AS total_pst FROM invoice_lines WHERE invoiceid='$invoiceid'"));

$get_pos_tax = get_config($dbc, 'pos_tax');
$pdf_tax = '';
if($get_pos_tax != '') {
	$pos_tax = explode('*#*',$get_pos_tax);

	$total_count = mb_substr_count($get_pos_tax,'*#*');
	for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
		$pos_tax_name_rate = explode('**',$pos_tax[$eq_loop]);

		if (strcasecmp($pos_tax_name_rate[0], 'gst') == 0) {
			$taxrate_value = $get_invoice['gst_amt'];
		}
		if (strcasecmp($pos_tax_name_rate[0], 'pst') == 0) {
			$taxrate_value = $get_invoice['pst_amt'];
		}

		if($pos_tax_name_rate[3] == 'Yes' && $get_invoice['client_tax_exemption'] == 'Yes') {

		} else {
			//$pdf_tax .= $pos_tax_name_rate[0] .' : '.$pos_tax_name_rate[1].'% : $'.$taxrate_value.'<br>';
			$pdf_tax .= '<tr><td style="text-align:right;" width="75%"><strong>'.$pos_tax_name_rate[0] .'['.$pos_tax_name_rate[1].'%]['.$pos_tax_name_rate[2].']</strong></td><td border="1" width="25%" style="text-align:right;">$'.$taxrate_value.'</td></tr>';
		}

		$pdf_tax_number .= $pos_tax_name_rate[0].' ['.$pos_tax_name_rate[2].'] <br>';

		if($pos_tax_name_rate[3] == 'Yes' && $get_invoice['client_tax_exemption'] == 'Yes') {
			$client_tax_number = $pos_tax_name_rate[0].' ['.$tax_exemption_number.']';
		}
	}
}
//Tax

$invoice_footer = get_config($dbc, 'invoice_footer');
if(!empty($get_invoice['type']) && !empty(get_config($dbc, 'invoice_footer_'.$get_invoice['type']))) {
    $invoice_footer = get_config($dbc, 'invoice_footer_'.$get_invoice['type']);
}

$logo = get_config($dbc, 'invoice_logo');
if(!empty($get_invoice['type']) && !empty(get_config($dbc, 'invoice_logo_'.$get_invoice['type']))) {
    $logo = get_config($dbc, 'invoice_logo_'.$get_invoice['type']);
}

$logo = 'download/'.$logo;
if(!file_exists($logo)) {
    $logo = '../POSAdvanced/'.$logo;
    if(!file_exists($logo)) {
        $logo = '';
    }
}
DEFINE('POS_LOGO', $logo);
DEFINE('INVOICE_FOOTER', $invoice_footer);
DEFINE('INVOICE_DATE', $get_invoice['invoice_date']);
DEFINE('INVOICEID', $invoiceid);
DEFINE('DUEDATE', date('Y-m-d', strtotime($roww['invoice_date'] . "+30 days")));
DEFINE('SHIP_DATE', $get_invoice['ship_date']);
DEFINE('SALESPERSON', decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']));
DEFINE('PAYMENT_TYPE', trim(explode('#*#',$get_invoice['payment_type'])[0],','));

// Hide Sales Person from Washtech
if ( $rookconnect !== 'washtech' ) {
	$sales_person = '<br>Sales Person : ' . SALESPERSON;
} else {
	$sales_person = '';
}

// PDF
class MYPDF extends TCPDF {
	//Page header
	public function Header() {
		$image_file = POS_LOGO;
		if(file_get_contents($image_file)) {
			$image_file = $image_file;
		} else {
			$image_file = '../Point of Sale/'.$image_file;
		}
		$image_file = str_replace(' ', '%20', $image_file);
		if(file_get_contents($image_file)) {

			$this->Image($image_file, 0, 3, 51, '', '', '', 'T', false, 300, 'L', false, false, 0, false, false, false);
		}

		$this->SetFont('helvetica', '', 9);
			if(DUEDATE !== '') {
				$tdduedate = '<br>Due Date : '.DUEDATE.'<br>';
			} else { $tdduedate = '';  }
		if($get_invoice['delivery_type'] !== '' && $get_invoice['delivery_type'] !== NULL) {
			$footer_text = '<p style="text-align:right;">Date : ' .INVOICE_DATE.'<br>Invoice# : '.INVOICEID.'<br>Ship Date : ' . SHIP_DATE . $sales_person . '<br>Payment Type : ' .PAYMENT_TYPE.'<br>Shipping Method : '.$get_invoice['delivery_type'].$tdduedate.'</p>';
		} else {
			$footer_text = '<p style="text-align:right;">Date : ' .INVOICE_DATE.'<br>Invoice# : '.INVOICEID.'<br>Ship Date : ' .SHIP_DATE . $sales_person . '<br>Payment Type : ' .PAYMENT_TYPE.$tdduedate.'</p>';
		}
		$this->writeHTMLCell(0, 0, 0 , 10, $footer_text, 0, 0, false, "R", true);
	}


	  protected $last_page_flag = false;

	  public function Close() {
		$this->last_page_flag = true;
		parent::Close();
	  }


	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom /* CHANGED (SetY used to be -25) */
		$this->SetY(-25);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		// Page number
			if ($this->last_page_flag) {
			  // ... footer for the last page ...
			  $footer_text = '<table width="400px" style="border-bottom:1px solid black;text-align:left;font-style: normal !important;font-size:9"><tr><td style="text-align:left;font-style: normal !important;font-size:9">
	Signature</td></tr></table><br><br><br>'.INVOICE_FOOTER;
			} else {
			  // ... footer for the normal page ...
			  $footer_text = INVOICE_FOOTER;
			}

		$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
	}
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
$pdf->setFooterData(array(0,64,0), array(0,64,128));

$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$pdf->AddPage();
$pdf->SetFont('helvetica', '', 9);
//$pdf->AddPage();
$html = '';

//$html .= '<p style="text-align:left;">Box 2052, Sundre, AB, T0M 1X0<br>Phone: 403-638-4030<br>Fax: 403-638-4001<br>Email: info@highlandprojects.com<br>Work Ticket# : </p>';

$html .= '<br><br><br><br><br><p style="text-align:left;">'.decryptIt($customer['name']).' '.decryptIt($customer['first_name']).' '.decryptIt($customer['last_name']).'<br>'.$customer['business_address'].'<br>'.$customer['city'].', '.$customer['state'].' '.$customer['zip_code'].'<br>'.decryptIt($customer['cell_phone']).'<br>'.decryptIt($customer['email_address']).'<br>';

if ( !empty($get_invoice['reference']) ) {
    $html .= 'Reference: '.$get_invoice['reference'].'<br><br>';
}

if($client_tax_number != '') {
	$html .= '<br>Tax Exemption Number : '.$get_invoice['tax_exemption_number'];
}
$html .= '</p>';
// START INVENTORY & MISC PRODUCTS
$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'inventory' AND item_id IS NOT NULL");
$result2 = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'misc product'");
$return_result = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`returned_qty`) FROM `invoice_lines` WHERE `invoiceid`='$invoiceid'"))[0];
$num_rows = mysqli_num_rows($result);
$num_rows2 = mysqli_num_rows($result2);
if($num_rows > 0 && $num_rows2 > 0) {
	$title = 'Inventory & Misc Products';
} else if ($num_rows > 0 && $num_rows2 == 0) {
	$title = 'Inventory';
} else if($num_rows == 0 && $num_rows2 > 0) {
	$title = 'Misc Products';
}
if($num_rows > 0 || $num_rows2 > 0) {
	$html .= '<h2>'.$title.'</h2>
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
		$returned		= $row['returned_qty'];

		if($inventoryid != '') {
			$amount = $price*($quantity-$returned);

			$html .= '<tr>';

				// Don't display Part# for SEA
				if ( $rookconnect !== 'sea' ) {
					$html .= '<td>' . get_inventory ( $dbc, $inventoryid, 'part_no' ) . '</td>';
				}

				$html .= '<td>'.($quantity < 0 ? 'Return: ' : '').get_inventory($dbc, $inventoryid, 'name').'</td>';
				$html .= '<td>'.number_format($quantity,0).'</td>';
				if($return_result > 0) {
					$html .= '<td>'.$returned.'</td>';
				}
				$html .= '<td>$'.$price.'</td>';
				$html .= '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
			$html .= '</tr>';
		}
	}

	$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'misc product'");
	while($row = mysqli_fetch_array( $result )) {
		$misc_product	= $row['description'];
		$price			= $row['unit_price'];
		$quantity		= $row['quantity'];
		$returned		= $row['returned_qty'];
		$amount			= $row['sub_total'];

		if($misc_product != '') {
			$html .= '<tr>';
			$html .=  '<td>Not Available</td>';
			$html .=  '<td>'.($quantity < 0 ? 'Return: ' : '').$misc_product.'</td>';
			$html .=  '<td>' . number_format($quantity,0) . '</td>';
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
// END INVENTORY AND MISC PRODUCTS

// START PRODUCTS
$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'package' AND item_id IS NOT NULL");
$num_rows3 = mysqli_num_rows($result);
if($num_rows3 > 0) {
	if($num_rows > 0 || $num_rows2 > 0) { $html .= '<br>'; }
	$html .= '<h2>Package(s)</h2>
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
			$amount = $row['sub_total'];
			$html .= '<tr>';
			$html .=  '<td>'.get_package($dbc, $inventoryid, 'category').'</td>';
			$html .=  '<td>'.($quantity < 0 ? 'Refund: ' : '').get_package($dbc, $inventoryid, 'heading').'</td>';
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
		$inventoryid = $row['item_id'];
		$price = $row['unit_price'];
		$quantity = $row['quantity'];
		$returned		= $row['returned_qty'];

		if($inventoryid != '') {
			$amount = $row['sub_total'];
			$html .= '<tr>';
			$html .=  '<td>'.get_products($dbc, $inventoryid, 'category').'</td>';
			$html .=  '<td>'.($quantity < 0 ? 'Return: ' : '').get_products($dbc, $inventoryid, 'heading').'</td>';
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
$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'service' AND item_id IS NOT NULL");
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
			$amount = $row['sub_total'];
			$html .= '<tr>';
			$html .=  '<td>'.get_services($dbc, $inventoryid, 'category').'</td>';
			$html .=  '<td>'.($quantity < 0 ? 'Refund: ' : '').get_services($dbc, $inventoryid, 'heading').'</td>';
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
		$inventoryid = $row['item_id'];
		$price = $row['unit_price'];
		$quantity = $row['quantity'];
		$returned		= $row['returned_qty'];

		if($inventoryid != '') {
			$amount = $row['sub_total'];

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

$html .= '
		<br><br>
		<table border="0" cellpadding="2">';
		//if ( !empty($couponid) || $coupon_value!=0 ) {
		//	$html .= '<tr><td style="text-align:right;" width="75%"><strong>Coupon Value</strong></td><td border="1" width="25%" style="text-align:right;">$'.$get_invoice['coupon_value'].'</td></tr>';
		//}
		//if($get_invoice['discount_value'] != 0) {
		//	$html .= '<tr><td style="text-align:right;" width="75%"><strong>Total Before Discount</strong></td><td border="1" width="25%" style="text-align:right;">$'.$get_invoice['total_price'] - $get_invoice['discount_value'].'</td></tr>';
		//	$html .= '<tr><td style="text-align:right;" width="75%"><strong>Discount Value</strong></td><td border="1" width="25%" style="text-align:right;">'.$d_value.'</td></tr>';
		//	$html .= '<tr><td style="text-align:right;" width="75%"><strong>Total After Discount</strong></td><td border="1" width="25%" style="text-align:right;">$'.$get_invoice['total_price'].'</td></tr>';
		//} else {
			$html .= '<tr><td style="text-align:right;" width="75%"><strong>Sub Total</strong></td><td border="1" width="25%" style="text-align:right;">$'.number_format($get_invoice['total_price'], 2).'</td></tr>';
		//}
		if($get_invoice['discount'] !='' && $get_invoice['discount'] != 0) {
			$html .= '<tr><td style="text-align:right;" width="75%"><strong>Discount</strong></td><td border="1" width="25%" style="text-align:right;">$'.number_format($get_invoice['discount'], 2).'</td></tr>';
            $html .= '<tr><td style="text-align:right;" width="75%"><strong>Total After Discount</strong></td><td border="1" width="25%" style="text-align:right;">$'.number_format($get_invoice['total_price'] - $get_invoice['discount'], 2).'</td></tr>';
		}

		if($get_invoice['delivery'] !='' && $get_invoice['delivery'] != 0) {
			$html .= '<tr><td style="text-align:right;" width="75%"><strong>Delivery</strong></td><td border="1" width="25%" style="text-align:right;">$'.number_format($get_invoice['delivery'], 2).'</td></tr>';
		}

		if($get_invoice['assembly'] !='' && $get_invoice['assembly'] != 0) {
			$html .= '<tr><td style="text-align:right;" width="75%"><strong>Assembly</strong></td><td border="1" width="25%" style="text-align:right;">$'.number_format($get_invoice['assembly'], 2).'</td></tr>';
		}

		if($pdf_tax != '') {
			$html .= $pdf_tax;
			//$html .= '<tr><td style="text-align:right;" width="75%"><strong>Tax</strong></td><td border="1" width="25%" style="text-align:right;">'.$pdf_tax.'</td></tr>';
		}

		$html .= '<tr><td style="text-align:right;" width="75%"><strong>Total</strong></td><td border="1" width="25%" style="text-align:right;">$'.number_format($get_invoice['final_price'], 2).'</td></tr>';
		if($get_invoice['deposit_paid'] > 0) {
			$html .='<tr><td style="text-align:right;" width="75%"><strong>Deposit Paid</strong></td><td border="1" width="25%" style="text-align:right;">$'.number_format($get_invoice['deposit_paid'], 2).'</td></tr>';
			$html .='<tr><td style="text-align:right;" width="75%"><strong>Updated Total</strong></td><td border="1" width="25%" style="text-align:right;">$'.number_format($get_invoice['updatedtotal'], 2).'</td></tr>';
		}


		$html .= '</table><br><br>';


$html .= '<br />';

$html .= html_entity_decode($comment).'<br>';

//$html .= 'Payment Method : '.$get_invoice['payment_type'].'<br>';


if($get_invoice['delivery_type'] == 'Pick-Up' || $get_invoice['delivery_type'] == 'Company Delivery') {
	//$html .= '<br><br><br><br><br><br><br><br><br><br><br><br><br>';
	//$html .= '<table width="400px" style="border-bottom:1px solid black;"><tr><td>
	//Signature</td></tr></table></div>
	//';
}

if (!file_exists('download')) {
	mkdir('download', 0777, true);
}

$pdf->writeHTML($html, true, false, true, false, '');
$invoice_type = $get_invoice['type'];
include('../Invoice/pos_invoice_append_ticket.php');
$pdf->Output('download/invoice_'.$invoiceid.'.pdf', 'F');