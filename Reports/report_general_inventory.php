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

    DEFINE('START_DATE', $starttimepdf);
    DEFINE('END_DATE', $endtimepdf);
    DEFINE('REPORT_LOGO', get_config($dbc, 'report_logo'));
    DEFINE('REPORT_HEADER', html_entity_decode(get_config($dbc, 'report_header')));
    DEFINE('REPORT_FOOTER', html_entity_decode(get_config($dbc, 'report_footer')));

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
            $footer_text = 'Inventory Analysis From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : Displays sold inventory items in the selected date range, as well as  total profit or loss for each item.";
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

    $html .= report_inventory($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/referral_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_general_inventory', 0, WEBSITE_URL.'/Reports/Download/referral_'.$today_date.'.pdf', 'Inventory Analysis Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/referral_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    } ?>
        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Displays sold inventory items in the selected date range, as well as  total profit or loss for each item.</div>
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
					<label class="col-sm-4">From:</label>
					<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Until:</label>
					<div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
				</div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                //echo '<a href="report_referral.php?referral=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a></h4><br>';

                echo report_inventory($dbc, $starttime, $endtime, '', '', '');
            ?>

        </form>

<?php
function report_inventory($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style) {

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th>Code</th>
    <th>Inventory Name</th>
    <th>Quantity</th>
    <th>Cost</th>
    <th>Total Cost</th>
    <th>Total Sales</th>
    <th>Profit/Loss</th>
    </tr>';

    /*
    $report_service = mysqli_query($dbc,"SELECT inventoryid, sell_price FROM invoice WHERE inventoryid != ',' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')");

    $get_invid = '';
    $get_fee = '';
    while($report_validation = mysqli_fetch_array($report_service)) {
        $get_invid .= $report_validation['inventoryid'].',';
        $get_fee .= $report_validation['sell_price'].',';
    }

    $all_invid = implode(",",array_filter(explode(",",$get_invid)));

    $all_f = implode(",",array_filter(explode(",",$get_fee)));
    $all_f_add = ','.$all_f.',';
    $final_fee = str_replace(",0,",",",$all_f_add);
    $all_fee = trim($final_fee,",");

    $all_s_explode = explode(',', $all_invid);
    $fee_explode = explode(',', $all_fee);

    $invid = array_filter($all_s_explode);
    $fee = array_filter($fee_explode);

    // Services
    $total_base_inv = 0;
    $total_appt = 0;
    $total_cost = 0;
    if($get_invid != '') {
        asort($invid);

        $sorted_arr2 = [];
        foreach($invid as $key=>$val) {
          array_push($sorted_arr2, $fee[$key]);
        }

        $combined = combineStringArrayWithDuplicates($invid, $sorted_arr2);

        foreach ($combined as $key => $value) {
            $key_invid_qty = explode(':', $key);
            $sid = $key_invid_qty[0];
            $report_data .= '<tr nobr="true">';
            $base_pay_inv_perc = $base_pay[1];

            $total_cost = (get_inventory($dbc, $sid, 'purchase_cost')*$key_invid_qty[1]);

            $profit_loss = ($value-$total_cost);
            $back = '';
            if($total_cost > $value) {
                $back = 'style="background-color: red;"';
            } else {
                $back = 'style="background-color: green;"';
            }

            $report_data .= '<td>'.get_inventory($dbc, $sid, 'code').'</td>';
            $report_data .= '<td>'.get_inventory($dbc, $sid, 'name').'</td>';

            $report_data .= '<td>'.$key_invid_qty[1].'</td>';
            $report_data .= '<td>'.get_inventory($dbc, $sid, 'purchase_cost').'</td>';
            $report_data .= '<td>'.number_format($total_cost, 2).'</td>';
            $report_data .= '<td>'.number_format($value, 2).'</td>';
            $report_data .= '<td '.$back.'>'.$profit_loss.'</td>';
            $report_data .= '</tr>';
            $total_base_inv += $value;
            $total_appt += $key_invid_qty[1];
        }
    }
    */

    $total_contact = mysqli_query($dbc,"SELECT SUM(quantity) AS total_quantity, SUM(sell_price) AS total_sell, inventoryid FROM report_inventory WHERE (DATE(today_date) >= '".$starttime."' AND DATE(today_date) <= '".$endtime."') GROUP BY inventoryid");

	$cost_field = get_config($dbc,'inventory_cost');
    $odd_even=0;
    while($row_report = mysqli_fetch_array($total_contact)) {
        $bg_class = $odd_even % 2 == 0 ? '' : 'background-color:#e6e6e6;';
        $inventoryid = $row_report['inventoryid'];
        $total_cost = (get_all_from_inventory($dbc, $inventoryid, $cost_field)*$row_report['total_quantity']);
        $total_sell = $row_report['total_sell'];
        $profit_loss = ($total_sell-$total_cost);
        $back = '';
        if($total_cost > $total_sell) {
            $back = 'style="background-color: red;"';
        } else {
            $back = 'style="background-color: green;"';
        }
        $report_data .= '<tr nobr="true" style="'.$bg_class.'">';
        $report_data .= '<td>'.get_all_from_inventory($dbc, $inventoryid, 'code').'</td>';
        $report_data .= '<td>'.get_all_from_inventory($dbc, $inventoryid, 'name').'</td>';
        $report_data .= '<td>'.$row_report['total_quantity'].'</td>';
        $report_data .= '<td>'.get_all_from_inventory($dbc, $inventoryid, $cost_field).'</td>';
        $report_data .= '<td>'.number_format($total_cost, 2).'</td>';
        $report_data .= '<td>'.$total_sell.'</td>';
        $report_data .= '<td '.$back.'>'.$profit_loss.'</td>';
        $report_data .= '</tr>';
        $odd_even++;
    }

    $report_data .= '</table>';

    return $report_data;
}

function combineStringArrayWithDuplicates ($keys, $values) {
    $total_array = sizeof($keys);
    $iter = 0;
    $key_old = 0;
    $fee = 0;
    $m = 0;
    foreach ($keys as $key) {
        if($iter == 0) {
            $fee += $values[$iter];
            $key_old = $key;

        } else if($key != $key_old && $iter != 0) {
            $combined[$key_old.':'.$m] = $fee;
            $m = 0;
            $fee = 0;
            $key_old = $key;
            $fee += $values[$iter];
        } else {
            $fee += $values[$iter];
        }
        $m++;
        $iter++;
    }
    if($iter == $total_array) {
        $combined[$key_old.':'.$m] = $fee;
    }
    return $combined;
}

?>