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
                $this->Image($image_file, 10, 10, '', '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);

            $this->SetFont('helvetica', '', 13);
            $footer_text = 'Validation From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : Validation by Therapist provides an invoice summary of the selected date range sorted by Therapist, and displays the patient and insurer portions of each invoice separately.";
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

    $html .= report_daily_validation($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '', $therapistpdf);

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/validation_'.$today_date.'.pdf', 'F');

    track_download($dbc, 'report_daily_validation', 0, WEBSITE_URL.'/Reports/Download/validation_'.$today_date.'.pdf', 'Validation Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/validation_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $therapist = $therapistpdf;
} ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Validation by Therapist provides an invoice summary of the selected date range sorted by Therapist, and displays the patient and insurer portions of each invoice separately.</div>
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
							<option <?php if ($therapist=='Unassigned') echo 'selected="selected"';?> value="Unassigned">Unassigned</option>
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
                //echo '<a href="report_referral.php?referral=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a></h4><br>';

                if ($therapist!='') {
                    echo report_daily_validation($dbc, $starttime, $endtime, '', '', '', $therapist);
                }
            ?>

        </form>


<?php
function report_daily_validation($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $therapist) {

    if($therapist == 'Unassigned') {
        $total1 = 0;
        $total2 = 0;
        $total3 = 0;

        $report_validation = mysqli_query($dbc,"SELECT * FROM invoice WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND (therapistsid='' OR therapistsid = 0) ORDER BY invoiceid");
        $num_rows = mysqli_num_rows($report_validation);

        if($num_rows > 0) {
            $report_data .= '<h3>Unassigned</h3>';
            $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">
            <tr style="'.$table_row_style.'" nobr="true">
                <th width="8%">Inv. Date</th>
                <th width="10%">Patient</th>
                <th width="7%">Inv. No</th>
                <th width="30%">Description</th>
                <th width="6%">Invoice Status</th>
                <th width="6%">Invoice Amt.</th>
                <th width="15%">Patient Pay</th>
                <th width="18%">Insurer Pay</th>
            </tr>';

            while($row_report = mysqli_fetch_array($report_validation)) {
                $invid = $row_report['invoiceid'];
                $report_data .= '<tr nobr="true">';
                $report_data .= '<td>' . $row_report['invoice_date'] . '</td>';

                $report_data .= '<td><a href="../Contacts/add_contacts.php?category=Patient&contactid='.$row_report['patientid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">'.get_contact($dbc, $row_report['patientid']). '</a></td>';

                $report_data .= '<td>#' . $row_report['invoiceid'];
                $name_of_file = '../Invoice/Download/invoice_'.$invid.'.pdf';
                $report_data .= '&nbsp;&nbsp;<a href="'.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" style="height: 13px; width: 13px;" title="PDF"> </a></td>';

                $serviceid = explode(',', $row_report['serviceid']);
                $report_data .= '<td>';
                $services = '';
                foreach ($serviceid as $total_sid) {
                    if($total_sid != '') {
                        $services .= get_all_from_service($dbc, $total_sid, 'service_code').' : '.get_all_from_service($dbc, $total_sid, 'heading').'<br>';
                    }
                }

                $report_data .= preg_replace('/(<br>)+$/', '', $services);

                $parts1 = explode(',', $row_report['inventoryid']);
                $invtype = explode(',', $row_report['invtype']);
                $k = 0;
                $total_inv = 0;
                $inventory = '';
                foreach ($parts1 as $key1) {
                    if($key1 != '') {
                        $inventory .= get_all_from_inventory($dbc, $key1 , 'name'). '<br>';
                    }
                    $k++;
                }
                $report_data .= preg_replace('/(<br>)+$/', '', $inventory);
                $report_data .= '</td>';

                $report_data .= '<td>'.($row_report['invoice_type'] == 'New' ? ($row_report['paid'] == 'Yes' ? 'Paid' : 'Invoiced') : $row_report['invoice_type']).'</td>';
                $report_data .= '<td>$' . $row_report['final_price'] . '</td>';

                $report_data .= '<td>';
                $invoice_patient = '';
                $report_validation2 = mysqli_query($dbc,"SELECT patient_price, paid FROM invoice_patient WHERE invoiceid='$invid'");
                while($row_report2 = mysqli_fetch_array($report_validation2)) {
                    $invoice_patient .= '$'.$row_report2['patient_price'].' : '.$row_report2['paid'].'<br>';
                    $total2 += $row_report2['patient_price'];
                }

                $report_data .= preg_replace('/(<br>)+$/', '', $invoice_patient);
                $report_data .= '</td>';

                $report_data .= '<td>';
                $invoice_insurer = '';
                $report_validation1 = mysqli_query($dbc,"SELECT insurerid, insurer_price, paid FROM invoice_insurer WHERE invoiceid='$invid'");
                while($row_report1 = mysqli_fetch_array($report_validation1)) {
                    if($row_report1['paid'] == 'No') {
                        $row_report1['paid'] = 'Waiting on Insurer';
                    }
                    $invoice_insurer .= '$'.$row_report1['insurer_price'].' : '.get_all_form_contact($dbc, $row_report1['insurerid'], 'name').' : '.$row_report1['paid'].'<br>';
                    $total3 += $row_report1['insurer_price'];
                }
                $report_data .= preg_replace('/(<br>)+$/', '', $invoice_insurer);
                $report_data .= '</td>';

                $report_data .= "</tr>";
                $total1 += $row_report['final_price'];
            }

            $report_data .= '<tr nobr="true">';
            $report_data .= '<td colspan="4"><b>Total</b></td>';
            $report_data .= '<td></td>';
            $report_data .= '<td><b>$' . number_format($total1, 2) . '</b></td>';
            $report_data .= '<td><b>$' . number_format($total2, 2) . '</b></td>';
            $report_data .= '<td><b>$' . number_format($total3, 2) . '</b></td>';
            $report_data .= "</tr>";
            $report_data .= '</table>';
        }
    } else {
        if($therapist == '') {
            $result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND (category_contact = 'Physical Therapist' OR category_contact = 'Massage Therapist' OR category_contact = 'Osteopathic Therapist') AND deleted=0 AND status=1"),MYSQLI_ASSOC));
        } else {
            $result = [ $therapist ];
        }

        foreach($result as $rowid) {
            $row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `contactid`='$rowid'"));

            $total1 = 0;
            $total2 = 0;
            $total3 = 0;
            $tid = $row['contactid'];
            $therapist = decryptIt($row['first_name']).' '.decryptIt($row['last_name']);

            $report_validation = mysqli_query($dbc,"SELECT * FROM invoice WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND therapistsid='$tid' ORDER BY invoiceid");
            $num_rows = mysqli_num_rows($report_validation);

            if($num_rows > 0) {
                $report_data .= '<h3>'.$therapist.'</h3>';
                $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">
                <tr style="'.$table_row_style.'" nobr="true">
                <th width="8%">Inv. Date</th>
                <th width="10%">Patient</th>
                <th width="7%">Inv. No</th>
                <th width="30%">Description</th>
                <th width="6%">Invoice Status</th>
                <th width="6%">Invoice Amt.</th>
                <th width="15%">Patient Pay</th>
                <th width="18%">Insurer Pay</th>
                </tr>';

                while($row_report = mysqli_fetch_array($report_validation)) {
                    $invid = $row_report['invoiceid'];
                    $report_data .= '<tr nobr="true">';
                    $report_data .= '<td>' . $row_report['invoice_date'] . '</td>';

                    $report_data .= '<td><a href="../Contacts/add_contacts.php?category=Patient&contactid='.$row_report['patientid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">'.get_contact($dbc, $row_report['patientid']). '</a></td>';

                    $report_data .= '<td>#' . $row_report['invoiceid'];
                    $name_of_file = '../Invoice/Download/invoice_'.$invid.'.pdf';
                    $report_data .= '&nbsp;&nbsp;<a href="'.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" style="height: 13px; width: 13px;" title="PDF"> </a></td>';

                    $serviceid = explode(',', $row_report['serviceid']);
                    $report_data .= '<td>';
                    $services = '';
                    foreach ($serviceid as $total_sid) {
                        if($total_sid != '') {
                            $services .= get_all_from_service($dbc, $total_sid, 'service_code').' : '.get_all_from_service($dbc, $total_sid, 'heading').'<br>';
                        }
                    }

                    $parts1 = explode(',', $row_report['inventoryid']);
                    $invtype = explode(',', $row_report['invtype']);
                    $k = 0;
                    $total_inv = 0;
                    $inventory = '';
                    foreach ($parts1 as $key1) {
                        if($key1 != '') {
                            $inventory .= get_all_from_inventory($dbc, $key1 , 'name'). '<br>';
                        }
                        $k++;
                    }
                    $report_data .= preg_replace('/(<br>)+$/', '', $services.$inventory);
                    $report_data .= '</td>';

					$report_data .= '<td>'.($row_report['invoice_type'] == 'New' ? ($row_report['paid'] == 'Yes' ? 'Paid' : 'Invoiced') : $row_report['invoice_type']).'</td>';
                    $report_data .= '<td>$' . $row_report['final_price'] . '</td>';

                    $report_data .= '<td>';
                    $invoice_patient = '';
                    $report_validation2 = mysqli_query($dbc,"SELECT patient_price, paid FROM invoice_patient WHERE invoiceid='$invid'");
                    while($row_report2 = mysqli_fetch_array($report_validation2)) {
                        $invoice_patient .= '$'.$row_report2['patient_price'].' : '.$row_report2['paid'].'<br>';
                        $total2 += $row_report2['patient_price'];
                    }

                    $report_data .= preg_replace('/(<br>)+$/', '', $invoice_patient);
                    $report_data .= '</td>';

                    $report_data .= '<td>';
                    $invoice_insurer = '';
                    $report_validation1 = mysqli_query($dbc,"SELECT insurerid, insurer_price, paid FROM invoice_insurer WHERE invoiceid='$invid'");
                    while($row_report1 = mysqli_fetch_array($report_validation1)) {
                        if($row_report1['paid'] == 'No') {
                            $row_report1['paid'] = 'Waiting on Insurer';
                        }
                    $invoice_insurer .= '$'.$row_report1['insurer_price'].' : '.get_all_form_contact($dbc, $row_report1['insurerid'], 'name').' : '.$row_report1['paid'].'<br>';
                        $total3 += $row_report1['insurer_price'];
                    }
                    $report_data .= preg_replace('/(<br>)+$/', '', $invoice_insurer);
                    $report_data .= '</td>';

                    $report_data .= "</tr>";
                    $total1 += $row_report['final_price'];
                }

                $report_data .= '<tr nobr="true">';
                $report_data .= '<td colspan="4"><b>Total</b></td>';
                $report_data .= '<td></td>';
                $report_data .= '<td><b>$' . number_format($total1, 2) . '</b></td>';
                $report_data .= '<td><b>$' . number_format($total2, 2) . '</b></td>';
                $report_data .= '<td><b>$' . number_format($total3, 2) . '</b></td>';
                $report_data .= "</tr>";
                $report_data .= '</table>';
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