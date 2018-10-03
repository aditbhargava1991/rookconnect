<?php

include_once ('../database_connection.php');
include_once ('../function.php');
$point_of_sell = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `invoice` WHERE `invoiceid`='$invoiceid'"));
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
//$payment_type = explode('#*#', $point_of_sell['payment_type']);

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


$html = '<h2 style="text-align:center;">Payment Of Accounts Receivable</h2>';
$html .= '<table style="border: 2px solid black;" width="100%" cellspacing="0" cellpadding="0">
	<tr style="border:2px solid black;">
		<td>
			<table width="100%" cellpadding="3" cellspacing="0">
				<tr height="27px">
					<td width="18%" style="border:1px solid black;">
						'.(INVOICE_LOGO != '' ? '<img src="'.INVOICE_LOGO.'" style="width:100px;">' : '').'
					</td>
                    <td width="32%" style="border:1px solid black;">
                    To : ';
                    foreach(array_unique($patient_ids) as $contactid) {
                        if($contactid == 0) {
                            $non_patient = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `invoice_nonpatient` WHERE `invoiceid`='".$get_invoice['invoiceid']."'"));
                            $html .= '<p>'.$non_patient['first_name'].' '.$non_patient['last_name'].'<br/>'.$non_patient['email'].'</p>';
                        } else {
                            $html .= '<p>'.get_contact($dbc, $contactid).'<br/>'.get_address($dbc, $contactid).'<br/>'.get_contact_phone($dbc, $contactid).'<br/>'.get_email($dbc, $contactid).'</p>';
                        }
                    }
					$html .= '</td>
					<td width="32%" style="border:1px solid black;">
						Ship To : <br>'.$ship_line_1.'<br>'.$ship_line_2.'
					</td>
					<td width="18%" style="border:1px solid black;">
						Salesperson : <br />
						'.decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).'<br><br>Payment Type : <br />'.$payment_type.'
					</td>
				</tr>
			</table>
		</td>
	</tr>';
	$html .= '
	<tr style="border:2px solid black;">
		<td>
			<table width="100%" cellpadding="3" cellspacing="0">
				<tr height="27px">
					<td style="border:1px solid black; text-align:center;" width="20%">
						Invoice Date
					</td>
					<td style="border:1px solid black; text-align:center;" width="20%">
						Invoice#
					</td>
					<td colspan="2" style="border:1px solid black; text-align:center;" width="60%">
						Invoice Amount
					</td>
				</tr>';
                $total_amt = 0;
                $sub_total = 0;
                $tax_amt = 0;

                $ar_lines = [];
                foreach($invoice as $inv) {
                    if(!isset($ar_lines[$inv[1]])) {
                        $ar_lines[$inv[1]] = [$inv[0],$inv[1],$inv[2],0,0,0];
                    }
                    $ar_lines[$inv[1]][3] += $inv[3];
                    $ar_lines[$inv[1]][4] += $inv[4];
                    $ar_lines[$inv[1]][5] += $inv[5];
                }
                foreach($ar_lines as $ar_line) {
                    $html .= '<tr height="27px"><td style="border:1px solid black;">'.$ar_line[0].'</td><td style="border:1px solid black;">'.$ar_line[1].'</td><td colspan="2" style="border:1px solid black; text-align:right;">$'.number_format($ar_line[3],2).'</td></tr>';
                    $total_amt += $ar_line[3];
                    $sub_total += $ar_line[4];
                    $tax_amt += $ar_line[5];
                }

				$html .= '
				<tr height="27px">
					<td colspan="3" style="border:1px solid black; text-align:right;">
						Total Due By Customer
					</td>
					<td style="border:1px solid black; text-align:right;">
						$'.number_format($sub_total,2).'
					</td>
				</tr>

				<tr height="27px">
					<td colspan="3" style="border:1px solid black; text-align:right;">
						GST
					</td>
					<td style="border:1px solid black; text-align:right;">
						$'.number_format((number_format($total_amt,2) - number_format($sub_total,2)),2).'
					</td>
				</tr>

    			<tr height="27px">
					<td colspan="3" style="border:1px solid black; text-align:right;">
						Total Amount Owing
					</td>
					<td style="border:1px solid black; text-align:right;">
						$'.number_format($total_amt,2).'
					</td>
				</tr>

				<tr height="27px">
					<td colspan="3" style="border:1px solid black; text-align:right;">
						Payment By
					</td>
					<td style="border:1px solid black; text-align:right;">
						'.$payment_type.' (-$'.number_format($total_amt,2).')
					</td>
				</tr>
				<tr height="27px">
					<td colspan="3" style="border:1px solid black; text-align:right;">
						Balance
					</td>
					<td style="border:1px solid black; text-align:right;">
						$0.00
					</td>
				</tr>

				<tr height="27px">
					<td colspan="2" rowspan="2" style="border:1px solid black;">
						SIGNED &amp; ACCEPTED BY:<br />&nbsp;
					</td>
					<td colspan="2" rowspan="2" style="border:1px solid black; text-align:center;">
						GST Registration<br />'.$gst_registrant.'
					</td>
				</tr>

			</table>
		</td>
	</tr>';
$html .= '</table>';

$html .= '<br /><h4 style="text-align:center;"><b>Distribution White &amp; Canary</b> Accounting <b>Pink</b> Customer</h4>';
//echo $html;

