<?php
/* Update Databases */

    //Invennico's Database Changes
echo "Invennico's DB Changes Done<br />\n";

mysqli_query($dbc, "INSERT INTO `general_configuration` (`configid`, `name`, `value`, `calllog_schedule_status`) VALUES (NULL, 'summary_block_sort', '0', NULL)");

    echo "Invennico's DB Changes Done<br />\n";
?>