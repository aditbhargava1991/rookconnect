<?php // Statutory Holiday Pay Breakdown
include ('../include.php');
checkAuthorised('report');
include ('../tcpdf/tcpdf.php');
error_reporting(0);
if(isset($_POST['printpdf'])) {

    $starttimepdf = $_POST['startpdf'];
    $endtimepdf = $_POST['endpdf'];
    $contactidpdf = $_POST['contactidpdf'];

    DEFINE('START_DATE', $starttimepdf);
    DEFINE('END_DATE', $endtimepdf);
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
            $footer_text = 'Statutory Holiday Compensation Breakdown From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : Displays the details used to calculate statutory holiday compensation for the selected date range. These reports can be given to each Staff to provide a detailed breakdown of their compensation by date range.";
            $this->writeHTMLCell(0, 0, 10 , 45, $footer_text, 0, 0, false, "R", true);
		}

		// Page footer
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

    $html .= report_statutory_breakdown($dbc, $contactidpdf, 'padding:3px; border:1px solid black;', '', '', $starttimepdf, $endtimepdf);

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/stat_holiday_breakdown_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_stat_holiday_pay', 0, WEBSITE_URL.'/Reports/Download/stat_holiday_breakdown_'.$today_date.'.pdf', 'Statutory Holiday Compensation Breakdown Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/stat_holiday_breakdown_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $contactid = $contactidpdf;
} ?>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php $contactid = $_SESSION['contactid'];
			if($_GET['contactid'] > 0) {
				$contactid = $_GET['contactid'];
			}
			if($_GET['start'] != '') {
				$starttime = $_GET['start'];
			}
			if($_GET['end'] != '') {
				$endtime = $_GET['end'];
			}

            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
                $contactid = $_POST['contactid'];
            }

            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-01');
            }

            if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            } ?>
            <center><div class="form-group">
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Staff:</label>
					<div class="col-sm-8">
						<select data-placeholder="Pick a Type" name="contactid" class="chosen-select-deselect form-control1" style="width:10%;" width="380">
							<option value="">Display All</option>
							<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT distinct(contactid), first_name, last_name FROM contacts WHERE category='staff' AND role != 'super' AND first_name != '' AND deleted=0 AND status=1"),MYSQLI_ASSOC));
							foreach($query as $rowid) { ?>
								<option <?= $rowid == $contactid ? "selected" : '' ?> value='<?= $rowid ?>' ><?= get_staff($dbc, $rowid) ?></option>
							<?php } ?>
						</select>
					</div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">From:</label>
					<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Until:</label>
					<div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
				</div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="startpdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endpdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="contactidpdf" value="<?php echo $contactid; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php echo report_statutory_breakdown($dbc, $contactid, '', '', '', $starttime, $endtime); ?>

        </form>

<?php function report_statutory_breakdown($dbc, $contactid, $table_style, $table_row_style, $grand_total_style, $starttime, $endtime) {
	$report_data = '';
	$stat_holidays = mysqli_query($dbc, "SELECT `name`, `date` FROM `holidays` WHERE `paid`=1 AND `deleted`=0 AND `date` BETWEEN '$starttime' AND '$endtime'");
	while($holiday = mysqli_fetch_array($stat_holidays)) {
		$stat_total = 0;
		$performance_bonus = 0;
		$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts`.contactid, `contacts`.scheduled_hours, `contacts`.`schedule_days`, `contacts`.category_contact, IFNULL(`base_pay`,'0*#*0') base_pay FROM contacts LEFT JOIN `compensation` ON `contacts`.`contactid`=`compensation`.`contactid` AND '$starttime' BETWEEN `compensation`.`start_date` AND `compensation`.`end_date` WHERE `contacts`.contactid='$contactid'"));
        $schedule = $row['schedule_days'];
        $base_pay = explode('*#*',$row['base_pay']);

		if($schedule == '' || in_array(date('w',$stat_day), explode(',',$schedule))) {
			$stat_start = date('Y-m-d',strtotime('-63 day',strtotime($holiday['date'])));
			$stat_end = date('Y-m-d',strtotime('-1 day',strtotime($holiday['date'])));
			$report_data .= "<h3>Pay Breakdown for {$holiday['name']}</h3>";
			$report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
			$report_data .= '<tr style="'.$table_row_style.'">
			<th width="40%">Service / Inventory</th>
			<th width="5%">Sale Amount</th>
			<th width="5%">Admin Fee</th>
			<th width="10%">Final Amount</th>
			<th width="10%">Compensation Rate</th>
			<th width="5%">Compensation Amount</th>
			<th width="5%">Quantity</th>
			<th width="10%">Final Compensation</th>
			<th width="10%">Performance Bonus</th>
			</tr>';

			$service_list = mysqli_query($dbc, "SELECT `item_id`, `fee`, `admin_fee`, COUNT(*) `total_appt` FROM `invoice_compensation` WHERE `contactid`='$contactid' AND `service_date` BETWEEN '$stat_start' AND '$stat_end' AND `item_type`='services' GROUP BY `item_id`, `fee`, `admin_fee`");
			while($service_row = mysqli_fetch_array($service_list)) {
				// Base Pay
				$current_serviceid = $service_row['item_id'];
				$current_service_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid`='$current_serviceid'"));
				$current_fee = $service_row['fee'];
				$current_admin = $service_row['admin_fee'];
				$current_final = $current_fee - $current_admin;
				$total_appt = $service_row['total_appt'];

				$current_comp = $base_pay[0]/100 * $current_final;
				$current_total = $current_comp * $total_appt;
				$stat_total += $current_total;

				// Base Pay Output
				$report_data .= "<tr><td>{$current_service_info['category']} - {$current_service_info['heading']}</td>
				<td>$".number_format($current_fee,2)."</td>
				<td>$".number_format($current_admin,2)."</td>
				<td>$".number_format($current_final,2)."</td>
				<td>{$base_pay[0]}: X ".($base_pay[0] / 100)."</td>
				<td>$".number_format($current_comp,2)."</td>
				<td>$total_appt</td>
				<td>$".number_format($current_total,2)."</td>";

				// Performance Bonus
				if($current_serviceid != '42' && $current_serviceid != '43' && $current_serviceid != '45') {
					$sp1 = $sp2 = $sp3 = $sp4 = $sp5 = 0;
					if($arr_target <= $arr_actual_value) {
						$sp1 = ($final_fee*($arr_perc/100)) * $total_appt;
					}

					if($avg_hours_sch_target <= $avg_hours_sch_actual_value) {
						$sp2 = ($final_fee*($avg_hours_sch_perc/100)) * $total_appt;
					}


					if($test_actual_value != 0) {
						$sp3 = ($final_fee*($test_perc/100)) * $total_appt;
					}

					if($inter_actual_value != 0) {
						$sp4 = ($final_fee*($inter_perc/100)) * $total_appt;
					}

					if($adv_actual_value != 0) {
						$sp5 = ($final_fee*($adv_perc/100)) * $total_appt;
					}
					$performance_bonus += $sp1 + $sp2 + $sp3 + $sp4 + $sp5;
				}
				$report_data .= "<td>$".number_format($sp1 + $sp2 + $sp3 + $sp4 + $sp5,2)."</td></tr>";
			}

			// Inventory Stat
			$comp_inv_list = mysqli_fetch_array(mysqli_query($dbc, "SELECT group_concat(`inventoryid` separator ',') as `inventoryids`, group_concat(`quantity` separator ',') as `qtys`, group_concat(`sell_price` separator ',') as `total_prices` FROM invoice WHERE therapistsid='$contactid' AND (service_date >= '".$stat_start."' AND service_date <= '".$stat_end."') AND `invoice_type` IN ('New','Refund','Adjustment')"));
			$inventory_list = [];
			foreach(explode(',',$comp_inv_list['inventoryids']) as $key => $current_inventoryid) {
				if(!empty(explode(',',$comp_inv_list['total_prices'])[$key])) {
					$description = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `inventory` WHERE `inventoryid`='$current_inventoryid'"))['name'];
					$total_price = explode(',',$comp_inv_list['total_prices'])[$key];
					$qty = explode(',',$comp_inv_list['qtys'])[$key];
					$report_data .= "<tr><td>$description</td>
					<td>$".number_format($total_price,2)."</td>
					<td></td>
					<td>$".number_format($total_price,2)."</td>
					<td>{$base_pay[1]}: X ".($base_pay[1] / 100)."</td>
					<td>$".number_format(round($total_price * $base_pay[1] / $qty) / 100,2)."</td>
					<td>$qty</td>
					<td>$".number_format(round($total_price * $base_pay[1]) / 100,2)."</td>
					<td>$0.00</td></tr>";
					$stat_total += round($total_price * $base_pay[1]) / 100;
				}
			}

			$active_days = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(DISTINCT((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')))) AS total_active FROM booking WHERE therapistsid = '$contactid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$stat_start."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$stat_end."')"));
			$report_data .= "<tr><td colspan=\"7\">Total Compensation for Period:</td><td>$".number_format($stat_total,2)."</td><td>$".number_format($performance_bonus,2)."</td></tr>
			</table>
			<p>Number of days worked during statutory period: {$active_days['total_active']}<br />\n
			Statutory Holiday Pay for {$holiday['name']} (Average pay for Period): $".number_format(($stat_total + $performance_bonus) / $active_days['total_active'], 2)."</p>\n";
		}
	}

    return $report_data;
} ?>