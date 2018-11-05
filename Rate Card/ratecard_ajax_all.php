<?php
include ('../include.php');
ob_clean();

if($_GET['fill'] == 'rate_card_config') {
    $action = $_GET['action'];
    $value = $_GET['value'];

    if($action == 'show_hide') {
        $ratecardid = $_GET['id'];
        $before_change = capture_before_change($dbc, 'rate_card', 'hide', 'ratecardid', $ratecardid);
        $query_rate_card = "UPDATE `rate_card` SET `hide` = '$value' WHERE `ratecardid` = '$ratecardid'";
        $result_rate_card	= mysqli_query($dbc, $query_rate_card);
        $history = capture_after_change('hide', $value);
				add_update_history($dbc, 'ratecard_history', $history, '', $before_change);
    }

    if($action == 'archive') {
        $ratecardid = $_GET['id'];
          $date_of_archival = date('Y-m-d');
          $before_change = capture_before_change($dbc, 'rate_card', 'deleted', 'ratecardid', $ratecardid);
          $before_change .= capture_before_change($dbc, 'rate_card', 'date_of_archival', 'ratecardid', $ratecardid);
          $query_rate_card = "UPDATE `rate_card` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `ratecardid` = '$ratecardid'";
          $result_rate_card	= mysqli_query($dbc, $query_rate_card);
          $history = capture_after_change('deleted', 1);
          $history .= capture_after_change('date_of_archival', $date_of_archival);
				  add_update_history($dbc, 'ratecard_history', $history, '', $before_change);
    }
    if($action == 'on_off') {
        $ratecardid = $_GET['id'];
        $before_change = capture_before_change($dbc, 'rate_card', 'on_off', 'ratecardid', $ratecardid);
        $query_rate_card = "UPDATE `rate_card` SET `on_off` = '$value' WHERE `ratecardid` = '$ratecardid'";
        $result_rate_card	= mysqli_query($dbc, $query_rate_card);
        $history = capture_after_change('on_off', $value);
        add_update_history($dbc, 'ratecard_history', $history, '', $before_change);
    }
}
//Packages
if($_GET['fill'] == 'package_service_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT `packageid`, `category` FROM `package` WHERE `service_type` = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['category']."'>".$row['category'].'</option>';
	}
}

if($_GET['fill'] == 'package_cat_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT `packageid`, `heading` FROM `package` WHERE `category` = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['packageid']."'>".$row['heading'].'</option>';
	}
}

if($_GET['fill'] == 'package_head_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `package` WHERE `packageid`='$value'"));
    echo $get_config['final_retail_price'].'*'.$get_config['admin_price'].'*'.$get_config['wholesale_price'].'*'.$get_config['commercial_price'].'*'.$get_config['client_price'].'*'.$get_config['msrp'];
}

//Promotion
if($_GET['fill'] == 'promotion_service_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT `promotionid`, `category` FROM `promotion` WHERE `service_type` = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['category']."'>".$row['category'].'</option>';
	}
}

if($_GET['fill'] == 'promotion_cat_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT `promotionid`, `heading` FROM `promotion` WHERE `category` = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['promotionid']."'>".$row['heading'].'</option>';
	}
}

if($_GET['fill'] == 'promotion_head_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `promotion` WHERE `promotionid`='$value'"));
    echo $get_config['final_retail_price'].'*'.$get_config['admin_price'].'*'.$get_config['wholesale_price'].'*'.$get_config['commercial_price'].'*'.$get_config['client_price'].'*'.$get_config['msrp'];
}



//Services
if($_GET['fill'] == 's_service_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT distinct(`category`) FROM `services` WHERE `service_type` = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['category']."'>".$row['category'].'</option>';
	}
}

if($_GET['fill'] == 's_cat_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT `serviceid`, `heading` FROM `services` WHERE `category` = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['serviceid']."'>".$row['heading'].'</option>';
	}
}

if($_GET['fill'] == 's_head_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `services` WHERE `serviceid`='$value'"));
    echo $get_config['final_retail_price'].'*'.$get_config['admin_price'].'*'.$get_config['wholesale_price'].'*'.$get_config['commercial_price'].'*'.$get_config['client_price'].'*'.$get_config['msrp'];
}

//Products
if($_GET['fill'] == 'p_product_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT distinct(`category`) FROM `products` WHERE `product_type` = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['category']."'>".$row['category'].'</option>';
	}
}

if($_GET['fill'] == 'p_cat_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT `productid`, `heading` FROM `products` WHERE `category` = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['productid']."'>".$row['heading'].'</option>';
	}
}

if($_GET['fill'] == 'p_head_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `products` WHERE `productid`='$value'"));
    echo $get_config['final_retail_price'].'*'.$get_config['admin_price'].'*'.$get_config['wholesale_price'].'*'.$get_config['commercial_price'].'*'.$get_config['client_price'].'*'.$get_config['msrp'].'*'.$get_config['cost'].'*'.$get_config['quantity'];
}

//SRED
if($_GET['fill'] == 'sred_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT distinct(`category`) FROM `sred` WHERE `sred_type` = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['category']."'>".$row['category'].'</option>';
	}
}

if($_GET['fill'] == 'sred_cat_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT `sredid`, `heading` FROM `sred` WHERE `category` = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['sredid']."'>".$row['heading'].'</option>';
	}
}

if($_GET['fill'] == 'sred_head_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `sred` WHERE `sredid`='$value'"));
    echo $get_config['final_retail_price'].'*'.$get_config['admin_price'].'*'.$get_config['wholesale_price'].'*'.$get_config['commercial_price'].'*'.$get_config['client_price'].'*'.$get_config['msrp'];
}

//Labour
if($_GET['fill'] == 'labour_type_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT `labourid`, `heading` FROM `labour` WHERE `labour_type` = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['labourid']."'>".$row['heading'].'</option>';
	}
}

if($_GET['fill'] == 'l_head_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `labour` WHERE `labourid`='$value'"));
    echo $get_config['hourly_rate'];
}


//Custom
if($_GET['fill'] == 'custom_service_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT `category` FROM `custom` WHERE `service_type` = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['category']."'>".$row['category'].'</option>';
	}
}

if($_GET['fill'] == 'custom_cat_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT `customid`, `heading` FROM `custom` WHERE `category` = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['customid']."'>".$row['heading'].'</option>';
	}
}

if($_GET['fill'] == 'custom_head_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `custom` WHERE `customid`='$value'"));
    echo $get_config['final_retail_price'].'*'.$get_config['admin_price'].'*'.$get_config['wholesale_price'].'*'.$get_config['commercial_price'].'*'.$get_config['client_price'].'*'.$get_config['msrp'];
}

//Staff
if($_GET['fill'] == 'st_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `contacts` WHERE `contactid`='$value'"));
    echo $get_config['monthly_rate'].'*'.$get_config['semi_monthly_rate'].'*'.$get_config['daily_rate'].'*'.$get_config['hr_rate_work'].'*'.$get_config['hr_rate_travel'].'*'.$get_config['field_day_cost'].'*'.$get_config['field_day_billable'];
}

//Contractor
if($_GET['fill'] == 'cnt_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `contacts` WHERE `contactid`='$value'"));
    echo $get_config['monthly_rate'].'*'.$get_config['semi_monthly_rate'].'*'.$get_config['daily_rate'].'*'.$get_config['hr_rate_work'].'*'.$get_config['hr_rate_travel'].'*'.$get_config['field_day_cost'].'*'.$get_config['field_day_billable'];
}

//Client
if($_GET['fill'] == 'cl_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `contacts` WHERE `contactid`='$value'"));
    echo $get_config['final_retail_price'].'*'.$get_config['admin_price'].'*'.$get_config['wholesale_price'].'*'.$get_config['commercial_price'].'*'.$get_config['client_price'].'*'.$get_config['msrp'].'*';

    $query = mysqli_query($dbc,"SELECT `contactid`, `name` FROM `contacts` WHERE `category`='Client' AND `deleted`=0");
    while($row = mysqli_fetch_array($query)) {
        if ($value == $row['contactid']) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
        echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['name']).'</option>';
    }
    echo "<option value=''></option>";

    echo '*';
    $query2 = mysqli_query($dbc,"SELECT `contactid`, `first_name`, `last_name`  FROM `contacts` WHERE `category`='Client' AND `deleted`=0");
    while($row2 = mysqli_fetch_array($query2)) {
        if ($value == $row2['contactid']) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
        echo "<option ".$selected." value='". $row2['contactid']."'>".decryptIt($row2['first_name']).' '.decryptIt($row2['last_name']).'</option>';
    }
    echo "<option value=''></option>";
}

//Vendor
if($_GET['fill'] == 'vendor_config') {
    $value = $_GET['value'];
    $query = mysqli_query($dbc,"SELECT distinct(`pricelist_name`) FROM `vendor_pricelist` WHERE `vendorid`='$value'");
    echo "<option value=''></option>";
    while($row = mysqli_fetch_array($query)) {
        echo "<option value='". $row['pricelist_name']."'>".$row['pricelist_name'].'</option>';
    }
}

if($_GET['fill'] == 'vpricelist_config') {
    $value = $_GET['value'];
    $query = mysqli_query($dbc,"SELECT distinct(`category`) FROM `vendor_pricelist` WHERE `pricelist_name`='$value'");
    echo "<option value=''></option>";
    while($row = mysqli_fetch_array($query)) {
        echo "<option value='". $row['category']."'>".$row['category'].'</option>';
    }
}

if($_GET['fill'] == 'vcat_config') {
    $value = $_GET['value'];
    $pricelist = $_GET['pricelist'];
    $query = mysqli_query($dbc,"SELECT `pricelistid`, `name` FROM `vendor_pricelist` WHERE `pricelist_name`='$pricelist' AND `category`='$value'");
    echo "<option value=''></option>";
    while($row = mysqli_fetch_array($query)) {
        echo "<option value='". $row['pricelistid']."'>".$row['name'].'</option>';
    }
}

if($_GET['fill'] == 'vproduct_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;

    $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `cdn_cpu` FROM `vendor_pricelist` WHERE `pricelistid`='$value'"));
    echo $query['cdn_cpu'];
}
//Customer
if($_GET['fill'] == 'cust_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `contacts` WHERE `contactid`='$value'"));
    echo $get_config['final_retail_price'].'*FFM*'.$get_config['admin_price'].'*FFM*'.$get_config['wholesale_price'].'*FFM*'.$get_config['commercial_price'].'*FFM*'.$get_config['client_price'].'*FFM*'.$get_config['msrp'].'*FFM*';

    $query = mysqli_query($dbc,"SELECT `contactid`, `name` FROM `contacts` WHERE `category`='Customer' AND `deleted`=0");
    while($row = mysqli_fetch_array($query)) {
        if ($value == $row['contactid']) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
        echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt(decryptIt($row['name'])).'</option>';
    }
    echo "<option value=''></option>";

    echo '*FFM*';
    $query2 = mysqli_query($dbc,"SELECT `contactid`, `first_name`, `last_name`  FROM `contacts` WHERE `category`='Customer' AND `deleted`=0");
    while($row2 = mysqli_fetch_array($query2)) {
        if ($value == $row2['contactid']) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
        echo "<option ".$selected." value='". $row2['contactid']."'>".$row2['first_name'].' '.$row2['last_name'].'</option>';
    }
    echo "<option value=''></option>";
}

//Material
if($_GET['fill'] == 'material_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `material` WHERE `materialid`='$value'"));
    echo $get_config['name'].'*FFM*'.$get_config['width'].'*FFM*'.$get_config['length'].'*FFM*'.$get_config['units'].'*FFM*'.$get_config['unit_weight'].'*FFM*'.$get_config['weight_per_feet'].'*FFM*'.$get_config['price'].'*FFM*';
}

//Inventory
if($_GET['fill'] == 'in_cat_config') {
    $value = $_GET['value'];
    echo "<option value=''></option>";
    $query2 = mysqli_query($dbc,"SELECT `category`, `inventoryid`, `name`  FROM `inventory` WHERE `category`='$value' AND `deleted`=0");
    while($row2 = mysqli_fetch_array($query2)) {
        echo "<option value='". $row2['inventoryid']."'>".$row2['name'].'</option>';
    }
}
if($_GET['fill'] == 'in_cat_config_partno') {
    $value = $_GET['value'];
    echo "<option value=''></option>";
    $query2 = mysqli_query($dbc,"SELECT `category`, `inventoryid`, `part_no`  FROM `inventory` WHERE `category`='$value' AND `deleted`=0");
    while($row2 = mysqli_fetch_array($query2)) {
        echo "<option value='". $row2['inventoryid']."'>".$row2['part_no'].'</option>';
    }
}

if($_GET['fill'] == 'in_code_part_name_config_number') {
    $value = $_GET['value'];
    echo "<option value=''></option>";
	$query2 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `category` FROM `inventory` WHERE `inventoryid`='$value' AND `deleted`=0"));
	$categie = $query2['category'];
    $query2 = mysqli_query($dbc,"SELECT `category`, `inventoryid`, `part_no`  FROM `inventory` WHERE `category`='$categie' AND `deleted`=0");
    while($row2 = mysqli_fetch_array($query2)) {
		if($value == $row2['inventoryid']) {
			$selected = 'selected';
		} else {
			$selected = '';
		}
        echo "<option ".$selected." value='". $row2['inventoryid']."'>".$row2['part_no'].'</option>';
    }
}

if($_GET['fill'] == 'in_code_part_no_config_name') {
    $value = $_GET['value'];
    echo "<option value=''></option>";
	$query2 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `category` FROM `inventory` WHERE `inventoryid`='$value' AND `deleted`=0"));
	$categie = $query2['category'];
    $query2 = mysqli_query($dbc,"SELECT `category`, `inventoryid`, `name`  FROM `inventory` WHERE `category`='$categie' AND `deleted`=0");
    while($row2 = mysqli_fetch_array($query2)) {
		if($value == $row2['inventoryid']) {
			$selected = 'selected';
		} else {
			$selected = '';
		}
        echo "<option ".$selected." value='". $row2['inventoryid']."'>".$row2['name'].'</option>';
    }
}

if($_GET['fill'] == 'in_code_part_name_config') {
    $value = $_GET['value'];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `inventory` WHERE `inventoryid`='$value'"));
    echo $get_config['final_retail_price'].'*FFM*'.$get_config['admin_price'].'*FFM*'.$get_config['wholesale_price'].'*FFM*'.$get_config['commercial_price'].'*FFM*'.$get_config['client_price'].'*FFM*'.$get_config['msrp'].'*FFM*';
}

if($_GET['fill'] == 'in_code_part_no_config') {
    $value = $_GET['value'];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `inventory` WHERE `inventoryid`='$value'"));
    echo $get_config['final_retail_price'].'*FFM*'.$get_config['admin_price'].'*FFM*'.$get_config['wholesale_price'].'*FFM*'.$get_config['commercial_price'].'*FFM*'.$get_config['client_price'].'*FFM*'.$get_config['msrp'].'*FFM*';
}

//Equipment
if($_GET['fill'] == 'eq_cat_config') {
    $value = $_GET['value'];
    $query = mysqli_query($dbc,"SELECT `category`, `equipmentid`, `unit_number`, `serial_number` FROM `equipment` WHERE `category`='$value' AND `deleted`=0");
    echo "<option value=''></option>";
    while($row = mysqli_fetch_array($query)) {
        echo "<option value='". $row['equipmentid']."'>".$row['unit_number'].' : '.$row['serial_number'].'</option>';
    }
}

if($_GET['fill'] == 'eq_un_sn_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `equipment` WHERE `equipmentid`='$value'"));
    echo $get_config['monthly_rate'].'*'.$get_config['semi_monthly_rate'].'*'.$get_config['daily_rate'].'*'.$get_config['hr_rate_work'].'*'.$get_config['hr_rate_travel'].'*'.$get_config['field_day_cost'].'*'.$get_config['field_day_billable'];
}

// Company Rate Card Descriptions
if($_GET['fill'] == 'rate_card_desc') {
	$query = '';
    $cat = filter_var($_GET['cat'],FILTER_SANITIZE_STRING);
	if($_GET['type'] == 'Position') {
		$query = "SELECT `name` id, `name` descript  FROM `positions` ORDER BY `name`";
	}
	else if($_GET['type'] == 'Staff') {
		$query = "SELECT `contactid` id, `first_name`, `last_name`, 'DECRYPT' descript  FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status` > 0 ORDER BY `last_name`, `first_name`";
	}
	else if($_GET['type'] == 'Equipment') {
		$query = "SELECT `type` id, `type` descript FROM `equipment` GROUP BY `type` ORDER BY `type`";
	}
	else if($_GET['type'] == 'Services') {
		$query = "SELECT `serviceid` id, `heading` descript FROM `services` WHERE '$cat' IN (`category`,'') AND `deleted`=0 ORDER BY `category`, `heading`";
	}
	else if($_GET['type'] == 'Expenses') {
		$query = "SELECT * FROM (SELECT CONCAT('EC ',`ec`,': ',`category`) `id`, `category` `descript` FROM `expense_categories` WHERE `deleted`=0 ORDER BY `ec`) `categories` UNION SELECT 'Uncategorized' `id`, '' `descript`";
	}
	if($query == '') {
		exit();
	}
	$result = mysqli_query($dbc, $query);

	$options = [];
	while($row = mysqli_fetch_array($result)) {
		$options[($row['descript'] == 'DECRYPT' ? decryptIt($row['last_name']).' '.decryptIt($row['first_name']) : $row['descript'])] = ($row['descript'] == 'DECRYPT' ? decryptIt($row['first_name']).' '.decryptIt($row['last_name']) : $row['descript']).'|'.$row['id'];
	}
	ksort($options);
	echo implode('*#*',$options);
}

// Staff Rate Dashboard Updates
if($_GET['fill'] == 'staff_rate_update') {
	$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$id = filter_var($_POST['rate_id'],FILTER_SANITIZE_STRING);
	if(is_array($_POST['value'])) {
		$value = filter_var(implode(',',$_POST['value']),FILTER_SANITIZE_STRING);
	} else {
		$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	}
	$history = filter_var('Staff rate card Edited by '.get_contact($dbc, $_SESSION['contactid']).' on '.date('Y-m-d h:i:s')." (`$field` set to '$value')",FILTER_SANITIZE_STRING);

	if(!mysqli_query($dbc, "UPDATE `staff_rate_table` SET `$field`='$value', `history`=IFNULL(CONCAT(HISTORY,'<br />\n','$history'),'$history') WHERE `rate_id`='$id'")) {
		echo "Error: ".mysqli_error($dbc);
	}
}

if($_GET['fill'] == 'staff_rate_order') {
    $category = $_GET['category'];
    $rateid = '';
    if (isset($_GET['rateid']) && !empty($_GET['rateid'])) {
        $rateid = $_GET['rateid'];
    }
    ?>
    <option value=""></option>
    <?php
    $row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `staff_rate_table` WHERE `rate_id` = '$rateid'"));
    $sort_order_all = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT GROUP_CONCAT(`sort_order` SEPARATOR ',') AS `sort_order_all` FROM `staff_rate_table` WHERE `category` = '" . $category . "'"));

    for($m=1;$m<=100;$m++) { ?>
        <option <?php if ($row['sort_order'] == $m) { echo  'selected="selected"'; } else if (strpos(','.$sort_order_all['sort_order_all'].',', ','.$m.',') !== FALSE) { echo " disabled"; } ?>
            value="<?php echo $m;?>"><?php echo $m;?></option>
    <?php }
}

if($_GET['fill'] == 'delete_rate_card') {
    $ratecardid = $_POST['ratecardid'];
         $date_of_archival = date('Y-m-d');
     mysqli_query($dbc, "UPDATE `tile_rate_card` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `ratecardid` = '$ratecardid'");
}
?>
