<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
$report_fields = explode(',', get_config($dbc, 'report_operation_fields'));
if (isset($_POST['printpdf'])) {
	$task = $_POST['report_task'];
	$material = $_POST['report_material'];
	$ticketid = $_POST['report_ticket'];
	$extra_ticketid = $_POST['report_extra_ticket'];
	$projectid = $_POST['report_project'];
	$from_date = $_POST['report_from'];
	$until_date = $_POST['report_until'];
    $today_date = date('Y-m-d');
	$pdf_name = "Download/".config_safe_str(TICKET_NOUN)."_attached_$today_date.pdf";

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
            $footer_text = 'Shop Work Orders - Time Spent';
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
    $html .= shop_work_orders($dbc, $from_date, $until_date, $ticketid, $extra_ticketid, $projectid, $task, $material, true, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output($pdf_name, 'F');
    track_download($dbc, 'report_operations_ticket_attached', 0, WEBSITE_URL.'/Reports/Download/'.config_safe_str(TICKET_NOUN).'_attached_'.$today_date.'.pdf', 'Shop Work Orders - Time Spent Report');
    ?>

	<script>
		window.location.replace('<?php echo $pdf_name; ?>');
	</script>
<?php } ?>


        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-inline" role="form">

            <?php $search_ticket = '';
			$search_extra_ticket = '';
			$search_project = '';
			$search_material = '';
			$search_task = '';
			$search_from = date('Y-m-01');
			$search_until = date('Y-m-d');

            if (isset($_POST['search_ticket'])) {
                $search_ticket = $_POST['search_ticket'];
            }
            if (isset($_POST['search_extra_ticket'])) {
                $search_extra_ticket = $_POST['search_extra_ticket'];
            }
            if (isset($_POST['search_project'])) {
                $search_project = $_POST['search_project'];
            }
            if (isset($_POST['search_material'])) {
                $search_material = $_POST['search_material'];
            }
            if (isset($_POST['search_task'])) {
                $search_task = $_POST['search_task'];
            }
            if (isset($_POST['search_from'])) {
                $search_from = $_POST['search_from'];
            }
            if (isset($_POST['search_until'])) {
                $search_until = $_POST['search_until'];
            } ?>

			<div class="col-sm-5">
				<label for="search_ticket" class="col-sm-4 control-label">Search By <?= TICKET_NOUN ?>:</label>
				<div class="col-sm-8">
					<select data-placeholder="Select a <?= TICKET_NOUN ?> #" name="search_ticket" class="chosen-select-deselect form-control">
						<option value=""></option>
						<?php
						$query = mysqli_query($dbc,"SELECT * FROM `tickets` WHERE `deleted`=0 ORDER BY `ticketid`");
						while($row = mysqli_fetch_array($query)) { ?>
							<option <?php if ($row['ticketid'] == $search_ticket) { echo " selected"; } ?> value='<?= $row['ticketid'] ?>' ><?= get_ticket_label($dbc, $row) ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<?php if(in_array('filter_extra_billing',$report_fields)) { ?>
				<div class="col-sm-5">
					<label for="search_extra_ticket" class="col-sm-4 control-label">Search By Extra Billing <?= TICKET_NOUN ?>:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select a <?= TICKET_NOUN ?> #" name="search_extra_ticket" class="chosen-select-deselect form-control">
							<option value=""></option>
							<?php
							$query = mysqli_query($dbc,"SELECT * FROM `tickets` WHERE `deleted`=0 AND `ticketid` IN (SELECT `ticketid` FROM `ticket_comment` WHERE `type`='service_extra_billing' AND `deleted`=0) ORDER BY `ticketid`");
							while($row = mysqli_fetch_array($query)) { ?>
								<option <?php if ($row['ticketid'] == $search_extra_ticket) { echo " selected"; } ?> value='<?= $row['ticketid'] ?>' ><?= get_ticket_label($dbc, $row) ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			<?php } ?>
			<div class="col-sm-5">
				<label for="search_project" class="col-sm-4 control-label">Search By <?= PROJECT_NOUN ?>:</label>
				<div class="col-sm-8">
					<select data-placeholder="Select a <?= PROJECT_NOUN ?>" name="search_project" class="chosen-select-deselect form-control">
						<option value=""></option>
						<?php $query = mysqli_query($dbc, "SELECT `projectid`, `project_name` FROM `project` WHERE `deleted`=0 AND `status` NOT IN ('Pending')");
						while($row = mysqli_fetch_assoc($query)) { ?>
							<option <?php if ($row['projectid'] == $search_project) { echo " selected"; } ?> value='<?php echo  $row['projectid']; ?>' ><?= PROJECT_NOUN.' #'.$row['projectid'].' '.$row['project_name'] ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="col-sm-5">
				<label for="search_contact" class="col-sm-4 control-label">Search By Task:</label>
				<div class="col-sm-8">
					<input type="text" name="search_task" class="form-control" value="<?= $search_task ?>">
				</div>
			</div>
			<?php if(in_array('filter_materials',$report_fields)) { ?>
				<div class="col-sm-5">
					<label for="search_material" class="col-sm-4 control-label">Search By Materials:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select Material" name="search_material" class="chosen-select-deselect form-control">
							<option value=""></option>
							<?php
							$query = mysqli_query($dbc,"SELECT `description` FROM (SELECT `description` FROM `ticket_attached` WHERE `deleted`=0 AND `src_table`='material' UNION SELECT `name` `description` FROM `material` WHERE `deleted`=0) `materials` WHERE IFNULL(`description`,'')!='' GROUP BY `description` ORDER BY `description`");
							while($row = mysqli_fetch_array($query)) { ?>
								<option <?php if ($row['description'] == $search_material) { echo " selected"; } ?> value='<?= $row['description'] ?>' ><?= $row['description'] ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			<?php } ?>

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

            <input type="hidden" name="report_ticket" value="<?php echo $search_ticket; ?>">
            <input type="hidden" name="report_extra_ticket" value="<?php echo $search_extra_ticket; ?>">
            <input type="hidden" name="report_project" value="<?php echo $search_project; ?>">
            <input type="hidden" name="report_material" value="<?php echo $search_material; ?>">
            <input type="hidden" name="report_task" value="<?php echo $search_task; ?>">
            <input type="hidden" name="report_from" value="<?php echo $search_from; ?>">
            <input type="hidden" name="report_until" value="<?php echo $search_until; ?>">
            <div class="clearfix"></div>

            <?= shop_work_orders($dbc, $search_from, $search_until, $search_ticket, $search_extra_ticket, $search_project, $search_task, $search_material) ?>

        </form>

<?php
function shop_work_orders($dbc, $search_from, $search_until, $search_ticket, $search_extra_ticket, $search_project, $search_task, $search_material, $no_page = false, $table_style = '', $table_row_style = '', $grand_total_style = '') {
    $report_data = '';

	$rowsPerPage = 25;
	$pageNum = 1;
	$limit = '';
	if(isset($_GET['page'])) {
		$pageNum = $_GET['page'];
	}
	$offset = ($pageNum - 1) * $rowsPerPage;
	if($no_page === false) {
		$limit = " LIMIT $offset, $rowsPerPage";
	}

	$edit_ticket = vuaed_visible_function($dbc, 'ticket');
	$edit_project = vuaed_visible_function($dbc, 'project');
	$from = '';
	if(!$no_page) {
		$from = '&from='.urlencode(WEBSITE_URL.'/Reports/report_operations_ticket_attached.php');
	}

	$sql = "SELECT CONCAT(`tickets`.`ticketid`,' ',`tickets`.`heading`) ticket_label, `tickets`.`ticketid`, CONCAT(`project`.`projectid`,' ',`project`.`project_name`) project_label, `project`.`projectid`, IF(`ticket_attached`.`src_table`='Staff_Tasks',`ticket_attached`.`position`,IF(`ticket_attached`.`src_table`='equipment',CONCAT(IFNULL(`equipment`.`unit_number`,''),' ',IFNULL(`equipment`.`category`,''),' ',IFNULL(`equipment`.`make`,''),' ',IFNULL(`equipment`.`model`,''),' ',IFNULL(`equipment`.`label`,''),' ',IFNULL(`equipment`.`nickname`,'')),IF(`ticket_attached`.`src_table`='material',CONCAT(IFNULL(`material`.`category`,''),' ',IFNULL(`material`.`sub_category`,''),' ',IFNULL(`material`.`name`,'')),`ticket_attached`.`item_id`))) 'description', `ticket_attached`.`date_stamp` date, `ticket_attached`.`qty`, `ticket_attached`.`src_table`, `ticket_attached`.`hours_tracked`
		FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` LEFT JOIN `ticket_attached` ON `tickets`.`ticketid`=`ticket_attached`.`ticketid` LEFT JOIN `equipment` ON `ticket_attached`.`item_id`=`equipment`.`equipmentid` LEFT JOIN `material` ON `ticket_attached`.`item_id`=`material`.`materialid`
		WHERE `tickets`.`deleted`=0 AND IFNULL(`project`.`deleted`,0)=0 AND `ticket_attached`.`deleted`=0 AND ('$search_ticket' IN ('',`tickets`.`ticketid`) OR '$search_extra_ticket' IN ('',`tickets`.`ticketid`)) AND '$search_project' IN ('',`project`.`projectid`) AND IF(`ticket_attached`.`src_table`='Staff_Tasks',`ticket_attached`.`position`,IF(`ticket_attached`.`src_table`='equipment',CONCAT(IFNULL(`equipment`.`unit_number`,''),' ',IFNULL(`equipment`.`category`,''),' ',IFNULL(`equipment`.`make`,''),' ',IFNULL(`equipment`.`model`,''),' ',IFNULL(`equipment`.`label`,''),' ',IFNULL(`equipment`.`nickname`,'')),IF(`ticket_attached`.`src_table`='material',CONCAT(IFNULL(`material`.`category`,''),' ',IFNULL(`material`.`sub_category`,''),' ',IFNULL(`material`.`name`,'')),''))) LIKE '%$search_task%' AND IF(`ticket_attached`.`src_table`='Staff_Tasks',`ticket_attached`.`position`,IF(`ticket_attached`.`src_table`='equipment',CONCAT(IFNULL(`equipment`.`unit_number`,''),' ',IFNULL(`equipment`.`category`,''),' ',IFNULL(`equipment`.`make`,''),' ',IFNULL(`equipment`.`model`,''),' ',IFNULL(`equipment`.`label`,''),' ',IFNULL(`equipment`.`nickname`,'')),IF(`ticket_attached`.`src_table`='material',CONCAT(IFNULL(`material`.`category`,''),' ',IFNULL(`material`.`sub_category`,''),' ',IFNULL(`material`.`name`,'')),''))) LIKE '%$search_material%' $limit";
	$total_sql = "SELECT SUM(`ticket_attached`.`hours_tracked`) `all_hours`
		FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` LEFT JOIN `ticket_attached` ON `tickets`.`ticketid`=`ticket_attached`.`ticketid` LEFT JOIN `equipment` ON `ticket_attached`.`item_id`=`equipment`.`equipmentid` LEFT JOIN `material` ON `ticket_attached`.`item_id`=`material`.`materialid`
		WHERE `tickets`.`deleted`=0 AND IFNULL(`project`.`deleted`,0)=0 AND `ticket_attached`.`deleted`=0 AND ('$search_ticket' IN ('',`tickets`.`ticketid`) OR '$search_extra_ticket' IN ('',`tickets`.`ticketid`)) AND '$search_project' IN ('',`project`.`projectid`) AND IF(`ticket_attached`.`src_table`='Staff_Tasks',`ticket_attached`.`position`,IF(`ticket_attached`.`src_table`='equipment',CONCAT(IFNULL(`equipment`.`unit_number`,''),' ',IFNULL(`equipment`.`category`,''),' ',IFNULL(`equipment`.`make`,''),' ',IFNULL(`equipment`.`model`,''),' ',IFNULL(`equipment`.`label`,''),' ',IFNULL(`equipment`.`nickname`,'')),IF(`ticket_attached`.`src_table`='material',CONCAT(IFNULL(`material`.`category`,''),' ',IFNULL(`material`.`sub_category`,''),' ',IFNULL(`material`.`name`,'')),''))) LIKE '%$search_task%' AND IF(`ticket_attached`.`src_table`='Staff_Tasks',`ticket_attached`.`position`,IF(`ticket_attached`.`src_table`='equipment',CONCAT(IFNULL(`equipment`.`unit_number`,''),' ',IFNULL(`equipment`.`category`,''),' ',IFNULL(`equipment`.`make`,''),' ',IFNULL(`equipment`.`model`,''),' ',IFNULL(`equipment`.`label`,''),' ',IFNULL(`equipment`.`nickname`,'')),IF(`ticket_attached`.`src_table`='material',CONCAT(IFNULL(`material`.`category`,''),' ',IFNULL(`material`.`sub_category`,''),' ',IFNULL(`material`.`name`,'')),''))) LIKE '%$search_material%' $limit";
	$query = "SELECT COUNT(*) numrows FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` LEFT JOIN `ticket_attached` ON `tickets`.`ticketid`=`ticket_attached`.`ticketid` LEFT JOIN `equipment` ON `ticket_attached`.`item_id`=`equipment`.`equipmentid` LEFT JOIN `material` ON `ticket_attached`.`item_id`=`material`.`materialid`
		WHERE `tickets`.`deleted`=0 AND IFNULL(`project`.`deleted`,0)=0 AND `ticket_attached`.`deleted`=0 AND ('$search_ticket' IN ('',`tickets`.`ticketid`) OR '$search_extra_ticket' IN ('',`tickets`.`ticketid`)) AND '$search_project' IN ('',`project`.`projectid`) AND IF(`ticket_attached`.`src_table`='Staff_Tasks',`ticket_attached`.`position`,IF(`ticket_attached`.`src_table`='equipment',CONCAT(IFNULL(`equipment`.`unit_number`,''),' ',IFNULL(`equipment`.`category`,''),' ',IFNULL(`equipment`.`make`,''),' ',IFNULL(`equipment`.`model`,''),' ',IFNULL(`equipment`.`label`,''),' ',IFNULL(`equipment`.`nickname`,'')),IF(`ticket_attached`.`src_table`='material',CONCAT(IFNULL(`material`.`category`,''),' ',IFNULL(`material`.`sub_category`,''),' ',IFNULL(`material`.`name`,'')),''))) LIKE '%$search_task%' AND IF(`ticket_attached`.`src_table`='Staff_Tasks',`ticket_attached`.`position`,IF(`ticket_attached`.`src_table`='equipment',CONCAT(IFNULL(`equipment`.`unit_number`,''),' ',IFNULL(`equipment`.`category`,''),' ',IFNULL(`equipment`.`make`,''),' ',IFNULL(`equipment`.`model`,''),' ',IFNULL(`equipment`.`label`,''),' ',IFNULL(`equipment`.`nickname`,'')),IF(`ticket_attached`.`src_table`='material',CONCAT(IFNULL(`material`.`category`,''),' ',IFNULL(`material`.`sub_category`,''),' ',IFNULL(`material`.`name`,'')),''))) LIKE '%$search_material%'";
    $result = mysqli_query($dbc,$sql);
	if($no_page === false) {
		echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
	}

    $report_data .= '<table border="1px" class="table table-bordered" width="100%" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">';
    $report_data .= '<th>'.TICKET_NOUN.' #</th>';
    $report_data .= '<th>'.PROJECT_NOUN.'</th>';
	$report_data .= '<th>Description</th>';
	$report_data .= '<th>Date</th>';
	$report_data .= '<th>Hours / Quantity</th>';
    $report_data .=  "</tr>";

    while($row = mysqli_fetch_array( $result )) {
        $report_data .= '<tr nobr="true">';
        $report_data .=  '<td data-title="'.TICKET_NOUN.' #">'.($edit_ticket > 0 ? '<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row['ticketid'].$from.'">' : '').$row['ticket_label'].($edit_ticket > 0 ? '</a>' : '').'</td>';
        $report_data .=  '<td data-title="'.PROJECT_NOUN.'">'.($edit_project > 0 ? '<a href="'.WEBSITE_URL.'/Project/projects.php?edit='.$row['projectid'].'">' : '').$row['project_label'].($edit_project > 0 ? '</a>' : '').'</td>';
        $report_data .=  '<td data-title="Description">' . ($row['description'] > 0 ? get_contact($dbc, $row['description']) : $row['description']) . '</td>';
        $report_data .=  '<td data-title="Date">' . $row['date'] . '</td>';
        $report_data .=  '<td data-title="Hours">' . ($row['src_table'] == 'material' ? $row['qty'] : $row['hours_tracked']) . '</td>';
        $report_data .=  "</tr>";
    }
    $report_data .= '<tr nobr="true" style="font-weight:bold;">';
    $report_data .=  '<td colspan="4">Total Hours'.($no_page === false ? ' (from all pages)' : '').'</td>';
    $report_data .=  '<td data-title="Total Hours">' . $dbc->query($total_sql)->fetch_assoc()['all_hours'] . '</td>';
    $report_data .=  "</tr>";

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