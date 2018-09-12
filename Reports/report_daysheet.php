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
	$therapistpdf = $_POST['therapistpdf'];

	DEFINE('START_DATE', $starttimepdf);
	DEFINE('REPORT_LOGO', get_config($dbc, 'report_logo'));
	DEFINE('REPORT_HEADER', html_entity_decode(get_config($dbc, 'report_header')));
	DEFINE('REPORT_FOOTER', html_entity_decode(get_config($dbc, 'report_footer')));

	class MYPDF extends TCPDF {

		public function Header() {
			//$image_file = WEBSITE_URL.'/img/Clinic-Ace-Logo-Final-250px.png';
			//$this->SetFont('helvetica', '', 13);
			//$this->Image($image_file, 0, 10, 60, '', 'PNG', '', 'T', false, 200, 'C', false, false, 0, false, false, false);
			//$footer_text = 'Daysheet <b>'.START_DATE.'</b>';
			//$this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);
		}

		// Page footer
		public function Footer() {
			/*
			$this->SetY(-24);
			$this->SetFont('helvetica', 'I', 9);
			$footer_text = '<span style="text-align:left;">'.REPORT_FOOTER.'</span>';
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);

			// Position at 15 mm from bottom
			$this->SetY(-15);
			$this->SetFont('helvetica', 'I', 9);
			$footer_text = '<span style="text-align:right;">Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages().' printed on '.date('Y-m-d H:i:s').'</span>';
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "R", true);
			*/
		}
	}

	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
	$pdf->setFooterData(array(0,64,0), array(0,64,128));

	$pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage();
	$pdf->SetFont('helvetica', '', 8);

	$html .= report_daysheet($dbc, $starttimepdf, 'padding:1px; border:1px solid black;', '', '', $therapistpdf, false);

	$today_date = date('Y-m-d');
	$pdf->writeHTML(utf8_encode($html), true, false, true, false, '');
	$pdf->Output('Download/daysheet_'.START_DATE.'.pdf', 'F');
    track_download($dbc, 'report_daysheet', 0, WEBSITE_URL.'/Reports/Download/daysheet_'.$today_date.'.pdf', 'Daysheet Report');

	?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/daysheet_<?php echo START_DATE;?>.pdf', 'fullscreen=yes');
	</script>
	<?php
	$starttime = $starttimepdf;
	$therapist = $therapistpdf;
}

if (isset($_POST['printallpdf'])) {
	$starttimepdf = $_POST['starttimepdf'];
	$therapistpdf = $_POST['therapistpdf'];

	DEFINE('START_DATE', $starttimepdf);
	DEFINE('REPORT_LOGO', get_config($dbc, 'report_logo'));
	DEFINE('REPORT_HEADER', html_entity_decode(get_config($dbc, 'report_header')));
	DEFINE('REPORT_FOOTER', html_entity_decode(get_config($dbc, 'report_footer')));

	class MYPDF extends TCPDF {

		public function Header() {
			//$image_file = WEBSITE_URL.'/img/Clinic-Ace-Logo-Final-250px.png';
			//$this->SetFont('helvetica', '', 13);
			//$this->Image($image_file, 0, 10, 60, '', 'PNG', '', 'T', false, 200, 'C', false, false, 0, false, false, false);
			//$footer_text = 'Daysheet <b>'.START_DATE.'</b>';
			//$this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);
		}

		// Page footer
		public function Footer() {
			/*
			$this->SetY(-24);
			$this->SetFont('helvetica', 'I', 9);
			$footer_text = '<span style="text-align:left;">'.REPORT_FOOTER.'</span>';
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);

			// Position at 15 mm from bottom
			$this->SetY(-15);
			$this->SetFont('helvetica', 'I', 9);
			$footer_text = '<span style="text-align:right;">Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages().' printed on '.date('Y-m-d H:i:s').'</span>';
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "R", true);
			*/
		}
	}

	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
	$pdf->setFooterData(array(0,64,0), array(0,64,128));

	$pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$sorted_therapists = mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `contactid` IN (SELECT therapistsid FROM booking WHERE type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) = '".$starttimepdf."' GROUP BY therapistsid)"), MYSQLI_ASSOC);
	$sorted_therapists = sort_contacts_array($sorted_therapists);

	foreach($sorted_therapists as $therapist) {
		$pdf->AddPage();
		$pdf->SetFont('helvetica', '', 8);
		$html = report_all_daysheet($dbc, $starttimepdf, 'padding:1px; border:1px solid black;', '', '', $therapist, false);

		$pdf->writeHTML(utf8_encode($html), true, false, true, false, '');
	}

	$today_date = date('Y-m-d');
	$pdf->Output('Download/daysheet_'.START_DATE.'.pdf', 'F');
	?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/daysheet_<?php echo START_DATE;?>.pdf', 'fullscreen=yes');
	</script>
	<?php
	$starttime = $starttimepdf;
	$therapist = $therapistpdf;
}

?>

<script type="text/javascript">
function set_followup(dropdown) {
	$.ajax({
		method: 'POST',
		url: '../ajax_all.php?fill=assessment_followup',
		data: { booking: $(dropdown).data('id'), followup: dropdown.value },
		success: function() {
		}
	});
}
$(document).on('change', 'select.set_followup_onchange', function() { set_followup(this); });
</script>
		<div class="notice double-gap-bottom popover-examples">
			<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
			<div class="col-sm-11"><span class="notice-name">NOTE:</span>
			The Therapist Day Sheet provides a daily schedule summary for each staff. The report that it generates shows one full day of all the appointments and the duration of each appointment.</div>
			<div class="clearfix"></div>
		</div>

		<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
			<input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
			<input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

			<?php
			if (isset($_POST['search_email_submit'])) {
				$starttime = $_POST['starttime'];
				$therapist = $_POST['therapist'];
			}

			if($starttime == 0000-00-00) {
				$starttime = date('Y-m-d');
			}

			?>

			<center>
				<div class="col-sm-5">
					<label class="col-sm-4"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="This is where you select the day that you would like to create the Day Sheet for."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20" style="padding-bottom:5px;" /></a></span>For Date:</label>
					<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
				</div>
				<div class="col-sm-5">
					<label class="col-sm-4 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="This is where you select the Therapist for the Day Sheet."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20" style="padding-bottom:5px;" /></a></span> Therapist:</label>
					<div class="col-sm-8"><select data-placeholder="Choose a Therapist..." name="therapist" class="chosen-select-deselect form-control1">
						<option value=""></option>
						<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND (category_contact = 'Physical Therapist' OR category_contact = 'Massage Therapist' OR category_contact = 'Osteopathic Therapist') AND deleted=0 AND status=1"),MYSQLI_ASSOC));
						foreach($query as $rowid) {
							echo "<option ".($rowid == $therapist ? 'selected' : '')." value='$rowid'>".get_contact($dbc, $rowid)."</option>";
						} ?>
					</select></div></div>

				<div class="col-sm-2"><button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div>
			</center>



			<input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
			<input type="hidden" name="therapistpdf" value="<?php echo $therapist; ?>">

			<button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
			<button type="submit" name="printallpdf" value="Print Report" class="btn brand-btn pull-right">Print All Therapist Reports</button>
			<br><br>
			<!--
			<button type="submit" name="printtherapistpdf" value="Print Report" class="btn brand-btn pull-right">Print Therapist Report</button>
			-->

			<br><br>

			<?php
				echo report_daysheet($dbc, $starttime, '', '', '', $therapist);
			?>

		</form>

<?php
function report_all_daysheet($dbc, $starttime, $table_style, $table_row_style, $grand_total_style, $therapist, $screen_mode = true) {
	$report_data = '';

	$today_day = date('w', strtotime($starttime));

	$scheduled_h = explode('*', get_all_form_contact($dbc, $therapist, 'scheduled_hours'));
	$report_data .= get_contact($dbc, $therapist).' : '.$starttime.' ['.$scheduled_h[$today_day].']'.'&nbsp;&nbsp;';

	$report_data .= '<img src="'.WEBSITE_URL.'/img/filled_star.png" width="10" height="10" border="0" alt="">&nbsp;Assessment&nbsp;&nbsp;';
	$report_data .= '<img src="'.WEBSITE_URL.'/img/orange.png" width="10" height="10" border="0" alt="">  &nbsp;Block Booking&nbsp;&nbsp;';
	$report_data .= '<img src="'.WEBSITE_URL.'/img/Maintenance.png" style="height:10px; width:13px;" width="14" height="9" border="0" alt="">&nbsp;Maintenance Patient&nbsp;&nbsp;';
	$report_data .= '<img src="'.WEBSITE_URL.'/img/red.png" width="10" height="10" border="0" alt="">  &nbsp;Block Slot&nbsp;&nbsp;<br>';

	$report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
	$report_data .= '<tr style="'.$table_row_style.'">
	<th style="width: 10%"></th>
	<th style="width: 8%">Appt Time</th>
	<th style="width: 3%">Dur</th>
	<th style="width: 20%">Patient</th>
	<th style="width: 35%">Injury</th>
	<th style="width: 24%">Appt. Type</th>';
	$report_data .= "</tr>";

	//$all_time = array('06:00 am', '06:15 am', '06:30 am', '06:45 am', '07:00 am', '07:15 am', '07:30 am', '07:45 am', '08:00 am', '08:15 am', '08:30 am', '08:45 am', '09:00 am', '09:15 am', '09:30 am', '09:45 am', '10:00 am', '10:15 am', '10:30 am', '10:45 am', '11:00 am', '11:15 am', '11:30 am', '11:45 am', '12:00 pm', '12:15 pm', '12:30 pm', '12:45 pm', '01:00 pm', '01:15 pm', '01:30 pm', '01:45 pm', '02:00 pm', '02:15 pm', '02:30 pm', '02:45 pm', '03:00 pm', '03:15 pm', '03:30 pm', '03:45 pm', '04:00 pm', '04:15 pm', '04:30 pm', '04:45 pm', '05:00 pm', '05:15 pm', '05:30 pm', '05:45 pm', '06:00 pm', '06:15 pm', '06:30 pm', '06:45 pm', '07:00 pm', '07:15 pm', '07:30 pm', '07:45 pm');

	if (strpos($_SERVER['SERVER_NAME'], 'ncthorncliffe') !== false) {
		$all_time = array('06:00:00', '06:15:00', '06:30:00', '06:45:00', '07:00:00', '07:15:00', '07:30:00', '07:45:00', '08:00:00', '08:15:00', '08:30:00', '08:45:00', '09:00:00', '09:15:00', '09:30:00', '09:45:00', '10:00:00', '10:15:00', '10:30:00', '10:45:00', '11:00:00', '11:15:00', '11:30:00', '11:45:00', '12:00:00', '12:15:00', '12:30:00', '12:45:00', '13:00:00', '13:15:00', '13:30:00', '13:45:00', '14:00:00', '14:15:00', '14:30:00', '14:45:00', '15:00:00', '15:15:00', '15:30:00', '15:45:00', '16:00:00', '16:15:00', '16:30:00', '16:45:00', '17:00:00', '17:15:00', '17:30:00', '17:45:00', '18:00:00', '18:15:00', '18:30:00', '18:45:00', '19:00:00');
	} else {
		$all_time = array('06:00:00', '06:15:00', '06:30:00', '06:45:00', '07:00:00', '07:15:00', '07:30:00', '07:45:00', '08:00:00', '08:15:00', '08:30:00', '08:45:00', '09:00:00', '09:15:00', '09:30:00', '09:45:00', '10:00:00', '10:15:00', '10:30:00', '10:45:00', '11:00:00', '11:15:00', '11:30:00', '11:45:00', '12:00:00', '12:15:00', '12:30:00', '12:45:00', '13:00:00', '13:15:00', '13:30:00', '13:45:00', '14:00:00', '14:15:00', '14:30:00', '14:45:00', '15:00:00', '15:15:00', '15:30:00', '15:45:00', '16:00:00', '16:15:00', '16:30:00', '16:45:00', '17:00:00', '17:15:00', '17:30:00', '17:45:00', '18:00:00', '18:15:00', '18:30:00', '18:45:00', '19:00:00', '19:15:00', '19:30:00', '19:45:00', '20:00:00', '20:15:00');
	}

	$block_appt = 15;

	foreach ($all_time as $grid) {
		$row_report = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM booking WHERE therapistsid = '$therapist' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) = '".$starttime."' AND  substr(appoint_date,12,19) = '".$grid."' ORDER BY appoint_date"));

		if($row_report['bookingid'] != '') {

			$appt_start = explode(' ', $row_report['appoint_date']);
			$appt_end = explode(' ', $row_report['end_appoint_date']);

			$start_time = date("h:i a", strtotime($appt_start[1]));
			$end_time = date("h:i a", strtotime($appt_end[1]));

			$injuryid = $row_report['injuryid'];
			$type = $row_report['type'];
			$bookingid = $row_report['bookingid'];

			$get_booking = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT bookingid, appoint_date, today_date FROM booking WHERE injuryid='$injuryid' AND type IN('A','C','F','H','N','U')"));
			$ass_bookingid = $get_booking['bookingid'];
			$bb_appoint_date = explode(' ', $get_booking['appoint_date']);
			$final_ass_appoint_date = $bb_appoint_date[0];

			$bb = 0;

			$get_bb = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_bb FROM booking WHERE injuryid='$injuryid' AND today_date = '".$final_ass_appoint_date."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) > '".$final_ass_appoint_date."'"));

			if($get_bb['total_bb'] >= 4 && $type != 'I' && $type != 'E' && $type != 'P' && $type != 'Q' && $type != 'R') {
				$bb = 1;
			}

			$maintenance = get_all_form_contact($dbc, $row_report['patientid'], 'maintenance');

			$report_data .= '<tr nobr="true">';

			$report_data .= '<td>';
			if($bb == 1) {
				$report_data .= '&nbsp;<img src="'.WEBSITE_URL.'/img/orange.png" style="height:10px; width:20px;" width="20" height="10" border="0" alt="">';
			}

			if($type == 'A' || $type == 'C' || $type == 'F' || $type == 'H' || $type == 'N' || $type == 'U') {
				$report_data .= '&nbsp;<img src="'.WEBSITE_URL.'/img/filled_star.png" style="height:20px; width:20px;" width="8" height="10" border="0" alt="">';
			}

			if($maintenance == 1) {
				$report_data .= '&nbsp;<img src="'.WEBSITE_URL.'/img/Maintenance.png" style="height:20px; width:25px;" width="14" height="9" border="0" alt="">';
			}
			$report_data .= '</td>';

			//$report_data .= '<td>'.$start_time. ' - '.$end_time .'</td>';
			$report_data .= '<td>&nbsp;'.$start_time.'</td>';

			$from_time = strtotime(date('Y-m-d').' '.$appt_start[1]);
			$to_time = strtotime(date('Y-m-d').' '.$appt_end[1]);
			$dur = round(abs($to_time - $from_time) / 60,2);
			if($row_report['patientid'] != '' && $row_report['patientid'] != '0') {
				$report_data .= '<td>&nbsp;'.$dur.'</td>';
			} else {
				$report_data .= '<td></td>';
			}

			$report_data .= '<td>&nbsp;'.get_contact($dbc, $row_report['patientid']).'</td>';

			//$report_data .= '<td>&nbsp;' . get_all_from_injury($dbc, $row_report['injuryid'], 'injury_name').' - '.get_all_from_injury($dbc, $row_report['injuryid'], 'injury_type').' : '.get_all_from_injury($dbc, $row_report['injuryid'], 'injury_date'). '</td>';
			$report_data .= '<td>&nbsp;' . get_all_from_injury($dbc, $row_report['injuryid'], 'injury_name').' - '.get_all_from_injury($dbc, $row_report['injuryid'], 'injury_type'). '</td>';
			$serviceid = get_all_from_injury($dbc, $row_report['injuryid'], 'serviceid');
			//$report_data .= '<td>&nbsp;'.get_type_from_booking($dbc, $row_report['type']).' : $'.get_all_from_service($dbc, $serviceid, 'fee').'</td>';
			$report_data .= '<td>&nbsp;'.get_type_from_booking($dbc, $row_report['type']).'</td>';

			$report_data .= '</tr>';

			if($type != 'I' && $type != 'E' && $type != 'P' && $type != 'Q' && $type != 'R') {
				$block_appt = $dur;
			}
		} else {
			$style = '';
			if($block_appt != 15) {
				$style = 'style="background-color: #D8D8D8;"';
				$block_appt -= 15;
			}

			$report_data .= '<tr nobr="true" '.$style.'>';

			$report_data .= '<td></td>';

			$report_data .= '<td>&nbsp;'.date("h:i a", strtotime($grid)) .'</td>';

			$report_data .= '<td></td>';

			$report_data .= '<td></td>';
			$report_data .= '<td></td>';
			$report_data .= '<td></td>';

			$report_data .= '</tr>';
		}

	}

	$report_data .= '</table>';

    $assessment_date1 = strtotime('-1 day', strtotime($starttime));
    $assessment_date = date('Y-m-d', $assessment_date1);

	$get_booking = mysqli_query($dbc,"SELECT patientid, type FROM booking WHERE (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) = '".$assessment_date."' AND therapistsid = '$therapist' AND type IN('A','C','F','H','N','U')");

    $num_rows = mysqli_num_rows($get_booking);

    if($num_rows > 0) {
        $report_data .= '<br><br><br><h2>Assessments from '.$assessment_date.'</h2><table border="1px" class="table table-bordered" style="'.$table_style.'">
        <tr style="'.$table_row_style.'" nobr="true">
        <th width="15%">Patient</th>
        <th width="15%">Phone</th>
        <th width="30%">Email</th>
        <th width="20%">Address</th>
        <th width="20%">Appt. Type</th>
        </tr>';

        while($row_report1 = mysqli_fetch_array($get_booking)) {
            $patientid = $row_report1['patientid'];

            $report_data .= '<tr nobr="true">';

			$report_data .= '<td><a href="../Contacts/add_contacts.php?category=Patient&contactid='.$patientid.'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">'.get_contact($dbc, $patientid). '</a></td>';

            $report_data .= '<td>' . get_contact_phone($dbc, $patientid) . '</td>';
            $report_data .= '<td>' . get_email($dbc, $patientid) . '</td>';
            $report_data .= '<td>' . get_address($dbc, $patientid) . '</td>';
            $report_data .= '<td>'.get_type_from_booking($dbc, $row_report1['type']).'</td>';
            $report_data .= "</tr>";

        }

        $report_data .= '</table>';

    }

	$report_validation = mysqli_query($dbc,"SELECT `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`contactid`, `patient_injury`.`injury_name`, `patient_injury`.`injury_type`, `booking`.`appoint_date`, `booking`.`assessment_followup_date`, `booking`.`bookingid` FROM `booking` LEFT JOIN `contacts` ON `booking`.`patientid`=`contacts`.`contactid` LEFT JOIN `patient_injury` ON `booking`.`injuryid`=`patient_injury`.`injuryid` WHERE `booking`.`therapistsid`='$therapist' AND `booking`.`type` IN ('A','C','F','H','N','U') AND `assessment_followup_date` IS NULL AND `booking`.`deleted`=0 AND `booking`.`appoint_date` < '$starttime' AND `contacts`.`deleted`=0 ORDER BY `appoint_date` ASC");

	$data = 0;
	$html_table = '';

	while($row_report = mysqli_fetch_array($report_validation)) {
		$data = 1;

		$html_table .= '<tr nobr="true">';

		$html_table .= '<td><a href="../Contacts/add_contacts.php?category=Patient&contactid='.$row_report['contactid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">'.decryptIt($row_report['first_name']).' '.decryptIt($row_report['last_name']). '</a></td>';
		$html_table .= '<td>' . get_contact_phone($dbc, $row_report['contactid']) . '</td>';
		$html_table .= '<td>' . $row_report['injury_name'].' : '.$row_report['injury_type'] . '</td>';
		$html_table .= '<td>' . $row_report['appoint_date'] . '</td>';
		$html_table .= '<td>'.($screen_mode ? '<select class="chosen-select form-control set_followup_onchange"  data-id="'.$row_report['bookingid'].'"><option></option>
				<option value="Complete">Follow Up Completed</option>
				<option '.($row_report['assessment_followup_date'] == null ? 'selected' : '').' value="Incomplete">Not Complete</option>
			</select>' : 'Not Complete').'</td>';
		$html_table .= '</tr>';
	}

	if($data == 1) {
		$report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
		$report_data .= '<tr style="'.$table_row_style.'" nobr="true">
		<th style="width:25%">Patient</th>
		<th style="width:20%">Telephone</th>
		<th style="width:20%">Injury</th>
		<th style="width:20%">Appointment Date</th>
		<th style="width:15%">Follow Up Status</th>';
		$report_data .= "</tr>";

		$report_data .= $html_table."</table>";
	} else {
		$report_data .= "No Assessment Follow Ups Outstanding";
	}

	return $report_data;
}


function report_daysheet($dbc, $starttime, $table_style, $table_row_style, $grand_total_style, $therapist, $screen_mode = true) {

	$report_data = '';
	$today_day = date('w', strtotime($starttime));


	$scheduled_h = explode('*', get_all_form_contact($dbc, $therapist, 'scheduled_hours'));
	$report_data .= get_contact($dbc, $therapist).' : '.$starttime.' ['.$scheduled_h[$today_day].']'.'&nbsp;&nbsp;';

	$report_data .= '<img src="'.WEBSITE_URL.'/img/filled_star.png" width="10" height="10" border="0" alt="">&nbsp;Assessment&nbsp;&nbsp;';
	$report_data .= '<img src="'.WEBSITE_URL.'/img/orange.png" width="10" height="10" border="0" alt="">  &nbsp;Block Booking&nbsp;&nbsp;';
	$report_data .= '<img src="'.WEBSITE_URL.'/img/Maintenance.png" style="height:10px; width:13px;" width="14" height="9" border="0" alt="">&nbsp;Maintenance Patient&nbsp;&nbsp;';
	$report_data .= '<img src="'.WEBSITE_URL.'/img/red.png" width="10" height="10" border="0" alt="">  &nbsp;Block Slot&nbsp;&nbsp;<br><br>';

	$report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
	$report_data .= '<tr style="'.$table_row_style.'">
	<th style="width: 10%"></th>
	<th style="width: 8%">Appt Time</th>
	<th style="width: 3%">Dur</th>
	<th style="width: 20%">Patient</th>
	<th style="width: 35%">Injury</th>
	<th style="width: 24%">Appt. Type</th>';
	$report_data .= "</tr>";

	//$all_time = array('06:00 am', '06:15 am', '06:30 am', '06:45 am', '07:00 am', '07:15 am', '07:30 am', '07:45 am', '08:00 am', '08:15 am', '08:30 am', '08:45 am', '09:00 am', '09:15 am', '09:30 am', '09:45 am', '10:00 am', '10:15 am', '10:30 am', '10:45 am', '11:00 am', '11:15 am', '11:30 am', '11:45 am', '12:00 pm', '12:15 pm', '12:30 pm', '12:45 pm', '01:00 pm', '01:15 pm', '01:30 pm', '01:45 pm', '02:00 pm', '02:15 pm', '02:30 pm', '02:45 pm', '03:00 pm', '03:15 pm', '03:30 pm', '03:45 pm', '04:00 pm', '04:15 pm', '04:30 pm', '04:45 pm', '05:00 pm', '05:15 pm', '05:30 pm', '05:45 pm', '06:00 pm', '06:15 pm', '06:30 pm', '06:45 pm', '07:00 pm', '07:15 pm', '07:30 pm', '07:45 pm');

	if (strpos($_SERVER['SERVER_NAME'], 'ncthorncliffe') !== false) {
		$all_time = array('06:00:00', '06:15:00', '06:30:00', '06:45:00', '07:00:00', '07:15:00', '07:30:00', '07:45:00', '08:00:00', '08:15:00', '08:30:00', '08:45:00', '09:00:00', '09:15:00', '09:30:00', '09:45:00', '10:00:00', '10:15:00', '10:30:00', '10:45:00', '11:00:00', '11:15:00', '11:30:00', '11:45:00', '12:00:00', '12:15:00', '12:30:00', '12:45:00', '13:00:00', '13:15:00', '13:30:00', '13:45:00', '14:00:00', '14:15:00', '14:30:00', '14:45:00', '15:00:00', '15:15:00', '15:30:00', '15:45:00', '16:00:00', '16:15:00', '16:30:00', '16:45:00', '17:00:00', '17:15:00', '17:30:00', '17:45:00', '18:00:00', '18:15:00', '18:30:00', '18:45:00', '19:00:00');
	} else {
		$all_time = array('06:00:00', '06:15:00', '06:30:00', '06:45:00', '07:00:00', '07:15:00', '07:30:00', '07:45:00', '08:00:00', '08:15:00', '08:30:00', '08:45:00', '09:00:00', '09:15:00', '09:30:00', '09:45:00', '10:00:00', '10:15:00', '10:30:00', '10:45:00', '11:00:00', '11:15:00', '11:30:00', '11:45:00', '12:00:00', '12:15:00', '12:30:00', '12:45:00', '13:00:00', '13:15:00', '13:30:00', '13:45:00', '14:00:00', '14:15:00', '14:30:00', '14:45:00', '15:00:00', '15:15:00', '15:30:00', '15:45:00', '16:00:00', '16:15:00', '16:30:00', '16:45:00', '17:00:00', '17:15:00', '17:30:00', '17:45:00', '18:00:00', '18:15:00', '18:30:00', '18:45:00', '19:00:00', '19:15:00', '19:30:00', '19:45:00', '20:00:00', '20:15:00');
	}

	$block_appt = 15;

	foreach ($all_time as $grid) {
		$row_report_query = mysqli_query($dbc,"SELECT * FROM booking WHERE therapistsid = '$therapist' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) = '".$starttime."' AND  substr(appoint_date,12,19) = '".$grid."' ORDER BY appoint_date");
		if(mysqli_num_rows($row_report_query) > 0) {
			while($row_report = mysqli_fetch_array($row_report_query)) {
				$appt_start = explode(' ', $row_report['appoint_date']);
				$appt_end = explode(' ', $row_report['end_appoint_date']);

				$start_time = date("h:i a", strtotime($appt_start[1]));
				$end_time = date("h:i a", strtotime($appt_end[1]));

				$injuryid = $row_report['injuryid'];
				$type = $row_report['type'];
				$bookingid = $row_report['bookingid'];

				$get_booking = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT bookingid, appoint_date, today_date FROM booking WHERE injuryid='$injuryid' AND type IN('A','C','F','H','N','U')"));
				$ass_bookingid = $get_booking['bookingid'];
				$bb_appoint_date = explode(' ', $get_booking['appoint_date']);
				$final_ass_appoint_date = $bb_appoint_date[0];

				$bb = 0;

				$get_bb = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_bb FROM booking WHERE injuryid='$injuryid' AND today_date = '".$final_ass_appoint_date."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) > '".$final_ass_appoint_date."'"));

				if($get_bb['total_bb'] >= 4 && $type != 'I' && $type != 'E' && $type != 'P' && $type != 'Q' && $type != 'R') {
					$bb = 1;
				}

				$maintenance = get_all_form_contact($dbc, $row_report['patientid'], 'maintenance');

				$report_data .= '<tr nobr="true">';

				$report_data .= '<td>';
				if($bb == 1) {
					$report_data .= '&nbsp;<img src="'.WEBSITE_URL.'/img/orange.png" style="height:10px; width:20px;" width="20" height="10" border="0" alt="">';
				}

				if($type == 'A' || $type == 'C' || $type == 'F' || $type == 'H' || $type == 'N' || $type == 'U') {
					$report_data .= '&nbsp;<img src="'.WEBSITE_URL.'/img/filled_star.png" style="height:20px; width:20px;" width="8" height="10" border="0" alt="">';
				}

				if($maintenance == 1) {
					$report_data .= '&nbsp;<img src="'.WEBSITE_URL.'/img/Maintenance.png" style="height:20px; width:25px;" width="14" height="9" border="0" alt="">';
				}
				$report_data .= '</td>';

				//$report_data .= '<td>'.$start_time. ' - '.$end_time .'</td>';
				$report_data .= '<td>&nbsp;'.$start_time.'</td>';

				$from_time = strtotime(date('Y-m-d').' '.$appt_start[1]);
				$to_time = strtotime(date('Y-m-d').' '.$appt_end[1]);
				$dur = round(abs($to_time - $from_time) / 60,2);
				if($row_report['patientid'] != '' && $row_report['patientid'] != '0') {
					$report_data .= '<td>&nbsp;'.$dur.'</td>';
				} else {
					$report_data .= '<td></td>';
				}

				$report_data .= '<td>&nbsp;'.(in_array($row_report['follow_up_call_status'], ['Cancelled','Late Cancellation / No-Show']) ? 'Cancelled: ' : '').get_contact($dbc, $row_report['patientid']).'</td>';

				//$report_data .= '<td>&nbsp;' . get_all_from_injury($dbc, $row_report['injuryid'], 'injury_name').' - '.get_all_from_injury($dbc, $row_report['injuryid'], 'injury_type').' : '.get_all_from_injury($dbc, $row_report['injuryid'], 'injury_date'). '</td>';
				$report_data .= '<td>&nbsp;' . get_all_from_injury($dbc, $row_report['injuryid'], 'injury_name').' - '.get_all_from_injury($dbc, $row_report['injuryid'], 'injury_type'). '</td>';
				$serviceid = get_all_from_injury($dbc, $row_report['injuryid'], 'serviceid');
				//$report_data .= '<td>&nbsp;'.get_type_from_booking($dbc, $row_report['type']).' : $'.get_all_from_service($dbc, $serviceid, 'fee').'</td>';
				$report_data .= '<td>&nbsp;'.get_type_from_booking($dbc, $row_report['type']).'</td>';

				$report_data .= '</tr>';

				if($type != 'I' && $type != 'E' && $type != 'P' && $type != 'Q' && $type != 'R') {
					$block_appt = $dur;
				}
			}
		} else {
			$style = '';
			if($block_appt != 15) {
				$style = 'style="background-color: #D8D8D8;"';
				$block_appt -= 15;
			}

			$report_data .= '<tr nobr="true" '.$style.'>';

			$report_data .= '<td></td>';

			$report_data .= '<td>&nbsp;'.date("h:i a", strtotime($grid)) .'</td>';

			$report_data .= '<td></td>';

			$report_data .= '<td></td>';
			$report_data .= '<td></td>';
			$report_data .= '<td></td>';

			$report_data .= '</tr>';
		}

	}

	$report_data .= '</table>';

    $assessment_date1 = strtotime('-1 day', strtotime($starttime));
    $assessment_date = date('Y-m-d', $assessment_date1);

	$get_booking = mysqli_query($dbc,"SELECT patientid, type FROM booking WHERE (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) = '".$assessment_date."' AND therapistsid = '$therapist' AND type IN('A','C','F','H','N','U')");

    $num_rows = mysqli_num_rows($get_booking);

    if($num_rows > 0) {
        $report_data .= '<br><br><br><h2>Assessments from '.$assessment_date.'</h2><table border="1px" class="table table-bordered" style="'.$table_style.'">
        <tr style="'.$table_row_style.'" nobr="true">
        <th width="15%">Patient</th>
        <th width="15%">Phone</th>
        <th width="30%">Email</th>
        <th width="20%">Address</th>
        <th width="20%">Appt. Type</th>
        </tr>';

        while($row_report1 = mysqli_fetch_array($get_booking)) {
            $patientid = $row_report1['patientid'];

            $report_data .= '<tr nobr="true">';

			$report_data .= '<td><a href="../Contacts/add_contacts.php?category=Patient&contactid='.$patientid.'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">'.get_contact($dbc, $patientid). '</a></td>';

            $report_data .= '<td>' . get_contact_phone($dbc, $patientid) . '</td>';
            $report_data .= '<td>' . get_email($dbc, $patientid) . '</td>';
            $report_data .= '<td>' . get_address($dbc, $patientid) . '</td>';
            $report_data .= '<td>'.get_type_from_booking($dbc, $row_report1['type']).'</td>';
            $report_data .= "</tr>";

        }

        $report_data .= '</table>';

    }

	$report_validation = mysqli_query($dbc,"SELECT `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`contactid`, `patient_injury`.`injury_name`, `patient_injury`.`injury_type`, `booking`.`appoint_date`, `booking`.`assessment_followup_date`, `booking`.`bookingid` FROM `booking` LEFT JOIN `contacts` ON `booking`.`patientid`=`contacts`.`contactid` LEFT JOIN `patient_injury` ON `booking`.`injuryid`=`patient_injury`.`injuryid` WHERE `booking`.`therapistsid`='$therapist' AND `booking`.`type` IN ('A','C','F','H','N','U') AND `assessment_followup_date` IS NULL AND `booking`.`deleted`=0 AND `booking`.`appoint_date` < '$starttime' AND `contacts`.`deleted`=0 ORDER BY `appoint_date` ASC");

	$data = 0;
	$html_table = '';

	while($row_report = mysqli_fetch_array($report_validation)) {
		$data = 1;

		$html_table .= '<tr nobr="true">';

		$html_table .= '<td><a href="../Contacts/add_contacts.php?category=Patient&contactid='.$row_report['contactid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">'.decryptIt($row_report['first_name']).' '.decryptIt($row_report['last_name']). '</a></td>';
		$html_table .= '<td>' . get_contact_phone($dbc, $row_report['contactid']) . '</td>';
		$html_table .= '<td>' . $row_report['injury_name'].' : '.$row_report['injury_type'] . '</td>';
		$html_table .= '<td>' . $row_report['appoint_date'] . '</td>';
		$html_table .= '<td>'.($screen_mode ? '<select class="chosen-select form-control set_followup_onchange" data-id="'.$row_report['bookingid'].'"><option></option>
				<option value="Complete">Follow Up Completed</option>
				<option '.($row_report['assessment_followup_date'] == null ? 'selected' : '').' value="Incomplete">Not Complete</option>
			</select>' : 'Not Complete').'</td>';
		$html_table .= '</tr>';
	}

	if($data == 1) {
		$report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
		$report_data .= '<tr style="'.$table_row_style.'" nobr="true">
		<th style="width:25%">Patient</th>
		<th style="width:20%">Telephone</th>
		<th style="width:20%">Injury</th>
		<th style="width:20%">Appointment Date</th>
		<th style="width:15%">Follow Up Status</th>';
		$report_data .= "</tr>";

		$report_data .= $html_table."</table>";
	} else {
		$report_data .= "<h4>No Assessment Follow Ups Outstanding</h4>";
	}

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