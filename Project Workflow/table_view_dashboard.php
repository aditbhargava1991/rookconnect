<?php
if($tab == 'Shop Time Sheets' || $tab == 'Shop Time Sheet' || $tab == 'Payroll') {
	$current_url = WEBSITE_URL.'/Project Workflow/project_workflow_dashboard.php?tile='.$tile.'&tab='.$tab;
    include_once("../Shop Time Sheet/shop_time_sheets.php");
} else {
//if(vuaed_visible_function($dbc, 'project_manage') == 1) {
    $get_fields = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT project_manage FROM field_config_project_manage WHERE tile = '" . $tile . "' AND tab = '" . $tab . "' AND accordion IS NOT NULL"));
    if(isset($get_fields['project_manage']) || $get_fields['project_manage'] != null) {
        echo '<a href="add_project_manage.php?tile='.$tile.'&tab='.$tab_url.'" class="btn brand-btn mobile-block gap-bottom pull-right">Add '.$tab_url.'</a>';
    }
//} ?>
<?php
$approvals = approval_visible_function($dbc, 'shop_work_orders');
$workorderstaff = '';
$workorder = '';
$workorderdate = '';
$workorderenddate = '';
$sort_order = ' ORDER BY p.projectmanageid ASC ';
if (isset($_POST['search_shopworkorder_submit'])) {
    if (isset($_POST['search_staff'])) {
        $workorderstaff = $_POST['search_staff'];
    }
    if (isset($_POST['search_date'])) {
        $workorderdate = $_POST['search_date'];
    }
    if (isset($_POST['search_workorder'])) {
        $workorder = $_POST['search_workorder'];
    }
    if (isset($_POST['end_search_date'])) {
        $workorderenddate = $_POST['end_search_date'];
    }
}
if (isset($_POST['display_all_shopworkorder'])) {
	$workorderstaff = '';
	$workorder = '';
	$workorderdate = '';
    $workorderenddate = '';
}

$check_status = '';
if (strpos($value_config, ','."Pending Status".',') !== FALSE) {
    $check_status = '';
}
if (strpos($value_config, ','."Approved Status".',') !== FALSE) {
    $check_status = 'Approved';
    $tab_url = 'Pending Work Order';
}

$rowsPerPage = 10;
$pageNum = 1;

if(isset($_GET['page'])) {
    $pageNum = $_GET['page'];
}

$offset = ($pageNum - 1) * $rowsPerPage;

if($workorderdate == '' and $workorderstaff != '' and $workorder == 0) {
    $query_check_credentials = "SELECT p.*,pd.* FROM project_manage as p,project_manage_detail as pd WHERE tile='$tile' AND tab='$tab_url' AND p.projectmanageid = pd.projectmanageid AND pd.detailid in (SELECT MAX(`detailid`) FROM `project_manage_detail` GROUP BY `projectmanageid`) AND p.contactid=$workorderstaff AND p.status='$check_status' $sort_order LIMIT $offset, $rowsPerPage";
    $query = "SELECT count(p.projectmanageid) numrows FROM project_manage as p,project_manage_detail as pd WHERE tile='$tile' AND tab='$tab_url' AND p.projectmanageid = pd.projectmanageid AND pd.detailid in (SELECT MAX(`detailid`) FROM `project_manage_detail` GROUP BY `projectmanageid`) AND p.contactid=$workorderstaff AND p.status='$check_status'";
}
else if($workorderdate == '' and $workorderstaff == '' and $workorder != 0) {
    $query_check_credentials = "SELECT p.*,pd.* FROM project_manage as p,project_manage_detail as pd WHERE tile='$tile' AND tab='$tab_url' AND p.projectmanageid = pd.projectmanageid AND pd.detailid in (SELECT MAX(`detailid`) FROM `project_manage_detail` GROUP BY `projectmanageid`) AND p.status='$check_status' AND p.unique_id=".$workorder." $sort_order";
    $query = "SELECT count(p.projectmanageid) numrows FROM project_manage as p,project_manage_detail as pd WHERE tile='$tile' AND tab='$tab_url' AND p.projectmanageid = pd.projectmanageid AND pd.detailid in (SELECT MAX(`detailid`) FROM `project_manage_detail` GROUP BY `projectmanageid`) AND p.status='$check_status' AND p.unique_id=".$workorder;
}
else if($workorderdate != '' and $workorderstaff == '' and $workorder == 0) {
    $query_check_credentials = "SELECT p.*,pd.* FROM project_manage as p,project_manage_detail as pd WHERE tile='$tile' AND tab='$tab_url' AND p.status='$check_status' AND p.projectmanageid = pd.projectmanageid AND pd.detailid in (SELECT MAX(`detailid`) FROM `project_manage_detail` GROUP BY `projectmanageid`)  AND (start_date LIKE '%".$workorderdate."%' OR estimated_completion_date LIKE '%".$workorderdate."%' OR effective_date LIKE '%".$workorderdate."%') $sort_order LIMIT $offset, $rowsPerPage";
    $query = "SELECT count(p.projectmanageid) numrows FROM project_manage as p,project_manage_detail as pd WHERE tile='$tile' AND tab='$tab_url' AND p.status='$check_status' AND p.projectmanageid = pd.projectmanageid AND pd.detailid in (SELECT MAX(`detailid`) FROM `project_manage_detail` GROUP BY `projectmanageid`)  AND (start_date LIKE '%".$workorderdate."%' OR estimated_completion_date LIKE '%".$workorderdate."%' OR effective_date LIKE '%".$workorderdate."%')";
}
else if($workorderenddate != '' and $workorderstaff == '' and $workorder == 0) {
    $query_check_credentials = "SELECT p.*,pd.* FROM project_manage as p,project_manage_detail as pd WHERE tile='$tile' AND tab='$tab_url' AND p.status='$check_status' AND p.projectmanageid = pd.projectmanageid AND pd.detailid in (SELECT MAX(`detailid`) FROM `project_manage_detail` GROUP BY `projectmanageid`)  AND (start_date LIKE '%".$workorderdate."%' OR estimated_completion_date LIKE '%".$workorderdate."%' OR effective_date LIKE '%".$workorderdate."%') $sort_order LIMIT $offset, $rowsPerPage";
    $query = "SELECT count(p.projectmanageid) numrows FROM project_manage as p,project_manage_detail as pd WHERE tile='$tile' AND tab='$tab_url' AND p.status='$check_status' AND p.projectmanageid = pd.projectmanageid AND pd.detailid in (SELECT MAX(`detailid`) FROM `project_manage_detail` GROUP BY `projectmanageid`)  AND (start_date LIKE '%".$workorderdate."%' OR estimated_completion_date LIKE '%".$workorderdate."%' OR effective_date LIKE '%".$workorderdate."%')";
}
else if($workorderdate == '' and $workorderstaff != '' and $workorder != 0) {
    $query_check_credentials = "SELECT p.*,pd.* FROM project_manage as p,project_manage_detail as pd WHERE tile='$tile' AND tab='$tab_url' AND p.status='$check_status' AND p.projectmanageid = pd.projectmanageid AND pd.detailid in (SELECT MAX(`detailid`) FROM `project_manage_detail` GROUP BY `projectmanageid`)  AND p.contactid=$workorderstaff AND p.unique_id=".$workorder." $sort_order LIMIT $offset, $rowsPerPage";
    $query = "SELECT count(p.projectmanageid) numrows FROM project_manage as p,project_manage_detail as pd WHERE tile='$tile' AND tab='$tab_url' AND p.status='$check_status' AND p.projectmanageid = pd.projectmanageid AND pd.detailid in (SELECT MAX(`detailid`) FROM `project_manage_detail` GROUP BY `projectmanageid`)  AND p.contactid=$workorderstaff AND p.unique_id=".$workorder;
}
else if($workorderdate != '' and $workorderstaff != '' and $workorder == 0) {
    $query_check_credentials = "SELECT p.*,pd.* FROM project_manage as p,project_manage_detail as pd WHERE tile='$tile' AND tab='$tab_url' AND p.status='$check_status' AND p.projectmanageid = pd.projectmanageid AND pd.detailid in (SELECT MAX(`detailid`) FROM `project_manage_detail` GROUP BY `projectmanageid`)  AND p.contactid=$workorderstaff AND (start_date LIKE '%".$workorderdate."%' OR estimated_completion_date LIKE '%".$workorderdate."%' OR effective_date LIKE '%".$workorderdate."%') $sort_order LIMIT $offset, $rowsPerPage" ;
    $query = "SELECT count(p.projectmanageid) numrows FROM project_manage as p,project_manage_detail as pd WHERE tile='$tile' AND tab='$tab_url' AND p.status='$check_status' AND p.projectmanageid = pd.projectmanageid AND pd.detailid in (SELECT MAX(`detailid`) FROM `project_manage_detail` GROUP BY `projectmanageid`)  AND p.contactid=$workorderstaff AND (start_date LIKE '%".$workorderdate."%' OR estimated_completion_date LIKE '%".$workorderdate."%' OR effective_date LIKE '%".$workorderdate."%')" ;
}
else if($workorderdate != '' and $workorderstaff == '' and $workorder != 0) {
    $query_check_credentials = "SELECT p.*,pd.* FROM project_manage as p,project_manage_detail as pd WHERE tile='$tile' AND tab='$tab_url' AND p.status='$check_status' AND p.projectmanageid = pd.projectmanageid AND pd.detailid in (SELECT MAX(`detailid`) FROM `project_manage_detail` GROUP BY `projectmanageid`)  AND p.unique_id=".$workorder." AND (start_date LIKE '%".$workorderdate."%' OR estimated_completion_date LIKE '%".$workorderdate."%' OR effective_date LIKE '%".$workorderdate."%') $sort_order LIMIT $offset, $rowsPerPage" ;
    $query = "SELECT count(p.projectmanageid) numrows FROM project_manage as p,project_manage_detail as pd WHERE tile='$tile' AND tab='$tab_url' AND p.status='$check_status' AND p.projectmanageid = pd.projectmanageid AND pd.detailid in (SELECT MAX(`detailid`) FROM `project_manage_detail` GROUP BY `projectmanageid`)  AND p.unique_id=".$workorder." AND (start_date LIKE '%".$workorderdate."%' OR estimated_completion_date LIKE '%".$workorderdate."%' OR effective_date LIKE '%".$workorderdate."%')" ;
}
else if($workorderdate != '' and $workorderstaff != '' and $workorder != 0) {
    $query_check_credentials = "SELECT p.*,pd.* FROM project_manage as p,project_manage_detail as pd WHERE tile='$tile' AND tab='$tab_url' AND p.status='$check_status' AND p.projectmanageid = pd.projectmanageid AND pd.detailid in (SELECT MAX(`detailid`) FROM `project_manage_detail` GROUP BY `projectmanageid`)  AND p.contactid=$workorderstaff AND p.unique_id=".$workorder." AND (start_date LIKE '%".$workorderdate."%' OR estimated_completion_date LIKE '%".$workorderdate."%' OR effective_date LIKE '%".$workorderdate."%') $sort_order LIMIT $offset, $rowsPerPage" ;
    $query = "SELECT count(p.projectmanageid) numrows FROM project_manage as p,project_manage_detail as pd WHERE tile='$tile' AND tab='$tab_url' AND p.status='$check_status' AND p.projectmanageid = pd.projectmanageid AND pd.detailid in (SELECT MAX(`detailid`) FROM `project_manage_detail` GROUP BY `projectmanageid`)  AND p.contactid=$workorderstaff AND p.unique_id=".$workorder." AND (start_date LIKE '%".$workorderdate."%' OR estimated_completion_date LIKE '%".$workorderdate."%' OR effective_date LIKE '%".$workorderdate."%')" ;
}
else{
	//$query_check_credentials = "SELECT * FROM project_manage WHERE tile='$tile' AND tab='$tab_url'";
    $query_check_credentials = "SELECT p.*,pd.* FROM project_manage as p,project_manage_detail as pd WHERE tile='$tile' AND tab='$tab_url' AND p.status='$check_status' AND p.projectmanageid = pd.projectmanageid AND pd.detailid in (SELECT MAX(`detailid`) FROM `project_manage_detail` GROUP BY `projectmanageid`) $sort_order LIMIT $offset, $rowsPerPage";
    //SELECT p.*,pd.* FROM project_manage as p,project_manage_detail as pd WHERE p.tile='Test Work Order' AND p.tab='Work Order' and p.projectmanageid = pd.projectmanageid and pd.detail_workorder =124
    $query = "SELECT count(p.projectmanageid) numrows FROM project_manage as p,project_manage_detail as pd WHERE tile='$tile' AND tab='$tab_url' AND p.status='$check_status' AND p.projectmanageid = pd.projectmanageid AND pd.detailid in (SELECT MAX(`detailid`) FROM `project_manage_detail` GROUP BY `projectmanageid`)";
}
$result = mysqli_query($dbc, $query_check_credentials);
$num_rows = mysqli_num_rows($result);

if($num_rows > 0) {
    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
    echo "<table class='table table-bordered table-striped'>";
    echo "<thead><tr class='hidden-xs hidden-sm'>";
	if (strpos($value_config, ','."Work Order".',') !== FALSE) {
        echo '<th>Work Order#</th>';
    }
	if (strpos($value_config, ','."Staff Name".',') !== FALSE) {
        echo '<th>Staff Name#</th>';
    }
    if (strpos($value_config, ','."Business".',') !== FALSE) {
        echo '<th>Business Contact</th>';
    }
	/*if (strpos($value_config, ','."Contact".',') !== FALSE) {
        echo '<th>Staff Name</th>';
    }*/
	if (strpos($value_config, ','."Total Project Budget".',') !== FALSE) {
        echo '<th>Project Budget</th>';
    }
    if (strpos($value_config, ','."Rate Card".',') !== FALSE) {
        echo '<th>Rate Card</th>';
    }
    if (strpos($value_config, ','."Short Name".',') !== FALSE) {
        echo '<th>Short Name</th>';
    }
    if (strpos($value_config, ','."Piece Work".',') !== FALSE) {
        echo '<th>Piece Work</th>';
    }
    if (strpos($value_config, ','."Heading".',') !== FALSE) {
        echo '<th>Heading</th>';
    }
    if (strpos($value_config, ','."Location".',') !== FALSE) {
        echo '<th>Location</th>';
    }
    if (strpos($value_config, ','."Job number".',') !== FALSE) {
        echo '<th>Job number</th>';
    }
    if (strpos($value_config, ','."Customer PO/AFE#".',') !== FALSE) {
        echo '<th>Customer PO/AFE#</th>';
    }
    if (strpos($value_config, ','."Staff(Assign To)".',') !== FALSE) {
        echo '<th>Staff(Assign To)</th>';
    }
    if (strpos($value_config, ','."Created Date".',') !== FALSE) {
        echo '<th>Created Date</th>';
    }
    if (strpos($value_config, ','."Start Date".',') !== FALSE) {
        echo '<th>Start Date</th>';
    }
    if (strpos($value_config, ','."Estimated Completion Date".',') !== FALSE) {
        echo '<th>Estimated Completion Date</th>';
    }
    if (strpos($value_config, ','."Task Start Date".',') !== FALSE) {
        echo '<th>Task Start Date</th>';
    }
    if (strpos($value_config, ','."Time Clock Start Date".',') !== FALSE) {
        echo '<th>Time Clock Start Date</th>';
    }
    if (strpos($value_config, ','."Work performed".',') !== FALSE) {
        echo '<th>Work performed</th>';
    }
    if (strpos($value_config, ','."Path".',') !== FALSE) {
        echo '<th>Path</th>';
    }
    if (strpos($value_config, ','."Milestone & Timeline".',') !== FALSE) {
        echo '<th>Milestone & Timeline</th>';
    }
    if (strpos($value_config, ','."Service Type".',') !== FALSE) {
        echo '<th>Service Type</th>';
    }
    if (strpos($value_config, ','."Service Category".',') !== FALSE) {
        echo '<th>Service Category</th>';
    }
    if (strpos($value_config, ','."Service Heading".',') !== FALSE) {
        echo '<th>Service Heading</th>';
    }
    if (strpos($value_config, ','."Support Documents".',') !== FALSE) {
        echo '<th>Support Documents</th>';
    }
    if (strpos($value_config, ','."Support Links".',') !== FALSE) {
        echo '<th>Support Links</th>';
    }
    if (strpos($value_config, ','."Review Documents".',') !== FALSE) {
        echo '<th>Review Documents</th>';
    }
    if (strpos($value_config, ','."Review Links".',') !== FALSE) {
        echo '<th>Review Links</th>';
    }
    if (strpos($value_config, ','."Description".',') !== FALSE) {
        echo '<th>Description</th>';
    }

    if (strpos($value_config, ','."General description".',') !== FALSE) {
        echo '<th>General description</th>';
    }
    if (strpos($value_config, ','."Fabrication".',') !== FALSE) {
        echo '<th>Fabrication</th>';
    }
    if (strpos($value_config, ','."Paint".',') !== FALSE) {
        echo '<th>Paint</th>';
    }
    if (strpos($value_config, ','."Structure".',') !== FALSE) {
        echo '<th>Structure</th>';
    }
    if (strpos($value_config, ','."Rigging".',') !== FALSE) {
        echo '<th>Rigging</th>';
    }
    if (strpos($value_config, ','."Sandblast".',') !== FALSE) {
        echo '<th>Sandblast</th>';
    }
    if (strpos($value_config, ','."Primer".',') !== FALSE) {
        echo '<th>Primer</th>';
    }
    if (strpos($value_config, ','."Foam".',') !== FALSE) {
        echo '<th>Foam</th>';
    }
    if (strpos($value_config, ','."Rockguard".',') !== FALSE) {
        echo '<th>Rockguard</th>';
    }

    if (strpos($value_config, ','."Notes".',') !== FALSE) {
        echo '<th>Notes</th>';
    }
    if (strpos($value_config, ','."Status".',') !== FALSE) {
        echo '<th>Status</th>';
    }
    if (strpos($value_config, ','."Doing Start and End Date".',') !== FALSE) {
        echo '<th>Doing Start and End Date</th>';
    }
    if (strpos($value_config, ','."Internal QA Date".',') !== FALSE) {
        echo '<th>Internal QA Date</th>';
    }
    if (strpos($value_config, ','."Client QA/Deliverable Date".',') !== FALSE) {
        echo '<th>Client QA/Deliverable Date</th>';
    }
    if (strpos($value_config, ','."Doing Assign To".',') !== FALSE) {
        echo '<th>Doing Assign To</th>';
    }
    if (strpos($value_config, ','."Internal QA Assign To".',') !== FALSE) {
        echo '<th>Internal QA Assign To</th>';
    }
    if (strpos($value_config, ','."Client QA/Deliverable Assign To".',') !== FALSE) {
        echo '<th>Client QA/Deliverable Assign To</th>';
    }
    if (strpos($value_config, ','."TO DO Date".',') !== FALSE) {
        echo '<th>TO DO Date</th>';
    }
    if (strpos($value_config, ','."Deliverable Date".',') !== FALSE) {
        echo '<th>Deliverable Date</th>';
    }
	if (strpos($value_config, ','."Effective Date".',') !== FALSE) {
        echo '<th>Effective Date</th>';
    }
    if (strpos($value_config, ','."Estimated Time to Complete Work".',') !== FALSE) {
        echo '<th>Estimated Time to Complete Work</th>';
    }

    if (strpos($value_config, ','."Project Summary".',') !== FALSE) {
        echo '<th>Project Summary</th>';
    }
    if (strpos($value_config, ','."Review PDF".',') !== FALSE) {
        echo '<th>Review PDF</th>';
    }
    if (strpos($value_config, ','."Front/Last Pages".',') !== FALSE) {
        echo '<th>Front/Last Pages</th>';
    }

    if (strpos($value_config, ','."Generate PDF".',') !== FALSE) {
        echo '<th>Generate PDF</th>';
    }
        echo '<th>Function</th>';
	if (strpos($value_config, ','."History".',') !== FALSE) {
        echo '<th>History</th>';
    }
        echo "</tr></thead>";
    } else {
        echo "<h2>No Record Found.</h2>";
    }

    while($row = mysqli_fetch_array( $result ))
{

        $projectmanageid = $row['projectmanageid'];
		/*if($workorder != 0){
			$query_detail="SELECT * FROM project_manage_detail WHERE projectmanageid=$projectmanageid AND detail_workorder = $workorder";
		}else{
			$query_detail = "SELECT * FROM project_manage_detail WHERE projectmanageid=$projectmanageid";
		}
		$result_detail = mysqli_query($dbc, $query_detail);
		$project_manage_detail = mysqli_fetch_assoc($result_detail);
		$num_rows_project_manage_detail = mysqli_num_rows($result_detail);*/
		//$project_manage_budget =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	project_manage_budget WHERE	projectmanageid='$projectmanageid'"));


		if (strpos($value_config, ','."Work Order".',') !== FALSE) {
            //<a href=\'add_project_manage.php?projectmanageid='.$row['projectmanageid'].'\'>#'.$row['unique_id'].'</a>
            echo '<td data-title="Notes"><a href="add_project_manage.php?projectmanageid='.$row['projectmanageid'].'&tile='.$tile.'&tab='.$tab.'&display_type=view">#'.$row['unique_id'].'</a></td>';
        }
        if (strpos($value_config, ','."Business".',') !== FALSE) {
            echo '<td data-title="Notes">' . get_contact($dbc, $row['businessid'], 'name').'<br>'.get_staff($dbc, $row['contactid']) . '</td>';
        }
		//if (strpos($value_config, ','."Contact".',') !== FALSE) {
        //    echo '<td data-title="Notes">' .get_staff($dbc, $row['contactid']) . '</td>';
        //}
		if (strpos($value_config, ','."Total Project Budget".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['detail_total_project_budget'] . '</td>';
        }
        if (strpos($value_config, ','."Rate Card".',') !== FALSE) {
            echo '<td data-title="Notes">' . get_rate_card($dbc, $row['ratecardid'], 'rate_card_name') . '</td>';
        }
        if (strpos($value_config, ','."Short Name".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['short_name'] . '</td>';
        }
        if (strpos($value_config, ','."Piece Work".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['piece_work'] . '</td>';
        }
        if (strpos($value_config, ','."Heading".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['heading'] . '</td>';
        }
        if (strpos($value_config, ','."Location".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['location'] . '</td>';
        }
        if (strpos($value_config, ','."Job number".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['job_number'] . '</td>';
        }
        if (strpos($value_config, ','."Customer PO/AFE#".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['afe_number'] . '</td>';
        }

        if (strpos($value_config, ','."Staff(Assign To)".',') !== FALSE) {
            echo '<td data-title="Notes">' . get_multiple_contact($dbc, $row['assign_to']) . '</td>';
        }
        if (strpos($value_config, ','."Created Date".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['created_date'] . '</td>';
        }
        if (strpos($value_config, ','."Start Date".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['start_date'] . '</td>';
        }
        if (strpos($value_config, ','."Estimated Completion Date".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['estimated_completion_date'] . '</td>';
        }
        if (strpos($value_config, ','."Task Start Date".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['task_start_date'] . '</td>';
        }
        if (strpos($value_config, ','."Time Clock Start Date".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['time_clock_start_date'] . '</td>';
        }
        if (strpos($value_config, ','."Work performed".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['work_performed_date'] . '</td>';
        }
        if (strpos($value_config, ','."Path".',') !== FALSE) {
            echo '<td data-title="Notes">' . get_project_path_milestone($dbc, $row['project_path'], 'project_path') . '</td>';
        }
        if (strpos($value_config, ','."Milestone & Timeline".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['milestone_timeline'] . '</td>';
        }
        if (strpos($value_config, ','."Service Type".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['service_type'] . '</td>';
        }
        if (strpos($value_config, ','."Service Category".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['service_category'] . '</td>';
        }
        if (strpos($value_config, ','."Service Heading".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['service_heading'] . '</td>';
        }

        if (strpos($value_config, ','."Support Documents".',') !== FALSE) {
            echo '<td data-title="Schedule">';
            $doc1 = mysqli_query($dbc, "SELECT * FROM project_manage_document_link WHERE projectmanageid='$projectmanageid' AND type='Support Document' ORDER BY doclinkid DESC");
            while($row_doc1 = mysqli_fetch_array($doc1)) {
                echo '-<a href="download/'.$row_doc1['document'].'" target="_blank">'.$row_doc1['document'].'</a><br>';
            }
            echo '</td>';
        }
        if (strpos($value_config, ','."Support Links".',') !== FALSE) {
            echo '<td data-title="Schedule">';
            $doc2 = mysqli_query($dbc, "SELECT * FROM project_manage_document_link WHERE projectmanageid='$projectmanageid' AND type='Support Link' ORDER BY doclinkid DESC");
            while($row_doc2 = mysqli_fetch_array($doc2)) {
                echo '-<a target="_blank" href=\''.$row_doc2['link'].'\'">Link</a><br>';
            }
            echo '</td>';
        }
        if (strpos($value_config, ','."Review Documents".',') !== FALSE) {
            echo '<td data-title="Schedule">';
            $doc3 = mysqli_query($dbc, "SELECT * FROM project_manage_document_link WHERE projectmanageid='$projectmanageid' AND type='Review Document' ORDER BY doclinkid DESC");
            while($row_doc3 = mysqli_fetch_array($doc3)) {
                echo '-<a href="download/'.$row_doc3['document'].'" target="_blank">'.$row_doc3['document'].'</a><br>';
            }
            echo '</td>';
        }
        if (strpos($value_config, ','."Review Links".',') !== FALSE) {
            echo '<td data-title="Schedule">';
            $doc4 = mysqli_query($dbc, "SELECT * FROM project_manage_document_link WHERE projectmanageid='$projectmanageid' AND type='Review Link' ORDER BY doclinkid DESC");
            while($row_doc4 = mysqli_fetch_array($doc4)) {
                echo '-<a target="_blank" href=\''.$row_doc4['link'].'\'">Link</a><br>';
            }
            echo '</td>';
        }
        if (strpos($value_config, ','."Description".',') !== FALSE) {
            //echo '<td data-title="Quote Description">' . html_entity_decode($row['description']) . '</td>';
			echo '<td data-title="Description"><span class="iframe_open_description" id="'.$row['projectmanageid'].'" style="cursor:pointer">View</span></td>';
        }

        if (strpos($value_config, ','."General description".',') !== FALSE) {
            echo '<td data-title="Quote Description">' . html_entity_decode($row['general_description']) . '</td>';
        }
        if (strpos($value_config, ','."Fabrication".',') !== FALSE) {
            echo '<td data-title="Quote Description">' . html_entity_decode($row['fabrication']) . '</td>';
        }
        if (strpos($value_config, ','."Paint".',') !== FALSE) {
            echo '<td data-title="Quote Description">' . html_entity_decode($row['paint']) . '</td>';
        }
        if (strpos($value_config, ','."Structure".',') !== FALSE) {
            echo '<td data-title="Quote Description">' . html_entity_decode($row['structure']) . '</td>';
        }
        if (strpos($value_config, ','."Rigging".',') !== FALSE) {
            echo '<td data-title="Quote Description">' . html_entity_decode($row['rigging']) . '</td>';
        }
        if (strpos($value_config, ','."Sandblast".',') !== FALSE) {
            echo '<td data-title="Quote Description">' . html_entity_decode($row['sandblast']) . '</td>';
        }
        if (strpos($value_config, ','."Primer".',') !== FALSE) {
            echo '<td data-title="Quote Description">' . html_entity_decode($row['primer']) . '</td>';
        }
        if (strpos($value_config, ','."Foam".',') !== FALSE) {
            echo '<td data-title="Quote Description">' . html_entity_decode($row['foam']) . '</td>';
        }
        if (strpos($value_config, ','."Rockguard".',') !== FALSE) {
            echo '<td data-title="Quote Description">' . html_entity_decode($row['rockguard']) . '</td>';
        }

        if (strpos($value_config, ','."Notes".',') !== FALSE) {
            echo '<td data-title="Quote Description">' . html_entity_decode($row['notes']) . '</td>';
        }

        if (strpos($value_config, ','."Status".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['status'] . '</td>';
        }
        if (strpos($value_config, ','."Doing Start and End Date".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['doing_start_date'].' - '.$row['doing_end_date'] . '</td>';
        }
        if (strpos($value_config, ','."Internal QA Date".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['internal_qa_date'] . '</td>';
        }
        if (strpos($value_config, ','."Client QA/Deliverable Date".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['client_qa_date'] . '</td>';
        }
        if (strpos($value_config, ','."Doing Assign To".',') !== FALSE) {
            echo '<td data-title="Notes">' . get_staff($dbc, $row['doing_assign_to']) . '</td>';
        }
        if (strpos($value_config, ','."Internal QA Assign To".',') !== FALSE) {
            echo '<td data-title="Notes">' . get_staff($dbc, $row['internal_qa_assign_to']) . '</td>';
        }
        if (strpos($value_config, ','."Client QA/Deliverable Assign To".',') !== FALSE) {
            echo '<td data-title="Client QA/Deliverable Assign To">' . get_staff($dbc, $row['client_qa_assign_to']) . '</td>';
        }
        if (strpos($value_config, ','."TO DO Date".',') !== FALSE) {
            echo '<td data-title="TO DO Date">' . $row['to_do_date'] . '</td>';
        }
        if (strpos($value_config, ','."Deliverable Date".',') !== FALSE) {
            echo '<td data-title="Deliverable Date">' . $row['deliverable_date'] . '</td>';
        }
		if (strpos($value_config, ','."Effective Date".',') !== FALSE) {
            echo '<td data-title="Notes">' . $row['effective_date'] . '</td>';
        }
        if (strpos($value_config, ','."Estimated Time to Complete Work".',') !== FALSE) {
            echo '<td data-title="Estimated Time to Complete Work">' . $row['start_time'] . '</td>';
        }

        if (strpos($value_config, ','."Project Summary".',') !== FALSE) {
            echo '<td data-title="Project Summary">';
            echo '<a href=\'edit_estimate.php?projectmanageid='.$row['projectmanageid'].'&type=profit_loss&tile='.$row['tile'].'&tab='.$row['tab'].'\'>View</a>';
            echo '</td>';
        }
        if (strpos($value_config, ','."Review PDF".',') !== FALSE) {
            echo '<td data-title="Review PDF">';
            echo '<a href=\'edit_estimate.php?projectmanageid='.$row['projectmanageid'].'&type=summary&tile='.$row['tile'].'&tab='.$row['tab'].'\'>Summary</a>';
            echo '</td>';
        }
        if (strpos($value_config, ','."Front/Last Pages".',') !== FALSE) {
            echo '<td data-title="Review PDF">';
            echo '<a href=\'quote_front_page.php?projectmanageid='.$row['projectmanageid'].'&tile='.$row['tile'].'&tab='.$row['tab'].'\'>Front/Last Pages</a>';
            echo '</td>';
        }

        if (strpos($value_config, ','."Generate PDF".',') !== FALSE) {
            echo '<td data-title="Review PDF">';
            echo '<a href=\'generate_pdf.php?projectmanageid='.$row['projectmanageid'].'&tile='.$row['tile'].'&tab='.$row['tab'].'\'>Generate PDF Files</a>';
			$ent_pdf = '';
			$other_pdf = '';
			$filename = 'download/pdf_fabrication_'.$row['projectmanageid'].'.pdf';
			if(file_exists($filename)) {
				$other_pdf .= '<br /><a target="_blank" href="'.$filename.'">Fabrication Order<img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a>';
			}
			$filename = 'download/pdf_paint_'.$row['projectmanageid'].'.pdf';
			if(file_exists($filename)) {
				$other_pdf .= '<br /><a target="_blank" href="'.$filename.'">Paint Order<img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a>';
			}
			$filename = 'download/pdf_rigging_'.$row['projectmanageid'].'.pdf';
			if(file_exists($filename)) {
				$other_pdf .= '<br /><a target="_blank" href="'.$filename.'">Rigging Order<img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a>';
			}
			$filename = 'download/pdf_structure_'.$row['projectmanageid'].'.pdf';
			if(file_exists($filename)) {
				$other_pdf .= '<br /><a target="_blank" href="'.$filename.'">Structure Order<img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a>';
			}
			
			$filename = 'download/pdf_'.$row['projectmanageid'].'.pdf';
			if(file_exists($filename)) {
				$ent_pdf = '<br /><a target="_blank" href="'.$filename.'">'.($other_pdf != '' ? 'Entire Order' : 'PDF '.$row['projectmanageid']).'<img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a>';
			}
            echo $ent_pdf.$other_pdf.'</td>';
        }

        echo '<td data-title="Function">';
        $contactid_timer = $_SESSION['contactid'];
        $get_timer = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT timer_type FROM project_manage_assign_to_timer WHERE projectmanageid='$projectmanageid' AND created_by='$contactid_timer' AND DATE(NOW()) = DATE(created_date) AND end_time IS NULL"));
        if($get_timer['timer_type'] == 'Work') {
            $timer = '#start_timer';
        } else if($get_timer['timer_type'] == 'Break') {
            $timer = '#break_timer';
        } else {
            $timer = '';
        }

        //if(vuaed_visible_function($dbc, 'project_manage') == 1) {
			echo '<a href="add_project_manage.php?tile=Shop Work Orders&tab=Shop Time Sheets&projectmanageid='.$row['projectmanageid'].'&tab_from_tile_view=Shop Work Order">Add Time</a>';
            echo ' | <a href="add_project_manage.php?projectmanageid='.$row['projectmanageid'].$timer.'&tile='.$tile.'&tab='.$tab.'">Edit</a>';
			echo ' | <a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&projectmanageid='.$row['projectmanageid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';

            if (strpos($value_config, ','."Email Approval".',') !== FALSE) {
                    //echo '<a onclick="approvebutton(this)" id="'.$row['projectmanageid'].'" href=\'project_workflow_dashboard.php?projectmanageid='.$row['projectmanageid'].'&ar_type=Approved&tile='.$row['tile'].'&tab='.$row['tab'].'\'>Approve</a> | ';

                    echo ' | <span class="open-approval" onclick="approvebutton(this)" id="'.$row['projectmanageid'].'">Email for Approval</span>';
            }

            if (strpos($value_config, ','."Reject".',') !== FALSE && $approvals > 0) {
                    echo ' | <a href=\'project_workflow_dashboard.php?projectmanageid='.$row['projectmanageid'].'&ar_type=Rejected&tile='.$row['tile'].'&tab='.$row['tab'].'\' onclick="return confirm(\'Are you sure you wish to Reject?\')">Reject</a>';
            }

            if (strpos($value_config, ','."Approve".',') !== FALSE && $approvals > 0) {
                    echo ' | <a href=\'project_workflow_dashboard.php?projectmanageid='.$row['projectmanageid'].'&ar_type=Approved&tile='.$row['tile'].'&tab='.$row['tab'].'\'>Approve</a>';
            }

            ?>
            <input type='hidden' class='getemailsapproval' value='' name='getemailsapproval'>

            <div class="approve-box-<?php echo $row['projectmanageid']; ?> approve-box">Please enter the email(s) (separated by a comma) you would like to send Approval.<br><br>
            <input type='text' style='max-width:300px;' name='' placeholder='email1@example.com,email2@example.com' class='form-control getemailsapproval2'><br><br>
            <button type='submit' name='send_wo_approve' class='btn brand-btn sendemailapprovalsubmit' value='<?php echo $row['unique_id']; ?>'>Send</button>
            <button onClick="hide-box" value="<?php echo $row['projectmanageid']; ?>" type='button' name='send_drive_logs' class='btn brand-btn send_cancel'>Cancel</button>
            </div>

            <?php

    		if (strpos($value_config, ','."Delete".',') !== FALSE) {
                echo ' | <a href=\'project_workflow_dashboard.php?projectmanageid='.$row['projectmanageid'].'&ar_type=delete&tile='.$row['tile'].'&tab='.$row['tab'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a>';
            }
        //}
        echo '</td>';
		if (strpos($value_config, ','."History".',') !== FALSE) {
			echo '<td data-title="History"><span class="iframe_open" id="'.$row['projectmanageid'].'" style="cursor:pointer">View All</span></td>';
		}
		echo "</tr>";
    }
    echo '</table>';

    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);

    //if(vuaed_visible_function($dbc, 'project_manage') == 1) {
    //    echo '<a href="add_project_manage.php?category='.$category.'" class="btn brand-btn mobile-block gap-bottom pull-right">Add Product</a>';
    //}
}
?>
