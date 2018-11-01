<?php include_once(substr(dirname(__FILE__), 0, -12).'include.php');

if($search_user > 0) {
    if($get_from != 'daysheet' && $get_from != 'calendar') {
        $today_date = date('Y-m-d');
        $fetch_until = date('Y-m-d');

        $latest_date = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `daysheet_reminders` WHERE `contactid` = '".$search_user."' AND `date` <= '".$today_date."' ORDER BY `date` DESC"))['date'];
        if(!empty($latest_date)) {
            $today_date = $latest_date;
        } else {
            $latest_date = date('Y-m-d');
        }
    }

    for($today_date; strtotime($today_date) <= strtotime($fetch_until); $today_date = date('Y-m-d', strtotime($today_date.' + 1 days'))) {
        $reminderids = [];
        //Reminders
        $reminders_query = "SELECT * FROM `reminders` WHERE `reminder_date` = '$today_date' AND `contactid` = '$search_user' AND `deleted` = 0";
        $reminders_result = mysqli_fetch_all(mysqli_query($dbc, $reminders_query),MYSQLI_ASSOC);
        foreach ($reminders_result as $reminder) {
            mysqli_query($dbc, "INSERT INTO `daysheet_reminders` (`reminderid`, `contactid`, `type`, `date`, `done`) SELECT '".$reminder['reminderid']."', '".$search_user."', 'reminder', '".$today_date."', '0' FROM (SELECT COUNT(*) rows FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['reminderid']."' AND `type` = 'reminder' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0) num WHERE num.rows = 0");
            $reminderid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daysheetreminderid` FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['reminderid']."' AND `type` = 'reminder' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0"))['daysheetreminderid'];
            $reminderids[] = $reminderid;
            if($reminder['done'] == 1) {
                mysqli_query($dbc, "UPDATE `daysheet_reminders` SET `done` = 1 WHERE `daysheetreminderid` = '$reminderid'");
            }
        }
        $sales_reminders_query = "SELECT * FROM `sales` WHERE `new_reminder` = '$today_date' AND (`primary_staff` = '$search_user' OR CONCAT(',',`share_lead`,',') LIKE '%,$search_user,%')";
        $sales_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $sales_reminders_query),MYSQLI_ASSOC);
        foreach ($sales_reminders_result as $reminder) {
            mysqli_query($dbc, "INSERT INTO `daysheet_reminders` (`reminderid`, `contactid`, `type`, `date`, `done`) SELECT '".$reminder['salesid']."', '".$search_user."', 'sales', '".$today_date."', '0' FROM (SELECT COUNT(*) rows FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['salesid']."' AND `type` = 'sales' AND `date` = '".$today_date."' AND `contactid` ='".$search_user."' AND `deleted` = 0) num WHERE num.rows = 0");
            $reminderid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daysheetreminderid` FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['salesid']."' AND `type` = 'sales' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0"))['daysheetreminderid'];
            $reminderids[] = $reminderid;
        }
        $so_reminders_query = "SELECT * FROM `sales_order` WHERE `next_action_date` = '$today_date' AND (`primary_staff` = '$search_user' OR CONCAT(',',`assign_staff`,',') LIKE '%,$search_user,%')";
        $so_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $so_reminders_query),MYSQLI_ASSOC);
        foreach ($so_reminders_result as $reminder) {
            mysqli_query($dbc, "INSERT INTO `daysheet_reminders` (`reminderid`, `contactid`, `type`, `date`, `done`) SELECT '".$reminder['posid']."', '".$search_user."', 'sales_order', '".$today_date."', '0' FROM (SELECT COUNT(*) rows FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['posid']."' AND `type` = 'sales_order' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0) num WHERE num.rows = 0");
            $reminderid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daysheetreminderid` FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['posid']."' AND `type` = 'sales_order' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0"))['daysheetreminderid'];
            $reminderids[] = $reminderid;
        }
        $sot_reminders_query = "SELECT * FROM `sales_order_temp` WHERE `next_action_date` = '$today_date' AND (`primary_staff` = '$search_user' OR CONCAT(',',`assign_staff`,',') LIKE '%,$search_user,%') AND `deleted` = 0";
        $sot_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $sot_reminders_query),MYSQLI_ASSOC);
        foreach ($sot_reminders_result as $reminder) {
            mysqli_query($dbc, "INSERT INTO `daysheet_reminders` (`reminderid`, `contactid`, `type`, `date`, `done`) SELECT '".$reminder['sotid']."', '".$search_user."', 'sales_order_temp', '".$today_date."', '0' FROM (SELECT COUNT(*) rows FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['sotid']."' AND `type` = 'sales_order_temp' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0) num WHERE num.rows = 0");
            $reminderid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daysheetreminderid` FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['sotid']."' AND `type` = 'sales_order_temp' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0"))['daysheetreminderid'];
            $reminderids[] = $reminderid;
        }
        $estimates_reminders_query = "SELECT `ea`.*, `e`.`estimate_name` FROM `estimate_actions` AS `ea` JOIN `estimate` AS `e` ON (`ea`.`estimateid`=`e`.`estimateid`) WHERE FIND_IN_SET ('$search_user', `e`.`assign_staffid`) AND `e`.`deleted`=0 AND FIND_IN_SET('$search_user', `ea`.`contactid`) AND `ea`.`deleted`=0 AND `ea`.`due_date`='". date('Y-m-d', strtotime($today_date)) ."'";
        $estimates_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $estimates_reminders_query),MYSQLI_ASSOC);
        foreach ($estimates_reminders_result as $reminder) {
            mysqli_query($dbc, "INSERT INTO `daysheet_reminders` (`reminderid`, `contactid`, `type`, `date`, `done`) SELECT '".$reminder['id']."', '".$search_user."', 'estimate', '".$today_date."', '0' FROM (SELECT COUNT(*) rows FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['id']."' AND `type` = 'estimate' AND `date` = '".$today_date."' AND `contactid` ='".$search_user."' AND `deleted` = 0) num WHERE num.rows = 0");
            $reminderid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daysheetreminderid` FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['id']."' AND `type` = 'estimate' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0"))['daysheetreminderid'];
            $reminderids[] = $reminderid;
        }
        $projects_reminders_query = "SELECT `pa`.*, `p`.`project_name` FROM `project_actions` AS `pa` JOIN `project` AS `p` ON (`pa`.`projectid`=`p`.`projectid`) WHERE FIND_IN_SET ('$search_user', `pa`.`contactid`) AND `p`.`deleted` = 0 AND `pa`.`deleted` = 0 AND `pa`.`due_date` = '".date('Y-m-d', strtotime($today_date))."'";
        $projects_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $projects_reminders_query),MYSQLI_ASSOC);
        foreach ($projects_reminders_result as $reminder) {
            mysqli_query($dbc, "INSERT INTO `daysheet_reminders` (`reminderid`, `contactid`, `type`, `date`, `done`) SELECT '".$reminder['id']."', '".$search_user."', 'project', '".$today_date."', '0' FROM (SELECT COUNT(*) rows FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['id']."' AND `type` = 'project' AND `date` = '".$today_date."' AND `contactid` ='".$search_user."' AND `deleted` = 0) num WHERE num.rows = 0");
            $reminderid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daysheetreminderid` FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['id']."' AND `type` = 'project' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0"))['daysheetreminderid'];
            $reminderids[] = $reminderid;
        }
        $cert_managers = get_config($dbc, 'certificate_reminder_contact');
        $cert_reminders_query = "SELECT * FROM `certificate` WHERE `reminder_date` = '$today_date' AND `deleted` = 0 AND (`contactid` = '$search_user' OR CONCAT(',',`certificate_reminder`,',') LIKE '%,$search_user,%' OR ',$cert_managers,' LIKE '%,$search_user,%')";
        $cert_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $cert_reminders_query),MYSQLI_ASSOC);
        foreach ($cert_reminders_result as $reminder) {
            mysqli_query($dbc, "INSERT INTO `daysheet_reminders` (`reminderid`, `contactid`, `type`, `date`, `done`) SELECT '".$reminder['certificateid']."', '".$search_user."', 'certificate', '".$today_date."', '0' FROM (SELECT COUNT(*) rows FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['certificateid']."' AND `type` = 'certificate' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0) num WHERE num.rows = 0");
            $reminderid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daysheetreminderid` FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['certificateid']."' AND `type` = 'certificate' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0"))['daysheetreminderid'];
            $reminderids[] = $reminderid;
        }
        $pfu_reminders_query = "SELECT * FROM `project` WHERE `followup` = '".$today_date."' AND `project_lead` = '".$search_user."'";
        $pfu_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $pfu_reminders_query),MYSQLI_ASSOC);
        foreach ($pfu_reminders_result as $key => $reminder) {
            $project_exists = false;
            foreach ($projects_reminders_result as $project_action) {
                if ($project_action['projectid'] == $reminder['projectid']) {
                    $project_exists = true;
                    unset($pfu_reminders_result[$key]);
                }
            }
            if (!$project_exists) {
                mysqli_query($dbc, "INSERT INTO `daysheet_reminders` (`reminderid`, `contactid`, `type`, `date`, `done`) SELECT '".$reminder['projectid']."', '".$search_user."', 'project_followup', '".$today_date."', '0' FROM (SELECT COUNT(*) rows FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['projectid']."' AND `type` = 'project_followup' AND `date` = '".$today_date."' AND `contactid` ='".$search_user."' AND `deleted` = 0) num WHERE num.rows = 0");
                $reminderid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daysheetreminderid` FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['projectid']."' AND `type` = 'project_followup' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0"))['daysheetreminderid'];
                $reminderids[] = $reminderid;
            }
        }
        $alerts_reminders_query = "SELECT * FROM `alerts` WHERE `alert_date` = '$today_date' AND `alert_user` = '$search_user'";
        $alerts_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $alerts_reminders_query),MYSQLI_ASSOC);
        foreach ($alerts_reminders_result as $reminder) {
            mysqli_query($dbc, "INSERT INTO `daysheet_reminders` (`reminderid`, `contactid`, `type`, `date`, `done`) SELECT '".$reminder['alertid']."', '".$search_user."', 'alert', '".$today_date."', '0' FROM (SELECT COUNT(*) rows FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['alertid']."' AND `type` = 'alert' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0) num WHERE num.rows = 0");
            $reminderid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daysheetreminderid` FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['alertid']."' AND `type` = 'alert' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0"))['daysheetreminderid'];
            $reminderids[] = $reminderid;
        }
        $inc_rep_reminders_query = "SELECT * FROM `incident_report` WHERE `ir14` = '$today_date' AND `assign_followup` = '$search_user' AND `followup_done` = 0 AND `deleted` = 0";
        $inc_rep_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $inc_rep_reminders_query),MYSQLI_ASSOC);
        foreach ($inc_rep_reminders_result as $reminder) {
            mysqli_query($dbc, "INSERT INTO `daysheet_reminders` (`reminderid`, `contactid`, `type`, `date`, `done`) SELECT '".$reminder['incidentreportid']."', '".$search_user."', 'incident_report', '".$today_date."', '0' FROM (SELECT COUNT(*) rows FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['incidentreportid']."' AND `type` = 'incident_report' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0) num WHERE num.rows = 0");
            $reminderid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daysheetreminderid` FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['incidentreportid']."' AND `type` = 'incident_report' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0"))['daysheetreminderid'];
            $reminderids[] = $reminderid;
        }
        $equipment_followup_reminders_query = "SELECT * FROM `equipment` WHERE `follow_up_date` = '".$today_date."' AND CONCAT(',',`follow_up_staff`,',') LIKE '%,".$search_user.",%' AND `deleted` = 0";
        $equipment_followup_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $equipment_followup_reminders_query),MYSQLI_ASSOC);
        foreach ($equipment_followup_reminders_result as $reminder) {
            mysqli_query($dbc, "INSERT INTO `daysheet_reminders` (`reminderid`, `contactid`, `type`, `date`, `done`) SELECT '".$reminder['equipmentid']."', '".$search_user."', 'equipment_followup', '".$today_date."', '0' FROM (SELECT COUNT(*) rows FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['equipmentid']."' AND `type` = 'equipment_followup' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0) num WHERE num.rows = 0");
            $reminderid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daysheetreminderid` FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['equipmentid']."' AND `type` = 'equipment_followup' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0"))['daysheetreminderid'];
            $reminderids[] = $reminderid;
        }
        $equipment_service_reminders_query = "SELECT * FROM `equipment` WHERE `next_service_date` = '".$today_date."' AND CONCAT(',',`follow_up_staff`,',') LIKE '%,".$search_user.",%' AND `deleted` = 0";
        $equipment_service_reminders_result = mysqli_fetch_all(mysqli_query($dbc, $equipment_service_reminders_query),MYSQLI_ASSOC);
        foreach ($equipment_service_reminders_result as $reminder) {
            mysqli_query($dbc, "INSERT INTO `daysheet_reminders` (`reminderid`, `contactid`, `type`, `date`, `done`) SELECT '".$reminder['equipmentid']."', '".$search_user."', 'equipment_service', '".$today_date."', '0' FROM (SELECT COUNT(*) rows FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['equipmentid']."' AND `type` = 'equipment_service' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0) num WHERE num.rows = 0");
            $reminderid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daysheetreminderid` FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['equipmentid']."' AND `type` = 'equipment_service' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0"))['daysheetreminderid'];
            $reminderids[] = $reminderid;
        }
        $inc_rep_flags_query = "SELECT * FROM `incident_report` WHERE IFNULL(`flag_colour`,'') != '' AND `flag_colour` != 'FFFFFF' AND '".$today_date."' BETWEEN `flag_start` AND `flag_end` AND CONCAT(',',`flag_user`,',') LIKE '%,".$search_user.",%' AND `deleted` = 0";
        $inc_rep_flags_result = mysqli_fetch_all(mysqli_query($dbc, $inc_rep_flags_query),MYSQLI_ASSOC);
        foreach ($inc_rep_flags_result as $reminder) {
            mysqli_query($dbc, "INSERT INTO `daysheet_reminders` (`reminderid`, `contactid`, `type`, `date`, `done`) SELECT '".$reminder['incidentreportid']."', '".$search_user."', 'incident_report_flag', '".$today_date."', '0' FROM (SELECT COUNT(*) rows FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['incidentreportid']."' AND `type` = 'incident_report_flag' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0) num WHERE num.rows = 0");
            $reminderid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daysheetreminderid` FROM `daysheet_reminders` WHERE `reminderid` = '".$reminder['incidentreportid']."' AND `type` = 'incident_report_flag' AND `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0"))['daysheetreminderid'];
            $reminderids[] = $reminderid;
        }

        //If reminders not found, mark it as deleted
        $reminderids = "'".implode("','",$reminderids)."'";
        $date_of_archival = date('Y-m-d');
        mysqli_query($dbc, "UPDATE `daysheet_reminders` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `daysheetreminderid` NOT IN (".$reminderids.") AND `date` = '".$today_date."' AND `date` >= '".date('Y-m-d')."' AND `contactid` = '".$search_user."' AND `done` = 0 AND `deleted` = 0");
    }

    $today_date = date('Y-m-d');

    //Check Past Due
    $past_due_list = mysqli_query($dbc, "SELECT * FROM `daysheet_reminders` WHERE `date` < '".date('Y-m-d')."' AND `contactid` = '".$search_user."' AND `deleted` = 0 AND `done` = 0 ORDER BY `date` DESC");
    foreach ($past_due_list as $daysheet_reminder) {
        if ($daysheet_reminder['type'] == 'sales') {
            $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales` WHERE `salesid` = '".$daysheet_reminder['reminderid']."'"));
            if(strtotime($reminder['new_reminder']) > strtotime($today_date)) {
                mysqli_query($dbc, "UPDATE `daysheet_reminders` SET `done` = 1 WHERE `daysheetreminderid` = '".$daysheet_reminder['daysheetreminderid']."'");
            }
        } else if ($daysheet_reminder['type'] == 'sales_order') {
            $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order` WHERE `posid` = '".$daysheet_reminder['reminderid']."'"));
            if(strtotime($reminder['next_action_date']) > strtotime($today_date)) {
                mysqli_query($dbc, "UPDATE `daysheet_reminders` SET `done` = 1 WHERE `daysheetreminderid` = '".$daysheet_reminder['daysheetreminderid']."'");
            }
        } else if ($daysheet_reminder['type'] == 'sales_order_temp') {
            $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `sotid` = '".$daysheet_reminder['reminderid']."'"));
            if(strtotime($reminder['next_action_date']) > strtotime($today_date)) {
                mysqli_query($dbc, "UPDATE `daysheet_reminders` SET `done` = 1 WHERE `daysheetreminderid` = '".$daysheet_reminder['daysheetreminderid']."'");
            }
        } else if ($daysheet_reminder['type'] == 'estimate') {
            $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `ea`.*, `e`.`estimate_name` FROM `estimate_actions` AS `ea` JOIN `estimate` AS `e` ON (`ea`.`estimateid`=`e`.`estimateid`) WHERE `ea`.`id` = '".$daysheet_reminder['reminderid']."'"));
            if(strtotime($reminder['due_date']) > strtotime($today_date)) {
                mysqli_query($dbc, "UPDATE `daysheet_reminders` SET `done` = 1 WHERE `daysheetreminderid` = '".$daysheet_reminder['daysheetreminderid']."'");
            }
        } else if ($daysheet_reminder['type'] == 'project') {
            $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `pa`.*, `p`.`project_name` FROM `project_actions` AS `pa` JOIN `project` AS `p` ON (`pa`.`projectid`=`p`.`projectid`) WHERE `pa`.`id` = '".$daysheet_reminder['reminderid']."'"));
            if(strtotime($reminder['due_date']) > strtotime($today_date)) {
                mysqli_query($dbc, "UPDATE `daysheet_reminders` SET `done` = 1 WHERE `daysheetreminderid` = '".$daysheet_reminder['daysheetreminderid']."'");
            }
        } else if ($daysheet_reminder['type'] == 'certificate') {
            $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `certificate` WHERE `certificateid` = '".$daysheet_reminder['reminderid']."'"));
            if(strtotime($reminder['reminder_date']) > strtotime($today_date)) {
                mysqli_query($dbc, "UPDATE `daysheet_reminders` SET `done` = 1 WHERE `daysheetreminderid` = '".$daysheet_reminder['daysheetreminderid']."'");
            }
        } else if ($daysheet_reminder['type'] == 'project_followup') {
            $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '".$daysheet_reminder['reminderid']."'"));
            if(strtotime($reminder['followup']) > strtotime($today_date)) {
                mysqli_query($dbc, "UPDATE `daysheet_reminders` SET `done` = 1 WHERE `daysheetreminderid` = '".$daysheet_reminder['daysheetreminderid']."'");
            }
        } else if ($daysheet_reminder['type'] == 'incident_report') {
            $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `incident_report` WHERE `incidentreportid` = '".$daysheet_reminder['reminderid']."'"));
            if(strtotime($reminder['ir14']) > strtotime($today_date)) {
                mysqli_query($dbc, "UPDATE `daysheet_reminders` SET `done` = 1 WHERE `daysheetreminderid` = '".$daysheet_reminder['daysheetreminderid']."'");
            }
        } else if ($daysheet_reminder['type'] == 'equipment_followup') {
            $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '".$daysheet_reminder['reminderid']."'"));
            if(strtotime($reminder['follow_up_date']) > strtotime($today_date)) {
                mysqli_query($dbc, "UPDATE `daysheet_reminders` SET `done` = 1 WHERE `daysheetreminderid` = '".$daysheet_reminder['daysheetreminderid']."'");
            }
        } else if ($daysheet_reminder['type'] == 'equipment_service') {
            $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '".$daysheet_reminder['reminderid']."'"));
            if(strtotime($reminder['next_service_date']) > strtotime($today_date)) {
                mysqli_query($dbc, "UPDATE `daysheet_reminders` SET `done` = 1 WHERE `daysheetreminderid` = '".$daysheet_reminder['daysheetreminderid']."'");
            }
        } else if ($daysheet_reminder['type'] == 'incident_report_flag') {
            $reminder = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `incident_report` WHERE `incidentreportid` = '".$daysheet_reminder['reminderid']."'"));
            if(time() < strtotime($reminder['flag_start']) || time() > strtotime(str_replace('9999',date('Y'),$reminder['flag_end']).' + 1 day') || strpos(','.$reminder['flag_user'].',',','.$search_user.',') === FALSE) {
                mysqli_query($dbc, "UPDATE `daysheet_reminders` SET `done` = 1 WHERE `reminderid` = '".$reminder['incidentreportid']."' AND `contactid` = '".$search_user."'");
            }
        }
    }

    //Past Due
    $past_due_list = "SELECT COUNT(`daysheetreminderid`) as num_rows FROM `daysheet_reminders` WHERE `date` < '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0 AND `done` = 0 ORDER BY `date` DESC";
    $num_rows_reminders = mysqli_fetch_assoc(mysqli_query($dbc, $past_due_list))['num_rows'];

    $past_due_tickets = mysqli_query($dbc, "SELECT `tickets`.* FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive','Done','On Hold','Waiting On Customer','Stopped Due To Customer','To Be Scheduled') AND (((`internal_qa_date` < '".date('Y-m-d')."' OR `internal_qa_date` = '0000-00-00') AND `internal_qa_contactid` LIKE '%,".$search_user.",%' AND `tickets`.`status` = 'Internal QA') OR ((`deliverable_date` < '".date('Y-m-d')."' OR `deliverable_date` = '0000-00-00') AND `deliverable_contactid` LIKE '%,".$search_user.",%' AND `tickets`.`status` = 'Customer QA') OR ((IF(`ticket_schedule`.`id` IS NULL, `tickets`.`to_do_date`, `ticket_schedule`.`to_do_date`) < '".date('Y-m-d')."' OR IF(`ticket_schedule`.`id` IS NULL, `tickets`.`to_do_date`, `ticket_schedule`.`to_do_date`) = '0000-00-00') AND (IF(`ticket_schedule`.`id` IS NULL, `tickets`.`to_do_end_date`, `ticket_schedule`.`to_do_end_date`) < '".date('Y-m-d')."') AND IF(`ticket_schedule`.`id` IS NULL,`tickets`.`contactid`,`ticket_schedule`.`contactid`) LIKE '%,".$search_user.",%' AND `tickets`.`status` NOT IN ('Internal QA','Customer QA')))");
    // $past_due_tickets = mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `deleted` = 0 AND `status` NOT IN ('Archive','Done','On Hold','Waiting On Customer','Stopped Due To Customer','To Be Scheduled') AND (((`internal_qa_date` < '".date('Y-m-d')."' OR `internal_qa_date` = '0000-00-00') AND `internal_qa_contactid` LIKE '%,".$search_user.",%' AND `status` = 'Internal QA') OR ((`deliverable_date` < '".date('Y-m-d')."' OR `deliverable_date` = '0000-00-00') AND `deliverable_contactid` LIKE '%,".$search_user.",%' AND `status` = 'Customer QA') OR ((`to_do_date` < '".date('Y-m-d')."' OR `to_do_date` = '0000-00-00') AND `to_do_end_date` < '".date('Y-m-d')."' AND `contactid` LIKE '%,".$search_user.",%' AND `status` NOT IN ('Internal QA','Customer QA')))");
    $num_rows_tickets = mysqli_num_rows($past_due_tickets);

    $past_due_tasks = mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `task_tododate` < '".date('Y-m-d')."' AND `contactid` = '".$search_user."' AND `deleted` = 0 AND `task_milestone_timeline` != 'Done' AND `task_milestone_timeline` != 'Archived' ORDER BY `task_tododate` DESC");
    $num_rows_tasks = mysqli_num_rows($past_due_tasks);

    $num_rows_past = $num_rows_reminders + $num_rows_tickets + $num_rows_tasks;


    //Reminders
    $reminders_list = "SELECT COUNT(`daysheetreminderid`) as num_rows FROM `daysheet_reminders` WHERE `date` = '".$today_date."' AND `contactid` = '".$search_user."' AND `deleted` = 0 AND `done` = 0 ORDER BY `date` DESC";
    $num_rows = mysqli_fetch_assoc(mysqli_query($dbc, $reminders_list))['num_rows'];

    //Tickets
    $equipment = [];
    $equipment_ids = $dbc->query("SELECT `equipmentid` FROM `equipment_assignment_staff` LEFT JOIN `equipment_assignment` ON `equipment_assignment_staff`.`equipment_assignmentid`=`equipment_assignment`.`equipment_assignmentid` WHERE `equipment_assignment_staff`.`deleted`=0 AND `equipment_assignment`.`deleted`=0 AND `equipment_assignment_staff`.`contactid`='$search_user'");
    while($equipment[] = $equipment_ids->fetch_assoc()['equipmentid']) { }
    $equipment = implode(',',array_filter($equipment));
    if($equipment == '') {
        $equipment = 0;
    }
    $tickets_query = "SELECT COUNT(`tickets`.`ticketid`) as num_rows FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE (internal_qa_date = '".$today_date."' OR `deliverable_date` = '".$today_date."' OR `tickets`.`to_do_date` = '".$today_date."' OR '".$today_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR `ticket_schedule`.`to_do_date`='".$today_date."' OR '".$today_date."' BETWEEN `ticket_schedule`.`to_do_date` AND `ticket_schedule`.`to_do_end_date`) AND (CONCAT(',',IFNULL(`tickets`.`contactid`,''),',',IFNULL(`ticket_schedule`.`contactid`,''),',',IFNULL(`internal_qa_contactid`,''),',',IFNULL(`deliverable_contactid`,''),',') LIKE '%,".$search_user.",%' OR (`tickets`.`equipmentid` IN ($equipment) AND `tickets`.`equipmentid` > 0) OR (`ticket_schedule`.`equipmentid` IN ($equipment) AND `ticket_schedule`.`equipmentid` > 0)) AND `tickets`.`deleted` = 0 ORDER BY IFNULL(NULLIF(`ticket_schedule`.`to_do_start_time`,''),IFNULL(NULLIF(`tickets`.`start_time`,''),`tickets`.`to_do_start_time`)) ASC";
    // $tickets_query = "SELECT COUNT(`ticketid`) as num_rows FROM `tickets` WHERE (`internal_qa_date` = '".$today_date."' AND `internal_qa_contactid` = '".$search_user."' AND `status` = 'Internal QA') OR (`deliverable_date` = '".$today_date."' AND `deliverable_contactid` = '".$search_user."' AND `status` = 'Customer QA') OR ((`to_do_date` = '".$today_date."' OR '".$today_date."' BETWEEN `to_do_date` AND `to_do_end_date`) AND `contactid` LIKE '%,".$search_user.",%') AND `deleted` = 0 AND `status` NOT IN ('Archive','Done')";
    $num_rows = $num_rows + mysqli_fetch_assoc(mysqli_query($dbc, $tickets_query))['num_rows'];

    //Tasks
    $tasks_query = "SELECT COUNT(`tasklistid`) as num_rows FROM `tasklist` WHERE `contactid` = '".$search_user."' AND `task_tododate` = '".$today_date."' AND `deleted` = 0";
    $num_rows = $num_rows + mysqli_fetch_assoc(mysqli_query($dbc, $tasks_query))['num_rows'];

    //Checklists
    $checklists_query = "SELECT COUNT(`checklistactionid`) as num_rows FROM `checklist_actions` WHERE `contactid` = '".$search_user."' AND `action_date` = '".$today_date."' AND `deleted` = 0 AND `done` = 0";
    $num_rows = $num_rows + mysqli_fetch_assoc(mysqli_query($dbc, $checklists_query))['num_rows'];


    //Notifications
    //Reminders
    $reminders_list = mysqli_query($dbc, "SELECT `dr`.* FROM `daysheet_reminders` AS `dr` LEFT JOIN `journal_notifications` AS `noti` ON `dr`.`daysheetreminderid` = `noti`.`src_id` AND `noti`.`src_table` = 'daysheet_reminders' AND `noti`.`contactid` = '$search_user' AND `noti`.`deleted` = 0 WHERE `dr`.`date` >= '$today_date' AND `dr`.`contactid` = '$search_user' AND `dr`.`deleted` = 0 AND `dr`.`done` = 0 AND IFNULL(`noti`.`seen`,0) != 1");
    while($row = mysqli_fetch_assoc($reminders_list)) {
        $noti_exists = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `journal_notifications` WHERE `src_table` = 'daysheet_reminders' AND `src_id` = '".$row['daysheetreminderid']."' AND `contactid` = '$search_user'"));
        if(empty($noti_exists)) {
            mysqli_query($dbc, "INSERT INTO `journal_notifications` (`contactid`, `src_table`, `src_id`) VALUES ('$search_user','daysheet_reminders','".$row['daysheetreminderid']."')");
        }
    }
    //Tickets
    $tickets_query = mysqli_query($dbc, "SELECT IFNULL(`ticket_schedule`.`id`, `tickets`.`ticketid`) as t_id, IF(`ticket_schedule`.`id` IS NULL,'tickets','ticket_schedule') `ticket_table` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE (internal_qa_date >= '$today_date' OR `deliverable_date` >= '$today_date' OR `tickets`.`to_do_date` >= '$today_date' OR `tickets`.`to_do_end_date` >= '$today_date' OR `ticket_schedule`.`to_do_date` >= '$today_date' OR `ticket_schedule`.`to_do_end_date` >= '$today_date') AND (CONCAT(',',IFNULL(`tickets`.`contactid`,''),',',IFNULL(`ticket_schedule`.`contactid`,''),',',IFNULL(`internal_qa_contactid`,''),',',IFNULL(`deliverable_contactid`,''),',') LIKE '%,$search_user,%' OR (`tickets`.`equipmentid` IN ($equipment) AND `tickets`.`equipmentid` > 0) OR (`ticket_schedule`.`equipmentid` IN ($equipment) AND `ticket_schedule`.`equipmentid` > 0)) AND `tickets`.`deleted` = 0 ORDER BY IFNULL(NULLIF(`ticket_schedule`.`to_do_start_time`,''),IFNULL(NULLIF(`tickets`.`start_time`,''),`tickets`.`to_do_start_time`)) ASC");
    while($row = mysqli_fetch_array($tickets_query)) {
        $noti_exists = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `journal_notifications` WHERE `src_table` = '".$row['ticket_table']."' AND `src_id` = '".$row['t_id']."' AND `contactid` = '$search_user'"));
        if(empty($noti_exists)) {
            mysqli_query($dbc, "INSERT INTO `journal_notifications` (`contactid`, `src_table`, `src_id`) VALUES ('$search_user','".$row['ticket_table']."','".$row['t_id']."')");
        }
    }
    //Tasks
    $tasks_query = mysqli_query($dbc, "SELECT `tl`.* FROM `tasklist` AS `tl` LEFT JOIN `journal_notifications` AS `noti` ON `tl`.`tasklistid` = `noti`.`src_id` AND `noti`.`src_table` = 'tasklist' AND `noti`.`contactid` = '$search_user' AND `noti`.`deleted` = 0 WHERE `tl`.`contactid` = '".$search_user."' AND `tl`.`task_tododate` >= '".$today_date."' AND `tl`.`deleted` = 0");
    while($row = mysqli_fetch_array($tasks_query)) {
        $noti_exists = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `journal_notifications` WHERE `src_table` = 'tasklist' AND `src_id` = '".$row['tasklistid']."' AND `contactid` = '$search_user'"));
        if(empty($noti_exists)) {
            mysqli_query($dbc, "INSERT INTO `journal_notifications` (`contactid`, `src_table`, `src_id`) VALUES ('$search_user','tasklist','".$row['tasklistid']."')");
        }
    }
    //Checklists
    $checklists_query = mysqli_query($dbc, "SELECT `chk`.* FROM `checklist_actions` AS `chk` LEFT JOIN `journal_notifications` AS `noti` ON `chk`.`checklistactionid` = `noti`.`src_id` AND `noti`.`src_table` = 'checklist_actions' AND `noti`.`contactid` = '$search_user' AND `noti`.`deleted` = 0 WHERE `chk`.`contactid` = '".$search_user."' AND `chk`.`action_date` >= '".$today_date."' AND `chk`.`deleted` = 0 AND `chk`.`done` = 0");
    while($row = mysqli_fetch_array($checklists_query)) {
        $noti_exists = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `journal_notifications` WHERE `src_table` = 'checklist_actions' AND `src_id` = '".$row['checklistactionid']."' AND `contactid` = '$search_user'"));
        if(empty($noti_exists)) {
            mysqli_query($dbc, "INSERT INTO `journal_notifications` (`contactid`, `src_table`, `src_id`) VALUES ('$search_user','checklist_actions','".$row['checklistactionid']."')");
        }
    }
    //Comments
    $notes = mysqli_query($dbc, "SELECT * FROM (SELECT `budgetcommid` `comment_id`, 'budget_comment' `comment_table` FROM `budget_comment` WHERE '$search_user' IN (`created_by`,`email_to`) AND `created_date`>='$today_date' UNION
        SELECT `projectcommid` `comment_id`, 'project_comment' `comment_table` FROM `project_comment` WHERE '$search_user' IN (`created_by`,`email_comment`) AND `created_date`>='$today_date' AND `type`='project_note' UNION
        SELECT `taskcommid` `comment_id`, 'task_comments' `comment_table` FROM `task_comments` WHERE '$search_user' IN (`created_by`) AND `created_date`>='$today_date' AND `deleted`=0 UNION
        SELECT `ticketcommid` `comment_id`, 'ticket_comment' `comment_table` FROM `ticket_comment` WHERE CONCAT(',',IFNULL(`created_by`,''),',',IFNULL(`email_comment`,''),',') LIKE '%,$search_user,%' AND `created_date`>='$today_date' AND `deleted`=0 UNION
        SELECT `id` `comment_id`, 'estimate_notes' `comment_table` FROM `estimate_notes` WHERE '$search_user' IN (`created_by`,`assigned`) AND `note_date`>='$today_date' AND `deleted`=0 UNION
        SELECT `note_id` `comment_id`, 'client_daily_log_notes' `comment_table` FROM `client_daily_log_notes` WHERE '$search_user' IN (`created_by`) AND `note_date`>='$today_date%' AND `deleted`=0 UNION
        SELECT `daysheetnotepadid` `comment_id`, 'daysheet_notepad' `comment_table` FROM `daysheet_notepad` WHERE `date`>='$today_date' AND `contactid`='$search_user') notes");
    while($row = mysqli_fetch_array($notes)) {
        $noti_exists = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `journal_notifications` WHERE `src_table` = '".$row['comment_table']."' AND `src_id` = '".$row['comment_id']."' AND `contactid` = '$search_user'"));
        if(empty($noti_exists)) {
            mysqli_query($dbc, "INSERT INTO `journal_notifications` (`contactid`, `src_table`, `src_id`) VALUES ('$search_user','".$row['comment_table']."','".$row['comment_id']."')");
        }
    }
    $noti_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`id`) as `num_rows` FROM `journal_notifications` WHERE `seen` = 0 AND `deleted` = 0 AND `contactid` = '$search_user'"))['num_rows'];
}