<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');

if (isset($_GET['printpdf'])) {
	$staffidspdf = array_filter(explode(',', $_GET['staffidspdf']));
	$approvedpdf = $_GET['approvedpdf'];
    $starttimepdf = $_GET['starttimepdf'];
    $endtimepdf = $_GET['endtimepdf'];

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
            $footer_text = 'Employee Payroll Report';
            $this->writeHTMLCell(0, 0, 0 , 15, $footer_text, 0, 0, false, "C", true);
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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 25, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('P','LETTER');
	$pdf->SetFont('helvetica','',9);

	$html = report_output($dbc, $staffidspdf, $approvedpdf, $starttimepdf, $endtimepdf, 'pdf');

	$pdf->writeHTML($html, true, false, true, false, '');
    $today_date = date('Y_m_d');
	$pdf->Output('Download/employee_payroll_report_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'employee_payroll_report_', 0, WEBSITE_URL.'/Reports/Download/employee_payroll_report_'.$today_date.'.pdf', 'Employee Payroll Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/employee_payroll_report_<?= $today_date ?>.pdf', 'fullscreen=yes');
	</script><?php
	$_GET['search_email_submit'] = 1;
	$staffids = $staffidspdf;
	$approved = $approvedpdf;
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
} ?>

<script type="text/javascript">
$(document).on('change', 'select[name="staffids[]"]', function() { filterStaff(this); });
function filterStaff(sel) {
	if($(sel).find('option[value="ALL"]').is(':selected')) {
		$(sel).find('option').prop('selected', true);
	}
	$(sel).find('option[value="ALL"]').prop('selected', false);
	$(sel).trigger('change.select2');
}
</script>

<!-- <div class="notice double-gap-bottom popover-examples">
    <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
    <div class="col-sm-11"><span class="notice-name">NOTE:</span> </div>
    <div class="clearfix"></div>
</div> -->

<form id="form1" name="form1" method="get" action="" enctype="multipart/form-data" class="form-horizontal gap-top" role="form">
    <input type="hidden" name="type" value="<?php echo $_GET['type']; ?>">
    <input type="hidden" name="report" value="<?php echo $_GET['report']; ?>">

    <?php
    if (isset($_GET['search_email_submit'])) {
    	$staffids = $_GET['staffids'];
    	$approved = $_GET['approved'];
        $starttime = $_GET['starttime'];
        $endtime = $_GET['endtime'];
    }
    if($starttime == 0000-00-00) {
    	if(date('l') == 'Sunday') {
    		$starttime = date('Y-m-d');
    	} else {
            $starttime = date('Y-m-d', strtotime('last Sunday', strtotime(date('Y-m-d'))));
    	}
    }
    if($endtime == 0000-00-00) {
    	if(date('l') == 'Saturday') {
    		$endtime = date('Y-m-d');
    	} else {
            $endtime = date('Y-m-d', strtotime('next Saturday', strtotime(date('Y-m-d'))));
    	}
    }
    if(empty($approved)) {
    	$approved = "Show All";
    }
    ?>

    <center>
		<div class="form-group col-sm-5">
			<label class="col-sm-4">Staff:</label>
			<div class="col-sm-8">
				<select name="staffids[]" multiple class="chosen-select-deselect" data-placeholder="Select Staff"><option />
					<option value="ALL">All Staff</option>
					<?php foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1")) as $staff_row) { ?>
						<option <?= in_array($staff_row['contactid'],$staffids) ? 'selected' : '' ?> value="<?= $staff_row['contactid'] ?>"><?= $staff_row['full_name'] ?></option>
					<?php } ?>
				</select>
			</div>
        </div>
		<div class="form-group col-sm-5">
			<label class="col-sm-4">Approved for Payroll:</label>
			<div class="col-sm-8">
				<select name="approved" class="chosen-select-deselect"><option />
					<option value="Show All" <?= $approved == 'Show All' ? 'selected' : '' ?>>Show All</option>
					<option value="Approved Only" <?= $approved == 'Approved Only' ? 'selected' : '' ?>>Approved Only</option>
				</select>
			</div>
        </div>
        <div class="clearfix"></div>
		<div class="form-group col-sm-5">
			<label class="col-sm-4">Date From:</label>
			<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
        </div>
		<div class="form-group col-sm-5">
			<label class="col-sm-4">Date Until:</label>
			<div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
		</div>
		<button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
	</center>

	<div class="clearfix"></div>

	<input type="hidden" name="staffidspdf" value="<?php echo implode(',', $staffids); ?>">
	<input type="hidden" name="approvedpdf" value="<?php echo $approved; ?>">
    <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
    <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">

    <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
    <br><br>
	<div class="clearfix"></div>

    <?php if(isset($_GET['search_email_submit'])) {
    	echo report_output($dbc, $staffids, $approved, $starttime, $endtime);
    } else {
		echo '<h3>Please submit the search criteria.</h3>';
	} ?>
</form>

<?php

function report_output($dbc, $staffids, $approved, $starttime, $endtime, $output_mode) {
	$timesheet_start_tile = get_config($dbc, 'timesheet_start_tile');
	$approv_query = '';
	if($approved == 'Approved Only') {
		$approv_query = " AND `approv` != 'N' ";
	}
	$report_page = '<table width="100%" border="0" cellpadding="2">
		<tr>
			<td style="padding:0.5em;width:15%;">Date Range:</td>
			<td style="padding:0.5em;width:85%;">'.date('M-j-Y',strtotime($starttime)).' to '.date('M-j-Y',strtotime($endtime)).'</td>
		</tr>
		<tr>
			<td style="padding:0.5em;width:15%;">Approved for Payroll:</td>
			<td style="padding:0.5em;width:85;">'.$approved.'</td>
		</tr>
	</table>';
	if(count($staffids) > 0) {
		foreach($staffids as $staffid) {
			if($staffid > 0) {
				$grand_total = 0;
				$total = [];
				$result = [];
				$min_time = [];
				$max_time = [];
				for($cur_day = $starttime; strtotime($cur_day) <= strtotime($endtime); $cur_day = date('Y-m-d', strtotime($cur_day.' + 1 day'))) {
					$query = mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `staff` = '$staffid' AND `date` = '$cur_day' AND `deleted` = 0 $approv_query ORDER BY `date`, IFNULL(DATE_FORMAT(CONCAT_WS(' ',DATE(NOW()),`start_time`),'%H:%i'),STR_TO_DATE(`start_time`,'%l:%i %p')) ASC, IFNULL(DATE_FORMAT(CONCAT_WS(' ',DATE(NOW()),`end_time`),'%H:%i'),STR_TO_DATE(`end_time`,'%l:%i %p')) ASC");
					while($row = mysqli_fetch_assoc($query)) {
						if(!empty($row['start_time']) && (strtotime($row['start_time']) < strtotime($min_time[$cur_day]) || empty($min_time[$cur_day]))) {
							$min_time[$cur_day] = date('h:i a', strtotime($row['start_time']));
						}
						if(!empty($row['end_time']) && (strtotime($row['end_time']) > strtotime($max_time) || empty($max_time[$cur_day]))) {
							$max_time[$cur_day] = date('h:i a', strtotime($row['end_time']));
						}
						if($row['ticketid'] > 0) {
							$label = get_ticket_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$row['ticketid']."'")));
							if($output_mode != 'pdf') {
								$label = '<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row['ticketid'].'" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Ticket/index.php?edit='.$row['ticketid'].'&calendar_view=true\'); return false;">'.$label.'</a>';
							}
						} else {
							$label = $timesheet_start_tile;
						}
						$total[$cur_day] += number_format($row['total_hrs'],2);
						$grand_total += number_format($row['total_hrs'],2);
						$result[$cur_day][] = ['label'=>$label, 'start_time'=>(!empty($row['start_time']) ? date('h:i a', strtotime($row['start_time'])) : ''), 'end_time'=>(!empty($row['end_time']) ? date('h:i a', strtotime($row['end_time'])) : ''),'hours'=>number_format($row['total_hrs'],2),'approved'=>$row['approved']];
					}
				}

				$report_page .= '<table border="0" cellpadding="2" class="table no-border" width="100%">
					<tr style="background-color: #ccc;">
						<td width="70%" colspan="2">'.get_contact($dbc, $staffid).'</td>
						<td width="15%" align="right">Total Hours:</td>
						<td width="15%" align="right">'.number_format($grand_total,2).'</td>
					</tr>';
				foreach($result as $cur_day => $records) {
					$report_page .= '<tr>
						<td width="55%" style="font-weight: bold; border-bottom: 2px solid black;">'.date('D-M-d-Y', strtotime($cur_day)).'</td>
						<td width="15%" style="font-weight: bold; border-bottom: 2px solid black;" align="right">Hrs: '.number_format($total[$cur_day],2).'</td>
						<td width="15%" style="font-weight: bold; border-bottom: 2px solid black;" align="right">In: '.$min_time[$cur_day].'</td>
						<td width="15%" style="font-weight: bold; border-bottom: 2px solid black;" align="right">Out: '.$max_time[$cur_day].'</td>
					</tr>';
					foreach($records as $record) {
						$report_page .= '<tr>
							<td width="55%" style="border-bottom: 1px solid black;">'.$record['label'].'</td>
							<td width="15%" style="border-bottom: 1px solid black;" align="right">'.$record['hours'].'</td>
							<td width="15%" style="border-bottom: 1px solid black;" align="right">'.$record['start_time'].'</td>
							<td width="15%" style="border-bottom: 1px solid black;" align="right">'.$record['end_time'].'</td>
						</tr>';
					}
				}
				if(empty($result)) {
					$report_page .= '<tr><td colspan="4" style="border-bottom: 1px solid black;">No Results Found.</td></tr>';
				}
				$report_page .= '</table>';
				if($output_mode == 'pdf') {
					$report_page .= '<p style="font-size: 1px;"></p>';
				}
			}
		}
	} else {
		$report_page .= '<h3>No Staff Selected</h3>';
	}
    return $report_page;
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