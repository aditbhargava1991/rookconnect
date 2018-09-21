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

$invoice_footer = get_config($dbc, 'invoice_footer');
if(!empty($point_of_sell['type']) && !empty(get_config($dbc, 'invoice_footer_'.$point_of_sell['type']))) {
    $invoice_footer = get_config($dbc, 'invoice_footer_'.$point_of_sell['type']);
}

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

class MYPDF extends TCPDF {

	//Page header
	public function Header() {
		// Logo
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

$html = '<br><br><br /><br /><center><div style="margin-top:10px; text-align:center;"><h1>PAYMENT OF ACCOUNTS RECEIVABLE</h1></div></center>';

$html .= '<div style="font-size:10px;">
	<table style="padding:3px; text-align:center;" border="1px" class="table table-bordered">
<tr style="padding:3px;  text-align:center" >
	<th colspan="3" style="background-color:grey; color:black;">Customer Information</th>
</tr>
<tr style="padding:3px;  text-align:center; background-color:white; color:black;" >
	<td>Customer Name</td>
	<td>Customer Phone</td>
	<td>Email</td>
</tr>
';

foreach(array_unique($patient_ids) as $contactid) {
    if($contactid == 0) {
        $non_patient = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `invoice_nonpatient` WHERE `invoiceid`='".$get_invoice['invoiceid']."'"));
        $html .= '<p>'.$non_patient['first_name'].' '.$non_patient['last_name'].'<br/>'.$non_patient['email'].'</p>';
    } else {
        $html .=  '<tr style="background-color:lightgrey; color:black;">
	<td>'.get_contact($dbc, $contactid).'</td>
	<td>'.get_contact_phone($dbc, $contactid).'</td>
	<td>'.get_email($dbc, $contactid).'</td>
    </tr>';
    }
}

$html .= '
</table>
<br><br>
';

	$html .= '
		<table border="1px" style="padding:3px; border:1px solid grey;">
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
		$html .= '<tr><td>'.$ar_line[0].'</td><td>'.$ar_line[1].'</td><td>$'.number_format($ar_line[3],2).'</td></tr>';
		$total_amt += $ar_line[3];
		$sub_total += $ar_line[4];
		$tax_amt += $ar_line[5];
	}

	$html .= '</table>';

///

$html .= '
<br><br><br>
<table style="padding:3px;" border="1px" class="table table-bordered">
<tr style="padding:3px;  text-align:center" >
	<th colspan="5" style="background-color:grey; color:black;">Payment Information</th>
</tr>
<tr style="padding:3px; text-align:center; background-color:white; color:black;" >
	<td>Total Due By Customer</td>
    <td>Tax</td>
    <td>Total Amount Owing</td>
    <td>Payment By</td>
    <td>Balance</td>
</tr>
<tr style="background-color:lightgrey; color:black;">
    <td>$'.number_format($sub_total,2).'</td>
    <td>';

    //Tax
    $get_pos_tax = get_config($dbc, 'invoice_tax');
    if($get_pos_tax != '') {
		$total_tax_rate = 0;
		foreach(explode('*#*',$get_pos_tax) as $pos_tax) {
			$total_tax_rate += explode('**',$pos_tax)[1];
		}
		foreach(explode('*#*',$get_pos_tax) as $pos_tax) {
			if($pos_tax != '') {
				$pos_tax_name_rate = explode('**',$pos_tax);
				$html .= $pos_tax_name_rate[0].' : $'.number_format($tax_amt * $pos_tax_name_rate[1] / $total_tax_rate,2).'<br>';
			}
		}
    }

$html .= '</td>
                <td>$'.number_format($total_amt,2).'</td>
                <td>'.$payment_type.' (-$'.number_format($total_amt,2).')</td>
                <td>$0.00</td>';

	$html .='
</tr>
</table><br /><br />';

    //Tax
    $get_pos_tax = get_config($dbc, 'invoice_tax');
    if($get_pos_tax != '') {
		$total_tax_rate = 0;
		foreach(explode('*#*',$get_pos_tax) as $pos_tax) {
			$total_tax_rate += explode('**',$pos_tax)[1];
		}
		foreach(explode('*#*',$get_pos_tax) as $pos_tax) {
			if($pos_tax != '') {
				$pos_tax_name_rate = explode('**',$pos_tax);
				$html .= $pos_tax_name_rate[0].'  ['.$pos_tax_name_rate[2].']';
			}
		}
    }

$html .= '<br><br>Created By: '.decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).'
<br><br><br>
<table width="400px" style="border-bottom:1px solid black;"><tr><td>
Signature</td></tr></table></div>
';
$html .= '<br />';