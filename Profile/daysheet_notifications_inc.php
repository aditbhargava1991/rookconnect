<?php
$noti_html = '';

$new_noti = false;
$old_noti = false;
$row_i = 0;
if(mysqli_num_rows($noti_list) > 0) {
    while($row = mysqli_fetch_array($noti_list)) {
    	if($row_limit > 0 && $row_i > $row_limit) {
    		break;
    	}
    	if($email_alert != 1 && $calendar_reminders != 1) {
            if(!$new_noti && $row['seen'] != 1) {
                $noti_html .= '</ul>';
                $noti_html .= '<h4 style="font-weight: normal;">New Notifications</h4>';
                if($daysheet_styling != 'card') {
                    $noti_html .= '<ul id="journal_notifications">';
                }
                $new_noti = true;
            }
            if(!$old_noti && $row['seen'] == 1) {
                $noti_html .= '</ul>';
                $noti_html .= '<h4 style="font-weight: normal;">Last 25 Notifications</h4>';
                if($daysheet_styling != 'card') {
                    $noti_html .= '<ul id="journal_notifications">';
                }
                $old_noti = true;
            }
        }

        $row_html = '';
        $note_label = '';
        switch($row['src_table']) {
            case 'daysheet_reminders':
                $daysheet_reminder = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `daysheet_reminders` WHERE `daysheetreminderid` = '".$row['src_id']."'".$daysheet_reminder_query));
                $reminder_label = '';
                $status_label = '';
                if ($daysheet_reminder['type'] == 'reminder') {
                    $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `reminders` WHERE `reminderid` = '".$daysheet_reminder['reminderid']."'".$reminder_query));
                    if(!empty($reminder)) {
	                    $reminder_url = get_reminder_url($dbc, $reminder);
	                    if(!empty($reminder_url)) {
	                        $reminder_label = '<a href="'.$reminder_url.'">Reminder: '.$daysheet_reminder['date'].' - '.$reminder['subject'].'</a>';
	                    } else {
	                        $reminder_label = '<div class="daysheet-span">Reminder: '.$daysheet_reminder['date'].' - '.$reminder['subject'].'</div>';
	                    }
	                }
                } else if ($daysheet_reminder['type'] == 'sales') {
                    $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales` WHERE `salesid` = '".$daysheet_reminder['reminderid']."'"));
                    $status_label = '<h5 style="font-weight: normal; font-style: italic; display: inline;"> currently in '.$reminder['status'].' - '.$reminder['new_reminder'].'</h5>';
                    $reminder_label = '<a href="'.WEBSITE_URL.'/Sales/sale.php?p=preview&id='.$reminder['salesid'].'">Reminder: '.$daysheet_reminder['date'].' - '.'Follow Up Sales: Sales #'.$reminder['salesid'].($daysheet_styling == 'card' ? $status_label : '').'</a>';
                } else if ($daysheet_reminder['type'] == 'sales_order') {
                    $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order` WHERE `posid` = '".$daysheet_reminder['reminderid']."'"));
                    $status_label = '<h5 style="font-weight: normal; font-style: italic; display: inline;"> currently in '.$reminder['status'].' - '.$reminder['next_action_date'].'</h5>';
                    $reminder_label = '<a href="'.WEBSITE_URL.'/Sales Order/index.php?p=preview&id='.$reminder['posid'].'">Reminder: '.$daysheet_reminder['date'].' - '.'Follow Up '.SALES_ORDER_NOUN.': '.($reminder['name'] != '' ? $reminder['name'] : SALES_ORDER_NOUN.' #'.$reminder['posid']).($daysheet_styling == 'card' ? $status_label : '').'</a>';
                } else if ($daysheet_reminder['type'] == 'sales_order_temp') {
                    $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `sotid` = '".$daysheet_reminder['reminderid']."'"));
                    $status_label = '<h5 style="font-weight: normal; font-style: italic; display: inline;"> currently in '.$reminder['status'].' - '.$reminder['next_action_date'].'</h5>';
                    $reminder_label = '<a href="'.WEBSITE_URL.'/Sales Order/order.php?p=details&sotid='.$reminder['sotid'].'">Reminder: '.$daysheet_reminder['date'].' - '.'Follow Up '.SALES_ORDER_NOUN.': '.($reminder['name'] != '' ? $reminder['name'] : SALES_ORDER_NOUN.' Form #'.$reminder['sotid']).($daysheet_styling == 'card' ? $status_label : '').'</a>';
                } else if ($daysheet_reminder['type'] == 'estimate') {
                    $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `ea`.*, `e`.`estimate_name`, `e`.`status` FROM `estimate_actions` AS `ea` JOIN `estimate` AS `e` ON (`ea`.`estimateid`=`e`.`estimateid`) WHERE `ea`.`id` = '".$daysheet_reminder['reminderid']."'"));
                    $status_label = '<h5 style="font-weight: normal; font-style: italic; display: inline;"> currently in '.$reminder['status'].' - '.$reminder['due_date'].'</h5>';
                    $reminder_label = '<a href="'.WEBSITE_URL.'/Estimate/estimates.php?view='.$reminder['estimateid'].'">Reminder: '.$daysheet_reminder['date'].' - '.'Follow Up Estimate: '.$reminder['estimate_name'].($daysheet_styling == 'card' ? $status_label : '').'</a>';
                } else if ($daysheet_reminder['type'] == 'project') {
                    $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `pa`.*, `p`.`project_name`, `p`.`status` FROM `project_actions` AS `pa` JOIN `project` AS `p` ON (`pa`.`projectid`=`p`.`projectid`) WHERE `pa`.`id` = '".$daysheet_reminder['reminderid']."'"));
                    $status_label = '<h5 style="font-weight: normal; font-style: italic; display: inline;"> currently in '.$reminder['status'].' - '.$reminder['due_date'].'</h5>';
                    $reminder_label = '<a href="'.WEBSITE_URL.'/Project/projects.php?edit='.$reminder['projectid'].'">Reminder: '.$daysheet_reminder['date'].' - '.'Follow Up Project: '.$reminder['project_name'].($daysheet_styling == 'card' ? $status_label : '').'</a>';
                } else if ($daysheet_reminder['type'] == 'project_followup') {
                    $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '".$daysheet_reminder['reminderid']."'"));
                    $status_label = '<h5 style="font-weight: normal; font-style: italic; display: inline;"> currently in '.$reminder['status'].' - '.$reminder['followup'].'</h5>';
                    $reminder_label = '<a href="'.WEBSITE_URL.'/Project/projects.php?edit='.$reminder['projectid'].'">Reminder: '.$daysheet_reminder['date'].' - '.'Follow Up Project: '.$reminder['project_name'].($daysheet_styling == 'card' ? $status_label : '').'</a>';
                } else if ($daysheet_reminder['type'] == 'certificate') {
                    $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `certificate` WHERE `certificateid` = '".$daysheet_reminder['reminderid']."'"));
                    $reminder_label = '<a href="'.WEBSITE_URL.'/Certificate/index.php?edit='.$reminder['certificateid'].'">Reminder: '.$daysheet_reminder['date'].' - '.'Certificate Reminder: '.$reminder['title'].'</a>';
                } else if ($daysheet_reminder['type'] == 'alert') {
                    $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `alerts` WHERE `alertid` = '".$daysheet_reminder['reminderid']."'"));
                    $reminder_label = '<a href="'.$reminder['alert_link'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">Reminder: '.$daysheet_reminder['date'].' - '.'Alert: '.$reminder['alert_text'].' - '.$reminder['alert_link'].'</a>';
                }
                if(!empty($reminder_label)) {
                    $row_html = $row_open.$reminder_label.$row_close;
                }
                break;
            case 'tickets':
            case 'ticket_schedule':
                $row_open_ticket = $row_open;
                if($row['src_table'] == 'ticket_schedule') {
                    $ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT `tickets`.*, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, CONCAT('<br>',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, `ticket_schedule`.`id` `stop_id`, `ticket_schedule`.`eta`, `ticket_schedule`.`client_name`, IFNULL(`ticket_schedule`.`address`, `tickets`.`address`) `address`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`to_do_start_time`, `tickets`.`to_do_start_time`) `to_do_start_time`, CONCAT(`start_available`,' - ',`end_available`) `availability` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid` = `ticket_schedule`.`ticketid` WHERE `ticket_schedule`.`id` = '".$row['src_id']."'"));
                } else {
                    $ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$row['src_id']."'"));
                }
                $delivery_color = get_delivery_color($dbc, $ticket['delivery_type']);
                if(!empty($delivery_color)) {
                    $delivery_style = 'style="background-color: '.$delivery_color.';"';
                } else {
                    $delivery_style = '';
                }
                if($daysheet_styling == 'card') {
                    $row_open_ticket = '<div class="block-group-daysheet" '.$delivery_style.'>';
                }
                $label = daysheet_ticket_label($dbc, $daysheet_ticket_fields, $ticket);
                if ($ticket['status'] == 'Internal QA') {
                    $ticket_date = $ticket['internal_qa_date'];
                } else if ($ticket['status'] == 'Customer QA') {
                    $ticket_date = $ticket['deliverable_date'];
                } else {
                    $ticket_date = $ticket['to_do_date'];
                }

                $status_icon = get_ticket_status_icon($dbc, $ticket['status']);
                if(!empty($status_icon)) {
                    if($status_icon == 'initials') {
                        $icon_img = '<span class="id-circle-large pull-right" style="background-color: #6DCFF6; font-family: \'Open Sans\';">'.get_initials($ticket['status']).'</span>';
                    } else {
                        $icon_img = '<img src="'.$status_icon.'" class="pull-right" style="max-height: 30px;">';
                    }
                } else {
                    $icon_img = '';
                }

                $row_html = $row_open_ticket.'<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'&action_mode='.$ticket_action_mode.'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\'); return false;">'.$icon_img.TICKET_NOUN.': '.$ticket_date.' - '.$label.'</a>'.$row_close;
                break;
            case 'tasklist':
                $task = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `tasklistid` = '".$row['src_id']."'"));
                $row_html = $row_open.'<a href="'.WEBSITE_URL.'/Tasks_Updated/add_task.php?tasklistid='.$task['tasklistid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" >'.'Task: '.$task['task_tododate'].' - '.($label != '' ? $label.'<br />' : '').$task['task_milestone_timeline'].' - '.get_contact($dbc, $task['businessid'], 'name').' - '.$task['heading'].'</a>'.$row_close;
                break;
            case 'checklist_actions':
                $checklist_action = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `checklist_actions` WHERE `checklistactionid` = '".$row['src_id']."'"));
                $checklist_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist_name` AS `cn` LEFT JOIN `checklist` AS `c`ON `c`.`checklistid` = `cn`.`checklistid` WHERE `checklistnameid` = '".$checklist_action['checklistnameid']."'"));
                $label = ($checklist_name['businessid'] > 0 ? get_contact($dbc, $checklist_name['businessid'], 'name').', ' : '').($checklist_name['projectid'] > 0 ? PROJECT_NOUN.' #'.$checklist_name['projectid'].' '.get_project($dbc,$checklist_name['projectid'],'project_name') : '');
                $row_html = $row_open.'<a href="'.WEBSITE_URL.'/Checklist/checklist.php?view='.$checklist_name['checklistid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">Checklist Item: '.($label != '' ? $label.'<br />' : '').'Checklist: '.$checklist_name['checklist_name'].' - Item: '.explode('&lt;p&gt;', $checklist_name['checklist'])[0].'</a>'.$row_close;
                break;
            case 'budget_comment':
                if($row['src_table'] == 'budget_comment') {
                    $day_note = mysqli_fetch_array(mysqli_query($dbc, "SELECT `comment`, 'comment' `src`, 0 `src_id`, 'Budget Comment' `type`, `created_by` `user`, `email_to` `assigned`, `created_date` FROM `budget_comment` WHERE `budgetcommid` = '".$row['src_id']."'"));
                    $note_label = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `budget` WHERE `budgetid` = '".$day_note['src_id']."'"))['budget_name'];
                }
            case 'project_comment':
                if($row['src_table'] == 'project_comment') {
                    $day_note = mysqli_fetch_array(mysqli_query($dbc, "SELECT `comment`, 'Project' `src`, `projectid` `src_id`, 'Project Comment' `type`, `created_by` `user`, `email_comment` `assigned`, `created_date` FROM `project_comment` WHERE `projectcommid` = '".$row['src_id']."'"));
                    $note_label = get_project_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '".$day_note['src_id']."'")));

                }
            case 'task_comments':
                if($row['src_table'] == 'task_comments') {
                    $day_note = mysqli_fetch_array(mysqli_query($dbc, "SELECT `comment`, 'Task' `src`, `tasklistid` `src_id`, 'Task Note' `type`, `created_by` `user`, 0 `assigned`, `created_date` FROM `task_comments` WHERE `taskcommid` = '".$row['src_id']."'"));
                    $note_label = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `tasklistid` = '".$day_note['src_id']."'"))['heading'];
                }
            case 'ticket_comment':
                if($row['src_table'] == 'ticket_comment') {
                    $day_note = mysqli_fetch_array(mysqli_query($dbc, "SELECT `comment`, 'Ticket' `src`, `ticketid` `src_id`, '".TICKET_NOUN." Note' `type`, `created_by` `user`, `email_comment` `assigned`, `created_date` FROM `ticket_comment` WHERE `ticketcommid` = '".$row['src_id']."'"));
                    $note_label = get_ticket_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$day_note['src_id']."'")));
                }
            case 'estimate_notes':
                if($row['src_table'] == 'estimate_notes') {
                    $day_note = mysqli_fetch_array(mysqli_query($dbc, "SELECT `notes` `comment`, 'comment' `src`, `estimateid` `src_id`, 'Estimate Note' `type`, `created_by` `user`, `assigned` `assigned`, `note_date` `created_date` FROM `estimate_notes` WHERE `id` = '".$row['src_id']."'"));
                    $note_label = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `estimate` WHERE `estimateid` = '".$day_note['src_id']."'"))['estimate_name'];
                }
            case 'client_daily_log_notes':
                if($row['src_table'] == 'client_daily_log_notes') {
                    $day_note = mysqli_fetch_array(mysqli_query($dbc, "SELECT `note` `comment`, 'comment' `src`, 0 `src_id`, 'Daily Log Notes' `type`, `created_by` `user`, `client_id` `assigned`, `note_date` `created_date` FROM `client_daily_log_notes` WHERE `note_id` = '".$row['src_id']."'"));
                }
            case 'daysheet_notepad':
                if($row['src_table'] == 'daysheet_notepad') {
                    $day_note = mysqli_fetch_array(mysqli_query($dbc, "SELECT `notes` `comment`, 'comment' `src`, 0 `src_id`, 'Journal Note' `type`, `contactid` `user`, 0 `assigned`, `date` `created_date` FROM `daysheet_notepad` WHERE `daysheetnotepadid` = '".$row['src_id']."'"));
                }
                $row_html = $row_open.($day_note['src'] == 'Task' ? '<a href="'.WEBSITE_URL.'/Tasks_Updated/add_task.php?tasklistid='.$day_note['src_id'].'" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Tasks_Updated/add_task.php?tasklistid='.$day_note['src_id'].'\'); return false;">' : ($day_note['src'] == 'Project' ? '<a href="'.WEBSITE_URL.'/Project/projects.php?edit='.$day_note['src_id'].'">' : ($day_note['src'] == 'Ticket' ? '<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$day_note['src_id'].'&action_mode='.$ticket_action_mode.'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\'); return false;">' : '<div class="daysheet-span">'))).(!empty($note_label) ? $note_label.'<br />' : '').$day_note['type'].': '.html_entity_decode($day_note['comment']).($day_note['src'] == 'Project' || $day_note['src'] == 'Ticket' || $day_note['src'] == 'Task' ? '</a>' : '</div>');
                $row_html .= $row_close;
                break;
        }
        if(!empty($row_html)) {
        	$noti_html .= $row_html;
        	$row_i++;
        }
    }
    if($daysheet_styling != 'card') {
        $noti_html .= '</ul>';
    }
}
?>