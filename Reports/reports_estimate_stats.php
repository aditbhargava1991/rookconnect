<?php
/*
Client Listing
*/
include ('../include.php');
include_once('../function.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['printpdf'])) {

    $starttimepdf = $_POST['starttimepdf'];
    $endtimepdf = $_POST['endtimepdf'];
    $staffidpdf = $_POST['staffidpdf'];
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
            $footer_text = 'Estimate Stat';
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
    $html = '';

    $estimate_report_stats = get_config($dbc, 'estimate_report_stats');
    $html .= reporting_estimate_stats($dbc, $starttimepdf, $endtimepdf, explode('#*#', $estimate_report_stats), $staffidpdf, '', '', '', 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

    $pdf->writeHTML($html, true, false, true, false, '');

    $today_date = date('Y-m-d');
	//$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/estimate_stat_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'estimate_stat', 0, WEBSITE_URL.'/Reports/Download/estimate_stat_'.$today_date.'.pdf', 'Staff History Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/estimate_stat_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $staffid = $staffidpdf;
    } ?>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
                $staffid = $_POST['staffid'];
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
				<div class="form-group col-sm-3">
                    <label class="col-sm-4">Staff:</label>
                    <div class="col-sm-8">
					<select name="staffid" data-placeholder="Select Staff" class="chosen-select-deselect">
						<option></option>
						<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 AND `status` > 0"),MYSQLI_ASSOC));
						foreach ($staff_list as $id) {
							echo '<option value="'.$id.'"'.($id == $staffid ? ' selected' : '').'>'.get_contact($dbc, $id).'</option>';
						} ?>
					</select>
                    </div>
				</div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="staffidpdf" value="<?php echo $staffid; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                $estimate_report_stats = get_config($dbc, 'estimate_report_stats');
                echo reporting_estimate_stats($dbc, $starttime, $endtime, explode('#*#', $estimate_report_stats), $staffid, '', '', '');
            ?>

        </form>

<?php

function reporting_estimate_stats($dbc, $from, $until, $statuses, $staff, $table_style, $table_row_style, $grand_total_style) {
	$staff_list = [];
	if (!empty($staff)) {
		$staff_list[] = $staff;
	} else {
		$staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 AND `status` > 0"),MYSQLI_ASSOC));
	}
	$html .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
	$html .= '<tr style="'.$table_row_style.'">';
	$html .= '<th>Staff</th>';
	$html .= '<th>Total Opportunities</th>';
	$html .= '<th>Total Value of Opportunities</th>';
	foreach ($statuses as $status) {
		$html .= '<th>Total '.$status.'</th>';
		$html .= '<th>Total Value of '.$status.'</th>';
		$html .= '<th>% of '.$status.' Opportunities</th>';
	}
	$html .= '</tr>';

	foreach ($staff_list as $id) {
		$estimate_summary = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as num_estimates, SUM(total_price) as total_value FROM `estimate` WHERE CONCAT(',',`assign_staffid`,',') LIKE '%,".$id.",%' AND `status_date` >= '".$from."' AND `status_date` <= '".$until."' AND `deleted` = 0"));
		$html .= '<tr style="'.$table_row_style.'">';
		$html .= '<td>'.get_contact($dbc, $id).'</td>';
		$html .= '<td>'.$estimate_summary['num_estimates'].'</td>';
		$html .= '<td>$'.number_format($estimate_summary['total_value'], 2).'</td>';
		foreach ($statuses as $status) {
			$estimate_stats = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as num_estimates, SUM(total_price) as total_value FROM `estimate` WHERE CONCAT(',',`assign_staffid`,',') LIKE '%,".$id.",%' AND `status` = '".preg_replace('/[^a-z]/','',strtolower($status))."' AND `status_date` >= '".$from."' AND `status_date` <= '".$until."' AND `deleted` = 0"));
			$html .= '<td>'.$estimate_stats['num_estimates'].'</td>';
			$html .= '<td>$'.number_format($estimate_stats['total_value'], 2).'</td>';
			$html .= '<td>'.number_format($estimate_summary['num_estimates'] / $estimate_stats['num_estimates'] * 100, 1).'%</td>';
		}
		$html .= '</tr>';
	}

	$html .= '</table>';

	return $html;
}

?>
