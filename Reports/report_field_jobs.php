<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if(isset($_POST['printpdf'])) {
	$type = $_POST['report_type'];
	$jobid = $_POST['report_job'];
	$from_date = $_POST['report_from'];
	$until_date = $_POST['report_until'];
    $today_date = date('Y-m-d');
	$pdf_name = "Download/field_jobs_$today_date.pdf";
    $logo = str_replace(' ','%20',get_config($dbc, 'report_logo'));
    DEFINE(REPORT_LOGO_URL, $logo);

    class MYPDF extends TCPDF {

		  public function Header() {
			      $image_file = WEBSITE_URL.'/img/fresh-focus-logo-dark.png';
            if(!empty(REPORT_LOGO_URL)) {
                $image_file = WEBSITE_URL.'/Reports/download/'.REPORT_LOGO_URL;
            }
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
    $html .= work_tickets($dbc, $jobid, $from_date, $until_date, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output($pdf_name, 'F');
    track_download($dbc, 'report_field_jobs', 0, WEBSITE_URL.'/Reports/Download/field_jobs_'.$today_date.'.pdf', 'Field Job Report');
    ?>

	<script>
		window.location.replace('<?php echo $pdf_name; ?>');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $projectid = $jobpdf;
} ?>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

            <?php
			$search_job = '';
			$search_from = date('Y-m-01');
			$search_until = date('Y-m-d');

            if (isset($_POST['search_job'])) {
                $search_job = $_POST['search_job'];
            }
            if (isset($_POST['search_from'])) {
                $search_from = $_POST['search_from'];
            }
            if (isset($_POST['search_until'])) {
                $search_until = $_POST['search_until'];
            }
            ?>

    <center>
		<div class="form-group col-sm-5">
			<label class="col-sm-4">From:</label>
			<div class="col-sm-8"><input name="search_from" type="text" class="datepicker form-control" value="<?php echo $search_from; ?>"></div>
		</div>
		<div class="form-group col-sm-5">
			<label class="col-sm-4">Until:</label>
			<div class="col-sm-8"><input name="search_until" type="text" class="datepicker form-control" value="<?php echo $search_until; ?>"></div>
		</div>
		<div class="form-group col-sm-5">
			<label class="col-sm-4">Field Job:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select a Job" name="search_job" class="chosen-select-deselect form-control1" width="380">
				<option value=""></option>
				<?php
				$query = mysqli_query($dbc,"SELECT `job_number`, `jobid` FROM `field_jobs` ORDER BY `job_number`");
				while($row = mysqli_fetch_array($query)) { ?>
					<option <?php if ($row['jobid'] == $search_job) { echo " selected"; } ?> value='<?php echo  $row['jobid']; ?>' ><?php echo $row['job_number']; ?></option>
				<?php } ?>
			</select>
			</div>
		</div>
        <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
        <button type="button" onclick="window.location=''" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
	</center>
        <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>

            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="report_job" value="<?php echo $search_job; ?>">
            <input type="hidden" name="report_from" value="<?php echo $search_from; ?>">
            <input type="hidden" name="report_until" value="<?php echo $search_until; ?>">
            <br><br>

            <?php
                echo work_tickets($dbc, $search_job, $search_from, $search_until);
            ?>

        </form>

<?php
function work_tickets($dbc, $jobid, $from_date, $until_date, $table_style = '', $table_row_style = '', $grand_total_style = '') {
    $report_data = '';

    $sql = "SELECT fwt.`workticketid`, fwt.`wt_date`, fj.`clientid`, fs.`site_name`, fj.`job_number`, fwt.`attach_invoice`, fwt.`sub_total`, fwt.`crew_total`,
		fwt.`billable_reg_hour`, fwt.`billable_ot_hour`, fwt.`billable_travel_hour`, fwt.`material_total`, fwt.`equip_total`, fwt.`subextra_total`
		FROM `field_work_ticket` fwt LEFT JOIN `field_jobs` fj ON fwt.jobid=fj.jobid LEFT JOIN `field_sites` fs ON fj.siteid=fs.siteid WHERE fwt.deleted = 0";
    if($jobid != '') {
        $sql .= " AND fwt.jobid='$jobid'";
    }
    if($from_date != '') {
        $sql .= " AND fwt.wt_date >= '$from_date'";
    }
    if($until_date != '') {
        $sql .= " AND fwt.wt_date <= '$until_date'";
    }
	$result = mysqli_query($dbc, $sql);

    $report_data .= '<table border="1px" class="table table-bordered" width="100%" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">';
    $report_data .= '<th>Work Ticket#</th>';
    $report_data .= '<th>Date</th>';
    $report_data .= '<th>Customer</th>';
    $report_data .= '<th>Location</th>';
    $report_data .= '<th>Job#</th>';
    $report_data .= '<th>Invoice#</th>';
    $report_data .= '<th>Total Revenue</th>';
    $report_data .= '<th>Labour</th>';
    $report_data .= '<th>Emp. Hr.</th>';
    $report_data .= '<th>Material</th>';
    $report_data .= '<th>Equipment</th>';
    $report_data .= '<th>Other</th>';
    $report_data .=  "</tr>";

    $total_revenue = 0;
    $total_labour = 0;
    $total_emp_hr = 0;
    $total_material = 0;
    $total_equipment = 0;
    $total_other = 0;

    while($row = mysqli_fetch_array( $result ))
    {
		$crew_hours = array_sum(array_merge(explode(',',$row['billable_reg_hour']),explode(',',$row['billable_ot_hour']),explode(',',$row['billable_travel_hour'])));
        $report_data .= '<tr nobr="true">';
        $report_data .= '<td>'.$row['workticketid'].'</td>';
        $report_data .= '<td>'.$row['wt_date'].'</td>';
        $report_data .= '<td>'.get_client($dbc, $row['clientid']).'</td>';
        $report_data .= '<td>'.$row['site_name'].'</td>';
        $report_data .= '<td>'.$row['job_number'].'</td>';
        $report_data .= '<td>'.$row['attach_invoice'].'</td>';
        $report_data .= '<td>'.number_format((float)$row['sub_total'], 2, '.', '').'</td>';
        $report_data .= '<td>'.number_format((float)$row['crew_total'], 2, '.', '').'</td>';
        $report_data .= '<td>'.$crew_hours.'</td>';
        $report_data .= '<td>'.number_format((float)$row['material_total'], 2, '.', '').'</td>';
        $report_data .= '<td>'.number_format((float)$row['equip_total'], 2, '.', '').'</td>';
        $report_data .= '<td>'.number_format((float)$row['subextra_total'], 2, '.', '').'</td>';
        $report_data .=  "</tr>";

		$total_revenue += (float)$row['sub_total'];
		$total_labour += (float)$row['crew_total'];
		$total_emp_hr += $crew_hours;
		$total_material += (float)$row['material_total'];
		$total_equipment += (float)$row['equip_total'];
		$total_other += (float)$row['subextra_total'];
    }

    $report_data .= '<tr nobr="true">';
    $report_data .= '<td colspan="6">Total</td>';
    $report_data .= '<td>'.number_format((float)$total_revenue, 2, '.', '').'</td>';
    $report_data .= '<td>'.number_format((float)$total_labour, 2, '.', '').'</td>';
    $report_data .= '<td>'.number_format((float)$total_emp_hr, 2, '.', '').'</td>';
    $report_data .= '<td>'.number_format((float)$total_material, 2, '.', '').'</td>';
    $report_data .= '<td>'.number_format((float)$total_equipment, 2, '.', '').'</td>';
    $report_data .= '<td>'.number_format((float)$total_other, 2, '.', '').'</td></tr>';
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