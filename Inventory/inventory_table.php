<?php
// Display Pager
$rowsPerPage = ITEMS_PER_PAGE;
if($_GET['no_search'] == 'true') {
	$rowsPerPage = 9999;
}
$pageNum = 1;

if(isset($_GET['page'])) {
	$pageNum = $_GET['page'];
}

$offset = ($pageNum - 1) * $rowsPerPage;
$inventory_sort = get_config($dbc, 'inventory_sort');

if($_GET['category'] == 'Top' && isset($_GET['search_category'])) {
	$_GET['search_inventory'] = $_GET['search_category'];
	unset($_GET['search_category']);
}
if(isset($_GET['search_category']) && !isset($_GET['display_all_inventory'])) {
	$inventory = filter_var($_GET['search_category'],FILTER_SANITIZE_STRING);
	$category = filter_var($_GET['category'],FILTER_SANITIZE_STRING);
	foreach(explode('#*#',get_config($dbc, 'inventory_tabs')) as $each_cat) {
		if($category == preg_replace('/[^a-z]/','',strtolower($each_cat))) {
			$category = filter_var($each_cat,FILTER_SANITIZE_STRING);
		}
	}
	$query_check_credentials = "SELECT `inventory`.*, `ticket_attached`.`po_line`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_number`, IFNULL(NULLIF(`ticket_attached`.`position`,''),`tickets`.`customer_order_num`) `customer_order`, `tickets`.`ticket_label` FROM `inventory` LEFT JOIN (SELECT * FROM `ticket_attached` WHERE `src_table`='inventory' AND `deleted`=0) `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `inventory`.`deleted` = 0 AND `inventory`.`category`='$category' AND (`inventory`.`name` LIKE '%" . $inventory . "%' OR `inventory`.`code` LIKE '%" . $inventory . "%' OR `inventory`.`part_no` LIKE '%" . $inventory . "%' OR `inventory`.`sub_category` LIKE '%" . $inventory . "%' OR `inventory`.`description` LIKE '%" . $inventory . "%' OR `inventory`.`purchase_cost` LIKE '%" . $inventory . "%' OR `inventory`.`min_bin` LIKE '%" . $inventory . "%' OR `inventory`.`date_of_purchase` LIKE '%" . $inventory . "%' OR `inventory`.`pallet` LIKE '%" . $inventory . "%' OR `tickets`.`ticket_label` LIKE '%" . $inventory . "%' OR `tickets`.`purchase_order` LIKE '%" . $inventory . "%' OR `ticket_attached`.`po_num` LIKE '%" . $inventory . "%' OR `tickets`.`customer_order_num` LIKE '%" . $inventory . "%' OR `ticket_attached`.`position` LIKE '%" . $inventory . "%') $match_business GROUP BY `inventory`.`inventoryid` ORDER BY ".($inventory_sort == 'po_line' ? "LPAD(IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`),100,0), LPAD(`ticket_attached`.`po_line`,100,0), " : '')."IF(`inventory`.`category` RLIKE '^[a-z]', 1, 2), `inventory`.`category`, IF(`inventory`.`name` RLIKE '^[a-z]', 1, 2), `inventory`.`name` LIMIT $offset, $rowsPerPage";
	$query_count = "SELECT COUNT(*) numrows FROM (SELECT `inventory`.*, `ticket_attached`.`po_line`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_number`, `tickets`.`ticket_label` FROM `inventory` LEFT JOIN (SELECT * FROM `ticket_attached` WHERE `src_table`='inventory' AND `deleted`=0) `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `inventory`.`deleted` = 0 AND `inventory`.`category`='$category' AND (`inventory`.`name` LIKE '%" . $inventory . "%' OR `inventory`.`code` LIKE '%" . $inventory . "%' OR `inventory`.`part_no` LIKE '%" . $inventory . "%' OR `inventory`.`sub_category` LIKE '%" . $inventory . "%' OR `inventory`.`description` LIKE '%" . $inventory . "%' OR `inventory`.`purchase_cost` LIKE '%" . $inventory . "%' OR `inventory`.`min_bin` LIKE '%" . $inventory . "%' OR `inventory`.`date_of_purchase` LIKE '%" . $inventory . "%' OR `inventory`.`pallet` LIKE '%" . $inventory . "%' OR `tickets`.`ticket_label` LIKE '%" . $inventory . "%' OR `tickets`.`purchase_order` LIKE '%" . $inventory . "%' OR `ticket_attached`.`po_num` LIKE '%" . $inventory . "%' OR `tickets`.`customer_order_num` LIKE '%" . $inventory . "%' OR `ticket_attached`.`position` LIKE '%" . $inventory . "%') $match_business GROUP BY `inventory`.`inventoryid`) `inventory`";
} else if(isset($_GET['search_inventory']) && !isset($_GET['display_all_inventory'])) {
	$inventory = filter_var($_GET['search_inventory'],FILTER_SANITIZE_STRING);
	$query_check_credentials = "SELECT `inventory`.*, `ticket_attached`.`po_line`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_number`, IFNULL(NULLIF(`ticket_attached`.`position`,''),`tickets`.`customer_order_num`) `customer_order`, `tickets`.`ticket_label` FROM `inventory` LEFT JOIN (SELECT * FROM `ticket_attached` WHERE `src_table`='inventory' AND `deleted`=0) `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `inventory`.`deleted` = 0 AND (`inventory`.`name` LIKE '%" . $inventory . "%' OR `inventory`.`code` LIKE '%" . $inventory . "%' OR `inventory`.`part_no` LIKE '%" . $inventory . "%' OR `inventory`.`category` = '$inventory' OR `inventory`.`sub_category` LIKE '%" . $inventory . "%' OR `inventory`.`description` LIKE '%" . $inventory . "%' OR `inventory`.`purchase_cost` LIKE '%" . $inventory . "%' OR `inventory`.`min_bin` LIKE '%" . $inventory . "%' OR `inventory`.`date_of_purchase` LIKE '%" . $inventory . "%' OR `inventory`.`pallet` LIKE '%" . $inventory . "%' OR `tickets`.`ticket_label` LIKE '%" . $inventory . "%' OR `tickets`.`purchase_order` LIKE '%" . $inventory . "%' OR `ticket_attached`.`po_num` LIKE '%" . $inventory . "%' OR `tickets`.`customer_order_num` LIKE '%" . $inventory . "%' OR `ticket_attached`.`position` LIKE '%" . $inventory . "%') $match_business GROUP BY `inventory`.`inventoryid` ORDER BY ".($inventory_sort == 'po_line' ? "LPAD(IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`),100,0), LPAD(`ticket_attached`.`po_line`,100,0), " : '')."IF(`inventory`.`category` RLIKE '^[a-z]', 1, 2), `inventory`.`category`, IF(`inventory`.`name` RLIKE '^[a-z]', 1, 2), `inventory`.`name` LIMIT $offset, $rowsPerPage";
	$query_count = "SELECT COUNT(*) numrows FROM (SELECT `inventory`.*, `ticket_attached`.`po_line`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_number`, `tickets`.`ticket_label` FROM `inventory` LEFT JOIN (SELECT * FROM `ticket_attached` WHERE `src_table`='inventory' AND `deleted`=0) `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `inventory`.`deleted` = 0 AND (`inventory`.`name` LIKE '%" . $inventory . "%' OR `inventory`.`code` LIKE '%" . $inventory . "%' OR `inventory`.`part_no` LIKE '%" . $inventory . "%' OR `inventory`.`category` = '$inventory' OR `inventory`.`sub_category` LIKE '%" . $inventory . "%' OR `inventory`.`description` LIKE '%" . $inventory . "%' OR `inventory`.`purchase_cost` LIKE '%" . $inventory . "%' OR `inventory`.`min_bin` LIKE '%" . $inventory . "%' OR `inventory`.`date_of_purchase` LIKE '%" . $inventory . "%' OR `inventory`.`pallet` LIKE '%" . $inventory . "%' OR `tickets`.`ticket_label` LIKE '%" . $inventory . "%' OR `tickets`.`purchase_order` LIKE '%" . $inventory . "%' OR `ticket_attached`.`po_num` LIKE '%" . $inventory . "%' OR `tickets`.`customer_order_num` LIKE '%" . $inventory . "%' OR `ticket_attached`.`position` LIKE '%" . $inventory . "%') $match_business GROUP BY `inventory`.`inventoryid`) `inventory`";
} else {
	if(isset($_GET['order_list']) && $currentlist == 'on' && $inventoryidorder !== '') {
		$query_check_credentials = "SELECT `inventory`.*, `ticket_attached`.`po_line`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_number`, IFNULL(NULLIF(`ticket_attached`.`position`,''),`tickets`.`customer_order_num`) `customer_order`, `tickets`.`ticket_label` FROM `inventory` LEFT JOIN (SELECT * FROM `ticket_attached` WHERE `src_table`='inventory' AND `deleted`=0) `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `inventory`.`deleted` = 0 AND `inventory`.`inventoryid` IN (".$inventoryidorder.") $match_business GROUP BY `inventory`.`inventoryid` ORDER BY ".($inventory_sort == 'po_line' ? "LPAD(IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`),100,0), LPAD(`ticket_attached`.`po_line`,100,0), " : '')."IF(`inventory`.`category` RLIKE '^[a-z]', 1, 2), `inventory`.`category`, IF(`inventory`.`name` RLIKE '^[a-z]', 1, 2), `inventory`.`name` LIMIT $offset, $rowsPerPage";
		$query_count = "SELECT COUNT(*) numrows FROM (SELECT `inventory`.*, `ticket_attached`.`po_line`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_number`, `tickets`.`ticket_label` FROM `inventory` LEFT JOIN (SELECT * FROM `ticket_attached` WHERE `src_table`='inventory' AND `deleted`=0) `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `inventory`.`deleted` = 0 AND `inventory`.`inventoryid` IN (".$inventoryidorder.") $match_business GROUP BY `inventory`.`inventoryid`) `inventory`";
	} else if(((empty($_GET['category'])) || ($_GET['category'] == 'Top')) && empty($_GET['purchase_order'])) {
		$query_check_credentials = "SELECT `inventory`.*, `ticket_attached`.`po_line`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_number`, IFNULL(NULLIF(`ticket_attached`.`position`,''),`tickets`.`customer_order_num`) `customer_order`, `tickets`.`ticket_label` FROM `inventory` LEFT JOIN (SELECT * FROM `ticket_attached` WHERE `src_table`='inventory' AND `deleted`=0) `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `inventory`.`deleted` = 0 $match_business GROUP BY `inventory`.`inventoryid` ORDER BY ".($inventory_sort == 'po_line' ? "LPAD(IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`),100,0), LPAD(`ticket_attached`.`po_line`,100,0), " : '')."IF(`inventory`.`category` RLIKE '^[a-z]', 1, 2), `inventory`.`category`, IF(`inventory`.`name` RLIKE '^[a-z]', 1, 2), `inventory`.`name` DESC LIMIT 25";
		$query_count = "SELECT 25 numrows";
	} else {
		$purchase_order = filter_var($_GET['purchase_order'],FILTER_SANITIZE_STRING);
		$category = $_GET['category'];
		$category = (!empty($purchase_order) && ($category=='purchaseorders' || $category=='customerorders' || $category=='pallet')) ? 'Top' : $category;
		$url_po = isset($_GET['po']) ? strtolower(trim($_GET['po'])) : '';
		if($category == 'dispall_31V2irt2u3e5S3s1f2ADe3_31') {
			//Display All option from category select menu
			$query_check_credentials = "SELECT `inventory`.*, `ticket_attached`.`po_line`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_number`, IFNULL(NULLIF(`ticket_attached`.`position`,''),`tickets`.`customer_order_num`) `customer_order`, `tickets`.`ticket_label` FROM `inventory` LEFT JOIN (SELECT * FROM `ticket_attached` WHERE `src_table`='inventory' AND `deleted`=0) `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `inventory`.`deleted` = 0 AND ('$purchase_order' = '' OR CONCAT('#*#',`tickets`.`purchase_order`,'#*#') LIKE '%#*#$purchase_order#*#%' OR `ticket_attached`.`po_num` LIKE '$purchase_order') $match_business GROUP BY `inventory`.`inventoryid` ORDER BY ".($inventory_sort == 'po_line' ? "LPAD(IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`),100,0), LPAD(`ticket_attached`.`po_line`,100,0), " : '')."IF(`inventory`.`category` RLIKE '^[a-z]', 1, 2), `inventory`.`category`, IF(`inventory`.`name` RLIKE '^[a-z]', 1, 2), `inventory`.`name` $match_business LIMIT $offset, $rowsPerPage";
			$query_count = "SELECT COUNT(*) numrows FROM (SELECT `inventory`.*, `ticket_attached`.`po_line`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_number`, `tickets`.`ticket_label` FROM `inventory` LEFT JOIN (SELECT * FROM `ticket_attached` WHERE `src_table`='inventory' AND `deleted`=0) `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `inventory`.`deleted` = 0 AND ('$purchase_order' = '' OR CONCAT('#*#',`tickets`.`purchase_order`,'#*#') LIKE '%#*#$purchase_order#*#%' OR `ticket_attached`.`po_num` LIKE '$purchase_order') $match_business GROUP BY `inventory`.`inventoryid`) `inventory`";
		} elseif ($category=='purchaseorders' ) {
			$url_ponum = filter_var($_GET['ponum'], FILTER_SANITIZE_STRING);
			$query_check_credentials = "SELECT `inventory`.*, `ticket_attached`.`po_line`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_number`, IFNULL(NULLIF(`ticket_attached`.`position`,''),`tickets`.`customer_order_num`) `customer_order`, `tickets`.`ticket_label` FROM `inventory` LEFT JOIN (SELECT * FROM `ticket_attached` WHERE `src_table`='inventory' AND `deleted`=0) `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `inventory`.`deleted` = 0 AND (CONCAT('#*#',`tickets`.`purchase_order`,'#*#') LIKE '%#*#$url_ponum#*#%' OR `ticket_attached`.`po_num` LIKE '$url_ponum') $match_business GROUP BY `inventory`.`inventoryid` ORDER BY ".($inventory_sort == 'po_line' ? "LPAD(IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`),100,0), LPAD(`ticket_attached`.`po_line`,100,0), " : '')."IF(`inventory`.`category` RLIKE '^[a-z]', 1, 2), `inventory`.`category`, IF(`inventory`.`name` RLIKE '^[a-z]', 1, 2), `inventory`.`name` $match_business LIMIT $offset, $rowsPerPage";
			$query_count = "SELECT COUNT(*) numrows FROM (SELECT `inventory`.*, `ticket_attached`.`po_line`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_number`, `tickets`.`ticket_label` FROM `inventory` LEFT JOIN (SELECT * FROM `ticket_attached` WHERE `src_table`='inventory' AND `deleted`=0) `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `inventory`.`deleted` = 0 AND (CONCAT('#*#',`tickets`.`purchase_order`,'#*#') LIKE '%#*#$url_ponum#*#%' OR `ticket_attached`.`po_num` LIKE '$url_ponum') $match_business GROUP BY `inventory`.`inventoryid`) `inventory`";
		} elseif ($category=='customerorders' ) {
			$url_conum = filter_var($_GET['conum'], FILTER_SANITIZE_STRING);
			$query_check_credentials = "SELECT `inventory`.*, `ticket_attached`.`po_line`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_number`, IFNULL(NULLIF(`ticket_attached`.`position`,''),`tickets`.`customer_order_num`) `customer_order`, `tickets`.`ticket_label` FROM `inventory` LEFT JOIN (SELECT * FROM `ticket_attached` WHERE `src_table`='inventory' AND `deleted`=0) `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `inventory`.`deleted` = 0 AND (CONCAT('#*#',`tickets`.`customer_order_num`,'#*#') LIKE '%#*#$url_conum#*#%' OR `ticket_attached`.`position` LIKE '$url_conum') $match_business GROUP BY `inventory`.`inventoryid` ORDER BY ".($inventory_sort == 'po_line' ? "LPAD(IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`),100,0), LPAD(`ticket_attached`.`po_line`,100,0), " : '')."IF(`inventory`.`category` RLIKE '^[a-z]', 1, 2), `inventory`.`category`, IF(`inventory`.`name` RLIKE '^[a-z]', 1, 2), `inventory`.`name` $match_business LIMIT $offset, $rowsPerPage";
			$query_count = "SELECT COUNT(*) numrows FROM (SELECT `inventory`.*, `ticket_attached`.`po_line`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_number`, `tickets`.`ticket_label` FROM `inventory` LEFT JOIN (SELECT * FROM `ticket_attached` WHERE `src_table`='inventory' AND `deleted`=0) `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `inventory`.`deleted` = 0 AND (CONCAT('#*#',`tickets`.`customer_order_num`,'#*#') LIKE '%#*#$url_conum#*#%' OR `ticket_attached`.`position` LIKE '$url_conum') $match_business GROUP BY `inventory`.`inventoryid`) `inventory`";
		} elseif ($category=='pallet' ) {
			$url_pallet = filter_var($_GET['pallet'], FILTER_SANITIZE_STRING);
			$query_check_credentials = "SELECT `inventory`.*, `ticket_attached`.`po_line`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_number`, IFNULL(NULLIF(`ticket_attached`.`position`,''),`tickets`.`customer_order_num`) `customer_order`, `tickets`.`ticket_label` FROM `inventory` LEFT JOIN (SELECT * FROM `ticket_attached` WHERE `src_table`='inventory' AND `deleted`=0) `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `inventory`.`deleted` = 0 AND (`inventory`.`pallet` LIKE '$url_pallet') $match_business GROUP BY `inventory`.`inventoryid` ORDER BY ".($inventory_sort == 'po_line' ? "LPAD(IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`),100,0), LPAD(`ticket_attached`.`po_line`,100,0), " : '')."IF(`inventory`.`category` RLIKE '^[a-z]', 1, 2), `inventory`.`category`, IF(`inventory`.`name` RLIKE '^[a-z]', 1, 2), `inventory`.`name` $match_business LIMIT $offset, $rowsPerPage";
			$query_count = "SELECT COUNT(*) numrows FROM (SELECT `inventory`.*, `ticket_attached`.`po_line`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_number`, `tickets`.`ticket_label` FROM `inventory` LEFT JOIN (SELECT * FROM `ticket_attached` WHERE `src_table`='inventory' AND `deleted`=0) `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `inventory`.`deleted` = 0 AND (`inventory`.`pallet` LIKE '$url_pallet') $match_business GROUP BY `inventory`.`inventoryid`) `inventory`";
		} else {
			$cat_list = explode('#*#',get_config($dbc, 'inventory_tabs'));
			foreach($cat_list as $each_cat) {
				if($category == preg_replace('/[^a-z]/','',strtolower($each_cat))) {
					$category = filter_var($each_cat,FILTER_SANITIZE_STRING);
				}
			}
			$query_check_credentials = "SELECT `inventory`.*, `ticket_attached`.`po_line`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_number`, IFNULL(NULLIF(`ticket_attached`.`position`,''),`tickets`.`customer_order_num`) `customer_order`, `tickets`.`ticket_label` FROM `inventory` LEFT JOIN (SELECT * FROM `ticket_attached` WHERE `src_table`='inventory' AND `deleted`=0) `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `inventory`.`deleted` = 0 AND ('$purchase_order' = '' OR CONCAT('#*#',`tickets`.`purchase_order`,'#*#') LIKE '%#*#$purchase_order#*#%' OR `ticket_attached`.`po_num` LIKE '$purchase_order') AND '$category' IN (`inventory`.`category`,'','Top') $match_business GROUP BY `inventory`.`inventoryid` ORDER BY ".($inventory_sort == 'po_line' ? "LPAD(IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`),100,0), LPAD(`ticket_attached`.`po_line`,100,0), " : '')."IF(`inventory`.`category` RLIKE '^[a-z]', 1, 2), `inventory`.`category`, IF(`inventory`.`name` RLIKE '^[a-z]', 1, 2), `inventory`.`name` LIMIT $offset, $rowsPerPage";
			$query_count = "SELECT COUNT(*) numrows FROM (SELECT `inventory`.*, `ticket_attached`.`po_line`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) `po_number`, `tickets`.`ticket_label` FROM `inventory` LEFT JOIN (SELECT * FROM `ticket_attached` WHERE `src_table`='inventory' AND `deleted`=0) `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `inventory`.`deleted` = 0 AND ('$purchase_order' = '' OR CONCAT('#*#',`tickets`.`purchase_order`,'#*#') LIKE '%#*#$purchase_order#*#%' OR `ticket_attached`.`po_num` LIKE '$purchase_order') AND '$category' IN (`inventory`.`category`,'','Top') $match_business GROUP BY `inventory`.`inventoryid`) `inventory`";
		}
	}
}

$result = mysqli_query($dbc, $query_check_credentials) or die(mysqli_error($dbc));

$num_rows = mysqli_num_rows($result);
if($num_rows > 0) {
	echo '<script>console.log("'.$num_rows.'")</script>';

	if(empty($_GET['category']) || $_GET['category'] == 'Top' || $_GET['category'] == 'dispall_31V2irt2u3e5S3s1f2ADe3_31') {
		//$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT inventory_dashboard FROM field_config_inventory WHERE inventory_dashboard IS NOT NULL"));
		$get_field_config = mysqli_fetch_assoc(mysqli_query ( $dbc, "SELECT `inventory_dashboard` FROM `field_config_inventory` WHERE `tab`='Top' UNION (SELECT `inventory_dashboard` FROM `field_config_inventory` WHERE `inventory_dashboard` IS NOT NULL ORDER BY IF(`tab`='Uncategorized','',IF(`tab`='','Z',`tab`)))"));
		$value_config = ','.$get_field_config['inventory_dashboard'].',';
	} else {
		$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT inventory_dashboard FROM field_config_inventory WHERE (tab='".$category."' OR tab='".$_GET['category']."') AND accordion IS NULL UNION (SELECT `inventory_dashboard` FROM `field_config_inventory` WHERE `inventory_dashboard` IS NOT NULL ORDER BY IF(`tab`='Uncategorized','',IF(`tab`='','Z',`tab`)))"));
		$value_config = ','.$get_field_config['inventory_dashboard'].',';
	}

	if($_GET['no_search'] != 'true') {
		echo '<div class="pagination_links">';
		display_pagination($dbc, $query_count, $pageNum, $rowsPerPage);
		echo '</div>';
	}
	echo "<table class='table table-bordered table-striped'>";
	echo "<thead>";
	echo "<tr ".($_GET['no_search'] == 'true' ? '' : "class='hidden-xs hidden-sm'").">";
		if (isset($_GET['order_list'])) {
			echo '<th>Include in Order List';
			if($tile_security['edit'] > 0) {
				echo "<div class='selectall2 selectbutton' title='This will select all PDFs on the current page.' style='text-decoration:underline;cursor:pointer;'>Select All</div>";
			}
			echo '</th>';
		}
		if (strpos($value_config, ','."Part #".',') !== FALSE) {
			echo '<th><span class="popover-examples" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="The Part # for an inventory item."><img src="' . WEBSITE_URL . '/img/info-w.png" width="18"></a></span> Part #</th>';
		}
		if (strpos($value_config, ','."ID #".',') !== FALSE) {
			echo '<th>ID #</th>';
		}
		if (strpos($value_config, ','."Item SKU".',') !== FALSE) {
			echo '<th>Item SKU</th>';
		}
		if (strpos($value_config, ','."Code".',') !== FALSE) {
			echo '<th>Code</th>';
		}
		if (strpos($value_config, ','."Description".',') !== FALSE) {
			echo '<th>Description</th>';
		}
		if (strpos($value_config, ','."Category".',') !== FALSE) {
			echo '<th>Tab</th>';
		}
		if (strpos($value_config, ','."Subcategory".',') !== FALSE) {
			echo '<th>Subtab</th>';
		}
		if (strpos($value_config, ','."Name".',') !== FALSE) {
			echo '<th><span class="popover-examples" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="The set name of the inventory item."><img src="' . WEBSITE_URL . '/img/info-w.png" width="18"></a></span> Name</th>';
		}
		if (strpos($value_config, ','."Product Name".',') !== FALSE) {
			echo '<th>Product Name</th>';
		}
		if (strpos($value_config, ','."Type".',') !== FALSE) {
			echo '<th>Tab</th>';
		}
		if (strpos($value_config, ','."Color".',') !== FALSE) {
			echo '<th>Color</th>';
		}
		if (strpos($value_config, ','."COGS".',') !== FALSE) {
			echo '<th>COGS GL Code</th>';
		}
		if (strpos($value_config, ','."Vendor".',') !== FALSE) {
			echo '<th>Vendor</th>';
		}
		if (strpos($value_config, ','."Date Of Purchase".',') !== FALSE) {
			echo '<th>Date Of Purchase</th>';
		}
		if (strpos($value_config, ','."Shipping Rate".',') !== FALSE) {
			echo '<th>Shipping Rate</th>';
		}
		if (strpos($value_config, ','."Shipping Cash".',') !== FALSE) {
			echo '<th>Shipping Cash</th>';
		}
		if (strpos($value_config, ','."Freight Charge".',') !== FALSE) {
			echo '<th>Freight Charge</th>';
		}
		if (strpos($value_config, ','."Exchange Rate".',') !== FALSE) {
			echo '<th>Exchange Rate</th>';
		}
		if (strpos($value_config, ','."Exchange $".',') !== FALSE) {
			echo '<th>Exchange $</th>';
		}

		if (strpos($value_config, ','."Ticket PO".',') !== FALSE) {
			echo '<th>PO #</th>';
		}
		if (strpos($value_config, ','."Ticket PO Only".',') !== FALSE) {
			echo '<th>PO #</th>';
		}
		if (strpos($value_config, ','."Ticket PO Line".',') !== FALSE) {
			echo '<th>PO Line Item #</th>';
		}
		if (strpos($value_config, ','."Customer Order".',') !== FALSE) {
			echo '<th>Customer Order #</th>';
		}
		if (strpos($value_config, ','."Pallet Num".',') !== FALSE) {
			echo '<th>Pallet #</th>';
			$pallet_list = $dbc->query("SELECT `pallet` FROM `inventory` WHERE `deleted`=0 AND IFNULL(`pallet`,'') != '' GROUP BY `pallet` ORDER BY `pallet`")->fetch_all();
		}
		if (strpos($value_config, ','."Ticket Label BOL".',') !== FALSE) {
			echo '<th>Bill of Lading</th>';
		}

		if (strpos($value_config, ','."Drum Unit Cost".',') !== FALSE) {
			echo '<th>Drum Unit Cost</th>';
		}

		if (strpos($value_config, ','."Drum Unit Price".',') !== FALSE) {
			echo '<th>Drum Unit Price</th>';
		}
		if (strpos($value_config, ','."Tote Unit Cost".',') !== FALSE) {
			echo '<th>Tote Unit Cost</th>';
		}
		if (strpos($value_config, ','."Tote Unit Price".',') !== FALSE) {
			echo '<th>Tote Unit Price</th>';
		}
		if (strpos($value_config, ','."WCB Price".',') !== FALSE) {
			echo '<th>WCB Price</th>';
		}
		if (strpos($value_config, ','."Sell Price".',') !== FALSE) {
			echo '<th>Sell Price</th>';
		}
		if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) {
			echo '<th><span class="popover-examples" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="The final price that the inventory item will be sold for."><img src="' . WEBSITE_URL . '/img/info-w.png" width="18"></a></span> Final Retail Price</th>';
		}
		if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) {
			echo '<th>Wholesale Price</th>';
		}
		if (strpos($value_config, ','."Commercial Price".',') !== FALSE) {
			echo '<th>Commercial Price</th>';
		}
		if (strpos($value_config, ','."Client Price".',') !== FALSE) {
			echo '<th>Client Price</th>';

		}
		if (strpos($value_config, ','."Purchase Order Price".',') !== FALSE) {
			echo '<th>Purchase Order Price</th>';
		}
		if (strpos($value_config, ','."Sales Order Price".',') !== FALSE) {
			echo '<th>'.SALES_ORDER_NOUN.' Price</th>';
		}
		if (strpos($value_config, ','."Suggested Retail Price".',') !== FALSE) {
			echo '<th>Suggested Retail Price</th>';
		}
		if (strpos($value_config, ','."Rush Price".',') !== FALSE) {
			echo '<th>Rush Price</th>';
		}
		if (strpos($value_config, ','."Include in Sales Orders".',') !== FALSE) {
			echo '<th>Include in '.SALES_ORDER_TILE.'</th>';
		}
		if (strpos($value_config, ','."Include in P.O.S.".',') !== FALSE) {
			echo '<th>Include in Point of Sale</th>';
		}
		if (strpos($value_config, ','."Include in Purchase Orders".',') !== FALSE) {
			echo '<th>Include in Purchase Orders</th>';
		}
		if (strpos($value_config, ','."Include in Product".',') !== FALSE) {
			echo '<th>Include in Product</th>';
		}
		if (strpos($value_config, ','."Preferred Price".',') !== FALSE) {
			echo '<th>Preferred Price</th>';
		}
		if (strpos($value_config, ','."Admin Price".',') !== FALSE) {
			echo '<th>Admin Price</th>';
		}
		if (strpos($value_config, ','."Web Price".',') !== FALSE) {
			echo '<th>Web Price</th>';
		}
		if (strpos($value_config, ','."Commission Price".',') !== FALSE) {
			echo '<th>Commission Price</th>';
		}
		if (strpos($value_config, ','."MSRP".',') !== FALSE) {
			echo '<th>MSRP</th>';
		}
		if (strpos($value_config, ','."Unit Price".',') !== FALSE) {
			echo '<th>Unit Price</th>';
		}
		
		if ( $rookconnect == 'sea' ) {
			// On SEA software only allow George to see the cost
			if ( isset ( $_SESSION['user_name'] ) && $_SESSION['user_name'] == 'georgev' ) {
				if (strpos($value_config, ','."Cost".',') !== FALSE) {
					echo '<th>Cost</th>';
				}
				if (strpos($value_config, ','."CDN Cost Per Unit".',') !== FALSE) {
					echo '<th>CDN Cost Per Unit</th>';
				}
				if (strpos($value_config, ','."USD Cost Per Unit".',') !== FALSE) {
					echo '<th>USD Cost Per Unit</th>';
				}
				if (strpos($value_config, ','."Average Cost".',') !== FALSE) {
					echo '<th>Average Cost</th>';
				}
				if (strpos($value_config, ','."Purchase Cost".',') !== FALSE) {
					echo '<th>Purchase Cost</th>';
				}
				if (strpos($value_config, ','."USD Invoice".',') !== FALSE) {
					echo '<th>USD Invoice</th>';
				}
			}
		} else {
			if (strpos($value_config, ','."Cost".',') !== FALSE) {
				echo '<th>Cost</th>';
			}
			if (strpos($value_config, ','."CDN Cost Per Unit".',') !== FALSE) {
				echo '<th>CDN Cost Per Unit</th>';
			}
			if (strpos($value_config, ','."USD Cost Per Unit".',') !== FALSE) {
				echo '<th>USD Cost Per Unit</th>';
			}
			if (strpos($value_config, ','."Average Cost".',') !== FALSE) {
				echo '<th>Average Cost</th>';
			}
			if (strpos($value_config, ','."Purchase Cost".',') !== FALSE) {
				echo '<th>Purchase Cost</th>';
			}
			if (strpos($value_config, ','."USD Invoice".',') !== FALSE) {
				echo '<th>USD Invoice</th>';
			}
		}

		if (strpos($value_config, ','."Unit Cost".',') !== FALSE) {
			echo '<th>Unit Cost</th>';
		}
		if (strpos($value_config, ','."Rent Price".',') !== FALSE) {
			echo '<th>Rent Price</th>';
		}
		if (strpos($value_config, ','."Rental Days".',') !== FALSE) {
			echo '<th>Rental Days</th>';
		}
		if (strpos($value_config, ','."Rental Weeks".',') !== FALSE) {
			echo '<th>Rental Weeks</th>';
		}
		if (strpos($value_config, ','."Rental Months".',') !== FALSE) {
			echo '<th>Rental Months</th>';
		}
		if (strpos($value_config, ','."Rental Years".',') !== FALSE) {
			echo '<th>Rental Years</th>';
		}
		if (strpos($value_config, ','."Reminder/Alert".',') !== FALSE) {
			echo '<th>Reminder/Alert</th>';
		}
		if (strpos($value_config, ','."Daily".',') !== FALSE) {
			echo '<th>Daily</th>';
		}
		if (strpos($value_config, ','."Weekly".',') !== FALSE) {
			echo '<th>Weekly</th>';
		}
		if (strpos($value_config, ','."Monthly".',') !== FALSE) {
			echo '<th>Monthly</th>';
		}
		if (strpos($value_config, ','."Annually".',') !== FALSE) {
			echo '<th>Annually</th>';
		}
		if (strpos($value_config, ','."#Of Days".',') !== FALSE) {
			echo '<th>#Of Days</th>';
		}
		if (strpos($value_config, ','."#Of Hours".',') !== FALSE) {
			echo '<th>#Of Hours</th>';
		}
		if (strpos($value_config, ','."#Of Kilometers".',') !== FALSE) {
			echo '<th>#Of Kilometers</th>';
		}
		if (strpos($value_config, ','."#Of Miles".',') !== FALSE) {
			echo '<th>#Of Miles</th>';
		}
		if (strpos($value_config, ','."Markup By $".',') !== FALSE) {
			echo '<th>Markup By $</th>';
		}
		if (strpos($value_config, ','."Markup By %".',') !== FALSE) {
			echo '<th>Markup By %</th>';
		}

		if (strpos($value_config, ','."GL Revenue".',') !== FALSE) {
			echo '<th>GL Revenue</th>';
		}
		if (strpos($value_config, ','."GL Assets".',') !== FALSE) {
			echo '<th>GL Assets</th>';
		}

		if (strpos($value_config, ','."Current Stock".',') !== FALSE) {
			echo '<th>Current Stock</th>';
		}
		if (strpos($value_config, ','."Current Inventory".',') !== FALSE) {
			echo '<th>Current Inventory</th>';
		}
		if (strpos($value_config, ','."Quantity".',') !== FALSE) {
			echo '<th>Quantity</th>';
		}
		if (strpos($value_config, ','."Expected".',') !== FALSE) {
			echo '<th>Expected</th>';
		}
		if (strpos($value_config, ','."Received".',') !== FALSE) {
			echo '<th>Received</th>';
		}
		if (strpos($value_config, ','."Discrepancy".',') !== FALSE) {
			echo '<th>Discrepancy</th>';
		}
		if (strpos($value_config, ','."Variance".',') !== FALSE) {
			echo '<th>GL Code</th>';
		}
		if (strpos($value_config, ','."Write-offs".',') !== FALSE) {
			echo '<th>Write-offs</th>';
		}
		if (strpos($value_config, ','."Buying Units".',') !== FALSE) {
			echo '<th>Buying Units</th>';
		}
		if (strpos($value_config, ','."Selling Units".',') !== FALSE) {
			echo '<th>Selling Units</th>';
		}
		if (strpos($value_config, ','."Stocking Units".',') !== FALSE) {
			echo '<th>Stocking Units</th>';
		}
		if (strpos($value_config, ','."Min Amount".',') !== FALSE) {
			echo '<th>Min Amount</th>';
		}
		if (strpos($value_config, ','."Max Amount".',') !== FALSE) {
			echo '<th>Max Amount</th>';
		}
		if (strpos($value_config, ','."Location".',') !== FALSE) {
			echo '<th>Location</th>';
		}
		if (strpos($value_config, ','."LSD".',') !== FALSE) {
			echo '<th>LSD</th>';
		}
		if (strpos($value_config, ','."Size".',') !== FALSE) {
			echo '<th>Size</th>';
		}
		if (strpos($value_config, ','."Weight".',') !== FALSE) {
			echo '<th>Weight</th>';
		}
		if (strpos($value_config, ','."Min Max".',') !== FALSE) {
			echo '<th>Min Max</th>';
		}
		if (strpos($value_config, ','."Min Bin".',') !== FALSE) {
			echo '<th>Min Bin</th>';
		}
		if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) {
			echo '<th>Estimated Hours</th>';
		}
		if (strpos($value_config, ','."Actual Hours".',') !== FALSE) {
			echo '<th>Actual Hours</th>';
		}
		if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) {
			echo '<th>Minimum Billable</th>';
		}
		if (strpos($value_config, ','."Quote Description".',') !== FALSE) {
			echo '<th>Quote Description</th>';
		}
		if (strpos($value_config, ','."Status".',') !== FALSE) {
			echo '<th>Status</th>';
		}
		if (strpos($value_config, ','."Display On Website".',') !== FALSE) {
			echo '<th style="text-align:center">Display On Website<!--<br><div class="selectall selectbutton" title="This will select all PDFs on the current page.">Select All</div><div class="submit_selection">[ submit ]</div>--></th>';
		}
		if (strpos($value_config, ','."Featured On Website".',') !== FALSE) {
			echo '<th>Featured</th>';
		}
		if (strpos($value_config, ','."New Item".',') !== FALSE) {
			echo '<th>New</th>';
		}
		if (strpos($value_config, ','."Item On Sale".',') !== FALSE) {
			echo '<th>On Sale</th>';
		}
		if (strpos($value_config, ','."Item On Clearance".',') !== FALSE) {
			echo '<th>Clearance</th>';
		}
		if (strpos($value_config, ','."Notes".',') !== FALSE) {
			echo '<th>Notes</th>';
		}
		if (strpos($value_config, ','."Comments".',') !== FALSE) {
			echo '<th>Comments</th>';
		}
		if ($_GET['no_search'] != 'true' && strpos($value_config, ','."History".',') !== FALSE) {
			echo '<th><span class="popover-examples" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Inventory History for each inventory item."><img src="' . WEBSITE_URL . '/img/info-w.png" width="18"></a></span> History</th>';
		}
		if ($_GET['no_search'] != 'true' && strpos($value_config, ','."Change Cost".',') !== FALSE) {
			echo '<th><span class="popover-examples" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Inventory History for each inventory item."><img src="' . WEBSITE_URL . '/img/info-w.png" width="18"></a></span> History</th>';
		}
		if ($_GET['no_search'] != 'true' && strpos($value_config, ','."Change Qty".',') !== FALSE) {
			echo '<th><span class="popover-examples" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Inventory History for each inventory item."><img src="' . WEBSITE_URL . '/img/info-w.png" width="18"></a></span> History</th>';
		}
		if ($_GET['no_search'] != 'true' && strpos($value_config, ','."Change Comment".',') !== FALSE) {
			echo '<th><span class="popover-examples" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Inventory History for each inventory item."><img src="' . WEBSITE_URL . '/img/info-w.png" width="18"></a></span> History</th>';
		}
		if($_GET['no_search'] != 'true') {
			echo '<th><span class="popover-examples" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Transfer, Edit, or Archive an inventory item."><img src="' . WEBSITE_URL . '/img/info-w.png" width="18"></a></span> Function</th>';
		}
		echo "</tr>";
		echo "</thead>";
	$editable = vuaed_visible_function($dbc, 'inventory');
	while($row = mysqli_fetch_array( $result ))
	{
		$color = '';
		if($row['status'] == 'In inventory') {
			$color = 'style="color: white;"';
		}
		if($row['status'] == 'In transit from vendor') {
			$color = 'style="color: red;"';
		}
		if($row['status'] == 'In transit between yards') {
			$color = 'style="color: blue;"';
		}
		if($row['status'] == 'Not confirmed in yard by inventory check') {
			$color = 'style="color: yellow;"';
		}
		if($row['status'] == 'Assigned to job') {
			$color = 'style="color: green;"';
		}
		if($row['status'] == 'In transit and assigned') {
			$color = 'style="color: purple;"';
		}

		echo '<tr '.$color.'>';
		if (isset($_GET['order_list'])) {
			echo '<td data-title="Include in Order List">';
			$HiddenProducts = explode(',',$inventoryidorder);
			if (in_array($row['inventoryid'], $HiddenProducts)) {
			  $checked = 'Checked';
			} else {
			  $checked = '';
			}
			?><input type='checkbox' style='width:20px; height:20px;' <?php echo $checked; ?> id='<?PHP echo $row['inventoryid']; ?>'  name='' class='order_list_includer' value='<?PHP echo $_GET['order_list']; ?>' <?= !($tile_security['edit'] > 0) ? 'readonly disabled' : '' ?>><br>
			<?php
			echo '</td>';
		}
		if (strpos($value_config, ','."Part #".',') !== FALSE) {
			echo '<td data-title="Part #">' . $row['part_no'] . '</td>';
		}
		if (strpos($value_config, ','."ID #".',') !== FALSE) {
			echo '<td data-title="ID #">' . $row['id_number'] . '</td>';
		}
		if (strpos($value_config, ','."Item SKU".',') !== FALSE) {
			echo '<td data-title="Item SKU">' . $row['item_sku'] . '</td>';
		}
		if (strpos($value_config, ','."Code".',') !== FALSE) {
			echo '<td data-title="Code">' . $row['code'] . '</td>';
		}
		if (strpos($value_config, ','."Description".',') !== FALSE) {
			echo '<td data-title="Desc.">' . $row['part_no'] . '</td>';
		}
		if (strpos($value_config, ','."Category".',') !== FALSE) {
			echo '<td data-title="Tab">' . $row['category'] . '</td>';
		}
		if (strpos($value_config, ','."Subcategory".',') !== FALSE) {
			echo '<td data-title="Sub Tab">' . $row['sub_category'] . '</td>';
		}
		if (strpos($value_config, ','."Name".',') !== FALSE) {
			echo '<td data-title="Name">' . $row['name'] . '</td>';
		}
		if (strpos($value_config, ','."Product Name".',') !== FALSE) {
			echo '<td data-title="Prod. Name">' . $row['product_name'] . '</td>';
		}
		if (strpos($value_config, ','."Type".',') !== FALSE) {
			echo '<td data-title="Tab">' . $row['type'] . '</td>';
		}
		if (strpos($value_config, ','."Color".',') !== FALSE) {
			echo '<td data-title="Color">' . $row['color'] . '</td>';
		}
		if (strpos($value_config, ','."COGS".',') !== FALSE) {
			echo '<td data-title="COGS">' . $row['cogs_total'] . '</td>';
		}
		if (strpos($value_config, ','."Vendor".',') !== FALSE) {
			$vendorid = $row['vendorid'];
			$get_vendor = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT  name FROM contacts WHERE contactid='$vendorid'"));
			echo '<td data-title="Vendor">' . $get_vendor['name'] . '</td>';
		}
		if (strpos($value_config, ','."Date Of Purchase".',') !== FALSE) {
			echo '<td data-title="Purchase Date">' . $row['date_of_purchase'] . '</td>';
		}
		if (strpos($value_config, ','."Shipping Rate".',') !== FALSE) {
			echo '<td data-title="Ship Rate">' . $row['shipping_rate'] . '</td>';
		}
		if (strpos($value_config, ','."Shipping Cash".',') !== FALSE) {
			echo '<td data-title="Ship Cash">' . $row['shipping_cash'] . '</td>';
		}
		if (strpos($value_config, ','."Freight Charge".',') !== FALSE) {
			echo '<td data-title="Freight">' . $row['freight_charge'] . '</td>';
		}
		if (strpos($value_config, ','."Exchange Rate".',') !== FALSE) {
			echo '<td data-title="Exchange Rate">' . $row['exchange_rate'] . '</td>';
		}
		if (strpos($value_config, ','."Exchange $".',') !== FALSE) {
			echo '<td data-title="Exchange $">' . $row['exchange_cash'] . '</td>';
		}

		if (strpos($value_config, ','."Ticket PO".',') !== FALSE) {
			$row_po_list = [];
			foreach(array_filter(explode('#*#',$row['po_number'])) as $row_po) {
				$row_po_list[] = '<a href="?category='.$_GET['category'].'&purchase_order='.$row_po.'">' . $row_po.'</a>';
			}
			echo '<td data-title="PO #">'.implode(', ',$row_po_list).($row['po_line'] > 0 ? ' - '.$row['po_line'] : '') . '</td>';
		}
		if (strpos($value_config, ','."Ticket PO Only".',') !== FALSE) {
			$row_po_list = [];
			foreach(array_filter(explode('#*#',$row['po_number'])) as $row_po) {
				$row_po_list[] = '<a href="?category='.$_GET['category'].'&purchase_order='.$row_po.'">' . $row_po.'</a>';
			}
			echo '<td data-title="PO #">'.implode('<br />',$row_po_list).'</td>';
		}
		if (strpos($value_config, ','."Ticket PO Line".',') !== FALSE) {
			echo '<td data-title="PO Line #">'.$row['po_line'].'</td>';
		}
		if (strpos($value_config, ','."Customer Order".',') !== FALSE) {
			echo '<td data-title="Customer Order">'.$row['customer_order'].'</td>';
		}
		if (strpos($value_config, ','."Pallet Num".',') !== FALSE) {
			echo '<td data-title="Pallet #">';
				if($editable == 1) {
					echo '<span><select class="chosen-select-deselect" data-placeholder="Select a Pallet" name="pallet" data-id="'.$row['inventoryid'].'" onchange="setPallet(this);"><option />';
					foreach($pallet_list as $pallet_name) {
						echo '<option '.($pallet_name[0] == $row['pallet'] ? 'selected' : '').' value="'.$pallet_name[0].'">'.$pallet_name[0].'</option>';
					}
					echo '<option value="ADD_NEW">New Pallet</option>';
					echo '</select></span>';
					echo '<input type="text" class="form-control" value="" style="display:none;" data-id="'.$row['inventoryid'].'" onchange="setPallet(this);">';
				} else {
					echo $row['pallet'];
				}
			echo '</td>';
		}
		if (strpos($value_config, ','."Ticket Label BOL".',') !== FALSE) {
			echo '<td data-title="Bill of Lading">' . $row['ticket_label'] . '</td>';
		}
		
		if (strpos($value_config, ','."Drum Unit Cost".',') !== FALSE) {
			echo '<td data-title="Drum Unit Cost">' . $row['drum_unit_cost'] . '</td>';
		}

		if (strpos($value_config, ','."Drum Unit Price".',') !== FALSE) {
			echo '<td data-title="Drum Unit Price">' . $row['drum_unit_price'] . '</td>';
		}
		if (strpos($value_config, ','."Tote Unit Cost".',') !== FALSE) {
			echo '<td data-title="Tote Unit Cost">' . $row['tote_unit_cost'] . '</td>';
		}
		if (strpos($value_config, ','."Tote Unit Price".',') !== FALSE) {
			echo '<td data-title="Tote Unit Price">' . $row['tote_unit_price'] . '</td>';
		}
		if (strpos($value_config, ','."WCB Price".',') !== FALSE) {
			echo '<td data-title="WCB Price">' . $row['wcb_price'] . '</td>';
		}
		if (strpos($value_config, ','."Sell Price".',') !== FALSE) {
			echo '<td data-title="Sell Price">' . $row['sell_price'] . '</td>';
		}
		if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) {
			echo '<td data-title="Retail">' . $row['final_retail_price'] . '</td>';
		}
		if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) {
			echo '<td data-title="Wholesale">' . $row['wholesale_price'] . '</td>';
		}
		if (strpos($value_config, ','."Commercial Price".',') !== FALSE) {
			echo '<td data-title="Comm. Price">' . $row['commercial_price'] . '</td>';
		}
		if (strpos($value_config, ','."Client Price".',') !== FALSE) {
			echo '<td data-title="Client Price">' . $row['client_price'] . '</td>';
		}
		if (strpos($value_config, ','."Purchase Order Price".',') !== FALSE) {
			 echo '<td data-title="Client Price">' . $row['purchase_order_price'] . '</td>';
		}
		if (strpos($value_config, ','."Sales Order Price".',') !== FALSE) {
			 echo '<td data-title="Client Price">' . $row['sales_order_price'] . '</td>';
		}
		if (strpos($value_config, ','."Suggested Retail Price".',') !== FALSE) {
			echo '<td data-title="Suggested Retail Price">' . $row['suggested_retail_price'] . '</td>';
		}
		if (strpos($value_config, ','."Rush Price".',') !== FALSE) {
			echo '<td data-title="Rush Price">' . $row['rush_price'] . '</td>';
		}
		if (strpos($value_config, ','."Include in Sales Orders".',') !== FALSE) {
			echo '<td data-title="Include in '.SALES_ORDER_TILE.'">';
			?><input type='checkbox' style='width:20px; height:20px;' <?php if($row['include_in_so'] !== '' && $row['include_in_so'] !== NULL) { echo "checked"; } ?> id='<?PHP echo $row['inventoryid']; ?>'  name='' class='sales_order_includer' value='1' <?= !($tile_security['edit'] > 0) ? 'readonly disabled' : '' ?>><br>
			<?php
			echo '</td>';
		}
		if (strpos($value_config, ','."Include in P.O.S.".',') !== FALSE) {
			echo '<td data-title="Include in P.O.S.">';
			?><input type='checkbox' style='width:20px; height:20px;' <?php if($row['include_in_pos'] !== '' && $row['include_in_pos'] !== NULL) { echo "checked"; } ?> id='<?PHP echo $row['inventoryid']; ?>'  name='' class='point_of_sale_includer' value='1' <?= !($tile_security['edit'] > 0) ? 'readonly disabled' : '' ?>><br>
			<?php
			echo '</td>';
		}
		if (strpos($value_config, ','."Include in Purchase Orders".',') !== FALSE) {
			echo '<td data-title="Include in Purchase Orders">';
			?><input type='checkbox' style='width:20px; height:20px;' <?php if($row['include_in_po'] !== '' && $row['include_in_po'] !== NULL) { echo "checked"; } ?> id='<?PHP echo $row['inventoryid']; ?>'  name='' class='purchase_order_includer' value='1' <?= !($tile_security['edit'] > 0) ? 'readonly disabled' : '' ?>><br>
			<?php
			echo '</td>';
		}

		if (strpos($value_config, ','."Include in Product".',') !== FALSE) {
			echo '<td data-title="Include in Product">';
			if($row['productid'] == '') {
			?><input type='checkbox' style='width:20px; height:20px;' <?php if($row['include_in_product'] !== '' && $row['include_in_product'] !== NULL && $row['include_in_product'] == 1) { echo "checked"; } ?> id='<?PHP echo $row['inventoryid']; ?>'  name='' class='product_includer' value='1' <?= !($tile_security['edit'] > 0) ? 'readonly disabled' : '' ?>><br>
			<?php
			} else {
				echo 'Already Included';
			}
			echo '</td>';
		}

		if (strpos($value_config, ','."Preferred Price".',') !== FALSE) {
			echo '<td data-title="Pref. Price">' . $row['preferred_price'] . '</td>';
		}
		if (strpos($value_config, ','."Admin Price".',') !== FALSE) {
			echo '<td data-title="Admin Price">' . $row['admin_price'] . '</td>';
		}
		if (strpos($value_config, ','."Web Price".',') !== FALSE) {
			echo '<td data-title="Web Price">' . $row['web_price'] . '</td>';
		}
		if (strpos($value_config, ','."Commission Price".',') !== FALSE) {
			echo '<td data-title="Commission">' . $row['commission_price'] . '</td>';
		}
		if (strpos($value_config, ','."MSRP".',') !== FALSE) {
			echo '<td data-title="MSRP">' . $row['msrp'] . '</td>';
		}
		if (strpos($value_config, ','."Unit Price".',') !== FALSE) {
			echo '<td data-title="Unit Price">' . $row['unit_price'] . '</td>';
		}

		if ( $rookconnect == 'sea' ) {
			// On SEA software only allow George to see the cost
			if ( isset ( $_SESSION['user_name'] ) && $_SESSION['user_name'] == 'georgev' ) {
				if (strpos($value_config, ','."Cost".',') !== FALSE) {
					echo '<td data-title="Cost">' . $row['cost'] . '</td>';
				}
				if (strpos($value_config, ','."CDN Cost Per Unit".',') !== FALSE) {
				   echo '<td data-title="CAD/Unit">' . $row['cdn_cpu'] . '</td>';
				}
				if (strpos($value_config, ','."USD Cost Per Unit".',') !== FALSE) {
					echo '<td data-title="USD/Unit">' . $row['usd_cpu'] . '</td>';
				}
				if (strpos($value_config, ','."Average Cost".',') !== FALSE) {
					echo '<td data-title="Avg. Cost">' . ($row['average_cost'] > 0 ? $row['average_cost'] : ($row['cost'] > 0 ? $row['cost'] : ($row['unit_cost'] > 0 ? $row['unit_cost'] : $row['purchase_cost']))) . '</td>';
				}
				if (strpos($value_config, ','."Purchase Cost".',') !== FALSE) {
					echo '<td data-title="Purchase Cost">' . $row['purchase_cost'] . '</td>';
				}
				if (strpos($value_config, ','."USD Invoice".',') !== FALSE) {
					echo '<td data-title="USD Invoice">' . $row['usd_invoice'] . '</td>';
				}
			}
		} else {
			if (strpos($value_config, ','."Cost".',') !== FALSE) {
				echo '<td data-title="Cost">' . $row['cost'] . '</td>';
			}
			if (strpos($value_config, ','."CDN Cost Per Unit".',') !== FALSE) {
			   echo '<td data-title="CAD/Unit">' . $row['cdn_cpu'] . '</td>';
			}
			if (strpos($value_config, ','."USD Cost Per Unit".',') !== FALSE) {
				echo '<td data-title="USD/Unit">' . $row['usd_cpu'] . '</td>';
			}
			if (strpos($value_config, ','."Average Cost".',') !== FALSE) {
				echo '<td data-title="Avg. Cost">' . ($row['average_cost'] > 0 ? $row['average_cost'] : ($row['cost'] > 0 ? $row['cost'] : ($row['unit_cost'] > 0 ? $row['unit_cost'] : $row['purchase_cost']))) . '</td>';
			}
			if (strpos($value_config, ','."Purchase Cost".',') !== FALSE) {
				echo '<td data-title="Purchase Cost">' . $row['purchase_cost'] . '</td>';
			}
			if (strpos($value_config, ','."USD Invoice".',') !== FALSE) {
				echo '<td data-title="USD Invoice">' . $row['usd_invoice'] . '</td>';
			}
		}
		
		if (strpos($value_config, ','."Unit Cost".',') !== FALSE) {
			echo '<td data-title="Unit Cost">' . $row['unit_cost'] . '</td>';
		}
		if (strpos($value_config, ','."Rent Price".',') !== FALSE) {
			echo '<td data-title="Rent Price">' . $row['rent_price'] . '</td>';
		}
		if (strpos($value_config, ','."Rental Days".',') !== FALSE) {
			echo '<td data-title="Rent Days">' . $row['rental_days'] . '</td>';
		}
		if (strpos($value_config, ','."Rental Weeks".',') !== FALSE) {
			echo '<td data-title="Rent Weeks">' . $row['rental_weeks'] . '</td>';
		}
		if (strpos($value_config, ','."Rental Months".',') !== FALSE) {
			echo '<td data-title="Rent Months">' . $row['rental_months'] . '</td>';
		}
		if (strpos($value_config, ','."Rental Years".',') !== FALSE) {
			echo '<td data-title="Rent Years">' . $row['rental_years'] . '</td>';
		}
		if (strpos($value_config, ','."Reminder/Alert".',') !== FALSE) {
			echo '<td data-title="Reminder">' . $row['reminder_alert'] . '</td>';
		}
		if (strpos($value_config, ','."Daily".',') !== FALSE) {
			echo '<td data-title="Daily">' . $row['daily'] . '</td>';
		}
		if (strpos($value_config, ','."Weekly".',') !== FALSE) {
			echo '<td data-title="Weekly">' . $row['weekly'] . '</td>';
		}
		if (strpos($value_config, ','."Monthly".',') !== FALSE) {
			echo '<td data-title="Monthly">' . $row['monthly'] . '</td>';
		}
		if (strpos($value_config, ','."Annually".',') !== FALSE) {
			echo '<td data-title="Annual">' . $row['annually'] . '</td>';
		}
		if (strpos($value_config, ','."#Of Days".',') !== FALSE) {
			echo '<td data-title="# Days">' . $row['total_days'] . '</td>';
		}
		if (strpos($value_config, ','."#Of Hours".',') !== FALSE) {
			echo '<td data-title="# Hours">' . $row['total_hours'] . '</td>';
		}
		if (strpos($value_config, ','."#Of Kilometers".',') !== FALSE) {
			echo '<td data-title="# Kilometers">' . $row['total_km'] . '</td>';
		}
		if (strpos($value_config, ','."#Of Miles".',') !== FALSE) {
			echo '<td data-title="# Miles">' . $row['total_miles'] . '</td>';
		}
		if (strpos($value_config, ','."Markup By $".',') !== FALSE) {
			echo '<td data-title="Markup $">' . $row['markup'] . '</td>';
		}
		if (strpos($value_config, ','."Markup By %".',') !== FALSE) {
			echo '<td data-title="Markup %">' . $row['markup_perc'] . '</td>';
		}

		if (strpos($value_config, ','."GL Revenue".',') !== FALSE) {
			echo '<td data-title="GL Revenue">' . $row['revenue'] . '</td>';
		}
		if (strpos($value_config, ','."GL Assets".',') !== FALSE) {
		   echo '<td data-title="GL Assets">' . $row['asset'] . '</td>';
		}

		if (strpos($value_config, ','."Current Stock".',') !== FALSE) {
			echo '<td data-title="Stock">' . $row['current_stock'] . '</td>';
		}
		if (strpos($value_config, ','."Current Inventory".',') !== FALSE) {
			echo '<td data-title="Inventory">' . $row['current_inventory'] . '</td>';
		}
		if (strpos($value_config, ','."Quantity".',') !== FALSE) {
			echo '<td data-title="Quantity">' . $row['quantity'] . '</td>';
		}
		if (strpos($value_config, ','."Expected".',') !== FALSE) {
			echo '<td data-title="Expected">' . $row['expected_inventory'] . '</td>';
		}
		if (strpos($value_config, ','."Received".',') !== FALSE) {
			echo '<td data-title="Received">' . $row['quantity'] . '</td>';
		}
		if (strpos($value_config, ','."Discrepancy".',') !== FALSE) {
			echo '<td data-title="Discrepancy">' . ($row['quantity'] - $row['expected_inventory']) . '</td>';
		}
		if (strpos($value_config, ','."Variance".',') !== FALSE) {
			echo '<td data-title="Variance">' . $row['inv_variance'] . '</td>';
		}
		if (strpos($value_config, ','."Write-offs".',') !== FALSE) {
			echo '<td data-title="Write Offs">' . $row['write_offs'] . '</td>';
		}

		if (strpos($value_config, ','."Buying Units".',') !== FALSE) {
			echo '<td data-title="Buying Units">' . $row['buying_units'] . '</td>';
		}
		if (strpos($value_config, ','."Selling Units".',') !== FALSE) {
			echo '<td data-title="Selling Units">' . $row['selling_units'] . '</td>';
		}
		if (strpos($value_config, ','."Stocking Units".',') !== FALSE) {
			echo '<td data-title="Stocking Units">' . $row['stocking_units'] . '</td>';
		}
		if (strpos($value_config, ','."Min Amount".',') !== FALSE) {
			echo '<td data-title="Min Amount">' . $row['min_amount'] . '</td>';
		}
		if (strpos($value_config, ','."Max Amount".',') !== FALSE) {
			echo '<td data-title="Max Amount">' . $row['max_amount'] . '</td>';
		}

		if (strpos($value_config, ','."Location".',') !== FALSE) {
			echo '<td data-title="Location">' . $row['location'] . '</td>';
		}
		if (strpos($value_config, ','."LSD".',') !== FALSE) {
			echo '<td data-title="LSD">' . $row['lsd'] . '</td>';
		}
		if (strpos($value_config, ','."Size".',') !== FALSE) {
			echo '<td data-title="Size">' . $row['size'] . '</td>';
		}
		if (strpos($value_config, ','."Weight".',') !== FALSE) {
			echo '<td data-title="Weight">' . $row['weight'] . '</td>';
		}
		if (strpos($value_config, ','."Min Max".',') !== FALSE) {
			echo '<td data-title="Min / Max">' . $row['min_max'] . '</td>';
		}
		if (strpos($value_config, ','."Min Bin".',') !== FALSE) {
			echo '<td data-title="Min Bin">' . $row['min_bin'] . '</td>';
		}
		if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) {
			echo '<td data-title="Est. Hours">' . $row['estimated_hours'] . '</td>';
		}
		if (strpos($value_config, ','."Actual Hours".',') !== FALSE) {
			echo '<td data-title="Actual Hours">' . $row['actual_hours'] . '</td>';
		}
		if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) {
			echo '<td data-title="Min. Billable">' . $row['minimum_billable'] . '</td>';
		}
		if (strpos($value_config, ','."Quote Description".',') !== FALSE) {
			echo '<td data-title="Quote Desc.">' . html_entity_decode($row['quote_description']) . '</td>';
		}
		if (strpos($value_config, ','."Status".',') !== FALSE) {
			echo '<td data-title="Status">' . $row['status'] . '</td>';
		}
		if (strpos($value_config, ','."Display On Website".',') !== FALSE) {
			//echo '<td data-title="On Website">' . $row['display_website'] . '</td>';
			/*$intv_id				= $row['inventoryid'];
			$get_website_config		= mysqli_fetch_assoc(mysqli_query($dbc, "SELECT display_website FROM inventory WHERE inventoryid='$intv_id'"));
			$display_website_config	= ',' . $get_website_config['display_website'] . ',';
			$checked				= ( strpos ( $display_website_config, ',' . "Yes" . ',' ) !== FALSE) ? $checked=" checked" : $checked="";

			echo '<td data-title="On Website" align="center"><input style="height:20px; width:20px;" type="checkbox" name="display_website[]" class="display_website" value="' . $intv_id . '"' . $checked . '></td>';*/ ?>
			<td data-title="On Website" align="center"><input type='checkbox' style='width:20px; height:20px;' <?php echo ( $row['display_website'] == 'Yes' && $row['display_website'] !== NULL ) ? "checked='checked'" : ""; ?> id='<?php echo $row['inventoryid']; ?>'  name='' class='display_website' value='<?php //echo ( $row['display_website'] == 'Yes' && $row['display_website'] !== NULL ) ? "Yes" : "No"; ?>' <?= !($tile_security['edit'] > 0) ? 'readonly disabled' : '' ?>></td><?php
		}
		if (strpos($value_config, ','."Featured On Website".',') !== FALSE) {
			$featured_item = ( $row['featured']=='0' ) ? 'No' : 'Yes';
			echo '<td data-title="New">' . $featured_item . '</td>';
		}
		if (strpos($value_config, ','."New Item".',') !== FALSE) {
			$new_item = ( $row['new']=='0' ) ? 'No' : 'Yes';
			echo '<td data-title="New">' . $new_item . '</td>';
		}
		if (strpos($value_config, ','."Item On Sale".',') !== FALSE) {
			$on_sale = ( $row['sale']=='0' ) ? 'No' : 'Yes';
			echo '<td data-title="On Sale">' . $on_sale . '</td>';
		}
		if (strpos($value_config, ','."Item On Clearance".',') !== FALSE) {
			$on_clearance = ( $row['clearance']=='0' ) ? 'No' : 'Yes';
			echo '<td data-title="Clearance">' . $on_clearance . '</td>';
		}
		if (strpos($value_config, ','."Notes".',') !== FALSE) {
			echo '<td data-title="Notes">' . $row['note'] . '</td>';
		}
		if (strpos($value_config, ','."Comments".',') !== FALSE) {
			echo '<td data-title="Comments">' . $row['comment'] . '</td>';
		}

		if ($_GET['no_search'] != 'true' && strpos($value_config, ','."History".',') !== FALSE) {
			echo '<td data-title="History"><a class="iframe_open" href="#" data-id="'.$row['inventoryid'].'">View</a></td>';
		}
		if ($_GET['no_search'] != 'true' && strpos($value_config, ','."Change Cost".',') !== FALSE) {
			echo '<td data-title="History"><a class="iframe_open" href="#" data-id="'.$row['inventoryid'].'" data-detail="cost">View</a></td>';
		}
		if ($_GET['no_search'] != 'true' && strpos($value_config, ','."Change Qty".',') !== FALSE) {
			echo '<td data-title="History"><a class="iframe_open" href="#" data-id="'.$row['inventoryid'].'" data-detail="qty">View</a></td>';
		}
		if ($_GET['no_search'] != 'true' && strpos($value_config, ','."Change Comment".',') !== FALSE) {
			echo '<td data-title="History"><a class="iframe_open" href="#" data-id="'.$row['inventoryid'].'" data-detail="comment">View</a></td>';
		}
		if($_GET['no_search'] != 'true') {
			echo '<td data-title="Function">';
			if($editable == 1) {
				if($number_of_connections > 0) {
					echo '<a href=\'cross_software_transfer.php?inventoryid='.$row['inventoryid'].'\'>Transfer</a> | ';
				}
				echo '<a href=\'add_inventory.php?inventoryid='.$row['inventoryid'].'\'>Edit</a> | ';
				echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&inventoryid='.$row['inventoryid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
			}
			echo '</td>';
		}
		echo "</tr>";
	}

	echo '</table>';
	if($_GET['no_search'] != 'true') {
		echo '<div class="pagination_links">';
		display_pagination($dbc, $query_count, $pageNum, $rowsPerPage);
		echo '</div>';
	}
} else {
	echo "<h2>No Record Found.</h2>";
}