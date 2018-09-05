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
            $footer_text = 'Receipt Summary From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : Total sales by Service Category and Payment Type for the selected timeframe.";
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

    $html .= report_sales_summary($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/receipt_summary_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_receipt_summary', 0, WEBSITE_URL.'/Reports/Download/receipt_summary_'.$today_date.'.pdf', 'Receipt Summary Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/receipt_summary_<?php echo  $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    } ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Total sales by Service Category and Payment Type for the selected timeframe.</div>
            <div class="clearfix"></div>
        </div>
        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

            <input type="hidden" name="report_type" value="<?php echo  $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo  $_GET['category']; ?>">

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

            <input type="hidden" name="starttimepdf" value="<?php echo  $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo  $endtime; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                //echo  '<a href="report_referral.php?referral=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a></h4><br>';

                echo  report_sales_summary($dbc, $starttime, $endtime, '', '', '');

                if(!empty($_GET['from'])) {
                    echo '<a href="'.WEBSITE_URL.'/Reports/report_daily_sales_summary.php?from='.$_GET['from'].'&to='.$_GET['to'].'" class="btn brand-btn">Back</a>';
                }
            ?>
        </form>

<?php
function report_sales_summary($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style) {
    $report_data = '';

    $report_dd = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(insurer_price) as `all_dd` FROM invoice_insurer WHERE paid='Yes' AND (paid_date >= '".$starttime."' AND paid_date <= '".$endtime."')"));
    $dd = $report_dd['all_dd'];

    $report_data .=  '<table border="1px" class="table table-bordered" style="'.$table_style.'">';

    $report_data .=  '<tr style="'.$table_row_style.'">';
    $report_data .=  '<th width="25%">&nbsp;</th>';
    $report_data .=  '<th width="9%">MasterCard</th>';
    $report_data .=  '<th width="6%">Visa</th>';
    $report_data .=  '<th width="5%">Amex</th>';
    $report_data .=  '<th width="7%">Debit</th>';
    $report_data .=  '<th width="6%">Cash</th>';
    $report_data .=  '<th width="6%">Cheque</th>';
    $report_data .=  '<th width="10%">Gift Certificates Redeemed</th>';
    $report_data .=  '<th width="8%">Pro Bono</th>';
    $report_data .=  '<th width="8%">Direct Deposit</th>';
    $report_data .=  '<th width="10%">Total</th>';
    $report_data .=  '</tr>';

    $final_total = 0;
    $total1 =  0;
    $total2 =  0;
    $total3 =  0;
    $total4 =  0;
    $total5 =  0;
    $total6 =  0;
    $total7 =  0;
    $total8 =  0;
    $grand_total = 0;

    $total_contact = mysqli_query($dbc,"SELECT * FROM invoice_patient WHERE paid != 'On Account' AND (DATE(paid_date) >= '".$starttime."' AND DATE(paid_date) <= '".$endtime."') GROUP BY injury_type");

    while($row_report = mysqli_fetch_array($total_contact)) {
        $sub_total = 0;
        $injury_type = $row_report['injury_type'];

        $all_mc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_mc` FROM invoice_patient WHERE paid='Master Card' AND injury_type = '$injury_type' AND (paid_date >= '".$starttime."' AND paid_date <= '".$endtime."')"));
        $mc = $all_mc['all_mc'];

        $all_visa = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_visa` FROM invoice_patient WHERE paid='Visa' AND injury_type = '$injury_type' AND (paid_date >= '".$starttime."' AND paid_date <= '".$endtime."')"));
        $visa = $all_visa['all_visa'];

        $all_amex = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_amex` FROM invoice_patient WHERE paid='Amex' AND injury_type = '$injury_type' AND (paid_date >= '".$starttime."' AND paid_date <= '".$endtime."')"));
        $amex = $all_amex['all_amex'];

        $all_dc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_dc` FROM invoice_patient WHERE paid='Debit Card' AND injury_type = '$injury_type' AND (paid_date >= '".$starttime."' AND paid_date <= '".$endtime."')"));
        $dc = $all_dc['all_dc'];

        $all_cash = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_cash` FROM invoice_patient WHERE paid='Cash' AND injury_type = '$injury_type' AND (paid_date >= '".$starttime."' AND paid_date <= '".$endtime."')"));
        $cash = $all_cash['all_cash'];

        $all_cheque = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_cheque` FROM invoice_patient WHERE paid='Cheque' AND injury_type = '$injury_type' AND (paid_date >= '".$starttime."' AND paid_date <= '".$endtime."')"));
        $cheque = $all_cheque['all_cheque'];

        $all_gift = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_gift` FROM invoice_patient WHERE paid='Gift Certificate Redeem' AND injury_type = '$injury_type' AND (paid_date >= '".$starttime."' AND paid_date <= '".$endtime."')"));
        $gift = $all_gift['all_gift'];

        $all_probono = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `all_probono` FROM invoice_patient WHERE paid='Pro-Bono' AND injury_type = '$injury_type' AND (paid_date >= '".$starttime."' AND paid_date <= '".$endtime."')"));
        $probono = $all_probono['all_probono'];

        if($injury_type == '') {
            $injury_type = 'Special/Extra<br>(Reports/Non Patients/Patients without injury)';
        }

        $report_data .= '<tr nobr="true">';
        $report_data .=  '<td>'.$injury_type.'</td>';

        $report_data .= '<td>';
        $report_data .=  number_format($mc, 2);
        $total1 += $mc;
        $sub_total += $mc;

        $report_data .=  '</td>';
        $report_data .=  '<td>';
        $report_data .=  number_format($visa, 2);
        $total2 += $visa;
        $sub_total += $visa;

        $report_data .=  '</td>';
        $report_data .=  '<td>';
        $report_data .=  number_format($amex, 2);
        $total6 += $amex;
        $sub_total += $amex;

        $report_data .=  '</td>';
        $report_data .=  '<td>';
        $report_data .=  number_format($dc, 2);
        $total3 += $dc;
        $sub_total += $dc;

        $report_data .=  '</td>';
        $report_data .=  '<td>';
        $report_data .=  number_format($cash, 2);
        $total4 += $cash;
        $sub_total += $cash;

        $report_data .=  '</td>';
        $report_data .=  '<td>';
        $report_data .=  number_format($cheque, 2);
        $total5 += $cheque;
        $sub_total += $cheque;

        $report_data .=  '</td>';
        $report_data .=  '<td>';
        $report_data .=  number_format($gift, 2);
        $total7 += $gift;
        $sub_total += $gift;

        $report_data .=  '</td>';
        $report_data .=  '<td>';
        $report_data .=  number_format($probono, 2);
        $total8 += $probono;
        $sub_total += $probono;

        $report_data .=  '</td><td>0.00</td><td><b>'.number_format($sub_total, 2).'</b></td>';
        $report_data .=  '</tr>';

        $grand_total += $sub_total;
    }

    /* Add here
    */

    $report_data .=  '<tr><td>Insurer</td><td>0.00</td><td>0.00</td><td>0.00</td><td>0.00</td><td>0.00</td><td>0.00</td><td>0.00</td><td>0.00</td><td>'.number_format($dd, 2).'</td><td><b>'.number_format($dd, 2).'</b></td></tr>';

    $report_data .=  '<tr><td><b>Grand Total</b></td><td><b>'.number_format($total1, 2).'</b></td><td><b>'.number_format($total2, 2).'</b></td><td><b>'.number_format($total6, 2).'</b></td><td><b>'.number_format($total3, 2).'</b></td><td><b>'.number_format($total4, 2).'</b></td><td><b>'.number_format($total5, 2).'</b></td><td><b>'.number_format($total7, 2).'</b></td><td><b>'.number_format($total8, 2).'</b></td><td><b>'.number_format($dd, 2).'</b></td><td><b>'.number_format(($grand_total+$dd), 2).'</b></td></tr>';

    $report_data .=  '</table>';

    return $report_data;

}

/*

    $report_validation = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT group_concat(`payment_type` separator '*#*') as `all_payment`, group_concat(`injuryid` separator '*#*') as `all_injury` FROM invoice WHERE payment_type IS NOT NULL AND payment_type NOT LIKE '#*#%' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));

    $payment = $report_validation['all_payment'];
    $injury = $report_validation['all_injury'];

    $payment = str_replace(',#*#', '#*#', $payment);
    $payment = str_replace(',*#*', '*#*', $payment);
    $payment = explode('*#*', $payment);
    $injury = explode('*#*', $injury);

    $final_pay = array();
    $k = 0;
    foreach ($payment as $sel) {
        $injury_type = get_all_from_injury($dbc, $injury[$k], 'injury_type');
        if (strpos($sel, ',') !== false) {
            $sep_sel = explode('#*#', $sel);
            $each_sep_pt = explode(',', $sep_sel[0]);
            $each_sep_pp = explode(',', $sep_sel[1]);
            $m = 0;
            foreach ($each_sep_pt as $value_each_sep_sel) {
                $final_pay[][$injury_type][$value_each_sep_sel] = $each_sep_pp[$m];
                $m++;
            }
        } else {
            $each_sel = explode('#*#', $sel);
            $final_pay[][$injury_type][$each_sel[0]] = $each_sel[1];
        }
        $k++;
    }

    $final_pay = call_user_func_array('array_merge_recursive', $final_pay);

    foreach ($final_pay as $key=>$value) {
        $sub_total = 0;
        $report_data .=  '<tr>';
        $report_data .=  '<td>'.$key.'</td>';

        $report_data .=  '<td>';
        if(count($value['Master Card']) == 1) {
            $report_data .=  number_format($value['Master Card'], 2);
            $sub_total += $value['Master Card'];
            $total1 += $value['Master Card'];
        } else {
            $report_data .=  number_format(array_sum($value['Master Card']), 2);
            $sub_total += array_sum($value['Master Card']);
            $total1 += array_sum($value['Master Card']);
        }
        $report_data .=  '</td>';
        $report_data .=  '<td>';
        if(count($value['Visa']) == 1) {
            $report_data .=  number_format($value['Visa'], 2);
            $sub_total += $value['Visa'];
            $total2 += $value['Visa'];
        } else {
            $report_data .=  number_format(array_sum($value['Visa']), 2);
            $sub_total += array_sum($value['Visa']);
            $total2 += array_sum($value['Visa']);
        }
        $report_data .=  '</td>';
        $report_data .=  '<td>';
        if(count($value['Debit Card']) == 1) {
            $report_data .=  number_format($value['Debit Card'], 2);
            $sub_total += $value['Debit Card'];
            $total3 += $value['Debit Card'];
        } else {
            $report_data .=  number_format(array_sum($value['Debit Card']), 2);
            $sub_total += array_sum($value['Debit Card']);
            $total3 += array_sum($value['Debit Card']);
       }
        $report_data .=  '</td>';
        $report_data .=  '<td>';
        if(count($value['Cash']) == 1) {
            $report_data .=  number_format($value['Cash'], 2);
            $sub_total += $value['Cash'];
            $total4 += $value['Cash'];
        } else {
            $report_data .=  number_format(array_sum($value['Cash']), 2);
            $sub_total += array_sum($value['Cash']);
            $total4 += array_sum($value['Cash']);
        }
        $report_data .=  '</td>';
        $report_data .=  '<td>';
        if(count($value['Cheque']) == 1) {
            $report_data .=  number_format($value['Cheque'], 2);
            $sub_total += $value['Cheque'];
            $total5 += $value['Cheque'];
        } else {
            $report_data .=  number_format(array_sum($value['Cheque']), 2);
            $sub_total += array_sum($value['Cheque']);
            $total5 += array_sum($value['Cheque']);
        }
        $report_data .=  '</td>';
        $report_data .=  '<td>';
        if(count($value['Amex']) == 1) {
            $report_data .=  number_format($value['Amex'], 2);
            $sub_total += $value['Amex'];
            $total6 += $value['Amex'];
        } else {
            $report_data .=  number_format(array_sum($value['Amex']), 2);
            $sub_total += array_sum($value['Amex']);
            $total6 += array_sum($value['Amex']);
        }
        $report_data .=  '</td>';
        $report_data .=  '<td>';
        if(count($value['Gift Certificate Redeem']) == 1) {
            $report_data .=  number_format($value['Gift Certificate Redeem'], 2);
            $sub_total += $value['Gift Certificate Redeem'];
            $total7 += $value['Gift Certificate Redeem'];
        } else {
            $report_data .=  number_format(array_sum($value['Gift Certificate Redeem']), 2);
            $sub_total += array_sum($value['Gift Certificate Redeem']);
            $total7 += array_sum($value['Gift Certificate Redeem']);
        }
        $report_data .=  '</td>';
        $report_data .=  '<td>';
        if(count($value['Pro-Bono']) == 1) {
            $report_data .=  number_format($value['Pro-Bono'], 2);
            $sub_total += $value['Pro-Bono'];
            $total8 += $value['Pro-Bono'];
        } else {
            $report_data .=  number_format(array_sum($value['Pro-Bono']), 2);
            $sub_total += array_sum($value['Pro-Bono']);
            $total8 += array_sum($value['Pro-Bono']);
        }
        $report_data .=  '</td>';
        $report_data .=  '<td>0.00</td><td>'.$sub_total.'</td>';
        $report_data .=  '</tr>';
        $final_total += $sub_total;
    }

    */
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