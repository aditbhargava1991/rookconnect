<?php
include_once('../include.php');
include_once('../tcpdf/tcpdf.php');
if(!file_exists('download')) {
	mkdir('download', 0777, true);
}
ob_clean();
date_default_timezone_set('America/Denver');
if(!($_SESSION['contactid'] > 0)) {
	echo "ERROR#*#Your session has timed out. Please log in and try again.";
	exit();
}
if($_GET['fill'] == 'project_path_milestone') {
    $project_path = $_GET['project_path'];
	echo '<option value=""></option>';
    foreach (explode('#*#', get_project_path_milestone($dbc, $project_path, 'milestone')) as $j => $cat_tab) {
        echo "<option value='". $cat_tab."'>".$cat_tab.'</option>';
    }
}

if($_GET['fill'] == 'ticket_security_settings') {
	$security_level = $_POST['security_level'];
	$subtabs_hidden = implode(',', $_POST['subtabs_hidden']);
	$subtabs_viewonly = implode(',', $_POST['subtabs_viewonly']);
	$fields_hidden = implode(',', $_POST['fields_hidden']);
	$fields_viewonly = implode(',', $_POST['fields_viewonly']);

	$num_rows = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `field_config_ticket_security` WHERE `security_level` = '$security_level'"))['num_rows'];
	if($num_rows > 0) {
		mysqli_query($dbc, "UPDATE `field_config_ticket_security` SET `subtabs_hidden` = '$subtabs_hidden', `subtabs_viewonly` = '$subtabs_viewonly', `fields_hidden` = '$fields_hidden', `fields_viewonly` = '$fields_viewonly' WHERE `security_level` = '$security_level'");
	} else {
		mysqli_query($dbc, "INSERT INTO `field_config_ticket_security` (`security_level`, `subtabs_hidden`, `subtabs_viewonly`, `fields_hidden`, `fields_viewonly`) VALUES ('$security_level', '$subtabs_hidden', '$subtabs_viewonly', '$fields_hidden', '$fields_viewonly')");
	}
}

if($_GET['fill'] == 'project_paths') {
	echo '<option value=""></option>';
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	foreach(explode(',',get_project($dbc, $projectid, 'project_path')) as $pathid) {
		if($pathid > 0) {
			$milestones = explode('#*#',get_field_value('milestone','project_path_milestone','project_path_milestone',$pathid));
			$prior_sort = 0;
			foreach($milestones as $i => $milestone) {
				$milestone_rows = $dbc->query("SELECT `sort` FROM `project_path_custom_milestones` WHERE `projectid`='$projectid' AND `milestone`='$milestone' AND `pathid`='$pathid' AND `path_type`='I'");
				if($milestone_rows->num_rows > 0) {
					$prior_sort = $milestone_rows->fetch_assoc()['sort'];
				} else if($milestone != 'Unassigned') {
					$dbc->query("INSERT INTO `project_path_custom_milestones` (`projectid`,`milestone`,`label`,`path_type`,`pathid`,`sort`) VALUES ('$projectid','$milestone','$milestone','I','$pathid','$prior_sort')");
				}
			}
		}
	}
	$milestone_list = $dbc->query("SELECT `milestones`.`id`, `milestones`.`milestone`, `milestones`.`label`, `milestones`.`sort`  FROM `project_path_custom_milestones` `milestones` LEFT JOIN `project` ON `milestones`.`projectid`=`project`.`projectid` AND CONCAT(',',`project`.`project_path`,',') LIKE CONCAT('%,',`milestones`.`pathid`,',%') WHERE `project`.`projectid`='$projectid' AND `milestones`.`path_type`='I' AND `milestones`.`deleted`=0 ORDER BY `milestones`.`pathid`,`milestones`.`path_type`,`milestones`.`sort`,`milestones`.`id`");
	while($milestone_row = $milestone_list->fetch_assoc()) {
		echo "<option ".($milestone_timeline == $milestone_row['milestone'] ? 'selected' : '')." value='". $milestone_row['milestone']."'>".$milestone_row['label'].'</option>';
	}
}
if($_GET['fill'] == 'client_project_path_milestone') {
    $project_path = $_GET['project_path'];
	echo '<option value=""></option>';
    $each_tab = explode('#*#', get_client_project_path_milestone($dbc, $project_path, 'milestone'));
    $timeline = explode('#*#', get_client_project_path_milestone($dbc, $project_path, 'timeline'));
    $j=0;
    foreach ($each_tab as $cat_tab) {
        echo "<option value='". $cat_tab."'>".$cat_tab.' : '.$timeline[$j].'</option>';
        $j++;
    }
}

if($_GET['fill'] == 'task_path_milestone') {
    $task_path = $_GET['task_path'];
	echo '<option value=""></option>';
    $each_tab = explode('#*#', get_project_path_milestone($dbc, $task_path, 'milestone'));
    $timeline = explode('#*#', get_project_path_milestone($dbc, $task_path, 'timeline'));
    $j=0;
    foreach ($each_tab as $cat_tab) {
        echo "<option value='". $cat_tab."'>".$cat_tab.' : '.$timeline[$j].'</option>';
        $j++;
    }
}

if($_GET['fill'] == 'taskassigncontact') {
	$businessid = $_GET['businessid'];

	$query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE businessid = '$businessid'");
	echo '<option value="">Please Select</option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['contactid']."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
	}
}

if($_GET['fill'] == 'client_projectname') {
	$clientid = explode(',',$_GET['clientid']);

	$project_list = [];
	foreach($clientid as $client) {
		$query = mysqli_query($dbc,"SELECT projectid, project_name FROM client_project WHERE CONCAT(',',clientid,',') LIKE '%,$client,%' and deleted=0");

		while($row = mysqli_fetch_array($query)) {
			$this_array = [$row['projectid'],$row['project_name']];
			if(!in_array($this_array, $project_list)) {
				$project_list[] = $this_array;
			}
		}
	}
	echo '<option value="">Please Select</option>';
	foreach($project_list as $project) {
		echo "<option value='".$project[0]."'>".$project[1]."</option>\n";
	}
}

if($_GET['fill'] == 'projectname') {
	$clientid = explode(',',$_GET['clientid']);

	$project_list = [];
	foreach($clientid as $client) {
		$query = mysqli_query($dbc,"SELECT projectid, project_name FROM project WHERE CONCAT(',',clientid,',') LIKE '%,$client,%' and deleted=0");

		while($row = mysqli_fetch_array($query)) {
			$this_array = [$row['projectid'],$row['project_name']];
			if(!in_array($this_array, $project_list)) {
				$project_list[] = $this_array;
			}
		}
	}
	echo '<option value="">Please Select</option>';
	foreach($project_list as $project) {
		echo "<option value='".$project[0]."'>".$project[1]."</option>\n";
	}
}

if($_GET['fill'] == 'get_projectname') {
	$businessid = $_GET['businessid'];

    $query = mysqli_query($dbc,"SELECT * FROM (SELECT projectid, project_name FROM project WHERE businessid = '$businessid' and deleted=0 UNION SELECT CONCAT('C',`projectid`), `project_name` FROM `client_project` WHERE `clientid`='$businessid' AND `deleted`=0) PROJECTS");
    echo '<option value="">Please Select A Project</option>';
    while($row = mysqli_fetch_array($query)) {
        echo "<option value='".$row['projectid']."'>".$row['project_name']."</option>\n";
    }
}

if($_GET['fill'] == 'get_project_business') {
	$clientid   = $_GET['clientid'];
    $businessid = '';

    $query = mysqli_query($dbc,"SELECT * FROM (SELECT `projectid`, `project_name` FROM `project` WHERE `clientid` = '$clientid' AND `deleted`=0 UNION SELECT CONCAT('C',`projectid`), `project_name` FROM `client_project` WHERE `clientid`='$clientid' AND `deleted`=0) PROJECTS");
    echo '<option value="">Please Select A Project</option>';
    while($row = mysqli_fetch_array($query)) {
        echo "<option value='".$row['projectid']."'>".$row['project_name']."</option>\n";
    }

    echo '**##**';

    $query = mysqli_query ( $dbc, "SELECT `businessid` FROM `contacts` WHERE `contactid`='$clientid'" );
    while ( $row=mysqli_fetch_array($query) ) {
        $businessid = $row['businessid'];
    }

    if ( !empty($businessid) ) {
        $query = mysqli_query ( $dbc, "SELECT `contactid`, `name` FROM `contacts` WHERE `contactid`='$businessid' AND `deleted`=0" );
        echo '<option value=""></option>';
        while ( $row=mysqli_fetch_array($query) ) {
            $selected = ( $businessid==$row['contactid'] ) ? 'selected="selected"' : '';
            echo '<option '. $selected .' value="'. $row['contactid'] .'">'. decryptIt($row['name']) .'</option>';
        }
    }
}

if($_GET['fill'] == 'deletetask') {
	$tasklistid = $_GET['tasklistid'];
	$query = mysqli_query($dbc,"DELETE FROM tasklist WHERE tasklistid='$tasklistid'");
}

if($_GET['fill'] == 'milestone') {
	$projectid = $_GET['projectid'];
	if(substr($projectid,0,1) == 'C') {
		$project_path = get_client_project($dbc, $projectid, 'project_path');

		echo '<option value=""></option>';
		$each_tab = explode('#*#', get_client_project_path_milestone($dbc, $project_path, 'milestone'));
		$timeline = explode('#*#', get_client_project_path_milestone($dbc, $project_path, 'timeline'));
		$j=0;
		foreach ($each_tab as $cat_tab) {
			echo "<option value='". $cat_tab."'>".$cat_tab.' : '.$timeline[$j].'</option>';
			$j++;
		}
	} else {
		$project_path = get_project($dbc, $projectid, 'project_path');

		echo '<option value=""></option>';
		$each_tab = explode('#*#', get_project_path_milestone($dbc, $project_path, 'milestone'));
		$timeline = explode('#*#', get_project_path_milestone($dbc, $project_path, 'timeline'));
		$j=0;
		foreach ($each_tab as $cat_tab) {
			echo "<option value='". $cat_tab."'>".$cat_tab.' : '.$timeline[$j].'</option>';
			$j++;
		}
	}
}

if($_GET['fill'] == 'ticketservice') {
	$service_category = $_GET['service_category'];
	$service_category = str_replace("__","&",$service_category);

	$query = mysqli_query($dbc,"SELECT DISTINCT(`service_type`) FROM `services` WHERE REPLACE(`category`, ' ', '') = '$service_category' ORDER BY `category`");
	echo '<option value="">Please Select</option>';
	while($row = mysqli_fetch_array($query)) {
        echo "<option value='".$row['service_type']."'>".$row['service_type'].'</option>';
	}
}

if($_GET['fill'] == 'ticketheading') {
	$service_type = $_GET['service_type'];
	$service_type = str_replace("__","&",$service_type);

	$service_category = $_GET['service_category'];
	$service_category = str_replace("__","&",$service_category);

	$query = mysqli_query($dbc,"SELECT serviceid, heading FROM services WHERE REPLACE(`service_type`, ' ', '') = '$service_type' AND REPLACE(`category`, ' ', '') = '$service_category'");
	echo '<option value="">Please Select</option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['serviceid']."'>".$row['heading'].'</option>';
	}
}

if($_GET['fill'] == 'ticketdesc') {
	$serviceids = $_POST['serviceids'];
	if(!is_array($serviceids)) {
		$serviceids = [$serviceids];
	}

	$service_descs = [];
	foreach($serviceids as $serviceid) {
		$get_service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading`, `ticket_description` FROM `services` WHERE `serviceid` = '$serviceid'"));
		$service_descs[$serviceid] = ['heading'=>$get_service['heading'], 'description'=>$get_service['ticket_description']];
	}

	$ticket_desc = '';
	foreach($service_descs as $service_desc) {
		if(count($service_descs) > 1) {
			$ticket_desc .= '<b>'.$service_desc['heading'].':</b><br>';
		}
		$ticket_desc .= html_entity_decode($service_desc['description']).'<br><br>';
	}

	echo rtrim($ticket_desc,'<br>');
}

if($_GET['fill'] == 'tasklistreassignstaff') {
	$tasklistid = $_GET['tasklistid'];
	$contactid = $_GET['contactid'];
	$query_update_project = "UPDATE `tasklist` SET  contactid='$contactid' WHERE `tasklistid` = '$tasklistid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);
}

if($_GET['fill'] == 'tasklistboard') {
	$tasklistid = $_GET['tasklistid'];
	$category = $_GET['category'];
	$query_update_project = "UPDATE `tasklist` SET  category='$category' WHERE `tasklistid` = '$tasklistid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);
}

if($_GET['fill'] == 'tasklistreassignstatus') {
	$tasklistid = $_GET['tasklistid'];
	$status = $_GET['status'];
	$query_update_project = "UPDATE `tasklist` SET  status='$status' WHERE `tasklistid` = '$tasklistid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);
}

if($_GET['fill'] == 'tasklistaddtime') {
	$tasklistid = $_GET['tasklistid'];
	$work_time = $_GET['worktime'];
	$query_update_project = "UPDATE `tasklist` SET  work_time='$work_time' WHERE `tasklistid` = '$tasklistid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);
}

if($_GET['fill'] == 'gochecklist') {
	$ticketid = $_GET['ticketid'];
	$checklist = filter_var($_GET['checklist'],FILTER_SANITIZE_STRING);
    echo $query_insert_ca = "INSERT INTO `ticket_checklist` (`ticketid`, `checklist`) VALUES ('$ticketid', '$checklist')";
    $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
}

if($_GET['fill'] == 'checklistdone') {
	$ticketchecklistid = $_GET['ticketchecklistid'];
	$query_update_project = "UPDATE `ticket_checklist` SET  checked=1 WHERE `ticketchecklistid` = '$ticketchecklistid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);
}

if($_GET['fill'] == 'starttickettimer') {
	$ticketid = $_GET['ticketid'];
    $start_time = time();
    $start_timer_time = date('g:i A');
	$created_date = date('Y-m-d H:i:s');
    $created_by = $_GET['login_contactid'];

    $running_tickets = mysqli_fetch_all(mysqli_query($dbc, "SELECT tt.* FROM `ticket_timer` tt LEFT JOIN `tickets` ti ON tt.`ticketid` = ti.`ticketid` WHERE tt.`created_by` = '$created_by' AND tt.`start_timer_time` > 0 AND ti.`deleted` = 0 AND ti.`status` != 'Archive' AND tt.`deleted` = 0"),MYSQLI_ASSOC);
    foreach ($running_tickets as $running_ticket) {
    	$tickettimerid = $running_ticket['tickettimerid'];
    	if(empty($running_ticket['timer']) && empty($running_ticket['end_time'])) {
	    	$timer = gmdate('H:i:s', strtotime(date('Y-m-d H:i:s')) - $running_ticket['start_timer_time']);
	    	$end_time = date('g:i A');
		    $query_update_ticket = "UPDATE `ticket_timer` SET `end_time` = '$end_time', `timer` = '$timer' WHERE `tickettimerid` = '$tickettimerid'";
		    $result_update_ticket = mysqli_query($dbc, $query_update_ticket);
    	}
	    $query_update_ticket = "UPDATE `ticket_timer` SET `start_timer_time`='0' WHERE `tickettimerid` = '$tickettimerid'";
	    $result_update_ticket = mysqli_query($dbc, $query_update_ticket);
	}
    $query_insert_client_doc = "INSERT INTO `ticket_timer` (`ticketid`, `timer_type`, `start_time`, `created_date`, `created_by`, `start_timer_time`) VALUES ('$ticketid', 'Work', '$start_timer_time', '$created_date', '$created_by', '$start_time')";
    $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
}

if($_GET['fill'] == 'pausetickettimer') {
	$ticketid = $_GET['ticketid'];
    $timer = $_GET['timer_value'];
    $start_time = time();
    $created_date = date('Y-m-d H:i:s');
    $created_by = $_GET['login_contactid'];
    $end_time = date('g:i A');
    if($timer != '0' && $timer != '00:00:00' && $timer != '') {
        $query_update_ticket = "UPDATE `ticket_timer` SET `end_time` = '$end_time', `timer` = '$timer' WHERE `ticketid` = '$ticketid' AND created_by='$created_by' AND timer_type='Work' AND end_time IS NULL";
        $result_update_ticket = mysqli_query($dbc, $query_update_ticket);
		echo insert_day_overview($dbc, $created_by, 'Ticket', date('Y-m-d'), '', "Updated ".TICKET_NOUN." #$ticketid - Added Time : $timer");

        $query_insert_client_doc = "INSERT INTO `ticket_timer` (`ticketid`, `timer_type`, `start_time`, `created_date`, `created_by`, `start_timer_time`) VALUES ('$ticketid', 'Break', '$end_time', '$created_date', '$created_by', '$start_time')";
        $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
    }
}

if($_GET['fill'] == 'stoptickettimer') {
	$ticketid = $_GET['ticketid'];
    $timer = $_GET['timer_value'];
    $start_time = time();
    $created_date = date('Y-m-d H:i:s');
    $created_by = $_GET['login_contactid'];
    $end_time = date('g:i A');
    if($timer != '0' && $timer != '00:00:00' && $timer != '') {
        $query_update_ticket = "UPDATE `ticket_timer` SET `end_time` = '$end_time', `timer` = '$timer' WHERE `ticketid` = '$ticketid' AND created_by='$created_by' AND end_time IS NULL";
        $result_update_ticket = mysqli_query($dbc, $query_update_ticket);
        $query_update_ticket = "UPDATE `ticket_timer` SET `start_timer_time`='0' WHERE `ticketid` = '$ticketid' AND created_by='$created_by' AND `start_timer_time` > 0";
        $result_update_ticket = mysqli_query($dbc, $query_update_ticket);
        mysqli_query($dbc, "INSERT INTO `time_cards` (`ticketid`,`staff`,`date`,`type_of_time`,`total_hrs`,`timer_tracked`,`comment_box`) VALUES ('$ticketid','$created_by','$created_date','Regular Hrs.','".((strtotime($timer) - strtotime('00:00:00')) / 3600)."','0','Time Added on Ticket #$ticketid')");
		echo insert_day_overview($dbc, $created_by, 'Ticket', date('Y-m-d'), '', "Updated ".TICKET_NOUN." #$ticketid - Added Time : $timer");
    }
	$ticket = $dbc->query("SELECT `pickup_address`, `pickup_city`, `pickup_postal_code`, `to_do_date`, `to_do_start_time` FROM `tickets` WHERE `ticketid`='$ticketid'")->fetch_assoc();
	$address = implode(', ',[$ticket['pickup_address'],$ticket['pickup_city'],$ticket['pickup_postal_code']]);
	if(trim($address,', ') != '') {
		if($next_address = $dbc->query("SELECT * FROM (SELECT `pickup_address`, `pickup_city`, `pickup_postal_code` FROM `tickets` WHERE `to_do_date`='{$ticket['to_do_date']}' AND `to_do_start_time` > '{$ticket['to_do_start_time']}' WHERE `deleted`=0 UNION SELECT `address`, `city`, `province` FROM `ticket_schedule` WHERE `to_do_date`='{$ticket['to_do_date']}' AND `to_do_start_time` > '{$ticket['to_do_start_time']}' WHERE `deleted`=0) `addresses` ORDER BY `to_do_start_time` ASC")) {
			$next_address = implode(', ',$next_address);
			if(trim($next_address,', ') != '') {
				$eta = $data = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=".urlencode($address)."&destinations=".urlencode($next_address)."&language=en-EN&sensor=false"));
				$eta_time = 0;
				$eta_dist = 0;
				foreach($eta->rows[0]->elements as $road) {
					$eta_time += $road->duration->value / 3600;
					$eta_dist += $road->distance->value / 1000;
				}
				$dbc->query("UPDATE `tickets` SET `est_distance`='$eta_dist', `est_time`='$eta_time', `completed_time`=NOW() WHERE `ticketid`='$ticketid'");
			}
		}
	}
}

if($_GET['fill'] == 'resumetickettimer') {
	$ticketid = $_GET['ticketid'];
    $timer = $_GET['timer_value'];
    $start_time = time();
    $created_date = date('Y-m-d H:i:s');
    $created_by = $_GET['login_contactid'];
    $end_time = date('g:i A');
    if($timer != '0' && $timer != '00:00:00' && $timer != '') {
        $query_update_ticket = "UPDATE `ticket_timer` SET `end_time` = '$end_time', `timer` = '$timer' WHERE `ticketid` = '$ticketid' AND created_by='$created_by' AND timer_type='Break' AND end_time IS NULL";
        $result_update_ticket = mysqli_query($dbc, $query_update_ticket);

        $query_insert_client_doc = "INSERT INTO `ticket_timer` (`ticketid`, `timer_type`, `start_time`, `created_date`, `created_by`, `start_timer_time`) VALUES ('$ticketid', 'Work', '$end_time', '$created_date', '$created_by', '$start_time')";
        $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
    }
}

if($_GET['fill'] == 'update_ticket_status') {
    $ticketid = $_GET['ticketid'];
    $status = $_GET['status'];
	$query_update_employee = "UPDATE `tickets` SET status = '$status', `status_date`=CURDATE() WHERE ticketid='$ticketid'";
	$result_update_employee = mysqli_query($dbc, $query_update_employee);
	if($status == 'Archive') {
		$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'"));
		$ticket_config = get_field_config($dbc, 'tickets');
		if($ticket['ticket_type'] != '') {
			$ticket_config .= ','.get_config($dbc, 'ticket_fields_'.$ticket['ticket_type']).',';
		}
		if(strpos($ticket_config,',Send Archive Email,') !== FALSE) {
			$ticket_label = get_ticket_label($dbc, $ticket);
			foreach(explode(',',$ticket['contactid'].','.$ticket['internal_qa_contactid'].','.$ticket['deliverable_contactid']) as $staffid) {
				if($staffid > 0) {
					$email = get_email($dbc, $staffid);
					if($email != '') {
						$subject = $ticket_label." has been Archived";
						$body = "You are receiving this email because you were involved in $ticket_label, and it has been archived.<br />
							To review this ".TICKET_NOUN.", <a href='".WEBSITE_URL."/Ticket/index.php?edit=".$ticket['ticketid']."&tile_name=".$ticket['ticket_type']."'>click here</a>.";
						send_email('', $email, '', '', $subject, $body);
					}
				}
			}
		}
	}
}


if($_GET['fill'] == 'update_ticket_mt') {
    $ticketid = $_GET['ticketid'];
    $milestone_timeline = $_GET['mt'];
	$query_update_employee = "UPDATE `tickets` SET milestone_timeline = '$milestone_timeline' WHERE ticketid='$ticketid'";
	$result_update_employee = mysqli_query($dbc, $query_update_employee);
}


if($_GET['fill'] == 'book_ticket') {
	$ticketid = $_GET['ticketid'];
	$contactid = $_GET['contactid'];
	$is_booked = $_GET['is_booked'];
	$contact_category = get_contact($dbc, $contactid, 'category');

	if($is_booked == 1) {
	        $date_of_archival = date('Y-m-d');
	mysqli_query($dbc, "UPDATE `ticket_attached` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `ticketid` = '$ticketid' AND `item_id` = '$contactid'");
	} else {
		$contact_exists = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `ticket_attached` WHERE `ticketid` = '$ticketid' AND `item_id` = '$item_id'"))['num_rows'];
		if($contact_exists > 0) {
			mysqli_query($dbc, "UPDATE `ticket_attached` SET `deleted` = 0 WHERE `ticketid` = '$ticketid' AND `item_id` = '$item_id'");
		} else {
			mysqli_query($dbc, "INSERT INTO `ticket_attached` (`ticketid`, `src_table`, `item_id`) VALUES ('$ticketid', '$contact_category', '$contactid')");
		}
	}
}

if($_GET['action'] == 'add_pieces') {
	if($_POST['ticketid'] > 0 && $_POST['count'] > 0) {
		$ticketid = $_POST['ticketid'];
		$count = $_POST['count'];
		$units = filter_var($_POST['units'],FILTER_SANITIZE_STRING);
		$current = $dbc->query("SELECT COUNT(*) FROM `ticket_attached` WHERE `src_table`='inventory_general' AND IFNULL(`description`,'') != 'import' AND `deleted`=0 AND `ticketid` > 0 AND `ticketid`='$ticketid'")->fetch_array()[0];
		if($count > $current) {
			for(; $current < $count; $current++) {
				$dbc->query("INSERT INTO `ticket_attached` (`ticketid`,`src_table`,`weight_units`) VALUES ('$ticketid','inventory_general','$units')");
			}
		} else if($count < $current) {
			for(; $current > $count; $current--) {
			    $date_of_archival = date('Y-m-d');
    		$dbc->query("UPDATE `ticket_attached` LEFT JOIN (SELECT MAX(`id`) `id` FROM `ticket_attached` WHERE `deleted`=0 AND `src_table`='inventory_general' AND `ticketid`='$ticketid') `top_id` ON `ticket_attached`.`id`=`top_id`.`id` SET `ticket_attached`.`deleted`=1, `ticket_attached`.`date_of_archival` = '$date_of_archival' WHERE `top_id`.`id` IS NOT NULL");
			}
		}
	}
}
if($_GET['action'] == 'update_fields') {
	$table_name = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
	$field_name = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$value = filter_var(htmlentities($_POST['value']),FILTER_SANITIZE_STRING);
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$id_field = filter_var($_POST['id_field'],FILTER_SANITIZE_STRING);
	$ticketid = filter_var($_POST['ticketid'],FILTER_SANITIZE_STRING);
	$type = filter_var($_POST['type'],FILTER_SANITIZE_STRING);
	$type_field = filter_var($_POST['type_field'],FILTER_SANITIZE_STRING);
	$attach = filter_var($_POST['attach'],FILTER_SANITIZE_STRING);
	$attach_field = filter_var($_POST['attach_field'],FILTER_SANITIZE_STRING);
	$detail = filter_var($_POST['detail'],FILTER_SANITIZE_STRING);
	$detail_field = filter_var($_POST['detail_field'],FILTER_SANITIZE_STRING);
	$manual_value = filter_var($_POST['manually_set'],FILTER_SANITIZE_STRING);
	$manual_field = filter_var($_POST['manual_field'],FILTER_SANITIZE_STRING);
	$ticket_history_addition = '';

    //Insert into Time Sheet tile
    // mysqli_query($dbc, "INSERT INTO `time_cards` (`ticketid`,`staff`,`date`,`type_of_time`,`total_hrs`,`timer_tracked`,`comment_box`) VALUES ('$ticketid','$attach','".date('Y-m-d')."','Regular Hrs.','".((strtotime($value) - strtotime('00:00:00')) / 3600)."','0','Time Added on Ticket #$ticketid')");

	$value_config = ','.get_field_config($dbc, 'tickets').',';
	if($ticketid > 0) {
		$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM tickets WHERE ticketid='$ticketid'"));
		$ticket_type = $get_ticket['ticket_type'];
	}
	if($ticket_type == '') {
		$ticket_type = get_config($dbc, 'default_ticket_type');
	}
	if(!empty($ticket_type)) {
		$value_config .= get_config($dbc, 'ticket_fields_'.$ticket_type).',';
		if(strpos($value_config,',Time Tracking Edit Past Date') !== FALSE && $get_ticket['to_do_date'] != '') {
			$_POST['date'] = $get_ticket['to_do_date'];
		}
	}

	if($field_name == 'status') {
		$current_history_value = mysqli_fetch_assoc(mysqli_query($dbc, "select history from tickets where ticketid = $id"));
		$current_history = $current_history_value['history'];
		$changer = get_contact($dbc, $_SESSION['contactid']);
		$history = $current_history . "<b>$changer</b> has Changed the status to $value on " . date("Y-m-d H:m:s") . "<br>";
		mysqli_query($dbc, "UPDATE tickets set history = '$history' where ticketid = $id");
		if($value == 'Archive') {
			$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$id'"));
			$ticket_config = get_field_config($dbc, 'tickets');
			if($ticket['ticket_type'] != '') {
				$ticket_config .= ','.get_config($dbc, 'ticket_fields_'.$ticket['ticket_type']).',';
			}
			if(strpos($ticket_config,',Send Archive Email,') !== FALSE) {
				$ticket_label = get_ticket_label($dbc, $ticket);
				foreach(explode(',',$ticket['contactid'].','.$ticket['internal_qa_contactid'].','.$ticket['deliverable_contactid']) as $staffid) {
					if($staffid > 0) {
						$email = get_email($dbc, $staffid);
						if($email != '') {
							$subject = $ticket_label." has been Archived";
							$body = "You are receiving this email because you were involved in $ticket_label, and it has been archived.<br />
								To review this ".TICKET_NOUN.", <a href='".WEBSITE_URL."/Ticket/index.php?edit=".$ticket['ticketid']."&tile_name=".$ticket['ticket_type']."'>click here</a>.";
							send_email('', $email, '', '', $subject, $body);
						}
					}
				}
			}
		}
		if(!empty($_POST['auto_create_unscheduled']) && !empty($value) && strpos($_POST['auto_create_unscheduled'], ','.$value.',') !== FALSE) {
			$latest_ticket_schedule = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `ticketid` = '".$ticketid."' WHERE `deleted` = 0 ORDER BY `sort` DESC"));
			mysqli_query($dbc, "INSERT INTO `ticket_schedule` (`ticketid`, `type`, `location_name`, `client_name`, `address`, `city`, `province`, `postal_code`, `country`, `map_link`, `details`, `email`, `carrier`, `vendor`, `lading_number`, `volume`, `order_number`, `sort`, `warehouse_location`, `container`, `manifest_num`) SELECT `ticketid`, `type`, `location_name`, `client_name`, `address`, `city`, `province`, `postal_code`, `country`, `map_link`, `details`, `email`, `carrier`, `vendor`, `lading_number`, `volume`, `order_number`, (`sort` + 1), `warehouse_location`, `container`, `manifest_num` FROM `ticket_schedule` WHERE `ticketid` = '".$ticketid."' AND `deleted` = 0 ORDER BY `sort` DESC LIMIT 1");
			echo 'created_unscheduled_stop';
		}
	}
	if($table_name == 'mileage' && ($field_name == 'start' || $field_name == 'end')) {
		$value = date('Y-m-d H:i:s', strtotime($value));
	}
	if($table_name == 'ticket_comment' && $type == 'member_note') {
		$table_name = 'client_daily_log_notes';
		$id_field = 'note_id';
		if($field_name == 'comment') {
			$field_name = 'note';
		} else if($field_name == 'email_comment') {
			$field_name = 'client_id';
			$value = trim($value,',');
		}
	}
	if($table_name == 'ticket_schedule' && ($field_name == 'to_do_start_time' || $field_name == 'start_available' || $field_name == 'end_available')) {
		$value = date('H:i:s', strtotime($value));
	}

	if(!($id > 0)) {
		$extra_id_info = '';
		if($table_name == 'ticket_attached' && $type == 'Staff_Tasks' && $_POST['extra'] == 'true' && $_POST['extra_id'] == $ticketid) {
			$config = get_field_config($dbc, 'tickets').get_config($dbc, 'ticket_fields_'.mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `ticket_type` FROM `tickets` WHERE `ticketid`='$ticketid'"))['ticket_type']).',';
			$projectid = '`projectid`';
			$available = '';
			$task_list = $_POST['task_list'];
			if(!is_array($task_list)) {
				$task_list = [$task_list];
			}
			foreach($task_list as $key => $task_list_extra) {
				$task_list[$key] = explode('|',$task_list_extra)[0];
			}
			$available = implode(',', $task_list);
			if(strpos($config,',Ticket Tasks Groups,') !== FALSE) {
				$ticket = $dbc->query("SELECT `projectid`, `ticket_type` FROM `tickets` WHERE `ticketid`='$ticketid'")->fetch_assoc();
				$projectid = $ticket['projectid'];
				$task_list = explode('#*#', get_config($dbc, 'ticket_'.(isset($ticket['ticket_type']) ? $ticket['ticket_type'] : 'ALL').'_staff_tasks') ?: $task_groups = get_config($dbc, 'site_work_order_tasks'));
				$project_type = '';
				foreach($task_list as $task_row) {
					$task_row = explode('*#*', $task_row);
					$this_type = $task_row[0];
					unset($task_row[0]);
					foreach($task_row as $task_item) {
						if($task_item == $attach) {
							$project_type = config_safe_str($this_type);
							if(!empty($available)) {
								$available = implode(',',$task_row);
							}
						}
					}
				}
				$project = $dbc->query("SELECT MAX(`p`.`projectid`) `projectid`, `cp`.`businessid` FROM `project` `cp` LEFT JOIN `project` `p` ON `p`.`businessid`=`cp`.`businessid` AND `p`.`projecttype`='$project_type' AND `p`.`siteid`=`cp`.`siteid` WHERE `cp`.`projectid`='$projectid'")->fetch_assoc();
				$business = $project['businessid'];
				$old_projectid = $projectid;
				$projectid = $project['projectid'];
				if(!($projectid > 0)) {
					$dbc->query("INSERT INTO `project` (`projecttype`, `businessid`, `siteid`, `clientid`, `ratecardid`, `project_name`, `created_date`, `created_by`, `start_date`, `approved_date`, `status`, `project_lead`) SELECT '$project_type', `businessid`, `siteid`, `clientid`, `ratecardid`, `project_name`, '".date('Y-m-d')."', '".$_SESSION['contactid']."', '".date('Y-m-d')."', '".date('Y-m-d')."', `status`, `project_lead` FROM `project` WHERE `projectid`='$old_projectid'");
					$projectid = $dbc->insert_id;
				}
			}
			if(strpos($config,',Extra Billing Create New,') !== FALSE && $_POST['extra_ticket_inserted'] != '1') {
				mysqli_query($dbc, "INSERT INTO `tickets` (`ticket_type`, `category`, `businessid`, `clientid`, `siteid`, `projectid`, `salesorderid`, `piece_work`, `heading`, `project_path`, `milestone_timeline`, `task_available`, `to_do_date`, `to_do_end_date`, `created_date`, `created_by`, `status`, `region`, `classification`, `con_location`)
					SELECT `ticket_type`, `category`, `businessid`, `clientid`, `siteid`, $projectid, `salesorderid`, `piece_work`, '-".$attach."-".date('Y-m-d')."', `project_path`, `milestone_timeline`, '$available', '".date('Y-m-d')."', '".date('Y-m-d')."', '".date('Y-m-d')."', '".$_SESSION['contactid']."', `status`, `region`, `classification`, `con_location` FROM `tickets` WHERE `ticketid`='$ticketid'");
				$ticketid = mysqli_insert_id($dbc);
				$dbc->query("INSERT INTO `ticket_comment` (`ticketid`,`type`,`comment`,`created_date`,`created_by`) VALUES ('$ticketid','service_extra_billing','".implode(', ',$available)." added.',DATE(NOW()),'".$_SESSION['contactid']."')");
				if(strpos($config,',Ticket Tasks Ticket Type,') !== FALSE) {
					$task_group = $_POST['task_group'];
					mysqli_query($dbc, "UPDATE `tickets` SET `ticket_type` = '".config_safe_str($task_group)."' WHERE `ticketid` = '$ticketid'");
				}
				$extra_id_info .= '|extra|'.$ticketid;
			}
		}
		mysqli_query($dbc, "INSERT INTO `$table_name` (`ticketid`) VALUES ('$ticketid')");
		$id = mysqli_insert_id($dbc);
		if($attach_field != '' && !($id > 0)) {
			mysqli_query($dbc, "INSERT INTO `$table_name` (`$attach_field`) VALUES ('$attach')");
			$id = mysqli_insert_id($dbc);
		} else if(!($id > 0)) {
			mysqli_query($dbc, "INSERT INTO `$table_name` () VALUES ()");
			$id = mysqli_insert_id($dbc);
		}
		if($table_name == 'contacts') {
			$contacts_tile = 'contacts';
			if(tile_enabled($dbc, 'contacts')['user_enabled'] == 1) {
				$contacts_tile = 'contacts';
			} else if(tile_enabled($dbc, 'members')['user_enabled'] == 1) {
				$contacts_tile = 'members';
			} else if(tile_enabled($dbc, 'client_info')['user_enabled'] == 1) {
				$contacts_tile = 'clientinfo';
			}
			mysqli_query($dbc, "UPDATE `contacts` SET `tile_name` = '$contacts_tile' WHERE `contactid` = '$id'");
		} else if ($table_name == 'ticket_schedule') {
			$status = get_config($dbc, 'ticket_default_status');
			$dbc->query("UPDATE `ticket_schedule` SET `status`='$status' WHERE `id`='$id'");
		}
		if($detail_field != '') {
			$dbc->query("UPDATE `$table_name` SET `$detail_field`='$detail' WHERE `$id_field`='$id'");
		}
		if($table_name == 'ticket_attached') {
			mysqli_query($dbc, "UPDATE `$table_name` SET `date_stamp`='".date('Y-m-d')."' WHERE `$id_field`='$id'");
		} else {
			mysqli_query($dbc, "UPDATE `$table_name` SET `created_by`='{$_SESSION['contactid']}' WHERE `$id_field`='$id'");
			mysqli_query($dbc, "UPDATE `$table_name` SET `created_date`=CURRENT_TIMESTAMP WHERE `$id_field`='$id'");
		}
		if($table_name == 'tickets' && !empty($_POST['tile_name'])) {
			$ticket_type = filter_var($_POST['tile_name'],FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "UPDATE `tickets` SET `ticket_type`='$ticket_type' WHERE `ticketid`='$id'");
		}
		if($type != '') {
			if($type_field == '') {
				$type_field = 'type';
			}
			mysqli_query($dbc, "UPDATE `$table_name` SET `$type_field`='$type' WHERE `$id_field`='$id'");
		}
		if($attach != '') {
			if($attach_field == '') {
				$attach_field = 'attach';
			}
			mysqli_query($dbc, "UPDATE `$table_name` SET `$attach_field`='$attach' WHERE `$id_field`='$id'");
		}
		echo $id.$extra_id_info;
		if($table_name == 'tickets') {
			insert_day_overview($dbc, $_SESSION['contactid'], 'Ticket', date('Y-m-d'), '', 'Created '.TICKET_NOUN.' #'.$id, $id);
		}
	} else if($table_name == 'contacts_description') {
		mysqli_query($dbc, "INSERT INTO `contacts_description` (`$id_field`) SELECT '$id' FROM (SELECT COUNT(*) rows FROM `contacts_description` WHERE `$id_field`='$id') num WHERE num.rows=0");
	}
	if($table_name == 'ticket_attached' && !empty($_POST['append_note'])) {
		$append = filter_var(htmlentities($_POST['append_note']),FILTER_SANITIZE_STRING);
		$dbc->query("UPDATE `ticket_attached` SET `notes`=CONCAT(IFNULL(`notes`,''),'$append') WHERE `id`='$id' AND `notes` NOT LIKE '%$append'");
	}
	if($table_name == 'ticket_attached' && $field_name == 'item_id' && $type == 'material') {
		if($_POST['auto_checkin'] == 1) {
			mysqli_query($dbc, "UPDATE `ticket_attached` SET `arrived` = 1 WHERE `id`='$id'");
		}
		if($_POST['auto_checkout'] == 1) {
			mysqli_query($dbc, "UPDATE `ticket_attached` SET `completed` = 1 WHERE `id`='$id'");
		}
	}
	if($table_name == 'ticket_attached' && ($field_name == 'arrived' || $field_name == 'completed')) {
		$seconds = time();
		if($field_name == 'arrived' && $value == 1) {
			mysqli_query($dbc, "UPDATE `ticket_attached` SET `timer_start`='$seconds' WHERE `id`='$id'");
			mysqli_query($dbc, "UPDATE `ticket_attached` SET `checked_in`='".date('h:i a')."' WHERE `id`='$id' AND IFNULL(`checked_in`,'') = ''");
		} else {
			$hours = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `timer_start`, `hours_tracked` FROM `ticket_attached` WHERE `id`='$id'"));
			if($hours['timer_start'] > 0) {
				$tracked = ($seconds - $hours['timer_start']) / 3600 + $hours['hours_tracked'];
				mysqli_query($dbc, "UPDATE `ticket_attached` SET `hours_tracked`='$tracked', `timer_start`=0, `date_stamp`='".date('Y-m-d')."' WHERE `id`='$id'");
				mysqli_query($dbc, "UPDATE `ticket_attached` SET `checked_out`='".date('h:i a')."' WHERE `id`='$id'");
			}
		}
	}
	$time_minimum = get_config($dbc, 'ticket_min_hours');
	$time_interval = get_config($dbc, 'timesheet_hour_intervals');
	if($table_name == 'ticket_attached' && $field_name == 'received' && $type == 'inventory') {
		$received = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `item_id`, `received` FROM `ticket_attached` WHERE `id`='$id'"));
		$diff = $received['received'] - $value;
		if($diff > 0 || $diff < 0 && $received['item_id'] > 0) {
			$dbc->query("INSERT INTO `inventory_change_log` (`inventoryid`,`contactid`,`location_of_change`,`old_inventory`,`changed_inventory`,`new_inventory`,`date_time`) SELECT `inventoryid`,'{$_SESSION['contactid']}','".TICKET_TILE." Tile',`quantity`,'$diff',IFNULL(`quantity`,0)+$diff FROM `inventory` WHERE `inventoryid`='{$received['item_id']}'");
			mysqli_query($dbc, "UPDATE `inventory` SET `quantity`=$value WHERE `inventoryid`='{$received['item_id']}'");
		}
	} else if($table_name == 'ticket_attached' && $field_name == 'qty' && $type == 'inventory') {
		$qty = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `item_id`, `qty` FROM `ticket_attached` WHERE `id`='$id'"));
		mysqli_query($dbc, "UPDATE `inventory` SET `expected_inventory`=$value WHERE `inventoryid`='{$qty['item_id']}'");
	} else if($table_name == 'ticket_attached' && $field_name == 'used' && $type == 'inventory') {
		$used = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `item_id`, `used` FROM `ticket_attached` WHERE `id`='$id'"));
		$diff = $used['used'] - $value;
		if($diff > 0 || $diff < 0 && $used['item_id'] > 0) {
			$dbc->query("INSERT INTO `inventory_change_log` (`inventoryid`,`contactid`,`location_of_change`,`old_inventory`,`changed_inventory`,`new_inventory`,`date_time`) SELECT `inventoryid`,'{$_SESSION['contactid']}','".TICKET_TILE." Tile',`quantity`,-$diff,IFNULL(`quantity`,0)-$diff FROM `inventory` WHERE `inventoryid`='{$used['item_id']}'");
			mysqli_query($dbc, "UPDATE `inventory` SET `quantity`=`quantity` - $diff WHERE `inventoryid`='{$used['item_id']}'");
		}
	} else if($table_name == 'inventory') {
		$dbc->query("INSERT INTO `inventory_change_log` (`inventoryid`,`contactid`,`location_of_change`,`change_comment`) SELECT `inventoryid`,'{$_SESSION['contactid']}','".TICKET_TILE." Tile','Updated $field_name to $value' FROM `inventory` WHERE `inventoryid`='$id'");
	} else if($table_name == 'ticket_attached' && $field_name == 'item_id' && $type == 'Staff') {
		mysqli_query($dbc, "UPDATE `tickets` LEFT JOIN (SELECT `ticketid`, GROUP_CONCAT(`item_id`) staff FROM `ticket_attached` WHERE src_table='Staff' AND `item_id` > 0 AND `ticketid`='$ticketid' AND `deleted`=0) `ticket_staff` ON `ticket_staff`.`ticketid`=`tickets`.`ticketid` SET `contactid`=`ticket_staff`.`staff` WHERE `tickets`.`ticketid`='$ticketid'");
	} else if($table_name == 'ticket_attached' && $field_name == 'arrived' && !($_GET['time_sheet'] == 'none')) {
		$attached = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `id`='$id'"));
		$seconds = time();
		$time = date('H:i');
		$today = (empty($_POST['date']) ? date('Y-m-d') : filter_var($_POST['date'],FILTER_SANITIZE_STRING));
		if(($attached['src_table'] == 'Staff' || $attached['src_table'] == 'Staff_Tasks' || $attached['src_table'] == 'Delivery') && $value == 1) {
			// Sign out of Day Tracking and create a new row to resume Day Tracking
			mysqli_query($dbc, "INSERT INTO `time_cards` (`timer_start`, `type_of_time`, `start_time`, `staff`, `date`, `day_tracking_type`, `created_by`) SELECT '$seconds', 'day_tracking', '$time', `staff`, '".date('Y-m-d')."', CONCAT('Work:',MAX(`time_cards_id`)), 0 FROM `time_cards` WHERE `timer_start` > 0 AND `type_of_time`='day_tracking' AND `day_tracking_type` NOT LIKE 'Work:%' AND `staff`='".$attached['item_id']."'");
			mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs`=GREATEST(IF('$time_interval' > 0,CEILING(((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600) / '$time_interval') * '$time_interval',((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600)),'$time_minimum'), `timer_tracked` = (($seconds - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `type_of_time`=IF(`day_tracking_type` IS NULL OR `day_tracking_type` = '', 'Regular Hrs.', `day_tracking_type`), `end_time`='$time' WHERE `timer_start` > 0 AND `type_of_time`='day_tracking' AND `day_tracking_type` NOT LIKE 'Work:%' AND `staff`='".$attached['item_id']."'");
			// Sign into the Ticket
			if(strpos($value_config, ',Staff Multiple Times,') === FALSE) {
				mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs` = GREATEST(IF('$time_interval' > 0,CEILING(((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600) / '$time_interval') * '$time_interval',((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600)),'$time_minimum'), `timer_tracked` = (($seconds - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `end_time`='$time' WHERE `type_of_time` NOT IN ('day_tracking','day_break') AND `timer_start` > 0 AND `staff`='{$attached['item_id']}'");
				mysqli_query($dbc, "INSERT INTO `time_cards` (`business`, `projectid`, `ticketid`, `staff`, `date`, `start_time`, `timer_start`, `type_of_time`, `comment_box`, `ticket_attached_id`) SELECT `businessid`, `projectid`, `ticketid`, '{$attached['item_id']}', '$today', '$time', '$seconds', '{$attached['position']}', 'Checked in on ".TICKET_NOUN." #{$attached['ticketid']} for {$attached['position']}', '{$attached['id']}' FROM `tickets` WHERE `ticketid`='{$attached['ticketid']}'");
				mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs` = GREATEST(IF('$time_interval' > 0,CEILING(((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600) / '$time_interval') * '$time_interval',((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600)),'$time_minimum'), `timer_tracked` = (($seconds - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `end_time`='$time', `comment_box`=CONCAT(IFNULL(`comment_box`,''),'Signed in on ".get_ticket_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='{$attached['ticketid']}'")))."') WHERE `type_of_time` NOT IN ('day_tracking','day_break') AND `ticketid`!='{$attached['ticketid']}' AND `staff`='{$attached['item_id']}' AND `timer_start` > 0");
			}
			mysqli_query($dbc, "UPDATE `ticket_attached` SET `checked_out`='".date('h:i a')."', `completed`=1 WHERE `id`!='$id' AND `src_table`='{$attached['src_table']}' AND `item_id`='{$attached['item_id']}' AND (`arrived`=1 AND `completed`=0)");
			mysqli_query($dbc, "UPDATE `ticket_attached` SET `checked_in`='".date('h:i a')."' WHERE `id`='$id' AND IFNULL(`checked_in`,'') = ''");
			if(get_config($dbc, 'ticket_force_starts_day') > 0 && $attached['item_id'] > 0) {
				$comment = get_config($dbc, 'day_tracking_preset_note' && strpos($value_config, ',Staff Multiple Times,') === FALSE);
				mysqli_query($dbc, "INSERT INTO `time_cards` (`staff`, `date`, `start_time`, `type_of_time`, `timer_start`, `comment_box`) VALUES ('{$attached['item_id']}', '$today', '$time', 'day_tracking', '$seconds', '$comment')");
			}
		} else if(($attached['src_table'] == 'Staff' || $attached['src_table'] == 'Staff_Tasks' || $attached['src_table'] == 'Delivery') && $value == 0) {
			// Sign Back Into Day Tracking, if they were Signed In
			mysqli_query($dbc, "UPDATE `time_cards` `time` LEFT JOIN `time_cards` `src` ON `time`.`day_tracking_type`=CONCAT('Work:',`src`.`time_cards_id`) SET `time`.`timer_start`='".time()."', `time`.`start_time`='".date('H:i')."', `time`.`date`='".date('Y-m-d')."', `time`.`comment_box`=`src`.`comment_box`, `time`.`day_tracking_type`=`src`.`day_tracking_type`, `time`.`created_by`='".$_SESSION['contactid']."' WHERE `time`.`timer_start` > 0 AND `time`.`day_tracking_type` LIKE 'Work%' AND `time`.`staff`='".$attached['item_id']."' AND `time`.`deleted`=0");
			// Sign out of the Ticket
			if(strpos($value_config, ',Staff Multiple Times,') === FALSE) {
				mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs` = GREATEST(IF('$time_interval' > 0,CEILING(((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600) / '$time_interval') * '$time_interval',((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600)),'$time_minimum'), `timer_tracked` = (($seconds - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `end_time`='$time' WHERE `type_of_time` NOT IN ('day_tracking','day_break') AND `timer_start` > 0 AND `staff`='{$attached['item_id']}'");
			}
			$hours = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`total_hrs`) FROM `time_cards` WHERE `ticketid`='{$attached['ticketid']}' AND `staff`='{$attached['item_id']}' AND `comment_box` LIKE '% for {$attached['position']}'"))[0];
			mysqli_query($dbc, "UPDATE `ticket_attached` SET `hours_tracked`='$hours' WHERE `id`='$id'");
			mysqli_query($dbc, "UPDATE `ticket_attached` SET `checked_out`='".date('h:i a')."' WHERE `id`='$id'");
			echo $hours;
		}
	} else if($table_name == 'ticket_attached' && $field_name == 'completed' && !($_GET['time_sheet'] == 'none')) {
		$attached = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `id`='$id'"));
		$seconds = time();
		$time = date('H:i');
		$today = (empty($_POST['date']) ? date('Y-m-d') : filter_var($_POST['date'],FILTER_SANITIZE_STRING));
		if(($attached['src_table'] == 'Staff' || $attached['src_table'] == 'Staff_Tasks' || $attached['src_table'] == 'Delivery') && $value == 1) {
			// Sign Back Into Day Tracking, if they were Signed In
			mysqli_query($dbc, "UPDATE `time_cards` `time` LEFT JOIN `time_cards` `src` ON `time`.`day_tracking_type`=CONCAT('Work:',`src`.`time_cards_id`) SET `time`.`timer_start`='".time()."', `time`.`start_time`='".date('H:i')."', `time`.`date`='".date('Y-m-d')."', `time`.`comment_box`=`src`.`comment_box`, `time`.`day_tracking_type`=`src`.`day_tracking_type`, `time`.`created_by`='".$_SESSION['contactid']."' WHERE `time`.`timer_start` > 0 AND `time`.`day_tracking_type` LIKE 'Work%' AND `time`.`staff`='".$attached['item_id']."' AND `time`.`deleted`=0");
			// Sign out of the Ticket

			if(strpos($value_config, ',Staff Multiple Times,') !== FALSE) {
				// If using the Staff Multiple Dates/Time setting, insert Time Cards based on those settings
				$staff_times = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `src_table` = 'Multiple_Timesheet_Row' AND `item_id` = '".$attached['id']."' AND '".$attached['id']."' > 0 AND `deleted` = 0");
				while($staff_time = mysqli_fetch_assoc($staff_times)) {
					mysqli_query($dbc, "INSERT INTO `time_cards` (`ticketid`, `ticket_attached_id`) SELECT '".$attached['ticketid']."', '".$staff_time['id']."' FROM (SELECT COUNT(*) rows FROM `time_cards` WHERE `ticket_attached_id` = '".$staff_time['id']."' AND `deleted` = 0) num WHERE num.rows=0");
					mysqli_query($dbc, "UPDATE `time_cards` SET `staff` = '".$attached['item_id']."', `date` = '".$staff_time['date_stamp']."', `start_time` = '".$staff_time['start_time']."', `end_time` = '".$staff_time['end_time']."', `type_of_time` = '".($staff_time['position'] == 'Sleep Hrs.' ? 'Sleep Hrs.' : $attached['position'])."', `total_hrs` = '".$staff_time['hours_set']."' WHERE `ticket_attached_id` = '".$staff_time['id']."'");
				}
			} else {
				// If payable hours are set and tracked in time cards, use that as total hours but keep time_tracked to the value set by checkin/checkout
				if(strpos($value_config,',Staff Set Hours Time Sheet,') !== FALSE && $attached['hours_set'] > 0) {
					$latest_time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `ticket_attached_id` = '".$attached['id']."' AND `deleted` = 0 AND `timer_start` > 0 ORDER BY `time_cards_id` DESC"));
					if(!empty($latest_time_card)) {
						mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs` = '".$attached['hours_set']."', `timer_tracked` = (($seconds - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `end_time`='$time' WHERE `time_cards_id` = '".$latest_time_card['time_cards_id']."'");
						mysqli_query($dbc, "UPDATE `time_cards` SET `deleted` = 1 WHERE `ticket_attached_id` = '".$attached['id']."' AND `time_cards_id` != '".$latest_time_card['time_cards_id']."'");
					}

				} else {
					// $existing_time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `ticket_attached_id` = '{$attached['id']}' AND `timer_tracked` = 0 AND `total_hrs` > 0 AND `deleted` = 0"));
					// $override_hours = '';
					// if(strpos($value_config,',Staff Set Hours Time Sheet,') !== FALSE && !empty($existing_time_card)) {
					// 	$override_hours = $existing_time_card['total_hrs'];
					// 	mysqli_query($dbc, "UPDATE `time_cards` SET `deleted` = '1' WHERE `time_cards_id` = '{$existing_time_card['time_cards_id']}'");
					// }
					mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs` = GREATEST(IF('$time_interval' > 0,CEILING(((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600) / '$time_interval') * '$time_interval',((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600)),'$time_minimum'), `timer_tracked` = (($seconds - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `end_time`='$time' WHERE `type_of_time` NOT IN ('day_tracking','day_break') AND `timer_start` > 0 AND `staff`='{$attached['item_id']}'");
				}
			}
			$hours = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`total_hrs`) FROM `time_cards` WHERE `ticketid`='{$attached['ticketid']}' AND `staff`='{$attached['item_id']}' AND `comment_box` LIKE '% for {$attached['position']}'"))[0];
			mysqli_query($dbc, "UPDATE `ticket_attached` SET `hours_tracked`='$hours' WHERE `id`='$id'");
			mysqli_query($dbc, "UPDATE `ticket_attached` SET `checked_out`='".date('h:i a')."' WHERE `id`='$id'");
            $ticket_heading = mysqli_fetch_array(mysqli_query($dbc, "SELECT `heading` FROM `tickets` WHERE `ticketid`='{$attached['ticketid']}'"))['heading'];
            insert_day_overview($dbc, $attached['item_id'], 'Ticket', date('Y-m-d'), '', 'Updated '.TICKET_NOUN.' #'.$attached['ticketid'].(!empty($ticket_heading) ? ': '.$ticket_heading : ''), $attached['ticketid']);
			echo $hours;
		}
	} else if($table_name == 'ticket_attached' && ($field_name == 'time_set' || ($field_name == 'hours_set' && $_POST['track_timesheet'] == 1)) && strpos($value_config, ',Staff Multiple Times,') === FALSE) {
		$hours = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `$id_field`='$id'"));
		$total_hours = $value;
		$other_hours = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT IFNULL(SUM(`hours_set`),0) sum_hours FROM `ticket_attached` WHERE `src_table` = '{$hours['src_table']}' AND `item_id` = '{$hours['item_id']}' AND `ticketid` = '{$hours['ticketid']}' AND `deleted` = 0 AND `$id_field` != '$id'"))['sum_hours'];
		$total_hours += $other_hours;

		$timecard_exists = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `ticketid` = '{$hours['ticketid']}' AND `staff` = '{$hours['item_id']}' AND `deleted` = 0"));
		if(!empty($timecard_exists)) {
			mysqli_query($dbc, "UPDATE `time_cards` SET `date` = '".(empty($_POST['date']) ? date('Y-m-d') : filter_var($_POST['date'],FILTER_SANITIZE_STRING))."', `total_hrs` = '$total_hours' WHERE `time_cards_id` = '{$timecard_exists['time_cards_id']}'");
		} else {
			mysqli_query($dbc, "INSERT INTO `time_cards` (`date`, `type_of_time`, `total_hrs`, `staff`, `ticketid`, `highlight`, `ticket_attached_id`) VALUES ('".(empty($_POST['date']) ? date('Y-m-d') : filter_var($_POST['date'],FILTER_SANITIZE_STRING))."', '{$hours['position']}', '$total_hours', '{$hours['item_id']}', '{$hours['ticketid']}', 1, '{$hours['id']}')");
		}
		$field_name = 'hours_set';
	} else if($table_name == 'ticket_attached' && $field_name == 'time_comment' && strpos($value_config, ',Staff Multiple Times,') === FALSE) {
		$hours = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `$id_field`='$id'"));
		mysqli_query($dbc, "UPDATE `time_cards` SET `comment_box`=CONCAT(IFNULL(CONCAT(`comment_box`,'&lt;br /&gt;'),''),'$value') WHERE `time_cards_id` IN (SELECT MAX(`time_cards_id`) FROM `time_cards` WHERE `ticketid`='{$hours['ticketid']}' AND `staff`='{$hours['item_id']}' AND `date`='".date('Y-m-d')."')");
	} else if($table_name == 'ticket_attached' && $field_name == 'position' && !($_GET['time_sheet'] == 'none') && strpos($value_config, ',Staff Multiple Times,') === FALSE) {
		$attached = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `id`='$id'"));
		mysqli_query($dbc, "UPDATE `time_cards` SET `comment`='Checked in on ".TICKET_NOUN." #{$attached['ticketid']} for $value' WHERE `ticketid`='{$attached['ticketid']}' AND `staff`='{$attached['item_id']}' AND `comment_box` LIKE '% for {$attached['position']}'");
	} else if($table_name == 'ticket_attached' && $field_name == 'date_stamp' && $_GET['time_sheet'] != 'none' && strpos($value_config, ',Staff Multiple Times,') === FALSE) {
		$attached = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `id`='$id'"));
		mysqli_query($dbc, "UPDATE `time_cards` SET `date`='$value' WHERE `ticketid`='{$attached['ticketid']}' AND `staff`='{$attached['item_id']}' AND `comment_box` LIKE '% for {$attached['position']}'");
	} else if($table_name == 'ticket_attached' && $field_name == 'hours_tracked' && $_GET['time_sheet'] != 'none' && strpos($value_config, ',Staff Multiple Times,') === FALSE) {
		$attached = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `id`='$id'"));
		mysqli_query($dbc, "UPDATE `ticket_attached` SET `checked_out`=FROM_UNIXTIME(UNIX_TIMESTAMP(STR_TO_DATE(`checked_in`,'%h:%i %p')) + `hours_tracked` / 3600,'%h:%i %p') WHERE `id`='$id'");
		$hours = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`total_hrs`) FROM `time_cards` WHERE `ticketid`='{$attached['ticketid']}' AND `staff`='{$attached['item_id']}' AND `comment_box` LIKE '% for {$attached['position']}'"))[0];
		if($hours != $value) {
			$hours = $value - $hours;
			$end_time = time_decimal2time(time_time2decimal($time) + $hours);
			mysqli_query($dbc, "INSERT INTO `time_cards` (`business`, `projectid`, `ticketid`, `staff`, `date`, `start_time`, `end_time`, `timer_start`, `type_of_time`, `comment_box`, `ticket_attached_id`) SELECT `businessid`, `projectid`, `ticketid`, '{$attached['item_id']}', '$today', '$time', '$end_time', '$seconds', '{$hours['position']}', 'Hours Modified on Ticket #{$attached['ticketid']} for  for {$attached['position']}', '{$attached['id']}' FROM `tickets` WHERE `ticketid`='{$attached['ticketid']}'");
		}
	} else if($table_name == 'ticket_attached' && $field_name == 'checked_in' && $value != '' && strpos($value_config, ',Staff Multiple Times,') === FALSE) {
		$attached = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `id`='$id'"));
		mysqli_query($dbc, "INSERT INTO `time_cards` (`ticketid`, `businessid`, `projectid`, `staff`, `date`, `ticket_attached_id`) SELECT `tickets`.`ticketid`, `tickets`.`businessid`, `tickets`.`projectid`, `ticket_attached`.`item_id`, `ticket_attached`.`date_stamp`, `ticket_attached`.`id` FROM `ticket_attached` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` LEFT JOIN `time_cards` ON `ticket_attached`.`ticketid`=`time_cards`.`ticketid` AND `ticket_attached`.`item_id`=`time_cards`.`staff` WHERE `time_cards`.`time_cards_id` IS NULL AND `ticket_attached`.`id`='{$attached['id']}'");
		mysqli_query($dbc, "UPDATE `time_cards` SET `start_time`='$value' WHERE `ticketid`='{$attached['ticketid']}' AND `staff`='{$attached['item_id']}' AND `comment_box` LIKE '% for {$attached['position']}'");
	} else if($table_name == 'ticket_attached' && $field_name == 'checked_out' && $value != '' && strpos($value_config, ',Staff Multiple Times,') === FALSE) {
		$attached = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `id`='$id'"));
		mysqli_query($dbc, "UPDATE `ticket_attached` SET `hours_tracked`=(UNIX_TIMESTAMP(STR_TO_DATE(`checked_out`,'%h:%i %p')) - UNIX_TIMESTAMP(STR_TO_DATE(`checked_in`,'%h:%i %p'))) / 3600 WHERE `id`='$id' AND `checked_in` != ''");
		mysqli_query($dbc, "INSERT INTO `time_cards` (`ticketid`, `businessid`, `projectid`, `staff`, `date`, `ticket_attached_id`) SELECT `tickets`.`ticketid`, `tickets`.`businessid`, `tickets`.`projectid`, `ticket_attached`.`item_id`, `ticket_attached`.`date_stamp`, `ticket_attached`.`ticket_attached_id` FROM `ticket_attached` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` LEFT JOIN `time_cards` ON `ticket_attached`.`ticketid`=`time_cards`.`ticketid` AND `ticket_attached`.`item_id`=`time_cards`.`staff` WHERE `time_cards`.`time_cards_id` IS NULL AND `ticket_attached`.`id`='{$attached['id']}'");
		mysqli_query($dbc, "UPDATE `time_cards` SET `end_time`='$value', `total_hrs`=(UNIX_TIMESTAMP(STR_TO_DATE(`end_time`,'%h:%i %p')) - UNIX_TIMESTAMP(STR_TO_DATE(`start_time`,'%h:%i %p'))) / 3600 WHERE `ticketid`='{$attached['ticketid']}' AND `staff`='{$attached['item_id']}' AND `comment_box` LIKE '% for {$attached['position']}'");
	} else if($table_name == 'ticket_attached' && $field_name == 'notes' && !($_GET['time_sheet'] == 'none') && $value != '' && strpos($value_config, ',Staff Multiple Times,') === FALSE) {
		$attached = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `id`='$id'"));
		if($attached['src_table'] == 'Staff') {
			mysqli_query($dbc, "UPDATE `time_cards` SET `comment_box`=CONCAT(IFNULL(CONCAT(`comment_box`,'&lt;br /&gt;'),''),'Checked Out: $value') WHERE `time_cards_id` IN (SELECT * FROM (SELECT MAX(`time_cards_id`) FROM `time_cards` WHERE `staff`='".$attached['item_id']."' AND `ticketid`='".$attached['ticketid']."' AND `type_of_time` IN ('Regular Hrs.','".$attached['position']."')) `time_id`)");
		}
	} else if($field_name == 'signature' || $field_name == 'witnessed') {
		include_once('../phpsign/signature-to-image.php');
		$signature = sigJsonToImage(html_entity_decode($value));
		imagepng($signature, 'download/'.$field_name.'_'.$id.'.png');
		echo 'download/'.$field_name.'_'.$id.'.png';
	} else if($table_name == 'tickets' && !empty($id) && $field_name == 'equipmentid') {
		$get_ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$id'"));
		$to_do_date = $get_ticket['to_do_date'];
		$equipassign = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipmentid` = '$value' AND `deleted` = 0 AND DATE(`start_date`) <= '$to_do_date' AND DATE(`end_date`) >= '$to_do_date' ORDER BY `start_date` DESC, `end_date` ASC"));

		//If there is an equipment_assignmnet for that equipment for that day, then update this ticket with the assigned staff, team, and equipment assignment
		if(!empty($equipassign)) {
			$equipment_assignmentid = $equipassign['equipment_assignmentid'];
			$teamid = $equipassign['teamid'];
			$equipassign_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '$equipment_assignmentid' AND `deleted` = 0"),MYSQLI_ASSOC);
			$contact = [];
			foreach ($equipassign_staff as $staffid) {
				if(!in_array($staffid['contactid'], $contact)) {
					$contact[] = $staffid['contactid'];
				}
			}
			$team_staff = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC);
			foreach ($team_staff as $staffid) {
				if(!in_array($staffid['contactid'], $contact)) {
					$contact[] = $staffid['contactid'];
				}
			}
			$contact = implode(',',$contact);
			mysqli_query($dbc, "UPDATE `tickets` SET `equipment_assignmentid` = '$equipment_assignmentid', `contactid` = ',$contact,', `teamid` = '$teamid' WHERE `ticketid` = '$id'");
			$ea_contacts = [];
			foreach(explode(',', $contact) as $ea_contact) {
				if($ea_contact > 0) {
					$ea_contacts[] = get_contact($dbc, $ea_contact);
				}
			}
			$ticket_history_addition .= " (".implode(', ',$ea_contacts).")";
		}
	} else if($table_name == 'contacts' && $field_name == 'business_address') {
		$value = encryptIt($value);
	} else if($table_name == 'ticket_schedule' && $field_name == 'complete' && $value > 0) {
		$ticket = $dbc->query("SELECT `address`, `city`, `province`, `to_do_date`, `to_do_start_time`, `contactid`, `equipmentid` FROM `ticket_schedule` WHERE `id`='$id'")->fetch_assoc();
		$address = implode(', ',[$ticket['address'],$ticket['city'],$ticket['province']]);
		if(trim($address,', ') != '') {
			if($next_address = $dbc->query("SELECT * FROM (SELECT `pickup_address`, `pickup_city`, `pickup_postal_code`, `to_do_start_time` FROM `tickets` WHERE `to_do_date`='{$ticket['to_do_date']}' AND `to_do_start_time` > '{$ticket['to_do_start_time']}' AND `deleted`=0 AND (`equipmentid` = '{$ticket['equipmentid']}' OR `contactid` = '{$ticket['contactid']}') UNION SELECT `address`, `city`, `province`, `to_do_start_time` FROM `ticket_schedule` WHERE `to_do_date`='{$ticket['to_do_date']}' AND `to_do_start_time` > '{$ticket['to_do_start_time']}' AND `deleted`=0 AND (`equipmentid` = '{$ticket['equipmentid']}' OR `contactid` = '{$ticket['contactid']}')) `addresses` ORDER BY `to_do_start_time` ASC")->fetch_assoc()) {
				$next_address = implode(', ',[$next_address['address'],$next_address['city'],$next_address['province']]);
				if(trim($next_address,', ') != '') {
					$eta = $data = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=".urlencode($address)."&destinations=".urlencode($next_address)."&language=en-EN&sensor=false"));
					$eta_time = 0;
					$eta_dist = 0;
					foreach($eta->rows[0]->elements as $road) {
						$eta_time += $road->duration->value / 3600;
						$eta_dist += $road->distance->value / 1000;
					}
					$dbc->query("UPDATE `ticket_schedule` SET `est_distance`='$eta_dist', `est_time`='$eta_time', `completed_time`=NOW() WHERE `id`='$id'");
				}
			}
		}
		$completed_ticket_status = get_config($dbc, 'auto_archive_complete_tickets');
		if(!empty($completed_ticket_status)) {
			mysqli_query($dbc, "UPDATE `ticket_schedule` SET `status` = '$completed_ticket_status' WHERE `id`='$id'");
		}
	} else if($table_name == 'tickets' && $field_name == 'sign_off_id' && $value > 0) {
		$ticket = $dbc->query("SELECT `pickup_address`, `pickup_city`, `pickup_postal_code`, `to_do_date`, `to_do_start_time` FROM `tickets` WHERE `ticketid`='$ticketid'")->fetch_assoc();
		$address = implode(', ',[$ticket['pickup_address'],$ticket['pickup_city'],$ticket['pickup_postal_code']]);
		if(trim($address,', ') != '') {
			if($next_address = $dbc->query("SELECT * FROM (SELECT `pickup_address`, `pickup_city`, `pickup_postal_code` FROM `tickets` WHERE `to_do_date`='{$ticket['to_do_date']}' AND `to_do_start_time` > '{$ticket['to_do_start_time']}' WHERE `deleted`=0 UNION SELECT `address`, `city`, `province` FROM `ticket_schedule` WHERE `to_do_date`='{$ticket['to_do_date']}' AND `to_do_start_time` > '{$ticket['to_do_start_time']}' WHERE `deleted`=0) `addresses` ORDER BY `to_do_start_time` ASC")) {
				$next_address = implode(', ',$next_address);
				if(trim($next_address,', ') != '') {
					$eta = $data = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=".urlencode($address)."&destinations=".urlencode($next_address)."&language=en-EN&sensor=false"));
					$eta_time = 0;
					$eta_dist = 0;
					foreach($eta->rows[0]->elements as $road) {
						$eta_time += $road->duration->value / 3600;
						$eta_dist += $road->distance->value / 1000;
					}
					$dbc->query("UPDATE `tickets` SET `est_distance`='$eta_dist', `est_time`='$eta_time', `completed_time`=NOW() WHERE `ticketid`='$ticketid'");
				}
			}
		}
	} else if($table_name == 'ticket_attached' && $field_name == 'deleted' && $value > 0) {
		mysqli_query($dbc, "UPDATE `time_cards` SET `deleted` = 1 WHERE `ticket_attached_id` = '".$id."' AND '".$id."' > 0");
	}

	// Check if we are adding a new contact
	if($_POST['one_time'] != 'true' && trim($value) != '' && !($value > 0)) {
		$bus_name = encryptIt($value);
		if($field_name == 'vendor' && $table_name == 'ticket_schedule') {
			$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `name`) VALUES ('contacts','$category','$bus_name')");
			$value = mysqli_insert_id($dbc);
		} else if($field_name == 'carrier' && $table_name == 'ticket_schedule') {
			mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `name`) VALUES ('contacts','Carrier','$bus_name')");
			$value = mysqli_insert_id($dbc);
		} else if($field_name == 'agentid' && $table_name == 'tickets') {
			$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `name`) VALUES ('contacts','$category','$bus_name')");
			$value = mysqli_insert_id($dbc);
		}
	}

	// Record History
	mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$id of $table_name updated: $field_name updated to $value$ticket_history_addition')");
	mysqli_query($dbc, "UPDATE `$table_name` SET `$field_name`='$value' WHERE `$id_field`='$id'");
	mysqli_query($dbc, "UPDATE `$table_name` SET `$manual_field`='$manual_value' WHERE `$id_field`='$id'");
	if($table_name == 'ticket_attached' && $type_field == 'src_table' && $type == 'medication' && ($field_name == 'position' || $field_name == 'item_id' || $field_name == 'description')) {
		mysqli_query($dbc, "INSERT INTO `medication` (`title`,`clientid`,`dosage`) SELECT `position`,`item_id`,`description` FROM `ticket_attached` LEFT JOIN (SELECT `clientid`, `title`, COUNT(*) rows FROM `medication` GROUP BY `clientid`, `title`) num ON `ticket_attached`.`item_id`=`num`.`clientid` AND `ticket_attached`.`position`=`num`.`title` WHERE `ticket_attached`.`id`='$id' AND `position` != '' AND `item_id` > 0 AND `description` != '' AND `num`.`rows` IS NULL");
	} else if($table_name == 'ticket_attached' && $type_field == 'src_table' && $type == 'guardian') {
		$guardians = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid`='$ticketid' AND `ticketid` > 0 AND `line_id`='$attach' AND `src_table`='guardian' GROUP BY `description`, `contact_info`");
		$first = [];
		$last = [];
		$phone = [];
		$contactid = 0;
		while($guardian = mysqli_fetch_assoc($guardians)) {
			$name = explode(' ',$guardian['description']);
			$first[] = $name[0];
			$last[] = trim(str_replace($name[0],'',$guardian['description']));
			$phone[] = $guardian['contact_info'];
			$contactid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `item_id` FROM `ticket_attached` WHERE `id`='{$guardian['line_id']}'"))['item_id'];
		}
		mysqli_query($dbc, "UPDATE `contacts_medical` SET `guardians_first_name`='".implode('*#*',$first)."', `guardians_last_name`='".implode('*#*',$last)."', `guardians_cell_phone`='".implode('*#*',$phone)."' WHERE `contactid`='$contactid'");
	}

	// Get or insert the Project ID, if configured to force a new project for the business
	if($field_name == 'businessid' && $table_name = 'tickets' && $value > 0 && get_config($dbc, 'ticket_project_function') == 'business_project') {
		$projectid = $dbc->query("SELECT `projectid` FROM `project` WHERE `deleted`=0 AND `businessid`='$value'")->fetch_assoc();
		if(!($projectid > 0)) {
			if(get_config($dbc, 'project_status_pending') == '') {
				$status = 'Pending';
			} else {
				$status = explode('#*#',get_config($dbc, 'project_status'))[0];
			}
			$category = BUSINESS_CAT;
			$projecttype = explode(',',get_config($dbc, 'project_tabs'));
			if(in_array($category,$projecttype)) {
				$projecttype = $category;
			} else {
				$projecttype = $projecttype[0];
			}
			$dbc->query("INSERT INTO `project` (`businessid`,`project_name`,`status`,`projecttype`,`created_date`,`created_by`) VALUES ('$value','".get_contact($dbc, $value, 'name_company')."','$status','$projecttype',DATE(NOW()),'{$_SESSION['contactid']}')");
			$projectid = $dbc->insert_id;
		}
		$dbc->query("UPDATE `tickets` SET `projectid`='$projectid' WHERE `ticketid`='$id'");
	} else if($field_name == 'clientid' && $table_name = 'tickets' && $value > 0 && get_config($dbc, 'ticket_project_function') == 'contact_project') {
		$projectid = $dbc->query("SELECT `projectid` FROM `project` WHERE `deleted`=0 AND CONCAT(',',`clientid`,',') LIKE '%,$value,%'")->fetch_assoc();
		if(!($projectid > 0)) {
			if(get_config($dbc, 'project_status_pending') == '') {
				$status = 'Pending';
			} else {
				$status = explode('#*#',get_config($dbc, 'project_status'))[0];
			}
			$category = get_contact($dbc, $value, 'category');
			$projecttype = explode(',',get_config($dbc, 'project_tabs'));
			if(in_array($category,$projecttype)) {
				$projecttype = $category;
			} else {
				$projecttype = $projecttype[0];
			}
			$dbc->query("INSERT INTO `project` (`businessid`,`project_name`,`status`,`projecttype`,`created_date`,`created_by`) VALUES ('$value','".get_contact($dbc, $value, 'name_company')."','$status','$projecttype',DATE(NOW()),'{$_SESSION['contactid']}')");
			$projectid = $dbc->insert_id;
		}
		$dbc->query("UPDATE `tickets` SET `projectid`='$projectid' WHERE `ticketid`='$id'");
	}

	// Add project history entry
	if($field_name == 'projectid' && $value > 0) {
		$user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
		mysqli_query($dbc, "INSERT INTO `project_history` (`updated_by`, `description`, `projectid`) VALUES ('$user', '".TICKET_NOUN." #$ticketid attached', '$projectid')");
	}

	//Insert into day overview if last edit was not within 15 minutes
	$day_overview_last = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `timestamp` FROM `day_overview` WHERE `type` = 'Ticket' AND `tableid` = '$ticketid' AND `contactid` = '".$_SESSION['contactid']."' ORDER BY `timestamp` DESC"));
	$timestamp_now = date('Y-m-d h:i:s');
	$timediff = strtotime($timestamp_now) - strtotime($day_overview_last['timestamp']);
	if($timediff > 900 && !empty($ticketid)) {
		$ticket_heading = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading` FROM `tickets` WHERE `ticketid` = '$ticketid'"))['heading'];
		insert_day_overview($dbc, $_SESSION['contactid'], 'Ticket', date('Y-m-d'), '', 'Updated '.TICKET_NOUN.' #'.$ticketid.(!empty($ticket_heading) ? ': '.$ticket_heading : ''), $ticketid);
	}

	if($_POST['sync_recurring_data'] == 1) {
		sync_recurring_tickets($dbc, $ticketid);
	} else {
		mysqli_query($dbc, "UPDATE `tickets` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
		mysqli_query($dbc, "UPDATE `ticket_attached` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
		mysqli_query($dbc, "UPDATE `ticket_schedule` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
		mysqli_query($dbc, "UPDATE `ticket_comment` SET `is_recurrence` = 0 WHERE `ticketid` = '$ticketid'");
	}

} else if($_GET['action'] == 'validate_address') {
	$data = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?key=".GEOCODER_KEY."&address=".urlencode($_POST['address'].','.$_POST['city'].','.$_POST['postal']).""));
	$number = $address = $city = $postal = '';
	foreach($data->results[0]->address_components as $element) {
		if(in_array('street_number',$element->types)) {
			$number = $element->short_name;
		} else if(in_array('route',$element->types)) {
			$address = $element->short_name;
		} else if(in_array('locality',$element->types)) {
			$city = $element->short_name;
		} else if(in_array('postal_code',$element->types) || in_array('zip_code',$element->types)) {
			$postal = $element->short_name;
		}
	}
	echo trim($number.' '.$address.'|'.$city.'|'.$postal);
} else if($_GET['action'] == 'attached_image') {
	$ticketid = filter_var($_POST['ticket'],FILTER_SANITIZE_STRING);
	$basename = filter_var($_FILES['file']['name']);
	$filename = preg_replace('/(\.[A-Za-z0-9]*)/', '$1', preg_replace('/[^\.A-Za-z0-9]/','',$basename));
	$i = 0;
	if(!file_exists('download')) {
		mkdir('download', 0777, true);
	}
	while(file_exists('download/'.$filename)) {
		$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.++$i.')$1', preg_replace('/[^\.A-Za-z0-9]/','',$basename));
	}
	move_uploaded_file($_FILES['file']['tmp_name'],'download/'.$filename);
	mysqli_query($dbc, "UPDATE `tickets` SET `attached_image` = '$filename' WHERE `ticketid` = '$ticketid'");
} else if($_GET['action'] == 'add_file') {
	$table_name = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
	$field_name = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$ticketid = filter_var($_POST['ticket'],FILTER_SANITIZE_STRING);
	foreach($_FILES['files']['name'] as $file => $basename) {
		if(!file_exists('download')) {
	        mkdir('download', 0777, true);
		}
		$filename = file_safe_str($basename);
		move_uploaded_file($_FILES['files']['tmp_name'][$file],'download/'.$filename);
		if($table_name == 'ticket_attached' || $table_name == 'ticket_schedule') {
			$id = filter_var($_POST['table_id'],FILTER_SANITIZE_STRING);
			if(!($id > 0)) {
				$dbc->query("INSERT INTO `$table_name` (`ticketid`) VALUES ('$ticketid')");
				$id = $dbc->insert_id;
			}
			mysqli_query($dbc, "UPDATE `$table_name` SET `$field_name`='$filename' WHERE `id`='$id'");
		} else {
			mysqli_query($dbc, "INSERT INTO `$table_name` (`ticketid`,`document`,`label`,`created_by`,`created_date`) VALUES ('$ticketid','$filename','$basename','".$_SESSION['contactid']."',DATE(NOW()))");
		}
	}
} else if($_GET['action'] == 'send_email') {
	$table_name = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
	$field_src = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$id_field = filter_var($_POST['id_field'],FILTER_SANITIZE_STRING);
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$sender = filter_var($_POST['sender'],FILTER_SANITIZE_STRING);
	$sender_name = filter_var($_POST['sender_name'],FILTER_SANITIZE_STRING);
	$subject = $_POST['subject'];
	$body = $_POST['body'];

	if($table_name != '') {

		$value = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `$table_name` LEFT JOIN `tickets` BASE ON `$table_name`.`ticketid`=`BASE`.`ticketid` WHERE `$table_name`.`$id_field`='$id'"));

		$subject = str_replace(['[REFERENCE]','[TICKETID]','[CLIENT]','[HEADING]','[STATUS]','[LABEL]'], [html_entity_decode($value[$field_src]),$value['ticketid'],get_client($dbc,$value['businessid']),$value['heading'],$value['status'],$value['ticket_label']],$subject);

		$body = str_replace(['[REFERENCE]','[TICKETID]','[CLIENT]','[HEADING]','[STATUS]','[LABEL]','[PROJECT]','[DESC]','[START_DATE]','[END_DATE]'], [html_entity_decode($value[$field_src]),$value['ticketid'],get_client($dbc,$value['businessid']),$value['heading'],$value['status'],$value['ticket_label'], '#'.$value['projectid'].' '.get_project($dbc, $value['projectid'], 'project_name'),html_entity_decode($value['assign_work']),$value['to_do_date'],$value['to_do_end_date']],$body);
	}
	$recipient = $_POST['recipient'];
	if(!is_array($_POST['recipient'])) {
		$recipient = [$_POST['recipient']];
	}
	foreach($recipient as $address) {
		if($address > 0) {
			$address = get_email($dbc, filter_var($address,FILTER_SANITIZE_STRING));
		}
		try {
			send_email([$sender=>$sender_name], $address, '', '', $subject, $body, '');
		} catch(Exception $e) { echo "Unable to send e-mail: ".$e->getMessage(); }
	}
} else if($_GET['action'] == 'send_notification') {
	$ticketid = filter_var($_POST['ticketid'],FILTER_SANITIZE_STRING);
	$staffid = implode(',', $_POST['staff']);
	$contactid = implode(',', $_POST['contacts']);
	$businessid = $_POST['business'];
	$list = $_POST['list'];
	$output = str_replace('[LIST]','<ul><li>'.implode('</li><li>',$list).'</li></ul>', html_entity_decode($_POST['pdf']));
	$sender_name = filter_var($_POST['sender_name'],FILTER_SANITIZE_STRING);
	$sender_email = filter_var($_POST['sender_email'],FILTER_SANITIZE_STRING);
	$subject = $_POST['subject'];
	$body = htmlentities($_POST['body']);
	$send_date = filter_var($_POST['send_date'],FILTER_SANITIZE_STRING);
	$follow_up_date = filter_var($_POST['follow_up_date'],FILTER_SANITIZE_STRING);
	$log = '';

	if(!empty($staffid) || !empty($contactid)) {
		mysqli_query($dbc, "INSERT INTO `ticket_notifications` (`ticketid`, `staffid`, `contactid`, `sender_name`, `sender_email`, `subject`, `email_body`, `status`, `created_by`, `send_date`, `follow_up_date`, `log`) VALUES ('$ticketid', '$staffid', '$contactid', '$sender_name', '$sender_email', '$subject', '$body', 'Pending', '".$_SESSION['contactid']."', '$send_date', '$follow_up_date', '')");
		$ticketnotificationid = mysqli_insert_id($dbc);
		$filename = '';
		if(strip_tags($output) != '') {
			$filename = 'download/'.config_safe_str(get_config($dbc, 'ticket_notify_list')).'_'.$ticketnotificationid.'.pdf';
			try {
				include_once('../tcpdf/tcpdf.php');
				$pdf = new TCPDF(TICKET_PDF_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

				$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
				$pdf->setFooterData(array(0,64,0), array(0,64,128));

				$pdf->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
				$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
				$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
				$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

				$pdf->AddPage();
				$pdf->SetFont('helvetica', '', 9);
				$pdf->writeHTML($output, true, false, true, false, '');
				$pdf->Output($filename, 'F');
				$dbc->query("UPDATE `ticket_notifications` SET `attachment` = '$filename' WHERE `ticketnotificationid` = '$ticketnotificationid'");
			} catch (Exception $e) {
				echo 'Unable to create PDF.<br />';
				$filename = '';
			}
		}
		if(empty($send_date)) {
			$recipients = array_merge([$businessid],explode(',', $staffid), explode(',', $contactid));
			$cc_address = get_config($dbc, 'ticket_notify_cc');
			foreach ($recipients as $address) {
				if($address > 0) {
					$address = get_email($dbc, filter_var($address,FILTER_SANITIZE_STRING));
					try {
						send_email([$sender_email=>$sender_name], $address, $cc_address, '', $subject, html_entity_decode($body), $filename);
					} catch(Exception $e) { $log .= "Unable to send e-mail to ".get_contact($dbc, $address).": ".$e->getMessage()."\n"; }
				}
			}
			$log .= "Notification Sent.";
			mysqli_query($dbc, "UPDATE `ticket_notifications` SET `log` = '$log', `status` = 'Sent', `send_date` = '".date('Y-m-d')."' WHERE `ticketnotificationid` = '$ticketnotificationid'");
		} else {
			$log .= "Notification Saved.";
		}
		echo $log;
	} else {
		echo 'No Staff or Contact selected.';
	}
} else if($_GET['action'] == 'complete') {
	$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
	$result = [];
	$ready = mysqli_query($dbc, "SELECT `created_by` `contact`, 'Running Timer' `status` FROM `ticket_timer` WHERE `ticketid`='$ticketid' AND `ticketid` > 0 AND `start_timer_time` > 0 AND `end_time` IS NULL AND `deleted` = 0 UNION
		SELECT `item_id` `contact`, CONCAT('Checked In ',`src_table`) `status` FROM `ticket_attached` WHERE `src_table` IN ('Staff','Staff_Tasks','Members','Clients') AND `item_id` > 0 AND `arrived` != `completed` AND `deleted`=0 AND `ticketid`='$ticketid' AND `ticketid` > 0 UNION
		SELECT `item_id` `contact`, 'Notes Not Complete' `status` FROM `ticket_attached` WHERE `src_table` IN ('Staff') AND `item_id` > 0 AND `deleted`=0 AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `discrepancy`=0 AND `item_id` NOT IN (SELECT `created_by` FROM `ticket_comment` WHERE `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 UNION SELECT `created_by` FROM `client_daily_log_notes` WHERE `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0)");
	if(mysqli_num_rows($ready) > 0 && !isset($_GET['force'])) {
		$result = [success => false,message => ''];
		$result['message'] .= "Unable to complete ".TICKET_NOUN.":\n";
		while($row = mysqli_fetch_assoc($ready)) {
			$result['message'] .= get_contact($dbc, $row['contact']).": ".$row['status']."\n";
		}
	} else {
		$result = [success => true,message => TICKET_NOUN." completed.\n"];
		$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contactid`, `internal_qa_contactid`, `deliverable_contactid`, `sign_off_id`, `sign_off_signature`, `projectid`, `ticket_type` FROM `tickets` WHERE `ticketid`='$ticketid'"));
		include_once('../phpsign/signature-to-image.php');
		$signature = sigJsonToImage(html_entity_decode($ticket['sign_off_signature']));
		if(!file_exists('download')) {
			mkdir('download', 0777, true);
		}
		imagepng($signature, 'download/sign_off_'.$ticketid.'_'.$ticket['sign_off_id'].'.png');
		$auto_status = get_config($dbc, 'auto_archive_complete_tickets');
		if($auto_status != '') {
			mysqli_query($dbc, "UPDATE `tickets` SET `status`='$auto_status' WHERE `ticketid`='$ticketid'");
		}
		$project_lead = 0;
		if($ticket['projectid'] > 0) {
			$project_lead = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `project_lead` FROM `project` WHERE `projectid`='".$ticket['projectid']."'"))['project_lead'];
		}
		$value_config = ','.get_field_config($dbc, 'tickets').',';
		if($ticket['ticket_type'] == '') {
			$ticket_type = get_config($dbc, 'default_ticket_type');
		}
		if(!empty($ticket_type)) {
			$value_config .= get_config($dbc, 'ticket_fields_'.$ticket_type).',';
		}
		if(strpos($value_config, ',Complete Email Users On Complete,') !== FALSE) {
			foreach(explode(',',$ticket['contactid'].','.$ticket['internal_qa_contactid'].','.$ticket['deliverable_contactid'].','.$project_lead) as $staffid) {
				if($staffid > 0) {
					$email = get_email($dbc, $staffid);
					if($email != '') {
						try {
							send_email('',$email,'','',TICKET_NOUN.' #'.$ticketid.' has been completed','You are receiving this email because you were '.($staffid == $project_lead ? 'assigned as the lead on the '.PROJECT_NOUN.' to which '.TICKET_NOUN.' #'.$ticketid.' was attached,' : 'assigned to '.TICKET_NOUN.' #'.$ticketid).' and it has been marked complete.<br />Click <a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticketid.'">here</a> to view the '.TICKET_NOUN.'.','');
							$result['message'] .= "Email sent to $email.\n";
						} catch(Exception $e) {
							$result['message'] .= "Unable to send an email to $email.\n";
						}
					}
				}
			}
		}
	}
	echo json_encode($result);
} else if($_GET['action'] == 'business_address_details') {
	$businessid = filter_var($_GET['business'],FILTER_SANITIZE_STRING);
	$business = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name`, `mailing_address`, `city`, `province`, `postal_code`, `country`, `google_maps_address` FROM `contacts` WHERE `contactid`='$businessid'"));
	$business['name'] = decryptIt($business['name']);
	echo json_encode($business);
} else if($_GET['action'] == 'business_services') {
	echo "<option></option>";
	$businessid = filter_var($_GET['business'],FILTER_SANITIZE_STRING);

	$rate_contact = get_config($dbc, 'rate_card_contact_'.$tab) ?: get_config($dbc, 'rate_card_contact');
	switch($rate_contact) {
		case 'businessid': $rate_contact = filter_var($_GET['businessid'],FILTER_SANITIZE_STRING); break;
		case 'agentid': $rate_contact = filter_var($_GET['agentid'],FILTER_SANITIZE_STRING); break;
		case 'origin:carrier': $rate_contact = filter_var($_GET['carrierid'],FILTER_SANITIZE_STRING); break;
		case 'origin:vendor': $rate_contact = filter_var($_GET['originvendor'],FILTER_SANITIZE_STRING); break;
		case 'destination:vendor': $rate_contact = filter_var($_GET['destvendor'],FILTER_SANITIZE_STRING); break;
	}
	$services = explode('**',mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `services` FROM `rate_card` WHERE `clientid` IN ('$rate_contact', '$businessid') AND `clientid` != '' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') ORDER BY `clientid`='$rate_contact' DESC"))['services']);
	$service_list = [];
	$service_price = [];
	foreach($services as $service) {
		$service = explode('#',$service);
		if($service[0] > 0) {
			$service_list[] = $service[0];
			$service_price[] = $service[1];
		}
	}
	$services = mysqli_query($dbc, "SELECT `serviceid`, `heading` FROM `services` WHERE `serviceid` IN (".implode(',',$service_list).") AND `deleted`=0");
	while($service = mysqli_fetch_assoc($services)) {
		$row_price = 0;
		foreach($service_list as $i => $id) {
			if($service['serviceid'] == $id) {
				$row_price = $service_price[$i];
			}
		}
		echo "<option data-rate-price='".$row_price."' value='".$service['serviceid']."'>".$service['heading']."</option>";
	}
} else if($_GET['action'] == 'addition') {
	$ticketid = filter_var($_GET['src_id'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `tickets` (`ticket_type`, `category`, `businessid`, `clientid`, `siteid`, `location`, `location_address`, `location_google`, `address`, `google_maps`, `site_location`, `lsd`, `location_notes`, `projectid`, `afe_number`, `heading`) SELECT `ticket_type`, `category`, `businessid`, `clientid`, `siteid`, `location`, `location_address`, `location_google`, `address`, `google_maps`, `site_location`, `lsd`, `location_notes`, `projectid`, `afe_number`, CONCAT('Addition to ".TICKET_NOUN." #',`ticketid`,' - ',`heading`) FROM `tickets` WHERE `ticketid`='$ticketid'");
	echo mysqli_insert_id($dbc);
} else if($_GET['action'] == 'po_invoice') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$basename = preg_replace('/[^\.A-Za-z0-9]/','',$_FILES['file']['name']);
	$filename = preg_replace('/(\.[A-Za-z0-9]*)/', '$1', $basename);
	if(!file_exists('download')) {
		mkdir('download', 0777, true);
	}
	for($i = 1; file_exists('download/'.$filename); $i++) {
		$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.$i.')$1', $basename);
	}
	move_uploaded_file($_FILES['file']['tmp_name'],'download/'.$filename);
	mysqli_query($dbc, "UPDATE `ticket_purchase_orders` SET `invoice`='$filename' WHERE `id`='$id'");
	echo $filename;
} else if($_GET['action'] == 'attach_po_invoice') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$basename = preg_replace('/[^\.A-Za-z0-9]/','',$_FILES['file']['name']);
	$filename = preg_replace('/(\.[A-Za-z0-9]*)/', '$1', $basename);
	if(!file_exists('../Purchase Order/download')) {
		mkdir('../Purchase Order/download', 0777, true);
	}
	for($i = 1; file_exists('../Purchase Order/download/'.$filename); $i++) {
		$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.$i.')$1', $basename);
	}
	move_uploaded_file($_FILES['file']['tmp_name'],'../Purchase Order/download/'.$filename);
	mysqli_query($dbc, "UPDATE `purchase_orders` SET `upload`='$filename' WHERE `id`='$id'");
	echo $filename;
} else if($_GET['action'] == 'create_recurrence') {
	$ticketid = filter_var($_POST['ticketid'], FILTER_SANITIZE_STRING);
	$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'"));
	$date = $ticket['to_do_date'];
	if($date == '') {
		$date = date('Y-m-d');
	}
	$dates = [];
	for($i = 1; count($dates) < $_POST['number'] && $i < 100; $i++) {
		switch($_POST['frequency']) {
			case 'quarterly':
				$dates[] = date('Y-m-d',strtotime('+'.($i*3).' month', strtotime($date)));
				break;
			case 'monthly':
				$dates[] = date('Y-m-d',strtotime('+'.$i.' month', strtotime($date)));
				break;
			case 'daily':
				$dates[] = date('Y-m-d',strtotime('+'.$i.' weekdays', strtotime($date)));
				break;
			case 'weekly':
			default:
				$start_date = strtotime('last sunday', strtotime($date.' + 1day'));
				if(count($_POST['recur_days']) == 0) {
					$_POST['recur_days'][] = date('l', strtotime($date));
				}
				foreach($_POST['recur_days'] as $day) {
					$new_date = strtotime('+'.$i.'weeks next '.$day,$start_date);
					if($new_date > strtotime($date) && count($dates) < $_POST['number']) {
						$dates[] = date('Y-m-d',$new_date);
					}
				}
				break;
		}
	}
	foreach($dates as $i => $date) {
		mysqli_query($dbc, "INSERT INTO `tickets` (`ticket_type`,`category`,`businessid`,`clientid`,`projectid`,`siteid`,`location`,`location_address`,`location_google`,`address`,`google_maps`,`site_location`,`lsd`,`location_notes`,`postal_code`,`serviceid`,`total_time`, `service_qty`,`assign_work`,`heading`,`to_do_date`,`to_do_end_date`,`created_by`,`created_date`,`contactid`) SELECT `ticket_type`,`category`,`businessid`,`clientid`,`projectid`,`siteid`,`location`,`location_address`,`location_google`,`address`,`google_maps`,`site_location`,`lsd`,`location_notes`,`postal_code`,`serviceid`, `total_time`,`service_qty`,`assign_work`,CONCAT(`heading`,' - $i'),'$date','$date',`created_by`,`created_date`,`contactid` FROM `tickets` WHERE `ticketid`='$ticketid'");
	}
} else if($_GET['action'] == 'po_invoice') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$basename = preg_replace('/[^\.A-Za-z0-9]/','',$_FILES['file']['name']);
	$filename = preg_replace('/(\.[A-Za-z0-9]*)/', '$1', $basename);
	if(!file_exists('download')) {
		mkdir('download', 0777, true);
	}
	for($i = 1; file_exists('download/'.$filename); $i++) {
		$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.$i.')$1', $basename);
	}
	move_uploaded_file($_FILES['file']['tmp_name'],'download/'.$filename);
	mysqli_query($dbc, "UPDATE `ticket_purchase_orders` SET `invoice`='$filename' WHERE `id`='$id'");
	echo $filename;
} else if($_GET['fill'] == 'add_checklist') {
	$ticketid = filter_var($_POST['ticketid'],FILTER_SANITIZE_STRING);
	$text = filter_var($_POST['new_item'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `ticket_checklist` (`ticketid`, `checklist`) VALUES ('$ticketid', '$text')");
} else if($_GET['action'] == 'multiple') {
	$ticketid = filter_var($_GET['ticket'],FILTER_SANITIZE_STRING);
	$count = filter_var($_GET['count'],FILTER_SANITIZE_STRING);
	$labels = explode(',',get_config($dbc, 'ticket_multiple_labels'));
	if($ticketid > 0 && $count > 1) {
		$count--;
		for($i = 0; $i < $count; $i++) {
			$label = ($labels[$i] != '' ? $labels[$i + 1] : '- '.($i + 2));
			mysqli_query($dbc, "INSERT INTO `tickets` (`main_ticketid`,`sub_ticket`,`ticket_type`,`category`,`businessid`,`contactid`,`clientid`,`siteid`,`location`,`location_address`,`location_google`,`address`,`google_maps`,`site_location`,`lsd`,`location_notes`,`postal_code`,`city`,`projectid`,`salesorderid`,`client_projectid`,`piece_work`,`serviceid`,`total_time`,`service_qty`,`service_estimate`,`sub_heading`,`heading`,`heading_auto`,`project_path`,`milestone_timeline`,`task_available`,`notes`,`fee_name`,`fee_details`,`fee_amt`,`created_date`,`created_by`,`status`,`deleted`,`history`,`police_contact`,`poison_contact`,`non_emergency_contact`,`emergency_contact`,`emergency_notes`,`member_start_time`,`member_end_time`,`afe_number`,`max_capacity`".($i == 0 ? ",`dropoff_name`,`dropoff_address`,`dropoff_city`,`dropoff_postal_code`,`dropoff_link`,`dropoff_date`,`dropoff_order`" : "").",`teamid`,`equipmentid`,`equipment_assignmentid`)
				SELECT `ticketid`,'$label',`ticket_type`,`category`,`businessid`,`contactid`,`clientid`,`siteid`,`location`,`location_address`,`location_google`,`address`,`google_maps`,`site_location`,`lsd`,`location_notes`,`postal_code`,`city`,`projectid`,`salesorderid`,`client_projectid`,`piece_work`,`serviceid`,`total_time`,`service_qty`,`service_estimate`,`sub_heading`,`heading`,`heading_auto`,`project_path`,`milestone_timeline`,`task_available`,`notes`,`fee_name`,`fee_details`,`fee_amt`,`created_date`,`created_by`,`status`,`deleted`,`history`,`police_contact`,`poison_contact`,`non_emergency_contact`,`emergency_contact`,`emergency_notes`,`member_start_time`,`member_end_time`,`afe_number`,`max_capacity`".($i == 0 ? ",`pickup_name`,`pickup_address`,`pickup_city`,`pickup_postal_code`,`pickup_link`,`pickup_date`,`pickup_order`" : "").",`teamid`,`equipmentid`,`equipment_assignmentid` FROM `tickets` WHERE `ticketid`='$ticketid'");
		}
		$label = $labels[0] != '' ? $labels[0] : '- 1';
		mysqli_query($dbc, "UPDATE `tickets` SET `main_ticketid`='$ticketid', `sub_ticket`='$label', `dropoff_name`='', `dropoff_address`='', `dropoff_city`='', `dropoff_postal_code`='', `dropoff_link`='', `dropoff_date`='', `dropoff_order`='' WHERE `ticketid`='$ticketid'");
	}
} else if($_GET['action'] == 'ticket_fields') {
	// Save the settings for ticket fields
	$ticket_fields = filter_var(implode(',',$_POST['fields']),FILTER_SANITIZE_STRING);
	$ticket_type = filter_var($_POST['field_name'],FILTER_SANITIZE_STRING);
	if($ticket_type == 'tickets') {
		set_field_config($dbc, $ticket_type, $ticket_fields);
	} else {
		set_config($dbc, $ticket_type, $ticket_fields);
	}

	// Save the settings for the types of tasks
	$tasks = filter_var($_POST['tasks'],FILTER_SANITIZE_STRING);
	$tasks_name = filter_var($_POST['tasks_name'],FILTER_SANITIZE_STRING);
	set_config($dbc, $tasks_name, $tasks);

	// Save the settings for labels, billing emails, and custom notes
	set_config($dbc, 'ticket_multiple_labels', filter_var($_POST['labels'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_extra_billing_email', filter_var($_POST['billing'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_custom_notes_heading', filter_var($_POST['note_heading'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_custom_notes_type', filter_var(implode('#*#',$_POST['note_types']),FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_individuals', filter_var(implode('#*#',$_POST['individuals']),FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_cancellation_reasons', filter_var(implode('#*#',$_POST['cancel_reasons']),FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_checkout_info', filter_var(implode('#*#',$_POST['checkout_info']),FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_checkout_info_staff', filter_var(implode('#*#',$_POST['checkout_info_staff']),FILTER_SANITIZE_STRING));
	set_config($dbc, 'transport_types', filter_var($_POST['transport_types'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'piece_types', filter_var($_POST['piece_types'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'delivery_types', filter_var($_POST['delivery_types'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'delivery_timeframe_default', filter_var($_POST['delivery_timeframe_default'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_warehouse_start_time', filter_var($_POST['ticket_warehouse_start_time'],FILTER_SANITIZE_STRING));
	set_config($dbc, $_POST['tab_transport_log_contact'], filter_var($_POST['tab_transport_log_contact_value'],FILTER_SANITIZE_STRING));
	set_config($dbc, $_POST['ticket_custom_field'], filter_var($_POST['ticket_custom_field_value'],FILTER_SANITIZE_STRING));
	set_config($dbc, $_POST['ticket_custom_field_values'], filter_var($_POST['ticket_custom_field_values_value'],FILTER_SANITIZE_STRING));
	set_config($dbc, $_POST['transport_destination_contact'], filter_var($_POST['transport_destination_contact_value'],FILTER_SANITIZE_STRING));
	set_config($dbc, $_POST['transport_carrier_category'], filter_var($_POST['transport_carrier_category_value'],FILTER_SANITIZE_STRING));
	set_config($dbc, $_POST['ticket_project_contact'], filter_var($_POST['ticket_project_contact_value'],FILTER_SANITIZE_STRING));
	set_config($dbc, $_POST['ticket_business_contact'], filter_var($_POST['ticket_business_contact_value'],FILTER_SANITIZE_STRING));
	set_config($dbc, $_POST['incomplete_ticket_status'], filter_var($_POST['incomplete_ticket_status_value'],FILTER_SANITIZE_STRING));
	set_config($dbc, $_POST['client_accordion_category'], filter_var($_POST['client_accordion_category_value'],FILTER_SANITIZE_STRING));
	set_config($dbc, $_POST['ticket_tab_locks'], filter_var(implode(',',$_POST['ticket_tab_locks_value']),FILTER_SANITIZE_STRING));
	set_config($dbc, $_POST['rate_card_contact'], filter_var($_POST['rate_card_contact_value'],FILTER_SANITIZE_STRING));
	set_config($dbc, $_POST['ticket_chemical_label'], filter_var($_POST['ticket_chemical_label_value'],FILTER_SANITIZE_STRING));
	set_config($dbc, $_POST['delivery_type_contacts'], filter_var($_POST['delivery_type_contacts_value'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'auto_archive_complete_tickets', filter_var($_POST['auto_archive_complete_tickets'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'delivery_km_service', filter_var($_POST['delivery_km_service'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'incomplete_inventory_reminder_email', filter_var($_POST['incomplete_inventory_reminder_email'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_notify_list', filter_var($_POST['ticket_notify_list'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_notify_pdf_content', filter_var(htmlentities($_POST['ticket_notify_pdf_content']),FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_notify_cc', filter_var($_POST['ticket_notify_cc'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_notify_list_items', filter_var(implode('#*#',$_POST['ticket_notify_list_items']),FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_email_approval', filter_var($_POST['ticket_email_approval'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_approval_status', filter_var($_POST['ticket_approval_status'],FILTER_SANITIZE_STRING));
	if($ticket_type == 'tickets') {
		set_config($dbc, 'ticket_attached_charts', filter_var(implode(',',array_filter($_POST['attached_charts'])),FILTER_SANITIZE_STRING));
		set_config($dbc, 'ticket_auto_create_unscheduled', filter_var(implode(',',$_POST['auto_create_unscheduled'])),FILTER_SANITIZE_STRING);
	} else {
		$tab = explode('ticket_fields_', $ticket_type)[1];
		set_config($dbc, 'ticket_attached_charts_'.$tab, filter_var(implode(',',array_filter($_POST['attached_charts'])),FILTER_SANITIZE_STRING));
		set_config($dbc, 'ticket_auto_create_unscheduled_'.$tab, filter_var(implode(',',$_POST['auto_create_unscheduled'])),FILTER_SANITIZE_STRING);
	}

	$delivery_colors = $_POST['ticket_delivery_colors'];
	if(!is_array($delivery_colors)) {
		$delivery_colors = [$delivery_colors];
	}
	foreach($delivery_colors as $delivery_color) {
		$delivery = explode('*#*', $delivery_color);
		if(!empty($delivery[0])) {
			$delivery_type = $delivery[0];
			$delivery_color = $delivery[1];
			mysqli_query($dbc, "INSERT INTO `field_config_ticket_delivery_color` (`delivery`, `color`) SELECT '$delivery_type', '$delivery_color' FROM (SELECT COUNT(*) rows FROM `field_config_ticket_delivery_color` WHERE `delivery` = '$delivery_type') num WHERE num.rows = 0");
			mysqli_query($dbc, "UPDATE `field_config_ticket_delivery_color` SET `color` = '$delivery_color' WHERE `delivery` = '$delivery_type'");
		}
	}

	set_config($dbc, 'ticket_notes_limit', filter_var($_POST['ticket_notes_limit'],FILTER_SANITIZE_STRING));

	$ticket_summary_hide_positions = $_POST['ticket_summary_hide_positions'];
	if(!is_array($ticket_summary_hide_positions)) {
		$ticket_summary_hide_positions = [$ticket_summary_hide_positions];
	}
	$ticket_summary_hide_positions = filter_var(implode('#*#', array_filter($ticket_summary_hide_positions)),FILTER_SANITIZE_STRING);
	if($ticket_type == 'tickets') {
		set_config($dbc, 'ticket_summary_hide_positions', $ticket_summary_hide_positions);
	} else {
		set_config($dbc, 'ticket_summary_hide_positions_'.filter_var($_POST['tab'],FILTER_SANITIZE_STRING), $ticket_summary_hide_positions);
	}
	set_config($dbc, 'ticket_delivery_time_mintime', filter_var($_POST['ticket_delivery_time_mintime'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_delivery_time_maxtime', filter_var($_POST['ticket_delivery_time_maxtime'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_recurring_status', filter_var($_POST['ticket_recurring_status'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_material_increment', filter_var($_POST['ticket_material_increment'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_notes_alert_role', filter_var($_POST['ticket_notes_alert_role'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_business_contact_add_pos', filter_var($_POST['ticket_business_contact_add_pos'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_staff_travel_default', filter_var($_POST['ticket_staff_travel_default'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_guardian_contact', filter_var($_POST['ticket_guardian_contact_value'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_recurrence_sync_upto', filter_var($_POST['ticket_recurrence_sync_upto'],FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'ticket_field_config') {
	if(is_array($_POST['fields'])) {
		$value = implode(',',$_POST['fields']);
	} else {
		$value = $_POST['fields'];
	}
	set_config($dbc, filter_var($_POST['field_name'],FILTER_SANITIZE_STRING), filter_var($value,FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'ticket_action_fields') {
	set_config($dbc, filter_var($_POST['field_name'],FILTER_SANITIZE_STRING), filter_var(implode(',',$_POST['fields']),FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'ticket_overview_fields') {
	set_config($dbc, filter_var($_POST['field_name'],FILTER_SANITIZE_STRING), filter_var(implode(',',$_POST['fields']),FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'ticket_db') {
	// Save the settings for ticket dashboard fields
	set_field_config($dbc, 'tickets_dashboard', filter_var(implode(',',$_POST['fields']),FILTER_SANITIZE_STRING));
	set_config($dbc, 'tickets_sort', filter_var(implode(',',$_POST['sort']),FILTER_SANITIZE_STRING));
	set_config($dbc, 'tickets_summary', filter_var(','.implode(',',$_POST['summary']).',',FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_slider_button', filter_var($_POST['slider_button'],FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'ticket_summary_security') {
	// Save the settings for ticket summary access
	set_config($dbc, $_POST['security'], filter_var(implode(',',$_POST['summary']),FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'ticket_text') {
	// Save the settings for ticket dashboard fields
	set_config($dbc, filter_var($_POST['config'],FILTER_SANITIZE_STRING), filter_var(htmlentities($_POST['text']),FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'ticket_types') {
	// Save the settings for ticket dashboard fields
	set_config($dbc, 'ticket_tabs', filter_var(implode(',',$_POST['types']),FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_type_tiles', filter_var($_POST['tiles'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'default_ticket_type', filter_var($_POST['default_ticket_type'],FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'ticket_status_list') {
	// Save the settings for ticket dashboard fields
	set_config($dbc, 'ticket_status', filter_var(implode(',',$_POST['tickets']),FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_status_icons', filter_var(implode(',',$_POST['ticket_status_icons'])));
	set_config($dbc, 'task_status', filter_var(implode(',',$_POST['tasks']),FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_status_color', filter_var(implode(',',$_POST['ticket_status_color']),FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'setting_tile') {
	// Save the settings for ticket dashboard fields
	set_config($dbc, filter_var($_POST['field'],FILTER_SANITIZE_STRING), filter_var($_POST['value'],FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'quick_action_settings') {
	// Save the settings for ticket dashboard fields
	set_config($dbc, 'quick_action_icons', filter_var($_POST['quick_action_icons'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_colour_flags', filter_var($_POST['flags'],FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_colour_flag_names', filter_var($_POST['names'],FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'update_max_time') {
	$ticketid = filter_var($_POST['ticketid'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "UPDATE `tickets` LEFT JOIN (SELECT `ticketid`, SEC_TO_TIME(SUM(TIME_TO_SEC(`time_length`))) `time_length` FROM `ticket_time_list` WHERE `time_type`='QA Estimate' AND `deleted`=0 GROUP BY `ticketid`) `time_list` ON `time_list`.`ticketid`=`tickets`.`ticketid` SET `tickets`.`max_qa_time`=`time_list`.`time_length` WHERE `time_list`.`ticketid` = '$ticketid'");
	mysqli_query($dbc, "UPDATE `tickets` LEFT JOIN (SELECT `ticketid`, SEC_TO_TIME(SUM(TIME_TO_SEC(`time_length`))) `time_length` FROM `ticket_time_list` WHERE `time_type`='Completion Estimate' AND `deleted`=0 GROUP BY `ticketid`) `time_list` ON `time_list`.`ticketid`=`tickets`.`ticketid` SET `tickets`.`max_time`=`time_list`.`time_length` WHERE `time_list`.`ticketid` = '$ticketid'");
} else if($_GET['action'] == 'delete_ticket_time') {
	$id = $_POST['id'];
	$table = $_POST['table'];
	$deleted_by = $_SESSION['contactid'];

	if($table == 'ticket_timer') {
	    $date_of_archival = date('Y-m-d');
		mysqli_query($dbc, "UPDATE `ticket_timer` SET `deleted` = 1, `date_of_archival` = '$date_of_archival', `deleted_by` = '$deleted_by' WHERE `tickettimerid` = '$id'");
	} else {
		$ticket_time = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `ticket_time_list` WHERE `id` = '$id'"));
		$ticketid = $ticket_time['ticketid'];
		$type = $ticket_time['time_type'];
		$time_length = $ticket_time['time_length'];

		$ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
		if($type == 'Completion Estimate' && $ticket_time['deleted'] == 0) {
			$max_time = $ticket['max_time'];
			$time_diff = gmdate('H:i:s', strtotime($max_time) - strtotime($time_length));
			mysqli_query($dbc, "UPDATE `tickets` LEFT JOIN (SELECT `ticketid`, SEC_TO_TIME(SUM(TIME_TO_SEC(`time_length`))) `time_length` FROM `ticket_time_list` WHERE `time_type`='Completion Estimate' AND `deleted`=0 GROUP BY `ticketid`) `time_list` ON `time_list`.`ticketid`=`tickets`.`ticketid` SET `tickets`.`max_time`=`time_list`.`time_length` WHERE `time_list`.`ticketid` = '$ticketid'");
		} else if($type == 'QA Estimate' && $ticket_time['deleted'] == 0) {
			$max_qa_time = $ticket['max_qa_time'];
			$time_diff = gmdate('H:i:s', strtotime($max_qa_time) - strtotime($time_length));
			mysqli_query($dbc, "UPDATE `tickets` LEFT JOIN (SELECT `ticketid`, SEC_TO_TIME(SUM(TIME_TO_SEC(`time_length`))) `time_length` FROM `ticket_time_list` WHERE `time_type`='QA Estimate' AND `deleted`=0 GROUP BY `ticketid`) `time_list` ON `time_list`.`ticketid`=`tickets`.`ticketid` SET `tickets`.`max_qa_time`=`time_list`.`time_length` WHERE `time_list`.`ticketid` = '$ticketid'");
		}
	    $date_of_archival = date('Y-m-d');
		mysqli_query($dbc, "UPDATE `ticket_time_list` SET `deleted` = 1, `date_of_archival` = '$date_of_archival', `deleted_by` = '$deleted_by' WHERE `id` = '$id'");
	}
} else if($_GET['action'] == 'add_stop') {
	$ticketid = filter_var($_POST['ticketid'],FILTER_SANITIZE_STRING);
	if(!mysqli_query($dbc, "INSERT INTO `tickets` (`main_ticketid`, `sub_ticket`, `ticket_type`, `category`, `businessid`, `clientid`, `other_ind`, `siteid`, `location`, `location_address`, `location_google`, `address`, `google_maps`, `site_location`, `lsd`, `location_notes`, `postal_code`, `pickup_order`, `city`, `projectid`, `salesorderid`, `client_projectid`, `piece_work`, `preferred_staff`, `contactid`, `service_type`, `service`, `serviceid`, `total_time`,`service_qty`, `service_estimate`, `sub_heading`, `heading`, `heading_auto`, `project_path`, `milestone_timeline`, `assign_work`, `task_available`, `notes`, `internal_qa_date`, `internal_qa_contactid`, `deliverable_date`, `deliverable_contactid`, `max_time`, `max_qa_time`, `spent_time`, `total_days`, `start_time`, `end_time`, `fee_name`, `fee_details`, `fee_amt`, `created_date`, `created_by`, `status`, `po_id`, `flag_colour`, `alerts_enabled`, `status_date`, `deleted`, `history`, `internal_qa_start_time`, `internal_qa_end_time`, `deliverable_start_time`, `deliverable_end_time`, `police_contact`, `poison_contact`, `non_emergency_contact`, `emergency_contact`, `emergency_notes`, `member_start_time`, `member_end_time`, `summary_notes`, `sign_off_id`, `sign_off_signature`, `afe_number`, `attached_image`, `max_capacity`, `equipmentid`, `equipment_assignmentid`, `teamid`, `region`, `classification`, `con_location`, `cancellation`, `mdsr_child_name`, `mdsr_child_dob`, `mdsr_date_of_report`, `mdsr_background_info`, `mdsr_progress`, `mdsr_clinical_impacts`, `mdsr_proposed_goal_areas`, `mdsr_recommendations`)
		SELECT `main_ticketid`, `sub_ticket`, `ticket_type`, `category`, `businessid`, `clientid`, `other_ind`, `siteid`, `location`, `location_address`, `location_google`, `address`, `google_maps`, `site_location`, `lsd`, `location_notes`, `postal_code`, `pickup_order`, `city`, `projectid`, `salesorderid`, `client_projectid`, `piece_work`, `preferred_staff`, `contactid`, `service_type`, `service`, `serviceid`,`total_time`, `service_qty`, `service_estimate`, `sub_heading`, `heading`, `heading_auto`, `project_path`, `milestone_timeline`, `assign_work`, `task_available`, `notes`, `internal_qa_date`, `internal_qa_contactid`, `deliverable_date`, `deliverable_contactid`, `max_time`, `max_qa_time`, `spent_time`, `total_days`, `start_time`, `end_time`, `fee_name`, `fee_details`, `fee_amt`, `created_date`, `created_by`, `status`, `po_id`, `flag_colour`, `alerts_enabled`, `status_date`, `deleted`, `history`, `internal_qa_start_time`, `internal_qa_end_time`, `deliverable_start_time`, `deliverable_end_time`, `police_contact`, `poison_contact`, `non_emergency_contact`, `emergency_contact`, `emergency_notes`, `member_start_time`, `member_end_time`, `summary_notes`, `sign_off_id`, `sign_off_signature`, `afe_number`, `attached_image`, `max_capacity`, `equipmentid`, `equipment_assignmentid`, `teamid`, `region`, `classification`, `con_location`, `cancellation`, `mdsr_child_name`, `mdsr_child_dob`, `mdsr_date_of_report`, `mdsr_background_info`, `mdsr_progress`, `mdsr_clinical_impacts`, `mdsr_proposed_goal_areas`, `mdsr_recommendations` FROM `tickets` WHERE `ticketid`='$ticketid'")) {
		echo mysqli_error($dbc);
	} else {
		echo mysqli_insert_id($dbc);
	}
} else if($_GET['action'] == 'update_inc_rep_email') {
	$email = $_POST['email'];
	set_config($dbc, 'inc_rep_reminder_email', $email);
} else if($_GET['action'] == 'send_inc_rep_reminder') {
	$ticketid = $_POST['ticketid'];
	$subject = $_POST['subject'];
	$body = $_POST['body'];
	$staff = $_POST['staff'];
	if(!is_array($_POST['staff'])) {
		$staff = [$_POST['staff']];
	}
	$sender_name = $_POST['sender_name'];
	$sender_email = $_POST['sender_email'];
	$second_reminder_email = $_POST['second_reminder_email'];
	$second_reminder_date = $_POST['second_reminder_date'];

	$incident_reports = $_POST['incident_reports'];
	if(!is_array($_POST['incident_reports'])) {
		$incident_reports = [$_POST['incident_reports']];
	}
	$incident_reports = implode(',', $incident_reports);

	$project_tabs = get_config($dbc, 'project_tabs');
	if($project_tabs == '') {
	    $project_tabs = 'Client,SR&ED,Internal,R&D,Business Development,Process Development,Addendum,Addition,Marketing,Manufacturing,Assembly';
	}
	$project_tabs = explode(',',$project_tabs);
	$project_vars = [];
	foreach($project_tabs as $item) {
	    $project_vars[preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)))] = $item;
	}
	$incident_reports_email = '';
	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT incident_report_dashboard FROM field_config_incident_report"));
	$value_config_ir = ','.$get_field_config['incident_report_dashboard'].',';
	$incident_reports = mysqli_query($dbc, "SELECT * FROM `incident_report` WHERE `incidentreportid` IN ($incident_reports)");
	$incident_reports_count += mysqli_num_rows($incident_reports);
	if($incident_reports_count > 0) {
	    $incident_reports_email .= "<table border='1'>";
	    $incident_reports_email .= "<tr>";
	        if (strpos($value_config_ir, ','."Program".',') !== FALSE) {
	            $incident_reports_email .= '<th>Program</th>';
	        }
	        if (strpos($value_config_ir, ','."Project Type".',') !== FALSE) {
	            $incident_reports_email .= '<th>'.PROJECT_NOUN.' Type</th>';
	        }
	        if (strpos($value_config_ir, ','."Project".',') !== FALSE) {
	            $incident_reports_email .= '<th>'.PROJECT_NOUN.'</th>';
	        }
	        if (strpos($value_config_ir, ','."Ticket".',') !== FALSE) {
	            $incident_reports_email .= '<th>'.TICKET_NOUN.'</th>';
	        }
	        if (strpos($value_config_ir, ','."Member".',') !== FALSE) {
	            $incident_reports_email .= '<th>Member</th>';
	        }
	        if (strpos($value_config_ir, ','."Client".',') !== FALSE) {
	            $incident_reports_email .= '<th>Client</th>';
	        }
	        if (strpos($value_config_ir, ','."Type".',') !== FALSE) {
	            $incident_reports_email .= '<th>Type</th>';
	        }
	        if (strpos($value_config_ir, ','."Staff".',') !== FALSE) {
	            $incident_reports_email .= '<th>Staff</th>';
	        }
	        if (strpos($value_config_ir, ','."Follow Up".',') !== FALSE) {
	            $incident_reports_email .= '<th>Follow Up</th>';
	        }
	        if (strpos($value_config_ir, ','."Date of Happening".',') !== FALSE) {
	            $incident_reports_email .= '<th>Date of Happening</th>';
	        }
	        if (strpos($value_config_ir, ','."Date Created".',') !== FALSE) {
	            $incident_reports_email .= '<th>Date Created</th>';
	        }
	        if (strpos($value_config_ir, ','."Location".',') !== FALSE) {
	            $incident_reports_email .= '<th>Location</th>';
	        }
	        if (strpos($value_config_ir, ','."PDF".',') !== FALSE) {
	            $incident_reports_email .= '<th>View</th>';
	        }
	    $incident_reports_email .= "</tr>";

	    while($row = mysqli_fetch_array( $incident_reports ))
	    {
	        $contact_list = [];
	        if ($row['contactid'] != '') {
	            $contact_list[$row['contactid']] = get_staff($dbc, $row['contactid']);
	        }
	        $attendance_list = [];
	        if ($row['attendance_staff'] != '') {
	            $attendance_list = explode(',', $row['attendance_staff']);
	        }
	        foreach($attendance_list as $attendee) {
	            $contact_list[] = $attendee;
	        }
	        if ($row['completed_by'] != '') {
	            $contact_list[] = get_contact($dbc, $row['completed_by']);
	        }
	        $contact_list = array_unique($contact_list);
	        $contact_list = implode(', ', $contact_list);

	        $incident_reports_email .= "<tr>";

	        if (strpos($value_config_ir, ','."Program".',') !== FALSE) {
	            $incident_reports_email .= '<td data-title="Program">'.(!empty(get_client($dbc, $row['programid'])) ? get_client($dbc, $row['programid']) : get_contact($dbc, $row['programid'])).'</td>';
	        }
            if (strpos($value_config_ir, ','."Project Type".',') !== FALSE) {
                echo '<td data-title="'.PROJECT_NOUN.' Type">'.$project_vars[$row['project_type']].'</td>';
            }
	        if (strpos($value_config_ir, ','."Project".',') !== FALSE) {
	            $project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '".$row['projectid']."'"));
	            $incident_reports_email .= '<td data-title="'.PROJECT_NOUN.'">'.get_project_label($dbc, $project).'</td>';
	        }
	        if (strpos($value_config_ir, ','."Ticket".',') !== FALSE) {
	            $ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$row['ticketid']."'"));
	            $incident_reports_email .= '<td data-title="'.TICKET_NOUN.'">'.get_ticket_label($dbc, $ticket).'</td>';
	        }
	        if (strpos($value_config_ir, ','."Member".',') !== FALSE) {
	            $incident_reports_email .= '<td data-title="Member">';
	                $member_list = [];
	                foreach(explode(',',$row['memberid']) as $member) {
	                    if($member != '') {
	                        $member_list[] = !empty(get_client($dbc, $member)) ? get_client($dbc, $member) : get_contact($dbc, $member);
	                    }
	                }
	                $incident_reports_email .= implode(', ',$member_list) . '</td>';
	        }
	        if (strpos($value_config_ir, ','."Client".',') !== FALSE) {
	            $incident_reports_email .= '<td data-title="Client">';
	                $client_list = [];
	                foreach(explode(',',$row['clientid']) as $client) {
	                    if($client != '') {
	                        $client_list[] = !empty(get_client($dbc, $client)) ? get_client($dbc, $client) : get_contact($dbc, $client);
	                    }
	                }
	                $incident_reports_email .= implode(', ',$client_list) . '</td>';
	        }
	        if (strpos($value_config_ir, ','."Type".',') !== FALSE) {
	            $incident_reports_email .= '<td data-title="Type">' . $row['type'] . '</td>';
	        }
	        if (strpos($value_config_ir, ','."Staff".',') !== FALSE) {
	            $incident_reports_email .= '<td data-title="Staff">' . $contact_list . '</td>';
	        }
	        if (strpos($value_config_ir, ','."Follow Up".',') !== FALSE) {
	            if($row['type'] == 'Near Miss') {
	                $incident_reports_email .= '<td data-title="Follow Up">N/A</td>';
	            } else {
	                $incident_reports_email .= '<td data-title="Follow Up">' . $row['ir14'] . '</td>';
	            }
	        }
	        if (strpos($value_config_ir, ','."Date of Happening".',') !== FALSE) {
	            $incident_reports_email .= '<td data-title="Date of Happening">' . $row['date_of_happening'] . '</td>';
	        }
	        if (strpos($value_config_ir, ','."Date Created".',') !== FALSE) {
	            $incident_reports_email .= '<td data-title="Date Created">' . $row['today_date'] . '</td>';
	        }
	        if (strpos($value_config_ir, ','."Location".',') !== FALSE) {
	            $incident_reports_email .= '<td data-title="Location">' . $row['location'] . '</td>';
	        }
	        if (strpos($value_config_ir, ','."PDF".',') !== FALSE) {
	            $name_of_file = 'incident_report_'.$row['incidentreportid'].'.pdf';
				$incident_reports_email .= '<td data-title="PDF"><a href="'.WEBSITE_URL.'/Incident Report/view_pdf_from_email.php?incidentreportid='.$row['incidentreportid'].'&ticketid='.$ticketid.'" target="_blank" ><img src="'.WEBSITE_URL.'/img/pdf.png" width="16" height="16" border="0" alt="View">View</a>';
	            $incident_reports_email .= '</td>';
	        }

	        $incident_reports_email .= "</tr>";
	    }
	    $incident_reports_email .= '</table>';
	}

	$ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
	$body = str_replace(['[TICKETID]','[HEADING]','[INCIDENT_REPORTS]'], [$ticketid,get_ticket_label($dbc, $ticket),$incident_reports_email],$body);
	$staff = array_unique(array_filter($staff));
	foreach($staff as $address) {
		mysqli_query($dbc, "INSERT INTO `incident_report_reminders` (`ticketid`, `staffid`, `subject`, `body`, `sender_name`, `sender_email`, `second_reminder_email`, `second_reminder_date`) VALUES ('$ticketid', '$address', '$subject', '".filter_var(htmlentities($body),FILTER_SANITIZE_STRING)."', '$sender_name', '$sender_email', '$second_reminder_email', '$second_reminder_date')");
		$address = get_email($dbc, filter_var($address,FILTER_SANITIZE_STRING));
		try {
			send_email([$sender_email=>$sender_name], $address, '', '', $subject, $body, '');
		} catch(Exception $e) { echo "Unable to send e-mail: ".$e->getMessage(); }
	}
	echo "\nReminders sent.";
} else if($_GET['action'] == 'ticket_sort_order') {
	$field_name = $_POST['field_name'];
	$blocks = json_decode($_POST['blocks']);
	$sort_order = filter_var(implode(',', $blocks),FILTER_SANITIZE_STRING);
	set_config($dbc, $field_name, $sort_order);
} else if($_GET['action'] == 'ticket_fields_sort_order') {
	$field_name = $_POST['field_name'];
	$accordion = $_POST['accordion'];
	$blocks = json_decode($_POST['blocks']);
	$sort_order = filter_var(implode(',', $blocks),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `field_config_ticket_fields` (`ticket_type`, `accordion`) SELECT '$field_name', '$accordion' FROM (SELECT COUNT(*) rows FROM `field_config_ticket_fields` WHERE `ticket_type` = '$field_name' AND `accordion` = '$accordion') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `field_config_ticket_fields` SET `fields` = '$sort_order' WHERE `ticket_type` = '$field_name' AND `accordion` = '$accordion'");
} else if($_GET['action'] == 'ticket_fields_sort_order_action') {
	$field_name = $_POST['field_name'];
	$accordion = $_POST['accordion'];
	$blocks = json_decode($_POST['blocks']);
	$sort_order = filter_var(implode(',', $blocks),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `field_config_ticket_fields_action` (`ticket_type`, `accordion`) SELECT '$field_name', '$accordion' FROM (SELECT COUNT(*) rows FROM `field_config_ticket_fields_action` WHERE `ticket_type` = '$field_name' AND `accordion` = '$accordion') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `field_config_ticket_fields_action` SET `fields` = '$sort_order' WHERE `ticket_type` = '$field_name' AND `accordion` = '$accordion'");
} else if($_GET['action'] == 'get_ticket_label') {
	$ticketid = $_GET['ticketid'];
	if($ticketid > 0) {
		$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'"));
		$ticket_label = get_ticket_label($dbc, $ticket);
		if($_GET['include_site'] == 1 && $ticket['siteid'] > 0) {
			$contact = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '{$ticket['siteid']}'"));
			$site_name = trim(decryptIt($contact['name']).($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').decryptIt($contact['first_name']).' '.decryptIt($contact['last_name']).' '.(empty($contact['display_name']) ? $contact['site_name'] : $contact['display_name']));
			$ticket_label .= ' - '.$site_name;
		}
		echo $ticket_label;
	}
} else if($_GET['action'] == 'new_ticket_from_calendar') {
	$to_do_date = $_POST['to_do_date'];
	$to_do_end_date= $_POST['to_do_end_date'];
	$to_do_start_time = $_POST['to_do_start_time'];
	$to_do_end_time= $_POST['to_do_end_time'];
	$equipmentid = $_POST['equipmentid'];
	$contactid = $_POST['contactid'];
	if(!is_array($contactid)) {
		$contactid = [$contactid];
	}
	$contactid = ','.implode(',',$contactid).',';
	$region = $_POST['region'];
	$con_location = $_POST['con_location'];
	$classification = $_POST['classification'];
	$status = $_POST['status'];
	$ticket_type = $_POST['ticket_type'];
	$businessid = filter_var($_POST['businessid'],FILTER_SANITIZE_STRING);
	$projectid = filter_var($_POST['projectid'],FILTER_SANITIZE_STRING);
	$milestone = filter_var($_POST['milestone'],FILTER_SANITIZE_STRING);

	if($businessid > 0 && !($projectid > 0) &&  get_config($dbc, 'ticket_project_function') == 'business_project') {
		$projectid = $dbc->query("SELECT `projectid` FROM `project` WHERE `deleted`=0 AND `businessid`='$value'")->fetch_assoc();
		if(!($projectid > 0)) {
			if(get_config($dbc, 'project_status_pending') == '') {
				$status = 'Pending';
			} else {
				$status = explode('#*#',get_config($dbc, 'project_status'))[0];
			}
			$category = BUSINESS_CAT;
			$projecttype = explode(',',get_config($dbc, 'project_tabs'));
			if(in_array($category,$projecttype)) {
				$projecttype = $category;
			} else {
				$projecttype = $projecttype[0];
			}
			$dbc->query("INSERT INTO `project` (`businessid`,`project_name`,`status`,`projecttype`,`created_date`,`created_by`) VALUES ('$value','".get_contact($dbc, $value, 'name_company')."','$status','$projecttype',DATE(NOW()),'{$_SESSION['contactid']}')");
			$projectid = $dbc->insert_id;
		}
	}

	// Insert the details of the ticket
	mysqli_query($dbc, "INSERT INTO `tickets` (`to_do_date`, `to_do_end_date`, `to_do_start_time`, `to_do_end_time`, `equipmentid`, `contactid`, `region`, `con_location`, `classification`, `status`,`ticket_type`,`businessid`,`projectid`,`milestone_timeline`) VALUES ('$to_do_date', '$to_do_end_date', '$to_do_start_time', '$to_do_end_time', '$equipmentid', '$contactid', '$region', '$con_location', '$classification', '$status','$ticket_type','$businessid','$projectid','$milestone')");
	$ticketid = mysqli_insert_id($dbc);

	// Create the first scheduled stop
	$scheduled_stop = $_POST['scheduled_stop'];
	if($scheduled_stop == 1) {
		$stop_equipmentid = $_POST['stop_equipmentid'];
		$stop_to_do_date = $_POST['stop_to_do_date'];
		$stop_to_do_start_time = $_POST['stop_to_do_start_time'];
		$stop_address = $_POST['stop_address'];
		$stop_city = $_POST['stop_city'];
		$stop_postal = $_POST['stop_postal'];
		mysqli_query($dbc, "INSERT INTO `ticket_schedule` (`ticketid`, `equipmentid`, `to_do_date`, `to_do_start_time`, `address`, `city`, `postal_code`) VALUES ('$ticketid', '$stop_equipmentid', '$stop_to_do_date', '$stop_to_do_start_time', '$stop_address', '$stop_city', '$stop_postal')");
		$ticket_schedule_id = mysqli_insert_id($dbc);
	}

	// Output the row ids
	echo $ticketid.'*#*'.$ticket_schedule_id;
} else if($_GET['action'] == 'ticket_log_config') {
	$template = $_POST['template'];
	if($template == 'template_a') {
		$header = filter_var(htmlentities($_POST['header']),FILTER_SANITIZE_STRING);
		$footer = filter_var(htmlentities($_POST['footer']),FILTER_SANITIZE_STRING);
		$fields = filter_var(implode(',', json_decode($_POST['fields'])),FILTER_SANITIZE_STRING);

		mysqli_query($dbc, "INSERT INTO `field_config_ticket_log` (`template`) SELECT 'template_a' FROM (SELECT COUNT(*) rows FROM `field_config_ticket_log` WHERE `template` = 'template_a') num WHERE num.rows = 0");
		mysqli_query($dbc, "UPDATE `field_config_ticket_log` SET `header` = '$header', `footer` = '$footer', `fields` = '$fields' WHERE `template` = 'template_a'");

		if(!file_exists('download')) {
	        mkdir('download', 0777, true);
		}
		if(!empty($_FILES['header_logo']['name'])) {
	        $header_logo = $basename = preg_replace('/[^a-z0-9.]*/','',strtolower($_FILES['header_logo']['name']));
	        $j = 0;
	        while(file_exists('download/'.$header_logo)) {
	            $header_logo = preg_replace('/(\.[a-z0-9]*)/', ' ('.++$j.')$1', $basename);
	        }
	        move_uploaded_file($_FILES['header_logo']['tmp_name'], 'download/'.$header_logo);
	        mysqli_query($dbc, "UPDATE `field_config_ticket_log` SET `header_logo` = '$header_logo' WHERE `template` = 'template_a'");
	        echo "header_logo*#*download/".$header_logo;
		}
	}
} else if($_GET['action'] == 'ticket_log_delete_logo') {
	$template = $_POST['template'];
	$logo = $_POST['logo'];
	if($logo == 'header') {
		mysqli_query($dbc, "UPDATE `field_config_ticket_log` SET `header_logo` = '' WHERE `template` = '$template'");
	}
} else if($_GET['action'] == 'ticket_log_template') {
	$template = $_POST['template'];
	set_config($dbc, 'ticket_log_template', $template);
} else if($_GET['action'] == 'get_address') {
	$contactid = filter_var($_POST['contactid'],FILTER_SANITIZE_STRING);
	$addresses = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `payment_address`, `payment_city`, `payment_state`, `payment_zip_code`, `business_address`, `business_city`, `business_state`, `business_zip`, `ship_to_address`, `ship_city`, `ship_state`, `ship_zip` FROM `contacts` WHERE `contactid`='$contactid'"));
	if($_POST['address'] == 'payment' && $addresses['payment_address'].$addresses['payment_city'].$addresses['payment_state'].$addresses['payment_zip_code'] == '') {
		$_POST['address'] = 'business';
	}
	if($_POST['address'] == 'shipping' && $addresses['ship_to_address'].$addresses['ship_city'].$addresses['ship_state'].$addresses['ship_zip'] == '') {
		$_POST['address'] = 'business';
	}
	if($_POST['address'] == 'mailing' && $addresses['ship_to_address'].$addresses['ship_city'].$addresses['ship_state'].$addresses['ship_zip'] == '') {
		$_POST['address'] = 'business';
	}
	if($_POST['address'] == 'business' && $addresses['business_address'].$addresses['business_city'].$addresses['business_state'].$addresses['business_zip'] == '') {
		$_POST['address'] = '';
	}
	switch($_POST['address']) {
		case 'business': $sql = "SELECT `business_address` `address`, `business_street` `street`, `business_city` `city`, `business_state` `province`, `business_country` `country`, `business_zip` `postal`, `google_maps_address` `map_link` FROM `contacts` WHERE `contactid`='$contactid'"; break;
		case 'payment': $sql = "SELECT `payment_address` `address`, `payment_address` `street`, `payment_city` `city`, `payment_state` `province`, `payment_zip_code` `postal`, '' `map_link` FROM `contacts` WHERE `contactid`='$contactid'"; break;
		case 'shipping': $sql = "SELECT `mailing_address` `full_address`, `ship_to_address` `address`, `ship_to_address` `street`, `ship_city` `city`, `ship_state` `province`, `ship_country` `country`, `ship_zip` `postal`, `ship_google_link` `map_link` FROM `contacts` WHERE `contactid`='$contactid'"; break;
		default: $sql = "SELECT `address`, `address` `street`, `city`, `province`, `country`, `postal_code` `postal` FROM `contacts`, `google_maps_address` `map_link` WHERE `contactid`='$contactid'"; break;
	}
	$address = mysqli_fetch_assoc(mysqli_query($dbc, $sql));
	if($_POST['address'] == 'business') {
		$address['address'] = decryptIt($address['address']);
		$address['street'] = decryptIt($address['street']);
		$address['city'] = decryptIt($address['city']);
		$address['province'] = decryptIt($address['province']);
		$address['country'] = decryptIt($address['country']);
		$address['postal'] = decryptIt($address['postal']);
	}
	echo json_encode($address);
} else if($_GET['action'] == 'get_category_list') {
	$category = filter_var(($_POST['category'] ?: $_GET['category']),FILTER_SANITIZE_STRING);
	echo '<option></option>';
	foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `name`, `first_name`, `last_name` FROM `contacts` WHERE `deleted`=0 AND `status` > 0 AND `category` LIKE '$category'")) as $contact) {
		echo '<option value="'.$contact['contactid'].'">'.$contact['full_name'].'</option>';
	}
} else if($_GET['action'] == 'archive') {
    $date_of_archival = date('Y-m-d');
	$ticketid = filter_var($_POST['ticketid'], FILTER_SANITIZE_STRING);
	$dbc->query("UPDATE `tickets` SET `status`='Archive', `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `ticketid`='$ticketid' AND `ticketid` > 0");
	if($_POST['delete_recurrences'] == 1) {
		$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
		$main_ticketid = $ticket['main_ticketid'];
		if($main_ticketid > 0) {
			mysqli_query($dbc, "UPDATE `tickets` SET `status`='Archive', `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `main_ticketid` = '$main_ticketid' AND `is_recurrence` = 1");
			mysqli_query($dbc, "UPDATE `ticket_recurrences` SET `deleted`=1 WHERE `ticketid` = '$main_ticketid'");
		}
	}
} else if($_GET['action'] == 'contact_address') {
	$contactid = filter_var($_POST['contactid'], FILTER_SANITIZE_STRING);
	$address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
	$city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);
	$province = filter_var($_POST['province'], FILTER_SANITIZE_STRING);
	$postal = filter_var($_POST['postal'], FILTER_SANITIZE_STRING);
	$country = filter_var($_POST['country'], FILTER_SANITIZE_STRING);
	$google_link = filter_var($_POST['google_link'], FILTER_SANITIZE_STRING);
	$dbc->query("UPDATE `contacts` SET `ship_to_address`='$address', `ship_city`='$city', `ship_state`='$province', `ship_zip`='$postal', `ship_country`='$country', `ship_google_link`='$google_link' WHERE `contactid`='$contactid'");
} else if($_GET['action'] == 'update_custom_accordion_name') {
	$ticket_type = $_POST['field_name'];
	$old_name = $_POST['old_name'];
	$new_name = $_POST['new_name'];
	if(!empty($new_name)) {
		mysqli_query($dbc, "UPDATE `field_config_ticket_fields` SET `accordion` = '$new_name' WHERE `ticket_type` = '$ticket_type' AND `accordion` = '$old_name'");
	}
} else if($_GET['action'] == 'remove_custom_accordion') {
	$ticket_type = $_POST['field_name'];
	$name = $_POST['name'];
	if(strpos($name, 'FFMCUST_') !== FALSE) {
		mysqli_query($dbc, "DELETE FROM `field_config_ticket_fields` WHERE `ticket_type` = '$ticket_type' AND `accordion` = '$name'");
	}
} else if($_GET['action'] == 'ticket_higher_level_headings') {
	$ticket_type = $_POST['field_name'];
	$heading_name = $_POST['heading_name'];
	$heading_accordions = $_POST['heading_accordions'];
	if(!empty($heading_name)) {
		mysqli_query($dbc, "DELETE FROM `field_config_ticket_headings` WHERE `ticket_type` = '$ticket_type' AND `heading` = '$heading_name'");
		foreach($heading_accordions as $heading_accordion) {
			mysqli_query($dbc, "INSERT INTO `field_config_ticket_headings` (`ticket_type`, `heading`, `accordion`) VALUES ('$ticket_type', '$heading_name', '$heading_accordion')");
		}
	}
} else if($_GET['action'] == 'update_higher_level_heading') {
	$ticket_type = $_POST['field_name'];
	$old_name = $_POST['old_name'];
	$new_name = $_POST['new_name'];
	if(!empty($new_name)) {
		mysqli_query($dbc, "UPDATE `field_config_ticket_headings` SET `heading` = '$new_name' WHERE `ticket_type` = '$ticket_type' AND `heading` = '$old_name'");
	}
} else if($_GET['action'] == 'remove_higher_level_heading') {
	$ticket_type = $_POST['field_name'];
	$name = $_POST['name'];
	mysqli_query($dbc, "DELETE FROM `field_config_ticket_headings` WHERE `ticket_type` = '$ticket_type' AND `heading` = '$name'");
} else if($_GET['action'] == 'update_ticket_accordion_name') {
	$ticket_type = $_POST['field_name'];
	$accordion = $_POST['accordion'];
	$accordion_name = $_POST['accordion_name'];
	mysqli_query($dbc, "INSERT INTO `field_config_ticket_accordion_names` (`ticket_type`, `accordion`, `accordion_name`) SELECT '$ticket_type', '$accordion', '$accordion_name' FROM (SELECT COUNT(*) rows FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '$field_name' AND `accordion` = '$accordion') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `field_config_ticket_accordion_names` SET `accordion_name` = '$accordion_name' WHERE `ticket_type` = '$ticket_type' AND `accordion` = '$accordion'");
	echo $accordion_name;
} else if($_GET['action'] == 'inventory_reminder') {
	if($_POST['id'] > 0) {
		$inventory = $dbc->query("SELECT * FROM `ticket_attached` WHERE `id`='{$_POST['id']}'")->fetch_assoc();
		$ticket = $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='{$inventory['ticketid']}'")->fetch_assoc();
		$email = get_config($dbc, 'incomplete_inventory_reminder_email');
		$from = decryptIt($_SESSION[STAFF_EMAIL_FIELD]);
		$subject = "General Cargo not Completed";
		$body = 'This is a reminder that the General Cargo for '.get_ticket_label($dbc, $ticket).'.<br />Please <a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].'">click here</a> to log in and review the '.TICKET_NOUN.'.';
		send_email($from, $email, '', '', $subject, $body);
	}
} else if($_GET['action'] == 'add_ticket_reminder') {
	$ticketid = $_POST['ticket'];
	$staff = $_POST['staff'];
	if(!is_array($staff)) {
		$staff = [$staff];
	}
	echo $staff;
	$reminder = TICKET_NOUN.' #'.$ticketid.' Reminder: '.$_POST['reminder'];
	$reminder_date = $_POST['reminder_date'];
	foreach($staff as $staffid) {
		if($staffid > 0) {
	        mysqli_query($dbc, "UPDATE `reminders` SET `done` = 1 WHERE `contactid` = '$staffid' AND `src_table` = 'tickets' AND `src_tableid` = '$ticketid'");
		    mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_type`, `subject`, `src_table`, `src_tableid`) VALUES ('$staffid', '$reminder_date', 'TICKET', '$reminder', 'tickets', '$ticketid')");
		}
	}
} else if($_GET['action'] == 'manual_update') {
	$table_name = filter_var($_POST['table_name'], FILTER_SANITIZE_STRING);
	$field_name = filter_var($_POST['field_name'], FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'], FILTER_SANITIZE_STRING);
	$ticketid = filter_var($_POST['ticketid'], FILTER_SANITIZE_STRING);
	$identifier = filter_var($_POST['identifier'], FILTER_SANITIZE_STRING);
	$id = filter_var($_POST['id'], FILTER_SANITIZE_STRING);
	$dbc->query("UPDATE `$table_name` SET `$field_name`='$value' WHERE `ticketid`='$ticketid' AND `$identifier`='$id'");
} else if($_GET['action'] == 'quick_actions') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	if($field == 'flag_colour') {
		$colours = explode(',', get_config($dbc, "ticket_colour_flags"));
		$labels = explode('#*#', get_config($dbc, "ticket_colour_flag_names"));
		$colour_key = array_search($value, $colours);
		$new_colour = ($colour_key === FALSE ? $colours[0] : ($colour_key + 1 < count($colours) ? $colours[$colour_key + 1] : 'FFFFFF'));
		$label = ($colour_key === FALSE ? $labels[0] : ($colour_key + 1 < count($colours) ? $labels[$colour_key + 1] : ''));
		echo $new_colour.html_entity_decode($label);
		mysqli_query($dbc, "UPDATE `tickets` SET `flag_colour`='$new_colour', `flag_start`='0000-00-00', `flag_end`='9999-12-31' WHERE `ticketid`='$id'");
	} else if($field == 'manual_flag_colour') {
		$flag_label = filter_var($_POST['label'],FILTER_SANITIZE_STRING);
		$flag_start = filter_var($_POST['start'],FILTER_SANITIZE_STRING);
		$flag_end = filter_var($_POST['end'],FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "UPDATE `tickets` SET `flag_colour`='$value', `flag_start`='$flag_start', `flag_end`='$flag_end' WHERE `ticketid`='$id'");
		mysqli_query($dbc, "UPDATE `ticket_comment` SET `deleted`=1, `date_of_archival`=DATE(NOW()) WHERE `ticketid`='$id' AND `type`='flag_comment'");
		if(!empty($flag_label)) {
			mysqli_query($dbc, "INSERT INTO `ticket_comment` (`ticketid`,`type`,`comment`,`created_date`,`created_by`) VALUES ('$id','flag_comment','$flag_label',DATE(NOW()),'".$_SESSION['contactid']."')");
		}
	} else if($field == 'document') {
		$folder = 'download';
		$basename = preg_replace('/[^\.A-Za-z0-9]/','',$_FILES['file']['name']);
		$filename = preg_replace('/(\.[A-Za-z0-9]*)/', '$1', $basename);
		for($i = 1; file_exists($folder.$filename); $i++) {
			$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.$i.')$1', $basename);
		}
		move_uploaded_file($_FILES['file']['tmp_name'],$folder.$filename);
		mysqli_query($dbc, "INSERT INTO `ticket_document` (`ticketid`,`document`,`created_by`,`created_date`) VALUES ('$id','$filename','".$_SESSION['contactid']."',DATE(NOW()))");
	} else if($field == 'reminder') {
		$sender = get_email($dbc, $_SESSION['contactid']);
		$milestone = ($result['milestone'] != '' ? $result['milestone'] : ($result['milestone_timeline'] != '' ? $result['milestone_timeline'] : $result['project_milestone']));
		$subject = "A reminder about a ".TICKET_NOUN;
		foreach($_POST['users'] as $i => $user) {
			$user = filter_var($user,FILTER_SANITIZE_STRING);
			$contacts = mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$user'");
			$body = filter_var(htmlentities("This is a reminder about a ".TICKET_NOUN.".<br />\n<br />
				<a href='".WEBSITE_URL."/Ticket/index.php?edit=$id'>Click here</a> to see the ".TICKET_NOUN.".<br />\n<br />
				$item"), FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "UPDATE `reminders` SET `done` = 1 WHERE `contactid` = '$user' AND `src_table` = 'tickets' AND `src_tableid` = '".$id."' AND `src_table` != '' AND `src_table` IS NOT NULL");
			$result = mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_time`, `reminder_type`, `subject`, `body`, `sender`, `src_table`, `src_tableid`)
				VALUES ('$user', '$value', '08:00:00', 'QUICK', '$subject', '$body', '$sender', 'tickets', '".$id."')");
		}
	} else if($field == 'alert') {
		$item = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$id'"));
		foreach($_POST['value'] as $user) {
			$user = filter_var($user,FILTER_SANITIZE_STRING);
			$link = WEBSITE_URL."/Ticket/index.php?edit=$id";
			$text = TICKET_NOUN;
			$date = date('Y/m/d');
			$sql = mysqli_query($dbc, "INSERT INTO `alerts` (`alert_date`, `alert_link`, `alert_text`, `alert_user`) VALUES ('$date', '$link', '$text', '$user')");
		}
	} else if($field == 'email') {
		$sender = get_email($dbc, $_SESSION['contactid']);
		$result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$id'"));
		$subject = "A reminder about a ".TICKET_NOUN;
		foreach($_POST['value'] as $user) {
			$user = get_email($dbc,$user);
			$body = "This is a reminder about a ".TICKET_NOUN.".<br />\n<br />
				<a href='".WEBSITE_URL."/Ticket/index.php?edit=$id'>Click here</a> to see the ".TICKET_NOUN.".<br />\n<br />
				$item";
			send_email($sender, $user, '', '', $subject, $body, '');
		}
	} else if($field == 'emailpdf') {
        $ticketid = $id;
        $filename = "download/output_".($ticketid > 0 ? $ticketid : 'new_'.config_safe_str(TICKET_NOUN))."_".date('Y_m_d').".pdf";
        $get_ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'"));

        $notes = mysqli_query($dbc, "SELECT `ticket_comment`.*, `tickets`.`ticket_type` FROM ticket_comment LEFT JOIN `tickets` ON `ticket_comment`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_comment`.ticketid='$ticketid' AND `ticket_comment`.type='note' AND `ticket_comment`.`deleted`=0 ORDER BY ticketcommid DESC");
        $html = '';
        $notes_count = mysqli_num_rows($notes);

        $html .= '<h1>'.get_ticket_label($dbc, mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$id'"))).'</h1>';

        if($ticketid > 0 && $notes_count > 0) {

            $html .= '<table border="1" cellpadding="2">';
            $html .= '<tr>';
            $html .= '<td><center><h3>Notes</h3></center></td>';
            $html .= '</tr>';
            while($row = mysqli_fetch_array($notes)) {
                $html .= '<tr><td>';
                $html .= html_entity_decode($row['comment'].$row['note']);
                $html .= "<em>Added by ".get_contact($dbc, $row['created_by'])." at ".$row['note_date'].$row['created_date'];
                if($row['reference_contact'] > 0) {
                    $html .= "<br />References ".get_contact($dbc, $row['reference_contact']);
                }
                if($row['client_id'] > 0) {
                    $html .= "<br />References ".get_contact($dbc, $row['client_id']);
                }
                foreach(explode(',',$row['email_comment']) as $assignid) {
                    if($assignid > 0) {
                        $html .= "<br />Assigned to ".get_contact($dbc, $assignid);
                    }
                }
                $html .= "</em>";
                $html .= '</td></tr>';
            }
            $html .= '</table>';
        } else if($ticketid > 0) {
            $html .= "<h4>No Notes Found</h4>";
        }

        class MYPDF extends TCPDF {

            public function Header() {
            }

            // Page footer
            public function Footer() {
                // Position at 15 mm from bottom
                $this->SetY(-15);
                $this->SetFont('helvetica', '', 9);
                $footer_text = '<p style="text-align:right;">Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages().'</p>';
                $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "R", true);
            }
        }
        $pdf = new MYPDF(TICKET_PDF_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
        $pdf->setFooterData(array(0,64,0), array(0,64,128));

        $pdf->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 9);

        //Add here commented code
        $pdf->writeHTML($html);
        $pdf->Output($filename, 'F');

		$sender = get_email($dbc, $_SESSION['contactid']);
		$result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$id'"));
		$subject = TICKET_NOUN.' PDF attached';
        $body = "Please see attachment";
        $user = $_POST['value'];
        $body = "Please see attachment";
        $email_attachments = 'download/'.$filename.'#FFM#';

        send_email($sender, $user, '', '', $subject, $body, $filename);

        /*
        $get_project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='{$get_ticket['projectid']}'"));

        $ticket_types = [];
        $value_config = ','.get_field_config($dbc, 'tickets').',';
        $sort_order = explode(',',get_config($dbc, 'ticket_sortorder'));
        foreach(explode(',',get_config($dbc, 'ticket_tabs')) as $ticket_type) {
            $ticket_types[config_safe_str($ticket_type)] = $ticket_type;
        }
        $ticket_type = $_GET['ticket_type'];
        if(!empty($get_ticket['ticket_type'])) {
            $ticket_type = $get_ticket['ticket_type'] ?: get_config($dbc, 'default_ticket_type');
        }
        if($ticket_type == '') {
            // $value_config .= get_config($dbc, 'ticket_fields_%', true).',';
            foreach($ticket_types as $type_i => $type_label) {
                $value_config .= get_config($dbc, 'ticket_fields_'.$type_i).',';
            }
        } else {
            $value_config .= get_config($dbc, 'ticket_fields_'.$ticket_type).',';
            $sort_order = explode(',',get_config($dbc, 'ticket_sortorder_'.$ticket_type));
        }

        //Accordion Sort Order
        foreach ($accordion_list as $accordion_field => $accordion_field_fields) {
            if(!in_array($accordion_field, $sort_order)) {
                $sort_order[] = $accordion_field;
            }
        }

        DEFINE('HEADER_LEFT', html_entity_decode(str_replace(['[TICKET_TYPE]','[TICKETID]','[TO_DO_DATE]'],[$ticket_types[$get_ticket['ticket_type']],$ticketid,$get_ticket['to_do_date']],get_config($dbc, 'ticket_pdf_header_left'))));
        DEFINE('HEADER_CENTER', html_entity_decode(str_replace(['[TICKET_TYPE]','[TICKETID]','[TO_DO_DATE]'],[$ticket_types[$get_ticket['ticket_type']],$ticketid,$get_ticket['to_do_date']],get_config($dbc, 'ticket_pdf_header_center'))));
        DEFINE('HEADER_RIGHT', html_entity_decode(str_replace(['[TICKET_TYPE]','[TICKETID]','[TO_DO_DATE]'],[$ticket_types[$get_ticket['ticket_type']],$ticketid,$get_ticket['to_do_date']],get_config($dbc, 'ticket_pdf_header_right'))));
        DEFINE('FOOTER_TEXT', html_entity_decode(str_replace(['[TICKET_TYPE]','[TICKETID]','[TO_DO_DATE]'],[$ticket_types[$get_ticket['ticket_type']],$ticketid,$get_ticket['to_do_date']],get_config($dbc, 'ticket_pdf_footer'))));
        DEFINE('PDF_LOGO_ALIGN', !empty(get_config($dbc, 'ticket_pdf_logo_align')) ? get_config($dbc, 'ticket_pdf_logo_align') : 'C');
        DEFINE ('TICKET_PDF_ORIENTATION', !empty(get_config($dbc, 'ticket_pdf_orientation')) ? get_config($dbc, 'ticket_pdf_orientation') : 'P');

        class MYPDF extends TCPDF {

            public function Header() {
                $this->SetY(10);
                $this->SetFont('helvetica', '', 9);
                $this->setCellHeightRatio(0.6);
                $this->writeHTMLCell(0, 0, 10, 20, HEADER_LEFT, 0, 0, false, "L", true);

                $this->SetY(10);
                $this->setCellHeightRatio(0.6);
                $footer_text = '<p style="text-align:center;">'.HEADER_CENTER.'</p>';
                $this->writeHTMLCell(0, 0, 0 , 10, $footer_text, 0, 0, false, "R", true);

                $this->SetY(10);
                $this->setCellHeightRatio(0.6);
                $footer_text = '<p style="text-align:right;">'.HEADER_RIGHT.'</p>';
                $this->writeHTMLCell(0, 0, 0 , 10, $footer_text, 0, 0, false, "R", true);

            }

            // Page footer
            public function Footer() {
                // Position at 15 mm from bottom
                $this->SetY(-15);
                $this->SetFont('helvetica', '', 9);
                $footer_text = '<p style="text-align:right;">Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages().'</p>';
                $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "R", true);
                $this->SetY(-15);
                $this->SetFont('helvetica', '', 9);
                $this->writeHTMLCell(0, 0, '', '', FOOTER_TEXT, 0, 0, false, "L", true);
            }
        }


        $pdf = new MYPDF(TICKET_PDF_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
        $pdf->setFooterData(array(0,64,0), array(0,64,128));

        $pdf->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 9);

        ob_clean();

        $_GET['edit'] = $ticketid;
        //unset($ticketid);
        $force_readonly = true;

        $pdf_header_color = get_calendar_today_color($dbc);

        $global_value_config = $value_config;

        $generate_pdf = true;
        $pdf_contents = [];
        $ticketid = $id;
        foreach($sort_order as $sort_field) {
            $value_config = $global_value_config;

            //Custom accordions
            if(strpos($value_config, ','.$sort_field.',') !== FALSE && substr($sort_field, 0, strlen('FFMCUST_')) === 'FFMCUST_') {
                $_GET['tab'] = str_replace(' ','_',$sort_field);
                $acc_label = explode('FFMCUST_',$sort_field)[1];
                include('edit_ticket_tab.php');
            }
            if (strpos($value_config, ','."Information".',') !== FALSE && $sort_field == 'Information') {
                $_GET['tab'] = 'project_info';
                $acc_label = PROJECT_NOUN.' Information';
                include('edit_ticket_tab.php');
            }
            if (strpos($value_config, ','."Details".',') !== FALSE && $sort_field == 'Details') {
                $_GET['tab'] = 'project_details';
                $acc_label = PROJECT_NOUN.' Details';
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
            if(strpos($value_config, ',Staff,') !== FALSE && $sort_field == 'Staff') {
                $_GET['tab'] = 'ticket_staff_list';
                $acc_label = 'Staff';
                include('edit_ticket_tab.php');
            }
            if(strpos($value_config, ',Staff Tasks,') !== FALSE && $sort_field == 'Staff Tasks') {
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
            if (strpos($value_config, ','."Delivery".',') !== FALSE && $sort_field == 'Delivery') {
                $_GET['tab'] = 'ticket_delivery';
                $acc_label = 'Delivery Details';
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
            if (strpos($value_config, ','."Timer".',') !== FALSE && $sort_field == 'Timer') {
                $_GET['tab'] = 'view_ticket_timer';
                $acc_label = 'Time Tracking';
                include('edit_ticket_tab.php');
                $collapse_i++;
            }
            if (strpos($value_config, ','."Timer".',') !== FALSE && $access_all > 0 && $sort_field == 'Timer') {
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
            if (strpos($value_config, ','."Notes".',') !== FALSE && $sort_field == 'Notes') {
                $_GET['tab'] = 'notes_view_ticket_comment';
                $acc_label = TICKET_NOUN.' Notes';
                include('edit_ticket_tab.php');
            }
            if ((strpos($value_config, ','."Summary".',') !== FALSE || strpos($value_config, ','."Staff Summary".',') !== FALSE) && $sort_field == 'Summary') {
                $_GET['tab'] = 'ticket_summary';
                $acc_label = strpos($value_config, ','."Staff Summary".',') !== FALSE ? 'Staff Summary' : 'Summary';
                include('edit_ticket_tab.php');
            }
            if (strpos($value_config, ','."Multi-Disciplinary Summary Report".',') !== FALSE && $sort_field == 'Multi-Disciplinary Summary Report') {
                $_GET['tab'] = 'view_multi_disciplinary_summary_report';
                $acc_label = 'Multi Disciplinary Summary Notes';
                include('edit_ticket_tab.php');
            }
            if (strpos($value_config, ','."Complete".',') !== FALSE && $sort_field == 'Complete') {
                $_GET['tab'] = 'ticket_complete';
                $acc_label = 'Complete '.TICKET_NOUN;
                include('edit_ticket_tab.php');
            }
            if (strpos($value_config, ','."Notifications".',') !== FALSE && $sort_field == 'Notifications') {
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
            if (strpos($value_config, ','."Other List".',') !== FALSE && $sort_field == 'Other List') {
                $_GET['tab'] = 'ticket_other_list';
                $acc_label = 'Other List';
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
        }
        ob_clean();
        $ticketid = $id;
        $html = '';
        $html .= '<h1>'.get_ticket_label($dbc, mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$id'"))).'</h1>';
        $html .= '<table border="1" cellpadding="2">';
        foreach($pdf_contents as $line) {
            $img = $line[2];
            $header = $line[0];
            $line = $line[1];
            if($header == '**HEADING**') {
                $html .= '<tr><td class="pdf_header" style="text-align: center; background-color: #'.$pdf_header_color.'; color: #fff; font-weight: bold; width: 100%; border: 1px solid black;">'.$line.'</td></tr>';
            } else {
                if($img != 'img') {
                    $line = preg_replace('/<img((?!>).)*>/','',$line);
                }
                $line = preg_replace('/<button((?!\/button>).)*\/button>/','',$line);
                if(strpos($line,'form-group')) {
                    $line = str_replace('class="form-group','style="margin:0;padding:0;display:block;width:900px;" class="',$line);
                } else {
                    $line = str_replace('div','span',$line);
                }
                $line = str_replace('class="form-control"','style="width:100%;"',$line);
                $line = str_replace('class="form-control datepicker"','style="width:100%;"',$line);
                $line = str_replace('select ','select style="width:100%;"',$line);
                $line = str_replace('textarea','div',$line);
                $line = str_replace('class="col-sm-4','style="width:25%;" class="',$line);
                $line = str_replace('class="control-label col-sm-4','style="width:25%;display:inline-block;" class="',$line);
                $line = str_replace('class="col-sm-8','style="width:74%;" class="',$line);

                $width = 100;
                $html .= '<tr>';
                if(!empty($header)) {
                    $width = 70;
                    $html .= '<td class="pdf_label" style="width: 30%; border: 1px solid black; display: inline;">'.$header.'</td>';
                }
                $html .= '<td class="pdf_content" style="width: '.$width.'%; border: 1px solid black; display: inline;">'.($ticketid>0 ? $line : '').'</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</table>';

        */
	}
} else if($_GET['action'] == 'get_locks') {
	$ticketid = $_GET['ticketid'];
	$tab = filter_var($_GET['tab'],FILTER_SANITIZE_STRING);
	if($ticketid > 0) {
		$output = ['locked'=>'','users'=>[]];
		$locks = $dbc->query("SELECT `user_id`, `active` FROM `table_locks` WHERE `table_name`='tickets' AND `table_row_id`='$ticketid' AND `tab_name`='$tab' AND `locked_at` LIKE '".date('Y-m-d')."%' AND TIME_TO_SEC(NOW()) - TIME_TO_SEC(locked_at) < 300");
		while($lock = $locks->fetch_assoc()) {
			if($lock['active'] > 0) {
				$output['locked'] = $lock['user_id'];
			}
			$output['users'][] = $lock['user_id'];
		}
		echo json_encode($output);
		if($output['locked'] > 0) {
			$dbc->query("INSERT INTO `table_locks` (`table_name`, `tab_name`, `user_id`, `table_row_id`, `active`) SELECT 'tickets', '$tab', '{$_SESSION['contactid']}', '$ticketid', '0' FROM `table_locks` WHERE `table_row_id`='$ticketid' AND `tab_name`='$tab' AND `user_id`='{$_SESSION['contactid']}' AND `table_name`='tickets' HAVING COUNT(*)=0");
			$dbc->query("UPDATE `table_locks` SET `active`='0', `locked_at`=CURRENT_TIMESTAMP WHERE `ticketid`='$ticketid' AND `tab`='$tab' AND `user_id`='{$_SESSION['contactid']}'");
		} else {
			$dbc->query("INSERT INTO `table_locks` (`table_name`, `tab_name`, `user_id`, `table_row_id`, `active`) SELECT 'tickets', '$tab', '{$_SESSION['contactid']}', '$ticketid', '1' FROM `table_locks` WHERE `table_row_id`='$ticketid' AND `tab_name`='$tab' AND `user_id`='{$_SESSION['contactid']}' AND `table_name`='tickets' HAVING COUNT(*)=0");
		}
	}
} else if($_GET['action'] == 'release_locks') {
	$ticketid = $_GET['ticketid'];
	$tab = filter_var($_GET['tab'],FILTER_SANITIZE_STRING);
	if($ticketid > 0) {
		$dbc->query("INSERT INTO `table_locks` (`table_name`, `tab_name`, `user_id`, `table_row_id`, `active`) SELECT 'tickets', '$tab', '{$_SESSION['contactid']}', '$ticketid', '0' FROM `table_locks` WHERE `table_row_id`='$ticketid' AND `tab_name`='$tab' AND `user_id`='{$_SESSION['contactid']}' AND `table_name`='tickets' HAVING COUNT(*)=0");
		$dbc->query("UPDATE `table_locks` SET `active`='0', `locked_at`=CURRENT_TIMESTAMP WHERE `ticketid`='$ticketid' AND `tab`='$tab' AND `user_id`='{$_SESSION['contactid']}'");
	}
} else if($_GET['action'] == 'ticket_pdf_logo') {
    if(!empty($_FILES['ticket_pdf_logo']['name'])) {
		if(!file_exists('download')) {
			mkdir('download', 0777, true);
		}
        $ticket_pdf_logo = $basename = preg_replace('/[^a-z0-9.]*/','',strtolower($_FILES['ticket_pdf_logo']['name']));
        $j = 0;
        while(file_exists('download/'.$ticket_pdf_logo)) {
            $ticket_pdf_logo = preg_replace('/(\.[a-z0-9]*)/', ' ('.++$j.')$1', $basename);
        }
        move_uploaded_file($_FILES['ticket_pdf_logo']['tmp_name'], 'download/'.$ticket_pdf_logo);
        set_config($dbc, 'ticket_pdf_logo', $ticket_pdf_logo);
        echo "download/".$ticket_pdf_logo;
    }
} else if($_GET['action'] == 'import_templates') {
	$businessid = $_POST['business'];
	if($businessid > 0) {
		$columns = filter_var($_POST['column_list'],FILTER_SANITIZE_STRING);
		set_config($dbc, 'ticket_import_'.$businessid, $columns);
	}
} else if($_GET['action'] == 'ticket_import_bus') {
	set_config($dbc, 'ticket_import_bus', $_POST['value']);
} else if($_GET['action'] == 'ticket_import_filters') {
	set_config($dbc, 'ticket_import_filters', $_POST['value']);
} else if($_GET['action'] == 'template_add_field') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$page = filter_var($_POST['page'],FILTER_SANITIZE_STRING);
	$dbc->query("INSERT INTO `ticket_pdf_fields` (`pdf_type`,`page`,`x`,`y`,`width`,`height`) VALUES ('$id', '$page','5','5','50','5')");
} else if($_GET['action'] == 'template_field') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	$dbc->query("UPDATE `ticket_pdf_fields` SET `$field`='$value' WHERE `id`='$id'");
} else if($_GET['action'] == 'template_setting') {
	$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	if($_POST['id'] > 0) {
		$dbc->query("UPDATE `ticket_pdf` SET `$field`='$value' WHERE `id`='{$_POST['id']}'");
	} else {
		$dbc->query("INSERT INTO `ticket_pdf` (`$field`) VALUES ('$value')");
		echo $dbc->insert_id;
	}
} else if($_GET['action'] == 'template_file') {
	$filename = file_safe_str($_FILES['file']['name'],'pdf_contents/');
	if(!file_exists('pdf_contents')) {
		mkdir('pdf_contents', 0777, true);
	}
	move_uploaded_file($_FILES['file']['tmp_name'],'pdf_contents/'.$filename);
	if($_POST['id'] > 0) {
		$id = $_POST['id'];
		$pages = array_filter(explode('#*#',$dbc->query("SELECT `pages` FROM `ticket_pdf` WHERE `id`='$id'")->fetch_assoc()['pages']));
		$pages[] = $filename;
		$dbc->query("UPDATE `ticket_pdf` SET `pages`='".implode('#*#',$pages)."' WHERE `id`='$id'");
		foreach($pages as $i => $page) {
			echo '<a href="?settings=forms&id='.$id.'&page='.($i + 1).'"><img src="pdf_contents/'.$page.'" style="width: 30%; margin: 2em;"></a>';
		}
	} else {
		$dbc->query("INSERT INTO `ticket_pdf` (`pages`) VALUES '$filename'");
		$id = $dbc->insert_id;
		echo '<a href="?settings=forms&id='.$id.'&page=1"><img src="pdf_contents/'.$filename.'" style="width: 30%; margin: 2em;"></a>';
		echo '#*#'.$id;
	}
} else if($_GET['action'] == 'get_customer_service_templates') {
	echo '<option></option>';
	$clientid = $_GET['clientid'];
	$customer_templates = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '$clientid'"))['service_templates'];
	if(!empty($customer_templates)) {
		foreach(explode(',',$customer_templates) as $customer_template) {
			$customer_template = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services_service_templates` WHERE `templateid` = '$customer_template'"));
			if(!empty($customer_template)) { ?>
				<option value="<?= $customer_template['templateid'] ?>"><?= $customer_template['name'] ?></option>
			<?php }
		}
	}
} else if($_GET['action'] == 'ticket_service_checklist') {
	$ticketid = $_POST['ticketid'];
	$staffid = $_POST['staffid'];
	$serviceid = $_POST['serviceid'];
	$checked = $_POST['checked'];
	$checked_by = $_SESSION['contactid'];
	$index = $_POST['index'];
	$today_date = date('Y-m-d');

	if($checked == 1) {
		mysqli_query($dbc, "INSERT INTO `ticket_service_checklist` (`ticketid`, `contactid`, `serviceid`, `checked_date`, `index`, `checked_by`) VALUES ('$ticketid', '$staffid', '$serviceid', '$today_date', '$index', '$checked_by')");
		$marked = 'complete';
	} else {
	        $date_of_archival = date('Y-m-d');
    	mysqli_query($dbc, "UPDATE `ticket_service_checklist` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `ticketid` = '$ticketid' AND `contactid` = '$staffid' AND `serviceid` = '$serviceid' AND `index` = '$index'");
		$marked = 'incomplete';
	}

	$history = get_contact($dbc, $staffid).' marked '.$marked.' by '.get_contact($dbc, $checked_by).' at '.date('Y-m-d h:i a').'.<br>';
	mysqli_query($dbc, "INSERT INTO `ticket_service_checklist_history` (`ticketid`, `serviceid`, `index`) SELECT '$ticketid', '$serviceid', '$index' FROM (SELECT COUNT(*) rows FROM `ticket_service_checklist_history` WHERE `ticketid` = '$ticketid' AND `serviceid` = '$serviceid' AND `index` = '$index') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `ticket_service_checklist_history` SET `history` = CONCAT(`history`, '$history') WHERE `ticketid` = '$ticketid' AND `serviceid` = '$serviceid' AND `index` = '$index'");
} else if($_GET['action'] == 'add_service_extra_billing') {
	$ticketid = filter_var($_POST['ticketid'],FILTER_SANITIZE_STRING);
	$comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);

	mysqli_query($dbc, "INSERT INTO `ticket_comment` (`ticketid`, `type`, `comment`, `created_date`, `created_by`) VALUES ('$ticketid', 'service_extra_billing', '$comment', '".date('Y-m-d')."', '".$_SESSION['contactid']."')");
} else if($_GET['action'] == 'ticket_service_checklist_history') {
	$ticketid = $_POST['ticketid'];
	$serviceid = $_POST['serviceid'];
	$index = $_POST['index'];

	$history = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_service_checklist_history` WHERE `ticketid` = '$ticketid' AND `serviceid` = '$serviceid' AND `index` = '$index' ORDER BY `id` ASC"))['history'];
	$history = rtrim($history, '<br>');
	echo $history;
} else if($_GET['action'] == 'revert_to_admin') {
	if($_POST['ticketid'] > 0) {
		$ticketid = $_POST['ticketid'];
		$dbc->query("UPDATE `tickets` SET `approvals`=NULL WHERE `ticketid`='$ticketid'");
	}
} else if($_GET['action'] == 'ticket_invoice') {
	include('../Invoice/create_ticket_invoice.php');
} else if($_GET['action'] == 'task_types') {
	foreach($_POST['tasks'] as $sort => $data) {
		$cat = filter_var($data['category'],FILTER_SANITIZE_STRING);
		$task = filter_var($data['task'],FILTER_SANITIZE_STRING);
		$details = filter_var($data['details'],FILTER_SANITIZE_STRING);
		if($data['id'] > 0) {
			$sql = "UPDATE `task_types` SET `category`='$cat', `description`='$task', `details`='$details', `sort`='$sort' WHERE `id`='{$data['id']}'";
		} else {
			$sql = "INSERT INTO `task_types` (`category`, `description`, `sort`) VALUES ('$cat','$task','$sort')";
		}
		$dbc->query($sql);
		if($dbc->insert_id > 0) {
			echo $dbc->insert_id;
		}
	}
} else if($_GET['action'] == 'get_contact_service_category') {
	$ticketid = $_GET['ticketid'];
	$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
	$service_cat = '';
	if(!empty($ticket['clientid'])) {
		$service_cat = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `service_category` FROM `contacts` WHERE `contactid` = '{$ticket['clientid']}'"))['service_category'];
	}
	echo $service_cat;
} else if($_GET['action'] == 'add_another_room_service_checklist') {
	$ticketid = $_POST['ticketid'];
	$copy_values = $_POST['copy_values'];
	$services = $_POST['services'];

	$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
	$ticket_services = explode(',',$ticket['serviceid']);
	$ticket_service_qty = explode(',',$ticket['service_qty']);
	$new_service_indexes = [];

	foreach($ticket_services as $i => $ticket_service) {
		foreach($services as $service) {
			$service_arr = explode('#*#', $service);
			$serviceid = $service_arr[0];
			$service_qty = $service_arr[1];
			if($serviceid == $ticket_service) {
				$new_service_indexes[$serviceid] = $ticket_service_qty[$i];
				$ticket_service_qty[$i] = ($ticket_service_qty[$i]+1);
			}
		}
	}
	$ticket_service_qty = implode(',',$ticket_service_qty);
	mysqli_query($dbc, "UPDATE `tickets` SET `service_qty` = '$ticket_service_qty' WHERE `ticketid` = '$ticketid'");

	if($copy_values == 1) {
		foreach($services as $service) {
			$service_arr = explode('#*#', $service);
			$serviceid = $service_arr[0];
			$service_qty = $service_arr[1];
			$query = mysqli_query($dbc, "SELECT * FROM `ticket_service_checklist` WHERE `ticketid` = '$ticketid' AND `serviceid` = '$serviceid' AND `index` = '$service_qty' AND `deleted` = 0");
			while($row = mysqli_fetch_assoc($query)) {
				if($new_service_indexes[$serviceid] > 0) {
					$service_qty = $new_service_indexes[$serviceid];
				}
				mysqli_query($dbc, "INSERT INTO `ticket_service_checklist` (`ticketid`, `contactid`, `serviceid`, `index`, `checked_date`, `checked_by`) VALUES ('$ticketid', '".$row['contactid']."', '$serviceid', '".($service_qty+1)."', '".date('Y-m-d')."', '".$_SESSION['contactid']."')");
				$history = get_contact($dbc, $row['contactid']).' marked complete by '.get_contact($dbc, $_SESSION['contactid']).' at '.date('Y-m-d h:i a').'.<br>';
				mysqli_query($dbc, "INSERT INTO `ticket_service_checklist_history` (`ticketid`, `serviceid`, `index`, `history`) VALUES ('$ticketid', '$serviceid', '".($service_qty+1)."', '$history')");
			}
		}
	}
} else if($_GET['action'] == 'customer_sign_off_complete_status') {
	$id = $_POST['id'];
	$status = get_config($dbc, 'auto_archive_complete_tickets');

	mysqli_query($dbc, "UPDATE `ticket_schedule` SET `status` = '$status', `complete` = 1 WHERE `id` = '$id'");
	echo $status;
} else if($_GET['action'] == 'create_recurring_ticket') {
	$ticketid = $_POST['ticketid'];
	$recurring_status = get_config($dbc, 'ticket_recurring_status');
	$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
	if($recurring_status == $ticket['status']) {
		mysqli_query($dbc, "INSERT INTO `tickets` (`ticket_type`, `category`, `businessid`, `clientid`, `siteid`, `projectid`, `heading`, `created_date`, `created_by`, `status`, `region`, `classification`, `con_location`)
			SELECT `ticket_type`, `category`, `businessid`, `clientid`, `siteid`, `projectid`, `heading`, '".date('Y-m-d')."', '".$_SESSION['contactid']."', '$recurring_status', `region`, `classification`, `con_location` FROM `tickets` WHERE `ticketid`='$ticketid'");
		echo "Recurring ".TICKET_NOUN." created (".TICKET_NOUN.' #'.mysqli_insert_id($dbc).")";
	}
} else if($_GET['action'] == 'update_ticket_total_budget_time') {
	$ticketid = $_POST['ticketid'];
	$time = $_POST['time'];
	if($ticketid > 0) {
		mysqli_query($dbc, "UPDATE `tickets` SET `total_budget_time` = '$time' WHERE `ticketid` = '$ticketid'");
	}

	$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
	if($ticket['total_budget_time'] != '00:00:00' && !empty($ticket['total_budget_time'])) {
		$total_budget_time = time_time2decimal($ticket['total_budget_time']);
		$total_staff_time = 0;
		$staff_hours = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `src_table` LIKE '%Staff%' AND `deleted` = 0 AND `ticketid` = '".$ticket['ticketid']."'");
		while($staff_hour = mysqli_fetch_assoc($staff_hours)) {
			if($staff_hour['hours_tracked'] > 0) {
				$total_staff_time += $staff_hour['hours_tracked'];
			} else if(!empty($staff_hour['checked_out']) && !empty($staff_hour['checked_in'])) {
				$total_staff_time += (time_time2decimal($staff_hour['checked_out']) - time_time2decimal($staff_hour['checked_in']));
			}
		}
		$total_budget_time_exceeded = number_format($total_staff_time - $total_budget_time,2);
	} else {
		$total_budget_time_exceeded = 0;
	}

	if($total_budget_time_exceeded > 0) {
		echo "Total Budget Time exceeded by $total_budget_time_exceeded hours.";
	}
} else if($_GET['action'] == 'get_customer_image') {
	$id = $_POST['id'];
	$field = $_POST['field'];
	$value = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `$field` FROM `ticket_attached` WHERE `id` = '$id'"))[$field];

	if(!empty($value)) {
		echo '<a href="download/'.$value.'" target="_blank"><img src="download/'.$value.'" style="max-width: 20em; max-height: 20em; border: 1px solid black;"></a>';
	}
} else if($_GET['action'] == 'get_service_time_estimate') {
	$ticketid = $_POST['ticketid'];

	$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
	$serviceids = explode(',', $ticket['serviceid']);
	$service_qtys = explode(',', $ticket['service_qty']);

	$time_est = 0;
	foreach($serviceids as $i => $serviceid) {
		$service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid` = '$serviceid'"));
		$estimated_hours = empty($service['estimated_hours']) ? '00:00' : $service['estimated_hours'];
		$qty = empty($service_qtys[$i]) ? 1 : $service_qtys[$i];
		$minutes = explode(':', $estimated_hours);
		$minutes = ($minutes[0]*60) + $minutes[1];
		$minutes = $qty * $minutes;
		$time_est += $minutes;
	}
	$new_hours = $time_est / 60;
	$new_minutes = $time_est % 60;
	$new_hours = sprintf('%02d', $new_hours);
	$new_minutes = sprintf('%02d', $new_minutes);
	$time_est = $new_hours.':'.$new_minutes;

	echo $time_est;
} else if($_GET['action'] == 'set_stamp') {
	$basename = filter_var($_FILES['file']['name']);
	$filename = preg_replace('/(\.[A-Za-z0-9]*)/', '$1', preg_replace('/[^\.A-Za-z0-9]/','',$basename));
	$i = 0;
	if(!file_exists('download')) {
		mkdir('download', 0777, true);
	}
	while(file_exists('download/'.$filename)) {
		$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.++$i.')$1', preg_replace('/[^\.A-Za-z0-9]/','',$basename));
	}
	move_uploaded_file($_FILES['file']['tmp_name'],'download/'.$filename);
	set_config($dbc, 'stamp_upload', $filename);
} else if($_GET['action'] == 'add_delete_note') {
	$ticketid = filter_var($_POST['ticketid'],FILTER_SANITIZE_STRING);
	$note = filter_var(htmlentities($_POST['note']),FILTER_SANITIZE_STRING);

	mysqli_query($dbc, "INSERT INTO `ticket_comment` (`ticketid`,`type`,`comment`,`created_date`,`created_by`) VALUES ('$ticketid','note','$note',DATE(NOW()),'{$_SESSION['contactid']}')");
} else if($_GET['action'] == 'create_recurrence_tickets') {
	//Initialize variables
	$start_date = $_POST['start_date'];
	$end_date = $_POST['end_date'];
	$repeat_type = $_POST['repeat_type'];
	$repeat_monthly = $_POST['repeat_monthly'];
	$repeat_interval = $_POST['repeat_interval'];
	$repeat_days = $_POST['repeat_days'];
	$result = [success => false, message => $start_date.$end_date.$repeat_type.$repeat_interval.implode(',',$repeat_days)];
	if($_GET['validate'] == 1) {
		//Validate form fields
		$validated = true;
		$validate_errors = [];
		$start_date_arr = explode('-',$start_date);
		$end_date_arr = explode('-',$end_date);
		if(!checkdate($start_date_arr[1], $start_date_arr[2], $start_date_arr[0])) {
			$validated = false;
			$validate_errors[] = "Invalid Start Date";
		}
		if(strtotime($start_date) > strtotime($end_date) && !empty(str_replace(['0000-00-00','1969-12-31'],'',$end_date))) {
			$validated = false;
			$validate_errors[] = "Start Date greater than End Date";
		}
		if(!in_array($repeat_type, ['day','week','month'])) {
			$validated = false;
			$validate_errors[] = "Invalid Repeat Type";
		}
		if(!($repeat_interval > 0)) {
			$validated = false;
			$validate_errors[] = "Invalid Repeat Interval";
		}
		if(($repeat_type == 'week' && empty($repeat_days)) || ($repeat_type == 'month' && $repeat_monthly != 'day' && empty($repeat_days))) {
			$validated = false;
			$validate_errors[] = "No repeat days selected";
		}
		if($validated) {
			//If validated, get the first 10 recurring dates and send it as a response so the user can verify that the dates are correct
			$ongoing_recurrence = false;
			if(empty(str_replace(['0000-00-00','1969-12-31'],'',$end_date))) {
				$sync_upto = !empty(get_config($dbc, 'ticket_recurrence_sync_upto')) ? get_config($dbc, 'ticket_recurrence_sync_upto') : '2 years';
				$end_date = date('Y-m-d', strtotime(date('Y-m-d').' + '.$sync_upto));
				$ongoing_recurrence = true;
			}
			$recurring_dates = get_recurrence_days(10, $start_date, $end_date, $repeat_type, $repeat_interval, $repeat_days, $repeat_monthly);
			$validate_message = "You are creating Recurring ".TICKET_TILE." every ".$repeat_interval. " ".$repeat_type.($repeat_interval > 1 ? "s" : "")." from ".$start_date.($ongoing_recurrence ? " ongoing" : " until ".$end_date).". Here is an example of what the following Recurring dates will look like:\n\n".implode(", ", $recurring_dates).(count($recurring_dates) > 10 ? ", ..." : "")."\n\nIf this is correct, please confirm to create your Recurring ".TICKET_TILE.".";
			$result = [success=>true, message=>$validate_message, first_date=>array_shift($recurring_dates)];
		} else {
			//If validate fails, return the message
			$result = [success=>false, message=>'Error: '.implode(', ',$validate_errors).'.'];
		}
		echo json_encode($result);
	} else {
		$ticketid = $_POST['ticketid'];

		if($ticketid > 0) {
			//Insert into ticket_recurrences table to save settings for ongoing Recurrences cron job
			mysqli_query($dbc, "INSERT INTO `ticket_recurrences` (`ticketid`, `start_date`, `end_date`, `repeat_type`, `repeat_monthly`, `repeat_interval`, `repeat_days`) VALUES ('$ticketid', '$start_date', '$end_date', '$repeat_type', '$repeat_monthly', '$repeat_interval', '".implode(',',$repeat_days)."')");

		    //Set main_ticketid and main_ids for records so they can be synced together
		    mysqli_query($dbc, "UPDATE `tickets` SET `main_ticketid` = '$ticketid', `is_recurrence` = 1 WHERE `ticketid` = '$ticketid'");
		    mysqli_query($dbc, "UPDATE `ticket_attached` SET `main_id` = `id`, `is_recurrence` = 1 WHERE `ticketid` = '$ticketid' AND `deleted` = 0");
		    mysqli_query($dbc, "UPDATE `ticket_schedule` SET `main_id` = `id`, `is_recurrence` = 1 WHERE `ticketid` = '$ticketid' AND `deleted` = 0");
		    mysqli_query($dbc, "UPDATE `ticket_comment` SET `main_id` = `ticketcommid`, `is_recurrence` = 1 WHERE `ticketid` = '$ticketid' AND `deleted` = 0");

			create_recurring_tickets($dbc, $ticketid, $start_date, $end_date, $repeat_type, $repeat_interval, $repeat_days, $repeat_monthly, $_POST['skip_first']);
			sync_recurring_tickets($dbc, $ticketid);
			echo 'Successfully created Recurring '.TICKET_TILE;
		}
	}
} else if($_GET['action'] == 'removePdfForm') {
	$form = filter_var($_POST['formid'],FILTER_SANITIZE_STRING);
	$ticket = filter_var($_POST['ticket'],FILTER_SANITIZE_STRING);
	$revision = filter_var($_POST['revision'],FILTER_SANITIZE_STRING);
	$all_rev = $dbc->query("SELECT `revisions` FROM `ticket_pdf` WHERE `id`='$form'")->fetch_assoc();
	$dbc->query("UPDATE `ticket_pdf_field_values` SET `deleted`=1 WHERE `ticketid`='$ticket' AND `pdf_type`='$form'".($all_rev['revisions'] > 0 ? " AND `revision`='$revision'" : ""));
} else if($_GET['action'] == 'checklist_tile') {
	$checklistid = filter_var($_GET['checklistid'],FILTER_SANITIZE_STRING);
	$checked = filter_var($_GET['checked'],FILTER_SANITIZE_STRING);
    if($checked = 'true') {
        $checked = 1;
    } else {
        $checked = 0;
    }
    echo "my my";
	$all_rev = $dbc->query("UPDATE checklist SET checklist_tile = $checked WHERE checklistid = '$checklistid'");
}
?>