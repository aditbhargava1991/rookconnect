<?php
/*
 * Profit & Loss: Staff Compensation Report
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
            $footer_text = 'Staff Compensation From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE: The report displays compensation per staff between two selected dates.";
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

    $html .= report_pnl_display($dbc, $search_start_pdf, $search_end_pdf, 'padding:3px; border:1px solid black;', 'border:1px solid black;', 'border:1px solid black;');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/report_pnl_staff_compensation_'.$today_date.'.pdf', 'F');

    track_download($dbc, 'report_pnl_staff_compensation', 0, WEBSITE_URL.'/Reports/Download/report_pnl_staff_compensation_'.$today_date.'.pdf', 'Staff Compensation Report');

    ?>

	<script type="text/javascript" language="Javascript">
        window.open('Download/report_pnl_staff_compensation_<?= $today_date; ?>.pdf', 'fullscreen=yes');
	</script><?php

    $search_start  = $search_start_pdf;
    $search_end    = $search_end_pdf;
} ?>
            <div class="notice double-gap-bottom popover-examples">
                <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
                <div class="col-sm-11">
                    <span class="notice-name">NOTE:</span>
                    The report displays compensation per staff between two selected dates.</div>
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
    include_once('compensation_function.php');

    $startyear = intval(explode('-', $search_start)[0]);
    $endyear   = intval(explode('-', $search_end)[0]);

    $staff = mysqli_query ( $dbc, "SELECT `contactid`, `category`, `name`, `last_name`, `first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1" );
    $contactlist = sort_contacts_array(mysqli_fetch_all($staff, MYSQLI_ASSOC));

    for ($year = $startyear; $year <= $endyear; $year++) {
		$stat_holidays = [];
		foreach(mysqli_fetch_all(mysqli_query($dbc, "SELECT `date` FROM `holidays` WHERE `paid`=1 AND `deleted`=0")) as $stat_day) {
			$stat_holidays[] = $stat_day[0];
		}
		$stat_holidays = implode(',', $stat_holidays);
		//$stat_holidays = explode(',',get_config($dbc, 'stat_holiday'));
        $totals = [0,0,0,0,0,0,0,0,0,0,0,0];

        $report_data2 = '<table width="100%" class="table table-bordered" style="'. $table_style .'">
            <thead>
                <tr class="hidden-xs hidden-sm">
                    <th width="10%" style="'. $table_row_style .'">Staff Member</th>';
                    for($month = 0; $month < 12; $month++) {
                        $dateObj = DateTime::createFromFormat('!m', $month+1);
                        $report_data2 .= '<th width="7.5%" style="vertical-align:middle; '. $table_row_style .'">'. $dateObj->format('F') .'</th>';
                    }
                $report_data2 .= '</tr>
            </thead>
            <tbody>';
                $odd_even = 0;
                foreach($contactlist as $contactid) {
                    $bg_class = $odd_even % 2 == 0 ? '' : 'background-color:#e6e6e6;';
                    $report_data2 .= '<tr style="'.$bg_class.'"><td width="10%" data-title="Staff" style="'. $table_row_style .'">'. get_contact($dbc, $contactid) .'</td>';
                    $startmonth   = 0;
                    $endmonth     = 12;

                    if($startyear == $year) {
                        $startmonth = intval(explode('-', $search_start)[1]) - 1;
                    }
                    if($endyear == $year) {
                        $endmonth = intval(explode('-', $search_end)[1]);
                    }

                    for($month = 0; $month < $startmonth; $month++) {
                        $dateObj = DateTime::createFromFormat('!m', $month+1);
                        $report_data2 .= '<td width="7.5%" data-title="'. $dateObj->format('F') .'" style="text-align:right; '. $table_row_style .'">-</td>';
                    }

                    for($month = $startmonth; $month < $endmonth; $month++) {
                        $dateObj = DateTime::createFromFormat('!m', $month+1);
                        $date_part = $year."-".$dateObj->format('m');
                        $starttimemonth = $date_part.'-01';
                        if($startyear == $year && $month == $startmonth) {
                            $starttimemonth = $search_start;
                        }
                        //$total_stat_holiday = $stat_days[$month];
                        $endtimemonth = date('Y-m-t',strtotime($starttimemonth));
                        if($endyear == $year && $month == $endmonth) {
                            $endtimemonth = $search_end;
                        }

                        $amt = 0;
                        $all_booking = 0;
                        $vacation_pay_perc = 0;
                        $vacation_pay = 0;
                        $grand_stat_total = 0;
                        $avg_per_day_stat = 0;

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
                                    $weekday  = date('w',$stat_day);
                                    if($schedule == '' || in_array(date('w',$stat_day), explode(',',$schedule))) {
                                        $stat_start = date('Y-m-d',strtotime('-63 day',$stat_day));
                                        $stat_end   = date('Y-m-d',strtotime('-1 day',$stat_day));
                                        include('report_compensation_stat_holiday.php');
                                    }
                                }
                            }

                            $vacation_pay = (($total_base_service+$total_base_inv)*$vacation_pay_perc)/100;
                            $amt = $total_base_service+$total_base_inv+$avg_per_day_stat+$vacation_pay;
                            $totals[$month] += $amt;
                        }
                        $report_data2 .= '<td data-title="'. $dateObj->format('F') .'" style="text-align:right; '. $table_row_style .'">$'. number_format($amt, 2, '.', ',') .'</td>';
                    }

                    for($month = $endmonth; $month < 12; $month++) {
                        $dateObj = DateTime::createFromFormat('!m', $month+1);
                        $report_data2 .= '<td data-title="'. $dateObj->format('F') .'" style="text-align:right; '. $table_row_style .'">-</td>';
                    }
                    $report_data2 .= '</tr>';
                    $odd_even++;
                }

                $report_data2 .= '<tr style="font-size:1.25em; font-weight:bold;">
                    <td width="10%" style="'. $table_row_style .'">Monthly Total</td>';
                    for($month = 0; $month < 12; $month++) {
                        $dateObj = DateTime::createFromFormat('!m', $month+1);
                        $report_data2 .= '<td width="7.5%" data-title="'. $dateObj->format('F') .'" style="text-align:right; '.$table_row_style.'">$'. number_format($totals[$month], 2, '.', ',') . '</td>';
                    }
                $report_data2 .= '</tr>
                <tr style="font-size:1.5em; font-weight:bold; '.$grand_total_style.'">
                    <td width="77.5%" colspan="10" style="border-right: none;">Total Compensation for ';
                    $report_data2 .= ($year == $startyear) ? $starttime : $year.'-01-01';
                    $report_data2 .= ' to ';
                    $report_data2 .= ($year == $endyear ) ? $endtime : $year.'-12-31';
                    $report_data2 .= '</td>
                    <td width="22.5%" data-title="Total" colspan="3" style="text-align:right; border-left:none;">$';
                    $report_data2 .= number_format(array_sum($totals), 2, '.', ',');
                    $report_data2 .= '</td>
                </tr>
            </tbody>
        </table>';
    }

    return $report_data2;
}