
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
    if(!mysqli_query($dbc, "ALTER TABLE `field_config_calendar_security`
        ADD PRIMARY KEY (`fieldconfigid`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `field_config_calendar_security`
        MODIFY `fieldconfigid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
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

    //2018-08-21 - Ticket #8853 - Ticket Calendar Security
    if(!mysqli_query($dbc, "ALTER TABLE `field_config_calendar_security` ADD `calendar_type` varchar(500) NOT NULL AFTER `fieldconfigid`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-08-21 - Ticket #8853 - Ticket Calendar Security

    //2018-08-24 - Ticket #8813 - Notable Happenings
    if(!mysqli_query($dbc, "ALTER TABLE `incident_report` ADD `saved` int(1) NOT NULL DEFAULT 0 AFTER `type`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-08-24 - Ticket #8813 - Notable Happenings

    //2018-08-23 - Ticket #8585 - Stat Pay
    if(!mysqli_query($dbc, "ALTER TABLE `contacts` ADD `stat_pay` varchar(500) NOT NULL")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `time_cards` ADD `holidayid` int(11) NOT NULL AFTER `shiftid`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-08-23 - Ticket #8585 - Stat Pay

    //2018-08-29 - Ticket #8717 - Sick Hours
    if(!mysqli_query($dbc, "ALTER TABLE `field_config_contacts_shifts` ADD `dayoff_types_timesheet` text NOT NULL AFTER `dayoff_types`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TABLE `field_config_time_cards_dayoff` (
        `fieldconfigid` int(11) NOT NULL,
        `hours_type` varchar(500) NOT NULL,
        `enabled` int(1) NOT NULL,
        `dayoff_type` varchar(500) NOT NULL)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `field_config_time_cards_dayoff`
        ADD PRIMARY KEY (`fieldconfigid`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `field_config_time_cards_dayoff`
        MODIFY `fieldconfigid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-08-29 - Ticket #8717 - Sick Hours

    //2018-08-28 - Ticket #7761 - POS
    if(!mysqli_query($dbc, "ALTER TABLE `invoice_lines` ADD `ticketid` int(11) NOT NULL AFTER `invoiceid`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `invoice` ADD `service_ticketid` text NOT NULL AFTER `serviceid`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `invoice` ADD `misc_ticketid` text NOT NULL AFTER `misc_item`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `invoice` CHANGE `serviceid` `serviceid` text")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `invoice` CHANGE `fee` `fee` text")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-08-28 - Ticket #7761 - POS

    //2018-08-30 - Ticket #9034 - Quick Action Projects
    if(!mysqli_query($dbc, "ALTER TABLE `project` ADD `flag_colour` VARCHAR(7)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `project` ADD `flag_start` DATE NOT NULL DEFAULT '0000-00-00' AFTER `flag_colour`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `project` ADD `flag_end` DATE NOT NULL DEFAULT '9999-12-31' AFTER `flag_start`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `project` ADD `flag_label` TEXT AFTER `flag_colour`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }

    if(!mysqli_query($dbc, "ALTER TABLE `checklist` ADD `flag_start` DATE NOT NULL DEFAULT '0000-00-00' AFTER `flag_colour`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `checklist` ADD `flag_end` DATE NOT NULL DEFAULT '9999-12-31' AFTER `flag_start`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `checklist` ADD `flag_label` TEXT AFTER `flag_colour`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-08-30 - Ticket #9034 - Quick Action Projects
    //2018-08-31 - Ticket #8720 - Contacts Sync
    if(!mysqli_query($dbc, "CREATE TABLE `contacts_sync` (
        `contactsyncid` int(11) NOT NULL,
        `contactid` int(11) NOT NULL,
        `synced_contactid` int(11) NOT NULL,
        `deleted` int(1) NOT NULL DEFAULT 0)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `contacts_sync`
        ADD PRIMARY KEY (`contactsyncid`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `contacts_sync`
        MODIFY `contactsyncid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    $updated_already = get_config($dbc, 'updated_ticket8720_contactsync');
    if(empty($updated_already)) {
        $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `deleted` = 0 AND `status` > 0 AND `category` != 'Staff'"));
        foreach($contact_list as $contact) {
            if($contact['businessid'] > 0) {
                mysqli_query($dbc, "INSERT INTO `contacts_sync` (`contactid`, `synced_contactid`) SELECT '".$contact['contactid']."', '".$contact['businessid']."' FROM (SELECT COUNT(*) rows FROM `contacts_sync` WHERE `deleted` = 0 AND ((`contactid` = '".$contact['contactid']."' AND `synced_contactid` = '".$contact['businessid']."') OR (`contactid` = '".$contact['businessid']."' AND `synced_contactid` = '".$contact['contactid']."'))) num WHERE num.rows=0");
            }
        }
        set_config($dbc, 'updated_ticket8720_contactsync', 1);
    }
    //2018-08-31 - Ticket #8720 - Contacts Sync

    //2018-08-30 - Ticket #9034 - Quick Action Projects
    if(!mysqli_query($dbc, "ALTER TABLE `project` ADD `flag_colour` VARCHAR(7)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `project` ADD `flag_start` DATE NOT NULL DEFAULT '0000-00-00' AFTER `flag_colour`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `project` ADD `flag_end` DATE NOT NULL DEFAULT '9999-12-31' AFTER `flag_start`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `project` ADD `flag_label` TEXT AFTER `flag_colour`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }

    if(!mysqli_query($dbc, "ALTER TABLE `checklist` ADD `flag_start` DATE NOT NULL DEFAULT '0000-00-00' AFTER `flag_colour`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `checklist` ADD `flag_end` DATE NOT NULL DEFAULT '9999-12-31' AFTER `flag_start`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `checklist` ADD `flag_label` TEXT AFTER `flag_colour`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-08-30 - Ticket #9034 - Quick Action Projects

    //2018-09-06 - Ticket #8931 - Form Builder
    if(!mysqli_query($dbc, "ALTER TABLE `user_forms` ADD `attached_contact_default_field` int(11) NOT NULL DEFAULT 0")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-09-06 - Ticket #8931 - Form Builder
    //2018-09-05 - Ticket #8740 - Ticket Service Direct/Indirect Time
    if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `service_direct_time` TEXT AFTER `service_total_time`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `service_indirect_time` TEXT AFTER `service_direct_time`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    $updated_already = get_config($dbc, 'updated_ticket8740_services');
    if(empty($updated_already)) {
        $tickets = mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `deleted` = 0 AND IFNULL(`service_total_time`,'') != ''");
        while($ticket = mysqli_fetch_assoc($tickets)) {
            if(!empty($ticket['service_total_time'])) {
                $ticket['service_total_time'] = explode(',', $ticket['service_total_time']);
                foreach($ticket['service_total_time'] as $i => $total_time) {
                    switch($total_time) {
                        case '15 Min':
                            $ticket['service_total_time'][$i] = 0.25;
                            break;
                        case '30 Min':
                            $ticket['service_total_time'][$i] = 0.5;
                            break;
                        case '45 Min':
                            $ticket['service_total_time'][$i] = 0.75;
                            break;
                        case '60 Min':
                            $ticket['service_total_time'][$i] = 1;
                            break;
                        case '1 Hr 15 Min':
                            $ticket['service_total_time'][$i] = 1.25;
                            break;
                        case '1 Hr 30 Min':
                            $ticket['service_total_time'][$i] = 1.5;
                            break;
                        case '1 Hr 45 Min':
                            $ticket['service_total_time'][$i] = 1.75;
                            break;
                        case '2 Hr':
                            $ticket['service_total_time'][$i] = 2;
                            break;
                        case '2 Hr 15 Min':
                            $ticket['service_total_time'][$i] = 2.25;
                            break;
                        case '2 Hr 30 Min':
                            $ticket['service_total_time'][$i] = 2.5;
                            break;
                        case '2 Hr 45 Min':
                            $ticket['service_total_time'][$i] = 2.75;
                            break;
                        case '3 Hr':
                            $ticket['service_total_time'][$i] = 3;
                            break;
                    }
                }
                $ticket['service_total_time'] = implode(',', $ticket['service_total_time']);
                mysqli_query($dbc, "UPDATE `tickets` SET `service_total_time` = '".$ticket['service_total_time']."' WHERE `ticketid` = '".$ticket['ticketid']."'");
            }
        }
        set_config($dbc, 'updated_ticket8740_services', 1);
    }
    //2018-09-05 - Ticket #8740 - Ticket Service Direct/Indirect Time

    //2018-09-05 - Ticket #9007 - Vacation Pay
    if(!mysqli_query($dbc, "ALTER TABLE `contacts` ADD `vaca_pay` varchar(500) NOT NULL")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-09-05 - Ticket #9007 - Vacation Pay


    //2018-09-10 - Ticket #9085 - Manifest
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_attached` CHANGE `po_line` `po_line` text")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-09-10 - Ticket #9085 - Manifest


    //2018-09-07 - Ticket #9008 - Stat Pay
    $updated_already = get_config($dbc, 'updated_ticket9008_statpay');
    if(empty($updated_already)) {
        mysqli_query($dbc, "UPDATE `contacts` SET `stat_pay` = 'Alberta Standard 4%' WHERE `stat_pay` = 'Alberta Standard'");
        set_config($dbc, 'updated_ticket9008_statpay', 1);
    }
    //2018-09-07 - Ticket #9008 - Stat Pay

    //2018-09-13 - Ticket #8978 - Washroom Support
    if(!mysqli_query($dbc, "ALTER TABLE `key_methodologies` ADD `toileting_info` text AFTER `toileting`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-09-13 - Ticket #8978 - Washroom Support

    //2018-09-11 - Ticket #8802 - Check In/Out
    if(!mysqli_query($dbc, "CREATE TABLE `ticket_attached_checkin` (
        `id` int(11) NOT NULL,
        `ticket_attached_id` int(11) NOT NULL,
        `checked_in` varchar(10),
        `checked_out` varchar(10))")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_attached_checkin`
        ADD PRIMARY KEY (`id`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_attached_checkin`
        MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-09-11 - Ticket #8802 - Check In/Out

    //2018-09-11 - Ticket #8811 - Tagging
    if(!mysqli_query($dbc, "CREATE TABLE `contacts_tagging` (
        `id` int(11) NOT NULL,
        `contactid` int(11) NOT NULL,
        `src_table` varchar(500) NOT NULL,
        `item_id` int(11) NOT NULL,
        `last_updated_date` date NOT NULL,
        `deleted` int(1) NOT NULL DEFAULT 0)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `contacts_tagging`
        ADD PRIMARY KEY (`id`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `contacts_tagging`
        MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-09-11 - Ticket #8811 - Tagging

    //2018-09-13 - Ticket #8814 - Incident Report Flagging
    if(!mysqli_query($dbc, "ALTER TABLE `incident_report` ADD `flag_colour` VARCHAR(7)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `incident_report` ADD `flag_start` DATE NOT NULL DEFAULT '0000-00-00' AFTER `flag_colour`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `incident_report` ADD `flag_end` DATE NOT NULL DEFAULT '9999-12-31' AFTER `flag_start`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `incident_report` ADD `flag_label` TEXT AFTER `flag_colour`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `incident_report` ADD `flag_user` TEXT AFTER `flag_colour`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-09-13 - Ticket #8814 - Incident Report Flagging

    //2018-09-13 - Ticket #8826 - Planner My Notes/Journal/Scrum Notes
    $updated_already = get_config($dbc, 'updated_ticket8826_planner');
    if(empty($updated_already)) {
        mysqli_query($dbc, "UPDATE `user_settings` SET `daysheet_button_config` = CONCAT(`daysheet_button_config`,',My Notes') WHERE IFNULL(`daysheet_button_config`,'') != ''");
        set_config($dbc, 'daysheet_button_config', get_config($dbc, 'daysheet_button_config').',My Notes');
        set_config($dbc, 'updated_ticket8826_planner', 1);
    }

    if(!mysqli_query($dbc, "ALTER TABLE `daysheet_notepad` ADD `last_updated_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TRIGGER `daysheet_notepad_last_updated` BEFORE UPDATE ON `daysheet_notepad`
         FOR EACH ROW BEGIN
            SET NEW.`last_updated_time` = CURRENT_TIMESTAMP;
        END")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }

    if(!mysqli_query($dbc, "ALTER TABLE `budget_comment` ADD `last_updated_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TRIGGER `budget_comment_last_updated` BEFORE UPDATE ON `budget_comment`
         FOR EACH ROW BEGIN
            SET NEW.`last_updated_time` = CURRENT_TIMESTAMP;
        END")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }

    if(!mysqli_query($dbc, "ALTER TABLE `project_comment` ADD `last_updated_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TRIGGER `project_comment_last_updated` BEFORE UPDATE ON `project_comment`
         FOR EACH ROW BEGIN
            SET NEW.`last_updated_time` = CURRENT_TIMESTAMP;
        END")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }

    if(!mysqli_query($dbc, "ALTER TABLE `task_comments` ADD `last_updated_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TRIGGER `task_comments_last_updated` BEFORE UPDATE ON `task_comments`
         FOR EACH ROW BEGIN
            SET NEW.`last_updated_time` = CURRENT_TIMESTAMP;
        END")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }

    if(!mysqli_query($dbc, "ALTER TABLE `ticket_comment` ADD `last_updated_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TRIGGER `ticket_comment_last_updated` BEFORE UPDATE ON `ticket_comment`
         FOR EACH ROW BEGIN
            SET NEW.`last_updated_time` = CURRENT_TIMESTAMP;
        END")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }

    if(!mysqli_query($dbc, "ALTER TABLE `email_comment` ADD `last_updated_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TRIGGER `email_comment_last_updated` BEFORE UPDATE ON `email_comment`
         FOR EACH ROW BEGIN
            SET NEW.`last_updated_time` = CURRENT_TIMESTAMP;
        END")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }

    if(!mysqli_query($dbc, "ALTER TABLE `estimate_notes` ADD `last_updated_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TRIGGER `estimate_notes_last_updated` BEFORE UPDATE ON `estimate_notes`
         FOR EACH ROW BEGIN
            SET NEW.`last_updated_time` = CURRENT_TIMESTAMP;
        END")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }

    if(!mysqli_query($dbc, "ALTER TABLE `client_daily_log_notes` ADD `last_updated_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TRIGGER `client_daily_log_notes_last_updated` BEFORE UPDATE ON `client_daily_log_notes`
         FOR EACH ROW BEGIN
            SET NEW.`last_updated_time` = CURRENT_TIMESTAMP;
        END")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }

    if(!mysqli_query($dbc, "ALTER TABLE `day_overview` ADD `last_updated_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TRIGGER `day_overview_last_updated` BEFORE UPDATE ON `day_overview`
         FOR EACH ROW BEGIN
            SET NEW.`last_updated_time` = CURRENT_TIMESTAMP;
        END")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-09-13 - Ticket #8826 - Planner My Notes/Journal/Scrum Notes

    //2018-09-17 - Ticket #9189 - Calendar
    $updated_already = get_config($dbc, 'updated_ticket9189_calendar');
    if(empty($updated_already)) {
        mysqli_query($dbc, "UPDATE `general_configuration` SET `value` = CONCAT(`value`,',status') WHERE `name` = 'calendar_ticket_card_fields'");
        set_config($dbc, 'updated_ticket9189_calendar', 1);
    }
    //2018-09-17 - Ticket #9189 - Calendar

    //2018-09-18 - Ticket #9010 - Shift Heading
    if(!mysqli_query($dbc, "ALTER TABLE `contacts_shifts` ADD `heading` VARCHAR(500) AFTER `security_level`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-09-18 - Ticket #9010 - Shift Heading


    //2018-09-18 - Ticket #8755 - Sales Lead Info Gathering
    if(!mysqli_query($dbc, "ALTER TABLE `infogathering_pdf` ADD `salesid` int(11) NOT NULL")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `infogathering_pdf` ADD `deleted` int(1) NOT NULL DEFAULT 0")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }

    //2018-09-19 - Ticket #8929 - Calendar Onlin Staff
    if(!mysqli_query($dbc, "CREATE TABLE `calendar_last_active` (
        `calendarlastactiveid` int(11) NOT NULL,
        `type` varchar(500) NOT NULL,
        `contactid` int(11) NOT NULL,
        `last_active` datetime NOT NULL)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `calendar_last_active`
        ADD PRIMARY KEY (`calendarlastactiveid`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `calendar_last_active`
        MODIFY `calendarlastactiveid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-09-19 - Ticket #8929 - Calendar Onlin Staff

    //2018-09-19 - Ticket #9060 - Ticket History Icon
    $updated_already = get_config($dbc, 'updated_ticket9060_tickethistory');
    if(empty($updated_already)) {
        $ticket_fields = ','.get_field_config($dbc, 'ticket_fields').',';
        $ticket_tabs = explode(',',get_config($dbc, 'ticket_tabs'));
        foreach($ticket_tabs as $type) {
            $ticket_fields .= get_config($dbc, 'ticket_fields_'.config_safe_str($type)).',';
        }
        $history_fields = [];
        if(strpos($ticket_fields, ',History,') !== FALSE) {
            $history_fields[] = 'Ticket History';
        }
        if(strpos($ticket_fields, ',Customer History Business Ticket Type,') !== FALSE) {
            $history_fields[] = 'Customer History Business Ticket Type';
        }
        if(strpos($ticket_fields, ',Customer History Business Project Type,') !== FALSE) {
            $history_fields[] = 'Customer History Business Project Type';
        }
        if(strpos($ticket_fields, ',Customer History Business Ticket Project Type,') !== FALSE) {
            $history_fields[] = 'Customer History Business Ticket Project Type';
        }
        if(strpos($ticket_fields, ',Customer History Customer Ticket Type,') !== FALSE) {
            $history_fields[] = 'Customer History Customer Ticket Type';
        }
        if(strpos($ticket_fields, ',Customer History Customer Project Type,') !== FALSE) {
            $history_fields[] = 'Customer History Customer Project Type';
        }
        if(strpos($ticket_fields, ',Customer History Customer Ticket Project Type,') !== FALSE) {
            $history_fields[] = 'Customer History Customer Ticket Project Type';
        }
        $history_fields = implode(',', $history_fields);
        if(!empty($history_fields)) {
            set_config($dbc, 'ticket_history_fields', $history_fields);
            $ticket_quick_actions = get_config($dbc, 'quick_action_icons');
            set_config($dbc, 'quick_action_icons', $ticket_quick_actions.',history');
        }
        set_config($dbc, 'updated_ticket9060_tickethistory', 1);
    }
    //2018-09-19 - Ticket #9060 - Ticket History Icon

    //2018-09-19 - Ticket #8929 - Calendar Onlin Staff
    if(!mysqli_query($dbc, "CREATE TABLE `calendar_last_active` (
        `calendarlastactiveid` int(11) NOT NULL,
        `type` varchar(500) NOT NULL,
        `contactid` int(11) NOT NULL,
        `last_active` datetime NOT NULL)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `calendar_last_active`
        ADD PRIMARY KEY (`calendarlastactiveid`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `calendar_last_active`
        MODIFY `calendarlastactiveid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-09-19 - Ticket #8929 - Calendar Onlin Staff

    //2018-09-25 - Ticket #9310 - Bell Notification
    if(!mysqli_query($dbc, "CREATE TABLE `field_config_ticket_alerts` (
        `fieldconfigid` int(11) NOT NULL,
        `ticket_type` varchar(500) NOT NULL,
        `enabled` int(1) NOT NULL DEFAULT 0,
        `status` varchar(500) NOT NULL,
        `contactid` text NOT NULL)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `field_config_ticket_alerts`
        ADD PRIMARY KEY (`fieldconfigid`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `field_config_ticket_alerts`
        MODIFY `fieldconfigid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TABLE `ticket_alerts` (
        `id` int(11) NOT NULL,
        `ticketid` int(11) NOT NULL,
        `sent` int(1) NOT NULL DEFAULT 0)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_alerts`
        ADD PRIMARY KEY (`id`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_alerts`
        MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-09-25 - Ticket #9310 - Bell Notification


 //2018-09-19 - Ticket #9060 - Ticket History Icon
    $updated_already = get_config($dbc, 'updated_ticket9060_tickethistory');
    if(empty($updated_already)) {
        $ticket_fields = ','.get_field_config($dbc, 'ticket_fields').',';
        $ticket_tabs = explode(',',get_config($dbc, 'ticket_tabs'));
        foreach($ticket_tabs as $type) {
            $ticket_fields .= get_config($dbc, 'ticket_fields_'.config_safe_str($type)).',';
        }
        $history_fields = [];
        if(strpos($ticket_fields, ',History,') !== FALSE) {
            $history_fields[] = 'Ticket History';
        }
        if(strpos($ticket_fields, ',Customer History Business Ticket Type,') !== FALSE) {
            $history_fields[] = 'Customer History Business Ticket Type';
        }
        if(strpos($ticket_fields, ',Customer History Business Project Type,') !== FALSE) {
            $history_fields[] = 'Customer History Business Project Type';
        }
        if(strpos($ticket_fields, ',Customer History Business Ticket Project Type,') !== FALSE) {
            $history_fields[] = 'Customer History Business Ticket Project Type';
        }
        if(strpos($ticket_fields, ',Customer History Customer Ticket Type,') !== FALSE) {
            $history_fields[] = 'Customer History Customer Ticket Type';
        }
        if(strpos($ticket_fields, ',Customer History Customer Project Type,') !== FALSE) {
            $history_fields[] = 'Customer History Customer Project Type';
        }
        if(strpos($ticket_fields, ',Customer History Customer Ticket Project Type,') !== FALSE) {
            $history_fields[] = 'Customer History Customer Ticket Project Type';
        }
        $history_fields = implode(',', $history_fields);
        if(!empty($history_fields)) {
            set_config($dbc, 'ticket_history_fields', $history_fields);
            $ticket_quick_actions = get_config($dbc, 'quick_action_icons');
            set_config($dbc, 'quick_action_icons', $ticket_quick_actions.',history');
        }
        set_config($dbc, 'updated_ticket9060_tickethistory', 1);
    }
    //2018-09-19 - Ticket #9060 - Ticket History Icon

    //2018-09-19 - Ticket #8929 - Calendar Onlin Staff
    if(!mysqli_query($dbc, "CREATE TABLE `calendar_last_active` (
        `calendarlastactiveid` int(11) NOT NULL,
        `type` varchar(500) NOT NULL,
        `contactid` int(11) NOT NULL,
        `last_active` datetime NOT NULL)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `calendar_last_active`
        ADD PRIMARY KEY (`calendarlastactiveid`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `calendar_last_active`
        MODIFY `calendarlastactiveid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-09-19 - Ticket #8929 - Calendar Onlin Staff

    //2018-09-20 - Ticket #8804 - Notable Happenings
    $updated_already = get_config($dbc, 'updated_ticket8804_increp');
    if(empty($updated_already)) {
        set_config($dbc, 'incident_report_tabs', get_config($dbc, 'incident_report_summary'));
        set_config($dbc, 'updated_ticket8804_increp', 1);
    }
    //2018-09-20 - Ticket #8804 - Notable Happenings

 //2018-09-25 - Ticket #9310 - Bell Notification
    if(!mysqli_query($dbc, "CREATE TABLE `field_config_ticket_alerts` (
        `fieldconfigid` int(11) NOT NULL,
        `ticket_type` varchar(500) NOT NULL,
        `enabled` int(1) NOT NULL DEFAULT 0,
        `status` varchar(500) NOT NULL,
        `contactid` text NOT NULL)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `field_config_ticket_alerts`
        ADD PRIMARY KEY (`fieldconfigid`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `field_config_ticket_alerts`
        MODIFY `fieldconfigid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TABLE `ticket_alerts` (
        `id` int(11) NOT NULL,
        `ticketid` int(11) NOT NULL,
        `sent` int(1) NOT NULL DEFAULT 0)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_alerts`
        ADD PRIMARY KEY (`id`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_alerts`
        MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-09-25 - Ticket #9310 - Bell Notification

    //2018-09-26 - Ticket #5816 - Ticket PDFs
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_pdf` ADD `page_orientation` varchar(500) AFTER `pdf_name`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-09-26 - Ticket #5816 - Ticket PDFs

    //2018-10-01 - Ticket #9354 - Dispatch
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_attached` ADD `completed_time` datetime NOT NULL AFTER `completed`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TRIGGER `ticket_attached_completed_time` BEFORE UPDATE ON `ticket_attached`
         FOR EACH ROW BEGIN
            IF NEW.`completed` != OLD.`completed` THEN
                SET NEW.`completed_time` = CURRENT_TIMESTAMP;
            END IF;
        END")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-10-01 - Ticket #9354 - Dispatch

    //2018-10-01 - Ticket #8812 - Incident Reports
    if(!mysqli_query($dbc, "ALTER TABLE `incident_report` ADD `manager_status` varchar(100) AFTER `approved_by`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `incident_report` ADD `manager_approved_by` int(11) NOT NULL DEFAULT 0 AFTER `manager_status`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TABLE `incident_report_comment` (
        `id` int(11) NOT NULL,
        `incidentreportid` int(11) NOT NULL,
        `type` varchar(100),
        `comment` text,
        `created_date` date NOT NULL,
        `created_by` int(11) NOT NULL,
        `seen_by` text,
        `deleted` int(1) NOT NULL DEFAULT 0,
        `date_of_archival` date)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `incident_report_comment`
        ADD PRIMARY KEY (`id`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `incident_report_comment`
        MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-10-01 - Ticket #8812 - Incident Reports

    //2018-10-09 - Ticket #9570 - PO#
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_attached` CHANGE `po_num` `po_num` text")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-10-09 - Ticket #9570 - PO#

    //2018-10-10 - Ticket #8789 - Check In Summary
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_attached_checkin` ADD `checked_in_date` date NOT NULL AFTER `ticket_attached_id`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_attached_checkin` ADD `checked_out_date` date NOT NULL AFTER `checked_in`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-10-10 - Ticket #8789 - Check In Summary

    //2018-10-11 - Ticket #9653 - Best Buy Changes
    if(!mysqli_query($dbc, "ALTER TABLE `contacts` ADD `hours_of_operation` varchar(500)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TABLE `field_config_ticket_delivery_restrictions` (
        `fieldconfigid` int(11) NOT NULL,
        `ticket_type` varchar(40),
        `security_level` varchar(500),
        `to_do_date_min` varchar(500),
        `to_do_date_max` varchar(500),
        `to_do_start_time_min` varchar(500),
        `to_do_start_time_max` varchar(500))")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `field_config_ticket_delivery_restrictions`
        ADD PRIMARY KEY (`fieldconfigid`)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `field_config_ticket_delivery_restrictions`
        MODIFY `fieldconfigid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `ticket_schedule` CHANGE `type` `type` varchar(500)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-10-11 - Ticket #9653 - Best Buy Changes
    //2018-10-18 - Task 7695 - CDS Match
    if(!mysqli_query($dbc, "ALTER TABLE `match_contact` CHANGE `support_contact` `support_contact` text")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `match_contact` CHANGE `support_contact_category` `support_contact_category` text")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `match_contact` CHANGE `staff_contact_category` `staff_contact_category` text")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `match_contact` CHANGE `staff_contact` `staff_contact` text")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-10-18 - Task 7695 - CDS Match

    //2018-10-04 - Ticket #5845 - Ocean BOL
    if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `notifyid` VARCHAR(500) AFTER `agentid`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-10-04 - Ticket #5845 - Ocean BOL

    //2018-10-19 - Ticket #8225 - Documents
    $updated_already = get_config($dbc, 'updated_ticket8225_documents');
    if(empty($updated_already)) {
        $documents_all_tabs = array_filter(explode(',',get_config($dbc, 'documents_all_tabs')));
        $documents_all_tiles = array_filter(explode(',',get_config($dbc, 'documents_all_tiles')));
        $documents_all_custom_tabs = array_filter(explode(',',get_config($dbc, 'documents_all_custom_tabs')));

        $enable_tile = false;
        if(tile_enabled($dbc, 'client_documents') && !in_array('Client Documents',$documents_all_tiles)) {
            mysqli_query($dbc, "INSERT INTO `security_privileges` (`tile`, `level`, `privileges`) SELECT 'documents_all_client_documents', `level`, `privileges` FROM `security_privileges` WHERE `tile` = 'client_documents'");
            $enable_tile = true;
            $documents_all_tiles[] = 'Client Documents';
        }
        if(tile_enabled($dbc, 'staff_documents') && !in_array('Staff Documents',$documents_all_tiles)) {
            mysqli_query($dbc, "INSERT INTO `security_privileges` (`tile`, `level`, `privileges`) SELECT 'documents_all_staff_documents', `level`, `privileges` FROM `security_privileges` WHERE `tile` = 'staff_documents'");
            $enable_tile = true;
            $documents_all_tiles[] = 'Staff Documents';
        }
        if(tile_enabled($dbc, 'internal_documents') && !in_array('Internal Documents',$documents_all_tiles)) {
            mysqli_query($dbc, "INSERT INTO `security_privileges` (`tile`, `level`, `privileges`) SELECT 'documents_all_internal_documents', `level`, `privileges` FROM `security_privileges` WHERE `tile` = 'internal_documents'");
            $enable_tile = true;
            $documents_all_tiles[] = 'Internal Documents';
        }
        if(tile_enabled($dbc, 'marketing_material') && !in_array('Marketing Material',$documents_all_tiles)) {
            mysqli_query($dbc, "INSERT INTO `security_privileges` (`tile`, `level`, `privileges`) SELECT 'documents_all_marketing_material', `level`, `privileges` FROM `security_privileges` WHERE `tile` = 'marketing_material'");
            $enable_tile = true;
            $documents_all_tiles[] = 'Marketing Material';
        }
        if(tile_enabled($dbc, 'documents')) {
            $documents = mysqli_query($dbc, "SELECT * FROM `documents` WHERE `deleted` = 0");
            while($row = mysqli_fetch_array($documents)) {
                $tab_name = config_safe_str($row['tile_name']);
                $category = $row['sub_tile_name'];
                $title = $row['document'];
                $doc_type = substr($title, strrpos($title, '.') + 1);
                mysqli_query($dbc, "INSERT INTO `custom_documents` (`tab_name`, `category`, `custom_documents_type`, `title`) VALUES ('$tab_name', '$category', '$doc_type', '$title')");
                $docid = mysqli_insert_id($dbc);
                mysqli_query($dbc, "INSERT INTO `custom_documents_uploads` (`custom_documentsid`, `type`, `document_link`) VALUES ('$docid', 'Document', '$title')");
                if(!in_array($row['tile_name'], $documents_all_tabs)) {
                    $documents_all_tabs[] = $row['tile_name'];  
                }
                if(!in_array($row['tile_name'], $documents_all_custom_tabs)) {
                    $documents_all_custom_tabs[] = $row['tile_name'];   
                }
                $config_exists = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_custom_documents` WHERE `tab_name` = '$tab_name'"));
                if(empty($config_exists)) {
                    mysqli_query($dbc, "INSERT INTO `field_config_custom_documents` (`tab_name`, `fields`, `dashboard`) VALUES ('".$tab_name."', 'Custom Documents Type,Category,Title,Uploader,Link', 'Custom Documents Type,Category,Title,Uploader,Link')");
                }
            }
            mysqli_query($dbc, "INSERT INTO `security_privileges` (`tile`, `level`, `privileges`) SELECT 'documents_all', `level`, `privileges` FROM `security_privileges` WHERE `tile` = 'documents'");
            $enable_tile = true;
        }
        if($enable_tile) {
            $tile_security = mysqli_query($dbc, "SELECT * FROM `tile_security` WHERE `tile_name` = 'documents_all'");
            if(!empty($tile_security)) {
                mysqli_query($dbc, "UPDATE `tile_security` SET `admin_enabled` = 1, `user_enabled` = 1 WHERE `tile_name` = 'documents_all'");
            } else {
                mysqli_query($dbc, "INSERT INTO `tile_security` (`tile_name`, `admin_enabled`, `user_enabled`) VALUES ('documents_all', 1, 1)");
            }
            foreach(get_security_levels($dbc) as $level_name) {
                $sql = "INSERT INTO `security_privileges` (`tile`, `level`, `privileges`) SELECT 'documents_all', '$level_name', '*hide*' FROM (SELECT COUNT(*) num FROM `security_privileges` WHERE `tile`='documents_all' AND `level`='$level_name') rows WHERE rows.num=0";
                $result = mysqli_query($dbc, $sql);
            }
            set_config($dbc, 'documents_all_tabs', implode(',',$documents_all_tabs));
            set_config($dbc, 'documents_all_tiles', implode(',',$documents_all_tiles));
            set_config($dbc, 'documents_all_custom_tabs', implode(',',$documents_all_custom_tabs));
        }
        
        set_config($dbc, 'updated_ticket8225_documents', 1);
    }
    //2018-10-19 - Ticket #8225 - Documents

    //2018-10-22 - Ticket #9920 - Ticket Types
    if(!mysqli_query($dbc, "ALTER TABLE `user_settings` ADD `appt_calendar_tickettypes` text AFTER `appt_calendar_classifications`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-10-22 - Ticket #9920 - Ticket Types

    //2018-10-24 - Ticket #9892 - Ticket Log PDF
    if(!mysqli_query($dbc, "ALTER TABLE `field_config_ticket_log` ADD `header_logo_align` VARCHAR(100) AFTER `header_logo`")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-10-24 - Ticket #9892 - Ticket Log PDF

    //2018-10-25 - Task #7753 - SIN
    if(!mysqli_query($dbc, "ALTER TABLE `contacts` CHANGE `sin` `sin` VARCHAR(100)")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    //2018-10-25 - Task #7753 - SIN

    echo "Baldwin's DB Changes Done<br />\n";
