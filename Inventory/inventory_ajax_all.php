<?php
include ('../database_connection.php');
include ('../function.php');
include ('../global.php');

if($_GET['fill'] == 'prodselectCategory') {
    $name = $_GET['name'];

	$query = mysqli_query($dbc,"SELECT inventoryid, name FROM inventory WHERE category='$name' AND deleted=0");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['inventoryid']."'>".$row['name'].'</option>';
	}
}

if($_GET['fill'] == 'prodselectName') {
    $name = $_GET['name'];

	$query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM inventory WHERE inventoryid = '$name'"));
	echo $query['sell_price'].'*#*'.$query['final_retail_price'].'*#*'.$query['unit_price'].'*#*'.$query['wholesale_price'].'*#*'.$query['commercial_price'].'*#*'.$query['client_price'].'*#*'.$query['preferred_price'].'*#*'.$query['admin_price'].'*#*'.$query['web_price'].'*#*'.$query['commission_price'];
}
if($_GET['fill'] == 'include_in_orders') {
    $id = $_GET['status'];
	$type = $_GET['type'];
	$function = $_GET['function'];
	$name = $_GET['name'];
	$value = $_GET['value'];
	if($type == 'inventoryorderlist') {

		$get_driver = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM order_lists WHERE order_id='$value'"));
		$inventoryidorder = $get_driver['inventoryid'];

		if($function == 'delete') {

		$parts = explode(',', $inventoryidorder);
		$item = $id;
		while(($i = array_search($item, $parts)) !== false) {
			unset($parts[$i]);
		}

		$inventoryid = implode(',', $parts);
		$query_update_employee = "UPDATE `order_lists` SET inventoryid = '$inventoryid' WHERE order_id = '$value'";
		$result_update_employee = mysqli_query($dbc, $query_update_employee);
		} else {
			//Add

			if($inventoryidorder == '' || $inventoryidorder == NULL) {
			} else {
				$id = $inventoryidorder.','.$id;
			}
			$query_update_employee = "UPDATE `order_lists` SET inventoryid = '$id' WHERE order_id = '$value'";
			$result_update_employee = mysqli_query($dbc, $query_update_employee);
		}
		echo $query_update_employee;
	}
}
if($_GET['fill'] == 'actual_inventory') {
	$new_value = $_GET['name'];
	$invid = $_GET['invid'];
	$original_qty = $_GET['original_qty'];
	$order = $_GET['order'];
	$delete = $_GET['edit'];
	if(isset($_GET['location'])) {
		$location = $_GET['location'];
		$name_of_location = $_GET['name_of_location'];
	} else {
		$location = 'false';
		$name_of_location = '';
	}
	if($order == '') {
		$order = 1;
	}
	$order_before = $order-1;
	$queryer = "SELECT * FROM inventory WHERE inventoryid='$invid'";
	$resulter = mysqli_query($dbc,$queryer) or die(mysqli_error($dbc));
	$get_config = mysqli_fetch_assoc($resulter);
	$multiple_qty_counter = $get_config['digital_count_qty_multiple'];
    if($get_config['digital_count_qty'] == '' || $get_config['digital_count_qty'] == NULL) {
		$digi_count = 0;
	} else { $digi_count = $get_config['digital_count_qty']; }
	$total_qtyer = 0;
    $var=explode(',',$multiple_qty_counter);
	$total = count($var);
	if($order > $total) {
		$multiple_qty_counter = $multiple_qty_counter.','.$new_value;
		$var=explode(',',$multiple_qty_counter);
	}
	$total = count($var);
	if($total > 0) {
		$i = 1;
		$new_string = '';
		foreach($var as $qty) {
			if($i == $order) {
				if($delete !== 'true' && $location !== 'true') {
					if($i == $total) {
						$new_string .= $new_value.'#$#';
					} else {
						$new_string .= $new_value.'#$#,';
					}
					$total_qtyer += $new_value;
				} else if($location == 'true') {
					if($i == $total) {
						$new_string .= $new_value.'#$#'.$name_of_location;
					} else {
						$new_string .= $new_value.'#$#'.$name_of_location.',';
					}
					$total_qtyer += $new_value;
				}
			} else {
				if($i == $total) {
					$new_string .= $qty;
				} else {
					if($delete == 'true' && $i == ($order_before) && $i == ($total-1)) {
						$new_string .= $qty.'';
					} else {
						$new_string .= $qty.',';
					}
				}
				$arr = explode("#$#", $qty);
				$first = $arr[0];
				$total_qtyer += $first;
			}
			$i++;

		}
	}


	$digi_count = $total_qtyer;

	$query = mysqli_query($dbc,"UPDATE inventory SET digital_count_qty = '$digi_count', digital_count_qty_multiple = '$new_string' WHERE inventoryid = $invid") or die(mysqli_error($dbc));

	if($original_qty > 0) {
		$variance = $digi_count - $original_qty;
	} else {
		$variance = $digi_count + $original_qty;
	}

	echo $variance;
}

if($_GET['fill'] == 'show_category_dropdown') {
	$value = $_GET['value'];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_category_dropdown'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$value' WHERE name='show_category_dropdown'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('show_category_dropdown', '$value')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
}

if($_GET['fill'] == 'inventory_default_select_all') {
	$value = $_GET['value'];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='inventory_default_select_all'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$value' WHERE name='inventory_default_select_all'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('inventory_default_select_all', '$value')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
}

if($_GET['fill'] == 'show_digi_count') {
	$value = $_GET['value'];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_digi_count'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$value' WHERE name='show_digi_count'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('show_digi_count', '$value')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
}

if($_GET['fill'] == 'default_digi_count_tab') {
	$value = $_GET['value'];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='default_digi_count_tab'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$value' WHERE name='default_digi_count_tab'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('default_digi_count_tab', '$value')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
}

if($_GET['fill'] == 'show_impexp_inv') {
	$value = $_GET['value'];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_impexp_inv'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$value' WHERE name='show_impexp_inv'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('show_impexp_inv', '$value')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
}

if($_GET['fill'] == 'posGetCost') {
    $pro = $_GET['pro'];
    $qty = $_GET['qty'];
    $productPrice = $_GET['productPrice'];
	
	echo get_inventory($dbc, $pro, $productPrice);
}

?>