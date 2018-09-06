<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
if (isset($_POST['printpdf'])) {
	$contact = $_POST['report_contact'];
	$woid = $_POST['report_wo'];
	$from_date = $_POST['report_from'];
	$until_date = $_POST['report_until'];
    $today_date = date('Y-m-d');
	$pdf_name = "Download/shop_work_orders_task_time_$today_date.pdf";

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
            $footer_text = 'Shop Work Orders - Time Spent by Task';
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

	$html = '<h3>Report Date: '.$from_date.($until_date == $from_date ? '' : ' to '.$until_date).'</h3>';
    $html .= shop_work_orders($dbc, $from_date, $until_date, $woid, $contact, true, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output($pdf_name, 'F');
    track_download($dbc, 'report_operations_shop_task_time', 0, WEBSITE_URL.'/Reports/Download/shop_work_orders_task_time_'.$today_date.'.pdf', 'Shop Work Orders - Time Spent by Task Report');
    ?>

	<script>
		window.location.replace('<?php echo $pdf_name; ?>');
	</script>
<?php } ?>


        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-inline" role="form">

            <?php $search_wo = '';
			$search_contact = '';
			$search_from = date('Y-m-01');
			$search_until = date('Y-m-d');

            if (isset($_POST['search_wo'])) {
                $search_wo = $_POST['search_wo'];
            }
            if (isset($_POST['search_contact'])) {
                $search_contact = $_POST['search_contact'];
            }
            if (isset($_POST['search_from'])) {
                $search_from = $_POST['search_from'];
            }
            if (isset($_POST['search_until'])) {
                $search_until = $_POST['search_until'];
            } ?>

			<div class="col-sm-5">
				<label for="search_wo" class="col-sm-4 control-label">Search By Work Order #:</label>
				<div class="col-sm-8">
					<select data-placeholder="Select a Work Order #" name="search_wo" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
						<option value=""></option>
						<?php
						$query = mysqli_query($dbc,"SELECT `unique_id`, `projectmanageid` FROM `project_manage` WHERE `status` = 'Approved' AND `tile` = 'Shop Work Orders' ORDER BY `unique_id`");
						while($row = mysqli_fetch_array($query)) { ?>
							<option <?php if ($row['projectmanageid'] == $search_wo) { echo " selected"; } ?> value='<?php echo  $row['projectmanageid']; ?>' ><?php echo $row['unique_id']; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="col-sm-5">
				<label for="search_contact" class="col-sm-4 control-label">Search By Business / Contact:</label>
				<div class="col-sm-8">
					<select data-placeholder="Select a Business / Contact" name="search_contact" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
						<option value=""></option>
						<?php $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `first_name`, `last_name`, `name`, `contactid` FROM `contacts` WHERE `deleted`=0 AND `status`=1 AND `contactid` IN (SELECT `businessid` FROM `project_manage` WHERE `status` = 'Approved' AND `tile` = 'Shop Work Orders' UNION SELECT `contactid` FROM `project_manage` WHERE `status` = 'Approved' AND `tile` = 'Shop Work Orders')"));
						foreach($contact_list as $contact) { ?>
							<option <?php if ($contact['contactid'] == $search_contact) { echo " selected"; } ?> value='<?php echo  $row['contactid']; ?>' ><?= ($contact['name'] != '' ? $contact['name'] : $contact['first_name'].' '.$contact['last_name']) ?></option>
						<?php } ?>
					</select>
				</div>
			</div>

				<div class="col-sm-5">
					<label for="search_from" class="col-sm-4 control-label">Search From Date:</label>
					<div class="col-sm-8 col-xs-8">
						<input type="text" class="form-control datepicker" name="search_from" value="<?php echo $search_from; ?>" style="width:100;">
					</div>
				</div>
				<div class="col-sm-5">
					<label for="search_until" class="col-sm-4 control-label">Search Until Date:</label>
					<div class="col-sm-8 col-xs-8">
						<input type="text" class="form-control datepicker" name="search_until" value="<?php echo $search_until; ?>" style="width:100;">
					</div>
				</div>

			<div class="col-sm-2">
				<button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
				<button type="button" onclick="window.location=''" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display Current</button>
				<button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
			</div>

            <input type="hidden" name="report_contact" value="<?php echo $search_contact; ?>">
            <input type="hidden" name="report_wo" value="<?php echo $search_wo; ?>">
            <input type="hidden" name="report_from" value="<?php echo $search_from; ?>">
            <input type="hidden" name="report_until" value="<?php echo $search_until; ?>">
            <br><br>

            <?= shop_work_orders($dbc, $search_from, $search_until, $search_wo, $search_contact) ?>

        </form>

<?php
function shop_work_orders($dbc, $search_from, $search_until, $search_wo, $search_contact, $no_page = false, $table_style = '', $table_row_style = '', $grand_total_style = '') {
    $report_data = '';

	$rowsPerPage = 15;
	$pageNum = 1;
	$limit = '';
	if(isset($_GET['page'])) {
		$pageNum = $_GET['page'];
	}
	$offset = ($pageNum - 1) * $rowsPerPage;
	if($no_page === false) {
		$limit = " LIMIT $offset, $rowsPerPage";
	}

	$clause = '';
    if($search_from != '') {
        $clause .= " AND timer.`created_date` >= '$search_from'";
    }
    if($search_until != '') {
        $clause .= " AND timer.`created_date` <= '$search_until'";
    }
    if($search_wo != '') {
        $clause .= " AND pm.`projectmanageid` = '$search_wo'";
    }
    if($search_contact != '') {
        $clause .= " AND '$search_contact' IN (pm.`businessid`,pm.`contactid`)";
    }
	$sql = "SELECT pm.`projectmanageid`, pm.`unique_id`, pm.`businessid`, pm.`contactid`, timer.`timer_task`, SEC_TO_TIME(SUM(TIME_TO_SEC(IFNULL(timer.`regular_hrs`,'00:00:00')))) reg_time,
		SEC_TO_TIME(SUM(TIME_TO_SEC(IFNULL(timer.`overtime_hrs`,'00:00:00')))) over_time, SEC_TO_TIME(SUM(TIME_TO_SEC(IFNULL(timer.`timer`,'00:00:00')))) total_time
		FROM `project_manage` pm LEFT JOIN `project_manage_assign_to_timer` timer ON pm.`projectmanageid`=timer.`projectmanageid`
		WHERE pm.`status`='Approved' AND pm.`tile`='Shop Work Orders' $clause GROUP BY pm.`projectmanageid`, pm.`unique_id`, timer.`timer_task` ORDER BY pm.`unique_id` $limit";
	$query = "SELECT COUNT(*) numrows FROM `project_manage` pm LEFT JOIN `project_manage_assign_to_timer` timer ON pm.`projectmanageid`=timer.`projectmanageid`
		WHERE pm.`status`='Approved' AND pm.`tile`='Shop Work Orders' $clause GROUP BY pm.`projectmanageid`, pm.`unique_id`, timer.`timer_task`";
    $result = mysqli_query($dbc,$sql);
	if($no_page === false) {
		echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
	}

    $report_data .= '<table border="1px" class="table table-bordered" width="100%" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">';
    $report_data .= '<th>Work Order #</th>';
    $report_data .= '<th>Business / Contact</th>';
	$report_data .= '<th>Task</th>';
	$report_data .= '<th>Regular</th>';
	$report_data .= '<th>Overtime</th>';
	$report_data .= '<th>Total Time</th>';
    $report_data .=  "</tr>";

    while($row = mysqli_fetch_array( $result ))
    {
        $report_data .= '<tr nobr="true">';
        $timetrackingid = $row['timetrackingid'];
        $report_data .=  '<td data-title="Work Order">' . $row['unique_id'] . '</td>';
        $report_data .=  '<td data-title="Business / Contact">' . ($row['businessid'] > 0 ? get_client($dbc, $row['businessid']).'<br />' : '').($row['contactid'] > 0 ? get_contact($dbc, $row['contactid']) : '') . '</td>';
        $report_data .=  '<td data-title="Task">' . $row['timer_task'] . '</td>';
        $report_data .=  '<td data-title="Regular">' . $row['reg_time'] . '</td>';
        $report_data .=  '<td data-title="Regular">' . $row['over_time'] . '</td>';
        $report_data .=  '<td data-title="Total Time">' . $row['total_time'] . '</td>';

        $report_data .=  "</tr>";
    }

    $report_data .=  '</table>';

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