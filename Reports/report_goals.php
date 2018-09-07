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
            $footer_text = 'Goals From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);
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

    $html .= report_goals($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '', 'report_data');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/goals_on_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_goals', 0, WEBSITE_URL.'/Reports/Download/goals_on_'.$today_date.'.pdf', 'Goals Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/goals_on_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    } ?>

<script type="text/javascript">

</script>
</head>
<body>
<?php include_once ('../navigation.php');
?>

<div class="container triple-pad-bottom">
    <div class="row">
        <div class="col-md-12">

        <?php echo reports_tiles($dbc);  ?>

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
                $endtime = date('Y-m-t');
            }
            ?>
            <center><div class="form-group">
                From: <input name="starttime" type="text" class="datepicker" value="<?php echo $starttime; ?>">
                &nbsp;&nbsp;&nbsp;
                Until: <input name="endtime" type="text" class="datepicker" value="<?php echo $endtime; ?>">
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                //echo '<a href="report_goals.php?goals=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a><br><br>';

                echo report_goals($dbc, $starttime, $endtime, '', '', '', 'page_data');
            ?>

        </form>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>

<?php
function report_goals($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $fetch) {
    $report_data = '';
    $page_data = '';

    $page_data .= '<div class="panel-group" id="accordion">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse_info" >
                        Total Appointments<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_info" class="panel-collapse collapse">
                <div class="panel-body">';

                $report_data .= '<h4>Total Appointments</h4>';

                $page_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">
                <tr style="'.$table_row_style.'"><th>Professional</th>
                <th>Total Appointments</th>
                </tr>';

                $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">
                <tr style="'.$table_row_style.'"><th>Professional</th>
                <th>Total Appointments</th>
                </tr>';

				$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status=1"),MYSQLI_ASSOC));
                $all_booking = 0;
                foreach($result as $rowid) {
					$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `contactid`='$rowid'"));
                    $therapistsid = $row['contactid'];

                    $total_booking = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_booking FROM booking WHERE therapistsid = '$therapistsid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));

                    $page_data .= '<tr>
                    <td>'.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</td>
                    <td>'.$total_booking['total_booking'].'</td>
                    </tr>';

                    $report_data .= '<tr>
                    <td>'.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</td>
                    <td>'.$total_booking['total_booking'].'</td>
                    </tr>';

                    $all_booking += $total_booking['total_booking'];
                }

                $page_data .= '<tr style="'.$grand_total_style.'"><td>Grand Total</td><td>'.$all_booking.'</td></tr>
                </table>';
                $report_data .= '<tr style="'.$grand_total_style.'"><td>Grand Total</td><td>'.$all_booking.'</td></tr>
                </table>';

    $page_data .= '</div></div></div>';

    $page_data .= '
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse_rate" >
                        Arrival Rate for Total Appointments (%)<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_rate" class="panel-collapse collapse">
                <div class="panel-body">';

                $report_data .= '<br><h4>Arrival Rate for Total Appointments (%)</h4>';

                $page_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">
                <tr style="'.$table_row_style.'"><th>Professional</th>
                <th>Total Appointments</th>
                <th>Completed Appointments</th>
                <th>Arrival Rate</th>
                </tr>';

                $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">
                <tr style="'.$table_row_style.'"><th>Professional</th>
                <th>Total Appointments</th>
                <th>Completed Appointments</th>
                <th>Arrival rate</th>
                </tr>';

				$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status=1"),MYSQLI_ASSOC));
                $all_booking = 0;
                $total_therapist = 0;
                foreach($result as $rowid) {
					$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `contactid`='$rowid'"));
                    $therapistsid = $row['contactid'];

                    $total_booking = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_booking FROM booking WHERE therapistsid = '$therapistsid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));

                    $total_completed_booking = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_completed_booking FROM booking WHERE therapistsid = '$therapistsid' AND (follow_up_call_status = 'Arrived' OR follow_up_call_status = 'Paid' OR follow_up_call_status = 'Invoiced') AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));

                    $arrival_rate = (($total_completed_booking['total_completed_booking'] / $total_booking['total_booking']) * 100);
                    $page_data .= '<tr>
                    <td>'.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</td>
                    <td>'.$total_booking['total_booking'].'</td>
                    <td>'.$total_completed_booking['total_completed_booking'].'</td>
                    <td>'.number_format($arrival_rate, 2).'%</td>
                    </tr>';

                    $report_data .= '<tr>
                    <td>'.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</td>
                    <td>'.$total_booking['total_booking'].'</td>
                    <td>'.$total_completed_booking['total_completed_booking'].'</td>
                    <td>'.number_format($arrival_rate, 2).'%</td>
                    </tr>';

                    $all_booking += $arrival_rate;
                    $total_therapist++;
                }
                $page_data .= '<tr style="'.$grand_total_style.'"><td colspan="3">Grand Total</td><td>'.number_format((float)($all_booking/$total_therapist), 2, '.', '').'%</td></tr>
                </table>';

                $report_data .= '<tr style="'.$grand_total_style.'"><td colspan="3">Grand Total</td><td>'.number_format((float)($all_booking/$total_therapist), 2, '.', '').'%</td></tr>
                </table>';

    $page_data .= '</div></div></div>';

    $page_data .= '
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse_3" >
                        Average Visits to Discharge<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_3" class="panel-collapse collapse">
                <div class="panel-body">';

                $report_data .= '<br><h4>Average Visits to Discharge</h4>';

                $page_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">
                <tr style="'.$table_row_style.'"><th>Professional</th>
                <th>Completed Appointments</th>
                <th>Discharged Patients</th>
                <th>Avg Visits</th>
                </tr>';

                $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">
                <tr style="'.$table_row_style.'"><th>Professional</th>
                <th>Completed Appointments</th>
                <th>Discharged Patients</th>
                <th>Avg Visits</th>
                </tr>';

				$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status=1"),MYSQLI_ASSOC));
                $all_booking = 0;
                foreach($result as $rowid) {
					$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `contactid`='$rowid'"));
                    $therapistsid = $row['contactid'];

                    $total_completed_booking = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_completed_booking FROM booking WHERE therapistsid = '$therapistsid' AND (follow_up_call_status = 'Arrived' OR follow_up_call_status = 'Invoiced' OR follow_up_call_status = 'Paid') AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));

                    $total_discharge_patient = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(injuryid) AS total_discharge_patient FROM patient_injury WHERE injury_therapistsid = '$therapistsid' AND (DATE(discharge_date) >= '".$starttime."' AND DATE(discharge_date) <= '".$endtime."')"));

                    $avg_visit_discharge = (($total_completed_booking['total_completed_booking'] / $total_discharge_patient['total_discharge_patient']));

                    $page_data .= '<tr>
                    <td>'.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</td>
                    <td>'.$total_completed_booking['total_completed_booking'].'</td>
                    <td>'.$total_discharge_patient['total_discharge_patient'].'</td>
                    <td>'.$avg_visit_discharge.'</td>
                    </tr>';

                    $report_data .= '<tr>
                    <td>'.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</td>
                    <td>'.$total_completed_booking['total_completed_booking'].'</td>
                    <td>'.$total_discharge_patient['total_discharge_patient'].'</td>
                    <td>'.$avg_visit_discharge.'</td>
                    </tr>';

                    $all_booking += $avg_visit_discharge;
                }
                $page_data .= '<tr style="'.$grand_total_style.'"><td colspan="3">Grand Total</td><td>'.$all_booking.'</td></tr>
                </table>';

                $report_data .= '<tr style="'.$grand_total_style.'"><td colspan="3">Grand Total</td><td>'.$all_booking.'</td></tr>
                </table>';

    $page_data .= '</div></div></div>';

    $page_data .= '
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse_4" >
                        % Of Available Hours Booked<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_4" class="panel-collapse collapse">
                <div class="panel-body">';

                $report_data .= '<br><h4>% Of Available Hours Booked</h4>';

                $page_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">
                <tr style="'.$table_row_style.'"><th>Professional</th>
                <th>Total Work Hours</th>
                <th>Total Hours Booked</th>
                <th>Hours Booked (%)</th>
                </tr>';

                $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">
                <tr style="'.$table_row_style.'"><th>Professional</th>
                <th>Total Work Hours</th>
                <th>Total Hours Booked</th>
                <th>Hours Booked (%)</th>
                </tr>';

				$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name, scheduled_hours, schedule_days FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status=1"),MYSQLI_ASSOC));
                $all_booking = 0;
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
                foreach($result as $rowid) {
					$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, scheduled_hours, schedule_days FROM `contacts` WHERE `contactid`='$rowid'"));
                    $therapistsid = $row['contactid'];
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

                                //$total_h = $each_double[1]-$each_double[0];
                                //total_work_hours += $total_h;
                            } else {
                                $each_double = explode(',', $each_hours);
                                foreach($each_double as $key) {
                                    $each_key = explode('-', $key);

                                    $t1 = StrToTime ($each_key[1]);
                                    $t2 = StrToTime ($each_key[0]);
                                    $diff = $t1 - $t2;
                                    $total_work_hours += $diff / ( 60 * 60 );

                                    //$total_h = $each_key[1]-$each_key[0];
                                    //$total_work_hours += $total_h;
                                }
                            }
                        }
                    }

                    $appoints = mysqli_query($dbc, "SELECT appoint_date, end_appoint_date FROM booking WHERE therapistsid = '$therapistsid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')");

                    while($row_appoints = mysqli_fetch_array( $appoints )) {
                        $appoint_date = strtotime($row_appoints['appoint_date']);
                        $end_appoint_date = strtotime($row_appoints['end_appoint_date']);
                        $differenceInSeconds = $end_appoint_date - $appoint_date;
                        $total_book_hours += ($differenceInSeconds / 3600);
                    }

                    $avail_booked = number_format((float)(($total_book_hours/$total_work_hours)*100), 2, '.', '');

                    $page_data .= '<tr>
                    <td>'.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</td>
                    <td>'.$total_work_hours.'</td>
                    <td>'.$total_book_hours.'</td>
                    <td>'.$avail_booked.'</td>
                    </tr>';

                    $report_data .= '<tr>
                    <td>'.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</td>
                    <td>'.$total_work_hours.'</td>
                    <td>'.$total_book_hours.'</td>
                    <td>'.$avail_booked.'</td>
                    </tr>';

                    $all_booking += $avail_booked;
                    $total_therapist++;
                }
                $page_data .= '<tr style="'.$grand_total_style.'"><td colspan="3">Grand Total</td><td>'.number_format((float)($all_booking/$total_therapist), 2, '.', '').'%</td></tr>
                </table>';

                $report_data .= '<tr style="'.$grand_total_style.'"><td colspan="3">Grand Total</td><td>'.number_format((float)($all_booking/$total_therapist), 2, '.', '').'%</td></tr>
                </table>';

    $page_data .= '</div></div></div>';

    $page_data .= '
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse_5" >
                        Block Booking (%)<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_5" class="panel-collapse collapse">
                <div class="panel-body">';

                $report_data .= '<br><h4>Block Booking (%)</h4>';

                $page_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">
                <tr style="'.$table_row_style.'"><th>Professional</th>
                <th>Total Appointments</th>
                <th>Total Block Booking Appointments</th>
                <th>Rate</th>
                </tr>';

                $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">
                <tr style="'.$table_row_style.'"><th>Professional</th>
                <th>Total Appointments</th>
                <th>Total Block Booking Appointments</th>
                <th>Rate</th>
                </tr>';

				$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status=1"),MYSQLI_ASSOC));
                $all_booking = 0;
                $total_therapist = 0;
                foreach($result as $rowid) {
					$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `contactid`='$rowid'"));
                    $therapistsid = $row['contactid'];

                    $total_booking = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_booking FROM booking WHERE therapistsid = '$therapistsid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));

                    $total_bb_booking = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_bb_booking FROM booking WHERE therapistsid = '$therapistsid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND block_booking = 1 AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));

                    $bb_rate = (($total_bb_booking['total_bb_booking'] / $total_booking['total_booking']) * 100);

                    $page_data .= '<tr>
                    <td>'.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</td>
                    <td>'.$total_booking['total_booking'].'</td>
                    <td>'.$total_bb_booking['total_bb_booking'].'</td>
                    <td>'.number_format((float)($bb_rate), 2, '.', '').'%</td>
                    </tr>';

                    $report_data .= '<tr>
                    <td>'.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</td>
                    <td>'.$total_booking['total_booking'].'</td>
                    <td>'.$total_bb_booking['total_bb_booking'].'</td>
                    <td>'.number_format((float)($bb_rate), 2, '.', '').'%</td>
                    </tr>';

                    $all_booking += $bb_rate;
                    $total_therapist++;
                }
                $page_data .= '<tr style="'.$grand_total_style.'"><td colspan="3">Grand Total</td><td>'.number_format((float)($all_booking/$total_therapist), 2, '.', '').'%</td></tr>
                </table>';

                $report_data .= '<tr style="'.$grand_total_style.'"><td colspan="3">Grand Total</td><td>'.number_format((float)($all_booking/$total_therapist), 2, '.', '').'%</td></tr>
                </table>';

    $page_data .= '</div></div></div>';

    $page_data .= '</div>';

    return $$fetch;
}

?>