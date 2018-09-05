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
    DEFINE('PATIENT', get_contact($dbc, $patientpdf));
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
            $footer_text = 'Sales History by Customer for : <b>'.PATIENT.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : This report displays all sales transactions and amounts for the selected customer.";
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

    $html .= report_daily_validation($dbc, $starttimepdf, $invoice_nopdf, $patientpdf, 'padding:3px; border:1px solid black;', '', '', $paid_datepdf, '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/sales_by_customer_detail_'.$today_date.'.pdf', 'F');

    track_download($dbc, 'report_sales_by_customer_detail', 0, WEBSITE_URL.'/Reports/Download/sales_by_customer_detail_'.$today_date.'.pdf', 'Sales History by Customer Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/sales_by_customer_detail_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $invoice_no = $invoice_nopdf;
    $patient = $patientpdf;
    $paid_date = $paid_datepdf;
    } ?>

<div id="report_div">
        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            This report displays all sales transactions and amounts for the selected customer.</div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            $starttime = '';
            $invoice_no = '';
            $paid_date = '';

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
                <!--
                Invoice# <input name="invoice_no" type="text" class="form-control1" value="<?php echo $invoice_no; ?>">
                &nbsp;&nbsp;&nbsp;
                Invoice Date: <input name="starttime" type="text" class="datepicker" value="<?php echo $starttime; ?>">
                &nbsp;&nbsp;&nbsp;
                Paid Date: <input name="paid_date" type="text" class="datepicker" value="<?php echo $paid_date; ?>">
                &nbsp;&nbsp;&nbsp;
                -->
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Customer:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select a Customer..." name="patient" class="chosen-select-deselect form-control1" width="380">
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

                if($patient != '') {
                    echo report_daily_validation($dbc, $starttime, $invoice_no, $patient, '', '', '', $paid_date, $starttime_paid_date, $endtime_paid_date);
                }

                if(!empty($_GET['from'])) {
                    echo '<a href="'.WEBSITE_URL.'/Reports/report_daily_sales_summary.php?from='.$_GET['from'].'&to='.$_GET['to'].'" class="btn brand-btn">Back</a>';
                }
            ?>



</div>

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
    <th width="18%">Customer</th>
    <th width="20%">Payment By</th>
    <th width="15%">Amount Paid</th>
    </tr>';

    $amt_to_bill = 0;
    while($row_report = mysqli_fetch_array($report_service)) {
        $patient_price = $row_report['patient_price'];
        $invoiceid = $row_report['invoiceid'];

        $report_data .= '<tr nobr="true">';
        $report_data .= '<td>#'.$invoiceid;
        $name_of_file = '/Invoice/Download/invoice_'.$invoiceid.'.pdf';
		if(file_exists('..'.$name_of_file)) {
			$report_data .= '&nbsp;&nbsp;<a href="'.WEBSITE_URL.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a>';
		}
		$report_data .= '</td>';

        $report_data .= '<td>'.$row_report['invoice_date'].'</td>';
        $report_data .= '<td>'.$row_report['paid_date'].'</td>';
		$report_data .= '<td><a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/'.CONTACTS_TILE.'/contacts_inbox.php?edit='.$row_report['patientid'].'\', \'auto\', false, true, $(\'#report_div\').outerHeight()+20); return false;">'.get_contact($dbc, $row_report['patientid']). '</a></td>';
        $report_data .= '<td>'.$row_report['paid'].'</td>';
        $report_data .= '<td>$'.$patient_price.'</td>';

        $report_data .= '</tr>';
        $amt_to_bill += $patient_price;
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

?>
<script>
$('document').ready(function() {
    var tables = $('table');

    tables.map(function(idx, table) {
        var rows = $(table).find('tbody > tr');
        rows.map(function(idx, row){
            if(idx%2 == 0) {
                $(row).css('background-color', '#e6e6e6');
            }
        })
    })
})
</script>