<?php include_once('../include.php');
// Get the list of ticket types available in the current Ticket tile
$tile_config = '';
$ticket_tabs = $ticket_conf_list = [];
$ticket_tile = '';
$ticket_noun = '';
$ticket_types = explode(',',get_config($dbc, 'ticket_tabs'));
$security = $tile_security = [];
$current_tile = '';
if(!empty($_GET['tile_group'])) {
	checkAuthorised(false,false,'ticket_tile_'.$_GET['tile_group']);
    $tile_config = explode('#*#',get_config($dbc, 'ticket_split_tiles_'.$_GET['tile_group']));
    $ticket_tile = $tile_config[0];
    $ticket_noun = $tile_config[1];
    foreach(explode('|',$tile_config[2]) as $type_id) {
        foreach($ticket_types as $type_name) {
            if($type_id == config_safe_str($type_name)) {
                $ticket_tabs[$type_id] = $type_name;
                $ticket_conf_list[] = $type_id;
            }
        }
    }
    $current_tile = 'tile_group='.$_GET['tile_group'].'&';
    $ticket_type = empty($_GET['type']) ? get_config($dbc, 'default_ticket_type') : filter_var($_GET['type'],FILTER_SANITIZE_STRING);
    if(empty($ticket_tabs[$ticket_type])) {
        $ticket_type = explode('|',$tile_config[2])[0];
    }
    $security = $tile_security = get_security($dbc, 'ticket_tile_'.$_GET['tile_group']);
} else if(!empty($_GET['tile_name'])) {
	checkAuthorised(false,false,'ticket_type_'.$_GET['tile_name']);
    foreach(get_config($dbc, 'ticket_split_tiles_%', true, null) as $tile_group) {
        if(in_array($_GET['tile_name'],explode('|',explode('#*#',$tile_group)[2]))) {
            $tile_config = explode('#*#',$tile_group);
        }
    }
    if(!empty($tile_config)) {
        $ticket_noun = $tile_config[1];
    } else {
        $ticket_noun = TICKET_NOUN;
    }
    foreach($ticket_types as $type_name) {
        if(config_safe_str($type_name) == $_GET['tile_name']) {
            $ticket_tabs[$_GET['tile_name']] = $type_name;
            $ticket_conf_list[] = filter_var($_GET['tile_name'],FILTER_SANITIZE_STRING);
            $ticket_tile = $type_name;
        }
    }
    $current_tile = 'tile_name='.$_GET['tile_name'].'&';
    $ticket_type = filter_var($_GET['tile_name'],FILTER_SANITIZE_STRING);
    $security = $tile_security = get_security($dbc, 'ticket_type_'.$ticket_type);
} else {
	checkAuthorised('ticket');
    foreach(array_filter($ticket_types) as $ticket_tab) {
        $ticket_tabs[config_safe_str($ticket_tab)] = $ticket_tab;
        $ticket_conf_list[] = config_safe_str($ticket_tab);
    }
    $ticket_conf_list[] = '';
    $ticket_tile = TICKET_TILE;
    $ticket_noun = TICKET_NOUN;
    $ticket_type = empty($_GET['type']) ? get_config($dbc, 'default_ticket_type') : filter_var($_GET['type'],FILTER_SANITIZE_STRING);
    $security = $tile_security = get_security($dbc, 'ticket');
}

// Get Security Options
$strict_view = strictview_visible_function($dbc, 'ticket');
$ticket_layout = get_config($dbc, 'ticket_layout');
if($strict_view > 0) {
	$security['edit'] = $tile_security['edit'] = 0;
	$security['config'] = $tile_security['config'] = 0;
}
$db_config = explode(',',get_field_config($dbc, 'tickets_dashboard'));
$ticketid = $_GET['edit'] > 0 ? $_GET['edit'] : 0;