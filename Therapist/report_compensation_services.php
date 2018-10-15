<?php

//$starttime1 = $starttime.' 06:00:00';
//$endtime1 = $endtime.' 20:00:00';
//$start_time_int = strtotime($starttime1);
//$end_time_int = strtotime($endtime1);

$report_validation = mysqli_query($dbc,"SELECT *, count(*) AS count FROM invoice_compensation WHERE contactid='$therapistid' AND (service_date >= '$starttime' AND service_date <= '$endtime') GROUP BY serviceid, fee, admin_fee");

$report_data .= '<h4>'.get_contact($dbc, $therapistid).' -  Services Compensation</h4>';
$report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
$report_data .= '<tr nobr="true" style="'.$table_row_style.'">
<th width="53%">Service</th>
<th width="7%">Fee</th>
<th width="6%">Admin Fee</th>
<th width="7%">Final Fee</th>
<th width="8%">Comp%</th>
<th width="7%">Comp Fee</th>
<th width="5%">Total App.</th>
<th width="7%">Services</th>';
$report_data .= "</tr>";

$total_base_service = 0;
$final_total_appt = 0;

while($row_tab = mysqli_fetch_array($report_validation)) {
    $all_service = $row_tab['serviceid'];

    $fee = $row_tab['fee'];
    $admin_price = $row_tab['admin_fee'];

    $final_fee = ($fee-$admin_price);
    $base_pay_perc = $base_pay[0];

    $report_data .= '<tr nobr="true">';
    $report_data .= '<td>'.get_all_from_service($dbc, $all_service, 'service_code').' - '.get_all_from_service($dbc, $all_service, 'heading').'</td>';
    $report_data .= '<td>$'.$fee.'</td>';
    $report_data .= '<td>-$'.$admin_price.'</td>';
    $report_data .= '<td>= $'.$final_fee.'</td>';

    $report_data .= '<td>'.$base_pay_perc.'% : X'.($base_pay_perc/100).'</td>';
    $report_data .= '<td>= $'.number_format(($final_fee*($base_pay_perc/100)), 2).'</td>';

    $report_data .= '<td>X'.$row_tab['count'].'</td>';

    $report_data .= '<td>$'.number_format((($final_fee*($base_pay_perc/100))*$row_tab['count']), 2).'</td>';

    $report_data .= '</tr>';

    $final_total_appt += $row_tab['count'];
    $total_base_service += (($final_fee*($base_pay_perc/100))*$row_tab['count']);

    $grand_total += $total_base_service;
}

$report_data .= '<tr nobr="true">';
$report_data .= '<td colspan="1">Total : '.get_contact($dbc, $therapistid).'</td><td></td><td></td><td></td>';
$report_data .= '<td></td><td></td><td>' . $final_total_appt . '</td>';
$report_data .= '<td>$' . number_format($total_base_service, 2) . '</td>';
$report_data .= "</tr>";
$report_data .= '</table><br>';