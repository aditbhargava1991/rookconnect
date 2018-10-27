<?php // Only update the tile list in session once every minute
if($_SESSION['tile_list_updated'] + 30 < time() || $_SERVER['PHP_SELF'] == '/Settings/settings.php' || count($_SESSION['tile_list']) == 0 || $force_load_list === true) {
	// This file generates the tiles or links
	include_once('tile_data.php');

	// Get all of the project types in an array
	$project_type_list = explode(',',get_config($dbc,'project_tabs'));
	$project_type_tile_list = [];
	foreach($project_type_list as $project_type_item) {
		$project_type_tile_list[] = 'project#*#'.$project_type_item;
	}
	// Get all of the ticket types in an array
	$ticket_type_tile_list = [];
    $ticket_type_tile_config = get_config($dbc, 'ticket_type_tiles');
	if($ticket_type_tile_config == 'SHOW') {
		$ticket_type_list = array_filter(explode(',',get_config($dbc,'ticket_tabs')));
		foreach($ticket_type_list as $ticket_type_item) {
			$ticket_type_tile_list[] = 'ticket#*#'.$ticket_type_item;
		}
	} else if(!empty($ticket_type_tile_config)) {
        foreach(explode(',',$ticket_type_tile_config) as $ticket_type_item) {
            $ticket_type_tile_list[] = 'ticket#*#'.$ticket_type_item;
        }
    }
    $ticket_tile_groups = $dbc->query("SELECT `name` FROM `general_configuration` WHERE `name` LIKE 'ticket_split_tiles_%'");
    while($ticket_tile_group = $ticket_tile_groups->fetch_assoc()) {
        $ticket_type_tile_list[] = 'ticket_group#*#'.substr($ticket_tile_group['name'],19);
    }
	// Get all of the project workflow types in an array
	$project_workflow_type_list = mysqli_query($dbc, "SELECT `tile_name` FROM `project_workflow`"); //explode(',',get_config($dbc,'project_tabs'));
	$project_workflow_type_tile_list = [];
	while($project_workflow_type_item = mysqli_fetch_array($project_workflow_type_list)['tile_name']) {
		$project_workflow_type_tile_list[] = 'project_workflow#*#'.$project_workflow_type_item;
	}
	$hr_tile_list = [];
	foreach(explode(',',get_config($dbc, 'hr_tiles')) as $hr_tiles) {
		if($hr_tiles != '') {
			$hr_tile_list[] = 'hr#*#'.config_safe_str($hr_tiles);
		}
	}
	// Get all document types in an array
	$documents_all_tile_list = [];
	if(!empty(get_config($dbc, 'documents_all_tiles'))) {
		$documents_all_tiles = array_filter(explode(',',get_config($dbc, 'documents_all_tiles')));
		foreach($documents_all_tiles as $documents_all_tile) {
			if(tile_visible($dbc, 'documents_all_'.config_safe_str($documents_all_tile), ROLE, 'documents_all')) {
				$documents_all_tile_list[] = 'documents_all#*#'.$documents_all_tile;
			}
		}
	}
	// Anything not selected in the User's order list should be displayed at the bottom
	$user_tile_list = @explode('*#*',mysqli_fetch_array(mysqli_query($dbc,"SELECT `tile_sort` FROM `contacts_tile_sort` WHERE `contactid`='".$_SESSION['contactid']."' UNION SELECT `tile_sort` FROM `contacts_tile_sort` LEFT JOIN `contacts` ON `contacts_tile_sort`.`contactid`=`contacts`.`contactid` WHERE `user_name`='FFMAdmin' UNION SELECT '' `tile_sort`"))['tile_sort']);
	// This defines the list of tile names and controls the default order
	// Tiles must be added here to be configurable as off or on
	// if($dbc->query("SELECT * FROM `orientation_staff` WHERE `staffid`='{$_SESSION['contactid']}' AND `start_date` <= DATE(NOW()) AND `completed`=0")->num_rows > 0) {
		// $all_tiles = array('orientation');
	// } else {
	$all_tiles = array_merge([ 'start_day_button',
            'admin_settings',
			'software_config',
			'security',
			'contacts',
			'contacts_inbox',
			'contacts3',
			'client_info',
			'contacts_rolodex',
			'staff',
			'orientation',
			'infogathering',
			'agenda_meeting',
			'sales',
			'certificate',
			'contracts',
			'driving_log',
			'hr',
			'preformance_review'],
		$hr_tile_list,
		[ 'package',
			'promotion',
			'services',
			'products',
			'labour',
			'material',
			'inventory',
			'vpl',
			'assets',
			'equipment',
			'custom',
			'intake',
			'pos',
			'posadvanced',
			'invoicing',
			'service_queue',
			'incident_report',
			'policy_procedure',
			'ops_manual',
			'emp_handbook',
			'how_to_checklist',
			'safety',
			'rate_card',
			'estimate',
			'field_ticket_estimates',
			'project',
			'calendar_rook',
			'interactive_calendar',
			'optimize',
			'properties',
			'training_quiz'],
		$project_type_tile_list,
		[ 'client_projects',
			'project_workflow', ],
		$project_workflow_type_tile_list,
		[ 'shop_work_orders',
			'site_work_orders',
			'ticket' ],
		$ticket_type_tile_list,
		[ 'daysheet',
			'time_tracking',
			'calllog',
			'budget',
			'profit_loss',
			'gao',
			'checklist',
			'tasks',
			'tasks_updated',
			'scrum',
			'communication',
			'communication_schedule',
			'email_communication',
			'phone_communication',
			'punch_card',
			'sign_in_time',
			'payroll',
			'purchase_order',
			'sales_order',
			'newsboard',
			'field_job',
			'expense',
			'payables',
			'billing',
			'report',
			'passwords',
			'gantt_chart',
			'client_documentation',
			'medication',
			'individual_support_plan',
			'social_story',
			'routine',
			'day_program',
			'match',
			'fund_development',
			'how_to_guide',
			'charts',
			'daily_log_notes',
			'timesheet',
			'software_guide',
			'helpdesk',
			'archiveddata',
			'customer_support',
			'demo',
			'ffmsupport',
			'appointment_calendar',
			'booking',
			'check_in',
			'check_out',
			'treatment_charts',
			'accounts_receivables',
			'therapist',
			'exercise_library',
			'notifications',
			'reactivation',
			'goals_compensation',
			'crm',
			'drop_off_analysis',
			'injury',
			'manual',
			'confirm',
			'website',
			'safety_manual',
			'members',
			'non_verbal_communication',
			'form_builder',
			'vendors',
			'quote',
			'cost_estimate',
			'documents_all',
			'dispatch' ],
		$documents_all_tile_list);
	// }
	$user_tile_list = array_unique(array_merge($user_tile_list, $all_tiles));
	$tile_list_temp = [];
	$tile_list_override = true;
	foreach(explode(',',ROLE) as $level) {
		if($level != '') {
			$restrict_dashboards = mysqli_query($dbc, "SELECT `tile_sort` FROM `tile_dashboards` WHERE CONCAT(',',`restrict_levels`,',') LIKE '%,$level,%' AND `deleted`=0");
			if(mysqli_num_rows($restrict_dashboards) > 0) {
				while($restrict_db = mysqli_fetch_assoc($restrict_dashboards)) {
					$tile_list_temp = array_unique(array_merge($tile_list_temp,explode('*#*',$restrict_db['tile_sort'])));
				}
			} else {
				$tile_list_override = false;
			}
		}
	}
	if($tile_list_override) {
		$user_tile_list = $tile_list_temp;
	}

	$pr_fields = ','.get_config($dbc, 'performance_review_fields').',';
	$pr_tile = '';
	if(strpos($pr_fields, ',Use As Tile,') === FALSE) {
		$user_tile_list = array_diff($user_tile_list, ['preformance_review']);
	}

	session_start(['cookie_lifetime' => 518400]);
	$_SERVER['page_load_info'] .= 'Session Started: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
	$_SESSION['tile_list'] = [];

	$detect = new Mobile_Detect;
	$is_mobile = ( $detect->isMobile() ) ? true : false;
	foreach($user_tile_list as $tile_name) {
		if(!is_array($tile_name) && strpos($tile_name,'#*#') !== false) {
			$tile_name = explode('#*#',$tile_name);
		}
		$tile_info = tile_data($dbc, $tile_name, $is_mobile);
		if($tile_info['link'] !== false) {
			if($tile_info['name'] == 'Therapist') {
				$tile_info['name'] = 'Therapists';
			}

			$_SESSION['tile_list'][] = ['tile' => $tile_name, 'label' => 'Tile: '.$tile_info['name'],'key'=>$tile_info['name'],'link' => WEBSITE_URL.'/'.$tile_info['link']];
		}
	}
	$_SESSION['tile_list_updated'] = time();
	session_write_close();
}
if(!$no_display) {
	// This is defined in home.php to create the tile layout, otherwise it uses the list item format of the menus
	$item_start = (empty($item_start) ? '<li>' : $item_start);
	$item_end = (empty($item_end) ? '</li>' : $item_end);

	$dashboard_arr = array_filter(explode('*#*',$dashboard_list));
	if(count($dashboard_arr) == 0) {
		foreach($_SESSION['tile_list'] as $tile_data) {
			echo $item_start.'<a href="'.$tile_data['link'].'">'.$tile_data['key']."</a>".$item_end."\n";
		}
	} else {
		array_unshift($dashboard_arr, 'start_day_button');
		foreach($dashboard_arr as $tile_name) {
			foreach($_SESSION['tile_list'] as $tile_data) {
				if($tile_data['tile'] == $tile_name || $tile_name == implode('#*#',$tile_data['tile'])) {
					echo $item_start.'<a href="'.$tile_data['link'].'">'.$tile_data['key']."</a>".$item_end."\n";
				}
			}
		}
	}
} ?>