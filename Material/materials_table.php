<?php if (isset($_GET['category'])) {
	$category_query = " AND `category` = '".$_GET['category']."'";
}

/* Pagination Counting */
$rowsPerPage = 25;
$pageNum = 1;

if(isset($_GET['page'])) {
	$pageNum = $_GET['page'];
}

$offset = ($pageNum - 1) * $rowsPerPage;

if($material != '') {
	$query_check_credentials = "SELECT * FROM material WHERE deleted=0 AND (name LIKE '%" . $material . "%' OR code LIKE '%" . $material . "%' OR category = '$material' OR sub_category LIKE '%" . $material . "%' OR description LIKE '%" . $material . "%' OR width LIKE '%" . $material . "%' OR length LIKE '%" . $material . "%' OR units LIKE '%" . $material . "%' $category_query) LIMIT $offset, $rowsPerPage";
	$pageQuery = "SELECT count(*) as numrows FROM material WHERE deleted=0 AND (name LIKE '%" . $material . "%' OR code LIKE '%" . $material . "%' OR category = '$material' OR sub_category LIKE '%" . $material . "%' OR description LIKE '%" . $material . "%' OR width LIKE '%" . $material . "%' OR length LIKE '%" . $material . "%' OR units LIKE '%" . $material . "%' $category_query)";
} else {
	if(isset($_GET['filter'])) { $url_search = $_GET['filter']; } else { $url_search = ''; }
	if($url_search == 'Top') {
		$query_check_credentials = "SELECT * FROM material WHERE deleted = 0 $category_query ORDER BY materialid DESC LIMIT 25";
	} else if($url_search == 'All') {
		$query_check_credentials = "SELECT * FROM material WHERE deleted = 0 $category_query ORDER BY code LIMIT $offset, $rowsPerPage";
		$pageQuery = "SELECT count(*) as numrows FROM material WHERE deleted = 0 ORDER BY code";
	} else {
		$query_check_credentials = "SELECT * FROM material WHERE deleted = 0 AND code LIKE '" . $url_search . "%' $category_query ORDER BY code LIMIT $offset, $rowsPerPage";
		$pageQuery = "SELECT count(*) as numrows FROM material WHERE deleted = 0 AND code LIKE '" . $url_search . "%' $category_query ORDER BY code";
	}

	//$query_check_credentials = "SELECT * FROM material WHERE deleted=0 LIMIT $offset, $rowsPerPage";
}

$result = mysqli_query($dbc, $query_check_credentials);

$num_rows = mysqli_num_rows($result);
if($num_rows > 0) {
	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT material_dashboard FROM field_config"));
	$value_config = ','.$get_field_config['material_dashboard'].',';

	// Added Pagination //
	if(isset($pageQuery)) {
		echo display_pagination($dbc, $pageQuery, $pageNum, $rowsPerPage);
	}
	// Pagination Finish //

	echo "<table class='table table-bordered table-striped'>";
        echo "<thead>";
            echo "<tr class='hidden-xs hidden-sm'>";
                if (strpos($value_config, ','."Code".',') !== FALSE) {
                    echo '<th>Code</th>';
                }
                if (strpos($value_config, ','."Category".',') !== FALSE) {
                    echo '<th>Category</th>';
                }
                if (strpos($value_config, ','."Sub-Category".',') !== FALSE) {
                    echo '<th>Sub-Category</th>';
                }

                if (strpos($value_config, ','."Material Name".',') !== FALSE) {
                    echo '<th>Material Name</th>';
                }
                if (strpos($value_config, ','."Description".',') !== FALSE) {
                    echo '<th>Description</th>';
                }
                if (strpos($value_config, ','."Quote Description".',') !== FALSE) {
                    echo '<th>Quote Description</th>';
                }
                if (strpos($value_config, ','."Vendor".',') !== FALSE) {
                    echo '<th>Vendor</th>';
                }
                if (strpos($value_config, ','."Width".',') !== FALSE) {
                    echo '<th>Width</th>';
                }

                if (strpos($value_config, ','."Length".',') !== FALSE) {
                    echo '<th>Length</th>';
                }
                if (strpos($value_config, ','."Units".',') !== FALSE) {
                    echo '<th>Units</th>';
                }
                if (strpos($value_config, ','."Unit Weight".',') !== FALSE) {
                    echo '<th>Unit Weight</th>';
                }
                if (strpos($value_config, ','."Weight Per Feet".',') !== FALSE) {
                    echo '<th>Weight Per Foot</th>';
                }
                if (strpos($value_config, ','."Quantity".',') !== FALSE) {
                    echo '<th>Quantity</th>';
                }
                if (strpos($value_config, ','."Price".',') !== FALSE) {
                    echo '<th>Price</th>';
                }
                echo '<th>Function</th>';
            echo "</tr>";
        echo "</thead>";
} else{
	echo "<h2>No Record Found.</h2>";
}
while($row = mysqli_fetch_array( $result ))
{
	echo '<tr>';
	if (strpos($value_config, ','."Code".',') !== FALSE) {
		echo '<td data-title="Code">' . $row['code'] . '</td>';
	}
	if (strpos($value_config, ','."Category".',') !== FALSE) {
		echo '<td data-title="Category">' . $row['category'] . '</td>';
	}
	if (strpos($value_config, ','."Sub-Category".',') !== FALSE) {
		echo '<td data-title="Sub Category">' . $row['sub_category'] . '</td>';
	}

	if (strpos($value_config, ','."Material Name".',') !== FALSE) {
		echo '<td data-title="Name">' . $row['name'] . '</td>';
	}
	if (strpos($value_config, ','."Description".',') !== FALSE) {
		echo '<td data-title="Description">' . html_entity_decode($row['description']) . '</td>';
	}
	if (strpos($value_config, ','."Quote Description".',') !== FALSE) {
		echo '<td data-title="Quote Desc">' . html_entity_decode($row['quote_description']) . '</td>';
	}
	if (strpos($value_config, ','."Vendor".',') !== FALSE) {
		$vendorid = $row['vendorid'];
		$get_vendor = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT  name FROM contacts WHERE contactid='$vendorid'"));
		echo '<td data-title="Vendor">' . decryptIt($get_vendor['name']) . '</td>';
	}

	if (strpos($value_config, ','."Width".',') !== FALSE) {
		echo '<td data-title="Width">' . $row['width'] . '</td>';
	}
	if (strpos($value_config, ','."Length".',') !== FALSE) {
		echo '<td data-title="Length">' . $row['length'] . '</td>';
	}
	if (strpos($value_config, ','."Units".',') !== FALSE) {
		echo '<td data-title="Units">' . $row['units'] . '</td>';
	}
	if (strpos($value_config, ','."Unit Weight".',') !== FALSE) {
		echo '<td data-title="Unit Weight">' . $row['unit_weight'] . '</td>';
	}
	if (strpos($value_config, ','."Weight Per Feet".',') !== FALSE) {
		echo '<td data-title="Weight / Ft.">' . $row['weight_per_feet'] . '</td>';
	}
	if (strpos($value_config, ','."Quantity".',') !== FALSE) {
		echo '<td data-title="Quantity">' . $row['quantity'] . '</td>';
	}
	if (strpos($value_config, ','."Price".',') !== FALSE) {
		echo '<td data-title="Price">$' . $row['price'] . '</td>';
	}
	echo '<td data-title="Function">';
	if(vuaed_visible_function($dbc, 'material') == 1) {
	echo '<a href=\'add_material.php?materialid='.$row['materialid'].'\'>Edit</a> | ';
	echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&materialid='.$row['materialid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
	}
	echo '</td>';

	echo "</tr>";
}

echo '</table>';

// Added Pagination //
if(isset($pageQuery)) {
   echo display_pagination($dbc, $pageQuery, $pageNum, $rowsPerPage);
}
// Pagination Finish //