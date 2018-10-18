<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
$report_fields = explode(',', get_config($dbc, 'report_operation_fields'));

if(isset($_POST['printpdf'])) {
	$status = $_POST['report_status'];
	$from_date = $_POST['report_from'];
	$until_date = $_POST['report_until'];
	$search_extra_ticket = $_POST['search_extra_ticketpdf'];
    $today_date = date('Y-m-d');
	$pdf_name = "Download/site_work_orders_$today_date.pdf";

	class MYPDF extends TCPDF {

		public function Header() {
			$image_file = WEBSITE_URL.'/img/fresh-focus-logo-dark.png';
			$this->SetFont('helvetica', '', 13);
            $this->Image($image_file, 0, 10, 60, '', 'PNG', '', 'T', false, 300, 'C', false, false, 0, false, false, false);
            $footer_text = 'Field Job Reports';
            $this->writeHTMLCell(0, 0, 0 , 40, $footer_text, 0, 0, false, "R", true);
		}

		// Page footer
		public function Footer() {
			// Location at 15 mm from bottom
			$this->SetY(-15);
			$this->SetFont('helvetica', '', 9);
			$footer_text = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
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

	$html = 'Period Start: '.$from_date.'<br />';
	$html .= 'Period End: '.$until_date.'<br />';
    $html .= work_orders($dbc, $status, $from_date, $until_date, $search_extra_ticket, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output($pdf_name, 'F');
    track_download($dbc, 'report_site_work_orders', 0, WEBSITE_URL.'/Reports/Download/site_work_orders_'.$today_date.'.pdf', 'Field Job Report');
    ?>

	<script>
		window.location.replace('<?php echo $pdf_name; ?>');
	</script>
<?php } ?>


		<?php $search_status = (!empty($_GET['wo_type']) ? filter_var($_GET['wo_type'],FILTER_SANITIZE_STRING) : 'Approved');
		$search_from = '';
		$search_until = '';
		$search_extra_ticket = '';

		if (isset($_POST['search_extra_ticket'])) {
			$search_extra_ticket = $_POST['search_extra_ticket'];
		}
		if (isset($_POST['search_from'])) {
			$search_from = $_POST['search_from'];
		} else {
			$search_from = date('Y-m-01');
		}
		if (isset($_POST['search_until'])) {
			$search_until = $_POST['search_until'];
		} else {
			$search_until = date('Y-m-d');
		} ?>
        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <center><div class="form-group">
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
				<?php if(in_array('filter_staff_expenses',$report_fields)) { ?>
					<div class="col-sm-5">
						<label for="search_expenses" class="col-sm-4 control-label">Only <?= TICKET_TILE ?> with Expenses:</label>
						<div class="col-sm-8">
							<select data-placeholder="Select Option" name="search_material" class="chosen-select-deselect form-control">
								<option <?= $search_expenses == 'No' ? 'selected' : '' ?> value="No">Display All</option>
								<option <?= $search_expenses == 'Yes' ? 'selected' : '' ?> value="Yes">Only with Expenses</option>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if(in_array('filter_ticket_notes',$report_fields)) { ?>
					<div class="col-sm-5">
						<label for="search_notes" class="col-sm-4 control-label">Only <?= TICKET_TILE ?> with Notes:</label>
						<div class="col-sm-8">
							<select data-placeholder="Select Option" name="search_material" class="chosen-select-deselect form-control">
								<option <?= $search_notes == 'No' ? 'selected' : '' ?> value="No">Display All</option>
								<option <?= $search_notes == 'Yes' ? 'selected' : '' ?> value="Yes">Only with Notes</option>
							</select>
						</div>
					</div>
				<?php } ?>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">From:</label>
					<div class="col-sm-8"><input name="search_from" type="text" class="datepicker form-control" value="<?php echo $search_from; ?>"></div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Until:</label>
					<div class="col-sm-8"><input name="search_until" type="text" class="datepicker form-control" value="<?php echo $search_until; ?>"></div>
				</div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
			<button type="button" onclick="window.location=''" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button></div></center>
        <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>

            <input type="hidden" name="report_status" value="<?php echo $search_status; ?>">
            <input type="hidden" name="report_from" value="<?php echo $search_from; ?>">
            <input type="hidden" name="report_until" value="<?php echo $search_until; ?>">
            <input type="hidden" name="search_extra_ticketpdf" value="<?php echo $search_extra_ticket; ?>">
            <br><br>

            <?php
                echo work_orders($dbc, $search_status, $search_from, $search_until, $search_extra_ticket);
            ?>

        </form>

<?php
function work_orders($dbc, $status = 'Active', $from_date = '', $until_date = '', $search_extra_ticket = '', $table_style = '', $table_row_style = '', $grand_total_style = '') {
    $report_data = '';
	$sql = "SELECT `tickets`.*, `date_stamp` FROM `ticket_attached` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_attached`.`deleted`=0 AND `ticket_attached`.`src_table` IN ('Staff','Staff_Tasks') AND `ticket_attached`.`date_stamp` BETWEEN '$from_date' AND '$until_date' AND `tickets`.`deleted`=0 GROUP BY `ticketid`, `date_stamp` ORDER BY `date_stamp` ASC";
	$result = mysqli_query($dbc, $sql);
	
	if($result->num_rows == 0) {
		return "<h3>No Work Orders Found</h3>";
	}

    $report_data .= '<table border="1px" class="table table-bordered" width="100%" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">';
    $report_data .= '<th>Work Order #</th>
			<th>Site</th>
			<th>Staff & Crew</th>
			<th>Services</th>
			<th>Material</th>';
    $report_data .=  "</tr>";

    while($work_order = mysqli_fetch_array( $result ))
    {
		$crew_list = [];
		$crew_added = $dbc->query("SELECT `item_id`, `position`, `hours_tracked` FROM `ticket_attached` WHERE `src_table` IN ('Staff','Staff_Tasks') AND `deleted`=0 AND `ticketid`='{$work_order['ticketid']}' AND `date_stamp`='{$work_order['date_stamp']}'");
		while($crew = $crew_added->fetch_assoc()) {
			$crew_list[] = get_contact($dbc, $crew['item_id']).': '.$crew['position'].' - '.$crew['hours_tracked'];
		}
		$service_list = [];
		foreach(array_filter(explode(',',$work_order['serviceid'])) as $service) {
			$service = $dbc->query("SELECT * FROM `services` WHERE `serviceid`='$service'")->fetch_assoc();
			$service_list[] = $service['category'].': '.$service['heading'];
		}
		$material_list = [];
		$material_added = $dbc->query("SELECT `item_id`, `description`, `checked_out`, `created_by`, `qty` FROM `ticket_attached` WHERE `src_table` IN ('material') AND `deleted`=0 AND `ticketid`='{$work_order['ticketid']}' AND `date_stamp`='{$work_order['date_stamp']}'");
		while($material = $material_added->fetch_assoc()) {
			$material_list[] = ($material['item_id'] > 0 ? get_field_value('name','material','materialid',$material['item_id']) : $material['description']).' Qty: '.$material['qty'].' Added: '.$material['checked_out'].($material['created_by'] > 0 ? ' by '.get_contact($dbc, $material['created_by']) : '');
		}
        $report_data .= '<tr nobr="true">
			<td data-title="Work Order #:"><a href="../Ticket/index.php?edit='.$work_order['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\',\'auto\',true,true); return false;">'.get_ticket_label($dbc, $work_order).' on '.$work_order['date_stamp'].'</a></td>
			<td data-title="Site:">'.get_contact($dbc,$work_order['siteid']).'</td>
			<td data-title="Staff & Crew:">'.implode("<br />\n", $crew_list).'</td>
			<td data-title="Services:">'.implode("<br />\n", $service_list).'</td>
			<td data-title="Materials:">'.implode("<br />\n", $material_list).'</td></tr>';
    }

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