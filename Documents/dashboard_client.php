<?php include_once('../include.php');
if(!empty($_GET['tile_name'])) {
	checkAuthorised(false,false,'documents_all_'.$_GET['tile_name']);
} else {
	checkAuthorised('documents_all');
}
include_once('document_settings.php');

echo '<a href="?tile_name='.$tile_name.'&tab='.$_GET['tab'].'&edit=" class="btn brand-btn pull-right show-on-mob">New '.$tab_type.'</a><div class="clearfix"></div>';

/* Pagination Counting */
$rowsPerPage = 25;
$pageNum = 1;

if(isset($_GET['page'])) {
	$pageNum = $_GET['page'];
}

$offset = ($pageNum - 1) * $rowsPerPage;

$query_search = '';
if(!empty($_GET['search_type'])) {
	$query_search .= " AND `client_documents_type` = '".$_GET['search_type']."'";
}
if(!empty($_GET['search_category'])) {
	$query_search .= " AND `category` = '".$_GET['search_category']."'";
}
if(!empty($_GET['search_query'])) {
	$query_search .= " AND (client_documents_code LIKE '%".$_GET['search_query']."%' OR client_documents_type LIKE '%".$_GET['search_query']."%' OR category LIKE '%".$_GET['search_query']."%' OR heading LIKE '%".$_GET['search_query']."%' OR name LIKE '%".$_GET['search_query']."%' OR title LIKE '%".$_GET['search_query']."%' OR fee LIKE '%".$_GET['search_query']."%')";
}
$query_check_credentials = "SELECT * FROM client_documents WHERE deleted = 0 $query_search LIMIT $offset, $rowsPerPage";
$query = "SELECT count(*) as numrows FROM client_documents WHERE deleted = 0 $query_search";

$result = mysqli_query($dbc, $query_check_credentials);
$num_rows = mysqli_num_rows($result);

if($num_rows > 0) {
	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT client_documents_dashboard FROM field_config"));
	$value_config = ','.$get_field_config['client_documents_dashboard'].',';
	
	// Add Pagintion //
	echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
	// Complete Pagination //
	
	echo "<table id='no-more-tables' class='table table-bordered'>";
	echo "<tr class='hidden-sm hidden-xs'>";
		if (strpos($value_config, ','."Client".',') !== FALSE) {
			echo '<th>Client</th>';
		}
		if (strpos($value_config, ','."Client Documents Code".',') !== FALSE) {
			echo '<th>Client Documents Code</th>';
		}
		if (strpos($value_config, ','."Client Documents Type".',') !== FALSE) {
			echo '<th>Client Document Type</th>';
		}
		if (strpos($value_config, ','."Category".',') !== FALSE) {
			echo '<th>Category</th>';
		}
		if (strpos($value_config, ','."Title".',') !== FALSE) {
			echo '<th>Title</th>';
		}
		if (strpos($value_config, ','."Uploader".',') !== FALSE) {
			echo '<th>Documents</th>';
		}
		if (strpos($value_config, ','."Link".',') !== FALSE) {
			echo '<th>Link</th>';
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
	$client_documentsid = $row['client_documentsid'];
	if (strpos($value_config, ','."Client".',') !== FALSE) {
		echo '<td data-title="Client">' . get_client($dbc, $row['contactid']) . '</td>';
	}
	if (strpos($value_config, ','."Client Documents Code".',') !== FALSE) {
		echo '<td data-title="Doc. Code">' . $row['client_documents_code'] . '</td>';
	}
	if (strpos($value_config, ','."Client Documents Type".',') !== FALSE) {
		echo '<td data-title="Doc. Type">' . $row['client_documents_type'] . '</td>';
	}
	if (strpos($value_config, ','."Category".',') !== FALSE) {
		echo '<td data-title="Category">' . $row['category'] . '</td>';
	}

	if (strpos($value_config, ','."Title".',') !== FALSE) {
		echo '<td data-title="Title">' . $row['title'] . '</td>';
	}
	if (strpos($value_config, ','."Uploader".',') !== FALSE) {
		echo '<td data-title="Upload">';
		$client_documents_uploads1 = "SELECT * FROM client_documents_uploads WHERE client_documentsid='$client_documentsid' AND type = 'Document' ORDER BY certuploadid DESC";
		$result1 = mysqli_query($dbc, $client_documents_uploads1);
		$num_rows1 = mysqli_num_rows($result1);
		if($num_rows1 > 0) {
			while($row1 = mysqli_fetch_array($result1)) {
				echo '<ul>';
				if(file_get_contents('../Client Documents/download/'.$row1['document_link'])) {
					$download_link = '../Client Documents/download/'.$row1['document_link'];
				} else {
					$download_link = 'download/'.$row1['document_link'];
				}
				echo '<li><a href="'.$download_link.'" target="_blank">'.$row1['document_link'].'</a></li>';
				echo '</ul>';
			}
		}
		echo '</td>';
	}
	if (strpos($value_config, ','."Link".',') !== FALSE) {
		echo '<td data-title="Link">';
		$client_documents_uploads2 = "SELECT * FROM client_documents_uploads WHERE client_documentsid='$client_documentsid' AND type = 'Link' ORDER BY certuploadid DESC";
		$result2 = mysqli_query($dbc, $client_documents_uploads2);
		$num_rows2 = mysqli_num_rows($result2);
		if($num_rows2 > 0) {
			$link_no = 1;
			while($row2 = mysqli_fetch_array($result2)) {
				echo '<ul>';
				echo '<li><a target="_blank" href=\''.$row2['document_link'].'\'">Link '.$link_no.'</a></li>';
				echo '</ul>';
				$link_no++;
			}
		}
		echo '</td>';
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
	if (strpos($value_config, ','."Description".',') !== FALSE) {
		echo '<td data-title="Description">' . html_entity_decode($row['description']) . '</td>';
	}
	if (strpos($value_config, ','."Quote Description".',') !== FALSE) {
		echo '<td data-title="Quote Desc.">' . html_entity_decode($row['quote_description']) . '</td>';
	}
	if (strpos($value_config, ','."Invoice Description".',') !== FALSE) {
		echo '<td data-title="Invoice Desc.">' . html_entity_decode($row['invoice_description']) . '</td>';
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

	echo '<td data-title="Function">';
	if($edit_access == 1) {
	echo '<a href=\'?tile_name='.$tile_name.'&tab='.$_GET['tab'].'&edit='.$client_documentsid.'\'>Edit</a> | ';
	echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&client_documentsid='.$client_documentsid.'&from_tile=documents_all&tile_name='.$tile_name.'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
	}
	echo '</td>';

	echo "</tr>";
}

echo '</table>';

// Add Pagintion //
echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
// Complete Pagination //
?>