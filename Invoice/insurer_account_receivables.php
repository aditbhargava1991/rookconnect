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
    $paid_date = $_POST['paid_date'];
    $date_deposit = $_POST['date_deposit'];
    $paid_type = $_POST['paid_type'];
    $insurer_price = 0;
    //$paid_date = date('Y-m-d');

    foreach ($_POST['invoiceinsurerid'] as $id => $value) {
		$query_update_in = "UPDATE `invoice_insurer` SET `paid` = 'Yes', `deposit_number` = '$deposit_number', `paid_date` = '$paid_date', `date_deposit` = '$date_deposit', `paid_type` = '$paid_type' WHERE `invoiceinsurerid` = '$value'";
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

    echo '<script type="text/javascript"> alert("Invoice Successfully Paid"); window.location.replace("insurer_account_receivables.php?p1='.$starttimepdf.'&p2='.$endtimepdf.'&p3='.$insurerpdf.'&p5='.$invoice_nopdf.'&p6='.$ui_nopdf.'"); </script>';
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
			alert("Invoice moved to Collection");
			location.reload();
		}
	});
}
function view_third_party_ar()
{
    $('.view_third_party_ar').toggleClass('hidden');
}
</script>

<?php
$payer_config = explode(',',get_config($dbc, 'invoice_payer_contact'));
define('PAYER_LABEL', count($payer_config) > 1 ? 'Third Party' : $payer_config[0]); ?>

<div class="standard-body-title hide-titles-mob">
    <h3 class="pull-left"><?= PAYER_LABEL ?> Accounts Receivable</h3>
    <div class="pull-right">
        <img src="../img/icons/ROOK-3dot-icon.png" class="no-toggle cursor-hand offset-top-15 double-gap-right" title="" width="25" data-original-title="Show/Hide Business Accounts Receivable" onclick="view_third_party_ar()"> </div>
    <div class="clearfix"></div>
</div>

<div class="standard-body-content padded-desktop ">
    <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal view_third_party_ar hidden" role="form">

        <div class="notice double-gap-bottom popover-examples">
        <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
        <div class="col-sm-11"><span class="notice-name">NOTE:</span>
        Displays <?= PAYER_LABEL ?> specific receivables within the selected dates.</div>
        <div class="clearfix"></div>
        </div>

        <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
        <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

        <?php
        if(!empty($_GET['p1'])) {
            $starttime = $_GET['p1'];
            $endtime = $_GET['p2'];
            $insurer = $_GET['p3'];
            $invoice_no = $_GET['p5'];
            $ui_no = $_GET['p6'];
        }
        if (isset($_POST['search_email_submit'])) {
            $starttime = $_POST['starttime'];
            $endtime = $_POST['endtime'];
            $insurer = $_POST['insurer'];
            $invoice_no = $_POST['invoice_no'];
            $ui_no = $_POST['ui_no'];
        }
        if (isset($_POST['search_email_all'])) {
            $starttime = date('Y-m-d');
            $endtime = date('Y-m-d');
            $insurer = '';
            $invoice_no = '';
            $ui_no = '';
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
            $insurer = $_GET['insurerid'];
        }
        ?>
        <br />

        <div class="form-group double-gap-bottom">
            <div class="col-xs-12">
                <div class="col-sm-6 col-xs-12">
                    <div class="col-sm-4"><label class="control-label"><?= PAYER_LABEL ?>:</label></div>
                    <div class="col-sm-8">
                        <select data-placeholder="Choose <?= PAYER_LABEL ?>..." name="insurer" class="chosen-select-deselect form-control" width="380">
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
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="col-sm-4"><label class="control-label">Invoice #:</label></div>
                    <div class="col-sm-8"><input name="invoice_no" type="text" class="form-control1 form-control" value="<?php echo $invoice_no; ?>" /></div>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="col-sm-6 col-xs-12">
                    <div class="col-sm-4"><label class="control-label">From:</label></div>
                    <div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>" /></div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="col-sm-4"><label class="control-label">To:</label></div>
                    <div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>" /></div>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="col-sm-6 col-xs-12">
                    <div class="col-sm-4"><label class="control-label">UI #:</label></div>
                    <div class="col-sm-8"><input name="ui_no" type="text" class="form-control1 form-control" value="<?php echo $ui_no; ?>" /></div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="col-xs-12 text-right">
                <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
                <button type="submit" name="search_email_all" value="Search" class="btn brand-btn mobile-block">Display Default</button>
            </div>
            <div class="clearfix"></div>
        </div>

        <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
        <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
        <input type="hidden" name="insurerpdf" value="<?php echo $insurer; ?>">
        <input type="hidden" name="invoice_nopdf" value="<?php echo $invoice_no; ?>">
        <input type="hidden" name="ui_nopdf" value="<?php echo $ui_no; ?>">

        <!-- <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button> -->

        </form>
        <form id="form2" name="form2" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

        <?php
            echo report_receivables($dbc, $starttime, $endtime, '', '', '', $insurer, $invoice_no, $ui_no);
        ?>

    </form>
</div>

<?php
function report_receivables($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $insurer, $invoice_no, $ui_no) {
    $report_data .= '<span>
    Payment Type &nbsp;<select name="paid_type" required class="form-control1" width="380">
        <option value="">Please Select</option>
        <option value="Transfer">Transfer</option>
        <option value="EFT">EFT</option>
        <option value="Cheque">Cheque</option>
    </select> &nbsp;
    Number &nbsp;<input type="text" class="" required name="deposit_number">&nbsp;&nbsp;';
    $report_data .= '&nbsp;Date Deposited&nbsp;<input type="text" required class="datepicker" name="date_deposit">&nbsp;Paid Date&nbsp;<input type="text" required class="datepicker" name="paid_date"></span>';

    $report_data .= '<div id="no-more-tables">';
    $report_data .= '<br /><br /><table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr class="hidden-xs hidden-sm" style="'.$table_row_style.'">
    <th>Invoice#</th>
    <th>U'.substr(PAYER_LABEL,0,1).'#</th>
    <th>Service Date</th>
    <th>Invoice Date</th>
    <th>'.PAYER_LABEL.'</th>
    <th>Amount Receivable</th>
    <th>Collection</th>
    <th>
		<span class="popover-examples list-inline" style="margin:0 0 0 0;"><a data-toggle="tooltip" data-placement="top" title="Here is where you apply the payment after you select all of the associated invoices and input the deposit/cheque #."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></span>
		<button type="submit" name="submit_pay" value="Submit" class="btn brand-btn">Pay</button>
	</th>
    </tr>';

    $query_add = '';
    if($insurer != '') {
        $query_add .= "AND ii.insurerid='$insurer'";
    }
    if($invoice_no != '') {
        $query_add .= " AND i.invoiceid='$invoice_no'";
    }
    if($ui_no != '') {
        $query_add .= " AND ii.ui_invoiceid='$ui_no'";
    }

    /*
    if($insurer != '') {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE (DATE(ii.invoice_date) >= '".$starttime."' AND DATE(ii.invoice_date) <= '".$endtime."') AND ii.insurerid='$insurer' AND ii.invoiceid = i.invoiceid AND ii.paid='Waiting on Insurer' ORDER BY ii.invoiceid");
    } else if($invoice_no != '') {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE (DATE(ii.invoice_date) >= '".$starttime."' AND DATE(ii.invoice_date) <= '".$endtime."') AND ii.paid IN ('Waiting on Insurer','No') AND ii.invoiceid = i.invoiceid AND i.invoiceid='$invoice_no' ORDER BY ii.invoiceid");
    } else if($ui_no != '') {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE (DATE(ii.invoice_date) >= '".$starttime."' AND DATE(ii.invoice_date) <= '".$endtime."') AND ii.paid IN ('Waiting on Insurer','No') AND ii.invoiceid = i.invoiceid AND ii.ui_invoiceid='$ui_no' ORDER BY ii.invoiceid");
    } else {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE (DATE(ii.invoice_date) >= '".$starttime."' AND DATE(ii.invoice_date) <= '".$endtime."') AND ii.invoiceid = i.invoiceid AND ii.paid IN ('Waiting on Insurer','No') ORDER BY ii.invoiceid");
    }
    */

    $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date, i.invoiceid_src, i.invoice_type FROM invoice_insurer ii, invoice i WHERE (DATE(ii.invoice_date) >= '".$starttime."' AND DATE(ii.invoice_date) <= '".$endtime."') AND ii.invoiceid = i.invoiceid ".$query_add." AND ii.paid != 'Yes' ORDER BY IF(i.invoiceid_src = '' OR i.invoiceid_src IS NULL, i.invoiceid, i.invoiceid_src), i.invoiceid");

    $amt_to_bill = 0;
    while($row_report = mysqli_fetch_array($report_service)) {
        $insurer_price = $row_report['insurer_price'];
        $invoiceid = $row_report['invoiceid'];
        $patientid = get_all_from_invoice($dbc, $invoiceid, 'patientid');
        $insurerid = rtrim($row_report['insurerid'],',');

        $each_insurance_payment = explode('#*#', $insurance_payment);
        $report_data .= '<tr nobr="true">';
        $report_data .= '<td data-title="Invoice#">#'.$invoiceid.' : '.get_contact($dbc, $patientid).($row_report['invoiceid_src'] > 0 ? ' ('.$row_report['invoice_type'].' for #'.$row_report['invoiceid_src'].')' : '').'</td>';

        $a = new \DateTime($row_report['invoice_date']);
        $b = new \DateTime;
        $total_days = $a->diff($b)->days;

        $report_data .= '<td data-title="U'.substr(PAYER_LABEL,0,1).'#">'.$row_report['ui_invoiceid'].'</td>';
        $report_data .= '<td data-title="Service Date">'.$row_report['service_date'].'</td>';
        $report_data .= '<td data-title="Invoice Date">'.$row_report['invoice_date'].'</td>';
        $report_data .= '<td data-title="'.PAYER_LABEL.'">'.get_all_form_contact($dbc, $insurerid, 'name').'</td>';
        $report_data .= '<td data-title="Amount Receivable">'.$insurer_price.'</td>';

        $coll_checked = '';
        if($row_report['collection'] == 1) {
            $coll_checked = ' checked disabled';
        }
        $report_data .=  '<td data-title="Collection"><input type="checkbox" '. $coll_checked.' onchange="waiting_on_collection(this)" value="'.$row_report['invoiceinsurerid'].'"></td>';

        $report_data .= '<td data-title=""><input type="checkbox" class="invoice" name="invoiceinsurerid[]" value="'.$row_report['invoiceinsurerid'].'" ></td>';

        $report_data .= '</tr>';
        $amt_to_bill += $insurer_price;
    }
    $report_data .= '<tr nobr="true">';
    $report_data .= '<td data-title="Total">Total</td><td></td><td></td><td></td><td></td><td>'.number_format($amt_to_bill, 2).'</td><td></td><td></td>';
    $report_data .= "</tr>";
    $report_data .= '</table><br>';
    $report_data .= '</div>';

    if(!empty($_GET['from'])) {
        if($_GET['report'] == 'ar_aging') {
            $report_data .= '<div class="pad-left gap-top double-gap-bottom"><a href="../Reports/report_ar_aging_summary.php?type=ar" class="btn config-btn">Back to Receivables</a></div>';
        } else {
            $report_data .= '<div class="pad-left gap-top double-gap-bottom"><a href="../Reports/report_receivables_summary.php?type=ar" class="btn config-btn">Back to Receivables</a></div>';
        }
    }

    return $report_data;
}
?>