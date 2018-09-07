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
            $footer_text = 'Customer Stats From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : Customer stats by Staff, Services and Injury Type for the selected date range.";
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

    $html .= report_by_therapist($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '');
    $html .= report_by_injury($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '');
    $html .= report_by_body_part($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/customer_stats_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_customer_stats', 0, WEBSITE_URL.'/Reports/Download/customer_stats_'.$today_date.'.pdf', 'Customer Stats Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/customer_stats_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
} ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Customer stats by Staff, Services and Injury Type for the selected date range.</div>
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

                echo report_by_therapist($dbc, $starttime, $endtime, '', '', '');
                echo report_by_injury($dbc, $starttime, $endtime, '', '', '');
                echo report_by_body_part($dbc, $starttime, $endtime, '', '', '');
            ?>

        </form>

<?php
function report_by_therapist($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style) {

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="40%">Description</th>
    <th width="10%">Customer Count</th>
    <th width="15%"># Arrived/Completed/<br />Invoiced/Paid</th>
    <th width="10%"># Cancelled</th>
    <th width="10%"># Late Cancellation/<br />No-Show</th>
    <th width="10%">Male</th>
    <th width="10%">Female</th>
    </tr>';

    $result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND (category_contact = 'Physical Therapist' OR category_contact = 'Massage Therapist' OR category_contact = 'Osteopathic Therapist') AND deleted=0 AND status=1"),MYSQLI_ASSOC));

    $final_total_appoint = 0;
    $total_assess = 0;
    $total_treated = 0;
    $total_appoint = 0;
    $total_arrived = 0;
    $total_cancelled = 0;
    $report_late_no_show = 0;
    $total_male = 0;
    $total_female = 0;

	foreach($result as $therapistsid) {

        // By Professional
		$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND contactid='$therapistsid'"));

        $report_total = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_appt FROM booking WHERE therapistsid = '$therapistsid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND  ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));
        $report_arrived = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_arrived FROM booking WHERE therapistsid = '$therapistsid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND (follow_up_call_status='Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status='Invoiced' OR follow_up_call_status='Paid') AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));
        $report_cancelled = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_cancelled FROM booking WHERE therapistsid = '$therapistsid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != ''  AND follow_up_call_status='Cancelled' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));
        $report_absent = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS report_late_no_show FROM booking WHERE therapistsid = '$therapistsid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND follow_up_call_status='Late Cancellation / No-Show' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));
        $report_male = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(b.bookingid) AS total_appt_male FROM booking b, contacts c WHERE b.therapistsid = '$therapistsid' AND b.type != 'I' AND b.type != 'E' AND b.type != 'P' AND b.type != 'Q' AND b.type != 'R' AND b.type != '' AND  ((str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."') AND c.contactid = b.patientid AND c.gender='Male'"));
        $report_female = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(b.bookingid) AS total_appt_female FROM booking b, contacts c WHERE b.therapistsid = '$therapistsid' AND b.type != 'I' AND b.type != 'E' AND b.type != 'P' AND b.type != 'Q' AND b.type != 'R' AND b.type != '' AND  ((str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."') AND c.contactid = b.patientid AND c.gender='Female'"));

        $report_data .= '<tr nobr="true">';
        $report_data .= '<td>'.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</td>';
        $report_data .= '<td>'.$report_total['total_appt'].'</td>';
        $report_data .= '<td>'.$report_arrived['total_arrived'].'</td>';
        $report_data .= '<td>'.$report_cancelled['total_cancelled'].'</td>';
        $report_data .= '<td>'.$report_absent['report_late_no_show'].'</td>';
        $report_data .= '<td>'.$report_male['total_appt_male'].'</td>';
        $report_data .= '<td>'.$report_female['total_appt_female'].'</td>';
        $report_data .= '</tr>';

        $final_total_appoint += $report_total['total_appt'];
        $total_arrived += $report_arrived['total_arrived'];
        $total_cancelled += $report_cancelled['total_cancelled'];
        $report_late_no_show += $report_absent['report_late_no_show'];
        $total_male += $report_male['total_appt_male'];
        $total_female += $report_female['total_appt_female'];

        // By Professional

    }

    $report_data .= '<tr><td><b>Total - Professional</b></td><td><b>'.$final_total_appoint.'</b></td><td><b>'.$total_arrived.'</b></td><td><b>'.$total_cancelled.'</b></td><td><b>'.$report_late_no_show.'</b></td><td><b>'.$total_male.'</b></td><td><b>'.$total_female.'</b></td></tr>';
    return $report_data;
}

function report_by_injury($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style) {

    $report_total_injury = mysqli_query($dbc,"SELECT pi.injury_type FROM booking b, patient_injury pi WHERE b.type != 'I' AND b.type != 'E' AND b.type != 'P' AND b.type != 'Q' AND b.type != 'R' AND b.type != '' AND  ((str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."') AND b.injuryid = pi.injuryid GROUP BY pi.injury_type");
    $final_total_appoint = 0;
    $total_assess = 0;
    $total_treated = 0;
    $total_appoint = 0;
    $total_arrived = 0;
    $total_cancelled = 0;
    $report_late_no_show = 0;
    $total_male = 0;
    $total_female = 0;

    while($row1 = mysqli_fetch_array($report_total_injury)) {
        $it = $row1['injury_type'];

        $report_total = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(b.bookingid) AS total_appt FROM booking b, patient_injury pi WHERE pi.injury_type = '$it' AND b.type != 'I' AND b.type != 'E' AND b.type != 'P' AND b.type != 'Q' AND b.type != 'R' AND b.type != '' AND  ((str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."') AND b.injuryid = pi.injuryid"));

        $report_arrived = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_arrived FROM booking b, patient_injury pi WHERE pi.injury_type = '$it' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND (follow_up_call_status='Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status='Invoiced' OR follow_up_call_status='Paid') AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."') AND b.injuryid = pi.injuryid"));

        $report_cancelled = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_cancelled FROM booking b, patient_injury pi WHERE pi.injury_type = '$it' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != ''  AND follow_up_call_status='Cancelled' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."') AND b.injuryid = pi.injuryid"));

        $report_absent = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS report_late_no_show FROM booking b, patient_injury pi WHERE pi.injury_type = '$it' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND follow_up_call_status='Late Cancellation / No-Show' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."') AND b.injuryid = pi.injuryid"));
        $report_male = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(b.bookingid) AS total_appt_male FROM booking b, contacts c, patient_injury pi WHERE pi.injury_type = '$it' AND b.type != 'I' AND b.type != 'E' AND b.type != 'P' AND b.type != 'Q' AND b.type != 'R' AND b.type != '' AND  ((str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."') AND c.contactid = b.patientid AND c.gender='Male' AND b.injuryid = pi.injuryid"));
        $report_female = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(b.bookingid) AS total_appt_female FROM booking b, contacts c, patient_injury pi WHERE pi.injury_type = '$it' AND b.type != 'I' AND b.type != 'E' AND b.type != 'P' AND b.type != 'Q' AND b.type != 'R' AND b.type != '' AND  ((str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."') AND c.contactid = b.patientid AND c.gender='Female' AND b.injuryid = pi.injuryid"));

        $report_data .= '<tr nobr="true">';
        $report_data .= '<td>'.$row1['injury_type'].'</td>';
        $report_data .= '<td>'.$report_total['total_appt'].'</td>';
        $report_data .= '<td>'.$report_arrived['total_arrived'].'</td>';
        $report_data .= '<td>'.$report_cancelled['total_cancelled'].'</td>';
        $report_data .= '<td>'.$report_absent['report_late_no_show'].'</td>';
        $report_data .= '<td>'.$report_male['total_appt_male'].'</td>';
        $report_data .= '<td>'.$report_female['total_appt_female'].'</td>';
        $report_data .= '</tr>';

        $final_total_appoint += $report_total['total_appt'];
        $total_arrived += $report_arrived['total_arrived'];
        $total_cancelled += $report_cancelled['total_cancelled'];
        $report_late_no_show += $report_absent['report_late_no_show'];
        $total_male += $report_male['total_appt_male'];
        $total_female += $report_female['total_appt_female'];
    }

    $report_data .= '<tr><td><b>Total - Service</b></td><td><b>'.$final_total_appoint.'</b></td><td><b>'.$total_arrived.'</b></td><td><b>'.$total_cancelled.'</b></td><td><b>'.$report_late_no_show.'</b></td><td><b>'.$total_male.'</b></td><td><b>'.$total_female.'</b></td></tr>';
    return $report_data;
}

function report_by_body_part($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style) {

    $report_total_injury = mysqli_query($dbc,"SELECT pi.injury_name FROM booking b, patient_injury pi WHERE b.type != 'I' AND b.type != 'E' AND b.type != 'P' AND b.type != 'Q' AND b.type != 'R' AND b.type != '' AND  ((str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."') AND b.injuryid = pi.injuryid GROUP BY pi.injury_name");
    $final_total_appoint = 0;
    $total_assess = 0;
    $total_treated = 0;
    $total_appoint = 0;
    $total_arrived = 0;
    $total_cancelled = 0;
    $report_late_no_show = 0;
    $total_male = 0;
    $total_female = 0;

    while($row1 = mysqli_fetch_array($report_total_injury)) {
        $it = $row1['injury_name'];

        $report_total = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(b.bookingid) AS total_appt FROM booking b, patient_injury pi WHERE pi.injury_name = '$it' AND b.type != 'I' AND b.type != 'E' AND b.type != 'P' AND b.type != 'Q' AND b.type != 'R' AND b.type != '' AND  ((str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."') AND b.injuryid = pi.injuryid"));

        $report_arrived = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_arrived FROM booking b, patient_injury pi WHERE pi.injury_name = '$it' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND (follow_up_call_status='Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status='Invoiced' OR follow_up_call_status='Paid') AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."') AND b.injuryid = pi.injuryid"));

        $report_cancelled = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_cancelled FROM booking b, patient_injury pi WHERE pi.injury_name = '$it' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != ''  AND follow_up_call_status='Cancelled' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."') AND b.injuryid = pi.injuryid"));

        $report_absent = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS report_late_no_show FROM booking b, patient_injury pi WHERE pi.injury_name = '$it' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND follow_up_call_status='Late Cancellation / No-Show' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."') AND b.injuryid = pi.injuryid"));
        $report_male = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(b.bookingid) AS total_appt_male FROM booking b, contacts c, patient_injury pi WHERE pi.injury_name = '$it' AND b.type != 'I' AND b.type != 'E' AND b.type != 'P' AND b.type != 'Q' AND b.type != 'R' AND b.type != '' AND  ((str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."') AND c.contactid = b.patientid AND c.gender='Male' AND b.injuryid = pi.injuryid"));
        $report_female = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(b.bookingid) AS total_appt_female FROM booking b, contacts c, patient_injury pi WHERE pi.injury_name = '$it' AND b.type != 'I' AND b.type != 'E' AND b.type != 'P' AND b.type != 'Q' AND b.type != 'R' AND b.type != '' AND  ((str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(b.appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."') AND c.contactid = b.patientid AND c.gender='Female' AND b.injuryid = pi.injuryid"));

        $report_data .= '<tr nobr="true">';
        $report_data .= '<td>'.$row1['injury_name'].'</td>';
        $report_data .= '<td>'.$report_total['total_appt'].'</td>';
        $report_data .= '<td>'.$report_arrived['total_arrived'].'</td>';
        $report_data .= '<td>'.$report_cancelled['total_cancelled'].'</td>';
        $report_data .= '<td>'.$report_absent['report_late_no_show'].'</td>';
        $report_data .= '<td>'.$report_male['total_appt_male'].'</td>';
        $report_data .= '<td>'.$report_female['total_appt_female'].'</td>';
        $report_data .= '</tr>';

        $final_total_appoint += $report_total['total_appt'];
        $total_arrived += $report_arrived['total_arrived'];
        $total_cancelled += $report_cancelled['total_cancelled'];
        $report_late_no_show += $report_absent['report_late_no_show'];
        $total_male += $report_male['total_appt_male'];
        $total_female += $report_female['total_appt_female'];
    }

    $report_data .= '<tr><td><b>Total -  Injury Type</b></td><td><b>'.$final_total_appoint.'</b></td><td><b>'.$total_arrived.'</b></td><td><b>'.$total_cancelled.'</b></td><td><b>'.$report_late_no_show.'</b></td><td><b>'.$total_male.'</b></td><td><b>'.$total_female.'</b></td></tr>';
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