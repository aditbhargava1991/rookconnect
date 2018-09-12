<?php $projectid = 0;
if($_GET['edit'] > 0 || isset($_GET['fill_user_form'])) {
    $projectid = filter_var($_GET['edit'],FILTER_SANITIZE_STRING);
    if(isset($_GET['fill_user_form'])) {
        $projectid = $_GET['projectid'];
    }
}
$tile = filter_var($_GET['tile_name'],FILTER_SANITIZE_STRING);
if($tile == '') {
	$tile = 'project';
}
if(!empty($_GET['tile_name'])) {
	checkAuthorised(false,false,'project_'.$_GET['tile_name']);
} else {
	checkAuthorised('project');
}
$project_tabs = ['favourite'=>'Favourite'];
$pending_projects = get_config($dbc, 'project_status_pending');
if($pending_projects != 'disable') {
	$project_tabs['pending'] = 'Pending';
}
if(in_array('All',$project_classify)) {
	$project_tabs['VIEW_ALL'] = 'All '.PROJECT_TILE;
}
foreach(array_filter(explode(',',get_config($dbc, "project_tabs"))) as $type_name) {
	if($tile == 'project' || $tile == config_safe_str($type_name)) {
		$project_tabs[config_safe_str($type_name)] = $type_name;
	}
}
$security = get_security($dbc, $tile);
$strict_view = strictview_visible_function($dbc, 'project');
if($strict_view > 0) {
	$security['edit'] = 0;
	$security['config'] = 0;
}
$tab_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_tabs` FROM field_config_project WHERE type='$projecttype'"))['config_tabs']),explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_tabs` FROM field_config_project WHERE type='ALL'"))['config_tabs']))));
if(count($tab_config) == 0) {
	$tab_config = explode(',','Path,Information,Details,Documents,Dates,Scope,Estimates,Tickets,Work Orders,Tasks,Checklists,Email,Phone,Reminders,Agendas,Meetings,Gantt,Profit,Report Checklist,Billing,Field Service Tickets,Purchase Orders,Invoices');
}