<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['printpdf'])) {
    $patientpdf = $_POST['patientpdf'];

    DEFINE('PATIENT', get_contact($dbc, $patientpdf));
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
            //$this->Image($image_file, 0, 10, 60, '', 'PNG', '', 'T', false, 300, 'C', false, false, 0, false, false, false);
            $footer_text = 'Future Appointment for Patient : <b>'.PATIENT.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : The report displays all future bookings for the selected customer, the staff working with them, their injury and the appointment status.";
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

    $html .= report_bb($dbc, $patientpdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/patient_'.$patientpdf.'.pdf', 'F');
    track_download($dbc, 'report_patient_block_booking', 0, WEBSITE_URL.'/Reports/Download/patient_'.$today_date.'.pdf', 'Future Appointment for Patient Report');

    $from = $_POST['from'];
    if($from == 'calendar') {
        echo '<script type="text/javascript"> window.open("Download/patient_'.$patientpdf.'.pdf", "fullscreen=yes"); window.top.close(); window.opener.location.reload(); </script>';
    } else {
	    echo '<script type="text/javascript" language="Javascript">
	    window.open("Download/patient_'.$patientpdf.'.pdf", "fullscreen=yes");
	    </script>';
    }
    $patient = $patientpdf;
} ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            The report displays all future bookings for the selected customer, the staff working with them, their injury and the appointment status.</div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if (isset($_POST['search_email_submit'])) {
                $patient = $_POST['patient'];
            } else if(!empty($_GET['patientid'])) {
                $patient = $_GET['patientid'];
                echo '<input type="hidden" name="from" value="calendar">';
            } else {
                $patient = '';
            }
            ?>

            <div class="form-group col-sm-5">
                <label for="site_name" class="col-sm-4 control-label">Customer:</label>
                <div class="col-sm-8">
                    <select data-placeholder="Select a Customer..." name="patient" class="chosen-select-deselect form-control">
                        <option value=""></option>
                        <?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE contactid IN (SELECT patientid FROM booking WHERE str_to_date(substr(appoint_date,1,10),'%Y-%m-%d') >= DATE(NOW())) AND deleted=0 AND status=1"),MYSQLI_ASSOC));
						foreach($query as $rowid) {
							echo "<option ".($patient == $rowid ? 'selected' : '')." value='$rowid'>".get_contact($dbc, $rowid)."</option>";
						} ?>
                    </select>
                </div>
			</div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>

            <input type="hidden" name="patientpdf" value="<?php echo $patient; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
            if($patient != '') {
                echo report_bb($dbc, $patient, '', '', '');
            }
            ?>



        </form>

<?php
function report_bb($dbc, $patient, $table_style, $table_row_style, $grand_total_style) {

    $report_data .= '<h3>' . get_contact($dbc, $patient) . '</h3>';

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th>Appointment Date, Time &amp; Day</th>
    <th>Staff</th>
    <th>Injury</th>
    <th>Status</th>
    </tr>';

    $query_check_credentials = "SELECT appoint_date, end_appoint_date, bookingid, injuryid, follow_up_call_status, therapistsid FROM booking WHERE deleted=0 AND patientid = '$patient' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= DATE(NOW()) ORDER BY appoint_date";

    $result = mysqli_query($dbc, $query_check_credentials);
    while($row = mysqli_fetch_array( $result ))
    {
        $appoint_date = explode(' ', $row['appoint_date']);
        $report_data .= '<tr nobr="true">';
        $report_data .= '<td>'.$row['appoint_date'].' : '.date("l", strtotime($appoint_date[0])).'</td>';
        $report_data .= '<td>'.get_contact($dbc, $row['therapistsid']).'</td>';
        $report_data .=  '<td>' . get_all_from_injury($dbc, $row['injuryid'], 'injury_name').' : '.get_all_from_injury($dbc, $row['injuryid'], 'injury_type') . '</td>';
        $report_data .=  '<td>' . $row['follow_up_call_status'] . '</td>';
        $report_data .= '</tr>';
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