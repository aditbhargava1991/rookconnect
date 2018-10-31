<?php include_once('../include.php');
include_once('../Ticket/field_list.php');
$delivery_timeframe_default = get_config($dbc, 'delivery_timeframe_default');
$delivery_type_contacts = get_config($dbc, 'delivery_type_contacts'.($ticket_type == '' ? '' : '_'.$ticket_type));
if($delivery_type_contacts == '') {
	$delivery_type_contacts = get_config($dbc, 'delivery_type_contacts');
}
if(isset($_GET['ticketid']) && empty($ticketid)) {
	$strict_view = strictview_visible_function($dbc, 'ticket');
	$tile_security = get_security($dbc, ($_GET['tile_name'] == '' ? 'ticket' : 'ticket_type_'.$_GET['tile_name']));
	if($strict_view > 0) {
		$tile_security['edit'] = 0;
		$tile_security['config'] = 0;
	}
	$value_config = get_field_config($dbc, 'tickets');
	$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
	$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM tickets WHERE ticketid='$ticketid'"));
	$ticket_type = $get_ticket['ticket_type'];
	if(!empty($ticket_type)) {
		$value_config .= get_config($dbc, 'ticket_fields_'.$ticket_type).',';
	}

	//Action Mode Fields
	if($_GET['action_mode'] == 1) {
		$value_config_all = $value_config;
		$value_config = ','.get_config($dbc, 'ticket_action_fields').',';
		if(!empty($ticket_type)) {
			$value_config .= get_config($dbc, 'ticket_action_fields_'.$ticket_type).',';
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

	if($get_ticket['to_do_date'] > date('Y-m-d') && strpos($value_config,',Ticket Edit Cutoff,') !== FALSE && $config_visible_function($dbc, 'ticket') < 1) {
		$access_all = false;
	} else if($get_ticket['status'] == 'Archive') {
		$access_all = false;
	} else if(config_visible_function($dbc, 'ticket') > 0) {
		$access_all = check_subtab_persmission($dbc, 'ticket', ROLE, 'all_access');
	} else if((count($ticket_roles) > 1 || explode('|',$ticket_roles[0])[0] != '') && mysqli_num_rows($ticket_role) > 0) {
		$ticket_role = html_entity_decode(mysqli_fetch_assoc($ticket_role)['position']);
		foreach($ticket_roles as $ticket_role_level) {
			$ticket_role_level = explode('|',html_entity_decode($ticket_role_level));
			if($ticket_role_level[0] > 0) {
				$ticket_role_level[0] = get_positions($dbc, $ticket_role_level[0], 'name');
			}
			if($ticket_role_level[0] == $ticket_role) {
				$access_all = in_array('ticket',$ticket_role_level);
			}
		}
	} else if(count(array_filter($arr, function ($var) { return (strpos($var, 'default') !== false); })) > 0) {
		foreach($ticket_roles as $ticket_role_level) {
			$ticket_role_level = explode('|',$ticket_role_level);
			if(in_array('default',$ticket_role_level)) {
				$access_all = in_array('ticket',$ticket_role_level);
			}
		}
	} else {
		$access_all = check_subtab_persmission($dbc, 'ticket', ROLE, 'all_access');
	}
	$sort_field = 'Delivery';
	$field_list = $accordion_list[$sort_field];
	$field_sort_order = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_fields` WHERE `ticket_type` = '".(empty($ticket_type) ? 'tickets' : 'tickets_'.$ticket_type)."' AND `accordion` = '".$sort_field."'"))['fields'];
	$field_sort_order = explode(',', $field_sort_order);
	foreach ($field_list as $default_field) {
		if(!in_array($default_field, $field_sort_order)) {
			$field_sort_order[] = $default_field;
		}
	}
	$renamed_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($get_ticket['ticket_type']) ? 'tickets' : 'tickets_'.$get_ticket['ticket_type'])."' AND `accordion` = '".$sort_field."'"))['accordion_name'];
	ob_clean();
}
$delivery_restrictions = '';
foreach(array_filter(explode(',', ROLE)) as $contact_role) {
	$restriction_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_delivery_restrictions` WHERE CONCAT(',',`security_level`,',') LIKE '%,$contact_role,%' AND `ticket_type` = '$ticket_type'"));
	if(!empty($restriction_config)) {
		if($restriction_config['to_do_date_min'] != '') {
			$delivery_restrictions['to_do_date_min'] = date('Y-m-d', strtotime(date('Y-m-d').($restriction_config['to_do_date_min'] >= 0 ? '+' : '').$restriction_config['to_do_date_min'].' Days'));
		}
		if($restriction_config['to_do_date_max'] != '') {
			$delivery_restrictions['to_do_date_max'] = date('Y-m-d', strtotime(date('Y-m-d').($restriction_config['to_do_date_max'] >= 0 ? '+' : '').$restriction_config['to_do_date_max'].' Days'));
		}
		if($restriction_config['to_do_start_time_min'] != '') {
			$delivery_restrictions['to_do_start_time_min'] = date('H:i', strtotime(date('H:i').($restriction_config['to_do_start_time_min'] >= 0 ? '+' : '').$restriction_config['to_do_start_time_min'].' Hours'));
		}
		if($restriction_config['to_do_start_time_max'] != '') {
			$delivery_restrictions['to_do_start_time_max'] = date('H:i', strtotime(date('H:i').($restriction_config['to_do_start_time_max'] >= 0 ? '+' : '').$restriction_config['to_do_start_time_max'].' Hours'));
		}
		break;
	}
}
$dbc->query("UPDATE `ticket_schedule` SET `deleted`=1 WHERE `ticketid`='$ticketid' AND IFNULL(`type`,'')='' AND IFNULL(`location_name`,'')='' AND IFNULL(`client_name`,'')='' AND IFNULL(`serviceid`,'')='' AND IFNULL(`address`,'')='' AND IFNULL(`city`,'')='' AND IFNULL(`province`,'')='' AND IFNULL(`postal_code`,'')='' AND IFNULL(`country`,'')='' AND IFNULL(`map_link`,'')='' AND IFNULL(`coordinates`,'')='' AND IFNULL(`est_time`,'')='' AND IFNULL(`details`,'')='' AND IFNULL(`email`,'')='' AND IFNULL(`carrier`,'')='' AND IFNULL(`vendor`,'')='' AND IFNULL(`lading_number`,'')='' AND IFNULL(`volume`,'')='' AND IFNULL(`eta`,'')='' AND IFNULL(`notes`,'')=''"); ?>
<script type="text/javascript">
$(document).ready(function() {
	if(ticketid > 0) {
		$('.scheduled_stop select[name="type"][data-table="ticket_schedule"]').each(function() {
			setHoursOfOperation(this);
		});
	}
});
</script>
<?php $default_services = '';
if(strpos($value_config,',Delivery Pickup Default Services,') !== FALSE) {
	$default_services = $dbc->query("SELECT * FROM `services_service_templates` WHERE `deleted`=0 AND `contactid`='$businessid' AND '$businessid' > 0")->fetch_assoc()['serviceid']; ?>
	<input type="hidden" name="default_services" value="<?= $default_services ?>">
<?php }

//New tickets should not show deleted Services but old tickets with deleted Services should
$query_services = '';
if(strpos($value_config, ',Service Rate Card,') !== FALSE) {
	$services = explode('**',mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `services` FROM `rate_card` WHERE `clientid` IN ('$rate_contact', '$businessid') AND `clientid` != '' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') ORDER BY `clientid`='$rate_contact' DESC"))['services']);
	$service_list = [];
	$service_price = [];
	foreach($services as $service) {
		$service = explode('#',$service);
		if($service[0] > 0) {
			$service_list[] = $service[0];
			$service_price[] = $service[1];
		}
	}
	$query_services = "`serviceid` IN (".implode(',',$service_list).") AND "; ?>
    <script>
    reload_services = true;
    </script>
<?php } ?>
<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Delivery Details</h3>') ?>
<?php if($access_all > 0) {
	$delivery_types = array_filter(explode(',',get_config($dbc, 'delivery_types'))); ?>
	<?php foreach($field_sort_order as $field_sort_field) { ?>
		<?php if (strpos($value_config, ','."Assigned Equipment".',') !== FALSE && $field_sort_field == 'Assigned Equipment') { ?>
			<?php $equipment_list = mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '$equipmentid'");
			$equipment = mysqli_fetch_assoc($equipment_list); ?>
			<?php if(strpos($value_config,',Assigned Equipment Inline,') !== FALSE) { ?>
				<div class="multi-block-assign form-group">
					<div class="hide-titles-mob">
						<label class="text-center col-sm-3">Tab</label>
						<label class="text-center col-sm-3">Make</label>
						<label class="text-center col-sm-3">Model</label>
						<label class="text-center col-sm-3">Unit #</label>
					</div>
					<div class="col-sm-3">
						<label class="control-label show-on-mob">Tab:</label>
						<select name="assign_eq_category" class="chosen-select-deselect"><option></option>
							<?php $groups = mysqli_query($dbc, "SELECT `category` FROM `equipment` WHERE `deleted`=0 GROUP BY `category` ORDER BY `category`");
							while($category = mysqli_fetch_assoc($groups)) { ?>
								<option <?= $equipment['category'] == $category['category'] ? 'selected' : '' ?> value="<?= $category['category'] ?>"><?= $category['category'] ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-sm-3">
						<label class="control-label show-on-mob">Make:</label>
						<select name="assign_eq_make" class="chosen-select-deselect"><option></option>
							<?php $groups = mysqli_query($dbc, "SELECT `make`, `category` FROM `equipment` WHERE `deleted`=0 GROUP BY `make` ORDER BY `make`");
							while($make = mysqli_fetch_assoc($groups)) { ?>
								<option data-category="<?= $make['category'] ?>" <?= $equipment['make'] == $make['make'] ? 'selected' : '' ?> value="<?= $make['make'] ?>"><?= $make['make'] ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-sm-3">
						<label class="control-label show-on-mob">Model:</label>
						<select name="assign_eq_model" class="chosen-select-deselect"><option></option>
							<?php $groups = mysqli_query($dbc, "SELECT `model`, `make`, `category` FROM `equipment` WHERE `deleted`=0 GROUP BY `model` ORDER BY `model`");
							while($model = mysqli_fetch_assoc($groups)) { ?>
								<option data-category="<?= $model['category'] ?>" data-make="<?= $model['make'] ?>" <?= $equipment['model'] == $model['model'] ? 'selected' : '' ?> value="<?= $model['model'] ?>"><?= $model['model'] ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-sm-3">
						<label class="control-label show-on-mob">Unit #:</label>
						<select id="assigned_equipment" name="equipmentid" data-table="tickets" data-id="<?= $get_ticket['ticketid'] ?>" data-id-field="ticketid" value="<?= $equipment['equipmentid'] ?>" class="chosen-select-deselect"><option></option>
							<?php $groups = mysqli_query($dbc, "SELECT `category`, `make`, `model`, `unit_number`, `equipmentid` FROM `equipment` WHERE `deleted`=0 ORDER BY `category`, `make`, `model`, `unit_number`");
							while($units = mysqli_fetch_assoc($groups)) { ?>
								<option data-category="<?= $units['category'] ?>" data-make="<?= $units['make'] ?>" data-model="<?= $units['model'] ?>" <?= $equipment['equipmentid'] == $units['equipmentid'] ? 'selected' : '' ?> value="<?= $units['equipmentid'] ?>"><?= $units['unit_number'] ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			<?php } else { ?>
				<h4>Assigned Equipment</h4>
				<div class="multi-block-assign">
					<div class="form-group">
						<label class="control-label col-sm-4">Tab:</label>
						<div class="col-sm-8">
							<select name="assign_eq_category" class="chosen-select-deselect"><option></option>
								<?php $groups = mysqli_query($dbc, "SELECT `category` FROM `equipment` WHERE `deleted`=0 GROUP BY `category` ORDER BY `category`");
								while($category = mysqli_fetch_assoc($groups)) { ?>
									<option <?= $equipment['category'] == $category['category'] ? 'selected' : '' ?> value="<?= $category['category'] ?>"><?= $category['category'] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Make:</label>
						<div class="col-sm-8">
							<select name="assign_eq_make" class="chosen-select-deselect"><option></option>
								<?php $groups = mysqli_query($dbc, "SELECT `make`, `category` FROM `equipment` WHERE `deleted`=0 GROUP BY `make` ORDER BY `make`");
								while($make = mysqli_fetch_assoc($groups)) { ?>
									<option data-category="<?= $make['category'] ?>" <?= $equipment['make'] == $make['make'] ? 'selected' : '' ?> value="<?= $make['make'] ?>"><?= $make['make'] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Model:</label>
						<div class="col-sm-8">
							<select name="assign_eq_model" class="chosen-select-deselect"><option></option>
								<?php $groups = mysqli_query($dbc, "SELECT `model`, `make`, `category` FROM `equipment` WHERE `deleted`=0 GROUP BY `model` ORDER BY `model`");
								while($model = mysqli_fetch_assoc($groups)) { ?>
									<option data-category="<?= $model['category'] ?>" data-make="<?= $model['make'] ?>" <?= $equipment['model'] == $model['model'] ? 'selected' : '' ?> value="<?= $model['model'] ?>"><?= $model['model'] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Unit #:</label>
						<div class="col-sm-8">
							<select id="assigned_equipment" name="equipmentid" data-table="tickets" data-id="<?= $get_ticket['ticketid'] ?>" data-id-field="ticketid" value="<?= $equipment['equipmentid'] ?>" class="chosen-select-deselect"><option></option>
								<?php $groups = mysqli_query($dbc, "SELECT `category`, `make`, `model`, `unit_number`, `equipmentid` FROM `equipment` WHERE `deleted`=0 ORDER BY `category`, `make`, `model`, `unit_number`");
								while($units = mysqli_fetch_assoc($groups)) { ?>
									<option data-category="<?= $units['category'] ?>" data-make="<?= $units['make'] ?>" data-model="<?= $units['model'] ?>" <?= $equipment['equipmentid'] == $units['equipmentid'] ? 'selected' : '' ?> value="<?= $units['equipmentid'] ?>"><?= $units['unit_number'] ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } ?>
		<?php if($field_sort_field == 'Delivery Stops') { ?>
			<?php if(strpos($value_config, ',Delivery Stops') !== FALSE) { ?>
				<div class="delivery_stop_group">
					<h4><?= TICKET_NOUN ?> Stop<img class="inline-img pull-left small black-color" onclick="add_stop();" data-history-label="Delivery Stop" src="../img/icons/ROOK-add-icon.png"><div class="clearfix"></div></h4>
					<div class="form-group">
						<label class="col-sm-4 control-label">Location Name:</label>
						<div class="col-sm-8">
							<input type="text" name="pickup_name" class="form-control" data-table="tickets" data-id="<?= $get_ticket['ticketid'] ?>" data-id-field="ticketid" value="<?= $get_ticket['pickup_name'] ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Address:</label>
						<div class="col-sm-8">
							<input type="text" name="pickup_address" class="form-control" data-table="tickets" data-id="<?= $get_ticket['ticketid'] ?>" data-id-field="ticketid" value="<?= $get_ticket['pickup_address'] ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">City:</label>
						<div class="col-sm-8">
							<input type="text" name="pickup_city" class="form-control" data-table="tickets" data-id="<?= $get_ticket['ticketid'] ?>" data-id-field="ticketid" value="<?= $get_ticket['pickup_city'] ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Postal Code:</label>
						<div class="col-sm-8">
							<input type="text" name="pickup_postal_code" class="form-control" data-table="tickets" data-id="<?= $get_ticket['ticketid'] ?>" data-id-field="ticketid" value="<?= $get_ticket['pickup_postal_code'] ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label"><span class="popover-examples list-inline">
								<a data-toggle="tooltip" data-placement="top" title="" data-original-title="The address must match Google maps format or the link will not populate properly."><img src="../img/info.png" width="20"></a>
							</span>Google Maps Link:</label>
						<div class="col-sm-8">
							<input type="text" name="pickup_link" class="form-control" data-table="tickets" data-id="<?= $get_ticket['ticketid'] ?>" data-id-field="ticketid" value="<?= $get_ticket['pickup_link'] ?>">
							<?php if(!empty($get_ticket['pickup_link'])) {
								echo '<a href="'.$get_ticket['pickup_link'].'">'.$get_ticket['pickup_link'].'</a>';
							} ?>
						</div>
					</div>
					<?php if (strpos($value_config, ','."Delivery Stops Volume".',') !== FALSE) { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">Volume (<?= get_config($dbc, 'volume_units') ?>):</label>
							<div class="col-sm-8">
								<input type="number" min="0" name="pickup_volume" class="form-control" data-table="tickets" data-id="<?= $get_ticket['ticketid'] ?>" data-id-field="ticketid" value="<?= $get_ticket['pickup_volume'] ?>">
							</div>
						</div>
					<?php } ?>
					<div class="form-group">
						<label class="col-sm-4 control-label">Description:</label>
						<div class="col-sm-12">
							<textarea name="pickup_description" class="form-control" data-table="tickets" data-id="<?= $get_ticket['ticketid'] ?>" data-id-field="ticketid"><?= html_entity_decode($get_ticket['pickup_description']) ?></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Date:</label>
						<div class="col-sm-8">
							<input type="text" name="to_do_date" data-table="tickets" data-id="<?= $get_ticket['ticketid'] ?>" data-id-field="ticketid" class="form-control datepicker" value="<?= substr($get_ticket['to_do_date'],0,10) ?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Time:</label>
						<div class="col-sm-8">
							<input type="text" name="to_do_start_time" data-table="tickets" data-id="<?= $get_ticket['ticketid'] ?>" data-id-field="ticketid" class="form-control datetimepicker<?= $calendar_window > 0 ? '-'.$calendar_window : '-30' ?>" value="<?= substr($get_ticket['to_do_start_time'],10) ?>">
						</div>
					</div>
					<?php if (strpos($value_config, ','."Delivery Stops Order".',') !== FALSE) { ?>
						<?php if (strpos($value_config, ','."PI Sales Order".',') !== FALSE) { ?>
							<div class="form-group">
								<label class="col-sm-4 control-label">Order #:</label>
								<div class="col-sm-8">
									<select name="pickup_order" class="chosen-select-deselect form-control" data-table="tickets" data-id="<?= $get_ticket['ticketid'] ?>" data-id-field="ticketid"><option></option>
										<?php $orders = mysqli_query($dbc, "SELECT * FROM `sales_order` WHERE `deleted`=0");
										while($order = mysqli_fetch_assoc($orders)) { ?>
											<option <?= $order['posid'] == $get_ticket['pickup_order'] ? 'selected' : '' ?> value="<?= $order['posid'] ?>">Order #<?= $order['posid'] ?> <?= $order['invoice_date'] ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						<?php } else { ?>
							<div class="form-group">
								<label class="col-sm-4 control-label">Order #:</label>
								<div class="col-sm-8">
									<input type="text" name="pickup_order" class="form-control" data-table="tickets" data-id="<?= $get_ticket['ticketid'] ?>" data-id-field="ticketid" value="<?= $get_ticket['pickup_order'] ?>">
								</div>
							</div>
						<?php } ?>
					<?php } ?>
				</div>
			<?php } else { ?>
				<?php $ticket_stops = mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 AND IFNULL(`type`,'') NOT IN ('origin','destination') $stop_id ORDER BY `sort`");
                if($ticket_stops->num_rows == 0) {
                    $delivery_default_tabs = get_config($dbc, 'delivery_default_tabs'.($ticket_type == '' ? '' : '_'.$ticket_type));
                    if(empty($delivery_default_tabs) && !empty($ticket_type)) {
                        $delivery_default_tabs = get_config($dbc, 'delivery_default_tabs');
                    }
                    if(!empty($delivery_default_tabs)) {
                        $delivery_default_tabs = explode(',',$delivery_default_tabs);
                        $delivery_default_sql = [];
                        foreach($delivery_default_tabs as $delivery_default_tab) {
                            $delivery_default_sql[] = "SELECT '$delivery_default_tab' `type`";
                        }
                        $ticket_stops = mysqli_query($dbc, implode(' UNION ',$delivery_default_sql));
                        echo "<!--".implode(' UNION ',$delivery_default_sql)."-->";
                    }
                }
				if($_GET['new_ticket_calendar'] == 'true' && empty($_GET['edit'])) {
					$stop['equipmentid'] = $_GET['equipmentid'];
					$stop['to_do_date'] = $_GET['current_date'];
					$stop['to_do_start_time'] = !empty($_GET['current_time']) ? date('h:i a', strtotime($_GET['current_time'])) : '';
					$stop['status'] = empty($default_status) ? 'Time Estimate Needed' : $default_status;
					if(strpos($value_config, ',Delivery Pickup Default Services,') !== FALSE) {
						$stop['serviceid'] = $default_services;
					}
					$classification = explode(',',get_field_value('classification','equipment','equipmentid',$_GET['equipmentid']))[0];
					if($classification != '') {
						$class_address = json_decode(html_entity_decode(get_config($dbc, 'equip_class_'.config_safe_str($classification).'_address_start')),true);
						if($class_address['address'] != '' && $class_address['city'] != '') {
							$stop['address'] = implode(', ',array_filter([$class_address['address'],$class_address['address2']]));
							$stop['city'] = $class_address['city'];
							$stop['postal_code'] = $class_address['postal_code'];
						}
					}
				} else {
					$stop = mysqli_fetch_assoc($ticket_stops);
					$stop_count = 0;
					if(empty($_GET['edit']) && empty($_GET['ticketid'])) {
						$stop['status'] = empty($default_status) ? 'Time Estimate Needed' : $default_status;
					}
				}
				$stop_i = 0;
				do {
					$stop_i++;
					if($stop['id'] == $_GET['stop'] || !($_GET['stop'] > 0)) { ?>
						<div id="tab_section_ticket_delivery_<?= $stop['id'] ?>" class="tab-section scheduled_stop">
                            <?php if (strpos($value_config, ','."Delivery Pickup Estimate".',') === FALSE) { ?>
                                <input type="hidden" name="est_time" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= time_decimal2time($stop['est_time'],true) ?>">
                            <?php } ?>
							<?php if(strpos($value_config, ',Delivery Pickup') !== FALSE && $get_ticket['main_ticketid'] == 0) { ?>
								<h4>Scheduled Stop <span class="block_count"><?= ++$stop_count ?> of <?= $ticket_stops->num_rows > 0 ? $ticket_stops->num_rows : 1 ?></span><img class="inline-img small pull-right stop_sort" src="../img/icons/drag_handle.png">
                                    <?php if(!($_GET['action_mode'] == 1)) { ?>
                                        <div class="col-sm-2 pull-right">
                                            <img class="inline-img small pull-right" src="../img/remove.png" data-history-label="Delivery Stop" onclick="remScheduledStop(this);">
                                            <img class="inline-img small black-color pull-right" src="../img/icons/ROOK-add-icon.png" data-history-label="Delivery Stop" onclick="addScheduledStop();">
                                        </div>
                                    <?php } ?>
                                </h4>
								<input type="hidden" name="sort" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['sort'] ?>">
                                <input type="hidden" name="deleted" value="0" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id">
							<?php } ?>
							<?php $equipment_list = mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '{$stop['equipmentid']}'");
							$equipment = mysqli_fetch_assoc($equipment_list);
							$equip_col_count = (strpos($value_config,',Delivery Pickup Equipment Category,') !== FALSE ? 1 : 0) + (strpos($value_config,',Delivery Pickup Equipment Make,') !== FALSE ? 1 : 0) + (strpos($value_config,',Delivery Pickup Equipment Model,') !== FALSE ? 1 : 0) + (strpos($value_config,',Delivery Pickup Equipment,') !== FALSE ? 1 : 0); ?>
							<?php foreach ($field_sort_order as $field_sort_field) { ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Equipment Category".',') !== FALSE && strpos($value_config, ','."Assigned Equipment Inline".',') === FALSE && $field_sort_field == 'Delivery Pickup Equipment Category') { ?>
									<div class="form-group">
										<label class="control-label col-sm-4">Equipment Tab:</label>
										<div class="col-sm-8">
											<select name="stop_eq_category" class="chosen-select-deselect"><option></option>
												<?php $groups = mysqli_query($dbc, "SELECT `category` FROM `equipment` WHERE `deleted`=0 GROUP BY `category` ORDER BY `category`");
												while($category = mysqli_fetch_assoc($groups)) { ?>
													<option <?= $equipment['category'] == $category['category'] ? 'selected' : '' ?> value="<?= $category['category'] ?>"><?= $category['category'] ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Equipment Make".',') !== FALSE && strpos($value_config, ','."Assigned Equipment Inline".',') === FALSE && $field_sort_field == 'Delivery Pickup Equipment Make') { ?>
									<div class="form-group">
										<label class="control-label col-sm-4">Equipment Make:</label>
										<div class="col-sm-8">
											<select name="stop_eq_make" class="chosen-select-deselect"><option></option>
												<?php $groups = mysqli_query($dbc, "SELECT `make`, `category` FROM `equipment` WHERE `deleted`=0 GROUP BY `make` ORDER BY `make`");
												while($make = mysqli_fetch_assoc($groups)) { ?>
													<option data-category="<?= $make['category'] ?>" <?= $equipment['make'] == $make['make'] ? 'selected' : '' ?> value="<?= $make['make'] ?>"><?= $make['make'] ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Equipment Model".',') !== FALSE && strpos($value_config, ','."Assigned Equipment Inline".',') === FALSE && $field_sort_field == 'Delivery Pickup Equipment Model') { ?>
									<div class="form-group">
										<label class="control-label col-sm-4">Equipment Model:</label>
										<div class="col-sm-8">
											<select name="stop_eq_model" class="chosen-select-deselect"><option></option>
												<?php $groups = mysqli_query($dbc, "SELECT `model`, `make`, `category` FROM `equipment` WHERE `deleted`=0 GROUP BY `model` ORDER BY `model`");
												while($model = mysqli_fetch_assoc($groups)) { ?>
													<option data-category="<?= $model['category'] ?>" data-make="<?= $model['make'] ?>" <?= $equipment['model'] == $model['model'] ? 'selected' : '' ?> value="<?= $model['model'] ?>"><?= $model['model'] ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Equipment".',') !== FALSE && strpos($value_config, ','."Assigned Equipment Inline".',') === FALSE && $field_sort_field == 'Delivery Pickup Equipment') { ?>
									<div class="form-group">
										<label class="control-label col-sm-4">Equipment Unit #:</label>
										<div class="col-sm-8">
											<select name="equipmentid" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" class="chosen-select-deselect"><option></option>
												<?php $groups = mysqli_query($dbc, "SELECT `category`, `make`, `model`, `unit_number`, `equipmentid` FROM `equipment` WHERE `deleted`=0 ORDER BY `category`, `make`, `model`, `unit_number`");
												while($units = mysqli_fetch_assoc($groups)) { ?>
													<option data-category="<?= $units['category'] ?>" data-make="<?= $units['make'] ?>" data-model="<?= $units['model'] ?>" <?= $equipment['equipmentid'] == $units['equipmentid'] ? 'selected' : '' ?> value="<?= $units['equipmentid'] ?>"><?= $units['unit_number'] ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
								<?php } else if (strpos($value_config, ','."Delivery Pickup Equipment".',') !== FALSE && strpos($value_config, ','."Assigned Equipment Inline".',') !== FALSE && $field_sort_field == 'Delivery Pickup Equipment') { ?>
									<div class="form-group">
										<?php if (strpos($value_config, ','."Delivery Pickup Equipment Category".',') !== FALSE) { ?>
											<label class="hide-titles-mob text-center col-sm-<?= floor(12 / $equip_col_count) ?>">Equipment Tab</label>
										<?php } ?>
										<?php if (strpos($value_config, ','."Delivery Pickup Equipment Make".',') !== FALSE) { ?>
											<label class="hide-titles-mob text-center col-sm-<?= floor(12 / $equip_col_count) ?>">Equipment Make</label>
										<?php } ?>
										<?php if (strpos($value_config, ','."Delivery Pickup Equipment Model".',') !== FALSE) { ?>
											<label class="hide-titles-mob text-center col-sm-<?= floor(12 / $equip_col_count) ?>">Equipment Model</label>
										<?php } ?>
										<?php if (strpos($value_config, ','."Delivery Pickup Equipment".',') !== FALSE) { ?>
											<label class="hide-titles-mob text-center col-sm-<?= floor(12 / $equip_col_count) ?>">Equipment Unit #</label>
										<?php } ?>
										<?php if (strpos($value_config, ','."Delivery Pickup Equipment Category".',') !== FALSE) { ?>
											<div class="col-sm-<?= floor(12 / $equip_col_count) ?>">
												<label class="control-label show-on-mob">Tab:</label>
												<select name="stop_eq_category" class="chosen-select-deselect"><option></option>
													<?php $groups = mysqli_query($dbc, "SELECT `category` FROM `equipment` WHERE `deleted`=0 GROUP BY `category` ORDER BY `category`");
													while($category = mysqli_fetch_assoc($groups)) { ?>
														<option <?= $equipment['category'] == $category['category'] ? 'selected' : '' ?> value="<?= $category['category'] ?>"><?= $category['category'] ?></option>
													<?php } ?>
												</select>
											</div>
										<?php } ?>
										<?php if (strpos($value_config, ','."Delivery Pickup Equipment Make".',') !== FALSE) { ?>
											<div class="col-sm-<?= floor(12 / $equip_col_count) ?>">
												<label class="control-label show-on-mob">Make:</label>
												<select name="stop_eq_make" class="chosen-select-deselect"><option></option>
													<?php $groups = mysqli_query($dbc, "SELECT `make`, `category` FROM `equipment` WHERE `deleted`=0 GROUP BY `make` ORDER BY `make`");
													while($make = mysqli_fetch_assoc($groups)) { ?>
														<option data-category="<?= $make['category'] ?>" <?= $equipment['make'] == $make['make'] ? 'selected' : '' ?> value="<?= $make['make'] ?>"><?= $make['make'] ?></option>
													<?php } ?>
												</select>
											</div>
										<?php } ?>
										<?php if (strpos($value_config, ','."Delivery Pickup Equipment Model".',') !== FALSE) { ?>
											<div class="col-sm-<?= floor(12 / $equip_col_count) ?>">
												<label class="control-label show-on-mob">Model:</label>
												<select name="stop_eq_model" class="chosen-select-deselect"><option></option>
													<?php $groups = mysqli_query($dbc, "SELECT `model`, `make`, `category` FROM `equipment` WHERE `deleted`=0 GROUP BY `model` ORDER BY `model`");
													while($model = mysqli_fetch_assoc($groups)) { ?>
														<option data-category="<?= $model['category'] ?>" data-make="<?= $model['make'] ?>" <?= $equipment['model'] == $model['model'] ? 'selected' : '' ?> value="<?= $model['model'] ?>"><?= $model['model'] ?></option>
													<?php } ?>
												</select>
											</div>
										<?php } ?>
										<?php if (strpos($value_config, ','."Delivery Pickup Equipment".',') !== FALSE) { ?>
											<div class="col-sm-<?= floor(12 / $equip_col_count) ?>">
												<label class="control-label show-on-mob">Unit #:</label>
												<select name="equipmentid" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" class="chosen-select-deselect"><option></option>
													<?php $groups = mysqli_query($dbc, "SELECT `category`, `make`, `model`, `unit_number`, `equipmentid` FROM `equipment` WHERE `deleted`=0 ORDER BY `category`, `make`, `model`, `unit_number`");
													while($units = mysqli_fetch_assoc($groups)) { ?>
														<option data-category="<?= $units['category'] ?>" data-make="<?= $units['make'] ?>" data-model="<?= $units['model'] ?>" <?= $equipment['equipmentid'] == $units['equipmentid'] ? 'selected' : '' ?> value="<?= $units['equipmentid'] ?>"><?= $units['unit_number'] ?></option>
													<?php } ?>
												</select>
											</div>
										<?php } ?>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Customer".',') !== FALSE && $field_sort_field == 'Delivery Pickup Customer') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">Customer Name:</label>
										<div class="col-sm-8">
											<input type="text" name="client_name" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['client_name'] ?>">
										</div>
									</div>
								<?php } else if (strpos($value_config, ','."Delivery Pickup Client".',') !== FALSE && $field_sort_field == 'Delivery Pickup Client') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">Client Name:</label>
										<div class="col-sm-8">
											<input type="text" name="client_name" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['client_name'] ?>">
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup".',') !== FALSE && $field_sort_field == 'Delivery Pickup') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">Location Name:</label>
										<div class="col-sm-8">
											<input type="text" name="location_name" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['location_name'] ?>">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label">Address:</label>
										<div class="col-sm-8">
											<input type="text" name="address" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['address'] ?>">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label">City:</label>
										<div class="col-sm-8">
											<input type="text" name="city" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['city'] ?>">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label">Postal Code:</label>
										<div class="col-sm-8">
											<input type="text" name="postal_code" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['postal_code'] ?>">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label"><span class="popover-examples list-inline">
												<a data-toggle="tooltip" data-placement="top" title="" data-original-title="The address must match Google maps format or the link will not populate properly."><img src="../img/info.png" width="20"></a>
											</span>Google Maps Link:</label>
										<div class="col-sm-8">
											<input type="text" name="map_link" class="form-control" data-auto-fill="<?= strpos($value_config,',Delivery Pickup Populate Google Link,') !== FALSE ? 'auto' : '' ?>" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['map_link'] ?>">
											<?php if(!empty($stop['map_link'])) {
												echo '<a href="'.$stop['map_link'].'">'.$stop['map_link'].'</a>';
											} ?>
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Address".',') !== FALSE && $field_sort_field == 'Delivery Pickup Address') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">Address:</label>
										<div class="col-sm-8">
											<input type="text" name="address" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['address'] ?>">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label">City:</label>
										<div class="col-sm-8">
											<input type="text" name="city" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['city'] ?>">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label">Postal Code:</label>
										<div class="col-sm-8">
											<input type="text" name="postal_code" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['postal_code'] ?>">
										</div>
									</div>
                                    <?php if (strpos($value_config, ','."Delivery Pickup Address Google".',') === FALSE) { ?>
                                        <input type="hidden" name="map_link" data-auto-fill="<?= strpos($value_config,',Delivery Pickup Populate Google Link,') !== FALSE ? 'auto' : '' ?>" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['map_link'] ?>">
                                    <?php } ?>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Address Google".',') !== FALSE && $field_sort_field == 'Delivery Pickup Address Google') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label"><span class="popover-examples list-inline">
												<a data-toggle="tooltip" data-placement="top" title="" data-original-title="The address must match Google maps format or the link will not populate properly."><img src="../img/info.png" width="20"></a>
											</span>Google Maps Link:</label>
										<div class="col-sm-8">
											<input type="text" name="map_link" class="form-control" data-auto-fill="<?= strpos($value_config,',Delivery Pickup Populate Google Link,') !== FALSE ? 'auto' : '' ?>" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['map_link'] ?>">
											<?php if(!empty($stop['map_link'])) {
												echo '<a href="'.$stop['map_link'].'">'.$stop['map_link'].'</a>';
											} ?>
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Coordinates".',') !== FALSE && $field_sort_field == 'Delivery Pickup Coordinates') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">Lat/Lng:</label>
										<div class="col-sm-4">
											<input type="text" name="coordinates" placeholder="Latitude Coordinates" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" data-concat="#*#" value="<?= explode('#*#',$stop['coordinates'])[0] ?>" data-manual="<?= $stop['coord_auto'] ?>" data-manual-field="coord_auto">
										</div>
										<div class="col-sm-4">
											<input type="text" name="coordinates" placeholder="Longitude Coordinates" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" data-concat="#*#" value="<?= explode('#*#',$stop['coordinates'])[1] ?>" data-manual="<?= $stop['coord_auto'] ?>" data-manual-field="coord_auto">
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Phone".',') !== FALSE && $field_sort_field == 'Delivery Pickup Phone') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">Phone:</label>
										<div class="col-sm-8">
											<input type="tel" name="details" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['details'] ?>">
										</div>
									</div>
									<div class="email_block">
										<div class="form-group">
											<label class="col-sm-4 control-label">Email:</label>
											<div class="col-sm-8">
												<input type="email" name="email" class="form-control email_recipient" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['email'] ?>">
												<label class="form-checkbox"><input type="checkbox" name="check_send_email" onclick="ticket_delivery_email(this);"> Send an email to this address</label>
											</div>
										</div>

										<script>
										function ticket_delivery_email(checked) {
											var div = $(checked).closest('.scheduled_stop');
											var str = div.find('.email_body').val();
											var eta = div.find('[name=eta]').val();
											if(eta == undefined || eta == '') {
												eta = 'on '+div.find('[name=to_do_date]').val();
											} else {
												eta = 'at '+eta;
											}
											if(checked.checked) {
												tinyMCE.editors[div.find('.email_body').attr('id')].setContent(str.replace('[[ETA]]',eta));
                                                div.find('.email_body').val(str.replace('[[ETA]]',eta));
												div.find('.email_div').show();
											} else {
												tinyMCE.editors[div.find('.email_body').attr('id')].setContent(str.substring(0,str.search('will occur '))+'will occur [[ETA]]'+str.search('. Please be ready'));
                                                div.find('.email_body').val(str.substring(0,str.search('will occur '))+'will occur [[ETA]]'+str.search('. Please be ready'));
												div.find('.email_div').hide();
											}
										}
										</script>
										<div class="email_div" style="display:none;">
											<div class="form-group">
												<label class="col-sm-4 control-label">Email Sender's Name:</label>
												<div class="col-sm-8">
													<input type="text" name="ticket_comment_email_sender_name" class="form-control email_sender_name" value="<?= EMAIL_NAME ?>">
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label">Email Sender's Address:</label>
												<div class="col-sm-8">
													<input type="text" name="ticket_comment_email_sender" class="form-control email_sender" value="<?= EMAIL_ADDRESS ?>">
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label">Email Subject:</label>
												<div class="col-sm-8">
													<input type="text" name="ticket_comment_email_subject" class="form-control email_subject" value="<?= 'Delivery on '.date('Y-m-d') ?>">
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label">Email Body:</label>
												<div class="col-sm-12">
													<textarea name="ticket_comment_email_body" class="form-control email_body"><p>Please be advised that a delivery will be made at your address shortly.</p>
														<p>It is estimated that the delivery will occur [[ETA]]. Please be ready to receive the delivery.</p>
														<p><a href="<?= WEBSITE_URL ?>/Ticket/status_link.php?s=<?= urlencode(encryptIt(json_encode(['ticket'=>$stop['ticketid'],'stop'=>$stop['id']]))) ?>">View Delivery Status</a></p></textarea>
												</div>
											</div>
											<button class="btn brand-btn pull-right" onclick="send_email(this); return false;">Send Email</button>
											<div class="clearfix"></div>
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Phone2".',') !== FALSE && $field_sort_field == 'Delivery Pickup Phone2') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">Additional Phone:</label>
										<div class="col-sm-8">
											<input type="tel" name="vendor" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['vendor'] ?>">
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Type".',') !== FALSE && $field_sort_field == 'Delivery Pickup Type') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">Delivery Tab:</label>
										<div class="col-sm-8">
											<?php if(count($delivery_types) > 0) { ?>
												<select name="type" class="chosen-select-deselect" data-placeholder="Select Type" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['type'] ?>" data-default-value="<?= get_config($dbc, 'delivery_type_default') ?>" data-hours-of-operation="<?= strpos($value_config, ','."Delivery Warehouse Hours of Operation".',') !== FALSE ? 1 : 0 ?>"><option></option>
													<?php foreach($delivery_types as $type_name) { ?>
														<option <?= $type_name == $stop['type'] || (empty($stop['type']) && !($stop['id'] > 0) && get_config($dbc, 'delivery_type_default') == $type_name) ? 'selected' : '' ?> value="<?= $type_name ?>"><?= $type_name ?></option>
													<?php }
													if($delivery_type_contacts != '') {
														$delivery_restrict_contacts = get_config($dbc, 'delivery_restrict_contacts');
														if(!empty($ticket_type)) {
															$delivery_restrict_contacts .= ','.get_config($dbc, 'delivery_restrict_contacts_'.$ticket_type);
														}
														$delivery_restrict_contacts = array_filter(array_unique(explode(',', $delivery_restrict_contacts)));
														$delivery_query = '';
														if(!empty($delivery_restrict_contacts)) {
															$delivery_query = " AND `contactid` IN (".implode(',', $delivery_restrict_contacts).")";
														}
														foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `name`, `first_name`, `last_name`, `address`, `city`, `postal_code` FROM `contacts` WHERE `category`='$delivery_type_contacts' AND `deleted`=0 AND `status` > 0".$delivery_query)) as $contact) { ?>
															<option <?= $contact['full_name'] == $stop['type'] ? 'selected' : '' ?> data-warehouse="yes" <?= strpos($value_config,',Delivery Pickup Populate Warehouse Address,') !== FALSE ? 'data-address="'.$contact['address'].'" data-city="'.$contact['city'].'" data-postal="'.$contact['postal_code'].'"' : '' ?> data-warehouseid="<?= $contact['contactid'] ?>" data-set-time="<?= get_config($dbc, 'ticket_warehouse_start_time') ?>" value="<?= $contact['full_name'] ?>"><?= $contact['full_name'] ?></option>
															<?php if($contact['full_name'] == $stop['type'] && $stop['type'] != 'warehouse') {
																$stop['type'] = 'warehouse';
															}
														}
													} ?>
												</select>
											<?php } else { ?>
												<input type="text" name="type" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['type'] ?>">
											<?php } ?>
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Volume".',') !== FALSE && $field_sort_field == 'Delivery Pickup Volume') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">Delivery Volume (<?= get_config($dbc, 'volume_units') ?>):</label>
										<div class="col-sm-8">
											<input type="number" min="0" name="volume" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['volume'] ?>">
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Cube".',') !== FALSE && $field_sort_field == 'Delivery Pickup Cube') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">Cube Size:</label>
										<div class="col-sm-8">
											<input type="number" min="0" name="volume" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['volume'] ?>">
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Service List".',') !== FALSE && $field_sort_field == 'Delivery Pickup Service List') { ?>
                                    <?php if(empty($delivery_service_list)) {
                                        $delivery_service_list = $dbc->query("SELECT `services`.* FROM `services` LEFT JOIN `rate_card` ON CONCAT('**',`rate_card`.`services`,'#') LIKE CONCAT('%**',`services`.`serviceid`,'#%') WHERE `rate_card`.`clientid`='$businessid' AND `rate_card`.`deleted`=0 AND `services`.`deleted`=0")->fetch_all(MYSQLI_ASSOC);
                                    } ?>
                                    <?php foreach(explode(',',$stop['serviceid']) as $stop_service) { ?>
                                        <div class="multi-block no_id_reset">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Services:</label>
                                                <div class="col-sm-7">
                                                    <select name="serviceid" data-placeholder="Select Services" class="form-control chosen-select-deselect" data-concat="," data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id"><option />
                                                        <?php foreach($delivery_service_list as $service) { ?>
                                                            <option <?= $service['serviceid'] == $stop_service ? 'selected' : '' ?> value="<?= $service['serviceid'] ?>"><?= implode(': ',array_filter([$service['category'],$service['service_type'],$service['heading']])) ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-1">
                                                    <img class="cursor-hand inline-img pull-right" onclick="remMulti(this);" data-remove="1" src="../img/remove.png">
                                                    <img class="cursor-hand inline-img pull-right" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup ETA".',') !== FALSE && $field_sort_field == 'Delivery Pickup ETA') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">
											<span class="popover-examples list-inline">
												<a data-toggle="tooltip" data-placement="top" title="" data-original-title="This is the Estimated Time of Arrival for the Delivery."><img src="../img/info.png" width="20"></a>
											</span>&nbsp;ETA Window:</label>
										<div class="col-sm-8">
											<input type="text" name="eta" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['eta'] ?>">
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Customer Est Time".',') !== FALSE && $field_sort_field == 'Delivery Pickup Customer Est Time') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label"><?= get_contact($dbc, $get_ticket['businessid'], 'name_company') ?> Estimated Time:</label>
										<div class="col-sm-8">
											<input type="text" name="cust_est" class="form-control" readonly value="<?= $stop['cust_est'] ?>">
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Estimate".',') !== FALSE && $field_sort_field == 'Delivery Pickup Estimate') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">Estimated Time:</label>
										<div class="col-sm-8">
											<input type="text" name="est_time" class="form-control timepicker" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= time_decimal2time($stop['est_time'],true) ?>">
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Date".',') !== FALSE && $field_sort_field == 'Delivery Pickup Date') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label"><span class="popover-examples list-inline">
												<a data-toggle="tooltip" data-placement="top" title="" data-original-title="This is the Scheduled Date for the Delivery. This Date is what that the Calendar looks for."><img src="../img/info.png" width="20"></a>
											</span>&nbsp;Scheduled Date:</label>
										<div class="col-sm-8">
											<input type="text" name="to_do_date" class="form-control datepicker" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" <?= !empty($delivery_restrictions['to_do_date_min']) ? 'data-min-date="'.$delivery_restrictions['to_do_date_min'].'"' : '' ?> <?= !empty($delivery_restrictions['to_do_date_max']) ? 'data-max-date="'.$delivery_restrictions['to_do_date_max'].'"' : '' ?> value="<?= $stop['to_do_date'] ?>" <?= !empty(str_replace('0000-00-00','',$stop['to_do_date'])) && !empty($delivery_restrictions['to_do_date_min']) && strtotime($stop['to_do_date']) < strtotime($delivery_restrictions['to_do_date_min']) ? 'disabled' : '' ?>>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label">
											<span class="popover-examples list-inline">
												<a data-toggle="tooltip" data-placement="top" title="" data-original-title="This is the Scheduled Time for the Delivery. This Time is what the Calendar looks for."><img src="../img/info.png" width="20"></a>
											</span>&nbsp;Scheduled Time:<?= ($stop['scheduled_lock'] > 0 ? ' <img class="inline-img" src="../img/icons/lock.png">' : '') ?></label>
										<div class="col-sm-8 stop_scheduled_time">
											<?php $ticket_delivery_time_mintime = (!empty($delivery_restrictions['to_do_start_time_min']) && ($stop['to_do_date'] == $delivery_restrictions['to_do_date_min'] || empty($delivery_restrictions['to_do_date_min'])) ? $delivery_restrictions['to_do_start_time_min'] : get_config($dbc, 'ticket_delivery_time_mintime'));
											$ticket_delivery_time_maxtime = (!empty($delivery_restrictions['to_do_start_time_max']) && ($stop['to_do_date'] == $delivery_restrictions['to_do_date_max'] || empty($delivery_restrictions['to_do_date_max'])) ? $delivery_restrictions['to_do_start_time_max'] : get_config($dbc, 'ticket_delivery_time_maxtime')); ?>
											<input type="text" name="to_do_start_time" data-window="<?= $delivery_timeframe_default ?>" class="form-control datetimepicker<?= $calendar_window > 0 ? '-'.$calendar_window : '-30' ?>" data-manual="1" data-manual-field="scheduled_lock" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" <?= !empty($ticket_delivery_time_mintime) ? 'data-datetimepicker-mintime="'.$ticket_delivery_time_mintime.'" data-default-datetimepicker-mintime="'.$ticket_delivery_time_mintime.'"' : '' ?> <?= !empty($ticket_delivery_time_maxtime) ? 'data-datetimepicker-maxtime="'.$ticket_delivery_time_maxtime.'" data-default-datetimepicker-maxtime="'.$ticket_delivery_time_maxtime.'"' : '' ?> value="<?= $stop['to_do_start_time'] ?>" <?= !empty(str_replace('0000-00-00','',$stop['to_do_date'])) && !empty($delivery_restrictions['to_do_date_min']) && strtotime($stop['to_do_date']) < strtotime($delivery_restrictions['to_do_date_min']) ? 'disabled data-disabled="true"' : '' ?>>
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Order".',') !== FALSE && $field_sort_field == 'Delivery Pickup Order') { ?>
									<?php if (strpos($value_config, ','."PI Sales Order".',') !== FALSE) { ?>
										<div class="form-group">
											<label class="col-sm-4 control-label">Order #:</label>
											<div class="col-sm-8">
												<select name="order_number" class="chosen-select-deselect form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id"><option></option>
													<?php $orders = mysqli_query($dbc, "SELECT * FROM `sales_order` WHERE `deleted`=0");
													while($order = mysqli_fetch_assoc($orders)) { ?>
														<option <?= $order['posid'] == $stop['order_number'] ? 'selected' : '' ?> value="<?= $order['posid'] ?>">Order #<?= $order['posid'] ?> <?= $order['invoice_date'] ?></option>
													<?php } ?>
												</select>
											</div>
										</div>
									<?php } else { ?>
										<div class="form-group">
											<label class="col-sm-4 control-label">Order #:</label>
											<div class="col-sm-8">
												<input type="text" name="order_number" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['order_number'] ?>">
											</div>
										</div>
									<?php } ?>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Timeframe".',') !== FALSE && $field_sort_field == 'Delivery Pickup Timeframe') {
									if($stop['start_available'] == '' && $stop['to_do_start_time'] != '' && $delivery_timeframe_default > 0) {
										$stop['start_available'] = $stop['to_do_start_time'];
										$stop['end_available'] = date('H:i',strtotime($stop['start_available'].' + '.$delivery_timeframe_default.' hours'));
									} ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">
											<span class="popover-examples list-inline">
												<a data-toggle="tooltip" data-placement="top" title="" data-original-title="This is the Time Frame of the Delivery, which includes the Available Start Time up to the Available End Time"><img src="../img/info.png" width="20"></a>
											</span>&nbsp;Delivery Time Frame:</label>
										<div class="col-sm-4">
											<input type="text" name="start_available" readonly class="form-control datetimepicker<?= $calendar_window > 0 ? '-'.$calendar_window : '' ?>" placeholder="Availability Start Time" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['start_available'] != '' ? date('g:i a',strtotime($stop['start_available'])) : '' ?>">
										</div>
										<div class="col-sm-4">
											<input type="text" name="end_available" readonly class="form-control datetimepicker<?= $calendar_window > 0 ? '-'.$calendar_window : '' ?>" placeholder="Availability End Time" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['end_available'] != '' ? date('g:i a',strtotime($stop['end_available'])) : '' ?>">
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Warehouse Only".',') !== FALSE && $field_sort_field == 'Delivery Pickup Warehouse Only') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">Warehouse Pick Up:</label>
										<div class="col-sm-8">
											<label class="form-checkbox"><input type="radio" name="type_<?= $stop_i ?>" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" <?= $stop['type'] == 'warehouse' ? 'checked' : '' ?> data-field-name="type" data-index="<?= $stop_i ?>" data-set-time="<?= get_config($dbc, 'ticket_warehouse_start_time') ?>" value="warehouse">Yes</label>
											<label class="form-checkbox"><input type="radio" name="type_<?= $stop_i ?>" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" <?= $stop['type'] == 'warehouse' ? '' : 'checked' ?> data-field-name="type" data-index="<?= $stop_i ?>" value="">No</label>
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Status".',') !== FALSE && $field_sort_field == 'Delivery Pickup Status') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">Status:</label>
										<div class="col-sm-8">
											<select data-placeholder="Select a Status..." name="status" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" id="status" class="chosen-select-deselect"><option/>
												<?php foreach(explode(',',get_config($dbc, 'ticket_status')) as $cat_tab) {
													echo "<option ".($stop['status'] == $cat_tab ? 'selected' : '')." value='". $cat_tab."'>".$cat_tab.'</option>';
												} ?>
											</select>
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Notes".',') !== FALSE && $field_sort_field == 'Delivery Pickup Notes') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">Delivery Notes:</label>
										<div class="col-sm-8">
											<textarea name="notes" class="no_tools form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id"><?= html_entity_decode($stop['notes']) ?></textarea>
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Upload".',') !== FALSE && $field_sort_field == 'Delivery Pickup Upload') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">Upload Picture / Document:</label>
										<div class="col-sm-8">
											<?php if(!empty($stop['uploads'])) { ?>
												<span><a href="download/<?= $stop['uploads'] ?>">View</a> | <a class="cursor-hand" onclick="$(this).closest('div').find('input[type=hidden]').val('').change();$(this).closest('span').hide();">Delete</a>
												<input type="hidden" name="uploads" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['uploads'] ?>"></span>
											<?php } ?>
											<input type="file" name="uploads" class="form-control" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id">
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Arrival".',') !== FALSE && $field_sort_field == 'Delivery Pickup Arrival') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">Arrival Time:</label>
										<div class="col-sm-8">
											<input type="text" name="checked_in" class="form-control datetimepicker<?= $calendar_window > 0 ? '-'.$calendar_window : '' ?>" placeholder="Arrival Time" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['checked_in'] ?>">
										</div>
									</div>
								<?php } ?>
								<?php if (strpos($value_config, ','."Delivery Pickup Departure".',') !== FALSE && $field_sort_field == 'Delivery Pickup Departure') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">Departure Time:</label>
										<div class="col-sm-8">
											<input type="text" name="checked_out" class="form-control datetimepicker<?= $calendar_window > 0 ? '-'.$calendar_window : '' ?>" placeholder="Departure Time" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="<?= $stop['checked_out'] ?>">
										</div>
									</div>
								<?php } ?>
								<?php if(strpos($value_config, ','."Delivery Calendar History".',') !== FALSE && $field_sort_field == 'Delivery Calendar History') { ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">Calendar History:</label>
										<div class="col-sm-8">
											<?= html_entity_decode($stop['calendar_history']) ?>
										</div>
									</div>
								<?php } ?>
								<?php if(strpos($value_config, ','."Delivery Completed Check".',') !== FALSE && $field_sort_field == 'Delivery Completed Check') { ?>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Completed Stop:</label>
                                        <div class="col-sm-6">
                                            <label class="form-checkbox"><input type="checkbox" name="complete" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="1" <?= $stop['complete'] == 1 ? 'checked' : '' ?>>Completed</label>
                                        </div>
                                    </div>
								<?php } ?>
							<?php } ?>
						</div>
						<hr>
					<?php } else {
						$stop_count++;
					}
				} while($stop = mysqli_fetch_assoc($ticket_stops)); ?>
				<?php if (strpos($value_config, ','."Delivery Pickup Dropoff Map".',') !== FALSE && $get_ticket['main_ticketid'] == 0) { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label">Pickup to Delivery Directions:</label>
					<div class="col-sm-12 route_map_div">
						<?php $_GET['map_action'] = 'pickup_delivery';
						include('add_ticket_maps.php');
						unset($_GET['map_action']); ?>
					</div>
				</div>
				<?php }
			} ?>
		<?php } ?>
	<?php } ?>
<?php } else { ?>
	<?php foreach($field_sort_order as $field_sort_field) { ?>
		<?php if (strpos($value_config, ','."Assigned Equipment".',') !== FALSE && $field_sort_field == 'Assigned Equipment') { ?>
			<h4>Assigned Equipment</h4>
			<?php $equipment_list = mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '$equipmentid'");
			$equipment = mysqli_fetch_assoc($equipment_list); ?>
			<div class="multi-block-assign">
				<div class="form-group">
					<label class="control-label col-sm-4">Tab:</label>
					<div class="col-sm-8">
						<?= $equipment['category'] ?>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4">Make:</label>
					<div class="col-sm-8">
						<?= $equipment['make'] ?>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4">Model:</label>
					<div class="col-sm-8">
						<?= $equipment['model'] ?>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4">Unit #:</label>
					<div class="col-sm-8">
						<?= $equipment['unit_number'] ?>
					</div>
				</div>
			</div>
		<?php } ?>
		<?php if($field_sort_field == 'Delivery Stops') { ?>
			<?php if(strpos($value_config, ',Delivery Stops,') !== FALSE) { ?>
				<h4><?= TICKET_NOUN ?> Stop</h4>
			<?php } else { ?>
				<?php $ticket_stops = mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `ticketid`='$ticketid' AND `deleted`=0 AND `type` != 'origin' AND `type` != 'destination' ORDER BY `sort`");
				$stop = mysqli_fetch_assoc($ticket_stops);
				do {
					if($stop['id'] == $_GET['stop'] || !($_GET['stop'] > 0)) { ?>
						<?php if(strpos($value_config, ',Delivery Pickup') !== FALSE && $get_ticket['main_ticketid'] == 0) { ?>
							<h4>Scheduled Stop<img class="inline-img small pull-right stop_sort" src="../img/icons/drag_handle.png"></h4>
						<?php } ?>
						<?php foreach($field_sort_order as $field_sort_field) { ?>
							<?php if (strpos($value_config, ','."Delivery Pickup Customer".',') !== FALSE && $field_sort_field == 'Delivery Pickup Customer') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Customer Name:</label>
									<div class="col-sm-8">
										<?= $stop['client_name'] ?>
									</div>
								</div>
							<?php } else if (strpos($value_config, ','."Delivery Pickup Client".',') !== FALSE && $field_sort_field == 'Delivery Pickup Client') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Client Name:</label>
									<div class="col-sm-8">
										<?= $stop['client_name'] ?>
									</div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Delivery Pickup".',') !== FALSE && $field_sort_field == 'Delivery Pickup') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Location Name:</label>
									<div class="col-sm-8">
										<?= $stop['location_name'] ?>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Address:</label>
									<div class="col-sm-8">
										<?= $stop['address'] ?>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">City:</label>
									<div class="col-sm-8">
										<?= $stop['city'] ?>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Postal Code:</label>
									<div class="col-sm-8">
										<?= $stop['postal_code'] ?>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Google Maps Link:</label>
									<div class="col-sm-8">
										<?php if($stop['map_link'] != '') { ?><a href="<?= $stop['map_link'] ?>">Map</a><?php } ?>
									</div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Delivery Pickup Address".',') !== FALSE && $field_sort_field == 'Delivery Pickup Address') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Address:</label>
									<div class="col-sm-8">
										<?= $stop['address'] ?>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">City:</label>
									<div class="col-sm-8">
										<?= $stop['city'] ?>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Postal Code:</label>
									<div class="col-sm-8">
										<?= $stop['postal_code'] ?>
									</div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Delivery Pickup Address Google".',') !== FALSE && $field_sort_field == 'Delivery Pickup Address Google') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Google Maps Link:</label>
									<div class="col-sm-8">
										<?php if($stop['map_link'] != '') { ?><a href="<?= $stop['map_link'] ?>">Map</a><?php } ?>
									</div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Delivery Pickup Coordinates".',') !== FALSE && $field_sort_field == 'Delivery Pickup Coordinates') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Lat/Lng:</label>
									<div class="col-sm-4">
										<?= explode('#*#',$stop['coordinates'])[0] ?>
									</div>
									<div class="col-sm-4">
										<?= explode('#*#',$stop['coordinates'])[1] ?>
									</div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Delivery Pickup Phone".',') !== FALSE && $field_sort_field == 'Delivery Pickup Phone') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Phone:</label>
									<div class="col-sm-8">
										<?= $stop['details'] ?>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Email:</label>
									<div class="col-sm-8">
										<?= $stop['email'] ?>
										<input type="hidden" name="email" class="email_recipient" value="<?= $stop['email'] ?>">
										<label class="form-checkbox"><input type="checkbox" name="check_send_email" onclick="ticket_delivery_email(this);"> Send an email to this address</label>
									</div>
								</div>

								<script>
								function ticket_delivery_email(checked) {
									var div = $(checked).closest('.scheduled_stop');
									var str = div.find('.email_body').val();
									if(checked.checked) {
										tinyMCE.editors[div.find('.email_body').attr('id')].setContent(str.replace('[[ETA]]',div.find('[name=eta]').val()))
										div.find('.email_div').show();
									} else {
										tinyMCE.editors[div.find('.email_body').attr('id')].setContent(str.substring(0,str.search('will occur at '))+'will occur at [[ETA]]'+str.search('. Please be ready'));
										div.find('.email_div').hide();
									}
								}
								</script>
								<div class="email_div" style="display:none;">
									<div class="form-group">
										<label class="col-sm-4 control-label">Email Sender's Name:</label>
										<div class="col-sm-8">
											<input type="text" name="ticket_comment_email_sender_name" class="form-control email_sender_name" value="<?= EMAIL_NAME ?>">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label">Email Sender's Address:</label>
										<div class="col-sm-8">
											<input type="text" name="ticket_comment_email_sender" class="form-control email_sender" value="<?= EMAIL_ADDRESS ?>">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label">Email Subject:</label>
										<div class="col-sm-8">
											<input type="text" name="ticket_comment_email_subject" class="form-control email_subject" value="<?= 'Delivery on '.date('Y-m-d') ?>">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label">Email Body:</label>
										<div class="col-sm-12">
											<textarea name="ticket_comment_email_body" class="form-control email_body">Please be advised that a delivery will be made at your address shortly.<br /><br />
												It is estimated that the delivery will occur at [[ETA]]. Please be ready to receive the delivery.</textarea>
										</div>
									</div>
									<button class="btn brand-btn pull-right" onclick="send_email(this); return false;">Send Email</button>
									<div class="clearfix"></div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Delivery Pickup Phone2".',') !== FALSE && $field_sort_field == 'Delivery Pickup Phone2') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Additional Phone:</label>
									<div class="col-sm-8">
										<?= $stop['vendor'] ?>
									</div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Delivery Pickup Type".',') !== FALSE && $field_sort_field == 'Delivery Pickup Type') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Delivery Tab:</label>
									<div class="col-sm-8">
										<?= $stop['type'] ?>
									</div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Delivery Pickup Volume".',') !== FALSE && $field_sort_field == 'Delivery Pickup Volume') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Delivery Volume (<?= get_config($dbc, 'volume_units') ?>):</label>
									<div class="col-sm-8">
										<?= $stop['volume'] ?>
									</div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Delivery Pickup Cube".',') !== FALSE && $field_sort_field == 'Delivery Pickup Cube') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Delivery Cube Size:</label>
									<div class="col-sm-8">
										<?= $stop['volume'] ?>
									</div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Delivery Pickup ETA".',') !== FALSE && $field_sort_field == 'Delivery Pickup ETA') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">ETA Window:</label>
									<div class="col-sm-8">
										<?= $stop['eta'] ?>
									</div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Delivery Pickup Customer Est Time".',') !== FALSE && $field_sort_field == 'Delivery Pickup Customer Est Time') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label"><?= get_contact($dbc, $get_ticket['businessid'], 'name_company') ?> Estimated Time:</label>
									<div class="col-sm-8">
										<?= $stop['cust_est'] ?>
									</div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Delivery Pickup Estimate".',') !== FALSE && $field_sort_field == 'Delivery Pickup Estimate') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Estimated Time:</label>
									<div class="col-sm-8">
										<?= time_decimal2time($stop['est_time'],true) ?>
									</div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Delivery Pickup Date".',') !== FALSE && $field_sort_field == 'Delivery Pickup Date') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Scheduled Date:</label>
									<div class="col-sm-8">
										<?= $stop['to_do_date'] ?>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Scheduled Time:</label>
									<div class="col-sm-8">
										<?= $stop['to_do_start_time'] ?>
									</div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Delivery Pickup Order".',') !== FALSE && $field_sort_field == 'Delivery Pickup Order') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Order #:</label>
									<div class="col-sm-8">
										<?= $stop['order_number'] ?>
									</div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Delivery Pickup Timeframe".',') !== FALSE && $field_sort_field == 'Delivery Pickup Timeframe') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Delivery Time Frame:</label>
									<div class="col-sm-4">
										<?= $stop['start_available'] != '' ? date('g:i a',strtotime($stop['start_available'])) : '' ?>
									</div>
									<div class="col-sm-4">
										<?= $stop['end_available'] != '' ? date('g:i a',strtotime($stop['end_available'])) : '' ?>
									</div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Delivery Pickup Warehouse Only".',') !== FALSE && $field_sort_field == 'Delivery Pickup Warehouse Only') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Warehouse Pick Up:</label>
									<div class="col-sm-8">
										<?= $stop['type'] == 'warehouse' ? 'Yes' : '' ?>
									</div>
								</div>
							<?php if (strpos($value_config, ','."Delivery Pickup Arrival".',') !== FALSE && $field_sort_field == 'Delivery Pickup Arrival') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Arrival Time:</label>
									<div class="col-sm-8">
										<?= $stop['checked_in'] ?>
									</div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Delivery Pickup Departure".',') !== FALSE && $field_sort_field == 'Delivery Pickup Departure') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Departure Time:</label>
									<div class="col-sm-8">
										<?= $stop['checked_out'] ?>
									</div>
								</div>
							<?php } ?>
							<?php if (strpos($value_config, ','."Delivery Pickup Status".',') !== FALSE && $field_sort_field == 'Delivery Pickup Status') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Status:</label>
									<div class="col-sm-8">
										<?= $stop['status'] ?>
									</div>
								</div>
							<?php } ?>
							<?php if(strpos($value_config, ','."Delivery Calendar History".',') !== FALSE && $field_sort_field == 'Delivery Calendar History') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Calendar History:</label>
									<div class="col-sm-8">
										<?= html_entity_decode($stop['calendar_history']) ?>
									</div>
								</div>
							<?php } ?>
							<?php if(strpos($value_config, ','."Delivery Pickup Notes".',') !== FALSE && $field_sort_field == 'Delivery Pickup Notes') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Delivery Notes:</label>
									<div class="col-sm-8">
										<?= html_entity_decode($stop['notes']) ?>
									</div>
								</div>
							<?php } ?>
							<?php if(strpos($value_config, ','."Delivery Pickup Upload".',') !== FALSE && $field_sort_field == 'Delivery Pickup Upload') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Upload:</label>
									<div class="col-sm-8">
										<?php if(!empty($stop['uploads'])) { ?>
											<a href="download/<?= $stop['uploads'] ?>">View</a>
										<?php } ?>
									</div>
								</div>
							<?php } ?>
                            <?php if(strpos($value_config, ','."Delivery Completed Check".',') !== FALSE && $field_sort_field == 'Delivery Completed Check') { ?>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Completed Stop:</label>
                                    <div class="col-sm-6">
                                        <label class="form-checkbox"><input type="checkbox" name="complete" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id" value="1" <?= $stop['complete'] == 1 ? 'checked' : '' ?>>Completed</label>
                                    </div>
                                    <input type="hidden" name="deleted" value="0" data-table="ticket_schedule" data-id="<?= $stop['id'] ?>" data-id-field="id">
                                    <div class="col-sm-2"><img class="inline-img small black-color pull-right" src="../img/icons/ROOK-add-icon.png" data-history-label="Delivery Stop" onclick="addScheduledStop();"><img class="inline-img small pull-right" src="../img/remove.png" data-history-label="Delivery Stop" onclick="remScheduledStop(this);"></div>
                                </div>
                            <?php } ?>
						<?php } ?>
						<?php } ?>
						<hr>
					<?php } ?>
				<?php } while($stop = mysqli_fetch_assoc($ticket_stops)); ?>
				<?php if (strpos($value_config, ','."Delivery Pickup Dropoff Map".',') !== FALSE && $get_ticket['main_ticketid'] == 0 && basename($_SERVER['SCRIPT_FILENAME']) == 'index.php') { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label">Pickup to Delivery Directions:</label>
					<div class="col-sm-8 route_map_div">
						<?php $_GET['map_action'] = 'pickup_delivery';
						include('add_ticket_maps.php');
						unset($_GET['map_action']); ?>
					</div>
				</div>
				<?php }
			} ?>
		<?php } ?>
	<?php } ?>
<?php } ?>