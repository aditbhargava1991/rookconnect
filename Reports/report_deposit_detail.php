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
    $payment_typepdf = $_POST['payment_typepdf'];
    $insurerpdf = $_POST['insurerpdf'];

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
            $footer_text = 'Deposit Detail Paid Date From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : Displays detailed information about deposits received from Insurers, including the date deposited, the insurer and the amount deposited.";
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

    $html .= report_receivables($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '', $insurerpdf, $payment_typepdf);

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/deposit_detail_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_deposit_detail', 0, WEBSITE_URL.'/Reports/Download/deposit_detail_'.$today_date.'.pdf', 'Deposit Detail Paid Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/deposit_detail_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $payment_type = $payment_typepdf;
    $insurer = $insurerpdf;
} ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Displays detailed information about deposits received from Insurers, including the date deposited, the insurer and the amount deposited.</div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

           <?php
            if(!empty($_GET['p1'])) {
                $starttime = $_GET['p1'];
                $endtime = $_GET['p2'];
                $insurer = $_GET['p3'];
                $payment_type = $_GET['p7'];
            }
            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
                $insurer = $_POST['insurer'];
                $payment_type = $_POST['payment_type'];
            }
            if (isset($_POST['search_email_all'])) {
                $starttime = date('Y-m-01');
                $endtime = date('Y-m-d');
                $insurer = '';
                $payment_type = '';
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
					<label class="col-sm-4"><span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Here is where you select the date range of the invoice. The date range must be large enough so that the invoice will populate."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						From Paid Date:</label>
					<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Until Paid Date:</label>
					<div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
				</div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Insurer:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select an Insurer..." name="insurer" class="chosen-select-deselect form-control1" width="380">
							<option value="">Select All</option>
							<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, name FROM contacts WHERE category='Insurer' AND deleted=0 AND status=1"),MYSQLI_ASSOC));
							foreach($query as $rowid) {
								echo "<option ".($rowid == $insurer ? 'selected' : '')." value='$rowid'>".get_client($dbc, $rowid)."</option>";
							} ?>
						</select>
					</div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Paid Type:</label>
					<div class="col-sm-8">
						<select data-placeholder="Choose a Type..." name="payment_type" class="chosen-select-deselect form-control">
							<option value="">Display All</option>
							<option <?php if ($payment_type=='Transfer') echo 'selected="selected"';?> value="Transfer">Transfer</option>
							<option <?php if ($payment_type=='EFT') echo 'selected="selected"';?> value="EFT">EFT</option>
							<option <?php if ($payment_type=='Cheque') echo 'selected="selected"';?> value="Cheque">Cheque</option>
						</select>
					</div>
                </div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
			<span class="popover-examples list-inline" style="margin:0 0 0 10px;"><a data-toggle="tooltip" data-placement="top" title="Select this to remove all of the search filters you've applied. It will revert back to today's invoices."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <button type="submit" name="search_email_all" value="Search" class="btn brand-btn mobile-block">Display Default</button></div></center>

            </div>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="insurerpdf" value="<?php echo $insurer; ?>">
            <input type="hidden" name="payment_typepdf" value="<?php echo $payment_type; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>

            <?php
                echo report_receivables($dbc, $starttime, $endtime, '', '', '', $insurer, $payment_type);
            ?>

        </form>
        
<?php
function report_receivables($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $insurer, $payment_type) {

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="40%">Insurer</th>
    <th width="10%">Amount</th>
    <th width="10%">Paid Type</th>
    <th width="20%">Number</th>
    <th width="10%">Date Deposited</th>
    <th width="10%">Paid Date</th>
    </tr>';

    if($insurer != '') {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE (DATE(ii.paid_date) >= '".$starttime."' AND DATE(ii.paid_date) <= '".$endtime."') AND ii.insurerid='$insurer' AND ii.invoiceid = i.invoiceid AND ii.paid='Yes' ORDER BY ii.invoiceid");
    } else if($payment_type != '') {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE (DATE(ii.paid_date) >= '".$starttime."' AND DATE(ii.paid_date) <= '".$endtime."') AND ii.paid='Yes' AND ii.invoiceid = i.invoiceid AND ii.paid_type='$payment_type' ORDER BY ii.invoiceid");
    } else {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE (DATE(ii.paid_date) >= '".$starttime."' AND DATE(ii.paid_date) <= '".$endtime."') AND ii.invoiceid = i.invoiceid AND ii.paid='Yes' ORDER BY ii.invoiceid");
    }

    $amt_to_bill = 0;
    $total = 0;
    $odd_even=0;
    while($row_report = mysqli_fetch_array($report_service)) {
        $bg_class = $odd_even % 2 == 0 ? '' : 'background-color:#e6e6e6;';
        $insurer_price = $row_report['insurer_price'];
        $invoiceid = $row_report['invoiceid'];
        $patientid = get_all_from_invoice($dbc, $invoiceid, 'patientid');
        $insurerid = rtrim($row_report['insurerid'],',');

        $each_insurance_payment = explode('#*#', $insurance_payment);
        $report_data .= '<tr nobr="true" style="'.$bg_class.'">';
        //$report_data .= '<td>#'.$invoiceid.' : '.get_contact($dbc, $patientid).'</td>';
        //$report_data .= '<td>#'.$row_report['ui_invoiceid'].'</td>';
        //$report_data .= '<td>'.$row_report['service_date'].'</td>';
        //$report_data .= '<td>'.$row_report['invoice_date'].'</td>';

		$report_data .= '<td><a href="../Contacts/add_contacts.php?category=Insurer&contactid='.$insurerid.'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">'.get_all_form_contact($dbc, $insurerid, 'name'). '</a></td>';
        $report_data .= '<td>$'.$insurer_price.'</td>';
        $report_data .= '<td>'.$row_report['paid_type'].'</td>';
        $report_data .= '<td>'.$row_report['deposit_number'].'</td>';
        $report_data .= '<td>'.$row_report['date_deposit'].'</td>';
        $report_data .= '<td>'.$row_report['paid_date'].'</td>';
        $report_data .= '</tr>';
        $total += $insurer_price;
        $odd_even++;
    }

    $report_data .= '<tr nobr="true"><td><b>Total</b></td><td><b>$'.$total.'</b></td><td></td><td></td><td></td><td></td></tr>';
    $report_data .= '</table><br>';

    return $report_data;
}

?>