<?php
	/*
	 * Jay's DB changes
	 */
	echo "<br /><br />\n\nJay's DB changes:<br />\n";
    
    // 17-Jul-2018
    if(!mysqli_query($dbc, "CREATE TABLE `newsboard_comments` ( `nbcommentid` INT(11) NOT NULL AUTO_INCREMENT, `newsboardid` INT(11) NOT NULL, `contactid` INT(11) NOT NULL, `created_date` DATE NOT NULL, `comment` TEXT NOT NULL, `deleted` INT(1) NOT NULL DEFAULT '0', PRIMARY KEY (`nbcommentid`));")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    
    // 30-Jul-2018
    if(!mysqli_query($dbc, "CREATE TABLE `local_software_guide` (`local_guideid` INT(11) NOT NULL AUTO_INCREMENT, `guideid` INT(11) NULL DEFAULT NULL, `additional_guide` TEXT NULL DEFAULT NULL, `deleted` SMALLINT(1) NOT NULL DEFAULT '0', PRIMARY KEY (`local_guideid`));")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    
    // 07-Aug-2018
    if(!mysqli_query($dbc, "ALTER TABLE `promotion` ADD `times_used` INT(11) NULL DEFAULT NULL AFTER `deleted`;")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    
    // 29-Aug-2018
    if(!mysqli_query($dbc, "ALTER TABLE `newsboard` DROP `newsboard_code`, DROP `heading`, DROP `name`, DROP `reminder_date`, DROP `fee`, DROP `quote_description`, DROP `invoice_description`, DROP `ticket_description`, DROP `final_retail_price`, DROP `admin_price`, DROP `wholesale_price`, DROP `commercial_price`, DROP `client_price`, DROP `minimum_billable`, DROP `estimated_hours`, DROP `actual_hours`, DROP `msrp`, DROP `unit_price`, DROP `unit_cost`, DROP `rent_price`, DROP `rental_days`, DROP `rental_weeks`, DROP `rental_months`, DROP `rental_years`, DROP `reminder_alert`, DROP `daily`, DROP `weekly`, DROP `monthly`, DROP `annually`, DROP `total_days`, DROP `total_hours`, DROP `total_km`, DROP `total_miles`, DROP `cost`;")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `newsboard` ADD `boardid` INT(11) NULL DEFAULT NULL AFTER `contactid`;")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "ALTER TABLE `newsboard` ADD `tags` TEXT NULL DEFAULT NULL AFTER `category`;")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    if(!mysqli_query($dbc, "CREATE TABLE `newsboard_boards` (`boardid` INT(11) NOT NULL AUTO_INCREMENT, `board_name` VARCHAR(250) NULL DEFAULT NULL, `shared_staff` TEXT NULL DEFAULT NULL, `deleted` TINYINT(1) NOT NULL DEFAULT '0', PRIMARY KEY (`boardid`));")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
    
    // 10-Sep-2018
    if(!mysqli_query($dbc, "ALTER TABLE `invoice` ADD `reference` VARCHAR(250) NULL DEFAULT NULL AFTER `patient_payment_receipt`;")) {
        echo "Error: ".mysqli_error($dbc)."<br />\n";
    }
	
    echo "Jay's DB Changes Done<br /><br />\n";
?>