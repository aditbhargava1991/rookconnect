<?php
$id = $_GET['id'];
if(is_numeric($id)): 
	$sql = "SELECT CONCAT('Unit: ',e.`unit_number`,', VIN:',`vin_number`,', ',e.`make`,' ',e.`model`) equipment, r.`history` FROM `company_rate_card` r LEFT JOIN `equipment` e on r.`item_id` = e.`equipmentid` WHERE `companyrcid`=$id";
	$result = mysqli_fetch_array(mysqli_query($dbc, $sql));
	echo ($result['equipment'] == '' ? '' : "<h2>Equipment: {$result['equipment']}</h2>");
	echo "<p>".($result['history'] == '' ? 'No changes to display.' : $result['history'])."</p>";
endif;