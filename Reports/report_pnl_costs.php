<?php
/*
 * Profit & Loss:Costs Report
 */
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if ( isset($_POST['printpdf']) ) {
    $search_start_pdf   = $_POST['search_start_pdf'];
    $search_end_pdf     = $_POST['search_end_pdf'];

    DEFINE('START_DATE', $search_start_pdf);
    DEFINE('END_DATE', $search_end_pdf);
    DEFINE('REPORT_LOGO', get_config($dbc, 'report_logo'));
    DEFINE('REPORT_HEADER', html_entity_decode(get_config($dbc, 'report_header')));
    DEFINE('REPORT_FOOTER', html_entity_decode(get_config($dbc, 'report_footer')));

	class MYPDF extends TCPDF {

		public function Header() {
            if ( REPORT_LOGO != '' ) {
                $image_file = 'download/'.REPORT_LOGO;
                $this->Image($image_file, 10, 10, 80, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);

            $this->SetFont('helvetica', '', 13);
            $footer_text = 'Inventory Costs From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE:The report displays inventory costs between two selected dates, broken out by inventory Items.";
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

    $html .= report_pnl_display($dbc, $search_start_pdf, $search_end_pdf, 'padding:3px; border:1px solid black;', 'border:1px solid black', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/report_pnl_expenses_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_pnl_costs', 0, WEBSITE_URL.'/Reports/Download/report_pnl_expenses_'.$today_date.'.pdf', 'Inventory Costs Report');

    ?>

	<script type="text/javascript" language="Javascript">
        window.open('Download/report_pnl_expenses_<?= $today_date; ?>.pdf', 'fullscreen=yes');
	</script><?php

    $search_start  = $search_start_pdf;
    $search_end    = $search_end_pdf;
} ?>

            <div class="notice double-gap-bottom popover-examples">
                <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
                <div class="col-sm-11">
                    <span class="notice-name">NOTE:</span>
                    The report displays inventory costs between two selected dates, broken out by inventory Items.</div>
                <div class="clearfix"></div>
            </div>

            <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form"><?php

                if ( isset($_POST['search_email_submit']) ) {
                    $search_start  = $_POST['search_start'];
                    $search_end    = $_POST['search_end'];
                }

                if ( $search_start == 0000-00-00 ) {
                    $search_start = date('Y-m-01');
                }

                if ( $search_end == 0000-00-00 ) {
                    $search_end = date('Y-m-d');
                } ?>

                <center><div class="form-group">
					<div class="form-group col-sm-5">
						<label class="col-sm-4">From:</label>
						<div class="col-sm-8"><input name="search_start" type="text" class="datepicker form-control" value="<?php echo $search_start; ?>"></div>
					</div>
					<div class="form-group col-sm-5">
						<label class="col-sm-4">Until:</label>
						<div class="col-sm-8"><input name="search_end" type="text" class="datepicker form-control" value="<?php echo $search_end; ?>"></div>
					</div>
				<button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

                <input type="hidden" name="search_start_pdf" value="<?= $search_start; ?>" />
                <input type="hidden" name="search_end_pdf" value="<?= $search_end; ?>" />

                <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
                <br /><br /><?php

                echo report_pnl_display($dbc, $search_start, $search_end, '', '', ''); ?>
            </form>

<?php
function report_pnl_display($dbc, $search_start, $search_end, $table_style, $table_row_style, $grand_total_style) {
    $startyear = intval(explode('-', $search_start)[0]);
    $endyear   = intval(explode('-', $search_end)[0]);

	$inv_cost_field = get_config($dbc,'inventory_cost');
    for ($year = $startyear; $year <= $endyear; $year++) {
        $total_sql = "SELECT SUM(IF(`date_added` LIKE '".$year."-01%', `cost`*`quantity`, 0)) `JAN`,
            SUM(IF(`date_added` LIKE '".$year."-02%', `cost`*`quantity`, 0)) `FEB`,
            SUM(IF(`date_added` LIKE '".$year."-03%', `cost`*`quantity`, 0)) `MAR`,
            SUM(IF(`date_added` LIKE '".$year."-04%', `cost`*`quantity`, 0)) `APR`,
            SUM(IF(`date_added` LIKE '".$year."-05%', `cost`*`quantity`, 0)) `MAY`,
            SUM(IF(`date_added` LIKE '".$year."-06%', `cost`*`quantity`, 0)) `JUN`,
            SUM(IF(`date_added` LIKE '".$year."-07%', `cost`*`quantity`, 0)) `JUL`,
            SUM(IF(`date_added` LIKE '".$year."-08%', `cost`*`quantity`, 0)) `AUG`,
            SUM(IF(`date_added` LIKE '".$year."-09%', `cost`*`quantity`, 0)) `SEP`,
            SUM(IF(`date_added` LIKE '".$year."-10%', `cost`*`quantity`, 0)) `OCT`,
            SUM(IF(`date_added` LIKE '".$year."-11%', `cost`*`quantity`, 0)) `NOV`,
            SUM(IF(`date_added` LIKE '".$year."-12%', `cost`*`quantity`, 0)) `DEC`
            FROM (SELECT CONCAT(IF(i.`category` = '', '', CONCAT(i.`category`,':')), i.`name`) name, i.`inventoryid`, rs.`quantity`, `$inv_cost_field` cost,
                rs.`date_added` FROM `receive_shipment` rs LEFT JOIN `inventory` i ON rs.`inventoryid`=i.`inventoryid` WHERE `date_added` >= '$search_start' AND `date_added` <= '$search_end') inventory";
        $total_result = mysqli_fetch_array(mysqli_query($dbc, $total_sql));
        $totals = [ $total_result['JAN'], $total_result['FEB'], $total_result['MAR'], $total_result['APR'], $total_result['MAY'], $total_result['JUN'],
            $total_result['JUL'], $total_result['AUG'], $total_result['SEP'], $total_result['OCT'], $total_result['NOV'], $total_result['DEC'] ];
        $total_taxes = [ $total_result['JANTAX'], $total_result['FEBTAX'], $total_result['MARTAX'], $total_result['APRTAX'], $total_result['MAYTAX'], $total_result['JUNTAX'],
            $total_result['JULTAX'], $total_result['AUGTAX'], $total_result['SEPTAX'], $total_result['OCTTAX'], $total_result['NOVTAX'], $total_result['DECTAX'] ];
        $inventory_sql = "SELECT `name`,
            SUM(IF(`date_added` LIKE '".$year."-01%', `cost`*`quantity`, 0)) `JAN`,
            SUM(IF(`date_added` LIKE '".$year."-02%', `cost`*`quantity`, 0)) `FEB`,
            SUM(IF(`date_added` LIKE '".$year."-03%', `cost`*`quantity`, 0)) `MAR`,
            SUM(IF(`date_added` LIKE '".$year."-04%', `cost`*`quantity`, 0)) `APR`,
            SUM(IF(`date_added` LIKE '".$year."-05%', `cost`*`quantity`, 0)) `MAY`,
            SUM(IF(`date_added` LIKE '".$year."-06%', `cost`*`quantity`, 0)) `JUN`,
            SUM(IF(`date_added` LIKE '".$year."-07%', `cost`*`quantity`, 0)) `JUL`,
            SUM(IF(`date_added` LIKE '".$year."-08%', `cost`*`quantity`, 0)) `AUG`,
            SUM(IF(`date_added` LIKE '".$year."-09%', `cost`*`quantity`, 0)) `SEP`,
            SUM(IF(`date_added` LIKE '".$year."-10%', `cost`*`quantity`, 0)) `OCT`,
            SUM(IF(`date_added` LIKE '".$year."-11%', `cost`*`quantity`, 0)) `NOV`,
            SUM(IF(`date_added` LIKE '".$year."-12%', `cost`*`quantity`, 0)) `DEC`
            FROM (SELECT CONCAT(IF(i.`category` = '', '', CONCAT(i.`category`,':')), i.`name`) name, i.`inventoryid`, rs.`quantity`, `$inv_cost_field` cost,
                rs.`date_added` FROM `receive_shipment` rs LEFT JOIN `inventory` i ON rs.`inventoryid`=i.`inventoryid` WHERE `date_added` >= '$search_start' AND `date_added` <= '$search_end') inventory
            GROUP BY `name`
            ORDER BY `name`, `date_added` LIMIT $offset, $rowsPerPage";
        $inventory = mysqli_query($dbc, $inventory_sql);

        $report_data = '<table class="table table-bordered" style="'. $table_style .'">
            <thead>
                <tr class="hidden-xs hidden-sm">
                    <th>Inventory</th>';
                    for($month = 0; $month < 12; $month++) {
                        $dateObj = DateTime::createFromFormat('!m', $month+1);
                        $report_data .= '<th style="width:8em; '. $table_row_style .'">'. $dateObj->format('F') .'</th>';
                    }
                $report_data .= '</tr>
            </thead>
            <tbody>';
                $category = '';

                $odd_even = 0;
                while($row = mysqli_fetch_array($inventory)) {
                    $bg_class = $odd_even % 2 == 0 ? '' : 'background-color:#e6e6e6;';
                    $report_data .= '<tr style="'.$bg_class.'"><td data-title="Inventory" style="'. $table_row_style .'">'. $row['name'] .'</td>';
                    for($month = 0; $month < 12; $month++) {
                        $dateObj = DateTime::createFromFormat('!m', $month+1);
                        $amt = $row[strtoupper($dateObj->format('M'))];
                        $report_data .= '<td data-title="'. $dateObj->format('F') .'" style="text-align:right;'. $table_row_style .'">$'. number_format($amt, 2, '.', ',') .'</td>';
                    }
                    $report_data .= '</tr>';
                    $odd_even++;
                }

                $report_data .= '<tr style="font-weight:bold;">
                    <td style="'. $table_row_style .'">Monthly Total</td>';
                    for($month = 0; $month < 12; $month++) {
                        $dateObj = DateTime::createFromFormat('!m', $month+1);
                        $report_data .= '<td data-title="'. $dateObj->format('F') .'" style="text-align:right;'. $table_row_style .'">$'. number_format($totals[$month], 2, '.', ',') . '</td>';
                    }
                $report_data .= '</tr>
                <tr style="font-size:1.5em; font-weight:bold;">
                    <td colspan="10" style="border-right:none;">Total Inventory Costs for ';
                    $report_data .= ($year == $startyear) ? $search_start : $year.'-01-01';
                    $report_data .= ' to ';
                    $report_data .= ($year == $endyear) ? $search_end : $year.'-12-31';
                    $report_data .= '</td>
                    <td data-title="Total" colspan="3" style="text-align:right; border-left:none;">$'. number_format(array_sum($totals), 2, '.', ',') .'</td>
                </tr>
            </tbody>
        </table>';
    }

    return $report_data;
}