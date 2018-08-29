<?php
/* Update Databases */

    //Invennico's Database Changes
    echo "Invennico's DB Changes:<br />\n";

    mysqli_query($dbc, "ALTER TABLE `field_config_expense` ADD `expense_mode` ENUM('tables','inbox') NOT NULL DEFAULT 'inbox' AFTER `expense`");
    
    mysqli_query($dbc, "ALTER TABLE `expense` ADD `approved_by` INT(11) NOT NULL AFTER `status`");
    
    mysqli_query($dbc, "CREATE TABLE `expense_approval_levels` ( `expense_approval_id` INT(11) NOT NULL AUTO_INCREMENT ,  `expense_approval_role_id` INT(11) NOT NULL ,  `expense_role_active` ENUM('0','1') NOT NULL DEFAULT '1' COMMENT '0=not active, 1 = active' ,  `expense_role_sorting` INT(11) NOT NULL ,    PRIMARY KEY  (`expense_approval_id`))");

    mysqli_query($dbc, "INSERT INTO `general_configuration` (`configid`, `name`, `value`, `calllog_schedule_status`) VALUES (NULL, 'desktop_notification_enabled', NULL, NULL");
    
    mysqli_query($dbc, "ALTER TABLE `contacts` ADD `desktop_notification` ENUM('0','1') NOT NULL DEFAULT '1' COMMENT '0=disable,1=enable' AFTER `main_siteid`");
    
    echo "Invennico's DB Changes Done<br />\n";
?>