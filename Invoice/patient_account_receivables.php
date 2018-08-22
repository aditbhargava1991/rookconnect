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
</script>
</head>
<body>
<?php include_once ('../navigation.php');
$purchaser_config = explode(',',get_config($dbc, 'invoice_purchase_contact'));
define('PURCHASER', count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0]); ?>

<div class="container triple-pad-bottom">
	<div class="iframe_overlay" style="display:none; margin-top:-20px; padding-bottom:20px;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="edit_board" src=""></iframe>
		</div>
	</div>
    <div class="row">
        <div class="col-md-12">
        <h2><?= PURCHASER ?> Accounts Receivable</h2>
        
        <?php if(config_visible_function($dbc, (FOLDER_NAME == 'posadvanced' ? 'posadvanced' : 'check_out')) == 1) {
            echo '<a href="field_config_invoice.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
        } ?>
		<?php include('tile_tabs.php'); ?>
        
        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

            <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Displays <?= PURCHASER ?> specific receivables within the selected dates.</div>
            <div class="clearfix"></div>
            </div>

            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
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
                $starttime = date('Y-m-d');
                $endtime = date('Y-m-d');
                $patient = '';
                $invoice_no = '';
            }
            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-d');
            }

            if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            }

            if(!empty($_GET['from'])) {
                $starttime = $_GET['from'];
                $endtime = $_GET['until'];
                $patient = $_GET['patientid'];
            }
            ?>

			<br /><br />

			<div class="form-group">

                <label for="site_name" class="col-sm-1 control-label">
					<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Search for invoice(s) by patient name."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<?= PURCHASER ?>:
				</label>
                <div class="col-sm-8" style="width:auto">
                    <select data-placeholder="Choose <?= PURCHASER ?>..." name="patient" class="chosen-select-deselect form-control" width="380">
                        <option value="">Display All</option>
                        <?php
                        $query = mysqli_query($dbc,"SELECT distinct(patientid) FROM invoice_patient WHERE paid IN ('On Account','No') ORDER BY patientid");
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

				From:
                    <input name="starttime" type="text" class="datepicker" value="<?php echo $starttime; ?>">
                Until:
                    <input name="endtime" type="text" class="datepicker" value="<?php echo $endtime; ?>">

					<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Search by invoice # directly. You must enter a complete value."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					Invoice #:
                    <input name="invoice_no" type="text" class="form-control1" value="<?php echo $invoice_no; ?>">

            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
			<span class="popover-examples list-inline" style="margin:0 0 0 10px;"><a data-toggle="tooltip" data-placement="top" title="Select this to remove all of the search filters you've applied. It will revert back to today's invoices."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <button type="submit" name="search_email_all" value="Search" class="btn brand-btn mobile-block">Display Default</button>
            </div>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="patientpdf" value="<?php echo $patient; ?>">
            <input type="hidden" name="invoice_nopdf" value="<?php echo $invoice_no; ?>">

            <?php
                echo report_receivables($dbc, $starttime, $endtime, '', '', '', $patient, $invoice_no);
            ?>

        </form>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>

<?php
function report_receivables($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $patient, $invoice_no) {
    if($patient != '') {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_patient ii, invoice i WHERE (DATE(ii.invoice_date) >= '".$starttime."' AND DATE(ii.invoice_date) <= '".$endtime."') AND ii.paid IN ('On Account','No') AND ii.invoiceid = i.invoiceid AND i.patientid = '$patient' ORDER BY ii.invoiceid");
    } else if($invoice_no != '') {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_patient ii, invoice i WHERE (DATE(ii.invoice_date) >= '".$starttime."' AND DATE(ii.invoice_date) <= '".$endtime."') AND ii.paid IN ('On Account','No') AND ii.invoiceid = i.invoiceid AND i.invoiceid='$invoice_no' ORDER BY ii.invoiceid");
    } else {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_patient ii, invoice i WHERE (DATE(ii.invoice_date) >= '".$starttime."' AND DATE(ii.invoice_date) <= '".$endtime."') AND ii.invoiceid = i.invoiceid AND ii.paid IN ('On Account','No') ORDER BY ii.invoiceid");
    }

    $report_data .= '<a href="" onclick="pay_receivables(\'all\'); return false;" class="btn brand-btn pull-right">Pay All</a>
        <span class="popover-examples list-inline pull-right" style="margin:0 0 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to enter the payment details for all listed invoices."><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span>';
    $report_data .= '<table border="1px" class="table table-bordered table-striped" style="'.$table_style.'">';
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
        $report_data .= '<td>#'.$invoiceid.'</td>';
        $report_data .= '<td>'.$row_report['invoice_date'].'</td>';
        $report_data .= '<td>'.$row_report['service_date'].'</td>';
        $report_data .= '<td>'.get_contact($dbc, $row_report['patientid']).'</td>';
        $report_data .= '<td>'.$row_report['patient_price'].'</td>';
        $report_data .= '<td><label class="form-checkbox any-width"><input type="checkbox" class="invoice" name="invoicepatientid" value="'.$row_report['invoicepatientid'].'"> Select</label><a onclick="pay_receivables('.$row_report['invoicepatientid'].'); return false;" class="btn brand-btn" href="">Pay Now</a></td>';
        $report_data .= '<input type="hidden" name="invoiceallid" value="'.$row_report['invoicepatientid'].'">';

        $report_data .= '</tr>';
        $amt_to_bill += $row_report['patient_price'];
    }
    $report_data .= '<tr nobr="true">';
    $report_data .= '<td>Total</td><td></td><td></td><td></td><td>'.number_format($amt_to_bill, 2).'</td><td></td>';
    $report_data .= "</tr>";
    $report_data .= '</table><br>';
    $report_data .= '<a href="" onclick="pay_receivables(); return false;" class="btn brand-btn pull-right">Pay Selected</a>
        <span class="popover-examples list-inline pull-right" style="margin:0 0 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to enter the payment details for the selected invoices."><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span>';

    if(!empty($_GET['from'])) {
        if($_GET['report'] == 'ar_aging') {
            $report_data .= '<div class="pad-left gap-top double-gap-bottom"><a href="../Reports/report_ar_aging_summary.php?type=ar" class="btn config-btn">Back to Receivables</a></div>';
        } else {
            $report_data .= '<div class="pad-left gap-top double-gap-bottom"><a href="../Reports/report_receivables_patient_summary.php?type=ar" class="btn config-btn">Back to Receivables</a></div>';
        }
    }

    return $report_data;
}
?>