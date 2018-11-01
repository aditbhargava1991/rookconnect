<?php
include_once('../database_connection.php');
include_once('../function.php');
include_once('../global.php');
include_once('../phpmailer.php');
error_reporting(0);

if($_GET['fill'] == 'project_path_milestone') {
	if(empty($_GET['project_path'])) {
		exit("(Select a Template above)");
	}
    $project_path = $_GET['project_path'];
    $each_tab = explode('#*#', get_project_path_milestone($dbc, $project_path, 'milestone'));
    $timeline = explode('#*#', get_project_path_milestone($dbc, $project_path, 'timeline'));
    $task = explode('#*#', get_project_path_milestone($dbc, $project_path, 'checklist'));
    $ticket = explode('#*#', get_project_path_milestone($dbc, $project_path, 'ticket'));
    $workorder = explode('#*#', get_project_path_milestone($dbc, $project_path, 'workorder'));
    foreach ($each_tab as $key => $cat_tab) {
		if($cat_tab != '') {
			echo "Milestone: ".$cat_tab.(!empty($timeline[$key]) ? ' ('.$timeline[$key].')' : '')."<br />";
			if(!empty($task[$key]) || !empty($ticket[$key]) || !empty($workorder[$key])) {
				echo "<ul>";
				$item_list = explode('*#*', $ticket[$key]);
				foreach($item_list as $item) {
					if($item != '') {
						$item = explode('FFMSPLIT',$item)[0];
						echo "<small><li>".TICKET_NOUN.": ".$item."</li></small>";
					}
				}
				$item_list = explode('*#*', $workorder[$key]);
				foreach($item_list as $item) {
					if($item != '') {
						echo "<small><li>Work Order: ".$item."</li></small>";
					}
				}
				$item_list = explode('*#*', $task[$key]);
				foreach($item_list as $item) {
					if($item != '') {
						echo "<small><li>".$item."</li></small>";
					}
				}
				echo "</ul>";
			}
		}
    }
}

//Packages
if($_GET['fill'] == 'package_service_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT packageid, category FROM package WHERE service_type = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['category']."'>".$row['category'].'</option>';
	}
}

if($_GET['fill'] == 'package_cat_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT packageid, heading FROM package WHERE category = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['packageid']."'>".$row['heading'].'</option>';
	}
}

if($_GET['fill'] == 'package_head_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    if(!empty($_GET['ratecardid'])) {
        $ratecardid = $_GET['ratecardid'];
        $get_rc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT package FROM rate_card WHERE ratecardid='$ratecardid'"));
        $package = explode('**', $get_rc['package']);

        foreach($package as $pp){
            if (strpos('#'.$pp, '#'.$value.'#') !== false) {
                $rate_card_price = explode('#', $pp);
            }
        }
    }
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM package WHERE packageid='$value'"));
    echo $get_config['final_retail_price'].'*'.$get_config['admin_price'].'*'.$get_config['wholesale_price'].'*'.$get_config['commercial_price'].'*'.$get_config['client_price'].'*'.$get_config['msrp'].'*'.$rate_card_price[1];
}

//Promotion
if($_GET['fill'] == 'promotion_service_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT promotionid, category FROM promotion WHERE service_type = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['category']."'>".$row['category'].'</option>';
	}
}

if($_GET['fill'] == 'promotion_cat_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT promotionid, heading FROM promotion WHERE category = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['promotionid']."'>".$row['heading'].'</option>';
	}
}

if($_GET['fill'] == 'promotion_head_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    if(!empty($_GET['ratecardid'])) {
        $ratecardid = $_GET['ratecardid'];
        $get_rc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT promotion FROM rate_card WHERE ratecardid='$ratecardid'"));
        $promotion = explode('**', $get_rc['promotion']);

        foreach($promotion as $pp){
            if (strpos('#'.$pp, '#'.$value.'#') !== false) {
                $rate_card_price = explode('#', $pp);
            }
        }
    }
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM promotion WHERE promotionid='$value'"));
    echo $get_config['final_retail_price'].'*'.$get_config['admin_price'].'*'.$get_config['wholesale_price'].'*'.$get_config['commercial_price'].'*'.$get_config['client_price'].'*'.$get_config['msrp'].'*'.$rate_card_price[1];
}



//Services
if($_GET['fill'] == 's_service_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT distinct(category) FROM services WHERE service_type = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['category']."'>".$row['category'].'</option>';
	}
}

if($_GET['fill'] == 's_cat_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT serviceid, heading FROM services WHERE category = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['serviceid']."'>".$row['heading'].'</option>';
	}
}

if($_GET['fill'] == 's_head_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    if(!empty($_GET['ratecardid'])) {
        $ratecardid = $_GET['ratecardid'];
        $get_rc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT services FROM rate_card WHERE ratecardid='$ratecardid'"));
        $services = explode('**', $get_rc['services']);

        foreach($services as $pp){
            if (strpos('#'.$pp, '#'.$value.'#') !== false) {
                $rate_card_price = explode('#', $pp);
            }
        }
    }
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM services WHERE serviceid='$value'"));
    echo $get_config['final_retail_price'].'*'.$get_config['admin_price'].'*'.$get_config['wholesale_price'].'*'.$get_config['commercial_price'].'*'.$get_config['client_price'].'*'.$get_config['msrp'].'*'.$rate_card_price[1].'*'.$get_config['minimum_billable'].'*'.$get_config['estimated_hours'].'*'.$get_config['actual_hours'];
}

//Products
if($_GET['fill'] == 'p_product_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT distinct(category) FROM products WHERE product_type = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['category']."'>".$row['category'].'</option>';
	}
}

if($_GET['fill'] == 'p_cat_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT productid, heading FROM products WHERE category = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['productid']."'>".$row['heading'].'</option>';
	}
}

if($_GET['fill'] == 'p_head_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    if(!empty($_GET['ratecardid'])) {
        $ratecardid = $_GET['ratecardid'];
        $get_rc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT products FROM rate_card WHERE ratecardid='$ratecardid'"));
        $products = explode('**', $get_rc['products']);

        foreach($products as $pp){
            if (strpos('#'.$pp, '#'.$value.'#') !== false) {
                $rate_card_price = explode('#', $pp);
            }
        }
    }
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM products WHERE productid='$value'"));
    echo $get_config['final_retail_price'].'*'.$get_config['admin_price'].'*'.$get_config['wholesale_price'].'*'.$get_config['commercial_price'].'*'.$get_config['client_price'].'*'.$get_config['msrp'].'*'.$rate_card_price[1].'*'.$get_config['minimum_billable'].'*'.$get_config['estimated_hours'].'*'.$get_config['actual_hours'];
}

//SR&ED
if($_GET['fill'] == 'sred_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT distinct(category) FROM sred WHERE sred_type = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['category']."'>".$row['category'].'</option>';
	}
}

if($_GET['fill'] == 'sred_cat_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT sredid, heading FROM sred WHERE category = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['sredid']."'>".$row['heading'].'</option>';
	}
}

if($_GET['fill'] == 'sred_head_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    if(!empty($_GET['ratecardid'])) {
        $ratecardid = $_GET['ratecardid'];
        $get_rc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT sred FROM rate_card WHERE ratecardid='$ratecardid'"));
        $sred = explode('**', $get_rc['sred']);

        foreach($sred as $pp){
            if (strpos('#'.$pp, '#'.$value.'#') !== false) {
                $rate_card_price = explode('#', $pp);
            }
        }
    }
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM sred WHERE sredid='$value'"));
    echo $get_config['final_retail_price'].'*'.$get_config['admin_price'].'*'.$get_config['wholesale_price'].'*'.$get_config['commercial_price'].'*'.$get_config['client_price'].'*'.$get_config['msrp'].'*'.$rate_card_price[1];
}

//Labour
if($_GET['fill'] == 'labour_type_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT labourid, heading FROM labour WHERE labour_type = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['labourid']."'>".$row['heading'].'</option>';
	}
}

if($_GET['fill'] == 'l_head_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    if(!empty($_GET['ratecardid'])) {
        $ratecardid = $_GET['ratecardid'];
        $get_rc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT labour FROM rate_card WHERE ratecardid='$ratecardid'"));
        $labour = explode('**', $get_rc['labour']);

        foreach($labour as $pp){
            if (strpos('#'.$pp, '#'.$value.'#') !== false) {
                $rate_card_price = explode('#', $pp);
            }
        }
    }
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM labour WHERE labourid='$value'"));
    echo $get_config['hourly_rate'].'*'.$rate_card_price[1];
}

//Custom
if($_GET['fill'] == 'custom_service_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT category FROM custom WHERE service_type = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['category']."'>".$row['category'].'</option>';
	}
}

if($_GET['fill'] == 'custom_cat_config') {
    $value = $_GET['value'];
	$query = mysqli_query($dbc,"SELECT customid, heading FROM custom WHERE category = '$value'");
	echo '<option value=""></option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['customid']."'>".$row['heading'].'</option>';
	}
}

if($_GET['fill'] == 'custom_head_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    if(!empty($_GET['ratecardid'])) {
        $ratecardid = $_GET['ratecardid'];
        $get_rc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT custom FROM rate_card WHERE ratecardid='$ratecardid'"));
        $custom = explode('**', $get_rc['custom']);

        foreach($custom as $pp){
            if (strpos('#'.$pp, '#'.$value.'#') !== false) {
                $rate_card_price = explode('#', $pp);
            }
        }
    }
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM custom WHERE customid='$value'"));
    echo $get_config['final_retail_price'].'*'.$get_config['admin_price'].'*'.$get_config['wholesale_price'].'*'.$get_config['commercial_price'].'*'.$get_config['client_price'].'*'.$get_config['msrp'].'*'.$rate_card_price[1];
}

//Staff
if($_GET['fill'] == 'st_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    if(!empty($_GET['ratecardid'])) {
        $ratecardid = $_GET['ratecardid'];
        $get_rc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT staff FROM rate_card WHERE ratecardid='$ratecardid'"));
        $staff = explode('**', $get_rc['staff']);

        foreach($staff as $pp){
            if (strpos('#'.$pp, '#'.$value.'#') !== false) {
                $rate_card_price = explode('#', $pp);
            }
        }
    }
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM contacts WHERE contactid='$value'"));
    echo $get_config['monthly_rate'].'*'.$get_config['semi_monthly_rate'].'*'.$get_config['daily_rate'].'*'.$get_config['hr_rate_work'].'*'.$get_config['hr_rate_travel'].'*'.$get_config['field_day_cost'].'*'.$get_config['field_day_billable'].'*'.$rate_card_price[1];
}

//Contractor
if($_GET['fill'] == 'cnt_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    if(!empty($_GET['ratecardid'])) {
        $ratecardid = $_GET['ratecardid'];
        $get_rc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT contractor FROM rate_card WHERE ratecardid='$ratecardid'"));
        $contractor = explode('**', $get_rc['contractor']);

        foreach($contractor as $pp){
            if (strpos('#'.$pp, '#'.$value.'#') !== false) {
                $rate_card_price = explode('#', $pp);
            }
        }
    }
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM contacts WHERE contactid='$value'"));
    echo $get_config['monthly_rate'].'*'.$get_config['semi_monthly_rate'].'*'.$get_config['daily_rate'].'*'.$get_config['hr_rate_work'].'*'.$get_config['hr_rate_travel'].'*'.$get_config['field_day_cost'].'*'.$get_config['field_day_billable'].'*'.$rate_card_price[1];
}

//Client
if($_GET['fill'] == 'cl_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    if(!empty($_GET['ratecardid'])) {
        $ratecardid = $_GET['ratecardid'];
        $get_rc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT client FROM rate_card WHERE ratecardid='$ratecardid'"));
        $client = explode('**', $get_rc['client']);

        foreach($client as $pp){
            if (strpos('#'.$pp, '#'.$value.'#') !== false) {
                $rate_card_price = explode('#', $pp);
            }
        }
    }
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM contacts WHERE contactid='$value'"));
    echo $get_config['final_retail_price'].'*'.$get_config['admin_price'].'*'.$get_config['wholesale_price'].'*'.$get_config['commercial_price'].'*'.$get_config['client_price'].'*'.$get_config['msrp'].'*'.$rate_card_price[1].'*';

    $query = mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='Client' AND deleted=0");
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
    $query2 = mysqli_query($dbc,"SELECT contactid, first_name, last_name  FROM contacts WHERE category='Client' AND deleted=0");
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

//Vendor
if($_GET['fill'] == 'vendor_config') {
    $value = $_GET['value'];
    $query = mysqli_query($dbc,"SELECT distinct(pricelist_name) FROM vendor_pricelist WHERE vendorid='$value'");
    echo "<option value=''></option>";
    while($row = mysqli_fetch_array($query)) {
        echo "<option value='". $row['pricelist_name']."'>".$row['pricelist_name'].'</option>';
    }
}

if($_GET['fill'] == 'vpricelist_config') {
    $value = $_GET['value'];
    $query = mysqli_query($dbc,"SELECT distinct(category) FROM vendor_pricelist WHERE pricelist_name='$value'");
    echo "<option value=''></option>";
    while($row = mysqli_fetch_array($query)) {
        echo "<option value='". $row['category']."'>".$row['category'].'</option>';
    }
}

if($_GET['fill'] == 'vcat_config') {
    $value = $_GET['value'];
    $pricelist = $_GET['pricelist'];
    $query = mysqli_query($dbc,"SELECT pricelistid, name FROM vendor_pricelist WHERE pricelist_name='$pricelist' AND category='$value'");
    echo "<option value=''></option>";
    while($row = mysqli_fetch_array($query)) {
        echo "<option value='". $row['pricelistid']."'>".decryptIt($row['name']).'</option>';
    }
}

if($_GET['fill'] == 'vproduct_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    if(!empty($_GET['ratecardid'])) {
        $ratecardid = $_GET['ratecardid'];
        $get_rc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT vendor FROM rate_card WHERE ratecardid='$ratecardid'"));
        $vendor = explode('**', $get_rc['vendor']);

        foreach($vendor as $pp){
            if (strpos('#'.$pp, '#'.$value.'#') !== false) {
                $rate_card_price = explode('#', $pp);
            }
        }
    }

    $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT cdn_cpu FROM vendor_pricelist WHERE pricelistid='$value'"));
    echo $query['cdn_cpu'].'*'.$rate_card_price[1];
}
//Customer
if($_GET['fill'] == 'cust_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    if(!empty($_GET['ratecardid'])) {
        $ratecardid = $_GET['ratecardid'];
        $get_rc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT customer FROM rate_card WHERE ratecardid='$ratecardid'"));
        $customer = explode('**', $get_rc['customer']);

        foreach($customer as $pp){
            if (strpos('#'.$pp, '#'.$value.'#') !== false) {
                $rate_card_price = explode('#', $pp);
            }
        }
    }
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM contacts WHERE contactid='$value'"));
    echo $get_config['final_retail_price'].'*FFM*'.$get_config['admin_price'].'*FFM*'.$get_config['wholesale_price'].'*FFM*'.$get_config['commercial_price'].'*FFM*'.$get_config['client_price'].'*FFM*'.$get_config['msrp'].'*FFM*'.$rate_card_price[1].'*FFM*';

    $query = mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='Customer' AND deleted=0");
    while($row = mysqli_fetch_array($query)) {
        if ($value == $row['contactid']) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
        echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['name']).'</option>';
    }
    echo "<option value=''></option>";

    echo '*FFM*';
    $query2 = mysqli_query($dbc,"SELECT contactid, first_name, last_name  FROM contacts WHERE category='Customer' AND deleted=0");
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
    if(!empty($_GET['ratecardid'])) {
        $ratecardid = $_GET['ratecardid'];
        $get_rc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT material FROM rate_card WHERE ratecardid='$ratecardid'"));
        $material = explode('**', $get_rc['material']);

        foreach($material as $pp){
            if (strpos('#'.$pp, '#'.$value.'#') !== false) {
                $rate_card_price = explode('#', $pp);
            }
        }
    }
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM material WHERE materialid='$value'"));
    echo $get_config['name'].'*FFM*'.$get_config['width'].'*FFM*'.$get_config['length'].'*FFM*'.$get_config['units'].'*FFM*'.$get_config['unit_weight'].'*FFM*'.$get_config['weight_per_feet'].'*FFM*'.$get_config['price'].'*FFM*'.$rate_card_price[1].'*FFM*';
}

//Inventory
if($_GET['fill'] == 'in_cat_config') {
    $value = $_GET['value'];
    echo "<option value=''></option>";
    $query2 = mysqli_query($dbc,"SELECT category, inventoryid, name  FROM inventory WHERE category='$value' AND deleted=0");
    while($row2 = mysqli_fetch_array($query2)) {
        echo "<option value='". $row2['inventoryid']."'>".$row2['name'].'</option>';
    }
}
if($_GET['fill'] == 'in_cat_config_partno') {
    $value = $_GET['value'];
    echo "<option value=''></option>";
    $query2 = mysqli_query($dbc,"SELECT category, inventoryid, part_no  FROM inventory WHERE category='$value' AND deleted=0");
    while($row2 = mysqli_fetch_array($query2)) {
        echo "<option value='". $row2['inventoryid']."'>".$row2['part_no'].'</option>';
    }
}

if($_GET['fill'] == 'in_code_part_name_config_number') {
    $value = $_GET['value'];
    echo "<option value=''></option>";
	$query2 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT category FROM inventory WHERE inventoryid='$value' AND deleted=0"));
	$categie = $query2['category'];
    $query2 = mysqli_query($dbc,"SELECT category, inventoryid, part_no  FROM inventory WHERE category='$categie' AND deleted=0");
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
	$query2 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT category FROM inventory WHERE inventoryid='$value' AND deleted=0"));
	$categie = $query2['category'];
    $query2 = mysqli_query($dbc,"SELECT category, inventoryid, name  FROM inventory WHERE category='$categie' AND deleted=0");
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
	$rate_card_price = 0;
    if(!empty($_GET['ratecardid'])) {
        $ratecardid = $_GET['ratecardid'];
        $get_rc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT inventory FROM rate_card WHERE ratecardid='$ratecardid'"));
        $inventory = explode('**', $get_rc['inventory']);

        foreach($inventory as $pp){
            if (strpos('#'.$pp, '#'.$value.'#') !== false) {
                $rate_card_price = explode('#', $pp);
            }
        }
    }
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM inventory WHERE inventoryid='$value'"));
    echo $get_config['final_retail_price'].'*FFM*'.$get_config['admin_price'].'*FFM*'.$get_config['wholesale_price'].'*FFM*'.$get_config['commercial_price'].'*FFM*'.$get_config['client_price'].'*FFM*'.$get_config['msrp'].'*FFM*';
}

if($_GET['fill'] == 'in_code_part_no_config') {
    $value = $_GET['value'];
	$rate_card_price = 0;
    if(!empty($_GET['ratecardid'])) {
        $ratecardid = $_GET['ratecardid'];
        $get_rc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT inventory FROM rate_card WHERE ratecardid='$ratecardid'"));
        $inventory = explode('**', $get_rc['inventory']);

        foreach($inventory as $pp){
            if (strpos('#'.$pp, '#'.$value.'#') !== false) {
                $rate_card_price = explode('#', $pp);
            }
        }
    }
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM inventory WHERE inventoryid='$value'"));
    echo $get_config['final_retail_price'].'*FFM*'.$get_config['admin_price'].'*FFM*'.$get_config['wholesale_price'].'*FFM*'.$get_config['commercial_price'].'*FFM*'.$get_config['client_price'].'*FFM*'.$get_config['msrp'].'*FFM*';
}
//Equipment
if($_GET['fill'] == 'eq_cat_config') {
    $value = $_GET['value'];
    $query = mysqli_query($dbc,"SELECT category, equipmentid, unit_number, serial_number FROM equipment WHERE category='$value' AND deleted=0");
    echo "<option value=''></option>";
    while($row = mysqli_fetch_array($query)) {
        echo "<option value='". $row['equipmentid']."'>".$row['unit_number'].' : '.$row['serial_number'].'</option>';
    }
}

if($_GET['fill'] == 'eq_un_sn_config') {
    $value = $_GET['value'];
    $rate_card_price = 0;
    if(!empty($_GET['ratecardid'])) {
        $ratecardid = $_GET['ratecardid'];
        $get_rc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment FROM rate_card WHERE ratecardid='$ratecardid'"));
        $equipment = explode('**', $get_rc['equipment']);

        foreach($equipment as $pp){
            if (strpos('#'.$pp, '#'.$value.'#') !== false) {
                $rate_card_price = explode('#', $pp);
            }
        }
    }
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM equipment WHERE equipmentid='$value'"));
    echo $get_config['monthly_rate'].'*'.$get_config['semi_monthly_rate'].'*'.$get_config['daily_rate'].'*'.$get_config['hr_rate_work'].'*'.$get_config['hr_rate_travel'].'*'.$get_config['field_day_cost'].'*'.$get_config['field_day_billable'].'*'.$rate_card_price[1];
}

if($_GET['fill'] == 'path_milestone') {
    $projectid = $_GET['taskid'];
    $milestone_timeline = $_GET['status'];
	$milestone_timeline = str_replace("FFMEND","&",$milestone_timeline);
    $milestone_timeline = str_replace("FFMSPACE"," ",$milestone_timeline);
    $milestone_timeline = str_replace("FFMHASH","#",$milestone_timeline);

	$query_update_project = "UPDATE `project` SET  milestone_timeline='$milestone_timeline' WHERE `projectid` = '$projectid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);
}

if($_GET['fill'] == 'ticket_path_milestone') {
    $ticketid = $_GET['ticketid'];
    $milestone_timeline = $_GET['status'];
	$milestone_timeline = str_replace("FFMEND","&",$milestone_timeline);
    $milestone_timeline = str_replace("FFMSPACE"," ",$milestone_timeline);
    $milestone_timeline = str_replace("FFMHASH","#",$milestone_timeline);

	echo $query_update_project = "UPDATE `tickets` SET  milestone_timeline='$milestone_timeline' WHERE `ticketid` = '$ticketid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);
}
if($_GET['fill'] == 'task_path_milestone') {
    $taskid = $_GET['id'];
    $milestone = $_GET['status'];
	$milestone = str_replace("FFMEND","&",$milestone);
    $milestone = str_replace("FFMSPACE"," ",$milestone);
    $milestone = str_replace("FFMHASH","#",$milestone);

	echo $query_update_project = "UPDATE `tasklist` SET  `project_milestone`='$milestone' WHERE `tasklistid` = '$taskid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);
}

if($_GET['fill'] == 'add_milestone_item') {
	$projectid = $_POST['projectid'];
	$milestone = $_POST['milestone'];
	$milestone = str_replace("FFMEND","&",$milestone);
    $milestone = str_replace("FFMSPACE"," ",$milestone);
    $milestone = str_replace("FFMHASH","#",$milestone);
	$item = $_POST['item'];

	$updated_by = $_SESSION['contactid'];
	$updated_date = date('Y-m-d');
	$query = "INSERT INTO `tasklist` (`projectid`, `project_milestone`, `heading`, `task`, `sort`, `updated_date`, `updated_by`)
		SELECT '$projectid', '$milestone', '$item', '$item', IFNULL(MAX(`sort`),0), '$updated_date', '$updated_by' FROM `tasklist` WHERE `projectid`='$projectid' AND `milestone`='$milestone'";
	$result = mysqli_query($dbc, $query);
    $taskid = mysqli_insert_id($dbc);

    $row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `tasklistid` = '$taskid'"));

    echo '<li id="'.$row['tasklistid'].'" class="ui-state-default ui-state-checklist" style=" '.($row['flag_colour'] != '' ? 'background-color: #'.$row['flag_colour'].';' : '').'"><span style="cursor:pointer; max-width: 540px; display:inline-block; width:100%;">';
        echo '<input type="checkbox" onclick="taskChange(this);" '.($row['checked'] == 1 ? 'checked' : '').' value="'.$row['tasklistid'].'" style="height: 1.5em; width: 1.5em;" name="tasklistid[]">';
            echo '<span class="pull-right" style="width: calc(100% - 2em);" data-task="'.$row['tasklistid'].'">';
            echo '<span style="display:inline-block; text-align:center; width:11%" title="Flag This!" onclick="flag_item(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" style="height:1.5em;" onclick="return false;"></span>';
            echo '<span style="display:inline-block; text-align:center; width:11%" title="Assign to Customer" onclick="assign_item(this); return false;" data-assigned="'.$row['assign_client'].'"><img src="'.WEBSITE_URL.'/img/icons/ROOK-sync-icon.png" style="height:1.5em;" onclick="return false;"></span>';
            echo '<span style="display:inline-block; text-align:center; width:11%" title="Send Alert" onclick="send_alert(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-alert-icon.png" style="height:1.5em;" onclick="return false;"></span>';
            echo '<span style="display:inline-block; text-align:center; width:11%" title="Send Email" onclick="send_email(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" style="height:1.5em;" onclick="return false;"></span>';
            echo '<span style="display:inline-block; text-align:center; width:11%" title="Schedule Reminder" onclick="send_reminder(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" style="height:1.5em;" onclick="return false;"></span>';
            echo '<span style="display:inline-block; text-align:center; width:11%" title="Attach File" onclick="attach_file(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" style="height:1.5em;" onclick="return false;"></span>';
            echo '<span style="display:inline-block; text-align:center; width:11%" title="Reply" onclick="send_reply(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" style="height:1.5em;" onclick="return false;"></span>';
            echo '<span style="display:inline-block; text-align:center; width:11%" title="Add Time" onclick="add_time(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-timer-icon.png" style="height:1.5em;" onclick="return false;"></span>';
            echo '<span style="display:inline-block; text-align:center; width:11%" title="Archive Item" onclick="archive(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" style="height:1.5em;" onclick="return false;"></span>';
						echo '<span style="display:inline-block; text-align:center; width:11%" title="Archive Item" onclick="archive(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-sync-icon.png" style="height:1.5em;" onclick="return false;"></span>';
						echo '</span>';
            echo '<input type="text" name="reply_'.$row['tasklistid'].'" style="display:none;" class="form-control" />';
            echo '<input type="text" name="task_time_'.$row['tasklistid'].'" style="display:none;" class="form-control timepicker" />';
            echo '<input type="text" name="reminder_'.$row['tasklistid'].'" style="display:none;" class="form-control datepicker" />';
            echo '<input type="file" name="attach_'.$row['tasklistid'].'" style="display:none;" class="form-control" />';
            echo '<br /><span class="display-field" style="white-space: normal;'.($row['checked'] == 1 ? ' text-decoration: line-through;' : '').'">Task #'.$row['tasklistid'].': '.html_entity_decode($row['task']).'</span>';
            $documents = mysqli_query($dbc, "SELECT * FROM task_document WHERE tasklistid='".$row['tasklistid']."'");
            while($doc = mysqli_fetch_array($documents)) {
                echo '<br /><a href="download/'.$doc['document'].'">'.$doc['document'].'<br /><small><em>Uploaded by '.get_staff($dbc, $doc['created_by']).' on '.$doc['created_date'].'</em></small></a>';
            }
        echo '<img class="drag_handle pull-right" src="'.WEBSITE_URL.'/img/icons/hold.png" style="height:1.5em; width:1.5em;" /></span></li>';
}
if($_GET['fill'] == 'delete_milestone_item') {
	$item = $_GET['tasklistid'];
	$updated_date = date('Y-m-d');
	$updated_by = $_SESSION['contactid'];
    $date_of_archival = date('Y-m-d');

	$query = "UPDATE `tasklist` SET `deleted`=1, `date_of_archival` = '$date_of_archival', `updated_by`='$updated_by', `updated_date`='$updated_date' WHERE `tasklistid`='$item'";
	$result = mysqli_query($dbc, $query);
}

if($_GET['fill'] == 'reply_milestone_item') {
	$item = $_GET['tasklistid'];
	$updated_date = date('Y-m-d');
	$updated_by = $_SESSION['contactid'];
	$reply = htmlentities('<br />'.$_POST['reply']);

	$query = "UPDATE `tasklist` SET `task`=CONCAT(`task`,'$reply'), `updated_by`='$updated_by', `updated_date`='$updated_date' WHERE `tasklistid`='$item'";
	$result = mysqli_query($dbc, $query);
}
if($_GET['fill'] == 'milestone_item_check') {
	$item = $_POST['id'];
	$updated_date = date('Y-m-d');
	$updated_by = $_SESSION['contactid'];
	$status = ($_POST['status'] == 'true' ? 1 : 0);
	$note = htmlentities('<br />'.($status == 1 ? 'Marked done' : 'Unchecked').' by '.get_contact($dbc, $updated_by).' at '.date('Y-m-d, g:i:s A'));

	$query = "UPDATE `tasklist` SET `task`=CONCAT(`task`,'$note'), `status`='".($status == 1 ? 'Done' : 'To Do')."', `updated_by`='$updated_by', `updated_date`='$updated_date' WHERE `tasklistid`='$item'";
	$result = mysqli_query($dbc, $query);

    $task = mysqli_fetch_array(mysqli_query($dbc, "SELECT `task` FROM `tasklist` WHERE `tasklistid`='$item'"))['task'];
    $html = 'Task #'.$item.': '.html_entity_decode($task);

    echo $html;
}
if($_GET['fill'] == 'milestone_item_alert') {
	$item_id = $_POST['id'];
	$user = $_POST['user'];
	$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `tasklistid`='$item_id'"));
	$id = $result['projectid'];
	$link = WEBSITE_URL."/Project/review_project.php?type=project_path&projectid=".$id;
	$text = "Project";
	$date = date('Y/m/d');
	$sql = mysqli_query($dbc, "INSERT INTO `alerts` (`alert_date`, `alert_link`, `alert_text`, `alert_user`) VALUES ('$date', '$link', '$text', '$user')");
}
if($_GET['fill'] == 'milestone_item_email') {
	$item_id = $_POST['id'];
	$user = $_POST['user'];
	$subject = '';
	$title = '';
	$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM tasklist WHERE tasklistid = '$item_id'"));
	$id = $result['projectid'];
	$item = $result['task'];
	$milestone = $result['milestone'];
	$subject = "A reminder about the Project $milestone";
	$contacts = mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$user'");
	while($row = mysqli_fetch_array($contacts)) {
        $email_address = get_email($dbc, $row['contactid']);
        if(trim($email_address) != '') {
			$body = "Hi ".decryptIt($row['first_name'])."<br />\n<br />
				This is a reminder about the Project $milestone on the Project.<br />\n<br />
				<a href='".WEBSITE_URL."/Project/review_project.php?type=project_path&projectid=$id'>Click here</a> to see the Project.<br />\n<br />
				$item";
			send_email('', $email_address, '', '', $subject, $body, '');
		}
	}
}
if($_GET['fill'] == 'milestone_item_reminder') {
	$item_id = $_POST['id'];
	$sender = get_email($dbc, $_SESSION['contactid']);
	$date = $_POST['schedule'];
	$to = $_POST['user'];
	$subject = '';
	$title = '';
	$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM tasklist WHERE tasklistid = '$item_id'"));
	$id = $result['projectid'];
	$item = $result['task'];
	$milestone = $result['milestone'];
	$subject = "A reminder about the Project $milestone";
	$contacts = mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$user'");
	$body = filter_var(htmlentities("This is a reminder about the Project $milestone on the Project.<br />\n<br />
		<a href='".WEBSITE_URL."/Project/review_project.php?type=project_path&projectid=$id'>Click here</a> to see the Project.<br />\n<br />
		$item"), FILTER_SANITIZE_STRING);
	$result = mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_time`, `reminder_type`, `subject`, `body`, `sender`)
		VALUES ('$to', '$date', '08:00:00', 'QUICK', '$subject', '$body', '$sender')");
}
if($_GET['fill'] == 'milestone_item_flag') {
	$item_id = $_POST['id'];
	$colour = mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colour` FROM tasklist WHERE tasklistid = '$item_id'"))['flag_colour'];
	$colour_list = explode(',', get_config($dbc, "ticket_colour_flags"));
	$colour_key = array_search($colour, $colour_list);
	$new_colour = ($colour_key === FALSE ? $colour_list[0] : ($colour_key + 1 < count($colour_list) ? $colour_list[$colour_key + 1] : ''));
	$result = mysqli_query($dbc, "UPDATE `tasklist` SET `flag_colour`='$new_colour' WHERE `tasklistid` = '$item_id'");
	echo $new_colour;
}
if($_GET['fill'] == 'milestone_item_upload') {
	$id = $_GET['id'];
	$type = $_GET['type'];
	$filename = htmlspecialchars($_FILES['file']['name'], ENT_QUOTES);
	$file = $_FILES['file']['tmp_name'];
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
	move_uploaded_file($file, "download/".$filename);
	$filename = filter_var($filename,FILTER_SANITIZE_STRING);
	$query_insert = "INSERT INTO `task_document` (`tasklistid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$id', 'Support Document', '$filename', '".date('Y/m/d')."', '".$_SESSION['contactid']."')";
	$result_insert = mysqli_query($dbc, $query_insert);
}
if($_GET['fill'] == 'milestone_quick_time') {
	$taskid = $_POST['id'];
	$projectid = $_POST['projectid'];
	$time = $_POST['time'];
	$query_time = "INSERT INTO `tasklist_time` (`tasklistid`, `work_time`, `contactid`, `timer_date`) VALUES ('$taskid', '$time', '".$_SESSION['contactid']."', '".date('Y-m-d')."')";
	$result = mysqli_query($dbc, $query_time);
	insert_day_overview($dbc, $_SESSION['contactid'], 'Task', date('Y-m-d'), '', "Added Time to Task #$taskid on Project #$projectid - Added Time : $time");
}
if($_GET['fill'] == 'assigntask') {
	$taskid = $_GET['id'];
	$status = $_GET['assign'];
    $updated_date = date('Y-m-d');
    $updated_by = $_SESSION['contactid'];

    $projectid = mysqli_fetch_array(mysqli_query($dbc, "SELECT `projectid` FROM `tasklist` WHERE `tasklistid` = '$taskid'"))['projectid'];
    $customers = mysqli_fetch_array(mysqli_query($dbc, "SELECT `clientid` FROM `project` WHERE `projectid` = '$projectid'"))['clientid'];
    $customerids = explode(',', $customers);
    $customernames = '';
    foreach($customerids as $id) {
        if ($customernames == '') {
            $customernames .= get_contact($dbc, $id);
        } else {
            $customernames .= ', ' . get_contact($dbc, $id);
        }
    }
    $note = htmlentities('<br />' . ($status == 1 ? 'This item has been assigned to ' : 'This item has been unassigned to ') . $customernames . ' by ' . get_contact($dbc, $updated_by) . ' at ' . date('Y-m-d, g:i:s A'));

    $query_update = "UPDATE `tasklist` SET `assign_client`='$status', `task`=CONCAT(`task`,'$note') WHERE `tasklistid`='$tasklistid'";
    $result = mysqli_query($dbc, $query_update);

    $task = mysqli_fetch_array(mysqli_query($dbc, "SELECT `task` FROM `tasklist` WHERE `tasklistid`='$taskid'"))['task'];
    $html = 'Task #'.$taskid.': '.html_entity_decode($task);

    echo $html;
}

if($_GET['fill'] == 'add_ticket') {

    $projectid = $_GET['projectid'];
    $project_path = get_project($dbc, $projectid, 'project_path');
    $businessid = get_project($dbc, $projectid, 'businessid');
    $clientid = get_project($dbc, $projectid, 'clientid');
    $heading = $_GET['heading'];
    $milestone_timeline = $_GET['milestone_timeline'];

    $contactid = ','.$_SESSION['contactid'].',';
	$created_date = date('Y-m-d');
    $created_by = $_SESSION['contactid'];
    $status = 'To Be Scheduled';

	$heading = str_replace("FFMEND","&",$heading);
    $heading = str_replace("FFMSPACE"," ",$heading);
    $heading = str_replace("FFMHASH","#",$heading);
    $heading = filter_var($heading,FILTER_SANITIZE_STRING);

	$milestone_timeline = str_replace("FFMEND","&",$milestone_timeline);
    $milestone_timeline = str_replace("FFMSPACE"," ",$milestone_timeline);
    $milestone_timeline = str_replace("FFMHASH","#",$milestone_timeline);
    $milestone_timeline = filter_var($milestone_timeline,FILTER_SANITIZE_STRING);

    if($heading != '') {
        $query_insert_log = "INSERT INTO `tickets` (`businessid`, `clientid`, `projectid`, `heading`, `contactid`, `created_date`, `created_by`, `status`, `project_path`,`milestone_timeline`) VALUES ('$businessid', '$clientid', '$projectid', '$heading', '$contactid', '$created_date', '$created_by', '$status', '$project_path', '$milestone_timeline')";
        $result_insert_log = mysqli_query($dbc, $query_insert_log);
    }
}
if($_GET['fill'] == 'review_project') {
	$projectid = $_GET['project'];
	$contactid = $_GET['contact'];

	$query_review = "UPDATE `project` SET `review_date` = CURRENT_TIMESTAMP, `reviewer_id` = '$contactid' WHERE `projectid`='$projectid'";
	$result_review = mysqli_query($dbc, $query_review);

	$user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
	mysqli_query($dbc, "INSERT INTO `project_history` (`updated_by`, `description`, `projectid`) VALUES ('$user', 'Reviewed by ".get_contact($dbc,$contactid)."', '$projectid')");
}
if($_GET['fill'] == 'ticketsendnote') {
	$item_id = $_POST['id'];
	$user = $_SESSION['contactid'];
	$note = filter_var(htmlentities('<p>'.$_POST['note'].'</p>'),FILTER_SANITIZE_STRING);
	$query_insert_note = "INSERT INTO `ticket_comment` (`ticketid`, `comment`, `created_date`, `created_by`, `type`, `note_heading`) VALUES ('$item_id', '$note', CURDATE(), '$user', 'note', 'Quick Note')";
	$result = mysqli_query($dbc, $query_insert_note);
}
if($_GET['fill'] == 'ticketsendemail') {
	$item_id = $_POST['id'];
	$sender = get_email($dbc, $_SESSION['contactid']);
	$to = get_email($dbc, $_POST['user']);
	$ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM tickets WHERE ticketid='$item_id'"));
	$subject = "A reminder about ".TICKET_NOUN." #$item_id";
	$body = 'Please review the following ticket.<br/><br/>
			Client: '.get_client($dbc,$ticket['businessid']).'<br>
			'.TICKET_NOUN.' Heading: '.$ticket['heading'].'<br>
			Status: '.$ticket['status'].'<br>
			<a target="_blank" href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].'">'.TICKET_NOUN.' #'.$ticket['ticketid'].'</a><br/><br/><br/>
			<img src="'.WEBSITE_URL.'/img/ffm-signature.png" width="154" height="77" border="0" alt="">';
	send_email($sender, $to, '', '', $subject, $body);
}
if($_GET['fill'] == 'ticketsendreminder') {
	$item_id = $_POST['id'];
	$sender = get_email($dbc, $_SESSION['contactid']);
	$date = $_POST['schedule'];
	$to = $_POST['user'];
	$ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM tickets WHERE ticketid='$item_id'"));
	$subject = "A reminder about ".TICKET_NOUN." #$item_id";
	$body = htmlentities('Please review the following ticket.<br/><br/>
			Client: '.get_client($dbc,$ticket['businessid']).'<br>
			'.TICKET_NOUN.' Heading: '.$ticket['heading'].'<br>
			Status: '.$ticket['status'].'<br>
			<a target="_blank" href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].'">'.TICKET_NOUN.' #'.$ticket['ticketid'].'</a><br/><br/><br/>
			<img src="'.WEBSITE_URL.'/img/ffm-signature.png" width="154" height="77" border="0" alt="">');
	$result = mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_time`, `reminder_type`, `subject`, `body`, `sender`)
		VALUES ('$to', '$date', '08:00:00', 'QUICK', '$subject', '$body', '$sender')");
}
if($_GET['fill'] == 'ticketsendalert') {
	$ticketid = $_POST['id'];
	$user = $_POST['user'];
	$link = WEBSITE_URL."/Ticket/index.php?edit=".$ticketid;
	$text = TICKET_NOUN;
	$date = date('Y/m/d');
	$sql = mysqli_query($dbc, "INSERT INTO `alerts` (`alert_date`, `alert_link`, `alert_text`, `alert_user`) VALUES ('$date', '$link', '$text', '$user')");
}
if($_GET['fill'] == 'ticketsendupload') {
	$ticketid = $_GET['id'];
	$user = $_SESSION['contactid'];
	$filename = htmlspecialchars($_FILES['file']['name'], ENT_QUOTES);
	$file = $_FILES['file']['tmp_name'];
    if (!file_exists('../Ticket/download')) {
        mkdir('..Ticket/download', 0777, true);
    }
	move_uploaded_file($file, '../Ticket/download/'.$filename);
	$filename = filter_var($filename,FILTER_SANITIZE_STRING);
	$query_insert = "INSERT INTO `ticket_document` (`ticketid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$ticketid', 'Support Document', 'download/$filename', CURDATE(), '$user')";
	$result_insert = mysqli_query($dbc, $query_insert);echo $query_insert;
}
if($_GET['fill'] == 'ticketflag') {
	$item_id = $_POST['id'];
	$colour = mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colour` FROM tickets WHERE ticketid = '$item_id'"))['flag_colour'];
	$colour_list = explode(',', get_config($dbc, "ticket_colour_flags"));
	$colour_key = array_search($colour, $colour_list);
	$new_colour = ($colour_key === FALSE ? $colour_list[0] : ($colour_key + 1 < count($colour_list) ? $colour_list[$colour_key + 1] : ''));
	$result = mysqli_query($dbc, "UPDATE `tickets` SET `flag_colour`='$new_colour' WHERE `ticketid` = '$item_id'");
	echo $new_colour;
}
if($_GET['fill'] == 'ticketquickarchive') {
	$ticketid = $_GET['id'];
	$query_archive = "UPDATE `tickets` SET status = 'Archive', `status_date`=CURDATE() WHERE ticketid='$ticketid'";
	$result = mysqli_query($dbc, $query_archive);
}
if($_GET['fill'] == 'ticketquicktime') {
	$ticketid = $_POST['id'];
	$time = strtotime($_POST['time']);
	$current_time = strtotime(mysqli_fetch_array(mysqli_query($dbc, "SELECT `spent_time` FROM `tickets` WHERE `ticketid`='$ticketid'"))['spent_time']);
	$total_time = date('H:i:s', $time + $current_time - strtotime('00:00:00'));
	$query_time = "UPDATE `tickets` SET `spent_time` = '$total_time' WHERE ticketid='$ticketid'";
	$result = mysqli_query($dbc, $query_time);
	insert_day_overview($dbc, $_SESSION['contactid'], 'Ticket', date('Y-m-d'), '', "Updated ".TICKET_NOUN." #$ticketid - Manually Added Time : ".$_POST['time']);
}
if($_GET['fill'] == 'ticket_template_service_list') {
	$id = $_GET['id'];
	$services = mysqli_query($dbc, "SELECT `serviceid`, CONCAT(`service_type`,': ',`category`) `groups`, `heading` FROM `services` ORDER BY `groups`, `heading`");
	$category = '';
	echo "<option></option>";
	while($service = mysqli_fetch_array($services)) {
		if($category != $service['groups']) {
			if($category != '') {
				echo "</optgroup>";
			}
			$category = $service['groups'];
			echo "<optgroup label='$category'>";
		}
		echo "<option ".($id == $service['serviceid'] ? 'selected' : '')." value='".$service['serviceid']."'>".$service['heading']."</option>";
	}
	if($category != '') {
		echo "</optgroup>";
	}
}
if($_GET['fill'] == 'assign_review') {
	$project = $_POST['project'];
	$staff = $_POST['staff'];
	$date = $_POST['date'];
	echo "UPDATE `client_project` SET `assign_review_date`='$date', `assign_review_id`='$staff' WHERE `projectid`='$project'";
	mysqli_query($dbc, "UPDATE `client_project` SET `assign_review_date`='$date', `assign_review_id`='$staff' WHERE `projectid`='$project'");
}
if($_GET['fill'] == 'send_email') {
    $commid = $_GET['commid'];
    $comm_query = "select `from_email`, `from_name`, `subject`, `email_body`, `to_contact`, `cc_contact`, `to_staff`, `cc_staff` from email_communication where email_communicationid=$commid";
    $comm_result = mysqli_fetch_assoc(mysqli_query($dbc, $comm_query));
    $from_email = $comm_result['from_email'];
    $from_name = $comm_result['from_name'];
    $meeting_arr_email = explode(",", $comm_result['to_contact']);
    $meeting_cc_arr_email = explode(",", $comm_result['cc_contact']);
    $subject = html_entity_decode($comm_result['subject']);
    $from_name = $comm_result['from_name'];
    $send_body = html_entity_decode($comm_result['email_body']);
    send_email([$from_email => $from_name], $meeting_arr_email, $meeting_cc_arr_email , '', $subject, $send_body);
}
?>
