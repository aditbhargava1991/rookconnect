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
            $footer_text = 'Monthly Sales by Injury Type Invoice Date From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : This report displays the Total Customer Accounts Receivable, Insurer Accounts Receivable, Customer Paid Amounts, Insurer Paid Amounts, Unassigned/Error Invoices and the Total Sales for the selected date range, broken down by Injury Type (service).";
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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 60, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);

    $html .= report_sales_summary($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/monthly_sales_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_review_sales', 0, WEBSITE_URL.'/Reports/Download/monthly_sales_'.$today_date.'.pdf', 'Monthly Sales by Injury Type Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/monthly_sales_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    } ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            This report displays the Total Customer Accounts Receivable, Insurer Accounts Receivable, Customer Paid Amounts, Insurer Paid Amounts, Unassigned/Error Invoices and the Total Sales for the selected date range, broken down by Injury Type (service).</div>
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

            if(!empty($_GET['from'])) {
                $starttime = $_GET['from'];
            } else if($starttime == 0000-00-00) {
                $starttime = date('Y-m-01');
            }

            if(!empty($_GET['to'])) {
                $endtime = $_GET['to'];
            } else if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            }

            ?>
            <center><div class="form-group">
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

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                echo report_sales_summary($dbc, $starttime, $endtime, '', '', '');

                if(!empty($_GET['from'])) {
                    echo '<a href="'.WEBSITE_URL.'/Reports/report_daily_sales_summary.php?from='.$_GET['from'].'&to='.$_GET['to'].'" class="btn brand-btn">Back</a>';
                }

            ?>

        </form>

<?php
function report_sales_summary($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style) {

    //Patients Amount To Bill
    $all_to_bill_patient = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_to_bill_patient` FROM invoice_patient WHERE paid='On Account' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));
    $to_bill_patient = $all_to_bill_patient['all_to_bill_patient'];

    $result1 = mysqli_query($dbc, "SELECT injury_type, SUM(patient_price) AS total_to_bill_patient FROM invoice_patient WHERE paid='On Account' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') GROUP BY injury_type");
    $cat_to_bill_patient = '';
    while($row1 = mysqli_fetch_array($result1)) {
        if($row1['injury_type'] == '') {
            $row1['injury_type'] = 'Special/Extra<br>(Reports/Non Patients/Patients without injury)';
        }
        $cat_to_bill_patient .= '&nbsp;&nbsp; - '.$row1['injury_type'].' : $'.$row1['total_to_bill_patient'].'<br>';
    }
    //Patients Amount To Bill

    //Patients Invoiced Amount
    $all_invoiced_patient = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_invoiced_patient` FROM invoice_patient WHERE paid != 'On Account' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));
    $invoiced_patient = $all_invoiced_patient['all_invoiced_patient'];

    $result3 = mysqli_query($dbc, "SELECT injury_type, SUM(patient_price) AS total_invoiced_patient FROM invoice_patient WHERE paid != 'On Account' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') GROUP BY injury_type");
    $cat_invoiced_patient = '';
    while($row3 = mysqli_fetch_array($result3)) {
        if($row3['injury_type'] == '') {
            $row3['injury_type'] = 'Special/Extra<br>(Reports/Non Patients/Patients without injury)';
        }

        if (array_key_exists($row3['injury_type'],$gst_array)) {
            $cat_invoiced_patient .= '&nbsp;&nbsp; - '.$row3['injury_type'].' : $'.($row3['total_invoiced_patient']-$gst_array[$row3['injury_type']]).'<br>';
        } else {
            $cat_invoiced_patient .= '&nbsp;&nbsp; - '.$row3['injury_type'].' : $'.number_format($row3['total_invoiced_patient'],2).'<br>';
        }
    }
    //Patients Invoiced Amount

    //Insurer Amount To Bill
    $all_to_bill_insurer = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(insurer_price) as `all_to_bill_insurer` FROM invoice_insurer WHERE paid != 'Yes' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));
    $to_bill_insurer = $all_to_bill_insurer['all_to_bill_insurer'];

    $result2 = mysqli_query($dbc, "SELECT injury_type, SUM(insurer_price) AS total_ins FROM invoice_insurer WHERE paid != 'Yes' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') GROUP BY injury_type");
    $cat_to_bill_insurer = '';
    while($row2 = mysqli_fetch_array($result2)) {
        if($row2['injury_type'] == '') {
            $row2['injury_type'] = 'Special/Extra<br>(Reports/Non Patients/Patients without injury)';
        }
        $cat_to_bill_insurer .= '&nbsp;&nbsp; - '.$row2['injury_type'].' : $'.$row2['total_ins'].'<br>';
    }
    //Insurer Amount To Bill

    //$all_invoiced_insurer = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(insurer_price) as `all_invoiced_insurer` FROM invoice_insurer WHERE paid = 'Yes' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));

    //Insurer Invoiced Amount
    $all_invoiced_insurer = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(insurer_price) as `all_invoiced_insurer` FROM invoice_insurer WHERE paid = 'Yes' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));
    $invoiced_insurer = $all_invoiced_insurer['all_invoiced_insurer'];

    $result2 = mysqli_query($dbc, "SELECT injury_type, SUM(insurer_price) AS total_ins FROM invoice_insurer WHERE paid = 'Yes' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') GROUP BY injury_type");
    $cat_invoiced_insurer = '';
    while($row2 = mysqli_fetch_array($result2)) {
        if($row2['injury_type'] == '') {
            $row2['injury_type'] = 'Special/Extra<br>(Reports/Non Patients/Patients without injury)';
        }
        $cat_invoiced_insurer .= '&nbsp;&nbsp; - '.$row2['injury_type'].' : $'.$row2['total_ins'].'<br>';
    }
    //Insurer Invoiced Amount

    //Total Sales
    $total_daily_sales = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(final_price) as `total_daily_sales` FROM invoice WHERE (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));
    $total_sales = $total_daily_sales['total_daily_sales'];

    $total_daily_sales = 0;
    //Total Sales

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="25%">
		<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Not Paid or On Account"><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span>
		Customer A/R
	</th>
    <th width="25%">
		<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Not Paid or On Account"><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span>
		Insurer A/R
	</th>
    <th width="25%">
		<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Paid"><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span>
		Customer Paid
	</th>
    <th width="25%">
		<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Paid"><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span>
		Insurer Paid
	</th>
    </tr>';

    $report_data .= '<tr nobr="true">';

    $report_data .= '<td>';
    /*
    $report_data .= '<a href="../Reports/report_receivables.php?from='.$starttime.'&to='.$endtime.'"><b>$' . number_format($to_bill_patient, 2).'</b></a><br><br>'.$cat_to_bill_patient;
    */
    $report_data .= '<b>$' . number_format($to_bill_patient, 2).'</b><br><br>'.$cat_to_bill_patient;
    $report_data .= '</td>';

    $report_data .= '<td>';
    /*
    $report_data .= '
    <a href="../Reports/report_receivables.php?from='.$starttime.'&to='.$endtime.'"><b>$' . number_format($to_bill_insurer, 2).'</b></a><br><br>'.$cat_to_bill_insurer.'<br>';
    */
    $report_data .= '
    <b>$' . number_format($to_bill_insurer, 2).'</b><br><br>'.$cat_to_bill_insurer.'<br>';
    $report_data .= '</td>';

    $report_data .= '<td>';
    /*
    $report_data .= '<a href="../Reports/report_patient_paid_invoices.php?from='.$starttime.'&to='.$endtime.'"><b>$' . number_format($invoiced_patient, 2).'</b></a><br><br>'.$cat_invoiced_patient.'<br>';
    */
    $report_data .= '<b>$' . number_format($invoiced_patient, 2).'</b><br><br>'.$cat_invoiced_patient.'<br>';
    $report_data .= '</td>';

    $report_data .= '<td>';
    /*
    $report_data .= '
    <a href="../Account%20Receivables/insurer_account_receivables_report.php?p1='.$starttime.'&p2='.$endtime.'"><b>$' . number_format($invoiced_insurer, 2).'</b></a><br><br>'.$cat_invoiced_insurer.'<br>';
    */
    $report_data .= '
    <b>$' . number_format($invoiced_insurer, 2).'</b><br><br>'.$cat_invoiced_insurer.'<br>';

    $report_data .= '</td>';

    $report_data .= '</tr>';

    $all_total = ($to_bill_patient+$to_bill_insurer+$invoiced_patient+$invoiced_insurer);
    $error = ($total_sales-$all_total);
    $report_data .= '<tr nobr="true">';
    $report_data .= '<td colspan="4">';
    $report_data .= '<b>Unassigned/Error Invoices = $'. number_format($error, 2).'</b><br><br>';
    $report_data .= '</td>';
    $report_data .= '</tr>';

    $report_data .= '<tr nobr="true">';

    $report_data .= '<td colspan="4">';
    /*
    $report_data .= '<b>Total Sales = <a href="../Invoice/all_invoice.php?from='.$starttime.'&to='.$endtime.'">$'. number_format($total_sales, 2).'</a></b><br><br>';
    */
    $report_data .= '<b>Total Sales = $'. number_format($total_sales, 2).'</b><br><br>';
    $report_data .= '</td>';
    $report_data .= '</tr>';

    $report_data .= '</table>';

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