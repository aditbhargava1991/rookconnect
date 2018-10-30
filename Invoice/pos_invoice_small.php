<?php
/*
 * Invoice Format for Receipt Printers
 * Copied from pos_invoice_2.php
 */
include_once ('../database_connection.php');

$get_invoice = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM invoice WHERE invoiceid='$invoiceid'"));
$contactid		= $get_invoice['patientid'];
$couponid		= $get_invoice['couponid'];
$coupon_value	= $get_invoice['coupon_value'];

if ( $edit_id == '0' ) {
	$edited = '';
} else {
	$edited = '_' . $edit_id;
}

$customer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT name, first_name, last_name, home_phone, cell_phone, email_address, business_address, mailing_address, city, state, country, zip_code, postal_code FROM contacts WHERE contactid='$contactid'"));

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
			$taxrate_value = $invoice_lines['total_gst'];
		}
		if (strcasecmp($pos_tax_name_rate[0], 'pst') == 0) {
			$taxrate_value = $invoice_lines['total_pst'];
		}

		if($pos_tax_name_rate[3] == 'Yes' && $get_invoice['client_tax_exemption'] == 'Yes') {

		} else {
			//$pdf_tax .= $pos_tax_name_rate[0] .' : '.$pos_tax_name_rate[1].'% : $'.$taxrate_value.'<br>';
			$pdf_tax .= '<tr><td style="text-align:right;" width="75%"><strong>'.$pos_tax_name_rate[0] .'['.$pos_tax_name_rate[1].'%]['.$pos_tax_name_rate[2].']</strong></td><td border="1" width="25%" style="text-align:right;">$'.number_format($taxrate_value,2).'</td></tr>';
		}

		$pdf_tax_number .= $pos_tax_name_rate[0].' ['.$pos_tax_name_rate[2].'] <br>';

		if($pos_tax_name_rate[3] == 'Yes' && $get_invoice['client_tax_exemption'] == 'Yes') {
			$client_tax_number = $pos_tax_name_rate[0].' ['.$tax_exemption_number.']';
		}
	}
}
//Tax

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
DEFINE('INVOICE_LOGO', $logo);
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
	public function Header() {}
    protected $last_page_flag = false;
    public function Close() {
        $this->last_page_flag = true;
        parent::Close();
    }


	// Page footer
	public function Footer() {
        $this->SetY(-10);
		$this->SetFont('helvetica', 'I', 7);
        if ($this->last_page_flag) {
            $footer_text = INVOICE_FOOTER;
        } else {
            $footer_text = '';
        }
        $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, 0, false, "C", true);
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A7', true, 'UTF-8', false);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
$pdf->setFooterData(array(0,64,0), array(0,64,128));

//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetMargins(5, 5, 5);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

$pdf->SetAutoPageBreak(TRUE, 0);

$pdf->AddPage();
$pdf->SetFont('helvetica', '', 8);

$html = '';

$html .= '<p style="text-align:center;"><img src="'.INVOICE_LOGO.'" width="100" /><br /><br />Invoice #: '. INVOICEID .'<br />Date: '. INVOICE_DATE .'<br />Payment Type: '. PAYMENT_TYPE .'</p>';
//$html .= '<br /><br /><br /><p style="text-align:center;">'. ( (!empty($customer['name'])) ? decryptIt($customer['name']) . '<br />' : '' ) . decryptIt($customer['first_name']) .' '. decryptIt($customer['last_name']) .'<br />'. ( (!empty($customer['mailing_address'])) ? $customer['mailing_address'] . '<br />' : '' ) . ( (!empty($customer['city'])) ? $customer['city'] . '<br />' : '' ) . ( (!empty($customer['postal_code'])) ? $customer['postal_code'] . '<br />' : '' ) . ( (!empty($customer['cell_phone'])) ? decryptIt($customer['cell_phone']) . '<br />' : '' ) . ( (!empty($customer['email_address'])) ? ecryptIt($customer['email_address']) : '' ) . '</p>';

if($client_tax_number != '') {
	$html .= '<br />Tax Exemption Number: '. $get_invoice['tax_exemption_number'];
}


//START INVENTORY & MISC PRODUCTS
$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'inventory' AND item_id IS NOT NULL");
$result2 = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'misc product'");
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
	$html .= '<h3 style="border-bottom:1px solid #000;">'. $titler .'</h3><table border="0" cellpadding="2">';
        while ( $row=mysqli_fetch_array($result) ) {
            $inventoryid = $row['item_id'];
            $price       = $row['unit_price'];
            $quantity    = $row['quantity'];
            $returned	 = $row['returned_qty'];

            if ( $inventoryid != '' ) {
                $amount = $price * ($quantity-$returned);

                $html .= '<tr>';
                    $html .= '<td>';
                        $html .= get_inventory($dbc, $inventoryid, 'name') .'<br />';
                        $html .= number_format($quantity, 0) .' @ $'. number_format($price, 2);
                    $html .= '</td>';
                    if($return_result > 0) {
                        $html .= '<td>'. $returned .'</td>';
                    }
                    $html .= '<td style="text-align:right;">$'. number_format($amount, 2) .'</td>';
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

            if ( $misc_product != '' ) {
                $html .= '<tr>';
                    $html .= '<td>';
                        $html .= ($quantity < 0 ? 'Return: ' : '').$misc_product .'<br />';
                        $html .= number_format($quantity, 0) .' @ $' . number_format($price, 2);
                    $html .= '</td>';
                    if($return_result > 0) {
                        $html .= '<td>'. $returned .'</td>';
                    }
                    $html .= '<td style="text-align:right;">$'. number_format($amount,2) .'</td>';
                $html .= '</tr>';
            }
        }
    $html .= '</table>';
}
// END INVENTORY AND MISC PRODUCTS

// START PACKAGES
$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category='package' AND item_id IS NOT NULL");
$num_rows3 = mysqli_num_rows($result);
if ( $num_rows3 > 0 ) {
	if ( $num_rows > 0 || $num_rows2 > 0) {
        $html .= '<br />';
    }
	$html .= '<h3>Package(s)</h3><table border="0" cellpadding="2">';
        while ( $row=mysqli_fetch_array($result) ) {
            $inventoryid = $row['item_id'];
            $price       = $row['unit_price'];
            $quantity    = $row['quantity'];
            $returned    = $row['returned_qty'];

            if ( $inventoryid != '' ) {
                $amount = $row['sub_total'];
                $html .= '<tr>';
                    $html .= '<td>';
                        $html .= ($quantity < 0 ? 'Refund: ' : '').get_package($dbc, $inventoryid, 'heading') .'<br />';
                        $html .= number_format($quantity, 0) .' @ $' . number_format($price, 2);
                    $html .= '</td>';
                    if($return_result > 0) {
                        $html .= '<td>'. $returned .'</td>';
                    }
                    $html .= '<td style="text-align:right;">$'. number_format($amount, 2) .'</td>';
                $html .= '</tr>';
            }
        }
	$html .= '</table>';
}
// END PACKAGES

// START PRODUCTS
$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'product' AND item_id IS NOT NULL");
$num_rows3 = mysqli_num_rows($result);
if($num_rows3 > 0) {
	if($num_rows > 0 || $num_rows2 > 0) { $html .= '<br>'; }

    $html .= '<h3>Product(s)</h3><table border="0" cellpadding="2">';
        while ( $row=mysqli_fetch_array($result) ) {
            $inventoryid = $row['item_id'];
            $price       = $row['unit_price'];
            $quantity    = $row['quantity'];
            $returned    = $row['returned_qty'];

            if($inventoryid != '') {
                $amount = $row['sub_total'];
                $html .= '<tr>';
                    $html .= '<td>';
                        $html .= ($quantity < 0 ? 'Refund: ' : '').get_products($dbc, $inventoryid, 'heading') .'<br />';
                        $html .= number_format($quantity, 0) .' @ $'. number_format($price, 2);
                    $html .= '</td>';
                    if($return_result > 0) {
                        $html .= '<td>'. $returned .'</td>';
                    }
                    $html .= '<td style="text-align:right;">$'. number_format($amount, 2) .'</td>';
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

    $html .= '<h3>Service(s)</h3><table border="0" cellpadding="2">';
        while ( $row=mysqli_fetch_array($result) ) {
            $inventoryid = $row['item_id'];
            $price       = $row['unit_price'];
            $quantity    = $row['quantity'];
            $returned    = $row['returned_qty'];

            if ( $inventoryid != '' ) {
                $amount = $row['sub_total'];
                $html .= '<tr>';
                    $html .= '<td>';
                        $html .= ($quantity < 0 ? 'Refund: ' : '').get_services($dbc, $inventoryid, 'heading') .'<br />';
                        $html .= number_format($quantity, 0) .' @ $'. number_format($price, 2);
                    $html .= '</td>';
                    if($return_result > 0) {
                        $html .= '<td>'.$returned.'</td>';
                    }
                    $html .= '<td style="text-align:right;">$'. number_format($amount, 2) .'</td>';
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
	if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0 || $num_rows4 > 0) { $html .= '<br />'; }

	$html .= '<h3>Vendor Price List Item(s)</h3><table border="0" cellpadding="2">';
        while ( $row=mysqli_fetch_array($result) ) {
            $inventoryid = $row['item_id'];
            $price       = $row['unit_price'];
            $quantity    = $row['quantity'];
            $returned    = $row['returned_qty'];

            if ( $inventoryid != '' ) {
                $amount = $row['sub_total'];

                $html .= '<tr>';
                    $html .= '<td>';
                        $html .= ($quantity < 0 ? 'Return: ' : '').get_vpl($dbc, $inventoryid, 'name') .'<br />';
                        $html .= number_format($quantity, 0) .' @ $'. number_format($price, 2);
                    $html .= '</td>';
                    if($return_result > 0) {
                        $html .= '<td>'. $returned .'</td>';
                    }
                    $html .= '<td style="text-align:right;">$'. number_format($amount, 2) .'</td>';
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

    $html .= '<h3>Time Sheets</h3><table border="0" cellpadding="2">';

    while($row = mysqli_fetch_array( $result )) {
        $amount = $row['sub_total'];

        $html .= '<tr>';
            $html .= '<td>';
                $html .= $row['heading'].'<br />';
                $html .= number_format($row['quantity'], 2).' @ $'.number_format($row['unit_price'], 2);
            $html .= '</td>';
            $html .= '<td style="text-align:right;">$'. number_format($amount, 2) .'</td>';
        $html .= '</tr>';
    }
    $html .= '</table>';
}
// START TIME SHEET

$html .= '
		<br /><br />
		<table border="0" cellpadding="2">';
            if ( $get_invoice['discount'] != 0 ) {
                $html .= '
                    <tr>
                        <td style="text-align:right;" width="75%"><strong>Discount</strong></td>
                        <td border="1" width="25%" style="text-align:right;">$'. $get_invoice['discount'] .'</td>
                    </tr>';
            }
			$html .= '
                <tr>
                    <td style="text-align:right;" width="75%"><strong>Sub Total</strong></td>
                    <td border="1" width="25%" style="text-align:right;">$'. number_format($get_invoice['total_price'], 2) .'</td>
                </tr>';
            if ( $get_invoice['delivery'] != 0 ) {
                $html .= '
                    <tr>
                        <td style="text-align:right;" width="75%"><strong>Delivery</strong></td>
                        <td border="1" width="25%" style="text-align:right;">$'. $get_invoice['delivery'] .'</td>
                    </tr>';
            }
            if ( $get_invoice['assembly'] != 0 ) {
                $html .= '
                    <tr>
                        <td style="text-align:right;" width="75%"><strong>Assembly</strong></td>
                        <td border="1" width="25%" style="text-align:right;">$'. $get_invoice['assembly'] .'</td>
                    </tr>';
            }
            if ( $pdf_tax != '' ) {
                $html .= $pdf_tax;
            }
            $html .= '
                <tr>
                    <td style="text-align:right;" width="75%"><strong>Total</strong></td>
                    <td border="1" width="25%" style="text-align:right;">$'. number_format($get_invoice['final_price'],2) .'</td>
                </tr>';
            if ( $get_invoice['deposit_paid'] > 0 ) {
                $html .='
                    <tr>
                        <td style="text-align:right;" width="75%"><strong>Deposit Paid</strong></td>
                        <td border="1" width="25%" style="text-align:right;">$'. $get_invoice['deposit_paid'] .'</td>
                    </tr>';
                $html .='
                    <tr>
                        <td style="text-align:right;" width="75%"><strong>Updated Total</strong></td>
                        <td border="1" width="25%" style="text-align:right;">$'. $get_invoice['updatedtotal'] .'</td>
                    </tr>';
            }
		$html .= '</table>';


$html .= '<br /><br /><br />';

$html .= $comment . '<br />';

if($get_invoice['delivery_type'] == 'Pick-Up' || $get_invoice['delivery_type'] == 'Company Delivery') {
	//$html .= '<br><br><br><br><br><br><br><br><br><br><br><br><br>';
	//$html .= '<table width="400px" style="border-bottom:1px solid black;"><tr><td>
	//Signature</td></tr></table></div>
	//';
}

if ( !file_exists('download') ) {
	mkdir('download', 0777, true);
}

$pdf->writeHTML($html, true, false, true, false, '');
$invoice_type = $get_invoice['type'];
include('../Invoice/pos_invoice_append_ticket.php');
$pdf->Output('download/invoice_'.$invoiceid.'.pdf', 'F');