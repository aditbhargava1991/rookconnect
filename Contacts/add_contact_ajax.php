<?php include('../include.php');
include_once('../tcpdf/tcpdf.php');
checkAuthorised('contacts_inbox');
error_reporting(0);
ob_clean();

if($_GET['fill'] == 'statement') {
	$contact = $_POST['contact'];
	$injury = $_POST['injury'];
	$from = $_POST['from'];
	$to = $_POST['to'];
	$options = explode(',',$_POST['option_list']);
	$balance = 0;
	
	$sql = "SELECT IF(`inv_status`.`invoiceid` IS NULL, 'OUTSTANDING', 'PAID') INV_TYPE, IF(`invoice`.`invoice_type`='Saved','Saved',`invoice`.`invoice_date`) tran_date, `invoice`.`therapistsid` staffid, `invoice`.`patientid` contactid, `invoice`.`injuryid`, `invoice`.`serviceid`, `invoice`.`inventoryid`, `invoice`.`quantity`, `invoice`.`packageid`, '' insurer, '' ins_payment, '' cust_pay, 0-IFNULL(`invoice`.`final_price`,0) amt FROM `invoice` LEFT JOIN (SELECT `invoiceid` FROM `invoice_insurer` WHERE `paid` IN ('Yes') UNION SELECT `invoiceid` FROM `invoice_patient` WHERE (`paid` NOT IN ('No','On Account','') AND `paid` IS NOT NULL AND `paid` NOT LIKE 'Net %')) `inv_status` ON `invoice`.`invoiceid`=`inv_status`.`invoiceid` WHERE `invoice`.`deleted`=0 GROUP BY `invoice`.`invoiceid` UNION SELECT 'CUST_PAY', `paid_date`, 0, `patientid` contactid, 0, '', '', '', '', '', '', `paid`, SUM(IFNULL(`patient_price`,0)) FROM `invoice_patient` WHERE `paid` NOT IN ('No','On Account','') AND `paid` IS NOT NULL AND `paid` NOT LIKE 'Net %' GROUP BY `paid`, `paid_date`, `patientid` UNION SELECT 'INS_PAY', `invoice_insurer`.`paid_date`, 0, `invoice`.`patientid` contactid, 0, '', '', '', '', `invoice_insurer`.`insurerid`, `invoice_insurer`.`paid`, '', SUM(IFNULL(`invoice_insurer`.`insurer_price`,0)) FROM `invoice_insurer` LEFT JOIN `invoice` ON `invoice_insurer`.`invoiceid`=`invoice`.`invoiceid` WHERE `invoice_insurer`.`paid` IN ('Yes') GROUP BY `invoice_insurer`.`paid`, `invoice_insurer`.`paid_date`, `invoice_insurer`.`insurerid`";
	
	$statement = mysqli_query($dbc, "SELECT * FROM (".$sql.") statement WHERE `contactid`='$contact' AND '$contact' != '' ORDER BY `tran_date`");
	$cust_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts`.`contactid`, `contacts`.`category`, SUM(IF(`statement`.`tran_date`!='Saved',amt,0)) credit_change, `contacts`.`first_name`, `contacts`.`last_name`, IFNULL(`contacts`.`amount_credit`,0) credit, IFNULL(`contacts`.`amount_owing`,0) debit FROM (".$sql.") statement LEFT JOIN `contacts` ON `statement`.`contactid`=`contacts`.`contactid` WHERE `contacts`.`contactid`='$contact'"));
	$end_balance = $cust_info['credit'] - $cust_info['debit'];
	$balance = $end_balance - $cust_info['credit_change'];
	$customer = decryptIt($cust_info['first_name']).' '.decryptIt($cust_info['last_name']);
	echo '<tr>';
		echo '<td data-title="" colspan="'.($cust_info['category'] == 'Patient' ? 7 : 6).'">Opening Balance</td>';
		echo '<td data-title="Opening Balance">$'.number_format($balance,2).'</td>';
	echo '</tr>';
	while($line = mysqli_fetch_array($statement)) {
		$balance += ($line['tran_date'] != 'Saved' ? $line['amt'] : 0);
		if((in_array('saved', $options) || $line['tran_date'] != 'Saved')
			&& (in_array('payments', $options) || $line['INV_TYPE'] != 'CUST_PAY')
			&& (in_array('insurer', $options) || $line['INV_TYPE'] != 'INS_PAY')
			&& (in_array('paid', $options) || $line['INV_TYPE'] != 'PAID')
			&& (in_array('outstanding', $options) || $line['INV_TYPE'] != 'OUTSTANDING')) {
			$injury = '';
			if($line['injuryid'] > 0) {
				$injury = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `patient_injury` WHERE `injuryid`='".$line['injuryid']."'"));
				$injury = $injury['injury_type'].': '.$injury['injury_name'].' ('.$injury['injury_date'].')';
			}
			echo '<tr>';
				echo '<td data-title="Transaction Date">'.$line['tran_date'].'</td>';
				echo '<td data-title="Staff">'.($line['staffid'] > 0 ? get_contact($dbc, $line['staffid']) : '').'</td>';
				echo $cust_info['category'] == 'Patient' ? '<td data-title="Injury">'.$injury.'</td>' : '';
				echo '<td data-title="Services">';
				foreach(explode(',',$line['serviceid']) as $service) {
					if($service > 0) {
						$service = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `services` WHERE `serviceid`='$service'"));
						echo ($service['category'] != '' ? $service['category'].': ' : '').$service['heading'].'<br />';
					}
				}
				$inv_qty = explode(',',$line['quantity']);
				foreach(explode(',',$line['inventoryid']) as $i => $inventory) {
					if($inventory > 0 && $inv_qty[$i] > 0) {
						$inventory = mysqli_fetch_array(mysqli_query($dbc, "SELECT `name` FROM `inventory` WHERE `inventoryid`='$inventory'"));
						echo $inv_qty[$i].' X '.$inventory['name'].'<br />';
					}
				}
				foreach(explode(',',$line['packageid']) as $package) {
					if($package > 0) {
						$package = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `package` WHERE `packageid`='$package'"));
						echo ($package['category'] != '' ? $package['category'].': ' : '').$package['heading'].'<br />';
					}
				}
				echo '</td>';
				echo '<td data-title="'.$cust_info['category'].'">'.$customer.'</td>';
				echo '<td data-title="Payment Type">'.($line['insurer'] > 0 ? get_client($dbc, $line['insurer']) : $line['cust_pay']).'</td>';
				echo '<td data-title="Payment">$'.number_format($line['amt'],2).'</td>';
				echo '<td data-title="Balance">$'.($line['tran_date'] != 'Saved' ? number_format($balance,2) : 'N/A').'</td>';
			echo '</tr>';
		}
	}
	echo '<tr>';
		echo '<td data-title="" colspan="'.($cust_info['category'] == 'Patient' ? 7 : 6).'">Closing Balance</td>';
		echo '<td data-title="Closing Balance">$'.number_format($end_balance,2).'</td>';
	echo '</tr>';
} else if($_GET['fill'] == 'statement_pdf') {
	$contact = $_POST['contact'];
	$injury = $_POST['injury'];
	$from = $_POST['from'];
	$to = $_POST['to'];
	$options = explode(',',$_POST['option_list']);
	$balance = 0;
	
	$sql = "SELECT IF(`inv_status`.`invoiceid` IS NULL, 'OUTSTANDING', 'PAID') INV_TYPE, IF(`invoice`.`invoice_type`='Saved','Saved',`invoice`.`invoice_date`) tran_date, `invoice`.`therapistsid` staffid, `invoice`.`patientid` contactid, `invoice`.`injuryid`, `invoice`.`serviceid`, `invoice`.`inventoryid`, `invoice`.`quantity`, `invoice`.`packageid`, '' insurer, '' ins_payment, '' cust_pay, 0-IFNULL(`invoice`.`final_price`,0) amt FROM `invoice` LEFT JOIN (SELECT `invoiceid` FROM `invoice_insurer` WHERE `paid` IN ('Yes') UNION SELECT `invoiceid` FROM `invoice_patient` WHERE `paid` NOT IN ('No','On Account','') AND `paid` IS NOT NULL AND `paid` NOT LIKE 'Net %') `inv_status` ON `invoice`.`invoiceid`=`inv_status`.`invoiceid` WHERE `invoice`.`deleted`=0 GROUP BY `invoice`.`invoiceid` UNION SELECT 'CUST_PAY', `paid_date`, 0, `patientid` contactid, 0, '', '', '', '', '', '', CONCAT('Patient: ',`paid`), SUM(IFNULL(`patient_price`,0)) FROM `invoice_patient` WHERE `paid` NOT IN ('No','On Account','') AND `paid` IS NOT NULL AND `paid` NOT LIKE 'Net %' GROUP BY `paid`, `paid_date`, `patientid` UNION SELECT 'INS_PAY', `invoice_insurer`.`paid_date`, 0, `invoice`.`patientid` contactid, 0, '', '', '', '', `invoice_insurer`.`insurerid`, `invoice_insurer`.`paid`, '', SUM(IFNULL(`invoice_insurer`.`insurer_price`,0)) FROM `invoice_insurer` LEFT JOIN `invoice` ON `invoice_insurer`.`invoiceid`=`invoice`.`invoiceid` WHERE `invoice_insurer`.`paid` IN ('Yes') GROUP BY `invoice_insurer`.`paid`, `invoice_insurer`.`paid_date`, `invoice_insurer`.`insurerid`";
	
	$statement = mysqli_query($dbc, "SELECT * FROM (".$sql.") statement WHERE `contactid`='$contact' AND '$contact' != '' ORDER BY `tran_date`");
	$cust_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts`.`contactid`, `contacts`.`category`, SUM(IF(`statement`.`tran_date`!='Saved',amt,0)) credit_change, `contacts`.`first_name`, `contacts`.`last_name`, IFNULL(`contacts`.`amount_credit`,0) credit, IFNULL(`contacts`.`amount_owing`,0) debit FROM (".$sql.") statement LEFT JOIN `contacts` ON `statement`.`contactid`=`contacts`.`contactid` WHERE `contacts`.`contactid`='$contact'"));
	$end_balance = $cust_info['credit'] - $cust_info['debit'];
	$balance = $end_balance - $cust_info['credit_change'];
	$customer = decryptIt($cust_info['first_name']).' '.decryptIt($cust_info['last_name']);
	
	$statement_html = '<table border="1px">
			<tr>
				<th>Transaction Date</th>
				<th>Staff</th>
				<th>Injury</th>
				<th>Services</th>
				<th>'.$cust_info['category'].'</th>
				<th>Payer</th>
				<th>Payment</th>
				<th>Balance</th>
			</tr>
			<tbody>';
			
	$statement_html .= '<tr>';
		$statement_html .= '<td data-title="" colspan="7">Opening Balance</td>';
		$statement_html .= '<td data-title="Opening Balance">$'.number_format($balance,2).'</td>';
	$statement_html .= '</tr>';
	while($line = mysqli_fetch_array($statement)) {
		$balance += ($line['tran_date'] != 'Saved' ? $line['amt'] : 0);
		if((in_array('saved', $options) || $line['tran_date'] != 'Saved')
			&& (in_array('payments', $options) || $line['INV_TYPE'] != 'CUST_PAY')
			&& (in_array('insurer', $options) || $line['INV_TYPE'] != 'INS_PAY')
			&& (in_array('paid', $options) || $line['INV_TYPE'] != 'PAID')
			&& (in_array('outstanding', $options) || $line['INV_TYPE'] != 'OUTSTANDING')) {
			$injury = '';
			if($line['injuryid'] > 0) {
				$injury = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `patient_injury` WHERE `injuryid`='".$line['injuryid']."'"));
				$injury = $injury['injury_type'].': '.$injury['injury_name'].' ('.$injury['injury_date'].')';
			}
			$statement_html .= '<tr>';
				$statement_html .= '<td data-title="Transaction Date">'.$line['tran_date'].'</td>';
				$statement_html .= '<td data-title="Staff">'.($line['staffid'] > 0 ? get_contact($dbc, $line['staffid']) : '').'</td>';
				$statement_html .= '<td data-title="Injury">'.$injury.'</td>';
				$statement_html .= '<td data-title="Services">';
				foreach(explode(',',$line['serviceid']) as $service) {
					if($service > 0) {
						$service = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `services` WHERE `serviceid`='$service'"));
						$statement_html .= ($service['category'] != '' ? $service['category'].': ' : '').$service['heading'].'<br />';
					}
				}
				$inv_qty = explode(',',$line['quantity']);
				foreach(explode(',',$line['inventoryid']) as $i => $inventory) {
					if($inventory > 0 && $inv_qty[$i] > 0) {
						$inventory = mysqli_fetch_array(mysqli_query($dbc, "SELECT `name` FROM `inventory` WHERE `inventoryid`='$inventory'"));
						$statement_html .= $inv_qty[$i].' X '.$inventory['name'].'<br />';
					}
				}
				foreach(explode(',',$line['packageid']) as $package) {
					if($package > 0) {
						$package = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `package` WHERE `packageid`='$package'"));
						$statement_html .= ($package['category'] != '' ? $package['category'].': ' : '').$package['heading'].'<br />';
					}
				}
				$statement_html .= '</td>';
				$statement_html .= '<td data-title="Patient">'.$customer.'</td>';
				$statement_html .= '<td data-title="Payer">'.($line['insurer'] > 0 ? get_client($dbc, $line['insurer']) : $line['cust_pay']).'</td>';
				$statement_html .= '<td data-title="Payment">$'.number_format($line['amt'],2).'</td>';
				$statement_html .= '<td data-title="Balance">$'.($line['tran_date'] != 'Saved' ? number_format($balance,2) : 'N/A').'</td>';
			$statement_html .= '</tr>';
		}
	}
	$statement_html .= '<tr>';
		$statement_html .= '<td data-title="" colspan="7">Closing Balance</td>';
		$statement_html .= '<td data-title="Closing Balance">$'.number_format($end_balance,2).'</td>';
	$statement_html .= '</tr>';
	$statement_html .= '</tbody>
		</table>';
		
    DEFINE('INVOICE_LOGO', get_config($dbc, 'invoice_logo'));
    DEFINE('INVOICE_HEADER', 'Account Statement for '.$customer);

	if(!class_exists('MYPDF')) {
		class MYPDF extends TCPDF {

			//Page header
			public function Header() {
				if(INVOICE_LOGO != '') {
					$image_file = '../Invoice/download/'.INVOICE_LOGO;
					$this->Image($image_file, 10, 10, 80, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
				}
				$this->setCellHeightRatio(0.7);
				$this->SetFont('helvetica', '', 9);
				$footer_text = '<p style="text-align:right;">'.INVOICE_HEADER.'</p>';
				$this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);
			}

			// Page footer
			public function Footer() {
				// Position at 15 mm from bottom
				$this->SetY(-10);
				$this->SetFont('helvetica', 'I', 8);
				$footer_text = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages().' printed on  '.date('m/d/y').' at '.date('g:i:s A');
				$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
			}
		}
	}

	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
	$pdf->setFooterData(array(0,64,0), array(0,64,128));

	$pdf->SetMargins(PDF_MARGIN_LEFT, 30, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);

    $today_date = date('Y-m-d');
	$pdf->writeHTML(utf8_encode($statement_html), true, false, true, false, '');
	
	$pdf_name = 'download/account_'.$cust_info['contactid'].'_statement_'.date('Y_m_d').'.pdf';
	unlink($pdf_name);
	$pdf->Output($pdf_name, 'F');
	echo "../Contacts/".$pdf_name;
}