<?php require_once('../tcpdf/tcpdf.php');
	if($_GET['action'] == 'build') {
		include_once('../include.php');
		$invoiceid = filter_var($_GET['invoiceid'],FILTER_SANITIZE_STRING);
		$get_invoice = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `invoice` WHERE `invoiceid`='$invoiceid'"));
	}
	if($get_invoice['tile_name'] == 'work_ticket') {
		include('invoice_pdf_work_ticket.php');
	} else {
		$next_booking = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `booking` WHERE `appoint_date` > DATE_ADD(NOW(), INTERVAL 1 HOUR) AND `deleted`=0 AND `patientid`='".$get_invoice['patientid']."' ORDER BY `appoint_date` ASC"));
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
        $logo = 'download/'.$logo;
        if(!file_exists($logo)) {
            $logo = '../POSAdvanced/'.$logo;
            if(!file_exists($logo)) {
                $logo = '';
            }
        }
	    $invoice_header = get_config($dbc, 'invoice_header');
	    if(!empty($get_invoice['type']) && !empty(get_config($dbc, 'invoice_header_'.$get_invoice['type']))) {
	        $invoice_header = get_config($dbc, 'invoice_header_'.$get_invoice['type']);
	    }
		DEFINE('INVOICE_LOGO', $logo);
		DEFINE('INVOICE_HEADER', html_entity_decode($invoice_header));
		DEFINE('INVOICE_FOOTER', $footer_text);
		$invoiceid = $get_invoice['invoiceid'];
		$all_payment_type = explode('#*#',$get_invoice['payment_type'])[0];

		//Patient Invoice
		if(!class_exists('MYPDF')) {
			class MYPDF extends TCPDF {

				//Page header
				public function Header() {
					if(INVOICE_LOGO != '') {
						$image_file = INVOICE_LOGO;
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
					// Position at 15 mm from bottom
					//$this->SetY(-5);
					//$this->SetFont('helvetica', 'I', 5);
					//$footer_text = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages().' printed on  '.date('m/d/y').' at '.date('g:i:s A');
					//$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);

					// Position at 30 mm from bottom
					$this->SetY(-45);
					// Set font
					$this->SetFont('helvetica', 'I', 10);
					// Page number
					$footer_text = INVOICE_FOOTER;
					$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
				}
			}
		}

		$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
		$pdf->setFooterData(array(0,64,0), array(0,64,128));

		$pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(40);
		$pdf->SetFooterMargin(60);
		$pdf->SetAutoPageBreak(TRUE, 60);
		$pdf->AddPage();
		$pdf->SetFont('helvetica', '', 9);

		$html = '<table style="border: none;"><tr><td style="color: #46A251; width: 12%;"><p>Invoice No.:</p><p>Invoice Date:</p></td>
			<td style="width: 14%;"><p>'.$get_invoice['invoiceid'].'</p><p>'.$get_invoice['invoice_date'].'</p></td>
			<td style="color: #46A251; width: 20%;">Client Information:</td><td style="width: 25%;">';

		if($get_invoice['patientid'] == 0) {
			$non_patient = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `invoice_nonpatient` WHERE `invoiceid`='".$get_invoice['invoiceid']."'"));
			$html .= $non_patient['first_name'].' '.$non_patient['last_name'].'<br/>'.$non_patient['email'];
		} else {
			$html .= get_contact($dbc, $get_invoice['patientid']).'<br/>'.get_address($dbc, $get_invoice['patientid']);
		}
		$html .= '</td><td style="color: #46A251; width: 12%;">';
		if($get_invoice['therapistsid'] > 0) {
			$html .= 'Practitioner:';
		}
		$html .= '</td><td style="width: 17%;">';
		if($get_invoice['therapistsid'] > 0) {
			$html .= get_contact($dbc, $get_invoice['therapistsid']);
			$licence = mysqli_fetch_array(mysqli_query($dbc, "SELECT `license`, `credential` FROM `contacts` WHERE `contactid`='".$get_invoice['therapistsid']."'"));
			$html .= ($licence['license'] != '' || $license['credential'] != '' ? '<br />'.$licence['credential'].($licence['license'] != '' && $license['credential'] != '' ? '; ' : '').$licence['license'] : '');
		}
		$html .= '</td></tr>';
		foreach(explode(',',$get_invoice['insurerid']) as $inv_insurerid) {
			if($inv_insurerid > 0) {
				$html .= '<tr><td></td><td></td><td style="color: #46A251;">Insurance:</td><td>';
				$html .= get_client($dbc, $inv_insurerid).'<br/>'.get_address($dbc, $inv_insurerid);
				$html .= '</td><td></td><td></td></tr>';
			}
		}
		$html .= '</table>';

		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->SetFont('helvetica', '', 9);
		$injury_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `patient_injury` WHERE `injuryid`='".$get_invoice['injuryid']."'"));
		$injury_info = $injury_info['injury_type'].': '.$injury_info['injury_name'];

		$html = '<table border="1px" class="table table-bordered" style="padding:3px; border:1px solid black;">
		<tr style="background-color: #37C6F4;; color: #FFFFFF;">
			<th width="12%" style="text-align: center;">Date of Service</th>
			<th width="12%" style="text-align: center;">Injury</th>
			<th width="48%" style="text-align: center;">Description</th>
			<th width="6%" style="text-align: right;">Qty</th>
			<th width="10%" style="text-align: right;">Price Per Unit</th>
			<th width="12%" style="text-align: right;">Line Total</th>
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
						<td style="text-align: left;">'.$injury_info.'</td>
						<td style="text-align: left;">'.(explode(',',$inv_info['fee'])[$i] < 0 ? 'Refund ' : '').($service['category'] != '' ? $service['category'].': ' : '').$service['heading'].'</td>
						<td style="text-align: right;">1</td>
						<td style="text-align: right;">$'.number_format($fee,2).'</td>
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
						<td style="text-align: center;">'.$injury_info.'</td>
						<td style="text-align: center;">'.($fee < 0 ? 'Refund ' : '').$inventory['name'].'</td>
						<td style="text-align: right;">'.$qty.'</td>
						<td style="text-align: right;">$'.number_format($fee,2).'</td>
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
						<td style="text-align: center;">'.$injury_info.'</td>
						<td style="text-align: center;">'.($fee < 0 ? 'Refund ' : '').($package['category'] != '' ? $package['category'].': ' : '').$package['heading'].'</td>
						<td style="text-align: right;">'.$qty.'</td>
						<td style="text-align: right;">$'.number_format($fee,2).'</td>
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
						<td style="text-align: center;">'.$injury_info.'</td>
						<td style="text-align: center;">'.($fee < 0 ? 'Refund ' : '').$misc.'</td>
						<td style="text-align: right;">'.$qty.'</td>
						<td style="text-align: right;">$'.number_format($fee,2).'</td>
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

		/*if($get_invoice['promotionid'] > 0) {
			$promo = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `promotion` WHERE `promotionid`='".$get_invoice['promotionid']."'"));
			$html .= '<tr>
				<td>'.$get_invoice['service_date'].'</td>
				<td>Promotion : '.$promo['heading'].'</td>
				<td><br></td>
				<td>-$'.number_format($promo['cost'],2).'</td>
				<td>-$'.number_format($promo['cost'],2).'</td>
			</tr>';
		}
		if($get_invoice['pro_bono'] != 0) {
			$html .= '<tr>
				<td>'.$get_invoice['service_date'].'</td>
				<td>Pro-Bono Service</td>
				<td><br></td>
				<td>-$'.number_format($get_invoice['pro_bono'],2).'</td>
				<td>-$'.number_format($get_invoice['pro_bono'],2).'</td>
			</tr>';
		}

		if($paid == 'Waiting on Insurer') {
			foreach(explode(',',$get_invoice['insurerid']) as $i => $insurerid) {
				if(explode(',',$get_invoice['insurance_payment'])[$i] != 0) {
					$html .= '<tr>
						<td>'.$get_invoice['service_date'].'</td>
						<td>Payment by Insurer: '.get_client($dbc, $insurerid).'</td>
						<td>&nbsp;</td>
						<td>$'.$get_invoice['final_price'].'</td>
						<td>$'.number_format(explode(',',$get_invoice['insurance_payment'])[$i],2).'</td>
					</tr>';
				}
			}
		} else if($paid == 'No') {
			foreach(explode(',',$get_invoice['insurerid']) as $i => $insurerid) {
				if(explode(',',$get_invoice['insurance_payment'])[$i] != 0) {
					$html .= '<tr>
						<td>'.$get_invoice['service_date'].'</td>
						<td>Payment by Insurer: '.get_client($dbc, $insurerid).'</td>
						<td>&nbsp;</td>
						<td>$'.$get_invoice['final_price'].'</td>
						<td>$'.number_format(explode(',',$get_invoice['insurance_payment'])[$i],2).'</td>
					</tr>';
				}
			}
			foreach(explode(',',explode('#*#',$get_invoice['payment_type'])[0]) as $i => $type) {
				if(explode(',',explode('#*#',$get_invoice['payment_type'])[1])[$i] != 0) {
					$html .= '<tr>
						<td>'.$get_invoice['service_date'].'</td>
						<td>'.(explode(',',explode('#*#',$get_invoice['payment_type'])[1])[$i] > 0 ? 'Payment' : 'Refund').' by '.$type.'</td>
						<td>&nbsp;</td>
						<td>$'.$get_invoice['final_price'].'</td>
						<td>$'.number_format(explode(',',explode('#*#',$get_invoice['payment_type'])[1])[$i],2).'</td>
					</tr>';
				}
			}
		} else if(($type != 'Pro-Bono') && ($paid != 'On Account') && (strpos($all_payment_type, 'On Account') === FALSE)) {
			foreach(explode(',',explode('#*#',$get_invoice['payment_type'])[0]) as $i => $type) {
				if(explode(',',explode('#*#',$get_invoice['payment_type'])[1])[$i] != 0) {
					$html .= '<tr>
						<td>'.$get_invoice['service_date'].'</td>
						<td>'.(explode(',',explode('#*#',$get_invoice['payment_type'])[1])[$i] > 0 ? 'Payment' : 'Refund').' by '.$type.'</td>
						<td>&nbsp;</td>
						<td>$'.$get_invoice['final_price'].'</td>
						<td>$'.number_format(explode(',',explode('#*#',$get_invoice['payment_type'])[1])[$i],2).'</td>
					</tr>';
				}
			}
		} else if($type != 'Pro-Bono') {
			$html .= '<tr>
				<td>'.$get_invoice['service_date'].'</td>
				<td>Pay this Amount</td>
				<td>&nbsp;</td>
				<td>$'.$get_invoice['final_price'].'</td>
				<td>$'.number_format(array_sum(explode(',',explode('#*#',$get_invoice['payment_type'])[1])[$i]),2).'</td>
			</tr>';
		}*/
		$html .= '</table><br/><br/>';

		$html .= '<table style="width: 100%;" cellspacing="20">';
		$html .= '<tr><td style="width:55%;"></td><td style="width: 30%;">Total Charges</td><td style="width: 15%;">$'.number_format($get_invoice['total_price'],2).'</td></tr>';
		$non_client_paid = 0;
		if($get_invoice['promotionid'] > 0) {
			$promo = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `promotion` WHERE `promotionid`='".$get_invoice['promotionid']."'"));
			$html .= '<tr><td style="width:55%;"></td><td style="width: 30%;">Promotion : '.$promo['heading'].'</td><td style="width: 15%;">$'.number_format($promo['cost'],2).'</td></tr>';
			$non_client_paid += $promo['cost'];
		}
		if($get_invoice['discount'] != '' && $get_invoice['discount'] != 0) {
			$html .= '<tr><td style="width:55%;"></td><td style="width: 30%;">Discount</td><td style="width: 15%;">$'.number_format($get_invoice['discount'],2).'</td></tr>';
            $non_client_paid += $get_invoice['discount'];
		}
		if($get_invoice['delivery'] != '' && $get_invoice['delivery'] != 0) {
			$html .= '<tr><td style="width:55%;"></td><td style="width: 30%;">Delivery</td><td style="width: 15%;">$'.number_format($get_invoice['delivery'],2).'</td></tr>';
		}
		if($get_invoice['assembly'] != '' && $get_invoice['assembly'] != 0) {
			$html .= '<tr><td style="width:55%;"></td><td style="width: 30%;">Assembly</td><td style="width: 15%;">$'.number_format($get_invoice['assembly'],2).'</td></tr>';
		}
		if($get_invoice['gratuity'] != '' && $get_invoice['gratuity'] != 0) {
			$html .= '<tr><td style="width:55%;"></td><td style="width: 30%;">Gratuity</td><td style="width: 15%;">$'.number_format($get_invoice['gratuity'],2).'</td></tr>';
		}
		if(array_sum(explode(',',$get_invoice['insurance_payment'])) != 0) {
			$html .= '<tr><td style="width:55%;"></td><td style="width: 30%;">Total Insurance Charges</td><td style="width: 15%;">$'.number_format(array_sum(explode(',',$get_invoice['insurance_payment'])),2).'</td></tr>';
			$non_client_paid += array_sum(explode(',',$get_invoice['insurance_payment']));
		}
		if($get_invoice['pro_bono'] != 0) {
			$html .= '<tr><td style="width:55%;"></td><td style="width: 30%;">Total Pro-Bono Services</td><td style="width: 15%;">$'.number_format($get_invoice['pro_bono'],2).'</td></tr>';
			$non_client_paid += $get_invoice['pro_bono'];
		}
		
		$client_due = ($non_client_paid == $get_invoice['total_price'] + $get_invoice['gst_amt'] ? 0 : $get_invoice['total_price'] - $non_client_paid + $get_invoice['delivery'] + $get_invoice['assembly']);
		$html .= '<tr><td style="width:55%;"></td><td style="width: 30%;">Total Due by Client</td><td style="width: 15%;">$'.number_format($client_due,2).'</td></tr>';
		
		//Tax
		$get_pos_tax = get_config($dbc, 'invoice_tax');
		if($get_pos_tax != '') {
			$tax_amt = $get_invoice['gst_amt'];
			$client_due += ($non_client_paid == $get_invoice['total_price'] + $get_invoice['gst_amt'] ? 0 : $get_invoice['gst_amt']);
			$total_tax_rate = 0;
			foreach(explode('*#*',$get_pos_tax) as $pos_tax) {
				$total_tax_rate += explode('**',$pos_tax)[1];
			}
			foreach(explode('*#*',$get_pos_tax) as $pos_tax) {
				if($pos_tax != '') {
					$pos_tax_name_rate = explode('**',$pos_tax);
					$html .= '<tr><td style="width:55%;"></td><td style="width: 30%;">'.$pos_tax_name_rate[0].'  ['.$pos_tax_name_rate[2].']</td><td style="width:15%;">$'.number_format($tax_amt * $pos_tax_name_rate[1] / $total_tax_rate,2).'</td></tr>';
				}
			}
		}
		
		$html .= '<tr><td style="width:55%;"></td><td style="color: #37C6F4; width: 30%;">TOTAL AMOUNT OWING</td><td style="width: 15%;">$'. number_format($client_due,2) .'</td></tr>';
		$html .= '</table>';

		$pdf->writeHTML($html, true, false, true, false, '');
        $invoice_type = $get_invoice['type'];
        include('../Invoice/pos_invoice_append_ticket.php');
		$pdf->Output('download/invoice_'.$invoiceid.'.pdf', 'F');

		$invoice_md5 .= md5_file("download/invoice_".$invoiceid.".pdf");
		$query_update_booking = "UPDATE `invoice` SET `invoice_md5` = '$invoice_md5' WHERE `invoiceid` = '$invoiceid'";
		$result_update_booking = mysqli_query($dbc, $query_update_booking);

		//Patient Invoice
		if($_GET['action'] == 'build') {
			echo "<script> window.location.replace('".WEBSITE_URL."/Invoice/download/invoice_".$invoiceid.".pdf'); </script>";
		}
	}