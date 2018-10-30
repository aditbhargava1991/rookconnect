<!-- Daysheet Daily Overview -->
<?php
$reminderids = [];
// Retrieve Data and Populate Daysheet Tables
//Reminders
$get_from = 'daysheet';
$search_user = $contactid;
$today_date = $daily_date;
$fetch_until = $daily_date;
include('../Notification/get_notifications.php');

//Tickets
$equipment = [];
$equipment_ids = $dbc->query("SELECT `equipmentid` FROM `equipment_assignment_staff` LEFT JOIN `equipment_assignment` ON `equipment_assignment_staff`.`equipment_assignmentid`=`equipment_assignment`.`equipment_assignmentid` WHERE `equipment_assignment_staff`.`deleted`=0 AND `equipment_assignment`.`deleted`=0 AND `equipment_assignment_staff`.`contactid`='$contactid' AND DATE(`equipment_assignment`.`start_date`) <= '$daily_date' AND DATE(`equipment_assignment`.`end_date`) >= '$daily_date' AND CONCAT(',',`hide_staff`,',') NOT LIKE '%,$contactid,%' AND CONCAT(',',`hide_days`,',') NOT LIKE '%,$daily_date,%'");
while($equipment[] = $equipment_ids->fetch_assoc()['equipmentid']) { }
$equipment = implode(',',array_filter($equipment));
if($equipment == '') {
	$equipment = 0;
}
if(strtotime($daily_date.' 23:59:59') < time() && get_config($dbc, 'timesheet_hide_past_days') == '1' && $dbc->query("SELECT COUNT(*) `count` FROM `time_cards` WHERE `date`='$daily_date' AND `staff`='{$_SESSION['contactid']}' AND `end_time` IS NULL AND `start_time` IS NOT NULL")->fetch_assoc()['count'] == 0) {
	$filtered_tickets = " AND 1=0 ";
}
$combine_category = get_config($dbc, 'daysheet_ticket_combine_contact_type');
$warehouse_query = '';
if(in_array('Combine Warehouse Stops',$daysheet_ticket_fields)) {
    $warehouse_query = " AND REPLACE(REPLACE(IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''),IFNULL(`tickets`.`city`,''))),' ',''),'-','') NOT IN (SELECT REPLACE(REPLACE(CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')),' ',''),'-','') FROM `contacts` WHERE `category`='Warehouses')";
}
$pickup_query = '';
if(in_array('Combine Pick Up Stops',$daysheet_ticket_fields)) {
    $pickup_query = " AND IFNULL(`ticket_schedule`.`type`,'') != 'Pick Up'";
}
$tickets_query = "SELECT `tickets`.*, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, CONCAT('<br>',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, `ticket_schedule`.`id` `stop_id`, `ticket_schedule`.`eta`, `ticket_schedule`.`client_name`, IFNULL(`ticket_schedule`.`address`, `tickets`.`address`) `address`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`to_do_start_time`, IFNULL(NULLIF(`tickets`.`start_time`,'00:00'),`tickets`.`to_do_start_time`)) `to_do_start_time`, CONCAT(`start_available`,' - ',`end_available`) `availability`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, IFNULL(`ticket_schedule`.`map_link`,`tickets`.`google_maps`) `map_link`, `ticket_schedule`.`notes` `delivery_notes`, `tickets`.`siteid` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ((internal_qa_date = '".$daily_date."' AND CONCAT(',',IFNULL(`internal_qa_contactid`,''),',') LIKE '%,".$contactid.",%') OR (`deliverable_date` = '".$daily_date."' AND CONCAT(',',IFNULL(`deliverable_contactid`,''),',') LIKE '%,".$contactid.",%') OR ((`tickets`.`to_do_date` = '".$daily_date."' OR '".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR `ticket_schedule`.`to_do_date`='".$daily_date."' OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND ((CONCAT(',',IFNULL(IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`),''),',') LIKE '%,".$contactid.",%') OR (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) IN ($equipment) AND IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) > 0)))) ".$warehouse_query.$pickup_query." $filtered_tickets AND `tickets`.`deleted` = 0 ORDER BY ".(in_array('Sort Completed to End',$daysheet_ticket_fields) ? "IFNULL(`ticket_schedule`.`status`,`tickets`.`status`)='$completed_ticket_status', " : '')."IFNULL(NULLIF(`ticket_schedule`.`to_do_start_time`,''),IFNULL(NULLIF(`tickets`.`start_time`,'00:00'),`tickets`.`to_do_start_time`)) ASC";
$tickets_result = mysqli_fetch_all(mysqli_query($dbc, $tickets_query),MYSQLI_ASSOC);

//Tasks
$tasks_query = "SELECT * FROM `tasklist` WHERE `contactid` = '".$contactid."' AND `task_tododate` = '".$daily_date."' AND `deleted` = 0";
$tasks_result = mysqli_fetch_all(mysqli_query($dbc, $tasks_query),MYSQLI_ASSOC);

//Checklists
// $user_fav_checklists = get_user_settings()['checklist_fav'];
// $day_of_week = date('w', strtotime($daily_date));
// $day_of_month = date('j', strtotime($daily_date));
// $user_fav_checklists = "'".implode("','", array_filter(explode(',',$user_fav_checklists)))."'";
// $checklists_query = "SELECT * FROM `checklist` WHERE `checklistid` IN ($user_fav_checklists) AND (`assign_staff` LIKE '%,$contactid,%' OR `assign_staff`=',ALL,') AND (`checklist_type` = 'daily' OR (`checklist_type` = 'weekly' AND `reset_day` = '$day_of_week') OR (`checklist_type` = 'monthly' AND `reset_day` = '$day_of_month') OR (`checklist_type` = 'ongoing')) AND `deleted`=0";
// $checklists_result = mysqli_fetch_all(mysqli_query($dbc, $checklists_query),MYSQLI_ASSOC);
$checklists_query = "SELECT * FROM `checklist_actions` WHERE `contactid` = '".$contactid."' AND `action_date` = '".$daily_date."' AND `deleted` = 0";
$checklists_result = mysqli_fetch_all(mysqli_query($dbc, $checklists_query),MYSQLI_ASSOC);

//Communication
$comm_query = "SELECT * FROM `email_communication` WHERE `deleted`=0 AND `created_by`='".$_SESSION['contactid']."' AND `today_date`='$daily_date'";
$comm_result = mysqli_fetch_all(mysqli_query($dbc, $comm_query),MYSQLI_ASSOC);

//Support Requests
$support_query = "SELECT * FROM `support` WHERE (`assigned`='' OR CONCAT(',',`assigned`,',') LIKE ',".$contactid.",') AND `deleted` = 0";
$support_result = mysqli_fetch_all(mysqli_query($dbc, $support_query),MYSQLI_ASSOC);

//Communications
$comm_query = "SELECT * FROM `email_communication` WHERE `deleted`=0 AND `created_by`='".$_SESSION['contactid']."' AND `today_date`='$daily_date'";
$comm_result = mysqli_fetch_all(mysqli_query($dbc, $comm_query),MYSQLI_ASSOC);
?>
<script type="text/javascript">
$(document).ready(function () {
    $('input[name="daysheet_reminder"]').on('click', function() {
        var daysheet_reminder = $(this);
        var daysheetreminderid = this.value;
        var done = 0;
        if ($(this).is(':checked')) {
            done = 1;
        }
        $.ajax({
            url: '../Profile/profile_ajax.php?fill=daysheet_reminders',
            method: 'POST',
            data: {
                daysheetreminderid: daysheetreminderid,
                done: done
            },
            success: function(response) {
                if (done == 1) {
                    daysheet_reminder.closest('p,.daysheet_row').find('span').css('text-decoration', 'line-through');
                } else {
                    daysheet_reminder.closest('p,.daysheet_row').find('span').css('text-decoration', 'none');
                }
            }
        });
    });
});
</script>

<?php if (in_array('Reminders', $daysheet_fields_config)) {
    $reminders_list = mysqli_query($dbc, "SELECT * FROM `daysheet_reminders` WHERE `date` = '".$daily_date."' AND `contactid` = '".$contactid."' AND `deleted` = 0");
    $num_rows = mysqli_num_rows($reminders_list); ?>
    <h4 style="font-weight: normal;">Reminders</h4>
    <?php if ($num_rows > 0) {
        if($daysheet_styling != 'card') {
            echo '<ul id="reminders_daily">';
        }
        $reminders_list = mysqli_fetch_all($reminders_list,MYSQLI_ASSOC);
        foreach ($reminders_list as $daysheet_reminder) {
            $reminder_label = daysheet_get_reminder($dbc, $daysheet_reminder);
            if(!empty($reminder_label)) {
                if($daysheet_styling == 'card') {
                    echo '<div class="col-xs-12 daysheet_row"><div class="col-xs-2" style="max-width: 35px;"><input style="position: relative; vertical-align: middle; top: 10px; height: 20px; width: 20px;" type="checkbox" name="daysheet_reminder" value="'.$daysheet_reminder['daysheetreminderid'].'" '.($daysheet_reminder['done'] == 1 ? 'checked="checked"' : '').'></div><div class="col-xs-10 block-group-daysheet"><span '.($daysheet_reminder['done'] == 1 ? 'style="text-decoration: line-through;"' : '').'>'.$reminder_label.'</span></div></div>';
                } else {
                    echo '<p style="font-weight: normal;"><input style="position: relative; vertical-align: middle; top: -0.25em;" class="form-checkbox" type="checkbox" name="daysheet_reminder" value="'.$daysheet_reminder['daysheetreminderid'].'" '.($daysheet_reminder['done'] == 1 ? 'checked="checked"' : '').'>&nbsp;&nbsp;<span '.($daysheet_reminder['done'] == 1 ? 'style="text-decoration: line-through;"' : '').'>'.$reminder_label.'</span></p>';
                }
            }
        }
        if($daysheet_styling != 'card') {
            echo '</ul>';
        }
    } else {
        echo '<ul id="reminders_daily">';
        echo 'No records found.';
        echo '</ul>';
    } ?>
    <div class="clearfix"></div>
    <hr>
<?php } ?>

<?php if (in_array('Tickets', $daysheet_fields_config)) { ?>
    <h4 style="font-weight: normal;"><?= TICKET_TILE ?></h4>
    <?php $no_tickets = true;
    $combined_tickets_shown = [];
    if(in_array('Combine Warehouse Stops', $daysheet_ticket_fields)) {
        $combined_query = "SELECT `tickets`.*, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, CONCAT('<br>',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, `ticket_schedule`.`id` `stop_id`, `ticket_schedule`.`eta`, `ticket_schedule`.`client_name`, IFNULL(`ticket_schedule`.`address`, `tickets`.`address`) `address`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`to_do_start_time`, IFNULL(NULLIF(`tickets`.`start_time`,'00:00'),`tickets`.`to_do_start_time`)) `to_do_start_time`, CONCAT(`start_available`,' - ',`end_available`) `availability`, `ticket_schedule`.`status` `schedule_status` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ((internal_qa_date = '".$daily_date."' AND CONCAT(',',IFNULL(`internal_qa_contactid`,''),',') LIKE '%,".$contactid.",%') OR (`deliverable_date` = '".$daily_date."' AND CONCAT(',',IFNULL(`deliverable_contactid`,''),',') LIKE '%,".$contactid.",%') OR ((`tickets`.`to_do_date` = '".$daily_date."' OR '".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR `ticket_schedule`.`to_do_date`='".$daily_date."' OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND ((CONCAT(',',IFNULL(IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`),''),',') LIKE '%,".$contactid.",%') OR (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) IN ($equipment) AND IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) > 0)))) AND REPLACE(REPLACE(IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''),IFNULL(`tickets`.`city`,''))),' ',''),'-','') IN (SELECT REPLACE(REPLACE(CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')),' ',''),'-','') FROM `contacts` WHERE `category`='".$combine_category."') $filtered_tickets AND `tickets`.`deleted` = 0 ORDER BY IFNULL(`ticket_schedule`.`address`,`tickets`.`address`), ".(in_array('Sort Completed to End',$daysheet_ticket_fields) ? "IFNULL(`ticket_schedule`.`status`,`tickets`.`status`)='$completed_ticket_status', " : '')."IFNULL(NULLIF(`ticket_schedule`.`to_do_start_time`,''),IFNULL(NULLIF(`tickets`.`start_time`,'00:00'),`tickets`.`to_do_start_time`)) ASC";
        $combined_result = mysqli_fetch_all(mysqli_query($dbc, $combined_query),MYSQLI_ASSOC);
        if(!empty($combined_result)) {
            $delivery_color = get_delivery_color($dbc, 'warehouse');
            if(!empty($delivery_color)) {
                $delivery_style = 'style="background-color: '.$delivery_color.';"';
            } else {
                $delivery_style = '';
            }
            if($daysheet_styling == 'card') {
                echo '<div class="block-group-daysheet" '.$delivery_style.'>';
            } else {
                echo '<ul id="tickets_daily">';
                echo '<li>';
            }
            $address = '';
            foreach ($combined_result as $ticket) {
                if(strtolower($address) != strtolower($ticket['address'])) {
                    $address = $ticket['address'];
                    echo '<h4 class="pad-5">'.$combine_category.": ".$address.'</h4>';
                }
                $label = $ticket['client_name'].' - '.$ticket['ticket_label'];
                echo '<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'&stop='.$ticket['stop_id'].'&action_mode='.$ticket_action_mode.'" class="inline" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\'); return false;" '.$opacity_styling.'>'.$icon_img.$label.'</a>';

                if(in_array('Combined Details with Confirm',$daysheet_ticket_fields)) {
                    echo '<label class="form-checkbox any-width pull-right"><input type="checkbox" '.($ticket['schedule_status'] == $completed_ticket_status || $ticket['status'] == $completed_ticket_status ? 'disabled checked' : '').' onclick="if(confirm(\'By checking off this box, you are agreeing it has been loaded onto your truck\')) { setStatus(\''.$ticket['ticketid'].'\',\''.$ticket['stop_id'].'\',\''.$completed_ticket_status.'\') } else { return false; }">'.$completed_ticket_status.'</label>';
                }
                echo '<div class="clearfix"></div>';
                $combined_tickets_shown[] = $ticket['stop_id'];
            }

            if($daysheet_styling != 'card') {
                echo '</li>';
                echo '</ul>';
            } else {
                echo '</div>';
            }
            $no_tickets = false;
        }
    }
    if(in_array('Combine Pick Up Stops', $daysheet_ticket_fields)) {
        $combined_query = "SELECT `tickets`.*, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, CONCAT('<br>',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, `ticket_schedule`.`id` `stop_id`, `ticket_schedule`.`eta`, `ticket_schedule`.`client_name`, IFNULL(`ticket_schedule`.`address`, `tickets`.`address`) `address`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`to_do_start_time`, IFNULL(NULLIF(`tickets`.`start_time`,'00:00'),`tickets`.`to_do_start_time`)) `to_do_start_time`, CONCAT(`start_available`,' - ',`end_available`) `availability`, `ticket_schedule`.`status` `schedule_status` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ((internal_qa_date = '".$daily_date."' AND CONCAT(',',IFNULL(`internal_qa_contactid`,''),',') LIKE '%,".$contactid.",%') OR (`deliverable_date` = '".$daily_date."' AND CONCAT(',',IFNULL(`deliverable_contactid`,''),',') LIKE '%,".$contactid.",%') OR ((`tickets`.`to_do_date` = '".$daily_date."' OR '".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR `ticket_schedule`.`to_do_date`='".$daily_date."' OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND ((CONCAT(',',IFNULL(IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`),''),',') LIKE '%,".$contactid.",%') OR (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) IN ($equipment) AND IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) > 0)))) AND `ticket_schedule`.`type` = 'Pick Up' ".$warehouse_query." $filtered_tickets AND `tickets`.`deleted` = 0 ORDER BY IFNULL(`ticket_schedule`.`address`,`tickets`.`address`), ".(in_array('Sort Completed to End',$daysheet_ticket_fields) ? "IFNULL(`ticket_schedule`.`status`,`tickets`.`status`)='$completed_ticket_status', " : '')."IFNULL(NULLIF(`ticket_schedule`.`to_do_start_time`,''),IFNULL(NULLIF(`tickets`.`start_time`,'00:00'),`tickets`.`to_do_start_time`)) ASC";
        $combined_result = mysqli_fetch_all(mysqli_query($dbc, $combined_query),MYSQLI_ASSOC);
        if(!empty($combined_result)) {
            $delivery_color = get_delivery_color($dbc, 'Pick Up');
            if(!empty($delivery_color)) {
                $delivery_style = 'style="background-color: '.$delivery_color.';"';
            } else {
                $delivery_style = '';
            }
            if($daysheet_styling != 'card') {
                echo '<ul id="tickets_daily">';
                echo '<li>';
            } else {
                echo '<div class="block-group-daysheet" '.$delivery_style.'>';
            }
            $address = '';
            foreach ($combined_result as $ticket) {
                if($address != $ticket['address']) {
                    $address = $ticket['address'];
                    echo '<h4 class="pad-5">Pick Up: '.$address.'</h4>';
                }
                $label = $ticket['client_name'].' - '.$ticket['ticket_label'];
                echo '<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'&stop='.$ticket['stop_id'].'&action_mode='.$ticket_action_mode.'" class="inline" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\'); return false;" '.$opacity_styling.'>'.$icon_img.$label.'</a>';

                if(in_array('Combined Details with Confirm',$daysheet_ticket_fields)) {
                    echo '<label class="form-checkbox any-width pull-right"><input type="checkbox" '.($ticket['schedule_status'] == $completed_ticket_status || $ticket['status'] == $completed_ticket_status ? 'disabled checked' : '').' onclick="if(confirm(\'By checking off this box, you are agreeing it has been loaded onto your truck\')) { setStatus(\''.$ticket['ticketid'].'\',\''.$ticket['stop_id'].'\',\''.$completed_ticket_status.'\') } else { return false; }">'.$completed_ticket_status.'</label>';
                }
                echo '<div class="clearfix"></div>';
                $combined_tickets_shown[] = $ticket['stop_id'];
            }

            if($daysheet_styling != 'card') {
                echo '</li>';
                echo '</ul>';
            } else {
                echo '</div>';
            }
            $no_tickets = false;
        }
    }
	if (!empty($tickets_result)) {
        if($daysheet_styling != 'card') {
            echo '<ul id="tickets_daily">';
        }
        foreach ($tickets_result as $ticket) {
            if(!($ticket['stop_id'] > 0) || !in_array($ticket['stop_id'], $combined_tickets_shown)) {
                $delivery_color = get_delivery_color($dbc, $ticket['delivery_type']);
                if(!empty($delivery_color)) {
                    $delivery_style = 'style="background-color: '.$delivery_color.';"';
                } else {
                    $delivery_style = '';
                }
                if($daysheet_styling == 'card') {
                    echo '<div class="block-group-daysheet" '.$delivery_style.'>';
                } else {
                    echo '<li>';
                }

                $label = daysheet_ticket_label($dbc, $daysheet_ticket_fields, $ticket, $completed_ticket_status, $daily_date);
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

                echo '<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'&stop='.$ticket['stop_id'].'&action_mode='.$ticket_action_mode.'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\'); return false;" '.$opacity_styling.'>'.$icon_img.$label.'</a>';
                echo '<div class="clearfix"></div>';

                if($daysheet_styling == 'card') {
                    echo '</div>';
                } else {
                    echo '</li>';
                }
            }
        }
        if($daysheet_styling != 'card') {
            echo '</ul>';
        }
    } else if($no_tickets) {
        echo '<ul id="tickets_daily">';
        echo 'No records found.';
        echo '</ul>';
    } ?>
    </ul>
    <hr>
<?php } ?>

<?php if (in_array('Tasks', $daysheet_fields_config)) { ?>
    <h4 style="font-weight: normal;"><?= TASK_TILE ?></h4>
    <?php if (!empty($tasks_result)) {
        if($daysheet_styling != 'card') {
            echo '<ul id="tasks_daily">';
        }
        foreach ($tasks_result as $task) {
            if($daysheet_styling == 'card') {
                echo '<div class="block-group-daysheet">';
            }
			$label = ($task['businessid'] > 0 ? get_contact($dbc, $task['businessid'], 'name').', ' : '').($task['projectid'] > 0 ? PROJECT_NOUN.' #'.$task['projectid'].' '.get_project($dbc,$task['projectid'],'project_name') : '');
            echo '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Tasks_Updated/add_task.php?tasklistid='.$task['tasklistid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'\'); return false;" ><span style="color: black;">'.($label != '' ? $label.'<br />' : '').$task['task_milestone_timeline'].' - '.get_contact($dbc, $task['businessid'], 'name').' - '.$task['heading'].'</span></a>';
            if($daysheet_styling == 'card') {
                echo '</div>';
            }
        }
        if($daysheet_styling != 'card') {
            echo '</ul>';
        }
    } else {
        echo '<ul id="tasks_daily">';
        echo 'No records found.';
        echo '</ul>';
    } ?>
    <hr>
<?php } ?>

<?php if (in_array('Checklists', $daysheet_fields_config)) { ?>
    <?php include('daysheet_checklist_functions.php'); ?>
    <h4 style="font-weight: normal;">Checklists</h4>
    <?php
    if (!empty($checklists_result)) {
        if($daysheet_styling != 'card') {
            echo '<ul id="checklists_daily">';
        }
        foreach ($checklists_result as $checklist_action) {
            if($daysheet_styling == 'card') {
                echo '<div class="block-group-daysheet">';
            } else {
                echo '<li>';
            }

            $checklist_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist_name` AS `cn` LEFT JOIN `checklist` AS `c`ON `c`.`checklistid` = `cn`.`checklistid` WHERE `checklistnameid` = '".$checklist_action['checklistnameid']."'"));
            $label = ($checklist_name['businessid'] > 0 ? get_contact($dbc, $checklist_name['businessid'], 'name').', ' : '').($checklist_name['projectid'] > 0 ? PROJECT_NOUN.' #'.$checklist_name['projectid'].' '.get_project($dbc,$checklist_name['projectid'],'project_name') : '');
            echo '<a href="../Checklist/checklist.php?view='.$checklist_name['checklistid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" style="color: black;'.($checklist_action['done'] == 1 ? 'text-decoration: line-through;' : '').'">'.($label != '' ? $label.'<br />' : '').'Checklist: '.$checklist_name['checklist_name'].' - Item: '.explode('&lt;p&gt;', $checklist_name['checklist'])[0].'</a>';
            if($daysheet_styling == 'card') {
                echo '</div>';
            } else {
                echo '</li>';
            }
        }
        if($daysheet_styling != 'card') {
            echo '</ul>';
        }
    } else {
        echo '<ul id="checklists_daily">';
        echo 'No records found.';
        echo '</ul>';
    } ?>
    <hr>
<?php } ?>

<?php if (in_array('Communication', $daysheet_fields_config)) { ?>
    <h4 style="font-weight: normal;">Communications</h4>
    <?php if (!empty($comm_result)) {
        if($daysheet_styling != 'card') {
            echo '<ul id="comm_daily">';
        }
        foreach ($comm_result as $row) {
			echo '<div class="note_block">';
			if($row['businessid'] > 0) {
				echo BUSINESS_CAT.': <a href="../Contacts/contacts_inbox.php?edit='.$row['businessid'].'" onclick="overlayIFrameSlider(this.href+\'&fields=all_fields\',\'auto\',true,true); return false;">'.get_contact($dbc, $row['businessid'], 'name_company').'</a><br />';
			}
			$individuals = [];
			foreach(array_filter(explode(',',$row['contactid'])) as $row_contactid) {
				$individuals[] = '<a href="../Contacts/contacts_inbox.php?edit='.$row_contactid.'" onclick="overlayIFrameSlider(this.href+\'&fields=all_fields\',\'auto\',true,true); return false;">'.get_contact($dbc, $row_contactid, 'name_company').'</a>';
			}
			if(count($individuals) > 0) {
				echo 'Individuals: '.implode(', ',$individuals).'<br />';
			}
			echo profile_id($dbc, $row['created_by'],false);
			echo '<div class="pull-right" style="width: calc(100% - 3.5em);">';
			echo '<p><b>From: '.$row['from_name'].' &lt;'.$row['from_email'].'&gt;</b><br />';
			echo '<b>To: '.implode('; ',array_filter(explode(',',$row['to_staff'].','.$row['to_contact'].','.$row['new_emailid']))).'</b><br />';
			echo '<b>CC: '.implode('; ',array_filter(explode(',',$row['cc_staff'].','.$row['cc_contact']))).'</b>';
			echo '<b>Subject: '.$row['subject'].'</b></p>';
			echo html_entity_decode($row['email_body']);
			echo '</div><div class="clearfix"></div><hr></div>';
        }
        if($daysheet_styling != 'card') {
            echo '</ul>';
        }
    } else {
        echo '<ul id="comm_daily">';
        echo 'No records found.';
        echo '</ul>';
    } ?>
    <hr>
<?php } ?>

<?php if (in_array('Support', $daysheet_fields_config)) { ?>
    <h4 style="font-weight: normal;">Support</h4>
    <?php
    if (!empty($support_result)) {
        if($daysheet_styling != 'card') {
            echo '<ul id="support_daily">';
        }
        foreach ($support_result as $row) {
            if($daysheet_styling == 'card') {
                echo '<div class="block-group-daysheet">';
            } else {
                echo '<li>';
            }
			echo '<span class="display-field"><b><a href="'.WEBSITE_URL.'/Support/customer_support.php?tab=requests&type='.$row['support_type'].'#'.$row['supportid'].'">Date of Request: '.$row['current_date']."</a></b><br />Software Link: <a href='".$row['software_url']."'>".$row['software_url']."</a><br />User Name: ".$row['software_user_name']."<br />Security Level: ".$row['software_role']."<br />Support Request #".$row['supportid']."<br />".$row['heading']."<hr>".html_entity_decode($row['message']).'</span>';

            if($daysheet_styling == 'card') {
                echo '</div>';
            } else {
                echo '</li>';
            }
        }
        if($daysheet_styling != 'card') {
            echo '</ul>';
        }
    } else {
        echo '<ul id="support_daily">';
        echo 'No records found.';
        echo '</ul>';
    } ?>
    <hr>
<?php } ?>

<?php if (in_array('Shifts', $daysheet_fields_config)) { ?>
    <?php include_once ('../Calendar/calendar_functions_inc.php'); ?>
    <h4 style="font-weight: normal;">Shifts</h4>
    <?php
    $day_of_week = date('l', strtotime($daily_date));
    $shifts = checkShiftIntervals($dbc, $_SESSION['contactid'], $day_of_week, $daily_date, 'all');
    if (!empty($shifts)) {
        if($daysheet_styling != 'card') {
            echo '<ul id="shifts_daily">';
        }
        $total_booked_time = 0;
        foreach($shifts as $shift) {
            if($daysheet_styling == 'card') {
                echo '<div class="block-group-daysheet" style="padding: 5px;">';
            } else {
                echo '<li>';
            }
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

            if($daysheet_styling == 'card') {
                echo '</div>';
            } else {
                echo '</li>';
            }
        }
        if($daysheet_styling == 'card') {
            echo '<div class="block-group-daysheet" style="padding: 5px;">Total Booked Time: '.(sprintf('%02d', floor($total_booked_time / 3600)).':'.sprintf('%02d', floor($total_booked_time % 3600 / 60))).'</div>';
        } else {
            echo '<br>Total Booked Time: '.(sprintf('%02d', floor($total_booked_time / 3600)).':'.sprintf('%02d', floor($total_booked_time % 3600 / 60))).'';
        }
        if($daysheet_styling != 'card') {
            echo '</ul>';
        }
    } else {
        echo '<ul id="shifts_daily">';
        echo 'No shifts found.';
        echo '</ul>';
    } ?>
    <hr>
<?php } ?>

<?php if (in_array('Tags', $daysheet_fields_config)) { ?>
    <h4 style="font-weight: normal;">Tags</h4>
    <?php $tags_html = daysheet_get_tags($dbc, $_SESSION['contactid']);
    if(!empty($tags_html)) {
        if($daysheet_styling != 'card') {
            echo '<ul id="tags_daily">';
        }
        echo $tags_html;
        if($daysheet_styling != 'card') {
            echo '</ul>';
        }
    } else {
        echo '<ul id="tags_daily">';
        echo 'No tags found.';
        echo '</ul>';
    }
}
