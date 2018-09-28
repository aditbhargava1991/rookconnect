<?php
/* Update Databases */

    //Invennico's Database Changes
echo "Invennico's DB Changes Done<br />\n";

mysqli_query($dbc, "UPDATE `general_configuration` SET `value` = '0' WHERE `general_configuration`.`name` = 'summary_block_sort");
 
    echo "Invennico's DB Changes Done<br />\n";
?>