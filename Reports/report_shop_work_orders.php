<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
if (isset($_POST['printpdf'])) {
	$type = $_POST['report_type'];
	$woid = $_POST['report_wo'];
	$from_date = $_POST['report_from'];
	$until_date = $_POST['report_until'];
    $today_date = date('Y-m-d');
	$pdf_name = "Download/shop_work_orders_$today_date.pdf";

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
            $footer_text = 'Shop Work Orders';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);
		}

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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);

	$html = '<h3>Report Date: '.$from_date.($until_date == $from_date ? '' : ' to '.$until_date).'</h3>';
    $html .= shop_work_orders($dbc, $from_date, $until_date, $woid, true, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output($pdf_name, 'F');
    ?>

	<script>
		window.location.replace('<?php echo $pdf_name; ?>');
	</script>
<?php } ?>

<script type="text/javascript">

</script>
</head>
<body>
<?php include_once ('../navigation.php');
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">

        <?php echo reports_tiles($dbc);  ?>

        <br><br>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-inline" role="form">

            <?php
			if(!isset($_GET['type'])) {
				$_GET['type'] = 'Daily';
			}

			$search_wo = '';
			$search_until = '';
			$search_from = date('Y-m-d');
			$date = date_create($search_from);
			if($_GET['type'] == 'Weekly') {
				$search_from = $date->sub(date_interval_create_from_date_string('7 days'))->format('Y-m-d');
			}
			else if($_GET['type'] == 'Monthly') {
				$search_from = $date->sub(date_interval_create_from_date_string('1 month'))->format('Y-m-d');
			}
			else if($_GET['type'] == 'Custom') {
				$search_from = $date->sub(date_interval_create_from_date_string('14 days'))->format('Y-m-d');
			}

            if (isset($_POST['search_wo'])) {
                $search_wo = $_POST['search_wo'];
            }
            if (isset($_POST['search_from'])) {
                $search_from = $_POST['search_from'];
            }
            if (isset($_POST['search_until'])) {
                $search_until = $_POST['search_until'];
            }
			$date = date_create($search_from);
			switch($_GET['type']) {
				case('Daily'): $search_until = $search_from; break;
				case('Weekly'): $search_until = $date->add(date_interval_create_from_date_string('7 days'))->format('Y-m-d'); break;
				case('Monthly'): $search_until = $date->add(date_interval_create_from_date_string('1 month'))->format('Y-m-d'); break;
				case('Custom'): $search_until = $search_until == '' ? date('Y-m-d') : $search_until; break;
			}
            ?>

			<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
				<label for="search_wo" class="control-label">Search By Work Order #:</label>
			</div>
			<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
				<select data-placeholder="Select a Work Order" name="search_wo" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
					<option value=""></option>
					<?php
					$query = mysqli_query($dbc,"SELECT `unique_id`, `projectmanageid` FROM `project_manage` WHERE `status` = 'Approved' AND `tile` = 'Shop Work Orders' ORDER BY `unique_id`");
					while($row = mysqli_fetch_array($query)) { ?>
						<option <?php if ($row['projectmanageid'] == $search_wo) { echo " selected"; } ?> value='<?php echo  $row['projectmanageid']; ?>' ><?php echo $row['unique_id']; ?></option>
					<?php } ?>
				</select>
			</div>

			<?php if($_GET['type'] == 'Daily'): ?>
				<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
					<label for="search_from" class="control-label">Date:</label>
				</div>
				<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
					<input type="text" class="form-control datepicker" name="search_from" value="<?php echo $search_from; ?>" style="width:100;">
				</div>
			<?php elseif($_GET['type'] == 'Weekly'): ?>
				<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
					<label for="search_from" class="control-label">Choose Start of Week:</label>
				</div>
				<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
					<input type="text" class="form-control datepicker" name="search_from" value="<?php echo $search_from; ?>" style="width:100;">
				</div>
			<?php elseif($_GET['type'] == 'Monthly'): ?>
				<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
					<label for="search_from" class="control-label">Choose Start of Month:</label>
				</div>
				<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
					<input type="text" class="form-control datepicker" name="search_from" value="<?php echo $search_from; ?>" style="width:100;">
				</div>
			<?php elseif($_GET['type'] == 'Custom'): ?>
				<div class="clearfix"></div>
				<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
					<label for="search_from" class="control-label">Search From Date:</label>
				</div>
				<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
					<input type="text" class="form-control datepicker" name="search_from" value="<?php echo $search_from; ?>" style="width:100;">
				</div>
				<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
					<label for="search_until" class="control-label">Search Until Date:</label>
				</div>
				<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
					<input type="text" class="form-control datepicker" name="search_until" value="<?php echo $search_until; ?>" style="width:100;">
				</div>
			<?php endif; ?>

			<div class="clearfix"></div>
			<div width="100%">
				<button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
				<?php if($_GET['type'] == 'Custom'): ?>
					<button type="button" onclick="window.location=''" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
				<?php else: ?>
					<button type="button" onclick="window.location=''" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display Current</button>
				<?php endif; ?>
				<button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
			</div>

            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="report_wo" value="<?php echo $search_wo; ?>">
            <input type="hidden" name="report_from" value="<?php echo $search_from; ?>">
            <input type="hidden" name="report_until" value="<?php echo $search_until; ?>">
            <br><br>

            <?php
            echo shop_work_orders($dbc, $search_from, $search_until, $search_wo);
            ?>

        </form>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>

<?php
function shop_work_orders($dbc, $search_from, $search_until, $search_wo, $no_page = false, $table_style = '', $table_row_style = '', $grand_total_style = '') {
    $report_data = '';

	$rowsPerPage = 15;
	$pageNum = 1;
	$limit = '';
	if(isset($_GET['page'])) {
		$pageNum = $_GET['page'];
	}
	$offset = ($pageNum - 1) * $rowsPerPage;
	if($no_page === false) {
		$limit = " LIMIT $offset, $rowsPerPage";
	}

	$clause = '';
    if($search_from != '') {
        $clause .= " AND timer.`created_date` >= '$search_from'";
    }
    if($search_until != '') {
        $clause .= " AND timer.`created_date` <= '$search_until'";
    }
    if($search_wo != '') {
        $clause .= " AND pm.`projectmanageid` = '$search_wo'";
    }
	$sql = "SELECT pm.`unique_id`, timer.`created_date`, GROUP_CONCAT(IFNULL(timer.`created_by`,0)) staff, GROUP_CONCAT(IFNULL(timer.`timer`,'')) duration
		FROM `project_manage` pm LEFT JOIN `project_manage_assign_to_timer` timer ON pm.`projectmanageid`=timer.`projectmanageid`
		WHERE pm.`status` = 'Approved' AND pm.`tile` = 'Shop Work Orders' $clause
		GROUP BY pm.`projectmanageid`, pm.`unique_id`, timer.`created_date`
		ORDER BY timer.`created_date` $limit";
	$query = "SELECT COUNT(*) numrows FROM (SELECT pm.`projectmanageid`
		FROM `project_manage` pm LEFT JOIN `project_manage_assign_to_timer` timer ON pm.`projectmanageid`=timer.`projectmanageid`
		WHERE pm.`status` = 'Approved' AND pm.`tile` = 'Shop Work Orders' $clause
		GROUP BY pm.`projectmanageid`, pm.`unique_id`, timer.`created_date`) shop_work_orders";
    $result = mysqli_query($dbc,$sql);
	if($no_page === false) {
		echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
	}

    $report_data .= '<table border="1px" class="table table-bordered" width="100%" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">';
    $report_data .= '<th>Work Order #</th>';
	$report_data .= '<th>Date</th>';
    $report_data .= '<th>Staff : Hours</th>';
    $report_data .=  "</tr>";

    while($row = mysqli_fetch_array( $result ))
    {
        $report_data .= '<tr nobr="true">';
        $timetrackingid = $row['timetrackingid'];
        $report_data .=  '<td data-title="Work Order">' . $row['unique_id'] . '</td>';
        $report_data .=  '<td data-title="Date">' . $row['created_date'] . '</td>';

        $report_data .=  '<td data-title="Staff Hours">';
        $id = explode(',', $row['staff']);
		$timer = explode(',', $row['duration']);
        foreach($id as $key => $value)  {
			$time = $timer[$key];
            if($value != '') {
                $report_data .= get_staff($dbc, $value).' : ';
            }
			if($time != '') {
				$report_data .= $time;
			}
			$report_data .= "<br />\n";
        }
        $report_data .=  '</td>';

        $report_data .=  "</tr>";
    }

    $report_data .=  '</table>';

    return $report_data;
}
?>