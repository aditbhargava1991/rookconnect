<?php
/*
Customer Listing
*/
include ('../include.php');
?>
<script type="text/javascript">
$(document).ready(function() {
	$('input.purchase_order_includer').on('change', function() {
		var id = $(this).attr('id');
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../ajax_all.php?fill=include_in_orders&type=po&name=services&value="+value+"&status="+id,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
		});
	});

	$('input.sales_order_includer').on('change', function() {
		var id = $(this).attr('id');
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../ajax_all.php?fill=include_in_orders&type=so&name=services&value="+value+"&status="+id,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
		});
	});

	$('input.point_of_sale_includer').on('change', function() {
		var id = $(this).attr('id');
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../ajax_all.php?fill=include_in_orders&type=pos&name=services&value="+value+"&status="+id,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
		});
	});
});
</script>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('services');
?>

<div class="container triple-pad-bottom">
    <div class="row">
		<div class="col-md-12">

        <div class="col-sm-10"><h1>Services Dashboard</h1></div>
		<div class="col-sm-2 double-gap-top">
			<?php
			if(config_visible_function($dbc, 'services') == 1) {
				echo '<a href="field_config_services.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
				echo '<span class="popover-examples list-inline pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
			}
			?>
        </div>
		<div class="clearfix"></div>

		<div class="notice double-gap-bottom popover-examples">
			<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
			<div class="col-sm-11"><span class="notice-name">NOTE:</span>
			In this section your business can outline all service headings and descriptions for quotes, ticketing systems, work orders, etc. Assigning your business services a Service Type, Category and Heading will enable reporting per service. Price points for services are added in the rate card section and may or may not be visible here. Services added here will display in the rate card; services added to the rate card may or may not be visible here.</div>
			<div class="clearfix"></div>
		</div>

        <form name="form_sites" method="post" action="" class="form-inline" role="form">
            <?php
				$sql = mysqli_query($dbc, "SELECT DISTINCT(category) FROM services WHERE category != '' ORDER BY `category`");
                while($row = mysqli_fetch_assoc($sql)){
                    $active_daily = '';
                    $row_cat = $row['category'];
                    $row_cat_subtab = str_replace ( ['&', '/', ',', ' ', '___', '__'], ['', '_', '', '_', '_', '_'], $row['category'] );

                    if((!empty($_GET['category'])) && ($_GET['category'] == $row_cat) && (!isset($_GET['currentlist']))) {
                        $active_daily = 'active_tab';
                    }

                    if ( check_subtab_persmission($dbc, 'services', ROLE, $row_cat_subtab) === true ) {
                        echo "<a href='services.php?category=".$row_cat."'><button type='button' class='btn brand-btn mobile-100 mobile-block ".$active_daily."' >".$row_cat."</button></a>&nbsp;&nbsp;";
                    } else {
                        echo '<button type="button" class="btn disabled-btn mobile-100 mobile-block">'. $row_cat .'</button>&nbsp;&nbsp;';
                    }
                }
                echo '<br><br>';
            ?>

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
                if(vuaed_visible_function($dbc, 'services') == 1) {
                    echo '<a href="add_services.php" class="btn brand-btn mobile-block pull-right double-gap-top">Add Service</a>'; ?>
					<span class="popover-examples double-gap-top pull-right pad-5"><a data-toggle="tooltip" data-placement="top" title="Click here to add a Service."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
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
                $query_check_credentials = "SELECT * FROM services WHERE deleted = 0 AND (service_type LIKE '%" . $vendor . "%' OR category ='$vendor' OR service_code ='$vendor' OR heading LIKE '%" . $vendor . "%') ORDER BY service_type, category, heading ASC";
                //$query = "SELECT count(*) as numrows FROM services WHERE deleted = 0 AND (service_type LIKE '%" . $vendor . "%' OR category ='$vendor' OR heading LIKE '%" . $vendor . "%') ORDER BY service_type, category, heading ASC ";
            } else {

                if(empty($_GET['category'])) {
                    $query_check_credentials = "SELECT * FROM services WHERE deleted = 0 AND category='' ORDER BY service_type, category, heading ASC LIMIT $offset, $rowsPerPage";
                    $query = "SELECT count(*) as numrows FROM services WHERE deleted = 0 AND category='' ORDER BY service_type, category, heading ASC";
                } else {
                    $category = $_GET['category'];
                    $query_check_credentials = "SELECT * FROM services WHERE deleted = 0 AND category='$category' ORDER BY service_type, category, heading ASC LIMIT $offset, $rowsPerPage";
                    $query = "SELECT count(*) as numrows FROM services WHERE deleted = 0 AND category='$category' ORDER BY service_type, category, heading ASC";
                }
            }

            $result = mysqli_query($dbc, $query_check_credentials);
			$rate_card_access = get_security($dbc, 'rate_card');

            $num_rows = mysqli_num_rows($result);
            if($num_rows > 0) {
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT services_dashboard FROM field_config"));
                $value_config = ','.$get_field_config['services_dashboard'].',';

                // Added Pagination //
                if($vendor == '') {
                    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                }
                // Pagination Finish //

                echo "<table class='table table-bordered'>";
                echo "<tr class='hidden-xs hidden-sm'>";
                    if (strpos($value_config, ','."Service Code".',') !== FALSE) {
                        echo '<th>Service Code</th>';
                    }
                    if (strpos($value_config, ','."Service Type".',') !== FALSE) {
                        echo '<th>Service Type</th>';
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
                    if (strpos($value_config, ','."Quantity".',') !== FALSE) {
                        echo '<th>Quantity</th>';
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
					if (strpos($value_config, ','."Purchase Order Price".',') !== FALSE) {
                        echo '<th>Purchase Order Price</th>';
                    }
					if (strpos($value_config, ','."Sales Order Price".',') !== FALSE) {
                        echo '<th>'.SALES_ORDER_NOUN.' Price</th>';
                    }
					if (strpos($value_config, ','."Include in Sales Orders".',') !== FALSE) {
                        echo '<th>Include in '.SALES_ORDER_TILE.'</th>';
                    }
					if (strpos($value_config, ','."Include in P.O.S.".',') !== FALSE) {
                        echo '<th>Include in '.POS_ADVANCE_TILE.'</th>';
                    }
					if (strpos($value_config, ','."Include in Purchase Orders".',') !== FALSE) {
                        echo '<th>Include in Purchase Orders</th>';
                    }
                    if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) {
                        echo '<th>Minimum Billable Hours</th>';
                    }
                    if (strpos($value_config, ','."Hourly Rate".',') !== FALSE) {
                        echo '<th>Hourly Rate</th>';
                    }
                    if (strpos($value_config, ','."Rate Card Rate".',') !== FALSE && $rate_card_access['visible'] > 0) {
                        echo '<th>Rate</th>';
                    }
                    if (strpos($value_config, ','."Rate Card".',') !== FALSE && $rate_card_access['edit'] > 0) {
                        echo '<th>Rate Card</th>';
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
                    if (strpos($value_config, ','."Checklist".',') !== FALSE) {
                        echo '<th>Checklist</th>';
                    }
                    echo '<th>Function</th>';
                    echo "</tr>";
            } else if(empty($_GET['category'])) {
                echo "<h2>Please select a category.</h2>";
            } else {
                echo "<h2>No Services Found.</h2>";
            }

            while($row = mysqli_fetch_array( $result ))
            {
                echo "<tr>";
                if (strpos($value_config, ','."Service Code".',') !== FALSE) {
                    echo '<td data-title="Code">' . $row['service_code'] . '</td>';
                }
                if (strpos($value_config, ','."Service Type".',') !== FALSE) {
       				echo '<td data-title="Type">' . $row['service_type'] . '</td>';
                }
                if (strpos($value_config, ','."Category".',') !== FALSE) {
                    echo '<td data-title="Category">' . $row['category'] . '</td>';
                }
                if (strpos($value_config, ','."Heading".',') !== FALSE) {
                    echo '<td data-title="Heading">' . $row['heading'] . '</td>';
                }
                if (strpos($value_config, ','."Name".',') !== FALSE) {
                    echo '<td data-title="Name">' . $row['name'] . '</td>';
                }
                if (strpos($value_config, ','."Fee".',') !== FALSE) {
                    echo '<td data-title="Fee">' . $row['fee'] . '</td>';
                }
                if (strpos($value_config, ','."Cost".',') !== FALSE) {
                    echo '<td data-title="Cost">' . $row['cost'] . '</td>';
                }
                if (strpos($value_config, ','."Quantity".',') !== FALSE) {
                    echo '<td data-title="Cost">' . $row['quantity'] . '</td>';
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
				if (strpos($value_config, ','."Purchase Order Price".',') !== FALSE) {
                    echo '<td data-title="Purchase Order Price">' . $row['purchase_order_price'] . '</td>';
                }
				if (strpos($value_config, ','."Sales Order Price".',') !== FALSE) {
                    echo '<td data-title="'.SALES_ORDER_NOUN.' Price">' . $row['sales_order_price'] . '</td>';
                }
				if (strpos($value_config, ','."Include in Sales Orders".',') !== FALSE) {
                        echo '<td data-title="Include in '.SALES_ORDER_TILE.'">';
						?><input type='checkbox' style='width:20px; height:20px;' <?php if($row['include_in_so'] !== '' && $row['include_in_so'] !== NULL) { echo "checked"; } ?> id='<?PHP echo $row['serviceid']; ?>'  name='' class='sales_order_includer' value='1'><br>
						<?php
						echo '</td>';
                    }
					if (strpos($value_config, ','."Include in P.O.S.".',') !== FALSE) {
                        echo '<td data-title="Include in P.O.S.">';
						?><input type='checkbox' style='width:20px; height:20px;' <?php if($row['include_in_pos'] !== '' && $row['include_in_pos'] !== NULL) { echo "checked"; } ?> id='<?PHP echo $row['serviceid']; ?>'  name='' class='point_of_sale_includer' value='1'><br>
						<?php
						echo '</td>';
                    }
					if (strpos($value_config, ','."Include in Purchase Orders".',') !== FALSE) {
                        echo '<td data-title="Include in Purchase Orders">';
						?><input type='checkbox' style='width:20px; height:20px;' <?php if($row['include_in_po'] !== '' && $row['include_in_po'] !== NULL) { echo "checked"; } ?> id='<?PHP echo $row['serviceid']; ?>'  name='' class='purchase_order_includer' value='1'><br>
						<?php
						echo '</td>';
                    }
                if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) {
                    echo '<td data-title="Min. Billable">' . $row['minimum_billable'] . '</td>';
                }
                if (strpos($value_config, ','."Hourly Rate".',') !== FALSE) {
                    echo '<td data-title="Hr. Rate">' . $row['hourly_rate'] . '</td>';
                }
				if (strpos($value_config, ','."Rate Card Rate".',') !== FALSE && $rate_card_access['visible'] > 0) {
					$rate = $dbc->query("SELECT `cust_price`, `uom` FROM `company_rate_card` WHERE `item_id`='{$row['serviceid']}' AND `tile_name` LIKE 'Services' AND `deleted`=0 AND `start_date` < DATE(NOW()) AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') > DATE(NOW())")->fetch_assoc();
					echo '<td data-title="Rate">$'.number_format($rate['cust_price'],2).' '.$rate['uom'].'</td>';
				}
				if (strpos($value_config, ','."Rate Card".',') !== FALSE && $rate_card_access['edit'] > 0) {
					echo '<td data-title="Rate Card"><a href="../Rate Card/ratecards.php?card=services&type=services&t='.$row['category'].'&status=add&id='.$row['serviceid'].'" onclick="">View Rate Card</a></td>';
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
                if (strpos($value_config, ','."Checklist".',') !== FALSE) {
                    echo '<td data-title="Checklist">' . implode('<br />',explode('#*#',$row['checklist'])) . '</td>';
                }

                echo '<td data-title="Function">';
                if(vuaed_visible_function($dbc, 'services') == 1) {
                echo '<a href=\'add_services.php?serviceid='.$row['serviceid'].'\'>Edit</a> | <a href=\'add_services.php?serviceid='.$row['serviceid'].'\'>View</a> | ';
				echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&serviceid='.$row['serviceid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
                }
                echo '</td>';

                echo "</tr>";
            }

            echo '</table></div>';
            // Added Pagination //
            if($vendor == '') {
                echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
            }
            // Pagination Finish //
            if(vuaed_visible_function($dbc, 'services') == 1) {
				echo '<a href="add_services.php" class="btn brand-btn mobile-block pull-right">Add Service</a>'; ?>
				<span class="popover-examples pull-right pad-5"><a data-toggle="tooltip" data-placement="top" title="Click here to add a Service."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
            }

            ?>
        </form>

        </div>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>
