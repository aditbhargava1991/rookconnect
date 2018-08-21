<?php
/* Update Databases */

    //Baldwin's Database Changes
    echo "Baldwin's DB Changes:<br />\n";
    
    //2018-06-15 - TIcket #7838 - Calendar Lock Icon
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_schedule` ADD `calendar_history` text NOT NULL")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `calendar_history` text NOT NULL")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-06-15 - TIcket #7838 - Calendar Lock Icon

    //2018-06-18 - Ticket #7888 - Cleans
    $updated_already = get_config($dbc, 'updated_ticket7888_materials');
    if(empty($updated_already)) {
        $ticket_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `general_configuration` WHERE `name` LIKE 'ticket_fields_%'"),MYSQLI_ASSOC);
        foreach ($ticket_types as $ticket_type) {
            $value_config = ','.$ticket_type['value'].',';
            $value_config = str_replace(',Material Category,',',Material Category,Material Subcategory,',$value_config);
            $value_config = trim($value_config, ',');
            set_config($dbc, $ticket_type['name'], $value_config);
        }
        $value_config = ','.get_field_config($dbc, 'tickets').',';
        $value_config = str_replace(',Material Category,',',Material Category,Material Subcategory,',$value_config);
        $value_config = trim($value_config, ',');
        mysqli_query($dbc, "UPDATE `field_config` SET `tickets` = '$value_config'");

        set_config($dbc, 'updated_ticket7888_materials', '1');
    }
    if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `service_templateid_loaded` int(1) NOT NULL DEFAULT 0")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-06-18 - Ticket #7888 - Cleans

    //2018-06-19 - Ticket #7952 - Staff Subtabs & Fields
    $updated_already = get_config($dbc, 'updated_ticket7952_staff');
    if(empty($updated_already)) {
        include('Staff/field_list.php');
        $tabs = ['Profile','Staff'];
        foreach($tabs as $tab) {
            $new_fields = [];
            $staff_fields = mysqli_query($dbc, "SELECT * FROM `field_config_contacts` WHERE `tab` = '$tab' AND IFNULL(`accordion`,'') != '' AND IFNULL(`subtab`,'') != ''");
            while($row = mysqli_fetch_assoc($staff_fields)) {
                $value_config = array_filter(explode(',',$row['contacts']));
                foreach($value_config as $value) {
                    $field_found = false;
                    foreach($field_list as $label => $list) {
                        foreach($list as $subtab => $fields) {
                            foreach($fields as $field) {
                                if($value == $field) {
                                    $field_found = true;
                                    if($subtab == $row['subtab']) {
                                        if(!in_array($field, $new_fields[$subtab][$row['accordiion']])) {
                                            $new_fields[$subtab][$row['accordion']][] = $field;
                                        }
                                    } else {
                                        if(!in_array($field, $new_fields[$subtab][$label])) {
                                            $new_fields[$subtab][$label][] = $field;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if(!$field_found) {
                        if(!in_array($value, $new_fields['hidden']['hidden'])) {
                            $new_fields['hidden']['hidden'][] = $value;
                        }
                    }
                }
            }
            mysqli_query($dbc, "DELETE FROM `field_config_contacts` WHERE `tab` = '$tab' AND IFNULL(`accordion`,'') != '' AND IFNULL(`subtab`,'') != ''");
            foreach($new_fields as $subtab => $accordion) {
                foreach($accordion as $label => $fields) {
                    mysqli_query($dbc, "INSERT INTO `field_config_contacts` (`tab`, `subtab`, `accordion`, `contacts`) VALUES ('$tab', '$subtab', '$label', ',".implode(',', $fields).",')");
                }
            }
        }
        $staff_tabs = explode(',',get_config($dbc, 'staff_field_subtabs'));
        $staff_subtabs = array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT `subtab` FROM `field_config_contacts` WHERE `tab` = 'Staff' AND IFNULL(`subtab`,'') != ''"),MYSQLI_ASSOC),'subtab');
        if(in_array('staff_bio',$staff_subtabs)) {
            $staff_tabs[] = 'Staff Bio';
        }
        if(in_array('health_concerns',$staff_tabs)) {
            $staff_tabs[] = 'Health Concerns';
        }
        if(in_array('allergies',$staff_tabs)) {
            $staff_tabs[] = 'Allergies';
        }
        if(in_array('company_benefits',$staff_tabs)) {
            $staff_tabs[] = 'Company Benefits';
        }
        $staff_tabs = implode(',',array_filter($staff_tabs));
        set_config($dbc, 'staff_field_subtabs', $staff_tabs);
        set_config($dbc, 'updated_ticket7952_staff', 1);
    }
    //2018-06-19 - Ticket #7952 - Staff Subtabs & Fields

    //2018-06-20 - TIcket #7967 - Multiple Sites
    if(!mysqli_query($dbc, "ALTER TABLE `contacts` ADD `main_siteid` int(1) NOT NULL DEFAULT 0")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-06-20 - TIcket #7967 - Multiple Sites

    //2018-06-21 - Ticket #8000 - HR Default Email
    $updated_already = get_config($dbc, 'updated_ticket8000_emails');
    if(empty($updated_already)) {
        $manual_emails = mysqli_query($dbc, "SELECT * FROM `general_configuration` WHERE `name` LIKE 'manual_%_email'");
        while($manual_email = mysqli_fetch_assoc($manual_emails)) {
            if($manual_email['value'] == 'dayanasanjay@yahoo.com') {
                set_config($dbc, $manual_email['name'], '');
            }
        }
        set_config($dbc, 'updated_ticket8000_emails', 1);
    }

    //2018-06-21 - Ticket #8000 - HR Default Email

    //2018-06-21 - Ticket #7736 - Shift Reports & My Shifts
    if(!mysqli_query($dbc, "ALTER TABLE `user_forms` ADD `attached_contacts` text NOT NULL")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `user_form_pdf` ADD `attached_contactid` int(11) NOT NULL DEFAULT 0")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-06-21 - Ticket #7736 - Shift Reports & My Shifts

    //2018-06-26 - Ticket #7370 - Equipment Styling
    if(!mysqli_query($dbc, "ALTER TABLE `equipment` ADD `equipment_image` VARCHAR(500)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-06-26 - Ticket #7370 - Equipment Styling

    //2018-06-26 - Ticket #7814 - Holidays Update Notifications
    if(!mysqli_query($dbc, "CREATE TABLE `holiday_update_reminders` (
        `reminderid` int(11) NOT NULL,
        `date` date NOT NULL,
        `sent` int(1) NOT NULL DEFAULT 1,
        `log` text NOT NULL)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `holiday_update_reminders`
        ADD PRIMARY KEY (`reminderid`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `holiday_update_reminders`
        MODIFY `reminderid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-06-26 - Ticket #7814 - Holidays Update Notifications

    //2018-06-28 - Ticket #7899 - Sessions Additions
    if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `service_total_time` varchar(500)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-06-28 - Ticket #7899 - Sessions Additions

    //2018-06-29 - Ticket #7898 - Clients Tile
    if(!mysqli_query($dbc, "ALTER TABLE `contacts_upload` ADD `comments_attachment` text")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `contacts_upload` ADD `description_attachment` text")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `contacts_upload` ADD `general_comments_attachment` text")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `contacts_upload` ADD `notes_attachment` text")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-06-29 - Ticket #7898 - Clients Tile

    //2018-07-03 - Ticket #7549 - Mileage Sheet
    if(!mysqli_query($dbc, "ALTER TABLE `rate_card` ADD `mileage` text AFTER `labour`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-03 - Ticket #7549 - Mileage Sheet

    //2018-07-04 - Ticket #7868 - Incident Reports Form Builder
    if(!mysqli_query($dbc, "ALTER TABLE `field_config_incident_report` ADD `user_form_id` int(11) NOT NULL DEFAULT 0")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `incident_report` ADD `pdf_id` int(11) NOT NULL DEFAULT 0")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-04 - Ticket #7868 - Incident Reports Form Builder

    //2018-07-04 - Ticket #8009 - Sessions Additions
    if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `guardianid` int(11) NOT NULL DEFAULT 0 AFTER `clientid`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-04 - Ticket #8009 - Sessions Additions

    //2018-07-11 - Ticket #8060 - Estimate Templates VPL
    if(!mysqli_query($dbc, "ALTER TABLE `estimate_template_lines` ADD `product_pricing` varchar(500) AFTER `qty`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-11 - Ticket #8060 - Estimate Templates VPL

    //2018-07-11 - Ticket #8150 - Contacts Additions
    if(!mysqli_query($dbc, "ALTER TABLE `rate_card` ADD `total_estimated_hours` decimal(10,2) AFTER `total_price`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-11 - Ticket #8150 - Contacts Additions

    //2018-07-11 - Ticket #7997 - Certificates
    $updated_already = get_config($dbc, 'updated_ticket7997_certificates');
    if(empty($updated_already)) {
        $result = mysqli_query($dbc, "SELECT DISTINCT(`certificate_type`) FROM `certificate` WHERE `deleted` = 0 ORDER BY `certificate_type`");
        $certificate_types = [];
        while($row = mysqli_fetch_assoc($result)) {
            $certificate_types[] = $row['certificate_type'];
        }
        set_config($dbc, 'certificate_types', implode('#*#', $certificate_types));

        $result = mysqli_query($dbc, "SELECT DISTINCT(`category`) FROM `certificate` WHERE `deleted` = 0 ORDER BY `category`");
        $certificate_categories = [];
        while($row = mysqli_fetch_assoc($result)) {
            $certificate_categories[] = $row['category'];
        }
        set_config($dbc, 'certificate_categories', implode('#*#', $certificate_categories));

        set_config($dbc, 'updated_ticket7997_certificates', 1);
    }
    if(!mysqli_query($dbc, "ALTER TABLE `certificate` CHANGE `certificate_reminder` `certificate_reminder` varchar(1000)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-11 - Ticket #7997 - Certificates

    //2018-07-13 - Task #6494 - AAFS Positions
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_attached` CHANGE `position` `position` VARCHAR(500)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-13 - Task #6494 - AAFS Positions

    //2018-07-17 - Ticket #8311 - Cleans Calendar
    if(!mysqli_query($dbc, "ALTER TABLE `teams` ADD `hide_days` text NOT NULL")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-17 - Ticket #8311 - Cleans Calendar

    //2018-07-17 - Ticket #8311 - Cleans Calendar
    if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `is_recurrence` int(1) NOT NULL DEFAULT 0 AFTER `main_ticketid`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_attached` ADD `main_id` int(11) NOT NULL DEFAULT 0 AFTER `id`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_attached` ADD `is_recurrence` int(1) NOT NULL DEFAULT 0 AFTER `main_id`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_schedule` ADD `main_id` int(11) NOT NULL DEFAULT 0 AFTER `id`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_schedule` ADD `is_recurrence` int(1) NOT NULL DEFAULT 0 AFTER `main_id`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_comment` ADD `main_id` int(11) NOT NULL DEFAULT 0 AFTER `ticketcommid`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_comment` ADD `is_recurrence` int(1) NOT NULL DEFAULT 0 AFTER `main_id`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-17 - Ticket #8311 - Cleans Calendar

    //2018-07-20 - Ticket #8352 - Sales Auto Archive
    if(!mysqli_query($dbc, "ALTER TABLE `sales` ADD `status_date` date NOT NULL")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TRIGGER `sales_status_date` BEFORE UPDATE ON `sales`
         FOR EACH ROW BEGIN
            IF NEW.`status` != OLD.`status` THEN
                SET NEW.`status_date` = CURDATE();
            END IF;
        END")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-20 - Ticket #8352 - Sales Auto Archive

    //2018-07-25 - Ticket #8413 - Cleans Calendar
    if(!mysqli_query($dbc, "ALTER TABLE `teams` ADD `team_name` varchar(500) AFTER `teamid`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TABLE `ticket_recurrences` (
        `id` int(11) NOT NULL,
        `ticketid` int(11) NOT NULL,
        `start_date` date NOT NULL,
        `end_date` date NOT NULL,
        `repeat_type` varchar(500),
        `repeat_interval` int(11) NOT NULL,
        `repeat_days` varchar(500),
        `last_added_date` date NOT NULL,
        `deleted` int(1) NOT NULL DEFAULT 0)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_recurrences`
        ADD PRIMARY KEY (`id`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_recurrences`
        MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-25 - Ticket #8413 - Cleans Calendar

    //2018-07-30 - Ticket #8467 - Cleans Recurring Monthly
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_recurrences` ADD `repeat_monthly` varchar(500) AFTER `repeat_type`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-30 - Ticket #8467 - Cleans Recurring Monthly

    //2018-07-30 - Ticket #8444 - Teams
    $updated_already = get_config($dbc, 'updated_ticket8444_teams');
    if(empty($updated_already)) {
        $estimate_groups = explode('#*#',mysqli_fetch_array(mysqli_query($dbc, "SELECT `estimate_groups` FROM field_config_estimate"))[0]);
        $ticket_groups = explode('#*#',get_config($dbc,'ticket_groups'));
        $so_groups = explode('*#*',get_config($dbc, 'sales_order_staff_groups'));
        $groups = array_merge($estimate_groups, $ticket_groups, $so_groups);
        foreach($groups as $group) {
            $group = explode(',', $group);
            $group_name = '';
            if(count($group) > 1 && !($group[0] > 0)) {
                $group_name = $group[0];
                unset($group[0]);
            }
            mysqli_query($dbc, "INSERT INTO `teams` (`team_name`, `start_date`, `end_date`) VALUES ('$group_name', '', '')");
            $teamid = mysqli_insert_id($dbc);
            foreach($group as $staff) {
                if($staff > 0) {
                    mysqli_query($dbc, "INSERT INTO `teams_staff` (`teamid`, `contactid`) VALUES ('$teamid', '$staff')");
                }
            }
        }
        set_config($dbc, 'updated_ticket8444_teams', 1);
    }
    //2018-07-30 - Ticket #8444 - Teams

    //2018-07-24 - Ticket #6075 - Performance Improvement Plan
    if(!mysqli_query($dbc, "CREATE TABLE `field_config_performance_reviews` (
        `fieldconfigid` int(11) NOT NULL,
        `user_form_id` int(11) NOT NULL,
        `enabled` int(1) NOT NULL,
        `limit_staff` text)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `field_config_performance_reviews`
        ADD PRIMARY KEY (`fieldconfigid`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `field_config_performance_reviews`
        MODIFY `fieldconfigid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }

    $updated_already = get_config($dbc, 'updated_ticket6075_pr');
    if(empty($updated_already)) {
        $pr_forms = array_filter(explode(',',get_config($dbc, 'performance_review_forms')));
        foreach($pr_forms as $pr_form) {
            if($pr_form > 0) {
                mysqli_query($dbc, "INSERT INTO `field_config_performance_reviews` (`user_form_id`,`enabled`) VALUES ('$pr_form', '1')");
            }
        }
        set_config($dbc, 'updated_ticket6075_pr', 1);
    }
    //2018-07-24 - Ticket #6075 - Performance Improvement Plan

    //2018-07-26 - Ticket #8394 - Contact Forms Editable
    if(!mysqli_query($dbc, "ALTER TABLE `user_forms` ADD `attached_contact_categories` text AFTER `attached_contacts`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-26 - Ticket #8394 - Contact Forms Editable

    //2018-07-27 - Ticket #7552 - Checklists
    if(!mysqli_query($dbc, "ALTER TABLE `checklist` ADD `project_milestone` varchar(500) NOT NULL AFTER `projectid`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `checklist` ADD `project_milestone` varchar(500) NOT NULL AFTER `projectid`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `checklist` ADD `salesid` int(11) NOT NULL AFTER `ticketid`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `checklist` ADD `sales_milestone` varchar(500) NOT NULL AFTER `salesid`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `checklist` ADD `task_path` int(10) NOT NULL AFTER `sales_milestone`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `checklist` ADD `task_board` int(10) NOT NULL AFTER `task_path`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `checklist` ADD `task_milestone_timeline` varchar(500) NOT NULL AFTER `task_board`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-27 - Ticket #7552 - Checklists

    //2018-07-31 - Ticket #7497 - Email Alerts
    if(!mysqli_query($dbc, "CREATE TABLE `field_config_email_alerts` (
        `fieldconfigid` int(11) NOT NULL,
        `software_default` int(1) NOT NULL DEFAULT 0,
        `contactid` int(11) NOT NULL,
        `enabled` int(1) NOT NULL DEFAULT 0,
        `alerts` text,
        `frequency` varchar(500),
        `alert_hour` varchar(500) NOT NULL,
        `alert_days` varchar(500) NOT NULL)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `field_config_email_alerts`
        ADD PRIMARY KEY (`fieldconfigid`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `field_config_email_alerts`
        MODIFY `fieldconfigid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `journal_notifications` ADD `email_sent` int(1) NOT NULL DEFAULT 0")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-07-31 - Ticket #7497 - Email Alerts

    //2018-08-02 - Ticket #8273 - Camping
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_attached` ADD `start_time` varchar(10) NOT NULL AFTER `date_stamp`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_attached` ADD `end_time` varchar(10) NOT NULL AFTER `start_time`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-08-02 - Ticket #8273 - Camping

    //2018-08-08 - Ticket #8582 - Ticket Timer
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_timer` ADD `deleted` int(1) NOT NULL DEFAULT 0")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_timer` ADD `deleted_by` int(11) NOT NULL DEFAULT 0")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_timer` ADD `date_of_archival` date NOT NULL AFTER `deleted`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-08-08 - Ticket #8582 - Ticket Timer

    //2018-08-07 - Ticket #8518 - Equipment Follow Up
    if(!mysqli_query($dbc, "ALTER TABLE `equipment` ADD `follow_up_date` date NOT NULL AFTER `finance`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `equipment` ADD `follow_up_staff` varchar(500) NOT NULL AFTER `follow_up_date`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-08-07 - Ticket #8518 - Equipment Follow Up

    //2018-08-09 - Ticket #8583 - Payroll: By Staff
    if(!mysqli_query($dbc, "ALTER TABLE `field_config` ADD `time_cards_total_hrs_layout` text AFTER `time_cards_dashboard`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    $updated_already = get_config($dbc, 'updated_ticket8583_timesheet');
    if(empty($updated_already)) {
        $value_config = ','.get_field_config($dbc, 'time_cards').',';
        $new_value_config = ',reg_hrs,overtime_hrs,doubletime_hrs,';
        if(strpos($value_config, ',view_ticket,') !== FALSE) {
            $new_value_config .= 'view_ticket,';
        }
        if(strpos($value_config, ',total_tracked_hrs,') !== FALSE) {
            $new_value_config .= 'total_tracked_hrs,';
        }
        if(strpos($value_config, ',staff_combine,') !== FALSE) {
            $new_value_config .= 'staff_combine,';
        }
        set_field_config($dbc, 'time_cards_total_hrs_layout', $new_value_config);
        set_config($dbc, 'updated_ticket8583_timesheet', 1);
    }
    //2018-08-09 - Ticket #8583 - Payroll: By Staff

    //2018-08-20 - Ticket #8609 - Calendar Security
    if(!mysqli_query($dbc, "CREATE TABLE `field_config_calendar_security` (
        `fieldconfigid` int(11) NOT NULL,
        `role` varchar(500) NOT NULL,
        `allowed_roles` text NOT NULL,
        `allowed_ticket_types` text NOT NULL)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `holiday_update_reminders`
        ADD PRIMARY KEY (`reminderid`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `holiday_update_reminders`
        MODIFY `reminderid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-08-20 - Ticket #8609 - Calendar Security

    //2018-08-16 - Ticket #8623 - Shifts
    if(!mysqli_query($dbc, "ALTER TABLE `contacts_shifts` ADD `security_level` varchar(500) AFTER `contactid`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-08-16 - Ticket #8623 - Shifts

    //2018-08-15 - Ticket #8552 - Temporary Profile Link
    if(!mysqli_query($dbc, "ALTER TABLE `contacts` ADD `update_url_key` varchar(500)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `contacts` ADD `update_url_expiry` date NOT NULL")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `contacts` ADD `update_url_role` varchar(500)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-08-15 - Ticket #8552 - Temporary Profile Link

    //2018-08-14 - Ticket #7563 - POS Different Types
    if(!mysqli_query($dbc, "ALTER TABLE `invoice` ADD `type` varchar(500) AFTER `tile_name`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-08-14 - Ticket #7563 - POS Different Types

    //2018-08-14 - Ticket #8490 - Time Sheets
    if(!mysqli_query($dbc, "ALTER TABLE `contacts_shifts` ADD `set_hours` int(1) NOT NULL DEFAULT 0")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `time_cards` ADD `shiftid` int(11) NOT NULL DEFAULT 0 AFTER `salesid`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-08-14 - Ticket #8490 - Time Sheets

    echo "Baldwin's DB Changes Done<br />\n";
?>