<?php include_once('../include.php');
if(isset($_GET['ticketid']) && empty($ticketid)) {
	$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
	$strict_view = strictview_visible_function($dbc, 'ticket');
	$tile_security = get_security($dbc, ($_GET['tile_name'] == '' ? 'ticket' : 'ticket_type_'.$_GET['tile_name']));
	if($strict_view > 0) {
		$tile_security['edit'] = 0;
		$tile_security['config'] = 0;
	}
	ob_clean();
}
if(!empty($_GET['doc_type']) && empty($doc_type)) {
	$doc_type = $_GET['doc_type'];
}

if($doc_type == 'project') {
	$projectid = get_field_value('projectid','tickets','ticketid',$ticketid);
	$doc_sql = "SELECT IFNULL(NULLIF(`label`,''),`upload`) `label`, CONCAT('../Project/download/',`upload`) `link`, `category`, `created_by` FROM `project_document` WHERE `projectid`='$projectid' AND `deleted`=0 AND IFNULL(`upload`,'') != ''";
	$doc_list = $dbc->query($doc_sql);
	if($doc_list->num_rows > 0) {
		echo '<h4>'.PROJECT_NOUN.' Documents</h4>';
		echo "<table class='table table-bordered'>
		<tr class='hidden-xs hidden-sm'>
		<th>Tab</th>
		<th>Document</th>
		<th>Added By</th>
		</tr>";
		while($doc = $doc_list->fetch_assoc()) {
			echo '<tr>
				<td data-title="Tab">'.$doc['category'].'</td>
				<td data-title="Document"><a href="'.$doc['link'].'">'.$doc['label'].'</a></td>
				<td data-title="Added By">'.get_contact($dbc,$doc['created_by']).'</td>
			</tr>';
		}
		echo "</table>";
	}
} else if($doc_type == 'contact') {
	$contacts = get_field_value('businessid clientid','tickets','ticketid',$ticketid);
	foreach($contacts as $contactid) {
		foreach(explode(',',$contactid) as $contactid) {
			if($contactid > 0) {
				$doc_sql = "SELECT IFNULL(NULLIF(`label`,''),`upload`) `label`, CONCAT('../Contacts/download/',`upload`) `link` FROM `contact_document` WHERE `contactid`='$contactid' AND `deleted`=0 AND IFNULL(`upload`,'') != ''";
				$doc_list = $dbc->query($doc_sql);
				if($doc_list->num_rows > 0) {
					echo '<h4>'.get_contact($dbc, $contactid).' Documents</h4>';
					echo "<table class='table table-bordered'>
					<tr class='hidden-xs hidden-sm'>
					<th>Document</th>
					</tr>";
					while($doc = $doc_list->fetch_assoc()) {
						echo '<tr>
							<td data-title="Document"><a href="'.$doc['link'].'">'.$doc['label'].'</a></td>
						</tr>';
					}
					echo "</table>";
				}
			}
		}
	}
} else {
	$query_check_credentials = "SELECT * FROM ticket_document WHERE ticketid='$ticketid' AND `deleted`=0 ORDER BY ticketdocid DESC";
	$result = mysqli_query($dbc, $query_check_credentials);
	$num_rows = mysqli_num_rows($result);
	if($ticketid > 0 && $num_rows > 0) {
		echo "<table class='table table-bordered'>
		<tr class='hidden-xs hidden-sm'>
		<th>Tab</th>
		<th>Document/Link</th>
		<th>Date</th>
		<th>Added By</th>
		<th></th>
		</tr>";
		while($row = mysqli_fetch_array($result)) {
			echo '<tr>';
			$by = $row['created_by'];
			echo '<td data-title="Tab"><select name="type" data-table="ticket_document" data-id="'.$row['ticketdocid'].'" data-id-field="ticketdocid" class="chosen-select">';
			echo '<option '.($row['type'] == 'Support' ? 'selected' : '').' value="Support">Support</option><option '.($row['type'] == 'Review' ? 'selected' : '').' value="Review">Review</option></td>';
			if($row['document'] != '') {
				echo '<td data-title="Document"><a href="download/'.$row['document'].'" target="_blank">'.($row['label'] == '' ? $row['document'] : $row['label']).'</a>';
			} else {
				echo '<td data-title="Link"><a target="_blank" href=\''.$row['link'].'\'">'.($row['label'] == '' ? $row['link'] : $row['label']).'</a>';
			}
			echo '<input type="text" class="form-control" style="display:none;" name="label" value="'.$row['label'].'" data-table="ticket_document" data-id="'.$row['ticketdocid'].'" data-id-field="ticketdocid" onblur="$(this).hide(); $(this).closest(\'td\').find(\'a\').text(this.value);"></td>';
			echo '<td data-title="Date">'.$row['created_date'].'</td>';
			echo '<td data-title="Added By">'.get_staff($dbc, $by).'</td>';
			echo '<td data-title="Function"><a href="" onclick="if(confirm(\'Are you sure?\')) { $(this).closest(\'td\').find(\'[name=deleted]\').val(\'1\').change().closest(\'tr\').hide(); } return false;">Delete</a> |
			<a href="" onclick="$(this).closest(\'tr\').find(\'[name=label]\').show().focus(); return false;">Rename</a>';
			echo '<input type="hidden" name="deleted" value="'.$row['deleted'].'" data-table="ticket_document" data-id="'.$row['ticketdocid'].'" data-id-field="ticketdocid" onblur="$(this).hide(); $(this).closest(\'td\').find(\'a\').text(this.value);"></td>';
			echo '</tr>';;

			//PDF Contents
			$pdf_content = '';
			$pdf_content .= 'Type: '.$row['type'].'<br>';
			if($row['document'] != '') {
				$pdf_content .= 'Document: '.($row['label'] == '' ? $row['document'] : $row['label'].' - '.$row['document']).'<br>';
			} else {
				$pdf_content .= 'Link: '.($row['label'] == '' ? $row['link'] : $row['label'].' - '.$row['link']).'<br>';
			}
			$pdf_content .= 'Date: '.$row['created_date'].'<br>';
			$pdf_content .= 'Added By: '.get_staff($dbc, $by);

			$pdf_contents[] = [$row['document'] != '' ? 'Document' : 'Link', $pdf_content];
		}
		echo '</table>';
	} else if($ticketid > 0) {
		// echo "<h4>No Documents or Links Found</h4>";
	}
}