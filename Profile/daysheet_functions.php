<?php //Daysheet functions
function daysheet_ticket_label ($dbc, $daysheet_ticket_fields, $ticket, $status_complete, $daily_date) {
    $contactid = $_SESSION['contactid'];
    //Label stuff
    $label = '';
    if($ticket['businessid'] > 0 && in_array('Business', $daysheet_ticket_fields)) {
        $label .= get_client($dbc, $ticket['businessid']).'<br />';
    }
    if($ticket['projectid'] > 0 && in_array('Project', $daysheet_ticket_fields)) {
        if(!empty($label)) {
            $label .= ', ';
        }
        $label .= PROJECT_NOUN.' #'.$ticket['projectid'].' '.get_project($dbc,$ticket['projectid'],'project_name').'<br />';
    }
    $label .= get_ticket_label($dbc, $ticket);
    if(($ticket['delivery_type'] == 'warehouse') && in_array('Warehouse Indicator', $daysheet_ticket_fields)) {
        $label .= '<br />Warehouse';
    }
    if((trim($ticket['contactid'],',') != '' || trim($ticket['internal_qa_contactid'],',') != '' || trim($ticket['deliverable_contactid'],',') != '') && in_array('Staff', $daysheet_ticket_fields)) {
        $staff_labels = [];
        foreach(array_filter(explode(',',$ticket['contactid'])) as $staff) {
            $staff_labels[] = get_contact($dbc, $staff);
        }
        foreach(array_filter(explode(',',$ticket['internal_qa_contactid'])) as $staff) {
            $staff_labels[] = get_contact($dbc, $staff).' (Internal QA)';
        }
        foreach(array_filter(explode(',',$ticket['deliverable_contactid'])) as $staff) {
            $staff_labels[] = get_contact($dbc, $staff).' (Deliverable)';
        }
        $label .= '<br />Staff: '.implode(', ', $staff_labels);
    }
    if(($ticket['clientid'] > 0 || !empty($ticket['client_name'])) && in_array('Customer', $daysheet_ticket_fields)) {
        if($ticket['ticket_table'] == 'ticket_schedule') {
            $label .= '<br />Customer: '.$ticket['client_name'];
        } else {
            $label .= '<br />Customer: '.get_contact($dbc, $ticket['clientid']);
        }
    }
    if($ticket['service_templateid'] > 0 && in_array('Service Template', $daysheet_ticket_fields)) {
        $service_template = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` FROM `services_service_templates` WHERE `templateid` = '".$ticket['service_templateid']."'"))['name'];
        $label .= '<br />Service Template: '.$service_template;
    }
    if(!empty($ticket['delivery_type']) && in_array('Delivery Type', $daysheet_ticket_fields)) {
        $label .= '<br />Delivery Type: '.ucfirst($ticket['delivery_type']);
    }
    if(!empty($ticket['address']) && in_array('Address', $daysheet_ticket_fields)) {
        $label .= '<br />Address: '.$ticket['address'];
    }
    $site = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '".$ticket['siteid']."' AND '".$ticket['siteid']."' > 0"));
    if((!empty($ticket['address']) || !empty($ticket['map_link']) || !empty($site['address']) || !empty($site['mailing_address']) || !empty($site['google_maps_address'])) && in_array('Map Link', $daysheet_ticket_fields)) {
        $map_link = json_encode(!empty($ticket['map_link']) ? $ticket['map_link'] : 'http://maps.google.com/maps/place/'.$ticket['address'].','.$ticket['city']);
        if(empty($ticket['map_link']) && empty($ticket['address'])) {
            $map_link = !empty($site['google_maps_address']) ? $site['google_maps_address'] : 'http://maps.google.com/maps/place/'.(!empty($site['address']) ? $site['address'] : $site['mailing_address']).','.$site['city'];
        }
        $label .= '<br />Google Maps Link: <span onclick="googleMapsLink(this);" data-href=\''.$map_link.'\'><u class="no-slider">Click Here</u></span>';
    }
    if(!empty($ticket['to_do_start_time']) && in_array('Start Time', $daysheet_ticket_fields)) {
        $label .= '<br />Time: '.date('h:i a', strtotime($ticket['to_do_start_time']));
    }
    if(!empty($ticket['eta']) && in_array('ETA', $daysheet_ticket_fields)) {
        $label .= '<br />ETA: '.$ticket['eta'];
    }
    if(!empty($ticket['availability']) && $ticket['availability'] != '00:00:00 - 00:00:00' && in_array('Availability', $daysheet_ticket_fields)) {
        $label .= '<br />Availability: '.$ticket['availability'];
    }

    if(in_array('Site Address', $daysheet_ticket_fields) && $ticket['siteid'] > 0) {
        $site = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '".$ticket['siteid']."'"));
        if(!empty($site['address'])) {
            $label .= '<br />Site Address: '.html_entity_decode($site['address']);
        }
    }

    if(in_array('Site Notes', $daysheet_ticket_fields) && $ticket['siteid'] > 0) {
        $site = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts_description` WHERE `contactid` = '".$ticket['siteid']."'"));
        if(!empty($site['notes'])) {
            $label .= '<br />Site Notes: '.html_entity_decode($site['notes']);
        }
    }

    if(in_array('Key Number', $daysheet_ticket_fields) && $ticket['siteid'] > 0) {
        $site = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `key_number` FROM `contacts` WHERE `contactid` = '".$ticket['siteid']."' AND '".$ticket['siteid']."' > 0"));
        if(!empty($site['key_number'])) {
            $label .= '<br />Key Number: '.$site['key_number'];
        }
    }

    if(in_array('Door Code Number', $daysheet_ticket_fields) && $ticket['siteid'] > 0) {
        $site = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `door_code_number` FROM `contacts` WHERE `contactid` = '".$ticket['siteid']."' AND '".$ticket['siteid']."' > 0"));
        if(!empty($site['door_code_number'])) {
            $label .= '<br />Door Code Number: '.$site['door_code_number'];
        }
    }

    if(in_array('Alarm Code Number', $daysheet_ticket_fields) && $ticket['siteid'] > 0) {
        $site = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `alarm_code_number` FROM `contacts` WHERE `contactid` = '".$ticket['siteid']."' AND '".$ticket['siteid']."' > 0"));
        if(!empty($site['alarm_code_number'])) {
            $label .= '<br />Alarm Code Number: '.$site['alarm_code_number'];
        }
    }

    //Timer stuff
    $total_minutes = 0;
    $ticket_timer = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_timer` WHERE `ticketid` = '".$ticket['ticketid']."' AND `created_by` = '".$contactid."' AND `timer_type` != 'Break' AND `deleted` = 0"),MYSQLI_ASSOC);
    foreach ($ticket_timer as $timer) {
        $hours = intval(explode(':', $timer['timer'])[0]);
        $minutes = intval(explode(':', $timer['timer'])[1]);
        $total_minutes += ($hours * 60) + $minutes;
    }
    $total_time = sprintf('%02d:%02d', (floor($total_minutes / 60)), ($total_minutes % 60));

    //Status stuff
    $user_status = $ticket['status'];
    if(strpos(','.$ticket['internal_qa_contactid'].',', ','.$contactid.',') !== FALSE && $ticket['status'] != 'Internal QA') {
        $user_status = 'Internal QA';
    } else if(strpos(','.$ticket['internal_qa_contactid'].',', ','.$contactid.',') === FALSE && $ticket['status'] == 'Internal QA') {
        $user_status = 'To Do';
    }
    if(strpos(','.$ticket['deliverable_contactid'].',', ','.$contactid.',') !== FALSE && $ticket['status'] != 'Customer QA' && $ticket['status'] != 'Waiting On Customer') {
        $user_status = 'Customer QA';
    } else if(strpos(','.$ticket['deliverable_contactid'].',', ','.$contactid.',') === FALSE && ($ticket['status'] == 'Customer QA' || $ticket['status'] == 'Waiting On Customer')) {
        $user_status = 'To Do';
    }
    // if (($ticket['status'] != 'Customer QA' && $ticket['status'] != 'Internal QA') && ($daily_date >= $ticket['to_do_date'] && $daily_date <= $ticket['to_do_end_date']) && (strpos($ticket['contactid'], ','.$contactid.',') !== FALSE)) {
        // $user_status = $ticket['status'];
    // }
	$eta = $ticket['eta'];
    $hours = intval(explode(':', $ticket['max_time'])[0]);
    $minutes = intval(explode(':', $ticket['max_time'])[1]);
    $ticket_minutes = ($hours * 60) + $minutes;

    if ($total_minutes <= $ticket_minutes && $user_status == $ticket['status']) {
        $total_time = '<h5 style="font-weight: normal; display: inline;">'.$total_time.'</h5>';
    } else if ($total_minutes > $ticket_minutes && $user_status == $ticket['status']) {
        $total_time = '<span style="color: red;">'.$total_time.'</span>';
    }

    $opacity_styling = '';
    if ($user_status != $ticket['status']) {
        echo '<i class="status_opacity">';
        $opacity_styling = 'style="opacity: 0.5;"';
    }

    if(!empty($ticket['total_budget_time']) && in_array('Total Budget Time', $daysheet_ticket_fields)) {
        $label .= '<br />Total Budget Time: '.$ticket['total_budget_time'];
    }

    if(in_array('Service Time Estimate', $daysheet_ticket_fields)) {
        $serviceids = explode(',', $ticket['serviceid']);
        $service_qtys = explode(',', $ticket['service_qty']);

        $time_est = 0;
        foreach($serviceids as $i => $serviceid) {
            $service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid` = '$serviceid'"));
            $estimated_hours = empty($service['estimated_hours']) ? '00:00' : $service['estimated_hours'];
            $qty = empty($service_qtys[$i]) ? 1 : $service_qtys[$i];
            $minutes = explode(':', $estimated_hours);
            $minutes = ($minutes[0]*60) + $minutes[1];
            $minutes = $qty * $minutes;
            $time_est += $minutes;
        }

        if(!empty($time_est)) {
            $new_hours = $time_est / 60;
            $new_minutes = $time_est % 60;
            $new_hours = sprintf('%02d', $new_hours);
            $new_minutes = sprintf('%02d', $new_minutes);
            $time_est = $new_hours.':'.$new_minutes;

            $label .= '<br />Service Time Estimate: '.$time_est;
        }
    }

    if(in_array('Time Estimate', $daysheet_ticket_fields)) {
        $label .= '<br />'.substr($ticket['max_time'], 0, 5).'/'.$total_time;
    }

    $label = '<span>'.$label.'</span>';

    if ($user_status != $ticket['status']) {
        $label .= '&nbsp;<h5 style="font-weight: normal; font-style: italic; display: inline;">currently in '.$ticket['status'].'</h5></i>';
    } else {
        $label .= ' ('.$ticket['status'].')<br />';
    }

    $ticket_documents = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) num_rows FROM `ticket_document` WHERE `ticketid` = '".$ticket['ticketid']."' AND `deleted` = 0"))['num_rows'];
    if(in_array('Attachment Indicator', $daysheet_ticket_fields) && $ticket_documents > 0) {
    	$label .= '<img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" class="inline-img" title="'.$ticket_documents.' Attachments">';
    }

    $ticket_comments = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) num_rows FROM `ticket_comment` WHERE `ticketid` = '".$ticket['ticketid']."' AND `deleted` = 0"))['num_rows'];
    if(in_array('Comment Indicator', $daysheet_ticket_fields) && $ticket_comments > 0) {
    	$label .= '<img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" class="inline-img" title="'.$ticket_comments.' Comments">';
    }
	
    if(in_array('Details with Confirm', $daysheet_ticket_fields)) {
    	$label .= '<label class="form-checkbox"><input type="checkbox" name="status" value="'.$status_complete.'">Mark '.$status_complete.'</label>';
    }

    if(in_array('Delivery Notes', $daysheet_ticket_fields) && strip_tags(html_entity_decode($ticket['delivery_notes'])) != '') {
    	$label .= 'Notes: '.html_entity_decode($ticket['delivery_notes']);
    }

    return $label;
}