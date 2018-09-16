<?php
/*
 * Jenish's DB changes
 */
echo "====== Jenish's db changes: ======\n";

/*if(!mysqli_query($dbc,"")) {
	echo "Error: ".mysqli_error($dbc)."\n";
}*/

/****************** Adding indexing for Ticket tables *********************/

if(!mysqli_query($dbc, "CREATE TABLE security_privileges_staff SELECT * FROM security_privileges LIMIT 0")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "ALTER TABLE `security_privileges_staff` ADD `staff` INT(15)")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "ALTER TABLE `security_privileges_staff` ADD PRIMARY KEY(`privilegesid`)")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "ALTER TABLE `security_privileges_staff` CHANGE `privilegesid` `privilegesid` INT(10) NOT NULL AUTO_INCREMENT") {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "ALTER TABLE `field_config_contacts` ADD `mandatory` BOOLEAN DEFAULT 0")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "CREATE TABLE task_dashboard_mandatory SELECT * FROM task_dashboard LIMIT 0")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "ALTER TABLE `field_config_equipment` ADD `mandatory` BOOLEAN DEFAULT 0")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}
echo "<br> ======Jenish's db changes Done======<br>";
?>
