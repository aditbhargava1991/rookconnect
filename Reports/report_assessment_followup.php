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

	$html .= report_followups($dbc, $starttimepdf, $endtimepdf, 'padding:1px; border:1px solid black;', '', '', $therapistpdf, false);

	$today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/assessment_followup_'.START_DATE.'.pdf', 'F');

    track_download($dbc, 'report_assessment_followup', 0, WEBSITE_URL.'/Reports/Download/assessment_followup_'.$today_date.'.pdf', 'Assessment Followup Report');

	?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/assessment_followup_<?php echo START_DATE;?>.pdf', 'fullscreen=yes');
	</script>
	<?php
	$starttime = $starttimepdf;
	$endtime = $endtimepdf;
	$therapist = $therapistpdf;
}

if (isset($_POST['printallpdf'])) {
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

	$sorted_therapists = mysqli_fetch_all(mysqli_query($dbc, "SELECT `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`contactid` FROM `booking` LEFT JOIN `contacts` ON `booking`.`therapistsid`=`contacts`.`contactid` WHERE `booking`.`type` IN ('A','C','F','H','N','U') AND ((`assessment_followup_date` IS NULL AND `booking`.`appoint_date` >= '$starttimepdf' AND `booking`.`appoint_date` <= '$endtimepdf') OR (`assessment_followup_date` >= '$starttimepdf' AND `assessment_followup_date` <= '$endtimepdf')) AND `booking`.`deleted`=0 AND `contacts`.`deleted`=0 AND `contacts`.`status`=1 GROUP BY `contacts`.`contactid`"), MYSQLI_ASSOC);
	$sorted_therapists = sort_contacts_array($sorted_therapists);

	foreach($sorted_therapists as $therapist) {
		$pdf->AddPage();
		$pdf->SetFont('helvetica', '', 8);
		$html = report_all_followups($dbc, $starttimepdf, $endtimepdf, 'padding:1px; border:1px solid black;', '', '', $therapist, false);

		$pdf->writeHTML($html, true, false, true, false, '');
	}
	if(count($sorted_therapists) == 0) {
		$pdf->AddPage();
		$pdf->SetFont('helvetica', '', 8);
		$pdf->writeHTML("<h1>No Therapists with Follow Ups in the selected Date Range</h1>", true, false, true, false, '');
	}

	$today_date = date('Y-m-d');
	$pdf->Output('Download/daysheet_'.START_DATE.'.pdf', 'F');
	?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/daysheet_<?php echo START_DATE;?>.pdf', 'fullscreen=yes');
	</script>
	<?php
	$starttime = $starttimepdf;
	$endtime = $endtimepdf;
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
				<div class="form-group col-sm-5">
					<label class="col-sm-4">From:</label>
					<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
				</div>
                <div class="form-group col-sm-5">
					<label class="col-sm-4">Until:</label>
					<div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
				</div>
                <div class="form-group col-sm-5">
					<label class="col-sm-4"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="This is where you select the Staff for whom the Assessment Follow Ups were completed or need to be completed."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20" style="padding-bottom:5px;" /></a></span> Staff:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select Staff..." name="therapist" class="chosen-select-deselect form-control1">
							<option value=""></option>
							<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status=1")) as $rowid) {
								echo "<option ".($rowid['contactid'] == $therapist ? 'selected' : '')." value='".$rowid['contactid']."'>".$rowid['first_name'].' '.$rowid['last_name']."</option>";
							} ?>
						</select>
					</div>
				</div>
				<div class="col-sm-2"><button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div>
			</center>



			<input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
			<input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
			<input type="hidden" name="therapistpdf" value="<?php echo $therapist; ?>">

			<button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
			<button type="submit" name="printallpdf" value="Print Report" class="btn brand-btn pull-right">Print All Therapist Reports</button>
			<div class="clearfix"></div><br>

			<?php
				echo report_followups($dbc, $starttime, $endtime, '', '', '', $therapist);
			?>

		</form>
		

<?php
function report_all_followups($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $therapist, $screen_mode = true) {
	$report_data = ($screen_mode ? '' : '<h3>Assessment Follow Ups: '.get_contact($dbc, $therapist).': '.$starttime.' - '.$endtime.'</h3>');
	$report_validation = mysqli_query($dbc,"SELECT `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`contactid`, `patient_injury`.`injury_name`, `patient_injury`.`injury_type`, `booking`.`appoint_date`, `booking`.`assessment_followup_date`, `booking`.`bookingid` FROM `booking` LEFT JOIN `contacts` ON `booking`.`patientid`=`contacts`.`contactid` LEFT JOIN `patient_injury` ON `booking`.`injuryid`=`patient_injury`.`injuryid` WHERE `booking`.`therapistsid`='$therapist' AND `booking`.`type` IN ('A','C','F','H','N','U') AND ((`assessment_followup_date` IS NULL AND `booking`.`appoint_date` >= '$starttime' AND `booking`.`appoint_date` <= '$endtime') OR (`assessment_followup_date` >= '$starttime' AND `assessment_followup_date` <= '$endtime')) AND `booking`.`deleted`=0 AND `contacts`.`deleted`=0AND `contacts`.`status`=1 ORDER BY `appoint_date` ASC");

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
				<option '.($row_report['assessment_followup_date'] == null ? '' : 'selected').' value="Complete">Follow Up Completed '.($row_report['assessment_followup_date'] == null ? '' : $row_report['assessment_followup_date']).'</option>
				<option '.($row_report['assessment_followup_date'] == null ? 'selected' : '').' value="Incomplete">Not Complete</option>
			</select>' : ($row_report['assessment_followup_date'] == null ? 'Not Complete' : $row_report['assessment_followup_date'])).'</td>';
		$html_table .= '</tr>';
	}

	if($data == 1) {
		$report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
		$report_data .= '<tr style="'.$table_row_style.'" nobr="true">
		<th style="width:20%">Patient</th>
		<th style="width:20%">Telephone</th>
		<th style="width:20%">Injury</th>
		<th style="width:20%">Appointment Date</th>
		<th style="width:20%">Follow Up Completed</th>';
		$report_data .= "</tr>";

		$report_data .= $html_table."</table>";
	} else {
		$report_data .= "No Assessment Follow Ups Found";
	}

	return $report_data;
}


function report_followups($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $therapist, $screen_mode = true) {
	$report_data = ($screen_mode ? '' : '<h3>Assessment Follow Ups: '.get_contact($dbc, $therapist).': '.$starttime.' - '.$endtime.'</h3>');
	$report_validation = mysqli_query($dbc,"SELECT `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`contactid`, `patient_injury`.`injury_name`, `patient_injury`.`injury_type`, `booking`.`appoint_date`, `booking`.`assessment_followup_date`, `booking`.`bookingid` FROM `booking` LEFT JOIN `contacts` ON `booking`.`patientid`=`contacts`.`contactid` LEFT JOIN `patient_injury` ON `booking`.`injuryid`=`patient_injury`.`injuryid` WHERE `booking`.`therapistsid`='$therapist' AND `booking`.`type` IN ('A','C','F','H','N','U') AND ((`assessment_followup_date` IS NULL AND `booking`.`appoint_date` >= '$starttime' AND `booking`.`appoint_date` <= '$endtime') OR (`assessment_followup_date` >= '$starttime' AND `assessment_followup_date` <= '$endtime')) AND `booking`.`deleted`=0 AND `contacts`.`deleted`=0 AND `contacts`.`status`=1 ORDER BY `appoint_date` ASC");

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
				<option '.($row_report['assessment_followup_date'] == null ? '' : 'selected').' value="Complete">Follow Up Completed '.($row_report['assessment_followup_date'] == null ? '' : $row_report['assessment_followup_date']).'</option>
				<option '.($row_report['assessment_followup_date'] == null ? 'selected' : '').' value="Incomplete">Not Complete</option>
			</select>' : ($row_report['assessment_followup_date'] == null ? 'Not Complete' : $row_report['assessment_followup_date'])).'</td>';
		$html_table .= '</tr>';
	}

	if($data == 1) {
		$report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
		$report_data .= '<tr style="'.$table_row_style.'" nobr="true">
		<th style="width:20%">Patient</th>
		<th style="width:20%">Telephone</th>
		<th style="width:20%">Injury</th>
		<th style="width:20%">Appointment Date</th>
		<th style="width:20%">Follow Up Completed</th>';
		$report_data .= "</tr>";

		$report_data .= $html_table."</table>";
	} else {
		$report_data .= "No Assessment Follow Ups Found";
	}

	return $report_data;
}
?>