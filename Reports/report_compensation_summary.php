<?php
    $vacation_pay = (($total_base_service+$total_base_inv)*$vacation_pay_perc)/100;
    $gt = $total_base_service+$total_base_inv;

    //<th>Vacation Pay</th>

    $report_data .= '<h4>'.get_contact($dbc, $therapistid).' -  Summary</h4>';
    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'" nobr="true">';
    $report_data .= '<th>Service Compensation</th><th>Inventory Compensation</th><th>Gross Compensation</th><th>Statutory Holiday Compensation</th>'.($vacation_pay > 0 ? '<th>Vacation Pay</th>' : '').'<th>Final Compensation</th></tr>';
    $report_data .= '<tr>';
    $report_data .= '<td>$' . number_format($total_base_service, 2) . '</td>';
    //$report_data .= '<td>$' . number_format($final_metrix, 2).'</td>';
    $report_data .= '<td>$' . number_format($total_base_inv, 2) . '</td>';
    $report_data .= '<td>$' . number_format($gt, 2) . '</td>';
    $report_data .= '<td>';
    //$report_data .= '('.$grand_stat_total.'/'.$days_worked.') = ';
    $report_data .= '<a href="'.WEBSITE_URL.'/Reports/report_stat_holiday_pay.php?type=compensation&contactid='.$therapistid.'&start='.$starttime.'&end='.$endtime.'">$' . number_format($avg_per_day_stat, 2) . '</a></td>';
    $report_data .= ($vacation_pay > 0 ? '<td>$' . number_format($vacation_pay, 2) . '</td>' : '');
    $report_data .= '<td>$' . number_format(($gt+$avg_per_day_stat+$vacation_pay), 2) . '</td>';
    $report_data .= '</tr>';
    $report_data .= '</table><br>';