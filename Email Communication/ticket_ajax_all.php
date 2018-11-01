<?php
include ('../database_connection.php');
include ('../function.php');
date_default_timezone_set('America/Denver');

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

if($_GET['fill'] == 'project_path_milestone') {
    $project_path = $_GET['project_path'];
	echo '<option value=""></option>';
    $each_tab = explode('#*#', get_project_path_milestone($dbc, $project_path, 'milestone'));
    $timeline = explode('#*#', get_project_path_milestone($dbc, $project_path, 'timeline'));
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

if($_GET['fill'] == 'projectname') {
	$clientid = explode(',',$_GET['clientid']);

	$project_list = [];
	foreach($clientid as $client) {
		$query = mysqli_query($dbc,"SELECT projectid, project_name FROM project WHERE CONCAT(',',clientid,',') LIKE '%,$client,%' and deleted=0 UNION SELECT projectid, project_name FROM client_project WHERE CONCAT(',',clientid,',') LIKE '%,$client,%' and deleted=0");

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

    $query = mysqli_query($dbc,"SELECT projectid, project_name FROM project WHERE businessid = '$businessid' and deleted=0 UNION SELECT `projectid`, `project_name` FROM `client_project` WHERE `clientid`='$businessid'");
    echo '<option value="">Please Select</option>';
    while($row = mysqli_fetch_array($query)) {
        echo "<option value='".$row['projectid']."'>".$row['project_name']."</option>\n";
    }
}

if($_GET['fill'] == 'deletetask') {
	$tasklistid = $_GET['tasklistid'];
	$query = mysqli_query($dbc,"DELETE FROM tasklist WHERE tasklistid='$tasklistid'");
}

if($_GET['fill'] == 'milestone') {
	$projectid = $_GET['projectid'];
    $project_path = get_project($dbc, $projectid, 'project_path');

	if(substr($projectid,0,1) == 'C') {
		//$project_path = $_GET['project_path'];
		echo '<option value=""></option>';
		$each_tab = explode('#*#', get_client_project_path_milestone($dbc, $project_path, 'milestone'));
		$timeline = explode('#*#', get_client_project_path_milestone($dbc, $project_path, 'timeline'));
		$j=0;
		foreach ($each_tab as $cat_tab) {
			echo "<option value='". $cat_tab."'>".$cat_tab.' : '.$timeline[$j].'</option>';
			$j++;
		}
	}
	else {
		//$project_path = $_GET['project_path'];
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
	$service_type = $_GET['service_type'];
	$service_type = str_replace("__","&",$service_type);

	$query = mysqli_query($dbc,"SELECT distinct(category) FROM services WHERE REPLACE(`service_type`, ' ', '') = '$service_type'");
	echo '<option value="">Please Select</option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='".$row['category']."'>".$row['category'].'</option>';
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
		echo "<option value='".$row['heading']."'>".$row['heading'].'</option>';
	}
}

if($_GET['fill'] == 'ticketdesc') {
	$service_type = $_GET['service_type'];
	$service_type = str_replace("__","&",$service_type);
	//$service_type = str_replace("++"," ",$service_type);

	$service_category = $_GET['service_category'];
	$service_category = str_replace("__","&",$service_category);
	//$service_category = str_replace("++"," ",$service_category);

	$heading = $_GET['heading'];
	$heading = str_replace("__","&",$heading);
	//$heading = str_replace("++"," ",$heading);

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ticket_description FROM services WHERE `service_type`='$service_type' AND `category` = '$service_category' AND `heading` = '$heading'"));
    echo html_entity_decode($get_config['ticket_description']);
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
	$created_date = date('Y-m-d');
    $created_by = $_GET['login_contactid'];

    $query_insert_client_doc = "INSERT INTO `ticket_timer` (`ticketid`, `timer_type`, `start_time`, `created_date`, `created_by`, `start_timer_time`) VALUES ('$ticketid', 'Work', '$start_timer_time', '$created_date', '$created_by', '$start_time')";
    $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
}

if($_GET['fill'] == 'pausetickettimer') {
	$ticketid = $_GET['ticketid'];
    $timer = $_GET['timer_value'];
    $start_time = time();
    $created_date = date('Y-m-d');
    $created_by = $_GET['login_contactid'];
    $end_time = date('g:i A');
    if($timer != '0' && $timer != '00:00:00' && $timer != '') {
        $query_update_ticket = "UPDATE `ticket_timer` SET `end_time` = '$end_time', `timer` = '$timer' WHERE `ticketid` = '$ticketid' AND created_by='$created_by' AND created_date='$created_date' AND timer_type='Work' AND end_time IS NULL";
        $result_update_ticket = mysqli_query($dbc, $query_update_ticket);

        $query_insert_client_doc = "INSERT INTO `ticket_timer` (`ticketid`, `timer_type`, `start_time`, `created_date`, `created_by`, `start_timer_time`) VALUES ('$ticketid', 'Break', '$end_time', '$created_date', '$created_by', '$start_time')";
        $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
		insert_day_overview($dbc, $created_by, 'Ticket', date('Y-m-d'), '', 'Added time to Ticket #'.$ticketid.' - '.$timer);
    }
}

if($_GET['fill'] == 'resumetickettimer') {
	$ticketid = $_GET['ticketid'];
    $timer = $_GET['timer_value'];
    $start_time = time();
    $created_date = date('Y-m-d');
    $created_by = $_GET['login_contactid'];
    $end_time = date('g:i A');
    if($timer != '0' && $timer != '00:00:00' && $timer != '') {
        $query_update_ticket = "UPDATE `ticket_timer` SET `end_time` = '$end_time', `timer` = '$timer' WHERE `ticketid` = '$ticketid' AND created_by='$created_by' AND created_date='$created_date' AND timer_type='Break' AND end_time IS NULL";
        $result_update_ticket = mysqli_query($dbc, $query_update_ticket);

        $query_insert_client_doc = "INSERT INTO `ticket_timer` (`ticketid`, `timer_type`, `start_time`, `created_date`, `created_by`, `start_timer_time`) VALUES ('$ticketid', 'Work', '$end_time', '$created_date', '$created_by', '$start_time')";
        $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
    }
}

if($_GET['fill'] == 'update_ticket_status') {
    $ticketid = $_GET['ticketid'];
    $status = $_GET['status'];
        $query_update_employee = "UPDATE `tickets` SET status = '$status' WHERE ticketid='$ticketid'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
		echo $query_update_employee;
}

if($_GET['fill'] == 'send_email') {
    $commid = $_GET['commid'];
    $comm_query = "select `from_email`, `from_name`, `subject`, `email_body`, `to_contact`, `cc_contact`, `to_staff`, `cc_staff` from email_communication where email_communicationid=$commid";
    $comm_result = mysqli_fetch_assoc(mysqli_query($dbc, $comm_query));
    $from_email = $comm_result['from_email'];
    $from_name = $comm_result['from_name'];
    $meeting_arr_email = explode(",", $comm_result['to_contact']);
    $meeting_cc_arr_email = explode(",", $comm_result['cc_contact']);
    $subject = $comm_result['subject'];
    $from_name = $comm_result['from_name'];
    $send_body = $comm_result['email_body'];
    send_email([$from_email => $from_name], $meeting_arr_email, $meeting_cc_arr_email , '', $subject, $send_body);
}
?>
