<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['printpdf'])) {
    $starttimepdf = $_POST['starttimepdf'];
    $endtimepdf = $_POST['endtimepdf'];

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
                $this->Image($image_file, 10, 10, 80, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);

            $this->SetFont('helvetica', '', 13);
            $footer_text = 'Rate Cards Report From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
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

    $html .= display_report($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/sales_summary_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_operations_rate_cards', 0, WEBSITE_URL.'/Reports/Download/sales_summary_'.$today_date.'.pdf', 'Rate Cards Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/sales_summary_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    } ?>

		<form name="form_sites" method="post" action="" class="form-inline" role="form">
		<?php
		if (isset($_POST['search_submit'])) {
			$search_month = substr($_POST['search_month'],0,7);
			if($search_month == 0000-00-00) {
				$search_month = date('Y-m');
			}
		} else {
			$search_month = date('Y-m');
		}
		?>
		<br>
		<center><div class="form-group col-sm-12">
			<div class="form-group col-sm-5">
				<label class="col-sm-4">Display Month:</label>
				<div class="col-sm-8"><input name="search_month" type="text" class="datepicker form-control" value="<?php echo $search_month; ?>"></div>
			</div>
			<div class="form-group col-xm-2">
				<div style="display:inline-block; padding: 0 0.5em;">
					<button type="submit" name="search_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
				</div>
				<div style="display:inline-block; padding: 0 0.5em;">
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click to refresh the page and see the current month for all staff."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<button type="submit" name="display_all" value="Display All" class="btn brand-btn mobile-block">Current Month</button>
				</div>
			</div><!-- .form-group -->
			<div class="clearfix"></div>
		</div></center>

            <?php
                //echo '<a href="report_referral.php?referral=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a></h4><br>';

                echo display_report($dbc, $search_month, '', '', '');
            ?>
        </form>
        
<?php
function display_report($dbc, $search_month, $table_style, $table_row_style, $grand_total_style) {
	$rate_sql = "SELECT 'position' `type`, `positions`.`name`, `positions`.`position_id` `id`, `daily` `day_rate`, `hourly` `rate` FROM `position_rate_table` LEFT JOIN `positions` ON `position_rate_table`.`position_id`=`positions`.`position_id` WHERE `position_rate_table`.`start_date` < DATE(NOW()) AND IFNULL(NULLIF(`position_rate_table`.`end_date`,'0000-00-00'),'9999-12-21') > DATE(NOW()) AND `position_rate_table`.`deleted`=0 AND `positions`.`deleted`=0 UNION
	SELECT `tile_name` `type`, `description` `name`, `item_id` `id`, `daily` `day_rate`, `hourly` `rate` FROM `company_rate_card` WHERE `start_date` < DATE(NOW()) AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-21') > DATE(NOW()) AND `deleted`=0 UNION
	SELECT 'services' `type`, CONCAT(TRIM(CONCAT(IFNULL(`services`.`category`,''),' ',IFNULL(`services`.`service_type`,''))),': ',`services`.`heading`) `name`, `services`.`serviceid` `id`, 0 `day_rate`, `service_rate` `rate` FROM `service_rate_card` LEFT JOIN `services` ON `service_rate_card`.`serviceid`=`services`.`serviceid` WHERE `service_rate_card`.`deleted`=0 AND `services`.`deleted`=0 AND `start_date` < DATE(NOW()) AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') > DATE(NOW()) UNION
	SELECT 'equip_category' `type`, `category` `name`, 0 `id`, `daily` `day_rate`, `hourly` `rate` FROM `category_rate_table` WHERE `deleted`=0 AND `start_date` < DATE(NOW()) AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') > DATE(NOW()) UNION
	SELECT 'staff' `type`, `staff_id` `name`, `staff_id`, `daily` `day_rate`, `hourly` `rate` FROM `staff_rate_table` WHERE `deleted`=0 AND `start_date` < DATE(NOW()) AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') > DATE(NOW()) UNION
	SELECT `tile_name` `type`, `src_id` `name`, `src_id` `id`, MAX(IF(`uom` LIKE '%daily%',`price`,0)) `day_rate`, MAX(IF(`uom` LIKE '%hour%',`price`,0)) `rate` FROM `tile_rate_card` WHERE `deleted`=0 AND `start_date` < DATE(NOW()) AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') > DATE(NOW()) GROUP BY `tile_name`, `src_id`";
	$rate_lines = mysqli_query($dbc, $rate_sql);
	$report_data .= '<table class="table table-bordered">
		<tr class="hidden-xs hidden-sm">
			<th>Rate Description</th>
			<th>Rate</th>
			<th>Total Quantity</th>
			<th>Total Discounts</th>
			<th>Total Charged</th>
		</tr>';
	$total_discounts = $total_charges = 0;
	while($rate = mysqli_fetch_array($rate_lines)) {
		$report_data .= '<tr><td data-title="Rate Description">'.($rate['type'] == 'staff' ? get_contact($dbc, $rate['id']) : ($rate['type'] == 'labour' && $rate['id'] > 0 ? implode(': ',get_field_value('labour_type heading', 'labour', 'labourid', $rate['id'])) : $rate['name'])).'</td>
			<td data-title="Rate">'.($rate['day_rate'] > 0 ? 'Daily Rate: $'.number_format($rate['day_rate'],2).'<br />' : '').($rate['rate'] > 0 ? ($rate['type'] != 'services' ? 'Hourly: $' : '$').number_format($rate['rate'],2) : '').'</td>';
		$discounts = $charges = $line_qty = 0;
		if($rate['type'] == 'services') {
			$service_list = $dbc->query("SELECT `serviceid`,`service_qty`,`service_discount`,`service_discount_type` FROM `tickets` WHERE `deleted`=0 AND IFNULL(`serviceid`,'') != ''");
			while($service = $service_list->fetch_assoc()) {
				foreach(explode(',',$service['serviceid']) as $i => $serviceid) {
					if($serviceid == $rate['id']) {
						$charge = $rate['rate'] * explode(',',$service['service_qty'])[$i];
						$discount = (explode(',',$service['service_discount_type'])[$i] == '$' ? explode(',',$service['service_discount'])[$i] : explode(',',$service['service_discount'])[$i] * $charge / 100);
						$line_qty += explode(',',$service['service_qty'])[$i];
						$charges += $charge - $discount;
						$discounts += $discount;
					}
				}
			}
		} else {
			$charge_list = $dbc->query("SELECT `qty`, `hours_set`, `hours_tracked`, `rate`, `discount`, `discount_type`, `notes` FROM `ticket_attached` WHERE `deleted`=0 AND `src_table`='".$rate['type']."' AND `item_id`='".$rate['id']."'");
			while($charge = $charge_list->fetch_assoc()) {
				$qty = $charge['hours_set'] > 0 ? $charge['hours_set'] : ($charge['hours_tracked'] > 0 ? $charge['hours_tracked'] : $charge['qty']);
				$line_qty += $qty;
				$rate = $charge['notes'] == 'daily' ? $rate['day_rate'] : ($rate['rate'] > 0 ? $rate['rate'] : $charge['rate']);
				$charges += $qty * $rate - ($charge['discount_type'] == '$' ? $charge['discount'] : $qty * $rate * $charge['discount'] / 100);
				$discounts += ($charge['discount_type'] == '$' ? $charge['discount'] : $qty * $rate * $charge['discount'] / 100);
			}
		}
		$total_discounts += $discounts;
		$total_charges += $charges;
		$report_data .= '<td data-title="Quantity">'.$line_qty.'</td>
			<td data-title="Discounts">$'.number_format($discounts, 2, '.', '').'</td>
			<td data-title="Charged">$'.number_format($charges, 2, '.', '').'</td></tr>';
	}
	$report_data .= '<tr>
		<td data-title=""><b>Totals</b></td>
		<td data-title=""></td>
		<td data-title=""></td>
		<td data-title="Discounts"><b>$'.number_format($total_discounts, 2, '.', '').'</b></td>
		<td data-title="Charges"><b>$'.number_format($total_charges, 2, '.', '').'</b></td>
	</tr>';
	$report_data .= '</table>';

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