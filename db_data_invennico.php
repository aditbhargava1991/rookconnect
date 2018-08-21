<?php
/* Update Databases */

    //Invennico's Database Changes
    echo "Invennico's DB Changes:<br />\n";

    mysqli_query($dbc, "ALTER TABLE `field_config_expense` ADD `expense_mode` ENUM('tables','inbox') NOT NULL DEFAULT 'inbox' AFTER `expense`");

    echo "Invennico's DB Changes Done<br />\n";
?>