<?php include_once('../include.php');
if(empty($_GET['card'])) {
	$_GET['card'] = $_GET['type'];
}
if(!file_exists('export')) {
	mkdir(export, 0777);
}
$file_name = 'export_'.date('Y_m_d_h_i').'.csv';
$field_config = array_filter(explode(',',get_config($dbc, 'company_rate_fields')));

if(!isset($_POST['import']) && !isset($_POST['export'])) {
	?><form class="form-horizontal" method="POST" action="" enctype="multipart/form-data">
		<div class="form-group">
			<label class="col-sm-4">Source Details:</label>
			<div class="col-sm-8">
				<button class="btn brand-btn" type="Submit" name="export" value="details">Export Current Rates</button>
				<button class="btn brand-btn" type="Submit" name="export" value="blank">Export Blank Template</button>
			</div>
		</div>
		<?php if($_GET['card'] == 'customer') { ?>
			<div class="form-group">
				<label class="col-sm-4">Contact to import for:<br /><em>This can be used to import a new rate card to a specific client. Please ensure it conforms to the layout that can be seen by exporting an existing rate card. You can also import multiple by not selecting a Contact here, and entering the id of the contact in the spreadsheet.</em></label>
				<div class="col-sm-8">
					<select class="chosen-select-deselect" name="clientid" data-placeholder="Select Contact..."><option />
						<?php foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `category`, `name`, `first_name`, `last_name`, `site_name`, `display_name` FROM `contacts` WHERE `deleted`=0 AND `status` > 0 AND `category` != 'Staff'")) as $contact) { ?>
							<option data-category="<?= $contact['category'] ?>" value="<?= $contact['contactid'] ?>"><?= $contact['full_name'] ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		<?php } else { ?>
			<h3>Importing Rate Cards</h3>
			<em>This can be used to quickly import multiple rate cards, or edit existing rate cards. To edit multiple rate cards, you can export the current rate cards, edit them, and then upload them back into the system. If you wish to add additional rate cards, you can do so simply by leaving the id field empty (the id field is not present in blank export files, as you can only add new rate cards with that file).</em>
		<?php } ?>
		<div class="form-group">
			<label class="col-sm-4">File with Rate Card Details to Import</label>
			<div class="col-sm-8">
				<input type="file" name="import_file" class="form-control">
			</div>
		</div>
		<button class="btn brand-btn pull-right" type="Submit" name="import" value="import">Upload</button>
		<div class="clearfix"></div>
	</form><?php
}
else if($_GET['card'] == 'universal' || $_GET['card'] == 'company' && isset($_POST['export'])) {
	$fields = ['ID' => 'companyrcid'];
	foreach($field_config as $field_name) {
		switch($field_name) {
			case 'start_end_dates': $fields['Start Date'] = 'start_date'; $fields['End Date'] = 'end_date'; break;
			case 'type': $fields['Type'] = 'rate_card_types'; break;
			case 'heading': $fields['Heading'] = 'heading'; break;
			case 'description': $fields['Description'] = 'description'; break;
			case 'itemtype': $fields['Description'] = 'description'; $fields['Item ID'] = 'item_id'; break;
			case 'daily': $fields['Daily'] = 'daily'; break;
			case 'hourly': $fields['Hourly'] = 'hourly'; break;
			case 'uom': $fields['Unit of Measure'] = 'uom'; break;
			case 'cost': $fields['Cost'] = 'cost'; break;
			case 'customer': $fields['Customer Price'] = 'cust_price'; break;
			case 'sort_order': $fields['Display Order'] = 'sort_order'; break;
		}
	}
	$fields['Archived'] = 'deleted';
	
	$export = fopen($file_name,'w');
	$headings = [];
	foreach($fields as $label => $field) {
		if($_POST['export'] != 'blank' || !in_array($field,['companyrcid','deleted'])) {
			$headings[] = $label;
		}
	}
	
	if($_POST['export'] != 'blank') {
		$rate_name = false;
		$rate_tile = false;
		echo "SELECT `".implode('`,`',$fields)."`,`rate_card_name`,`tile_name` FROM `company_rate_card` WHERE `deleted`=0 ORDER BY `rate_card_name`, `tile_name`, `companyrcid`";
		$rate_cards = $dbc->query("SELECT `".implode('`,`',$fields)."`,`rate_card_name`,`tile_name` FROM `company_rate_card` WHERE `deleted`=0 ORDER BY `rate_card_name`, `tile_name`, `companyrcid`");
		while($rate = $rate_cards->fetch_assoc()) {
			if($rate_name !== $rate['rate_card_name'] || $rate_tile !== $rate['tile_name']) {
				if($rate_name !== false) {
					fputcsv($export,[]);
				}
				$rate_name = $rate['rate_card_name'];
				$rate_tile = $rate['tile_name'];
				fputcsv($export,['Rate Card:',($rate_name ?: 'Universal'),'Items:',$rate_tile]);
				fputcsv($export,$headings);
			}
			if($rate['tile_name'] == 'Position' && $rate['item_id'] > 0) {
				$rate['item_id'] = get_field_value('name','positions','position_id',$rate['item_id']);
			} else if($rate['tile_name'] == 'Services' && $rate['item_id'] > 0) {
				$rate['item_id'] = implode(': ',get_field_value('category service_type heading','services','serviceid',$rate['item_id']));
			} else if($rate['tile_name'] == 'Labour' && $rate['item_id'] > 0) {
				$rate['item_id'] = implode(': ',get_field_value('labour_type category heading','labour','labourid',$rate['item_id']));
			} else if($rate['tile_name'] == 'Staff' && $rate['item_id'] > 0) {
				$rate['item_id'] = get_contact($dbc, $rate['item_id'], 'name_company');
			}
			unset($rate['rate_card_name']);
			unset($rate['tile_name']);
			fputcsv($export, $rate);
		}
	} else {
		fputcsv($export,['Rate Card:','','Items:','']);
		fputcsv($export,$headings);
	}
	ob_clean();
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file_name));
	readfile($file_name);
	exit();
}
else if($_GET['card'] == 'universal' || $_GET['card'] == 'company') {
	if($file = fopen($_FILES['import_file']['tmp_name'],'r')) {
		$headings = [];
		$rate_name = '';
		$rate_tile = '';
		$contact_names = sort_contacts_query($dbc->query("SELECT `contactid`, `name`, `first_name`, `last_name` FROM `contacts` WHERE `deleted`=0 AND `status` > 0"));
		while($row = fgetcsv($file)) {
			if(count(array_filter($row)) == 0) {
				$headings = [];
				continue;
			}
			if($row[0] == 'Rate Card:') {
				$rate_name = filter_var($row[1],FILTER_SANITIZE_STRING);
				if($rate_name == 'Universal') {
					$rate_name = '';
				}
				$rate_tile = filter_var($row[3],FILTER_SANITIZE_STRING);
			} else if(count($headings) == 0) {
				foreach($row as $label) {
					switch($label) {
						case 'ID': $headings[] = 'companyrcid'; break;
						case 'Start Date': $headings[] = 'start_date'; break;
						case 'End Date': $headings[] = 'end_date'; break;
						case 'Type': $headings[] = 'rate_card_types'; break;
						case 'Heading': $headings[] = 'heading'; break;
						case 'Description': $headings[] = 'description'; break;
						case 'Item ID': $headings[] = 'item_id'; break;
						case 'Daily': $headings[] = 'daily'; break;
						case 'Hourly': $headings[] = 'hourly'; break;
						case 'Unit of Measure': $headings[] = 'uom'; break;
						case 'Cost': $headings[] = 'cost'; break;
						case 'Customer Price': $headings[] = 'cust_price'; break;
						case 'Display Order': $headings[] = 'sort_order'; break;
						case 'Archived': $headings[] = 'deleted'; break;
					}
				}
			} else {
				$id = 0;
				foreach($headings as $i => $field) {
					if('companyrcid' == $field) {
						$id = $row[$i];
					} else {
						if($field == 'item_id' && !($row[$i] > 0) && $rate_tile == 'Staff') {
							foreach($contact_names as $contact) {
								if($row[$i] == trim($contact['name'].': '.$contact['first_name'].' '.$contact['last_name'],': ')) {
									$row[$i] = $contact['contactid'];
								}
							}
						} else if($field == 'item_id' && !($row[$i] > 0) && $rate_tile == 'Position') {
							$row[$i] = $dbc->query("SELECT `position_id` FROM `positions` WHERE `deleted`=0 AND `name`='".$row[$i]."'")->fetch_array()[0];
						} else if($field == 'item_id' && !($row[$i] > 0) && $rate_tile == 'Services') {
							$row[$i] = $dbc->query("SELECT `serviceid` FROM `services` WHERE `deleted`=0 AND CONCAT(IFNULL(`category`,''),': ',IFNULL(`service_type`,''),': ',IFNULL(`heading`,''))='".$row[$i]."'")->fetch_array()[0];
						} else if($field == 'item_id' && !($row[$i] > 0) && $rate_tile == 'Labour') {
							$row[$i] = $dbc->query("SELECT `labourid` FROM `labour` WHERE `deleted`=0 AND CONCAT(IFNULL(`labour_type`,''),': ',IFNULL(`category`,''),': ',IFNULL(`heading`,''))='".$row[$i]."'")->fetch_array()[0];
						}
						$values[] = filter_var($row[$i],FILTER_SANITIZE_STRING);
						$updates[] = "`$field`='".filter_var($row[$i],FILTER_SANITIZE_STRING)."'";
					}
				}
				$updates[] = "`rate_card_name`='".$rate_name."'";
				$updates[] = "`tile_name`='".$rate_tile."'";
				if($id > 0) {
					echo "UPDATE `company_rate_card` SET ".implode(',',$updates)." WHERE `companyrcid`='$id'";
					$dbc->query("UPDATE `company_rate_card` SET ".implode(',',$updates)." WHERE `companyrcid`='$id'");
				} else {
					$dbc->query("INSERT INTO `company_rate_card` (`".implode('`,`',array_filter($fields,function($v) { return $v != 'companyrcid'; }))."`) VALUES ('".implode("','",$values)."')");
				}
			}
		}
	}
	echo "<script>window.location.replace('?type=company');</script>";
}
else if($_GET['card'] == 'customer' && isset($_POST['export'])) {
	$rate_cards = $dbc->query("SELECT * FROM `rate_card` WHERE `deleted`=0");
	$export = fopen($file_name,'w');
	$rate = $rate_cards->fetch_assoc();
	$headings = ['ratecardid','clientid','rate_card_name','start_date','end_date','alert_date','alert_staff','frequency_type','frequency_interval','packageid','package_price','promotionid','promotion_price','serviceid','service_price','service_unit','productid','product_price','sredid','sred_price','client','client_price','materialid','material_price','inventoryid','inventory_price','equipmentid','equipment_price','equipment_category','equipment_category_hourly','equipment_category_daily','staffid','staff_price','staff_position','staff_position_hourly','staff_position_daily','contractorid','contractor_price','customerid','customer_price','expense_type','expense_category','expense_price','vendorid','vendor_price','customid','custom_price','labourid','labour_price','other_type','other_detail','other_price','total_price','on_off','hide','deleted'];
	fputcsv($export,$headings);
	if($_POST['export'] != 'blank') {
		do {
			$max = 0;
			$details = [];
			$details['ratecardid'] = $rate['ratecardid'];
			$details['clientid'] = $rate['clientid'];
			$details['rate_card_name'] = $rate['rate_card_name'];
			$details['start_date'] = $rate['start_date'];
			$details['end_date'] = $rate['end_date'];
			$details['alert_date'] = $rate['alert_date'];
			$details['alert_staff'] = $rate['alert_staff'];
			$details['frequency_type'] = $rate['frequency_type'];
			$details['frequency_interval'] = $rate['frequency_interval'];
			$details['packageid'] = [];
			$details['package_price'] = [];
			foreach(explode('**',$rate['package']) as $j => $package) {
				if($j > $max) {
					$max = $j;
				}
				$details['packageid'][] = explode('#',$package)[0];
				$details['package_price'][] = explode('#',$package)[1];
			}
			$details['promotionid'] = [];
			$details['promotion_price'] = [];
			foreach(explode('**',$rate['promotion']) as $j => $promotion) {
				if($j > $max) {
					$max = $j;
				}
				$details['promotionid'][] = explode('#',$promotion)[0];
				$details['promotion_price'][] = explode('#',$promotion)[1];
			}
			$details['serviceid'] = [];
			$details['service_price'] = [];
			$details['service_unit'] = [];
			foreach(explode('**',$rate['services']) as $j => $service) {
				if($j > $max) {
					$max = $j;
				}
				$details['serviceid'][] = explode('#',$service)[0];
				$details['service_price'][] = explode('#',$service)[1];
				$details['service_unit'][] = explode('#',$service)[2];
			}
			$details['productid'] = [];
			$details['product_price'] = [];
			foreach(explode('**',$rate['products']) as $j => $product) {
				if($j > $max) {
					$max = $j;
				}
				$details['productid'][] = explode('#',$product)[0];
				$details['product_price'][] = explode('#',$product)[1];
			}
			$details['sredid'] = [];
			$details['sred_price'] = [];
			foreach(explode('**',$rate['sred']) as $j => $sred) {
				if($j > $max) {
					$max = $j;
				}
				$details['sredid'][] = explode('#',$sred)[0];
				$details['sred_price'][] = explode('#',$sred)[1];
			}
			$details['client'] = [];
			$details['client_price'] = [];
			foreach(explode('**',$rate['client']) as $j => $client) {
				if($j > $max) {
					$max = $j;
				}
				$details['client'][] = explode('#',$client)[0];
				$details['client_price'][] = explode('#',$client)[1];
			}
			$details['materialid'] = [];
			$details['material_price'] = [];
			foreach(explode('**',$rate['material']) as $j => $material) {
				if($j > $max) {
					$max = $j;
				}
				$details['materialid'][] = explode('#',$material)[0];
				$details['material_price'][] = explode('#',$material)[1];
			}
			$details['inventoryid'] = [];
			$details['inventory_price'] = [];
			foreach(explode('**',$rate['inventory']) as $j => $inventory) {
				if($j > $max) {
					$max = $j;
				}
				$details['inventoryid'][] = explode('#',$inventory)[0];
				$details['inventory_price'][] = explode('#',$inventory)[1];
			}
			$details['equipmentid'] = [];
			$details['equipment_price'] = [];
			foreach(explode('**',$rate['equipment']) as $j => $equipment) {
				if($j > $max) {
					$max = $j;
				}
				$details['equipmentid'][] = explode('#',$equipment)[0];
				$details['equipment_price'][] = explode('#',$equipment)[1];
			}
			$details['equipment_category'] = [];
			$details['equipment_category_hourly'] = [];
			$details['equipment_category_daily'] = [];
			foreach(explode('**',$rate['equipment_category']) as $j => $equipment_category) {
				if($j > $max) {
					$max = $j;
				}
				$details['equipment_category'][] = explode('#',$equipment_category)[0];
				$details['equipment_category_hourly'][] = explode('#',$equipment_category)[1];
				$details['equipment_category_daily'][] = explode('#',$equipment_category)[2];
			}
			$details['staffid'] = [];
			$details['staff_price'] = [];
			foreach(explode('**',$rate['staff']) as $j => $staff) {
				if($j > $max) {
					$max = $j;
				}
				$details['staffid'][] = explode('#',$staff)[0];
				$details['staff_price'][] = explode('#',$staff)[1];
			}
			$details['staff_position'] = [];
			$details['staff_position_hourly'] = [];
			$details['staff_position_daily'] = [];
			foreach(explode('**',$rate['staff_position']) as $j => $staff_position) {
				if($j > $max) {
					$max = $j;
				}
				$details['staff_position'][] = explode('#',$staff_position)[0];
				$details['staff_position_hourly'][] = explode('#',$staff_position)[1];
				$details['staff_position_daily'][] = explode('#',$staff_position)[2];
			}
			$details['contractorid'] = [];
			$details['contractor_price'] = [];
			foreach(explode('**',$rate['contractor']) as $j => $contractor) {
				if($j > $max) {
					$max = $j;
				}
				$details['contractorid'][] = explode('#',$contractor)[0];
				$details['contractor_price'][] = explode('#',$contractor)[1];
			}
			$details['customerid'] = [];
			$details['customer_price'] = [];
			foreach(explode('**',$rate['customer']) as $j => $customer) {
				if($j > $max) {
					$max = $j;
				}
				$details['customerid'][] = explode('#',$customer)[0];
				$details['customer_price'][] = explode('#',$customer)[1];
			}
			$details['expense_type'] = [];
			$details['expense_category'] = [];
			$details['expense_price'] = [];
			foreach(explode('**',$rate['expense']) as $j => $expense) {
				if($j > $max) {
					$max = $j;
				}
				$details['expense_type'][] = explode('#',$expense)[0];
				$details['expense_category'][] = explode('#',$expense)[1];
				$details['expense_price'][] = explode('#',$expense)[2];
			}
			$details['vendorid'] = [];
			$details['vendor_price'] = [];
			foreach(explode('**',$rate['vendor']) as $j => $vendor) {
				if($j > $max) {
					$max = $j;
				}
				$details['vendorid'][] = explode('#',$vendor)[0];
				$details['vendor_price'][] = explode('#',$vendor)[1];
			}
			$details['customid'] = [];
			$details['custom_price'] = [];
			foreach(explode('**',$rate['custom']) as $j => $custom) {
				if($j > $max) {
					$max = $j;
				}
				$details['customid'][] = explode('#',$custom)[0];
				$details['custom_price'][] = explode('#',$custom)[1];
			}
			$details['labourid'] = [];
			$details['labour_price'] = [];
			foreach(explode('**',$rate['labour']) as $j => $labour) {
				if($j > $max) {
					$max = $j;
				}
				$details['labourid'][] = explode('#',$labour)[0];
				$details['labour_price'][] = explode('#',$labour)[1];
			}
			$details['other_type'] = [];
			$details['other_detail'] = [];
			$details['other_price'] = [];
			foreach(explode('**',$rate['other']) as $j => $other) {
				if($j > $max) {
					$max = $j;
				}
				$details['other_type'][] = explode('#',$other)[0];
				$details['other_detail'][] = explode('#',$other)[1];
				$details['other_price'][] = explode('#',$other)[2];
			}
			$details['total_price'] = $rate['total_price'];
			$details['on_off'] = $rate['on_off'];
			$details['hide'] = $rate['hide'];
			$details['deleted'] = $rate['deleted'];
			for($i = 0; $i < $max; $i++) {
				fputcsv($export, ['ratecardid'=>$details['ratecardid'],'clientid'=>$details['clientid'],'rate_card_name'=>$details['rate_card_name'],'start_date'=>$details['start_date'],'end_date'=>$details['end_date'],'alert_date'=>$details['alert_date'],'alert_staff'=>$details['alert_staff'],'frequency_type'=>$details['frequency_type'],'frequency_interval'=>$details['frequency_interval'],'packageid'=>$details['packageid'][$i],'package_price'=>$details['package_price'][$i],'promotionid'=>$details['promotionid'][$i],'promotion_price'=>$details['promotion_price'][$i],'serviceid'=>$details['serviceid'][$i],'service_price'=>$details['service_price'][$i],'service_unit'=>$details['service_unit'][$i],'productid'=>$details['productid'][$i],'product_price'=>$details['product_price'][$i],'sredid'=>$details['sredid'][$i],'sred_price'=>$details['sred_price'][$i],'clientid'=>$details['clientid'][$i],'client_price'=>$details['client_price'][$i],'materialid'=>$details['materialid'][$i],'material_price'=>$details['material_price'][$i],'inventoryid'=>$details['inventoryid'][$i],'inventory_price'=>$details['inventory_price'][$i],'equipmentid'=>$details['equipmentid'][$i],'equipment_price'=>$details['equipment_price'][$i],'equipment_category'=>$details['equipment_category'][$i],'equipment_category_hourly'=>$details['equipment_category_hourly'][$i],'equipment_category_daily'=>$details['equipment_category_daily'][$i],'staffid'=>$details['staffid'][$i],'staff_price'=>$details['staff_price'][$i],'staff_position'=>$details['staff_position'][$i],'staff_position_hourly'=>$details['staff_position_hourly'][$i],'staff_position_daily'=>$details['staff_position_daily'][$i],'contractorid'=>$details['contractorid'][$i],'contractor_price'=>$details['contractor_price'][$i],'customerid'=>$details['customerid'][$i],'customer_price'=>$details['customer_price'][$i],'expense_type'=>$details['expense_type'][$i],'expense_category'=>$details['expense_category'][$i],'expense_price'=>$details['expense_price'][$i],'vendorid'=>$details['vendorid'][$i],'vendor_price'=>$details['vendor_price'][$i],'customid'=>$details['customid'][$i],'custom_price'=>$details['custom_price'][$i],'labourid'=>$details['labourid'][$i],'labour_price'=>$details['labour_price'][$i],'other_type'=>$details['other_type'][$i],'other_detail'=>$details['other_detail'][$i],'other_price'=>$details['other_price'][$i],'total_price'=>$details['total_price'],'on_off'=>$details['on_off'],'hide'=>$details['hide'],'deleted'=>$details['deleted']]);
			}
		} while($rate = $rate_cards->fetch_assoc());
	}
	ob_clean();
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file_name));
	readfile($file_name);
	exit();
	
}
else if($_GET['card'] == 'customer') {
	if($file = fopen($_FILES['import_file']['tmp_name'],'r')) {
		$titles = fgetcsv($file);
		$fields = [];
		foreach($titles as $i => $field) {
			if(in_array($field,['companyrcid','rate_card_name','start_date','end_date','alert_date','alert_staff','tile_name','rate_card_types','rate_categories','heading','description','item_id','uom','cost','cust_price','profit','margin','daily','hourly','sort_order','deleted'])) {
				$fields[$i] = $field;
			}
		}
		$clientid = $_POST['clientid'] > 0 ? $_POST['clientid'] : 0;
		$single_client = $clientid > 0 ? true : false;
		$current_client = $clientid;
		$client_list = [];
		$current_values = [];
		while($row = fgetcsv($file)) {
			foreach($fields as $i => $field) {
				if(!$single_client && 'clientid' == $field && $row[$i] > 0) {
					$clientid = $row[$i];
				} else if($row[$i] != '') {
					$field_name = $field.'_row';
					$$field_name = filter_var($row[$i],FILTER_SANITIZE_STRING);
				}
				if($current_client != $clientid) {
					foreach($fields as $i => $field) {
						$client_list[$current_client][$field] = $$field;
						$current_values[$field] = [];
						$field_name = $field.'_row';
						$current_values[$field][] = $$field_name;
						unset($$field_name);
					}
					$current_values = [];
					$current_client = $clientid;
				} else {
					foreach($fields as $i => $field) {
						if(!isset($$field)) {
							$$field = [];
						}
						$field_name = $field.'_row';
						$current_values[$field][] = $$field_name;
						unset($$field_name);
					}
				}
			}
		}
		foreach($fields as $i => $field) {
			$client_list[$current_client][$field] = $$field;
		}
		foreach($client_list as $clientid => $fields) {
			$field_list = [];
			$value_list = [];
		}
		if($id > 0) {
			$dbc->query("UPDATE `company_rate_card` SET ".implode(',',$updates)." WHERE `companyrcid`='$id'");
		} else {
			$dbc->query("INSERT INTO `company_rate_card` (`".implode('`,`',array_filter($fields,function($v) { return $v != 'companyrcid'; }))."`) VALUES ('".implode("','",$values)."')");
		}
	}
}
else if($_GET['card'] == 'estimate' && isset($_POST['export'])) {
	$rate_cards = $dbc->query("SELECT * FROM `rate_card_estimate_scopes` WHERE `deleted`=0");
	$export = fopen($file_name,'w');
	$headings = [];
	while($rate_card = $rate_cards->fetch_assoc()) {
		$rate = $rate_cards->fetch_assoc();
		fputcsv($export,['Estimate Scope:',$rate['rate_card_name'],'('.$rate['id'].')']);
		$rate_items = $dbc->query("SELECT * FROM `rate_card_estimate_scope_lines` WHERE `deleted`=0");
		$item = $rate_items->fetch_assoc();
		if(empty($headings)) {
			foreach($item as $field => $value) {
				if($_POST['export'] != 'blank' || !in_array($field,['id','deleted'])) {
					$headings[] = $field;
				}
			}
		}
		fputcsv($export,$headings);
		if($_POST['export'] != 'blank') {
			do {
				fputcsv($export, $item);
			} while($item = $rate_items->fetch_assoc());
		}
	}
	ob_clean();
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file_name));
	readfile($file_name);
	exit();
	
}
else if($_GET['card'] == 'estimate') {
	
}
else if($_GET['card'] == 'position' && isset($_POST['export'])) {
	$rate_cards = $dbc->query("SELECT * FROM `position_rate_table` WHERE `deleted`=0");
	$export = fopen($file_name,'w');
	$rate = $rate_cards->fetch_assoc();
	$headings = [];
	foreach($rate as $field => $value) {
		if($_POST['export'] != 'blank' || !in_array($field,['rate_id','deleted'])) {
			$headings[] = $field;
		}
	}
	fputcsv($export,$headings);
	if($_POST['export'] != 'blank') {
		do {
			fputcsv($export, $rate);
		} while($rate = $rate_cards->fetch_assoc());
	}
	ob_clean();
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file_name));
	readfile($file_name);
	exit();
	
}
else if($_GET['card'] == 'position') {
	if($file = fopen($_FILES['import_file']['tmp_name'],'r')) {
		$titles = fgetcsv($file);
		$fields = [];
		foreach($titles as $i => $field) {
			if(in_array($field,['rate_id','start_date','end_date','alert_date','alert_staff','position_id','deleted','annual','monthly','semi_month','weekly','daily','hourly','hourly_work','hourly_travel','field_day_actual','field_day_bill','cost','price_admin','price_wholesale','price_commercial','price_client','minimum','unit_price','unit_cost','rent_price','rent_days','rent_weeks','rent_months','rent_years','num_days','num_hours','num_kms','num_miles','fee','hours_estimated','hours_actual','service_code','description','history'])) {
				$fields[$i] = $field;
			}
		}
		while($row = fgetcsv($file)) {
			$columns = [];
			$updates = [];
			$values = [];
			$id = 0;
			foreach($fields as $i => $field) {
				if('rate_id' == $field && $row[$i] > 0) {
					$id = $row[$i];
				} else if($row[$i] != '') {
					$columns[] = $field;
					$values[] = filter_var($row[$i],FILTER_SANITIZE_STRING);
				}
				$updates[] = "`$field`='".filter_var($row[$i],FILTER_SANITIZE_STRING)."'";
			}
			if($id > 0) {
				$dbc->query("UPDATE `position_rate_table` SET ".implode(',',$updates)." WHERE `rate_id`='$id'");
			} else {
				$dbc->query("INSERT INTO `position_rate_table` (`".implode('`,`',$columns)."`) VALUES ('".implode("','",$values)."')");
			}
		}
	}
}
else if($_GET['card'] == 'staff' && isset($_POST['export'])) {
	$rate_cards = $dbc->query("SELECT * FROM `staff_rate_table` WHERE `deleted`=0");
	$export = fopen($file_name,'w');
	$rate = $rate_cards->fetch_assoc();
	$headings = [];
	foreach($rate as $field => $value) {
		if($_POST['export'] != 'blank' || !in_array($field,['rate_id','deleted'])) {
			$headings[] = $field;
		}
	}
	fputcsv($export,$headings);
	if($_POST['export'] != 'blank') {
		do {
			fputcsv($export, $rate);
		} while($rate = $rate_cards->fetch_assoc());
	}
	ob_clean();
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file_name));
	readfile($file_name);
	exit();
	
}
else if($_GET['card'] == 'staff') {
	if($file = fopen($_FILES['import_file']['tmp_name'],'r')) {
		$titles = fgetcsv($file);
		$fields = [];
		foreach($titles as $i => $field) {
			if(in_array($field,['rate_id','staff_id','category','start_date','end_date','alert_date','alert_staff','deleted','annual','monthly','semi_month','weekly','daily','hourly','hourly_work','hourly_travel','field_day_actual','field_day_bill','cost','price_admin','price_wholesale','price_commercial','price_client','minimum','unit_price','unit_cost','rent_price','rent_days','rent_weeks','rent_months','rent_years','num_days','num_hours','num_kms','num_miles','fee','hours_estimated','hours_actual','service_code','description','work_desc','history','color_code','sort_order','travel_range_1','travel_range_5','travel_range_1_5'])) {
				$fields[$i] = $field;
			}
		}
		while($row = fgetcsv($file)) {
			$updates = [];
			$values = [];
			$id = 0;
			foreach($fields as $i => $field) {
				if('rate_id' == $field && $row[$i] > 0) {
					$id = $row[$i];
				} else if($row[$i] != '') {
					$values[] = filter_var($row[$i],FILTER_SANITIZE_STRING);
				}
				$updates[] = "`$field`='".filter_var($row[$i],FILTER_SANITIZE_STRING)."'";
			}
			if($id > 0) {
				$dbc->query("UPDATE `staff_rate_table` SET ".implode(',',$updates)." WHERE `rate_id`='$id'");
			} else {
				$dbc->query("INSERT INTO `staff_rate_table` (`".implode('`,`',array_filter($fields,function($v) { return $v != 'rate_id'; }))."`) VALUES ('".implode("','",$values)."')");
			}
		}
	}
}
else if($_GET['card'] == 'equipment' && isset($_POST['export'])) {
	$rate_cards = $dbc->query("SELECT * FROM `equipment_rate_table` WHERE `deleted`=0");
	$export = fopen($file_name,'w');
	$rate = $rate_cards->fetch_assoc();
	$headings = [];
	foreach($rate as $field => $value) {
		if($_POST['export'] != 'blank' || !in_array($field,['rate_id','deleted'])) {
			$headings[] = $field;
		}
	}
	fputcsv($export,$headings);
	if($_POST['export'] != 'blank') {
		do {
			fputcsv($export, $rate);
		} while($rate = $rate_cards->fetch_assoc());
	}
	ob_clean();
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file_name));
	readfile($file_name);
	exit();
	
}
else if($_GET['card'] == 'equipment') {
	if($file = fopen($_FILES['import_file']['tmp_name'],'r')) {
		$titles = fgetcsv($file);
		$fields = [];
		foreach($titles as $i => $field) {
			if(in_array($field,['rate_id','start_date','end_date','alert_date','alert_staff','equipment_id','deleted','annual','monthly','semi_month','weekly','daily','hourly','hourly_work','hourly_travel','field_day_actual','field_day_bill','cost','price_admin','price_wholesale','price_commercial','price_client','minimum','unit_price','unit_cost','rent_price','rent_days','rent_weeks','rent_months','rent_years','num_days','num_hours','num_kms','num_miles','fee','hours_estimated','hours_actual','service_code','description','history'])) {
				$fields[$i] = $field;
			}
		}
		while($row = fgetcsv($file)) {
			$updates = [];
			$values = [];
			$id = 0;
			foreach($fields as $i => $field) {
				if('rate_id' == $field && $row[$i] > 0) {
					$id = $row[$i];
				} else if($row[$i] != '') {
					$values[] = filter_var($row[$i],FILTER_SANITIZE_STRING);
				}
				$updates[] = "`$field`='".filter_var($row[$i],FILTER_SANITIZE_STRING)."'";
			}
			if($id > 0) {
				$dbc->query("UPDATE `equipment_rate_table` SET ".implode(',',$updates)." WHERE `rate_id`='$id'");
			} else {
				$dbc->query("INSERT INTO `equipment_rate_table` (`".implode('`,`',array_filter($fields,function($v) { return $v != 'rate_id'; }))."`) VALUES ('".implode("','",$values)."')");
			}
		}
	}
}
else if($_GET['card'] == 'category' && isset($_POST['export'])) {
	$rate_cards = $dbc->query("SELECT * FROM `category_rate_table` WHERE `deleted`=0");
	$export = fopen($file_name,'w');
	$rate = $rate_cards->fetch_assoc();
	$headings = [];
	foreach($rate as $field => $value) {
		if($_POST['export'] != 'blank' || !in_array($field,['rate_id','deleted'])) {
			$headings[] = $field;
		}
	}
	fputcsv($export,$headings);
	if($_POST['export'] != 'blank') {
		do {
			fputcsv($export, $rate);
		} while($rate = $rate_cards->fetch_assoc());
	}
	ob_clean();
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file_name));
	readfile($file_name);
	exit();
	
}
else if($_GET['card'] == 'category') {
	if($file = fopen($_FILES['import_file']['tmp_name'],'r')) {
		$titles = fgetcsv($file);
		$fields = [];
		foreach($titles as $i => $field) {
			if(in_array($field,['rate_id','start_date','end_date','alert_date','alert_staff','category','deleted','annual','monthly','semi_month','weekly','daily','hourly','hourly_work','hourly_travel','field_day_actual','field_day_bill','cost','price_admin','price_wholesale','price_commercial','price_client','minimum','unit_price','unit_cost','rent_price','rent_days','rent_weeks','rent_months','rent_years','num_days','num_hours','num_kms','num_miles','fee','hours_estimated','hours_actual','service_code','description','history'])) {
				$fields[$i] = $field;
			}
		}
		while($row = fgetcsv($file)) {
			$updates = [];
			$values = [];
			$id = 0;
			foreach($fields as $i => $field) {
				if('rate_id' == $field && $row[$i] > 0) {
					$id = $row[$i];
				} else if($row[$i] != '') {
					$values[] = filter_var($row[$i],FILTER_SANITIZE_STRING);
				}
				$updates[] = "`$field`='".filter_var($row[$i],FILTER_SANITIZE_STRING)."'";
			}
			if($id > 0) {
				$dbc->query("UPDATE `category_rate_table` SET ".implode(',',$updates)." WHERE `rate_id`='$id'");
			} else {
				$dbc->query("INSERT INTO `category_rate_table` (`".implode('`,`',array_filter($fields,function($v) { return $v != 'rate_id'; }))."`) VALUES ('".implode("','",$values)."')");
			}
		}
	}
}
else if($_GET['card'] == 'services' && isset($_POST['export'])) {
	$rate_cards = $dbc->query("SELECT * FROM `service_rate_card` WHERE `deleted`=0");
	$export = fopen($file_name,'w');
	$rate = $rate_cards->fetch_assoc();
	$headings = [];
	foreach($rate as $field => $value) {
		if($_POST['export'] != 'blank' || !in_array($field,['serviceratecardid','deleted'])) {
			$headings[] = $field;
		}
	}
	fputcsv($export,$headings);
	if($_POST['export'] != 'blank') {
		do {
			fputcsv($export, $rate);
		} while($rate = $rate_cards->fetch_assoc());
	}
	ob_clean();
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file_name));
	readfile($file_name);
	exit();
	
}
else if($_GET['card'] == 'services') {
	if($file = fopen($_FILES['import_file']['tmp_name'],'r')) {
		$titles = fgetcsv($file);
		$fields = [];
		foreach($titles as $i => $field) {
			if(in_array($field,['serviceratecardid','serviceid','start_date','end_date','alert_date','alert_staff','service_rate','admin_fee','editable','history','deleted'])) {
				$fields[$i] = $field;
			}
		}
		while($row = fgetcsv($file)) {
			$updates = [];
			$values = [];
			$id = 0;
			foreach($fields as $i => $field) {
				if('serviceratecardid' == $field && $row[$i] > 0) {
					$id = $row[$i];
				} else if($row[$i] != '') {
					$values[] = filter_var($row[$i],FILTER_SANITIZE_STRING);
				}
				$updates[] = "`$field`='".filter_var($row[$i],FILTER_SANITIZE_STRING)."'";
			}
			if($id > 0) {
				$dbc->query("UPDATE `service_rate_card` SET ".implode(',',$updates)." WHERE `serviceratecardid`='$id'");
			} else {
				$dbc->query("INSERT INTO `service_rate_card` (`".implode('`,`',array_filter($fields,function($v) { return $v != 'serviceratecardid'; }))."`) VALUES ('".implode("','",$values)."')");
			}
		}
	}
}
else if($_GET['card'] == 'labour' && isset($_POST['export'])) {
	$rate_cards = $dbc->query("SELECT `ratecardid`, `src_id` `labour_id`, `start_date`, `end_date`, `uom`, `cost`, `profit_percent`, `profit_dollar`, `price`, `deleted` FROM `tile_rate_card` WHERE `deleted`=0 AND `tile_name`='labour'");
	$export = fopen($file_name,'w');
	$rate = $rate_cards->fetch_assoc();
	$headings = [];
	foreach($rate as $field => $value) {
		if($_POST['export'] != 'blank' || !in_array($field,['ratecardid','deleted'])) {
			$headings[] = $field;
		}
	}
	fputcsv($export,$headings);
	if($_POST['export'] != 'blank') {
		do {
			fputcsv($export, $rate);
		} while($rate = $rate_cards->fetch_assoc());
	}
	ob_clean();
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file_name));
	readfile($file_name);
	exit();
}
else if($_GET['card'] == 'labour') {
	if($file = fopen($_FILES['import_file']['tmp_name'],'r')) {
		$titles = fgetcsv($file);
		$fields = [];
		foreach($titles as $i => $field) {
			if(in_array($field,['ratecardid','src_id` `labour_id','start_date','end_date','uom','cost','profit_percent','profit_dollar','price','deleted'])) {
				$fields[$i] = $field;
			}
		}
		while($row = fgetcsv($file)) {
			$updates = [];
			$values = [];
			$id = 0;
			foreach($fields as $i => $field) {
				if('ratecardid' == $field && $row[$i] > 0) {
					$id = $row[$i];
				} else if($row[$i] != '') {
					$values[] = filter_var($row[$i],FILTER_SANITIZE_STRING);
				}
				$updates[] = "`$field`='".filter_var($row[$i],FILTER_SANITIZE_STRING)."'";
			}
			
			if($id > 0) {
				$dbc->query("UPDATE `tile_rate_card` SET ".implode(',',$updates)." WHERE `ratecardid`='$id'");
			} else {
				$dbc->query("INSERT INTO `tile_rate_card` (`tile_name`,`".implode('`,`',array_filter($fields,function($v) { return $v != 'ratecardid'; }))."`) VALUES ('labour','".implode("','",$values)."')");
			}
		}
	}
}