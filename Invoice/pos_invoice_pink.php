<?php
include_once ('../database_connection.php');
include_once ('../function.php');
$point_of_sell = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `invoice` WHERE `invoiceid`='$invoiceid'"));
if(empty($posid)) {
	$posid = $invoiceid;
}
$contactid		= $point_of_sell['patientid'];
$couponid		= $point_of_sell['couponid'];
$coupon_value	= $point_of_sell['coupon_value'];
$dep_total		= $point_of_sell['deposit_total'];
$updatedtotal	= $point_of_sell['updatedtotal'];

/* Tax */
$point_of_sell_product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(gst) AS `total_gst`, SUM(pst) AS `total_pst` FROM `invoice_lines` WHERE `invoiceid`='$invoiceid'"));

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
			$pdf_tax .= '
                <tr>
                    <td></td>
                    <td style="text-transform:uppercase;">'.$pos_tax_name_rate[0] .'['.$pos_tax_name_rate[1].'%]['.$pos_tax_name_rate[2].']</td>
                    <td align="right">$'. $taxrate_value .'</td>
                </tr>';
		}

		$pdf_tax_number .= $pos_tax_name_rate[0].' ['.$pos_tax_name_rate[2].'] <br>';

		if($pos_tax_name_rate[3] == 'Yes' && $point_of_sell['client_tax_exemption'] == 'Yes') {
			$client_tax_number = $pos_tax_name_rate[0].' ['.$tax_exemption_number.']';
		}
	}
}
//Tax

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
$invoice_header = get_config($dbc, 'invoice_header');
if(!empty($point_of_sell['type']) && !empty(get_config($dbc, 'invoice_header_'.$point_of_sell['type']))) {
    $invoice_header = get_config($dbc, 'invoice_header_'.$point_of_sell['type']);
}
$invoice_footer = get_config($dbc, 'invoice_footer');
if(!empty($point_of_sell['type']) && !empty(get_config($dbc, 'invoice_footer_'.$point_of_sell['type']))) {
    $invoice_footer = get_config($dbc, 'invoice_footer_'.$point_of_sell['type']);
}
$payment_type = explode('#*#', $point_of_sell['payment_type']);

DEFINE('INVOICE_HEADER', $invoice_header);
DEFINE('INVOICE_FOOTER', $invoice_footer);
DEFINE('INVOICEID', $posid);
DEFINE('INVOICE_DATE', $point_of_sell['invoice_date']);

class MYPDF extends TCPDF {
	/* Page Header */
	public function Header() {
		$image_file = POS_LOGO;
		if(file_get_contents($image_file)) {
			$image_file = $image_file;
		} else {
			$image_file = '../Point of Sale/'.$image_file;
		}
        /* if(file_get_contents($image_file)) {
			$this->Image($image_file, 0, 3, 51, '', '', '', 'T', false, 300, 'R', false, false, 0, false, false, false);
		} */

		$this->SetFont('helvetica', '', 9);

        $header_text = '
            <table>
                <tr>
                    <td width="50%">'.INVOICE_HEADER.'</td>
                    <td width="50%">'.(!empty($image_file) ? '<img src="'.$image_file.'" />' : '').'</td>
                </tr>
            </table>';

		$this->writeHTMLCell(0, 0, 15 , 10, $header_text, 0, 0, false, "L", true);
	}

    protected $last_page_flag = false;

    public function Close() {
        $this->last_page_flag = true;
        parent::Close();
    }

	/* Page Footer */
	public function Footer() {
		// Position from bottom
		$this->SetY(-27);
		$this->SetFont('helvetica', 'I', 8);

        if ($this->last_page_flag) {
            $footer_text = '
                <table>
                    <tr>
                        <td align="center">'.INVOICE_FOOTER.'</td>
                    </tr>
                </table>';
        }

		$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, 'C', true);
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

$html .= '
    <br /><br /><br /><br /><br />
    <p style="color:#df5a87; font-size:1.3em; margin-left:5px;">INVOICE</p><br /><br />
    <table border="0" style="border-bottom:1px solid #df5a87;">
        <tr>
            <td width="50%"><b>INVOICE TO</b><br />'.get_contact($dbc, $contactid).'</td>
            <td width="25%" align="right"><b>INVOICE #<br />DATE<br />DUE DATE<br />TERMS</b></td>
            <td width="25%">'.INVOICEID.'<br />'.INVOICE_DATE.'<br />'.INVOICE_DATE.'<br />Due on receipt</td>
        </tr>
        <tr>
            <td colspan="3"></td>
        </tr>
    </table>';

if($point_of_sell['invoice_date'] !== '') {
	$tdduedate = '<td>'.date('Y-m-d', strtotime($roww['invoice_date'] . "+30 days")).'</td>';
	$thduedate = '<td>Due Date</td>';
} else {
    $tdduedate = '';
    $thduedate = '';
}

/* START INVENTORY & MISC PRODUCTS */
$result = mysqli_query($dbc, "SELECT * FROM `invoice_lines` WHERE `invoiceid`='$invoiceid' AND `category`='inventory' AND `item_id` IS NOT NULL");
$result2 = mysqli_query($dbc, "SELECT * FROM `invoice_lines` WHERE `invoiceid`='$invoiceid' AND `category`='misc product'");
$return_result = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`returned_qty`) FROM `invoice_lines` WHERE `invoiceid`='$invoiceid'"))[0];
$returned_amt = 0;
$num_rows = mysqli_num_rows($result);
$num_rows2 = mysqli_num_rows($result2);
if($num_rows > 0 && $num_rows2 > 0) {
	$heading = 'Inventory & Misc Products';
} else if ($num_rows > 0 && $num_rows2 == 0) {
	$heading = 'Inventory';
} else if($num_rows == 0 && $num_rows2 > 0) {
	$heading = 'Misc Products';
}

if($num_rows > 0 || $num_rows2 > 0) {
	$html .= '
        <h2>'.$heading.'</h2>
		<table border="0" style="padding:3px;">
            <tr>
                <th style="background-color:#f9e7ee; color:#df5a87;">PART#</th>
                <th style="background-color:#f9e7ee; color:#df5a87;">PRODUCT</th>
                <th align="right" style="background-color:#f9e7ee; color:#df5a87;">QTY</th>
                '.($return_result > 0 ? '<th align="right" style="background-color:#f9e7ee; color:#df5a87;">RETURNED</th>' : '').'
                <th align="right" style="background-color:#f9e7ee; color:#df5a87;">RATE</th>
                <th align="right" style="background-color:#f9e7ee; color:#df5a87;">AMOUNT</th>
            </tr>';

            while ( $row=mysqli_fetch_array($result) ) {
                $inventoryid	= $row['item_id'];
                $price			= $row['unit_price'];
                $quantity		= $row['quantity'];
                $returned		= $row['returned_qty'];

                if ( $inventoryid != '' ) {
                    $amount = $price*($quantity-$returned);

                    $html .= '
                        <tr>
                            <td>'. get_inventory($dbc, $inventoryid, 'part_no') .'</td>
                            <td>'. ($amount < 0 ? 'Return: ' : '').get_inventory($dbc, $inventoryid, 'name') .'</td>
                            <td align="right">'. number_format($quantity,0) .'</td>
                            '.($return_result > 0 ? '<td align="right">'.$returned.'</td>' : '').'
                            <td align="right">$'. $price . '</td>
                            <td align="right">$'. number_format($amount, 2). '</td>
                        </tr>';
                }

                $returned_amt += $price * $returned;
            }

	$result = mysqli_query($dbc, "SELECT * FROM `invoice_lines` WHERE `invoiceid`='$invoiceid' AND `category`='misc product'");
	while ( $row=mysqli_fetch_array($result)) {
		$misc_product = $row['misc_product'];
		$price = $row['unit_price'];
		$qty = $row['quantity'];
		$returned = $row['returned_qty'];

		if($misc_product != '') {
			$html .= '
                <tr>
                    <td>Not Available</td>
                    <td>'. ($qty < 0 ? 'Return: ' : '').$misc_product .'</td>
                    <td align="right">'. number_format($qty,0) .'</td>
                    '.($return_result > 0 ? '<td align="right">'.$returned.'</td>' : '').'
                    <td align="right">$'. $price .'</td>
                    <td align="right">$'. $price * ($qty - $returned) .'</td>
                </tr>';
		}
	}
	$html .= '</table>';
}

/* START PRODUCTS */
$result = mysqli_query($dbc, "SELECT * FROM `invoice_lines` WHERE `invoiceid`='$invoiceid' AND `category`='package' AND `item_id` IS NOT NULL");
$num_rows3 = mysqli_num_rows($result);
if($num_rows3 > 0) {
	if($num_rows > 0 || $num_rows2 > 0) { $html .= '<br>'; }
	$html .= '
        <h2>Product(s)</h2>
		<table border="0" style="padding:3px;">
            <tr>
                <th style="background-color:#f9e7ee; color:#df5a87;">CATEGORY</th>
                <th style="background-color:#f9e7ee; color:#df5a87;">HEADING</th>
                <th align="right" style="background-color:#f9e7ee; color:#df5a87;">QTY</th>'.
                ($return_result > 0 ? '<th align="right" style="background-color:#f9e7ee; color:#df5a87;">RETURNED</th>' : '').'
                <th align="right" style="background-color:#f9e7ee; color:#df5a87;">RATE</th>
                <th align="right" style="background-color:#f9e7ee; color:#df5a87;">AMOUNT</th>
            </tr>';

            while ( $row=mysqli_fetch_array($result) ) {
                $inventoryid = $row['item_id'];
                $price = $row['unit_price'];
                $quantity = $row['quantity'];
                $returned = $row['returned_qty'];

                if($inventoryid != '') {
                    $amount = $price*($quantity-$returned);
                    $html .= '
                        <tr>
                            <td>'. get_products($dbc, $inventoryid, 'category') .'</td>
                            <td>'. ($amount < 0 ? 'Refund: ' : '').get_products($dbc, $inventoryid, 'heading') .'</td>
                            <td align="right">'. number_format($quantity,0) .'</td>'.
                            ($return_result > 0 ? '<td align="right">'.$returned.'</td>' : '').'
                            <td align="right">$'. $price .'</td>
                            <td align="right">$'. number_format($amount,2) .'</td>
                        </tr>';
                }
            }
            $html .= '</table>';
}

/* START SERVICES */
$result = mysqli_query($dbc, "SELECT * FROM `invoice_lines` WHERE `invoiceid`='$invoiceid' AND `category`='service' AND `item_id` IS NOT NULL");
$num_rows4 = mysqli_num_rows($result);
if($num_rows4 > 0) {
	if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0) { $html .= '<br>'; }
	$html .= '
        <h2>Service(s)</h2>
		<table border="0" style="padding:3px;">
            <tr>
                <th style="background-color:#f9e7ee; color:#df5a87;">ACTIVITY</th>
                <th align="right" style="background-color:#f9e7ee; color:#df5a87;">QTY</th>
                <th align="right" style="background-color:#f9e7ee; color:#df5a87;">RATE</th>
                <th align="right" style="background-color:#f9e7ee; color:#df5a87;">AMOUNT</th>
            </tr>';

            while ( $row=mysqli_fetch_array($result) ) {
                $inventoryid = $row['item_id'];
                $price = $row['unit_price'];
                $quantity = $row['quantity'];
                $returned = $row['returned_qty'];

                if($inventoryid != '') {
                    $amount = $price*($quantity-$returned);
                    $html .= '
                        <tr>
                            <td>'. ($amount < 0 ? 'Refund: ' : '').get_services($dbc, $inventoryid, 'heading') .'</td>
                            <td align="right">'. number_format($quantity,0) .'</td>
                            <td align="right">$'. $price .'</td>
                            <td align="right">$'. number_format($amount,2) .'</td>
                        </tr>';
                }
            }
            $html .= '</table>';
}

/* START VPL */
$result = mysqli_query($dbc, "SELECT * FROM `invoice_lines` WHERE `invoiceid`='$invoiceid' AND `category`='vpl' AND `item_id` IS NOT NULL");
$num_rows5 = mysqli_num_rows($result);
if($num_rows5 > 0) {
	if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0 || $num_rows4 > 0) { $html .= '<br>'; }

	$html .= '
        <h2>Vendor Price List Item(s)</h2>
		<table border="0" style="padding:3px;">
            <tr>
                <th style="background-color:#f9e7ee; color:#df5a87;">PART#</th>
                <th style="background-color:#f9e7ee; color:#df5a87;">PRODUCT</th>
                <th align="right" style="background-color:#f9e7ee; color:#df5a87;">QTY</th>'.
                ($return_result > 0 ? '<th align="right" style="background-color:#f9e7ee; color:#df5a87;">RETURNED</th>' : '').'
                <th align="right" style="background-color:#f9e7ee; color:#df5a87;">RATE</th>
                <th align="right" style="background-color:#f9e7ee; color:#df5a87;">AMOUNT</th>
            </tr>';

            while($row = mysqli_fetch_array( $result )) {
                $inventoryid = $row['item_id'];
                $price = $row['unit_price'];
                $quantity = $row['quantity'];
                $returned = $row['returned_qty'];

                if($inventoryid != '') {
                    $amount = $price*($quantity-$returned);
                    $html .= '
                        <tr>
                            <td>'. get_vpl($dbc, $inventoryid, 'part_no') .'</td>
                            <td>'. ($amount < 0 ? 'Return: ' : '').get_vpl($dbc, $inventoryid, 'name') .'</td>
                            <td align="right">'. number_format($quantity,0) .'</td>'.
                            ($return_result > 0 ? '<td align="right">'. $returned .'</td>' : '').'
                            <td align="right">$'. $price .'</td>
                            <td align="right">$'. number_format($amount,2) .'</td>
                        </tr>';
                }
            }
            $html .= '</table>';
}

/* START TIME SHEET */
$result = mysqli_query($dbc, "SELECT * FROM `invoice_lines` WHERE `invoiceid`='$invoiceid' AND `category`='time_cards' AND `item_id` IS NOT NULL");
$num_rows6 = mysqli_num_rows($result);
if($num_rows6 > 0) {
	if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0 || $num_rows4 > 0 || $num_rows5 > 0) { $html .= '<br>'; }

	$html .= '
        <h2>Time Sheets</h2>
		<table border="0" style="padding:3px;">
            <tr>
                <th style="background-color:#f9e7ee; color:#df5a87;">HEADING</th>
                <th align="right" style="background-color:#f9e7ee; color:#df5a87;">QTY</th>
                <th align="right" style="background-color:#f9e7ee; color:#df5a87;">RATE</th>
                <th align="right" style="background-color:#f9e7ee; color:#df5a87;">AMOUNT</th>
            </tr>';

            while($row = mysqli_fetch_array( $result )) {
                $amount = $row['sub_total'];
                $html .= '
                    <tr>
                        <td>'. ($amount < 0 ? 'Refund: ' : '').$row['heading'] .'</td>
                        <td align="right">'. number_format($row['quantity'],0) .'</td>
                        <td align="right">$'. $row['unit_price'] .'</td>
                        <td align="right">$'. number_format($amount, 2) .'</td>
                    </tr>';
            }
            $html .= '</table>';
}

$html .= '
    <br /><br />
    <table border="0" cellpadding="0" style="border-top:1px dotted #ccc;">
        <tr>
            <td></td>
        </tr>
    </table>
    <br />
    <table border="0">';
        if ( !empty($couponid) || $coupon_value!=0 ) {
            $html .= '
                <tr>
                    <td width="50%"></td>
                    <td width="25%">COUPON VALUE</td>
                    <td align="right" width="25%">$'. $point_of_sell['coupon_value'] .'</td>
                </tr>';
        }
        if($point_of_sell['discount'] != '' && $point_of_sell['discount'] != 0) {
            $html .= '
                <tr>
                    <td></td>
                    <td>TOTAL BEFORE DISCOUNT</td>
                    <td align="right">$'. $point_of_sell['total_price'] .'</td>
                </tr>
                <tr>
                    <td></td>
                    <td>DISCOUNT VALUE</td>
                    <td align="right">$'. $point_of_sell['discount'] .'</td>
                </tr>
                <tr>
                    <td></td>
                    <td>TOTAL AFTER DISCOUNT</td>
                    <td align="right">$'. number_format($point_of_sell['total_price'] - $point_of_sell['discount'], 2) .'</td>
                </tr>';
        } else {
            $html .= '
                <tr>
                    <td></td>
                    <td>SUB TOTAL</td>
                    <td align="right">$'. number_format($point_of_sell['total_price'], 2) .'</td>
                </tr>';
        }
        if($point_of_sell['delivery'] != '' && $point_of_sell['delivery'] != 0) {
            $html .= '
                <tr>
                    <td></td>
                    <td>DELIVERY</td>
                    <td align="right">$'. number_format($point_of_sell['delivery'], 2) .'</td>
                </tr>';
        }
        if($point_of_sell['assembly'] != '' && $point_of_sell['assembly'] != 0) {
            $html .= '
                <tr>
                    <td></td>
                    <td>ASSEMBLY</td>
                    <td align="right">$'. number_format($point_of_sell['assembly'], 2) .'</td>
                </tr>';
        }

        if($pdf_tax != '') {
            $html .= $pdf_tax;
        }

        $total_returned_amt = 0;
        if($returned_amt != 0) {
            $total_tax_rate = ($gst_rate/100) + ($pst_rate/100);
            $total_returned_amt = $returned_amt + ($returned_amt * $total_tax_rate);
            $html .= '
                <tr>
                    <td></td>
                    <td>RETURNED TOTAL (INCLUDING TAX)</td>
                    <td align="right">$'. $total_returned_amt .'</td>
                </tr>';
        }

        $html .= '
            <tr>
                <td></td>
                <td>TOTAL</td>
                <td align="right">$'.number_format($point_of_sell['final_price'] - $total_returned_amt, 2).'</td>
            </tr>
            <tr>
                <td></td>
                <td>BALANCE DUE</td>
                <td align="right" style="font-size:1.3em; font-weight:bold;">$'.number_format($point_of_sell['final_price'] - $total_returned_amt, 2).'</td>
            </tr>';

        if($point_of_sell['deposit_paid'] > 0) {
            $html .='
                <tr>
                    <td></td>
                    <td>DEPOSIT PAID</td>
                    <td align="right">$'. $point_of_sell['deposit_paid'] .'</td>
                </tr>
                <tr>
                    <td></td>
                    <td>UPDATED TOTAL</td>
                    <td align="right">$'. $point_of_sell['updatedtotal'] .'</td>
                </tr>';
        }

    $html .= '</table>';


$html .= '<br />';

if (!file_exists('download')) {
	mkdir('download', 0777, true);
}

$pdf->writeHTML($html, true, false, true, false, '');
$invoice_type = $point_of_sell['type'];
include('../Invoice/pos_invoice_append_ticket.php');
$pdf->Output('download/invoice_'.$invoiceid.'.pdf', 'F');