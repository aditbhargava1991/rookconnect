<?php
/* Update Databases */

    //Invennico's Database Changes
echo "Invennico's DB Changes Done<br />\n";

mysqli_query($dbc, "CREATE TABLE `ticket_time` ( `time_id` INT(11) NOT NULL AUTO_INCREMENT ,  `ticketid` INT(11) NULL ,  `work_time` TIME NULL DEFAULT '00:00:00' ,  `src` VARCHAR(1) NOT NULL ,  `contactid` INT(11) NULL ,  `timer_date` DATE NULL ,    PRIMARY KEY  (`time_id`)) ENGINE = InnoDB");
mysqli_query($dbc,"ALTER TABLE `ticket_time`  ADD `start_time` TIME NOT NULL  AFTER `ticketid`,  ADD `end_time` TIME NOT NULL  AFTER `start_time`");    
mysqli_query($dbc,"ALTER TABLE `tickets`  ADD `work_time` TIME NULL DEFAULT '00:00:00'  AFTER `guardianid`");

echo "Invennico's DB Changes Done<br />\n";
?>