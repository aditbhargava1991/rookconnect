<?php
	/*
	 * Software Guide Dashboard
	 */
	error_reporting(0);
	if(!isset($_GET['from_manual']) && $_GET['from_manual'] != 1)
	include ('../include.php');
    include ('check_security.php');
    include ('../database_connection_htg.php');
    $rookconnect = get_software_name();
?>

</head>
<body>

<?php
	include_once ('../navigation.php');
	checkAuthorised('how_to_guide');
?>

<div class="container">
	<div class="row">

        <div class="col-sm-10">
			<h1>All Software Guides Dashboard</h1>
		</div>
		<div class="col-sm-2 double-gap-top">
			<?php
				if ( config_visible_function ( $dbc, 'how_to_guide' ) == 1 ) {
					if(isset($_GET['maintype'])) {
						echo '<a href="'.WEBSITE_URL.'/How To Guide/field_config_guides.php?maintype=htg" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
					}
					else {
						echo '<a href="field_config_guides.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
					}
					echo '<span class="popover-examples list-inline pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
				}
			?>
        </div>

		<div class="clearfix triple-gap-bottom"></div>

        <div class="tab-container">
            <div class="tab pull-left"><a href="guides_dashboard.php" class="btn active_tab text-uppercase">Software Guides</a></div><?php
            if ( $rookconnect=='rook' || $rookconnect=='localhost' ) { ?>
                <div class="tab pull-left"><a href="notes.php" class="btn brand-btn text-uppercase">Notes</a></div><?php
            } ?>
            <div class="clearfix"></div>
        </div><?php

        if ( config_visible_function ( $dbc, 'how_to_guide' ) == 1 ) { ?>
            <div class="tab-container"><?php
                if(isset($_GET['maintype'])) {
                    echo '<div class="tab pull-right"><a href="'.WEBSITE_URL.'/How To Guide/add_edit_guide.php?maintype='.$_GET['maintype'].'" class="btn brand-btn mobile-block">Add Software Guide</a></div>';
                }
                else {
                    echo '<div class="tab pull-right"><a href="add_edit_guide.php" class="btn brand-btn">Add Software Guide</a></div>';
                } ?>
                <div class="clearfix"></div>
            </div><?php
        } ?>

        <div class="no-more-tables clearfix">
			<?php
				/* Pagination Counting */
				$rowsPerPage	= 25;
				$pageNum		= 1;

				if ( isset ( $_GET['page'] ) ) {
					$pageNum = $_GET['page'];
				}

				$offset = ($pageNum - 1) * $rowsPerPage;

				if ( isset ( $_GET['page'] ) ) {
					$query_general = "SELECT * FROM `how_to_guide` WHERE `deleted`=0 ORDER BY `tile` LIMIT $offset, $rowsPerPage";
					$query_pagination = "SELECT COUNT(`guideid`) AS numrows FROM `how_to_guide` WHERE `deleted`=0 ORDER BY `tile`";
				} else {
					$query_general = "SELECT * FROM `how_to_guide` WHERE `deleted`=0 ORDER BY `tile` LIMIT $offset, $rowsPerPage";
					$query_pagination = "SELECT COUNT(`guideid`) AS numrows FROM `how_to_guide` WHERE `deleted`=0 ORDER BY `tile`";
				}

				$results_general	= mysqli_query ( $dbc_htg, $query_general );
				$num_rows_general	= mysqli_num_rows ( $results_general );

				if ( $num_rows_general > 0 ) {

					// Add Pagination
					if ( isset ( $query_pagination ) ) {
						echo display_pagination($dbc_htg, $query_pagination, $pageNum, $rowsPerPage);
					}

					$get_field_config = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `how_to_guide_dashboard` FROM `field_config`" ) );
					$value_config = ',' . $get_field_config['how_to_guide_dashboard'] . ',';

					echo '<table class="table table-bordered">';
						echo '<tr class="hidden-xs hidden-sm">';
							if ( strpos ( $value_config, ',Tile,' ) !== FALSE ) {
								echo '<th width="15%">Tile</th>';
							}
							if ( strpos ( $value_config, ',Subtab,' ) !== FALSE) {
								echo '<th width="18%">Sub Tab / Heading</th>';
							}
							if ( strpos ( $value_config, ',Order,' ) !== FALSE) {
								echo '<th width="7%">Order</th>';
							}
							if ( strpos ( $value_config, ',Description,' ) !== FALSE) {
								echo '<th width="43%">Description</th>';
							}
							if ( strpos ( $value_config, ',Image,' ) !== FALSE) {
								echo '<th width="7%">Image</th>';
							}
							if ( strpos ( $value_config, ',Function,' ) !== FALSE) {
								echo '<th width="10%">Function</th>';
							}
						echo "</tr>";

						while ( $row = mysqli_fetch_assoc ( $results_general ) ) {
							echo "<tr>";
								if ( strpos ( $value_config, ',Tile,' ) !== FALSE ) {
									echo '<td data-title="Tile">' . $row['tile'] . '</td>';
								}
								if ( strpos ( $value_config, ',Subtab,' ) !== FALSE ) {
									echo '<td data-title="Sub Tab">' . $row['subtab'] . '</td>';
								}
								if ( strpos ( $value_config, ',Order,' ) !== FALSE ) {
									echo '<td data-title="Order" align="center">' . $row['sort_order'] . '</td>';
								}
								if ( strpos ( $value_config, ',Description,' ) !== FALSE ) {
									echo '<td data-title="Description">' . substr ( html_entity_decode ( $row['description'] ), 0, 120 ) . ' ...</td>';
								}
								if ( strpos ( $value_config, ',Image,' ) !== FALSE ) {
									if ( !empty ( $row['image'] ) ) {
										echo '<td data-title="Image"><a href="'.$row['image_full_path'].'" target="view">View</a></td>';
									} else {
										echo '<td data-title="Image" align="center">-</td>';
									}
								}
								if ( strpos($value_config, ',Function,')!==FALSE && ($rookconnect=='rook' || $rookconnect=='localhost') ) {
									echo '<td data-title="Function">';
										echo '<a href="view_guide.php?guideid='.$row['guideid'].'&page='.$pageNum.'">View</a> | ';
                                        echo '<a href="add_edit_guide.php?guideid='. $row['guideid'] .'">Edit</a> | ';
										echo '<a href="../delete_restore.php?action=delete&guideid='.$row['guideid'].'&page='.$pageNum.'" onclick="return confirm(\'Are you sure you want to delete?\')">Archive</a>';
									echo '</td>';
								} else {
                                    echo '<td data-title="Function">';
										echo '<a href="view_guide.php?guideid='.$row['guideid'].'&page='.$pageNum.'">View</a>';
									echo '</td>';
                                }
							echo "</tr>";
						} //while
					echo '</table>';

					if ( config_visible_function ( $dbc, 'how_to_guide' ) == 1 ) {
						if(isset($_GET['maintype'])) {
							echo '<a href="'.WEBSITE_URL.'/How To Guide/add_edit_guide.php?maintype='.$_GET['maintype'].'" class="btn brand-btn mobile-block pull-right">Add Software Guide</a>';
						}
						else {
							echo '<a href="add_edit_guide.php" class="btn brand-btn mobile-block pull-right">Add Software Guide</a>';
						}
					}

					// Add Pagination
					if ( isset ( $query_pagination ) ) {
						echo display_pagination($dbc_htg, $query_pagination, $pageNum, $rowsPerPage);
					}

				} else {
					echo "<h2>No Records Found.</h2>";
				}
			?>
        </div><!-- .no-more-tables -->

    </div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>