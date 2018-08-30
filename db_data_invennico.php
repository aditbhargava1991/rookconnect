<?php
/* Update Databases */

    //Invennico's Database Changes
    echo "Invennico's DB Changes:<br />\n";

    mysqli_query($dbc, "ALTER TABLE `project_path_milestone` ADD `intake_forms` TEXT NOT NULL AFTER `default_path`, ADD `check_list` TEXT NOT NULL AFTER `intake_form`;");
    
    echo "Invennico's DB Changes Done<br />\n";
?>