<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
if(isset($_POST['printcsv'])) {
  $insurer = $_POST['insurerpdf'];
  $invoice_no = $_POST['invoice_nopdf'];
  $ui_no = $_POST['ui_nopdf'];
  $ui_total = $_POST['ui_totalpdf'];
  $as_at_date = $_POST['as_at_datepdf'];
  $file_name = "ui_invoice_" . date("Y-m-d_m") . '.csv';

  ob_end_clean();
  $fp = fopen('php://output','w');
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename='.$file_name);

  $data[] = 'UI#,Invoice#,Service Date,Invoice Date,Insurer,Amount Receivable';
  $today_date = date("Y-m-d");
  if($insurer != '') {
      $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE ii.insurerid='$insurer' AND ii.invoiceid = i.invoiceid AND (ii.paid_date > '$as_at_date' OR IFNULL(ii.`paid`,'')!='Yes') AND ii.ui_invoiceid IS NOT NULL ORDER BY ii.invoiceid");
  } else if($invoice_no != '') {
      $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE (ii.paid_date > '$as_at_date' OR IFNULL(ii.`paid`,'')!='Yes') AND ii.invoiceid = i.invoiceid AND i.invoiceid='$invoice_no' AND ii.ui_invoiceid IS NOT NULL ORDER BY ii.invoiceid");
  } else if($ui_no != '') {
      $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE (ii.paid_date > '$as_at_date' OR IFNULL(ii.`paid`,'')!='Yes') AND ii.invoiceid = i.invoiceid AND ii.ui_invoiceid='$ui_no' AND ii.ui_invoiceid IS NOT NULL ORDER BY ii.invoiceid");
  } else if($ui_total > 0) {
      $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE (ii.paid_date > '$as_at_date' OR IFNULL(ii.`paid`,'')!='Yes') AND ii.invoiceid = i.invoiceid AND ii.ui_invoiceid IN (SELECT `ui_invoiceid` FROM `invoice_insurer` GROUP BY `ui_invoiceid` HAVING SUM(`insurer_price`)=$ui_total) AND ii.ui_invoiceid IS NOT NULL ORDER BY ii.invoiceid");
  } else {
      $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE ii.invoiceid = i.invoiceid AND invoice_date = '$today_date' AND (ii.paid_date > '$as_at_date' OR IFNULL(ii.`paid`,'')!='Yes') AND ii.ui_invoiceid IS NOT NULL ORDER BY ii.invoiceid");
  }

  $amt_to_bill = 0;
  while($row_report = mysqli_fetch_array($report_service)) {
      $row = '';
      $insurer_price = $row_report['insurer_price'];
      $invoiceid = $row_report['invoiceid'];
      $patientid = get_all_from_invoice($dbc, $invoiceid, 'patientid');
      $insurerid = rtrim($row_report['insurerid'],',');
      $each_insurance_payment = explode('#*#', $insurance_payment);

      $row .= $row_report['ui_invoiceid'] . ',';
      $row .= $invoiceid . ',';
      $row .= $row_report['invoice_date'].',';
      $row .= $row_report['service_date'].',';
      $row .= get_all_form_contact($dbc, $insurerid, 'name').',';
      $row .= $insurer_price . ',';

      $amt_to_bill += $insurer_price;
      $data[] = $row;
  }

  $data[] = ',,,,Total,$'.number_format($amt_to_bill, 2);

  foreach ($data as $line) {
      $val = explode(",", $line);
      fputcsv($fp, $val);
  }
  exit();
}

if (isset($_POST['printpdf'])) {
    $insurerpdf = $_POST['insurerpdf'];
    $invoice_nopdf = $_POST['invoice_nopdf'];
    $ui_nopdf = $_POST['ui_nopdf'];
    $ui_totalpdf = $_POST['ui_totalpdf'];
    $as_at_datepdf = $_POST['as_at_datepdf'];

    //DEFINE('START_DATE', $starttimepdf);
    //DEFINE('END_DATE', $endtimepdf);
    DEFINE('AS_AT_DATE', $as_at_datepdf);
    DEFINE('REPORT_LOGO', get_config($dbc, 'report_logo'));
    DEFINE('REPORT_HEADER', html_entity_decode(get_config($dbc, 'report_header')));
    DEFINE('REPORT_FOOTER', html_entity_decode(get_config($dbc, 'report_footer')));

	class MYPDF extends TCPDF {

		public function Header() {
			//$image_file = WEBSITE_URL.'/img/Clinic-Ace-Logo-Final-250px.png';
            if(REPORT_LOGO != '') {
                $image_file = 'download/'.REPORT_LOGO;
                $this->Image($image_file, 10, 10, 80, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);

            $this->SetFont('helvetica', '', 13);
            $footer_text = 'UI Invoice Report as at '.AS_AT_DATE;
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "C", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : UI Reports are grouped receivables (this tab displays the groups and their total amounts).";
            $this->writeHTMLCell(0, 0, 10 , 45, $footer_text, 0, 0, false, "R", true);
		}

		// Page footer
		public function Footer() {
            $this->SetY(-24);
            $this->SetFont('helvetica', 'I', 9);
            $footer_text = '<span style="text-align:left;">'.REPORT_FOOTER.'</span>';
            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);

			// Position at 15 mm from bottom
			$this->SetY(-15);
            $this->SetFont('helvetica', 'I', 9);
			$footer_text = '<span style="text-align:right;">Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages().' printed on '.date('Y-m-d H:i:s').'</span>';
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "R", true);
    	}
	}

	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
	$pdf->setFooterData(array(0,64,0), array(0,64,128));

	$pdf->SetMargins(PDF_MARGIN_LEFT, 55, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);

    $html .= '<br><br>' . report_receivables($dbc, 'padding:3px; border:1px solid black;', '', '', $insurerpdf, $invoice_nopdf, $ui_nopdf, $ui_totalpdf, $as_at_datepdf);

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/ui_invoice_report_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'ui_invoice_reports', 0, WEBSITE_URL.'/Reports/Download/ui_invoice_report_'.$today_date.'.pdf', 'UI Invoice Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/ui_invoice_report_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $insurer = $insurerpdf;
    $invoice_no = $invoice_nopdf;
    $ui_no = $ui_nopdf;
    $ui_total = $ui_totalpdf;
    $as_at_date = $as_at_datepdf;
}

?>


        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

            <div class="notice double-gap-bottom popover-examples">
                <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
                <div class="col-sm-11"><span class="notice-name">NOTE:</span>
                UI Reports are grouped receivables (this tab displays the groups and their total amounts).</div>
                <div class="clearfix"></div>
            </div>

            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if(!empty($_GET['p1'])) {
                $insurer = $_GET['p3'];
                $invoice_no = $_GET['p5'];
                $ui_no = $_GET['p6'];
                $ui_total = $_GET['p7'];
            }
            else if (isset($_POST['search_email_submit'])) {
                $insurer = $_POST['insurer'];
                $invoice_no = $_POST['invoice_no'];
                $ui_no = $_POST['ui_no'];
                $ui_total = $_POST['ui_total'];
				$as_at_date = $_POST['as_at_date'];
            }
            else if (isset($_POST['search_email_all'])) {
                $insurer = '';
                $invoice_no = '';
                $ui_no = '';
                $ui_total = '';
				$as_at_date = date('Y-m-d');
            }
			else {
				$as_at_date = date('Y-m-d');
			}
            ?>
            <center><div class="form-group">
				<div class="form-group col-sm-5">
					<label class="col-sm-4"><span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Search for invoice(s) by insurer."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						Insurer:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select an Insurer..." name="insurer" class="chosen-select-deselect form-control">
							<option value="">Display All</option>
							<?php
								$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='Insurer' AND deleted=0 AND `status`=1"),MYSQLI_ASSOC));
								foreach($query as $id) {
									$selected = '';
									$selected = $id == $insurer ? 'selected = "selected"' : '';
									echo "<option " . $selected . "value='". $id."'>".get_client($dbc, $id).'</option>';
								}
							  ?>
						</select>
					</div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4"><span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Select the date that the report will be based."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						Report As At:</label>
					<div class="col-sm-8"><input name="as_at_date" type="text" class="form-control" value="<?php echo $as_at_date; ?>"></div>
				</div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4"><span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Search by invoice # directly. You must enter a complete value."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						Invoice #:</label>
					<div class="col-sm-8"><input name="invoice_no" type="text" class="form-control" value="<?php echo $invoice_no; ?>"></div>
				</div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4"><span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Search by the generated UI #."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						UI #:</label>
					<div class="col-sm-8"><input name="ui_no" type="text" class="form-control" value="<?php echo $ui_no; ?>"></div>
				</div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4"><span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Search by the total value of the generated UI."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						UI Total $:</label>
					<div class="col-sm-8"><input name="ui_total" type="text" class="form-control" value="<?php echo $ui_total; ?>"></div>
				</div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
			<span class="popover-examples list-inline" style="margin:0 0 0 10px;"><a data-toggle="tooltip" data-placement="top" title="Select this to remove all of the search filters you've applied. It will revert back to today's invoices."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <button type="submit" name="search_email_all" value="Search" class="btn brand-btn mobile-block">Display All</button></div></center>

            <input type="hidden" name="insurerpdf" value="<?php echo $insurer; ?>">
            <input type="hidden" name="invoice_nopdf" value="<?php echo $invoice_no; ?>">
            <input type="hidden" name="ui_nopdf" value="<?php echo $ui_no; ?>">
            <input type="hidden" name="ui_totalpdf" value="<?php echo $ui_total; ?>">
            <input type="hidden" name="as_at_datepdf" value="<?php echo $as_at_date; ?>">

            <button type="submit" name="printcsv" value="Print CSV Report" title="Print CSV Report" class="pull-right"><img title="Print CSV Report" width="15px" src="../img/csv.png"></button>
            <button type="submit" name="printpdf" style="margin-right:15px" value="Print PDF Report" title="Print PDF Report" title="Print PDF Report" class="pull-right"><img src="../img/pdf.png"></button>
            <br><br>

            <?php echo report_receivables($dbc, '', '', '', $insurer, $invoice_no, $ui_no, $ui_total, $as_at_date); ?>

        </form>

<?php
function report_receivables($dbc, $table_style, $table_row_style, $grand_total_style, $insurer, $invoice_no, $ui_no, $ui_total, $as_at_date) {

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="10%">UI#</th>
    <th width="24%">Invoice#</th>
    <th width="8%">Service Date</th>
    <th width="8%">Invoice Date</th>
    <th width="40%">Insurer</th>
    <th width="10%">Amount Receivable</th>
    </tr>';

    $today_date = date("Y-m-d");
    if($insurer != '') {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE ii.insurerid='$insurer' AND ii.invoiceid = i.invoiceid AND (ii.paid_date > '$as_at_date' OR IFNULL(ii.`paid`,'')!='Yes') AND ii.ui_invoiceid IS NOT NULL ORDER BY ii.invoiceid");
    } else if($invoice_no != '') {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE (ii.paid_date > '$as_at_date' OR IFNULL(ii.`paid`,'')!='Yes') AND ii.invoiceid = i.invoiceid AND i.invoiceid='$invoice_no' AND ii.ui_invoiceid IS NOT NULL ORDER BY ii.invoiceid");
    } else if($ui_no != '') {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE (ii.paid_date > '$as_at_date' OR IFNULL(ii.`paid`,'')!='Yes') AND ii.invoiceid = i.invoiceid AND ii.ui_invoiceid='$ui_no' AND ii.ui_invoiceid IS NOT NULL ORDER BY ii.invoiceid");
    } else if($ui_total > 0) {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE (ii.paid_date > '$as_at_date' OR IFNULL(ii.`paid`,'')!='Yes') AND ii.invoiceid = i.invoiceid AND ii.ui_invoiceid IN (SELECT `ui_invoiceid` FROM `invoice_insurer` GROUP BY `ui_invoiceid` HAVING SUM(`insurer_price`)=$ui_total) AND ii.ui_invoiceid IS NOT NULL ORDER BY ii.invoiceid");
    } else {
        //$report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE ii.invoiceid = i.invoiceid AND i.invoice_date > '$today_date' AND (ii.paid_date > '$as_at_date' OR IFNULL(ii.`paid`,'')!='Yes') AND ii.ui_invoiceid IS NOT NULL ORDER BY ii.invoiceid");
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE ii.invoiceid = i.invoiceid AND ii.paid_date <> '' AND ii.`paid` != 'Yes' AND ii.ui_invoiceid IS NOT NULL ORDER BY ii.invoiceid");
    }
    
    //return $query;exit;
    
    $amt_to_bill = 0;
    $odd_even = 0;
    
    while($row_report = mysqli_fetch_array($report_service)) {
        $bg_class = $odd_even % 2 == 0 ? '' : 'background-color:#e6e6e6;';
        
        $insurer_price = $row_report['insurer_price'];
        $invoiceid = $row_report['invoiceid'];
        $patientid = get_all_from_invoice($dbc, $invoiceid, 'patientid');
        $insurerid = rtrim($row_report['insurerid'],',');
        $each_insurance_payment = explode('#*#', $insurance_payment);
        
        $report_data .= '<tr nobr="true" style="'.$bg_class.'">';
            $report_data .= '<td>#'.$row_report['ui_invoiceid'];
            $name_of_file = '../Invoice/Download/patientunpaid_'.$row_report['ui_invoiceid'].'.pdf';
            $report_data .= '&nbsp;&nbsp;<a href="'.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a></td>';
            //$report_data .= '<td>#'.$row_report['ui_invoiceid'].'</td>';
            $report_data .= '<td>#'.$invoiceid;
            $name_of_file = '../Invoice/Download/invoice_'.$invoiceid.'.pdf';
            $report_data .= '&nbsp;&nbsp;<a href="'.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a>';
            $report_data .= ' : '.get_contact($dbc, $patientid).'</td>';
            $report_data .= '<td>'.$row_report['invoice_date'].'</td>';
            $report_data .= '<td>'.$row_report['service_date'].'</td>';
            $report_data .= '<td>'.get_all_form_contact($dbc, $insurerid, 'name').'</td>';
            $report_data .= '<td align="right">'.$insurer_price.'</td>';
        $report_data .= '</tr>';
        
        $amt_to_bill += $insurer_price;
        
        $odd_even++;
    }
    $report_data .= '<tr nobr="true">';
    $report_data .= '<td colspan="5"><b>Total</b></td><td align="right"><b>'.number_format($amt_to_bill, 2).'</b></td>';
    $report_data .= "</tr>";
    $report_data .= '</table>';

    return $report_data;
}
?>
