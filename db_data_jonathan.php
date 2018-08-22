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
		
		set_config($dbc, 'db_version_jonathan', 8);
	}
	
	if($db_version_jonathan < 9) {
		// July 25, 2018
		if(!mysqli_query($dbc, "ALTER TABLE `ticket_pdf_field_values` ADD `deleted` TINYINT(1) NOT NULL DEFAULT 0")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		
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
		
		set_config($dbc, 'db_version_jonathan', 8);
	}
	
	if(get_config($dbc, 'update_timesheet_config') < 1) {
		// July 9, 2018
		if(!mysqli_query($dbc, "UPDATE `field_config` SET `time_cards`=CONCAT(`time_cards`,',comment_box,')")) {
			echo "Error: ".mysqli_error($dbc)."<br />\n";
		}
		set_config($dbc, 'update_timesheet_config', 1);
	}
	
	echo "Jonathan's DB Changes Done<br />\n";
?>