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
            $footer_text = 'Drop Off Analysis From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : This report is used to track if a customer has shown up for the number of appointments scheduled in their treatment plan, and displays the customer injury, how many treatments of the plan have been completed, their start date and their latest booking. This report is printed out for staff to check off if they have discharged the customer or called the customer to follow up, and for any comments to be recorded. If a customer has an Active injury file in the software and the last booking with the clinic is older than 7 days, then the appointment will display here.";
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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 65, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	//$pdf->AddPage('L', 'LETTER');
    //$pdf->SetFont('helvetica', '', 8);

    $pdf_gen = 'Yes';
    $html .= report_dropoff($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '', $therapistpdf,$pdf_gen,$pdf);

    $today_date = date('Y-m-d');
	//$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/drop_off_analysis_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_drop_off_analysis', 0, WEBSITE_URL.'/Reports/Download/drop_off_analysis_'.$today_date.'.pdf', 'Drop Off Analysis Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/drop_off_analysis_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $therapist = $therapistpdf;
    } ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            This report is used to track if a customer has shown up for the number of appointments scheduled in their treatment plan, and displays the customer injury, how many treatments of the plan have been completed, their start date and their latest booking. This report is printed out for staff to check off if they have discharged the customer or called the customer to follow up, and for any comments to be recorded. If a customer has an Active injury file in the software and the last booking with the clinic is older than 7 days, then the appointment will display here.</div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

            <?php
            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
                $therapist = $_POST['therapist'];
            }
            /*
            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-d', strtotime('-7 days'));
            }
            if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            }
            */
            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-01');
            }

            if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            }
            ?>
            <center><div class="form-group">
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Injury Created Date From:</label>
					<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Injury Created Date Until:</label>
					<div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
				</div>
				<div class="form-group col-sm-5">
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
                echo report_dropoff($dbc, $starttime, $endtime, '', '', '', $therapist,'');
            ?>

        </form>

<?php
function report_dropoff($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $therapist,$pdf_gen,$pdf) {
    $report_data = '';

    if($therapist == '') {
		$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status=1"),MYSQLI_ASSOC));
    } else {
		$result = [ $therapist ];
    }

    foreach($result as $therapistid) {
		$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$therapistid'"));

		$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, ""),MYSQLI_ASSOC));
        $report_validation = mysqli_query($dbc,"SELECT pi.injuryid, pi.injury_name, pi.injury_type, pi.contactid, pi.drop_off_analysis_dc, pi.drop_off_analysis_cc, pi.drop_off_analysis_status FROM patient_injury pi, contacts c WHERE pi.injury_therapistsid = '$therapistid' AND ((str_to_date(substr(pi.today_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(pi.today_date,1,10),'%Y-%m-%d')) <= '".$endtime."') AND pi.contactid = c.contactid AND pi.discharge_date IS NULL ORDER BY c.first_letter");

        $data = 0;
        $html_table = '';

        while($row_report = mysqli_fetch_array($report_validation)) {
            $injuryid = $row_report['injuryid'];
            $contactid = $row_report['contactid'];

            $get_visit =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_booking FROM	booking WHERE patientid = '$contactid' AND (follow_up_call_status = 'Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status = 'Paid' OR follow_up_call_status = 'Invoiced')"));

            //if($get_visit['total_booking'] != 0) {

                $get_arrived_first =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT MIN((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d'))) AS first_arrived FROM booking WHERE patientid = '$contactid' AND (follow_up_call_status = 'Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status = 'Paid' OR follow_up_call_status = 'Invoiced')"));

                $get_arrived =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT MAX((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d'))) AS last_arrived FROM booking WHERE patientid = '$contactid'"));

                if(strtotime($get_arrived['last_arrived']) < strtotime(date('Y-m-d', strtotime('-7 days')))) {
                    $data = 1;

                    $html_table .= '<tr nobr="true">';

		            $html_table .= '<td><a href="../Contacts/add_contacts.php?category=Patient&contactid='.$row_report['contactid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">'.get_contact($dbc, $row_report['contactid']). '</a></td>';

                    $html_table .= '<td>' . get_contact_phone($dbc, $row_report['contactid']) . '</td>';
                    $html_table .= '<td>' . $row_report['injury_name'].' : '.$row_report['injury_type'] . '</td>';
                    //$html_table .= '<td>' . $get_visit['total_booking'] . '</td>';

                    $total_injury = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_injury FROM booking WHERE injuryid='$injuryid'"));

                    $treatment_plan = get_all_from_injury($dbc, $injuryid, 'treatment_plan');
                    $final_treatment_done = '';
                    //if($treatment_plan != '') {
                        $final_treatment_done = ($total_injury['total_injury']).'/'.$treatment_plan;
                    //}

                    $html_table .= '<td>' . $final_treatment_done . '</td>';

                    $html_table .= '<td>' . $get_arrived_first['first_arrived'] . '</td>';
                    $html_table .= '<td>' . $get_arrived['last_arrived'] . '</td>';

                    if($row_report['drop_off_analysis_dc'] == 1) {
                        $html_table .= '<td><img src="'.WEBSITE_URL.'/img/checkmark.png" height="20" title="PDF"></td>';
                    } else {
                        $html_table .= '<td><img src="'.WEBSITE_URL.'/img/checkbox.png" height="12" title="PDF"></td>';
                    }

                    $checked1 = '';
                    if($row_report['drop_off_analysis_cc'] == 1) {
                        $html_table .= '<td><img src="'.WEBSITE_URL.'/img/checkmark.png" height="20" title="PDF"></td>';
                    } else {
                        $html_table .= '<td><img src="'.WEBSITE_URL.'/img/checkbox.png" height="12" title="PDF"></td>';
                    }
                    $html_table .= '<td>'.$row_report['drop_off_analysis_status'];
                    $html_table .= '</td>';


                    // $html_table .= '<td><img src="'.WEBSITE_URL.'/img/checkbox.png" height="12" title="PDF"></td>';
                    //$html_table .= '<td><img src="'.WEBSITE_URL.'/img/checkbox.png" height="12" title="PDF"></td>';
                    //$html_table .= '<td><br><br>______________________</td>';
                    $html_table .= '</tr>';
                }
            //}
        }

        if($data == 1) {
            if($pdf_gen != '') {
                $report_data .= $pdf->AddPage('L', 'LETTER');
                $report_data .= $pdf->SetFont('helvetica', '', 8);
                $report_data = '';
            }

            $report_data .= '<h4>'.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</h4><br>';
            $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
            $report_data .= '<tr style="'.$table_row_style.'" nobr="true">
            <th style="width:8%">Client</th>
            <th style="width:12%">Telephone</th>
            <th style="width:20%">Injury</th>
            <th style="width:10%">Treatment Plan</th>
            <th style="width:8%">First Date Arrived</th>
            <th style="width:8%">Latest Booking</th>
            <th style="width:7%">Discharge Client</th>
            <th style="width:5%">Call Client</th>
            <th style="width:18%">Comment</th>';
            $report_data .= "</tr>";

            $report_data .= $html_table;

            $report_data .= '</table>';
            if($pdf_gen != '') {
                $report_data .= $pdf->writeHTML(utf8_encode($report_data), true, false, true, false, '');
            }
        }
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