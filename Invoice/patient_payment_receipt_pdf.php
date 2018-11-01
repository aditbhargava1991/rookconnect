 <?php require_once('../tcpdf/tcpdf.php');
 
	$get_invoice = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `invoice` WHERE `invoiceid`='$invoiceid'"));
    $patientid = $get_invoice['patientid'];
    $therapistsid = $get_invoice['therapistsid'];
    $service_date = $get_invoice['service_date'];
	if(!defined(INVOICE_LOGO)) {
	    $logo = get_config($dbc, 'invoice_logo');
	    if(!empty($get_invoice['type']) && !empty(get_config($dbc, 'invoice_logo_'.$get_invoice['type']))) {
	        $logo = get_config($dbc, 'invoice_logo_'.$get_invoice['type']);
	    }

		DEFINE('INVOICE_LOGO', $logo);
	}
	if(!defined(INVOICE_HEADER)) {
	    $invoice_header = get_config($dbc, 'invoice_header');
	    if(!empty($get_invoice['type']) && !empty(get_config($dbc, 'invoice_header_'.$get_invoice['type']))) {
	        $invoice_header = get_config($dbc, 'invoice_header_'.$get_invoice['type']);
	    }
		DEFINE('INVOICE_HEADER', html_entity_decode($invoice_header));
	}
	if(!defined(INVOICE_FOOTER)) {
		$next_booking = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `booking` WHERE `appoint_date` > DATE_ADD(NOW(), INTERVAL 1 HOUR) AND `deleted`=0 AND `patientid`='".$get_invoice['patientid']."' ORDER BY `appoint_date` ASC"));
		if($next_booking['bookingid'] > 0) {
			$footer_text = '<p style="color: #37C6F4; font-size: 14; font-weight: bold; text-align: center;">Your next appointment is '.date('d/m/y',strtotime($next_booking['appoint_date']))." at ".date('G:ia',strtotime($next_booking['appoint_date'])).'</p>';
		}
	    $invoice_footer = get_config($dbc, 'invoice_footer');
	    if(!empty($get_invoice['type']) && !empty(get_config($dbc, 'invoice_footer_'.$get_invoice['type']))) {
	        $invoice_footer = get_config($dbc, 'invoice_footer_'.$get_invoice['type']);
	    }
		$footer_text .= html_entity_decode($invoice_footer);
		DEFINE('INVOICE_FOOTER', $footer_text);
	}

    $staff = get_contact($dbc, $therapistsid);

    //Patient Invoice
	if(!class_exists('PATIENTPDF')) {
		class PATIENTPDF extends TCPDF {

			//Page header
			public function Header() {
				if(INVOICE_LOGO != '') {
					$image_file = 'download/'.INVOICE_LOGO;
					$image_size = getimagesize($image_file);
					$width = ''; // Max 80
					$height = ''; // Max 25
					if($image_size[0] * 25 / $image_size[1] > 80) {
						$width = 80;
					} else {
						$height = 25;
					}
					$this->Image($image_file, 10, 10, $width, $height, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
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

	$html = '<table style="border: none;" cellspacing="20"><tr><td style="color: #46A251; width: 20%;"><p>Invoice No.:</p><p>Invoice Date:</p></td>
		<td style="width: 20%;"><p>'.$get_invoice['invoiceid'].'</p><p>'.$get_invoice['invoice_date'].'</p></td>
		<td style="color: #46A251; width: 30%;">Client Information:</td><td style="width: 30%;">';

    if($get_invoice['patientid'] == 0) {
		$non_patient = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `invoice_nonpatient` WHERE `invoiceid`='".$get_invoice['invoiceid']."'"));
        $html .= $non_patient['first_name'].' '.$non_patient['last_name'].'<br/>'.$non_patient['email'];
    } else {
        $html .= get_contact($dbc, $get_invoice['patientid']).'<br/>'.get_address($dbc, $get_invoice['patientid']);
    }
	$html .= '</td></tr></table>';
	
    $html .= '<table border="1px" class="table table-bordered" style="padding:3px; border:1px solid black;">
	<tr style="background-color: #37C6F4;; color: #FFFFFF;">
		<th width="25%" style="text-align: center;">Date of Service</th>
		<th width="50%" style="text-align: center;">Description</th>
		<th width="25%" style="text-align: right;">Line Total</th>
	</tr>';
	
	$base_invoiceid = ($get_invoice['invoiceid_src'] > 0 ? $get_invoice['invoiceid_src'] : $get_invoice['invoiceid']);
	$invoice_changes = mysqli_query($dbc, "SELECT * FROM `invoice` WHERE (`invoiceid_src`='".$base_invoiceid."' OR `invoiceid`='".$base_invoiceid."') AND `invoiceid` <= '".$get_invoice['invoiceid']."' ORDER BY `invoiceid` ASC");
	while($inv_info = mysqli_fetch_array($invoice_changes)) {
		foreach(explode(',',$inv_info['serviceid']) as $i => $service) {
			if($service > 0) {
				$service = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid`='$service'"));
				$fee_owed = $fee = explode(',',$inv_info['fee'])[$i];
				$html .= '<tr>
					<td style="text-align: center;">'.$inv_info['service_date'].'</td>
					<td style="text-align: left;">'.(explode(',',$inv_info['fee'])[$i] < 0 ? 'Refund ' : '').($service['category'] != '' ? $service['category'].': ' : '').$service['heading'].'</td>
					<td style="text-align: right;">$'.number_format($fee_owed,2).'</td>
				</tr>';
				foreach(explode('#*#',explode(',',$inv_info['service_insurer'])[$i]) as $line_insurer) {
					$line_insurer = explode(':',$line_insurer);
					if($line_insurer[0] > 0 && $line_insurer[1] != 0) {
						$fee_owed -= $line_insurer[1];
						$html .= '<tr style="background-color: #9DDCF4;"><td colspan="4" style="text-align: center;">'.get_client($dbc, $line_insurer[0]).'</td><td style="text-align: right;">(-$'.number_format($line_insurer[1],2).')</td><td style="text-align: right;">$'.number_format($fee_owed,2).'</td></tr>';
					}
				}
			}
		}
		foreach(explode(',',$inv_info['inventoryid']) as $i => $inventory) {
			if($inventory > 0) {
				$inventory = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `inventory` WHERE `inventoryid`='$inventory'"));
				$qty = explode(',',$inv_info['quantity'])[$i];
				$fee_owed = $fee = explode(',',$inv_info['sell_price'])[$i];
				$html .= '<tr>
					<td style="text-align: center;">'.$inv_info['service_date'].'</td>
					<td style="text-align: center;">'.($fee < 0 ? 'Refund ' : '').$inventory['name'].'</td>
					<td style="text-align: right;">$'.number_format($fee_owed,2).'</td>
				</tr>';
				foreach(explode('#*#',explode(',',$inv_info['inventory_insurer'])[$i]) as $line_insurer) {
					$line_insurer = explode(':',$line_insurer);
					if($line_insurer[0] > 0 && $line_insurer[1] != 0) {
						$fee_owed -= $line_insurer[1];
						$html .= '<tr style="background-color: #9DDCF4;"><td colspan="4" style="text-align: center;">'.get_client($dbc, $line_insurer[0]).'</td><td style="text-align: right;">(-$'.number_format($line_insurer[1],2).')</td><td style="text-align: right;">$'.number_format($fee_owed,2).'</td></tr>';
					}
				}
			}
		}
		foreach(explode(',',$inv_info['packageid']) as $i => $package) {
			if($package > 0) {
				$package = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `package` WHERE `packageid`='$package'"));
				$fee_owed = $fee = explode(',',$inv_info['package_cost'])[$i];
				$html .= '<tr>
					<td style="text-align: center;">'.$inv_info['service_date'].'</td>
					<td style="text-align: center;">'.($fee < 0 ? 'Refund ' : '').($package['category'] != '' ? $package['category'].': ' : '').$package['heading'].'</td>
					<td style="text-align: right;">$'.number_format($fee_owed,2).'</td>
				</tr>';
				foreach(explode('#*#',explode(',',$inv_info['package_insurer'])[$i]) as $line_insurer) {
					$line_insurer = explode(':',$line_insurer);
					if($line_insurer[0] > 0 && $line_insurer[1] != 0) {
						$fee_owed -= $line_insurer[1];
						$html .= '<tr style="background-color: #9DDCF4;"><td colspan="4" style="text-align: center;">'.get_client($dbc, $line_insurer[0]).'</td><td style="text-align: right;">(-$'.number_format($line_insurer[1],2).')</td><td style="text-align: right;">$'.number_format($fee_owed,2).'</td></tr>';
					}
				}
			}
		}
		foreach(explode(',',$inv_info['misc_item']) as $i => $misc) {
			if($misc != '') {
				$qty = explode(',',$inv_info['misc_qty'])[$i];
				$fee_owed = $fee = explode(',',$inv_info['misc_total'])[$i];
				$html .= '<tr>
					<td style="text-align: center;">'.$inv_info['service_date'].'</td>
					<td style="text-align: center;">'.($fee < 0 ? 'Refund ' : '').$misc.'</td>
					<td style="text-align: right;">$'.number_format($fee_owed,2).'</td>
				</tr>';
				foreach(explode('#*#',explode(',',$inv_info['misc_insurer'])[$i]) as $line_insurer) {
					$line_insurer = explode(':',$line_insurer);
					if($line_insurer[0] > 0 && $line_insurer[1] != 0) {
						$fee_owed -= $line_insurer[1];
						$html .= '<tr style="background-color: #9DDCF4;"><td colspan="4" style="text-align: center;">'.get_client($dbc, $line_insurer[0]).'</td><td style="text-align: right;">(-$'.number_format($line_insurer[1],2).')</td><td style="text-align: right;">$'.number_format($fee_owed,2).'</td></tr>';
					}
				}
			}
		}
	}
	$html .= '</table>';
	
	$html .= '<table style="border: none;" cellspacing="20"><tr><td></td><td></td><td style="color: #46A251;">';
	if($get_invoice['therapistsid'] > 0) {
		$html .= 'Practitioner:';
	}
	$html .= '</td><td>';
	if($get_invoice['therapistsid'] > 0) {
		$html .= get_contact($dbc, $get_invoice['therapistsid']);
		$licence = mysqli_fetch_array(mysqli_query($dbc, "SELECT `license`, `credential` FROM `contacts` WHERE `contactid`='".$get_invoice['therapistsid']."'"));
		$html .= ($licence['license'] != '' || $license['credential'] != '' ? '<br />'.$licence['credential'].($licence['license'] != '' && $license['credential'] != '' ? '; ' : '').$licence['license'] : '');
	}
	$html .= '</td></tr>';
	$html .= '<tr><td></td><td></td><td style="color: #37C6F4; font-weight: bold;">INVOICE DETAILS</td><td></td></tr>';
    
    $html .= '<tr><td></td><td></td><td>Sub Total:</td><td>$'.number_format($get_invoice['total_price'],2).'</td></tr>';
    
    if ($get_invoice['discount']!='' || $get_invoice['discount']!=0) {
        $html .= '<tr><td></td><td></td><td>Discount:</td><td>$'.number_format($get_invoice['discount'],2).'</td></tr>';
        $html .= '<tr><td></td><td></td><td>Total After Discount:</td><td>$'.number_format($get_invoice['total_price']-$get_invoice['discount'],2).'</td></tr>';
    }
    if ($get_invoice['delivery']!='' || $get_invoice['delivery']!=0) {
        $html .= '<tr><td></td><td></td><td>Delivery:</td><td>$'.number_format($get_invoice['delivery'],2).'</td></tr>';
    }
    if ($get_invoice['assembly']!='' || $get_invoice['assembly']!=0) {
        $html .= '<tr><td></td><td></td><td>Assembly:</td><td>$'.number_format($get_invoice['assembly'],2).'</td></tr>';
    }
	
	$balance_owing = $get_invoice['final_price'] - $get_invoice['pro_bono'] - array_sum(explode(',',$get_invoice['insurance_payment']));
	$html .= '<tr><td></td><td></td><td>Total Due by Client:</td><td>$'.number_format(($balance_owing != 0 ? $balance_owing - $get_invoice['gst_amt'] : 0),2).'</td></tr>';
    //Tax
    $get_pos_tax = get_config($dbc, 'invoice_tax');
    if($get_pos_tax != '') {
		$tax_amt = $get_invoice['gst_amt'];
		$total_tax_rate = 0;
		foreach(explode('*#*',$get_pos_tax) as $pos_tax) {
			$total_tax_rate += explode('**',$pos_tax)[1];
		}
		foreach(explode('*#*',$get_pos_tax) as $pos_tax) {
			if($pos_tax != '') {
				$pos_tax_name_rate = explode('**',$pos_tax);
				$html .= '<tr><td></td><td></td><td>'.$pos_tax_name_rate[0].'  ['.$pos_tax_name_rate[2].']:</td><td>$'.number_format($tax_amt * $pos_tax_name_rate[1] / $total_tax_rate,2).'</td></tr>';
			}
		}
    }
	$html .= '<tr><td></td><td></td><td style="color: #37C6F4; font-weight: bold;">TOTAL AMOUNT OWING:</td><td style="color: #37C6F4; font-weight: bold;">$'.number_format($balance_owing,2).'</td></tr>';
	$payment_query = mysqli_query($dbc, "SELECT `paid`, SUM(`patient_price`) amount FROM `invoice_patient` WHERE `invoiceid`='".$get_invoice['invoiceid']."' AND IFNULL(`paid`,'') NOT IN ('On Account','') GROUP BY `paid`");
	while($payment_row = mysqli_fetch_array($payment_query)) {
		$html .= '<tr><td></td><td></td><td style="color: #37C6F4; font-weight: bold;">PAYMENT BY:</td><td style="color: #37C6F4; font-weight: bold;">'.$payment_row['paid'].' (-$'.number_format($payment_row['amount'],2).')</td></tr>';
		$balance_owing -= $payment_row['amount'];
	}
	$html .= '<tr><td></td><td></td><td style="color: #37C6F4; font-weight: bold;">BALANCE:</td><td style="color: #37C6F4; font-weight: bold;">$'.number_format($balance_owing,2).'</td></tr></table>';

	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('download/patientreceipt_'.$invoiceid.'.pdf', 'F');

	//Patient Invoice