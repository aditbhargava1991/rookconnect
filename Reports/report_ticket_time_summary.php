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
    $as_at_datepdf = $_POST['as_at_datepdf'];

    DEFINE('START_DATE', $starttimepdf);
    DEFINE('END_DATE', $endtimepdf);
    DEFINE('AS_AT_DATE', $as_at_datepdf);
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
            $footer_text = 'View Report by Employee Name as at '.AS_AT_DATE.' Including Invoices '.(START_DATE > '0000-00-00' ? ' From <b>'.START_DATE.'</b> Until <b>'.END_DATE.'</b>' : 'Until <b>'.END_DATE.'</b>');
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : This report displays the total # of tickets, estimated time for each ticket and the total actual time tracked on each ticket, searchable by date range.";
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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);

    $html .= report_receivables($dbc, $starttimepdf, $endtimepdf, $as_at_datepdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/receivables_'.$today_date.'.pdf', 'F');

    track_download($dbc, 'report_ticket_time_summary', 0, WEBSITE_URL.'/Reports/Download/receivables_'.$today_date.'.pdf', 'Ticket Type Summary Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/receivables_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $as_at_date = $as_at_datepdf;
    } ?>

        <!--
        <br>
        <a href='report_receivables.php?type=Daily'><button type="button" class="btn brand-btn mobile-block active_tab" >By Invoice#</button></a>&nbsp;&nbsp;
        <a href='report_receivables_summary.php?type=Daily'><button type="button" class="btn brand-btn mobile-block" >Insurer Aging Receivable Summary</button></a>&nbsp;&nbsp;
        <a href='report_receivables_patient_summary.php?type=Daily'><button type="button" class="btn brand-btn mobile-block" >Patient Aging Receivable Summary</button></a>&nbsp;&nbsp;

        <a href='report_receivables_patient_paid_summary.php?type=Daily'><button type="button" class="btn brand-btn mobile-block" >Patient Paid Summary</button></a>&nbsp;&nbsp;
        -->

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Use this report to view the estimated time vs. total actual time spent per ticket by employee in a selected time frame, as well as the total # of tickets.</div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
				$as_at_date = $_POST['as_at'];
            }

            if(!empty($_GET['from'])) {
                $starttime = $_GET['from'];
            } else if($starttime == 0000-00-00) {
                $starttime = date('Y-m-d');
            }

            if(!empty($_GET['to'])) {
                $endtime = $_GET['to'];
            } else if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            }

            if(!empty($_GET['as_at_date'])) {
                $as_at_date = $_GET['as_at_date'];
            } else if($as_at_date == 0000-00-00) {
                $as_at_date = date('Y-m-d');
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
            <input type="hidden" name="as_at_datepdf" value="<?php echo $as_at_date; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                echo report_receivables($dbc, $starttime, $endtime, $as_at_date, '', '', '');

                if(!empty($_GET['from'])) {
                    echo '<a href="'.WEBSITE_URL.'/Reports/report_daily_sales_summary.php?from='.$_GET['from'].'&to='.$_GET['to'].'" class="btn brand-btn">Back</a>';
                }

            ?>

        </form>

<?php
function report_receivables($dbc, $starttime, $endtime, $as_at_date, $table_style, $table_row_style, $grand_total_style) {
    $report_data .= '<h3>Time By Employee</h3><table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="30%">Employee</th>
    <th width="15%"># of Tickets</th>
	<th width="15%">Ticket Ids</th>
    <th width="20%">Total Estimated Time</th>
    <th width="20%">Total Actual Time</th>
    </tr>';

    //$report_service = mysqli_query($dbc,"SELECT invoiceid, insurerid, insurance_payment, service_date, invoice_date FROM invoice WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND paid='Waiting on Insurer' AND insurance_payment != '#*#' ORDER BY insurerid");

	$start_date = $starttime;
	$end_date = $endtime;
	$query_check_credentials1 = "SELECT ticketid,spent_time,created_by FROM tickets where created_by is not null and to_do_date >= '$start_date' and to_do_end_date <= '$end_date'";
	$result1 = mysqli_query($dbc, $query_check_credentials1);
	$num_rows1 = mysqli_num_rows($result1);
	if($num_rows1 > 0) {
		$times = array();
		$add_times = 0;
		while($row1 = mysqli_fetch_array($result1)) {
			$created_by = $row1['created_by'];
			$ticket_result[$row1['created_by']]['manual_time'][] = $row1['spent_time'];
			$ticket_result[$row1['created_by']]['ticketid'][] = $row1['ticketid'];

			$tempTicketNumber = $row1['ticketid'];
			$query_check_credentials2 = "SELECT start_time,end_time,timer_type FROM ticket_timer where ticketid = $tempTicketNumber AND `deleted` = 0";
			$result2 = mysqli_query($dbc, $query_check_credentials2);
			while($row2 = mysqli_fetch_array($result2)) {
				if($row2['end_time'] != '' && $row2['timer_type'] == 'Work') {
					$to_time = strtotime($row2['start_time']);
					$from_time = strtotime($row2['end_time']);
					$times[] = round(abs($to_time - $from_time) / 60,2);
					$add_times += round(abs($to_time - $from_time),2);
				}
			}

			$hours = floor($add_times / 3600);
			$minutes = floor($add_times % 3600 / 60);
			$seconds = $add_times % 60;
			if($hours <= 10) {
				$hours = '0' . $hours;
			}
			if($minutes <= 10) {
				$minutes = '0' . $minutes;
			}
			if($seconds <= 10) {
				$seconds = '0' . $seconds;
			}

			$ticket_result[$row1['created_by']]['actual_time'][] = $hours . ':' . $minutes . ':' . $seconds;
		}
	}

	foreach($ticket_result as $created_by => $ticket_details) {
		$contact_name = get_contact($dbc, $created_by);
		if($contact_name != null && $contact_name != '' && $contact_name != '-') {
			$report_data .= '<tr nobr="true">';
			$report_data .= '<td>'.$contact_name.'</td>';
			$ticket_count = count($ticket_details['ticketid']);
			$report_data .= '<td>'.$ticket_count.'</td>';
			$report_data .= '<td>';
			foreach($ticket_details['ticketid'] as $subticketid)
				$report_data .= $subticketid.'<br>';
			$report_data .= '</td>';
			$report_data .= '<td>';
			foreach($ticket_details['actual_time'] as $actualtime)
				$report_data .= $actualtime.'<br>';
			$report_data .= '</td>';
            $report_data .= '<td>';
            foreach($ticket_details['manual_time'] as $manualtime)
                $report_data .= $manualtime.'<br>';
            $report_data .= '</td>';
			$report_data .= '</tr>';
		}
	}

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