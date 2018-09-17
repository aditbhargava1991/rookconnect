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

if (isset($_POST['submit_btn'])) {
    $invoice_date = date('Y-m-d');

    $invoiceid = $_POST['invoiceid'];

    $refund_service = implode(',', $_POST['refund_service']).',';
    $fee = implode(',', $_POST['fee']).',';
    $refund_inventory = implode(',', $_POST['refund_inventory']).',';
    $sell_price = implode(',', $_POST['sell_price']).',';

    $query_insert_invoice = "INSERT INTO `invoice_refund` (`invoiceid`, `serviceid`, `fee`, `inventoryid`, `sell_price`, `invoice_date`) VALUES ('$invoiceid', '$refund_service', '$fee', '$refund_inventory', '$sell_price', '$invoice_date')";
    $result_insert_invoice = mysqli_query($dbc, $query_insert_invoice);
    $refundid = mysqli_insert_id($dbc);

    $refund_final_price = 0;
    $service = '';
    $refund_service_fee = 0;
    for($i=0; $i<count($_POST['refund_service']); $i++) {
        $serviceid = $_POST['refund_service'][$i];
        $fee = $_POST['fee'][$i];
        //$query = mysqli_query($dbc,"DELETE FROM report_compensation WHERE invoiceid='$invoiceid' AND serviceid='$serviceid'");
        //$result_update_in = mysqli_query($dbc, "UPDATE `report_validation` SET `refundid` = '$refundid' WHERE invoiceid='$invoiceid' AND serviceid='$serviceid'");

        //$result_update_summary = mysqli_query($dbc, "UPDATE `report_summary` SET `refund` = `refund` + $fee WHERE DATE(today_date) = DATE(NOW())");

        $service .= get_all_from_service($dbc, $serviceid, 'service_code').' : '.get_all_from_service($dbc, $serviceid, 'heading').'<br>';

        $refund_service_fee += $fee;
        $refund_final_price += $fee;
    }

    $refund_inv_fee = 0;
    $refund_inventory_item = '';
    for($i=0; $i<count($_POST['refund_inventory']); $i++) {
        $inventoryid = $_POST['refund_inventory'][$i];
        $sell_price = $_POST['sell_price'][$i];

        $query = mysqli_query($dbc,"DELETE FROM report_inventory WHERE invoiceid='$invoiceid' AND inventoryid='$inventoryid'");

        $result_update_in = mysqli_query($dbc, "UPDATE `inventory` SET `current_stock` = current_stock + 1 WHERE `inventoryid` = '$inventoryid'");

        //$result_update_in = mysqli_query($dbc, "UPDATE `report_validation` SET `refundid` = '$refundid' WHERE invoiceid='$invoiceid' AND inventoryid='$inventoryid'");

        //$result_update_summary = mysqli_query($dbc, "UPDATE `report_summary` SET `refund` = `refund` + $sell_price WHERE DATE(today_date) = DATE(NOW())");

        $refund_inventory_item .= get_all_from_inventory($dbc, $inventoryid, 'name').'<br>';
        $refund_final_price += $sell_price;
        $refund_inv_fee += $sell_price;

    }

    $result_update_in = mysqli_query($dbc, "UPDATE `invoice_refund` SET `final_price` = '$refund_final_price' WHERE refundid='$refundid'");

    $get_invoice = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM invoice WHERE invoiceid='$invoiceid'"));

    $logo = get_config($dbc, 'invoice_logo');
    if(!empty($get_invoice['type']) && !empty(get_config($dbc, 'invoice_logo_'.$get_invoice['type']))) {
        $logo = get_config($dbc, 'invoice_logo_'.$get_invoice['type']);
    }
    DEFINE('INVOICE_LOGO', $logo);

    include ('refund_invoice_pdf.php');

    echo '<script type="text/javascript"> window.location.replace("invoice.php"); </script>';

}
?>
<script type="text/javascript" src="../Invoice/invoice.js"></script>
</head>

<body>
<?php include_once ('../navigation.php');

?>
<div class="container">
  <div class="row">

<h1 class="triple-pad-bottom">Refund</h1>

<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

<?php
    if(!empty($_GET['invoiceid'])) {
        $invoiceid = $_GET['invoiceid'];
        $get_invoice = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM invoice WHERE invoiceid='$invoiceid'"));

        $patient = get_contact($dbc, $get_invoice['patientid']);
        $staff = get_contact($dbc, $get_invoice['therapistsid']);
        $account_balance = get_all_from_patient($dbc, $get_invoice['patientid'], 'account_balance');

        $bookingid = $get_invoice['bookingid'];
        $injuryid = $get_invoice['injuryid'];
        $injury = get_all_from_injury($dbc, $injuryid, 'injury_type').' : '.get_all_from_injury($dbc, $injuryid, 'injury_name').' : '.get_all_from_injury($dbc, $injuryid, 'injury_date');

        echo '<input type="hidden" id="invoiceid" name="invoiceid" value="'.$invoiceid.'" />';

        $serviceid =$get_invoice['serviceid'];
        $fee =$get_invoice['fee'];
        $inventoryid =$get_invoice['inventoryid'];
        $sell_price =$get_invoice['sell_price'];
        $invtype =$get_invoice['invtype'];
        $quantity =$get_invoice['quantity'];
        $total_price =$get_invoice['total_price'];
        $final_price =$get_invoice['final_price'];
    }
?>

  <div class="form-group">
    <label for="site_name" class="col-sm-4 control-label">Patient<span class="hp-red">*</span>:</label>
    <div class="col-sm-8">
        <?php echo $patient; ?>
    </div>
  </div>

  <div class="form-group">
    <label for="site_name" class="col-sm-4 control-label">Therapist<span class="hp-red">*</span>:</label>
    <div class="col-sm-8">
        <?php echo $staff; ?>
    </div>
  </div>

  <div class="form-group">
    <label for="site_name" class="col-sm-4 control-label">Injury:</label>
    <div class="col-sm-8">
        <?php echo $injury; ?>
    </div>
  </div>

    <div class="form-group service_option">
        <label for="additional_note" class="col-sm-4 control-label">Services:</label>
        <div class="col-sm-8">
            <div class="form-group clearfix">
                <label class="col-sm-8 text-center">Service</label>
                <label class="col-sm-1 text-center">Refund</label>
            </div>

            <?php
            if($serviceid != '') {
                $each_serviceid = explode(',',$serviceid);
                $each_fee = explode(',',$fee);
                $total_count = mb_substr_count($serviceid,',');
                $id_loop = 500;

                for($client_loop=0; $client_loop<=$total_count; $client_loop++) {
                    if($each_serviceid[$client_loop] != '') {
                        $serviceid = $each_serviceid[$client_loop];
                        $fee = $each_fee[$client_loop];
                        ?>

                    <div class="form-group clearfix">

                        <div class="col-sm-7 text-center">
                            <?php echo get_all_from_service($dbc, $serviceid, 'category').' : '.get_all_from_service($dbc, $serviceid, 'heading'). ' : $'.get_all_from_service($dbc, $serviceid, 'fee'); ?>
                        </div> <!-- Quantity -->

                        <div class="col-sm-1 text-center">
                            <input name="fee[]" type="hidden" value="<?php echo $fee; ?>" class="form-control" />
                        </div> <!-- Quantity -->
                        <div class="col-sm-1 text-center">
                            <input type="checkbox" style="height: 25px; width: 25px;" name="refund_service[]" class="form-control" value="<?php echo $serviceid; ?>">
                        </div>
                    </div>
                        <?php
                        $id_loop++;
                    }
                }
            }
            ?>

        </div>
    </div>

    <div class="form-group product_option">
        <label for="additional_note" class="col-sm-4 control-label">Products<br>MVA Claim Price: </label>
        <div class="col-sm-8">
            <div class="form-group clearfix">
                <label class="col-sm-4 text-center">Product</label>
                <label class="col-sm-2 text-center">Type</label>
                <label class="col-sm-2 text-center">Qty</label>
                <label class="col-sm-2 text-center">Sell Price</label>
                <label class="col-sm-1 text-center">Refund</label>
            </div>

            <?php

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
                        ?>

                        <div class="form-group clearfix">
                            <div class="col-sm-4 text-center">
                                <?php echo get_all_from_inventory($dbc, $inventoryid, 'name'); ?>
                            </div> <!-- Quantity -->
                            <div class="col-sm-2 text-center">
                                <?php echo $invtype; ?>
                            </div> <!-- Quantity -->
                            <div class="col-sm-2 text-center">
                                <?php echo $quantity; ?>
                            </div> <!-- Quantity -->
                            <div class="col-sm-2 text-center">
                                <?php echo $sell_price; ?>
                            </div> <!-- Quantity -->
                            <div class="col-sm-1 text-center">
                                <input name="sell_price[]" type="hidden" value="<?php echo $sell_price; ?>" class="form-control" />
                            </div>
                            <div class="col-sm-1 text-center">
                                <input type="checkbox" style="height: 25px; width: 25px;" name="refund_inventory[]" value="<?php echo $inventoryid; ?>">
                            </div>
                        </div>
                    <?php
                    $id_loop++;
                    }
                }
            }
            ?>

        </div>
    </div>

  <div class="form-group">
    <label for="site_name" class="col-sm-4 control-label">Total Price($)<span class="hp-red">*</span>:</label>
    <div class="col-sm-8">
      <?php echo $total_price; ?>
    </div>
  </div>

    <?php
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='invoice_tax'"));
    $value_config = $get_field_config['value'];

    $invoice_tax = explode('*#*',$value_config);

    $total_count = mb_substr_count($value_config,'*#*');
    $tax_rate = 0;
    for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
        $invoice_tax_name_rate = explode('**',$invoice_tax[$eq_loop]);
        $tax_rate += $invoice_tax_name_rate[1];

        if($tax_rate != '') {
        ?>

        <div class="clearfix"></div>

      <div class="form-group">
        <label for="site_name" class="col-sm-4 control-label"><?php echo $invoice_tax_name_rate[0];?>(%):<br></label>
        <div class="col-sm-8">
          <?php echo $invoice_tax_name_rate[1];?>
        </div>
      </div>

    <?php }
    }
    echo '<input type="hidden" name="tax_rate" id="tax_rate" value="'.$tax_rate.'" />';
    ?>

  <div class="form-group">
    <label for="site_name" class="col-sm-4 control-label">Final Price($)<span class="hp-red">*</span>:</label>
    <div class="col-sm-8">
      <?php echo $final_price; ?>
    </div>
  </div>

  <div class="form-group">
    <div class="col-sm-4 clearfix">
        <a href="invoice.php" class="btn brand-btn pull-right">Back</a>
    </div>
    <div class="col-sm-8">
        <button type="submit" name="submit_btn" id="submit" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
    </div>
  </div>

    </div>
  </div>
<?php include ('../footer.php'); ?>