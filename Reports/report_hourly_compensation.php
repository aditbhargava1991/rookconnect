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
                $this->Image($image_file, 10, 10, '', '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);

            $this->SetFont('helvetica', '', 13);
            $footer_text = 'Hourly Compensation From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : Displays how many hours each Therapist is scheduled for the selected time frame, and from the hourly pay it will count total compensation pay.";
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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);

    $html .= report_compensation($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '', $therapistpdf);

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/hourly_compensation_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_hourly_compensation', 0, WEBSITE_URL.'/Reports/Download/hourly_compensation_'.$today_date.'.pdf', 'Hourly Compensation Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/hourly_compensation_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $therapist = $therapistpdf;

}
if (isset($_POST['printapptpdf'])) {
    $starttimepdf = $_POST['starttimepdf'];
    $endtimepdf = $_POST['endtimepdf'];
    $therapistpdf = $_POST['therapistpdf'];
    $stat_holidays_pdf = $_POST['stat_holidays_pdf'];

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
            $footer_text = 'Compensation From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);
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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);

    $html .= report_appt_compensation($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '', $therapistpdf, $stat_holidays_pdf);

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/compensation_appt_'.$today_date.'.pdf', 'F');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/compensation_appt_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $therapist = $therapistpdf;

    $stat_holidays = $stat_holidays_pdf;

}

?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Displays how many hours each Therapist is scheduled for the selected time frame, and from the hourly pay it will count total compensation pay.</div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            //$contactid = '';
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

            $value_config = ','.get_config($dbc, 'reports_dashboard').',';
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
						<select data-placeholder="Select a Staff..." name="therapist" class="chosen-select-deselect form-control">
							<option value=""></option>
							<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND (category_contact = 'Physical Therapist' OR category_contact = 'Massage Therapist' OR category_contact = 'Osteopathic Therapist') AND deleted=0 AND status=1"),MYSQLI_ASSOC));
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

            <div class="pull-right">
                <span class="popover-examples list-inline" style="margin:0 0 0 10px;"><a data-toggle="tooltip" data-placement="top" title="This report is for staff to see their compensation structure schedule."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20" style="padding-bottom:5px;" /></a></span>
                <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn" onclick="set_form_action();">Print Report</button>
            </div>
            <br><br>

            <?php
                //echo '<a href="report_compensation.php?compensation=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a></h4><br>';

                echo report_compensation($dbc, $starttime, $endtime, '', '', '', $therapist);
            ?>

        </form>

<?php
function report_compensation($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $therapist) {
    $report_data = '';

    if($therapist == '') {
		$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND (category_contact = 'Physical Therapist' OR category_contact = 'Massage Therapist' OR category_contact = 'Osteopathic Therapist') AND deleted=0 AND status=1"),MYSQLI_ASSOC));
        //$result = mysqli_query($dbc, "SELECT contactid, scheduled_hours FROM contacts WHERE category='Staff' AND deleted=0");
    } else {
		$result = [ $therapist ];
    }

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th>Staff</th>
    <th>Scheduled Hours</th>
    <th>Hourly Pay</th>
    <th>Compensation</th>';
    $report_data .= "</tr>";

    $all_booking = 0;
    $grand_total = 0;
	foreach($result as $rowid) {
		$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT contactid, scheduled_hours FROM contacts WHERE contactid='$rowid'"));
        $therapistid = $row['contactid'];

        $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM hourly_pay WHERE contactid='$therapistid'"));
        $hourly_pay = $get_contact['hourly_pay'];

        //% of Available Hours Schedules
        $start_for_total_hours = $starttime;
        $now = strtotime($endtime);
        $your_date = strtotime($start_for_total_hours);
        $datediff = $now - $your_date;
        $total_days = floor($datediff/(60*60*24));

        $total_weekdays = '';
        for($i=0;$i<=$total_days;$i++) {
            $total_weekdays .= date('w', strtotime($start_for_total_hours)).',';
            $start_for_total_hours = date('Y-m-d',strtotime($start_for_total_hours . "+1 days"));
        }
        $schedule_days_var = rtrim($total_weekdays, ",");
        $total_therapist = 0;
        $s_hours = explode('*', $row['scheduled_hours']);
        $s_days = explode(',', $schedule_days_var);
        $total_work_hours = 0;
        $total_book_hours = 0;

        for($i=0;$i<=$total_days;$i++) {
            $s_day_index = $s_days[$i];
            $each_hours = $s_hours[$s_day_index];
            if($each_hours != '') {
                $each_double = explode(',', $each_hours);
                $count_no_come = count($each_double);
                if($count_no_come == 1) {
                    $each_double = explode('-', $each_hours);

                    $t1 = StrToTime ($each_double[1]);
                    $t2 = StrToTime ($each_double[0]);
                    $diff = $t1 - $t2;
                    $total_work_hours += $diff / ( 60 * 60 );
                } else {
                    $each_double = explode(',', $each_hours);
                    foreach($each_double as $key) {
                        $each_key = explode('-', $key);

                        $t1 = StrToTime ($each_key[1]);
                        $t2 = StrToTime ($each_key[0]);
                        $diff = $t1 - $t2;
                        $total_work_hours += $diff / ( 60 * 60 );
                    }
                }
            }
        }

        $appoints = mysqli_query($dbc, "SELECT appoint_date, end_appoint_date FROM booking WHERE therapistsid = '$therapistid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND follow_up_call_status != 'Cancelled' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')");

        while($row_appoints = mysqli_fetch_array( $appoints )) {
            $appoint_date = strtotime($row_appoints['appoint_date']);
            $end_appoint_date = strtotime($row_appoints['end_appoint_date']);
            $differenceInSeconds = $end_appoint_date - $appoint_date;
            $total_book_hours += ($differenceInSeconds / 3600);
        }

        $avail_booked = number_format((float)(($total_book_hours/$total_work_hours)*100), 2, '.', '');
        //% of Available Hours Schedules

        // ****************** Actual Value ******************


        $report_data .= '<tr nobr="true">';
        $report_data .= '<td>'.get_contact($dbc, $therapistid).'</td>';
        $report_data .= '<td>'.$total_work_hours.'</td>';
        //$report_data .= '<td>'.$total_book_hours.'</td>';
        $report_data .= '<td>$'.$hourly_pay.'</td>';
        $report_data .= '<td>$'.($total_work_hours*$hourly_pay).'</td>';
        $report_data .= '</tr>';
    }

    $report_data .= '</table><br>';

    return $report_data;
}
