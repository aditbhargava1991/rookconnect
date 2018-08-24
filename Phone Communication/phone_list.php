<script type="text/javascript">
$(document).on('change', 'select[name="followup_by[]"]', function() { changeFollowup(this); });
$(document).on('change', 'select[name="status[]"]', function() { changePOSStatus(this); });
function changePOSStatus(sel) {
	var status = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../Phone Communication/communication_ajax_all.php?fill=update_comm_status&phone_communicationid="+arr[1]+'&status='+status,
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
<form name="form_sites" method="post" action="" class="form-inline" role="form">

            <center>
            <div class="form-group">
                <label for="site_name" class="col-sm-5 control-label">Search By Any:</label>
                <div class="col-sm-6">
				<?php if(isset($_POST['search_equipment_submit'])) { ?>
					<input style="max-width:260px;" type="text" name="search_equipment" value="<?php echo $_POST['search_equipment']?>" class="form-control">
				<?php } else { ?>
					<input style="max-width:260px;" type="text" name="search_equipment" class="form-control">
				<?php } ?>
                </div>
            </div>
            &nbsp;
                <span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here after you have entered text into Search By Any."><img src="../img/info.png" width="20"></a></span>
				<button type="submit" name="search_equipment_submit" value="Search"  class="btn brand-btn mobile-block">Search</button>

                <span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click to refresh the page and see all communication within this tab."><img src="../img/info.png" width="20"></a></span>
				<button type="submit" name="display_all_equipment" value="Display All" class=" btn brand-btn mobile-block">Display All</button>
            </center>

			<?php
				if(vuaed_visible_function($dbc, 'phone_communication') == 1) {
                    echo '<div class="pull-right"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here to create and phone internal or external phone communication tied to this project."><img src="../img/info.png" width="20"></a></span>';
					echo '<a href="../Phone Communication/add_communication.php?projectid='.$_GET['edit'].'&type='.$_GET['type'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" class="btn brand-btn mobile-100-pull-right mobile-block">Add Communication</a></div>';
				}
			?>

    <div id="no-more-tables"> <?php

    $equipment = '';

    if (isset($_POST['search_equipment_submit'])) {
        $equipment = $_POST['search_equipment'];

        if (isset($_POST['search_equipment'])) {
            $equipment = $_POST['search_equipment'];
        }
        if ($_POST['search_category'] != '') {
            $equipment = $_POST['search_category'];
        }
    }

    if (isset($_POST['display_all_equipment'])) {
        $equipment = '';
    }

    /* Pagination Counting */
    $rowsPerPage = 25;
    $pageNum = 1;

    if(isset($_GET['page'])) {
        $pageNum = $_GET['page'];
    }

    $offset = ($pageNum - 1) * $rowsPerPage;

    if($equipment != '') {
        $query_check_credentials = "SELECT * FROM equipment WHERE deleted=0 AND (unit_number LIKE '%" . $equipment . "%' OR type LIKE '%" . $equipment . "%' OR category LIKE '%" . $equipment . "%' OR ownership_status LIKE '%" . $equipment . "%' OR make LIKE '%" . $equipment . "%' OR model LIKE '%" . $equipment . "%' OR model_year LIKE '%" . $equipment . "%' OR cost LIKE '%" . $equipment . "%') LIMIT $offset, $rowsPerPage";
        $query = "SELECT count(*) as numrows FROM equipment WHERE deleted=0 AND (unit_number LIKE '%" . $equipment . "%' OR type LIKE '%" . $equipment . "%' OR category LIKE '%" . $equipment . "%' OR ownership_status LIKE '%" . $equipment . "%' OR make LIKE '%" . $equipment . "%' OR model LIKE '%" . $equipment . "%' OR model_year LIKE '%" . $equipment . "%' OR cost LIKE '%" . $equipment . "%')";
    } else {
		$project_clause = '';
		if(!empty($_GET['projectid'])) {
			$project_clause = " AND (`projectid`='".$_GET['projectid']."' OR CONCAT('C',`client_projectid`)='".$_GET['projectid']."')";
		} else if(!empty($_GET['edit'])) {
			$project_clause = " AND (`projectid`='".$_GET['edit']."' OR CONCAT('C',`client_projectid`)='".$_GET['edit']."')";
		}
        if($_GET['type'] == 'Internal') {
            $query_check_credentials = "SELECT * FROM phone_communication WHERE communication_type = 'Internal' AND deleted = 0 AND status != 'Archived' $project_clause ORDER BY phone_communicationid DESC LIMIT $offset, $rowsPerPage";
            $query = "SELECT count(*) as numrows FROM phone_communication WHERE communication_type = 'Internal' AND deleted = 0 AND status != 'Archived' $project_clause ORDER BY phone_communicationid DESC";
            $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT internal_communication_dashboard FROM field_config"));
            $value_config = ','.$get_field_config['internal_communication_dashboard'].',';
        } else {
            $query_check_credentials = "SELECT * FROM phone_communication WHERE communication_type = 'External' AND deleted = 0 AND status != 'Archived' $project_clause ORDER BY phone_communicationid DESC LIMIT $offset, $rowsPerPage";
            $query = "SELECT count(*) as numrows FROM phone_communication WHERE communication_type = 'External' AND deleted = 0 AND status != 'Archived' $project_clause ORDER BY phone_communicationid DESC";
            $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT external_communication_dashboard FROM field_config"));
            $value_config = ','.$get_field_config['external_communication_dashboard'].',';
        }
    }

    $result = mysqli_query($dbc, $query_check_credentials);

    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0) {

        // Added Pagination //
        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
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
            echo '<th>Comment</th>';
        }
        if (strpos($value_config, ','."Body".',') !== FALSE) {
            echo '<th>Body</th>';
        }
        if (strpos($value_config, ','."Attachment".',') !== FALSE) {
            echo '<th>Attachment</th>';
        }
        if (strpos($value_config, ','."To Staff".',') !== FALSE) {
            echo '<th>To Staff</th>';
        }
        if (strpos($value_config, ','."CC Staff".',') !== FALSE) {
            echo '<th>CC Staff</th>';
        }
        if (strpos($value_config, ','."To Contact".',') !== FALSE) {
            echo '<th>To Contact</th>';
        }
        if (strpos($value_config, ','."CC Contact".',') !== FALSE) {
            echo '<th>CC Contact</th>';
        }
		if (strpos($value_config, ','."Additional Phone".',') !== FALSE) {
            echo '<th>Additional Phone</th>';
        }
		echo '<th>Date of Call</th>';
        if (strpos($value_config, ','."Phone Date".',') !== FALSE) {
            echo '<th>Phone Date</th>';
        }
        if (strpos($value_config, ','."Phone By".',') !== FALSE) {
            echo '<th>Staff</th>';
        }
		if (strpos($value_config, ','."Follow Up By".',') !== FALSE) {
            echo '<th>Follow Up By</th>';
        }
		if (strpos($value_config, ','."Follow Up Date".',') !== FALSE) {
            echo '<th>Follow Up Date</th>';
        }
            echo '<th>Status</th><th>Function</th>';
            echo "</tr></thead>";
        } else {
            echo "<h2>No Record Found.</h2>";
        }

        while($row = mysqli_fetch_array( $result ))
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
            echo '<td data-title="Comment">' . $row['comment']. '</td>';
        }
		if (strpos($value_config, ','."Additional Phone".',') !== FALSE) {
            echo '<td data-title="Additional Phone">' . $row['new_phoneid']. '</td>';
        }
        echo '<td data-title="Phone Date">' . $row['doc']. '</td>';
        if (strpos($value_config, ','."Phone By".',') !== FALSE) {
            echo '<td data-title="Phone By">'.get_staff($dbc, $row['created_by']) . '</td>';
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

        echo '<td data-title="Function">';

        if(vuaed_visible_function($dbc, 'phone_communication') == 1) {
            echo '<a href=\'../Phone Communication/add_communication.php?type='.$_GET['type'].'&phone_communicationid='.$row['phone_communicationid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'\'>Edit | </a>';
        }

		echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?type='.$_GET['type'].'&action=delete&phone_communicationid='.$row['phone_communicationid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
        echo '</td>';

        echo "</tr>";
    }
    echo '</table>';

   
	// Added Pagination //
	echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
	// Pagination Finish //

    if(vuaed_visible_function($dbc, 'phone_communication') == 1) {
        echo '<div class="pull-right"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here to create and phone internal or external phone communication tied to this project."><img src="../img/info.png" width="20"></a></span>';
        echo '<a href="../Phone Communication/add_communication.php?projectid='.$_GET['edit'].'&type='.$_GET['type'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" class="btn brand-btn mobile-block">Add Communication</a></div>';
    }

    ?>

</div>
</form>