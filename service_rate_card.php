<?php

include ('include.php');

// Add rate card.
/*
$result_survey = mysqli_query($dbc, "SELECT serviceid, fee, admin_price FROM services");
while(($row =  mysqli_fetch_assoc($result_survey))) {
    $serviceid = $row['serviceid'];
    $fee = $row['fee'];
    $admin_price = $row['admin_price'];
    $start_date = '2014-01-01';
    $end_date = '2017-12-31';

    $query_insert_invoice = "INSERT INTO `service_rate_card` (`serviceid`, `start_date`, `end_date`, `service_rate`, `admin_fee`) VALUES ('$serviceid', '$start_date', '$end_date', '$fee', '$admin_price')";
    $result_insert_invoice = mysqli_query($dbc, $query_insert_invoice);
}
*/

// Update admin fee in invoice.
$result_survey = mysqli_query($dbc, "SELECT invoiceid, serviceid FROM invoice");
while(($row =  mysqli_fetch_assoc($result_survey))) {
    $serviceid = $row['serviceid'];
    $invoiceid = $row['invoiceid'];
    $service = explode(',', $serviceid);
    $all_af = '';
    foreach ($service as $key) {
        $admin_price = get_all_from_service($dbc, $key, 'admin_price');
        $all_af .= $admin_price.',';
    }
    $my = rtrim($all_af,',');
    $query_update_vendor = "UPDATE `invoice` SET `admin_fee` = '$my' WHERE `invoiceid` = '$invoiceid'";
    $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
}

// Add data in compensation.
$result_survey = mysqli_query($dbc, "SELECT invoiceid, serviceid, therapistsid, fee, admin_fee, service_date FROM invoice");
while(($row =  mysqli_fetch_assoc($result_survey))) {
    $invoiceid = $row['invoiceid'];
    $therapistsid = $row['therapistsid'];
    $service_date = $row['service_date'];

    $service = explode(',', $row['serviceid']);
    $fee = explode(',', $row['fee']);
    $admin_fee = explode(',', $row['admin_fee']);
    $m = 0;
    foreach ($service as $key) {
        if($key != '') {
            $f = $fee[$m];
            $af = $admin_fee[$m];
            $query_insert_invoice = "INSERT INTO `invoice_compensation` (`invoiceid`, `contactid`, `item_id`, `item_type`, `fee`, `admin_fee`, `service_date`) VALUES ('$invoiceid', '$therapistsid', '$key', 'services', '$f', '$af', '$service_date')";
            $result_insert_invoice = mysqli_query($dbc, $query_insert_invoice);
            $m++;
        }
    }
}


echo 'Rate card added';
echo '<br>';
echo 'invoice admin fee updated';
echo '<br>';
echo 'invoice_compensation table updated';