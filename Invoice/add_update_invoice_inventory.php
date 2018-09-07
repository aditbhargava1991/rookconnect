<?php

// Inv Code

$mva_inv_price = 0;
//$list_inventory_patient = '';
//$list_inventory_insures = '';
//
//$inv_insurer_price = 0;
//$inv_patient_price = 0;

if($mva_inv_price > $mva_claim_price) {
    //$inv_insurer_price = $mva_claim_price;
    //$inv_patient_price = ($mva_inv_price-$mva_claim_price);
    $change_claim = $mva_claim_price;
} else {
    $change_claim = $mva_inv_price;
}

//$query_update_patient = "UPDATE `patient_injury` SET `mva_claim_price` = mva_claim_price - '$change_claim' WHERE `injuryid` = '$injuryid'";
//$result_update_patient = mysqli_query($dbc, $query_update_patient);

for($i=0; $i<count($_POST['inventoryid']); $i++) {
    $inventoryid = $_POST['inventoryid'][$i];
    $invtype_rep = $_POST['invtype'][$i];
    $sell_price_rec_rep = $_POST['sell_price'][$i];
    $quantity = $_POST['quantity'][$i];
    //$type_report = get_all_from_inventory($dbc, $iid, 'type');

    $inventory_desc .= get_all_from_inventory($dbc, $inventoryid, 'code').' : '. get_all_from_inventory($dbc, $inventoryid, 'name');

    if($paid == 'Yes') {
        $amt_invoiced = $amt_paid = $sell_price_rec_rep;
        $amt_bill = '0.00';
    }
    if($paid == 'No') {
        $amt_bill = $sell_price_rec_rep;
        $amt_invoiced = $amt_paid = '0.00';
    }

    if($inventoryid != '') {
        $query_insert_invoice_report = "INSERT INTO `report_inventory` (`invoiceid`, `inventoryid`, `type`, `quantity`, `sell_price`, `today_date`) VALUES ('$invoiceid', '$inventoryid', '$invtype_rep', '$quantity', '$sell_price_rec_rep', '$final_service_date')";
        $result_insert_invoice_report = mysqli_query($dbc, $query_insert_invoice_report);

        //$query_insert_validation = "INSERT INTO `report_validation` (`therapist`, `invoice_date`, `patient`, `invoiceid`, `inventoryid`, `description`, `payer_name`, `rate`, `qty`, `amt_to_bill`, `amt_invoiced`, `amt_paid`) VALUES ('$staff', '$final_service_date', '$patients', '$invoiceid', '$inventoryid', '$inventory_desc', '$payer_name', '$sell_price_rec_rep', '$quantity_report', '$amt_bill', '$amt_invoiced', '$amt_paid')";
        //$result_insert_validation = mysqli_query($dbc, $query_insert_validation);
    }

    if($invtype_rep == 'MVA') {
        $mva_inv_price += $sell_price_rec_rep;
    }

    //if($invtype_rep == 'General' && $sell_price_rec_rep != 0) {
    //    $list_inventory_patient .= get_all_from_inventory($dbc, $inventoryid, 'name').'<br>';
    //    $inv_patient_price += $sell_price_rec_rep;
    //} else if($invtype_rep == 'WCB') {
    //    $list_inventory_insures .= get_all_from_inventory($dbc, $inventoryid, 'name').'<br>';
    //    $inv_insurer_price += $sell_price_rec_rep;
    //} else {
    //    if($mva_inv_price <= $mva_claim_price) {
    //        $list_inventory_insures .= get_all_from_inventory($dbc, $inventoryid, 'name').'<br>';
    //        $inv_insurer_price += $sell_price_rec_rep;
    //    } else {
    //        $list_inventory_patient .= get_all_from_inventory($dbc, $inventoryid, 'name').'<br>';
    //        $list_inventory_insures .= get_all_from_inventory($dbc, $inventoryid, 'name').'<br>';
    //    }
    //}

    //if($paid == 'No') {
    //    $query_insert_invoice_report = "INSERT INTO `report_receivables` (`invoiceid`, `itemid`, `fee`, `today_date`) VALUES ('$invoiceid', '0', '$sell_price_rec_rep', '$final_service_date')";
    //    $result_insert_invoice_report = mysqli_query($dbc, $query_insert_invoice_report);
    //}

    $query_inv = "UPDATE `inventory` SET current_stock = current_stock - $quantity WHERE `inventoryid` = '$inventoryid'";
    $result_update_inven = mysqli_query($dbc, $query_inv);
    
    //Connection set on database_connection.php Check Admin Settings > Sync Inventory for details.
    if ( $dbc_inventory ) {
        mysqli_query($dbc_inventory, $query_inv);
    }

    $stock = get_all_from_inventory($dbc, $inventoryid, 'current_stock');
    $min_bin = get_all_from_inventory($dbc, $inventoryid, 'min_bin');

    if($stock <= $min_bin) {
        $to = get_config($dbc, 'minbin_email');
        $subject = 'Inventory Min Bin Email';

        $message = $inventory_desc.' is reduced to min bin. Please check that.';
        $message = "<br><br><a href='".WEBSITE_URL."/Inventory/add_inventory.php?inventoryid=".$inventoryid."'>Click to View Product</a>";

        //send_email('', $to, '', '', $subject, $message, '');
    }
}

// Inv Code