<?php include('../include.php');
$date = filter_var($_GET['date'],FILTER_SANITIZE_STRING);
$equipment = filter_var($_GET['equipment'],FILTER_SANITIZE_STRING);
$warehouse = "SELECT IFNULL(`ticket_schedule`.`address`, `tickets`.`address`) `address`, IFNULL(`ticket_schedule`.`city`, `tickets`.`city`) `city`, IFNULL(`ticket_schedule`.`postal_code`, `tickets`.`postal_code`) `postal_code` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE `tickets`.`deleted`=0 AND ((`tickets`.`to_do_date` = '".$date."' OR '".$date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR `ticket_schedule`.`to_do_date`='".$date."' OR '".$date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) IN ('$equipment') AND IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) > 0)) AND (REPLACE(REPLACE(IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''),IFNULL(`tickets`.`city`,''))),' ',''),'-','') IN (SELECT REPLACE(REPLACE(CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')),' ',''),'-','') FROM `contacts` WHERE `category`='Warehouses') OR `ticket_schedule`.`type`='warehouse') ORDER BY `ticket_schedule`.`to_do_date`,`ticket_schedule`.`to_do_start_time`";
$address = implode(', ',array_filter($dbc->query($warehouse)->fetch_assoc())); ?>
<script src="map_sorting.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= DIRECTIONS_KEY ?>"></script>
<div class="form-horizontal triple-padded">
    <a href="../blank_loading_page.php" class="pull-right"><img class="inline-img" src="../img/icons/cancel.png"></a>
    <h3><?= implode(': ',array_filter($dbc->query("SELECT `category`,`make`,`model`,`unit_number`,`label` FROM `equipment` WHERE `equipmentid`='$equipment'")->fetch_assoc())) ?></h3>
    <em>Clicking Submit will use the addresses below as the start and end address for the day, and will re-arrange the scheduled work orders in an attempt optimize the order of deliveries to minimize the driving time for this equipment.</em>
	<div class="form-group">
		<label class="col-sm-4">Starting Address:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="origin" value="<?= $address ?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4">Ending Address:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="destination" value="<?= $address ?>">
		</div>
	</div>
	<button class="btn brand-btn confirm_btn pull-right" onclick="sort_by_map('<?= $date ?>','<?= $equipment ?>',$('[name=origin]').val(),$('[name=destination]').val());">Submit</button>
	<button class="btn brand-btn pull-right" onclick="window.location.reload();">Cancel Sorting</button>
</div>