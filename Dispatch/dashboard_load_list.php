<?php include_once('../include.php');
include_once('../Dispatch/dashboard_functions.php');
include_once('../Dispatch/config.php');
ob_clean();

$daily_date = $_POST['date'];
$active_equipment = json_decode($_POST['active_equipment']);
$equip_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT *, CONCAT(' #', `unit_number`) label FROM `equipment` WHERE `deleted`=0 ".$equip_cat_query." $allowed_equipment_query $customer_query ORDER BY `label`"),MYSQLI_ASSOC);

//POPULATE
$equipment_result = [];
$equipment_buttons = [];
$summary_result = [];
foreach($equip_list as $equipment) {
	$equip_assign = mysqli_fetch_array(mysqli_query($dbc, "SELECT ea.*, e.*, ea.`notes`, ea.`classification` FROM `equipment_assignment` ea LEFT JOIN `equipment` e ON ea.`equipmentid` = e.`equipmentid` WHERE e.`equipmentid` = '".$equipment['equipmentid']."' AND ea.`deleted` = 0 AND DATE(`start_date`) <= '$daily_date' AND DATE(ea.`end_date`) >= '$daily_date' AND CONCAT(',',ea.`hide_days`,',') NOT LIKE '%,$daily_date,%' ORDER BY ea.`start_date` DESC, ea.`end_date` ASC, e.`category`, e.`unit_number`"));
	$equipment_assignmentid = $equip_assign['equipment_assignmentid'];

	$ticket_times = [];
	$equipment_html = '';
    $status_summary = [];
    $ontime_summary = [];
    $star_summary = '';
    $star_contacts = [];

	$warehouse_query = '';
	$warehouse_times = [];
	if($combine_warehouses == 1) {
		$warehouse_query = " AND REPLACE(REPLACE(IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''),IFNULL(`tickets`.`city`,''))),' ',''),'-','') NOT IN (SELECT REPLACE(REPLACE(CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')),' ',''),'-','') FROM `contacts` WHERE `category`='Warehouses')";
		$all_warehouses_sql = "SELECT `tickets`.*, `ticket_schedule`.`id` `stop_id`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`equipment_assignmentid`,`tickets`.`equipment_assignmentid`) `equipment_assignmentid`, IFNULL(`ticket_schedule`.`teamid`,`tickets`.`teamid`) `teamid`, IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`) `contactid`, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`id`, 0) `ticket_scheduleid`, IFNULL(`ticket_schedule`.`last_updated_time`,`tickets`.`last_updated_time`) `last_updated_time`, CONCAT(' - ',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name`, IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),' ',IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''), IFNULL(`tickets`.`city`,''))) `warehouse_full_address`, `tickets`.`businessid` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND IFNULL(IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`),'') != '' AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done') AND REPLACE(REPLACE(IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''),IFNULL(`tickets`.`city`,''))),' ',''),'-','') IN (SELECT REPLACE(REPLACE(CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')),' ',''),'-','') FROM `contacts` WHERE `category`='Warehouses')".$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query.$ticket_customer_query." ORDER BY IFNULL(NULLIF(`ticket_schedule`.`to_do_start_time`,''),IFNULL(NULLIF(`tickets`.`start_time`,'00:00'),`tickets`.`to_do_start_time`)) ASC";
		$warehouse_tickets = mysqli_fetch_all(mysqli_query($dbc, $all_warehouses_sql),MYSQLI_ASSOC);

        $delivery_color = get_delivery_color($dbc, 'warehouse');
        if(!empty($delivery_color)) {
            $delivery_style = 'style="background-color: '.$delivery_color.';"';
        } else {
            $delivery_style = '';
        }
        if(!empty($warehouse_tickets)) {
        	foreach($warehouse_tickets as $ticket) {
        		$warehouse_times[date('H:i', strtotime($ticket['to_do_start_time']))][$ticket['warehouse_full_address']][] = ($ticket['stop_id'] > 0 ? 'ticket_schedule-'.$ticket['stop_id'] : 'tickets-'.$ticket['ticketid']);
        		$status_summary[$ticket['status']]['count']++;
        		$status_summary[$ticket['status']]['color'] = empty($ticket_status_color[$status]) ? '#'.substr(md5(encryptIt($status)), 0, 6) : $ticket_status_color[$status];
        		$status_summary[$ticket['status']]['status'] = $ticket['status'];
        		$summary_result['status_summary'][$ticket['status']]['count']++;
        		$summary_result['status_summary'][$ticket['status']]['color'] = empty($ticket_status_color[$status]) ? '#'.substr(md5(encryptIt($status)), 0, 6) : $ticket_status_color[$status];
        		$summary_result['status_summary'][$ticket['status']]['status'] = $ticket['status'];
        	}
        }
        foreach($warehouse_times as $start_time => $addresses) {
        	foreach($addresses as $address => $ticketids) {
				$row_html = '';
				if($edit_access > 0) {
					$row_html .= '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Calendar/view_warehouse_pickups.php?warehouse='.urlencode($address).'&ticketids='.implode(',',$ticketids).'\', \'auto\', true, true); return false;">';
				}
				$row_html .= '<div class="dispatch-equipment-block" '.$delivery_color.'>';
				$row_html .= '<b>Warehouse: '.$address.' ('.count($ticketids).' Pick Up'.(count($ticketids) > 1 ? 's' : '').')</b>';
				$row_html .= '</div>';
				if($edit_access > 0) {
					$row_html .= '</a>';
				}
				$ticket_times[$start_time][] = $row_html;
			}
        }
	}

	$pickup_query = '';
	$pickup_times = [];
	if($combine_pickups == 1) {
		$pickup_query = " AND `ticket_schedule`.`type` != 'Pick Up'";
		$all_pickups_sql = "SELECT `tickets`.*, `ticket_schedule`.`id` `stop_id`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`equipment_assignmentid`,`tickets`.`equipment_assignmentid`) `equipment_assignmentid`, IFNULL(`ticket_schedule`.`teamid`,`tickets`.`teamid`) `teamid`, IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`) `contactid`, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`id`, 0) `ticket_scheduleid`, IFNULL(`ticket_schedule`.`last_updated_time`,`tickets`.`last_updated_time`) `last_updated_time`, CONCAT(' - ',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name`, IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),' ',IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''), IFNULL(`tickets`.`city`,''))) `pickup_full_address`, `tickets`.`businessid` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done') AND `ticket_schedule`.`type` = 'Pick Up'".$warehouse_query.$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query.$ticket_customer_query." ORDER BY IFNULL(NULLIF(`ticket_schedule`.`to_do_start_time`,''),IFNULL(NULLIF(`tickets`.`start_time`,'00:00'),`tickets`.`to_do_start_time`)) ASC";
		$pickup_tickets = mysqli_fetch_all(mysqli_query($dbc, $all_pickups_sql),MYSQLI_ASSOC);

        $delivery_color = get_delivery_color($dbc, 'Pick Up');
        if(!empty($delivery_color)) {
            $delivery_style = 'style="background-color: '.$delivery_color.';"';
        } else {
            $delivery_style = '';
        }
        if(!empty($pickup_tickets)) {
        	foreach($pickup_tickets as $ticket) {
        		$pickup_times[date('H:i', strtotime($ticket['to_do_start_time']))][$ticket['pickup_full_address']][] = ($ticket['stop_id'] > 0 ? 'ticket_schedule-'.$ticket['stop_id'] : 'tickets-'.$ticket['ticketid']);
        		$status_summary[$ticket['status']]['count']++;
        		$status_summary[$ticket['status']]['color'] = empty($ticket_status_color[$status]) ? '#'.substr(md5(encryptIt($status)), 0, 6) : $ticket_status_color[$status];
        		$status_summary[$ticket['status']]['status'] = $ticket['status'];
        		$summary_result['status_summary'][$ticket['status']]['count']++;
        		$summary_result['status_summary'][$ticket['status']]['color'] = empty($ticket_status_color[$status]) ? '#'.substr(md5(encryptIt($status)), 0, 6) : $ticket_status_color[$status];
        		$summary_result['status_summary'][$ticket['status']]['status'] = $ticket['status'];
        	}
        }
        foreach($pickup_times as $start_time => $addresses) {
        	foreach($addresses as $address => $ticketids) {
				$row_html = '';
				if($edit_access > 0) {
					$row_html .= '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Calendar/view_warehouse_pickups.php?warehouse='.urlencode($address).'&ticketids='.implode(',',$ticketids).'\', \'auto\', true, true); return false;">';
				}
				$row_html .= '<div class="dispatch-equipment-block" '.$delivery_color.'>';
				$row_html .= '<b>Pick Up: '.$address.' ('.count($ticketids).' Pick Up'.(count($ticketids) > 1 ? 's' : '').')</b>';
				$row_html .= '</div>';
				if($edit_access > 0) {
					$row_html .= '</a>';
				}
				$ticket_times[$start_time][] = $row_html;
			}
        }
	}

	$all_tickets_sql = "SELECT `tickets`.*, `ticket_schedule`.`id` `stop_id`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`equipment_assignmentid`,`tickets`.`equipment_assignmentid`) `equipment_assignmentid`, IFNULL(`ticket_schedule`.`teamid`,`tickets`.`teamid`) `teamid`, IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`) `contactid`, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`id`, 0) `ticket_scheduleid`, IFNULL(`ticket_schedule`.`last_updated_time`,`tickets`.`last_updated_time`) `last_updated_time`, CONCAT(' - ',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name`, IFNULL(`ticket_schedule`.`address`,`tickets`.`pickup_address`) `pickup_address`, IFNULL(`ticket_schedule`.`city`,`tickets`.`pickup_city`) `pickup_city`, `ticket_schedule`.`notes` `delivery_notes`, `tickets`.`businessid`, CONCAT(`start_available`,' - ',`end_available`) `availability`, `ticket_schedule`.`end_available` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done')".$warehouse_query.$pickup_query.$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query.$ticket_customer_query." ORDER BY IFNULL(NULLIF(`ticket_schedule`.`to_do_start_time`,''),IFNULL(NULLIF(`tickets`.`start_time`,'00:00'),`tickets`.`to_do_start_time`)) ASC";
	$tickets = mysqli_fetch_all(mysqli_query($dbc, $all_tickets_sql),MYSQLI_ASSOC);

	foreach($tickets as $ticket) {
		$status = $ticket['status'];
		if($calendar_checkmark_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
			$checkmark_ticket = 'dispatch-checkmark-ticket';
		} else {
			$checkmark_ticket = '';
		}
		$status_icon = get_ticket_status_icon($dbc, $ticket['status']);
	    if(!empty($status_icon)) {
	        $icon_img = '';
	    	$icon_background = '';
	    	if($calendar_ticket_status_icon == 'background' && $status_icon != 'initials') {
				$icon_background = " background-image: url(\"".$status_icon."\"); background-repeat: no-repeat; height: 100%; background-size: contain; background-position: center;";
	    	} else {
		    	if($status_icon == 'initials') {
					$icon_img = '<span class="id-circle-small pull-right" style="background-color: #6DCFF6; font-family: \'Open Sans\';">'.get_initials($ticket['status']).'</span>';
		    	} else {
			        $icon_img = '<img src="'.$status_icon.'" class="pull-right" style="max-height: 20px;">';
			    }
			}
	    } else {
	        $icon_img = '';
	    	$icon_background = '';
	    }

	    $row_html = '';
		if($edit_access > 0) {
			$row_html .= '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Ticket/index.php?calendar_view=true&edit='.$ticket['ticketid'].'&stop='.$ticket['stop_id'].'\', \'auto\', true, true); return false;">';
		}
	    $row_html .= "<div class='dispatch-equipment-block ".$checkmark_ticket."' data-equipment='".$equipment['equipmentid']."' data-region='".$ticket['region']."' data-location='".$ticket['con_location']."' data-classification='".$ticket['classification']."' data-businessid='".$ticket['businessid']."' data-status='".$ticket['status']."' ";
	    $row_html .= "style='";
		$delivery_color = get_delivery_color($dbc, $ticket['delivery_type']);
		if($calendar_highlight_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
			$row_html .= 'background-color:'.$calendar_completed_color[$status].';';
		} else if($calendar_highlight_incomplete_tickets == 1 && in_array($status, $calendar_incomplete_status)) {
			$row_html .= 'background-color:'.$calendar_incomplete_color[$status].';';
		} else if(!empty($delivery_color)) {
			$row_html .= "background-color:".$delivery_color.';';
		} else {
			if($ticket['region'] == '') {
				$ticket['region'] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '".$equipment_assignmentid."'"))['region'];
				if($ticket['region'] == '') {
					$ticket['region'] = explode('*#*', mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '".$ticket['equipmentid']."'"))['region'])[0];
				}
			}
			if($ticket['region'] != '') {
				foreach($allowed_regions as $region_line => $region_name) {
					if($region_name == $ticket['region']) {
						$row_html .= "background-color:".$region_colours[$region_line].";";
					}
				}
			}
		}

		$row_html .= $icon_background;
		$row_html .= "'>";
		$row_html .= $icon_img;
		if($ticket_status_color_code == 1 && !empty($ticket_status_color[$status])) {
			$row_html .= '<div class="ticket-status-color" style="background-color: '.$ticket_status_color[$status].';"></div>';
		}
		$row_html .= dispatch_ticket_label($dbc, $ticket);
		$row_html .= "</div>";
		if($edit_access > 0) {
			$row_html .= '</a>';
		}

		$ticket_times[date('H:i', strtotime($ticket['to_do_start_time']))][] = $row_html;

		foreach(array_filter(explode(',',$ticket['contactid'])) as $star_contact) {
			$star_contacts[] = $star_contact;
		}
		$status_summary[$ticket['status']]['count']++;
		$status_summary[$ticket['status']]['color'] = empty($ticket_status_color[$status]) ? '#'.substr(md5(encryptIt($status)), 0, 6) : $ticket_status_color[$status];
		$status_summary[$ticket['status']]['status'] = $ticket['status'];
		$summary_result['status_summary'][$ticket['status']]['count']++;
		$summary_result['status_summary'][$ticket['status']]['color'] = empty($ticket_status_color[$status]) ? '#'.substr(md5(encryptIt($status)), 0, 6) : $ticket_status_color[$status];
		$summary_result['status_summary'][$ticket['status']]['status'] = $ticket['status'];

		$customer_notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid` = '".$ticket['ticketid']."' AND `src_table` = 'customer_approve' AND `line_id` = '".$ticket['stop_id']."' AND `deleted` = 0"));
		$time_compare = $ticket['to_do_date'].(!empty($ticket['end_available']) ? date('H:i:s', strtotime($ticket['end_available'])) : (!empty($ticket['to_do_end_time']) ? date('H:i:s', strtotime($ticket['to_do_end_time'])) : date('H:i:s', strtotime($ticket['to_do_start_time']))));
		$customer_notes['completed_time'] = convert_timestamp_mysql($dbc, $completed_notes['completed_time']);
		if(strtotime(empty(str_replace('0000-00-00 00:00:00','',$customer_notes['completed_time'])) ? date('Y-m-d H:i:s') : $customer_notes['completed_time']) > strtotime($time_compare)) {
			$ontime_summary['Not On Time']['count']++;
			$ontime_summary['Not On Time']['label'] = 'Not On Time';
			$summary_result['ontime_summary']['Not On Time']['count']++;
			$summary_result['ontime_summary']['Not On Time']['label'] = 'Not On Time';
		} else if($customer_notes['completed'] == 1 && strtotime($customer_notes['completed_time']) <= strtotime($time_compare)) {
			$ontime_summary['On Time']['count']++;
			$ontime_summary['On Time']['label'] = 'On Time';
			$summary_result['ontime_summary']['On Time']['count']++;
			$summary_result['ontime_summary']['On Time']['label'] = 'On Time';
		} else {
			$ontime_summary['Ongoing']['count']++;
			$ontime_summary['Ongoing']['label'] = 'Ongoing';
			$summary_result['ontime_summary']['Ongoing']['count']++;
			$summary_result['ontime_summary']['Ongoing']['label'] = 'Ongoing';
		}
	}

	ksort($ticket_times);
	foreach($ticket_times as $ticket_htmls) {
		foreach($ticket_htmls as $ticket_html) {
			$equipment_html .= $ticket_html;
		}
	}

	$title_color = substr(md5(encryptIt($equipment['equipmentid'])), 0, 6);

	if(empty($equipment_html)) {
		$equipment_html = '<div style="margin: 0.5em; padding: 0.5em;">No '.TICKET_TILE.' Found.</div>';
	}
	$equipment_html = '<div data-equipment="'.$equipment['equipmentid'].'" class="dispatch-equipment-group">
            <div class="dispatch-equipment-title" style="background-color: #'.$title_color.'"><a href="" onclick="view_summary(\''.$equipment['equipmentid'].'\'); return false;"><img class="inline-img pull-right btn-horizontal-collapse no-toggle" src="../img/icons/pie-chart.png" title="View Summary" style="margin: 0;"></a><b>'.$equipment['label'].'</b></div>
            <div class="dispatch-equipment-content">'.$equipment_html.'</div>
        </div>';

	//Summary blocks
	$assigned_team = $dbc->query("SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid`='$equipment_assignmentid' AND `deleted`=0");
	while($contact = $assigned_team->fetch_assoc()) {
		$star_contacts[] = $contact['contactid'];
	}
    $star_html = '';
	foreach(array_filter(array_unique($star_contacts)) as $star_contact) {
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
	$star_summary = '<div class="dispatch-summary-block" data-equipment="'.$equipment['equipmentid'].'" style="border: 3px solid #'.$title_color.'"><b>Star Ratings</b>'.$star_html.'</div>';

	$ontime_summary_html = '<div class="dispatch-summary-block" data-equipment="'.$equipment['equipmentid'].'" style="border: 3px solid #'.$title_color.'"><div class="dispatch-summary-ontime"></div></div>';
	$ontime_summary_arr = [
		[
			'label' => 'On Time',
			'count' => empty($ontime_summary['On Time']['count']) ? 0 : $ontime_summary['On Time']['count'],
			'color' => '#00ff00'
		],
		[
			'label' => 'Not On Time',
			'count' => empty($ontime_summary['Not On Time']['count']) ? 0 : $ontime_summary['Not On Time']['count'],
			'color' => '#ff0000'
		],
		[
			'label' => 'Ongoing',
			'count' => empty($ontime_summary['Ongoing']['count']) ? 0 : $ontime_summary['Ongoing']['count'],
			'color' => '#ddd'
		]
	];


	$status_summary_html = '<div class="dispatch-summary-block" data-equipment="'.$equipment['equipmentid'].'" style="border: 3px solid #'.$title_color.'"><div class="dispatch-summary-status"></div></div>';
	$status_summary_arr = [];
	foreach($status_summary as $status) {
		$status_summary_arr[] = [
			'status' => $status['status'],
			'count' => $status['count'],
			'color' => $status['color']
		];
	}

	$equipment_arr = [
		'equipmentid' => $equipment['equipmentid'],
		'label' => $equipment['label'],
		'html' => $equipment_html,
		'summary' => '<div class="dispatch-summary-group" data-equipment="'.$equipment['equipmentid'].'">'.$ontime_summary_html.$status_summary_html.$star_summary.'</div>',
		'status_summary' => $status_summary_arr,
		'ontime_summary' => $ontime_summary_arr
	];
	$equipment_result[] = $equipment_arr;

	$equipment_buttons[] = '<div data-color="#'.$title_color.'" data-equipment="'.$equipment['equipmentid'].'" class="dispatch-equipment-button '.(($reset_active != 1 && (empty($active_equipment) || in_array($equipment['equipmentid'], $active_equipment))) || ($reset_active == 1 && !empty($ticket_times)) ? 'active' : '').'" style="background-color: #'.$title_color.'"><a href="" onclick="filter_equipment(this); return false;">'.$equipment['label'].'</a></div>';
}
$summary_htmls[] = '<div class="dispatch-summary-block" data-equipment="ALL"><div class="dispatch-summary-ontime"></div></div>';
$summary_htmls[] = '<div class="dispatch-summary-block" data-equipment="ALL"><div class="dispatch-summary-status"></div></div>';
$summary_htmls[] = '<div class="dispatch-summary-block" data-equipment="ALL"><b>Star Ratings</b>'.implode('',$summary_result['star_summary']).'</div>';

$ontime_summary_arr = [
	[
		'label' => 'On Time',
		'count' => empty($summary_result['ontime_summary']['On Time']['count']) ? 0 : $summary_result['ontime_summary']['On Time']['count'],
		'color' => '#00ff00'
	],
	[
		'label' => 'Not On Time',
		'count' => empty($summary_result['ontime_summary']['Not On Time']['count']) ? 0 : $summary_result['ontime_summary']['Not On Time']['count'],
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
	'label' => 'All',
	'equipment' => $equipment_result,
	'buttons' => $equipment_buttons,
	'summary' => '<div class="dispatch-summary-group" data-equipment="ALL">'.implode('',$summary_htmls).'</div>',
	'status_summary' => $status_summary_arr,
	'ontime_summary' => $ontime_summary_arr
];
echo json_encode($result_list);