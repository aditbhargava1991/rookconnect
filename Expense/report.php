<?php /* Expenses Report */
include_once('../tcpdf/tcpdf.php');
include_once('report_function.php');
if (isset($_POST['printpdf'])) {
    $starttimepdf = $_POST['starttimepdf'];
    $endtimepdf = $_POST['endtimepdf'];

    DEFINE('START_DATE', $starttimepdf);
    DEFINE('END_DATE', $endtimepdf);
    DEFINE('EXPENSE_LOGO', get_config($dbc, 'expense_logo'));
    DEFINE('EXPENSE_FOOTER', html_entity_decode(get_config($dbc, 'expense_footer')));

	class MYPDF extends TCPDF {

		public function Header() {
            if(EXPENSE_LOGO != '') {
                $image_file = 'download/'.EXPENSE_LOGO;
                //$this->Image($image_file, 10, 10, 80, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
                $this->Image($image_file, 10, 10, '', '', '', '', 'T', false, '', '', false, false, 0, false, false, false);
            }

            $this->SetFont('helvetica', '', 13);
            $footer_text = '<p style="text-align:right;">Pay Period From '.START_DATE.' To '.END_DATE.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);
		}

		// Page footer
		public function Footer() {
            if(EXPENSE_FOOTER != '') {
            $this->SetY(-27);
            $this->SetFont('helvetica', 'I', 9);
            $footer_text = '<span style="text-align:center;">'.EXPENSE_FOOTER.'</span>';
            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
            }

			// Position at 15 mm from bottom
			$this->SetY(-12);
			$this->SetFont('helvetica', 'I', 9);
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

	$pdf->AddPage();
    $pdf->SetFont('helvetica', '', 9);

    $html = report_expense($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('download/expense_'.$today_date.'.pdf', 'F');
    ?>

	<script type="text/javascript" language="Javascript">
	window.location.replace('download/expense_<?php echo $today_date;?>.pdf');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
} ?>

<body>
<?php include_once ('../navigation.php');
?>

<form name="form_sites" method="post" action="" class="form-inline" role="form">
	<?php
	if (isset($_POST['search_email_submit'])) {
		$starttime = $_POST['starttime'];
		$endtime = $_POST['endtime'];
	} else {
		if (!isset($starttime)) {
			$starttime = date('Y-m-01');
			$endtime = date('Y-m-d');
		}
	}

	if($starttime == 0000-00-00) {
		$starttime = date('Y-m-01');
	}

	if($endtime == 0000-00-00) {
		$endtime = date('Y-m-d');
	}
	?>
	<br>
	<div class="form-group ">
		<div class="col-sm-4">
			<label for="site_name" class="col-sm-12 control-label">From:</label>
		</div>
		<div class="col-sm-8">
			<input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>">
		</div>
	</div>

	  <!-- end time -->
	<div class="form-group until">
		<div class="col-sm-4">
			<label for="site_name" class="col-sm-12 control-label">Until:</label>
		</div>
		<div class="col-sm-8" >
			<input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>">
		</div>
	</div>

	<button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
	<br>

	<input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
	<input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">

	<button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
	<br><br>

</form>

<?php
	echo report_expense($dbc, $starttime, $endtime, '', '', '', '');
?>