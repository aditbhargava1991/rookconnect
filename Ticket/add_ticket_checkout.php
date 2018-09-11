<?php error_reporting(0);
include_once('../include.php');
include_once('../Ticket/field_list.php');
$folder = FOLDER_NAME;
if(isset($_GET['folder'])) {
	$folder = filter_var($_GET['folder'], FILTER_SANITIZE_STRING);
	$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
}
if(basename($_SERVER['SCRIPT_FILENAME']) == 'add_ticket_checkout.php') {
	ob_clean();
	$strict_view = strictview_visible_function($dbc, 'ticket');
	$tile_security = get_security($dbc, ($_GET['tile_name'] == '' ? 'ticket' : 'ticket_type_'.$_GET['tile_name']));
	if($strict_view > 0) {
		$tile_security['edit'] = 0;
		$tile_security['config'] = 0;
	}
	$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'"));
	$ticket_roles = explode('#*#',get_config($dbc, 'ticket_roles'));
	$ticket_role = mysqli_query($dbc, "SELECT `position` FROM `ticket_attached` WHERE `src_table`='Staff' AND `position`!='' AND `item_id`='".$_SESSION['contactid']."' AND `ticketid`='$ticketid' AND `ticketid` > 0");
	if($get_ticket['to_do_date'] > date('Y-m-d') && strpos($value_config,',Ticket Edit Cutoff,') !== FALSE && $config_visible_function($dbc, 'ticket') < 1) {
		$access_staff_checkin = false;
		$access_all_checkin = false;
	} else if($get_ticket['status'] == 'Archive') {
		$access_staff_checkin = 0;
		$access_all_checkin = 0;
	} else if(config_visible_function($dbc, 'ticket') > 0) {
		$access_staff_checkin = check_subtab_persmission($dbc, 'ticket', ROLE, 'staff_checkin');
		$access_all_checkin = check_subtab_persmission($dbc, 'ticket', ROLE, 'all_checkin');
	} else if((count($ticket_roles) > 1 || explode('|',$ticket_roles[0])[0] != '') && mysqli_num_rows($ticket_role) > 0) {
		$ticket_role = mysqli_fetch_assoc($ticket_role);
		foreach($ticket_roles as $ticket_role_level) {
			$ticket_role_level = explode('|',$ticket_role_level);
			if($ticket_role_level[0] > 0) {
				$ticket_role_level[0] = get_positions($dbc, $ticket_role_level[0], 'name');
			}
			if($ticket_role_level[0] == $ticket_role['position']) {
				$access_staff_checkin = in_array('staff_checkin',$ticket_role_level);
				$access_all_checkin = in_array('all_checkin',$ticket_role_level);
			}
		}
	} else if(count(array_filter($arr, function ($var) { return (strpos($var, 'default') !== false); })) > 0) {
		foreach($ticket_roles as $ticket_role_level) {
			$ticket_role_level = explode('|',$ticket_role_level);
			if(in_array('default',$ticket_role_level)) {
				$access_staff_checkin = in_array('staff_checkin',$ticket_role_level);
				$access_all_checkin = in_array('all_checkin',$ticket_role_level);
			}
		}
	} else {
		$access_staff_checkin = check_subtab_persmission($dbc, 'ticket', ROLE, 'staff_checkin');
		$access_all_checkin = check_subtab_persmission($dbc, 'ticket', ROLE, 'all_checkin');
	}
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

	//Apply Templates
	if(strpos($value_config,',TEMPLATE Work Ticket') !== FALSE) {
		$value_config = ',Information,PI Business,PI Name,PI Project,PI AFE,PI Sites,Staff,Staff Position,Staff Hours,Staff Overtime,Staff Travel,Staff Subsistence,Services,Service Category,Equipment,Materials,Material Quantity,Material Rates,Purchase Orders,Notes,';
	}
	$query_daily = "";
	if(strpos($value_config,',Time Tracking Current,') !== FALSE) {
		$query_daily = " AND `date_stamp`='".date('Y-m-d')."' ";
	}

	$sort_field = 'Check Out';
	if($_GET['staffcheckout'] == 'true') {
		$sort_field = 'Staff Check Out';
	}
	$field_list = $accordion_list[$sort_field];
	$field_sort_order = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_fields` WHERE `ticket_type` = '".(empty($get_ticket['ticket_type']) ? 'tickets' : 'tickets_'.$get_ticket['ticket_type'])."' AND `accordion` = '".$sort_field."'"))['fields'];
	$field_sort_order = explode(',', $field_sort_order);
	foreach ($field_list as $default_field) {
		if(!in_array($default_field, $field_sort_order)) {
			$field_sort_order[] = $default_field;
		}
	}
	$renamed_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($get_ticket['ticket_type']) ? 'tickets' : 'tickets_'.$get_ticket['ticket_type'])."' AND `accordion` = '".$sort_field."'"))['accordion_name'];
}
if($sort_field == 'Staff Check Out') {
	$value_config_addition = 'Staff ';
	$query_addition = "AND `src_table` = 'Staff'";
}
if(!empty($query_daily)) {
	$query_daily = " AND (`date_stamp`='".date('Y-m-d')."' OR IFNULL(`checked_out`,'') = '')";
}
if(strpos($value_config, ',Checkout Show Checked In Only,') !== FALSE) {
	$query_checkedinonly = " AND IFNULL(`checked_in`,'') != ''";
	echo '<input type="hidden" name="reload_checkout_on_checkin" value="1">';
}
?>
<script>
function setCheckoutReason(select) {
	if(select.value == 'MANUAL') {
		$(select).closest('.form-group').find('.select_checkout_reason').hide();
		$(select).closest('.form-group').find('.manual_checkout_reason').show().find('input').focus();
	} else {
		$(select).closest('.form-group').find('.select_checkout_reason').show();
		$(select).closest('.form-group').find('.manual_checkout_reason').hide();
	}
}
</script>
<h3><?= (!empty($renamed_accordion) ? $renamed_accordion : ($sort_field == 'Staff Check Out' ? 'Staff Check Out' : (strpos($value_config, ','."Check Out Member Pick Up".',') !== FALSE ? 'Member Pick Up' : 'Check Out'))) ?>
<?php $stopid = filter_var($_GET['stop'],FILTER_SANITIZE_STRING);
$checkins = mysqli_query($dbc, "SELECT `id`, `src_table`, `completed`, `item_id`, `position`, `notes` FROM `ticket_attached` WHERE `item_id` > 0 AND `src_table` NOT IN ('Wait List') AND `deleted`=0 AND `ticketid`='$ticketid' AND '$ticketid' > 0 AND (`src_table` != 'Delivery' OR `line_id`='$stopid') AND `tile_name`='".$folder."' $query_addition $query_daily $query_checkedinonly ORDER BY `src_table` != 'Staff', `src_table`");
if(strpos($value_config, ',Checkin Delivery,') !== FALSE && !($_GET['stop'] > 0)) { ?>
	</h3><h4>Please access a specific Stop to check out</h4>
<?php } else if(mysqli_num_rows($checkins) == 0) { ?>
	</h3><h4>No records found attached to this <?= TICKET_NOUN ?>.</h4>
<?php } else if(strpos($value_config, ','.$value_config_addition.'Checkout Hide All Button,') === FALSE && (($checkin['src_table'] == 'Staff' && $access_staff_checkin == TRUE) || ($checkin['src_table'] != 'Staff' && $access_all_checkin == TRUE))) { ?>
	<button class="btn brand-btn pull-right" onclick="if($(this).closest('.panel-body,.tab-section,.has-main-screen .main-screen').find('.toggle[value=0]').length > 0 && confirm('Are you sure you want to check out all individuals?')) { toggleAll(this); } return false;">Check Out All</button></h3>
	<div class="clearfix"></div>
<?php } else { ?>
	</h3>
<?php }
$src_group = '';
$checkins = mysqli_fetch_all($checkins,MYSQLI_ASSOC);
foreach ($field_sort_order as $field_sort_field) {
	foreach ($checkins as $checkin) {
		$pdf_content = '';
		if($value_config_addition.'Checkout '.$checkin['src_table'] == $field_sort_field) {
			if($checkin['src_table'] != $src_group && in_array($checkin['src_table'],['Members','Staff','Staff_Tasks','Delivery','Clients','material','equipment']) && strpos($value_config, ',Checkout '.$checkin['src_table'].',') !== FALSE) {
				$src_group = $checkin['src_table'];
				if($src_group != '') {
					echo "<hr>\n";
				}
				echo "<h4>".ucwords($src_group == 'Staff_Tasks' ? 'Staff' : $src_group)."</h4>";
			}
			if(in_array($checkin['src_table'],['Staff','Staff_Tasks','Delivery','Clients','Members']) && strpos($value_config, ','.$value_config_addition.'Checkout '.$checkin['src_table'].',') !== FALSE) { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?= get_contact($dbc, $checkin['item_id']).($checkin['position'] != '' ? ' - '.$checkin['position'] : '') ?>:</label>
					<div class="col-sm-2 <?= ($checkin['src_table'] == 'Staff' && $access_staff_checkin == TRUE) || ($checkin['src_table'] != 'Staff' && $access_all_checkin == TRUE) ? 'toggleSwitch mobile-lg' : '' ?>">
						<input type="hidden" name="checkin_id[]" value="<?= $checkin['item_id'] ?>">
						<input type="hidden" name="completed" data-table="ticket_attached" data-id="<?= $checkin['id'] ?>" data-id-field="id" value="<?= $checkin['completed'] ?>" class="toggle" <?= strpos($value_config,',Check Out Before Check In,') !== FALSE ? 'data-no-toggle-off="1"' : '' ?>>
						<span style="<?= $checkin['completed'] > 0 ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg inline-img"> Not Checked Out</span>
						<span style="<?= $checkin['completed'] > 0 ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg inline-img"> Checked Out</span>
					</div>
					<?php $pdf_content .= get_contact($dbc, $checkin['item_id']).($checkin['position'] != '' ? ' - '.$checkin['position'] : '').' - '.($checkin['arrived'] > 0 ? 'Checked Out' : 'Not Checked Out'); ?>
					<?php if(strpos($value_config, ','.$value_config_addition."Checkout Notes".',') !== FALSE) { ?>
						<label class="col-sm-2 control-label">Check Out Reason:</label>
						<div class="col-sm-4 select_checkout_reason">
							<select name="notes" data-placeholder="Select a Reason" data-table="ticket_attached" data-id="<?= $checkin['id'] ?>" data-id-field="id" class="chosen-select-deselect" onchange="setCheckoutReason(this);">
								<option></option>
								<?php $checkout_reasons = explode('#*#',get_config($dbc, 'ticket_checkout_info'));
								if($sort_field == 'Staff Check Out') {
									$checkout_reasons = explode('#*#',get_config($dbc, 'ticket_checkout_info_staff'));
								}
								foreach($checkout_reasons as $reason) { ?>
									<option <?= $checkin['notes'] == $reason ? 'selected' : '' ?> value="<?= $reason ?>"><?= $reason ?></option>
								<?php }
								if($checkin['notes'] != '' && !in_array($checkin['notes'],$checkout_reasons)) { ?>
									<option selected value="<?= $checkin['notes'] ?>"><?= $checkin['notes'] ?></option>
								<?php } ?>
								<option value="MANUAL">Other Reason</option>
							</select>
						</div>
						<div class="col-sm-4 manual_checkout_reason" style="display:none;">
							<div class="col-sm-11">
								<input type="text" name="notes" data-table="ticket_attached" data-id="<?= $checkin['id'] ?>" data-id-field="id" class="form-control" value="<?= $checkin['notes'] ?>">
							</div>
							<div class="col-sm-1">
								<img class="pull-right inline-img" src="../img/remove.png" onclick="$(this).closest('.form-group').find('.select_checkout_reason select').val('').trigger('change.select2');">
							</div>
						</div>
						<?php $pdf_content .= '<br>Check Out Reason: '.$checkin['notes']; ?>
					<?php } ?>
				</div>
				<hr class="visible-xs">
				<?php $pdf_contents[] = [$checkin['src_table'], $pdf_content]; ?>
			<?php } else if(in_array($checkin['src_table'],['material']) && strpos($value_config, ',Checkout '.$checkin['src_table'].',') !== FALSE) {
				$material = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `material` WHERE `materialid`='{$checkin['item_id']}'")); ?>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?= $material['category'].': '.$material['sub_category'].' ',$material['name'] ?>:</label>
					<div class="col-sm-8 <?= $access_all_checkin == TRUE ? 'toggleSwitch mobile-lg' : '' ?>">
						<input type="hidden" name="checkin_id[]" value="<?= $checkin['item_id'] ?>">
						<input type="hidden" name="completed" data-table="ticket_attached" data-id="<?= $checkin['id'] ?>" data-id-field="id" value="<?= $checkin['completed'] ?>" class="toggle" <?= strpos($value_config,',Check Out Before Check In,') !== FALSE ? 'data-no-toggle-off="1"' : '' ?>>
						<span style="<?= $checkin['completed'] > 0 ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg inline-img"> Not Checked Out</span>
						<span style="<?= $checkin['completed'] > 0 ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg inline-img"> Checked Out</span>
					</div>
				</div>
				<hr class="visible-xs">
				<?php $pdf_contents[] = ['Material', ($material['category'].': '.$material['sub_category'].' '.$material['name']).' - '.($checkin['arrived'] > 0 ? 'Checked Out' : 'Not Checked Out')]; ?>
			<?php } else if(in_array($checkin['src_table'],['equipment']) && strpos($value_config, ',Checkout '.$checkin['src_table'].',') !== FALSE) {
				$equipment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='{$checkin['item_id']}'")); ?>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?= $equipment['category'].': '.$equipment['make'].' ',$equipment['model'].' '.$equipment['label'].' '.$equipment['unit_number'] ?>:</label>
					<div class="col-sm-8 <?= $access_all_checkin == TRUE ? 'toggleSwitch mobile-lg' : '' ?>">
						<input type="hidden" name="checkin_id[]" value="<?= $checkin['item_id'] ?>">
						<input type="hidden" name="completed" data-table="ticket_attached" data-id="<?= $checkin['id'] ?>" data-id-field="id" value="<?= $checkin['completed'] ?>" class="toggle" <?= strpos($value_config,',Check Out Before Check In,') !== FALSE ? 'data-no-toggle-off="1"' : '' ?>>
						<span style="<?= $checkin['completed'] > 0 ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg inline-img"> Not Checked Out</span>
						<span style="<?= $checkin['completed'] > 0 ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg inline-img"> Checked Out</span>
					</div>
				</div>
				<hr class="visible-xs">
				<?php $pdf_contents[] = ['Equipment', ($equipment['category'].': '.$equipment['make'].' '.$equipment['model'].' '.$equipment['label'].' '.$equipment['unit_number']).' - '.($checkin['arrived'] > 0 ? 'Checked Out' : 'Not Checked Out')]; ?>
			<?php }
		}
	}
} ?>
