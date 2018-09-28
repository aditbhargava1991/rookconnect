<?php
/*
Inventory Listing
*/
include ('../include.php');
error_reporting(0);
?>
<script>
    // $(document).on('change.select2', 'select[name="search_client"]', function() { submitForm(this); });

    // function submitForm(thisForm) {
    //     if (!$('input[name="search_user_submit"]').length) {
    //         var input = $("<input>")
    //                     .attr("type", "hidden")
    //                     .attr("name", "search_user_submit").val("1");
    //         $('[name=form_sites]').append($(input));
    //     }

    //     thisForm.form.submit();
    // }
</script>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('agenda_meeting');
$value_config = ','.mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_agendas_meetings"))['field_config'].',';
?>
<div class="container">
	<div class="row">

        <div>
			<div class="col-sm-10">
				<h1>Meetings</h1>
			</div>
			<div class="col-sm-2 double-gap-top">
				<?php
					if(config_visible_function($dbc, 'agenda_meeting') == 1) {
						echo '<a href="field_config_agenda_meeting.php?type=tab&category=Meeting" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
						echo '<span class="popover-examples pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add or remove fields for Agenda &amp; Meetings."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
					}
				?>
			</div>
			<div class="clearfix"></div>
        </div>

        <div class="gap-top gap-left tab-container mobile-100-container"><?php
            if ( check_subtab_persmission( $dbc, 'agenda_meeting', ROLE, 'how_to_guide' ) !== false ) { ?>
                <a href="how_to_guide.php"><button type="button" class="btn brand-btn mobile-block mobile-100 tab">How To Guide</button></a><?php
            } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100 tab">How To Guide</button><?php
            }

            if ( check_subtab_persmission( $dbc, 'agenda_meeting', ROLE, 'agenda' ) !== false ) { ?>
                <a href="agenda.php"><button type="button" class="btn brand-btn mobile-block mobile-100 tab">Agendas</button></a><?php
            } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100 tab">Agendas</button><?php
            }

            if ( check_subtab_persmission( $dbc, 'agenda_meeting', ROLE, 'meeting' ) !== false ) { ?>
                <a href="meeting.php"><button type="button" class="btn brand-btn mobile-block mobile-100 tab active_tab">Meetings</button></a><?php
            } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100 tab">Meetings</button><?php
            } ?>

			<?php
				if(vuaed_visible_function($dbc, 'agenda_meeting') == 1) {
					echo '<a href="add_meeting.php" class="btn brand-btn mobile-block gap-bottom pull-right mobile-100-pull-right tab">Add Meeting</a>';
					echo '<span class="popover-examples pull-right" style="margin:7px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to create a Meeting."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
				}
			?>
		</div><?php

        $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='am_meeting'"));
        $note = $notes['note'];

        if ( !empty($note) ) { ?>
            <div class="notice double-gap-bottom popover-examples">
                <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
                <div class="col-sm-11">
                    <span class="notice-name">NOTE:</span>
                    <?= $note; ?>
                </div>
                <div class="clearfix"></div>
            </div><?php
        } ?>

        <form name="form_sites" method="post" action="" class="form-horizontal" role="form">
		<?php
            $search_staff = '';
            $search_client = '';
            $search_date = '';
            if(isset($_POST['search_user_submit'])) {
                $search_staff = $_POST['search_staff'];
                $search_client = $_POST['search_client'];
                $search_date = $_POST['search_date'];
            }
			if (isset($_POST['display_all_inventory'])) {
                $search_staff = '';
				$search_client = '';
                $search_date = '';
			}

			$rowsPerPage = 25;
			$pageNum = 1;

			if(isset($_GET['page'])) {
				$pageNum = $_GET['page'];
			}

			$offset = ($pageNum - 1) * $rowsPerPage;
        ?>

        <div class="form-group">
                <label for="site_name" class="col-sm-1 control-label">Staff:</label>
                <div class="col-sm-8" style="width:auto">
                <select data-placeholder="Select a Staff" name="search_staff" class="chosen-select-deselect form-control" width="380">
                    <option value=""></option>
                    <?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1"),MYSQLI_ASSOC));
                    foreach($staff_list as $staffid) {
                        $row_name = get_contact($dbc, $staffid);
                        echo '<option'.($search_staff == $staffid ? ' selected' : '').' value="'.$staffid.'">'.$row_name."</option>\n";
                    } ?>
                </select>
            </div>
		<?php if(strpos($value_config, ','."Business".',') !== FALSE) { ?>
					<label for="site_name" class="col-sm-1 control-label"><?= BUSINESS_CAT ?>:</label>
					<div class="col-sm-8" style="width:auto">
					<select data-placeholder="Select a <?= BUSINESS_CAT ?>" name="search_client" class="chosen-select-deselect form-control" width="380">
						<option value=""></option>
						<?php $businesses = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT(am.businessid) contactid, c.name FROM contacts c, agenda_meeting am WHERE am.businessid=c.contactid AND type='Meeting'"),MYSQLI_ASSOC));
						foreach($businesses as $businessid) {
							$row_name = get_client($dbc, $businessid);
							echo '<option'.($search_client == $businessid ? ' selected' : '').' value="'.$businessid.'">'.$row_name."</option>\n";
						} ?>
					</select>
				</div>
		<?php } else { ?>
			<div class="form-group">
					<label for="site_name" class="col-sm-1 control-label">Contact:</label>
					<div class="col-sm-8" style="width:auto">
					<select data-placeholder="Select a Contact" name="search_client" class="chosen-select-deselect form-control" width="380">
						<option value=""></option>
						<?php $contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT(am.businesscontactid) contactid, c.first_name, c.last_name FROM contacts c, agenda_meeting am WHERE am.businesscontactid=c.contactid AND type='Meeting'"),MYSQLI_ASSOC));
						foreach($contact_list as $contactid) {
							$row_name = get_contact($dbc, $contactid);
							echo '<option'.($search_client == $contactid ? ' selected' : '').' value="'.$contactid.'">'.$row_name."</option>\n";
						} ?>
					</select>
				</div>
		<?php } ?>
            &nbsp;&nbsp;&nbsp;
            Meeting Date: <input type="text" name="search_date" value="<?php echo $search_date; ?>" class="datepicker" />
        <!--<button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>-->
        <button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block gap-left">Search</button>
        <span class="popover-examples" style="margin:7px 5px 0 15px;"><a data-toggle="tooltip" data-placement="top" title="Click here to refresh the page with all Meetings."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
        <button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
        </div>

        <?php
        $staff_query = '';
        if($search_staff > 0) {
            $staff_query = "AND FIND_IN_SET($search_staff, companycontactid)";
        }
        if($search_client != '' && $search_date != '') {
            $query_check_credentials = "SELECT * FROM agenda_meeting WHERE type='Meeting' AND deleted = 0 AND '$search_client' IN (businessid, businesscontactid) $staff_query AND date_of_meeting = '$search_date' ORDER BY date_of_meeting DESC LIMIT $offset, $rowsPerPage";
			$query_check_credentials = "SELECT count(*) as numrows FROM agenda_meeting WHERE type='Meeting' $staff_query AND deleted = 0 AND '$search_client' IN (businessid, businesscontactid) AND date_of_meeting = '$search_date' ORDER BY date_of_meeting DESC";
        } elseif($search_client != '') {
            $query_check_credentials = "SELECT * FROM agenda_meeting WHERE type='Meeting' $staff_query AND deleted = 0 AND '$search_client' IN (businessid, businesscontactid) ORDER BY date_of_meeting DESC LIMIT $offset, $rowsPerPage";
			$query = "SELECT count(*) as numrows FROM agenda_meeting WHERE type='Meeting $staff_query AND deleted = 0' AND '$search_client' IN (businessid, businesscontactid) ORDER BY date_of_meeting DESC";
        } elseif($search_date != '') {
            $query_check_credentials = "SELECT * FROM agenda_meeting WHERE type='Meeting' $staff_query AND deleted = 0 AND date_of_meeting='$search_date' ORDER BY date_of_meeting DESC LIMIT $offset, $rowsPerPage";
			$query = "SELECT count(*) as numrows FROM agenda_meeting WHERE type='Meeting' $staff_query AND deleted = 0 AND date_of_meeting='$search_date' ORDER BY date_of_meeting DESC";
        } else {
            $query_check_credentials = "SELECT * FROM agenda_meeting WHERE type='Meeting' $staff_query AND deleted = 0 ORDER BY date_of_meeting DESC LIMIT $offset, $rowsPerPage";
			$query = "SELECT count(*) as numrows from agenda_meeting WHERE type='Meeting' $staff_query AND deleted = 0 ORDER BY date_of_meeting DESC";
        }

        $result = mysqli_query($dbc, $query_check_credentials);

        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
			echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
            echo "<div id='no-more-tables'><table class='table table-bordered table-striped'>";
            echo "<thead>";
            echo "<tr class='hidden-xs hidden-sm'>";
            echo '<th>'.(strpos($value_config, ','."Business".',') !== FALSE ? BUSINESS_CAT : 'Contact').'</th>';
            echo '<th>Date of Meeting</th>';
            echo '<th>Time of Meeting</th>';
            echo '<th>Location</th>';
            echo '<th>Staff Members</th>';
            echo '<th>Function</th>';
            echo "</tr>";
            echo "</thead>";
        } else {
            echo "<h2>No Record Found.</h2>";
        }
        while($row = mysqli_fetch_array( $result )) {
            echo "<tr>";
            echo '<td data-title="'.(strpos($value_config, ','."Business".',') !== FALSE ? BUSINESS_CAT : 'Contact').'">';
				echo (strpos($value_config, ','."Business".',') !== FALSE ? get_client($dbc, $row['businessid']) : get_contact($dbc, $row['businesscontactid'])) . '</td>';
            echo '<td data-title="Meeting Date">' . $row['date_of_meeting'] . '</td>';
            echo '<td data-title="Meeting Time">' . $row['time_of_meeting'].' - '.$row['end_time_of_meeting'] . '</td>';
            echo '<td data-title="Location">' . $row['location'] . '</td>';
            echo '<td data-title="Location">' .  get_multiple_contact($dbc, $row['companycontactid']). '</td>';
            echo '<td data-title="Function">';
            if(vuaed_visible_function($dbc, 'agenda_meeting') == 1) {
                if($row['status'] == 'Approve') {
                    echo '<a href=\'add_meeting.php?agendameetingid='.$row['agendameetingid'].'\'>Update</a>';
                } else if($row['status'] == 'Done') {
                    echo '<a href=\'add_meeting.php?agendameetingid='.$row['agendameetingid'].'\'>Review</a>';
                } else if($row['status'] == 'Pending') {
                    echo '<a href=\'add_meeting.php?agendameetingid='.$row['agendameetingid'].'\'>Edit</a>';
                }
                echo '<a href=\'../delete_restore.php?action=delete&page=meeting&agendameetingid='.$row['agendameetingid'].'\' onclick="return confirm(\'Are you sure?\')"> | Archive</a>';

				//echo '<a href=\'add_meeting.php?agendameetingid='.$row['agendameetingid'].'\'>Edit</a>';
            }
            echo '</td>';

            echo "</tr>";
        }
		if($num_rows > 0) {
			echo '</table></div>';
		}

		echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
        if(vuaed_visible_function($dbc, 'agenda_meeting') == 1) {
            echo '<a href="add_meeting.php" class="btn brand-btn mobile-block gap-bottom pull-right">Add Meeting</a>';
			echo '<span class="popover-examples pull-right" style="margin:7px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to create a Meeting."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
        }
        ?>
		</form>

	</div>
</div>

<?php include ('../footer.php'); ?>
