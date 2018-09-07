<?php $rowsPerPage = ITEMS_PER_PAGE;
$pageNum = 1;

if(isset($_GET['page'])) {
	$pageNum = $_GET['page'];
}

$offset = ($pageNum - 1) * $rowsPerPage;

if($asset != '') {
	$query_check_credentials = "SELECT * FROM asset WHERE deleted=0 AND (name LIKE '%" . $asset . "%' OR code LIKE '%" . $asset . "%' OR part_no LIKE '%" . $asset . "%' OR category = '$asset' OR sub_category LIKE '%" . $asset . "%' OR description LIKE '%" . $asset . "%' OR purchase_cost LIKE '%" . $asset . "%' OR min_bin LIKE '%" . $asset . "%' OR date_of_purchase LIKE '%" . $asset . "%')";
} else {
	/*
	if(isset($_GET['filter'])) { $url_search = $_GET['filter']; } else { $url_search = ''; }
	if($url_search == 'Top') {
		$query_check_credentials = "SELECT * FROM asset WHERE deleted = 0 ORDER BY assetid DESC LIMIT 10";
	} else if($url_search == 'All') {
		$query_check_credentials = "SELECT * FROM asset WHERE deleted = 0 ORDER BY part_no";
	} else {
		$query_check_credentials = "SELECT * FROM asset WHERE deleted = 0 AND part_no LIKE '" . $url_search . "%' ORDER BY part_no";
	}
	*/

	//$query_check_credentials = "SELECT * FROM asset WHERE deleted=0 LIMIT $offset, $rowsPerPage";

	if((empty($_GET['category'])) || ($_GET['category'] == 'Top')) {
		$query_check_credentials = "SELECT * FROM asset WHERE deleted = 0 ORDER BY assetid DESC LIMIT 25";
	} else {
		$category = $_GET['category'];
		$query_check_credentials = "SELECT * FROM asset WHERE deleted = 0 AND category='$category'";
	}
}

$result = mysqli_query($dbc, $query_check_credentials);

$num_rows = mysqli_num_rows($result);
if($num_rows > 0) {

	if(empty($_GET['category']) || $_GET['category'] == 'Top') {
		$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT asset_dashboard FROM field_config_asset WHERE asset_dashboard IS NOT NULL"));
		$value_config = ','.$get_field_config['asset_dashboard'].',';
	} else {
		$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT asset_dashboard FROM field_config_asset WHERE tab='$category' AND accordion IS NULL AND asset_dashboard IS NOT NULL"));
		$value_config = ','.$get_field_config['asset_dashboard'].',';
	}

	echo "<table class='table table-bordered table-striped'>";
	echo "<thead>";
	echo "<tr class='hidden-xs hidden-sm'>";
		if (strpos($value_config, ','."Part #".',') !== FALSE) {
			echo '<th>Part #</th>';
		}
		if (strpos($value_config, ','."ID #".',') !== FALSE) {
			echo '<th>ID #</th>';
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
			echo '<th>Name</th>';
		}
		if (strpos($value_config, ','."Product Name".',') !== FALSE) {
			echo '<th>Product Name</th>';
		}
		if (strpos($value_config, ','."Type".',') !== FALSE) {
			echo '<th>Type</th>';
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
		if (strpos($value_config, ','."Vendor".',') !== FALSE) {
			echo '<th>Vendor</th>';
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
		if (strpos($value_config, ','."Current Asset".',') !== FALSE) {
			echo '<th>Current Asset</th>';
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
		if (strpos($value_config, ','."Location".',') !== FALSE) {
			echo '<th>Location</th>';
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
		echo '<th>Function</th>';
		echo "</tr>";
		echo "</thead>";
} else{
	echo "<h2>No Record Found.</h2>";
}
while($row = mysqli_fetch_array( $result ))
{
	$color = '';
	if($row['status'] == 'In asset') {
		$color = 'style="color: white;"';
	}
	if($row['status'] == 'In transit from vendor') {
		$color = 'style="color: red;"';
	}
	if($row['status'] == 'In transit between yards') {
		$color = 'style="color: blue;"';
	}
	if($row['status'] == 'Not confirmed in yard by asset check') {
		$color = 'style="color: yellow;"';
	}
	if($row['status'] == 'Assigned to job') {
		$color = 'style="color: green;"';
	}
	if($row['status'] == 'In transit and assigned') {
		$color = 'style="color: purple;"';
	}

	echo '<tr '.$color.'>';
	if (strpos($value_config, ','."Part #".',') !== FALSE) {
		echo '<td data-title="Part #">' . $row['part_no'] . '</td>';
	}
	if (strpos($value_config, ','."ID #".',') !== FALSE) {
		echo '<td data-title="ID #">' . $row['id_number'] . '</td>';
	}
	if (strpos($value_config, ','."Code".',') !== FALSE) {
		echo '<td data-title="Code">' . $row['code'] . '</td>';
	}
	if (strpos($value_config, ','."Description".',') !== FALSE) {
		echo '<td data-title="Description">' . html_entity_decode($row['description']) . '</td>';
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
		echo '<td data-title="Product Name">' . $row['product_name'] . '</td>';
	}
	if (strpos($value_config, ','."Type".',') !== FALSE) {
		echo '<td data-title="Type">' . $row['type'] . '</td>';
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
	if (strpos($value_config, ','."Vendor".',') !== FALSE) {
		$vendorid = $row['vendorid'];
		$get_vendor = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT  name FROM contacts WHERE contactid='$vendorid'"));
		echo '<td data-title="Vendor">' . decryptIt($get_vendor['name']) . '</td>';
	}
	if (strpos($value_config, ','."Purchase Cost".',') !== FALSE) {
		echo '<td data-title="Purchase Cost">' . $row['purchase_cost'] . '</td>';
	}
	if (strpos($value_config, ','."Date Of Purchase".',') !== FALSE) {
		echo '<td data-title="Purchase Date">' . $row['date_of_purchase'] . '</td>';
	}
	if (strpos($value_config, ','."Shipping Rate".',') !== FALSE) {
		echo '<td data-title="Shipping">' . $row['shipping_rate'] . '</td>';
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
		echo '<td data-title="Days">' . $row['total_days'] . '</td>';
	}
	if (strpos($value_config, ','."#Of Hours".',') !== FALSE) {
		echo '<td data-title="Hours">' . $row['total_hours'] . '</td>';
	}
	if (strpos($value_config, ','."#Of Kilometers".',') !== FALSE) {
		echo '<td data-title="Kilometers">' . $row['total_km'] . '</td>';
	}
	if (strpos($value_config, ','."#Of Miles".',') !== FALSE) {
		echo '<td data-title="Miles">' . $row['total_miles'] . '</td>';
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
	if (strpos($value_config, ','."Current Asset".',') !== FALSE) {
		echo '<td data-title="Asset">' . $row['current_asset'] . '</td>';
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
		echo '<td data-title="Min Max">' . $row['min_max'] . '</td>';
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
	if(vuaed_visible_function($dbc, 'asset') == 1) {
	echo '<a href=\'add_asset.php?assetid='.$row['assetid'].'\'>Edit</a> | ';
	echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&assetid='.$row['assetid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
	}
	echo '</td>';

	echo "</tr>";
}

echo '</table>';