<!-- Daysheet My Tasks -->
<?php
    $past_due_list = mysqli_query($dbc, "SELECT * FROM `daysheet_reminders` WHERE `date` < '".date('Y-m-d')."' AND `contactid` = '".$contactid."' AND `deleted` = 0 AND `done` = 0 ORDER BY `date` DESC");
    $num_rows_reminders = mysqli_num_rows($past_due_list);

    $past_due_tickets = mysqli_query($dbc, "SELECT `tickets`.*, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, CONCAT('<br>',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, `ticket_schedule`.`id` `stop_id`, `ticket_schedule`.`eta`, `ticket_schedule`.`client_name`, IFNULL(`ticket_schedule`.`address`, `tickets`.`address`) `address`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`to_do_start_time`, IFNULL(NULLIF(`tickets`.`start_time`,'00:00'),`tickets`.`to_do_start_time`)) `to_do_start_time`, CONCAT(`start_available`,' - ',`end_available`) `availability`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, IFNULL(`ticket_schedule`.`map_link`,`tickets`.`google_maps`) `map_link`, `tickets`.`siteid` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive','Done','On Hold','Waiting On Customer','Stopped Due To Customer','To Be Scheduled') AND (((`internal_qa_date` < '".date('Y-m-d')."' OR `internal_qa_date` = '0000-00-00') AND `internal_qa_contactid` LIKE '%,".$contactid.",%' AND `tickets`.`status` = 'Internal QA') OR ((`deliverable_date` < '".date('Y-m-d')."' OR `deliverable_date` = '0000-00-00') AND `deliverable_contactid` LIKE '%,".$contactid.",%' AND `tickets`.`status` = 'Customer QA') OR ((IF(`ticket_schedule`.`id` IS NULL, `tickets`.`to_do_date`, `ticket_schedule`.`to_do_date`) < '".date('Y-m-d')."' OR IF(`ticket_schedule`.`id` IS NULL, `tickets`.`to_do_date`, `ticket_schedule`.`to_do_date`) = '0000-00-00') AND (IF(`ticket_schedule`.`id` IS NULL, `tickets`.`to_do_end_date`, `ticket_schedule`.`to_do_end_date`) < '".date('Y-m-d')."') AND IF(`ticket_schedule`.`id` IS NULL,`tickets`.`contactid`,`ticket_schedule`.`contactid`) LIKE '%,".$contactid.",%' AND `tickets`.`status` NOT IN ('Internal QA','Customer QA')))");
    $num_rows_tickets = mysqli_num_rows($past_due_tickets);

    $past_due_tasks = mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `task_tododate` < '".date('Y-m-d')."' AND `contactid` = '".$contactid."' AND `deleted` = 0 AND `task_milestone_timeline` != 'Done' AND `task_milestone_timeline` != 'Archived' ORDER BY `task_tododate` DESC");
    $num_rows_tasks = mysqli_num_rows($past_due_tasks);

    $num_rows = $num_rows_reminders + $num_rows_tickets + $num_rows_tasks;
?>
<div class="col-xs-12">
    <div class="weekly-div" style="overflow-y: hidden;">
        <?php if($num_rows > 0) {
            if($num_rows_reminders > 0) {
                echo '<h4 style="font-weight: normal;">Reminders</h4>';
                if($daysheet_styling != 'card') {
                    echo '<ul id="past_due_alerts">';
                }
                while($daysheet_reminder = mysqli_fetch_array( $past_due_list )) {
                    $reminder_label = '';
                    $status_label = '';
                    if ($daysheet_reminder['type'] == 'reminder') {
                        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `reminders` WHERE `reminderid` = '".$daysheet_reminder['reminderid']."'"));
                        $reminder_url = get_reminder_url($dbc, $reminder, 1);
                        $slider = 1;
                        if(empty($reminder_url)) {
                            $slider = 0;
                            $reminder_url = get_reminder_url($dbc, $reminder);
                        }
                        if(!empty($reminder_url)) {
                            if($slider == 1) {
                                $reminder_label = '<a href="" onclick="overlayIFrameSlider(\''.$reminder_url.'\'); return false;">'.$daysheet_reminder['date'].' - '.$reminder['subject'].'</a>';
                            } else {
                                $reminder_label = '<a href="'.$reminder_url.'">'.$daysheet_reminder['date'].' - '.$reminder['subject'].'</a>';
                            }
                        } else {
                            $reminder_label = '<div class="daysheet-span">'.$daysheet_reminder['date'].' - '.$reminder['subject'].'</div>';
                        }
                    } else if ($daysheet_reminder['type'] == 'sales') {
                        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales` WHERE `salesid` = '".$daysheet_reminder['reminderid']."'"));
                        $status_label = '<h5 style="font-weight: normal; font-style: italic; display: inline;"> currently in '.$reminder['status'].' - '.$reminder['new_reminder'].'</h5>';
                        $reminder_label = '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Sales/sale.php?iframe_slider=1&p=details&id='.$reminder['salesid'].'\'); return false;" style="color: black;">'.$daysheet_reminder['date'].' - '.'Follow Up Sales: Sales #'.$reminder['salesid'].($daysheet_styling == 'card' ? $status_label : '').'</a>';
                    } else if ($daysheet_reminder['type'] == 'sales_order') {
                        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order` WHERE `posid` = '".$daysheet_reminder['reminderid']."'"));
                        $status_label = '<h5 style="font-weight: normal; font-style: italic; display: inline;"> currently in '.$reminder['status'].' - '.$reminder['next_action_date'].'</h5>';
                        $reminder_label = '<a href="../Sales Order/index.php?p=preview&id='.$reminder['posid'].'" style="color: black;">'.$daysheet_reminder['date'].' - '.'Follow Up '.SALES_ORDER_NOUN.': '.($reminder['name'] != '' ? $reminder['name'] : SALES_ORDER_NOUN.' #'.$reminder['posid']).($daysheet_styling == 'card' ? $status_label : '').'</a>';
                    } else if ($daysheet_reminder['type'] == 'sales_order_temp') {
                        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `sotid` = '".$daysheet_reminder['reminderid']."'"));
                        $status_label = '<h5 style="font-weight: normal; font-style: italic; display: inline;"> currently in '.$reminder['status'].' - '.$reminder['next_action_date'].'</h5>';
                        $reminder_label = '<a href="../Sales Order/order.php?p=details&sotid='.$reminder['sotid'].'" style="color: black;">'.$daysheet_reminder['date'].' - '.'Follow Up '.SALES_ORDER_NOUN.': '.($reminder['name'] != '' ? $reminder['name'] : SALES_ORDER_NOUN.' Form #'.$reminder['sotid']).($daysheet_styling == 'card' ? $status_label : '').'</a>';
                    } else if ($daysheet_reminder['type'] == 'estimate') {
                        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `ea`.*, `e`.`estimate_name`, `e`.`status` FROM `estimate_actions` AS `ea` JOIN `estimate` AS `e` ON (`ea`.`estimateid`=`e`.`estimateid`) WHERE `ea`.`id` = '".$daysheet_reminder['reminderid']."'"));
                        $status_label = '<h5 style="font-weight: normal; font-style: italic; display: inline;"> currently in '.$reminder['status'].' - '.$reminder['due_date'].'</h5>';
                        $reminder_label = '<a href="../Estimate/estimates.php?view='.$reminder['estimateid'].'" style="color: black;">'.$daysheet_reminder['date'].' - '.'Follow Up Estimate: '.$reminder['estimate_name'].($daysheet_styling == 'card' ? $status_label : '').'</a>';
                    } else if ($daysheet_reminder['type'] == 'project') {
                        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `pa`.*, `p`.`project_name`, `p`.`status` FROM `project_actions` AS `pa` JOIN `project` AS `p` ON (`pa`.`projectid`=`p`.`projectid`) WHERE `pa`.`id` = '".$daysheet_reminder['reminderid']."'"));
                        $status_label = '<h5 style="font-weight: normal; font-style: italic; display: inline;"> currently in '.$reminder['status'].' - '.$reminder['due_date'].'</h5>';
                        $reminder_label = '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Project/projects.php?iframe_slider=1&edit='.$reminder['projectid'].'\'); return false;" style="color: black;">'.$daysheet_reminder['date'].' - '.'Follow Up Project: '.$reminder['project_name'].($daysheet_styling == 'card' ? $status_label : '').'</a>';
                    } else if ($daysheet_reminder['type'] == 'project_followup') {
                        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '".$daysheet_reminder['reminderid']."'"));
                        $status_label = '<h5 style="font-weight: normal; font-style: italic; display: inline;"> currently in '.$reminder['status'].' - '.$reminder['followup'].'</h5>';
                        $reminder_label = '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Project/projects.php?iframe_slider=1&edit='.$reminder['projectid'].'\'); return false;" style="color: black;">'.$daysheet_reminder['date'].' - '.'Follow Up Project: '.$reminder['project_name'].($daysheet_styling == 'card' ? $status_label : '').'</a>';
                    } else if ($daysheet_reminder['type'] == 'certificate') {
                        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `certificate` WHERE `certificateid` = '".$daysheet_reminder['reminderid']."'"));
                        $reminder_label = '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Certificate/edit_certificate.php?edit='.$reminder['certificateid'].'\'); return false;" style="color: black;">'.$daysheet_reminder['date'].' - '.'Certificate Reminder: '.$reminder['title'].'</a>';
                    } else if ($daysheet_reminder['type'] == 'alert') {
                        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `alerts` WHERE `alertid` = '".$daysheet_reminder['reminderid']."'"));
                        $reminder_label = '<a href="'.$reminder['alert_link'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" style="color: black;">'.$daysheet_reminder['date'].' - '.'Alert: '.$reminder['alert_text'].' - '.$reminder['alert_link'].'</a>';
                    } else if ($daysheet_reminder['type'] == 'incident_report') {
                        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `incident_report` WHERE `incidentreportid` = '".$daysheet_reminder['reminderid']."'"));
                        $reminder_label = '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Incident Report/add_incident_report.php?incidentreportid='.$reminder['incidentreportid'].'\'); return false;" style="color: black;">Follow Up '.INC_REP_NOUN.': '.$reminder['type'].' #'.$reminder['incidentreportid'].'</a>';
                    } else if ($daysheet_reminder['type'] == 'equipment_followup') {
                        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '".$daysheet_reminder['reminderid']."'"));
                        $reminder_label = '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Incident Equipment/edit_equipment.php?edit='.$reminder['equipmentid'].'&iframe_slider=1\'); return false;" style="color: black;">Follow Up Equipment ('.$reminder['category'].' #'.$reminder['unit_number'].'): Next Service Date coming up on '.$reminder['next_service_date'].'</a>';
                    } else if ($daysheet_reminder['type'] == 'equipment_service') {
                        $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '".$daysheet_reminder['reminderid']."'"));
                        $reminder_label = '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Incident Equipment/edit_equipment.php?edit='.$reminder['equipmentid'].'&iframe_slider=1\'); return false;" style="color: black;">Equipment Service Reminder ('.$reminder['category'].' #'.$reminder['unit_number'].'): Service Date scheduled for '.$reminder['next_service_date'].'</a>';
                    }
                    if(!empty($reminder_label)) {
                        if($daysheet_styling == 'card') {
                            echo '<div class="col-xs-12 daysheet_row"><div class="col-xs-2" style="max-width: 35px;"><input style="position: relative; vertical-align: middle; top: 10px; height: 20px; width: 20px;" type="checkbox" name="daysheet_reminder" value="'.$daysheet_reminder['daysheetreminderid'].'" '.($daysheet_reminder['done'] == 1 ? 'checked="checked"' : '').'></div><div class="col-xs-10 block-group-daysheet"><span '.($daysheet_reminder['done'] == 1 ? 'style="text-decoration: line-through;"' : '').'>'.$reminder_label.'</span></div></div>';
                        } else {
                            echo '<div style="font-weight: normal; display: inline;" class="daysheet_row"><input style="position: relative; vertical-align: middle; top: -0.25em;" class="form-checkbox" type="checkbox" name="daysheet_reminder" value="'.$daysheet_reminder['daysheetreminderid'].'" '.($daysheet_reminder['done'] == 1 ? 'checked="checked"' : '').'>&nbsp;&nbsp;<span '.($daysheet_reminder['done'] == 1 ? 'style="text-decoration: line-through;"' : '').'>'.$reminder_label.'</span>'.$status_label.'</div><br />';
                        }
                    }
                }
                if($daysheet_styling != 'card') {
                    echo '</ul>';
                }
                echo '<div class="clearfix"></div>';
                echo '<hr>';
            }

            //Item Layout
            if($daysheet_styling == 'card') {
                $row_open = '<div class="block-group-daysheet">';
                $row_close = '</div>';
            } else {
                $row_open = '<li>';
                $row_close = '</li>';
            }
            if($num_rows_tickets > 0) {
                $row_open_ticket = $row_open;
                echo '<h4 style="font-weight: normal;">'.TICKET_NOUN.'s</h4>';
                if($daysheet_styling != 'card') {
                    echo '<ul id="past_due_tickets">';
                }
                while($ticket = mysqli_fetch_array($past_due_tickets)) {
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

                    echo $row_open_ticket.'<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'&action_mode='.$ticket_action_mode.'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\'); return false;">'.$icon_img.'<span style="color: black;">'.$ticket_date.' - '.$label.'</span><div class="clearfix"></div></a>'.$row_close;
                }
                if($daysheet_styling != 'card') {
                    echo '</ul>';
                }
                echo '<div class="clearfix"></div>';
                echo '<hr>';
            }

            if($num_rows_tasks > 0) {
                echo '<h4 style="font-weight: normal;">'.TASK_TILE.'</h4>';
                if($daysheet_styling != 'card') {
                    echo '<ul id="past_due_tasks">';
                }
                $label = '';
                while($task = mysqli_fetch_array($past_due_tasks)) {
                    echo $row_open.'<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Tasks_Updated/add_task.php?tasklistid='.$task['tasklistid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'\'); return false;" ><span style="color: black;">'.$task['task_tododate'].' - '.($label != '' ? $label.'<br />' : '').$task['task_milestone_timeline'].' - '.get_contact($dbc, $task['businessid'], 'name').' - '.$task['heading'].'</span></a>'.$row_close;
                }
                if($daysheet_styling != 'card') {
                    echo '</ul>';
                }
                echo '<div class="clearfix"></div>';
                echo '<hr>';
            }
        } else {
            echo "<h2>No Record Found.</h2>";
            echo '<script type="text/javascript">window.location.href = "daysheet.php?daily_date='.$daily_date.'"</script>';
        } ?>
    </div>
</div>