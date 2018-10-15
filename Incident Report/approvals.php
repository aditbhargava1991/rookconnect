<?php $admin_fields = explode(',', get_config($dbc, 'incident_report_admin_fields'));
if(empty($status_field)) {
    $status_field = 'status';
}
if(empty($approved_by_field)) {
    $approved_by_field = 'approved_by';
}
if($num_rows > 0) {

    // Added Pagination //
    if(empty($_POST['search_incident_reports'])) {
        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
    }
    // Pagination Finish //

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT incident_report_dashboard FROM field_config_incident_report"));
    $value_config = ','.$get_field_config['incident_report_dashboard'].',';

    echo "<table class='table table-bordered'>";
    echo "<tr class='hidden-xs hidden-sm'>";
        if (strpos($value_config, ','."Program".',') !== FALSE) {
            echo '<th>Program</th>';
        }
        if (strpos($value_config, ','."Project Type".',') !== FALSE) {
            echo '<th>'.PROJECT_NOUN.' Type</th>';
        }
        if (strpos($value_config, ','."Project".',') !== FALSE) {
            echo '<th>'.PROJECT_NOUN.'</th>';
        }
        if (strpos($value_config, ','."Ticket".',') !== FALSE) {
            echo '<th>'.TICKET_NOUN.'</th>';
        }
        if (strpos($value_config, ','."Member".',') !== FALSE) {
            echo '<th>Member</th>';
        }
        if (strpos($value_config, ','."Client".',') !== FALSE) {
            echo '<th>Client</th>';
        }
        if (strpos($value_config, ','."Type".',') !== FALSE) {
            echo '<th>Type</th>';
        }
        if (strpos($value_config, ','."Staff".',') !== FALSE) {
            echo '<th>Staff</th>';
        }
        if (strpos($value_config, ','."Follow Up".',') !== FALSE) {
            echo '<th>Follow Up</th>';
        }
        if (strpos($value_config, ','."Date of Happening".',') !== FALSE) {
            echo '<th>Date of Happening</th>';
        }
        if (strpos($value_config, ','."Date of Incident".',') !== FALSE) {
            echo '<th>Date of Incident</th>';
        }
        if (strpos($value_config, ','."Date Created".',') !== FALSE) {
            echo '<th>Date Created</th>';
        }
        if (strpos($value_config, ','."Location".',') !== FALSE) {
            echo '<th>Location</th>';
        }
		if($page_status != 'Pending') {
			echo '<th>'.($page_status == 'Revision' ? 'In Revision' : ($page_status == 'Review' ? 'Under Review' : 'Approved')).'</th>';
		}
        if (strpos($value_config, ','."PDF".',') !== FALSE) {
            echo '<th>View</th>';
        }
        if (in_array('notes', $admin_fields)) {
            echo '<th>Notes</th>';
        }
        if(vuaed_visible_function($dbc, 'incident_report') == 1) {
            echo '<th>Function</th>';
        }
    echo "</tr>";

    while($row = mysqli_fetch_array( $result ))
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

        $project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '".$row['projectid']."'"));
        $project = get_project_label($dbc, $project);
        $ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$row['ticketid']."'"));
        $ticket = get_ticket_label($dbc, $ticket);
        $program = (!empty(get_client($dbc, $row['programid'])) ? get_client($dbc, $row['programid']) : get_contact($dbc, $row['programid']));
        $member_list = [];
		$status = $row[$status_field];
		
        foreach(explode(',',$row['memberid']) as $member) {
            if($member != '') {
                $member_list[] = !empty(get_client($dbc, $member)) ? get_client($dbc, $member) : get_contact($dbc, $member);
            }
        }
        $member_list = implode(', ',$member_list);
        $client_list = [];
        foreach(explode(',',$row['clientid']) as $client) {
            if($client != '') {
                $client_list[] = !empty(get_client($dbc, $client)) ? get_client($dbc, $client) : get_contact($dbc, $client);
            }
        }
        $client_list = implode(', ',$client_list);
        $project_type = $project_vars[$row['project_type']];

        $hidden_row = '';
        if(!empty($_POST['search_incident_reports'])) {
            $search_key = $_POST['search_incident_reports'];
            if(stripos($project, $search_key) === FALSE && stripos($ticket, $search_key) === FALSE && stripos($program, $search_key) === FALSE && stripos($member_list, $search_key) === FALSE && stripos($client_list, $search_key) === FALSE && stripos($contact_name, $search_key) === FALSE && stripos($project_type, $search_key) === FALSE) {
                $hidden_row = 'style="display: none;"';
            }
        }
        echo "<tr $hidden_row>";

        if (strpos($value_config, ','."Program".',') !== FALSE) {
            echo '<td data-title="Program">'.$program.'</td>';
        }
        if (strpos($value_config, ','."Project Type".',') !== FALSE) {
            echo '<td data-title="'.PROJECT_NOUN.' Noun">'.$project_type.'</td>';
        }
        if (strpos($value_config, ','."Project".',') !== FALSE) {
            echo '<td data-title="'.PROJECT_NOUN.'">'.$project.'</td>';
        }
        if (strpos($value_config, ','."Ticket".',') !== FALSE) {
            echo '<td data-title="'.TICKET_NOUN.'">'.$ticket.'</td>';
        }
        if (strpos($value_config, ','."Member".',') !== FALSE) {
            echo '<td data-title="Member">'.$member_list.'</td>';
        }
        if (strpos($value_config, ','."Client".',') !== FALSE) {
            echo '<td data-title="Client">'.$client_list.'</td>';
        }
        if (strpos($value_config, ','."Type".',') !== FALSE) {
            echo '<td data-title="Type">' . $row['type'] . '</td>';
        }
        if (strpos($value_config, ','."Staff".',') !== FALSE) {
            echo '<td data-title="Staff">' . implode(', ', $contact_list) . '</td>';
        }
        if (strpos($value_config, ','."Follow Up".',') !== FALSE) {
            if($row['type'] == 'Near Miss') {
                echo '<td data-title="Follow Up">N/A</td>';
            } else {
                echo '<td data-title="Follow Up">' . $row['ir14'] . '</td>';
            }
        }
        if (strpos($value_config, ','."Date of Happening".',') !== FALSE) {
            echo '<td data-title="Date of Happening">' . $row['date_of_happening'] . '</td>';
        }
        if (strpos($value_config, ','."Date of Incident".',') !== FALSE) {
            echo '<td data-title="Date of Incident">' . $row['incident_date'] . '</td>';
        }
        if (strpos($value_config, ','."Date Created".',') !== FALSE) {
            echo '<td data-title="Date Created">' . $row['today_date'] . '</td>';
        }
        if (strpos($value_config, ','."Location".',') !== FALSE) {
            echo '<td data-title="Location">' . $row['location'] . '</td>';
        }
		if($page_status != 'Pending') {
			echo '<td data-title="'.($page_status == 'Revision' ? 'In Revision' : ($page_status == 'Review' ? 'Under Review' : 'Approved')).'">'.profile_id($dbc, $row[$approved_by_field], false).'</td>';
		}
        if (strpos($value_config, ','."PDF".',') !== FALSE) {
            $name_of_file = 'incident_report_'.$row['incidentreportid'].'.pdf';
			echo '<td data-title="PDF">'.(file_exists('download/'.$name_of_file) ? '<a href="download/'.$name_of_file.'" target="_blank" ><img src="'.WEBSITE_URL.'/img/pdf.png" width="16" height="16" border="0" alt="View">View</a>' : '');
            if ($row['revision_number'] > 0) {
                $revision_dates = explode('*#*', $row['revision_date']);
                for ($i = 0; $i < $row['revision_number']; $i++) {
                    $name_of_file = 'incident_report_'.$row['incidentreportid'].'_'.($i+1).'.pdf';
					if(file_exists('download/'.$name_of_file)) {
						echo '<br /><a href="download/'.$name_of_file.'" target="_blank" ><img src="'.WEBSITE_URL.'/img/pdf.png" width="16" height="16" border="0" alt="view">View R'.($i+1).': '.$revision_dates[$i].'</a>';
					}
                }
            }
            echo '</td>';
        }
        if (in_array('notes', $admin_fields)) {
            $new_notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`id`) num_rows FROM `incident_report_comment` WHERE `incidentreportid` = '".$row['incidentreportid']."' AND `deleted` = 0 AND CONCAT(',',`seen_by`,',') NOT LIKE '%,".$_SESSION['contactid'].",%' AND `type` = 'approval_note'"))['num_rows'];
            echo '<td data-title="Notes">
                <a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/quick_action_notes.php?tile=incident_report_approvals&id='.$row['incidentreportid'].'\'); return false;" style="position: relative;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" class="inline-img no-toggle" title="Add Note">'.($new_notes > 0 ? '<span class="custom-chart-comments" style="">'.$new_notes.'</span>' : '').'</a>
            </td>';
        }
		echo '<td data-title="Function">
			<select name="status" class="chosen-select-deselect" data-id="'.$row['incidentreportid'].'" onchange="setStatus(this);"><option />
				<option '.(empty($status) || $status == 'Pending' ? 'selected' : '').' value="Pending">Pending</option>
				<option '.($status == 'Revision' ? 'selected' : '').' value="Revision">In Revision</option>
				<option '.($status == 'Review' ? 'selected' : '').' value="Review">In Review</option>
				<option '.($status == 'Done' ? 'selected' : '').' value="Done">Approved</option>
			</select>
		</td>';

        echo "</tr>";
    }

    echo '</table></div>';
    // Added Pagination //
    if(empty($_POST['search_incident_reports'])) {
        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
    }
    // Pagination Finish //
} else {
    echo "<h2>No Record Found.</h2>";
}