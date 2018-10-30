<?php
/*
Add Invoice
*/
include_once('../tcpdf/tcpdf.php');
include ('../include.php');
if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
} else {
    checkAuthorised('check_out');
}
error_reporting(0);

if (!empty($_GET['type']) && $_GET['invoiceid'] > 0) {
    mysqli_query($dbc, "UPDATE `invoice` SET `type` = '".$_GET['type']."' WHERE `invoiceid` = '".$_GET['invoiceid']."'");
}

if (isset($_POST['save_btn'])) {
	$invoice_mode = 'Saved';
	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}
    include('add_update_invoice.php');
    echo '<script type="text/javascript"> alert("Invoice Successfully Saved"); window.location.replace("index.php?tab=today"); </script>';
}

if (isset($_POST['submit_btn'])) {
	$invoice_mode = 'New';
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
	$invoice_design = get_config($dbc, 'invoice_design');
    if(!empty($get_invoice['type']) && !empty(get_config($dbc, 'invoice_design_'.$get_invoice['type']))) {
        $invoice_design = get_config($dbc, 'invoice_design_'.$get_invoice['type']);
    }
	switch($invoice_design) {
		case 2:
			include('pos_invoice_2.php');
			break;
		case 3:
			include('pos_invoice_3.php');
			break;
		case 4:
			include ('patient_invoice_pdf.php');
			if($insurerid != '') {
				include ('insurer_invoice_pdf.php');
			}
			break;
		case 5:
            include('pos_invoice_small.php');
			break;
		case 'service':
            include('pos_invoice_service.php');
			break;
		case 'pink':
			include ('pos_invoice_pink.php');
			break;
		case 'cnt1':
			include ('pos_invoice_contractor_1.php');
			break;
		case 'cnt2':
			include ('pos_invoice_contractor_2.php');
			break;
		case 'cnt3':
			include ('pos_invoice_contractor_3.php');
			break;
        case 'custom_ticket':
            include ('pos_invoice_custom_ticket.php');
            break;
		case 1:
        default:
			include('pos_invoice_1.php');
			break;
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

    mysqli_close($dbc); //Close the DB Connection
}

if (isset($_POST['submit_pay'])) {

		$all_invoiceid = $_POST['invoiceid'];
		//$payment_type = $_POST['payment_type'];
		$from = $_POST['from'];

        $type = implode(',', $_POST['payment_type']);
        $payment_price = implode(',', $_POST['payment_price']);
        $payment_type = $type.'#*#'.$payment_price;

        $payment_type = !empty($payment_type) ? "'$payment_type'" : "NULL";

		$var=explode(',',$all_invoiceid);
	    foreach($var as $invoiceid) {

            $bookingid = get_all_from_invoice($dbc, $invoiceid, 'bookingid');
            $follow_up_call_status = 'Paid';
            $query_update_booking = "UPDATE `booking` SET `follow_up_call_status` = '$follow_up_call_status' WHERE `bookingid` = '$bookingid'";
            $result_update_booking = mysqli_query($dbc, $query_update_booking);

            $calid = get_calid_from_bookingid($dbc, $bookingid);
            $query_update_cal = "UPDATE `mrbs_entry` SET `patientstatus` = '$follow_up_call_status' WHERE `id` = '$calid'";
            $result_update_cal = mysqli_query($dbc, $query_update_cal);

			$query_invoice = "UPDATE `invoice` SET `payment_type` = $payment_type, `paid` = 'Yes' WHERE `invoiceid` = '$invoiceid'";
			$result_invoice = mysqli_query($dbc, $query_invoice);

			$invoice = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT patientid, fee, sell_price,total_price FROM invoice WHERE invoiceid='$invoiceid'"));
			$patientid = $invoice['patientid'];
			$total_price = $invoice['total_price'];
			//$sell_price = $invoice['sell_price'];

			$query_update_patient = "UPDATE `patients` SET `account_balance` = account_balance - '$total_price' WHERE `patientid` = '$patientid'";
			$result_update_patient = mysqli_query($dbc, $query_update_patient);
		}
		if ($from == 'patient') {
            echo '<script type="text/javascript"> alert("Invoice Successfully Paid."); window.location.replace("index.php?tab=today?patientid='.$patientid.'"); </script>';
		} else {
            echo '<script type="text/javascript"> alert("Invoice Successfully Paid."); window.location.replace("index.php?tab=today"); </script>';
		}
}

?>
<script type="text/javascript" src="../Invoice/invoice.js"></script>
<?php $ux_options = explode(',',get_config($dbc, FOLDER_NAME.'_ux'));
if(in_array('touch',$ux_options) && (!in_array('standard',$ux_options) || $_GET['ux'] == 'touch')) { ?>
	<script> debugger;window.location.replace('touch_main.php'); </script>
<?php } ?>

<style>
.pay-div { padding: 0; }
.preview_div { padding-right: 2em; }
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
    .wrapper { display:flex; flex-direction:column; }
	.preview_div { position:initial; order:2; }
    .main-div { order:1; }
    .control-div { margin-top:30px; order:3; }
}
</style>
</head>

<body>
<?php include_once ('../navigation.php');

?>
<div class="container">
	<div class="iframe_overlay" style="display:none; margin-top:-20px; padding-bottom:20px;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="edit_board" src=""></iframe>
		</div>
	</div>
    <div class="row">

		<?php // if(empty($_GET['action'])) { ?>

        <div class="col-sm-9"><h1 class="triple-pad-bottom"><?= (empty($current_tile_name) ? 'Check Out' : $current_tile_name) ?>
		<a href="" onclick="$('#save').click(); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/save.png" height="32" width="32" title="Save Invoice" class="pull-right override-theme-color-icon"></a></h1></div>
        <?php if(config_visible_function($dbc, (FOLDER_NAME == 'posadvanced' ? 'posadvanced' : 'check_out')) == 1) {
            echo '<a href="field_config_invoice.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
        } ?>

		<div class="clearfix"></div>

		<?php include('tile_tabs.php'); ?><br /><br />

		<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
        <?php $invoice_type = '';
        if(!empty($_GET['type'])) {
            $invoice_type = $_GET['type'];
        }
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

            $invoice_type = $get_invoice['type'];

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

			if($bookingid != 0) {
				$service_date = explode(' ', get_patient_from_booking($dbc, $bookingid, 'appoint_date'))[0];
			}

            $type = get_patient_from_booking($dbc, $bookingid, 'type');
            $app_type = get_type_from_booking($dbc, $type);

            $total_injury = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_injury FROM booking WHERE injuryid='$injuryid' AND (follow_up_call_status = 'Arrived' OR follow_up_call_status='Completed' OR follow_up_call_status = 'Paid' OR follow_up_call_status = 'Invoiced')"));

            $treatment_plan = get_all_from_injury($dbc, $injuryid, 'treatment_plan');
            $final_treatment_done = '';
            //if($treatment_plan != '') {
                $final_treatment_done = ' : '.($total_injury['total_injury']).'/'.$treatment_plan;
            //}

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
            $service_ticketid =$get_invoice['service_ticketid'];
            $fee =$get_invoice['fee'];
            $inventoryid =$get_invoice['inventoryid'];
            $sell_price =$get_invoice['sell_price'];
            $invtype =$get_invoice['invtype'];
            $quantity =$get_invoice['quantity'];
            $packageid =$get_invoice['packageid'];
            $package_cost =$get_invoice['package_cost'];
            $misc_items =$get_invoice['misc_item'];
            $misc_ticketid =$get_invoice['misc_ticketid'];
            $misc_prices =$get_invoice['misc_price'];
            $misc_qtys =$get_invoice['misc_qty'];

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
            $gratuity = $get_invoice['gratuity'];
            //echo '<input type="hidden" name="ticketid[]" value="'.$get_invoice['ticketid'].'" />';
        } else {
            echo '<input type="hidden" name="set_promotion" id="set_promotion" />';
        }

				echo '<input type="hidden" name="set_gf" id="set_gf" />';

        echo '<input type="hidden" id="paid_notpaid" name="paid_notpaid" value="'.$paid.'" />';


        $field_config = explode(',',get_config($dbc, 'invoice_fields'));
        if(!empty($invoice_type) && !empty(get_config($dbc, 'invoice_fields_'.$invoice_type))) {
            $field_config = explode(',',get_config($dbc, 'invoice_fields_'.$invoice_type));
        }
        ?>

		<div class="wrapper">
        <div class="col-sm-3 preview_div">
			<h3>Details</h3>
			<h4 <?= (in_array('invoice_date',$field_config) ? '' : 'style="display:none;"') ?>>Invoice Date: <label class="detail_invoice_date pull-right"><?= date('Y-m-d') ?></label></h4>
			<h4 <?= (in_array('customer',$field_config) ? '' : 'style="display:none;"') ?>><?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?>: <label class="detail_patient_name pull-right"><?= (empty($_GET['invoiceid']) ? get_contact($dbc, $_GET['contactid']) : $patient) ?></label></h4>
			<h4 <?= (in_array('injury',$field_config) ? '' : 'style="display:none;"') ?>>Injury: <label class="detail_patient_injury pull-right"><?= (empty($_GET['invoiceid']) ? '' : $injury) ?></label></h4>
			<h4 <?= (in_array('treatment',$field_config) ? '' : 'style="display:none;"') ?>>Treatment Plan: <label class="detail_patient_treatment pull-right"><?= (empty($_GET['invoiceid']) ? '' : $treatment_plan) ?></label></h4>
			<h4 <?= (in_array('staff',$field_config) ? '' : 'style="display:none;"') ?>>Staff: <label class="detail_staff_name pull-right"><?= (empty($_GET['invoiceid']) ? '' : $staff) ?></label></h4>
			<h4 <?= (in_array('services',$field_config) || in_array('unbilled_tickets',$field_config) ? '' : 'style="display:none;"') ?>>Services</h4>
			<div class="detail_service_list" <?= (in_array('services',$field_config) || in_array('unbilled_tickets',$field_config) ? '' : 'style="display:none;"') ?>></div>
			<h4 <?= (in_array('inventory',$field_config) ? '' : 'style="display:none;"') ?>>Inventory</h4>
			<div class="detail_inventory_list" <?= (in_array('inventory',$field_config) ? '' : 'style="display:none;"') ?>></div>
			<h4 <?= (in_array('products',$field_config) ? '' : 'style="display:none;"') ?>>Products</h4>
			<div class="detail_products_list" <?= (in_array('products',$field_config) ? '' : 'style="display:none;"') ?>></div>
			<h4 <?= (in_array('packages',$field_config) ? '' : 'style="display:none;"') ?>>Packages</h4>
			<div class="detail_package_list" <?= (in_array('packages',$field_config) ? '' : 'style="display:none;"') ?>></div>
			<h4 <?= (in_array('misc_items',$field_config) || in_array('unbilled_tickets',$field_config) ? '' : 'style="display:none;"') ?>>Miscellaneous Items</h4>
			<div class="detail_misc_list" <?= (in_array('misc_items',$field_config) || in_array('unbilled_tickets',$field_config) ? '' : 'style="display:none;"') ?>></div>
			<h4>Sub-Total: <label class="detail_sub_total_amt pull-right">$0.00</label></h4>
			<h4 <?= (in_array('promo',$field_config) ? '' : 'style="display:none;"') ?>>Promotion: <label class="detail_promo_amt pull-right"><?= $promotionid > 0 ? '' : 'N/A' ?></label></h4>
            <h4 <?= (in_array('discount',$field_config) ? '' : 'style="display:none;"') ?>>Discount: <label class="detail_discount_amt pull-right">$0.00</label></h4>
            <h4>Sub-Total after Discount: <label class="detail_sub_total_after_discount pull-right">$0.00</label></h4>
			<h4 <?= (in_array('delivery',$field_config) ? '' : 'style="display:none;"') ?>>Delivery: <label class="detail_shipping_amt pull-right">$0.00</label></h4>
            <h4 <?= (in_array('assembly',$field_config) ? '' : 'style="display:none;"') ?>>Assembly: <label class="detail_assembly_amt pull-right">$0.00</label></h4>
			<h4>Total before Tax: <label class="detail_mid_total_amt pull-right">$0.00</label></h4>
			<h4>GST: <label class="detail_gst_amt pull-right">$0.00</label></h4>
			<h4 <?= (in_array('tips',$field_config) ? '' : 'style="display:none;"') ?>>Gratuity: <label class="detail_gratuity_amt pull-right">$0.00</label></h4>
			<h4 <?= (in_array('gf',$field_config) ? '' : 'style="display:none;"') ?>>Gift Card Value: <label class="detail_gf_amt pull-right"><span id="detail_gift_amount">N/A</span></label></h4>
			<h4 style="display:none;">Credit to Account: <label class="detail_credit_balance pull-right">$0.00</label></h4>
			<h4>Total: <label class="detail_total_amt pull-right">$0.00</label></h4>
			<h4 style="display:none;"><?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> Portion: <label class="detail_insurer_amt pull-right">$0.00</label></h4>
			<h4 style="display:none;"><?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?> Portion: <label class="detail_patient_amt pull-right">$0.00</label></h4>
		</div>

        <div class="main-div">
        <?php $invoice_types = array_filter(explode(',',get_config($dbc, 'invoice_types')));
        if(!empty($invoice_types)) { ?>
            <div class="form-group">
                <label class="col-sm-2 control-label">Invoice Type:</label>
                <div class="col-sm-7">
                    <select name="type" class="chosen-select-deselect form-control">
                        <option></option>
                        <?php foreach($invoice_types as $invoice_type_dropdown) {
                            echo '<option value="'.config_safe_str($invoice_type_dropdown).'" '.($invoice_type == config_safe_str($invoice_type_dropdown) ? 'selected' : '').'>'.$invoice_type_dropdown.'</option>';

                        } ?>
                    </select>
                </div>
            </div>
        <?php } ?>

		<div class="form-group" <?= (in_array('invoice_date',$field_config) ? '' : 'style="display:none;"') ?>>
			<label for="site_name" class="col-sm-2 control-label">Invoice Date:</label>
			<div class="col-sm-7">
				<input type="text" readonly value="<?= date('Y-m-d'); ?>" class="form-control">
			</div>
		</div>

          <?php if(!empty($_GET['invoiceid'])) { ?>
          <div class="form-group" <?= (in_array('customer',$field_config) ? '' : 'style="display:none;"') ?>>
            <label for="site_name" class="col-sm-2 control-label"><?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?>:</label>
            <div class="col-sm-7">
                <?php echo $patient; ?>
            </div>
          </div>

          <?php } else { ?>

			<div class="form-group" <?= (in_array('invoice_type',$field_config) ? '' : 'style="display:none;"') ?>>
				<label for="site_name" class="col-sm-2 control-label">Type:</label>
				<div class="col-sm-7">
				  <div class="radio">
                    <span class="popover-examples list-inline">
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="All patients who have a profile in the software."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                    </span>
					<label class="pad-right">
                    <input type="radio" checked name="invoice_type" class="patient_type" value="Patient"><?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?></label>
                    <span class="popover-examples list-inline">
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Non-<?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?>s making a purchase."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                    </span>
					<label class="pad-right"><input type="radio" name="invoice_type" value="Non Patient" class="patient_type">Non <?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?></label>
				  </div>
				</div>
			</div>

          <div class="form-group non_patient_fields">
            <label for="site_name" class="col-sm-2 control-label">First Name<span class="hp-red">*</span>:</label>
            <div class="col-sm-7">
              <input name="first_name" type="text" class="form-control" />
            </div>
          </div>
          <div class="form-group non_patient_fields">
            <label for="site_name" class="col-sm-2 control-label">Last Name<span class="hp-red">*</span>:</label>
            <div class="col-sm-7">
              <input name="last_name" type="text" class="form-control" />
            </div>
          </div>
          <div class="form-group non_patient_fields">
            <label for="site_name" class="col-sm-2 control-label">Email:</label>
            <div class="col-sm-7">
              <input name="email" type="text" class="form-control" />
            </div>
          </div>

              <div class="form-group patient patient_type_fields" <?= (in_array('customer',$field_config) ? '' : 'style="display:none;"') ?>>
                <label for="site_name" class="col-sm-2 control-label">
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select a Customer's name."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
                <?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?><span class="hp-red">*</span>:</label>
                <div class="col-sm-7">
                    <!--
                    <select id="patientid" data-placeholder="Select a Customer..." name="patientid" class="chosen-select-deselect form-control" width="380">
                        <option value=""></option>
                        <option value="NEW">Add New <?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?></option>
                        <?php
                        /*
                        $query = mysqli_query($dbc,"SELECT contactid, name, first_name, last_name FROM contacts WHERE category IN ('".implode("','",$purchaser_config)."') AND status>0 AND deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            if ($_GET['contactid'] == $row['contactid']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            //echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['name']).($row['last_name'] != '' && $row['first_name'] != '' && $row['name'] != '' ? ': '.decryptIt($row['first_name']).' '.decryptIt($row['last_name']) : '').'</option>'; ?>
                            <option <?= $selected; ?> value=<?= $row['contactid']; ?>><?php
                                if ( !empty($row['name']) ) {
                                    echo decryptIt($row['name']) . ': ';
                                }
                                if ($row['last_name'] != '' && $row['first_name'] != '') {
                                    echo decryptIt($row['first_name']).' '.decryptIt($row['last_name']);
                                } else {
                                    '';
                                } ?>
                            </option><?php
                        }
                        */
                        ?>
                    </select>
                    -->
                    <select id="patientid" data-placeholder="Select a Customer..." name="patientid" class="chosen-select-deselect form-control" width="380">
                        <option value=""></option>
                        <option value="NEW">Add New <?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?></option>
                        <?php
                            $query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name FROM contacts WHERE category IN ('".implode("','",$purchaser_config)."') AND status>0 AND deleted=0"));
                            foreach($query as $contact) {
                                $selected = '';
                                $selected = $contact['contactid'] == $_GET['contactid'] ? 'selected="selected"' : '';
                                echo "<option ".$selected." value='".$contact['contactid']."'>".$contact['full_name'].'</option>';
                            }
                        ?>
                    </select>
                </div>
              </div>

          <?php } ?>

        <div class="form-group patient  <?= (in_array('reference',$field_config) ? 'reference' : '" style="display:none;') ?>">
            <label for="site_name" class="col-sm-2 control-label">Reference:</label>
            <div class="col-sm-7"><input type="text" name="reference" class="form-control" /></div>
        </div>

        <div class="form-group patient  <?= (in_array('contract',$field_config) ? 'reference' : '" style="display:none;') ?>">
            <label for="site_name" class="col-sm-2 control-label">Contract:</label>
            <div class="col-sm-7"><input type="text" name="contract" class="form-control" /></div>
        </div>

        <div class="form-group patient  <?= (in_array('po_num',$field_config) ? 'reference' : '" style="display:none;') ?>">
            <label for="site_name" class="col-sm-2 control-label">PO #:</label>
            <div class="col-sm-7"><input type="text" name="po_num" class="form-control" /></div>
        </div>

        <div class="form-group patient  <?= (in_array('area',$field_config) ? 'reference' : '" style="display:none;') ?>">
            <label for="site_name" class="col-sm-2 control-label">Area:</label>
            <div class="col-sm-7"><input type="text" name="area" class="form-control" /></div>
        </div>

              <div class="form-group patient  <?= (in_array('injury',$field_config) ? 'patient_type_fields' : '" style="display:none;') ?>">
                <label for="site_name" class="col-sm-2 control-label">
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the injury."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
                Injury<span class="hp-red">*</span>:</label>
                <div class="col-sm-7">
                    <select id="injuryid" data-placeholder="Select an Injury..." name="injuryid" class="chosen-select-deselect form-control" width="380">
                        <option value=""></option>
                        <?php
                        $pid = $_GET['contactid'];
                        $query = mysqli_query($dbc,"SELECT contactid, injuryid, injury_name, injury_date, injury_type, treatment_plan FROM patient_injury WHERE contactid='$pid' AND discharge_date IS NULL AND deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            $injuryid = $row['injuryid'];
                            $total_injury = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(bookingid) AS total_injury FROM booking WHERE injuryid='$injuryid'"));

                            $treatment_plan = get_all_from_injury($dbc, $injuryid, 'treatment_plan');
                            $final_treatment_done = '';
                            if($treatment_plan != '') {
                                $final_treatment_done = ' : '.($total_injury['total_injury']+1).'/'.$treatment_plan;
                            }

                            echo "<option ".($get_invoice['injuryid'] == $injuryid ? 'selected' : '')." value='". $row['injuryid']."'>".$row['injury_type'].' : '.$row['injury_name']. ' : '.$row['injury_date'].$final_treatment_done.'</option>';
                        }
                        ?>
                    </select>
                </div>
              </div>

           <div class="form-group treatment_plan" <?= (in_array('treatment',$field_config) ? '' : 'style="display:none;"') ?>>
            <label for="site_name" class="col-sm-2 control-label">Treatment Plan:</label>
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
                <label for="site_name" class="col-sm-2 control-label">
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the staff providing treatment."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
                Staff:</label>
                <div class="col-sm-7">
                    <select id="therapistsid" data-placeholder="Select Staff..." name="therapistsid" class="chosen-select-deselect form-control" width="380">
                        <option value=""></option>
                        <?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0"),MYSQLI_ASSOC));
						foreach($query as $row) {
                            echo "<option ".($get_invoice['therapistsid'] == $row ? 'selected' : '')." value='". $row."'>".get_contact($dbc, $row).'</option>';
                        } ?>
                    </select>
                </div>
              </div>

			  <div class="form-group" <?= (in_array('appt_type',$field_config) ? '' : 'style="display:none;"') ?>>
				<label for="site_name" class="col-sm-2 control-label">Appointment Type:</label>
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
				<div class="col-sm-7">
					<input type="text" name="service_date" class="form-control datepicker" value="<?= $service_date ?>">
				</div>
			  </div>

			  <div class="form-group" <?= (in_array('pricing',$field_config) ? '' : 'style="display:none;"') ?>>
				<label for="site_name" class="col-sm-2 control-label">Product Pricing:</label>
				<div class="col-sm-7">
					<select name="pricing" data-placeholder="Select Pricing" class="chosen-select-deselect"><option></option>
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

			<div class="form-group" <?= (in_array('pay_mode',$field_config) ? '' : 'style="display:none;"') ?>>
				<label for="site_name" class="col-sm-2 control-label">
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select payment method (you must select one in order to move on)."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
                Payment Method:</label>
				<div class="col-sm-7">
                    <select data-placeholder="Select a Type..." name="paid" id="paid_status" class="chosen-select-deselect form-control" width="480">
                        <option value=""></option>
                        <!--<option <?php if ($paid=='Saved') echo 'selected="selected"';?>  value="Saved">Save Invoice</option>-->
                        <option <?php if ($paid=='Yes') echo 'selected="selected"';?>  value="Yes">Patient Invoice : Patient is paying full amount on checkout.</option>
                        <option <?php if ($paid=='Waiting on Insurer') echo 'selected="selected"';?> value="Waiting on Insurer">Waiting on <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> : Clinic is waiting on <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> to pay full amount.</option>
                        <option <?php if ($paid=='No') echo 'selected="selected"';?>  value="No">Partially Paid : The invoice is being paid partially by patient and partially by <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?>.</option>
                        <option <?php if ($paid=='On Account') echo 'selected="selected"';?> value="On Account">A/R On Account : Patient will pay invoice in future. Must choose Payment Type as Apply A/R to Account.</option>
                        <option <?php if ($paid=='Credit On Account') echo 'selected="selected"';?> value="Credit On Account">Credit On Account : Patient is appyling credit to profile.</option>
                    </select>

                    <!--
				  <div class="radio">

                    <span class="popover-examples list-inline">
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Save Invoice"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                    </span>
					<label class="pad-right"><input type="radio" name="paid" value="Saved" <?php if($paid == 'Saved') { echo "checked"; } ?> >Save Invoice</label>

                    <span class="popover-examples list-inline">
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Patient is paying full amount on checkout."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                    </span>
					<label class="pad-right"><input type="radio" name="paid" value="Yes" <?php if($paid == 'Yes') { echo "checked"; } ?> >Patient Invoice</label>

                    <span class="popover-examples list-inline">
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Clinic is waiting on insurer to pay full amount."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                    </span>
					<label class="pad-right"><input type="radio" name="paid" value="Waiting on Insurer" <?php if($paid == 'Waiting on Insurer') { echo "checked"; } ?>>Waiting on Insurer</label>

                    <span class="popover-examples list-inline">
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="The invoice is being paid partially by patient and partially by insurer."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                    </span>
					<label class="pad-right"><input type="radio" name="paid" value="No" <?php if($paid == 'No') { echo "checked"; } ?> >Partially Paid</label>

                    <span class="popover-examples list-inline">
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Patient will pay invoice in future. Must choose Payment Type as Apply A/R to Account."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                    </span>
					<label class="pad-right"><input type="radio" name="paid" value="On Account" <?php if($paid == 'On Account') { echo "checked"; } ?>>A/R On Account</label>

                    <span class="popover-examples list-inline">
                        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Patient is appyling credit to profile."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                    </span>
					<label class="pad-right"><input type="radio" name="paid" value="Credit On Account" <?php if($paid == 'Credit On Account') { echo "checked"; } ?>>Credit On Account</label>
				  </div>
                  -->

				</div>
			</div>

            <div class="form-group service_option" <?= (in_array('services',$field_config) ? '' : 'style="display:none;"') ?>>
                <label for="additional_note" class="col-sm-2 control-label">
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the service."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
                Services:</label>
                <div class="col-sm-7">
                    <div class="form-group clearfix hide-titles-mob">
                        <label class="col-sm-4 text-center">Category</label>
                        <label class="col-sm-5 text-center">Service Name</label>
                        <label class="col-sm-2 text-center">Fee</label>
                    </div>

				    <?php
				    if(!empty($_GET['invoiceid'])) {

                    if($serviceid != '') {
                        $each_serviceid = explode(',',$serviceid);
                        $each_serviceticketid = explode(',',$service_ticketid);
                        $each_fee = explode(',',$fee);
                        $total_count = mb_substr_count($serviceid,',');
                        $id_loop = 500;

                        for($client_loop=0; $client_loop<=$total_count; $client_loop++) {
                            if($each_serviceid[$client_loop] != '' && !($each_serviceticketid[$client_loop] > 0)) {
                                $serviceid = $each_serviceid[$client_loop];
                                $fee = $each_fee[$client_loop];
                                ?>

							<div class="form-group clearfix">
							    <div class="col-sm-4"><label class="show-on-mob">Service Category:</label>
                                    <select data-placeholder="Select a Category..." id="<?php echo 'category_'.$id_loop; ?>" class="chosen-select-deselect form-control service_category_onchange" width="380">
                                        <option value=""></option>
                                        <?php
                                        if($app_type == '') {
                                            $query = mysqli_query($dbc,"SELECT category, GROUP_CONCAT(DISTINCT(appointment_type)) appointment_type FROM services WHERE deleted=0 GROUP BY `category`");
                                        } else {
                                            $query = mysqli_query($dbc,"SELECT category, GROUP_CONCAT(DISTINCT(appointment_type)) appointment_type FROM services WHERE deleted=0 AND (appointment_type = '' OR appointment_type='$type') GROUP BY `category`");
                                        }
                                        while($row = mysqli_fetch_array($query)) {
                                            if (get_all_from_service($dbc, $serviceid, 'category') == $row['category']) {
                                                $selected = 'selected="selected"';
                                            } else {
                                                $selected = '';
                                            }
                                            echo "<option data-appt-type=',".$row['appointment_type'].",' ".$selected." value='". $row['category']."'>".$row['category'].'</option>';
                                        }
                                        ?>
                                    </select>
							    </div> <!-- Quantity -->

                                <div class="col-sm-5"><label class="show-on-mob">Service Name:</label>
                                    <input type="hidden" name="service_ticketid[]" value="">
                                    <select id="<?php echo 'serviceid_'.$id_loop; ?>" data-placeholder="Select a Service..." name="serviceid[]" class="chosen-select-deselect form-control serviceid" width="380">
                                        <option value=""></option>
                                        <?php
                                        //$query = mysqli_query($dbc,"SELECT serviceid, category, service_type, fee FROM services WHERE deleted=0 AND (appointment_type = '' OR appointment_type='$type')");
                                        $db_category = get_all_from_service($dbc, $serviceid, 'category');
                                        if($app_type == '') {
                                            //$query = mysqli_query($dbc,"SELECT serviceid, category, heading, fee FROM services WHERE deleted=0 AND category='$db_category'");

                                            $query = mysqli_query($dbc,"SELECT s.serviceid, s.heading, r.cust_price service_rate, s.appointment_type, r.editable FROM services s,  company_rate_card r WHERE s.category='$db_category' AND s.serviceid = r.item_id AND r.tile_name LIKE 'Services' AND '$invoice_date' >= r.start_date AND ('$invoice_date' <= r.end_date OR IFNULL(r.end_date,'0000-00-00') = '0000-00-00')");
                                        } else {
                                            //$query = mysqli_query($dbc,"SELECT serviceid, category, heading, fee FROM services WHERE deleted=0 AND (appointment_type = '' OR appointment_type='$type')");

                                            $query = mysqli_query($dbc,"SELECT s.serviceid, s.heading, r.cust_price `service_rate`, s.appointment_type, r.editable FROM services s,  company_rate_card r WHERE (s.appointment_type = '' OR s.appointment_type='$type') AND s.serviceid = r.item_id AND `tile_name` LIKE 'Services' AND '$invoice_date' >= r.start_date AND ('$invoice_date' <= r.end_date OR IFNULL(r.end_date,'0000-00-00') = '0000-00-00')");
                                        }
										$fee_editable = false;
                                        //$query = mysqli_query($dbc,"SELECT distinct(category) FROM services WHERE deleted=0");
                                        while($row = mysqli_fetch_array($query)) {
                                            if ($serviceid == $row['serviceid']) {
                                                $selected = 'selected="selected"';
												if($row['editable'] > 0) {
													$fee_editable = true;
												}
                                            } else {
                                                $selected = '';
                                            }
                                            echo "<option data-editable='".$row['editable']."' data-appt-type=',".$row['appointment_type'].",' ".$selected." value='". $row['serviceid']."'>".$row['heading'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-sm-2"><label class="show-on-mob">Total Fee:</label>
									<input name="fee[]" <?= $fee_editable ? '' : 'readonly' ?> id="<?php echo 'fee_'.$id_loop; ?>"  type="number" step="any" value="<?php echo $fee; ?>" class="form-control fee" />
									<input name="gst_exempt[]" id="<?php echo 'gstexempt_'.$id_loop; ?>"  type="hidden" value="<?php echo get_all_from_service($dbc, $serviceid, 'gst_exempt'); ?>" class="form-control gstexempt" />
									<input name="service_row_id[]" type="hidden" value="<?= $insurer_row_id++ ?>" class="insurer_row_id" />
                                </div>

                                <div class="col-sm-1">
									<img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_service_row(this);">
									<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_service_row();">
                                </div>

                                <div class="col-sm-12 pay-div"></div>
                            </div>
                                <?php
                                $id_loop++;
                            }
                        }
                    }
                    ?>

                    <?php } ?>

					<div class="additional_service form-group clearfix">

						<div class="col-sm-4"><label class="show-on-mob">Service Category:</label>
							<select data-placeholder="Select a Category..." id="category_0" class="chosen-select-deselect form-control service_category_onchange" width="380">
								<option value=""></option>
								<?php
								if((!empty($_GET['invoiceid'])) && ($type != '')) {
									$query = mysqli_query($dbc,"SELECT category, GROUP_CONCAT(DISTINCT(appointment_type)) appointment_type FROM services WHERE deleted=0 AND (appointment_type = '' OR appointment_type='$type') GROUP BY `category`");
								} else {
									$query = mysqli_query($dbc,"SELECT category, GROUP_CONCAT(DISTINCT(appointment_type)) appointment_type FROM services WHERE deleted=0 GROUP BY `category`");
								}
								while($row = mysqli_fetch_array($query)) {
									echo "<option data-appt-type=',".$row['appointment_type'].",' value='". $row['category']."'>".$row['category'].'</option>';
								}
								?>
							</select>
						</div>
						<div class="col-sm-5"><label class="show-on-mob">Service Name:</label>
                            <input type="hidden" name="service_ticketid[]" value="">
							<select id="serviceid_0" data-placeholder="Select a Service..." name="serviceid[]" class="chosen-select-deselect form-control serviceid" width="380">
								<option value=""></option>
								<?php
								/*
								$query = mysqli_query($dbc,"SELECT serviceid, category, heading, fee FROM services WHERE deleted=0");
								while($row = mysqli_fetch_array($query)) {
									echo "<option value='". $row['serviceid']."'>".$row['category'].' : '.$row['heading']. ' : '.$row['fee'].'</option>';
								}
								*/
								?>
							</select>
						</div>
						<div class="col-sm-2"><label class="show-on-mob">Total Fee:</label>
							<input name="fee[]" readonly id="fee_0" type="number" step="any" value=0 class="form-control fee" />
							<input name="gst_exempt[]" id="gstexempt_0"  type="hidden" value="0" class="form-control gstexempt" />
							<input name="service_row_id[]" type="hidden" value="<?= $insurer_row_id++ ?>" class="insurer_row_id" />
						</div>

						<div class="col-sm-1">
							<img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_service_row(this);">
							<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_service_row();">
						</div>

						<div class="col-sm-12 pay-div"></div>
					</div>

                    <div id="add_here_new_service"></div>

                    <!--<div class="form-group triple-gapped clearfix">
                        <div class="col-sm-offset-4 col-sm-8">
                            <button id="add_row_service" class="btn brand-btn pull-left">Add Service</button>
                        </div>
                    </div>-->

                </div>
            </div>

            <div class="form-group product_option" <?= (in_array('inventory',$field_config) ? '' : 'style="display:none;"') ?>>
                <label for="additional_note" class="col-sm-2 control-label">
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select any products from inventory here."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
                Inventory:<?php echo (in_array('injury', $field_config) ? '<br>MVA Claim Price:' : '');
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
				if(in_array('inventory_cat',$field_config) && in_array('inventory_part',$field_config) && in_array('inventory_type',$field_config) && in_array('inventory_price',$field_config)) {
					$col1 = $col2 = $col3 = $col4 = 2;
				} else if(in_array('inventory_cat',$field_config) && in_array('inventory_part',$field_config) && in_array('inventory_type',$field_config)) {
					$col1 = $col2 = $col3 = $col4 = 2;
					$col5 = 0;
				} else if(in_array('inventory_cat',$field_config) && in_array('inventory_part',$field_config) && in_array('inventory_price',$field_config)) {
					$col1 = 1;
					$col2 = $col3 = $col5 = 2;
					$col4 = 0;
				} else if(in_array('inventory_cat',$field_config) && in_array('inventory_type',$field_config) && in_array('inventory_price',$field_config)) {
					$col1 = $col3 = $col4 = $col5 = 2;
					$col2 = 0;
				} else if(in_array('inventory_part',$field_config) && in_array('inventory_type',$field_config) && in_array('inventory_price',$field_config)) {
					$col2 = $col3 = $col4 = $col5 = 2;
					$col1 = 0;
				} else if(in_array('inventory_cat',$field_config) && in_array('inventory_part',$field_config)) {
					$col1 = $col3 = 3;
					$col2 = 2;
					$col4 = $col5 = 0;
				} else if(in_array('inventory_cat',$field_config) && in_array('inventory_type',$field_config)) {
					$col1 = $col3 = 3;
					$col4 = 2;
					$col2 = $col5 = 0;
				} else if(in_array('inventory_cat',$field_config) && in_array('inventory_price',$field_config)) {
					//$col1 = $col3 = 3;
					$col3 = 3;
					$col5 = 2;
					$col2 = $col4 = 0;
				} else if(in_array('inventory_part',$field_config) && in_array('inventory_type',$field_config)) {
					$col2 = $col3 = 3;
					$col4 = 2;
					$col1 = $col5 = 0;
				} else if(in_array('inventory_part',$field_config) && in_array('inventory_price',$field_config)) {
					$col2 = $col3 = 3;
					$col5 = 2;
					$col1 = $col4 = 0;
				} else if(in_array('inventory_type',$field_config) && in_array('inventory_price',$field_config)) {
					$col3 = $col4 = 3;
					$col5 = 2;
					$col1 = $col2 = 0;
				} else if(in_array('inventory_cat',$field_config)) {
					$col1 = $col3 = 4;
					$col2 = $col4 = $col5 = 0;
				} else if(in_array('inventory_part',$field_config)) {
					$col2 = $col3 = 4;
					$col1 = $col4 = $col5 = 0;
				} else if(in_array('inventory_type',$field_config)) {
					$col3 = 5;
					$col4 = 3;
					$col1 = $col2 = $col5 = 0;
				} else if(in_array('inventory_price',$field_config)) {
					$col3 = 6;
					$col5 = 2;
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
                        <?php if(in_array('inventory_cat',$field_config)) { ?><label class="col-sm-<?= $col1 ?> text-center">Category</label><?php } ?>
                        <?php if(in_array('inventory_part',$field_config)) { ?><label class="col-sm-<?= $col2 ?> text-center">Part #</label><?php } ?>
                        <label class="col-sm-<?= $col3 ?> text-center">Name</label>
                        <?php if(in_array('inventory_type',$field_config)) { ?><label class="col-sm-<?= $col4 ?> text-center">Type</label><?php } ?>
                        <?php if(in_array('inventory_price',$field_config)) { ?><label class="col-sm-<?= $col5 ?> text-center">Price</label><?php } ?>
                        <label class="col-sm-<?= $col6 ?> text-center">Qty</label>
                        <label class="col-sm-<?= $col7 ?> text-center">Total</label>
                    </div>

				    <?php
				    if(!empty($_GET['invoiceid'])) {

                    if($inventoryid != '') {

                        $each_inventoryid = explode(',',$inventoryid);
                        $each_sell_price = explode(',',$sell_price);
                        $each_invtype = explode(',',$invtype);
                        $each_quantity = explode(',',$quantity);

                        $total_count = mb_substr_count($inventoryid,',');
                        $id_loop = 500;

                        for($client_loop=0; $client_loop<=$total_count; $client_loop++) {
                            if($each_inventoryid[$client_loop] != '') {
                                $inventoryid = $each_inventoryid[$client_loop];
                                $sell_price = $each_sell_price[$client_loop];
                                $invtype = $each_invtype[$client_loop];
                                $quantity = $each_quantity[$client_loop];
                                $inv_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `part_no`, `final_retail_price`, `wcb_price`, `client_price`, `web_price`, `purchase_order_price`, `sales_order_price`, `admin_price`, `wholesale_price`, `commercial_price`, `preferred_price`, `gst_exempt` FROM `inventory` WHERE `inventoryid`='$inventoryid'"));
								$gst_exempt = $inv_info['gst_exempt'];
                                ?>

                                <div class="additional_product form-group clearfix">
                                    <div class="col-sm-<?= $col1 ?>" <?= (in_array('inventory_cat',$field_config) ? '' : 'style="display:none;"') ?>><label class="show-on-mob">Inventory Category:</label>
                                        <select data-placeholder="Select Category..." id="<?php echo 'inventorycat_'.$id_loop; ?>" name="inventorycat[]" class="chosen-select-deselect form-control inventorycat" width="380">
                                            <option></option>
                                            <?php $query = mysqli_query($dbc,"SELECT `category` FROM inventory WHERE deleted=0 GROUP BY `category` ORDER BY `category`");
                                            while($row = mysqli_fetch_array($query)) {
                                                echo "<option ".($row['category'] == $inv_info['category'] ? 'selected' : '')." value='". $row['category']."'>".$row['category'].'</option>';
                                            } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-<?= $col2 ?>" <?= (in_array('inventory_part',$field_config) ? '' : 'style="display:none;"') ?>><label class="show-on-mob">Inventory Part #:</label>
                                        <select data-placeholder="Select Part #..." id="<?php echo 'inventorypart_'.$id_loop; ?>" name="inventorypart[]" class="chosen-select-deselect form-control inventorypart" width="380">
                                            <option value=""></option>
                                            <?php foreach($inventory_list as $row) {
												if(in_array($inv_info['category'],['',$row['category']])) {
													echo "<option data-category='".$row['category']."' ".($row['part_no'] == $inv_info['part_no'] ? 'selected' : '')." value='". $row['part_no']."'>".$row['part_no'].'</option>';
												}
											} ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-<?= $col3 ?>"><label class="show-on-mob">Inventory Name:</label>
                                        <select data-placeholder="Select Inventory..." id="<?php echo 'inventoryid_'.$id_loop; ?>" name="inventoryid[]" class="chosen-select-deselect form-control inventoryid" width="380">
                                            <option value=""></option>
                                            <?php foreach($inventory_list as $row) {
												if(in_array($inv_info['category'],['',$row['category']])) {
													echo "<option data-category='".$row['category']."' data-part='".$row['part_no']."' ".($row['inventoryid'] == $inventoryid ? 'selected' : '')." value='". $row['inventoryid']."'>".$row['name'].'</option>';
												}
											} ?>
                                        </select>
                                    </div> <!-- Quantity -->
                                    <div class="col-sm-<?= $col4 ?>" <?= (in_array('inventory_type',$field_config) ? '' : 'style="display:none;"') ?>><label class="show-on-mob">Type:</label>
                                        <select data-placeholder="Select a Type..." id="<?php echo 'invtype_'.$id_loop; ?>" name="invtype[]" class="chosen-select-deselect form-control invtype" width="480">
                                        <option <?= ($invtype == 'General' ? "selected" : '') ?> value="General">General</option>
                                        <option <?= ($invtype == 'WCB' ? "selected" : (strpos($injury_type,'WCB') === false && $injury_type != '' ? "disabled" : '')) ?> value="WCB">WCB</option>
                                        <option <?= ($invtype == 'MVA' ? "selected" : (strpos($injury_type,'MVA') === false && $injury_type != '' ? "disabled" : '')) ?> value="MVA">MVA</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-<?= $col5 ?>" <?= (in_array('inventory_price',$field_config) ? '' : 'style="display:none;"') ?>><label class="show-on-mob">Unit Price:</label>
                                        <input name="unit_price[]" id="<?php echo 'unitprice_'.$id_loop; ?>" value="<?php echo $sell_price / $quantity; ?>" type="number" step="any" readonly class="form-control invunitprice" />
                                    </div> <!-- Quantity -->
                                    <div class="col-sm-<?= $col6 ?>"><label class="show-on-mob">Quantity:</label>
                                        <input name="quantity[]" id="<?php echo 'quantity_'.$id_loop; ?>" onchange="changeProduct($('#inventoryid_'+this.id.split('_')[1]).get(0));" value="<?php echo $quantity; ?>" type="number" min="0" step="any" class="form-control quantity" />
                                    </div> <!-- Quantity -->
                                    <div class="col-sm-<?= $col7 ?>"><label class="show-on-mob">Total:</label>
                                        <input name="sell_price[]" id="<?php echo 'sellprice_'.$id_loop; ?>" onchange="countTotalPrice()" value="<?php echo $sell_price; ?>" type="number" step="any" readonly class="form-control sellprice" />
										<input name="inventory_row_id[]" type="hidden" value="<?= $insurer_row_id++ ?>" class="insurer_row_id" />
										<input name="inventory_gst_exempt[]" type="hidden" value="<?= $gst_exempt ?>" />
                                    </div>
									<div class="col-sm-1">
										<img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_product_row(this);">
										<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_product_row();">
									</div>
									<div class="col-sm-12 pay-div"></div>
                                </div>
                            <?php
                            $id_loop++;
                            }
                        }
                    }
                    }
                    ?>

					<div class="clearfix"></div>
					<div class="additional_product form-group clearfix">
						<div class="col-sm-<?= $col1 ?>" <?= (in_array('inventory_cat',$field_config) ? '' : 'style="display:none;"') ?>>
                            <label class="show-on-mob">Inventory Category:</label>
							<select data-placeholder="Select Category..." id="inventorycat_0" name="inventorycat[]" class="chosen-select-deselect form-control inventorycat" width="380">
								<option value=""></option>
								<?php $query = mysqli_query($dbc,"SELECT `category` FROM inventory WHERE deleted=0 GROUP BY `category` ORDER BY `category`");
								while($row = mysqli_fetch_array($query)) {
									echo "<option value='". $row['category']."'>".$row['category'].'</option>';
								} ?>
							</select>
						</div>
						<div class="col-sm-<?= $col2 ?>" <?= (in_array('inventory_part',$field_config) ? '' : 'style="display:none;"') ?>>
                            <label class="show-on-mob">Inventory Part #:</label>
							<select data-placeholder="Select Part #..." id="inventorypart_0" name="inventorypart[]" class="chosen-select-deselect form-control inventorypart" width="380">
								<option value=""></option>
								<?php $query = mysqli_query($dbc,"SELECT `category`, `part_no` FROM inventory WHERE deleted=0 ORDER BY `part_no`");
								while($row = mysqli_fetch_array($query)) {
									echo "<option data-category='".$row['category']."' value='". $row['part_no']."'>".$row['part_no'].'</option>';
								} ?>
							</select>
						</div>
						<div class="col-sm-<?= $col3 ?>">
                            <label class="show-on-mob">Inventory Name:</label>
							<select data-placeholder="Select Inventory..." id="inventoryid_0" name="inventoryid[]" class="chosen-select-deselect form-control inventoryid" width="380">
								<option value=""></option>
								<?php $query = mysqli_query($dbc,"SELECT `inventoryid`, `category`, `part_no`, `name` FROM inventory WHERE deleted=0 ORDER BY name");
								while($row = mysqli_fetch_array($query)) {
									echo "<option data-category='".$row['category']."' data-part='".$row['part_no']."' value='". $row['inventoryid']."'>".$row['name'].'</option>';
								} ?>
							</select>
						</div> <!-- Quantity -->
						<div class="col-sm-<?= $col4 ?>" <?= (in_array('inventory_type',$field_config) ? '' : 'style="display:none;"') ?>>
                            <label class="show-on-mob">Type:</label>
							<select data-placeholder="Select a Type..." id="invtype_0" name="invtype[]" class="chosen-select-deselect form-control invtype" width="480">
								<option value="General">General</option>
								<option <?= (strpos($injury_type,'WCB') === false && $injury_type != '' ? "disabled" : '') ?> value="WCB">WCB</option>
								<option <?= (strpos($injury_type,'MVA') === false && $injury_type != '' ? "disabled" : '') ?> value="MVA">MVA</option>
							</select>
						</div>
						<div class="col-sm-<?= $col5 ?>" <?= (in_array('inventory_price',$field_config) ? '' : 'style="display:none;"') ?>>
                            <label class="show-on-mob">Unit Price:</label>
							<input name="unit_price[]" id="unitprice_0" value="0" type="text" step="any" readonly class="form-control invunitprice" />
						</div>
						<div class="col-sm-<?= $col6 ?>">
                            <label class="show-on-mob">Quantity:</label>
							<input name="quantity[]" id="quantity_0" onchange="changeProduct($('#inventoryid_'+this.id.split('_')[1]).get(0));" value=1 type="number" min="0" step="any" class="form-control quantity" />
						</div>
						<div class="col-sm-<?= $col7 ?>"><label class="show-on-mob">Total:</label>
							<input name="sell_price[]" id="sellprice_0" onchange="countTotalPrice()" value="0" type="text" step="any" readonly class="form-control sellprice" />
							<input name="inventory_row_id[]" type="hidden" value="<?= $insurer_row_id++ ?>" class="insurer_row_id" />
							<input name="inventory_gst_exempt[]" type="hidden" value="0" />
						</div>
                        <div class="col-sm-<?= $col8 ?> col-pricing" <?= (in_array('pricing',$field_config) ? '' : 'style="display:none;"') ?>>
                            <img src="../img/icons/ROOK-edit-icon.png" alt="Edit Pricing" title="Edit Pricing" width="30" class="cursor-hand" onclick="$(this).hide(); $(this).closest('.col-pricing').find('.pricing-div').show();" />
                            <div class="pricing-div" style="display:none;">
                                <select data-placeholder="Select Pricing" id="linepricing_0" name="linepricing[]" class="chosen-select-deselect form-control linepricing" onchange="changeProduct($('#inventoryid_'+this.id.split('_')[1]).get(0));">
                                    <option></option>
                                    <?php if(in_array('price_admin', $field_config)) { ?><option value="admin_price">Admin Price</option><?php } ?>
                                    <?php if(in_array('price_client', $field_config)) { ?><option value="client_price">Client Price</option><?php } ?>
                                    <?php if(in_array('price_commercial', $field_config)) { ?><option value="commercial_price">Commercial Price</option><?php } ?>
                                    <?php if(in_array('price_distributor', $field_config)) { ?><option value="distributor_price">Distributor Price</option><?php } ?>
                                    <?php if(in_array('price_retail', $field_config)) { ?><option value="final_retail_price">Final Retail Price</option><?php } ?>
                                    <?php if(in_array('price_preferred', $field_config)) { ?><option value="preferred_price">Preferred Price</option><?php } ?>
                                    <?php if(in_array('price_po', $field_config)) { ?><option value="purchase_order_price">Purchase Order Price</option><?php } ?>
                                    <?php if(in_array('price_sales', $field_config)) { ?><option value="sales_order_price"><?= SALES_ORDER_NOUN ?> Price</option><?php } ?>
                                    <?php if(in_array('price_web', $field_config)) { ?><option value="web_price">Web Price</option><?php } ?>
                                    <?php if(in_array('price_wholesale', $field_config)) { ?><option value="wholesale_price">Wholesale Price</option><?php } ?>
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

                    <!--<div class="form-group triple-gapped clearfix">
                        <div class="col-sm-offset-4 col-sm-8">
                            <button id="add_row_product" class="btn brand-btn pull-left">Add Product</button>
                        </div>
                    </div>-->
                </div>
            </div>

            <div class="form-group package_option" <?= (in_array('packages',$field_config) ? '' : 'style="display:none;"') ?>>
                <label for="additional_note" class="col-sm-2 control-label">
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select any packages here."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
                Packages:</label>
                <div class="col-sm-7">
                    <div class="form-group clearfix hide-titles-mob">
                        <label class="col-sm-4 text-center">Category</label>
                        <label class="col-sm-5 text-center">Package Name</label>
                        <label class="col-sm-2 text-center">Fee</label>
                        <label class="col-sm-1 text-center"></label>
                    </div>

				    <?php $each_package = explode(',', $packageid);
					$each_package_cost = explode(',', $package_cost);
					foreach($each_package as $loop => $package) {
						$package_cost = $each_package_cost[$loop];
						$package_cat = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category` FROM `package` WHERE `packageid`='$package'"))['category']; ?>
						<div class="additional_package form-group clearfix">
							<div class="col-sm-4"><label class="show-on-mob">Package Category:</label>
								<select data-placeholder="Select Category..." id="<?php echo 'packagecat_'.$loop; ?>" name="packagecat[]" class="chosen-select-deselect form-control packagecat">
									<option value=""></option>
									<?php $query = mysqli_query($dbc,"SELECT `category` FROM `package` WHERE deleted=0 GROUP BY `category` ORDER BY `category`");
									while($row = mysqli_fetch_array($query)) {
										echo "<option ".($package_cat == $row['category'] ? 'selected' : '')." value='". $row['category']."'>".$row['category'].'</option>';
									} ?>
								</select>
							</div>
							<div class="col-sm-5"><label class="show-on-mob">Package Name:</label>
								<select data-placeholder="Select Package..." id="<?php echo 'packageid_'.$loop; ?>" name="packageid[]" class="chosen-select-deselect form-control packageid">
									<option value=""></option>
									<?php $query = mysqli_query($dbc,"SELECT `packageid`, `heading`, `category`, `cost` FROM `package` WHERE deleted=0 ORDER BY `heading`");
									while($row = mysqli_fetch_array($query)) {
										echo "<option ".($package == $row['packageid'] ? 'selected' : '')." data-cat='".$row['category']."' data-cost='".$row['cost']."' value='". $row['packageid']."'>".$row['heading'].'</option>';
									} ?>
								</select>
							</div>
							<div class="col-sm-2"><label class="show-on-mob">Fee:</label>
								<input name="package_cost[]" id="<?php echo 'package_cost_'.$loop; ?>" onchange="countTotalPrice()" value="<?php echo $package_cost + 0; ?>" type="number" step="any" readonly class="form-control package_cost" />
								<input name="package_row_id[]" type="hidden" value="<?= $insurer_row_id++ ?>" class="insurer_row_id" />
								<input name="package_gst_exempt[]" type="hidden" value="0" />
							</div>
							<div class="col-sm-1">
								<img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_package_row(this);">
								<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_package_row();">
							</div>
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
                        <label class="col-sm-5 text-center">Product Name</label>
                        <label class="col-sm-3 text-center">Price</label>
                        <label class="col-sm-1 text-center">Qty</label>
                        <label class="col-sm-2 text-center">Total</label>
                    </div>

				    <?php $each_misc = explode(',', $misc_items);
                    $each_misc_ticketid = explode(',', $misc_ticketid);
					$each_misc_price = explode(',', $misc_prices);
					$each_misc_qty = explode(',', $misc_qtys);
					foreach($each_misc as $loop => $misc_item) {
                        if(!($each_misc_ticketid[$loop] > 0)) {
    						$misc_price = $each_misc_price[$loop];
    						$misc_qty = $each_misc_qty[$loop]; ?>
    						<div class="additional_misc form-group clearfix">
                                <input type="hidden" name="misc_ticketid[]" value="">
    							<div class="col-sm-5"><label class="show-on-mob">Product Name:</label>
    								<input type="text" name="misc_item[]" value="<?= $misc_item ?>" class="form-control misc_name">
    							</div>
    							<div class="col-sm-3"><label class="show-on-mob">Unit Price:</label>
    								<input type="number" step="any" min="0" name="misc_price[]" value="<?= $misc_price / $misc_qty ?>" onchange="setThirdPartyMisc(this); countTotalPrice()" class="form-control misc_price">
    							</div>
    							<div class="col-sm-1"><label class="show-on-mob">Quantity:</label>
    								<input type="number" step="any" min="0" name="misc_qty[]" value="<?= $misc_qty ?>" onchange="setThirdPartyMisc(this); countTotalPrice()" class="form-control misc_qty">
    							</div>
    							<div class="col-sm-2"><label class="show-on-mob">Total:</label>
    								<input type="number" readonly name="misc_total[]" value="<?= $misc_price ?>" class="form-control misc_total">
    								<input name="misc_row_id[]" type="hidden" value="<?= $insurer_row_id++ ?>" class="insurer_row_id" />
    							</div>
    							<div class="col-sm-1">
    								<img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_misc_row(this);">
    								<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_misc_row();">
    							</div>
    							<div class="col-sm-12 pay-div"></div>
    						</div>
    					<?php }
                    } ?>
                    <div id="add_here_new_misc"></div>
                </div>
            </div>

            <div class="form-group ticket_option" <?= (in_array('unbilled_tickets',$field_config) ? '' : 'style="display:none;"') ?>>
                <label for="additional_note" class="col-sm-2 control-label">
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Add items from unbilled <?= TICKET_TILE ?> here."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
                Unbilled <?= TICKET_TILE ?>:</label>
                <div class="col-sm-7"><?php
                    $db_config = explode(',',get_field_config($dbc, 'tickets_dashboard'));
					$tickets = $dbc->query("SELECT `tickets`.* FROM `tickets` LEFT JOIN `invoice` ON CONCAT(',',`invoice`.`ticketid`,',') LIKE CONCAT('%,',`tickets`.`ticketid`,',%') WHERE (`invoice`.`invoiceid` IS NULL OR `invoice`.`invoiceid` = '".$invoiceid."') ".($_GET['contactid'] > 0 ? "AND (CONCAT(',',IFNULL(`tickets`.`businessid`,''),',',IFNULL(`tickets`.`clientid`,''),',') LIKE '%,".filter_var($_GET['contactid'],FILTER_SANITIZE_STRING).",%' OR (IFNULL(`tickets`.`businessid`,0)=0 AND IFNULL(NULLIF(NULLIF(`tickets`.`clientid`,'0'),',,'),'')=''))" : "")." AND `tickets`.`deleted`=0 ".(in_array('Administration',$db_config) ?"AND IFNULL(`approvals`,'') != ''" : ''))->fetch_all(MYSQLI_ASSOC); ?>

                    <?php foreach(explode(',', $get_invoice['ticketid']) as $invoice_ticketid) { ?>
                        <div class="invoice_ticket">
                            <select name="ticketid[]" data-placeholder="Select a <?= TICKET_NOUN ?>" class="chosen-select-deselect">
                                <option></option>
                                <?php foreach($tickets as $ticket) {
                                    if($ticket['ticketid'] > 0) {
                                        echo '<option value="'.$ticket['ticketid'].'" '.($invoice_ticketid == $ticket['ticketid'] ? 'selected' : '').'>'.get_ticket_label($dbc, $ticket).'</option>';
                                    }
                                } ?>
                            </select>
                            <div class="ticket_details">
                                <!-- Loaded from JavaScript -->
                            </div>
                            <div class="clearfix"></div>
                            <div class="form-group pull-right">
                                <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_ticket_row(this);">
                                <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_ticket_row();">
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="form-group" <?= (in_array('promo',$field_config) ? '' : 'style="display:none;"') ?>>
                <label for="site_name" class="col-sm-2 control-label">
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Apply any promotions here."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
                Promotion:</label>
                <div class="col-sm-7">
                    <select data-placeholder="Select a Promotion..." id="promotionid" name="promotionid" class="chosen-select-deselect form-control" width="380">
                        <option value=""></option>
                        <?php
                        //$query = mysqli_query($dbc,"SELECT promotionid, heading, cost FROM promotion WHERE deleted=0 AND DATE(expiry_date) >= DATE(NOW())");
                        $query = mysqli_query($dbc,"SELECT promotionid, heading, cost FROM promotion WHERE deleted=0");
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

            <?php if (in_array('discount',$field_config)) { ?>
                <div class="form-group">
                    <label for="giftcard" class="col-sm-2 control-label">Discount Type:</label>
                    <div class="col-sm-7">
                        <label><input type="radio" name="discount_type" value="%" />%</label>
                        <label><input type="radio" name="discount_type" value="$" />$</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="giftcard" class="col-sm-2 control-label">Discount Value:</label>
                    <div class="col-sm-7">
                        <input name="discount_value" onchange="countTotalPrice()" id="discount_value" type="text" class="form-control" value="<?= $discount_value; ?>" />
                    </div>
                </div>
            <?php } ?>

			<div class="form-group" <?= (in_array('pay_mode',$field_config) ? '' : 'style="display:none;"') ?>>
				<label for="site_name" class="col-sm-2 control-label">Payment Method:</label>
				<div class="col-sm-7">

                    <select data-placeholder="Select a Type..." name="paid" id="paid_status" class="chosen-select-deselect form-control" width="480">
                        <option value=""></option>
                        <!--<option <?php if ($paid=='Saved') echo 'selected="selected"';?>  value="Saved">Save Invoice</option>-->
                        <option <?php if ($paid=='Yes') echo 'selected="selected"';?>  value="Yes">Patient Invoice : Patient is paying full amount on checkout.</option>
                        <option <?php if ($paid=='Waiting on Insurer') echo 'selected="selected"';?> value="Waiting on Insurer">Waiting on <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> : Clinic is waiting on <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> to pay full amount.</option>
                        <option <?php if ($paid=='No') echo 'selected="selected"';?>  value="No">Partially Paid : The invoice is being paid partially by patient and partially by <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?>.</option>
                        <option <?php if ($paid=='On Account') echo 'selected="selected"';?> value="On Account">A/R On Account : Patient will pay invoice in future. Must choose Payment Type as Apply A/R to Account.</option>
                        <option <?php if ($paid=='Credit On Account') echo 'selected="selected"';?> value="Credit On Account">Credit On Account : Patient is appyling credit to profile.</option>
                    </select>
				</div>
			</div>

          <!--<div class="form-group">
            <label for="site_name" class="col-sm-2 control-label">Total Price($)<span class="hp-red">*</span>:</label>
            <div class="col-sm-7">
              <input name="total_price" value="<?php echo $total_price; ?>" id="total_price" type="text" class="form-control" />
            </div>
          </div>

            <?php
            $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='invoice_tax'"));
            $value_config = $get_field_config['value'];

            $invoice_tax = explode('*#*',$value_config);

            $total_count = mb_substr_count($value_config,'*#*');
            $tax_rate = 0;
			//for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
            foreach($invoice_tax as $invoice_tax_line) {
                $invoice_tax_name_rate = explode('**',$invoice_tax_line);
                $tax_rate += floatval($invoice_tax_name_rate[1]);

                /*if($tax_rate != '') {
                ?>

                <div class="clearfix"></div>

              <div class="form-group">
                <label for="site_name" class="col-sm-2 control-label"><?php echo $invoice_tax_name_rate[0];?>(%):<br></label>
                <div class="col-sm-7">
                  <input name="invoice_tax" readonly value='<?php echo $invoice_tax_name_rate[1];?>' type="text" class="form-control invoice_tax" />
                </div>
              </div>

            <?php }*/
            } ?>-->
            <input type="hidden" name="tax_rate" id="tax_rate" value="<?= $tax_rate ?>" />
			<input name="total_price" value="<?php echo 0+$total_price; ?>" id="total_price" type="hidden" />
			<input name="final_price" value="<?php echo 0+$final_price; ?>" id="final_price" type="hidden" />

          <div class="form-group" <?= (in_array('tips',$field_config) ? '' : 'style="display:none;"') ?>>
            <label for="site_name" class="col-sm-2 control-label">
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the gratuity to be applied to the assigned staff."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
            Gratuity($):</label>
            <div class="col-sm-7">
              <input name="gratuity" onchange="countTotalPrice()" id="gratuity" type="text" class="form-control" value="<?= $gratuity ?>" />
            </div>
          </div>

          <div class="form-group" <?= (in_array('delivery',$field_config) ? '' : 'style="display:none;"') ?>>
            <label for="site_name" class="col-sm-2 control-label">
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the delivery method chosen by the <?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?>."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
            Delivery Option:</label>
            <div class="col-sm-7">
              <select name="delivery_type" data-placeholder="Select a Delivery Option..." id="delivery_type" class="form-control chosen-select-deselect"><option></option>
				<option <?= ($delivery_type == 'Pick-Up' ? 'selected' : '') ?> value="Pick-Up">Pick-Up</option>
				<option <?= ($delivery_type == 'Company Delivery' ? 'selected' : '') ?> value="Company Delivery">Company Delivery</option>
				<option <?= ($delivery_type == 'Drop Ship' ? 'selected' : '') ?> value="Drop Ship">Drop Ship</option>
				<option <?= ($delivery_type == 'Shipping' ? 'selected' : '') ?> value="Shipping">Shipping</option>
				<option <?= ($delivery_type == 'Shipping on Customer Account' ? 'selected' : '') ?> value="Shipping on Customer Account">Shipping on Customer Account</option>
			  </select>
            </div>
          </div>

          <div class="form-group confirm_delivery" <?= (($delivery_type == 'Drop Ship' || $delivery_type == 'Shipping' || $delivery_type == 'Company Delivery') ? '' : 'style="display:none;"') ?>>
            <label for="site_name" class="col-sm-2 control-label">
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Update the address for delivery. If it is wrong, you will need to update it on the <?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?> profile. You can also enter a one-time shipping address."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
            Confirm Delivery Address:</label>
            <div class="col-sm-7">
              <input name="delivery_address" onchange="countTotalPrice()" id="delivery_address" type="text" class="form-control" value="<?= $delivery_address ?>" />
            </div>
          </div>

          <div class="form-group deliver_contractor" <?= (($delivery_type == 'Drop Ship' || $delivery_type == 'Shipping') ? '' : 'style="display:none;"') ?>>
            <label for="site_name" class="col-sm-2 control-label">
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the contractor that will handle the delivery."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
            Delivery Contractor:</label>
            <div class="col-sm-7">
              <select name="contractorid" id="contractorid" class="form-control chosen-select-deselect"><option></option>
				<?php $contractors = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `last_name`, `first_name`, `name` FROM `contacts` WHERE `category` LIKE 'Contractor%' AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
				foreach($contractors as $contractor) {
					$contractor = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `name` FROM `contacts` WHERE `contactid`='$contractor'"));
					echo "<option ".($contractor['contactid'] == $contractorid ? 'selected' : '')." value='". $contractor['contactid']."'>".($contractor['name'] != '' ? decryptIt($contractor['name']) : decryptIt($contractor['first_name']).' '.decryptIt($contractor['last_name'])).'</option>';
				} ?>
			  </select>
            </div>
          </div>

          <div class="form-group ship_amt" <?= (($delivery_type == '' || $delivery_type == 'Pick-Up') ? 'style="display:none;"' : '') ?>>
            <label for="site_name" class="col-sm-2 control-label">
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Enter the cost of shipping."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
            Delivery/Shipping Amount:</label>
            <div class="col-sm-7">
              <input name="delivery" onchange="countTotalPrice()" id="delivery" type="text" class="form-control" value="<?= $delivery ?>" />
            </div>
          </div>

          <div class="form-group" <?= (in_array('ship_date',$field_config) ? '' : 'style="display:none;"') ?>>
            <label for="site_name" class="col-sm-2 control-label">
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Enter the date by which the order will ship."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
            Ship Date:</label>
            <div class="col-sm-7">
              <input name="ship_date" onchange="countTotalPrice()" id="ship_date" type="text" class="form-control datepicker" value="<?= $ship_date ?>" />
            </div>
          </div>

        <?php if (in_array('assembly',$field_config)) { ?>
            <div class="form-group">
                <label for="giftcard" class="col-sm-2 control-label">
                Assembly:</label>
                <div class="col-sm-7">
                    <input name="assembly" onchange="countTotalPrice()" id="assembly" type="text" class="form-control" value="<?= $assembly; ?>" />
                </div>
            </div>
        <?php } ?>

          <?php
          /*

            if(!empty($_GET['patientid'])) {
                $patientid = $_GET['patientid'];
                $account_balance = get_all_from_patient($dbc, $patientid, 'account_balance');

                if($account_balance < 0) {
                ?>

                 <div class="form-group">
                    <label for="site_name" class="col-sm-2 control-label">Amount to Pay:</label>
                    <div class="col-sm-7">
                    <?php echo $account_balance; ?>
                </div> </div>
                <input type="hidden" id="account_balance" value="<?php echo $account_balance; ?>">
            <?php } else { ?>
                <input type="hidden" id="account_balance" value="0">
            <?php }
          }
          */
          ?>

          <?php
          /*
            if(!empty($_GET['patientid'])) {
                $patientid = $_GET['patientid'];
                $result_crm_promotion = mysqli_query($dbc, "SELECT * FROM crm_promotion WHERE used = 0 AND DATE(expiry_date) >= DATE(NOW()) AND patientid ='$patientid'"); ?>

                  <div class="form-group">
                    <label for="site_name" class="col-sm-2 control-label">Promotion:</label>
                    <div class="col-sm-7">
                <?php
                while($row = mysqli_fetch_array($result_crm_promotion)) { ?>
                      <?php
                      echo '<input onchange="countTotalPrice()" id="pro'.$row['promotionid'].'_'.get_all_from_service($dbc, $row['serviceid'], 'fee').'" name="promotionid" type="radio" value="'.$row['promotionid'].'" class="form" />&nbsp;&nbsp;'.get_all_from_service($dbc, $row['serviceid'], 'heading'). ' : '.$row['expiry_date']. ' : $'.get_all_from_service($dbc, $row['serviceid'], 'fee').'<br>';
                      ?>
                <?php } ?>
                </div> </div>
            <?php }
            */
            ?>

          <!--<div class="form-group">
            <label for="site_name" class="col-sm-2 control-label">Final Price($)<span class="hp-red">*</span>:</label>
            <div class="col-sm-7">
              <input name="final_price" value="<?php echo $final_price; ?>" id="final_price" type="text" class="form-control" />
            </div>
          </div>-->

            <!--<div class="form-group ins_payment_option">
                <label for="additional_note" class="col-sm-2 control-label">Insurance Payment:</label>
                <div class="col-sm-7">
                    <div class="form-group clearfix">
                        <label class="col-sm-2 text-center"></label>
                        <label class="col-sm-3 text-center">3rd Party Payment Provider/Insurance</label>
                        <label class="col-sm-3 text-center">Insurance Payment</label>
                    </div>

                    <?php
                    $ins_insurerid = explode(',',$insurerid);
                    $ins_pay = explode('#*#',$insurance_payment);

                    $total_count = mb_substr_count($insurerid,',');
                    for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
                        $insurance_payment_pay = explode(',',$ins_pay[1]);

                        if($insurance_payment_pay[$eq_loop] != '') {
                    ?>
                        <div class="clearfix"></div>
                        <div class="form-group clearfix">
                            <div class="col-sm-2">
                            </div>
                            <div class="col-sm-3">
                              <select name="insurerid[]" id="payer_name" class="chosen-select-deselect form-control" width="380">
                                <option value=""></option>
                                    <?php
                                    $query = mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='Insurer' AND deleted=0 ORDER BY name");
                                    while($row = mysqli_fetch_array($query)) {
                                        if ($ins_insurerid[$eq_loop] == $row['contactid']) {
                                            $selected = 'selected="selected"';
                                        } else {
                                            $selected = '';
                                        }
                                        echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['name']). '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <input name="insurance_payment[]" value="<?php echo $insurance_payment_pay[$eq_loop];?>" type="text" onchange="countTotalPrice()" class="form-control" />
                            </div>

                        </div>
                    <?php }
                    } ?>

                    <div class="ins_additional_payment clearfix">
                        <div class="clearfix"></div>
                        <div class="form-group clearfix">
                            <div class="col-sm-2">
                            </div>
                            <div class="col-sm-3">
                              <select name="insurerid[]" id="payer_name" class="chosen-select-deselect form-control" width="380">
                                <option value=""></option>
                                    <?php
                                    $query = mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='Insurer' AND deleted=0 ORDER BY name");
                                    while($row = mysqli_fetch_array($query)) {
                                        echo "<option value='". $row['contactid']."'>".decryptIt($row['name']). '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <input name="insurance_payment[]" type="text" onchange="countTotalPrice()" class="form-control" />
                            </div>
                        </div>
                    </div>

                    <div id="ins_add_here_new_payment"></div>

                    <div class="form-group triple-gapped clearfix">
                        <div class="col-sm-offset-4 col-sm-8">
                            <button id="ins_add_row_payment" class="btn brand-btn pull-left">Add Another Insurer</button>
                        </div>
                    </div>
                </div>
            </div>-->

          <!-- <div class="form-group">
            <label for="site_name" class="col-sm-2 control-label">Payment Type<span class="hp-red">*</span>:</label>
            <div class="col-sm-7">
              <select id="payment_type" name="payment_type" data-placeholder="Choose a Type..." class="chosen-select-deselect form-control" width="380">
					<option value=''></option>
					<option value = 'Master Card'>Master Card</option>
					<option value = 'Visa'>Visa</option>
					<option value = 'Debit'>Debit</option>
					<option value = 'Cash'>Cash</option>
					<option value = 'Check'>Check</option>
					<option value = 'Cash'>Gift Certificate Redeem</option>
              </select>
            </div>
          </div>
          -->

        <div class="form-group" <?= (in_array('next_appt',$field_config) ? '' : 'style="display:none;"') ?>>
                <label for="site_name" class="col-sm-2 control-label">
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select to book the next appointment. If you click save, the appointment will be saved, and will not appear on this invoice when it is being edited."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
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
        <!--
        <div class="form-group">
                <label for="site_name" class="col-sm-2 control-label">Next Appointment<span class="hp-red">*</span>:</label>
                <div class="col-sm-7">
                  <input required name="next_appointment" type="radio" value="Yes" class="form next_appointment" /> Yes
                  <input required name="next_appointment" checked type="radio" value="No" class="form next_appointment" /> No
                </div>
            </div>

            <input type="hidden" name="bookingid" value=<?php echo $bookingid; ?> >

         <span class="next_appointment_fields">
          <div class="form-group">
            <label for="first_name" class="col-sm-2 control-label"></label>
            <div class="col-sm-7">
                <button type="button" name="block_booking" value="block_booking" class="block_booking_btn btn brand-btn pull-right">Block Booking</button>
            </div>
          </div>

          <div class="form-group">
            <label for="first_name" class="col-sm-2 control-label"></label>
            <div class="col-sm-7">
                <button type="button" name="block_booking1" value="block_booking1" class="normal_booking_btn btn brand-btn pull-right">1 Booking</button>
            </div>
          </div>

         <span class="normal_booking_display">
          <div class="form-group clearfix">
            <label for="first_name" class="col-sm-2 control-label text-right">Start Appointment Date & Time:
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Click on the 15 minute interval labels to specify those times"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="25"></a>
                </span>
            </label>
            <div class="col-sm-7">
                <input name="appoint_date" id="range_example_3_start" type="text" placeholder="Click for Datepicker" class="datetimepicker"></p>
            </div>
          </div>

          <div class="form-group clearfix">
            <label for="first_name" class="col-sm-2 control-label text-right">End Appointment Date & Time:
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Click on the 15 minute interval labels to specify those times"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="25"></a>
                </span>
            </label>
            <div class="col-sm-7">
                <input name="end_appoint_date" id="range_example_3_end" type="text" placeholder="Click for Datepicker" class="datetimepicker"></p>
            </div>
          </div>
          </span>

          <span class="block_booking_display">

            <div class="form-group">
                <label for="" class="col-sm-2 control-label"></label>
                <div class="col-sm-7">
                    <div class="form-group clearfix">
                        <label class="col-sm-2">Booking</label>
                        <label class="col-sm-4">Start Appointment Date & Time
                            <span class="popover-examples list-inline">
                                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Click on the 15 minute interval labels to specify those times"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="25"></a>
                            </span>
                        </label>
                        <label class="col-sm-3">End Appointment Date & Time
                            <span class="popover-examples list-inline">
                                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Click on the 15 minute interval labels to specify those times"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="25"></a>
                            </span>
                        </label>
                    </div>

                    <?php
                    $total_block = get_config($dbc, 'minumum_block_booking_appointments');
                    for($book=1; $book<$total_block;$book++) {
                    ?>
                    <div class="clearfix">
                        <div class="clearfix"></div>
                        <div class="form-group clearfix">
                            <div class="col-sm-2">
                                <?php echo $book;?>
                            </div>
                            <div class="col-sm-4">
                                <input name="block_appoint_date[]" id="appointdate_<?php echo $book; ?>" type="text" placeholder="Click for Datepicker" class="datetimepicker"></p>
                            </div>
                            <div class="col-sm-3">
                                <input name="block_end_appoint_date[]" id="endappointdate_<?php echo $book; ?>" type="text" placeholder="Click for Datepicker" class="datetimepicker"></p>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                    <div class="additional_booking clearfix">
                        <div class="clearfix"></div>
                        <div class="form-group clearfix">
                            <div class="col-sm-2">
                               <span class="booking_head"><?php echo $book;?></span>
                            </div>
                            <div class="col-sm-4">
                                <input name="block_appoint_date[]" id="appointdate_<?php echo $book; ?>" type="text" placeholder="Click for Datepicker" class="datetimepicker"></p>
                            </div>
                            <div class="col-sm-3">
                                <input name="block_end_appoint_date[]" id="endappointdate_<?php echo $book; ?>" type="text" placeholder="Click for Datepicker" class="datetimepicker"></p>
                            </div>
                        </div>
                    </div>

                    <div id="add_here_new_booking"></div>

                    <div class="form-group triple-gapped clearfix">
                        <div class="col-sm-offset-4 col-sm-8">
                            <button id="add_row_booking" class="btn brand-btn pull-left">Add Booking</button>
                        </div>
                    </div>

                </div>
            </div>
          </span>

          </span>
          -->
               <div class="form-group" <?= (in_array('survey',$field_config) ? '' : 'style="display:none;"') ?>>
                <label for="site_name" class="col-sm-2 control-label">
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the proper survey to send here."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
                Send Survey:</label>
                <div class="col-sm-7">
                    <select data-placeholder="Select a Survey..." name="survey" class="chosen-select-deselect form-control" width="380">
                      <option value=""></option>
                      <?php
                        $query = mysqli_query($dbc,"SELECT surveyid, name, service FROM crm_feedback_survey_form WHERE deleted=0");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option ".($get_invoice['survey'] == $row['surveyid'] ? 'selected' : '')." value='". $row['surveyid']."'>".$row['name'].' : '.$row['service'].'</option>';
                        }
                      ?>
                    </select>
                </div>
              </div>

			<?php if (strpos(','.get_config($dbc, 'crm_dashboard').',', ',Recommendations,') !== FALSE) { ?>
				<div class="form-group" <?= (in_array('request_recommend',$field_config) ? '' : 'style="display:none;"') ?>>
					<label for="site_name" class="col-sm-2 control-label">
						<span class="popover-examples list-inline">
						<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select whether or not to send the Recommendation email."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
						</span>
						Request Recommendation Report:</label>
					<div class="col-sm-7">
						<label class="control-label"><input type="radio" name="request_recommendation" <?= ($get_invoice['request_recommend'] == 'send' ? 'checked' : '') ?> value="send"> Send</label>
						<label class="control-label"><input type="radio" name="request_recommendation" <?= ($get_invoice['request_recommend'] == 'no' ? 'checked' : '') ?> value="no"> Don't Send</label>
					</div>
				</div>
			<?php } ?>

               <div class="form-group" <?= (in_array('followup',$field_config) ? '' : 'style="display:none;"') ?>followup>
                <label for="site_name" class="col-sm-2 control-label">
                <span class="popover-examples list-inline">
                    <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select the proper follow up email type."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
                Send Follow Up Email After Assessment: </label>
                <div class="col-sm-7">
                    <select data-placeholder="Select an Email Type..." name="follow_up_assessment_email" class="chosen-select-deselect form-control" width="380">
                      <option value=""></option>
                      <option <?= ($get_invoice['follow_up_email'] == 'Massage' ? 'selected' : '') ?> value="Massage">Massage Follow Up Email</option>
                      <option <?= ($get_invoice['follow_up_email'] == 'Physiotherapy' ? 'selected' : '') ?> value="Physiotherapy">Physiotherapy Follow Up Email</option>
                    </select>
                </div>
              </div>

			<?php if (in_array('giftcard',$field_config)) { ?>
                <div class="form-group">
                    <label for="giftcard" class="col-sm-2 control-label">
                    Gift Card:</label>
                    <div class="col-sm-7">
                        <input type="text" <?php echo ($return ? 'readonly' : ''); ?> name="gf_number" onblur="changeGF(this.value);" id="gf_number" value="<?= ($gf_number) ? $gf_number : ''; ?>" type="text" class="form-control" />
                    </div>
                </div>
            <?php } ?>

            <div class="form-group payment_option">
                <label for="additional_note" class="col-sm-2 control-label"><?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?> Payment:</label>
                <div class="col-sm-7">
					<label class="col-sm-12 control-checkbox"><input type="checkbox" name="add_credit" value="add_credit" onchange="allow_edit_amount();">
					<input type="hidden" name="credit_balance" value=0>Add balance as credit on <?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?> Account</label>
                    <div class="form-group clearfix hide-titles-mob">
                        <label class="col-sm-6 text-center">Type</label>
                        <label class="col-sm-6 text-center">Amount</label>
                    </div>

                    <?php
                    $pt1 = explode('#*#',$payment_type);
                    $pt_type = $pt1[0];
                    $pt_amount = $pt1[1];
                    $pt_type_each = explode(',',$pt_type);
                    $pt_amount_each = explode(',',$pt_amount);
                    $final_pt = '';
                    $m = 0;
                    foreach ($pt_type_each as $pt_each) {
                        if($pt_each != '') {
                        $final_pt .= $pt_each.','.$pt_amount_each[$m].'#*#';
                        }
                        $m++;
                    }

                    $patient_pay = explode('#*#',$final_pt);

                    $total_count = mb_substr_count($final_pt,'#*#');
                    for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
                        $patient_payment_pay = explode(',',$patient_pay[$eq_loop]);

                        if($patient_payment_pay[0] != '') {
                        ?>
                        <div class="clearfix"></div>
                        <div class="additional_payment form-group clearfix">
                            <div class="col-sm-6"><label class="show-on-mob">Payment Type:</label>
                                <select id="payment_type" name="payment_type[]" data-placeholder="Select a Type..." class="chosen-select-deselect form-control" width="380">
                                    <option value=''></option>
									<?php foreach(explode(',',get_config($dbc, 'invoice_payment_types')) as $available_pay_method) { ?>
										<option <?php if ($patient_payment_pay[0] == $available_pay_method) { echo " selected"; } ?>  value = '<?= $available_pay_method ?>'><?= $available_pay_method ?></option>
									<?php } ?>
                                    <?php if($account_balance != 0) { ?>
                                    <option value = 'Patient Account' ><?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?> Account : $<?php echo $account_balance; ?></option>
                                    <?php }
                                    if($patient_payment_pay[0] == "Patient Account") { ?>
                                        <option <?php if ($patient_payment_pay[0] == "Patient Account") { echo " selected"; } ?>  value = 'Patient Account' ><?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?> Account</option>
                                    <?php }
									if(strpos(WEBSITE_URL,'clinicace') !== FALSE) { ?>
										<option <?php if ($patient_payment_pay[0] == "On Account") { echo " selected"; } ?>  value = 'On Account'>Apply A/R to Account</option>
									<?php } ?>
                                </select>
                            </div>
                            <div class="col-sm-5"><label class="show-on-mob">Payment Amount:</label>
                                <input name="payment_price[]" value="<?php echo $patient_payment_pay[1];?>" type="text" class="form-control payment_price" onchange="countTotalPrice();" />
                            </div>
							<div class="col-sm-1">
								<img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_patient_payment_row(this);">
								<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_patient_payment_row();">
							</div>
                        </div>
                    <?php }
                    } ?>

					<div class="clearfix"></div>
					<div class="additional_payment form-group clearfix">
						<div class="col-sm-6"><label class="show-on-mob">Payment Type:</label>
						  <select id="payment_type" name="payment_type[]" data-placeholder="Select a Type..." class="chosen-select-deselect form-control" width="380">
								<option value=''></option>
								<?php foreach(explode(',',get_config($dbc, 'invoice_payment_types')) as $available_pay_method) { ?>
									<option value = '<?= $available_pay_method ?>'><?= $available_pay_method ?></option>
								<?php } ?>
								<?php if($account_balance != 0) { ?>
								<option value = 'Patient Account' >Apply Credit to <?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?> Account : $<?php echo $account_balance; ?></option>
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

                    <!--<div class="form-group triple-gapped clearfix">
                        <div class="col-sm-offset-4 col-sm-8">
                            <button id="add_row_payment" class="btn brand-btn pull-left">Add Payment Option</button>
                        </div>
                    </div>-->
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

        </div><!-- .main-div -->

        <div class="control-div">
          <div class="form-group">
            <div class="col-sm-2 col-xs-4">
            	<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Clicking here will discard changes and return you to the <?= (empty($current_tile_name) ? 'Check Out' : $current_tile_name) ?> tile main dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="index.php?tab=today" class="btn brand-btn">Back</a>
			</div>
            <div class="col-sm-7 col-xs-8">
                <button type="submit" name="submit_btn" onclick="return validateappo();" id="submit" value="Submit" class="btn brand-btn pull-right">Submit</button>
                <span class="popover-examples list-inline pull-right" style="margin:5px;"><a data-toggle="tooltip" data-placement="top" title="Click here to Submit the invoice after processing payment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <!--<button type="submit" name="save_btn" onclick="return validateappo();" id="save" value="Save" class="btn brand-btn pull-right">Save</button>-->
                <button type="submit" name="save_btn" onclick="return validateappo();" id="save" value="Save" class="pull-right image-btn"><img src="../img/icons/save.png" alt="Save" width="30" class="override-theme-color-icon" /></button>
				<span class="popover-examples list-inline pull-right" style="margin:5px;"><a data-toggle="tooltip" data-placement="top" title="Click here to Save this invoice for when a client is checking out (you will need to complete the transaction later)."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <!--
                <span class="popover-examples list-inline pull-right button_info_icon" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to generate the invoice."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                -->
            </div>
          </div>
        </div>

        </div><!-- .wrapper -->

        </form>

		<?php // } ?>

        <?php
        // if(!empty($_GET['action'])) {  include('pay_invoice.php');   }
        ?>

    </div>
  </div>
<script>
$(window).scroll(function() {
	if ($(this).scrollTop() > $('form')[0].offsetTop) {
		$('.preview_div').addClass("sticky");
	} else {
		$('.preview_div').removeClass("sticky");
	}
});
$(document).ready(function() {
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
			if($('[name=treatment_plan]').is(':visible')) {
				$('.detail_patient_treatment').html($('[name=treatment_plan] option:selected').text()).closest('h4').show();
			} else {
				$('.detail_patient_treatment').closest('h4').hide();
			}
			$('.detail_staff_name ').html($('[name=therapistsid] option:selected').text() == '' ? 'N/A' : $('[name=therapistsid] option:selected').text());
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
	<?php if($paid != '') {
		echo "pay_mode_selected('$paid');\n";
		if($paid == 'No' || $paid == 'Waiting on Insurer') {
			echo "var service_ins = '".$get_invoice['service_insurer']."';\n";
			echo "var inv_ins = '".$get_invoice['inventory_insurer']."';\n";
			echo "var package_ins = '".$get_invoice['package_insurer']."';\n";
		} else {
			echo "var service_ins = '0:0';\n";
			echo "var inv_ins = '0:0';\n";
			echo "var package_ins = '0:0';\n";
		}
	} else {
		echo "var service_ins = '0:0';\n";
		echo "var inv_ins = '0:0';\n";
		echo "var package_ins = '0:0';\n";
	} ?>

	var i = 1;
	$(service_ins.split(',')).each(function() {
		var j = 0;
		$(this.split('#*#')).each(function() {
			var info = this.split(':');
			var target = $('.service_option').find('.form-group').eq(i).find('.pay-div').eq(j);
			target.find('[name="insurerid[]"]').val(info[0]).trigger('change.select2');
			target.find('[name="insurer_payment_amt[]"]').val(info[1]);
			j++;
		});
		i++;
	});
	var i = 1;
	$(inv_ins.split(',')).each(function() {
		var j = 0;
		$(this.split('#*#')).each(function() {
			var info = this.split(':');
			var target = $('.product_option').find('.form-group').eq(i).find('.pay-div').eq(j);
			target.find('[name="insurerid[]"]').val(info[0]).trigger('change.select2');
			target.find('[name="insurer_payment_amt[]"]').val(info[1]);
			j++;
		});
		i++;
	});
	var i = 1;
	$(package_ins.split(',')).each(function() {
		var j = 0;
		$(this.split('#*#')).each(function() {
			var info = this.split(':');
			var target = $('.package_option').find('.form-group').eq(i).find('.pay-div').eq(j);
			target.find('[name="insurerid[]"]').val(info[0]).trigger('change.select2');
			target.find('[name="insurer_payment_amt[]"]').val(info[1]);
			j++;
		});
		i++;
	});
	countTotalPrice();
});
$(document).on('change', 'select[name="app_type"]', function() { changeApptType(this.value); });
$(document).on('change', 'select[name="pricing"]', function() {
    if ($('[name="pricing"] option:selected').val()=='admin_price') {
        $('[name="unit_price[]"]').attr('readonly', false);
    } else {
        $('[name="unit_price[]"]').attr('readonly', true);
    }
    updatePricing();
});
$(document).on('change', '[name="unit_price[]"]', function() {
    adminPrice(this);
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
$(document).on('change', 'select[name="paid"]', function() { pay_mode_selected(this.value); });
$(document).on('change', 'select.service_category_onchange', function() { changeCategory(this); });
$(document).on('change', 'select[name="serviceid[]"]', function() { changeService(this); });
$(document).on('change', '[name="fee[]"]', function() { countTotalPrice(); });
$(document).on('change', 'select[name="inventorycat[]"]', function() { filterInventory(this); });
$(document).on('change', 'select[name="inventorypart[]"]', function() { changeProduct(this); });
$(document).on('change', 'select[name="inventoryid[]"]', function() { changeProduct(this); });
$(document).on('change', 'select[name="invtype[]"]', function() { changeProduct(this); });
$(document).on('change', 'select[name="packagecat[]"]', function() { changePackage(this); });
$(document).on('change', 'select[name="packageid[]"]', function() { changePackage(this); });
$(document).on('change', 'select[name="promotionid"]', function() { changePromotion(this); });
$(document).on('change', 'select[name="delivery_type"]', function() { countTotalPrice(); });
$(document).on('change', 'select[name="contractorid"]', function() { countTotalPrice(); });
$(document).on('change', 'select[name="payment_type[]"]', function() { set_patient_payment_row(); });

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
					'<a href="#job_file" data-toggle="tooltip" data-placement="top" title="The portion that the <?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> will pay."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span></label>'+
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
		yearRange: '<?= date('Y') - 10 ?>:<?= date('Y') + 5 ?>',
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

function billTicket(input) {
	var block = $(input).closest('label');
	if(input.checked) {
		block.find('[disabled]').removeAttr('disabled');
	} else {
		block.find('[type=hidden]').prop('disabled',true);
	}
	setTotalPrice();
}
</script>
<?php include ('../footer.php'); ?>