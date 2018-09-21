<?php $guest_access = true;
include(substr(dirname(__FILE__), 0, -8).'include.php');
ob_clean();

$today_date = date('Y-m-d');

$rate_types = [
	'position' => [
		'table' => 'position_rate_table',
		'tile_name' => '',
		'config_table' => 'position_rate_fields',
		'idfield' => 'rate_id'
	],
	'staff' => [
		'table' => 'staff_rate_table',
		'tile_name' => '',
		'config_table' => 'staff_rate_fields',
		'idfield' => 'rate_id'
	],
	'equipment' => [
		'table' => 'equipment_rate_table',
		'tile_name' => '',
		'config_table' => 'equipment_rate_fields',
		'idfield' => 'rate_id'
	],
	'category' => [
		'table' => 'category_rate_table',
		'tile_name' => '',
		'config_table' => 'category_rate_fields',
		'idfield' => 'rate_id'
	],
	'labour' => [
		'table' => 'tile_rate_card',
		'tile_name' => 'labour',
		'config_table' => 'labour_rate_fields',
		'idfield' => 'ratecardid'
	],
	'service' => [
		'table' => 'service_rate_card',
		'tile_name' => '',
		'config_table' => 'services_rate_fields',
		'idfield' => 'serviceratecardid'
	],
	'company' => [
		'table' => 'company_rate_card',
		'tile_name' => '',
		'config_table' => 'company_rate_fields',
		'idfield' => 'companyrcid'
	],
	'customer' => [
		'table' => 'rate_card',
		'tile_name' => '',
		'config_table' => '',
		'idfield' => 'ratecardid'
	]
];

foreach($rate_types as $key => $rate_type) {
	if($key == 'customer') {
		$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_ratecard`"));
		$field_config = $get_field_config['config_fields'];
	} else {
		$field_config = get_config($dbc, $rate_type['config_table']);
	}
	$reminders = mysqli_query($dbc, "SELECT * FROM `{$rate_type['table']}` WHERE `deleted` = 0 AND `alert_date` = '$today_date'".(!empty($rate_type['tile_name']) ? " AND `tile_name` = '".$rate_type['tile_name']."'" : ''));
	while($row = mysqli_fetch_assoc($reminders)) {
		switch($key) {
			case 'position':
				$position = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `positions` WHERE `position_id` = '".$row['position_id']."'"));
				$subject = "Position Rate Card reminder - ".$position['name'];
				$body = 'This is a reminder about the Position Rate Card for '.$position['name'].' that needs to be followed up with. Click <a href="'.WEBSITE_URL.'"/Rate Card/rate_card.php?card=position&status=add&id='.$row[$rate_type['idfield']].'">HERE</a> to go to the Rate Card.';
				break;
			case 'staff':
				$staff_list = [];
				foreach(explode(',',$row['staff_id']) as $staffid) {
					if($staffid > 0) {
						$staff_list[] = get_contact($dbc, $staffid);
					}
				}
				$subject = "Staff Rate Card reminder - ".implode(', ',$staff_list).(!empty($row['category']) ? ' - '.$row['category'] : '');
				$body = 'This is a reminder about the Staff Rate Card for '.implode(', ',$staff_list).' that needs to be followed up with. Click <a href="'.WEBSITE_URL.'"/Rate Card/rate_card.php?card=staff&status=add&id='.$row[$rate_type['idfield']].'">HERE</a> to go to the Rate Card.';
				break;
			case 'equipment':
				$equipment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT *, IFNULL(CONCAT(`make`,' ',`model`,', Unit: ',`unit_number`,', VIN:',`vin_number`),'N/A') equipment_label FROM `equipment` WHERE `equipmentid` = '".$row['equipment_id']."'"));
				$subject = "Equipment Rate Card reminder - ".$equipment['equipment_label'];
				$body = 'This is a reminder about the Equipment Rate Card for '.$equipment['equipment_label'].' that needs to be followed up with. Click <a href="'.WEBSITE_URL.'"/Rate Card/rate_card.php?card=equipment&status=add&id='.$row[$rate_type['idfield']].'">HERE</a> to go to the Rate Card.';
				break;
			case 'category':
				$subject = "Equipment Category Rate Card reminder - ".$row['category'];
				$body = 'This is a reminder about the Equipment Category Rate Card for '.$row['category'].' that needs to be followed up with. Click <a href="'.WEBSITE_URL.'"/Rate Card/rate_card.php?card=category&status=add&id='.$row[$rate_type['idfield']].'">HERE</a> to go to the Rate Card.';
				break;
			case 'labour':
				$labour = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `labour` WHERE `labourid` = '".$row['src_id']."'"));
				$subject = "Labour Rate Card reminder - ".$labour['heading'];
				$body = 'This is a reminder about the Labour Rate Card for '.$labour['heading'].' that needs to be followed up with. Click <a href="'.WEBSITE_URL.'"/Rate Card/rate_card.php?card=labour&status=add&id='.$row['ratecardid'].'">HERE</a> to go to the Rate Card.';
				break;
			case 'service':
				$service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid` = '".$row['serviceid']."'"));
				$subject = "Service Rate Card reminder - ".$service['heading'];
				$body = 'This is a reminder about the Service Rate Card for '.$service['heading'].' that needs to be followed up with. Click <a href="'.WEBSITE_URL.'"/Rate Card/rate_card.php?card=services&status=add&id='.$row['ratecardid'].'">HERE</a> to go to the Rate Card.';
				break;
			case 'company':
				$subject = "Company Rate Card reminder - ".$row['rate_card_name'];
				$body = 'This is a reminder about the Company Rate Card - '.$row['rate_card_name'].' that needs to be followed up with. Click <a href="'.WEBSITE_URL.'"/Rate Card/rate_card.php?card=company&status=add&id='.$row['companyrcid'].'">HERE</a> to go to the Rate Card.';
				break;
			case 'customer':
				$subject = "Customer Rate Card reminder - ".$row['rate_card_name'];
				$body = 'This is a reminder about the Customer Rate Card - '.$row['rate_card_name'].' that needs to be followed up with. Click <a href="'.WEBSITE_URL.'"/Rate Card/rate_card.php?card=customer&status=add&ratecardid='.$row['companyrcid'].'">HERE</a> to go to the Rate Card.';
				break;
		}
		foreach(explode(',',$row['alert_staff']) as $staffid) {
			if($staffid > 0) {
				if($key == 'company') {
					$reminder_exists = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) numrows FROM `reminders` WHERE `contactid` = '$staffid' AND `reminder_date` = '$today_date' AND `reminder_type` = 'Rate Card' AND `src_table` = '".$rate_type['table']."' AND `src_tableid` IN (SELECT `companyrcid` FROM `company_rate_card` WHERE `rate_card_name` = '".$row['rate_card_name']."' AND `deleted` = 0)"))['numrows'];
				} else {
					$reminder_exists = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) numrows FROM `reminders` WHERE `contactid` = '$staffid' AND `reminder_date` = '$today_date' AND `reminder_type` = 'Rate Card' AND `src_table` = '".$rate_type['table']."' AND `src_tableid` = '".$row[$rate_type['idfield']]."'"))['numrows'];
				}
				if(empty($reminder_exists)) {
					mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_type`, `subject`, `body`, `src_table`, `src_tableid`) VALUES ('$staffid', '$today_date', 'Rate Card', '$subject', '".htmlentities($body)."', '".$rate_type['table']."', '".$row[$rate_type['idfield']]."')");

					if(strpos(','.$field_config.',', ',email_alerts,') !== FALSE) {
						$email = get_email($dbc, $staffid);
						if(!empty($email)) {
							send_email('', $email, '', '', $subject, $body, '');
						}
					}
				}
			}
		}
	}
}
