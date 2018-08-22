<?php
/*
Dashboard
*/

if(!empty($_GET['archiveid'])) {
	$id = $_GET['archiveid'];
    $date_of_archival = date('Y-m-d');
	$rate_card = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE `companyrcid`='$id'"));
	$sql = "UPDATE `company_rate_card` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `rate_card_name`='".$rate_card['rate_card_name']."' AND IFNULL(`rate_categories`,'')='".$rate_card['rate_categories']."'";
	mysqli_query($dbc, $sql);
}
if (!empty($_GET['action'])) :
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

    $query_check_credentials = "SELECT r.*, c.name FROM `company_rate_card` r, `contacts` c WHERE r.`clientid` = c.`contactid` AND r.`deleted` = 0 AND r.`on_off` = 1 AND r.`rate_card_name` != '' LIMIT $offset, $rowsPerPage";
    $query = "SELECT COUNT(r.*) as numrows FROM `company_rate_card` r, `contacts` c WHERE r.`clientid` = c.`contactid` AND r.`deleted` = 0 AND r.`on_off` = 1 AND r.`rate_card_name` != ''";

    $result = mysqli_query($dbc, $query_check_credentials);

    $num_rows = mysqli_num_rows($result);
    if($num_rows > 0) {
        // Added Pagination //
        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
        // Pagination Finish //
        $html .= '<table class="table table-striped" border="1px" style="padding:3px; border:1px solid black;">';
        $html .= '<thead><tr style="background-color:grey; color:black;">
            <th>Client</th>
            <th>Rate Card Name</th>
            <th>Rate Card Category</th>
            <th>Total Cost</th>
            <th>Last Edited</th>
            </tr></thead>';
    } else {
        $html .= "<h2>No Record Found.</h2>";
    }
    while($row = mysqli_fetch_array( $result )) {
        $html .= '<tr nobr="true">';
        $html .= '<td data-title="Unit Number">' . decryptIt($row['name']) . '</td>';
        $html .= '<td data-title="Unit Number">' . $row['rate_card_name'] . '</td>';
        $html .= '<td data-title="Unit Number">' . $row['rate_categories'] . '</td>';
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
	window.location.replace('?type=company&card=company&status=active');
	window.open('Download/Rate_Card_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>

<?php endif; ?>

<div id='no-more-tables'>
<a href="?type=company&card=company&status=active&action=printpdf" class="btn brand-btn pull-right">Print PDF</a>
<?php
$db_config = get_config($dbc, 'company_db_rate_fields');
if(str_replace(',','',$db_config) == '') {
	$db_config = ',card,total_cost,';
}

/* Pagination Counting */
$rowsPerPage = 25;
$pageNum = 1;

if(isset($_GET['page'])) {
	$pageNum = $_GET['page'];
}
$universal_categories = false;
$univseral_id = '';
if(strpos(get_config($dbc, 'universal_rate_fields'),',category,') !== FALSE) {
	$universal_categories = true;
	$rowsPerPage = 24;
	$universal_id = mysqli_fetch_array(mysqli_query($dbc, "SELECT MIN(`companyrcid`) id FROM `company_rate_card` WHERE `rate_card_name`='' AND IFNULL(`rate_categories`,'') = '".$_GET['category']."'"))['id'];
}

$offset = ($pageNum - 1) * $rowsPerPage;

$query_check_credentials = "SELECT MIN(companyrcid) AS id, `rate_card_name`, IFNULL(`rate_categories`,''), SUM(`profit` / (`margin` / 100)) AS price, `start_date`, `end_date`, `alert_date`, `alert_staff` FROM `company_rate_card` WHERE `deleted`=0 AND `rate_card_name` != '' AND IFNULL(`rate_categories`,'') LIKE '".$_GET['category']."%' GROUP BY `rate_card_name`, IFNULL(`rate_categories`,'') LIMIT $offset, $rowsPerPage";
$query = "SELECT count(DISTINCT CONCAT(`rate_card_name`,'|',IFNULL(`rate_categories`,''))) AS numrows FROM `company_rate_card` WHERE `deleted`=0 AND `rate_card_name` != '' AND IFNULL(`rate_categories`,'') LIKE '".$_GET['category']."%' GROUP BY `rate_card_name`, `rate_categories`";

$result = mysqli_query($dbc, $query_check_credentials);

$num_rows = mysqli_num_rows($result);
if($num_rows > 0 || $universal_categories) {
	// Added Pagination //
	echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
	// Pagination Finish //

	echo "<table class='table table-bordered table-striped'>";
	echo "<thead>";
	echo '<tr class="hidden-xs hidden-sm">
		'.(strpos($db_config,',card,')!==false?'<th>Rate Card Name</th>':'').'
		'.(strpos($db_config,',category,')!==false?'<th>Rate Card Category</th>':'').'
		'.(strpos($db_config,',start_end_dates,')!==false?'<th>Start Date</th><th>End Date</th>':'').'
		'.(strpos($db_config,',alert_date,')!==false?'<th>Alert Date</th>':'').'
		'.(strpos($db_config,',alert_staff,')!==false?'<th>Alert Staff</th>':'').'
		'.(strpos($db_config,',total_cost,')!==false?'<th>Total Cost</th>':'').'
		<th>Function</th>
		</tr></thead>';

	/*if($universal_categories) {
		echo '<tr>';
		if(strpos($db_config,',card,')!==false) {
			echo '<td data-title="Rate Card Name">Universal Rates</td>';
		}
		if(strpos($db_config,',category,')!==false) {
			echo '<td data-title="Rate Card Category">' . (empty($_GET['category']) ? 'Universal' : $_GET['category']) . '</td>';
		}
		if(strpos($db_config,',total_cost,')!==false) {
			echo '<td data-title="Total Cost"></td>';
		}
		echo '<td data-title="Function">';
		if(vuaed_visible_function($dbc, 'rate_card')) {
			echo '<a href="?card=universal&type=universal&status=add&category='.$_GET['category'].'&id='.$universal_id.'">Edit</a>';
		}
		echo '</td></tr>';
	}*/
	while($row = mysqli_fetch_array( $result )) {
		echo '<tr>';
		if(strpos($db_config,',card,')!==false) {
			echo '<td data-title="Rate Card Name">' . $row['rate_card_name'] . '</td>';
		}
		if(strpos($db_config,',category,')!==false) {
			echo '<td data-title="Rate Card Category">' . $row['rate_categories'] . '</td>';
		}
		if(strpos($db_config,',start_end_dates,') !== false) {
			echo '<td data-title="Start Date">' . $row['start_date'] . '</td>';
			echo '<td data-title="End Date">' . $row['end_date'] . '</td>';
		}
		if(strpos($db_config,',alert_date,') !== false) {
			echo '<td data-title="Alert Date">' . $row['alert_date'] . '</td>';
		}
		if(strpos($db_config,',alert_staff,') !== false) {
			echo '<td data-title="Alert Staff">';
			$staff_list = [];
			foreach(explode(',',$row['alert_staff']) as $staffid) {
				if($staffid > 0) {
					$staff_list[] = get_contact($dbc, $staffid);
				}
			}
			echo implode(', ',$staff_list);
			echo '</td>';
		}
		if(strpos($db_config,',total_cost,')!==false) {
			echo '<td data-title="Total Cost">$' . round($row['price'],2) . '</td>';
		}
		echo '<td data-title="Function">';
		if(vuaed_visible_function($dbc, 'rate_card')) {
			echo '<a href="?type=company&card=company&status=add&id='.$row['id'].'">Edit</a> | ';
			echo '<a href="?type=company&card=company&status=current&archiveid='.$row['id'].'" onclick="return confirm(\'Are you sure you want to archive this rate card?\');">Archive</a>';
		} else {
			echo '<a href="?type=company&card=company&status=show&id='.$row['id'].'">View</a>';
		}
		echo '</td></tr>';

		echo "</tr>";
	}

	echo '</table>';
	// Added Pagination //
	echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
	// Pagination Finish //
} else {
	echo "<h2>No Record Found.</h2>";
}
?>
</div>