<?php
/* Update Databases */

    //Invennico's Database Changes
echo "Invennico's DB Changes Done<br />\n";

mysqli_query($dbc, "ALTER TABLE `exercise_config` ADD `deleted` INT(1) NOT NULL DEFAULT '0', ADD `date_of_archival` DATE NULL DEFAULT NULL");
mysqli_query($dbc, "ALTER TABLE `subtab_staff_config` ADD `deleted` INT(1) NOT NULL DEFAULT '0', ADD `date_of_archival` DATE NULL DEFAULT NULL");

mysqli_query($dbc2, "ALTER TABLE `exercise_config` ADD `deleted` INT(1) NOT NULL DEFAULT '0', ADD `date_of_archival` DATE NULL DEFAULT NULL");
mysqli_query($dbc2, "ALTER TABLE `subtab_staff_config` ADD `deleted` INT(1) NOT NULL DEFAULT '0', ADD `date_of_archival` DATE NULL DEFAULT NULL");

    echo "Invennico's DB Changes Done<br />\n";
?>