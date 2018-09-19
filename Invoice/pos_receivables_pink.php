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
//$payment_type = explode('#*#', $point_of_sell['payment_type']);

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
    <p style="color:#df5a87; font-size:1.3em; margin-left:5px;">PAYMENT OF ACCOUNTS RECEIVABLE</p><br /><br />
    <table border="0" style="border-bottom:1px solid #df5a87;">
        <tr>
            <td width="50%"><b>TO: </b>';

            foreach(array_unique($patient_ids) as $contactid) {
                if($contactid == 0) {
                    $non_patient = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `invoice_nonpatient` WHERE `invoiceid`='".$get_invoice['invoiceid']."'"));
                    $html .= '<p>'.$non_patient['first_name'].' '.$non_patient['last_name'].'<br/>'.$non_patient['email'].'</p>';
                } else {
                    $html .= '<p>'.get_contact($dbc, $contactid).'<br/>'.get_address($dbc, $contactid).'</p>';
                }
            }

            $html .= '</td><td width="50%"><b>PAYMENT DATE: </b>'.date('Y-m-d').'</td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
    </table><br><br>';

	$html .= '
		<table border="0" style="padding:3px;">
            <tr>
                <th style="background-color:#f9e7ee; color:#df5a87;">INVOICE DATE</th>
                <th style="background-color:#f9e7ee; color:#df5a87;">INVOICE#</th>
                <th align="right" style="background-color:#f9e7ee; color:#df5a87;">INVOICE AMOUNT</th>
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
		$html .= '<tr><td>'.$ar_line[0].'</td><td>'.$ar_line[1].'</td><td align="right">$'.number_format($ar_line[3],2).'</td></tr>';
		$total_amt += $ar_line[3];
		$sub_total += $ar_line[4];
		$tax_amt += $ar_line[5];
	}

	$html .= '</table>';

$html .= '
    <br /><br />
    <table border="0" cellpadding="0" style="border-top:1px dotted #ccc;">
        <tr>
            <td></td>
        </tr>
    </table>
    <br />
    <table border="0">';
        $html .= '
            <tr>
                <td></td>
                <td>TOTAL DUE BY CUSTOMER:</td>
                <td align="right">$'.number_format($sub_total,2).'</td>
            </tr>';

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
				$html .= '<tr><td></td><td>'.$pos_tax_name_rate[0].'  ['.$pos_tax_name_rate[2].']:</td><td  align="right">$'.number_format($tax_amt * $pos_tax_name_rate[1] / $total_tax_rate,2).'</td></tr>';
			}
		}
    }

        $html .= '
            <tr>
                <td></td>
                <td>TOTAL AMOUNT OWING:</td>
                <td align="right">$'.number_format($total_amt,2).'</td>
            </tr>';
        $html .= '
            <tr>
                <td></td>
                <td>PAYMENT BY:</td>
                <td align="right">'.$payment_type.' (-$'.number_format($total_amt,2).')</td>
            </tr>';
        $html .= '
            <tr>
                <td></td>
                <td>BALANCE:</td>
                <td align="right">$0.00</td>
            </tr>';

    $html .= '</table>';

$html .= '<br />';