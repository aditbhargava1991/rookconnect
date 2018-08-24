<?php include_once('../include.php');
checkAuthorised('timesheet');
include_once('config.php');
$tab_config = get_config($dbc,'timesheet_tabs').',day_tracking,';

foreach($config['tabs'] as $tab_name => $tab) {
	if(strpos(",$tab_config,",",$tab_name,") === FALSE) {
		unset($config['tabs'][$tab_name]);
	} else if(is_array($tab)) {
		$config['tabs'][$tab_name] = $tab[$default_tab];
	}
}
$config['tabs']['day_tracking'] = 'start_day.php';

ob_clean();
foreach($config['tabs'] as $tab_name => $tab) {
	var_dump($tab);
	if(!empty($tab) && ($_GET['url'] == $tab || !in_array($_GET['url'], $config['tabs']))) {
		header("Location: ".$tab);
		exit();
	}
}
header("Location: time_cards.php");