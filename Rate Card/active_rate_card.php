<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('rate_card');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (!empty($_GET['action'])) {
	class MYPDF extends TCPDF {

		//Page header
		public function Header() {
			$image_file = WEBSITE_URL.'/img/fresh-focus-logo-dark.png';
			$this->Image($image_file, 10, 10, 60, '', 'PNG', '', 'C', false, 300, '', false, false, 0, false, false, false);
		}

		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			$this->SetY(-20);
			$footer_file = WEBSITE_URL.'/img/letterhead_footer.png';
			$this->Image($footer_file, '', '', 120, '', 'PNG', '', 'C', false, 300, '', false, false, 0, false, false, false);
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

	$html = '';

	/* Pagination Counting */
	$rowsPerPage = 25;
	$pageNum = 1;

	if(isset($_GET['page'])) {
		$pageNum = $_GET['page'];
	}

	$offset = ($pageNum - 1) * $rowsPerPage;

	$query_check_credentials = "SELECT r.*, c.name FROM rate_card r, contacts c WHERE r.clientid = c.contactid AND r.deleted = 0 AND r.on_off = 1 LIMIT $offset, $rowsPerPage";
	$query = "SELECT count(r.*) as numrows FROM rate_card r, contacts c WHERE r.clientid = c.contactid AND r.deleted = 0 AND r.on_off = 1";

	$result = mysqli_query($dbc, $query_check_credentials);

	$num_rows = mysqli_num_rows($result);
	if($num_rows > 0) {
		// Added Pagination //
		echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
		// Pagination Finish //
		$html .= '<table border="1px" style="padding:3px; border:1px solid black;">';
		$html .= '<tr style="background-color:grey; color:black;">
			<th>Client</th>
			<th>Rate Card Name</th>
			<th>Total Cost</th>
			<th>Last Edited</th>
			</tr>';
	} else {
		$html .= "<h2>No Record Found.</h2>";
	}
	while($row = mysqli_fetch_array( $result )) {
		$html .= '<tr nobr="true">';
		$html .= '<td data-title="Unit Number">' . decryptIt($row['name']) . '</td>';
		$html .= '<td data-title="Unit Number">' . $row['rate_card_name'] . '</td>';
		$html .= '<td data-title="Unit Number">$' . $row['total_price'] . '</td>';
		$who_added = get_staff($dbc, $row['who_added']);
		$html .= '<td data-title="Unit Number">' . $who_added .' Edited On '. $row['when_added']. '</td>';
		$html .= '</tr>';
	}

	$today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/active_ratecard_'.$today_date.'.pdf', 'F');
	?>

	<script type="text/javascript" language="Javascript">
	window.location.replace('active_rate_card.php');
	window.open('Download/active_ratecard_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>

<?php }

?>
<script type="text/javascript">

</script>

</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
	<div class="row">

		<h1>Active Rate Cards
		<?php
		if(config_visible_function($dbc, 'rate_card') == 1) {
			echo '<a href="field_config_rate_card.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><br><br>';
		}
		?></h1>

		<a href='company_active_rate_card.php'><button type="button" class="btn brand-btn mobile-block" >My Companies Rate Card</button></a>
		<a href='active_rate_card.php'><button type="button" class="btn brand-btn mobile-block active_tab" >Customer Specific Rate Card</button></a>

		<br><br>

		<a href='active_rate_card.php'><button type="button" class="btn brand-btn mobile-block active_tab" >Active Rate Cards</button></a>
		<a href='view_rate_card.php'><button type="button" class="btn brand-btn mobile-block" >Current Rate Card Status</button></a>
		<?php if(vuaed_visible_function($dbc, 'rate_card') == 1) { ?>
		<a href='add_rate_card.php'><button type="button" class="btn brand-btn mobile-block" >Add Rate Card</button></a>
		<?php } ?>
		<a href="<?php echo WEBSITE_URL; ?>/home.php"	class="btn brand-btn pull-right">Back</a>
		<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
		<br><div id='no-more-tables'>

		<?php

		echo '<a href="active_rate_card.php?action=printpdf" class="btn brand-btn pull-right">Print PDF</a>';

		/* Pagination Counting */
		$rowsPerPage = 25;
		$pageNum = 1;

		if(isset($_GET['page'])) {
			$pageNum = $_GET['page'];
		}

		$offset = ($pageNum - 1) * $rowsPerPage;

		$query_check_credentials = "SELECT r.*, c.name FROM rate_card r, contacts c WHERE r.clientid = c.contactid AND r.deleted = 0 AND r.on_off = 1 LIMIT $offset, $rowsPerPage";
		$query = "SELECT count(c.name) as numrows FROM rate_card r, contacts c WHERE r.clientid = c.contactid AND r.deleted = 0 AND r.on_off = 1";

		$result = mysqli_query($dbc, $query_check_credentials);

		$num_rows = mysqli_num_rows($result);
		if($num_rows > 0) {
			// Added Pagination //
			echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
			// Pagination Finish //

			echo '<table class="table table-bordered table-striped">';
			echo '<thead>';
			echo '<tr class="hidden-xs hidden-sm">
				<th>Client</th>
				<th>Rate Card Name</th>
				<th>Total Cost</th>
				<th>Last Edited</th>
				</tr></thead>';
		} else {
			echo "<h2>No Record Found.</h2>";
		}
		while($row = mysqli_fetch_array( $result )) {
			echo '<tr>';
			echo '<td data-title="Client">' . decryptIt($row['name']) . '</td>';
			echo '<td data-title="Rate Card Name">' . $row['rate_card_name'] . '</td>';
			echo '<td data-title="Total Cost">$' . $row['total_price'] . '</td>';
			$who_added = get_staff($dbc, $row['who_added']);
			echo '<td data-title="Last Edited">' . $who_added .' Edited On '. $row['when_added']. '</td>';
			echo '</tr>';

			echo "</tr>";
		}

		echo '</table>';
		// Added Pagination //
		echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
		// Pagination Finish //
		?>
		<a href="<?php echo WEBSITE_URL; ?>/home.php"	class="btn brand-btn pull-right">Back</a>
		<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
		</div>
	</div>
</div>

<?php include ('../footer.php'); ?>
