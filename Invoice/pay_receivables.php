<?php include_once('../include.php');
$purchaser_config = explode(',',get_config($dbc, 'invoice_purchase_contact'));
define('PURCHASER', count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0]);
?>
<form class="form-horizontal col-sm-12" method="POST" action="">
    <h2>Selected Invoices<a href="../blank_loading_page.php" class="pull-right small"><img src="../img/icons/cancel.png" class="inline-img"></a></h2>
    <div class="clearfix"></div>
    <table class="table table-bordered">
        <tr>
            <th>Invoice#</th>
            <th>Service Date</th>
            <th>Invoice Date</th>
            <th><?= PURCHASER ?></th>
            <th>Amount Receivable</th>
        </tr>
        <?php $bill_amt = 0;
        $contactid = $_GET['customer'];
        $show_statement = $contactid > 0;
        foreach(explode(',',$_GET['invoices']) as $invoiceid) {
            if($invoiceid > 0) {
                $invoice = $dbc->query("SELECT `i`.`invoice_date`, `i`.`service_date`, `i`.`payment_type`, `i`.`patientid`, SUM(`ii`.`patient_price`) `patient_price`, GROUP_CONCAT(`ii`.`invoicepatientid`) `invoicepatientid` FROM invoice_patient ii, invoice i WHERE ii.invoiceid = i.invoiceid AND i.invoiceid='$invoiceid' AND (IFNULL(ii.`paid`,'') IN ('On Account','','No') OR ii.`paid` LIKE 'Net %')")->fetch_assoc();
                $payment_type = implode(', ',array_filter(array_unique(explode('#*#',$row_report['payment_type'])))); ?>
                <tr nobr="true">
                    <td>#<?= $invoiceid ?></td>
                    <td><?= $invoice['invoice_date'] ?></td>
                    <td><?= $invoice['service_date'] ?></td>
                    <td><?= get_contact($dbc, $invoice['patientid']) ?></td>
                    <td><?= $invoice['patient_price'] ?></td>
                    <input type="hidden" name="invoicepatientid[]" value="<?= $invoice['invoicepatientid'] ?>">
                </tr>
                <?php $bill_amt += $invoice['patient_price'];
                if($invoice['patientid'] > 0 && $invoice['patientid'] != $contactid && !($contactid > 0)) {
                    $contactid = $invoice['patientid'];
                    $show_statement = true;
                } else if($invoice['patientid'] > 0 && $contactid > 0 && $invoice['patientid'] != $contactid) {
                    $show_statement = false;
                }
            }
        } ?>
        <tr nobr="true">
            <td>Total</td><td></td><td></td><td></td><td><?= number_format($bill_amt, 2) ?></td>
        </tr>
    </table>
    <span class="pull-right">Pay By
      <select name="payment_type" data-placeholder="Select Payment Method..." class="chosen-select-deselect form-control"><option />
        <?php foreach(explode(',',get_config($dbc, 'invoice_payment_types')) as $available_pay_method) { ?>
            <option value='<?= $available_pay_method ?>'><?= $available_pay_method ?></option>
        <?php } ?>
      </select>
    </span>
    <div class="clearfix"></div>
    <button type="submit" name="submit_patient" value="pay_now" class="pull-right btn brand-btn">Complete Payment</button>
    <div class="clearfix"></div>
    <?php if($show_statement) {
        $_GET['edit'] = $contactid;
        $hide_filter_options = true;
        $field_option = 'Account Statement';
        include('../Contacts/edit_tile_data.php');
    } ?>
</form>