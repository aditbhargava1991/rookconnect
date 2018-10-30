<?php include_once('../include.php');
include_once('../Ticket/field_list.php');
include_once('../Ticket/config.php');
if(empty($ticketid) && !isset($value_config)) {
	$access_view_project_info = check_subtab_persmission($dbc, 'ticket', ROLE, 'view_project_info');
	$access_view_project_details = check_subtab_persmission($dbc, 'ticket', ROLE, 'view_project_details');
	$access_view_staff = check_subtab_persmission($dbc, 'ticket', ROLE, 'view_staff');
	$access_view_summary = check_subtab_persmission($dbc, 'ticket', ROLE, 'view_summary');
	$access_view_complete = check_subtab_persmission($dbc, 'ticket', ROLE, 'view_complete');
	$access_view_notifications = check_subtab_persmission($dbc, 'ticket', ROLE, 'view_notifications');
	if((isset($_GET['intake_key']) && !($ticketid > 0)) || $_GET['estimate_mode'] > 0) {
		$access_project = true;
		$access_staff = true;
		$access_contacts = true;
		$access_waitlist = true;
		$access_staff_checkin = true;
		$access_all_checkin = true;
		$access_medication = true;
		$access_complete = true;
		$access_services = true;
		$access_all = true;
		$access_any = true;
    }
	$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
	$value_config = ','.get_field_config($dbc, 'tickets').',';
	$sort_order = explode(',',get_config($dbc, 'ticket_sortorder'));
	if($ticketid > 0) {
		$get_ticket = $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'")->fetch_assoc();
		$ticket_type = empty($get_ticket['ticket_type']) ? $ticket_type : $get_ticket['ticket_type'];
	} else if(!empty($_GET['type'])) {
		$ticket_type = $_GET['type'];
	}
	if(!empty($ticket_type)) {
		$value_config .= get_config($dbc, 'ticket_fields_'.$ticket_type).',';
		$sort_order = explode(',',get_config($dbc, 'ticket_sortorder_'.$ticket_type));
	}
	$force_project = get_config($dbc, 'ticket_project_function');

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
		}
	}

	//Estimate Mode Fields
	if($_GET['estimate_mode'] == 1) {
		$value_config_all = $value_config;
		$value_config = ','.get_config($dbc, 'ticket_estimate_fields').',';
		if(!empty($ticket_type)) {
			$value_config .= get_config($dbc, 'ticket_estimate_fields_'.$ticket_type).',';
		}
		if(empty(trim($value_config,','))) {
			$value_config = $value_config_all;
		} else {
			foreach($action_mode_ignore_fields as $action_mode_ignore_field) {
				if(strpos(','.$value_config_all.',',','.$action_mode_ignore_field.',') !== FALSE) {
					$value_config .= ','.$action_mode_ignore_field;
				}
			}
		}
        foreach($estimate_mode_restrict as $remove_field) {
            $value_config = str_replace($remove_field.',','',$value_config);
        }
	}

	//Intake Fields
	if(isset($_GET['intake_key'])) {
		$value_config_all = $value_config;
		$value_config = ','.get_config($dbc, 'ticket_intake_fields').',';
		if(!empty($ticket_type)) {
			$value_config .= get_config($dbc, 'ticket_intake_fields_'.$ticket_type).',';
		}
		if(empty(trim($value_config,','))) {
			$value_config = $value_config_all;
		} else {
			if(strpos($value_config, ','."Hide Trash Icon".',') !== FALSE) {
				$hide_trash_icon = 1;
			}
			foreach($action_mode_ignore_fields as $action_mode_ignore_field) {
				if(strpos(','.$value_config_all.',',','.$action_mode_ignore_field.',') !== FALSE) {
					$value_config .= ','.$action_mode_ignore_field;
				}
			}
			$value_config = ','.implode(',',array_intersect(explode(',',$value_config), explode(',',$value_config_all))).',';
		}
	}

	$created_by = $get_ticket['created_by'];
	$created_date = $get_ticket['created_date'];
}
if($_GET['action_mode'] == 1) {
	$merged_config_fields = explode(',',$value_config);
	if(!in_array('Mileage',$merged_config_fields) && in_array('Drive Time',$merged_config_fields)) {
		$key = array_search('Drive Time',$merged_config_fields);
		$merged_config_fields[$key] = 'Mileage';
	}
	if(!in_array('Check In',$merged_config_fields) && in_array('Member Drop Off',$merged_config_fields)) {
		$key = array_search('Member Drop Off',$merged_config_fields);
		$merged_config_fields[$key] = 'Check In';
	}
	if(!in_array('Ticket Details',$merged_config_fields) && in_array('Services',$merged_config_fields)) {
		$key = array_search('Services',$merged_config_fields);
		$merged_config_fields[$key] = 'Ticket Details';
	}
	if(!in_array('Check Out',$merged_config_fields) && in_array('Check Out Member Pick Up',$merged_config_fields)) {
		$key = array_search('Check Out Member Pick Up',$merged_config_fields);
		$merged_config_fields[$key] = 'Check Out';
	}
	if(!in_array('Summary',$merged_config_fields) && in_array('Staff Summary',$merged_config_fields)) {
		$key = array_search('Staff Summary',$merged_config_fields);
		$merged_config_fields[$key] = 'Summary';
	}
	$sort_order = array_intersect($sort_order, $merged_config_fields);
}
?>
<?php if(count($ticket_tabs) > 1 && !($_GET['action_mode'] > 0 || $_GET['overview_mode'] > 0) && $tile_security['edit'] > 0 && !($strict_view > 0)) { ?>
	<a href="" data-tab-target="ticket_type"><li class="<?= $_GET['tab'] == 'ticket_type' ? 'active blue' : '' ?>"><?= TICKET_NOUN ?> Tab</li></a>
<?php } ?>
<?php $current_heading = '';
$current_heading_closed = true;
$value_config = ','.$value_config.',';
$sort_order = array_filter($sort_order);
foreach($sort_order as $sort_field) { ?>
	<?php //Add higher level heading
	$this_heading = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_headings` WHERE `ticket_type` = '".(empty($ticket_type) ? 'tickets' : 'tickets_'.$ticket_type)."' AND `accordion` = '".$sort_field."'"))['heading'];
	if($this_heading != $current_heading) {
		if(!$current_heading_closed) { ?>
				</ul>
			</li>
			<?php $current_heading_closed = true;
		}
		if(!empty($this_heading)) { ?>
			<li class="sidebar_heading sidebar-higher-level"><a class="cursor-hand collapsed sidebar_heading_collapse" data-toggle="collapse" data-target="#ticket_heading_<?= config_safe_str($this_heading) ?>"><?= $this_heading ?><span class="arrow" /></a>
				<ul class="collapse" id="ticket_heading_<?= config_safe_str($this_heading) ?>">
			<?php $current_heading_closed = false;
			$current_heading = $this_heading;
		}
	} ?>
	<?php if(strpos($value_config, ','.$sort_field.',') !== FALSE && substr($sort_field, 0, strlen('FFMCUST_')) === 'FFMCUST_') { ?>
		<a href="" data-tab-target="<?= str_replace(' ','_',$sort_field) ?>"><li class="<?= $_GET['tab'] == str_replace(' ','_',$sort_field) ? 'active blue' : '' ?>"><?= explode('FFMCUST_', $sort_field)[1] ?></li></a>
 	<?php } ?>
 	<?php $renamed_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($ticket_type) ? 'tickets' : 'tickets_'.$ticket_type)."' AND `accordion` = '".$sort_field."'"))['accordion_name']; ?>
	<?php if (strpos($value_config, ','."Information".',') !== FALSE && $sort_field == 'Information' && $access_view_project_info > 0) { ?>
		<a href="" data-tab-target="project_info"><li class="<?= $_GET['tab'] == 'project_info' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : ('manual' == $force_project ? PROJECT_NOUN : TICKET_NOUN).' Information' ?></li></a>
	<?php } ?>
	<?php if (strpos($value_config, ','."Purchase Order List".',') !== FALSE && $sort_field == 'Purchase Order List') { ?>
		<a href="" data-tab-target="ticket_po_number"><li class="<?= $_GET['tab'] == 'ticket_po_number' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Purchase Orders' ?></li></a>
	<?php } ?>
	<?php if (strpos($value_config, ','."Customer Orders".',') !== FALSE && $sort_field == 'Customer Orders') { ?>
		<a href="" data-tab-target="ticket_customer_order"><li class="<?= $_GET['tab'] == 'ticket_customer_order' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Customer Orders' ?></li></a>
	<?php } ?>
	<?php if (strpos($value_config, ','."Details".',') !== FALSE && $sort_field == 'Details') { ?>
		<a href="" data-tab-target="project_details"><li class="<?= $_GET['tab'] == 'project_details' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : ('manual' == $force_project ? PROJECT_NOUN : TICKET_NOUN).' Details' ?></li></a>
	<?php } ?>
	<?php if (strpos($value_config, ','."Contact Notes".',') !== FALSE && $sort_field == 'Contact Notes') { ?>
		<a href="" data-tab-target="ticket_contact_notes"><li class="<?= $_GET['tab'] == 'ticket_contact_notes' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : CONTACTS_NOUN.' Notes' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Path & Milestone".',') !== FALSE && $sort_field == 'Path & Milestone') { ?>
		<a href="" data-tab-target="ticket_path_milestone"><li class="<?= $_GET['tab'] == 'ticket_path_milestone' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : PROJECT_NOUN.' Path & Milestone' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Individuals".',') !== FALSE && $sort_field == 'Individuals') { ?>
		<a href="" data-tab-target="ticket_individuals"><li class="<?= $_GET['tab'] == 'ticket_individuals' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Individuals Present' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Fees".',') !== FALSE && $sort_field == 'Fees') { ?>
		<a href="" data-tab-target="ticket_fees"><li class="<?= $_GET['tab'] == 'ticket_fees' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Fees' ?></li></a>
	<?php } ?>

	<?php if ((strpos($value_config, ','."Location".',') !== FALSE || strpos($value_config, ','."Emergency".',') !== FALSE) && $sort_field == 'Location') { ?>
		<a href="" data-tab-target="ticket_location"><li class="<?= $_GET['tab'] == 'ticket_location' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Sites' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Members ID".',') !== FALSE && $sort_field == 'Members ID') { ?>
		<a href="" data-tab-target="ticket_members_id_card"><li class="<?= $_GET['tab'] == 'ticket_members_id_card' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Members ID Card' ?></li></a>
	<?php } ?>

	<?php if ((strpos($value_config, ','."Mileage".',') !== FALSE || strpos($value_config, ','."Drive Time".',') !== FALSE) && $sort_field == 'Mileage') { ?>
		<a href="" data-tab-target="ticket_mileage"><li class="<?= $_GET['tab'] == 'ticket_mileage' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : (strpos($value_config, ','."Mileage".',') !== FALSE ? 'Mileage' : 'Drive Time') ?></li></a>
	<?php } ?>

	<?php if(strpos($value_config, ',Staff,') !== FALSE && $sort_field == 'Staff' && $access_view_staff > 0) { ?>
		<a href="" data-tab-target="ticket_staff_list"><li class="<?= $_GET['tab'] == 'ticket_staff_list' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff' ?></li></a>
	<?php } ?>

	<?php if(strpos($value_config, ',Staff Tasks,') !== FALSE && $sort_field == 'Staff Tasks' && $access_view_staff > 0) { ?>
		<?php if($access_any == true) { ?>
			<a href="" data-tab-target="ticket_staff_assign_tasks"><li class="<?= $_GET['tab'] == 'ticket_staff_assign_tasks' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Assigned Tasks' ?></li></a>
		<?php } ?>
		<?php if($ticketid > 0 && $_GET['new_ticket'] != 'true') { ?>
			<a href="" data-tab-target="ticket_staff_tasks"><li class="<?= $_GET['tab'] == 'ticket_staff_tasks' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff Tasks' ?></li></a>
		<?php } ?>
	<?php } ?>

	<?php if(strpos($value_config, ',Members,') !== FALSE && $sort_field == 'Members') { ?>
		<a href="" data-tab-target="ticket_members"><li class="<?= $_GET['tab'] == 'ticket_members' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Members' ?></li></a>
	<?php } ?>

	<?php if(strpos($value_config, ',Clients,') !== FALSE && $sort_field == 'Clients') { ?>
		<a href="" data-tab-target="ticket_clients"><li class="<?= $_GET['tab'] == 'ticket_clients' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Clients' ?></li></a>
	<?php } ?>

	<?php if(strpos($value_config, ',Wait List,') !== FALSE && $sort_field == 'Wait List') { ?>
		<a href="" data-tab-target="ticket_wait_list"><li class="<?= $_GET['tab'] == 'ticket_wait_list' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Wait List' ?></li></a>
	<?php } ?>

	<?php if ((strpos($value_config, ','."Check In".',') !== FALSE || strpos($value_config, ','."Check In Member Drop Off".',') !== FALSE) && $sort_field == 'Check In') { ?>
		<a href="" data-tab-target="ticket_checkin"><li class="<?= $_GET['tab'] == 'ticket_checkin' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : (strpos($value_config, ','."Check In Member Drop Off".',') !== FALSE ? 'Member Drop Off' : 'Check In') ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Medication".',') !== FALSE && $access_medication === TRUE && $sort_field == 'Medication') { ?>
		<a href="" data-tab-target="ticket_medications"><li class="<?= $_GET['tab'] == 'ticket_medications' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Medication Administration' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Ticket Details".',') !== FALSE && $sort_field == 'Ticket Details') { ?>
		<a href="" data-tab-target="ticket_info"><li class="<?= $_GET['tab'] == 'ticket_info' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : TICKET_NOUN.' Details' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Services".',') !== FALSE && $sort_field == 'Ticket Details') { ?>
		<a href="" data-tab-target="ticket_info"><li class="<?= $_GET['tab'] == 'ticket_info' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Services' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Service Staff Checklist".',') !== FALSE && $sort_field == 'Service Staff Checklist') { ?>
		<a href="" data-tab-target="ticket_service_checklist"><li class="<?= $_GET['tab'] == 'ticket_service_checklist' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Service Checklist' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Service Extra Billing".',') !== FALSE && $sort_field == 'Service Extra Billing') {
		$display_none = '';
		if(strpos($value_config, ',Service Extra Billing Display Only If Exists,') !== FALSE || strpos($value_config, ',Service Extra Billing Add Option,') !== FALSE) {
			$num_extra_billing = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`ticketcommid`) `num_rows` FROM `ticket_comment` WHERE `ticketid` = '$ticketid' AND `deleted` = 0 AND `type` = 'service_extra_billing'"))['num_rows'];
			if(!($num_extra_billing > 0)) {
				$display_none = 'style="display:none;"';
			}
		} ?>
		<a href="" data-tab-target="ticket_service_extra_billing" class="service_extra_billing" <?= $display_none ?>><li class="<?= $_GET['tab'] == 'ticket_service_extra_billing' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Service Extra Billing' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Equipment".',') !== FALSE && $sort_field == 'Equipment') { ?>
		<a href="" data-tab-target="ticket_equipment"><li class="<?= $_GET['tab'] == 'ticket_equipment' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Equipment' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Checklist".',') !== FALSE && $access_all > 0 && $sort_field == 'Checklist') { ?>
		<a href="" data-tab-target="ticket_checklist"><li class="<?= $_GET['tab'] == 'ticket_checklist' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Checklist' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Checklist Items".',') !== FALSE && $access_all > 0 && $sort_field == 'Checklist Items') { ?>
		<a href="" data-tab-target="ticket_view_checklist"><li class="<?= $_GET['tab'] == 'ticket_view_checklist' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Checklists' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Charts".',') !== FALSE && $access_all > 0 && $sort_field == 'Charts') { ?>
		<a href="" data-tab-target="ticket_view_charts"><li class="<?= $_GET['tab'] == 'ticket_view_charts' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Charts' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Safety".',') !== FALSE && $access_all > 0 && $sort_field == 'Safety') { ?>
		<a href="" data-tab-target="ticket_safety"><li class="<?= $_GET['tab'] == 'ticket_safety' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Safety' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Materials".',') !== FALSE && $sort_field == 'Materials') { ?>
		<a href="" data-tab-target="ticket_materials"><li class="<?= $_GET['tab'] == 'ticket_materials' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Materials' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ',Miscellaneous') !== FALSE && $sort_field == 'Miscellaneous') { ?>
		<a href="" data-tab-target="ticket_miscellaneous"><li class="<?= $_GET['tab'] == 'ticket_miscellaneous' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Miscellaneous' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ',Inventory Basic') !== FALSE && $sort_field == 'Inventory') { ?>
		<a href="" data-tab-target="ticket_inventory"><li class="<?= $_GET['tab'] == 'ticket_inventory' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Inventory' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Inventory General".',') !== FALSE && $sort_field == 'Inventory General') { ?>
		<a href="" data-tab-target="ticket_inventory_general"><li class="<?= $_GET['tab'] == 'ticket_inventory_general' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'General Cargo / Inventory Information' ?></li></a>
		<?php if(strpos($value_config, ',Inventory General Detail,') !== FALSE) { ?>
			<ul style="margin-top: 0; <?= (in_array('ticket_inventory_general',$ticket_tab_locks) && !in_array('ticket_inventory_general',$unlocked_tabs) ? 'display:none;' : '') ?>">
				<?php $general_list = $dbc->query("SELECT `id`, `piece_type`, `piece_num` FROM `ticket_attached` WHERE `src_table`='inventory_general' AND (IFNULL(`description`,'') != 'import' OR IFNULL(`piece_type`,'') != '') AND `deleted`=0 AND `ticketid`='$ticketid'");
				$i = 0;
				while($general_item = $general_list->fetch_assoc()) { ?>
					<a href="" data-tab-target="general_detail_<?= $general_item['id'] ?>"><li><?= "Piece ".(++$i).': '.$general_item['piece_type'] ?></li></a>
				<?php } ?>
				<?php $general_description = $dbc->query("SELECT `description` FROM `ticket_attached` WHERE `src_table`='inventory_general' AND `description`='import' AND `ticketid`='$ticketid' AND `ticketid` > 0")->fetch_assoc()['description'];
				if($ticketid > 0 && strpos($value_config,',Inventory General Detail by Pallet,') !== FALSE) {
					$pallets = $dbc->query("SELECT `pallet`, COUNT(*) `items` FROM `inventory` LEFT JOIN `ticket_attached` ON `inventory`.`inventoryid`=`ticket_attached`.`item_id` WHERE `ticket_attached`.`deleted`=0 AND `ticket_attached`.`src_table`='inventory' AND `ticket_attached`.`ticketid`='$ticketid' GROUP BY `inventory`.`pallet` ORDER BY IFNULL(`inventory`.`pallet`,'') = '', `inventory`.`pallet`");
					$pallet = $pallets->fetch_assoc();
				} else {
					$pallet = ['pallet'=>'','items'=>0];
				}
				if(strpos($value_config,',Inventory General Detail by Pallet,') !== FALSE && ($pallet['pallet'] != '' || $general_description == 'import')) {
					$i = 0;
					do { ?>
						<a href="" data-tab-target="general_pallet_<?= $i++ ?>"><li><?= ($pallet['pallet'] != '' ? $pallet['pallet'] : 'No Pallet Assigned').': '.$pallet['items'] ?>
							<?php if(strpos($value_config,',Inventory General Pallet Default Locked,') !== FALSE && !in_array('inventory_general_pallet_'.config_safe_str($pallet['pallet']),$unlocked_tabs)) { ?>
								<em class="cursor-hand tab_lock_toggle_link pull-right"><img class="inline-img" src="../img/icons/lock.png"></em>
							<?php } ?></li></a>
					<?php } while($pallet = $pallets->fetch_assoc());
				} ?>
			</ul>
		<?php } ?>
	<?php } ?>

	<?php if (strpos($value_config, ','."Inventory Detail".',') !== FALSE && $sort_field == 'Inventory Detail') { ?>
		<a href="" data-tab-target="ticket_inventory_detailed" data-toggle="collapse" data-target="#collapse_status"><li class="<?= $_GET['tab'] == 'ticket_inventory_detailed' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Detailed Cargo / Inventory Information' ?>
			<img class="inline-img cursor-hand tab_lock_toggle_link" src="../img/icons/lock.png" style="<?= (in_array('ticket_inventory_detailed',$ticket_tab_locks) && !in_array('ticket_inventory_detailed',$unlocked_tabs) ? '' : 'display:none;') ?>"></li></a>
		<ul style="margin-top: 0; <?= (in_array('ticket_inventory_detailed',$ticket_tab_locks) && !in_array('ticket_inventory_detailed',$unlocked_tabs) ? 'display:none;' : '') ?>">
			<?php $general_list = $dbc->query("SELECT `id`, `piece_type`, `piece_num` FROM `ticket_attached` WHERE `src_table`='inventory_general' AND `deleted`=0 AND `ticketid`='$ticketid'");
			while($general_item = $general_list->fetch_assoc()) {
				if(!($general_item['piece_num'] > 0)) {
					$general_item['piece_num'] = 1;
				}
				for($i = 0; $i < $general_item['piece_num']; $i++) { ?>
					<a href="" data-tab-target="detail_inventory_<?= config_safe_str($general_item['piece_type'].'_'.$i) ?>"><li><?= $general_item['piece_type']." #".($i+1) ?></li></a>
				<?php }
			} ?>
		</ul>
	<?php } ?>

	<?php if (strpos($value_config, ','."Inventory Return".',') !== FALSE && $sort_field == 'Inventory Return') { ?>
		<a href="" data-tab-target="ticket_inventory_return"><li class="<?= $_GET['tab'] == 'ticket_inventory_return' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Return Information' ?>
			<img class="inline-img cursor-hand tab_lock_toggle_link" src="../img/icons/lock.png" style="<?= (in_array('ticket_inventory_return',$ticket_tab_locks) && !in_array('ticket_inventory_return',$unlocked_tabs) ? '' : 'display:none;') ?>"></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Purchase Orders".',') !== FALSE && $access_all > 0 && $sort_field == 'Purchase Orders') { ?>
		<a href="" data-tab-target="ticket_purchase_orders"><li class="<?= $_GET['tab'] == 'ticket_purchase_orders' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Purchase Orders' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Attached Purchase Orders".',') !== FALSE && $access_all > 0 && $sort_field == 'Attached Purchase Orders') { ?>
		<a href="" data-tab-target="ticket_attach_purchase_orders"><li class="<?= $_GET['tab'] == 'ticket_attach_purchase_orders' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Purchase Orders' ?></li></a>
	<?php } ?>


    <?php if (strpos($value_config, ','."Delivery".',') !== FALSE && strpos($value_config, ',Delivery Stops') === FALSE && $sort_field == 'Delivery') { ?>
        <?php $stopid = filter_var($_GET['stop'],FILTER_SANITIZE_STRING);
        $stop_list = $dbc->query("SELECT * FROM `ticket_schedule` WHERE `ticketid`='$ticketid' AND `deleted`=0 AND '$stopid' IN (`id`,'')"); ?>
        <li class="<?= $_GET['tab'] == 'ticket_delivery' ? 'active blue' : '' ?> sidebar_heading sidebar-higher-level"><a class="cursor-hand collapsed sidebar_heading_collapse" <?= $stop_list->num_rows > 0 ? 'data-toggle="collapse" data-target="#delivery_list"' : 'data-tab-target="ticket_delivery"' ?>><?= (!empty($renamed_accordion) ? $renamed_accordion : 'Delivery Details') ?><?= $stop_list->num_rows > 0 ? '<span class="pull-right arrow"></span><span class="pull-right">'.$stop_list->num_rows.'</span>' : '' ?></a>
        <ul id="delivery_list" class="collapse">
        <?php while($ticket_stop = $stop_list->fetch_assoc()) { ?>
            <a href="" data-tab-target="ticket_delivery_<?= $ticket_stop['id'] ?>"><li class="<?= $_GET['tab'] == 'ticket_delivery' ? 'active blue' : '' ?> sidebar-lower-level"><?= ($ticket_stop['id'] > 0 ? (empty($ticket_stop['client_name']) ? $ticket_stop['location_name'] : $ticket_stop['client_name']) : '').'<br /><em>Status: '.$ticket_stop['status'].'</em>' ?></li></a>
        <?php } ?>
        </ul></li>

    <?php } else if (strpos($value_config, ','."Delivery".',') !== FALSE && $sort_field == 'Delivery') { ?>
        <a href="" data-tab-target="ticket_delivery"><li class="<?= $_GET['tab'] == 'ticket_delivery' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Delivery Details' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ',Delivery Summary,') !== FALSE && strpos($value_config, ',Delivery Summary') !== FALSE && $sort_field == 'Delivery Summary') { ?>
		<?php $this_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($ticket_type) ? 'tickets' : 'tickets_'.$ticket_type)."' AND `accordion` = 'Delivery Summary'"))['accordion_name']; ?>
		<a href="" data-tab-target="ticket_delivery_summary"><li class="<?= $_GET['tab'] == 'delivery_summary' ? 'active blue' : '' ?>"><?= !empty($this_accordion) ? $this_accordion : (!empty($renamed_accordion) ? $renamed_accordion : 'Delivery Summary') ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ',Transport,') !== FALSE && strpos($value_config, ',Transport Origin') !== FALSE && $sort_field == 'Transport') { ?>
		<?php $this_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($ticket_type) ? 'tickets' : 'tickets_'.$ticket_type)."' AND `accordion` = 'Transport Origin'"))['accordion_name']; ?>
		<a href="" data-tab-target="ticket_transport_origin"><li class="<?= $_GET['tab'] == 'ticket_transport_origin' ? 'active blue' : '' ?>"><?= !empty($this_accordion) ? $this_accordion : (!empty($renamed_accordion) ? $renamed_accordion : 'Transport Log').' - Origin' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ',Transport,') !== FALSE && strpos($value_config, ',Transport Destination') !== FALSE && $sort_field == 'Transport') { ?>
		<?php $this_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($ticket_type) ? 'tickets' : 'tickets_'.$ticket_type)."' AND `accordion` = 'Transport Destination'"))['accordion_name']; ?>
		<a href="" data-tab-target="ticket_transport_destination"><li class="<?= $_GET['tab'] == 'ticket_transport_destination' ? 'active blue' : '' ?>"><?= !empty($this_accordion) ? $this_accordion : (!empty($renamed_accordion) ? $renamed_accordion : 'Transport Log').' - Destination' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ',Transport,') !== FALSE && strpos(str_replace(['Transport Origin','Transport Destination'],'',$value_config), ',Transport ') !== FALSE && $sort_field == 'Transport') {
		$default_contact_category = get_config($dbc, 'transport_carrier_category');
		$contact_category = ($ticket_type == '' ? $default_contact_category : get_config($dbc, 'transport_carrier_category_'.$ticket_type));
		if($contact_category == '') {
			$contact_category = $default_contact_category;
		} ?>
		<?php $this_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($ticket_type) ? 'tickets' : 'tickets_'.$ticket_type)."' AND `accordion` = 'Transport Carrier'"))['accordion_name']; ?>
		<a href="" data-tab-target="ticket_transport_details"><li class="<?= $_GET['tab'] == 'ticket_transport_details' ? 'active blue' : '' ?>"><?= !empty($this_accordion) ? $this_accordion : (!empty($renamed_accordion) ? $renamed_accordion.' - '.$contact_category : $contact_category.' Details') ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Documents".',') !== FALSE && $sort_field == 'Documents') { ?>
		<a href="" data-tab-target="view_ticket_documents"><li class="<?= $_GET['tab'] == 'view_ticket_documents' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Documents' ?>
			<img class="inline-img cursor-hand tab_lock_toggle_link" src="../img/icons/lock.png" style="<?= (in_array('view_ticket_documents',$ticket_tab_locks) && !in_array('view_ticket_documents',$unlocked_tabs) ? '' : 'display:none;') ?>"></li></a>
	<?php } ?>

	<?php if ((strpos($value_config, ','."Check Out".',') !== FALSE || strpos($value_config, ','."Check Out Member Pick Up".',') !== FALSE) && $sort_field == 'Check Out') { ?>
		<a href="" data-tab-target="ticket_checkout"><li class="<?= $_GET['tab'] == 'ticket_checkout' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : (strpos($value_config, ','."Check Out Member Pick Up".',') !== FALSE ? 'Member Pick Up' : 'Check Out') ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Staff Check Out".',') !== FALSE && $sort_field == 'Staff Check Out') { ?>
		<a href="" data-tab-target="ticket_checkout_staff"><li class="<?= $_GET['tab'] == 'ticket_checkout_staff' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff Check Out' ?></li></a>
	<?php } ?>

	<?php if ((strpos($value_config, ','."Deliverables".',') !== FALSE || strpos($value_config, ','."Deliverable To Do".',') !== FALSE || strpos($value_config, ','."Deliverable Internal".',') !== FALSE || strpos($value_config, ','."Deliverable Customer".',') !== FALSE) && $sort_field == 'Deliverables') { ?>
		<a href="" data-tab-target="view_ticket_deliverables"><li class="<?= $_GET['tab'] == 'view_ticket_deliverables' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Deliverables' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Time Tracking Block".',') !== FALSE && $sort_field == 'Timer') { ?>
		<a href="" data-tab-target="view_ticket_timer"><li class="<?= $_GET['tab'] == 'view_ticket_timer' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Time Tracking' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Day Tracking Block".',') !== FALSE && $access_all > 0 && $sort_field == 'Timer') { ?>
		<a href="" data-tab-target="view_day_tracking"><li class="<?= $_GET['tab'] == 'view_day_tracking' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Day Tracking' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Addendum".',') !== FALSE && $sort_field == 'Addendum') { ?>
		<a href="" data-tab-target="addendum_view_ticket_comment"><li class="<?= $_GET['tab'] == 'addendum_view_ticket_comment' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Addendum' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Client Log".',') !== FALSE && $sort_field == 'Client Log') { ?>
		<a href="" data-tab-target="ticket_log_notes"><li class="<?= $_GET['tab'] == 'ticket_log_notes' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff Log Notes' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Debrief".',') !== FALSE && $sort_field == 'Debrief') { ?>
		<a href="" data-tab-target="debrief_view_ticket_comment"><li class="<?= $_GET['tab'] == 'debrief_view_ticket_comment' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Debrief' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Member Log Notes".',') !== FALSE && $sort_field == 'Member Log Notes') { ?>
		<a href="" data-tab-target="member_view_ticket_comment"><li class="<?= $_GET['tab'] == 'member_view_ticket_comment' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : $category.' Specific Daily Log Notes' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Cancellation".',') !== FALSE && $sort_field == 'Cancellation') { ?>
		<a href="" data-tab-target="ticket_cancellation"><li class="<?= $_GET['tab'] == 'ticket_cancellation' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Cancellation' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Custom Notes".',') !== FALSE && $sort_field == 'Custom Notes') { ?>
		<a href="" data-tab-target="custom_view_ticket_comment"><li class="<?= $_GET['tab'] == 'custom_view_ticket_comment' ? 'active blue' : '' ?>"><?= get_config($dbc, 'ticket_custom_notes_heading') ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Internal Communication".',') !== FALSE && $sort_field == 'Internal Communication') { ?>
		<a href="" data-tab-target="internal_communication"><li class="<?= $_GET['tab'] == 'internal_communication' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Internal Communication' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."External Communication".',') !== FALSE && $sort_field == 'External Communication') { ?>
		<a href="" data-tab-target="external_communication"><li class="<?= $_GET['tab'] == 'external_communication' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'External Communication' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Notes".',') !== FALSE && $sort_field == 'Notes') { ?>
		<a href="" data-tab-target="notes_view_ticket_comment"><li class="<?= $_GET['tab'] == 'notes_view_ticket_comment' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Notes' ?></li></a>
	<?php } ?>

	<?php if ((strpos($value_config, ','."Summary".',') !== FALSE || strpos($value_config, ','."Staff Summary".',') !== FALSE) && $sort_field == 'Summary' && $access_view_summary > 0) { ?>
		<a href="" data-tab-target="ticket_summary"><li class="<?= $_GET['tab'] == 'ticket_summary' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : (strpos($value_config, ','."Staff Summary".',') !== FALSE ? 'Staff Summary' : 'Summary') ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Multi-Disciplinary Summary Report".',') !== FALSE && $sort_field == 'Multi-Disciplinary Summary Report') { ?>
		<a href="" data-tab-target="view_multi_disciplinary_summary_report"><li class="<?= $_GET['tab'] == 'view_multi_disciplinary_summary_report' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Multi-Disciplinary Summary Report' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Complete".',') !== FALSE && $sort_field == 'Complete' && $access_view_complete > 0) { ?>
		<a href="" data-tab-target="ticket_complete"><li class="<?= $_GET['tab'] == 'ticket_complete' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Complete '.TICKET_NOUN ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Notifications".',') !== FALSE && $sort_field == 'Notifications' && $access_view_notifications > 0) { ?>
		<a href="" data-tab-target="view_ticket_notifications"><li class="<?= $_GET['tab'] == 'view_ticket_notifications' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Notifications' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Region Location Classification".',') !== FALSE && $sort_field == 'Region Location Classification') { ?>
		<a href="" data-tab-target="ticket_reg_loc_class"><li class="<?= $_GET['tab'] == 'ticket_reg_loc_class' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Region/Location/Classification' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Incident Reports".',') !== FALSE && $sort_field == 'Incident Reports') { ?>
		<a href="" data-tab-target="view_ticket_incident_reports"><li class="<?= $_GET['tab'] == 'view_ticket_incident_reports' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : INC_REP_TILE ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Billing".',') !== FALSE && $sort_field == 'Billing') { ?>
		<a href="" data-tab-target="ticket_billing"><li class="<?= $_GET['tab'] == 'ticket_billing' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Billing' ?></li></a>
	<?php } ?>


	<?php if (strpos($value_config, ','."Customer Notes".',') !== FALSE && $sort_field == 'Customer Notes') { ?>
        <?php $stopid = filter_var($_GET['stop'],FILTER_SANITIZE_STRING);
        $stop_list = $dbc->query("SELECT * FROM `ticket_schedule` WHERE `ticketid`='$ticketid' AND `deleted`=0 AND '$stopid' IN (`id`,'')"); ?>
        <li class="<?= $_GET['tab'] == 'ticket_customer_notes' ? 'active blue' : '' ?> sidebar_heading sidebar-higher-level"><a class="cursor-hand collapsed sidebar_heading_collapse" <?= $stop_list->num_rows > 0 ? 'data-toggle="collapse" data-target="#customer_notes"' : 'data-tab-target="ticket_customer_notes"' ?>><?= (!empty($renamed_accordion) ? $renamed_accordion : 'Customer Notes') ?><?= $stop_list->num_rows > 0 ? '<span class="pull-right arrow"></span><span class="pull-right">'.$stop_list->num_rows.'</span>' : '' ?></a>
        <ul class="collapse" id="customer_notes">
        <?php while($ticket_stop = $stop_list->fetch_assoc()) { ?>
            <a href="" data-tab-target="ticket_customer_notes_<?= $ticket_stop['id'] ?>"><li class="sidebar-lower-level <?= $_GET['tab'] == 'ticket_customer_notes' ? 'active blue' : '' ?>"><?= ($ticket_stop['id'] > 0 ? (empty($ticket_stop['client_name']) ? $ticket_stop['location_name'] : $ticket_stop['client_name']) : '').'<br /><em>Status: '.$ticket_stop['status'].'</em>' ?></li></a>
        <?php } ?>
        </ul></li>

	<?php } ?>

	<?php if (strpos($value_config, ','."Reading".',') !== FALSE && $sort_field == 'Reading') { ?>
		<a href="" data-tab-target="ticket_readings"><li class="<?= $_GET['tab'] == 'ticket_readings' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Monitor Readings' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Tank Reading".',') !== FALSE && $sort_field == 'Tank Reading') { ?>
		<a href="" data-tab-target="ticket_tank_readings"><li class="<?= $_GET['tab'] == 'ticket_tank_readings' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Tank Readings' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Shipping List".',') !== FALSE && $sort_field == 'Shipping List') { ?>
		<a href="" data-tab-target="ticket_shipping_list"><li class="<?= $_GET['tab'] == 'ticket_shipping_list' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Shipping List' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Location Details".',') !== FALSE && $sort_field == 'Location Details') { ?>
		<a href="" data-tab-target="ticket_location_details"><li class="<?= $_GET['tab'] == 'ticket_location_details' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Location Details' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Residue".',') !== FALSE && $sort_field == 'Residue') { ?>
		<a href="" data-tab-target="ticket_residues"><li class="<?= $_GET['tab'] == 'ticket_residues' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Residue' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Other List".',') !== FALSE && $sort_field == 'Other List') { ?>
		<a href="" data-tab-target="ticket_other_list"><li class="<?= $_GET['tab'] == 'ticket_other_list' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Other Products' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Pressure".',') !== FALSE && $sort_field == 'Pressure') { ?>
		<a href="" data-tab-target="ticket_pressure"><li class="<?= $_GET['tab'] == 'ticket_pressure' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Pressure' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Chemicals".',') !== FALSE && $sort_field == 'Chemicals') { ?>
		<a href="" data-tab-target="ticket_chemicals"><li class="<?= $_GET['tab'] == 'ticket_chemicals' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Chemicals' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Application Report".',') !== FALSE && $sort_field == 'Application Report') { ?>
		<a href="" data-tab-target="ticket_apply_report"><li class="<?= $_GET['tab'] == 'ticket_apply_report' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Application Report' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Intake".',') !== FALSE && $sort_field == 'Intake') { ?>
		<a href="" data-tab-target="ticket_intake"><li class="<?= $_GET['tab'] == 'ticket_intake' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Intake' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."History".',') !== FALSE && $sort_field == 'History') { ?>
		<a href="" data-tab-target="ticket_history"><li class="<?= $_GET['tab'] == 'ticket_history' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'History' ?></li></a>
	<?php } ?>

	<?php if (strpos($value_config, ','."Work History".',') !== FALSE && $sort_field == 'Work History' && $access_complete) { ?>
		<a href="" data-tab-target="ticket_work_history"><li class="<?= $_GET['tab'] == 'ticket_work_history' ? 'active blue' : '' ?>"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Work History' ?></li></a>
	<?php } ?>
<?php } ?>
<?php //Close heading if not already closed
if(!$current_heading_closed) { ?>
		</ul>
	</li>
<?php } ?>
<li>Created<?= ($created_by > 0 ? ' by '.get_staff($dbc, $created_by).'<br />' : '').' on '.(!empty($created_date) ? convert_timestamp_mysql($dbc, $created_date, true) : date('Y-m-d')) ?></li>
<?php if(time() < strtotime(str_replace('0000-00-00',date('Y-m-d'),$get_ticket['flag_start'])) || time() > strtotime(str_replace('9999',date('Y'),$get_ticket['flag_end']).' + 1 day')) {
	$get_ticket['flag_colour'] = '';
}
if($get_ticket['flag_colour'] != '' && $get_ticket['flag_colour'] != 'FFFFFF') {
	$flag_comment = '';
	$quick_action_icons = explode(',',get_config($dbc, 'quick_action_icons'));
	if(in_array('flag_manual',$quick_action_icons)) {
		$flag_comment = html_entity_decode($dbc->query("SELECT `comment` FROM `ticket_comment` WHERE `deleted`=0 AND `ticketid`='$ticketid' AND `type`='flag_comment' ORDER BY `ticketcommid` DESC")->fetch_assoc()['comment']);
	} else {
		$ticket_flag_names = [''=>''];
		$flag_names = explode('#*#', get_config($dbc, 'ticket_colour_flag_names'));
		foreach(explode(',',get_config($dbc, 'ticket_colour_flags')) as $i => $colour) {
			$ticket_flag_names[$colour] = $flag_names[$i];
		}
		$flag_comment = $ticket_flag_names[$get_ticket['flag_colour']];
	} ?>
<?php } ?>
<li class="flag-label-block" style="<?= $get_ticket['flag_colour'] != '' && $get_ticket['flag_colour'] != 'FFFFFF' ? '' : 'display:none;' ?>background-color:#<?= $get_ticket['flag_colour'] ?>;">Flagged<?= empty($flag_comment) ? '' : ': '.$flag_comment ?></li>