<?php include_once('../include.php');
checkAuthorised(FOLDER_NAME == 'posadvanced' ? 'posadvanced' : 'check_out');
$security = get_security($dbc, (FOLDER_NAME == 'posadvanced' ? 'posadvanced' : 'check_out'));

if (!empty($_GET['type']) && $_GET['invoiceid'] > 0) {
    mysqli_query($dbc, "UPDATE `invoice` SET `type` = '".$_GET['type']."' WHERE `invoiceid` = '".$_GET['invoiceid']."'");
}
$invoice_type = empty($_GET['type']) ? '' : filter_var($_GET['type'],FILTER_SANITIZE_STRING);
$invoice_mode = empty($_GET['inv_mode']) ? '' : filter_var($_GET['inv_mode'],FILTER_SANITIZE_STRING);
$insurer_row_id = 0;
$paid = 'Yes';
$app_type = '';
$type = '';
$invoiceid = 0;
$service_date = date('Y-m-d');
$purchaser_config = explode(',',get_config($dbc, 'invoice_purchase_contact'));
$payer_config = explode(',',get_config($dbc, 'invoice_payer_contact'));

if(!empty($_GET['contactid'])) {
    $account_balance = get_all_form_contact($dbc, $_GET['contactid'], 'amount_credit');
    $delivery_address = get_ship_address($dbc, $_GET['contactid']);
}
if(!empty($_GET['invoiceid'])) {
    $invoiceid = $_GET['invoiceid'];
    $get_invoice = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM invoice WHERE invoiceid='$invoiceid'"));
    $invoice_type = $get_invoice['type'];

    $_GET['contactid'] = $get_invoice['patientid'];
    $patient_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='{$get_invoice['patientid']}'"));
    $billable = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `billable_dollars` FROM contacts_cost WHERE contactid = '{$get_invoice['patientid']}'"))['billable_dollars'];
    $billed = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`final_price`) `total` FROM invoice WHERE deleted=0 AND patientid = '{$get_invoice['patientid']}' ORDER BY invoiceid"))['total'];
    $patient = (($patient_info['category'] == 'Business' || $patient_info['category'] == 'Insurer') && $patient_info['name'] != '' ? decryptIt($patient_info['name']) : decryptIt($patient_info['first_name']).' '.decryptIt($patient_info['last_name'])).($billable > 0 ? "<br />Billable: $".$billed." of $".$billable : '');
    $staff = get_contact($dbc, $get_invoice['therapistsid']);
    $account_balance = get_all_form_contact($dbc, $get_invoice['patientid'], 'amount_credit');
    $pricing = $get_invoice['pricing'];
    $delivery_address = get_ship_address($dbc, $_GET['contactid']);

    $bookingid = $get_invoice['bookingid'];
    $injuryid = $get_invoice['injuryid'];
    $promotionid = $get_invoice['promotionid'];
    $invoice_date = $get_invoice['invoice_date'];
    $service_date = $get_invoice['service_date'];
    if($bookingid != 0) {
        $service_date = explode(' ', get_patient_from_booking($dbc, $bookingid, 'appoint_date'))[0];
    }

    $type = get_patient_from_booking($dbc, $bookingid, 'type');
    $app_type = get_type_from_booking($dbc, $type);

    $total_injury = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_injury FROM booking WHERE injuryid='$injuryid' AND (follow_up_call_status = 'Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status = 'Paid' OR follow_up_call_status = 'Invoiced')"));
    $treatment_plan = get_all_from_injury($dbc, $injuryid, 'treatment_plan');
    $final_treatment_done = ($treatment_plan != '' ? ' : '.($total_injury['total_injury']).'/'.$treatment_plan : '');
    $injury = get_all_from_injury($dbc, $injuryid, 'injury_type').' : '.get_all_from_injury($dbc, $injuryid, 'injury_name').' : '.get_all_from_injury($dbc, $injuryid, 'injury_date').$final_treatment_done;
    $injury_type = get_all_from_injury($dbc, $injuryid, 'injury_type');
    $mva_claim_price = get_all_from_injury($dbc, $injuryid, 'mva_claim_price');

    $serviceid =$get_invoice['serviceid'];
    $service_ticketid =$get_invoice['service_ticketid'];
    $fee =$get_invoice['fee'];
    $admin_fee =$get_invoice['admin_fee'];
    $service_ins = $get_invoice['service_insurer'];
    $inventoryid =$get_invoice['inventoryid'];
    $sell_price =$get_invoice['sell_price'];
    $invtype =$get_invoice['invtype'];
    $quantity =$get_invoice['quantity'];
    $inv_ins = $get_invoice['inventory_insurer'];
    $packageid =$get_invoice['packageid'];
    $package_cost =$get_invoice['package_cost'];
    $package_ins = $get_invoice['package_insurer'];
    $misc_items =$get_invoice['misc_item'];
    $misc_ticketid =$get_invoice['misc_ticketid'];
    $misc_prices =$get_invoice['misc_price'];
    $misc_qtys =$get_invoice['misc_qty'];
    $mis_ins = $get_invoice['misc_insurer'];

    $discount_value = $get_invoice['discount'];
    $assembly = $get_invoice['assembly'];
    $customer_billing_status = $get_invoice['customer_billing_status'];
    $delivery = $get_invoice['delivery'];
    $delivery_address = $get_invoice['delivery_address'];
    $delivery_type = $get_invoice['delivery_type'];
    $contractorid = $get_invoice['contractorid'];
    $ship_date = $get_invoice['ship_date'];

    $total_price =$get_invoice['total_price'];
    $final_price =$get_invoice['final_price'];
    $insurerid = $get_invoice['insurerid'];
    $insurance_payment = $get_invoice['insurance_payment'];
    $payment_type = $get_invoice['payment_type'];
    $paid = $get_invoice['paid'];
    $patient_paid_info = explode('#*#', $get_invoice['payment_type']);
    $patient_paid_type = explode(',', $patient_paid_info[0]);
    $patient_paid_amt = explode(',', $patient_paid_info[1]);
    $insurer_paid_who = $get_invoice['insurerid'];
    $insurer_paid_amt = $get_invoice['insurance_payment'];
    $gratuity = $get_invoice['gratuity'];

    // Prior Discount Percentage
    $discount_percent = 1;
    if($_GET['inv_mode'] == 'adjust') {
        $discount_percent = 1 - ($discount_value / $total_price);
    }

    $adj_result = mysqli_query($dbc, "SELECT * FROM `invoice` WHERE `invoiceid_src`='$invoiceid'");
    while($invoice_adj = mysqli_fetch_array($adj_result)) {
        $serviceid .= $invoice_adj['serviceid'];
        $fee .= $invoice_adj['fee'];
        $admin_fee .= $invoice_adj['admin_fee'];
        $service_ins .= $invoice_adj['service_insurer'];
        $inventoryid .= $invoice_adj['inventoryid'];
        $sell_price .= $invoice_adj['sell_price'];
        $invtype .= $invoice_adj['invtype'];
        $quantity .= $invoice_adj['quantity'];
        $inv_ins .= $invoice_adj['inventory_insurer'];
        $packageid .= $invoice_adj['packageid'];
        $package_cost .= $invoice_adj['package_cost'];
        $package_ins .= $invoice_adj['package_insurer'];
        $misc_items .= $invoice_adj['misc_item'];
        $misc_prices .= $invoice_adj['misc_price'];
        $misc_qtys .= $invoice_adj['misc_qty'];
        $misc_ins .= $invoice_adj['misc_insurer'];

        $patient_paid_info = explode('#*#', $invoice_adj['payment_type']);
        $patient_paid_type = array_merge($patient_paid_type,explode(',', $patient_paid_info[0]));
        $patient_paid_amt = array_merge($patient_paid_amt,explode(',', $patient_paid_info[1]));
        $insurer_paid_who .= ','.$invoice_adj['insurerid'];
        $insurer_paid_amt .= ','.$invoice_adj['insurance_payment'];
    }
    $insurer_paid_who = explode(',',$insurer_paid_who);
    $insurer_paid_amt = explode(',',$insurer_paid_amt);
}

$field_config = explode(',',get_config($dbc, 'invoice_fields'));
if(!empty($invoice_type)) {
    $field_config = explode(',',get_config($dbc, 'invoice_fields_'.$invoice_type));
}
$ux_options = explode(',',get_config($dbc, FOLDER_NAME.'_ux'));

if(!IFRAME_PAGE) { ?>
    <script>
    var gapSpace = 0;
    $(document).ready(function() {
        $(window).resize(function() {
            $('.main-screen').css('padding-bottom',0);
            if($('.tile-sidebar').is(':visible')) {
                var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.tile-sidebar:visible').offset().top;
                if(available_height > 200) {
                    $('.main-screen .main-screen').outerHeight(available_height - gapSpace).css('overflow-y','auto');
                    $('.tile-sidebar').outerHeight(available_height - gapSpace).css('overflow-y','auto');
                    $('.scalable').outerHeight(available_height - gapSpace).css('overflow-y','auto');
                }
            }
        }).resize();
    });
    var windowSize = null;
    function setWindowSize() {
        clearInterval(windowSize);
        gapSpace = 15;
        $(window).resize();
        windowSize = setInterval(function() { $(window).resize(); }, 50);
        setTimeout(function() {
            gapSpace = 0;
            $(window).resize();
            clearInterval(windowSize);
        }, 500);
    }
    </script>
<?php } ?>
<script src="../Invoice/invoice.js"></script>