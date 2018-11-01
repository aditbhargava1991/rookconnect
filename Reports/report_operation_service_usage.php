<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
if (isset($_POST['printpdf'])) {
	$contact = $_POST['report_contact'];
	$woid = $_POST['report_wo'];
	$from_date = $_POST['report_from'];
	$until_date = $_POST['report_until'];
    $today_date = date('Y-m-d');
	$pdf_name = "Download/shop_work_orders_time_$today_date.pdf";

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
            $footer_text = 'Shop Work Orders - Time Spent';
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
    $html .= shop_work_orders($dbc, $from_date, $until_date, $woid, $contact, true, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output($pdf_name, 'F');
    track_download($dbc, 'report_operation_service_usage', 0, WEBSITE_URL.'/Reports/Download/shop_work_orders_time_'.$today_date.'.pdf', 'Shop Work Orders - Time Spent Report');
    ?>

	<script>
		window.location.replace('<?php echo $pdf_name; ?>');
	</script>
<?php } ?>

				<div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
							This report displays how much of each service you are selling.</div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-inline" role="form">

            <?php $search_wo = '';
            if (isset($_POST['search_service'])) {
                $search_service = $_POST['search_service'];
            } ?>

			<div class="col-sm-5">
				<label for="search_wo" class="col-sm-4 control-label">Search By Service:</label>
				<div class="col-sm-8">
					<select data-placeholder="Select a Service Category" name="search_service" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380" onchange="this.form.submit()">
						<option value=""></option>
						<?php
						$query = mysqli_query($dbc,"SELECT distinct category from services order by category");
						while($row = mysqli_fetch_array($query)) { ?>
							<option <?php if ($row['category'] == $search_service) { echo " selected"; } ?> value='<?php echo  $row['category']; ?>' ><?php echo $row['category']; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>


			<div class="col-sm-6">
				<!--<button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>-->
				<button type="button" onclick="window.location=''" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display ALL</button>
				<button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
			</div>

            <input type="hidden" name="report_contact" value="<?php echo $search_service; ?>">
            <br><br>

            <?= service_usage($dbc, $search_service) ?>

        </form>

<?php
function service_usage($dbc, $search_service, $no_page = false, $table_style = '', $table_row_style = '', $grand_total_style = '') {
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
    if($search_service != '') {
        $clause .= " WHERE category = '$search_service'";
    }

	  $sql = "SELECT category, count(*) as category_count from services $clause group by category";
		$total_sql = "SELECT count(serviceid) as total_count from services";
    $result = mysqli_query($dbc,$sql);
		$total_count_array = mysqli_fetch_assoc(mysqli_query($dbc, $total_sql));
	  if($no_page === false) {
		  //echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
	  }

    $report_data .= '<table border="1px" class="table table-bordered" width="100%" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">';
    $report_data .= '<th>Service Category</th>';
    $report_data .= '<th>% of Services Sold</th>';
    $report_data .=  "</tr>";

    while($row = mysqli_fetch_array( $result ))
    {
			  $perc = $row['category_count'] / $total_count_array['total_count'] * 100;
        $report_data .= '<tr nobr="true">';
				$report_data .=  '<td data-title="Category">' . $row['category'] . '</td>';
        $report_data .=  '<td data-title="Category Count">' . round($perc, 1 , PHP_ROUND_HALF_EVEN) . '% </td>';

        $report_data .=  "</tr>";
    }

    $report_data .=  '</table>';

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
