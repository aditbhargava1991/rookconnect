<?php $guest_access = true;
error_reporting(0);
include(substr(dirname(__FILE__), 0, -8).'include.php');
ob_clean();

// Auto Archive Unbooked Tickets
$auto_archive_day = get_config($dbc, 'auto_archive_unbooked_day');
$auto_archive_time = get_config($dbc, 'auto_archive_unbooked_time');
if((date('l') === $auto_archive_day || date('j') === $auto_archive_day) && $auto_archive_time === date('H')) {
    echo "Archiving Unbooked ".TICKET_TILE."...\n";
    $dbc->query("INSERT INTO `ticket_history` (`ticketid`,`src`,`description`) SELECT `ticketid`,'Auto Archive Unbooked',CONCAT('Auto Archived Delivery id ',`id`) FROM `ticket_schedule` WHERE (((`contactid` IS NULL OR `contactid`=0 OR `contactid`='') AND (`equipmentid` IS NULL OR `equipmentid`=0 OR `equipmentid`='')) OR `to_do_date`='' OR `to_do_date` IS NULL OR `to_do_date`='0000-00-00') AND `deleted`=0 AND `status` NOT IN ('Archived','Archive') AND `type` NOT IN ('Origin','Destination')");
    $dbc->query("UPDATE `ticket_schedule` SET `deleted`=1, `status`='Archived' WHERE (((`contactid` IS NULL OR `contactid`=0 OR `contactid`='') AND (`equipmentid` IS NULL OR `equipmentid`=0 OR `equipmentid`='')) OR `to_do_date`='' OR `to_do_date` IS NULL OR `to_do_date`='0000-00-00') AND `deleted`=0 AND `status` NOT IN ('Archived','Archive') AND `type` NOT IN ('Origin','Destination')");
    if($dbc->affected_rows > 0) {
        echo "Archived ".$dbc->affected_rows." ".TICKET_NOUN." Deliveries\n";
    }
    $dbc->query("INSERT INTO `ticket_history` (`ticketid`,`src`,`description`) SELECT `ticketid`,'Auto Archive Unbooked',CONCAT('Auto Archived ".TICKET_NOUN."# ',`ticketid`) FROM `tickets` WHERE ((`contactid` IS NULL OR `contactid`=0 OR `contactid`='') AND (`equipmentid` IS NULL OR `equipmentid`=0 OR `equipmentid`='') OR `to_do_date`='' OR `to_do_date` IS NULL OR `to_do_date`='0000-00-00') AND `deleted`=0 AND `status` NOT IN ('Archived','Archive') AND `ticketid` NOT IN (SELECT `ticketid` FROM `ticket_schedule` WHERE `deleted`=0)");
    $dbc->query("UPDATE `tickets` SET `deleted`=1, `status`='Archived' WHERE ((`contactid` IS NULL OR `contactid`=0 OR `contactid`='') AND (`equipmentid` IS NULL OR `equipmentid`=0 OR `equipmentid`='') OR `to_do_date`='' OR `to_do_date` IS NULL OR `to_do_date`='0000-00-00') AND `deleted`=0 AND `status` NOT IN ('Archived','Archive') AND `ticketid` NOT IN (SELECT `ticketid` FROM `ticket_schedule` WHERE `deleted`=0)");
    if($dbc->affected_rows > 0) {
        echo "Archived ".$dbc->affected_rows." ".TICKET_TILE."\n";
    }
}