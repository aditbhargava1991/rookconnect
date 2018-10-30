<?php include_once('../include.php');
include_once('../Ticket/field_list.php');
include_once('../Ticket/config.php');
$back_url = tile_visible($dbc, 'Tickets') > 0 ? WEBSITE_URL.'/Ticket/tickets.php' : WEBSITE_URL.'/home.php';
if(!empty($_GET['calendar_view'])) {
	$reload_cache_date = date('Y-m-d');
	$iframe_parent = $_SERVER['HTTP_REFERER'];
	if(strpos($iframe_parent, 'Daysheet') !== FALSE || strpos($iframe_parent, 'Profile') !== FALSE) {
		$calendar_ticket_slider = get_config($dbc, 'daysheet_ticket_slider');
		if(in_array(get_user_settings()['daysheet_ticket_slider'], ['full','accordion'])) {
			$calendar_ticket_slider = get_user_settings()['daysheet_ticket_slider'];
		}
	} else if(strpos($iframe_parent, 'Calendar') !== FALSE) {
		$calendar_ticket_slider = get_config($dbc, 'calendar_ticket_slider');
		if(in_array(get_user_settings()['calendar_ticket_slider'], ['full','accordion'])) {
			$calendar_ticket_slider = get_user_settings()['calendar_ticket_slider'];
		}
	} else {
		$calendar_ticket_slider = get_config($dbc, 'ticket_slider_layout');
	}
	$calendar_ticket_slider = !empty($calendar_ticket_slider) ? $calendar_ticket_slider : 'accordion';
	$back_url = WEBSITE_URL."/blank_loading_page.php";
} else if(!empty($_GET['from'])) {
	echo '<input type="hidden" name="from" value="'.$_GET['from'].'">';
	$back_url = urldecode($_GET['from']);
}
if(!empty($_GET['from_alert'])) {
	$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$_GET['edit']."'"));
	if(trim($get_ticket['contactid'],',') == '') {
		mysqli_query($dbc, "UPDATE `tickets` SET `contactid` = ',".$_SESSION['contactid'].",' WHERE `ticketid` = '".$_GET['edit']."'");
	} else if(!in_array($_SESSION['contactid'], explode(',', trim($get_ticket['contactid'],',')))) {
		$staff_labels = [];
		foreach(explode(',', trim($get_ticket['contactid'],',')) as $ticket_contactid) {
			$staff_labels[] = get_contact($dbc, $ticket_contactid);
		}
		$staff_labels = implode(', ', $staff_labels); ?>
		<script type="text/javascript">
			$(document).ready(function() {
				alert("<?= $staff_labels ?> has taken responsibility of this <?= TICKET_NOUN ?>.");


			});
		</script>
	<?php }
}

$ticket_layout = get_config($dbc, 'ticket_layout');
$value_config = ','.get_field_config($dbc, 'tickets').',';
$sort_order = explode(',',get_config($dbc, 'ticket_sortorder'));
$ticket_tab_locks = get_config($dbc, 'ticket_tab_locks');
$client_accordion_category = get_config($dbc, 'client_accordion_category');
$attached_charts = explode(',',get_config($dbc, 'ticket_attached_charts'));
$auto_create_unscheduled = get_config($dbc, 'ticket_auto_create_unscheduled');
$force_project = get_config($dbc, 'ticket_project_function');
$hour_increment = get_config($dbc, "ticket_hour_increments");
if($hour_increment > 0 && $hour_increment <= 60) {
	$hour_increment = $hour_increment / 60;
} else {
	$hour_increment = 'any';
}
$calendar_window = $dbc->query("SELECT MIN(`value`) `window` FROM `general_configuration` WHERE `name` LIKE '%_increments' AND `value` > 0")->fetch_assoc()['window'];
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
	echo '<input type="hidden" name="supportid" id="supportid" value="'.$supportid.'">';
} else {
	echo '<input type="hidden" name="supportid" id="supportid" value="0">';
}
if($_GET['from_type'] == 'customer_rate_services') {
	echo '<input type="hidden" name="customer_rate_services" id="customer_rate_services" value="1">';
}
if(!empty($_GET['bid'])) {
	$businessid = $_GET['bid'];
} else if($_SESSION['category'] == BUSINESS_CAT) {
	$businessid = $_SESSION['contactid'];
}
if(!empty($_GET['clientid'])) {
	$clientid = $_GET['clientid'];
	$businessid = get_contact($dbc, $clientid, 'businessid');
} else if(!in_array($_SESSION['category'],[BUSINESS_CAT,'Staff'])) {
	$clientid = $_SESSION['contactid'];
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
if(!empty($_GET['edit'])) {
	$ticketid = filter_var($_GET['edit'],FILTER_SANITIZE_STRING);
	$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM tickets WHERE ticketid='$ticketid'"));
	foreach($get_ticket as $field_id => $value) {
		if($value == '0000-00-00' || $value == '0') {
			$get_ticket[$field_id] = '';
		}
	}

	$ticket_type = empty($get_ticket['ticket_type']) ? $ticket_type : $get_ticket['ticket_type'];
	$businessid = $get_ticket['businessid'] ?: $businessid;
	$equipmentid = $get_ticket['equipmentid'];

	$clientid = $get_ticket['clientid'] ?: $clientid;
	if($businessid == '') {
		$businessid = get_contact($dbc, $clientid, 'businessid');
	}

	$projectid = $get_ticket['projectid'];
	$client_projectid = $get_ticket['client_projectid'];
	$piece_work = $get_ticket['piece_work'];
	//$projecttype = get_project($dbc, $projectid, 'projecttype');
	$service_type = $get_ticket['service_type'];
	$service = $get_ticket['service'];
	$sub_heading = $get_ticket['sub_heading'];
	$heading = $get_ticket['heading'];
	$heading_auto = $get_ticket['heading_auto'];
	$category = $get_ticket['category'];
	$assign_work = $get_ticket['assign_work'];
    $details_where = $get_ticket['details_where'];
    $details_who = $get_ticket['details_who'];
    $details_why = $get_ticket['details_why'];
    $details_what = $get_ticket['details_what'];
    $details_position = $get_ticket['details_position'];
	$project_path = '';
	if(!empty($projectid)) {
		$project_path = get_project($dbc, $projectid, 'project_path');
	} else if(!empty($client_projectid)) {
		$project_path = get_client_project($dbc, $client_projectid, 'project_path');
	}

	$projecttype = get_project($dbc, $projectid, 'projecttype');
	$milestone_timeline = html_entity_decode($get_ticket['milestone_timeline']);

	$created_date = date('Y-m-d');
	$login_id = $_SESSION['contactid'];
	// AND timer_type='Break' AND end_time IS NULL

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
	<input type="hidden" class="start_time" value="<?php echo $time_seconds ?>">
	<input type="hidden" id="login_contactid" value="<?php echo $_SESSION['contactid'] ?>" />
	<input type="hidden" id="timer_type" value="<?php echo $timer_type ?>" />
<?php } else if(!empty($_GET['type'])) {
	$ticket_type = $_GET['type'];
}

$default_status = get_config($dbc, "ticket_default_status_".$ticket_type);
if(empty($default_status)) {
    $default_status = get_config($dbc, "ticket_default_status");
}
$status = empty($status) ? (empty($default_status) ? 'Time Estimate Needed' : $default_status) : $status;
if(!empty($status) && $status != 'ffm_est_ticket' && $ticketid > 0 && !check_subtab_persmission($dbc, 'ticket', ROLE, 'ticket_status'.config_safe_str($status))) {
    $force_readonly = true;
}
if(empty($ticket_status)) {
    $ticket_status = config_safe_str($status);
}
if(!empty(MATCH_CONTACTS) && !in_array($get_ticket['businessid'],explode(',',MATCH_CONTACTS)) && !in_array_any(array_filter(explode(',',$get_ticket['clientid'])),explode(',',MATCH_CONTACTS)) && $ticketid > 0) {
	ob_clean();
	header('Location: index.php'.(empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group']));
	exit();
} ?>
<input type="hidden" id="ticketid" name="ticketid" value="<?php echo $ticketid ?>" />
<input name="unlocked_tabs" type="hidden" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $get_ticket['unlocked_tabs'] ?>">
<input type="hidden" name="action_mode" id="action_mode" value="<?= $_GET['action_mode'] ?>">
<input type="hidden" name="estimate_mode" id="estimate_mode" value="<?= $_GET['estimate_mode'] ?>">
<input type="hidden" name="overview_mode" id="overview_mode" value="<?= $_GET['overview_mode'] ?>">
<input name="heading" type="hidden" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?php echo $heading; ?>">
<?php if(empty($ticketid) && !empty($_GET['projectid'])) { ?>
	<input type="hidden" name="projectid" id="projectid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $projectid ?>">
	<input type="hidden" name="businessid" id="businessid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $businessid ?>">
	<input type="hidden" name="clientid" id="clientid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $clientid ?>">
<?php } ?>
<?php if(!empty($_GET['milestone_timeline'])) { ?>
	<input type="hidden" name="milestone_timeline" id="milestone_timeline" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $milestone_timeline ?>">
<?php } ?>
<?php //Get Ticket Type Fields
if(!empty($ticket_type)) {
	$value_config .= get_config($dbc, 'ticket_fields_'.$ticket_type).',';
	$sort_order = explode(',',get_config($dbc, 'ticket_sortorder_'.$ticket_type));
	$ticket_tab_locks .= ','.get_config($dbc, 'ticket_tab_locks_'.$ticket_type);
	$client_accordion_category = get_config($dbc, 'client_accordion_category_'.$ticket_type) ?: $client_accordion_category;
	$attached_charts = explode(',',get_config($dbc, 'ticket_attached_charts_'.$ticket_type));
	$auto_create_unscheduled = get_config($dbc, 'ticket_auto_create_unscheduled_'.$tab);
	if(empty(get_config($dbc, 'ticket_auto_create_unscheduled_'.$tab))) {
		$auto_create_unscheduled = get_config($dbc, 'ticket_auto_create_unscheduled');
	}
}

//Action Mode Fields
$hide_trash_icon = 0;
if($_GET['action_mode'] == 1) {
	$value_config_all = $value_config;
	$value_config = ','.get_config($dbc, 'ticket_action_fields').',';
	if(!empty($ticket_type)) {
		$value_config .= get_config($dbc, 'ticket_action_fields_'.$ticket_type).',';
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

//Estimate Mode Fields
$hide_trash_icon = 0;
if($_GET['estimate_mode'] == 1) {
	$value_config_all = $value_config;
	$value_config = ','.get_config($dbc, 'ticket_estimate_fields').',';
	if(!empty($ticket_type)) {
		$value_config .= get_config($dbc, 'ticket_estimate_fields_'.$ticket_type).',';
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
    foreach($estimate_mode_restrict as $remove_field) {
        $value_config = str_replace($remove_field.',','',$value_config);
    }
    $ticket_tabs = [$_GET['ticket_type']];
	$ticket_layout = 'full';
	$calendar_ticket_slider = 'full';
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
	$ticket_layout = 'full';
	$calendar_ticket_slider = 'full';
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
		$value_config = ','.implode(',',array_intersect(explode(',',$value_config), explode(',',$value_config_all))).','; ?>
        <script>
        var status_reload = false;
        var status_value = '<?= $status ?>';
        completed_last = function() {
            if(status_reload == true) {
                window.location.reload();
            }
        }
        </script>
    <?php }
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
	echo '<input type="hidden" name="intake_key" value="'.$_GET['intake_key'].'">';
	echo '<input type="hidden" name="intake_alerted" value="1">';
}

//Edit Staff From Dashboard
if($_GET['edit_staff_dashboard'] == 1) {
	$value_config = explode(',',$value_config);
	foreach($value_config as $field_key => $field_value) {
		if(!in_array($field_value, $accordion_list['Staff']) && !in_array($field_value, $action_mode_ignore_fields) && $field_value != 'Staff') {
			unset($value_config[$field_key]);
		}
	}
	$value_config = ','.implode(',',array_filter($value_config)).',';
}

if($force_project == 'business_project' && strpos($value_config,' Business,') === FALSE) {
	$value_config = ',PI Business'.$value_config;
} else if($force_project == 'contact_project' && strpos($value_config,',PI Name,') === FALSE && strpos($value_config,',Detail Contact,') === FALSE) {
	$value_config = ',PI Name'.$value_config;
}
$ticket_tab_locks = explode(',',$ticket_tab_locks);
$unlocked_tabs = explode(',',$get_ticket['unlocked_tabs']);
echo '<input type="hidden" name="auto_create_unscheduled" value=",'.$auto_create_unscheduled.',">';

//Accordion Sort Order
foreach ($accordion_list as $accordion_field => $accordion_field_fields) {
	if(!in_array($accordion_field, $sort_order)) {
		$sort_order[] = $accordion_field;
	}
}

//Apply Templates
if(strpos($value_config,',TEMPLATE Work Ticket') !== FALSE) {
	$value_config = ',Information,PI Business,PI Name,PI Project,PI AFE,PI Sites,Staff,Staff Position,Staff Hours,Staff Overtime,Staff Travel,Staff Subsistence,Services,Service Category,Equipment,Materials,Material Quantity,Material Rates,Purchase Orders,Notes,';
}
// Add Required Fields
if(strpos($value_config,',Documents,') !== FALSE && strpos($value_config,',Documents Docs,') === FALSE && strpos($value_config,',Documents Links,') === FALSE) {
	$value_config .= ',Documents Docs,Documents Links,';
}

//New ticket from calendar
if($_GET['new_ticket_calendar'] == 'true' && empty($_GET['edit'])) {
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
	echo '<input type="hidden" id="new_ticket_from_calendar" value="1">';
	if(strpos($value_config,',Deliverable To Do,') === FALSE) {
		echo '<input type="hidden" name="contactid[]" value="'.$calendar_contactid.'">';
	}
	if(strpos($value_config,',Deliverable To Do,') === FALSE && strpos($value_config,',Detail Times,') === FALSE) {
		echo '<input type="hidden" name="to_do_start_time" value="'.$get_ticket['to_do_start_time'].'">';
		echo '<input type="hidden" name="to_do_end_time" value="'.$get_ticket['to_do_end_time'].'">';
	}
}

//Check if only using today's data
$query_daily = "";
if(strpos($value_config,',Time Tracking Current,') !== FALSE) {
	$query_daily = " AND (`date_stamp`='".date('Y-m-d')."' OR IFNULL(`checked_out`,'') = '')";
}
if(isset($_GET['min_view'])) {
	$value_config = $min_view;
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
if((isset($_GET['intake_key']) && !($ticketid > 0)) || ($_GET['estimate_mode'] > 0 && !$force_readonly)) {
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
} else if(strpos($value_config, ',Edit Section Options,') !== FALSE) {
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
} else {echo '<!--'.$value_config.'-->';
	$access_project = $access_any == 0 ? false : check_subtab_persmission($dbc, 'ticket', ROLE, 'project');
	$access_staff = $access_any == 0 ? false : check_subtab_persmission($dbc, 'ticket', ROLE, 'staff_list');
	$access_contacts = $access_any == 0 ? false : check_subtab_persmission($dbc, 'ticket', ROLE, 'contact_list');
	$access_waitlist = $access_any == 0 ? false : check_subtab_persmission($dbc, 'ticket', ROLE, 'wait_list');
	$access_staff_checkin = $access_any == 0 ? false : check_subtab_persmission($dbc, 'ticket', ROLE, 'staff_checkin');
	$access_all_checkin = $access_any == 0 ? false : check_subtab_persmission($dbc, 'ticket', ROLE, 'all_checkin');
	$access_medication = $access_any == 0 ? false : check_subtab_persmission($dbc, 'ticket', ROLE, 'medication');
	$access_complete = $access_any == 0 ? false : check_subtab_persmission($dbc, 'ticket', ROLE, 'complete');
	$access_services = $access_any == 0 ? false : check_subtab_persmission($dbc, 'ticket', ROLE, 'services');
	$access_all = $access_any == 0 ? false : check_subtab_persmission($dbc, 'ticket', ROLE, 'all_access');
}
if(strpos($value_config,',Time Tracking Current,') !== FALSE) {
	$query_daily = " AND `date_stamp`='".date('Y-m-d')."' ";
}


$security_levels = explode(',',trim(ROLE,','));
$subtabs_hidden = [];
$subtabs_viewonly = [];
$fields_hidden = [];
$fields_viewonly = [];
$i = 0;
foreach($security_levels as $security_level) {
	if(tile_visible($dbc, $security_folder, $security_level)) {
		$security_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_security` WHERE `security_level`='$security_level'"));
		if(!empty($security_config)) {
			if($i == 0) {
				$subtabs_hidden = explode(',',$security_config['subtabs_hidden']);
				$subtabs_viewonly = explode(',',$security_config['subtabs_viewonly']);
				$fields_hidden = explode(',',$security_config['fields_hidden']);
				$fields_viewonly = explode(',',$security_config['fields_viewonly']);
			} else {
				$subtabs_hidden = array_intersect(explode(',',$security_config['subtabs_hidden']), $subtabs_hidden);
				$subtabs_viewonly = array_intersect(explode(',',$security_config['subtabs_viewonly']), $subtabs_viewonly);
				$fields_hidden = array_intersect(explode(',',$security_config['fields_hidden']), $fields_hidden);
				$fields_viewonly = array_intersect(explode(',',$security_config['fields_viewonly']), $fields_viewonly);
			}
			$i++;
		}
	}
}
$is_recurrence = false;
if($get_ticket['main_ticketid'] > 0 && $get_ticket['is_recurrence'] == 1 && !$force_readonly) {
	$is_recurrence = true;
}
$quick_action_html = '<div class="clearfix"></div>';
if(!($strict_view > 0) && !isset($_GET['intake_key'])) {
	$quick_actions = explode(',',get_config($dbc, 'quick_action_icons'));
	$quick_action_html .= '<div class="action-icons" data-colour="'.$get_ticket['flag_colour'].'" data-table="tickets" data-id-field="ticketid">';
    if($_GET['calendar_view'] == 'true') {
        $quick_action_html .= '<a href="" onclick="openFullView(); return false;"><img src="../img/icons/ROOK-FullScreen-icon.png" alt="Open Full Window" title="Open Full Window" class="no-toggle inline-img" width="25" /></a>';
    }
	$quick_action_html .= (strpos($value_config,',Create Recurrence Button,') !== FALSE ? '<img src="'.WEBSITE_URL.'/img/month-overview-blue.png" class="inline-img no-toggle" title="Recurring '.TICKET_TILE .'" onclick="dialogCreateRecurrence(this);">' : '');
	$quick_action_html .= (in_array('flag_manual',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" class="inline-img manual-flag-icon no-toggle" title="Flag This!">' : '');
	$quick_action_html .= (!in_array('flag_manual',$quick_actions) && in_array('flag',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" class="inline-img flag-icon no-toggle" title="Flag This!">' : '');
	$quick_action_html .= (in_array('email',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" class="inline-img email-icon no-toggle" title="Send Email">' : '');
	$quick_action_html .= (in_array('reminder',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" class="inline-img reminder-icon no-toggle" title="Schedule Reminder">' : '');
	$quick_action_html .= (in_array('attach',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" class="inline-img attach-icon no-toggle" title="Attach File">' : '');
	$quick_action_html .= (in_array('reply',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" class="inline-img reply-icon no-toggle" title="Add Note">' : '');
	$quick_action_html .= (in_array('history',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/eyeball.png" class="inline-img history-icon no-toggle" title="History" onclick="displayCustomerHistory();">' : '');
	$quick_action_html .= (in_array('archive',$quick_actions) && $tile_security['edit'] > 0 ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" class="inline-img archive-icon no-toggle" title="Archive">' : '');
	$quick_action_html .= '</div><br />
		<input type="text" name="emailpdf" value="" class="form-control" style="display:none;">
		<input type="file" name="document" value="" data-table="ticket_document" data-folder="ticket_document" style="display:none;">';
} else if($_GET['calendar_view'] == 'true') {
    $quick_action_html .= '<a href="" onclick="openFullView(); return false;" class="pull-right"><img src="../img/icons/ROOK-FullScreen-icon.png" alt="Open Full Window" title="Open Full Window" class="no-toggle inline-img" width="25" /></a>';
}

$global_value_config = $value_config; ?>

<script src="ticket.js"></script>
<script>
var no_verify = <?= IFRAME_PAGE ? 'true' : 'false' ?>;
$(document).ready(function() {
	setActions();
	window.onbeforeunload = function() {
        send_creator_email();
		return checkMandatoryFields();
	}
	$('#mobile_tabs .panel-heading').off('click',loadPanel).click(loadPanel);
	<?php if($ticket_layout != 'Accordions' || $include_hidden == 'true') { ?>
		if($('#calendar_view').val() != 'true') {
			$('.add_gap_here').last().css('min-height',$('.tab-section').closest('.standard-body').height() - $('.tab-section').closest('.standard-body').find('.add_gap_here').last().innerHeight() - $('.tab-section').last().height() + 30);
		}
	<?php } ?>
	$('[data-tab-target]').click(function() {
		$('.main-screen .main-screen').scrollTop($('#tab_section_'+$(this).data('tab-target')).offset().top + $('.main-screen .main-screen').scrollTop() - $('.main-screen .main-screen').offset().top);
		return false;
	});
	setTimeout(function() {
		$('.main-screen .main-screen').scroll(function() {
			var screenTop = $('.main-screen .main-screen').offset().top + 10;
			var screenHeight = $('.main-screen .main-screen').innerHeight();
			$('.active.blue').removeClass('active blue');
			$('.tab-section').filter(function() { return $(this).offset().top + this.clientHeight > screenTop && $(this).offset().top < screenTop + screenHeight; }).each(function() {
				$('[data-tab-target='+$(this).attr('id').replace('tab_section_','')+']').find('li').addClass('active blue');
			});
			$('.sidebar_heading ul').each(function() {
				if($(this).find('li.active').length > 0) {
					$(this).closest('.sidebar_heading').find('a.cursor-hand').addClass('active blue');
					if($(this).closest('.sidebar_heading').find('.sidebar_heading_collapse').hasClass('collapsed')) {
						$(this).closest('.sidebar_heading').find('.sidebar_heading_collapse').click();
					}
				} else {
					$(this).closest('.sidebar_heading').find('a.cursor-hand').removeClass('active blue');
					if(!$(this).closest('.sidebar_heading').find('.sidebar_heading_collapse').hasClass('collapsed')) {
						$(this).closest('.sidebar_heading').find('.sidebar_heading_collapse').click();
					}
				}
			});
			<?php if($ticketid > 0) { ?>
				// getTabLocks();
			<?php } ?>
		});
		$('.main-screen .main-screen').scroll();
	}, 500);
	if(force_caps) {
		$('select').each(function() {
			$(this).find('option').each(function() {
				this.text = this.text.toUpperCase();
			});
		});
		initInputs();
	}
	<?php if(strpos_any(['Inventory Basic Billing','Staff Billing','Miscellaneous Billing'],$value_config)) { ?>
		reload_billing();
	<?php } ?>
	<?php if($is_recurrence && !($_GET['action_mode'] > 0) && !($_GET['estimate_mode'] > 0) && !($_GET['overview_mode'] > 0)) { ?>
		$('#dialog_edit_recurrence').dialog({
			resizable: true,
			height: "auto",
			width: ($(window).width() <= 800 ? $(window).width() : 800),
			modal: true,
			buttons: {
				"All Recurrences": function() {
					clearInterval(check_recurrence);
					var ticketid = $('#ticketid').val();
					$('#sync_recurrences').val(1);
					$('.sync_recurrences_note').show();
					$.ajax({
						url: '../Ticket/ticket_ajax_all.php?action=set_ticket_recurring&ticketid='+ticketid,
						method: 'GET',
						success: function(response) {

						}
					});
					$(this).dialog('close');
				},
				"One Occurence": function() {
					clearInterval(check_recurrence);
					$('#sync_recurrences').val(0);
					$(this).dialog('close');
				}
			}
		});
		var check_recurrence = '';
		check_recurrence = setInterval(function() {
			if(!($('#dialog_edit_recurrence').is(':visible'))) {
				$('#sync_recurrences').val(0);
				clearInterval(check_recurrence);
			}
		},50);
	<?php } ?>

    var menu_bar_height = $('#nav').height() + $('.tile-header').height() + 12;
    $('.menu-bar').css('top', menu_bar_height);

    $('.main-screen .main-screen').scroll(function() {
        if ($(this).scrollTop() > 70) {
            $('.menu-bar').fadeIn("fast");
            $('.menu-content:visible').fadeOut("fast");
            $('.menu_button:hidden').fadeIn("fast");
            $('#back_to_top').fadeIn("fast");
        } else {
            $('.menu-bar').fadeOut("fast");
            $('.menu-content').fadeOut("fast");
            $('#back_to_top').fadeOut("fast");
        }
    });
	if(ticketid > 0) {
		checkStaffShifts().success(function(response) {
			var response = JSON.parse(response);
			if(response.success == false) {
				alert(response.message);
			}
		});
	}
});
$(document).on('click', '.menu_button', function() {
    $('.menu-content').load('edit_tickets.php .standard-body-title', function() {
        setActions();
        $(this).find('.action-icons img').addClass('theme-color-icon');
        $(this).find('.action-icons img.archive-icon').removeClass('theme-color-icon');
        $('.menu-bar').innerWidth($('#main_screen_block').innerWidth() - 20);
        $('.menu-content .standard-body-title').css('cssText', 'margin-top: -9px !important; margin-right: -4px; padding-top: 1px !important;');
    });
    $('.menu-content').fadeIn("fast");
    $('.menu_button').hide();
});
$(document).on('click', '#back_to_top', function(e) {
   e.preventDefault();
   $('.standard-body').animate({scrollTop: 0}, 400);
});
function send_creator_email() {
    <?php if(!($ticketid > 0) && strpos($value_config, ','."Email Creator".',') !== false && !empty($_SESSION['email_address'])) {
        $ticket_new_email_subject = get_config($dbc, 'ticket_new_email_subject'.($ticket_type == '' ? '' : '_'.$ticket_type));
        if(empty($ticket_new_email_subject) && !empty($ticket_type)) {
            $ticket_new_email_subject = get_config($dbc, 'ticket_new_email_subject');
        }
        $ticket_new_email_body = get_config($dbc, 'ticket_new_email_body'.($ticket_type == '' ? '' : '_'.$ticket_type));
        if(empty($ticket_new_email_body) && !empty($ticket_type)) {
            $ticket_new_email_body = get_config($dbc, 'ticket_new_email_body');
        } ?>
        $.post('../ajax_all.php?fill=send_email', {
            send_every_email: 'true',
            send_to: '<?= decryptIt($_SESSION['email_address']) ?>',
            subject: '<?= $ticket_new_email_subject ?>'.replace('[TICKET]',$('.ticketid_span').text()),
            body: '<?= htmlentities($ticket_new_email_body.'<br /><a href="'.WEBSITE_URL.'/Ticket/index.php?edit=TICKETID'.(empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group']).'">Click Here</a> to view the '.TICKET_NOUN) ?>'.replace('TICKETID',$('[name=ticketid]').val()).replace('[TICKET]',$('.ticketid_span').text())
        });
    <?php } ?>
}
function loadPanel() {
	if(!$(this).hasClass('higher_level_heading')) {
		$(this).off('click',loadPanel);
		var panel = $(this).closest('.panel').find('.panel-body');
		var accordion = $(panel).data('accordion');
		panel.html('Loading...');
		$.ajax({
			url: panel.data('file-name')+(tile_group == '' ? '' : '&tile_group='+tile_group)+(tile_name == '' ? '' : '&tile_name='+tile_name)+'<?= $ticketid > 0 ? '' : '&'.http_build_query($_GET) ?>&action_mode=<?= $_GET['action_mode'] ?>&estimate_mode=<?= $_GET['estimate_mode'] ?>&date=<?= $_GET['date'] ?>&overview_mode=<?= $_GET['overview_mode'] ?>&ticketid='+ticketid,
			method: 'POST',
			data: { accordion: accordion },
			response: 'html',
			success: function(response) {
				panel.html(response);
				setSave();
			}
		});
	}
}
function setActions() {
	$('.archive-icon').off('click').click(function() {
		var ticketid = $('#ticketid').val();
		$.ajax({
			url: 'ticket_ajax_all.php?action=archive',
			method: 'POST',
			data: { ticketid: ticketid },
			success: function(response) {
				window.location.href = '<?= $back_url ?>';
			}
		});
	});
	$('.manual-flag-icon').off('click').click(function() {
		var ticketid = $('#ticketid').val();
		overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_flags.php?tile=tickets&id='+ticketid, 'auto', false, true);
	});
	$('.flag-icon').off('click').click(function() {
		var ticketid = $('#ticketid').val();
		var item = $(this).closest('.action-icons');
		$.ajax({
			url: 'ticket_ajax_all.php?action=quick_actions',
			method: 'POST',
			data: {
				field: 'flag_colour',
				value: item.data('colour'),
				table: item.data('table'),
				id: ticketid,
				id_field: item.data('id-field')
			},
			success: function(response) {
				item.data('colour',response.substr(0,6));
				// item.css('background-color','#'+response.substr(0,6));
				// item.find('.flag-label').html(response.substr(6));
				setManualFlag('', response.substr(0,6), response.substr(6));
			}
		});
	});
	$('.attach-icon').off('click').click(function() {
		var ticketid = $('#ticketid').val();
		var item = $(this).closest('.action-icons');
		item.find('[type=file]').off('change').change(function() {
			var fileData = new FormData();
			fileData.append('file',$(this)[0].files[0]);
			fileData.append('field','document');
			fileData.append('table','ticket_document');
			fileData.append('folder','download');
			fileData.append('id',ticketid);
			fileData.append('id_field','ticketid');
			$.ajax({
				contentType: false,
				processData: false,
				method: "POST",
				url: "ticket_ajax_all.php?action=quick_actions",
				data: fileData
			});
		}).click();
	});
	$('.reply-icon').off('click').click(function() {
		var ticketid = $('#ticketid').val();
		overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_notes.php?tile=tickets&id='+ticketid, 'auto', false, true);
	});
	$('.reminder-icon').off('click').click(function() {
		var ticketid = $('#ticketid').val();
		overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_reminders.php?tile=tickets&id='+ticketid, 'auto', false, true);
	});
	$('.email-icon').off('click').click(function() {
		var ticketid = $('#ticketid').val();
		overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=tickets&id='+ticketid, 'auto', false, true);
	});
}
function setManualFlag(ticketid = '', colour, label) {
	if(colour == 'FFFFFF') {
		colour = '';
	}
	$('.flag-label').text(label);

	if(colour != '') {
		$('.standard-body-title').css('background-color', '#'+colour);
		$('.flag-label-block').show();
		$('.flag-label-block').css('background-color', '#'+colour);
		if(label != '') {
			$('.flag-label-block').text('Flagged: '+label);
		} else {
			$('.flag-label-block').text('Flagged');
		}
	} else {
		$('.standard-body-title').css('background-color', '');
		$('.flag-label-block').hide();
	}
}
function checkMandatoryFields() {
	var ready = ticketid > 0 || ticketid == 'multi' || <?= IFRAME_PAGE ? 'true' : 'false' ?>;
	$('.required').each(function() {
		if(!no_verify && this.value == '' && $(this).attr('type') != 'hidden' && (this.name != 'projectid' || $('[name=piece_work]').filter(function() { return this.value != ''; }).length != 1)) {
			var target = this;
			if($(target).is('select')) {
				var select2 = $(target).next('.select2');
				$(select2).find('.select2-selection').css('background-color', 'red');
				$(select2).find('.select2-selection__placeholder').css('color', 'white');
			} else {
				$(target).css('background-color', 'red');
			}
			if(ready) {
				<?php $incomplete_status = get_config($dbc, 'incomplete_ticket_status_'.$ticket_type);
				if($incomplete_status == '') {
					$incomplete_status = get_config($dbc, 'incomplete_ticket_status');
				}
				if($incomplete_status != '') { ?>
					$('[name=status]').val('<?= $incomplete_status ?>').change();
				<?php } ?>
				setTimeout(function() {
					alert("Please fill in the "+$(target).closest('.form-group').find('label').text().split("\n")[0].replace(/^[^a-zA-Z0-9()]*/g,'').replace(/[^a-zA-Z0-9()]*$/g,'')+".");
					$('.main-screen .default_screen').scrollTop($('.main-screen .default_screen').scrollTop() + $(target).offset().top - $('.main-screen .default_screen').offset().top - 30);
					$(target).focus();
				}, 0);
				ready = false;
			}
		}
	});
	if(ready && ($('[name=arrived][value=1]').length != $('[name=completed][value=1]').not('.no_time').length && $('[name=completed][value=0]').not('.no_time').length > 0) || $.inArray($('[name=timer]').val(),['',undefined]) < 0) {
		setTimeout(function() {
			alert("This <?= TICKET_NOUN ?> is currently actively tracking time.");
		}, 0);
		ready = false;
	}
	if(!ready) {
		return false;
	}
}
function fillCustomForm(a) {
	var target = $(a).data('target');
	var href = $(a).prop('href');
	if(checkMandatoryFields() != false) {
		if(target == 'slider') {
			overlayIFrameSlider(href, 'auto', true, true);
		} else {
			window.open(href, '_blank');
		}
	}
}
var ticketid = 0;
var stopid = '<?= $_GET['stop'] ?>';
var ticketid_list = [];
var ticket_wait = false;
var user_email = '<?= decryptIt($_SESSION['email_address']) ?>';
var user_id = '<?= $_SESSION['contactid'] ?>';
var from_url = '<?= urlencode($back_url) ?>';
var new_ticket_url = '<?= $_GET['new_ticket'] != 'true' && $_GET['edit'] > 0 ? '' : '&new_ticket=true' ?>';
var ticket_name = '<?= TICKET_NOUN ?>';
var folder_name = '<?= FOLDER_NAME ?>';
var tile_name = '<?= $_GET['tile_name'] ?>';
var set_business_delivery = <?= strpos($value_config,',Business Set Delivery,') !== FALSE ? 'true' : 'false' ?>;
var force_caps = <?= strpos($value_config,',Force All Caps,') !== FALSE ? 'true' : 'false' ?>;
var staff_list = [];
var task_list = [];
var projectFilter = function() {}
var clientFilter = function() {}
var businessFilter = function() {}
var setServiceFilters = function() {}
var updateLabel = <?= ($_GET['edit'] > 0 && $_GET['new_ticket'] != 'true') || strpos($value_config, ',Hide New Ticketid,') === FALSE ? 'true' : 'false' ?>;
var defaultStatus = '<?= $default_status ?>';
var heading = '<?= $get_ticket['heading'] ?>';

var setHeading = function() {
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
</script>
<?php if($include_hidden != 'true') { ?>
	<div id="dialog_quick_reminder" title="Add Reminder" style="display: none;">
		<div class="form-group">
			<label class="col-sm-4 control-label">Staff:</label>
			<div class="col-sm-8">
				<select name="quick_reminder_staff[]" multiple data-placeholder="Select a Staff" class="chosen-select-deselect">
					<?php $quick_reminder_staffs = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 AND `status` = 1 AND `show_hide_user` = 1"),MYSQLI_ASSOC));
					foreach ($quick_reminder_staffs as $quick_reminder_staff) {
						echo '<option value="'.$quick_reminder_staff.'" '.($quick_reminder_staff == $_SESSION['contactid'] ? 'selected' : '').'>'.get_contact($dbc, $quick_reminder_staff).'</option>';
					} ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Reminder:</label>
			<div class="col-sm-8">
				<input type="text" name="quick_reminder_text" value="" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Reminder Date:</label>
			<div class="col-sm-8">
				<input type="text" name="quick_reminder_date" value="<?= date('Y-m-d') ?>" class="datepicker form-control">
			</div>
		</div>
	</div>
	<div id="dialog_delete_note" title="Please enter a deletion Note" style="display: none;">
		<div class="form-group">
			<label class="col-sm-4 control-label">Note:</label>
			<div class="col-sm-8">
				<textarea type="text" name="delete_note" class="form-control"></textarea>
			</div>
		</div>
	</div>
	<div id="dialog_create_recurrence" title="Create New Recurrences" style="display: none;">
		<script type="text/javascript">
		$(document).on('change', 'select[name="recurrence_repeat_type"],select[name="recurrence_repeat_monthly_type"]', function() {
			var repeat_type = $('[name="recurrence_repeat_type"]').val();
			var month_type = $('[name="recurrence_repeat_monthly_type"]').val();
			if(repeat_type == 'week') {
				$('.recurrence_monthly_settings').hide();
				$('.recurrence_repeat_days').show();
			} else if(repeat_type == 'month') {
				$('.recurrence_monthly_settings').show();
				if(month_type != 'day') {
					$('.recurrence_repeat_days').show();
				} else {
					$('.recurrence_repeat_days').hide();
				}
			} else {
				$('.recurrence_monthly_settings').hide();
				$('.recurrence_repeat_days').hide();
			}
		});
		</script><span class="ui-helper-hidden-accessible"><input type="text"/></span>
		<div class="form-group">
			<label class="col-sm-4 control-label">Start Date:</label>
			<div class="col-sm-8">
				<input type="text" name="recurrence_start_date" data-current-day="<?= date('Y-m-d') ?>" class="form-control datepicker" value="<?= date('Y-m-d') ?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">End Date:</label>
			<div class="col-sm-8">
				<input type="text" name="recurrence_end_date" class="form-control datepicker" value="">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Repeats:</label>
			<div class="col-sm-8">
				<select name="recurrence_repeat_type" class="form-control chosen-select-deselect">
					<option value="day">Daily</option>
					<option value="week" selected>Weekly</option>
					<option value="month">Monthly</option>
				</select>
			</div>
		</div>
		<div class="form-group recurrence_monthly_settings" style="display:none;">
			<label class="col-sm-4 control-label">Repeat Tab:</label>
			<div class="col-sm-8">
				<select name="recurrence_repeat_monthly_type" class="form-control  chosen-select-deselect">
					<option value="day" selected>By Day</option>
					<option value="first">First Week of Month</option>
					<option value="second">Second Week of Month</option>
					<option value="third">Third Week of Month</option>
					<option value="fourth">Fourth Week of Month</option>
					<option value="last">Last Week of Month</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Repeat Interval:</label>
			<div class="col-sm-8">
				<select name="recurrence_repeat_interval" class="form-control chosen-select-deselect">
	                <?php for ($repeat_i = 1; $repeat_i <= 30; $repeat_i++) {
	                    echo '<option value="'.$repeat_i.'">'.$repeat_i.'</option>';
	                } ?>
				</select>
			</div>
		</div>
		<div class="form-group recurrence_repeat_days">
			<label class="col-sm-4 control-label">Repeat Days:</label>
			<div class="col-sm-8">
	            <?php $days_of_week = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
	            foreach ($days_of_week as $day_of_week_label) {
	                echo '<label style="padding-right: 0.5em; "><input type="checkbox" name="recurrence_repeat_days[]" value="'.$day_of_week_label.'">'.$day_of_week_label.'</label>';
	            } ?>
			</div>
		</div>
	</div>
	<div id="dialog_edit_recurrence" title="Edit Recurrences?" style="display: none;">
		Would you like to edit all recurrences for this <?= TICKET_NOUN ?> or just this one occurence?
	</div>
	<div id="dialog_recurrence_settings" title="Edit or Create Recurrences?" style="display: none;">
		Would you like to edit current Recurrence settings or create new Recurrences?
	</div>
	<div id="dialog_view_site" title="Select a Site" style="display: none;">
		<div class="form-group">
			<label class="col-sm-4">Site:</label>
			<div class="col-sm-8">
				<select name="view_site" data-placeholder="Select a Site..." class="chosen-select-deselect">
					<option></option>
				</select>
			</div>
		</div>
	</div>
<?php } ?>
<?php if(!empty($_GET['calendar_view'])) { ?>
	<div class="double-gap-top standard-body form-horizontal calendar-iframe-screen <?= $calendar_ticket_slider=='full' ? 'calendar-iframe-full' : 'calendar-iframe-accordion'; ?>">
		<input type="hidden" id="calendar_view" value="true">
<?php } ?>
<input type="hidden" name="sync_recurrences" id="sync_recurrences" value="<?= ($is_recurrence && !($_GET['action_mode'] > 0) && !($_GET['estimate_mode'] > 0) && !($_GET['overview_mode'] > 0) ? 1 : 0) ?>">
<input type="hidden" name="checkout_before_checkin" id="checkin_before_checkout" value="<?= strpos($value_config,',Check Out Before Check In,') !== FALSE ? 1 : 0 ?>">
<?php if(get_config($dbc, 'ticket_textarea_style') == 'no_editor') { ?>
	<script>
	var no_tools = true;
	</script>
<?php } ?>
<?php if(empty($_GET['calendar_view']) || $calendar_ticket_slider == 'accordion') { ?>
	<?php if($_GET['calendar_view'] == 'true' && (!($_GET['estimate_mode'] > 0) || !$force_readonly)) { ?>
		<div class="col-sm-12 double-gap-top">
			<h3 style="margin-top: 5px;"><?= !empty($_GET['edit']) ? ($_GET['overview_mode'] > 0 ? '' : 'Edit ') : 'Add ' ?><?= TICKET_NOUN ?> <span class="ticketid_span"><?= get_ticket_label($dbc, $get_ticket) ?></span><?= $_GET['overview_mode'] > 0 ? ' Overview' : '' ?>
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
			<span class="block-label flag-label-block" style="<?= $get_ticket['flag_colour'] != '' && $get_ticket['flag_colour'] != 'FFFFFF' ? '' : 'display:none;' ?>background-color:#<?= $get_ticket['flag_colour'] ?>;">Flagged<?= empty($flag_comment) ? '' : ': '.$flag_comment ?></span>
            <a href="../blank_loading_page.php" class="pull-right" style="margin-top: -0.5em;"><img class="no-toggle inline-img" src="../img/icons/cancel.png" title="Close"></a>
            </h3>
			<hr>
		</div>
	<?php }
    if(IFRAME_PAGE) {
        echo $quick_action_html.'<div class="clearfix"></div>';
    }
	if(count($ticket_tabs) > 1 && !($_GET['action_mode'] > 0 || $_GET['estimate_mode'] > 0 || $_GET['overview_mode'] > 0) && $tile_security['edit'] > 0 && !($strict_view > 0)) { ?>
		<div class="form-group clearfix <?= $calendar_ticket_slider != 'accordion' ? 'show-on-mob' : '' ?>" <?= isset($_GET['intake_key']) ? 'style="display:none;"' : '' ?>>
			<label for="ticket_type" class="col-sm-4 control-label text-right"><?= TICKET_NOUN ?> Tab:</label>
			<div class="col-sm-8">
				<select name="ticket_type" id="ticket_type" data-placeholder="Select a Tab..." data-initial="<?= $ticket_type ?>" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control">
					<option value=''></option>
					<?php foreach($ticket_tabs as $type_name) {
						$type_value = config_safe_str($type_name);
						if(check_subtab_persmission($dbc, 'ticket', ROLE, 'ticket_type_'.$type_value) === TRUE) {
							echo "<option ".($type_value == $ticket_type ? 'selected' : '')." value='$type_value'>$type_name</option>";
						}
					} ?>
				</select>
			</div>
		</div>
	<?php } else if(count($ticket_tabs) == 1) { ?>
        <input type="hidden" name="ticket_type" id="ticket_type" data-placeholder="Select a Tab..." data-initial="<?= $ticket_type ?>" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?php foreach($ticket_tabs as $type_id => $type_name) { echo $type_id; } ?>">
    <?php } ?>
	<?php /*if($_GET['action_mode'] == 1 && $calendar_ticket_slider == 'accordion') {
		$get_query = $_GET;
		unset($get_query['action_mode']); ?>
		<div class="pull-right gap-left gap-top">
			<a href="?<?= http_build_query($get_query); ?>" class="btn brand-btn" onclick="viewFullTicket(this); return false;">View Full <?= TICKET_NOUN ?></a>
		</div>
	<?php } ?>
	<?php if($_GET['overview_mode'] == 1 && $calendar_ticket_slider == 'accordion') {
		$get_query = $_GET;
		unset($get_query['overview_mode']); ?>
		<div class="pull-right gap-left gap-top">
			<a href="?<?= http_build_query($get_query); ?>" class="btn brand-btn" onclick="viewFullTicket(this); return false;">View Full <?= TICKET_NOUN ?></a>
		</div>
	<?php }*/ ?>
	<div class="<?= $calendar_ticket_slider != 'accordion' ? 'show-on-mob' : '' ?>">
		<span class="sync_recurrences_note" style="display: none; color: red;"><b>You are editing all Recurrences of this <?= TICKET_NOUN?>. Please refresh the page if you would like to edit only this occurrence.</b></span>
	</div>
	<div class="<?= $calendar_ticket_slider != 'accordion' ? 'show-on-mob' : 'main-screen' ?> panel-group block-panels col-xs-12 form-horizontal" style="background-color: #fff; padding: 0; margin-left: 5px; width: calc(100% - 10px); height: 100%;" id="mobile_tabs">
		<?php if($wait_on_approval) {
			echo '<h4>Submitted for Administration Approval</h4>';
		} ?>
		<?php $current_heading = '';
		$current_heading_closed = true;
		$indent_accordion_text = '';
		$sort_order = array_filter($sort_order);
		if($_GET['action_mode'] > 0 || $_GET['estimate_mode'] > 0 || $_GET['overview_mode'] > 0) {
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
		foreach($sort_order as $sort_field) { ?>
			<?php //Add higher level heading
			if($_GET['edit_staff_dashboard'] != 1) {
				$this_heading = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_headings` WHERE `ticket_type` = '".(empty($ticket_type) ? 'tickets' : 'tickets_'.$ticket_type)."' AND `accordion` = '".$sort_field."'"))['heading'];
			}
			if($this_heading != $current_heading) {
				if(!$current_heading_closed) { ?>
							</div>
						</div>
					</div>
					<?php $current_heading_closed = true;
					$indent_accordion_text = '';
					$heading_id = '';
				}
				if(!empty($this_heading)) {
					$heading_id = '_'.config_safe_str($this_heading); ?>
					<div class="panel panel-default">
						<div class="panel-heading mobile_load higher_level_heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_heading_<?= config_safe_str($this_heading) ?>">
									<?= $this_heading ?><span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_heading_<?= config_safe_str($this_heading) ?>" class="panel-collapse collapse">
							<div class="panel-body" style="padding: 0; margin: -1px;" id="mobile_tabs<?= $heading_id ?>">
							<?php $current_heading_closed = false;
							$current_heading = $this_heading;
							$indent_accordion_text = 'class="double-gap-left"';
				}
			} ?>
			<?php if(strpos($value_config, ','.$sort_field.',') !== FALSE && substr($sort_field, 0, strlen('FFMCUST_')) === 'FFMCUST_') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_<?= str_replace(' ','_',$sort_field) ?>">
								<?= explode('FFMCUST_', $sort_field)[1] ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_<?= str_replace(' ','_',$sort_field) ?>" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=<?= str_replace(' ','_',$sort_field) ?>">
							Loading...
						</div>
					</div>
				</div>
		 	<?php } ?>
			<?php $renamed_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($ticket_type) ? 'tickets' : 'tickets_'.$ticket_type)."' AND `accordion` = '".$sort_field."'"))['accordion_name']; ?>
			<?php if (strpos($value_config, ','."Information".',') !== FALSE && $sort_field == 'Information' && $access_view_project_info > 0) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_project_info">
								<?= !empty($renamed_accordion) ? $renamed_accordion : PROJECT_NOUN.' Information' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_project_info" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=project_info">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if (strpos($value_config, ','."Purchase Order List".',') !== FALSE && $sort_field == 'Purchase Order List') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_purchase_order">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Purchase Orders' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_purchase_order" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_po_number">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if (strpos($value_config, ','."Customer Orders".',') !== FALSE && $sort_field == 'Customer Orders') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_customer_order">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Customer Orders' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_customer_order" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_customer_order">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if (strpos($value_config, ','."Details".',') !== FALSE && $sort_field == 'Details') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_project_details">
								<?= !empty($renamed_accordion) ? $renamed_accordion : PROJECT_NOUN.' Details' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_project_details" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=project_details">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if (strpos($value_config, ','."Contact Notes".',') !== FALSE && $sort_field == 'Contact Notes') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_contact_notes">
								<?= !empty($renamed_accordion) ? $renamed_accordion : CONTACTS_NOUN.' Notes' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_contact_notes" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_contact_notes">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if (strpos($value_config, ','."Path & Milestone".',') !== FALSE && $sort_field == 'Path & Milestone') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_path_milestone">
								<?= !empty($renamed_accordion) ? $renamed_accordion : PROJECT_NOUN.' Path & Milestone' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_path_milestone" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_path_milestone">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Individuals".',') !== FALSE && $sort_field == 'Individuals') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_individuals">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Individuals Present' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_individuals" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_individuals">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Fees".',') !== FALSE && $sort_field == 'Fees') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_fees">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Fees' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_fees" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_fees">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if ((strpos($value_config, ','."Location".',') !== FALSE || strpos($value_config, ','."Emergency".',') !== FALSE) && $sort_field == 'Location') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_location">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Sites' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_location" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_location">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Members ID".',') !== FALSE && $sort_field == 'Members ID') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_members_id_card">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Members ID Card' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_members_id_card" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_members_id_card">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if ((strpos($value_config, ','."Mileage".',') !== FALSE || strpos($value_config, ','."Drive Time".',') !== FALSE) && $sort_field == 'Mileage') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_mileage">
								<?= !empty($renamed_accordion) ? $renamed_accordion : (strpos($value_config, ','."Mileage".',') !== FALSE ? 'Mileage' : 'Drive Time') ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_mileage" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_mileage">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if(strpos($value_config, ',Staff,') !== FALSE && $sort_field == 'Staff' && $access_view_staff > 0) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_staff_list">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_staff_list" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_staff_list">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if(strpos($value_config, ',Staff Tasks,') !== FALSE && $sort_field == 'Staff Tasks' && $access_view_staff > 0) { ?>
				<?php if($access_any == true) { ?>
					<div class="panel panel-default">
						<div class="panel-heading mobile_load">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_staff_assign_tasks">
									<?= !empty($renamed_accordion) ? $renamed_accordion : 'Assigned Tasks' ?><span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_ticket_staff_assign_tasks" class="panel-collapse collapse">
							<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_staff_assign_tasks">
								Loading...
							</div>
						</div>
					</div>
				<?php } ?>
				<?php if($ticketid > 0 && $_GET['new_ticket'] != 'true') { ?>
					<div class="panel panel-default">
						<div class="panel-heading mobile_load">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_staff_tasks">
									<?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff Tasks' ?><span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_ticket_staff_tasks" class="panel-collapse collapse">
							<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_staff_tasks">
								Loading...
							</div>
						</div>
					</div>
				<?php } ?>
			<?php } ?>

			<?php if(strpos($value_config, ',Members,') !== FALSE && $sort_field == 'Members') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_members">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Members' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_members" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_members">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if(strpos($value_config, ',Clients,') !== FALSE && $sort_field == 'Clients') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_clients">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Clients' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_clients" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_clients">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if(strpos($value_config, ',Wait List,') !== FALSE && $sort_field == 'Wait List') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_wait_list">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Wait List' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_wait_list" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_wait_list">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if ((strpos($value_config, ','."Check In".',') !== FALSE || strpos($value_config, ','."Check In Member Drop Off".',') !== FALSE) && $sort_field == 'Check In') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_checkin">
								<?= !empty($renamed_accordion) ? $renamed_accordion : (strpos($value_config, ','."Check In Member Drop Off".',') !== FALSE ? 'Member Drop Off' : 'Check In') ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_checkin" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_checkin">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Medication".',') !== FALSE && $access_medication === TRUE && $sort_field == 'Medication') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_medication">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Medication Administration' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_medication" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_medications">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Ticket Details".',') !== FALSE && $sort_field == 'Ticket Details') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_info">
								<?= !empty($renamed_accordion) ? $renamed_accordion : TICKET_NOUN.' Details' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_info" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_info">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Services".',') !== FALSE && $sort_field == 'Ticket Details') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_services">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Services' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_services" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_info">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Service Staff Checklist".',') !== FALSE && $sort_field == 'Service Staff Checklist') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_service_checklist">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Service Checklist' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_service_checklist" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_service_checklist">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Service Extra Billing".',') !== FALSE && $sort_field == 'Service Extra Billing') {
				$display_none = '';
				if(strpos($value_config, ',Service Extra Billing Display Only If Exists,') !== FALSE) {
					$num_extra_billing = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`ticketcommid`) `num_rows` FROM `ticket_comment` WHERE `ticketid` = '$ticketid' AND `deleted` = 0 AND `type` = 'service_extra_billing'"))['num_rows'];
					if(!($num_extra_billing > 0)) {
						$display_none = 'style="display:none;"';
					}
				} ?>
				<div class="panel panel-default service_extra_billing" <?= $display_none ?>>
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_service_extra_billing">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Service Extra Billing' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_service_extra_billing" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_service_extra_billing">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Equipment".',') !== FALSE && $sort_field == 'Equipment') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_equipment">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Equipment' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_equipment" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_equipment">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Inventory".',') !== FALSE && $sort_field == 'Inventory') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_inventory">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Inventory' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_inventory" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_inventory">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Inventory General".',') !== FALSE && $sort_field == 'Inventory General') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_inventory_general">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'General Cargo / Inventory Information' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_inventory_general" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_inventory_general">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Inventory Detail".',') !== FALSE && $sort_field == 'Inventory Detail') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_inventory_detailed">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Detailed Cargo / Inventory Information' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_inventory_detailed" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_inventory_detailed">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Inventory Return".',') !== FALSE && $sort_field == 'Inventory Return') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_inventory_return">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Return Information' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_inventory_return" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_inventory_return">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Checklist".',') !== FALSE && $access_all > 0 && $sort_field == 'Checklist') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_checklist">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Checklist' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_checklist" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_checklist">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Checklist Items".',') !== FALSE && $access_all > 0 && $sort_field == 'Checklist Items') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_view_checklist">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Checklists' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_view_checklist" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_view_checklist">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Charts".',') !== FALSE && $access_all > 0 && $sort_field == 'Charts') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_view_charts">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Charts' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_view_charts" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_view_charts">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Safety".',') !== FALSE && $access_all > 0 && $sort_field == 'Safety') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_safety">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Safety' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_safety" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_safety">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Materials".',') !== FALSE && $sort_field == 'Materials') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_materials">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Materials' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_materials" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_materials">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Inventory".',') !== FALSE && strpos($value_config, ',Inventory Basic') !== FALSE && $sort_field == 'Inventory') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_inventory">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Inventory' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_inventory" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_inventory">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Miscellaneous".',') !== FALSE && strpos($value_config, ',Miscellaneous') !== FALSE && $sort_field == 'Miscellaneous') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_miscellaneous">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Miscellaneous' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_miscellaneous" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_miscellaneous">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Purchase Orders".',') !== FALSE && $access_all > 0 && $sort_field == 'Purchase Orders') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_purchase_orders">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Purchase Orders' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_purchase_orders" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_purchase_orders">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Attached Purchase Orders".',') !== FALSE && $access_all > 0 && $sort_field == 'Attached Purchase Orders') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_attach_purchase_orders">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Purchase Orders' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_attach_purchase_orders" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_attach_purchase_orders">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php $stopid = filter_var($_GET['stop'],FILTER_SANITIZE_STRING);
            $stop_list = $dbc->query("SELECT * FROM `ticket_schedule` WHERE `ticketid`='$ticketid' AND `deleted`=0 AND '$stopid' IN (`id`,'')");
            if (strpos($value_config, ','."Delivery".',') !== FALSE && strpos($value_config, ',Delivery Stops') === FALSE && $sort_field == 'Delivery' && $stop_list->num_rows > 0) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load higher_level_heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_delivery">
                                <?= (!empty($renamed_accordion) ? $renamed_accordion : 'Delivery Details') ?><span class="pull-right"><?= $stop_list->num_rows ?></span><span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_ticket_delivery" class="panel-collapse collapse">
                        <div class="panel-body no-pad">
                            <?php while($ticket_stop = $stop_list->fetch_assoc()) { ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading mobile_load">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#collapse_ticket_delivery" <?= $indent_accordion_text ?> href="#collapse_ticket_delivery_<?= $ticket_stop['id'] ?>">
                                                <?= ($ticket_stop['id'] > 0 ? (empty($ticket_stop['client_name']) ? $ticket_stop['location_name'] : $ticket_stop['client_name']) : '').'<br /><em class="small">Status: '.$ticket_stop['status'].'</em>' ?><span class="glyphicon glyphicon-plus"></span>
                                            </a>
                                        </h4>
                                    </div>

                                    <div id="collapse_ticket_delivery_<?= $ticket_stop['id'] ?>" class="panel-collapse collapse">
                                        <div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_delivery&stop=<?= $ticket_stop['id'] ?>">
                                            Loading...
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
			<?php } else if (strpos($value_config, ','."Delivery".',') !== FALSE && $sort_field == 'Delivery') { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_delivery">

                                <?= (!empty($renamed_accordion) ? $renamed_accordion : 'Delivery Details') ?><span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>


                    <div id="collapse_ticket_delivery_<?= $ticket_stop['id'] ?>" class="panel-collapse collapse">

                        <div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_delivery&stop=<?= $_GET['stop'] ?>">
                            Loading...
                        </div>
                    </div>
                </div>
			<?php } ?>

			<?php if (strpos($value_config, ',Delivery Summary,') !== FALSE && $sort_field == 'Delivery Summary') { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_delivery_summary">
                                <?= !empty($renamed_accordion) ? $renamed_accordion : 'Delivery Summary' ?><span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_ticket_delivery_summary" class="panel-collapse collapse">
                        <div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_delivery_summary">
                            Loading...
                        </div>
                    </div>
                </div>
            <?php } ?>

			<?php if (strpos($value_config, ',Transport,') !== FALSE && $sort_field == 'Transport') { ?>
				<?php if(strpos($value_config, ',Transport Origin') !== FALSE) { ?>
					<div class="panel panel-default">
						<div class="panel-heading mobile_load">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_transport_origin">
									<?= !empty($renamed_accordion) ? $renamed_accordion : 'Transport Log - Origin' ?><span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_ticket_transport_origin" class="panel-collapse collapse">
							<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_transport_origin">
								Loading...
							</div>
						</div>
					</div>
				<?php } ?>
				<?php if(strpos($value_config, ',Transport Destination') !== FALSE) { ?>
					<div class="panel panel-default">
						<div class="panel-heading mobile_load">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_transport_destination">
									<?= !empty($renamed_accordion) ? $renamed_accordion : 'Transport Log - Destination' ?><span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_ticket_transport_destination" class="panel-collapse collapse">
							<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_transport_destination">
								Loading...
							</div>
						</div>
					</div>
				<?php } ?>
				<?php if(strpos(str_replace(['Transport Origin','Transport Destination'],'',$value_config), ',Transport ') !== FALSE) { ?>
					<div class="panel panel-default">
						<div class="panel-heading mobile_load">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_transport_details">
									<?= !empty($renamed_accordion) ? $renamed_accordion : 'Carrier Details' ?><span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_ticket_transport_details" class="panel-collapse collapse">
							<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_transport_details">
								Loading...
							</div>
						</div>
					</div>
				<?php } ?>
			<?php } ?>

			<?php if (strpos($value_config, ','."Documents".',') !== FALSE && $sort_field == 'Documents') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_view_ticket_documents">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Documents' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_view_ticket_documents" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=view_ticket_documents">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if ((strpos($value_config, ','."Check Out".',') !== FALSE || strpos($value_config, ','."Check Out Member Pick Up".',') !== FALSE) && $sort_field == 'Check Out') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_checkout">
								<?= !empty($renamed_accordion) ? $renamed_accordion : (strpos($value_config, ','."Check Out Member Pick Up".',') !== FALSE ? 'Member Pick Up' : 'Check Out') ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_checkout" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_checkout">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Staff Check Out".',') !== FALSE && $sort_field == 'Staff Check Out') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_checkout_staff">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff Check Out' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_checkout_staff" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_checkout_staff&staffcheckout=true">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if ((strpos($value_config, ','."Deliverables".',') !== FALSE || strpos($value_config, ','."Deliverable To Do".',') !== FALSE || strpos($value_config, ','."Deliverable Internal".',') !== FALSE || strpos($value_config, ','."Deliverable Customer".',') !== FALSE) && $sort_field == 'Deliverables') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_view_ticket_deliverables">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Deliverables' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_view_ticket_deliverables" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=view_ticket_deliverables">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Time Tracking Block".',') !== FALSE && $sort_field == 'Timer') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_view_ticket_timer">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Time Tracking' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_view_ticket_timer" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=view_ticket_timer">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Day Tracking Block".',') !== FALSE && $access_all > 0 && $sort_field == 'Timer') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_view_day_tracking">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Day Tracking' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_view_day_tracking" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=view_day_tracking">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Addendum".',') !== FALSE && $sort_field == 'Addendum') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_addendum_view_ticket_comment">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Addendum' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_addendum_view_ticket_comment" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=addendum_view_ticket_comment">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Client Log".',') !== FALSE && $sort_field == 'Client Log') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_log_notes">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff Log Notes' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_log_notes" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_log_notes">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Debrief".',') !== FALSE && $sort_field == 'Debrief') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_debrief_view_ticket_comment">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Debrief' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_debrief_view_ticket_comment" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=debrief_view_ticket_comment">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Member Log Notes".',') !== FALSE && $sort_field == 'Member Log Notes') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_member_view_ticket_comment">
								<?= !empty($renamed_accordion) ? $renamed_accordion : $category.' Specific Daily Log Notes' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_member_view_ticket_comment" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=member_view_ticket_comment">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Cancellation".',') !== FALSE && $sort_field == 'Cancellation') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_cancellation">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Cancellation' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_cancellation" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_cancellation">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Custom Notes".',') !== FALSE && $sort_field == 'Custom Notes') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_custom_view_ticket_comment">
								<?= get_config($dbc, 'ticket_custom_notes_heading') ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_custom_view_ticket_comment" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=custom_view_ticket_comment">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Internal Communication".',') !== FALSE && $sort_field == 'Internal Communication') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_internal_communication">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Internal Communication' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_internal_communication" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=internal_communication">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."External Communication".',') !== FALSE && $sort_field == 'External Communication') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_external_communication">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'External Communication' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_external_communication" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=external_communication">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Notes".',') !== FALSE && $sort_field == 'Notes') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_notes_view_ticket_comment">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Notes' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_notes_view_ticket_comment" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=notes_view_ticket_comment">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if ((strpos($value_config, ','."Summary".',') !== FALSE || strpos($value_config, ','."Staff Summary".',') !== FALSE) && $sort_field == 'Summary' && $access_view_summary > 0) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_summary">
								<?= !empty($renamed_accordion) ? $renamed_accordion : (strpos($value_config, ','."Staff Summary".',') !== FALSE ? 'Staff Summary' : 'Summary') ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_summary" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_summary">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Multi-Disciplinary Summary Report".',') !== FALSE && $sort_field == 'Multi-Disciplinary Summary Report') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_view_multi_disciplinary_summary_report">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Multi-Disciplinary Summary Report' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_view_multi_disciplinary_summary_report" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=view_multi_disciplinary_summary_report">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Complete".',') !== FALSE && $sort_field == 'Complete' && $access_view_complete > 0) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_complete">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Complete '.TICKET_NOUN ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_complete" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_complete">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Notifications".',') !== FALSE && $sort_field == 'Notifications' && $access_view_notifications > 0) { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_view_ticcket_notifications">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Notifications' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_view_ticcket_notifications" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=view_ticket_notifications">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Region Location Classification".',') !== FALSE && $sort_field == 'Region Location Classification') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_reg_loc_class">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Region/Location/Classification' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_reg_loc_class" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_reg_loc_class">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Incident Reports".',') !== FALSE && $sort_field == 'Incident Reports') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_view_ticket_incident_reports">
								<?= !empty($renamed_accordion) ? $renamed_accordion : INC_REP_TILE ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_view_ticket_incident_reports" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=view_ticket_incident_reports">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Billing".',') !== FALSE && $sort_field == 'Billing') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_billing">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Billing' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_billing" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_billing">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Residue".',') !== FALSE && $sort_field == 'Residue') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_residues">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Residue' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_residues" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_residues">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Reading".',') !== FALSE && $sort_field == 'Reading') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_readings">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Reading' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_readings" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_readings">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Other List".',') !== FALSE && $sort_field == 'Other List') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_other_list">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Other List' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_other_list" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_other_list">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Pressure".',') !== FALSE && $sort_field == 'Pressure') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_pressure">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Pressure' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_pressure" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_pressure">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Chemicals".',') !== FALSE && $sort_field == 'Chemicals') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_chemicals">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Chemicals' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_chemicals" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_chemicals">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Application Report".',') !== FALSE && $sort_field == 'Application Report') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_apply_report">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Application Report' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_apply_report" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_apply_report">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Intake".',') !== FALSE && $sort_field == 'Intake') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_intake">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Intake' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_intake" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_intake">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."History".',') !== FALSE && $sort_field == 'History') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_history">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'History' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_history" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_history">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Work History".',') !== FALSE && $sort_field == 'Work History') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_work_history">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Work History' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_work_history" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_work_history">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Tank Reading".',') !== FALSE && $sort_field == 'Tank Reading') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_tank_readings">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Tank Reading' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_tank_readings" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_intake_tank_readings">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Shipping List".',') !== FALSE && $sort_field == 'Shipping List') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_shipping_list">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Shipping List' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_shipping_list" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_shipping_list">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Location Details".',') !== FALSE && $sort_field == 'Location Details') { ?>
				<div class="panel panel-default">
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_location_details">
								<?= !empty($renamed_accordion) ? $renamed_accordion : 'Location Details' ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ticket_location_details" class="panel-collapse collapse">
						<div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_location_details">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if (strpos($value_config, ','."Customer Notes".',') !== FALSE && $sort_field == 'Customer Notes') {
                $stopid = filter_var($_GET['stop'],FILTER_SANITIZE_STRING);
                $stop_list = $dbc->query("SELECT * FROM `ticket_schedule` WHERE `ticketid`='$ticketid' AND `deleted`=0 AND '$stopid' IN (`id`,'')");
                if($stop_list->num_rows > 0) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading mobile_load higher_level_heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_customer_notes">
                                    <?= (!empty($renamed_accordion) ? $renamed_accordion : 'Customer Notes') ?><span class="pull-right"><?= $stop_list->num_rows ?></span><span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>

                        <div id="collapse_ticket_customer_notes" class="panel-collapse collapse">
                            <div class="panel-body no-pad" data-accordion="<?= $sort_field ?>">
                                <?php while($ticket_stop = $stop_list->fetch_assoc()) { ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading mobile_load">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#collapse_ticket_customer_notes" <?= $indent_accordion_text ?> href="#collapse_ticket_customer_notes_<?= $ticket_stop['id'] ?>">
                                                    <?= ($ticket_stop['id'] > 0 ? (empty($ticket_stop['client_name']) ? $ticket_stop['location_name'] : $ticket_stop['client_name']) : '').'<br><em class="small">Status: '.$ticket_stop['status'].'</em>' ?><span class="glyphicon glyphicon-plus"></span>
                                                </a>
                                            </h4>
                                        </div>

                                        <div id="collapse_ticket_customer_notes_<?= $ticket_stop['id'] ?>" class="panel-collapse collapse">
                                            <div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_customer_notes&stop=<?= $ticket_stop['id'] ?>">
                                                Loading...
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading mobile_load">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mobile_tabs<?= $heading_id ?>" <?= $indent_accordion_text ?> href="#collapse_ticket_customer_notes">
                                    <?= (!empty($renamed_accordion) ? $renamed_accordion : 'Customer Notes') ?><span class="glyphicon glyphicon-plus"></span>

                                </a>
                            </h4>
                        </div>


                        <div id="collapse_ticket_customer_notes_<?= $ticket_stop['id'] ?>" class="panel-collapse collapse">

                            <div class="panel-body" data-accordion="<?= $sort_field ?>" data-file-name="edit_ticket_tab.php?<?= isset($_GET['intake_key']) ? 'intake_key='.$_GET['intake_key'].'&' : '' ?>ticketid=<?= $ticketid ?>&tab=ticket_customer_notes&stop=<?= $ticket_stop['id'] ?>">
                                Loading...
                            </div>
                        </div>
                    </div>
                <?php } ?>
			<?php } ?>

		<?php } ?>
		<?php //Close heading if not already closed
		if(!$current_heading_closed) { ?>
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="clearfix"></div>
	</div>
<?php } ?>
<?php if(empty($_GET['calendar_view'])) { ?>
	<div class="tile-sidebar sidebar sidebar-override hide-titles-mob standard-collapsible" <?= $ticket_layout == 'Accordions' ? 'style="display:none;"' : '' ?>>
		<ul><?php include('../Ticket/edit_sidebar.php'); ?></ul>
	</div>
	<div class="scale-to-fill has-main-screen hide-titles-mob">
		<div class="main-screen standard-body default_screen form-horizontal" id="main_screen_block">
			<div class="standard-body-title" style="<?= $ticket_layout == 'Accordions' ? 'display:none;' : '' ?><?= $get_ticket['flag_colour'] != '' && $get_ticket['flag_colour'] != 'FFFFFF' ? 'background-color:#'.$get_ticket['flag_colour'].';' : '' ?>">
				<h3><?= !empty($_GET['edit']) ? ($_GET['overview_mode'] > 0 ? '' : 'Edit ') : 'Add ' ?><?= TICKET_NOUN ?><?= $_GET['overview_mode'] > 0 ? ' Overview' : '' ?>
					<?php /*if($_GET['action_mode'] == 1) {
						$get_query = $_GET;
						unset($get_query['action_mode']); ?>
						<div class="pull-right gap-left">
							<a href="?<?= http_build_query($get_query); ?>" class="btn brand-btn" onclick="viewFullTicket(this); return false;">View Full <?= TICKET_NOUN ?></a>
						</div>
					<?php } ?>
					<?php if($_GET['overview_mode'] == 1) {
						$get_query = $_GET;
						unset($get_query['overview_mode']); ?>
						<div class="pull-right gap-left">
							<a href="?<?= http_build_query($get_query); ?>" class="btn brand-btn" onclick="viewFullTicket(this); return false;">View Full <?= TICKET_NOUN ?></a>
						</div>
					<?php }*/ ?>
					<?= '<div class="pull-right" style="position: relative; bottom: 0.3em;">'.$quick_action_html.'</div>' ?>
					<span class="flag-label"><?= $flag_comment ?></span>
					<span class="sync_recurrences_note" style="display: none; color: red;"><div class="clearfix"></div><b>You are editing all Recurrences of this <?= TICKET_NOUN?>. Please refresh the page if you would like to edit only this occurrence.</b></span>
				</h3>
                <div class="menu-bar" style="display:none; position:fixed; right:20px; z-index:1;">
                    <div class="menu-content"></div>
                    <img src="../img/icons/ROOK-3dot-icon.png" width="30" class="no-toggle cursor-hand pull-right menu_button offset-right-10" title="More Options" />
                </div>
			</div>
<?php } ?>
<?php if($calendar_ticket_slider != 'accordion' || $include_hidden == 'true') { ?>
		<div class="standard-body-content pad-top <?= $ticket_layout == 'Accordions' ? 'standard-body-accordions' : '' ?>">
            <?php /*if(empty($_GET['calendar_view']) && ($_GET['action_mode'] > 0 || $_GET['overview_mode'] > 0) && $ticket_layout == 'accordion') {
				$get_query = $_GET;
				unset($get_query['action_mode']);
				unset($get_query['overview_mode']); ?>
				<div class="pull-right gap-left gap-top">
					<a href="?<?= http_build_query($get_query); ?>" class="btn brand-btn" onclick="viewFullTicket(this); return false;">View Full <?= TICKET_NOUN ?></a>
				</div>
			<?php }*/ ?>
			<?php if($_GET['calendar_view'] == 'true' && (!($_GET['estimate_mode'] > 0) || !$force_readonly)) { ?>
				<div class="col-sm-12">
					<h3 style="margin-top: 5px;"><?= !empty($_GET['edit']) ? ($_GET['overview_mode'] > 0 ? '' : 'Edit ') : 'Add ' ?><?= TICKET_NOUN ?> <span class="ticketid_span"><?= get_ticket_label($dbc, $get_ticket) ?></span><?= $_GET['overview_mode'] > 0 ? ' Overview' : '' ?>
                        <a href="../blank_loading_page.php" class="pull-right" style="margin-top: -0.5em;"><img class="no-toggle inline-img" src="../img/icons/cancel.png" title="Close"></a>
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
							<span class="block-label flag-label" style="background-color:#<?= $get_ticket['flag_colour'] ?>;">Flagged<?= empty($flag_comment) ? '' : ': '.$flag_comment ?></span>
						<?php } ?>
						<?php /*if($_GET['action_mode'] == 1) {
							$get_query = $_GET;
							unset($get_query['action_mode']); ?>
							<div class="pull-right gap-left gap-top">
								<a href="?<?= http_build_query($get_query); ?>" class="btn brand-btn" onclick="viewFullTicket(this); return false;">View Full <?= TICKET_NOUN ?></a>
							</div>
						<?php } ?>
						<?php if($_GET['overview_mode'] == 1) {
							$get_query = $_GET;
							unset($get_query['overview_mode']); ?>
							<div class="pull-right gap-left gap-top">
								<a href="?<?= http_build_query($get_query); ?>" class="btn brand-btn" onclick="viewFullTicket(this); return false;">View Full <?= TICKET_NOUN ?></a>
							</div>
						<?php } ?>
						<?php $get_query = $_GET;*/ ?>
							<div class="pull-right" style="margin-top: -0.5em;">
								<a href="" onclick="openFullView(); return false;"><img src="../img/icons/ROOK-FullScreen-icon.png" alt="Open Full Window" title="Open Full Window" class="no-toggle inline-img" /></a>
							</div>
						<?php if(strpos($value_config,',Quick Reminder Button,') !== FALSE && !($strict_view > 0) && !isset($_GET['intake_key'])) { ?>
					        <div class="pull-right" style="margin-top: -0.5em;">
								<a href="" onclick="dialogQuickReminder(); return false;"><img class="no-toggle inline-img" src="../img/icons/ROOK-reminder-icon.png" border="0" alt="" title="Add a Quick Reminder." /></a>
							</div>
							<div class="clearfix"></div>
						<?php } ?>
                        <div class="clearfix"></div>
					</h3>
					<hr>
				</div>
			<?php } ?>
			<?php if(strpos($value_config,',Export Ticket Log,') !== FALSE && !empty($ticketid) && !isset($_GET['intake_key'])) {
				$ticket_log_template = !empty(get_config($dbc, 'ticket_log_template')) ? get_config($dbc, 'ticket_log_template') : 'template_a'; ?>
				<a href="../Ticket/ticket_log_templates/<?= $ticket_log_template ?>_pdf.php?ticketid=<?= $ticketid ?>" target="_blank" class="pull-right btn brand-btn gap-top gap-bottom">Export <?= TICKET_NOUN ?> Log</a>
				<div class="clearfix"></div>
			<?php } ?>
			<?php if($wait_on_approval) {
				echo '<h4>Awaiting Admin Approval</h4>';
			} ?>
			<?php if((count($ticket_tabs) > 1 && !($_GET['action_mode'] > 0 || $_GET['estimate_mode'] > 0 || $_GET['overview_mode'] > 0) && $tile_security['edit'] > 0 && !($strict_view > 0)) || isset($_GET['intake_key'])) { ?>
				<div class="tab-section col-sm-12" id="tab_section_ticket_type" <?= isset($_GET['intake_key']) ? 'style="display:none;"' : '' ?>>
					<h3><?= TICKET_NOUN ?> Tab</h3>
					<label for="ticket_type" class="col-sm-4 control-label" style="text-align: left;"><?= TICKET_NOUN ?> Tab:</label>
					<div class="col-sm-8">
						<select name="ticket_type" id="ticket_type" data-initial="<?= $ticket_type ?>" data-placeholder="Select a Tab..." data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control">
							<option value=''></option>
							<?php foreach($ticket_tabs as $type_name) {
								$type_value = config_safe_str($type_name);
								if(check_subtab_persmission($dbc, 'ticket', ROLE, 'ticket_type_'.$type_value) === TRUE) {
									echo "<option ".($type_value == $ticket_type ? 'selected' : '')." value='$type_value'>$type_name</option>";
								}
							} ?>
						</select>
					</div>
					<div class="clearfix"></div>
					<hr>
				</div>
				<div class="clearfix"></div>
			<?php } ?>
			<?php if($ticket_layout == 'Accordions' && $include_hidden != 'true') {
				$collapse_i = 0;
				$collapse_in = 'in'; ?>
		        <div class="panel-group" id="accordion2">
			<?php } ?>
			<?php foreach($sort_order as $sort_field) {
				$value_config = $global_value_config;
				//Custom accordions
				if(strpos($value_config, ','.$sort_field.',') !== FALSE && substr($sort_field, 0, strlen('FFMCUST_')) === 'FFMCUST_') {
					$_GET['tab'] = str_replace(' ','_',$sort_field);
					$acc_label = explode('FFMCUST_',$sort_field)[1];
					include('edit_ticket_tab.php');
			 	}

				if (strpos($value_config, ','."Information".',') !== FALSE && $sort_field == 'Information' && $access_view_project_info > 0) {
					$_GET['tab'] = 'project_info';
					$acc_label = PROJECT_NOUN.' Information';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Purchase Order List".',') !== FALSE && $sort_field == 'Purchase Order List') {
					$_GET['tab'] = 'ticket_po_number';
					$acc_label = 'Purchase Orders';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Customer Orders".',') !== FALSE && $sort_field == 'Customer Orders') {
					$_GET['tab'] = 'ticket_customer_order';
					$acc_label = 'Customer Orders';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Details".',') !== FALSE && $sort_field == 'Details') {
					$_GET['tab'] = 'project_details';
					$acc_label = PROJECT_NOUN.' Details';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Contact Notes".',') !== FALSE && $sort_field == 'Contact Notes') {
					$_GET['tab'] = 'ticket_contact_notes';
					$acc_label = CONTACTS_NOUN.' Notes';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Path & Milestone".',') !== FALSE && $sort_field == 'Path & Milestone') {
					$_GET['tab'] = 'ticket_path_milestone';
					$acc_label = PROJECT_NOUN.' Path & Milestone';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Individuals".',') !== FALSE && $sort_field == 'Individuals') {
					$_GET['tab'] = 'ticket_individuals';
					$acc_label = 'Individuals Present';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Fees".',') !== FALSE && $sort_field == 'Fees') {
					$_GET['tab'] = 'ticket_fees';
					$acc_label = 'Fees';
					include('edit_ticket_tab.php');
				}
				if ((strpos($value_config, ','."Location".',') !== FALSE || strpos($value_config, ','."Emergency".',') !== FALSE) && $sort_field == 'Location') {
					$_GET['tab'] = 'ticket_location';
					$acc_label = 'Site';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Members ID".',') !== FALSE && $sort_field == 'Members ID') {
					$_GET['tab'] = 'ticket_members_id_card';
					$acc_label = 'Members ID Card';
					include('edit_ticket_tab.php');
				}
				if ((strpos($value_config, ','."Mileage".',') !== FALSE || strpos($value_config, ','."Drive Time".',') !== FALSE) && $sort_field == 'Mileage') {
					$_GET['tab'] = 'ticket_mileage';
					$acc_label = strpos($value_config, ','."Mileage".',') !== FALSE ? 'Mileage' : 'Drive Time';
					include('edit_ticket_tab.php');
				}
				if(strpos($value_config, ',Staff,') !== FALSE && $sort_field == 'Staff' && $access_view_staff > 0) {
					$_GET['tab'] = 'ticket_staff_list';
					$acc_label = 'Staff';
					include('edit_ticket_tab.php');
				}
				if(strpos($value_config, ',Staff Tasks,') !== FALSE && $sort_field == 'Staff Tasks' && $access_view_staff > 0) {
					if($access_any == true) {
						$_GET['tab'] = 'ticket_staff_assign_tasks';
						$acc_label = 'Staff Tasks';
						include('edit_ticket_tab.php');
						$collapse_i++;
					}
					if($ticketid > 0 && $_GET['new_ticket'] != 'true') {
						$_GET['tab'] = 'ticket_staff_tasks';
						$acc_label = 'Staff Tasks';
						include('edit_ticket_tab.php');
						$collapse_i++;
					}
				}
				if(strpos($value_config, ',Members,') !== FALSE && $sort_field == 'Members') {
					$_GET['tab'] = 'ticket_members';
					$acc_label = 'Members';
					include('edit_ticket_tab.php');
				}
				if(strpos($value_config, ',Clients,') !== FALSE && $sort_field == 'Clients') {
					$_GET['tab'] = 'ticket_clients';
					$acc_label = 'Clients';
					include('edit_ticket_tab.php');
				}
				if(strpos($value_config, ',Wait List,') !== FALSE && $sort_field == 'Wait List') {
					$_GET['tab'] = 'ticket_wait_list';
					$acc_label = 'Wait List';
					include('edit_ticket_tab.php');
				}
				if ((strpos($value_config, ','."Check In".',') !== FALSE || strpos($value_config, ','."Check In Member Drop Off".',') !== FALSE) && $sort_field == 'Check In') {
					$_GET['tab'] = 'ticket_checkin';
					$acc_label = strpos($value_config, ','."Check In Member Drop Off".',') !== FALSE ? 'Member Drop Off' : 'Check In';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Medication".',') !== FALSE && $access_medication === TRUE && $sort_field == 'Medication') {
					$_GET['tab'] = 'ticket_medications';
					$acc_label = 'Medication Administration';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Ticket Details".',') !== FALSE && $sort_field == 'Ticket Details') {
					$_GET['tab'] = 'ticket_info';
					$acc_label = TICKET_NOUN.' Details';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Services".',') !== FALSE && $sort_field == 'Ticket Details') {
					$_GET['tab'] = 'ticket_info';
					$acc_label = 'Services';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Service Staff Checklist".',') !== FALSE && $sort_field == 'Service Staff Checklist') {
					$_GET['tab'] = 'ticket_service_checklist';
					$acc_label = 'Service Checklist';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Service Extra Billing".',') !== FALSE && $sort_field == 'Service Extra Billing') {
					$_GET['tab'] = 'ticket_service_extra_billing';
					$acc_label = 'Service Extra Billing';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Equipment".',') !== FALSE && $sort_field == 'Equipment') {
					$_GET['tab'] = 'ticket_equipment';
					$acc_label = 'Equipment';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Checklist".',') !== FALSE && $access_all > 0 && $sort_field == 'Checklist') {
					$_GET['tab'] = 'ticket_checklist';
					$acc_label = 'Checklist';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Checklist Items".',') !== FALSE && $access_all > 0 && $sort_field == 'Checklist Items') {
					$_GET['tab'] = 'ticket_view_checklist';
					$acc_label = 'Checklist Items';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Charts".',') !== FALSE && $access_all > 0 && $sort_field == 'Charts') {
					$_GET['tab'] = 'ticket_view_charts';
					$acc_label = 'Charts';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Safety".',') !== FALSE && $access_all > 0 && $sort_field == 'Safety') {
					$_GET['tab'] = 'ticket_safety';
					$acc_label = 'Safety Checklist';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Materials".',') !== FALSE && $sort_field == 'Materials') {
					$_GET['tab'] = 'ticket_materials';
					$acc_label = 'Materials';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ',Miscellaneous') !== FALSE && $sort_field == 'Miscellaneous') {
					$_GET['tab'] = 'ticket_miscellaneous';
					$acc_label = 'Miscellaneous';
					include('edit_ticket_tab.php');
					$collapse_i++;
				}
				if (strpos($value_config, ',Inventory Basic') !== FALSE && $sort_field == 'Inventory') {
					$_GET['tab'] = 'ticket_inventory';
					$acc_label = 'Inventory';
					include('edit_ticket_tab.php');
					$collapse_i++;
				}
				if (strpos($value_config, ',Inventory General,') !== FALSE && $sort_field == 'Inventory General') {
					$_GET['tab'] = 'ticket_inventory_general';
					$acc_label = 'General Cargo / Inventory Information';
					include('edit_ticket_tab.php');
					$collapse_i++;
				}
				if (strpos($value_config, ',Inventory Detail,') !== FALSE && $sort_field == 'Inventory Detail') {
					$_GET['tab'] = 'ticket_inventory_detailed';
					$acc_label = 'Detailed Cargo / Inventory Information';
					include('edit_ticket_tab.php');
					$collapse_i++;
				}
				if (strpos($value_config, ',Inventory Return,') !== FALSE && $sort_field == 'Inventory Return') {
					$_GET['tab'] = 'ticket_inventory_return';
					$acc_label = 'Return Information';
					include('edit_ticket_tab.php');
					$collapse_i++;
				}
				if (strpos($value_config, ','."Purchase Orders".',') !== FALSE && $access_all > 0 && $sort_field == 'Purchase Orders') {
					$_GET['tab'] = 'ticket_purchase_orders';
					$acc_label = 'Purchase Orders';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Attached Purchase Orders".',') !== FALSE && $access_all > 0 && $sort_field == 'Attached Purchase Orders') {
					$_GET['tab'] = 'ticket_attach_purchase_orders';
					$acc_label = 'Purchase Orders';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Delivery".',') !== FALSE && $sort_field == 'Delivery') {
					$_GET['tab'] = 'ticket_delivery';
					$acc_label = 'Delivery Details';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Delivery Summary".',') !== FALSE && $sort_field == 'Delivery Summary') {
					$_GET['tab'] = 'ticket_delivery_summary';
					$acc_label = 'Delivery Summary';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ',Transport Origin') !== FALSE && $sort_field == 'Transport') {
					$_GET['tab'] = 'ticket_transport_origin';
					$acc_label = 'Transport Log - Origin';
					include('edit_ticket_tab.php');
					$collapse_i++;
				}
				if (strpos($value_config, ',Transport Destination') !== FALSE && $sort_field == 'Transport') {
					$_GET['tab'] = 'ticket_transport_destination';
					$acc_label = 'Transport Log - Destination';
					include('edit_ticket_tab.php');
					$collapse_i++;
				}
				if (strpos(str_replace(['Transport Origin','Transport Destination'],'',$value_config), ',Transport ') !== FALSE && $sort_field == 'Transport') {
					$_GET['tab'] = 'ticket_transport_details';
					$acc_label = 'Carrier Details';
					include('edit_ticket_tab.php');
					$collapse_i++;
				}
				if (strpos($value_config, ','."Documents".',') !== FALSE && $sort_field == 'Documents') {
					$_GET['tab'] = 'view_ticket_documents';
					$acc_label = 'Documents';
					include('edit_ticket_tab.php');
				}
				if ((strpos($value_config, ','."Check Out".',') !== FALSE || strpos($value_config, ','."Check Out Member Pick Up".',') !== FALSE) && $sort_field == 'Check Out') {
					$_GET['tab'] = 'ticket_checkout';
					$acc_label = strpos($value_config, ','."Check In Member Pick Up".',') !== FALSE ? 'Member Pick Up' : 'Check Out';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Staff Check Out".',') !== FALSE && $sort_field == 'Staff Check Out') {
					$_GET['tab'] = 'ticket_checkout_staff';
					$acc_label = 'Staff Check Out';
					include('edit_ticket_tab.php');
				}
				if ((strpos($value_config, ','."Deliverables".',') !== FALSE || strpos($value_config, ','."Deliverable To Do".',') !== FALSE || strpos($value_config, ','."Deliverable Internal".',') !== FALSE || strpos($value_config, ','."Deliverable Customer".',') !== FALSE) && $sort_field == 'Deliverables') {
					$_GET['tab'] = 'view_ticket_deliverables';
					$acc_label = 'Deliverables';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Time Tracking Block".',') !== FALSE && $sort_field == 'Timer') {
					$_GET['tab'] = 'view_ticket_timer';
					$acc_label = 'Time Tracking';
					include('edit_ticket_tab.php');
					$collapse_i++;
				}
				if (strpos($value_config, ','."Day Tracking Block".',') !== FALSE && $access_all > 0 && $sort_field == 'Timer') {
					$_GET['tab'] = 'view_day_tracking';
					$acc_label = 'Day Tracking';
					include('edit_ticket_tab.php');
					$collapse_i++;
				}
				if (strpos($value_config, ','."Addendum".',') !== FALSE && $sort_field == 'Addendum') {
					$_GET['tab'] = 'addendum_view_ticket_comment';
					$acc_label = 'Addendum Notes';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Client Log".',') !== FALSE && $sort_field == 'Client Log') {
					$_GET['tab'] = 'ticket_log_notes';
					$acc_label = 'Staff Log Notes';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Debrief".',') !== FALSE && $sort_field == 'Debrief') {
					$_GET['tab'] = 'debrief_view_ticket_comment';
					$acc_label = 'Debrief Notes';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Member Log Notes".',') !== FALSE && $sort_field == 'Member Log Notes') {
					$category = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `category` FROM `contacts` WHERE `category` NOT IN (".STAFF_CATS.",'Business','Sites') AND `deleted`=0 AND `status`>0 GROUP BY `category` ORDER BY COUNT(*) DESC"))['category'];
					$_GET['tab'] = 'member_view_ticket_comment';
					$acc_label = $category.' Daily Log Notes';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Cancellation".',') !== FALSE && $sort_field == 'Cancellation') {
					$_GET['tab'] = 'ticket_cancellation';
					$acc_label = 'Cancellation';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Custom Notes".',') !== FALSE && $sort_field == 'Custom Notes') {
					$_GET['tab'] = 'custom_view_ticket_comment';
					$acc_label = 'Notes';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Internal Communication".',') !== FALSE && $sort_field == 'Internal Communication') {
					$_GET['tab'] = 'internal_communication';
					$acc_label = 'Internal Communication';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."External Communication".',') !== FALSE && $sort_field == 'External Communication') {
					$_GET['tab'] = 'external_communication';
					$acc_label = 'External Communication';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Notes".',') !== FALSE && $sort_field == 'Notes') {
					$_GET['tab'] = 'notes_view_ticket_comment';
					$acc_label = TICKET_NOUN.' Notes';
					include('edit_ticket_tab.php');
				}
				if ((strpos($value_config, ','."Summary".',') !== FALSE || strpos($value_config, ','."Staff Summary".',') !== FALSE) && $sort_field == 'Summary' && $access_view_summary > 0) {
					$_GET['tab'] = 'ticket_summary';
					$acc_label = strpos($value_config, ','."Staff Summary".',') !== FALSE ? 'Staff Summary' : 'Summary';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Multi-Disciplinary Summary Report".',') !== FALSE && $sort_field == 'Multi-Disciplinary Summary Report') {
					$_GET['tab'] = 'view_multi_disciplinary_summary_report';
					$acc_label = 'Multi Disciplinary Summary Notes';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Complete".',') !== FALSE && $sort_field == 'Complete' && $access_view_complete > 0) {
					$_GET['tab'] = 'ticket_complete';
					$acc_label = 'Complete '.TICKET_NOUN;
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Notifications".',') !== FALSE && $sort_field == 'Notifications' && $access_view_notifications > 0) {
					$_GET['tab'] = 'view_ticket_notifications';
					$acc_label = 'Notifications';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Region Location Classification".',') !== FALSE && $sort_field == 'Region Location Classification') {
					$_GET['tab'] = 'ticket_reg_loc_class';
					$acc_label = 'Region/Location/Classification';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Incident Reports".',') !== FALSE && $sort_field == 'Incident Reports') {
					$_GET['tab'] = 'view_ticket_incident_reports';
					$acc_label = INC_REP_TILE;
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Billing".',') !== FALSE && $sort_field == 'Billing') {
					$_GET['tab'] = 'ticket_billing';
					$acc_label = 'Billing';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Customer Notes".',') !== FALSE && $sort_field == 'Customer Notes') {
					$_GET['tab'] = 'ticket_customer_notes';
					$acc_label = 'Customer Notes';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Location Details".',') !== FALSE && $sort_field == 'Location Details') {
					$_GET['tab'] = 'ticket_location_details';
					$acc_label = 'Location Details';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Residue".',') !== FALSE && $sort_field == 'Residue') {
					$_GET['tab'] = 'ticket_residues';
					$acc_label = 'Residue';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Reading".',') !== FALSE && $sort_field == 'Reading') {
					$_GET['tab'] = 'ticket_readings';
					$acc_label = 'Reading';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Tank Reading".',') !== FALSE && $sort_field == 'Tank Reading') {
					$_GET['tab'] = 'ticket_tank_readings';
					$acc_label = 'Tank Reading';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Shipping List".',') !== FALSE && $sort_field == 'Shipping List') {
					$_GET['tab'] = 'ticket_shipping_list';
					$acc_label = 'Shipping List';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Other List".',') !== FALSE && $sort_field == 'Other List') {
					$_GET['tab'] = 'ticket_other_list';
					$acc_label = 'Other List';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Pressure".',') !== FALSE && $sort_field == 'Pressure') {
					$_GET['tab'] = 'ticket_pressure';
					$acc_label = 'Pressure';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Chemicals".',') !== FALSE && $sort_field == 'Chemicals') {
					$_GET['tab'] = 'ticket_chemicals';
					$acc_label = 'Chemicals';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Application Report".',') !== FALSE && $sort_field == 'Application Report') {
					$_GET['tab'] = 'ticket_apply_report';
					$acc_label = 'Application Report';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Intake".',') !== FALSE && $sort_field == 'Intake') {
					$_GET['tab'] = 'ticket_intake';
					$acc_label = 'Intake';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."History".',') !== FALSE && $sort_field == 'History') {
					$_GET['tab'] = 'ticket_history';
					$acc_label = 'History';
					include('edit_ticket_tab.php');
				}
				if (strpos($value_config, ','."Work History".',') !== FALSE && $sort_field == 'Work History') {
					$_GET['tab'] = 'ticket_work_history';
					$acc_label = 'Work History';
					include('edit_ticket_tab.php');
				}
				$collapse_i++;
			} ?>

            <img id="back_to_top" src="../img/icons/ROOK-BackToTop-icon.png" class="no-toggle cursor-hand back-to-top" title="Back To Top" width="30" />
<?php } ?>
			<?php if($ticket_layout == 'Accordions' && $include_hidden != 'true') { ?>
				</div>
			<?php } ?>
            <br /><div class="clearfix"></div>
            <?php if($_GET['estimate_mode'] == 0)  { ?>
                <?php if(IFRAME_PAGE) { ?>
                    <div class="gap-top add_gap_here">
                        <?php if(strpos($value_config,',Finish Button Hide,') === FALSE && !isset($_GET['intake_key'])) { ?>
                            <a href="index.php<?= (empty($_GET['tile_name']) ? '' : '?tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '?tile_group='.$_GET['tile_group']) ?>" class="pull-right btn brand-btn finish_btn" data-history-label="Finish <?= TICKET_NOUN ?>" onclick="<?= $update_time == 'auto_sort' && $ticketid > 0 && $_GET['new_ticket'] != 'true' && $_GET['action_mode'] == 1 ? "if(sorting_done != 2) { return getDriveTime(this, '".date('Y-m-d')."', '".($stopid > 0 ? get_field_value('equipmentid','ticket_schedule','id',$stopid) : $get_ticket['equipmentid'])."'); }" : '' ?><?= (strpos($value_config, ','."Timer".',') !== FALSE) ? 'stopTimers();' : '' ?><?= (strpos($value_config, ','."Check Out".',') !== FALSE || strpos($value_config, ','."Complete Combine Checkout Summary".',') !== FALSE) ? 'return checkoutAll(this);' : '' ?>" <?= strpos($value_config, ','."Finish Check Out Require Signature".',') !== FALSE ? 'data-require-signature="1"' : '' ?> <?= strpos($value_config, ','."Finish Create Recurring Ticket".',') !== FALSE ? 'data-recurring-ticket="1"' : '' ?>>Finish</a>
                        <?php } ?>
                        <?php if($access_any && !isset($_GET['intake_key'])) { ?>
                            <?php if(!empty($admin_group) && !isset($_GET['intake_key'])) { ?>
                                <a class="btn brand-btn pull-right cursor-hand collapsed" data-toggle="collapse" data-target="#approval_submit" data-sort="<?= $update_time ?>" onclick="<?= $update_time == 'auto_sort' && $_GET['action_mode'] == 1 ? "if(sorting_done != 2) { return getDriveTime(this, '".date('Y-m-d')."', '".($stopid > 0 ? get_field_value('equipmentid','ticket_schedule','id',$stopid) : $get_ticket['equipmentid'])."'); }" : '' ?> updateApprovalIDLabel()">Submit for Approval</a>
                            <?php } ?>
                            <?php if(strpos($value_config,',Create Recurrence Button,') !== FALSE && $_GET['action_mode'] != 1 && $_GET['estimate_mode'] != 1 && $_GET['overview_mode'] != 1) { ?>
                                <a href="<?= $back_url ?>" class="pull-right btn brand-btn" data-history-label="Create Recurrence" onclick="dialogCreateRecurrence(this); return false">Create Recurrence</a>
                            <?php } ?>

                            <?php if(strpos($value_config,',Additional,') !== FALSE) { ?>
                                <a href="index.php?edit=0&addition_to=current_ticket<?= (empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group']) ?>" class="pull-right addition_button btn brand-btn" data-history-label="Create Addition" onclick="return addition();">Additional</a>
                            <?php } ?>
                            <?php if(strpos($value_config,',Multiple,') !== FALSE) { ?>
                                <a href="index.php?edit=0&addition_to=current_ticket<?= (empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group']) ?>" class="pull-right multiple_button btn brand-btn" data-history-label="Create Multiple" onclick="return multiple_tickets($('[name=multiple_ticket_count]').val(), ticketid);">Multiple <?= TICKET_TILE ?></a>
                                <div class="col-sm-1 pull-right"><input type="number" value="1" min="1" step="1" class="form-control" name="multiple_ticket_count"></div>
                            <?php } ?>
                            <?php $pdfs = $dbc->query("SELECT `id`, `pdf_name`, `target` FROM `ticket_pdf` WHERE `deleted`=0 AND CONCAT(',',IFNULL(NULLIF(`ticket_types`,''),'$ticket_type'),',') LIKE '%,$ticket_type,%'");
                            while($pdf = $pdfs->fetch_assoc()) { ?>
                                <a href="../Ticket/index.php?custom_form=<?= $pdf['id'] ?>&ticketid=<?= $ticketid > 0 ? $ticketid : '' ?><?= (empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group']) ?>" target="_blank" data-history-label="Create <?= $pdf['pdf_name'] ?> from <?= TICKET_NOUN ?>" class="pull-right btn brand-btn margin-horizontal" data-target="<?= $pdf['target'] ?>" onclick="fillCustomForm(this); return false;"><?= $pdf['pdf_name'] ?></a>
                            <?php } ?>
                        <?php } ?>
                        <?php if(strpos($value_config,',Export Ticket Log,') !== FALSE && !empty($ticketid) && !isset($_GET['intake_key'])) {
                            $ticket_log_template = !empty(get_config($dbc, 'ticket_log_template')) ? get_config($dbc, 'ticket_log_template') : 'template_a'; ?>
                            <a href="../Ticket/ticket_log_templates/<?= $ticket_log_template ?>_pdf.php?ticketid=<?= $ticketid > 0 ? $ticketid : '' ?>" target="_blank" data-history-label="Export <?= TICKET_NOUN ?> Log" class="pull-right btn brand-btn">Export <?= TICKET_NOUN ?> Log</a>
                        <?php } ?>
                        <?php if($access_any && !isset($_GET['intake_key'])) { ?>
                            <a href="<?= $back_url ?>" class="pull-right gap-right"><img class="no-toggle" src="<?= WEBSITE_URL ?>/img/icons/save.png" alt="Save" width="36" title="Save" /></a>
                            <?php if($hide_trash_icon != 1) { ?><a href="<?php echo $back_url; ?>" class="pull-left gap-left" onclick="<?= strpos($value_config, ',Delete Button Add Note,') ? 'dialogDeleteNote(this); return false;' : 'return archive();' ?>"><img class="no-toggle" src="<?= WEBSITE_URL; ?>/img/icons/ROOK-trash-icon.png" alt="Archive" width="36" title="Archive" /></a><?php } ?>
                        <?php } ?>
                    </div>
                <?php } ?>
                <div class="add_gap_here" <?= $calendar_ticket_slider == 'accordion' ? 'style="display:none;"' : '' ?>>
                    <?php if(strpos($value_config,',Finish Button Hide,') === FALSE && !isset($_GET['intake_key'])) { ?>
                        <a href="<?= empty($ticket_next_step_timesheet) || !($ticketid > 0) || $_GET['new_ticket'] == 'true' || $_GET['action_mode'] == 0 ? 'index.php'.(empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group']) : 'next_ticket_step.php?action_mode='.$_GET['action_mode'].(empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group']) ?>" <?= empty($ticket_next_step_timesheet) || !($ticketid > 0) || $_GET['new_ticket'] == 'true' ? '' : 'target="_top"' ?> class="pull-right btn brand-btn finish_btn" data-history-label="Finish <?= TICKET_NOUN ?>" onclick="<?= $update_time == 'auto_sort' && ($ticketid > 0) && $_GET['new_ticket'] != 'true' && $_GET['action_mode'] == 1 ? "if(sorting_done != 2) { return getDriveTime(this, '".date('Y-m-d')."', '".($stopid > 0 ? get_field_value('equipmentid','ticket_schedule','id',$stopid) : $get_ticket['equipmentid'])."'); }" : '' ?><?= (strpos($value_config, ','."Timer".',') !== FALSE) ? 'stopTimers();' : '' ?><?= (strpos($value_config, ','."Check Out".',') !== FALSE || strpos($value_config, ','."Complete Combine Checkout Summary".',') !== FALSE) ? 'return checkoutAll(this);' : '' ?>" <?= strpos($value_config, ','."Finish Check Out Require Signature".',') !== FALSE ? 'data-require-signature="1"' : '' ?> <?= strpos($value_config, ','."Finish Create Recurring Ticket".',') !== FALSE ? 'data-recurring-ticket="1"' : '' ?>>Finish</a>
                    <?php } ?>
                    <?php if($access_any && !isset($_GET['intake_key'])) { ?>
                        <?php if(!empty($admin_group) && !isset($_GET['intake_key'])) { ?>
                            <a class="btn brand-btn pull-right cursor-hand collapsed" data-toggle="collapse" data-target="#approval_submit" data-sort="<?= $update_time ?>" onclick="<?= $update_time == 'auto_sort' && $_GET['action_mode'] == 1 ? "if(sorting_done != 2) { return getDriveTime(this, '".date('Y-m-d')."', '".($stopid > 0 ? get_field_value('equipmentid','ticket_schedule','id',$stopid) : $get_ticket['equipmentid'])."'); }" : '' ?> updateApprovalIDLabel()">Submit for Approval</a>
                        <?php } ?>
                        <?php if(strpos($value_config,',Create Recurrence Button,') !== FALSE && $_GET['action_mode'] != 1 && $_GET['overview_mode'] != 1) { ?>
                            <a href="<?= $back_url ?>" class="pull-right btn brand-btn" data-history-label="Create Recurrence" onclick="dialogCreateRecurrence(this); return false">Create Recurrence</a>
                        <?php } ?>

                        <?php if(strpos($value_config,',Additional,') !== FALSE) { ?>
                            <a href="index.php?edit=0&addition_to=current_ticket<?= (empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group']) ?>" class="pull-right addition_button btn brand-btn" data-history-label="Create Addition" onclick="return addition();">Additional</a>
                        <?php } ?>
                        <?php if(strpos($value_config,',Multiple,') !== FALSE) { ?>
                            <a href="index.php?edit=0&addition_to=current_ticket<?= (empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group']) ?>" class="pull-right multiple_button btn brand-btn" data-history-label="Create Multiple" onclick="return multiple_tickets($('[name=multiple_ticket_count]').val(), ticketid);">Multiple <?= TICKET_TILE ?></a>
                            <div class="col-sm-1 pull-right"><input type="number" value="1" min="1" step="1" class="form-control" name="multiple_ticket_count"></div>
                        <?php } ?>
                        <?php $pdfs = $dbc->query("SELECT `id`, `pdf_name`, `target` FROM `ticket_pdf` WHERE `deleted`=0 AND CONCAT(',',IFNULL(NULLIF(`ticket_types`,''),'$ticket_type'),',') LIKE '%,$ticket_type,%'");
                        while($pdf = $pdfs->fetch_assoc()) { ?>
                            <a href="../Ticket/index.php?custom_form=<?= $pdf['id'] ?>&ticketid=<?= $ticketid > 0 ? $ticketid : '' ?><?= (empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group']) ?>" target="_blank" data-history-label="Create <?= $pdf['pdf_name'] ?> from <?= TICKET_NOUN ?>" class="pull-right btn brand-btn margin-horizontal" data-target="<?= $pdf['target'] ?>" onclick="fillCustomForm(this); return false;"><?= $pdf['pdf_name'] ?></a>
                        <?php } ?>
                    <?php } ?>
                    <?php if(strpos($value_config,',Export Ticket Log,') !== FALSE && !empty($ticketid) && !isset($_GET['intake_key'])) {
                        $ticket_log_template = !empty(get_config($dbc, 'ticket_log_template')) ? get_config($dbc, 'ticket_log_template') : 'template_a'; ?>
                        <a href="../Ticket/ticket_log_templates/<?= $ticket_log_template ?>_pdf.php?ticketid=<?= $ticketid > 0 ? $ticketid : '' ?>" target="_blank" data-history-label="Export <?= TICKET_NOUN ?> Log" class="pull-right btn brand-btn">Export <?= TICKET_NOUN ?> Log</a>
                    <?php } ?>
                    <?php if($access_any && !isset($_GET['intake_key'])) { ?>
                        <a href="<?= $back_url ?>" class="pull-right gap-right"><img class="no-toggle" src="<?= WEBSITE_URL ?>/img/icons/save.png" alt="Save" width="36" data-history-label="Save <?= TICKET_NOUN ?>" title="Save" /></a>
                        <?php if($hide_trash_icon != 1) { ?><a href="<?php echo $back_url; ?>" class="pull-left gap-left" data-history-label="Archive <?= TICKET_NOUN ?>" onclick="<?= strpos($value_config, ',Delete Button Add Note,') ? 'dialogDeleteNote(this); return false;' : 'return archive();' ?>"><img class="no-toggle" src="<?= WEBSITE_URL; ?>/img/icons/ROOK-trash-icon.png" alt="Archive" title="Archive" width="36" /></a><?php } ?>
                    <?php } ?>
                </div>
                <?php if(!empty($admin_group) && !isset($_GET['intake_key'])) {
                    $recipients = [];
                    foreach(explode(',',$admin_group['contactid']) as $admin_recipient) {
                        $recipients[] = get_email($dbc,$admin_recipient);
                    }
                    $body = 'A '.TICKET_NOUN.' has been submitted for approval. Please log in and review it.<br/><br/>
                        <b><a target="_blank" href="'.WEBSITE_URL.'/Ticket/index.php?tab=administration_'.$admin_group['id'].'_pending__'.(empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group']).'">Approvals</a></b><br/>
                        <a target="_blank" href="'.WEBSITE_URL.'/Ticket/index.php?'.(empty($_GET['tile_name']) ? '' : 'tile_name='.$_GET['tile_name'].'&').(empty($_GET['tile_group']) ? '' : 'tile_group='.$_GET['tile_group'].'&').'edit="></a>'; ?>
                    <script>
                    function updateApprovalIDLabel() {
                        $('#approval_submit [name=approval_subject]').val($('.ticketid_span').text()+' has been submitted for approval');
                        $('#approval_submit [name=approval_body]').val($('#approval_submit [name=approval_body]').val().replace(/\?edit=.*/,'?edit='+ticketid+'">'+$('.ticketid_span').text()+'</a>'));
                        tinyMCE.get('approval_body').setContent($('#approval_submit [name=approval_body]').val());
                    }
                    </script>
                    <div class="collapse" id="approval_submit">
                        <div class="clearfix"></div>
                        <h3>Submit for Approval</h3>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Submitter's Name:</label>
                            <div class="col-sm-8">
                                <input type="text" name="approval_name" class="form-control email_sender_name" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Submitter's Address:</label>
                            <div class="col-sm-8">
                                <input type="text" name="approval_email" class="form-control email_sender" value="<?= get_email($dbc, $_SESSION['contactid']) ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Email Subject:</label>
                            <div class="col-sm-8">
                                <input type="text" name="approval_subject" class="form-control email_subject" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Email Body:</label>
                            <div class="col-sm-12">
                                <textarea name="approval_body" class="form-control email_body"><?php echo $body; ?></textarea>
                            </div>
                        </div>
                        <input type="hidden" name="status" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid">
                        <input type="hidden" name="email_recipient" value="<?= implode(';',array_filter($recipients)) ?>">
                        <button class="btn brand-btn pull-right" onclick="send_email(this); $('[name=status]').first().val('<?= $admin_group['status'] ?>').change(); return false;">Send Email</button>
                        <div class="clearfix"></div>
                    </div>
                <?php } ?>
            <?php } ?>
			<div class="double-gap-bottom clearfix"></div>
		</div>
<?php if(empty($_GET['calendar_view'])) { ?>
		</div>
<?php } ?>
</div>
