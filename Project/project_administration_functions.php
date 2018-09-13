<?php function get_administration_tickets($dbc, $name, $projectid, $type_list = []) {
    $type_query = '';
    if(!empty($type_list)) {
        $type_query = " AND `tickets`.`ticket_type` IN ('".implode("','",$type_list)."')";
    }
	$project_query = '';
	if($projectid > 0) {
		$project_query = " AND `projectid` = '$projectid'";
	}
	$project_admin_multiday_tickets = get_config($dbc, 'project_admin_multiday_tickets');

	// Figure out which tab we are on
	$name = explode('_',$name); 
	$status = $name[2];
	$id = $name[1];
	$filter_region = $name[3];
    foreach(mysqli_fetch_all($dbc->query("SELECT IFNULL(`region`,'') FROM `tickets` WHERE `deleted`=0 ".($other_groups['regions'] != "','" && $other_groups['regions'] != "" ? " AND ((`region` IN ('{$admin_group['region']}','') AND `region` NOT IN ('{$other_groups['regions']}')) OR ('{$admin_group['region']}'='' AND `region` NOT IN ('{$other_groups['regions']}')))" : "")." GROUP BY IFNULL(`region`,'')")) as $region) {
        if($filter_region == str_replace('_','',config_safe_str($region[0]))) {
            $filter_region = $region[0];
        }
    }
	$filter_class = $name[4];
    foreach(mysqli_fetch_all($dbc->query("SELECT IFNULL(`classification`,'') FROM `tickets` WHERE `deleted`=0 ".($other_groups['classifications'] != "','" && $other_groups['classifications'] != "" ? " AND (`classification` IN ('{$admin_group['classification']}','') OR ('{$admin_group['classification']}'='' AND `classification` NOT IN ('{$other_groups['classifications']}')))" : "")." GROUP BY IFNULL(`classification`,'')")) as $class) {
        if($filter_class == str_replace('_','',config_safe_str($class[0]))) {
            $filter_class = $class[0];
        }
    }
	$filter_site = $name[5];
	$filter_business = $name[6];

	$precedence_search = "',_%,'";
	$contact_search = "'%,{$_SESSION['contactid']},%'";
	if($project_admin_multiday_tickets == 1) {
		$precedence_search = "CONCAT('%#*#',IFNULL(`ticket_attached`.`date_stamp`,''),'%')";
		$precedence_search2 = "CONCAT('%#*#',IFNULL(`tickets`.`to_do_date`,''),'%')";
		$contact_search = "CONCAT('%,{$_SESSION['contactid']}#*#',IFNULL(`ticket_attached`.`date_stamp`,''),',%')";
		$contact_search2 = "CONCAT('%,{$_SESSION['contactid']}#*#',IFNULL(`tickets`.`to_do_date`,''),',%')";
	}

	// Get the approval settings for the current tab
	$admin_groups = $dbc->query("SELECT * FROM `field_config_project_admin` WHERE `deleted`=0 AND CONCAT(',',`contactid`,',') LIKE '%,{$_SESSION['contactid']},%'");
	for($admin_group = $admin_groups->fetch_assoc(); $admin_group['id'] != $id && !empty($admin_group['name']); $admin_group = $admin_groups->fetch_assoc());

	// Get other management groups to exclude the tickets that fall to them from this group
	$other_groups = $dbc->query("SELECT GROUP_CONCAT(`region` SEPARATOR ''',''') `regions`, GROUP_CONCAT(`classification` SEPARATOR ''',''') `classifications`, GROUP_CONCAT(`location` SEPARATOR ''',''') `locations`, GROUP_CONCAT(IFNULL(NULLIF(`customer`,''),0)) `customers`, GROUP_CONCAT(`staff`) `staff` FROM `field_config_project_admin` WHERE `id`!='$id' AND `deleted`=0")->fetch_assoc();

	// Filter based on the selected Region and Classification
	$region_query = " AND (REPLACE(IFNULL(`tickets`.`region`,''),'[^a-zA-Z0-9]+','') LIKE '$filter_region' OR '$filter_region'='') AND (REPLACE(IFNULL(`tickets`.`classification`,''),'[^a-zA-Z0-9]+','') LIKE '$filter_class' OR '$filter_class'='')";

	// Filter out the non-relevant tickets
	$group_query = (trim($other_groups['regions'],",'") != "" ? " AND (`tickets`.`region`='{$admin_group['region']}' OR ('{$admin_group['region']}'='' AND IFNULL(`tickets`.`region`,'') NOT IN ('{$other_groups['regions']}')))" : "");
	$group_query .= (trim($other_groups['classifications'],",'") != "" ? " AND (`tickets`.`classification` IN ('{$admin_group['classification']}','') OR ('{$admin_group['classification']}'='' AND `tickets`.`classification` NOT IN ('{$other_groups['classifications']}')))" : "");
	$group_query .= (trim($other_groups['locations'],",'") != "" ? " AND (`tickets`.`con_location` IN ('{$admin_group['location']}','') OR ('{$admin_group['location']}'='' AND `tickets`.`con_location` NOT IN ('{$other_groups['locations']}')))" : "");
	$group_query .= (trim($other_groups['customers'],",0") != '' ? " AND (`tickets`.`businessid` IN ('{$admin_group['customer']}','') OR ('{$admin_group['customer']}'='' AND `tickets`.`businessid` NOT IN ({$other_groups['customers']})))" : "");

	// Get the names of the managers
	$admin_group_managers = [];
	foreach(explode(',',$admin_group['contactid']) as $admin_manager_id) {
		if($admin_manager_id > 0) {
			$admin_group_managers[$admin_manager_id] = get_contact($dbc, $admin_manager_id);
		}
	}
	$manager_count = count($admin_group_managers);

	// Filter out the tickets that do not belong on the current tab
	$status_query = " AND IFNULL(`tickets`.`approvals`,'') NOT LIKE ".($admin_group['precedence'] == 0 ? $precedence_search : $contact_search)." AND IFNULL(`tickets`.`revision_required`,'') NOT LIKE $contact_search";
	$status_query2 = " AND IFNULL(`tickets`.`approvals`,'') NOT LIKE ".($admin_group['precedence'] == 0 ? $precedence_search2 : $contact_search2)." AND IFNULL(`tickets`.`revision_required`,'') NOT LIKE $contact_search2";
	if($status == 'approved') {
		$status_query = " AND IFNULL(`tickets`.`approvals`,'') LIKE ".($admin_group['precedence'] == 0 ? $precedence_search : $contact_search)." AND IFNULL(`tickets`.`revision_required`,'') NOT LIKE $contact_search";
		$status_query2 = " AND IFNULL(`tickets`.`approvals`,'') LIKE ".($admin_group['precedence'] == 0 ? $precedence_search2 : $contact_search2)." AND IFNULL(`tickets`.`revision_required`,'') NOT LIKE $contact_search2";
	} else if($status == 'revision') {
		$status_query = " AND IFNULL(`tickets`.`approvals`,'') NOT LIKE $contact_search AND IFNULL(`tickets`.`revision_required`,'') LIKE $contact_search";
		$status_query2 = " AND IFNULL(`tickets`.`approvals`,'') NOT LIKE $contact_search2 AND IFNULL(`tickets`.`revision_required`,'') LIKE $contact_search2";
	}

	$site_query = '';
	if($filter_site > 0) {
		$site_query = " AND `tickets`.`siteid` = '{$filter_site}'";
	}

	$business_query = '';
	if($filter_business > 0) {
		$business_query = " AND `tickets`.`businessid` = '{$filter_business}'";
	}

	$project_admin_display_completed = get_config($dbc, 'project_admin_display_completed');
	$complete_query = '';
	if($project_admin_display_completed == 1) {
		$ticket_complete_status = get_config($dbc, 'auto_archive_complete_tickets');
		$complete_query .= " AND `tickets`.`status` = '$ticket_complete_status'";
	} else if(!in_array($admin_group['status'],['','NA'])) {
		$complete_query .= " AND `tickets`.`status`='".$admin_group['status']."'";
	}

	if($project_admin_multiday_tickets == 1) {
		$tickets = $dbc->query("SELECT * FROM (SELECT `tickets`.*,`tickets`.`to_do_date` `ticket_date` FROM `tickets` WHERE `tickets`.`deleted`=0 AND (',".$admin_group['action_items'].",' LIKE '%,Tickets,%' OR ',".$admin_group['action_items'].",' LIKE CONCAT('%,ticket_type_',`tickets`.`ticket_type`,',%')) AND IFNULL(`tickets`.`to_do_date`,'') != '' ".$type_query.$status_query2.$project_query.$group_query.$region_query.$site_query.$business_query.$complete_query." UNION SELECT `tickets`.*, `ticket_attached`.`date_stamp` `ticket_date` FROM `tickets` RIGHT JOIN `ticket_attached` ON `tickets`.`ticketid` = `ticket_attached`.`ticketid` WHERE `tickets`.`deleted`=0 AND `ticket_attached`.`deleted`=0 AND `ticket_attached`.`src_table` LIKE 'Staff%' AND IFNULL(`ticket_attached`.`date_stamp`,'') != IFNULL(`tickets`.`to_do_date`,'')".$status_query.$project_query.$group_query.$region_query.$site_query.$business_query.$complete_query." GROUP BY `ticket_attached`.`date_stamp`) `all_tickets` ORDER BY `all_tickets`.`ticketid`");
	} else {
		$tickets = $dbc->query("SELECT *, `tickets`.`to_do_date` `ticket_date` FROM `tickets` WHERE `tickets`.`deleted`=0 AND (',".$admin_group['action_items'].",' LIKE '%,Tickets,%' OR ',".$admin_group['action_items'].",' LIKE CONCAT('%,ticket_type_',`tickets`.`ticket_type`,',%')) ".$type_query.$status_query.$project_query.$group_query.$region_query.$site_query.$business_query.$complete_query);
	}

	return $tickets;
}