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

    DEFINE('START_DATE', $starttimepdf);
    DEFINE('END_DATE', $endtimepdf);
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
            $footer_text = 'View Receivables From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
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

    $html .= report_receivables($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/receivables_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_receivables_patient_paid_summary', 0, WEBSITE_URL.'/Reports/Download/receivables_'.$today_date.'.pdf', 'View Receivables Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/receivables_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
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

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            /*
            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
            }
            */

            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-d');
            }

            if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            }
            ?>
            <!--
            <div class="form-group">
                <label for="site_name" class="col-sm-4 control-label">From:</label>
                <div class="col-sm-8">
                    <input name="starttime" type="text" class="datepicker" value="<?php echo $starttime; ?>">
                </div>
            </div>

            <div class="form-group until">
                <label for="site_name" class="col-sm-4 control-label">Until:</label>
                <div class="col-sm-8" style="width:auto">
                    <input name="endtime" type="text" class="datepicker" value="<?php echo $endtime; ?>"></p>
                </div>
            </div>

            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
            -->
            <br>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                echo report_receivables($dbc, $starttime, $endtime, '', '', '');
            ?>

        </form>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>

<?php
function report_receivables($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style) {

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="32%">Patient</th>
    <th width="10%">Invoiced</th>
    <th width="10%">Paid</th>
    <th width="10%">Total Due</th>
    <th width="10%">Current</th>
    <th width="7%">30-59</th>
    <th width="7%">60-89</th>
    <th width="7%">90-119</th>
    <th width="7%">120+</th>
    </tr>';

    $report_service = mysqli_query($dbc,"SELECT DISTINCT(patientid) FROM invoice_patient WHERE paid != 'On Account' ORDER BY patientid");

    $total1 = 0;
    $total2 = 0;
    $total3 = 0;
    $total4 = 0;
    $total5 = 0;
    $total6 = 0;
    $total7 = 0;
    $total8 = 0;
    while($row_report = mysqli_fetch_array($report_service)) {

        $patientid = $row_report['patientid'];

        $ti = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_payment` FROM invoice_patient WHERE patientid='$patientid'"));
        $total_invoiced = $ti['all_payment'];

        $tp = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_payment` FROM invoice_patient WHERE patientid='$patientid' AND paid != 'On Account'"));
        $total_paid = $tp['all_payment'];

        $td = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_payment` FROM invoice_patient WHERE patientid='$patientid' AND paid = 'On Account'"));
        $total_due = $td['all_payment'];

        $last30 = date('Y-m-d', strtotime('today - 30 days'));
        $last60 = date('Y-m-d', strtotime('today - 60 days'));
        $last90 = date('Y-m-d', strtotime('today - 90 days'));
        $last120 = date('Y-m-d', strtotime('today - 120 days'));
        $today_date = date('Y-m-d');

        $total_30 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$last30."' AND DATE(invoice_date) <= '".$today_date."') AND patientid='$patientid' AND paid = 'On Account'"));
        $total_last30 = $total_30['all_payment'];

        $total_3059 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$last60."' AND DATE(invoice_date) < '".$last30."') AND patientid='$patientid' AND paid = 'On Account'"));
        $total_last3059 = $total_3059['all_payment'];

        $total_6089 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$last90."' AND DATE(invoice_date) < '".$last60."') AND patientid='$patientid' AND paid = 'On Account'"));
        $total_last6089 = $total_6089['all_payment'];

        $total_90119 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$last120."' AND DATE(invoice_date) < '".$last90."') AND patientid='$patientid' AND paid = 'On Account'"));
        $total_last90119 = $total_90119['all_payment'];

        $total_120 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) < '".$last120."') AND patientid='$patientid' AND paid = 'On Account'"));
        $total_last120 = $total_120['all_payment'];

        $report_data .= '<tr nobr="true">';
        $report_data .= '<td>'.$patientid.' : '.get_contact($dbc, $patientid).'</td>';
        $report_data .= '<td>$'.$total_invoiced.'</td>';
        $report_data .= '<td>$'.$total_paid.'</td>';
        $report_data .= '<td>$'.$total_due.'</td>';
        $report_data .= '<td>$'.$total_last30.'</td>';
        $report_data .= '<td>$'.$total_last3059.'</td>';
        $report_data .= '<td>$'.$total_last6089.'</td>';
        $report_data .= '<td>$'.$total_last90119.'</td>';
        $report_data .= '<td>$'.$total_last120.'</td>';
        $report_data .= '</tr>';

        $total1 += $total_invoiced;
        $total2 += $total_paid;
        $total3 += $total_due;
        $total4 += $total_last30;
        $total5 += $total_last3059;
        $total6 += $total_last6089;
        $total7 += $total_last90119;
        $total8 += $total_last120;
    }

    $report_data .= '<tr nobr="true">';
    $report_data .= '<td>Total</td><td>$'.number_format($total1, 2).'</td><td>$'.number_format($total2, 2).'</td><td>$'.number_format($total3, 2).'</td><td>$'.number_format($total4, 2).'</td><td>$'.number_format($total5, 2).'</td><td>$'.number_format($total6, 2).'</td><td>$'.number_format($total7, 2).'</td><td>$'.number_format($total8, 2).'</td>';
    $report_data .= "</tr>";
    $report_data .= '</table><br>';

    return $report_data;
}

?>