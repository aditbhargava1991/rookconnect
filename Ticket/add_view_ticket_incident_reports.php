<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>'.INC_REP_TILE.'</h3>') ?>
<?php $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT incident_report_dashboard, incident_types FROM field_config_incident_report"));
if(vuaed_visible_function($dbc, 'incident_report') == 1) {
	echo '<a href="../Incident Report/add_incident_report.php?ticketid='.$ticketid.'" onclick="overlayIFrameSlider(this.href,\'75%\',false,true,\'auto\',true); return false;" data-history-label="Start '.INC_REP_NOUN.'" class="btn brand-btn pull-right">Add '.INC_REP_NOUN.'</a>';
}
echo '<div class="clearfix"></div>';
$value_config_ir = ','.$get_field_config['incident_report_dashboard'].',';
$incident_reports = mysqli_query($dbc, "SELECT * FROM `incident_report` WHERE `ticketid`='$ticketid' AND '$ticketid' > 0");
$incident_reports_count = mysqli_num_rows($incident_reports);
if($generate_pdf) {
    ob_clean();
}
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
            echo '<th>Tab</th>';
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
        if ($debrief_accordion) {
            echo '<th>Include in Reminder</th>';
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
                $member_ir_output = [];
                foreach(explode(',',$row['memberid']) as $member) {
                    if($member != '') {
                        $member_ir_output[] = !empty(get_client($dbc, $member)) ? get_client($dbc, $member) : get_contact($dbc, $member);
                    }
                }
                echo implode(', ',$member_ir_output) . '</td>';
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
            echo '<td data-title="Tab">' . $row['type'] . '</td>';
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
        if (strpos($value_config_ir, ','."Location".',') !== FALSE) {
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
        if ($debrief_accordion) {
            echo '<td data-title="Include in Reminder" align="center"><input type="checkbox" name="inc_rep_reminder[]" value="'.$row['incidentreportid'].'" style="height: 20px; width: 20px;"></td>';
        }

        echo "</tr>";
    }
    echo '</table>';
} else {
	echo "<h4>No ".INC_REP_TILE." Found</h4>";
}
if($generate_pdf) {
    $pdf_contents[] = ['', ob_get_contents()];
}
?>