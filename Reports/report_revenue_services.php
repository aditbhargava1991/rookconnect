<?php
/*
$starttime1 = $starttime.' 06:00:00';
$endtime1 = $endtime.' 20:00:00';
$start_time_int = strtotime($starttime1);
$end_time_int = strtotime($endtime1);

$report_validation = mysqli_query($dbc,"SELECT i.serviceid FROM invoice i, booking b, mrbs_entry m WHERE i.therapistsid='$therapistid' AND b.bookingid = i.bookingid AND m.id = b.calid AND (m.start_time >= '$start_time_int' AND m.end_time <= '$end_time_int')");

$all_service = '';
while($row_tab = mysqli_fetch_array( $report_validation )) {
    $all_service .= $row_tab['serviceid'].',';
}

$all_service = str_replace(",,,",",",$all_service);
$all_service = str_replace(",,",",",$all_service);

$serviceid = explode(',', $all_service);
$serviceid = array_filter($serviceid);

// Services
if($all_service != '') {
    asort($serviceid);
    $occurences = array_count_values($serviceid);

    $report_data .= '<h4>'.get_contact($dbc, $therapistid).' -  Services Revenue</h4>';

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr nobr="true" style="'.$table_row_style.'">
    <th width="50%">Service</th>
    <th width="10%">Fee</th>
    <th width="10%">Admin Fee</th>
    <th width="10%">Final Fee</th>
    <th width="10%">Total App.</th>
    <th width="10%">Revenue</th>';
    $report_data .= "</tr>";
    $total_base_service = 0;
    $total_base_fee = 0;
    $final_total_appt = 0;
    $final_comp = 0;
    $final_total_fee = 0;
    $final_af = 0;
    $final_ff = 0;
    foreach ($occurences as $key => $total_appt) {
        $final_serviceid = rtrim($key,',');

        $fee = get_all_from_service($dbc, $final_serviceid, 'fee');
        $admin_price = get_all_from_service($dbc, $final_serviceid, 'admin_price');
        $final_fee = ($fee-$admin_price);
        $service_fee = ($final_fee*$total_appt);
        $base_pay_perc = $base_pay[0];
        $comp_pay = ($base_pay_perc*0.01*$service_fee);

        if(($final_serviceid == 47 && $therapistid == 6908) || ($final_serviceid == 62)) {

        } else {
            $report_data .= '<tr nobr="true">';
            $report_data .= '<td>'.get_all_from_service($dbc, $final_serviceid, 'service_code').' - '.get_all_from_service($dbc, $final_serviceid, 'heading').'</td>';
            $report_data .= '<td>$'.$fee.'</td>';
            $report_data .= '<td>-$'.$admin_price.'</td>';
            $report_data .= '<td>= $'.$final_fee.'</td>';

            //$report_data .= '<td>'.$base_pay_perc.'% : X'.($base_pay_perc/100).'</td>';
            //$report_data .= '<td>= $'.number_format(($final_fee*($base_pay_perc/100)), 2).'</td>';

            $report_data .= '<td>X'.$total_appt.'</td>';

            $report_data .= '<td>$'.number_format(($final_fee*$total_appt), 2).'</td>';

            $report_data .= '</tr>';
        }
        $final_total_appt += $total_appt;
        $total_base_service += ($final_fee*$total_appt);
        $final_total_fee += $fee;
        $final_af += $admin_price;
        $final_ff += $final_fee;
    }
    $report_data .= '<tr nobr="true">';
    $report_data .= '<td colspan="1"><b>Total : '.get_contact($dbc, $therapistid).'</b></td>
    <td><b>$' . number_format($final_total_fee, 2) . '</b></td>
    <td><b>-$' . number_format($final_af, 2) . '</b></td>
    <td><b>=$' . number_format($final_ff, 2) . '</b></td>
    <td><b>X' . $final_total_appt . '</b></td>
    <td><b>$' . number_format($total_base_service, 2) . '</b></td>';
    $report_data .= "</tr>";
    $report_data .= '</table><br>';
}
*/


//$report_validation = mysqli_query($dbc,"SELECT *, SUM(quantity) AS count FROM invoice_lines WHERE `category`='service' AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice_compensation` WHERE therapistsid='$therapistid' AND (service_date >= '$starttime' AND service_date <= '$endtime')) GROUP BY item_id, unit_price, admin_fee");

$report_validation = mysqli_query($dbc, "SELECT il.item_id, il.unit_price, il.admin_fee, ic.qty count FROM invoice_lines il, invoice_compensation ic WHERE (il.invoiceid=ic.invoiceid AND il.item_id=ic.item_id) AND ic.contactid='$therapistid' AND (ic.service_date BETWEEN '$starttime' AND '$endtime') AND `ic`.`item_type`='services'");

$report_data .= '<h4>'.get_contact($dbc, $therapistid).' -  Services Revenue</h4>';
$report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
$report_data .= '<tr nobr="true" style="'.$table_row_style.'">
<th width="50%">Service</th>
<th width="10%">Fee</th>
<th width="10%">Admin Fee</th>
<th width="10%">Final Fee</th>
<th width="10%">Total App.</th>
<th width="10%">Revenue</th>';
$report_data .= "</tr>";

$total_base_service = 0;
$final_total_appt = 0;

while($row_tab = mysqli_fetch_array($report_validation)) {
    $serviceid = $row_tab['item_id'];

    $fee = $row_tab['unit_price'];
    $admin_price = $row_tab['admin_fee'];
    $final_fee = ($fee-$admin_price);
	$code = get_all_from_service($dbc, $serviceid, 'service_code');
	$heading = get_all_from_service($dbc, $serviceid, 'heading');
	
    $report_data .= '<tr nobr="true">';
    $report_data .= '<td>'.$code.($code != '' && $heading != '' ? ' - ' : '').$heading.'</td>';
    $report_data .= '<td>$'.$fee.'</td>';
    $report_data .= '<td>-$'.$admin_price.'</td>';
    $report_data .= '<td>= $'.$final_fee.'</td>';
    $report_data .= '<td>X'.$row_tab['count'].'</td>';
    $report_data .= '<td>$'.number_format(($final_fee*$row_tab['count']), 2).'</td>';
    $report_data .= '</tr>';

    $final_total_appt += $row_tab['count'];
    $total_base_service += ($final_fee*$row_tab['count']);
}

$report_data .= '<tr nobr="true">';
$report_data .= '<td colspan="1">Total : '.get_contact($dbc, $therapistid).'</td><td></td><td></td><td></td>';
$report_data .= '<td>' . $final_total_appt . '</td>';
$report_data .= '<td>$' . number_format($total_base_service, 2) . '</td>';
$report_data .= "</tr>";
$report_data .= '</table><br>';