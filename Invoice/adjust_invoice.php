<?php
/*
Add Invoice
*/
include ('../include.php');
if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
} else {
    checkAuthorised('check_out');
}
error_reporting(0);

if (isset($_POST['submit_btn'])) {
    include_once('../tcpdf/tcpdf.php');
	$invoice_mode = 'Adjustment';
	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}
    include('add_update_invoice.php');
	$type = implode(',', $_POST['payment_type']);

    if($bookingid != 0) {
        $appoint_date = get_patient_from_booking($dbc, $bookingid, 'appoint_date');
        $service_date = explode(' ', $appoint_date);
        $final_service_date = $service_date[0];
    } else {
        $final_service_date = $today_date;
    }

    $ins_pay = 0;
    for($i=0; $i<count($_POST['insurerid']); $i++) {
        $ins_pay += $_POST['insurance_payment'][$i];
    }

    $get_invoice = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM invoice WHERE invoiceid='$invoiceid'"));
    $patientid = $get_invoice['patientid'];
    $therapistsid = $get_invoice['therapistsid'];
    $injuryid = $get_invoice['injuryid'];

    $patients = get_contact($dbc, $patientid);
    $staff = get_contact($dbc, $therapistsid);

    include('add_update_invoice_inventory.php');

    if($_POST['next_appointment'] == 'Yes') {
        include('invoice_booking.php');
    }

	// PDF

    if($insurerid != '') {
        include ('insurer_invoice_pdf.php');
    }

    if($_POST['survey'] != '') {
        include ('send_survey.php');
    }

    if($_POST['follow_up_assessment_email'] != '') {
        include ('send_follow_up_email.php');
    }

    $invoicefrom = $_POST['invoicefrom'];
    $search_user = $_POST['search_user'];
    $search_invoice = $_POST['search_invoice'];

    if($invoicefrom == 'report') {
        $invoicefrom_start = $_POST['invoicefrom_start'];
        $invoicefrom_end = $_POST['invoicefrom_end'];
        echo '<script type="text/javascript"> alert("Invoice Updated."); window.location.replace("../Reports/report_unassigned_invoices.php?type=Daily&start='.$invoicefrom_start.'&end='.$invoicefrom_end.'");
        </script>';
    } else if($invoicefrom == 'calendar') {
        echo '<script type="text/javascript"> window.top.close(); window.opener.location.reload(); </script>';
    } else {
        if($search_user != '') {
            echo '<script type="text/javascript"> alert("Invoice Updated."); window.location.replace("all_invoice.php?search_user='.$search_user.'");</script>';
        } else if($search_invoice != '') {
            echo '<script type="text/javascript"> alert("Invoice Updated."); window.location.replace("all_invoice.php?search_invoice='.$search_invoice.'");</script>';
        } else {
            echo '<script type="text/javascript"> alert("Invoice Generated."); window.location.replace("index.php?tab=today");
            window.open("download/invoice_'.$invoiceid.'.pdf", "fullscreen=yes");
            </script>';
        }
    }
} ?>
<script type="text/javascript" src="../Invoice/invoice.js"></script>
</head>

<body>
<?php
    include_once ('../navigation.php');
    $ux_options = explode(',',get_config($dbc, FOLDER_NAME.'_ux'));
?>
<div class="container">
    <div class="row">

        <h1 class="pull-left">Refund / Adjustments</h1>
        <?php if(config_visible_function($dbc, (FOLDER_NAME == 'posadvanced' ? 'posadvanced' : 'check_out')) == 1) {
            echo '<a href="field_config_invoice.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
        } ?>
        <div class="clearfix"></div>
		<?php include('tile_tabs.php'); ?>

		<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <div class="col-sm-5"></div><div class="col-sm-4" style="text-align:right;"><h3><label style="color: blue;">Adjustment <input type="checkbox" onchange="if(this.checked) { $('.adjust_block').show(); $('[name=paid]').first().change(); } else { $('.adjust_block').hide(); }"></label>
                <label style="color: red;">Refund <input type="checkbox" onchange="if(this.checked) { $('.return_block').show(); } else { $('.return_block').hide(); }"></label></h3></div>
            <div class="clearfix"></div>

            <?php $insurer_row_id = 0;
            $paid = '';
            $app_type = '';
            $type = '';
            $invoiceid = 0;
            $service_date = date('Y-m-d');
            $field_config = explode(',',get_config($dbc, 'invoice_fields'));
            $purchaser_config = explode(',',get_config($dbc, 'invoice_purchase_contact'));
            $payer_config = explode(',',get_config($dbc, 'invoice_payer_contact'));

            if(!empty($_GET['contactid'])) {
                $account_balance = get_all_form_contact($dbc, $_GET['contactid'], 'amount_credit');
                $delivery_address = get_ship_address($dbc, $_GET['contactid']);
            }
            if(!empty($_GET['from'])) {
                echo '<input type="hidden" name="invoicefrom" value="'.$_GET['from'].'" />';
                echo '<input type="hidden" name="invoicefrom_start" value="'.$_GET['report_from'].'" />';
                echo '<input type="hidden" name="invoicefrom_end" value="'.$_GET['report_to'].'" />';
            }
            if(!empty($_GET['search_user'])) {
                echo '<input type="hidden" name="search_user" value="'.$_GET['search_user'].'" />';
            }
            if(!empty($_GET['search_invoice'])) {
                echo '<input type="hidden" name="search_invoice" value="'.$_GET['search_invoice'].'" />';
            }
            if(!empty($_GET['invoiceid'])) {
                $invoiceid = $_GET['invoiceid'];
                $get_invoice = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM invoice WHERE invoiceid='$invoiceid'"));

                if($get_invoice['patientid'] > 0) {
                    $patient_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='{$get_invoice['patientid']}'"));
                    $patient = (($patient_info['category'] == 'Business' || $patient_info['category'] == 'Insurer') && $patient_info['name'] != '' ? decryptIt($patient_info['name']) : decryptIt($patient_info['first_name']).' '.decryptIt($patient_info['last_name']));
                } else {
                    $non_patient = mysqli_fetch_array(mysqli_query($dbc, "SELECT `first_name`, `last_name` FROM `invoice_nonpatient` WHERE `invoiceid`='$invoiceid'"));
                    $patient = $non_patient['first_name'].' '.$non_patient['last_name'];
                }
                $staff = get_contact($dbc, $get_invoice['therapistsid']);
                $account_balance = get_all_form_contact($dbc, $get_invoice['patientid'], 'amount_credit');
                $pricing = $get_invoice['pricing'];
                $delivery_address = get_ship_address($dbc, $_GET['contactid']);

                $bookingid = $get_invoice['bookingid'];
                $injuryid = $get_invoice['injuryid'];
                $promotionid = $get_invoice['promotionid'];
                $invoice_date = $get_invoice['invoice_date'];
                $service_date = $get_invoice['service_date'];

                $type = get_patient_from_booking($dbc, $bookingid, 'type');
                $app_type = get_type_from_booking($dbc, $type);

                $total_injury = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_injury FROM booking WHERE injuryid='$injuryid' AND (follow_up_call_status = 'Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status = 'Paid' OR follow_up_call_status = 'Invoiced')"));

                $treatment_plan = get_all_from_injury($dbc, $injuryid, 'treatment_plan');
                $final_treatment_done = '';
                $final_treatment_done = ' : '.($total_injury['total_injury']).'/'.$treatment_plan;

                $injury = get_all_from_injury($dbc, $injuryid, 'injury_type').' : '.get_all_from_injury($dbc, $injuryid, 'injury_name').' : '.get_all_from_injury($dbc, $injuryid, 'injury_date').$final_treatment_done;

                $injury_type = get_all_from_injury($dbc, $injuryid, 'injury_type');

                $treatment_plan = get_all_from_injury($dbc, $injuryid, 'treatment_plan');

                echo '<input type="hidden" id="invoiceid" name="invoiceid" value="'.$invoiceid.'" />';
                echo '<input type="hidden" id="patientid" name="patientid" value="'.$get_invoice['patientid'].'" />';
                echo '<input type="hidden" id="therapistsid" name="therapistsid" value="'.$get_invoice['therapistsid'].'" />';
                echo '<input type="hidden" id="injuryid" name="injuryid" value="'.$injuryid.'" />';

                $mva_claim_price = get_all_from_injury($dbc, $injuryid, 'mva_claim_price');
                echo '<input type="hidden" name="mva_claim_price" value="'.$mva_claim_price.'" />';
                echo '<input type="hidden" name="set_promotion" id="set_promotion" value="'.get_promotion($dbc, $promotionid, 'cost').'" />';

                $serviceid =$get_invoice['serviceid'];
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
                $misc_prices =$get_invoice['misc_price'];
                $misc_qtys =$get_invoice['misc_qty'];
                $mis_ins = $get_invoice['misc_insurer'];

                $delivery = $get_invoice['delivery'];
                $delivery_address = $get_invoice['delivery_address'];
                $delivery_type = $get_invoice['delivery_type'];
                $contractorid = $get_invoice['contractorid'];
                $ship_date = $get_invoice['ship_date'];

                $paid = $get_invoice['paid'];

                $patient_paid_info = explode('#*#', $get_invoice['payment_type']);
                $patient_paid_type = explode(',', $patient_paid_info[0]);
                $patient_paid_amt = explode(',', $patient_paid_info[1]);
                $insurer_paid_who = explode(',', $get_invoice['insurerid']);
                $insurer_paid_amt = explode(',', $get_invoice['insurance_payment']);

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
            } else {
                echo '<input type="hidden" name="set_promotion" id="set_promotion" />';
            }

            echo '<input type="hidden" id="paid_notpaid" name="paid_notpaid" value="'.$paid.'" />';

            ?>

            <div class="col-sm-3 preview_div">
                <h3>Details</h3>
                <h4 <?= (in_array('invoice_date',$field_config) ? '' : 'style="display:none;"') ?>>Invoice Date: <label class="detail_invoice_date pull-right"><?= date('Y-m-d') ?></label></h4>
                <h4 <?= (in_array('customer',$field_config) ? '' : 'style="display:none;"') ?>><?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?>: <label class="detail_patient_name pull-right"><?= (empty($_GET['invoiceid']) ? get_contact($dbc, $_GET['contactid']) : $patient) ?></label></h4>
                <h4 <?= (in_array('injury',$field_config) ? '' : 'style="display:none;"') ?>>Injury: <label class="detail_patient_injury pull-right"><?= (empty($_GET['invoiceid']) ? '' : $injury) ?></label></h4>
                <h4 <?= (in_array('treatment',$field_config) ? '' : 'style="display:none;"') ?>>Treatment Plan: <label class="detail_patient_treatment pull-right"><?= (empty($_GET['invoiceid']) ? '' : $treatment_plan) ?></label></h4>
                <h4 <?= (in_array('staff',$field_config) ? '' : 'style="display:none;"') ?>>Staff: <label class="detail_staff_name pull-right"><?= (empty($_GET['invoiceid']) ? '' : $staff) ?></label></h4>
                <h4 <?= (in_array('services',$field_config) ? '' : 'style="display:none;"') ?>>Services</h4>
                <div class="detail_service_list" <?= (in_array('services',$field_config) ? '' : 'style="display:none;"') ?>></div>
                <h4 <?= (in_array('inventory',$field_config) ? '' : 'style="display:none;"') ?>>Inventory</h4>
                <div class="detail_inventory_list" <?= (in_array('inventory',$field_config) ? '' : 'style="display:none;"') ?>></div>
                <h4 <?= (in_array('products',$field_config) ? '' : 'style="display:none;"') ?>>Products</h4>
                <div class="detail_products_list" <?= (in_array('products',$field_config) ? '' : 'style="display:none;"') ?>></div>
                <h4 <?= (in_array('packages',$field_config) ? '' : 'style="display:none;"') ?>>Packages</h4>
                <div class="detail_package_list" <?= (in_array('packages',$field_config) ? '' : 'style="display:none;"') ?>></div>
                <h4 <?= (in_array('misc_items',$field_config) ? '' : 'style="display:none;"') ?>>Miscellaneous Items</h4>
                <div class="detail_misc_list" <?= (in_array('misc_items',$field_config) ? '' : 'style="display:none;"') ?>></div>
                <h4>Sub-Total: <label class="detail_sub_total_amt pull-right">$0.00</label></h4>
                <h4 <?= (in_array('promo',$field_config) ? '' : 'style="display:none;"') ?>>Promotion: <label class="detail_promo_amt pull-right"><?= $promotionid > 0 ? '' : 'N/A' ?></label></h4>
                <h4 <?= (in_array('delivery',$field_config) ? '' : 'style="display:none;"') ?>>Delivery: <label class="detail_shipping_amt pull-right">$0.00</label></h4>
                <h4 <?= (in_array('delivery',$field_config) ? '' : 'style="display:none;"') ?>>Total before Tax: <label class="detail_mid_total_amt pull-right">$0.00</label></h4>
                <h4>GST: <label class="detail_gst_amt pull-right">$0.00</label></h4>
                <h4 <?= (in_array('tips',$field_config) ? '' : 'style="display:none;"') ?>>Gratuity: <label class="detail_gratuity_amt pull-right">$0.00</label></h4>
                <h4 style="display:none;">Credit to Account: <label class="detail_credit_balance pull-right">$0.00</label></h4>
                <h4>Total: <label class="detail_total_amt pull-right">$0.00</label></h4>
                <h4 style="display:none;">Refund Amount: <label class="detail_refund_amt pull-right">$0.00</label></h4>
                <h4 style="display:none;">Adjustment Amount: <label class="detail_adjust_amt pull-right">$0.00</label></h4>
                <h4 style="display:none;"><?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> Portion: <label class="detail_insurer_amt pull-right">$0.00</label></h4>
                <h4 style="display:none;"><?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?> Portion: <label class="detail_patient_amt pull-right">$0.00</label></h4>
            </div>

            <div class="form-group" <?= (in_array('invoice_date',$field_config) ? '' : 'style="display:none;"') ?>>
                <label for="site_name" class="col-sm-2 control-label">Invoice Date:</label>
                <div class="col-sm-7">
                    <input type="text" readonly value="<?= date('Y-m-d'); ?>" class="form-control">
                </div>
            </div>

              <input type="hidden" name="invoice_mode" value="Adjustment">
              <div class="form-group" <?= (in_array('customer',$field_config) ? '' : 'style="display:none;"') ?>>
                <label for="site_name" class="col-sm-2 control-label"><?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?>:</label>
                <div class="col-sm-7 control-label" style="text-align:left;">
                    <?php echo $patient; ?>
                </div>
              </div>

              <div class="form-group" <?= (in_array('injury',$field_config) ? '' : ' style="display:none;"') ?>>
                <label for="site_name" class="col-sm-2 control-label">Injury:</label>
                <div class="col-sm-7 control-label" style="text-align:left;">
                    <?php echo $injury; ?>
                </div>
              </div>

              <div class="form-group <?= (in_array('injury',$field_config) ? 'adjust_block' : '" style="display:none;') ?>">
                <label for="site_name" class="col-sm-2 control-label">Adjusted Injury:</label>
                <div class="col-sm-7">
                    <select id="injuryid" data-placeholder="Select an Injury..." name="injuryid" class="chosen-select-deselect form-control" width="380">
                        <option value=""></option>
                        <?php $pid = $_GET['contactid'];
                        $query = mysqli_query($dbc,"SELECT contactid, injuryid, injury_name, injury_date, injury_type, treatment_plan FROM patient_injury WHERE contactid='$pid' AND discharge_date IS NULL AND deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            $total_injury = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_injury FROM booking WHERE injuryid='".$row['injuryid']."'"));

                            $treatment_plan = get_all_from_injury($dbc, $row['injuryid'], 'treatment_plan');
                            $final_treatment_done = '';
                            if($treatment_plan != '') {
                                $final_treatment_done = ' : '.($total_injury['total_injury']).'/'.$treatment_plan;
                            }

                            echo "<option ".($injuryid == $row['injuryid'] ? 'selected' : '')." value='". $row['injuryid']."'>".$row['injury_type'].' : '.$row['injury_name']. ' : '.$row['injury_date'].$final_treatment_done.'</option>';
                        } ?>
                    </select>
                </div>
              </div>

               <div class="form-group treatment_plan" <?= (in_array('treatment',$field_config) ? '' : 'style="display:none;"') ?>>
                <label for="site_name" class="col-sm-2 control-label">Treatment Plan:</label>
                <div class="col-sm-7 control-label" style="text-align:left;">
                  <?= $treatment_plan ?>
                </div>
              </div>

               <div class="form-group treatment_plan <?= (in_array('treatment',$field_config) ? 'adjust_block' : '" style="display:none;') ?>">
                <label for="site_name" class="col-sm-2 control-label">Adjusted Treatment Plan:</label>
                <div class="col-sm-7">
                  <select name="treatment_plan" data-placeholder="Select a Plan..." class="chosen-select-deselect form-control" width="380">
                        <option value=''></option>
                        <option <?php if ($treatment_plan == "3") { echo " selected"; } ?> value = '3'>3</option>
                        <option <?php if ($treatment_plan == "4") { echo " selected"; } ?> value = '4'>4</option>
                        <option <?php if ($treatment_plan == "5") { echo " selected"; } ?> value = '5'>5</option>
                        <option <?php if ($treatment_plan == "6") { echo " selected"; } ?> value = '6'>6</option>
                        <option <?php if ($treatment_plan == "7") { echo " selected"; } ?> value = '7'>7</option>
                        <option <?php if ($treatment_plan == "12") { echo " selected"; } ?>  value = '12'>12</option>
                        <option <?php if ($treatment_plan == "21") { echo " selected"; } ?> value = '21'>21</option>
                  </select>
                </div>
              </div>

              <div class="form-group" <?= (in_array('staff',$field_config) ? '' : 'style="display:none;"') ?>>
                <label for="site_name" class="col-sm-2 control-label">Staff:</label>
                <div class="col-sm-7 control-label" style="text-align:left;">
                    <?php echo $staff; ?>
                </div>
              </div>

              <div class="form-group <?= (in_array('staff',$field_config) ? 'adjust_block' : '" style="display:none;') ?>">
                <label for="site_name" class="col-sm-2 control-label">Adjusted Staff:</label>
                <div class="col-sm-7">
                    <select id="therapistsid" data-placeholder="Select Staff..." name="therapistsid" class="chosen-select-deselect form-control" width="380">
                        <option value=""></option>
                            <?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND (category_contact = 'Physical Therapist' OR category_contact = 'Massage Therapist' OR category_contact = 'Osteopathic Therapist')  AND deleted=0"),MYSQLI_ASSOC));
                            foreach($query as $row) {
                                echo "<option ".($row == $get_invoice['therapistsid'] ? 'selected' : '')." value='". $row."'>".get_contact($dbc, $row).'</option>';
                            } ?>
                    </select>
                </div>
              </div>

              <div class="form-group" <?= (in_array('appt_type',$field_config) ? '' : 'style="display:none;"') ?>>
                <label for="site_name" class="col-sm-2 control-label">Appointment Type:</label>
                <div class="col-sm-7 control-label" style="text-align:left;">
                    <?php echo $app_type; ?>
                </div>
              </div>

              <div class="form-group <?= (in_array('appt_type',$field_config) ? 'adjust_block' : '" style="display:none;') ?>">
                <label for="site_name" class="col-sm-2 control-label">Adjusted Appointment Type:</label>
                <div class="col-sm-7">
                    <select name="app_type" class="chosen-select-deselect"><option></option>
                        <?php $appointment_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `appointment_type` WHERE `deleted` = 0"),MYSQLI_ASSOC);
                        foreach ($appointment_types as $appointment_type) {
                            echo '<option '.($type == $appointment_type['id'] ? 'selected' : '').' value="'.$appointment_type['id'].'">'.$appointment_type['name'].'</option>';
                        } ?>
                    </select>
                </div>
              </div>

                  <div class="form-group" <?= (in_array('service_date',$field_config) ? '' : 'style="display:none;"') ?>>
                    <label for="site_name" class="col-sm-2 control-label">Service Date:</label>
                    <div class="col-sm-7 control-label" style="text-align:left;">
                        <?= $service_date ?>
                    </div>
                  </div>

                  <div class="form-group <?= (in_array('service_date',$field_config) ? 'adjust_block' : '" style="display:none;') ?>">
                    <label for="site_name" class="col-sm-2 control-label">Adjusted Service Date:</label>
                    <div class="col-sm-7">
                        <input type="text" name="service_date" class="form-control datepicker" value="<?= $service_date ?>">
                    </div>
                  </div>

                  <div class="form-group" <?= (in_array('pricing',$field_config) ? '' : 'style="display:none;"') ?>>
                    <label for="site_name" class="col-sm-2 control-label">Product Pricing:</label>
                    <div class="col-sm-7">
                        <?= ucwords(str_replace('_',' ',$pricing)) ?>
                    </div>
                  </div>

                  <div class="form-group <?= (in_array('pricing',$field_config) ? 'adjust_block' : '" style="display:none;') ?>">
                    <label for="site_name" class="col-sm-2 control-label">Adjusted Product Pricing:</label>
                    <div class="col-sm-7">
                        <select name="pricing" data-placeholder="Select Pricing" class="chosen-select-deselect"><option></option>
                            <?php if(in_array('price_client', $field_config)) { ?><option <?= ($pricing == 'client_price' ? 'selected' : '') ?> value="client_price">Client Price</option><?php } ?>
                            <?php if(in_array('price_admin', $field_config)) { ?><option <?= ($pricing == 'admin_price' ? 'selected' : '') ?> value="admin_price">Admin Price</option><?php } ?>
                            <?php if(in_array('price_commercial', $field_config)) { ?><option <?= ($pricing == 'commercial_price' ? 'selected' : '') ?> value="commercial_price">Commercial Price</option><?php } ?>
                            <?php if(in_array('price_wholesale', $field_config)) { ?><option <?= ($pricing == 'wholesale_price' ? 'selected' : '') ?> value="wholesale_price">Wholesale Price</option><?php } ?>
                            <?php if(in_array('price_retail', $field_config)) { ?><option <?= ($pricing == 'final_retail_price' || $pricing == '' ? 'selected' : '') ?> value="final_retail_price">Final Retail Price</option><?php } ?>
                            <?php if(in_array('price_preferred', $field_config)) { ?><option <?= ($pricing == 'preferred_price' ? 'selected' : '') ?> value="preferred_price">Preferred Price</option><?php } ?>
                            <?php if(in_array('price_po', $field_config)) { ?><option <?= ($pricing == 'purchase_order_price' ? 'selected' : '') ?> value="purchase_order_price">Purchase Order Price</option><?php } ?>
                            <?php if(in_array('price_sales', $field_config)) { ?><option <?= ($pricing == 'sales_order_price' ? 'selected' : '') ?> value="sales_order_price"><?= SALES_ORDER_NOUN ?> Price</option><?php } ?>
                            <?php if(in_array('price_web', $field_config)) { ?><option <?= ($pricing == 'web_price' ? 'selected' : '') ?> value="web_price">Web Price</option><?php } ?>
                        </select>
                    </div>
                  </div>

                <div class="form-group" <?= (in_array('pay_mode',$field_config) ? '' : 'style="display:none;"') ?>>
                    <label for="site_name" class="col-sm-2 control-label">Original Payment Method:</label>
                    <div class="col-sm-7 control-label" style="text-align:left;">
                        <?php switch($paid) {
                            case 'Yes':
                                echo "Patient Invoice : Patient is paying full amount on checkout";
                                break;
                            case 'Waiting on Insurer':
                                echo "Waiting on Insurer : Clinic is waiting on insurer to pay full amount";
                                break;
                            case 'No':
                                echo "Partially Paid : The invoice is being paid partially by patient and partially by insurer";
                                break;
                            case 'On Account':
                                echo "A/R On Account : Patient will pay invoice in future. Must choose Payment Type as Apply A/R to Account";
                                break;
                            case 'Credit On Account':
                                echo "Credit On Account : Patient is appyling credit to profile";
                                break;
                        } ?>
                    </div>
                </div>

                <div class="form-group <?= (in_array('pay_mode',$field_config) ? 'adjust_block' : '" style="display:none;') ?>">
                    <label for="site_name" class="col-sm-2 control-label">Adjustment Payment Method:</label>
                    <div class="col-sm-7">

                        <select data-placeholder="Select a Type..." name="paid" id="paid_status" class="chosen-select-deselect form-control" width="480">
                            <option value=""></option>
                            <option <?php if ($paid=='Yes') echo 'selected="selected"';?>  value="Yes">Patient Invoice : Patient is paying full amount on checkout.</option>
                            <option <?php if ($paid=='Waiting on Insurer') echo 'selected="selected"';?> value="Waiting on Insurer">Waiting on <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> : Clinic is waiting on <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> to pay full amount.</option>
                            <option <?php if ($paid=='No') echo 'selected="selected"';?>  value="No">Partially Paid : The invoice is being paid partially by patient and partially by <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?>.</option>
                            <option <?php if ($paid=='On Account') echo 'selected="selected"';?> value="On Account">A/R On Account : Patient will pay invoice in future. Must choose Payment Type as Apply A/R to Account.</option>
                            <option <?php if ($paid=='Credit On Account') echo 'selected="selected"';?> value="Credit On Account">Credit On Account : Patient is appyling credit to profile.</option>
                        </select>
                    </div>
                </div>

                <div class="form-group service_option" <?= (in_array('services',$field_config) ? '' : 'style="display:none;"') ?>>
                    <label for="additional_note" class="col-sm-2 control-label">Services:</label>
                    <div class="col-sm-7">
                        <div class="form-group clearfix hide-titles-mob">
                            <label class="col-sm-4">Category</label>
                            <label class="col-sm-4">Service Name</label>
                            <label class="col-sm-2">Fee</label>
                            <label class="col-sm-2 return_block">Refund</label>
                        </div>

                        <?php
                        if(!empty($_GET['invoiceid'])) {

                        if($serviceid != '') {
                            $each_serviceid = explode(',',$serviceid);
                            $each_fee = explode(',',$fee);

                            foreach($each_serviceid as $loop_check => $check_service) {
                                $matched = false;
                                $check_fee = $each_fee[$loop_check];
                                if($check_fee > 0 || $check_fee < 0) {
                                    foreach($each_serviceid as $valid_check => $valid_service) {
                                        $valid_fee = $each_fee[$valid_check];
                                        if(!$matched && $loop_check != $valid_check && $check_service == $valid_service && $check_fee * 1 == $valid_fee * -1) {
                                            unset($each_serviceid[$valid_check]);
                                            unset($each_fee[$valid_check]);
                                            unset($each_serviceid[$loop_check]);
                                            unset($each_fee[$loop_check]);
                                            $matched = true;
                                        }
                                    }
                                }
                            }
                            $each_serviceid = array_values($each_serviceid);
                            $each_fee = array_values($each_fee);

                            $total_count = mb_substr_count($serviceid,',');
                            $id_loop = 500;

                            for($client_loop=0; $client_loop<=$total_count; $client_loop++) {
                                if($each_serviceid[$client_loop] != '') {
                                    $serviceid = $each_serviceid[$client_loop];
                                    $fee = $each_fee[$client_loop];
                                    $service_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid`='$serviceid'"));
                                    ?>

                                <div class="form-group clearfix refundable">
                                    <div class="col-sm-4"><label class="show-on-mob">Service Category:</label>
                                        <?= $service_info['category'] ?>
                                    </div>

                                    <div class="col-sm-4"><label class="show-on-mob">Service Name:</label>
                                        <?= $service_info['heading'] ?><input type="hidden" name="init_serviceid[]" value="<?= $service_info['serviceid'] ?>"><input type="hidden" name="servicelabel" value="<?= $service_info['category'].': '.$service_info['heading'] ?>">
                                    </div>

                                    <div class="col-sm-2"><label class="show-on-mob">Total Fee:</label>
                                        <input name="fee[]" readonly id="<?php echo 'fee_'.$id_loop; ?>"  type="number" step="any" value="<?php echo $fee; ?>" class="form-control fee" />
                                        <input name="init_gst_exempt[]" id="<?php echo 'gstexempt_'.$id_loop; ?>"  type="hidden" value="<?php echo get_all_from_service($dbc, $serviceid, 'gst_exempt'); ?>" class="form-control gstexempt" />
                                        <input name="init_service_row_id[]" type="hidden" value="<?= $insurer_row_id ?>" class="insurer_row_id" />
                                    </div>

                                    <div class="col-sm-2 return_block">
                                        <label><input type="checkbox" name="servicerow_refund[]" value="<?= $insurer_row_id ?>" onchange="countTotalPrice()"> Refund</label>
                                    </div>

                                    <div class="col-sm-12">
                                        <?php foreach(explode('#*#',explode(',',$service_ins)[$client_loop]) as $line_insurer) {
                                            $line_insurer = explode(':',$line_insurer);
                                            if($line_insurer[1] != 0) { ?>
                                                <label class="col-sm-6">Payment by <?= get_client($dbc, $line_insurer[0]) ?>:</label>
                                                <label class="col-sm-6">$<?= number_format($line_insurer[1], 2) ?></label><input type="hidden" name="init_insurer_payment[]" value="<?= $line_insurer[1] ?>">
                                            <?php }
                                        } ?></div>
                                    <div class="col-sm-12 pay-div"></div>
                                </div>
                                    <?php
                                    $id_loop++;
                                    $insurer_row_id++;
                                }
                            }
                        }
                        ?>

                        <?php } ?>

                        <div class="additional_service form-group clearfix adjust_block">
                            <div class="col-sm-4"><label class="show-on-mob">Service Category:</label>
                                <select data-placeholder="Select a Category..." id="category_0" class="chosen-select-deselect form-control service_category_onchange" width="380">
                                    <option value=""></option>
                                    <?php $query = mysqli_query($dbc,"SELECT category, GROUP_CONCAT(DISTINCT(appointment_type)) appointment_type FROM services WHERE deleted=0 GROUP BY `category`");
                                    while($row = mysqli_fetch_array($query)) {
                                        echo "<option data-appt-type=',".$row['appointment_type'].",' value='". $row['category']."'>".$row['category'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-4"><label class="show-on-mob">Service Name:</label>
                                <select id="serviceid_0" data-placeholder="Select a Service..." name="serviceid[]" class="chosen-select-deselect form-control serviceid" width="380">
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="col-sm-2"><label class="show-on-mob">Total Fee:</label>
                                <input name="fee[]" readonly id="fee_0" type="number" step="any" value=0 class="form-control fee" />
                                <input name="gst_exempt[]" id="gstexempt_0"  type="hidden" value="0" class="form-control gstexempt" />
                                <input name="service_row_id[]" type="hidden" value="<?= $insurer_row_id++ ?>" class="insurer_row_id" />
                            </div>

                            <div class="col-sm-2">
                                <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_service_row(this);">
                                <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_service_row();">
                            </div>

                            <div class="col-sm-12 pay-div"></div>
                        </div>

                        <div id="add_here_new_service"></div>

                    </div>
                </div>

                <div class="form-group product_option" <?= (in_array('inventory',$field_config) ? '' : 'style="display:none;"') ?>>
                    <label for="additional_note" class="col-sm-2 control-label">Inventory:<?php echo (in_array('injury', $field_config) ? '<br>MVA Claim Price:' : '');
                    if(!empty($_GET['invoiceid'])) {
                        echo $mva_claim_price;
                    } else {
                        echo '<span class="mva_claim_price"></span>';
                    }

                    //Calculate Column Widths
                    $col1 = 1;
                    $col2 = 2;
                    $col3 = 2;
                    $col4 = 1;
                    $col5 = 1;
                    $col6 = 2;
                    $col7 = 2;
                    $col8 = 1;
                    if(in_array('inventory_cat',$field_config) && in_array('inventory_part',$field_config) && in_array('inventory_type',$field_config)) {
                        $col1 = $col2 = $col3 = 2;
                        $col4 = 1;
                        $col5 = 0;
                    } else if(in_array('inventory_cat',$field_config) && in_array('inventory_part',$field_config) && in_array('inventory_price',$field_config)) {
                        //$col1 = $col2 = $col3 = 2;
                        $col1 = 1;
                        $col2 = $col3 = 2;
                        $col5 = 1;
                        $col4 = 0;
                    } else if(in_array('inventory_cat',$field_config) && in_array('inventory_type',$field_config) && in_array('inventory_price',$field_config)) {
                        $col1 = $col3 = $col5 = 2;
                        $col4 = 1;
                        $col2 = 0;
                    } else if(in_array('inventory_part',$field_config) && in_array('inventory_type',$field_config) && in_array('inventory_price',$field_config)) {
                        $col2 = $col3 = $col5 = 2;
                        $col4 = 1;
                        $col1 = 0;
                    } else if(in_array('inventory_cat',$field_config) && in_array('inventory_part',$field_config)) {
                        $col1 = 3;
                        $col3 = $col2 = 2;
                        $col4 = $col5 = 0;
                    } else if(in_array('inventory_cat',$field_config) && in_array('inventory_type',$field_config)) {
                        $col1 = 3;
                        $col3 = $col4 = 2;
                        $col2 = $col5 = 0;
                    } else if(in_array('inventory_cat',$field_config) && in_array('inventory_price',$field_config)) {
                        //$col1 = 3;
                        $col3 = 3;
                        //$col3 = $col5 = 2;
                        $col5 = 2;
                        $col2 = $col4 = 0;
                    } else if(in_array('inventory_part',$field_config) && in_array('inventory_type',$field_config)) {
                        $col2 = 3;
                        $col3 = $col4 = 2;
                        $col1 = $col5 = 0;
                    } else if(in_array('inventory_part',$field_config) && in_array('inventory_price',$field_config)) {
                        $col2 = 3;
                        $col3 = $col5 = 2;
                        $col1 = $col4 = 0;
                    } else if(in_array('inventory_type',$field_config) && in_array('inventory_price',$field_config)) {
                        $col3 = 3;
                        $col4 = $col5 = 2;
                        $col1 = $col2 = 0;
                    } else if(in_array('inventory_cat',$field_config)) {
                        $col1 = $col3 = 4;
                        $col2 = $col4 = $col5 = 0;
                    } else if(in_array('inventory_part',$field_config)) {
                        $col2 = 4;
                        $col3 = 3;
                        $col1 = $col4 = $col5 = 0;
                    } else if(in_array('inventory_type',$field_config)) {
                        $col3 = 4;
                        $col4 = 3;
                        $col1 = $col2 = $col5 = 0;
                    } else if(in_array('inventory_price',$field_config)) {
                        $col3 = 4;
                        $col5 = 3;
                        $col1 = $col2 = $col4 = 0;
                    }
                    mysqli_set_charset($dbc, 'utf8');
                    $inventory_list = mysqli_fetch_all(mysqli_query($dbc,"SELECT `inventoryid`, `category`, `part_no`, `name` FROM inventory WHERE deleted=0 ORDER BY name"),MYSQLI_ASSOC);
                    ?></label>
                    <script>
                        var inv_list = <?= json_encode($inventory_list) ?>;
                    </script>
                    <div class="col-sm-7">
                        <div class="form-group clearfix hide-titles-mob">
                            <?php if(in_array('inventory_cat',$field_config)) { ?><label class="col-sm-<?= $col1 ?>">Category</label><?php } ?>
                            <?php if(in_array('inventory_part',$field_config)) { ?><label class="col-sm-<?= $col2 ?>">Part #</label><?php } ?>
                            <label class="col-sm-<?= $col3 ?>">Name</label>
                            <?php if(in_array('inventory_type',$field_config)) { ?><label class="col-sm-<?= $col4 ?>">Type</label><?php } ?>
                            <?php if(in_array('inventory_price',$field_config)) { ?><label class="col-sm-<?= $col5 ?>">Price</label><?php } ?>
                            <label class="col-sm-<?= $col6 ?>">Qty</label>
                            <label class="col-sm-<?= $col7 ?>">Total</label>
                            <label class="col-sm-2 return_block">Refund Qty<span class="popover-examples list-inline">
                                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Enter a negative quantity to refund that quantity."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span></label>
                        </div>

                        <?php if($inventoryid != '') {

                            $each_inventoryid = explode(',',$inventoryid);
                            $each_sell_price = explode(',',$sell_price);
                            $each_invtype = explode(',',$invtype);
                            $each_quantity = explode(',',$quantity);

                            foreach($each_inventoryid as $loop_check => $check_inventory) {
                                $check_qty = $each_quantity[$loop_check];
                                $check_sell = $each_sell_price[$loop_check];
                                $check_type = $each_invtype[$loop_check];
                                if($check_qty > 0 || $check_qty < 0) {
                                    foreach($each_inventoryid as $valid_check => $valid_inventory) {
                                        $valid_qty = $each_quantity[$valid_check];
                                        $valid_sell = $each_sell_price[$valid_check];
                                        $valid_type = $each_invtype[$valid_check];
                                        if(($valid_qty > 0 || $valid_qty < 0) && $loop_check != $valid_check && $check_inventory == $valid_inventory && $check_type == $valid_type) {
                                            $check_qty += $valid_qty;
                                            $check_sell += $valid_sell;
                                            $each_quantity[$loop_check] = $check_qty;
                                            $each_sell_price[$loop_check] = $check_sell;
                                            unset($each_quantity[$valid_check]);
                                            unset($each_invtype[$valid_check]);
                                            unset($each_sell_price[$valid_check]);
                                            unset($each_inventoryid[$valid_check]);
                                        }
                                    }
                                }
                            }
                            $each_inventoryid = array_values($each_inventoryid);
                            $each_sell_price = array_values($each_sell_price);
                            $each_invtype = array_values($each_invtype);
                            $each_quantity = array_values($each_quantity);

                            $total_count = count($each_inventoryid);
                            $id_loop = 500;

                            for($client_loop=0; $client_loop<$total_count; $client_loop++) {
                                if($each_inventoryid[$client_loop] > 0) {
                                    $inventoryid = $each_inventoryid[$client_loop];
                                    $sell_price = $each_sell_price[$client_loop];
                                    $invtype = $each_invtype[$client_loop];
                                    $quantity = $each_quantity[$client_loop];
                                    $inv_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT `name`, `category`, `part_no`, `final_retail_price`, `wcb_price`, `client_price`, `web_price`, `purchase_order_price`, `sales_order_price`, `admin_price`, `wholesale_price`, `commercial_price`, `preferred_price`, `gst_exempt` FROM `inventory` WHERE `inventoryid`='$inventoryid'"));
                                    $gst_exempt = $inv_info['gst_exempt'];
                                    ?>

                                    <div class="additional_product form-group clearfix refundable">
                                        <div class="col-sm-<?= $col1 ?>" <?= (in_array('inventory_cat',$field_config) ? '' : 'style="display:none;"') ?>><label class="show-on-mob">Inventory Category:</label>
                                            <input type="hidden" id="<?php echo 'inventorycat_'.$id_loop; ?>" name="inventorycat[]" value="<?= $inv_info['category'] ?>"><?= $inv_info['category'] ?>
                                        </div>
                                        <div class="col-sm-<?= $col2 ?>" <?= (in_array('inventory_part',$field_config) ? '' : 'style="display:none;"') ?>><label class="show-on-mob">Inventory Part #:</label>
                                            <input type="hidden" id="<?php echo 'inventorypart_'.$id_loop; ?>" name="inventorypart[]" value="<?= $inv_info['part_no'] ?>"><?= $inv_info['part_no'] ?>
                                        </div>
                                        <div class="col-sm-<?= $col3 ?>"><label class="show-on-mob">Inventory Name:</label>
                                            <?php echo $inv_info['name']; ?><input type="hidden" name="inventoryid[]" value="<?= $inventoryid ?>"><input type="hidden" name="inventorylabel" value="<?= $inv_info['name'].(in_array('inventory_type',$field_config) ? ': '.$invtype : '') ?>">
                                        </div>
                                        <div class="col-sm-<?= $col4 ?>" <?= (in_array('inventory_type',$field_config) ? '' : 'style="display:none;"') ?>><label class="show-on-mob">Type:</label>
                                            <?= $invtype ?><input type="hidden" name="invtype[]" value="<?= $invtype ?>">
                                        </div>
                                        <div class="col-sm-<?= $col5 ?>" <?= (in_array('inventory_price',$field_config) ? '' : 'style="display:none;"') ?>><label class="show-on-mob">Unit Price:</label>
                                            <input name="unit_price[]" id="<?php echo 'unitprice_'.$id_loop; ?>" value="<?php echo $sell_price / $quantity; ?>" type="hidden" class="form-control invunitprice" />
                                            <?php echo number_format($sell_price / $quantity,2); ?>
                                        </div> <!-- Quantity -->
                                        <div class="col-sm-<?= $col6 ?>"><label class="show-on-mob">Quantity:</label>
                                            <input name="init_quantity[]" value="<?= $quantity ?>" type="hidden" class="form-control" />
                                            <?= $quantity ?>
                                        </div> <!-- Quantity -->
                                        <div class="col-sm-<?= $col7 ?>"><label class="show-on-mob">Total:</label>
                                            <input name="init_price[]" value="<?= $sell_price ?>" type="hidden" />
                                            <input name="sell_price[]" id="<?php echo 'sellprice_'.$id_loop; ?>" onchange="countTotalPrice()" value="0" type="hidden" step="any" readonly class="form-control sellprice" />
                                            <input name="sell_price_display[]" id="<?php echo 'sellpricedisplay_'.$id_loop; ?>" value="<?= number_format($sell_price,2) ?>" type="text" step="any" readonly class="form-control" />
                                            <input name="inventory_row_id[]" type="hidden" value="<?= $insurer_row_id++ ?>" class="insurer_row_id" />
                                            <input name="inventory_gst_exempt[]" type="hidden" value="<?= $gst_exempt ?>" />
                                        </div>
                                        <div class="col-sm-2 return_block">
                                            <input name="quantity[]" id="<?php echo 'quantity_'.$id_loop; ?>" onchange="changeProduct(this)" value="0" type="number" min="<?= -$quantity ?>" max="0" step="any" class="form-control quantity" />
                                        </div>
                                        <div class="col-sm-12">
                                        <?php foreach(explode('#*#',explode(',',$inv_ins)[$client_loop]) as $line_insurer) {
                                            $line_insurer = explode(':',$line_insurer);
                                            if($line_insurer[1] != 0) { ?>
                                                <label class="col-sm-6">Payment by <?= get_client($dbc, $line_insurer[0]) ?>:</label>
                                                <label class="col-sm-6">$<?= number_format($line_insurer[1], 2) ?></label><input type="hidden" name="init_insurer_payment[]" value="<?= $line_insurer[1] ?>">
                                            <?php }
                                        } ?></div>
                                        <div class="col-sm-12 pay-div"></div>
                                    </div>
                                    <?php $id_loop++;
                                }
                            }
                        } ?>

                        <div class="clearfix"></div>
                        <div class="additional_product form-group clearfix adjust_block">
                            <div class="col-sm-<?= $col1 ?>" <?= (in_array('inventory_cat',$field_config) ? '' : 'style="display:none;"') ?>><label class="show-on-mob">Inventory Category:</label>
                                <select data-placeholder="Select Category..." id="inventorycat_0" name="inventorycat[]" class="chosen-select-deselect form-control inventorycat" width="380">
                                    <option value=""></option>
                                    <?php $query = mysqli_query($dbc,"SELECT `category` FROM inventory WHERE deleted=0 GROUP BY `category` ORDER BY `category`");
                                    while($row = mysqli_fetch_array($query)) {
                                        echo "<option value='". $row['category']."'>".$row['category'].'</option>';
                                    } ?>
                                </select>
                            </div>
                            <div class="col-sm-<?= $col2 ?>" <?= (in_array('inventory_part',$field_config) ? '' : 'style="display:none;"') ?>><label class="show-on-mob">Inventory Part #:</label>
                                <select data-placeholder="Select Part #..." id="inventorypart_0" name="inventorypart[]" class="chosen-select-deselect form-control inventorypart" width="380">
                                    <option value=""></option>
                                    <?php $query = mysqli_query($dbc,"SELECT `category`, `part_no` FROM inventory WHERE deleted=0 ORDER BY `part_no`");
                                    while($row = mysqli_fetch_array($query)) {
                                        echo "<option data-category='".$row['category']."' value='". $row['part_no']."'>".$row['part_no'].'</option>';
                                    } ?>
                                </select>
                            </div>
                            <div class="col-sm-<?= $col3 ?>"><label class="show-on-mob">Inventory Name:</label>
                                <select data-placeholder="Select Inventory..." id="inventoryid_0" name="inventoryid[]" class="chosen-select-deselect form-control inventoryid" width="380">
                                    <option value=""></option>
                                    <?php $query = mysqli_query($dbc,"SELECT `inventoryid`, `category`, `part_no`, `name` FROM inventory WHERE deleted=0 ORDER BY name");
                                    while($row = mysqli_fetch_array($query)) {
                                        echo "<option data-category='".$row['category']."' data-part='".$row['part_no']."' value='". $row['inventoryid']."'>".$row['name'].'</option>';
                                    } ?>
                                </select>
                            </div> <!-- Quantity -->
                            <div class="col-sm-<?= $col4 ?>" <?= (in_array('inventory_type',$field_config) ? '' : 'style="display:none;"') ?>><label class="show-on-mob">Type:</label>
                                <select data-placeholder="Select a Type..." id="invtype_0" name="invtype[]" class="chosen-select-deselect form-control invtype" width="480">
                                    <option value="General">General</option>
                                    <option <?= (strpos($injury_type,'WCB') === false && $injury_type != '' ? "disabled" : '') ?> value="WCB">WCB</option>
                                    <option <?= (strpos($injury_type,'MVA') === false && $injury_type != '' ? "disabled" : '') ?> value="MVA">MVA</option>
                                </select>
                            </div>
                            <div class="col-sm-<?= $col5 ?>" <?= (in_array('inventory_price',$field_config) ? '' : 'style="display:none;"') ?>><label class="show-on-mob">Unit Price:</label>
                                <input name="unit_price[]" id="unitprice_0" value=0 type="number" step="any" readonly class="form-control invunitprice" />
                            </div>
                            <div class="col-sm-<?= $col6 ?>"><label class="show-on-mob">Quantity:</label>
                                <input name="quantity[]" id="quantity_0" onchange="changeProduct($('#inventoryid_'+this.id.split('_')[1]).get(0));" value=1 type="number" min="0" step="any" class="form-control quantity" />
                            </div>
                            <div class="col-sm-<?= $col7 ?>"><label class="show-on-mob">Total:</label>
                                <input name="sell_price[]" id="sellprice_0" onchange="countTotalPrice()" value=0 type="number" step="any" readonly class="form-control sellprice" />
                                <input name="inventory_row_id[]" type="hidden" value="<?= $insurer_row_id++ ?>" class="insurer_row_id" />
                                <input name="inventory_gst_exempt[]" type="hidden" value="0" />
                            </div>
                            <div class="col-sm-<?= $col8 ?> col-pricing" <?= (in_array('pricing',$field_config) ? '' : 'style="display:none;"') ?>>
                                <img src="../img/icons/ROOK-edit-icon.png" alt="Edit Pricing" title="Edit Pricing" width="30" class="cursor-hand" onclick="$(this).hide(); $(this).closest('.col-pricing').find('.pricing-div').show();" />
                                <div class="pricing-div" style="display:none;">
                                    <select data-placeholder="Select Pricing" id="linepricing_0" name="linepricing[]" class="chosen-select-deselect form-control linepricing" onchange="changeProduct($('#inventoryid_'+this.id.split('_')[1]).get(0));">
                                        <option></option>
                                        <?php if(in_array('price_admin', $field_config)) { ?><option <?= ($pricing == 'admin_price' ? 'selected' : '') ?> value="admin_price">Admin Price</option><?php } ?>
                                        <?php if(in_array('price_client', $field_config)) { ?><option <?= ($pricing == 'client_price' ? 'selected' : '') ?> value="client_price">Client Price</option><?php } ?>
                                        <?php if(in_array('price_commercial', $field_config)) { ?><option <?= ($pricing == 'commercial_price' ? 'selected' : '') ?> value="commercial_price">Commercial Price</option><?php } ?>
                                        <?php if(in_array('price_distributor', $field_config)) { ?><option <?= ($pricing == 'distributor_price' ? 'selected' : '') ?> value="distributor_price">Distributor Price</option><?php } ?>
                                        <?php if(in_array('price_retail', $field_config)) { ?><option <?= ($pricing == 'final_retail_price' || $pricing == '' ? 'selected' : '') ?> value="final_retail_price">Final Retail Price</option><?php } ?>
                                        <?php if(in_array('price_preferred', $field_config)) { ?><option <?= ($pricing == 'preferred_price' ? 'selected' : '') ?> value="preferred_price">Preferred Price</option><?php } ?>
                                        <?php if(in_array('price_po', $field_config)) { ?><option <?= ($pricing == 'purchase_order_price' ? 'selected' : '') ?> value="purchase_order_price">Purchase Order Price</option><?php } ?>
                                        <?php if(in_array('price_sales', $field_config)) { ?><option <?= ($pricing == 'sales_order_price' ? 'selected' : '') ?> value="sales_order_price"><?= SALES_ORDER_NOUN ?> Price</option><?php } ?>
                                        <?php if(in_array('price_web', $field_config)) { ?><option <?= ($pricing == 'web_price' ? 'selected' : '') ?> value="web_price">Web Price</option><?php } ?>
                                        <?php if(in_array('price_wholesale', $field_config)) { ?><option <?= ($pricing == 'wholesale_price' ? 'selected' : '') ?> value="wholesale_price">Wholesale Price</option><?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_product_row(this);">
                                <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_product_row();">
                            </div>
                            <div class="col-sm-12 pay-div"></div>
                        </div>

                        <div id="add_here_new_product"></div>
                    </div>
                </div>

                <div class="form-group package_option" <?= (in_array('packages',$field_config) ? '' : 'style="display:none;"') ?>>
                    <label for="additional_note" class="col-sm-2 control-label">Packages:</label>
                    <div class="col-sm-7">
                        <div class="form-group clearfix hide-titles-mob">
                            <label class="col-sm-4">Category</label>
                            <label class="col-sm-4">Package Name</label>
                            <label class="col-sm-2">Fee</label>
                            <label class="col-sm-2 return_block">Refund</label>
                        </div>

                        <?php $each_package = explode(',', $packageid);
                        $each_package_cost = explode(',', $package_cost);

                        foreach($each_package as $loop_check => $check_service) {
                            $matched = false;
                            $check_fee = $each_package_cost[$loop_check];
                            if($check_fee > 0 || $check_fee < 0) {
                                foreach($each_package as $valid_check => $valid_service) {
                                    $valid_fee = $each_package_cost[$valid_check];
                                    if(!$matched && $loop_check != $valid_check && $check_service == $valid_service && $check_fee * 1 == $valid_fee * -1) {
                                        unset($each_package[$valid_check]);
                                        unset($each_package_cost[$valid_check]);
                                        unset($each_package[$loop_check]);
                                        unset($each_package_cost[$loop_check]);
                                        $matched = true;
                                    }
                                }
                            }
                        }
                        $each_package = array_values($each_package);
                        $each_package_cost = array_values($each_package_cost);

                        foreach($each_package as $loop => $package) {
                            $package_cost = $each_package_cost[$loop];
                            $package_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `package` WHERE `packageid`='$package'")); ?>
                            <div class="additional_package form-group clearfix <?= (empty($package) ? 'adjust_block' : 'refundable') ?>">
                                <div class="col-sm-4"><label class="show-on-mob">Package Category:</label>
                                    <?php if($package > 0) {
                                        echo $package_info['category'];
                                    } else { ?>
                                        <select data-placeholder="Select Category..." id="<?php echo 'packagecat_'.$loop; ?>" name="packagecat[]" class="chosen-select-deselect form-control packagecat">
                                            <option value=""></option>
                                            <?php $query = mysqli_query($dbc,"SELECT `category` FROM `package` WHERE deleted=0 GROUP BY `category` ORDER BY `category`");
                                            while($row = mysqli_fetch_array($query)) {
                                                echo "<option ".($package_info['category'] == $row['category'] ? 'selected' : '')." value='". $row['category']."'>".$row['category'].'</option>';
                                            } ?>
                                        </select>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-4"><label class="show-on-mob">Package Name:</label>
                                    <?php if($package > 0) {
                                        echo $package_info['heading'];
                                        ?><input type="hidden" name="package_label" value="<?= $package_info['category'].': '.$package_info['heading'] ?>">
                                        <input type="hidden" name="init_packageid[]" value="<?= $package ?>"><?php
                                    } else { ?>
                                        <select data-placeholder="Select Package..." id="<?php echo 'packageid_'.$loop; ?>" name="<?= (empty($package) ? '' : 'init_') ?>packageid[]" class="chosen-select-deselect form-control packageid">
                                            <option value=""></option>
                                            <?php $query = mysqli_query($dbc,"SELECT `packageid`, `heading`, `category`, `cost` FROM `package` WHERE deleted=0 ORDER BY `heading`");
                                            while($row = mysqli_fetch_array($query)) {
                                                echo "<option ".($package == $row['packageid'] ? 'selected' : '')." data-cat='".$row['category']."' data-cost='".$row['cost']."' value='". $row['packageid']."'>".$row['heading'].'</option>';
                                            } ?>
                                        </select>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-2"><label class="show-on-mob">Fee:</label>
                                    <input name="package_cost[]" id="<?php echo 'package_cost_'.$loop; ?>" onchange="countTotalPrice()" value="<?php echo $package_cost + 0; ?>" type="number" step="any" readonly class="form-control package_cost" />
                                    <input name="<?= (empty($package) ? '' : 'init_') ?>package_row_id[]" type="hidden" value="<?= $insurer_row_id ?>" class="insurer_row_id" />
                                    <input name="package_gst_exempt[]" type="hidden" value="0" />
                                </div>
                                <div class="col-sm-2 <?= empty($package) ? '' : 'return_block' ?>">
                                    <?php if(empty($package)) { ?>
                                        <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_package_row(this);">
                                        <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_package_row();">
                                    <?php } else { ?>
                                        <label><input type="checkbox" name="packagerow_refund[]" value="<?= $insurer_row_id ?>" onchange="countTotalPrice()"> Refund</label>
                                    <?php }
                                    $insurer_row_id++; ?>
                                </div>
                                <div class="col-sm-12">
                                    <?php foreach(explode('#*#',explode(',',$package_ins)[$client_loop]) as $line_insurer) {
                                        $line_insurer = explode(':',$line_insurer);
                                        if($line_insurer[1] != 0) { ?>
                                            <label class="col-sm-6">Payment by <?= get_client($dbc, $line_insurer[0]) ?>:</label>
                                            <label class="col-sm-6">$<?= number_format($line_insurer[1], 2) ?></label><input type="hidden" name="init_insurer_payment[]" value="<?= $line_insurer[1] ?>">
                                        <?php }
                                    } ?></div>
                                <div class="col-sm-12 pay-div"></div>
                            </div>
                        <?php } ?>
                        <div id="add_here_new_package"></div>

                        <!--<div class="form-group triple-gapped clearfix">
                            <div class="col-sm-offset-4 col-sm-8">
                                <button id="add_row_product" class="btn brand-btn pull-left">Add Product</button>
                            </div>
                        </div>-->
                    </div>
                </div>

                <div class="form-group misc_option" <?= (in_array('misc_items',$field_config) ? '' : 'style="display:none;"') ?>>
                    <label for="additional_note" class="col-sm-2 control-label">
                    <span class="popover-examples list-inline">
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Add any Miscellaneous Items here."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                    </span>
                    Miscellaneous Items:</label>
                    <div class="col-sm-7">
                        <div class="form-group clearfix hide-titles-mob">
                            <label class="col-sm-3">Product Name</label>
                            <label class="col-sm-3">Price</label>
                            <label class="col-sm-2">Quantity</label>
                            <label class="col-sm-2">Total</label>
                            <label class="col-sm-2 return_block">Refund Qty<span class="popover-examples list-inline">
                                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Enter a negative quantity to refund that quantity."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span></label>
                        </div>

                        <?php $each_misc = explode(',', $misc_items);
                        $each_misc_price = explode(',', $misc_prices);
                        $each_misc_qty = explode(',', $misc_qtys);
                        foreach($each_misc as $loop => $misc_item) {
                            $misc_price = $each_misc_price[$loop];
                            $misc_qty = $each_misc_qty[$loop]; ?>
                            <div class="additional_misc form-group clearfix <?= (empty($misc_item) ? 'adjust_block' : 'refundable') ?>">
                                <div class="col-sm-3"><label class="show-on-mob">Product Name:</label>
                                    <input type="text" <?= (empty($misc_item) ? '' : 'readonly') ?> name="misc_item[]" value="<?= $misc_item ?>" class="form-control misc_name">
                                </div>
                                <div class="col-sm-3"><label class="show-on-mob">Price:</label>
                                    <input type="number" <?= (empty($misc_item) ? '' : 'readonly') ?> step="any" min="0" name="misc_price[]" value="<?= $misc_price / $misc_qty ?>" onchange="setThirdPartyMisc(this); countTotalPrice()" class="form-control misc_price">
                                </div>
                                <div class="col-sm-2"><label class="show-on-mob">Quantity:</label>
                                    <input type="number" <?= (empty($misc_item) ? '' : 'readonly') ?> step="any" min="0" name="misc_qty[]" value="<?= $misc_qty ?>" onchange="setThirdPartyMisc(this); countTotalPrice()" class="form-control <?= (empty($misc_item) ? 'misc_qty' : 'init_qty') ?>">
                                </div>
                                <div class="col-sm-2"><label class="show-on-mob">Total:</label>
                                    <input type="number" readonly name="misc_total[]" value="<?= $misc_price ?>" class="form-control misc_total">
                                </div>
                                <div class="col-sm-2 <?= empty($misc_item) ? '' : 'return_block' ?>">
                                    <?php if(empty($misc_item)) { ?>
                                        <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_misc_row(this);">
                                        <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_misc_row();">
                                    <?php } else { ?>
                                        <label class="show-on-mob">Refund Qty:</label>
                                        <input type="number" name="misc_return[]" step="any" max="0" min="<?= -$misc_qty ?>" value="0" onchange="countTotalPrice()" class="form-control <?= (empty($misc_item) ? '' : 'misc_qty') ?>">
                                    <?php }
                                    $insurer_row_id++; ?>
                                </div>
                                <div class="col-sm-12">
                                    <?php foreach(explode('#*#',explode(',',$misc_ins)[$client_loop]) as $line_insurer) {
                                        $line_insurer = explode(':',$line_insurer);
                                        if($line_insurer[1] != 0) { ?>
                                            <label class="col-sm-6">Payment by <?= get_client($dbc, $line_insurer[0]) ?>:</label>
                                            <label class="col-sm-6">$<?= number_format($line_insurer[1], 2) ?></label><input type="hidden" name="init_insurer_payment[]" value="<?= $line_insurer[1] ?>">
                                        <?php }
                                    } ?></div>
                                <div class="col-sm-12 pay-div"></div>
                            </div>
                        <?php } ?>
                        <div id="add_here_new_misc"></div>
                    </div>
                </div>

                  <div class="form-group <?= (in_array('promo',$field_config) ? 'adjust_block' : '" style="display:none;') ?>">
                    <label for="site_name" class="col-sm-2 control-label">Promotion:</label>
                    <div class="col-sm-7">
                        <select data-placeholder="Select a Promotion..." id="promotionid" name="promotionid" class="chosen-select-deselect form-control" width="380">
                            <option value=""></option>
                            <?php
                            $query = mysqli_query($dbc,"SELECT promotionid, heading, cost FROM promotion WHERE deleted=0 AND DATE(expiry_date) >= DATE(NOW())");
                            while($row = mysqli_fetch_array($query)) {
                                if ($promotionid == $row['promotionid']) {
                                    $selected = 'selected="selected"';
                                } else {
                                    $selected = '';
                                }
                                echo "<option ".$selected." value='". $row['promotionid']."'>".$row['heading'].': $'.number_format($row['cost'],2).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                  </div>

                <div class="form-group <?= (in_array('pay_mode',$field_config) ? 'adjust_block' : '" style="display:none;') ?>">
                    <label for="site_name" class="col-sm-2 control-label">Adjustment Payment Method:</label>
                    <div class="col-sm-7">

                        <select data-placeholder="Select a Type..." name="paid" id="paid_status" class="chosen-select-deselect form-control" width="480">
                            <option value=""></option>
                            <option <?php if ($paid=='Yes') echo 'selected="selected"';?>  value="Yes">Patient Invoice : Patient is paying full amount on checkout.</option>
                            <option <?php if ($paid=='Waiting on Insurer') echo 'selected="selected"';?> value="Waiting on Insurer">Waiting on <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> : Clinic is waiting on <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> to pay full amount.</option>
                            <option <?php if ($paid=='No') echo 'selected="selected"';?>  value="No">Partially Paid : The invoice is being paid partially by patient and partially by <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?>.</option>
                            <option <?php if ($paid=='On Account') echo 'selected="selected"';?> value="On Account">A/R On Account : Patient will pay invoice in future. Must choose Payment Type as Apply A/R to Account.</option>
                            <option <?php if ($paid=='Credit On Account') echo 'selected="selected"';?> value="Credit On Account">Credit On Account : Patient is appyling credit to profile.</option>
                        </select>
                    </div>
                </div>

                <?php
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='invoice_tax'"));
                $value_config = $get_field_config['value'];

                $invoice_tax = explode('*#*',$value_config);

                $total_count = mb_substr_count($value_config,'*#*');
                $tax_rate = 0;
                foreach($invoice_tax as $invoice_tax_line) {
                    $invoice_tax_name_rate = explode('**',$invoice_tax_line);
                    $tax_rate += floatval($invoice_tax_name_rate[1]);
                } ?>
                <input type="hidden" name="tax_rate" id="tax_rate" value="<?= $tax_rate ?>" />
                <input name="total_price" value="<?php echo 0+$total_price; ?>" id="total_price" type="hidden" />
                <input name="final_price" value="<?php echo 0+$final_price; ?>" id="final_price" type="hidden" />

              <div class="form-group <?= (in_array('tipes',$field_config) ? 'adjust_block' : '" style="display:none;') ?>">
                <label for="site_name" class="col-sm-2 control-label">Gratuity($):</label>
                <div class="col-sm-7">
                  <input name="gratuity" onchange="countTotalPrice()" id="gratuity" type="text" class="form-control" />
                </div>
              </div>

              <div class="form-group" <?= (in_array('delivery',$field_config) ? '' : 'style="display:none;"') ?>>
                <label for="site_name" class="col-sm-2 control-label">
                    <span class="popover-examples list-inline">
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the delivery method chosen by the <?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?>."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                    </span>
                Original Delivery:</label>
                <div class="col-sm-7">
                  Option: <?= $delivery_type ?><br />
                  <?php if($delivery_type == 'Drop Ship' || $delivery_type == 'Shipping' || $delivery_type == 'Company Delivery') { ?>Address: <?= $delivery_address ?><br /><?php } ?>
                  <?php if($delivery_type == 'Drop Ship' || $delivery_type == 'Shipping') { ?>Contractor: <?= get_contact($dbc, $contractorid, 'name_company') ?><br /><?php } ?>
                  <?php if($delivery != 0) { ?>Amount: <?= $delivery ?><?php } ?>
                </div>
              </div>

              <div class="form-group <?= (in_array('delivery',$field_config) ? 'adjust_block' : '" style="display:none;') ?>">
                <label for="site_name" class="col-sm-2 control-label">
                    <span class="popover-examples list-inline">
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the delivery method chosen by the <?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?>."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                    </span>
                Delivery Option:</label>
                <div class="col-sm-7">
                  <select name="delivery_type" id="delivery_type" class="form-control chosen-select-deselect"><option></option>
                    <option value="Pick-Up">Pick-Up</option>
                    <option value="Company Delivery">Company Delivery</option>
                    <option value="Drop Ship">Drop Ship</option>
                    <option value="Shipping">Shipping</option>
                    <option value="Shipping on Customer Account">Shipping on Customer Account</option>
                  </select>
                </div>
              </div>

              <div class="form-group confirm_delivery" style="display:none;">
                <label for="site_name" class="col-sm-2 control-label">
                    <span class="popover-examples list-inline">
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Update the address for delivery. If it is wrong, you will need to update it on the <?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?> profile. You can also enter a one-time shipping address."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                    </span>
                Confirm Delivery Address:</label>
                <div class="col-sm-7">
                  <input name="delivery_address" onchange="countTotalPrice()" id="delivery_address" type="text" class="form-control" value="<?= $delivery_address ?>" />
                </div>
              </div>

              <div class="form-group deliver_contractor" style="display:none;">
                <label for="site_name" class="col-sm-2 control-label">
                    <span class="popover-examples list-inline">
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the contractor that will handle the delivery."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                    </span>
                Delivery Contractor:</label>
                <div class="col-sm-7">
                  <select name="contractorid" id="contractorid" class="form-control chosen-select-deselect"><option></option>
                    <?php $contractors = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `last_name`, `first_name`, `name` FROM `contacts` WHERE `category` LIKE 'Contractor%' AND `deleted`=0 AND `status`=1"),MYSQLI_ASSOC));
                    foreach($contractors as $contractor) {
                        $contractor = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `name` FROM `contacts` WHERE `contactid`='$contractor'"));
                        echo "<option ".($contractor['contactid'] == $contractorid ? 'selected' : '')." value='". $contractor['contactid']."'>".($contractor['name'] != '' ? decryptIt($contractor['name']) : decryptIt($contractor['first_name']).' '.decryptIt($contractor['last_name'])).'</option>';
                    } ?>
                  </select>
                </div>
              </div>

              <div class="form-group ship_amt" style="display:none;">
                <label for="site_name" class="col-sm-2 control-label">
                    <span class="popover-examples list-inline">
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Enter the cost of shipping."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                    </span>
                Delivery/Shipping Amount:</label>
                <div class="col-sm-7">
                  <input name="delivery" onchange="countTotalPrice()" id="delivery" type="text" class="form-control" value="<?= $delivery ?>" />
                </div>
              </div>

              <div class="form-group <?= (in_array('ship_date',$field_config) ? 'adjust_block' : '" style="display:none;') ?>">
                <label for="site_name" class="col-sm-2 control-label">
                    <span class="popover-examples list-inline">
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Enter the date by which the order will ship."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                    </span>
                Ship Date:</label>
                <div class="col-sm-7">
                  <input name="ship_date" onchange="countTotalPrice()" id="ship_date" type="text" class="form-control datepicker" value="<?= $ship_date ?>" />
                </div>
              </div>

            <div class="form-group" <?= (in_array('next_appt',$field_config) ? '' : 'style="display:none;"') ?>>
                    <label for="site_name" class="col-sm-2 control-label">
                    <span class="popover-examples list-inline">
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select to book the next appointment."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                    </span>
                    Next Appointment<span class="hp-red">*</span>:</label>
                    <div class="col-sm-7">
                      <label><input required name="next_appointment" type="radio" value="Yes" class="form next_appointment" /> Yes</label>
                      <label><input required name="next_appointment" checked type="radio" value="No" class="form next_appointment" /> No</label>
                        <a href="/mrbs/" target="_blank" class="next_appointment_fields pull-right btn brand-btn">Check Calendar</a><br><br>
                    </div>
                </div>

                <input type="hidden" name="bookingid" value=<?php echo $bookingid; ?> >

            <span class="next_appointment_fields">
              <div class="form-group">
                <label for="first_name" class="col-sm-2 control-label"></label>
                <div class="col-sm-7 book-calendar">
                    <div class="form-group clearfix">
                        <label class="col-sm-3">Start Appt Date & Time
                            <span class="popover-examples list-inline">
                                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Click on the 15 minute interval labels to specify those times"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="25"></a>
                            </span>
                        </label>
                        <label class="col-sm-3">End Appt Date & Time
                            <span class="popover-examples list-inline">
                                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Click on the 15 minute interval labels to specify those times"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="25"></a>
                            </span>
                        </label>
                        <label class="col-sm-5">Type
                        </label>
                    </div>
                    <div class="form-group clearfix book-validate-cal">
                        <div class="book_1">
                            <span class="col-sm-3">
                                <input name="block_appoint_date[]" id="appointdate_1" type="text" placeholder="Click for Datepicker" class="datetimepicker form-control"></p>
                            </span>
                            <span class="col-sm-3">
                                <input name="block_end_appoint_date[]" id="endappointdate_1" type="text" placeholder="Click for Datepicker" class="datetimepicker form-control"></p>
                            </span>
                            <span class="col-sm-5">
                                <select data-placeholder="Select a Type..." id="appointtype_1" name="appointtype[]" class="chosen-select-deselect form-control input-sm">
                                    <option value=""></option>
                                    <?php $appointment_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `appointment_type` WHERE `deleted` = 0"),MYSQLI_ASSOC);
                                    foreach ($appointment_types as $appointment_type) {
                                        echo '<option '.($type == $appointment_type['id'] ? 'selected' : '').' value="'.$appointment_type['id'].'">'.$appointment_type['name'].'</option>';
                                    } ?>
                                </select>
                            </span>
                            <span class="col-sm-1">
                                <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="removeclass(this);" title="Remove this Row">
                                <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="addmore();" title="Add Additional Appointment">
                            </span><div class="clearfix"></div>
                        </div>
                    </div>
                </div>
              </div>

              </span>

                   <div class="form-group <?= (in_array('survey',$field_config) ? 'adjust_block' : '" style="display:none;') ?>">
                    <label for="site_name" class="col-sm-2 control-label">Send Survey:</label>
                    <div class="col-sm-7">
                        <select data-placeholder="Select a Survey..." name="survey" class="chosen-select-deselect form-control" width="380">
                          <option value=""></option>
                          <?php
                            $query = mysqli_query($dbc,"SELECT surveyid, name, service FROM crm_feedback_survey_form WHERE deleted=0");
                            while($row = mysqli_fetch_array($query)) {
                                echo "<option value='". $row['surveyid']."'>".$row['name'].' : '.$row['service'].'</option>';
                            }
                          ?>
                        </select>
                    </div>
                  </div>

                <?php if (strpos(','.get_config($dbc, 'crm_dashboard').',', ',Recommendations,') !== FALSE) { ?>
                    <div class="form-group"<?= (in_array('request_recommend',$field_config) ? '' : 'style="display:none;"') ?>>
                        <label for="site_name" class="col-sm-2 control-label">
                            <span class="popover-examples list-inline">
                            <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select whether or not to send the Recommendation email."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                            </span>
                            Request Recommendation Report:</label>
                        <div class="col-sm-7">
                            <label class="control-label"><input type="radio" name="request_recommendation" value="send"> Send</label>
                            <label class="control-label"><input type="radio" name="request_recommendation" value="no"> Don't Send</label>
                        </div>
                    </div>
                <?php } ?>

                   <div class="form-group <?= (in_array('followup',$field_config) ? 'adjust_block' : '" style="display:none;') ?>">
                    <label for="site_name" class="col-sm-2 control-label">Send Follow Up Email After Assessment: </label>
                    <div class="col-sm-7">
                        <select data-placeholder="Select an Email Type..." name="follow_up_assessment_email" class="chosen-select-deselect form-control" width="380">
                          <option value=""></option>
                          <option value="Massage">Massage Follow Up Email</option>
                          <option value="Physiotherapy">Physiotherapy Follow Up Email</option>
                        </select>
                    </div>
                  </div>

                <div class="form-group">
                    <label for="additional_note" class="col-sm-2 control-label">Invoice Payment Information:</label>
                    <div class="col-sm-7">
                        <div class="form-group clearfix hide-titles-mob">
                            <label class="col-sm-4">Paid By</label>
                            <label class="col-sm-3">Payment Type</label>
                            <label class="col-sm-3">Payment Amount</label>
                            <label class="col-sm-2 return_block">Refund Amounts</label>
                        </div>

                        <?php foreach($insurer_paid_who as $loop_check => $check_insurer) {
                            $check_amt = $insurer_paid_amt[$loop_check];
                            if($check_amt != 0) {
                                foreach($insurer_paid_who as $valid_check => $valid_insurer) {
                                    $valid_amt = $insurer_paid_amt[$valid_check];
                                    if($loop_check != $valid_check && $check_insurer == $valid_insurer) {
                                        $insurer_paid_amt[$loop_check] += $valid_amt;
                                        unset($insurer_paid_who[$valid_check]);
                                        unset($insurer_paid_amt[$valid_check]);
                                    }
                                }
                            } else {
                                unset($insurer_paid_who[$loop_check]);
                                unset($insurer_paid_amt[$loop_check]);
                            }
                        }
                        $insurer_paid_who = array_values($insurer_paid_who);
                        $insurer_paid_amt = array_values($insurer_paid_amt);

                        foreach($insurer_paid_who as $i => $ins_pay_id) {
                            if($insurer_paid_amt[$i] > 0) { ?>
                                <div class="form-group clearfix">
                                    <div class="col-sm-4"><label class="col-sm-4 show-on-mob">Paid By:</label><?= get_client($dbc, $ins_pay_id) ?></div>
                                    <div class="col-sm-3"><label class="col-sm-4 show-on-mob">Payment Type:</label><?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> Payment</div>
                                    <div class="col-sm-3"><label class="col-sm-4 show-on-mob">Payment Amount:</label>$<?= number_format($insurer_paid_amt[$i],2) ?><input type="hidden" name="amount_previously_paid[]" value="<?= $insurer_paid_amt[$i] ?>"><input type="hidden" name="insurer_amt[]" value="<?= $insurer_paid_amt[$i] ?>"><input type="hidden" name="insurer_payer[]" value="<?= $ins_pay_id ?>"></div>
                                </div>
                            <?php }
                        }

                        foreach($patient_paid_type as $loop_check => $check_patient) {
                            $check_amt = $patient_paid_amt[$loop_check];
                            if($check_amt != 0) {
                                foreach($patient_paid_type as $valid_check => $valid_patient) {
                                    $valid_amt = $patient_paid_amt[$valid_check];
                                    if($loop_check != $valid_check && $check_patient == $valid_patient) {
                                        $patient_paid_amt[$loop_check] += $valid_amt;
                                        unset($patient_paid_type[$valid_check]);
                                        unset($patient_paid_amt[$valid_check]);
                                    }
                                }
                            } else {
                                unset($patient_paid_type[$loop_check]);
                                unset($patient_paid_amt[$loop_check]);
                            }
                        }
                        $patient_paid_type = array_values($patient_paid_type);
                        $patient_paid_amt = array_values($patient_paid_amt);

                        foreach($patient_paid_type as $i => $patient_pay_type) { ?>
                            <div class="form-group clearfix">
                                <div class="col-sm-4"><label class="col-sm-4 show-on-mob">Paid By:</label><?= $patient ?></div>
                                <div class="col-sm-3"><label class="col-sm-4 show-on-mob">Payment Type:</label><?= $patient_pay_type ?></div>
                                <div class="col-sm-3"><label class="col-sm-4 show-on-mob">Payment Amount:</label>$<?= number_format($patient_paid_amt[$i],2) ?><input type="hidden" name="amount_previously_paid[]" value="<?= $patient_paid_amt[$i] ?>"></div>
                                <div class="col-sm-2 return_block"><label class="col-sm-4 show-on-mob">Refund amount to <?= $patient_pay_type ?>:</label>
                                    <input type="hidden" name="refund_to_type[]" value="<?= $patient_pay_type ?>"><input type="number" class="form-control" name="refund_type_amount[]" value="0" min="0" max="<?= $patient_paid_amt[$i] ?>" data-status="auto" onchange="adjustRefundAmt();" step="any"></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="form-group payment_option adjust_block">
                    <label for="additional_note" class="col-sm-2 control-label"><?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?> Payment:</label>
                    <div class="col-sm-7">
                        <div class="form-group clearfix hide-titles-mob">
                            <label class="col-sm-6">Type</label>
                            <label class="col-sm-6">Amount</label>
                        </div>

                        <?php
                        $cust_payments = mysqli_query($dbc, "SELECT SUM(`patient_price`) amt, `paid` FROM `invoice_patient` WHERE `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE '$invoiceid' IN (`invoiceid`,`invoiceid_src`)) GROUP BY `paid`");
                        /*while($cust_payment = mysqli_fetch_array($cust_payments)) { ?>
                            <div class="clearfix"></div>
                            <div class="additional_payment form-group clearfix ">
                                <div class="col-sm-6"><label class="show-on-mob">Payment Type:</label>
                                    <select id="payment_type" name="payment_type[]" data-placeholder="Select a Type..." class="chosen-select-deselect form-control" width="380">
                                        <option value=''></option>
                                        <?php foreach(explode(',',get_config($dbc, 'invoice_payment_types')) as $available_pay_method) { ?>
                                            <option <?php if ($cust_payment['paid'] == $available_pay_method) { echo " selected"; } ?>  value = '<?= $available_pay_method ?>'><?= $available_pay_method ?></option>
                                        <?php } ?>
                                        <?php if($account_balance != 0) { ?>
                                        <option value = 'Patient Account' >Patient Account : $<?php echo $account_balance; ?></option>
                                        <?php }
                                        if($cust_payment[0] == "Patient Account") { ?>
                                            <option <?php if ($cust_payment['paid'] == "Patient Account") { echo " selected"; } ?>  value = 'Patient Account' >Patient Account</option>
                                        <?php }
                                        if(strpos(WEBSITE_URL,'clinicace') !== FALSE) { ?>
                                            <option <?php if ($patient_payment_pay[0] == "On Account") { echo " selected"; } ?>  value = 'On Account'>Apply A/R to Account</option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-5"><label class="show-on-mob">Payment Amount:</label>
                                    <input name="payment_price[]" value="<?php echo $cust_payment['amt'];?>" type="text" class="form-control payment_price" onchange="countTotalPrice();" />
                                </div>
                                <div class="col-sm-1">
                                    <img src="<?= WEBSITE_URL ?>/img/plus.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right" onclick="add_patient_payment_row();">
                                    <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right" onclick="rem_patient_payment_row(this);">
                                </div>
                            </div>
                        <?php }*/
                        //$pt1 = explode('#*#',$payment_type);
                        //$pt_type = $pt1[0];
                        //$pt_amount = $pt1[1];
                        //$pt_type_each = explode(',',$pt_type);
                        //$pt_amount_each = explode(',',$pt_amount);
                        //$final_pt = '';
                        //$m = 0;
                        //foreach ($pt_type_each as $pt_each) {
                        //    if($pt_each != '') {
                        //    $final_pt .= $pt_each.','.$pt_amount_each[$m].'#*#';
                        //    }
                        //    $m++;
                        //}
                        //
                        //$patient_pay = explode('#*#',$final_pt);
                        //
                        //$total_count = mb_substr_count($final_pt,'#*#');
                        //for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
                        //    $patient_payment_pay = explode(',',$patient_pay[$eq_loop]);
                        //
                        //    if($patient_payment_pay[0] != '') {
                        //    ?><!--
                            <div class="clearfix"></div>
                            <div class="additional_payment form-group clearfix ">
                                <div class="col-sm-6"><label class="show-on-mob">Payment Type:</label>
                                    <select id="payment_type" name="payment_type[]" data-placeholder="Select a Type..." class="chosen-select-deselect form-control" width="380" onchange="set_patient_payment_row();">
                                        <option value=''></option>
                                        <?php //foreach(explode(',',get_config($dbc, 'invoice_payment_types')) as $available_pay_method) { ?>
                                            <option <?php //if ($patient_payment_pay[0] == $available_pay_method) { echo " selected"; } ?>  value = '<?php //echo $available_pay_method ?>'><?php //echo $available_pay_method ?></option>
                                        <?php //} ?>
                                        <?php //if($account_balance != 0) { ?>
                                        <option value = 'Patient Account' >Patient Account : $<?php //echo $account_balance; ?></option>
                                        <?php //}
                                        //if($patient_payment_pay[0] == "Patient Account") { ?>
                                            <option <?php //if ($patient_payment_pay[0] == "Patient Account") { echo " selected"; } ?>  value = 'Patient Account' >Patient Account</option>
                                        <?php //} ?>
                                        <option <?php //if ($patient_payment_pay[0] == "On Account") { echo " selected"; } ?>  value = 'On Account'>Apply A/R to Account</option>
                                    </select>
                                </div>
                                <div class="col-sm-5"><label class="show-on-mob">Payment Amount:</label>
                                    <input name="payment_price[]" value="<?php //echo $patient_payment_pay[1];?>" type="text" class="form-control payment_price" onchange="countTotalPrice();" />
                                </div>
                                <div class="col-sm-1">
                                    <img src="<?php //echo WEBSITE_URL ?>/img/plus.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right" onclick="add_patient_payment_row();">
                                    <img src="<?php //echo WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right" onclick="rem_patient_payment_row(this);">
                                </div>
                            </div>-->
                        <?php //}
                        //} ?>

                        <div class="clearfix"></div>
                        <div class="additional_payment form-group clearfix">
                            <div class="col-sm-6"><label class="show-on-mob">Payment Type:</label>
                              <select id="payment_type" name="payment_type[]" data-placeholder="Select a Type..." class="chosen-select-deselect form-control" width="380">
                                    <option value=''></option>
                                    <?php foreach(explode(',',get_config($dbc, 'invoice_payment_types')) as $available_pay_method) { ?>
                                        <option value = '<?= $available_pay_method ?>'><?= $available_pay_method ?></option>
                                    <?php } ?>
                                    <?php if($account_balance != 0) { ?>
                                    <option value = 'Patient Account' >Apply Credit to Patient Account : $<?php echo $account_balance; ?></option>
                                    <?php }
                                    if(strpos(WEBSITE_URL,'clinicace') !== FALSE) { ?>
                                        <option value = 'On Account'>Apply A/R to Account</option>
                                    <?php } ?>
                              </select>
                            </div>
                            <div class="col-sm-5"><label class="show-on-mob">Payment Amount:</label>
                                <input name="payment_price[]" type="text" id="payment_price_0" class="form-control payment_price" onchange="countTotalPrice();" />
                            </div>
                            <div class="col-sm-1">
                                <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_patient_payment_row(this);">
                                <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_patient_payment_row();">
                            </div>
                        </div>

                        <div id="add_here_new_payment"></div>
                    </div>
                </div>

                   <div class="form-group" <?= (in_array('comment',$field_config) ? '' : 'style="display:none;"') ?>followup>
                    <label for="site_name" class="col-sm-2 control-label">Comment:</label>
                    <div class="col-sm-7">
                        <textarea name="comment" class="form-control"><?= $comment ?></textarea>
                    </div>
                  </div>

                 <div class="form-group">
                    <div class="col-sm-2">
                        <p><span class="empire-red pull-right"><em>Required Fields *</em></span></p>
                    </div>
                    <div class="col-sm-6"></div>
                </div>

              <div class="form-group">
                <div class="col-sm-2 clearfix">
                    <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Clicking here will discard changes and return you to the <?= (empty($current_tile_name) ? 'Check Out' : $current_tile_name) ?> tile main dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="refund_invoices.php" class="btn brand-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-7">
                    <button type="submit" name="submit_btn" onclick="return validateappo();" id="submit" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
                    <span class="popover-examples list-inline pull-right" style="margin:5px;"><a data-toggle="tooltip" data-placement="top" title="Click here to Submit the Refund / Adjustment after processing any applicable payments."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                </div>
              </div>

        </form>

		<?php // } ?>

        <?php
        // if(!empty($_GET['action'])) {  include('pay_invoice.php');   }
        ?>

    </div>
  </div>
<style>
.pay-div {
	padding: 0;
}
@media(min-width:768px) {
	.sticky {
		max-width: 100%;
		position: fixed !important;
		top: 0;
		right: 0;
	}
	.preview_div {
		position: absolute;
		display: block;
		right: 0;
	}
}
@media(max-width:767px) {
	.preview_div {
		position: relative;
	}
}
.preview_div {
	padding-right: 2em;
}
</style>
<script>
$(window).scroll(function() {
	if ($(this).scrollTop() > $('form')[0].offsetTop) {
		$('.preview_div').addClass("sticky");
	} else {
		$('.preview_div').removeClass("sticky");
	}
});
$(document).ready(function() {
	$('.adjust_block').hide();
	$('.return_block').hide();
	previous_payment += <?= array_sum($patient_paid_amt) > $get_invoice['final_price'] ? $get_invoice['final_price'] : array_sum($patient_paid_amt) ?>;
	$('.form-control').change(function() {
		<?php if($patient != '') { ?>
			$('.detail_patient_name').html('<?= $patient ?>');
			$('.detail_patient_injury').html('<?= $injury ?>');
			if($('[name=treatment_plan]').is(':visible')) {
				$('.detail_patient_treatment').html('<?= $treatment_plan ?>').closest('h4').show();
			} else {
				$('.detail_patient_treatment').closest('h4').hide();
			}
			$('.detail_staff_name ').html('<?= $staff ?>');
		<?php } else { ?>
			if($('.non_patient_fields').is(':visible')) {
				$('.detail_patient_name').html($('[name=first_name]').val() + ' ' + $('[name=last_name]').val());
				$('.detail_patient_injury').closest('h4').hide();
			} else {
				$('.detail_patient_name').html($('[name=patientid] option:selected').text());
				if($('#injuryid_chosen').is(':visible')) {
					$('.detail_patient_injury').html($('[name=injuryid] option:selected').text() == '' ? 'Please Select' : $('[name=injuryid] option:selected').text()).closest('h4').show();
				}
			}
			$('.detail_staff_name ').html('<?= $staff ?>');
			if($('[name=treatment_plan]').is(':visible')) {
				$('.detail_patient_treatment').html($('[name=treatment_plan] option:selected').text()).closest('h4').show();
			} else {
				$('.detail_patient_treatment').closest('h4').hide();
			}
		<?php } ?>
		$('.detail_promo_amt ').html($('[name=promotionid] option:selected').text() == '' ? 'N/A' : $('[name=promotionid] option:selected').text());
		if($('#paid_status').val() != '' && $('#paid_status').val() != 'Saved' && $('#paid_status').val() != 'Waiting on Insurer') {
			$('.detail_patient_amt').closest('h4').show();
		} else {
			$('.detail_patient_amt').closest('h4').hide();
		}
		if($('#paid_status').val() == 'No' || $('#paid_status').val() == 'Waiting on Insurer') {
			$('.detail_insurer_amt').closest('h4').show();
		} else {
			$('.detail_insurer_amt').closest('h4').hide();
		}
		$('[name="serviceid[]"]').each(function() {
			var label = $(this).find('option:selected').text();
			var fee = $(this).closest('.form-group').find('[name="fee[]"]').val();
		});
	});
	$('.form-control').first().change();
	countTotalPrice();
	<?php if($paid != '') {
		echo "pay_mode_selected('$paid');";
	} ?>
});
$(document).on('change', 'select[name="pricing"]', function() {
    if ($('[name="pricing"] option:selected').val()=='admin_price') {
        $('[name="unit_price[]"]').attr('readonly', false);
    } else {
        $('[name="unit_price[]"]').attr('readonly', true);
    }
    updatePricing();
});
$(document).on('change', 'select[name="linepricing[]"]', function() {
    var rowid = this.id.split('_')[1];
    if ($('#linepricing_'+rowid+' option:selected').val()=='admin_price') {
        $('#unitprice_'+rowid).attr('readonly', false);
    } else {
        $('#unitprice_'+rowid).attr('readonly', true);
    }
    updatePricing();
});
$(document).on('change', '[name="unit_price[]"]', function() {
    adminPrice(this);
});
$(document).on('change', 'select[name="app_type"]', function() { changeApptType(this.value); });
$(document).on('change', 'select[name="pricing"]', function() { updatePricing(); });
$(document).on('change', 'select[name="paid"]', function() { pay_mode_selected(this.value); });
$(document).on('change', 'select.service_category_onchange', function() { changeCategory(this); });
$(document).on('change', 'select[name="serviceid[]"]', function() { changeService(this); });
$(document).on('change', 'select[name="inventorycat[]"]', function() { filterInventory(this); });
$(document).on('change', 'select[name="inventorypart[]"]', function() { changeProduct(this); });
$(document).on('change', 'select[name="inventoryid[]"]', function() { changeProduct(this); });
$(document).on('change', 'select[name="invtype[]"]', function() { changeProduct(this); });
$(document).on('change', 'select[name="packagecat[]"]', function() { changePackage(this); });
$(document).on('change', 'select[name$="packageid[]"]', function() { changePackage(this); });
$(document).on('change', 'select[name="promotionid"]', function() { changePromotion(this); });
$(document).on('change', 'select[name="delivery_type"]', function() { countTotalPrice(); });
$(document).on('change', 'select[name="contractorid"]', function() { countTotalPrice(); });
$(document).on('change', 'select[name="payment_type[]"]', function() { set_patient_payment_row(); });

function adjustRefundAmt() {
	$('[name="refund_type_amount[]"]').data('status','manual');
	if($('[name=paid]').val() == 'Yes' || $('[name=paid]').val() == 'No') {
		$('[name=paid]').change();
	} else if($('[name=paid]').val() == 'Waiting on Insurer') {
		pay_mode_selected('No');
	} else {
		pay_mode_selected('Yes');
	}

}
function pay_mode_selected(paid) {
	if(paid == 'No' || paid == 'Waiting on Insurer') {
		if($('.pay-div').html() == '') {
			$('.pay-div').html('<div class="insurer_line"><label class="col-sm-2 control-label"><?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> Name:</label>'+
				'<div class="col-sm-4"><select name="insurerid[]" class="chosen-select-deselect form-control" width="380">'+
                    '<option value=""></option><?php
					$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category IN ('".implode("','",$payer_config)."') AND deleted=0 ORDER BY name"),MYSQLI_ASSOC));
					foreach($query as $row) {
						echo '<option value="'. $row.'">'.htmlentities(get_client($dbc, $row), ENT_QUOTES).'</option>';
					}
					?></select></div>'+
				'<label class="col-sm-2 control-label"><?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> Portion: <span class="popover-examples list-inline">'+
					'<a href="#job_file" data-toggle="tooltip" data-placement="top" title="The portion that the <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> will pay before tax. The applicable tax will be added to this amount."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span></label>'+
				'<div class="col-sm-2"><input type="number" step="any" name="insurer_payment_amt[]" class="form-control" value="0" onchange="countTotalPrice();">'+
					'<input type="hidden" name="insurer_row_applied[]" value=""></div>'+
				'<div class="col-sm-2"><img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_insurer_row(this);">'+
					'<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_insurer_row(this);"></div></div>');
			$('[name="insurerid[]"]').select2({
                width: '100%'
            });
			$('.pay-div').each(function() {
				$(this).find('[name="insurer_row_applied[]"]').val($(this).closest('.form-group').find('.insurer_row_id').val());
			});
		}
		if(paid == 'Waiting on Insurer') {
			$('[name="serviceid[]"]').change();
			$('[name="quantity[]"]').change();
			$('[name="packageid[]"]').change();
			$('[name="misc_qty[]"]').change();
		}
	} else {
		$('.pay-div').empty();
	}
	$('[name=paid]').val(paid).trigger('change.select2');
	$('[name="payment_price[]"]').last().attr('readonly','readonly');
	countTotalPrice();
}

var clone = $('.book-validate-cal').clone();
clone.find('.datetimepicker').val('');
clone.find('.datetimepicker').each(function() {
$(this).removeAttr('id').removeClass('hasDatepicker');
	$('.datetimepicker').datetimepicker({
		controlType: 'select',
		changeMonth: true,
		changeYear: true,
		yearRange: '<?= date('Y') - 20 ?>:<?= date('Y') + 5 ?>',
		dateFormat: 'yy-mm-dd',
		timeFormat: "hh:mm tt",
		minuteGrid: 15,
		hourMin: 6,
		hourMax: 20,
		//minDate: 0
	});
});
function addmore()
{
	var classname = $('.book-calendar [class^=book_]').last().attr('class');
	var classes = classname.split("_");
	var value = parseInt(classes[1]) + 1;
	var currentclass = 'book_' + value;

	var insertstring = '<div class="'+ currentclass +'">'+
							'<span class="col-sm-3">'+
								'<input name="block_appoint_date[]" id="appointdate_'+value+'" type="text" placeholder="Click for Datepicker" class="datetimepicker form-control"></p>'+
							'</span>'+
							'<span class="col-sm-3">'+
								'<input name="block_end_appoint_date[]" id="endappointdate_'+value+'" type="text" placeholder="Click for Datepicker" class="datetimepicker form-control"></p>'+
							'</span>'+
							'<span class="col-sm-5">'+
								'<select data-placeholder="Select a Type..." id="appointtype_'+value+'" name="appointtype[]" class="chosen-select-deselect form-control input-sm"><option value=""></option>'+
								'<option value="A">Private-PT-Assessment</option>'+
							'<option value="B">Private-PT-Treatment</option>'+
							'<option value="C">MVC-IN-PT-Assessment</option>'+
							'<option value="D">MVC-IN-PT-Treatment</option>'+
							'<option value="F">MVC-OUT-PT-Assessment</option>'+
							'<option value="G">MVC-OUT-PT-Treatment</option>'+
							'<option value="H">WCB-PT-Assessment</option>'+
							'<option value="J">WCB-PT-Treatment</option>'+
							'<option value="K">Private-MT</option>'+
							'<option value="L">MVC-IN-MT</option>'+
							'<option value="M">MVC-OUT-MT</option>'+
							'<option value="N">AHS-PT-Assessment</option>'+
							'<option value="O">AHS-PT-Treatment</option>'+
							'<option value="S">Reassessment</option>'+
							'<option value="T">Post-Reassessment</option>'+
							'<option value="U">Private-MT-Assessment</option>'+
							'<option value="V">Orthotics</option>'+
							'</select></p>'+
							'</span>'+
							'<span class="col-sm-1">'+
							'<img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="removeclass(this);" title="Remove this Row">'+
							'<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="addmore();" title="Add Additional Appointment">'+
							'</span><div class="clearfix"></div>'+
						'</div>';
	jQuery(insertstring).insertAfter('.' + classname);
	resetChosen($('.'+currentclass).find('.chosen-select-deselect'));
	var clone = $('.book-validate-cal').clone();
	clone.find('.datetimepicker').val('');
	clone.find('.datetimepicker').each(function() {
		$(this).removeAttr('id').removeClass('hasDatepicker');
		$('.datetimepicker').datetimepicker({
			controlType: 'select',
			changeMonth: true,
			changeYear: true,
			yearRange: '<?= date('Y') - 10 ?>:<?= date('Y') + 5 ?>',
			dateFormat: 'yy-mm-dd',
			timeFormat: "hh:mm tt",
			minuteGrid: 15,
			hourMin: 7,
			hourMax: 19
		});
	});
}

function removeclass(remove)
{
	if($('[class^=book_]').length == 1) {
		addmore();
	}
	$(remove).closest('[class^=book_]').remove();
}

function validateappo()
{
	if(jQuery("input[name='next_appointment']:checked").val() == 'Yes' || jQuery("input[name='next_appointment']:checked").val() == 'yes')
	{
		var count = 0;
		//alert(jQuery(".book-validate-cal > div"));
		var therapiststemid = "<?php echo $get_invoice['therapistsid']; ?>";
		jQuery(".book-validate-cal").children().each(function(n, i) {
			if(typeof this.className !== 'object') {
				var classname = this.className;
				var splitclass = classname.split("_");
				var i = parseInt(splitclass[1]);
				var appdate = jQuery('#appointdate_' + i).val();
				var endappdate = jQuery('#endappointdate_' + i).val();
				$.ajax({
				  type: "GET",
				  url: "../Invoice/appointment_ajax.php",
				  data: 'appdate=' + appdate + '&endappdate=' + endappdate + '&therapistid=' + therapiststemid,
				  cache: false,
				  success: function(data){
					  if(data == 1) {
						  jQuery('#appointdate_' + i)
						  jQuery('#appointdate_' + i).addClass('borderClass');
						  jQuery('#endappointdate_' + i).addClass('borderClass');
						  count = 1;
					  }
					  else {
						  jQuery('#appointdate_' + i)
						  jQuery('#appointdate_' + i).removeClass('borderClass');
						  jQuery('#endappointdate_' + i).removeClass('borderClass');
					  }

				  },

				  error: function(data) {
					  alert("Something Wrong in Appointment");
				  },

				  async:false
				});
			}
		});

		if(count > 0) {
			alert("There are some clashes in Appointment dates marked with Red Border");
			return false;
		}

		return true;
	}
}
</script>
<?php include ('../footer.php'); ?>
