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
            $footer_text = 'Sales Summary by Injury Type From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : You can see Sales, Payments and A/R for the specified date range. Sales is divided by Patient and Insurer, as well as how much has been paid and not paid. Each area of this section is divided by service category. It will display Total GST paid, Inventory Sales and Sales with and without GST. Payments will display by payment types from the invoice. A/R is divided up by Total Invoice Amount, Amount to Bill and A/R.";
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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 65, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);

    $html .= report_sales_summary($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/sales_summary_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_daily_sales_summary', 0, WEBSITE_URL.'/Reports/Download/sales_summary_'.$today_date.'.pdf', 'Sales Summary by Injury Type Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/sales_summary_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    } ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            You can see Sales, Payments and A/R for the specified date range. Sales is divided by Patient and Insurer, as well as how much has been paid and not paid. Each area of this section is divided by service category. It will display Total GST paid, Inventory Sales and Sales with and without GST. Payments will display by payment types from the invoice. A/R is divided up by Total Invoice Amount, Amount to Bill and A/R.</div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
            } else if(!empty($_GET['from'])) {
                $starttime = $_GET['from'];
            } else if($starttime == 0000-00-00) {
                $starttime = date('Y-m-01');
            }

            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
            } else if(!empty($_GET['to'])) {
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
                //echo '<a href="report_referral.php?referral=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a></h4><br>';

                echo report_sales_summary($dbc, $starttime, $endtime, '', '', '');
            ?>
        </form>

<?php
function report_sales_summary($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style) {

    $report_dd = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(insurer_price) as `all_dd` FROM invoice_insurer WHERE paid='Yes' AND (paid_date >= '".$starttime."' AND paid_date <= '".$endtime."')"));
    $dd = $report_dd['all_dd'];

    $report_gst = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(final_price) as `final_price`, SUM(total_price) as `total_price` FROM invoice WHERE (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));
    $invoiced_gst = ($report_gst['final_price']-$report_gst['total_price']);

    // Inv sales
    $report_inv = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(`sub_total`) inventory FROM (SELECT `sub_total`, `invoiceid` FROM `invoice_patient` WHERE `product_name` != '' UNION SELECT `sub_total`, `invoiceid` FROM `invoice_insurer` WHERE `product_name` != '') products WHERE `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE `invoice_date` >= '".$starttime."' AND `invoice_date` <= '".$endtime."')"));
    $invoiced_inventory = $report_inv['inventory'];
    // Inv sales

    //$result5 = mysqli_query($dbc, "SELECT pi.injury_type, inv.total_price, inv.final_price, SUM(inv.final_price-inv.total_price) AS total_gst FROM invoice inv, patient_injury pi WHERE (pi.injuryid = inv.injuryid OR inv.injuryid = 0) AND inv.total_price != inv.final_price AND (inv.invoice_date >= '".$starttime."' AND inv.invoice_date <= '".$endtime."') GROUP BY pi.injury_type");

    $result5 = mysqli_query($dbc, "SELECT pi.injury_type, inv.total_price, inv.final_price, SUM(inv.final_price-inv.total_price) AS total_gst FROM invoice inv, patient_injury pi WHERE (pi.injuryid = inv.injuryid) AND inv.injuryid != 0 AND pi.injury_type IS NOT NULL AND pi.injury_type !='' AND inv.total_price != inv.final_price AND (inv.invoice_date >= '".$starttime."' AND inv.invoice_date <= '".$endtime."') GROUP BY pi.injury_type");

    $cat_invoiced_gst = '';
    $total_all_gst = 0;
    $gst_array = array();
    while($row5 = mysqli_fetch_array($result5)) {
        $cat_injury_type = $row2['injury_type'];

        if($row5['injury_type'] == '') {
            $row5['injury_type'] = 'Special/Extra<br>(Reports/Non Patients/Patients without injury)';
        }
        $cat_invoiced_gst .= '&nbsp;&nbsp; - '.$row5['injury_type'].' : $'.number_format($row5['total_gst'], 2).'<br>';
        $gst_array[$row5['injury_type']] = number_format($row5['total_gst'],2);
        $total_all_gst += $row5['total_gst'];
    }

    $special_extra_gst = $invoiced_gst-$total_all_gst;
    if($special_extra_gst != 0) {
        $cat_invoiced_gst .= '&nbsp;&nbsp; - Special/Extra<br>(Reports/Non Patients/Patients without injury) : $'.number_format($invoiced_gst-$total_all_gst, 2).'<br>';
        $gst_array['Special/Extra<br>(Reports/Non Patients/Patients without injury)'] = number_format($special_extra_gst,2);
    }

    $all_to_bill_patient = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(sub_total) as `all_to_bill_patient` FROM invoice_patient WHERE IFNULL(paid,'') IN ('On Account','') AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND `product_name` = ''"));
    $to_bill_patient = $all_to_bill_patient['all_to_bill_patient'];

    $result1 = mysqli_query($dbc, "SELECT injury_type, SUM(sub_total) AS total_to_bill_patient FROM invoice_patient WHERE IFNULL(paid,'') IN ('On Account','') AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND `product_name` = '' GROUP BY injury_type");
    $cat_to_bill_patient = '';
    while($row1 = mysqli_fetch_array($result1)) {
        if($row1['injury_type'] == '') {
            $row1['injury_type'] = 'Special/Extra<br>(Reports/Non Patients/Patients without injury)';
        }
        $cat_to_bill_patient .= '&nbsp;&nbsp; - '.$row1['injury_type'].' : $'.$row1['total_to_bill_patient'].'<br>';
    }

    $all_invoiced_patient = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(sub_total) as `all_invoiced_patient` FROM invoice_patient WHERE IFNULL(paid,'') NOT IN ('On Account','') AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND `product_name` = ''"));
    //$invoiced_patient = $all_invoiced_patient['all_invoiced_patient']-$invoiced_gst;
    $invoiced_patient = $all_invoiced_patient['all_invoiced_patient'];

    $result3 = mysqli_query($dbc, "SELECT injury_type, SUM(sub_total) AS total_invoiced_patient FROM invoice_patient WHERE IFNULL(paid,'') NOT IN ('On Account','') AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND `product_name` = '' GROUP BY injury_type");
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

    $all_to_bill_insurer = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(sub_total) as `all_to_bill_insurer` FROM invoice_insurer WHERE paid != 'Yes' AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND `product_name` = ''"));
    $to_bill_insurer = $all_to_bill_insurer['all_to_bill_insurer'];

    $result2 = mysqli_query($dbc, "SELECT injury_type, SUM(sub_total) AS total_ins FROM invoice_insurer WHERE paid != 'Yes' AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND `product_name` = '' GROUP BY injury_type");
    $cat_to_bill_insurer = '';
    while($row2 = mysqli_fetch_array($result2)) {
        if($row2['injury_type'] == '') {
            $row2['injury_type'] = 'Special/Extra<br>(Reports/Non Patients/Patients without injury)';
        }
        $cat_to_bill_insurer .= '&nbsp;&nbsp; - '.$row2['injury_type'].' : $'.$row2['total_ins'].'<br>';
    }

    //$all_invoiced_insurer = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(insurer_price) as `all_invoiced_insurer` FROM invoice_insurer WHERE paid = 'Yes' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));

    $all_invoiced_insurer = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(sub_total) as `all_invoiced_insurer` FROM invoice_insurer WHERE paid = 'Yes' AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND `product_name` = ''"));
    $invoiced_insurer = $all_invoiced_insurer['all_invoiced_insurer'];

    $result2 = mysqli_query($dbc, "SELECT injury_type, SUM(sub_total) AS total_ins FROM invoice_insurer WHERE paid = 'Yes' AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND `product_name` = '' GROUP BY injury_type");
    $cat_invoiced_insurer = '';
    while($row2 = mysqli_fetch_array($result2)) {
        if($row2['injury_type'] == '') {
            $row2['injury_type'] = 'Special/Extra<br>(Reports/Non Patients/Patients without injury)';
        }
        $cat_invoiced_insurer .= '&nbsp;&nbsp; - '.$row2['injury_type'].' : $'.$row2['total_ins'].'<br>';
    }

    $all_mc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_mc` FROM invoice_patient WHERE paid='Master Card' AND (paid_date >= '".$starttime."' AND paid_date <= '".$endtime."')"));
    $mc = $all_mc['all_mc'];

    $all_visa = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_visa` FROM invoice_patient WHERE paid='Visa' AND (paid_date >= '".$starttime."' AND paid_date <= '".$endtime."')"));
    $visa = $all_visa['all_visa'];

    $all_dc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_dc` FROM invoice_patient WHERE paid='Debit Card' AND (paid_date >= '".$starttime."' AND paid_date <= '".$endtime."')"));
    $dc = $all_dc['all_dc'];

    $all_cash = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_cash` FROM invoice_patient WHERE paid='Cash' AND (paid_date >= '".$starttime."' AND paid_date <= '".$endtime."')"));
    $cash = $all_cash['all_cash'];

    $all_cheque = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_cheque` FROM invoice_patient WHERE paid='Cheque' AND (paid_date >= '".$starttime."' AND paid_date <= '".$endtime."')"));
    $cheque = $all_cheque['all_cheque'];

    $all_amex = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_amex` FROM invoice_patient WHERE paid='Amex' AND (paid_date >= '".$starttime."' AND paid_date <= '".$endtime."')"));
    $amex = $all_amex['all_amex'];

    $all_gift = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_gift` FROM invoice_patient WHERE paid='Gift Certificate Redeem' AND (paid_date >= '".$starttime."' AND paid_date <= '".$endtime."')"));
    $gift = $all_gift['all_gift'];

    $all_probono = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_probono` FROM invoice_patient WHERE paid='Pro-Bono' AND (paid_date >= '".$starttime."' AND paid_date <= '".$endtime."')"));
    $probono = $all_probono['all_probono'];

    $total_daily_sales = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(final_price) as `total_daily_sales` FROM invoice WHERE (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));
    $total_sales = $total_daily_sales['total_daily_sales'];

    $total_daily_sales = 0;

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="40%">
		<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="This column identifies the to bill amount, invoiced amount and total sales for the clinic."><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span>
		Sales
	</th>
    <th width="30%">
		<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="This column breaks out the payment type for the sales summary."><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span>
		Payments
	</th>
    <th width="30%">
		<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="This column shows the accounts receivable side to cross reference with sales results."><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span>
		Summary
	</th>
    </tr>';

    $report_data .= '<tr nobr="true">';

    $report_data .= '<td>';
    $report_data .= 'Patient Amount To Bill
		<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Not Paid or On Account"><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span>
    = <a href="../Reports/report_receivables.php?from='.$starttime.'&to='.$endtime.'">' . number_format($to_bill_patient, 2).'</a><br>'.$cat_to_bill_patient.'<br>';
    $report_data .= 'Insurer Amount To Bill =
		<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Not Paid or On Account"><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span>
    <a href="../Reports/report_receivables.php?from='.$starttime.'&to='.$endtime.'">' . number_format($to_bill_insurer, 2).'</a><br>'.$cat_to_bill_insurer.'<br>';

    $report_data .= 'Patient Invoiced Amount =
		<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Patient Paid"><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span>
    <a href="../Reports/report_patient_paid_invoices.php?from='.$starttime.'&to='.$endtime.'">' . number_format($invoiced_patient, 2).'</a><br>'.$cat_invoiced_patient.'<br>';
    $report_data .= 'Insurer Invoiced Amount  =
		<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Insurer Paid"><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span>
    <a href="../Account%20Receivables/insurer_account_receivables_report.php?p1='.$starttime.'&p2='.$endtime.'">' . number_format($invoiced_insurer, 2).'</a><br>'.$cat_invoiced_insurer.'<br>';

    $report_data .= 'Total Inventory Sales from all invoices = $' . number_format($invoiced_inventory, 2).'<br><br>';

    $report_data .= 'Total Sales without GST = '. number_format($report_gst['total_price'], 2).'<br><br><hr>';

    $report_data .= 'Total GST(Total GST from all invoice) = ' . number_format($invoiced_gst, 2).'<br>'.$cat_invoiced_gst.'<br>';

    //$report_data .= 'Unassigned amount = '. number_format($total_sales-$to_bill_patient-$to_bill_insurer-$invoiced_patient-$invoiced_insurer-$invoiced_gst, 2).'<br>';

    $report_data .= 'Total Sales with GST = <a href="../Invoice/all_invoice.php?from='.$starttime.'&to='.$endtime.'">'. number_format($total_sales, 2).'</a>';

    $report_data .= '</td>';

    $report_data .= '<td>';
    $report_data .=  'Master Card : '.number_format($mc, 2);
    $total_daily_sales += $mc;

    $report_data .=  '<br>';
    $report_data .=  'Visa : '.number_format($visa, 2);
    $total_daily_sales += $visa;

    $report_data .=  '<br>';
    $report_data .=  'Debit Card : '.number_format($dc, 2);
    $total_daily_sales += $dc;

    $report_data .=  '<br>';
    $report_data .=  'Cash : '.number_format($cash, 2);
    $total_daily_sales += $cash;

    $report_data .=  '<br>';
    $report_data .=  'Cheque : '.number_format($cheque, 2);
    $total_daily_sales += $cheque;

    $report_data .=  '<br>';
    $report_data .=  'Amex : '.number_format($amex, 2);
    $total_daily_sales += $amex;

    $report_data .=  '<br>';
    $report_data .=  'Gift Certificate Redeem : '.number_format($gift, 2);
    $total_daily_sales += $gift;

    $report_data .=  '<br>';
    $report_data .=  'Pro-Bono : '.number_format($probono, 2);
    $total_daily_sales += $probono;

    $report_data .=  '<br>Direct Deposit : '.number_format($dd, 2);

    $total_daily_sales += $dd;

    $report_data .=  '<br><br>Total Deposit Payment : <a href="../Reports/report_receipt_summary.php?from='.$starttime.'&to='.$endtime.'">'.number_format($total_daily_sales, 2).'</a>';

    $report_data .= '</td>';

    $report_invoiced = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(final_price) as `final_price` FROM invoice WHERE (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));
    $total_invoiced = $report_invoiced['final_price'];

    $report_data .= '<td>';
    $report_data .= 'Amount To Bill = ' . number_format(($to_bill_patient+$to_bill_insurer), 2).'<br>';
    $report_data .= 'Invoiced Amount = ' . number_format($invoiced_patient, 2).'<br>';
    $report_data .= '-Payment Amount = -'. number_format($total_daily_sales, 2).'<br>';
    $report_data .= 'A/R = '.number_format(($to_bill_patient+$to_bill_insurer+$invoiced_patient-$total_daily_sales), 2) .'<br>';

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