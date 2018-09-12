<?php
$match_business = '';
if(!empty(MATCH_CONTACTS)) {
	$match_business = " AND `tickets`.`businessid` IN (".MATCH_CONTACTS.")";
	$match_business_picklist = " AND `businessid` IN (".MATCH_CONTACTS.")";
}
$current_tab = basename($_SERVER['PHP_SELF'], '.php');
$dropdownornot ='';
$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_category_dropdown'"));
if($get_config['configid'] > 0) {
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='show_category_dropdown'"));
	if($get_config['value'] == '1') {
		$dropdownornot = 'true';
	}
}
$inv_security = get_security($dbc, 'inventory');
$get_default = get_config($dbc, 'inventory_default');
if($get_default != '') {
	$dbc->query("UPDATE `inventory` SET `category`='$get_default' WHERE IFNULL(`category`,'')='' AND `deleted`=0");
	$before_change = '';
  $history = "Inventory is been Updated. <br />";
  add_update_history($dbc, 'inventory_history', $history, '', $before_change);
}
$inventory_setting  = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM inventory_setting WHERE inventorysettingid = 1"));
$set_check_value    = ','.$inventory_setting['value'].','; ?>

<ul class="sidebar">
	<li class="standard-sidebar-searchbox"><input type="text" name="search_inventory" value="<?= $_GET['search_inventory'] ?>" class="form-control search_list" placeholder="Search <?= INVENTORY_TILE ?>" onchange="window.location.replace('inventory.php?category=Top&ticket_status=<?= urlencode($_GET['ticket_status']) ?>&search_inventory='+encodeURI(this.value));"></li>
	<?php if(strpos($set_check_value, ',summary') !== FALSE) { ?>
		<a href='inventory.php'><li <?= $current_tab == 'inventory' && empty($_GET['category']) ? 'class="active"' : '' ?>>Summary</li></a>
	<?php } else if(empty($_GET['category'])) {
		$_GET['category'] = 'Top';
	} ?>
	<?php if(check_subtab_persmission( $dbc, 'inventory', ROLE, 'inventory' ) === true) {
        if(get_config($dbc, 'inventory_default_select_all') == 1) { ?>
            <a href='inventory.php?category=dispall_31V2irt2u3e5S3s1f2ADe3_31<?php echo $order_list;?>'><li <?= $current_tab == 'inventory' && $_GET['category'] == 'dispall_31V2irt2u3e5S3s1f2ADe3_31' ? 'class="active"' : '' ?>><?= INVENTORY_TILE ?></li></a><?php
        } else { ?>
            <a href='inventory.php?category=Top<?php echo $order_list;?>'><li <?= $current_tab == 'inventory' && $_GET['category'] == 'Top' ? 'class="active"' : '' ?>><?= INVENTORY_TILE ?></li></a><?php
        }
    }
	$tabs = get_config($dbc, 'inventory_tabs');
	$each_tab = explode('#*#', $tabs);
    if($current_tab == 'inventory') {
		echo '<ul style="margin: 0px;">';
			$category = $_GET['category'];

			$active_all = '';
			$active_bom = '';
			if($_GET['category'] == 'Top' && $currentlist != 'on') {
				$active_all = 'class="active"';
			}
			echo "<a href='inventory.php?category=Top".$order_list."'><li $active_all>Last 25 Added</li></a>";
			if($dropdownornot !== 'true') {
				foreach ($each_tab as $cat_tab) {
					$url_tab = preg_replace('/[^a-z]/','',strtolower($cat_tab));
					$active_daily = '';
					if((!empty($_GET['category'])) && ($_GET['category'] == $url_tab) && (!isset($_GET['currentlist']))) {
						$active_daily = 'class="active"';
						$active_cat = $cat_tab;
					}
					echo "<a href='inventory.php?category=".$url_tab.$order_list."'><li $active_daily>".$cat_tab."</li></a>";
				}
			}

			if($_GET['category'] == 'bom') {
				$active_bom = ' active_tab';
			}

            $inventory_setting = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `value` FROM `inventory_setting` WHERE `inventorysettingid` = 1"));
            $set_check_value = $inventory_setting['value'];
            $url_po = filter_var($_GET['purchase_order'],FILTER_SANITIZE_STRING);
            $url_cat = filter_var($_GET['category'],FILTER_SANITIZE_STRING);
            $url_cat = !empty($url_po) ? 'Top' : $url_cat;

            if ( strpos(','.$set_check_value.',', ",purchaseorders") !== false ) {
                $url_ponum = filter_var($_GET['ponum'], FILTER_SANITIZE_STRING);
                $po_result = mysqli_query($dbc, "SELECT `id`, `item_id`, `po_num` FROM `ticket_attached` WHERE `src_table`='inventory' AND `deleted`=0 GROUP BY `po_num` ORDER BY `po_num`");
                if ( $po_result->num_rows>0 ) {
                    echo '<li class="cursor-hand '.($url_cat=='purchaseorders' ? '' : 'collapsed').'" data-toggle="collapse" data-target="#po_list">Purchase Orders<span class="arrow"></span>';
                        echo '<ul id="po_list" class="collapse '.($url_cat=='purchaseorders' ? 'in' : '').'">';
                            while ( $po_row=mysqli_fetch_assoc($po_result) ) {
                                echo '<a href="inventory.php?category=purchaseorders&ponum='.$po_row['po_num'].'" class="'.($url_ponum==$po_row['po_num'] ? 'active' : '').'"><li>'.$po_row['po_num'].'</li></a>';
                            }
                        echo '</ul>';
                    echo '</li></a>';
                }
            }

            if ( strpos(','.$set_check_value.',', ",customerorders") !== false ) {
                $url_conum = filter_var($_GET['conum'], FILTER_SANITIZE_STRING);
                $co_result = mysqli_query($dbc, "SELECT `id`, `item_id`, `position` FROM `ticket_attached` WHERE `src_table`='inventory' AND `deleted`=0 GROUP BY `position` ORDER BY `position`");
                if ( $co_result->num_rows>0 ) {
                    echo '<li class="cursor-hand '.($url_cat=='customerorders' ? '' : 'collapsed').'" data-toggle="collapse" data-target="#co_list">Customer Orders<span class="arrow"></span>';
                        echo '<ul id="co_list" class="collapse '.($url_cat=='customerorders' ? 'in' : '').'">';
                            while ( $co_row=mysqli_fetch_assoc($co_result) ) {
                                echo '<a href="inventory.php?category=customerorders&conum='.$co_row['position'].'" class="'.($url_conum==$co_row['position'] ? 'active' : '').'"><li>'.$co_row['position'].'</li></a>';
                            }
                        echo '</ul>';
                    echo '</li></a>';
                }
            }

            if ( strpos(','.$set_check_value.',', ",pallet") !== false ) {
                $url_pallet = filter_var($_GET['pallet'], FILTER_SANITIZE_STRING);
                $pallet_result = mysqli_query($dbc, "SELECT `inventoryid`, `pallet` FROM `inventory` WHERE `pallet`<>'' AND `deleted`=0 GROUP BY `pallet` ORDER BY `pallet`");
                if ( $pallet_result->num_rows>0 ) {
                    echo '<li class="cursor-hand '.($url_cat=='pallet' ? '' : 'collapsed').'" data-toggle="collapse" data-target="#pallet_list">Pallet #<span class="arrow"></span>';
                        echo '<ul id="pallet_list" class="collapse '.($url_cat=='pallet' ? 'in' : '').'">';
                            while ( $pallet_row=mysqli_fetch_assoc($pallet_result) ) {
                                echo '<a href="inventory.php?category=pallet&pallet='.$pallet_row['pallet'].'" class="'.($url_pallet==$pallet_row['pallet'] ? 'active' : '').'"><li>'.$pallet_row['pallet'].'</li></a>';
                            }
                        echo '</ul>';
                    echo '</li></a>';
                }
            }
		echo '</ul>';
	}

    if (strpos($set_check_value, "warehouse") !== FALSE) {
        if(check_subtab_persmission( $dbc, 'inventory', ROLE, 'warehouse' ) === true) { ?>
            <li class="cursor-hand"><a data-toggle="collapse" data-target="#warehouse_tabs" class="<?= $current_tab == 'warehouse' ? '' : 'collapsed' ?>">Warehousing<span class="arrow"></span></a>
				<ul class="collapse <?= $current_tab == 'warehouse' ? 'in' : '' ?>" id="warehouse_tabs">
					<?php $warehouse_name = '';
					$warehouses = '';
					$purchase_orders = '<b>Purchase Orders</b>';
					$tickets = '<b>'.TICKET_TILE.'</b>';
					foreach(sort_contacts_query($dbc->query("SELECT `contacts`.`contactid`,`contacts`.`name`,IFNULL(IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`),'') `display_name`,IFNULL(`tickets`.`ticket_label`,'') `ticket_label`,`tickets`.`ticketid`,COUNT(*) `inv_count` FROM `contacts` LEFT JOIN `ticket_schedule` ON `contacts`.`contactid`=`ticket_schedule`.`warehouse_location` LEFT JOIN `ticket_attached` ON `ticket_schedule`.`ticketid`=`ticket_attached`.`ticketid` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_schedule`.`warehouse_location` > 0 AND `ticket_attached`.`src_table` IN ('inventory_general') AND `tickets`.`deleted`=0 AND `ticket_attached`.`deleted`=0 AND `ticket_schedule`.`deleted`=0 $match_business GROUP BY `contacts`.`contactid`,`contacts`.`name`,`contacts`.`last_name`,`contacts`.`first_name`, IFNULL(NULLIF(`ticket_attached`.`po_num`,''),`tickets`.`purchase_order`) ORDER BY `purchase_order`")) as $warehouse) {
						if($warehouse_name != $warehouse['name'].' '.$warehouse['first_name'].' '.$warehouse['last_name']) {
							if($warehouse_name != '') {
								$warehouses .= $purchase_orders.$tickets.'</ul><div class="clearfix"></div>';
								$purchase_orders = '<b>Purchase Orders</b>';
								$tickets = '<b>'.TICKET_TILE.'</b>';
							}
							$warehouse_name = $warehouse['name'].' '.$warehouse['first_name'].' '.$warehouse['last_name'];
							$warehouses .= '<li class="cursor-hand"><a data-toggle="collapse" data-target="#warehouselist_'.$warehouse['contactid'].'" class="'.($_GET['warehouse'] == $warehouse['contactid'] ? '' : 'collapsed').'">'.$warehouse_name.'<span class="arrow"></span></a>
								<ul class="collapse '.($_GET['warehouse'] == $warehouse['contactid'] ? 'in' : '').'" id="warehouselist_'.$warehouse['contactid'].'">';
						}
						$purchase_orders .= "<a href='warehouse.php?warehouse=".$warehouse['contactid']."&po=".$warehouse['display_name']."'><li ".($warehouse['contactid'] == $_GET['warehouse'] && isset($_GET['po']) && $warehouse['display_name'] == $_GET['po'] ? 'class="active"' : '').">".($warehouse['display_name'] != '' ? $warehouse['display_name'] : 'No Purchase Order #')."<span class='pull-right'>".$warehouse['inv_count']."</span></li></a>";
						$tickets .= "<a href='warehouse.php?warehouse=".$warehouse['contactid']."&ticket=".$warehouse['ticketid']."'><li ".($warehouse['contactid'] == $_GET['warehouse'] && isset($_GET['ticket']) && $warehouse['ticketid'] == $_GET['ticket'] ? 'class="active"' : '').">".($warehouse['display_name'] != '' ? $warehouse['ticket_label'] : 'No '.TICKET_NOUN)."<span class='pull-right'>".$warehouse['inv_count']."</span></li></a>";
					}
					if($warehouse_name != '') {
						$warehouses .= $purchase_orders.$tickets.'</ul><div class="clearfix"></div>';
					}
					echo $warehouses;
					if($_GET['warehouse'] != '') {
						$ticket_filters = explode(',',get_field_config($dbc, 'tickets_dashboard'));
						if(in_array('Status',$ticket_filters)) {
							$statuses = explode(',',get_config($dbc, 'ticket_status'));
							$_GET['ticket_status'] = filter_var($_GET['ticket_status'],FILTER_SANITIZE_STRING);
							if(!in_array('Time Estimate Needed',$statuses)) {
								$ticket_list = mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `tickets`.`ticketid` IN (SELECT `ticketid` FROM `ticket_schedule` WHERE `warehouse_location`='$warehouse') AND `status`='Time Estimate Needed' AND `deleted`=0 AND `ticketid` IN (SELECT `ticketid` FROM `ticket_attached` WHERE `src_table` IN ('inventory_general')) $match_business");
								$status_id = urlencode('Time Estimate Needed');
								echo "<a href='warehouse.php?warehouse=".$_GET['warehouse'].($status_id == urlencode($_GET['ticket_status']) ? '' : "&ticket_status=".$status_id)."'><li ".($status_id == urlencode($_GET['ticket_status']) ? 'class="active"' : '').">Active ".TICKET_TILE."<span class='pull-right'>".$ticket_list->num_rows."</span></li></a>";
							}
							foreach($statuses as $status_name) {
								$ticket_list = mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `tickets`.`ticketid` IN (SELECT `ticketid` FROM `ticket_schedule` WHERE `warehouse_location`='$warehouse') AND `status`='$status_name' AND `deleted`=0 AND `ticketid` IN (SELECT `ticketid` FROM `ticket_attached` WHERE `src_table` IN ('inventory_general')) $match_business");
								if($ticket_list->num_rows > 0) {
									$status_id = urlencode($status_name);
									echo "<a href='warehouse.php?warehouse=".$_GET['warehouse'].($status_id == urlencode($_GET['ticket_status']) ? '' : "&ticket_status=".$status_id)."'><li ".($status_id == urlencode($_GET['ticket_status']) ? 'class="active"' : '').">".$status_name."<span class='pull-right'>".$ticket_list->num_rows."</span></li></a>";
								}
							}
						}
						if(in_array('Business',$ticket_filters)) {
							$ticket_list = mysqli_query($dbc, "SELECT businessid, COUNT(*) rows FROM `tickets` WHERE `tickets`.`ticketid` IN (SELECT `ticketid` FROM `ticket_schedule` WHERE `warehouse_location`='$warehouse') AND `businessid` > 0 AND `deleted`=0 AND `ticketid` IN (SELECT `ticketid` FROM `ticket_attached` WHERE `src_table` IN ('inventory_general')) $match_business GROUP BY `businessid` HAVING COUNT(*) > 0");
							if($ticket_list->num_rows > 0) {
								echo '<b>'.BUSINESS_CAT.'</b>';
							}
							while($tickets = $ticket_list->fetch_assoc()) {
								echo "<a href='warehouse.php?warehouse=".$_GET['warehouse'].($tickets['businessid'] == $_GET['businessid'] ? '' : "&businessid=".$tickets['businessid'])."'><li ".($tickets['businessid'] == $_GET['businessid'] ? 'class="active"' : '').">".get_contact($dbc, $tickets['businessid'], 'name')."<span class='pull-right'>".$tickets['rows']."</span></li></a>";
							}
						}
						if(in_array('Contact',$ticket_filters)) {
							$ticket_list = mysqli_query($dbc, "SELECT clientid, COUNT(*) rows FROM `tickets` WHERE `tickets`.`ticketid` IN (SELECT `ticketid` FROM `ticket_schedule` WHERE `warehouse_location`='$warehouse') AND `clientid` > 0 AND `deleted`=0 AND `ticketid` IN (SELECT `ticketid` FROM `ticket_attached` WHERE `src_table` IN ('inventory_general')) $match_business GROUP BY `clientid` HAVING COUNT(*) > 0");
							if($ticket_list->num_rows > 0) {
								echo '<b>Contacts</b>';
							}
							while($tickets = $ticket_list->fetch_assoc()) {
								echo "<a href='warehouse.php?warehouse=".$_GET['warehouse'].($tickets['clientid'] == $_GET['clientid'] ? '' : "&clientid=".$tickets['clientid'])."'><li ".($tickets['clientid'] == $_GET['clientid'] ? 'class="active"' : '').">".get_contact($dbc, $tickets['clientid'])."<span class='pull-right'>".$tickets['rows']."</span></li></a>";
							}
						}
						if(in_array('Project',$ticket_filters)) {
							echo '<b>'.PROJECT_NOUN.' Tabs</b>';
							$project_types = [];
							foreach(explode(',',get_config($dbc, 'project_tabs')) as $type_name) {
								$project_types[preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($type_name)))] = $type_name;
							}
							foreach($project_types as $cat_tab_value => $cat_tab) {
								$row = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `tickets` WHERE `projectid` IN (SELECT `projectid` FROM `project` WHERE `deleted`=0 AND `projecttype`='$cat_tab_value') AND `deleted`=0 AND `tickets`.`ticketid` IN (SELECT `ticketid` FROM `ticket_schedule` WHERE `warehouse_location`='$warehouse') AND `deleted`=0 AND `ticketid` IN (SELECT `ticketid` FROM `ticket_attached` WHERE `src_table` IN ('inventory_general')) $match_business"));
								echo "<a href='warehouse.php?warehouse=".$_GET['warehouse'].($cat_tab != $_GET['projecttype'] ? "&projecttype=".$cat_tab_value : '')."'><li ".($cat_tab_value == $_GET['projecttype'] ? 'class="active"' : '').">".$cat_tab."<span class='pull-right'>".$row['count']."</span></li></a>";
							}
						}
					} ?>
				</ul>
			</li><?php
        }
    }

    if (strpos($set_check_value, "pick_lists") !== FALSE && check_subtab_persmission( $dbc, 'inventory', ROLE, 'pick_list_create' ) === true) { ?>
		<li class="cursor-hand"><a data-toggle="collapse" data-target="#picklist_manage" class="<?= $current_tab == 'pick_list_create' ? '' : 'collapsed' ?>">Manage <?= INVENTORY_NOUN ?> Pick Lists<span class="arrow"></span></a>
			<ul class="collapse <?= $current_tab == 'pick_list_create' ? 'in' : '' ?>" id="picklist_manage">
				<?php $pick_lists = $dbc->query("SELECT `id`, `name` FROM `pick_lists` WHERE `deleted`=0 AND `completed`=0 $match_business_picklist ORDER BY `name`");
				while($list = $pick_lists->fetch_assoc()) { ?>
					<a href="pick_list_create.php?edit=<?= $list['id'] ?>"><li><?= $list['name'] ?></li></a>
				<?php } ?>
				<?php if($inv_security['edit'] > 0) { ?>
					<a href="pick_list_create.php?edit=0"><li>Create New Pick List</li></a>
				<?php } ?>
			</ul>
		</li>
		<?php $pick_lists = $dbc->query("SELECT `id`, `name` FROM `pick_lists` WHERE `deleted`=0 AND `completed`=1 $match_business_picklist ORDER BY `name`");
		if($pick_lists->num_rows > 0) { ?>
			<li class="cursor-hand"><a data-toggle="collapse" data-target="#picklist_manage_complete" class="<?= $current_tab == 'pick_list_create' ? '' : 'collapsed' ?>">Completed <?= INVENTORY_NOUN ?> Pick Lists<span class="arrow"></span></a>
				<ul class="collapse <?= $current_tab == 'pick_list_create' ? 'in' : '' ?>" id="picklist_manage_complete">
					<?php while($list = $pick_lists->fetch_assoc()) { ?>
						<a href="pick_list_create.php?edit=<?= $list['id'] ?>"><li><?= $list['name'] ?></li></a>
					<?php } ?>
				</ul>
			</li>
		<?php } ?>
    <?php }

    if (strpos($set_check_value, "pick_lists") !== FALSE && check_subtab_persmission( $dbc, 'inventory', ROLE, 'pick_list_fill' ) === true) { ?>
		<li class="cursor-hand"><a data-toggle="collapse" data-target="#picklist_fill" class="<?= $current_tab == 'pick_list_fill' ? '' : 'collapsed' ?>">Fill <?= INVENTORY_NOUN ?> Pick Lists<span class="arrow"></span></a>
			<ul class="collapse <?= $current_tab == 'pick_list_fill' ? 'in' : '' ?>" id="picklist_fill">
				<?php $pick_lists = $dbc->query("SELECT `id`, `name` FROM `pick_lists` WHERE `deleted`=0 AND `completed`=0 $match_business_picklist ORDER BY `name`");
				if($pick_lists->num_rows > 0) {
					while($list = $pick_lists->fetch_assoc()) { ?>
						<a href="pick_list_fill.php?id=<?= $list['id'] ?>"><li><?= $list['name'] ?></li></a>
					<?php }
				} else { ?>
					<li>No Pick Lists Found</li>
				<?php } ?>
			</ul>
		</li>
    <?php }

    if (strpos($set_check_value, "no_cost") !== FALSE) {
        if(check_subtab_persmission( $dbc, 'inventory', ROLE, 'no_cost' ) === true) { ?>
            <a href="no_cost.php"><li <?= $current_tab == 'no_cost' ? 'class="active"' : '' ?>><?= INVENTORY_NOUN ?> Without a Cost</li></a><?php
        }
    }

    if (strpos($set_check_value, "rs") !== FALSE) {
        if(check_subtab_persmission( $dbc, 'inventory', ROLE, 'receive_shipment' ) === true) { ?>
            <a href="receive_shipment.php"><li <?= $current_tab == 'receive_shipment' ? 'class="active"' : '' ?>>Receive Shipment</li></a><?php
        }
    }

    if (strpos($set_check_value, ','."bom") !== FALSE || strpos($set_check_value, "bom") !== FALSE) {
        if(check_subtab_persmission( $dbc, 'inventory', ROLE, 'bill_of_material' ) === true) { ?>
            <a href="bill_of_material.php"><li <?= $current_tab == 'bill_of_material' ? 'class="active"' : '' ?>>Bill of Material</li></a><?php
        }
    }

    if (strpos($set_check_value, ','."bomc") !== FALSE || strpos($set_check_value, "bomc") !== FALSE) {
        if(check_subtab_persmission( $dbc, 'inventory', ROLE, 'bill_of_material_consumables' ) === true) { ?>
            <a href="bill_of_material_consumables.php"><li <?= $current_tab == 'bill_of_material_consumables' ? 'class="active"' : '' ?>>Bill of Material (Consumables)</li></a><?php
        }
    }

    if (strpos($set_check_value, ','."writeoff") !== FALSE || strpos($set_check_value, "writeoff") !== FALSE) {
        if(check_subtab_persmission( $dbc, 'inventory', ROLE, 'waste_write_off' ) === true) { ?>
            <a href="waste_write_off.php"><li <?= $current_tab == 'waste_write_off' ? 'class="active"' : '' ?>>Waste / Write-Off</li></a><?php
        }
    }

    if (strpos($set_check_value, ','."checklists") !== FALSE || strpos($set_check_value, "checklists") !== FALSE) {
        if(check_subtab_persmission( $dbc, 'inventory', ROLE, 'checklist' ) === true) { ?>
            <a href="inventory_checklist.php"><li <?= $current_tab == 'inventory_checklist' ? 'class="active"' : '' ?>>Checklists</li></a><?php
        }
    }

    if (strpos($set_check_value, ','."orderlists") !== FALSE || strpos($set_check_value, "orderlists") !== FALSE) {
        if(check_subtab_persmission( $dbc, 'inventory', ROLE, 'orderlists' ) === true) { ?>
            <a href="order_lists.php"><li <?= $current_tab == 'order_lists' ? 'class="active"' : '' ?>>Order Lists</li></a><?php
        }
    }

    if (strpos($set_check_value, ','."checklist_orders") !== FALSE || strpos($set_check_value, "checklist_orders") !== FALSE) {
        if(check_subtab_persmission( $dbc, 'inventory', ROLE, 'checklist_orders' ) === true) { ?>
            <a href="order_checklists.php"><li <?= $current_tab == 'order_checklists' ? 'class="active"' : '' ?>>Order Checklists</li></a><?php
        }
    }

    $total_inv = mysqli_num_rows(mysqli_query($dbc,"SELECT * FROM products WHERE include_in_inventory = 1"));
    if($total_inv > 0){ ?>
        <a href="products.php"><li <?= $current_tab == 'products' ? 'class="active"' : '' ?>>Products</li></a><?php
    }

    if(isset($_GET['order_list'])) {
        if ($inventoryidorder !== '') {
            if($currentlist == 'on') {
                $active = 'active_tab';
            } else { $active = ''; } ?>
        <a href="inventory.php?currentlist&category=Top".$order_list.""><li <?= $currentlist == 'on' ? 'class="active"' : '' ?>>Order List Items</li></a><?php
        }
    } ?>
</ul>
