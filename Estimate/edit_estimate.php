<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('estimate');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
$main_page = isset($_POST['referer']) ? $_POST['referer'] : $_SERVER['HTTP_REFERER'];
if(strpos($main_page,'/estimate.php') === false && strpos($main_page,'/cost_estimate.php') === false) {
	$main_page = (tile_visible($dbc, 'cost_estimate') ? 'cost_estimate.php' : 'estimate.php');
}

if (isset($_POST['submit'])) {
	$estimateid = filter_var($_POST['estimateid'],FILTER_SANITIZE_STRING);
	$qd = htmlentities($_POST['estimate_data']);
	$estimate_data = filter_var($qd,FILTER_SANITIZE_STRING);

	$before_change = capture_before_change($dbc, 'estimate', 'estimate_data', 'estimateid', $estimateid);

	$query_update_report = "UPDATE `estimate` SET `estimate_data` = '$estimate_data' WHERE `estimateid` = '$estimateid'";
	$result_update_report = mysqli_query($dbc, $query_update_report);

	$history = capture_after_change('estimate_data', $estimate_data);
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT logo FROM field_config_estimate"));
	$logo = $get_field_config['logo'];
	DEFINE('QUOTE_LOGO', $logo);

	class MYPDF extends TCPDF {

		//Page header
		public function Header() {
			$image_file = 'download/'.QUOTE_LOGO;
			$this->Image($image_file, 10, 10, 60, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
			$this->SetFont('helvetica', '', 8);
			$header_text = '';
			$this->writeHTMLCell(0, 0, '', '', $header_text, 0, 0, false, "L", "R",true);
		}

		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			$this->SetY(-15);
			$this->SetFont('helvetica', 'I', 8);
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

	$pdf_html = $_POST['estimate_data'];

	$pdf->writeHTML($pdf_html, true, false, true, false, '');
	$pdf->Output('download/estimate_'.$estimateid.'.pdf', 'F');

	echo '<script type="text/javascript"> window.location.replace("'.$main_page.'"); </script>';

}
?>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
	<div class="row">
		<form id="form1" name="form1" method="post" action="edit_estimate.php" enctype="multipart/form-data" class="form-horizontal" role="form">

		<?php
			$profit_loss = '';
			$budget = '';
			$type = $_GET['type'];
			$estimateid = $_GET['estimateid'];
			if($_GET['type'] == 'profit_loss') {
				$profit_loss = 'active_tab';
			}
			if($_GET['type'] == 'budget') {
				$budget = 'active_tab';
			}
		?>
		<input type="hidden" name="referer" value="<?php echo $main_page; ?>" />
		<a href='edit_estimate.php?estimateid=<?php echo $estimateid;?>&type=profit_loss'><button type="button" class="btn brand-btn mobile-block <?php echo $profit_loss; ?>" >By Heading</button></a>&nbsp;&nbsp;
		<a href='edit_estimate.php?estimateid=<?php echo $estimateid;?>&type=budget'><button type="button" class="btn brand-btn mobile-block <?php echo $budget; ?>" >By Service</button></a>&nbsp;&nbsp;
		 <br /><br />
		<a href="<?php echo $main_page; ?>" class="btn config-btn">Back to Dashboard</a>
		<br /><br />

			<?php
			$estimate_all = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT *  FROM estimate WHERE estimateid='$estimateid'"));
			?>
			<input type="hidden" name="estimateid" id="estimateid" value="<?php echo $estimateid; ?>">

			<?php if($type == 'summary') { ?>
			<div class="form-group">
				<label for="additional_note" class="col-sm-2 control-label"><?= ESTIMATE_TILE ?>:</label>
				<div class="col-sm-10">
					<textarea name="estimate_data" rows="15" cols="50" class="form-control"><?php echo $estimate_all['estimate_data']; ?></textarea>
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-12 clearfix">
					<br /><br />
					<a href="<?php echo $main_page; ?>" class="btn brand-btn pull-right">Back</a>
					<br /><br />
					<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-8">
					<!-- <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button> -->
				</div>
			</div>
			<?php } ?>

			<?php if($type == 'profit_loss') { ?>
				<!--<a href="estimate.php" class="btn config-btn">Back to Dashboard</a>
				<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back to Dashboard</a>-->
				<div id='no-more-tables'>
					<table class="table table-bordered">
						<tr class="hidden-xs hidden-sm">
							<th>Type</th>
							<th>Heading</th>
							<th>Description</th>
							<th>U of M</th>
							<th>Quantity</th>
							<th>Cost</th>
							<th>% Margin</th>
							<th><?= ESTIMATE_TILE ?> Price</th>
							<th>$ Profit</th>
							<th>Total</th>
						</tr>
						<?php echo $estimate_all['review_profit_loss']; ?>
						<tr>
							<td data-title="Type">Total <?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Price</td>
							<td data-title="Heading"></td>
							<td data-title="Description"></td>
							<td data-title="U of M"></td>
							<td data-title="Quantity"></td>
							<td data-title="Cost">$<?php echo $estimate_all['financial_cost']; ?></td>
							<td data-title="Estimate Margin"><?php echo $estimate_all['financial_margin']; ?>%</td>
							<td data-title="Estimate Price">$<?php echo $estimate_all['financial_price']; ?></td>
							<td data-title="Estimate Profit">$<?php echo $estimate_all['financial_profit']; ?></td>
							<td data-title="Total">$<?php echo $estimate_all['total_price']; ?></td>
						</tr>
					</table>
				</div>
				<!--<a href="estimate.php"	class="btn brand-btn pull-right">Back</a>-->
				<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>
			<?php } ?>

			<?php if($type == 'budget') { ?>
				<!--<a href="estimate.php" class="btn config-btn">Back to Dashboard</a>
				<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				<?php
				$budget_price = explode('*#*', $estimate_all['budget_price']);
				//echo '<h3>Total Budget : $'.$budget_price[16].'</h3><br>';
				//echo '<h3>Total Estimate : $'.$estimate_all['total_price'].'</h3><br>';
				?><div id='no-more-tables'>
					<table class="table table-bordered">
						<tr class="hidden-xs hidden-sm">
							<th>Type</th>
							<th>% Margin</th>
							<th>$ Profit</th>
							<th>Total</th>
						</tr>
						<?php echo $estimate_all['review_budget']; ?>
						<tr>
							<td data-title="Type">Total</td>
							<td data-title="Estimate Margin"><?php echo $estimate_all['financial_margin']; ?>%</td>
							<td data-title="Estimate Profit">$<?php echo $estimate_all['financial_profit']; ?></td>
							<td data-title="Estimate Price">$<?php echo $estimate_all['total_price']; ?></td>
						</tr>
					</table>
				</div>
				<!--<a href="estimate.php"	class="btn brand-btn pull-right">Back</a>-->
				<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>
			<?php } ?>
		</form>

	</div>
</div>

<?php include ('../footer.php'); ?>
