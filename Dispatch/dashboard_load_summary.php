<?php include_once('../include.php');
include_once('../Dispatch/dashboard_functions.php');
include_once('../Dispatch/config.php');
ob_clean();

$daily_date = $_POST['date'];
$equipment = json_decode($_POST['equipment']);

$equip_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT *, CONCAT(' #', `unit_number`) label FROM `equipment` WHERE `equipmentid` IN (".implode(',',$equipment).")"),MYSQLI_ASSOC);

//POPULATE
$summary_result = [];
foreach($equip_list as $equipment) {
	$equip_assign = mysqli_fetch_array(mysqli_query($dbc, "SELECT ea.*, e.*, ea.`notes`, ea.`classification` FROM `equipment_assignment` ea LEFT JOIN `equipment` e ON ea.`equipmentid` = e.`equipmentid` WHERE e.`equipmentid` = '".$equipment['equipmentid']."' AND ea.`deleted` = 0 AND DATE(`start_date`) <= '$daily_date' AND DATE(ea.`end_date`) >= '$daily_date' AND CONCAT(',',ea.`hide_days`,',') NOT LIKE '%,$daily_date,%' ORDER BY ea.`start_date` DESC, ea.`end_date` ASC, e.`category`, e.`unit_number`"));
	$equipment_assignmentid = $equip_assign['equipment_assignmentid'];

    $status_summary = [];
    $ontime_summary = [];
    $star_summary = '';
    $star_contacts = [];

	$assigned_team = $dbc->query("SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid`='$equipment_assignmentid' AND `deleted`=0");
	while($contact = $assigned_team->fetch_assoc()) {
		$star_contacts[] = $contact['contactid'];
	}

	$warehouse_query = '';
    if(!($combine_warehouses > 0) && $dont_count_warehouse > 0) {
        $warehouse_query = " AND IFNULL(`ticket_schedule`.`type`,'') NOT LIKE '%warehouse%' AND REPLACE(REPLACE(IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''),IFNULL(`tickets`.`city`,''))),' ',''),'-','') NOT IN (SELECT REPLACE(REPLACE(CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')),' ',''),'-','') FROM `contacts` WHERE `category`='Warehouses')";
    } else if($combine_warehouses == 1) {
		$warehouse_query = " AND IFNULL(`ticket_schedule`.`type`,'') NOT LIKE '%warehouse%' AND REPLACE(REPLACE(IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''),IFNULL(`tickets`.`city`,''))),' ',''),'-','') NOT IN (SELECT REPLACE(REPLACE(CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')),' ',''),'-','') FROM `contacts` WHERE `category`='Warehouses')";
		if($dont_count_warehouse > 0) {
            
        } else {
			$all_warehouses_sql = "SELECT `tickets`.*, `ticket_schedule`.`id` `stop_id`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`equipment_assignmentid`,`tickets`.`equipment_assignmentid`) `equipment_assignmentid`, IFNULL(`ticket_schedule`.`teamid`,`tickets`.`teamid`) `teamid`, IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`) `contactid`, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`id`, 0) `ticket_scheduleid`, IFNULL(`ticket_schedule`.`last_updated_time`,`tickets`.`last_updated_time`) `last_updated_time`, CONCAT(' - ',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name`, IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),' ',IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''), IFNULL(`tickets`.`city`,''))) `warehouse_full_address`, `tickets`.`businessid` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND IFNULL(IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`),'') != '' AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done') AND (REPLACE(REPLACE(IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''),IFNULL(`tickets`.`city`,''))),' ',''),'-','') IN (SELECT REPLACE(REPLACE(CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')),' ',''),'-','') FROM `contacts` WHERE `category`='Warehouses') OR IFNULL(`ticket_schedule`.`type`,'') LIKE '%warehouse%') ".$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query.$ticket_customer_query;
			$warehouse_tickets = mysqli_fetch_all(mysqli_query($dbc, $all_warehouses_sql),MYSQLI_ASSOC);

	        if(!empty($warehouse_tickets)) {
	        	foreach($warehouse_tickets as $ticket) {
	        		$summary_result['status_summary'][$ticket['status']]['count']++;
	        		$summary_result['status_summary'][$ticket['status']]['color'] = $ticket_status_color[$ticket['status']];
	        		$summary_result['status_summary'][$ticket['status']]['status'] = $ticket['status'];
	        	}
	        }
	    }
	}

	$pickup_query = '';
	if($combine_pickups == 1) {
		$pickup_query = " AND `ticket_schedule`.`type` != 'Pick Up'";
		$all_pickups_sql = "SELECT `tickets`.*, `ticket_schedule`.`id` `stop_id`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`equipment_assignmentid`,`tickets`.`equipment_assignmentid`) `equipment_assignmentid`, IFNULL(`ticket_schedule`.`teamid`,`tickets`.`teamid`) `teamid`, IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`) `contactid`, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`id`, 0) `ticket_scheduleid`, IFNULL(`ticket_schedule`.`last_updated_time`,`tickets`.`last_updated_time`) `last_updated_time`, CONCAT(' - ',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name`, IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),' ',IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''), IFNULL(`tickets`.`city`,''))) `pickup_full_address`, `tickets`.`businessid` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done') AND `ticket_schedule`.`type` = 'Pick Up'".$warehouse_query.$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query.$ticket_customer_query;
		$pickup_tickets = mysqli_fetch_all(mysqli_query($dbc, $all_pickups_sql),MYSQLI_ASSOC);

        if(!empty($pickup_tickets)) {
        	foreach($pickup_tickets as $ticket) {
        		if(empty($ticket_status_color[$ticket['status']])) {
        			$ticket_status_color[$ticket['status']] = substr(md5(encryptIt($ticket['status'])), 0, 6);
        		}
        		$summary_result['status_summary'][$ticket['status']]['count']++;
        		$summary_result['status_summary'][$ticket['status']]['color'] = $ticket_status_color[$ticket['status']];
        		$summary_result['status_summary'][$ticket['status']]['status'] = $ticket['status'];
        	}
        }
	}

	$all_tickets_sql = "SELECT `tickets`.*, `ticket_schedule`.`id` `stop_id`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`equipment_assignmentid`,`tickets`.`equipment_assignmentid`) `equipment_assignmentid`, IFNULL(`ticket_schedule`.`teamid`,`tickets`.`teamid`) `teamid`, IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`) `contactid`, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`id`, 0) `ticket_scheduleid`, IFNULL(`ticket_schedule`.`last_updated_time`,`tickets`.`last_updated_time`) `last_updated_time`, CONCAT(' - ',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name`, IFNULL(`ticket_schedule`.`address`,`tickets`.`pickup_address`) `pickup_address`, IFNULL(`ticket_schedule`.`city`,`tickets`.`pickup_city`) `pickup_city`, `ticket_schedule`.`notes` `delivery_notes`, `tickets`.`businessid`, CONCAT(`start_available`,' - ',`end_available`) `availability`, `ticket_schedule`.`end_available` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done')".$warehouse_query.$pickup_query.$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query.$ticket_customer_query;
	$tickets = mysqli_fetch_all(mysqli_query($dbc, $all_tickets_sql),MYSQLI_ASSOC);

	foreach($tickets as $ticket) {
		if(empty($ticket_status_color[$ticket['status']])) {
			$ticket_status_color[$ticket['status']] = substr(md5(encryptIt($ticket['status'])), 0, 6);
		}
		$status = $ticket['status'];
		$summary_result['status_summary'][$ticket['status']]['count']++;
		$summary_result['status_summary'][$ticket['status']]['color'] = $ticket_status_color[$ticket['status']];
		$summary_result['status_summary'][$ticket['status']]['status'] = $ticket['status'];

		$customer_notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid` = '".$ticket['ticketid']."' AND `src_table` = 'customer_approve' AND `line_id` = '".$ticket['stop_id']."' AND `deleted` = 0"));
		$time_compare = $ticket['to_do_date'].(!empty($ticket['end_available']) ? date('H:i:s', strtotime($ticket['end_available'])) : date('H:i:s', strtotime($ticket['to_do_start_time'].' + '.$delivery_timeframe_default.' hours')));
		$customer_notes['completed_time'] = empty(str_replace('0000-00-00 00:00:00','',$customer_notes['completed_time'])) ? date('Y-m-d H:i:s') : convert_timestamp_mysql($dbc, $customer_notes['completed_time']);
		if(strtotime($customer_notes['completed_time']) > strtotime($time_compare) && strtotime(date('Y-m-d H:i:s')) > strtotime($time_compare)) {
			$summary_result['ontime_summary']['Out Of Window']['count']++;
			$summary_result['ontime_summary']['Out Of Window']['label'] = 'Out Of Window';
		} else if($customer_notes['completed'] == 1 && strtotime($customer_notes['completed_time']) <= strtotime($time_compare)) {
			$summary_result['ontime_summary']['On Time']['count']++;
			$summary_result['ontime_summary']['On Time']['label'] = 'On Time';
		} else {
			$summary_result['ontime_summary']['Ongoing']['count']++;
			$summary_result['ontime_summary']['Ongoing']['label'] = 'Ongoing';
		}
	}

	$title_color = get_equipment_color($equipment['equipmentid']);

	//Summary blocks
	foreach(array_filter(array_unique($star_contacts)) as $star_contact) {
	    $star_html = '';
		if($star_contact > 0) {
            $rating = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`ticket_schedule`.`id`) `num_rows`, `ticket_schedule`.`id`, `ticket_attached`.`rate`, AVG(`ticket_attached`.`rate`) `avg_rating` FROM `ticket_schedule` LEFT JOIN `equipment_assignment` ON `equipment_assignment`.`equipmentid` = `ticket_schedule`.`equipmentid` LEFT JOIN `equipment_assignment_staff` ON `equipment_assignment_staff`.`equipment_assignmentid` = `equipment_assignment`.`equipment_assignmentid` LEFT JOIN `ticket_attached` ON `ticket_attached`.`src_table`='customer_approve' AND `ticket_attached`.`line_id`=`ticket_schedule`.`id` WHERE `ticket_schedule`.`to_do_date` BETWEEN `equipment_assignment`.`start_date` AND IFNULL(NULLIF(`equipment_assignment`.`end_date`,'0000-00-00'),'9999-12-31') AND CONCAT(',',`equipment_assignment`.`hide_days`,',') NOT LIKE CONCAT('%,',`ticket_schedule`.`to_do_date`,',%') AND `equipment_assignment_staff`.`contactid` = '$star_contact' AND `equipment_assignment_staff`.`deleted` = 0 AND `ticket_schedule`.`deleted` = 0 AND `equipment_assignment`.`deleted` = 0 AND `ticket_attached`.`rate` != 0"));
            $rating_html = '';
            for($i = 0; $i < 5; $i++) {
            	if($rating['avg_rating'] >= 1) {
            		$rating_html .= '<img class="inline-img" src="../img/icons/star.png">';
            	} else if($rating['avg_rating'] >= 0.5) {
            		$rating_html .= '<img class="inline-img" src="../img/icons/star_half.png">';
            	} else {
            		$rating_html .= '<img class="inline-img" src="../img/icons/star_empty.png">';
            	}
            	$rating['avg_rating'] -= 1;
            }
			$contact = $dbc->query("SELECT `contacts`.`tile_name`, `contacts`.`first_name`, `contacts`.`last_name`, `contacts_upload`.`contactimage` FROM `contacts` LEFT JOIN `contacts_upload` ON `contacts`.`contactid`=`contacts_upload`.`contactid` WHERE `contacts`.`contactid`='$star_contact'")->fetch_assoc();
			$img_url = '../'.($contact['tile_name'] == 'staff' || $contact['category'] == 'staff' ? (file_exists('../Staff/download/'.$contact['contactimage']) ? 'Staff' : 'Profile') : 'Contacts').'/download/'.$contact['contactimage'];
			$star_html .= '<div class="small dispatch-summary-star" data-contact="'.$star_contact.'"><div class="pull-left"><img class="inline-img" src="'.(!empty($contact['contactimage']) && file_exists($img_url) ? $img_url : '../img/person.PNG').'" style="width: auto; height:6em; max-height:6em;"></div>'.decryptIt($contact['first_name']).' '.decryptIt($contact['last_name']).' - '.get_config($dbc, 'company_name').'<br />
					'.$rating_html.'<br />
					'.$rating['num_rows'].' Deliveries Completed</div>';
			$star_html .= '<div class="clearfix"></div>';
			$summary_result['star_summary'][$star_contact] = $star_html;
		}
	}
}

$equipmentid = $_POST['equipmentid'];

$border_styling = '';
$label = 'All';
if($equipmentid == 'VISIBLE') {
	$label = 'Visible';
} else if($equipmentid != 'ALL') {
	$title_color = substr(md5(encryptIt($equipmentid)), 0, 6);
	$border_styling = 'style="border: 3px solid #'.$title_color.'"';
	$label = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT *, CONCAT(' #', `unit_number`) label FROM `equipment` WHERE `equipmentid` = '$equipmentid'"))['label'];
}

$summary_htmls[] = '<div class="dispatch-summary-block" data-equipment="'.$equipmentid.'" '.$border_styling.'><div class="dispatch-summary-ontime"></div></div>';
$summary_htmls[] = '<div class="dispatch-summary-block" data-equipment="'.$equipmentid.'" '.$border_styling.'><div class="dispatch-summary-status"></div></div>';
$summary_htmls[] = '<div class="dispatch-summary-block" data-equipment="'.$equipmentid.'" '.$border_styling.'><b>Star Ratings</b>'.implode('',$summary_result['star_summary']).'</div>';

$ontime_summary_arr = [
	[
		'label' => 'On Time',
		'count' => empty($summary_result['ontime_summary']['On Time']['count']) ? 0 : $summary_result['ontime_summary']['On Time']['count'],
		'color' => '#00ff00'
	],
	[
		'label' => 'Out Of Window',
		'count' => empty($summary_result['ontime_summary']['Out Of Window']['count']) ? 0 : $summary_result['ontime_summary']['Out Of Window']['count'],
		'color' => '#ff0000'
	],
	[
		'label' => 'Ongoing',
		'count' => empty($summary_result['ontime_summary']['Ongoing']['count']) ? 0 : $summary_result['ontime_summary']['Ongoing']['count'],
		'color' => '#ddd'
	]
];

$status_summary_arr = [];
foreach($summary_result['status_summary'] as $status) {
	$status_summary_arr[] = [
		'status' => $status['status'],
		'count' => $status['count'],
		'color' => $status['color']
	];
}

$result_list = [
	'label' => $label,
	'equipmentid' => $equipmentid,
	'summary' => '<div class="dispatch-summary-group" data-equipment="'.$equipmentid.'">'.implode('',$summary_htmls).'</div>',
	'status_summary' => $status_summary_arr,
	'ontime_summary' => $ontime_summary_arr
];
echo json_encode($result_list);