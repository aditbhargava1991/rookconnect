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
	$lt = $_POST['labour_type'];
	$lc = $_POST['labour_category'];
    $today_date = date('Y-m-d');
	$pdf_name = "Download/labour_$today_date.pdf";

	class MYPDF extends TCPDF {

		public function Header() {
			$image_file = WEBSITE_URL.'/img/fresh-focus-logo-dark.png';
			$this->SetFont('helvetica', '', 13);
            $this->Image($image_file, 0, 10, 60, '', 'PNG', '', 'T', false, 300, 'C', false, false, 0, false, false, false);
            $footer_text = 'Labour Reports';
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

    $html .= work_tickets($dbc, $lt, $lc, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output($pdf_name, 'F');
    track_download($dbc, 'report_pnl_labour', 0, WEBSITE_URL.'/Reports/Download/labour_'.$today_date.'.pdf', 'Labour Report');
    ?>

	<script>
		//window.location.replace('<?php echo $pdf_name; ?>');
        window.open('<?= $pdf_name; ?>', 'fullscreen=yes');
	</script>
    <?php
    $labour_category = $lc;
    $labour_type = $lt;
} ?>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

            <?php
            if (isset($_POST['search_email_submit'])) {
                $labour_type = $_POST['lt'];
                $labour_category = $_POST['lc'];
            }
            ?>

    <center>
		<div class="form-group col-sm-5">
			<label class="col-sm-4">Labour Type:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select a Type" name="lt" class="chosen-select-deselect form-control1" width="380">
				<option value=""></option>
				<?php
				$query = mysqli_query($dbc,"SELECT DISTINCT(labour_type) FROM `labour` ORDER BY `labour_type`");
				while($row = mysqli_fetch_array($query)) { ?>
					<option <?php if ($row['labour_type'] == $labour_type) { echo " selected"; } ?> value='<?php echo  $row['labour_type']; ?>' ><?php echo $row['labour_type']; ?></option>
				<?php } ?>
			</select>
			</div>
		</div>

		<div class="form-group col-sm-5">
			<label class="col-sm-4">Labour Category:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select a Category" name="lc" class="chosen-select-deselect form-control1" width="380">
				<option value=""></option>
				<?php
				$query = mysqli_query($dbc,"SELECT DISTINCT(category) FROM `labour` ORDER BY `category`");
				while($row = mysqli_fetch_array($query)) { ?>
					<option <?php if ($row['category'] == $labour_category) { echo " selected"; } ?> value='<?php echo  $row['category']; ?>' ><?php echo $row['category']; ?></option>
				<?php } ?>
			</select>
			</div>
		</div>

        <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
	</center>
        <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>

            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="labour_type" value="<?php echo $labour_type; ?>">
            <input type="hidden" name="labour_category" value="<?php echo $labour_category; ?>">
            <br><br>

            <?php
                echo work_tickets($dbc, $labour_type, $labour_category);
            ?>

        </form>
        
<?php
function work_tickets($dbc, $labour_type, $labour_category, $table_style = '', $table_row_style = '', $grand_total_style = '') {
    $report_data = '';

    $sql = "SELECT * FROM `labour` WHERE deleted = 0";
    if($labour_type != '') {
        $sql .= " AND labour_type='$labour_type'";
    }
    if($labour_category != '') {
        $sql .= " AND category = '$labour_category'";
    }

	$result = mysqli_query($dbc, $sql);

    $report_data .= '<table border="1px" class="table table-bordered" width="100%" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">';
    $report_data .= '<th>Labour Type</th>';
    $report_data .= '<th>Category</th>';
    $report_data .= '<th>Heading</th>';
    $report_data .= '<th>Salary</th>';
    $report_data .= '<th>Minimum Billable</th>';
    $report_data .= '<th>Estimated Hours</th>';
    $report_data .= '<th>Actual Hours</th>';
    $report_data .=  "</tr>";

    $odd_even = 0;
    while($row = mysqli_fetch_array( $result ))
    {
        $bg_class = $odd_even % 2 == 0 ? '' : 'background-color:#e6e6e6;';
        $report_data .= '<tr nobr="true" style="'.$bg_class.'">';
            $report_data .= '<td data-title="Type">' . $row['labour_type'] . '</td>';
            $report_data .= '<td data-title="Category">' . $row['category'] . '</td>';
            $report_data .= '<td data-title="Heading">' . $row['heading'] . '</td>';
            $report_data .= '<td data-title="Salary">' . $row['salary'] . '</td>';
            $report_data .= '<td data-title="Min. Billable">' . $row['minimum_billable'] . '</td>';
            $report_data .= '<td data-title="Est. Hours">' . $row['estimated_hours'] . '</td>';
            $report_data .= '<td data-title="Actual Hours">' . $row['actual_hours'] . '</td>';
        $report_data .=  "</tr>";

		$total_salary += (float)$row['salary'];
		$total_minimum_billable += (float)$row['minimum_billable'];
		$total_estimated_hours += (float)$row['estimated_hours'];
		$total_actual_hours += (float)$row['actual_hours'];
        $odd_even++;
    }

    $report_data .= '<tr nobr="true">';
    $report_data .= '<td colspan="3"><b>Total</b></td>';
    $report_data .= '<td><b>'.number_format((float)$total_salary, 2, '.', '').'</b></td>';
    $report_data .= '<td><b>'.number_format((float)$total_minimum_billable, 2, '.', '').'</b></td>';
    $report_data .= '<td><b>'.number_format((float)$total_estimated_hours, 2, '.', '').'</b></td>';
    $report_data .= '<td><b>'.number_format((float)$total_actual_hours, 2, '.', '').'</b></td>';
    $report_data .=  "</tr>";
    $report_data .= '</table>';

    return $report_data;
}
?>