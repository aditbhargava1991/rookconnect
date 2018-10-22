<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('field_job');
error_reporting(0);

$role = $_SESSION['role'];
$s_employeeid = $_SESSION['contactid'];

if (isset($_POST['upload_doc'])) {
    $fieldpoid = $_POST['upload_doc'];
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
	$docs = htmlspecialchars($_FILES["file"]["name"], ENT_QUOTES);

    move_uploaded_file($_FILES["file"]["tmp_name"], "download/".$_FILES["file"]["name"]);

    $before_change = capture_before_change($dbc, 'field_po', 'vendor_invoice', 'fieldpoid', $fieldpoid);
    $before_change .= capture_before_change($dbc, 'field_po', 'status', 'fieldpoid', $fieldpoid);

    $query_update_bid = "UPDATE `field_po` SET `vendor_invoice` = '$docs', `status` = 'To be Billed' WHERE `fieldpoid` = '$fieldpoid'";
    $result_update_bid = mysqli_query($dbc, $query_update_bid);

    $history = capture_after_change('vendor_invoice', $docs);
    $history .= capture_after_change('status', 'To be Billed');
	  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

    echo '<script type="text/javascript"> window.location.replace("field_jobs.php"); </script>';
}

if((!empty($_GET['workticketid'])) && ($_GET['status'] == 'Approve')) {
	$workticketid = $_GET['workticketid'];
	$today_date = date('Y-m-d');

  $before_change = capture_before_change($dbc, 'field_work_ticket', 'status', 'workticketid', $workticketid);
  $before_change .= capture_before_change($dbc, 'field_work_ticket', 'date_received', 'workticketid', $workticketid);

	$query_update_site = "UPDATE `field_work_ticket` SET `status` = 'Approved', `date_received` = '$today_date' WHERE	`workticketid` = '$workticketid'";
	$result_update_site	= mysqli_query($dbc, $query_update_site);

  $history = capture_after_change('status', 'Approved');
  $history .= capture_after_change('date_received', $today_date);
  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

	echo '<script type="text/javascript"> window.location.replace("field_jobs.php"); </script>';
}

if((!empty($_GET['workticketid'])) && ($_GET['status'] == 'Revert')) {
	$workticketid = $_GET['workticketid'];

  $before_change = capture_before_change($dbc, 'field_work_ticket', 'status', 'workticketid', $workticketid);
  $before_change .= capture_before_change($dbc, 'field_work_ticket', 'date_received', 'workticketid', $workticketid);

	$query_update_site = "UPDATE `field_work_ticket` SET `status` = 'Pending', `date_received` = '' WHERE	`workticketid` = '$workticketid'";
	$result_update_site	= mysqli_query($dbc, $query_update_site);

  $history = capture_after_change('status', 'Pending');
  $history .= capture_after_change('date_received', '');
  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

	echo '<script type="text/javascript"> window.location.replace("field_jobs.php"); </script>';
}

if((!empty($_GET['wtsend'])) && (!empty($_GET['contactid']))) {
	$contactid = $_GET['contactid'];
	$workticketid = $_GET['workticketid'];
	$get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT email FROM contacts WHERE contactid='$contactid'"));

	if($get_contact['email'] != '') {
		$to = $get_contact['email'];
		$from = "highland@highland.com";
		$subject ="Highland Project Work Ticket";
		$message = "Please see attachment for your Work Ticket.";
		$headers = "From: $from";

		// boundary
		$semi_rand = md5(time());
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

		// headers for attachment
		$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

		// multipart boundary
		$message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
		$message .= "--{$mime_boundary}\n";

		$wt_file = 'download/field_work_ticket_'.$workticketid.'.pdf';
		$filename = basename($wt_file);
		$file = fopen($wt_file,"rb");
		$data = fread($file,filesize($wt_file));
		fclose($file);
		$data = chunk_split(base64_encode($data));
		$message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$wt_file\"\n" .
		"Content-Disposition: attachment;\n" . " filename=\"$filename\"\n" .
		"Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
		$message .= "--{$mime_boundary}\n";
		$ok = @mail($to, $subject, $message, $headers);
	}

	echo '<script type="text/javascript"> window.location.replace("field_jobs.php"); </script>';

}

if((!empty($_GET['jobid'])) && (!empty($_GET['action']))) {
	$jobid = $_GET['jobid'];
        $date_of_archival = date('Y-m-d');

  $before_change = capture_before_change($dbc, 'field_jobs', 'deleted', 'jobid', $jobid);
  $before_change .= capture_before_change($dbc, 'field_jobs', 'date_of_archival', 'jobid', $jobid);

  $query_update_job = "UPDATE `field_jobs` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE	`jobid` = '$jobid'";
	$result_update_job	= mysqli_query($dbc, $query_update_job);

  $history = capture_after_change('deleted', '1');
  $history .= capture_after_change('date_of_archival', '');
  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

  $before_change = capture_before_change($dbc, 'field_po', 'deleted', 'jobid', $jobid);
  $before_change .= capture_before_change($dbc, 'field_po', 'date_of_archival', 'jobid', $jobid);

  $query_update_po = "UPDATE `field_po` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE	`jobid` = '$jobid'";
	$result_update_po	= mysqli_query($dbc, $query_update_po);

  $history = capture_after_change('deleted', '1');
  $history .= capture_after_change('date_of_archival', '');
  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

  $before_change = capture_before_change($dbc, 'field_foreman_sheet', 'deleted', 'jobid', $jobid);
  $before_change .= capture_before_change($dbc, 'field_foreman_sheet', 'date_of_archival', 'jobid', $jobid);

  $query_update_fs = "UPDATE `field_foreman_sheet` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE	`jobid` = '$jobid'";
  $result_update_fs	= mysqli_query($dbc, $query_update_fs);

  $history = capture_after_change('deleted', '1');
  $history .= capture_after_change('date_of_archival', '');
  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

  $before_change = capture_before_change($dbc, 'field_invoice', 'deleted', 'jobid', $jobid);
  $before_change .= capture_before_change($dbc, 'field_invoice', 'date_of_archival', 'jobid', $jobid);

  $query_update_in = "UPDATE `field_invoice` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE	`jobid` = '$jobid'";
	$result_update_in	= mysqli_query($dbc, $query_update_in);

  $history = capture_after_change('deleted', '1');
  $history .= capture_after_change('date_of_archival', '');
  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

	header('Location: field_jobs.php');
}

$tab_result = mysqli_fetch_array(mysqli_query($dbc, "select value from general_configuration where name='field_job_tabs'"));
$tab_config = $tab_result['value'];
$dashboard_result = mysqli_fetch_array(mysqli_query($dbc, "select dashboard_list from field_config_field_jobs where tab='foreman'"));
$dashboard_config = $dashboard_result['dashboard_list'];
$edit_result = mysqli_fetch_array(mysqli_query($dbc, "select field_list from field_config_field_jobs where tab='foreman'"));
$edit_config = $edit_result['field_list'];
if(str_replace(',','',$dashboard_config) == '') {
	$dashboard_config = ',job,crew,';
}
if(str_replace(',','',$edit_config) == '') {
	$edit_config = ',job,date,afe,additional,site,description,crew_name,crew_pos,crew_reg,crew_ot,crew_travel,crew_sub,equipment,stock_desc,stock_qty,stock_price,stock_amount,comments,';
}
?>
<script src="/js/jquery.cookie.js"></script>
<script type="text/javascript">
function actionDate(sel) {
	var action = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "field_job_ajax_all.php?from=field_jobs_wt&action=actiondate&id="+arr[1]+'&value='+action,
		dataType: "html",   //expect html to be returned
		success: function(response){
            alert('Date Sent Success.');
			location.reload();
		}
	});
}
</script>
</head>
<body>

<?php include ('../navigation.php');
?>

<div class="container">
<div class="row">

<div class="col-md-12">
    	<!-- Admin -->
        <h1 class="single-pad-bottom">Field Jobs Dashboard<?php
			if(config_visible_function($dbc, 'field_jobs') == 1) {
				echo '<a href="config_field_jobs.php?tab=foreman" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><span class="popover-examples list-inline"><a class="pull-right" style="margin:-5px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span><br><br>';
			} ?></h1>
        <?php if (strpos($tab_config,',sites,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'sites' ) === true) { ?>
			<a href='field_sites.php'><button type="button" class="btn brand-btn mobile-block" >Sites</button></a>
        <?php } ?>
        <?php if (strpos($tab_config,',jobs,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'jobs' ) === true) { ?>
			<a href='field_jobs.php'><button type="button" class="btn brand-btn mobile-block" >Jobs</button></a>
        <?php } ?>
        <?php if (strpos($tab_config,',foreman,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'foreman_sheet' ) === true) { ?>
			<a href='field_foreman_sheet.php'><button type="button" class="btn brand-btn mobile-block active_tab" >Foreman Sheet</button></a>
        <?php } ?>
        <?php if (strpos($tab_config,',po,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'po' ) === true) { ?>
			<a href='field_po.php'><button type="button" class="btn brand-btn mobile-block" >PO</button></a>
        <?php } ?>
        <?php if (strpos($tab_config,',work,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'work_ticket' ) === true) { ?>
			<a href='field_work_ticket.php'><button type="button" class="btn brand-btn mobile-block" >Work Ticket</button></a>
        <?php } ?>
        <?php if (strpos($tab_config,',invoice,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'invoices' ) === true) { ?>
            <a href='field_invoice.php?paytype=Unpaid'><button type="button" class="btn brand-btn mobile-block" >Outstanding Invoices</button></a>
            <a href='field_invoice.php?paytype=Paid'><button type="button" class="btn brand-btn mobile-block" >Paid Invoices</button></a>
        <?php } ?>
        <?php if (strpos($tab_config,',payroll,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'payroll' ) === true) { ?>
			<a href='field_payroll.php'><button type="button" class="btn brand-btn mobile-block" >Payroll</button></a>
        <?php } ?>
<br><br>
<form name="form_jobs" enctype="multipart/form-data" method="post" action="" class="form-inline" role="form">

    <?php if(search_visible_function($dbc, 'field_job') == 0) {  } else {?>
    <center>
        <div class="form-group">
            <label for="site_name" class="col-sm-5 control-label">Job#/Customer:</label>
            <div class="col-sm-6">
                <?php if(isset($_POST['search_fs_submit'])) { ?>
                    <input type="text" name="search_fs" value="<?php echo $_POST['search_fs']?>" class="form-control">
                <?php } else { ?>
                    <input type="text" name="search_fs" class="form-control">
                <?php } ?>
            </div>
        </div>
        &nbsp;
            <button type="submit" name="search_fs_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
            <button type="submit" name="display_all_fs" value="Display All" class="btn brand-btn mobile-block">Display Current</button>
    </center>
    <?php } ?>


       <?php
        echo '<a href="add_field_foreman_sheet.php" class="btn brand-btn pull-right">Add Foreman Sheet</a>';
        echo '<a href="add_field_foreman_sheet.php?pdf_foreman_sheet=new" class="btn brand-btn pull-right">Blank Foreman Sheet PDF</a>';
        ?>

    <div id="no-more-tables">
        <?php
        // Display Pager

        $rowsPerPage = ITEMS_PER_PAGE;
        $pageNum = 1;

        if(isset($_GET['page'])) {
            $pageNum = $_GET['page'];
        }

        $offset = ($pageNum - 1) * $rowsPerPage;

        $fs_name_search = '';
        if (isset($_POST['search_fs_submit'])) {
            $fs_name_search = $_POST['search_fs'];
        }
        if (isset($_POST['display_all_fs'])) {
            $fs_name_search = '';
        }

        if(search_visible_function($dbc, 'field_job') == 0) {
            $query_check_credentials = "SELECT fj.*, cl.contactid, cl.name, fs.*  FROM contacts cl, field_jobs fj, field_foreman_sheet fs WHERE fs.deleted = 0 AND (fj.`invoice` != 'Flat Rate' OR fs.office_status = 'Pending' OR fs.office_status IS NULL OR fs.supervisor_status IS NULL) AND fs.jobid = fj.jobid AND fj.clientid = cl.contactid AND fj.foremanid LIKE '%,".$s_employeeid.",%' ORDER BY fsid DESC LIMIT $offset, $rowsPerPage";
            $query   = "SELECT COUNT(fsid) AS numrows FROM contacts cl, field_jobs fj, field_foreman_sheet fs WHERE fs.deleted = 0 AND (fj.`invoice` != 'Flat Rate' OR fs.office_status = 'Pending' OR fs.office_status IS NULL OR fs.supervisor_status IS NULL) AND fs.jobid = fj.jobid AND fj.clientid = cl.contactid AND fj.foremanid LIKE '%,".$s_employeeid.",%' ORDER BY fsid DESC";
        } else {
            if($fs_name_search != '') {
				$client_list = search_contacts_table($dbc, $fs_name_search, " AND `contactid` IN (SELECT `clientid` FROM `field_jobs`) ", 'NAME');
				$contact_last = search_contacts_table($dbc, $fs_name_search, " AND `contactid` IN (SELECT `contactid` FROM `field_jobs`) ", 'LAST');
				$contact_first = search_contacts_table($dbc, $fs_name_search, " AND `contactid` IN (SELECT `contactid` FROM `field_jobs`) ", 'FIRST');
                $query_check_credentials = "SELECT fj.*, fs.*, fj.contactid AS fj_contactid FROM field_jobs fj, field_foreman_sheet fs WHERE fs.jobid = fj.jobid AND (fj.job_number LIKE '%$fs_name_search%' OR fj.clientid IN ($client_list) OR fj.contactid IN ($contact_last) OR fj.contactid IN ($contact_first)) ORDER BY fs.fsid DESC";
                $query = "SELECT COUNT(*) numrows FROM field_jobs fj, field_foreman_sheet fs WHERE fs.jobid = fj.jobid AND (fj.job_number LIKE '%$fs_name_search%' OR fj.clientid IN ($client_list) OR fj.contactid IN ($contact_last) OR fj.contactid IN ($contact_first))";
            } else {
                $query_check_credentials = "SELECT fj.*, fs.*, fj.contactid AS fj_contactid FROM contacts cl, field_jobs fj, field_foreman_sheet fs WHERE fs.deleted = 0 AND (fj.`invoice` != 'Flat Rate' OR 'Pending' IN (IFNULL(fs.`office_status`,'Pending'),IFNULL(fs.`supervisor_status`,'Pending'))) AND fs.jobid = fj.jobid AND fj.clientid = cl.contactid ORDER BY fsid DESC LIMIT $offset, $rowsPerPage";
                $query   = "SELECT COUNT(fs.fsid) AS numrows FROM field_foreman_sheet fs LEFT JOIN `field_jobs` fj ON fs.jobid=fj.jobid WHERE fs.deleted = 0 AND (fj.`invoice` != 'Flat Rate' OR 'Pending' IN (IFNULL(fs.`office_status`,'Pending'),IFNULL(fs.`supervisor_status`,'Pending')))";
            }
        }

        if($fs_name_search == '') {
        // how many rows we have in database
            //$query   = "SELECT COUNT(fsid) AS numrows FROM field_foreman_sheet WHERE deleted = 0";
        }

        if($fs_name_search == '') {
            echo '<h1 class="single-pad-bottom">'.display_pagination($dbc, $query, $pageNum, $rowsPerPage).'</h1>';
        }
        $result = mysqli_query($dbc, $query_check_credentials);

        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
        echo "<table class='table table-bordered table-striped'>";
        echo "<thead><tr class='hidden-xs hidden-sm'>
                ".(strpos($dashboard_config,',job,') !== false ? "<th>Job#</th>" : "")."
                ".(strpos($dashboard_config,',date,') !== false ? "<th>Date</th>" : "")."
                ".(strpos($dashboard_config,',contact,') !== false ? "<th>Contact</th>" : "")."
                ".(strpos($dashboard_config,',crew') !== false ? "<th>Crew Info<br><em>(".
					(strpos($edit_config,',crew_name,') !== false ? "Name" : "").
					(strpos($edit_config,',crew_pos,') !== false ? " - Position" : "").
					(strpos($edit_config,',crew_reg,') !== false ? " - Reg Hour" : "").
					(strpos($edit_config,',crew_ot,') !== false ? " - OT Hour" : "").
					(strpos($edit_config,',crew_travel,') !== false ? " - Travel" : "").")</em></th>" : "")."
				<th>Function</th>
				<th>Supervisor Process</th>
				<th>Office Process</th>
				<th>Work Ticket</th>";
                echo "</tr></thead>";
        } else {
            echo "<h2>No Record Found.</h2>";
        }
        while($row = mysqli_fetch_array( $result ))
        {
            $jobid = $row['jobid'];
            $fsid = $row['fsid'];
            $result_wt = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(workticketid) AS total_wt, workticketid FROM field_work_ticket WHERE jobid='$jobid' AND fsid='$fsid'"));

            echo '<tr>';
            //echo '<td data-title="Job#"><a href=\'add_field_job.php?jobid='.$jobid.'\'>' . $row['job_number'] . '</a></td>';

			if(strpos($dashboard_config,',job') !== false) {
				echo '<td data-title="Job#">' . $row['job_number'] . '</td>';
			}

			if(strpos($dashboard_config,',date') !== false) {
				echo '<td data-title="Date">' . $row['today_date']. '</td>';
			}


			if(strpos($dashboard_config,',contact') !== false) {
			    echo '<td data-title="Contact">'.get_client($dbc, $row['clientid']).'<br>'.get_staff($dbc, $row['fj_contactid']).'</td>';
			}

			if(strpos($dashboard_config,',crew') !== false) {
				$contactid = $row['contactid'];

				$total_count = mb_substr_count($row['contactid'],',');
				$contactid = explode(',',$row['contactid']);
				$positionname = explode(',',$row['positionname']);
				$crew_reg_hour = explode(',',$row['crew_reg_hour']);
				$crew_ot_hour = explode(',',$row['crew_ot_hour']);
				$crew_travel = explode(',',$row['crew_travel_hour']);

				$crew = '';
				for($emp_loop=0; $emp_loop<=$total_count; $emp_loop++) {
					if($contactid[$emp_loop] != '') {
						$crew .= (strpos($edit_config,',crew_name,') !== false ? get_staff($dbc, $contactid[$emp_loop]) : '');
						$crew .= ' - '.get_positions($dbc, $positionname[$emp_loop], 'name');
						if(isset($crew_reg_hour[$emp_loop])) {
							$crew .= (strpos($edit_config,',crew_reg,') !== false ? ' - ' . $crew_reg_hour[$emp_loop] : '');
						}
						if(isset($crew_ot_hour[$emp_loop])) {
							$crew .= (strpos($edit_config,',crew_ot,') !== false ? ' - ' . $crew_ot_hour[$emp_loop] : '');
						}
						if(isset($crew_travel[$emp_loop])) {
							$crew .= (strpos($edit_config,',crew_travel,') !== false ? ' - ' . $crew_travel[$emp_loop] : '');
						}
						$crew .= '<br>';
					}
				}
				echo '<td data-title="Crew Info">' . $crew . '</td>';
			}

            //Function
            echo '<td data-title="Function">';

            if($result_wt['total_wt'] != 0) {
                if(approval_visible_function($dbc, 'field_job') == 1) {
                    echo '<a href=\'add_field_foreman_sheet.php?jobid='.$row['jobid'].'&fsid='.$row['fsid'].'\'>Edit</a>';
                }
            } else {
                echo '<a href=\'add_field_foreman_sheet.php?jobid='.$row['jobid'].'&fsid='.$row['fsid'].'\'>Edit</a>';
            }

            // echo '<a href=\'../delete_restore.php?action=delete&subtab=foreman&fsid='.$row['fsid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
            echo '</td>';

            if($row['deleted'] == 1) {
				echo '<td>Archived</td>';
			}
            else if($row['supervisor_status'] == 'Pending') {
            	echo '<td><a href=\'add_field_foreman_sheet.php?jobid='.$row['jobid'].'&fsid='.$row['fsid'].'\'>'.$row['supervisor_status'].'</a></td>';
            } else {
                //Supervisor
                echo '<td data-title="Supervisor Process">';
                echo $row['supervisor_status'];
                echo '</td>';
            }

			if($row['deleted'] == 1) {
				echo '<td>Archived</td>';
			}
            else if($row['office_status'] == 'Pending') {
            	echo '<td><a href=\'add_field_foreman_sheet.php?jobid='.$row['jobid'].'&fsid='.$row['fsid'].'\'>'.$row['office_status'].'</a></td>';
            } else {
                //Supervisor
                echo '<td data-title="Office Process">';
                echo $row['office_status'];
                echo '</td>';
            }

            //Comment
            $fsid = $row['fsid'];
            $get_fsid =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT (LENGTH(comment) - LENGTH(REPLACE(comment, '##', '')))/ LENGTH('##') AS `occurrences` FROM field_foreman_sheet WHERE fsid='$fsid'"));

            //echo '<td data-title="Comment">';
            //echo '<a href=\'add_field_foreman_sheet.php?status=comment&fsid='.$row['fsid'].'\'>'.round($get_fsid['occurrences']).'</a>';
            //echo '</td>';

            // Work ticket
			echo '<td data-title="Work Ticket">';
			if($row['office_status'] == 'Approved' && $row['invoice'] != 'Flat Rate' && check_subtab_persmission( $dbc, 'field_job', ROLE, 'work_ticket' ) === true) {
				if($result_wt['total_wt'] == 0) {
					echo '<a href=\'add_field_work_ticket.php?jobid='.$row['jobid'].'&fsid='.$row['fsid'].'\'>Create</a>';
				} else {
					$name_of_file = 'download/field_work_ticket_'.$result_wt['workticketid'].'.pdf';
					echo '<a href='.$name_of_file.' target="_blank">#'.$result_wt['workticketid'].' <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a>';
				}
			} else if($row['invoice'] == 'Flat Rate') {
				echo 'Flat Rate Job';
			} else {
				echo '-';
			}

			echo '</td>';

            echo "</tr>";
        }

        echo '</table></div>';
        echo '<a href="add_field_foreman_sheet.php" class="btn brand-btn pull-right">Add Foreman Sheet</a>';
        echo '<a href="add_field_foreman_sheet.php?pdf_foreman_sheet=new" class="btn brand-btn pull-right">Blank Foreman Sheet PDF</a>';

        if($fs_name_search == '') {
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
        }

        ?>


</form>

</div>
</div>
</div>
<?php include ('../footer.php'); ?>
