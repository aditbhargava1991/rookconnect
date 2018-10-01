<?php
include('../include.php');
date_default_timezone_set('America/Denver');
ob_clean();

/* ----- Dashboard ----- */
if ( $_GET['fill']=='changeStatus' ) {
    $type    = $_GET['type'];
    $soid    = $_GET['soid'];
    $status  = $_GET['status'];
    if ($type == 'sot') {
        $result_update = mysqli_query ( $dbc, "UPDATE `sales_order_temp` SET `status`='{$status}' WHERE `sotid`='{$soid}'" );
    } else {
        $result_update = mysqli_query ( $dbc, "UPDATE `sales_order` SET `status`='{$status}' WHERE `posid`='{$soid}'" );
    }
}

if ( $_GET['fill']=='changeNextAction' ) {
    $type          = $_GET['type'];
    $soid          = $_GET['soid'];
    $nextaction    = $_GET['nextaction'];
    if ($type == 'sot') {
        $result_update = mysqli_query ( $dbc, "UPDATE `sales_order_temp` SET `next_action`='{$nextaction}' WHERE `sotid`='{$soid}'" );
    } else {
        $result_update = mysqli_query ( $dbc, "UPDATE `sales_order` SET `next_action`='{$nextaction}' WHERE `posid`='{$soid}'" );
    }
}

if ( $_GET['fill']=='changeNextActionDate' ) {
    $type           = $_GET['type'];
    $soid           = $_GET['soid'];
    $nextActionDate = $_GET['nextActionDate'];
    if ($type == 'sot') {
        $result_update = mysqli_query ( $dbc, "UPDATE `sales_order_temp` SET `next_action_date`='{$nextActionDate}' WHERE `sotid`='{$soid}'" );
    } else {
        $result_update = mysqli_query ( $dbc, "UPDATE `sales_order` SET `next_action_date`='{$nextActionDate}' WHERE `posid`='{$soid}'" );
    }
}


/* ----- Details ----- */
// if ( $_GET['fill']=='setInventoryPricing' ) {
//     $pricing    = $_GET['pricing'];
//     $sotid  = $_GET['sotid'];
//     $result = mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `sotid` = '$sotid'");
//     if ($result->num_rows == 0) {
//         mysqli_query($dbc, "INSERT INTO `sales_order_temp` VALUES ()");
//         $sotid = mysqli_insert_id($dbc);
//         echo $sotid;
//     }

//     $contact_category = $_GET['contact_category'];
//     if ($contact_category == 'team') {
//         mysqli_query($dbc, "UPDATE `sales_order_temp` SET `inventory_pricing_team` = '$pricing' WHERE `sotid` = '$sotid'");
//     } else {
//         mysqli_query($dbc, "UPDATE `sales_order_temp` SET `inventory_pricing` = '$pricing' WHERE `sotid` = '$sotid'");
//     }

//     $result = mysqli_query($dbc, "SELECT * FROM `sales_order_product_temp` WHERE `parentsotid`='$sotid' AND `item_type`='inventory' AND `contact_category` = '$contact_category'");
//     if ( $result->num_rows > 0 ) {
//         while ( $row=mysqli_fetch_assoc($result) ) {
//             $product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `$pricing` AS `pricing` FROM `inventory` WHERE `inventoryid`='{$row['item_type_id']}'"));
//             mysqli_query($dbc, "UPDATE `sales_order_product_temp` SET `pricing`='$pricing', `item_price`='{$product['pricing']}' WHERE `item_type_id`='{$row['item_type_id']}' AND `contact_category` = '$contact_category' AND `parentsotid` = '$sotid'");
//         }
//     }
// }

// if ( $_GET['fill']=='setVendorPricing' ) {
//     $pricing    = $_GET['pricing'];
//     $sotid  = $_GET['sotid'];
//     $result = mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `sotid` = '$sotid'");
//     if ($result->num_rows == 0) {
//         mysqli_query($dbc, "INSERT INTO `sales_order_temp` VALUES ()");
//         $sotid = mysqli_insert_id($dbc);
//         echo $sotid;
//     }

//     $contact_category = $_GET['contact_category'];
//     if ($contact_category == 'team') {
//         mysqli_query($dbc, "UPDATE `sales_order_temp` SET `vendor_pricing_team` = '$pricing' WHERE `sotid` = '$sotid'");
//     } else {
//         mysqli_query($dbc, "UPDATE `sales_order_temp` SET `vendor_pricing` = '$pricing' WHERE `sotid` = '$sotid'");
//     }

//     $result = mysqli_query($dbc, "SELECT * FROM `sales_order_product_temp` WHERE `parentsotid`='$sotid' AND `item_type`='vendor' AND `contact_category` = '$contact_category'");
//     if ( $result->num_rows > 0 ) {
//         while ( $row=mysqli_fetch_assoc($result) ) {
//             $product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `$pricing` AS `pricing` FROM `vendor_price_list` WHERE `inventoryid`='{$row['item_type_id']}'"));
//             mysqli_query($dbc, "UPDATE `sales_order_product_temp` SET `pricing`='$pricing', `item_price`='{$product['pricing']}' WHERE `item_type_id`='{$row['item_type_id']}' AND `contact_category` = '$contact_category' AND `parentsotid` = '$sotid'");
//         }
//     }
// }

if ( $_GET['fill']=='removeItem' ) {
    if($_GET['from_type'] == 'template') {
        $id = $_GET['id'];
        $result_update = mysqli_query ( $dbc, "DELETE FROM `sales_order_template_product` WHERE `id` = '$id'" );
    } else {
        $sotid = $_GET['sotid'];
        $result_update = mysqli_query ( $dbc, "DELETE FROM `sales_order_product_temp` WHERE `sotid`='$sotid'" );
    }
}

if ( $_GET['fill']=='changeCustomer') {
    $customerid = $_GET['businessid'];
    $sotid = $_GET['sotid'];
    $history = '';

    $result = mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `sotid` = '$sotid'");
    if ($result->num_rows > 0) {
        mysqli_query($dbc, "UPDATE `sales_order_temp` SET `customerid` = '$customerid' WHERE `sotid` = '$sotid'");
    } else {
        mysqli_query($dbc, "INSERT INTO `sales_order_temp` (`customerid`) VALUES ('$customerid')");
        $sotid = mysqli_insert_id($dbc);
        echo $sotid;
        $history .= 'Created new '.SALES_ORDER_NOUN.'<br />';
    }
    $history .= 'Attached '.SALES_ORDER_NOUN.' to '.get_client($dbc, $customerid).'<br />';

    //History
    if($history != '') {
        $historyid = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`id`) FROM `sales_order_history` WHERE `sales_order_id`='$sotid' AND `contactid`='".$_SESSION['contactid']."' AND `date` >= '".date('Y-m-d H:i:s',strtotime('-15min'))."'"))[0];
        if($historyid > 0) {
            mysqli_query($dbc, "UPDATE `sales_order_history` SET `history`=CONCAT(`history`,'".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."') WHERE `id`='$historyid'");
        } else {
            mysqli_query($dbc, "INSERT INTO `sales_order_history` (`sales_order_id`, `history`, `contactid`) VALUES ('$sotid', '".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."', '".$_SESSION['contactid']."')");
        }
    }
}

if ( $_GET['fill']=='deleteContactId') {
    $contactid = $_GET['contactid'];
        $date_of_archival = date('Y-m-d');

    mysqli_query($dbc, "UPDATE `contacts` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `contactid` = '$contactid'");
}

/* Iframe */
if ( $_GET['fill']=='changeCategory' ) {
    $category  = $_GET['category'];
    $item_type = $_GET['item_type'];
    $table = ($category == 'vendor') ? 'vendor_price_list' : $category;

    if($category == 'services') {
        $result = mysqli_query($dbc, "SELECT `serviceid`, `heading` FROM `$table` WHERE `category`='$category' AND `deleted`=0");
        echo '<option value=""></option>';
        while ( $row=mysqli_fetch_assoc($result) ) {
            echo '<option value="'. $row['serviceid'] .'**#**'. $row['heading'] .'">'. $row['heading'] .'</option>';
        }
    } else {
        $result = mysqli_query($dbc, "SELECT `inventoryid`, `name` FROM `$table` WHERE `category`='$category' AND `deleted`=0");
        echo '<option value=""></option>';
        while ( $row=mysqli_fetch_assoc($result) ) {
            echo '<option value="'. $row['inventoryid'] .'**#**'. $row['name'] .'">'. $row['name'] .'</option>';
        }
    }
}

// if ( $_GET['fill']=='changeItem' ) {
//     $invid     = $_GET['invid'];
//     $pricing   = $_GET['pricing'];
//     $item_type = $_GET['item_type'];
//     $table = ($category == 'vendor') ? 'vendor_price_list' : $category;

//     if($category == 'services') {
//         $result   = mysqli_query($dbc, "SELECT DISTINCT(`category`) FROM `$table` WHERE `deleted`=0 ORDER BY `category`");
//         $category = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `category` FROM `$table` WHERE `serviceid`='$invid'"));

//         echo '<option value=""></option>';
//         while ( $row=mysqli_fetch_assoc($result) ) {
//             $selected = ( $row['category']==$category['category'] ) ? 'selected="selected"' : '';
//             echo '<option value="'. $row['category'] .'" '. $selected .'>'. $row['category'] .'</option>';
//         }

//         echo '*#*';

//         $price = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `serviceid`, `$pricing` AS `pricing` FROM `$table` WHERE `serviceid`='$invid' AND `deleted`=0"));
//         echo ( !empty($price['pricing']) ) ? $price['pricing'] : '0';
//     } else {
//         $result   = mysqli_query($dbc, "SELECT DISTINCT(`category`) FROM `$table` WHERE `deleted`=0 ORDER BY `category`");
//         $category = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `category` FROM `$table` WHERE `inventoryid`='$invid'"));

//         echo '<option value=""></option>';
//         while ( $row=mysqli_fetch_assoc($result) ) {
//             $selected = ( $row['category']==$category['category'] ) ? 'selected="selected"' : '';
//             echo '<option value="'. $row['category'] .'" '. $selected .'>'. $row['category'] .'</option>';
//         }

//         echo '*#*';

//         $price = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `inventoryid`, `$pricing` AS `pricing` FROM `$table` WHERE `inventoryid`='$invid' AND `deleted`=0"));
//         echo ( !empty($price['pricing']) ) ? $price['pricing'] : '0';
//     }
// }

if ($_GET['fill']=='uploadCsv') {
    $category = $_GET['category'];
    $file_name = htmlspecialchars($_FILES['csv_file']['name']);
    $file =  htmlspecialchars($_FILES['csv_file']['tmp_name']);

    $handle = fopen($file, 'r');
    $headers = fgetcsv($handle, 0, ',');

    $contacts = [];

    while (($csv = fgetcsv($handle, 0, ',')) !== FALSE) {
        $num = count($csv);
        $values = ['category' => $category, 'first_name' => '', 'last_name' => '', 'email_address' => '', 'user_name' => '', 'user_name_use_email' => '', 'password' => '', 'password_auto_generate' => '', 'email_login_credentials' => '', 'player_number' => ''];
        for ($i = 0; $i < $num; $i++) {
            $values[$headers[$i]] = trim(mysqli_real_escape_string($dbc, htmlspecialchars_decode($csv[$i],ENT_NOQUOTES)));
        }
        $contacts[] = $values;
    }

    fclose($handle);

    echo json_encode($contacts);
}

if ($_GET['fill']=='previewDetails') {
    $div = $_GET['div'];
    $cat_config = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_so_contacts`"),MYSQLI_ASSOC);
    foreach($cat_config as $contact_cat) {
        $contact_category = $contact_cat['contact_category'];
        if($div == 'preview_'.$contact_category.'_order') {
            $sotid = $_POST['sotid'];
            $heading_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT `heading_name` FROM `sales_order_product_temp` WHERE `parentsotid` = '$sotid' AND `contact_category` = '$contact_category'"),MYSQLI_ASSOC);
            if (!empty($heading_list)) {
                echo '<h5 style="display: inline;">'.strtoupper($contact_category).' ORDER DETAILS:</h5><br>';
            }
            foreach($heading_list as $heading) {
                $heading_name = $heading['heading_name'];
                echo '<b>'.$heading_name.'</b></br>';
                echo '<ul>';
                $item_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `sales_order_product_temp` WHERE `parentsotid` = '$sotid' AND `contact_category` = '$contact_category' AND `heading_name` = '$heading_name'"),MYSQLI_ASSOC);
                foreach ($item_list as $item) {
                    echo '<li>';
                    if (!empty($item['item_category'])) {
                        echo $item['item_category'].': ';
                    }
                    echo $item['item_name'].'</li>';
                }
                echo '</ul>';
            }
            echo '<hr>';
            break;
        } else if($div == 'preview_'.$contact_category.'_roster') {
            $customerid = $_POST['customerid'];
            $contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` = '$contact_category' AND `businessid` = '$customerid' AND `deleted` = 0"),MYSQLI_ASSOC));
            if (!empty($contact_list)) {
                echo '<h5 style="display: inline;">'.strtoupper($contact_category).' ROSTER:</h5><br>';
            }
            foreach ($contact_list as $contactid) {
                echo get_contact($dbc, $contactid)."<br>";
            }
            break;
        }
    }
    if(empty($cat_config)) {
        if($div == 'preview_nocat_order') {
            $sotid = $_POST['sotid'];
            $heading_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT `heading_name` FROM `sales_order_product_temp` WHERE `parentsotid` = '$sotid' AND `contact_category` = '**no_cat**'"),MYSQLI_ASSOC);
            if (!empty($heading_list)) {
                echo '<h5 style="display: inline;">'.strtoupper(SALES_ORDER_NOUN).' OPTIONS:</h5><br>';
            }
            foreach($heading_list as $heading) {
                $heading_name = $heading['heading_name'];
                echo '<b>'.$heading_name.'</b></br>';
                echo '<ul>';
                $item_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `sales_order_product_temp` WHERE `parentsotid` = '$sotid' AND `contact_category` = '**no_cat**' AND `heading_name` = '$heading_name'"),MYSQLI_ASSOC);
                foreach ($item_list as $item) {
                    echo '<li>';
                    if (!empty($item['item_category'])) {
                        echo $item['item_category'].': ';
                    }
                    echo $item['item_name'].'</li>';
                }
                echo '</ul>';
            }
            echo '<hr>';
            break;
        }
    }

    switch ($div) {
        case 'preview_customer':
            $customerid = $_POST['customerid'];
            $customer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '$customerid'"));
            if (!empty($customer['name'])) {
                echo "<b>".decryptIt($customer['name'])."</b><br>";
            }
            if (!empty($customer['mailing_address'])) {
                echo "<b>".$customer['mailing_address']."</b><br>";
            }
            if (!empty($customer['city']) || !empty($customer['province']) || !empty($customer['country'])) {
                $city_province_country = '';
                if (!empty($customer['city'])) {
                    $city_province_country .= $customer['city'].', ';
                }
                if (!empty($customer['province'])) {
                    $city_province_country .= $customer['province'].', ';
                }
                if (!empty($customer['country'])) {
                    $city_province_country .= $customer['country'];
                }
                $city_province_country = rtrim($city_province_country, ',');
                echo "<b>".$city_province_country."</b><br>";
            }
            if (!empty($customer['postal_code'])) {
                echo "<b>".$customer['postal_code']."</b><br>";
            }
            if (!empty($customer['office_phone'])) {
                echo "<b>".decryptIt($customer['office_phone'])."</b><br>";
            }
            if (!empty($customer['email_address'])) {
                echo "<b>".decryptIt($customer['email_address'])."</b><br>";
            }
            break;
        case 'preview_logo':
            $sotid = $_POST['sotid'];
            $logo = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `sotid` = '$sotid'"))['logo'];
            if (empty($logo)) {
                $logo = get_config($dbc, 'sales_order_logo');
            }

            if (!empty($logo)) {
                echo '<h5 style="display: inline;">LOGO: </h5><a href="download/'.$logo.'" target="_blank">View</a><br />';
            }
            break;
    }
}

if ($_GET['fill']=='deleteSalesOrderForm') {
    $sotid = $_GET['sotid'];
        $date_of_archival = date('Y-m-d');

    mysqli_query($dbc, "UPDATE `sales_order_temp` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `sotid` = '$sotid'");
    $history = 'Deleted '.SALES_ORDER_NOUN.' Form<br />';

    //History
    if($history != '') {
        $historyid = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`id`) FROM `sales_order_history` WHERE `sales_order_id`='$sotid' AND `contactid`='".$_SESSION['contactid']."' AND `date` >= '".date('Y-m-d H:i:s',strtotime('-15min'))."'"))[0];
        if($historyid > 0) {
            mysqli_query($dbc, "UPDATE `sales_order_history` SET `history`=CONCAT(`history`,'".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."') WHERE `id`='$historyid'");
        } else {
            mysqli_query($dbc, "INSERT INTO `sales_order_history` (`sales_order_id`, `history`, `contactid`) VALUES ('$sotid', '".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."', '".$_SESSION['contactid']."')");
        }
    }
}

if ($_GET['fill']=='deleteSalesOrderTemplate') {
    $templateid = $_GET['templateid'];
        $date_of_archival = date('Y-m-d');

    mysqli_query($dbc, "UPDATE `sales_order_template` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `id` = '$templateid'");
}

if ($_GET['fill']=='loadItems') {
    $table = $_GET['table'];
    $all_items = [];
    if($table == 'equipment') {
        $query = mysqli_query($dbc, "SELECT DISTINCT `unit_number`, `category` FROM `$table` WHERE `deleted`=0 AND `unit_number`<>'' ORDER BY `unit_number`");
        while($row = mysqli_fetch_array($query)) {
            $all_items[] = ['category'=>$row['category'], 'label'=>$row['unit_number'], 'value'=>htmlspecialchars($row['unit_number'])];
        }
    } else if($table == 'labour') {
        $query = mysqli_query($dbc, "SELECT DISTINCT `heading`, `labour_type`, `category` FROM `$table` WHERE `deleted`=0 AND `heading`<>'' ORDER BY `heading`");
        while($row = mysqli_fetch_array($query)) {
            $all_items[] = ['labour_type'=>$row['labour_type'], 'category'=>$row['category'], 'label'=>$row['heading'], 'value'=>htmlspecialchars($row['heading'])];
        }
    } else if($table == 'services') {
        $query = mysqli_query($dbc, "SELECT DISTINCT `heading`, `category` FROM `$table` WHERE `deleted`=0 AND `heading`<>'' ORDER BY `heading`");
        while($row = mysqli_fetch_array($query)) {
            $all_items[] = ['category'=>$row['category'], 'subcategory'=>'', 'label'=>$row['heading'], 'value'=>htmlspecialchars($row['heading'])];
        }
    } else {
        $query = mysqli_query($dbc, "SELECT DISTINCT `name`, `category`, `sub_category` FROM `$table` WHERE `deleted`=0 AND `name`<>'' ORDER BY `name`");
        while($row = mysqli_fetch_array($query)) {
            $all_items[] = ['category'=>$row['category'], 'subcategory'=>$row['sub_category'], 'label'=>$row['name'], 'value'=>htmlspecialchars($row['name'])];
        }
    }
    echo json_encode($all_items);
}

if ($_GET['fill']=='loadItemDetails') {
    $category = mysqli_real_escape_string($dbc, $_POST['category']);
    $subcategory = mysqli_real_escape_string($dbc, $_POST['subcategory']);
    $labourtype = mysqli_real_escape_string($dbc, $_POST['labourtype']);
    $labourcategory = mysqli_real_escape_string($dbc, $_POST['labourcategory']);
    $name = mysqli_real_escape_string($dbc, $_POST['product_name']);
    $customer = mysqli_real_escape_string($dbc, $_POST['customer']);
    $item_type = $_POST['item_type'];
    switch($item_type) {
        case 'equipment':
            $table = 'equipment';
            $table_field = 'unit_number';
            $table_cat_field = 'category';
            $table_subcat_field = '';
            $table_id = 'equipmentid';
            $config_table = 'field_config_equipment';
            break;
        case 'labour':
            $table = 'labour';
            $table_field = 'heading';
            $table_cat_field = 'labour_type';
            $table_subcat_field = 'category';
            $table_id = 'labourid';
            $config_table = '';
            break;
        case 'services':
            $table = 'services';
            $table_field = 'heading';
            $table_cat_field = 'category';
            $table_subcat_field = 'service_type';
            $table_id = 'serviceid';
            $config_table = 'field_config_services';
            break;
        case 'vendor':
            $table = 'vendor_price_list';
            $table_field = 'name';
            $table_cat_field = 'category';
            $table_subcat_field = 'sub_category';
            $table_id = 'inventoryid';
            $config_table = 'field_config_vpl';
            break;
        case 'inventory':
            $table = 'inventory';
            $table_field = 'name';
            $table_cat_field = 'category';
            $table_subcat_field = 'sub_category';
            $table_id = 'inventoryid';
            $config_table = 'field_config_inventory';
            break;
    }

    $price_types = ['Client Price','Admin Price','Commercial Price','Wholesale Price','Final Retail Price','Preferred Price','Web Price','Purchase Order Price','Sales Order Price','Drum Unit Cost','Drum Unit Price','Tote Unit Cost','Suggested Retail Price','Rush Price','Unit Price','MSRP','Fee','Unit Cost','Rent Price','Hourly Rate','Purchase Order Price','Sales Order Price','HR Rate Work','HR Rate Travel','Field Day Cost','Field Day Billable','CDN Cost Per Unit','USD Cost Per Unit'];

    if($item_type == 'equipment') {
        include('../Equipment/field_list.php');
        $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT equipment_dashboard FROM field_config_equipment WHERE tab='$category' AND accordion IS NULL UNION SELECT equipment_dashboard FROM field_config_equipment WHERE `tab` NOT IN ('service_record','service_request') AND equipment_dashboard IS NOT NULL"));
        $field_config = explode(',', $field_config['equipment_dashboard']);
    } else if($item_type == 'labour') {
        include('../Labour/field_list.php');
        $price_types = [];
        $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `labour_dashboard` FROM `field_config` WHERE `labour_dashboard` IS NOT NULL AND `labour_dashboard` != ''"));
        $field_config = explode(',', $field_config['labour_dashboard']);
    } else if($item_type == 'services') {
        include('../Services/field_list.php');
        $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `services_dashboard` FROM `field_config` WHERE `fieldconfigid` = 1"));
        $field_config = explode(',',$field_config['services_dashboard']);
    } else {
        include('../Inventory/field_list.php');
        $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `inventory_dashboard` FROM `$config_table` WHERE `tab` = '$category' AND `inventory_dashboard` IS NOT NULL && `inventory_dashboard` != ''"));
        $field_config = explode(',', $field_config['inventory_dashboard']);
    }

    if($item_type == 'labour') {
        $filter_query = "`deleted` = 0 AND `labour_type` = '$labourtype'";
        if(!empty($labourcategory)) {
            $filter_query .= " AND `category` = '$labourcategory'";
        }
    } else {
        $filter_query = "`deleted` = 0 AND `category` = '$category'";
        if(!empty($subcategory)) {
            $filter_query .= " AND `$table_subcat_field` = '".$subcategory."'";
        }
    }
    if(!empty($name)) {
        $filter_query .= " AND `$table_field` = '$name'";
    }

    $item_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `$table` WHERE $filter_query"),MYSQLI_ASSOC);

    $html = '<table class="table table-bordered table-striped">';
    $html .= '<thead><tr class="hidden-xs">';

    foreach ($field_config as $key => $field) {
        if (in_array($field, $price_types)) {
            unset($field_config[$key]);
            $field_config[] = $field;
        }
    }
    $field_config = array_filter($field_config);

    $service_fields = ','.get_field_config($dbc,'services').',';
    $include_hours = false;
    if(strpos($service_fields, ',Estimated Hours,') !== false) {
        $include_hours = true;
    }

    // if($name == '**NEW_ITEM**') {
        if($item_type == 'equipment') {
            if($name != '**NEW_ITEM**') {
                $html .= '<th>Name</th>';
            }
        } else if($item_type == 'services' || $item_type == 'labour') {
            $html .= '<th>Heading</th>';
        } else {
            $html .= '<th>Name</th>';
        }
    // }
    foreach ($field_config as $field) {
        if ($field != 'Labour Type' && $field != 'Rate Card' && $field != 'Rate Card Price' && $field != 'Category' && $field != 'Subcategory' && $field != 'Name' && $field != 'Heading' && $field != 'Estimated Hours' && $field != 'Staff') {
            $html .= '<th>'.$field.'</th>';
        }
    }
    if($name != '**NEW_ITEM**') {
    	$html .= '<th>Rate Price</th>';
    }
    if($item_type == 'services' && $include_hours) {
        $html .= '<th>Time Estimate</th>';
    }
    $html .= '<th width="10%">'.SALES_ORDER_NOUN.' Price</th>';
    if($name != '**NEW_ITEM**') {
        $html .= '<th width="5%">Add Item</th>';
    }
    $html .= '</tr></thead>';

    if($name == '**NEW_ITEM**') {
        $html .= '<tr>';
        if($item_type == 'labour') {
            $html .= '<input type="hidden" name="new_item_category[]" value="'.$labourtype.'">';
            $html .= '<input type="hidden" name="new_item_subcategory[]" value="'.$labourcategory.'">';
        } else {
            $html .= '<input type="hidden" name="new_item_category[]" value="'.$category.'">';
            $html .= '<input type="hidden" name="new_item_subcategory[]" value="'.$subcategory.'">';
        }
        if($item_type == 'equipment') {
        } else if($item_type == 'services' || $item_type == 'labour') {
            $html .= '<td data-title="Heading"><input type="text" name="new_item_heading[]" class="form-control"></td>';
        } else {
            $html .= '<td data-title="Name"><input type="text" name="new_item_name[]" class="form-control"></td>';
        }
        foreach ($field_config as $field) {
            if ($field != 'Labour Type' && $field != 'Rate Card' && $field != 'Rate Card Price' && $field != 'Category' && $field != 'Subcategory' && $field != 'Name' && $field != 'Heading' && $field != 'Estimated Hours' && $field != 'Staff') {
                $field_key = array_search($field,$field_list);
                if(strpos($field_key, '**NOCSV**') === FALSE) {
                    $field_key = trim($field_key, '#');
                    if (in_array($field, $price_types)) {
                        $html .= '<td data-title="'.$field.'"><input type="number" name="new_item_'.$field_key.'[]" class="form-control" step="0.01"></td>';
                    } else {
                        $html .= '<td data-title="'.$field.'"><input type="text" name="new_item_'.$field_key.'[]" class="form-control"></td>';
                    }
                }
            }
        }
        if($item_type == 'services' && $include_hours) {
            $html .= '<td data-title="Time Estimate"><input type="text" name="time_estimate[]" value="" class="form-control timepicker" onchange="updateTime(this);"></td>';
        }
        $html .= '<td data-title="Price"><input type="number" step="0.01" name="new_price[]" value="" class="form-control" min="0.00" onchange="updatePrice(this);" onpaste="updatePrice(this);" oninput="updatePrice(this);"></td>';
        $html .= '</tr>';
    } else {
        foreach ($item_list as  $item) {
            $html .= '<tr>';
            if($item_type == 'equipment') {
                $html .= '<td data-title="Name">'.get_equipment_label($dbc, $item).'</td>';
            } else if($item_type == 'services' || $item_type == 'labour') {
                $html .= '<td data-title="Heading">'.$item['heading'].'</td>';
            } else {
                $html .= '<td data-title="Name">'.$item['name'].'</td>';
            }
            foreach ($field_config as $field) {
                if ($field != 'Labour Type' && $field != 'Rate Card' && $field != 'Rate Card Price' && $field != 'Category' && $field != 'Subcategory' && $field != 'Name' && $field != 'Heading' && $field != 'Estimated Hours' && $field != 'Staff') {
                    $field_key = array_search($field,$field_list);
                    if (in_array($field, $price_types)) {
                        $html .= '<td data-title="'.$field.'">'.number_format(trim((!empty($item[$field_key]) ? $item[$field_key] : '0.00'),'$'),2,'.','').'&nbsp;&nbsp;<input type="checkbox" onchange="setPrice(this);" style="transform: scale(1.5); position: relative; top: 0.2em;"></td>';
                    } else {
                        $html .= '<td data-title="'.$field.'">'.html_entity_decode($item[$field_key]).'</td>';
                    }
                }
            }

            if($item_type == 'equipment') {
                $rate_info = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='equipment' AND `description` IN (SELECT `unit_number` FROM `equipment` WHERE `equipmentid`='{$item[$table_id]}') AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')
                    UNION SELECT * FROM `equipment_rate_table` WHERE `deleted`=0 AND `equipment_id`='{$item[$table_id]}' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')
                    UNION SELECT * FROM `category_rate_table` WHERE `deleted`=0 AND `category` IN (SELECT `category` FROM `equipment` WHERE `equipmentid`='{$item[$table_id]}') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')"));
                $rate = 0;
                if($rate_info['cost'] > 0) {
                    $rate = $rate_info['cost'];
                }

                $html .= '<td data-title="Rate Price">'.number_format($rate,2,'.','').'&nbsp;&nbsp;<input type="checkbox" onchange="setPrice(this);" style="transform: scale(1.5); position: relative; top: 0.2em;"></td>';
            } else if($item_type == 'labour') {
                $rate_info = mysqli_query($dbc, "SELECT * FROM `tile_rate_card` WHERE `deleted` = 0 AND `tile_name` = 'labour' AND `src_id` = '{$item[$table_id]}' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
                $rates = [];
                while($row = mysqli_fetch_assoc($rate_info)) {
                    $rates[] = ['uom'=>$row['uom'],'price'=>$row['price']];
                }
                if(empty($rates)) {
                    $rates = ['price'=>0];
                }
                $html .= '<td data-title="Rate Price">';

                foreach($rates as $rate) {
                    $html .= (!empty($rate['uom']) ? $rate['uom'].': ' : '').'$'.number_format($rate['price'],2,'.','').'&nbsp;&nbsp;<input type="checkbox" onchange="setPrice(this);" data-price="'.number_format($rate['price'],2,'.','').'" style="transform: scale(1.5); position: relative; top: 0.2em;"><br>';
                }
                $html .= '</td>';
            } else {
    			$rate_info = $dbc->query("SELECT `$item_type` `price` FROM `rate_card` WHERE `clientid`='$customer' AND `clientid` > 0 AND CONCAT('**',`$item_type`,'#') LIKE '%**{$item[$table_id]}#%' AND `deleted`=0 AND `on_off`=1 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') UNION
    				SELECT `service_rate` `price` FROM `service_rate_card` WHERE `deleted`=0 AND `serviceid`='{$item[$table_id]}' AND '$item_type'='services' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') UNION
    				SELECT `cust_price` `price` FROM `company_rate_card` WHERE LOWER(`tile_name`)='$item_type' AND `item_id`='{$item[$table_id]}' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')")->fetch_assoc()['price'];
    			$rate = 0;
    			if(strpos($rate_info,'#') !== FALSE) {
    				foreach(explode('**',$rate_info) as $rate_line) {
    					$rate_line = explode('#',$rate_line);
    					if($rate_line[0] == $item[$table_id]) {
    						$rate = $rate_line[1];
    					}
    				}
    			} else {
    				$rate = $rate_info;
    			}
                $html .= '<td data-title="Rate Price">'.number_format($rate,2,'.','').'&nbsp;&nbsp;<input type="checkbox" onchange="setPrice(this);" style="transform: scale(1.5); position: relative; top: 0.2em;"></td>';
            }

            if($item_type == 'services' && $include_hours) {
                $html .= '<td data-title="Time Estimate"><input type="text" name="time_estimate[]" value="'.$item['estimated_hours'].'" class="form-control timepicker" data-initial="'.$item['estimated_hours'].'" onchange="updateTime(this);"></td>';
            }
            $html .= '<td data-title="Price"><input type="number" step="0.01" name="price[]" value="" class="form-control" min="0.00" onchange="updatePrice(this);" onpaste="updatePrice(this);" oninput="updatePrice(this);"></td>';
            if($item_type == 'equipment') {
                $item_value = get_equipment_label($dbc, $item);
            } else if($item_type == 'services' || $item_type == 'labour') {
                $item_value = $item['heading'];
            } else {
                $item_value = '';
                if(in_array('Size', $field_config) || in_array('Color', $field_config)) {
                    $item_value .= '(';
                    if(in_array('Color', $field_config)) {
                        $item_value .= $item['color'].', ';
                    }
                    if(in_array('Size', $field_config)) {
                        $item_value .= $item['size'];
                    }
                    $item_value .= ')';
                }
                $item_value = trim($item['name'].' '.$item_value);
            }

            $html .= '<td data-title="Add Item"><input type="checkbox" name="inventoryid[]" value="'.$item[$table_id].'*#*'.$item[$table_cat_field].'*#*'.htmlspecialchars($item_value).'" style="transform: scale(1.5); position: relative; top: 0.2em;" onclick="setEmptyPrice(this);"></td>';
            $html .= '</tr>';
        }
    }
    $html .= '</table>';

    echo $html;
}

if ($_GET['fill'] == 'updateProductPrice') {
    if($_GET['from_type'] == 'template') {
        $id = $_GET['id'];
        $price = number_format($_GET['price'], 2);

        mysqli_query($dbc, "UPDATE `sales_order_template_product` SET `item_price` = '$price' WHERE `id` = '$id'");
    } else {
        $main_sotid = $_GET['main_sotid'];
        $sotid = $_GET['sotid'];
        $price = number_format($_GET['price'], 2);

        $product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_product_temp` WHERE `sotid` = '$sotid'"));

        mysqli_query($dbc, "UPDATE `sales_order_product_temp` SET `item_price` = '$price' WHERE `sotid` = '$sotid'");
        if ($price != $product['price']) {
            $history = 'Updated Price of '.$product['item_category'].': '.$product['item_name'].' from '.$product['item_price'].' to '.$price.'<br />';
        }

        //History
        if($history != '') {
            $historyid = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`id`) FROM `sales_order_history` WHERE `sales_order_id`='$main_sotid' AND `contactid`='".$_SESSION['contactid']."' AND `date` >= '".date('Y-m-d H:i:s',strtotime('-15min'))."'"))[0];
            if($historyid > 0) {
                mysqli_query($dbc, "UPDATE `sales_order_history` SET `history`=CONCAT(`history`,'".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."') WHERE `id`='$historyid'");
            } else {
                mysqli_query($dbc, "INSERT INTO `sales_order_history` (`sales_order_id`, `history`, `contactid`) VALUES ('$main_sotid', '".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."', '".$_SESSION['contactid']."')");
            }
        }
    }

    echo $price;
}

if ($_GET['fill'] == 'updateProductTime') {
    if($_GET['from_type'] == 'template') {
        $id = $_GET['id'];
        $time_estimate = $_GET['time_estimate'];

        mysqli_query($dbc, "UPDATE `sales_order_template_product` SET `time_estimate` = '$time_estimate' WHERE `id` = '$id'");
    } else {
        $main_sotid = $_GET['main_sotid'];
        $sotid = $_GET['sotid'];
        $time_estimate = $_GET['time_estimate'];

        $product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_product_temp` WHERE `sotid` = '$sotid'"));

        mysqli_query($dbc, "UPDATE `sales_order_product_temp` SET `time_estimate` = '$time_estimate' WHERE `sotid` = '$sotid'");
        if ($time_estimate != $product['time_estimate']) {
            $history = 'Updated Time Estimate of '.$product['item_category'].': '.$product['item_name'].' from '.$product['time_estimate'].' to '.$time_estimate.'<br />';
        }

        //History
        if($history != '') {
            $historyid = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`id`) FROM `sales_order_history` WHERE `sales_order_id`='$main_sotid' AND `contactid`='".$_SESSION['contactid']."' AND `date` >= '".date('Y-m-d H:i:s',strtotime('-15min'))."'"))[0];
            if($historyid > 0) {
                mysqli_query($dbc, "UPDATE `sales_order_history` SET `history`=CONCAT(`history`,'".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."') WHERE `id`='$historyid'");
            } else {
                mysqli_query($dbc, "INSERT INTO `sales_order_history` (`sales_order_id`, `history`, `contactid`) VALUES ('$main_sotid', '".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."', '".$_SESSION['contactid']."')");
            }
        }
    }

    echo $time_estimate;
}

if ($_GET['fill'] == 'deleteDesign') {
    $main_sotid = $_GET['main_sotid'];
    $sotid = $_GET['sotid'];

    $design = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_upload_temp` WHERE `sotid` = '$sotid'"));

    mysqli_query($dbc, "DELETE FROM `sales_order_upload_temp` WHERE `sotid` = '$sotid'");
    $history = 'Deleted Design '.$design['name'].'<br />';

    //History
    if($history != '') {
        $historyid = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`id`) FROM `sales_order_history` WHERE `sales_order_id`='$main_sotid' AND `contactid`='".$_SESSION['contactid']."' AND `date` >= '".date('Y-m-d H:i:s',strtotime('-15min'))."'"))[0];
        if($historyid > 0) {
            mysqli_query($dbc, "UPDATE `sales_order_history` SET `history`=CONCAT(`history`,'".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."') WHERE `id`='$historyid'");
        } else {
            mysqli_query($dbc, "INSERT INTO `sales_order_history` (`sales_order_id`, `history`, `contactid`) VALUES ('$main_sotid', '".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."', '".$_SESSION['contactid']."')");
        }
    }

}

if ($_GET['fill'] == 'changeHeading') {
    $from_type = $_GET['from_type'];
    $item_type = $_GET['item_type'];
    $contact_category = $_GET['contact_category'];
    $heading_name = $_GET['heading_name'];

    if($from_type == 'template') {
        $templateid = $_GET['id'];
        $mandatory_quantity = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_template_product` WHERE `item_type` = '$item_type' AND `contact_category` = '$contact_category' AND `heading_name` = '$heading_name' AND `template_id` = '$templateid'"))['mandatory_quantity'];
    } else {
        $sotid = $_GET['id'];
        $mandatory_quantity = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_product_temp` WHERE `item_type` = '$item_type' AND `contact_category` = '$contact_category' AND `heading_name` = '$heading_name' AND `parentsotid` = '$sotid'"))['mandatory_quantity'];
    }
    echo $mandatory_quantity;
}

// if ($_GET['fill'] == 'loadTemplate') {
//     $history = '';
//     $templateid = $_GET['templateid'];
//     $template_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `template_name` FROM `sales_order_template` WHERE `id` = '$templateid'"))['template_name'];
//     $contactid = $_SESSION['contactid'];
//     $sotid = $_GET['sotid'];
//     if (empty($sotid)) {
//         mysqli_query($dbc, "INSERT INTO `sales_order_temp` VALUES ()");
//         $sotid = mysqli_insert_id($dbc);
//     }
//     $sales_order_name = mysqli_fetch_assoc(mysqlI_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `sotid` = '$sotid'"))['name'];
//     if(empty($sales_order_name)) {
//         $sales_order_name = 'Sales Order Form #'.$sotid;
//     }

//     mysqli_query($dbc, "DELETE FROM `sales_order_product_temp` WHERE `parentsotid` = '$sotid'");

//     $template_products = mysqli_query($dbc, "SELECT * FROM `sales_order_template_product` WHERE `template_id` = '$templateid'");
//     while ($row = mysqli_fetch_array($template_products)) {
//         $item_type = $row['item_type'];
//         $item_type_id = $row['item_type_id'];
//         $item_category = $row['item_category'];
//         $item_name = $row['item_name'];
//         $item_price = $row['item_price'];
//         $contact_category = $row['contact_category'];
//         $heading_name = $row['heading_name'];
//         $mandatory_quantity = $row['mandatory_quantity'];

//         mysqli_query($dbc, "INSERT INTO `sales_order_product_temp` (`contactid`, `item_type`, `item_type_id`, `item_category`, `item_name`, `item_price`, `contact_category`, `heading_name`, `mandatory_quantity`, `parentsotid`) VALUES ('$contactid', '$item_type', '$item_type_id', '$item_category', '$item_name', '$item_price', '$contact_category', '$heading_name', '$mandatory_quantity','$sotid')");
//     }
//     $history = 'Loaded Template '.$template_name.' into '.$sales_order_name.'<br />';

//     //History
//     if($history != '') {
//         $historyid = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`id`) FROM `sales_order_history` WHERE `sales_order_id`='$sotid' AND `contactid`='".$_SESSION['contactid']."' AND `date` >= '".date('Y-m-d H:i:s',strtotime('-15min'))."'"))[0];
//         if($historyid > 0) {
//             mysqli_query($dbc, "UPDATE `sales_order_history` SET `history`=CONCAT(`history`,'".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."') WHERE `id`='$historyid'");
//         } else {
//             mysqli_query($dbc, "INSERT INTO `sales_order_history` (`sales_order_id`, `history`, `contactid`) VALUES ('$sotid', '".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."', '".$_SESSION['contactid']."')");
//         }
//     }

//     echo $sotid;
// }

if ($_GET['fill'] == 'updateHeading') {
    $from_type = $_GET['from_type'];
    $item_type = $_GET['item_type'];
    $contact_category = $_GET['contact_category'];
    $old_heading_name = $_GET['old_heading_name'];
    $heading_name = $_GET['heading_name'];
    $mandatory_checkbox = $_GET['mandatory_checkbox'];
    if($mandatory_checkbox == 1) {
        $mandatory_quantity = $_GET['mandatory_quantity'];
    } else {
        $mandatory_quantity = 0;
    }

    if ($from_type == 'template') {
        $templateid = $_GET['templateid'];
        mysqli_query($dbc, "UPDATE `sales_order_template_product` SET `heading_name` = '$heading_name', `mandatory_quantity` = '$mandatory_quantity' WHERE `template_id` = '$templateid' AND `heading_name` = '$old_heading_name' AND `item_type` = '$item_type' AND `contact_category` = '$contact_category'");

    } else {
        $sotid = $_GET['sotid'];
        mysqli_query($dbc, "UPDATE `sales_order_product_temp` SET `heading_name` = '$heading_name', `mandatory_quantity` = '$mandatory_quantity' WHERE `parentsotid` = '$sotid' AND `heading_name` = '$old_heading_name' AND `item_type` = '$item_type' AND `contact_category` = '$contact_category'");
    }

    echo $heading_name.($mandatory_quantity > 0 ? ' (Mandatory Quantity: '.$mandatory_quantity.')' : '');
}

if ($_GET['fill'] == 'changeClassification') {
    $classification = $_GET['classification'];
    $sotid = $_GET['sotid'];
    $history = '';

    mysqli_query($dbc, "UPDATE `sales_order_temp` SET `classification` = '$classification' WHERE `sotid` = '$sotid'");
    $history .= 'Attached '.SALES_ORDER_NOUN.' to Classification '.$classification.'<br />';

    //History
    if($history != '') {
        $historyid = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`id`) FROM `sales_order_history` WHERE `sales_order_id`='$sotid' AND `contactid`='".$_SESSION['contactid']."' AND `date` >= '".date('Y-m-d H:i:s',strtotime('-15min'))."'"))[0];
        if($historyid > 0) {
            mysqli_query($dbc, "UPDATE `sales_order_history` SET `history`=CONCAT(`history`,'".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."') WHERE `id`='$historyid'");
        } else {
            mysqli_query($dbc, "INSERT INTO `sales_order_history` (`sales_order_id`, `history`, `contactid`) VALUES ('$sotid', '".filter_var(htmlentities($history),FILTER_SANITIZE_STRING)."', '".$_SESSION['contactid']."')");
        }
    }
}

if ($_GET['fill'] == 'downloadCsv') {
    $category = $_GET['category'];
    $cat_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_so_contacts` WHERE `contact_category` = '$category'"));
    $field_config = ','.$cat_config['fields'].',';
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    $FileName = 'download/sales_order_csv - '.$category.'.csv';
    $file = fopen($FileName, "w");

    $csv = [];
    if(strpos($field_config, ',First Name,') !== FALSE) {
        $csv[] = 'first_name';
    }
    if(strpos($field_config, ',Last Name,') !== FALSE) {
        $csv[] = 'last_name';
    }
    if(strpos($field_config, ',Email Address,') !== FALSE) {
        $csv[] = 'email_address';
    }
    if(strpos($field_config, ',Player Number,') !== FALSE) {
        $csv[] = 'player_number';
    }
    if(strpos($field_config, ',Username & Password,') !== FALSE) {
        $csv[] = 'user_name';
    }
    if(strpos($field_config, ',Email Address,') !== FALSE && strpos($field_config, ',Username & Password,') !== FALSE) {
        $csv[] = 'user_name_use_email';
    }
    if(strpos($field_config, ',Username & Password,') !== FALSE) {
        $csv[] = 'password';
    }
    if(strpos($field_config, ',Username & Password,') !== FALSE) {
        $csv[] = 'password_auto_generate';
    }
    if(strpos($field_config, ',Email Address,') !== FALSE && strpos($field_config, ',Username & Password,') !== FALSE) {
        $csv[] = 'email_login_credentials';
    }
    fputcsv($file, $csv);
    fclose($file);
    echo $FileName;
}

if ($_GET['fill'] == 'removeTemplateFromSO') {
    $sotid = $_POST['sotid'];
    $templateid = $_POST['templateid'];
    $load_type = $_POST['load_type'];
    if($load_type == 'sales_order') {
        $table_col = 'copied_sotid';
    } else if($load_type == 'template') {
        $table_col = 'templateid';
    }

    $sot = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `sotid` = '$sotid'"));
    $templateids = explode(',', $sot[$table_col]);
    foreach (array_keys($templateids, $templateid) as $key) {
        unset($templateids[$key]);
    }
    $templateids = implode(',', array_filter($templateids));
    mysqli_query($dbc, "UPDATE `sales_order_temp` SET `$table_col` = '$templateids' WHERE `sotid` = '$sotid'");

    mysqli_query($dbc, "DELETE FROM `sales_order_product_temp` WHERE `parentsotid` = '$sotid' AND `$table_col` = '$templateid'");
}

if ($_GET['fill'] == 'settingsDeleteLogo') {
    $logo = $_POST['logo'];

    if($logo == 'header') {
        mysqli_query($dbc, "UPDATE `field_config_so_pdf` SET `header_logo` = ''");
    } else if($logo == 'footer') {
        mysqli_query($dbc, "UPDATE `field_config_so_pdf` SET `footer_logo` = ''");
    }
}

if ($_GET['fill'] == 'reoderItems') {
    $sotid = $_POST['sotid'];
    $templateid = $_POST['templateid'];
    if($_POST['from_type'] == 'template') {
        $table = 'sales_order_template_product';
        $table_id = 'id';
    } else {
        $table = 'sales_order_product_temp';
        $table_id = 'sotid';
    }

    $contact_category = $_POST['contact_category'];
    $item_type = $_POST['item_type'];
    $heading_name = $_POST['heading_name'];
    $heading_sortorder = $_POST['heading_sortorder'];
    $items = $_POST['items'];
    if(!is_array($items)) {
        $items = [$items];
    }

    $i = 1;
    foreach($items as $item) {
        mysqli_query($dbc, "UPDATE `$table` SET `heading_sortorder` = '$heading_sortorder', `sortorder` = '$i' WHERE `$table_id` = '$item'");
        $i++;

    }
}
?>