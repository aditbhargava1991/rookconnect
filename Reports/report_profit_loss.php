<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
error_reporting(0);

/* Hide Kristi from accessing Profit & Loss report on SEA (temp fix)
 * Code also added on report_titles_function.php
 */
$software_url = $_SERVER['SERVER_NAME'];
if ( $software_url == 'sea-alberta.rookconnect.com' || $software_url == 'sea-regina.rookconnect.com' || $software_url == 'sea-saskatoon.rookconnect.com' || $software_url == 'sea-vancouver.rookconnect.com' || $software_url == 'sea.freshfocussoftware.com' ) {
	$rookconnect = 'SEA';
}

$contactid = $_SESSION['contactid'];
if ( $rookconnect == 'SEA' ) {
	$results = mysqli_query ( $dbc, "SELECT `user_name` FROM `contacts` WHERE `contactid`='$contactid'");

	while ( $row = mysqli_fetch_assoc ( $results) ) {
		$user_name = $row[ 'user_name' ];
		if ( $user_name == 'kristi' ) {
			$url = WEBSITE_URL . '/Reports/report_tiles.php';
			header('Location: ' . $url);
			break;
		}
	}
}

include_once('../tcpdf/tcpdf.php');
error_reporting(0);
if(isset($_GET['category_sort'])) {
	$category_or_singles = 'category';
} else {
	$category_or_singles = 'single';
}

if (isset($_POST['printpdf'])) {
    $starttimepdf = $_POST['starttimepdf'];
    $endtimepdf = $_POST['endtimepdf'];
	$category_or_singles = $_POST['category_or_singles'];
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
                $this->Image($image_file, 10, 10, '', '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);

            $this->SetFont('helvetica', '', 13);
            $footer_text = 'Profit Loss From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
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

    $html .= report_profit_loss($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;', $category_or_singles);

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/validation_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_profit_loss', 0, WEBSITE_URL.'/Reports/Download/validation_'.$today_date.'.pdf', 'Profit Loss Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/validation_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    } ?>


        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">
			<input type="hidden" name="category_or_singles" value="<?php echo $category_or_singles; ?>">
			<?php if($category_or_singles == 'single') { ?>
				<button type="button" onclick="location.href = '?category_sort&type=sales&report=<?= $_GET['report'] ?>';" class="btn brand-btn">Sort by Category</button>
			<?php } else { ?>
				<button type="button" onclick="location.href = '?type=sales&report=<?= $_GET['report'] ?>';" class="btn brand-btn">Sort by Individual Items</button>
           <?php
			}
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

                echo report_profit_loss($dbc, $starttime, $endtime, '', '', '', $category_or_singles);
            ?>

        </form>

<?php
function report_profit_loss($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $category_or_singles) {
		if($category_or_singles == 'category') {
			$total = 0;

			$report_validation = mysqli_query($dbc,"SELECT pp.inventoryid, SUM(pp.price) AS total_price, COUNT(pp.inventoryid) AS count_invid FROM point_of_sell p, point_of_sell_product pp WHERE p.posid = pp.posid AND (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') GROUP BY pp.inventoryid");
			$num_rows = mysqli_num_rows($report_validation);

			if($num_rows > 0) {
				$report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">
				<tr style="'.$table_row_style.'">
				<th>Category</th>
				<th>Gross Profit</th>
				</tr>';
				$categ_list = '';
				$blank = 0;
				while($row_report = mysqli_fetch_array($report_validation)) {
					$inventoryid = $row_report['inventoryid'];
					$total_price = $row_report['total_price'];
					$count_invid = $row_report['count_invid'];
					$cdn_cpu = get_inventory($dbc, $inventoryid, 'cdn_cpu');
					$total_cdn = ($cdn_cpu*$count_invid);

					$categ = get_inventory($dbc, $inventoryid, 'category');
					$HiddenProducts = explode(',',$categ_list);
					if (in_array($categ, $HiddenProducts)) {
						if($categ == '' || $categ == NULL) {
							$blank += ($total_price-$total_cdn);
						} else {
							${$categ.'_123'} += ($total_price-$total_cdn);
						}
					} else {
						$categ_list = $categ_list.','.$categ;
						${$categ.'_123'} = ($total_price-$total_cdn);
					}
				}
				$i = 0;
				$totayl = 0;
			   $var=explode(',',$categ_list);
			   foreach($var as $categie)
				{
					$i++;
					if($categie == '' || $categie == NULL) {
						$category_name = 'No category found';
						$total_cate = $blank;
						$totayl += $blank;
					} else {
						$total_cate = ${$categie.'_123'};
						$category_name = $categie;
						$totayl += ${$categie.'_123'};
					}
					$report_data .= '<tr nobr="true">';
					$report_data .=	 	'<td>'.$category_name.'</td>';
					$report_data .= 	'<td>$'.number_format(($total_cate),2) . '</td>';
					$report_data .= "</tr>";
				}
				$report_data .= '<tr nobr="true">';
				$report_data .= '<td>Total Profit</td>';
				$report_data .= '<td>$'.number_format($totayl, 2).'</td>';
				$report_data .= "</tr>";

				$report_data .= '</table>';
			}
		} else {
			$total = 0;
			$costs = 0;

			//$report_validation = mysqli_query($dbc,"SELECT pp.inventoryid, SUM(pp.price) AS total_price, COUNT(pp.inventoryid) AS count_invid FROM point_of_sell p, point_of_sell_product pp WHERE p.posid = pp.posid AND (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') GROUP BY pp.inventoryid");

			//Price should be multiplied by the quantity
			$report_validation = mysqli_query($dbc,"
				SELECT
					pp.`inventoryid`,
                    p.`posid`,
                    p.`invoice_date`,
					inv.`name`,
					pp.`price`,
					pp.`quantity`,
                    p.payment_type
				FROM
					`point_of_sell` p,
					`point_of_sell_product` pp,
					`inventory` inv
				WHERE
                    p.`posid` = pp.`posid` AND
					pp.`inventoryid` = inv.`inventoryid` AND
					p.`status` NOT IN ('Void') AND (DATE(p.`invoice_date`) >= '" . $starttime . "' AND DATE(p.`invoice_date`) <= '" . $endtime . "') ORDER BY p.`posid` DESC
			");

			$num_rows = mysqli_num_rows($report_validation);

			if($num_rows > 0) {
				$report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">
				<tr style="'.$table_row_style.'">
                <th>Invoice#</th>
				<th>Inventory</th>
                <th>Status</th>
                <th>Breakdown</th>
				<th>Gross Costs</th>
				<th>Gross Profit</th>
				</tr>';

				while($row_report = mysqli_fetch_array($report_validation)) {
					$inventoryid = $row_report['inventoryid'];
					$quantity	 = $row_report['quantity'];
					//$total_price = $row_report['total_price'];
					$total_price = $row_report['price'] * $quantity;
					$count_invid = $row_report['count_invid'];

					$cdn_cpu = get_inventory($dbc, $inventoryid, 'cdn_cpu');
					//$total_cdn = ($cdn_cpu*$count_invid);
					$total_cdn = $cdn_cpu * $quantity;


					$report_data .= '<tr nobr="true">';
                    $report_data .= '<td>' .$row_report['posid'].'</td>';
					$report_data .= '<td>' . get_inventory($dbc, $inventoryid, 'category'). ' : '. get_inventory($dbc, $inventoryid, 'name'). '</td>';
                    $report_data .= '<td>' .$row_report['payment_type'].'</td>';

					$report_data .= '<td>qty: ' . $quantity . ' | price: ' . $row_report['price'] . ' | total_price: ' . $total_price . ' | cdn_cpu: ' . $cdn_cpu . ' | total_cdn: ' . $total_cdn . ' = ('.$total_price.' - '.$total_cdn.') </td>';

					$report_data .= '<td align="right">$' . number_format($total_cdn,2). '</td>';
					$report_data .= '<td align="right">$' . number_format(($total_price-$total_cdn),2). '</td>';
					$report_data .= "</tr>";
					$total += ($total_price-$total_cdn);
					$costs += $total_cdn;
				}

				$report_data .= '<tr nobr="true">';
				$report_data .= '<td><strong>Total Profit</strong></td><td></td><td></td><td></td>';
				$report_data .= '<td align="right"><strong>$'.number_format($costs, 2).'</strong></td>';
				$report_data .= '<td align="right"><strong>$'.number_format($total, 2).'</strong></td>';
				$report_data .= "</tr>";

				$report_data .= '</table>';
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