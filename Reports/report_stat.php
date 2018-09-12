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
            $footer_text = 'Stat From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "C", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : <br>Therapists Stats tracks bookings compared to arrivals and hours utilized by each Therapist. The report displays the date range selected. This report is compared to Goals & Objectives to track each Therapist's performance.";
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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 90, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);

    /*
    $start_date = date('Y-m-d', strtotime($starttimepdf));
    $end_date = date('Y-m-d', strtotime($endtimepdf));
    $end_date1 = date('Y-m-d', strtotime($endtimepdf.' + 6 days'));
    $html = '';

    for($date = $start_date; $date <= $end_date1; $date = date('Y-m-d', strtotime($date. ' + 7 days')))
    {
        $pdf->AddPage('L', 'LETTER');
        $pdf->SetFont('helvetica', '', 8);
        $html = '';

        $start_end_date = getWeekDates($date, $start_date, $end_date);
        $html .= "<h4>".$start_end_date."</h4>";
        $se_date = explode(' : ', $start_end_date);
        $html .= '<br><br>' . report_goals($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '', $therapistpdf, 'print');
        $pdf->writeHTML($html, true, false, true, false, '');
    }
    */

    $html .= '<br><br>' . report_goals($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '', $therapistpdf, 'print');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/stat_on_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_stat', 0, WEBSITE_URL.'/Reports/Download/stat_on_'.$today_date.'.pdf', 'Stat Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/stat_on_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $therapist = $therapistpdf;
    } ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            <br>Therapists Stats tracks bookings compared to arrivals and hours utilized by each Therapist. The report displays the date range selected. This report is compared to Goals & Objectives to track each Therapist's performance.</div>
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

            <center>
				<div class="col-sm-5">
					<label class="col-sm-4">From:</label>
					<div class="col-sm-8">
						<input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>">
					</div>
				</div>
				<div class="col-sm-5">
					<label class="col-sm-4">Until:</label>
					<div class="col-sm-8">
						<input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>">
					</div>
				</div>
				<div class="col-sm-5">
					<label class="col-sm-4">Therapist:</label>
					<div class="col-sm-8"><select data-placeholder="Choose a Therapist..." name="therapist" class="chosen-select-deselect form-control1" width="380">
						<option <?php if ($therapist=='') echo 'selected="selected"';?> value="">Select All</option>
						<option <?php if ($therapist=='Physical Therapist') echo 'selected="selected"';?> value="Physical Therapist">Physical Therapist</option>
						<option <?php if ($therapist=='Massage Therapist') echo 'selected="selected"';?> value="Massage Therapist">Massage Therapist</option>
						<option <?php if ($therapist=='Osteopathic Therapist') echo 'selected="selected"';?> value="Osteopathic Therapist">Osteopathic Therapist</option>
						<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND (category_contact = 'Physical Therapist' OR category_contact = 'Massage Therapist' OR category_contact = 'Osteopathic Therapist') AND deleted=0 AND status=1"),MYSQLI_ASSOC));
						foreach($query as $rowid) {
							echo "<option ".($rowid == $therapist ? 'selected' : '')." value='$rowid'>".get_contact($dbc, $rowid)."</option>";
						} ?>
					</select></div>
				</div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></center>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="therapistpdf" value="<?php echo $therapist; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                /*
                $start_date = date('Y-m-d', strtotime($starttime));
                $end_date = date('Y-m-d', strtotime($endtime));
                $end_date1 = date('Y-m-d', strtotime($endtime.' + 6 days'));

                for($date = $start_date; $date <= $end_date1; $date = date('Y-m-d', strtotime($date. ' + 7 days')))
                {
                    $start_end_date = getWeekDates($date, $start_date, $end_date);
                    echo "<h4>".$start_end_date."</h4>";
                    $se_date = explode(' : ', $start_end_date);
                    echo report_goals($dbc, $se_date[0], $se_date[1], '', '', '', $therapist, 'screen'); //'screen' - display on screen | 'print' - print PDF

                    echo "<br>";
                }

                */

                //echo '<a href="report_goals.php?goals=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a><br><br>';

                echo report_goals($dbc, $starttime, $endtime, '', '', '', $therapist, 'screen');
            ?>

        </form>

<?php
function getWeekDates($date, $start_date, $end_date)
{
    $week =  date('W', strtotime($date));
    $year =  date('Y', strtotime($date));
    $from = date("Y-m-d", strtotime("{$year}-W{$week}+1")); //Returns the date of monday in week
    if($from < $start_date) $from = $start_date;
    $to = date("Y-m-d", strtotime("{$year}-W{$week}-6"));   //Returns the date of sunday in week
    if($to > $end_date) $to = $end_date;
    return $from." : ".$to;//Output : Start Date-->2012-09-03 End Date-->2012-09-09
}

function report_goals($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $therapist, $output) {

    if ( $output == 'screen' ) {
		//print to screen

		/* Hidden as per client request. Info i's + headings show these details
		$report_data = 'C1 = Client Scheduled<i>[Total Booking of Any status]</i><br>C2 = # of Client Visits<i>[Total Completed Booking]</i><br>C3 = % Arrivals<i>[(Total Completed Booking/Total Booking) * 100]</i><br>C4 = Average # of Visits per Client to Discharge<i>[Total Completed Booking of Discharged Patietns/Total Discharged Patients]</i><br>C5 = % of Available Hours Schedules<i>[(Total Completed Booking/(Total Active Days * 17 OR 25)) * 100]</i><br>C6 = # of Completed Assessment<br>C7 = Block Booking<i>[(Total Block Booking for related Assessment/Total Completed Assessment)*100]</i><br><br>';
		*/

		$report_data = '<br><table border="1px" class="table table-bordered" style="'.$table_style.'">
		<tr style="'.$table_row_style.'" nobr="true">
		<th width="8%">
            <center>
                <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="The staff member."><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span><br />
                Staff
            </center>
        </th>
		<th width="6%">
			<center>
				<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Total bookings during a given time frame."><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span><br />
				Clients Scheduled
			</center>
		</th>
		<th width="6%">
			<center>
				<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Total number of completed bookings."><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span><br />
				# of Client Visits
			</center>
		</th>';

		if($therapist != 'Massage Therapist') {
			$report_data .= '
			<th width="9%">
				<center>
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Total number of completed bookings divided by the total bookings scheduled. Then multiplied by 100."><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span><br />
					% Arrivals
				</center>
			</th>
			<th width="20%">
				<center>
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Total number of completed bookings from discharge patients divided by the total number of discharge patients."><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span><br />
					Average # of Visits Per Client To Discharge
				</center>
			</th>
			<th width="9%">
				<center>
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Total completed bookings divided by configured total appointments. Then multiplied by 100."><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span><br />
					% of Available Hours Scheduled
				</center>
			</th>
			<th width="2%">
				<center>
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Number of assessments in a given time period."><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span><br />
					Assemnt Count
				</center>
			</th>';
		} else {
			$report_data .= '
			<th width="24%">
				<center>
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Total number of completed bookings divided by the total bookings scheduled. Then multiplied by 100."><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span><br />
					% Arrivals
				</center>
			</th>
			<th width="40%">
				<center>
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Total number of completed bookings from discharge patients divided by the total number of discharge patients."><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span><br />
					Average # of Visits per Client to Discharge
				</center>
			</th>
			<th width="24%">
				<center>
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Total completed bookings divided by total active days, times 17. Then multiplied by 100."><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span><br />
					% of Available Hours Schedules
				</center>
			</th>
			<th width="2%">
				<center>
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Number of assessments in a given time period."><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span><br />
					Assemnt Count
				</center>
			</th>';
		}

		if($therapist != 'Massage Therapist') {
			$report_data .= '<th width="40%">';
			$report_data .= '<center><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Total number of block booking divided by the number of assessments, then multiplied by 100."><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span><br />Block Booking</center>';
			$report_data .= '</th>';
		}
		$report_data .= '</tr>';

	} else {
		//print PDF

		$report_data = '<table border="1px" class="table table-bordered" style="'.$table_style.'">
		<tr style="'.$table_row_style.'" nobr="true">
		<th width="9%"></th>
		<th width="6%">Client Sched.</th>
		<th width="6%"># of Client Visits</th>';

		if ($therapist != 'Massage Therapist') {
			$report_data .= '
			<th width="15%">% Arrivals</th>
			<th width="20%">Average # of Visits Per Client To Discharge</th>
			<th width="15%">% of Ava. Hours Scheduled</th>
			<th width="6%">Assmnt Count</th>';
		} else {
			$report_data .= '
			<th width="15%">% Arrivals</th>
			<th width="44%">Average # of Visits per Client to Discharge</th>
			<th width="15%">% of Ava. Hours Schedules</th>
			<th width="6%">Assmnt Count</th>';
		}

		if ( $therapist != 'Massage Therapist' ) {
			$report_data .= '<th width="24%">Block Booking</th>';
		}
		$report_data .= '</tr>';
	}

    if($therapist == '') {
		$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name, scheduled_hours FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status=1 AND (category_contact = 'Physical Therapist' OR category_contact = 'Massage Therapist' OR category_contact = 'Osteopathic Therapist')"),MYSQLI_ASSOC));
    } else if($therapist == 'Physical Therapist') {
		$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name, scheduled_hours FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status=1 AND category_contact = 'Physical Therapist'"),MYSQLI_ASSOC));
    } else if($therapist == 'Massage Therapist') {
		$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name, scheduled_hours FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status=1 AND category_contact = 'Massage Therapist'"),MYSQLI_ASSOC));
    } else if($therapist == 'Osteopathic Therapist') {
		$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name, scheduled_hours FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status=1 AND category_contact = 'Osteopathic Therapist'"),MYSQLI_ASSOC));
    } else {
		$result = [ $therapist ];
    }

    $c1 = 0;
    $c2 = 0;
    $c3 = 0;
    $c4 = 0;
    $c5 = 0;
    $c6 = 0;
    $c7 = 0;
    $c8 = 0;
    $cdis = 0;

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
    //% of Available Hours Schedules

    $total_entry1 = 0;
    $total_entry2 = 0;
    $total_entry3 = 0;
    $total_entry4 = 0;

    $total_block_booking = 0;

    foreach($result as $rowid) {
		$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `scheduled_hours` FROM `contacts` WHERE `contactid`='$rowid'"));
        $therapistsid = $row['contactid'];
        $category_contact = get_all_form_contact($dbc, $therapistsid, 'category_contact');

        //Client Scheduled
        $total_booking = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_booking FROM booking WHERE therapistsid = '$therapistsid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));
        //Client Scheduled

        //# of Client Visits
        $total_completed_booking = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_completed_booking FROM booking WHERE therapistsid = '$therapistsid' AND (follow_up_call_status = 'Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status = 'Paid' OR follow_up_call_status = 'Invoiced') AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));
        //# of Client Visits

        //% Arrivals
        $arrival_rate = (($total_completed_booking['total_completed_booking'] / $total_booking['total_booking']) * 100);
        //% Arrivals

        //	Average # Visits per Client to Discharge

        $result_dc = mysqli_query($dbc, "SELECT contactid FROM patient_injury WHERE injury_therapistsid = '$therapistsid' AND discharge_stat = 1 AND (DATE(discharge_date) >= '".$starttime."' AND DATE(discharge_date) <= '".$endtime."') GROUP BY contactid");
        $total_dc_appt = 0;
        $total_dp_name = '';
        $total_comp_book_of_dis_pat_value = 0;
        while($row_dc = mysqli_fetch_array($result_dc)) {
            $patientid = $row_dc['contactid'];

            if (strpos($category_contact, 'Physical') !== false) {
                $total_comp_book_of_dis_pat = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_comp_book_of_dis_pat FROM booking WHERE patientid = '$patientid' AND type IN ('A','B','C','D','F','G','H','J','N','O') AND (follow_up_call_status = 'Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status = 'Paid' OR follow_up_call_status = 'Invoiced')"));
            } else {
                $total_comp_book_of_dis_pat = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_comp_book_of_dis_pat FROM booking WHERE patientid = '$patientid' AND type IN ('K','L','M','U') AND (follow_up_call_status = 'Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status = 'Paid' OR follow_up_call_status = 'Invoiced')"));
            }

            $total_comp_book_of_dis_pat_value += $total_comp_book_of_dis_pat['total_comp_book_of_dis_pat'];

            if($total_comp_book_of_dis_pat['total_comp_book_of_dis_pat'] >0 ) {
                $total_dc_appt++;
                $total_dp_name .= $patientid.'-'.get_contact($dbc, $patientid).'('.$total_comp_book_of_dis_pat['total_comp_book_of_dis_pat'].') | ';
            }
        }
        $total_dc_patient = $total_dc_appt;

        $avg_visit_discharge_new = ($total_comp_book_of_dis_pat_value / $total_dc_patient);

        $total_discharge_patient = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(injuryid) AS total_discharge_patient FROM patient_injury WHERE injury_therapistsid = '$therapistsid' AND discharge_stat = 1 AND (DATE(discharge_date) >= '".$starttime."' AND DATE(discharge_date) <= '".$endtime."')"));
        //$avg_visit_discharge = (($total_completed_booking['total_completed_booking'] / $total_discharge_patient['total_discharge_patient']));
        //	Average # Visits per Client to Discharge

        //% of Available Hours Schedules
        $total_active = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(DISTINCT((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')))) AS total_active FROM booking WHERE therapistsid = '$therapistsid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));

        $ava_hours_stat = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ava_hours_stat, ava_hours_stat_bi, ava_hours_stat_weekly, ava_hours_stat_daily FROM goal WHERE therapistid = '$therapistsid'"));

        /*
        if (strpos($category_contact, 'Physical') !== false) {
            $total_avg_active = ($total_active['total_active']*$ava_hours_stat['ava_hours_stat']);
            //$total_avg_active = ($ava_hours_stat['ava_hours_stat']);
        } else {
            //$total_avg_active = ($total_active['total_active']*$ava_hours_stat['ava_hours_stat']);

            $now = strtotime($endtime);
            $your_date = strtotime($starttime);
            $datediff = $now - $your_date;

            $total_days_diff = floor($datediff / (60 * 60 * 24));

            if($total_days_diff >= 28) {
                $total_avg_active = ($ava_hours_stat['ava_hours_stat']*4);
            } elseif($total_days_diff >= 14) {
                $total_avg_active = ($ava_hours_stat['ava_hours_stat']*2);
            } else {
                $total_avg_active = ($ava_hours_stat['ava_hours_stat']);
            }
        }
        */

        // For MT it was 5 as per blair, as per Hannah's visit to Client we changed it to 25.

        //$avail_hours_sch = (($total_completed_booking['total_completed_booking'] / $total_avg_active) * 100);

        $your_date = new DateTime($starttime);
        $now = new DateTime($endtime);

        $total_days_diff = $now->diff($your_date)->format("%a");

        //$now = strtotime($endtime);
        //$your_date = strtotime($starttime);
        //$datediff = $now - $your_date;

        //echo $total_days_diff = floor($datediff / (60 * 60 * 24));

        if($total_days_diff >= 28) {
            $stat_hours = $ava_hours_stat['ava_hours_stat'];
        } elseif($total_days_diff >= 13) {
            $stat_hours = $ava_hours_stat['ava_hours_stat_bi'];
        } elseif($total_days_diff >= 6) {
            $stat_hours = $ava_hours_stat['ava_hours_stat_weekly'];
        } else {
            $stat_hours = $ava_hours_stat['ava_hours_stat_daily'];
        }

        $avail_hours_sch = (($total_completed_booking['total_completed_booking'] / $stat_hours) * 100);
        //% of Available Hours Schedules

        //% of Available Hours Schedules
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

            $appoints = mysqli_query($dbc, "SELECT appoint_date, end_appoint_date FROM booking WHERE therapistsid = '$therapistsid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND  ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')");

            while($row_appoints = mysqli_fetch_array( $appoints )) {
                $appoint_date = strtotime($row_appoints['appoint_date']);
                $end_appoint_date = strtotime($row_appoints['end_appoint_date']);
                $differenceInSeconds = $end_appoint_date - $appoint_date;
                $total_book_hours += ($differenceInSeconds / 3600);
            }

            $avail_booked = number_format((float)(($total_book_hours/$total_work_hours)*100), 2, '.', '');
        //% of Available Hours Schedules

        //# of New Clients
        $total_newclient = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(statid) AS total_newclient FROM therapist_stat WHERE therapistid = '$therapistsid' AND (DATE(today_date) >= '".$starttime."' AND DATE(today_date) <= '".$endtime."')"));
        //# of New Clients

        //Assessment Count
        $total_injury = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_assessment FROM booking WHERE therapistsid = '$therapistsid' AND type IN('A','C','F','H','N','U') AND (follow_up_call_status = 'Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status = 'Paid' OR follow_up_call_status = 'Invoiced') AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));
        //Assessment Count

        //Block Booking
        $get_booking = mysqli_query($dbc,"SELECT bookingid, appoint_date, today_date, patientid FROM booking WHERE therapistsid = '$therapistsid' AND type IN('A','C','F','H','N','U') AND (follow_up_call_status = 'Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status = 'Paid' OR follow_up_call_status = 'Invoiced') AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."') ORDER BY appoint_date");
        $total_bb = 0;
        $bb_patient = '';
        $ass_patient = '';
        $total5_app = 0;
        $total_bb5 = 0;
        while($row_get_booking = mysqli_fetch_array($get_booking)) {
            $bb_appoint_date = explode(' ', $row_get_booking['appoint_date']);
            $final_ass_appoint_date = $bb_appoint_date[0];

            $next_appoint1 = date('Y-m-d', strtotime("+7 day", strtotime($final_ass_appoint_date)));
            $next_appoint2 = date('Y-m-d', strtotime("+7 day", strtotime($next_appoint1)));
            $next_appoint3 = date('Y-m-d', strtotime("+7 day", strtotime($next_appoint2)));
            $next_appoint4 = date('Y-m-d', strtotime("+7 day", strtotime($next_appoint3)));
            $next_appoint5 = date('Y-m-d', strtotime("+7 day", strtotime($next_appoint4)));

            // 1 = $final_ass_appoint_date > and $next_appoint1 <=
            // 2 = $next_appoint1 > and $next_appoint2 <=
            // 3 = $next_appoint2 > and $next_appoint3 <=
            $patientid = $row_get_booking['patientid'];
            $type = $row_get_booking['type'];

            $get_bb1 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS get_bb1 FROM booking WHERE patientid='$patientid' AND today_date = '".$final_ass_appoint_date."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) > '".$final_ass_appoint_date."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$next_appoint1."'"));

            $get_bb2 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS get_bb2 FROM booking WHERE patientid='$patientid' AND today_date = '".$final_ass_appoint_date."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) > '".$next_appoint1."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$next_appoint2."'"));

            $get_bb3 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS get_bb3 FROM booking WHERE patientid='$patientid' AND today_date = '".$final_ass_appoint_date."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) > '".$next_appoint2."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$next_appoint3."'"));

            $get_bb4 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS get_bb4 FROM booking WHERE patientid='$patientid' AND today_date = '".$final_ass_appoint_date."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) > '".$next_appoint3."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$next_appoint4."'"));

            $get_bb5 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS get_bb5 FROM booking WHERE patientid='$patientid' AND today_date = '".$final_ass_appoint_date."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) > '".$next_appoint4."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$next_appoint5."'"));

            if($get_bb1['get_bb1'] >= 1) {
                $total5_app++;
            }
            if($get_bb2['get_bb2'] >= 1) {
                $total5_app++;
            }
            if($get_bb3['get_bb3'] >= 1) {
                $total5_app++;
            }
            if($get_bb4['get_bb4'] >= 1) {
                $total5_app++;
            }
            if($get_bb5['get_bb5'] >= 1) {
                $total5_app++;
            }

            if($total5_app >= 3) {
                $total_bb5++;
            }

            if($get_bb1['get_bb1'] >= 1 && $get_bb2['get_bb2'] >= 1 && $get_bb3['get_bb3'] >= 1 && $get_bb4['get_bb4'] >= 1 && $get_bb5['get_bb5'] >= 1) {
                $total_bb++;
                $bb_patient .= $row_get_booking['bookingid'].'-'.get_contact($dbc, $patientid).' | ';
                $ass_patient .= $row_get_booking['bookingid'].'-'.get_contact($dbc, $patientid).' | ';
                //$total_bb = $get_bb['total_bb'];
            } else {
                $ass_patient .= $row_get_booking['bookingid'].'-'.get_contact($dbc, $patientid).' | ';
            }
            $total5_app = 0;
        }
        $total_block_booking += $total_bb5;
        $block_booking = (($total_bb5 / $total_injury['total_assessment']) * 100);
        //$block_booking = (($total_bb5 * 100) / $total_completed_booking['total_completed_booking']);

        //$total_bb_booking = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_bb_booking FROM booking WHERE therapistsid = '$therapistsid' AND block_booking = 1 AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));
        //$bb_rate = (($total_bb_booking['total_bb_booking'] / $total_injury['total_assessment']) * 100);
        //$bb_rate = (($total_bb_booking['total_bb_booking'] / $total_booking['total_booking']) * 100);

        //Block Booking

        //<td>A:'.$total_active['total_active']. ' : C:'.$total_completed_booking['total_completed_booking']. ' : '.number_format($avail_hours_sch, 2).'%</td>

        //<td>'.$total_newclient['total_newclient'].'</td>

        $report_data .= '<tr nobr="true">
        <td>'.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</td>
        <td>'.$total_booking['total_booking'].'</td>
        <td>'.$total_completed_booking['total_completed_booking'].'</td>
        <td>('.$total_completed_booking['total_completed_booking'].'/'.$total_booking['total_booking'].')*100='.number_format($arrival_rate, 2).'%</td>
        <td>'.$total_comp_book_of_dis_pat_value.'/'.$total_dc_patient.'='.number_format($avg_visit_discharge_new, 2).'<br>'.$total_dp_name.'</td>
        <td>('.$total_completed_booking['total_completed_booking'].'/'.$stat_hours.')*100='.number_format($avail_hours_sch, 2).'%</td>
        <td>'.$total_injury['total_assessment'].'</td>';

        if($therapist != 'Massage Therapist') {
            //$report_data .= '<td>('.$total_bb.'/'.$total_injury['total_assessment'].')*100='.number_format((float)($block_booking), 2, '.', '').'%<br>BB : '.$bb_patient.'<br>AS : '.$ass_patient.'</td>';

            $report_data .= '<td>('.$total_bb5.'/'.$total_injury['total_assessment'].')*100='.number_format((float)($block_booking), 2, '.', '').'%</td>';

        }
        $report_data .= '</tr>';

        if(number_format($arrival_rate, 2) != '0.00') {
            $total_entry1++;
        }
        if(number_format($avail_hours_sch, 2) != '0.00') {
            $total_entry2++;
        }
        if($block_booking != 0) {
            $total_entry3++;
        }

        if(number_format($avg_visit_discharge_new, 2) != '0.00') {
            $total_entry4++;
        }

        $c1 += $total_booking['total_booking'];
        $c2 += $total_completed_booking['total_completed_booking'];

        $c33 += $arrival_rate;
        $c4 += $avg_visit_discharge_new;
        $c55 += $avail_hours_sch;
        $c6 += $total_newclient['total_newclient'];
        $c7 += $total_injury['total_assessment'];
        $c88 += number_format((float)($block_booking), 2, '.', '');
    }

    $c3 = number_format($c33/$total_entry1, 2);
    $c5 = number_format($c55/$total_entry2, 2);
    //$c8 = number_format($c88/$total_entry3, 2);
    $c8 = ($total_block_booking/$c7)*100;
    $cdis = number_format($c4/$total_entry4, 2);

    $report_data .= '<tr nobr="true"><td><b>Average/Total</b></td><td><b>'.$c1.'</b></td><td><b>'.$c2.'</b></td><td><b>'.number_format((($c2/$c1)*100), 2).'%</b></td><td><b>'.$cdis.'</b></td><td><b>'.$c5.'%</b></td><td><b>'.$c7.'</b></td>';

    if($therapist != 'Massage Therapist') {
        $report_data .= '<td><b>('.$total_block_booking.'/'.$c7.')*100 = '.number_format($c8,2).'%</b></td>';
    }

    $report_data .= '</tr>';

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