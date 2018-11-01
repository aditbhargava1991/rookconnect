<?php
/*
 * How Did They Hear About Us Report
 */
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if ( isset ( $_POST['printpdf'] ) ) {
    $start_date_pdf = $_POST['start_date_pdf'];
    $end_date_pdf   = $_POST['end_date_pdf'];

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

            $this->SetFont('helvetica', '', 13);
            $footer_text = 'Referrals From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
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

    $html .= report_referral($dbc, $start_date_pdf, $end_date_pdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/web_referral_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_web_referral', 0, WEBSITE_URL.'/Reports/Download/web_referral_'.$today_date.'.pdf', 'Referrals Report');

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
				<button type="submit" name="search_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

                <input type="hidden" name="start_date_pdf" value="<?php echo $start_date; ?>" />
                <input type="hidden" name="end_date_pdf" value="<?php echo $end_date; ?>" />

                <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
                <br /><br /><?php

                echo report_referral($dbc, $start_date, $end_date, '', '', ''); ?>
            </form>
            

<?php
function report_referral($dbc, $start_date, $end_date, $table_style, $table_row_style, $grand_total_style) {

    $total  = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT COUNT(`refid`) AS `total` FROM `report_web_referral` WHERE (`date_added` BETWEEN '$start_date' AND '$end_date') ORDER BY `date_added` DESC" ) );
    $result = mysqli_query ( $dbc, "SELECT * FROM `report_web_referral` WHERE (`date_added` BETWEEN '$start_date' AND '$end_date') ORDER BY `date_added` DESC" );

    $num_rows = mysqli_num_rows($result);

    if ( $num_rows > 0 ) {

        $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
            $report_data .= '
                <tr style="'.$table_row_style.'">
                    <th>Form</th>
                    <th>Customer</th>
                    <th>Referred By</th>
                    <th>Date</th>
                </tr>';

            while ( $row = mysqli_fetch_assoc($result) ) {
                $report_data .= '<tr nobr="true">';
                    $report_data .= '<td>' . $row['form'] . '</td>';
                    $report_data .= '<td>' . $row['first_name'] . ' ' . $row['last_name'] . '</td>';
                    $report_data .= '<td>' . $row['ref_first_name'] . ' ' . $row['ref_last_name'] . '</td>';
                    $report_data .= '<td>' . $row['date_added'] . '</td>';
                $report_data .= '</tr>';
            }

            $report_data .= '
                <tr>
                    <td colspan="2" align="right"><strong>Total</strong></td>
                    <td><strong>' . $total['total'] . '</strong></td>
                </tr>';
        $report_data .= '</table>';

    } else {
        $report_data = '<h2>No records found for the selected date range.</h2>';
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