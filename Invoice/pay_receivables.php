<?php include_once('../include.php');
$purchaser_config = explode(',',get_config($dbc, 'invoice_purchase_contact'));
define('PURCHASER', count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0]);

if (isset($_POST['submit_patient'])) {
    include_once('../tcpdf/tcpdf.php');
    error_reporting(E_ALL);
    $payment_type = $_POST['payment_type'];
    if(empty($payment_type)) {
        exit("<script> alert('No payment type selected, no payment applied.'); </script>");
    }
    $paid_date = date('Y-m-d');
	$payment_receipt = $download_folder."download/ar_receipt_".preg_replace('/[^a-z]/','',strtolower($payment_type))."_".date('Y_m_d_H_i_s').".pdf";
	$patient_ids = [];
	$invoice = [];

    foreach (explode(',',implode(',',$_POST['invoicepatientid'])) as $id => $value) {
		$query_update_in = "UPDATE `invoice_patient` SET `paid` = '$payment_type', `paid_date` = '$paid_date', `receipt_file`=CONCAT(IFNULL(CONCAT(`receipt_file`,'#*#'),''),'$payment_receipt') WHERE `invoicepatientid` = '$value'";
		$result_update_in = mysqli_query($dbc, $query_update_in);
		$invoice_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT `invoice`.`patientid`, `invoice`.`invoice_date`, `invoice`.`invoiceid`, `invoice`.`therapistsid`, `invoice_patient`.`patient_price`, `invoice_patient`.`sub_total`, `invoice_patient`.`gst_amt` FROM `invoice_patient` LEFT JOIN `invoice` ON `invoice_patient`.`invoiceid`=`invoice`.`invoiceid` WHERE `invoice_patient`.`invoicepatientid`='$value'"));
		$query_update_in = "UPDATE `invoice` SET `patient_payment_receipt` = '1', `status`='Completed', `paid`='Yes', `payment_type`=REPLACE(`payment_type`,'On Account','$payment_type') WHERE `invoiceid` = '".$invoice_info['invoiceid']."'";
        $result_update_in = mysqli_query($dbc, $query_update_in);
		$patientid = $invoice_info['patientid'];
		$patient_ids[] = $invoice_info['patientid'];
		$therapist_info = '';
		if($invoice_info['therapistsid'] > 0) {
			$therapist_row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `first_name`, `last_name`, `credential`, `license` FROM `contacts` WHERE `contactid`='".$invoice_info['therapistsid']."'"));
			$therapist_info .= decryptIt($therapist_row['first_name']).' '.decryptIt($therapist_row['last_name']);
			$therapist_info .= ($therapist_row['credential'] != '' ? ': '.$therapist_row['credential'] : '');
			$therapist_info .= ($therapist_row['license'] != '' ? ';<br />'.$therapist_row['license'] : '');
		}
		$invoice[] = [$invoice_info['invoice_date'],$invoice_info['invoiceid'],$therapist_info,$invoice_info['patient_price'],$invoice_info['sub_total'],$invoice_info['gst_amt']];
    }

	$therapistsid = get_all_from_invoice($dbc, $invoiceid, 'therapistsid');
    $service_date = get_all_from_invoice($dbc, $invoiceid, 'service_date');

    $staff = get_contact($dbc, $therapistsid);


//
	$invoice_design = get_config($dbc, 'invoice_design');
    if(!empty($get_invoice['type']) && !empty(get_config($dbc, 'invoice_design_'.$get_invoice['type']))) {
        $invoice_design = get_config($dbc, 'invoice_design_'.$get_invoice['type']);

	$next_booking = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `booking` WHERE `appoint_date` > NOW() AND `deleted`=0 AND `patientid`='".$get_invoice['patientid']."' ORDER BY `appoint_date` ASC"));
	if($next_booking['bookingid'] > 0) {
		$footer_text = '<p style="color: #37C6F4; font-size: 14; font-weight: bold; text-align: center;">Your next appointment is '.date('d/m/y',strtotime($next_booking['appoint_date']))." at ".date('G:ia',strtotime($next_booking['appoint_date'])).'</p>';
	}
    $invoice_footer = get_config($dbc, 'invoice_footer');
    if(!empty($get_invoice['type']) && !empty(get_config($dbc, 'invoice_footer_'.$get_invoice['type']))) {
        $invoice_footer = get_config($dbc, 'invoice_footer_'.$get_invoice['type']);
    }
	$footer_text .= html_entity_decode($invoice_footer);
    $logo = get_config($dbc, 'invoice_logo');
    if(!empty($get_invoice['type']) && !empty(get_config($dbc, 'invoice_logo_'.$get_invoice['type']))) {
        $logo = get_config($dbc, 'invoice_logo_'.$get_invoice['type']);
    }
    $invoice_header = get_config($dbc, 'invoice_header');
    if(!empty($get_invoice['type']) && !empty(get_config($dbc, 'invoice_header_'.$get_invoice['type']))) {
        $invoice_header = get_config($dbc, 'invoice_header_'.$get_invoice['type']);
    }
    DEFINE('INVOICE_LOGO', $logo);
    DEFINE('INVOICE_HEADER', html_entity_decode($invoice_header));
    DEFINE('INVOICE_FOOTER', $footer_text);

    //Patient Invoice
	if(!class_exists('PATIENTPDF')) {
		class PATIENTPDF extends TCPDF {

			//Page header
			public function Header() {
				if(INVOICE_LOGO != '') {
					$image_file = 'download/'.INVOICE_LOGO;
					$this->Image($image_file, 10, 10, '', 25, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
				}
				$this->setCellHeightRatio(0.7);
				$this->SetFont('helvetica', '', 8);
				$footer_text = '<p style="text-align:right;">'.INVOICE_HEADER.'</p>';
				$this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);
			}

			// Page footer
			public function Footer() {
				// Position at 30 mm from bottom
				$this->SetY(-30);
				// Set font
				$this->SetFont('helvetica', 'I', 10);
				// Page number
				$footer_text = INVOICE_FOOTER;
				$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
			}
		}
	}

	$pdf = new PATIENTPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
    $pdf->setFooterData(array(0,64,0), array(0,64,128));

    $pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 9);

	$html = '<h2>Payment of Accounts Receivable</h2>
	<table style="border: none;" cellpadding="20"><tr><td style="color: #46A251; width: 20%;"><p>Payment Date:</p></td>
		<td style="width: 20%;"><p>'.$paid_date.'</p></td>
		<td style="color: #46A251; width: 30%;">'.PURCHASER.' Information:</td><td style="width: 30%;">';

	foreach(array_unique($patient_ids) as $contactid) {
		if($contactid == 0) {
			$non_patient = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `invoice_nonpatient` WHERE `invoiceid`='".$get_invoice['invoiceid']."'"));
			$html .= '<p>'.$non_patient['first_name'].' '.$non_patient['last_name'].'<br/>'.$non_patient['email'].'</p>';
		} else {
			$html .= '<p>'.get_contact($dbc, $contactid).'<br/>'.get_address($dbc, $contactid).'</p>';
		}
	}
	$html .= '</td></tr></table>';
	$html .= '<table style="border: none;" cellpadding="5"><tr style="background-color: #37C6F4; border: solid black 1px;"><td>Invoice Date</td><td>Invoice Number</td><td>Provider Name & Registration Information</td><td>Invoice Amount</td></tr>';
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
	switch($invoice_design) {
		case 1:
			include('pos_invoice_1.php');
			break;
		case 2:
			include('pos_invoice_2.php');
			break;
		case 3:
			include('pos_invoice_3.php');
			break;
		case 4:
			include ('patient_invoice_pdf.php');
			if($insurerid != '') {
				include ('insurer_invoice_pdf.php');
			}
			break;
		case 5:
            include('pos_invoice_small.php');
			break;
		case 'service':
            include('pos_invoice_service.php');
			break;
		case 'pink':
			include ('pos_receivables_pink.php');
			break;
		case 'cnt1':
			include ('pos_invoice_contractor_1.php');
			break;
		case 'cnt2':
			include ('pos_invoice_contractor_2.php');
			break;
		case 'cnt3':
			include ('pos_invoice_contractor_3.php');
			break;
        case 'custom_ticket':
            include ('pos_invoice_custom_ticket.php');
            break;
	}
//
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }

	$pdf->writeHTML(utf8_encode($html), true, false, true, false, '');
	$pdf->Output($payment_receipt, 'F');

    $query_update_invoice = "UPDATE `contacts` SET `amount_credit` = amount_credit + '$total_amt' WHERE `contactid` = '$patientid'";
    $result_update_invoice = mysqli_query($dbc, $query_update_invoice);

    $first_name = get_all_form_contact($dbc, $patientid, 'first_name');
    $table_name = strtolower($first_name[0]);

    $result_insert_vendor = mysqli_query($dbc, "UPDATE `contacts_fn_".$table_name."` SET `amount_credit` = amount_credit + '$total_amt' WHERE `contactid` = '$patientid'");

    $last_name = get_all_form_contact($dbc, $patientid, 'last_name');
    $table_name = strtolower($last_name[0]);

    $result_insert_vendor = mysqli_query($dbc, "UPDATE `contacts_ln_".$table_name."` SET `amount_credit` = amount_credit + '$total_amt' WHERE `contactid` = '$patientid'");

    echo '<script> window.open("'.$payment_receipt.'"); window.location.replace("../blank_loading_page.php"); </script>';
} ?>
<form class="form-horizontal col-sm-12" method="POST" action="">
    <h2>Selected Invoices<a href="../blank_loading_page.php" class="pull-right small"><img src="../img/icons/cancel.png" class="inline-img"></a></h2>
    <div class="clearfix"></div>
    <table class="table table-bordered">
        <tr>
            <th>Invoice#</th>
            <th>Service Date</th>
            <th>Invoice Date</th>
            <th><?= PURCHASER ?></th>
            <th>Amount Receivable</th>
        </tr>
        <?php $bill_amt = 0;
        $contactid = $_GET['customer'];
        $show_statement = $contactid > 0;
        foreach(explode(',',$_GET['invoices']) as $invoiceid) {
            if($invoiceid > 0) {
                $invoice = $dbc->query("SELECT `i`.`invoice_date`, `i`.`service_date`, `i`.`payment_type`, `i`.`patientid`, SUM(`ii`.`patient_price`) `patient_price`, GROUP_CONCAT(`ii`.`invoicepatientid`) `invoicepatientid` FROM invoice_patient ii, invoice i WHERE ii.invoiceid = i.invoiceid AND i.invoiceid='$invoiceid' AND (IFNULL(ii.`paid`,'') IN ('On Account','','No') OR ii.`paid` LIKE 'Net %')")->fetch_assoc();
                $payment_type = implode(', ',array_filter(array_unique(explode('#*#',$row_report['payment_type'])))); ?>
                <tr nobr="true">
                    <td>#<?= $invoiceid ?></td>
                    <td><?= $invoice['invoice_date'] ?></td>
                    <td><?= $invoice['service_date'] ?></td>
                    <td><?= get_contact($dbc, $invoice['patientid']) ?></td>
                    <td><?= $invoice['patient_price'] ?></td>
                    <input type="hidden" name="invoicepatientid[]" value="<?= $invoice['invoicepatientid'] ?>">
                </tr>
                <?php $bill_amt += $invoice['patient_price'];
                if($invoice['patientid'] > 0 && $invoice['patientid'] != $contactid && !($contactid > 0)) {
                    $contactid = $invoice['patientid'];
                    $show_statement = true;
                } else if($invoice['patientid'] > 0 && $contactid > 0 && $invoice['patientid'] != $contactid) {
                    $show_statement = false;
                }
            }
        } ?>
        <tr nobr="true">
            <td>Total</td><td></td><td></td><td></td><td><?= number_format($bill_amt, 2) ?></td>
        </tr>
    </table>
    <span class="pull-right">Pay By
      <select name="payment_type" data-placeholder="Select Payment Method..." class="chosen-select-deselect form-control"><option />
        <?php foreach(explode(',',get_config($dbc, 'invoice_payment_types')) as $available_pay_method) { ?>
            <option value='<?= $available_pay_method ?>'><?= $available_pay_method ?></option>
        <?php } ?>
      </select>
    </span>
    <div class="clearfix"></div>
    <button type="submit" name="submit_patient" value="pay_now" class="pull-right btn brand-btn">Complete Payment</button>
    <div class="clearfix"></div>
    <?php if($show_statement) {
        $_GET['edit'] = $contactid;
        $hide_filter_options = true;
        $field_option = 'Account Statement';
        include('../Contacts/edit_tile_data.php');
    } ?>
</form>