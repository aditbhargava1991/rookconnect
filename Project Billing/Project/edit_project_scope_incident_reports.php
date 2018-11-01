<?php error_reporting(0);
include_once('../include.php');
if(!isset($security)) {
    $security = get_security($dbc, $tile);
    $strict_view = strictview_visible_function($dbc, 'project');
    if($strict_view > 0) {
        $security['edit'] = 0;
        $security['config'] = 0;
    }
}
if(!isset($projectid)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
		if($tile == 'project' || $tile == config_safe_str($type_name)) {
			$project_tabs[config_safe_str($type_name)] = $type_name;
		}
	}
}
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
$project_security = get_security($dbc, 'project');
$incident_reports_count = 0; ?>
<!-- <h3><?= INC_REP_TILE ?></h3> -->
<?php 
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT incident_report_dashboard FROM field_config_incident_report"));
$value_config_ir = ','.$get_field_config['incident_report_dashboard'].',';
$incident_reports = mysqli_query($dbc, "SELECT * FROM `incident_report` WHERE `projectid`='$projectid'");
$incident_reports_count += mysqli_num_rows($incident_reports);
if($incident_reports_count > 0) {
    echo "<table class='table table-bordered'>";
    echo "<tr class='hidden-xs hidden-sm'>";
        if (strpos($value_config_ir, ','."Project".',') !== FALSE) {
            echo '<th>'.PROJECT_NOUN.'</th>';
        }
        if (strpos($value_config_ir, ','."Ticket".',') !== FALSE) {
            echo '<th>'.TICKET_NOUN.'</th>';
        }
        if (strpos($value_config_ir, ','."Program".',') !== FALSE) {
            echo '<th>Program</th>';
        }
        if (strpos($value_config_ir, ','."Member".',') !== FALSE) {
            echo '<th>Member</th>';
        }
        if (strpos($value_config_ir, ','."Client".',') !== FALSE) {
            echo '<th>Client</th>';
        }
        if (strpos($value_config_ir, ','."Type".',') !== FALSE) {
            echo '<th>Type</th>';
        }
        if (strpos($value_config_ir, ','."Staff".',') !== FALSE) {
            echo '<th>Staff</th>';
        }
        if (strpos($value_config_ir, ','."Follow Up".',') !== FALSE) {
            echo '<th>Follow Up</th>';
        }
        if (strpos($value_config_ir, ','."Date of Happening".',') !== FALSE) {
            echo '<th>Date of Happening</th>';
        }
        if (strpos($value_config_ir, ','."Date Created".',') !== FALSE) {
            echo '<th>Date Created</th>';
        }
        if (strpos($value_config_ir, ','."Location".',') !== FALSE) {
            echo '<th>Location</th>';
        }
        if (strpos($value_config_ir, ','."PDF".',') !== FALSE) {
            echo '<th>View</th>';
        }
    echo "</tr>";

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

        echo "<tr>";

        if (strpos($value_config_ir, ','."Project".',') !== FALSE) {
            $project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '".$row['projectid']."'"));
            echo '<td data-title="'.PROJECT_NOUN.'">'.get_project_label($dbc, $project).'</td>';
        }
        if (strpos($value_config_ir, ','."Ticket".',') !== FALSE) {
            $ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$row['ticketid']."'"));
            echo '<td data-title="'.TICKET_NOUN.'">'.get_ticket_label($dbc, $ticket).'</td>';
        }
        if (strpos($value_config_ir, ','."Program".',') !== FALSE) {
            echo '<td data-title="Program">'.(!empty(get_client($dbc, $row['programid'])) ? get_client($dbc, $row['programid']) : get_contact($dbc, $row['programid'])).'</td>';
        }
        if (strpos($value_config_ir, ','."Member".',') !== FALSE) {
            echo '<td data-title="Member">';
                $member_list = [];
                foreach(explode(',',$row['memberid']) as $member) {
                    if($member != '') {
                        $member_list[] = !empty(get_client($dbc, $member)) ? get_client($dbc, $member) : get_contact($dbc, $member);
                    }
                }
                echo implode(', ',$member_list) . '</td>';
        }
        if (strpos($value_config_ir, ','."Client".',') !== FALSE) {
            echo '<td data-title="Client">';
                $client_list = [];
                foreach(explode(',',$row['clientid']) as $client) {
                    if($client != '') {
                        $client_list[] = !empty(get_client($dbc, $client)) ? get_client($dbc, $client) : get_contact($dbc, $client);
                    }
                }
                echo implode(', ',$client_list) . '</td>';
        }
        if (strpos($value_config_ir, ','."Type".',') !== FALSE) {
            echo '<td data-title="Type">' . $row['type'] . '</td>';
        }
        if (strpos($value_config_ir, ','."Staff".',') !== FALSE) {
            echo '<td data-title="Staff">' . $contact_list . '</td>';
        }
        if (strpos($value_config_ir, ','."Follow Up".',') !== FALSE) {
            if($row['type'] == 'Near Miss') {
                echo '<td data-title="Follow Up">N/A</td>';
            } else {
                echo '<td data-title="Follow Up">' . $row['ir14'] . '</td>';
            }
        }
        if (strpos($value_config_ir, ','."Date of Happening".',') !== FALSE) {
            echo '<td data-title="Date of Happening">' . $row['date_of_happening'] . '</td>';
        }
        if (strpos($value_config_ir, ','."Date Created".',') !== FALSE) {
            echo '<td data-title="Date Created">' . $row['today_date'] . '</td>';
        }
        if (strpos($value_config_ir, ','."Locaiton".',') !== FALSE) {
            echo '<td data-title="Location">' . $row['location'] . '</td>';
        }
        if (strpos($value_config_ir, ','."PDF".',') !== FALSE) {
            $name_of_file = 'incident_report_'.$row['incidentreportid'].'.pdf';
			echo '<td data-title="PDF"><a href="'.WEBSITE_URL.'/Incident Report/download/'.$name_of_file.'" target="_blank" ><img src="'.WEBSITE_URL.'/img/pdf.png" width="16" height="16" border="0" alt="View">View</a>';
            if ($row['revision_number'] > 0) {
                $revision_dates = explode('*#*', $row['revision_date']);
                for ($i = 0; $i < $row['revision_number']; $i++) {
                    $name_of_file = 'incident_report_'.$row['incidentreportid'].'_'.($i+1).'.pdf';
                    echo '<br /><a href="'.WEBSITE_URL.'/Incident Report/download/'.$name_of_file.'" target="_blank" ><img src="'.WEBSITE_URL.'/img/pdf.png" width="16" height="16" border="0" alt="view">View R'.($i+1).': '.$revision_dates[$i].'</a>';
                }
            }
            echo '</td>';
        }

        echo "</tr>";
    }
    echo '</table>';
} else {
	echo "<h2>No ".INC_REP_TILE." Found</h2>";
} ?>
<?php include('next_buttons.php'); ?>