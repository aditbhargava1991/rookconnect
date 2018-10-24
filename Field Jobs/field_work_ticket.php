<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('field_job');
error_reporting(0);

$role = $_SESSION['role'];
$s_employeeid = $_SESSION['contactid'];

if (isset($_POST['submit_pay'])) {
	$workticketid = implode(',',$_POST['workticketid_send']);
    $each_wt = $_POST['workticketid_send'][0];

	$wt_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT jobid FROM  field_work_ticket  WHERE workticketid = '$each_wt'"));
    $jobid = $wt_result['jobid'];

	$job_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT contactid FROM  field_jobs  WHERE jobid = '$jobid'"));
    $contactid_send = $job_result['contactid'];
	header('Location: send_work_ticket.php?workticketid='.$workticketid.'&cid='.$contactid_send);
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

		$before_change = '';
    $history = "field_po entry has been updated for fieldpoid -> $fieldpoid. <br />";
    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

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

$tab_result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE `name`='field_job_tabs'"));
$tab_config = $tab_result['value'];
$dashboard_result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `dashboard_list` FROM `field_config_field_jobs` WHERE `tab`='work'"));
$dashboard_config = $dashboard_result['dashboard_list'];
if(str_replace(',','',$dashboard_config) == '') {
	$dashboard_config = ',ticket,job,date,description,mod_reg,mod_ot,';
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
		if(config_visible_function($dbc, 'field_jobs') == 1) {
			echo '<a href="config_field_jobs.php?tab=work" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><span class="popover-examples list-inline"><a class="pull-right" style="margin:-5px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span><br><br>';
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
			<a href='field_po.php'><button type="button" class="btn brand-btn mobile-block" >PO</button></a>
        <?php } ?>
        <?php if (strpos($tab_config,',work,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'work_ticket' ) === true) { ?>
			<a href='field_work_ticket.php'><button type="button" class="btn brand-btn mobile-block active_tab" >Work Ticket</button></a>
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
        <div class="form-group col-lg-9 col-md-10 col-sm-12 col-xs-12">
            <label for="site_name" class="col-sm-6 control-label">Search by Job# / Date / Customer / AFE# / Additional Information:</label>
            <div class="col-sm-6">
                <?php if(isset($_POST['search_wt_submit'])) { ?>
                    <input type="text" name="search_wt" value="<?php echo $_POST['search_wt']?>" class="form-control">
                <?php } else { ?>
                    <input type="text" name="search_wt" class="form-control">
                <?php } ?>
            </div>
        </div>
		<div class="form-group col-lg-3 col-md-2 col-sm-12 col-xs-12">
            <button type="submit" id="search_wt_submit" name="search_wt_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
            <button type="submit" name="display_all_wt" value="Display All" class="btn brand-btn mobile-block">Display Current</button>
		</div>
    </center>&nbsp;<div class="clearfix"></div>
    <?php }

       echo '<a href="add_field_work_ticket.php?from=blank" class="btn brand-btn pull-right">Add Work Ticket</a><div class="clearfix"></div>';
    ?>
        <div class="pull-right">Green = completed and ready to be invoiced<br/>
        Yellow = pending/requires cust. approval</div><div class="clearfix"></div>

    <div id="no-more-tables">
        <?php
        // Display Pager
        $rowsPerPage = 50;
        $pageNum = 1;

        if(isset($_GET['page'])) {
            $pageNum = $_GET['page'];
        }

        $offset = ($pageNum - 1) * $rowsPerPage;

        $wt_name_search = '';
        if (isset($_POST['search_wt_submit'])) {
            $wt_name_search = $_POST['search_wt'];
        }
        if (isset($_POST['display_all_wt'])) {
            $wt_name_search = '';
        }

        if($wt_name_search != '') {
			$client_list = search_contacts_table($dbc, $wt_name_search, " AND `contactid` IN (SELECT `clientid` FROM `field_jobs`) ", 'NAME');
            $query_check_credentials = "SELECT fj.*, fwt.*, cl.contactid, cl.name FROM contacts cl, field_work_ticket fwt, field_jobs fj WHERE fwt.jobid = fj.jobid AND fj.clientid = cl.contactid AND (fj.job_number LIKE '%$wt_name_search%' OR fj.afe_number LIKE '%$wt_name_search%' OR fj.additional_info LIKE '%$wt_name_search%' OR fwt.wt_date LIKE '%$wt_name_search%' OR fj.clientid IN ($client_list)) ORDER BY workticketid DESC";
        } else {
            $query_check_credentials = "SELECT fj.*, fwt.*, cl.contactid, cl.name, fj.contactid AS fj_contactid FROM contacts cl, field_work_ticket fwt, field_jobs fj WHERE fwt.deleted = 0 AND fwt.jobid = fj.jobid AND fj.clientid = cl.contactid AND fwt.attach_invoice = 0 ORDER BY workticketid DESC LIMIT $offset, $rowsPerPage";
        }

        // how many rows we have in database
        $query   = "SELECT COUNT(workticketid) AS numrows FROM contacts cl, field_work_ticket fwt, field_jobs fj WHERE fwt.deleted = 0 AND fwt.jobid = fj.jobid AND fj.clientid = cl.contactid AND fwt.attach_invoice = 0 ORDER BY workticketid DESC";

        if($wt_name_search == '') {
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
        }
        $result = mysqli_query($dbc, $query_check_credentials);

        echo "<button type='submit' name='submit_pay' value='Submit' class='btn brand-btn pull-right '>Send Email</button><br>";

        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
        echo "<table class='table table-bordered'>";
        echo "<tr class='hidden-xs hidden-sm'>
                ".(strpos($dashboard_config,',work_ticket,') !== false ? "<th>WT#</th>" : "")."
                ".(strpos($dashboard_config,',date,') !== false ? "<th>Date</th>" : "")."
                ".(strpos($dashboard_config,',job,') !== false ? "<th>Job#</th>" : "")."
                ".(strpos($dashboard_config,',customer,') !== false ? "<th>Customer</th>" : "");
                if(strpos($dashboard_config,',invoice,') !== false && $wt_name_search != '') {
                    echo "<th>Invoice#</th>";
                }
                echo (strpos($dashboard_config,',sent,') !== false ? "<th>Date Sent</th>" : "")."
                ".(strpos($dashboard_config,',approved,') !== false ? "<th>Date Approved by Customer</th>" : "")."
                <th>PDF</th>
                <th>Function</th>
                </tr>";
        } else {
            echo "<h2>No Record Found.</h2>";
        }
        $user_loop = '';
        $submit_inc = 0;
        while($row = mysqli_fetch_array( $result ))
        {
            $jobid = $row['jobid'];
            $fsid = $row['fsid'];
            $fs_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM  field_foreman_sheet  WHERE fsid = '$fsid'"));

            $workticketid = $row['workticketid'];
            echo '<input type="hidden" name="workticketid[]" value="'.$workticketid.'" />';

            $style = '';
            if($row['status'] == 'Pending') {
                $style = "style= 'background-color: #bda501;'";
            } else {
                $style = "style= 'background-color: #1a5002;'";
            }

            echo '<tr '.$style.'>';
            if(strpos($dashboard_config,',work_ticket,') !== false) {
				echo '<td data-title="WT#">' . $row['workticketid'] . '</td>';
			}
            if(strpos($dashboard_config,',date,') !== false) {
				echo '<td data-title="WT Date">' . $row['wt_date'] . '</td>';
			}
            if(strpos($dashboard_config,',job,') !== false) {
				echo '<td data-title="Job#"><a href=\'add_field_job.php?jobid='.$row['jobid'].'\'>' . $row['job_number'].'<br />AFE #'.$row['afe_number'] .'<br />'.$row['additional_info'] . '</a></td>';
			}

            if(strpos($dashboard_config,',customer,') !== false) {
			    echo '<td data-title="Contact">'.get_client($dbc, $row['clientid']).'<br>'.get_staff($dbc, $row['fj_contactid']).'</td>';
			}

            if(strpos($dashboard_config,',invoice,') !== false && $wt_name_search != '') {
                if($row['attach_invoice'] == 0) {
                    echo '<td data-title="PDF">NA</td>';
                } else {
                    $name_of_file = 'download/field_invoice_'.$row['attach_invoice'].'.pdf';
                    echo '<td data-title="PDF"><a href='.$name_of_file.' target="_blank">' . $row['attach_invoice']. '</a></td>';
                }
            }
            ?>

			<?php if(strpos($dashboard_config,',sent,') !== false) { ?>
				<td data-title="Date Sent">
				<?php
				if($row['deleted'] == 1) {
					echo 'Archived';
				} else {
					$sent_dates = [];
					foreach(array_filter(explode('<br>',$row['date_sent'])) as $date_sent) {
						$date_sent = explode('|',$date_sent);
						if($date_sent[1] == 'hand') {
							$sent_dates[] = $date_sent[0].' - Hand Delivered';
						} else {
							$sent_dates[] = $date_sent[0].' - Emailed';
						}
					}
					echo implode('<br>',$sent_dates);
				}
				echo "&nbsp;";
				$submit_value = 'submit_'.$submit_inc;
				//echo '<button type="submit" name="submit_services" value="'.$submit_value.'" class="btn btn-xs brand-btn mobile-block">Submit</button>';
				  ?>
				</td>
			<?php } ?>

            <?php if(strpos($dashboard_config,',approved,') !== false) { ?>
				<td data-title="Date Received">
				<?php
				if($row['deleted'] == 1) {
					echo 'Archived';
				} else if($row['date_received'] == '0000-00-00' || $row['date_received'] == '' ) {
					echo 'Not yet received';
				} else {
					echo $row['date_received'];
				}
				?>

				</td>
			<?php } ?>

            <?php
            $name_of_file = 'download/field_work_ticket_'.$row['workticketid'].'.pdf';
            echo '<td data-title="PDF"><a href='.$name_of_file.' target="_blank">View <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"> </a>';
            if($row['status'] == 'Pending') {
                //echo ' | <a href=\'field_jobs.php?wtsend=pdf&workticketid='.$row['workticketid'].'&contactid='.$row['contactid'].'\'>Send</a></td>';
            }

            $fsid = $row['fsid'];
            echo '<td data-title="Function">';

            if($row['status'] == 'Pending') {
                echo '<a href=\'add_field_work_ticket.php?fsid='.$fsid.'&jobid='.$jobid.'&workticketid='.$row['workticketid'].'\'>Edit</a> | ';
            }

            if($row['status'] == 'Pending') {
                echo 'Pending | <a href=\'field_jobs.php?workticketid='.$row['workticketid'].'&status=Approve\'>Cust. Approve</a>';
            } else {
                echo 'Cust. Approved | <a href=\'field_jobs.php?workticketid='.$row['workticketid'].'&status=Revert\'>Revert</a>';
            }
            echo ' | <input type="checkbox" name="workticketid_send[]" value="'.$row['workticketid'].'"> | ';
            echo '<a class="cursor-hand" onclick="$.get(\'field_job_ajax_all.php?action=hand_deliver&workticketid='.$row['workticketid'].'\'); $(\'#search_wt_submit\').click(); return false;">Hand Deliver</a> | ';
			echo '<a href=\'../delete_restore.php?action=delete&subtab=wt&workticketid='.$row['workticketid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
            echo '<input type="hidden" name="contactid_send" value="'.$contactid.'" />';

            echo '</td>';
            echo "</tr>";
            $submit_inc++;
        }

        echo '</table></div>';

        if($wt_name_search == '') {
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
        }
        echo '<a href="add_field_work_ticket.php?from=blank" class="btn brand-btn pull-right">Add Work Ticket</a>';

        ?>

</form>

</div>
</div>
</div>
<?php include ('../footer.php'); ?>
