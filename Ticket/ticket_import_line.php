<?php if($_POST['export'] != '' || $_POST['import'] == 'template') {
	set_time_limit(1800);
	include_once('ticket_field_list.php');
	if($_POST['import'] == 'template') {
		$file_name = 'export/import_template.csv';
		$tickets = mysqli_query($dbc, "SELECT * FROM `tickets` LIMIT 1");
		$value_config = get_field_config($dbc,'tickets');
		$type_configs = mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE `name` LIKE 'ticket_fields_%'");
		while($type_fields = mysqli_fetch_assoc($type_configs)) {
			$value_config .= $type_fields['value'].',';
		}
	} else {
		$type = filter_var($_POST['export'],FILTER_SANITIZE_STRING);
		$file_name = 'export/export_'.$type.'_'.date('Y_m_d').'.csv';

		//Get the tickets to export
		if(!empty($_POST['export_projectid'])) {
			$project_query = " AND `projectid` = '".$_POST['export_projectid']."'";
		} else {
			$project_query = '';
		}
		$tickets = mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `deleted`=0 AND '$type' IN (`ticket_type`,'ALL') $project_query");

		//Get Fields and Apply Templates
		$value_config = ','.mysqli_fetch_assoc(mysqli_query($dbc,"SELECT tickets FROM field_config"))['tickets'].','.($type != '' ? get_config($dbc, 'ticket_fields_'.$type).',' : '');
		if(strpos($value_config,',TEMPLATE Work Ticket') !== FALSE) {
			$value_config = ',Information,PI Business,PI Name,PI Project,PI AFE,PI Sites,Staff,Staff Position,Staff Hours,Staff Overtime,Staff Travel,Staff Subsistence,Services,Service Category,Equipment,Materials,Material Quantity,Material Rates,Purchase Orders,Notes,';
		}
	}
	$headings = [];
	$counts = [];
	$ticket = mysqli_fetch_assoc($tickets);
	foreach(explode(',', 'REQUIRED'.$value_config) as $config) {
		foreach(ticket_field_name($config, 0) as $field_data) {
			$count = 1;
			if(!empty($field_data[3]) && empty($counts[$field_data[3]])) {
				$counts[$field_data[3]] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT MAX(`rows`) `max` FROM (SELECT COUNT(*) `rows` FROM `".$field_data[3]."` WHERE `deleted`=0 AND `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `deleted`=0) GROUP BY `ticketid`) `num`"))['max'];
				$counts[$field_data[3]] = $counts[$field_data[3]] > 0 ? $counts[$field_data[3]] : 1;
			}
			for($i = 0; $i < 1 || (!empty($field_data[3]) && $counts[$field_data[3]] > $i); $i++) {
				$headings[] = $field_data[1];
			}
		}
	}

    if (!file_exists('export')) {
        mkdir('export', 0777, true);
    }
	$export = fopen($file_name,'w');
	fputcsv($export,$headings);
	if($_POST['import'] != 'template') {
		do {
			$ticket_fields = [];
			foreach(explode(',','REQUIRED'.$value_config) as $config) {
				foreach(ticket_field_name($config, 0) as $i => $field_data) {
					if(!empty($field_data[3]) && $field_data[3] != 'tickets') {
						if($field_data[2] == 'po_line_detail') {
							$field_data[2] = 'po_line';
						} else if($field_data[2] == 'name') {
							$field_data[2] = 'item_id';
						}
						$j = 0;
						$fields = mysqli_query("SELECT `".$field_data[2]."` `field` FROM `".$field_data[3]."` WHERE `ticketid`='".$ticket['ticketid']."' AND `deleted`=0");
						while($ticket_fields[] = mysqli_fetch_assoc($fields)['field']) {
							$j++;
						}
						for(; $j < 1 || (!empty($field_data[3]) && $counts[$field_data[3]] > $j); $j++) {
							$ticket_fields[] = '';
						}
					} else if($field_data[2] == 'location' && $ticket['siteid'] > 0) {
						$ticket_fields[] = get_contact($dbc, $ticket['siteid'], 'site_name');
					} else if($field_data[2] == 'businessid_name' && $ticket['businessid'] > 0) {
						$ticket_fields[] = get_contact($dbc, $ticket['businessid'],'name');
					} else if($field_data[2] == 'clientid_name' && $ticket['clientid'] > 0) {
						$ticket_fields[] = get_contact($dbc, $ticket['clientid']);
					} else if($field_data[2] == 'agentid_name' && $ticket['agentid'] > 0) {
						$ticket_fields[] = get_contact($dbc, $ticket['agentid']);
					} else if($field_data[2] == 'contactid_name') {
						$name = '';
						foreach(explode(',',$ticket['contactid']) as $name_id) {
							if($name_id > 0) {
								$name .= get_contact($dbc, $name_id).',';
							}
						}
						$ticket_fields[] = $name;
					} else if($field_data[2] == 'internal_qa_contactid_name') {
						$name = '';
						foreach(explode(',',$ticket['internal_qa_contactid']) as $name_id) {
							if($name_id > 0) {
								$name .= get_contact($dbc, $name_id).',';
							}
						}
						$ticket_fields[] = $name;
					} else if($field_data[2] == 'deliverable_contactid_name') {
						$name = '';
						foreach(explode(',',$ticket['deliverable_contactid']) as $name_id) {
							if($name_id > 0) {
								$name .= get_contact($dbc, $name_id).',';
							}
						}
						$ticket_fields[] = $name;
					} else {
						$ticket_fields[] = $ticket[$field_data[2]];
					}
				}
			}
			fputcsv($export,$ticket_fields);
		} while($ticket = mysqli_fetch_assoc($tickets));
	}
	ob_clean();
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file_name));
	readfile($file_name);
	exit();
} else if($_POST['import'] == 'file') {
	set_time_limit(1800);
	include_once('ticket_field_list.php');
	$current_user = get_contact($dbc, $_SESSION['contactid']);
	$current_date = date('Y-m-d');
	$row = 1;
	$row_count = 0;
	$row_skipped = 0;
	$projectid = filter_var($_POST['projectid'],FILTER_SANITIZE_STRING);
	$businessid = filter_var($_POST['businessid'],FILTER_SANITIZE_STRING);
	$ticket_type = filter_var($_POST['ticket_type'],FILTER_SANITIZE_STRING);
	$equipmentid = filter_var($_POST['assignid'],FILTER_SANITIZE_STRING);
	$to_do_date = filter_var($_POST['to_do_date'],FILTER_SANITIZE_STRING);
	$client_cat = $dbc->query("SELECT `category` FROM `contacts` WHERE `deleted`=0 AND `status` > 0 AND `contactid` IN (SELECT `clientid` FROM `tickets` WHERE `deleted`=0)")->fetch_assoc()['category'];
	$agent_cat = $dbc->query("SELECT `category` FROM `contacts` WHERE `deleted`=0 AND `status` > 0 AND `contactid` IN (SELECT `agentid` FROM `tickets` WHERE `deleted`=0)")->fetch_assoc()['category'];
	$equipment_assignmentid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT MAX(`equipment_assignmentid`) `id` FROM `equipment_assignment` WHERE `equipmentid`='$equipmentid' AND '$to_do_date' BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-99-99')"))['id'];
	if($file = fopen($_FILES['import_file']['tmp_name'],'r')) {
		$titles = fgetcsv($file);
		$duplicates = [];
		$headings = [];
		$tables = [];
		$id_heading = '';
		$group_heading = '';
		$ticket_import_filters = get_config($dbc, 'ticket_import_filters');
		if($businessid > 0) {
			$template = array_filter(explode('#*#',get_config($dbc, 'ticket_import_'.$businessid)));
			if(count($template) > 0) {
				foreach($titles as $i => $name) {
					foreach($template as $temp_line) {
						$temp_line = explode('-*-',$temp_line);
						if($temp_line[0] == 'ticket_identifier' && $temp_line[1] == $name) {
							$id_heading = $i;
						} else if($temp_line[0] == 'ticket_grouping' && $temp_line[1] == $name) {
							$group_heading = $i;
						} else if($temp_line[1] == $name && $titles[$i] != $name) {
							$titles[] = $temp_line[0].'#*#'.$i;
						} else if($temp_line[1] == $name) {
							$titles[$i] = $temp_line[0];
						}
					}
				}
			}
		}
		foreach($titles as $name) {
			$name = explode('#*#',$name);
			$field = ticket_field_name($name[0], 1);
			$headings[] = $field[0][2].($name[1] > 0 ? '#*#'.$name[1] : '');
			$tables[] = $field[0][3];
		}
		if($projectid > 0 && !in_array('projectid',$headings)) {
			$headings[] = 'projectid';
		}
		if($businessid > 0 && !in_array('businessid',$headings)) {
			$headings[] = 'businessid';
		}
		if($ticket_type != '' && !in_array('ticket_type',$headings)) {
			$headings[] = 'ticket_type';
		}
		if($equipmentid > 0 && $to_do_date != '' && !in_array('equipment_assignmentid',$headings)) {
			$headings[] = 'equipment_assignmentid';
		}
		$global_headings = $headings;
		$ticket_import_list = [];
		while($ticket = fgetcsv($file)) {
			$ticket_import_list[] = $ticket;
		}
		fclose($file);
		foreach($ticket_import_list as $ticket) {
			$ticketid = 0;
			$table_list = [];
			$last_updated_time = '';
			$headings = $global_headings;
			$history = '';
			foreach($headings as $col => $title) {
				if($title == '') {
					unset($headings[$col]);
				} else if($tables[$col] != '' && $tables[$col] != 'tickets' && explode('#*#',$title)[1] > 0) {
					$title = explode('#*#',$title);
					unset($headings[$col]);
					$col = $title[1];
					$title = $title[0];
					$counter = 0;
					while(!empty($table_list[$tables[$col].'#'.$counter][$title])) {
						$counter++;
					}
					if(empty($ticket[$col])) {
						$ticket[$col] = '##EMPTY_PLACEHOLDER##';
					}
					$table_list[$tables[$col].'#'.$counter][$title] = $ticket[$col];
				} else if($tables[$col] != '' && $tables[$col] != 'tickets') {
					unset($headings[$col]);
					$counter = 0;
					while(!empty($table_list[$tables[$col].'#'.$counter][$title])) {
						$counter++;
					}
					if(empty($ticket[$col])) {
						$ticket[$col] = '##EMPTY_PLACEHOLDER##';
					}
					$table_list[$tables[$col].'#'.$counter][$title] = $ticket[$col];
				} else if($title == 'location') {
					$siteid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `site_name`='".$ticket[$col]."' AND `deleted`=0 AND IFNULL(`site_name`,'') != '' AND `category`='Sites'"))['contactid'];
				} else if($title == 'businessid_name') {
					if($ticket[$col] != '') {
						$name = encryptIt($ticket[$col]);
						$businessid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `name`='$name'"))['contactid'];
						if(!($businessid > 0)) {
							mysqli_query($dbc, "INSERT INTO `contacts` (`category`,`name`) VALUES ('".BUSINESS_CAT."','$name')");
							$businessid = mysqli_insert_id($dbc);
						}
					}
					unset($headings[$col]);
					if(!in_array('businessid', $headings)) {
						$headings[] = 'businessid';
					}
				} else if($title == 'clientid_name') {
					if($ticket[$col] != '') {
						$first_name = encryptIt(explode(' ',$ticket[$col])[0]);
						$last_name = encryptIt(explode(' ',$ticket[$col])[1]);
						$clientid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `first_name`='$first_name' AND `last_name`='$last_name'"))['contactid'];
						if(!($clientid > 0)) {
							mysqli_query($dbc, "INSERT INTO `contacts` (`category`,`first_name`,`last_name`) VALUES ('$client_cat','$first_name','$last_name')");
							$clientid = mysqli_insert_id($dbc);
						}
					}
					unset($headings[$col]);
					if(!in_array('clientid', $headings)) {
						$headings[] = 'clientid';
					}
				} else if($title == 'contactid_name') {
					if($ticket[$col] != '') {
						$first_name = encryptIt(explode(' ',$ticket[$col])[0]);
						$last_name = encryptIt(explode(' ',$ticket[$col])[1]);
						$contactid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `first_name`='$first_name' AND `last_name`='$last_name'"))['contactid'];
						if(!($contactid > 0)) {
							mysqli_query($dbc, "INSERT INTO `contacts` (`category`,`first_name`,`last_name`) VALUES ('$client_cat','$first_name','$last_name')");
							$contactid = mysqli_insert_id($dbc);
						}
					}
					unset($headings[$col]);
					if(!in_array('contactid', $headings)) {
						$headings[] = 'contactid';
					}
				} else if($title == 'agentid_name') {
					if($ticket[$col] != '') {
						$name = encryptIt($ticket[$col]);
						$agentid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `name`='$name'"))['contactid'];
						if(!($agentid > 0)) {
							mysqli_query($dbc, "INSERT INTO `contacts` (`category`,`name`) VALUES ('$agent_cat','$name')");
							$agentid = mysqli_insert_id($dbc);
						}
					}
					unset($headings[$col]);
					if(!in_array('agentid', $headings)) {
						$headings[] = 'agentid';
					}
				} else if($title == 'last_updated_date') {
					$last_updated_time = $ticket[$col].$last_updated_time;
					unset($headings[$col]);
					unset($ticket[$col]);
				} else if($title == 'last_updated_time') {
					$last_updated_time = $last_updated_time.$ticket[$col];
				} else if(($title != 'equipment_assignmentid' || !($_POST['assignid'] > 0) || $_POST['to_do_date'] == '') && ($title != 'projectid' || !($_POST['projectid'] > 0)) && ($title != 'businessid' || !($_POST['businessid'] > 0)) && ($title != 'ticket_type' || empty($_POST['ticket_type'])) && ($title != 'businessid' || $ticket[$col] > 0) && ($title != 'equipmentid' || !($_POST['assignid'] > 0)) && ($title != 'to_do_date' || $_POST['to_do_date'] != '')) {
					$$title = $ticket[$col];
				}
				$id_value = '';
				if($id_heading == $col && $id_heading != '' && $ticket[$col] != '') {
					$id_value = filter_var($ticket[$col],FILTER_SANITIZE_STRING);
					$sql = "SELECT `ticketid` FROM `tickets` WHERE `deleted`=0 AND `status` NOT IN ('Archive','Archived') AND (`ticketid`='$id_value' OR `ticket_label`='$id_value')";
					$ticketid = $dbc->query($sql);
					echo "<!--Identifier: $sql-->";
					if($ticketid->num_rows > 0) {
						$ticketid = $ticketid->fetch_assoc()['ticketid'];
					} else if($ticket_import_filters == 'ticketid_valid') {
						$row_skipped++;
						continue 2;
					} else {
						$ticketid = 0;
					}
				} else if($id_heading == $col && $id_heading != '' && $ticket_import_filters == 'ticketid') {
					echo '<!--Row Skipped-->';
					$row_skipped++;
					continue 2;
				} else if($group_heading == $col && $group_heading != '' && $ticket[$col] != '' && !($ticketid > 0)) {
					$id_value = filter_var($ticket[$col],FILTER_SANITIZE_STRING);
					$sql = "SELECT `ticketid` FROM `tickets` WHERE `deleted`=0 AND `status` NOT IN ('Archive','Archived') AND `$title`='$id_value'";
					$ticketid = $dbc->query($sql);
					echo "<!--Group Identifier: $sql-->";
					if($ticketid->num_rows > 0) {
						$ticketid = $ticketid->fetch_assoc()['ticketid'];
					} else if($ticket_import_filters == 'ticket_group' && $id_value == '') {
						$row_skipped++;
						continue 2;
					} else {
						$ticketid = 0;
					}
				}
			}
			if($ticketid > 0) {
				$sql = "UPDATE `tickets` SET ";
				$columns = [];
				foreach($headings as $var) {
					if($var != 'UNKNOWN') {
						$columns[] = "`".filter_var($var,FILTER_SANITIZE_STRING)."`='".filter_var(htmlentities($$var),FILTER_SANITIZE_STRING)."'";
					}
				}
				$history .= "\nEdited using import function by $current_user";
				$columns[] = "`history`=CONCAT(IFNULL(`history`,''),'\nEdited using import function by $current_user')";
				$sql .= implode(',',$columns)." WHERE `ticketid`='$ticketid'";
			} else {
				$sql = "INSERT INTO `tickets` (";
				$columns = [];
				$values = [];
				foreach($headings as $var) {
					$columns[] = "`".filter_var($var,FILTER_SANITIZE_STRING)."`";
					$values[] = "'".filter_var(htmlentities($$var),FILTER_SANITIZE_STRING)."'";
				}
				$columns[] = '`history`';
				$values[] = "'".TICKET_NOUN." created by ".$current_user."'";
				$history .= "\nImported by $current_user";
				$columns[] = '`created_by`';
				$values[] = "'".$_SESSION['contactid']."'";
				$columns[] = '`created_date`';
				$values[] = "'".$current_date."'";
				if(!in_array('status', $headings)) {
					$columns[] = '`status`';
					$values[] = "'".get_config($dbc, 'ticket_status')."'";
				}
				$sql .= implode(',',$columns).") VALUES (".implode(',',$values).")";
			}
			$row++;
			$row_count++;
			if(!mysqli_query($dbc, $sql)) {
				echo "Error reading line $row. <!--$sql--><br />";
				$row_count--;
			} else {
				echo "<!--Main SQL: $sql-->";
				if($dbc->insert_id > 0) {
					$ticketid = $dbc->insert_id;
				}
			}
			if($ticketid > 0) {
				foreach($table_list as $table_name => $table) {
					$table_name = explode('#', $table_name)[0];
					$columns = ['ticketid'];
					$values = [$ticketid];
					$sub_cols = [];
					$sub_vals = [];
					$qty = 1;
					foreach($table as $column => $value) {
						if($value == '##EMPTY_PLACEHOLDER##') {
							$value = '';
						}
						$columns[] = $column;
						$values[] = filter_var($value,FILTER_SANITIZE_STRING);
					}
					if($table_name == 'ticket_attached' && in_array_any(['po_line','qty','name','received'],$columns)) {
						echo "<!--$row: UPDATE `tickets` SET `unlocked_tabs`=CONCAT(IFNULL(`unlocked_tabs`,''),',ticket_inventory_general') WHERE `ticketid`='$ticketid'-->";
						$dbc->query("UPDATE `tickets` SET `unlocked_tabs`=CONCAT(IFNULL(`unlocked_tabs`,''),',ticket_inventory_general') WHERE `ticketid`='$ticketid'");
					}
					if($table_name == 'ticket_attached' && in_array('po_line',$columns)) {
						$columns[] = 'src_table';
						$values[] = 'inventory_general';
						$columns[] = 'description';
						$values[] = 'import';
						echo "<!--$row: UPDATE `tickets` SET `unlocked_tabs`=CONCAT(IFNULL(`unlocked_tabs`,''),',ticket_inventory_general') WHERE `ticketid`='$ticketid'-->";
						$dbc->query("UPDATE `tickets` SET `unlocked_tabs`=CONCAT(IFNULL(`unlocked_tabs`,''),',ticket_inventory_general') WHERE `ticketid`='$ticketid'");
					}
					if($table_name == 'ticket_attached' && in_array('po_line_detail',$columns)) {
						$key = array_search('po_line_detail',$columns);
						$po_line = $values[$key];
						unset($columns[$key]);
						unset($values[$key]);
						$sub_cols[] = 'po_line';
						$sub_vals[] = $po_line;
						$columns[] = 'src_table';
						$values[] = 'inventory_general';
						$columns[] = 'description';
						$values[] = 'import';
					}
					if($table_name == 'ticket_attached' && in_array('name',$columns)) {
						$key = array_search('name',$columns);
						$inv_cur_row = $dbc->query("SELECT `inventoryid`,`ticket_attached`.`id` FROM `inventory` LEFT JOIN (SELECT `ticket_attached`.`id`,`ticket_attached`.`ticketid`, `ticket_attached`.`item_id`, `ticket_attached`.`po_line`,`tickets`.`purchase_order`,`tickets`.`customer_order_num` FROM `ticket_attached` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_attached`.`deleted`=0 AND `ticket_attached`.`src_table`='inventory') `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` WHERE `inventory`.`name` LIKE '{$values[$key]}' AND `ticket_attached`.`po_line`='$po_line' AND `ticket_attached`.`ticketid`='$ticketid' AND `ticket_attached`.`purchase_order` LIKE '%$purchase_order%' AND `ticket_attached`.`customer_order_num` LIKE '%$customer_order_num%'");
						$inv_id = 0;
						if($inv_cur_row->num_rows == 0) {
							echo "<!--$row: INSERT INTO `inventory` (`name`) VALUES ('{$values[$key]}')-->";
							$dbc->query("INSERT INTO `inventory` (`name`) VALUES ('{$values[$key]}')");
							$inv_id = $dbc->insert_id;
						} else {
							$inv_cur_row = $inv_cur_row->fetch_assoc();
							$sub_cols[] = 'id';
							$sub_vals[] = $inv_cur_row['id'];
							$inv_id = $inv_cur_row['inventoryid'];
						}
						unset($columns[$key]);
						unset($values[$key]);
						$sub_cols[] = 'item_id';
						$sub_vals[] = $inv_id;
						$columns[] = 'position';
						$values[] = 1;
						$history .= "\nImported Inventory Item #".$dbc->insert_id.": {$values[$key]} by $current_user";
					}
					if($table_name == 'ticket_attached' && in_array('qty',$columns)) {
						$key = array_search('qty',$columns);
						$qty = $values[$key];
						unset($columns[$key]);
						unset($values[$key]);
						$sub_cols[] = 'qty';
						$sub_vals[] = $qty;
						$inv_id = $sub_vals[array_search('item_id',$sub_cols)];
						echo "<!--$row: UPDATE `inventory` SET `expected_inventory`='$qty' WHERE `inventoryid`='$inv_id'-->";
						$dbc->query("UPDATE `inventory` SET `expected_inventory`='$qty' WHERE `inventoryid`='$inv_id'");
					}
					if($table_name == 'ticket_attached' && in_array('received',$columns)) {
						$key = array_search('received',$columns);
						$received = $values[$key];
						unset($columns[$key]);
						unset($values[$key]);
						$sub_cols[] = 'received';
						$sub_vals[] = $received;
						$inv_id = $sub_vals[array_search('item_id',$sub_cols)];
						echo "<!--$row: UPDATE `inventory` SET `quantity`='$received' WHERE `inventoryid`='$inv_id'-->";
						$dbc->query("UPDATE `inventory` SET `quantity`='$received' WHERE `inventoryid`='$inv_id'");
						$dbc->query("INSERT INTO `inventory_change_log` (`inventoryid`,`contactid`,`location_of_change`,`old_inventory`,`changed_inventory`,`new_inventory`,`date_time`) SELECT `inventoryid`,'{$_SESSION['contactid']}','".TICKET_TILE." Tile',0,'$qty','$qty' FROM `inventory` WHERE `inventoryid`='$inv_id'");
					}
					if(count(array_filter($columns, function($v) { return !in_array($v, ['ticketid','src_table','description','position']); })) > 0) {
						$sql = "INSERT INTO `$table_name` (`".implode('`,`',$columns)."`) VALUES ('".implode("','",$values)."')";
						echo "<!--General $row: $sql-->";
						if(!mysqli_query($dbc, $sql)) {
							echo "Error reading Detail for line $row. <!--$sql--><br />";
						}
						$line_id = $dbc->insert_id;
					} else if($table_name == 'ticket_attached') {
						$line_id = $dbc->query("SELECT `id` FROM `$table_name` WHERE `ticketid`='$ticketid' AND `src_table`='inventory_general'")->fetch_assoc()['id'];
						if(!($line_id > 0)) {
							$sql = "INSERT INTO `$table_name` (`".implode('`,`',$columns)."`) VALUES ('".implode("','",$values)."')";
							echo "<!--General $row: $sql-->";
							if(!mysqli_query($dbc, $sql)) {
								echo "Error reading Detail for line $row. <!--$sql--><br />";
							}
							$line_id = $dbc->insert_id;
						}
						$dbc->query("UPDATE `ticket_attached` SET `description`='import',`position`='1' WHERE `id`='$line_id'");
					}
					if(count($sub_cols) > 0) {
						$key = array_search('id',$sub_cols);
						if($key > 0) {
							$id_line = $sub_vals[$key];
							unset($sub_cols[$key]);
							unset($sub_vals[$key]);
							$sub_sets = [];
							foreach($sub_cols as $sub_i => $sub_field) {
								$sub_sets[] = "`$sub_field`='{$sub_vals[$sub_i]}'";
							}
							$sql = "UPDATE `ticket_attached` SET ".implode(',',$sub_sets).", `line_id`='$line_id' WHERE `id`='$id_line'";
						} else {
							$sql = "INSERT INTO `ticket_attached` (`ticketid`,`src_table`,`line_id`,`".implode('`,`',$sub_cols)."`) VALUES ('$ticketid','inventory','".$line_id."','".implode("','",$sub_vals)."')";
						}
						$history .= "\nImported detail by $current_user";
						echo "<!--Detail $row: $sql-->";
						if(!mysqli_query($dbc, $sql)) {
							echo "Error reading Detail for line $row. <!--$sql--><br />";
						}
					}
				}
			}
		}
		echo "<h2>Successfully imported $row_count records.";
		if($row_skipped > 0) {
			echo "<br /><em>Per configuration, skipped $row_skipped records.</em>";
		}
		echo "</h2>";
	} else {
		echo "<h1>Unable to read from selected file.</h1>";
	}
	if(!empty($_GET['from'])) {
	}
}
$value_config = ','.mysqli_fetch_assoc(mysqli_query($dbc,"SELECT tickets FROM field_config"))['tickets'].',';
$projectid = $_GET['projectid'];
$equipmentid = $_GET['equipmentid'];
$to_do_date = $_GET['to_do_date'];
$ticket_types = array_merge(['ALL' => 'All '.TICKET_TILE],$ticket_tabs);
$db_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `tickets_dashboard` FROM `field_config`"))['tickets_dashboard'];
if($db_config == '') {
	$db_config = 'Business,Contact,Heading,Services,Status,Deliverable Date';
}
$db_config = explode(',',$db_config); ?>

<form class="form-horizontal margin-vertical margin-horizontal" action="" method="POST" enctype="multipart/form-data">
	<h2>Import / Export <?= TICKET_TILE ?></h2><?php
    $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='tickets_import_export'"));
    $note = $notes['note'];
    if ( !empty($note) ) { ?>
        <div class="notice popover-examples">
            <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            <?= $note ?></div>
            <div class="clearfix"></div>
        </div><?php
    } ?>
	<!--
    <div class="notice double-gap-bottom popover-examples">
		<div class="col-sm-1 notice-icon"><img src="<?php //WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
		<div class="col-sm-11"><span class="notice-name">NOTE:</span>
		You can use this page to export, mass edit, and import <?php //TICKET_TILE ?>. To edit, first export your <?php //TICKET_TILE ?>, edit them, and then import them again. To just import, use the template below. Do not change the titles of the columns, as they are used to determine what is in the column, and do not remove the ticketid column.</div>
		<div class="clearfix"></div>
	</div>
    -->
	<div class="form-group">
		<label class="col-sm-4">Export <?= TICKET_TILE ?></label>
		<div class="col-sm-8">
			<?php foreach($ticket_types as $type => $type_name) {
				if($_GET['tile_name'] == '' || $_GET['tile_name'] == $type) { ?>
					<button name="export" value="<?= $type ?>" type="submit" class="btn brand-btn">Export <?= $type_name ?></button>
				<?php }
			} ?>
			<button name="import" value="template" type="submit" class="btn brand-btn">Download Import Template</button>
		</div>
	</div>
	<?php if(in_array('Export Business', $db_config)) { ?>
		<div class="form-group">
			<label class="col-sm-4">Attach to <?= BUSINESS_CAT ?>:<br><i>(NOTE: All <?= TICKET_TILE ?> in this import will be attached to this <?= BUSINESS_CAT ?> if a <?= BUSINESS_CAT ?> is selected.)</i></label>
			<div class="col-sm-8">
				<select name="businessid" data-placeholder="Select <?= BUSINESS_CAT ?>" class="chosen-select-deselect form-control">
					<option></option>
				  	<?php if(get_config($dbc, 'ticket_import_bus') == 'template_only') {
						$business_filter = '`general_configuration`.`value` IS NOT NULL AND';
					}
					foreach(sort_contacts_query(mysqli_query($dbc,"SELECT `contacts`.`contactid`, `contacts`.`name`, `projectid` FROM `contacts` LEFT JOIN `project` ON `contacts`.`contactid`=`project`.`businessid` LEFT JOIN `general_configuration` ON `general_configuration`.`name`=CONCAT('ticket_import_',`contacts`.`contactid`) WHERE $business_filter `contacts`.`deleted`=0 AND `contacts`.`status`>0 AND `contacts`.`category`='".BUSINESS_CAT."'")) as $row) {
						echo "<option ".($row['projectid'] == $projectid && $projectid > 0 ? 'selected' : '')." value='".$row['contactid']."' data-project='".$row['projectid']."'>".$row['name'].'</option>';
					} ?>
				</select>
			</div>
		</div>
	<?php } ?>
	<?php if(in_array('Export Project', $db_config)) { ?>
		<div class="form-group">
			<label class="col-sm-4">Attach to <?= PROJECT_NOUN ?>:<br><i>(NOTE: All <?= TICKET_TILE ?> in this import will be attached to this <?= PROJECT_NOUN ?> if a <?= PROJECT_NOUN ?> is selected.)</i></label>
			<div class="col-sm-8">
				<select name="projectid" data-placeholder="Select <?= PROJECT_NOUN ?>" class="chosen-select-deselect form-control">
					<option></option>
				  	<?php $query = mysqli_query($dbc,"SELECT projectid, projecttype, project_name, businessid, clientid, status FROM project WHERE deleted=0 order by project_name");
					while($row = mysqli_fetch_array($query)) {
						echo "<option ".($row['projectid'] == $projectid ? 'selected' : '')." data-business='".$row['businessid']."' value='".$row['projectid']."'>".get_project_label($dbc,$row).'</option>';
					} ?>
				</select>
			</div>
		</div>
	<?php } ?>
	<?php if(in_array('Export Equipment',$db_config)) { ?>
		<?php $query = mysqli_query($dbc,"SELECT `ea`.`equipmentid`,CONCAT(IFNULL(`e`.`category`,''),': ',IFNULL(`e`.`make`,''),' ',IFNULL(`e`.`model`,''),' ',IFNULL(`e`.`label`,''),' ',IFNULL(`e`.`unit_number`,'')) `label` FROM `equipment_assignment` `ea` LEFT JOIN `equipment` `e` ON `ea`.`equipmentid`=`e`.`equipmentid` WHERE `ea`.`end_date` >= DATE(NOW()) AND `ea`.`deleted`=0 and `e`.`deleted`=0 GROUP BY `equipmentid`");
		if($query->num_rows > 0) { ?>
			<div class="form-group">
				<label class="col-sm-4">Attach to Equipment:<br><i>(NOTE: All <?= TICKET_TILE ?> will be assigned to this Equipment.)</i></label>
				<div class="col-sm-8">
					<select name="assignid" data-placeholder="Select Equipment" class="chosen-select-deselect form-control">
						<option></option>
						<?php while($row = mysqli_fetch_array($query)) {
							echo "<option ".($equipmentid == $row['equipmentid'] ? 'selected' : '')." value='".$row['equipmentid']."'>".$row['label'].'</option>';
						} ?>
					</select>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if(in_array('Export Date',$db_config)) { ?>
		<div class="form-group">
			<label class="col-sm-4">Assign to Date:<br><i>(NOTE: All <?= TICKET_TILE ?> will be assigned this Date.)</i></label>
			<div class="col-sm-8">
				<input type="text" class="form-control datepicker" name="to_do_date" value="<?= $to_do_date ?>">
			</div>
		</div>
	<?php } ?>
	<?php if(count($ticket_types) > 1) { ?>
		<div class="form-group">
			<label class="col-sm-4">Import as <?= TICKET_NOUN ?> Tab:<br><i>(NOTE: All <?= TICKET_TILE ?> in this import will be imported as this <?= TICKET_NOUN ?> Type if a <?= TICKET_NOUN ?> Type is selected.)</i></label>
			<div class="col-sm-8">
				<select name="ticket_type" data-placeholder="Select <?= TICKET_NOUN ?> Tab" class="chosen-select-deselect form-control">
					<option></option>
				  	<?php $query = mysqli_query($dbc,"SELECT projectid, projecttype, project_name, businessid, clientid, status FROM project WHERE deleted=0 order by project_name");
					foreach($ticket_types as $type => $type_name) {
						echo "<option value='".$type."'>".$type_name.'</option>';
					} ?>
				</select>
			</div>
		</div>
	<?php } ?>
	<div class="form-group">
		<label class="col-sm-4">Select File to Import:</label>
		<div class="col-sm-8">
			<input type="file" name="import_file" class="form-control">
		</div>
	</div>
	<a href="<?= !empty($_GET['from']) ? $_GET['from'] : '?tile_name='.$_GET['tile_name'] ?>" class="btn brand-btn">Back</a>
	<button name="import" value="file" type="submit" class="btn brand-btn pull-right">Import <?= TICKET_TILE ?></button>
	<!--<button name="import" value="manual" type="submit" class="btn brand-btn pull-right">Manual Field Import</button>-->
	<script>
	$('[name=businessid]').change(function() {
		var business = this.value;
		$('[name=projectid]').find('option').each(function() {
			if($(this).data('business') == business) {
				$(this).show();
			} else if(business > 0 && $(this).data('business') > 0) {
				$(this).hide();
			} else {
				$(this).show();
			}
		});
		$('[name=projectid]').trigger('change.select2');
	});
	$('[name=projectid]').change(function() {
		if($(this).find('option:selected').data('business') > 0) {
			$('[name=businessid]').val($(this).find('option:selected').data('business')).trigger('change.select2');
		}
	});
	</script>
</form>
<div class="clearfix"></div>
