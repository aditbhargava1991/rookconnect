<?php
/*
 * Profit & Loss:Summary
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
                $this->Image($image_file, 10, 10, '', 20, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);

            $this->SetFont('helvetica', '', 13);
            $footer_text = 'Summary From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE:The report displays a summary of Profit & Loss between two selected dates. It is an overview of every other report listed previously for the selected date range.";
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
	$pdf->Output('Download/report_pnl_summary_'.$today_date.'.pdf', 'F');

    track_download($dbc, 'report_pnl_summary', 0, WEBSITE_URL.'/Reports/Download/report_pnl_summary_'.$today_date.'.pdf', 'Summary Report');

    ?>

	<script type="text/javascript" language="Javascript">
        window.open('Download/report_pnl_summary_<?= $today_date; ?>.pdf', 'fullscreen=yes');
	</script><?php

    $search_start  = $search_start_pdf;
    $search_end    = $search_end_pdf;
} ?>

            <div class="notice double-gap-bottom popover-examples">
                <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
                <div class="col-sm-11">
                    <span class="notice-name">NOTE:</span>
                    The report displays a summary of Profit &amp; Loss between two selected dates. It is an overview of every other report listed previously for the selected date range.</div>
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

    //Calculate Revenues
    //Create Temporary Table for Calculations
    $table_name = 'summary_profit_loss';
    if(!mysqli_query($dbc, "CREATE TEMPORARY TABLE IF NOT EXISTS `$table_name` (`status` VARCHAR(40), `invoice_date` VARCHAR(12), `type` VARCHAR(12), `heading` VARCHAR(200), `category` VARCHAR(200), `total` DECIMAL(10,2))")) {
        echo mysqli_error($dbc);
    }

    //Load in the Point of Sale data
    mysqli_query($dbc, "INSERT INTO `$table_name` (`status`, `invoice_date`, `type`, `heading`, `category`, `total`) SELECT pos.`status`, pos.`invoice_date`, posp.`type_category`,
        IFNULL(i.`name`, IFNULL(p.`heading`, IFNULL(s.`heading`, IFNULL(`misc_product`, 'Other')))) heading,
        IFNULL(i.`category`, IFNULL(p.`category`, IFNULL(s.`category`, IFNULL(i.`name`, 'Other')))) category,
        (posp.`quantity` * posp.`price`) total
        FROM `point_of_sell_product` posp LEFT JOIN `point_of_sell` pos ON posp.`posid`=pos.`posid`
            LEFT JOIN `products` p ON posp.`type_category`='product' AND posp.`inventoryid`=p.`productid`
            LEFT JOIN `inventory` i ON posp.`type_category`='inventory' AND posp.`inventoryid`=i.`inventoryid`
            LEFT JOIN `services` s ON posp.`type_category`='service' AND posp.`inventoryid`=s.`serviceid`
        WHERE `invoice_date` >= '$search_start' AND `invoice_date` <= '$search_end' AND pos.`status` NOT IN ('Voided')
        ORDER BY IF(IFNULL(i.`category`, IFNULL(p.`category`, IFNULL(s.`category`, IFNULL(i.`name`, 'Other')))) = 'Other', 'ZZZ', IFNULL(i.`category`, IFNULL(p.`category`, IFNULL(s.`category`, IFNULL(i.`name`, 'Other'))))), `heading`");
    //Load in the Check Out Invoices
    mysqli_query($dbc, "INSERT INTO `$table_name` (`status`, `invoice_date`, `type`, `heading`, `category`, `total`)
        SELECT IF(`invoice_patient`.`paid`!='On Account' AND `invoice_patient`.`paid`!='' AND `invoice_patient`.`paid` IS NOT NULL,'Completed','Unpaid'), `invoice`.`invoice_date`, '', IF(`invoice_patient`.`service_category`='','Inventory',`invoice_patient`.`service_category`), IF(`invoice_patient`.`service_name`='',`invoice_patient`.`product_name`,`invoice_patient`.`service_name`), `sub_total`
        FROM `invoice_patient` LEFT JOIN `invoice` ON `invoice`.`invoiceid`=`invoice_patient`.`invoiceid`
        WHERE `invoice`.`invoice_date` >= '$search_start' AND `invoice`.`invoice_date` <= '$search_end'");
    mysqli_query($dbc, "INSERT INTO `$table_name` (`status`, `invoice_date`, `type`, `heading`, `category`, `total`)
        SELECT IF(`invoice_insurer`.`paid`='Yes','Completed','Unpaid'), `invoice`.`invoice_date`, '', IF(`invoice_insurer`.`service_category`='','Inventory',`invoice_insurer`.`service_category`), IF(`invoice_insurer`.`service_name`='',`invoice_insurer`.`product_name`,`invoice_insurer`.`service_name`), `sub_total`
        FROM `invoice_insurer` LEFT JOIN `invoice` ON `invoice`.`invoiceid`=`invoice_insurer`.`invoiceid`
        WHERE `invoice`.`invoice_date` >= '$search_start' AND `invoice`.`invoice_date` <= '$search_end'");

    $total_revenue = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`total`) `total`
        FROM `$table_name` WHERE `invoice_date` >= '$search_start' AND `invoice_date` <= '$search_end' AND `status` IN ('Completed')"))['total'];
    $total_receivable = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`total`) `total`
        FROM `$table_name` WHERE `invoice_date` >= '$search_start' AND `invoice_date` <= '$search_end' AND `status` NOT IN ('Completed')"))['total'];
    mysqli_query($dbc, "DROP TEMPORARY TABLE IF EXISTS `$table_name`");

    //Calculate Staff Compensation
    include_once('compensation_function.php');
    $total_compensation = 0;
    $staff_sql = mysqli_query($dbc, "SELECT `contactid`, `category`, `name`, `last_name`, `first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`=1 AND `deleted`=0");

	$stat_holidays = [];
	foreach(mysqli_fetch_all(mysqli_query($dbc, "SELECT `date` FROM `holidays` WHERE `paid`=1 AND `deleted`=0")) as $stat_day) {
		$stat_holidays[] = $stat_day[0];
	}
	$stat_holidays = implode(',', $stat_holidays);
	//$stat_holidays = explode(',',get_config($dbc, 'stat_holiday'));
	$inv_cost_field = get_config($dbc,'inventory_cost');
    while($contactlist = mysqli_fetch_array($staff_sql)) {
        $contactid = $contactlist['contactid'];
        for ($year = $startyear; $year <= $endyear; $year++) {
            $startmonth = 0;
            $endmonth = 12;

            if($startyear == $year) {
                $startmonth = intval(explode('-', $search_start)[1]) - 1;
            }
            if($endyear == $year) {
                $endmonth = intval(explode('-', $search_end)[1]);
            }

            for($month = $startmonth; $month < $endmonth; $month++) {
                $dateObj = DateTime::createFromFormat('!m', $month+1);
                $date_part = $year."-".$dateObj->format('m');
                $starttimemonth = $date_part.'-01';

                if($startyear == $year && $month == $startmonth) {
                    $starttimemonth = $search_start;
                }

                $total_stat_holiday = $stat_days[$month];
                $endtimemonth = date('Y-m-t',strtotime($starttimemonth));

                if($endyear == $year && $month == $endmonth) {
                    $endtimemonth = $search_end;
                }

                $stat_start = $stat_starts[$month];
                $stat_end = $stat_ends[$month];
                $amt = 0;
                $all_booking = 0;
                $vacation_pay_perc = 0;
                $vacation_pay = 0;
                $grand_stat_total = 0;
                $avg_per_day_stat = 0;
                $therapistid = $contactid;

                if(strtotime($starttimemonth) <= strtotime('today')) {
                    $starttime = $starttimemonth;
                    $endtime = $endtimemonth;
                    $invoicetype = "'New','Refund','Adjustment'";

                    //$table_style = $table_row_style = $grand_total_style = '';
                    $row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts`.contactid, `contacts`.scheduled_hours, `contacts`.`schedule_days`, `contacts`.category_contact, IFNULL(`base_pay`,'0*#*0') base_pay FROM contacts LEFT JOIN `compensation` ON `contacts`.`contactid`=`compensation`.`contactid` AND '$starttime' BETWEEN `compensation`.`start_date` AND `compensation`.`end_date` WHERE `contacts`.contactid='$contactid'"));
                    $therapistid = $row['contactid'];
                    $category_contact = $row['category_contact'];
                    $schedule = $row['schedule_days'];
                    $base_pay = explode('*#*',$row['base_pay']);

                    include ('report_compensation_services.php');
                    include ('report_compensation_preformance_logic.php');
                    include ('report_compensation_inventory.php');

                    foreach(explode(',',$stat_holidays) as $stat_day) {
                        if($stat_day >= $starttime && $stat_day <= $endtime) {;
                            $stat_day = strtotime($stat_day);
                            $weekday = date('w',$stat_day);
                            if($schedule == '' || in_array(date('w',$stat_day), explode(',',$schedule))) {
                                $stat_start = date('Y-m-d',strtotime('-63 day',$stat_day));
                                $stat_end = date('Y-m-d',strtotime('-1 day',$stat_day));
                                include('report_compensation_stat_holiday.php');
                            }
                        }
                    }

                    $vacation_pay = (($total_base_service+$total_base_inv)*$vacation_pay_perc)/100;
                    $total_compensation += $total_base_service+$total_base_inv+$avg_per_day_stat+$vacation_pay;
                }
            }
        }
    }

    $total_costs = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`$inv_cost_field`) * rs.`quantity` cost
        FROM `receive_shipment` rs LEFT JOIN `inventory` i ON rs.`inventoryid`=i.`inventoryid` WHERE `date_added` >= '$search_start' AND `date_added` <= '$search_end'"))['cost'];
    $total_expense = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(total) `total` FROM (SELECT contacts.contactid, expense.title heading, ex_date expense_date, gst tax, amount total
        FROM expense LEFT JOIN contacts ON contacts.category IN (".STAFF_CATS.") AND IFNULL(`contacts`.`staff_category`,'') NOT IN (".STAFF_CATS_HIDE.") AND expense.staff=CONCAT(contacts.first_name,' ',contacts.last_name) UNION
        SELECT submit_staff, expense_heading heading, expense_date, tax, actual_amount total FROM budget_expense) expenses WHERE expense_date >= '$search_start' AND expense_date <= '$search_end'"))['total'];
    $total = $total_revenue + $total_receivable - $total_compensation - $total_expense - $total_costs;

    $starttime_summary = $search_start;
    if(!empty($_POST['search_start'])) {
        $starttime_summary = $_POST['search_start'];
    } else if (!empty($_GET['search_start'])) {
        $starttime_summary = $_GET['search_start'];
    }

    $endtime_summary = $search_end;
    if(!empty($_POST['search_end'])) {
        $endtime_summary = $_POST['search_end'];
    } else if (!empty($_GET['search_end'])) {
        $endtime_summary = $_GET['search_end'];
    }

    $report_data2 = '<table class="table table-bordered" style="'. $table_style .'">
        <thead>
            <tr class="hidden-xs hidden-sm">
                <th style="'. $table_row_style .'"></th>
                <th style="'. $table_row_style .'">Profit / Loss</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td data-title="" style="'. $table_row_style .'">Total Revenue:</td>
                <td data-title="Total Revenue" style="text-align:right; '. $table_row_style .'">$'. number_format($total_revenue, 2, '.', ',') .'</td>
            </tr>
            <tr>
                <td data-title="" style="background-color:#e6e6e6; '. $table_row_style .'">Total Receivables:</td>
                <td data-title="Total Receivables" style="background-color:#e6e6e6; text-align:right; '. $table_row_style .'">$'. number_format($total_receivable, 2, '.', ',') .'</td>
            </tr>
            <tr>
                <td data-title="" style="'. $table_row_style .'">Total Compensation:</td>
                <td data-title="Total Compensation" style="text-align:right; '. $table_row_style .'">$'. number_format($total_compensation, 2, '.', ',') .'</td>
            </tr>
            <tr>
                <td data-title="" style="background-color:#e6e6e6; '. $table_row_style .'">Total Expenses:</td>
                <td data-title="Total Expenses" style="background-color:#e6e6e6; text-align:right; '. $table_row_style .'">$'. number_format($total_expense, 2, '.', ',') .'</td>
            </tr>
            <tr>
                <td data-title="" style="'. $table_row_style .'">Total Costs:</td>
                <td data-title="Total Costs" style="text-align:right; '. $table_row_style .'">$'. number_format($total_costs, 2, '.', ',') .'</td>
            </tr>
            <tr style="font-size:1.5em; font-weight:bold;">
                <td style="'. $table_row_style .'">Summary Profit / Loss from '. $starttime_summary .' to '. $endtime_summary .'</td>
                <td data-title="Summary Profit / Loss" style="text-align:right; '. $table_row_style .'">$'. number_format($total, 2, '.', ',') .'</td>
            </tr>
        </tbody>
    </table>';

    return $report_data2;
}