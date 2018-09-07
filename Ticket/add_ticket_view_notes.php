<?php include_once('../include.php');
include_once('../Ticket/field_list.php');
if(isset($_GET['ticketid']) && empty($ticketid)) {
	ob_clean();
	$strict_view = strictview_visible_function($dbc, 'ticket');
	$tile_security = get_security($dbc, ($_GET['tile_name'] == '' ? 'ticket' : 'ticket_type_'.$_GET['tile_name']));
	if($strict_view > 0) {
		$tile_security['edit'] = 0;
		$tile_security['config'] = 0;
	}
	$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
	$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM tickets WHERE ticketid='$ticketid'"));
	$ticket_type = $get_ticket['ticket_type'];
	$comment_type = filter_var($_GET['note_type'],FILTER_SANITIZE_STRING);
	$value_config = get_field_config($dbc, 'tickets');
	if(!empty($ticket_type)) {
		$value_config .= get_config($dbc, 'ticket_fields_'.$ticket_type).',';
	}

	//Action Mode Fields
	if($_GET['action_mode'] == 1) {
		$value_config_all = $value_config;
		$value_config = ','.get_config($dbc, 'ticket_action_fields').',';
		if(!empty($ticket_type)) {
			$value_config .= get_config($dbc, 'ticket_action_fields_'.$ticket_type).',';
		}
		if(empty(trim($value_config,','))) {
			$value_config = $value_config_all;
		} else {
			foreach($action_mode_ignore_fields as $action_mode_ignore_field) {
				if(strpos(','.$value_config_all.',',','.$action_mode_ignore_field.',') !== FALSE) {
					$value_config .= ','.$action_mode_ignore_field;
				}
			}
		}
	}
}

if($comment_type == 'member_note') {
	$notes = mysqli_query($dbc, "SELECT `client_daily_log_notes`.*, `tickets`.`ticket_type` FROM client_daily_log_notes LEFT JOIN `tickets` ON `client_daily_log_notes`.`ticketid`=`tickets`.`ticketid` WHERE `client_daily_log_notes`.ticketid='$ticketid' AND `client_daily_log_notes`.`deleted`=0 ORDER BY note_id DESC");
} else {
	$notes = mysqli_query($dbc, "SELECT `ticket_comment`.*, `tickets`.`ticket_type` FROM ticket_comment LEFT JOIN `tickets` ON `ticket_comment`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_comment`.ticketid='$ticketid' AND `ticket_comment`.type='$comment_type' AND `ticket_comment`.`deleted`=0 ORDER BY ticketcommid DESC");
}
$ticket_notes_limit = get_config($dbc, 'ticket_notes_limit');
$ticket_notes_limit = $ticket_notes_limit < 1 ? 1 : $ticket_notes_limit;
if($generate_pdf) {
	ob_clean();
}
$notes_count = mysqli_num_rows($notes);
if($ticketid > 0 && $notes_count > 0) {
	$note_delete = check_subtab_persmission($dbc, 'ticket', ROLE, 'delete_notes');
	$note_i = 0;
	while($row = mysqli_fetch_array($notes)) {
		$bg_class = $note_i % 2 == 0 ? 'row-even-bg' : 'row-odd-bg';
        echo '<div class="note_block '.$bg_class.'" '.(strpos($value_config, ','."Notes Limit".',') !== FALSE && $note_i >= $ticket_notes_limit ? 'style="display:none;"' : '').'>';
			if($comment_type == 'member_note') {
				echo '<input type="hidden" name="deleted" data-table="client_daily_log_notes" data-id="'.$row['note_id'].'" data-id-field="note_id" value="0">';
			} else {
				echo '<input type="hidden" name="deleted" data-table="ticket_comment" data-id="'.$row['ticketcommid'].'" data-id-field="ticketcommid" value="0">';
			}
			echo profile_id($dbc, $row['created_by']);
			echo '<div class="pull-right" style="width: calc(100% - 3.5em);">'.html_entity_decode($row['comment'].$row['note']);
			echo "<em>Added by ".get_contact($dbc, $row['created_by'])." at ".$row['note_date'].$row['created_date'];
			if($row['reference_contact'] > 0) {
				echo "<br />References ".get_contact($dbc, $row['reference_contact']);
			}
			if($row['client_id'] > 0) {
				echo "<br />References ".get_contact($dbc, $row['client_id']);
			}
			foreach(explode(',',$row['email_comment']) as $assignid) {
				if($assignid > 0) {
					echo "<br />Assigned to ".get_contact($dbc, $assignid);
				}
			}
			echo "</em>";
			echo ($note_delete ? ' <a href="" onclick="$(this).closest(\'.note_block\').hide().find(\'input[name=deleted]\').val(1).change(); return false;" class="pull-right">Delete</a>' : '');
		echo '</div><div class="clearfix"></div></div>';
		$note_i++;
	}
	if(strpos($value_config, ','."Notes Limit".',') !== FALSE && $notes_count > $ticket_notes_limit) {
		echo '<a href="" onclick="$(this).closest(\'.tab-section,.panel-body\').find(\'.note_block\').show(); $(this).hide(); return false;" class="pull-right">View All Notes</a>';
	}
} else if($ticketid > 0) {
	// echo "<h4>No Notes Found</h4>";
}
if($generate_pdf) {
	$pdf_contents[] = ['', ob_get_contents()];
}