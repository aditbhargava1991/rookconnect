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
            $footer_text = 'Estimates From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            //$this->writeHTMLCell(0, 0, 10 , 45, $footer_text, 0, 0, false, "R", true);
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

    $html .= report_estimates($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/report_estimates_'.$today_date.'.pdf', 'F');

    track_download($dbc, 'report_estimates', 0, WEBSITE_URL.'/Reports/Download/report_estimates_'.$today_date.'.pdf', 'Estimates Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/report_estimates_<?php echo  $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
} ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            The report displays Estimates for the selected date range.</div>
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

            <input type="hidden" name="starttimepdf" value="<?php echo  $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo  $endtime; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                echo  report_estimates($dbc, $starttime, $endtime, '', '', '');
            ?>
        </form>


<?php
function report_estimates($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style) {
    $report_data = '';
    $report_data .=  '<table border="1px" class="table table-bordered" style="'.$table_style.'">';

    $report_data .=  '<tr style="'.$table_row_style.'">';
    $report_data .=  '<th width="5%">ID#</th>';
    $report_data .=  '<th width="25%">Name</th>';
    $report_data .=  '<th width="15%">Business</th>';
    $report_data .=  '<th width="15%">Contact</th>';
    $report_data .=  '<th width="15%">Staff</th>';
    $report_data .=  '<th width="15%">Status</th>';
    $report_data .=  '<th width="5%">Source</th>';
    $report_data .=  '<th width="5%">$ Total</th>';
    $report_data .=  '</tr>';

	$estimate_list = mysqli_query($dbc, "SELECT estimateid, estimate_name, businessid, clientid, assign_staffid, status, total_price FROM `estimate` WHERE `deleted`=0 AND `created_date` BETWEEN '$starttime' AND '$endtime'");
	while($row = mysqli_fetch_assoc($estimate_list)) {
		$report_data .= '<tr>
			<td data-title="Estimate Name">#'.$row['estimateid'].'</td>
			<td data-title="Estimate Name">'.$row['estimate_name'].'</td>
			<td data-title="Estimate Name">'.get_client($dbc, $row['businessid']).'</td>
			<td data-title="Estimate Name">'.get_contact($dbc, $row['clientid']).'</td>
			<td data-title="Created Date">'.get_multiple_contact($dbc, $row['assign_staffid']).'</td>
			<td data-title="Status">'.$row['status'].'</td>
			<td data-title="Status">-</td>
			<td data-title="Status">'.$row['total_price'].'</td>
		</tr>';
	}

    $report_data .=  '</table>';

    return $report_data;

}