<?php
if($tile_security['edit'] == 1) {
	echo "";
	echo '<div class="pull-right gap-bottom">';
		?><span class="popover-examples list-inline" style="margin:0 5px 0 10px;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
	echo '<a href="../Ticket/index.php?'.$current_tile.'edit=0&from='.WEBSITE_URL.$_SERVER['REQUEST_URI'].'" class="btn brand-btn mobile-block">Add '.TICKET_NOUN.'</a>';
	echo '</div>';
}
echo 'Displaying a total of '.mysqli_num_rows($result).' '.TICKET_TILE.'.';
echo '<div id="no-more-tables"><table class="table table-bordered">';
echo '<tr class="hidden-xs hidden-sm">
		'.(!in_array('Label',$db_config) ? '<th>'.TICKET_NOUN.' #</th>' : '<th>'.TICKET_NOUN.'</th>').'
		'.(in_array('Project',$db_config) ? '<th>'.PROJECT_NOUN.' Information</th>' : '').'
		'.(in_array('Business',$db_config) || in_array('Contact',$db_config) ?
			('<th>'.(in_array('Business',$db_config) ? 'Business<br>' : '').
			(in_array('Contact',$db_config) ? 'Contact' : '').'</th>') : '').'
		'.(in_array('Services',$db_config) ? '<th>Service</th>' : '').'
		'.(in_array('Heading',$db_config) ? '<th>'.TICKET_NOUN.' Heading</th>' : '').'
		'.(in_array('Staff',$db_config) ? '<th>Staff</th>' : '').'
		'.(in_array('Members',$db_config) ? '<th>Members</th>' : '').'
		'.(in_array('Clients',$db_config) ? '<th>Clients</th>' : '').'
		'.(in_array('Create Date',$db_config) ? '<th>Created Date</th>' : '').'
		'.(in_array('Ticket Date',$db_config) ? '<th>Date</th>' : '').'
		'.(in_array('Deliverable Date',$db_config) ? '<th>TO DO</th>
			<th>Internal QA</th>
			<th>Deliverable</th>' : '').'
		'.(in_array('Documents',$db_config) ? '<th>Documents</th>' : '').'
		'.(in_array('Invoiced',$db_config) ? '<th>Invoiced</th>' : '').'
		'.(in_array('Status',$db_config) ? '<th>Current Status</th>' : '').'

		<th>Function</th>
		'.(!isset($edit_access) && check_subtab_persmission($dbc, 'ticket', ROLE, 'view_history') ? '<th>History</th>' : '').'
	</tr>';
$project_security = get_security($dbc, 'project');
while($row = mysqli_fetch_array( $result )) {
	echo '<tr>
		'.(!in_array('Label',$db_config) ? '<td data-title="'.TICKET_NOUN.' #">'.($tile_security['edit'] == 1 ? '<a href=\'../Ticket/index.php?'.$current_tile.'edit='.$row['ticketid'].'&from='.WEBSITE_URL.$_SERVER['REQUEST_URI'].'\'>'.($row['main_ticketid'] > 0 ? $row['main_ticketid'].' '.$row['sub_ticket']
			: $row['ticketid']).'</a>' : $row['ticketid']).'</td>' : '<td data-title="'.TICKET_NOUN.' #">'.($tile_security['edit'] == 1 ? '<a href=\'../Ticket/index.php?'.$current_tile.'edit='.$row['ticketid'].'&from='.WEBSITE_URL.$_SERVER['REQUEST_URI'].'\'>'.get_ticket_label($dbc, $row).'</a>' : $row['ticketid']).'</td>');
	if(in_array('Project',$db_config)) {
		echo '<td data-title="'.PROJECT_NOUN.' Information">';
			if($row['projectid'] > 0) {
				$project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='".$row['projectid']."'"));
				echo PROJECT_NOUN.' #'.$project['projectid'].' '.$project['project_name'].'<br />'.$project_types[$project['projecttype']];
			} else {
				echo 'No '.PROJECT_NOUN;
			}
		echo '</td>';
	}
	if(in_array('Business',$db_config) || in_array('Contact',$db_config)) {
		echo '<td data-title="'.(in_array('Business',$db_config) ? 'Business ' : '').(in_array('Contact',$db_config) ? 'Contact' : '').'">';
		echo (in_array('Business',$db_config) ? get_contact($dbc,$row['businessid'],'name').'<br />' : '');
		if(in_array('Contact',$db_config)) {
			foreach(array_filter(explode(',',$row['clientid'])) as $clientid) {
				echo get_contact($dbc, $clientid).'<br />';
			}
		}
		echo '</td>';
	}
	if(in_array('Services',$db_config)) {
		echo '<td data-title="Service">';
		foreach(array_filter(explode(',',$row['serviceid'])) as $service) {
			$service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `category`, `heading` FROM `services` WHERE `serviceid`='$service'"));
			echo ($service['category'] == '' ? '' : $service['category'].': ').$service['heading'].'<br />';
		}
		echo '</td>';
	}
	echo (in_array('Heading',$db_config) ? '<td data-title="'.TICKET_NOUN.' Heading">'.$row['heading'].'</td>' : '');
	if(in_array('Staff',$db_config)) {
		echo '<td data-title="Staff">';
		foreach(array_filter(explode(',',$row['contactid'])) as $staff) {
			echo '<a href="../Staff/staff_edit.php?contactid='.$staff.'">'.get_contact($dbc, $staff).'</a><br />';
		}
		foreach(array_filter(explode(',',$row['internal_qa_contactid'])) as $staff) {
			echo '<a href="../Staff/staff_edit.php?contactid='.$staff.'">'.get_contact($dbc, $staff).'</a> (Internal QA)<br />';
		}
		foreach(array_filter(explode(',',$row['deliverable_contactid'])) as $staff) {
			echo '<a href="../Staff/staff_edit.php?contactid='.$staff.'">'.get_contact($dbc, $staff).'</a> (Deliverable)<br />';
		}
		echo '</td>';
	}
	if(in_array('Members',$db_config)) {
		echo '<td data-title="Members">';
		$member_list = mysqli_query($dbc, "SELECT `item_id` FROM `ticket_attached` WHERE `src_table`='members' AND `ticketid`='{$row['ticketid']}' AND `deleted`=0");
		while($member = mysqli_fetch_assoc($member_list)['item_id']) {
			echo '<a href="../Members/contact_inbox.php?edit='.$member.'">'.get_contact($dbc, $member).'</a><br />';
		}
		echo '</td>';
	}
	if(in_array('Clients',$db_config)) {
		echo '<td data-title="Clients">';
		$member_list = mysqli_query($dbc, "SELECT `item_id` FROM `ticket_attached` WHERE `src_table`='clients' AND `ticketid`='{$row['ticketid']}' AND `deleted`=0");
		while($member = mysqli_fetch_assoc($member_list)['item_id']) {
			echo '<a href="../ClientInfo/contacts_inbox.php?edit='.$member.'">'.get_contact($dbc, $member).'</a><br />';
		}
		echo '</td>';
	}
	if(in_array('Create Date',$db_config)) {
		echo '<td data-title="Date Created">'.$row['created_date'].'</td>';
	}
	if(in_array('Ticket Date',$db_config)) {
		echo '<td data-title="Date">';
			$dates = mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE IFNULL(`to_do_date`,'0000-00-00')!='0000-00-00' AND `ticketid`='".$row['ticketid']."'");
			if($dates->num_rows > 0) {
				while($date_row = $dates->fetch_assoc()) {
					switch($date_row['type']) {
						case 'origin': echo 'Shipment Date: '; break;
						case 'destination': echo 'Delivery Date: '; break;
						case '': break;
						default: echo $date_row['type'].': '; break;
					}
					echo $date_row['to_do_date']."<br />\n";
				}
			} else {
				echo $ticket['to_do_date'];
			}
		echo '</td>';
	}
	if(in_array('Deliverable Date',$db_config)) {
		echo '<td data-title="TO DO">'.($row['to_do_date'] == '' ? '' : $row['to_do_date'].'<br />');
		foreach(array_filter(explode(',', $row['contactid'])) as $staff) {
			echo get_contact($dbc, $staff).'<br />';
		}
		echo $row['max_time'].'</td>';
		echo '<td data-title="Internal QA">'.($row['internal_qa_date'] == '' ? '' : $row['internal_qa_date'].'<br />');
		foreach(array_filter(explode(',', $row['internal_qa_contactid'])) as $staff) {
			echo get_contact($dbc, $staff).'<br />';
		}
		echo '</td>';
		echo '<td data-title="Deliverable">'.($row['deliverable_date'] == '' ? '' : $row['deliverable_date'].'<br />');
		foreach(array_filter(explode(',', $row['deliverable_contactid'])) as $staff) {
			echo get_contact($dbc, $staff).'<br />';
		}
		echo '</td>';
	}
	if(in_array('Documents',$db_config)) {
		echo '<td data-title="Documents">';
			$documents = mysqli_query($dbc, "SELECT IFNULL(NULLIF(`label`,''),`document`) `label`, CONCAT('download/',`document`) `link` FROM `ticket_document` WHERE `ticketid`='".$row['ticketid']."' AND `deleted`=0 AND IFNULL(`document`,'') != '' UNION
				SELECT CONCAT('Project: ',IFNULL(NULLIF(`label`,''),`upload`)) `label`, CONCAT('../Project/download',`upload`) `link` FROM `project_document` WHERE `projectid`='".$row['projectid']."' AND `deleted`=0 AND IFNULL(`upload`,'') != ''");
			while($document = $documents->fetch_assoc()) {
				echo '<a href="'.$document['link'].'">'.$document['label']."</a><br />\n";
			}
		echo '</td>';
	}
	if(in_array('Invoiced',$db_config)) {
		echo '<td data-title="Invoiced">';
			echo $row['invoiced'] > 0 ? 'Yes' : 'No';
		echo '</td>';
	}
	if(in_array('Status',$db_config)) {
		echo '<td data-title="Current Status">';
			if($tile_security['edit'] > 0) {
				echo '<select name="status[]" id="status_'.$row['ticketid'].'" class="chosen-select-deselect1 form-control">
						<option value=""></option>';
						foreach ($ticket_status_list as $cat_tab) {
							echo "<option ".($row['status'] == $cat_tab ? 'selected' : '')." value='". $cat_tab."'>".$cat_tab.'</option>';
						}
				echo '</select>';
			} else {
				echo $row['status'];
                if($row['status'] == 'Archive' || $row['status'] == 'Archived' || $row['deleted'] == 1) {
                    echo '<br>'.$row['date_of_archival'];
                }
			}
		echo '</td>';
	}

	echo '<td data-title="Function">';
		$functions = [];
		if(in_array('Export Ticket Log',$db_config)) {
			$ticket_log_template = !empty(get_config($dbc, 'ticket_log_template')) ? get_config($dbc, 'ticket_log_template') : 'template_a';
			$functions[] = '<a href="../Ticket/ticket_log_templates/'.$ticket_log_template.'_pdf.php?ticketid='.$row['ticketid'].'">Export '.TICKET_NOUN.' Log</a>';
		}
		if(in_array('PDF',$db_config) && check_subtab_persmission($dbc, 'ticket', ROLE, 'view_pdf')) {
			$functions[] = '<a href="../Ticket/ticket_pdf.php?ticketid='.$row['ticketid'].'">View PDF <img src="../img/pdf.png" class="inline-img small"></a>';
		}
		if($tile_security['edit'] == 1) {
			$functions[] = '<a href=\'../Ticket/index.php?'.$current_tile.'edit='.$row['ticketid'].'&from='.WEBSITE_URL.$_SERVER['REQUEST_URI'].'\'>Edit</a>';
			$functions[] = '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&ticketid='.$row['ticketid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
		} else if($edit_access > 0) {
			$functions[] = '<a href=\'../Ticket/index.php?'.$current_tile.'edit='.$row['ticketid'].'&from='.WEBSITE_URL.$_SERVER['REQUEST_URI'].'\'>View</a>';
			$functions[] = '<a href=\'../delete_restore.php?action=restore&ticketid='.$row['ticketid'].'&category=tickets\' onclick="return confirm(\'Are you sure you want to restore this item?\')">Restore</a>';
			$functions[] = '<a href=\'../delete_restore.php?action=delete_2&ticketid='.$row['ticketid'].'&category=tickets\' onclick="return confirm(\'By deleting this item, you may never be able to gain access to this item again. Are you sure you want to delete this item?\')">Delete</a>';
		} else {
			$functions[] = '<a href=\'../Ticket/index.php?'.$current_tile.'edit='.$row['ticketid'].'&from='.WEBSITE_URL.$_SERVER['REQUEST_URI'].'\'>View</a>';
		}
		echo implode('<br />',$functions);
	echo '</td>';
	if(!isset($edit_access)) {
		echo '<td data-title="History"><span class="iframe_open" id="'.$row['ticketid'].'" style="cursor:pointer">View All</span></td>';
	}
	echo '</tr>';
}

echo '</table></div>';

if($tile_security['edit'] == 1) {
	echo "";
	echo '<div class="pull-right gap-bottom">';
		?><span class="popover-examples list-inline" style="margin:0 5px 0 10px;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
	echo '<a href="../Ticket/index.php?'.$current_tile.'edit=0&from='.WEBSITE_URL.$_SERVER['REQUEST_URI'].'" class="btn brand-btn mobile-block">Add '.TICKET_NOUN.'</a>';
	echo '</div>';
} ?>