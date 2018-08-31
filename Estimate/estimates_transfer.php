<?php if($estimateid > 0) {
	$sort = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`sort_order`) FROM `estimate_scope` WHERE `estimateid`='$estimateid'"))[0] + 1;
	$estimate = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `estimate` WHERE `estimateid`='$estimateid'"));

	// Packages
	foreach(explode('**',$estimate['package']) as $package) {
		$package = explode('#',$package);
		$id = $package[0];
		if($id > 0) {
			$price = $package[1];
			$details = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `package` WHERE `packageid`='$id'"));
			$cost = $details['cost'];
			$profit = $price - $cost;
			$margin = $profit / $cost * 100;
			mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`heading`,`src_table`,`src_id`,`qty`,`cost`,`profit`,`margin`,`price`,`retail`,`sort_order`)
				VALUES ('$estimateid','Packages','package','$id',1,'$cost','$profit','$margin','$price','$price','$sort')");
			$sort++;
		}
	}

	$before_change = '';
	$history = "Estimates scope entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);

	// Promotions
	foreach(explode('**',$estimate['promotion']) as $promotion) {
		$promotion = explode('#',$promotion);
		$id = $promotion[0];
		if($id > 0) {
			$price = $promotion[1];
			$details = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `promotion` WHERE `promotionid`='$id'"));
			$cost = $details['cost'];
			$profit = $price - $cost;
			$margin = $profit / $cost * 100;
			mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`heading`,`src_table`,`src_id`,`qty`,`cost`,`profit`,`margin`,`price`,`retail`,`sort_order`)
				VALUES ('$estimateid','Promotions','promotion','$id',1,'$cost','$profit','$margin','$price','$price','$sort')");
			$sort++;
		}
	}

	$before_change = '';
	$history = "Estimates scope entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);

	// Materials
	foreach(explode('**',$estimate['material']) as $material) {
		$material = explode('#',$material);
		$id = $material[0];
		if($id > 0) {
			$price = $material[1];
			$details = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `material` WHERE `materialid`='$id'"));
			$cost = $details['price'];
			$profit = $price - $cost;
			$margin = $profit / $cost * 100;
			mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`heading`,`src_table`,`src_id`,`qty`,`cost`,`profit`,`margin`,`price`,`retail`,`sort_order`)
				VALUES ('$estimateid','Materials','material','$id',1,'$cost','$profit','$margin','$price','$price','$sort')");
			$sort++;
		}
	}

	$before_change = '';
	$history = "Estimates scope entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	// Services
	foreach(explode('**',$estimate['services']) as $services) {
		$services = explode('#',$services);
		$id = $services[0];
		if($id > 0) {
			$total = $services[1];
			$price = $services[1] / $services[2];
			$qty = $services[2];
			$uom = $services[3];
			$details = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid`='$id'"));
			$cost = $details['cost'];
			$profit = $price - $cost;
			$margin = $profit / $cost * 100;
			mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`heading`,`src_table`,`src_id`,`qty`,`uom`,`cost`,`profit`,`margin`,`price`,`retail`,`sort_order`)
				VALUES ('$estimateid','Services','services','$id','$qty','$uom','$cost','$profit','$margin','$price','$total','$sort')");
			$sort++;
		}
	}

	$before_change = '';
	$history = "Estimates scope entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	// Products
	foreach(explode('**',$estimate['products']) as $products) {
		$products = explode('#',$products);
		$id = $products[0];
		if($id > 0) {
			$total = $producst[1];
			$price = $products[1] / $products[2];
			$qty = $products[2];
			$uom = $products[3];
			$cost = $price - $products[4];
			$profit = $price - $cost;
			$margin = $profit / $cost * 100;
			mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`heading`,`src_table`,`src_id`,`qty`,`uom`,`cost`,`profit`,`margin`,`price`,`retail`,`sort_order`)
				VALUES ('$estimateid','Products','products','$id','$qty','$uom','$cost','$profit','$margin','$price','$total','$sort')");
			$sort++;
		}
	}
	$before_change = '';
	$history = "Estimates scope entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	// SRED
	foreach(explode('**',$estimate['sred']) as $sred) {
		$sred = explode('#',$sred);
		$id = $sred[0];
		if($id > 0) {
			$total = $sred[1];
			$price = $sred[1] / $sred[2];
			$qty = $sred[2];
			$details = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `sred` WHERE `productid`='$id'"));
			$cost = $details['cost'];
			$profit = $price - $cost;
			$margin = $profit / $cost * 100;
			mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`heading`,`src_table`,`src_id`,`qty`,`uom`,`cost`,`profit`,`margin`,`price`,`retail`,`sort_order`)
				VALUES ('$estimateid','SR&ED','sred','$id',1,'$cost','$profit','$margin','$price','$total','$sort')");
			$sort++;
		}
	}
	$before_change = '';
	$history = "Estimates scope entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	// Labour
	foreach(explode('**',$estimate['labour']) as $labour) {
		$labour = explode('#',$labour);
		$id = $labour[0];
		if($id > 0) {
			$total = $labour[1];
			$price = $labour[1] / $labour[2];
			$qty = $labour[2];
			$uom = $labour[3];
			$cost = $price - $labour[4];
			$profit = $price - $cost;
			$margin = $profit / $cost * 100;
			mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`heading`,`src_table`,`src_id`,`qty`,`cost`,`profit`,`margin`,`price`,`retail`,`sort_order`)
				VALUES ('$estimateid','Labour','labour','$id',1,'$cost','$profit','$margin','$price','$total','$sort')");
			$sort++;
		}
	}
	$before_change = '';
	$history = "Estimates scope entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	// Client
	foreach(explode('**',$estimate['client']) as $client) {
		$client = explode('#',$client);
		$id = $client[0];
		if($id > 0) {
			$price = $client[1];
			$cost = 0;
			$profit = $price - $cost;
			$margin = 0;
			mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`heading`,`src_table`,`src_id`,`qty`,`cost`,`profit`,`margin`,`price`,`retail`,`sort_order`)
				VALUES ('$estimateid','Client','client','$id',1,'$cost','$profit','$margin','$price','$price','$sort')");
			$sort++;
		}
	}
	$before_change = '';
	$history = "Estimates scope entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	// Customer
	foreach(explode('**',$estimate['customer']) as $customer) {
		$customer = explode('#',$customer);
		$id = $customer[0];
		if($id > 0) {
			$price = $customer[1];
			$cost = 0;
			$profit = $price - $cost;
			$margin = 0;
			mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`heading`,`src_table`,`src_id`,`qty`,`cost`,`profit`,`margin`,`price`,`retail`,`sort_order`)
				VALUES ('$estimateid','Customers','contacts','$id',1,'$cost','$profit','$margin','$price','$price','$sort')");
			$sort++;
		}
	}
	$before_change = '';
	$history = "Estimates scope entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	// Inventory
	foreach(explode('**',$estimate['inventory']) as $inventory) {
		$inventory = explode('#',$inventory);
		$id = $inventory[0];
		if($id > 0) {
			$total = $inventory[1];
			$price = $inventory[1] / $inventory[2];
			$qty = $inventory[2];
			$uom = $inventory[3];
			$cost = $price - $inventory[4];
			$profit = $price - $cost;
			$margin = $profit / $cost * 100;
			mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`heading`,`src_table`,`src_id`,`qty`,`uom`,`cost`,`profit`,`margin`,`price`,`retail`,`sort_order`)
				VALUES ('$estimateid','Inventory','inventory','$id','$qty','$uom','$cost','$profit','$margin','$price','$total','$sort')");
			$sort++;
		}
	}
	$before_change = '';
	$history = "Estimates scope entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	// Equipment
	foreach(explode('**',$estimate['equipment']) as $equipment) {
		$equipment = explode('#',$equipment);
		$id = $equipment[0];
		if($id > 0) {
			$total = $equipment[1];
			$price = $equipment[1] / $equipment[2];
			$qty = $equipment[2];
			$uom = $equipment[3];
			$cost = $price - $equipment[4];
			$profit = $price - $cost;
			$margin = $profit / $cost * 100;
			mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`heading`,`src_table`,`src_id`,`qty`,`uom`,`cost`,`profit`,`margin`,`price`,`retail`,`sort_order`)
				VALUES ('$estimateid','Equipment','equipment','$id','$qty','$uom','$cost','$profit','$margin','$price','$total','$sort')");
			$sort++;
		}
	}
	$before_change = '';
	$history = "Estimates scope entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	// Staff
	foreach(explode('**',$estimate['staff']) as $staff) {
		$staff = explode('#',$staff);
		$id = $staff[0];
		if($id > 0) {
			$total = $staff[1];
			$price = $staff[1] / $staff[2];
			$qty = $staff[2];
			$cost = 0;
			$profit = $price - $cost;
			$margin = 0;
			mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`heading`,`src_table`,`src_id`,`qty`,`cost`,`profit`,`margin`,`price`,`retail`,`sort_order`)
				VALUES ('$estimateid','Staff','contacts','$id','$qty','$cost','$profit','$margin','$price','$price','$sort')");
			$sort++;
		}
	}
	$before_change = '';
	$history = "Estimates scope entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	// Contractor
	foreach(explode('**',$estimate['contractor']) as $contractor) {
		$contractor = explode('#',$contractor);
		$id = $contractor[0];
		if($id > 0) {
			$total = $contractor[1];
			$price = $contractor[1] / $contractor[2];
			$qty = $contractor[2];
			$cost = 0;
			$profit = $price - $cost;
			$margin = 0;
			mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`heading`,`src_table`,`src_id`,`qty`,`cost`,`profit`,`margin`,`price`,`retail`,`sort_order`)
				VALUES ('$estimateid','Contractor','contacts','$id','$qty','$cost','$profit','$margin','$price','$price','$sort')");
			$sort++;
		}
	}
	$before_change = '';
	$history = "Estimates scope entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	// Expense
	foreach(explode('**',$estimate['services']) as $services) {
		$services = explode('#',$services);
		$id = $services[0];
		if($id > 0) {
			$total = $services[1];
			$cost = 0;
			$profit = $price - $cost;
			$margin = 0;
			mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`heading`,`description`,`qty=`,`cost`,`profit`,`margin`,`price`,`retail`,`sort_order`)
				VALUES ('$estimateid','Expense','miscellaneous','$id=','$cost','$profit','$margin','$price','$total','$sort')");
			$sort++;
		}
	}
	$before_change = '';
	$history = "Estimates scope entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	// Vendor
	foreach(explode('**',$estimate['vendor']) as $vendor) {
		$vendor = explode('#',$vendor);
		$id = $vendor[0];
		if($id > 0) {
			$total = $vendor[1];
			$price = $vendor[1] / $vendor[2];
			$qty = $vendor[2];
			$details = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `vendor_price_list` WHERE `inventoryid`='$id'"));
			$cost = $details['cost'];
			$profit = $price - $cost;
			$margin = $profit / $cost * 100;
			mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`heading`,`src_table`,`src_id`,`qty`,`cost`,`profit`,`margin`,`price`,`retail`,`sort_order`)
				VALUES ('$estimateid','Vendor','vpl','$id','$qty','$cost','$profit','$margin','$price','$total','$sort')");
			$sort++;
		}
	}
	$before_change = '';
	$history = "Estimates scope entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	// Custom
	foreach(explode('**',$estimate['custom']) as $custom) {
		$custom = explode('#',$custom);
		$id = $custom[0];
		if($id > 0) {
			$price = $custom[1];
			$details = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `custom` WHERE `customid`='$id'"));
			$cost = $details['cost'];
			$profit = $price - $cost;
			$margin = $profit / $cost * 100;
			mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`heading`,`src_table`,`src_id`,`qty`,`cost`,`profit`,`margin`,`price`,`retail`,`sort_order`)
				VALUES ('$estimateid','Custom','custom','$id',1,'$cost','$profit','$margin','$price','$price','$sort')");
			$sort++;
		}
	}
	$before_change = '';
	$history = "Estimates scope entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	// Other
	foreach(explode('**',$estimate['other']) as $other) {
		$other = explode('#',$other);
		$description = $other[0];
		if($description != '') {
			$price = $other[1];
			$cost = 0;
			$profit = $price - $cost;
			$margin = 0;
			mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`heading`,`description`,`qty`,`cost`,`profit`,`margin`,`price`,`retail`,`sort_order`)
				VALUES ('$estimateid','Customers','$description',1,'$cost','$profit','$margin','$price','$price','$sort')");
			$sort++;
		}
	}
	$before_change = '';
	$history = "Estimates scope entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	// Rate Card Items
	$estimate_ratecard = mysqli_query($dbc, "SELECT * FROM `estimate_company_rate_card` WHERE `estimateid`='$estimateid' AND `deleted`=0");
	while($ratecard = mysqli_fetch_array($estimate_ratecard)) {
		mysqli_query($dbc, "INSERT INTO (`estimateid`,`heading`,`description`,`uom`,`qty`,`cost`,`profit`,`margin`,`price`,`retail`,`multiple`,`sort_order`)
			VALUES ('$estimateid','{$ratecard['tile_name']}','{$ratecard['description']}','{$ratecard['uom']}','{$ratecard['qty']}','{$ratecard['cost']}','{$ratecard['profit']}','{$ratecard['margin']}','{$ratecard['cust_price']}','{$ratecard['rc_total']}','{$ratecard['total_multiple']}','$sort')");
		$sort++;
	}

	// Miscellaneous Items
	$estimate_misc = mysqli_query($dbc, "SELECT * FROM `estimate_misc` WHERE `estimateid`='$estimateid' AND `deleted`=0");
	while($misc = mysqli_fetch_array($estimate_misc)) {
		mysqli_query($dbc, "INSERT INTO (`estimateid`,`heading`,`description`,`uom`,`qty`,`cost`,`profit`,`margin`,`price`,`retail`,`multiple`,`sort_order`)
			VALUES ('$estimateid','{$misc['accordion']}','{$misc['heading']} - {$misc['description']}','{$misc['uom']}','{$misc['qty']}','{$misc['cost']}','".($misc['price'] - $misc['cost'])."','".(($misc['price'] - $misc['cost']) / $misc['cost'])."','{$misc['estimate_price']}','{$misc['total']}','{$misc['total_multiple']}','$sort')");
		$sort++;
	}
	$before_change = capture_before_change($dbc, 'estimate_misc', 'deleted', 'estimateid', $estimateid);
	mysqli_query($dbc, "UPDATE `estimate_misc` SET `deleted`=0 WHERE `estimateid`='$estimateid'");
	$history = capture_after_change('deleted', 0);
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
} ?>
