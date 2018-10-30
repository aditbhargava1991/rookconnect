<?php include_once ('../database_connection.php');
$point_of_sell = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM invoice WHERE invoiceid='$invoiceid'"));
if(empty($posid)) {
	$posid = $invoiceid;
}
$contactid		= $point_of_sell['patientid'];
$couponid		= $point_of_sell['couponid'];
$coupon_value	= $point_of_sell['coupon_value'];
$dep_total		= $point_of_sell['deposit_total'];
$updatedtotal	= $point_of_sell['updatedtotal'];
$customer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid='$contactid'"));

// if ( $edit_id == '0' ) {
	// $edited = '';
// } else {
	// $edited = '_' . $edit_id;
// }

//Tax
$point_of_sell_product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(gst) AS total_gst, SUM(pst) AS total_pst FROM invoice_lines WHERE invoiceid='$invoiceid'"));

$get_pos_tax = get_config($dbc, 'pos_tax');
$pdf_tax = '';
$pdf_tax_number = '';
$gst_rate = 0;
$pst_rate = 0;
if($get_pos_tax != '') {
	$pos_tax = explode('*#*',$get_pos_tax);

	$total_count = mb_substr_count($get_pos_tax,'*#*');
	for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
		$pos_tax_name_rate = explode('**',$pos_tax[$eq_loop]);

		if (strcasecmp($pos_tax_name_rate[0], 'gst') == 0) {
			$taxrate_value = $point_of_sell['gst_amt'];
            $gst_rate = $pos_tax_name_rate[1];
		}
		if (strcasecmp($pos_tax_name_rate[0], 'pst') == 0) {
			$taxrate_value = $point_of_sell['pst_amt'];
            $pst_rate = $pos_tax_name_rate[1];
		}

		if($pos_tax_name_rate[3] == 'Yes' && $point_of_sell['client_tax_exemption'] == 'Yes') {

		} else {
			//$pdf_tax .= $pos_tax_name_rate[0] .' : '.$pos_tax_name_rate[1].'% : $'.$taxrate_value.'<br>';
			$pdf_tax .= '<tr><td align="right" width="75%"><strong>'.$pos_tax_name_rate[0] .'['.$pos_tax_name_rate[1].'%]['.$pos_tax_name_rate[2].']</strong></td><td align="right" border="1" width="25%" style="background-color:rgb(232,238,238);">$'.$taxrate_value.'</td></tr>';
		}

		$pdf_tax_number .= $pos_tax_name_rate[0].' ['.$pos_tax_name_rate[2].'] <br>';

		if($pos_tax_name_rate[3] == 'Yes' && $point_of_sell['client_tax_exemption'] == 'Yes') {
			$client_tax_number = $pos_tax_name_rate[0].' ['.$tax_exemption_number.']';
		}
	}
}
//Tax

$invoice_footer = get_config($dbc, 'invoice_footer');
if(!empty($point_of_sell['type']) && !empty(get_config($dbc, 'invoice_footer_'.$point_of_sell['type']))) {
    $invoice_footer = get_config($dbc, 'invoice_footer_'.$point_of_sell['type']);
}
$payment_type = explode('#*#', $point_of_sell['payment_type']);

$logo = get_config($dbc, 'invoice_logo');
if(!empty($point_of_sell['type']) && !empty(get_config($dbc, 'invoice_logo_'.$point_of_sell['type']))) {
    $logo = get_config($dbc, 'invoice_logo_'.$point_of_sell['type']);
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
DEFINE('INVOICE_DATE', $point_of_sell['invoice_date']);
DEFINE('INVOICEID', $posid);
DEFINE('COMPANY_SOFTWARE_NAME', $company_software_name);
DEFINE('SHIP_DATE', $point_of_sell['ship_date']);
DEFINE('SALESPERSON', decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']));
DEFINE('PAYMENT_TYPE', $payment_type[0]);

// PDF
if ( !class_exists('MYPDF') ) {
    class MYPDF extends TCPDF {
        //Page header
        public function Header() {
            $image_file = POS_LOGO;
            if(file_get_contents($image_file)) {
                $image_file = $image_file;
            } else {
                $image_file = '../Point of Sale/'.$image_file;
            }
            $this->Image($image_file, 0, 3, 51, '', '', '', 'T', false, 300, 'L', false, false, 0, false, false, false);

            $this->SetFont('helvetica', '', 9);

                //$footer_text = '<p style="text-align:right;">Date : ' .INVOICE_DATE.'<br>Invoice# : '.INVOICEID.'<br>Ship Date : ' .SHIP_DATE.'<br>Sales Person : ' .SALESPERSON.'<br>Payment Type : ' .PAYMENT_TYPE.'<br>Shipping Method : '.$point_of_sell['delivery_type'].'</p>';
                $footer_text = '<table border="0"><tr><td style="width:50%;padding:10px;"><br><br><br><br><br></td><td  style="width:50%;"><p style="text-align:right;"><h1><em>Invoice</em></h1>Date: '.INVOICE_DATE.'<br>Invoice #: '.INVOICEID.'<br></p></td></tr></table>';

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
            $this->SetY(-20);
            // Set font
            $this->SetFont('helvetica', 'I', 8);
            // Page number
                if ($this->last_page_flag) {
                  // ... footer for the last page ...
                  //<table width="400px" style="border-bottom:1px solid black;text-align:left;font-style: normal !important;font-size:9"><tr><td style="text-align:left;font-style: normal !important;font-size:9">
        //Signature</td></tr></table>
                  //$footer_text = '<br><br><center><p style="text-align:center;">Transfer Funds to '.COMPANY_SOFTWARE_NAME.'<br>Thank you for your business!</p></center><br>'.INVOICE_FOOTER;
                  $footer_text = '<center><p style="text-align:center;">'. INVOICE_FOOTER .'</p></center>';
                } else {
                  // ... footer for the normal page ...
                  $footer_text = '<center><p style="text-align:center;">'. INVOICE_FOOTER .'</p></center>';
                }

            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
        }
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
if($point_of_sell['invoice_date'] !== '') {
	$tdduedate = '<td>'.date('Y-m-d', strtotime($roww['invoice_date'] . "+30 days")).'</td>';
	$thduedate = '<td>Due Date</td>';
} else { $tdduedate = ''; $thduedate = ''; }
$html .= '<p style="text-align:center"><h2>'.COMPANY_SOFTWARE_NAME.'</h2></p><br><br><br><br>
<table>
    <tr>
        <td style="width:20%">To:</td>
        <td style="width:30%">'.decryptIt($customer['name']).' '.decryptIt($customer['first_name']).' '.decryptIt($customer['last_name']).'<br>'.$customer['mailing_address'].'<br>'.$customer['city'].' '.$customer['state'].' '.$customer['zip_code'].'<br>'.decryptIt($customer['cell_phone']).'<br>'.decryptIt($customer['email_address']).'</td>
        <td style="width:20%">Ship to:</td><td style="width:30%">';
            if ( !empty($point_of_sell['delivery']) && !empty($point_of_sell['delivery_address']) ) {
                $html .= $point_of_sell['delivery_address'];
            } else {
                $html .= decryptIt($customer['name']).' '.decryptIt($customer['first_name']).' '.decryptIt($customer['last_name']).'<br>'.$customer['mailing_address'].'<br>'.$customer['city'].' '.$customer['state'].' '.$customer['zip_code'].'<br>'.decryptIt($customer['cell_phone']).'<br>'.decryptIt($customer['email_address']);
            }
        $html .= '</td>
    </tr>
</table><br><br>';

if ( !empty($point_of_sell['reference']) ) {
    $html .= '<table><tr><td style="width:20%">Reference:</td><td style="width:80%">'.$point_of_sell['reference'].'</td></tr></table><br><br>';
}
$html .= '<table border="1px" style="padding:3px; border:1px solid grey;">
		<tr nobr="true" style="background-color:rgb(140,173,174); color:black; "><td>Salesperson</td><td>Delivery Option</td><td>Ship Date</td><td>Payment Type</td>'.$thduedate.'</tr>
<tr><td>'.SALESPERSON.'</td><td>'.$point_of_sell['delivery_type'].'</td><td>'.SHIP_DATE.'</td><td>'.PAYMENT_TYPE.'</td>'.$tdduedate.'</tr>
</table><br><br>
';

if($client_tax_number != '') {
	$html .= '<br>Tax Exemption Number : '.$point_of_sell['tax_exemption_number'];
}
$html .= '</p>';
// START INVENTORY & MISC PRODUCTS
$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'inventory' AND item_id IS NOT NULL");
$result2 = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'misc product'");
$return_result = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`returned_qty`) FROM `invoice_lines` WHERE `invoiceid`='$invoiceid'"))[0];
$returned_amt = 0;
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
		<table border="1px" style="padding:3px; border:1px solid grey;">
		<tr nobr="true" style="background-color:rgb(140,173,174); color:black;  width:22%;">';

	// Don't display Part# for sea-alberta.rookconnect.com
	//if ( $rookconnect !== 'sea' ) {
		$html .= '<th>Part#</th>';
	//}

	$html .= '<th>Product</th><th>Quantity</th>';
	if($return_result > 0) {
		$html .= '<th>Returned</th>';
	}
	$html .= '<th>Price</th><th>Total</th></tr>';

	while ( $row = mysqli_fetch_array ( $result ) ) {
		$inventoryid	= $row['item_id'];
		$price			= $row['unit_price'];
		$quantity		= $row['quantity'];
		$returned		= $row['returned_qty'];

		if ( $inventoryid != '' ) {
			$amount = $price*($quantity-$returned);

			$html .= '<tr>';
				// Don't display Part# for SEA
				//if ( $rookconnect !== 'sea' ) {
					$html .= '<td>' . get_inventory ( $dbc, $inventoryid, 'part_no' ) . '</td>';
				//}
				$html .= '<td>' . ($quantity < 0 ? 'Return: ' : '').get_inventory ( $dbc, $inventoryid, 'name' ) . '</td>';
				$html .= '<td>' . number_format($quantity, 0) . '</td>';
				if($return_result > 0) {
					$html .= '<td>'.$returned.'</td>';
				}
				$html .= '<td>$'. $price . '</td>';
				$html .= '<td style="text-align:right; background-color:rgb(232,238,238);">$'.number_format($amount,2).'</td>';
			$html .= '</tr>';
		}

        $returned_amt += $price * $returned;
	}

	$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'misc product'");
	while($row = mysqli_fetch_array( $result )) {
		$misc_product = $row['description'];
		$price = $row['unit_price'];
		$qty = $row['quantity'];
		$returned = $row['returned_qty'];

		if($misc_product != '') {
			$html .= '<tr>';
			$html .=  '<td>Not Available</td>';
			$html .=  '<td>'.($qty < 0 ? 'Return: ' : '').$misc_product.'</td>';
			$html .=  '<td>'.number_format($qty,0).'</td>';
			if($return_result > 0) {
				$html .= '<td>'.$returned.'</td>';
			}
			$html .=  '<td>$'.$price.'</td>';
			$html .=  '<td style="text-align:right; background-color:rgb(232,238,238);">$'.number_format(($price * ($qty - $returned)), 2).'</td>';
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
	$html .= '<h2>Product(s)</h2>
		<table border="1px" style="padding:3px; border:1px solid grey;">
		<tr nobr="true" style="background-color:rgb(140,173,174); color:black;  width:22%;">
		<th>Category</th><th>Heading</th><th>Quantity</th>';
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
			$html .=  '<td>'.get_products($dbc, $inventoryid, 'category').'</td>';
			$html .=  '<td>'.($quantity < 0 ? 'Return: ' : '').get_products($dbc, $inventoryid, 'heading').'</td>';
			$html .=  '<td>'.number_format($quantity,0).'</td>';
			if($return_result > 0) {
				$html .= '<td>'.$returned.'</td>';
			}
			$html .=  '<td>$'.$price.'</td>';
			$html .=  '<td style="text-align:right; background-color:rgb(232,238,238);">$'.number_format($amount,2).'</td>';
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
		<table border="1px" style="padding:3px; border:1px solid grey;">
		<tr nobr="true" style="background-color:rgb(140,173,174); color:black;  width:22%;">
		<th>Category</th><th>Heading</th><th>Quantity</th>';
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
			$html .=  '<td>'.get_services($dbc, $inventoryid, 'category').'</td>';
			$html .=  '<td>'.($quantity < 0 ? 'Refund: ' : '').get_services($dbc, $inventoryid, 'heading').'</td>';
			$html .=  '<td>'.number_format($quantity,0).'</td>';
			if($return_result > 0) {
				$html .= '<td>'.$returned.'</td>';
			}
			$html .=  '<td>$'.$price.'</td>';
			$html .=  '<td style="text-align:right; background-color:rgb(232,238,238);">$'.number_format($amount,2).'</td>';
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
		<tr nobr="true" style="background-color:rgb(140,173,174); color:black;  width:22%;">';

	// Don't display Part# for SEA
	//if ( $rookconnect !== 'sea' ) {
		$html .= '<th>Part#</th>';
	//}

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
			$html .=  '<td>'.get_vpl($dbc, $inventoryid, 'part_no').'</td>';
			$html .=  '<td>'.($quantity < 0 ? 'Return: ' : '').get_vpl($dbc, $inventoryid, 'name').'</td>';
			$html .=  '<td>'.number_format($quantity,0).'</td>';
			if($return_result > 0) {
				$html .= '<td>'.$returned.'</td>';
			}
			$html .=  '<td>$'.$price.'</td>';
			$html .=  '<td style="text-align:right; background-color:rgb(232,238,238);">$'.number_format($amount,2).'</td>';
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
		<tr nobr="true" style="background-color:rgb(140,173,174); color:black;  width:22%;">';

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
		if ( !empty($couponid) || $coupon_value!=0 ) {
			$html .= '<tr><td style="text-align:right;" width="75%"><strong>Coupon Value</strong></td><td align="right" border="1" width="25%" style="background-color:rgb(232,238,238);">$'.$point_of_sell['coupon_value'].'</td></tr>';
		}
		if($point_of_sell['discount'] != '' && $point_of_sell['discount'] != 0) {
			$html .= '<tr><td align="right" width="75%"><strong>Total Before Discount</strong></td><td align="right" border="1" width="25%" style="background-color:rgb(232,238,238);">$'.$point_of_sell['total_price'].'</td></tr>';
			$html .= '<tr><td align="right" width="75%"><strong>Discount Value</strong></td><td align="right" border="1" width="25%" style="background-color:rgb(232,238,238);">$'.$point_of_sell['discount'].'</td></tr>';
			$html .= '<tr><td align="right" width="75%"><strong>Total After Discount</strong></td><td align="right" border="1" width="25%" style="background-color:rgb(232,238,238);">$'.number_format($point_of_sell['total_price'] - $point_of_sell['discount'], 2).'</td></tr>';
		} else {
			$html .= '<tr><td align="right" width="75%"><strong>Sub Total</strong></td><td align="right" border="1" width="25%" style="background-color:rgb(232,238,238);">$'.number_format($point_of_sell['total_price'], 2).'</td></tr>';
		}
		if($point_of_sell['delivery'] != '' && $point_of_sell['delivery'] != 0) {
			$html .= '<tr><td align="right" width="75%"><strong>Delivery</strong></td><td align="right" border="1" width="25%" style="background-color:rgb(232,238,238);">$'.number_format($point_of_sell['delivery'],2).'</td></tr>';
		}
		if($point_of_sell['assembly'] != '' && $point_of_sell['assembly'] != 0) {
			$html .= '<tr><td align="right" width="75%"><strong>Assembly</strong></td><td align="right" border="1" width="25%" style="background-color:rgb(232,238,238);">$'.number_format($point_of_sell['assembly'],2).'</td></tr>';
		}

		if($pdf_tax != '') {
			$html .= $pdf_tax;
			//$html .= '<tr><td style="text-align:right;" width="75%"><strong>Tax</strong></td><td width="25%" style="text-align:right;">'.$pdf_tax.'</td></tr>';
		}

		$total_returned_amt = 0;
        if($returned_amt != 0) {
			$total_tax_rate = ($gst_rate/100) + ($pst_rate/100);
            $total_returned_amt = $returned_amt + ($returned_amt * $total_tax_rate);
            $html .= '<tr><td align="right" width="75%"><strong>Returned Total (Including Tax)</strong></td><td align="right" border="1" width="25%" style="background-color: rgb(232,238,238);">$'.$total_returned_amt.'</td></tr>';
		}


		$html .= '<tr><td align="right" width="75%"><strong>Total</strong></td><td align="right" border="1" width="25%" style="background-color: rgb(232,238,238);">$'.number_format($point_of_sell['final_price'] - $total_returned_amt, 2).'</td></tr>';
		if($point_of_sell['deposit_paid'] > 0) {
			$html .='<tr><td align="right" width="75%"><strong>Deposit Paid</strong></td><td align="right" border="1" width="25%" style="background-color: rgb(232,238,238);">$'.$point_of_sell['deposit_paid'].'</td></tr>';
			$html .='<tr><td align="right" width="75%"><strong>Updated Total</strong></td><td align="right" border="1" width="25%" style="background-color: rgb(232,238,238);">$'.$point_of_sell['updatedtotal'].'</td></tr>';
		}

		$html .= '</table><br><br>';


$html .= '<br />';

$html .= html_entity_decode($comment).'<br>';

if (!file_exists('download')) {
	mkdir('download', 0777, true);
}

//if(empty($posid) && !empty($invoiceid)) {
	$pdf->writeHTML($html, true, false, true, false, '');
    $invoice_type = $point_of_sell['type'];
    include('../Invoice/pos_invoice_append_ticket.php');
	$pdf->Output('download/invoice_'.$invoiceid.$edited.'.pdf', 'F');
/* } else {
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('../Point of Sale/download/invoice_'.$posid.$edited.'.pdf', 'F');
} */