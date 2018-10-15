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
            $footer_text = 'Gross Revenue by Staff From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "C", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : How many Total Assessments and Treatments each Staff has. It will display Total Company Billables and Admin Fees collected by Staff in the selected time frame.";
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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 60, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);

    $html .= '<br><br>' . report_goals($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '', $therapistpdf, 'print');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/assessment_tally_board_on_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_gross_revenue_by_staff', 0, WEBSITE_URL.'/Reports/Download/assessment_tally_board_on_'.$today_date.'.pdf', 'Gross Revenue by Staff Report');
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
            How many Total Assessments and Treatments each Staff has. It will display Total Company Billables and Admin Fees collected by Staff in the selected time frame.</div>
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
						<select data-placeholder="Select a Staff..." name="therapist" class="chosen-select-deselect form-control1" width="380">
							<option value="">Select All</option>
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

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                echo report_goals($dbc, $starttime, $endtime, '', '', '', $therapist, 'screen');
            ?>

        </form>
<?php
function report_goals($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $therapist, $output) {

    //print PDF

    $report_data = '<table border="1px" class="table table-bordered" style="'.$table_style.'">
    <tr style="'.$table_row_style.'" nobr="true">
    <th width="20%">Staff Name</th>
    <th width="10%">Assessments Count</th>
    <th width="10%">Treatments Count</th>
    <th width="40%">Total Company Billables<br>(Total Revenue - Compensation)</th>
    <th width="20%">Total Admin Fee Collected</th>
    </tr>';

    if($therapist == '') {
		$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name, scheduled_hours FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND contactid != '6896' AND contactid != '6909' AND (category_contact = 'Physical Therapist' OR category_contact = 'Massage Therapist' OR category_contact = 'Osteopathic Therapist') AND deleted=0 AND status=1"),MYSQLI_ASSOC));
    } else if($therapist == 'Physical Therapist') {
		$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name, scheduled_hours FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND contactid != '6896' AND contactid != '6909' AND category_contact = 'Physical Therapist' AND deleted=0 AND status=1"),MYSQLI_ASSOC));
    } else if($therapist == 'Massage Therapist') {
		$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name, scheduled_hours FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND contactid != '6896' AND contactid != '6909' AND category_contact = 'Massage Therapist' AND deleted=0 AND status=1"),MYSQLI_ASSOC));
    } else if($therapist == 'Osteopathic Therapist') {
		$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name, scheduled_hours FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND contactid != '6896' AND contactid != '6909' AND category_contact = 'Osteopathic Therapist' AND deleted=0 AND status=1"),MYSQLI_ASSOC));
    } else {
		$result = [ $therapist ];
    }

    $c7 = 0;

    $total_assess = 0;
    $total_treat = 0;
    $total_billable = 0;
    $total_af = 0;
    $odd_even=0;
    foreach($result as $therapistsid) {
        $bg_class = $odd_even % 2 == 0 ? '' : 'background-color:#e6e6e6;';
		$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT contactid, first_name, last_name, scheduled_hours FROM contacts WHERE contactid='$therapistsid'"));

        //Assessment Count
        $total_ass = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_assessment FROM booking WHERE therapistsid = '$therapistsid' AND type IN('A','C','F','H','N','U','S','T') AND (follow_up_call_status = 'Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status = 'Paid' OR follow_up_call_status = 'Invoiced') AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));
        //Assessment Count

        //Treatments Count
        $total_injury = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_treatment FROM booking WHERE therapistsid = '$therapistsid' AND type IN('B','D','G','J','K','L', 'M', 'O') AND (follow_up_call_status = 'Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status = 'Paid' OR follow_up_call_status = 'Invoiced') AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$starttime."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$endtime."')"));
        //Treatments Count

        //Total Company Billables
        $revenue = count_revenue($dbc,$starttime, $endtime, $therapistsid);
        $comp = count_comp($dbc,$starttime, $endtime, $therapistsid);
        $billable = ($revenue-$comp);
        //Total Company Billables

        //Admin Fee
        $admn_fee = count_adminfee($dbc,$starttime, $endtime, $therapistsid);
        //Admin Fee

        $report_data .= '<tr nobr="true" style="'.$bg_class.'">
        <td>'.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</td>
        <td>'.$total_ass['total_assessment'].'</td>
        <td>'.$total_injury['total_treatment'].'</td>
        <td>$'.number_format($revenue,2).' - $'.number_format($comp,2).' = $'.number_format($billable,2).'</td>
        <td>$'.number_format($admn_fee,2).'</td>';
        $report_data .= '</tr>';

        $total_assess += $total_ass['total_assessment'];
        $total_treat += $total_injury['total_treatment'];
        $total_billable += $billable;
        $total_af += $admn_fee;
        $odd_even++;
    }

    $report_data .= '<tr nobr="true"><td><b>Average/Total</b></td><td><b>'.$total_assess.'</b></td><td><b>'.$total_treat.'</b></td><td><b>$'.number_format($total_billable,2).'</b></td><td><b>$'.number_format($total_af,2).'</b></td>';

    $report_data .= '</tr>';

    $report_data .= '</table>';

    return $report_data;
}

function count_revenue($dbc,$starttime, $endtime, $therapistsid) {

    /*
    $starttime1 = $starttime.' 06:00:00';
    $endtime1 = $endtime.' 20:00:00';
    $start_time_int = strtotime($starttime1);
    $end_time_int = strtotime($endtime1);

    $report_validation = mysqli_query($dbc,"SELECT i.serviceid FROM invoice i, booking b, mrbs_entry m WHERE i.therapistsid='$therapistsid' AND b.bookingid = i.bookingid AND m.id = b.calid AND (m.start_time >= '$start_time_int' AND m.end_time <= '$end_time_int')");

    $all_service = '';
    while($row_tab = mysqli_fetch_array( $report_validation )) {
        $all_service .= $row_tab['serviceid'].',';
    }

    $all_service = str_replace(",,,",",",$all_service);
    $all_service = str_replace(",,",",",$all_service);

    $serviceid = explode(',', $all_service);
    $serviceid = array_filter($serviceid);

    // Services
    if($all_service != '') {
        asort($serviceid);
        $occurences = array_count_values($serviceid);

        $total_base_service = 0;

        foreach ($occurences as $key => $total_appt) {
            $final_serviceid = rtrim($key,',');
            $fee = get_all_from_service($dbc, $final_serviceid, 'fee');
            $admin_price = get_all_from_service($dbc, $final_serviceid, 'admin_price');
            $final_fee = ($fee-$admin_price);
            $total_base_service += ($final_fee*$total_appt);
        }
    }

    return $total_base_service;*/


    $report_validation = mysqli_query($dbc,"SELECT *, count(*) AS count FROM invoice_compensation WHERE contactid='$therapistsid' AND (service_date >= '$starttime' AND service_date <= '$endtime') AND `item_type`='services' GROUP BY item_id, fee, admin_fee");

    $total_base_service = 0;

    while($row_tab = mysqli_fetch_array($report_validation)) {
        $fee = $row_tab['fee'];
        $admin_price = $row_tab['admin_fee'];
        $final_fee = ($fee-$admin_price);
        $total_base_service += ($final_fee*$row_tab['count']);
    }

    return $total_base_service;
}

function count_adminfee($dbc,$starttime, $endtime, $therapistsid) {

    /*
    $starttime1 = $starttime.' 06:00:00';
    $endtime1 = $endtime.' 20:00:00';
    $start_time_int = strtotime($starttime1);
    $end_time_int = strtotime($endtime1);

    $report_validation = mysqli_query($dbc,"SELECT i.serviceid FROM invoice i, booking b, mrbs_entry m WHERE i.therapistsid='$therapistsid' AND b.bookingid = i.bookingid AND m.id = b.calid AND (m.start_time >= '$start_time_int' AND m.end_time <= '$end_time_int')");

    $all_service = '';
    while($row_tab = mysqli_fetch_array( $report_validation )) {
        $all_service .= $row_tab['serviceid'].',';
    }

    $all_service = str_replace(",,,",",",$all_service);
    $all_service = str_replace(",,",",",$all_service);

    $serviceid = explode(',', $all_service);
    $serviceid = array_filter($serviceid);

    // Services
    if($all_service != '') {
        asort($serviceid);
        $occurences = array_count_values($serviceid);

        $final_af = 0;

        foreach ($occurences as $key => $total_appt) {
            $final_serviceid = rtrim($key,',');
            $admin_price = get_all_from_service($dbc, $final_serviceid, 'admin_price');
            $final_af += ($admin_price*$total_appt);
        }
    }

    return $final_af;

    */

    $report_validation = mysqli_query($dbc,"SELECT *, count(*) AS count FROM invoice_compensation WHERE contactid='$therapistsid' AND (service_date >= '$starttime' AND service_date <= '$endtime') AND `item_type`='services' GROUP BY item_id, fee, admin_fee");

    $final_af = 0;

    while($row_tab = mysqli_fetch_array($report_validation)) {
        $admin_price = $row_tab['admin_fee'];
        $final_af += ($admin_price*$row_tab['count']);
    }

    return $final_af;
}

function count_comp($dbc,$starttime, $endtime, $therapistsid) {

    /*
    $starttime1 = $starttime.' 06:00:00';
    $endtime1 = $endtime.' 20:00:00';
    $start_time_int = strtotime($starttime1);
    $end_time_int = strtotime($endtime1);

    $report_validation = mysqli_query($dbc,"SELECT i.serviceid FROM invoice i, booking b, mrbs_entry m WHERE i.therapistsid='$therapistsid' AND b.bookingid = i.bookingid AND m.id = b.calid AND (m.start_time >= '$start_time_int' AND m.end_time <= '$end_time_int')");

    $all_service = '';
    while($row_tab = mysqli_fetch_array( $report_validation )) {
        $all_service .= $row_tab['serviceid'].',';
    }

    $all_service = str_replace(",,,",",",$all_service);
    $all_service = str_replace(",,",",",$all_service);

    $serviceid = explode(',', $all_service);
    $serviceid = array_filter($serviceid);

    $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT base_pay FROM compensation WHERE contactid='$therapistsid'"));
    $base_pay = explode('*#*',$get_contact['base_pay']);

    // Services
    if($all_service != '') {
        asort($serviceid);
        $occurences = array_count_values($serviceid);
        $total_base_service = 0;

        foreach ($occurences as $key => $total_appt) {
            $final_serviceid = rtrim($key,',');

            $fee = get_all_from_service($dbc, $final_serviceid, 'fee');
            $admin_price = get_all_from_service($dbc, $final_serviceid, 'admin_price');
            $final_fee = ($fee-$admin_price);
            $service_fee = ($final_fee*$total_appt);
            $base_pay_perc = $base_pay[0];
            $comp_pay = ($base_pay_perc*0.01*$service_fee);

            $total_base_service += $comp_pay;
        }
    }
    return $total_base_service;

    */

    $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT base_pay FROM compensation WHERE contactid='$therapistsid' AND '$starttime' BETWEEN start_date AND end_date"));
    $base_pay = explode('*#*',$get_contact['base_pay']);

    $report_validation = mysqli_query($dbc,"SELECT *, count(*) AS count FROM invoice_compensation WHERE contactid='$therapistsid' AND (service_date >= '$starttime' AND service_date <= '$endtime') AND `item_type`='services' GROUP BY item_id, fee, admin_fee");

    $total_base_service = 0;

    while($row_tab = mysqli_fetch_array($report_validation)) {
        $fee = $row_tab['fee'];
        $admin_price = $row_tab['admin_fee'];

        $final_fee = ($fee-$admin_price);
        $base_pay_perc = $base_pay[0];

        $total_base_service += (($final_fee*($base_pay_perc/100))*$row_tab['count']);
    }

    return $total_base_service;
}
?>