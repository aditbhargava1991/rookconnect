<?php
$projectid = $_GET['projectid'];
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));

$items = [];
$packages = array_filter(explode('**',$project['package']));
foreach($packages as $package) {
	$items[] = array_merge(['Package'],explode('#',$package));
}
$promotions = array_filter(explode('**',$project['promotion']));
foreach($promotions as $promotion) {
	$items[] = array_merge(['Promotion'],explode('#',$promotion));
}
$materials = array_filter(explode('**',$project['material']));
foreach($materials as $material) {
	$items[] = array_merge(['Material'],explode('#',$material));
}
$services = array_filter(explode('**',$project['services']));
foreach($services as $service) {
	$items[] = array_merge(['Service'],explode('#',$service));
}
$products = array_filter(explode('**',$project['products']));
foreach($products as $product) {
	$items[] = array_merge(['Product'],explode('#',$product));
}
$sreds = array_filter(explode('**',$project['sred']));
foreach($sreds as $sred) {
	$items[] = array_merge(['SR&ED'],explode('#',$sred));
}
$labours = array_filter(explode('**',$project['labour']));
foreach($labours as $labour) {
	$items[] = array_merge(['Labour'],explode('#',$labour));
}
$clients = array_filter(explode('**',$project['client']));
foreach($clients as $client) {
	$items[] = array_merge(['Client'],explode('#',$client));
}
$customers = array_filter(explode('**',$project['customer']));
foreach($customers as $customer) {
	$items[] = array_merge(['Customer'],explode('#',$customer));
}
$inventories = array_filter(explode('**',$project['inventory']));
foreach($inventories as $inventory) {
	$items[] = array_merge(['Inventory'],explode('#',$inventory));
}
$equipments = array_filter(explode('**',$project['equipment']));
foreach($equipments as $equipment) {
	$items[] = array_merge(['Equipment'],explode('#',$equipment));
}
$staffs = array_filter(explode('**',$project['staff']));
foreach($staffs as $staff) {
	$items[] = array_merge(['Staff'],explode('#',$staff));
}
$contractors = array_filter(explode('**',$project['contractor']));
foreach($contractors as $contractor) {
	$items[] = array_merge(['Contractor'],explode('#',$contractor));
}
$expenses = array_filter(explode('**',$project['expense']));
foreach($expenses as $expense) {
	$items[] = array_merge(['Expense'],explode('#',$expense));
}
$vendors = array_filter(explode('**',$project['vendor']));
foreach($vendors as $vendor) {
	$items[] = array_merge(['Vendor'],explode('#',$vendor));
}
$customs = array_filter(explode('**',$project['custom']));
foreach($customs as $custom) {
	$items[] = array_merge(['Custom'],explode('#',$custom));
}
$others = array_filter(explode('**',$project['other_detail']));
foreach($others as $other) {
	$items[] = array_merge(['Other'],explode('#',$other));
}
$purchase_orders = mysqli_query($dbc, "SELECT `posid`, `po_category`, `status`, `total_price` FROM `purchase_orders` WHERE `projectid`='$projectid' AND `deleted`=0");
while($po = mysqli_fetch_array($purchase_orders)) {
	$items[] = ['Purchase Order','PO #'.$po['posid'], implode(': ',array_filter([$po['po_category'],$po['status']])), $po['total_price']];
}
$tasks = mysqli_query($dbc, "SELECT `tasklistid`, `heading`, `work_time`, `hourly` FROM `tasklist` LEFT JOIN `staff_rate_table` ON CONCAT(',',`staff_rate_table`.`staff_id`,',') LIKE CONCAT('%,',`tasklist`.`created_by`,',%') AND `staff_rate_table`.`deleted`=0 AND DATE(NOW()) BETWEEN `staff_rate_table`.`start_date` AND IFNULL(NULLIF(`staff_rate_table`.`end_date`,'0000-00-00'),'9999-12-31') WHERE `tasklist`.`projectid`='$projectid' GROUP BY `tasklist`.`tasklistid`");
while($task = mysqli_fetch_array($tasks)) {
	$hours = explode(':',$task['work_time']);
	$hours = $hours[0] + ($hours[1] / 60) + ($hours[2] / 3600);
	$items[] = ['Task', 'Task #'.$task['tasklistid'],$task['heading']." (".round($hours,2)." hours)",round($hours * $task['hourly'],2)];
}
$tickets = mysqli_query($dbc, "SELECT `tickets`.`ticketid`, `tickets`.`heading`, `ticket_timer`.`start_time`, `ticket_timer`.`end_time`, `hourly` FROM `ticket_timer` LEFT JOIN `tickets` ON `ticket_timer`.`ticketid`=`tickets`.`ticketid` LEFT JOIN `staff_rate_table` ON CONCAT(',',`staff_rate_table`.`staff_id`,',') LIKE CONCAT('%,',`ticket_timer`.`created_by`,',%') AND `staff_rate_table`.`deleted`=0 AND DATE(NOW()) BETWEEN `staff_rate_table`.`start_date` AND IFNULL(NULLIF(`staff_rate_table`.`end_date`,'0000-00-00'),'9999-12-31') WHERE `tickets`.`projectid`='$projectid' AND `ticket_timer`.`deleted` = 0 GROUP BY `ticket_timer`.`tickettimerid` ORDER BY `tickettimerid`");
while($ticket = mysqli_fetch_array($tickets)) {
	$end = $ticket['end_time'];
	if(empty($end)) {
		$end = date('h:i A');
	}
	$hours = (strtotime($end) - strtotime($ticket['start_time'])) / 3600;
	$items[] = ['Ticket', 'Ticket #'.$ticket['ticketid'],$ticket['heading']." (".round($hours,2)." hours)",round($hours * $ticket['hourly'],2)];
}
$tasks = mysqli_query($dbc, "SELECT `time_id`, tasks.`project_milestone`, tasks.`heading`, timer.`work_time`, `hourly` FROM `tasklist_time` timer LEFT JOIN `tasklist` tasks ON timer.`tasklistid`=tasks.`tasklistid` LEFT JOIN `staff_rate_table` ON CONCAT(',',`staff_rate_table`.`staff_id`,',') LIKE CONCAT('%,',timer.`contactid`,',%') AND `staff_rate_table`.`deleted`=0 AND DATE(NOW()) BETWEEN `staff_rate_table`.`start_date` AND IFNULL(NULLIF(`staff_rate_table`.`end_date`,'0000-00-00'),'9999-12-31') WHERE tasks.`projectid`='$projectid' GROUP BY timer.`time_id` ORDER BY `time_id`");
while($task = mysqli_fetch_array($tasks)) {
	$hours = explode(':',$task['work_time']);
	$hours = $hours[0] + ($hours[1] / 60) + ($hours[2] / 3600);
	$items[] = ['Task', $task['project_milestone'].' Milestone',html_entity_decode($task['heading'])." (".round($hours,2)." hours)",round($hours * $task['hourly'],2)];
}

echo '<div id="no-more-tables"><table class="table table-bordered">';
echo '<tr class="hidden-xs hidden-sm">
	<th>Type</th>
	<th>Heading</th>
	<th>Description</th>
	<th>Cost</th>
	</tr>';

$total = 0;
foreach($items as $item) {
	$type = $item[0];
	$heading = '';
	$description = '';
	$price = 0;
	if($type == 'Other') {
		$description = $item[1];
		$price = $item[2];
	} else if($type == 'Expense') {
		$heading = $item[1];
		$description = $item[2];
		$price = $item[3];
	} else if($type == 'Labour') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `labour_type`, `heading` FROM `labour` WHERE `labourid`=".$item[1]));
		$heading = $result['labour_type'];
		$description = $result['heading'];
		$price = $item[2] * $item[3];
	} else if($type == 'Equipment') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, CONCAT(`unit_number`,' : ',`serial_number`) `description` FROM `equipment` WHERE `equipmentid`=".$item[1]));
		$heading = $result['category'];
		$description = $result['description'];
		$price = $item[2] * $item[3];
	} else if($type == 'Inventory') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, CONCAT(`unit_number`,' : ',`serial_number`) `description` FROM `inventory` WHERE `inventoryid`=".$item[1]));
		$heading = $result['category'];
		$description = $result['description'];
		$price = $item[2] * $item[3];
	} else if($type == 'Customer') {
		$description = get_contact($dbc, $item[1]);
		$price = $item[2];
	} else if($type == 'Vendor') {
		$description = get_contact($dbc, $item[1], 'name');
		$price = $item[2] * $item[3];
	} else if($type == 'Client') {
		$description = get_contact($dbc, $item[1], 'name');
		$price = $item[2];
	} else if($type == 'Contractor') {
		$description = get_contact($dbc, $item[1]);
		$price = $item[2] * $item[3];
	} else if($type == 'Staff') {
		$description = get_contact($dbc, $item[1]);
		$price = $item[2] * $item[3];
	} else if($type == 'SR&ED') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `sred` WHERE `sredid`=".$item[1]));
		$heading = $result['category'];
		$description = $result['heading'];
		$price = $item[2] * $item[3];
	} else if($type == 'Product') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `products` WHERE `productid`=".$item[1]));
		$heading = $result['category'];
		$description = $result['heading'];
		$price = $item[2] * $item[3];
	} else if($type == 'Service') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `services` WHERE `serviceid`=".$item[1]));
		$heading = $result['category'];
		$description = $result['heading'];
		$price = $item[2] * $item[3];
	} else if($type == 'Material') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `code`, `name` FROM `material` WHERE `materialid`=".$item[1]));
		$heading = $result['code'];
		$description = $result['name'];
		$price = $item[2] * $item[3];
	} else if($type == 'Custom') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `custom` WHERE `customid`=".$item[1]));
		$heading = $result['category'];
		$description = $result['heading'];
		$price = $item[2];
	} else if($type == 'Promotion') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `promotion` WHERE `promotionid`=".$item[1]));
		$heading = $result['category'];
		$description = $result['heading'];
		$price = $item[2];
	} else if($type == 'Package') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `package` WHERE `packageid`=".$item[1]));
		$heading = $result['category'];
		$description = $result['heading'];
		$price = $item[2];
	} else {
		$heading = $item[1];
		$description = $item[2];
		$price = $item[3];
	}
	$total += $price;
	echo '<td data-title="Type">'.$type.'</td>';
	echo '<td data-title="Heading">'.$heading.'</td>';
	echo '<td data-title="Description">'.$description.'</td>';
	echo '<td data-title="Cost">'.$price.'</td>';
	echo "</tr>";
}

echo '<td colspan="3">Total</td>';
echo '<td data-title="Estimate Price">'.$total.'</td>';
echo "</tr>";

echo '</table></div>';