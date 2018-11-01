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
            $footer_text = 'Monthly Sales by Injury Type Invoice Date From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : This report displays the Total Customer Accounts Receivable, Customer Paid Amounts, Unassigned/Error Invoices and the Total Sales for the selected date range, broken down by Service, Inventory Sold, Refunds of Services and Refunds of Inventory. Total GST for the selected date range is also displayed.";
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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 60, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);

    $html .= report_sales_summary($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/monthly_sales_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_invoice_sales_summary', 0, WEBSITE_URL.'/Reports/Download/monthly_sales_'.$today_date.'.pdf', 'Monthly Sales by Injury Type Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/monthly_sales_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    } ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            This report displays the Total Customer Accounts Receivable, Customer Paid Amounts, Unassigned/Error Invoices and the Total Sales for the selected date range, broken down by Service, Inventory Sold, Refunds of Services and Refunds of Inventory. Total GST for the selected date range is also displayed.</div>
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

            if(!empty($_GET['from'])) {
                $starttime = $_GET['from'];
            } else if($starttime == 0000-00-00) {
                $starttime = date('Y-m-01');
            }

            if(!empty($_GET['to'])) {
                $endtime = $_GET['to'];
            } else if($endtime == 0000-00-00) {
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
                echo report_sales_summary($dbc, $starttime, $endtime, '', '', '');

                if(!empty($_GET['from'])) {
                    echo '<a href="'.WEBSITE_URL.'/Reports/report_daily_sales_summary.php?from='.$_GET['from'].'&to='.$_GET['to'].'" class="btn brand-btn">Back</a>';
                }

            ?>

        </form>

<?php
function report_sales_summary($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style) {
	$total_services = 0;
	$total_inventory = 0;
	$total_packages = 0;
	$total_products = 0;
	$total_misc = 0;

    ////Customer A/R
    $all_customer_ar = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(sub_total) as `all_customer_ar` FROM invoice_patient WHERE (paid = 'On Account' OR paid = '' OR paid IS NULL) AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));
    $customer_ar = $all_customer_ar['all_customer_ar'];

        // Services
    $result1 = mysqli_query($dbc, "SELECT service_category, SUM(sub_total) AS total_customer_ar FROM invoice_patient WHERE (paid = 'On Account' OR paid = '' OR paid IS NULL) AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND service_category != '' AND sub_total > 0 GROUP BY service_category");
    $cat_customer_ar = '<b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_product_service_summary.php?type=sales">Services</a></b><br>';
    while($row1 = mysqli_fetch_array($result1)) {
        $cat_customer_ar .= '&nbsp;&nbsp; - '.$row1['service_category'].' : $'.number_format($row1['total_customer_ar'],2).'<br>';
    }

        //Inventory
    $r1 = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(sub_total) AS total_customer_ar_inventory FROM invoice_patient WHERE (paid = 'On Account' OR paid = '' OR paid IS NULL) AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND product_name != '' AND `product_name` NOT LIKE 'Miscellaneous%' AND sub_total > 0"));
    $cat_customer_ar .= '<br><b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_inventory_summary.php?type=sales">Inventory</a></b> : $'.number_format($r1['total_customer_ar_inventory'],2).'<br>';

        //Misc
    $r1m = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(sub_total) AS total_customer_ar_inventory FROM invoice_patient WHERE (paid = 'On Account' OR paid = '' OR paid IS NULL) AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND product_name != '' AND `product_name` LIKE 'Miscellaneous%' AND sub_total > 0"));
    $cat_customer_ar .= '<br><b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_inventory_summary.php?type=sales">Miscellaneous</a></b> : $'.number_format($r1m['total_customer_ar_inventory'],2).'<br>';

        //Package
    /*
    $r2 = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(package_price) AS total_customer_ar_package FROM invoice_patient WHERE paid='On Account' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND package_name != '' AND package_price > 0"));
    $cat_customer_ar .= '<br><b>Package</b> : $'.$r2['total_customer_ar_package'].'<br>';
    */

        // Refund Services
    $r3 = mysqli_query($dbc, "SELECT service_category, SUM(sub_total) AS total_customer_ar FROM invoice_patient WHERE (paid = 'On Account' OR paid = '' OR paid IS NULL) AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND service_category != '' AND sub_total < 0 GROUP BY service_category");
    $cat_customer_ar .= '<br><b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_product_service_summary.php?type=sales">Refund Services</a></b><br>';
    while($row3 = mysqli_fetch_array($r3)) {
        $cat_customer_ar .= '&nbsp;&nbsp; - '.$row3['service_category'].' : $'.number_format($row3['total_customer_ar'],2).'<br>';
    }

        //Refund Inventory
    $r4 = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(sub_total) AS total_customer_ar_inventory FROM invoice_patient WHERE (paid = 'On Account' OR paid = '' OR paid IS NULL) AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND product_name != '' AND `product_name` NOT LIKE 'Miscellaneous%' AND sub_total < 0"));
    $cat_customer_ar .= '<br><b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_inventory_summary.php?type=sales">Refund Inventory</a></b> : $'.number_format($r4['total_customer_ar_inventory'],2).'<br>';

        //Refund Misc
    $r4m = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(sub_total) AS total_customer_ar_inventory FROM invoice_patient WHERE (paid = 'On Account' OR paid = '' OR paid IS NULL) AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND product_name != '' AND `product_name` LIKE 'Miscellaneous%' AND sub_total < 0"));
    $cat_customer_ar .= '<br><b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_inventory_summary.php?type=sales">Refund Miscellaneous</a></b> : $'.number_format($r4m['total_customer_ar_inventory'],2).'<br>';

        //Refund Package
    /*
    $r5 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(package_price) AS total_customer_ar_package FROM invoice_patient WHERE paid='On Account' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND package_name != '' AND package_price < 0"));
    $cat_customer_ar .= '<br><b>Refund Package</b> : $'.$r5['total_customer_ar_package'].'<br>';
    */

    ////Customer A/R

    ////Insurer A/R
    $all_insurer_ar = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(sub_total) as `all_insurer_ar` FROM invoice_insurer WHERE paid != 'Yes' AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));
    $insurer_ar = $all_insurer_ar['all_insurer_ar'];

        //Services
    $r6 = mysqli_query($dbc, "SELECT service_category, SUM(sub_total) AS total_ins FROM invoice_insurer WHERE paid != 'Yes' AND sub_total > 0 AND service_category != '' AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') GROUP BY service_category");

    $cat_insurer_ar = '<b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_product_service_summary.php?type=sales">Services</a></b><br>';
    while($row6 = mysqli_fetch_array($r6)) {
        $cat_insurer_ar .= '&nbsp;&nbsp; - '.$row6['service_category'].' : $'.number_format($row6['total_ins'],2).'<br>';
    }

        //Inventory
    $r7 = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(sub_total) AS total_ins_inventory FROM invoice_insurer WHERE paid != 'Yes' AND sub_total > 0 AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND product_name != ''"));
    $cat_insurer_ar .= '<br><b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_inventory_summary.php?type=sales">Inventory</a></b> : $'.number_format($r7['total_ins_inventory'],2).'<br>';

        //Package
    /*
    $r8 = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(package_price) AS total_ins_ar_package FROM invoice_insurer WHERE paid != 'Yes' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND package_name != '' AND package_price > 0"));
    $cat_insurer_ar .= '<br><b>Package</b> : $'.$r8['total_ins_ar_package'].'<br>';
    */

        //Refund Services
    $r9 = mysqli_query($dbc, "SELECT service_category, SUM(sub_total) AS total_ins FROM invoice_insurer WHERE paid != 'Yes' AND sub_total < 0 AND service_category != '' AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') GROUP BY service_category");

    $cat_insurer_ar .= '<br><b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_product_service_summary.php?type=sales">Refund Services</a></b><br>';
    while($row9 = mysqli_fetch_array($r9)) {
        $cat_insurer_ar .= '&nbsp;&nbsp; - '.$row9['service_category'].' : $'.number_format($row9['total_ins'],2).'<br>';
    }

        //Refund Inventory
    $r10 = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(sub_total) AS total_ins_inventory FROM invoice_insurer WHERE paid != 'Yes' AND sub_total < 0 AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND product_name != ''"));
    $cat_insurer_ar .= '<br><b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_inventory_summary.php?type=sales">Refund Inventory</a></b> : $'.number_format($r10['total_ins_inventory'],2).'<br>';

        //Refund Package
    /*
    $r11 = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(package_price) AS total_ins_refund_ar_package FROM invoice_insurer WHERE paid != 'Yes' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND package_name != '' AND package_price < 0"));
    $cat_insurer_ar .= '<br><b>Refund Package</b> : $'.$r11['total_ins_refund_ar_package'].'<br>';
    */

    ////Insurer A/R

    ////Customer Paid
    $all_customer_paid = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(sub_total) as `all_customer_paid` FROM invoice_patient WHERE (paid != 'On Account' AND paid != '' AND paid IS NOT NULL) AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));
    $customer_paid = $all_customer_paid['all_customer_paid'];

        //Services
    $r12 = mysqli_query($dbc, "SELECT service_category, SUM(sub_total) AS total_customer_paid FROM invoice_patient WHERE (paid != 'On Account' AND paid != '' AND paid IS NOT NULL) AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND sub_total > 0 AND service_category != '' GROUP BY service_category");
    $cat_customer_paid = '<b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_product_service_summary.php?type=sales">Services</a></b><br>';
    while($row12 = mysqli_fetch_array($r12)) {
        $cat_customer_paid .= '&nbsp;&nbsp; - '.$row12['service_category'].' : $'.number_format($row12['total_customer_paid'],2).'<br>';
    }

        //Inventory
    $r13 = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(sub_total) AS total_customer_paid_inventory FROM invoice_patient WHERE (paid != 'On Account' AND paid != '' AND paid IS NOT NULL) AND sub_total > 0 AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND product_name != '' AND `product_name` NOT LIKE 'Miscellaneous%'"));
    $cat_customer_paid .= '<br><b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_inventory_summary.php?type=sales">Inventory</a></b> : $'.number_format($r13['total_customer_paid_inventory'],2).'<br>';

        //Miscellaneous
    $r13 = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(sub_total) AS total_customer_paid_inventory FROM invoice_patient WHERE (paid != 'On Account' AND paid != '' AND paid IS NOT NULL) AND sub_total > 0 AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND product_name != '' AND `product_name` LIKE 'Miscellaneous%'"));
    $cat_customer_paid .= '<br><b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_inventory_summary.php?type=sales">Miscellaneous</a></b> : $'.number_format($r13['total_customer_paid_inventory'],2).'<br>';

        //Package
    /*
    $r14 = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(package_price) AS total_customer_paid_package FROM invoice_patient WHERE paid != 'On Account' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND package_name != '' AND package_price > 0"));
    $cat_customer_paid .= '<br><b>Package</b> : $'.$r14['total_customer_paid_package'].'<br>';
    */

        //Refund Services
    $r15 = mysqli_query($dbc, "SELECT service_category, SUM(sub_total) AS total_customer_paid FROM invoice_patient WHERE (paid != 'On Account' AND paid != '' AND paid IS NOT NULL) AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND sub_total < 0 AND service_category != '' GROUP BY service_category");
    $cat_customer_paid .= '<br><b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_product_service_summary.php?type=sales">Refund Services</a></b><br>';
    while($row15 = mysqli_fetch_array($r15)) {
        $cat_customer_paid .= '&nbsp;&nbsp; - '.$row15['service_category'].' : $'.number_format($row15['total_customer_paid'],2).'<br>';
    }

        //Refund Inventory
    $r16 = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(sub_total) AS total_customer_paid_inventory FROM invoice_patient WHERE (paid != 'On Account' AND paid != '' AND paid IS NOT NULL) AND sub_total < 0 AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND product_name != '' AND `product_name` NOT LIKE 'Miscellaneous%'"));
    $cat_customer_paid .= '<br><b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_inventory_summary.php?type=sales">Refund Inventory</a></b> : $'.number_format($r16['total_customer_paid_inventory'],2).'<br>';

        //Refund Miscellaneous
    $r16 = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(sub_total) AS total_customer_paid_inventory FROM invoice_patient WHERE (paid != 'On Account' AND paid != '' AND paid IS NOT NULL) AND sub_total < 0 AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND product_name != '' AND `product_name` LIKE 'Miscellaneous%'"));
    $cat_customer_paid .= '<br><b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_inventory_summary.php?type=sales">Refund Miscellaneous</a></b> : $'.number_format($r16['total_customer_paid_inventory'],2).'<br>';

        //Refund Package
    /*
    $r17 = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(package_price) AS total_customer_paid_refund_package FROM invoice_patient WHERE paid != 'On Account' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND package_name != '' AND package_price < 0"));
    $cat_customer_paid .= '<br><b>Refund Package</b> : $'.$r17['total_customer_paid_refund_package'].'<br>';
    */

    ////Customer Paid

    ////Insurer Paid
    $all_insurer_paid = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(sub_total) as `all_insurer_paid` FROM invoice_insurer WHERE paid = 'Yes' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));
    $insurer_paid = $all_insurer_paid['all_insurer_paid'];

        //Services
    $r18 = mysqli_query($dbc, "SELECT service_category, SUM(sub_total) AS total_ins FROM invoice_insurer WHERE paid = 'Yes' AND sub_total > 0 AND service_category != ''  AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') GROUP BY service_category");
    $cat_insurer_paid = '<b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_product_service_summary.php?type=sales">Services</a></b><br>';
    while($row18 = mysqli_fetch_array($r18)) {
        $cat_insurer_paid .= '&nbsp;&nbsp; - '.$row18['service_category'].' : $'.number_format($row18['total_ins'],2).'<br>';
    }

        //Inventory
    $r19 = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(sub_total) AS total_ins_inventory FROM invoice_insurer WHERE paid = 'Yes' AND sub_total > 0 AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND product_name != ''"));
    $cat_insurer_paid .= '<br><b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_inventory_summary.php?type=sales">Inventory</a></b> : $'.number_format($r19['total_ins_inventory'],2).'<br>';

        //Package
    /*
    $r20 = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(package_price) AS total_ins_paid_package FROM invoice_insurer WHERE paid = 'Yes' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND package_name != '' AND package_price > 0"));
    $cat_insurer_paid .= '<br><b>Package</b> : $'.$r20['total_ins_paid_package'].'<br>';
    */

        //Refund Services
    $r21 = mysqli_query($dbc, "SELECT service_category, SUM(sub_total) AS total_ins FROM invoice_insurer WHERE paid = 'Yes' AND sub_total < 0 AND service_category != '' AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') GROUP BY service_category");
    $cat_insurer_paid .= '<br><b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_product_service_summary.php?type=sales">Refund Services</a></b><br>';
    while($row21 = mysqli_fetch_array($r21)) {
        $cat_insurer_paid .= '&nbsp;&nbsp; - '.$row21['service_category'].' : $'.number_format($row21['total_ins'],2).'<br>';
    }

        //Refund Inventory
    $r22 = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(sub_total) AS total_ins_inventory FROM invoice_insurer WHERE paid = 'Yes' AND sub_total < 0 AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND product_name != ''"));
    $cat_insurer_paid .= '<br><b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_inventory_summary.php?type=sales">Refund Inventory</a></b> : $'.number_format($r22['total_ins_inventory'],2).'<br>';

        //Refund Package
    /*
    $r23 = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(package_price) AS total_ins_paid_refund_package FROM invoice_insurer WHERE paid = 'Yes' AND (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') AND package_name != '' AND package_price < 0"));
    $cat_insurer_paid .= '<br><b>Refund Package</b> : $'.$r23['total_ins_paid_refund_package'].'<br>';
    */

    ////Insurer Paid

    ////Error Reporting
	$invoice_list = mysqli_query($dbc, "SELECT `invoice`.`invoiceid`, `invoice`.`serviceid`, `invoice`.`fee`, `invoice`.`inventoryid`, `invoice`.`sell_price`, `invoice`.`packageid`, `invoice`.`package_cost`, `invoice`.`productid`, `invoice`.`product_price`, `invoice`.`misc_item`, `invoice`.`misc_price`, `invoice`.`total_price`, `invoice`.`final_price`, IFNULL(ip.`sub_patient`,0)+IFNULL(ii.`sub_insurer`,0) sub_assigned, IFNULL(ip.`total_patient`,0)+IFNULL(ii.`total_insurer`,0) total_assigned
		FROM `invoice` LEFT JOIN (SELECT SUM(`sub_total`) sub_patient, SUM(`patient_price`) total_patient, `invoiceid` FROM `invoice_patient` GROUP BY `invoiceid`) ip ON `invoice`.`invoiceid`=ip.`invoiceid`
		LEFT JOIN (SELECT SUM(`sub_total`) sub_insurer, SUM(`insurer_price`) total_insurer, `invoiceid` FROM `invoice_insurer` GROUP BY `invoiceid`) ii ON `invoice`.`invoiceid`=ii.`invoiceid` WHERE (`invoice`.`invoice_date` >= '".$starttime."' AND `invoice`.`invoice_date` <= '".$endtime."')");
	$un_payment = 0;
	$un_service = 0;
	$un_total = 0;
	while($invoice_row = mysqli_fetch_array($invoice_list)) {
		$row_un_payment = 0;
		$row_un_service = 0;
		if($invoice_row['total_price'] != $invoice_row['sub_assigned']) {
			$row_un_payment = $invoice_row['total_price'] - $invoice_row['sub_assigned'];
		}
		$services_inventory = 0;
		foreach(explode(',',$invoice_row['serviceid']) as $this_row => $this_id) {
			if($this_id > 0) {
				$services_inventory += explode(',', $invoice_row['fee'])[$this_row];
				$total_services += explode(',', $invoice_row['fee'])[$this_row];
			}
		}
		foreach(explode(',',$invoice_row['inventoryid']) as $this_row => $this_id) {
			if($this_id > 0) {
				$services_inventory += explode(',', $invoice_row['sell_price'])[$this_row];
				$total_inventory += explode(',', $invoice_row['sell_price'])[$this_row];
			}
		}
		foreach(explode(',',$invoice_row['packageid']) as $this_row => $this_id) {
			if($this_id > 0) {
				$services_inventory += explode(',', $invoice_row['package_cost'])[$this_row];
				$total_packages += explode(',', $invoice_row['package_cost'])[$this_row];
			}
		}
		foreach(explode(',',$invoice_row['productid']) as $this_row => $this_id) {
			if($this_id > 0) {
				$services_inventory += explode(',', $invoice_row['product_price'])[$this_row];
				$total_products += explode(',', $invoice_row['product_price'])[$this_row];
			}
		}
		foreach(explode(',',$invoice_row['misc_item']) as $this_row => $this_id) {
			if(!empty($this_id)) {
				$services_inventory += explode(',', $invoice_row['misc_price'])[$this_row];
				$total_misc += explode(',', $invoice_row['misc_price'])[$this_row];
			}
		}
		if($invoice_row['total_price'] != $services_inventory) {
			$row_un_service = $invoice_row['total_price'] - $services_inventory;
		}
		$un_payment += $row_un_payment;
		$un_service += $row_un_service;
		$un_total += (abs($row_un_payment) > abs($row_un_service) ? $row_un_payment : $row_un_service);
	}
	$cat_unassigned = '<p><b><a href="'.WEBSITE_URL.'/Reports/report_unassigned_invoices.php?type=sales">Unassigned Payments</a></b>: '.number_format($un_payment,2).'</p>';
	$cat_unassigned .= '<p><b>Unassigned Services / Inventory</b>: '.number_format($un_service,2).'</p>';
	$cat_unassigned .= '<p><b>Total</b>: '.number_format($un_total,2).'</p>';
    ////Error Reporting

    //Total Sales
    $total_daily_sales = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(total_price) as `total_daily_sales` FROM invoice WHERE (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));
    $total_sales = $total_daily_sales['total_daily_sales'];

    $total_daily_sales = 0;
    //Total Sales

    //Total GST
    $total_gst = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(gst_amt) as `total_gst` FROM invoice WHERE (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));
    $total_gst_amount = $total_gst['total_gst'];

    $total_gst = 0;
    //Total GST

    //Promotion
    $promotion = 0;
    $all_promotion1 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(promotion_price) as `all_promotion1` FROM invoice_insurer WHERE (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));
    $promotion += $all_promotion1['all_promotion1'];

    $all_promotion2 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(promotion_price) as `all_promotion2` FROM invoice_patient WHERE (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));
    $promotion += $all_promotion2['all_promotion2'];

    $result2 = mysqli_query($dbc, "SELECT promotion_name, SUM(promotion_price) AS total_promotion1 FROM invoice_insurer WHERE (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') GROUP BY promotion_name");
    $cat_promotion = '<br>';
    while($row2 = mysqli_fetch_array($result2)) {
        $cat_promotion .= '&nbsp;&nbsp; - '.$row2['promotion_name'].' : $'.number_format($row2['total_promotion1'],2).'<br>';
    }

    $result2 = mysqli_query($dbc, "SELECT promotion_name, SUM(promotion_price) AS total_promotion2 FROM invoice_patient WHERE (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."') GROUP BY promotion_name");
    while($row2 = mysqli_fetch_array($result2)) {
        $cat_promotion .= '&nbsp;&nbsp; - '.$row2['promotion_name'].' : $'.number_format($row2['total_promotion2'],2).'<br>';
    }

    //Promotion

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="33%">
		<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Not Paid or On Account"><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span>
		Customer A/R
	</th>
    <th width="33%">
		<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Paid"><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span>
		Customer Paid
	</th>
    <th width="34%">
		<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="All Errors: Unassigned Payments are not recorded as being to the Insurer or the Patient, and No Services / Inventory are not assigned to particular Services or Inventory. The total is the total amount that falls into one of the categories, some of which overlap."><img src="'. WEBSITE_URL .'/img/info.png" width="20" style="padding-bottom:5px;"></a></span>
		Unassigned/Errors
	</th>
    </tr>';

    $report_data .= '<tr nobr="true">';

    $report_data .= '<td>';
        /*
        $report_data .= '<a href="../Reports/report_receivables.php?from='.$starttime.'&to='.$endtime.'"><b>$' . number_format($customer_ar, 2).'</b></a><br><br>'.$cat_customer_ar;
        */
        $report_data .= $cat_customer_ar.'<br><b>Total : $' . number_format($customer_ar, 2).'</b>';
    $report_data .= '</td>';

    /*$report_data .= '<td>';
    $report_data .= '
    <a href="../Reports/report_receivables.php?from='.$starttime.'&to='.$endtime.'"><b>$' . number_format($insurer_ar, 2).'</b></a><br><br>'.$cat_insurer_ar.'<br>';
    */

    /* $report_data .= $cat_insurer_ar.'<br><b>Total : $' . number_format($insurer_ar, 2).'</b>';
    $report_data .= '</td>'; */

    $report_data .= '<td>';
    /*
    $report_data .= '<a href="../Reports/report_patient_paid_invoices.php?from='.$starttime.'&to='.$endtime.'"><b>$' . number_format($customer_paid, 2).'</b></a><br><br>'.$cat_customer_paid.'<br>';
    */
    $report_data .= $cat_customer_paid.'<br><b>Total : $' . number_format($customer_paid, 2).'</b>';
    $report_data .= '</td>';

    /*$report_data .= '<td>';
    $report_data .= '
    <a href="../Account%20Receivables/insurer_account_receivables_report.php?p1='.$starttime.'&p2='.$endtime.'"><b>$' . number_format($insurer_paid, 2).'</b></a><br><br>'.$cat_insurer_paid.'<br>';
    */
    /* $report_data .= $cat_insurer_paid.'<br><b>Total : $' . number_format($insurer_paid, 2).'</b>';
    $report_data .= '</td>'; */

	$report_data .= "<td>".$cat_unassigned."</td>";
    $report_data .= '</tr>';

    // Unassigned/Error Invoices
    $ftotal = 0;

    $total_insurer = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(sub_total) as `total_insurer_price` FROM invoice_insurer WHERE (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));
    $total_patient = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(sub_total) as `total_patient_price` FROM invoice_patient WHERE (invoice_date >= '".$starttime."' AND invoice_date <= '".$endtime."')"));
    $total_each = $total_insurer['total_insurer_price']+$total_patient['total_patient_price'];

    if($total_sales != $total_each) {
        $error_in = ($total_sales-$total_each);
        $ftotal += $error_in;
    }

    $report_data .= '<tr nobr="true">';
    $report_data .= '<td colspan="3">';
    $report_data .= '<b><a href="'.WEBSITE_URL.'/Reports/report_unassigned_invoices.php?type=sales">Unassigned/Errors</a> : $'. number_format($un_total, 2).'</b>';
    $report_data .= '</td>';
    $report_data .= '</tr>';

    // Unassigned/Error Invoices

	if($total_services != 0) {
		$report_data .= '<tr nobr="true">';
		$report_data .= '<td colspan="3">';
		$report_data .= '<b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_product_service_summary.php?type=sales">Services</a> : $'. number_format($total_services, 2).'</b>';
		$report_data .= '</td>';
		$report_data .= '</tr>';
	}

	if($total_inventory != 0) {
		$report_data .= '<tr nobr="true">';
		$report_data .= '<td colspan="3">';
		$report_data .= '<b><a href="'.WEBSITE_URL.'/Reports/report_sales_by_inventory_summary.php?type=sales">Inventory</a> : $'. number_format($total_inventory, 2).'</b>';
		$report_data .= '</td>';
		$report_data .= '</tr>';
	}

	if($total_packages != 0) {
		$report_data .= '<tr nobr="true">';
		$report_data .= '<td colspan="3">';
		$report_data .= '<b>Packages : $'. number_format($total_packages, 2).'</b>';
		$report_data .= '</td>';
		$report_data .= '</tr>';
	}

	if($total_products != 0) {
		$report_data .= '<tr nobr="true">';
		$report_data .= '<td colspan="3">';
		$report_data .= '<b>Products : $'. number_format($total_products, 2).'</b>';
		$report_data .= '</td>';
		$report_data .= '</tr>';
	}

	if($total_misc != 0) {
		$report_data .= '<tr nobr="true">';
		$report_data .= '<td colspan="3">';
		$report_data .= '<b>Miscellaneous : $'. number_format($total_misc, 2).'</b>';
		$report_data .= '</td>';
		$report_data .= '</tr>';
	}

    $report_data .= '<tr nobr="true">';
    $report_data .= '<td colspan="3">';
    $report_data .= '<b>Total Sales : $'. number_format($total_sales, 2).'</b>';
    $report_data .= '</td>';
    $report_data .= '</tr>';

    $report_data .= '<tr nobr="true">';
    $report_data .= '<td colspan="3">';
    $report_data .= '<b>Total GST : $'. number_format($total_gst_amount, 2).'</b>';
    $report_data .= '</td>';
    $report_data .= '</tr>';

    $report_data .= '<tr nobr="true">';
    $report_data .= '<td colspan="3">';
    $report_data .= '<b>Grand Total (including GST) : $'. number_format($total_gst_amount+$total_sales, 2).'</b>';
    $report_data .= '</td>';
    $report_data .= '</tr>';

    if($promotion != 0) {
        $report_data .= '<tr nobr="true">';
        $report_data .= '<td colspan="3">';
        $report_data .= '<b>Promotion : $' . number_format($promotion, 2).'</b>'.$cat_promotion;
        $report_data .= '</td>';
        $report_data .= '</tr>';
    }

    $report_data .= '</table>';

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