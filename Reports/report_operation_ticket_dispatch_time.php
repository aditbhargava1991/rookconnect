<?php /* Ticket Inventory Transport Report */
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');

error_reporting(0);

if (isset($_POST['printpdf'])) {
    $startpdf = $_POST['startpdf'];
    $endpdf = $_POST['endpdf'];

    DEFINE('START_DATE', $startpdf);
    DEFINE('END_DATE', $endpdf);
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
            $footer_text = TICKET_NOUN.' Travel Time <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE: Displays statistics of ".TICKET_NOUN." Travel Time ".START_DATE." to ".END_DATE.".";
            $this->writeHTMLCell(0, 0, 10 , 45, $footer_text, 0, 0, false, "R", true);
		}

		// Page footer
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

    $html .= report_tracking($dbc, $startpdf, $endpdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/download_tracker_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_operation_ticket_dispatch_time', 0, WEBSITE_URL.'/Reports/Download/download_tracker_'.$today_date.'.pdf', 'Travel Time Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/download_tracker_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $startdate = $startpdf;
    $enddate = $endpdf;
} ?>


		<?php if (isset($_POST['search_submit'])) {
			$startdate = $_POST['starttime'];
			$enddate = $_POST['endtime'];
			$businessid = $_POST['businessid'];
			$projectid = $_POST['projectid'];
		}
		if($startdate == 0000-00-00) {
			$startdate = date('Y-m-01');
		}
		if($enddate == 0000-00-00) {
			$enddate = date('Y-m-d');
		} ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Displays statistics of <?= TICKET_NOUN ?> Travel Time from <?= $startdate ?> to <?= $enddate ?>.</div>
            <div class="clearfix"></div>
        </div>
		<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

        <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">

            <center>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">From:</label>
					<div class="col-sm-8">
						<input name="starttime" type="text" class="datepicker form-control" value="<?php echo $startdate; ?>">
					</div>
				</div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Until:</label>
					<div class="col-sm-8">
						<input name="endtime" type="text" class="datepicker form-control" value="<?php echo $enddate; ?>">
					</div>
				</div>
				<button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
				<button type="submit" name="search_submit" value="Search" class="btn brand-btn pull-right">Submit</button>
			</center>

            <input type="hidden" name="startpdf" value="<?php echo $startdate; ?>">
            <input type="hidden" name="endpdf" value="<?php echo $enddate; ?>">

			<div class="clearfix"></div>

			<?php echo report_tracking($dbc, $startdate, $enddate, '', '', ''); ?>
<?php
function report_tracking($dbc, $startdate, $enddate, $table_style, $table_row_style, $grand_total_style) {
	$startdate = date('Y-m-d',strtotime($startdate));
	$enddate = date('Y-m-d',strtotime($enddate));
	$tickets = mysqli_query($dbc, "SELECT `tickets`.*, `tickets`.`to_do_date`, `tickets`.`est_time`, `tickets`.`est_distance`, `tickets`.`completed_time`, SEC_TO_TIME(TIME_TO_SEC(MIN(`next`.`start`)) - TIME_TO_SEC(`tickets`.`completed_time`)) `next_time` FROM `tickets` LEFT JOIN (SELECT `ticket_timer`.`start_time` `start`, `to_do_date` FROM `ticket_timer` LEFT JOIN `tickets` ON `ticket_timer`.`ticketid`=`tickets`.`ticketid` AND `ticket_timer`.`deleted` = 0 WHERE `tickets`.`deleted`=0 UNION SELECT `completed_time` `start`, `to_do_date` FROM `ticket_schedule` WHERE `ticket_schedule`.`deleted`=0 UNION SELECT `completed_time` `start`, `to_do_date` FROM `tickets` WHERE `tickets`.`deleted`=0) `next` ON `next`.`start` > `tickets`.`completed_time` AND `next`.`to_do_date`=`tickets`.`to_do_date` WHERE `completed_time` != '' GROUP BY `tickets`.`ticketid` UNION
		SELECT `tickets`.*, `ticket_schedule`.`to_do_date`, `ticket_schedule`.`est_time`, `ticket_schedule`.`est_distance`, `ticket_schedule`.`completed_time`, SEC_TO_TIME(TIME_TO_SEC(MIN(`next`.`start`)) - TIME_TO_SEC(`ticket_schedule`.`completed_time`)) `next_time` FROM `ticket_schedule` LEFT JOIN `tickets` ON `ticket_schedule`.`ticketid`=`tickets`.`ticketid` LEFT JOIN (SELECT `ticket_timer`.`start_time` `start`, `to_do_date` FROM `ticket_timer` LEFT JOIN `tickets` ON `ticket_timer`.`ticketid`=`tickets`.`ticketid` AND `ticket_timer`.`deleted` = 0 WHERE `tickets`.`deleted`=0 UNION SELECT `completed_time` `start`, `to_do_date` FROM `ticket_schedule` WHERE `ticket_schedule`.`deleted`=0 UNION SELECT `completed_time` `start`, `to_do_date` FROM `tickets` WHERE `tickets`.`deleted`=0) `next` ON `next`.`start` > `ticket_schedule`.`completed_time` AND `next`.`to_do_date`=`ticket_schedule`.`to_do_date` WHERE `ticket_schedule`.`completed_time` != '' GROUP BY `ticket_schedule`.`ticketid`");

	if(mysqli_num_rows($tickets) > 0) {
		$report_data = '<table border="1px" class="table table-bordered" style="'.$table_style.'">
			<tr style="'.$table_row_style.'" nobr="true">
				<th>Work Order</th>
				<th>'.TICKET_NOUN.' Date</th>
				<th>Estimated Time</th>
				<th>Estimated Distance</th>
				<th>Approximate Time</th>
			</tr>';
			while($row = mysqli_fetch_assoc($tickets)) {
				$ticket_label = get_ticket_label($dbc, $row);
				$project_label = $row['projectid'] > 0 ? get_project_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='".$row['projectid']."'"))) : '';
				$origin = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `ticketid`='".$row['ticketid']."' AND `deleted`=0 AND `type`='origin'"));
				$destination = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `ticketid`='".$row['ticketid']."' AND `deleted`=0 AND `type`='destination'"));
				$report_data .= '<tr style="'.$table_row_style.'" nobr="true">
						<td data-title="Work Order">'.$ticket_label.'</td>
						<td data-title="'.TICKET_NOUN.' Date">'.$row['to_do_date'].'</td>
						<td data-title="Estimated Time">'.$row['est_time'].'</td>
						<td data-title="Estimated Distance">'.$row['est_distance'].'</td>
						<td data-title="Approximate Time">'.$row['next_time'].'</td>
					</tr>';
			}
		$report_data .= '</table>';
	} else {
		$report_data = '<h3>No Records Found.</h3>';
	}
	return $report_data;
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