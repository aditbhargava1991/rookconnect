<?php
/*
Customer Listing
*/
include ('../include.php');
include ('../database_connection_htg.php');
$rookconnect = get_software_name();
?>
<script>
    function submitForm(thisForm) {
        if (!$('input[name="search_user_submit"]').length) {
            var input = $("<input>")
                        .attr("type", "hidden")
                        .attr("name", "search_user_submit").val("1");
            $('[name=form_sites]').append($(input));
        }

        $('[name=form_sites]').submit();
    }
</script>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('newsboard');
?>

<div class="container triple-pad-bottom">
    <div class="row">
		<div class="col-md-12">

		<h1 class=""><span class="popover-examples list-inline" style="margin:0 5px 0 12px;"><a data-toggle="tooltip" data-placement="top" title="A place where you can post internal messages/events for the entire company."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="30"></a></span>News Boards
        <?php
        if(config_visible_function($dbc, 'newsboard') == 1) {
            echo '<a href="field_config_newsboard.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><span class="popover-examples list-inline"><a class="pull-right" style="margin:-5px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span><br><br>';
        }
        ?>
		<div style="text-align:right;">
			<p>
				<?php $base_url = WEBSITE_URL; ?>
				<a href="<?php echo $base_url . '/newsboard.php'; ?>"> 
					<img src="<?php echo WEBSITE_URL; ?>/img/icons/switch-6.png" width="50px" class="switch_info_off1">
					<img src="<?php echo WEBSITE_URL; ?>/img/icons/switch-7.png" class="switch_info_on1" style='display:none;' width="50px">
				</a>
			</p>
		</div>
        </h1>
        
        <?php if ($rookconnect=='rook' || $rookconnect=='localhost') { ?>
            <div class="gap-left tab-container mobile-100-container">
                <a href="newsboard.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Local News Boards</button></a>
                <a href="newsboard_sw.php"><button type="button" class="btn brand-btn mobile-block mobile-100 active_tab">Softwarewide News Boards</button></a>
            </div>
        <?php } ?>

        <form name="form_sites" method="post" action="" class="form-inline" style="overflow:visible;" role="form">
            <?php
            $search_vendor = '';
            if(isset($_POST['search_user_submit'])) {
                $search_vendor = $_POST['search_vendor'];
					if($search_vendor !== '') {
						$search_any = "AND (ce.description LIKE '%" . $search_vendor . "%' OR ce.title LIKE '%" . $search_vendor . "%')";
					} else {
						$search_any = "";
					}
            }
            if (isset($_POST['display_all_inventory'])) {
                $search_vendor = '';
            }
            ?>
            <div class="form-group">
                <label for="search_vendor" class="col-sm-5 control-label">Search By Any:</label>
                <div class="col-sm-7">
                <?php if($search_vendor !== '') { ?>
                    <input type="text" name="search_vendor" class="form-control" value="<?php echo $_POST['search_vendor']?>" onchange="submitForm()">
                <?php } else { ?>
                    <input type="text" name="search_vendor" class="form-control" onchange="submitForm()">
                <?php } ?>
                </div>
            </div>
			<?php $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT newsboard_dashboard FROM field_config"));
                $value_config = ','.$get_field_config['newsboard_dashboard'].','; ?>

            <div class="form-group">
              <div class="col-sm-12">
                <!--<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Remember to fill in one of the fields before clicking Search."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>-->

				<span class="popover-examples list-inline" style="margin:0 2px 0 15px;"><a data-toggle="tooltip" data-placement="top" title="Refreshes the page to display all of the News Boards."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
              </div>
            </div>

            <br><br>

            <?php
            if(vuaed_visible_function($dbc, 'newsboard') == 1) {
                echo '<a href="add_newsboard.php" class="btn brand-btn mobile-block pull-right">Add News Board Post</a><span class="popover-examples list-inline pull-right" style="margin:7px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click to add a News Board post."><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span><br /><br /><br />';
            }
            ?>

            <div id="no-more-tables">

            <?php

            /* Pagination Counting */
            $rowsPerPage = 25;
            $pageNum = 1;

            if(isset($_GET['page'])) {
                $pageNum = $_GET['page'];
            }

            $offset = ($pageNum - 1) * $rowsPerPage;

            if($search_vendor !== '' ) {
                $query_check_credentials = "SELECT ce.* FROM newsboard ce WHERE ce.deleted = 0 AND ce.newsboard_type='Softwarewide' $search_any ORDER BY ce.newsboardid ASC LIMIT $offset, $rowsPerPage";
                $query = "SELECT count(ce.newsboardid) as numrows FROM newsboard ce WHERE ce.deleted = 0 AND ce.newsboard_type='Softwarewide' $search_any ORDER BY ce.newsboardid ASC";
            } else {
                $query_check_credentials = "SELECT * FROM newsboard WHERE deleted = 0 AND newsboard_type='Softwarewide' ORDER BY newsboardid DESC";
                $query = "SELECT count(*) as numrows FROM newsboard WHERE deleted = 0 AND newsboard_type='Softwarewide'";
            }
            $result = mysqli_query($dbc_htg, $query_check_credentials);
            
            $num_rows = mysqli_num_rows($result);
            if($num_rows > 0) {


                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT newsboard_dashboard FROM field_config"));
                $value_config = ','.$get_field_config['newsboard_dashboard'].',';

                // Added Pagination //
                echo display_pagination($dbc_htg, $query, $pageNum, $rowsPerPage);
                // Pagination Finish //

                echo "<table class='table table-bordered'>";
                echo "<tr class='hidden-sm hidden-xs'>";
                    if (strpos($value_config, ','."Staff".',') !== FALSE) {
                        echo '<th>Staff</th>';
                    }
                    if (strpos($value_config, ','."Title".',') !== FALSE) {
                        echo '<th>Title</th>';
                    }
                    if (strpos($value_config, ','."Issue Date".',') !== FALSE) {
                        echo '<th>Issue Date</th>';
                    }
                    if (strpos($value_config, ','."Expiry Date".',') !== FALSE) {
                        echo '<th>Expiry Date</th>';
                    }
                    if (strpos($value_config, ','."Reminder Date".',') !== FALSE) {
                        echo '<th>Reminder Date</th>';
                    }
                    if (strpos($value_config, ','."Uploader".',') !== FALSE) {
                        echo '<th>Header Image</th>';
                    }
                    if (strpos($value_config, ','."Description".',') !== FALSE) {
                        echo '<th>Description</th>';
                    }
                    echo '<th>Function</th>';
                    echo "</tr>";
            } else {
                echo "<h2>No Record Found.</h2>";
            }
            while($row = mysqli_fetch_array( $result ))
            {
                $style = '';
                if(date('Y-m-d') == $row['reminder_date']) {
                    $style = 'style = color:red;';
                }
                echo "<tr ".$style.">";
                $newsboardid = $row['newsboardid'];
                if (strpos($value_config, ','."Staff".',') !== FALSE) {
                    echo '<td data-title="Staff">' . get_staff($dbc, $row['contactid']) . '</td>';
                }
                if (strpos($value_config, ','."Title".',') !== FALSE) {
                    echo '<td data-title="Title">' . $row['title'] . '</td>';
                }
                if (strpos($value_config, ','."Issue Date".',') !== FALSE) {
                    echo '<td data-title="Issue Date">' . $row['issue_date'] . '</td>';
                }
                if (strpos($value_config, ','."Expiry Date".',') !== FALSE) {
                    echo '<td data-title="Expiry Date">' . $row['expiry_date'] . '</td>';
                }
                if (strpos($value_config, ','."Reminder Date".',') !== FALSE) {
                    echo '<td data-title="Reminder Date">' . $row['reminder_date'] . '</td>';
                }

                if (strpos($value_config, ','."Uploader".',') !== FALSE) {
                    echo '<td data-title="Documents">';
                    $newsboard_uploads1 = "SELECT * FROM newsboard_uploads WHERE newsboardid='$newsboardid' AND type = 'Document' ORDER BY certuploadid DESC";
                    $result1 = mysqli_query($dbc_htg, $newsboard_uploads1);
                    $num_rows1 = mysqli_num_rows($result1);
                    if($num_rows1 > 0) {
						while($row1 = mysqli_fetch_array($result1)) {
							$image = ( substr ( $row1['document_link'], 0, 4 ) == 'http' ) ? $row1['document_link'] : 'https://ffm.rookconnect.com/News Board/download/' . $row1['document_link'];
							echo '<a href="' . $image . '" target="_blank">' . $row1['document_link'] . '</a><br />';
                        }
                    }
                    echo '</td>';
                }
                
                if (strpos($value_config, ','."Description".',') !== FALSE) {
                    $desc = html_entity_decode ( $row['description'] );
					echo '<td data-title="Description">' . limit_text( $desc, 80 ) . '</td>';
                }
                echo '<td data-title="Function">';
                if(vuaed_visible_function($dbc, 'newsboard') == 1) {
                echo '<a href=\'add_newsboard.php?newsboardid='.$newsboardid.'\'>Edit</a> | ';
				echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&newsboardid='.$newsboardid.'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
                }
                echo '</td>';

                echo "</tr>";
            }

            echo '</table></div>';

            // Added Pagination //
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
            // Pagination Finish //

            if(vuaed_visible_function($dbc, 'newsboard') == 1) {
            echo '<a href="add_newsboard.php" class="btn brand-btn mobile-block pull-right">Add News Board Post</a><span class="popover-examples list-inline pull-right" style="margin:7px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click to add a News Board post."><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span>';
            }

            ?>
        </form>

        </div>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>
