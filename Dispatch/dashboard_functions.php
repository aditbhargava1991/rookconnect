<?php
function get_customer_equipment($dbc, $start_date, $end_date) {
	$equipmentids = [];
	for($calendar_date = $start_date; strtotime($calendar_date) <= strtotime($end_date); $calendar_date = date('Y-m-d', strtotime($calendar_date.' + 1 day'))) {
		$equipmentids = array_merge($equipmentids, array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT IFNULL(`ticket_schedule`.`equipmentid`, `tickets`.`equipmentid`) `equipmentid` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid` = `ticket_schedule`.`ticketid` WHERE `tickets`.`deleted` = 0 AND `ticket_schedule`.`deleted` = 0 AND '".$calendar_date."' BETWEEN `tickets`.`to_do_date` AND `tickets`.`to_do_end_date` OR '".$calendar_date."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(`ticket_schedule`.`to_do_end_date`,`ticket_schedule`.`to_do_date`) AND (`tickets`.`businessid` = '".$_SESSION['contactid']."' OR CONCAT(',',`tickets`.`clientid`,',') LIKE '%,".$_SESSION['contactid'].",%')"),MYSQLI_ASSOC),'equipmentid'));
	}
	return $equipmentids;
}
function dispatch_ticket_label($dbc, $ticket, $stop_number) {
	$clickable_html = '';
	if(vuaed_visible_function($dbc, 'ticket') > 0) {
		$clickable_html .= 'onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Ticket/edit_ticket_tab.php?tab=ticket_customer_notes&ticketid='.$ticket['ticketid'].'&stop='.$ticket['stop_id'].'\', \'auto\', true, true, \'auto\', false, \'true\'); return false;"';
	}

	$dispatch_tile_ticket_card_fields = explode(',',get_config($dbc, 'dispatch_tile_ticket_card_fields'));

	$start_time = date('h:i a', strtotime($ticket['to_do_start_time']));
	if(!empty($ticket['to_do_end_time'])) {
		$end_time = date('h:i a', strtotime($ticket['to_do_end_time']));
	} else if (!empty($ticket['max_time']) && $ticket['max_time'] != '00:00:00') {
		$end_time = date('h:i a', strtotime('+'.$max_time_hour.' hours +'.$max_time_minute.' minutes', strtotime($start_time)));
	} else {
		$end_time = date('h:i a', strtotime('+'.($day_period * 2).' minutes', strtotime($start_time)));
	}
	$max_time = $ticket['max_time'];

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
	
	$row_html = '<b>'.(!empty($stop_number) ? $stop_number.'. ' : '').get_ticket_label($dbc, $ticket).$ticket['location_description'].'</b>'.
	(in_array('project',$dispatch_tile_ticket_card_fields) ? '<br />'.PROJECT_NOUN.' #'.$ticket['projectid'].' '.$ticket['project_name'].'<br />' : '').
	(in_array('customer',$dispatch_tile_ticket_card_fields) ? '<br />'.'Customer: '.get_contact($dbc, $ticket['businessid'], 'name') : '').
	(in_array('client',$dispatch_tile_ticket_card_fields) ? '<br />'.'Client: '.$clients : '').
	(in_array('assigned',$dispatch_tile_ticket_card_fields) ? '<br />'.'Staff: '.$assigned_staff : '').
	(in_array('site_address',$dispatch_tile_ticket_card_fields) ? '<br />'.'Site Address: '.$site_address : '').
	(in_array('service_template',$dispatch_tile_ticket_card_fields) ? '<br />'.'Service Template: '.$service_template : '').
	(in_array('start_date',$dispatch_tile_ticket_card_fields) ? '<br />'.'Date: '.$ticket['to_do_date'] : '').
	(in_array('time',$dispatch_tile_ticket_card_fields) ? '<br />'.(!empty($max_time) && $max_time != '00:00:00' ? "(".$max_time.") " : '').$start_time." - ".$end_time : '').
	(in_array('eta',$dispatch_tile_ticket_card_fields) ? '<br />ETA: '.(!empty($max_time) && $max_time != '00:00:00' ? "(".$max_time.") " : '').$start_time." - ".$end_time : '');
	if(in_array('available',$dispatch_tile_ticket_card_fields)) {
		$row_html .= '<br />Time Frame: '.$ticket['availability'];
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
	$row_html .= '<span class="bottom-right no-toggle" title="'.$ticket['status'].' as of '.$ticket['status_date'].($ticket['status_contact'] > 0 ? ' by '.get_contact($dbc, $ticket['status_contact']) : '').'">'.$ticket['status'].' '.date('H:i',strtotime($ticket['status_date'])).'</span>';
    $row_html .= dispatch_delivery_hover_icons($dbc, $ticket, $stop_number, $clickable_html, $dispatch_tile_ticket_card_fields);

	return $row_html;
}

function dispatch_delivery_hover_icons($dbc, $ticket, $stop_number, $clickable_html, $dispatch_tile_ticket_card_fields) {
    $row_html = '';
	$customer_notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid` = '".$ticket['ticketid']."' AND `src_table` = 'customer_approve' AND `line_id` = '".$ticket['stop_id']."' AND `deleted` = 0"));
	if(in_array('camera',$dispatch_tile_ticket_card_fields)) {
		$existing_photos = [];

		if(file_exists('../Ticket/download/'.$customer_notes['location_to']) && !empty($customer_notes['location_to'])) {
			$photo_file = $customer_notes['location_to'];
			$thumbnail_file = explode('.',$photo_file);
			array_pop($thumbnail_file);
			$thumbnail_file = implode('.',$thumbnail_file).'_thumbnail.jpg';
			if(file_exists('../Ticket/download/'.$thumbnail_file)) {
				$photo_file = $thumbnail_file;
			}
			$existing_photos['Photo of Property'] = WEBSITE_URL.'/Ticket/download/'.$photo_file;
		}
		if(file_exists('../Ticket/download/'.$customer_notes['weight_units']) && !empty($customer_notes['weight_units'])) {
			$photo_file = $customer_notes['weight_units'];
			$thumbnail_file = explode('.',$photo_file);
			array_pop($thumbnail_file);
			$thumbnail_file = implode('.',$thumbnail_file).'_thumbnail.jpg';
			if(file_exists('../Ticket/download/'.$thumbnail_file)) {
				$photo_file = $thumbnail_file;
			}
			$existing_photos['Property Damage Photo'] = WEBSITE_URL.'/Ticket/download/'.$photo_file;
		}
		if(file_exists('../Ticket/download/'.$customer_notes['dimension_units']) && !empty($customer_notes['dimension_units'])) {
			$photo_file = $customer_notes['dimension_units'];
			$thumbnail_file = explode('.',$photo_file);
			array_pop($thumbnail_file);
			$thumbnail_file = implode('.',$thumbnail_file).'_thumbnail.jpg';
			if(file_exists('../Ticket/download/'.$thumbnail_file)) {
				$photo_file = $thumbnail_file;
			}
			$existing_photos['Product Damage Photo'] = WEBSITE_URL.'/Ticket/download/'.$photo_file;
		}
		if(file_exists('../Ticket/download/'.$customer_notes['dimensions']) && !empty($customer_notes['dimensions'])) {
			$photo_file = $customer_notes['dimensions'];
			$thumbnail_file = explode('.',$photo_file);
			array_pop($thumbnail_file);
			$thumbnail_file = implode('.',$thumbnail_file).'_thumbnail.jpg';
			if(file_exists('../Ticket/download/'.$thumbnail_file)) {
				$photo_file = $thumbnail_file;
			}
			$existing_photos['Product Damage Close Up'] = WEBSITE_URL.'/Ticket/download/'.$photo_file;
		}

		$camera_class = '';
		if(!empty($existing_photos)) {
			$camera_class = 'active';
		} else {
			$existing_photos = [''];
		}
		foreach($existing_photos as $photo_label => $photo_file) {
			$row_html .= '<img '.$clickable_html.' src="../img/icons/ROOK-camera-icon.png" class="no-slider inline-img dispatch-equipment-camera '.$camera_class.'" onmouseover="display_camera(this);" onmouseout="hide_camera();" data-label="'.$photo_label.'" data-file="'.$photo_file.'">';	
		}
	}
	if(in_array('signature',$dispatch_tile_ticket_card_fields)) {
		if($customer_notes['signature'] != '') {
			if(!file_exists('../Ticket/export/customer_sign_'.$customer_notes['id'].'.png')) {
				if(!file_exists('export')) {
					mkdir('export',0777,true);
				}
				include_once('../phpsign/signature-to-image.php');
				$signature = sigJsonToImage(html_entity_decode($customer_notes['signature']));
				imagepng($signature, '../Ticket/export/customer_sign_'.$customer_notes['id'].'.png');
			}
		}
		$signature_class = '';
		if(file_exists('../Ticket/export/customer_sign_'.$customer_notes['id'].'.png')) {
			$signature_class = 'active';
		}
		$row_html .= '<img '.$clickable_html.' src="../img/icons/ROOK-signature-icon.png" class="no-slider inline-img dispatch-equipment-signature '.$signature_class.'" onmouseover="display_signature(this);" onmouseout="hide_signature();" data-file="'.WEBSITE_URL.'/Ticket/export/customer_sign_'.$customer_notes['id'].'.png">';
	}
	if(in_array('star_rating',$dispatch_tile_ticket_card_fields)) {
		$rating_html = '';
		if($customer_notes['rate'] > 0 || $customer_notes['contact_info'] > 0) {
            $rating_html .= '<div class="star_rating_hover_html" style="display: none;">';

			$rating = $customer_notes['rate'];
            $rating_html .= '<span><b>Delivery Team:</b><br />';
            for($i = 0; $i < 5; $i++) {
            	if($rating >= 1) {
            		$rating_html .= '<img class="inline-img" src="../img/icons/star.png">';
            	} else if($rating >= 0.5) {
            		$rating_html .= '<img class="inline-img" src="../img/icons/star_half.png">';
            	} else {
            		$rating_html .= '<img class="inline-img" src="../img/icons/star_empty.png">';
            	}
            	$rating -= 1;
            }
            $rating_html .= '</span>';

			$rating = $customer_notes['contact_info'];
            $rating_html .= '<br /><span><b>Delivery Service:</b><br />';
            for($i = 0; $i < 5; $i++) {
            	if($rating >= 1) {
            		$rating_html .= '<img class="inline-img" src="../img/icons/star.png">';
            	} else if($rating >= 0.5) {
            		$rating_html .= '<img class="inline-img" src="../img/icons/star_half.png">';
            	} else {
            		$rating_html .= '<img class="inline-img" src="../img/icons/star_empty.png">';
            	}
            	$rating -= 1;
            }
            $rating_html .= '</span>';

            $rating_html .= '</div>';
		}
		$rating_class = '';
		if(!empty($rating_html)) {
			$rating_class = 'active';
		}

		$row_html .= '<img '.$clickable_html.' src="../img/icons/ROOK-star-icon.png" class="no-slider inline-img dispatch-equipment-rating '.$rating_class.'" onmouseover="display_star_rating(this);" onmouseout="hide_star_rating();">'.$rating_html;
	}
	if(in_array('customer_notes_hover',$dispatch_tile_ticket_card_fields)) {
		$notes_html = [];
		if(!empty($customer_notes['location_from'])) {
			$notes_html[] = '<b>Driver Notes: </b><br />'.trim(trim(html_entity_decode($customer_notes['location_from']),'<p>'),'</p>');
		}
		if(!empty($customer_notes['description'])) {
			$notes_html[] = '<b>Property Damage Notes: </b><br />'.trim(trim(html_entity_decode($customer_notes['description']),'<p>'),'</p>');
		}
		if(!empty($customer_notes['notes'])) {
			$notes_html[] = '<b>Product Damage Notes: </b><br />'.trim(trim(html_entity_decode($customer_notes['notes']),'<p>'),'</p>');
		}
		if(!empty($customer_notes['weight'])) {
			$notes_html[] = '<b>Additional Comments: </b><br />'.trim(trim(html_entity_decode($customer_notes['weight']),'<p>'),'</p>');
		}
		$notes_html = implode('<br />', $notes_html);

		$notes_class = '';
		if(!empty($notes_html)) {
			$notes_class = 'active';
			$notes_html = '<div class="customer_notes_hover_html" style="display: none;">'.$notes_html.'</div>';
		}

		$row_html .= '<img '.$clickable_html.' src="../img/icons/ROOK-reply-icon.png" class="no-slider inline-img dispatch-equipment-customer-notes '.$notes_class.'" onmouseover="display_customer_notes(this);" onmouseout="hide_customer_notes();">'.$notes_html;
	}
    return $row_html;
}
function draw_svg_truck($ontime, $notontime, $ongoing) {
	$total_stops = $ontime + $notontime + $ongoing;

	$curr_x = 62;
	$ontime_polygon = '';
	$notontime_polygon = '';
	$ongoing_polygon = '';
	if($ontime > 0) {
		$width = ceil(($ontime / $total_stops) * 126);
		if(($width + $curr_x) > 188) {
			$width = 188 - $curr_x;
		}
		$ontime_polygon = '<polygon points="'.$curr_x.',12 '.$curr_x.',66 '.($width+$curr_x).',66 '.($width+$curr_x).',12" stroke-linejoin="round" style="fill:#00ff00;fill-rule:evenodd;"/>';
		$curr_x += $width;
	}
	if($notontime > 0) {
		$width = ceil(($notontime / $total_stops) * 126);
		if(($width + $curr_x) > 188) {
			$width = 188 - $curr_x;
		}
		$notontime_polygon = '<polygon points="'.$curr_x.',12 '.$curr_x.',66 '.($width+$curr_x).',66 '.($width+$curr_x).',12" stroke-linejoin="round" style="fill:#ff0000;fill-rule:evenodd;"/>';
		$curr_x += $width;
	}
	if($ongoing > 0) {
		$width = ceil(($ongoing / $total_stops) * 126);
		if(($width + $curr_x) > 188) {
			$width = 188 - $curr_x;
		}
		$ongoing_polygon = '<polygon points="'.$curr_x.',12 '.$curr_x.',66 '.($width+$curr_x).',66 '.($width+$curr_x).',12" stroke-linejoin="round" style="fill:#ddd;fill-rule:evenodd;"/>';
		$curr_x += $width;
	}
	$truck_html = '<svg height="100" width="200">
		<polygon points="40,30 25,55 10,65 10,85 182,85 182,68 60,68 60,30" stroke-linejoin="round" style="fill:#777;stroke:black;stroke-width:5;fill-rule:evenodd;"/>
		<circle cx="40" cy="85" r="10" stroke="black" stroke-width="5" fill="white" />
		<circle cx="155" cy="85" r="10" stroke="black" stroke-width="5" fill="white" />
		<polygon points="60,10 60,68 190,68 190,10" stroke-linejoin="round" style="fill:white;stroke:black;stroke-width:5;fill-rule:evenodd;"/>'.
		$ontime_polygon.$notontime_polygon.$ongoing_polygon.
	'</svg>';
	return $truck_html;
}
function get_equipment_color($equipmentid) {
	$color = substr(md5(encryptIt($equipmentid)), 0, 6);

	$r = hexdec(substr($color,0,2));
	$g = hexdec(substr($color,2,2));
	$b = hexdec(substr($color,4,2));
	$yiq = (($r*299)+($g*587)+($b*114))/1000;

	if($yiq >= 128) {
		$darken_percentage = 255 - $yiq;
		$color = darken_color($color, $darken_percentage);
	}

	return $color;
}