<?php
/*
compensation
*/
include ('../include.php');
checkAuthorised('goals_compensation');

?>
</head>
<body>
<?php include_once ('../navigation.php');
?>

<div class="container triple-pad-bottom">
    <div class="row">
		<div class="col-md-12">

        <h1 class="">Stat Report Dashboard</h1>

        <?php
            $role = $_SESSION['role'];
            $therapistid = $_SESSION['contactid'];

        if($role== 'super') {
            echo '<a href="field_config_compensation.php" class="mobile-block pull-right"><img style="width:50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me" /></a><br><br>';
        }
        ?>
        </h1>

		<div class="tab-container">
			<span class="popover-examples list-inline" style="margin:0 0 0 10px;"><span><a data-toggle="tooltip" data-placement="top" title="These are the goals for stats reporting."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span></span><?php
            if ( check_subtab_persmission( $dbc, 'goals_compensation', ROLE, 'staff_goals' ) === true ) { ?>
                <a href='goals.php'><button type="button" class="btn brand-btn mobile-block mobile-100">Staff Goals</button></a><?php
            } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100">Staff Goals</button><?php
            } ?>
			
            <span class="popover-examples list-inline" style="margin:0 0 0 10px;"><span><a data-toggle="tooltip" data-placement="top" title="Compensation values for staff."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span></span><?php
            if ( check_subtab_persmission( $dbc, 'goals_compensation', ROLE, 'staff_comp' ) === true ) { ?>
                <a href='compensation.php'><button type="button" class="btn brand-btn mobile-block mobile-100">Staff Compensation</button></a><?php
            } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100">Staff Compensation</button><?php
            } ?>
			
            <span class="popover-examples list-inline" style="margin:0 0 0 10px;"><span><a data-toggle="tooltip" data-placement="top" title="Stat Report"><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span></span><?php
            if ( check_subtab_persmission( $dbc, 'goals_compensation', ROLE, 'stat_report' ) === true ) { ?>
                <a href='stat_report_setup.php'><button type="button" class="btn brand-btn mobile-block mobile-100 active_tab">Stat Report</button></a><?php
            } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100">Stat Report</button><?php
            } ?>
			
            <span class="popover-examples list-inline" style="margin:0 0 0 10px;"><span><a data-toggle="tooltip" data-placement="top" title="Hourly Pay"><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span></span><?php
            if ( check_subtab_persmission( $dbc, 'goals_compensation', ROLE, 'hourly_pay' ) === true ) { ?>
                <a href='hourly_pay.php'><button type="button" class="btn brand-btn mobile-block mobile-100">Hourly Pay</button></a><?php
            } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100">Hourly Pay</button><?php
            } ?>
		</div>

        <form name="form_sites" method="post" action="" class="form-inline" role="form">

            <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            You can set "% of Available Hours Scheduled" in the Stat Report using the below listed ways for each Staff: <br>
            Monthly = If the search field date difference in the Stat Report is equal to or greater than 28 days, then the Stat Report will pull the Monthly field from the Goals & Compensation tile.<br>
            Bi-Weekly = If the search field date difference in the Stat Report is between 13 - 27 days, the Stat Report will pull the Monthly field from the Goals & Compensation tile (including 13 days and 27 days).<br>
            Weekly = If the search field date difference in the Stat Report is between 6 - 12 days, the Stat Report will pull the Monthly field from the Goals & Compensation tile (including 6 days and 12 days).<br>
            Daily = If the search field date difference in the Stat Report is less than 6 days, the Stat Report will pull the Monthly field from the Goals & Compensation tile.
            </div>

            <div class="clearfix"></div>
            </div>

            <div class="pad-top pad-bottom">
                <?php
                if(vuaed_visible_function($dbc, 'goals_compensation') == 1) {
                    echo '
						<span class="pull-right">
							<span class="popover-examples list-inline" style="margin:0 0 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add stat report values"><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span>
							<a href="add_stat_report_setup.php" class="btn brand-btn mobile-block">Add Stat Report Value</a>
						</span>';
                }
                ?>
            </div>

            <div class="no-more-tables">

            <?php
            //Search

            //if($role== 'super') {
                $query_check_credentials = "SELECT * FROM goal WHERE deleted=0";
            //} else {
            //    $query_check_credentials = "SELECT * FROM goal WHERE deleted=0 AND therapistid='$therapistid'";
            //}

            $result = mysqli_query($dbc, $query_check_credentials);

            $num_rows = mysqli_num_rows($result);
            if($num_rows > 0) {
                echo "<table class='table table-bordered'>";
                echo "<tr class='hidden-xs hidden-sm'>";
                    echo '<th>Staff</th>';
                    echo '<th>Date Set</th>';
                    echo '<th>Function</th>';
				echo "</tr>";

				while($row = mysqli_fetch_array( $result ))
				{
					echo "<tr>";
					echo '<td>'.get_contact($dbc, $row['therapistid']). '</td>';
					//<a href="#"  onclick=" window.open(\''.WEBSITE_URL.'/Contact/add_contact.php?type=Patient&contactid='.$row['therapistid'].'\', \'newwindow\', \'width=900, height=900\'); return false;">'.get_contact($dbc, $row['therapistid']). '</a>

					echo '<td data-title="Service Type">' . $row['today_date'] . '</td>';

					echo '<td data-title="Function">';
					echo '<a href=\'add_stat_report_setup.php?goalid='.$row['goalid'].'\'>Edit</a>';

					//echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&setupid='.$row['setupid'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a>';
					echo '</td>';

					echo "</tr>";
				}

				echo '</table></div>';
            } else {
                echo "<h2>No Record Found.</h2>";
            }
            if(vuaed_visible_function($dbc, 'goals_compensation') == 1) {
            echo '
				<span class="pull-right">
					<span class="popover-examples list-inline" style="margin:0 0 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add stat report values"><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span>
					<a href="add_stat_report_setup.php" class="btn brand-btn mobile-block">Add Stat Report Value</a>
				</span>';
            }

            ?>
        </form>

        </div>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>