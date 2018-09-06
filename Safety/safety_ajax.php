<?php include_once('../include.php');
ob_clean();

if($_GET['action'] == 'setting_tabs') {
	set_config($dbc, 'safety_dashboard', implode(',',array_filter($_POST['subtabs'])));
	set_config($dbc, 'safety_bypass_list', implode(',',$_POST['bypass']));
}
else if($_GET['action'] == 'settings_manuals') {
	set_config($dbc, 'safety_manuals_fields', implode(',',$_POST['fields']));
}
else if($_GET['action'] == 'settings_config') {
	set_config($dbc, filter_var($_POST['name'],FILTER_SANITIZE_STRING), htmlentities(filter_var($_POST['value'],FILTER_SANITIZE_STRING)));
}
else if($_GET['action'] == 'setting_sites') {
	set_config($dbc, 'safety_main_site_tabs', implode(',',$_POST['sites']));
} else if($_GET['action'] == 'mark_favourite') {
	$id = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
	$user = filter_var($_GET['user'],FILTER_SANITIZE_STRING);
	echo "UPDATE `safety` SET `favourite`=TRIM(BOTH ',' FROM REPLACE(IF(CONCAT(',',IFNULL(`favourite`,''),',') LIKE '%,$user,%',REPLACE(CONCAT(',',IFNULL(`favourite`,''),','),',$user,',','),CONCAT(IFNULL(`favourite`,''),',$user')),',,',',')) WHERE `safetyid`='$id'";
	mysqli_query($dbc, "UPDATE `safety` SET `favourite`=TRIM(BOTH ',' FROM REPLACE(IF(CONCAT(',',IFNULL(`favourite`,''),',') LIKE '%,$user,%',REPLACE(CONCAT(',',IFNULL(`favourite`,''),','),',$user,',','),CONCAT(IFNULL(`favourite`,''),',$user')),',,',',')) WHERE `safetyid`='$id'");
} else if($_GET['action'] == 'mark_pinned') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$users = filter_var(implode(',',$_POST['users']),FILTER_SANITIZE_STRING);
	echo "UPDATE `safety` SET `pinned`=',$users,' WHERE `safetyid`='$id'";
	mysqli_query($dbc, "UPDATE `safety` SET `pinned`=',$users,' WHERE `safetyid`='$id'");
} else if($_GET['action'] == 'archive') {
	$id = filter_var($_POST['id'], FILTER_SANITIZE_STRING);
	    $date_of_archival = date('Y-m-d');
    mysqli_query($dbc, "UPDATE `safety` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `safetyid`='$id'");
} else if($_GET['action'] == 'form_layout') {
	$safetyid = filter_var($_POST['safetyid'],FILTER_SANITIZE_STRING);
	$form = filter_var($_POST['form'],FILTER_SANITIZE_STRING);
	$field = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
	$value = filter_var(htmlentities($_POST['value']),FILTER_SANITIZE_STRING);
	if(!($safetyid > 0)) {
		mysqli_query($dbc, "INSERT INTO `safety` (`safetyid`, `form`) SELECT '$safetyid', '$form' FROM (SELECT COUNT(*) `rows` FROM `safety` WHERE `safetyid`='$safetyid') `num` WHERE `num`.`rows`=0");
		if(mysqli_insert_id($dbc) > 0) {
			echo mysqli_insert_id($dbc);
			$safetyid = mysqli_insert_id($dbc);
		}

		$before_change = '';
		$history = "Safety entry has been added. <br />";
		add_update_history($dbc, 'safety_history', $history, '', $before_change);
	}
	if(!empty($_POST['user_form_id'])) {
		mysqli_query($dbc, "UPDATE `safety` SET `user_form_id` = '".$_POST['user_form_id']."' WHERE `safetyid` = '$safetyid'");
	}
	if($field != '') {
		mysqli_query($dbc, "UPDATE `safety` SET `$field`='$value' WHERE `safetyid`='$safetyid'");
	}

	$before_change = '';
  $history = "safety_attendance entry has been updated for safetyattid $safetyid <br />";
  add_update_history($dbc, 'safety_history', $history, '', $before_change);
}
