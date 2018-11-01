<?php /* Ticket Inventory Transport Report */
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');

error_reporting(0);

if (isset($_POST['printpdf'])) {
    $startpdf = $_POST['startpdf'];
    $endpdf = $_POST['endpdf'];
    $businesspdf = $_POST['businesspdf'];
    $projectpdf = $_POST['projectpdf'];

    DEFINE('START_DATE', $startpdf);
    DEFINE('END_DATE', $endpdf);
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
            $footer_text = 'Inventory Transported <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE: Displays statistics of Inventory being Transported ".START_DATE." to ".END_DATE.".";
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

    $html .= report_tracking($dbc, $startpdf, $endpdf, $businesspdf, $projectpdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/download_tracker_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_inventory_transport', 0, WEBSITE_URL.'/Reports/Download/download_tracker_'.$today_date.'.pdf', 'Inventory Transported Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/download_tracker_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $startdate = $startpdf;
    $enddate = $endpdf;
    $businessid = $businesspdf;
    $projectid = $projectpdf;
} ?>

		<?php if (isset($_POST['search_submit'])) {
			$startdate = $_POST['starttime'];
			$enddate = $_POST['endtime'];
			$businessid = $_POST['businessid'];
			$projectid = $_POST['projectid'];
		}
		if($startdate == 0000-00-00) {
			$startdate = date('Y-m-01');
		}
		if($enddate == 0000-00-00) {
			$enddate = date('Y-m-d');
		} ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Displays statistics of Inventory being Transported from <?= $startdate ?> to <?= $enddate ?>.</div>
            <div class="clearfix"></div>
        </div>
		<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

        <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">

            <center>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">From:</label>
					<div class="col-sm-8">
						<input name="starttime" type="text" class="datepicker form-control" value="<?php echo $startdate; ?>">
					</div>
				</div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Until:</label>
					<div class="col-sm-8">
						<input name="endtime" type="text" class="datepicker form-control" value="<?php echo $enddate; ?>">
					</div>
				</div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4"><?= PROJECT_TILE ?>:</label>
					<div class="col-sm-8"><select data-placeholder="Select <?= PROJECT_NOUN ?>..." name="projectid" class="chosen-select-deselect form-control1" width="380">
						<option></option>
						<?php $projects = mysqli_query($dbc, "SELECT * FROM `project` WHERE `deleted`=0 AND `projectid` IN (SELECT `projectid` FROM `tickets` WHERE `deleted`=0)");
						while($row = $projects->fetch_assoc()) {
							echo "<option ".($row['projectid'] == $projectid ? 'selected' : '')." value='".$row['projectid']."'>".get_project_label($dbc, $row)."</option>";
						} ?>
					</select></div>
				</div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4"><?= BUSINESS_CAT ?>:</label>
					<div class="col-sm-8"><select data-placeholder="Select <?= BUSINESS_CAT ?>..." name="businessid" class="chosen-select-deselect form-control1" width="380">
						<option></option>
						<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT contactid, name FROM contacts WHERE category='".BUSINESS_CAT."' AND deleted=0 AND status=1")) as $row) {
							echo "<option ".($row['contactid'] == $businessid ? 'selected' : '')." value='".$row['contactid']."'>".$row['name']."</option>";
						} ?>
					</select></div>
				</div>
				<button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
				<button type="submit" name="search_submit" value="Search" class="btn brand-btn pull-right">Submit</button>
			</center>

            <input type="hidden" name="startpdf" value="<?php echo $startdate; ?>">
            <input type="hidden" name="endpdf" value="<?php echo $enddate; ?>">
            <input type="hidden" name="businesspdf" value="<?php echo $businessid; ?>">
            <input type="hidden" name="projectpdf" value="<?php echo $projectid; ?>">

			<div class="clearfix"></div>

			<?php echo report_tracking($dbc, $startdate, $enddate, $businessid, $projectid, '', '', ''); ?>

<?php function report_tracking($dbc, $startdate, $enddate, $businessid, $projectid, $table_style, $table_row_style, $grand_total_style) {
	$startdate = date('Y-m-d',strtotime($startdate));
	$enddate = date('Y-m-d',strtotime($enddate));
	$businessid = filter_var($businessid, FILTER_SANITIZE_STRING);
	$projectid = filter_var($projectid, FILTER_SANITIZE_STRING);

	$tickets = mysqli_query($dbc, "SELECT `tickets`.*, `inventory`.`piece_num`, `inventory`.`weight`, `inventory`.`weight_units`, `inventory`.`dimensions`, `inventory`.`dimension_units`, `origin`.`location_name` `origin_name`, `origin`.`to_do_date` `origin_date`, `dest`.`location_name` `dest_name`, `dest`.`to_do_date` `dest_date`, `project`.`projectid`,`projecttype`,`project`.`project_name`,`project`.`start_date` `project_date`,`project`.`businessid` `project_business`,`project`.`clientid` `project_client`,`project`.`status` `project_status`
	FROM `ticket_attached` `inventory` LEFT JOIN `tickets` ON `inventory`.`ticketid`=`tickets`.`ticketid`
	LEFT JOIN `ticket_schedule` `origin` ON `tickets`.`ticketid`=`origin`.`ticketid` AND `origin`.`type`='origin' AND `origin`.`deleted`=0
	LEFT JOIN `ticket_schedule` `dest` ON `tickets`.`ticketid`=`dest`.`ticketid` AND `dest`.`type`='destination' AND `dest`.`deleted`=0
	LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` AND `project`.`deleted`=0
	WHERE `inventory`.`deleted`=0 AND `tickets`.`deleted`=0 AND `inventory`.`src_table`='inventory' AND '$projectid' IN (`tickets`.`projectid`,'') AND '$businessid' IN (`tickets`.`businessid`,'') AND IFNULL(`origin`.`to_do_date`,'$startdate') BETWEEN '$startdate' AND '$enddate' AND IFNULL(`dest`.`to_do_date`,'$startdate') BETWEEN '$startdate' AND '$enddate'
		ORDER BY IFNULL(`origin`.`to_do_date`,'$enddate') ASC, IFNULL(`dest`.`to_do_date`,'$enddate') ASC, IFNULL(`tickets`.`to_do_date`,'$enddate') ASC, `tickets`.`ticketid` DESC");
	$ticket_edit = vuaed_visible_function($dbc, 'tickets', ROLE);
	if(mysqli_num_rows($tickets) > 0) {
		$report_data = '<table border="1px" class="table table-bordered" style="'.$table_style.'">
			<tr style="'.$table_row_style.'" nobr="true">
				<th>Work Order</th>
				<th>'.BUSINESS_CAT.'</th>
				<th>'.PROJECT_NOUN.'</th>
				<th>Shipment Date</th>
				<th>Origin</th>
				<th>Arrival Date</th>
				<th>Destination</th>
				<th># of Pieces</th>
				<th>Weight</th>
				<th>Dimensions</th>
			</tr>';
			while($row = mysqli_fetch_assoc($tickets)) {
				$ticket_label = get_ticket_label($dbc, $row);
				$project_label = $row['projectid'] > 0 ? get_project_label($dbc, ['projectid'=>$row['projectid'],'projecttype'=>$row['projecttype'],'project_name'=>$row['project_name'],'start_date'=>$row['project_date'],'businessid'=>$row['project_business'],'clientid'=>$row['project_client'],'status'=>$row['project_status']]) : '';
				$report_data .= '<tr style="'.$table_row_style.'" nobr="true">';
					if($ticket_edit == 1) {
						$report_data .= '<td data-title="Work Order"><a href="../Ticket/index.php?edit='.$row['ticketid'].'">'.$ticket_label.'</a></td>';
					} else {
						$report_data .= '<td data-title="Work Order">'.$ticket_label.'</td>';
					}
					$report_data .= '<td data-title="'.BUSINESS_CAT.'">'.get_client($dbc, $row['businessid']).'</td>
						<td data-title="'.PROJECT_NOUN.'">'.$project_label.'</td>
						<td data-title="Shipment Date">'.$row['origin_date'].'</td>
						<td data-title="Origin">'.$row['origin_name'].'</td>
						<td data-title="Arrival Date">'.$row['dest_date'].'</td>
						<td data-title="Destination">'.$row['dest_name'].'</td>
						<td data-title="# of Pieces">'.$row['piece_num'].'</td>
						<td data-title="Weight">'.$row['weight'].' '.$row['weight_units'].'</td>
						<td data-title="Dimensions">'.$row['dimensions'].' '.$row['dimension_units'].'</td>
					</tr>';
			}
		$report_data .= '</table>';
	} else {
		$report_data = '<h3>No Records Found.</h3>';
	}
	return $report_data;
} ?>
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