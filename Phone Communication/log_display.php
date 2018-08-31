<script type="text/javascript">
$(document).on('change', 'select[name="followup_by[]"]', function() { changeFollowup(this); });
$(document).on('change', 'select[name="status[]"]', function() { changePOSStatus(this); });
function changePOSStatus(sel) {
	var status = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "<?php echo WEBSITE_URL; ?>/Phone Communication/communication_ajax_all.php?fill=update_comm_status&phone_communicationid="+arr[1]+'&status='+status,
		dataType: "html",   //expect html to be returned
		success: function(response){
			if(status == 'Archived') {
				$(sel).closest('tr').hide();
			}
		}
	});
}
function changeFollowup(sel) {
	var by = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../Phone Communication/communication_ajax_all.php?fill=update_comm_followup&phone_communicationid="+arr[1]+'&by='+by,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
	});
}
function changeFollowupDate(sel) {
	var date = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../Phone Communication/communication_ajax_all.php?fill=update_comm_followup_date&phone_communicationid="+arr[1]+'&date='+date,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
	});
}
</script>
<div id="no-more-tables">
<?php
$project_clause = '';
if(!empty($_GET['projectid'])) {
	$project_clause = " WHERE (`projectid`='".$_GET['projectid']."' OR CONCAT('C',`client_projectid`)='".$_GET['projectid']."')";
} else if(!empty($_GET['edit'])) {
	$project_clause = " WHERE (`projectid`='".$_GET['edit']."' OR CONCAT('C',`client_projectid`)='".$_GET['edit']."')";
}
/* Pagination Counting */
$rowsPerPage = 25;
$pageNum = 1;

if(isset($_GET['page'])) {
	$pageNum = $_GET['page'];
}

$offset = ($pageNum - 1) * $rowsPerPage;

$query_check_credentials = "SELECT * FROM phone_communication $project_clause ORDER BY phone_communicationid DESC LIMIT $offset, $rowsPerPage";
$query_num_rows = "SELECT COUNT(*) numrows FROM phone_communication $project_clause ORDER BY phone_communicationid DESC";
$result = mysqli_query($dbc, $query_check_credentials);
$num_rows = mysqli_num_rows($result);

$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT log_communication_dashboard FROM field_config"));
$value_config = ','.$get_field_config['log_communication_dashboard'].',';

if($num_rows > 0) {
	// Added Pagination //
	echo display_pagination($dbc, $query_num_rows, $pageNum, $rowsPerPage);
	// Pagination Finish //
	
	echo "<table class='table table-bordered table-striped'>";
	echo "<thead><tr class='hidden-xs hidden-sm'>";
	if (strpos($value_config, ','."Phone#".',') !== FALSE) {
		echo '<th>Phone#</th>';
	}
	if (strpos($value_config, ','."Business".',') !== FALSE) {
		echo '<th>Business</th>';
	}
	if (strpos($value_config, ','."Contact".',') !== FALSE) {
		echo '<th>Contact</th>';
	}
	if (strpos($value_config, ','."Project".',') !== FALSE) {
		echo '<th>Project</th>';
	}
	if (strpos($value_config, ','."Subject".',') !== FALSE) {
		echo '<th>Subject</th>';
	}
	if (strpos($value_config, ','."Body".',') !== FALSE) {
		echo '<th>Body</th>';
	}
	if (strpos($value_config, ','."Attachment".',') !== FALSE) {
		echo '<th>Attachment</th>';
	}
	if (strpos($value_config, ','."Phone To".',') !== FALSE) {
		echo '<th>Phone To</th>';
	}
	if (strpos($value_config, ','."Phone Date".',') !== FALSE) {
		echo '<th>Phone Date</th>';
	}
	if (strpos($value_config, ','."Phone By".',') !== FALSE) {
		echo '<th>Phone By</th>';
	}
	if (strpos($value_config, ','."Follow Up By".',') !== FALSE) {
		echo '<th>Follow Up By</th>';
	}
	if (strpos($value_config, ','."Follow Up Date".',') !== FALSE) {
		echo '<th>Follow Up Date</th>';
	}
	if (strpos($value_config, ','."Status".',') !== FALSE) {
		echo '<th>Status</th>';
	}
	echo "</tr></thead>";

	while($row = mysqli_fetch_array($result))
	{
		echo '<tr>';
		if (strpos($value_config, ','."Phone#".',') !== FALSE) {
			echo '<td data-title="Phone#">' . $row['phone_communicationid']. '</td>';
		}
		if (strpos($value_config, ','."Business".',') !== FALSE) {
			echo '<td data-title="Business">' . get_contact($dbc, $row['businessid'], 'name'). '</td>';
		}
		if (strpos($value_config, ','."Contact".',') !== FALSE) {
			echo '<td data-title="Business">'.get_staff($dbc, $row['contactid']) . '</td>';
		}
		if (strpos($value_config, ','."Project".',') !== FALSE) {
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
		if (strpos($value_config, ','."Subject".',') !== FALSE) {
			echo '<td data-title="Subject">' . $row['subject']. '</td>';
		}
		if (strpos($value_config, ','."Body".',') !== FALSE) {
			echo '<td data-title="Body">' . html_entity_decode($row['phone_body']). '</td>';
		}
		if (strpos($value_config, ','."Attachment".',') !== FALSE) {
			echo '<td data-title="Attachment">';
			$phone_communicationid = $row['phone_communicationid'];
			$result1 = mysqli_query($dbc, "SELECT * FROM phone_communicationid_upload WHERE phone_communicationid='$phone_communicationid' ORDER BY phonecommuploadid DESC");
			while($row2 = mysqli_fetch_array($result1)) {
				echo '<a href="../Phone Communication/download/'.$row2['document'].'" target="_blank">'.$row2['document'].'</a></br>';
			}
			echo '</td>';
		}
		if (strpos($value_config, ','."Phone To".',') !== FALSE) {
			$all_phone = $row['to_staff'].','.$row['cc_staff'].','.$row['to_contact'].','.$row['cc_contact'].','.$row['new_phoneid'];
			$all_phone = str_replace(',,', ',', $all_phone);
			$all_phone = str_replace(',,', ',', $all_phone);
			$all_phone = rtrim($all_phone,',');
			$all_phone = ltrim($all_phone,',');
			$all_phone = str_replace(',', '<br>', $all_phone);
			echo '<td data-title="Phone To">' . $all_phone. '</td>';
		}
		if (strpos($value_config, ','."Phone Date".',') !== FALSE) {
			echo '<td data-title="Phone Date">' . $row['today_date']. '</td>';
		}
		if (strpos($value_config, ','."Phone By".',') !== FALSE) {
			echo '<td data-title="Phone By">' . get_staff($dbc, $row['created_by']). '</td>';
		}
		if (strpos($value_config, ','."Follow Up By".',') !== FALSE) {
			echo '<td data-title="Follow Up By"><select name="followup_by[]" id="followupdate_'.$row['phone_communicationid'].'" data-placeholder="Select a Staff..." class="chosen-select-deselect form-control">
					<option value=""></option>';
					$staff_query = mysqli_query($dbc,"SELECT contactid, first_name, last_name, category, phone_address FROM contacts WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY."");
					while($contact = mysqli_fetch_array($staff_query)) {
							echo '<option '.($row['follow_up_by'] == $contact['contactid'] ? " selected" : '').' value="'.$contact['contactid'].'">'.decryptIt($contact['first_name']).' '.decryptIt($contact['last_name']).'</option>';
					}
			echo '</select></td>';
		}
		if (strpos($value_config, ','."Follow Up Date".',') !== FALSE) {
			echo '<td data-title="Follow Up Date"><input type="text" name="followupdate[]" onchange="changeFollowupDate(this)" id="followupdate_'.$row['phone_communicationid'].'" value="'.$row['follow_up_date'].'" class="datepicker form-control"></td>';
		}
		if (strpos($value_config, ','."Status".',') !== FALSE) {
			echo '<td data-title="Current Status">';
			?>
			<select name="status[]" id="status_<?php echo $row['phone_communicationid']; ?>" class="chosen-select-deselect1 form-control" width="380">
				 <option value=""></option>
				 <option <?php if ($row['status'] == "Pending") { echo " selected"; } ?> value="Pending">Pending</option>
				 <option <?php if ($row['status'] == "Follow up") { echo " selected"; } ?> value="Follow up">Follow up</option>
				 <option <?php if ($row['status'] == "Resolved") { echo " selected"; } ?> value="Resolved">Resolved</option>
				 <!--<option <?php if ($row['status'] == "Archived") { echo " selected"; } ?> value="Archived">Archived</option>-->
			</select>

			<?php echo '</td>';
		}
		echo "</tr>";
	}
	echo '</table>';

	// Added Pagination //
	echo display_pagination($dbc, $query_num_rows, $pageNum, $rowsPerPage);
	// Pagination Finish //
} else {
	echo "<h2>No Record Found.</h2>";
} ?>

</div>