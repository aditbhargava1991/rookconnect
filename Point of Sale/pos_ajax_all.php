<?php
error_reporting(0);
include ('../database_connection.php');
include ('../function.php');
include ('../global.php');
ob_clean();

if($_GET['fill'] == 'posPromotion') {
    $pro = $_GET['pro'];
    $qty = $_GET['qty'];
    $productPrice = $_GET['productPrice'];

    $pos_promotion = explode('*#*',get_config($dbc, 'pos_promotion'));
    $total_promo_count = mb_substr_count(get_config($dbc, 'pos_promotion'),'*#*');
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
    echo $result['pricing_level'].'**'.$result['client_tax_exemption'].'**'.$result['tax_exemption_number'].'**'.$ship_address;
}

if($_GET['fill'] == 'posFromCategory') {
    $name = $_GET['name'];

	$query = mysqli_query($dbc,"SELECT inventoryid, part_no, name FROM inventory WHERE category='$name'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['inventoryid']."'>".$row['part_no'].'</option>';
	}
    echo '*#*';
	$query = mysqli_query($dbc,"SELECT inventoryid, part_no, name FROM inventory WHERE category='$name' AND deleted=0");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['inventoryid']."'>".$row['name'].'</option>';
	}
}

/* Apply Gift Card */
if ( $_GET['fill'] == 'posGF' ) {
  $gf_number = $_GET['gf_number'];
  $today_date = date('Y-m-d');
  $gf_row = mysqli_fetch_assoc( mysqli_query( $dbc, "select value from pos_giftcards where giftcard_number = '$gf_number' and deleted = 0 and status = 0 and issue_date <= '$today_date' AND expiry_date >= '$today_date'"));
  if($gf_row['value'] == null || $gf_row['value'] == '') {
    echo "na";
  }
  else {
  	echo $gf_row['value'];
  }
}

if($_GET['fill'] == 'posFromCategoryserv') {
    $name = $_GET['name'];

	$query = mysqli_query($dbc,"SELECT serviceid, service_type, heading FROM services WHERE category='$name'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['serviceid']."'>".$row['service_type'].'</option>';
	}
    echo '*#*';

	$query = mysqli_query($dbc,"SELECT serviceid, service_type, heading FROM services WHERE category='$name'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['serviceid']."'>".$row['heading'].'</option>';
	}
}

if($_GET['fill'] == 'posFromCategoryprod') {
    $name = $_GET['name'];

	$query = mysqli_query($dbc,"SELECT productid, product_type, heading FROM products WHERE category='$name'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['productid']."'>".$row['product_type'].'</option>';
	}
    echo '*#*';
	$query = mysqli_query($dbc,"SELECT productid, product_type, heading FROM products WHERE category='$name'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['productid']."'>".$row['heading'].'</option>';
	}
}
// PRODUCTS VV
if($_GET['fill'] == 'posUpProductFromProductprod') {
    $inventoryid = $_GET['inventoryid'];
    $productPrice = $_GET['productPrice'];
    $category = $_GET['category'];

    if($category == '') {
        $category = get_inventory($dbc, $inventoryid, 'category');
    }
	$query = mysqli_query($dbc,"SELECT distinct(category) FROM products WHERE deleted=0");
	echo '<option value=""></option>';
	$result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT category FROM products WHERE productid='$inventoryid'"));
	while($row = mysqli_fetch_array($query)) {
        if ($result['category'] == $row['category']) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
		echo "<option ".$selected." value='".$row['category']."'>".$row['category'].'</option>';
	}

    echo '**##**';

	$query = mysqli_query($dbc,"SELECT productid, product_type FROM products WHERE deleted=0");
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

	$query = mysqli_query($dbc,"SELECT productid, heading FROM products WHERE deleted=0");
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

    $result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT $productPrice FROM products WHERE productid='$inventoryid'"));
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
    $category = $_GET['category'];

    if($category == '') {
        $category = get_inventory($dbc, $inventoryid, 'category');
    }
	$query = mysqli_query($dbc,"SELECT distinct(category) FROM services WHERE deleted=0");
	echo '<option value=""></option>';
	$result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT category FROM services WHERE serviceid='$inventoryid'"));
	while($row = mysqli_fetch_array($query)) {
        if ($result['category'] == $row['category']) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
		echo "<option ".$selected." value='".$row['category']."'>".$row['category'].'</option>';
	}

    echo '**##**';

	$query = mysqli_query($dbc,"SELECT serviceid, service_type FROM services WHERE deleted=0");
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

	$query = mysqli_query($dbc,"SELECT serviceid, heading FROM services WHERE deleted=0");
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

    $result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT $productPrice FROM services WHERE serviceid='$inventoryid'"));
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
    $category = $_GET['category'];

    //if($category == '') {
        $category = get_inventory($dbc, $inventoryid, 'category');
    //}
	$query = mysqli_query($dbc,"SELECT distinct(category) FROM inventory WHERE deleted=0");
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

	$query = mysqli_query($dbc,"SELECT inventoryid, part_no FROM inventory WHERE deleted=0");
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

	$query = mysqli_query($dbc,"SELECT inventoryid, name FROM inventory WHERE deleted=0 ORDER BY name");
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

    $result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT $productPrice FROM inventory WHERE inventoryid='$inventoryid'"));
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
    $before_change = capture_before_change($dbc, 'point_of_sell', 'deleted', 'posid', $name);
    $before_change .= capture_before_change($dbc, 'point_of_sell', 'status', 'posid', $name);
    $before_change .= capture_before_change($dbc, 'point_of_sell', 'status_history', 'posid', $name);

    $query_update = "UPDATE `point_of_sell` SET deleted = '1', status = '$status', status_history = '$status_history' WHERE posid='$name'";
	} else {
    $before_change = capture_before_change($dbc, 'point_of_sell', 'status', 'posid', $name);
    $before_change .= capture_before_change($dbc, 'point_of_sell', 'status_history', 'posid', $name);

		$query_update = "UPDATE `point_of_sell` SET status = '$status', status_history = '$status_history' WHERE posid='$name'";
	}
    $result_update = mysqli_query($dbc, $query_update);

    $history = capture_after_change('deleted', '1');
    $history .= capture_after_change('status', $status);
    $history .= capture_after_change('status_history', $status_history);
		add_update_history($dbc, 'pos_history', $history, '', $before_change);
}

if($_GET['fill'] == 'customer') {
    $name = $_GET['name'];
    $result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM customer WHERE customerid='$name'"));
	echo $result['phone'].'#$#'.$result['customer'].'#$#'.$result['email'].'#$#'.$result['reference'].'#$#'.$result['pricing'];
}
?>
