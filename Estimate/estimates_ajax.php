<?php include('../include.php');
error_reporting(0);
checkAuthorised();
ob_clean();

if($_GET['action'] == 'save_template_field') {
	$table = filter_var($_POST['table_name'],FILTER_SANITIZE_STRING);
	$line_id = filter_var($_POST['line_id'],FILTER_SANITIZE_STRING);
	$heading_id = filter_var($_POST['heading_id'],FILTER_SANITIZE_STRING);
	$template_id = filter_var($_POST['template_id'],FILTER_SANITIZE_STRING);
	$field_name = filter_var($_POST['field_name'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);

	$sql = "";
	if($table == 'estimate_templates') {
		if(!is_numeric($template_id)) {
			mysqli_query($dbc, "INSERT INTO `estimate_templates` () VALUES ()");
			$template_id = mysqli_insert_id($dbc);
			echo $template_id;
		}
		$sql = "UPDATE `$table` SET `$field_name`='$value' WHERE `id`='$template_id'";
	} else if($table == 'estimate_template_headings') {
		if(!is_numeric($heading_id)) {
			mysqli_query($dbc, "INSERT INTO `estimate_template_headings` () VALUES ()");
			$heading_id = mysqli_insert_id($dbc);
			echo $heading_id;
		}
		$sql = "UPDATE `$table` SET `$field_name`='$value', `template_id`='$template_id' WHERE `id`='$heading_id'";
	} else if($table == 'estimate_template_lines') {
		if(!is_numeric($line_id)) {
			mysqli_query($dbc, "INSERT INTO `estimate_template_lines` () VALUES ()");
			$line_id = mysqli_insert_id($dbc);
			echo $line_id;
		}
		$sql = "UPDATE `$table` SET `$field_name`='$value', `heading_id`='$heading_id' WHERE `id`='$line_id'";
	}
	mysqli_query($dbc, $sql);
} else if($_GET['action'] == 'set_sort_order') {
	$table = filter_var($_POST['table_name'],FILTER_SANITIZE_STRING);
	$i = 0;
	foreach($_POST['sort_ids'] as $id) {
		mysqli_query($dbc, "UPDATE `$table` SET `sort_order`='$i' WHERE `id`='$id'");
		$i++;
	}
} else if($_GET['action'] == 'estimate_uploads') {
	$table = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
	$estimate = filter_var($_POST['estimate'],FILTER_SANITIZE_STRING);
	foreach($_FILES['files']['name'] as $file => $basename) {
		if($table == 'estimate_document') {
			$filename = preg_replace('/(\.[A-Za-z0-9]*)/', '$1', preg_replace('/[^\.A-Za-z0-9]/','',$basename));
			$i = 0;
			while(file_exists('download/'.$filename)) {
				$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.++$i.')$1', preg_replace('/[^\.A-Za-z0-9]/','',$basename));
			}
			move_uploaded_file($_FILES['files']['tmp_name'][$file],'download/'.$filename);
			mysqli_query($dbc, "INSERT INTO `estimate_document` (`estimateid`,`upload`,`created_by`) VALUES ('$estimate','$filename','".$_SESSION['contactid']."')");
		}
	}
} else if($_GET['action'] == 'table_locks') {
	$user_id = filter_var($_POST['session_id'],FILTER_SANITIZE_STRING);
	$table_row = filter_var($_POST['estimateid'],FILTER_SANITIZE_STRING);

	//Check if anybody is using the currently requested section
	$locked = [];
	$messages = [];
	foreach($_POST['section'] as $section_name) {
		$section_name = filter_var($section_name,FILTER_SANITIZE_STRING);
		$current_locks = mysqli_fetch_array(mysqli_query($dbc, "SELECT `user_id` FROM `table_locks` WHERE TIMEDIFF(CURRENT_TIMESTAMP,`locked_at`) < '00:10:00' AND `table_name`='estimates' AND `tab_name`='$section_name' AND `table_row_id`='$table_row' AND `user_id` != '$user_id'"));
		if($current_locks['user_id'] > 0) {
			$locked[] = $section_name;
			$messages[] = get_contact($dbc, $current_locks['user_id'])." has a lock on the $section_name tab.\n";
		} else {
			//Create a row for the user if it doesn't exist
			mysqli_query($dbc, "INSERT INTO `table_locks` (`user_id`, `tab_name`) SELECT '$user_id', '$section_name' FROM (SELECT COUNT(*) rows FROM `table_locks` WHERE `user_id`='$user_id' AND `tab_name`='$section_name') num WHERE num.rows=0");
			//Mark the section as locked by the current user
			mysqli_query($dbc, "UPDATE `table_locks` SET `locked_at`=CURRENT_TIMESTAMP, `table_name`='estimates', `tab_name`='$section_name', `table_row_id`='$table_row' WHERE `user_id`='$user_id' AND `tab_name`='$section_name'");
		}
	}
	echo implode(',',$locked).'#*#'.implode('',$messages);
} else if($_GET['action'] == 'list_estimate_scope') {
	$src = filter_var($_POST['src'],FILTER_SANITIZE_STRING);
	$query = mysqli_query($dbc, "SELECT `id`,`heading`,`description`,`src_table`,`src_id`,`qty`,`cost`,`price`,`retail`,`multiple` FROM `estimate_scope` WHERE `estimateid`='$src' AND `deleted`=0 ORDER BY `sort_order`");
	echo "SELECT `id`,`heading`,`description`,`src_table`,`src_id`,`qty`,`cost`,`price`,`retail`,`multiple` FROM `estimate_scope` WHERE `estimateid`='$src' AND `deleted`=0 ORDER BY `sort_order`";
	if(mysqli_num_rows($query) > 0) {
		echo "<table class='table table_bordered'>
		<tr class='hidden-sm hidden-xs'>
			<th>Include</th>
			<th>Heading</th>
			<th>Description</th>
			<th>Quantity</th>
			<th>Cost</th>
			<th>Price</th>
		</tr>";
		while($line = mysqli_fetch_assoc($query)) {
			$description = $line['description'];
			if($description == '' && $line['src_table'] == 'equipment') {
				$description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`make`,''),' ',IFNULL(`model`,''),' ',IFNULL(`label`,''),' ',IFNULL(`unit_number`,'')) label FROM `equipment` WHERE `equipmentid`='".$line['src_id']."'"))['label'];
			} else if($description == '' && $line['src_table'] == 'inventory') {
				$description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`product_name`,''),' ',IFNULL(`name`,''),' ',IFNULL(`part_no`,'')) label FROM `inventory` WHERE `inventoryid`='".$line['src_id']."'"))['label'];
			} else if($description == '' && $line['src_table'] == 'labour') {
				$description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`labour_type`,''),' ',IFNULL(`category`,''),' ',IFNULL(`heading`,''),' ',IFNULL(`name`,'')) label FROM `labour` WHERE `labourid`='".$line['src_id']."'"))['label'];
			} else if($description == '' && $line['src_table'] == 'material') {
				$description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`sub_category`,''),' ',IFNULL(`name`,'')) label FROM `material` WHERE `materialid`='".$line['src_id']."'"))['label'];
			} else if($description == '' && $line['src_table'] == 'positions') {
				$description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` label FROM `positions` WHERE `position_id`='".$line['src_id']."'"))['label'];
			} else if($description == '' && $line['src_table'] == 'products') {
				$description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`heading`,'')) label FROM `products` WHERE `productid`='".$line['src_id']."'"))['label'];
			} else if($description == '' && $line['src_table'] == 'services') {
				$description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`heading`,'')) label FROM `services` WHERE `serviceid`='".$line['src_id']."'"))['label'];
			} else if($description == '' && $line['src_table'] == 'vpl') {
				$description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`product_name`,'')) label FROM `vendor_price_list` WHERE `inventoryid`='".$line['src_id']."'"))['label'];
			}
			echo "<tr>
				<td data-title='Include'><label class='form-checkbox-any'><input type='checkbox' checked name='include' value='".$line['id']."'> Include</label></td>
				<td data-title='Heading'>".$line['heading']."</td>
				<td data-title='Description'>".$description."</td>
				<td data-title='Quantity'>".$line['qty']."</td>
				<td data-title='Cost'>".number_format($line['cost'],2)."</td>
				<td data-title='Price'>".number_format($line['price'],2)."</td>
			</tr>";
		}
		echo "</table>";
	} else {
		echo "<h3>No Details Found</h3>";
	}
} else if($_GET['action'] == 'list_template_scope') {
	$template = filter_var($_POST['template'],FILTER_SANITIZE_STRING);
	$query = mysqli_query($dbc, "SELECT items.`id`,headings.`heading_name`,items.`description`,items.`src_table`,items.`src_id`,items.`qty` FROM `estimate_template_headings` headings LEFT JOIN `estimate_template_lines` items ON items.`heading_id`=headings.`id` WHERE headings.`deleted`=0 AND items.`deleted`=0 AND headings.`template_id`='$template' ORDER BY items.`sort_order`");
	if(mysqli_num_rows($query) > 0) {
		echo "<table class='table table_bordered'>
		<tr class='hidden-sm hidden-xs'>
			<th>Include</th>
			<th>Heading</th>
			<th>Description</th>
			<th>Quantity</th>
			<th>Cost</th>
			<th>Price</th>
		</tr>";
		while($line = mysqli_fetch_assoc($query)) {
			$description = $line['description'];
			if($description == '' && $line['src_table'] == 'equipment') {
				$description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`make`,''),' ',IFNULL(`model`,''),' ',IFNULL(`label`,''),' ',IFNULL(`unit_number`,'')) label FROM `equipment` WHERE `equipmentid`='".$line['src_id']."'"))['label'];
			} else if($description == '' && $line['src_table'] == 'inventory') {
				$description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`product_name`,''),' ',IFNULL(`name`,''),' ',IFNULL(`part_no`,'')) label FROM `inventory` WHERE `inventoryid`='".$line['src_id']."'"))['label'];
			} else if($description == '' && $line['src_table'] == 'labour') {
				$description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`labour_type`,''),' ',IFNULL(`category`,''),' ',IFNULL(`heading`,''),' ',IFNULL(`name`,'')) label FROM `labour` WHERE `labourid`='".$line['src_id']."'"))['label'];
			} else if($description == '' && $line['src_table'] == 'material') {
				$description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`sub_category`,''),' ',IFNULL(`name`,'')) label FROM `material` WHERE `materialid`='".$line['src_id']."'"))['label'];
			} else if($description == '' && $line['src_table'] == 'positions') {
				$description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` label FROM `positions` WHERE `position_id`='".$line['src_id']."'"))['label'];
			} else if($description == '' && $line['src_table'] == 'products') {
				$description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`heading`,'')) label FROM `products` WHERE `productid`='".$line['src_id']."'"))['label'];
			} else if($description == '' && $line['src_table'] == 'services') {
				$description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`heading`,'')) label FROM `services` WHERE `serviceid`='".$line['src_id']."'"))['label'];
			} else if($description == '' && $line['src_table'] == 'vpl') {
				$description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`product_name`,'')) label FROM `vendor_price_list` WHERE `inventoryid`='".$line['src_id']."'"))['label'];
			} else if($description == '' && $line['src_id'] > 0) {
				$description = get_contact($dbc, $line['src_id']);
			}
			echo "<tr>
				<td data-title='Include'><label class='form-checkbox-any'><input type='checkbox' checked name='include' value='".$line['id']."'> Include</label></td>
				<td data-title='Heading'>".$line['heading_name']."</td>
				<td data-title='Description'>".$description."</td>
				<td data-title='Quantity'>".$line['qty']."</td>
				<td data-title='Cost'>".number_format($line['cost'],2)."</td>
				<td data-title='Price'>".number_format($line['price'],2)."</td>
			</tr>";
		}
		echo "</table>";
	} else {
		echo "<h3>No Details Found</h3>";
	}
} else if($_GET['action'] == 'copy_estimate') {
	$src = filter_var($_POST['src'],FILTER_SANITIZE_STRING);
	$target = filter_var($_POST['target'],FILTER_SANITIZE_STRING);
	$include = filter_var($_POST['include'],FILTER_SANITIZE_STRING);
	$sort = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`sort_order`) FROM `estimate_scope` WHERE `estimateid`='$target'"))[0] + 1;
	mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`heading`,`description`,`src_table`,`src_id`,`rate_card`,`uom`,`qty`,`cost`,`profit`,`margin`,`price`,`retail`,`multiple`,`sort_order`)
		SELECT '$target',`heading`,`description`,`src_table`,`src_id`,`rate_card`,`uom`,`qty`,`cost`,`profit`,`margin`,`price`,`retail`,`multiple`,`sort_order` + $sort FROM `estimate_scope` WHERE `estimateid`='$src' AND `deleted`=0 AND `id` IN ($include)");
} else if($_GET['action'] == 'apply_template') {
	$template = filter_var($_POST['template'],FILTER_SANITIZE_STRING);
	$target = filter_var($_POST['target'],FILTER_SANITIZE_STRING);
	$sort = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`sort_order`) FROM `estimate_scope` WHERE `estimateid`='$target'"))[0] + 1;
	$include = filter_var($_POST['include'],FILTER_SANITIZE_STRING);
	$inv_cost_field = get_config($dbc,'inventory_cost');
	foreach($_POST['rate'] as $rate) {
		$rate = filter_var($rate,FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`templateid`,`templateline`,`heading`,`description`,`src_table`,`src_id`,`qty`,`rate_card`,`sort_order`)
			SELECT '$target','$template',items.`id`,headings.`heading_name`,items.`description`,items.`src_table`,items.`src_id`,items.`qty`,'$rate' rate_card,(headings.`sort_order` * 100 + items.`sort_order` + $sort) sort_order
				FROM `estimate_template_headings` headings LEFT JOIN `estimate_template_lines` items ON items.`heading_id`=headings.`id` WHERE headings.`deleted`=0 AND items.`deleted`=0 AND headings.`template_id`='$template' AND `items`.`id` IN ($include)");
		$entries = mysqli_query($dbc, "SELECT * FROM `estimate_scope` WHERE `estimate_scope`.`estimateid`='$target' AND `estimate_scope`.`templateid`='$template' AND `estimate_scope`.`deleted`=0");
		while($entry = mysqli_fetch_array($entries)) {
			$line = [];
			if($entry['src_table'] == 'inventory') {
				$line = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `inventory` WHERE `inventoryid`='{$entry['item_id']}'"));
			} else if($entry['src_table'] == 'vpl') {
				$line = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `vendor_price_list` WHERE `inventoryid`='{$entry['item_id']}'"));
			}
			$rate = explode(':',$rate);
			$rate_name = '';
			if($rate[0] == 'SCOPE') {
				$specific = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `rate_card_estimate_scope_lines` WHERE `line_id`='{$entry['templateline']}' AND `rate_id` IN ('{$rate[1]}',0) ORDER BY `rate_id` DESC"));
			} else if($rate[0] == 'COMPANY') {
				$rate_name = mysqli_fetch_array(mysqli_query($dbc, "SELECT `rate_card_name` FROM `company_rate_card` WHERE `companyrcid`='{$rate[1]}'"))[0];
			}
			$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='{$entry['src_table']}' AND `tile_name`!='miscellaneous' AND `item_id`='{$entry['src_id']}' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') ORDER BY `rate_card_name` != '$rate_name'");
			if(mysqli_num_rows($general) == 0 && $entry['src_table'] == 'clients') {
				$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='clients' AND `description`='".get_contact($dbc, $entry['src_id'])."' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
			} else if(mysqli_num_rows($general) == 0 && $entry['src_table'] == 'equipment') {
				$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='equipment' AND `description` IN (SELECT `unit_number` FROM `equipment` WHERE `equipmentid`='{$entry['src_id']}') AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')
					UNION SELECT * FROM `equipment_rate_table` WHERE `deleted`=0 AND `equipment_id`='{$entry['src_id']}' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')
					UNION SELECT * FROM `category_rate_table` WHERE `deleted`=0 AND `category` IN (SELECT `category` FROM `equipment` WHERE `equipmentid`='{$entry['src_id']}') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
			} else if(mysqli_num_rows($general) == 0 && $entry['src_table'] == 'inventory') {
				$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='inventory' AND `description` IN (SELECT `product_name` FROM `inventory` WHERE `inventoryid`='{$entry['src_id']}') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
				if(mysqli_num_rows($general) == 0) {
					$general = mysqli_query($dbc, "SELECT '' `uom`, 1 `qty`, `$inv_cost_field` `cost`, '' `profit`, '' `margin`, '' `cust_price`, '' `retail_rate` FROM `inventory` WHERE `inventoryid`='{$entry['src_id']}'");
				}
			} else if(mysqli_num_rows($general) == 0 && $entry['src_table'] == 'labour') {
				$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='labour' AND `description` IN (SELECT `heading` FROM `labour` WHERE `labourid`='{$entry['src_id']}') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
			} else if(mysqli_num_rows($general) == 0 && $entry['src_table'] == 'material') {
				$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='material' AND `description` IN (SELECT `name` FROM `material` WHERE `materialid`='{$entry['src_id']}') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
			} else if(mysqli_num_rows($general) == 0 && $entry['src_table'] == 'position') {
				$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='position' AND `description` IN (SELECT `name` FROM `positions` WHERE `position_id`='{$entry['src_id']}') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')
					UNION SELECT * FROM `position_rate_table` WHERE `position_id`='{$entry['src_id']}' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
			} else if(mysqli_num_rows($general) == 0 && $entry['src_table'] == 'products') {
				$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='products' AND `description` IN (SELECT CONCAT(`category`,' ',`heading`) FROM `products` WHERE `productid`='{$entry['src_id']}') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
			} else if(mysqli_num_rows($general) == 0 && $entry['src_table'] == 'services') {
				$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='services' AND `description` IN (SELECT CONCAT(`category`,' ',`heading`) FROM `services` WHERE `serviceid`='{$entry['src_id']}') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')
					UNION SELECT * FROM `service_rate_card` WHERE `serviceid`='{$entry['src_id']}' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
			} else if(mysqli_num_rows($general) == 0 && $entry['src_table'] == 'staff') {
				$general = mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE LOWER(`tile_name`)='staff' AND `description`='".get_contact($dbc, $entry['src_id'])."' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
			}
			$general = mysqli_fetch_array($general);
			$uom = ($specific['uom'] != '' ? $specific['uom'] : $general['uom']);
			$qty = ($entry['qty'] > 0 ? $entry['qty'] : ($specific['qty'] > 0 ? $specific['qty'] : $general['qty']));
			$cost = ($line['cost'] > 0 ? $line['cost'] : ($specific['cost'] > 0 ? $specific['cost'] : $general['cost'])) * ((100 + get_config($dbc, 'inventory_markup')) / 100);
			$profit = ($specific['profit'] > 0 ? $specific['profit'] : $general['profit']);
			$margin = ($specific['margin'] > 0 ? $specific['margin'] : $general['margin']);
			$price = ($specific['cust_price'] > 0 ? $specific['cust_price'] : $general['cust_price']);
			$retail = ($line['final_retail_price'] > 0 ? $line['final_retail_price'] : ($specific['retail_rate'] > 0 ? $specific['retail_rate'] : $general['retail_rate']));

			$tempid = $entry['id'];
			$before_change = capture_before_change($dbc, 'estimate_scope', 'uom', 'id', $tempid);
			$before_change .= capture_before_change($dbc, 'estimate_scope', 'qty', 'id', $tempid);
			$before_change .= capture_before_change($dbc, 'estimate_scope', 'cost', 'id', $tempid);
			$before_change .= capture_before_change($dbc, 'estimate_scope', 'profit', 'id', $tempid);
			$before_change .= capture_before_change($dbc, 'estimate_scope', 'margin', 'id', $tempid);
			$before_change .= capture_before_change($dbc, 'estimate_scope', 'price', 'id', $tempid);
			$before_change .= capture_before_change($dbc, 'estimate_scope', 'retail', 'id', $tempid);

			mysqli_query($dbc, "UPDATE `estimate_scope` SET `uom`='$uom',`qty`='$qty',`cost`='$cost',`profit`='$profit',`margin`='$margin',`price`='$price',`retail`='$retail' WHERE `id`='{$entry['id']}'");

			$history = capture_after_change('uom', $uom);
			$history .= capture_after_change('qty', $qty);
			$history .= capture_after_change('cost', $cost);
			$history .= capture_after_change('profit', $profit);
			$history .= capture_after_change('margin', $margin);
			$history .= capture_after_change('price', $price);
			$history .= capture_after_change('retail', $retail);
			add_update_history($dbc, 'estimates_history', $history, '', $before_change);
		}
	}
} else if($_GET['action'] == 'estimate_add_heading') {
	$estimateid = filter_var($_POST['estimate'],FILTER_SANITIZE_STRING);
	$scope_name = filter_var($_POST['scope'],FILTER_SANITIZE_STRING);
	if(mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `scope_name`='$scope_name' AND `deleted`=0"))['count'] == 0) {
		mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`scope_name`,`heading`,`sort_order`) SELECT '$estimateid',CONCAT('Scope ',COUNT(DISTINCT IFNULL(`scope_name`,''))+1),'Heading 1',IFNULL(MAX(`sort_order`),0)+1 FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `scope_name`='$scope_name' AND `deleted`=0");
		$before_change = '';
		$history = "Estimates scope entry has been added. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	}
	mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`scope_name`,`heading`,`sort_order`) SELECT '$estimateid','$scope_name',CONCAT('Heading ',COUNT(DISTINCT `heading`)+1),IFNULL(MAX(`sort_order`),0)+1 FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `scope_name`='$scope_name' AND `deleted`=0");
	$before_change = '';
	$history = "Estimates scope entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
} else if($_GET['action'] == 'estimate_add_scope') {
	$estimateid = filter_var($_POST['estimate'],FILTER_SANITIZE_STRING);
	if(mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0"))['count'] == 0) {
		mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`scope_name`,`heading`,`sort_order`) SELECT '$estimateid',CONCAT('Scope ',COUNT(DISTINCT IFNULL(`scope_name`,''))+1),'Heading 1',IFNULL(MAX(`sort_order`),0)+1 FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0");
		$before_change = '';
		$history = "Estimates scope entry has been added. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	}
	mysqli_query($dbc, "INSERT INTO `estimate_scope` (`estimateid`,`scope_name`,`heading`,`sort_order`) SELECT '$estimateid',CONCAT('Scope ',COUNT(DISTINCT IFNULL(`scope_name`,''))+1),'Heading 1',IFNULL(MAX(`sort_order`),0)+1 FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0");
	$before_change = '';
	$history = "Estimates scope entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
} else if($_GET['action'] == 'estimate_fields') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$id_field = filter_var($_POST['id_field'],FILTER_SANITIZE_STRING);
	$table = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
	$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	if(is_array($_POST['value'])) {
		$value = filter_var(implode(',',$_POST['value']),FILTER_SANITIZE_STRING);
	} else {
		$value = filter_var(htmlentities($_POST['value']),FILTER_SANITIZE_STRING);
	}
	$estimate = filter_var($_POST['estimate'],FILTER_SANITIZE_STRING);
	if(!($id > 0)) {
		mysqli_query($dbc, "INSERT INTO `$table` (`estimateid`) VALUES ('$estimate')");
		$id = mysqli_insert_id($dbc);
		echo $id;
		if($table == 'estimate') {
			$before_change = capture_before_change($dbc, 'estimate', 'created_by', 'estimateid', $id);
			$before_change .= capture_before_change($dbc, 'estimate', 'created_date', 'estimateid', $id);
			mysqli_query($dbc, "UPDATE `estimate` SET `created_by`='".$_SESSION['contactid']."', `created_date`=DATE(NOW()) WHERE `estimateid`='$id'");
			$history = capture_after_change('created_by', $_SESSION['contactid']);
			$history .= capture_after_change('created_date', DATE(NOW()));
			add_update_history($dbc, 'estimates_history', $history, '', $before_change);
			insert_day_overview($dbc, $_SESSION['contactid'], ESTIMATE_TILE, date('Y-m-d'), '', 'Added Estimate #'.$id, $id);
		}
	}
    if($id > 0 && $table == 'estimate' && in_array($field, ['businessid','businessid','clientid','siteid','afe_number','heading'])) {
        $ticket_field = $field;
        if($ticket_field == 'estimate_name') {
            $ticket_field = 'heading';
        }
        $dbc->query("UPDATE `tickets` SET `$ticket_field`='$value' WHERE `estimateid`='$id' AND `deleted`=0");
    }
	if($table == 'estimate_actions') {
		if($field == 'completed') {
			$value = htmlentities("Follow Up #$id Completed by ".get_contact($dbc, $_SESSION['contactid'])." on ".date('Y-m-d')."<br />".$value);
			mysqli_query($dbc, "INSERT INTO `estimate_notes` (`estimateid`, `heading`, `notes`, `created_by`) VALUES ('$estimate', 'Follow Up Completed', '$value', '{$_SESSION['contactid']}')");
			$before_change = '';
			$history = "Estimates notes entry has been added. <br />";
			add_update_history($dbc, 'estimates_history', $history, '', $before_change);
			$value = 1;
			$history = htmlentities(get_contact($dbc, $_SESSION['contactid'])." completed follow up action $id on ".date('Y-m-d h:i a'));
        } else if($field == 'delete') {
						$before_change = capture_before_change($dbc, 'estimate_actions', 'deleted', 'id', $id);
            mysqli_query($dbc, "UPDATE `estimate_actions` SET `deleted`=1 WHERE `id`='$id'");
						$history = capture_after_change('deleted', 1);
						add_update_history($dbc, 'estimates_history', $history, '', $before_change);
            $history = htmlentities(get_contact($dbc, $_SESSION['contactid'])." deleted follow up action $id on ".date('Y-m-d h:i a'));
		} else if($field == '') {
			$history = htmlentities(get_contact($dbc, $_SESSION['contactid'])." added follow up action $id on ".date('Y-m-d h:i a'));
		} else {
			$history = htmlentities(get_contact($dbc, $_SESSION['contactid'])." set follow up $field to '$value' for action $id on ".date('Y-m-d h:i a'));
		}
	} else if($table == 'estimate_notes') {
		mysqli_query($dbc, "UPDATE `estimate_notes` SET `heading`=IF(IFNULL(`heading`,'')='','Note',`heading`), `created_by`='".$_SESSION['contactid']."' WHERE `id`='$id'");
		$before_change = '';
		$history = "Estimates note entry has been updated for id $id. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);
		$history = htmlentities(get_contact($dbc, $_SESSION['contactid'])." added note '$value' on ".date('Y-m-d h:i a'));
	} else {
		$history = htmlentities(get_contact($dbc, $_SESSION['contactid'])." set $field to '$value' on ".date('Y-m-d h:i a'));
	}
	if($table == 'estimate' && $field == 'status' && $value == 'archived') {
    $date_of_archival = date('Y-m-d');
		$before_change = capture_before_change($dbc, 'estimate', 'deleted', 'estimateid', $id);
		$before_change .= capture_before_change($dbc, 'estimate', 'date_of_archival', 'estimateid', $id);
		mysqli_query($dbc, "UPDATE `estimate` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `estimateid`='$id'");
		$history = capture_after_change('deleted', 1);
		$history .= capture_after_change('date_of_archival', $date_of_archival);
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	} else if($table == 'estimate' && $field == 'status') {
		mysqli_query($dbc, "UPDATE `estimate` SET `status_date`=DATE(NOW())");
		$before_change = '';
		$history = "Set estimate date for all the entries. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	}
	mysqli_query($dbc, "UPDATE `$table` SET `$field`='$value' WHERE `$id_field`='$id'");
	$before_change = capture_before_change($dbc, 'estimate', 'history', 'estimateid', $estimate);
	mysqli_query($dbc, "UPDATE `estimate` SET `history`=CONCAT(IFNULL(CONCAT(`history`,'<br />'),''),'$history') WHERE `estimateid`='$estimate'");
	$history_reports = capture_after_change('history', $history);
	add_update_history($dbc, 'estimates_history', $history_reports, '', $before_change);


	//Insert into day overview if last edit was wiithin 15 minutes
	$day_overview_last = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `timestamp` FROM `day_overview` WHERE `type` = 'Estimate' AND `tableid` = '$estimate' AND `contactid` = '".$_SESSION['contactid']."' ORDER BY `timestamp` DESC"));
	$timestamp_now = date('Y-m-d h:i:s');
	$timediff = strtotime($timestamp_now) - strtotime($day_overview_last['timestamp']);
	if($timediff > 900 && !empty($estimate)) {
		$estimate_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `estimate_name` FROM `estimate` WHERE `estimateid` = '$estimate'"))['estimate_name'];
		insert_day_overview($dbc, $_SESSION['contactid'], ESTIMATE_TILE, date('Y-m-d'), '', 'Edited Estimate #'.$estimate.(!empty($estimate_name) ? ': '.$estimate_name : ''), $estimate);
	}

} else if($_GET['action'] == 'setting_types') {
	set_config($dbc, 'estimate_tabs', implode(',',$_POST['estimate_tabs']));
} else if($_GET['action'] == 'settings_general') {
	$name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
	$value = filter_var(htmlentities($_POST['value']),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT '$name' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='$name') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$value' WHERE `name`='$name'");
} else if($_GET['action'] == 'setting_dashboard') {
	$length = filter_var($_POST['length'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimate_dashboard_length' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimate_dashboard_length') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$length' WHERE `name`='estimate_dashboard_length'");
	set_config($dbc, 'estimate_summary_view', $_POST['summary']);
} else if($_GET['action'] == 'setting_status') {
	$status = filter_var(implode('#*#',array_filter($_POST['status'])),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimate_status' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimate_status') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$status' WHERE `name`='estimate_status'");
	$summary = filter_var(implode('#*#',array_filter($_POST['summary'])),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimate_summarize' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimate_summarize') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$summary' WHERE `name`='estimate_summarize'");
	$projects = filter_var($_POST['projects'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimate_project_status' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimate_project_status') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$projects' WHERE `name`='estimate_project_status'");
} else if($_GET['action'] == 'setting_groups') {
	$groups = [];
	foreach($_POST['groups'] as $group) {
		$groups[] = implode(',',$group);
	}
	$group_list = filter_var(implode('#*#',$groups),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `field_config_estimate` (`estimate_groups`) SELECT '' FROM (SELECT COUNT(*) rows FROM `field_config_estimate`) num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `field_config_estimate` SET `estimate_groups`='$group_list'");
} else if($_GET['action'] == 'setting_fields') {
	$field_list = filter_var(implode(',',$_POST['fields']),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `field_config_estimate` (`config_fields`) SELECT '' FROM (SELECT COUNT(*) rows FROM `field_config_estimate`) num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `field_config_estimate` SET `config_fields`='$field_list'");
	$scope = filter_var(implode('#*#',$_POST['scope']),FILTER_SANITIZE_STRING);
	if($scope != '') {
		mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimate_field_order' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimate_field_order') num WHERE num.rows=0");
		mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$scope' WHERE `name`='estimate_field_order'");
	}
} else if($_GET['action'] == 'setting_reporting') {
	$stats = filter_var(implode('#*#',array_filter($_POST['stats'])),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimate_report_stats' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimate_report_stats') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$stats' WHERE `name`='estimate_report_stats'");
	$alerts = filter_var(implode('#*#',array_filter($_POST['alerts'])),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'estimate_report_alerts' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='estimate_report_alerts') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$alerts' WHERE `name`='estimate_report_alerts'");
} else if($_GET['action'] == 'deleteStyle') {
	$id = filter_var($_GET['styleid'],FILTER_SANITIZE_STRING);
    $date_of_archival = date('Y-m-d');
	mysqli_query($dbc, "UPDATE `estimate_pdf_setting` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `pdfsettingid`='$id'");
	$before_change = '';
	$history = "Estimate PDF setting has been updated. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
} else if($_GET['action'] == 'clearEstimates') {
	set_user_settings($dbc, 'estimate_closed', date('Y-m-d'));
} else if($_GET['action'] == 'addContentPage') {
	$dbc->query('INSERT INTO `estimate_content_page` () VALUES ()');
	echo $dbc->insert_id;
} else if($_GET['action'] == 'inventory_list') {
	$category = filter_var($_GET['category'],FILTER_SANITIZE_STRING);
	$inv_list = $dbc->query("SELECT * FROM `inventory` WHERE `deleted`=0 AND `category`='$category'");
	echo '<div class="form-group hide-titles-mob">
		<div class="col-sm-8">Inventory</div>
		<div class="col-sm-2">Price</div>
		<div class="col-sm-2">Quantity</div>
	</div>';
	while($inv = $inv_list->fetch_assoc()) {
		echo '<div class="form-group">
			<div class="col-sm-8"><label class="show-on-mob">Inventory:</label><input type="hidden" name="inventoryid[]" value="'.$inv['inventoryid'].'">'.$inv['name'].' '.$inv['product_name'].' '.$inv['part_no'].'</div>
			<div class="col-sm-2"><label class="show-on-mob">Price:</label><input type="hidden" name="cost[]" value="'.($inv['average_cost'] > 0 ? $inv['average_cost'] : $inv['cost']).'"><input type="number" readonly class="form-control" name="price[]" value="'.$inv['final_retail_price'].'"></div>
			<div class="col-sm-2"><label class="show-on-mob">Qty:</label><input type="number" class="form-control" name="qty[]" value=""></div>
		</div>';
	}
} else if($_GET['action'] == 'service_list') {
	$category = filter_var($_GET['category'],FILTER_SANITIZE_STRING);
	$serv_list = $dbc->query("SELECT `services`.`serviceid`,`services`.`heading`, `company_rate_card`.`cust_price`,`company_rate_card`.`cost` FROM `services` LEFT JOIN `company_rate_card` ON `services`.`serviceid`=`company_rate_card`.`item_id` AND `company_rate_card`.`tile_name`='Services' AND `company_rate_card`.`deleted`=0 WHERE `services`.`deleted`=0 AND `services`.`category`='$category' GROUP BY `services`.`serviceid`");
	echo '<div class="form-group hide-titles-mob">
		<div class="col-sm-8">Service</div>
		<div class="col-sm-2">Rate</div>
		<div class="col-sm-2">Quantity</div>
	</div>';
	while($service = $serv_list->fetch_assoc()) {
		echo '<div class="form-group">
			<div class="col-sm-8"><label class="show-on-mob">Service:</label><input type="hidden" name="serviceid[]" value="'.$service['serviceid'].'">'.$service['heading'].'</div>
			<div class="col-sm-2"><label class="show-on-mob">Rate:</label><input type="hidden" name="cost[]" value="'.$service['cost'].'"><input type="number" readonly class="form-control" name="price[]" value="'.$service['cust_price'].'"></div>
			<div class="col-sm-2"><label class="show-on-mob">Qty:</label><input type="number" class="form-control" name="qty[]" value=""></div>
		</div>';
	}
} else if($_GET['action'] == 'labour_list') {
	$category = filter_var($_GET['category'],FILTER_SANITIZE_STRING);
	$labour_list = $dbc->query("SELECT `labour`.`labourid`,`labour`.`heading`, `company_rate_card`.`cust_price`,`company_rate_card`.`cost` FROM `labour` LEFT JOIN `company_rate_card` ON `labour`.`labourid`=`company_rate_card`.`item_id` AND `company_rate_card`.`tile_name`='Labour' AND `company_rate_card`.`deleted`=0 WHERE `labour`.`deleted`=0 AND `labour`.`labour_type`='$category' GROUP BY `labour`.`labourid`");
	echo '<div class="form-group hide-titles-mob">
		<div class="col-sm-8">Labour</div>
		<div class="col-sm-2">Rate</div>
		<div class="col-sm-2">Quantity</div>
	</div>';
	while($labour = $labour_list->fetch_assoc()) {
		echo '<div class="form-group">
			<div class="col-sm-8"><label class="show-on-mob">Labour:</label><input type="hidden" name="labour_id[]" value="'.$labour['labourid'].'">'.$labour['heading'].'</div>
			<div class="col-sm-2"><label class="show-on-mob">Rate:</label><input type="hidden" name="cost[]" value="'.$labour['cost'].'"><input type="number" readonly class="form-control" name="price[]" value="'.$labour['cust_price'].'"></div>
			<div class="col-sm-2"><label class="show-on-mob">Qty:</label><input type="number" class="form-control" name="qty[]" value=""></div>
		</div>';
	}
} else if($_GET['action'] == 'equip_list') {
	$category = filter_var($_GET['category'],FILTER_SANITIZE_STRING);
	$equip_list = $dbc->query("SELECT `equipment`.`equipmentid`,`equipment`.`heading`, `company_rate_card`.`cust_price`,`company_rate_card`.`cost` FROM `equipment` LEFT JOIN `company_rate_card` ON `equipment`.`equipmentid`=`company_rate_card`.`item_id` AND `company_rate_card`.`tile_name`='Equipment' AND `company_rate_card`.`deleted`=0 WHERE `equipment`.`deleted`=0 AND `equipment`.`category`='$category' GROUP BY `equipment`.`equipmentid`");
	echo '<div class="form-group hide-titles-mob">
		<div class="col-sm-8">Equipment</div>
		<div class="col-sm-2">Rate</div>
		<div class="col-sm-2">Quantity</div>
	</div>';
	while($equipment = $equip_list->fetch_assoc()) {
		echo '<div class="form-group">
			<div class="col-sm-8"><label class="show-on-mob">Equipment:</label><input type="hidden" name="equipment_id[]" value="'.$equipment['equipmentid'].'">'.$equipment['heading'].'</div>
			<div class="col-sm-2"><label class="show-on-mob">Rate:</label><input type="hidden" name="cost[]" value="'.$equipment['cost'].'"><input type="number" readonly class="form-control" name="price[]" value="'.$equipment['cust_price'].'"></div>
			<div class="col-sm-2"><label class="show-on-mob">Qty:</label><input type="number" class="form-control" name="qty[]" value=""></div>
		</div>';
	}
} else if($_GET['action'] == 'material_list') {
	$category = filter_var($_GET['category'],FILTER_SANITIZE_STRING);
	$material_list = $dbc->query("SELECT `material`.`materialid`,`material`.`name`, `company_rate_card`.`cust_price`,`company_rate_card`.`cost` FROM `material` LEFT JOIN `company_rate_card` ON `material`.`materialid`=`company_rate_card`.`item_id` AND `company_rate_card`.`tile_name`='Material' AND `company_rate_card`.`deleted`=0 WHERE `material`.`deleted`=0 AND `material`.`category`='$category' GROUP BY `material`.`materialid`");
	echo '<div class="form-group hide-titles-mob">
		<div class="col-sm-8">Material</div>
		<div class="col-sm-2">Rate</div>
		<div class="col-sm-2">Quantity</div>
	</div>';
	while($material = $material_list->fetch_assoc()) {
		echo '<div class="form-group">
			<div class="col-sm-8"><label class="show-on-mob">Material:</label><input type="hidden" name="material_id[]" value="'.$material['materialid'].'">'.$material['name'].'</div>
			<div class="col-sm-2"><label class="show-on-mob">Rate:</label><input type="hidden" name="cost[]" value="'.$material['cost'].'"><input type="number" readonly class="form-control" name="price[]" value="'.$material['cust_price'].'"></div>
			<div class="col-sm-2"><label class="show-on-mob">Qty:</label><input type="number" class="form-control" name="qty[]" value=""></div>
		</div>';
	}
} else if($_GET['action'] == 'product_list') {
	$category = filter_var($_GET['category'],FILTER_SANITIZE_STRING);
	$product_list = $dbc->query("SELECT `products`.`productid`,`products`.`heading`, `company_rate_card`.`cust_price`,`company_rate_card`.`cost` FROM `products` LEFT JOIN `company_rate_card` ON `products`.`productid`=`company_rate_card`.`item_id` AND `company_rate_card`.`tile_name`='Products' AND `company_rate_card`.`deleted`=0 WHERE `products`.`deleted`=0 AND `products`.`category`='$category' GROUP BY `products`.`productid`");
	echo '<div class="form-group hide-titles-mob">
		<div class="col-sm-8">Product</div>
		<div class="col-sm-2">Rate</div>
		<div class="col-sm-2">Quantity</div>
	</div>';
	while($product = $product_list->fetch_assoc()) {
		echo '<div class="form-group">
			<div class="col-sm-8"><label class="show-on-mob">Product:</label><input type="hidden" name="product_id[]" value="'.$product['productid'].'">'.$product['heading'].'</div>
			<div class="col-sm-2"><label class="show-on-mob">Rate:</label><input type="hidden" name="cost[]" value="'.$product['cost'].'"><input type="number" readonly class="form-control" name="price[]" value="'.$product['cust_price'].'"></div>
			<div class="col-sm-2"><label class="show-on-mob">Qty:</label><input type="number" class="form-control" name="qty[]" value=""></div>
		</div>';
	}
} else if($_GET['action'] == 'cost_analysis') {
	$id = filter_var($_GET['id'],FILTER_VALIDATE_INT);
    $estimateid = filter_var($_GET['estimateid'],FILTER_VALIDATE_INT);
    $qty = filter_var($_GET['qty'],FILTER_VALIDATE_INT);
    $profit = filter_var($_GET['profit'],FILTER_SANITIZE_STRING);
    $margin = filter_var($_GET['margin'],FILTER_SANITIZE_STRING);
    $price = filter_var($_GET['price'],FILTER_SANITIZE_STRING);
    $retail = filter_var($_GET['retail'],FILTER_SANITIZE_STRING);

		$before_change = capture_before_change($dbc, 'estimate_scope', 'qty', 'id', $id);
		$before_change .= capture_before_change($dbc, 'estimate_scope', 'profit', 'id', $id);
		$before_change .= capture_before_change($dbc, 'estimate_scope', 'margin', 'id', $id);
		$before_change .= capture_before_change($dbc, 'estimate_scope', 'price', 'id', $id);
		$before_change .= capture_before_change($dbc, 'estimate_scope', 'retail', 'id', $id);

    mysqli_query($dbc, "UPDATE `estimate_scope` SET `qty`='$qty', `profit`='$profit', `margin`='$margin', `price`='$price', `retail`='$retail' WHERE `id`='$id'");

		$history = capture_after_change('qty', $qty);
		$history .= capture_after_change('profit', $profit);
		$history .= capture_after_change('margin', $margin);
		$history .= capture_after_change('price', $price);
		$history .= capture_after_change('retail', $retail);

		add_update_history($dbc, 'estimates_history', $history, '', $before_change);

    $total_price = 0;
    $total_cost = 0;
    $query = mysqli_query($dbc, "SELECT `qty`, `cost`, `retail`, `pricing` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0");
	$us_exchange = json_decode(file_get_contents('https://www.bankofcanada.ca/valet/observations/group/FX_RATES_DAILY/json'), TRUE);
	$us_rate = $us_exchange['observations'][count($us_exchange['observations']) - 1]['FXUSDCAD']['v'];
    while( $row=mysqli_fetch_assoc($query) ) {
        $total_price += $row['retail'];
        $total_cost += $row['qty'] * ($row['pricing'] == 'usd_cpu' ? $row['cost'] * $us_rate : $row['cost']);
    }
    $margin = number_format(($total_cost > 0 ? ($total_price - $total_cost) / $total_cost * 100 : 0),2, '.', '');
    $profit = number_format($total_price - $total_cost,2, '.', '');
    $total = number_format($total_price,2, '.', '');
    echo $margin.'%' .'*#*'. '$'.$profit .'*#*'. '$'.$total;

} else if($_GET['action'] == 'save_status_for_archive') {
    $status_type = filter_var($_POST['status_type'],FILTER_SANITIZE_STRING);
    $status = filter_var($_POST['status'],FILTER_SANITIZE_STRING);
    $name = '';
    if ( $status_type=='closed' ) {
        $name = 'estimate_status_closed';
    } else if ( $status_type=='abandoned' ) {
        $name = 'estimate_status_abandoned';
    }

    set_config($dbc, $name, $status);
    echo '$status_type: '.$status_type.' | $status: '.$status.' | $name:'.$name.'<br>'.mysqli_error($dbc);
} else if($_GET['action'] == 'copy_as_template') {
	$estimateid = filter_var($_POST['estimate'],FILTER_SANITIZE_STRING);
	$template = 'Template - Copy of '.ESTIMATE_TILE.' '.$estimateid;
	mysqli_query($dbc, "INSERT INTO `estimate_templates` (`template_name`) VALUES ('$template')");
	$before_change = '';
	$history = "Estimates template entry has been added. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	$templateid = mysqli_insert_id($dbc);
	$headings = mysqli_query($dbc, "SELECT `heading` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0 GROUP BY `heading`");
	while($heading = mysqli_fetch_assoc($headings)['heading']) {
		mysqli_query($dbc, "INSERT INTO `estimate_template_headings` (`template_id`, `heading_name`) VALUES ('$templateid','$heading')");
		$before_change = '';
		$history = "Estimates template heading entry has been added. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);
		$headingid = mysqli_insert_id($dbc);
		mysqli_query($dbc, "INSERT INTO `estimate_template_lines` (`heading_id`, `src_table`, `description`, `src_id`, `qty`, `sort_order`) SELECT '$headingid', `src_table`, `description`, `src_id`, `qty`, `sort_order` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `heading`='$heading' AND `deleted`=0");
		$before_change = '';
		$history = "Estimates template lines entry has been added. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);
	}
	echo $templateid;
} else if($_GET['action'] == 'activate_ticket') {
    $ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
    $type = filter_var($_GET['type'],FILTER_SANITIZE_STRING);
    $status = get_config($dbc, 'ticket_default_status'.($type == '' ? '' : '_'.$type));
    $status = empty($status) ? get_config($dbc, 'ticket_default_status') : $status;
    set_field_value($status, 'status', 'tickets', 'ticketid', $ticketid);
}
