<?php include_once('../include.php');
include_once('../Ticket/field_list.php');

if(!empty($_GET['reload_table'])) {
	ob_clean();
	$ticketid = $_GET['ticketid'];
	$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
	$value_config = get_field_config($dbc, 'tickets');
	if($get_ticket['ticket_type'] != '') {
		$value_config .= get_config($dbc, 'ticket_fields_'.$get_ticket['ticket_type']).',';
	}

	//Action Mode Fields
	if($_GET['action_mode'] == 1) {
		$value_config_all = $value_config;
		$value_config = ','.get_config($dbc, 'ticket_action_fields').',';
		if(!empty($get_ticket['ticket_type'])) {
			$value_config .= get_config($dbc, 'ticket_action_fields_'.$get_ticket['ticket_type']).',';
		}
		if(empty(trim($value_config,','))) {
			$value_config = $value_config_all;
		} else {
			foreach($action_mode_ignore_fields as $action_mode_ignore_field) {
				if(strpos(','.$value_config_all.',',','.$action_mode_ignore_field.',') !== FALSE) {
					$value_config .= ','.$action_mode_ignore_field;
				}
			}
			$value_config = ','.implode(',',array_intersect(explode(',',$value_config), explode(',',$value_config_all))).',';
		}
	}

	$query_daily = "";
	if(strpos($value_config,',Time Tracking Current,') !== FALSE) {
		$query_daily = " AND `date_stamp`='".date('Y-m-d')."' ";
	}
	if(isset($_GET['min_view'])) {
		$value_config = $min_view;
	}

	$ticket_roles = explode('#*#',get_config($dbc, 'ticket_roles'));
	$ticket_role = mysqli_query($dbc, "SELECT `position` FROM `ticket_attached` WHERE `src_table`='Staff' AND `position`!='' AND `item_id`='".$_SESSION['contactid']."' AND `ticketid`='$ticketid' AND `ticketid` > 0 $query_daily");
	if($get_ticket['to_do_date'] > date('Y-m-d') && strpos($value_config,',Ticket Edit Cutoff,') !== FALSE && $config_visible_function($dbc, 'ticket') < 1) {
		$access_services = false;
	} else if($get_ticket['status'] == 'Archive' || $force_readonly) {
		$access_services = false;
	} else if(config_visible_function($dbc, 'ticket') > 0) {
		$access_services = check_subtab_persmission($dbc, 'ticket', ROLE, 'services');
	} else if((count($ticket_roles) > 1 || explode('|',$ticket_roles[0])[0] != '') && mysqli_num_rows($ticket_role) > 0) {
		$ticket_role = html_entity_decode(mysqli_fetch_assoc($ticket_role)['position']);
		foreach($ticket_roles as $ticket_role_level) {
			$ticket_role_level = explode('|',html_entity_decode($ticket_role_level));
			if($ticket_role_level[0] > 0) {
				$ticket_role_level[0] = get_positions($dbc, $ticket_role_level[0], 'name');
			}
			if($ticket_role_level[0] == $ticket_role) {
				$access_services = in_array('services',$ticket_role_level);
			}
		}
	} else if(count(array_filter($arr, function ($var) { return (strpos($var, 'default') !== false); })) > 0) {
		foreach($ticket_roles as $ticket_role_level) {
			$ticket_role_level = explode('|',$ticket_role_level);
			if(in_array('default',$ticket_role_level)) {
				$access_services = in_array('services',$ticket_role_level);
			}
		}
	} else {
		$access_services = check_subtab_persmission($dbc, 'ticket', ROLE, 'services');
	}

	$category = $_POST['category'];
	$service_type = $_POST['service_type'];
	$services = [];
	foreach(explode(',',(!empty($_GET['serviceid']) ? $_GET['serviceid'] : mysqli_fetch_array(mysqli_query($dbc, "SELECT `serviceid` FROM `tickets` WHERE `ticketid`='$ticketid'"))[0])) as $i => $serviceid) {
		if($serviceid > 0) {
			$service = $dbc->query("SELECT * FROM `services` WHERE `serviceid`='$serviceid'")->fetch_assoc();
			if($service['category'] == $category && $service['service_type'] == $service_type) {
				$services[$service['serviceid']] = ['service'=>$service, 'quantity'=>explode(',',$get_ticket['service_qty'])[$i],'fuel_surcharge'=>explode(',',$get_ticket['service_fuel_charge'])[$i],'total_time'=>explode(',',$get_ticket['service_total_time'])[$i]];
			}
		}
	}
	$panel_i = $_GET['panel_i'];
} ?>
<table id="service_group_table_<?= $panel_i ?>" class="table table-bordered customer_rate_services_group">
	<tr class="hidden-sm hidden-xs">
		<?php if(strpos($value_config,',Service Heading,') !== FALSE) { ?>
			<th>Heading</th>
		<?php } ?>
		<?php if(strpos($value_config,',Service Estimated Hours,') !== FALSE) { ?>
			<th style="width:10%;">Time Estimate</th>
		<?php } ?>
		<?php if(strpos($value_config,',Service Fuel Charge,') !== FALSE) { ?>
			<th style="width:10%;">Fuel Surcharge</th>
		<?php } ?>
		<th style="width: 10%;">Add Service</th>
	</tr>
	<?php $all_services = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `services` WHERE `category` = '$category' AND `service_type` = '$service_type' AND `deleted` = 0 ORDER BY `heading`"),MYSQLI_ASSOC);
	foreach($all_services as $all_service) { ?>
		<tr>
			<?php if(strpos($value_config,',Service Heading,') !== FALSE) { ?>
				<td data-title="Heading"><?= $all_service['heading'] ?></td>
			<?php } ?>
			<?php if(strpos($value_config,',Service Estimated Hours,') !== FALSE) {
				$estimated_hours = empty($all_service['estimated_hours']) ? '00:00' : $all_service['estimated_hours'];
				$qty = empty($services[$all_service['serviceid']]['quantity']) ? 1 : $services[$alL_service['serviceid']]['quantity'];
				$minutes = explode(':', $estimated_hours);
				$minutes = ($minutes[0]*60) + $minutes[1];
				$minutes = $qty * $minutes;
				$new_hours = $minutes / 60;
				$new_minutes = $minutes % 60;
				$new_hours = sprintf('%02d', $new_hours);
				$new_minutes = sprintf('%02d', $new_minutes);
				$estimated_hours = $new_hours.':'.$new_minutes;
				$total_time_estimate += $minutes; ?>
				<td data-title="Time Estimate" class="<?= !$access_services ? 'readonly-block' : '' ?>"><input name="service_estimated_hours" value="<?= $estimated_hours ?>" type="text" class="timepicker-5 form-control" disabled /></td>
			<?php } ?>
			<?php if(strpos($value_config,',Service Fuel Charge,') !== FALSE) { ?>
				<td data-title="Fuel Surcharge" class="<?= !$access_services ? 'readonly-block' : '' ?>"><input type="number" min=0 step="any" class="form-control" name="service_fuel_charge" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," value="<?= $services[$all_service['serviceid']]['fuel_surcharge'] ?: 0 ?>" <?= array_key_exists($all_service['serviceid'],$services) ? '' : 'disabled' ?> <?= !$access_services ? 'readonly' : '' ?>></td>
			<?php } ?>
			<td data-title="Include" class="<?= !$access_services ? 'readonly-block' : '' ?>"><label class="form-checkbox"><input type="checkbox" name="serviceid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," class="serviceid" value="<?= $all_service['serviceid'] ?>" <?= array_key_exists($all_service['serviceid'],$services) ? 'checked' : '' ?> onchange="ticketServiceUpdated(this);" <?= !$access_services ? 'readonly' : '' ?>></label></td>
			<?php if(strpos($value_config,',Service Quantity,') !== FALSE || strpos($value_config,',Service # of Rooms,') !== FALSE) { ?>
				<input type="hidden" name="service_qty" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," value="<?= $services[$all_service['serviceid']]['quantity'] > 0 ? $services[$all_service['serviceid']]['quantity'] : 1 ?>" <?= array_key_exists($all_service['serviceid'],$services) ? '' : 'disabled' ?>>
			<?php } ?>
		</tr>
	<?php } ?>
</table>