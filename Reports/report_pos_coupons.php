<?php
/*
 * POS Coupons Report
 */
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if ( isset ( $_POST['printpdf'] ) ) {
    $starttimepdf	= $_POST['starttimepdf'];
    $endtimepdf		= $_POST['endtimepdf'];

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
            $header_text = '<center>'.REPORT_HEADER.'</center>';
            $this->writeHTMLCell(0, 0, 0 , 5, $header_text, 0, 0, false, "C", true);
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

    $html = '<br /><br /><h2>POS Coupons Usage</h2>' . report_pos_coupons($dbc, 'padding:3px; border:1px solid black;', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/pos_coupons_'.$today_date.'.pdf', 'F');

    track_download($dbc, 'report_pos_coupons', 0, WEBSITE_URL.'/Reports/Download/pos_coupons_'.$today_date.'.pdf', 'POS Coupons Report');

    ?>

	<script type="text/javascript">
		window.open('Download/pos_coupons_<?php echo $today_date; ?>.pdf', 'fullscreen=yes');
	</script><?php
} ?>

            <form method="post" action="">
				<button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
			</form>
            <br /><br /><?php

			echo report_pos_coupons($dbc, '', ''); ?>
<?php
function report_pos_coupons($dbc, $table_style, $table_row_style) {
	$report_data = '';
	$result = mysqli_query ( $dbc, "SELECT * FROM `pos_touch_coupons` WHERE `deleted`=0" );

	if ( mysqli_num_rows($result) > 0 ) {
		$report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
			$report_data .= '<tr style="'.$table_row_style.'">
				<th width="8%">Coupon ID</th>
				<th width="20%">Title</th>
				<th width="30%">Description</th>
				<th width="10%">Coupon Value</th>
				<th width="10%">Start Date</th>
				<th width="10%">Expiry Date</th>
				<th width="12%"># of Times Used</th>
			</tr>';

			while ( $row=mysqli_fetch_assoc($result) ) {
				if ( $row['discount_type'] == '%' ) {
					$discount = $row['discount'] . '%';
				} else {
					$discount = '$' . number_format ( $row['discount'], 2 );
				}

				$report_data .= '<tr>';
					$report_data .= '<td>' . $row['couponid']		. '</td>';
					$report_data .= '<td>' . $row['title']			. '</td>';
					$report_data .= '<td>' . html_entity_decode ( $row['description'] ) . '</td>';
					$report_data .= '<td>' . $discount				. '</td>';
					$report_data .= '<td>' . $row['start_date']		. '</td>';
					$report_data .= '<td>' . $row['expiry_date']	. '</td>';
					$report_data .= '<td>' . $row['used_times']		. '</td>';
				$report_data .= "</tr>";
			}
		$report_data .= '</table>';

		return $report_data;

	} else { ?>
		<h2>No Record Found.</h2><?php
	}
} ?>
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
