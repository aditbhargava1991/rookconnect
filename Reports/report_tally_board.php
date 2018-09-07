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
            $footer_text = 'Assessment Tally Board From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "C", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : Displays by Staff member how many assessments have been completed during the selected date range.";
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

    $html .= '<br><br>' . report_goals($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '', $therapistpdf, 'print');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/assessment_tally_board_on_'.$today_date.'.pdf', 'F');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/assessment_tally_board_on_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $therapist = $therapistpdf;
    } ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Displays by Staff member how many assessments have been completed during the selected date range.</div>
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
					<label class="col-sm-4">Staff:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select a Staff..." name="therapist" class="chosen-select-deselect form-control1" width="380">
							<option <?php if ($therapist=='') echo 'selected="selected"';?> value="">Select All</option>
							<option <?php if ($therapist=='Physical Therapist') echo 'selected="selected"';?> value="Physical Therapist">Physical Therapist</option>
							<option <?php if ($therapist=='Massage Therapist') echo 'selected="selected"';?> value="Massage Therapist">Massage Therapist</option>
							<option <?php if ($therapist=='Osteopathic Therapist') echo 'selected="selected"';?> value="Osteopathic Therapist">Osteopathic Therapist</option>
							<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND (category_contact = 'Physical Therapist' OR category_contact = 'Massage Therapist' OR category_contact = 'Osteopathic Therapist') AND deleted=0 AND status=1"),MYSQLI_ASSOC));
							foreach($query as $rowid) {
								echo "<option ".($rowid == $therapist ? 'selected' : '')." value='$rowid'>".get_contact($dbc, $rowid)."</option>";
							} ?>
						</select>
					</div>
				</div>                
				<button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
			</center>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="therapistpdf" value="<?php echo $therapist; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
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

		$report_data = '<br><table border="1px" class="table table-bordered" style="'.$table_style.'">
		<tr style="'.$table_row_style.'" nobr="true">
		<th width="20%">
            <center>
                <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="The staff member"><img src="'. WEBSITE_URL .'/img/info-w.png" width="20" style="padding-bottom:5px;"></a></span><br />
                Staff
            </center>
        </th>
		<th width="80%">
			<center>
				<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Number of assessments in a given time period."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20" style="padding-bottom:5px;"></a></span><br />
				Assessment Count per Patient
			</center>
		</th>';
		$report_data .= '</tr>';

	} else {
		//print PDF

		$report_data = '<table border="1px" class="table table-bordered" style="'.$table_style.'">
		<tr style="'.$table_row_style.'" nobr="true">
		<th width="20%"></th>
		<th width="80%">Assessment Count per Patient</th>
        </tr>';
	}

    if($therapist == '') {
		$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name, scheduled_hours FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND (category_contact = 'Physical Therapist' OR category_contact = 'Massage Therapist') AND deleted=0 AND status=1"),MYSQLI_ASSOC));
    } else if($therapist == 'Physical Therapist') {
		$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name, scheduled_hours FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND contactid != '6896' AND contactid != '6909' AND category_contact = 'Physical Therapist' AND deleted=0 AND status=1"),MYSQLI_ASSOC));
    } else if($therapist == 'Massage Therapist') {
		$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name, scheduled_hours FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND contactid != '6896' AND contactid != '6909' AND category_contact = 'Massage Therapist' AND deleted=0 AND status=1"),MYSQLI_ASSOC));
    } else if($therapist == 'Osteopathic Therapist') {
		$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name, scheduled_hours FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND contactid != '6896' AND contactid != '6909' AND category_contact = 'Osteopathic Therapist' AND deleted=0 AND status=1"),MYSQLI_ASSOC));
    } else {
		$result = [$therapist];
    }

    $c7 = 0;

    $total_block_booking = 0;
    foreach($result as $rowid) {
		$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `scheduled_hours` FROM `contacts` WHERE `contactid`='$rowid'"));
        $therapistsid = $row['contactid'];

        //Assessment Count
        // $total_injury = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_assessment FROM booking WHERE therapistsid = '$therapistsid' AND type IN('A','C','F','H','N','U','S','T','U') AND (follow_up_call_status = 'Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status = 'Paid' OR follow_up_call_status = 'Invoiced') AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));
        //Assessment Count

        $patient_injury = mysqli_query($dbc,"SELECT patientid, COUNT(bookingid) AS total_assessment FROM booking WHERE therapistsid = '$therapistsid' AND type IN('A','C','F','H','N','U','S','T','U') AND (follow_up_call_status = 'Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status = 'Paid' OR follow_up_call_status = 'Invoiced') AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."') GROUP BY patientid");

        $report_data .= '<tr nobr="true">
        <td>'.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</td>
        <td>';
        while ($patient = mysqli_fetch_array($patient_injury)) {
            $report_data .= '<a href="../Contacts/add_contacts.php?category=Patient&contactid='.$patient['patientid'].'">'.get_contact($dbc, $patient['patientid']).'</a>: '.$patient['total_assessment'].'<br />';
            $c7 += $patient['total_assessment'];
        }
        if ($patient_injury->num_rows == 0) {
            $report_data .= '0';
        }
        $report_data .= '</td></tr>';

        // $c7 += $total_injury['total_assessment'];
    }

    $report_data .= '<tr nobr="true"><td><b>Total</b></td><td><b>'.$c7.'</b></td>';

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