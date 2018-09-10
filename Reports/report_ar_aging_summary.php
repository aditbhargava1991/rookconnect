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
    $endtimepdf = $_POST['endtimepdf'];
    $as_at_datepdf = $_POST['as_at_datepdf'];

    DEFINE('START_DATE', $starttimepdf);
    DEFINE('END_DATE', $endtimepdf);
    DEFINE('AS_AT_DATE', $as_at_datepdf);
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
            $footer_text = 'A/R Aging Summary as at '.AS_AT_DATE.' Including Invoices '.(START_DATE > '0000-00-00' ? ' From <b>'.START_DATE.'</b> Until <b>'.END_DATE.'</b>' : 'Until <b>'.END_DATE.'</b>');
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : This displays how much has been Invoiced, Paid and is Due from each Customer and how old it is (Current [under 30 days], 30-59 days, 60-89 days, 90-119 days, 120 + days).";
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

    $html .= report_receivables($dbc, $starttimepdf, $endtimepdf, $as_at_datepdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/ar_aging_summary_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_ar_aging_summary', 0, WEBSITE_URL.'/Reports/Download/ar_aging_summary_'.$today_date.'.pdf', 'A/R Aging Summary Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/ar_aging_summary_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $as_at_date = $as_at_datepdf;
    } ?>


<div id="invoice_div">
        <!--
        <br>
        <a href='report_account_receivable.php'><button type="button" class="btn brand-btn mobile-block" >Customer Balance Summary</button></a>&nbsp;&nbsp;
        <a href='report_customer_balance_detail.php'><button type="button" class="btn brand-btn mobile-block" >Customer Balance by Invoice</button></a>&nbsp;&nbsp;
        <a href='report_ar_aging_summary.php'><button type="button" class="btn brand-btn mobile-block active_tab" >A/R Aging Summary</button></a>&nbsp;&nbsp;
        <a href='report_collections_report.php'><button type="button" class="btn brand-btn mobile-block" >Collections Report by Customer</button></a>&nbsp;&nbsp;
        <a href='report_invoice_list.php'><button type="button" class="btn brand-btn mobile-block" >Invoice List</button></a>&nbsp;&nbsp;
        -->

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            This displays how much has been Invoiced, Paid and is Due from each Customer and how old it is (Current [under 30 days], 30-59 days, 60-89 days, 90-119 days, 120 + days) relative to the Report As At date. Clicking the customer name will redirect you to their Profile/Injury/Account dashboard. Clicking the insurer name will redirect you to their Profile dashboard. Clicking the outstanding $ amount will redirect you to their Accounts Receivable dashboard, where payment can be applied to the outstanding invoice.</div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
				$as_at_date = $_POST['as_at'];
            }

            if(!empty($_GET['from'])) {
                $starttime = $_GET['from'];
            } /* else if($starttime == 0000-00-00) {
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
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="as_at_datepdf" value="<?php echo $as_at_date; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                echo report_receivables($dbc, $starttime, $endtime, $as_at_date, '', '', '');

                if(!empty($_GET['from'])) {
                    echo '<a href="'.WEBSITE_URL.'/Reports/report_daily_sales_summary.php?from='.$_GET['from'].'&to='.$_GET['to'].'" class="btn brand-btn">Back</a>';
                }

            ?>

        </form>
</div>


<?php
function report_receivables($dbc, $starttime, $endtime, $as_at_date, $table_style, $table_row_style, $grand_total_style) {
	$report_data = "<h3>Accounts Receivable Summary As At ".date('Y-m-d',strtotime($as_at_date))."</h3>";

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="32%">Customer</th>
    <th width="10%">Invoiced</th>
    <th width="10%">Paid</th>
    <th width="10%">Total Due</th>
    <th width="10%">Current</th>
    <th width="7%">30-59</th>
    <th width="7%">60-89</th>
    <th width="7%">90-119</th>
    <th width="7%">120+</th>
    </tr>';
    
    $ar_types = "'On Account', 'Net 30', 'Net 30 Days', 'Net 60', 'Net 60 Days', 'Net 90', 'Net 90 Days', 'Net 120', 'Net 120 Days', ''";

    $report_service = mysqli_query($dbc,"SELECT DISTINCT(patientid) FROM invoice_patient WHERE (paid_date > '$as_at_date' OR `paid` IN ($ar_types)) AND (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') ORDER BY patientid");

    $total1 = 0;
    $total2 = 0;
    $total3 = 0;
    $total4 = 0;
    $total5 = 0;
    $total6 = 0;
    $total7 = 0;
    $total8 = 0;
    
    $odd_even = 0;
    
    while($row_report = mysqli_fetch_array($report_service)) {
        $bg_class = $odd_even % 2 == 0 ? '' : 'background-color:#e6e6e6;';

        $patientid = $row_report['patientid'];

        $ti = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_payment` FROM invoice_patient WHERE patientid='$patientid' AND (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."')"));
        $total_invoiced = $ti['all_payment'];

        $tp = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_payment` FROM invoice_patient WHERE patientid='$patientid' AND paid_date <= '$as_at_date' AND IFNULL(`paid`,'') NOT IN ('On Account','') AND (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."')"));
        $total_paid = $tp['all_payment'];

        $td = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_payment` FROM invoice_patient WHERE patientid='$patientid' AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ('On Account','')) AND (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."')"));
        $total_due = $td['all_payment'];

        $today_date = date('Y-m-d');
        $last29 = date('Y-m-d', strtotime($as_at_date.' - 29 days'));
        $last30 = date('Y-m-d', strtotime($as_at_date.' - 30 days'));
        $last59 = date('Y-m-d', strtotime($as_at_date.' - 59 days'));
        $last60 = date('Y-m-d', strtotime($as_at_date.' - 60 days'));
        $last89 = date('Y-m-d', strtotime($as_at_date.' - 89 days'));
        $last90 = date('Y-m-d', strtotime($as_at_date.' - 90 days'));
        $last119 = date('Y-m-d', strtotime($as_at_date.' - 119 days'));
        $last120 = date('Y-m-d', strtotime($as_at_date.' - 120 days'));

        $total_30 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND DATE(invoice_date) >= '".$last29."' AND patientid = '$patientid' AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
        $total_last30 = $total_30['all_payment'];

        $total_3059 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND (DATE(invoice_date) >= '".$last59."' AND DATE(invoice_date) < '".$last29."') AND  patientid='$patientid' AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
        $total_last3059 = $total_3059['all_payment'];

        $total_6089 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND (DATE(invoice_date) >= '".$last89."' AND DATE(invoice_date) < '".$last59."') AND patientid='$patientid' AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
        $total_last6089 = $total_6089['all_payment'];

        $total_90119 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND (DATE(invoice_date) >= '".$last119."' AND DATE(invoice_date) < '".$last89."') AND patientid='$patientid' AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
        $total_last90119 = $total_90119['all_payment'];

        $total_120 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND (DATE(invoice_date) < '".$last119."') AND patientid='$patientid' AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
        $total_last120 = $total_120['all_payment'];

        $report_data .= '<tr nobr="true" style="'.$bg_class.'">';
        $report_data .= '<td><a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/'.CONTACTS_TILE.'/contacts_inbox.php?edit='.$row_report['patientid'].'\', \'auto\', false, true, $(\'#invoice_div\').outerHeight()+20); return false;">'.$patientid.' : '.get_contact($dbc, $patientid).'</a></td>';
        $report_data .= '<td align="right">'.(is_numeric($total_invoiced) ? '$'.number_format($total_invoiced,2) : '-').'</td>';
        $report_data .= '<td align="right">'.(is_numeric($total_paid) ? '$'.number_format($total_paid,2) : '-').'</td>';
        $report_data .= '<td align="right">'.(is_numeric($total_due) ? '$'.number_format($total_due,2) : '-').'</td>';

        if (floatval($total_last30) != 0) {
            //$report_data .= '<td><a href="../Account Receivables/patient_account_receivables.php?from='.$last29.'&until='.$today_date.'&patientid='.$patientid.'&report=ar_aging">$'.$total_last30.'</a></td>';
            $report_data .= '<td align="right">$'.$total_last30.'</td>';
        } else {
            $report_data .= '<td align="right">$0.00</td>';
        }

        if (floatval($total_last3059) != 0) {
            //$report_data .= '<td><a href="../Account Receivables/patient_account_receivables.php?from='.$last59.'&until='.$last30.'&patientid='.$patientid.'&report=ar_aging">$'.$total_last3059.'</a></td>';
            $report_data .= '<td align="right">$'.$total_last3059.'</td>';
        } else {
            $report_data .= '<td align="right">$0.00</td>';
        }

        if (floatval($total_last6089) != 0) {
            //$report_data .= '<td><a href="../Account Receivables/patient_account_receivables.php?from='.$last89.'&until='.$last60.'&patientid='.$patientid.'&report=ar_aging">$'.$total_last6089.'</a></td>';
            $report_data .= '<td align="right">$'.$total_last6089.'</td>';
        } else {
            $report_data .= '<td align="right">$0.00</td>';
        }

        if (floatval($total_last90119) != 0) {
            //$report_data .= '<td><a href="../Account Receivables/patient_account_receivables.php?from='.$last119.'&until='.$last90.'&patientid='.$patientid.'&report=ar_aging">$'.$total_last90119.'</a></td>';
            $report_data .= '<td align="right">$'.$total_last90119.'</td>';
        } else {
            $report_data .= '<td align="right">$0.00</td>';
        }

        if (floatval($total_last120) != 0) {
            //$report_data .= '<td><a href="../Account Receivables/patient_account_receivables.php?from=2016-01-01&until='.$last120.'&patientid='.$patientid.'&report=ar_aging">$'.$total_last120.'</a></td>';
            $report_data .= '<td align="right">$'.$total_last120.'</td>';
        } else {
            $report_data .= '<td align="right">$0.00</td>';
        }

        //$report_data .= '<td>$'.$total_last30.'</td>';
        //$report_data .= '<td>$'.$total_last3059.'</td>';
        //$report_data .= '<td>$'.$total_last6089.'</td>';
        //$report_data .= '<td>$'.$total_last90119.'</td>';
        //$report_data .= '<td>$'.$total_last120.'</td>';
        $report_data .= '</tr>';

        $total1 += $total_invoiced;
        $total2 += $total_paid;
        $total3 += $total_due;
        $total4 += $total_last30;
        $total5 += $total_last3059;
        $total6 += $total_last6089;
        $total7 += $total_last90119;
        $total8 += $total_last120;
        
        $odd_even++;
    }

    $report_data .= '<tr nobr="true">';
    $report_data .= '<td><b>Total</b></td><td align="right"><b>$'.number_format($total1, 2).'</b></td><td align="right"><b>$'.number_format($total2, 2).'</b></td><td align="right"><b>$'.number_format($total3, 2).'</b></td><td align="right"><b>$'.number_format($total4, 2).'</b></td><td align="right"><b>$'.number_format($total5, 2).'</b></td><td align="right"><b>$'.number_format($total6, 2).'</b></td><td align="right"><b>$'.number_format($total7, 2).'</b></td><td align="right"><b>$'.number_format($total8, 2).'</b></td>';
    $report_data .= "</tr>";
    $report_data .= '</table><br><br><br>';

    /* $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="32%">Insurer</th>
    <th width="10%">Invoiced</th>
    <th width="10%">Paid</th>
    <th width="10%">Total Due</th>
    <th width="10%">Current</th>
    <th width="7%">30-59</th>
    <th width="7%">60-89</th>
    <th width="7%">90-119</th>
    <th width="7%">120+</th>
    </tr>'; */

    //$report_service = mysqli_query($dbc,"SELECT invoiceid, insurerid, insurance_payment, service_date, invoice_date FROM invoice WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND paid='Waiting on Insurer' AND insurance_payment != '#*#' ORDER BY insurerid");

    //$report_service = mysqli_query($dbc,"SELECT * FROM invoice_insurer WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND paid!='Yes' ORDER BY invoiceid");

	/* $report_service = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `name` FROM `contacts` WHERE `contactid` IN (SELECT `insurerid` FROM `invoice_insurer`)"),MYSQLI_ASSOC));

    $total1 = 0;
    $total2 = 0;
    $total3 = 0;
    $total4 = 0;
    $total5 = 0;
    $total6 = 0;
    $total7 = 0;
    $total8 = 0;
	foreach($report_service as $insurerid) {
	    $total_invoiced = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(insurer_price) AS total_invoiced FROM invoice_insurer WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND insurerid = '$insurerid'"));
	    $total_paid = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(insurer_price) AS total_paid FROM invoice_insurer WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND insurerid = '$insurerid' AND paid_date <= '$as_at_date' AND IFNULL(`paid`,'')='Yes'"));
	    $total_due = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(insurer_price) AS total_due FROM invoice_insurer WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND insurerid = '$insurerid' AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'')!='Yes')"));

        $today_date = date('Y-m-d');
        $last29 = date('Y-m-d', strtotime($as_at_date.' - 29 days'));
        $last30 = date('Y-m-d', strtotime($as_at_date.' - 30 days'));
        $last59 = date('Y-m-d', strtotime($as_at_date.' - 59 days'));
        $last60 = date('Y-m-d', strtotime($as_at_date.' - 60 days'));
        $last89 = date('Y-m-d', strtotime($as_at_date.' - 89 days'));
        $last90 = date('Y-m-d', strtotime($as_at_date.' - 90 days'));
        $last119 = date('Y-m-d', strtotime($as_at_date.' - 119 days'));
        $last120 = date('Y-m-d', strtotime($as_at_date.' - 120 days'));

        $total_last30 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(insurer_price) AS total_last30 FROM invoice_insurer WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND (DATE(invoice_date) >= '".$last29."' AND DATE(invoice_date) <= '".$today_date."') AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'')!='Yes') AND insurerid = '$insurerid'"));

        $total_last3059 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(insurer_price) AS total_last3059 FROM invoice_insurer WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND (DATE(invoice_date) >= '".$last59."' AND DATE(invoice_date) <= '".$last30."') AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'')!='Yes') AND insurerid = '$insurerid'"));

        $total_last6089 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(insurer_price) AS total_last6089 FROM invoice_insurer WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND (DATE(invoice_date) >= '".$last89."' AND DATE(invoice_date) <= '".$last60."') AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'')!='Yes') AND insurerid = '$insurerid'"));

        $total_last90119 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(insurer_price) AS total_last90119 FROM invoice_insurer WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND (DATE(invoice_date) >= '".$last119."' AND DATE(invoice_date) <= '".$last90."') AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'')!='Yes') AND insurerid = '$insurerid'"));

        $total_last120 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(insurer_price) AS total_last120 FROM invoice_insurer WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND (DATE(invoice_date) <= '".$last120."') AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'')!='Yes') AND insurerid = '$insurerid'"));

        $report_data .= '<tr nobr="true">';
        $report_data .= '<td><a href="../Contacts/add_contacts.php?category=Insurer&contactid='.$insurerid.'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">'.get_all_form_contact($dbc, $insurerid, 'name').'</a></td>';
        $report_data .= '<td>'.(is_numeric($total_invoiced['total_invoiced']) ? '$'.number_format($total_invoiced['total_invoiced'],2) : '-').'</td>';
        $report_data .= '<td>'.(is_numeric($total_paid['total_paid']) ? '$'.number_format($total_paid['total_paid'],2) : '-').'</td>';
        $report_data .= '<td>'.(is_numeric($total_due['total_due']) ? '$'.number_format($total_due['total_due'],2) : '-').'</td>';


        if (floatval($total_last30['total_last30']) != 0) {
            $report_data .= '<td><a href="../Account Receivables/insurer_account_receivables.php?from='.$last29.'&until='.$today_date.'&insurerid='.$insurerid.'&report=ar_aging">$'.$total_last30['total_last30'].'</a></td>';
        } else {
            $report_data .= '<td>$0.00</td>';
        }

        if (floatval($total_last3059['total_last3059']) != 0) {
        $report_data .= '<td><a href="../Account Receivables/insurer_account_receivables.php?from='.$last59.'&until='.$last30.'&insurerid='.$insurerid.'&report=ar_aging">$'.$total_last3059['total_last3059'].'</a></td>';
        } else {
            $report_data .= '<td>$0.00</td>';
        }

        if (floatval($total_last6089['total_last6089']) != 0) {
        $report_data .= '<td><a href="../Account Receivables/insurer_account_receivables.php?from='.$last89.'&until='.$last60.'&insurerid='.$insurerid.'&report=ar_aging">$'.$total_last6089['total_last6089'].'</a></td>';
        } else {
            $report_data .= '<td>$0.00</td>';
        }

        if (floatval($total_last90119['total_last90119']) != 0) {
        $report_data .= '<td><a href="../Account Receivables/insurer_account_receivables.php?from='.$last119.'&until='.$last90.'&insurerid='.$insurerid.'&report=ar_aging">$'.$total_last90119['total_last90119'].'</a></td>';
        } else {
            $report_data .= '<td>$0.00</td>';
        }

        if (floatval($total_last120['total_last120']) != 0) {
        $report_data .= '<td><a href="../Account Receivables/insurer_account_receivables.php?from=2016-01-01&until='.$last120.'&insurerid='.$insurerid.'&report=ar_aging">$'.$total_last120['total_last120'].'</a></td>';
        } else {
            $report_data .= '<td>$0.00</td>';
        }

        //$report_data .= '<td>$'.$total_last3059['total_last3059'].'</td>';
        //$report_data .= '<td>$'.$total_last6089['total_last6089'].'</td>';
        //$report_data .= '<td>$'.$total_last90119['total_last90119'].'</td>';
        //$report_data .= '<td>$'.$total_last120['total_last120'].'</td>';
        $report_data .= '</tr>';

        $total1 += $total_invoiced['total_invoiced'];
        $total2 += $total_paid['total_paid'];
        $total3 += $total_due['total_due'];
        $total4 += $total_last30['total_last30'];
        $total5 += $total_last3059['total_last3059'];
        $total6 += $total_last6089['total_last6089'];
        $total7 += $total_last90119['total_last90119'];
        $total8 += $total_last120['total_last120'];
    }

    $report_data .= '<tr nobr="true">';
    $report_data .= '<td><b>Total</b></td><td><b>$'.number_format($total1, 2).'</b></td><td><b>$'.number_format($total2, 2).'</b></td><td><b>$'.number_format($total3, 2).'</b></td><td><b>$'.number_format($total4, 2).'</b></td><td><b>$'.number_format($total5, 2).'</b></td><td><b>$'.number_format($total6, 2).'</b></td><td><b>$'.number_format($total7, 2).'</b></td><td><b>$'.number_format($total8, 2).'</b></td>';
    $report_data .= "</tr>";
    $report_data .= '</table><br>'; */

    return $report_data;
}

?>