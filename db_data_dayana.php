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

    echo "Dayana's DB Changes Done<br />\n";
?>