<?php
/*
 * Profit & Loss: Revenue & Receivables Report
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
            $footer_text = 'Revenue & Receivables From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE: The report displays combined revenue &amp; receivables between two selected dates, broken out by Items.";
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
	$pdf->Output('Download/report_pnl_revenue_receivables_'.$today_date.'.pdf', 'F');

    track_download($dbc, 'report_pnl_revenue_receivables', 0, WEBSITE_URL.'/Reports/Download/report_pnl_revenue_receivables_'.$today_date.'.pdf', 'Revenue & Receivables Report');
    ?>

	<script type="text/javascript" language="Javascript">
        window.open('Download/report_pnl_revenue_receivables_<?= $today_date; ?>.pdf', 'fullscreen=yes');
	</script><?php

    $search_start  = $search_start_pdf;
    $search_end    = $search_end_pdf;
} ?>

            <div class="notice double-gap-bottom popover-examples">
                <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
                <div class="col-sm-11">
                    <span class="notice-name">NOTE:</span>
                    The report displays combined revenue &amp; receivables between two selected dates, broken out by Items.</div>
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
    $startyear  = intval(explode('-', $search_start)[0]);
    $endyear    = intval(explode('-', $search_end)[0]);

    //Create Temporary Table for Calculations
    $table_name = 'revenue_profit_loss';
    if ( !mysqli_query($dbc, "CREATE TEMPORARY TABLE IF NOT EXISTS `$table_name` (`invoice_date` VARCHAR(12), `type` VARCHAR(12), `heading` VARCHAR(200), `category` VARCHAR(200), `total` DECIMAL(10,2))") ) {
        echo mysqli_error($dbc);
    }

    //Load in the Point of Sale data
    mysqli_query ( $dbc, "INSERT INTO `$table_name` (`invoice_date`, `type`, `heading`, `category`, `total`) SELECT pos.`invoice_date`, posp.`type_category`,
        IFNULL(i.`category`, IFNULL(p.`category`, IFNULL(s.`category`, IFNULL(i.`name`, 'Other')))) heading,
        '' category,
        (posp.`quantity` * posp.`price`) total
        FROM `point_of_sell_product` posp LEFT JOIN `point_of_sell` pos ON posp.`posid`=pos.`posid`
            LEFT JOIN `products` p ON posp.`type_category`='product' AND posp.`inventoryid`=p.`productid`
            LEFT JOIN `inventory` i ON posp.`type_category`='inventory' AND posp.`inventoryid`=i.`inventoryid`
            LEFT JOIN `services` s ON posp.`type_category`='service' AND posp.`inventoryid`=s.`serviceid`
        WHERE `invoice_date` >= '$search_start' AND `invoice_date` <= '$search_end' AND pos.`status` IN ('Completed', 'Posted Past Due', 'Returns')
        ORDER BY IF(IFNULL(i.`category`, IFNULL(p.`category`, IFNULL(s.`category`, IFNULL(i.`name`, 'Other')))) = 'Other', 'ZZZ', IFNULL(i.`category`, IFNULL(p.`category`, IFNULL(s.`category`, IFNULL(i.`name`, 'Other'))))), `heading`");
    //Load in the Check Out Invoices
    mysqli_query($dbc, "INSERT INTO `$table_name` (`invoice_date`, `type`, `category`, `heading`, `total`)
        SELECT `invoice`.`invoice_date`, 'service', '', `invoice_patient`.`service_category`, `invoice_patient`.`sub_total`
        FROM `invoice_patient` LEFT JOIN `invoice` ON `invoice`.`invoiceid`=`invoice_patient`.`invoiceid`
        WHERE `invoice`.`invoice_date` >= '$search_start' AND `invoice`.`invoice_date` <= '$search_end' AND `invoice_patient`.`service_category` != ''");
    mysqli_query($dbc, "INSERT INTO `$table_name` (`invoice_date`, `type`, `category`, `heading`, `total`)
        SELECT `invoice`.`invoice_date`, 'service', '', `invoice_insurer`.`service_category`, `invoice_insurer`.`sub_total`
        FROM `invoice_insurer` LEFT JOIN `invoice` ON `invoice`.`invoiceid`=`invoice_insurer`.`invoiceid`
        WHERE `invoice`.`invoice_date` >= '$search_start' AND `invoice`.`invoice_date` <= '$search_end' AND `invoice_insurer`.`service_category` != ''");
    mysqli_query($dbc, "INSERT INTO `$table_name` (`invoice_date`, `type`, `category`, `heading`, `total`)
        SELECT `invoice`.`invoice_date`, 'inventory', '', IFNULL(`inventory`.`category`,'Miscellaneous'), MAX(`invoice_patient`.`sub_total`)
        FROM `invoice_patient` LEFT JOIN `invoice_lines` ON `invoice_patient`.`invoiceid`=`invoice_lines`.`invoiceid` AND `invoice_patient`.`product_name`=`invoice_lines`.`description`
            LEFT JOIN `inventory` ON `invoice_lines`.`item_id`=`inventory`.`inventoryid` OR (`invoice_lines`.`item_id` IS NULL AND `invoice_patient`.`product_name`=`inventory`.`name`)
            LEFT JOIN `invoice` ON `invoice`.`invoiceid`=`invoice_patient`.`invoiceid`
        WHERE `invoice`.`invoice_date` >= '$search_start' AND `invoice`.`invoice_date` <= '$search_end' AND `invoice_patient`.`service_category` = '' GROUP BY `invoice_patient`.`invoicepatientid`");
    mysqli_query($dbc, "INSERT INTO `$table_name` (`invoice_date`, `type`, `category`, `heading`, `total`)
        SELECT `invoice`.`invoice_date`, 'inventory', '', IFNULL(`inventory`.`category`,'Miscellaneous'), MAX(`invoice_insurer`.`sub_total`)
        FROM `invoice_insurer` LEFT JOIN `invoice_lines` ON `invoice_insurer`.`invoiceid`=`invoice_lines`.`invoiceid` AND `invoice_insurer`.`product_name`=`invoice_lines`.`description`
            LEFT JOIN `inventory` ON `invoice_lines`.`item_id`=`inventory`.`inventoryid` OR (`invoice_lines`.`item_id` IS NULL AND `invoice_insurer`.`product_name`=`inventory`.`name`)
            LEFT JOIN `invoice` ON `invoice`.`invoiceid`=`invoice_insurer`.`invoiceid`
        WHERE `invoice`.`invoice_date` >= '$search_start' AND `invoice`.`invoice_date` <= '$search_end' AND `invoice_insurer`.`service_category` = '' GROUP BY `invoice_insurer`.`invoiceinsurerid`");

    for ($year = $startyear; $year <= $endyear; $year++) {
        //Pull revenue amounts by category
        $total_rows = "SELECT COUNT(DISTINCT CONCAT(`type`,`category`,`heading`)) numrows FROM `$table_name`";
        $months = mysqli_fetch_array(mysqli_query($dbc, "SELECT `type`, `heading`, `category`,
            SUM(IF(`invoice_date` LIKE '".$year."-01%', `total`, 0)) `JAN`,
            SUM(IF(`invoice_date` LIKE '".$year."-02%', `total`, 0)) `FEB`,
            SUM(IF(`invoice_date` LIKE '".$year."-03%', `total`, 0)) `MAR`,
            SUM(IF(`invoice_date` LIKE '".$year."-04%', `total`, 0)) `APR`,
            SUM(IF(`invoice_date` LIKE '".$year."-05%', `total`, 0)) `MAY`,
            SUM(IF(`invoice_date` LIKE '".$year."-06%', `total`, 0)) `JUN`,
            SUM(IF(`invoice_date` LIKE '".$year."-07%', `total`, 0)) `JUL`,
            SUM(IF(`invoice_date` LIKE '".$year."-08%', `total`, 0)) `AUG`,
            SUM(IF(`invoice_date` LIKE '".$year."-09%', `total`, 0)) `SEP`,
            SUM(IF(`invoice_date` LIKE '".$year."-10%', `total`, 0)) `OCT`,
            SUM(IF(`invoice_date` LIKE '".$year."-11%', `total`, 0)) `NOV`,
            SUM(IF(`invoice_date` LIKE '".$year."-12%', `total`, 0)) `DEC`
            FROM `$table_name`"));
        $totals = [ $months['JAN'], $months['FEB'], $months['MAR'], $months['APR'], $months['MAY'], $months['JUN'], $months['JUL'], $months['AUG'], $months['SEP'], $months['OCT'], $months['NOV'], $months['DEC'] ];
        $revenue_sql = "SELECT LOWER(`type`) type, `heading`, `category`,
            SUM(IF(`invoice_date` LIKE '".$year."-01%', `total`, 0)) `JAN`,
            SUM(IF(`invoice_date` LIKE '".$year."-02%', `total`, 0)) `FEB`,
            SUM(IF(`invoice_date` LIKE '".$year."-03%', `total`, 0)) `MAR`,
            SUM(IF(`invoice_date` LIKE '".$year."-04%', `total`, 0)) `APR`,
            SUM(IF(`invoice_date` LIKE '".$year."-05%', `total`, 0)) `MAY`,
            SUM(IF(`invoice_date` LIKE '".$year."-06%', `total`, 0)) `JUN`,
            SUM(IF(`invoice_date` LIKE '".$year."-07%', `total`, 0)) `JUL`,
            SUM(IF(`invoice_date` LIKE '".$year."-08%', `total`, 0)) `AUG`,
            SUM(IF(`invoice_date` LIKE '".$year."-09%', `total`, 0)) `SEP`,
            SUM(IF(`invoice_date` LIKE '".$year."-10%', `total`, 0)) `OCT`,
            SUM(IF(`invoice_date` LIKE '".$year."-11%', `total`, 0)) `NOV`,
            SUM(IF(`invoice_date` LIKE '".$year."-12%', `total`, 0)) `DEC`
            FROM `$table_name`
            GROUP BY CONCAT(`type`,`category`,`heading`)
            ORDER BY `type`,`category`,`heading`";
        $revenues = mysqli_query($dbc, $revenue_sql);

        $report_data = '
            <table class="table table-bordered" style="'. $table_style .'">
                <thead>
                    <tr class="hidden-xs hidden-sm">
                        <th>Items</th>';
                        for ($month = 0; $month < 12; $month++) {
                            $dateObj = DateTime::createFromFormat('!m', $month+1);
                            $report_data .= '<th style="width:8em; '. $table_row_style .'">'. $dateObj->format('F') .'</th>';
                        }
                    $report_data .= '</tr>
                </thead>
                <tbody>';
                    $tile_name  = '';
                    $category   = '';
                    $startmonth = ($startyear == $year ? intval(explode('-', $search_start)[1]) - 1 : 0);
                    $endmonth   = ($endyear == $year ? intval(explode('-', $search_end)[1]) - 1 : 11);

                    $odd_even = 0;
                    while($row = mysqli_fetch_array($revenues)) {
                        $bg_class = $odd_even % 2 == 0 ? '' : 'background-color:#e6e6e6;';
                        if($tile_name != $row['type']) {
                            $tile_name = $row['type'];
                            $report_data .= '<tr><td colspan="13" style="font-size:1.1em; font-weight:bold; '. $table_row_style .'">'. strtoupper($tile_name) .'</td></tr>';
                        }
                        if($category != $row['category']) {
                            $category = $row['category'];
                            $report_data .= '<tr><td colspan="13" style="padding-left:1em; font-size:1.1em; font-weight:bold; '. $table_row_style .'">'. $category .'</td></tr>';
                        }
                        $report_data .= '<tr style="'.$bg_class.'"><td style="padding-left:1em; '. $table_row_style .'" data-title="Revenue Item">'. $row['heading'] .'</td>';
                        for($month = 0; $month < 12; $month++) {
                            $dateObj = DateTime::createFromFormat('!m', $month+1);
                            $amt = $row[strtoupper($dateObj->format('M'))];
                            $report_data .= '<td data-title="'. $dateObj->format('F') .'" style="text-align:right; '. $table_row_style .'">&nbsp;&nbsp;';
                            $report_data .= ($month < $startmonth || $month > $endmonth) ? '-' : '$'. number_format($amt, 2, '.', ',');
                            $report_data .= '</td>';
                        }
                        $report_data .= '</tr>';
                        $odd_even++;
                    }

                    $report_data .= '<tr style="font-weight:bold;">
                        <td style="'. $table_row_style .'">Monthly Total</td>';
                        for($month = 0; $month < 12; $month++) {
                            $dateObj = DateTime::createFromFormat('!m', $month+1);
                            $report_data .= '<td data-title="'. $dateObj->format('F') .'" style="text-align:right; '. $table_row_style .'">$'. number_format($totals[$month], 2, '.', ',') . '</td>';
                        }
                    $report_data .= '</tr>

                    <tr style="font-size:1.5em; font-weight:bold;">
                        <td colspan="10" style="border-right:none;">Total Revenue for ';
                            $report_data .= ($year == $startyear) ? $search_start : $year . '-01-01';
                            $report_data .= ' to ';
                            $report_data .= ($year == $endyear) ? $search_end : $year . '-12-31';
                            $report_data .= '</td>
                        <td data-title="Total" colspan="3" style="text-align:right; border-left:none;">$'. number_format(array_sum($totals), 2, '.', ',') . '</td>
                    </tr>
                </tbody>
            </table>';
    }

    return $report_data;

    mysqli_query($dbc, "DROP TEMPORARY TABLE IF EXISTS `$table_name`");
}