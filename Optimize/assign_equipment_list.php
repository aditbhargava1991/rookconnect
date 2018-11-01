<?php include('../include.php');
$region = filter_var(($_GET['region'] != '' ? $_GET['region'] : '%'),FILTER_SANITIZE_STRING);
$location = filter_var(($_GET['location'] != '' ? $_GET['location'] : '%'),FILTER_SANITIZE_STRING);
$classification = filter_var(($_GET['classification'] != '' ? $_GET['classification'] : '%'),FILTER_SANITIZE_STRING);
$date = filter_var(($_GET['date'] != '' ? $_GET['date'] : date('Y-m-d')),FILTER_SANITIZE_STRING);
$all_equip = $_GET['staff_only'] > 0 ? false : true;

echo '<h3>Equipment</h3>';
$region_filter = [];
foreach(array_filter(explode(',',$region)) as $region) {
	$region_filter[] = "CONCAT('*#*',IFNULL(`equipment`.`region`,''),'*#*') LIKE '%*#*$region*#*%'";
}
$location_filter = [];
foreach(array_filter(explode(',',$location)) as $location) {
	$location_filter[] = "CONCAT('*#*',IFNULL(`equipment`.`location`,''),'*#*') LIKE '%*#*$location*#*%'";
}
$class_filter = [];
foreach(array_filter(explode(',',$classification)) as $classification) {
	$class_filter[] = "CONCAT('*#*',IFNULL(`equipment`.`classification`,''),'*#*') LIKE '%*#*$classification*#*%'";
}
$warehouse_query = '';
if(get_config($dbc, 'optimize_dont_count_warehouse') == 1) {
    $warehouse_query = " AND REPLACE(REPLACE(IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''),IFNULL(`tickets`.`city`,''))),' ',''),'-','') NOT IN (SELECT REPLACE(REPLACE(CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')),' ',''),'-','') FROM `contacts` WHERE `category`='Warehouses')";
}
$equipment_list = $dbc->query("SELECT `equipment`.`equipmentid`, CONCAT(`equipment`.`category`,': ',`equipment`.`make`,' ',`equipment`.`model`,' ',`equipment`.`unit_number`) `label`, SUM(IF(`schedule`.`to_do_date`='$date',1,0)) `assigned` FROM `equipment` LEFT JOIN (SELECT IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` WHERE `tickets`.`deleted`=0 $warehouse_query AND IFNULL(`ticket_schedule`.`deleted`,0)=0) `schedule` ON `equipment`.`equipmentid`=`schedule`.`equipmentid` WHERE ".(count($region_filter) > 0 ? '('.implode(' OR ',$region_filter).') AND' : '')." ".(count($location_filter) > 0 ? '('.implode(' OR ',$location_filter).') AND' : '')." ".(count($class_filter) > 0 ? '('.implode(' OR ',$class_filter).') AND' : '')." `equipment`.`deleted`=0 GROUP BY `equipment`.`equipmentid` ORDER BY `equipment`.`make`, `equipment`.`model`, `equipment`.`unit_number`");
if($equipment_list->num_rows > 0) { ?>
    <script>
    $(document).ready(function() {
        calcEquipListWidth();
    });
    function calcEquipListWidth() {
        $('#equip_list_scroll').height($('.equip_list').height() - $('.equip_list h3').outerHeight(true));
    }
    </script>
    <div style="overflow-y:auto;padding:0 0.5em;" id="equip_list_scroll">
	<?php while($equip = $equipment_list->fetch_assoc()) {
        $equip_assign = mysqli_fetch_array(mysqli_query($dbc, "SELECT ea.*, e.*, ea.`notes`, ea.`classification` FROM `equipment_assignment` ea LEFT JOIN `equipment` e ON ea.`equipmentid` = e.`equipmentid` WHERE e.`equipmentid` = '".$equip['equipmentid']."' AND ea.`deleted` = 0 AND DATE(`start_date`) <= '$date' AND DATE(ea.`end_date`) >= '$date' AND CONCAT(',',ea.`hide_days`,',') NOT LIKE '%,$date,%' ORDER BY ea.`start_date` DESC, ea.`end_date` ASC, e.`category`, e.`unit_number`"));
        $team_count = 0;
        if(!empty($equip_assign)) {
            $hide_staff = explode(',',$equip_assign['hide_staff']);
            $team_name = '';
            // Get Names of Assigned Team Members
            $team_contacts = sort_contacts_query(mysqli_query($dbc, "SELECT `teams_staff`.`id`,`teams_staff`.`contactid`,`teams_staff`.`contact_position`,`contacts`.`name`,`contacts`.`last_name`,`contacts`.`first_name`,`contacts`.`category` FROM `teams_staff` LEFT JOIN `contacts` ON `teams_staff`.`contactid`=`contacts`.`contactid` WHERE `teams_staff`.`teamid` ='".$equip_assign['teamid']."' AND `teams_staff`.`contactid` NOT IN ('".implode("','",$hide_staff)."') AND `teams_staff`.`deleted` = 0 AND `teams_staff`.`contactid` > 0"),'id');
            foreach($team_contacts as $team_contact) {
                $team_count++;
                $team_name .= $team_contact['category'].': '.(!empty($team_contact['contact_position']) ? $team_contact['contact_position'].': ' : '').($team_contact['category'] == 'Staff' ? $team_contact['person_name'] : $team_contact['full_name']).'<br />';
            }
            // Get Names of Assigned Staff / Contractors
            $assigned_contacts = sort_contacts_query(mysqli_query($dbc, "SELECT `ea_staff`.`id`,`ea_staff`.`contactid`,`ea_staff`.`contact_position`,`contacts`.`name`,`contacts`.`last_name`,`contacts`.`first_name`,`contacts`.`category` FROM `equipment_assignment_staff` `ea_staff` LEFT JOIN `contacts` ON `ea_staff`.`contactid`=`contacts`.`contactid` WHERE `ea_staff`.`equipment_assignmentid` ='".$equip_assign['equipment_assignmentid']."' AND `ea_staff`.`contactid` NOT IN ('".implode("','",$hide_staff)."') AND `ea_staff`.`deleted` = 0 AND `ea_staff`.`contactid` > 0"),'id');
            foreach($assigned_contacts as $assign_contact) {
                $team_count++;
                $team_name .= $assign_contact['category'].': '.(!empty($assign_contact['contact_position']) ? $assign_contact['contact_position'].': ' : '').($assign_contact['category'] == 'Staff' ? $assign_contact['person_name'] : $assign_contact['full_name']).'<br />';
            }
        } else {
            $team_name = '(No Team Assigned)<br />';
        }
        if($team_count > 0 || $all_equip) { ?>
            <div data-id="<?= $equip['equipmentid'] ?>" class="block-item equipment">
                <h4><?= $equip['label'] ?></h4>
                <?= $team_name ?>
                <?= $equip['assigned'].' '.TICKET_TILE ?>
                <?php if($_GET['sorticons'] == 'true') { ?>
                    <img src="../img/sort-icon.png" class="pull-right inline-img" onclick="get_addresses('<?= $date ?>', '<?= $equip['equipmentid'] ?>'); return false;">
                <?php } ?>
            </div>
        <?php }
    }
    echo '</div>';
} else {
	echo '<h4>No Equipment Found</h4>';
}