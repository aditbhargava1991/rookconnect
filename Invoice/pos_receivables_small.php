<?php
/*
 * Invoice Format for Receipt Printers
 * Copied from pos_invoice_2.php
 */
include_once ('../database_connection.php');
include_once ('../function.php');

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
DEFINE('SALESPERSON', decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']));
DEFINE('PAYMENT_TYPE', $payment_type);

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

$html .= '<p style="text-align:center;"><img src="'.INVOICE_LOGO.'" width="100" /></p>';

$html = '<br><br><center><div style="margin-top:10px; text-align:center;"><h3>PAYMENT OF ACCOUNTS RECEIVABLE</h3></div></center>';

$html .= '<p style="text-align:center;">Payment Date: '. date('Y-m-d') .'<br />Payment Type: '. PAYMENT_TYPE .'</p>';
//$html .= '<br /><br /><br /><p style="text-align:center;">'. ( (!empty($customer['name'])) ? decryptIt($customer['name']) . '<br />' : '' ) . decryptIt($customer['first_name']) .' '. decryptIt($customer['last_name']) .'<br />'. ( (!empty($customer['mailing_address'])) ? $customer['mailing_address'] . '<br />' : '' ) . ( (!empty($customer['city'])) ? $customer['city'] . '<br />' : '' ) . ( (!empty($customer['postal_code'])) ? $customer['postal_code'] . '<br />' : '' ) . ( (!empty($customer['cell_phone'])) ? decryptIt($customer['cell_phone']) . '<br />' : '' ) . ( (!empty($customer['email_address'])) ? ecryptIt($customer['email_address']) : '' ) . '</p>';


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

$html .= '
		<br /><br />
		<table border="0" cellpadding="2">';

			$html .= '
                <tr>
                    <td style="text-align:right;" width="75%"><strong>Total Due By Customer</strong></td>
                    <td border="1" width="25%" style="text-align:right;">$'.number_format($sub_total,2).'</td>
                </tr>';

            if ( $pdf_tax != '' ) {
                $html .= $pdf_tax;
            }

			$html .= '
                <tr>
                    <td style="text-align:right;" width="75%"><strong>Total Amount Owing</strong></td>
                    <td border="1" width="25%" style="text-align:right;">$'.number_format($total_amt,2).'</td>
                </tr>';
			$html .= '
                <tr>
                    <td style="text-align:right;" width="75%"><strong>Payment By</strong></td>
                    <td border="1" width="25%" style="text-align:right;">'.$payment_type.' (-$'.number_format($total_amt,2).')</td>
                </tr>';
			$html .= '
                <tr>
                    <td style="text-align:right;" width="75%"><strong>Balance</strong></td>
                    <td border="1" width="25%" style="text-align:right;">$0.00</td>
                </tr>';

		$html .= '</table>';
