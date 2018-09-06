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
            $footer_text = 'Customer Contact List From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : This report lists each customer's phone number(s), email and billing address, as well as their invoice (click the PDF icon to access their invoice), the invoice date and the invoice amount.";
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
	$pdf->Output('Download/customer_contact_list_'.$today_date.'.pdf', 'F');

    track_download($dbc, 'reports_customer_contact_list', 0, WEBSITE_URL.'/Reports/Download/customer_contact_list_'.$today_date.'.pdf', 'Customer Contact List Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/customer_contact_list_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
} ?>

        <!--
        <br>
        <a href='report_review_sales.php'><button type="button" class="btn brand-btn mobile-block" >Monthly Sales by Injury Type</button></a>&nbsp;&nbsp;
        <a href='report_invoice_sales_summary.php'><button type="button" class="btn brand-btn mobile-block" >Invoice Sales Summary</button></a>&nbsp;&nbsp;
        <a href='report_sales_by_customer_summary.php'><button type="button" class="btn brand-btn mobile-block" >Sales by Customer Summary</button></a>&nbsp;&nbsp;
        <a href='report_sales_by_customer_detail.php'><button type="button" class="btn brand-btn mobile-block" >Sales History by Customer</button></a>&nbsp;&nbsp;
        <a href='report_sales_by_product_service_summary.php'><button type="button" class="btn brand-btn mobile-block" >Sales by Service Summary</button></a>&nbsp;&nbsp;
        <a href='report_sales_by_inventory_summary.php'><button type="button" class="btn brand-btn mobile-block" >Sales by Inventory Summary</button></a>&nbsp;&nbsp;
        <a href='report_sales_by_product_service_detail.php'><button type="button" class="btn brand-btn mobile-block" >Sales by Inventory/Service Detail</button></a>&nbsp;&nbsp;
        <a href='report_customer_contact_list.php'><button type="button" class="btn brand-btn mobile-block active_tab" >Customer Contact List</button></a>&nbsp;&nbsp;
        <a href='report_payment_method_list.php'><button type="button" class="btn brand-btn mobile-block" >Payment Method List</button></a>&nbsp;&nbsp;
        <a href='report_transaction_list_by_customer.php'><button type="button" class="btn brand-btn mobile-block" >Transaction List by Customer</button></a>&nbsp;&nbsp;
        <a href='report_unbilled_charges.php'><button type="button" class="btn brand-btn mobile-block" >Unbilled Invoices</button></a>&nbsp;&nbsp;
        <a href='report_deposit_detail.php'><button type="button" class="btn brand-btn mobile-block" >Deposit Detail</button></a>&nbsp;&nbsp;
        -->

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            This report lists each customer's phone number(s), email and billing address, as well as their invoice (click the PDF icon to access their invoice), the invoice date and the invoice amount.</div>
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

    $report_validation = mysqli_query($dbc,"SELECT * FROM invoice WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."')");
    $num_rows = mysqli_num_rows($report_validation);

    if($num_rows > 0) {
        $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">
        <tr style="'.$table_row_style.'" nobr="true">
        <th width="15%">Customer Name</th>
        <th width="15%">Phone</th>
        <th width="20%">Email</th>
        <th width="20%">Address</th>
        <th width="10%">Invoice #</th>
        <th width="10%">Invoice Date</th>
        <th width="10%">Invoice Amount</th>
        </tr>';

        while($row_report = mysqli_fetch_array($report_validation)) {
            $patientid = $row_report['patientid'];
            $invoiceid = $row_report['invoiceid'];

            $report_data .= '<tr nobr="true">';
            //$report_data .= '<td>' . $row_report['invoice_date'] . '</td>';

			$report_data .= '<td><a href="../Contacts/add_contacts.php?category=Patient&contactid='.$patientid.'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">'.get_contact($dbc, $patientid). '</a></td>';
            $report_data .= '<td>' . get_contact_phone($dbc, $patientid) . '</td>';
            $report_data .= '<td>' . get_email($dbc, $patientid) . '</td>';
            $report_data .= '<td>' . get_address($dbc, $patientid) . '</td>';

            $report_data .= '<td>#'.$invoiceid;
            $name_of_file = '../Invoice/Download/invoice_'.$invoiceid.'.pdf';
            $report_data .= '&nbsp;&nbsp;<a href="'.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a></td>';

            $report_data .= '<td>'.$row_report['invoice_date'].'</td>';
            $report_data .= '<td>$'.$row_report['final_price'].'</td>';

            $report_data .= "</tr>";
        }

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