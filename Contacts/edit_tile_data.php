<?php if ($field_option == "Client Support Plan") {
	// $tmp = $contactid;
	// $contactid = $_GET['edit']; ?>
	<!--<h3>Individual Service Plan (ISP)</h3>
	<div class="form-group">
		<?php // $display_contact = $contactid;
		// $from_url = urlencode(WEBSITE_URL.'/'.FOLDER_URL.'/add_contacts.php?category='.$url_category.'&contactid='.$contactid.'&subtab='.$subtab);
		// include_once('../Individual Support Plan/support_plan_list.php'); ?>
	</div>-->
	<?php // $contactid = $tmp;
} else if ($field_option == "Medications Client Profile") {
	$tmp = $contactid;
	$contactid = $_GET['edit']; ?>
	<h3>Medications</h3>
	<div class="form-group">
		<?php $display_contact = $contactid;
		$from_url = urlencode(WEBSITE_URL.'/'.FOLDER_URL.'/add_contacts.php?category='.$url_category.'&contactid='.$contactid.'&subtab='.$subtab);
		include_once('../Medication/medication_list.php'); ?>
	</div>
	<?php $contactid = $tmp;
} else if ($field_option == "Client Medical Charts") {
	// $tmp = $contactid;
	// $contactid = $_GET['edit']; ?>
	<?php // include_once '../Medical Charts/config.php';
	// $display_contact = $contactid;
	// $return_url = urlencode(WEBSITE_URL.'/'.FOLDER_URL.'/add_contacts.php?category='.$url_category.'&contactid='.$contactid.'&subtab='.$subtab); ?>
	<!--<h3>Bowel Movement</h3>
	<div class="form-group">
		<?php // include_once('../Medical Charts/bowel_movement_list.php'); ?>
	</div>
	<h3>Seizure Record</h3>
	<div class="form-group">
		<?php // include_once('../Medical Charts/seizure_record_list.php'); ?>
	</div>
	<h3>Daily Water Temp</h3>
	<div class="form-group">
		<?php // include_once('../Medical Charts/daily_water_temp_list.php'); ?>
	</div>
	<h3>Blood Glucose</h3>
	<div class="form-group">
		<?php // include_once('../Medical Charts/blood_glucose_list.php'); ?>
	</div>-->
	<?php //$contactid = $tmp;
} else if ($field_option == "Client Daily Log Notes") {
	$tmp = $contactid;
	$contactid = $_GET['edit']; ?>
	<h3>Daily Log Notes</h3>
	<div class="form-group daily_log_note_div">
		<?php $display_contact = $contactid;
		$min_display = 1;
		$from_url = urlencode(WEBSITE_URL.'/'.FOLDER_URL.'/add_contacts.php?category='.$url_category.'&contactid='.$contactid.'&subtab='.$subtab);
		include_once('../Daily Log Notes/log_note_list.php'); ?>
	</div>
	<?php $contactid = $tmp;
} else if ($field_option == "Client Top Daily Log Notes") {
	$tmp = $contactid;
	$contactid = $_GET['edit'];
	$_GET['mode'] = 'read';
	$_GET['max'] = 5; ?>
	<h3>Daily Log Notes</h3>
	<div class="form-group">
		<?php $display_contact = $contactid;
		include_once('../Daily Log Notes/log_note_list.php'); ?>
	</div>
	<script>
	$(document).ready(function() {
		$('#no-more-tables,#no-more-tables *').show().last().click();
	});
	</script>
	<?php $contactid = $tmp;
} else if ($field_option == "Client Activities Social Story") {
	// $tmp = $contactid;
	// $contactid = $_GET['edit']; ?>
	<!--<h3>Activities</h3>
	<div class="form-group">
		<?php // include_once('../Social Story/config.php');
		// $search_client = $contactid;
		// $from_url = urlencode(WEBSITE_URL.'/'.FOLDER_URL.'/add_contacts.php?category='.$url_category.'&contactid='.$contactid.'&subtab='.$subtab);
		// include_once('../Social Story/activities_list.php'); ?>
	</div>-->
	<?php // $contactid = $tmp;
} else if ($field_option == "Client Communication Social Story") {
	// $tmp = $contactid;
	// $contactid = $_GET['edit']; ?>
	<!--<h3>Communication</h3>
	<div class="form-group">
		<?php // include_once('../Social Story/config.php');
		// $search_client = $contactid;
		// $from_url = urlencode(WEBSITE_URL.'/'.FOLDER_URL.'/add_contacts.php?category='.$url_category.'&contactid='.$contactid.'&subtab='.$subtab);
		// include_once('../Social Story/communication_list.php'); ?>
	</div>-->
	<?php // $contactid = $tmp;
} else if ($field_option == "Client Routines Social Story") {
	// $tmp = $contactid;
	// $contactid = $_GET['edit']; ?>
	<!--<h3>Routines</h3>
	<div class="form-group">
		<?php include_once('../Social Story/config.php');
		// $search_client = $contactid;
		// $from_url = urlencode(WEBSITE_URL.'/'.FOLDER_URL.'/add_contacts.php?category='.$url_category.'&contactid='.$contactid.'&subtab='.$subtab);
		// include_once('../Social Story/routines_list.php'); ?>
	</div>-->
	<?php // $contactid = $tmp;
} else if ($field_option == "Client Protocols Social Story") {
	// $tmp = $contactid;
	// $contactid = $_GET['edit']; ?>
	<!--<h3>Protocols</h3>
	<div class="form-group">
		<?php // include_once('../Social Story/config.php');
		// $search_client = $contactid;
		// $from_url = urlencode(WEBSITE_URL.'/'.FOLDER_URL.'/add_contacts.php?category='.$url_category.'&contactid='.$contactid.'&subtab='.$subtab);
		// include_once('../Social Story/protocols_list.php'); ?>
	</div>-->
	<?php // $contactid = $tmp;
} else if ($field_option == "Client Key Methodologies Social Story") {
	// $tmp = $contactid;
	// $contactid = $_GET['edit']; ?>
	<!--<h3>Key Methodologies</h3>
	<div class="form-group">
		<?php // include_once('../Social Story/config.php');
		// $search_client = $contactid;
		// $from_url = urlencode(WEBSITE_URL.'/'.FOLDER_URL.'/add_contacts.php?category='.$url_category.'&contactid='.$contactid.'&subtab='.$subtab);
		// include_once('../Social Story/key_methodologies_list.php'); ?>
	</div>-->
	<?php // $contactid = $tmp;
} else if ($field_option == "Patient Block Booking") {
	$tmp = $contactid;
	$contactid = $_GET['edit']; ?>
    <h3><?= get_contact($dbc, $contactid) ?> Block Booking</h3>
	<div id="no-more-tables">
		<table border="1px" class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th>Appointment Date, Time &amp; Day</th>
				<th>Staff</th>
				<th>Injury</th>
				<th>Status</th>
			</tr>
			<?php $patient_bb = mysqli_query($dbc, "SELECT appoint_date, end_appoint_date, bookingid, injuryid, follow_up_call_status, therapistsid FROM booking WHERE deleted=0 AND patientid = '$contactid' AND '$contactid' != '' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= DATE(NOW()) ORDER BY appoint_date");
			while($row_bb = mysqli_fetch_array( $patient_bb ))
			{
				$appoint_date = explode(' ', $row_bb['appoint_date']);
				echo '<tr nobr="true">';
				echo '<td>'.$row_bb['appoint_date'].' : '.date("l", strtotime($appoint_date[0])).'</td>';
				echo '<td>'.get_contact($dbc, $row_bb['therapistsid']).'</td>';
				echo  '<td>' . get_all_from_injury($dbc, $row_bb['injuryid'], 'injury_name').' : '.get_all_from_injury($dbc, $row_bb['injuryid'], 'injury_type') . '</td>';
				echo  '<td>' . $row_bb['follow_up_call_status'] . '</td>';
				echo '</tr>';
			} ?>

		</table>
	</div>
	<?php $contactid = $tmp;
} else if ($field_option == "Incident Reports") {
	$project_tabs = get_config($dbc, 'project_tabs');
	if($project_tabs == '') {
	    $project_tabs = 'Client,SR&ED,Internal,R&D,Business Development,Process Development,Addendum,Addition,Marketing,Manufacturing,Assembly';
	}
	$project_tabs = explode(',',$project_tabs);
	$project_vars = [];
	foreach($project_tabs as $item) {
	    $project_vars[preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)))] = $item;
	}
	$display_contact = $contactid;
	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT incident_report_dashboard FROM field_config_incident_report"));
	$value_config_ir = ','.$get_field_config['incident_report_dashboard'].',';
	$query_check_credentials = "SELECT * FROM incident_report WHERE (CONCAT(',',`contactid`,',') LIKE '%,$display_contact,%' AND `contactid` != '') OR (CONCAT(',',`clientid`,',') LIKE '%,$display_contact,%' AND `clientid` != '') OR (CONCAT(',',`memberid`,',') LIKE '%,$display_contact,%' AND `memberid` != '') OR `programid` = '$display_contact' ORDER BY incidentreportid DESC";
	$result = mysqli_query($dbc, $query_check_credentials);
	$num_rows = mysqli_num_rows($result);

	if($num_rows > 0) {
        echo "<table id='no-more-tables' class='table table-bordered'>";
        echo "<tr class='hidden-xs hidden-sm'>";
            if (strpos($value_config_ir, ','."Program".',') !== FALSE) {
                echo '<th>Program</th>';
            }
            if (strpos($value_config_ir, ','."Project Type".',') !== FALSE) {
                echo '<th>'.PROJECT_NOUN.' Type</th>';
            }
            if (strpos($value_config_ir, ','."Project".',') !== FALSE) {
                echo '<th>'.PROJECT_NOUN.'</th>';
            }
            if (strpos($value_config_ir, ','."Ticket".',') !== FALSE) {
                echo '<th>'.TICKET_NOUN.'</th>';
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
	        $contact_list = implode(', ', $contact_list);

            echo "<tr>";

            if (strpos($value_config_ir, ','."Program".',') !== FALSE) {
                echo '<td data-title="Program">'.(!empty(get_client($dbc, $row['programid'])) ? get_client($dbc, $row['programid']) : get_contact($dbc, $row['programid'])).'</td>';
            }
            if (strpos($value_config_ir, ','."Project Type".',') !== FALSE) {
                echo '<td data-title="'.PROJECT_NOUN.' Type">'.$project_vars[$row['project_type']].'</td>';
            }
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

            echo "</tr>";
	    }
	    echo '</table>';
	} else { ?>
		<p>No <?= INC_REP_NOUN ?> found.</p>
	<?php }
} else if ($field_option == "Medical Details Medications") {
	$tmp = $contactid;
	$contactid = $_GET['edit'];
	include('../Members/add_medications.php');
	$contactid = $tmp;
} else if ($field_option == "Project Addition") { ?>
	<h4><?= PROJECT_TILE ?></h4>
	<?php $result = mysqli_query($dbc, "SELECT * FROM `projects` WHERE `project_lead`='".$contactid."' AND `deleted`=0");
	$project_security = get_security($dbc, 'project');
	if(mysqli_num_rows($result) > 0) { ?>
		<table class="table table-bordered">
			<tr>
				<th><?= PROJECT_NOUN ?></th>
			</tr>
			<?php while($row = mysqli_fetch_assoc($result)) { ?>
				<tr>
					<td data-title="<?= PROJECT_NOUN ?>">
						<?= ($project_security['edit'] > 0 ? '<a href="../Project/project.php?edit='.$row['projectid'].'">' : '').PROJECT_NOUN.' #'.$row['projectid'].' '.$row['project_name'].($project_security['edit'] > 0 ? '</a>' : '') ?>
					</td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo '<label class="col-sm-12 control-label">No Records Found.</label>';
	}
} else if ($field_option == "Estimates Addition") { ?>
	<h4><?= ESTIMATE_TILE ?></h4>
	<?php $result = mysqli_query($dbc, "SELECT * FROM `estimate` WHERE (CONCAT(',',`assign_staffid`,',') LIKE '%,".$contactid.",%' OR `clientid` = '".$contactid."' OR `businessid` = '".$contactid."') AND `deleted`=0");
	if(mysqli_num_rows($result) > 0) { ?>
		<table class="table table-bordered">
			<tr>
				<th>Customer</th>
				<th>Contact</th>
				<th><?= ESTIMATE_TILE ?> Status</th>
				<th>Next Action</th>
				<th>Follow Up Date</th>
				<th>Function</th>
			</tr>
			<?php while($row = mysqli_fetch_assoc($result)) { ?>
				<?php $estimate_action = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `estimate_actions` WHERE `estimateid` = '".$row['estimateid']."' AND `deleted` = 0"));
					if($estimate_action['action'] == 'email') {
						$next_action = 'Email';
					} else if($estimate_action['action'] == 'phone') {
						$next_action = 'Phone Call';
					} else {
						$next_action = '';
					} ?>
				<tr>
					<td data-title="Customer"><?= get_client($dbc, $row['businessid']) ?></td>
					<td data-title="Contact"><?= get_contact($dbc, $row['clientid']) ?></td>
					<td data-title="Estimate Status"><?= $row['status'] ?></td>
					<td data-title="Next Action"><?= $next_action ?></td>
					<td data-title="Follow Up Date"><?= $estimate_action['due_date'] ?></td>
					<td data-title="Function"><a href="../Estimate/estimates.php?view=<?= $row['estimateid'] ?>">View</a></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo '<label class="col-sm-12 control-label">No Records Found.</label>';
	}
} else if ($field_option == "Ticket Addition") { ?>
	<h4><?= TICKET_TILE ?></h4>
	<?php $result = mysqli_query($dbc, "SELECT * FROM `tickets` WHERE ((`ticketid` IN (SELECT `ticketid` FROM `ticket_attached` WHERE `src_table` IN ('Staff', 'Members','Clients') AND `item_id`='".$contactid."') OR CONCAT(',',`contactid`,',') LIKE '%,".$contactid.",%' OR CONCAT(',',`internal_qa_contactid`,',') LIKE '%,".$contactid.",%' OR CONCAT(',',`deliverable_contactid`,',') LIKE '%,".$contactid.",%') OR CONCAT(',',`clientid`,',') LIKE '%,$contactid,%' OR `businessid` = '$contactid') AND `deleted`=0");
	if(mysqli_num_rows($result) > 0) {
		$db_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `tickets_dashboard` FROM `field_config`"))['tickets_dashboard'];
		if($db_config == '') {
			$db_config = 'Contact,Heading,Services,Status,Deliverable Date';
		}
		$db_config = explode(',',$db_config);
		$ticket_status_list = explode(',',get_config($dbc, 'ticket_status'));
		$project_types = [];
		foreach(explode(',',get_config($dbc, 'project_tabs')) as $type_name) {
			$project_types[preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($type_name)))] = $type_name;
		}
		$tile_security = get_security($dbc, 'ticket');
		include('../Ticket/ticket_table.php');
	} else {
		echo '<label class="col-sm-12 control-label">No Records Found.</label>';
	}
} else if($field_option == "Ticket Notes Addition") {
	$result = mysqli_query($dbc, "SELECT * FROM `ticket_comment` WHERE `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE (CONCAT(',',`clientid`,',') LIKE '%,$contactid,%' OR `businessid` = '$contactid') AND `deleted`=0) AND `deleted` = 0 ORDER BY `ticketid`");
	$current_ticketid = '';
	if(mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_assoc($result)) {
			if($current_ticketid != $row['ticketid']) {
				$current_ticketid = $row['ticketid'];
				echo '<h5><a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row['ticketid'].'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\'); return false;">'.get_ticket_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$row['ticketid']."'")))."</a></h5>";
			}
			echo '<div>';
			echo profile_id($dbc, $row['created_by']);
			echo '<div class="pull-right" style="width: calc(100% - 3.5em);">'.html_entity_decode($row['comment']);
			echo "<em>Added by ".get_contact($dbc, $row['created_by'])." at ".$row['created_date'];
			if($row['reference_contact'] > 0) {
				echo "<br />References ".get_contact($dbc, $row['reference_contact']);
			}
			foreach(explode(',',$row['email_comment']) as $assignid) {
				if($assignid > 0) {
					echo "<br />Assigned to ".get_contact($dbc, $assignid);
				}
			}
			echo '</em>';
			echo '</div><div class="clearfix"></div><hr></div>';
		}
	} else {
		echo '<label class="col-sm-12 control-label">No Records Found.</label>';
	}
} else if ($field_option == "Tasks Addition") { ?>
	<h4><?= TASK_TILE ?></h4>
	<div class="pad-5 gap-bottom">
        <b>Total Time Tracked:
        <?php
            $total_time = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`timer_tracked`) `total_time` FROM `time_cards` WHERE `customer`='$contactid' AND `customer` > 0 AND `deleted`=0"));
            echo !empty($total_time['total_time']) ? time_decimal2time($total_time['total_time']) : '0:00';
        ?></b>
    </div>
    <?php $result = mysqli_query($dbc, "SELECT `t`.`tasklistid`, `t`.`clientid`, `t`.`businessid`, `t`.`heading`, `t`.`created_date`, `t`.`task_tododate`, `b`.`board_security` FROM `tasklist` `t` JOIN `task_board` `b` ON (`t`.`task_board`=`b`.`taskboardid`) WHERE ((`t`.`clientid`='$contactid' AND `t`.`clientid` > 0) OR (`t`.`businessid`='$contactid' AND `t`.`businessid` > 0)) AND `t`.`deleted`=0");
    if ( mysqli_num_rows($result) > 0 ) {
		echo '<ul>';
        while ( $row_tasks=mysqli_fetch_assoc($result) ) { ?>
            <li><a href="" onclick="overlayIFrameSlider('../Tasks_Updated/add_task.php?tasklistid=<?=$row_tasks['tasklistid']?>', '50%', false, true, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;">Task #<?= $row_tasks['tasklistid'] ?>: <?= $row_tasks['heading'] ?></a></li><?php
        }
        echo '</ul>';
	} else {
		echo '<label class="col-sm-12 control-label">No Records Found.</label>';
	}
} else if ($field_option == "Account Statement") {
	$tmp = $contactid;
	$contactid = $_GET['edit']; ?>
    <script>
    function update_statement_table() {
        var injuryid = $('[name=statement_search_injury]').val();
        var from_date = $('[name=statement_search_from]').val();
        var to_date = $('[name=statement_search_to]').val();
        var options = $('[name="statement_options[]"]:checked').map( function() { return this.value; }).get().join(",");
        $.ajax({
            url: '../Contacts/add_contact_ajax.php?fill=statement',
            method: 'POST',
            data: { contact: '<?= $contactid ?>', injury: injuryid, from: from_date, to: to_date, option_list: options },
            success: function(response) {
                $('#statement_table_body').html(response);
            }
        });
    }
    <?php if($hide_filter_options) { } else { ?>
        function reset_statement_table() {
            $('[name=statement_search_injury] option:selected').removeAttr('selected');
            $('[name=statement_search_injury]').trigger('change.select2');
            $('[name=statement_search_from]').val('');
            $('[name=statement_search_to]').val('');
            $('[name="statement_options[]"]:checked').removeAttr('checked');
            $('[name="statement_options[]"][value="outstanding"]').prop('checked','checked');
            $('[name="statement_options[]"][value="paid"]').prop('checked','checked');
            $('[name="statement_options[]"][value="payments"]').prop('checked','checked');
            update_statement_table();
        }
        function print_statement_table() {
            var injuryid = $('[name=statement_search_injury]').val();
            var from_date = $('[name=statement_search_from]').val();
            var to_date = $('[name=statement_search_to]').val();
            var options = $('[name="statement_options[]"]:checked').map( function() { return this.value; }).get().join(",");
            $.ajax({
                url: '../Contacts/add_contact_ajax.php?fill=statement_pdf',
                method: 'POST',
                data: { contact: '<?= $contactid ?>', injury: injuryid, from: from_date, to: to_date, option_list: options },
                success: function(response) {
                    window.open(response);
                }
            });
        }
    <?php } ?>
    $(document).ready(function() {
        update_statement_table();
    });
    </script>
    <h3><?= get_contact($dbc, $contactid) ?> Account Statement</h3>
    <?php if($hide_filter_options) { ?>
        <input type="checkbox" checked name="statement_options[]" value="outstanding" style="display:none;">
        <input type="checkbox" checked name="statement_options[]" value="paid" style="display:none;">
        <input type="checkbox" checked name="statement_options[]" value="payments" style="display:none;">
        <input type="checkbox" checked name="statement_options[]" value="insurer" style="display:none;">
    <?php } else { ?>
        <div class="search-group">
            <div class="form-group col-lg-9 col-md-8 col-sm-12 col-xs-12">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="col-sm-4">
                        <label for="site_name" class="control-label">
                            <span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Check the options to display for the statement."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                            Include Invoices:</label>
                    </div>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="statement_options[]" value="saved"> Include Saved (Unbilled) Invoices</label>
                        <label class="form-checkbox"><input type="checkbox" checked name="statement_options[]" value="outstanding"> Include Oustanding Invoices</label>
                        <label class="form-checkbox"><input type="checkbox" checked name="statement_options[]" value="paid"> Include Paid Invoices</label>
                        <label class="form-checkbox"><input type="checkbox" checked name="statement_options[]" value="payments"> Show Payment Transactions</label>
                        <label class="form-checkbox"><input type="checkbox" checked name="statement_options[]" value="insurer"> Show Insurer Transactions</label>
                    </div>
                </div>
            </div>
            <div class="form-group col-lg-3 col-md-4 col-sm-12 col-xs-12">
                <div style="display:inline-block; padding: 0 0.5em;">
                    <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here after you have selected your options."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <button name="statement_update" value="Search" class="btn brand-btn mobile-block" onclick="update_statement_table(); return false;">Search</button>
                    <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click to refresh the table and display the full invoice."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <button name="statement_reset" value="Display All" class="btn brand-btn mobile-block" onclick="reset_statement_table(); return false;">Display All</button><br />
                    <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click to download the PDF of the Account Statement."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <button name="statement_pdf" value="PDF" class="btn brand-btn mobile-block" onclick="print_statement_table(); return false;">Print Statement</button>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    <?php } ?>
	<div id="no-more-tables">
		<table border="1px" class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th>Transaction Date</th>
				<th>Staff</th>
				<?= $category == 'Patient' ? '<th>Injury</th>' : '' ?>
				<th>Services</th>
				<th><?= $category ?></th>
				<th>Payment Type</th>
				<th>Payment</th>
				<th>Balance</th>
			</tr>
			<tbody id="statement_table_body">
			</tbody>
		</table>
	</div>
	<?php $contactid = $tmp;
} else if ($field_option == "Sales Order Addition") {
	$so_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_so`"));
	$so_value_config = ','.$so_field_config['fields'].','; ?>

	<h5>Ongoing <?= SALES_ORDER_TILE ?></h5><?php
	$sot_list = mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE (`customerid` = '$contactid' OR `primary_staff` = '$contactid' OR CONCAT(',',`assign_staff`,',') LIKE '%,$contactid,%') AND `deleted` = 0 AND `status` != 'Archive'");
	if (mysqli_num_rows($sot_list) > 0) { ?>
		<table class="table table-bordered" style="white-space: normal;">
			<tr class="hidden-sm hidden-xs">
				<th width="20%"><?= SALES_ORDER_NOUN ?></th>
				<th width="20%">Customer</th>
				<?php if(strpos($so_value_config, ',Primary Staff,') !== FALSE || strpos($so_value_config, ',Assign Staff,') !== FALSE) { ?>
					<th width="20%">Staff</th>
				<?php } ?>
				<th>Status</th>
				<?php if(strpos($so_value_config, ',Next Action,')) { ?>
				<th>Next Action</th>
				<?php } ?>
				<?php if(strpos($so_value_config, ',Next Action Follow Up Date,')) { ?>
				<th>Follow Up Date</th>
				<?php } ?>
				<th>Function</th>
			</tr>
			<?php while ($row = mysqli_fetch_array($sot_list)) { ?>
				<tr>
					<td data-title="<?= SALES_ORDER_NOUN ?>">
						<?= (!empty($row['name']) ? $row['name'] : SALES_ORDER_NOUN.' Form #'.$row['sotid']) ?>
					</td>
					<td data-title="Customer"><?php
						echo get_client($dbc, $row['customerid']).(!empty($row['classification']) ? ': '.$row['classification'] : '');
						if (!empty($row['business_contact'])) {
							$business_contacts = '<br />Business Contact: ';
							foreach (explode(',', $row['business_contact']) as $business_contact) {
								$business_contact = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '$business_contact'"));
								$business_contacts .= '<a href="'.WEBSITE_URL.'/'.ucfirst($business_contact['tile_name']).'/contacts_inbox.php?category='.$business_contact['category'].'&edit='.$business_contact['contactid'].'">'.get_contact($dbc, $business_contact['contactid']).'</a>, ';
							}
							$business_contacts = rtrim($business_contacts, ', ');
							echo $business_contacts;
						}
						?>
					</td>
					<?php if(strpos($so_value_config, ',Primary Staff,') !== FALSE || strpos($so_value_config, ',Assign Staff,') !== FALSE) { ?>
						<td data-title="Staff"><?php
							if (empty($row['primary_staff']) && empty($row['assign_staff'])) {
								echo '-';
							} else {
								echo (!empty($row['primary_staff']) ? 'Primary Staff: <a href="'.WEBSITE_URL.'/Staff/staff_edit.php?contactid='.$row['primary_staff'].'">'.get_contact($dbc, $row['primary_staff']).'</a>' : '');
								if(!empty($row['assign_staff'])) {
									$staff_list = '<br />Assigned Staff: ';
									foreach (explode(',', $row['assign_staff']) as $assign_staff) {
										$staff_list .= '<a href="'.WEBSITE_URL.'/Staff/staff_edit.php?contactid='.$assign_staff.'">'.get_contact($dbc, $assign_staff).'</a>, ';
									}
									$staff_list = rtrim($staff_list, ', ');
									echo $staff_list;
								}
							} ?>
						</td>
					<?php } ?>
					<td data-title="Status">
						<?= !empty($row['status']) ? $row['status'] : '-' ?>
					</td>
					<?php if(strpos($so_value_config, ',Next Action,')) { ?>
						<td data-title="Next Action">
							<?= !empty($row['next_action']) ? $row['next_action'] : '-' ?>
						</td>
					<?php } ?>
					<?php if(strpos($so_value_config, ',Next Action Follow Up Date,')) { ?>
						<td data-title="Follow Up Date">
							<?= !empty($row['next_action_date']) ? $row['next_action_date'] : '-' ?>
						</td>
					<?php } ?>
					<td data-title="Function">
						<a href="<?= WEBSITE_URL ?>/Sales Order/order.php?p=details&sotid=<?= $row['sotid'] ?>">View</a> |
						<a href="<?= WEBSITE_URL ?>/Sales Order/generate_pdf.php?sotid=<?= $row['sotid'] ?>" target="_blank">PDF</a>
					</td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo '<label class="col-sm-12 control-label">No Records Found.</label>';
	} ?>

	<h5>Submitted <?= SALES_ORDER_TILE ?></h5><?php
	$so_list = mysqli_query($dbc, "SELECT * FROM `sales_order` WHERE (`contactid` = '$contactid' OR `primary_staff` = '$contactid' OR CONCAT(',',`assign_staff`,',') LIKE '%,$contactid,%') AND `deleted` = 0 AND `status` != 'Archive'");
	if (mysqli_num_rows($so_list) > 0) { ?>
		<table class="table table-bordered" style="white-space: normal;">
			<tr class="hidden-sm hidden-xs">
				<th width="20%"><?= SALES_ORDER_NOUN ?></th>
				<th width="20%">Customer</th>
				<?php if(strpos($so_value_config, ',Primary Staff,') !== FALSE || strpos($so_value_config, ',Assign Staff,') !== FALSE) { ?>
					<th width="20%">Staff</th>
				<?php } ?>
				<th>Status</th>
				<?php if(strpos($so_value_config, ',Next Action,')) { ?>
				<th>Next Action</th>
				<?php } ?>
				<?php if(strpos($so_value_config, ',Next Action Follow Up Date,')) { ?>
				<th>Follow Up Date</th>
				<?php } ?>
				<th>Function</th>
			</tr>
			<?php while ($row = mysqli_fetch_array($so_list)) { ?>
				<tr>
					<td data-title="<?= SALES_ORDER_NOUN ?>">
						<?= (!empty($row['name']) ? $row['name'] : SALES_ORDER_NOUN.' #'.$row['posid']) ?>
					</td>
					<td data-title="Customer"><?php
						echo get_client($dbc, $row['contactid']).(!empty($row['classification']) ? ': '.$row['classification'] : '');
						if (!empty($row['business_contact'])) {
							$business_contacts = '<br />Business Contact: ';
							foreach (explode(',', $row['business_contact']) as $business_contact) {
								$business_contact = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '$business_contact'"));
								$business_contacts .= '<a href="'.WEBSITE_URL.'/'.ucfirst($business_contact['tile_name']).'/contacts_inbox.php?category='.$business_contact['category'].'&edit='.$business_contact['contactid'].'">'.get_contact($dbc, $business_contact['contactid']).'</a>, ';
							}
							$business_contacts = rtrim($business_contacts, ', ');
							echo $business_contacts;
						}
						?>
					</td>
					<?php if(strpos($so_value_config, ',Primary Staff,') !== FALSE || strpos($so_value_config, ',Assign Staff,') !== FALSE) { ?>
						<td data-title="Staff"><?php
							if (empty($row['primary_staff']) && empty($row['assign_staff'])) {
								echo '-';
							} else {
								echo (!empty($row['primary_staff']) ? 'Primary Staff: <a href="'.WEBSITE_URL.'/Staff/staff_edit.php?contactid='.$row['primary_staff'].'">'.get_contact($dbc, $row['primary_staff']).'</a>' : '');
								if(!empty($row['assign_staff'])) {
									$staff_list = '<br />Assigned Staff: ';
									foreach (explode(',', $row['assign_staff']) as $assign_staff) {
										$staff_list .= '<a href="'.WEBSITE_URL.'/Staff/staff_edit.php?contactid='.$assign_staff.'">'.get_contact($dbc, $assign_staff).'</a>, ';
									}
									$staff_list = rtrim($staff_list, ', ');
									echo $staff_list;
								}
							} ?>
						</td>
					<?php } ?>
					<td data-title="Status">
						<?= !empty($row['status']) ? $row['status'] : '-' ?>
					</td>
					<?php if(strpos($so_value_config, ',Next Action,')) { ?>
						<td data-title="Next Action">
							<?= !empty($row['next_action']) ? $row['next_action'] : '-' ?>
						</td>
					<?php } ?>
					<?php if(strpos($so_value_config, ',Next Action Follow Up Date,')) { ?>
						<td data-title="Follow Up Date">
							<?= !empty($row['next_action_date']) ? $row['next_action_date'] : '-' ?>
						</td>
					<?php } ?>
					<?php $pdf_file = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_pdf` WHERE `type` = 'so' AND `soid` = '".$row['posid']."' ORDER BY `pdfid` DESC"))['file_name']; ?>
					<td data-title="Function">
						<a href="<?= WEBSITE_URL ?>/Sales Order/index.php?p=preview&id=<?= $row['posid'] ?>">View</a>
						<?php if(file_get_contents(WEBSITE_URL.'/Sales Order/download/'.$pdf_file) && !empty($pdf_file)) { ?>
						 | <a href="<?= WEBSITE_URL ?>/Sales Order/download/<?= $pdf_file ?>" target="_blank">PDF</a>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo '<label class="col-sm-12 control-label">No Records Found.</label>';
	}
} else if ($field_option == "Appointments Addition") {
	$total_appt = 0;
	$appt_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT COUNT(*) num_appts, `follow_up_call_status` FROM `booking` WHERE `deleted` = 0 AND `patientid` = '$contactid' GROUP BY `follow_up_call_status`"),MYSQLI_ASSOC);
	if(count($appt_list) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th>Contact</th>
				<?php foreach($appt_list as $appt_status) { ?>
					<th><?= !empty($appt_status['follow_up_call_status']) ? $appt_status['follow_up_call_status'] : 'No Status' ?></th>
				<?php } ?>
				<th>Total Appointments</th>
			</tr>
			<tr>
				<td data-title="Contact"><?= get_contact($dbc, $contactid) ?></td>
				<?php foreach($appt_list as $appt_status) {
					$total_appt += intval($appt_status['num_appts']); ?>
					<td data-title="<?= !empty($appt_status['follow_up_call_status']) ? $appt_status['follow_up_call_status'] : 'No Status' ?>"><?= intval($appt_status['num_appts']) ?></td>
				<?php } ?>
				<td data-title="Total Appointments"><?= $total_appt ?></td>
			</tr>
		</table>
	<?php } else {
		echo 'No Appointments Found.';
	}
} else if ($field_option == "Upcoming Appointments Addition") {
	$today_date = date('Y-m-d 00:00:00');
	$appt_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `booking` WHERE ('$contactid' IN (`therapistsid`,`patientid`) OR CONCAT('*#*',`therapistsid`,'*#*') LIKE '%*#*$contactid*#*%') AND `follow_up_call_status` NOT LIKE '%cancel%' AND `deleted` = 0"),MYSQLI_ASSOC);
	foreach($appt_list as $appt_i => $row) {
		$upcoming_date = false;
		$appoint_dates = explode('*#*', $row['appoint_date']);
		$end_appoint_dates = explode('*#*', $row['end_appoint_date']);
		$appt_dates = array_merge($appoint_dates, $end_appoint_dates);
		foreach ($appt_dates as $appt_date) {
			if(strtotime($appt_date) >= strtotime($today_date)) {
				$upcoming_date = true;
				break;
			}
		}
		if(!$upcoming_date) {
			unset($appt_list[$appt_i]);
		}
	}
	if(count($appt_list) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th><?= $current_type ?></th>
				<th>Staff</th>
				<th>Start Time</th>
				<th>End Time</th>
				<th>View</th>
			</tr>
			<?php foreach ($appt_list as $row) { ?>
				<tr>
					<td data-title="<?= $current_type ?>"><?= get_contact($dbc, $row['patientid']) ?></td>
					<td data-title="Staff">
						<?php foreach(explode('*#*', $row['therapistsid']) as $therapistsid) {
							echo get_contact($dbc, $therapistsid).'<br />';
						} ?>
					</td>
					<td data-title="Start Time">
						<?php foreach(explode('*#*', $row['appoint_date']) as $appoint_date) {
							echo date('F d, Y h:i a', strtotime($appoint_date)).'<br />';
						} ?>
					</td>
					<td data-title="End Time">
						<?php foreach(explode('*#*', $row['end_appoint_date']) as $end_appoint_date) {
							echo date('F d, Y h:i a', strtotime($end_appoint_date)).'<br />';
						} ?>
					</td>
					<td data-title="View"><a href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Calendar/booking.php?action=view&bookingid=<?= $row['bookingid'] ?>', 'auto', false, true, $('.iframe_overlay').closest('.container').outerHeight() + 20); return false;">View</a></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo 'No Upcoming Appointments.';
	}
} else if ($field_option == "Agenda Meeting Addition") {
	$am_value_config = ','.mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_agendas_meetings"))['field_config'].',';
	$agenda_list = mysqli_query($dbc, "SELECT * FROM `agenda_meeting` WHERE `type` = 'Agenda' AND (CONCAT(',',`businessid`,',') LIKE '%,".$contactid.",%' OR CONCAT(',',`businesscontactid`,',') LIKE '%,".$contactid.",%' OR CONCAT(',',`companycontactid`,',') LIKE '%,".$contactid.",%')"); ?>
	<h4>Agendas</h4>
	<?php if (mysqli_num_rows($agenda_list) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th><?= (strpos($am_value_config, ','."Business".',') !== FALSE ? BUSINESS_CAT : 'Contact') ?></th>
				<th>Date of Meeting</th>
				<th>Time of Meeting</th>
				<th>Location</th>
				<th>Function</th>
			</tr>
			<?php while($row = mysqli_fetch_array($agenda_list)) { ?>
				<tr>
					<td data-title="<?= (strpos($am_value_config, ','."Business".',') !== FALSE ? BUSINESS_CAT : 'Contact') ?>"><?=  (strpos($am_value_config, ','."Business".',') !== FALSE ? get_client($dbc, $row['businessid']) : get_contact($dbc, $row['businesscontactid'])) ?></td>
					<td data-title="Date of Meeting"><?= $row['date_of_meeting'] ?></td>
					<td data-title="Time of Meeting"><?= $row['time_of_meeting'].' - '.$row['end_time_of_meeting'] ?></td>
					<td><?= $row['location'] ?></td>
					<td><a href="<?= WEBSITE_URL ?>/Agenda Meetings/add_agenda.php?agendameetingid=<?= $row['agendameetingid'] ?>">View</a></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo 'No Agendas Found.';
	}

	$am_value_config = ','.mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_agendas_meetings"))['field_config'].',';
	$agenda_list = mysqli_query($dbc, "SELECT * FROM `agenda_meeting` WHERE `type` = 'Meeting' AND (CONCAT(',',`businessid`,',') LIKE '%,".$contactid.",%' OR CONCAT(',',`businesscontactid`,',') LIKE '%,".$contactid.",%' OR CONCAT(',',`companycontactid`,',') LIKE '%,".$contactid.",%')"); ?>
	<h4>Meetings</h4>
	<?php if (mysqli_num_rows($agenda_list) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th><?= (strpos($am_value_config, ','."Business".',') !== FALSE ? BUSINESS_CAT : 'Contact') ?></th>
				<th>Date of Meeting</th>
				<th>Time of Meeting</th>
				<th>Location</th>
				<th>Function</th>
			</tr>
			<?php while($row = mysqli_fetch_array($agenda_list)) { ?>
				<tr>
					<td data-title="<?= (strpos($am_value_config, ','."Business".',') !== FALSE ? BUSINESS_CAT : 'Contact') ?>"><?=  (strpos($am_value_config, ','."Business".',') !== FALSE ? get_client($dbc, $row['businessid']) : get_contact($dbc, $row['businesscontactid'])) ?></td>
					<td data-title="Date of Meeting"><?= $row['date_of_meeting'] ?></td>
					<td data-title="Time of Meeting"><?= $row['time_of_meeting'].' - '.$row['end_time_of_meeting'] ?></td>
					<td><?= $row['location'] ?></td>
					<td><a href="<?= WEBSITE_URL ?>/Agenda Meetings/add_agenda.php?agendameetingid=<?= $row['agendameetingid'] ?>">View</a></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo 'No Meetings Found.';
	}
} else if ($field_option == 'Point of Sale Addition') {
	$pos_value_config = ','.mysqli_fetch_assoc(mysqli_query($dbc,"SELECT pos_dashboard FROM field_config"))['pos_dashboard'].',';
	$pos_list = mysqli_query($dbc, "SELECT * FROM `point_of_sell` WHERE `contactid` = '$contactid' AND `deleted` = 0");
	if(mysqli_num_rows($pos_list) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
            	<?php if (strpos($pos_value_config, ','."Invoice #".',') !== FALSE) {
                    echo '<th>Invoice #</th>';
                }
                if (strpos($pos_value_config, ','."Invoice Date".',') !== FALSE) {
                    echo '<th>Invoice Date</th>';
                }
                if (strpos($pos_value_config, ','."Customer".',') !== FALSE) {
                    echo '<th>Customer</th>';
                }
                if (strpos($pos_value_config, ','."Total Price".',') !== FALSE) {
                    echo '<th>Total Price</th>';
                }
                if (strpos($pos_value_config, ','."Payment Type".',') !== FALSE) {
                    echo '<th>Payment Type</th>';
                }
                if (strpos($pos_value_config, ','."Delivery/Shipping Type".',') !== FALSE) {
                    echo '<th>Delivery/Shipping Type</th>';
                }
                if (strpos($pos_value_config, ','."Invoice PDF".',') !== FALSE) {
                    echo '<th>Invoice PDF</th>';
                }
                if (strpos($pos_value_config, ','."Comment".',') !== FALSE) {
                    echo '<th>Comment</th>';
                }
                if (strpos($pos_value_config, ','."Status".',') !== FALSE) {
                    echo '<th>Status</th>';
                } ?>
			</tr>
			<?php while($row = mysqli_fetch_array($pos_list)) { ?>
				<tr>
	            	<?php if (strpos($pos_value_config, ','."Invoice #".',') !== FALSE) {
	                    echo '<td data-title="Invoice #">'.$row['posid'].'</td>';
	                }
	                if (strpos($pos_value_config, ','."Invoice Date".',') !== FALSE) {
	                    echo '<td data-title="Invoice Date">'.$row['invoice_date'].'</td>';
	                }
	                if (strpos($pos_value_config, ','."Customer".',') !== FALSE) {
	                    echo '<td data-title="Customer">'.get_client($dbc, $row['contactid']).'</td>';
	                }
	                if (strpos($pos_value_config, ','."Total Price".',') !== FALSE) {
	                	echo '<td data-title="Total Price">$'.number_format($row['total_price'],2).'</td>';
	                }
	                if (strpos($pos_value_config, ','."Payment Type".',') !== FALSE) {
						$get_pay_type = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM point_of_sell WHERE posid='".$row['posid']."'"));
	                    echo '<td data-title="Payment Type">'.$get_pay_type['payment_type'].'</td>';
	                }
	                if (strpos($pos_value_config, ','."Delivery/Shipping Type".',') !== FALSE) {
	                    echo '<td data-title="Delivery/Shipping Type">'.$row['delivery_type'].'</td>';
	                }
	                if (strpos($pos_value_config, ','."Invoice PDF".',') !== FALSE) {
	                	echo '<td data-title="Invoice PDF">';
						$version = $row['edit_id'];
						for($pos_i = $version; $pos_i > 0; $pos_i--) {
							echo '<a target="_blank" href="'.WEBSITE_URL.'/Point of Sale/download/invoice_'.$row['posid'].'_'.$pos_i.'.pdf">PDF '.($pos_i == $version ? '' : 'V'.$pos_i).' <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF '.($pos_i == $version ? '' : 'V'.$pos_i).'"></a><br />';
						}
						echo '<a target="_blank" href="'.WEBSITE_URL.'/Point of Sale/download/invoice_'.$row['posid'].'.pdf">'.($version > 0 ? 'Original' : 'PDF').' <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a></td>';
	                }
	                if (strpos($pos_value_config, ','."Comment".',') !== FALSE) {
	                    echo '<td data-title="Comment">'.html_entity_decode($row['comment']).'</td>';
	                }
	                if (strpos($pos_value_config, ','."Status".',') !== FALSE) {
	                    echo '<td data-title="Status">'.$row['status'].'</td>';
	                } ?>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo '<label class="col-sm-12 control-label">No Records Found.</label>';
	}
} else if ($field_option == 'POSAdvanced Addition') {
	$pos_list = mysqli_query($dbc, "SELECT * FROM `invoice` WHERE (`businessid` = '$contactid' OR `patientid` = '$contactid' OR `therapistsid` = '$contactid') AND `deleted` = 0");
	if(mysqli_num_rows($pos_list) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th>Invoice #</th>
				<th>Invoice Date</th>
				<th>Patient</th>
				<th>Service Date</th>
				<th>Service</th>
				<th>Total</th>
				<th>Paid</th>
				<th>Patient Invoice</th>
				<th>Patient Receipt</th>
				<th>Function</th>
			</tr>
			<?php while($row = mysqli_fetch_array($pos_list)) { ?>
				<tr>
					<td data-title="Invoice #"><?= ($row['invoice_type'] == 'New' ? '#'.$row['invoiceid'] : $row['invoice_type'].' #'.$row['invoiceid'].'<br />For Invoice #'.$row['invoiceid_src']) ?></td>
					<td data-title="Invoice Date"><?= $row['invoice_date'] ?></td>
					<?php if($row['patientid'] > 0) { ?>
						<td data-title="Patient"><a href="../Contacts/add_contacts.php?category=Patient&contactid=<?= $row['patientid'] ?>&from_url=<?= urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']) ?>"><?= get_contact($dbc, $row['patientid']) ?></a></td>
					<?php } ?>
					<td data-title="Service Date"><?= $row['service_date'] ?></td>
					<td data-title="Service"><?= get_all_from_service($dbc, $row['serviceid'], 'service_code').' : '.get_all_from_service($dbc, $row['serviceid'], 'service_type') ?></td>
					<?php
						$insurer = '';
		                $invoice_insurer =	mysqli_query($dbc,"SELECT insurer_price, paid FROM invoice_insurer WHERE	invoiceid='".$row['invoiceid']."'");
		                while($row_invoice_insurer = mysqli_fetch_array($invoice_insurer)) {
		                    $insurer .= 'I : '.$row_invoice_insurer['insurer_price'].' : '.$row_invoice_insurer['paid'].'<br>';
		                }

		                $patient = '';
		                $invoice_patient =	mysqli_query($dbc,"SELECT SUM(patient_price) price, paid FROM invoice_patient WHERE invoiceid='".$row['invoiceid']."' GROUP BY `paid`");
		                while($row_patient_insurer = mysqli_fetch_array($invoice_patient)) {
		                    $patient .= 'P : '.$row_patient_insurer['price'].' : '.$row_patient_insurer['paid'].'<br>';
		                }
	                ?>
					<td data-title="Total">$<?= $row['final_price'] ?><br><?= $insurer.$patient ?></td>
					<?php
						$patient_paid = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`patient_price`) total_paid FROM `invoice_patient` WHERE `invoiceid`='".$row['invoiceid']."' AND IFNULL(`paid`,'') NOT IN ('On Account','')"))['total_paid'];
						$insurer_paid = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`insurer_price`) total_paid FROM `invoice_insurer` WHERE `invoiceid`='".$row['invoiceid']."' AND `paid`='Yes'"))['total_paid'];
						$insurer_owing = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`insurer_price`) total_paid FROM `invoice_insurer` WHERE `invoiceid`='".$row['invoiceid']."' AND `paid`!='Yes'"))['total_paid'];
						if($row['final_price'] == $patient_paid + $insurer_paid) {
							$paid = 'Paid in Full';
						} else if ($row['final_price'] == $patient_paid + $insurer_paid + $insurer_owing) {
							$paid = 'Patient Balance Paid in Full<br />Insurer Balance Owing: $'.number_format($insurer_owing, 2);
						} else {
							$paid = 'Patient Balance Owing: $'.number_format($row['final_price'] - $patient_paid - $insurer_paid - $insurer_owing, 2);
							$paid = 'Patient Balance Paid in Full<br />Insurer Balance Owing: $'.number_format($insurer_owing, 2);
						}
					?>
					<td data-title="Paid"><?= $paid ?></td>
					<?php
		                if($row['final_price'] != '' && $row['invoice_type'] != 'Saved') {
		                    $name_of_file = WEBSITE_URL.'/Invoice/Download/invoice_'.$row['invoiceid'].'.pdf';
		                    if(file_exists($name_of_file)) {
		                        //$md5 = md5_file($name_of_file);
		                        //if($md5 == $row['invoice_md5']) {
		                            echo '<td data-title="Patient Invoice"><a href="'.$name_of_file.'" target="_blank"> <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a> | <a href=\''.WEBSITE_URL.'/Invoice/unpaid_invoice.php?action=email&invoiceid='.$row['invoiceid'].'&patientid='.$patientid.'\' >Email</a></td>';
		                        //} else {
		                        //    echo '<td>(Error : File has been Changed)</td>';
		                        //}
		                    } else {
		                        echo '<td data-title="Patient Invoice">-</td>';
		                    }
		                } else {
		                    echo '<td data-title="Patient Invoice">-</td>';
		                }
					?>
					<td data-title="Patient Receipt">
						<?php
			                if($row['patient_payment_receipt'] == 1) {
			                    $name_of_file = WEBSITE_URL.'/Invoice/Download/patientreceipt_'.$row['invoiceid'].'.pdf';
			                    if(file_exists($name_of_file)) {
			                        echo '<a href="'.$name_of_file.'" target="_blank">Receipt  <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a><br />';
			                    }
								$receipts = mysqli_fetch_all(mysqli_query($dbc, "SELECT `receipt_file` FROM `invoice_patient` WHERE `invoiceid`='".$row['invoiceid']."' AND `receipt_file` IS NOT NULL"));
								$receipt_list = [];
								foreach($receipts as $receipt) {
									$receipt_list = array_merge($receipt_list, explode('#*#',$receipt[0]));
								}
								foreach(array_unique(array_filter($receipt_list)) as $receipt) {
									if(file_exists($receipt)) {
										echo '<a href="'.$receipt.'" target="_blank">Account Receipt  <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a><br />';
									}
								}
			                }
						?>
					</td>
						<?php
							if($row['invoice_type'] == 'Saved') {
								echo '<td data-title="Function"><a href=\''.WEBSITE_URL.'/Invoice/create_invoice.php?invoiceid='.$row['invoiceid'].'&contactid='.$row['patientid'].'&search_user='.$contactid.'&search_invoice='.$row['invoiceid'].'\' >Edit</a>';
								$role = $_SESSION['role'];
								if($role== 'super' || $role == ',office_admin,' || $role == ',executive_front_staff,') {
									echo ' | <a onclick="return confirm(\'Are you sure you want to archive this invoice?\')" href=\''.WEBSITE_URL.'/Invoice/today_invoice.php?invoiceid='.$row['invoiceid'].'&action=delete\' >Archive</a>';
								}
							} else {
								echo '<td data-title="Function"><a href=\''.WEBSITE_URL.'/Invoice/adjust_invoice.php?invoiceid='.($row['invoiceid_src'] == 0 ? $row['invoiceid'] : $row['invoiceid_src']).'&contactid='.$row['patientid'].'&search_user='.$contactid.'&search_invoice='.$row['invoiceid'].'\' >Refund / Adjustments</a>';
							}
						?>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo '<label class="col-sm-12 control-label">No Records Found.</label>';
	}
} else if ($field_option == 'Purchase Orders Addition') {
	$po_value_config = ','.mysqli_fetch_assoc(mysqli_query($dbc,"SELECT purchase_order_dashboard FROM field_config"))['purchase_order_dashboard'].',';
	$po_list = mysqli_query($dbc, "SELECT * FROM `purchase_orders` WHERE `contactid` = '$contactid' AND `deleted` = 0");
	if(mysqli_num_rows($po_list) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
            	<?php if (strpos($po_value_config, ','."Invoice #".',') !== FALSE) {
                    echo '<th>Invoice #</th>';
                }
                if (strpos($po_value_config, ','."Invoice Date".',') !== FALSE) {
                    echo '<th>Invoice Date</th>';
                }
                if (strpos($po_value_config, ','."Customer".',') !== FALSE) {
                    echo '<th>Customer</th>';
                }
                if (strpos($po_value_config, ','."Total Price".',') !== FALSE) {
                    echo '<th>Total Price</th>';
                }
                if (strpos($po_value_config, ','."Payment Type".',') !== FALSE) {
                    echo '<th>Payment Type</th>';
                }
                if (strpos($po_value_config, ','."Delivery/Shipping Type".',') !== FALSE) {
                    echo '<th>Delivery/Shipping Type</th>';
                }
                if (strpos($po_value_config, ','."Invoice PDF".',') !== FALSE) {
                    echo '<th>Invoice PDF</th>';
                }
                if (strpos($po_value_config, ','."View Spreadsheet".',') !== FALSE) {
                    echo '<th>View Spreadsheet</th>';
                }
                if (strpos($po_value_config, ','."Comment".',') !== FALSE) {
                    echo '<th>Comment</th>';
                }
                if (strpos($po_value_config, ','."Status".',') !== FALSE) {
                    echo '<th>Status</th>';
                } ?>
			</tr>
			<?php while($row = mysqli_fetch_array($po_list)) { ?>
				<tr>
	            	<?php if (strpos($po_value_config, ','."Invoice #".',') !== FALSE) {
	                    echo '<td data-title="Invoice #">'.$row['posid'].'</td>';
	                }
	                if (strpos($po_value_config, ','."Invoice Date".',') !== FALSE) {
	                    echo '<td data-title="Invoice Date">'.$row['invoice_date'].'</td>';
	                }
	                if (strpos($po_value_config, ','."Customer".',') !== FALSE) {
	                    echo '<td data-title="Customer">'.get_client($dbc, $row['contactid']).'</td>';
	                }
	                if (strpos($po_value_config, ','."Total Price".',') !== FALSE) {
	                	echo '<td data-title="Total Price">$'.number_format($row['total_price'],2).'</td>';
	                }
	                if (strpos($po_value_config, ','."Payment Type".',') !== FALSE) {
						$get_pay_type = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM purchase_orders WHERE posid='".$row['posid']."'"));
	                    echo '<td data-title="Payment Type">'.$get_pay_type['payment_type'].'</td>';
	                }
	                if (strpos($po_value_config, ','."Delivery/Shipping Type".',') !== FALSE) {
	                    echo '<td data-title="Delivery/Shipping Type">'.$row['delivery_type'].'</td>';
	                }
	                if (strpos($po_value_config, ','."Invoice PDF".',') !== FALSE) {
						echo '<td data-title="Invoice PDF"><a target="_blank" href="'.WEBSITE_URL.'/Purchase Order/download/invoice_'.$row['posid'].'.pdf">PDF <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a></td>';
	                }
	                if (strpos($po_value_config, ','."View Spreadsheet".',') !== FALSE) {
						echo '<td data-title="View Spreadsheet">';
						if($row['spreadsheet_name'] !== NULL && $row['spreadsheet_name'] !== '' ) {
							echo '<a target="_blank" href="'.WEBSITE_URL.'/Purchase Order/download/'.$row['spreadsheet_name'].'">Spreadsheet <img style="width:15px;" src="'.WEBSITE_URL.'/img/icons/file.png" title="Spreadsheet"></a></td>';
						} else { echo '-'; }
	                }
	                if (strpos($po_value_config, ','."Comment".',') !== FALSE) {
	                    echo '<td data-title="Comment">'.html_entity_decode($row['comment']).'</td>';
	                }
	                if (strpos($po_value_config, ','."Status".',') !== FALSE) {
	                    echo '<td data-title="Status">'.$row['status'].'</td>';
	                } ?>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo '<label class="col-sm-12 control-label">No Records Found.</label>';
	}
} else if ($field_option == "Sales Addition") {
	$sales_list = mysqli_query($dbc, "SELECT * FROM `sales` WHERE (CONCAT(',',`contactid`,',') LIKE '%,$contactid,%' OR CONCAT(',',`share_lead`,',') LIKE '%,$contactid,%' OR `primary_staff` = '$contactid') AND `deleted` = 0");
	if (mysqli_num_rows($sales_list) > 0) { ?>
		<table class="table table-bordered" style="white-space: normal;">
			<tr class="hidden-sm hidden-xs">
				<th>Sales #</th>
				<th>Customer</th>
				<th>Staff</th>
				<th>Status</th>
				<th>Next Action</th>
				<th>Follow Up Date</th>
				<th>Function</th>
			</tr>
			<?php while ($row = mysqli_fetch_array($sales_list)) { ?>
				<tr>
					<td data-title="Sales #">Sales #<?= $row['salesid'] ?></td>
					<td data-title="Customer"><?php
						echo get_client($dbc, $row['businessid']).(!empty($row['classification']) ? ': '.$row['classification'] : '');
						if (!empty($row['contactid'])) {
							$business_contacts = '<br />Business Contact: ';
							foreach (explode(',', $row['contactid']) as $business_contact) {
								$business_contact = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '$business_contact'"));
								$business_contacts .= '<a href="'.WEBSITE_URL.'/'.ucfirst($business_contact['tile_name']).'/contacts_inbox.php?category='.$business_contact['category'].'&edit='.$business_contact['contactid'].'">'.get_contact($dbc, $business_contact['contactid']).'</a>, ';
							}
							$business_contacts = rtrim($business_contacts, ', ');
							echo $business_contacts;
						}
						?>
					</td>
					<td data-title="Staff"><?php
						if (empty($row['primary_staff']) && empty($row['share_lead'])) {
							echo '-';
						} else {
							echo (!empty($row['primary_staff']) ? 'Primary Staff: <a href="'.WEBSITE_URL.'/Staff/staff_edit.php?contactid='.$row['primary_staff'].'">'.get_contact($dbc, $row['primary_staff']).'</a>' : '');
							if(!empty($row['share_lead'])) {
								$staff_list = '<br />Share Lead: ';
								foreach (explode(',', $row['share_lead']) as $assign_staff) {
									$staff_list .= '<a href="'.WEBSITE_URL.'/Staff/staff_edit.php?contactid='.$assign_staff.'">'.get_contact($dbc, $assign_staff).'</a>, ';
								}
								$staff_list = rtrim($staff_list, ', ');
								echo $staff_list;
							}
						} ?>
					</td>
					<td data-title="Status">
						<?= !empty($row['status']) ? $row['status'] : '-' ?>
					</td>
					<td data-title="Next Action">
						<?= !empty($row['next_action']) ? $row['next_action'] : '-' ?>
					</td>
					<td data-title="Follow Up Date">
						<?= !empty($row['new_reminder']) ? $row['new_reminder'] : '-' ?>
					</td>
					<td data-title="Function">
						<a href="<?= WEBSITE_URL ?>/Sales/sale.php?p=preview&id=<?= $row['salesid'] ?>">View</a>
					</td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo '<label class="col-sm-12 control-label">No Records Found.</label>';
	}
} else if ($field_option == "Expense Addition") {
	$expense_list = mysqli_query($dbc, "SELECT * FROM `expense` WHERE `staff` = '$contactid' AND `deleted` = 0");
	if (mysqli_num_rows($expense_list) > 0) { ?>
		<table class="table table-bordered" style="white-space: normal;">
			<tr class="hidden-sm hidden-xs">
				<th>Expense #</th>
				<th>Contact</th>
				<th>Description</th>
				<th>Date</th>
				<th>Total</th>
				<th>Status</th>
			</tr>
			<?php while ($row = mysqli_fetch_array($expense_list)) { ?>
				<tr>
					<td data-title="Expense #">Expense #<?= $row['expenseid'] ?></td>
					<td data-title="Contact"><?= get_contact($dbc, $row['staff']) ?></td>
					<td data-title="Description"><?= html_entity_decode($row['description']) ?></td>
					<td data-title="Date"><?= $row['ex_date'] ?></td>
					<td data-title="Total">$<?= $row['total'] ?></td>
					<td data-title="Status"><?= $row['status'] ?></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo '<label class="col-sm-12 control-label">No Records Found.</label>';
	}
} else if ($field_option == "Email Communication Addition") {
	$comm_types = ['internal' => 'Internal', 'external' => 'External'];
	foreach ($comm_types as $comm_key => $comm_type) {
		echo '<h4>'.$comm_type.'</h4>';
		$pc_value_config = ','.mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$comm_key."_communication_dashboard FROM field_config"))[$comm_key.'_communication_dashboard'].',';
		$email_list = mysqli_query($dbc, "SELECT * FROM `email_communication` WHERE (`businessid` = '$contactid' OR `contactid` = '$contactid' OR `created_by` = '$contactid') AND `communication_type` = '".$comm_type."' AND `deleted` = 0");
		if (mysqli_num_rows($email_list) > 0) { ?>
			<table class="table table-bordered" style="white-space: normal;">
				<tr class="hidden-sm hidden-xs">
					<?php
				        if (strpos($pc_value_config, ','."Email#".',') !== FALSE) {
				            echo '<th>Email#</th>';
				        }
				        if (strpos($pc_value_config, ','."Business".',') !== FALSE) {
				            echo '<th>Business</th>';
				        }
				        if (strpos($pc_value_config, ','."Contact".',') !== FALSE) {
				            echo '<th>Contact</th>';
				        }
				        if (strpos($pc_value_config, ','."Project".',') !== FALSE) {
				            echo '<th>Project</th>';
				        }
				        if (strpos($pc_value_config, ','."Subject".',') !== FALSE) {
				            echo '<th>Subject</th>';
				        }
				        if (strpos($pc_value_config, ','."Body".',') !== FALSE) {
				            echo '<th>Body</th>';
				        }
				        if (strpos($pc_value_config, ','."Attachment".',') !== FALSE) {
				            echo '<th>Attachment</th>';
				        }
				        if (strpos($pc_value_config, ','."To Staff".',') !== FALSE) {
				            echo '<th>To Staff</th>';
				        }
				        if (strpos($pc_value_config, ','."CC Staff".',') !== FALSE) {
				            echo '<th>CC Staff</th>';
				        }
				        if (strpos($pc_value_config, ','."To Contact".',') !== FALSE) {
				            echo '<th>To Contact</th>';
				        }
				        if (strpos($pc_value_config, ','."CC Contact".',') !== FALSE) {
				            echo '<th>CC Contact</th>';
				        }
						if (strpos($pc_value_config, ','."Additional Email".',') !== FALSE) {
				            echo '<th>Additional Email</th>';
				        }
				        if (strpos($pc_value_config, ','."Email Date".',') !== FALSE) {
				            echo '<th>Email Date</th>';
				        }
				        if (strpos($pc_value_config, ','."Email By".',') !== FALSE) {
				            echo '<th>Staff</th>';
				        }
						if (strpos($pc_value_config, ','."Follow Up By".',') !== FALSE) {
				            echo '<th>Follow Up By</th>';
				        }
						if (strpos($pc_value_config, ','."Follow Up Date".',') !== FALSE) {
				            echo '<th>Follow Up Date</th>';
				        }
				        echo '<th>Status</th><th>Function</th>';
				    ?>
				</tr>
				<?php while ($row = mysqli_fetch_array($email_list)) { ?>
					<tr>
						<?php
					        if (strpos($pc_value_config, ','."Email#".',') !== FALSE) {
					            echo '<td data-title="Email#">' . $row['email_communicationid']. '</td>';
					        }
					        if (strpos($pc_value_config, ','."Business".',') !== FALSE) {
					            echo '<td data-title="Business">' . get_contact($dbc, $row['businessid'], 'name'). '</td>';
					        }
					        if (strpos($pc_value_config, ','."Contact".',') !== FALSE) {
					            echo '<td data-title="Contact">'.get_staff($dbc, $row['contactid']) . '</td>';
					        }
					        if (strpos($pc_value_config, ','."Project".',') !== FALSE) {
								echo '<td data-title="Project">';
								$project_tabs = get_config($dbc, 'project_tabs');
								$project_tabs = explode(',',$project_tabs);
								foreach($project_tabs as $item) {
									if(preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item))) == get_project($dbc, $row['projectid'], 'projecttype')) {
										echo $item.': ';
									}
								}
								echo get_project($dbc, $row['projectid'], 'project_name').'</td>';
					        }
					        if (strpos($pc_value_config, ','."Subject".',') !== FALSE) {
					            echo '<td data-title="Subject">' . $row['subject']. '</td>';
					        }
					        if (strpos($pc_value_config, ','."Body".',') !== FALSE) {
					            echo '<td data-title="Body">' . html_entity_decode($row['email_body']). '</td>';
					        }
					        if (strpos($pc_value_config, ','."Attachment".',') !== FALSE) {
					            echo '<td data-title="Attachment">';
					            $email_communicationid = $row['email_communicationid'];
					            $result1 = mysqli_query($dbc, "SELECT * FROM email_communicationid_upload WHERE email_communicationid='$email_communicationid' ORDER BY emailcommuploadid DESC");
					            while($row2 = mysqli_fetch_array($result1)) {
					                echo '<a href="../Email Communication/download/'.$row2['document'].'" target="_blank">'.$row2['document'].'</a></br>';
					            }
					            echo '</td>';
					        }
					        if (strpos($pc_value_config, ','."To Staff".',') !== FALSE) {
					            echo '<td data-title="To Staff">' . $row['to_staff']. '</td>';
					        }
					        if (strpos($pc_value_config, ','."CC Staff".',') !== FALSE) {
					            echo '<td data-title="CC Staff">' . $row['cc_staff']. '</td>';
					        }
					        if (strpos($pc_value_config, ','."To Contact".',') !== FALSE) {
					            echo '<td data-title="To Contact">' . $row['to_contact']. '</td>';
					        }
					        if (strpos($pc_value_config, ','."CC Contact".',') !== FALSE) {
					            echo '<td data-title="CC Contact">' . $row['cc_contact']. '</td>';
					        }
							if (strpos($pc_value_config, ','."Additional Email".',') !== FALSE) {
					            echo '<td data-title="Additional Email">' . $row['new_emailid']. '</td>';
					        }
					        if (strpos($pc_value_config, ','."Email Date".',') !== FALSE) {
					            echo '<td data-title="Email Date">' . $row['today_date']. '</td>';
					        }
					        if (strpos($pc_value_config, ','."Email By".',') !== FALSE) {
					            echo '<td data-title="Email By">'.get_staff($dbc, $row['created_by']) . '</td>';
					        }
							if (strpos($pc_value_config, ','."Follow Up By".',') !== FALSE) {
								echo '<td data-title="Follow Up By">'.get_contact($dbc, $row['follow_up_by']).'</td>';
					        }
							if (strpos($pc_value_config, ','."Follow Up Date".',') !== FALSE) {
					            echo '<td data-title="Follow Up Date">'.$row['follow_up_date'].'</td>';
					        }
					        echo '<td data-title="Current Status">'.$row['status'].'</td>';
					        echo '<td data-title="Function">';

					        if(vuaed_visible_function($dbc, 'email_communication') == 1) {
					            echo '<a href=\'../Email Communication/add_communication.php?type='.$_GET['type'].'&email_communicationid='.$row['email_communicationid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'\'>Edit | </a>';
					        }

							echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?type='.$_GET['type'].'&action=delete&email_communicationid='.$row['email_communicationid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
					        echo '</td>';
					    ?>
					</tr>
				<?php } ?>
			</table>
		<?php } else {
			echo '<label class="col-sm-12 control-label">No Records Found.</label>';
		}
	}
} else if ($field_option == "Phone Communication Addition") {
	$comm_types = ['internal' => 'Internal', 'external' => 'External'];
	foreach ($comm_types as $comm_key => $comm_type) {
		echo '<h4>'.$comm_type.'</h4>';
		$pc_value_config = ','.mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$comm_key."_communication_dashboard FROM field_config"))[$comm_key.'_communication_dashboard'].',';
		$email_list = mysqli_query($dbc, "SELECT * FROM `phone_communication` WHERE (`businessid` = '$contactid' OR `contactid` = '$contactid' OR `created_by` = '$contactid') AND `communication_type` = '".$comm_type."' AND `deleted` = 0");
		if (mysqli_num_rows($email_list) > 0) { ?>
			<table class="table table-bordered" style="white-space: normal;">
				<tr class="hidden-sm hidden-xs">
					<?php
				        if (strpos($pc_value_config, ','."Phone#".',') !== FALSE) {
				            echo '<th>Phone#</th>';
				        }
				        if (strpos($pc_value_config, ','."Business".',') !== FALSE) {
				            echo '<th>Business</th>';
				        }
				        if (strpos($pc_value_config, ','."Contact".',') !== FALSE) {
				            echo '<th>Contact</th>';
				        }
				        if (strpos($pc_value_config, ','."Project".',') !== FALSE) {
				            echo '<th>Project</th>';
				        }
				        if (strpos($pc_value_config, ','."Subject".',') !== FALSE) {
				            echo '<th>Comment</th>';
				        }
						if (strpos($pc_value_config, ','."Additional Phone".',') !== FALSE) {
				            echo '<th>Additional Phone</th>';
				        }
				        if (strpos($pc_value_config, ','."Phone Date".',') !== FALSE) {
				            echo '<th>Phone Date</th>';
				        }
				        if (strpos($pc_value_config, ','."Phone By".',') !== FALSE) {
				            echo '<th>Staff</th>';
				        }
						if (strpos($pc_value_config, ','."Follow Up By".',') !== FALSE) {
				            echo '<th>Follow Up By</th>';
				        }
						if (strpos($pc_value_config, ','."Follow Up Date".',') !== FALSE) {
				            echo '<th>Follow Up Date</th>';
				        }
				        echo '<th>Status</th><th>Function</th>';
				    ?>
				</tr>
				<?php while ($row = mysqli_fetch_array($email_list)) { ?>
					<tr>
						<?php
					        if (strpos($pc_value_config, ','."Phone#".',') !== FALSE) {
					            echo '<td data-title="Phone#">' . $row['phone_communicationid']. '</td>';
					        }
					        if (strpos($pc_value_config, ','."Business".',') !== FALSE) {
					            echo '<td data-title="Business">' . get_contact($dbc, $row['businessid'], 'name'). '</td>';
					        }
					        if (strpos($pc_value_config, ','."Contact".',') !== FALSE) {
					            echo '<td data-title="Contact">'.get_staff($dbc, $row['contactid']) . '</td>';
					        }
					        if (strpos($pc_value_config, ','."Project".',') !== FALSE) {
								echo '<td data-title="Project">';
								$project_tabs = get_config($dbc, 'project_tabs');
								$project_tabs = explode(',',$project_tabs);
								foreach($project_tabs as $item) {
									if(preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item))) == get_project($dbc, $row['projectid'], 'projecttype')) {
										echo $item.': ';
									}
								}
								echo get_project($dbc, $row['projectid'], 'project_name').'</td>';
					        }
					        if (strpos($pc_value_config, ','."Subject".',') !== FALSE) {
					            echo '<td data-title="Comment">' . $row['comment']. '</td>';
					        }
							if (strpos($pc_value_config, ','."Additional Phone".',') !== FALSE) {
					            echo '<td data-title="Additional Phone">' . $row['new_phoneid']. '</td>';
					        }
					        if (strpos($pc_value_config, ','."Phone Date".',') !== FALSE) {
					            echo '<td data-title="Phone Date">' . $row['doc']. '</td>';
					        }
					        if (strpos($pc_value_config, ','."Phone By".',') !== FALSE) {
					            echo '<td data-title="Phone By">'.get_staff($dbc, $row['created_by']) . '</td>';
					        }
							if (strpos($pc_value_config, ','."Follow Up By".',') !== FALSE) {
								echo '<td data-title="Follow Up By">'.get_contact($dbc, $row['follow_up_by']).'</td>';
					        }
							if (strpos($pc_value_config, ','."Follow Up Date".',') !== FALSE) {
					            echo '<td data-title="Follow Up Date">'.$row['follow_up_date'].'</td>';
					        }
					        echo '<td data-title="Current Status">'.$row['status'].'</td>';
					        echo '<td data-title="Function">';

					        if(vuaed_visible_function($dbc, 'phone_communication') == 1) {
					            echo '<a href=\'../Phone Communication/add_communication.php?type='.$_GET['type'].'&phone_communicationid='.$row['phone_communicationid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'\'>Edit | </a>';
					        }

							echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?type='.$_GET['type'].'&action=delete&phone_communicationid='.$row['phone_communicationid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
					        echo '</td>';
					    ?>
					</tr>
				<?php } ?>
			</table>
		<?php } else {
			echo '<label class="col-sm-12 control-label">No Records Found.</label>';
		}
	}
} else if ($field_option == 'Forms Addition') {
	$forms_list = mysqli_query($dbc, "SELECT * FROM `hr_attendance` WHERE `assign_staffid` = '".$contactid."' AND `done` = 1");
	if(mysqli_num_rows($forms_list) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
                <th>Staff</th>
                <th>Topic</th>
                <th>Heading</th>
                <th>Sub Section Heading</th>
                <th>PDF</th>
			</tr>
			<?php while($row = mysqli_fetch_array($forms_list)) {
                $hr_form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `hr` WHERE `hrid` = '".$row['hrid']."'"));
                $form_name = get_hr($dbc, $row['hrid'], 'form');
                $user_form_id = get_hr($dbc, $row['hrid'], 'user_form_id');
                if($user_form_id > 0) {
                    $user_pdf = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_pdf` WHERE `pdf_id` = '".$row['fieldlevelriskid']."'"));
                    $pdf_path = 'download/'.$user_pdf['generated_file'];
                } else {
                    if($form_name == 'AVS Near Miss') {
                        $pdf_path = 'avs_near_miss/download/hr_'.$row['fieldlevelriskid'].'.pdf';
                    }

                    if($form_name == 'Employee Information Form') {
                        $pdf_path = 'employee_information_form/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Employee Driver Information Form') {
                        $pdf_path = 'employee_driver_information_form/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Time Off Request') {
                        $get_staff = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT fieldlevelriskid FROM hr_time_off_request WHERE hrid='$hrid' AND (contactid='$contactid' OR attendance_staff LIKE '%," . $login_user . ",%') AND DATE(today_date) = CURDATE() AND status='New'"));

                        $pdf_path = 'time_off_request/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Confidential Information') {
                        $pdf_path = 'confidential_information/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Work Hours Policy') {
                        $pdf_path = 'work_hours_policy/download/hr_'.$row['fieldlevelriskid'].'.pdf';
                    }
                    if($form_name == 'Direct Deposit Information') {
                        $pdf_path = 'direct_deposit_information/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Employee Substance Abuse Policy') {
                        $pdf_path = 'employee_substance_abuse_policy/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Employee Right to Refuse Unsafe Work') {
                        $pdf_path = 'employee_right_to_refuse_unsafe_work/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Shop Yard and Office Orientation') {
                        $pdf_path = 'employee_shop_yard_office_orientation/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == "Copy of Drivers Licence and Safety Tickets") {
                        $pdf_path = 'copy_of_drivers_licence_safety_tickets/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'PPE Requirements') {
                        $pdf_path = 'ppe_requirements/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Verbal Training in Emergency Response Plan') {
                        $pdf_path = 'verbal_training_in_emergency_response_plan/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Eligibility for General Holidays and General Holiday Pay') {
                        $pdf_path = 'eligibility_for_general_holidays_general_holiday_pay/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Maternity Leave and Parental Leave') {
                        $pdf_path = 'maternity_leave_parental_leave/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Employment Verification Letter') {
                        $pdf_path = 'employment_verification_letter/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Background Check Authorization') {
                        $pdf_path = 'background_check_authorization/download/hr_'.$row['fieldlevelriskid'].'.pdf';
                    }
                    if($form_name == 'Disclosure of Outside Clients') {
                        $pdf_path = 'disclosure_of_outside_clients/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Employment Agreement') {
                        $pdf_path = 'employment_agreement/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Independent Contractor Agreement') {
                        $pdf_path = 'independent_contractor_agreement/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Letter of Offer') {
                        $pdf_path = 'letter_of_offer/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Employee Non-Disclosure Agreement') {
                        $pdf_path = 'employee_nondisclosure_agreement/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Employee Self Evaluation') {
                        $pdf_path = 'employee_self_evaluation/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'HR Complaint') {
                        $pdf_path = 'hr_complaint/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Exit Interview') {
                        $pdf_path = 'exit_interview/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Employee Expense Reimbursement') {
                        $pdf_path = 'employee_expense_reimbursement/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Absence Report') {
                        $pdf_path = 'absence_report/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Employee Accident Report Form') {
                        $pdf_path = 'employee_accident_report_form/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Trucking Information') {
                        $pdf_path = 'trucking_information/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Contractor Orientation') {
                        $pdf_path = 'contractor_orientation/download/hr_'.$row['fieldlevelriskid'].'.pdf';
                    }
                    if($form_name == 'Contract Welder Inspection Checklist') {
                        $pdf_path = 'avs_near_miss/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Contractor Pay Agreement') {
                        $pdf_path = 'avs_near_miss/download/hr_'.$row['fieldlevelriskid'].'.pdf';

                    }
                    if($form_name == 'Employee Holiday Request Form') {
                        $pdf_path = 'avs_near_miss/download/hr_'.$row['fieldlevelriskid'].'.pdf';
                    }
                    if($form_name == 'Employee Coaching Form') {
                        $pdf_path = 'avs_near_miss/download/hr_'.$row['fieldlevelriskid'].'.pdf';
                    }
                } ?>
				<tr>
                    <td data-title="Staff"><?= get_contact($dbc, $row['assign_staffid']) ?></td>
                    <td data-title="Topic"><?= $hr_form['category'] ?></td>
                    <td data-title="Heading"><?= $hr_form['heading'] ?></td>
                    <td data-title="Sub Section Heading"><?= $hr_form['sub_heading'] ?></td>
                    <td data-title="Function"><a href="../HR/<?= $pdf_path ?>" target="_blank">View</a></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo '<label class="col-sm-12 control-label">No Records Found.</label>';
	}
} else if ($field_option == 'Manuals Addition') {
	$manuals_list = mysqli_query($dbc, "SELECT * FROM `manuals_staff` WHERE `staffid` = '".$contactid."' AND `done` = 1");
	if(mysqli_num_rows($manuals_list) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
                <th>Staff</th>
                <th>Topic</th>
                <th>Heading</th>
                <th>Sub Section Heading</th>
                <th>PDF</th>
			</tr>
			<?php while($row = mysqli_fetch_array($manuals_list)) {
                $manual = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `manuals` WHERE `manualtypeid` = '".$row['manualtypeid']."'"));
                $pdf_path = file_exists('../HR/download/manual_'.$row['manualtypeid'].'_signed_'.$row['manualstaffid'].'_'.$row['today_date'].'.pdf') ? '../HR/download/manual_'.$row['manualtypeid'].'_signed_'.$row['manualstaffid'].'_'.$row['$today_date'].'.pdf' : '../Manuals/download/manual_'.$row['manualtypeid'].'_signed_'.$row['manualstaffid'].'_'.$row['today_date'].'.pdf'; ?>
				<tr>
                    <td data-title="Staff"><?= get_contact($dbc, $row['assign_staffid']) ?></td>
                    <td data-title="Topic"><?= $manual['category'] ?></td>
                    <td data-title="Heading"><?= $manual['heading'] ?></td>
                    <td data-title="Sub Section Heading"><?= $manual['sub_heading'] ?></td>
                    <td data-title="Function"><a href="<?= $pdf_path ?>" target="_blank">View</a></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo '<label class="col-sm-12 control-label">No Records Found.</label>';
	}
} else if ($field_option == 'Contracts Addition') {
	$contract_list = mysqli_query($dbc, "SELECT * FROM `contracts_completed` WHERE (`businessid` = '".$contactid."' OR `contactid` = '$contactid' OR `staffid` = '$contactid') AND `deleted` = 0");
	if(mysqli_num_rows($contract_list) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th>Customer</th>
				<th>Contact</th>
                <th>Staff</th>
                <th>Topic</th>
                <th>Heading</th>
                <th>Sub Section Heading</th>
                <th>PDF</th>
			</tr>
			<?php while($row = mysqli_fetch_array($contract_list)) {
                $contract = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contracts` WHERE `contractid` = '".$row['contractid']."'")); ?>
				<tr>
					<td data-title="Customer"><?= get_client($dbc, $row['businessid']) ?></td>
					<td data-title="Contact"><?= get_contact($dbc, $row['contactid']) ?></td>
                    <td data-title="Staff"><?= get_contact($dbc, $row['staffid']) ?></td>
                    <td data-title="Topic"><?= $contract['category'] ?></td>
                    <td data-title="Heading"><?= $contract['heading'] ?></td>
                    <td data-title="Sub Section Heading"><?= $contract['sub_heading'] ?></td>
                    <td data-title="Function"><a href="<?= WEBSITE_URL ?>/Contracts/download/<?= $row['contract_file'] ?>" target="_blank">View</a></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo '<label class="col-sm-12 control-label">No Records Found.</label>';
	}
} else if ($field_option == 'Client Documents Addition') {
	$cd_value_config = ','.mysqli_fetch_assoc(mysqli_query($dbc,"SELECT client_documents_dashboard FROM field_config"))['client_documents_dashboard'].',';
	$doc_list = mysqli_query($dbc, "SELECT * FROM `client_documents` WHERE `contactid` = '$contactid' AND `deleted` = 0");
	if(mysqli_num_rows($doc_list) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
            	<?php
					if (strpos($cd_value_config, ','."Client".',') !== FALSE) {
						echo '<th>Client</th>';
					}
					if (strpos($cd_value_config, ','."Client Documents Code".',') !== FALSE) {
						echo '<th>Client Documents Code</th>';
					}
					if (strpos($cd_value_config, ','."Client Documents Type".',') !== FALSE) {
						echo '<th>Client Document Type</th>';
					}
					if (strpos($cd_value_config, ','."Category".',') !== FALSE) {
						echo '<th>Category</th>';
					}
					if (strpos($cd_value_config, ','."Title".',') !== FALSE) {
						echo '<th>Title</th>';
					}
					if (strpos($cd_value_config, ','."Uploader".',') !== FALSE) {
						echo '<th>Documents</th>';
					}
					if (strpos($cd_value_config, ','."Link".',') !== FALSE) {
						echo '<th>Link</th>';
					}
					if (strpos($cd_value_config, ','."Heading".',') !== FALSE) {
						echo '<th>Heading</th>';
					}
					if (strpos($cd_value_config, ','."Name".',') !== FALSE) {
						echo '<th>Name</th>';
					}
					if (strpos($cd_value_config, ','."Fee".',') !== FALSE) {
						echo '<th>Fee</th>';
					}
					if (strpos($cd_value_config, ','."Cost".',') !== FALSE) {
						echo '<th>Cost</th>';
					}
					if (strpos($cd_value_config, ','."Description".',') !== FALSE) {
						echo '<th>Description</th>';
					}
					if (strpos($cd_value_config, ','."Quote Description".',') !== FALSE) {
						echo '<th>Quote Description</th>';
					}
					if (strpos($cd_value_config, ','."Invoice Description".',') !== FALSE) {
						echo '<th>Invoice Description</th>';
					}
					if (strpos($cd_value_config, ','."Ticket Description".',') !== FALSE) {
						echo '<th>'.TICKET_NOUN.' Description</th>';
					}
					if (strpos($cd_value_config, ','."Final Retail Price".',') !== FALSE) {
						echo '<th>Final Retail Price</th>';
					}
					if (strpos($cd_value_config, ','."Admin Price".',') !== FALSE) {
						echo '<th>Admin Price</th>';
					}
					if (strpos($cd_value_config, ','."Wholesale Price".',') !== FALSE) {
						echo '<th>Wholesale Price</th>';
					}
					if (strpos($cd_value_config, ','."Commercial Price".',') !== FALSE) {
						echo '<th>Commercial Price</th>';
					}
					if (strpos($cd_value_config, ','."Client Price".',') !== FALSE) {
						echo '<th>Client Price</th>';
					}
					if (strpos($cd_value_config, ','."Minimum Billable".',') !== FALSE) {
						echo '<th>Minimum Billable</th>';
					}
					if (strpos($cd_value_config, ','."Estimated Hours".',') !== FALSE) {
						echo '<th>Estimated Hours</th>';
					}
					if (strpos($cd_value_config, ','."Actual Hours".',') !== FALSE) {
						echo '<th>Actual Hours</th>';
					}
					if (strpos($cd_value_config, ','."MSRP".',') !== FALSE) {
						echo '<th>MSRP</th>';
					}
					echo '<th>Function</th>';
				?>
			</tr>
			<?php while($row = mysqli_fetch_array($doc_list)) { ?>
				<tr>
	            	<?php
						$client_documentsid = $row['client_documentsid'];
						if (strpos($cd_value_config, ','."Client".',') !== FALSE) {
							echo '<td data-title="Client">' . get_client($dbc, $row['contactid']) . '</td>';
						}
						if (strpos($cd_value_config, ','."Client Documents Code".',') !== FALSE) {
							echo '<td data-title="Doc. Code">' . $row['client_documents_code'] . '</td>';
						}
						if (strpos($cd_value_config, ','."Client Documents Type".',') !== FALSE) {
							echo '<td data-title="Doc. Type">' . $row['client_documents_type'] . '</td>';
						}
						if (strpos($cd_value_config, ','."Category".',') !== FALSE) {
							echo '<td data-title="Category">' . $row['category'] . '</td>';
						}

						if (strpos($cd_value_config, ','."Title".',') !== FALSE) {
							echo '<td data-title="Title">' . $row['title'] . '</td>';
						}
						if (strpos($cd_value_config, ','."Uploader".',') !== FALSE) {
							echo '<td data-title="Upload">';
							$client_documents_uploads1 = "SELECT * FROM client_documents_uploads WHERE client_documentsid='$client_documentsid' AND type = 'Document' ORDER BY certuploadid DESC";
							$result1 = mysqli_query($dbc, $client_documents_uploads1);
							$num_rows1 = mysqli_num_rows($result1);
							if($num_rows1 > 0) {
								while($row1 = mysqli_fetch_array($result1)) {
									echo '<ul>';
									echo '<li><a href="'.WEBSITE_URL.'/Client Documents/download/'.$row1['document_link'].'" target="_blank">'.$row1['document_link'].'</a></li>';
									echo '</ul>';
								}
							}
							echo '</td>';
						}
						if (strpos($cd_value_config, ','."Link".',') !== FALSE) {
							echo '<td data-title="Link">';
							$client_documents_uploads2 = "SELECT * FROM client_documents_uploads WHERE client_documentsid='$client_documentsid' AND type = 'Link' ORDER BY certuploadid DESC";
							$result2 = mysqli_query($dbc, $client_documents_uploads2);
							$num_rows2 = mysqli_num_rows($result2);
							if($num_rows2 > 0) {
								$link_no = 1;
								while($row2 = mysqli_fetch_array($result2)) {
									echo '<ul>';
									echo '<li><a target="_blank" href=\''.$row2['document_link'].'\'">Link '.$link_no.'</a></li>';
									echo '</ul>';
									$link_no++;
								}
							}
							echo '</td>';
						}

						if (strpos($cd_value_config, ','."Heading".',') !== FALSE) {
							echo '<td data-title="Heading">' . $row['heading'] . '</td>';
						}
						if (strpos($cd_value_config, ','."Name".',') !== FALSE) {
							echo '<td data-title="Name">' . $row['name'] . '</td>';
						}
						if (strpos($cd_value_config, ','."Fee".',') !== FALSE) {
							echo '<td data-title="Fee">' . $row['fee'] . '</td>';
						}
						if (strpos($cd_value_config, ','."Cost".',') !== FALSE) {
							echo '<td data-title="Cost">' . $row['cost'] . '</td>';
						}
						if (strpos($cd_value_config, ','."Description".',') !== FALSE) {
							echo '<td data-title="Description">' . html_entity_decode($row['description']) . '</td>';
						}
						if (strpos($cd_value_config, ','."Quote Description".',') !== FALSE) {
							echo '<td data-title="Quote Desc.">' . html_entity_decode($row['quote_description']) . '</td>';
						}
						if (strpos($cd_value_config, ','."Invoice Description".',') !== FALSE) {
							echo '<td data-title="Invoice Desc.">' . html_entity_decode($row['invoice_description']) . '</td>';
						}
						if (strpos($cd_value_config, ','."Ticket Description".',') !== FALSE) {
							echo '<td data-title="'.TICKET_NOUN.' Desc">' . html_entity_decode($row['ticket_description']) . '</td>';
						}
						if (strpos($cd_value_config, ','."Final Retail Price".',') !== FALSE) {
							echo '<td data-title="Retail">' . $row['final_retail_price'] . '</td>';
						}
						if (strpos($cd_value_config, ','."Admin Price".',') !== FALSE) {
							echo '<td data-title="Admin Price">' . $row['admin_price'] . '</td>';
						}
						if (strpos($cd_value_config, ','."Wholesale Price".',') !== FALSE) {
							echo '<td data-title="Wholesale">' . $row['wholesale_price'] . '</td>';
						}
						if (strpos($cd_value_config, ','."Commercial Price".',') !== FALSE) {
							echo '<td data-title="Comm. Price">' . $row['commercial_price'] . '</td>';
						}
						if (strpos($cd_value_config, ','."Client Price".',') !== FALSE) {
							echo '<td data-title="Client Price">' . $row['client_price'] . '</td>';
						}
						if (strpos($cd_value_config, ','."Minimum Billable".',') !== FALSE) {
							echo '<td data-title="Min. Billable">' . $row['minimum_billable'] . '</td>';
						}
						if (strpos($cd_value_config, ','."Estimated Hours".',') !== FALSE) {
							echo '<td data-title="Est. Hours">' . $row['estimated_hours'] . '</td>';
						}
						if (strpos($cd_value_config, ','."Actual Hours".',') !== FALSE) {
							echo '<td data-title="Actual Hours">' . $row['actual_hours'] . '</td>';
						}
						if (strpos($cd_value_config, ','."MSRP".',') !== FALSE) {
							echo '<td data-title="MSRP">' . $row['msrp'] . '</td>';
						}

						echo '<td data-title="Function">';
						if(vuaed_visible_function($dbc, 'client_documents') == 1) {
						echo '<a href=\''.WEBSITE_URL.'/Client Documents/add_client_documents.php?client_documentsid='.$client_documentsid.'\'>Edit</a> | ';
						echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&client_documentsid='.$client_documentsid.'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
						}
						echo '</td>';
					?>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo '<label class="col-sm-12 control-label">No Records Found.</label>';
	}
} else if ($field_option == 'Staff Documents Addition') {
	$sd_value_config = ','.mysqli_fetch_assoc(mysqli_query($dbc,"SELECT staff_documents_dashboard FROM field_config"))['staff_documents_dashboard'].',';
	$doc_list = mysqli_query($dbc, "SELECT * FROM `staff_documents` WHERE `contactid` = '$contactid' AND `deleted` = 0");
	if(mysqli_num_rows($doc_list) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
            	<?php
					if (strpos($sd_value_config, ','."Staff".',') !== FALSE) {
						echo '<th>Staff</th>';
					}
					if (strpos($sd_value_config, ','."Staff Documents Code".',') !== FALSE) {
						echo '<th>Staff Documents Code</th>';
					}
					if (strpos($sd_value_config, ','."Staff Documents Type".',') !== FALSE) {
						echo '<th>Staff Document Type</th>';
					}
					if (strpos($sd_value_config, ','."Category".',') !== FALSE) {
						echo '<th>Category</th>';
					}
					if (strpos($sd_value_config, ','."Title".',') !== FALSE) {
						echo '<th>Title</th>';
					}
					if (strpos($sd_value_config, ','."Uploader".',') !== FALSE) {
						echo '<th>Documents</th>';
					}
					if (strpos($sd_value_config, ','."Link".',') !== FALSE) {
						echo '<th>Link</th>';
					}
					if (strpos($sd_value_config, ','."Heading".',') !== FALSE) {
						echo '<th>Heading</th>';
					}
					if (strpos($sd_value_config, ','."Name".',') !== FALSE) {
						echo '<th>Name</th>';
					}
					if (strpos($sd_value_config, ','."Fee".',') !== FALSE) {
						echo '<th>Fee</th>';
					}
					if (strpos($sd_value_config, ','."Cost".',') !== FALSE) {
						echo '<th>Cost</th>';
					}
					if (strpos($sd_value_config, ','."Description".',') !== FALSE) {
						echo '<th>Description</th>';
					}
					if (strpos($sd_value_config, ','."Quote Description".',') !== FALSE) {
						echo '<th>Quote Description</th>';
					}
					if (strpos($sd_value_config, ','."Invoice Description".',') !== FALSE) {
						echo '<th>Invoice Description</th>';
					}
					if (strpos($sd_value_config, ','."Ticket Description".',') !== FALSE) {
						echo '<th>'.TICKET_NOUN.' Description</th>';
					}
					if (strpos($sd_value_config, ','."Final Retail Price".',') !== FALSE) {
						echo '<th>Final Retail Price</th>';
					}
					if (strpos($sd_value_config, ','."Admin Price".',') !== FALSE) {
						echo '<th>Admin Price</th>';
					}
					if (strpos($sd_value_config, ','."Wholesale Price".',') !== FALSE) {
						echo '<th>Wholesale Price</th>';
					}
					if (strpos($sd_value_config, ','."Commercial Price".',') !== FALSE) {
						echo '<th>Commercial Price</th>';
					}
					if (strpos($sd_value_config, ','."Staff Price".',') !== FALSE) {
						echo '<th>Staff Price</th>';
					}
					if (strpos($sd_value_config, ','."Minimum Billable".',') !== FALSE) {
						echo '<th>Minimum Billable</th>';
					}
					if (strpos($sd_value_config, ','."Estimated Hours".',') !== FALSE) {
						echo '<th>Estimated Hours</th>';
					}
					if (strpos($sd_value_config, ','."Actual Hours".',') !== FALSE) {
						echo '<th>Actual Hours</th>';
					}
					if (strpos($sd_value_config, ','."MSRP".',') !== FALSE) {
						echo '<th>MSRP</th>';
					}
					echo '<th>Function</th>';
				?>
			</tr>
			<?php while($row = mysqli_fetch_array($doc_list)) { ?>
				<tr>
	            	<?php
						$staff_documentsid = $row['staff_documentsid'];
						if (strpos($sd_value_config, ','."Staff".',') !== FALSE) {
							echo '<td data-title="Staff"><a href="'.WEBSITE_URL.'/Staff/staff_edit.php?contactid=' . $row['contactid'] . '&from=' . urlencode(WEBSITE_URL . $_SERVER['REQUEST_URI']) . '">' . get_contact($dbc, $row['contactid']) . '</a></td>';
						}
						if (strpos($sd_value_config, ','."Staff Documents Code".',') !== FALSE) {
							echo '<td data-title="Doc. Code">' . $row['staff_documents_code'] . '</td>';
						}
						if (strpos($sd_value_config, ','."Staff Documents Type".',') !== FALSE) {
							echo '<td data-title="Doc. Type">' . $row['staff_documents_type'] . '</td>';
						}
						if (strpos($sd_value_config, ','."Category".',') !== FALSE) {
							echo '<td data-title="Category">' . $row['category'] . '</td>';
						}

						if (strpos($sd_value_config, ','."Title".',') !== FALSE) {
							echo '<td data-title="Title">' . $row['title'] . '</td>';
						}
						if (strpos($sd_value_config, ','."Uploader".',') !== FALSE) {
							echo '<td data-title="Upload">';
							$staff_documents_uploads1 = "SELECT * FROM staff_documents_uploads WHERE staff_documentsid='$staff_documentsid' AND type = 'Document' ORDER BY certuploadid DESC";
							$result1 = mysqli_query($dbc, $staff_documents_uploads1);
							$num_rows1 = mysqli_num_rows($result1);
							if($num_rows1 > 0) {
								while($row1 = mysqli_fetch_array($result1)) {
									echo '<ul>';
									echo '<li><a href="'.WEBSITE_URL.'/Staff Documents/download/'.$row1['document_link'].'" target="_blank">'.$row1['document_link'].'</a></li>';
									echo '</ul>';
								}
							}
							echo '</td>';
						}
						if (strpos($sd_value_config, ','."Link".',') !== FALSE) {
							echo '<td data-title="Link">';
							$staff_documents_uploads2 = "SELECT * FROM staff_documents_uploads WHERE staff_documentsid='$staff_documentsid' AND type = 'Link' ORDER BY certuploadid DESC";
							$result2 = mysqli_query($dbc, $staff_documents_uploads2);
							$num_rows2 = mysqli_num_rows($result2);
							if($num_rows2 > 0) {
								$link_no = 1;
								while($row2 = mysqli_fetch_array($result2)) {
									echo '<ul>';
									echo '<li><a target="_blank" href=\''.$row2['document_link'].'\'">Link '.$link_no.'</a></li>';
									echo '</ul>';
									$link_no++;
								}
							}
							echo '</td>';
						}

						if (strpos($sd_value_config, ','."Heading".',') !== FALSE) {
							echo '<td data-title="Heading">' . $row['heading'] . '</td>';
						}
						if (strpos($sd_value_config, ','."Name".',') !== FALSE) {
							echo '<td data-title="Name">' . $row['name'] . '</td>';
						}
						if (strpos($sd_value_config, ','."Fee".',') !== FALSE) {
							echo '<td data-title="Fee">' . $row['fee'] . '</td>';
						}
						if (strpos($sd_value_config, ','."Cost".',') !== FALSE) {
							echo '<td data-title="Cost">' . $row['cost'] . '</td>';
						}
						if (strpos($sd_value_config, ','."Description".',') !== FALSE) {
							echo '<td data-title="Description">' . html_entity_decode($row['description']) . '</td>';
						}
						if (strpos($sd_value_config, ','."Quote Description".',') !== FALSE) {
							echo '<td data-title="Quote Desc.">' . html_entity_decode($row['quote_description']) . '</td>';
						}
						if (strpos($sd_value_config, ','."Invoice Description".',') !== FALSE) {
							echo '<td data-title="Invoice Desc.">' . html_entity_decode($row['invoice_description']) . '</td>';
						}
						if (strpos($sd_value_config, ','."Ticket Description".',') !== FALSE) {
							echo '<td data-title="'.TICKET_NOUN.' Desc">' . html_entity_decode($row['ticket_description']) . '</td>';
						}
						if (strpos($sd_value_config, ','."Final Retail Price".',') !== FALSE) {
							echo '<td data-title="Retail">' . $row['final_retail_price'] . '</td>';
						}
						if (strpos($sd_value_config, ','."Admin Price".',') !== FALSE) {
							echo '<td data-title="Admin Price">' . $row['admin_price'] . '</td>';
						}
						if (strpos($sd_value_config, ','."Wholesale Price".',') !== FALSE) {
							echo '<td data-title="Wholesale">' . $row['wholesale_price'] . '</td>';
						}
						if (strpos($sd_value_config, ','."Commercial Price".',') !== FALSE) {
							echo '<td data-title="Comm. Price">' . $row['commercial_price'] . '</td>';
						}
						if (strpos($sd_value_config, ','."Staff Price".',') !== FALSE) {
							echo '<td data-title="Staff Price">' . $row['staff_price'] . '</td>';
						}
						if (strpos($sd_value_config, ','."Minimum Billable".',') !== FALSE) {
							echo '<td data-title="Min. Billable">' . $row['minimum_billable'] . '</td>';
						}
						if (strpos($sd_value_config, ','."Estimated Hours".',') !== FALSE) {
							echo '<td data-title="Est. Hours">' . $row['estimated_hours'] . '</td>';
						}
						if (strpos($sd_value_config, ','."Actual Hours".',') !== FALSE) {
							echo '<td data-title="Actual Hours">' . $row['actual_hours'] . '</td>';
						}
						if (strpos($sd_value_config, ','."MSRP".',') !== FALSE) {
							echo '<td data-title="MSRP">' . $row['msrp'] . '</td>';
						}

						echo '<td data-title="Function">';
						if(vuaed_visible_function($dbc, 'staff_documents') == 1) {
						echo '<a href=\''.WEBSITE_URL.'/Staff Documents/add_staff_documents.php?staff_documentsid='.$staff_documentsid.'\'>Edit</a> | ';
						echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&staff_documentsid='.$staff_documentsid.'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
						}
						echo '</td>';
					?>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo '<label class="col-sm-12 control-label">No Records Found.</label>';
	}
} else if ($field_option == "Certificates Addition") {
	$certificates_list = mysqli_query($dbc, "SELECT * FROM `certificate` WHERE `contactid` = '$contactid' AND `deleted` = 0");
	if(mysqli_num_rows($certificates_list) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th>Contact</th>
				<th>Certificate Type</th>
				<th>Title</th>
				<th>Issue Date</th>
				<th>Expiry Date</th>
				<th>Reminder Date</th>
				<th>Function</th>
			</tr>
			<?php while($row = mysqli_fetch_array($certificates_list)) { ?>
				<tr>
					<td data-title="Contact"><?= get_contact($dbc, $contactid) ?></td>
					<td data-title="Certificate Type"><?= $row['certificate_type'] ?></td>
					<td data-title="Title"><?= $row['title'] ?></td>
					<td data-title="Issue Date"><?= $row['issue_date'] ?></td>
					<td data-title="Expiry Date"><?= $row['expiry_date'] ?></td>
					<td data-title="Reminder Date"><?= $row['reminder_date'] ?></td>
					<td data-title="Function"><a href="<?= WEBSITE_URL ?>/Certificate/add_certificate.php?certificateid=<?= $row['certificateid'] ?>">View</a></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo 'No Certificates Found.';
	}
} else if ($field_option == "Service Queue Addition") {
	$service_queue_list = mysqli_query($dbc, "SELECT sq.`posid` as posid, sq.`inv_date` as inv_date, sq.`status` as status FROM `service_queue` sq left join `point_of_sell` pos on sq.`posid` = pos.`posid` where pos.`contactid` = '$contactid'");
	if(mysqli_num_rows($service_queue_list) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th>Contact</th>
				<th>Invoice #</th>
				<th>Invoice Date</th>
				<th>Status</th>
				<th>Function</th>
			</tr>
			<?php while($row = mysqli_fetch_array($service_queue_list)) { ?>
				<tr>
					<td data-title="Contact"><?= get_contact($dbc, $contactid) ?></td>
					<td data-title="Invoice #"><?= $row['posid'] ?></td>
					<td data-title="Invoice Date"><?= $row['inv_date'] ?></td>
					<td data-title="Status"><?= html_entity_decode($row['status']) ?></td>
					<td data-title="Function"><a href="<?= WEBSITE_URL ?>/Service Queue/service_queue.php?posid=<?= $row['posid'] ?>" class="btn brand-btn">Serviced</a></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo '<label class="col-sm-12 control-label">No Records Found.</label>';
	}
} else if ($field_option == "Injury Addition") {
	$injury_list = mysqli_query($dbc, "SELECT * FROM `patient_injury` WHERE (`contactid` = '$contactid' OR `injury_therapistsid` = '$contactid') AND `deleted` = 0 ORDER BY `discharge_date` IS NULL DESC");
	if(mysqli_num_rows($injury_list) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th>Patient</th>
				<th>Therapist</th>
				<th>Type : Name</th>
				<th>Injury Date</th>
				<th>Treatment Plan</th>
				<th>Status</th>
				<th>Function</th>
			</tr>
			<?php while($row = mysqli_fetch_array($injury_list)) { ?>
				<tr>
					<td data-title="Patient"><?= get_contact($dbc, $row['contactid']) ?></td>
					<td data-title="Therapist"><?= get_contact($dbc, $row['injury_therapistsid']) ?></td>
					<td data-title="Type : Name"><?= $row['injury_type'].' : '.$row['injury_name'] ?></td>
					<td data-title="Injury Date"><?= $row['injury_date'] ?></td>
					<td data-title="Treatment Plan">
					<?php
		                $future_appt = 0;
		                $appoint_date = mysqli_query($dbc,"SELECT appoint_date, follow_up_call_status FROM booking WHERE injuryid='".$row['injuryid']."' ORDER BY appoint_date DESC");
		                echo (!empty($row['treatment_plan']) ? $row['treatment_plan'] : '-').'<br>';
		                while($appoint_date1 = mysqli_fetch_array($appoint_date)) {
		                    echo substr($appoint_date1['appoint_date'],0,10).' : '.$appoint_date1['follow_up_call_status'].'<br>';

		                    if (new DateTime() < new DateTime($appoint_date1['appoint_date'])) {
		                        $future_appt = 1;
		                    }
		                }
		            ?>
					</td>
					<td data-title="Status"><?= !empty($row['discharge_date']) ? 'Discharged' : 'Active' ?></td>
					<td data-title="Function"><a href="<?= WEBSITE_URL ?>/Injury/add_injury.php?injuryid=<?= $row['injuryid'] ?>">Edit</a> | <a href="<?= WEBSITE_URL ?>/Injury/discharge_comment.php?injuryid=<?= $row['injuryid'] ?>"><?= (!empty($row['discharge_date']) ? 'View Discharge Note' : 'Discharge') ?></a></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo 'No Injuries Found.';
	}
} else if ($field_option == "Exercise Library Addition") {
	$exercise_list = mysqli_query($dbc, "SELECT * FROM `exercise_config` WHERE `type` = '$contactid' AND `deleted` = 0");
	if(mysqli_num_rows($exercise_list) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th>Contact</th>
				<th>Category</th>
				<th>Title</th>
				<th>Link(s)</th>
				<th>Function</th>
			</tr>
			<?php while($row = mysqli_fetch_array($exercise_list)) { ?>
				<tr>
					<td data-title="Contact"><?= get_contact($dbc, $contactid) ?></td>
					<td data-title="Category"><?= $row['category'] ?></td>
					<td data-title="Title"><?= $row['title'] ?></td>
					<td data-title="Link(s)">
					<?php
	                    $get_doc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(exlibraryuploadid) AS total_id FROM exercise_library_upload WHERE type='document' AND exerciseid='".$row['exerciseid']."'"));

	                    if($get_doc['total_id'] > 0) {
	                        $result1 = mysqli_query($dbc, "SELECT upload, exlibraryuploadid FROM exercise_library_upload WHERE type='document' AND exerciseid='".$row['exerciseid']."'");

	                        echo '<ul>';
	                        while($row1 = mysqli_fetch_array($result1)) {
	                            $document = $row1['upload'];
	                            if($document != '') {
	                                echo '<li><a href="Download/'.$document.'" target="_blank">'.$document.'</a></li>';
	                            }
	                        }
	                        echo '</ul>';
	                    }

	                    $get_doc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(exlibraryuploadid) AS total_id FROM exercise_library_upload WHERE type='link' AND exerciseid='".$row['exerciseid']."'"));

	                    if($get_doc['total_id'] > 0) {
	                        $result2 = mysqli_query($dbc, "SELECT upload, exlibraryuploadid FROM exercise_library_upload WHERE type='link' AND exerciseid='".$row['exerciseid']."'");

	                        echo '<ul>';
	                        while($row2 = mysqli_fetch_array($result2)) {
	                            $link = $row2['upload'];
	                            if($link != '') {
	                                echo '<li><a href="'.$link.'" target="_blank">'.$link.'</a></li>';
	                            }
	                        }
	                        echo '</ul>';
	                    }

	                    $get_doc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(exlibraryuploadid) AS total_id FROM exercise_library_upload WHERE type='video' AND exerciseid='".$row['exerciseid']."'"));

	                    if($get_doc['total_id'] > 0) {
	                        $result3 = mysqli_query($dbc, "SELECT upload, exlibraryuploadid FROM exercise_library_upload WHERE type='video' AND exerciseid='".$row['exerciseid']."'");

	                        echo '<ul>';
	                        while($row3 = mysqli_fetch_array($result3)) {
	                            $video = $row3['upload'];
	                            if($video != '') {
	                                echo '<li><a href="Download/'.$video.'" target="_blank">'.$video.'</a></li>';
	                            }
	                        }
	                        echo '</ul>';
	                    }
                    ?>
					</td>
					<td data-title="Function"><a href="<?= WEBSITE_URL ?>/Exercise Plan/add_exercise_config.php?exerciseid=<?= $row['exerciseid'] ?>">Edit</a></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo 'No Exercise Plan Found.';
	}
} else if ($field_option == "Treatment Charts Addition") {
	$treatment_charts_list = mysqli_query($dbc, "SELECT * FROM `patientform_pdf` WHERE `patientid` = '$contactid' ORDER BY `today_date` DESC");
	if(mysqli_num_rows($treatment_charts_list) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th>Contact</th>
				<th>Topic</th>
				<th>Heading</th>
				<th>Sub Section Heading</th>
				<th>Date</th>
				<th>Function</th>
			</tr>
			<?php while($row = mysqli_fetch_array($treatment_charts_list)) {
				$patientform = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `patientform` WHERE `patientformid` = '".$row['patientformid']."'")); ?>
				<tr>
					<td data-title="Contact"><?= get_contact($dbc, $row['patientid']) ?></td>
					<td data-title="Topic"><?= ucfirst($patientform['category']) ?></td>
					<td data-title="Heading"><?= $patientform['heading'] ?></td>
					<td data-title="Sub Section Heading"><?= $patientform['sub_heading'] ?></td>
					<td data-title="Date"><?= $row['today_date'] ?></td>
					<td data-title="Function"><a href="<?= WEBSITE_URL ?>/Treatment/<?= $row['pdf_path'] ?>">View PDF</a></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo 'No Treatment Charts Found.';
	}
} else if ($field_option == "Match Addition") { ?>
	<script type="text/javascript">
	function reload_match() {
		var contactid = $('[name="contactid"]').val();
		$.ajax({
			url: '../Contacts/edit_addition_match.php?edit='+contactid,
			success: function(response) {
				$('#match_div').html(response);
				initInputs('#match_div');
			}
		});
	}
	function update_match_status(a) {
		var matchid = $(a).data('matchid');
		var status = $(a).data('status');
		$.ajax({
			url :'../Match/isp_ajax_all.php?fill=update_match_status&matchid='+matchid+'&status='+status,
			method: 'GET',
			success: function(response) {
				reload_match();
			}
		});
	}
	function delete_match(a) {
		if(confirm("Are you sure you want to archive this Match?")) {
			var matchid = $(a).data('matchid');
			$.ajax({
				url :'../Match/isp_ajax_all.php?fill=delete_match&matchid='+matchid,
				method: 'GET',
				success: function(response) {
					$(a).closest('tr').remove();
				}
			});
		}
	}
	</script>
	<div class="form-group pull-right"><a href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Match/edit_match.php?from_tile=contacts&edit=&support_contact=<?= $contactid ?>', 'auto', false, true); return false;" class="btn brand-btn">Add Match</a></div>
	<div id="match_div">
		<?php include('../Contacts/edit_addition_match.php'); ?>
	</div>
<?php } else if ($field_option == "Customer Rate Card Addition" && vuaed_visible_function($dbc,'rate_card')) {
	$rate_card_list = mysqli_query($dbc, "SELECT * FROM `rate_card` WHERE `clientid` ='$contactid' AND `clientid` > 0 AND `deleted` = 0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')"); ?>
	<a href="../Rate Card/ratecards.php?type=customer&card=customer&status=add&clientid=<?= $contactid ?>" class="btn brand-btn pull-right" onclick="overlayIFrameSlider(this.href, 'auto', false, true); return false;">Add New</a>
	<?php if(mysqli_num_rows($rate_card_list) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th>Client</th>
				<th>Rate Card Name</th>
				<th>Total Cost</th>
				<th>Function</th>
			</tr>
			<?php while($row = mysqli_fetch_array($rate_card_list)) { ?>
				<tr>
					<td data-title="Client"><?= (!empty(get_client($dbc, $row['clientid'])) ? get_client($dbc, $row['clientid']) : get_contact($dbc, $row['clientid'])) ?></td>
					<td data-title="Rate Card Name"><?= $row['rate_card_name'] ?></td>
					<td data-title="Total Cost">$<?= number_format($row['total_price'],2,'.','') ?></td>
					<td data-title="Function"><a href="<?= WEBSITE_URL ?>/Rate Card/ratecards.php?type=customer&card=customer&status=add&ratecardid=<?= $row['ratecardid'] ?>" onclick="overlayIFrameSlider(this.href, 'auto', false, true); return false;">Edit</a></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo 'No Customer Rate Cards Found.';
	} ?>
	<a href="../Rate Card/ratecards.php?type=customer&card=customer&status=add&clientid=<?= $contactid ?>" class="btn brand-btn pull-right" onclick="overlayIFrameSlider(this.href, 'auto', false, true); return false;">Add New</a>
<?php } else if ($field_option == "Customer Rate Card Fields" || $field_option == "Customer Rate Card Totalled" || $field_option == "Customer Rate Card Totalled Group Cat Type") {
	$rate_card = mysqli_query($dbc, "SELECT * FROM `rate_card` WHERE `clientid` ='$contactid' AND `clientid` > 0 AND `deleted` = 0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')")->fetch_assoc();
	$service_fields = explode(',',get_field_config($dbc, 'services'));
	$service_templates = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `services_service_templates` WHERE `deleted` = 0 AND `contactid` = 0"),MYSQLI_ASSOC); ?>
	<div class="form-group">
		<label class="col-sm-4 control-label">Rate Card Name:</label>
		<div class="col-sm-8">
			<input type="text" name="rate_card_name" data-contactid-field="clientid" data-row-id="<?= $rate_card['ratecardid'] ?>" data-row-field="ratecardid" value="<?= $rate_card['rate_card_name'] ?>" data-field="rate_card_name" data-table="rate_card" class="form-control">
		</div>
	</div>
	<?php if(in_array('Service Create Ticket',$service_fields)) { ?>
		<button class="btn brand-btn pull-right" onclick="create_service_ticket(); return false;">Create <?= TICKET_NOUN ?> for Selected Services</button>
	<?php } ?>
	<?php if(!empty($service_templates)) { ?>
		<a href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Contacts/load_service_template.php?contactid=<?= $contactid ?>', 'auto', true, true); return false" class="btn brand-btn pull-right">Load Service Template</a>
	<?php } ?>
	<a href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Contacts/customer_service_template.php?contactid=<?= $contactid ?>', 'auto', false, true); return false;" class="btn brand-btn pull-right">Edit Customer Service Templates</a>
		<div class="clearfix"></div>
	<h4>Services</h4>
	<script>
	$(document).on('change', 'select[name="service_num_rooms[]"]', function() {
		<?php if($field_option == 'Customer Rate Card Totalled Group Cat Type') { ?>
			update_contacts_services_group_rooms(this);
		<?php } else { ?>
			update_contacts_services_rooms(this);
		<?php } ?>
	});
	$(document).on('change', 'select[name="service_category_group"]', function() { rate_service_category_group_updated(this); });
	$(document).on('change', 'select[name="service_type_group"]', function() { rate_service_type_group_updated(this); });
	function reload_service_table() {
		$.ajax({
			url: '../Contacts/edit_addition_customer_rate_services.php?edit=<?= $contactid ?>&field_option=<?= $field_option ?>',
			success: function(response) {
				$('.customer_rate_div').html(response);
				$('.customer_rate_div').find('[data-field]').not('.tile-search').off('blur',unsaved).blur(unsaved).off('focus',unsaved).focus(unsaved).off('change',saveField).change(saveField);
			}
		});
	}
	function select_template_items(chk) {
		var templateid = '';
		if($(chk).is(':checked')) {
			templateid = $(chk).data('templateid');
		}
		$.ajax({
			url: '../Contacts/edit_addition_customer_rate_services.php?edit=<?= $contactid ?>&load_template='+templateid+'&field_option=<?= $field_option ?>',
			success: function(response) {
				$('.customer_rate_div').html(response);
				$('.customer_rate_div').find('[data-field]').not('.tile-search').off('blur',unsaved).blur(unsaved).off('focus',unsaved).focus(unsaved).off('change',saveField).change(saveField);
			}
		});
	}
	function create_service_ticket() {
		var service_list = [];
		$('[name=service_create]:checked').each(function() {
			service_list.push(this.value);
			$(this).removeAttr('checked');
		});
		$('[name=select_service_template]:checked').each(function() {
			$(this).removeAttr('checked');
		});
		overlayIFrameSlider('../Ticket/index.php?calendar_view=true&edit=0&<?= $contact['category'] == BUSINESS_CAT ? 'bid' : 'clientid' ?>=<?= $contactid ?>&from_type=customer_rate_services&serviceid='+service_list.join(','), 'auto', true, true);
	}
	function rate_service_category_updated(select) {
		var next = $(select).closest('td').nextAll().find('select').first();
		if(select.value != '') {
			$.post('contacts_ajax.php?action=loadServices', { category: select.value, target: next.attr('name') }, function(response) {
				next.empty().html(response).trigger('chosen.select2');
			});
			if(next.attr('name') != 'heading') {
				var heading = $(select).closest('td').nextAll().find('[name="heading"]').first();
				$.post('contacts_ajax.php?action=loadServices', { category: select.value, target: 'heading' }, function(response) {
					heading.empty().html(response).trigger('chosen.select2');
				});
			}
		}
	}
	function rate_service_type_updated(select) {
		var next = $(select).closest('td').nextAll().find('[name=heading]').first();
		if(select.value != '') {
			$.post('contacts_ajax.php?action=loadServices', { type: select.value, target: next.attr('name') }, function(response) {
				next.empty().html(response).trigger('chosen.select2');
			});
		}
	}
	function rate_service_updated(select) {
		$(select).closest('tr').find('[name="services[]"]').data('prepend',$(select).val()+'#').change();
		$(select).closest('tr').find('[name=service_create]').val($(select).val());

		if($(select).closest('tr').find('[name=service_time_estimate]') != undefined) {
			$.ajax({
				url: 'contacts_ajax.php?action=getServiceTimeEstimate&serviceid='+$(select).val(),
				success: function(response) {
					$(select).closest('tr').find('[name=service_time_estimate]').val(response);
				}
			});
		}
		<?php if($field_option == 'Customer Rate Card Totalled Group Cat Type') { ?>
			if($(select).closest('.cattype_block').find('[name="service_num_rooms[]"]') != undefined) {
				update_contacts_services_group_rooms($(select).closest('.cattype_block').find('[name="service_num_rooms[]"]'));
			}
		<?php } else { ?>
			if($(select).closest('tr').find('[name="service_num_rooms[]"]') != undefined) {
				update_contacts_services_rooms($(select).closest('tr').find('[name="service_num_rooms[]"]'));
			}
		<?php } ?>
	}
	function rate_service_remove(img, table = '.customer_rate_services') {
		var table = $(img).closest('table');
		if($(table).find('tr select[name="heading"]').length <= 1) {
			addDelimitedRow($(table));
		}
		$(img).closest('tr').remove();
		$(table).find('[name="services[]"]').first().change();
		$(table).find('[name="service_comments[]"]').first().change();
	}
	function add_all_services(chk) {
		var table = $(chk).closest('table');
		var tr = $(chk).closest('tr');
		var category = $(tr).find('select.service_category').val();
		var service_type = $(tr).find('select.service_type').val();
		var keep_adding = true;
		if((category != '' && category != undefined) || (service_type != '' && service_type != undefined)) {
			$(tr).find('[name="heading"] option').each(function() {
				destroyInputs(table);
				var serviceid = $(this).val();
				if(serviceid != undefined && serviceid != '' && table.find('[name="heading"][value="'+serviceid+'"]').length == 0) {
					var clone = tr.clone();
					clone.find('input,select').val('');
					clone.find('select.service_category').val(category);
					clone.find('select.service_type').val(service_type);
					clone.find('[name="heading"]').val(serviceid);
					table.append(clone);
					initInputs('#'+table.attr('id'));
					table.find('[data-field]').not('.tile-search').off('blur',unsaved).blur(unsaved).off('focus',unsaved).focus(unsaved).off('change',saveField).change(saveField);
					table.find('tr').last().find('[name="heading"]').change();
				}
			});
			destroyInputs(table);
			initInputs('#'+table.attr('id'));
			$(table).find('select[name="heading"]').each(function() {
				if($(this).val() == '' || $(this).val() == undefined) {
					$(this).closest('tr').find('.remove_btn').click();
				}
			});
		}
	}
	function update_contacts_services_rooms(sel) {
		var contactid = $('[name=contactid]').val();
		var tr = $(sel).closest('tr');
		var serviceid = $(tr).find('[name="heading"]').val();
		var num_rooms = $(sel).val();
		if(serviceid > 0) {
			$.ajax({
				url: '../Contacts/contacts_ajax.php?action=update_contacts_services',
				method: 'POST',
				data: {
					contactid: contactid,
					serviceid: serviceid,
					num_rooms: num_rooms
				},
				success: function(response) {
					$(sel).val(response).trigger('change.select2');
				}
			});
		}
	}
	function rate_service_category_group_updated(select) {
		var block = $(select).closest('.cattype_block');
		var category = $(block).find('[name="service_category_group"]').val();
		$(block).find('[name="service_type_group"] option').show();
		if(category != undefined && category != '') {
			$(block).find('[name="service_type_group"] option').filter(function() { return $(this).data('category') != category; }).hide();
		}
		$(block).find('[name="service_type_group"]').trigger('change.select2');

		rate_service_accordion_display(block);
	}
	function rate_service_type_group_updated(select) {
		var block = $(select).closest('.cattype_block');
		var service_type = $(block).find('[name="service_type_group"]');
		if(service_type != undefined && service_type.val() != '') {
			$(block).find('[name="service_category_group"]').val($(service_type).find('option:selected').data('category'));
		}
		$(block).find('[name="service_category_group"]').trigger('change.select2');

		rate_service_accordion_display(block);
	}
	function rate_service_accordion_display(block) {
		var contactid = $('[name=contactid]').val();
		var category = $(block).find('[name="service_category_group"]').val();
		var service_type = $(block).find('[name="service_type_group"]').val();

		if(((category != undefined && category != '') || category == undefined) && ((service_type != undefined && service_type != '') || service_type == undefined)) {
			$.ajax({
				url: '../Contacts/edit_addition_customer_rate_service_group.php?edit='+contactid+'&reload_table=true',
				method: 'POST',
				data: { category: category, service_type: service_type },
				dataType: 'html',
				success: function(response) {
					destroyInputs('.cattype_block');
					$(block).find('.service_group').html(response);
					$(block).find('.service_group').find('[data-field]').off('blur',unsaved).blur(unsaved).off('focus',unsaved).focus(unsaved).off('change',saveField).change(saveField);
					$(block).find('.service_group').show();
					$(block).find('.service_group_hide').hide();
					initInputs('.cattype_block');

					var title = '';
					if(category != undefined && category != '') {
						title += category;
					}
					if(service_type != undefined && service_type != '') {
						title += ': '+service_type;
					}
					if(title != '') {
						title += ' - Services';
					} else {
						title = 'Services';
					}
					$(block).closest('.cattype_block').find('.panel-title a').text(title);
				}
			});
		} else {
			$(block).find('.service_group').hide();
			$(block).find('.service_group_hide').show();
		}
	}
	function update_contacts_services_group_rooms(sel) {
		var contactid = $('[name=contactid]').val();
		var num_rooms = $(sel).val();

		var block = $(sel).closest('.cattype_block');
		var services = [];
		$(block).find('[name="heading"]').each(function() {
			var serviceid = $(this).val();
			if(serviceid > 0) {
				services.push(serviceid);
			}
		});

		$.ajax({
			url: '../Contacts/contacts_ajax.php?action=update_contacts_services_group',
			method: 'POST',
			data: {
				contactid: contactid,
				services: services,
				num_rooms: num_rooms
			},
			success: function(response) {
				$(sel).val(response).trigger('change.select2');
			}
		});
	}
	function add_service_group() {
		destroyInputs('.cattype_block');
		var block = $('.cattype_block').last();
		var clone = $(block).clone();
		var panel_i = $('[name="service_panel_i"]').val();
		$('[name="service_panel_i"]').val(parseInt(panel_i) + 1);

		clone.find('input,select').val('');
		clone.find('.service_group').html('').hide();
		clone.find('.service_group_hide').show();
		clone.find('.panel-collapse').prop('id', 'collapse_service_group_'+panel_i);
		clone.find('.panel-title a').prop('href', '#collapse_service_group_'+panel_i);
		clone.find('.panel-title a').html('Services<span class="glyphicon glyphicon-plus"></span>');

		block.after(clone);

		initInputs('.cattype_block');
	}
	</script>
	<div id="no-more-tables" class="customer_rate_div">
		<?php include('../Contacts/edit_addition_customer_rate_services.php'); ?>
	</div>
	<?php if(in_array('Service Create Ticket',$service_fields)) { ?>
		<button class="btn brand-btn pull-right" onclick="create_service_ticket(); return false;">Create <?= TICKET_NOUN ?> for Selected Services</button>
	<?php } ?>
	<?php if($field_option == 'Customer Rate Card Totalled Group Cat Type') { ?>
		<button class="btn brand-btn pull-right" onclick="add_service_group(); return false;">Add Service Group</button>
	<?php } else { ?>
		<button class="btn brand-btn pull-right" onclick="addDelimitedRow($('.customer_rate_services')); return false;">Add Service</button>
	<?php } ?>
	<div class="clearfix"></div>
<?php } else if ($field_option == "Intake Forms Addition") {
	$intake_list = mysqli_query($dbc, "SELECT * FROM `intake` WHERE `contactid` = '$contactid' AND `deleted` = 0 ORDER BY `received_date` DESC");
	if(mysqli_num_rows($intake_list) > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th>Contact</th>
				<th>Category</th>
				<th>Received Date</th>
				<th>Function</th>
			</tr>
			<?php while($row = mysqli_fetch_array($intake_list)) { ?>
				<tr>
					<td data-title="Contact"><?= get_contact($dbc, $row['contactid']) ?></td>
					<td data-title="Category"><?= $row['category'] ?></td>
					<td data-title="Received Date"><?= $row['received_date'] ?></td>
					<td data-title="Function"><a href="<?= WEBSITE_URL ?>/Intake/<?= $row['intake_file'] ?>">View PDF</a></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo 'No Intake Forms Found.';
	}
} else if ($field_option == "Ticket Notifications Addition") {
	$noti_list = mysqli_query($dbc, "SELECT * FROM `ticket_notifications` WHERE (CONCAT(',', `staffid`, ',') LIKE '%,$contactid,%' OR CONCAT(',', `contactid`, ',') LIKE '%,$contactid,%') AND `deleted` = 0 ORDER BY `send_date` ASC");
	if(mysqli_num_rows($noti_list) > 0) { ?>
		<table class="table table-bordered">
			<tr>
				<th>Staff</th>
				<th>Contacts</th>
				<th>Sender Name</th>
				<th>Sender Email</th>
				<th>Status</th>
				<th>Send Date</th>
				<th>Follow Up Date</th>
				<th>Log</th>
			</tr>
			<?php while($row = mysqli_fetch_array($noti_list)) { ?>
				<tr>
					<td data-title="Staff"><?php $staff_list = [];
						foreach (explode(',', $row['staffid']) as $noti_staffid) {
							$staff_list[] = get_contact($dbc, $noti_staffid);
						}
						echo implode(', ', $staff_list); ?>
					</td>
					<td data-title="Contacts"><?php $contacts_list = [];
						foreach (explode(',', $row['contactid']) as $noti_contactid) {
							$contacts_list[] = get_contact($dbc, $noti_contactid);
						}
						echo implode(', ', $contacts_list); ?>
					</td>
					<td data-title="Sender Name"><?= $row['sender_name'] ?></td>
					<td data-title="Sender Email"><?= $row['sender_email'] ?></td>
					<td data-title="Status"><?= $row['status'] ?></td>
					<td data-title="Send Date"><?= $row['send_date'] ?></td>
					<td data-title="Follow Up Date"><?= $row['follow_up_date'] ?></td>
					<td data-title="Log"><?= str_replace("\n", "<br>", $row['log']) ?></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo 'No Ticket Notifications Found.';
	}
} else if ($field_option == "Appointment Confirmations Addition") {
	$noti_list = mysqli_query($dbc, "SELECT * FROM `booking` WHERE `patientid` = '$contactid' AND `deleted` = 0 ORDER BY `appoint_date` ASC");
	if(mysqli_num_rows($noti_list) > 0) { ?>
		<table class="table table-bordered">
			<tr>
				<th>Staff</th>
				<th>Customer</th>
	            <th>Contact Info</th>
	            <th>Appointment Date</th>
	            <th>Status</th>
	            <th>Email Confirmation Sent Date</th>
			</tr>
			<?php while($row = mysqli_fetch_array($noti_list)) { ?>
				<tr>
					<td data-title="Staff"><?= get_contact($dbc, $row['therapistsid']) ?></td>
					<td data-title="Customer"><?= get_contact($dbc, $row['patientid']) ?></td>
					<td data-title="Contact Info"><?= get_email($dbc, $row['patientid']).'<br>'.get_contact_phone($dbc, $row['patientid']) ?></td>
					<td data-title="Appointment Date"><?= $row['appoint_date'] ?></td>
					<td data-title="Status"><?= $row['follow_up_call_status'] ?></td>
					<td data-title="Email Confirmation Sent Date"><?= !empty($row['confirmation_email_date']) ? $row['confirmation_email_date'] : 'No Email Sent.' ?></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo 'No Appointment Confirmations Found.';
	}
} else if ($field_option == "PO Addition") {
	$po_list = mysqli_query($dbc, "SELECT * FROM `contact_order_numbers` WHERE `contactid` = '$contactid' AND `deleted` = 0 AND `category`='po_number' ORDER BY `detail` ASC");
	if(mysqli_num_rows($po_list) > 0) { ?>
		<table class="table table-bordered">
			<tr>
				<th>Purchase Order #</th>
			</tr>
			<?php while($row = mysqli_fetch_array($po_list)) { ?>
				<tr>
					<td data-title="Purchase Order #"><a href="../Purchase Order/index.php?tab=cust_po&po=<?= $row['detail'] ?>"><?= $row['detail'] ?></a></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo 'No Purchase Order #s found.';
	}
} ?>