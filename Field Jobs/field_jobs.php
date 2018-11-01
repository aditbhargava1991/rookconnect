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

    echo '<script type="text/javascript"> window.location.replace("field_po.php"); </script>';
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

	echo '<script type="text/javascript"> window.location.replace("field_work_ticket.php"); </script>';
}

if((!empty($_GET['workticketid'])) && ($_GET['status'] == 'Revert')) {
	$workticketid = $_GET['workticketid'];
  $before_change = capture_before_change($dbc, 'field_work_ticket', 'status', 'workticketid', $workticketid);
  $before_change .= capture_before_change($dbc, 'field_work_ticket', 'date_received', 'workticketid', $workticketid);

	$query_update_site = "UPDATE `field_work_ticket` SET `status` = 'Pending', `date_received` = '' WHERE	`workticketid` = '$workticketid'";
	$result_update_site	= mysqli_query($dbc, $query_update_site);

  $history = capture_after_change('status', 'Pending');
  $history .= capture_after_change('date_received', '');

	echo '<script type="text/javascript"> window.location.replace("field_work_ticket.php"); </script>';
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
  $history .= capture_after_change('date_of_archival', $date_of_archival);

  $before_change = capture_before_change($dbc, 'field_po', 'deleted', 'jobid', $jobid);
  $before_change .= capture_before_change($dbc, 'field_po', 'date_of_archival', 'jobid', $jobid);

  $query_update_po = "UPDATE `field_po` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE	`jobid` = '$jobid'";
	$result_update_po	= mysqli_query($dbc, $query_update_po);

  $history = capture_after_change('deleted', '1');
  $history .= capture_after_change('date_of_archival', $date_of_archival);

  $before_change = capture_before_change($dbc, 'field_foreman_sheet', 'deleted', 'jobid', $jobid);
  $before_change .= capture_before_change($dbc, 'field_foreman_sheet', 'date_of_archival', 'jobid', $jobid);

  $query_update_fs = "UPDATE `field_foreman_sheet` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE	`jobid` = '$jobid'";
	$result_update_fs	= mysqli_query($dbc, $query_update_fs);

  $history = capture_after_change('deleted', '1');
  $history .= capture_after_change('date_of_archival', $date_of_archival);

  $before_change = capture_before_change($dbc, 'field_invoice', 'deleted', 'jobid', $jobid);
  $before_change .= capture_before_change($dbc, 'field_invoice', 'date_of_archival', 'jobid', $jobid);

  $query_update_in = "UPDATE `field_invoice` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE	`jobid` = '$jobid'";
	$result_update_in	= mysqli_query($dbc, $query_update_in);

  $history = capture_after_change('deleted', '1');
  $history .= capture_after_change('date_of_archival', $date_of_archival);

	header('Location: field_jobs.php');
}

$tab_result = mysqli_fetch_array(mysqli_query($dbc, "select value from general_configuration where name='field_job_tabs'"));
$tab_config = $tab_result['value'];
$dashboard_result = mysqli_fetch_array(mysqli_query($dbc, "select dashboard_list from field_config_field_jobs where tab='jobs'"));
$dashboard_config = $dashboard_result['dashboard_list'];
if(str_replace(',','',$dashboard_config) == '') {
	$dashboard_config = ',job,contact,foreman,';
}
?>
<script src="js/jquery.cookie.js"></script>
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
		if(config_visible_function($dbc, 'field_job') == 1) {
			echo '<a href="config_field_jobs.php?tab=jobs" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><span class="popover-examples list-inline"><a class="pull-right" style="margin:-5px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span><br><br>';
		} ?></h1>
        <?php if (strpos($tab_config,',sites,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'sites' ) === true) { ?>
			<a href='field_sites.php'><button type="button" class="btn brand-btn mobile-block" >Sites</button></a>
        <?php } ?>
        <?php if (strpos($tab_config,',jobs,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'jobs' ) === true) { ?>
			<a href='field_jobs.php'><button type="button" class="btn brand-btn mobile-block active_tab" >Jobs</button></a>
        <?php } ?>
        <?php if (strpos($tab_config,',foreman,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'foreman_sheet' ) === true) { ?>
			<a href='field_foreman_sheet.php'><button type="button" class="btn brand-btn mobile-block" >Foreman Sheet</button></a>
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
                <?php if(isset($_POST['search_job_submit'])) { ?>
                    <input type="text" name="search_job" value="<?php echo $_POST['search_job']?>" class="form-control">
                <?php } else { ?>
                    <input type="text" name="search_job" class="form-control">
                <?php } ?>
            </div>
        </div>
        &nbsp;
            <button type="submit" name="search_job_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
            <button type="submit" name="display_all_job" value="Display All" class="btn brand-btn mobile-block">Display Current</button>
    </center>
    <?php } ?>

        <?php
        if(vuaed_visible_function($dbc, 'field_job') == 1) {
            echo '<a href="add_field_job.php" class="btn brand-btn mobile-block pull-right">Create Job</a>';
        }
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

        $job_name_search = '';
        if (isset($_POST['search_job_submit'])) {
            $job_name_search = $_POST['search_job'];
        }
        if (isset($_POST['display_all_job'])) {
            $job_name_search = '';
        }

        if(search_visible_function($dbc, 'field_job') == 0) {
            $query_check_credentials = "SELECT co.*, cl.contactid, cl.name FROM contacts cl, field_jobs co WHERE co.clientid = cl.contactid AND co.deleted = 0 AND co.foremanid LIKE '%,".$s_employeeid.",%' LIMIT $offset, $rowsPerPage";
            $query   = "SELECT COUNT(jobid) AS numrows FROM contacts cl, field_jobs co WHERE co.clientid = cl.contactid AND co.deleted = 0 AND co.foremanid LIKE '%,".$s_employeeid.",%'";
        } else {
            if($job_name_search != '') {
				$client_list = search_contacts_table($dbc, $job_name_search, " AND `contactid` IN (SELECT `clientid` FROM `field_jobs`) ", 'NAME');
				$contact_last = search_contacts_table($dbc, $job_name_search, " AND `contactid` IN (SELECT `contactid` FROM `field_jobs`) ", 'LAST');
				$contact_first = search_contacts_table($dbc, $job_name_search, " AND `contactid` IN (SELECT `contactid` FROM `field_jobs`) ", 'FIRST');
                $query_check_credentials = "SELECT fj.* FROM field_jobs fj WHERE (fj.job_number LIKE '%$job_name_search%' OR fj.clientid IN ($client_list) OR fj.contactid IN ($contact_last) OR fj.contactid IN ($contact_first)) ORDER BY fj.jobid DESC";
                $query = "SELECT COUNT(fj.*) numrows FROM field_jobs fj WHERE (fj.job_number LIKE '%$job_name_search%' OR fj.clientid IN ($client_list) OR fj.contactid IN ($contact_last) OR fj.contactid IN ($contact_first))";
            } else {
                $query_check_credentials = "SELECT * FROM field_jobs WHERE deleted = 0 ORDER BY job_number DESC LIMIT $offset, $rowsPerPage";
                $query   = "SELECT COUNT(jobid) AS numrows FROM field_jobs WHERE deleted = 0";
            }
        }

        // how many rows we have in database
        //$query   = "SELECT COUNT(jobid) AS numrows FROM field_jobs WHERE deleted = 0";

        if($job_name_search == '') {
            echo '<h1 class="single-pad-bottom">'.display_pagination($dbc, $query, $pageNum, $rowsPerPage).'</h1>';
        }
        $result = mysqli_query($dbc, $query_check_credentials);

        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
        echo "<table class='table table-bordered table-striped'>";
        echo "<thead><tr class='hidden-xs hidden-sm'>
                ".(strpos($dashboard_config,',job,') !== false ? "<th>Job#</th>" : "")."
                ".(strpos($dashboard_config,',contact,') !== false ? "<th>Customer<br>Contact</th>" : "")."
                ".(strpos($dashboard_config,',site,') !== false ? "<th>Site Location</th><th>Description</th>" : "")."
                ".(strpos($dashboard_config,',foreman,') !== false ? "<th>Foreman</th>" : "")."
                <th>Foreman Sheet</th>";
                if(vuaed_visible_function($dbc, 'field_job') == 1) {
                    echo "<th>Work Ticket<br>(Pending | Competed)</th>";
                }
                echo "<th>PO</th>";
                if(vuaed_visible_function($dbc, 'field_job') == 1) {
                    echo "<th>Invoice</th>
                    <th>Function</th>";
                }
                echo "</tr></thead>";
        } else {
            echo "<h2>No Record Found.</h2>";
        }
        while($row = mysqli_fetch_array( $result ))
        {
            $jobid = $row['jobid'];

            echo '<tr>';
            if(strpos($dashboard_config,',job,') !== false) {
				echo '<td data-title="Job#">' . $row['job_number'] . '</td>';
			}

            if(strpos($dashboard_config,',contact,') !== false) {
				echo '<td data-title="Contact">'.get_client($dbc, $row['clientid']).'<br>'.get_staff($dbc, $row['contactid']).'</td>';
			}

            if(strpos($dashboard_config,',site,') !== false) {
				echo '<td data-title="Site Location">' . get_site($dbc, $row['siteid']). '</td>';
                echo '<td data-title="Job#">' . html_entity_decode($row['description']) . '</td>';
			}

            //Foreman
            $foremanid = $row['foremanid'];
            $each_foremanid = explode(',',$foremanid);
            if(strpos($dashboard_config,',foreman,') !== false) {
				echo '<td data-title="Foreman">';
				foreach($each_foremanid as $all_emp) {
					if($all_emp != '') {
						$get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT first_name, last_name FROM contacts WHERE contactid='$all_emp'"));
						echo decryptIt($get_contact['first_name']).' '.decryptIt($get_contact['last_name']).'<br>';
					}
				}
				echo '</td>';
			}

            //Foreman Sheet
            $result_fs = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(fsid) AS total_fs FROM field_foreman_sheet WHERE jobid='$jobid'"));
            // ('.$result_fs['total_fs'].')

            echo '<td data-title="Foreman Sheet">'.($row['deleted'] == 1 ? 'Archived' : '<a href=\'add_field_foreman_sheet.php?jobid='.$row['jobid'].'\'>Create</a>').'</td>';

            //WT
            if(vuaed_visible_function($dbc, 'field_job') == 1) {
                $result_wt_c = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(workticketid) AS total_wt_c FROM field_work_ticket WHERE jobid='$jobid' AND status='Approved' AND  attach_invoice=0"));

                $result_wt_p = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(workticketid) AS total_wt_p FROM field_work_ticket WHERE jobid='$jobid' AND status='Pending'"));

                echo '<td data-title="PO">';
                $style_1 = '';
                $style_2 = '';
                if($result_wt_p['total_wt_p'] >0 ) {
                    $style_1 = 'style= "color: #bda501;"';
                    echo '<a '.$style_1.' target="_blank" href="field_work_ticket.php?type=Pending&jobid='.$jobid.'"> ('.$result_wt_p['total_wt_p'].') View Pending</a> | ';
                } else {
                    echo '('.$result_wt_p['total_wt_p'].') Pending | ';
                }
                if($result_wt_c['total_wt_c'] >0 ) {
                    $style_2 = 'style= "color: #1a5002;"';
                    echo '<a '.$style_2.' target="_blank" href="field_work_ticket.php?type=Approved&jobid='.$jobid.'"> ('.$result_wt_c['total_wt_c'].') View Completed</a>';
                } else {
                    echo '('.$result_wt_c['total_wt_c'].') Completed';
                }

                echo '</td>';
            }

            //PO
            $result_po = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(fieldpoid) AS total_po FROM field_po WHERE jobid='$jobid' AND attach_workticket=0 AND deleted=0"));

            echo '<td data-title="PO">';
            echo '<a target="_blank" href="field_po.php?jobid='.$jobid.'">('.$result_po['total_po'].')</a>';
            echo  ' <a href="add_field_po.php?jobid='.$row['jobid'].'" class="">Create</a>';
            echo '</td>';

            //Invoice
            if(vuaed_visible_function($dbc, 'field_job') == 1) {
                $result_in = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(invoiceid) AS total_in FROM field_invoice WHERE jobid='$jobid' AND deleted=0 AND status='Unpaid'"));
                echo '<td data-title="Invoice">';
                echo '<a target="_blank" href="field_invoice.php?paytype=Unpaid&jobid='.$jobid.'">('.$result_in['total_in'].' Outstanding)</a>';
                if($result_wt_c['total_wt_c'] > 0) {
                    echo ' <a href=\'create_field_invoice.php?jobid='.$jobid.'\'>Create</a>';
                } else if($get_job['invoice'] != 'Flat Rate') {
                    echo ' <a href=\'create_field_invoice.php?jobid='.$jobid.'&mode=flat_rate\'>Flat Rate</a>';
                }
                echo '</td>';
            }

            //Complete
            if(vuaed_visible_function($dbc, 'field_job') == 1) {
                echo '<td data-title="Complete">';
                echo '<a href=\'add_field_job.php?jobid='.$row['jobid'].'\'>Edit</a> | ';
				echo '<a href=\'../delete_restore.php?action=delete&subtab=jobs&fieldjobid='.$row['jobid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';

                /*
                    echo '<a href=\'delete_restore.php?action=delete&jobid='.$row['jobid'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a>';
                */
                $comp = 0;
                $result_fsid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(fsid) AS total_fsid FROM field_foreman_sheet WHERE jobid='$jobid' AND deleted=0 AND fsid NOT IN(SELECT fsid FROM field_work_ticket)"));

                $total_fsid = $result_fsid['total_fsid'];

                $result_wt_c = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(workticketid) AS total_wt_c FROM field_work_ticket WHERE jobid='$jobid' AND  attach_invoice=0 AND deleted=0"));

                $total_wt = $result_wt_c['total_wt_c'];

                $result_po = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(fieldpoid) AS total_po FROM field_po WHERE jobid='$jobid' AND attach_workticket=0 AND deleted=0"));

                $total_po = $result_po['total_po'];

                $result_invoice = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(invoiceid) AS total_invoice FROM field_invoice WHERE jobid='$jobid' AND status='Unpaid'"));
                $total_invoice = $result_invoice['total_invoice'];

                if($total_fsid != 0 || $total_wt != 0 || $total_po != 0 || $total_invoice != 0) {
                    $comp = 1;
                }

                if($comp == 0) {
                    echo ' | <a onclick="return confirm(\'Are you sure?\')" href=\'field_jobs.php?action=complete&jobid='.$jobid.'\'>Complete</a>';
                }
                echo '</td>';
            }

            echo "</tr>";
        }

        echo '</table></div>';
        if(vuaed_visible_function($dbc, 'field_job') == 1) {
            echo '<a href="add_field_job.php" class="btn brand-btn mobile-block pull-right">Create Job</a>';
        }

        if($job_name_search == '') {
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
        }

        ?>


</form>

</div>
</div>
</div>
<?php include ('../footer.php'); ?>
