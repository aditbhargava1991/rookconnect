<?php
/*
 * Internal & External Email Dashboard
 * Included In: dashboard.php
 */
 
include ('../include.php');
$type = empty($type) ? (isset($_GET['type']) ? filter_var($_GET['type'], FILTER_SANITIZE_STRING) : 'internal') : $type;
?>

<script type="text/javascript">
    $(document).on('change', 'select[name="followup_by[]"]', function() { changeFollowup(this); });
    $(document).on('change', 'select[name="status[]"]', function() { changeStatus(this); });
    function changeStatus(sel) {
        var status = sel.value;
        var typeId = sel.id;
        var arr = typeId.split('_');
        $.ajax({
            type: "GET",
            url: "../Email Communication/communication_ajax_all.php?fill=update_comm_status&email_communicationid="+arr[1]+'&status='+status,
            dataType: "html",
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
        $.ajax({
            type: "GET",
            url: "../Email Communication/communication_ajax_all.php?fill=update_comm_followup&email_communicationid="+arr[1]+'&by='+by,
            dataType: "html",
            success: function(response){
            }
        });
    }
    function changeFollowupDate(sel) {
        var date = sel.value;
        var typeId = sel.id;
        var arr = typeId.split('_');
        $.ajax({
            type: "GET",
            url: "../Email Communication/communication_ajax_all.php?fill=update_comm_followup_date&email_communicationid="+arr[1]+'&date='+date,
            dataType: "html",
            success: function(response){
            }
        });
    }
</script>
<form name="form_sites" method="post" action="" class="form-inline" role="form">
    <div id="no-more-tables"><?php
        /* Pagination Counting */
        $rowsPerPage = 25;
        $pageNum = 1;

        if(isset($_GET['page'])) {
            $pageNum = $_GET['page'];
        }

        $offset = ($pageNum - 1) * $rowsPerPage;
        
        $project_clause = '';
        if(!empty($_GET['projectid'])) {
            $project_clause = " AND (`projectid`='".$_GET['projectid']."' OR CONCAT('C',`client_projectid`)='".$_GET['projectid']."')";
        } else if(!empty($_GET['edit'])) {
            $project_clause = " AND (`projectid`='".$_GET['edit']."' OR CONCAT('C',`client_projectid`)='".$_GET['edit']."')";
        }
        
        $search_clause = '';
        if ( isset($_POST['search']) ) {
            $search_term = filter_var($_POST['search_term'], FILTER_SANITIZE_STRING);
            $search_clause = " AND (`subject` LIKE '%$search_term%' OR `email_body` LIKE '%$search_term%' OR `to_staff` LIKE '%$search_term%' OR `cc_staff` LIKE '%$search_term%' OR `to_contact` LIKE '%$search_term%' OR `cc_contact` LIKE '%$search_term%' OR `new_emailid` LIKE '%$search_term%' OR `status` LIKE '%$search_term%')";
        }
        
        if($type == 'internal') {
            $query_comm = "SELECT * FROM `email_communication` WHERE `communication_type` = 'Internal' AND `deleted` = 0 AND `status` != 'Archived' $project_clause $search_clause ORDER BY `email_communicationid` DESC LIMIT $offset, $rowsPerPage";
            $query = "SELECT COUNT(*) `numrows` FROM `email_communication` WHERE `communication_type` = 'Internal' AND `deleted` = 0 AND `status` != 'Archived' $project_clause $search_clause ORDER BY `email_communicationid` DESC";
            $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `internal_communication_dashboard` FROM `field_config`"));
            $value_config = ','.$get_field_config['internal_communication_dashboard'].',';
        } else {
            $query_comm = "SELECT * FROM `email_communication` WHERE `communication_type` = 'External' AND `deleted` = 0 AND `status` != 'Archived' $project_clause $search_clause ORDER BY `email_communicationid` DESC LIMIT $offset, $rowsPerPage";
            $query = "SELECT COUNT(*) `numrows` FROM `email_communication` WHERE `communication_type` = 'External' AND `deleted` = 0 AND `status` != 'Archived' $project_clause $search_clause ORDER BY `email_communicationid` DESC";
            $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `external_communication_dashboard` FROM `field_config`"));
            $value_config = ','.$get_field_config['external_communication_dashboard'].',';
        }

        $result = mysqli_query($dbc, $query_comm);
        $num_rows = mysqli_num_rows($result);

        if($num_rows > 0) {
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);

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
                            echo (strpos($value_config, ','."To Staff".',') !== false) ? '<th>To Staff</th>' : '';
                            echo (strpos($value_config, ','."CC Staff".',') !== false) ? '<th>CC Staff</th>' : '';
                            echo (strpos($value_config, ','."To Contact".',') !== false) ? '<th>To Contact</th>' : '';
                            echo (strpos($value_config, ','."CC Contact".',') !== false) ? '<th>CC Contact</th>' : '';
                            echo (strpos($value_config, ','."Additional Email".',') !== false) ? '<th>Additional Email</th>' : '';
                            echo (strpos($value_config, ','."Email Date".',') !== false) ? '<th>Email Date</th>' : '';
                            echo (strpos($value_config, ','."Email By".',') !== false) ? '<th>Staff</th>' : '';
                            echo (strpos($value_config, ','."Follow Up By".',') !== false) ? '<th>Follow Up By</th>' : '';
                            echo (strpos($value_config, ','."Follow Up Date".',') !== false) ? '<th>Follow Up Date</th>' : '';
                            echo '<th>Status</th>';
                            echo '<th>Function</th>';
                        echo "</tr>";
                    echo "</thead>";

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
                            echo (strpos($value_config, ','."Subject".',') !== false) ? '<td data-title="Subject">'. html_entity_decode(htmlspecialchars_decode($row['subject'])) .'</td>' : '';
                            echo (strpos($value_config, ','."Body".',') !== false) ? '<td data-title="Body">'. html_entity_decode(htmlspecialchars_decode($row['email_body'])) .'</td>' : '';
                            if (strpos($value_config, ','."Attachment".',') !== false) {
                                echo '<td data-title="Attachment">';
                                $email_communicationid = $row['email_communicationid'];
                                $result1 = mysqli_query($dbc, "SELECT * FROM email_communicationid_upload WHERE email_communicationid='$email_communicationid' ORDER BY emailcommuploadid DESC");
                                while($row2 = mysqli_fetch_array($result1)) {
                                    echo '<a href="../Email Communication/download/'.$row2['document'].'" target="_blank">'.$row2['document'].'</a></br>';
                                }
                                echo '</td>';
                            }
                            echo (strpos($value_config, ','."To Staff".',') !== false) ? '<td data-title="To Staff">'. $row['to_staff'] .'</td>' : '';
                            echo (strpos($value_config, ','."CC Staff".',') !== false) ? '<td data-title="CC Staff">'. $row['cc_staff'] .'</td>' : '';
                            echo (strpos($value_config, ','."To Contact".',') !== false) ? '<td data-title="To Contact">'. $row['to_contact'] .'</td>' : '';
                            echo (strpos($value_config, ','."CC Contact".',') !== false) ? '<td data-title="CC Contact">'. $row['cc_contact'] .'</td>' : '';
                            echo (strpos($value_config, ','."Additional Email".',') !== false) ? '<td data-title="Additional Email">'. $row['new_emailid'] .'</td>' : '';
                            echo (strpos($value_config, ','."Email Date".',') !== false) ? '<td data-title="Email Date">'. $row['today_date'] .'</td>' : '';
                            echo (strpos($value_config, ','."Email By".',') !== false) ? '<td data-title="Email By">'. get_staff($dbc, $row['created_by']) .'</td>' : '';
                            if (strpos($value_config, ','."Follow Up By".',') !== false) {
                                echo '<td data-title="Follow Up By"><select name="followup_by[]" id="followupby_'.$row['email_communicationid'].'" data-placeholder="Select a Staff..." class="chosen-select-deselect form-control">
                                        <option value=""></option>';
                                        $staff_query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name, category, email_address FROM contacts WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 AND `status` = 1"));
                                        foreach($staff_query as $contact) {
                                                echo '<option '.($row['follow_up_by'] == $contact['contactid'] ? " selected" : '').' value="'.$contact['contactid'].'">'.$contact['first_name'].' '.$contact['last_name'].'</option>';
                                        }
                                echo '</select></td>';
                            }
                            if (strpos($value_config, ','."Follow Up Date".',') !== false) {
                                echo '<td data-title="Follow Up Date"><input type="text" name="followupdate[]" onchange="changeFollowupDate(this)" id="followupdate_'.$row['email_communicationid'].'" value="'.$row['follow_up_date'].'" class="datepicker form-control"></td>';
                            }
                            echo '<td data-title="Current Status">'; ?>
                                <select name="status[]" id="status_<?php echo $row['email_communicationid']; ?>" class="chosen-select-deselect1 form-control" width="380">
                                     <option value=""></option>
                                     <option <?php if ($row['status'] == "Pending") { echo " selected"; } ?> value="Pending">Pending</option>
                                     <option <?php if ($row['status'] == "Follow up") { echo " selected"; } ?> value="Follow up">Follow up</option>
                                     <option <?php if ($row['status'] == "Resolved") { echo " selected"; } ?> value="Resolved">Resolved</option>
                                     <!--<option <?php if ($row['status'] == "Archived") { echo " selected"; } ?> value="Archived">Archived</option>-->
                                </select><?php
                            echo '</td>';
                            echo '<td data-title="Function">';
                                if(vuaed_visible_function($dbc, 'email_communication') == 1) {
                                    echo '<a class="cursor-hand" onclick="overlayIFrameSlider(\'add_email.php?type='.$type.'\', \'auto\', false, true);">Edit</a> | ';
                                }
                                echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?type='.$_GET['type'].'&action=delete&email_communicationid='.$row['email_communicationid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
                            echo '</td>';
                        echo "</tr>";
                    }
                echo '</table>';
            echo '</div><!-- .table-responsive -->';
        } else {
            echo "<h2>No Record Found.</h2>";
        }
        
        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage); ?>
    </div><!-- #no-more-tables -->
</form>