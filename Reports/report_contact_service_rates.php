<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');

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
            $footer_text = "NOTE : This report lists each customer's services, and rates for those services.";
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

    $html .= report_generate($dbc, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/contact_service_rates_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'reports_contact_service_rates', 0, WEBSITE_URL.'/Reports/Download/contact_service_rates_'.$today_date.'.pdf', 'Customer Contact List From Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/contact_service_rates_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
} ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            This report lists each customer's services, and rates for those services.</div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <!--<input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

           <?php /* if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
            }

            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-01');
            }

            if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            } */ ?>
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
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">-->

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                //echo '<a href="report_referral.php?referral=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a></h4><br>';

                echo report_generate($dbc);
            ?>

        </form>
<?php
function report_generate($dbc, $table_style = '', $table_row_style = '', $grand_total_style = '') {
	$total_hrs = 0;
	$total_rate = 0;
	$report = '<table border="1px" class="table table-bordered" style="'.$table_style.'">
        <tr style="'.$table_row_style.'" nobr="true">
			<th width="15%">Name</th>
			<th width="40%">Services</th>
			<th width="15%">Total Hours</th>
			<th width="15%">Total Rate</th>
			<th width="15%">Per Hour Rate</th>
        </tr>';
	foreach(sort_contacts_query($dbc->query("SELECT `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`name`, `contacts`.`contactid`, `contacts_cost`.`total_rate`, `rate_card`.`services` FROM `contacts` LEFT JOIN `contacts_cost` ON `contacts`.`contactid`=`contacts_cost`.`contactid` LEFT JOIN `rate_card` ON `contacts`.`contactid`=`rate_card`.`clientid` AND DATE(NOW()) BETWEEN `rate_card`.`start_date` AND IFNULL(NULLIF(`rate_card`.`end_date`,'0000-00-00'),'9999-12-31') WHERE `contacts`.`deleted`=0 AND `contacts`.`status` > 0 AND `rate_card`.`clientid` IS NOT NULL")) as $contact) {
		$rate = 0;
		$hours = 0;
		$report .= '<tr>
				<td data-title="Name">'.$contact['name'].($contact['name'] != '' && ($contact['first_name'].$contact['last_name']) != '' ? ': ' : '').$contact['first_name'].' '.$contact['last_name'].'</td>
				<td data-title="Services">';
		foreach(explode('**',$contact['services']) as $service_line) {
			$service_line = explode('#',$service_line);
			if($service_line[0] > 0) {
				$service = $dbc->query("SELECT * FROM `services` WHERE `serviceid`='{$service_line[0]}'")->fetch_assoc();
				$service_rate = $service_line[1] * 1;
				$service_hours = time_time2decimal($service['estimated_hours']);
				$hours += $service_hours > 0 ? $service_hours : ($service_rate > 0 ? 1 : 0);
				$report .= (($service['category'].$service['service_type']) != '' ? $service['category'].($service['category'] != '' && $service['service_type'] != '' ? ' - ' : '').$service['service_type'].': ' : '').$service['heading'].': $'.number_format($service_rate,2).($service_hours > 0 ? ' @ '.$service['estimated_hours'] : '').'<br />';
				$rate += $service_rate;
			}
		}
		$report .= '</td>
				<td data-title="Hours">'.time_decimal2time($hours).'</td>
				<td data-title="Rates">$'.number_format($rate,2).'</td>
				<td data-title="Per Hour Rate">$'.number_format($rate / $hours,2).'</td>
			</tr>';
		$total_hrs += $hours;
		$total_rate += $rate;
	}
	$report .= '<tr>
			<td>Totals</td>
			<td></td>
			<td data-title="Total Hours">'.time_decimal2time($total_hrs).'</td>
			<td data-title="Total Rates">$'.number_format($total_rate,2).'</td>
			<td data-title="Per Hour Rate">$'.number_format($total_rate / $total_hrs,2).' / hr</td>
		</tr>
	</table>';
	return $report;
}

?>