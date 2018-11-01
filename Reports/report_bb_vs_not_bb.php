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
            $footer_text = 'Block Booking From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : Displays an appointment summary of all assessment patients with the assessment date and appointment dates, and displays block bookings for 3,4 and 5 weeks.";
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

    $html .= report_goals($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '', $therapistpdf);

    $today_date = date('Y-m-d');
	$pdf->writeHTML(utf8_encode($html), true, false, true, false, '');
	$pdf->Output('Download/blockbooking_on_'.$today_date.'.pdf', 'F');

    track_download($dbc, 'report_bb_vs_not_bb', 0, WEBSITE_URL.'/Reports/Download/blockbooking_on_'.$today_date.'.pdf', 'Block Booking Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/blockbooking_on_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $therapist = $therapistpdf;
    } ?>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Displays an appointment summary of all assessment patients with the assessment date and appointment dates, and displays block bookings for 3,4 and 5 weeks.</div>
            <div class="clearfix"></div>
        </div>

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
					<label class="col-sm-4">Staff:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select a Staff..." name="therapist" class="chosen-select-deselect form-control1" width="380">
							<option value="">Select All</option>
							<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status=1"),MYSQLI_ASSOC));
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
                //echo '<a href="report_goals.php?goals=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a><br><br>';

                echo report_goals($dbc, $starttime, $endtime, '', '', '', $therapist);
            ?>

        </form>
<?php
function report_goals($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $therapist) {

    $report_data = '<table border="1px" class="table table-bordered" style="'.$table_style.'">
    <tr style="'.$table_row_style.'" nobr="true">
    <th width="10%">Staff</th>
    <th width="45%">Assessment Patients</th>
    <th width="45%">Block Booking Patients</th>
    </tr>';

    if($therapist == '') {
		$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name, scheduled_hours FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND contactid != '6896' AND contactid != '6909' AND deleted=0 AND status=1"),MYSQLI_ASSOC));
    } else {
		$result = [ $therapist ];
    }

    $total_entry1 = 0;
    $total_entry2 = 0;
    $total_entry3 = 0;
    foreach($result as $therapistsid) {
		$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT contactid, first_name, last_name, scheduled_hours FROM contacts WHERE contactid='$therapistsid'"));
        $category_contact = get_all_form_contact($dbc, $therapistsid, 'category_contact');

        //Assessment Count
        $total_injury = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_assessment FROM booking WHERE therapistsid = '$therapistsid' AND type IN('A','C','F','H','N','U') AND (follow_up_call_status = 'Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status = 'Paid' OR follow_up_call_status = 'Invoiced') AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));
        //Assessment Count

        //Block Booking

        /*
        $get_booking = mysqli_query($dbc,"SELECT bookingid, appoint_date, today_date, patientid FROM booking WHERE therapistsid = '$therapistsid' AND type IN('A','C','F','H','N','U') AND (follow_up_call_status = 'Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status = 'Paid' OR follow_up_call_status = 'Invoiced') AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')");
        $total_bb = 0;
        $bb_patient = '';
        $ass_patient = '';
        while($row_get_booking = mysqli_fetch_array($get_booking)) {
            $bb_appoint_date = explode(' ', $row_get_booking['appoint_date']);
            $final_ass_appoint_date = $bb_appoint_date[0];
            $patientid = $row_get_booking['patientid'];
            $type = $row_get_booking['type'];

            $get_bb = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_bb FROM booking WHERE patientid='$patientid' AND today_date = '".$final_ass_appoint_date."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) > '".$final_ass_appoint_date."'"));

            if($get_bb['total_bb'] >= 3) {

                if(get_contact($dbc, $patientid) == 'Shawn Press') {
                    $get_bb1 = mysqli_query($dbc,"SELECT bookingid, appoint_date FROM booking WHERE patientid='$patientid' AND today_date = '".$final_ass_appoint_date."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) > '".$final_ass_appoint_date."'");

                    while($row_get_booking1 = mysqli_fetch_array($get_bb1)) {
                        $bb_patient .= $row_get_booking1['bookingid'].' : '.$row_get_booking1['appoint_date'].'<br>';
                    }
                }

                $total_bb++;
                $bb_patient .= get_contact($dbc, $patientid).'<br>';
                $ass_patient .= get_contact($dbc, $patientid).'<br>';
                //$total_bb = $get_bb['total_bb'];
            } else {
                $ass_patient .= get_contact($dbc, $patientid).'<br>';
            }
        }
        $block_booking = (($total_bb / $total_injury['total_assessment']) * 100);
        */

        $get_booking = mysqli_query($dbc,"SELECT bookingid, appoint_date, today_date, patientid, create_by FROM booking WHERE therapistsid = '$therapistsid' AND type IN('A','C','F','H','N','U') AND (follow_up_call_status = 'Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status = 'Paid' OR follow_up_call_status = 'Invoiced') AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."') AND `patientid` > 0 ORDER BY appoint_date");
        $total_bb = 0;
        $total_bb4 = 0;
        $total_bb5 = 0;
        $total_bb6 = 0;
        $bb_patient = '';
        $ass_patient = '';
        $total_app = 0;
        $bb4_patient = '';
        $bb5_patient = '';
        $bb6_patient = '';
        $total4_app = 0;
        $total5_app = 0;
        $total6_app = 0;

        while($row_get_booking = mysqli_fetch_array($get_booking)) {
            $bb_appoint_date = explode(' ', $row_get_booking['appoint_date']);
            $final_ass_appoint_date = $bb_appoint_date[0];
            $patientid = $row_get_booking['patientid'];
            $type = $row_get_booking['type'];

            $all_app = 'Assessment Date '.$final_ass_appoint_date.' Booked on '.$row_get_booking['today_date'].' by '.$row_get_booking['create_by'].'<br>';

            $get_booking1 = mysqli_query($dbc,"SELECT bookingid, appoint_date, today_date, patientid, create_by FROM booking WHERE type NOT IN('A','C','F','H','N','U') AND patientid='$patientid' AND  (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$final_ass_appoint_date."' ORDER BY appoint_date");
            while($row_get_booking1 = mysqli_fetch_array($get_booking1)) {
                $next_app_date = explode(' ', $row_get_booking1['appoint_date']);
                $all_app .= 'Appt Date '.$next_app_date[0].' Booked on '.$row_get_booking1['today_date'].' by '.$row_get_booking1['create_by'].'<br>';
            }

            $next_appoint1 = date('Y-m-d', strtotime("+7 day", strtotime($final_ass_appoint_date)));
            $next_appoint2 = date('Y-m-d', strtotime("+7 day", strtotime($next_appoint1)));
            $next_appoint3 = date('Y-m-d', strtotime("+7 day", strtotime($next_appoint2)));

            $next_appoint4 = date('Y-m-d', strtotime("+7 day", strtotime($next_appoint3)));
            $next_appoint5 = date('Y-m-d', strtotime("+7 day", strtotime($next_appoint4)));
            $next_appoint6 = date('Y-m-d', strtotime("+7 day", strtotime($next_appoint5)));

            // 1 = $final_ass_appoint_date > and $next_appoint1 <=
            // 2 = $next_appoint1 > and $next_appoint2 <=
            // 3 = $next_appoint2 > and $next_appoint3 <=

            $get_bb1 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS get_bb1 FROM booking WHERE patientid='$patientid' AND today_date = '".$final_ass_appoint_date."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) > '".$final_ass_appoint_date."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$next_appoint1."'"));

            $get_bb2 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS get_bb2 FROM booking WHERE patientid='$patientid' AND today_date = '".$final_ass_appoint_date."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) > '".$next_appoint1."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$next_appoint2."'"));

            $get_bb3 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS get_bb3 FROM booking WHERE patientid='$patientid' AND today_date = '".$final_ass_appoint_date."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) > '".$next_appoint2."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$next_appoint3."'"));

            $get_bb4 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS get_bb4 FROM booking WHERE patientid='$patientid' AND today_date = '".$final_ass_appoint_date."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) > '".$next_appoint3."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$next_appoint4."'"));

            $get_bb5 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS get_bb5 FROM booking WHERE patientid='$patientid' AND today_date = '".$final_ass_appoint_date."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) > '".$next_appoint4."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$next_appoint5."'"));

            $get_bb6 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS get_bb6 FROM booking WHERE patientid='$patientid' AND today_date = '".$final_ass_appoint_date."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) > '".$next_appoint5."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$next_appoint6."'"));

            if($get_bb1['get_bb1'] >= 1) {
                $total_app++;
                $total4_app++;
                $total5_app++;
                $total6_app++;
            }

            if($get_bb2['get_bb2'] >= 1) {
                $total_app++;
                $total4_app++;
                $total5_app++;
                $total6_app++;
            }

            if($get_bb3['get_bb3'] >= 1) {
                $total_app++;
                $total4_app++;
                $total5_app++;
                $total6_app++;
            }

            if($get_bb4['get_bb4'] >= 1) {
                $total4_app++;
                $total5_app++;
                $total6_app++;
            }

            if($get_bb5['get_bb5'] >= 1) {
                $total5_app++;
                $total6_app++;
            }

            if($get_bb6['get_bb6'] >= 1) {
                $total6_app++;
            }

            $selected_in_bb3 = 0;
            if($get_bb1['get_bb1'] >= 1 && $get_bb2['get_bb2'] >= 1 && $get_bb3['get_bb3'] >= 1) {
                $total_bb++;
                $bb_patient .= $patientid.' : '.get_contact($dbc, $patientid).'<br>';
                $ass_patient .= $patientid.' : '.get_contact($dbc, $patientid).' : '.$total_app.'<br>';
                $ass_patient .= $all_app.'<br>================================<br>';
                //$total_bb = $get_bb['total_bb'];
                $selected_in_bb3 = 1;
            } else {
                $ass_patient .= $patientid.' : '.get_contact($dbc, $patientid).' : '.$total_app.'<br>';
                $ass_patient .= $all_app.'<br>================================<br>';
            }

            $selected_in_bb4 = 0;
            if(($total4_app >= 3) && ($selected_in_bb3 == 0)) {
                $bb4_patient .= $patientid.' : '.get_contact($dbc, $patientid).'<br>';
                $selected_in_bb4 = 1;
                $total_bb4++;
            }
            $selected_in_bb5 = 0;
            if(($total5_app >= 3) && ($selected_in_bb3 == 0) && ($selected_in_bb4 == 0)) {
                $bb5_patient .= $patientid.' : '.get_contact($dbc, $patientid).'<br>';
                $selected_in_bb5 = 1;
                $total_bb5++;
            }
            if(($total6_app >= 3) && ($selected_in_bb3 == 0) && ($selected_in_bb4 == 0) && ($selected_in_bb5 == 0)) {
                $bb6_patient .= $patientid.' : '.get_contact($dbc, $patientid).'<br>';
                $total_bb6++;
            }

            $total_app = 0;
            $total4_app = 0;
            $total5_app = 0;
            $total6_app = 0;
        }
        $block_booking = ($total_injury['total_assessment'] != 0 ? ($total_bb / $total_injury['total_assessment']) * 100 : 0);

        $report_data .= '<tr nobr="true">
        <td>'.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</td>
        <td>'.$ass_patient.'</td>
        <td><b>Assessment + 3 appt in next 3 weeks : '.$total_bb.' </b><br>'.$bb_patient.'<br>================================<br>
		<b>Assessment + 3 appt in the next 4 weeks : '.$total_bb4.'</b><br>'.$bb4_patient.'<br>================================<br>
		<b>Assessment + 3 appt in the next 5 weeks : '.$total_bb5.'</b><br>'.$bb5_patient.'<br>================================<br>
		<b>Assessment + 3 appt in the next 6 weeks : '.$total_bb6.'</b><br>'.$bb6_patient.'</td>
        </tr>';
    }

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