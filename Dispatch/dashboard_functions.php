<?php
function get_customer_equipment($dbc, $start_date, $end_date) {
	$equipmentids = [];
	for($calendar_date = $start_date; strtotime($calendar_date) <= strtotime($end_date); $calendar_date = date('Y-m-d', strtotime($calendar_date.' + 1 day'))) {
		$equipmentids = array_merge($equipmentids, array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT IFNULL(`ticket_schedule`.`equipmentid`, `tickets`.`equipmentid`) `equipmentid` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid` = `ticket_schedule`.`ticketid` WHERE `tickets`.`deleted` = 0 AND `ticket_schedule`.`deleted` = 0 AND '".$calendar_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$calendar_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`) AND (`tickets`.`businessid` = '".$_SESSION['contactid']."' OR CONCAT(',',`tickets`.`clientid`,',') LIKE '%,".$_SESSION['contactid'].",%')"),MYSQLI_ASSOC),'equipmentid'));
	}
	return $equipmentids;
}
function dispatch_ticket_label($dbc, $ticket) {
	$dispatch_tile_ticket_card_fields = explode(',',get_config($dbc, 'dispatch_tile_ticket_card_fields'));

	$clients = [];
	foreach(array_filter(explode(',',$ticket['clientid'])) as $clientid) {
		$client = !empty(get_client($dbc, $clientid)) ? get_client($dbc, $clientid) : get_contact($dbc, $clientid);
		if(!empty($client) && $client != '-') {
			$clients[] = $client;
		}
	}
	$clients = implode(', ',$clients);

	$site = $ticket['siteid'];
	$site_address = '';
	if($site > 0) {
        $site_address = html_entity_decode(mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '".$ticket['siteid']."'"))['address']);
	}
	$service_template = '';
	if($ticket['service_templateid'] > 0) {
		$service_template = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` FROM `services_service_templates` WHERE `templateid` = '".$ticket['service_templateid']."'"))['name'];
	}
	$assigned_staff = [];
	foreach(array_unique(array_filter(explode(',',$ticket['contactid']))) as $contactid) {
		$assigned_staff[] = get_contact($dbc, $contactid);
	}
	$assigned_staff = implode(', ',$assigned_staff);
	
	$row_html = '<b>'.get_ticket_label($dbc, $ticket).$ticket['location_description'].'</b>'.
	(in_array('project',$dispatch_tile_ticket_card_fields) ? '<br />'.PROJECT_NOUN.' #'.$ticket['projectid'].' '.$ticket['project_name'].'<br />' : '').
	(in_array('customer',$dispatch_tile_ticket_card_fields) ? '<br />'.'Customer: '.get_contact($dbc, $ticket['businessid'], 'name') : '').
	(in_array('client',$dispatch_tile_ticket_card_fields) ? '<br />'.'Client: '.$clients : '').
	(in_array('assigned',$dispatch_tile_ticket_card_fields) ? '<br />'.'Staff: '.$assigned_staff : '').
	(in_array('site_address',$dispatch_tile_ticket_card_fields) ? '<br />'.'Site Address: '.$site_address : '').
	(in_array('service_template',$dispatch_tile_ticket_card_fields) ? '<br />'.'Service Template: '.$service_template : '').
	(in_array('start_date',$dispatch_tile_ticket_card_fields) ? '<br />'.'Date: '.$ticket['to_do_date'] : '');
	if(in_array('available',$dispatch_tile_ticket_card_fields)) {
		$row_html .= '<br />Availability: '.$ticket['availability'];
	}
	$row_html .= (in_array('address',$dispatch_tile_ticket_card_fields) ? '<br />'.$ticket['pickup_name'].($ticket['pickup_name'] != '' ? '<br />' : ' ').$ticket['pickup_address'].($ticket['pickup_address'] != '' ? '<br />' : ' ').$ticket['pickup_city'] : '');
	if(in_array('status',$dispatch_tile_ticket_card_fields)) {
		$row_html .= '<br />'."Status: ".$ticket['status'];
	}
	if(in_array('ticket_notes',$dispatch_tile_ticket_card_fields)) {
		$ticket_notes = mysqli_query($dbc, "SELECT * FROM `ticket_comment` WHERE `ticketid` = '".$ticket['ticketid']."' AND `deleted` = 0");
		if(mysqli_num_rows($ticket_notes) > 0) {
			$row_html .= "<br />Notes: ";
			while($ticket_note = mysqli_fetch_assoc($ticket_notes)) {
				$row_html .= "<br />".trim(trim(html_entity_decode($ticket_note['comment']),"<p>"),"</p>")."<br />";
				$row_html .= "<em>Added by ".get_contact($dbc, $ticket_note['created_by'])." at ".$ticket_note['created_date']."</em>";
			}
		}
	}
	if(in_array('delivery_notes',$dispatch_tile_ticket_card_fields) && !empty($ticket['delivery_notes'])) {
		$row_html .= '<br />Delivery Notes: '.html_entity_decode($ticket['delivery_notes']);
	}
	$row_html .= '<div class="clearfix"></div>';
	if(in_array('camera',$dispatch_tile_ticket_card_fields)) {
		$customer_notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid` = '".$ticket['ticketid']."' AND `src_table` = 'customer_approve' AND `line_id` = '".$ticket['stop_id']."' AND `deleted` = 0"));
		$camera_class = '';
		if(file_exists('../Ticket/download/'.$customer_notes['location_to']) && !empty($customer_notes['location_to'])) {
			$camera_class = 'active';
		}
		$row_html .= '<img src="../img/camera.png" class="inline-img dispatch-equipment-camera '.$camera_class.'" onmouseover="display_camera(this);" onmouseout="hide_camera();" data-file="'.WEBSITE_URL.'/Ticket/download/'.$customer_notes['location_to'].'">';
	}
	if(in_array('signature',$dispatch_tile_ticket_card_fields)) {
		$customer_notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid` = '".$ticket['ticketid']."' AND `src_table` = 'customer_approve' AND `line_id` = '".$ticket['stop_id']."' AND `deleted` = 0"));
		$signature_class = '';
		if(file_exists('../Ticket/export/customer_sign_'.$customer_notes['id'].'.png')) {
			$signature_class = 'active';
		}
		$clickable_html = '';
		if(vuaed_visible_function($dbc, 'ticket') > 0) {
			$clickable_html .= 'onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Ticket/edit_ticket_tab.php?tab=ticket_customer_notes&ticketid='.$ticket['ticketid'].'&stop='.$ticket['stop_id'].'\', \'auto\', true, true, \'auto\', false, \'true\'); return false;"';
		}
		$row_html .= '<img '.$clickable_html.' src="../img/icons/star.png" class="black-color no-slider inline-img dispatch-equipment-signature '.$signature_class.'" onmouseover="display_signature(this);" onmouseout="hide_signature();" data-file="'.WEBSITE_URL.'/Ticket/export/customer_sign_'.$customer_notes['id'].'.png">';
	}

	return $row_html;
}