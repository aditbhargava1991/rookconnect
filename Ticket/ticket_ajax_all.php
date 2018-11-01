<?php
include_once('../include.php');
include_once('../tcpdf/tcpdf.php');
if(!file_exists('download')) {
    mkdir('download', 0777, true);
}
ob_clean();
date_default_timezone_set('America/Denver');
if(!($_SESSION['contactid'] > 0) && !isset($_SESSION['intake_ticket'])) {
    echo "ERROR#*#Your session has timed out. Please log in and try again.";
    exit();
}
if($_GET['fill'] == 'add_edit_project') {
    $ticketid = $_GET['ticketid'];
    $project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(projectid) `num`, projectid FROM `project` WHERE `project_name` = 'Piece Work Project' AND `deleted` = 0"));

    if($project['num'] > 0) {
        $projectid = $project['projectid'];
    } else {
        $sql = "INSERT INTO `project` (`project_name`,`status`) VALUES ('Piece Work Project','Active Project')";
        $result_insert_ticket = mysqli_query($dbc, $sql);
        $projectid = mysqli_insert_id($dbc);
    }
    mysqli_query($dbc, "UPDATE `tickets` SET projectid = '$projectid' WHERE ticketid = $ticketid");
    mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$ticketid of tickets updated: projectid updated to $projectid')");
    echo $projectid;
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
if($_GET['fill'] == 'start_timer') {
    $ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
    $contactid = filter_var($_GET['contactid'],FILTER_SANITIZE_STRING);
    $timer_date = date('Y-m-d');
    $start_time = date('h:i A');
    $query_add_time = "INSERT INTO `ticket_time` (`ticketid`, `start_time`, `contactid`, `src`, `timer_date`) VALUES ('$ticketid', '$start_time', '$contactid', 'A', '$timer_date')";
    $result_add_time = mysqli_query($dbc, $query_add_time);
    $ticketid = $_GET['ticketid'];
    $start_time = time();
    $start_timer_time = date('g:i A');
    $created_date = date('Y-m-d H:i:s');
    $created_by = $contactid;

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
} else if($_GET['fill'] == 'stop_timer') {
    $ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
    $timer_value = filter_var($_GET['timer_value'],FILTER_SANITIZE_STRING);
    $contactid = filter_var($_GET['contactid'],FILTER_SANITIZE_STRING);
    $timer_date = date('Y-m-d');
    $end_time = date('h:i A');
    if($timer_value != '0' && $timer_value != '00:00:00' && $timer_value != '') {
        //$query_add_time = "INSERT INTO `tasklist_time` (`tasklistid`, `work_time`, `src`, `contactid`, `timer_date`) VALUES ('$taskid', '$timer_value', 'A', '$contactid', '$timer_date')";
        $query_add_time = "UPDATE `ticket_time` SET `end_time` = '$end_time', `work_time`='$timer_value' WHERE `ticketid`='$ticketid' AND `contactid` = '$contactid' AND `timer_date` = '$timer_date' AND `src` = 'A' AND `start_time` IS NOT NULL AND `end_time` IS NULL";
        $result_add_time = mysqli_query($dbc, $query_add_time);

        insert_day_overview($dbc, $contactid, 'Ticket', date('Y-m-d'), '', "Updated Ticket #$ticketid - Added Time : $timer_value");

        $query_update_time = "UPDATE `ticket` SET `work_time`=ADDTIME(`work_time`,'$timer_value') WHERE `ticketid`='$ticketid'";
        $result_update_time = mysqli_query($dbc, $query_update_time);


        $query_add_time = "INSERT INTO `time_cards` (`staff`, `date`, `type_of_time`, `total_hrs`, `comment_box`) VALUES ('$contactid', '$timer_date', 'Regular Hrs.', '".((strtotime($timer_value) - strtotime('00:00:00'))/3600)."', 'Time Added on Task #$taskid')";
        $result_add_time = mysqli_query($dbc, $query_add_time);
    }


}
if($_GET['fill'] == 'ticket_quick_time') {
    $ticketid = $_POST['id'];
    $tt=$_POST['time'];
    $time = strtotime($_POST['time']);
    $current_time = strtotime(mysqli_fetch_array(mysqli_query($dbc, "SELECT `work_time` FROM `tickets` WHERE `ticketid`='$ticketid'"))['work_time']);

    $total_time = date('H:i:s', $time + $current_time - strtotime('00:00:00'));
    $query_time = "UPDATE `tickets` SET `max_time` = '$total_time' WHERE ticketid='$ticketid'";
    mysqli_query($dbc, "INSERT INTO `ticket_time` (`ticketid`, `work_time`, `src`, `contactid`, `timer_date`) VALUES ('$ticketid', '".$_POST['time']."', 'M', '".$_SESSION['contactid']."', '".date('Y-m-d')."')");
    mysqli_query($dbc, "INSERT INTO `time_cards` (`staff`,`date`,`type_of_time`,`total_hrs`,`timer_tracked`,`comment_box`) VALUES ('".$_SESSION['contactid']."','".date('Y-m-d')."','Regular Hrs.','".(($time - strtotime('00:00:00')) / 3600)."','0','Time Added on Ticket #$ticketid')");
    $result = mysqli_query($dbc, $query_time);
    insert_day_overview($dbc, $_SESSION['contactid'], 'Ticket', date('Y-m-d'), '', "Updated Ticket #$ticketid - Added Time : ".$_POST['time']);
    echo $query="INSERT INTO ticket_time_list(ticketid,time_type,time_length,created_by)
            VALUES('$ticketid','Regular Hrs.','$tt','$_SESSION[contactid]')";
    mysqli_query($dbc, $query);
    echo 'Added '.$_POST['time']." - $total_time total";
}
if($_GET['fill'] == 'ticketreply') {
    $id = $_POST['taskid'];
    //$reply = filter_var(htmlentities('<p>'.$_POST['reply'].'</p>'),FILTER_SANITIZE_STRING);
    $reply = filter_var(htmlentities($_POST['reply']),FILTER_SANITIZE_STRING);
    //$query = "UPDATE `tasklist` SET `task`=CONCAT(`task`,'$reply') WHERE `tasklistid`='$id'";
    //$result = mysqli_query($dbc,$query);
    if ( isset($_POST['user']) ) {
        $user = preg_replace('/[^0-9]/', '', $_POST['user']);
        $reply .= get_contact($dbc, $user);
    }
    $contactid = $_SESSION['contactid'];
    $created_date = date('Y-m-d');
    $insert = mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `created_by`, `created_date`, `comment`) VALUES ('$id', '$contactid', '$created_date', '$reply')");
    echo mysqli_error($dbc);
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
    mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$ticketid of tickets updated: status updated to $status')");
    if(in_array(explode('#*#',get_config($dbc, "ticket_archive_status")), $status)) {
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
    mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$ticketid of tickets updated: milestone_timeline updated to $milestone_timeline')");
    $result_update_employee = mysqli_query($dbc, $query_update_employee);
}


if($_GET['fill'] == 'book_ticket') {
    $ticketid = $_GET['ticketid'];
    $contactid = $_GET['contactid'];
    $is_booked = $_GET['is_booked'];
    $contact_category = get_contact($dbc, $contactid, 'category');

    if($is_booked == 1) {
        $date_of_archival = date('Y-m-d');
        $rows = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid` = '$ticketid' AND `item_id` = '$contactid'");
        while($row = mysqli_fetch_assoc($rows)) {
            mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #{$row['id']} of ticket_attached updated: deleted updated to 1')");
            mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #{$row['id']} of ticket_attached updated: date_of_archival updated to $date_of_archival')");
        }
        mysqli_query($dbc, "UPDATE `ticket_attached` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `ticketid` = '$ticketid' AND `item_id` = '$contactid'");
    } else {
        $contact_exists = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) as num_rows FROM `ticket_attached` WHERE `ticketid` = '$ticketid' AND `item_id` = '$item_id'"))['num_rows'];
        if($contact_exists > 0) {
            $rows = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid` = '$ticketid' AND `item_id` = '$item_id'");
            while($row = mysqli_fetch_assoc($rows)) {
                mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #{$row['id']} of ticket_attached updated: deleted updated to 1')");
            }
            mysqli_query($dbc, "UPDATE `ticket_attached` SET `deleted` = 0 WHERE `ticketid` = '$ticketid' AND `item_id` = '$item_id'");
        } else {
            mysqli_query($dbc, "INSERT INTO `ticket_attached` (`ticketid`, `src_table`, `item_id`) VALUES ('$ticketid', '$contact_category', '$contactid')");
            $insertid = mysqli_insert_id($dbc);
            mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$insertid of ticket_attached inserted')");
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
                $insertid = mysqli_insert_id($dbc);
                mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$insertid of ticket_attached inserted')");
            }
        } else if($count < $current) {
            for(; $current > $count; $current--) {
                $date_of_archival = date('Y-m-d');
                $row = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT MAX(`id`) `id` FROM `ticket_attached` WHERE `deleted`=0 AND `src_table`='inventory_general' AND `ticketid`='$ticketid'"));
                if(empty($row)) {
                    mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #{$row['id']} of ticket_attached updated: deleted updated to 1')");
                    mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #{$row['id']} of ticket_attached updated: date_of_archival updated to $date_of_archival')");
                }
                $dbc->query("UPDATE `ticket_attached` LEFT JOIN (SELECT MAX(`id`) `id` FROM `ticket_attached` WHERE `deleted`=0 AND `src_table`='inventory_general' AND `ticketid`='$ticketid') `top_id` ON `ticket_attached`.`id`=`top_id`.`id` SET `ticket_attached`.`deleted`=1, `ticket_attached`.`date_of_archival` = '$date_of_archival' WHERE `top_id`.`id` IS NOT NULL");
            }
        }
    }
}
if($_GET['action'] == 'no_bill') {
    $billable = get_field_value('service_no_bill',$_POST['table'],$_POST['id_field'],$_POST['id']);
    $billable = explode(',',$billable);
    if($_POST['value'] > 0) {
        $billable[] = $_POST['serviceid'];
    } else {
        $row = array_search($_POST['serviceid'],$billable);
        if($row !== false) {
            unset($billable[$row]);
        }
    }
    set_field_value(implode(',',array_filter($billable)),'service_no_bill', $_POST['table'], $_POST['id_field'], $_POST['id']);
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
    $end_time_from = TICKET_TILE.' Tile';

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
    $status_change = false;
    if(!empty($ticket_type)) {
        $value_config .= get_config($dbc, 'ticket_fields_'.$ticket_type).',';
        $ticket_status = get_field_value('status','tickets','ticketid',$ticketid);
        $status_list = explode(',',get_config($dbc, 'ticket_status'));
        if(!in_array($ticket_status, $status_list)) {
            $new_status = get_config($dbc, 'ticket_default_status_'.$ticket_type);
            if(empty($new_status)) {
                $new_status = get_config($dbc, 'ticket_defaul_status');
            }
            if(!empty($new_status)) {
                $status_change = true;
                set_field_value($new_status, 'status', 'tickets', 'ticketid', $ticketid);
            }
        }
    }
    if(strpos($value_config,',Time Tracking Edit Past Date') !== FALSE && $get_ticket['to_do_date'] != '') {
        $_POST['date'] = $get_ticket['to_do_date'];
    }

    if($field_name == 'status' || $status_change) {
        $current_history_value = mysqli_fetch_assoc(mysqli_query($dbc, "select history from tickets where ticketid = $id"));
        $current_history = $current_history_value['history'];
        $changer = get_contact($dbc, $_SESSION['contactid']);
        $history = $current_history . "<b>$changer</b> has Changed the status to $value on " . date("Y-m-d H:m:s") . "<br>";
        mysqli_query($dbc, "UPDATE tickets set history = '$history' where ticketid = $id");
        if(in_array($value, explode('#*#',get_config($dbc, "ticket_archive_status")))) {
            if($table_name != 'tickets') {
                $dbc->query("UPDATE `$table_name` SET `deleted`=1 WHERE `$id_field`='$id'");
            } else {
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
        }
        if(!empty($_POST['auto_create_unscheduled']) && !empty($value) && strpos($_POST['auto_create_unscheduled'], ','.$value.',') !== FALSE) {
            $latest_ticket_schedule = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `ticketid` = '".$ticketid."' WHERE `deleted` = 0 ORDER BY `sort` DESC"));
            mysqli_query($dbc, "INSERT INTO `ticket_schedule` (`ticketid`, `type`, `location_name`, `client_name`, `address`, `city`, `province`, `postal_code`, `country`, `map_link`, `details`, `email`, `carrier`, `vendor`, `lading_number`, `volume`, `order_number`, `sort`, `warehouse_location`, `container`, `manifest_num`) SELECT `ticketid`, `type`, `location_name`, `client_name`, `address`, `city`, `province`, `postal_code`, `country`, `map_link`, `details`, `email`, `carrier`, `vendor`, `lading_number`, `volume`, `order_number`, (`sort` + 1), `warehouse_location`, `container`, `manifest_num` FROM `ticket_schedule` WHERE `ticketid` = '".$ticketid."' AND `deleted` = 0 ORDER BY `sort` DESC LIMIT 1");
            echo 'created_unscheduled_stop';
        }
    }
    if($table_name == 'mileage' && ($field_name == 'start' || $field_name == 'end')) {
        $value = date('Y-m-d '.TIME_FORMAT_SEC, strtotime($value));
    } else if(in_array($table_name,['tickets','ticket_schedule']) && in_array($field_name,['est_time','max_time'])) {
        $start = get_field_value('to_do_start_time',$table_name,$id_field,$id);
        if(!empty($start)) {
            $end = date('H:i:s', strtotime($start) + ($value * 3600));
            set_field_value($end,'to_do_end_time',$table_name,$id_field,$id);
        }
    } else if(in_array($table_name,['tickets','ticket_schedule']) && in_array($field_name,['to_do_start_time'])) {
        $start = get_field_value('to_do_start_time',$table_name,$id_field,$id);
        $end = get_field_value('to_do_end_time',$table_name,$id_field,$id);
        if(!empty($end) && !empty($start)) {
            $end = date('H:i:s', strtotime($value) + strtotime($end) - strtotime($start));
            set_field_value($end,'to_do_end_time',$table_name,$id_field,$id);
        }
    } else if(in_array($table_name,['ticket_schedule']) && !(get_field_value('coord_auto',$table_name,$id_field,$id) > 0) && in_array($field_name,['address','city','province','postal_code','country'])) {
        set_field_value('','coordinates',$table_name,$id_field,$id);
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
            mysqli_query($dbc, "UPDATE `$table_name` SET `created_by`='{$_SESSION['contactid']}' WHERE `$id_field`='$id'");
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
    if($table_name == 'ticket_attached' && ($field_name == 'item_id' || $field_name == 'description') && $type == 'material') {
        if($_POST['auto_checkin'] == 1) {
            mysqli_query($dbc, "UPDATE `ticket_attached` SET `arrived` = 1, `checked_in`=DATE_FORMAT(NOW(),'%H:%i:%s') WHERE `id`='$id'");
        }
        if($_POST['auto_checkout'] == 1) {
            mysqli_query($dbc, "UPDATE `ticket_attached` SET `completed` = 1, `checked_out`=DATE_FORMAT(NOW(),'%H:%i:%s') WHERE `id`='$id'");
        }
    }
    if($table_name == 'ticket_attached' && ($field_name == 'arrived' || $field_name == 'completed')) {
        $seconds = time();
        if($field_name == 'arrived' && $value == 1) {
            mysqli_query($dbc, "UPDATE `ticket_attached` SET `timer_start`='$seconds' WHERE `id`='$id'");
            mysqli_query($dbc, "UPDATE `ticket_attached` SET `checked_in`='".date('h:i a')."' WHERE `id`='$id' AND IFNULL(`checked_in`,'') = ''");
            if($_POST['no_toggle_off'] == 1) {
                mysqli_query($dbc, "UPDATE `ticket_attached` SET `completed`=0 WHERE `id`='$id'");
            }
        } else {
            $hours = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `timer_start`, `hours_tracked`, `date_stamp` FROM `ticket_attached` WHERE `id`='$id'"));
            if($hours['timer_start'] > 0) {
                $checked_in_date = $hours['date_stamp'];
                $checked_out_date = date('Y-m-d');
                $checked_in_time = date('h:i a', $hours['timer_start']);
                $checked_out_time = date('h:i a');
                mysqli_query($dbc, "INSERT INTO `ticket_attached_checkin` (`ticket_attached_id`, `checked_in_date`, `checked_in`, `checked_out_date`, `checked_out`) VALUES ('$id', '$checked_in_date', '$checked_in_time', '$checked_out_date', '$checked_out_time')");
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
            mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs`=GREATEST(IF('$time_interval' > 0,CEILING(((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600) / '$time_interval') * '$time_interval',((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600)),'$time_minimum'), `timer_tracked` = (($seconds - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `type_of_time`=IF(`day_tracking_type` IS NULL OR `day_tracking_type` = '', 'Regular Hrs.', `day_tracking_type`), `end_time`='$time', `end_time_from` = '$end_time_from' WHERE `timer_start` > 0 AND `type_of_time`='day_tracking' AND `day_tracking_type` NOT LIKE 'Work:%' AND `staff`='".$attached['item_id']."'");
            // Sign into the Ticket
            if(strpos($value_config, ',Staff Multiple Times,') === FALSE) {
                mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs` = GREATEST(IF('$time_interval' > 0,CEILING(((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600) / '$time_interval') * '$time_interval',((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600)),'$time_minimum'), `timer_tracked` = (($seconds - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `end_time`='$time', `end_time_from` = '$end_time_from' WHERE `type_of_time` NOT IN ('day_tracking','day_break') AND `timer_start` > 0 AND `staff`='{$attached['item_id']}'");
                mysqli_query($dbc, "INSERT INTO `time_cards` (`business`, `projectid`, `ticketid`, `staff`, `date`, `start_time`, `timer_start`, `type_of_time`, `comment_box`, `ticket_attached_id`) SELECT `businessid`, `projectid`, `ticketid`, '{$attached['item_id']}', '$today', '$time', '$seconds', '{$attached['position']}', 'Checked in on ".TICKET_NOUN." #{$attached['ticketid']} for {$attached['position']}', '{$attached['id']}' FROM `tickets` WHERE `ticketid`='{$attached['ticketid']}'");
                mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs` = GREATEST(IF('$time_interval' > 0,CEILING(((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600) / '$time_interval') * '$time_interval',((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600)),'$time_minimum'), `timer_tracked` = (($seconds - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `end_time`='$time', `end_time_from` = '$end_time_from', `comment_box`=CONCAT(IFNULL(`comment_box`,''),'Signed in on ".get_ticket_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='{$attached['ticketid']}'")))."') WHERE `type_of_time` NOT IN ('day_tracking','day_break') AND `ticketid`!='{$attached['ticketid']}' AND `staff`='{$attached['item_id']}' AND `timer_start` > 0");
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
                mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs` = GREATEST(IF('$time_interval' > 0,CEILING(((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600) / '$time_interval') * '$time_interval',((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600)),'$time_minimum'), `timer_tracked` = (($seconds - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `end_time`='$time', `end_time_from` = '$end_time_from' WHERE `type_of_time` NOT IN ('day_tracking','day_break') AND `timer_start` > 0 AND `staff`='{$attached['item_id']}'");
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
                    mysqli_query($dbc, "UPDATE `time_cards` SET `staff` = '".$attached['item_id']."', `date` = '".$staff_time['date_stamp']."', `start_time` = '".$staff_time['start_time']."', `end_time` = '".$staff_time['end_time']."', `end_time_from` = '$end_time_from', `type_of_time` = '".($staff_time['position'] == 'Sleep Hrs.' ? 'Sleep Hrs.' : $attached['position'])."', `total_hrs` = '".$staff_time['hours_set']."' WHERE `ticket_attached_id` = '".$staff_time['id']."'");
                }
            } else {
                // If payable hours are set and tracked in time cards, use that as total hours but keep time_tracked to the value set by checkin/checkout
                if(strpos($value_config,',Staff Set Hours Time Sheet,') !== FALSE && $attached['hours_set'] > 0) {
                    $latest_time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `ticket_attached_id` = '".$attached['id']."' AND `deleted` = 0 AND `timer_start` > 0 ORDER BY `time_cards_id` DESC"));
                    if(!empty($latest_time_card)) {
                        mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs` = '".$attached['hours_set']."', `timer_tracked` = (($seconds - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `end_time`='$time', `end_time_from` = '$end_time_from' WHERE `time_cards_id` = '".$latest_time_card['time_cards_id']."'");
                        mysqli_query($dbc, "UPDATE `time_cards` SET `deleted` = 1 WHERE `ticket_attached_id` = '".$attached['id']."' AND `time_cards_id` != '".$latest_time_card['time_cards_id']."'");
                    }

                } else {
                    // $existing_time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `ticket_attached_id` = '{$attached['id']}' AND `timer_tracked` = 0 AND `total_hrs` > 0 AND `deleted` = 0"));
                    // $override_hours = '';
                    // if(strpos($value_config,',Staff Set Hours Time Sheet,') !== FALSE && !empty($existing_time_card)) {
                    // 	$override_hours = $existing_time_card['total_hrs'];
                    // 	mysqli_query($dbc, "UPDATE `time_cards` SET `deleted` = '1' WHERE `time_cards_id` = '{$existing_time_card['time_cards_id']}'");
                    // }
                    mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs` = GREATEST(IF('$time_interval' > 0,CEILING(((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600) / '$time_interval') * '$time_interval',((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600)),'$time_minimum'), `timer_tracked` = (($seconds - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `end_time`='$time', `end_time_from` = '$end_time_from' WHERE `type_of_time` NOT IN ('day_tracking','day_break') AND `timer_start` > 0 AND `staff`='{$attached['item_id']}'");
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
        mysqli_query($dbc, "UPDATE `time_cards` SET `end_time`='$value', `end_time_from` = '$end_time_from', `total_hrs`=(UNIX_TIMESTAMP(STR_TO_DATE(`end_time`,'%h:%i %p')) - UNIX_TIMESTAMP(STR_TO_DATE(`start_time`,'%h:%i %p'))) / 3600 WHERE `ticketid`='{$attached['ticketid']}' AND `staff`='{$attached['item_id']}' AND `comment_box` LIKE '% for {$attached['position']}'");
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

    // Insert/Update Time Sheets if tracking Service Total/Direct/Indirect Time
    $ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
    $staff_list = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid` = '".$ticketid."' AND `deleted` = 0 AND `src_table` LIKE 'Staff%' AND `item_id` > 0");
    if(strpos($value_config, ',Service Track Total Time,') !== FALSE) {
        $total_time = 0;
        foreach(array_filter(explode(',', $ticket['service_total_time'])) as $service_total_time) {
            if($service_total_time > 0) {
                $total_time += $service_total_time;
            }
        }
        if($total_time > 0) {
            foreach($staff_list as $staff) {
                mysqli_query($dbc, "INSERT INTO `time_cards` (`ticketid`, `ticket_attached_id`, `staff`, `date`, `type_of_time`) SELECT '".$ticketid."', '".$staff['id']."', '".$staff['item_id']."', '".$ticket['to_do_date']."', 'Regular Hrs.' FROM (SELECT COUNT(*) rows FROM `time_cards` WHERE `deleted` = 0 AND `ticket_attached_id` = '".$staff['id']."' AND `type_of_time` = 'Regular Hrs.') num WHERE num.rows=0");
                mysqli_query($dbc, "UPDATE `time_cards` SET `staff` = '".$staff['item_id']."', `total_hrs` = '".$total_time."', `date` = '".$ticket['to_do_date']."' WHERE `ticket_attached_id` = '".$staff['id']."' AND `type_of_time` = 'Regular Hrs.'");
            }
        }
    }
    if(strpos($value_config, ',Service Track Direct Time,') !== FALSE) {
        $direct_time = 0;
        foreach(array_filter(explode(',', $ticket['service_direct_time'])) as $service_direct_time) {
            if($service_direct_time > 0) {
                $direct_time += $service_direct_time;
            }
        }
        if($direct_time > 0) {
            foreach($staff_list as $staff) {
                mysqli_query($dbc, "INSERT INTO `time_cards` (`ticketid`, `ticket_attached_id`, `staff`, `date`, `type_of_time`) SELECT '".$ticketid."', '".$staff['id']."', '".$staff['item_id']."', '".$ticket['to_do_date']."', 'Direct Hrs.' FROM (SELECT COUNT(*) rows FROM `time_cards` WHERE `deleted` = 0 AND `ticket_attached_id` = '".$staff['id']."' AND `type_of_time` = 'Direct Hrs.') num WHERE num.rows=0");
                mysqli_query($dbc, "UPDATE `time_cards` SET `staff` = '".$staff['item_id']."', `total_hrs` = '".$direct_time."', `date` = '".$ticket['to_do_date']."' WHERE `ticket_attached_id` = '".$staff['id']."' AND `type_of_time` = 'Direct Hrs.'");
            }
        }
    }
    if(strpos($value_config, ',Service Track Indirect Time,') !== FALSE) {
        $indirect_time = 0;
        foreach(array_filter(explode(',', $ticket['service_indirect_time'])) as $service_indirect_time) {
            if($service_indirect_time > 0) {
                $indirect_time += $service_indirect_time;
            }
        }
        if($indirect_time > 0) {
            foreach($staff_list as $staff) {
                mysqli_query($dbc, "INSERT INTO `time_cards` (`ticketid`, `ticket_attached_id`, `staff`, `date`, `type_of_time`) SELECT '".$ticketid."', '".$staff['id']."', '".$staff['item_id']."', '".$ticket['to_do_date']."', 'Indirect Hrs.' FROM (SELECT COUNT(*) rows FROM `time_cards` WHERE `deleted` = 0 AND `ticket_attached_id` = '".$staff['id']."' AND `type_of_time` = 'Indirect Hrs.') num WHERE num.rows=0");
                mysqli_query($dbc, "UPDATE `time_cards` SET `staff` = '".$staff['item_id']."', `total_hrs` = '".$indirect_time."', `date` = '".$ticket['to_do_date']."' WHERE `ticket_attached_id` = '".$staff['id']."' AND `type_of_time` = 'Indirect Hrs.'");
            }
        }
    }

    //Insert into day overview if last edit was not within 15 minutes
    $day_overview_last = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `timestamp` FROM `day_overview` WHERE `type` = 'Ticket' AND `tableid` = '$ticketid' AND `contactid` = '".$_SESSION['contactid']."' ORDER BY `timestamp` DESC"));
    $timestamp_now = date('Y-m-d H:i:s');
    $timediff = strtotime($timestamp_now) - strtotime($day_overview_last['timestamp']);
    if($timediff > 900 && !empty($ticketid)) {
        $ticket_heading = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading` FROM `tickets` WHERE `ticketid` = '$ticketid'"))['heading'];
        insert_day_overview($dbc, $_SESSION['contactid'], 'Ticket', date('Y-m-d'), '', 'Updated '.TICKET_NOUN.' #'.$ticketid.(!empty($ticket_heading) ? ': '.$ticket_heading : ''), $ticketid);
    }

    //Alert users if enabled
    if($ticketid > 0) {
        $get_ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
        $ticket_type = $get_ticket['ticket_type'];
        if($ticket_type == '') {
            $ticket_type = get_config($dbc, 'default_ticket_type');
        }
        $ticket_status = $get_ticket['status'];
        if(empty($ticket_status)) {
            $ticket_status = get_config($dbc, 'ticket_default_status');
        }
        $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_alerts` WHERE `ticket_type` = '".$ticket_type."'"));
        $ticket_alert = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_alerts` WHERE `ticketid` = '".$ticketid."'"));
        if($field_config['enabled'] == 1 && $ticket_alert['sent'] != 1 && ($ticket_status == $field_config['status'] || empty($field_config['status']))) {
            $ticket_tabs = explode(',',get_config($dbc, 'ticket_tabs'));
            $type_label = TICKET_NOUN;
            foreach($ticket_tabs as $type) {
                if(config_safe_str($type) == $ticket_type) {
                    $type_label = $type;
                }
            }
            $subject = str_replace(['[TICKET]'],[get_ticket_label($dbc, $get_ticket)],$field_config['subject']);
            $body = str_replace(['[TICKET]'],[get_ticket_label($dbc, $get_ticket)],html_entity_decode($field_config['body']));
            $body .= "To review this ".TICKET_NOUN.", <a href='".WEBSITE_URL."/Ticket/index.php?edit=".$get_ticket['ticketid']."&tile_name=".$get_ticket['ticket_type']."&from_alert=1'>click here</a>.";
            foreach(explode(',', $field_config['contactid']) as $staffid) {
                if($staffid > 0) {
                    $email = get_email($dbc, $staffid);
                    send_email('', $email, '', '', $subject, $body);
                    mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_type`, `subject`, `src_table`, `src_tableid`) VALUES ('$staffid', '".date('Y-m-d')."', 'TICKET_ALERTS', '$subject', 'ticket_alerts', '$ticketid')");
                }
            }
            mysqli_query($dbc, "INSERT INTO `ticket_alerts` (`ticketid`, `sent`) VALUES ('$ticketid', 1)");
        }
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
    mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$ticketid of tickets updated: attached_image updated to $filename')");
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
            mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$id of $table_name updated: $field_name updated to $filename')");
        } else {
            mysqli_query($dbc, "INSERT INTO `$table_name` (`ticketid`,`document`,`label`,`created_by`,`created_date`) VALUES ('$ticketid','$filename','$basename','".$_SESSION['contactid']."',DATE(NOW()))");
            $insertid = mysqli_insert_id($dbc);
            mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$insertid of $table_name updated: document updated to $filename')");
        }

        //Create smaller thumbnail with max height or width of 800
        $info = getimagesize('download/'.$filename);
        $mime = $info['mime'];
        if($mime == 'image/jpeg' || $mime == 'image/png' || $mime == 'image/gif') {
            list($image_width, $image_height) = getimagesize('download/'.$filename);
            $new_height = $image_height;
            $new_width = $image_width;
            if($image_height > 800) {
                $new_width = ($new_width / $new_height) * 800;
                $new_height = 800;
            }
            if($new_width > 800) {
                $new_height = ($new_height / $new_width) * 800;
                $new_width = 800;
            }
            $new_file_name = explode('.',$filename);
            array_pop($new_file_name);
            $new_file_name = implode('.',$new_file_name);
            resize_image_convert_jpg($new_width, $new_height, 'download/'.$new_file_name.'_thumbnail', 'download/'.$filename);
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
    }
    $result = [];
    $sql = "SELECT `created_by` `contact`, 'Running Timer' `status` FROM `ticket_timer` WHERE `ticketid`='$ticketid' AND `ticketid` > 0 AND `start_timer_time` > 0 AND `end_time` IS NULL AND `deleted` = 0 UNION
		SELECT `item_id` `contact`, CONCAT('Checked In ',`src_table`) `status` FROM `ticket_attached` WHERE `src_table` IN ('Staff','Staff_Tasks','Members','Clients') AND `item_id` > 0 AND `arrived` != `completed` AND `deleted`=0 AND `ticketid`='$ticketid' AND `ticketid` > 0";
    if(strpos($value_config, ',Complete Do Not Require Notes,') === FALSE) {
        $sql .= " UNION SELECT `item_id` `contact`, 'Notes Not Complete' `status` FROM `ticket_attached` WHERE `src_table` IN ('Staff') AND `item_id` > 0 AND `deleted`=0 AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `discrepancy`=0 AND `item_id` NOT IN (SELECT `created_by` FROM `ticket_comment` WHERE `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0 UNION SELECT `created_by` FROM `client_daily_log_notes` WHERE `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0)";
    }
    $ready = mysqli_query($dbc, $sql);
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
            mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$ticketid of tickets updated: status updated to $auto_status')");
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
    $services = mysqli_query($dbc, "SELECT `serviceid`, `category`, `service_type`, `heading` FROM `services` WHERE `serviceid` IN (".implode(',',$service_list).") AND `deleted`=0");
    while($service = mysqli_fetch_assoc($services)) {
        $row_price = 0;
        foreach($service_list as $i => $id) {
            if($service['serviceid'] == $id) {
                $row_price = $service_price[$i];
            }
        }
        echo "<option data-rate-price='".$row_price."' value='".$service['serviceid']."'>".implode(': ',array_filter([$service['category'],$service['service_type'],$service['heading']]))."</option>";
    }
} else if($_GET['action'] == 'business_services_fetch') {
    $businessid = filter_var($_GET['business'],FILTER_SANITIZE_STRING);
    $template_list = get_contact($dbc, $businessid, 'service_templates');
    $service_list = [];
    foreach(explode(',',$template_list) as $templateid) {
        if($templateid > 0) {
            foreach(explode(',',get_field_value('serviceid','services_service_templates','templateid',$templateid)) as $service) {
                $service_list[] = $service;
            }
        }
    }
    $rate_contact = get_config($dbc, 'rate_card_contact_'.$tab) ?: get_config($dbc, 'rate_card_contact');

    $services = explode('**',mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `services` FROM `rate_card` WHERE `clientid` = '$businessid' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') ORDER BY `clientid`='$rate_contact' DESC"))['services']);
    foreach($service_list as &$serviceid) {
        foreach($services as $service) {
            $service = explode('#',$service);
            if($service[0] ==  $serviceid) {
                $serviceid .= 'FFM'.$row_price;
            }
        }
    }
    echo implode('#*#',$service_list);

    /* $services = mysqli_query($dbc, "SELECT `serviceid`, `heading` FROM `services` WHERE `serviceid`= '$serviceid'");
    while($service = mysqli_fetch_assoc($services)) {
        //echo "<option data-rate-price='".$row_price."' value='".$serviceid."'>". get_services($dbc, $serviceid, 'heading')."</option>";
        echo $serviceid.'FFM'.$row_price;
    } */
} else if($_GET['action'] == 'addition') {
    $ticketid = filter_var($_GET['src_id'],FILTER_SANITIZE_STRING);
    mysqli_query($dbc, "INSERT INTO `tickets` (`ticket_type`, `category`, `businessid`, `clientid`, `siteid`, `location`, `location_address`, `location_google`, `address`, `google_maps`, `site_location`, `lsd`, `location_notes`, `projectid`, `afe_number`, `heading`) SELECT `ticket_type`, `category`, `businessid`, `clientid`, `siteid`, `location`, `location_address`, `location_google`, `address`, `google_maps`, `site_location`, `lsd`, `location_notes`, `projectid`, `afe_number`, CONCAT('Addition to ".TICKET_NOUN." #',`ticketid`,' - ',`heading`) FROM `tickets` WHERE `ticketid`='$ticketid'");
    $insertid = mysqli_insert_id($dbc);
    echo $insertid;
    mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$insertid','{$_SESSION['contactid']}','Row #$insertid of tickets inserted: addition to ".TICKET_NOUN." #$ticketid)");
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
    if($_POST['mandatory'] == 1) {
        $ticket_fields = filter_var(implode(',',$_POST['fields']),FILTER_SANITIZE_STRING);
        $ticket_type = filter_var($_POST['field_name'],FILTER_SANITIZE_STRING);
        if($ticket_type == 'tickets') {
            set_field_mandatory_config($dbc, $ticket_type, $ticket_fields);
        } else {
            set_config($dbc, $ticket_type, $ticket_fields,1);
        }

        // Save the settings for the types of tasks
        $tasks = filter_var($_POST['tasks'],FILTER_SANITIZE_STRING);
        $tasks_name = filter_var($_POST['tasks_name'],FILTER_SANITIZE_STRING);
        set_config($dbc, $tasks_name, $tasks);

        // Save the settings for labels, billing emails, and custom notes
        set_config($dbc, 'ticket_multiple_labels', filter_var($_POST['labels'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_extra_billing_email', filter_var($_POST['billing'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_custom_notes_heading', filter_var($_POST['note_heading'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_custom_notes_type', filter_var(implode('#*#',$_POST['note_types']),FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_individuals', filter_var(implode('#*#',$_POST['individuals']),FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_cancellation_reasons', filter_var(implode('#*#',$_POST['cancel_reasons']),FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_checkout_info', filter_var(implode('#*#',$_POST['checkout_info']),FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_checkout_info_staff', filter_var(implode('#*#',$_POST['checkout_info_staff']),FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'transport_types', filter_var($_POST['transport_types'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'piece_types', filter_var($_POST['piece_types'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'delivery_types', filter_var($_POST['delivery_types'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'delivery_timeframe_default', filter_var($_POST['delivery_timeframe_default'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_warehouse_start_time', filter_var($_POST['ticket_warehouse_start_time'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, $_POST['tab_transport_log_contact'], filter_var($_POST['tab_transport_log_contact_value'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, $_POST['ticket_custom_field'], filter_var($_POST['ticket_custom_field_value'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, $_POST['ticket_custom_field_values'], filter_var($_POST['ticket_custom_field_values_value'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, $_POST['transport_destination_contact'], filter_var($_POST['transport_destination_contact_value'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, $_POST['transport_carrier_category'], filter_var($_POST['transport_carrier_category_value'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, $_POST['ticket_project_contact'], filter_var($_POST['ticket_project_contact_value'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, $_POST['ticket_business_contact'], filter_var($_POST['ticket_business_contact_value'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, $_POST['incomplete_ticket_status'], filter_var($_POST['incomplete_ticket_status_value'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, $_POST['client_accordion_category'], filter_var($_POST['client_accordion_category_value'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, $_POST['ticket_tab_locks'], filter_var(implode(',',$_POST['ticket_tab_locks_value']),FILTER_SANITIZE_STRING), 1);
        set_config($dbc, $_POST['rate_card_contact'], filter_var($_POST['rate_card_contact_value'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, $_POST['ticket_chemical_label'], filter_var($_POST['ticket_chemical_label_value'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, $_POST['delivery_type_contacts'], filter_var($_POST['delivery_type_contacts_value'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, $_POST['ticket_default_status'], filter_var($_POST['ticket_default_status_value'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, $_POST['ticket_new_email_subject'], filter_var($_POST['ticket_new_email_subject_value'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, $_POST['ticket_new_email_body'], filter_var($_POST['ticket_new_email_body_value'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, $_POST['delivery_default_tabs'], filter_var($_POST['delivery_default_tabs_value'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, $_POST['ticket_invoice_type'], filter_var($_POST['ticket_invoice_type_value'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'auto_archive_complete_tickets', filter_var($_POST['auto_archive_complete_tickets'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'delivery_km_service', filter_var($_POST['delivery_km_service'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'incomplete_inventory_reminder_email', filter_var($_POST['incomplete_inventory_reminder_email'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_notify_list', filter_var($_POST['ticket_notify_list'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_notify_pdf_content', filter_var(htmlentities($_POST['ticket_notify_pdf_content']),FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_notify_cc', filter_var($_POST['ticket_notify_cc'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_notify_list_items', filter_var(implode('#*#',$_POST['ticket_notify_list_items']),FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_email_approval', filter_var($_POST['ticket_email_approval'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_approval_status', filter_var($_POST['ticket_approval_status'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'delivery_default_pickup_type', filter_var($_POST['delivery_default_pickup_type'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'delivery_default_pickup_time', filter_var($_POST['delivery_default_pickup_time'],FILTER_SANITIZE_STRING), 1);
        if($ticket_type == 'tickets') {
            set_config($dbc, 'ticket_attached_charts', filter_var(implode(',',array_filter($_POST['attached_charts'])),FILTER_SANITIZE_STRING), 1);
            set_config($dbc, 'ticket_auto_create_unscheduled', filter_var(implode(',',$_POST['auto_create_unscheduled'])),FILTER_SANITIZE_STRING);
        } else {
            $tab = explode('ticket_fields_', $ticket_type)[1];
            set_config($dbc, 'ticket_attached_charts_'.$tab, filter_var(implode(',',array_filter($_POST['attached_charts'])),FILTER_SANITIZE_STRING), 1);
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

        set_config($dbc, 'ticket_notes_limit', filter_var($_POST['ticket_notes_limit'],FILTER_SANITIZE_STRING), 1);

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
        set_config($dbc, 'ticket_delivery_time_mintime', filter_var($_POST['ticket_delivery_time_mintime'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_delivery_time_maxtime', filter_var($_POST['ticket_delivery_time_maxtime'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_recurring_status', filter_var($_POST['ticket_recurring_status'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_material_increment', filter_var($_POST['ticket_material_increment'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_notes_alert_role', filter_var($_POST['ticket_notes_alert_role'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_business_contact_add_pos', filter_var($_POST['ticket_business_contact_add_pos'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_staff_travel_default', filter_var($_POST['ticket_staff_travel_default'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_guardian_contact', filter_var($_POST['ticket_guardian_contact_value'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'ticket_recurrence_sync_upto', filter_var($_POST['ticket_recurrence_sync_upto'],FILTER_SANITIZE_STRING), 1);
    }
    else {
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
        set_config($dbc, $_POST['ticket_default_status'], filter_var($_POST['ticket_default_status_value'],FILTER_SANITIZE_STRING));
        set_config($dbc, $_POST['ticket_new_email_subject'], filter_var($_POST['ticket_new_email_subject_value'],FILTER_SANITIZE_STRING));
        set_config($dbc, $_POST['ticket_new_email_body'], filter_var($_POST['ticket_new_email_body_value'],FILTER_SANITIZE_STRING));
        set_config($dbc, $_POST['delivery_default_tabs'], filter_var($_POST['delivery_default_tabs_value'],FILTER_SANITIZE_STRING));
        set_config($dbc, $_POST['ticket_invoice_type'], filter_var($_POST['ticket_invoice_type_value'],FILTER_SANITIZE_STRING));
        set_config($dbc, 'auto_archive_complete_tickets', filter_var($_POST['auto_archive_complete_tickets'],FILTER_SANITIZE_STRING));
        set_config($dbc, 'delivery_km_service', filter_var($_POST['delivery_km_service'],FILTER_SANITIZE_STRING));
        set_config($dbc, 'incomplete_inventory_reminder_email', filter_var($_POST['incomplete_inventory_reminder_email'],FILTER_SANITIZE_STRING));
        set_config($dbc, 'ticket_notify_list', filter_var($_POST['ticket_notify_list'],FILTER_SANITIZE_STRING));
        set_config($dbc, 'ticket_notify_pdf_content', filter_var(htmlentities($_POST['ticket_notify_pdf_content']),FILTER_SANITIZE_STRING));
        set_config($dbc, 'ticket_notify_cc', filter_var($_POST['ticket_notify_cc'],FILTER_SANITIZE_STRING));
        set_config($dbc, 'ticket_notify_list_items', filter_var(implode('#*#',$_POST['ticket_notify_list_items']),FILTER_SANITIZE_STRING));
        set_config($dbc, 'ticket_email_approval', filter_var($_POST['ticket_email_approval'],FILTER_SANITIZE_STRING));
        set_config($dbc, 'ticket_approval_status', filter_var($_POST['ticket_approval_status'],FILTER_SANITIZE_STRING));
        set_config($dbc, 'delivery_default_pickup_type', filter_var($_POST['delivery_default_pickup_type'],FILTER_SANITIZE_STRING), 1);
        set_config($dbc, 'delivery_default_pickup_time', filter_var($_POST['delivery_default_pickup_time'],FILTER_SANITIZE_STRING), 1);
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
        set_config($dbc, 'delivery_type_default', filter_var($_POST['delivery_type_default'],FILTER_SANITIZE_STRING));
        set_config($dbc, 'delivery_restrict_contacts'.(!empty($_POST['tab']) ? '_'.$_POST['tab'] : ''), filter_var(implode(',',$_POST['delivery_restrict_contacts']),FILTER_SANITIZE_STRING));
    }

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
} else if($_GET['action'] == 'ticket_intake_fields') {
    set_config($dbc, filter_var($_POST['field_name'],FILTER_SANITIZE_STRING), filter_var(implode(',',$_POST['fields']),FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'generate_intake_url') {
    $type = $_GET['type'];
    $today_date = date('Y-m-d h:i:s');
    $ticket_intake_url = preg_replace('/[^\p{L}\p{N}\s]/u', '', encryptIt($type.'_'.$today_date));
    if(empty($type)) {
        $config_name = 'ticket_intake_url';
    } else {
        $config_name = 'ticket_intake_url_'.$type;
    }
    set_config($dbc, $config_name, $ticket_intake_url);
    echo '<a href="'.WEBSITE_URL.'/Ticket/index.php?edit=0&type='.$type.'&intake_key='.$ticket_intake_url.'" target="_blank">'.WEBSITE_URL.'/Ticket/index.php?edit=0&type='.$type.'&intake_key='.$ticket_intake_url.'</a>';
} else if($_GET['action'] == 'remove_intake_url') {
    $type = $_GET['type'];
    if(empty($type)) {
        $config_name = 'ticket_intake_url';
    } else {
        $config_name = 'ticket_intake_url_'.$type;
    }
    set_config($dbc, $config_name, '');
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
    set_config($dbc, 'ticket_tabs_color', filter_var(implode(',',$_POST['color_lists']),FILTER_SANITIZE_STRING));
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
    set_config($dbc, 'ticket_history_fields', filter_var($_POST['history_fields'],FILTER_SANITIZE_STRING));
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
    if( empty($_POST['milestone_timeline']) || empty($_POST['status']) || empty($_POST['to_do_date']) || empty($_POST['to_do_end_date'] || empty($_POST['to_do_start_time']) || empty($_POST['to_do_end_time']))){
        $contactid = "";
    }else{
        $contactid = $_POST['contactid'];
    }
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
    mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$ticketid of tickets inserted)");

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
        mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$ticket_schedule_id of ticket_schedule inserted)");
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
    mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$ticketid of tickets updated: status updated to Archive')");
    mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$ticketid of tickets updated: deleted updated to 1')");
    mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$ticketid of tickets updated: date_of_archival updated to $date_of_archival')");
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
    if($table_name == 'tickets') {
        $dbc->query("UPDATE `$table_name` SET `$field_name`='$value' WHERE `ticketid`='$ticketid'");
    } else {
        $dbc->query("UPDATE `$table_name` SET `$field_name`='$value' WHERE `ticketid`='$ticketid' AND `$identifier`='$id'");
    }
    mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$id of $table_name updated: $field_name updated to $value)");
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
        mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$id','{$_SESSION['contactid']}','Row #$id of tickets updated: flag_colour updated to $new_colour')");
        mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$id','{$_SESSION['contactid']}','Row #$id of tickets updated: flag_start updated to 0000-00-00')");
        mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$id','{$_SESSION['contactid']}','Row #$id of tickets updated: flag_end updated to 9999-12-31')");
    } else if($field == 'document') {
        $folder = 'download';
        $basename = preg_replace('/[^\.A-Za-z0-9]/','',$_FILES['file']['name']);
        $filename = preg_replace('/(\.[A-Za-z0-9]*)/', '$1', $basename);
        for($i = 1; file_exists($folder.$filename); $i++) {
            $filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.$i.')$1', $basename);
        }
        move_uploaded_file($_FILES['file']['tmp_name'],$folder.$filename);
        mysqli_query($dbc, "INSERT INTO `ticket_document` (`ticketid`,`document`,`created_by`,`created_date`) VALUES ('$id','$filename','".$_SESSION['contactid']."',DATE(NOW()))");
        $documentid = mysqli_insert_id($dbc);
        mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$did','{$_SESSION['contactid']}','Row #$documentid of ticket_document updated: $filename inserted')");
    } else if($field == 'alert') {
        $item = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$id'"));
        foreach($_POST['value'] as $user) {
            $user = filter_var($user,FILTER_SANITIZE_STRING);
            $link = WEBSITE_URL."/Ticket/index.php?edit=$id";
            $text = TICKET_NOUN;
            $date = date('Y/m/d');
            $sql = mysqli_query($dbc, "INSERT INTO `alerts` (`alert_date`, `alert_link`, `alert_text`, `alert_user`) VALUES ('$date', '$link', '$text', '$user')");
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
} else if($_GET['action'] == 'list_customer_service_templates') {
    $contact = filter_var($_GET['contactid'],FILTER_SANITIZE_STRING);
    $template_list = explode(',',get_contact($dbc, $contact, 'service_templates'));
    echo mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services_service_templates` WHERE `deleted`=0 AND `contactid`='$contact' AND `templateid` IN ('".implode("','",$template_list)."') OR '".implode(',',$template_list)."' = ''"))['serviceid'];
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
    $insertid = mysqli_insert_id($dbc);
    mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$insertid of ticket_comment inserted)");
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
    mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$ticketid of tickets updated: service_qty updated to $ticket_service_qty)");

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
    $ticketid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `ticketid` FROM `ticket_schedule` WHERE `id` = '$id'"))['ticketid'];
    mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$id of ticket_schedule updated: status updated to $status)");
    mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$id of ticket_schedule updated: complete updated to 1)");
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
        mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$ticketid of tickets updated: total_budget_time updated to $time)");
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
    $insertid = mysqli_insert_id($dbc);
    mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Row #$insertid of ticket_comment inserted)");
} else if($_GET['action'] == 'create_recurrence_tickets') {
    //Initialize variables
    $edit = $_GET['edit'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $repeat_type = $_POST['repeat_type'];
    $repeat_monthly = $_POST['repeat_monthly'];
    $repeat_interval = $_POST['repeat_interval'];
    $repeat_days = $_POST['repeat_days'];
    $create_starting_at = '';
    if($edit == 1) {
        if(strtotime($start_date) < strtotime(date('Y-m-d'))) {
            $create_starting_at = date('Y-m-d');
        } else {
            $create_starting_at = $start_date;
        }
    }
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
            $recurring_dates = get_recurrence_days(10, $start_date, $end_date, $repeat_type, $repeat_interval, $repeat_days, $repeat_monthly, $create_starting_at);
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
            $recurring_dates = get_recurrence_days(1, $start_date, $end_date, $repeat_type, $repeat_interval, $repeat_days, $repeat_monthly, $create_starting_at);
            mysqli_query($dbc, "UPDATE `tickets` SET `to_do_date` = '".$recurring_dates[0]."', `to_do_end_date` = '".$recurring_dates[0]."' WHERE `ticketid` = '$ticketid'");

            if($edit == 1) {
                $ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
                $main_ticketid = $ticket['main_ticketid'];
                mysqli_query($dbc, "UPDATE `tickets` SET `is_recurrence` = 0 WHERE `main_ticketid` = '$main_ticketid' AND `to_do_date` < '$create_starting_at'");
                mysqli_query($dbc, "UPDATE `tickets` SET `deleted` = 1 WHERE `main_ticketid` = '$main_ticketid' AND (`to_do_date` >= '$create_starting_at' OR IFNULL(`to_do_date`,'0000-00-00') = '0000-00-00') AND `ticketid` != '$ticketid'");
                mysqli_query($dbc, "UPDATE `ticket_recurrences` SET `deleted` = 1 WHERE `ticketid` = '$main_ticketid'");
            }

            //Insert into ticket_recurrences table to save settings for ongoing Recurrences cron job
            mysqli_query($dbc, "INSERT INTO `ticket_recurrences` (`ticketid`, `start_date`, `end_date`, `repeat_type`, `repeat_monthly`, `repeat_interval`, `repeat_days`) VALUES ('$ticketid', '$start_date', '$end_date', '$repeat_type', '$repeat_monthly', '$repeat_interval', '".implode(',',$repeat_days)."')");

            //Set main_ticketid and main_ids for records so they can be synced together
            mysqli_query($dbc, "UPDATE `tickets` SET `main_ticketid` = '$ticketid', `is_recurrence` = 1 WHERE `ticketid` = '$ticketid'");
            mysqli_query($dbc, "UPDATE `ticket_attached` SET `main_id` = `id`, `is_recurrence` = 1 WHERE `ticketid` = '$ticketid' AND `deleted` = 0");
            mysqli_query($dbc, "UPDATE `ticket_schedule` SET `main_id` = `id`, `is_recurrence` = 1 WHERE `ticketid` = '$ticketid' AND `deleted` = 0");
            mysqli_query($dbc, "UPDATE `ticket_comment` SET `main_id` = `ticketcommid`, `is_recurrence` = 1 WHERE `ticketid` = '$ticketid' AND `deleted` = 0");

            create_recurring_tickets($dbc, $ticketid, $start_date, $end_date, $repeat_type, $repeat_interval, $repeat_days, $repeat_monthly, $_POST['skip_first'], $create_starting_at);
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
} else if($_GET['action'] == 'tile_splitting') {
    foreach($_POST['tiles'] as $tile) {
        $details = $tile['name'].'#*#'.$tile['noun'].'#*#'.implode('|',$tile['types']);
        $tile = config_safe_str($tile['name']);
        set_config($dbc, 'ticket_split_tiles_'.$tile, $details);
    }
} else if($_GET['action'] == 'get_recurrence_settings') {
    $ticketid = $_POST['ticketid'];
    $main_ticketid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"))['main_ticketid'];
    $recurrence_settings = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_recurrences` WHERE `ticketid` = '$main_ticketid' AND `deleted` = 0"));
    $result = [start_date=>$recurrence_settings['start_date'], end_date=>$recurrence_settings['end_date'], repeat_type=>$recurrence_settings['repeat_type'], repeat_monthly=>$recurrence_settings['repeat_monthly'], repeat_interval=>$recurrence_settings['repeat_interval'], repeat_days=>explode(',',$recurrence_settings['repeat_days'])];
    echo json_encode($result);

} else if($_GET['action'] == 'reload_po_num_dropdown') {
    $ticketid = $_GET['ticketid'];
    $get_ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
    $ticket_po_list = array_filter(explode('#*#',$get_ticket['purchase_order']));
    $po_numbers = $dbc->query("SELECT `po_num` FROM `ticket_attached` WHERE `deleted`=0 AND `ticketid` > 0 AND `src_table` IN ('inventory_general','inventory') AND `ticketid`='$ticketid' AND IFNULL(`po_num`,'') != '' GROUP BY `po_num`");
    $po_line_list = [];
    while($po_num_line = $po_numbers->fetch_assoc()) {
        $po_num_line['po_num'] = explode('#*#', $po_num_line['po_num']);
        $po_line_list = array_merge($po_line_list, $po_num_line['po_num']);
    }
    $po_list = array_filter(array_unique(array_merge($po_line_list,$ticket_po_list)));
    sort($po_list);
    echo json_encode($po_list);

} else if($_GET['action'] == 'set_ticket_recurring') {
    $ticketid = $_GET['ticketid'];
    if($ticketid > 0) {
        mysqli_query($dbc, "UPDATE `tickets` SET `is_recurrence` = 1 WHERE `ticketid` = '$ticketid'");
        mysqli_query($dbc, "UPDATE `ticket_attached` SET `is_recurrence` = 1 WHERE `ticketid` = '$ticketid'");
        mysqli_query($dbc, "UPDATE `ticket_schedule` SET `is_recurrence` = 1 WHERE `ticketid` = '$ticketid'");
        mysqli_query($dbc, "UPDATE `ticket_comment` SET `is_recurrence` = 1 WHERE `ticketid` = '$ticketid'");
        sync_recurring_tickets($dbc, $ticketid);
    }
} else if($_GET['action'] == 'check_staff_shifts') {
    include_once('../Calendar/calendar_functions_inc.php');

    if($_POST['from_calendar'] == 1) {
        $to_do_date = $_POST['to_do_date'];
        $value_config = ',';
        if($_POST['check_shifts'] == 1) {
            $value_config .= 'Staff Check Shifts,';
        }
        if($_POST['check_days_off'] == 1) {
            $value_config .= 'Staff Check Days Off,';
        }
    } else {
        $ticketid = $_POST['ticketid'];
        if($ticketid > 0) {
            $get_ticket = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM tickets WHERE ticketid='$ticketid'"));
            $ticket_type = $get_ticket['ticket_type'];
            $to_do_date = $get_ticket['to_do_date'];
        }
        $value_config = ','.get_field_config($dbc, 'tickets').',';
        if($ticket_type == '') {
            $ticket_type = get_config($dbc, 'default_ticket_type');
        }
        if(!empty($ticket_type)) {
            $value_config .= get_config($dbc, 'ticket_fields_'.$ticket_type).',';
        }
    }

    if((strpos($value_config, ',Staff Check Shifts,') !== FALSE || strpos($value_config, ',Staff Check Days Off,') !== FALSE) && !empty(str_replace('0000-00-00','',$to_do_date))) {
        if($_POST['from_calendar'] == 1) {
            if($_POST['blocktype'] == 'team') {
                $teamid = $_POST['staffid'];
                $staff_list = [];
                $team_contacts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC);
                foreach($team_contacts as $team_contact) {
                    if(strtolower(get_contact($dbc, $team_contact['contactid'], 'category')) == 'staff') {
                        $staff_list[] = $team_contact['contactid'];
                    }
                }
            } else {
                $staff_list = [$_POST['staffid']];
            }
        } else {
            $staffid = $_POST['staffid'];
            $staff_list = array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid` = '$ticketid' AND `deleted` = 0 AND `src_table` LIKE 'Staff%'"),MYSQLI_ASSOC),'item_id');
            $staff_list[] = $staffid;
        }
        $staff_list = array_unique(array_filter($staff_list));

        $day_of_week = date('l',strtotime($to_do_date));

        $message = [];
        foreach($staff_list as $staffid) {
            $daysoff = checkShiftIntervals($dbc, $staffid, $day_of_week, $to_do_date, 'daysoff');
            if(!empty($daysoff)) {
                $message[] = get_contact($dbc, $staffid).' has Time Off';
            } else if(strpos($value_config, ',Staff Check Shifts,') !== FALSE) {
                $shifts = checkShiftIntervals($dbc, $staffid, $day_of_week, $to_do_date, 'shifts');
                if(empty($shifts)) {
                    $message[] = get_contact($dbc, $staffid).' has No Shift';
                }
            }
        }
        if(!empty($message)) {
            $result = [success=>false, message=>implode("\n",$message)];
        } else {
            $result = [success=>true, message=>''];
        }
    } else {
        $result = [success=>true, message=>''];
    }
    echo json_encode($result);
} else if($_GET['action'] == 'get_staff_no_shifts') {
    include_once('../Calendar/calendar_functions_inc.php');
    $ticketid = $_POST['ticketid'];

    $value_config = ','.get_field_config($dbc, 'tickets').',';
    if($ticketid > 0) {
        $get_ticket = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM tickets WHERE ticketid='$ticketid'"));
        $ticket_type = $get_ticket['ticket_type'];
        $to_do_date = $get_ticket['to_do_date'];
    }
    if($ticket_type == '') {
        $ticket_type = get_config($dbc, 'default_ticket_type');
    }
    if(!empty($ticket_type)) {
        $value_config .= get_config($dbc, 'ticket_fields_'.$ticket_type).',';
    }

    if((strpos($value_config, ',Staff Hide No Shift,') !== FALSE || strpos($value_config, ',Staff Hide Days Off,') !== FALSE) && !empty(str_replace('0000-00-00','',$to_do_date))) {
        $staff_list = mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `position`, `positions_allowed` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0");
        $staff_no_shifts = [];
        $day_of_week = date('l',strtotime($to_do_date));
        while($row = mysqli_fetch_assoc($staff_list)) {
            $daysoff = checkShiftIntervals($dbc, $row['contactid'], $day_of_week, $to_do_date, 'daysoff');
            if(!empty($daysoff)) {
                $staff_no_shifts[] = $row['contactid'];
            } else if(strpos($value_config, ',Staff Hide No Shift,') !== FALSE) {
                $shifts = checkShiftIntervals($dbc, $row['contactid'], $day_of_week, $to_do_date, 'shifts');
                if(empty($shifts)) {
                    $staff_no_shifts[] = $row['contactid'];
                }
            }
        }
        $result = [staff_list=>$staff_no_shifts];
    } else {
        $result = [staff_list=>[]];
    }
    echo json_encode($result);
} else if($_GET['action'] == 'reload_po_num_dropdown') {
    $ticketid = $_GET['ticketid'];
    $get_ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
    $ticket_po_list = array_filter(explode('#*#',$get_ticket['purchase_order']));
    $po_numbers = $dbc->query("SELECT `po_num` FROM `ticket_attached` WHERE `deleted`=0 AND `ticketid` > 0 AND `src_table` IN ('inventory_general','inventory') AND `ticketid`='$ticketid' AND IFNULL(`po_num`,'') != '' GROUP BY `po_num`");
    $po_line_list = [];
    while($po_num_line = $po_numbers->fetch_assoc()) {
        $po_line_list[] = $po_num_line['po_num'];
    }
    $po_list = array_unique(array_merge($po_line_list,$ticket_po_list));
    sort($po_list);
    echo json_encode($po_list);

} else if($_GET['action'] == 'set_ticket_recurring') {
    $ticketid = $_GET['ticketid'];
    if($ticketid > 0) {
        mysqli_query($dbc, "UPDATE `tickets` SET `is_recurrence` = 1 WHERE `ticketid` = '$ticketid'");
        mysqli_query($dbc, "UPDATE `ticket_attached` SET `is_recurrence` = 1 WHERE `ticketid` = '$ticketid'");
        mysqli_query($dbc, "UPDATE `ticket_schedule` SET `is_recurrence` = 1 WHERE `ticketid` = '$ticketid'");
        mysqli_query($dbc, "UPDATE `ticket_comment` SET `is_recurrence` = 1 WHERE `ticketid` = '$ticketid'");
        sync_recurring_tickets($dbc, $ticketid);
    }


} else if($_GET['action'] == 'ticket_click_history') {
    $ticketid = $_POST['ticketid'];
    $section = $_POST['section'];
    $label = $_POST['label'];
    $icon = $_POST['icon'];

    // Record History
    mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `description`) VALUES ('$ticketid','{$_SESSION['contactid']}','Clicked on $icon for $label in $section section')");
} else if($_GET['action'] == 'ticket_alerts') {
    $ticket_tab = $_POST['ticket_tab'];
    if(!empty($ticket_tab)) {
        $enabled = $_POST['enabled'];
        $subject = filter_var($_POST['subject'],FILTER_SANITIZE_STRING);
        $body = filter_var(htmlentities($_POST['body']),FILTER_SANITIZE_STRING);
        $status = $_POST['status'];
        $staffid = $_POST['staffid'];
        mysqli_query($dbc, "INSERT INTO `field_config_ticket_alerts` (`ticket_type`) SELECT '$ticket_tab' FROM (SELECT COUNT(*) rows FROM `field_config_ticket_alerts` WHERE `ticket_type`='$ticket_tab') num WHERE num.rows=0");
        mysqli_query($dbc, "UPDATE `field_config_ticket_alerts` SET `enabled`='$enabled', `subject`='$subject', `body`='$body', `status`='$status', `contactid`='$staffid' WHERE `ticket_type`='$ticket_tab'");
    }
}
if($_GET['action'] == 'get_ticket_client') {
    $ticketid = $_GET['ticketid'];
    if($ticketid > 0) {
        $ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
        $clientid = array_filter(explode(',',$ticket['clientid']))[0];
        if($clientid > 0) {
            echo $clientid;
        }
    }
} else if($_GET['action'] == 'get_restrict_contacts_list') {
    $tab = $_POST['tab'];
    $category = $_POST['category'];
    $delivery_restrict_contacts = explode(',',get_config($dbc, 'delivery_restrict_contacts'));
    $tab_delivery_restrict_contacts = explode(',',get_config($dbc, 'delivery_restrict_contacts'.($tab == '' ? '' : '_'.$tab)));
    $all_contacts = sort_contacts_query(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `deleted` = 0 AND `status` > 0 AND `category` = '".$category."'"));
    foreach($all_contacts as $delivery_contact) { ?>
        <label class="form-checkbox"><input type="checkbox" name="delivery_restrict_contacts<?= $tab == '' ? '' : '_'.$tab ?>" value="<?= $delivery_contact['contactid'] ?>" <?= in_array($delivery_contact['contactid'],$delivery_restrict_contacts) && !empty($tab) ? 'checked disabled' : '' ?> <?= in_array($delivery_contact['contactid'],$tab_delivery_restrict_contacts) ? 'checked' : '' ?>> <?= $delivery_contact['full_name'] ?></label>
    <?php }
} else if($_GET['action'] == 'get_hours_of_operation') {
    $ticketid = $_POST['ticketid'];
    $ticket_type = get_field_value('ticket_type','tickets','ticketid',$ticketid);
    $warehouseid = $_POST['warehouseid'];
    $to_do_date = $_POST['to_do_date'];
    $to_do_start_time = $_POST['to_do_start_time'];
    $result = [];
    if(!empty($to_do_date)) {
        $warehouse = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `hours_of_operation` FROM `contacts` WHERE `contactid` = '$warehouseid'"));
        $day_i = date('w',strtotime($to_do_date));
        $hours = explode('-',explode(',',$warehouse['hours_of_operation'])[$day_i]);
        $start_time = !empty($hours[0]) ? date('H:i', strtotime($hours[0])) : '';
        $end_time = !empty($hours[1]) ? date('H:i', strtotime($hours[1])) : '';
        if(!empty($start_time) && !empty($end_time)) {
            if(strtotime($to_do_start_time) < strtotime($start_time)) {
                $to_do_start_time = date('h:i a',strtotime($start_time));
            }
            if(strtotime($to_do_start_time) > strtotime($end_time)) {
                $to_do_start_time = date('h:i a',strtotime($end_time));
            }
        }
        if($hours[0] == 'closed') {
            $start_time = 'closed';
        }
        $result = [start_time=>$start_time, end_time=>$end_time, to_do_start_time=>$to_do_start_time];
    } else {
        $result = [start_time=>'', end_time=>'', to_do_start_time=>''];
    }
    echo json_encode($result);
} else if($_GET['action'] == 'get_restricted_hours') {
    $ticketid = $_POST['ticketid'];
    $ticket_type = get_field_value('ticket_type','tickets','ticketid',$ticketid);
    $to_do_date = $_POST['to_do_date'];
    $to_do_start_time = $_POST['to_do_start_time'];
    $start_time = !empty($_POST['start_time']) ? date('H:i', strtotime($_POST['start_time'])) : '';
    $end_time = !empty($_POST['end_time']) ? date('H:i', strtotime($_POST['end_time'])) : '';
    $result = [];
    if(!empty($to_do_date)) {
        if(!empty($start_time) && !empty($end_time)) {
            $delivery_restrictions = '';
            foreach(array_filter(explode(',', ROLE)) as $contact_role) {
                $restriction_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_delivery_restrictions` WHERE CONCAT(',',`security_level`,',') LIKE '%,$contact_role,%' AND `ticket_type` = '$ticket_type'"));
                if(!empty($restriction_config)) {
                    if($restriction_config['to_do_date_min'] != '') {
                        $delivery_restrictions['to_do_date_min'] = date('Y-m-d', strtotime(date('Y-m-d').($restriction_config['to_do_date_min'] >= 0 ? '+' : '').$restriction_config['to_do_date_min'].' Days'));
                    }
                    if($restriction_config['to_do_date_max'] != '') {
                        $delivery_restrictions['to_do_date_max'] = date('Y-m-d', strtotime(date('Y-m-d').($restriction_config['to_do_date_max'] >= 0 ? '+' : '').$restriction_config['to_do_date_max'].' Days'));
                    }
                    if($restriction_config['to_do_start_time_min'] != '') {
                        $delivery_restrictions['to_do_start_time_min'] = date('H:i', strtotime(date('H:i').($restriction_config['to_do_start_time_min'] >= 0 ? '+' : '').$restriction_config['to_do_start_time_min'].' Hours'));
                    }
                    if($restriction_config['to_do_start_time_max'] != '') {
                        $delivery_restrictions['to_do_start_time_max'] = date('H:i', strtotime(date('H:i').($restriction_config['to_do_start_time_max'] >= 0 ? '+' : '').$restriction_config['to_do_start_time_max'].' Hours'));
                    }
                    break;
                }
            }
            if(!empty($delivery_restrictions['to_do_start_time_min']) && strtotime($start_time) < strtotime($delivery_restrictions['to_do_start_time_min']) && ($to_do_date == $delivery_restrictions['to_do_date_min'] || empty($delivery_restrictions['to_do_date_min']))) {
                $start_time = date('H:i',strtotime($delivery_restrictions['to_do_start_time_min']));
            }
            if(!empty($delivery_restrictions['to_do_start_time_max']) && strtotime($end_time) > strtotime($delivery_restrictions['to_do_start_time_max']) && ($to_do_date == $delivery_restrictions['to_do_date_max'] || empty($delivery_restrictions['to_do_date_max']))) {
                $end_time = date('H:i',strtotime($delivery_restrictions['to_do_start_time_max']));
            }
            if(strtotime($to_do_start_time) < strtotime($start_time)) {
                $to_do_start_time = date('h:i a',strtotime($start_time));
            }
            if(strtotime($to_do_start_time) > strtotime($end_time)) {
                $to_do_start_time = date('h:i a',strtotime($end_time));
            }
        }
        $result = [start_time=>$start_time, end_time=>$end_time, to_do_start_time=>$to_do_start_time];
    } else {
        $result = [start_time=>$start_time, end_time=>$end_time, to_do_start_time=>''];
    }
    echo json_encode($result);
} else if($_GET['action'] == 'save_delivery_restriction') {
    $tab = $_POST['tab'];
    $security_level = $_POST['security_level'];
    $to_do_date_min = $_POST['to_do_date_min'];
    $to_do_date_max = $_POST['to_do_date_max'];
    $to_do_start_time_min = $_POST['to_do_start_time_min'];
    $to_do_start_time_max = $_POST['to_do_start_time_max'];

    mysqli_query($dbc, "INSERT INTO `field_config_ticket_delivery_restrictions` (`ticket_type`) SELECT '$tab' FROM (SELECT COUNT(*) rows FROM `field_config_ticket_delivery_restrictions` WHERE `ticket_type` = '$tab') num WHERE num.rows=0");
    mysqli_query($dbc, "UPDATE `field_config_ticket_delivery_restrictions` SET `security_level` = '$security_level', `to_do_date_min` = '$to_do_date_min', `to_do_date_max` = '$to_do_date_max', `to_do_start_time_min` = '$to_do_start_time_min', `to_do_start_time_max` = '$to_do_start_time_max' WHERE `ticket_type` = '$tab'");
}


