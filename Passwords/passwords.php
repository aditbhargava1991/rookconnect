<?php
	/*
	 * Passwords
	 */
	include ('../include.php');
	error_reporting(0);
?>
</head>
<body>

<?php
	include_once ('../navigation.php');
    checkAuthorised('passwords');

	/* Get logged in user's role */
	if ( !empty ( $_GET[ 'level' ] ) ) {
		$level_url = $_GET[ 'level' ];

	} else {
		$contacterid	= $_SESSION['contactid'];
		$result			= mysqli_query ( $dbc, "SELECT `role` FROM `contacts` WHERE `contactid`='$contacterid'" );

		while ( $row = mysqli_fetch_assoc( $result ) ) {
			$role = $row[ 'role' ];
		}

		$level_url = (stripos(','.$role.',',',super,') !== false) ? 'admin' : $role;
	}
?>

<div class="container triple-pad-bottom">
    <div class="row">
		<div class="col-md-12">

        <div class="col-sm-10">
			<h1>Passwords Dashboard</h1>
		</div>
		<div class="col-sm-2 double-gap-top">
			<?php
				if(config_visible_function($dbc, 'passwords') == 1) {
					echo '<a href="field_config_passwords.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
				}
			?>
        </div>

		<div class="clearfix double-gap-bottom"></div>

        <form name="form_sites" method="post" action="" class="form-inline" role="form">

            <?php

            $category = '';
            if(!empty($_GET['category'])) {
                $category = $_GET['category'];
            }
			echo '<div class="tab-container mobile-100-container">';
            $tabs = get_config($dbc, 'password_category');
            $each_tab = explode(',', $tabs);

			foreach ($each_tab as $cat_tab) {
                /*
				 * Check subtab settings
				 * Function check_subtab_persmission( database_connection, tile_name, security_level, subtab_name )
				 */
				$display = check_subtab_persmission( $dbc, 'passwords', $level_url, $cat_tab );

				$active_daily = '';
				if ( ( !empty($_GET['category']) ) && ( $_GET['category'] == $cat_tab ) ) {
					$active_daily = 'active_tab';
				}

				/*
				$contactid = get_passwordconfig($dbc, $cat_tab, 'contactid');
				if (strpos($contactid, ','.$_SESSION['contactid'].',') !== FALSE) {
                    $active_daily = '';
                    if((!empty($_GET['category'])) && ($_GET['category'] == $cat_tab)) {
                        $active_daily = 'active_tab';
                    }
                    echo "<a href='passwords.php?category=".$cat_tab."'><button type='button' class='btn brand-btn mobile-block mobile-100 ".$active_daily."' >".$cat_tab."</button></a>&nbsp;&nbsp;";
                }
				*/

				/* Display all subtabs to superadmin 'super' (FFMAdmin) */
				if (strpos(','.ROLE.',',',super,') !== FALSE) {
					echo "<a href='passwords.php?category=".$cat_tab."'><button type='button' class='btn brand-btn mobile-block mobile-100 ".$active_daily."' >".$cat_tab."</button></a>&nbsp;&nbsp;";
				} else {
					$contactid = get_passwordconfig($dbc, $cat_tab, 'contactid');
					if ( strpos ( $contactid, ','.$_SESSION['contactid'].',') !== FALSE ) {
						if ( $display === TRUE ) {
							echo "<a href='passwords.php?category=".$cat_tab."'><button type='button' class='btn brand-btn mobile-block mobile-100 ".$active_daily."' >".$cat_tab."</button></a>&nbsp;&nbsp;";
						} else {
							echo "<a href='#'><button type='button' class='btn disabled-btn mobile-block mobile-100 ".$active_daily."' >".$cat_tab."</button></a>&nbsp;&nbsp;";
						}
					}
				}
            }
            ?>
			</div>
            <center>
            <div class="pad-top pad-bottom">
                <div class="form-group gap-right">
                    <label for="search_vendor" class="control-label">Search &nbsp; <span class="popover-examples list-inline">
					<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Search by heading, category, type, business, name, or description."><img src="../img/info.png" width="20"></a></span>:</label>
				</div>
				<div class="form-group gap-right">
                    <?php if(isset($_POST['search_vendor_submit'])) { ?>
                        <input type="text" name="search_vendor" class="form-control" value="<?php echo $_POST['search_vendor']?>">
                    <?php } else { ?>
                        <input type="text" name="search_vendor" class="form-control">
                    <?php } ?>
                </div>
                <div class="form-group gap-right">
                    <button type="submit" name="search_vendor_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
                </div>
                <div class="form-group gap-right">
                    <button type="submit" name="display_all_vendor" value="Display All" class="btn brand-btn mobile-block">Display All</button>
                </div>
                <?php
                if(vuaed_visible_function($dbc, 'passwords') == 1) {
                    echo '<a href="add_passwords.php" class="btn brand-btn mobile-block pull-right">Add Password</a>';
                }
                ?>
            </div>
            </center>

            <div id="no-more-tables">

            <?php
            //Search
            $vendor = '';
            if (isset($_POST['search_vendor_submit'])) {
                if (isset($_POST['search_vendor'])) {
                    $vendor = $_POST['search_vendor'];
                }
            }
            if (isset($_POST['display_all_vendor'])) {
                $vendor = '';
            }

            /* Pagination Counting */
            $rowsPerPage = 25;
            $pageNum = 1;

            if(isset($_GET['page'])) {
                $pageNum = $_GET['page'];
            }

            $offset = ($pageNum - 1) * $rowsPerPage;

            if($vendor != '') {
				$contact_list = search_contacts_table($dbc, $vendor, " AND `contactid` IN (SELECT `businessid` FROM `passwords`) ");
                $query_check_credentials = "SELECT p.`passwordid` contactid, c.`name`, c.`last_name`, c.`first_name` FROM passwords p LEFT JOIN `contacts` c ON p.businessid=c.contactid WHERE (p.deleted = 0 AND p.category = '$category') AND (p.businessid IN ($contact_list) OR p.password_type LIKE '%" . $vendor . "%' OR p.heading LIKE '%" . $vendor . "%' OR p.description LIKE '%" . $vendor . "%' OR p.name LIKE '%" . $vendor . "%' ) GROUP BY passwordid";
				$result = mysqli_query($dbc, $query_check_credentials);
				$pageNum = 1;
				$rowsPerPage = mysqli_num_rows($result);
				$offset = 0;
            } else {
                if(!empty($_GET['category'])) {
                    $query_check_credentials = "SELECT p.`passwordid` contactid, c.`name`, c.`last_name`, c.`first_name` FROM passwords p LEFT JOIN `contacts` c ON p.businessid=c.contactid WHERE p.deleted = 0 AND p.category='$category' ORDER BY p.password_type";
                } else {
                    $query_check_credentials = "SELECT p.`passwordid` contactid, c.`name`, c.`last_name`, c.`first_name` FROM passwords p LEFT JOIN `contacts` c ON p.businessid=c.contactid WHERE p.deleted = 0 ORDER BY p.password_type";
                }
            }

            $result = mysqli_query($dbc, $query_check_credentials);

            $num_rows = mysqli_num_rows($result);
			$query = "SELECT '$num_rows' numrows";
            if($num_rows > 0) {
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT passwords_dashboard FROM field_config"));
                $value_config = ','.$get_field_config['passwords_dashboard'].',';

                // Added Pagination //
                echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                // Pagination Finish //

                echo "<table class='table table-bordered table-striped'>";
                echo "<thead><tr class='hidden-xs hidden-sm'>";
                    if (strpos($value_config, ','."Business".',') !== FALSE) {
                        echo '<th>Business</th>';
                    }
                    if (strpos($value_config, ','."Password Code".',') !== FALSE) {
                        echo '<th>Password Code</th>';
                    }
                    if (strpos($value_config, ','."Password Type".',') !== FALSE) {
                        echo '<th>Password Type</th>';
                    }
                    if (strpos($value_config, ','."Category".',') !== FALSE) {
                        echo '<th>Category</th>';
                    }
                    if (strpos($value_config, ','."Heading".',') !== FALSE) {
                        echo '<th>Heading</th>';
                    }
                    if (strpos($value_config, ','."Name".',') !== FALSE) {
                        echo '<th>Name</th>';
                    }
                    if (strpos($value_config, ','."Fee".',') !== FALSE) {
                        echo '<th>Fee</th>';
                    }
                    if (strpos($value_config, ','."Cost".',') !== FALSE) {
                        echo '<th>Cost</th>';
                    }
                    if (strpos($value_config, ','."Description".',') !== FALSE) {
                        echo '<th>Description</th>';
                    }
                    if (strpos($value_config, ','."Quote Description".',') !== FALSE) {
                        echo '<th>Quote Description</th>';
                    }
                    if (strpos($value_config, ','."Invoice Description".',') !== FALSE) {
                        echo '<th>Invoice Description</th>';
                    }
                    if (strpos($value_config, ','."Ticket Description".',') !== FALSE) {
                        echo '<th>'.TICKET_NOUN.' Description</th>';
                    }
                    if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) {
                        echo '<th>Final Retail Price</th>';
                    }
                    if (strpos($value_config, ','."Admin Price".',') !== FALSE) {
                        echo '<th>Admin Price</th>';
                    }
                    if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) {
                        echo '<th>Wholesale Price</th>';
                    }
                    if (strpos($value_config, ','."Commercial Price".',') !== FALSE) {
                        echo '<th>Commercial Price</th>';
                    }
                    if (strpos($value_config, ','."Client Price".',') !== FALSE) {
                        echo '<th>Client Price</th>';
                    }
                    if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) {
                        echo '<th>Minimum Billable Hours</th>';
                    }
                    if (strpos($value_config, ','."Hourly Rate".',') !== FALSE) {
                        echo '<th>Hourly Rate</th>';
                    }
                    if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) {
                        echo '<th>Estimated Hours</th>';
                    }
                    if (strpos($value_config, ','."Actual Hours".',') !== FALSE) {
                        echo '<th>Actual Hours</th>';
                    }
                    if (strpos($value_config, ','."MSRP".',') !== FALSE) {
                        echo '<th>MSRP</th>';
                    }
                    echo '<th>Function</th>';
				echo "</tr></thead>";

				$password_list = sort_contacts_array(mysqli_fetch_all($result,MYSQLI_ASSOC));
				foreach($password_list as $id) {
					$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `passwords` WHERE `passwordid`='$id'"));
					echo "<tr>";
						if (strpos($value_config, ','."Business".',') !== FALSE) {
							echo '<td data-title="Business">' . get_client($dbc, $row['businessid']) .'</td>';
						}
						if (strpos($value_config, ','."Password Code".',') !== FALSE) {
							echo '<td data-title="Code">' . $row['password_code'] . '</td>';
						}
						if (strpos($value_config, ','."Password Type".',') !== FALSE) {
							echo '<td data-title="Type">' . $row['password_type'] . '</td>';
						}
						if (strpos($value_config, ','."Category".',') !== FALSE) {
							echo '<td data-title="Category">' . $row['category'] . '</td>';
						}
						if (strpos($value_config, ','."Heading".',') !== FALSE) {
							echo '<td data-title="Heading">' . $row['heading'] . '</td>';
						}
						if (strpos($value_config, ','."Name".',') !== FALSE) {
							echo '<td data-title="Name">' . decryptIt($row['name']) . '</td>';
						}
						if (strpos($value_config, ','."Fee".',') !== FALSE) {
							echo '<td data-title="Fee">' . $row['fee'] . '</td>';
						}
						if (strpos($value_config, ','."Cost".',') !== FALSE) {
							echo '<td data-title="Cost">' . $row['cost'] . '</td>';
						}
						if (strpos($value_config, ','."Description".',') !== FALSE) {
							echo '<td data-title="Description">' . html_entity_decode($row['description']) . '</td>';
						}
						if (strpos($value_config, ','."Quote Description".',') !== FALSE) {
							echo '<td data-title="Quote Desc">' . html_entity_decode($row['quote_description']) . '</td>';
						}
						if (strpos($value_config, ','."Invoice Description".',') !== FALSE) {
							echo '<td data-title="Invoice Desc">' . html_entity_decode($row['invoice_description']) . '</td>';
						}
						if (strpos($value_config, ','."Ticket Description".',') !== FALSE) {
							echo '<td data-title="'.TICKET_NOUN.' Desc">' . html_entity_decode($row['ticket_description']) . '</td>';
						}
						if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) {
							echo '<td data-title="Retail">' . $row['final_retail_price'] . '</td>';
						}
						if (strpos($value_config, ','."Admin Price".',') !== FALSE) {
							echo '<td data-title="Admin Price">' . $row['admin_price'] . '</td>';
						}
						if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) {
							echo '<td data-title="Wholesale">' . $row['wholesale_price'] . '</td>';
						}
						if (strpos($value_config, ','."Commercial Price".',') !== FALSE) {
							echo '<td data-title="Comm. Price">' . $row['commercial_price'] . '</td>';
						}
						if (strpos($value_config, ','."Client Price".',') !== FALSE) {
							echo '<td data-title="Client Price">' . $row['client_price'] . '</td>';
						}
						if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) {
							echo '<td data-title="Min. Billable">' . $row['minimum_billable'] . '</td>';
						}
						if (strpos($value_config, ','."Hourly Rate".',') !== FALSE) {
							echo '<td data-title="Hr. Rate">' . $row['hourly_rate'] . '</td>';
						}
						if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) {
							echo '<td data-title="Est. Hours">' . $row['estimated_hours'] . '</td>';
						}
						if (strpos($value_config, ','."Actual Hours".',') !== FALSE) {
							echo '<td data-title="Actual Hours">' . $row['actual_hours'] . '</td>';
						}
						if (strpos($value_config, ','."MSRP".',') !== FALSE) {
							echo '<td data-title="MSRP">' . $row['msrp'] . '</td>';
						}

						echo '<td data-title="Function">';
						if(vuaed_visible_function($dbc, 'passwords') == 1) {
						echo '<a href=\'add_passwords.php?passwordid='.$row['passwordid'].'\'>Edit</a> | ';
						echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&passwordid='.$row['passwordid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
						}
						echo '</td>';

					echo "</tr>";
				}

				echo '</table></div>';

				// Added Pagination //
				echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
				// Pagination Finish //
            } else {
                echo "<h2>No Record Found.</h2>";
            }

            if(vuaed_visible_function($dbc, 'passwords') == 1) {
            echo '<a href="add_passwords.php" class="btn brand-btn mobile-block pull-right">Add Password</a>';
            }

            ?>
        </form>

        </div>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>
