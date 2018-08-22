<?php
/*
Sites Listing
*/
include ('../include.php');
checkAuthorised('field_job');
error_reporting(0);

$tab_result = mysqli_fetch_array(mysqli_query($dbc, "select value from general_configuration where name='field_job_tabs'"));
$tab_config = $tab_result['value'];
$dashboard_result = mysqli_fetch_array(mysqli_query($dbc, "select dashboard_list from field_config_field_jobs where tab='sites'"));
$dashboard_config = $dashboard_result['dashboard_list'];
if(str_replace(',','',$dashboard_config) == '') {
	$dashboard_config = ',site_name,';
}
$level = trim(ROLE,',');
?>
</head>
<body>
<?php include_once ('../navigation.php');

?>

<div class="container">
	<div class="row">
		<div class="col-md-12">

        <h1 class="single-pad-bottom">Field Jobs Dashboard<?php
		if(config_visible_function($dbc, 'field_jobs') == 1) {
			echo '<a href="config_field_jobs.php?tab=sites" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><span class="popover-examples list-inline"><a class="pull-right" style="margin:-5px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span><br><br>';
		} ?></h1>
        <?php if (strpos($tab_config,',sites,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'sites' ) === true) { ?>
			<a href='field_sites.php'><button type="button" class="btn brand-btn mobile-block active_tab" >Sites</button></a>
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
        <?php if (strpos($tab_config,',invoice,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'invoices' ) === true) { ?>
            <a href='field_invoice.php?paytype=Unpaid'><button type="button" class="btn brand-btn mobile-block" >Outstanding Invoices</button></a>
            <a href='field_invoice.php?paytype=Paid'><button type="button" class="btn brand-btn mobile-block" >Paid Invoices</button></a>
        <?php } ?>
        <?php if (strpos($tab_config,',payroll,') !== false && check_subtab_persmission( $dbc, 'field_job', ROLE, 'payroll' ) === true) { ?>
			<a href='field_payroll.php'><button type="button" class="btn brand-btn mobile-block" >Payroll</button></a>
        <?php } ?>
        <br><br>
        <form name="form_clients" method="post" action="" class="form-inline" role="form">

            <center>
                <div class="form-group">
                    <label for="site_name" class="col-sm-5 control-label">Search By Any:</label>
                    <div class="col-sm-6">
                        <?php if(isset($_POST['search_site_name_submit'])) { ?>
                            <input type="text" name="search_site_name" value="<?php echo $_POST['search_site_name']?>" class="form-control"  style='max-width:200px;'>
                        <?php } else { ?>
                            <input type="text" name="search_site_name" class="form-control"  style='max-width:200px;'>
                        <?php } ?>
                    </div>
                </div>
                &nbsp;
				<button type="submit" name="search_site_name_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
			<button type="submit" name="display_all_site_name" value="Display All" class="btn brand-btn mobile-block">Display All</button>
            </center>

			<?php
			echo '<a href="add_field_site.php" class="btn brand-btn mobile-block pull-right">Add Site</a>';
			?>

		<div class='no-more-tables'>
			<?php
            $site_name = '';
            if (isset($_POST['search_site_name_submit'])) {
                $site_name = $_POST['search_site_name'];
            }
            if (isset($_POST['display_all_site_name'])) {
                $site_name = '';
            }

            /* Pagination Counting */
            $rowsPerPage = 25;
            $pageNum = 1;

            if(isset($_GET['page'])) {
                $pageNum = $_GET['page'];
            }

            $offset = ($pageNum - 1) * $rowsPerPage;

            if($site_name != '') {
				$client_list = search_contacts_table($dbc, $site_name, " AND `contactid` IN (SELECT `clientid` FROM `field_sites`) ", 'NAME');
                $query_check_credentials = "SELECT s.* FROM field_sites s WHERE s.deleted = 0 AND (s.clientid IN ($client_list) OR s.site_name LIKE '%" . $site_name . "%' OR s.domain_name LIKE '%" . $site_name . "%' OR s.display_name LIKE '%" . $site_name . "%' OR s.phone_number LIKE '%" . $site_name . "%' OR s.fax_number LIKE '%" . $site_name . "%' OR s.mail_street LIKE '%" . $site_name . "%' OR s.mail_country LIKE '%" . $site_name . "%' OR s.mail_city LIKE '%" . $site_name . "%' OR s.mail_state LIKE '%" . $site_name . "%' OR s.mail_zip LIKE '%" . $site_name . "%') LIMIT $offset, $rowsPerPage";
                $query = "SELECT count(s.clientid) as numrows FROM field_sites s WHERE s.deleted = 0 AND (s.clientid IN ($client_list) OR s.site_name LIKE '%" . $site_name . "%' OR s.domain_name LIKE '%" . $site_name . "%' OR s.display_name LIKE '%" . $site_name . "%' OR s.phone_number LIKE '%" . $site_name . "%' OR s.fax_number LIKE '%" . $site_name . "%' OR s.mail_street LIKE '%" . $site_name . "%' OR s.mail_country LIKE '%" . $site_name . "%' OR s.mail_city LIKE '%" . $site_name . "%' OR s.mail_state LIKE '%" . $site_name . "%' OR s.mail_zip LIKE '%" . $site_name . "%')";
            } else {
                $query_check_credentials = "SELECT * FROM field_sites WHERE deleted = 0 LIMIT $offset, $rowsPerPage";
                $query = "SELECT count(*) as numrows FROM field_sites WHERE deleted = 0";
            }

			$result = mysqli_query($dbc, $query_check_credentials);

			$num_rows = mysqli_num_rows($result);
			if($num_rows > 0) {
				// Added Pagination //
				echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
				// Pagination Finish //

				echo "<table class='table table-bordered table-striped'>";
				echo "<thead><tr class='hidden-xs hidden-sm'>
						".(strpos($dashboard_config,',site_name,') !== false ? "<th>Site name</th>" : "")."
						".(strpos($dashboard_config,',customer,') !== false ? "<th>Customer</th>" : "")."
						".(strpos($dashboard_config,',website,') !== false ? "<th>Website</th>" : "")."
						".(strpos($dashboard_config,',display,') !== false ? "<th>Display Name</th>" : "")."
						".(strpos($dashboard_config,',address,') !== false ? "<th>Address</th>" : "")."
						".(strpos($dashboard_config,',phone,') !== false ? "<th>Phone Number</th>" : "")."
						".(strpos($dashboard_config,',fax,') !== false ? "<th>Fax Number</th>" : "")."
						<th>Function</th>
						</tr></thead>";

				while($row = mysqli_fetch_array( $result ))
				{
					echo '<tr>';
					if(strpos($dashboard_config,',site_name,') !== false) {
						echo '<td data-title="Site name"><a href=\'add_field_site.php?siteid='.$row['siteid'].'\'>' . $row['site_name'] . '</a></td>';
					}
					if(strpos($dashboard_config,',customer,') !== false) {
						echo '<td data-title="Customer">'.get_client($dbc, $row['clientid']).'</td>';
					}
					if(strpos($dashboard_config,',website,') !== false) {
						echo '<td data-title="Website">' . $row['domain_name'] . '</td>';
					}
					if(strpos($dashboard_config,',display,') !== false) {
						echo '<td data-title="Display Name">' . $row['display_name'] . '</td>';
					}
					if(strpos($dashboard_config,',address,') !== false) {
						echo '<td data-title="Address">' . $row['office_street']. ' '.$row['office_country'] . ' '.$row['office_city']. ' '.$row['office_state']. ' '.$row['office_zip']. '</td>';
					}
					if(strpos($dashboard_config,',phone,') !== false) {
						echo '<td data-title="Phone Number">' . $row['phone_number'] . '</td>';
					}
					if(strpos($dashboard_config,',fax,') !== false) {
						echo '<td data-title="Fax Number">' . $row['fax_number'] . '</td>';
					}

					echo '<td data-title="Function">';
					echo '<a href=\'add_field_site.php?siteid='.$row['siteid'].'\'>Edit</a> | ';
					echo '<a href=\'../delete_restore.php?action=delete&subtab=sites&siteid='.$row['siteid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
					echo '</td>';

					echo "</tr>";
				}

				echo '</table></div>';

				// Added Pagination //
				echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
				// Pagination Finish //

				echo '<a href="add_field_site.php" class="btn brand-btn mobile-block pull-right">Add Site</a>';
			} else {
				echo "<h2>No Record Found.</h2>";
			}
			?>
		</form>
		</div>
	</div>
</div>

<?php include ('../footer.php'); ?>
