<?php include_once('config.php');
include('../navigation.php'); ?>
<div class="container">
    <div class="row">
        <?php $ticket_next_step_timesheet = explode(',',get_config($dbc, 'ticket_next_step_timesheet'));
        $current_time_tracking = $dbc->query("SELECT * FROM `time_cards` WHERE `timer_start` > 0 AND `type_of_time` IN ('day_tracking') AND `staff`='".$_SESSION['contactid']."'")->fetch_assoc()['day_tracking_type'];
        $staff_tasks = $dbc->query("SELECT * FROM `task_types` WHERE `deleted`=0 AND IFNULL(`description`,'') != '' ORDER BY `sort`, `category`");
        while($task = $staff_tasks->fetch_assoc()) {
            if(in_array_any(['all_tasks','task_'.config_safe_str($task['category'])],$ticket_next_step_timesheet)) { ?>
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12" align="center"><span class="dashboard link" style="width: calc(100% - 45px);"><a onclick="$.post('../Timesheet/start_day.php', { submit: ($(this).hasClass('support-btn') ? 'end_day' : 'start_day'), staff_start: ['<?= $_SESSION['contactid'] ?>'], staff_end: ['<?= $_SESSION['contactid'] ?>'], day_tracking_type: '<?= $task['description'] ?>' }); $(this).toggleClass('support-btn'); $('.support-btn').not(this).removeClass('support-btn');" class="cursor-hand <?= $current_time_tracking == $task['description'] ? 'support-btn' : '' ?>"><?= $task['description'] ?></a></span></div>
            <?php }
        }
        if(in_array('next_ticket',$ticket_next_step_timesheet)) {
            $daily_date = date('Y-m-d');
            $calendar_checkmark_status = get_config($dbc, 'calendar_checkmark_status');
            if(empty($calendar_checkmark_status)) {
                $calendar_checkmark_status = ['Complete'];
            } else {
                $calendar_checkmark_status = explode('*#*', $calendar_checkmark_status);
            }
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
                $warehouse_query = " AND IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''),IFNULL(`tickets`.`city`,''))) NOT IN (SELECT CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')) FROM `contacts` WHERE `category`='Warehouses')";
            }
            $pickup_query = '';
            if(in_array('Combine Pick Up Stops',$daysheet_ticket_fields)) {
                $pickup_query = " AND IFNULL(`ticket_schedule`.`type`,'') != 'Pick Up'";
            }
            $contactid = $_SESSION['contactid'];
            $tickets_query = "SELECT `tickets`.*, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, CONCAT('<br>',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, `ticket_schedule`.`id` `stop_id`, `ticket_schedule`.`eta`, `ticket_schedule`.`client_name`, IFNULL(`ticket_schedule`.`equipmentid`, `tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`address`, `tickets`.`address`) `address`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`to_do_start_time`, IFNULL(NULLIF(`tickets`.`start_time`,'00:00'),`tickets`.`to_do_start_time`)) `to_do_start_time`, CONCAT(`start_available`,' - ',`end_available`) `availability`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, IFNULL(`ticket_schedule`.`map_link`,`tickets`.`google_maps`) `map_link`, `ticket_schedule`.`notes` `delivery_notes`, `tickets`.`siteid` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ((internal_qa_date = '".$daily_date."' AND CONCAT(',',IFNULL(`internal_qa_contactid`,''),',') LIKE '%,".$contactid.",%') OR (`deliverable_date` = '".$daily_date."' AND CONCAT(',',IFNULL(`deliverable_contactid`,''),',') LIKE '%,".$contactid.",%') OR ((`tickets`.`to_do_date` = '".$daily_date."' OR '".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR `ticket_schedule`.`to_do_date`='".$daily_date."' OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND ((CONCAT(',',IFNULL(IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`),''),',') LIKE '%,".$contactid.",%') OR (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) IN ($equipment) AND IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) > 0)))) ".$warehouse_query.$pickup_query." $filtered_tickets AND `tickets`.`deleted` = 0 AND IFNULL(`ticket_schedule`.`status`,`tickets`.`status`) NOT IN ('".implode("','",$calendar_checkmark_status)."') ORDER BY IFNULL(NULLIF(`ticket_schedule`.`to_do_start_time`,''),IFNULL(NULLIF(`tickets`.`start_time`,'00:00'),`tickets`.`to_do_start_time`)) ASC";
            $ticket = mysqli_query($dbc, $tickets_query);
            if($ticket->num_rows > 0) {
                $update_time = get_config($dbc, 'scheduling_calendar_est_time');
                if($update_time == 'auto_sort') { ?>
                    <script src="../Calendar/map_sorting.js"></script>
                <?php }
                $ticket = $ticket->fetch_assoc(); ?>
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12" align="center"><span class="dashboard link" style="width: calc(100% - 45px);"><a href="../Ticket/index.php?<?= (empty($_GET['tile_name']) ? '' : 'tile_name='.$_GET['tile_name'].'&').(empty($_GET['tile_group']) ? '' : 'tile_group='.$_GET['tile_group'].'&') ?>edit=<?= $ticket['ticketid'] ?>&action_mode=<?= $_GET['action_mode'] ?>" onclick="<?= $update_time == 'auto_sort' ? 'sort_by_map(\''.date('Y-m-d').'\', \''.$ticket['equipmentid'].'\', \'\', \'\', \'true\');' : '' ?>"><?= get_ticket_label($dbc, $ticket) ?></a></span></div>
            <?php }
        }
        if(in_array('break',$ticket_next_step_timesheet)) { ?>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12" align="center"><span class="dashboard link" style="width: calc(100% - 45px);"><a onclick="$.post('../Timesheet/start_day.php', { submit: ($(this).hasClass('support-btn') ? 'end_break' : 'day_break'), staff_end: ['<?= $_SESSION['contactid'] ?>'] }); $(this).toggleClass('support-btn'); $('.support-btn').not(this).removeClass('support-btn');" class="cursor-hand <?= strpos($current_time_tracking,'Break') === 0 ? 'support-btn' : '' ?>">Break</a></span></div>
        <?php }
        if(in_array('end_day',$ticket_next_step_timesheet)) { ?>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12" align="center"><span class="dashboard link" style="width: calc(100% - 45px);"><a onclick="$.post('../Timesheet/start_day.php', { submit: 'end_day', staff_end: ['<?= $_SESSION['contactid'] ?>'] });" href="<?= WEBSITE_URL ?>/home.php"><?= get_config($dbc, 'timesheet_end_tile') ?></a></span></div>
        <?php } ?>
    </div>
</div>
<?php include('../footer.php');