<?php
/* Update Databases */

    //Invennico's Database Changes
echo "Invennico's DB Changes Done<br />\n";

/*mysqli_query($dbc, "INSERT INTO `general_configuration` (`configid`, `name`, `value`, `calllog_schedule_status`) VALUES (NULL, 'summary_block_sort', '0', NULL)");*/

#For 'Manage Sub-Tabs'#
mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`, `value`, `calllog_schedule_status`)
VALUES ('sales_sub_tabs_order', '[{&#34;title&#34;:&#34;Tile Settings&#34;,&#34;segment&#34;:&#34;tile&#34;},{&#34;title&#34;:&#34;Fields&#34;,&#34;segment&#34;:&#34;fields&#34;},{&#34;title&#34;:&#34;Dashboards&#34;,&#34;segment&#34;:&#34;dashboards&#34;},{&#34;title&#34;:&#34;Quick Action Icons&#34;,&#34;segment&#34;:&#34;actions&#34;},{&#34;title&#34;:&#34;Accordion&#34;,&#34;segment&#34;:&#34;accordion&#34;},{&#34;title&#34;:&#34;Lead Source&#34;,&#34;segment&#34;:&#34;lead_source&#34;},{&#34;title&#34;:&#34;Next Action&#34;,&#34;segment&#34;:&#34;next_action&#34;},{&#34;title&#34;:&#34;Lead Status&#34;,&#34;segment&#34;:&#34;lead_status&#34;},{&#34;title&#34;:&#34;Auto Archive&#34;,&#34;segment&#34;:&#34;auto_archive&#34;},{&#34;title&#34;:&#34;Sales Lead&#34;,&#34;segment&#34;:&#34;sales_lead&#34;},{&#34;title&#34;:&#34;Manage Sub-Tabs&#34;,&#34;segment&#34;:&#34;manage_sub_tabs&#34;}]', NULL)");
 
 #For 'Sales Lead'#
 mysqli_query($dbc, "ALTER TABLE `contacts`
ADD `sales_lead_is_active` tinyint(2) NULL DEFAULT '1' COMMENT '1 : Active, 0 : Inactive' AFTER `category`");

    echo "Invennico's DB Changes Done<br />\n";





?>