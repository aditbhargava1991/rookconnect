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
            $footer_text = 'Pro-Bono Summary From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : Summarizes each item provided Pro-Bono.";
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

    $html .= report_daily_validation($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '', 1);

    $today_date = date('Y-m-d');
	$pdf->writeHTML(utf8_encode($html), true, false, true, false, '');
	$pdf->Output('Download/pro_bono_summary_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_marketing_pro_bono', 0, WEBSITE_URL.'/Reports/Download/pro_bono_summary_'.$today_date.'.pdf', 'Pro-Bono Summary Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/pro_bono_summary_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    } ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Summarizes each item provided Pro-Bono.</div>
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
					<label class="col-sm-4">Invoice Date From:</label>
					<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
				</div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Invoice Date Until:</label>
					<div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
				</div>
			<button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <div id="no-more-tables"><?php echo report_daily_validation($dbc, $starttime, $endtime, '', '', ''); ?></div>

        </form>
<?php
function report_daily_validation($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $pdf = '0') {
	$report_pro_bono = mysqli_query($dbc,"SELECT invoiceid, invoice_date, therapistsid, patientid, serviceid, service_pro_bono, inventoryid, inventory_pro_bono, packageid, package_pro_bono, productid, product_pro_bono, misc_item, misc_pro_bono FROM invoice WHERE (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')");
	if(mysqli_num_rows($report_pro_bono) > 0) {
		$total_pro_bono = 0;
		$report_data = '<table border="1" class="table table-bordered" style="'.$table_style.'">';
		$report_data .= '<tr class="'.($pdf == 0 ? 'hidden-sm hidden-xs' : '').'" style="'.$table_row_style.'">
			<th>Service or Inventory</th>
			<th>Invoice #</th>
			<th>Invoice Date</th>
			<th>Therapist</th>
			<th>Patient</th>
			<th>Value (including GST)</th></tr>';

		while($row = mysqli_fetch_array($report_pro_bono)) {
			$get_staff = true;

			$services = explode(',',$row['serviceid']);
			$spb = explode(',',$row['service_pro_bono']);
			$inventory = explode(',',$row['inventoryid']);
			$ipb = explode(',',$row['inventory_pro_bono']);
			$packages = explode(',',$row['packageid']);
			$ppb = explode(',',$row['package_pro_bono']);
			$products = explode(',',$row['productid']);
			$prodpb = explode(',',$row['product_pro_bono']);
			$miscs = explode(',',$row['misc_item']);
			$mpb = explode(',',$row['misc_pro_bono']);
			foreach($services as $i => $sid) {
				if(!empty($spb[$i])) {
					if($get_staff) {
						$staff = get_contact($dbc, $row['therapistsid']);
						$contact = get_contact($dbc, $row['patientid']);
						$get_staff = false;
					}
					$service = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `services` WHERE `serviceid`='$sid'"));
					$report_data .= '<tr nobr="true">
						<td data-title="Service">'.(!empty($service['category']) ? $service['category'].': ' : '').$service['heading'].'</td>
						<td data-title="Invoice #"><a title="Invoice #'.$row['invoiceid'].'" href="'.WEBSITE_URL.'/Invoice/Download/invoice_'.$row['invoiceid'].'.pdf" target="_blank">Invoice #'.$row['invoiceid'].' <img src="'.WEBSITE_URL.'/img/pdf.png"></a></td>
						<td data-title="Invoice Date">'.$row['invoice_date'].'</td>
						<td data-title="Therapist">'.$staff.'</td>
						<td data-title="Patient">'.$contact.'</td>
						<td data-title="Value (including GST)">$'.number_format($spb[$i],2).'</td></tr>';
					$total_pro_bono += $spb[$i];
				}
			}
			foreach($inventory as $i => $iid) {
				if(!empty($ipb[$i])) {
					if($get_staff) {
						$staff = get_contact($dbc, $row['therapistsid']);
						$contact = get_contact($dbc, $row['patientid']);
						$get_staff = false;
					}
					$inventory = mysqli_fetch_array(mysqli_query($dbc, "SELECT `name` FROM `inventory` WHERE `inventoryid`='$iid'"));
					$report_data .= '<tr nobr="true">
						<td data-title="Inventory">'.(!empty($inventory['category']) ? $inventory['category'].': ' : '').$inventory['heading'].'</td>
						<td data-title="Invoice #"><a title="Invoice #'.$row['invoiceid'].'" href="'.WEBSITE_URL.'/Invoice/Download/invoice_'.$row['invoiceid'].'.pdf">Invoice #'.$row['invoiceid'].' <img src="'.WEBSITE_URL.'/img/pdf.png"></a></td>
						<td data-title="Invoice Date">'.$row['invoice_date'].'</td>
						<td data-title="Therapist">'.$staff.'</td>
						<td data-title="Patient">'.$contact.'</td>
						<td data-title="Value (including GST)">$'.number_format($ipb[$i],2).'</td></tr>';
					$total_pro_bono += $ipb[$i];
				}
			}
			foreach($packages as $i => $pid) {
				if(!empty($ppb[$i])) {
					if($get_staff) {
						$staff = get_contact($dbc, $row['therapistsid']);
						$contact = get_contact($dbc, $row['patientid']);
						$get_staff = false;
					}
					$package = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `package` WHERE `packageid`='$pid'"));
					$report_data .= '<tr nobr="true">
						<td data-title="Package">'.(!empty($package['category']) ? $package['category'].': ' : '').$package['heading'].'</td>
						<td data-title="Invoice #"><a title="Invoice #'.$row['invoiceid'].'" href="'.WEBSITE_URL.'/Invoice/Download/invoice_'.$row['invoiceid'].'.pdf">Invoice #'.$row['invoiceid'].' <img src="'.WEBSITE_URL.'/img/pdf.png"></a></td>
						<td data-title="Invoice Date">'.$row['invoice_date'].'</td>
						<td data-title="Therapist">'.$staff.'</td>
						<td data-title="Patient">'.$contact.'</td>
						<td data-title="Value including GST">$'.number_format($ppb[$i],2).'</td></tr>';
					$total_pro_bono += $ppb[$i];
				}
			}
			foreach($products as $i => $pid) {
				if(!empty($ppb[$i])) {
					if($get_staff) {
						$staff = get_contact($dbc, $row['therapistsid']);
						$contact = get_contact($dbc, $row['patientid']);
						$get_staff = false;
					}
					$product = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `product` WHERE `product`='$pid'"));
					$report_data .= '<tr nobr="true">
						<td data-title="Product">'.(!empty($product['category']) ? $product['category'].': ' : '').$product['heading'].'</td>
						<td data-title="Invoice #"><a title="Invoice #'.$row['invoiceid'].'" href="'.WEBSITE_URL.'/Invoice/Download/invoice_'.$row['invoiceid'].'.pdf">Invoice #'.$row['invoiceid'].' <img src="'.WEBSITE_URL.'/img/pdf.png"></a></td>
						<td data-title="Invoice Date">'.$row['invoice_date'].'</td>
						<td data-title="Therapist">'.$staff.'</td>
						<td data-title="Patient">'.$contact.'</td>
						<td data-title="Value including GST">$'.number_format($prodpb[$i],2).'</td></tr>';
					$total_pro_bono += $prodpb[$i];
				}
			}
			foreach($miscs as $i => $misc) {
				if(!empty($ppb[$i])) {
					if($get_staff) {
						$staff = get_contact($dbc, $row['therapistsid']);
						$contact = get_contact($dbc, $row['patientid']);
						$get_staff = false;
					}
					$report_data .= '<tr nobr="true">
						<td data-title="Item">'.$misc.'</td>
						<td data-title="Invoice #"><a title="Invoice #'.$row['invoiceid'].'" href="'.WEBSITE_URL.'/Invoice/Download/invoice_'.$row['invoiceid'].'.pdf">Invoice #'.$row['invoiceid'].' <img src="'.WEBSITE_URL.'/img/pdf.png"></a></td>
						<td data-title="Invoice Date">'.$row['invoice_date'].'</td>
						<td data-title="Therapist">'.$staff.'</td>
						<td data-title="Patient">'.$contact.'</td>
						<td data-title="Value including GST">$'.number_format($mpb[$i],2).'</td></tr>';
					$total_pro_bono += $mpb[$i];
				}
			}
		}

		$report_data .= '<tr nobr="true"><td colspan="5"><b>Total</b></td>';
		$report_data .= '<td><b>$'.number_format($total_pro_bono, 2).'</b></td></tr></table>';

		return $report_data;
	} else {
		return "<h2>No Invoices Found</h2>";
	}
} ?>
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