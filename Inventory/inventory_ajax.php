<?php error_reporting(0);
include ('../database_connection.php');
include ('../function.php');
include ('../global.php');
include ('../email.php');

if(isset($_GET['fill'])) {
	if($_GET['fill'] == 'checklist') {
	    $checklistid = $_GET['checklistid'];
	    $checked = $_GET['checked'];
	    $updated_by = decryptIt($_SESSION['contactid']);
	    $updated_date = date('Y-m-d');
		$note = htmlentities('<br />'.($status == 1 ? 'Marked done' : 'Unchecked').' by '.get_contact($dbc, $updated_by).' at '.date('Y-m-d, g:i:s A'));

		$query = "UPDATE `item_checklist_line` SET  `checklist`=CONCAT(`checklist`,'$note'), `checked`='$checked'  WHERE `checklistlineid` = '$checklistid'";
		$result = mysqli_query($dbc, $query);
	}

	if($_GET['fill'] == 'checklist_priority') {print_r($_GET);
	    $lineid = $_GET['lineid'];
	    $afterid = $_GET['afterid'];
	    $checklistid = mysqli_fetch_array(mysqli_query($dbc, "SELECT `checklistid` FROM `item_checklist_line` WHERE `checklistlineid`='$lineid'"))['checklistid'];
	    $line_priority = mysqli_fetch_array(mysqli_query($dbc, "SELECT `priority` FROM `item_checklist_line` WHERE `checklistlineid`='$lineid'"))['priority'];
	    $after_priority = mysqli_fetch_array(mysqli_query($dbc, "SELECT `priority` FROM `item_checklist_line` WHERE `checklistlineid`='$afterid'"))['priority'];

		$query = "UPDATE `item_checklist_line` SET  `priority`=`priority`+1 WHERE `priority` > '$after_priority' AND `priority` < '$line_priority' AND `checklistid` = '$checklistid'";
		$result = mysqli_query($dbc, $query);echo $query;

		$query = "UPDATE `item_checklist_line` SET  `priority`='".($after_priority + 1)."' WHERE `checklistlineid` = '$checklistid'";
		$result = mysqli_query($dbc, $query);echo $query;

	}

	if($_GET['fill'] == 'add_checklist') {
		$checklistid = $_POST['checklist'];
		$checklist = filter_var($_POST['line'],FILTER_SANITIZE_STRING);
		$query_insert = "INSERT INTO `item_checklist_line` (`checklistid`, `checklist`, `priority`) SELECT '$checklistid', '$checklist', (IFNULL(MAX(`priority`),1)+1) FROM `item_checklist_line` WHERE `checklistid`='$checklistid'";
		mysqli_query($dbc, $query_insert);
	}

	if($_GET['fill'] == 'delete_checklist') {
		$id = $_GET['checklistid'];
    $date_of_archival = date('Y-m-d');
		$query = "UPDATE `item_checklist_line` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `checklistlineid`='$id'";
		$result = mysqli_query($dbc,$query);
	}
	if($_GET['fill'] == 'checklistreply') {
		$id = $_POST['id'];
		$reply = filter_var(htmlentities('<p>'.$_POST['reply'].'</p>'),FILTER_SANITIZE_STRING);
		$query = "UPDATE `item_checklist_line` SET `checklist`=CONCAT(`checklist`,'$reply') WHERE `checklistlineid`='$id'";
		$result = mysqli_query($dbc,$query);
	}
	if($_GET['fill'] == 'checklistalert') {
		$item_id = $_POST['id'];
		$type = $_POST['type'];
		$user = $_POST['user'];
		if($type == 'checklist') {
			$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM item_checklist_line WHERE checklistlineid='$item_id'"));
			$id = $result['checklistid'];
		}
		else {
			$id = $item_id;
		}
		$link = WEBSITE_URL."/Equipment/equipment_checklist.php";
		$text = "Checklist";
		$date = date('Y/m/d');
		$sql = mysqli_query($dbc, "INSERT INTO `alerts` (`alert_date`, `alert_link`, `alert_text`, `alert_user`) VALUES ('$date', '$link', '$text', '$user')");
	}
	if($_GET['fill'] == 'checklistemail') {
		$item_id = $_POST['id'];
		$type = $_POST['type'];
		$user = $_POST['user'];
		$subject = '';
		$title = '';
		if($type == 'checklist') {
			$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM item_checklist_line WHERE checklistlineid='$item_id'"));
			$id = $result['checklistid'];
			$title = explode('<p>',html_entity_decode($result['checklist']))[0];
			$subject = "A reminder about the $title on the checklist";
		}
		else {
			$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM checklist WHERE checklistid = '$item_id'"));
			$id = $item_id;
			$title = $result['item_checklist_line'];
			$subject = "A reminder about the $title checklist";
		}
		$contacts = mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$user'");
		while($row = mysqli_fetch_array($contacts)) {
			$email_address = get_email($dbc, $row['contactid']);
			if(trim($email_address) != '') {
				$body = "Hi ".decryptIt($row['first_name'])."<br />\n<br />
					This is a reminder about the $title on the equipment checklist.<br />\n<br />
					<a href='".WEBSITE_URL."/Equipment/equipment_checklist.php\">Click here</a> to see the checklists page.";
				send_email('', $email_address, '', '', $subject, $body, '');
			}
		}
	}
	if($_GET['fill'] == 'checklistreminder') {
		$item_id = $_POST['id'];
		$sender = get_email($dbc, $_SESSION['contactid']);
		$date = $_POST['schedule'];
		$type = $_POST['type'];
		$to = $_POST['user'];
		$subject = '';
		$title = '';
		if($type == 'checklist') {
			$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM item_checklist_line WHERE checklistlineid='$item_id'"));
			$id = $result['checklistid'];
			$title = explode('<p>',html_entity_decode($result['checklist']))[0];
			$subject = "A reminder about the $title on the equipment checklist";
			$body = htmlentities("This is a reminder about the $title on the equipment checklist.<br />\n<br />
				<a href=\"".WEBSITE_URL."/Equipment/equipment_checklist.php\">Click here</a> to see the checklists page.");
		}
		else {
			$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM checklist WHERE checklistid = '$item_id'"));
			$id = $item_id;
			$title = $result['item_checklist_line'];
			$subject = "A reminder about the $title checklist";
			$body = htmlentities("This is a reminder about the $title checklist.<br />\n<br />
				<a href=\"".WEBSITE_URL."/Equipment/equipment_checklist.php\">Click here</a> to see the checklists page.");
		}
		$result = mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_time`, `reminder_type`, `subject`, `body`, `sender`)
			VALUES ('$to', '$date', '08:00:00', 'QUICK', '$subject', '$body', '$sender')");
	}
	if($_GET['fill'] == 'checklistflag') {
		$item_id = $_POST['id'];
		$type = $_POST['type'];
		if($type == 'checklist') {
			$colour = mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colour` FROM item_checklist_line WHERE checklistlineid = '$item_id'"))['flag_colour'];
			$colour_list = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colours` FROM `field_config_checklist`"))['flag_colours']);
			$colour_key = array_search($colour, $colour_list);
			$new_colour = ($colour_key === FALSE ? $colour_list[0] : ($colour_key + 1 < count($colour_list) ? $colour_list[$colour_key + 1] : ''));
			$result = mysqli_query($dbc, "UPDATE `item_checklist_line` SET `flag_colour`='$new_colour' WHERE `checklistlineid` = '$item_id'");
			echo $new_colour;
		}
		else {
			$colour = mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colour` FROM item_checklist WHERE checklistid = '$item_id'"))['flag_colour'];
			$colour_list = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colours` FROM `field_config_checklist`"))['flag_colours']);
			$colour_key = array_search($colour, $colour_list);
			$new_colour = ($colour_key === FALSE ? $colour_list[0] : ($colour_key + 1 < count($colour_list) ? $colour_list[$colour_key + 1] : ''));
			$result = mysqli_query($dbc, "UPDATE `item_checklist` SET `flag_colour`='$new_colour' WHERE `checklistid` = '$item_id'");
			echo $new_colour;
		}
	}
	if($_GET['fill'] == 'checklist_upload') {
		$id = $_GET['id'];
		$type = $_GET['type'];
		$filename = $_FILES['file']['name'];
		$file = $_FILES['file']['tmp_name'];
	    if (!file_exists('download')) {
	        mkdir('download', 0777, true);
	    }
		$basefilename = $filename = preg_replace('/[^A-Za-z0-9\.]/','_',$filename);
		$i = 0;
		while(file_exists('download/'.$filename)) {
			$filename = preg_replace('/(\.[A-Za-z0-9]*)/', '('.++$i.')$1', $basefilename);
		}
		move_uploaded_file($file, "download/".$filename);
		if($type == 'checklist') {
			$query_insert = "INSERT INTO `item_checklist_document` (`checklistlineid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$id', 'Support Document', '$filename', '".date('Y/m/d')."', '".$_SESSION['contactid']."')";
			$result_insert = mysqli_query($dbc, $query_insert);
		}
		else if($type == 'checklist_board') {
			$query_insert = "INSERT INTO `item_checklist_document` (`checklistid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$id', 'Support Document', '$filename', '".date('Y/m/d')."', '".$_SESSION['contactid']."')";
			$result_insert = mysqli_query($dbc, $query_insert);

		}
	}
	if($_GET['fill'] == 'checklist_quick_time') {
		$checklistid = $_POST['id'];
		$time = $_POST['time'];
		$query_time = "INSERT INTO `item_checklist_time` (`checklistlineid`, `work_time`, `contactid`, `timer_date`) VALUES ('$checklistid', '$time', '".$_SESSION['contactid']."', '".date('Y-m-d')."')";
		$result = mysqli_query($dbc, $query_time);
		insert_day_overview($dbc, $_SESSION['contactid'], 'Checklist', date('Y-m-d'), '', "Updated Checklist Item #$checklistid - Added Time : $time");
	}
} else if(isset($_GET['action'])) {
	if($_GET['action'] == 'save_template_field') {
		$table = filter_var($_POST['table_name'],FILTER_SANITIZE_STRING);
		$heading_id = filter_var($_POST['heading_id'],FILTER_SANITIZE_STRING);
		$template_id = filter_var($_POST['template_id'],FILTER_SANITIZE_STRING);
		$field_name = filter_var($_POST['field_name'],FILTER_SANITIZE_STRING);
		$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);

		$sql = "";
		if($table == 'inventory_templates') {
			if(!is_numeric($template_id)) {
				mysqli_query($dbc, "INSERT INTO `inventory_templates` () VALUES ()");
				$template_id = mysqli_insert_id($dbc);
				echo $template_id;
			}
			$sql = "UPDATE `$table` SET `$field_name`='$value' WHERE `id`='$template_id'";
		} else if($table == 'inventory_templates_headings') {
			if(!is_numeric($heading_id)) {
				mysqli_query($dbc, "INSERT INTO `inventory_templates_headings` () VALUES ()");
				$heading_id = mysqli_insert_id($dbc);
				echo $heading_id;
			}
			$sql = "UPDATE `$table` SET `$field_name`='$value', `template_id`='$template_id' WHERE `id`='$heading_id'";
		}
		mysqli_query($dbc, $sql);
	} else if($_GET['action'] == 'set_sort_order') {
		$table = filter_var($_POST['table_name'],FILTER_SANITIZE_STRING);
		$i = 0;
		foreach($_POST['sort_ids'] as $id) {
			mysqli_query($dbc, "UPDATE `$table` SET `sort_order`='$i' WHERE `id`='$id'");
			$i++;
		}
	} else if($_GET['action'] == 'generate_import_csv') {
		include('field_list.php');
		$FileName = 'download/Add_multiple_inventory.csv';
		$file = fopen($FileName, "w");
		$HeadingsArray = [];
		foreach($field_list as $key => $field) {
			if(strpos($key, '#') === FALSE && strpos($key, '**NOCSV**') === FALSE) {
				$HeadingsArray[] = $key;
			}
		}
		fputcsv($file, $HeadingsArray);
		fclose($file);
		echo $FileName;
	} else if($_GET['action'] == 'dashboard_update') {
		$name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
		$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
		$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
		$dbc->query("UPDATE `inventory` SET `$name`='$value' WHERE `inventoryid`='$id'");
		$before_change = '';
    $history = "Inventory with id $id is been Updated. <br />";
    add_update_history($dbc, 'inventory_history', $history, '', $before_change);
		echo $dbc->query("SELECT `quantity` - `expected_inventory` `diff` FROM `inventory` WHERE `inventoryid`='$id'")->fetch_assoc()['diff'];
		if($name == 'quantity' && $_POST['ticket'] > 0) {
			$ticketid = $_POST['ticket'];
			$dbc->query("UPDATE `ticket_attached` SET `received`='$value' WHERE `ticketid`='$ticketid' AND `src_table`='inventory' AND `item_id`='$id' AND `deleted`=0");
		}
	} else if($_GET['action'] == 'category_list') {
		$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
		$po = filter_var($_POST['po'],FILTER_SANITIZE_STRING);
		$po_line = filter_var($_POST['po_line'],FILTER_SANITIZE_STRING);
		$ticket = filter_var($_POST['ticket'],FILTER_SANITIZE_STRING);
		$customer_order = filter_var($_POST['customer_order'],FILTER_SANITIZE_STRING);
		$detail_customer_order = filter_var($_POST['detail_customer_order'],FILTER_SANITIZE_STRING);
		$pallet = filter_var($_POST['pallet'],FILTER_SANITIZE_STRING);
		$cat_list = $po_list = $po_line_list = $ticket_list = $cust_order_list = $detail_cust_list = $pallet_list = [];
		echo '<option />';
		$list = $dbc->query("SELECT `inventory`.`inventoryid`, `inventory`.`product_name`, `inventory`.`name`, `inventory`.`category`, `inventory`.`pallet`, `inventory`.`quantity` - CAST(`inventory`.`assigned_qty` AS SIGNED INT) `available`, `tickets`.`ticketid`, `tickets`.`ticket_label`, `tickets`.`purchase_order`, `tickets`.`customer_order_num`, `tickets`.`po_line`, `tickets`.`position` FROM `inventory` LEFT JOIN (SELECT `tickets`.`ticketid`, `ticket_label`, `item_id`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `purchase_order`, IFNULL(NULLIF(`ticket_attached`.`position`,''),`tickets`.`customer_order_num`) `customer_order_num`, `ticket_attached`.`po_line`, `ticket_attached`.`position` FROM `ticket_attached` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_attached`.`deleted`=0 AND `tickets`.`deleted`=0 AND `ticket_attached`.`src_table` IN ('inventory','inventory_detailed')) `tickets` ON `inventory`.`inventoryid`=`tickets`.`item_id` WHERE `inventory`.`deleted`=0 AND '$category' IN ('',`inventory`.`category`) AND ('$po'='' OR CONCAT('#*#',`tickets`.`purchase_order`,'#*#') LIKE '%#*#$po#*#%') AND '$po_line' IN ('',`tickets`.`po_line`) AND '$detail_customer_order' IN ('',`tickets`.`position`) AND '$ticket' IN ('',`tickets`.`ticketid`) AND ('$customer_order'='' OR CONCAT('#*#',`tickets`.`customer_order_num`,'#*#') LIKE '%#*#$customer_order#*#%') AND '$pallet' IN ('',`inventory`.`pallet`) AND (IFNULL(`product_name`,'') != '' OR  IFNULL(`name`,'') != '') ORDER BY `inventory`.`category`, `inventory`.`product_name`, `inventory`.`name`");
		while($item = $list->fetch_assoc()) {
			echo '<option value="'.$item['inventoryid'].'" data-quantity="'.$item['available'].'">'.$item['product_name'].' '.$item['name'].'</option>';
			$cat_list[] = $item['category'];
			$po_list[] = $item['purchase_order'];
			$po_line_list[] = $item['po_line'];
			$ticket_list[$item['ticketid']] = $item['ticket_label'];
			$cust_order_list[] = $item['customer_order_num'];
			$detail_cust_list[] = $item['position'];
			$pallet_list[] = $item['pallet'];
		}
		echo '#*#';
		echo '<option />';
		foreach(array_unique($cat_list) as $cat_row) {
			echo '<option '.($category == $cat_row ? 'selected' : '').' value="'.$cat_row.'">'.$cat_row.'</option>';
		}
		echo '#*#';
		echo '<option />';
		foreach(array_unique($po_list) as $po_row) {
			echo '<option '.($po == $po_row ? 'selected' : '').' value="'.$po_row.'">'.$po_row.'</option>';
		}
		echo '#*#';
		echo '<option />';
		foreach(array_unique($po_line_list) as $po_row) {
			echo '<option '.($po_line == $po_row ? 'selected' : '').' value="'.$po_row.'">'.$po_row.'</option>';
		}
		echo '#*#';
		echo '<option />';
		foreach($ticket_list as $ticketid => $ticket_row) {
			echo '<option '.($ticket == $ticketid ? 'selected' : '').' value="'.$ticketid.'">'.$ticket_row.'</option>';
		}
		echo '#*#';
		echo '<option />';
		foreach(array_unique($cust_order_list) as $co_row) {
			echo '<option '.($customer_order == $co_row ? 'selected' : '').' value="'.$co_row.'">'.$co_row.'</option>';
		}
		echo '#*#';
		echo '<option />';
		foreach(array_unique($detail_cust_list) as $co_row) {
			echo '<option '.($detail_customer_order == $co_row ? 'selected' : '').' value="'.$co_row.'">'.$co_row.'</option>';
		}
		echo '#*#';
		echo '<option />';
		foreach(array_unique($pallet_list) as $pallet_row) {
			echo '<option '.($pallet == $pallet_row ? 'selected' : '').' value="'.$pallet_row.'">'.$pallet_row.'</option>';
		}
	} else if($_GET['action'] == 'pick_list_add_category') {
		$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
		$po = filter_var($_POST['po'],FILTER_SANITIZE_STRING);
		$po_line = filter_var($_POST['po_line'],FILTER_SANITIZE_STRING);
		$ticket = filter_var($_POST['ticket'],FILTER_SANITIZE_STRING);
		$customer_order = filter_var($_POST['customer_order'],FILTER_SANITIZE_STRING);
		$detail_customer_order = filter_var($_POST['detail_customer_order'],FILTER_SANITIZE_STRING);
		$pallet = filter_var($_POST['pallet'],FILTER_SANITIZE_STRING);
		$list = $dbc->query("SELECT `inventory`.`inventoryid`, `inventory`.`product_name`, `inventory`.`name`, `inventory`.`category`, `inventory`.`pallet`, `inventory`.`quantity` - CAST(`inventory`.`assigned_qty` AS SIGNED INT) `available`, `tickets`.`ticketid`, `tickets`.`ticket_label`, `tickets`.`purchase_order`, `tickets`.`customer_order_num`, `tickets`.`po_line`, `tickets`.`position` FROM `inventory` LEFT JOIN (SELECT `tickets`.`ticketid`, `ticket_label`, `item_id`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `purchase_order`, IFNULL(NULLIF(`ticket_attached`.`position`,''),`tickets`.`customer_order_num`) `customer_order_num`, `ticket_attached`.`po_line`, `ticket_attached`.`position` FROM `ticket_attached` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_attached`.`deleted`=0 AND `tickets`.`deleted`=0 AND `ticket_attached`.`src_table` IN ('inventory','inventory_detailed')) `tickets` ON `inventory`.`inventoryid`=`tickets`.`item_id` WHERE `inventory`.`deleted`=0 AND '$category' IN ('',`inventory`.`category`) AND ('$po'='' OR CONCAT('#*#',`tickets`.`purchase_order`,'#*#') LIKE '%#*#$po#*#%') AND '$po_line' IN ('',`tickets`.`po_line`) AND '$detail_customer_order' IN ('',`tickets`.`position`) AND '$ticket' IN ('',`tickets`.`ticketid`) AND ('$customer_order'='' OR CONCAT('#*#',`tickets`.`customer_order_num`,'#*#') LIKE '%#*#$customer_order#*#%') AND '$pallet' IN ('',`inventory`.`pallet`) AND (IFNULL(`product_name`,'') != '' OR  IFNULL(`name`,'') != '') ORDER BY `inventory`.`category`, `inventory`.`product_name`, `inventory`.`name`");
		$items = [];
		while($item = $list->fetch_assoc()) {
			$items[] = $item['inventoryid'];
		}
		echo json_encode($items);
	}
	else if($_GET['action'] == 'add_update_inventory') {
		    $brand = filter_var(htmlentities($_POST['brand']),FILTER_SANITIZE_STRING);
		    $code = filter_var($_POST['code'],FILTER_SANITIZE_STRING);
		    $description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);
		    $application = filter_var(htmlentities($_POST['application']),FILTER_SANITIZE_STRING);
		    $comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);
		    $question = filter_var(htmlentities($_POST['question']),FILTER_SANITIZE_STRING);
		    $request = filter_var(htmlentities($_POST['request']),FILTER_SANITIZE_STRING);
		    $note = filter_var(htmlentities($_POST['note']),FILTER_SANITIZE_STRING);

		    if($_POST['same_desc'] == 1) {
		        $quote_description = $description;
		    } else {
		        $quote_description = filter_var(htmlentities($_POST['quote_description']),FILTER_SANITIZE_STRING);
		    }

		    $vendorid =	( empty ( $_POST['vendorid'] ) ) ? 0 : $_POST['vendorid'];
		    $display_website = $_POST['display_website'];
		    $featured = $_POST['featured'];
		    $on_sale = $_POST['on_sale'];
		    $on_clearance = $_POST['on_clearance'];
		    $new_item = $_POST['new_item'];

		    if ( $_FILES["upload_main_image"]["name"] ) {
		        $main_image = $_FILES["upload_main_image"]["name"];
		        move_uploaded_file($_FILES["upload_main_image"]["tmp_name"], "download/".$_FILES["upload_main_image"]["name"]) ;
		    }

		    if ( $_FILES["upload_additional_images"]["name"] ) {
		        $additional_images = implode('*#*',$_FILES["upload_additional_images"]["name"]);
		        for($i = 0; $i < count($_FILES['upload_additional_images']['name']); $i++) {
		            move_uploaded_file($_FILES["upload_additional_images"]["tmp_name"][$i], "download/".$_FILES["upload_additional_images"]["name"][$i]) ;
		        }
		    }

		    if ( $_FILES["spec_sheet"]["name"] ) {
		        $spec_sheet = $_FILES["spec_sheet"]["name"];
		        move_uploaded_file($_FILES["spec_sheet"]["tmp_name"], "download/".$_FILES["spec_sheet"]["name"]) ;
		    }

		    if($_POST['size'] == 'Other') {
		        $size = filter_var($_POST['size_name'],FILTER_SANITIZE_STRING);
		    } else {
		        $size = filter_var($_POST['size'],FILTER_SANITIZE_STRING);
		    }

		    /* OLD weight dropdown
		    if($_POST['weight'] == 'Other') {
		        $weight = filter_var($_POST['weight_name'],FILTER_SANITIZE_STRING);
		    } else {
		        $weight = filter_var($_POST['weight'],FILTER_SANITIZE_STRING);
		    }*/

		    $weight   = filter_var($_POST['weight'],FILTER_SANITIZE_STRING);
		    $gauge    =	filter_var($_POST['gauge'],FILTER_SANITIZE_STRING);
		    $length   =	filter_var($_POST['length'],FILTER_SANITIZE_STRING);
		    $pressure =	filter_var($_POST['pressure'],FILTER_SANITIZE_STRING);

		    $type =	filter_var($_POST['type'],FILTER_SANITIZE_STRING);
		    $date_of_purchase =	filter_var($_POST['date_of_purchase'],FILTER_SANITIZE_STRING);
			if ( empty ($date_of_purchase) ) { $date_of_purchase = '0000-00-00'; }
		    $purchase_cost =	filter_var($_POST['purchase_cost'],FILTER_SANITIZE_STRING);
			if ( empty ($purchase_cost) ) { $purchase_cost = 0.00; }
		    $sell_price =	filter_var($_POST['sell_price'],FILTER_SANITIZE_STRING);
			if ( empty ($sell_price) ) { $sell_price = 0.00; }
		    $markup =	filter_var($_POST['markup'],FILTER_SANITIZE_STRING);
		    $freight_charge =	filter_var($_POST['freight_charge'],FILTER_SANITIZE_STRING);
			if ( empty ($freight_charge) ) { $freight_charge = 0.00; }
		    $min_bin =	filter_var($_POST['min_bin'],FILTER_SANITIZE_STRING);
			if ( empty ($min_bin) ) { $min_bin = 0.00; }
		    $current_stock =	filter_var($_POST['current_stock'],FILTER_SANITIZE_STRING);
			if ( empty ($current_stock) ) { $current_stock = 0; }

			$stocking_units = filter_var($_POST['stocking_units'],FILTER_SANITIZE_STRING);
			$selling_units  = filter_var($_POST['selling_units'],FILTER_SANITIZE_STRING);
			$buying_units = filter_var($_POST['buying_units'],FILTER_SANITIZE_STRING);
			$warehouse = filter_var($_POST['warehouse'],FILTER_SANITIZE_STRING);
			$location = filter_var($_POST['location'],FILTER_SANITIZE_STRING);
			$asset = filter_var($_POST['asset'],FILTER_SANITIZE_STRING);
			$revenue = filter_var($_POST['revenue'],FILTER_SANITIZE_STRING);
			$inv_variance = filter_var($_POST['inv_variance'],FILTER_SANITIZE_STRING);
		    $web_price = filter_var($_POST['web_price'],FILTER_SANITIZE_STRING);
			if ( empty ($web_price) ) { $web_price = 0.00; }
		    $clearance_price = filter_var($_POST['clearance_price'],FILTER_SANITIZE_STRING);
			if ( empty ($clearance_price) ) { $clearance_price = 0.00; }
			$average_cost = filter_var($_POST['average_cost'],FILTER_SANITIZE_STRING);
			$preferred_price = filter_var($_POST['preferred_price'],FILTER_SANITIZE_STRING);
			if ( empty ($preferred_price) ) { $preferred_price = 0.00; }

		    $id_number = filter_var($_POST['id_number'],FILTER_SANITIZE_STRING);
		    $operator = filter_var($_POST['operator'],FILTER_SANITIZE_STRING);
		    $lsd = filter_var($_POST['lsd'],FILTER_SANITIZE_STRING);

		    $distributor_price = filter_var($_POST['distributor_price'],FILTER_SANITIZE_STRING);
			if ( empty ($distributor_price) ) { $distributor_price = 0.00; }
		    $final_retail_price = filter_var($_POST['final_retail_price'],FILTER_SANITIZE_STRING);
			if ( empty ($final_retail_price) ) { $final_retail_price = 0.00; }
		    $admin_price = filter_var($_POST['admin_price'],FILTER_SANITIZE_STRING);
			if ( empty ($admin_price) ) { $admin_price = 0.00; }
		    $wholesale_price = filter_var($_POST['wholesale_price'],FILTER_SANITIZE_STRING);
			if ( empty ($wholesale_price) ) { $wholesale_price = 0.00; }
		    $commercial_price = filter_var($_POST['commercial_price'],FILTER_SANITIZE_STRING);
			if ( empty ($commercial_price) ) { $commercial_price = 0.00; }
		    $client_price = filter_var($_POST['client_price'],FILTER_SANITIZE_STRING);
			if ( empty ($client_price) ) { $client_price = 0.00; }
			$purchase_order_price = filter_var($_POST['purchase_order_price'],FILTER_SANITIZE_STRING);
			if ( empty ($purchase_order_price) ) { $purchase_order_price = 0.00; }
			$sales_order_price = filter_var($_POST['sales_order_price'],FILTER_SANITIZE_STRING);
			if ( empty ($sales_order_price) ) { $sales_order_price = 0.00; }
		    $minimum_billable = filter_var($_POST['minimum_billable'],FILTER_SANITIZE_STRING);
		    $estimated_hours = filter_var($_POST['estimated_hours'],FILTER_SANITIZE_STRING);
		    $actual_hours = filter_var($_POST['actual_hours'],FILTER_SANITIZE_STRING);
		    $msrp = filter_var($_POST['msrp'],FILTER_SANITIZE_STRING);
			if ( empty ($msrp) ) { $msrp = 0.00; }

		    $product_name = filter_var($_POST['product_name'],FILTER_SANITIZE_STRING);
		    $cost = filter_var($_POST['cost'],FILTER_SANITIZE_STRING);
		    $usd_cpu = filter_var($_POST['usd_cpu'],FILTER_SANITIZE_STRING);
			if ( empty ($usd_cpu) ) { $usd_cpu = 0.00; }
		    $commission_price = filter_var($_POST['commission_price'],FILTER_SANITIZE_STRING);
		    $markup_perc = filter_var($_POST['markup_perc'],FILTER_SANITIZE_STRING);
		    $current_inventory = filter_var($_POST['current_inventory'],FILTER_SANITIZE_STRING);
			if ( empty ($current_inventory) ) { $current_inventory = 0; }
		    $write_offs = filter_var($_POST['write_offs'],FILTER_SANITIZE_STRING);
		    $min_max = filter_var($_POST['min_max'],FILTER_SANITIZE_STRING);
		    $status = filter_var($_POST['status'],FILTER_SANITIZE_STRING);
		    $drum_unit_cost = filter_var($_POST['drum_unit_cost'],FILTER_SANITIZE_STRING);
			if ( empty ($drum_unit_cost) ) { $drum_unit_cost = 0.00; }
		    $drum_unit_price = filter_var($_POST['drum_unit_price'],FILTER_SANITIZE_STRING);
			if ( empty ($drum_unit_price) ) { $drum_unit_price = 0.00; }
		    $tote_unit_cost = filter_var($_POST['tote_unit_cost'],FILTER_SANITIZE_STRING);
			if ( empty ($tote_unit_cost) ) { $tote_unit_cost = 0.00; }
		    $tote_unit_price = filter_var($_POST['tote_unit_price'],FILTER_SANITIZE_STRING);
			if ( empty ($tote_unit_price) ) { $tote_unit_price = 0.00; }
		    $wcb_price = filter_var($_POST['wcb_price'],FILTER_SANITIZE_STRING);
			if ( empty ($wcb_price) ) { $wcb_price = 0.00; }
		    $unit_price = filter_var($_POST['unit_price'],FILTER_SANITIZE_STRING);
			if ( empty ($unit_price) ) { $unit_price = 0.00; }
		    $unit_cost = filter_var($_POST['unit_cost'],FILTER_SANITIZE_STRING);
			if ( empty ($unit_cost) ) { $unit_cost = 0.00; }
		    $rent_price = filter_var($_POST['rent_price'],FILTER_SANITIZE_STRING);
			if ( empty ($rent_price) ) { $rent_price = 0.00; }
		    $rental_days = filter_var($_POST['rental_days'],FILTER_SANITIZE_STRING);
		    $rental_weeks = filter_var($_POST['rental_weeks'],FILTER_SANITIZE_STRING);
		    $rental_months = filter_var($_POST['rental_months'],FILTER_SANITIZE_STRING);
		    $rental_years = filter_var($_POST['rental_years'],FILTER_SANITIZE_STRING);
		    $reminder_alert = filter_var($_POST['reminder_alert'],FILTER_SANITIZE_STRING);
		    $daily = filter_var($_POST['daily'],FILTER_SANITIZE_STRING);
		    $weekly = filter_var($_POST['weekly'],FILTER_SANITIZE_STRING);
		    $monthly = filter_var($_POST['monthly'],FILTER_SANITIZE_STRING);
		    $annually = filter_var($_POST['annually'],FILTER_SANITIZE_STRING);
		    $total_days = filter_var($_POST['total_days'],FILTER_SANITIZE_STRING);
		    $total_hours = filter_var($_POST['total_hours'],FILTER_SANITIZE_STRING);
		    $total_km = filter_var($_POST['total_km'],FILTER_SANITIZE_STRING);
		    $total_miles = filter_var($_POST['total_miles'],FILTER_SANITIZE_STRING);
			$include_in_pos = filter_var($_POST['include_in_pos'],FILTER_SANITIZE_STRING);
		    $include_in_product = filter_var($_POST['include_in_product'],FILTER_SANITIZE_STRING);
			$include_in_po = filter_var($_POST['include_in_po'],FILTER_SANITIZE_STRING);
			$include_in_so = filter_var($_POST['include_in_so'],FILTER_SANITIZE_STRING);

		    $item_sku = filter_var($_POST['item_sku'],FILTER_SANITIZE_STRING);
		    $color = filter_var($_POST['color'],FILTER_SANITIZE_STRING);
		    $suggested_retail_price = filter_var($_POST['suggested_retail_price'],FILTER_SANITIZE_STRING);
		    $min_amount = filter_var($_POST['min_amount'],FILTER_SANITIZE_STRING);
		    $max_amount = filter_var($_POST['max_amount'],FILTER_SANITIZE_STRING);
		    $rush_price = filter_var($_POST['rush_price'],FILTER_SANITIZE_STRING);
		    $distributor_price = filter_var($_POST['distributor_price'],FILTER_SANITIZE_STRING);

		    if($_POST['category'] == 'Other') {
		        $category = filter_var($_POST['category_name'],FILTER_SANITIZE_STRING);
		    } else {
		        //$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
		        $category = filter_var($_POST['actual_category_name'],FILTER_SANITIZE_STRING);
		    }

		    if($_POST['sub_category'] == 'Other') {
		        $sub_category = filter_var($_POST['sub_category_name'],FILTER_SANITIZE_STRING);
		    } else {
		        $sub_category = filter_var($_POST['sub_category'],FILTER_SANITIZE_STRING);
		    }

		    $part_no = filter_var($_POST['part_no'],FILTER_SANITIZE_STRING);
		    $part_no_old = '';
		    $gst_exempt = filter_var($_POST['gst_exempt'],FILTER_SANITIZE_STRING);
		    $gtin = filter_var($_POST['gtin'],FILTER_SANITIZE_STRING);

		    $name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
		    $name_on_website = filter_var($_POST['name_on_website'],FILTER_SANITIZE_STRING);

		    $usd_invoice = filter_var($_POST['usd_invoice'],FILTER_SANITIZE_STRING);
		    $shipping_rate =	filter_var($_POST['shipping_rate'],FILTER_SANITIZE_STRING);
			if ( empty ($shipping_rate) ) { $shipping_rate = 0.00; }
		    $shipping_cash =	filter_var($_POST['shipping_cash'],FILTER_SANITIZE_STRING);
			if ( empty ($shipping_cash) ) { $shipping_cash = 0.00; }
		    $exchange_rate =	filter_var($_POST['exchange_rate'],FILTER_SANITIZE_STRING);
			if ( empty ($exchange_rate) ) { $exchange_rate = 0.00; }
		    $exchange_cash =	filter_var($_POST['exchange_cash'],FILTER_SANITIZE_STRING);
			if ( empty ($exchange_cash) ) { $exchange_cash = 0.00; }
		    $pallet =	filter_var($_POST['pallet'],FILTER_SANITIZE_STRING);
		    $cdn_cpu =	filter_var($_POST['cdn_cpu'],FILTER_SANITIZE_STRING);
			if ( empty ($cdn_cpu) ) { $cdn_cpu = 0.00; }
		    $cogs_total =	filter_var($_POST['cogs_total'],FILTER_SANITIZE_STRING);

			$quantity = filter_var($_POST['quantity'],FILTER_SANITIZE_STRING);
			$bill_of_material = filter_var($_POST['bill_of_material_hidden'],FILTER_SANITIZE_STRING);
					if($bill_of_material == '0') {
						$bill_of_material = filter_var(implode(',',$_POST['bill_of_material']),FILTER_SANITIZE_STRING);
					}
			if(!empty($_POST['inventoryid'])) {

				$resultw = mysqli_query($dbc, "SELECT * FROM inventory WHERE inventoryid= '".$_POST['inventoryid']."'");
				while($rww = mysqli_fetch_assoc($resultw)) {

					// Put in bill of material history log, if the user changed the bill of material of this inventory item. //
					if($rww['bill_of_material'] !== $bill_of_material) {
						$dater = date('Y/m/d h:i:s a', time());
						$contactid = $_SESSION['contactid'];
						$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid'");
						while($row = mysqli_fetch_assoc($result)) {
							$contact_name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' (ID: '.$row['contactid'].')';
						}
						$query_insert_inventory = "INSERT INTO `bill_of_material_log` (`pieces_of_inventoryid`, `old_pieces_of_inventoryid`, `inventoryid`, `date_time`, `contact`, `type`, `deleted`
		    ) VALUES ('$bill_of_material', '".$rww['bill_of_material']."', '".$_POST['inventoryid']."', '$dater', '$contact_name', 'Edit', '0')";
						mysqli_query($dbc, $query_insert_inventory) or die(mysqli_error($dbc));
					}

					// Put in inventory quantity change log, if the user changed the quantity of this inventory item.
					$chng_qty = $quantity - $rww['quantity'];
					$old_inventory = $rww['quantity'];
					if($old_inventory == '' || $old_inventory == NULL) {
						$old_inventory = 0;
					}
					$new_inv = $quantity;
					$contactidd = $_SESSION['contactid'];
					$datetime = date('Y/m/d h:i:s a', time());
					$inv = $_POST['inventoryid'];
					$cur_inv = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `inventory` WHERE `inventoryid`='$inv'"));
					$old_cost = ($cur_inv['purchase_cost'] > 0 ? $cur_inv['purchase_cost'] : ($cur_inv['unit_cost'] > 0 ? $cur_inv['unit_cost'] : ($cur_inv['cost'] > 0 ? $cur_inv['cost'] : $cur_inv['average_cost'])));
					$current_cost = ($purchase_cost > 0 ? $purchase_cost : ($unit_cost > 0 ? $unit_cost : ($cost > 0 ? $cost : $average_cost)));
					$new_cost = ($chng_qty > 0 ? (($old_cost * $old_inventory) + ($current_cost * $chng_qty)) / $new_inv : $current_cost);
					$average_cost = ($average_cost > 0 && $average_cost != $cur_inv['average_cost'] ? $average_cost : $new_cost);
					$query_add_log = "INSERT INTO `inventory_change_log` (`inventoryid`, `contactid`, `location_of_change`, `old_inventory`, `old_cost`, `changed_quantity`, `current_cost`, `new_inventory`, `new_cost`, `date_time`, `deleted`) VALUES ('$inv', '$contactidd', 'Inventory Tile', '$old_inventory', '$old_cost', '$chng_qty', '$current_cost', '$new_inv', '$new_cost', '$datetime', '0' )";

		            mysqli_query($dbc, $query_add_log) or die(mysqli_error($dbc));

		            $part_no_old = $rww['part_no'];
				}
			}

		    $supplimentary = filter_var(implode(',',$_POST['supplimentary']),FILTER_SANITIZE_STRING);

		    if(empty($_POST['inventoryid'])) {
		        // New Inventory Item
		        $query_insert_inventory = "INSERT INTO `inventory` (`code`, `gtin`, `brand`, `category`, `sub_category`, `part_no`, `gst_exempt`, `description`, `application`, `supplimentary`, `comment`, `question`, `request`, `display_website`, `vendorid`, `size`, `gauge`, `weight`, `length`, `pressure`, `type`, `name`, `name_on_website`, `date_of_purchase`, `purchase_cost`, `sell_price`, `markup`, `freight_charge`, `min_bin`, `current_stock`, `final_retail_price`, `admin_price`, `wholesale_price`, `commercial_price`, `client_price`, `purchase_order_price`, `sales_order_price`, `distributor_price`, `minimum_billable`, `estimated_hours`, `actual_hours`, `msrp`, `quote_description`, `usd_invoice`, `shipping_rate`, `shipping_cash`, `exchange_rate`, `exchange_cash`, `pallet`, `cdn_cpu`, `cogs_total`, `warehouse`, `location`, `inv_variance`, `average_cost`, `asset`, `revenue`, `buying_units`, `selling_units`, `stocking_units`, `preferred_price`, `web_price`, `clearance_price`, `id_number`, `operator`, `lsd`, `quantity`, `product_name`, `cost`, `usd_cpu`, `commission_price`, `markup_perc`, `current_inventory`, `write_offs`, `min_max`, `status`, `note`, `unit_price`, `unit_cost`, `rent_price`, `rental_days`, `rental_weeks`, `rental_months`, `rental_years`, `reminder_alert`, `daily`,`weekly`, `monthly`, `annually`,  `total_days`, `total_hours`, `total_km`, `total_miles`, `bill_of_material`, `include_in_so`,`include_in_po`,`include_in_pos`, `drum_unit_cost`, `drum_unit_price`, `tote_unit_cost`, `tote_unit_price`, `include_in_product`, `wcb_price`, `spec_sheet`, `featured`, `sale`, `clearance`, `new`, `main_image`,`item_sku`,`color`,`suggested_retail_price`, `rush_price`, `min_amount`, `max_amount`)
					VALUES ('$code', '$gtin', '$brand', '$category', '$sub_category', '$part_no', '$gst_exempt', '$description', '$application', '$supplimentary', '$comment', '$question', '$request', '$display_website', '$vendorid', '$size', '$gauge', '$weight', '$length', '$pressure', '$type', '$name', '$name_on_website', '$date_of_purchase', '$purchase_cost', '$sell_price', '$markup', '$freight_charge', '$min_bin', '$current_stock', '$final_retail_price', '$admin_price', '$wholesale_price', '$commercial_price', '$client_price', '$purchase_order_price', '$sales_order_price', '$distributor_price', '$minimum_billable', '$estimated_hours', '$actual_hours', '$msrp', '$quote_description', '$usd_invoice', '$shipping_rate', '$shipping_cash', '$exchange_rate', '$exchange_cash', '$pallet', '$cdn_cpu', '$cogs_total', '$warehouse', '$location', '$inv_variance', '$average_cost', '$asset', '$revenue', '$buying_units', '$selling_units', '$stocking_units', '$preferred_price', '$web_price', '$clearance_price', '$id_number', '$operator', '$lsd', '$quantity', '$product_name', '$cost', '$usd_cpu', '$commission_price', '$markup_perc', '$current_inventory', '$write_offs', '$min_max', '$status', '$note', '$unit_price', '$unit_cost', '$rent_price', '$rental_days', '$rental_weeks', '$rental_months', '$rental_years', '$reminder_alert', '$daily', '$weekly', '$monthly', '$annually', '$total_days', '$total_hours', '$total_km', '$total_miles', '$bill_of_material', '$include_in_so', '$include_in_po', '$include_in_pos', '$drum_unit_cost', '$drum_unit_price', '$tote_unit_cost', '$tote_unit_price', '$include_in_product', '$wcb_price', '$spec_sheet', '$featured', '$on_sale', '$on_clearance', '$new_item', '$main_image', '$item_sku', '$color', '$suggested_retail_price', '$rush_price', '$min_amount', '$max_amount')";
		        $result_insert_inventory = mysqli_query($dbc, $query_insert_inventory);
		        $inventoryid = mysqli_insert_id($dbc);

		        $before_change = '';
		        $history = "New inventory Added. <br />";
				    add_update_history($dbc, 'inventory_history', $history, '', $before_change);

		        // Insert the same record to led.rookconnect.com
		        if ( $led ) {
		            /* Change prices before inserting to LED
		             * Final Retail Price = SEA Alberta Web Price
		             * Preferred Price = CDN Cost + 50%
		             * Distributor Price = CDN Cost + 32%
		             */
		            $final_retail_price_led = $web_price;
		            $preferred_price_led = $cdn_cpu + ($cdn_cpu * 0.5);
		            $distributor_price_led = $cdn_cpu + ($cdn_cpu * 0.32);
		            $query_insert_inventory_led = "INSERT INTO `inventory` (`code`, `gtin`, `brand`, `category`, `sub_category`, `part_no`, `gst_exempt`, `description`, `application`, `supplimentary`, `comment`, `question`, `request`, `display_website`, `vendorid`, `size`, `gauge`, `weight`, `length`, `pressure`, `type`, `name`, `name_on_website`, `date_of_purchase`, `purchase_cost`, `sell_price`, `markup`, `freight_charge`, `min_bin`, `current_stock`, `final_retail_price`, `admin_price`, `wholesale_price`, `commercial_price`, `client_price`, `purchase_order_price`, `sales_order_price`, `distributor_price`, `minimum_billable`, `estimated_hours`, `actual_hours`, `msrp`, `quote_description`, `usd_invoice`, `shipping_rate`, `shipping_cash`, `exchange_rate`, `exchange_cash`, `pallet`, `cdn_cpu`, `cogs_total`, `warehouse`, `location`, `inv_variance`, `average_cost`, `asset`, `revenue`, `buying_units`, `selling_units`, `stocking_units`, `preferred_price`, `web_price`, `clearance_price`, `id_number`, `operator`, `lsd`, `quantity`, `product_name`, `cost`, `usd_cpu`, `commission_price`, `markup_perc`, `current_inventory`, `write_offs`, `min_max`, `status`, `note`, `unit_price`, `unit_cost`, `rent_price`, `rental_days`, `rental_weeks`, `rental_months`, `rental_years`, `reminder_alert`, `daily`,`weekly`, `monthly`, `annually`,  `total_days`, `total_hours`, `total_km`, `total_miles`, `bill_of_material`, `include_in_so`,`include_in_po`,`include_in_pos`, `drum_unit_cost`, `drum_unit_price`, `tote_unit_cost`, `tote_unit_price`, `include_in_product`, `wcb_price`, `spec_sheet`, `featured`, `sale`, `clearance`, `new`, `main_image`,`item_sku`,`color`,`suggested_retail_price`, `rush_price`, `min_amount`, `max_amount`)
		                VALUES ('$code', '$gtin', '$brand', '$category', '$sub_category', '$part_no', '$gst_exempt', '$description', '$application', '$supplimentary', '$comment', '$question', '$request', '$display_website', '$vendorid', '$size', '$gauge', '$weight', '$length', '$pressure', '$type', '$name', '$name_on_website', '$date_of_purchase', '$purchase_cost', '$sell_price', '$markup', '$freight_charge', '$min_bin', '$current_stock', '$final_retail_price_led', '$admin_price', '$wholesale_price', '$commercial_price', '$client_price', '$purchase_order_price', '$sales_order_price', '$distributor_price_led', '$minimum_billable', '$estimated_hours', '$actual_hours', '$msrp', '$quote_description', '$usd_invoice', '$shipping_rate', '$shipping_cash', '$exchange_rate', '$exchange_cash', '$pallet', '$cdn_cpu', '$cogs_total', '$warehouse', '$location', '$inv_variance', '$average_cost', '$asset', '$revenue', '$buying_units', '$selling_units', '$stocking_units', '$preferred_price_led', '$web_price', '$clearance_price', '$id_number', '$operator', '$lsd', '$quantity', '$product_name', '$cost', '$usd_cpu', '$commission_price', '$markup_perc', '$current_inventory', '$write_offs', '$min_max', '$status', '$note', '$unit_price', '$unit_cost', '$rent_price', '$rental_days', '$rental_weeks', '$rental_months', '$rental_years', '$reminder_alert', '$daily', '$weekly', '$monthly', '$annually', '$total_days', '$total_hours', '$total_km', '$total_miles', '$bill_of_material', '$include_in_so', '$include_in_po', '$include_in_pos', '$drum_unit_cost', '$drum_unit_price', '$tote_unit_cost', '$tote_unit_price', '$include_in_product', '$wcb_price', '$spec_sheet', '$featured', '$on_sale', '$on_clearance', '$new_item', '$main_image', '$item_sku', '$color', '$suggested_retail_price', '$rush_price', '$min_amount', '$max_amount')";
		            $result_insert_inventory_led = mysqli_query($dbc_led, $query_insert_inventory_led);
		            $before_change = '';
		            $history = "New inventory Added. <br />";
		    		    add_update_history($dbc, 'inventory_history', $history, '', $before_change);
		        } else {
		            //Else we sync the Inventory with the configured db on database_connection.php More details in Admin Settings > Sync Inventory
		            if ( $dbc_inventory ) {
		                mysqli_query($dbc_inventory, $query_insert_inventory);
		            }
		        }

		        if ( !empty($additional_images) ) {
		            for($i = 0; $i < count($_FILES['upload_additional_images']['name']); $i++) {
		                $additional_image = $_FILES["upload_additional_images"]["name"][$i];
		                $query_insert_images = "INSERT INTO `inventory_images` (`inventoryid`, `image`) VALUES ('$inventoryid', '$additional_image')";
		                $result_insert_images = mysqli_query($dbc, $query_insert_images);
		            }

		            $before_change = '';
		            $history = "New inventory images Added. <br />";
		    		    add_update_history($dbc, 'inventory_history', $history, '', $before_change);
		        }

		        $url = 'Added';

		    } else {
		        // Update Inventory Item
		        $inventoryid = $_POST['inventoryid'];
		        $update_spec_sheet = ( empty($spec_sheet) ) ? "" : " `spec_sheet`='$spec_sheet',";
		        $update_main_image = ( empty($main_image) ) ? "" : ", `main_image`='$main_image'";
		        $query_update_inventory = "UPDATE `inventory` SET `code`='$code', `gtin`='$gtin', `brand`='$brand', `category`='$category', `sub_category`='$sub_category', `part_no`='$part_no', `gst_exempt`='$gst_exempt', `description`='$description', `application`='$application', `supplimentary`='$supplimentary', `comment`='$comment', `question`='$question', `request`='$request', `display_website`='$display_website', `vendorid`='$vendorid', `size`='$size', `gauge`='$gauge', `weight`='$weight', `length`='$length', `pressure`='$pressure', `type`='$type', `name`='$name', `name_on_website`='$name_on_website', `date_of_purchase`='$date_of_purchase', `purchase_cost`='$purchase_cost', `sell_price`='$sell_price', `markup`='$markup', `freight_charge`='$freight_charge', `min_bin`='$min_bin', `current_stock`='$current_stock', `final_retail_price`='$final_retail_price', `admin_price`='$admin_price', `wholesale_price`='$wholesale_price', `commercial_price`='$commercial_price', `client_price`='$client_price', `purchase_order_price`='$purchase_order_price', `sales_order_price`='$sales_order_price', `distributor_price`='$distributor_price', `minimum_billable`='$minimum_billable', `estimated_hours`='$estimated_hours', `actual_hours`='$actual_hours', `msrp`='$msrp', `quote_description`='$quote_description', `usd_invoice`='$usd_invoice', `shipping_rate`='$shipping_rate', `shipping_cash`='$shipping_cash', `exchange_rate`='$exchange_rate', `exchange_cash`='$exchange_cash', `pallet`='$pallet', `cdn_cpu`='$cdn_cpu', `cogs_total`='$cogs_total', `warehouse`='$warehouse', `location`='$location', `inv_variance`='$inv_variance', `average_cost`='$average_cost', `asset`='$asset', `revenue`='$revenue', `buying_units`='$buying_units', `selling_units`='$selling_units', `stocking_units`='$stocking_units', `preferred_price`='$preferred_price', `web_price`='$web_price', `clearance_price`='$clearance_price', `id_number`='$id_number', `operator`='$operator', `lsd`='$lsd', `quantity`='$quantity', `product_name`='$product_name', `cost`='$cost', `usd_cpu`='$usd_cpu', `commission_price`='$commission_price', `markup_perc`='$markup_perc', `current_inventory`='$current_inventory', `write_offs`='$write_offs', `min_max`='$min_max', `status`='$status', `note`='$note', `unit_price`='$unit_price', `unit_cost`='$unit_cost', `rent_price`='$rent_price', `rental_days`='$rental_days', `rental_weeks`='$rental_weeks', `rental_months`='$rental_months', `rental_years`='$rental_years', `reminder_alert`='$reminder_alert', `daily`='$daily', `weekly`='$weekly', `monthly`='$monthly', `annually`='$annually', `total_days`='$total_days', `total_hours`='$total_hours', `total_km`='$total_km', `total_miles`='$total_miles', `bill_of_material`='$bill_of_material', `include_in_so`='$include_in_so', `include_in_po`='$include_in_po', `include_in_pos`='$include_in_pos', `drum_unit_cost`= '$drum_unit_cost', `drum_unit_price`='$drum_unit_price', `tote_unit_cost`='$tote_unit_cost', `tote_unit_price`='$tote_unit_price', `include_in_product`='$include_in_product', `wcb_price`='$wcb_price',". $update_spec_sheet ." `featured`='$featured', `sale`='$on_sale', `clearance`='$on_clearance', `new`='$new_item'". $update_main_image .", `item_sku` = '$item_sku', `color` = '$color', `suggested_retail_price` = '$suggested_retail_price', `rush_price` = '$rush_price', `min_amount` = '$min_amount', `max_amount` = '$max_amount' WHERE `inventoryid`='$inventoryid'";
		        $result_update_inventory	= mysqli_query($dbc, $query_update_inventory);
		        $before_change = '';
		        $history = "Inventory with id $inventoryid is been Updated. <br />";
		        add_update_history($dbc, 'inventory_history', $history, '', $before_change);

		        if ( $led ) {
		            /* Update the same record on led.rookconnect.com
		             * Change prices before inserting to LED
		             * Final Retail Price = SEA Alberta Web Price
		             * Preferred Price = CDN Cost + 50%
		             * Distributor Price = CDN Cost + 32%
		             */
		            if ( !empty($part_no_old) ) {
		                $final_retail_price_led = $web_price;
		                $preferred_price_led = $cdn_cpu + ($cdn_cpu * 0.5);
		                $distributor_price_led = $cdn_cpu + ($cdn_cpu * 0.32);
		                $query_update_inventory_led = "UPDATE `inventory` SET `code`='$code', `gtin`='$gtin', `brand`='$brand', `category`='$category', `sub_category`='$sub_category', `part_no`='$part_no', `gst_exempt`='$gst_exempt', `description`='$description', `application`='$application', `supplimentary`='$supplimentary', `comment`='$comment', `question`='$question', `request`='$request', `display_website`='$display_website', `vendorid`='$vendorid', `size`='$size', `gauge`='$gauge', `weight`='$weight', `length`='$length', `pressure`='$pressure', `type`='$type', `name`='$name', `name_on_website`='$name_on_website', `date_of_purchase`='$date_of_purchase', `purchase_cost`='$purchase_cost', `sell_price`='$sell_price', `markup`='$markup', `freight_charge`='$freight_charge', `min_bin`='$min_bin', `current_stock`='$current_stock', `final_retail_price`='$final_retail_price_led', `admin_price`='$admin_price', `wholesale_price`='$wholesale_price', `commercial_price`='$commercial_price', `client_price`='$client_price', `purchase_order_price`='$purchase_order_price', `sales_order_price`='$sales_order_price', `distributor_price`='$distributor_price_led', `minimum_billable`='$minimum_billable', `estimated_hours`='$estimated_hours', `actual_hours`='$actual_hours', `msrp`='$msrp', `quote_description`='$quote_description', `usd_invoice`='$usd_invoice', `shipping_rate`='$shipping_rate', `shipping_cash`='$shipping_cash', `exchange_rate`='$exchange_rate', `exchange_cash`='$exchange_cash', `pallet`='$pallet', `cdn_cpu`='$cdn_cpu', `cogs_total`='$cogs_total', `warehouse`='$warehouse', `location`='$location', `inv_variance`='$inv_variance', `average_cost`='$average_cost', `asset`='$asset', `revenue`='$revenue', `buying_units`='$buying_units', `selling_units`='$selling_units', `stocking_units`='$stocking_units', `preferred_price`='$preferred_price_led', `web_price`='$web_price', `clearance_price`='$clearance_price', `id_number`='$id_number', `operator`='$operator', `lsd`='$lsd', `quantity`='$quantity', `product_name`='$product_name', `cost`='$cost', `usd_cpu`='$usd_cpu', `commission_price`='$commission_price', `markup_perc`='$markup_perc', `current_inventory`='$current_inventory', `write_offs`='$write_offs', `min_max`='$min_max', `status`='$status', `note`='$note', `unit_price`='$unit_price', `unit_cost`='$unit_cost', `rent_price`='$rent_price', `rental_days`='$rental_days', `rental_weeks`='$rental_weeks', `rental_months`='$rental_months', `rental_years`='$rental_years', `reminder_alert`='$reminder_alert', `daily`='$daily', `weekly`='$weekly', `monthly`='$monthly', `annually`='$annually', `total_days`='$total_days', `total_hours`='$total_hours', `total_km`='$total_km', `total_miles`='$total_miles', `bill_of_material`='$bill_of_material', `include_in_so`='$include_in_so', `include_in_po`='$include_in_po', `include_in_pos`='$include_in_pos', `drum_unit_cost`= '$drum_unit_cost', `drum_unit_price`='$drum_unit_price', `tote_unit_cost`='$tote_unit_cost', `tote_unit_price`='$tote_unit_price', `include_in_product`='$include_in_product', `wcb_price`='$wcb_price',". $update_spec_sheet ." `featured`='$featured', `sale`='$on_sale', `clearance`='on_clearance', `new`='$new_item'". $update_main_image .", `item_sku` = '$item_sku', `color` = '$color', `suggested_retail_price` = '$suggested_retail_price', `rush_price` = '$rush_price', `min_amount` = '$min_amount', `max_amount` = '$max_amount' WHERE `part_no`='$part_no_old'";
		                $result_update_inventory_led = mysqli_query($dbc_led, $query_update_inventory_led);
		            }
		        } else {
		            //Else we sync the Inventory with the configured DB on database_connection.php More details in Admin Settings > Sync Inventory
		            if ( $dbc_inventory ) {
		                mysqli_query($dbc_inventory, $query_update_inventory);
		            }
		        }

		            $before_change = '';
		            $history = "Inventory with id $inventoryid has been Updated. <br />";
		            add_update_history($dbc, 'inventory_history', $history, '', $before_change);

		        //}

		        // Update all SEA Software `code` & `part_no` - both the same. We do this only if the `part_no` is updated on SEA Alberta.
		        if ( $sea_partno_edit==true && !empty($part_no_old) ) {
		            $query_update_inventory = "UPDATE `inventory` SET `code`='$part_no', `gtin`='$gtin', `part_no`='$part_no' WHERE `part_no`='$part_no_old'";

		            // Connect to each SEA Software as Cross Software doesn't work from SEA Alberta
		            $dbc_global     = mysqli_connect('localhost', 'sea_software_use', 'dRagonflY!306', 'sea_devsoftware_db');
		            $dbc_regina     = mysqli_connect('localhost', 'sea_software_use', 'dRagonflY!306', 'sea_regina_db');
		            $dbc_saskatoon  = mysqli_connect('localhost', 'sea_software_use', 'dRagonflY!306', 'sea_saskatoon_db');
		            $dbc_vancouver  = mysqli_connect('localhost', 'sea_software_use', 'dRagonflY!306', 'sea_vancouver_db');

		            $result_update_inventory = mysqli_query ( $dbc, $query_update_inventory );
		            $result_update_inventory = mysqli_query ( $dbc_global, $query_update_inventory );
		            $result_update_inventory = mysqli_query ( $dbc_regina, $query_update_inventory );
		            $result_update_inventory = mysqli_query ( $dbc_saskatoon, $query_update_inventory );
		            $result_update_inventory = mysqli_query ( $dbc_vancouver, $query_update_inventory );

		            mysqli_close($dbc_global);
		            mysqli_close($dbc_regina);
		            mysqli_close($dbc_saskatoon);
		            mysqli_close($dbc_vancouver);
		        }

		        if ( !empty($additional_images) ) {
		            for($i = 0; $i < count($_FILES['upload_additional_images']['name']); $i++) {
		                $additional_image = $_FILES["upload_additional_images"]["name"][$i];
		                $query_insert_images = "INSERT INTO `inventory_images` (`inventoryid`, `image`) VALUES ('$inventoryid', '$additional_image')";
		                $result_insert_images = mysqli_query($dbc, $query_insert_images);
		            }
		        }

		        $url = 'Updated';
		    }

		    if($include_in_product == 1) {
		        $query_insert_invoice = "INSERT INTO `products` (`inventoryid`, `code`, `brand`, `category`, `sub_category`, `part_no`, `gst_exempt`, `description`, `application`, `supplimentary`, `comment`, `question`, `request`, `display_website`, `vendorid`, `size`, `gauge`, `weight`, `length`, `pressure`, `type`, `name`, `date_of_purchase`, `purchase_cost`, `sell_price`, `markup`, `freight_charge`, `min_bin`, `current_stock`, `final_retail_price`, `admin_price`, `wholesale_price`, `commercial_price`, `client_price`, `purchase_order_price`, `sales_order_price`, `distributor_price`, `minimum_billable`, `estimated_hours`, `actual_hours`, `msrp`, `quote_description`, `usd_invoice`, `shipping_rate`, `shipping_cash`, `exchange_rate`, `exchange_cash`, `cdn_cpu`, `cogs_total`, `location`, `inv_variance`, `average_cost`, `asset`, `revenue`, `buying_units`, `selling_units`, `stocking_units`, `preferred_price`, `web_price`, `clearance_price`, `id_number`, `operator`, `lsd`, `quantity`, `product_name`, `cost`, `usd_cpu`, `commission_price`, `markup_perc`, `current_inventory`, `write_offs`, `min_max`, `status`, `note`, `unit_price`, `unit_cost`, `rent_price`, `rental_days`, `rental_weeks`, `rental_months`, `rental_years`, `reminder_alert`, `daily`,`weekly`, `monthly`, `annually`,  `total_days`, `total_hours`, `total_km`, `total_miles`, `bill_of_material`, `include_in_so`,`include_in_po`,`include_in_pos`, `drum_unit_cost`, `drum_unit_price`, `tote_unit_cost`, `tote_unit_price`) SELECT inventoryid, code, brand, category, sub_category, part_no, gst_exempt, description, application, supplimentary, comment, question, request, display_website, vendorid, size, gauge, weight, length, pressure, type, name, date_of_purchase, purchase_cost, sell_price, markup, freight_charge, min_bin, current_stock, final_retail_price, admin_price, wholesale_price, commercial_price, client_price, purchase_order_price, sales_order_price, distributor_price, minimum_billable, estimated_hours, actual_hours, msrp, quote_description, usd_invoice, shipping_rate, shipping_cash, exchange_rate, exchange_cash, cdn_cpu, cogs_total, location, inv_variance, average_cost, asset, revenue, buying_units, selling_units, stocking_units, preferred_price, web_price, clearance_price, id_number, operator, lsd, quantity, product_name, cost, usd_cpu, commission_price, markup_perc, current_inventory, write_offs, min_max, status, note, unit_price, unit_cost, rent_price, rental_days, rental_weeks, rental_months, rental_years, reminder_alert, daily, weekly, monthly, annually, total_days, total_hours, total_km, total_miles, bill_of_material, include_in_so, include_in_po, include_in_pos, drum_unit_cost, drum_unit_price, tote_unit_cost, tote_unit_price from `inventory` WHERE `inventoryid`='$inventoryid'";
		        $result_insert_invoice = mysqli_query($dbc, $query_insert_invoice);
		        $before_change = '';
		        $history = "New product Added. <br />";
				    add_update_history($dbc, 'inventory_history', $history, '', $before_change);
		    } else {
		        $query = mysqli_query($dbc,"DELETE FROM products WHERE `inventoryid`='$inventoryid'");
		    }
	}
}
?>
