<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('field_job');
error_reporting(0);

$tab_result = mysqli_fetch_array(mysqli_query($dbc, "select value from general_configuration where name='field_job_tabs'"));
$tab_config = $tab_result['value'];
$dashboard_result = mysqli_fetch_array(mysqli_query($dbc, "select dashboard_list from field_config_field_jobs where tab='invoice'"));
$dashboard_config = $dashboard_result['dashboard_list'];
if(str_replace(',','',$dashboard_config) == '') {
	$dashboard_config = ',invoice,job,customer,';
}

if((!empty($_GET['invoiceid'])) && ($_GET['status'] == 'Paid')) {
	$invoiceid = $_GET['invoiceid'];
	$date_paid = date('Y-m-d');
	$before_change = capture_before_change($dbc, 'field_invoice', 'status', 'invoiceid', $invoiceid);
	$before_change .= capture_before_change($dbc, 'field_invoice', 'date_paid', 'invoiceid', $invoiceid);

	$query_update_site = "UPDATE `field_invoice` SET `status` = 'Paid', `date_paid` = '$date_paid' WHERE `invoiceid` = '$invoiceid'";
	$result_update_site	= mysqli_query($dbc, $query_update_site);

	$history = capture_after_change('status', 'Paid');
	$history .= capture_after_change('date_paid', $date_paid);
	add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

	echo '<script type="text/javascript"> window.location.replace("field_invoice.php?paytype=Unpaid"); </script>';
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
$edit_access = vuaed_visible_function($dbc, 'field_jobs'); ?>

<div class="container">
<div class="row">

<div class="col-md-12">
    	<!-- Admin -->
        <h1 class="single-pad-bottom">Field Jobs Dashboard<?php
			if(config_visible_function($dbc, 'field_jobs') == 1) {
				echo '<a href="config_field_jobs.php?tab=invoice" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><span class="popover-examples list-inline"><a class="pull-right" style="margin:-5px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span><br><br>';
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
			<a href='field_work_ticket.php'><button type="button" class="btn brand-btn mobile-block" >Work Ticket</button></a>
        <?php } ?>
        <?php if (strpos($tab_config,',invoice,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'invoices' ) === true) {
			$active_unpaid = '';
			$active_paid = '';
			if($_GET['paytype'] == 'Unpaid') {
				$active_unpaid = ' active_tab';
			}
			if($_GET['paytype'] == 'Paid') {
				$active_paid = ' active_tab';
			}
			?>
			<a href='field_invoice.php?paytype=Unpaid'><button type="button" class="btn brand-btn mobile-block <?php echo $active_unpaid;?>" >Outstanding Invoices</button></a>
			<a href='field_invoice.php?paytype=Paid'><button type="button" class="btn brand-btn mobile-block <?php echo $active_paid;?>" >Paid Invoices</button></a>
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
                <?php if(isset($_POST['search_out_in_submit'])) { ?>
                    <input type="text" name="search_out_in" value="<?php echo $_POST['search_out_in']?>" class="form-control">
                <?php } else { ?>
                    <input type="text" name="search_out_in" class="form-control">
                <?php } ?>
            </div>
        </div>
        &nbsp;
            <button type="submit" name="search_out_in_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
            <button type="submit" name="display_all_in" value="Display All" class="btn brand-btn mobile-block">Display Current</button>
    </center>
    <?php } ?>

    <div id="no-more-tables">
        <?php
        // Display Pager

        $rowsPerPage = ITEMS_PER_PAGE;
        $pageNum = 1;

        if(isset($_GET['page'])) {
            $pageNum = $_GET['page'];
        }

        $offset = ($pageNum - 1) * $rowsPerPage;

        $in_name_search = '';
        if (isset($_POST['search_out_in_submit'])) {
            $in_name_search = $_POST['search_out_in'];
        }
        if (isset($_POST['display_all_in'])) {
            $in_name_search = '';
        }
        $paytype = $_GET['paytype'];
        if($in_name_search != '') {
			$client_list = search_contacts_table($dbc, $in_name_search, " AND `contactid` IN (SELECT `clientid` FROM `field_jobs`) ", 'NAME');
            $query_check_credentials = "SELECT fj.*, fi.* FROM field_invoice fi, field_jobs fj WHERE fi.deleted = 0 AND fi.jobid = fj.jobid AND fi.status='$paytype' AND (fj.job_number LIKE '%$in_name_search%' OR fj.clientid IN ($client_list)) ORDER BY fi.invoiceid DESC";
        } else if(!empty($_GET['jobid'])) {
            $url_jobid = $_GET['jobid'];
            $query_check_credentials = "SELECT fj.*, fi.* FROM field_invoice fi, field_jobs fj WHERE fi.deleted = 0 AND fi.jobid = '$url_jobid' AND fi.status='$paytype' ORDER BY fi.invoiceid DESC LIMIT $offset, $rowsPerPage";
            $query   = "SELECT COUNT(invoiceid) AS numrows FROM field_invoice fi, field_jobs fj WHERE fi.deleted = 0 AND fi.jobid = '$url_jobid' AND fi.status='$paytype' ORDER BY fi.invoiceid DESC";
        } else {
            $query_check_credentials = "SELECT fj.*, fi.* FROM field_invoice fi, field_jobs fj WHERE fi.deleted = 0 AND fi.jobid = fj.jobid AND fi.status='$paytype' ORDER BY fi.invoiceid DESC LIMIT $offset, $rowsPerPage";
            $query   = "SELECT COUNT(invoiceid) AS numrows FROM field_invoice fi, field_jobs fj WHERE fi.deleted = 0 AND fi.jobid = fj.jobid AND fi.status='$paytype' ORDER BY fi.invoiceid";
        }

        if($in_name_search == '') {
            echo '<h1 class="single-pad-bottom">'.display_pagination($dbc, $query, $pageNum, $rowsPerPage).'</h1>';
        }
        $result = mysqli_query($dbc, $query_check_credentials);

        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
        echo "<table class='table table-bordered table-striped'>";
        echo "<thead><tr class='hidden-xs hidden-sm'>
                ".(strpos($dashboard_config,'invoice') !== false ? "<th>Invoice#</th>" : "")."
                ".(strpos($dashboard_config,'job') !== false ? "<th>Job#</th>" : "")."
                ".(strpos($dashboard_config,'customer') !== false ? "<th>Customer</th>" : "")."
                ".(strpos($dashboard_config,'date') !== false ? "<th>Created Date</th>" : "")."
                <th>Invoice</th>";
                if($paytype == 'Unpaid') {
                echo "<th>Status</th>";
                } else {
                echo "<th>Date paid</th>";
                }
                echo "</tr></thead>";
        } else {
            echo "<h2>No Record Found.</h2>";
        }
        while($row = mysqli_fetch_array( $result ))
        {
            $jobid = $row['jobid'];
            echo '<tr>';
            if(strpos($dashboard_config,'invoice') !== false) {
				echo '<td data-title="Invoice#">' . $row['invoiceid'] . '</td>';
			}
            if(strpos($dashboard_config,'job') !== false) {
				echo '<td data-title="Job#"><a href=\'add_field_job.php?jobid='.$row['jobid'].'\'>' . $row['job_number'] . '</a></td>';
			}

            if(strpos($dashboard_config,'customer') !== false) {
			    echo '<td data-title="Contact">'.get_client($dbc, $row['clientid']).'<br>'.get_staff($dbc, $row['contactid']).'</td>';
			}
            if(strpos($dashboard_config,'date') !== false) {
				echo '<td data-title="Created Date">' . $row['invoice_date']. '</td>';
			}

            $name_of_file = 'download/field_invoice_'.$row['invoiceid'].'.pdf';
            echo '<td data-title="Invoice"><a href='.$name_of_file.' target="_blank">View <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a>';
			if($edit_access > 0) {
				echo ' - <a href="create_field_invoice.php?jobid='.$row['jobid'].'&invoiceid='.$row['invoiceid'].'">Regenerate</a>';
			}
			echo '</td>';

            if($paytype == 'Unpaid') {
                echo '<td data-title="Status">Unpaid | <a href=\'field_invoice.php?invoiceid='.$row['invoiceid'].'&status=Paid\'>Paid</a>';
            } else {
                echo '<td data-title="Created Date">' . $row['date_paid']. '';
            }
			echo ' | <a href=\'../delete_restore.php?action=delete&category=invoice&subtab='.$_GET['paytype'].'&invoiceid='.$row['invoiceid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a></td>';

            echo "</tr>";
        }

        echo '</table></div>';

        if($in_name_search == '') {
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
        }

        ?>

</form>

</div>
</div>
</div>
<?php include ('../footer.php'); ?>
