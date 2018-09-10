<?php
/*
Client Listing
*/
include ('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
} else {
    checkAuthorised('check_out');
} ?>

<script type="text/javascript">
$(document).ready(function() {
	$(window).resize(function() {
        var available_height = window.innerHeight - $('footer:visible').height() - $('.tile-header').height() - $('.standard-body-title').height() - 5;
        if(available_height > 200) {
            $('#invoice_div .standard-body').height(available_height);
        }
    }).resize();
});

function waiting_on_collection(sel) {
	var action = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({
		type: "GET",
		url: "../ajax_all.php?fill=arcollection&invoiceinsurerid="+action,
		dataType: "html",   //expect html to be returned
		success: function(response){
			alert("Invoice moved to Collection");
			location.reload();
		}
	});
}
function pay_receivables(invoiceid) {
    if(invoiceid == 'all') {
        invoice_list = [];
        $('[name=invoiceallid]').each(function() {
            invoice_list.push(this.value);
        });
        if(invoice_list.length == 0) {
            alert('No Invoices Found!');
            return;
        }
        invoiceid = invoice_list.join(',');
    } else if(invoiceid > 0) {
        invoiceid = invoiceid;
    } else if(invoiceid == undefined) {
        invoice_list = [];
        $('[name=invoicepatientid]:checked').each(function() {
            invoice_list.push(this.value);
        });
        if(invoice_list.length == 0) {
            alert('No Invoices Selected!');
            return;
        }
        invoiceid = invoice_list.join(',');
    } else {
        alert('Invalid Invoice');
        return;
    }
    overlayIFrameSlider('pay_receivables.php?customer='+$('[name=patient]').val()+'&invoices='+invoiceid);
}
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
$purchaser_config = explode(',',get_config($dbc, 'invoice_purchase_contact'));
define('PURCHASER', count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0]); ?>

<div id="invoice_div" class="container">
	<div class="iframe_overlay" style="display:none; margin-top:-20px; padding-bottom:20px;">
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
                    <span class="pull-right gap-top offset-right-5"><img src="../img/icons/pie-chart.png" alt="View Summary" title="View Summary" class="cursor-hand no-toggle inline-img" onclick="view_summary();" /></span>
                    <div class="clearfix"></div>
                    <div class="view_tabs double-padded" style="display:none;"><?php include('tile_tabs.php'); ?></div>
                    
                    <!-- Summary Blocks --><?php
                    if(!empty($_GET['p1'])) {
                        $starttime = $_GET['p1'];
                        $endtime = $_GET['p2'];
                        $patient = $_GET['p3'];
                        $invoice_no = $_GET['p5'];
                    }
                    if (isset($_POST['search_email_submit'])) {
                        $starttime = $_POST['starttime'];
                        $endtime = $_POST['endtime'];
                        $patient = $_POST['patient'];
                        $invoice_no = $_POST['invoice_no'];
                    }
                    if (isset($_POST['search_email_all'])) {
                        $starttime = '';
                        $endtime = date('Y-m-d');
                        $patient = '';
                        $invoice_no = '';
                    }
                    if($starttime == 0000-00-00) {
                        $starttime = (in_array($rookconnect,['sea','led']) ? '' : date('Y-m-d'));
                    }

                    if($endtime == 0000-00-00) {
                        $endtime = date('Y-m-d');
                    }

                    if(!empty($_GET['from'])) {
                        $starttime = $_GET['from'];
                        $endtime = $_GET['until'];
                        $patient = $_GET['patientid'];
                    }
                    
                    $patient_clause = !empty($patient) ? "AND patientid = '$patientid'" : '';
                    $ar_types = "'On Account', 'Net 30', 'Net 30 Days', 'Net 60', 'Net 60 Days', 'Net 90', 'Net 90 Days', 'Net 120', 'Net 120 Days', ''";
                    
                    $query_ar = mysqli_query($dbc,"SELECT DISTINCT(patientid) FROM invoice_patient WHERE (paid_date > '$as_at_date' OR `paid` IN ($ar_types)) AND (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') ORDER BY patientid");
                    
                    $total_ar_current = 0;
                    $total_ar_30 = 0;
                    $total_ar_60 = 0;
                    $total_ar_90 = 0;
                    $total_ar_120 = 0;
                    
                    while($row = mysqli_fetch_array($query_ar)) {
                        $patientid = $row['patientid'];
                        $today_date = date('Y-m-d');
                        $as_at_date = $_GET['search_to'] != '' ? $_GET['search_to'] : $today_date;
                        $last29 = date('Y-m-d', strtotime($as_at_date.' - 29 days'));
                        $last30 = date('Y-m-d', strtotime($as_at_date.' - 30 days'));
                        $last59 = date('Y-m-d', strtotime($as_at_date.' - 59 days'));
                        $last60 = date('Y-m-d', strtotime($as_at_date.' - 60 days'));
                        $last89 = date('Y-m-d', strtotime($as_at_date.' - 89 days'));
                        $last90 = date('Y-m-d', strtotime($as_at_date.' - 90 days'));
                        $last119 = date('Y-m-d', strtotime($as_at_date.' - 119 days'));
                        $last120 = date('Y-m-d', strtotime($as_at_date.' - 120 days'));

                        $total_30 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND DATE(invoice_date) >= '".$last29."' AND patientid = '$patientid' AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
                        $total_last30 = $total_30['all_payment'];

                        $total_3059 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND (DATE(invoice_date) >= '".$last59."' AND DATE(invoice_date) < '".$last29."') AND patientid = '$patientid' AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
                        $total_last3059 = $total_3059['all_payment'];

                        $total_6089 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND (DATE(invoice_date) >= '".$last89."' AND DATE(invoice_date) < '".$last59."') AND patientid = '$patientid' AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
                        $total_last6089 = $total_6089['all_payment'];

                        $total_90119 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND (DATE(invoice_date) >= '".$last119."' AND DATE(invoice_date) < '".$last89."') AND patientid = '$patientid' AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
                        $total_last90119 = $total_90119['all_payment'];

                        $total_120 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND (DATE(invoice_date) < '".$last119."') AND patientid = '$patientid' AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
                        $total_last120 = $total_120['all_payment'];
                        
                        $total_ar_current += $total_last30;
                        $total_ar_30 += $total_last3059;
                        $total_ar_60 += $total_last6089;
                        $total_ar_90 += $total_last90119;
                        $total_ar_120 += $total_last120;
                    } ?>
                    
                    <div class="view_summary double-gap-bottom" style="display:none;">
                        <?php $total_invoices = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`final_price`) `final_price` FROM `invoice` WHERE `deleted`=0 $search_clause $search_invoice_clause")); ?>
                        <div class="col-xs-12 col-sm-3 gap-top">
                            <div class="summary-block">
                                <div class="text-lg"><?= ( $total_ar_current > 0 ) ? '<a href="../Reports/report_tiles.php?type=ar&report=A/R Aging Summary&from='.$starttime.'&to='.$endtime.'">$'.number_format($total_ar_current, 2).'</a>' : 0; ?></div>
                                <div>Current A/R</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-3 gap-top">
                            <div class="summary-block">
                                <div class="text-lg"><?= ( $total_ar_30 > 0 ) ? '<a href="../Reports/report_tiles.php?type=ar&report=A/R Aging Summary&from='.$starttime.'&to='.$endtime.'">$'.number_format($total_ar_30, 2).'</a>' : 0; ?></div>
                                <div>30 - 59 Days A/R</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-3 gap-top">
                            <div class="summary-block">
                                <div class="text-lg"><?= ( $total_ar_60 > 0 ) ? '<a href="../Reports/report_tiles.php?type=ar&report=A/R Aging Summary&from='.$starttime.'&to='.$endtime.'">$'.number_format($total_ar_60, 2).'</a>' : 0; ?></div>
                                <div>60 - 89 Dyas A/R</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-3 gap-top">
                            <div class="summary-block">
                                <div class="text-lg"><?= ( $total_ar_90 > 0 ) ? '<a href="../Reports/report_tiles.php?type=ar&report=A/R Aging Summary&from='.$starttime.'&to='.$endtime.'">$'.number_format($total_ar_90, 2).'</a>' : 0; ?></div>
                                <div>90 - 119 Days A/R</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-3 gap-top">
                            <div class="summary-block">
                                <div class="text-lg"><?= ( $total_ar_120 > 0 ) ? '<a href="../Reports/report_tiles.php?type=ar&report=A/R Aging Summary&from='.$starttime.'&to='.$endtime.'">$'.number_format($total_ar_120, 2).'</a>' : 0; ?></div>
                                <div>120+ Days A/R</div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div><!-- .view_summary -->
                </div>
            </div><!-- .tile-header -->

            <div class="scale-to-fill has-main-screen">
                <div class="main-screen standard-body form-horizontal">
                    <div class="standard-body-title">
                        <h3><?= PURCHASER ?> Accounts Receivable</h3>
                    </div>
                    
                    <div class="standard-body-content">
                        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

                            <div class="notice popover-examples">
                                <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
                                <div class="col-sm-11">
                                    <span class="notice-name">NOTE:</span>
                                    Displays <?= PURCHASER ?> specific receivables within the selected dates.
                                </div>
                                <div class="clearfix"></div>
                            </div>

                            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
                            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

                            <div class="form-group search-group double-gap-top">
                                <div class="col-xs-12">
                                    <div class="col-sm-6 col-xs-12">
                                        <div class="col-sm-4">
                                            <label for="site_name" class="col-sm-1 control-label">
                                                <!--<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Search for invoice(s) by patient name."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>-->
                                                <?= PURCHASER ?>:
                                            </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <select data-placeholder="Select <?= PURCHASER ?>..." name="patient" class="chosen-select-deselect form-control">
                                                <option value="">Display All</option>
                                                <?php
                                                $query = mysqli_query($dbc,"SELECT distinct(patientid) FROM invoice_patient WHERE (IFNULL(`paid`,'') IN ('On Account','No','') OR `paid` LIKE 'Net %') ORDER BY patientid");
                                                while($row = mysqli_fetch_array($query)) {
                                                    if ($patient == $row['patientid']) {
                                                        $selected = 'selected="selected"';
                                                    } else {
                                                        $selected = '';
                                                    }
                                                    echo "<option ".$selected." value='". $row['patientid']."'>".get_contact($dbc, $row['patientid']).'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xs-12">
                                        <!--<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Search by invoice # directly. You must enter a complete value."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>-->
                                        <div class="col-sm-4">
                                            <label>Search By Invoice #:</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input name="invoice_no" type="text" placeholder="Invoice #" class="form-control1 form-control" value="<?php echo $invoice_no; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <div class="col-sm-6 col-xs-12">
                                        <div class="col-sm-4">
                                            <label for="site_name" class="control-label">Search From Date:</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xs-12">
                                        <div class="col-sm-4">
                                            <label for="site_name" class="control-label">Search To Date:</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 text-right gap-top">
                                    <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
                                    <button type="submit" name="search_email_all" value="Search" class="btn brand-btn mobile-block">Display All</button>
                                </div>
                            </div>

                            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
                            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
                            <input type="hidden" name="patientpdf" value="<?php echo $patient; ?>">
                            <input type="hidden" name="invoice_nopdf" value="<?php echo $invoice_no; ?>">

                            <?php
                                echo report_receivables($dbc, $starttime, $endtime, '', '', '', $patient, $invoice_no);
                            ?>
                            
                            <?php $show_statement = ($patient > 0);
                            if($show_statement) {
                                $category = get_field_value('category','contacts','contactid',$patient);
                                $_GET['edit'] = $contactid = $patient;
                                $hide_filter_options = true;
                                $field_option = 'Account Statement';
                                include('../Contacts/edit_tile_data.php');
                            } ?>

                        </form>
                    </div><!-- .standard-body-content -->
                </div><!-- .main-screen standard-body -->
            </div><!-- .has-main-screen -->
        </div><!-- .main-screen -->
        
    </div><!-- .row -->
</div><!-- .container -->
<?php include ('../footer.php'); ?>

<?php
function report_receivables($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $patient, $invoice_no) {
    if($starttime == 0000-00-00) {
        $starttime = '0000-00-00';
    }
    if($patient != '') {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_patient ii, invoice i WHERE (DATE(ii.invoice_date) >= '".$starttime."' AND DATE(ii.invoice_date) <= '".$endtime."') AND (IFNULL(ii.`paid`,'') IN ('On Account','','No') OR ii.`paid` LIKE 'Net %') AND ii.invoiceid = i.invoiceid AND i.patientid = '$patient' AND `i`.`status` NOT IN ('Void') ORDER BY ii.invoiceid DESC");
    } else if($invoice_no != '') {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_patient ii, invoice i WHERE (DATE(ii.invoice_date) >= '".$starttime."' AND DATE(ii.invoice_date) <= '".$endtime."') AND (IFNULL(ii.`paid`,'') IN ('On Account','','No') OR ii.`paid` LIKE 'Net %') AND ii.invoiceid = i.invoiceid AND i.invoiceid='$invoice_no' AND `i`.`status` NOT IN ('Void') ORDER BY ii.invoiceid DESC");
    } else {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_patient ii, invoice i WHERE (DATE(ii.invoice_date) >= '".$starttime."' AND DATE(ii.invoice_date) <= '".$endtime."') AND ii.invoiceid = i.invoiceid AND (IFNULL(ii.`paid`,'') IN ('On Account','','No') OR ii.`paid` LIKE 'Net %') AND `i`.`status` NOT IN ('Void') ORDER BY ii.invoiceid DESC");
    }

    $report_data .= '<a href="" onclick="pay_receivables(\'all\'); return false;" class="btn brand-btn pull-right gap-top gap-bottom">Pay All</a>
        <span class="popover-examples list-inline pull-right" style="margin:15px 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to enter the payment details for all listed invoices."><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span><div class="clearfix"></div>';
    $report_data .= '<div id="no-more-tables"><table border="1px" class="table table-bordered table-striped" style="'.$table_style.'">';
    $report_data .= '<thead><tr style="'.$table_row_style.'">
    <th>Invoice#</th>
    <th>Service Date</th>
    <th>Invoice Date</th>
    <th>'.PURCHASER.'</th>
    <th>Amount Receivable</th>
    <th>Pay</th>
    </tr></thead>';

    $amt_to_bill = 0;
    while($row_report = mysqli_fetch_array($report_service)) {

        $invoiceid = $row_report['invoiceid'];
        $payment_type = ltrim($row_report['payment_type'],'#*#');

        $report_data .= '<tr nobr="true">';
        $report_data .= '<td data-title="Invoice#">'.(file_exists('download/invoice_'.$invoiceid.'.pdf') ? '<a href="download/invoice_'.$invoiceid.'.pdf" target="_blank">#'.$invoiceid.'</a>' : '#'.$invoiceid).'</td>';
        $report_data .= '<td data-title="Service Date">'.$row_report['invoice_date'].'</td>';
        $report_data .= '<td data-title="Invoice Date">'.$row_report['service_date'].'</td>';
        $report_data .= '<td data-title="'.PURCHASER.'"><a href="../Contacts/contacts_inbox.php?edit='.$row_report['patientid'].'" onclick="overlayIFrameSlider(this.href, \'auto\', false, true); return false;">'.get_contact($dbc, $row_report['patientid']).' <img class="inline-img" src="../img/person.PNG"></a></td>';
        $report_data .= '<td data-title="Amount" align="right">'.$row_report['patient_price'].'</td>';
        $report_data .= '<td data-title="Pay"><label class="form-checkbox any-width"><input type="checkbox" class="invoice" name="invoicepatientid" value="'.$row_report['invoicepatientid'].'"> Select</label><a onclick="pay_receivables('.$row_report['invoicepatientid'].'); return false;" class="btn brand-btn" href="">Pay Now</a></td>';
        $report_data .= '<input type="hidden" name="invoiceallid" value="'.$row_report['invoicepatientid'].'">';

        $report_data .= '</tr>';
        $amt_to_bill += $row_report['patient_price'];
    }
    $report_data .= '<tr nobr="true">';
    $report_data .= '<td colspan="4"><b>Total</b></td><td align="right"><b>'.number_format($amt_to_bill, 2).'</b></td><td></td>';
    $report_data .= "</tr>";
    $report_data .= '</table></div>';
    $report_data .= '<a href="" onclick="pay_receivables(); return false;" class="btn brand-btn pull-right double-gap-bottom">Pay Selected</a>
        <span class="popover-examples list-inline pull-right" style="margin:5px 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to enter the payment details for the selected invoices."><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span><div class="clearfix"></div>';

    if(!empty($_GET['from'])) {
        if($_GET['report'] == 'ar_aging') {
            $report_data .= '<div class="pad-left gap-top double-gap-bottom"><a href="../Reports/report_ar_aging_summary.php?type=ar" class="btn config-btn">Back to Receivables</a></div>';
        } else {
            // $report_data .= '<div class="pad-left gap-top double-gap-bottom"><a href="../Reports/report_receivables_patient_summary.php?type=ar" class="btn config-btn">Back to Receivables</a></div>';
        }
    }

    return $report_data;
}
?>