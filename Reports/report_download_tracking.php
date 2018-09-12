<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');

error_reporting(0);

if (isset($_POST['printpdf'])) {
    $startpdf = $_POST['startpdf'];
    $endpdf = $_POST['endpdf'];
    $staffpdf = $_POST['staffpdf'];

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
            $footer_text = 'Download Tracker <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE: Displays statistics of reports downloaded from ".START_DATE." to ".END_DATE.".";
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

    $html .= report_tracking($dbc, $startpdf, $endpdf, $staffpdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/download_tracker_'.$today_date.'.pdf', 'F');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/download_tracker_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $startdate = $startpdf;
    $enddate = $endpdf;
    $staffid = $staffpdf;
} ?>

		<?php if (isset($_POST['search_submit'])) {
			$startdate = $_POST['starttime'];
			$enddate = $_POST['endtime'];
			$staffid = $_POST['staff'];
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
            Displays statistics of HR Forms and Manuals downloaded<!-- from <?= $startdate ?> to <?= $enddate ?>-->.</div>
            <div class="clearfix"></div>
        </div>
		<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

        <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">

            <center>
				<div class="col-sm-5">
					<label class="col-sm-4">From:</label>
					<div class="col-sm-8">
						<input name="starttime" type="text" class="datepicker form-control" value="<?php echo $startdate; ?>">
					</div>
				</div>
				<div class="col-sm-5">
					<label class="col-sm-4">Until:</label>
					<div class="col-sm-8">
						<input name="endtime" type="text" class="datepicker form-control" value="<?php echo $enddate; ?>">
					</div>
				</div>
				<div class="col-sm-5">
					<label class="col-sm-4">Staff:</label>
					<div class="col-sm-8"><select data-placeholder="Select Staff..." name="staff" class="chosen-select-deselect form-control1" width="380">
                       <option value=''></option>
						<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status=1")) as $row) {
							echo "<option ".($row['contactid'] == $staffid ? 'selected' : '')." value='".$row['contactid']."'>".$row['first_name'].' '.$row['last_name']."</option>";
						} ?>
					</select></div>
				</div>
            <button type="submit" name="search_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></center>

            <input type="hidden" name="startpdf" value="<?php echo $startdate; ?>">
            <input type="hidden" name="endpdf" value="<?php echo $enddate; ?>">
            <input type="hidden" name="staffpdf" value="<?php echo $staffid; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
			<div class="clearfix"></div>

			<?php echo report_tracking($dbc, $startdate, $enddate, $staffid, '', '', ''); ?>

<?php
function report_tracking($dbc, $startdate, $enddate, $staffid, $table_style, $table_row_style, $grand_total_style) {
	$clause = '';
	if($startdate != '') {
		$clause .= " AND `today_date` >= '$startdate'";
	}
	if($enddate != '') {
		$enddatetime = date('Y-m-d',strtotime($enddate.' +1 day'));
		$clause .= " AND `today_date` <= '$enddatetime'";
	}
	if($staffid != '') {
		$clause .= " AND `staffid` = '$staffid'";
	}
	$downloads = mysqli_query($dbc, "SELECT * FROM `download_tracking` WHERE `deleted`=0".$clause);

	if(mysqli_num_rows($downloads) > 0) {
		$report_data = '<table border="1px" class="table table-bordered" style="'.$table_style.'">
			<tr style="'.$table_row_style.'" nobr="true">
				<th width="20%">Staff</th>
				<th width="15%">Date</th>
				<th width="15%">Time</th>
				<th width="20%">Name</th>
				<th width="30%">Description</th>
			</tr>';
			while($row = mysqli_fetch_assoc($downloads)) {
				$label = '';
				if($row['table'] == 'hr') {
					$form = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `hr` WHERE `hrid`='".$row['tableid']."'"));
					$label = $form['third_heading'] == '' ? ($form['sub_heading'] == '' ? $form['heading_number'].' - '.$form['heading'] : $form['sub_heading_number'].' - '.$form['sub_heading']) : $form['third_heading_number'].' - '.$form['third_heading'];
				} else if($row['table'] == 'manuals') {
					$manual = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `manuals` WHERE `manualtypeid`='".$row['tableid']."'"));
					$label = $manual['third_heading'] == '' ? ($manual['sub_heading'] == '' ? $manual['heading_number'].' - '.$manual['heading'] : $manual['sub_heading_number'].' - '.$manual['sub_heading']) : $manual['third_heading_number'].' - '.$manual['third_heading'];
				}
				$report_data .= '<tr>
					<td data-title="Staff">'.get_contact($dbc, $row['staffid']).'</td>
					<td data-title="Date">'.date('Y-m-d', strtotime($row['today_date'])).'</td>
					<td data-title="Time">'.date('g:i a', strtotime($row['today_date'])).'</td>
					<td data-title="Name"><a target= "_blank" href="'.$row['download_link'].'">View Report</a></td>
					<td data-title="Description">'.html_entity_decode($row['description']).'</td>
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