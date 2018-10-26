<?php include_once('../include.php');
//SETTINGS
$reset_active = get_config($dbc, 'dispatch_tile_reset_active');
$edit_access = vuaed_visible_function($dbc, 'dispatch');
$ticket_view_access = tile_visible($dbc, 'ticket');
$equipment_edit_access = vuaed_visible_function($dbc, 'equipment');
$staff_view_access = tile_visible($dbc, 'staff');

$search_fields = array_filter(explode(',',get_config($dbc, 'dispatch_tile_search_fields')));
$equipment_fields = array_filter(explode(',',get_config($dbc, 'dispatch_tile_equipment_fields')));
$completed_ticket_status = get_config($dbc, 'auto_archive_complete_tickets');
$combine_warehouses = get_config($dbc, 'dispatch_tile_combine_warehouse');
$combine_pickups = get_config($dbc, 'dispatch_tile_combine_pickup');
$contactid = $_SESSION['contactid'];
$contact_regions = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0])));
$region_colours = explode(',',get_config($dbc, '%_region_colour', true));
$contact_security = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts_security` WHERE `contactid`='$contactid'"));
$allowed_regions = array_filter(explode('#*#',$contact_security['region_access']));
if(count($allowed_regions) == 0) {
    $allowed_regions = $contact_regions;
}
$class_regions = explode(',',get_config($dbc, '%_class_regions', true, ','));
$class_logos = explode('*#*',get_config($dbc, '%_class_logos', true, '*#*'));
$contact_classifications = [];
$classification_regions = [];
$classification_logos = [];
$allowed_classifications = array_filter(explode('#*#',$contact_security['classification_access']));
foreach(explode(',',get_config($dbc, '%_classification', true, ',')) as $i => $contact_classification) {
    if(in_array($contact_classification, $allowed_classifications) || empty($allowed_classifications)) {
    	$row = array_search($contact_classification, $contact_classifications);
    	if($class_regions[$i] == 'ALL') {
    		$class_regions[$i] = '';
    	}
    	if($row !== FALSE && $class_regions[$i] != '') {
    		$classification_regions[$row][] = $class_regions[$i];
            $classification_logos[$row] = $class_logos[$i];
    	} else {
    		$contact_classifications[] = $contact_classification;
    		$classification_regions[] = array_filter([$class_regions[$i]]);
            $classification_logos[] = $class_logos[$i];
    	}
    }
}
$allowed_classifications = $contact_classifications;
$contact_locations = array_filter(explode(',', mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `con_locations` FROM `field_config_contacts` WHERE `con_locations` IS NOT NULL"))['con_locations']));
$allowed_locations = array_filter(explode('#*#',$contact_security['location_access']));
if(count($allowed_locations) == 0) {
    $allowed_locations = $contact_locations;
}
$allowed_equipment = $contact_security['equipment_access'];
$allowed_equipment_query = '';
if(!empty($allowed_equipment)) {
    $allowed_equipment_query = " AND `equipmentid` IN ($allowed_equipment)";
}
$allowed_regions_arr = [];
foreach($allowed_regions as $allowed_region) {
	$allowed_regions_arr[] = " CONCAT(',',`tickets`.`region`,',') LIKE '%,$allowed_region,%'";
}
$allowed_regions_arr[] = " IFNULL(`tickets`.`region`,'') = ''";
$allowed_regions_query = " AND (".implode(' OR ', $allowed_regions_arr).")";

$allowed_locations_arr = [];
foreach($allowed_locations as $allowed_location) {
	$allowed_locations_arr[] = " CONCAT(',',`tickets`.`con_location`,',') LIKE '%,$allowed_location,%'";
}
$allowed_locations_arr[] = " IFNULL(`tickets`.`con_location`,'') = ''";
$allowed_locations_query = " AND (".implode(' OR ', $allowed_locations_arr).")";

$allowed_classifications_arr = [];
foreach($allowed_classifications as $allowed_classification) {
	$allowed_classifications_arr[] = " CONCAT(',',`tickets`.`classification`,',') LIKE '%,$allowed_classification,%'";
}
$allowed_classifications_arr[] = " IFNULL(`tickets`.`classification`,'') = ''";
$allowed_classifications_query = " AND (".implode(' OR ', $allowed_classifications_arr).")";
$calendar_checkmark_tickets = get_config($dbc, 'calendar_checkmark_tickets');
$calendar_checkmark_status = get_config($dbc, 'calendar_checkmark_status');
if(empty($calendar_checkmark_status)) {
    $calendar_checkmark_status = ['Complete'];
} else {
    $calendar_checkmark_status = explode('*#*', $calendar_checkmark_status);
}
$calendar_highlight_tickets = get_config($dbc, 'calendar_highlight_tickets');
$calendar_completed_colors = explode('*#*',get_config($dbc, 'calendar_completed_color'));
foreach ($calendar_checkmark_status as $i => $checkmark_status) {
    if(empty($calendar_completed_colors[$i])) {
        $calendar_completed_colors[$i] = '#00ff00';
    }
    $calendar_completed_color[$checkmark_status] = $calendar_completed_colors[$i];
}
if(empty($calendar_completed_color)) {
    $calendar_completed_color = '#00ff00';
}
$calendar_incomplete_status = get_config($dbc, 'calendar_incomplete_status');
if(empty($calendar_incomplete_status)) {
    $calendar_incomplete_status = ['Incomplete'];
} else {
    $calendar_incomplete_status = explode('*#*', $calendar_incomplete_status);
}
$calendar_highlight_incomplete_tickets = get_config($dbc, 'calendar_highlight_incomplete_tickets');
$calendar_incomplete_colors = explode('*#*',get_config($dbc, 'calendar_incomplete_color'));
foreach($calendar_incomplete_status as $i => $incomplete_status) {
    if(empty($calendar_incomplete_colors[$i])) {
        $calendar_incomplete_colors[$i] = '#ff0000';
    }
    $calendar_incomplete_color[$incomplete_status] = $calendar_incomplete_colors[$i];
}
if(empty($calendar_incomplete_color)) {
    $calendar_incomplete_color = '#ff0000';
}
$ticket_status_color = [];
$status_color_codes = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_status_color`"),MYSQLI_ASSOC);
foreach ($status_color_codes as $status_color_code) {
    $ticket_status_color[$status_color_code['status']] = $status_color_code['color'];
}
$customer_roles = array_filter(explode(',',get_config($dbc, 'dispatch_tile_customer_roles')));
$is_customer = false;
foreach(array_filter(explode(',',ROLE)) as $session_role) {
    if(in_array($session_role,$customer_roles)) {
        $is_customer = true;
    }
}
$ticket_customer_query = '';
if($is_customer) {
	$ticket_customer_query = " AND (`tickets`.`businessid` = '".$_SESSION['contactid']."' OR CONCAT(',', `tickets`.`clientid`, ',') LIKE '%,".$_SESSION['contactid'].",%')";
}
$equipment_categories = array_filter(explode(',',get_config($dbc, 'dispatch_tile_equipment_category')));
$equip_cat_query = '';
$equipment_label = 'Equipment';
if(!empty($equipment_categories)) {
	$equip_cat_query = " AND `category` IN ('".implode("','", $equipment_categories)."')";
    if(count($equipment_categories) == 1) {
        $equipment_label = $equipment_categories[0];
    }
}
$customer_query = '';
if($is_customer) {
    $daily_date = !empty($_GET['date']) ? $_GET['date'] : (!empty($_POST['date']) ? $_POST['date'] : date('Y-m-d'));
	$customer_equipments = get_customer_equipment($dbc, $daily_date, $daily_date);
	$customer_query .= " AND `equipmentid` IN (".implode(',', $customer_equipments).")";
}
$dont_count_warehouse = get_config($dbc, 'dispatch_tile_dont_count_warehouse');
$group_regions = get_config($dbc, 'dispatch_tile_group_regions');
$dispatch_tile_ticket_card_fields = explode(',', get_config($dbc, 'dispatch_tile_ticket_card_fields'));
$auto_refresh = get_config($dbc, 'dispatch_tile_auto_refresh');
if(!empty($auto_refresh)) {
    $auto_refresh = date_parse($auto_refresh);
    $auto_refresh = ($auto_refresh['hour'] * 3600) + ($auto_refresh['minute'] * 60);
}
$summary_tab = get_config($dbc, 'dispatch_tile_summary_tab');
$delivery_timeframe_default = !empty(get_config($dbc, 'delivery_timeframe_default')) ? get_config($dbc, 'delivery_timeframe_default') : 3;