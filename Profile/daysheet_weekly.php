<!-- Daysheet Weekly/Monthly Overview -->
<script type="text/javascript">
$(document).ready(function (){
    $('.expand-div-link').each(function() {
        var weekly_div = $(this).parent().find('.weekly-div');
        var max_height = parseFloat($(this).parent().find('.weekly-div').css('font-size')) * 8;
        if (weekly_div.height() >= max_height) {
            weekly_div.css('max-height', '8em');
        } else {
            $(this).hide();
        }
    });
});
function expandDiv(link) {
    if ($(link).parent().find('.weekly-div').css('max-height') == 'none') {
        $(link).parent().find('.weekly-div').css('max-height', '8em');
    } else {
        $(link).parent().find('.weekly-div').css('max-height', '');
    }
}
</script>
<?php
$period_start = $period_end = $daily_format = '';
if($side_content == 'monthly') {
	$period_start = date('Y-m-01',strtotime($weekly_date));
	$period_end = date('Y-m-t',strtotime($weekly_date));
	$daily_format = 'F j';
} else {
	$day = date('w', strtotime($weekly_date));
	$period_start = date('Y-m-d', strtotime($weekly_date.' -'.($day - 1).' days'));
	$period_end = date('Y-m-d', strtotime($weekly_date.' -'.($day - 7).' days'));
	$daily_format = 'l, F j';
}
for ($current_day = $period_start; $current_day <= $period_end; $current_day = date('Y-m-d',strtotime($current_day.' + 1 day'))) {
    if (in_array((date('w',strtotime($current_day)) == 0 ? 7 : date('w',strtotime($current_day))), $daysheet_weekly_config)) {
    $day_of_week = date($daily_format, strtotime($current_day));

    // Retrieve Data
    //Reminders
    $reminders_query = "SELECT * FROM `reminders` WHERE `reminder_date` = '$current_day' AND `contactid` = '$contactid' AND `deleted` = 0";
    $reminders_result = mysqli_fetch_all(mysqli_query($dbc, $reminders_query),MYSQLI_ASSOC);
    $sales_reminders_query = "SELECT * FROM `sales` WHERE `new_reminder` = '$current_day' AND (`primary_staff` = '$contactid' OR CONCAT(',',`share_lead`,',') LIKE '%,$contactid,%')";
    $sales_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $sales_reminders_query),MYSQLI_ASSOC);
    $so_reminders_query = "SELECT * FROM `sales_order` WHERE `next_action_date` = '$current_day' AND (`primary_staff` = '$contactid' OR CONCAT(',',`assign_staff`,',') LIKE '%,$contactid,%')";
    $so_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $so_reminders_query),MYSQLI_ASSOC);
    $sot_reminders_query = "SELECT * FROM `sales_order_temp` WHERE `next_action_date` = '$current_day' AND (`primary_staff` = '$contactid' OR CONCAT(',',`assign_staff`,',') LIKE '%,$contactid,%')";
    $sot_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $sot_reminders_query),MYSQLI_ASSOC);
    $estimates_reminders_query = "SELECT `ea`.*, `e`.`estimate_name` FROM `estimate_actions` AS `ea` JOIN `estimate` AS `e` ON (`ea`.`estimateid`=`e`.`estimateid`) WHERE FIND_IN_SET ('$contactid', `e`.`assign_staffid`) AND `e`.`deleted`=0 AND FIND_IN_SET('$contactid', `ea`.`contactid`) AND `ea`.`deleted`=0 AND `ea`.`due_date`='". date('Y-m-d', strtotime($current_day)) ."'";
    $estimates_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $estimates_reminders_query),MYSQLI_ASSOC);
    $projects_reminders_query = "SELECT `pa`.*, `p`.`project_name` FROM `project_actions` AS `pa` JOIN `project` AS `p` ON (`pa`.`projectid`=`p`.`projectid`) WHERE FIND_IN_SET ('$contactid', `pa`.`contactid`) AND `p`.`deleted` = 0 AND `pa`.`deleted` = 0 AND `pa`.`due_date` = '".date('Y-m-d', strtotime($current_day))."'";
    $projects_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $projects_reminders_query),MYSQLI_ASSOC);
    $pfu_reminders_query = "SELECT * FROM `project` WHERE `followup` = '".$current_day."' AND `project_lead` = '".$contactid."'";
    $pfu_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $pfu_reminders_query),MYSQLI_ASSOC);
    foreach ($pfu_reminders_result as $key => $reminder) {
        foreach ($projects_reminders_result as $project_action) {
            if ($project_action['projectid'] == $reminder['projectid']) {
                unset($pfu_reminders_result[$key]);
            }
        }
    }
    $cert_reminders_query = "SELECT * FROM `certificate` WHERE `reminder_date` = '".$current_day."' AND `contactid` = '".$contactid."'";
    $cert_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $cert_reminders_query),MYSQLI_ASSOC);
    $alerts_reminders_query = "SELECT * FROM `alerts` WHERE `alert_date` = '$current_day' AND `alert_user` = '$contactid'";
    $alerts_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $alerts_reminders_query),MYSQLI_ASSOC);
    $inc_rep_reminders_query = "SELECT * FROM `incident_report` WHERE `ir14` = '".$current_day."' AND `assign_followup` = '".$contactid."'";
    $inc_rep_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $inc_rep_reminders_query),MYSQLI_ASSOC);
    $equipment_followup_reminders_query = "SELECT * FROM `equipment` WHERE `follow_up_date` = '".$current_day."' AND CONCAT(',',`follow_up_staff`,',') LIKE '%,".$contactid.",%' AND `deleted` = 0";
    $equipment_followup_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $equipment_followup_reminders_query),MYSQLI_ASSOC);
    $equipment_service_reminders_query = "SELECT * FROM `equipment` WHERE `next_service_date` = '".$current_day."' AND CONCAT(',',`follow_up_staff`,',') LIKE '%,".$contactid.",%' AND `deleted` = 0";
    $equipment_service_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $equipment_service_reminders_query),MYSQLI_ASSOC);
    $inc_rep_flags_query = "SELECT * FROM `incident_report` WHERE IFNULL(`flag_colour`,'') != '' AND `flag_colour` != 'FFFFFF' AND '".$current_day."' BETWEEN `flag_start` AND `flag_end` AND CONCAT(',',`flag_user`,',') LIKE '%,".$contactid.",%' AND `deleted` = 0";
    $inc_rep_flags_result = mysqli_fetch_all(mysqli_query($dbc, $inc_rep_flags_query),MYSQLI_ASSOC);

    //Tickets
	if(strtotime($current_day.' 23:59:59') < time() && get_config($dbc, 'timesheet_hide_past_days') == '1' && $dbc->query("SELECT COUNT(*) `count` FROM `time_cards` WHERE `date`='$current_day' AND `staff`='{$_SESSION['contactid']}' AND `end_time` IS NULL AND `start_time` IS NOT NULL")->fetch_assoc()['count'] == 0) {
		$filtered_tickets = " AND 1=0 ";
	}
	$tickets_query = "SELECT `tickets`.*, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, CONCAT('<br>',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, `ticket_schedule`.`id` `stop_id`, `ticket_schedule`.`eta`, `ticket_schedule`.`client_name`, IFNULL(`ticket_schedule`.`address`, `tickets`.`address`) `address`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`to_do_start_time`, IFNULL(NULLIF(`tickets`.`start_time`,'00:00'),`tickets`.`to_do_start_time`)) `to_do_start_time`, CONCAT(`start_available`,' - ',`end_available`) `availability`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, IFNULL(`ticket_schedule`.`map_link`,`tickets`.`google_maps`) `map_link`, `ticket_schedule`.`notes` `delivery_notes`, `tickets`.`siteid` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ((internal_qa_date = '".$current_day."' AND CONCAT(',',IFNULL(`internal_qa_contactid`,''),',') LIKE '%,".$contactid.",%') OR (`deliverable_date` = '".$current_day."' AND CONCAT(',',IFNULL(`deliverable_contactid`,''),',') LIKE '%,".$contactid.",%') OR ((`tickets`.`to_do_date` = '".$current_day."' OR '".$current_day."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR `ticket_schedule`.`to_do_date`='".$current_day."' OR '".$current_day."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND ((CONCAT(',',IFNULL(IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`),''),',') LIKE '%,".$contactid.",%') OR (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) IN ($equipment) AND IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) > 0)))) $filtered_tickets AND `tickets`.`deleted` = 0 ORDER BY IFNULL(NULLIF(`ticket_schedule`.`to_do_start_time`,''),IFNULL(NULLIF(`tickets`.`start_time`,'00:00'),`tickets`.`to_do_start_time`)) ASC";
    $tickets_result = mysqli_fetch_all(mysqli_query($dbc, $tickets_query),MYSQLI_ASSOC);

    //Tasks
    $tasks_query = "SELECT * FROM `tasklist` WHERE `contactid` = '".$contactid."' AND `task_tododate` = '".$current_day."' AND `deleted` = 0";
    $tasks_result = mysqli_fetch_all(mysqli_query($dbc, $tasks_query),MYSQLI_ASSOC);

    //Item Layout
    if($daysheet_styling == 'card') {
        $row_open = '<div class="block-group-daysheet">';
        $row_open_shifts = '<div class="block-group-daysheet" style="padding: 5px;">';
        $row_close = '</div>';
    } else {
        $row_open = '<li>';
        $row_close = '</li>';
    }
    ?>

    <div class="col-xs-12">
        <div class="weekly-div" style="overflow-y: hidden;">
            <h4 style="font-weight: normal;"><?= $day_of_week ?></h4>
            <?php if($daysheet_styling != 'card') { ?>
            <ul>
            <?php } ?>
            <?php
                $no_records = true;
                if (in_array('Reminders', $daysheet_fields_config)) {
                    foreach ($reminders_result as $reminder) {
                        $reminder_url = get_reminder_url($dbc, $reminder, 1);
                        $slider = 1;
                        if(empty($reminder_url)) {
                            $slider = 0;
                            $reminder_url = get_reminder_url($dbc, $reminder);
                        }
                        if(!empty($reminder_url)) {
                            if($slider == 1) {
                                $reminder_label = '<a href="" onclick="overlayIFrameSlider(\''.$reminder_url.'\'); return false;">Reminder: '.$reminder['subject'].'</a>';
                            } else {
                                $reminder_label = '<a href="'.$reminder_url.'">Reminder: '.$reminder['subject'].'</a>';
                            }
                        } else {
                            $reminder_label = '<div class="daysheet-span">Reminder: '.$reminder['subject'].'</div>';
                        }
                        echo $row_open.$reminder_label.$row_close;
                        $no_records = false;
                    }
                    foreach ($sales_reminders_result as $reminder) {
                        echo $row_open.'<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Sales/sale.php?iframe_slider=1&p=details&id='.$reminder['salesid'].'\'); return false;" style="color: black;">Follow Up Sales: Sales #'.$reminder['salesid'].'</a>'.$row_close;
                        $no_records = false;
                    }
                    foreach ($so_reminders_result as $reminder) {
                        echo $row_open.'<a href="../Sales Order/index.php?p=preview&id='.$reminder['posid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" style="color: black;">Follow Up '.SALES_ORDER_NOUN.': '.($reminder['name'] != '' ? $reminder['name'] : SALES_ORDER_NOUN.' #'.$reminder['posid']).'</a>'.$row_close;
                        $no_records = false;
                    }
                    foreach ($sot_reminders_result as $reminder) {
                        echo $row_open.'<a href="../Sales Order/order.php?p=details&sotid='.$reminder['sotid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" style="color: black;">Follow Up '.SALES_ORDER_NOUN.': '.($reminder['name'] != '' ? $reminder['name'] : SALES_ORDER_NOUN.' Form #'.$reminder['sotid']).'</a>'.$row_close;
                        $no_records = false;
                    }
                    foreach ($estimates_reminders_result as $reminder) {
                        echo $row_open.'<a href="../Estimate/estimates.php?view='.$reminder['estimateid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" style="color: black;">Follow Up Estimate: '.$reminder['estimate_name'].'</a>'.$row_close;
                        $no_records = false;
                    }
                    foreach ($projects_reminders_result as $reminder) {
                        echo $row_open.'<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Project/projects.php?iframe_slider=1&edit='.$reminder['projectid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'\'); return false;" style="color: black;">Follow Up Project: '.$reminder['project_name'].'</a>'.$row_close;
                        $no_records = false;
                    }
                    foreach ($pfu_reminders_result as $reminder) {
                        echo $row_open.'<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Project/projects.php?iframe_slider=1&edit='.$reminder['projectid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'\'); return false;" style="color: black;">Follow Up Project: '.$reminder['project_name'].'</a>'.$row_close;
                        $no_records = false;
                    }
                    foreach ($cert_reminders_result as $reminder) {
                        echo $row_open.'<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Certificate/edit_certificate.php?edit='.$reminder['certificateid'].'\'); return false;" style="color: black;">Certificate Reminder: '.$reminder['title'].'</a>'.$row_close;
                        $no_records = false;
                    }
                    foreach ($alerts_reminders_result as $reminder) {
                        echo $row_open.'<a href="'.$reminder['alert_link'].'" style="color: black;">Alert: '.$reminder['alert_text'].' - '.$reminder['alert_link'].''.$row_close;
                        $no_records = false;
                    }
                    foreach ($inc_rep_reminders_result as $reminder) {
                        echo $row_open.'<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Incident Report/add_incident_report.php?incidentreportid='.$reminder['incidentreportid'].'\'); return false;" style="color: black;">Follow Up '.INC_REP_NOUN.': '.$reminder['type'].' #'.$reminder['incidentreportid'].'</a>'.$row_close;
                        $no_records = false;
                    }
                    foreach ($equipment_followup_reminders_result as $reminder) {
                        echo $row_open.'<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'Equipment/edit_equipment.php?edit='.$reminder['equipmentid'].'&iframe_slider=1\'); return false;" style="color: black;">Follow Up Equipment ('.$reminder['category'].' #'.$reminder['unit_number'].'): Next Service Date coming up on '.$reminder['next_service_date'].'</a>'.$row_close;
                        $no_records = false;
                    }
                    foreach ($equipment_service_reminders_result as $reminder) {
                        echo $row_open.'<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'Equipment/edit_equipment.php?edit='.$reminder['equipmentid'].'&iframe_slider=1\'); return false;" style="color: black;">Equipment Service Reminder ('.$reminder['category'].' #'.$reminder['unit_number'].'): Service Date scheduled for '.$reminder['next_service_date'].'</a>'.$row_close;
                        $no_records = false;
                    }
                    foreach ($inc_rep_flags_result as $reminder) {
                        echo $row_open.'<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Incident Report/add_incident_report.php?incidentreportid='.$reminder['incidentreportid'].'\'); return false;" style="color: black;">Flagged '.INC_REP_NOUN.': '.$reminder['type'].' #'.$reminder['incidentreportid'].(!empty($reminder['flag_label']) ? ' - '.$reminder['flag_label'] : '').'</a>'.$row_close;
                        $no_records = false;
                    }
                }
                if (in_array('Tickets', $daysheet_fields_config)) {
                    $row_open_ticket = $row_open;
                    foreach ($tickets_result as $ticket) {
                        $delivery_color = get_delivery_color($dbc, $ticket['delivery_type']);
                        if(!empty($delivery_color)) {
                            $delivery_style = 'style="background-color: '.$delivery_color.';"';
                        } else {
                            $delivery_style = '';
                        }
                        if($daysheet_styling == 'card') {
                            $row_open_ticket = '<div class="block-group-daysheet" '.$delivery_style.'>';
                        }
                        $label = daysheet_ticket_label($dbc, $daysheet_ticket_fields, $ticket, $current_day);
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
                        echo $row_open_ticket.'<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'&stop='.$ticket['stop_id'].'&action_mode='.$ticket_action_mode.'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\'); return false;">'.$icon_img.$label.'</a>'.$row_close;
                        $no_records = false;
                    }
                }
                if (in_array('Tasks', $daysheet_fields_config)) {
                    foreach ($tasks_result as $task) {
						$label = ($task['businessid'] > 0 ? get_contact($dbc, $task['businessid'], 'name').', ' : '').($task['projectid'] > 0 ? PROJECT_NOUN.' #'.$task['projectid'].' '.get_project($dbc,$task['projectid'],'project_name') : '');
                        echo $row_open.'<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Tasks_Updated/add_task.php?tasklistid='.$task['tasklistid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'\'); return false;" style="color: black;">'.($label != '' ? $label.'<br />' : '').'Task: '.$task['task_milestone_timeline'].' - '.$task['heading'].'</a>'.$row_close;
                        $no_records = false;
                    }
                }
                if (in_array('Shifts', $daysheet_fields_config)) {
                    include_once ('../Calendar/calendar_functions_inc.php');
                    $day_of_week = date('l', strtotime($current_day));
                    $shifts = checkShiftIntervals($dbc, $_SESSION['contactid'], $day_of_week, $current_day, 'all');
                    if(!empty($shifts)) {
                        foreach ($shifts as $shift) {
                            echo $row_open_shifts;
                            if(!empty($shift['heading'])) {
                                echo $shift['heading'].'<br>';
                            }
                            if(!empty($shift['dayoff_type'])) {
                                echo 'Day Off: '.date('h:i a', strtotime($shift['starttime'])).' - '.date('h:i a', strtotime($shift['endtime'])).'<br>';
                                echo 'Day Off Type: '.$shift['dayoff_type'];
                            } else {
                                $total_booked_time += (strtotime($shift['endtime']) - strtotime($shift['starttime']));
                                echo 'Shift: '.date('h:i a', strtotime($shift['starttime'])).' - '.date('h:i a', strtotime($shift['endtime']));
                                if(!empty($shift['break_starttime']) && !empty($shift['break_endtime'])) {
                                    echo '<br>';
                                    echo 'Break: '.date('h:i a', strtotime($shift['break_starttime'])).' - '.date('h:i a', strtotime($shift['break_endtime']));
                                }
                                if(!empty($shift['clientid'])) {
                                    echo '<br>';
                                    echo get_contact($dbc, $shift['clientid'], 'category').': ';
                                    echo '<a href="'.WEBSITE_URL.'/'.ucfirst(get_contact($dbc, $shift['clientid'], 'tile_name')).'/contacts_inbox.php?edit='.$shift['clientid'].'" style="padding: 0; display: inline;">'.get_contact($dbc, $shift['clientid']).'</a>';
                                }
                            }
                            if(!empty($shift['notes'])) {
                                echo '<br>';
                                echo 'Notes: '.html_entity_decode($shift['notes']);
                            }
                            echo $row_close;
                            $no_records = false;
                        }
                        if($daysheet_styling == 'card') {
                            echo '<div class="block-group-daysheet" style="padding: 5px;">Total Booked Time: '.(sprintf('%02d', floor($total_booked_time / 3600)).':'.sprintf('%02d', floor($total_booked_time % 3600 / 60))).'</div>';
                        } else {
                            echo '<br>Total Booked Time: '.(sprintf('%02d', floor($total_booked_time / 3600)).':'.sprintf('%02d', floor($total_booked_time % 3600 / 60))).'';
                        }
                    }
                }
                if ($no_records) {
                    echo 'No records found.';
                }
            ?>
            <?php if($daysheet_styling != 'card') { ?>
            </ul>
            <?php } ?>
        </div>
        <a class="expand-div-link" href="" onclick="expandDiv(this); return false;"><div style="font-size: 1.5em; text-align: center;">...</div></a>
        <hr>
    </div>
    <?php }
} ?>