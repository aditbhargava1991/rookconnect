<?php include_once('include.php');
$query = $dbc->query("SELECT * FROM `ticket_schedule` WHERE `ticketid`='50373'");
while($row = $query->fetch_assoc()) {
	print_r($row);
}
$query = $dbc->query("SELECT * FROM `contacts` WHERE `contactid`='1244'");
while($row = $query->fetch_assoc()) {
	print_r($row);
}