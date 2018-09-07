<?php
include ('../database_connection.php');
include ('../function.php');
include ('../global.php');
error_reporting(0);
if($_GET['fill'] == 'posPromotion') {
    $pro = $_GET['pro'];
    $qty = $_GET['qty'];
    $productPrice = $_GET['productPrice'];

    $pos_promotion = explode('*#*',get_config($dbc, 'purchase_order_promotion'));
    $total_promo_count = mb_substr_count(get_config($dbc, 'purchase_order_promotion'),'*#*');
    $pos_promo_inventory = array();
    for($eq_loop=0; $eq_loop<=$total_promo_count; $eq_loop++) {
        $pos_promotion_data = explode('**',$pos_promotion[$eq_loop]);

        if($pos_promotion_data[0] == $pro) {
            if($qty >= $pos_promotion_data[1]) {
                $pricing =  $pos_promotion_data[2];
                echo get_inventory($dbc, $pro, $pricing);
            } else {
                echo get_inventory($dbc, $pro, $productPrice);
            }
        }
    }
}

if($_GET['fill'] == 'POSclient') {
    $contactid = $_GET['clientid'];
    $result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT contactid, pricing_level, client_tax_exemption, tax_exemption_number  FROM contacts WHERE contactid='$contactid'"));
    $ship_address = get_ship_address($dbc, $contactid);

	/* Populate Project drop down */
	$options_project = '<option value=""></option>';
	$result_project = mysqli_query($dbc, "SELECT p.`projectid`, p.`businessid`, p.`project_name` FROM `project` p LEFT JOIN `contacts` c ON (c.`contactid` = p.`businessid`) WHERE c.`contactid` = {$contactid} AND p.`deleted`=0 ORDER BY p.`project_name`");
    while($row_project = mysqli_fetch_assoc($result_project)) {
		$options_project .= "<option value='" . $row_project['projectid'] . "'>" . $row_project['project_name'] . "</option>";
	}

	/* Populate Ticket drop down */
	$options_ticket = '<option value=""></option>';
	$result_ticket = mysqli_query($dbc, "
		SELECT
			t.`ticketid`, t.`heading`
		FROM
			`tickets` t
		LEFT JOIN
			`contacts` c1 ON (c1.`contactid` = t.`businessid`)
		LEFT JOIN
			`contacts` c2 ON (c2.`contactid` = t.`clientid`)
		LEFT JOIN
			`contacts` c3 ON (c3.`contactid` = t.`contactid`)
		WHERE
			(c1.`contactid` = {$contactid} OR c2.`contactid` = {$contactid} OR c3.`contactid` = {$contactid}) AND
			t.`status` != 'Archived'
		ORDER BY
			t.`created_date` DESC");
    while($row_ticket = mysqli_fetch_assoc($result_ticket)) {
		$options_ticket .= "<option value='" . $row_ticket['ticketid'] . "'>" . $row_ticket['heading'] . "</option>";
	}

	/* Populate Workorder drop down
	$options_workorder = '<option value=""></option>';
	$result_workorder = mysqli_query($dbc, "
		SELECT
			w.`workorderid`, w.`contactid`, w.`heading`
		FROM
			`workorder` w
		JOIN
			`contacts` c1 ON (c1.`contactid` = w.`businessid`)
		JOIN
			`contacts` c2 ON (c2.`contactid` = w.`contactid`)
		WHERE
			(c1.`contactid` LIKE '%,{$contactid},%' OR c2.`contactid` = '%,{$contactid},%')
		ORDER BY
			w.`created_date` DESC");
    while($row_workorder = mysqli_fetch_assoc($result_workorder)) {
		$options_workorder .= "<option value='" . $row_workorder['workorderid'] . "'>" . $row_workorder['heading'] . "</option>";
	} */

	echo $result['pricing_level'].'**'.$result['client_tax_exemption'].'**'.$result['tax_exemption_number'].'**'.$ship_address.'**'.$options_project.'**'.$options_ticket;
}

if($_GET['fill'] == 'cross_software_approval') {
	$id = $_GET['status'];
	$dbc_conn = $_GET['dbc'];
	$dbc_cross = ${'dbc_cross_'.$dbc_conn};
	if(isset($_GET['disapprove'])) {
		$message = $_GET['name'];
    $before_change = capture_before_change($dbc, 'purchase_orders', 'cross_software_approval', 'posid', $id);
    $before_change .= capture_before_change($dbc, 'purchase_orders', 'cross_software_disapproval', 'posid', $id);
		mysqli_query($dbc_cross,"UPDATE `purchase_orders` SET cross_software_approval = 'disapproved', cross_software_disapproval = '$message' WHERE posid='$id'") or die(mysqli_error($dbc_cross));
    $history = capture_after_change('cross_software_approval', 'disapproved');
    $history .= capture_after_change('cross_software_disapproval', $message);
	  add_update_history($dbc, 'po_history', $history, '', $before_change);
	} else {
    $before_change = capture_before_change($dbc, 'purchase_orders', 'cross_software_approval', 'posid', $id);
		mysqli_query($dbc_cross,"UPDATE `purchase_orders` SET cross_software_approval = '1' WHERE posid='$id'") or die(mysqli_error($dbc_cross));
    $history = capture_after_change('cross_software_approval', '1');
	  add_update_history($dbc, 'po_history', $history, '', $before_change);

		//Insert into Point of Sale as Accounts Receivable
		$po = mysqli_fetch_array(mysqli_query($dbc_cross, "SELECT * FROM `purchase_orders` WHERE `posid`='$id'"));
		$products = mysqli_query($dbc_cross, "SELECT * FROM `purchase_orders_product` WHERE `posid`='$id'");

		$invoice_date = $po[''];
		$result = mysqli_query($dbc, "INSERT INTO `point_of_sell` (`invoice_date`, `productpricing`, `sub_total`, `discount_type`, `discount_value`, `total_after_discount`, `delivery`, `assembly`, `total_before_tax`, `client_tax_exemption`, `tax_exemption_number`, `total_price`, `payment_type`, `comment`, `ship_date`, `due_date`, `status`, `status_history`, `gst`, `pst`, `delivery_type`, `delivery_address`, `deposit_paid`, `updatedtotal`, `cross_software`, `software_author`, `software_seller`)
			VALUES ('".$po['invoice_date']."', '".$po['productpricing']."', '".$po['sub_total']."', '".$po['discount_type']."', '".$po['discount_value']."', '".$po['total_after_discount']."', '".$po['delivery']."', '".$po['assembly']."', '".$po['total_before_tax']."', '".$po['client_tax_exemption']."', '".$po['tax_exemption_number']."', '".$po['total_price']."', '".$po['payment_type']."', '".$po['comment']."', '".$po['ship_date']."', '".$po['due_date']."', '".$po['status']."', '".$po['status_history']."', '".$po['gst_total']."', '".$po['pst_total']."', '".$po['delivery_type']."', '".$po['delivery_address']."', '".$po['dep_paid']."', '".$po['updatedtotal']."', '".$po['cross_software']."', '".$po['software_author']."', '".$po['software_seller']."')");
		$local_id = mysqli_insert_id($dbc);
    $before_change = '';
    $history = "Point of sell entry has been added. <br />";
    add_update_history($dbc, 'po_history', $history, '', $before_change);

		while($product = mysqli_fetch_array($products)) {
			$result = mysqli_query($dbc, "INSERT INTO `point_of_sell_product` (`posid`, `inventoryid`, `misc_product`, `quantity`, `price`, `gst`, `pst`, `type_category`)
				VALUES ('$local_id', '".$product['inventoryid']."', '".$product['misc_product']."', '".$product['quantity']."', '".$product['price']."', '".$product['gst']."', '".$product['pst']."', '".$product['type_category']."')");
		}

    $before_change = '';
    $history = "Point of sell products have been added. <br />";
    add_update_history($dbc, 'po_history', $history, '', $before_change);

		include ('../Point of Sale/create_pos_pdf.php');
		$pos_design = get_config($dbc, 'pos_design');

		if($pos_design == 1) {
			echo create_pos1_pdf($dbc,$local_id,($po['discount_type'] == '$' ? '$' : '').$po['discount_value'].($po['discount_type'] == '%' ? '%' : ''),$po['comment'], $po['gst_total'], $po['pst_total'], $rookconnect, 0);
		}

		if($pos_design == 2) {
			echo create_pos2_pdf($dbc,$local_id,($po['discount_type'] == '$' ? '$' : '').$po['discount_value'].($po['discount_type'] == '%' ? '%' : ''),$po['comment'], $po['gst_total'], $po['pst_total'], $rookconnect, 0);
		}

		if($pos_design == 3) {
			echo create_pos3_pdf($dbc,$local_id,($po['discount_type'] == '$' ? '$' : '').$po['discount_value'].($po['discount_type'] == '%' ? '%' : ''),$po['comment'], $po['gst_total'], $po['pst_total'], $rookconnect, 0, COMPANY_SOFTWARE_NAME);
		}
	}
}

if($_GET['fill'] == 'posFromCategory') {
    $name = $_GET['name'];

	$query = mysqli_query($dbc,"SELECT inventoryid, part_no, name FROM inventory WHERE category='$name' AND include_in_po != '' AND `deleted`=0");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['inventoryid']."'>".$row['part_no'].'</option>';
	}
    echo '*#*';
	$query = mysqli_query($dbc,"SELECT inventoryid, part_no, name FROM inventory WHERE category='$name' AND include_in_po != '' AND `deleted`=0");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['inventoryid']."'>".$row['name'].'</option>';
	}
}
// VPL
if($_GET['fill'] == 'posFromCategoryvpl') {
    $name = $_GET['name'];

	$query = mysqli_query($dbc,"SELECT inventoryid, part_no, name FROM vendor_price_list WHERE category='$name' AND include_in_po != '' AND `deleted`=0");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['inventoryid']."'>".$row['part_no'].'</option>';
	}
    echo '*#*';
	$query = mysqli_query($dbc,"SELECT inventoryid, part_no, name FROM vendor_price_list WHERE category='$name' AND include_in_po != '' AND `deleted`=0");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['inventoryid']."'>".$row['name'].'</option>';
	}
}
// SERVICES
if($_GET['fill'] == 'posFromCategoryserv') {
    $name = $_GET['name'];

	$query = mysqli_query($dbc,"SELECT serviceid, service_type, heading FROM services WHERE category='$name' AND include_in_po != '' AND `deleted`=0");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['serviceid']."'>".$row['service_type'].'</option>';
	}
    echo '*#*';

	$query = mysqli_query($dbc,"SELECT serviceid, service_type, heading FROM services WHERE category='$name' AND include_in_po != '' AND `deleted`=0");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['serviceid']."'>".$row['heading'].'</option>';
	}
}
// PRODUCTS
if($_GET['fill'] == 'posFromCategoryprod') {
    $name = $_GET['name'];

	$query = mysqli_query($dbc,"SELECT productid, product_type, heading FROM products WHERE category='$name' AND include_in_po != '' AND `deleted`=0");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['productid']."'>".$row['product_type'].'</option>';
	}
    echo '*#*';
	$query = mysqli_query($dbc,"SELECT productid, product_type, heading FROM products WHERE category='$name' AND include_in_po != '' AND `deleted`=0");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['productid']."'>".$row['heading'].'</option>';
	}
}
// PRODUCTS VV
if($_GET['fill'] == 'posUpProductFromProductprod') {
    $inventoryid = $_GET['inventoryid'];
    $productPrice = $_GET['productPrice'];
   // $category = $_GET['category'];

        $category = get_products($dbc, $inventoryid, 'category');

	$query = mysqli_query($dbc,"SELECT distinct(category) FROM products WHERE deleted=0 AND include_in_po != ''");
	echo '<option value=""></option>';
	$result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT category FROM products WHERE productid='$inventoryid' AND include_in_po != ''"));
	while($row = mysqli_fetch_array($query)) {
        if ($result['category'] == $row['category']) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
		echo "<option ".$selected." value='".$row['category']."'>".$row['category'].'</option>';
	}

    echo '**##**';

	$query = mysqli_query($dbc,"SELECT productid, product_type FROM products WHERE deleted=0 AND include_in_po != ''");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
        if ($inventoryid == $row['productid']) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
		echo "<option ".$selected." value='".$row['productid']."'>".$row['product_type'].'</option>';
	}

    echo '**##**';

	$query = mysqli_query($dbc,"SELECT productid, heading FROM products WHERE deleted=0 AND include_in_po != ''");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
        if ($inventoryid == $row['productid']) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
		echo "<option ".$selected." value='".$row['productid']."'>".$row['heading'].'</option>';
	}

    echo '**##**';

    $result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT $productPrice FROM products WHERE productid='$inventoryid' AND include_in_po != ''"));
	if($result[$productPrice] == '' || $result[$productPrice] == NULL ) {
		echo '0';
	} else {
		echo $result[$productPrice];
	}

}
// FOR SERVICES VV
if($_GET['fill'] == 'posUpProductFromProductserv') {
    $inventoryid = $_GET['inventoryid'];
    $productPrice = $_GET['productPrice'];
   // $category = $_GET['category'];

        $category = get_services($dbc, $inventoryid, 'category');

	$query = mysqli_query($dbc,"SELECT distinct(category) FROM services WHERE deleted=0 AND include_in_po != ''");
	echo '<option value=""></option>';
	$result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT category FROM services WHERE serviceid='$inventoryid' AND include_in_po != ''"));
	while($row = mysqli_fetch_array($query)) {
        if ($result['category'] == $row['category']) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
		echo "<option ".$selected." value='".$row['category']."'>".$row['category'].'</option>';
	}

    echo '**##**';

	$query = mysqli_query($dbc,"SELECT serviceid, service_type FROM services WHERE deleted=0 AND include_in_po != ''");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
        if ($inventoryid == $row['serviceid']) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
		echo "<option ".$selected." value='".$row['serviceid']."'>".$row['service_type'].'</option>';
	}

    echo '**##**';

	$query = mysqli_query($dbc,"SELECT serviceid, heading FROM services WHERE deleted=0 AND include_in_po != ''");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
        if ($inventoryid == $row['serviceid']) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
		echo "<option ".$selected." value='".$row['serviceid']."'>".$row['heading'].'</option>';
	}

    echo '**##**';

    $result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT $productPrice FROM services WHERE serviceid='$inventoryid' AND include_in_po != ''"));
	if($result[$productPrice] == '' || $result[$productPrice] == NULL ) {
		echo '0';
	} else {
		echo $result[$productPrice];
	}
}
// FOR INVENTORY VV
if($_GET['fill'] == 'posUpProductFromProduct') {
    $inventoryid = $_GET['inventoryid'];
    $productPrice = $_GET['productPrice'];

        $category = get_inventory($dbc, $inventoryid, 'category');

	$query = mysqli_query($dbc,"SELECT distinct(category) FROM inventory WHERE deleted=0 AND include_in_po != ''");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
        if ($category == $row['category']) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
		echo "<option ".$selected." value='".$row['category']."'>".$row['category'].'</option>';
	}

    echo '**##**';

	$query = mysqli_query($dbc,"SELECT inventoryid, part_no FROM inventory WHERE deleted=0 AND include_in_po != ''");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
        if ($inventoryid == $row['inventoryid']) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
		echo "<option ".$selected." value='".$row['inventoryid']."'>".$row['part_no'].'</option>';
	}

    echo '**##**';

	$query = mysqli_query($dbc,"SELECT inventoryid, name FROM inventory WHERE deleted=0 AND include_in_po != ''");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
        if ($inventoryid == $row['inventoryid']) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
		echo "<option ".$selected." value='".$row['inventoryid']."'>".$row['name'].'</option>';
	}

    echo '**##**';

    $result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT $productPrice FROM inventory WHERE inventoryid='$inventoryid' AND include_in_po != ''"));
	if($result[$productPrice] == '' || $result[$productPrice] == NULL ) {
		echo '0';
	} else {
		echo $result[$productPrice];
	}

}

// FOR VPL VV
if($_GET['fill'] == 'posUpProductFromProductvpl') {
    $inventoryid = $_GET['inventoryid'];
    $productPrice = $_GET['productPrice'];

        $category = get_vpl($dbc, $inventoryid, 'category');

	$query = mysqli_query($dbc,"SELECT distinct(category) FROM vendor_price_list WHERE deleted=0 AND include_in_po != ''");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
        if ($category == $row['category']) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
		echo "<option class='".$category."' ".$selected." value='".$row['category']."'>".$row['category'].'</option>';
	}

    echo '**##**';

	$query = mysqli_query($dbc,"SELECT inventoryid, part_no FROM vendor_price_list WHERE deleted=0 AND include_in_po != ''");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
        if ($inventoryid == $row['inventoryid']) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
		echo "<option ".$selected." value='".$row['inventoryid']."'>".$row['part_no'].'</option>';
	}

    echo '**##**';

	$query = mysqli_query($dbc,"SELECT inventoryid, name FROM vendor_price_list WHERE deleted=0 AND include_in_po != ''");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
        if ($inventoryid == $row['inventoryid']) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
		echo "<option ".$selected." value='".$row['inventoryid']."'>".$row['name'].'</option>';
	}

    echo '**##**';

    $result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT $productPrice FROM vendor_price_list WHERE inventoryid='$inventoryid' AND include_in_po != ''"));
	if($result[$productPrice] == '' || $result[$productPrice] == NULL ) {
		echo '0';
	} else {
		echo $result[$productPrice];
	}

}

///
if($_GET['fill'] == 'POSstatus') {
    $name = $_GET['name'];
    $status = $_GET['status'];
    $status_history = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed to '.$status.' on '.date('Y-m-d');
	if($status == 'Archived') {
		$status_history = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed to Archived on '.date('Y-m-d');
    $before_change = capture_before_change($dbc, 'purchase_orders', 'deleted', 'posid', $name);
    $before_change .= capture_before_change($dbc, 'purchase_orders', 'status_history', 'posid', $name);
		$query_update = "UPDATE `purchase_orders` SET deleted = '1', status_history = '$status_history' WHERE posid='$name'";
	} else {
    $before_change = capture_before_change($dbc, 'purchase_orders', 'status', 'posid', $name);
    $before_change .= capture_before_change($dbc, 'purchase_orders', 'status_history', 'posid', $name);
		$query_update = "UPDATE `purchase_orders` SET status = '$status', status_history = '$status_history' WHERE posid='$name'";
	}
    $result_update = mysqli_query($dbc, $query_update);
    $history = capture_after_change('deleted', '1');
    $history .= capture_after_change('status', $status);
    $history .= capture_after_change('status_history', $status_history);
	  add_update_history($dbc, 'po_history', $history, '', $before_change);
}

if($_GET['fill'] == 'customer') {
    $name = $_GET['name'];
    $result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM customer WHERE customerid='$name'"));
	echo $result['phone'].'#$#'.$result['customer'].'#$#'.$result['email'].'#$#'.$result['reference'].'#$#'.$result['pricing'];
}

if($_GET['fill'] == 'pay_po') {
    $val = $_GET['val'];
	$id = $_GET['id'];
  $before_change = capture_before_change($dbc, 'purchase_orders_product', 'total_paid', 'posproductid', $id);
	$query_update = "UPDATE `purchase_orders_product` SET total_paid = '$val' WHERE posproductid='$id'";
    $result = mysqli_fetch_assoc(mysqli_query($dbc, $query_update));
    $history = capture_after_change('total_paid', $val);
    add_update_history($dbc, 'po_history', $history, '', $before_change);
}

if($_GET['fill'] == 'rec_po') {
    $val		= $_GET['val'];
	$id			= $_GET['id'];
	$inv		= $_GET['inv'];
	$newid = '';
	$chng_qty	= $_GET['chng_qty'];
	$price	= $_GET['price'];
	$averaged = $price;
	$contactid	= $_SESSION['contactid'];
	$datetime	= date('Y/m/d h:i:s a', time());
	if(!isset($_GET['cross'])) {
		$dbcorzen = $dbc;
	} else {
		$dbcorzen = $dbczen;
	}
	if(isset($_GET['inv_update'])) {
		$row = mysqli_fetch_assoc ( mysqli_query ( $dbcorzen, "SELECT * FROM inventory WHERE inventoryid='$inv'") );
		$categ = $row['category'];
		$namer = $row['name'];
		$old_inventory = $row['quantity'];
		$old_cost = $row['average_cost'];
		$averaged = (($old_inventory * $old_cost) + ($chng_qty * $price)) / ($old_inventory + $chng_qty);
		if ( $old_inventory == NULL || $old_inventory == '' ) {
			$old_inventory = '0';
		}
			$new_inv = $old_inventory + $chng_qty;
			$resultw = mysqli_query($dbcorzen, "SELECT * FROM inventory WHERE inventoryid= '".$inv."'");
			while($rww = mysqli_fetch_assoc($resultw)) {
					if(!isset($_GET['cross'])) {
						//check if cross software functionality or not
						$query_add_log = "INSERT INTO `inventory_change_log` (`inventoryid`, `contactid`, `location_of_change`, `old_inventory`, `old_cost`, `changed_quantity`, `current_cost`, `new_inventory`, `new_cost`, `date_time`, `deleted`) VALUES ('$inv', '$contactid', 'Purchase Orders: Receiving', '$old_inventory', '$old_cost', '$chng_qty', '$price', '$new_inv', '$averaged', '$datetime', '0' )";
						mysqli_query($dbc, $query_add_log);

					} else {

						$resultwx = mysqli_query($dbc, "SELECT * FROM inventory WHERE category = '".$rww['category']."' AND name = '".$rww['name']."' AND deleted = 0");
						$numofinv = mysqli_num_rows($resultwx);
						if($numofinv > 0) {
							echo "xx Exists xx";

							$invieid = mysqli_fetch_assoc($resultwx);
							$old_inventory_1 = $invieid['quantity'];
							if ( $old_inventory_1 == NULL || $old_inventory_1 == '' ) {
								$old_inventory_1 = '0';
							}
							$new_inv = $old_inventory_1 + $chng_qty;
							$invieidd = $invieid['inventoryid'];
							$query_add_log = "INSERT INTO `inventory_change_log` (`inventoryid`, `contactid`, `location_of_change`, `old_inventory`, `old_cost`, `changed_quantity`, `current_cost`, `new_inventory`, `new_cost`, `date_time`, `deleted`) VALUES ('$invieidd', '$contactid', 'Purchase Orders: Receiving', '$old_inventory_1', '$old_cost', '$chng_qty', '$price', '$new_inv', '$averaged', '$datetime', '0' )";
							mysqli_query($dbc, $query_add_log);
							$cross_change = -1*$chng_qty;
							$new_inv_cross = $cross_change + $old_inventory;
							$query_add_log = "INSERT INTO `inventory_change_log` (`inventoryid`, `contactid`, `location_of_change`, `old_inventory`, `old_cost`, `changed_quantity`, `current_cost`, `new_inventory`, `new_cost`, `date_time`, `deleted`) VALUES ('$inv', '', 'Purchase Orders: Sending', '$old_inventory', '$old_cost', '$cross_change', '$price', '$new_inv_cross', '$averaged', '$datetime', '0' )";
							mysqli_query($dbczen, $query_add_log);
						} else {

							echo "xx NO Exist xx";

							$query_add_log = "INSERT INTO `inventory` (`category`, `name`, `quantity`) VALUES ('$categ', '$namer', '$chng_qty')";
							mysqli_query($dbc, $query_add_log);
							$newid = mysqli_insert_id($dbc);
							$query_add_log = "INSERT INTO `inventory_change_log` (`inventoryid`, `contactid`, `location_of_change`, `old_inventory`, `old_cost`, `changed_quantity`, `current_cost`, `new_inventory`, `new_cost`, `date_time`, `deleted`) VALUES ('$newid', '$contactid', 'Purchase Orders: Receiving', '0', '$chng_qty', '$chng_qty', '$datetime', '0' )";
							mysqli_query($dbc, $query_add_log);
							$cross_change = -1*$chng_qty;
							$new_inv_cross = $cross_change + $old_inventory;
							$query_add_log = "INSERT INTO `inventory_change_log` (`inventoryid`, `contactid`, `location_of_change`, `old_inventory`, `old_cost`, `changed_quantity`, `current_cost`, `new_inventory`, `new_cost`, `date_time`, `deleted`) VALUES ('$inv', '', 'Purchase Orders: Sending', '$old_inventory', '$cross_change', '$new_inv_cross', '$datetime', '0' )";
							mysqli_query($dbczen, $query_add_log);

						}
					}

			}

	}

	//$query_add_log = "INSERT INTO `inventory_change_log` (`inventoryid`, `contactid`, `old_inventory`, `changed_quantity`, `new_inventory`, `date_time`, `deleted`) VALUES ('$inv', '$contactid', '$old_inventory', '$chng_qty', '$new_inv', '$datetime', '0' )";
	//mysqli_query($dbc, $query_add_log);
	if(isset($_GET['additional_qty'])) {
    $before_change = capture_before_change($dbc, 'purchase_orders_product', 'additional_qty_received', 'posproductid', $id);
		$query_update = "UPDATE `purchase_orders_product` SET additional_qty_received = '$val' WHERE posproductid='$id'";
		$result = mysqli_fetch_assoc(mysqli_query($dbc, $query_update));
    $history = capture_after_change('additional_qty_received', $val);
	  add_update_history($dbc, 'po_history', $history, '', $before_change);
	} else {
    $before_change = capture_before_change($dbc, 'purchase_orders_product', 'qty_received', 'posproductid', $id);
		$query_update = "UPDATE `purchase_orders_product` SET qty_received = '$val' WHERE posproductid='$id'";
		$result = mysqli_fetch_assoc(mysqli_query($dbc, $query_update));
    $history = capture_after_change('qty_received', $val);
	  add_update_history($dbc, 'po_history', $history, '', $before_change);
	}

	if(isset($_GET['inv_update'])) {
		//check if you are updating inventory or some other item ^^
		if(isset($_GET['cross'])) {
		//check if cross software functionality or not
			if($newid !== '') {
				$query_update = "UPDATE `inventory` SET `quantity` = ($old_inventory - $chng_qty) WHERE inventoryid='$inv'";
				$result = mysqli_fetch_assoc(mysqli_query($dbczen, $query_update));
			} else {
				$query_update = "UPDATE `inventory` SET `quantity` = ($old_inventory - $chng_qty) WHERE inventoryid='$inv'";
				echo $query_update;
				$result = mysqli_fetch_assoc(mysqli_query($dbczen, $query_update));
				$query_update = "UPDATE `inventory` SET `quantity` = ($old_inventory_1 + $chng_qty) WHERE inventoryid='$invieidd'";
				$result = mysqli_fetch_assoc(mysqli_query($dbc, $query_update));
			}
		} else {
			$query_update = "UPDATE `inventory` SET `quantity` = ($chng_qty + `quantity`), `average_cost` = '$averaged' WHERE inventoryid='$inv'";
			$result = mysqli_fetch_assoc(mysqli_query($dbc, $query_update));

		}
	}
}

if($_GET['fill'] == 'pay_gst') {
    $val = $_GET['val'];
	$id = $_GET['id'];
  $before_change = capture_before_change($dbc, 'purchase_orders', 'gst_paid', 'posid', $id);
	$query_update = "UPDATE `purchase_orders` SET gst_paid = '$val' WHERE posid='$id'";
    $result = mysqli_fetch_assoc(mysqli_query($dbc, $query_update));
    $history = capture_after_change('gst_paid', $val);
    add_update_history($dbc, 'po_history', $history, '', $before_change);
}

if($_GET['fill'] == 'pay_pst') {
    $val = $_GET['val'];
	$id = $_GET['id'];
  $before_change = capture_before_change($dbc, 'purchase_orders', 'gst_paid', 'posid', $id);
	$query_update = "UPDATE `purchase_orders` SET gst_paid = '$val' WHERE posid='$id'";
    $result = mysqli_fetch_assoc(mysqli_query($dbc, $query_update));
    $history = capture_after_change('gst_paid', $val);
    add_update_history($dbc, 'po_history', $history, '', $before_change);
}

if($_GET['fill'] == 'pay_del') {
    $val = $_GET['val'];
	$id = $_GET['id'];
  $before_change = capture_before_change($dbc, 'purchase_orders', 'delivery_paid', 'posid', $id);
	$query_update = "UPDATE `purchase_orders` SET delivery_paid = '$val' WHERE posid='$id'";
    $result = mysqli_fetch_assoc(mysqli_query($dbc, $query_update));
    $history = capture_after_change('delivery_paid', $val);
    add_update_history($dbc, 'po_history', $history, '', $before_change);
}
if($_GET['fill'] == 'pay_asse') {
    $val = $_GET['val'];
	$id = $_GET['id'];
  $before_change = capture_before_change($dbc, 'purchase_orders', 'assembly_paid', 'posid', $id);
	$query_update = "UPDATE `purchase_orders` SET assembly_paid = '$val' WHERE posid='$id'";
    $result = mysqli_fetch_assoc(mysqli_query($dbc, $query_update));
    $history = capture_after_change('assembly_paid', $val);
    add_update_history($dbc, 'po_history', $history, '', $before_change);
}
if($_GET['fill'] == 'change_id') {
    $val = $_GET['val'];
	$id = $_GET['id'];
  $before_change = capture_before_change($dbc, 'purchase_orders', 'new_id_number', 'posid', $id);
	$query_update = "UPDATE `purchase_orders` SET new_id_number = '$val' WHERE posid='$id'";
    $result = mysqli_fetch_assoc(mysqli_query($dbc, $query_update));
    $history = capture_after_change('new_id_number', $val);
    add_update_history($dbc, 'po_history', $history, '', $before_change);

}

if($_GET['fill'] == 'posUpProductPriceFromProductprod') {
    $productid = $_GET['productid'];
    $productPrice = $_GET['productPrice'];
    $result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT $productPrice  FROM products WHERE productid='$productid'"));
    echo $result[$productPrice];
}

if($_GET['fill'] == 'changeProject') {
    $po = $_GET['po'];
	$id = $_GET['id'];
	$clientid = '';
	if(substr($id,0,1) == 'C') {
		$clientid = substr($id,1);
		$id = '';
	}
  $before_change = capture_before_change($dbc, 'purchase_orders', 'projectid', 'posid', $po);
  $before_change .= capture_before_change($dbc, 'purchase_orders', 'client_projectid', 'posid', $po);
	$query_update = "UPDATE `purchase_orders` SET `projectid`='$id', `client_projectid`='$clientid' WHERE `posid`='$po'";
    $result_update = mysqli_query($dbc, $query_update);
    $history = capture_after_change('projectid', $id);
    $history .= capture_after_change('client_projectid', $clientid);
    add_update_history($dbc, 'po_history', $history, '', $before_change);
}

if($_GET['fill'] == 'changeTicket') {
    $po = $_GET['po'];
	$id = $_GET['id'];
  $before_change = capture_before_change($dbc, 'purchase_orders', 'ticketid', 'posid', $po);
	$query_update = "UPDATE `purchase_orders` SET `ticketid`='$id' WHERE `posid`='$po'";
    $result_update = mysqli_query($dbc, $query_update);
    $history = capture_after_change('ticketid', $id);
    add_update_history($dbc, 'po_history', $history, '', $before_change);
}

if($_GET['fill'] == 'changeWorkOrder') {
    $po = $_GET['po'];
	$id = $_GET['id'];
  $before_change = capture_before_change($dbc, 'purchase_orders', 'workorderid', 'posid', $po);
	$query_update = "UPDATE `purchase_orders` SET `workorderid`='$id' WHERE `posid`='$po'";
    $result_update = mysqli_query($dbc, $query_update);
    $history = capture_after_change('workorderid', $id);
    add_update_history($dbc, 'po_history', $history, '', $before_change);
}
if($_GET['action'] == 'contact_po_numbers') {
	$target = filter_var($_POST['new_po'],FILTER_SANITIZE_STRING);
	$src = filter_var($_POST['old_po'],FILTER_SANITIZE_STRING);
  $before_change = capture_before_change($dbc, 'contact_order_numbers', 'detail', 'category', 'po_number', 'detail', $src, 'deleted', '0');
	$dbc->query("UPDATE `contact_order_numbers` SET `detail`='$target' WHERE `deleted`=0 AND `detail`='$src' AND `category`='po_number'");
  $history = capture_after_change('detail', $target);
  add_update_history($dbc, 'po_history', $history, '', $before_change);
}
if($_GET['action'] == 'contact_po_number_contacts') {
	error_reporting(0);
	ob_clean();
	$contactid = filter_var($_POST['contact'],FILTER_SANITIZE_STRING);
	$detail = filter_var($_POST['po'],FILTER_SANITIZE_STRING);
	$deleted = filter_var($_POST['deleted'],FILTER_SANITIZE_STRING);
	if($_POST['id'] > 0) {
		$dbc->query("UPDATE `contact_order_numbers` SET `contactid`='$contactid', `detail`='$detail', `deleted`='$deleted' WHERE `id`='{$_POST['id']}'");
	} else {
		$dbc->query("INSERT INTO `contact_order_numbers` (`category`, `detail`, `contactid`) VALUES ('po_number','$detail','$contactid')");
		echo $dbc->insert_id;
	}
}
?>
