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

$customer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid='$contactid'"));

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

	    $footer_text = '<p style="text-align:right;">Payment Date : ' .date('Y-m-d').'<br>Payment Type : ' .$payment_type.'</p>';
		$this->writeHTMLCell(0, 0, 0 , 10, $footer_text, 0, 0, false, "R", true);
	}

	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom /* CHANGED (SetY used to be -25) */
		$this->SetY(-25);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		// Page number

	    $footer_text = '<table width="400px" style="border-bottom:1px solid black;text-align:left;font-style: normal !important;font-size:9"><tr><td style="text-align:left;font-style: normal !important;font-size:9">
	Signature</td></tr></table><br><br><br>'.INVOICE_FOOTER;

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


$html .= '
    <table>
        <tr>
            <td width="100%"><b>To: </b>';

            foreach(array_unique($patient_ids) as $contactid) {
                if($contactid == 0) {
                    $non_patient = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `invoice_nonpatient` WHERE `invoiceid`='".$get_invoice['invoiceid']."'"));
                    $html .= '<p>'.$non_patient['first_name'].' '.$non_patient['last_name'].'<br/>'.$non_patient['email'].'</p>';
                } else {
                    $html .= '<p>'.get_contact($dbc, $contactid).'<br/>'.get_address($dbc, $contactid).'<br/>'.get_contact_phone($dbc, $contactid).'<br/>'.get_email($dbc, $contactid).'</p>';
                }
            }

            $html .= '</td></tr>
    </table><br><br>';

///




$html .= '<table border="1px" style="padding:3px; border:1px solid grey;">
		<tr nobr="true" style="background-color:rgb(140,173,174); color:black; "><td>Salesperson</td><td>Payment Type</td></tr>
<tr><td>'.SALESPERSON.'</td><td>'.$payment_type.'</td></tr>
</table><br><br>
';


	$html .= '
		<table border="1px" style="padding:3px; border:1px solid black;">
		<tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">';

	$html .= '<th>Invoice Date</th><th>Invoice#</th><th>Invoice Amount</th></tr>';

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
		$html .= '<tr><td>'.$ar_line[0].'</td><td>'.$ar_line[1].'</td><td align="right">$'.number_format($ar_line[3],2).'</td></tr>';
		$total_amt += $ar_line[3];
		$sub_total += $ar_line[4];
		$tax_amt += $ar_line[5];
	}

	$html .= '</table>';


$html .= '
		<br><br>
		<table border="0" cellpadding="2">';

	    $html .= '<tr><td align="right" width="75%"><strong>Total Due By Customer</strong></td><td align="right" border="1" width="25%">$'.number_format($sub_total,2).'</td></tr>';

		if($pdf_tax != '') {
			$html .= $pdf_tax;
		}

		$html .= '<tr><td align="right" width="75%"><strong>Total Amount Owing</strong></td><td align="right" border="1" width="25%"">$'.number_format($total_amt,2).'</td></tr>';

		$html .= '<tr><td align="right" width="75%"><strong>Payment By</strong></td><td align="right" border="1" width="25%"">'.$payment_type.' (-$'.number_format($total_amt,2).')</td></tr>';

		$html .= '<tr><td align="right" width="75%"><strong>Balance</strong></td><td align="right" border="1" width="25%"">$0.00</td></tr>';

		$html .= '</table><br><br>';
