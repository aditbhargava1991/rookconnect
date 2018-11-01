<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['printpdf'])) {
    $patientpdf = $_POST['patientpdf'];

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
            $footer_text = 'Transaction List by Customer : <b>'.PATIENT.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : Groups transactions by customer name, so you can see all activity related to each customer.";
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

    $html .= report_daily_validation($dbc, $patientpdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/transaction_list_by_customer_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_transaction_list_by_customer', 0, WEBSITE_URL.'/Reports/Download/transaction_list_by_customer_'.$today_date.'.pdf', 'Transaction List by Customer Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/transaction_list_by_customer_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $patient = $patientpdf;
    } ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Groups transactions by customer name, so you can see all activity related to each customer.</div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if (isset($_POST['search_email_submit'])) {
                $patient = $_POST['patient'];
            }

            ?>
            <center><div class="form-group">
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Customer:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select a Customer..." name="patient" class="chosen-select-deselect form-control">
							<option value=""></option>
							<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `contactid` IN (SELECT `patientid` FROM `invoice_patient` WHERE `paid` IS NULL OR `paid`='' OR `paid`='On Account')"),MYSQLI_ASSOC));
							foreach($query as $row) {
								echo "<option ".($row==$patient ? 'selected' : '')." value='$row'>".get_contact($dbc, $row)."</option>";
							} ?>
						</select>
					</div>
                </div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="patientpdf" value="<?php echo $patient; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                //echo '<a href="report_referral.php?referral=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a></h4><br>';

                if($patient != '') {
                    echo report_daily_validation($dbc, $patient, '', '', '');
                }
            ?>

        </form>
        
<?php
function report_daily_validation($dbc, $patient, $table_style, $table_row_style, $grand_total_style) {

    $report_data = '';

    $report_service = mysqli_query($dbc,"SELECT * FROM invoice WHERE patientid='$patient' ORDER BY invoiceid DESC");

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="10%">Invoice #</th>
    <th width="10%">Invoice Date</th>
    <th width="18%">Customer</th>
    <th width="30%">Injury</th>
    <th width="10%">Invoice Total</th>
    <th width="20%">Payment Type</th>
    </tr>';

    $amt_to_bill = 0;
    while($row_report = mysqli_fetch_array($report_service)) {
        $invoiceid = $row_report['invoiceid'];

        $report_data .= '<tr nobr="true">';
        $report_data .= '<td>#'.$invoiceid;
        $name_of_file = '../Invoice/Download/invoice_'.$invoiceid.'.pdf';
        $report_data .= '&nbsp;&nbsp;<a href="'.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a></td>';

        $report_data .= '<td>'.$row_report['invoice_date'].'</td>';
		$report_data .= '<td><a href="../Contacts/add_contacts.php?category=Patient&contactid='.$row_report['patientid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">'.get_contact($dbc, $row_report['patientid']). '</a></td>';
        $report_data .= '<td>' . get_all_from_injury($dbc, $row_report['injuryid'], 'injury_name').' : '.get_all_from_injury($dbc, $row_report['injuryid'], 'injury_type'). '</td>';

        $report_data .= '<td>$'.$row_report['final_price'].'</td>';

        $arr = explode("#*#", $row_report['payment_type']);
        if(rtrim($arr[0],',') == '') {
            $pt = 'By Insurer';
        } else {
            $pt = rtrim($arr[0],',');
        }
        $report_data .= '<td>'.$pt.'</td>';

        $report_data .= '</tr>';
        $amt_to_bill += $row_report['final_price'];
    }

    $report_data .= '<tr nobr="true">';
    $report_data .= '<td><b>Total</b></td><td></td><td></td><td></td><td><b>$'.number_format($amt_to_bill, 2).'</b></td><td></td>';
    $report_data .= "</tr>";
    $report_data .= '</table><br>';

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