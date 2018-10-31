<?php
	//Jonathan's Database Changes
	echo "Jonathan's DB Changes:<br />\n";
	$db_version_jonathan = get_config($dbc, 'db_version_jonathan');
	/*** USE THE FOLLOWING EXAMPLES: ***
	if(!mysqli_query($dbc, "CREATE TABLE IF NOT EXISTS `table_name` (
		`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`deleted` TINYINT(1) NOT NULL DEFAULT 0
	)")) {
		echo "Error: ".mysqli_error($dbc)."<br />\n";
	}
	if(!mysqli_query($dbc, "ALTER TABLE `table_name` ADD `column` VARCHAR(40) DEFAULT '' AFTER `exist_column`")) {
		echo "Error: ".mysqli_error($dbc)."<br />\n";
	}
	if(!mysqli_query($dbc, "ALTER TABLE `tickets` CHANGE `siteid` `siteid` TEXT NOT NULL")) {
		echo "Error: ".mysqli_error($dbc)."<br />\n";
	} */
	set_config($dbc, 'db_version_jonathan', 6);
	if($db_version_jonathan < 7) {
		// June 16, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_schedule` ADD `notes` TEXT AFTER `order_number`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
    
		// June 18, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_manifests` ADD `revision` INT(11) UNSIGNED NOT NULL DEFAULT 1 AFTER `signature`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_manifests` ADD `history` TEXT AFTER `signature`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
    
		// June 20, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `estimate_scope` ADD `pricing` VARCHAR(20) AFTER `price`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
    
		// June 25, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `rate_card` ADD `ref_card` TEXT AFTER `rate_card_name`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		// June 27, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_history` ADD `src` TEXT AFTER `userid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		
		set_config($dbc, 'db_version_jonathan', 7);
	}
	
	if($db_version_jonathan < 8) {
		// June 29, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `flag_start` DATE NOT NULL DEFAULT '0000-00-00' AFTER `flag_colour`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `flag_end` DATE NOT NULL DEFAULT '9999-12-31' AFTER `flag_start`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `checklist_name` ADD `flag_start` DATE NOT NULL DEFAULT '0000-00-00' AFTER `flag_colour`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `checklist_name` ADD `flag_end` DATE NOT NULL DEFAULT '9999-12-31' AFTER `flag_start`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `checklist_name` ADD `flag_label` TEXT AFTER `flag_colour`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `tasklist` ADD `flag_start` DATE NOT NULL DEFAULT '0000-00-00' AFTER `flag_colour`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `tasklist` ADD `flag_end` DATE NOT NULL DEFAULT '9999-12-31' AFTER `flag_start`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `tasklist` ADD `flag_label` TEXT AFTER `flag_colour`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `intake` ADD `flag_start` DATE NOT NULL DEFAULT '0000-00-00' AFTER `flag_colour`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `intake` ADD `flag_end` DATE NOT NULL DEFAULT '9999-12-31' AFTER `flag_start`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `intake` ADD `flag_label` TEXT AFTER `flag_colour`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
    
		// July 5, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `purchase_orders` ADD `date_sent` TEXT AFTER `status_history`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `purchase_orders` ADD `sent_by` TEXT AFTER `date_sent`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		
		// July 6, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `main_approval` INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `sign_off_signature`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `final_approval` INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `main_approval`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `main_approval_signed` TEXT AFTER `main_approval`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `final_approval_signed` TEXT AFTER `final_approval`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		
		// July 5, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `purchase_orders` ADD `date_sent` TEXT AFTER `status_history`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `purchase_orders` ADD `sent_by` TEXT AFTER `date_sent`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		
		// July 11, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `equipment` ADD `location_cookie` TEXT AFTER `location`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		
		// July 12, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_schedule` ADD `uploads` TEXT AFTER `details`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		
		// July 23, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `field_config_project_admin` ADD `status` TEXT AFTER `staff`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `field_config_project_admin` ADD `unlocked_fields` TEXT AFTER `status`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		
		// July 25, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_pdf_field_values` ADD `deleted` TINYINT(1) NOT NULL DEFAULT 0")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		
		// July 4, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `phone_communication` ADD `file` TEXT AFTER `comment`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `phone_communication` ADD `manual` VARCHAR(20) AFTER `contactid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		
		set_config($dbc, 'db_version_jonathan', 7);
		// July 23, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `sales` ADD `flag_colour` VARCHAR(7) AFTER `contactid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `sales` ADD `flag_start` DATE NOT NULL DEFAULT '0000-00-00' AFTER `flag_colour`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `sales` ADD `flag_end` DATE NOT NULL DEFAULT '9999-12-31' AFTER `flag_start`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `sales` ADD `flag_label` TEXT AFTER `flag_colour`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `time_cards` ADD `salesid` INT(11) UNSIGNED NOT NULL AFTER `email_communicationid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		
		// July 25, 2018
		if(!mysqli_query($dbc, "CREATE TABLE IF NOT EXISTS `sales_history` (
			`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`salesid` INT(11) UNSIGNED NOT NULL DEFAULT 0,
			`created_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
			`created_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
			`history` TEXT,
			`deleted` TINYINT(1) NOT NULL DEFAULT 0
		)")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		
		// July 27, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `details_tile` TEXT AFTER `details_where`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `details_tab` TEXT AFTER `details_tile`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `email_communication` ADD `ticketid` INT(11) UNSIGNED NOT NULL AFTER `projectid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		
		set_config($dbc, 'db_version_jonathan', 8);
	}
	
	if($db_version_jonathan < 10) {
		// July 30, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `tickets` CHANGE `siteid` `siteid` TEXT NOT NULL")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_attached` CHANGE `siteid` `siteid` TEXT NOT NULL")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `support` ADD `software_userid` INT(11) UNSIGNED NOT NULL AFTER `software_url`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `support` ADD `software_user_name` TEXT AFTER `software_userid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `support` ADD `software_role` TEXT AFTER `software_user_name`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		
		// August 7, 2018
		if(!mysqli_query($dbc, "CREATE TABLE IF NOT EXISTS `newsboard_seen` (
			`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`newsboardid` INT(11) UNSIGNED NOT NULL DEFAULT 0,
			`contactid` INT(11) UNSIGNED NOT NULL DEFAULT 0,
			`seen_date` DATETIME DEFAULT CURRENT_TIMESTAMP
		)")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        // August 10, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `purchase_orders` ADD `equipmentid` INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `siteid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        
        // August 11, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `newsboard_seen` ADD `newsboard_src` VARCHAR(2) DEFAULT NULL AFTER `newsboardid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
    
        // August 13, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `equipment` ADD `last_oil_filter_change_hrs` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `last_oil_filter_change`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `equipment` ADD `next_oil_filter_change_hrs` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `next_oil_filter_change`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `equipment` ADD `last_insp_tune_up_hrs` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `last_insp_tune_up`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `equipment` ADD `next_insp_tune_up_hrs` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `next_insp_tune_up`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `equipment` ADD `last_tire_rotation_hrs` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `last_tire_rotation`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `equipment` ADD `next_tire_rotation_hrs` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `next_tire_rotation`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `equipment` ADD `current_hrs` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `mileage`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `site_work_driving_inspect` ADD `begin_hours` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `final_odo_kms`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `site_work_driving_inspect` ADD `final_hours` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `begin_hours`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `driving_log_safety_inspect` ADD `begin_hours` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `final_odo_kms`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `driving_log_safety_inspect` ADD `final_hours` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `begin_hours`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        
        //August 19, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `sales_document` ADD `deleted` TINYINT NOT NULL DEFAULT 0")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `sales_notes` ADD `deleted` TINYINT NOT NULL DEFAULT 0")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `sales` CHANGE `created_date` `created_date` DATETIME NULL DEFAULT CURRENT_TIMESTAMP")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        
    //August 23, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `project` ADD `project_team` TEXT AFTER `project_colead`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        
    //August 24, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `project_path_milestone` ADD `items` TEXT AFTER `checklist`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `project_path_milestone` ADD `intakes` TEXT AFTER `items`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
    
    //August 29, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `email_communication` ADD `salesid` INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `contactid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
    
        //August 31, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `invoice` ADD `area` TEXT AFTER `injuryid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `invoice` ADD `po_num` TEXT AFTER `injuryid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `invoice` ADD `contract` TEXT AFTER `injuryid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
    
        //September 5, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `project` CHANGE `projection_service_heading` `projection_service_heading` TEXT")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `project` CHANGE `projection_service_price` `projection_service_price` TEXT")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `project` CHANGE `projection_product_heading` `projection_product_heading` TEXT")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `project` CHANGE `projection_product_price` `projection_product_price` TEXT")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `project` CHANGE `projection_task_heading` `projection_task_heading` TEXT")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `project` CHANGE `projection_task_price` `projection_task_price` TEXT")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `project` CHANGE `projection_inventory_heading` `projection_inventory_heading` TEXT")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `project` CHANGE `projection_inventory_price` `projection_inventory_price` TEXT")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `project` CHANGE `projection_admin_heading` `projection_admin_heading` TEXT")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `project` CHANGE `projection_admin_price` `projection_admin_price` TEXT")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `field_config_incident_report` ADD `user_emails` TINYINT(1) NOT NULL DEFAULT 0 AFTER `keep_revisions`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `field_config_incident_report` ADD `all_emails` TEXT AFTER `user_emails`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
    
        //September 6, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `project` ADD `deadline` VARCHAR(10) AFTER `followup`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}

        //September 11, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `match_contact` ADD `tile_list` TEXT AFTER `staff_contact`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        
        //September 17, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_schedule` CHANGE `volume` `volume` TEXT")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        
        //September 18, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `email_communication` CHANGE `today_date` `today_date` DATETIME")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `communication_tags` TEXT AFTER `created_by`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "UPDATE `tickets` SET `communication_tags`=`clientid` WHERE `communication_tags` IS NULL AND `clientid` IS NOT NULL AND `clientid` != '' AND `deleted`=0 AND `status` != 'Archive'")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        
        // September 25, 2018 - Ticket 9343
		if(!mysqli_query($dbc, "ALTER TABLE `invoice_lines` ADD `stop_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `ticketid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        // Ticket 9343

    
        //September 20, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_schedule` ADD `surcharge` TEXT AFTER `serviceid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_schedule` ADD `service_discount` TEXT AFTER `serviceid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_schedule` ADD `service_discount_type` TEXT AFTER `serviceid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}

        // October 1, 2018 - Ticket 9483
		if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `sort_status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `status`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_schedule` ADD `sort_status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `status`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        // Ticket 9343

        // October 12, 2018 - Ticket 9129
		if(!mysqli_query($dbc, "ALTER TABLE `invoice_compensation` CHANGE `serviceid` `item_id` INT(11) UNSIGNED NOT NULL DEFAULT 0")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `invoice_compensation` CHANGE `therapistsid` `contactid` INT(11) UNSIGNED NOT NULL DEFAULT 0")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `invoice_compensation` ADD `item_type` VARCHAR(20) NOT NULL DEFAULT 'services' AFTER `item_id`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		} else {
            mysqli_query($dbc, "UPDATE `invoice_compensation` SET `item_type`='services' WHERE `item_type` IS NULL OR `item_type` = ''");
        }
		if(!mysqli_query($dbc, "ALTER TABLE `invoice_compensation` ADD `line_id` INT(11) NOT NULL DEFAULT 0 AFTER `invoiceid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `invoice_compensation` ADD `comp_percent` DECIMAL(5,2) NOT NULL DEFAULT 100 AFTER `admin_fee`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `invoice_compensation` ADD `compensation` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `comp_percent`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "CREATE TABLE IF NOT EXISTS `rate_compensation` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `rate_card` INT(11) UNSIGNED NOT NULL DEFAULT 0,
            `item_type` VARCHAR(20) NOT NULL DEFAULT '',
            `comp_percent` DECIMAL(5,2) NOT NULL DEFAULT 100,
            `deleted` TINYINT(1) NOT NULL DEFAULT 0
        )")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        // Ticket 9129

        // October 15, 2018 - Ticket 9586
		if(!mysqli_query($dbc, "ALTER TABLE `field_config_ticket_alerts` ADD `subject` TEXT AFTER `status`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		} else {
            mysqli_query($dbc, "UPDATE `field_config_ticket_alerts` SET `subject`='New ".TICKET_NOUN." has been created - [TICKET]'");
        }
		if(!mysqli_query($dbc, "ALTER TABLE `field_config_ticket_alerts` ADD `body` TEXT AFTER `status`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		} else {
            mysqli_query($dbc, "UPDATE `field_config_ticket_alerts` SET `body`='&lt;p&gt;You are receiving this email because you are tagged to be alerted when a new ".TICKET_NOUN." of this type is created.'&lt;/p&gt;");
        }
        // Ticket 9586

        // October 16, 2018 - Ticket 9744
		if(!mysqli_query($dbc, "ALTER TABLE `equipment` ADD `part_serials` TEXT AFTER `serial_number`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `equipment` ADD `part_serial_names` TEXT AFTER `serial_number`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        // Ticket 9744

        // October 18, 2018 - Ticket 9389
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_attached` ADD `created_by` INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `date_stamp`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        // Ticket 9389

        // October 19, 2018 - Ticket 9873
		if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `service_no_bill` TEXT AFTER `serviceid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_schedule` ADD `service_no_bill` TEXT AFTER `serviceid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        // Ticket 9873

        // October 22, 2018 - Ticket 9919
		if(!mysqli_query($dbc, "ALTER TABLE `field_config_project_admin` ADD `options` TEXT AFTER `status`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        // Ticket 9919

        // October 23, 2018 - Ticket 9720
		if(!mysqli_query($dbc, "ALTER TABLE `contacts` ADD `software_url` TEXT AFTER `website`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        // Ticket 9720

        // October 23, 2018 - Ticket 9827
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_schedule` ADD `status_date` DATETIME AFTER `status`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_schedule` ADD `status_contact` INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `status_date`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        // Ticket 9827

        // October 24, 2018 - Ticket 9140
		if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `estimateid` INT(11) UNSIGNED NOT NULL AFTER `projectid`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        // Ticket 9140

        // October 26, 2018 - Ticket 9488
		if(!mysqli_query($dbc, "ALTER TABLE `rate_compensation` ADD `comp_fee` DECIMAL(10,2) AFTER `comp_percent`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        // Ticket 9488

        // October 26, 2018 - Ticket 9488
		if(!mysqli_query($dbc, "ALTER TABLE `tickets` ADD `incognito_fields` TEXT AFTER `ticket_label_date`")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
        // Ticket 9488
		
		set_config($dbc, 'db_version_jonathan', 8);
    }

	
	if(get_config($dbc, 'update_timesheet_config') < 1) {
		// July 9, 2018
		if(!mysqli_query($dbc, "UPDATE `field_config` SET `time_cards`=CONCAT(`time_cards`,',comment_box,')")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		set_config($dbc, 'update_timesheet_config', 1);
	}
	if(get_config($dbc, 'update_project_details_path_config') < 1) {
		// July 9, 2018
		if(!mysqli_query($dbc, "UPDATE `field_config_project` SET `config_tabs`=REPLACE(`config_tabs`,'Information,','Information,Details Path,Staff,')")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		set_config($dbc, 'update_project_details_path_config', 1);
	}
	if(get_config($dbc, 'update_timesheet_layout_fields') < 1) {
		// September 19, 2018
		if(!mysqli_query($dbc, "UPDATE `general_configuration` LEFT JOIN `field_config` ON 1=1 SET `time_cards`=CONCAT(REPLACE(`time_cards`,'total_tracked_hrs,',''),',ticket_select,task_select,total_tracked_hrs_task,total_hrs,') WHERE `general_configuration`.`name`='timesheet_layout' AND `general_configuration`.`value`='ticket_task'")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "UPDATE `general_configuration` LEFT JOIN `field_config` ON 1=1 SET `time_cards`=CONCAT(REPLACE(`time_cards`,'total_tracked_hrs,',''),',position_select,total_tracked_hrs_task,total_hrs,') WHERE `general_configuration`.`name`='timesheet_layout' AND `general_configuration`.`value`='position_dropdown'")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		set_config($dbc, 'update_timesheet_layout_fields', 1);
	}
	if(get_config($dbc, 'update_delivery_google') < 1) {
		// September 19, 2018
		if(!mysqli_query($dbc, "UPDATE `general_configuration` SET `value`=REPLACE(`value`,'Delivery Pickup Address','Delivery Pickup Address,Delivery Pickup Address Google') WHERE `name` LIKE 'ticket_%'")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		if(!mysqli_query($dbc, "UPDATE `field_config` SET `tickets`=REPLACE(`tickets`,'Delivery Pickup Address','Delivery Pickup Address,Delivery Pickup Address Google')")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		set_config($dbc, 'update_delivery_google', 1);
	}
    
    // Ticket 9873
	if(get_config($dbc, 'update_project_admin_notes') < 1) {
		// October 19, 2018
		if(!mysqli_query($dbc, "UPDATE `general_configuration` SET `value`=CONCAT(`value`,',Notes,') WHERE `name` LIKE 'project_admin_fields'")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		set_config($dbc, 'update_project_admin_notes', 1);
	}
    // Ticket 9873
    
    // Ticket 9491
	if(get_config($dbc, 'update_start_day') < 1) {
		// October 25, 2018
        if(!empty(get_config($dbc, 'timesheet_start_tile'))) {
            if(!mysqli_query($dbc, "INSERT INTO `tile_security` (`tile_name`, `admin_enabled`, `user_enabled`) VALUES ('start_day_button',1,1)")) {
                echo "Error: ".mysqli_error($dbc)."<br />\n";
            }
        } else {
            set_config($dbc, 'timesheet_start_tile', 'Start Day');
        }
		set_config($dbc, 'update_start_day', 1);
	}
    // Ticket 9491
	
	echo "Jonathan's DB Changes Done<br />\n";
?>