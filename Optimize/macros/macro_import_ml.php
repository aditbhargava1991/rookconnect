<?php // CDS Best Buy Format Macro
include_once ('include.php');
error_reporting(0);

if(isset($_POST['upload_file']) && !empty($_FILES['csv_file']['tmp_name'])) {
	$default_status = get_config($dbc, 'ticket_default_status');
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
	$businessid = filter_var($_POST['businessid'],FILTER_SANITIZE_STRING);
	$business = $dbc->query("SELECT * FROM `contacts` WHERE `contactid`='$businessid'")->fetch_assoc();
	$business_name = decryptIt($business['name']);
	$region = $business['region'];
	$classification = $business['classification'];
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
		$num = count($csv);
		$values = [];
		for($i = 0; $i < $num; $i++) {
			$values[$headers[$i]] = trim($csv[$i]);
		}
		$new_values[$values['Order-No.']]['invoice_number'] = $values['Order-No.'];
		$new_values[$values['Order-No.']]['date'] = date('Y-m-d',strtotime($values['Delivery Date']));
		$new_values[$values['Order-No.']]['description'] = ($new_values[$values['Order-No.']]['description'] == '' ? implode(' ',array_filter([$values['Qty'],$values['Appliance'],$values['Appliance /Part']])) : implode(', ',array_merge(explode(', ',$new_values[$values['Order-No.']]['description']),[implode(' ',array_filter([$values['Qty'],$values['Appliance'],$values['Appliance /Part']]))])));
		$new_values[$values['Order-No.']]['weight'] = ($new_values[$values['Order-No.']]['weight'] == '' ? $values['Gross Weight (KG)'] : implode(', ',array_merge(explode(', ',$new_values[$values['Order-No.']]['weight']),[$values['Gross Weight (KG)']])));
		$new_values[$values['Order-No.']]['volume'] = ($new_values[$values['Order-No.']]['volume'] == '' ? $values['Gross Volume (LTR)'] : implode(', ',array_merge(explode(', ',$new_values[$values['Order-No.']]['volume']),[$values['Gross Volume (LTR)']])));
		$new_values[$values['Order-No.']]['customer_name'] = $values['Name'];
		$new_values[$values['Order-No.']]['street_address'] = $values['Street'];
		$new_values[$values['Order-No.']]['unit_number'] = $values['Additional Address Info'];
		$new_values[$values['Order-No.']]['city'] = $values['City'];
		$new_values[$values['Order-No.']]['province'] = $values['Prov'];
		$new_values[$values['Order-No.']]['postal_code'] = $values['Postalcode'];
		$new_values[$values['Order-No.']]['phone1'] = $values['Phone 1'];
		$new_values[$values['Order-No.']]['phone2'] = $values['Phone 2'];
		$new_values[$values['Order-No.']]['phone3'] = $values['Phone 3'];
		$new_values[$values['Order-No.']]['email'] = $values['eMail'];
		$new_values[$values['Order-No.']]['info'] = $values['00022'];
		$new_values[$values['Order-No.']]['status'] = $values['Shipment Status'];
	}
	fclose($handle);

	if (!file_exists('output_ml')) {
		mkdir('output_ml', 0777, true);
	}
	$today_date = date('Y_m_d');
	$FileName = "output_ml/".file_safe_str("macro_miele_".$today_date.".csv",'output_ml/');
	$file = fopen($FileName, "w");
	$new_csv = ['Invoice Number', 'Date', 'Description', 'Gross Weight', 'Gross Volume', 'Customer Name', 'Street Address', 'Unit Number', 'City', 'Province', 'Postal Code','Phone 1','Phone 2','Phone 3','Email','00022','Shipment Status'];
	$date = date('Y-m-d');
	foreach ($new_values as $key => $value) {
		if(!empty($value['date']) && !empty($value['customer_name']) && !empty($value['street_address']) && !empty($value['city']) && !empty($value['phone1']) && !empty(strip_tags(html_entity_decode($value['description'])))) {
			$existing = $dbc->query("SELECT * FROM `ticket_schedule` LEFT JOIN `tickets` ON `ticket_schedule`.`ticketid`=`tickets`.`ticketid` WHERE `tickets`.`businessid`='$businessid' AND `ticket_schedule`.`order_number`='$key' AND `ticket_schedule`.`to_do_date`='".$value['date']."' AND `ticket_schedule`.`client_name`='".$value['customer_name']."' AND `ticket_schedule`.`address`='".$value['street_address']."' AND `ticket_schedule`.`city`='".$value['city']."' AND `ticket_schedule`.`details`='".$value['phone1'].','.$value['phone2'].','.$value['phone3']."'");
			if($existing->num_rows == 0) {
				$date = $value['date'];
				$dbc->query("INSERT INTO `tickets` (`ticket_type`,`businessid`,`region`,`classification`, `salesorderid`,`ticket_label`,`heading`) VALUES ('$ticket_type','$businessid','$region','$classification','$key','$business_name - $key','$business_name - $key')");
				$ticketid = $dbc->insert_id;
				if(!empty($warehouses[$value['city']]) && !empty($value['city'])) {
					$dbc->query("INSERT INTO `ticket_schedule` (`ticketid`,`type`,`to_do_date`,`to_do_start_time`,`client_name`,`address`,`city`,`postal_code`,`order_number`,`status`) VALUES ('$ticketid','".$warehouses[$value['city']]['warehouse_name']."','".$value['date']."','".$warehouse_start_time."','".$business_name."','".$warehouses[$value['city']]['address']."','".$warehouses[$value['city']]['city']."','".$warehouses[$value['city']]['postal_code']."','".$key."','$default_status')");
				}
				$dbc->query("INSERT INTO `ticket_schedule` (`ticketid`,`to_do_date`,`client_name`,`address`,`city`,`province`,`postal_code`,`details`,`email`,`order_number`,`notes`,`status`) VALUES ('$ticketid','".$value['date']."','".$value['customer_name']."','".$value['street_address'].' '.$value['unit_number']."','".$value['city']."','".$value['province']."','".$value['postal_code']."','".$value['email']."','".$value['phone1'].','.$value['phone2'].','.$value['phone3']."','".$key."','&lt;p&gt;".$value['description']."&lt;/p&gt;&lt;p&gt;Gross Volume (LTR): ".$value['volume']."&lt;/p&gt;&lt;p&gt;Gross Weight (KG): ".$value['weight']."&lt;/p&gt;&lt;p&gt;".$value['info']."&lt;/p&gt;','$default_status')");
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
			<label class="form-checkbox"><input type="radio" name="delimiter" value="newline" checked>New Line</label>
			<label class="form-checkbox"><input type="radio" name="delimiter" value="comma">Comma</label><br>
			<select class="chosen-select-deselect" data-placeholder="Select <?= BUSINESS_CAT ?>" name="businessid"><option />
				<?php foreach(sort_contacts_query($dbc->query("SELECT `name`, `contactid` FROM `contacts` WHERE `category`='".BUSINESS_CAT."' AND `deleted`=0 AND `status` > 0")) as $business) { ?>
					<option value="<?= $business['contactid'] ?>"><?= $business['name'] ?></option>
				<?php } ?>
			</select>
			<input type="file" name="csv_file">
			<input type="hidden" name="ticket_type" value="<?php foreach($macro_list as $macro) {
				if($macro[0] == $_GET['macro']) {
					echo $macro[1];
				}
			} ?>">
			<input type="submit" name="upload_file" value="Submit" class="btn brand-btn">
		</p>
	</ol>
</form>