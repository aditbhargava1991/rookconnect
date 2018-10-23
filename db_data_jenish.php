<?php
/*
 * Jenish's DB changes
 */
echo "====== Jenish's db changes: ======\n";

/*if(!mysqli_query($dbc,"")) {
	echo "Error: ".mysqli_error($dbc)."\n";
}*/

/****************** Adding indexing for Ticket tables *********************/


if(!mysqli_query($dbc, "ALTER TABLE `field_config_contacts` ADD `mandatory` BOOLEAN DEFAULT 0")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "CREATE TABLE `field_config_mandatory_project` LIKE `field_config_project`")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "ALTER TABLE `general_configuration` ADD `mandatory` BOOLEAN DEFAULT 0")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "CREATE TABLE `field_config_mandatory_contacts` LIKE `field_config_contacts`")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "CREATE TABLE `db_backup_history` LIKE `checklist_history`")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "ALTER TABLE `email_communication` ADD `draft` BOOLEAN DEFAULT 0")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

/*if(!mysqli_query($dbc, "CREATE TABLE `email_communication_draft`
  ( `email_draft_number` INT(11) NOT NULL AUTO_INCREMENT , `from_email` TEXT NULL DEFAULT NULL ,
  `from_name` TEXT NULL DEFAULT NULL , `to_emails` TEXT NULL DEFAULT NULL ,
  `cc_emails` TEXT NULL DEFAULT NULL , `subject` TEXT NULL DEFAULT NULL ,
  `send_body` TEXT NULL DEFAULT NULL , `meeting_attachment` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`email_draft_number`))")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}*/

mysqli_query($dbc, "CREATE TABLE `field_jobs_history` (
  `history_id` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `before_change` text,
  `contactid` int(11) NOT NULL)"
);
if(!mysqli_query($dbc, "ALTER TABLE `field_jobs_history` CHANGE `history_id` `history_id` INT(11) NOT NULL AUTO_INCREMENT")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}




if(!mysqli_query($dbc, "CREATE TABLE `newsboard_tag` ( `newsboard_tagid` INT NOT NULL AUTO_INCREMENT ,
                        `boardid` INT(11) NOT NULL , `newsboardid` INT(11) NOT NULL ,
                        `staff` TEXT NOT NULL , PRIMARY KEY (`newsboard_tagid`))"))
{
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}


echo "<br> ======Jenish's db changes Done======<br>";
?>
