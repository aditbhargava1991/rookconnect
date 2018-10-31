<?php include_once('../include.php');
// Get the list of ticket types available in the current Ticket tile
$tile_config = '';
$ticket_tabs = $ticket_conf_list = [];
$ticket_tile = TICKET_TILE;
$ticket_noun = TICKET_NOUN;
$ticket_types = explode(',',get_config($dbc, 'ticket_tabs'));
$security = $tile_security = [];
$current_tile = ''; ?>
<script>
var tile_name = tile_group = '';
</script>
<?php if(!empty($_GET['tile_group'])) {
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
    if(empty($ticket_type) || !in_array($ticket_type,$ticket_types)) {
        if(count($ticket_types) == 1) {
            $ticket_type = $ticket_types[0];
        }
    }
    if(empty($ticket_tabs[$ticket_type])) {
        $ticket_type = explode('|',$tile_config[2])[0];
    }
    $security = $tile_security = get_security($dbc, 'ticket_tile_'.$_GET['tile_group']); ?>
    <script>
    tile_group = '<?= $_GET['tile_group'] ?>';
    </script>
<?php } else if(!empty($_GET['tile_name'])) {
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
    $security = $tile_security = get_security($dbc, 'ticket_type_'.$ticket_type); ?>
    <script>
    tile_name = '<?= $_GET['tile_name'] ?>';
    </script>
<?php } else {
	checkAuthorised('ticket');
    foreach($ticket_types as $ticket_tab) {
        $ticket_tabs[config_safe_str($ticket_tab)] = $ticket_tab;
        $ticket_conf_list[] = config_safe_str($ticket_tab);
    }
    $ticket_conf_list[] = '';
    $ticket_type = empty($_GET['type']) ? get_config($dbc, 'default_ticket_type') : filter_var($_GET['type'],FILTER_SANITIZE_STRING);
    $security = $tile_security = get_security($dbc, 'ticket');
}

foreach($ticket_tabs as $type_id => $type_name) {
    if(!check_subtab_persmission($dbc, 'ticket', ROLE, 'ticket_type_'.$type_id)) {
        unset($ticket_tabs[$type_id]);
    }
}

// Get Security Options
if(!isset($ticket_tabs[$ticket_type])) {
    $ticket_type = config_safe_str(array_values($ticket_tabs)[0]);
}
$strict_view = strictview_visible_function($dbc, 'ticket');
$ticket_layout = get_config($dbc, 'ticket_layout');
if($strict_view > 0) {
	$security['edit'] = $tile_security['edit'] = 0;
	$security['config'] = $tile_security['config'] = 0;
}
$db_config = explode(',',get_field_config($dbc, 'tickets_dashboard'));
$ticketid = $_GET['edit'] > 0 ? $_GET['edit'] : 0;
$ticket_stop = $_GET['stop'] > 0 ? $_GET['stop'] : 0;
if($ticket_stop > 0) {
    $ticket_status = config_safe_str(get_field_value('status','ticket_schedule','id',$ticket_stop));
    if(empty($ticket_status)) {
        $ticket_status = config_safe_str(get_field_value('status','tickets','ticketid',$ticketid));
    }
} else if($ticketid > 0) {
    $ticket_status = config_safe_str(get_field_value('status','tickets','ticketid',$ticketid));
}
$ticket_next_step_timesheet = array_filter(explode(',',get_config($dbc, 'ticket_next_step_timesheet')));

$update_time = get_config($dbc, 'scheduling_calendar_est_time');
if($update_time == 'auto_sort' && $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { ?>
    <script src="../Calendar/map_sorting.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= DIRECTIONS_KEY ?>"></script>
<?php }
if($ticketid > 0) {
    $incognito_fields = explode(',',get_field_value('incognito_fields','tickets','ticketid',$ticketid));
}