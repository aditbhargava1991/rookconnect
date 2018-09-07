<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if(isset($_POST['printcsv'])) {
  $starttime = $_POST['starttimepdf'];
  $endtime = $_POST['endtimepdf'];
  $as_at_date = $_POST['as_at_datepdf'];
  $file_name = "report_collection_" . date("Y-m-d_m") . '.csv';

  ob_end_clean();
  $fp = fopen('php://output','w');
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename='.$file_name);

  $report_service = mysqli_query($dbc,"SELECT * FROM invoice_patient WHERE (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ('On Account','')) AND (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND patientid = '$patient' ORDER BY invoiceid DESC");
  $data[] = 'Invoice #,Invoice Date,Customer,Days Past Due,Amount Owed (Open Balance)';
  $amt_to_bill = 0;
  while($row_report = mysqli_fetch_array($report_service)) {
      $row = '';

      $today_date = date("Y-m-d");
      $date1 = new DateTime($row_report['invoice_date']);
      $date2 = new DateTime($today_date);

      $patient_price = $row_report['patient_price'];
      $row .= $row_report['invoiceid'] . ',';
      $row .= $row_report['invoice_date'] . ',';
      $row .= get_contact($dbc, $row_report['patientid']) . ',';
      $row .= $date2->diff($date1)->format("%a");
      $row .= $patient_price;
      $amt_to_bill += $patient_price;
      $data[] = $row;
  }

  $data[] = ',,,Total,$'.number_format($amt_to_bill, 2);

  foreach ($data as $line) {
      $val = explode(",", $line);
      fputcsv($fp, $val);
  }
  exit();
}

if (isset($_POST['printpdf'])) {
    $patientpdf = $_POST['patientpdf'];
    $starttimepdf = $_POST['starttimepdf'];
    $endtimepdf = $_POST['endtimepdf'];
    $as_at_datepdf = $_POST['as_at_datepdf'];

    DEFINE('START_DATE', $starttimepdf);
    DEFINE('END_DATE', $endtimepdf);
    DEFINE('AS_AT_DATE', $as_at_datepdf);
    DEFINE('PATIENT', get_contact($dbc, $patientpdf));
    DEFINE('REPORT_LOGO', get_config($dbc, 'report_logo'));
    DEFINE('REPORT_HEADER', html_entity_decode(get_config($dbc, 'report_header')));
    DEFINE('REPORT_FOOTER', html_entity_decode(get_config($dbc, 'report_footer')));

    class MYPDF extends TCPDF {

		public function Header() {
			//$image_file = WEBSITE_URL.'/img/Clinic-Ace-Logo-Final-250px.png';
            if(REPORT_LOGO != '') {
                $image_file = 'download/'.REPORT_LOGO;
                $this->Image($image_file, 10, 10, '', 20, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);

            $this->SetFont('helvetica', '', 13);
            $footer_text = 'Collections Report by Customer for: <b>'.PATIENT.'</b> as at '.AS_AT_DATE.' Including Invoices '.(START_DATE > '0000-00-00' ? ' From <b>'.START_DATE.'</b> Until <b>'.END_DATE.'</b>' : 'Until <b>'.END_DATE.'</b>');
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : Shows overdue invoices grouped by customer. Includes the days past due, and total for each customer.";
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

    $html .= report_daily_validation($dbc, $patientpdf, $starttimepdf, $endtimepdf, $as_at_datepdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/collections_report_'.$today_date.'.pdf', 'F');

    track_download($dbc, 'report_collections_report', 0, WEBSITE_URL.'/Reports/Download/collections_report_'.$today_date.'.pdf', 'Collections Report by Customer Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/collections_report_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $patient = $patientpdf;
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $as_at_date = $as_at_datepdf;
    } ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Shows overdue invoices grouped by customer. Includes the days past due, and total for each customer.</div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if (isset($_POST['search_email_submit'])) {
                $patient = $_POST['patient'];
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
				$as_at_date = $_POST['as_at'];
            }

            if(!empty($_GET['from'])) {
                $starttime = $_GET['from'];
            } /*else if($starttime == 0000-00-00) {
				$starttime = date('Y-m-01');
			}*/

            if(!empty($_GET['to'])) {
                $endtime = $_GET['to'];
            } else if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            }

            if(!empty($_GET['as_at_date'])) {
                $as_at_date = $_GET['as_at_date'];
            } else if($as_at_date == 0000-00-00) {
                $as_at_date = date('Y-m-d');
            }

            ?>
            <center><div class="form-group">
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Report As At:</label>
					<div class="col-sm-8"><input name="as_at" type="text" class="datepicker form-control" value="<?php echo $as_at_date; ?>"></div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">From:</label>
					<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Until:</label>
					<div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
				</div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Customer:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select a Customer..." name="patient" class="chosen-select-deselect form-control" width="380">
							<option value=""></option>
							<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE contactid IN (SELECT patientid FROM invoice_patient WHERE paid = 'On Account' OR paid = '' OR paid IS NULL)"),MYSQLI_ASSOC));
							foreach($query as $rowid) {
								echo "<option ".($patient == $rowid ? 'selected' : '')." value='$rowid'>".get_contact($dbc, $rowid)."</option>";
							} ?>
						</select>
					</div>
				</div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="patientpdf" value="<?php echo $patient; ?>">
            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="as_at_datepdf" value="<?php echo $as_at_date; ?>">

            <button type="submit" name="printcsv" value="Print CSV Report" title="Print CSV Report" class="pull-right"><img title="Print CSV Report" width="15px" src="../img/csv.png"></button>
            <button type="submit" name="printpdf" style="margin-right:15px" value="Print PDF Report" title="Print PDF Report" title="Print PDF Report" class="pull-right"><img src="../img/pdf.png"></button>
            <br><br>

            <?php
                //echo '<a href="report_referral.php?referral=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a></h4><br>';

                if($patient > 0) {
                    echo report_daily_validation($dbc, $patient, $starttime, $endtime, $as_at_date, '', '', '');
                }
            ?>

        </form>

<?php
function report_daily_validation($dbc, $patient, $starttime, $endtime, $as_at_date, $table_style, $table_row_style, $grand_total_style) {
	$patient_name = get_contact($dbc, $patient);
    $report_data = '<h3>Collections Report for '.$patient_name.'</h3>';

    $report_service = mysqli_query($dbc,"SELECT * FROM invoice_patient WHERE (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ('On Account','')) AND (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND patientid = '$patient' ORDER BY invoiceid DESC");

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="15%">Invoice #</th>
    <th width="15%">Invoice Date</th>
    <th width="28%">Customer</th>
    <th width="10%">Days Past Due</th>
    <th width="30%">Amount Owed (Open Balance)</th>
    </tr>';

    $amt_to_bill = 0;
    $odd_even = 0;
    
    while($row_report = mysqli_fetch_array($report_service)) {
        $bg_class = $odd_even % 2 == 0 ? '' : 'background-color:#e6e6e6;';
        
        $patient_price = $row_report['patient_price'];
        $invoiceid = $row_report['invoiceid'];

        $today_date = date("Y-m-d");
        $date1 = new DateTime($row_report['invoice_date']);
        $date2 = new DateTime($today_date);

        $report_data .= '<tr nobr="true" style="'.$bg_class.'">';
            $report_data .= '<td>#' . $invoiceid;
            $name_of_file = '../Invoice/Download/invoice_'.$invoiceid.'.pdf';
            $report_data .= '&nbsp;&nbsp;<a href="'.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a></td>';
            $report_data .= '<td>'.$row_report['invoice_date'].'</td>';
            $report_data .= '<td><a href="../Contacts/add_contacts.php?category=Patient&contactid='.$row_report['patientid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">'.$patient_name. '</a></td>';
            $report_data .= '<td align="right">'.$date2->diff($date1)->format("%a").' Days</td>';
            $report_data .= '<td align="right">$'.$patient_price.'</td>';
        $report_data .= '</tr>';
        
        $amt_to_bill += $patient_price;
        $odd_even++;
    }

    $report_data .= '<tr nobr="true">';
    $report_data .= '<td colspan="4"><b>Total</b></td><td align="right"><b>$'.number_format($amt_to_bill, 2).'</b></td>';
    $report_data .= "</tr>";
    $report_data .= '</table><br>';

    if($starttime == '' && $invoice_no == '' && $patient == '') {
        //$report_data .= display_pagination($dbc, $query, $pageNum, $rowsPerPage);
    }

    return $report_data;
}

?>
