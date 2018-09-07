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
}
?>
