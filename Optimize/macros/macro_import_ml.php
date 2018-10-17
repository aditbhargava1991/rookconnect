<?php // CDS Best Buy Format Macro
include_once ('include.php');
error_reporting(0);

if(isset($_POST['upload_file']) && !empty($_FILES['csv_file']['tmp_name'])) {
	$default_status = get_config($dbc, 'ticket_default_status');
	$businessid = filter_var($_POST['businessid'],FILTER_SANITIZE_STRING);
	$default_services = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services_service_templates` WHERE `deleted`=0 AND `contactid`='$businessid'"))['serviceid'];
    $service_est_time = 0;
    foreach(explode(',',$default_services) as $serviceid) {
        $est_hours = explode(':',$dbc->query("SELECT `estimated_hours` FROM `services` WHERE `serviceid`='$serviceid'")->fetch_assoc()['estimated_hours']);
        $service_est_time += $est_hours[0] * 1 + $est_hours[1] / 60 + $est_hours[2] / 3600;
    }
    $increment_time = get_config($dbc, 'scheduling_increments');
    $increment_time = ($increment_time > 0 ? $increment_time * 60 : 1800);
    $warehouse_start_time = get_config($dbc, 'ticket_warehouse_start_time');
	$warehouses = [];
	$warehouse_assignments = explode('#*#', get_config($dbc, 'bb_macro_warehouse_assignments'));
	foreach($warehouse_assignments as $warehouse_assignment) {
		$warehouse_assignment = explode('|', $warehouse_assignment);
		$city = $warehouse_assignment[0];
		$warehouseid = $warehouse_assignment[1];
		if(!empty($city) && $warehouseid > 0) {
			$warehouse = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contactid`, `name`, `first_name`, `last_name`, `address`, `city`, `postal_code` FROM `contacts` WHERE `contactid` = '$warehouseid'"));
    		$warehouse_name = trim(decryptIt($warehouse['name']).(decryptIt($warehouse['name']) != '' && decryptIt($warehouse['first_name']).decryptIt($warehouse['last_name']) != '' ? ': ' : '').($warehouse['first_name']).' '.($warehouse['last_name']).' '.(empty($warehouse['display_name']) ? $warehouse['site_name'] : $warehouse['display_name']));
    		if($warehouse_name == '') {
    			$warehouse_name = 'warehouse';
    		}

			$warehouses[$city] = ['warehouseid' => $warehouseid, 'city' => $warehouse['city'], 'address' => $warehouse['address'], 'postal_code' => $warehouse['postal_code'], 'warehouse_name' => $warehouse_name];
		}
	}

	$file_name = $_FILES['csv_file']['tmp_name'];
	$delimiter = filter_var($_POST['delimiter'],FILTER_SANITIZE_STRING);
	$business = $dbc->query("SELECT * FROM `contacts` WHERE `contactid`='$businessid'")->fetch_assoc();
	$business_name = decryptIt($business['name']);
	$region = $business['region'];
	$classification = array_values(array_filter(explode(',',$business['classification'])))[0];
	$ticket_type = filter_var($_POST['ticket_type'],FILTER_SANITIZE_STRING);
	if($delimiter == 'comma') {
		$delimiter = ", ";
	} else {
		$delimiter = "\n";
	}
	$handle = fopen($file_name, 'r');
	$new_values = [];
	$headers = [];
    while(count(array_filter($headers)) < 10) {
        $headers = fgetcsv($handle, 2048, ",");
    }

	while (($csv = fgetcsv($handle, 2048, ",")) !== FALSE) {
        $values = [ 'ORDER' => trim($csv[0]), 'DATE' => trim($csv[1]), 'QTY' => trim($csv[2]), 'SKU' => trim($csv[3]), 'MODEL' => trim($csv[4]), 'APPLIANCE' => trim($csv[5]),
            'APP_PART' => trim($csv[6]), 'WEIGHT' => trim($csv[7]), 'VOLUME' => trim($csv[8]), 'CUST_NO' => trim($csv[9]), 'NAME' => trim($csv[10]), 'STREET' => trim($csv[11]),
            'UNIT' => trim($csv[12]), 'CITY' => trim($csv[13]), 'PROV' => trim($csv[14]), 'POSTAL' => trim($csv[15]), 'PHONE1' => trim($csv[16]), 'PHONE2' => trim($csv[17]),
            'PHONE3' => trim($csv[18]), 'EMAIL' => trim($csv[19]), 'DAYNOTE' => trim($csv[20]), 'STATUS' => trim($csv[21]) ];
		$new_values[$values['ORDER']]['invoice_number'] = $values['ORDER'];
		$new_values[$values['ORDER']]['date'] = date('Y-m-d',strtotime($values['DATE']));
		$new_values[$values['ORDER']]['description'] = ($new_values[$values['ORDER']]['description'] == '' ? implode(' ',array_filter([$values['QTY'],$values['APPLIANCE'],$values['APP_PART']])) : implode(', ',array_merge(explode(', ',$new_values[$values['ORDER']]['description']),[implode(' ',array_filter([$values['QTY'],$values['APPLIANCE'],$values['APP_PART'],$values['WEIGHT']]))])));
		$new_values[$values['ORDER']]['volume'] = ($new_values[$values['ORDER']]['volume'] == '' ? $values['VOLUME'] : implode(', ',array_merge(explode(', ',$new_values[$values['ORDER']]['volume']),[$values['VOLUME']])));
		$new_values[$values['ORDER']]['customer_name'] = $values['NAME'];
		$new_values[$values['ORDER']]['street_address'] = $values['STREET'];
		$new_values[$values['ORDER']]['unit_number'] = $values['UNIT'];
		$new_values[$values['ORDER']]['city'] = $values['CITY'];
		$new_values[$values['ORDER']]['province'] = $values['PROV'];
		$new_values[$values['ORDER']]['postal_code'] = $values['POSTAL'];
		$new_values[$values['ORDER']]['phone1'] = $values['PHONE1'];
		$new_values[$values['ORDER']]['phone2'] = implode(', ',array_filter([(trim($values['PHONE2'],'0- ') != '' ? $values['PHONE2'] : ''),(trim($values['PHONE3'],'0- ') != '' ? $values['PHONE3'] : '')]));
		$new_values[$values['ORDER']]['email'] = $values['EMAIL'];
		$new_values[$values['ORDER']]['info'] = $values['DAYNOTE'];
		$new_values[$values['ORDER']]['status'] = $values['STATUS'];
	}
	fclose($handle);

	if (!file_exists('output_ml')) {
		mkdir('output_ml', 0777, true);
	}
	$today_date = date('Y_m_d');
	$FileName = "output_ml/".file_safe_str("macro_miele_".$today_date.".csv",'output_ml/');
	$file = fopen($FileName, "w");
	$new_csv = ['Invoice Number', 'Date', 'Description', 'Gross Volume', 'Customer Name', 'Street Address', 'Unit Number', 'City', 'Province', 'Postal Code','Phone 1','Phone 2','Phone 3','Email','00022','Shipment Status'];
	$date = date('Y-m-d');
	foreach ($new_values as $key => $value) {
		if(!empty($value['date']) && !empty($value['customer_name']) && !empty($value['street_address']) && !empty($value['city']) && !empty($value['phone1']) && !empty(strip_tags(html_entity_decode($value['description'])))) {
			$existing = $dbc->query("SELECT * FROM `ticket_schedule` LEFT JOIN `tickets` ON `ticket_schedule`.`ticketid`=`tickets`.`ticketid` WHERE `tickets`.`businessid`='$businessid' AND CONCAT(`ticket_schedule`.`order_number`,'-') LIKE '$key-%' AND `ticket_schedule`.`to_do_date`='".$value['date']."' AND `ticket_schedule`.`client_name`='".$value['customer_name']."' AND `ticket_schedule`.`address`='".$value['street_address']."' AND `ticket_schedule`.`city`='".$value['city']."' AND `ticket_schedule`.`details`='".$value['phone1'].','.$value['phone2'].','.$value['phone3']."'");
			if($existing->num_rows == 0 || $_POST['duplicate'] == 'all_dupes') {
				$date = $value['date'];
				$dbc->query("INSERT INTO `tickets` (`ticket_type`,`businessid`,`region`,`classification`, `salesorderid`,`ticket_label`,`heading`) VALUES ('$ticket_type','$businessid','$region','$classification','$key".($existing->num_rows > 0 ? '-'.($existing->num_rows + 1) : '')."','$business_name - $key','$business_name - $key')");
				$ticketid = $dbc->insert_id;
				if(!empty($warehouses[$value['city']]) && !empty($value['city'])) {
					$dbc->query("INSERT INTO `ticket_schedule` (`ticketid`,`type`,`to_do_date`,`to_do_start_time`,`client_name`,`address`,`city`,`postal_code`,`order_number`,`status`) VALUES ('$ticketid','".$warehouses[$value['city']]['warehouse_name']."','".$value['date']."','".$warehouse_start_time."','".$business_name."','".$warehouses[$value['city']]['address']."','".$warehouses[$value['city']]['city']."','".$warehouses[$value['city']]['postal_code']."','".$key."','$default_status')");
				}
				$dbc->query("INSERT INTO `ticket_schedule` (`type`, `ticketid`,`to_do_date`,`client_name`,`address`,`city`,`province`,`postal_code`,`details`,`vendor`,`email`,`order_number`,`serviceid`,`est_time`,`map_link`,`volume`,`notes`,`status`) VALUES ('".get_config($dbc, 'delivery_type_default')."', '$ticketid','".$value['date']."','".$value['customer_name']."','".$value['street_address'].' '.$value['unit_number']."','".$value['city']."','".$value['province']."','".$value['postal_code']."','".$value['email']."','".$value['phone1']."','".$value['phone2']."','".$key."','$serviceid','$service_est_time','".'https://www.google.ca/maps/place/'.urlencode($value['street_address'].' '.$value['unit_number'].",".$value['city'].",".$value['province'].",".$value['postal_code'])."','".$value['volume']."','&lt;p&gt;".$value['description']."&lt;/p&gt;&lt;p&gt;".$value['info']."&lt;/p&gt;','$default_status')");
				$dbc->query("INSERT INTO `ticket_history` (`ticketid`,`userid`,`src`,`description`) VALUES ('$ticketid',".$_SESSION['contactid'].",'optimizer','Best Buy macro imported ".TICKET_NOUN." $ticketid')");
			}
		}
	}
	echo "<script>window.location.replace('?tab=assign&date=$date&region=$region&classification=$classification');</script>";
}
?>

<h1>Miele Import Macro</h1>

<form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
	<ol>
		<li>Upload your CSV file using the File Uploader.</li>
		<li>Specify whether to combine the SKU Descriptions using a comma or separated by new line. (NOTE: New line sometimes doesn't display properly depending on the CSV viewer you are using. If this doesn't work, use the comma separator).</li>
		<li>Select the business to which the deliveries will be attached.</li>
		<li>Press the Submit button to run the macro and import the <?= TICKET_TILE ?> into the software.</li>
		<br>
		<p>
			<label class="form-checkbox"><input type="radio" name="delimiter" value="newline">New Line</label>
			<label class="form-checkbox"><input type="radio" name="delimiter" value="comma" checked>Comma</label><br>
			<label class="form-checkbox"><input type="radio" name="duplicate" value="no_dupe">No Duplicates</label>
			<label class="form-checkbox"><input type="radio" name="duplicate" value="all_dupes" checked>Allow Duplicates</label><br>
			<select class="chosen-select-deselect" data-placeholder="Select <?= BUSINESS_CAT ?>" name="businessid"><option />
				<?php foreach(sort_contacts_query($dbc->query("SELECT `name`, `first_name`, `last_name`, `contactid` FROM `contacts` WHERE `category`='".BUSINESS_CAT."' AND `deleted`=0 AND `status` > 0 AND (`classification` IN ('".implode("','",$cur_bus)."') OR `contactid` IN ('".implode("','",$cur_bus)."') OR '' IN ('".implode("','",$cur_bus)."') OR 'ALL' IN ('".implode("','",$cur_bus)."'))")) as $business) { ?>
					<option value="<?= $business['contactid'] ?>"><?= $business['full_name'] ?></option>
				<?php } ?>
			</select>
			<input type="file" name="csv_file">
			<input type="hidden" name="ticket_type" value="<?= $cur_macro[1] ?>">
			<input type="submit" name="upload_file" value="Submit" class="btn brand-btn">
		</p>
	</ol>
</form>