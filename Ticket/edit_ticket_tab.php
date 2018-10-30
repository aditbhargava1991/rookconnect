<?php $include_folder = '';
include_once('../include.php');
include_once('../Ticket/field_list.php');
include_once('../Ticket/config.php');
if(!empty($_POST['accordion'])) {
	$sort_field = $_POST['accordion'];
}
if($_GET['tab'] == 'ticket_medications') {
	$sort_field = 'Medication';
} else if($_GET['tab'] == 'ticket_staff_list') {
	$sort_field = 'Staff';
} else if($_GET['tab'] == 'ticket_log_notes') {
	$sort_field = 'Client Log';
} else if($_GET['tab'] == 'addendum_view_ticket_comment') {
	$sort_field = 'Addendum';
} else if($_GET['tab'] == 'notes_view_ticket_comment') {
	$sort_field = 'Notes';
} else if($_GET['tab'] == 'debrief_view_ticket_comment') {
	$sort_field = 'Debrief';
} else if($_GET['tab'] == 'view_multi_disciplinary_summary_report') {
	$sort_field = 'Multi-Disciplinary Summary Report';
} else if($_GET['tab'] == 'ticket_complete') {
	$sort_field = 'Complete';
} else if($_GET['tab'] == 'member_view_ticket_comment') {
	$sort_field = 'Member Log Notes';
} else if($_GET['tab'] == 'ticket_checkout_staff') {
	$sort_field = 'Staff Check Out';
} else if($_GET['tab'] == 'project_info') {
	$sort_field = 'Information';
} else if($_GET['tab'] == 'project_details') {
	$sort_field = 'Details';
} else if($_GET['tab'] == 'ticket_path_milestone') {
	$sort_field = 'Path & Milestone';
} else if($_GET['tab'] == 'ticket_individuals') {
	$sort_field = 'Individuals';
} else if($_GET['tab'] == 'ticket_fees') {
	$sort_field = 'Fees';
} else if($_GET['tab'] == 'ticket_location') {
	$sort_field = 'Location';
} else if($_GET['tab'] == 'ticket_members_id_card') {
	$sort_field = 'Members ID';
} else if($_GET['tab'] == 'ticket_mileage') {
	$sort_field = 'Mileage';
} else if($_GET['tab'] == 'ticket_staff_assign_tasks') {
	$sort_field = 'Staff Tasks';
} else if($_GET['tab'] == 'ticket_staff_tasks') {
	$sort_field = 'Staff Tasks';
} else if($_GET['tab'] == 'ticket_members') {
	$sort_field = 'Members';
} else if($_GET['tab'] == 'ticket_clients') {
	$sort_field = 'Clients';
} else if($_GET['tab'] == 'ticket_wait_list') {
	$sort_field = 'Wait List';
} else if($_GET['tab'] == 'ticket_checkin') {
	$sort_field = 'Check In';
} else if($_GET['tab'] == 'ticket_info') {
	$sort_field = 'Ticket Details';
} else if($_GET['tab'] == 'ticket_info') {
	$sort_field = 'Ticket Details';
} else if($_GET['tab'] == 'ticket_equipment') {
	$sort_field = 'Equipment';
} else if($_GET['tab'] == 'ticket_checklist') {
	$sort_field = 'Checklist';
} else if($_GET['tab'] == 'ticket_view_checklist') {
	$sort_field = 'Checklist Items';
} else if($_GET['tab'] == 'ticket_view_charts') {
	$sort_field = 'Charts';
} else if($_GET['tab'] == 'ticket_safety') {
	$sort_field = 'Safety';
} else if($_GET['tab'] == 'ticket_materials') {
	$sort_field = 'Materials';
} else if($_GET['tab'] == 'ticket_miscellaneous') {
	$sort_field = 'Miscellaneous';
} else if($_GET['tab'] == 'ticket_inventory') {
	$sort_field = 'Inventory';
} else if($_GET['tab'] == 'ticket_inventory_general') {
	$sort_field = 'Inventory General';
} else if($_GET['tab'] == 'ticket_inventory_detailed') {
	$sort_field = 'Inventory Detail';
} else if($_GET['tab'] == 'ticket_inventory_return') {
	$sort_field = 'Inventory Return';
} else if(strpos($_GET['tab'],'inventory') !== FALSE) {
	$sort_field = 'Inventory';
} else if($_GET['tab'] == 'ticket_purchase_orders') {
	$sort_field = 'Purchase Orders';
} else if($_GET['tab'] == 'ticket_attach_purchase_orders') {
	$sort_field = 'Attached Purchase Orders';
} else if($_GET['tab'] == 'ticket_delivery') {
	$sort_field = 'Delivery';
} else if($_GET['tab'] == 'ticket_transport_origin') {
	$sort_field = 'Transport';
} else if($_GET['tab'] == 'ticket_transport_destination') {
	$sort_field = 'Transport';
} else if($_GET['tab'] == 'ticket_transport_details') {
	$sort_field = 'Transport';
} else if($_GET['tab'] == 'view_ticket_documents') {
	$sort_field = 'Documents';
} else if($_GET['tab'] == 'ticket_checkout') {
	$sort_field = 'Check Out';
} else if($_GET['tab'] == 'view_ticket_deliverables') {
	$sort_field = 'Deliverables';
} else if($_GET['tab'] == 'view_ticket_timer') {
	$sort_field = 'Timer';
} else if($_GET['tab'] == 'view_day_tracking') {
	$sort_field = 'Timer';
} else if($_GET['tab'] == 'ticket_cancellation') {
	$sort_field = 'Cancellation';
} else if($_GET['tab'] == 'custom_view_ticket_comment') {
	$sort_field = 'Custom Notes';
} else if($_GET['tab'] == 'ticket_summary') {
	$sort_field = 'Summary';
} else if($_GET['tab'] == 'view_ticket_notifications') {
	$sort_field = 'Notifications';
} else if($_GET['tab'] == 'ticket_reg_loc_class') {
	$sort_field = 'Region Location Classification';
} else if($_GET['tab'] == 'view_ticket_incident_reports') {
	$sort_field = 'Incident Reports';
} else if($_GET['tab'] == 'ticket_billing') {
	$sort_field = 'Billing';
} else if($_GET['tab'] == 'ticket_customer_notes') {
	$sort_field = 'Customer Notes';
} else if($_GET['tab'] == 'ticket_residues') {
	$sort_field = 'Residue';
} else if($_GET['tab'] == 'ticket_readings') {
	$sort_field = 'Reading';
} else if($_GET['tab'] == 'ticket_other_list') {
	$sort_field = 'Other List';
} else if($_GET['tab'] == 'ticket_pressure') {
	$sort_field = 'Pressure';
} else if($_GET['tab'] == 'ticket_chemicals') {
	$sort_field = 'Chemicals';
} else if($_GET['tab'] == 'ticket_intake') {
	$sort_field = 'Intake';
} else if($_GET['tab'] == 'ticket_history') {
	$sort_field = 'History';
} else if($_GET['tab'] == 'ticket_work_history') {
	$sort_field = 'Work History';
} else if($_GET['tab'] == 'ticket_tank_readings.php') {
	$sort_field = 'Tank Reading';
} else if($_GET['tab'] == 'ticket_shipping_list.php') {
	$sort_field = 'Shipping List';
} else if($_GET['tab'] == 'ticket_location_details.php') {
	$sort_field = 'Location Details';
} else if($_GET['tab'] == 'ticket_service_checklist.php') {
	$sort_field = 'Service Staff Checklist';
} else if($_GET['tab'] == 'ticket_service_extra_billing.php') {
	$sort_field = 'Service Extra Billing';
} else if($_GET['tab'] == 'internal_communication') {
	$sort_field = 'Internal Communication';
} else if($_GET['tab'] == 'external_communication') {
	$sort_field = 'External Communication';
}

if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit_ticket_tab.php' && ($_GET['ticketid'] > 0 || !empty($_GET['tab'])) && !$generate_pdf) {
	$ticketid = $_GET['edit'] = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
	if(!empty($_GET['from'])) {
		echo '<input type="hidden" name="from" value="'.$_GET['from'].'">';
		$back_url = urldecode($_GET['from']);
	}

	$value_config = get_field_config($dbc, 'tickets');
	$ticket_tab_locks = get_config($dbc, 'ticket_tab_locks');
	$force_project = get_config($dbc, 'ticket_project_function');
	$client_accordion_category = get_config($dbc, 'client_accordion_category');
	$calendar_window = $dbc->query("SELECT MIN(`value`) `window` FROM `general_configuration` WHERE `name` LIKE '%_increments' AND `value` > 0")->fetch_assoc()['window'];
	$hour_increment = get_config($dbc, "ticket_hour_increments");
	if($hour_increment > 0 && $hour_increment <= 60) {
		$hour_increment = $hour_increment / 60;
	} else {
		$hour_increment = 'any';
	}
	$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `position`, `positions_allowed` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"));

	$rate_contact = get_config($dbc, 'rate_card_contact_'.$tab) ?: get_config($dbc, 'rate_card_contact');
	switch($rate_contact) {
		case 'businessid': $rate_contact = $bill['businessid']; break;
		case 'agentid': $rate_contact = $bill['agentid']; break;
		default: $rate_contact = explode(':',$rate_contact);
			$rate_contactid = $dbc->query("SELECT `vendor`,`carrier` FROM `ticket_schedule` WHERE `ticketid`='$ticketid' AND `type`='{$rate_contact[0]}'")->fetch_assoc();
			$rate_contact = $rate_contactid[$rate_contact[1]];
			break;
	}
	if(explode(':',$get_ticket['rate_card'])[1] == 'company') {
		$rate_card = get_field_value('rate_card_name','company_rate_card','companyrcid',explode(':',$get_ticket['rate_card'])[1]);
	}

	$clientid = '';
	$businessid = '';
	$heading_auto = 1;
	if(!empty($_GET['supportid'])) {
		$supportid = $_GET['supportid'];
		$company_name = get_support($dbc, $supportid, 'company_name');
		$get_contact =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT contactid FROM	contacts WHERE	name='$company_name'"));
		$businessid = $get_contact['contactid'];
		$heading = get_support($dbc, $supportid, 'heading');
		$heading_auto = 1;
		$assign_work = get_support($dbc, $supportid, 'message');
		$status = 'Time Estimate Needed';
		echo '<input type="hidden" name="supportid" id="supportid" value="'.$supportid.'">';
	} else {
		echo '<input type="hidden" name="supportid" id="supportid" value="0">';
	}
	if(!empty($_GET['bid'])) {
		$businessid = $_GET['bid'];
	}
	if(!empty($_GET['clientid'])) {
		$clientid = $_GET['clientid'];
		$businessid = get_contact($dbc, $clientid, 'businessid');
	}
	if(!empty($_GET['projectid'])) {
		$projectid = $_GET['projectid'];
		$businessid = get_project($dbc, $projectid, 'businessid');
		$clientid = explode(',',trim(get_project($dbc, $projectid, 'clientid'),','))[0];
		$project_path = get_project($dbc, $projectid, 'project_path');
		$project_lead = get_project($dbc, $projectid, 'project_lead');
	}
	if(!empty($_GET['milestone_timeline'])) {
		$milestone_timeline = str_replace(['FFMSPACE','FFMEND','FFMHASH'], [' ','&','#'], urldecode($_GET['milestone_timeline']));
	}

	if(get_config($dbc, 'ticket_default_session_user') != 'no_user') {
		$contactid = $_SESSION['contactid'];
	}
	if(!empty($_GET['contactid'])) {
		$contactid = ','.$_GET['contactid'].',';
	}
	if(!empty($_GET['startdate'])) {
		$to_do_date = $_GET['startdate'];
	}
	if(!empty($_GET['enddate'])) {
		$to_do_end_date = $_GET['enddate'];
	}
	if(!empty($_GET['starttime'])) {
		$to_do_start_time = $_GET['starttime'];
	}
	if(!empty($_GET['endtime'])) {
		$to_do_end_time = $_GET['endtime'];
	}

	//New ticket from calendar
	if($_GET['new_ticket_calendar'] == 'true' && empty($_GET['edit']) && !($_GET['ticketid'] > 0)) {
		$get_ticket['to_do_date'] = $to_do_date = $_GET['current_date'];
		$get_ticket['to_do_end_date'] = $to_do_end_date = $_GET['current_date'];
		$get_ticket['to_do_start_time'] = $to_do_start_time = !empty($_GET['current_time']) ? date('h:i a', strtotime($_GET['current_time'])) : '';
		$get_ticket['to_do_end_time'] = $to_do_end_time = !empty($_GET['current_time']) ? date('h:i a', strtotime($_GET['current_time'])) : '';
		if(!empty($_GET['end_time'])) {
			$get_ticket['to_do_end_time'] = $to_do_end_time = $_GET['end_time'];
		}
		$equipmentid = $_GET['equipmentid'];
		$equipment_assignmentid = $_GET['equipment_assignmentid'];

		$equipment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '$equipmentid'"));
		$equip_assign = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'"));
		$teamid = $equip_assign['teamid'];
		$contactid = [];
		$team_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC);
		foreach ($team_staff as $staff) {
			$contactid[] = $staff['contactid'];
		}
		$equip_assign_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '$equipment_assignmentid' AND `deleted` = 0"),MYSQLI_ASSOC);
		foreach ($equip_assign_staff as $staff) {
			$contactid[] = $staff['contactid'];
		}
		if(!empty($_GET['calendar_contactid'])) {
			foreach(explode(',', $_GET['calendar_contactid']) as $calendar_contactid) {
				$contactid[] = $calendar_contactid;
			}
		}
		$contactid = array_filter(array_unique($contactid));
		$calendar_contactid = ','.implode(',', $contactid).',';
		$contactid = $calendar_contactid;
		$get_ticket['region'] = !empty($equip_assign['region']) ? $equip_assign['region'] : explode('*#*', $equipment['region'])[0];
		if(empty($get_ticket['region'])) {
			$get_ticket['region'] = $_GET['calendar_region'];
		}
		$get_ticket['con_location'] = !empty($equip_assign['location']) ? $equip_assign['location'] : explode('*#*', $equipment['location'])[0];
		if(empty($get_ticket['con_location'])) {
			$get_ticket['con_location'] = $_GET['calendar_location'];
		}
		$get_ticket['classification'] = !empty($equip_assign['classification']) ? $equip_assign['classification'] : explode('*#*', $equipment['classification'])[0];
		if(empty($get_ticket['classification'])) {
			$get_ticket['classification'] = $_GET['calendar_classification'];
		}
	}

	if(!empty($_GET['edit']) || $_GET['ticketid'] > 0) {
		$ticketid = filter_var($_GET['edit'],FILTER_SANITIZE_STRING);
		$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM tickets WHERE ticketid='$ticketid'"));
		foreach($get_ticket as $field_id => $value) {
			if($value == '0000-00-00' || $value == '0') {
				$get_ticket[$field_id] = '';
			}
		}

		$ticket_type = empty($get_ticket['ticket_type']) ? $ticket_type : $get_ticket['ticket_type'];
		$businessid = $get_ticket['businessid'];
		$equipmentid = $get_ticket['equipmentid'];

		$clientid = $get_ticket['clientid'];
		if($businessid == '') {
			$businessid = get_contact($dbc, $clientid, 'businessid');
		}

		$projectid = $get_ticket['projectid'];
		$client_projectid = $get_ticket['client_projectid'];
		$piece_work = $get_ticket['piece_work'];
		$service_type = $get_ticket['service_type'];
		$service = $get_ticket['service'];
		$sub_heading = $get_ticket['sub_heading'];
		$heading = $get_ticket['heading'];
		$heading_auto = $get_ticket['heading_auto'];
		$category = $get_ticket['category'];
		$assign_work = $get_ticket['assign_work'];
		$project_path = '';
		if(!empty($projectid)) {
			$project_path = get_project($dbc, $projectid, 'project_path');
		} else if(!empty($client_projectid)) {
			$project_path = get_client_project($dbc, $client_projectid, 'project_path');
		}
		//}

		$projecttype = get_project($dbc, $projectid, 'projecttype');
		$milestone_timeline = html_entity_decode($get_ticket['milestone_timeline']);

		$created_date = date('Y-m-d');
		$login_id = $_SESSION['contactid'];

		$get_ticket_timer = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT start_timer_time, timer_type FROM ticket_timer WHERE tickettimerid IN (SELECT MAX(`tickettimerid`) FROM `ticket_timer` WHERE `ticketid`='$ticketid' AND created_by='$login_id' AND `deleted` = 0)"));

		$created_date = $get_ticket['created_date'];
		$created_by = $get_ticket['created_by'];

		$start_time = $get_ticket_timer['start_timer_time'];
		$timer_type = $get_ticket_timer['timer_type'];

		if($start_time == '0' || $start_time == '') {
			$time_seconds = 0;
		} else {
			$time_seconds = (time()-$start_time);
		}

		$to_do_date = $get_ticket['to_do_date'];
		$internal_qa_date = $get_ticket['internal_qa_date'];
		$deliverable_date = $get_ticket['deliverable_date'];

		$to_do_end_date = $get_ticket['to_do_end_date'];
		$internal_qa_contactid = $get_ticket['internal_qa_contactid'];
		$deliverable_contactid = $get_ticket['deliverable_contactid'];

		$to_do_start_time = $get_ticket['to_do_start_time'] == '' ? '' : date('h:i a', strtotime($get_ticket['to_do_start_time']));
		$to_do_end_time = $get_ticket['to_do_end_time'] == '' ? '' : date('h:i a', strtotime($get_ticket['to_do_end_time']));
		$internal_qa_start_time = $get_ticket['internal_qa_start_time'] == '' ? '' : date('h:i a', strtotime($get_ticket['internal_qa_start_time']));
		$internal_qa_end_time = $get_ticket['internal_qa_end_time'] == '' ? '' : date('h:i a', strtotime($get_ticket['internal_qa_end_time']));
		$deliverable_start_time = $get_ticket['deliverable_start_time'] == '' ? '' : date('h:i a', strtotime($get_ticket['deliverable_start_time']));
		$deliverable_end_time = $get_ticket['deliverable_end_time'] == '' ? '' : date('h:i a', strtotime($get_ticket['deliverable_end_time']));

		$status = $get_ticket['status'];
		$max_time = explode(':', $get_ticket['max_time']);
		$max_qa_time = explode(':', $get_ticket['max_qa_time']);
		$spent_time = $get_ticket['spent_time'];
		$total_days = $get_ticket['total_days'];
		$contactid = $get_ticket['contactid']; ?>
		<script type="text/javascript" src="ticket.js"></script>
		<script>
		$(document).ready(function() {
			$('.start_time').val(<?= $time_seconds ?>);
			$('#login_contactid').val(<?= $_SESSION['contactid'] ?>);
			$('#timer_type').val('<?= $timer_type ?>');
		});
		</script>
	<?php }
	if(!empty(MATCH_CONTACTS) && !in_array($get_ticket['businessid'],explode(',',MATCH_CONTACTS)) && !in_array_any(array_filter(explode(',',$get_ticket['clientid'])),explode(',',MATCH_CONTACTS))) {
		ob_clean();
		header('Location: index.php'.(empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group']));
		exit();
	} ?>
	<script>
	if(typeof ticketid_list == 'undefined') {
		ticketid_list = [];
	}
	if(typeof force_caps == 'undefined') {
		force_caps = <?= strpos($value_config,',Force All Caps,') !== FALSE ? 'true' : 'false' ?>;
	}
	$(document).ready(function() {
		if(force_caps) {
			$('select').each(function() {
				$(this).find('option').each(function() {
					this.text = this.text.toUpperCase();
				});
			});
			initInputs();
			$('input[type=text]').each(function() {
				this.value = this.value.toUpperCase();
			});
		}
	});
	if(typeof tile_name == 'undefined') {
		tile_name = '<?= $_GET['tile_name'] ?>';
	}
	if(typeof updateLabel == 'undefined') {
		updateLabel = <?= (($_GET['edit'] > 0 || $_GET['ticketid'] > 0) && $_GET['new_ticket'] != 'true') || strpos($value_config, ',Hide New Ticketid,') === FALSE ? 'true' : 'false' ?>;
	}
	if(typeof setHeading == 'undefined') {
		setHeading = function() {
			if(ticketid > 0) {
                var new_heading = '';
			<?php if(strpos($value_config, ','."Heading Business Invoice".',') !== false) { ?>
				if($('[name=heading_auto]').val() == 1 && $('[name=businessid]').length > 0 && $('[name=salesorderid]').length > 0) {
					var business = $('[name=businessid] option:selected').first().text();
					var invoice = $('[name=salesorderid]').first().val();
                    new_heading = business+' - '+invoice;
				}
			<?php } else if(strpos($value_config, ','."Heading Bus Invoice Date".',') !== false) { ?>
				if($('[name=heading_auto]').val() == 1 && $('[name=businessid]').length > 0 && $('[name=salesorderid]').length > 0 && $('[name=to_do_date]').length > 0) {
					var business = $('[name=businessid] option:selected').first().text();
					var invoice = $('[name=salesorderid]').first().val();
					var date = $('[name=to_do_date]').first().val();
                    new_heading = invoice+' - '+business+' '+date;
				}
			<?php } else if(strpos($value_config, ','."Heading Project Invoice Date".',') !== false) { ?>
				if($('[name=heading_auto]').val() == 1 && $('[name=projectid]').length > 0 && $('[name=salesorderid]').length > 0 && $('[name=to_do_date]').length > 0) {
					var project = $('[name=projectid] option:selected').first().text();
					var invoice = $('[name=salesorderid]').first().val();
					var date = $('[name=to_do_date]').first().val();
                    new_heading = invoice+' - '+project+' '+date;
				}
			<?php } else if(strpos($value_config, ','."Heading Date".',') !== false) { ?>
				if($('[name=heading_auto]').val() == 1 && $('[name=to_do_date]').length > 0) {
					var date = $('[name=to_do_date]').first().val();
                    new_heading = date;
				}
			<?php } else if(strpos($value_config, ','."Heading Business Date".',') !== false) { ?>
				if($('[name=heading_auto]').val() == 1 && $('[name=businessid]').length > 0 && $('[name=to_do_date]').length > 0) {
					var business = $('[name=businessid] option:selected').first().text();
					var date = $('[name=to_do_date]').first().val();
                    new_heading = business+' - '+date;
				}
			<?php } else if(strpos($value_config, ','."Heading Contact Date".',') !== false) { ?>
				if($('[name=heading_auto]').val() == 1 && $('[name=clientid]').length > 0 && $('[name=to_do_date]').length > 0) {
					var contact = $('[name=clientid] option:selected').first().text();
					var date = $('[name=to_do_date]').first().val();
                    new_heading = contact+' - '+date;
				}
			<?php } else if(strpos($value_config, ','."Heading Business".',') !== false) { ?>
				if($('[name=heading_auto]').val() == 1 && $('[name=businessid]').length > 0) {
					var business = $('[name=businessid] option:selected').first().text();
                    new_heading = business;
				}
			<?php } else if(strpos($value_config, ','."Heading Contact".',') !== false) { ?>
				if($('[name=heading_auto]').val() == 1 && $('[name=clientid]').length > 0) {
					var contact = $('[name=clientid] option:selected').first().text();
                    new_heading = contact;
				}
			<?php } else if(strpos($value_config, ','."Heading Milestone Date".',') !== false) { ?>
				if($('[name=heading_auto]').val() == 1 && $('[name=milestone_timeline]').length > 0 && $('[name=to_do_date]').length > 0) {
					var milestone = $('[name=milestone_timeline] option:selected').text();
					var date = $('[name=to_do_date]').first().val();
                    new_heading = milestone+': '+invoice;
				}
			<?php } else if(strpos($value_config, ','."Heading Assigned".',') !== false) { ?>
				if($('[name=heading_auto]').val() == 1 && $('[name=contactid]').length > 0) {
					var assigned = $('[name=contactid] option:selected,[name=item_id][data-type=Staff] option:selected').first().text();
                    new_heading = assigned;
				}
			<?php } ?>
                if(new_heading != heading) {
                    heading = new_heading;
                    $('[name=heading]').val(heading);
                    if($('[name=heading]:visible').length > 0) {
                        $('[name=heading]:visible').first().change();
                    } else {
                        $('[name=heading]').first().change();
                    }
                }
			} else { setTimeout(setHeading, 250); }
		}
	}
	/*var ticket_wait = false;
	var user_email = '<?= decryptIt($_SESSION['email_address']) ?>';
	var user_id = '<?= $_SESSION['contactid'] ?>';
	var from_url = '<?= urlencode($back_url) ?>';
	var new_ticket_url = '<?= $_GET['new_ticket'] != 'true' && $_GET['edit'] > 0 ? '' : '&new_ticket=true' ?>';
	var ticket_name = '<?= TICKET_NOUN ?>';
	var folder_name = '<?= FOLDER_NAME ?>';
	var tile_name = '<?= $_GET['tile_name'] ?>';
	var staff_list = [];
	var task_list = [];
	var projectFilter = function() {}
	var clientFilter = function() {}
	var businessFilter = function() {}
	var setHeading = function() {}
	var setServiceFilters = function() {}
	var updateLabel = <?= ($_GET['edit'] > 0 && $_GET['new_ticket'] != 'true') || strpos($value_config, ',Hide New Ticketid,') === FALSE ? 'true' : 'false' ?>;*/
	</script>
	<script src="ticket.js"></script>
	<input type="hidden" id="ticketid" name="ticketid" value="<?php echo $ticketid ?>" />
	<?php if(get_config($dbc, 'ticket_textarea_style') == 'no_editor') { ?>
		<script>
		no_tools = true;
		</script>
	<?php } ?>
	<input name="heading" type="hidden" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?php echo $heading; ?>">
	<?php //Get Ticket Type Fields
	if(!empty($ticket_type)) {
		$value_config .= get_config($dbc, 'ticket_fields_'.$ticket_type).',';
		$ticket_tab_locks .= ','.get_config($dbc, 'ticket_tab_locks_'.$ticket_type);
		$client_accordion_category = get_config($dbc, 'client_accordion_category_'.$ticket_type) ?: $client_accordion_category;
	}
	$ticket_tab_locks = explode(',',$ticket_tab_locks);
	$unlocked_tabs = explode(',',$get_ticket['unlocked_tabs']);

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
			$value_config = ','.implode(',',array_intersect(explode(',',$value_config), explode(',',$value_config_all))).',';
		}
        foreach($estimate_mode_restrict as $remove_field) {
            $value_config = str_replace($remove_field.',','',$value_config);
        }
	}

	//Overview Fields
	if($_GET['overview_mode'] == 1) {
		$value_config_all = $value_config;
		$value_config = ','.get_config($dbc, 'ticket_overview_fields').',';
		if(!empty($ticket_type)) {
			$value_config .= get_config($dbc, 'ticket_overview_fields_'.$ticket_type).',';
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
		$force_readonly = true;
	}

	//Force read only if Ticket is deleted
	if($get_ticket['deleted'] == 1) {
		$force_readonly = true;
		$strict_view = 1;
	}

    //Status Fields
    if(!empty($ticket_status)) {
        $value_config_all = $value_config;
        $value_config = ','.get_config($dbc, 'ticket_status_fields_'.$ticket_status).',';
        if(!empty($ticket_type)) {
            $value_config .= get_config($dbc, 'ticket_status_fields_'.$ticket_status.'_'.$ticket_type).',';
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
        $ticket_layout = $calendar_ticket_slider = 'full';
    }

	//Apply Templates
	if(strpos($value_config,',TEMPLATE Work Ticket') !== FALSE) {
		$value_config = ',Information,PI Business,PI Name,PI Project,PI AFE,PI Sites,Staff,Staff Position,Staff Hours,Staff Overtime,Staff Travel,Staff Subsistence,Services,Service Category,Equipment,Materials,Material Quantity,Material Rates,Purchase Orders,Notes,';
	}
	// Add Required Fields
	if(strpos($value_config,',Documents,') !== FALSE && strpos($value_config,',Documents Docs,') === FALSE && strpos($value_config,',Documents Links,') === FALSE) {
		$value_config .= ',Documents Docs,Documents Links,';
	}

	//Check if only using today's data
	$query_daily = "";
	if(strpos($value_config,',Time Tracking Current,') !== FALSE) {
		$query_daily = " AND (`date_stamp`='".date('Y-m-d')."' OR IFNULL(`checked_out`,'') = '')";
	}
	
	// Get Approval Settings
	$wait_on_approval = false;
	$admin_group = $dbc->query("SELECT * FROM `field_config_project_admin` WHERE (CONCAT(',',`action_items`,',') LIKE '%,Tickets,%' OR CONCAT(',',`action_items`,',') LIKE '%,ticket_type_".$ticket_type.",%') AND ',".$get_ticket['businessid'].",".$get_ticket['clientid'].",' LIKE CONCAT('%,',IFNULL(NULLIF(`customer`,''),'%'),',%') AND ',".$get_ticket['contactid'].",".$get_ticket['internal_qa_contactid'].",".$get_ticket['deliverable_contactid'].",".$get_ticket['created_by'].",' LIKE CONCAT('%,',IFNULL(NULLIF(`staff`,''),'%'),',%') AND `region` IN ('".$get_ticket['region']."','')  AND `location` IN ('".$get_ticket['con_location']."','')  AND `classification` IN ('".$get_ticket['classification']."','') AND IFNULL(`status`,'') != '' AND `deleted`=0");
	if($admin_group->num_rows > 0) {
		$admin_group = $admin_group->fetch_assoc();
		if($get_ticket['status'] == $admin_group['status']) {
			$wait_on_approval = true;
		}
        $value_config_all = $value_config;
        if(!empty($admin_group['unlocked_fields']) && !$wait_on_approval && $get_ticket['status'] != 'Archive' && !$force_readonly) {
            $value_config = ','.$admin_group['unlocked_fields'].',';
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
	} else {
		$admin_group = [];
	}	

	//Get Security Permissions
	$ticket_roles = explode('#*#',get_config($dbc, 'ticket_roles'));
	$ticket_role = mysqli_query($dbc, "SELECT `position` FROM `ticket_attached` WHERE `src_table`='Staff' AND `position`!='' AND `item_id`='".$_SESSION['contactid']."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted` = 0 $query_daily");
	$access_any = (vuaed_visible_function($dbc, 'ticket') + vuaed_visible_function($dbc, 'ticket_type_'.$get_ticket['ticket_type'])) > 0;
	$access_view_project_info = check_subtab_persmission($dbc, 'ticket', ROLE, 'view_project_info');
	$access_view_project_details = check_subtab_persmission($dbc, 'ticket', ROLE, 'view_project_details');
	$access_view_staff = check_subtab_persmission($dbc, 'ticket', ROLE, 'view_staff');
	$access_view_summary = check_subtab_persmission($dbc, 'ticket', ROLE, 'view_summary');
	$access_view_complete = check_subtab_persmission($dbc, 'ticket', ROLE, 'view_complete');
	$access_view_notifications = check_subtab_persmission($dbc, 'ticket', ROLE, 'view_notifications');
	$config_access = config_visible_function($dbc, 'ticket');
	$uneditable_statuses = ','.get_config($dbc, 'ticket_uneditable_status').',';
	if(!empty($get_ticket['status']) && strpos($uneditable_statuses, ','.$get_ticket['status'].',') !== FALSE) {
		$strict_view = 1;
	}
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
    } else if(($get_ticket['to_do_date'] > date('Y-m-d') && strpos($value_config,',Ticket Edit Cutoff,') !== FALSE && $config_access < 1) || $strict_view > 0 || ($wait_on_approval && strpos(','.$admin_group.',',','.$_SESSION['contactid'].',') !== false)) {
		$access_project = false;
		$access_staff = false;
		$access_contacts = false;
		$access_waitlist = false;
		$access_staff_checkin = false;
		$access_all_checkin = false;
		$access_medication = false;
		$access_complete = false;
		$access_services = false;
		$access_all = false;
		$access_any = false;
	} else if($get_ticket['status'] == 'Archive' || $force_readonly) {
		$access_project = false;
		$access_staff = false;
		$access_contacts = false;
		$access_waitlist = false;
		$access_staff_checkin = false;
		$access_all_checkin = false;
		$access_medication = check_subtab_persmission($dbc, 'ticket', ROLE, 'medication');
		$access_complete = false;
		$access_services = false;
		$access_all = false;
		$access_any = false;
	} else if($config_access > 0) {
		$access_project = check_subtab_persmission($dbc, 'ticket', ROLE, 'project');
		$access_staff = check_subtab_persmission($dbc, 'ticket', ROLE, 'staff_list');
		$access_contacts = check_subtab_persmission($dbc, 'ticket', ROLE, 'contact_list');
		$access_waitlist = check_subtab_persmission($dbc, 'ticket', ROLE, 'wait_list');
		$access_staff_checkin = check_subtab_persmission($dbc, 'ticket', ROLE, 'staff_checkin');
		$access_all_checkin = check_subtab_persmission($dbc, 'ticket', ROLE, 'all_checkin');
		$access_medication = check_subtab_persmission($dbc, 'ticket', ROLE, 'medication');
		$access_complete = check_subtab_persmission($dbc, 'ticket', ROLE, 'complete');
		$access_services = check_subtab_persmission($dbc, 'ticket', ROLE, 'services');
		$access_all = check_subtab_persmission($dbc, 'ticket', ROLE, 'all_access');
	} else if((count($ticket_roles) > 1 || explode('|',$ticket_roles[0])[0] != '') && mysqli_num_rows($ticket_role) > 0) {
		$ticket_role = html_entity_decode(mysqli_fetch_assoc($ticket_role)['position']);
		foreach($ticket_roles as $ticket_role_level) {
			$ticket_role_level = explode('|',html_entity_decode($ticket_role_level));
			if($ticket_role_level[0] > 0) {
				$ticket_role_level[0] = get_positions($dbc, $ticket_role_level[0], 'name');
			}
			if($ticket_role_level[0] == $ticket_role) {
				$access_project = in_array('project',$ticket_role_level);
				$access_staff = in_array('staff_list',$ticket_role_level);
				$access_contacts = in_array('contact_list',$ticket_role_level);
				$access_waitlist = in_array('wait_list',$ticket_role_level);
				$access_staff_checkin = in_array('staff_checkin',$ticket_role_level);
				$access_all_checkin = in_array('all_checkin',$ticket_role_level);
				$access_medication = in_array('medication',$ticket_role_level);
				$access_complete = in_array('complete',$ticket_role_level);
				$access_services = in_array('services',$ticket_role_level);
				$access_all = in_array('all_access',$ticket_role_level);
			}
		}
	} else if(count(array_filter($arr, function ($var) { return (strpos($var, 'default') !== false); })) > 0) {
		foreach($ticket_roles as $ticket_role_level) {
			$ticket_role_level = explode('|',$ticket_role_level);
			if(in_array('default',$ticket_role_level)) {
				$access_project = in_array('project',$ticket_role_level);
				$access_staff = in_array('staff_list',$ticket_role_level);
				$access_contacts = in_array('contact_list',$ticket_role_level);
				$access_waitlist = in_array('wait_list',$ticket_role_level);
				$access_staff_checkin = in_array('staff_checkin',$ticket_role_level);
				$access_all_checkin = in_array('all_checkin',$ticket_role_level);
				$access_medication = in_array('medication',$ticket_role_level);
				$access_complete = in_array('complete',$ticket_role_level);
				$access_services = in_array('services',$ticket_role_level);
				$access_all = in_array('all_access',$ticket_role_level);
			}
		}
	} else {
		$access_project = check_subtab_persmission($dbc, 'ticket', ROLE, 'project');
		$access_staff = check_subtab_persmission($dbc, 'ticket', ROLE, 'staff_list');
		$access_contacts = check_subtab_persmission($dbc, 'ticket', ROLE, 'contact_list');
		$access_waitlist = check_subtab_persmission($dbc, 'ticket', ROLE, 'wait_list');
		$access_staff_checkin = check_subtab_persmission($dbc, 'ticket', ROLE, 'staff_checkin');
		$access_all_checkin = check_subtab_persmission($dbc, 'ticket', ROLE, 'all_checkin');
		$access_medication = check_subtab_persmission($dbc, 'ticket', ROLE, 'medication');
		$access_complete = check_subtab_persmission($dbc, 'ticket', ROLE, 'complete');
		$access_services = check_subtab_persmission($dbc, 'ticket', ROLE, 'services');
		$access_all = check_subtab_persmission($dbc, 'ticket', ROLE, 'all_access');
	}
	$global_value_config = $value_config;

	if(strpos($value_config,',Time Tracking Current,') !== FALSE) {
		$query_daily = " AND `date_stamp`='".date('Y-m-d')."' ";
	}
}

if($force_project == 'business_project' && strpos($value_config,' Business,') === FALSE) {
	$value_config = ',PI Business'.$value_config;
} else if($force_project == 'contact_project' && strpos($value_config,',PI Name,') === FALSE && strpos($value_config,',Detail Contact,') === FALSE) {
	$value_config = ',PI Name'.$value_config;
}
if(!empty($_GET['tile_name'])) {
	$get_ticket['ticket_type'] = $_GET['tile_name'];
}
if(!empty($_GET['bid'])) {
	$businessid = $_GET['bid'];
}
if(!empty($_GET['clientid'])) {
	$clientid = $_GET['clientid'];
	$businessid = get_contact($dbc, $clientid, 'businessid');
}
if(!empty($_GET['projectid'])) {
	$projectid = $_GET['projectid'];
	$businessid = get_project($dbc, $projectid, 'businessid');
	$clientid = explode(',',trim(get_project($dbc, $projectid, 'clientid'),','))[0];
	$project_path = get_project($dbc, $projectid, 'project_path');
	$project_lead = get_project($dbc, $projectid, 'project_lead');
}
if(!empty($_GET['milestone_timeline'])) {
	$milestone_timeline = str_replace(['FFMSPACE','FFMEND','FFMHASH'], [' ','&','#'], urldecode($_GET['milestone_timeline']));
}

if(!empty($_GET['contactid'])) {
	$contactid = ','.$_GET['contactid'].',';
}
if(!empty($_GET['startdate'])) {
	$to_do_date = $_GET['startdate'];
}
if(!empty($_GET['enddate'])) {
	$to_do_end_date = $_GET['enddate'];
}
if(!empty($_GET['starttime'])) {
	$to_do_start_time = $_GET['starttime'];
}
if(!empty($_GET['endtime'])) {
	$to_do_end_time = $_GET['endtime'];
}

$acc_label_id = $sort_field;
if($sort_field == 'Transport' && $_GET['tab'] == 'ticket_transport_destination') {
    $acc_label_id = 'Transport Destination';
}
if($sort_field == 'Transport' && $_GET['tab'] == 'ticket_transport_origin') {
    $acc_label_id = 'Transport Origin';
}
if($sort_field == 'Transport' && $_GET['tab'] == 'ticket_transport_details') {
    $acc_label_id = 'Transport Carrier';
}
$renamed_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($ticket_type) ? 'tickets' : 'tickets_'.$ticket_type)."' AND `accordion` = '".$acc_label_id."'"))['accordion_name'];
if(!empty($renamed_accordion)) {
	$acc_label = $renamed_accordion;
}
if($generate_pdf) {
	$pdf_contents[] = ['**HEADING**', $acc_label];
}
?>
<?php if($ticket_layout == 'Accordions') { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_<?= $collapse_i ?>">
					<?= $acc_label ?><span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_<?= $collapse_i ?>" class="panel-collapse collapse <?php echo $collapse_in; $collapse_in = '' ?>">
            <div class="panel-body">
<?php } ?>
<?php if(empty($_GET['tab_only'])) { ?>
	<div id="tab_section_<?=$_GET['tab'] ?>" class="tab-section col-sm-12">
<?php } ?>
	<div class="locked" style="<?= (in_array($_GET['tab'],$ticket_tab_locks) && !in_array($_GET['tab'],$unlocked_tabs) ? '' : 'display:none;') ?>">
		<em class="cursor-hand tab_lock_toggle">Click this section to unlock.<img class="inline-img" src="../img/icons/lock.png"></em>
		<input type="hidden" name="lock_tabs" value="<?= $_GET['tab'] ?>" data-toggle="<?= (in_array($_GET['tab'],$ticket_tab_locks) && !in_array($_GET['tab'],$unlocked_tabs) ? '0' : '1') ?>">
		<hr class="hide-titles-mob">
	</div>
	<div class="lockable" style="<?= (in_array($_GET['tab'],$ticket_tab_locks) && !in_array($_GET['tab'],$unlocked_tabs) ? 'display:none;' : '') ?>">
		<script>$(document).ready(function() {
			setSave();
			initSelectOnChanges();
			initLocks();
		});</script>
		<input type="hidden" name="no_time_sheet" value="<?= strpos($value_config, ',No Track Time Sheets,') !== FALSE ? 1 : 0 ?>">
		<?php if($_GET['tab'] == 'ticket_medications') {
			if(!isset($member_list)) {
				$member_list = sort_contacts_query(mysqli_query($dbc, "SELECT `first_name`, `last_name`, `contactid` FROM `contacts` WHERE `category`='Members' AND `deleted`=0 AND `status`>0 AND `contactid` IN (SELECT `item_id` FROM `ticket_attached` WHERE `deleted`=0 AND `src_table`='Members')"));
			}
		} else if($_GET['tab'] == 'ticket_staff_list') {
			$roles = explode('#*#',get_config($dbc,"ticket_roles"));
		} else if($_GET['tab'] == 'ticket_log_notes') {
			$comment_type = 'client_log';
		} else if($_GET['tab'] == 'addendum_view_ticket_comment') {
			$comment_type = 'addendum';
			echo (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : "<h3>Addendum Notes</h3>");
			$_GET['tab'] = 'view_ticket_comment';
		} else if($_GET['tab'] == 'notes_view_ticket_comment') {
			$comment_type = 'note';
			echo (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : "<h3>".TICKET_NOUN." Notes</h3>");
			$_GET['tab'] = 'view_ticket_comment';
		} else if($_GET['tab'] == 'debrief_view_ticket_comment') {
			$comment_type = 'debrief';
			echo (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : "<h3>Debrief Notes</h3>");
			$_GET['tab'] = 'view_ticket_comment';
		} else if($_GET['tab'] == 'view_multi_disciplinary_summary_report') {
			$comment_type = 'note';
			echo (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : "<h3>Multi Disciplinary Summary Notes</h3>");
			$_GET['tab'] = 'view_ticket_comment';
		} else if($_GET['tab'] == 'ticket_complete') {
			$comment_type = 'note';
		} else if($_GET['tab'] == 'member_view_ticket_comment') {
			$category = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `category` FROM `contacts` WHERE `category` NOT IN (".STAFF_CATS.",'Business','Sites') AND `deleted`=0 AND `status`>0 GROUP BY `category` ORDER BY COUNT(*) DESC"))['category'];
			$comment_type = 'member_note';
			echo (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : "<h3>$category Daily Log Notes</h3>");
			$_GET['tab'] = 'view_ticket_comment';
		} else if($_GET['tab'] == 'ticket_checkout_staff') {
			$_GET['tab'] = 'ticket_checkout';
		} else if($_GET['tab'] == 'internal_communication') {
			$communication_type = 'Internal';
			$communication_method = 'email';
			$_GET['tab'] = 'communication';
		} else if($_GET['tab'] == 'external_communication') {
			$communication_type = 'External';
			$communication_method = 'email';
			$_GET['tab'] = 'communication';
		}

		if(substr($sort_field, 0, strlen('FFMCUST_')) === 'FFMCUST_') {
			echo '<h3>'.$acc_label.'</h3>';
			$custom_accordion = true;
			$custom_field_sort_order = '';
			if($_GET['action_mode'] == 1) {
				$custom_field_sort_order = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_fields_action` WHERE `ticket_type` = '".(empty($ticket_type) ? 'tickets' : 'tickets_'.$ticket_type)."' AND `accordion` = '".$sort_field."'"))['fields'];
			}
			if(empty($custom_field_sort_order)) {
				$custom_field_sort_order = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_fields` WHERE `ticket_type` = '".(empty($ticket_type) ? 'tickets' : 'tickets_'.$ticket_type)."' AND `accordion` = '".$sort_field."'"))['fields'];
			}
			$value_config = ','.$custom_field_sort_order.',';
			$custom_field_sort_order = explode(',', $custom_field_sort_order);
			foreach ($custom_field_sort_order as $custom_field_sort_field) {
				$field_sort_order = [$custom_field_sort_field];
				include('add_project_info.php');
				include('add_project_details.php');
				include('add_ticket_cancellation.php');
				include('add_ticket_fees.php');
				include('add_ticket_path_milestone.php');
				include('add_ticket_reg_loc_class.php');
			}
		} else {
			if(strpos($value_config, ',Complete Combine Checkout Summary,') !== FALSE && $sort_field == 'Complete') {
				$sort_fields = ['Check Out'=>'ticket_checkout','Summary'=>'ticket_summary','Complete'=>'ticket_complete'];
			} else {
				$sort_fields = [$sort_field => $_GET['tab']];
			}
			foreach($sort_fields as $sort_field => $include_tab) {
				$value_config = $global_value_config;
				$custom_accordion = false;
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
				include('add_'.$include_tab.'.php');
			}
		} ?>
		<div class="clearfix"></div>
		<?php if($ticket_layout != 'Accordions') { ?>
			<hr class="hide-titles-mob">
		<?php } ?>
	</div>
<?php if(empty($_GET['tab_only'])) { ?>
	</div>
<?php } ?>
<?php if($ticket_layout == 'Accordions') { ?>
            </div>
        </div>
    </div>
<?php } ?>
