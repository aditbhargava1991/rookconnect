<?php // Get the updated compensation details
$report_validation = mysqli_query($dbc,"SELECT `item_type`,`item_id`,`fee`,`admin_fee`,`comp_percent`,SUM(`compensation`) `comp`,SUM(`qty`) `qty`,GROUP_CONCAT(`invoiceid`) `invoices` FROM invoice_compensation WHERE contactid='$therapistid' AND (service_date >= '$starttime' AND service_date <= '$endtime') AND invoiceid IN (SELECT `invoiceid` FROM `invoice` WHERE `invoice_type` IN ($invoicetype)) GROUP BY `item_type`, `item_id`, `fee`, `admin_fee`, `comp_percent`");
if($report_validation->num_rows > 0) {
    $comp_entered = true;
    $report_data .= '<h4>'.get_contact($dbc, $therapistid).' -  Compensation</h4>';
    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr nobr="true" style="'.$table_row_style.'">';
    if(in_array('therapist_patient_info',$report_fields)) {
        $purchaser_config = explode(',',get_config($dbc, 'invoice_purchase_contact'));
        $purchaser_label = count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0];
        $report_data .= '<th width="33%">Description</th>';
        $report_data .= '<th width="20%">'.$purchaser_label.'</th>';
    } else {
        $report_data .= '<th width="53%">Description</th>';
    }
    $report_data .= '<th width="7%">Fee</th>
    <th width="7%">Portion for Compensation</th>
    <th width="8%">Comp %</th>
    <th width="7%">Compensation Amount</th>
    <th width="5%">Quantity</th>
    <th width="7%">Total Compensation</th>';
    while($report_row = $report_validation->fetch_assoc()) {
        $item = $report_row['item_id'];
        $item_type = $report_row['item_type'];
        $base_pay_perc = 50;//$report_row['comp_percent'] < 100 ? $report_row['comp_percent'] : $base_pay[0];
        $final_fee = $report_row['fee']-$report_row['admin_fee'];
        $total_fee = ($final_fee*($base_pay_perc/100))*$report_row['qty'];
        $description = '';
        switch($report_row['item_type']) {
            case "services": $description .= "<b>Service</b>: ".implode(': ',array_filter(get_field_value('category heading name','services','serviceid',$report_row['item_id']))); break;
            case "inventory": $description .= "<b>Inventory</b>: ".implode(': ',array_filter(get_field_value('category product_name name','inventory','inventoryid',$report_row['item_id']))); break;
            case "product": $description .= "<b>Product</b>: ".implode(': ',array_filter(get_field_value('category heading','products','productid',$report_row['item_id']))); break;
            case "equipment": $description .= "<b>Equipment</b>: ".implode(': ',array_filter(get_field_value('category make model label unit_number','equipment','equipmentid',$report_row['item_id']))); break;
            case "labour": $description .= "<b>Labour</b>: ".implode(': ',array_filter(get_field_value('category labour_type heading name','labour','labour_id',$report_row['item_id']))); break;
            case "material": $description .= "<b>Material</b>: ".implode(': ',array_filter(get_field_value('category sub_category name','material','materialid',$report_row['item_id']))); break;
            case "vpl": $description .= "<b>Vendor Pricelist"; break;
            case "position": $description .= "<b>Position</b>: ".get_field_value('name','positions','position_id',$report_row['item_id']); break;
            case "staff": $description .= "<b>Staff</b>: ".get_contact($dbc, $report_row['item_id']); break;
            case "misc": $description .= "<b>Miscellaneous Item</b>"; break;
            case "ticket": $description .= "<b>".TICKET_NOUN."</b>: ".get_ticket_label($dbc, $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='".$report_row['item_id']."' AND `ticketid` > 0")->fetch_assoc()); break;
            case "hourly": $description .= "<b>Per Hour</b>"; break;
        }
        switch($report_row['item_type']) {
            case "services": $invoice_summary_total["Services"] += $total_fee; break;
            case "inventory": $invoice_summary_total["Inventory"] += $total_fee; break;
            case "product": $invoice_summary_total["Products"] += $total_fee; break;
            case "equipment": $invoice_summary_total["Equipment"] += $total_fee; break;
            case "labour": $invoice_summary_total["Labour"] += $total_fee; break;
            case "material": $invoice_summary_total["Materials"] += $total_fee; break;
            case "vpl": $invoice_summary_total["Vendor Pricelist"] += $total_fee; break;
            case "position": $invoice_summary_total["Positions"] += $total_fee; break;
            case "staff": $invoice_summary_total["Staff"] += $total_fee; break;
            case "misc": $invoice_summary_total["Miscellaneous Items"] += $total_fee; break;
            case "ticket": $invoice_summary_total[TICKET_TILE] += $total_fee; break;
            case "hourly": $invoice_summary_total["Per Hour"] += $total_fee; break;
        }

        $report_data .= '<tr nobr="true">';
        $report_data .= '<td>'.$description.'</td>';
        if(in_array('therapist_patient_info',$report_fields)) {
            $report_data .= '<td>';
            $customers = [];
            foreach(array_unique(explode(',',$report_row['invoices'])) as $invoiceid) {
                $customers[] = get_field_value('patientid','invoice','invoiceid',$invoiceid);
            }
            foreach(array_unique(array_filter($customers)) as $customer) {
                $customer = sort_contacts_query($dbc->query("SELECT `category`,`contactid`,`first_name`,`last_name`,`name` FROM `contacts` WHERE `contactid`='$customer'"));
                $report_data .= '<a href="../Contacts/contacts_inbox.php?edit='.$customer['contactid'].'&category='.$customer['category'].'" onclick="overlayIFrameSlider(this.href,\'auto\',true,true); return false;>'.$customer['full_name'].'</a><br />';
            }
            $report_data .= '</td>';
        }
        $report_data .= '<td>$'.number_format($report_row['fee'],2).'</td>';
        $report_data .= '<td>= $'.number_format($final_fee,2).'</td>';
        $report_data .= '<td>'.$base_pay_perc.'% : X'.($base_pay_perc/100).'</td>';
        $report_data .= '<td>= $'.number_format(($final_fee*($base_pay_perc/100)), 2).'</td>';
        $report_data .= '<td>'.$report_row['qty'].'</td>';
        $report_data .= '<td>$'.number_format($total_fee, 2).'</td>';
        $report_data .= '</tr>';

        $grand_total += $total_fee;
    }
    $report_data .= '</table><br />';
}