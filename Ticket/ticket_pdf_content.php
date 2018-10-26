<?php ob_clean();

$_GET['edit'] = $ticketid;
unset($ticketid);
$force_readonly = true;

$pdf_header_color = get_calendar_today_color($dbc);

$global_value_config = $value_config;

$generate_pdf = true;
$pdf_contents = [];
foreach($sort_order as $sort_field) {
    $value_config = $global_value_config;

    //Custom accordions
    if(strpos($value_config, ','.$sort_field.',') !== FALSE && substr($sort_field, 0, strlen('FFMCUST_')) === 'FFMCUST_') {
        $_GET['tab'] = str_replace(' ','_',$sort_field);
        $acc_label = explode('FFMCUST_',$sort_field)[1];
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Information".',') !== FALSE && $sort_field == 'Information') {
        $_GET['tab'] = 'project_info';
        $acc_label = PROJECT_NOUN.' Information';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Details".',') !== FALSE && $sort_field == 'Details') {
        $_GET['tab'] = 'project_details';
        $acc_label = PROJECT_NOUN.' Details';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Path & Milestone".',') !== FALSE && $sort_field == 'Path & Milestone') {
        $_GET['tab'] = 'ticket_path_milestone';
        $acc_label = PROJECT_NOUN.' Path & Milestone';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Individuals".',') !== FALSE && $sort_field == 'Individuals') {
        $_GET['tab'] = 'ticket_individuals';
        $acc_label = 'Individuals Present';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Fees".',') !== FALSE && $sort_field == 'Fees') {
        $_GET['tab'] = 'ticket_fees';
        $acc_label = 'Fees';
        include('../Ticket/edit_ticket_tab.php');
    }
    if ((strpos($value_config, ','."Location".',') !== FALSE || strpos($value_config, ','."Emergency".',') !== FALSE) && $sort_field == 'Location') {
        $_GET['tab'] = 'ticket_location';
        $acc_label = 'Site';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Members ID".',') !== FALSE && $sort_field == 'Members ID') {
        $_GET['tab'] = 'ticket_members_id_card';
        $acc_label = 'Members ID Card';
        include('../Ticket/edit_ticket_tab.php');
    }
    if ((strpos($value_config, ','."Mileage".',') !== FALSE || strpos($value_config, ','."Drive Time".',') !== FALSE) && $sort_field == 'Mileage') {
        $_GET['tab'] = 'ticket_mileage';
        $acc_label = strpos($value_config, ','."Mileage".',') !== FALSE ? 'Mileage' : 'Drive Time';
        include('../Ticket/edit_ticket_tab.php');
    }
    if(strpos($value_config, ',Staff,') !== FALSE && $sort_field == 'Staff') {
        $_GET['tab'] = 'ticket_staff_list';
        $acc_label = 'Staff';
        include('../Ticket/edit_ticket_tab.php');
    }
    if(strpos($value_config, ',Staff Tasks,') !== FALSE && $sort_field == 'Staff Tasks') {
        if($ticketid > 0 && $_GET['new_ticket'] != 'true') {
            $_GET['tab'] = 'ticket_staff_tasks';
            $acc_label = 'Staff Tasks';
            include('../Ticket/edit_ticket_tab.php');
            $collapse_i++;
        }
    }
    if(strpos($value_config, ',Members,') !== FALSE && $sort_field == 'Members') {
        $_GET['tab'] = 'ticket_members';
        $acc_label = 'Members';
        include('../Ticket/edit_ticket_tab.php');
    }
    if(strpos($value_config, ',Clients,') !== FALSE && $sort_field == 'Clients') {
        $_GET['tab'] = 'ticket_clients';
        $acc_label = 'Clients';
        include('../Ticket/edit_ticket_tab.php');
    }
    if(strpos($value_config, ',Wait List,') !== FALSE && $sort_field == 'Wait List') {
        $_GET['tab'] = 'ticket_wait_list';
        $acc_label = 'Wait List';
        include('../Ticket/edit_ticket_tab.php');
    }
    if ((strpos($value_config, ','."Check In".',') !== FALSE || strpos($value_config, ','."Check In Member Drop Off".',') !== FALSE) && $sort_field == 'Check In') {
        $_GET['tab'] = 'ticket_checkin';
        $acc_label = strpos($value_config, ','."Check In Member Drop Off".',') !== FALSE ? 'Member Drop Off' : 'Check In';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Medication".',') !== FALSE && $access_medication === TRUE && $sort_field == 'Medication') {
        $_GET['tab'] = 'ticket_medications';
        $acc_label = 'Medication Administration';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Ticket Details".',') !== FALSE && $sort_field == 'Ticket Details') {
        $_GET['tab'] = 'ticket_info';
        $acc_label = TICKET_NOUN.' Details';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Services".',') !== FALSE && $sort_field == 'Ticket Details') {
        $_GET['tab'] = 'ticket_info';
        $acc_label = 'Services';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Equipment".',') !== FALSE && $sort_field == 'Equipment') {
        $_GET['tab'] = 'ticket_equipment';
        $acc_label = 'Equipment';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Checklist".',') !== FALSE && $access_all > 0 && $sort_field == 'Checklist') {
        $_GET['tab'] = 'ticket_checklist';
        $acc_label = 'Checklist';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Checklist Items".',') !== FALSE && $access_all > 0 && $sort_field == 'Checklist Items') {
        $_GET['tab'] = 'ticket_view_checklist';
        $acc_label = 'Checklist Items';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Charts".',') !== FALSE && $access_all > 0 && $sort_field == 'Charts') {
        $_GET['tab'] = 'ticket_view_charts';
        $acc_label = 'Charts';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Safety".',') !== FALSE && $access_all > 0 && $sort_field == 'Safety') {
        $_GET['tab'] = 'ticket_safety';
        $acc_label = 'Safety Checklist';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Materials".',') !== FALSE && $sort_field == 'Materials') {
        $_GET['tab'] = 'ticket_materials';
        $acc_label = 'Materials';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ',Miscellaneous') !== FALSE && $sort_field == 'Miscellaneous') {
        $_GET['tab'] = 'ticket_miscellaneous';
        $acc_label = 'Miscellaneous';
        include('../Ticket/edit_ticket_tab.php');
        $collapse_i++;
    }
    if (strpos($value_config, ',Inventory Basic') !== FALSE && $sort_field == 'Inventory') {
        $_GET['tab'] = 'ticket_inventory';
        $acc_label = 'Inventory';
        include('../Ticket/edit_ticket_tab.php');
        $collapse_i++;
    }
    if (strpos($value_config, ',Inventory General,') !== FALSE && $sort_field == 'Inventory General') {
        $_GET['tab'] = 'ticket_inventory_general';
        $acc_label = 'General Cargo / Inventory Information';
        include('../Ticket/edit_ticket_tab.php');
        $collapse_i++;
    }
    if (strpos($value_config, ',Inventory Detail,') !== FALSE && $sort_field == 'Inventory Detail') {
        $_GET['tab'] = 'ticket_inventory_detailed';
        $acc_label = 'Detailed Cargo / Inventory Information';
        include('../Ticket/edit_ticket_tab.php');
        $collapse_i++;
    }
    if (strpos($value_config, ',Inventory Return,') !== FALSE && $sort_field == 'Inventory Return') {
        $_GET['tab'] = 'ticket_inventory_return';
        $acc_label = 'Return Information';
        include('../Ticket/edit_ticket_tab.php');
        $collapse_i++;
    }
    if (strpos($value_config, ','."Purchase Orders".',') !== FALSE && $access_all > 0 && $sort_field == 'Purchase Orders') {
        $_GET['tab'] = 'ticket_purchase_orders';
        $acc_label = 'Purchase Orders';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Attached Purchase Orders".',') !== FALSE && $access_all > 0 && $sort_field == 'Attached Purchase Orders') {
        $_GET['tab'] = 'ticket_attach_purchase_orders';
        $acc_label = 'Purchase Orders';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Delivery".',') !== FALSE && $sort_field == 'Delivery') {
        $_GET['tab'] = 'ticket_delivery';
        $acc_label = 'Delivery Details';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ',Transport Origin') !== FALSE && $sort_field == 'Transport') {
        $_GET['tab'] = 'ticket_transport_origin';
        $acc_label = 'Transport Log - Origin';
        include('../Ticket/edit_ticket_tab.php');
        $collapse_i++;
    }
    if (strpos($value_config, ',Transport Destination') !== FALSE && $sort_field == 'Transport') {
        $_GET['tab'] = 'ticket_transport_destination';
        $acc_label = 'Transport Log - Destination';
        include('../Ticket/edit_ticket_tab.php');
        $collapse_i++;
    }
    if (strpos(str_replace(['Transport Origin','Transport Destination'],'',$value_config), ',Transport ') !== FALSE && $sort_field == 'Transport') {
        $_GET['tab'] = 'ticket_transport_details';
        $acc_label = 'Carrier Details';
        include('../Ticket/edit_ticket_tab.php');
        $collapse_i++;
    }
    if (strpos($value_config, ','."Documents".',') !== FALSE && $sort_field == 'Documents') {
        $_GET['tab'] = 'view_ticket_documents';
        $acc_label = 'Documents';
        include('../Ticket/edit_ticket_tab.php');
    }
    if ((strpos($value_config, ','."Check Out".',') !== FALSE || strpos($value_config, ','."Check Out Member Pick Up".',') !== FALSE) && $sort_field == 'Check Out') {
        $_GET['tab'] = 'ticket_checkout';
        $acc_label = strpos($value_config, ','."Check In Member Pick Up".',') !== FALSE ? 'Member Pick Up' : 'Check Out';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Staff Check Out".',') !== FALSE && $sort_field == 'Staff Check Out') {
        $_GET['tab'] = 'ticket_checkout_staff';
        $acc_label = 'Staff Check Out';
        include('../Ticket/edit_ticket_tab.php');
    }
    if ((strpos($value_config, ','."Deliverables".',') !== FALSE || strpos($value_config, ','."Deliverable To Do".',') !== FALSE || strpos($value_config, ','."Deliverable Internal".',') !== FALSE || strpos($value_config, ','."Deliverable Customer".',') !== FALSE) && $sort_field == 'Deliverables') {
        $_GET['tab'] = 'view_ticket_deliverables';
        $acc_label = 'Deliverables';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Timer".',') !== FALSE && $sort_field == 'Timer') {
        $_GET['tab'] = 'view_ticket_timer';
        $acc_label = 'Time Tracking';
        include('../Ticket/edit_ticket_tab.php');
        $collapse_i++;
    }
    if (strpos($value_config, ','."Timer".',') !== FALSE && $access_all > 0 && $sort_field == 'Timer') {
        $_GET['tab'] = 'view_day_tracking';
        $acc_label = 'Day Tracking';
        include('../Ticket/edit_ticket_tab.php');
        $collapse_i++;
    }
    if (strpos($value_config, ','."Addendum".',') !== FALSE && $sort_field == 'Addendum') {
        $_GET['tab'] = 'addendum_view_ticket_comment';
        $acc_label = 'Addendum Notes';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Client Log".',') !== FALSE && $sort_field == 'Client Log') {
        $_GET['tab'] = 'ticket_log_notes';
        $acc_label = 'Staff Log Notes';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Debrief".',') !== FALSE && $sort_field == 'Debrief') {
        $_GET['tab'] = 'debrief_view_ticket_comment';
        $acc_label = 'Debrief Notes';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Member Log Notes".',') !== FALSE && $sort_field == 'Member Log Notes') {
        $category = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `category` FROM `contacts` WHERE `category` NOT IN (".STAFF_CATS.",'Business','Sites') AND `deleted`=0 AND `status`>0 GROUP BY `category` ORDER BY COUNT(*) DESC"))['category'];
        $_GET['tab'] = 'member_view_ticket_comment';
        $acc_label = $category.' Daily Log Notes';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Cancellation".',') !== FALSE && $sort_field == 'Cancellation') {
        $_GET['tab'] = 'ticket_cancellation';
        $acc_label = 'Cancellation';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Custom Notes".',') !== FALSE && $sort_field == 'Custom Notes') {
        $_GET['tab'] = 'custom_view_ticket_comment';
        $custom_note_labels = get_config($dbc, 'ticket_custom_notes_type');
        foreach(explode('#*#',$custom_note_labels) as $custom_comment_types) {
            $acc_label = $custom_comment_types;
            $custom_comment_types = [$custom_comment_types];
            include('../Ticket/edit_ticket_tab.php');
        }
    }
    if (strpos($value_config, ','."Internal Communication".',') !== FALSE && $sort_field == 'Internal Communication') {
        $_GET['tab'] = 'internal_communication';
        $acc_label = 'Internal Communication';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."External Communication".',') !== FALSE && $sort_field == 'External Communication') {
        $_GET['tab'] = 'external_communication';
        $acc_label = 'External Communication';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Notes".',') !== FALSE && $sort_field == 'Notes') {
        $_GET['tab'] = 'notes_view_ticket_comment';
        $acc_label = TICKET_NOUN.' Notes';
        include('../Ticket/edit_ticket_tab.php');
    }
    if ((strpos($value_config, ','."Summary".',') !== FALSE || strpos($value_config, ','."Staff Summary".',') !== FALSE) && $sort_field == 'Summary') {
        $_GET['tab'] = 'ticket_summary';
        $acc_label = strpos($value_config, ','."Staff Summary".',') !== FALSE ? 'Staff Summary' : 'Summary';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Multi-Disciplinary Summary Report".',') !== FALSE && $sort_field == 'Multi-Disciplinary Summary Report') {
        $_GET['tab'] = 'view_multi_disciplinary_summary_report';
        $acc_label = 'Multi Disciplinary Summary Notes';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Complete".',') !== FALSE && $sort_field == 'Complete') {
        $_GET['tab'] = 'ticket_complete';
        $acc_label = 'Complete '.TICKET_NOUN;
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Notifications".',') !== FALSE && $sort_field == 'Notifications') {
        $_GET['tab'] = 'view_ticket_notifications';
        $acc_label = 'Notifications';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Region Location Classification".',') !== FALSE && $sort_field == 'Region Location Classification') {
        $_GET['tab'] = 'ticket_reg_loc_class';
        $acc_label = 'Region/Location/Classification';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Incident Reports".',') !== FALSE && $sort_field == 'Incident Reports') {
        $_GET['tab'] = 'view_ticket_incident_reports';
        $acc_label = INC_REP_TILE;
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Billing".',') !== FALSE && $sort_field == 'Billing') {
        $_GET['tab'] = 'ticket_billing';
        $acc_label = 'Billing';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Customer Notes".',') !== FALSE && $sort_field == 'Customer Notes') {
        $_GET['tab'] = 'ticket_customer_notes';
        $acc_label = 'Customer Notes';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Residue".',') !== FALSE && $sort_field == 'Residue') {
        $_GET['tab'] = 'ticket_residues';
        $acc_label = 'Residue';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Reading".',') !== FALSE && $sort_field == 'Reading') {
        $_GET['tab'] = 'ticket_readings';
        $acc_label = 'Reading';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Tank Reading".',') !== FALSE && $sort_field == 'Tank Reading') {	
        $_GET['tab'] = 'ticket_tank_readings';
        $acc_label = 'Tank Reading';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Shipping List".',') !== FALSE && $sort_field == 'Shipping List') {
        $_GET['tab'] = 'ticket_shipping_list';
        $acc_label = 'Shipping List';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Other List".',') !== FALSE && $sort_field == 'Other List') {
        $_GET['tab'] = 'ticket_other_list';
        $acc_label = 'Other List';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Pressure".',') !== FALSE && $sort_field == 'Pressure') {
        $_GET['tab'] = 'ticket_pressure';
        $acc_label = 'Pressure';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Chemicals".',') !== FALSE && $sort_field == 'Chemicals') {
        $_GET['tab'] = 'ticket_chemicals';
        $acc_label = 'Chemicals';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Intake".',') !== FALSE && $sort_field == 'Intake') {
        $_GET['tab'] = 'ticket_intake';
        $acc_label = 'Intake';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."History".',') !== FALSE && $sort_field == 'History') {
        $_GET['tab'] = 'ticket_history';
        $acc_label = 'History';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Work History".',') !== FALSE && $sort_field == 'Work History') {
        $_GET['tab'] = 'ticket_work_history';
        $acc_label = 'Work History';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Service Staff Checklist".',') !== FALSE && $sort_field == 'Service Staff Checklist') {
        $_GET['tab'] = 'ticket_service_checklist';
        $acc_label = 'Service Checklist';
        include('../Ticket/edit_ticket_tab.php');
    }
    if (strpos($value_config, ','."Service Extra Billing".',') !== FALSE && $sort_field == 'Service Extra Billing') {
        $_GET['tab'] = 'ticket_service_extra_billing';
        $acc_label = 'Service Extra Billing';
        include('../Ticket/edit_ticket_tab.php');
    }
}
ob_clean();
$html .= '<h1>'.get_ticket_label($dbc, mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"))).'</h1>';
$html .= '<table border="1" cellpadding="2">';
$htmls = [];
foreach($pdf_contents as $key => $line) {
    $line_html = '';
    $img = $line[2];
    $header = $line[0];
    $line = $line[1];
    if($header == '**HEADING**') {
        end($htmls);
        if($hide_blank_fields && explode('#*#',key($htmls))[0] == '**HEADING**') {
            array_pop($htmls);
        }
        $htmls[$header.'#*#'.$key] = '<tr><td class="pdf_header" style="text-align: center; background-color: #'.$pdf_header_color.'; color: #fff; font-weight: bold; width: 100%; border: 1px solid black;">'.$line.'</td></tr>';
    } else if (!empty(trim(strip_tags($line[1]))) || !$hide_blank_fields) {
        if($img != 'img') {
            $line = preg_replace('/<img((?!>).)*>/','',$line);
        }
        $line = preg_replace('/<button((?!\/button>).)*\/button>/','',$line);
        if(strpos($line,'form-group')) {
            $line = str_replace('class="form-group','style="margin:0;padding:0;display:block;width:900px;" class="',$line);
        } else {
            $line = str_replace('div','span',$line);
        }
        $line = str_replace('class="form-control"','style="width:100%;"',$line);
        $line = str_replace('class="form-control datepicker"','style="width:100%;"',$line);
        $line = str_replace('select ','select style="width:100%;"',$line);
        $line = str_replace('textarea','div',$line);
        $line = str_replace('class="col-sm-4','style="width:25%;" class="',$line);
        $line = str_replace('class="control-label col-sm-4','style="width:25%;display:inline-block;" class="',$line);
        $line = str_replace('class="col-sm-8','style="width:74%;" class="',$line);

        $width = 100;
        $line_html .= '<tr>';
        if(!empty($header)) {
            $width = 70;
            $line_html .= '<td class="pdf_label" style="width: 30%; border: 1px solid black; display: inline;">'.$header.'</td>';
        }
        $line_html .= '<td class="pdf_content" style="width: '.$width.'%; border: 1px solid black; display: inline;">'.($ticketid>0 ? $line : '').'</td>';
        $line_html .= '</tr>';
        if(!empty(trim(strip_tags($line))) || !$hide_blank_fields) {
            $htmls[$header.'#*#'.$key] = $line_html;
        }
    }
}
end($htmls);
if($hide_blank_fields && explode('#*#',key($htmls))[0] == '**HEADING**') {
    array_pop($htmls);
}
$html .= implode('',$htmls);
$html .= '</table>';