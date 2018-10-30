<?php include_once('../include.php');
include_once('../Dispatch/dashboard_functions.php');
include_once('../Dispatch/config.php');
ob_clean();

$daily_date = $_POST['date'];
$equipmentid = $_POST['equipmentid'];

$equipment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT *, CONCAT(' #', `unit_number`) label, `region` FROM `equipment` WHERE `equipmentid` = '$equipmentid'"));

//POPULATE
$equip_assign = mysqli_fetch_array(mysqli_query($dbc, "SELECT ea.*, e.*, ea.`notes`, ea.`classification` FROM `equipment_assignment` ea LEFT JOIN `equipment` e ON ea.`equipmentid` = e.`equipmentid` WHERE e.`equipmentid` = '".$equipment['equipmentid']."' AND ea.`deleted` = 0 AND DATE(`start_date`) <= '$daily_date' AND DATE(ea.`end_date`) >= '$daily_date' AND CONCAT(',',ea.`hide_days`,',') NOT LIKE '%,$daily_date,%' ORDER BY ea.`start_date` DESC, ea.`end_date` ASC, e.`category`, e.`unit_number`"));
$equipment_assignmentid = $equip_assign['equipment_assignmentid'];

$ticket_times = [];
$equipment_html = '';
$star_contacts = [];
$completed_tickets = 0;
$total_tickets = 0;

$assigned_team = $dbc->query("SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid`='$equipment_assignmentid' AND `deleted`=0");
while($contact = $assigned_team->fetch_assoc()) {
	$star_contacts[] = $contact['contactid'];
}

$warehouse_query = '';
$warehouse_times = [];
if($combine_warehouses == 1) {
	$warehouse_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`tickets`.`ticketid`) num_rows FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND IFNULL(IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`),'') != '' AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done') AND REPLACE(REPLACE(IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''),IFNULL(`tickets`.`city`,''))),' ',''),'-','') IN (SELECT REPLACE(REPLACE(CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')),' ',''),'-','') FROM `contacts` WHERE `category`='Warehouses')"))['num_rows'];
	$warehouse_query = " AND REPLACE(REPLACE(IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''),IFNULL(`tickets`.`city`,''))),' ',''),'-','') NOT IN (SELECT REPLACE(REPLACE(CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')),' ',''),'-','') FROM `contacts` WHERE `category`='Warehouses')";
	$all_warehouses_sql = "SELECT `tickets`.*, `ticket_schedule`.`id` `stop_id`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`equipment_assignmentid`,`tickets`.`equipment_assignmentid`) `equipment_assignmentid`, IFNULL(`ticket_schedule`.`teamid`,`tickets`.`teamid`) `teamid`, IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`) `contactid`, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`id`, 0) `ticket_scheduleid`, IFNULL(`ticket_schedule`.`last_updated_time`,`tickets`.`last_updated_time`) `last_updated_time`, CONCAT(' - ',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name`, IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),' ',IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''), IFNULL(`tickets`.`city`,''))) `warehouse_full_address`, `tickets`.`businessid` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND IFNULL(IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`),'') != '' AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done') AND REPLACE(REPLACE(IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''),IFNULL(`tickets`.`city`,''))),' ',''),'-','') IN (SELECT REPLACE(REPLACE(CONCAT(IFNULL(`address`,''),IFNULL(`city`,'')),' ',''),'-','') FROM `contacts` WHERE `category`='Warehouses')".$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query.$ticket_customer_query." ORDER BY IFNULL(NULLIF(`ticket_schedule`.`to_do_start_time`,''),IFNULL(NULLIF(`tickets`.`start_time`,'00:00'),`tickets`.`to_do_start_time`)) ASC";
	$warehouse_tickets = mysqli_fetch_all(mysqli_query($dbc, $all_warehouses_sql),MYSQLI_ASSOC);

    if(!empty($warehouse_tickets)) {
    	foreach($warehouse_tickets as $ticket) {
    		if(empty($ticket_status_color[$ticket['status']])) {
    			$ticket_status_color[$ticket['status']] = substr(md5(encryptIt($ticket['status'])), 0, 6);
    		}
    		$warehouse_times[date('H:i', strtotime($ticket['to_do_start_time']))][$ticket['warehouse_full_address']][] = ($ticket['stop_id'] > 0 ? 'ticket_schedule-'.$ticket['stop_id'] : 'tickets-'.$ticket['ticketid']);

    		if($dont_count_warehouse != 1) {
	    		$total_tickets++;
	    		if(in_array($ticket['status'],$calendar_checkmark_status)) {
	    			$completed_tickets++;
	    		}
	    	}
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
	$pickup_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`tickets`.`ticketid`) num_rows FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done') AND `ticket_schedule`.`type` = 'Pick Up'"))['num_rows'];
	$pickup_query = " AND `ticket_schedule`.`type` != 'Pick Up'";
	$all_pickups_sql = "SELECT `tickets`.*, `ticket_schedule`.`id` `stop_id`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`equipment_assignmentid`,`tickets`.`equipment_assignmentid`) `equipment_assignmentid`, IFNULL(`ticket_schedule`.`teamid`,`tickets`.`teamid`) `teamid`, IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`) `contactid`, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`id`, 0) `ticket_scheduleid`, IFNULL(`ticket_schedule`.`last_updated_time`,`tickets`.`last_updated_time`) `last_updated_time`, CONCAT(' - ',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name`, IFNULL(NULLIF(CONCAT(IFNULL(`ticket_schedule`.`address`,''),' ',IFNULL(`ticket_schedule`.`city`,'')),''),CONCAT(IFNULL(`tickets`.`address`,''), IFNULL(`tickets`.`city`,''))) `pickup_full_address`, `tickets`.`businessid` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done') AND `ticket_schedule`.`type` = 'Pick Up'".$warehouse_query.$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query.$ticket_customer_query." ORDER BY IFNULL(NULLIF(`ticket_schedule`.`to_do_start_time`,''),IFNULL(NULLIF(`tickets`.`start_time`,'00:00'),`tickets`.`to_do_start_time`)) ASC";
	$pickup_tickets = mysqli_fetch_all(mysqli_query($dbc, $all_pickups_sql),MYSQLI_ASSOC);

    if(!empty($pickup_tickets)) {
    	foreach($pickup_tickets as $ticket) {
    		if(empty($ticket_status_color[$ticket['status']])) {
    			$ticket_status_color[$ticket['status']] = substr(md5(encryptIt($ticket['status'])), 0, 6);
    		}
    		$pickup_times[date('H:i', strtotime($ticket['to_do_start_time']))][$ticket['pickup_full_address']][] = ($ticket['stop_id'] > 0 ? 'ticket_schedule-'.$ticket['stop_id'] : 'tickets-'.$ticket['ticketid']);

    		$total_tickets++;
    		if(in_array($ticket['status'],$calendar_checkmark_status)) {
    			$completed_tickets++;
    		}
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

$all_stop_numbers = [];
$stop_numbers_sql = "SELECT `ticket_schedule`.`id` `stop_id`, `tickets`.`ticketid`, IFNULL(`ticket_schedule`.`status`,`tickets`.`status`) `status` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done')".$warehouse_query.$pickup_query." ORDER BY IFNULL(NULLIF(`ticket_schedule`.`to_do_start_time`,''),IFNULL(NULLIF(`tickets`.`start_time`,'00:00'),`tickets`.`to_do_start_time`)) ASC, `tickets`.`ticketid` ASC, `ticket_schedule`.`id` ASC";
$stop_numbers = mysqli_fetch_all(mysqli_query($dbc, $stop_numbers_sql),MYSQLI_ASSOC);
$stop_num = 1;
$next_stop_id = '';
$next_stop_url = '';
foreach($stop_numbers as $stop_i => $stop) {
	$all_stop_numbers[($stop['stop_id'] > 0 ? 'ticket_schedule,'.$stop['stop_id'] : 'tickets,'.$stop['ticketid'])] = $stop_num++;
	if(!in_array($stop['status'], $calendar_checkmark_status) && (in_array($stop_numbers[$stop_i-1]['status'], $calendar_checkmark_status) || $stop_i == 0)) {
		$next_stop_id = ($stop['stop_id'] > 0 ? 'ticket_schedule,'.$stop['stop_id'] : 'tickets,'.$stop['ticketid']);
	}
}

$all_tickets_sql = "SELECT `tickets`.*, `ticket_schedule`.`id` `stop_id`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`equipment_assignmentid`,`tickets`.`equipment_assignmentid`) `equipment_assignmentid`, IFNULL(`ticket_schedule`.`teamid`,`tickets`.`teamid`) `teamid`, IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`) `contactid`, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`id`, 0) `ticket_scheduleid`, IFNULL(`ticket_schedule`.`last_updated_time`,`tickets`.`last_updated_time`) `last_updated_time`, CONCAT(' - ',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, IFNULL(`ticket_schedule`.`status_date`, `tickets`.`status_date`) `status_date`, IFNULL(`ticket_schedule`.`status_contact`, 0) `status_contact`, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name`, IFNULL(`ticket_schedule`.`address`,`tickets`.`pickup_address`) `pickup_address`, IFNULL(`ticket_schedule`.`city`,`tickets`.`pickup_city`) `pickup_city`, `ticket_schedule`.`notes` `delivery_notes`, `tickets`.`businessid`, CONCAT(`start_available`,' - ',`end_available`) `availability`, `ticket_schedule`.`end_available` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done')".$warehouse_query.$pickup_query.$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query.$ticket_customer_query." ORDER BY IFNULL(NULLIF(`ticket_schedule`.`to_do_start_time`,''),IFNULL(NULLIF(`tickets`.`start_time`,'00:00'),`tickets`.`to_do_start_time`)) ASC, `tickets`.`ticketid` ASC, `ticket_schedule`.`id` ASC";
$tickets = mysqli_fetch_all(mysqli_query($dbc, $all_tickets_sql),MYSQLI_ASSOC);

foreach($tickets as $ticket) {
	if((!empty($ticket['stop_id']) && 'ticket_schedule,'.$ticket['stop_id'] == $next_stop_id) || (empty($ticket['stop_id']) && 'tickets,'.$ticket['ticketid'] == $next_stop_id)) {
		$next_stop_url = WEBSITE_URL.'/Ticket/status_link.php?s='.urlencode(encryptIt(json_encode(['ticket'=>$ticket['ticketid'],'stop'=>$ticket['stop_id']])));
	}
	if(empty($ticket_status_color[$ticket['status']])) {
		$ticket_status_color[$ticket['status']] = substr(md5(encryptIt($ticket['status'])), 0, 6);
	}
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
				$icon_img = '<span class="no-toggle id-circle-small pull-right" style="background-color: #6DCFF6; font-family: \'Open Sans\';" title="'.$ticket['status'].'">'.get_initials($ticket['status']).'</span>';
	    	} else {
		        $icon_img = '<img src="'.$status_icon.'" class="no-toggle pull-right" style="max-height: 20px;" title="'.$ticket['status'].'">';
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
    $row_html .= 'border: 3px solid '.$ticket_status_color[$ticket['status']].';';

	$row_html .= $icon_background;
	$row_html .= "'>";
	$row_html .= $icon_img;
	if($ticket_status_color_code == 1 && !empty($ticket_status_color[$status])) {
		$row_html .= '<div class="ticket-status-color" style="background-color: '.$ticket_status_color[$status].';"></div>';
	}
	$stop_number = '';
	if($ticket['stop_id'] > 0) {
		$stop_number = $all_stop_numbers['ticket_schedule,'.$ticket['stop_id']];
	} else {
		$stop_number = $all_stop_numbers['tickets,'.$ticket['ticketid']];
	}
	$row_html .= dispatch_ticket_label($dbc, $ticket, $stop_number);
	$row_html .= "</div>";
	if($edit_access > 0) {
		$row_html .= '</a>';
	}

	$total_tickets++;
	if(in_array($ticket['status'],$calendar_checkmark_status)) {
		$completed_tickets++;
	}

	$ticket_times[date('H:i', strtotime($ticket['to_do_start_time']))][] = $row_html;

	foreach(array_filter(explode(',',$ticket['contactid'])) as $star_contact) {
		$star_contacts[] = $star_contact;
	}
}

ksort($ticket_times);
foreach($ticket_times as $ticket_htmls) {
	foreach($ticket_htmls as $ticket_html) {
		$equipment_html .= $ticket_html;
	}
}

$title_color = get_equipment_color($equipment['equipmentid']);

if(empty($equipment_html)) {
	$equipment_html = '<div style="margin: 0.5em; padding: 0.5em;">No '.TICKET_TILE.' Found.</div>';
}
$staff_html = [];
foreach(array_filter(array_unique($star_contacts)) as $star_contact) {
	$staff_html[] = ($staff_view_access > 0 ? '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Staff/staff_edit.php?view_only=id_card&contactid='.$star_contact.'\', \'auto\', true, true); return false;">' : '').get_contact($dbc, $star_contact).($staff_view_access > 0 ? '</a>' : '');
}
$staff_html = implode('<br />', $staff_html);
$region_label = '';
if($group_regions == 1) {
	$region_label = ' ('.str_replace('*#*', ', ', $equipment['region']).')';
}
$view_map = '';
if(in_array('view_map', $equipment_fields) && count($tickets) == count($stop_numbers) && $warehouse_count == count($warehouse_tickets) && $pickup_count == count($pickup_tickets)) {
	$view_map = '<a href="" class="pull-right" onclick="get_day_map(\''.$daily_date.'\', \''.$equipment['equipmentid'].'\'); return false;"><img class="inline-img no-toggle white-color" title="View Schedule Map" src="../img/icons/navigation.png"></a>';
}
$next_stop = '';
if(in_array('next_stop', $equipment_fields) && !empty($next_stop_url) && $daily_date == date('Y-m-d')) {
	$next_stop = '<a href="'.$next_stop_url.'" class="pull-right" target="_blank"><img class="inline-img no-toggle white-color" title="View Next Stop" src="../img/icons/navigation.png"></a>';
}
$equipment_html = '<div data-equipment="'.$equipment['equipmentid'].'" class="dispatch-equipment-group">
        <div class="dispatch-equipment-title" style="background-color: #'.$title_color.'"><a href="" onclick="retrieve_summary(this, \''.$equipment['equipmentid'].'\'); return false;"><img class="dispatch-summary-icon inline-img pull-right btn-horizontal-collapse no-toggle white-color" src="../img/icons/pie-chart.png" title="View Summary" style="margin: 0;"></a><b>'.($equipment_edit_access > 0 ? '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Equipment/edit_equipment.php?edit='.$equipment['equipmentid'].'&iframe_slider=1\', \'auto\', true, true); return false;">' : '').$equipment['label'].($equipment_edit_access > 0 ? '</a>' : '').'</b>'.$region_label.'<div class="dispatch-equipment-staff">'.$staff_html.'<br />(Completed '.$completed_tickets.' of '.$total_tickets.' '.TICKET_TILE.')'.$view_map.$next_stop.'</div></div>
        <div class="dispatch-equipment-content">'.$equipment_html.'</div>
    </div>';

$result_list = [
	'equipmentid' => $equipment['equipmentid'],
	'label' => $equipment['label'],
	'html' => $equipment_html
];

echo json_encode($result_list);