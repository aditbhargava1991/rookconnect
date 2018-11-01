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

function daysheet_get_reminder($dbc, $daysheet_reminder) {
    $reminder_label = '';

    if ($daysheet_reminder['type'] == 'reminder') {
        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `reminders` WHERE `reminderid` = '".$daysheet_reminder['reminderid']."'"));
        $reminder_url = get_reminder_url($dbc, $reminder, 1);
        $slider = 1;
        if(empty($reminder_url)) {
            $slider = 0;
            $reminder_url = get_reminder_url($dbc, $reminder);
        }
        if(!empty($reminder_url)) {
            if($slider == 1 && $use_slider == 1) {
                $reminder_label = '<a href="" onclick="overlayIFrameSlider(\''.$reminder_url.'\'); return false;">'.$reminder['subject'].'</a>';
            } else {
                $reminder_label = '<a href="'.$reminder_url.'">'.$reminder['subject'].'</a>';
            }
        } else {
            $reminder_label = '<div class="daysheet-span">'.$reminder['subject'].'</div>';
        }
    } else if ($daysheet_reminder['type'] == 'sales') {
        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales` WHERE `salesid` = '".$daysheet_reminder['reminderid']."'"));
        $reminder_label = '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Sales/sale.php?iframe_slider=1&p=details&id='.$reminder['salesid'].'\'); return false;" style="color: black;">Follow Up Sales: Sales #'.$reminder['salesid'].'</a>';
    } else if ($daysheet_reminder['type'] == 'sales_order') {
        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order` WHERE `posid` = '".$daysheet_reminder['reminderid']."'"));
        $reminder_label = '<a href="../Sales Order/index.php?p=preview&id='.$reminder['posid'].'" style="color: black;">Follow Up '.SALES_ORDER_NOUN.': '.($reminder['name'] != '' ? $reminder['name'] : SALES_ORDER_NOUN.' #'.$reminder['posid']).'</a>';
    } else if ($daysheet_reminder['type'] == 'sales_order_temp') {
        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `sotid` = '".$daysheet_reminder['reminderid']."'"));
        $reminder_label = '<a href="../Sales Order/order.php?p=details&sotid='.$reminder['sotid'].'" style="color: black;">Follow Up '.SALES_ORDER_NOUN.': '.($reminder['name'] != '' ? $reminder['name'] : SALES_ORDER_NOUN.' Form #'.$reminder['sotid']).'</a>';
    } else if ($daysheet_reminder['type'] == 'estimate') {
        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `ea`.*, `e`.`estimate_name` FROM `estimate_actions` AS `ea` JOIN `estimate` AS `e` ON (`ea`.`estimateid`=`e`.`estimateid`) WHERE `ea`.`id` = '".$daysheet_reminder['reminderid']."'"));
        $reminder_label = '<a href="../Estimate/estimates.php?view='.$reminder['estimateid'].'" style="color: black;">Follow Up Estimate: '.$reminder['estimate_name'].'</a>';
    } else if ($daysheet_reminder['type'] == 'project') {
        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `pa`.*, `p`.`project_name` FROM `project_actions` AS `pa` JOIN `project` AS `p` ON (`pa`.`projectid`=`p`.`projectid`) WHERE `pa`.`id` = '".$daysheet_reminder['reminderid']."'"));
        $reminder_label = '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Project/projects.php?iframe_slider=1&edit='.$reminder['projectid'].'\'); return false;" style="color: black;">Follow Up Project: '.$reminder['project_name'].'</a>';
    } else if ($daysheet_reminder['type'] == 'project_followup') {
        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '".$daysheet_reminder['reminderid']."'"));
        $reminder_label = '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Project/projects.php?iframe_slider=1&edit='.$reminder['projectid'].'\'); return false;" style="color: black;">Follow Up Project: '.$reminder['project_name'].'</a>';
    } else if ($daysheet_reminder['type'] == 'certificate') {
        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `certificate` WHERE `certificateid` = '".$daysheet_reminder['reminderid']."'"));
        $reminder_label = '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Certificate/edit_certificate.php?edit='.$reminder['certificateid'].'\'); return false;" style="color: black;">Certificate Reminder: '.$reminder['title'].'</a>';
    } else if ($daysheet_reminder['type'] == 'alert') {
        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `alerts` WHERE `alertid` = '".$daysheet_reminder['reminderid']."'"));
        $reminder_label = '<a href="'.$reminder['alert_link'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" style="color: black;">Alert: '.$reminder['alert_text'].' - '.$reminder['alert_link'].'</a>';
    } else if ($daysheet_reminder['type'] == 'incident_report') {
        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `incident_report` WHERE `incidentreportid` = '".$daysheet_reminder['reminderid']."'"));
        $reminder_label = '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Incident Report/add_incident_report.php?incidentreportid='.$reminder['incidentreportid'].'\'); return false;" style="color: black;">Follow Up '.INC_REP_NOUN.': '.$reminder['type'].' #'.$reminder['incidentreportid'].'</a>';
    } else if ($daysheet_reminder['type'] == 'equipment_followup') {
        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '".$daysheet_reminder['reminderid']."'"));
        $reminder_label = '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Equipment/edit_equipment.php?edit='.$reminder['equipmentid'].'&iframe_slider=1\'); return false;" style="color: black;">Follow Up Equipment ('.$reminder['category'].' #'.$reminder['unit_number'].'): Next Service Date coming up on '.$reminder['next_service_date'].'</a>';
    } else if ($daysheet_reminder['type'] == 'equipment_service') {
        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '".$daysheet_reminder['reminderid']."'"));
        $reminder_label = '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Equipment/edit_equipment.php?edit='.$reminder['equipmentid'].'&iframe_slider=1\'); return false;" style="color: black;">Equipment Service Reminder ('.$reminder['category'].' #'.$reminder['unit_number'].'): Service Date scheduled for '.$reminder['next_service_date'].'</a>';
    } else if ($daysheet_reminder['type'] == 'incident_report_flag') {
        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `incident_report` WHERE `incidentreportid` = '".$daysheet_reminder['reminderid']."'"));
        $reminder_label = '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Incident Report/add_incident_report.php?incidentreportid='.$reminder['incidentreportid'].'\'); return false;" style="color: black;">Flagged '.INC_REP_NOUN.': '.$reminder['type'].' #'.$reminder['incidentreportid'].(!empty($reminder['flag_label']) ? ' - '.$reminder['flag_label'] : '').'</a>';
    }

    return $reminder_label;
}

function daysheet_get_tags($dbc, $contactid, $start_date = '0000-00-00', $end_date = '9999-12-31', $offset = 0, $rowsPerPage = 9999999999) {
    $daysheet_styling = $user_settings['daysheet_styling'];
    if(empty($daysheet_styling)) {
        $daysheet_styling = get_config($dbc, 'daysheet_styling');
    }
    if(empty($daysheet_styling)) {
        $daysheet_styling = 'card';
    }
    $tagged_items = mysqli_query($dbc, "SELECT * FROM `contacts_tagging` WHERE `contactid` = '$contactid' AND `deleted` = 0 AND `last_updated_date` BETWEEN '$start_date' AND '$end_date' ORDER BY `last_updated_date` DESC LIMIT $offset, $rowsPerPage");

    $html = '';
    while($row = mysqli_fetch_assoc($tagged_items)) {
        $block_html = '';
        switch($row['src_table']) {
            case 'incident_report':
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT incident_report_dashboard FROM field_config_incident_report"));
                $value_config = ','.$get_field_config['incident_report_dashboard'].',';

                $incident_report = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `incident_report` WHERE `incidentreportid` = '".$row['item_id']."'"));
                $contact_list = [];
                if ($incident_report['contactid'] != '') {
                    $contact_list[$incident_report['contactid']] = get_staff($dbc, $incident_report['contactid']);
                }
                $attendance_list = [];
                if ($incident_report['attendance_staff'] != '') {
                    $attendance_list = explode(',', $incident_report['attendance_staff']);
                }
                foreach($attendance_list as $attendee) {
                    $contact_list[] = $attendee;
                }
                if ($incident_report['completed_by'] != '') {
                    $contact_list[] = get_contact($dbc, $incident_report['completed_by']);
                }
                $contact_list = array_unique($contact_list);
                if(empty($contact_list) && $current_type == 'SAVED') {
                    $contact_list = [''];
                }
                $contact_name = implode(', ',$contact_list);

                $project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '".$incident_report['projectid']."'"));
                $project = get_project_label($dbc, $project);
                $ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$incident_report['ticketid']."'"));
                $ticket = get_ticket_label($dbc, $ticket);
                $program = (!empty(get_client($dbc, $incident_report['programid'])) ? get_client($dbc, $incident_report['programid']) : get_contact($dbc, $incident_report['programid']));
                $member_list = [];
                foreach(explode(',',$incident_report['memberid']) as $member) {
                    if($member != '') {
                        $member_list[] = !empty(get_client($dbc, $member)) ? get_client($dbc, $member) : get_contact($dbc, $member);
                    }
                }
                $member_list = implode(', ',$member_list);
                $client_list = [];
                foreach(explode(',',$incident_report['clientid']) as $client) {
                    if($client != '') {
                        $client_list[] = !empty(get_client($dbc, $client)) ? get_client($dbc, $client) : get_contact($dbc, $client);
                    }
                }
                $client_list = implode(', ',$client_list);
                $project_type = $project_vars[$incident_report['project_type']];

                $block_html .= '<a href="" onclick="untagMyself(this, \''.$row['id'].'\'); return false;" class="pull-right"><img src="../img/icons/cancel.png" class="inline-img no-toggle" title="Untag Myself"></a>';
                $block_html .= '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Incident Report/add_incident_report.php?incidentreportid='.$row['item_id'].'\'); return false;">';

                $block_html .= INC_REP_NOUN.' #'.$row['item_id'].'<br />';

                if (strpos($value_config, ','."Program".',') !== FALSE && !empty(str_replace('-','',$program))) {
                    $block_html .= 'Program: '.$program.'<br />';
                }
                if (strpos($value_config, ','."Project Type".',') !== FALSE && !empty($project_type)) {
                    $block_html .= PROJECT_NOUN.' Type: '.$project_type.'<br />';
                }
                if (strpos($value_config, ','."Project".',') !== FALSE && !empty(str_replace('-','',$project))) {
                    $block_html .= PROJECT_NOUN.': '.$project.'<br />';
                }
                if (strpos($value_config, ','."Ticket".',') !== FALSE && !empty(str_replace('-','',$ticket))) {
                    $block_html .= TICKET_NOUN.': '.$ticket.'<br />';
                }
                if (strpos($value_config, ','."Member".',') !== FALSE && !empty($member_list)) {
                    $block_html .= 'Member: '.$member_list.'<br />';
                }
                if (strpos($value_config, ','."Client".',') !== FALSE && !empty($client_list)) {
                    $block_html .= 'Client: '.$client_list.'<br />';
                }
                if (strpos($value_config, ','."Type".',') !== FALSE && !empty($incident_report['type'])) {
                    $block_html .= 'Type: ' . $incident_report['type'].'<br />';
                }
                if (strpos($value_config, ','."Staff".',') !== FALSE && !empty(str_replace('-','',$contact_name))) {
                    $block_html .= 'Staff: ' . $contact_name.'<br />';
                }
                if (strpos($value_config, ','."Follow Up".',') !== FALSE && !empty($incident_report['ir14'])) {
                    $block_html .= 'Follow Up: ' . $incident_report['ir14'].'<br />';
                }
                if (strpos($value_config, ','."Date of Happening".',') !== FALSE && !empty(str_replace('0000-00-00','',$incident_report['date_of_happening']))) {
                    $block_html .= 'Date of Happening: ' . $incident_report['date_of_happening'].'<br />';
                }
                if (strpos($value_config, ','."Date of Incident".',') !== FALSE && !empty(str_replace('0000-00-00','',$incident_report['incident_date']))) {
                    $block_html .= 'Date of Incident: ' . $incident_report['incident_date'].'<br />';
                }
                if (strpos($value_config, ','."Date Created".',') !== FALSE && !empty(str_replace('0000-00-00','',$incident_report['today_date']))) {
                    $block_html .= 'Date Created: ' . $incident_report['today_date'].'<br />';
                }
                if (strpos($value_config, ','."Location".',') !== FALSE && !empty($incident_report['location'])) {
                    $block_html .= 'Location: ' . $incident_report['location'].'<br />';
                }
                if (strpos($value_config, ','."PDF".',') !== FALSE) {
                    $name_of_file = 'incident_report_'.$incident_report['incidentreportid'].'.pdf';
                    $block_html .= 'PDF: '.(file_exists('../Incident Report/download/'.$name_of_file) ? '<span onclick="window.open(\''.WEBSITE_URL.'/Incident Report/download/'.$name_of_file.'\', \'_blank\');" class="no-slider"><img src="'.WEBSITE_URL.'/img/pdf.png" width="16" height="16" border="0" alt="View">View</span>' : '');
                    if ($incident_report['revision_number'] > 0) {
                        $revision_dates = explode('*#*', $incident_report['revision_date']);
                        for ($i = 0; $i < $incident_report['revision_number']; $i++) {
                            $name_of_file = 'incident_report_'.$incident_report['incidentreportid'].'_'.($i+1).'.pdf';
                            if(file_exists('../Incident Report/download/'.$name_of_file)) {
                                $block_html .= '<br /><span onclick="window.open(\''.WEBSITE_URL.'/Incident Report/download/'.$name_of_file.'\', \'_blank\');" class="no-slider"><img src="'.WEBSITE_URL.'/img/pdf.png" width="16" height="16" border="0" alt="view">View R'.($i+1).': '.$revision_dates[$i].'</span>';
                            }
                        }
                    }
                    $block_html .= '<br />';
                }
                $block_html .= 'Last Updated Date: '.$row['last_updated_date'];

                $block_html .= '</a>';

                break;
        }
        if(!empty($block_html)) {
            if($daysheet_styling == 'card') {
                $html .= '<div class="block-group-daysheet">'.$block_html.'</div>';
            } else {
                $html .= '<li>'.$block_html.'</li>';
            }
        }
    }
    return $html;
}