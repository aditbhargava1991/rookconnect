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
            $footer_text = "NOTE : Displays a list of Unassigned/Error Invoices with how much is not assigned to either a Customer or an Insurer for the selected date range.";
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
	$pdf->Output('Download/sales_summary_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_unassigned_invoices', 0, WEBSITE_URL.'/Reports/Download/sales_summary_'.$today_date.'.pdf', 'Sales Summary by Injury Type Report');

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
            Displays a list of Unassigned/Error Invoices with how much is not assigned to either a Customer or an Insurer for the selected date range.</div>
            <div class="clearfix"></div>
        </div>
        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if(!empty($_GET['start'])) {
                $starttime = $_GET['start'];
                $endtime = $_GET['end'];
            }
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

                echo report_sales_summary($dbc, $starttime, $endtime, '', '', '');
            ?>
        </form>

<?php
function report_sales_summary($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style) {

    $result5 = mysqli_query($dbc, "SELECT patientid,invoiceid,final_price,total_price, invoice_date, injuryid,insurance_payment,payment_type,total_price FROM invoice WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."'");

	$report_data .= '<h1>Unassigned Payments</h1>';
    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="10%">Invoice #</th>
    <th width="10%">Invoice Date</th>
    <th width="20%">Customer</th>
    <th width="30%">Injury</th>
    <th width="10%">Invoice Sub Total</th>
    <th width="10%">Invoice Total</th>
    <th width="18%">Unassigned Amount</th>
    </tr>';
    $ftotal = 0;
    while($row5 = mysqli_fetch_array($result5)) {
        $invoiceid = $row5['invoiceid'];
        $final_price = $row5['final_price'];
        $total_price = $row5['total_price'];
        $insurance_payment = $row5['insurance_payment'];
        $payment_type = $row5['payment_type'];

        $total_insurer = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(insurer_price) as `total_insurer_price`, SUM(sub_total) as `sub_total_insurer_price` FROM invoice_insurer WHERE invoiceid = '$invoiceid'"));
        $total_patient = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) as `total_patient_price`, SUM(sub_total) as `sub_total_patient_price` FROM invoice_patient WHERE invoiceid = '$invoiceid'"));
        $total_each = $total_insurer['total_insurer_price']+$total_patient['total_patient_price'];
		$total_gst = $total_each - $total_insurer['sub_total_insurer_price']-$total_patient['sub_total_patient_price'];

        if($total_price != $total_insurer['sub_total_insurer_price']+$total_patient['sub_total_patient_price']) {
            $report_data .= '<tr nobr="true">';
            $report_data .= '<td><a href=\''.WEBSITE_URL.'/Invoice/create_invoice.php?invoiceid='.$row5['invoiceid'].'&patientid='.$row5['patientid'].'&from=report&report_from='.$starttime.'&report_to='.$endtime.'\' >'.$row5['invoiceid'].'</a>';

            //$report_data .= '<td>'.$row5['invoiceid'].'';
            $name_of_file = '/Invoice/Download/invoice_'.$row5['invoiceid'].'.pdf';
			if(file_exists('..'.$name_of_file)) {
				$report_data .= '&nbsp;&nbsp;<a href="'.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a>';
			}
			$report_data .= '</td>';

            $report_data .= '<td>'.$row5['invoice_date'].'</td>';
			$report_data .= '<td><a href="'.WEBSITE_URL.'/Contacts/contacts_inbox.php?edit='.$row5['patientid'].'">'.get_contact($dbc, $row5['patientid']). '</a></td>';
            $report_data .= '<td>' . get_all_from_injury($dbc, $row5['injuryid'], 'injury_name').' : '.get_all_from_injury($dbc, $row5['injuryid'], 'injury_type'). '</td>';
            $report_data .= '<td>$'.$row5['total_price'].'</td>';
            $report_data .= '<td>$'.$row5['final_price'].'</td>';
            $report_data .= '<td>$'.number_format(($final_price-$total_each),2).'</td>';

            $report_data .= '</tr>';

            $error_in = ($final_price-$total_each);
            $ftotal += $error_in;
        }

    }

    $report_data .= '<tr nobr="true"><td>Total</td><td></td><td></td><td></td><td></td><td></td><td>'.number_format($ftotal,2).'</td></tr>';
    $report_data .= '</table>';


	$invoice_list = mysqli_query($dbc, "SELECT * FROM `invoice` WHERE (`invoice`.`invoice_date` >= '".$starttime."' AND `invoice`.`invoice_date` <= '".$endtime."')");
	$un_payment = 0;
	$un_service = 0;
	$un_total = 0;

	$report_data .= '<h1>Unassigned Services / Inventory</h1>';
    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="10%">Invoice #</th>
    <th width="10%">Invoice Date</th>
    <th width="20%">Customer</th>
    <th width="30%">Injury</th>
    <th width="10%">Invoice Total</th>
    <th width="18%">Unassigned Amount</th>
    </tr>';
	$un_service = 0;
	while($invoice_row = mysqli_fetch_array($invoice_list)) {
		$row_un_service = 0;
		$services_inventory = 0;
		foreach(explode(',',$invoice_row['serviceid']) as $this_row => $this_id) {
			if($this_id > 0) {
				$services_inventory += explode(',', $invoice_row['fee'])[$this_row];
				$total_services += explode(',', $invoice_row['fee'])[$this_row];
			}
		}
		foreach(explode(',',$invoice_row['inventoryid']) as $this_row => $this_id) {
			if($this_id > 0) {
				$services_inventory += explode(',', $invoice_row['sell_price'])[$this_row];
				$total_inventory += explode(',', $invoice_row['sell_price'])[$this_row];
			}
		}
		foreach(explode(',',$invoice_row['packageid']) as $this_row => $this_id) {
			if($this_id > 0) {
				$services_inventory += explode(',', $invoice_row['package_cost'])[$this_row];
				$total_packages += explode(',', $invoice_row['package_cost'])[$this_row];
			}
		}
		foreach(explode(',',$invoice_row['productid']) as $this_row => $this_id) {
			if($this_id > 0) {
				$services_inventory += explode(',', $invoice_row['product_price'])[$this_row];
				$total_product += explode(',', $invoice_row['product_price'])[$this_row];
			}
		}
		foreach(explode(',',$invoice_row['misc_item']) as $this_row => $this_id) {
			if(!empty($this_id)) {
				$services_inventory += explode(',', $invoice_row['misc_price'])[$this_row];
				$total_misc += explode(',', $invoice_row['misc_price'])[$this_row];
			}
		}
		if($invoice_row['total_price'] != $services_inventory) {
			$report_data .= '<tr nobr="true">';
			$report_data .= '<td><a href=\''.WEBSITE_URL.'/Invoice/create_invoice.php?invoiceid='.$invoice_row['invoiceid'].'&patientid='.$invoice_row['patientid'].'&from=report&report_from='.$starttime.'&report_to='.$endtime.'\' >'.$invoice_row['invoiceid'].'</a>';

			$name_of_file = '/Invoice/Download/invoice_'.$invoice_row['invoiceid'].'.pdf';
			if(file_exists('..'.$name_of_file)) {
				$report_data .= '&nbsp;&nbsp;<a href="'.WEBSITE_URL.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a>';
			}
			$report_data .= '</td>';

			$report_data .= '<td>'.$invoice_row['invoice_date'].'</td>';
			$report_data .= '<td><a href="'.WEBSITE_URL.'/Contacts/contacts_inbox.php?edit='.$invoice_row['patientid'].'">'.get_contact($dbc, $invoice_row['patientid']). '</a></td>';
			$report_data .= '<td>' . get_all_from_injury($dbc, $invoice_row['injuryid'], 'injury_name').' : '.get_all_from_injury($dbc, $invoice_row['injuryid'], 'injury_type'). '</td>';
			$report_data .= '<td>$'.$invoice_row['total_price'].'</td>';
			$report_data .= '<td>$'.number_format(($invoice_row['total_price'] - $services_inventory),2).'</td>';

			$report_data .= '</tr>';
		}
		$un_service += $invoice_row['total_price'] - $services_inventory;
	}

    $report_data .= '<tr nobr="true"><td>Total</td><td></td><td></td><td></td><td></td><td>'.number_format($un_service,2).'</td></tr>';
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