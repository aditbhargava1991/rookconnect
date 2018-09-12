<?php
/*
Payment/Invoice Listing
*/
include ('../include.php');
if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
} else {
    checkAuthorised('check_out');
}
include_once('../tcpdf/tcpdf.php');

if (isset($_POST['submit'])) {
    $invoiceid = $_POST['submit'];
    $refund_amount = $_POST['refund_'.$invoiceid];
    $refund_date = date('Y-m-d');
    DEFINE('INVOICE_LOGO', get_config($dbc, 'invoice_logo'));
    DEFINE('INVOICE_HEADER', html_entity_decode(get_config($dbc, 'invoice_header')));
    DEFINE('INVOICE_FOOTER', html_entity_decode(get_config($dbc, 'invoice_footer')));

    //Refund Invoice
	class MYPDF extends TCPDF {

		//Page header
		public function Header() {
            if(INVOICE_LOGO != '') {
                $image_file = 'download/'.INVOICE_LOGO;
                $this->Image($image_file, 10, 10, 80, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.INVOICE_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);
		}

		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			$this->SetY(-10);
			$this->SetFont('helvetica', 'I', 8);
			$footer_text = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages().' printed on  '.date('m/d/y').' at '.date('g:i:s A');
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);

            // Position at 15 mm from bottom
            $this->SetY(-45);
            // Set font
            $this->SetFont('helvetica', 'I', 10);
            // Page number
            $footer_text = INVOICE_FOOTER;
            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
		}
	}

	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
    $pdf->setFooterData(array(0,64,0), array(0,64,128));

    $pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 11);

	$html = '';

	$html .= '
	<b>Refund for Invoice# : '.$invoiceid.'<br/>
	Refund Amount : $'.$refund_amount.'</b><br/><br/>';

    $get_invoice = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM invoice WHERE invoiceid='$invoiceid'"));
    $patientid = $get_invoice['patientid'];

    $patients = get_contact($dbc, $patientid);

    $html .= 'Patient Information : </b><br/>'.
    $patients .'<br/>'.
    get_address($dbc, $patientid).'<br/><br/>
    ';

    $pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/refundinvoice_'.$invoiceid.'.pdf', 'F');

    $query_update_booking = "UPDATE `invoice_patient` SET `refund_amount` = '$refund_amount', `refund_date` = '$refund_date' WHERE `invoiceid` = '$invoiceid'";
    $result_update_booking = mysqli_query($dbc, $query_update_booking);

    $query_update_booking = "UPDATE `invoice` SET `refund_amount` = '$refund_amount', `refund_date` = '$refund_date' WHERE `invoiceid` = '$invoiceid'";
    $result_update_booking = mysqli_query($dbc, $query_update_booking);

    echo '<script type="text/javascript"> alert("Refund Invoice Generated."); window.location.replace("all_invoice.php");
    window.open("Download/refundinvoice_'.$invoiceid.'.pdf", "fullscreen=yes");
    </script>';

}

if (isset($_POST['submit_pay'])) {
	$all_invoice = implode(',',$_POST['invoice']);
	header('Location: add_invoice.php?action=pay&from=patient&invoiceid='.$all_invoice);
}

if((!empty($_GET['action'])) && ($_GET['action'] == 'email')) {

	$invoiceid = $_GET['invoiceid'];
	$patientid = $_GET['patientid'];

	$name_of_file = 'invoice_'.$invoiceid.'.pdf';

    $to = get_email($dbc, $patientid);
    $subject = 'Physiotherapy Invoice';
	$body = 'Please find attached your invoice from Physiotherapy';
    $attachment = 'Download/'.$name_of_file;

    send_email('', $to, '', '', $subject, $body, $attachment);

    echo '<script type="text/javascript"> alert("Invoice Successfully Sent to Patient."); window.location.replace("today_invoice.php"); </script>';

	//header('Location: unpaid_invoice.php');
    // Send Email to Client
}
?>
<script type="text/javascript" src="invoice.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$(window).resize(function() {
        var available_height = window.innerHeight - $('footer:visible').height() - $('.tile-header').height() - $('.standard-body-title').height() - 5;
        if(available_height > 200) {
            $('#invoice_div .standard-body').height(available_height);
        }
    }).resize();
    
    $('.all_view').click(function(event) {  //on click
		var arr = $('.patientid_for_invoice').val().split('_');
        if(this.checked) { // check select status
            $('.privileges_view_'+arr[1]).each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "checkbox1"
            });
        }else{
            $('.privileges_view_'+arr[1]).each(function() { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "checkbox1"
            });
        }
    });
});

function view_tabs() {
    $('.view_tabs').toggle();
}
function view_summary() {
    $('.view_summary').toggle();
}
</script>
</head>
<body>
<?php include_once ('../navigation.php');
$ux_options = explode(',',get_config($dbc, FOLDER_NAME.'_ux'));
?>
<div id="invoice_div" class="container">
    <div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="edit_board" src=""></iframe>
		</div>
	</div>
    
	<div class="row">
        <div class="main-screen">
            <div class="tile-header standard-header">
                <div class="row">
                    <h1 class="pull-left"><a href="invoice_main.php"><?= (empty($current_tile_name) ? 'Check Out' : $current_tile_name) ?></a></h1>
                    <?php if(config_visible_function($dbc, (FOLDER_NAME == 'posadvanced' ? 'posadvanced' : 'check_out')) == 1) {
                        echo '<a href="field_config_invoice.php" class="pull-right gap-right gap-top"><img width="30" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me no-toggle"></a>';
                    } ?>
                    <span class="pull-right gap-top offset-right-5"><img src="../img/icons/eyeball.png" alt="View Tabs" title="View Tabs" class="cursor-hand no-toggle inline-img" onclick="view_tabs();" /></span>
                    <span class="pull-right gap-top offset-right-5"><img src="../img/icons/pie-chart.png" alt="Reporting" title="Reporting" class="cursor-hand no-toggle inline-img" onclick="view_summary();" /></span>
                    <div class="clearfix"></div>
                    <div class="view_tabs double-padded" style="display:none;"><?php include('tile_tabs.php'); ?></div>
                    
                    <!-- Summary Blocks --><?php
                    if (isset($_POST['display_all_inventory'])) {
                        $search_user = '';
                        $search_invoiceid = '';
                        $search_date = '';
                        $search_clause = "AND (`invoice_date` BETWEEN '".date('Y-m-01')."' AND '".date('Y-m-t')."')";
                    } else if(isset($_POST['search_user_submit'])) {
                        $search_user = $_POST['search_user'];
                        $search_invoiceid = $_POST['search_invoiceid'];
                        $search_date = $_POST['search_date'];
                        $search_clause = !empty($search_user) ? "AND `patientid`='$search_user'" : '';
                        $search_clause .= !empty($search_invoiceid) ? " AND `invoiceid`='$search_invoiceid'" : '';
                        $search_clause .= !empty($search_date) ? " AND `invoice_date`='$search_date'" : '';
                    } else if(!empty($_GET['search_user'])) {
                        $search_user = $_GET['search_user'];
                        $search_invoiceid = '';
                        $search_date = '';
                        $search_clause = !empty($search_user) ? "AND `patientid`='$search_user'" : '';
                    } else if(!empty($_GET['search_invoice'])) {
                        $search_invoiceid = $_GET['search_invoice'];
                        $search_user = '';
                        $search_date = '';
                        $search_clause = !empty($search_invoiceid) ? " AND `invoiceid`='$search_invoiceid'" : '';
                    } else if(!empty($_GET['search_date'])) {
                        $search_date = $_GET['search_date'];
                        $search_user = '';
                        $search_invoiceid = '';
                        $search_clause = !empty($search_date) ? " AND `invoice_date`='$search_date'" : '';
                    } else {
                        $search_user = '';
                        $search_invoiceid = '';
                        $search_date = '';
                        $search_clause = "AND (`invoice_date` BETWEEN '".date('Y-m-01')."' AND '".date('Y-m-t')."')";
                    } ?>
                    <div class="view_summary double-gap-bottom" style="display:none;">
                        <div class="col-xs-12 col-sm-4 gap-top">
                            <div class="summary-block">
                                <?php $total_invoices = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`final_price`) `final_price` FROM `invoice` WHERE `deleted`=0 $search_clause")); ?>
                                <div class="text-lg"><?= ( $total_invoices['final_price'] > 0 ) ? '<a href="../Reports/report_tiles.php?type=sales&report=POS%20Advanced%20Sales%20Summary&landing=true&pos_submit=yes&from='.$search_from.'&to='.$search_to.'">$'.number_format($total_invoices['final_price'], 2).'</a>' : '$'. 0; ?></div>
                                <div>Total Invoices</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-4 gap-top">
                            <div class="summary-block"><?php
                                $ar_types = array('On Account', 'Net 30', 'Net 30 Days', 'Net 60', 'Net 60 Days', 'Net 90', 'Net 90 Days', 'Net 120', 'Net 120 Days');
                                $ar_amounts = 0;
                                $nonar_amounts = 0;
                                $ar_invoices = mysqli_query($dbc, "SELECT `payment_type` FROM `invoice` WHERE `deleted`=0 $search_clause");
                                while ( $row = mysqli_fetch_assoc($ar_invoices) ) {
                                    list($payment_types, $payment_amounts) = explode('#*#', $row['payment_type']);
                                    $types = explode(',', $payment_types);
                                    $amounts = explode(',', $payment_amounts);
                                    $count = count($types);
                                    for ( $i=0; $i <= $count; $i++ ) {
                                        if ( in_array($types[$i], $ar_types) ) {
                                            $ar_amounts += $amounts[$i];
                                        } else {
                                            $nonar_amounts += $amounts[$i];
                                        }
                                    }
                                } ?>
                                <div class="text-lg"><?= ( $ar_amounts > 0 ) ? '<a href="../Reports/report_tiles.php?type=sales&report=POS%20Advanced%20Sales%20Summary&landing=true&pos_submit=yes&from='.$search_from.'&to='.$search_to.'">$'.number_format($ar_amounts, 2).'</a>' : '$'. 0; ?></div>
                                <div>Total A/R Invoices</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-4 gap-top">
                            <div class="summary-block">
                                <div class="text-lg"><?= ( $nonar_amounts > 0 ) ? '<a href="../Reports/report_tiles.php?type=sales&report=POS%20Advanced%20Sales%20Summary&landing=true&pos_submit=yes&from='.$search_from.'&to='.$search_to.'">$'.number_format($nonar_amounts, 2).'</a>' : '$'. 0; ?></div>
                                <div>Total Paid Invoices</div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div><!-- .view_summary -->
                </div>
            </div><!-- .tile-header -->

            <div class="scale-to-fill has-main-screen">
                <div class="main-screen standard-body form-horizontal">
                    <div class="standard-body-title">
                        <h3>Refund / Adjustments</h3>
                    </div>
                    
                    <div class="standard-body-content">
                        <form name="invoice" method="post" action="" class="form-horizontal" role="form">
                        <div class="notice double-gap-bottom popover-examples">
                            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
                            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
                            <br>To Refund: Click Refund / Adjustments under the Function heading for the invoice you wish to access. Search by <?= $purchaser_label ?>, Invoice # and/or Invoice Date and click Search. Click the red Refund checkbox. You will now see the details of the <?= $purchaser_label ?> invoice that can be refunded.
                            <br>
                            To Adjust: Adjustment is used for adding to an existing invoice, such as when a service was not added to an invoice when it was created. Click Refund / Adjustments under the Function heading for the invoice you wish to access. Search by <?= $purchaser_label ?>, Invoice # and/or Invoice Date and click Search. Click the blue Adjustment checkbox.  You will now see the details of the <?= $purchaser_label ?> invoice that can be adjusted.</div>
                            <div class="clearfix"></div>
                        </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="col-sm-4"><label for="search_user" class="control-label">Search by <?= $purchaser_label ?>:</label></div>
                                    <div class="col-sm-8">
                                        <select data-placeholder="Select a <?= $purchaser_label ?>" name="search_user" id="search_user" class="chosen-select-deselect form-control" width="380">
                                            <option value=""></option>
                                            <?php
                                            /* function lastNameSort($a, $b) {
                                                $aLast = explode(' ', $a);
                                                $bLast = explode(' ', $b);

                                                return strcasecmp($aLast, $bLast);
                                            }

                                            $query = mysqli_query($dbc,"SELECT distinct(patientid) FROM invoice WHERE deleted = 0 AND patientid != 0");
                                            while($row = mysqli_fetch_array($query)) {
                                                $patients[$row['patientid']] = get_client($dbc,$row['patientid']).get_contact($dbc, $row['patientid']);
                                            }

                                            uasort($patients, 'lastNameSort');
                                            foreach($patients as $patientid => $patient) { */
                                            ?><!--<option <?php if ($patientid == $search_user) { echo " selected"; } ?> value='<?php echo  $patientid; ?>' ><?php echo $patient; ?></option>-->
                                            <?php	/* } */
                                            ?>
                                            <?php
                                                    /* $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
                                                    foreach($query as $id) {
                                                        echo "<option value='". $id."'>".get_contact($dbc, $id).'</option>';
                                                    } */
                                                  ?>
                                            <?php
                                                $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name FROM contacts WHERE `contactid` IN (SELECT `patientid` FROM `invoice`) AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
                                                foreach($query as $id) {
                                                    $selected = '';
                                                    $selected = $id == $search_user ? 'selected = "selected"' : '';
                                                    echo "<option ".$selected." value='".$id."'>".get_contact($dbc, $id).'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="col-sm-4"><label for="search_invoiceid" class="control-label">Invoice #:</label></div>
                                    <div class="col-sm-8"><input name="search_invoiceid" placeholder="Invoice #" id="search_invoiceid" type="text" class="form-control" value="<?php echo $search_invoiceid; ?>" /></div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="col-sm-4"><label for="search_date" class="control-label">Invoice Date:</label></div>
                                    <div class="col-sm-8"><input name="search_date" id="search_date" type="text" class="datepicker form-control" value="<?php echo $search_date; ?>" /></div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-sm-12 text-right">
                                    <button type="submit" name="search_user_submit" id="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
                                    <button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
                                </div>
                            </div>

                            <input type="hidden" name="patientpdf" value="<?php echo $search_user; ?>">
                            <input type="hidden" name="invoicepdf" value="<?php echo $search_invoiceid; ?>">
                            <input type="hidden" name="datepdf" value="<?php echo $search_date; ?>">

                            <div id="no-more-tables" class="table-responsive double-gap-top">
                            <?php
                            //echo '<a href="add_invoice.php" class="btn brand-btn pull-right">Sell</a>';
                            // Display Pager

                            /* Pagination Counting */
                            $rowsPerPage = 25;
                            $pageNum = 1;

                            if(isset($_GET['page'])) {
                                $pageNum = $_GET['page'];
                            }

                            $offset = ($pageNum - 1) * $rowsPerPage;

                            if(!empty($_GET['patientid']) && !empty($_GET['from'])) {
                                $patientid = $_GET['patientid'];
                                $from = $_GET['from'];
                                $to = $_GET['to'];
                                $query_check_credentials = "SELECT * FROM invoice WHERE deleted = 0 AND invoice_type!='Saved' AND patientid='$patientid' AND (invoice_date >= '".$from."' AND invoice_date <= '".$to."') ORDER BY invoiceid DESC, paid ASC,payment_type ASC,final_price DESC";
                            } else if(!empty($_GET['from'])) {
                                $from = $_GET['from'];
                                $to = $_GET['to'];
                                $query_check_credentials = "SELECT * FROM invoice WHERE deleted = 0 AND invoice_type!='Saved' AND (invoice_date >= '".$from."' AND invoice_date <= '".$to."') ORDER BY invoiceid DESC, paid ASC,payment_type ASC,final_price DESC";
                            } else {
                                if($search_user != '') {
                                    $query_check_credentials = "SELECT * FROM invoice WHERE deleted = 0 AND invoice_type!='Saved' AND patientid='$search_user' ORDER BY invoiceid DESC, paid ASC,payment_type ASC,final_price DESC";
                                    //$query = "SELECT count(*) as numrows FROM invoice WHERE deleted = 0 AND patientid='$search_user' ORDER BY invoiceid DESC, paid ASC,payment_type ASC,final_price DESC";
                                } else if($search_invoiceid != '') {
                                    $query_check_credentials = "SELECT * FROM invoice WHERE deleted = 0 AND invoice_type!='Saved' AND invoiceid='$search_invoiceid' ORDER BY invoiceid DESC, paid ASC,payment_type ASC,final_price DESC";
                                    //$query = "SELECT count(*) as numrows FROM invoice WHERE deleted = 0 AND invoiceid='$search_invoiceid' ORDER BY invoiceid DESC, paid ASC,payment_type ASC,final_price DESC";
                                }  else if($search_date != '') {
                                    $query_check_credentials = "SELECT * FROM invoice WHERE deleted = 0 AND invoice_type!='Saved' AND invoice_date='$search_date' ORDER BY invoiceid DESC, paid ASC,payment_type ASC,final_price DESC";
                                    //$query = "SELECT count(*) as numrows FROM invoice WHERE deleted = 0 AND invoiceid='$search_invoiceid' ORDER BY invoiceid DESC, paid ASC,payment_type ASC,final_price DESC";
                                } else {
                                    $query_check_credentials = "SELECT * FROM invoice WHERE deleted = 0 ORDER BY invoiceid DESC, paid ASC,payment_type ASC,final_price DESC LIMIT $offset, $rowsPerPage";
                                    $query = "SELECT count(*) as numrows FROM invoice WHERE deleted = 0 ORDER BY invoiceid DESC, paid ASC,payment_type ASC,final_price DESC";
                                }
                            }

                            $num_rows = 0;
                            if($search_user != '' || $search_invoiceid != '' || $search_date != '' || (!empty($_GET['from']))) {
                                $result = mysqli_query($dbc, $query_check_credentials);
                                $num_rows = mysqli_num_rows($result);
                            }

                            if($num_rows > 0) {
                                // Added Pagination //

                                //if($search_user == '' && $search_invoiceid == '' && $search_date == '') {
                                //    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                                //}
                                echo "<table border='2' cellpadding='10' class='table'>";
                                echo "<tr class='hidden-xs'>";
                                echo "<th>Invoice #</th>
                                <th>Invoice Date</th>
                                <th>".$purchaser_label."</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Invoice PDF</th>
                                <th>Function</th>
                                </tr>";
                            } else {
                                //echo "<h2>No Record Found.</h2>";
                            }

                            $final_total = 0;
                            $src_row = false;
                            $src_ids = [];
                            while($src_row || $row = mysqli_fetch_array( $result ))
                            {
                                if(!$src_row && in_array($row['invoiceid'],$src_ids)) {
                                    continue;
                                }
                                $src_row = false;
                                $patientid = $row['patientid'];
                                $invoiceid = $row['invoiceid'];

                                echo '<tr>';

                                echo '<td data-title="Invoice #">' . ($row['invoice_type'] == 'New' ? '#'.$row['invoiceid'] : $row['invoice_type'].' #'.$row['invoiceid']).($row['invoiceid_src'] > 0 ? '<br />For Invoice #'.$row['invoiceid_src'] : '') . '</td>';
                                echo '<td data-title="Date">' . $row['invoice_date'] . '</td>';

                                if($row['patientid'] != 0) {
                                    //echo '<td><a href="../Contacts/add_contacts.php?category=Patient&contactid='.$row['patientid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">'.get_contact($dbc, $row['patientid']). '</a></td>';
                                    echo '<td data-title="'.$purchaser_label.'"><a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/'.CONTACTS_TILE.'/contacts_inbox.php?edit='.$row['patientid'].'\', \'auto\', false, true, $(\'#invoice_div\').outerHeight()+20); return false;">'. get_contact($dbc, $row['patientid']) .'</a></td>';
                                } else {
                                    echo '<td data-title="'.$purchaser_label.'">-</td>';
                                }

                                //echo '<td>' . $row['service_date'] . '</td>';

                                //$serviceid = $row['serviceid'];
                                //echo '<td>'. get_all_from_service($dbc, $serviceid, 'service_code').' : '.get_all_from_service($dbc, $serviceid, 'service_type') . '</td>';

                                echo '<td data-title="Total">$<b>' . ($row['final_price']).'</b><br>';
                                    $insurer = '';
                                    $invoice_insurer =	mysqli_query($dbc,"SELECT insurer_price, paid FROM invoice_insurer WHERE	invoiceid='$invoiceid'");
                                    while($row_invoice_insurer = mysqli_fetch_array($invoice_insurer)) {
                                        $insurer .= 'I : '.$row_invoice_insurer['insurer_price'].' : '.$row_invoice_insurer['paid'].'<br>';
                                    }
                                    $patient = '';
                                    /* $invoice_patient =	mysqli_query($dbc,"SELECT SUM(patient_price) price, paid FROM invoice_patient WHERE invoiceid='$invoiceid' GROUP BY `paid`");
                                    while($row_patient_insurer = mysqli_fetch_array($invoice_patient)) {
                                        $patient .= 'P : '.$row_patient_insurer['price'].' : '.$row_patient_insurer['paid'].'<br>';
                                    } */
                                    $invoice_patient =	mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `payment_type` FROM `invoice` WHERE `invoiceid`='$invoiceid'"))['payment_type'];
                                    list($paid_payment_type, $paid_payment_amount) = explode('#*#', $invoice_patient);
                                    $paid_payment_type_arr = explode(',', $paid_payment_type);
                                    $paid_payment_amount_arr = explode(',', $paid_payment_amount);
                                    $payment_count = count($paid_payment_type_arr);
                                    for ($i=0; $i<$payment_count; $i++) {
                                        echo '$'. number_format($paid_payment_amount_arr[$i],2) .' - '. $paid_payment_type_arr[$i] .'<br />';
                                    }
                                echo $insurer.$patient.'</td>';

                                $patient_paid = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`patient_price`) total_paid FROM `invoice_patient` WHERE `invoiceid`='".$row['invoiceid']."' AND IFNULL(`paid`,'') NOT IN ('On Account','')"))['total_paid'];
                                $insurer_paid = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`insurer_price`) total_paid FROM `invoice_insurer` WHERE `invoiceid`='".$row['invoiceid']."' AND `paid`='Yes'"))['total_paid'];
                                $insurer_owing = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`insurer_price`) total_paid FROM `invoice_insurer` WHERE `invoiceid`='".$row['invoiceid']."' AND `paid`!='Yes'"))['total_paid'];
                                if($row['final_price'] == $patient_paid + $insurer_paid) {
                                    $paid = 'Paid in Full';
                                } else if ($row['final_price'] == $patient_paid + $insurer_paid + $insurer_owing) {
                                    $paid = 'Balance Paid in Full<br />Balance Owing: $'.number_format($insurer_owing, 2);
                                } else {
                                    $paid = 'Balance Owing: $'.number_format($row['final_price'] - $patient_paid - $insurer_paid - $insurer_owing, 2);
                                    $paid = 'Balance Paid in Full<br />Balance Owing: $'.number_format($insurer_owing, 2);
                                }

                                echo '<td data-title="Paid">' . $paid . '</td>';

                                echo '<td data-title="Invoice PDF">';
                                if($row['final_price'] != '' && $row['invoice_type'] != 'Saved') {
                                    $name_of_file = 'download/invoice_'.$row['invoiceid'].'.pdf';
                                    if(file_exists($name_of_file)) {
                                        //$md5 = md5_file($name_of_file);
                                        //if($md5 == $row['invoice_md5']) {
                                            echo '<a href="'.$name_of_file.'" target="_blank">Invoice #'.$row['invoiceid'].' <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a><br />';
                                            //echo '| <a href=\'unpaid_invoice.php?action=email&invoiceid='.$row['invoiceid'].'&patientid='.$patientid.'\' >Email</a></td>';
                                        //} else {
                                        //    echo '<td>(Error : File has been Changed)</td>';
                                        //}
                                    }
                                }
                                if($row['invoiceid_src'] > 0) {
                                    $name_of_file = 'download/invoice_'.$row['invoiceid_src'].'.pdf';
                                    if(file_exists($name_of_file)) {
                                        echo '<a href="'.$name_of_file.'" target="_blank">Primary Invoice #'.$row['invoiceid_src'].' <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a>';
                                    }
                                }
                                echo '</td>';

                                /* echo '<td>';
                                if($row['patient_payment_receipt'] == 1) {
                                    $name_of_file = 'Download/patientreceipt_'.$row['invoiceid'].'.pdf';
                                    if(file_exists($name_of_file)) {
                                        echo '<a href="'.$name_of_file.'" target="_blank">Receipt  <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a><br />';
                                    }
                                    $receipts = mysqli_fetch_all(mysqli_query($dbc, "SELECT `receipt_file` FROM `invoice_patient` WHERE `invoiceid`='".$row['invoiceid']."' AND `receipt_file` IS NOT NULL"));
                                    $receipt_list = [];
                                    foreach($receipts as $receipt) {
                                        $receipt_list = array_merge($receipt_list, explode('#*#',$receipt[0]));
                                    }
                                    foreach(array_unique(array_filter($receipt_list)) as $receipt) {
                                        if(file_exists($receipt)) {
                                            echo '<a href="'.$receipt.'" target="_blank">Account Receipt  <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a><br />';
                                        }
                                    }
                                }
                                echo '</td>'; */

                                $invoiceid= $row['invoiceid'];
                                $patient_price =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS total_patient_pay FROM invoice_patient WHERE	invoiceid='$invoiceid' AND paid != 'On Account'"));

                                //echo '<td>';
                                //if($row['refund_amount'] == '') {
                                //    echo '<a id="refund_btn link_'.$row['invoiceid'].'" onclick="changeRefund(this);">Refund</a>';
                                //    echo '<input style= "width:40%;" value="'.$patient_price['total_patient_pay'].'" name="refund_'.$row['invoiceid'].'" id="tb_'.$row['invoiceid'].'" type="text" class="form-control refund_tb">
                                //    <button type="submit" id="btn_'.$row['invoiceid'].'" name="submit" value="'.$row['invoiceid'].'" class="btn brand-btn refund_tb">Submit</button>
                                //    ';
                                //} else {
                                //    $name_of_file = 'Download/refundinvoice_'.$row['invoiceid'].'.pdf';
                                //    echo '<a href="'.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a>';
                                //}
                                //echo '</td>';

                                //if($row['paid'] != 'Yes' && $row['final_price'] != '') {
                                    if($row['invoice_type'] == 'Saved') {
                                        echo '<td data-title="Function"><a href=\'add_invoice.php?invoiceid='.$row['invoiceid'].'&contactid='.$row['patientid'].'&search_user='.$search_user.'&search_invoice='.$search_invoiceid.'\' >Edit</a>';
                                        $role = $_SESSION['role'];
                                        if($role== 'super' || $role == ',office_admin,' || $role == ',executive_front_staff,') {
                                            echo ' | <a onclick="return confirm(\'Are you sure you want to archive this invoice?\')" href=\'today_invoice.php?invoiceid='.$row['invoiceid'].'&action=delete\' >Archive</a>';
                                        }
                                    } else {
                                        echo '<td data-title="Function"><a href=\'adjust_invoice.php?invoiceid='.($row['invoiceid_src'] == 0 ? $row['invoiceid'] : $row['invoiceid_src']).'&contactid='.$row['patientid'].'&search_user='.$search_user.'&search_invoice='.$search_invoiceid.'\' >Refund / Adjustments</a>';
                                    }
                                //} else {
                                //    echo '<td>-</td>';
                                //}

                                /*
                                if($row['final_price'] != '' && $row['insurerid'] != 0) {
                                    echo '<td>';
                                    $all_insurerid = explode(',', $row['insurerid']);
                                    foreach($all_insurerid as $insurerid){
                                        if($insurerid != '') {
                                        $name_of_file = 'Download/insuranceinvoice_'.$insurerid.'_'.$row['invoiceid'].'.pdf';
                                        echo '<a href="'.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a>';
                                        }
                                    }
                                    echo '</td>';
                                } else {
                                    echo '<td>-</td>';
                                }
                                $invoiceid = $row['invoiceid'];
                                $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT refundid FROM invoice_refund WHERE	invoiceid='$invoiceid'"));
                                if($get_staff['refundid'] == '') {
                                    echo '<td><a href=\'refund_invoice.php?invoiceid='.$row['invoiceid'].'\' >Refund</a></td>';
                                } else {
                                    $name_of_file = 'Download/refundinvoice_'.$row['invoiceid'].'_'.$get_staff['refundid'].'.pdf';
                                    echo '<td><a href="'.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a></td>';
                                }

                                $invoiceid = $row['invoiceid'];
                                if($row['final_price'] != '') {
                                    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(invoiceinsurerid) AS total_invoiceinsurerid FROM	invoice_insurer WHERE invoiceid='$invoiceid' AND (paid='Waiting on Insurer' OR paid='No')"));


                                    //if($get_staff['total_invoiceinsurerid'] != 0) {
                                        echo '<td><input type="checkbox" onchange="waiting_on_insurer(this)" value="'.$row['invoiceid'].'"></td>';
                                    //} else {
                                       // echo '<td><img src="'.WEBSITE_URL.'/img/filled_star.png" width="20" height="20" border="0" alt=""></td>';
                                    //}
                                } else {
                                    echo '<td>-</td>';
                                }

                                */

                                $final_total += $row['final_price'];
                                echo "</tr>";
                                if($row['invoiceid_src'] > 0) {
                                    $row = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `invoice` WHERE `invoiceid`='".$row['invoiceid_src']."'"));
                                    $src_row = true;
                                    $src_ids[] = $row['invoiceid'];
                                }
                            }

                            echo "<tr>";
                                echo "<td colspan='3'><b>Total</b></td><td colspan='5'><b>$".$final_total."</b></td>";
                            echo "</tr>";

                            echo '</table></div>';
                            // Added Pagination //

                            //if($search_user == '' && $search_invoiceid == '' && $search_date == '') {
                            //    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                            //}
                            //echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                            //echo '<a href="add_invoice.php" class="btn brand-btn pull-right">Sell</a>';
                            ?>

                        </form>
                    </div><!-- .standard-body-content -->
                </div><!-- .main-screen standard-body -->
            </div><!-- .has-main-screen -->
        </div><!-- .main-screen -->

        <?php
        if(!empty($_GET['patientid'])) {
            echo '<a href="'.WEBSITE_URL.'/Reports/report_sales_by_customer_summary.php?from='.$_GET['from'].'&to='.$_GET['to'].'" class="btn brand-btn">Back</a>';
        } else if(!empty($_GET['from'])) {
            echo '<a href="'.WEBSITE_URL.'/Reports/report_daily_sales_summary.php?from='.$_GET['from'].'&to='.$_GET['to'].'" class="btn brand-btn">Back</a>';
        } else {
            //echo '<a href="'.WEBSITE_URL.'/home.php" class="btn brand-btn">Back</a>';
        }
        ?>

	</div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>