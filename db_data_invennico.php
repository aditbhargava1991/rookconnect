<?php
/* Update Databases */

    //Invennico's Database Changes
echo "Invennico's DB Changes Done<br />\n";

mysqli_query($dbc, "ALTER TABLE `checklist`
ADD `flag_label` text COLLATE 'latin1_swedish_ci' NULL AFTER `flag_colour`,
ADD `flag_start` date NOT NULL DEFAULT '0000-00-00' AFTER `flag_label`,
ADD `flag_end` date NOT NULL DEFAULT '9999-12-31' AFTER `flag_start`;");


    echo "Invennico's DB Changes Done<br />\n";
?>