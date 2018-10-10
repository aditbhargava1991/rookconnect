<?php
/* Update Databases */

    //Invennico's Database Changes
echo "Invennico's DB Changes Done<br />\n";


mysqli_query($dbc, "UPDATE `general_configuration` SET `value` = '0' WHERE `general_configuration`.`name` = 'summary_block_sort");
mysqli_query($dbc, "ALTER TABLE `field_config_equipment` ADD `flag_colours` VARCHAR(255) NULL AFTER `inspection_list`, ADD `flag_names` TEXT NULL AFTER `flag_colours`;");
mysqli_query($dbc, "ALTER TABLE `equipment` ADD `flag_label` TEXT NULL AFTER `equipment_image`, ADD `flag_colour` VARCHAR(6) NULL AFTER `flag_label`, ADD `flag_start` DATE NOT NULL AFTER `flag_colour`, ADD `flag_end` DATE NOT NULL AFTER `flag_start`");

    echo "Invennico's DB Changes Done<br />\n";
?>