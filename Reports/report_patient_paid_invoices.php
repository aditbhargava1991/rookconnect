<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['printpdf'])) {
    $starttimepdf = $_POST['starttimepdf'];
    $invoice_nopdf = $_POST['invoice_nopdf'];
    $patientpdf = $_POST['patientpdf'];
    $paid_datepdf = $_POST['paid_datepdf'];

    //DEFINE('START_DATE', $starttimepdf);
    //DEFINE('INVOICE_NO', $invoice_nopdf);
    //DEFINE('PATIENT', $starttimepdf);
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
            $footer_text = 'Paid Invoices';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);
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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);

    $html .= report_daily_validation($dbc, $starttimepdf, $invoice_nopdf, $patientpdf, 'padding:3px; border:1px solid black;', '', '', $paid_datepdf, '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/patient_unpaid_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_patient_paid_invoices', 0, WEBSITE_URL.'/Reports/Download/patient_unpaid_'.$today_date.'.pdf', 'Paid Invoices Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/patient_unpaid_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $invoice_no = $invoice_nopdf;
    $patient = $patientpdf;
    $paid_date = $paid_datepdf;
    } ?>

<script type="text/javascript">

</script>
</head>
<body>
<?php include_once ('../navigation.php');
?>

<div class="container triple-pad-bottom">
    <div class="row">
        <div class="col-md-12">

        <?php echo reports_tiles($dbc);  ?>

        <br><br>
        <a href='report_patient_unpaid_invoices.php?type=sales'><button type="button" class="btn brand-btn mobile-block" >Unpaid Invoices</button></a>&nbsp;&nbsp;
        <a href='report_patient_paid_invoices.php?type=sales'><button type="button" class="btn brand-btn mobile-block active_tab" >Paid Invoices</button></a>&nbsp;&nbsp;

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $invoice_no = $_POST['invoice_no'];
                $patient = $_POST['patient'];
                $paid_date = $_POST['paid_date'];
            } else if((!empty($_GET['from']))) {
                $starttime_paid_date = $_GET['from'];
                $endtime_paid_date = $_GET['to'];
            }
            ?>
            <center><div class="form-group">
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Invoice #:</label>
					<div class="col-sm-8"><input name="invoice_no" type="text" class="form-control" value="<?php echo $invoice_no; ?>"></div>
				</div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Invoice Date:</label>
					<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Paid Date:</label>
					<div class="col-sm-8"><input name="paid_date" type="text" class="datepicker form-control" value="<?php echo $paid_date; ?>"></div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Customer:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select a Staff..." name="patient" class="chosen-select-deselect form-control1" width="380">
							<option value="">Select All</option>
							<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE contactid IN (SELECT patientid FROM invoice_patient WHERE paid != 'On Account' AND paid != '' AND paid IS NOT NULL)"),MYSQLI_ASSOC));
							foreach($query as $rowid) {
								echo "<option ".($rowid == $patient ? 'selected' : '')." value='$rowid'>".get_contact($dbc, $rowid)."</option>";
							} ?>
						</select>
					</div>
                </div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="paid_datepdf" value="<?php echo $paid_date; ?>">
            <input type="hidden" name="invoice_nopdf" value="<?php echo $invoice_no; ?>">
            <input type="hidden" name="patientpdf" value="<?php echo $patient; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                //echo '<a href="report_referral.php?referral=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a></h4><br>';

                echo report_daily_validation($dbc, $starttime, $invoice_no, $patient, '', '', '', $paid_date, $starttime_paid_date, $endtime_paid_date);

                if(!empty($_GET['from'])) {
                    echo '<a href="'.WEBSITE_URL.'/Reports/report_daily_sales_summary.php?from='.$_GET['from'].'&to='.$_GET['to'].'" class="btn brand-btn">Back</a>';
                }
            ?>



        </form>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>

<?php
function report_daily_validation($dbc, $starttime, $invoice_no, $patient, $table_style, $table_row_style, $grand_total_style, $paid_date, $starttime_paid_date, $endtime_paid_date) {

    $report_data = '';

    $rowsPerPage = 25;
    $pageNum = 1;

    if(isset($_GET['page'])) {
        $pageNum = $_GET['page'];
    }

    $offset = ($pageNum - 1) * $rowsPerPage;

    $where_query = '';
    if($starttime != '') {
        $where_query .= " AND invoice_date = '$starttime'";
    }
    if($paid_date != '') {
        $where_query .= " AND paid_date = '$paid_date'";
    }
    if($invoice_no != '') {
        $where_query .= " AND invoiceid = '$invoice_no'";
    }
    if($patient != '') {
        $where_query .= " AND patientid = '$patient'";
    }

    //$report_service = mysqli_query($dbc,"SELECT * FROM invoice_patient WHERE (paid != 'On Account' AND paid != '' AND paid IS NOT NULL) $where_query ORDER BY invoiceid DESC LIMIT $offset, $rowsPerPage");
    $query = "SELECT count(*) as numrows FROM invoice_patient WHERE (paid != 'On Account' AND paid != '' AND paid IS NOT NULL) $where_query ORDER BY invoiceid DESC";

    if($starttime_paid_date != '') {
        $report_service = mysqli_query($dbc,"SELECT * FROM invoice_patient WHERE (paid != 'On Account' AND paid != '' AND paid IS NOT NULL) AND (paid_date >= '".$starttime_paid_date."' AND paid_date <= '".$endtime_paid_date."') ORDER BY invoiceid DESC");
    } else if($starttime == '' && $invoice_no == '' && $patient == '' && $table_style == '' && $paid_date == '') {
        $report_service = mysqli_query($dbc,"SELECT * FROM invoice_patient WHERE (paid != 'On Account' AND paid != '' AND paid IS NOT NULL) $where_query ORDER BY invoiceid DESC LIMIT $offset, $rowsPerPage");
        $query = "SELECT count(*) as numrows FROM invoice_patient WHERE (paid != 'On Account' AND paid != '' AND paid IS NOT NULL) $where_query ORDER BY invoiceid DESC";
        $report_data .= display_pagination($dbc, $query, $pageNum, $rowsPerPage);
    } else {
        $report_service = mysqli_query($dbc,"SELECT * FROM invoice_patient WHERE (paid != 'On Account' AND paid != '' AND paid IS NOT NULL) $where_query ORDER BY invoiceid DESC");
    }

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="15%">Invoice #</th>
    <th width="15%">Invoice Date</th>
    <th width="15%">Paid Date</th>
    <th width="15%">Customer</th>
    <th width="20%">Payment By</th>
    <th width="20%">Amount Paid</th>
    </tr>';

    $amt_to_bill = 0;$odd_even = 0;
    while($row_report = mysqli_fetch_array($report_service)) {
        $bg_class = $odd_even % 2 == 0 ? 'background-color:#ffffff;' : 'background-color:#e6e6e6;';
        $patient_price = $row_report['patient_price'];
        $invoiceid = $row_report['invoiceid'];

        $report_data .= '<tr nobr="true" style="'.$bg_class.'">';
        $report_data .= '<td>#'.$invoiceid.'</td>';
        $report_data .= '<td>'.$row_report['invoice_date'].'</td>';
        $report_data .= '<td>'.$row_report['paid_date'].'</td>';
        $report_data .= '<td>'.get_contact($dbc, $row_report['patientid']).'</td>';
        $report_data .= '<td>'.$row_report['paid'].'</td>';
        $report_data .= '<td>$'.$patient_price.'</td>';
        $report_data .= '</tr>';
        $amt_to_bill += $patient_price;
        $odd_even++;
    }

    $report_data .= '<tr nobr="true">';
    $report_data .= '<td><b>Total</b></td><td></td><td></td><td></td><td></td><td><b>$'.number_format($amt_to_bill, 2).'</b></td>';
    $report_data .= "</tr>";
    $report_data .= '</table><br>';

    if($starttime == '' && $invoice_no == '' && $patient == '') {
        //$report_data .= display_pagination($dbc, $query, $pageNum, $rowsPerPage);
    }

    return $report_data;
}