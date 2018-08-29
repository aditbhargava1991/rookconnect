<?php
/*
Dashboard
*/
if (!empty($_GET['action'])):
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

    $query_check_credentials = "SELECT r.*, c.name FROM rate_card r, contacts c WHERE r.clientid = c.contactid AND r.deleted = 0 AND r.on_off = 1 AND '$contact_category' IN (c.`category`,'') LIMIT $offset, $rowsPerPage";
    $query = "SELECT count(r.*) as numrows FROM rate_card r, contacts c WHERE r.clientid = c.contactid AND r.deleted = 0 AND r.on_off = 1 AND '$contact_category' IN (c.`category`,'')";

    $result = mysqli_query($dbc, $query_check_credentials);

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `field_config_ratecard`"));
    $db_config = ','.$get_field_config['dashboard_fields'].',';

    $num_rows = mysqli_num_rows($result);
    if($num_rows > 0) {
        // Added Pagination //
        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
        // Pagination Finish //
        $html .= '<table border="1px" style="padding:3px; border:1px solid black;">';
        $html .= '<tr style="background-color:grey; color:black;">
            <th>Client</th>
            <th>Rate Card Name</th>'.
            (strpos($db_config, ',start_end_dates,') !== FALSE ? '<th>Start Date</th><th>End Date</th>' : '').
            (strpos($db_config, ',alert_date,') !== FALSE ? '<th>Alert Date</th>' : '').
            (strpos($db_config, ',alert_staff,') !== FALSE ? '<th>Alert Staff</th>' : '').
            (strpos($db_config, ',created_by,') !== FALSE ? '<th>Created By</th>' : '').
            '<th>Total Cost</th>
            <th>Last Edited</th>
            </tr>';
    } else {
        $html .= "<h2>No Record Found.</h2>";
    }
    while($row = mysqli_fetch_array( $result )) {
        $html .= '<tr nobr="true">';
        $html .= '<td data-title="Unit Number">' . decryptIt($row['name']) . '</td>';
        $html .= '<td data-title="Unit Number">' . $row['rate_card_name'] . '</td>';
        if(strpos($db_config, ',start_end_dates,') !== FALSE) {
            $html .= '<td data-title="Start Date">'.$row['start_date'].'</td>';
            $html .= '<td data-title="End Date">'.$row['end_date'].'</td>';
        }
        if(strpos($db_config, ',alert_date,') !== FALSE) {
            $html .= '<td data-title="Alert Date">'.$row['alert_date'].'</td>';
        }
        if(strpos($db_config, ',alert_staff,') !== FALSE) {
            $html .= '<td data-title="Alert Staff">';
            $staff_list = [];
            foreach(explode(',',$row['alert_staff']) as $staffid) {
                if($staffid > 0) {
                    $staff_list[] = get_contact($dbc, $staffid);
                }
            }
            $html .= implode(', ',$staff_list);
            $html .= '</td>';
        }
        if(strpos($db_config, ',created_by,') !== FALSE) {
            $html .= '<td data-title="Created By">'.get_contact($dbc, $row['created_by']).'</td>';
        }
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

<?php endif; ?>

<div id='no-more-tables'>
	<!-- <a href="active_rate_card.php?action=printpdf" class="btn brand-btn pull-right">Print PDF</a> -->
	<?php
	/* Pagination Counting */
	$rowsPerPage = 25;
	$pageNum = 1;

	if(isset($_GET['page'])) {
		$pageNum = $_GET['page'];
	}

	$offset = ($pageNum - 1) * $rowsPerPage;

	$contact_category = filter_var($_GET['category'],FILTER_SANITIZE_STRING);
	$query_check_credentials = "SELECT * FROM rate_card_holiday_pay WHERE deleted = 0 LIMIT $offset, $rowsPerPage";
	$query = "SELECT count(ratecardholidayid) as numrows FROM rate_card_holiday_pay WHERE deleted = 0";

	$result = mysqli_query($dbc, $query_check_credentials);

    $db_config = ','.get_config($dbc, 'holiday_db_rate_fields').',';

	if(mysqli_num_rows($result) > 0):
		// Added Pagination //
		echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
		// Pagination Finish // ?>

		<table class="table table-bordered table-striped">
        <thead>
		<tr class="hidden-xs hidden-sm">
			<?php if(strpos($db_config,',holiday_rate_type,') !== FALSE) { ?>
			<th>Holiday Rate Type</th>
			<?php } ?>
			<?php if(strpos($db_config,',holiday_rate_position,') !== FALSE) { ?>
				<th>Position</th>
			<?php } ?>
			<?php if(strpos($db_config,',holiday_rate_staff,') !== FALSE) { ?>
				<th>Staff</th>
			<?php } ?>
			<?php if(strpos($db_config,',hoilday_rate_hours,') !== FALSE) { ?>
				<th>Number of Hours paid</th>
			<?php } ?>
			<th>Function</th>
		</tr>
        </thead>
		<?php
		while($row = mysqli_fetch_array( $result )) {
			echo '<tr>';
            if(strpos($db_config, ',holiday_rate_type,') !== FALSE) {
			echo '<td data-title="Rate Card Name">' . $row['rate_type'] . '</td>';
            }
            if(strpos($db_config, ',holiday_rate_position,') !== FALSE) {
                echo '<td data-title="Start Date">'.get_positions($dbc, $row['positionid'], 'name').'</td>';
            }
            if(strpos($db_config, ',holiday_rate_staff,') !== FALSE) {
                echo '<td data-title="Alert Date">'.get_staff($dbc, $row['staffid']).'</td>';
            }
            if(strpos($db_config, ',hoilday_rate_hours,') !== FALSE) {
                 echo '<td data-title="Start Date">'.$row['no_of_hours_paid'].'</td>';
            }
            echo '<td data-title="Functions">';
            if(vuaed_visible_function($dbc, 'rate_card') == 1) {
				echo '<a href=\'?card=holiday&type=holiday&status=add&ratecardid='.$row['ratecardholidayid'].'\'>Edit</a>';
            }
			echo '</td>';
			echo '</tr>';
		}
		echo "</table>";

		// Added Pagination //
		echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
		// Pagination Finish //
	else:
		echo "<h2>No Record Found.</h2>";
	endif; ?>
</div>