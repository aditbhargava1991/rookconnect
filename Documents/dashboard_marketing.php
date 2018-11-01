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
	$query_search .= " AND `marketing_material_type` = '".$_GET['search_type']."'";
}
if(!empty($_GET['search_category'])) {
	$query_search .= " AND `category` = '".$_GET['search_category']."'";
}
if(!empty($_GET['search_query'])) {
	$query_search .= " AND (marketing_material_code LIKE '%".$_GET['search_query']."%' OR marketing_material_type LIKE '%".$_GET['search_query']."%' OR category LIKE '%".$_GET['search_query']."%' OR heading LIKE '%".$_GET['search_query']."%' OR name LIKE '%".$_GET['search_query']."%' OR title LIKE '%".$_GET['search_query']."%' OR fee LIKE '%".$_GET['search_query']."%' OR marketing_materialid IN (SELECT `marketing_materialid` FROM `marketing_material_uploads` WHERE `document_link` LIKE '%".$_GET['search_query']."%'))";
}
$query_check_credentials = "SELECT * FROM marketing_material WHERE deleted = 0 $query_search LIMIT $offset, $rowsPerPage";
$query = "SELECT count(*) as numrows FROM marketing_material WHERE deleted = 0 $query_search";

$result = mysqli_query($dbc, $query_check_credentials);
$num_rows = mysqli_num_rows($result);

if($num_rows > 0) {
	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT marketing_material_dashboard FROM field_config"));
	if(empty($get_field_config['marketing_material_dashboard'])) {
		$get_field_config['marketing_material_dashboard'] = 'Marketing Material Type,Category,Title,Uploader,Link';
	}
	$value_config = ','.$get_field_config['marketing_material_dashboard'].',';

	//Added Pagination
	echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
	//Finish Pagination

	echo "<table id='no-more-tables' class='table table-bordered'>";
	echo "<tr class='hidden-xs hidden-sm'>";
		if (strpos($value_config, ','."Marketing Material Code".',') !== FALSE) {
			echo '<th>Marketing Material Code</th>';
		}
		if (strpos($value_config, ','."Marketing Material Type".',') !== FALSE) {
			echo '<th>Marketing Material Type</th>';
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
		if (strpos($value_config, ','."Send Email".',') !== FALSE) {
			echo '<th>Send</th>';
		}
		echo '<th>Function</th>';
		echo "</tr>";
} else {
	echo "<h2>No Record Found.</h2>";
}
while($row = mysqli_fetch_array( $result ))
{
	echo "<tr>";
	$marketing_materialid = $row['marketing_materialid'];
	if (strpos($value_config, ','."Marketing Material Code".',') !== FALSE) {
		echo '<td data-title="Code">' . $row['marketing_material_code'] . '</td>';
	}
	if (strpos($value_config, ','."Marketing Material Type".',') !== FALSE) {
		echo '<td data-title="Type">' . $row['marketing_material_type'] . '</td>';
	}
	if (strpos($value_config, ','."Category".',') !== FALSE) {
		echo '<td data-title="Category">' . $row['category'] . '</td>';
	}

	if (strpos($value_config, ','."Title".',') !== FALSE) {
		echo '<td data-title="Title">' . $row['title'] . '</td>';
	}
	if (strpos($value_config, ','."Uploader".',') !== FALSE) {
		echo '<td data-title="Upload">';
		$marketing_material_uploads1 = "SELECT * FROM marketing_material_uploads WHERE marketing_materialid='$marketing_materialid' AND type = 'Document' ORDER BY certuploadid DESC";
		$result1 = mysqli_query($dbc, $marketing_material_uploads1);
		$num_rows1 = mysqli_num_rows($result1);
		if($num_rows1 > 0) {
			while($row1 = mysqli_fetch_array($result1)) {
				echo '<ul  style="list-style:none; margin:0; padding:0;">';
				if(file_get_contents('../Marketing Material/download/'.$row1['document_link'])) {
					$download_link = '../Marketing Material/download/'.$row1['document_link'];
				} else {
					$download_link = 'download/'.$row1['document_link'];
				}

                $document_link_name = $row1['document_link_name'];
                if($row1['document_link_name'] == '') {
                    $document_link_name = $row1['document_link'];
                }
				echo '<li><a href="'.$download_link.'" target="_blank">'.$document_link_name.'</a></li>';

				echo '</ul>';
				if($num_rows1 > 1) {
				 echo '<hr style=" margin:0; padding:0;margin-bottom:3px;margin-top:3px;box-shadow:1px 1px 1px grey">';
				}
			}
		}
		echo '</td>';
	}
	if (strpos($value_config, ','."Link".',') !== FALSE) {
		echo '<td data-title="Link">';
		$marketing_material_uploads2 = "SELECT * FROM marketing_material_uploads WHERE marketing_materialid='$marketing_materialid' AND type = 'Link' ORDER BY certuploadid DESC";
		$result2 = mysqli_query($dbc, $marketing_material_uploads2);
		$num_rows2 = mysqli_num_rows($result2);
		if($num_rows2 > 0) {
			$link_no = 1;
			while($row2 = mysqli_fetch_array($result2)) {
				echo '<ul style="list-style:none; margin:0; padding:0;">';
                $document_link_name = $row2['document_link_name'];
                if($row2['document_link_name'] == '') {
                    $document_link_name = $row2['document_link'];
				    echo '<li><a target="_blank" href=\''.$row2['document_link'].'\'">Link '.$link_no.'</a></li>';
                } else {
				    echo '<li><a href="'.$download_link.'" target="_blank">'.$document_link_name.'</a></li>';
                }
                echo '</ul>';
				if($num_rows2 > 1) {
				 echo '<hr style=" margin:0; padding:0;margin-bottom:3px;margin-top:3px;box-shadow:1px 1px 1px grey">';
				}
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
		echo '<td data-title="'.TICKET_NOUN.' Desc.">' . html_entity_decode($row['ticket_description']) . '</td>';
	}
	if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) {
		echo '<td data-title="Retail Price">' . $row['final_retail_price'] . '</td>';
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
    if (strpos($value_config, ','."Send Email".',') !== FALSE) {
        echo '<td data-title="Send Email"><a href=\'?tile_name='.$tile_name.'&tab=marketing_material&send_material=true&marketing_materialid='.$marketing_materialid.'\'>Send</a></td>';
    }

	echo '<td data-title="Function">';
	if($edit_access == 1) {
	echo '<a href=\'?tile_name='.$tile_name.'&tab='.$_GET['tab'].'&edit='.$marketing_materialid.'\'>Edit</a> | ';
	echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&marketing_materialid='.$marketing_materialid.'&from_tile=documents_all&tile_name='.$tile_name.'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
	}
	echo '</td>';

	echo "</tr>";
}

echo '</table>';
//Added Pagination
echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
//Finish Pagination
?>