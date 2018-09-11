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

if(!mysqli_query($dbc, "CREATE TABLE task_dashboard_mandatory SELECT * FROM task_dashboard LIMIT 0")) {
  echo "Error: ".mysqli_error($dbc)."<br />\n";
}

echo "<br> ======Jenish's db changes Done======<br>";
?>
