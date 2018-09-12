<?php
/*
Client Listing
*/
include ('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
if(!empty($folder)) {
} else if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
} else {
    checkAuthorised('check_out');
}

if (isset($_POST['submit_pay'])) {
    $invoiceinsurerid = $_POST['invoiceinsurerid'];
    $deposit_number = $_POST['deposit_number'];
    $insurer_price = 0;
    $paid_date = date('Y-m-d');

    foreach ($_POST['invoiceinsurerid'] as $id => $value) {
		$query_update_in = "UPDATE `invoice_insurer` SET `paid` = 'Yes', `deposit_number` = '$deposit_number', `paid_date` = '$paid_date' WHERE `invoiceinsurerid` = '$value'";
		$result_update_in = mysqli_query($dbc, $query_update_in);

        $invoiceid = get_all_from_invoice_insurer($dbc, $value, 'invoiceid');

        $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(invoiceinsurerid) AS total_invoiceinsurerid FROM	invoice_insurer WHERE invoiceid='$invoiceid' AND paid='Waiting on Insurer'"));

        if($get_staff['total_invoiceinsurerid'] == 0) {
		    $query_update_in = "UPDATE `invoice` SET `paid` = 'Yes' WHERE `invoiceid` = '$invoiceid'";
		    $result_update_in = mysqli_query($dbc, $query_update_in);
        }
        $insurer_price += get_all_from_invoice_insurer($dbc, $invoiceinsurerid, 'insurer_price');
    }

    /*
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(summaryid) AS summaryid FROM report_summary WHERE DATE(today_date) = '$paid_date'"));
    if($get_config['summaryid'] == 0) {
        $query_insert_summary = "INSERT INTO `report_summary` (`today_date`) VALUES ('$paid_date')";
        $result_insert_summary = mysqli_query($dbc, $query_insert_summary);
    }

    $query_update_summary = "UPDATE `report_summary` SET `daily_payment_amount` = `daily_payment_amount` + '$insurer_price', `Direct Deposit` = `Direct Deposit` + $insurer_price WHERE DATE(today_date) = DATE(NOW())";
    $result_update_summary = mysqli_query($dbc, $query_update_summary);
    */

    $starttimepdf = $_POST['starttimepdf'];
    $endtimepdf = $_POST['endtimepdf'];
    $insurerpdf = $_POST['insurerpdf'];
    $invoice_nopdf = $_POST['invoice_nopdf'];
    $ui_nopdf = $_POST['ui_nopdf'];
    $ui_totalpdf = $_POST['ui_totalpdf'];

    echo '<script type="text/javascript"> window.location.replace("insurer_account_receivables.php?p1='.$starttimepdf.'&p2='.$endtimepdf.'&p3='.$insurerpdf.'&p5='.$invoice_nopdf.'&p6='.$ui_nopdf.'&p7='.$ui_totalpdf.'"); </script>';
}

?>

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
			//alert("Invoice moved to Collection");
			location.reload();
		}
	});
}
</script>
</head>
<body>
<?php include_once ('../navigation.php');
$payer_config = explode(',',get_config($dbc, 'invoice_payer_contact'));
define('PAYER_LABEL', count($payer_config) > 1 ? 'Third Party' : $payer_config[0]); ?>

<div class="container triple-pad-bottom">
    <div class="row">
        <h2>U<?= substr(PAYER_LABEL,0,1) ?> Invoice Report</h2>
        <?php if(!isset($download_folder) && config_visible_function($dbc, (FOLDER_NAME == 'posadvanced' ? 'posadvanced' : 'check_out')) == 1) {
            echo '<a href="field_config_invoice.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
        } ?>
        
		<?php include($folder.'tile_tabs.php'); ?>
        
        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

            <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            U<?= substr(PAYER_LABEL,0,1) ?> Reports are grouped receivables (this tab displays the groups and their total amounts). </div>
            <div class="clearfix"></div>
            </div>

            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if(!empty($_GET['p1'])) {
                $insurer = $_GET['p3'];
                $invoice_no = $_GET['p5'];
                $ui_no = $_GET['p6'];
                $ui_total = $_GET['p7'];
            }
            if (isset($_POST['search_email_submit'])) {
                $insurer = $_POST['insurer'];
                $invoice_no = $_POST['invoice_no'];
                $ui_no = $_POST['ui_no'];
                $ui_total = $_POST['ui_total'];
            }
            if (isset($_POST['search_email_all'])) {
                $insurer = '';
                $invoice_no = '';
                $ui_no = '';
                $ui_total = '';
            }
            ?>
            <br /><br />

            <div class="form-group">
                <label for="site_name" class="col-sm-2 control-label">
					<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Search for invoice(s) by <?= PAYER_LABEL ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<?= PAYER_LABEL ?>:
				</label>
                <div class="col-sm-8" style="width:auto">
                    <select data-placeholder="Choose a <?= PAYER_LABEL ?>..." name="insurer" class="chosen-select-deselect form-control" width="380">
                        <option value="">Display All</option>
						<?php
							$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN ('".implode("','",$payer_config)."') AND deleted=0 AND `status`=1"),MYSQLI_ASSOC));
							foreach($query as $id) {
								$selected = '';
								$selected = $id == $insurer ? 'selected = "selected"' : '';
								echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id,'name').'</option>';
							}
						?>
                    </select>
                </div>

                <span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Search by invoice # directly. You must enter a complete value."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                Invoice #:
                <input name="invoice_no" type="text" class="form-control1" value="<?php echo $invoice_no; ?>">

                <span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Search by the generated U<?= substr(PAYER_LABEL,0,1) ?> #."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                U<?= substr(PAYER_LABEL,0,1) ?> #:
                <input name="ui_no" type="text" class="form-control1" value="<?php echo $ui_no; ?>">

                <span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Search by the total value of the generated U<?= substr(PAYER_LABEL,0,1) ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                U<?= substr(PAYER_LABEL,0,1) ?> Total $:
                <input name="ui_total" type="text" class="form-control1" value="<?php echo $ui_total; ?>">
			<br><br>
			<div style="text-align:center">
				<button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
				<span class="popover-examples list-inline" style="margin:0 0 0 10px;"><a data-toggle="tooltip" data-placement="top" title="Select this to remove all of the search filters you've applied. It will revert back to today's invoices."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<button type="submit" name="search_email_all" value="Search" class="btn brand-btn mobile-block">Display All</button>
			</div>
            <br>

            <input type="hidden" name="insurerpdf" value="<?php echo $insurer; ?>">
            <input type="hidden" name="invoice_nopdf" value="<?php echo $invoice_no; ?>">
            <input type="hidden" name="ui_nopdf" value="<?php echo $ui_no; ?>">
            <input type="hidden" name="ui_totalpdf" value="<?php echo $ui_total; ?>">

            <!-- <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button> -->
            <br><br>

            <?php
                echo report_receivables($dbc, '', '', '', $insurer, $invoice_no, $ui_no, $ui_total);
            ?>
			</div>

        </form>
    </div>
</div>
<?php include ('../footer.php'); ?>

<?php
function report_receivables($dbc, $table_style, $table_row_style, $grand_total_style, $insurer, $invoice_no, $ui_no, $ui_total) {

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th>U'.substr(PAYER_LABEL,0,1).'#</th>
    <th>Invoice#</th>
    <th>Service Date</th>
    <th>Invoice Date</th>
    <th>'.PAYER_LABEL.'</th>
    <th>Amount Receivable</th>
    </tr>';

    if($insurer != '') {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE ii.insurerid='$insurer' AND ii.invoiceid = i.invoiceid AND ii.paid IN ('Waiting on Insurer','No') AND ii.ui_invoiceid IS NOT NULL ORDER BY IF(i.invoiceid_src = '' OR i.invoiceid_src IS NULL, i.invoiceid, i.invoiceid_src), i.invoiceid");
    } else if($invoice_no != '') {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE ii.paid IN ('Waiting on Insurer','No') AND ii.invoiceid = i.invoiceid AND i.invoiceid='$invoice_no' AND ii.ui_invoiceid IS NOT NULL ORDER BY IF(i.invoiceid_src = '' OR i.invoiceid_src IS NULL, i.invoiceid, i.invoiceid_src), i.invoiceid");
    } else if($ui_no != '') {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE ii.paid IN ('Waiting on Insurer','No') AND ii.invoiceid = i.invoiceid AND ii.ui_invoiceid='$ui_no' AND ii.ui_invoiceid IS NOT NULL ORDER BY IF(i.invoiceid_src = '' OR i.invoiceid_src IS NULL, i.invoiceid, i.invoiceid_src), i.invoiceid");
    } else if($ui_total > 0) {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE ii.paid IN ('Waiting on Insurer','No') AND ii.invoiceid = i.invoiceid AND ii.ui_invoiceid IN (SELECT `ui_invoiceid` FROM `invoice_insurer` GROUP BY `ui_invoiceid` HAVING SUM(`insurer_price`)=$ui_total) AND ii.ui_invoiceid IS NOT NULL ORDER BY IF(i.invoiceid_src = '' OR i.invoiceid_src IS NULL, i.invoiceid, i.invoiceid_src), i.invoiceid");
    } else {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE ii.invoiceid = i.invoiceid AND ii.paid IN ('Waiting on Insurer','No') AND ii.ui_invoiceid IS NOT NULL ORDER BY IF(i.invoiceid_src = '' OR i.invoiceid_src IS NULL, i.invoiceid, i.invoiceid_src), i.invoiceid");
    }

    $amt_to_bill = 0;
    while($row_report = mysqli_fetch_array($report_service)) {
        $insurer_price = $row_report['insurer_price'];
        $invoiceid = $row_report['invoiceid'];
        $patientid = get_all_from_invoice($dbc, $invoiceid, 'patientid');
        $insurerid = rtrim($row_report['insurerid'],',');

        $each_insurance_payment = explode('#*#', $insurance_payment);
        $report_data .= '<tr nobr="true">';
        $report_data .= '<td>#'.$row_report['ui_invoiceid'].'</td>';
        $report_data .= '<td>#'.$invoiceid.' : '.get_contact($dbc, $patientid).($row['invoiceid_src'] > 0 ? '<br />'.$row['invoice_type'].' for Invoice #'.$row['invoiceid_src'] : '').'</td>';
        $report_data .= '<td>'.$row_report['invoice_date'].'</td>';
        $report_data .= '<td>'.$row_report['service_date'].'</td>';
        $report_data .= '<td>'.get_all_form_contact($dbc, $insurerid, 'name').'</td>';
        $report_data .= '<td>'.$insurer_price.'</td>';

        $report_data .= '</tr>';
        $amt_to_bill += $insurer_price;
    }
    $report_data .= '<tr nobr="true">';
    $report_data .= '<td>Total</td><td></td><td></td><td></td><td></td><td>'.number_format($amt_to_bill, 2).'</td>';
    $report_data .= "</tr>";
    $report_data .= '</table><br>';

    return $report_data;
}
?>