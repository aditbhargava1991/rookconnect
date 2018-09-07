<?php
/*
Inventory Listing
*/
error_reporting(0);
include ('../include.php');
checkAuthorised('vpl');

$filter_query = '';
$page_title = "Vendor Price List";
if($_GET['vendorid'] > 0) {
	$filter_query .= " AND `vendorid` = '".$_GET['vendorid']."'";
	$page_title .= ": ".(!empty(get_client($dbc, $_GET['vendorid'])) ? get_client($dbc, $_GET['vendorid']) : get_contact($dbc, $_GET['vendorid']));
}
if(!empty($_GET['vpl_name'])) {
	$filter_query .= " AND `vpl_name` = '".$_GET['vpl_name']."'";
	$page_title .= " - ".$_GET['vpl_name'];
}
?>

<div class="standard-body-title">
	<h3><?= $page_title ?></h3>
</div>
<div class="standard-body-content pad-10">
	<form name="form_sites" method="post" action="" class="form-inline" role="form">

	    <?php
			echo "<div class='mobile-100-container double-gap-bottom'>";

			$dropdownornot ='';
				$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_category_dropdown_vpl'"));
				if($get_config['configid'] > 0) {
					$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='show_category_dropdown_vpl'"));
					if($get_config['value'] == '1') {
						$dropdownornot = 'true';
					}
				}
		    $sort_accordions = get_config($dbc, 'vpl_sort_accordions');

	        $category = $_GET['category'];
	        $tabs = get_config($dbc, 'vpl_tabs');
	        $each_tab = explode(',', $tabs);

	        $active_all = '';
	        $active_bom = '';
	        if(empty($_GET['category']) || $_GET['category'] == 'Top' && $currentlist != 'on') {
	            $active_all = 'active_tab';
	        }
	        echo '<span class="popover-examples" style="margin:2px 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Filter the Vendor Price List by the Last 25 items added."><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span><a href="?vpl=1&vendorid='.$_GET['vendorid'].'&vpl_name='.$_GET['vpl_name'].'&category=Top"><button type="button" class="btn brand-btn mobile-block mobile-100 '.$active_all.'">'.($sort_accordions == 1 ? 'All Items' : 'Last 25 Added').'</button></a>&nbsp;&nbsp;';
			if($dropdownornot !== 'true') {
	        foreach ($each_tab as $cat_tab) {
	            $active_daily = '';
	            if((!empty($_GET['category'])) && ($_GET['category'] == $cat_tab) && (!isset($_GET['currentlist']))) {
	                $active_daily = 'active_tab';
	            }
	            echo "<a href='?vpl=1&vendorid=".$_GET['vendorid']."&vpl_name=".$_GET['vpl_name']."&category=".$cat_tab."'><button type='button' class='btn brand-btn mobile-100 mobile-block ".$active_daily."' >".$cat_tab."</button></a>&nbsp;&nbsp;";
	        }
			} else {	?>
			<div class="form-group  mobile-100 cate_fitter" style='width:100%; '>
				<select name="search_category" class="chosen-select-deselect form-control category_actual" onchange="location = this.value;">
				  <option value="" selected>Select a Category</option>
				  <?php
					$sql = mysqli_query($dbc, "SELECT * FROM vendor_price_list WHERE deleted = 0 GROUP BY category ORDER BY IF(category RLIKE '^[a-z]', 1, 2), category, IF(name RLIKE '^[a-z]', 1, 2), name");
					while($row = mysqli_fetch_assoc($sql)){
						$active_daily = '';
						$cat_tab = $row['category'];
						if((!empty($_GET['category'])) && ($_GET['category'] == $cat_tab) && (!isset($_GET['currentlist']))) {
							$active_daily = 'selected';
						}
						echo "<option value='?vpl=1&vendorid=".$_GET['vendorid']."&vpl_name=".$_GET['vpl_name']."&category=".$cat_tab."' ".$active_daily." >".$cat_tab."</option>";
					}
				  ?>
				</select>
			</div>
				<?php
			}
	        if($_GET['category'] == 'bom') {
	            $active_bom = ' active_tab';
	        }

	        //echo display_filter('inventory.php');
	    ?>
		</div>

	    <center>
	    <div class="form-group">
	        <label for="site_name" class="col-sm-5 control-label">Search By Any:</label>
	        <div class="col-sm-6">
				<?php if(isset($_POST['search_inventory_submit'])) { ?>
					<input  type="text" name="search_inventory" value="<?php echo $_POST['search_inventory']?>" class="form-control">
				<?php } else { ?>
					<input type="text" name="search_inventory" class="form-control">
				<?php } ?>
	        </div>
	    </div>
	    &nbsp;
			<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Click this once you have typed in the Search By Any field."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<button type="submit" name="search_inventory_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
			<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="This refreshes the page to view all Vendor Price List."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
	    </center>

	    <?php
		$impexp_or_not ='';
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_impexp_vpl'"));
		if($get_config['configid'] > 0) {
			$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='show_impexp_vpl'"));
			if($get_config['value'] == '1') {
				$impexp_or_not = 'true';
			}
		}

	    if(vuaed_visible_function($dbc, 'inventory') == 1) {
	        if($_GET['category'] != 'Top') {
			    echo '<a href="?vpl=1&vendorid='.$_GET['vendorid'].'&vpl_name='.$_GET['vpl_name'].'&inventoryid='.$row['inventoryid'].'&category='.$category.'" class="double-gap-top btn brand-btn mobile-block gap-bottom pull-right">Add Product</a>';
	        }
	        ?>
			<span class="popover-examples double-gap-top pull-right pad-5"><a data-toggle="tooltip" data-placement="top" title="You must be in a category before you can add a product."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php

			if($impexp_or_not == 'true') {
				echo '<a href="?vpl=1&impexp=1" class="double-gap-top btn brand-btn mobile-block gap-bottom pull-right">Import/Export</a>'; ?>
				<span class="popover-examples double-gap-top pull-right pad-5"><a data-toggle="tooltip" data-placement="top" title="Click here to Import or Export a product(s)."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
			}
		}
	  if (isset($_GET['order_list'])) {  ?>
					<div class="col-lg-1 col-md-3 col-sm-12 col-xs-12 pull-sm-right pull-xs-right" style="padding-right:12px;">
						<div class='selectall selectbutton sel2' title='This will select all PDFs on the current page.' style='cursor:pointer;text-decoration:underline;'>Select All</div>
					</div>
	  <?php } ?>
	<div id="no-more-tables">
		<?php
		// Display Pager
		$inventory = '';
		if (isset($_POST['search_inventory_submit'])) {
			$inventory = $_POST['search_inventory'];
	        if (isset($_POST['search_inventory'])) {
	            $inventory = $_POST['search_inventory'];
	        }
	      //  if ($_POST['search_category'] != '') {
	          //  $inventory = $_POST['search_category'];
	        //}
		}
		if (isset($_POST['display_all_inventory'])) {
			$inventory = '';
		}
		$rowsPerPage = ITEMS_PER_PAGE;
		$pageNum = 1;

		if(isset($_GET['page'])) {
			$pageNum = $_GET['page'];
		}

		$offset = ($pageNum - 1) * $rowsPerPage;

	    /* Pagination Counting */
	    $rowsPerPage = 25;
	    $pageNum = 1;

	    if(isset($_GET['page'])) {
	        $pageNum = $_GET['page'];
	    }

	    $offset = ($pageNum - 1) * $rowsPerPage;

	    $query_offset = '';
	    if($sort_accordions != 1) {
			$query_offset = " LIMIT $offset, $rowsPerPage";
	    }

		if($inventory != '') {
			$query_check_credentials = "SELECT * FROM vendor_price_list WHERE deleted=0 AND (name LIKE '%" . $inventory . "%' OR code LIKE '%" . $inventory . "%' OR part_no LIKE '%" . $inventory . "%' OR category = '$inventory' OR sub_category LIKE '%" . $inventory . "%' OR description LIKE '%" . $inventory . "%' OR purchase_cost LIKE '%" . $inventory . "%' OR min_bin LIKE '%" . $inventory . "%' OR date_of_purchase LIKE '%" . $inventory . "%') ".$filter_query.$query_offset;
	        $query = "SELECT count(*) as numrows FROM vendor_price_list WHERE deleted=0 AND (name LIKE '%" . $inventory . "%' OR code LIKE '%" . $inventory . "%' OR part_no LIKE '%" . $inventory . "%' OR category = '$inventory' OR sub_category LIKE '%" . $inventory . "%' OR description LIKE '%" . $inventory . "%' OR purchase_cost LIKE '%" . $inventory . "%' OR min_bin LIKE '%" . $inventory . "%' OR date_of_purchase LIKE '%" . $inventory . "%')".$filter_query;
		} else {
			if((empty($_GET['category'])) || ($_GET['category'] == 'Top')) {
	            $query_check_credentials = "SELECT * FROM vendor_price_list WHERE deleted = 0 $filter_query ORDER BY inventoryid DESC".$query_offset;
				$query = "SELECT count(*) as numrows FROM vendor_price_list WHERE deleted = 0 $filter_query";
	        } else {
	            $category = $_GET['category'];
	            $query_check_credentials = "SELECT * FROM vendor_price_list WHERE deleted = 0 $filter_query AND category='$category'".$query_offset;
	            $query = "SELECT count(*) as numrows FROM vendor_price_list WHERE deleted = 0 $filter_query AND category='$category'";
	        }
		}

		$result = mysqli_query($dbc, $query_check_credentials);

		$num_rows = mysqli_num_rows($result);
		if($num_rows > 0) {

	        if(empty($_GET['category']) || $_GET['category'] == 'Top') {
	            $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT inventory_dashboard FROM field_config_vpl WHERE inventory_dashboard IS NOT NULL AND IFNULL(`tab`,'') = ''"));
	            $value_config = ','.$get_field_config['inventory_dashboard'].',';
	        } else {
	            $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT inventory_dashboard FROM field_config_vpl WHERE tab='$category' AND accordion IS NULL"));
	            $value_config = ','.$get_field_config['inventory_dashboard'].',';
	        }

	        // Added Pagination //
	        if($sort_accordions == 1) {
				if($inventory != '') {
					$query_categories = "SELECT DISTINCT `category` FROM vendor_price_list WHERE deleted=0 AND (name LIKE '%" . $inventory . "%' OR code LIKE '%" . $inventory . "%' OR part_no LIKE '%" . $inventory . "%' OR category = '$inventory' OR sub_category LIKE '%" . $inventory . "%' OR description LIKE '%" . $inventory . "%' OR purchase_cost LIKE '%" . $inventory . "%' OR min_bin LIKE '%" . $inventory . "%' OR date_of_purchase LIKE '%" . $inventory . "%') ".$filter_query." ORDER BY `category`";
				} else {
			        if((empty($_GET['category'])) || ($_GET['category'] == 'Top')) {
			            $query_categories = "SELECT DISTINCT `category` FROM vendor_price_list WHERE deleted = 0 $filter_query ORDER BY `category`";
			        } else {
			            $category = $_GET['category'];
			            $query_categories = "SELECT DISTINCT `category` FROM vendor_price_list WHERE deleted = 0 $filter_query AND category='$category' ORDER BY `category`";
			        }
				}
				echo '<div class="clearfix"></div>';
				echo '<div id="no-more-tables">';
				echo '<div class="panel-group" id="accordion2">';
	        } else {
		        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
		        $query_categories = "SELECT 1";
		    }
		    $result_categories = mysqli_query($dbc, $query_categories);

		    while($vpl_cat = mysqli_fetch_assoc($result_categories)) {
		    	if($sort_accordions == 1) {
		    		echo '<div class="panel panel-default">';
		    		echo '<div class="panel-heading no_load">';
		    		echo '<h4 class="panel-title">';
		    		echo '<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_'.config_safe_str($vpl_cat['category']).'">'.(empty($vpl_cat['category']) ? 'No Category' : $vpl_cat['category']).'<span class="glyphicon glphyicon-plus"></span></a>';
		    		echo '</h4>';
		    		echo '</div>';
		    		echo '<div id="collapse_'.config_safe_str($vpl_cat['category']).'" class="panel-collapse collapse">';
		    		echo '<div class="panel-body">';
		    	}
		        // Pagination Finish //
			    echo "<table class='table table-bordered'>";
			    echo "<tr class='hidden-xs hidden-sm'>";
				if (isset($_GET['order_list'])) {
					echo '<th>Include in Order List';
					echo "<div class='selectall selectbutton' title='This will select all PDFs on the current page.' style='text-decoration:underline;cursor:pointer;'>Select All</div>";
					echo '</th>';
				}
	            if (strpos($value_config, ','."Vendor".',') !== FALSE) {
	                echo '<th>Vendor</th>';
	            }
	            if (strpos($value_config, ','."VPL Name".',') !== FALSE) {
	                echo '<th>Vendor Price List</th>';
	            }
	            if (strpos($value_config, ','."Part #".',') !== FALSE) {
	                echo '<th>Part #</th>';
	            }
	            if (strpos($value_config, ','."ID #".',') !== FALSE) {
	                echo '<th><span class="popover-examples" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="The ID # for this Price List as set when importing the Price List."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></span> ID #</th>';
	            }
	            if (strpos($value_config, ','."Item SKU".',') !== FALSE) {
	                echo '<th>Item SKU</th>';
	            }
	            if (strpos($value_config, ','."Code".',') !== FALSE) {
	                echo '<th>Code</th>';
	            }
	            if (strpos($value_config, ','."Description".',') !== FALSE) {
	                echo '<th>Description</th>';
	            }
	            if (strpos($value_config, ','."Category".',') !== FALSE) {
	                echo '<th>Category</th>';
	            }
	            if (strpos($value_config, ','."Subcategory".',') !== FALSE) {
	                echo '<th>Subcategory</th>';
	            }
	            if (strpos($value_config, ','."Name".',') !== FALSE) {
	                echo '<th><span class="popover-examples" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="The set name of the Price List."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></span> Name</th>';
	            }
	            if (strpos($value_config, ','."Product Name".',') !== FALSE) {
	                echo '<th>Product Name</th>';
	            }
	            if (strpos($value_config, ','."Type".',') !== FALSE) {
	                echo '<th>Type</th>';
	            }
	            if (strpos($value_config, ','."Color".',') !== FALSE) {
	                echo '<th>Color</th>';
	            }
	            if (strpos($value_config, ','."Cost".',') !== FALSE) {
	                echo '<th>Cost</th>';
	            }
	            if (strpos($value_config, ','."CDN Cost Per Unit".',') !== FALSE) {
	                echo '<th>CDN Cost Per Unit</th>';
	            }
	            if (strpos($value_config, ','."USD Cost Per Unit".',') !== FALSE) {
	                echo '<th>USD Cost Per Unit</th>';
	            }
	            if (strpos($value_config, ','."COGS".',') !== FALSE) {
	                echo '<th>COGS GL Code</th>';
	            }
	            if (strpos($value_config, ','."Average Cost".',') !== FALSE) {
	                echo '<th>Average Cost</th>';
	            }
	            if (strpos($value_config, ','."USD Invoice".',') !== FALSE) {
	                echo '<th>USD Invoice</th>';
	            }
	            if (strpos($value_config, ','."Purchase Cost".',') !== FALSE) {
	                echo '<th>Purchase Cost</th>';
	            }
	            if (strpos($value_config, ','."Date Of Purchase".',') !== FALSE) {
	                echo '<th>Date Of Purchase</th>';
	            }
	            if (strpos($value_config, ','."Shipping Rate".',') !== FALSE) {
	                echo '<th>Shipping Rate</th>';
	            }
	            if (strpos($value_config, ','."Shipping Cash".',') !== FALSE) {
	                echo '<th>Shipping Cash</th>';
	            }
	            if (strpos($value_config, ','."Freight Charge".',') !== FALSE) {
	                echo '<th>Freight Charge</th>';
	            }
	            if (strpos($value_config, ','."Exchange Rate".',') !== FALSE) {
	                echo '<th>Exchange Rate</th>';
	            }
	            if (strpos($value_config, ','."Exchange $".',') !== FALSE) {
	                echo '<th>Exchange $</th>';
	            }


	            if (strpos($value_config, ','."Sell Price".',') !== FALSE) {
	                echo '<th>Sell Price</th>';
	            }
	            if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) {
	                echo '<th>Final Retail Price</th>';
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
	            if (strpos($value_config, ','."Suggested Retail Price".',') !== FALSE) {
	                echo '<th>Suggested Retail Price</th>';
	            }
	            if (strpos($value_config, ','."Rush Price".',') !== FALSE) {
	                echo '<th>Rush Price</th>';
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
	            if (strpos($value_config, ','."Preferred Price".',') !== FALSE) {
	                echo '<th>Preferred Price</th>';
	            }

	            if (strpos($value_config, ','."Admin Price".',') !== FALSE) {
	                echo '<th>Admin Price</th>';
	            }
	            if (strpos($value_config, ','."Web Price".',') !== FALSE) {
	                echo '<th>Web Price</th>';
	            }
	            if (strpos($value_config, ','."Commission Price".',') !== FALSE) {
	                echo '<th>Commission Price</th>';
	            }
	            if (strpos($value_config, ','."MSRP".',') !== FALSE) {
	                echo '<th>MSRP</th>';
	            }

	            if (strpos($value_config, ','."Unit Price".',') !== FALSE) {
	                echo '<th>Unit Price</th>';
	            }
	            if (strpos($value_config, ','."Unit Cost".',') !== FALSE) {
	                echo '<th>Unit Cost</th>';
	            }
	            if (strpos($value_config, ','."Rent Price".',') !== FALSE) {
	                echo '<th>Rent Price</th>';
	            }
	            if (strpos($value_config, ','."Rental Days".',') !== FALSE) {
	                echo '<th>Rental Days</th>';
	            }
	            if (strpos($value_config, ','."Rental Weeks".',') !== FALSE) {
	                echo '<th>Rental Weeks</th>';
	            }
	            if (strpos($value_config, ','."Rental Months".',') !== FALSE) {
	                echo '<th>Rental Months</th>';
	            }
	            if (strpos($value_config, ','."Rental Years".',') !== FALSE) {
	                echo '<th>Rental Years</th>';
	            }
	            if (strpos($value_config, ','."Reminder/Alert".',') !== FALSE) {
	                echo '<th>Reminder/Alert</th>';
	            }
	            if (strpos($value_config, ','."Daily".',') !== FALSE) {
	                echo '<th>Daily</th>';
	            }
	            if (strpos($value_config, ','."Weekly".',') !== FALSE) {
	                echo '<th>Weekly</th>';
	            }
	            if (strpos($value_config, ','."Monthly".',') !== FALSE) {
	                echo '<th>Monthly</th>';
	            }
	            if (strpos($value_config, ','."Annually".',') !== FALSE) {
	                echo '<th>Annually</th>';
	            }
	            if (strpos($value_config, ','."#Of Days".',') !== FALSE) {
	                echo '<th>#Of Days</th>';
	            }
	            if (strpos($value_config, ','."#Of Hours".',') !== FALSE) {
	                echo '<th>#Of Hours</th>';
	            }
	            if (strpos($value_config, ','."#Of Kilometers".',') !== FALSE) {
	                echo '<th>#Of Kilometers</th>';
	            }
	            if (strpos($value_config, ','."#Of Miles".',') !== FALSE) {
	                echo '<th>#Of Miles</th>';
	            }
	            if (strpos($value_config, ','."Markup By $".',') !== FALSE) {
	                echo '<th>Markup By $</th>';
	            }
	            if (strpos($value_config, ','."Markup By %".',') !== FALSE) {
	                echo '<th>Markup By %</th>';
	            }

	            if (strpos($value_config, ','."GL Revenue".',') !== FALSE) {
	                echo '<th>GL Revenue</th>';
	            }
	            if (strpos($value_config, ','."GL Assets".',') !== FALSE) {
	                echo '<th>GL Assets</th>';
	            }

	            if (strpos($value_config, ','."Current Stock".',') !== FALSE) {
	                echo '<th>Current Stock</th>';
	            }
	            if (strpos($value_config, ','."Current Inventory".',') !== FALSE) {
	                echo '<th>Current Inventory</th>';
	            }
	            if (strpos($value_config, ','."Quantity".',') !== FALSE) {
	                echo '<th>Quantity</th>';
	            }
	            if (strpos($value_config, ','."Variance".',') !== FALSE) {
	                echo '<th>GL Code</th>';
	            }
	            if (strpos($value_config, ','."Write-offs".',') !== FALSE) {
	                echo '<th>Write-offs</th>';
	            }
	            if (strpos($value_config, ','."Buying Units".',') !== FALSE) {
	                echo '<th>Buying Units</th>';
	            }
	            if (strpos($value_config, ','."Selling Units".',') !== FALSE) {
	                echo '<th>Selling Units</th>';
	            }
	            if (strpos($value_config, ','."Stocking Units".',') !== FALSE) {
	                echo '<th>Stocking Units</th>';
	            }
	            if (strpos($value_config, ','."Min Amount".',') !== FALSE) {
	                echo '<th>Min Amount</th>';
	            }
	            if (strpos($value_config, ','."Max Amount".',') !== FALSE) {
	                echo '<th>Max Amount</th>';
	            }
	            if (strpos($value_config, ','."Location".',') !== FALSE) {
	                echo '<th><span class="popover-examples" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="The location of the vendor attached to this Price List."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></span> Location</th>';
	            }
	            if (strpos($value_config, ','."LSD".',') !== FALSE) {
	                echo '<th>LSD</th>';
	            }
	            if (strpos($value_config, ','."Size".',') !== FALSE) {
	                echo '<th>Size</th>';
	            }
	            if (strpos($value_config, ','."Weight".',') !== FALSE) {
	                echo '<th>Weight</th>';
	            }
	            if (strpos($value_config, ','."Min Max".',') !== FALSE) {
	                echo '<th>Min Max</th>';
	            }
	            if (strpos($value_config, ','."Min Bin".',') !== FALSE) {
	                echo '<th>Min Bin</th>';
	            }
	            if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) {
	                echo '<th>Estimated Hours</th>';
	            }
	            if (strpos($value_config, ','."Actual Hours".',') !== FALSE) {
	                echo '<th>Actual Hours</th>';
	            }
	            if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) {
	                echo '<th>Minimum Billable</th>';
	            }
	            if (strpos($value_config, ','."Quote Description".',') !== FALSE) {
	                echo '<th>Quote Description</th>';
	            }
	            if (strpos($value_config, ','."Status".',') !== FALSE) {
	                echo '<th>Status</th>';
	            }
	            if (strpos($value_config, ','."Display On Website".',') !== FALSE) {
	                echo '<th>Display On Website</th>';
	            }
	            if (strpos($value_config, ','."Notes".',') !== FALSE) {
	                echo '<th>Notes</th>';
	            }
	            if (strpos($value_config, ','."Comments".',') !== FALSE) {
	                echo '<th>Comments</th>';
	            }
	            echo '<th><span class="popover-examples" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Edit or archive a Price List."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></span> Function</th>';
	            echo "</tr>";

	            if($sort_accordions == 1) {
					if($inventory != '') {
						$query_check_credentials = "SELECT * FROM vendor_price_list WHERE deleted=0 AND `category` = '".$vpl_cat['category']."' AND (name LIKE '%" . $inventory . "%' OR code LIKE '%" . $inventory . "%' OR part_no LIKE '%" . $inventory . "%' OR category = '$inventory' OR sub_category LIKE '%" . $inventory . "%' OR description LIKE '%" . $inventory . "%' OR purchase_cost LIKE '%" . $inventory . "%' OR min_bin LIKE '%" . $inventory . "%' OR date_of_purchase LIKE '%" . $inventory . "%') ".$filter_query;
					} else {
				        if((empty($_GET['category'])) || ($_GET['category'] == 'Top')) {
				            $query_check_credentials = "SELECT * FROM vendor_price_list WHERE deleted = 0 AND `category` = '".$vpl_cat['category']."' $filter_query ORDER BY inventoryid DESC";
				        } else {
				            $category = $_GET['category'];
				            $query_check_credentials = "SELECT * FROM vendor_price_list WHERE deleted = 0 AND `category` = '".$vpl_cat['category']."' $filter_query AND category='$category'";
				        }
					}
					$result = mysqli_query($dbc, $query_check_credentials);
				}
				while($row = mysqli_fetch_array( $result ))
				{
			        $color = '';
			        if($row['status'] == 'In inventory') {
			            $color = 'style="color: white;"';
			        }
			        if($row['status'] == 'In transit from vendor') {
			            $color = 'style="color: red;"';
			        }
			        if($row['status'] == 'In transit between yards') {
			            $color = 'style="color: blue;"';
			        }
			        if($row['status'] == 'Not confirmed in yard by inventory check') {
			            $color = 'style="color: yellow;"';
			        }
			        if($row['status'] == 'Assigned to job') {
			            $color = 'style="color: green;"';
			        }
			        if($row['status'] == 'In transit and assigned') {
			            $color = 'style="color: purple;"';
			        }

					echo '<tr '.$color.'>';
					if (isset($_GET['order_list'])) {
						echo '<td data-title="Include in Order List">';
						$HiddenProducts = explode(',',$inventoryidorder);
						if (in_array($row['inventoryid'], $HiddenProducts)) {
						  $checked = 'Checked';
						} else {
						  $checked = '';
						}
			            ?><input type='checkbox' style='width:20px; height:20px;' <?php echo $checked; ?> id='<?PHP echo $row['inventoryid']; ?>'  name='' class='order_list_includer' value='<?PHP echo $_GET['order_list']; ?>'><br>
			            <?php
			            echo '</td>';
					}
			        if (strpos($value_config, ','."Vendor".',') !== FALSE) {
			            $vendorid = $row['vendorid'];
			            $get_vendor = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT  name FROM contacts WHERE contactid='$vendorid'"));
			            echo '<td data-title="Vendor">' . decryptIt($get_vendor['name']) . '</td>';
			        }
			        if (strpos($value_config, ','."VPL Name".',') !== FALSE) {
			            echo '<td data-title="Vendor Price List">' . $row['vpl_name'] . '</td>';
			        }
			        if (strpos($value_config, ','."Part #".',') !== FALSE) {
			            echo '<td data-title="Part #">' . $row['part_no'] . '</td>';
			        }
			        if (strpos($value_config, ','."ID #".',') !== FALSE) {
			            echo '<td data-title="ID #">' . $row['id_number'] . '</td>';
			        }
			        if (strpos($value_config, ','."Item SKU".',') !== FALSE) {
			            echo '<td data-title="Item SKU">' . $row['item_sku'] . '</td>';
			        }
			        if (strpos($value_config, ','."Code".',') !== FALSE) {
			            echo '<td data-title="Code">' . $row['code'] . '</td>';
			        }
			        if (strpos($value_config, ','."Description".',') !== FALSE) {
			            echo '<td data-title="Desc.">' . $row['part_no'] . '</td>';
			        }
			        if (strpos($value_config, ','."Category".',') !== FALSE) {
			            echo '<td data-title="Category">' . $row['category'] . '</td>';
			        }
			        if (strpos($value_config, ','."Subcategory".',') !== FALSE) {
			            echo '<td data-title="Sub Category">' . $row['sub_category'] . '</td>';
			        }
			        if (strpos($value_config, ','."Name".',') !== FALSE) {
			            echo '<td data-title="Name">' . $row['name'] . '</td>';
			        }
			        if (strpos($value_config, ','."Product Name".',') !== FALSE) {
			            echo '<td data-title="Prod. Name">' . $row['product_name'] . '</td>';
			        }
			        if (strpos($value_config, ','."Type".',') !== FALSE) {
			            echo '<td data-title="Type">' . $row['type'] . '</td>';
			        }
			        if (strpos($value_config, ','."Color".',') !== FALSE) {
			            echo '<td data-title="Color">' . $row['color'] . '</td>';
			        }
			        if (strpos($value_config, ','."Cost".',') !== FALSE) {
			            echo '<td data-title="Cost">' . $row['cost'] . '</td>';
			        }
			        if (strpos($value_config, ','."CDN Cost Per Unit".',') !== FALSE) {
			           echo '<td data-title="CAD/Unit">' . $row['cdn_cpu'] . '</td>';
			        }
			        if (strpos($value_config, ','."USD Cost Per Unit".',') !== FALSE) {
			            echo '<td data-title="USD/Unit">' . $row['usd_cpu'] . '</td>';
			        }
			        if (strpos($value_config, ','."COGS".',') !== FALSE) {
			            echo '<td data-title="COGS">' . $row['cogs_total'] . '</td>';
			        }
			        if (strpos($value_config, ','."Average Cost".',') !== FALSE) {
			            echo '<td data-title="Avg. Cost">' . $row['average_cost'] . '</td>';
			        }
			        if (strpos($value_config, ','."USD Invoice".',') !== FALSE) {
			            echo '<td data-title="USD Invoice">' . $row['usd_invoice'] . '</td>';
			        }
			        if (strpos($value_config, ','."Purchase Cost".',') !== FALSE) {
			            echo '<td data-title="Purchase Cost">' . $row['purchase_cost'] . '</td>';
			        }
			        if (strpos($value_config, ','."Date Of Purchase".',') !== FALSE) {
			            echo '<td data-title="Purchase Date">' . $row['date_of_purchase'] . '</td>';
			        }
			        if (strpos($value_config, ','."Shipping Rate".',') !== FALSE) {
			            echo '<td data-title="Ship Rate">' . $row['shipping_rate'] . '</td>';
			        }
			        if (strpos($value_config, ','."Shipping Cash".',') !== FALSE) {
			            echo '<td data-title="Ship Cash">' . $row['shipping_cash'] . '</td>';
			        }
			        if (strpos($value_config, ','."Freight Charge".',') !== FALSE) {
			            echo '<td data-title="Freight">' . $row['freight_charge'] . '</td>';
			        }
			        if (strpos($value_config, ','."Exchange Rate".',') !== FALSE) {
			            echo '<td data-title="Exchange Rate">' . $row['exchange_rate'] . '</td>';
			        }
			        if (strpos($value_config, ','."Exchange $".',') !== FALSE) {
			            echo '<td data-title="Exchange $">' . $row['exchange_cash'] . '</td>';
			        }
			        if (strpos($value_config, ','."Sell Price".',') !== FALSE) {
			            echo '<td data-title="Sell Price">' . $row['sell_price'] . '</td>';
			        }
			        if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) {
			            echo '<td data-title="Retail">' . $row['final_retail_price'] . '</td>';
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
			        if (strpos($value_config, ','."Suggested Retail Price".',') !== FALSE) {
			            echo '<td data-title="Suggested Retail Price">' . $row['suggested_retail_price'] . '</td>';
			        }
			        if (strpos($value_config, ','."Rush Price".',') !== FALSE) {
			            echo '<td data-title="Rush Price">' . $row['rush_price'] . '</td>';
			        }
					if (strpos($value_config, ','."Include in Sales Orders".',') !== FALSE) {
			                echo '<td data-title="Include in '.SALES_ORDER_TILE.'">';
							?><input type='checkbox' style='width:20px; height:20px;' <?php if($row['include_in_so'] !== '' && $row['include_in_so'] !== NULL) { echo "checked"; } ?> id='<?PHP echo $row['inventoryid']; ?>'  name='' class='sales_order_includer' value='1'><br>
							<?php
							echo '</td>';
			            }
						if (strpos($value_config, ','."Include in P.O.S.".',') !== FALSE) {
			                echo '<td data-title="Include in P.O.S.">';
							?><input type='checkbox' style='width:20px; height:20px;' <?php if($row['include_in_pos'] !== '' && $row['include_in_pos'] !== NULL) { echo "checked"; } ?> id='<?PHP echo $row['inventoryid']; ?>'  name='' class='point_of_sale_includer' value='1'><br>
							<?php
							echo '</td>';
			            }
						if (strpos($value_config, ','."Include in Purchase Orders".',') !== FALSE) {
			                echo '<td data-title="Include in Purchase Orders">';
							?><input type='checkbox' style='width:20px; height:20px;' <?php if($row['include_in_po'] !== '' && $row['include_in_po'] !== NULL) { echo "checked"; } ?> id='<?PHP echo $row['inventoryid']; ?>'  name='' class='purchase_order_includer' value='1'><br>
							<?php
							echo '</td>';
			            }
			        if (strpos($value_config, ','."Preferred Price".',') !== FALSE) {
			            echo '<td data-title="Pref. Price">' . $row['preferred_price'] . '</td>';
			        }

			        if (strpos($value_config, ','."Admin Price".',') !== FALSE) {
			            echo '<td data-title="Admin Price">' . $row['admin_price'] . '</td>';
			        }
			        if (strpos($value_config, ','."Web Price".',') !== FALSE) {
			            echo '<td data-title="Web Price">' . $row['web_price'] . '</td>';
			        }
			        if (strpos($value_config, ','."Commission Price".',') !== FALSE) {
			            echo '<td data-title="Commission">' . $row['commission_price'] . '</td>';
			        }
			        if (strpos($value_config, ','."MSRP".',') !== FALSE) {
			            echo '<td data-title="MSRP">' . $row['msrp'] . '</td>';
			        }
			        if (strpos($value_config, ','."Unit Price".',') !== FALSE) {
			            echo '<td data-title="Unit Price">' . $row['unit_price'] . '</td>';
			        }
			        if (strpos($value_config, ','."Unit Cost".',') !== FALSE) {
			            echo '<td data-title="Unit Cost">' . $row['unit_cost'] . '</td>';
			        }
			        if (strpos($value_config, ','."Rent Price".',') !== FALSE) {
			            echo '<td data-title="Rent Price">' . $row['rent_price'] . '</td>';
			        }
			        if (strpos($value_config, ','."Rental Days".',') !== FALSE) {
			            echo '<td data-title="Rent Days">' . $row['rental_days'] . '</td>';
			        }
			        if (strpos($value_config, ','."Rental Weeks".',') !== FALSE) {
			            echo '<td data-title="Rent Weeks">' . $row['rental_weeks'] . '</td>';
			        }
			        if (strpos($value_config, ','."Rental Months".',') !== FALSE) {
			            echo '<td data-title="Rent Months">' . $row['rental_months'] . '</td>';
			        }
			        if (strpos($value_config, ','."Rental Years".',') !== FALSE) {
			            echo '<td data-title="Rent Years">' . $row['rental_years'] . '</td>';
			        }
			        if (strpos($value_config, ','."Reminder/Alert".',') !== FALSE) {
			            echo '<td data-title="Reminder">' . $row['reminder_alert'] . '</td>';
			        }
			        if (strpos($value_config, ','."Daily".',') !== FALSE) {
			            echo '<td data-title="Daily">' . $row['daily'] . '</td>';
			        }
			        if (strpos($value_config, ','."Weekly".',') !== FALSE) {
			            echo '<td data-title="Weekly">' . $row['weekly'] . '</td>';
			        }
			        if (strpos($value_config, ','."Monthly".',') !== FALSE) {
			            echo '<td data-title="Monthly">' . $row['monthly'] . '</td>';
			        }
			        if (strpos($value_config, ','."Annually".',') !== FALSE) {
			            echo '<td data-title="Annual">' . $row['annually'] . '</td>';
			        }
			        if (strpos($value_config, ','."#Of Days".',') !== FALSE) {
			            echo '<td data-title="# Days">' . $row['total_days'] . '</td>';
			        }
			        if (strpos($value_config, ','."#Of Hours".',') !== FALSE) {
			            echo '<td data-title="# Hours">' . $row['total_hours'] . '</td>';
			        }
			        if (strpos($value_config, ','."#Of Kilometers".',') !== FALSE) {
			            echo '<td data-title="# Kilometers">' . $row['total_km'] . '</td>';
			        }
			        if (strpos($value_config, ','."#Of Miles".',') !== FALSE) {
			            echo '<td data-title="# Miles">' . $row['total_miles'] . '</td>';
			        }
			        if (strpos($value_config, ','."Markup By $".',') !== FALSE) {
			            echo '<td data-title="Markup $">' . $row['markup'] . '</td>';
			        }
			        if (strpos($value_config, ','."Markup By %".',') !== FALSE) {
			            echo '<td data-title="Markup %">' . $row['markup_perc'] . '</td>';
			        }

			        if (strpos($value_config, ','."GL Revenue".',') !== FALSE) {
			            echo '<td data-title="GL Revenue">' . $row['revenue'] . '</td>';
			        }
			        if (strpos($value_config, ','."GL Assets".',') !== FALSE) {
			           echo '<td data-title="GL Assets">' . $row['asset'] . '</td>';
			        }

			        if (strpos($value_config, ','."Current Stock".',') !== FALSE) {
			            echo '<td data-title="Stock">' . $row['current_stock'] . '</td>';
			        }

			        if (strpos($value_config, ','."Current Inventory".',') !== FALSE) {
			            echo '<td data-title="Inventory">' . $row['current_inventory'] . '</td>';
			        }
			        if (strpos($value_config, ','."Quantity".',') !== FALSE) {
			            echo '<td data-title="Quantity">' . $row['quantity'] . '</td>';
			        }
			        if (strpos($value_config, ','."Variance".',') !== FALSE) {
			            echo '<td data-title="Variance">' . $row['inv_variance'] . '</td>';
			        }
			        if (strpos($value_config, ','."Write-offs".',') !== FALSE) {
			            echo '<td data-title="Write Offs">' . $row['write_offs'] . '</td>';
			        }

			        if (strpos($value_config, ','."Buying Units".',') !== FALSE) {
			            echo '<td data-title="Buying Units">' . $row['buying_units'] . '</td>';
			        }
			        if (strpos($value_config, ','."Selling Units".',') !== FALSE) {
			            echo '<td data-title="Selling Units">' . $row['selling_units'] . '</td>';
			        }
			        if (strpos($value_config, ','."Stocking Units".',') !== FALSE) {
			            echo '<td data-title="Stocking Units">' . $row['stocking_units'] . '</td>';
			        }
			        if (strpos($value_config, ','."Min Amount".',') !== FALSE) {
			            echo '<td data-title="Min Amount">' . $row['min_amount'] . '</td>';
			        }
			        if (strpos($value_config, ','."Max Amount".',') !== FALSE) {
			            echo '<td data-title="Max Amount">' . $row['max_amount'] . '</td>';
			        }

			        if (strpos($value_config, ','."Location".',') !== FALSE) {
			            echo '<td data-title="Location">' . $row['location'] . '</td>';
			        }
			        if (strpos($value_config, ','."LSD".',') !== FALSE) {
			            echo '<td data-title="LSD">' . $row['lsd'] . '</td>';
			        }
			        if (strpos($value_config, ','."Size".',') !== FALSE) {
			            echo '<td data-title="Size">' . $row['size'] . '</td>';
			        }
			        if (strpos($value_config, ','."Weight".',') !== FALSE) {
			            echo '<td data-title="Weight">' . $row['weight'] . '</td>';
			        }
			        if (strpos($value_config, ','."Min Max".',') !== FALSE) {
			            echo '<td data-title="Min / Max">' . $row['min_max'] . '</td>';
			        }
			        if (strpos($value_config, ','."Min Bin".',') !== FALSE) {
			            echo '<td data-title="Min Bin">' . $row['min_bin'] . '</td>';
			        }
			        if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) {
			            echo '<td data-title="Est. Hours">' . $row['estimated_hours'] . '</td>';
			        }
			        if (strpos($value_config, ','."Actual Hours".',') !== FALSE) {
			            echo '<td data-title="Actual Hours">' . $row['actual_hours'] . '</td>';
			        }
			        if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) {
			            echo '<td data-title="Min. Billable">' . $row['minimum_billable'] . '</td>';
			        }
			        if (strpos($value_config, ','."Quote Description".',') !== FALSE) {
			            echo '<td data-title="Quote Desc.">' . html_entity_decode($row['quote_description']) . '</td>';
			        }
			        if (strpos($value_config, ','."Status".',') !== FALSE) {
			            echo '<td data-title="Status">' . $row['status'] . '</td>';
			        }
			        if (strpos($value_config, ','."Display On Website".',') !== FALSE) {
			            echo '<td data-title="On Website">' . $row['display_website'] . '</td>';
			        }
			        if (strpos($value_config, ','."Notes".',') !== FALSE) {
			            echo '<td data-title="Notes">' . $row['note'] . '</td>';
			        }
			        if (strpos($value_config, ','."Comments".',') !== FALSE) {
			            echo '<td data-title="Comments">' . $row['comment'] . '</td>';
			        }

			        echo '<td data-title="Function">';
			        if(vuaed_visible_function($dbc, 'vpl') == 1) {
					echo '<a href=\'?vpl=1&vendorid='.$_GET['vendorid'].'&vpl_name='.$_GET['vpl_name'].'&inventoryid='.$row['inventoryid'].'\'>Edit</a> | ';
					echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&vplid='.$row['inventoryid'].'&back_url='.urlencode($_SERVER['REQUEST_URI']).'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
			        }
					echo '</td>';

					echo "</tr>";
				}

			    echo '</table>';

			    if($sort_accordions == 1) {
			    	echo '</div></div></div>';
			    }
			}

			if($sort_accordions == 1) {
				echo '</div></div>';
			}
		} else {
			echo "<h2>No Record Found.</h2>";
		}

	    // Added Pagination //
        if($sort_accordions != 1) {
		    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
		}
	    // Pagination Finish //

	    if(vuaed_visible_function($dbc, 'vpl') == 1) {
	        if($_GET['category'] != 'Top') {
			    echo '<a href="?vpl=1&vendorid='.$_GET['vendorid'].'&vpl_name='.$_GET['vpl_name'].'&inventoryid=&category='.$category.'" class="btn brand-btn mobile-block gap-bottom pull-right">Add Product</a>';
	        }
	        ?>

			<span class="popover-examples pull-right pad-5"><a data-toggle="tooltip" data-placement="top" title="You must be in a category before you can add a product."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
	    }
	    //echo display_filter('inventory.php');

		?>
	</div>
</form>
</div>