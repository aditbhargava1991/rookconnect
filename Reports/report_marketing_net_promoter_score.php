<?php
/*
 * Net Promoter Score
 */
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if ( isset ( $_POST['printpdf'] ) ) {
    $start_date_pdf = $_POST['start_date_pdf'];
    $end_date_pdf   = $_POST['end_date_pdf'];
    $location_pdf   = $_POST['location_pdf'];

    DEFINE('START_DATE', $start_date_pdf);
    DEFINE('END_DATE', $end_date_pdf);
    DEFINE('REPORT_LOGO', get_config($dbc, 'report_logo'));
    DEFINE('REPORT_HEADER', html_entity_decode(get_config($dbc, 'report_header')));
    DEFINE('REPORT_FOOTER', html_entity_decode(get_config($dbc, 'report_footer')));

	class MYPDF extends TCPDF {

		public function Header() {
            if(REPORT_LOGO != '') {
                $image_file = 'download/'.REPORT_LOGO;
                $this->Image($image_file, 10, 10, 80, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);

            $this->SetFont('helvetica', '', 15);
            $footer_text = 'Ratings From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 30, 30, $footer_text, 0, 0, false, "R", true);
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

    $html .= report_net_promoter_score($dbc, $start_date_pdf, $end_date_pdf, $location_pdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/web_referral_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_marketing_net_promoter_score', 0, WEBSITE_URL.'/Reports/Download/web_referral_'.$today_date.'.pdf', 'Ratings Report');


    ?>

	<script type="text/javascript">
        window.open('Download/web_referral_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script><?php

    $starttime  = $start_date_pdf;
    $endtime    = $end_date_pdf;
} ?>


            <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form"><?php
                if ( isset ( $_POST['search_submit'] ) ) {
                    $start_date  = $_POST['start_date'];
                    $end_date    = $_POST['end_date'];
                    $location    = $_POST['location'];
                }

                if ( $start_date == '0000-00-00' || empty($start_date) ) {
                    $start_date = date('Y-m-01');
                }

                if ( $end_date == '0000-00-00' || empty($end_date) ) {
                    $end_date = date('Y-m-d');
                } ?>

				<center><div class="form-group">
					<div class="form-group col-sm-5">
						<label class="col-sm-4">From:</label>
						<div class="col-sm-8"><input name="start_date" type="text" class="datepicker form-control" value="<?php echo $start_date; ?>"></div>
					</div>
					<div class="form-group col-sm-5">
						<label class="col-sm-4">Until:</label>
						<div class="col-sm-8"><input name="end_date" type="text" class="datepicker form-control" value="<?php echo $end_date; ?>"></div>
					</div>
					<div class="form-group col-sm-5">
						<label class="col-sm-4">Location:</label>
						<div class="col-sm-8">
							<select data-placeholder="Select a Location..." name="location" class="chosen-select-deselect form-control">
								<option value=""></option>
								<?php $query = mysqli_query($dbc, "SELECT DISTINCT `location` FROM `report_net_promoter_score` ORDER BY `location` ASC");
								while($row = mysqli_fetch_assoc($query)) {
									echo "<option ".($row['location'] == $location ? 'selected' : '')." value='".$row['location']."'>".$row['location']."</option>";
								} ?>
							</select>
						</div>
					</div>
				<button type="submit" name="search_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

                <input type="hidden" name="start_date_pdf" value="<?= $start_date; ?>" />
                <input type="hidden" name="end_date_pdf" value="<?= $end_date; ?>" />
                <input type="hidden" name="location_pdf" value="<?= $location; ?>" />

                <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
                <br /><br /><?php

                echo report_net_promoter_score($dbc, $start_date, $end_date, $location, '', '', ''); ?>
            </form>

<?php
function report_net_promoter_score($dbc, $start_date, $end_date, $location, $table_style, $table_row_style, $grand_total_style) {

    $total  = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT COUNT(`npsid`) AS `total` FROM `report_net_promoter_score` WHERE (`date_added` BETWEEN '$start_date' AND '$end_date') AND `location`='$location' ORDER BY `date_added` DESC" ) );
    $result = mysqli_query ( $dbc, "SELECT * FROM `report_net_promoter_score` WHERE (`date_added` BETWEEN '$start_date' AND '$end_date') AND `location`='$location' ORDER BY `date_added`, `location` DESC" );

    $num_rows = mysqli_num_rows($result);

    if ( $num_rows > 0 ) {

        $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
            $report_data .= '
                <tr style="'.$table_row_style.'">
                    <th>Location</th>
                    <th>Date</th>
                    <th>Rating</th>
                    <th>Comments</th>
                </tr>';

            while ( $row = mysqli_fetch_assoc($result) ) {
                $report_data .= '<tr nobr="true">';
                    $report_data .= '<td>' . $row['location']   . '</td>';
                    $report_data .= '<td>' . $row['date_added'] . '</td>';
                    $report_data .= '<td>' . $row['rating']     . '</td>';
                    $report_data .= '<td>' . $row['comments']   . '</td>';
                $report_data .= '</tr>';
            }

            $report_data .= '
                <tr>
                    <td colspan="2" align="right"><strong>Total Number of Ratings</strong></td>
                    <td><strong>' . $total['total'] . '</strong></td>
                </tr>';
        $report_data .= '</table>';

    } else {
        $report_data = '<h2>No records found for the selected date range and location.</h2>';
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