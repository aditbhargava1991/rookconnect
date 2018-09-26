<?php
/*
 * Jenish's DB changes
 */
echo "====== Jenish's db changes: ======\n";

/*if(!mysqli_query($dbc,"")) {
	echo "Error: ".mysqli_error($dbc)."\n";
}*/

/****************** Adding indexing for Ticket tables *********************/

if(!mysqli_query($dbc, "ALTER TABLE `security_privileges_log` ADD `type` TEXT(50) DEFAULT NULL")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}


if(!mysqli_query($dbc, "CREATE TABLE subtab_staff_config SELECT * FROM subtab_config LIMIT 0")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "CREATE TABLE security_privileges_staff SELECT * FROM security_privileges LIMIT 0")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "ALTER TABLE `security_privileges_staff` ADD `staff` INT(15)")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "ALTER TABLE `security_privileges_staff` ADD PRIMARY KEY(`privilegesid`)")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "ALTER TABLE `field_config_contacts` ADD `mandatory` BOOLEAN DEFAULT 0")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "ALTER TABLE `general_configuration` ADD `mandatory` BOOLEAN DEFAULT 0")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

if(!mysqli_query($dbc, "CREATE TABLE field_mandatory_config AS SELECT * FROM field_config WHERE 1=0;")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}



echo "<br> ======Jenish's db changes Done======<br>";
?>
