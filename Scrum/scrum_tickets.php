<?php if($scrum_tab != 'status') { ?>
	<div class="scrum_tickets" id="scrum_tickets">
<?php }
$ticket_status = get_config($dbc, 'ticket_status').','.get_config($dbc,'task_status');
if($scrum_tab == 'status') {
	foreach(explode(',',$ticket_status) as $status_name) {
		if(config_safe_str($status_name) == $_GET['subtab']) {
			$ticket_status = $status_name;
		}
	}
} else {
	$ticket_status = str_replace("Doing Today","Yesterday,Doing Today",$ticket_status);
	$ticket_status = str_replace(",Archive","",$ticket_status);
}

$each_tab = array_filter(array_unique(explode(',', $ticket_status)));

$i=0;
foreach ($each_tab as $cat_tab) {
	$status = $cat_tab;
	$status = str_replace("&","FFMEND",$status);
	$status = str_replace(" ","FFMSPACE",$status);
	$status = str_replace("#","FFMHASH",$status);

	$class_on = '';
	if($check_table_orient == '1') {
		$class_on = 'horizontal-on';
		$class_on_2 = 'horizontal-on-title';
	} else {
		$class_on = '';
		$class_on_2 = '';
	}

	echo '<ul id="sortable'.$i.'" class="'.($scrum_tab == 'status' ? 'connectedChecklist' : 'connectedSortable').' '.$status.' '.$class_on.'">
	<li class="ui-state-default ui-state-disabled no-sort '.$class_on_2.'">'.$cat_tab.'</li>';

	echo '<li class="new_task_box no-sort"><input onChange="changeEndAme(this)" name="add_ticket" id="add_new_ticket '.$status.'" type="text" class="form-control" /></li>';

	$today_date = date('Y-m-d');
	$yesterday = date('Y-m-d',strtotime("-1 days"));

	if($scrum_tab != 'all' || $_GET['subtab'] == 'tickets') {
		if($cat_tab == 'Yesterday'){
			$result = mysqli_query($dbc,"SELECT * FROM tickets WHERE (internal_qa_date='$yesterday' OR deliverable_date='$yesterday' OR '$yesterday' BETWEEN to_do_date AND to_do_end_date) AND status != 'Archive' $query_clause $ticket_query ORDER BY status");
		} else if($cat_tab == 'Doing Today') {
			$result = mysqli_query($dbc,"SELECT * FROM tickets WHERE (internal_qa_date='$today_date' OR deliverable_date='$today_date' OR '$today_date' BETWEEN to_do_date AND to_do_end_date) AND status != 'Archive' $query_clause $ticket_query ORDER BY status");
		} else {
			$result = mysqli_query($dbc, "SELECT * FROM tickets WHERE status='$cat_tab' $query_clause $ticket_query ORDER BY status");
		}

		while($row = mysqli_fetch_array( $result )) {
			$status_date = "";
			switch($row['status']) {
				case 'Scheduled/To Do': $status_date = $row['to_do_date'] != '0000-00-00' ? '['.$row['to_do_date'].']' : ''; break;
				case 'Internal QA': $status_date = $row['internal_qa_date'] != '0000-00-00' ? '['.$row['internal_qa_date'].']' : ''; break;
				case 'Customer QA': $row['deliverable_date'] != '0000-00-00' ? $status_date = '['.$row['deliverable_date'].']' : ''; break;
			}
			echo '<li id="'.$row['ticketid'].'" class="ui-state-default '.$class_on.'" style=" '.($row['flag_colour'] != '' ? 'background-color: #'.$row['flag_colour'].';' : '').'">';
			echo '<span class="pull-right" style="width: 100%;" data-ticket="'.$row['ticketid'].'" data-class="ticket">';
			echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Flag This!" onclick="flag_item(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" style="height:1.5em;"></span>';
			echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Send Alert" onclick="send_alert(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-alert-icon.png" style="height:1.5em;" onclick="return false;"></span>';
			echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Send Email" onclick="send_email(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" style="height:1.5em;" onclick="return false;"></span>';
			echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Schedule Reminder" onclick="send_reminder(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" style="height:1.5em;" onclick="return false;"></span>';
			echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Attach File" onclick="attach_file(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" style="height:1.5em;" onclick="return false;"></span>';
			echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Add Note" onclick="send_reply(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" style="height:1.5em;" onclick="return false;"></span>';
			echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Add Time" onclick="add_time(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-timer-icon.png" style="height:1.5em;" onclick="return false;"></span>';
			echo '<span style="display:inline-block; text-align:center; width:12.5%;" title="Archive Item" onclick="archive(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" style="height:1.5em;" onclick="return false;"></span>';
			echo '</span><div class="clearfix"></div><a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\',\'auto\',true,true); return false;">'.TICKET_NOUN.' #'.$row['ticketid'].' : '.get_client($dbc, $row['businessid']).' - '.limit_text($row['heading'], 5 ).'<br><em>'.get_multiple_contact($dbc, $row['contactid']).$status_date. '</em></a>';
			echo '<img class="drag_handle pull-right" src="'.WEBSITE_URL.'/img/icons/hold.png" style="height:1.5em; width:1.5em;" />';
			echo '<input type="text" name="reply_'.$row['ticketid'].'" style="display:none; margin-top: 2em;" class="form-control" />';
			echo '<input type="text" name="ticket_time_'.$row['ticketid'].'" style="display:none; margin-top: 2em;" class="form-control timepicker" />';
			echo '<input type="text" name="reminder_'.$row['ticketid'].'" style="display:none; margin-top: 2em;" class="form-control datepicker" />';
			echo '<input type="file" name="attach_'.$row['ticketid'].'" style="display:none;" class="form-control" /></li>';
		}
	}

	if($scrum_tab != 'all' || $_GET['subtab'] == 'tasks') {
		if($cat_tab == 'Yesterday'){
			$result = mysqli_query($dbc,"SELECT * FROM tasklist WHERE task_tododate='$yesterday' AND status != 'Archive' AND `deleted`=0 $query_clause $task_query ORDER BY status");
		} else if($cat_tab == 'Doing Today') {
			$result = mysqli_query($dbc,"SELECT * FROM tasklist WHERE task_tododate='$today_date' AND status != 'Archive' AND `deleted`=0 $query_clause $task_query ORDER BY status");
		} else {
			$result = mysqli_query($dbc, "SELECT * FROM tasklist WHERE status='$cat_tab' AND `deleted`=0 $query_clause $task_query ORDER BY status");
		}

		while($row = mysqli_fetch_array( $result )) {
			$status_date = "";
			$status_date = $row['task_tododate'] != '0000-00-00' ? '['.$row['task_tododate'].']' : '';
			echo '<li id="'.$row['tasklistid'].'" class="ui-state-default '.$class_on.'" style=" '.($row['flag_colour'] != '' ? 'background-color: #'.$row['flag_colour'].';' : '').'">';
			echo '<span class="pull-right" style="width: 100%;" data-ticket="'.$row['tasklistid'].'" data-class="task">';
			echo '<span style="display:inline-block; text-align:center; width:11.11%;" title="Flag This!" onclick="flag_item(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" style="height:1.5em;"></span>';
			echo '<span style="display:inline-block; text-align:center; width:11.11%;" title="Send Alert" onclick="send_alert(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-alert-icon.png" style="height:1.5em;" onclick="return false;"></span>';
			echo '<span style="display:inline-block; text-align:center; width:11.11%;" title="Send Email" onclick="send_email(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" style="height:1.5em;" onclick="return false;"></span>';
			echo '<span style="display:inline-block; text-align:center; width:11.11%;" title="Schedule Reminder" onclick="send_reminder(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" style="height:1.5em;" onclick="return false;"></span>';
			echo '<span style="display:inline-block; text-align:center; width:11.11%;" title="Attach File" onclick="attach_file(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" style="height:1.5em;" onclick="return false;"></span>';
			echo '<span style="display:inline-block; text-align:center; width:11.11%;" title="Add Note" onclick="send_reply(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" style="height:1.5em;" onclick="return false;"></span>';
			echo '<span style="display:inline-block; text-align:center; width:11.11%;" title="Add Time" onclick="add_time(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-timer-icon.png" style="height:1.5em;" onclick="return false;"></span>';
			echo '<span style="display:inline-block; text-align:center; width:11.11%;" title="Track Time" onclick="track_time(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-timer2-icon.png" style="height:1.5em;" onclick="return false;"></span>';
			echo '<span style="display:inline-block; text-align:center; width:11.11%;" title="Archive Item" onclick="archive(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" style="height:1.5em;" onclick="return false;"></span>';
			echo '</span><div class="clearfix"></div><a href="'.WEBSITE_URL.'/Tasks_Updated/add_task.php?tasklistid='.$row['tasklistid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" onclick="overlayIFrameSlider(this.href,\'auto\',true,true); return false;">Task #'.$row['tasklistid'].' : '.get_client($dbc, $row['businessid']).' - '.limit_text($row['heading'], 5 ).'<br><em>'.get_multiple_contact($dbc, $row['contactid']).$status_date. '</em></a>';
			echo '<img class="drag_handle pull-right" src="'.WEBSITE_URL.'/img/icons/hold.png" style="height:1.5em; width:1.5em;" />';
			echo '<input type="text" name="reply_'.$row['tasklistid'].'" style="display:none; margin-top: 2em;" class="form-control" />';
			echo '<input type="text" name="ticket_time_'.$row['tasklistid'].'" style="display:none; margin-top: 2em;" class="form-control timepicker" />';
			echo '<input type="text" name="reminder_'.$row['tasklistid'].'" style="display:none; margin-top: 2em;" class="form-control datepicker" />';
			echo '<input type="file" name="attach_'.$row['tasklistid'].'" style="display:none;" class="form-control" /></li>';
		}
	}
	echo '</ul>';
	$i++;
}
if($scrum_tab != 'status') { ?>
	</div>
<?php } ?>
<script>
function track_time(task) {
    var task_id = $(task).parents('span').data('ticket');
   if(task_id.toString().substring(0,5) == 'BOARD') {
           task_id = task_id.substring(5);
   }
   overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_timer.php?tile=tasks&id='+task_id, 'auto', false, true);
}
</script>