<?php /* Pagination Counting */
$rowsPerPage = 25;
$pageNum = 1;

if(isset($_GET['page'])) {
	$pageNum = $_GET['page'];
}

$offset = ($pageNum - 1) * $rowsPerPage;

if($vendor != '') {
	$query_check_credentials = "SELECT * FROM custom WHERE deleted = 0 AND (service_type LIKE '%" . $vendor . "%' OR category ='$vendor' OR heading LIKE '%" . $vendor . "%') LIMIT $offset, $rowsPerPage";
	$query = "SELECT count(*) as numrows FROM custom WHERE deleted = 0 AND (service_type LIKE '%" . $vendor . "%' OR category ='$vendor' OR heading LIKE '%" . $vendor . "%')";
} else {
	$query_check_credentials = "SELECT * FROM custom WHERE deleted = 0 ORDER BY service_type LIMIT $offset, $rowsPerPage";
	$query = "SELECT count(*) as numrows FROM custom WHERE deleted = 0 ORDER BY service_type";
}

$result = mysqli_query($dbc, $query_check_credentials);

$num_rows = mysqli_num_rows($result);
if($num_rows > 0) {
	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT custom_dashboard FROM field_config"));
	$value_config = ','.$get_field_config['custom_dashboard'].',';

	// Added Pagination //
	echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
	// Pagination Finish //

	echo "<table class='table table-bordered'>";
	echo "<tr class='hidden-xs hidden-sm'>";
		if (strpos($value_config, ','."Service Type".',') !== FALSE) {
			echo '<th>Service Type</th>';
		}
		if (strpos($value_config, ','."Category".',') !== FALSE) {
			echo '<th>Category</th>';
		}
		if (strpos($value_config, ','."Heading".',') !== FALSE) {
			echo '<th>Heading</th>';
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
		echo '<th>Function</th>';
		echo "</tr>";
} else {
	echo "<h2>No Record Found.</h2>";
}

while($row = mysqli_fetch_array( $result ))
{
	echo "<tr>";

	if (strpos($value_config, ','."Service Type".',') !== FALSE) {
		echo '<td data-title="Service Type">' . $row['service_type'] . '</td>';
	}
	if (strpos($value_config, ','."Category".',') !== FALSE) {
		echo '<td data-title="category">' . $row['category'] . '</td>';
	}
	if (strpos($value_config, ','."Heading".',') !== FALSE) {
		echo '<td data-title="Heading">' . $row['heading'] . '</td>';
	}
	if (strpos($value_config, ','."Cost".',') !== FALSE) {
		echo '<td data-title="Cost">' . $row['cost'] . '</td>';
	}
	if (strpos($value_config, ','."Description".',') !== FALSE) {
		echo '<td data-title="Description">' . html_entity_decode($row['description']) . '</td>';
	}
	if (strpos($value_config, ','."Quote Description".',') !== FALSE) {
		echo '<td data-title="Quote Description">' . html_entity_decode($row['quote_description']) . '</td>';
	}
	if (strpos($value_config, ','."Invoice Description".',') !== FALSE) {
		echo '<td data-title="Invoice Description">' . html_entity_decode($row['invoice_description']) . '</td>';
	}
	if (strpos($value_config, ','."Ticket Description".',') !== FALSE) {
		echo '<td data-title="'.TICKET_NOUN.' Description">' . html_entity_decode($row['ticket_description']) . '</td>';
	}
	if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) {
		echo '<td data-title="Final Retail Price">' . $row['final_retail_price'] . '</td>';
	}
	if (strpos($value_config, ','."Admin Price".',') !== FALSE) {
		echo '<td data-title="Admin Price">' . $row['admin_price'] . '</td>';
	}
	if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) {
		echo '<td data-title="Wholesale Price">' . $row['wholesale_price'] . '</td>';
	}
	if (strpos($value_config, ','."Commercial Price".',') !== FALSE) {
		echo '<td data-title="Commercial Price">' . $row['commercial_price'] . '</td>';
	}
	if (strpos($value_config, ','."Client Price".',') !== FALSE) {
		echo '<td data-title="Client Price">' . $row['client_price'] . '</td>';
	}
	if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) {
		echo '<td data-title="Minimum Billable">' . $row['minimum_billable'] . '</td>';
	}
	if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) {
		echo '<td data-title="Estimated Hours">' . $row['estimated_hours'] . '</td>';
	}
	if (strpos($value_config, ','."Actual Hours".',') !== FALSE) {
		echo '<td data-title="Actual Hours">' . $row['actual_hours'] . '</td>';
	}
	if (strpos($value_config, ','."MSRP".',') !== FALSE) {
		echo '<td data-title="MSRP">' . $row['msrp'] . '</td>';
	}

	echo '<td data-title="Function">';
	if(vuaed_visible_function($dbc, 'custom') == 1) {
	echo '<a href=\'add_custom.php?customid='.$row['customid'].'\'>Edit</a> | ';
	echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&customid='.$row['customid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
	}
	echo '</td>';

	echo "</tr>";
}

echo '</table></div>';

// Added Pagination //
echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
// Pagination Finish // ?>