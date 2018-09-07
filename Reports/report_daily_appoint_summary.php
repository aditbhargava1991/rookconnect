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
    $therapistpdf = $_POST['therapistpdf'];

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
            $footer_text = 'Appointment Summary From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : You can see how many appointments each Staff has with a particular status in the selected timeframe.";
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

    $html .= report_appoint_summary($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '', $therapistpdf);

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/appointment_summary_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_daily_appoint_summary', 0, WEBSITE_URL.'/Reports/Download/appointment_summary_'.$today_date.'.pdf', 'Appointment Summary Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/appointment_summary_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $therapist = $therapistpdf;
} ?>

        <a href='report_daily_appoint_summary.php?type=operations'><button type="button" class="btn brand-btn mobile-block active_tab" >Summary</button></a>&nbsp;&nbsp;
        <a href='report_daily_appoint_summary_breakdown.php?type=operations'><button type="button" class="btn brand-btn mobile-block" >Breakdown</button></a>&nbsp;&nbsp;

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            You can see how many appointments each Staff has with a particular status in the selected timeframe.</div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
                $therapist = $_POST['therapist'];
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
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Staff:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select Staff..." name="therapist" class="chosen-select-deselect form-control1" width="380">
							<option <?php if ($therapist=='') echo 'selected="selected"';?> value="">Select All</option>
							<?php foreach(array_filter(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT categories FROM field_config_contacts WHERE tab='Staff' AND `categories` IS NOT NULL"))['categories'])) as $staff_category) { ?>
								<option <?php if ($therapist==$staff_category) echo 'selected="selected"';?> value="<?= $staff_category ?>"><?= $staff_category ?></option>
							<?php } ?>
							<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE categoryIN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status=1"),MYSQLI_ASSOC));
							foreach($query as $rowid) {
								echo "<option ".($rowid == $therapist ? 'selected' : '')." value='$rowid'>".get_contact($dbc, $rowid)."</option>";
							} ?>
						</select>
					</div>
				</div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="therapistpdf" value="<?php echo $therapist; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                //echo '<a href="report_referral.php?referral=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a></h4><br>';

                echo report_appoint_summary($dbc, $starttime, $endtime, '', '', '', $therapist);
            ?>

        </form>


<?php
function report_appoint_summary($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $therapist) {

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th>Staff</th>
    <th>Arrived/Completed/<br />Invoiced/Paid</th>
    <th>Cancelled</th>
    <th>Rescheduled</th>
    <th>Late/Cancellation/<br />No-Show</th>
    <th>Booked Confirmed/<br />Unconfirmed</th>
    <th><b>Total Assessments</b></th>
    <th><b>Total Treatments</b></th>
    </tr>';

    if($therapist == '') {
        $result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND (category_contact = 'Physical Therapist' OR category_contact = 'Massage Therapist' OR category_contact = 'Osteopathic Therapist') AND deleted=0 AND status=1"),MYSQLI_ASSOC));
    } else if($therapist == 'Physical Therapist') {
        $result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND category_contact = 'Physical Therapist' AND deleted=0 AND status=1"),MYSQLI_ASSOC));
    } else if($therapist == 'Massage Therapist') {
        $result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND category_contact = 'Massage Therapist' AND deleted=0 AND status=1"),MYSQLI_ASSOC));
    } else if($therapist == 'Osteopathic Therapist') {
        $result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND category_contact = 'Osteopathic Therapist' AND deleted=0 AND status=1"),MYSQLI_ASSOC));
    } else {
        $result = [$therapist];
    }

    $final_total_appoint = 0;
    $total_assess = 0;
    $total_treated = 0;
    $total_appoint = 0;
    $total_arrived = 0;
    $total_cancelled = 0;
    $total_absent = 0;
    $total_pending = 0;
    $total_rescheduled = 0;

	foreach($result as $therapistsid) {
		$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND IFNULL(`staff_category`,'') NOT IN (".STAFF_CATS_HIDE.") AND contactid='$therapistsid'"));

        $report_arrived = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_arrived FROM booking WHERE therapistsid = '$therapistsid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND (follow_up_call_status='Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status='Invoiced' OR follow_up_call_status='Paid') AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));
        $report_cancelled = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_cancelled FROM booking WHERE therapistsid = '$therapistsid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != ''  AND follow_up_call_status='Cancelled' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));
        $report_rescheduled = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_rescheduled FROM booking WHERE therapistsid = '$therapistsid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != ''  AND follow_up_call_status='Rescheduled' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));
        $report_absent = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_absent FROM booking WHERE therapistsid = '$therapistsid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND follow_up_call_status='Late Cancellation / No-Show' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));
        $report_pending = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_pending FROM booking WHERE therapistsid = '$therapistsid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND (follow_up_call_status='Booked Confirmed' OR follow_up_call_status='Booked Unconfirmed') AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));
        // Need to verify this only selects assessed
		$report_assessed = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_assessed FROM booking WHERE therapistsid = '$therapistsid' AND type IN('A','C','F','H','N','U') AND (follow_up_call_status='Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status='Invoiced' OR follow_up_call_status='Paid') AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));
		// Need to verify this only selects treated
        $report_treated = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_treated FROM booking WHERE therapistsid = '$therapistsid' AND type NOT IN('A','C','F','H','N','U') AND (follow_up_call_status='Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status='Invoiced' OR follow_up_call_status='Paid') AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));

        $report_data .= '<tr nobr="true">';
        $report_data .= '<td>'.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</td>';
        $report_data .= '<td>'.$report_arrived['total_arrived'].'</td>';
        $report_data .= '<td>'.$report_cancelled['total_cancelled'].'</td>';
        $report_data .= '<td>'.$report_rescheduled['total_rescheduled'].'</td>';
        $report_data .= '<td>'.$report_absent['total_absent'].'</td>';
        $report_data .= '<td>'.$report_pending['total_pending'].'</td>';
        $report_data .= '<td><b>'.$report_assessed['total_assessed'].'</b></td>';
        $report_data .= '<td><b>'.$report_treated['total_treated'].'</b></td>';
        $report_data .= '</tr>';

        $total_appoint = ($report_arrived['total_arrived']+$report_cancelled['total_cancelled']+$report_rescheduled['total_rescheduled']+$report_absent['total_absent']+$report_pending['total_pending']);

        $final_total_appoint += $total_appoint;
        $total_assess += $report_assessed['total_assessed'];
        $total_treated += $report_treated['total_treated'];
        $total_arrived += $report_arrived['total_arrived'];
        $total_cancelled += $report_cancelled['total_cancelled'];
        $total_absent += $report_absent['total_absent'];
        $total_pending += $report_pending['total_pending'];
        $total_rescheduled += $report_rescheduled['total_rescheduled'];
    }

    $report_data .= '<tr><td><b>Grand Total</b></td><td><b>'.$total_arrived.'</b></td><td><b>'.$total_cancelled.'</b></td><td><b>'.$total_rescheduled.'</b></td><td><b>'.$total_absent.'</b></td><td><b>'.$total_pending.'</b></td><td><b>'.$total_assess.'</b></td><td><b>'.$total_treated.'</b></td></tr>';
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