<?php $result = mysqli_query($dbc, "SELECT certificate.*, `project`.`project_name` FROM certificate LEFT JOIN `project` ON `certificate`.`projectid`=`project`.`projectid` WHERE `certificate`.`projectid`='$projectid' ORDER BY `certificate`.expiry_date ASC");

if(vuaed_visible_function($dbc, 'certificate') == 1) {
	echo '<a href="../Certificate/add_certificate.php?projectid='.$projectid.'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" class="btn brand-btn mobile-block pull-right">Add Certificate</a>';
	echo '<span class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add Certificates."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
}
if(mysqli_num_rows($result) > 0) {
	$value_config = ','.mysqli_fetch_assoc(mysqli_query($dbc,"SELECT certificate_dashboard FROM field_config"))['certificate_dashboard'].',';
    echo '<div id="no-more-tables"><table class="table table-bordered">';
    echo '<tr class="hidden-xs hidden-sm">';
	if (strpos($value_config, ','."Certificate Code".',') !== FALSE) {
		echo '<th>Certificate Code</th>';
	}
	if (strpos($value_config, ','."Certificate Type".',') !== FALSE) {
		echo '<th>Certificate Type</th>';
	}
	if (strpos($value_config, ','."Category".',') !== FALSE) {
		echo '<th>Category</th>';
	}
	if (strpos($value_config, ','."Title".',') !== FALSE) {
		echo '<th>Title</th>';
	}

	if (strpos($value_config, ','."Issue Date".',') !== FALSE) {
		echo '<th>Issue Date</th>';
	}
	if (strpos($value_config, ','."Expiry Date".',') !== FALSE) {
		echo '<th>Expiry Date</th>';
	}
	if (strpos($value_config, ','."Reminder Date".',') !== FALSE) {
		echo '<th>Reminder Date</th>';
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
		echo '<th>Ticket Description</th>';
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
    echo '</tr>';
	$total_time = 0;
	while($row = mysqli_fetch_array( $result )) {
		echo '<tr>';

		if (strpos($value_config, ','."Certificate Code".',') !== FALSE) {
			echo '<td data-title="Certificate Code">' . $row['certificate_code'] . '</td>';
		}
		if (strpos($value_config, ','."Certificate Type".',') !== FALSE) {
			echo '<td data-title="Certificate Type">' . $row['certificate_type'] . '</td>';
		}
		if (strpos($value_config, ','."Category".',') !== FALSE) {
			echo '<td data-title="Category">' . $row['category'] . '</td>';
		}
		if (strpos($value_config, ','."Title".',') !== FALSE) {
			echo '<td data-title="Title">' . $row['title'] . '</td>';
		}
		if (strpos($value_config, ','."Issue Date".',') !== FALSE) {
			echo '<td data-title="Issue Date">' . $row['issue_date'] . '</td>';
		}
		if (strpos($value_config, ','."Expiry Date".',') !== FALSE) {
			echo '<td data-title="Expiry Date">' . $row['expiry_date'] . '</td>';
		}
		if (strpos($value_config, ','."Reminder Date".',') !== FALSE) {
			echo '<td data-title="Reminder Date">' . $row['reminder_date'] . '</td>';
		}
		if (strpos($value_config, ','."Uploader".',') !== FALSE) {
			echo '<td data-title="Documents">';
			$certificate_uploads1 = "SELECT * FROM certificate_uploads WHERE certificateid='$certificateid' AND type = 'Document' ORDER BY certuploadid DESC";
			$result1 = mysqli_query($dbc, $certificate_uploads1);
			$num_rows1 = mysqli_num_rows($result1);
			if($num_rows1 > 0) {
				while($row1 = mysqli_fetch_array($result1)) {
					echo '<ul>';
					echo '<li><a href="download/'.$row1['document_link'].'" target="_blank">'.$row1['document_link'].'</a></li>';
					echo '</ul>';
				}
			}
			echo '</td>';
		}
		if (strpos($value_config, ','."Link".',') !== FALSE) {
			echo '<td data-title="Link">';
			$certificate_uploads2 = "SELECT * FROM certificate_uploads WHERE certificateid='$certificateid' AND type = 'Link' ORDER BY certuploadid DESC";
			$result2 = mysqli_query($dbc, $certificate_uploads2);
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
			echo '<td data-title="Quote Description">' . html_entity_decode($row['quote_description']) . '</td>';
		}
		if (strpos($value_config, ','."Invoice Description".',') !== FALSE) {
			echo '<td data-title="Invoice Description">' . html_entity_decode($row['invoice_description']) . '</td>';
		}
		if (strpos($value_config, ','."Ticket Description".',') !== FALSE) {
			echo '<td data-title="Ticket Description">' . html_entity_decode($row['ticket_description']) . '</td>';
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
			echo '<td data-title="MRSP">' . $row['msrp'] . '</td>';
		}

		echo '<td data-title="Function">';
		if(vuaed_visible_function($dbc, 'certificate') == 1) {
			echo '<a href=\''.WEBSITE_URL.'/Certificate/add_certificate.php?certificateid='.$certificateid.'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'\'>Edit</a> | ';
			echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&certificateid='.$certificateid.'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
		}
		echo '</td>';

		echo "</tr>";
	}

	echo '</table></div>';
} else {
    echo "<h2>No Certificates Found.</h2>";
}
if(vuaed_visible_function($dbc, 'certificate') == 1) {
	echo '<a href="../Certificate/add_certificate.php?projectid='.$projectid.'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" class="btn brand-btn mobile-block pull-right">Add Certificate</a>';
	echo '<span class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add Certificates."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
}