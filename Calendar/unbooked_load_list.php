<?php include_once('../include.php');
checkAuthorised('calendar_rook');
include_once('../Calendar/calendar_settings_inc.php');
ob_clean();
if($_GET['wait_list'] == 'ticket' || $_GET['wait_list'] == 'ticket_multi') {
	if($_GET['load_all'] == 1) {
		if($_GET['type'] == 'schedule') {
			$ticket_list = mysqli_query($dbc, "SELECT * FROM (SELECT `ticketid`, '' `sub_label`, 'tickets' `table`, 'ticketid' `id_field`, `tickets`.`ticketid` `id_value`, `tickets`.`heading`, `tickets`.`projectid`, `project`.`project_name`, `tickets`.`contactid`, `tickets`.`businessid`, `tickets`.`region`, `tickets`.`con_location`, `tickets`.`classification`, `tickets`.`preferred_staff`, `tickets`.`pickup_start_available`, `tickets`.`pickup_end_available`, `tickets`.`pickup_name`, '' `client_name`, `tickets`.`pickup_address`, `tickets`.`pickup_city`, `tickets`.`status`, `tickets`.`to_do_date`, `project`.`projecttype`, '' `location_name`, `tickets`.`clientid` FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` WHERE `tickets`.`status` NOT IN ('Archive','Archived') AND `tickets`.`deleted`=0 AND `tickets`.`ticketid` NOT IN (SELECT `ticketid` FROM `ticket_schedule` WHERE `deleted`=0) $allowed_ticket_types_query UNION
				SELECT `tickets`.`ticketid`, `ticket_schedule`.`sort`+1 `sub_label`, 'ticket_schedule' `table`, 'id' `id_field`, `ticket_schedule`.`id` `id_value`, `tickets`.`heading`, `tickets`.`projectid`, `project`.`project_name`, `ticket_schedule`.`contactid`, `tickets`.`businessid`, `tickets`.`region`, `tickets`.`con_location`, `tickets`.`classification`, `tickets`.`preferred_staff`, `ticket_schedule`.`start_available`, `ticket_schedule`.`end_available`, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name`, `ticket_schedule`.`address`, `ticket_schedule`.`city`, `ticket_schedule`.`status`, `ticket_schedule`.`to_do_date`, `project`.`projecttype`, `ticket_schedule`.`location_name`, `tickets`.`clientid` FROM `ticket_schedule` LEFT JOIN `tickets` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `tickets`.`deleted`=0 LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` WHERE `tickets`.`status` NOT IN ('Archive','Archived') AND `ticket_schedule`.`deleted`=0 $allowed_ticket_types_query) `schedule` ORDER BY IF(`schedule`.`to_do_date` = '0000-00-00', '', `schedule`.`to_do_date`) <> '' DESC, `schedule`.`to_do_date` ASC, `schedule`.`ticketid` DESC, `schedule`.`sub_label` ASC");
		} else {
			$ticket_list = mysqli_query($dbc, "SELECT `ticketid`, '' `sub_label`, 'tickets' `table`, 'ticketid' `id_field`, `tickets`.`ticketid` `id_value`, `tickets`.`heading`, `tickets`.`projectid`, `project`.`project_name`, `tickets`.`contactid`, `tickets`.`businessid`, `tickets`.`region`, `tickets`.`con_location`, `tickets`.`classification`, `tickets`.`preferred_staff`, `tickets`.`pickup_start_available`, `tickets`.`pickup_end_available`, `tickets`.`pickup_name`, '' `client_name`, `tickets`.`pickup_address`, `tickets`.`pickup_city`, `tickets`.`status`, `tickets`.`to_do_date`, `project`.`projecttype`, `tickets`.`clientid` FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` WHERE `tickets`.`status` NOT IN ('Archive','Archived') ANd `tickets`.`deleted`=0 $allowed_ticket_types_query ORDER BY IF(`to_do_date` = '0000-00-00', '', `to_do_date`) <> '' DESC, `to_do_date` ASC, `ticketid` DESC, `sub_label` ASC");
		}
	} else {
		if($_GET['type'] == 'schedule') {
			$ticket_list = mysqli_query($dbc, "SELECT * FROM (SELECT `ticketid`, '' `sub_label`, 'tickets' `table`, 'ticketid' `id_field`, `tickets`.`ticketid` `id_value`, `tickets`.`heading`, `tickets`.`projectid`, `project`.`project_name`, `tickets`.`contactid`, `tickets`.`businessid`, `tickets`.`region`, `tickets`.`con_location`, `tickets`.`classification`, `tickets`.`preferred_staff`, `tickets`.`pickup_start_available`, `tickets`.`pickup_end_available`, `tickets`.`pickup_name`, '' `client_name`, `tickets`.`pickup_address`, `tickets`.`pickup_city`, `tickets`.`status`, `tickets`.`to_do_date`, `project`.`projecttype`, '' `location_name`, `tickets`.`clientid` FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` WHERE `tickets`.`status` NOT IN ('Archive','Archived') AND (IFNULL(`to_do_date`,'0000-00-00') IN ('0000-00-00','') OR `tickets`.`status` = 'To Be Scheduled' OR IFNULL(`to_do_start_time`,'00:00:00') IN ('00:00:00','')) AND `tickets`.`deleted`=0 AND `tickets`.`ticketid` NOT IN (SELECT `ticketid` FROM `ticket_schedule` WHERE `deleted`=0) $allowed_ticket_types_query UNION
				SELECT `tickets`.`ticketid`, `ticket_schedule`.`sort`+1 `sub_label`, 'ticket_schedule' `table`, 'id' `id_field`, `ticket_schedule`.`id` `id_value`, `tickets`.`heading`, `tickets`.`projectid`, `project`.`project_name`, `ticket_schedule`.`contactid`, `tickets`.`businessid`, `tickets`.`region`, `tickets`.`con_location`, `tickets`.`classification`, `tickets`.`preferred_staff`, `ticket_schedule`.`start_available`, `ticket_schedule`.`end_available`, `ticket_schedule`.`location_name`, `ticket_schedule`.`client_name`, `ticket_schedule`.`address`, `ticket_schedule`.`city`, `ticket_schedule`.`status`, `ticket_schedule`.`to_do_date`, `project`.`projecttype`, `ticket_schedule`.`location_name`, `tickets`.`clientid` FROM `ticket_schedule` LEFT JOIN `tickets` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `tickets`.`deleted`=0 LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` WHERE `tickets`.`status` NOT IN ('Archive','Archived') AND (IFNULL(`ticket_schedule`.`to_do_date`,'0000-00-00') IN ('0000-00-00','') OR `tickets`.`status` = 'To Be Scheduled' OR IFNULL(`ticket_schedule`.`to_do_start_time`,'00:00:00') IN ('00:00:00','')) AND `ticket_schedule`.`deleted`=0 $allowed_ticket_types_query) `schedule` ORDER BY IF(`schedule`.`to_do_date` = '0000-00-00', '', `schedule`.`to_do_date`) <> '' DESC, `schedule`.`to_do_date` ASC, `schedule`.`ticketid` DESC, `schedule`.`sub_label` ASC");
		} else {
			$ticket_list = mysqli_query($dbc, "SELECT `ticketid`, '' `sub_label`, 'tickets' `table`, 'ticketid' `id_field`, `tickets`.`ticketid` `id_value`, `tickets`.`heading`, `tickets`.`projectid`, `project`.`project_name`, `tickets`.`contactid`, `tickets`.`businessid`, `tickets`.`region`, `tickets`.`con_location`, `tickets`.`classification`, `tickets`.`preferred_staff`, `tickets`.`pickup_start_available`, `tickets`.`pickup_end_available`, `tickets`.`pickup_name`, '' `client_name`, `tickets`.`pickup_address`, `tickets`.`pickup_city`, `tickets`.`status`, `tickets`.`to_do_date`, `project`.`projecttype`, `tickets`.`clientid` FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` WHERE `tickets`.`status` NOT IN ('Archive','Archived') AND (IFNULL(`to_do_date`,'0000-00-00') IN ('0000-00-00','') OR `tickets`.`status` = 'To Be Scheduled' OR REPLACE(IFNULL(`tickets`.`contactid`,''),',','') = '' $allowed_ticket_types_query ".($_GET['type'] == 'ticket' ? '' : "OR IFNULL(`to_do_start_time`,'00:00:00') IN ('00:00:00','')")." OR (`tickets`.`staff_capacity` > 0 AND `tickets`.`staff_capacity` > (SELECT COUNT(`id`) `num_rows` FROM `ticket_attached` WHERE `src_table` = 'Staff' AND `deleted` = 0 AND `ticketid` = `tickets`.`ticketid`))) AND `tickets`.`deleted`=0 ORDER BY IF(`to_do_date` = '0000-00-00', '', `to_do_date`) <> '' DESC, `to_do_date` ASC, `ticketid` DESC, `sub_label` ASC");
		}
	}
	$projecttype_filters = [];
	$project_filters = [];
	$region_filters = [];
	$location_filters = [];
	$classification_filters = [];
	$cust_filters = [];
	$client_filters = [];
	$staff_filters = [];
	$status_filters = [];
	$tickets = [];
	while($ticket = mysqli_fetch_array($ticket_list)) {
		//Check region/location/classification
		if(empty($ticket['region']) && $ticket['businessid'] > 0) {
			$ticket['region'] = get_contact($dbc, $ticket['businessid'], 'region');
		}
		if(empty($ticket['con_location']) && $ticket['businessid'] > 0) {
			$ticket['con_location'] = get_contact($dbc, $ticket['businessid'], 'con_locations');
		}
		if(empty($ticket['classification']) && $ticket['businessid'] > 0) {
			$ticket['classification'] = get_contact($dbc, $ticket['businessid'], 'classification');
		}

		//Disable no access tickets
		if((count(array_intersect(explode(',',$ticket['region']), $allowed_regions)) == 0 && !empty($ticket['region'])) || (count(array_intersect(explode(',',$ticket['con_location']), $allowed_locations)) == 0 && !empty($ticket['con_location'])) || (count(array_intersect(explode(',',$ticket['classification']), $allowed_classifications)) == 0 && !empty($ticket['classification']))) {
			continue;
		}
		// if((!in_array($ticket['region'], $allowed_regions) && !empty($ticket['region'])) || (!in_array($ticket['con_location'], $allowed_locations) && !empty($ticket['con_location'])) || (!in_array($ticket['classification'], $allowed_classifications) && !empty($ticket['classification']))) {
		// 	continue;
		// }

		$filter_count['projecttype_filters'][$ticket['projecttype']]++;
		$filter_count['project_filters'][$ticket['projectid']]++;
		foreach(explode(',',$ticket['region']) as $single_region) {
			$filter_count['region_filters'][$single_region]++;
		}
		foreach(explode(',',$ticket['con_location']) as $single_location) {
			$filter_count['location_filters'][$single_location]++;
		}
		foreach(explode(',',$ticket['classification']) as $single_classification) {
			$filter_count['classification_filters'][$single_classification]++;
		}
		$filter_count['cust_filters'][$ticket['businessid']]++;
		foreach(explode(',',$ticket['clientid']) as $single_client) {
			$filter_count['client_filters'][$single_client]++;
		}
		$filter_count['status_filters'][$ticket['status']]++;

		$customer = get_client($dbc, $ticket['businessid']);
		$assigned_staffids = explode(',', trim($ticket['contactid'], ','));
		$assigned_staff = '';
		foreach ($assigned_staffids as $assigned_staffid) {
			$assigned_staff .= get_contact($dbc, $assigned_staffid).', ';
			$filter_count['staff_filters'][$assigned_staffid]++;
		}
		$assigned_staff = rtrim($assigned_staff, ', ');

		$tickets['tickets'][] = [
			'id'=>$ticket['ticketid'],
			'id_field'=>$ticket['id_field'],
			'id_value'=>$ticket['id_value'],
			'text'=>get_ticket_label($dbc, $ticket, null, null, $calendar_ticket_label).' '.$ticket['heading'].' '.$ticket['project_name'].' '.$customer,
			'projecttype'=>$ticket['projecttype'],
			'project'=>$ticket['projectid'],
			'cust'=>$ticket['businessid'],
			'client'=>$ticket['clientid'],
			'staff'=>$ticket['contactid'],
			'staffnames'=>$assigned_staff,
			'region'=>$ticket['region'],
			'location'=>$ticket['con_location'],
			'classification'=>$ticket['classification'],
			'status'=>$ticket['status'],
			'startdate'=>$ticket['to_do_date']
		];
	}
	foreach($filter_count as $filter_type => $filter_counts) {
		foreach ($filter_counts as $id => $count) {
			$tickets['filter_count'][$filter_type][] = ['id' => $id, 'count' => $count];
		}
	}
	echo json_encode($tickets);
}