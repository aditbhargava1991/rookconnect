<?php
/*
Client Listing
*/
include ('../include.php');
include_once('../function.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_EquipmentT['printpdf'])) {

    $starttimepdf = $_EquipmentT['starttimepdf'];
    $endtimepdf = $_EquipmentT['endtimepdf'];
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
            $footer_text = 'Equipment History';
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

	//$pdf->AddPage('L', 'LETTER');
    //$pdf->SetFont('helvetica', '', 9);

    //$html .= report_receivables($dbc, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

    $start_date = date('Y-m-d', strtotime($starttimepdf));
    $end_date = date('Y-m-d', strtotime($endtimepdf));
    $end_date_set = date('Y-m-d', strtotime($endtimepdf. ' + 1 days'));
    $html = '';


        $pdf->AddPage('L', 'LETTER');
        $pdf->SetFont('helvetica', '', 9);
        $html = '';

        $html .= "<h3>".$date."</h3>";
        $html .= '<br><br>' . report_receivables($dbc, $start_date, $end_date_set, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

        $pdf->writeHTML($html, true, false, true, false, '');


    $today_date = date('Y-m-d');
	//$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/equipment_history_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'history_equipment_history', 0, WEBSITE_URL.'/Reports/Download/equipment_history_'.$today_date.'.pdf', 'Equipment History Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/equipment_history_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    } ?>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if (isset($_EquipmentT['search_email_submit'])) {
                $starttime = $_EquipmentT['starttime'];
                $endtime = $_EquipmentT['endtime'];
            }

            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-d');
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
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                $start_date = date('Y-m-d', strtotime($starttime));
                $end_date = date('Y-m-d', strtotime($endtime. ' + 1 days'));
                echo report_receivables($dbc, $start_date, $end_date, '', '', '');
                echo "<br>";

            ?>

        </form>

<?php
function report_receivables($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style) {

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';

    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="30%">Detail Before Change</th>
    <th width="30%">Changed Detail</th>
    <th width="15%">Changed By</th>
    <th width="15%">Changed Date</th>
    </tr>';

	$query = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM equipment_history where updated_at >= '$starttime' and updated_at < '$endtime' ORDER BY updated_at DESC"));
    $odd_even = 0;
	foreach($query as $rowid) {
        $bg_class = $odd_even % 2 == 0 ? '' : 'background-color:#e6e6e6;';
        $changed_by = $rowid[7];
        $changed_date = $rowid[6];
        $change_details = $rowid[8];
        $cid = $row['contactid'];

        $report_data .= '<tr nobr="true" style="'.$bg_class.'">';
            $report_data .= '<td>'.html_entity_decode($rowid[4]).'</td>';
            $report_data .= '<td>'.html_entity_decode($change_details).'</td>';
            $report_data .= '<td>'.$changed_by.'</td>';
            $report_data .= '<td>'.$changed_date.'</td>';
        $report_data .= "</tr>";

        $odd_even++;
    }

    $report_data .= "</table>";

    return $report_data;
}

function AddPlayTime2($times) {
    $minutes = 0;
    foreach ($times as $time) {
        $minutes += $time;
    }

    $hours = floor($minutes / 60);
    $minutes -= $hours * 60;

    //return $hours.':'.$minutes;
    return sprintf('%02d:%02d:00', $hours, $minutes);
}

function AddPlayTime($times) {
    // loop tequipmentought all the times
    foreach ($times as $time) {
        list($hour, $minute) = explode(':', $time);
        $minutes += $hour * 60;
        $minutes += $minute;
    }

    $hours = floor($minutes / 60);
    $minutes -= $hours * 60;

    // returns the time already formatted
    return sprintf('%02d H %02d M', $hours, $minutes);
}
?>
