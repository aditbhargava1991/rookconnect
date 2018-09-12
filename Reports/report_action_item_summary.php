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
            $footer_text = 'Action Item Summary Report';
            $this->writeHTMLCell(0, 0, 0 , 40, $footer_text, 0, 0, false, "R", true);
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

    $html .= report_receivables($dbc, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;',$starttimepdf, $endtimepdf);

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/action_item_summary_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_action_item_summary', 0, WEBSITE_URL.'/Reports/Download/action_item_summary_'.$today_date.'.pdf', 'Action Item Summary');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/action_item_summary_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
} ?>

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
				<!--
                <div class="form-group col-sm-5">
					<label class="col-sm-4">Staff:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select a Staff..." name="contactid" class="chosen-select-deselect form-control1" width="380">
							<option value="">Select All</option>
							<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND role != 'super' AND `deleted`=0 AND `status`=1"),MYSQLI_ASSOC));
							foreach($query as $rowid) {
								echo "<option ".($rowid == $contactid ? 'selected' : '')." value='$rowid'>".get_contact($dbc, $rowid)."</option>";
							} ?>
						</select>
					</div>
                </div>
                -->
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="contactidpdf" value="<?php echo $contactid; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                    echo report_receivables($dbc, '', '', '', $starttime, $endtime);
            ?>

        </form>

<?php
function report_receivables($dbc, $table_style, $table_row_style, $grand_total_style, $starttime, $endtime) {

    $report_data = '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="6%">'.TICKET_NOUN.'#</th>
    <th width="30%">Heading</th>
    <th width="15%">Business</th>
    <th width="15%">Staff</th>
    <th width="10%">Scheduled Date</th>
    <th width="7%">Estimated Dev. Time</th>
    <th width="6%">QA Time</th>
    <th width="6%">Actual Time</th>
    <th width="5%">Budget</th>
    </tr>';

        $report_validation = mysqli_query($dbc,"SELECT ticketid FROM tickets WHERE ((to_do_date >= '".$starttime."' AND to_do_date <= '".$endtime."') OR (internal_qa_date = '".$starttime."') OR (deliverable_date = '".$starttime."')) ORDER BY to_do_date");

        $each_tab = explode(',', $total_ticket['all_ticket']);
        $total_assigned_time = array();


        while($row_report = mysqli_fetch_array($report_validation)) {
            $ticketid = $row_report['ticketid'];
		    $total_all = [];

            $tickets = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(IF(`timers`.`type`='Tracked',`timers`.`time`,0)))) `timer_total`, SEC_TO_TIME(SUM(TIME_TO_SEC(IF(`timers`.`type`='Manual',`timers`.`time`,0)))) `manual_time` FROM `tickets` LEFT JOIN (SELECT `ticketid`,`created_by`,`created_date`,`time_length` `time`, 'Manual' `type` FROM `ticket_time_list` WHERE `time_type`='Manual Time' AND `deleted`=0 UNION SELECT `ticketid`,`created_by`,`created_date`,`timer` `time`, 'Tracked' `type` FROM `ticket_timer` WHERE `deleted` = 0) `timers` ON `tickets`.`ticketid`=`timers`.`ticketid` WHERE `tickets`.`ticketid` ='$ticketid'"));

            $total_all[] = $tickets['timer_total'];
            $total_all[] = $tickets['manual_time'];

            $report_data .= "<tr>";
            $report_data .= '<td><a target= "_blank" href="../Ticket/index.php?edit='.$ticketid.'">#'.$ticketid.'</a></td>';
            $report_data .= '<td>'.get_tickets($dbc, $ticketid, 'heading').'</td>';
            $report_data .= '<td>'.get_client($dbc, get_tickets($dbc, $ticketid, 'businessid')).'</td>';
            $report_data .= '<td>'.get_multiple_contact($dbc, get_tickets($dbc, $ticketid, 'contactid')).'</td>';
            $report_data .= '<td>'.get_tickets($dbc, $ticketid, 'to_do_date').'</td>';
            $report_data .= '<td>'.substr(get_tickets($dbc, $ticketid, 'max_time'), 0, -3).'</td>';
            $report_data .= '<td>'.substr(get_tickets($dbc, $ticketid, 'max_qa_time'), 0, -3).'</td>';
            $report_data .= '<td>'.AddPlayTime($total_all).'</td>';

            $total_time    =   strtotime(get_tickets($dbc, $ticketid, 'max_time'));
            $spent_time   =   strtotime(AddPlayTime($total_all));

            if($total_time < $spent_time)
            {
               $report_data .= '<td><img src="'.WEBSITE_URL.'/img/reject.jpg" width="20" height="20" border="0" alt=""></td>';
            }
            else{
               $report_data .= '<td><img src="'.WEBSITE_URL.'/img/approve.jpg" width="20" height="20" border="0" alt=""></td>';
            }

            $report_data .= "</tr>";

        }

    $report_data .= "</table>";

    return $report_data;
}


function AddPlayTime($times) {
    // loop throught all the times
	$minutes = 0;
    foreach ($times as $time) {
        list($hour, $minute) = explode(':', $time);
        $minutes += explode(':',$time)[0] * 60;
        $minutes += explode(':',$time)[1];
    }

    $hours = floor($minutes / 60);
    $minutes -= $hours * 60;

    // returns the time already formatted
    return $hours.':'.sprintf('%02d', $minutes);
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