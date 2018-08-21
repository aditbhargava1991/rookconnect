<?php
/*
 * Email Log Dashboard
 * Included In: dashboard.php
 */
 
include ('../include.php');
?>

<script type="text/javascript">
$(document).ready(function() {
	$('#email_popup').click(function(e) {
		if(!$(e.target).hasClass('popup-inner')) {
			closeEmailPopup();
		}
	});
});
$(document).on('change', 'select[name="followup_by[]"]', function() { changeFollowup(this); });
$(document).on('change', 'select[name="status[]"]', function() { changeStatus(this); });
function changeStatus(sel) {
	var status = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "<?php echo WEBSITE_URL; ?>/Email Communication/communication_ajax_all.php?fill=update_comm_status&email_communicationid="+arr[1]+'&status='+status,
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
		url: "../Email Communication/communication_ajax_all.php?fill=update_comm_followup&email_communicationid="+arr[1]+'&by='+by,
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
		url: "../Email Communication/communication_ajax_all.php?fill=update_comm_followup_date&email_communicationid="+arr[1]+'&date='+date,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
	});
}
</script>

<form name="form_sites" method="post" action="" class="form-inline" role="form">
    <div class="row">
        <div class="col-sm-3 control-label">Filter By:</div>
        <div class="col-sm-6">
            <select name="filter" data-placeholder="Filter By..." class="chosen-select-deselect form-control">
                <option></option>
                <option value="Internal" <?= isset($_POST['filter']) && $_POST['filter'] == 'Internal' ? 'selected' : '' ?>>Internal</option>
                <option value="External" <?= isset($_POST['filter']) && $_POST['filter'] == 'External' ? 'selected' : '' ?>>External</option>
            </select>
        </div>
        <div class="col-sm-3">
            <input type="submit" name="submit" value="Submit" class="btn brand-btn" />
            <input type="submit" name="all" value="Display All" class="btn brand-btn" />
        </div>
    </div>
    
    <div id="no-more-tables" class="double-gap-top"><?php
        $project_clause = '';
        if(!empty($_GET['projectid'])) {
            $project_clause = " WHERE (`projectid`='".$_GET['projectid']."' OR CONCAT('C',`client_projectid`)='".$_GET['projectid']."')";
        } else if(!empty($_GET['edit'])) {
            $project_clause = " WHERE (`projectid`='".$_GET['edit']."' OR CONCAT('C',`client_projectid`)='".$_GET['edit']."')";
        }
        
        $type_clause = '';
        $filter_type = '';
        if ( isset($_POST['submit']) ) {
            $filter_type = isset($_POST['filter']) ? filter_var($_POST['filter'], FILTER_SANITIZE_STRING) : '';
        }
        if ( !empty($filter_type) ) {
            if ( !empty($project_clause) ) {
                $type_clause = " AND communication_type = '$filter_type'";
            } else {
                $type_clause = " WHERE communication_type = '$filter_type'";
            }
        }
        
        $search_clause = '';
        if ( isset($_POST['search']) ) {
            $search_term = filter_var($_POST['search_term'], FILTER_SANITIZE_STRING);
            if ( !empty($project_clause) ) {
                $search_clause = " AND (`subject` LIKE '%$search_term%' OR `email_body` LIKE '%$search_term%' OR `to_staff` LIKE '%$search_term%' OR `cc_staff` LIKE '%$search_term%' OR `to_contact` LIKE '%$search_term%' OR `cc_contact` LIKE '%$search_term%' OR `new_emailid` LIKE '%$search_term%' OR `status` LIKE '%$search_term%')";
            } else {
                $search_clause = " WHERE (`subject` LIKE '%$search_term%' OR `email_body` LIKE '%$search_term%' OR `to_staff` LIKE '%$search_term%' OR `cc_staff` LIKE '%$search_term%' OR `to_contact` LIKE '%$search_term%' OR `cc_contact` LIKE '%$search_term%' OR `new_emailid` LIKE '%$search_term%' OR `status` LIKE '%$search_term%')";
            }
        }
        
        if ( isset($_POST['all']) ) {
            $type_clause = '';
        }
            
        
        /* Pagination Counting */
        $rowsPerPage = 25;
        $pageNum = 1;

        if(isset($_GET['page'])) {
            $pageNum = $_GET['page'];
        }

        $offset = ($pageNum - 1) * $rowsPerPage;

        $query_check_credentials = "SELECT * FROM email_communication $project_clause $type_clause $search_clause ORDER BY email_communicationid DESC LIMIT $offset, $rowsPerPage";
        $query_num_rows = "SELECT COUNT(*) numrows FROM email_communication $project_clause $type_clause $search_clause ORDER BY email_communicationid DESC";
        $result = mysqli_query($dbc, $query_check_credentials);
        $num_rows = mysqli_num_rows($result);

        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT log_communication_dashboard FROM field_config"));
        $value_config = ','.$get_field_config['log_communication_dashboard'].',';

        if($num_rows > 0) {
            echo display_pagination($dbc, $query_num_rows, $pageNum, $rowsPerPage);

            echo '<div class="table-responsive">';
                echo '<table class="table table-bordered table-striped">';
                    echo '<thead>';
                        echo '<tr class="hidden-xs hidden-sm">';
                            echo (strpos($value_config, ','."Email#".',') !== false) ? '<th>Email#</th>' : '';
                            echo (strpos($value_config, ','."Business".',') !== false) ? '<th>Business</th>' : '';
                            echo (strpos($value_config, ','."Contact".',') !== false) ? '<th>Contact</th>' : '';
                            echo (strpos($value_config, ','."Project".',') !== false) ? '<th>Project</th>' : '';
                            echo (strpos($value_config, ','."Subject".',') !== false) ? '<th>Subject</th>' : '';
                            echo (strpos($value_config, ','."Body".',') !== false) ? '<th>Body</th>' : '';
                            echo (strpos($value_config, ','."Attachment".',') !== false) ? '<th>Attachment</th>' : '';
                            echo (strpos($value_config, ','."Email To".',') !== false) ? '<th>Email To</th>' : '';
                            echo (strpos($value_config, ','."Email Date".',') !== false) ? '<th>Email Date</th>' : '';
                            echo (strpos($value_config, ','."Email By".',') !== false) ? '<th>Email By</th>' : '';
                            echo (strpos($value_config, ','."Follow Up By".',') !== false) ? '<th>Follow Up By</th>' : '';
                            echo (strpos($value_config, ','."Follow Up Date".',') !== false) ? '<th>Follow Up Date</th>' : '';
                            echo (strpos($value_config, ','."Status".',') !== false) ? '<th>Status</th>' : '';
                        echo '</tr>';
                    echo '</thead>';
                    
                    while($row = mysqli_fetch_array($result)) {
                        echo '<tr>';
                            echo (strpos($value_config, ','."Email#".',') !== false) ? '<td data-title="Email#">'. $row['email_communicationid'] .'</td>' : '';
                            echo (strpos($value_config, ','."Business".',') !== false) ? '<td data-title="Business">'. get_contact($dbc, $row['businessid'], 'name') .'</td>' : '';
                            echo (strpos($value_config, ','."Contact".',') !== false) ? '<td data-title="Business">'. get_staff($dbc, $row['contactid']) .'</td>' : '';
                            if (strpos($value_config, ','."Project".',') !== false) {
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
                            echo (strpos($value_config, ','."Subject".',') !== false) ? '<td data-title="Subject">'. $row['subject'] .'</td>' : '';
                            echo (strpos($value_config, ','."Body".',') !== false) ? '<td data-title="Body">'. html_entity_decode($row['email_body']) .'</td>' : '';
                            if (strpos($value_config, ','."Attachment".',') !== false) {
                                echo '<td data-title="Attachment">';
                                $email_communicationid = $row['email_communicationid'];
                                $result1 = mysqli_query($dbc, "SELECT * FROM email_communicationid_upload WHERE email_communicationid='$email_communicationid' ORDER BY emailcommuploadid DESC");
                                while($row2 = mysqli_fetch_array($result1)) {
                                    echo '<a href="../Email Communication/download/'.$row2['document'].'" target="_blank">'.$row2['document'].'</a></br>';
                                }
                                echo '</td>';
                            }
                            if (strpos($value_config, ','."Email To".',') !== false) {
                                $all_email = $row['to_staff'].','.$row['cc_staff'].','.$row['to_contact'].','.$row['cc_contact'].','.$row['new_emailid'];
                                $all_email = str_replace(',,', ',', $all_email);
                                $all_email = str_replace(',,', ',', $all_email);
                                $all_email = rtrim($all_email,',');
                                $all_email = ltrim($all_email,',');
                                $all_email = str_replace(',', '<br>', $all_email);
                                echo '<td data-title="Email To">' . $all_email. '</td>';
                            }
                            echo (strpos($value_config, ','."Email Date".',') !== false) ? '<td data-title="Email Date">'. $row['today_date'] .'</td>' : '';
                            echo (strpos($value_config, ','."Email By".',') !== false) ? '<td data-title="Email By">'. get_staff($dbc, $row['created_by']) .'</td>' : '';
                            if (strpos($value_config, ','."Follow Up By".',') !== false) {
                                echo '<td data-title="Follow Up By">
                                    <select name="followup_by[]" id="followupby_'.$row['email_communicationid'].'" data-placeholder="Select a Staff..." class="chosen-select-deselect form-control">
                                        <option value=""></option>';
                                        $staff_query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name, category, email_address FROM contacts WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 AND `status` = 1"));
                                        foreach($staff_query as $contact) {
                                            echo '<option '.($row['follow_up_by'] == $contact['contactid'] ? " selected" : '').' value="'.$contact['contactid'].'">'.$contact['first_name'].' '.$contact['last_name'].'</option>';
                                        }
                                    echo '</select>
                                </td>';
                            }
                            if (strpos($value_config, ','."Follow Up Date".',') !== false) {
                                echo '<td data-title="Follow Up Date"><input type="text" name="followupdate[]" onchange="changeFollowupDate(this)" id="followupdate_'.$row['email_communicationid'].'" value="'.$row['follow_up_date'].'" class="datepicker form-control"></td>';
                            }
                            if (strpos($value_config, ','."Status".',') !== false) {
                                echo '<td data-title="Current Status">'; ?>
                                    <select name="status[]" id="status_<?php echo $row['email_communicationid']; ?>" class="chosen-select-deselect1 form-control">
                                        <option value=""></option>
                                        <option <?php if ($row['status'] == "Pending") { echo " selected"; } ?> value="Pending">Pending</option>
                                        <option <?php if ($row['status'] == "Follow up") { echo " selected"; } ?> value="Follow up">Follow up</option>
                                        <option <?php if ($row['status'] == "Resolved") { echo " selected"; } ?> value="Resolved">Resolved</option>
                                        <!--<option <?php if ($row['status'] == "Archived") { echo " selected"; } ?> value="Archived">Archived</option>-->
                                    </select><?php
                                echo '</td>';
                            }
                        echo '</tr>';
                    }
                echo '</table>';
            echo '</div><!-- .table-responsive -->';
            
            echo display_pagination($dbc, $query_num_rows, $pageNum, $rowsPerPage);
        } else {
            echo '<h2>No Record Found.</h2>';
        } ?>
    </div><!-- #no-more-tables -->
</form>