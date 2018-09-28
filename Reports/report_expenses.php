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
            $footer_text = 'Expense Report From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
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

    $html .= report_sales_summary($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/sales_summary_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_expenses', 0, WEBSITE_URL.'/Reports/Download/sales_summary_'.$today_date.'.pdf', 'Expense Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/sales_summary_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    } ?>

		<form name="form_sites" method="post" action="" class="form-inline" role="form">
		<?php
		if (isset($_POST['search_submit'])) {
			$search_month = substr($_POST['search_month'],0,7);
			if($search_month == 0000-00-00) {
				$search_month = date('Y-m');
			}
			$search_staff = $_POST['search_staff'];
		} else {
			$search_month = date('Y-m');
			$search_staff = '';
		}
		?>
		<br>
		<center><div class="form-group col-sm-12">
			<div class="form-group col-sm-5">
				<label class="col-sm-4">Display Month:</label>
				<div class="col-sm-8"><input name="search_month" type="text" class="datepicker form-control" value="<?php echo $search_month; ?>"></div>
			</div>
			<div class="form-group col-sm-5">
				<label class="col-sm-4">Expensed For:</label>
				<div class="col-sm-8">
					<select data-placeholder="Select a Staff..." name="search_staff" class="chosen-select-deselect form-control1" width="380">
						<option value="">Select All</option>
						<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status=1"),MYSQLI_ASSOC));
						foreach($query as $rowid) {
							echo "<option ".($rowid == $search_staff ? 'selected' : '')." value='$rowid'>".get_contact($dbc, $rowid)."</option>";
						} ?>
					</select>
				</div>
			</div>
			<div class="form-group col-xm-2">
				<div style="display:inline-block; padding: 0 0.5em;">
					<button type="submit" name="search_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
				</div>
				<div style="display:inline-block; padding: 0 0.5em;">
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click to refresh the page and see the current month for all staff."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<button type="submit" name="display_all" value="Display All" class="btn brand-btn mobile-block">Current Month</button>
				</div>
			</div><!-- .form-group -->
			<div class="clearfix"></div>
		</div></center>

            <?php
                //echo '<a href="report_referral.php?referral=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a></h4><br>';

                echo report_expense_summary($dbc, $search_month, $search_staff, '', '', '');
            ?>
        </form>

<?php
function report_expense_summary($dbc, $search_month, $search_staff, $table_style, $table_row_style, $grand_total_style) {
	$categories_sql = "SELECT * FROM (SELECT CONCAT('EC ',`ec`,': ',`category`) `ec_code`, `category`, CONCAT('GL ',`gl`,': ',`heading`) `gl_code`, `heading` FROM `expense_categories` ORDER BY `ec`, `gl`) `categories`
		UNION SELECT 'Uncategorized', '', 'Misc', ''";
	$category_query = mysqli_query($dbc, $categories_sql);
	$report_data .= "<table class='table table-bordered'><tr class='hidden-xs hidden-sm'><th>Category & Heading</th><th>Expense Amount</th><th>Tax</th><th>Total</th></tr>";
	$final_amt = $final_tax = $final_total = 0;
	$odd_even=0;
	while($cat_row = mysqli_fetch_array($category_query)) {
		$bg_class = $odd_even % 2 == 0 ? '' : 'background-color:#e6e6e6;';
		$query_expenses = "SELECT * FROM expense WHERE LEFT(ex_date,7) = '$search_month' AND (`staff`='$search_staff' OR '$search_staff'='') AND `deleted`=0 AND `reimburse`=1";
		$category_value = $cat_row['category'];
		$heading_value = $cat_row['heading'];
		$report_data .= "<tr style='".$bg_class."'><td data-title='Category & Heading'>".$cat_row['ec_code'].': '.$cat_row['gl_code']."</td>";
		$result = mysqli_query($dbc, $query_expenses." AND `category`='$category_value' AND `title`='$heading_value'");
		$cat_amt = $cat_tax = $cat_total = 0;
		while($row = mysqli_fetch_array($result)) {
			$cat_amt += $row['amount'];
			$cat_tax += $row['pst'] + $row['gst'];
			$cat_total += $row['total'];
		}
		$final_amt += $cat_amt;
		$final_tax += $cat_tax;
		$final_total += $cat_total;
		$report_data .= "<td data-title='Amount'>$".number_format($cat_amt, 2, '.', '')."</td><td data-title='Tax'>$".number_format($cat_tax, 2, '.', '')."</td><td data-title='Total'>$".number_format($cat_total, 2, '.', '')."</td></tr>";
		$odd_even++;
	}
	$report_data .= "<tr><td data-title=''><b>Totals</b></td><td data-title='Amount'><b>$".number_format($final_amt, 2, '.', '')."</b></td><td data-title='Tax'><b>$".number_format($final_tax, 2, '.', '')."</b></td><td data-title='Total'><b>$".number_format($final_total, 2, '.', '')."</b></td></tr>";
	$report_data .= "</table>";

	return $report_data;
}