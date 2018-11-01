<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['printpdf'])) {
    $starttimepdf = $_POST['starttimepdf'];
    $endtimepdf = $_POST['endtimepdf'];

    DEFINE('REPORT_LOGO', get_config($dbc, 'report_logo'));
    DEFINE('REPORT_HEADER', html_entity_decode(get_config($dbc, 'report_header')));
    DEFINE('REPORT_FOOTER', html_entity_decode(get_config($dbc, 'report_footer')));
    DEFINE('START_DATE', $starttimepdf);
    DEFINE('END_DATE', $endtimepdf);

    class MYPDF extends TCPDF {

		public function Header() {
			//$image_file = WEBSITE_URL.'/img/Clinic-Ace-Logo-Final-250px.png';
            if(REPORT_LOGO != '') {
                $image_file = 'download/'.REPORT_LOGO;
                $this->Image($image_file, 10, 10, 80, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);

            $this->SetFont('helvetica', '', 13);
            $footer_text = 'Estimate Item Closing % By Quantity From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : Estimate Item Closing % By Quantity";
            $this->writeHTMLCell(0, 0, 10 , 45, $footer_text, 0, 0, false, "R", true);
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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 55, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);

    $html .= report_daily_validation($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/estimate_item_closing_quantity_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_estimate_qty', 0, WEBSITE_URL.'/Reports/Download/estimate_item_closing_quantity_'.$today_date.'.pdf', 'Estimate Item Closing % By Quantity');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/estimate_item_closing_quantity_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    } ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Summarizes sales for each item on your Service List. Includes quantity and total sales.</div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
            }

            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-01');
            }

            if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            }
            ?>
            <center><div class="form-group">
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Invoice Date From:</label>
					<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Invoice Date Until:</label>
					<div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
				</div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                echo report_daily_validation($dbc, $starttime, $endtime, '', '', '');
            ?>

        </form>

<?php
function report_daily_validation($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style) {

    $report_data = '';

    $arr = array('miscellaneous' => 'Miscellaneous', 'vpl' => 'Vendor Pricelist', 'staff' => 'Staff', 'services' => 'Services', 'products' => 'Products', 'position' => 'Position', 'material' => 'Material', 'labour' => 'Labour', 'inventory' => 'inventory', 'equipment' => 'Equipment', 'clients' => 'Clients');

    //Misc
    foreach ($arr as $key => $val) {
        $report_scope = mysqli_query($dbc,"SELECT src_id, uom, SUM(qty) AS total_qty FROM estimate_scope WHERE src_table = '$key' AND src_id >0 AND (DATE(today_date) >= '".$starttime."' AND DATE(today_date) <= '".$endtime."') GROUP BY src_id");
        $qty = 0;
        if(mysqli_num_rows($report_scope) > 0) {
            $report_data .= '<h3>'.$val.'</h3>';
            $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
            $report_data .= '<tr style="'.$table_row_style.'">
            <th>Item Name</th>
            <th>Units</th>
            <th>Qty Estimated</th>
            <th>Qty Pending</th>
            <th>Qty Lost</th>
            <th>Qty Won</th>
            <th>Win %</th>';
            $report_data .= "</tr>";
            while($report_each_scope = mysqli_fetch_array($report_scope)) {
                    $report_data .= '<tr nobr="true">';
                    $id = $report_each_scope['src_id'];

                    if($key == 'miscellaneous') {
                        $heading = 'Miscellaneous';
                    }
                    if($key == 'vpl') {
                        $heading = get_vpl($dbc, $id, 'vpl_name');
                    }
                    if($key == 'staff') {
                        $heading = get_contact($dbc, $id, 'name_company');
                    }
                    if($key == 'services') {
                        $heading = get_all_from_service($dbc, $id, 'heading');
                    }
                    if($key == 'products') {
                        $heading = get_products($dbc, $id, 'heading');
                    }
                    if($key == 'position') {
                        $heading = get_positions($dbc, $id, 'name');
                    }
                    if($key == 'material') {
                        $heading =  get_material($dbc, $id, 'name');
                    }
                    if($key == 'labour') {
                        $heading = get_labour($dbc, $id, 'heading');
                    }
                    if($key == 'inventory') {
                        $heading = get_inventory($dbc, $id, 'name');
                    }
                    if($key == 'equipment') {
                        $heading = get_equipment_field($dbc, $id, 'category');
                    }
                    if($key == 'clients') {
                        $heading = get_contact($dbc, $id, 'name_company');
                    }
                    $report_data .= '<td>'.$heading.'</td>';
                    $report_data .= '<td>'.$report_each_scope['uom'].'</td>';
                    $report_data .= '<td>'.number_format($report_each_scope['total_qty'],2).'</td>';
                    $report_data .= '<td>'.number_format($report_each_scope['total_qty'],2).'</td>';
                    $report_data .= '<td>0.00</td>';
                    $report_data .= '<td>0.00</td>';
                    $report_data .= '<td>0.00%</td>';
                    $report_data .= "</tr>";
                    $qty += $report_each_scope['total_qty'];
            }

            $report_data .= "<tr>";
            $report_data .= '<td><b>Total</b></td><td></td><td><b>'.number_format($qty,2).'</b></td><td><b>'.number_format($qty,2).'</b></td><td><b>0.00</b></td><td><b>0.00</b></td><td><b>0.00%</b></td>';
            $report_data .= "</tr>";
            $qty = 0;

        $report_data .= '</table><br>';
        }
    }

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