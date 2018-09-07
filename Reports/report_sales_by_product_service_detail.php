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
            $footer_text = 'Sales by Inventory/Service Detail From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : Lists sales for each item on your Inventory/Service List. Includes the date, service/inventory, quantity, rate, amount and total.";
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
	$pdf->Output('Download/sales_by_product_service_detail_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_sales_by_product_service_detail', 0, WEBSITE_URL.'/Reports/Download/sales_by_product_service_detail_'.$today_date.'.pdf', 'Sales by Inventory/Service Detail Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/sales_by_product_service_detail_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
} ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Lists sales for each item on your Inventory/Service List. Includes the date, service/inventory, quantity, rate, amount and total.</div>
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

                echo report_daily_validation($dbc, $starttime, $endtime, '', '', '');
            ?>

        </form>

<?php
function report_daily_validation($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style) {

    $total1 = 0;
    $total2 = 0;
    $total_service = 0;
    $total_fee = 0;

    $report_validation = mysqli_query($dbc,"SELECT total_price, invoiceid, invoice_date, serviceid, fee, inventoryid, quantity, sell_price, final_price FROM invoice WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND (`total_price` > 0 OR `total_price` < 0)");
    $num_rows = mysqli_num_rows($report_validation);

    if($num_rows > 0) {
        $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">
        <tr style="'.$table_row_style.'" nobr="true">
        <th width="10%">Date</th>
        <th width="55%">Services/Sales</th>
        <th width="5%">Quantity</th>
        <th width="10%">Rate/Amount</th>
        <th width="10%">Total Without GST</th>
        <th width="10%">Total With GST</th>
        </tr>';

        while($row_report = mysqli_fetch_array($report_validation)) {
            $invid = $row_report['invoiceid'];
            $report_data .= '<tr nobr="true">';
            $report_data .= '<td>' . $row_report['invoice_date'] . '</td>';
            //$report_data .= '<td>' . get_contact($dbc, $row_report['patientid']) . '</td>';
            //$report_data .= '<td>' . $row_report['invoiceid'] . '</td>';

            $serviceid = explode(',', $row_report['serviceid']);
            $fee = explode(',', $row_report['fee']);

            $m = 0;
            $service_name = '';
            $service_qty = '';
            $service_fee = '';
            foreach ($serviceid as $total_sid) {
                if($total_sid != '') {
                    $service_name .= get_all_from_service($dbc, $total_sid, 'service_code').' : '.get_all_from_service($dbc, $total_sid, 'heading').'<br>';
                    $service_qty .= '1<br>';
                    $service_fee .= '$'.$fee[$m].'<br>';
                    $total_service += $fee[$m];
                }
                $m++;
            }

            $parts1 = explode(',', $row_report['inventoryid']);
            $quantity = explode(',', $row_report['quantity']);
            $sell_price = explode(',', $row_report['sell_price']);
            $k = 0;
            $inv_name = '';
            $inv_qty = '';
            $inv_fee = '';
            foreach ($parts1 as $key1) {
                if($key1 != '') {
                    if($quantity[$k] == '') {
                        $quantity[$k] = 1;
                    }
                    $inv_name .= get_all_from_inventory($dbc, $key1 , 'name').'<br>';
                    $inv_qty .= $quantity[$k].'<br>';
                    $inv_fee .= '$'.$sell_price[$k].'<br>';
                    $total_fee += $sell_price[$k];
                }
                $k++;
            }

            $report_data .= '<td>'.$service_name.$inv_name.'</td>';
            $report_data .= '<td>'.$service_qty.$inv_qty.'</td>';
            $report_data .= '<td>'.$service_fee.$inv_fee.'</td>';

            $report_data .= '<td>$' . $row_report['total_price'] . '</td>';
            $report_data .= '<td>$' . $row_report['final_price'] . '</td>';

            //$name_of_file = '../Invoice/Download/invoice_'.$invid.'.pdf';
            //$report_data .= '<td><a href="'.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a></td>';

            $report_data .= "</tr>";

            $total1 += $row_report['final_price'];
            $total2 += $row_report['total_price'];
        }

        $report_data .= '<tr nobr="true">';
        $report_data .= '<td colspan="2"><b>Total</b></td>';
        $report_data .= '<td><b>Services:<br>Inventory:</b></td>';
        $report_data .= '<td><b>$' . number_format($total_service, 2) . '<br>$' . number_format($total_fee, 2) . '</b></td>';
        $report_data .= '<td><b>$' . number_format($total2, 2) . '</b></td>';
        $report_data .= '<td><b>$' . number_format($total1, 2) . '</b></td>';
        $report_data .= "</tr>";
        $report_data .= '</table>';
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