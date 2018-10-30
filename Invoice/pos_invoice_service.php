<?php include_once ('../database_connection.php');
$point_of_sell = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM invoice WHERE invoiceid='$invoiceid'"));
if(empty($posid)) {
	$posid = $invoiceid;
}
$businessid		= $point_of_sell['businessid'];
$contactid		= $point_of_sell['patientid'];
$couponid		= $point_of_sell['couponid'];
$coupon_value	= $point_of_sell['coupon_value'];
$dep_total		= $point_of_sell['deposit_total'];
$updatedtotal	= $point_of_sell['updatedtotal'];
$ticket = $dbc->query("SELECT `ticket_label`, `assign_work` FROM `tickets` WHERE `ticketid`='{$point_of_sell['ticketid']}'")->fetch_assoc();
$business = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid='$businessid'"));
$customer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid='$contactid'"));

//Tax
$point_of_sell_product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(gst) AS total_gst, SUM(pst) AS total_pst FROM invoice_lines WHERE invoiceid='$invoiceid'"));
$get_pos_tax = get_config($dbc, 'pos_tax');
$pdf_tax = $pdf_tax_number = $client_tax_number = $gst_registrant = '';
$gst_amt = $gst_rate = $pst_amt = $pst_rate = 0;
if($get_pos_tax != '') {
	foreach(explode('*#*',$get_pos_tax) as $pos_tax) {
		$pos_tax_info = explode('**',$pos_tax);
		if(strtolower($pos_tax_info[0]) == 'gst') {
			$gst_amt = $point_of_sell['gst_amt'];
            $gst_rate = $pos_tax_info[1];
			$gst_registrant = $pos_tax_info[2];
		} else if (strtolower($pos_tax_info[0]) == 'pst') {
			$pst_amt = $point_of_sell['pst_amt'];
            $pst_rate = $pos_tax_info[1];
		}

		$pdf_tax_number .= $pos_tax_info[0].' ['.$pos_tax_info[2].'] <br>';

		if($pos_tax_info[3] == 'Yes' && $point_of_sell['client_tax_exemption'] == 'Yes') {
			$client_tax_number = $pos_tax_info[0].' ['.$tax_exemption_number.']';
		}
	}
}

// Invoice Logo
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
DEFINE('INVOICE_LOGO', $logo);
$invoice_footer = get_config($dbc, 'invoice_footer');
if(!empty($point_of_sell['type']) && !empty(get_config($dbc, 'invoice_footer_'.$point_of_sell['type']))) {
    $invoice_footer = get_config($dbc, 'invoice_footer_'.$point_of_sell['type']);
}
$payment_type = explode('#*#', $point_of_sell['payment_type']);

DEFINE('INVOICE_FOOTER', $invoice_footer);

class MYPDF extends TCPDF {
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

$bus_address = explode('<br>',get_address($dbc, $point_of_sell['businessid']));
$ship_address = explode(',',$point_of_sell['delivery_address']);
$ship_line_1 = $ship_address[0];
$ship_line_2 = '';
if(count($ship_address > 0)) {
	$ship_line_2 = $ship_address[count($ship_address) - 1].', '.$ship_line_2;
	unset($ship_address[count($ship_address) - 1]);
}
if(count($ship_address > 0)) {
	$ship_line_2 = $ship_address[count($ship_address) - 1].', '.$ship_line_2;
	unset($ship_address[count($ship_address) - 1]);
}
if(count($ship_address > 0)) {
	$ship_line_2 = $ship_address[count($ship_address) - 1].', '.$ship_line_2;
	unset($ship_address[count($ship_address) - 1]);
}
$ship_line_2 = str_replace($ship_line_1,'',trim($ship_line_2,', '));
if(count($ship_address > 0)) {
	$ship_line_1 = implode(',',$ship_address);
}

$html = '<h2 style="text-align:center;">Sales/Service Record</h2>';
$html .= '<table style="border: 2px solid black;" width="100%" cellspacing="0" cellpadding="0">
	<tr style="border:2px solid black;">
		<td>
			<table width="100%" cellpadding="3" cellspacing="0">
				<tr height="27px">
					<td rowspan="5" width="18%" style="border:1px solid black;">
						'.(INVOICE_LOGO != '' ? '<img src="'.INVOICE_LOGO.'" style="width:100px;">' : '').'
					</td>
					<td width="32%" colspan="3" style="border:1px solid black;">
						Invoice To '.get_contact($dbc, $point_of_sell['businessid']).'
					</td>
					<td width="32%" colspan="3" style="border:1px solid black;">
						Ship To
					</td>
					<td rowspan="2" width="18%" style="border:1px solid black;">
						Work Order #<br />
						<span style="font-size:1.5em;">XXXXX</span>
					</td>
				</tr>
				<tr height="27px">
					<td colspan="3" style="border:1px solid black;">
						'.$bus_address[0].'
					</td>
					<td colspan="3" style="border:1px solid black;">
						'.$ship_line_1.'
					</td>
				</tr>
				<tr height="27px">
					<td colspan="3" style="border:1px solid black;">
						'.$bus_address[1].'
					</td>
					<td colspan="3" style="border:1px solid black;">
						'.$ship_line_2.'
					</td>
					<td rowspan="2" style="border:1px solid black;">
						Terms<br />&nbsp;
					</td>
				</tr>
				<tr height="27px">
					<td colspan="3" style="border:1px solid black;">
						'.$bus_address[2].'
					</td>
					<td colspan="3" style="border:1px solid black;">
					</td>
				</tr>
				<tr height="50px">
					<td style="border:1px solid black; width:13%;">
						Date Order Taken<br />'.$point_of_sell['service_date'].'&nbsp;
					</td>
					<td style="border:1px solid black; width:13%;">
						Date Completed<br />'.$point_of_sell['invoice_date'].'&nbsp;
					</td>
					<td colspan="2" style="border:1px solid black; width:12%;">
						Ordered By<br />'.get_contact($dbc, $point_of_sell['patientid']).'&nbsp;
					</td>
					<td style="border:1px solid black; width:13%;">
						Telephone<br />'.get_contact_first_phone($dbc, $point_of_sell['patientid']).'&nbsp;
					</td>
					<td style="border:1px solid black; width:13%;">
						Shipped Via<br />'.$point_of_sell['delivery_type'].'&nbsp;
					</td>
					<td style="border:1px solid black;">
						Customer PO #<br />'.$ticket['ticket_label'].'&nbsp;
					</td>
				</tr>
			</table>
		</td>
	</tr>';
	$html .= '<tr style="border:2px solid black;">
		<td>
			<table width="100%" cellpadding="3" cellspacing="0">
				<tr height="27px">
					<td width="50%" style="border:1px solid black; font-size:1.5em;">
						<b>Service Requested/Performed</b>
					</td>
					<td width="50%" style="border:1px solid black;">
						Performed By '.get_contact($dbc, $point_of_sell['therapistsid']).'
					</td>
				</tr>
				<tr height="27px">
					<td colspan="2" style="border:1px solid black;">
						'.html_entity_decode($ticket['assign_work']).'
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr style="border:2px solid black;">
		<td>
			<table width="100%" cellpadding="3" cellspacing="0">
				<tr height="27px">
					<td style="border:1px solid black; text-align:center;" width="8%">
						Quantity Ordered
					</td>
					<td style="border:1px solid black; text-align:center;" width="8%">
						Part Number
					</td>
					<td colspan="4" style="border:1px solid black; text-align:center;" width="52%">
						Description
					</td>
					<td style="border:1px solid black; text-align:center;" width="8%">
						Back Ordered
					</td>
					<td style="border:1px solid black; text-align:center;" width="8%">
						Quantity Shipped
					</td>
					<td style="border:1px solid black; text-align:center;" width="8%">
						Unit Price
					</td>
					<td style="border:1px solid black; text-align:center;" width="8%">
						Extended Amount
					</td>
				</tr>';
				$pos_lines = $dbc->query("SELECT * FROM `invoice_lines` WHERE `invoiceid`='$invoiceid' AND `deleted`=0");
				while($pos_line = $pos_lines->fetch_assoc()) {
					$html .= '<tr height="27px">
						<td style="border:1px solid black;">
							'.number_format($pos_line['quantity'],2).'
						</td>
						<td style="border:1px solid black;">
							'.($pos_line['category'] == 'inventory' ? get_field_value('part_no','inventory','inventoryid',$pos_line['item_id']) : '').'
						</td>
						<td colspan="4" style="border:1px solid black;">
							'.$pos_line['description'].'
						</td>
						<td style="border:1px solid black;">
							0.00
						</td>
						<td style="border:1px solid black;">
							'.number_format($pos_line['quantity'],2).'
						</td>
						<td style="border:1px solid black;">
							$'.number_format($pos_line['unit_price'],2).'
						</td>
						<td style="border:1px solid black; text-align:right;">
							$'.number_format($pos_line['sub_total'],2).'
						</td>
					</tr>';
				}
				$html .= '<tr height="27px" style="border-top:4px solid black">
					<td colspan="3" style="border:1px solid black; font-size:1.25em;" width="43%">
						<b>TERMS OF SALE</b>
					</td>
					<td style="border:1px solid black; text-align:center;" width="9%">
						LABOUR
					</td>
					<td style="border:1px solid black; text-align:center;" width="8%">
						1
					</td>
					<td style="border:1px solid black; text-align:center;" width="8%">
						2
					</td>
					<td style="border:1px solid black; text-align:center;">
						3
					</td>
					<td style="border:1px solid black; text-align:center;">
						TOTAL
					</td>
					<td style="border:1px solid black; text-align:center;">
						RATE
					</td>
					<td style="border:1px solid black;">
					</td>
				</tr>
				<tr height="27px">
					<td colspan="3" rowspan="2" style="border:1px solid black;">
						Service Charge of 1.5% per month on overdue accounts.<br />
						No goods accepted for return without prior written authorization.<br />
						All returns are subject to a handling charge.<br />
						Goods purchase on special order cannot be returned for credit.
					</td>
					<td style="border:1px solid black; text-align:center;">
						REG.
					</td>
					<td style="border:1px solid black;">
					</td>
					<td style="border:1px solid black;">
					</td>
					<td style="border:1px solid black;">
					</td>
					<td style="border:1px solid black;">
					</td>
					<td style="border:1px solid black;">
					</td>
					<td style="border:1px solid black;">
					</td>
				</tr>
				<tr height="27px">
					<td style="border:1px solid black; text-align:center;">
						O.T.
					</td>
					<td style="border:1px solid black;">
					</td>
					<td style="border:1px solid black;">
					</td>
					<td style="border:1px solid black;">
					</td>
					<td style="border:1px solid black;">
					</td>
					<td style="border:1px solid black;">
					</td>
					<td style="border:1px solid black;">
					</td>
				</tr>
				<tr height="27px">
					<td colspan="3" rowspan="2" style="border:1px solid black;">
						I HEREBY ACKNOWLEDGE THE ABOVE WORK HAS BEEN COMPLETED TO MY SATISFACTION AND PAYMENT WILL BE ISSUED UPON RECEIPT OF INVOICE.
					</td>
					<td style="border:1px solid black; text-align:center;">
						MILEAGE
					</td>
					<td style="border:1px solid black;">
					</td>
					<td style="border:1px solid black;">
					</td>
					<td style="border:1px solid black;">
					</td>
					<td style="border:1px solid black;">
					</td>
					<td style="border:1px solid black;">
					</td>
					<td style="border:1px solid black;">
					</td>
				</tr>
				<tr height="27px">
					<td colspan="6" style="border:1px solid black; text-align:right;">
						Total Before Tax
					</td>
					<td style="border:1px solid black; text-align:right;">
						$'.number_format($point_of_sell['total_price'],2).'
					</td>
				</tr>
				<tr height="27px">
					<td colspan="4" rowspan="2" style="border:1px solid black;">
						SIGNED &amp; ACCEPTED BY:<br />&nbsp;
					</td>
					<td colspan="3" rowspan="2" style="border:1px solid black; text-align:center;">
						GST Registration<br />'.$gst_registrant.'
					</td>
					<td colspan="2" style="border:1px solid black; text-align:right;">
						GST
					</td>
					<td style="border:1px solid black; text-align:right;">
						$'.number_format($gst_amt,2).'
					</td>
				</tr>
				<tr height="27px">
					<td colspan="2" style="border:1px solid black; text-align:right;">
						Total Due This Order
					</td>
					<td style="border:1px solid black; text-align:right;">
						$'.number_format($point_of_sell['final_price'],2).'
					</td>
				</tr>
			</table>
		</td>
	</tr>';
$html .= '</table>';
$html .= '<br /><h4 style="text-align:center;"><b>Distribution White &amp; Canary</b> Accounting <b>Pink</b> Customer</h4>';
//echo $html;
if (!file_exists('download')) {
	mkdir('download', 0777, true);
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
$pdf->setFooterData(array(0,64,0), array(0,64,128));

$pdf->SetMargins(PDF_MARGIN_LEFT, 30, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$pdf->AddPage();
$pdf->SetFont('helvetica', '', 7);

$pdf->writeHTML($html, true, false, true, false, '');
$invoice_type = $point_of_sell['type'];
include('../Invoice/pos_invoice_append_ticket.php');
$pdf->Output('download/invoice_'.$invoiceid.'.pdf', 'F');