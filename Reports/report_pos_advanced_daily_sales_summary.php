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
                $this->Image($image_file, 10, 10, '', '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);

            $this->SetFont('helvetica', '', 13);
            $footer_text = 'POS Sales Summary From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
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

    $html .= report_sales_summary($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/sales_summary_'.$today_date.'.pdf', 'F');

    track_download($dbc, 'report_pos_advanced_daily_sales_summary', 0, WEBSITE_URL.'/Reports/Download/sales_summary_'.$today_date.'.pdf', 'POS Sales Summary Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/sales_summary_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    } ?>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
            }
            
            if (isset($_GET['pos_submit'])) {
                $starttime = $_GET['from'];
                $endtime = $_GET['to'];
            }

            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-01');
            }

            if($endtime == 0000-00-00) {
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
            <br /><br />

            <?php
                //echo '<a href="report_referral.php?referral=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a></h4><br />';

                echo report_sales_summary($dbc, $starttime, $endtime, '', '', '');
            ?>
        </form>

<?php
function report_sales_summary($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style) {

    //$report_validation = mysqli_query($dbc, "SELECT payment_type, SUM(`sub_total`) AS sub_total, SUM(`discount_value`) AS discount_value, SUM(`delivery`) AS delivery, SUM(`assembly`) AS assembly, SUM(`total_before_tax`) AS total_before_tax, SUM(`gst`) AS gst, SUM(`pst`) AS pst, SUM(`total_price`) AS total_price FROM point_of_sell WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."')");

    $report_validation = mysqli_query($dbc, "SELECT final_price, discount, gst_amt, pst_amt, SUM(final_price + discount - gst_amt - pst_amt - delivery - assembly) sub_total, delivery, assembly, payment_type FROM invoice WHERE deleted=0 AND (status='Completed' OR status='Posted') AND (invoice_date BETWEEN '$starttime' AND '$endtime') GROUP BY invoiceid");

    if ( $report_validation->num_rows >0 ) {
        $sub_total = 0;
        $discount_value = 0;
        $delivery = 0;
        $assembly = 0;
        $total_before_tax = 0;
        $gst_total = 0;
        $pst_total = 0;
        $total = 0;

        $amex = 0;
        $cash = 0;
        $cheque = 0;
        $debit = 0;
        $eft = 0;
        $master = 0;
        $on = 0;
        $visa = 0;
        $uncategorized = 0;
        $other = 0;

        $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
        $report_data .= '<tr style="'.$table_row_style.'">
            <th>Daily Sales</th>
            <th>Daily Payments</th>
            <th>Daily A/R</th>
        </tr>';

        while($row_report = mysqli_fetch_array($report_validation)) {
            $sub_total += $row_report['sub_total'];
            $discount_value += $row_report['discount'];
            $delivery += $row_report['delivery'];
            $assembly += $row_report['assembly'];
            $total_before_tax += $row_report['sub_total'] - $row_report['discount'] + $row_report['delivery'] + $row_report['assembly'];
            $gst_total += $row_report['gst_amt'];
            $pst_total += $row_report['pst_amt'];
            $total += $row_report['final_price'];

            list($payment_type, $amount) = explode('#*#', $row_report['payment_type']);
            if ( $payment_type=='American Express' ) {
                $amex += $amount;
            } elseif ( $payment_type=='Cash' ) {
                $cash += $amount;
            } elseif ( $payment_type=='Cheque' || $payment_type=='Check' ) {
                $cheque += $amount;
            } elseif ( $payment_type=='Debit' ) {
                $debit += $amount;
            } elseif ( $payment_type=='EFT' ) {
                $eft += $amount;
            } elseif ( $payment_type=='Mastercard' ) {
                $master += $amount;
            } elseif ( $payment_type=='On Account' ) {
                $on += $amount;
            } elseif ( $payment_type=='Visa' ) {
                $visa += $amount;
            } elseif ( $payment_type=='' ) {
                $uncategorized += $amount;
            } else {
                $other += $amount;
            }

            $paid = $amex + $cash + $cheque + $debit + $eft + $master + $visa + $uncategorized;
            $ar = $on + $other;
        }//while

        $report_data .= '<tr nobr="true">';
            $report_data .= '<td>';
                $report_data .= 'Sub Total = '. number_format($sub_total, 2) .'<br />';
                $report_data .= 'Discount Value = '. number_format($discount_value, 2) .'<br />';
                $report_data .= 'Delivery = '. number_format($delivery, 2) .'<br />';
                $report_data .= 'Assembly = '. number_format($assembly, 2) .'<br />';
                $report_data .= 'Net Sales = '. number_format($total_before_tax - $delivery - $assembly, 2) .'<br />';
                $report_data .= 'Total Before Tax = '. number_format($total_before_tax, 2) .'<br />';
                $report_data .= 'GST = '. number_format($gst_total, 2) .'<br />';
                $report_data .= 'PST = '. number_format($pst_total, 2) .'<br />';
                $report_data .= 'Total Price = '. number_format($total, 2) .'<br />';
            $report_data .= '</td>';

            $report_data .= '<td>';
                $report_data .= 'Mastercard = '. number_format($master, 2) .'<br />';
                $report_data .= 'Visa = '. number_format($visa, 2) .'<br />';
                $report_data .= 'American Express = '. number_format($amex, 2) .'<br />';
                $report_data .= 'Debit = '. number_format($debit, 2) .'<br />';
                $report_data .= 'Cash = '. number_format($cash, 2) .'<br />';
                $report_data .= 'Cheques = '. number_format($cheque, 2) .'<br />';
                $report_data .= 'On Account = '. number_format($on, 2) .'<br />';
                $report_data .= 'Uncategorized = '. number_format($uncategorized, 2) .'<br />';
                $report_data .= 'Other = '. number_format($other, 2) .'<br />';
            $report_data .= '</td>';

            $report_data .= '<td>';
                $report_data .= 'Paid Invoices = '. number_format($paid, 2) .'<br />';
                $report_data .= 'Unpaid Invoices = '. number_format($ar, 2) .'<br />';
                $report_data .= 'Total Invoices = '. number_format($total, 2) .'<br />';
            $report_data .= '</td>';
        $report_data .= "</tr>";
    } //if num_rows>0

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