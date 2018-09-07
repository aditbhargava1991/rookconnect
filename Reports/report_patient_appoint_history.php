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
                $this->Image($image_file, 10, 10, 80, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);

            $this->SetFont('helvetica', '', 13);
            $footer_text = 'History for Customer : <b>'.PATIENT.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : Displays the appointment summary by customer.";
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

    $html .= report_history($dbc, $patientpdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/customer_'.$patientpdf.'.pdf', 'F');
    track_download($dbc, 'report_patient_appoint_history', 0, WEBSITE_URL.'/Reports/Download/customer_'.$today_date.'.pdf', 'History for Customer Report');

    $from = $_POST['from'];
    if($from == 'calendar') {
        echo '<script type="text/javascript"> window.open("Download/customer_'.$patientpdf.'.pdf", "fullscreen=yes"); window.top.close(); window.opener.location.reload(); </script>';
    } else {
	    echo '<script type="text/javascript" language="Javascript">
	    window.open("Download/customer_'.$patientpdf.'.pdf", "fullscreen=yes");
	    </script>';
    }
    $patient = $patientpdf;
} ?>


        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Displays the appointment summary by customer.</div>
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
                //$patient = '';
            }
            ?>
            <center><div class="form-group">
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Customer:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select a Customer..." name="patient" class="chosen-select-deselect form-control1" width="380">
							<option value=""></option>
							<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE contactid IN (SELECT patientid FROM booking WHERE str_to_date(substr(appoint_date,1,10),'%Y-%m-%d') <= DATE(NOW())) AND deleted=0 AND status=1"),MYSQLI_ASSOC));
							foreach($query as $rowid) {
								echo "<option ".($rowid == $patient ? 'selected' : '')." value='$rowid'>".get_contact($dbc, $rowid)."</option>";
							} ?>
						</select>
					</div>
                </div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="patientpdf" value="<?php echo $patient; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
            if($patient != '') {
                echo report_history($dbc, $patient, '', '', '');
            }
            ?>



        </form>

<?php
function report_history($dbc, $patient, $table_style, $table_row_style, $grand_total_style) {

    //$report_data .= '<h3>' . get_contact($dbc, $patient) . '</h3>';

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="15%">Appointment Date & Time</th>
    <th width="10%">Staff</th>
    <th width="23%">Injury</th>
    <th width="28%">Service(s)<br>Product(s)</th>
    <th width="8%">Invoice Total</th>
    <th width="14%">Status</th>
    </tr>';

    $query_check_credentials = "SELECT appoint_date, end_appoint_date, bookingid, injuryid, follow_up_call_status, therapistsid FROM booking WHERE deleted=0 AND patientid = '$patient' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= DATE(NOW()) ORDER BY appoint_date";

    $result = mysqli_query($dbc, $query_check_credentials);
    $odd_even = 0;
    while($row = mysqli_fetch_array( $result ))
    {
        $bg_class = $odd_even % 2 == 0 ? '' : 'background-color:#e6e6e6;';
        $bookingid = $row['bookingid'];
        $invoice = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT serviceid, inventoryid, final_price, invoiceid FROM invoice WHERE bookingid='$bookingid'"));

        $report_data .= '<tr nobr="true" style="'.$bg_class.'">';
        $report_data .= '<td>'.$row['appoint_date'].'<br>'.$row['end_appoint_date'].'</td>';
        $report_data .= '<td>'.get_contact($dbc, $row['therapistsid']).'</td>';
        //$report_data .= '<td>'.$row['bookingid'].'</td>';
        $report_data .=  '<td>' . get_all_from_injury($dbc, $row['injuryid'], 'injury_name').' : '.get_all_from_injury($dbc, $row['injuryid'], 'injury_type') . '</td>';

        $report_data .= '<td>';
        $parts = explode(',', $invoice['serviceid']);
        foreach ($parts as $key) {
            if($key != '') {
                $report_data .= get_all_from_service($dbc, $key, 'heading').'<br>';
            }
        }
        $report_data .= '<br>';

        $parts1 = explode(',', $row['inventoryid']);
        $invtype = explode(',', $row['invtype']);
        foreach ($parts1 as $key1) {
            if($key1 != '') {
                $report_data .= $invtype[$k].' : '.get_all_from_inventory($dbc, $key1 , 'name').'<br>';

            }
        }
        $report_data .= '</td>';
        $report_data .=  '<td>$' . $invoice['final_price'] . '</td>';
        $report_data .=  '<td>' . $row['follow_up_call_status'] . '</td>';
        //$name_of_file = '../Invoice/Download/invoice_'.$invoice['invoiceid'].'.pdf';
        //$report_data .= '<td><a href="'.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a></td>';
        $report_data .= '</tr>';
        $odd_even++;
    }

    $report_data .= '</table>';

    return $report_data;
}

?>