<?php include_once('../include.php');
include_once('../Dispatch/config.php');
include_once('../Dispatch/dashboard_functions.php');
ob_clean();

$daily_date = $_POST['date'];
$active_equipment = json_decode($_POST['active_equipment']);
$equip_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT *, CONCAT(`category`, ' #', `unit_number`) label FROM `equipment` WHERE `deleted`=0 ".$equip_cat_query." $allowed_equipment_query $customer_query ORDER BY `label`"),MYSQLI_ASSOC);

//POPULATE
$equipment_result = [];
$equipment_buttons = [];
$summary_result = [];
foreach($equip_list as $equipment) {
	$equip_assign = mysqli_fetch_array(mysqli_query($dbc, "SELECT ea.*, e.*, ea.`notes`, ea.`classification` FROM `equipment_assignment` ea LEFT JOIN `equipment` e ON ea.`equipmentid` = e.`equipmentid` WHERE e.`equipmentid` = '".$equipment['equipmentid']."' AND ea.`deleted` = 0 AND DATE(`start_date`) <= '$daily_date' AND DATE(ea.`end_date`) >= '$daily_date' AND CONCAT(',',ea.`hide_days`,',') NOT LIKE '%,$daily_date,%' ORDER BY ea.`start_date` DESC, ea.`end_date` ASC, e.`category`, e.`unit_number`"));

	$equipment_html = '';
    $summary_html = '';

	$warehouse_query = '';
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
		$equipment_html .= '<div class="dispatch-equipment-block" '.$delivery_color.'>';
		foreach($warehouse_tickets as $ticket) {
            if(strtolower($address) != strtolower($ticket['address'])) {
                $address = $ticket['address'];
                $equipment_html .= '<h4 class="pad-5">Warehouse: '.$address.'</h4>';
            }
            $label = $ticket['client_name'].' - '.$ticket['ticket_label'];
            $equipment_html .= '<span>'.$label.'</span>';

            $equipment_html .= '<label class="form-checkbox any-width pull-right"><input type="checkbox" disabled '.($ticket['status'] == $completed_ticket_status ? 'checked' : '').'>'.$completed_ticket_status.'</label>';
            $equipment_html .= '<div class="clearfix"></div>';
		}
		$equipment_html .= '</div>';
	}

	$pickup_query = '';
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
		$equipment_html .= '<div class="dispatch-equipment-block" '.$delivery_color.'>';
		foreach($warehouse_tickets as $ticket) {
            if(strtolower($address) != strtolower($ticket['address'])) {
                $address = $ticket['address'];
                $equipment_html .= '<h4 class="pad-5">Pick Up: '.$address.'</h4>';
            }
            $label = $ticket['client_name'].' - '.$ticket['ticket_label'];
            $equipment_html .= '<span>'.$label.'</span>';

            $equipment_html .= '<label class="form-checkbox any-width pull-right"><input type="checkbox" disabled '.($ticket['status'] == $completed_ticket_status ? 'checked' : '').'>'.$completed_ticket_status.'</label>';
            $equipment_html .= '<div class="clearfix"></div>';
		}
		$equipment_html .= '</div>';
	}

	$all_tickets_sql = "SELECT `tickets`.*, `ticket_schedule`.`id` `stop_id`, IFNULL(`ticket_schedule`.`to_do_date`,`tickets`.`to_do_date`) `to_do_date`, IFNULL(`ticket_schedule`.`to_do_start_time`,`tickets`.`to_do_start_time`) `to_do_start_time`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, IFNULL(`ticket_schedule`.`to_do_end_time`,`tickets`.`to_do_end_time`) `to_do_end_time`, IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`) `equipmentid`, IFNULL(`ticket_schedule`.`equipment_assignmentid`,`tickets`.`equipment_assignmentid`) `equipment_assignmentid`, IFNULL(`ticket_schedule`.`teamid`,`tickets`.`teamid`) `teamid`, IFNULL(`ticket_schedule`.`contactid`,`tickets`.`contactid`) `contactid`, IF(`ticket_schedule`.`id` IS NULL,'ticket','ticket_schedule') `ticket_table`, IFNULL(`ticket_schedule`.`id`, 0) `ticket_scheduleid`, IFNULL(`ticket_schedule`.`last_updated_time`,`tickets`.`last_updated_time`) `last_updated_time`, CONCAT(' - ',IFNULL(NULLIF(`ticket_schedule`.`location_name`,''),`ticket_schedule`.`client_name`)) `location_description`, IFNULL(`ticket_schedule`.`scheduled_lock`,0) `scheduled_lock`, `ticket_schedule`.`type` `delivery_type`, IFNULL(`ticket_schedule`.`status`, `tickets`.`status`) `status`, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name`, IFNULL(`ticket_schedule`.`address`,`tickets`.`pickup_address`) `pickup_address`, IFNULL(`ticket_schedule`.`city`,`tickets`.`pickup_city`) `pickup_city`, `ticket_schedule`.`notes` `delivery_notes`, `tickets`.`businessid` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 WHERE ('".$daily_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$daily_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`)) AND (IFNULL(`ticket_schedule`.`equipmentid`,`tickets`.`equipmentid`)='".$equipment['equipmentid']."') AND `tickets`.`deleted` = 0 AND `tickets`.`status` NOT IN ('Archive', 'Done')".$warehouse_query.$pickup_query.$allowed_regions_query.$allowed_locations_query.$allowed_classifications_query.$ticket_customer_query." ORDER BY IFNULL(NULLIF(`ticket_schedule`.`to_do_start_time`,''),IFNULL(NULLIF(`tickets`.`start_time`,'00:00'),`tickets`.`to_do_start_time`)) ASC";
	$tickets = mysqli_fetch_all(mysqli_query($dbc, $all_tickets_sql),MYSQLI_ASSOC);

	foreach($tickets as $ticket) {
		$status = $ticket['status'];
		if($calendar_checkmark_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
			$checkmark_ticket = 'calendar-checkmark-ticket';
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

	    $ticket_html = "<div class='dispatch-equipment-block ".$checkmark_ticket."' data-equipment='".$equipment['equipmentid']."' data-region='".$ticket['region']."' data-location='".$ticket['con_location']."' data-classification='".$ticket['classification']."' data-businessid='".$ticket['businessid']."' data-status='".$ticket['status']."' ";
	    $ticket_html .= "style='";
		$delivery_color = get_delivery_color($dbc, $ticket['delivery_type']);
		if($calendar_highlight_tickets == 1 && in_array($status, $calendar_checkmark_status)) {
			$ticket_html .= 'background-color:'.$calendar_completed_color[$status].';';
		} else if($calendar_highlight_incomplete_tickets == 1 && in_array($status, $calendar_incomplete_status)) {
			$ticket_html .= 'background-color:'.$calendar_incomplete_color[$status].';';
		} else if(!empty($delivery_color)) {
			$ticket_html .= "background-color:".$delivery_color.';';
		}

		$ticket_html .= $icon_background;
		$ticket_html .= "'>";
		$ticket_html .= $icon_img;
		if($ticket_status_color_code == 1 && !empty($ticket_status_color[$status])) {
			$ticket_html .= '<div class="ticket-status-color" style="background-color: '.$ticket_status_color[$status].';"></div>';
		}
		$ticket_html .= dispatch_ticket_label($dbc, $ticket);
		$ticket_html .= "</div>";

		$equipment_html .= $ticket_html;
	}

	$title_color = substr(md5(encryptIt($equipment['equipmentid'])), 0, 6);

	if(empty($equipment_html)) {
		$equipment_html = '<div style="margin: 0.5em; padding: 0.5em;">No '.TICKET_TILE.' Found.</div>';
	}
	$equipment_html = '<div data-equipment="'.$equipment['equipmentid'].'" class="dispatch-equipment-group">
            <div class="dispatch-equipment-title" style="background-color: #'.$title_color.'"><b>'.$equipment['label'].'</b><a href="" onclick="view_summary(\''.$eqipment['equipmentid'].'\'); return false;"><img class="inline-img pull-right btn-horizontal-collapse no-toggle" src="../img/icons/pie-chart.png" title="" data-original-title="View Summary" style="margin: 0;"></a></div>
            <div class="dispatch-equipment-content">'.$equipment_html.'</div>
        </div>';

	$equipment_arr = [
		'equipmentid' => $equipment['equipmentid'],
		'label' => $equipment['label'],
		'html' => $equipment_html,
        'summary' => $summary_html
	];
	$equipment_result[] = $equipment_arr;

	$equipment_buttons[] = '<div data-color="#'.$title_color.'" data-equipment="'.$equipment['equipmentid'].'" class="dispatch-equipment-button '.(empty($active_equipment) || in_array($equipment['equipmentid'], $active_equipment) ? 'active' : '').'" style="background-color: #'.$title_color.'"><a href="" onclick="filter_equipment(this); return false;">'.$equipment['label'].'</a></div>';
}

$result_list = [
	'equipment' => $equipment_result,
	'buttons' => $equipment_buttons
];
echo json_encode($result_list);