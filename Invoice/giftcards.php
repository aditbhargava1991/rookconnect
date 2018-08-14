<?php
	/*
	 * Gift Cards for POS Touch
	 */

	error_reporting(0);
	include ('../include.php');
?>
</head>

<body><?php
	include_once ('../navigation.php');
if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
} else {
    checkAuthorised('check_out');
} ?>

	<div class="container triple-pad-bottom">
		<div class="row">

		<h1>Gift Cards	Dashboard</h1>
        <?php if(config_visible_function($dbc, (FOLDER_NAME == 'posadvanced' ? 'posadvanced' : 'check_out')) == 1) {
            echo '<a href="field_config_invoice.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
        } ?>

			<div class="clearfix"></div>
			<?php include('tile_tabs.php'); ?>

			<form name="form_coupons" method="post" action="" class="form-inline">
				<div class="pad-top pad-bottom">
					<div class="form-group gap-right">
						<label for="search_vendor" class="control-label">Search By Any:</label>
						<input type="text" name="search_term" class="form-control" value="<?php echo (isset($_POST['search_submit'])) ? $_POST['search_term'] : ''; ?>">
					</div>
					<div class="form-group gap-right double-gap-top">
						<button type="submit" name="search_submit" value="Search" class="btn brand-btn mobile-block mobile-100">Search</button>
					</div>
					<div class="form-group gap-right double-gap-top">
						<button type="submit" name="display_all_submit" value="Display All" class="btn brand-btn mobile-block mobile-100">Display All</button>
					</div>
				</div>

				<div class="gap-top double-gap-bottom clearfix">
					<div class="mobile-100-container"><a href="add_giftcards.php" class="btn brand-btn mobile-block pull-right mobile-100-pull-right">Add Gift Cards</a></div>
				</div>

				<div id="no-more-tables"><?php
					// Search
					$search_term = '';
					if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_submit']) ) {
						$search_term = ( !empty ($_POST['search_term']) ) ? filter_var ($_POST['search_term'], FILTER_SANITIZE_STRING) : '';
					} else {
						$search_term = '';
					}

					/* Pagination Counting */
					$rowsPerPage = 25;
					$pageNum = 1;

					if ( isset($_GET['page']) ) {
						$pageNum = $_GET['page'];
					}

					$offset = ($pageNum - 1) * $rowsPerPage;

					if ( $search_term == '' ) {
						$query_check_credentials = "SELECT * FROM `pos_giftcards` WHERE `deleted` != 1 LIMIT $offset, $rowsPerPage";
						$query = "SELECT COUNT(*) AS numrows FROM `pos_giftcards` WHERE `deleted` != 1";
					} else {
						$query_check_credentials = "SELECT * FROM `pos_giftcards` WHERE `description` LIKE '%{$search_term}%' AND `deleted` != 1 ORDER BY `expiry_date` DESC LIMIT $offset, $rowsPerPage";
						$query = "SELECT COUNT(*) AS numrows FROM `pos_giftcards` WHERE `description` LIKE '%{$search_term}%' AND `deleted` != 1 ORDER BY `expiry_date` DESC";
					}

					$result		= mysqli_query($dbc, $query_check_credentials);
					$num_rows	= ($result) ? mysqli_num_rows($result) : 0;

					if ( $num_rows > 0 ) {

						echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);

						$get_field_config	= mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `pos_dashboard` FROM `field_config`" ) );
						$value_config		= ',' . $get_field_config['pos_dashboard'] . ',';

						echo '<table class="table table-bordered">';
							echo '<tr class="hidden-xs hidden-sm">';
									if (strpos($value_config, ',Gift Card ID,') !== FALSE )
										echo '<th>Gift Card ID</th>';
									if (strpos($value_config, ',Gift Card Number,') !== FALSE )
										echo '<th>Gift Card Number</th>';
									if (strpos($value_config, ',Gift Card Value,') !== FALSE )
										echo '<th>Gift Card Value</th>';
									if (strpos($value_config, ',Created For,') !== FALSE )
										echo '<th>Created For</th>';
									if (strpos($value_config, ',Created By,') !== FALSE )
										echo '<th>Created By</th>';
									if (strpos($value_config, ',Issue Date GF,') !== FALSE )
										echo '<th>Issue Date</th>';
									if (strpos($value_config, ',Expiry Date GF,') !== FALSE )
										echo '<th>Expiry Date</th>';
									if (strpos($value_config, ',Status_GF,') !== FALSE )
										echo '<th>Used Amount</th>';
									if (strpos($value_config, ',Description_GF,') !== FALSE )
										echo '<th>Description</th>';
									echo '<th>Function</th>';
							echo '</tr>';

							while ( $row = mysqli_fetch_array($result) ) {
								echo '<tr>';
									if (strpos($value_config, ',Gift Card ID,') !== FALSE )
										echo '<td data-title="Gift Card ID">' . $row['posgiftcardsid'] . '</td>';
									if (strpos($value_config, ',Gift Card Number,') !== FALSE )
										echo '<td data-title="Gift Card Number">' . $row['giftcard_number'] . '</td>';
									if (strpos($value_config, ',Gift Card Value,') !== FALSE )
										echo '<td data-title="Gift Card Value"> $' . $row['value'] . '</td>';
									if (strpos($value_config, ',Created For,') !== FALSE )
										echo '<td data-title="Created For">' . get_contact($dbc, $row['created_for']) . '</td>';
									if (strpos($value_config, ',Created By,') !== FALSE )
										echo '<td data-title="Created By">' . get_contact($dbc, $row['created_by']) . '</td>';
									if (strpos($value_config, ',Issue Date GF,') !== FALSE )
										echo '<td data-title="Issue Date">' . $row['issue_date'] . '</td>';
									if (strpos($value_config, ',Expiry Date GF,') !== FALSE )
										echo '<td data-title="Expiry Date">' . $row['expiry_date'] . '</td>';
									if (strpos($value_config, ',Status_GF,') !== FALSE ) {
										echo '<td data-title="Used Amount">$'.$row['used_value'].'</td>';
									}
									if (strpos($value_config, ',Description_GF,') !== FALSE) {
										echo '<td data-title="Description">'.html_entity_decode($row['description']).'</td>';
									}
									echo '
										<td data-title="Function" width="16%" nowrap>
											<a href="giftcard_receipt.php?giftcard='.$row['posgiftcardsid'].'"><img class="inline-img" src="../img/pdf.png">Receipt</a> |
											<a href="add_giftcards.php?giftcardid=' . $row['posgiftcardsid'] . '" title="Edit this submission">Edit</a> |
											<a href="../delete_restore.php?action=delete&posgiftcardsid=' . $row['posgiftcardsid'] . '" onclick="return confirm(\'Are you sure you want to delete?\')" title="Delete this submission">Delete</a>
										</td>';
								echo '</tr>';
							}
						echo '</table>';

						echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);

					} else {
						echo '<h2>No Records Found.</h2>';
					} ?>

				</div><!-- #no-more-tables -->

				<div class="double-gap-top clearfix">
					<div class="mobile-100-container"><a href="add_giftcards.php" class="btn brand-btn mobile-block pull-right mobile-100-pull-right">Add Gift Cards</a></div>
				</div>

			</form>

		</div><!-- .row -->
	</div><!-- .container -->

<?php include ('../footer.php'); ?>
