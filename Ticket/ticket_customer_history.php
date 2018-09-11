<?php include_once('../include.php');
include_once('../Ticket/field_list.php');

if(!empty($_GET['edit'])) {
	if(IFRAME_MODE) { ?>
		<script type="text/javascript">
		$(document).ready(function() {
			$('.overview-block a').click(function() {
				window.parent.location.href = $(this).prop('href');
				return false;
			});
		});
		</script>
	<?php }
	$ticketid = $_GET['edit'];
	$value_config = get_field_config($dbc, 'tickets');

	$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
	$ticket_type = $get_ticket['ticket_type'];
	$businessid = $get_ticket['businessid'];
	$clientid = $get_ticket['clientid'];
	$projectid = $get_ticket['projectid'];
	$projecttype = get_project($dbc, $get_ticket['projectid'], 'projecttype');
	if(!empty($ticket_type)) {
		$value_config .= get_config($dbc, 'ticket_fields_'.$ticket_type).',';
	}
	$sort_field = 'Customer History';

	$field_list = $accordion_list[$sort_field];
	$field_sort_order = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_fields` WHERE `ticket_type` = '".(empty($ticket_type) ? 'tickets' : 'tickets_'.$ticket_type)."' AND `accordion` = '".$sort_field."'"))['fields'];
	if(empty($field_sort_order)) {
		$field_sort_order = $value_config;
	}
	$field_sort_order = explode(',', $field_sort_order);
	foreach ($field_list as $default_field) {
		if(!in_array($default_field, $field_sort_order)) {
			$field_sort_order[] = $default_field;
		}
	}

	$ticket_type_label = '';
	$ticket_tabs = array_filter(explode(',',get_config($dbc, 'ticket_tabs')));
	foreach($ticket_tabs as $ticket_tab) {
		if(config_safe_str($ticket_tab) == $ticket_type) {
			$ticket_type_label = $ticket_tab;
		}
	}

	$projecttype_label = '';
	$project_tabs = array_filter(explode(',',get_config($dbc, 'project_tabs')));
	foreach($project_tabs as $project_tab) {
		if(config_safe_str($project_tab) == $projecttype) {
			$projecttype_label = $project_tab;
		}
	}

	echo '<h3 style="margin: 20px 20px 0px 20px;">'.get_ticket_label($dbc, $get_ticket).'</h3>';
	$block = '';
	foreach($field_sort_order as $field_sort_field) {
		if(strpos($value_config, ',Customer History Business Ticket Type,') !== FALSE && $field_sort_field == 'Customer History Business Ticket Type') {
			if($businessid > 0) {
				$block .= '<div class="overview-block">';
				$block .= '<h4>'.get_client($dbc, $businessid).' - Last 5 by '.TICKET_NOUN.' Tab'.(!empty($ticket_type_label) ? ' ('.$ticket_type_label.')' : '').'</h4>';
				$tickets = mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticket_type` = '$ticket_type' AND `businessid` = '$businessid' AND `deleted` = 0 AND `ticketid` != '$ticketid' ORDER BY `ticketid` DESC LIMIT 0, 5");
				if(mysqli_num_rows($tickets) > 0) {
					while($row = mysqli_fetch_assoc($tickets)) {
						$block .= get_ticket_block($dbc, $row, $value_config);
					}
				} else {
					$block .= '<p>No '.TICKET_TILE.' Found.</p>';
				}
				$block .= '</div>';
			}
		} else if(strpos($value_config, ',Customer History Business Project Type,') !== FALSE && $field_sort_field == 'Customer History Business Project Type') {
			if($businessid > 0 && $projectid > 0) {
				$block .= '<div class="overview-block">';
				$block .= '<h4>'.get_client($dbc, $businessid).' - Last 5 by '.PROJECT_NOUN.' Tab'.(!empty($projecttype_label) ? ' ('.$projecttype_label.')' : '').'</h4>';
				$tickets = mysqli_query($dbc, "SELECT `tickets`.* FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid` = `project`.`projectid` WHERE `tickets`.`businessid` = '$businessid' AND `project`.`projecttype` = '$projecttype' AND `tickets`.`deleted` = 0 AND `tickets`.`ticketid` != '$ticketid' ORDER BY `tickets`.`ticketid` DESC LIMIT 0, 5");
				if(mysqli_num_rows($tickets) > 0) {
					while($row = mysqli_fetch_assoc($tickets)) {
						$block .= '<p><a style="color:black !important;" href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row['ticketid'].'">'.get_ticket_label($dbc, $row).'</a></p>';
					}
				} else {
					$block .= '<p>No '.TICKET_TILE.' Found.</p>';
				}
				$block .= '</div>';
			}
		} else if(strpos($value_config, ',Customer History Business Ticket Project Type,') !== FALSE && $field_sort_field == 'Customer History Business Ticket Project Type') {
			if($businessid > 0 && $projectid > 0) {
				$block .= '<div class="overview-block">';
				$block .= '<h4>'.get_client($dbc, $businessid).' - Last 5 by '.TICKET_NOUN.' Tab'.(!empty($ticket_type_label) ? ' ('.$ticket_type_label.')' : '').' & '.PROJECT_NOUN.' Type'.(!empty($projecttype_label) ? ' ('.$projecttype_label.')' : '').'</h4>';
				$tickets = mysqli_query($dbc, "SELECT `tickets`.* FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid` = `project`.`projectid` WHERE `tickets`.`businessid` = '$businessid' AND `project`.`projecttype` = '$projecttype' AND `tickets`.`ticket_type` = '$ticket_type' AND `tickets`.`deleted` = 0 AND `tickets`.`ticketid` != '$ticketid' ORDER BY `tickets`.`ticketid` DESC LIMIT 0, 5");
				if(mysqli_num_rows($tickets) > 0) {
					while($row = mysqli_fetch_assoc($tickets)) {
						$block .= '<p><a style="color:black !important;" href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row['ticketid'].'">'.get_ticket_label($dbc, $row).'</a></p>';
					}
				} else {
					$block .= '<p>No '.TICKET_TILE.' Found.</p>';
				}
				$block .= '</div>';
			}
		} else if(strpos($value_config, ',Customer History Customer Ticket Type,') !== FALSE && $field_sort_field == 'Customer History Customer Ticket Type') {
			foreach(explode(',', $clientid) as $client_id) {
				if($client_id > 0) {
					$block .= '<div class="overview-block">';
					$block .= '<h4>'.(!empty(get_client($dbc, $client_id)) ? get_client($dbc, $client_id) : get_contact($dbc, $client_id)).' - Last 5 by '.TICKET_NOUN.' Tab'.(!empty($ticket_type_label) ? ' ('.$ticket_type_label.')' : '').'</h4>';
					$tickets = mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticket_type` = '$ticket_type' AND CONCAT(',',`clientid`,',') LIKE ('%,$client_id,%') AND `deleted` = 0 AND `ticketid` != '$ticketid' ORDER BY `ticketid` DESC LIMIT 0, 5");
					if(mysqli_num_rows($tickets) > 0) {
						while($row = mysqli_fetch_assoc($tickets)) {
							$block .= '<p><a style="color:black !important;" href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row['ticketid'].'">'.get_ticket_label($dbc, $row).'</a></p>';
						}
					} else {
						$block .= '<p>No '.TICKET_TILE.' Found.</p>';
					}
					$block .= '</div>';
				}
			}
		} else if(strpos($value_config, ',Customer History Customer Project Type,') !== FALSE && $field_sort_field == 'Customer History Customer Project Type') {
			foreach(explode(',', $clientid) as $client_id) {
				if($client_id > 0 && $projectid > 0) {
					$block .= '<div class="overview-block">';
					$block .= '<h4>'.(!empty(get_client($dbc, $client_id)) ? get_client($dbc, $client_id) : get_contact($dbc, $client_id)).' - Last 5 by '.PROJECT_NOUN.' Tab'.(!empty($projecttype_label) ? ' ('.$projecttype_label.')' : '').'</h4>';
					$tickets = mysqli_query($dbc, "SELECT `tickets`.* FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid` = `project`.`projectid` WHERE CONCAT(',',`tickets`.`clientid`,',') LIKE ('%,$client_id,%') AND `project`.`projecttype` = '$projecttype' AND `tickets`.`deleted` = 0 AND `tickets`.`ticketid` != '$ticketid' ORDER BY `tickets`.`ticketid` DESC LIMIT 0, 5");
					if(mysqli_num_rows($tickets) > 0) {
						while($row = mysqli_fetch_assoc($tickets)) {
							$block .= '<p><a style="color:black !important;" href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row['ticketid'].'">'.get_ticket_label($dbc, $row).'</a></p>';
						}
					} else {
						$block .= '<p>No '.TICKET_TILE.' Found.</p>';
					}
					$block .= '</div>';
				}
			}
		} else if(strpos($value_config, ',Customer History Customer Ticket Project Type,') !== FALSE && $field_sort_field == 'Customer History Customer Ticket Project Type') {
			foreach(explode(',', $clientid) as $client_id) {
				if($client_id > 0 && $projectid > 0) {
					$block .= '<div class="overview-block">';
					$block .= '<h4>'.(!empty(get_client($dbc, $client_id)) ? get_client($dbc, $client_id) : get_contact($dbc, $client_id)).' - Last 5 by '.TICKET_NOUN.' Tab'.(!empty($ticket_type_label) ? ' ('.$ticket_type_label.')' : '').' & '.PROJECT_NOUN.' Type'.(!empty($projecttype_label) ? ' ('.$projecttype_label.')' : '').'</h4>';
					$tickets = mysqli_query($dbc, "SELECT `tickets`.* FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid` = `project`.`projectid` WHERE CONCAT(',',`tickets`.`clientid`,',') LIKE ('%,$client_id,%') AND `project`.`projecttype` = '$projecttype' AND `tickets`.`ticket_type` = '$ticket_type' AND `tickets`.`deleted` = 0 AND `tickets`.`ticketid` != '$ticketid' ORDER BY `tickets`.`ticketid` DESC LIMIT 0, 5");
					if(mysqli_num_rows($tickets) > 0) {
						while($row = mysqli_fetch_assoc($tickets)) {
							$block .= '<p><a style="color:black !important;" href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row['ticketid'].'">'.get_ticket_label($dbc, $row).'</a></p>';
						}
					} else {
						$block .= '<p>No '.TICKET_TILE.' Found.</p>';
					}
					$block .= '</div>';
				}
			}
		}
	}
	echo $block;
} else {
	echo '<h3 style="margin: 20px 20px 0px 20px;">Invalid '.TICKET_NOUN.'</h3>';
}

function get_ticket_block($dbc, $ticket, $value_config) {
	$html = '';
	if(strpos($value_config, ',Customer History Field Service Template,')) {
		$html .= '<div class="clearfix"></div><div class="form-group">
			<label class="col-sm-4 control-label">Service Template:</label>
			<div class="col-sm-8">'.mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` FROM `services_service_templates` WHERE `templateid` = '".$ticket['service_templateid']."' AND '".$ticket['service_templateid']."' > 0"))['name'].'</div>
		</div>';
	}
	if(strpos($value_config, ',Customer History Field Display Notes,')) {
		$notes_html = '';
		$ticket_notes = mysqli_query($dbc, "SELECT `ticket_comment`.*, `tickets`.`ticket_type` FROM ticket_comment LEFT JOIN `tickets` ON `ticket_comment`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_comment`.ticketid='".$ticket['ticketid']."' AND `ticket_comment`.type='note' AND `ticket_comment`.`deleted`=0 ORDER BY ticketcommid DESC");
		while($row = mysqli_fetch_assoc($ticket_notes)) {
			$notes_html .= profile_id($dbc, $row['created_by'], false);
			$notes_html .= '<div class="pull-right" style="width: calc(100% - 3.5em);">'.html_entity_decode($row['comment'].$row['note']);
			$notes_html .= "<em>Added by ".get_contact($dbc, $row['created_by'])." at ".$row['note_date'].$row['created_date'];
			$notes_html .= "</em>";
			$notes_html .= '</div><div class="clearfix"></div><hr>';
		}
		$html .= '<div class="clearfix"></div><div class="form-group">
			<label class="col-sm-12 control-label">Notes:</label>
			<div class="col-sm-12">'.$notes_html.'</div>
		</div>';
	}

	if(strpos($value_config, ',Customer History Field') !== FALSE) {
		$html = '<div class="block-group"><a style="color:black !important;" href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].'"><h3 style="margin-top: 0;">'.get_ticket_label($dbc, $ticket).'</a></h3>'.$html.'<div class="clearfix"></div></div>';
	} else {
		$html = '<p><a style="color:black !important;" href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].'">'.get_ticket_label($dbc, $ticket).'</a></p>';
	}
	return $html;
}