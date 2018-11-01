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
            $footer_text = 'Discharge vs Active From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : Displays the total number of Active and Discharged customers, with discharged customer names and discharge dates.";
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

    $html .= report_discharge($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '', $therapistpdf);

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/active_discharge_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_discharge', 0, WEBSITE_URL.'/Reports/Download/active_discharge_'.$today_date.'.pdf', 'Discharge vs Active Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/active_discharge_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $therapist = $therapistpdf;
    } ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Displays the total number of Active and Discharged customers, with discharged customer names and discharge dates.</div>
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
						<select data-placeholder="Select a Staff..." name="therapist" class="chosen-select-deselect form-control">
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
                //echo '<a href="report_discharge.php?goals=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a><br><br>';

                echo report_discharge($dbc, $starttime, $endtime, '', '', '', $therapist);
            ?>

        </form>

<?php
function report_discharge($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $therapist) {
    $report_data = '';

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">
    <tr style="'.$table_row_style.'">
    <th width="10%"><center>Staff</center></th>
    <th width="10%">
		<center>
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="The total number of customers that are currently under a program with the staff."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20" style="padding-bottom:5px;"></a></span><br />
			Total Active<br><em>based on date added</em>
		</center>
	</th>
    <th width="10%">
		<center>
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="The total number of discharged customers within the specific time range."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20" style="padding-bottom:5px;"></a></span><br />
			Total Discharged<br><em>on basis of discharge date</em>
		</center>
	</th>
    <th width="70%">
		<span class="popover-examples list-inline" style="margin:0 0 0 2px;"><a data-toggle="tooltip" data-placement="top" title="This shows the breakdown of each customer and the date they were discharged for staff follow up."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20" style="padding-bottom:5px;"></a></span><br />
		Discharged Customer Name and Discharge Date
	</th>
    </tr>';

    if($therapist == '') {
		$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name, scheduled_hours FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND (category_contact = 'Physical Therapist' OR category_contact = 'Massage Therapist' OR category_contact = 'Osteopathic Therapist') AND deleted=0 AND status=1 ORDER BY first_name"),MYSQLI_ASSOC));
    } else {
		$result = [ $therapist ];
    }

    $c1 = 0;
    $c2 = 0;
    foreach($result as $therapistsid) {
		$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT contactid, first_name, last_name, scheduled_hours FROM contacts WHERE contactid='$therapistsid'"));

        $total_active = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(injuryid) AS total_active FROM patient_injury WHERE injury_therapistsid = '$therapistsid' AND discharge_date IS NULL AND today_date >= '".$starttime."' AND today_date <= '".$endtime."'"));

        $total_discharge = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(injuryid) AS total_discharge FROM patient_injury WHERE injury_therapistsid = '$therapistsid' AND discharge_date IS NOT NULL AND discharge_date >= '".$starttime."' AND discharge_date <= '".$endtime."'"));

        $discharge_patient = mysqli_query($dbc,"SELECT contactid, discharge_date FROM patient_injury WHERE injury_therapistsid = '$therapistsid' AND discharge_date IS NOT NULL AND discharge_date >= '".$starttime."' AND discharge_date <= '".$endtime."'");
        $name_discharge = '';
        while($row_discharge_patient = mysqli_fetch_array($discharge_patient)) {
            $name_discharge .= get_contact($dbc, $row_discharge_patient['contactid']).' : '.$row_discharge_patient['discharge_date'].' || ';
        }

        $report_data .= '<tr>
        <td>'.decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</td>
        <td>'.$total_active['total_active'].'</td>
        <td>'.$total_discharge['total_discharge'].'</td>
        <td>'.$name_discharge.'</td>
        </tr>';
        $c1 += $total_active['total_active'];
        $c2 += $total_discharge['total_discharge'];
    }
    $report_data .= '<tr><td><b>Total</b></td><td><b>'.$c1.'</b></td><td><b>'.$c2.'</b></td><td></td></tr>';

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