<?php
/*
 * Profit & Loss: Expenses Report
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
            $footer_text = 'Staff Compensation From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE: The report displays expenses between two selected dates, broken out by expense category.";
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

    track_download($dbc, 'report_pnl_expenses', 0, WEBSITE_URL.'/Reports/Download/report_pnl_expenses_'.$today_date.'.pdf', 'Staff Compensation Report');
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
                    The report displays expenses between two selected dates, broken out by expense category.</div>
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

    $odd_even = 0;
    for ($year = $startyear; $year <= $endyear; $year++) {
        $bg_class = $odd_even % 2 == 0 ? '' : 'background-color:#e6e6e6;';
        $total_sql = "SELECT SUM(IF(`expense_date` LIKE '".$year."-01%', `total`, 0)) `JAN`,
            SUM(IF(`expense_date` LIKE '".$year."-02%', `total`, 0)) `FEB`,
            SUM(IF(`expense_date` LIKE '".$year."-03%', `total`, 0)) `MAR`,
            SUM(IF(`expense_date` LIKE '".$year."-04%', `total`, 0)) `APR`,
            SUM(IF(`expense_date` LIKE '".$year."-05%', `total`, 0)) `MAY`,
            SUM(IF(`expense_date` LIKE '".$year."-06%', `total`, 0)) `JUN`,
            SUM(IF(`expense_date` LIKE '".$year."-07%', `total`, 0)) `JUL`,
            SUM(IF(`expense_date` LIKE '".$year."-08%', `total`, 0)) `AUG`,
            SUM(IF(`expense_date` LIKE '".$year."-09%', `total`, 0)) `SEP`,
            SUM(IF(`expense_date` LIKE '".$year."-10%', `total`, 0)) `OCT`,
            SUM(IF(`expense_date` LIKE '".$year."-11%', `total`, 0)) `NOV`,
            SUM(IF(`expense_date` LIKE '".$year."-12%', `total`, 0)) `DEC`,
            SUM(IF(`expense_date` LIKE '".$year."-01%', `tax`, 0)) `JANTAX`,
            SUM(IF(`expense_date` LIKE '".$year."-02%', `tax`, 0)) `FEBTAX`,
            SUM(IF(`expense_date` LIKE '".$year."-03%', `tax`, 0)) `MARTAX`,
            SUM(IF(`expense_date` LIKE '".$year."-04%', `tax`, 0)) `APRTAX`,
            SUM(IF(`expense_date` LIKE '".$year."-05%', `tax`, 0)) `MAYTAX`,
            SUM(IF(`expense_date` LIKE '".$year."-06%', `tax`, 0)) `JUNTAX`,
            SUM(IF(`expense_date` LIKE '".$year."-07%', `tax`, 0)) `JULTAX`,
            SUM(IF(`expense_date` LIKE '".$year."-08%', `tax`, 0)) `AUGTAX`,
            SUM(IF(`expense_date` LIKE '".$year."-09%', `tax`, 0)) `SEPTAX`,
            SUM(IF(`expense_date` LIKE '".$year."-10%', `tax`, 0)) `OCTTAX`,
            SUM(IF(`expense_date` LIKE '".$year."-11%', `tax`, 0)) `NOVTAX`,
            SUM(IF(`expense_date` LIKE '".$year."-12%', `tax`, 0)) `DECTAX`
            FROM (SELECT 'EXPENSE' `source`, 'Staff Expense' `category`, staff `heading`, ex_date expense_date, gst tax, amount total FROM expense UNION
                SELECT 'BUDGET' `source`, category `category`, expense `heading`, expense_date, tax, actual_amount total FROM budget_expense LEFT JOIN budget_category ON budget_expense.budget_categoryid=budget_category.budget_categoryid) expenses
            WHERE `expense_date` >= '$search_start' AND `expense_date` <= '$search_end'";
        $total_result = mysqli_fetch_array(mysqli_query($dbc, $total_sql));
        $totals = [ $total_result['JAN'], $total_result['FEB'], $total_result['MAR'], $total_result['APR'], $total_result['MAY'], $total_result['JUN'],
            $total_result['JUL'], $total_result['AUG'], $total_result['SEP'], $total_result['OCT'], $total_result['NOV'], $total_result['DEC'] ];
        $total_taxes = [ $total_result['JANTAX'], $total_result['FEBTAX'], $total_result['MARTAX'], $total_result['APRTAX'], $total_result['MAYTAX'], $total_result['JUNTAX'],
            $total_result['JULTAX'], $total_result['AUGTAX'], $total_result['SEPTAX'], $total_result['OCTTAX'], $total_result['NOVTAX'], $total_result['DECTAX'] ];
        $expenses_sql = "SELECT source, heading, category, tab,
            SUM(IF(`expense_date` LIKE '".$year."-01%', `total`-`tax`, 0)) `JAN`,
            SUM(IF(`expense_date` LIKE '".$year."-02%', `total`-`tax`, 0)) `FEB`,
            SUM(IF(`expense_date` LIKE '".$year."-03%', `total`-`tax`, 0)) `MAR`,
            SUM(IF(`expense_date` LIKE '".$year."-04%', `total`-`tax`, 0)) `APR`,
            SUM(IF(`expense_date` LIKE '".$year."-05%', `total`-`tax`, 0)) `MAY`,
            SUM(IF(`expense_date` LIKE '".$year."-06%', `total`-`tax`, 0)) `JUN`,
            SUM(IF(`expense_date` LIKE '".$year."-07%', `total`-`tax`, 0)) `JUL`,
            SUM(IF(`expense_date` LIKE '".$year."-08%', `total`-`tax`, 0)) `AUG`,
            SUM(IF(`expense_date` LIKE '".$year."-09%', `total`-`tax`, 0)) `SEP`,
            SUM(IF(`expense_date` LIKE '".$year."-10%', `total`-`tax`, 0)) `OCT`,
            SUM(IF(`expense_date` LIKE '".$year."-11%', `total`-`tax`, 0)) `NOV`,
            SUM(IF(`expense_date` LIKE '".$year."-12%', `total`-`tax`, 0)) `DEC`
            FROM (SELECT 'EXPENSE' source, `expense_for` tab, CONCAT(IFNULL(ec,''), ':', expense.category) category, concat(IFNULL(gl,''), ':', expense.title) heading, ex_date expense_date, gst tax, amount total FROM expense LEFT JOIN expense_categories ON expense.category=expense_categories.category AND expense.title=expense_categories.heading UNION
                SELECT `budget`.`budget_name` `source`, `budget`.`budgetid` tab, CONCAT(IFNULL(ec,''), ':', category) category, CONCAT(IFNULL(gl,''), ':', expense) heading, expense_date, tax, actual_amount total FROM budget_expense LEFT JOIN budget_category ON budget_expense.budget_categoryid=budget_category.budget_categoryid LEFT JOIN `budget` ON `budget_category`.`budgetid`=`budget`.`budgetid`) expenses
            WHERE `expense_date` LIKE '$search_start' AND `expense_date` <= '$search_end'
            GROUP BY `source`, `tab`, `category`, `heading`
            ORDER BY `source`, `tab`, `category`, `heading`, `expense_date` LIMIT $offset, $rowsPerPage";
        $expenses = mysqli_query($dbc, $expenses_sql);

        $report_data = '<table class="table table-bordered" style="'. $table_style .'">
            <thead>
                <tr class="hidden-xs hidden-sm">
                    <th style="'. $table_row_style .'">Expense</th>';
                    for($month = 0; $month < 12; $month++) {
                        $dateObj = DateTime::createFromFormat('!m', $month+1);
                        $report_data .= '<th style="width:8em; '. $table_row_style .'">'. $dateObj->format('F') .'</th>';
                    }
                $report_data .= '</tr>
            </thead>
            <tbody>';
                $category = $source = '';
                
                $odd_even2 = 0;
                while($row = mysqli_fetch_array($expenses)) {
                    $bg_class2 = $odd_even2 % 2 == 0 ? '' : 'background-color:#e6e6e6;';
                    if($source != $row['source'].$row['tab']) {
                        $source = $row['source'].$row['tab'];
                        if($row['source'] != 'EXPENSE') {
                            $report_data .= '<tr><td colspan="13" style="font-size:1.1em; font-weight:bold; '. $table_row_style .'"><a href="'. WEBSITE_URL .'/Budget/budget_expense.php?budgetid='. $row['tab'] .'&from_url='. urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">'. $row['source'] .'</a></td></tr>';
                        }
                    }

                    if($category != $row['category']) {
                        $category = $row['category'];
                        $report_data .= '<tr><td colspan="13" style="font-size:1.1em; font-weight:bold; '. $table_row_style .'">'. $category .'</td></tr>';
                    }

                    $report_data .= '<tr style="'.$bg_class2.'"><td data-title="Expense" style="'. $table_row_style .'">';
                    $report_data .= ( $row['source'] == 'STAFF') ? get_contact($dbc,$row['heading']) : $row['heading'];
                    $report_data .= '</td>';

                    for($month = 0; $month < 12; $month++) {
                        $dateObj = DateTime::createFromFormat('!m', $month+1);
                        $amt = $row[strtoupper($dateObj->format('M'))];
                        $report_data .= '<td data-title="'. $dateObj->format('F') .'" style="text-align:right; '. $table_row_style .'">$'. number_format($amt, 2, '.', ',') .'</td>';
                    }
                    $report_data .= '</tr>';
                    $odd_even2++;
                }

                $report_data .= '<tr style="'.$bg_class.'">
                    <td style="'. $table_row_style .'">Sales Tax Total</td>';
                    for($month = 0; $month < 12; $month++) {
                        $dateObj = DateTime::createFromFormat('!m', $month+1);
                        $report_data .= '<td data-title="'. $dateObj->format('F') .' Sales Tax" style="text-align:right; '. $table_row_style .'">$'. number_format($total_taxes[$month], 2, '.', ',') . '</td>';
                    }
                $report_data .= '</tr>
                <tr style="font-weight:bold;">
                    <td style="'. $table_row_style .'">Monthly Total</td>';
                    for($month = 0; $month < 12; $month++) {
                        $dateObj = DateTime::createFromFormat('!m', $month+1);
                        $report_data .= '<td data-title="'. $dateObj->format('F') .'" style="text-align:right; '. $table_row_style .'">$'. number_format($totals[$month], 2, '.', ',') . '</td>';
                    }
                $report_data .= '</tr>
                <tr style="font-size:1.5em; font-weight:bold;">
                    <td colspan="10" style="border-right:none;">Total Expenses for ';
                    $report_data .= ( $year == $startyear ) ? $search_start : $year.'-01-01';
                    $report_data .= ' to ';
                    $report_data .= ( $year == $endyear ) ? $search_end : $year.'-12-31';
                    $report_data .= '</td>
                    <td data-title="Total" colspan="3" style="text-align:right; border-left:none;">$'. number_format(array_sum($totals), 2, '.', ',') .'</td>
                </tr>
            </tbody>
        </table>';
        $odd_even++;
    }

    return $report_data;
}