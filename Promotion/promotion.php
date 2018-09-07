<?php
/*
Customer Listing
*/
include ('../include.php');
?>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('promotion');
?>

<div class="container triple-pad-bottom">
    <div class="row">
		<div class="col-md-12">

        <div class="col-sm-10">
			<h1>Promotions &amp; Coupons Dashboard</h1>
		</div>
		<div class="col-sm-2 double-gap-top">
			<?php
				if(config_visible_function($dbc, 'promotion') == 1) {
					echo '<a href="field_config_promotion.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
				}
			?>
        </div>

		<div class="clearfix"></div>

        <form name="form_sites" method="post" action="" class="form-inline" role="form">
            <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            All promotions (both active and expired) are displayed below. They list in order of Expiry Date. Only active (not expired) promotions will be available to be applied to customer invoices in the Check Out tile.</div>
            <div class="clearfix"></div>
            </div>

            <center>
            <div class="form-group">
                <label for="site_name" class="col-sm-5 control-label">Search By Any:</label>
                <div class="col-sm-6">
                <?php if(isset($_POST['search_vendor_submit'])) { ?>
                    <input type="text" name="search_vendor" class="form-control" value="<?php echo $_POST['search_vendor']?>">
                <?php } else { ?>
                    <input type="text" name="search_vendor" class="form-control">
                <?php } ?>
                </div>
            </div>

            &nbsp;<button type="submit" name="search_vendor_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
            <button type="submit" name="display_all_vendor" value="Display All" class="btn brand-btn mobile-block">Display All</button>
            </center>

            <?php
                if(vuaed_visible_function($dbc, 'promotion') == 1) {
                    echo '<a href="add_promotion.php" class="btn brand-btn mobile-block pull-right double-gap-top">Add Promotion</a>';
            }
                ?>

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
                $query_check_credentials = "SELECT * FROM promotion WHERE deleted = 0 AND (service_type LIKE '%" . $vendor . "%' OR category ='$vendor' OR heading LIKE '%" . $vendor . "%') LIMIT $offset, $rowsPerPage";
                $query = "SELECT count(*) as numrows FROM promotion WHERE deleted = 0 AND (service_type LIKE '%" . $vendor . "%' OR category ='$vendor' OR heading LIKE '%" . $vendor . "%')";
            } else {
                $query_check_credentials = "SELECT * FROM promotion WHERE deleted = 0 ORDER BY service_type LIMIT $offset, $rowsPerPage";
                $query = "SELECT count(*) as numrows FROM promotion WHERE deleted = 0 ORDER BY service_type";
            }

            $result = mysqli_query($dbc, $query_check_credentials);

            $num_rows = mysqli_num_rows($result);

            if($num_rows > 0) {
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT promotion_dashboard FROM field_config"));
                $value_config = ','.$get_field_config['promotion_dashboard'].',';

                // Added Pagination //
                echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                // Pagination Finish //

                echo "<table class='table table-bordered table-striped'>";
                    echo "<thead>";
                        echo "<tr class='hidden-sm hidden-xs'>";
                            if (strpos($value_config, ','."Service Type".',') !== FALSE) {
                                echo '<th>Service Type</th>';
                            }
                            if (strpos($value_config, ','."Category".',') !== FALSE) {
                                echo '<th>Category</th>';
                            }
                            if (strpos($value_config, ','."Heading".',') !== FALSE) {
                                echo '<th>Heading</th>';
                            }
                            if (strpos($value_config, ','."Expiry Date".',') !== FALSE) {
                                echo '<th>Expiry Date</th>';
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
                                echo '<th>Minimum Billable</th>';
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
                            if (strpos($value_config, ','."Times Used".',') !== FALSE) {
                                echo '<th># of Times Used</th>';
                            }
                            echo '<th>Function</th>';
                        echo "</tr>";
                    echo "</thead>";
            } else {
                echo "<h2>No Record Found.</h2>";
            }

            while($row = mysqli_fetch_array( $result ))
            {
                echo "<tr>";

                if (strpos($value_config, ','."Service Type".',') !== FALSE) {
       				echo '<td data-title="Type">' . $row['service_type'] . '</td>';
                }
                if (strpos($value_config, ','."Category".',') !== FALSE) {
                    echo '<td data-title="Category">' . $row['category'] . '</td>';
                }
                if (strpos($value_config, ','."Heading".',') !== FALSE) {
                    echo '<td data-title="Heading">' . $row['heading'] . '</td>';
                }
                if (strpos($value_config, ','."Expiry Date".',') !== FALSE) {
                    echo '<td data-title="Expiry Date">' . $row['expiry_date'] . '</td>';
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
                if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) {
                    echo '<td data-title="Est. Hours">' . $row['estimated_hours'] . '</td>';
                }
                if (strpos($value_config, ','."Actual Hours".',') !== FALSE) {
                    echo '<td data-title="Actual Hours">' . $row['actual_hours'] . '</td>';
                }
                if (strpos($value_config, ','."MSRP".',') !== FALSE) {
                    echo '<td data-title="MSRP">' . $row['msrp'] . '</td>';
                }
                if (strpos($value_config, ','."Times Used".',') !== FALSE) {
                    echo '<td data-title="# of Times Used">' . ( $row['times_used'] == NULL ? 0 : $row['times_used'] ) . '</td>';
                }

                echo '<td data-title="Function">';
                if(vuaed_visible_function($dbc, 'promotion') == 1) {
                echo '<a href=\'add_promotion.php?promotionid='.$row['promotionid'].'\'>Edit</a> | ';
				echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&promotionid='.$row['promotionid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
                }

                //echo '<a href=\'delete_restore.php?action=delete&promotionid='.$row['promotionid'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a>';
                echo '</td>';

                echo "</tr>";
            }

            echo '</table></div>';
            // Added Pagination //
            echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
            // Pagination Finish //
            if(vuaed_visible_function($dbc, 'promotion') == 1) {
            echo '<a href="add_promotion.php" class="btn brand-btn mobile-block pull-right">Add Promotion</a>';
            }

            ?>
        </form>

        </div>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>
