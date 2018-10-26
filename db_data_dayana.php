<?php
/* Update Databases */

    //Dayana's Database Changes
    echo "Dayana's DB Changes:<br />\n";

mysqli_query($dbc, "CREATE TABLE IF NOT EXISTS `field_config_ticket_security` (
  `fieldconfigid` int(11) NOT NULL AUTO_INCREMENT,
  `security_level` varchar(200) DEFAULT NULL,
  `subtabs_hidden` text,
  `subtabs_viewonly` text NOT NULL,
  `fields_hidden` text NOT NULL,
  `fields_viewonly` text NOT NULL,
  PRIMARY KEY (`fieldconfigid`)
)");

mysqli_query($dbc, "ALTER TABLE `project`
ADD `projection_service_heading` VARCHAR(500) NULL AFTER `project_color_code`,
ADD `projection_service_price` VARCHAR(10) NULL AFTER `projection_service_heading`,
ADD `projection_product_heading` VARCHAR(500) NULL AFTER `projection_service_price`,
ADD `projection_product_price` VARCHAR(10) NULL AFTER `projection_product_heading`,
ADD `projection_task_heading` VARCHAR(500) NULL AFTER `projection_product_price`,
ADD `projection_task_price` VARCHAR(10) NULL AFTER `projection_task_heading`,
ADD `projection_inventory_heading` VARCHAR(500) NULL AFTER `projection_task_price`,
ADD `projection_inventory_price` VARCHAR(10) NULL AFTER `projection_inventory_heading`,
ADD `projection_admin_heading` VARCHAR(500) NULL AFTER `projection_inventory_price`,
ADD `projection_admin_price` VARCHAR(10) NULL AFTER `projection_admin_heading`
");

mysqli_query($dbc, "ALTER TABLE `contacts` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `sales_lead` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `inventory` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `equipment` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `field_sites` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `field_jobs` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `field_foreman_sheet` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `field_po` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `field_work_ticket` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `field_invoice` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `tickets` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `estimate` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `email_communication` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `newsboard` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `match_contact` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `expense` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `point_of_sell` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `sales_order` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `purchase_orders` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `vendor_price_list` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `infogathering` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `marketing_material` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `contracts` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `incident_report` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `performance_review` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `intake_forms` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `point_of_sell` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `sales_order` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `purchase_orders` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `project_manage_assign_to_timer` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `pos_giftcards` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `staff_documents` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `custom_documents` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `website_promotions` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `newsboard` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `medication` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `newsboard` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `how_to_guide` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `notes` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `email_communication` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `estimate` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `quote` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `products` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `match_contact` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `internal_documents` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `documents` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `client_documents` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `hr` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `tickets` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `checklist` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `item_checklist` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `manuals` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `certificate` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `agenda_meeting` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `properties` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `reminders` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `infogathering` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `goal` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `compensation` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `hourly_pay` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `field_payroll` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `custom` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `labour` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `pos_touch_coupons` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `project_document` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `marketing_material` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `fund_development_funding` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `individual_support_plan` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `key_methodologies` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `project_workflow` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `passwords` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `services` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `promotion` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `expense` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `package` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `time_tracking` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `exercise_config` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `budget` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `asset` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `order_lists` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `safety` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `material` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `checklist` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `ticket_document` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `driving_log_timer` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `category_rate_table` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `staff_rate_table` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `equipment_rate_table` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `position_rate_table` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `site_work_po` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `seizure_record` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `blood_glucose` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `bowel_movement` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `daily_water_temp` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `daily_water_temp_bus` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `daily_fridge_temp` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `daily_freezer_temp` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `daily_dishwasher_temp` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `patientform` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `service_rate_card` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `support` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `tasklist` ADD `date_of_archival` DATE NULL AFTER `deleted`");


mysqli_query($dbc, "ALTER TABLE `positions` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `bid` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `booking` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `daysheet_reminders` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `ticket_attached` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `contacts_shifts` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `equipment_assignment_staff` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `appointment_type` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `client_project` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `ticket_notifications` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `marsheet_row` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `marsheet` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `seizure_record` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `assessment` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `treatment` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `treatment_exercise_plan` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `treatment_plan` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `contracts` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `cost_estimate` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `tasklist` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `equipment_assignment` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `estimate` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `user_form_fields` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `user_form_page_detail` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `user_form_page` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `intake_forms` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `jobs` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `tile_rate_card` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `custom_charts` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `field_config_custom_charts_lines` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `field_config_custom_charts` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `daysheet_reminders` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `project_form` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `project_custom_details` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `project` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `rate_card` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `intake` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `sales_order_temp` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `sales_order_template` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `time_cards` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `ticket_attached` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `ticket_time_list` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `ticket_service_checklist` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `assessment` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `treatment` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `treatment_exercise_plan` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `treatment_plan` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `my_certificate` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `position_rate_table` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `equipment_rate_table` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `category_rate_table` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `staff_rate_table` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `service_rate_card` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `tile_rate_card` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `site_work_po` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `waitlist` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `checklist_subtab` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `checklist_name` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `item_checklist_line` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `client_project_milestone_checklist` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `sales` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `contracts_staff` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `client_daily_log_notes` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `item_checklist_line` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `equipment_wo_checklist` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `estimate_pdf_setting` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `estimate_scope` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `treatment_exercise_plan` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `expense_categories` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `expense_policy` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `user_form_fields` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `user_forms` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `hr` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `manuals` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `item_checklist_line` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `contact_package_sold` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `jobs_milestone_checklist` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `tasklist` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `company_rate_card` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `rate_card_breakdown` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `ticket_comment` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `safety` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `sales` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `tile_dashboards` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `project_manage_assign_to_timer` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `site_work_checklist` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `staff_rate_table` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `support_services` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `task_board` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `ticket_attached` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `holidays` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `time_cards` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `checklist_document` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `sales` ADD `date_of_archival` DATE NULL AFTER `deleted`");
mysqli_query($dbc, "ALTER TABLE `order_lists` ADD `date_of_archival` DATE NULL AFTER `deleted`");

mysqli_query($dbc, "ALTER TABLE `download_tracking` CHANGE `table` `table_name` VARCHAR(500) NULL");

mysqli_query($dbc, "ALTER TABLE `agenda_meeting` ADD `heading` VARCHAR(500) NULL AFTER `location`");

$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='vendor_tile_name'"));
if($get_config['configid'] > 0) {
    $query_update_employee = "UPDATE `general_configuration` SET value = 'Vendors' WHERE name='vendor_tile_name'";
    $result_update_employee = mysqli_query($dbc, $query_update_employee);
} else {
    $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('vendor_tile_name', 'Vendors')";
    $result_insert_config = mysqli_query($dbc, $query_insert_config);
}


mysqli_query($dbc, "ALTER TABLE `estimate_scope` ADD `today_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `sort_order`");

mysqli_query($dbc, "CREATE TABLE IF NOT EXISTS `project_timer` (
  `projecttimerid` int(10) NOT NULL AUTO_INCREMENT,
  `projectid` int(10) DEFAULT NULL,
  `staff` int(10) DEFAULT NULL,
  `today_date` date DEFAULT NULL,
  `timer_value` time DEFAULT NULL,
  PRIMARY KEY (`projecttimerid`)
");

mysqli_query($dbc, "ALTER TABLE `checklist` ADD `checklist_tile` INT(1) NOT NULL DEFAULT '0' AFTER `subtabid`");

mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('contacts_summary', 'Per Category,Per Business')");

mysqli_query($dbc, "ALTER TABLE `admin_tile_config` ADD `tasks_updated` VARCHAR(500) NULL AFTER `client_documentation_history`, ADD `tasks_updated_history` TEXT NULL AFTER `tasks_updated`");

//mysqli_query($dbc, "ALTER TABLE `tile_config` ADD `tasks_updated` VARCHAR(500) NULL AFTER `client_documentation_history`, ADD `tasks_updated_history` TEXT NULL AFTER `tasks_updated`");

mysqli_query($dbc, "ALTER TABLE `tasklist` ADD `flag_label` VARCHAR(200) NOT NULL AFTER `flag_colour`, ADD `flag_start` DATE NULL AFTER `flag_label`, ADD `flag_end` DATE NULL AFTER `flag_start`");

mysqli_query($dbc, "ALTER TABLE `task_dashboard` ADD `task_fields` TEXT NULL AFTER `task_dashboard_tile`");

mysqli_query($dbc, "UPDATE task_dashboard SET task_fields='Board Type,Board Name,Status,Task Name,To Do Date,Assign Staff,Flag This,Send Alert,Send Email,Schedule Reminder,Attach File,Comments,Add Time,Track Time' WHERE task_id=1");

mysqli_query($dbc, "ALTER TABLE `tasklist` CHANGE `flag_label` `flag_label` VARCHAR(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL");

mysqli_query($dbc, "ALTER TABLE `sales` ADD `number_of_days` INT(10) NOT NULL DEFAULT '0' AFTER `classification`");

mysqli_query($dbc, "ALTER TABLE `sales` ADD `number_of_days_start_date` DATE NULL AFTER `number_of_days`");
mysqli_query($dbc, "ALTER TABLE `contacts` ADD `serviceid` INT(10) NULL AFTER `vaca_pay`");

mysqli_query($dbc, "ALTER TABLE `tasklist_time` ADD `start_time` VARCHAR(50) NULL AFTER `tasklistid`, ADD
`end_time` VARCHAR(50) NULL AFTER `start_time`");

mysqli_query($dbc, "ALTER TABLE `tasklist` CHANGE `status` `status` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL");

mysqli_query($dbc, "ALTER TABLE `tasklist` ADD `estimated_time` TIME NOT NULL DEFAULT '00:00:00' AFTER `date_of_archival`");

mysqli_query($dbc, "UPDATE field_config
SET tickets = REPLACE(tickets, ',Timer,', ',Timer,Time Tracking Block,Day Tracking Block,')
WHERE tickets LIKE '%,Timer,%'");

mysqli_query($dbc, "ALTER TABLE `time_cards` ADD `approve_by` INT(10) NULL AFTER `approv`");

mysqli_query($dbc, "ALTER TABLE `time_cards` ADD `approve_date` DATE NULL AFTER `approve_by`");

mysqli_query($dbc, "ALTER TABLE `time_cards` ADD `end_time_from` VARCHAR(1000) NULL AFTER `end_time`");

mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('task_default_status', 'Doing Today')");

$layout = get_config($dbc, 'timesheet_layout');

if($layout == 'position_dropdown' || $layout == 'ticket_task') {
	set_config($dbc, 'timesheet_layout', 'multi_line');
}

mysqli_query($dbc, "ALTER TABLE `client_documents_uploads` ADD `document_link_name` VARCHAR(500) NULL AFTER `document_link`");

mysqli_query($dbc, "ALTER TABLE `custom_documents_uploads` ADD `document_link_name` VARCHAR(500) NULL AFTER `document_link`");

mysqli_query($dbc, "ALTER TABLE `internal_documents_uploads` ADD `document_link_name` VARCHAR(500) NULL AFTER `document_link`");

mysqli_query($dbc, "ALTER TABLE `staff_documents_uploads` ADD `document_link_name` VARCHAR(500) NULL AFTER `document_link`");

mysqli_query($dbc, "ALTER TABLE `marketing_material_uploads` ADD `document_link_name` VARCHAR(500) NULL AFTER `document_link`");

mysqli_query($dbc, "ALTER TABLE `contacts` ADD `days_per_week` INT(10) NULL AFTER `hours_of_operation`, ADD `hours_per_week` INT(10) NULL AFTER `days_per_week`");

mysqli_query($dbc, "ALTER TABLE `invoice` ADD `customer_billing_status` VARCHAR(200) NULL DEFAULT 'Pending' AFTER `patient_payment_receipt`");


mysqli_query($dbc, "ALTER TABLE `task_board` ADD `flag_label` VARCHAR(200) NULL AFTER `date_of_archival`, ADD `flag_start` DATE NULL AFTER `flag_label`, ADD `flag_end` DATE NULL AFTER `flag_start`");

mysqli_query($dbc, "ALTER TABLE `project` ADD `classification1` VARCHAR(500) NULL AFTER `flag_label`");


mysqli_query($dbc, "ALTER TABLE `contacts` ADD `flag_colour` VARCHAR(20) NULL AFTER `update_url_expiry`");

mysqli_query($dbc, "ALTER TABLE `contacts` ADD `flag_label` VARCHAR(500) NULL AFTER `flag_colour`, ADD `flag_start` DATE NULL AFTER `flag_label`, ADD `flag_end` DATE NULL AFTER `flag_start`");

mysqli_query($dbc, "ALTER TABLE `equipment` ADD `favorite` INT(1) NOT NULL DEFAULT '0' AFTER `equipment_image`");


    echo "Dayana's DB Changes Done<br />\n";

?>