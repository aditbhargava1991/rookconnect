<?php include('../include.php');
$region = filter_var(($_GET['region'] != '' ? $_GET['region'] : ''),FILTER_SANITIZE_STRING);
$location = filter_var(($_GET['location'] != '' ? $_GET['location'] : ''),FILTER_SANITIZE_STRING);
$classification = filter_var(($_GET['classification'] != '' ? $_GET['classification'] : ''),FILTER_SANITIZE_STRING);
$date = filter_var(($_GET['date'] != '' ? $_GET['date'] : date('Y-m-d')),FILTER_SANITIZE_STRING);

$equipment_list = $dbc->query("SELECT `equipment`.`equipmentid`, CONCAT(`equipment`.`category`,': ',`equipment`.`make`,' ',`equipment`.`model`,' ',`equipment`.`unit_number`) `label`, SUM(IF(`schedule`.`to_do_date`='$date',1,0)) `assigned` FROM `equipment` LEFT JOIN (SELECT IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` WHERE `tickets`.`deleted`=0 AND IFNULL(`ticket_schedule`.`deleted`,0)=0) `schedule` ON `equipment`.`equipmentid`=`schedule`.`equipmentid` WHERE '$region' IN (`equipment`.`region`,'') AND '$location' IN (`equipment`.`location`,'') AND '$classification' IN (`classification`,'') AND `equipment`.`deleted`=0 GROUP BY `equipment`.`equipmentid` ORDER BY `equipment`.`make`, `equipment`.`model`, `equipment`.`unit_number`");
echo '<h3>Equipment</h3>';
if($equipment_list->num_rows > 0) {
	while($equip = $equipment_list->fetch_assoc()) { ?>
		<div data-id="<?= $equip['equipmentid'] ?>" class="block-item equipment">
			<h4><?= $equip['label'] ?></h4>
			<?= $equip['assigned'].' '.TICKET_TILE ?>
		</div>
	<?php }
} else {
	echo '<h4>No Equipment Found</h4>';
}