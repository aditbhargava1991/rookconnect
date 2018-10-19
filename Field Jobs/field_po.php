<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('field_job');
error_reporting(0);

$role = $_SESSION['role'];
$s_employeeid = $_SESSION['contactid'];

if (isset($_POST['upload_doc_for_po_invoice'])) {
    $fieldpoid = $_POST['upload_doc_for_po_invoice'];
	$file_count = count($_FILES["file_".$fieldpoid]["name"]);
	for($i = 0; $i < $file_count; $i++) {
		//echo "I: $i<br />\n";
		if (!file_exists('download/field_invoice')) {
			mkdir('download/field_invoice', 0777, true);
		}
		$docs = htmlspecialchars($_FILES["file_".$fieldpoid]["name"][$i], ENT_QUOTES);

		$third_invoice_no = $_POST['thirdinvoiceno_'.$fieldpoid];

		//echo "File Name: ".print_r($_FILES,true)."<br />\n";
		move_uploaded_file($_FILES["file_".$fieldpoid]["tmp_name"][$i], "download/field_invoice/".$_FILES["file_".$fieldpoid]["name"][$i]);

		if($docs != '') {
			$docs = '##FFM##'.$docs;
			$query_update_bid = "UPDATE `field_po` SET `vendor_invoice`=CONCAT(ifnull(vendor_invoice, ''), '$docs'), `status` = 'To be Billed' WHERE `fieldpoid` = '$fieldpoid'";
			$result_update_bid = mysqli_query($dbc, $query_update_bid);

      $before_change = '';
      $history = "field_po entry has been updated for fieldpoid -> $fieldpoid. <br />";
      add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
		}
		if($third_invoice_no != '') {
			$query_update_bid = "UPDATE `field_po` SET `status` = 'To be Billed', `third_invoice_no` = '$third_invoice_no' WHERE `fieldpoid` = '$fieldpoid'";
			$result_update_bid = mysqli_query($dbc, $query_update_bid);

      $before_change = '';
      $history = "field_po entry has been updated for fieldpoid -> $fieldpoid. <br />";
      add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
		}
	}


    echo '<script type="text/javascript"> //window.location.replace("field_po.php"); </script>';
}

if (isset($_POST['upload_doc'])) {
    $fieldpoid = $_POST['upload_doc'];
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
	$docs = htmlspecialchars($_FILES["file"]["name"], ENT_QUOTES);

    move_uploaded_file($_FILES["file"]["tmp_name"], "download/".$_FILES["file"]["name"]);

    $query_update_bid = "UPDATE `field_po` SET `vendor_invoice` = '$docs', `status` = 'To be Billed' WHERE `fieldpoid` = '$fieldpoid'";

    $result_update_bid = mysqli_query($dbc, $query_update_bid);

    echo '<script type="text/javascript"> window.location.replace("field_jobs.php"); </script>';
}

if((!empty($_GET['workticketid'])) && ($_GET['status'] == 'Approve')) {
	$workticketid = $_GET['workticketid'];
	$today_date = date('Y-m-d');
	$query_update_site = "UPDATE `field_work_ticket` SET `status` = 'Approved', `date_received` = '$today_date' WHERE	`workticketid` = '$workticketid'";
	$result_update_site	= mysqli_query($dbc, $query_update_site);

  $before_change = '';
  $history = "field_work_ticket entry has been updated for workticketid -> $workticketid. <br />";
  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
	echo '<script type="text/javascript"> window.location.replace("field_jobs.php"); </script>';
}

if((!empty($_GET['workticketid'])) && ($_GET['status'] == 'Revert')) {
	$workticketid = $_GET['workticketid'];
	$query_update_site = "UPDATE `field_work_ticket` SET `status` = 'Pending', `date_received` = '' WHERE	`workticketid` = '$workticketid'";
	$result_update_site	= mysqli_query($dbc, $query_update_site);

  $before_change = '';
  $history = "field_work_ticket entry has been updated for workticketid -> $workticketid. <br />";
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
	$query_update_job = "UPDATE `field_jobs` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE	`jobid` = '$jobid'";
	$result_update_job	= mysqli_query($dbc, $query_update_job);

  $before_change = '';
  $history = "field_jobs entry has been updated for jobid -> $jobid. <br />";
  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

	$query_update_po = "UPDATE `field_po` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE	`jobid` = '$jobid'";
	$result_update_po	= mysqli_query($dbc, $query_update_po);

  $before_change = '';
  $history = "field_po entry has been updated for jobid -> $jobid. <br />";
  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

	$query_update_fs = "UPDATE `field_foreman_sheet` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE	`jobid` = '$jobid'";
	$result_update_fs	= mysqli_query($dbc, $query_update_fs);

  $before_change = '';
  $history = "field_foreman_sheet entry has been updated for jobid -> $jobid. <br />";
  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

	$query_update_in = "UPDATE `field_invoice` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE	`jobid` = '$jobid'";
	$result_update_in	= mysqli_query($dbc, $query_update_in);

  $before_change = '';
  $history = "field_invoice entry has been updated for jobid -> $jobid. <br />";
  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

	header('Location: field_jobs.php');
}

$tab_result = mysqli_fetch_array(mysqli_query($dbc, "SELECT value FROM general_configuration WHERE name='field_job_tabs'"));
$tab_config = $tab_result['value'];
$dashboard_result = mysqli_fetch_array(mysqli_query($dbc, "SELECT dashboard_list FROM field_config_field_jobs WHERE tab='po'"));
$dashboard_config = $dashboard_result['dashboard_list'];
if(str_replace(',','',$dashboard_config) == '') {
	$dashboard_config = ',po,job,';
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
			echo '<a href="config_field_jobs.php?tab=po" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><span class="popover-examples list-inline"><a class="pull-right" style="margin:-5px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span><br><br>';
		} ?></h1>
        <?php if (strpos($tab_config,',sites,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'sites' ) === true) { ?>
			<a href='field_sites.php'><button type="button" class="btn brand-btn mobile-block" >Sites</button></a>
        <?php } ?>
        <?php if (strpos($tab_config,',jobs,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'jobs' ) === true) { ?>
			<a href='field_jobs.php'><button type="button" class="btn brand-btn mobile-block" >Jobs</button></a>
        <?php } ?>
        <?php if (strpos($tab_config,',foreman,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'foreman_sheet' ) === true) { ?>
			<a href='field_foreman_sheet.php'><button type="button" class="btn brand-btn mobile-block" >Foreman Sheet</button></a>
        <?php } ?>
        <?php if (strpos($tab_config,',po,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'po' ) === true) { ?>
			<a href='field_po.php'><button type="button" class="btn brand-btn mobile-block active_tab" >PO</button></a>
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

    <?php if(search_visible_function($dbc, 'field_job') == 0) { } else { ?>
    <center><div class="double-pad-bottom">
        <label for="search_site">Search By Job # Or Any:</label>
        <?php if(isset($_POST['search_po_submit'])) { ?>
            <input type="text" name="search_po" value="<?php echo $_POST['search_po']?>" class="form-control" style="max-width:200px;">
        <?php } else { ?>
            <input type="text" name="search_po" class="form-control" style="max-width:200px;">
        <?php } ?>
        <button type="submit" name="search_po_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
        <button type="submit" name="display_all_po" value="Display All" class="btn brand-btn mobile-block">Display Current</button>
    </div></center>
    <?php } ?>

    <div id="no-more-tables">
        <?php
        echo '<a href="add_field_po.php" class="btn brand-btn pull-right">Create PO</a>';
        // Display Pager
        $rowsPerPage = ITEMS_PER_PAGE;
        $pageNum = 1;

        if(isset($_GET['page'])) {
            $pageNum = $_GET['page'];
        }

        $offset = ($pageNum - 1) * $rowsPerPage;

        $po_name_search = '';
        if (isset($_POST['search_po_submit'])) {
            $po_name_search = $_POST['search_po'];
			$vendor_list = search_contacts_table($dbc, $po_name_search, " AND `category`='Vendor'");
        }
        if (isset($_POST['display_all_po'])) {
            $po_name_search = '';
        }

        if(search_visible_function($dbc, 'field_job') == 0) {
            $query_check_credentials = "SELECT fj.*, fp.*  FROM field_jobs fj, field_po fp WHERE fp.attach_workticket = 0 AND fp.deleted = 0 AND fp.jobid = fj.jobid AND fj.foremanid LIKE '%,".$s_employeeid.",%' ORDER BY fieldpoid DESC LIMIT $offset, $rowsPerPage";
            $query   = "SELECT COUNT(fieldpoid) AS numrows FROM field_jobs fj, field_po fp WHERE fp.attach_workticket = 0 AND fp.deleted = 0 AND fp.jobid = fj.jobid AND fj.foremanid LIKE '%,".$s_employeeid.",%' ORDER BY fieldpoid DESC";
        } else {
            if($po_name_search != '') {
                $query_check_credentials = "SELECT fj.*, fp.* FROM field_jobs fj, field_po fp WHERE (fj.job_number LIKE '%$po_name_search%' OR fp.po_number LIKE '%$po_name_search%' OR fp.status = '$po_name_search' OR fp.vendorid IN ($vendor_list)) AND fp.jobid = fj.jobid ORDER BY fieldpoid DESC";
                $query = "SELECT COUNT(*) numrows FROM field_jobs fj, field_po fp WHERE (fj.job_number LIKE '%$po_name_search%' OR fp.po_number LIKE '%$po_name_search%' OR fp.status = '$po_name_search' OR fp.vendorid IN ($vendor_list)) AND fp.jobid = fj.jobid ORDER BY fieldpoid DESC";
            } else {
                $query_check_credentials = "SELECT fj.*, fp.* FROM field_jobs fj, field_po fp WHERE fp.attach_workticket = 0 AND fp.deleted = 0 AND fp.jobid = fj.jobid ORDER BY fieldpoid DESC LIMIT $offset, $rowsPerPage";
                $query   = "SELECT COUNT(fieldpoid) AS numrows FROM field_jobs fj, field_po fp WHERE fp.attach_workticket = 0 AND fp.deleted = 0 AND fp.jobid = fj.jobid ORDER BY fieldpoid DESC";
            }
        }

        // how many rows we have in database
        //$query   = "SELECT COUNT(workticketid) AS numrows FROM field_work_ticket";

        if($po_name_search == '') {
            echo '<h1 class="single-pad-bottom">'.display_pagination($dbc, $query, $pageNum, $rowsPerPage).'</h1>';
        }
        $result = mysqli_query($dbc, $query_check_credentials);

        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
        echo "<table class='table table-bordered table-striped'>";
        echo "<thead><tr class='hidden-xs hidden-sm'>
                ".(strpos($dashboard_config,',po,') !== false ? "<th>PO #</th>" : "")."
                ".(strpos($dashboard_config,',job,') !== false ? "<th>Job #</th>" : "")."
                ".(strpos($dashboard_config,',vendor,') !== false ? "<th>Vendor</th>" : "")."
                <th>PO</th>
                <th>3rd party invoice# / Upload Invoice</th>
                <th>Status</th>
                <th>Created By</th>
                <th>Last Updated By</th>";
                if(vuaed_visible_function($dbc, 'field_job') == 1) {
                    echo "<th>Function</th>";
                }
                echo "</tr></thead>";
        } else {
            echo "<h2>No Record Found.</h2>";
        }
        while($row = mysqli_fetch_array( $result ))
        {
            $jobid = $row['jobid'];
            echo '<tr>';
            if(strpos($dashboard_config,',po,') !== false) {
				echo '<td data-title="PO#">' . $row['po_number']. '</td>';
			}
            //echo '<td data-title="Job#"><a href=\'add_field_job.php?jobid='.$row['jobid'].'\'>' . $row['job_number'] . '</a></td>';

            if(strpos($dashboard_config,',job,') !== false) {
				echo '<td data-title="Job#">' . $row['job_number'] . '</td>';
			}

            if(strpos($dashboard_config,',vendor,') !== false) {
				echo '<td data-title="Vendor">' . get_client($dbc, $row['vendorid']). '</td>';
			}

            $name_of_file = 'download/field_po_'.$row['fieldpoid'].'.pdf';
            echo '<td data-title="PDF"><a href='.$name_of_file.' target="_blank">View</a></td>';

			echo '<td data-title="Invoice">';
			$vendor_invoice = $row['vendor_invoice'];

            if($row['third_invoice_no'] == '') {
                echo '<input name="thirdinvoiceno_'.$row['fieldpoid'].'" value="'.$row['third_invoice_no'].'" type="text" class="form-control" style="width: 20%;" />';
            } else {
                echo '#'.$row['third_invoice_no'];
            }

            if($vendor_invoice != '') {
                $vin = explode('##FFM##', $vendor_invoice);
                $vinc = 0;
                foreach($vin as $venin) {
                    if($venin != '') {
                        echo '<br> - <a href="download/field_invoice/'.$venin.'" target="_blank">'.$venin.'</a>';
                        echo ' | <a href=\'../delete_restore.php?action=delete&subtab=po&fpoid='.$row['fieldpoid'].'&vinc='.$vinc.'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
                    }
                    $vinc++;
                }
            }

            echo '&nbsp;&nbsp;<input accept="*" name="file_'.$row['fieldpoid'].'[]" type="file" onchange="invoiceUpload(this)" data-filename-placement="inside" class="form-control" multiple>';
            echo '<button type="submit" name="upload_doc_for_po_invoice" value="'.$row['fieldpoid'].'" class="btn brand-btn">Submit</button>';

			/*
            if($vendor_invoice != '') {
				echo '<a href="download/'.$vendor_invoice.'" target="_blank">'.$vendor_invoice.'</a> ';
			} else {
				echo '<input name="file" type="file" id="file" data-filename-placement="inside" class="form-control">';
				echo '<button type="submit" name="upload_doc" value="'.$row['fieldpoid'].'" class="btn brand-btn">Submit</button>';
			}
            */
			echo '</td>';

            echo '<td data-title="Status">' . ($row['deleted'] == 1 ? 'Archived' : $row['status']). '</td>';
            echo '<td data-title="Created By">' . $row['created_by']. '</td>';
            echo '<td data-title="Last Updated By">' . $row['edited_by']. '</td>';

            if(vuaed_visible_function($dbc, 'field_job') == 1) {
                echo '<td data-title="Function">';
                if($row['status'] == 'Pending') {
                    echo '<a href=\'add_field_po.php?jobid='.$row['jobid'].'&fieldpoid='.$row['fieldpoid'].'\'>Edit</a>';
					//Disabled the ability to Archive POs per Ticket #3039
					//That ticket was based on a request from Steven through the Checklist: "When a Po is issued against a job we have come across one that was archived before it was ever attached to a Work Ticket. There must not be able to happen. Please check into this and see how to prevent this from happening."
					//echo ' | <a href=\'../delete_restore.php?action=delete&fieldpoid='.$row['fieldpoid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
                } else if($row['status'] == 'To be Billed') {
                    echo '<a href=\'add_field_po.php?jobid='.$row['jobid'].'&fieldpoid='.$row['fieldpoid'].'\'>Edit</a>';
                } else {
                    echo '-';
                }
                echo '</td>';
            }

            echo "</tr>";
        }

        echo '</table></div>';
        echo '<a href="add_field_po.php" class="btn brand-btn pull-right">Create PO</a>';
        if($po_name_search == '') {
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
        }
        ?>


</form>

</div>
</div>
</div>
<?php include ('../footer.php'); ?>
