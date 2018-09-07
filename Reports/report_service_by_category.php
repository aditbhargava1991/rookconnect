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

    DEFINE('REPORT_LOGO', get_config($dbc, 'report_logo'));
    DEFINE('REPORT_HEADER', html_entity_decode(get_config($dbc, 'report_header')));
    DEFINE('REPORT_FOOTER', html_entity_decode(get_config($dbc, 'report_footer')));
    DEFINE('START_DATE', $starttimepdf);
    DEFINE('END_DATE', $endtimepdf);

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
            $footer_text = 'Services by Category From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : Displays Total # of Appointments and Total Sales by each service category.";
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

    $html .= report_daily_validation($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/patient_unpaid_'.$today_date.'.pdf', 'F');
     track_download($dbc, 'report_service_by_category', 0, WEBSITE_URL.'/Reports/Download/patient_unpaid_'.$today_date.'.pdf', 'Services by Category Report');
   ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/patient_unpaid_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
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
        <br>
        <a href='report_service_by_category.php?type=Daily'><button type="button" class="btn brand-btn mobile-block active_tab" >By Category</button></a>&nbsp;&nbsp;
        <a href='report_service_by_name.php?type=Daily'><button type="button" class="btn brand-btn mobile-block" >By Name</button></a>&nbsp;&nbsp;

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Displays Total # of Appointments and Total Sales by each service category.</div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
            }

            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-01');
            }

            if($endtime == 0000-00-00) {
                $endtime = date('Y-m-t');
            }
            ?>
            <center><div class="form-group">
                From: <input name="starttime" type="text" class="datepicker" value="<?php echo $starttime; ?>">
                &nbsp;&nbsp;&nbsp;
                Until: <input name="endtime" type="text" class="datepicker" value="<?php echo $endtime; ?>">
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                //echo '<a href="report_referral.php?referral=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a></h4><br>';

                echo report_daily_validation($dbc, $starttime, $endtime, '', '', '');
            ?>



        </form>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>

<?php
function report_daily_validation($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style) {

    $report_data = '';

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th>Service Category</th>
    <th>Qty</th>
    <th>Total $</th>';
    $report_data .= "</tr>";

        $report_validation = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT group_concat(`serviceid` separator ',') as `all_serviceid`, group_concat(`fee` separator ',') as `all_fee` FROM invoice WHERE serviceid IS NOT NULL AND (service_date >= '".$starttime."' AND service_date <= '".$endtime."')"));

        $serviceid = explode(',', $report_validation['all_serviceid']);
        $fee = explode(',', $report_validation['all_fee']);

        $serviceid = array_filter($serviceid);
        $fee = array_filter($fee);

        $sorted_arr1 = [];
        foreach ($serviceid as $skey => $svalue) {
            $report_s = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT category FROM services WHERE serviceid = '$svalue'"));
            $sorted_arr1[$skey] = $report_s['category'];
        }

        // Services
        $total_base_inv = 0;
        $total_appt = 0;
        if($report_validation['all_serviceid'] != '') {
            asort($sorted_arr1);

            $sorted_arr2 = [];
            foreach($sorted_arr1 as $key=>$val) {
              array_push($sorted_arr2, $fee[$key]);
            }
            $combined = combineStringArrayWithDuplicates($sorted_arr1, $sorted_arr2);

            foreach ($combined as $key => $value) {
                $key_invid_qty = explode(':', $key);
                $cat = $key_invid_qty[0];
                $report_data .= '<tr nobr="true">';
                $base_pay_inv_perc = $base_pay[1];

                $report_data .= '<td>'.$cat.'</td>';

                $inv_pay = ($base_pay_inv_perc/100)*$value;
                $report_data .= '<td>'.$key_invid_qty[1].'</td>';
                $report_data .= '<td>'.number_format($value, 2).'</td>';
                $report_data .= '</tr>';
                $total_base_inv += $value;
                $total_appt += $key_invid_qty[1];
            }
        }

        $report_data .= '<tr nobr="true">';
        $report_data .= '<td><b>Total</b></td><td><b>'.$total_appt.'</b></td>';
        $report_data .= '<td><b>$' . number_format($total_base_inv, 2) . '</b></td>';
        $report_data .= "</tr>";
        $report_data .= '</table><br>';

        return $report_data;
}

function combineStringArrayWithDuplicates ($keys, $values) {
    $total_array = sizeof($keys);
    $iter = 0;
    $key_old = 0;
    $fee = 0;
    $m = 0;
    foreach ($keys as $key) {
        if($iter == 0) {
            $fee += $values[$iter];
            $key_old = $key;

        } else if($key != $key_old && $iter != 0) {
            $combined[$key_old.':'.$m] = $fee;
            $m = 0;
            $fee = 0;
            $key_old = $key;
            $fee += $values[$iter];
        } else {
            $fee += $values[$iter];
        }
        $m++;
        $iter++;
    }
    if($iter == $total_array) {
        $combined[$key_old.':'.$m] = $fee;
    }
    return $combined;
}