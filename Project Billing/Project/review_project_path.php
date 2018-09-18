<div class="scrum_tickets" id="scrum_tickets">
<?php
$projectid = $_GET['projectid'];

//Unassigned
    $result1 = mysqli_query($dbc, "SELECT * FROM tickets WHERE projectid='$projectid' AND `status` != 'Archive' AND (`status` = '' OR milestone_timeline = 'Unassigned' OR milestone_timeline = '' OR milestone_timeline IS NULL OR to_do_date IS NULL OR to_do_date = '0000-00-00' OR  to_do_end_date IS NULL OR to_do_end_date = '0000-00-00' OR contactid IS NULL OR contactid = ',,' OR contactid = ',' OR contactid = '')");

    echo '<ul id="sortable Unassigned" class="connectedSortable Unassigned"><li class="ui-state-default ui-state-disabled no-sort">Unassigned</li><br>';

	// Define the date to be displayed for the tickets
    while($row1 = mysqli_fetch_array( $result1 )) {
		$issue = [];
		if($row1['status'] == '') {
			$issue[] = 'No Status';
		}
		if($row1['milestone_timeline'] == '') {
			$issue[] = 'No Milestone';
		}
		if($row1['to_do_date'] == '' || $row1['to_do_date'] == '0000-00-00') {
			$issue[] = 'No Scheduled/To Do Date';
		}
		if(trim($row1['contactid'],',') == '') {
			$issue[] = 'No Staff Assigned Scheduled/To Do';
		}
		echo '<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row1['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" id="'.$row1['ticketid'].'"><li style="background: white;" id="'.$row1['ticketid'].'" class="ui-state-default ui-state-ticket">'.TICKET_NOUN.' #'.$row1['ticketid'].' : '.limit_text($row1['heading'], 5 ). '<br><em>['.implode(', ',$issue).']</em></li></a>';
    }

    echo '</ul>';

//Unassigned


$i=0;
$project_path = get_project($dbc, $projectid, 'project_path');

$each_tab = explode('#*#', get_project_path_milestone($dbc, $project_path, 'milestone'));
$timeline = explode('#*#', get_project_path_milestone($dbc, $project_path, 'timeline'));

foreach ($each_tab as $cat_tab) {
    $result = mysqli_query($dbc, "SELECT * FROM tickets WHERE projectid='$projectid' AND milestone_timeline = '$cat_tab' AND `status` != 'Archive'");

    $status = $cat_tab;
    $status = str_replace("&","FFMEND",$status);
    $status = str_replace(" ","FFMSPACE",$status);
    $status = str_replace("#","FFMHASH",$status);
    echo '<ul id="sortable'.$i.'" class="connectedSortable '.$status.'"><li class="ui-state-default ui-state-disabled no-sort">'.$cat_tab.'<br>'.$timeline[$i].'</li><br>';

	// Define the date to be displayed for the tickets
    while($row = mysqli_fetch_array( $result )) {
		$status_date = "";
		switch($row['status']) {
			case 'Scheduled/To Do': $status_date = $row['to_do_date'] != '0000-00-00' ? ' - '.$row['to_do_date'] : ''; break;
			case 'Internal QA': $status_date = $row['internal_qa_date'] != '0000-00-00' ? ' - '.$row['internal_qa_date'] : ''; break;
			case 'Customer QA': $row['deliverable_date'] != '0000-00-00' ? $status_date = ' - '.$row['deliverable_date'] : ''; break;
			case 'Archive': $status_date = ' - '.date('Y-m-d', strtotime($row['status_date'])); break;
		}
		echo '<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" id="'.$row['ticketid'].'">';
		echo '<li style="background: white; '.($row['flag_colour'] != '' ? 'background-color: #'.$row['flag_colour'].';' : '').'" id="'.$row['ticketid'].'" class="ui-state-default ui-state-ticket">';
		echo '<span class="pull-right" style="width: 100%;" data-ticket="'.$row['ticketid'].'">';
		echo '<span style="display:inline-block; text-align:center; width:12.5%;" onclick="ticket_flag_item(this); return false;"><img class="no-toggle" src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" style="height:1.5em;" onclick="return false;" title="Flag This"></span>';
		echo '<span style="display:inline-block; text-align:center; width:12.5%;" onclick="ticket_send_alert(this); return false;"><img class="no-toggle" src="'.WEBSITE_URL.'/img/icons/ROOK-alert-icon.png" style="height:1.5em;" onclick="return false;" title="Send Alert"></span>';
		echo '<span style="display:inline-block; text-align:center; width:12.5%;" onclick="ticket_send_email(this); return false;"><img class="no-toggle" src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" style="height:1.5em;" onclick="return false;" title="Send Email"></span>';
		echo '<span style="display:inline-block; text-align:center; width:12.5%;" onclick="ticket_send_reminder(this); return false;"><img class="no-toggle" src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" style="height:1.5em;" onclick="return false;" title="Schedule Reminder"></span>';
		echo '<span style="display:inline-block; text-align:center; width:12.5%;" onclick="ticket_attach_file(this); return false;"><img class="no-toggle" src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" style="height:1.5em;" onclick="return false;" title="Attach File"></span>';
		echo '<span style="display:inline-block; text-align:center; width:12.5%;" onclick="ticket_send_reply(this); return false;"><img class="no-toggle" src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" style="height:1.5em;" onclick="return false;" title="Add Note"></span>';
		echo '<span style="display:inline-block; text-align:center; width:12.5%;" onclick="ticket_add_time(this); return false;"><img class="no-toggle" src="'.WEBSITE_URL.'/img/icons/ROOK-timer-icon.png" style="height:1.5em;" onclick="return false;" title="Add Time"></span>';
		echo '<span style="display:inline-block; text-align:center; width:12.5%;" onclick="ticket_archive(this); return false;"><img class="no-toggle" src="'.WEBSITE_URL.'/img/icons/trash-icon-red.png" style="height:1.5em;" onclick="return false;" title="Archive"></span>';
		echo '</span><div class="clearfix"></div>'.TICKET_NOUN.' #'.$row['ticketid'].' : '.limit_text($row['heading'], 5 ). '<br><em>['.$row['status'].$status_date.']</em>';
		echo '<img class="drag_handle pull-right" src="'.WEBSITE_URL.'/img/icons/hold.png" style="height:1.5em; width:1.5em;" />';
		echo '<input type="text" name="ticket_reply_'.$row['ticketid'].'" style="display:none;" class="form-control" />';
		echo '<input type="text" name="ticket_reminder_'.$row['ticketid'].'" style="display:none;" class="form-control datepicker" />';
		echo '<input type="text" name="ticket_time_'.$row['ticketid'].'" style="display:none;" class="form-control timepicker" />';
		echo '<input type="file" name="ticket_attach_'.$row['ticketid'].'" style="display:none;" class="form-control" />';
		echo '</li></a>';
    }

	// Add tasks for the milestone
	$result = mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `projectid`='$projectid' AND `project_milestone` = '$cat_tab' AND `deleted` = 0 ORDER BY `sort`");
	while($row = mysqli_fetch_array($result)) {
		echo '<li id="'.$row['tasklistid'].'" class="ui-state-default ui-state-checklist" style=" '.($row['flag_colour'] != '' ? 'background-color: #'.$row['flag_colour'].';' : '').'"><span style="cursor:pointer; max-width: 540px; display:inline-block; width:100%;">';
		echo '<input type="checkbox" onclick="taskChange(this);" '.($row['checked'] == 1 ? 'checked' : '').' value="'.$row['tasklistid'].'" style="height: 1.5em; width: 1.5em;" name="tasklistid[]">';
			echo '<span class="pull-right" style="width: calc(100% - 2em);" data-task="'.$row['tasklistid'].'">';
			echo '<span style="display:inline-block; text-align:center; width:11%" onclick="flag_item(this); return false;"><img class="no-toggle" src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" style="height:1.5em;" onclick="return false;" title="Flag This!"></span>';
			echo '<span style="display:inline-block; text-align:center; width:11%" onclick="assign_item(this); return false;" data-assigned="'.$row['assign_client'].'"><img class="no-toggle" src="'.WEBSITE_URL.'/img/icons/ROOK-sync-icon.png" style="height:1.5em;" onclick="return false;" title="Assign to Customer"></span>';
			echo '<span style="display:inline-block; text-align:center; width:11%" onclick="send_alert(this); return false;"><img class="no-toggle" src="'.WEBSITE_URL.'/img/icons/ROOK-alert-icon.png" style="height:1.5em;" onclick="return false;" title="Send Alert"></span>';
			echo '<span style="display:inline-block; text-align:center; width:11%" onclick="send_email(this); return false;"><img class="no-toggle" src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" style="height:1.5em;" onclick="return false;" title="Send Email"></span>';
			echo '<span style="display:inline-block; text-align:center; width:11%" onclick="send_reminder(this); return false;"><img class="no-toggle" src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" style="height:1.5em;" onclick="return false;" title="Schedule Reminder"></span>';
			echo '<span style="display:inline-block; text-align:center; width:11%" onclick="attach_file(this); return false;"><img class="no-toggle" src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" style="height:1.5em;" onclick="return false;" title="Attach File"></span>';
			echo '<span style="display:inline-block; text-align:center; width:11%" onclick="send_reply(this); return false;"><img class="no-toggle" src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" style="height:1.5em;" onclick="return false;" title="Add Note"></span>';
			echo '<span style="display:inline-block; text-align:center; width:11%" onclick="add_time(this); return false;"><img class="no-toggle" src="'.WEBSITE_URL.'/img/icons/ROOK-timer-icon.png" style="height:1.5em;" onclick="return false;" title="Add Time"></span>';
			echo '<span style="display:inline-block; text-align:center; width:11%" onclick="archive(this); return false;"><img class="no-toggle" src="'.WEBSITE_URL.'/img/icons/trash-icon-red.png" style="height:1.5em;" onclick="return false;" title="Archive"></span>';
			echo '</span>';
			echo '<input type="text" name="reply_'.$row['tasklistid'].'" style="display:none;" class="form-control" />';
			echo '<input type="text" name="tasklist_time_'.$row['tasklistid'].'" style="display:none;" class="form-control timepicker" />';
			echo '<input type="text" name="reminder_'.$row['tasklistid'].'" style="display:none;" class="form-control datepicker" />';
			echo '<input type="file" name="attach_'.$row['tasklistid'].'" style="display:none;" class="form-control" />';
			echo '<br /><span class="display-field" style="white-space: normal;'.($row['checked'] == 1 ? ' text-decoration: line-through;' : '').'">Task #'.$row['tasklistid'].': '.html_entity_decode($row['task']).'</span>';
			$documents = mysqli_query($dbc, "SELECT * FROM task_document WHERE tasklistid='".$row['tasklistid']."'");
			while($doc = mysqli_fetch_array($documents)) {
				echo '<br /><a href="download/'.$doc['document'].'">'.$doc['document'].'<br /><small><em>Uploaded by '.get_staff($dbc, $doc['created_by']).' on '.$doc['created_date'].'</em></small></a>';
			}
		echo '<img class="drag_handle pull-right" src="'.WEBSITE_URL.'/img/icons/hold.png" style="height:1.5em; width:1.5em;" /></span></li>';
	}
	echo "<li></li>";

    echo '<li class="no-sort"><input placeholder="Add New Task" onChange="addQuickItem(this)" name="add_task" id="add_new_task '.$projectid.' '.$status.'" type="text" class="form-control" /></li>';
	echo '<li class="no-sort"><a id="'.$projectid.'_'.$status.'" href="'.WEBSITE_URL.'/Ticket/index.php?edit=0&projectid='.$projectid.'&milestone_timeline='.$status.'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" class="btn brand-btn pull-right">Add '.TICKET_NOUN.'</a></li>';
    echo '</ul>';
    $i++;
}
?>
</div>

<script>
function taskChange(check) {
	var id = check.value;
	var status = check.checked;
	$.ajax({
		method: 'POST',
		url: 'project_ajax_all.php?fill=milestone_item_check',
		data: { id: id, status: status },
		success: function(result) {
			$(check).closest('li').find('.display-field').html(result);
		},
		complete: function(result) {
			console.log(result.responseText);
			if(check.checked) {
				$(check).closest('li').find('.display-field').css('text-decoration','line-through');
			} else {
				$(check).closest('li').find('.display-field').css('text-decoration','');
			}
		}
	});
}
function choose_user(target, id, date) {
	var title	= 'Choose a User';
	$('iframe').load(function() {
		this.contentWindow.document.body.style.overflow = 'hidden';
		this.contentWindow.document.body.style.minHeight = '0';
		this.contentWindow.document.body.style.paddingBottom = '5em';
		var height = $(this).contents().find('option').length * $(this).contents().find('select').height();
		$(this).contents().find('select').data({id: id});
		this.style.height = (height + this.contentWindow.document.body.offsetHeight + 180) + 'px';
		$(this).contents().find('.btn').off();
		$(this).contents().find('.btn').click(function() {
			if($(this).closest('body').find('select').val() != '' && confirm('Are you sure you want to send the '+target+' to the selected user?')) {
				if(target == 'alert') {
					$.ajax({
						method: 'POST',
						url: 'project_ajax_all.php?fill=milestone_item_alert',
						data: { id: id, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				else if(target == 'email') {
					$.ajax({
						method: 'POST',
						url: 'project_ajax_all.php?fill=milestone_item_email',
						data: { id: id, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				else if(target == 'reminder') {
					$.ajax({
						method: 'POST',
						url: 'project_ajax_all.php?fill=milestone_item_reminder',
						data: { id: id, schedule: date, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				$(this).closest('body').find('select').val('');
				$('.close_iframer').click();
			}
			else if($(this).closest('body').find('select').val() == '') {
				$('.close_iframer').click();
			}
		});
	});
	$('#iframe_instead_of_window').attr('src', '<?php echo WEBSITE_URL; ?>/Staff/select_staff.php?target='+target);
	$('.iframe_title').text(title);
	$('.iframe_holder').show();
	$('.hide_on_iframe').hide();
}
function send_alert(task) {
	task_id = $(task).closest('li').attr('id');
	choose_user('alert',  task_id);
}
function send_email(task) {
	task_id = $(task).closest('li').attr('id');
	choose_user('email', task_id);
}
function send_reminder(task) {
	task_id = $(task).parents('span').data('task');
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task board';
		task_id = task_id.substring(5);
	}
	var name_id = (type == 'task board' ? 'board_' : '');
	$('[name=reminder_'+name_id+task_id+']').show().focus();
	$('[name=reminder_'+name_id+task_id+']').keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	});
	$('[name=reminder_'+name_id+task_id+']').blur(function() {
		$(this).hide();
	});
	$('[name=reminder_'+name_id+task_id+']').change(function() {
		var date = $(this).val().trim();
		$(this).val('');
		if(date != '') {
			choose_user('reminder', task_id, date);
		}
	});
}
function send_reply(task) {
	var task_id = $(task).closest('li').attr('id');
	$('[name=reply_'+task_id+']').show().focus();
	$('[name=reply_'+task_id+']').blur(function() {
		$(this).hide();
		var reply = $(this).val().trim();
		$(this).val('');
		if(reply != '') {
			var today = new Date();
			var save_reply = reply + "<br /><small><em>Reply added by <?php echo get_contact($dbc, $_SESSION['contactid']); ?> at "+today.toLocaleString()+"</em></small>";
			$.ajax({    //create an ajax request to load_page.php
				data: { reply: save_reply },
				type: "POST",
				url: "project_ajax_all.php?fill=reply_milestone_item&tasklistid="+$(task).closest('li').attr('id'),
				dataType: "html",   //expect html to be returned
				success: function(response){
					window.location.reload();
					console.log(response.responseText);
				}
			});
		}
	});
}
function attach_file(task) {
	task_id = $(task).parents('span').data('task');
	var type = 'task';
	if(task_id.toString().substring(0,5) == 'BOARD') {
		var type = 'task_board';
		task_id = task_id.substring(5);
	}
	var file_id = 'attach_'+(type == 'task' ? '' : 'board_')+task_id;
	$('[name='+file_id+']').change(function() {
		var fileData = new FormData();
		fileData.append('file',$('[name='+file_id+']')[0].files[0]);
		$.ajax({
			contentType: false,
			processData: false,
			type: "POST",
			url: "project_ajax_all.php?fill=milestone_item_upload&type="+type+"&id="+task_id,
			data: fileData,
			complete: function(result) {
				console.log(result.responseText);
				window.location.reload();
				//alert('Your file has been uploaded.');
			}
		});
	});
	$('[name='+file_id+']').click();
}
function flag_item(task) {
	task_id = $(task).parents('span').data('task');
	$.ajax({
		method: "POST",
		url: "project_ajax_all.php?fill=milestone_item_flag",
		data: { id: task_id },
		complete: function(result) {
			console.log(result.responseText);
			$(task).closest('li').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
		}
	});
}
function assign_item(task) {
	task_id = $(task).parents('span').data('task');
	status = ($(task).data('assigned') == '1' ? '0' : '1');
	$(task).data('assigned', status);
	$.ajax({
		method: "GET",
		url: "project_ajax_all.php?fill=assigntask&id="+task_id+"&assign="+status,
		success: function(result) {
			$(task).closest('li').find('.display-field').html(result);
		},
		complete: function(result) {
			console.log(result.responseText);
			if(status == 1) {
				alert("This item is now visible to the client.");
			} else {
				alert("This item is no longer visible to the client.")
			}
		}
	});
}
function add_time(task) {
	task_id = $(task).parents('span').data('task');
	$('[name=task_time_'+task_id+']').show();
	$('[name=task_time_'+task_id+']').timepicker('option', 'onClose', function(time) {
		var time = $(this).val();
		$('[name=task_time_'+task_id+']').hide();
		$(this).val('00:00');
		if(time != '' && time != '00:00') {
			$.ajax({
				method: 'POST',
				url: 'project_ajax_all.php?fill=milestone_quick_time',
				data: { id: task_id, projectid: '<?= $projectid ?>', time: time+':00' },
				complete: function(result) { console.log(result.responseText); }
			})
		}
	});
	$('[name=task_time_'+task_id+']').timepicker('show');
}
function archive(task) {
	if(confirm("Are you sure you want to archive this item?")) {
		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "project_ajax_all.php?fill=delete_milestone_item&id="+$(task).closest('li').attr('id'),
			dataType: "html",   //expect html to be returned
			success: function(response){
				$(task).parents('li').hide();
				console.log(response.responseText);
			}
		});
	}
}
function ticket_choose_user(target, id, date) {
	var title	= 'Select a User';
	$('iframe').load(function() {
		this.contentWindow.document.body.style.overflow = 'hidden';
		this.contentWindow.document.body.style.minHeight = '0';
		this.contentWindow.document.body.style.paddingBottom = '5em';
		var height = $(this).contents().find('option').length * $(this).contents().find('select').height();
		$(this).contents().find('select').data({id: id});
		this.style.height = (height + this.contentWindow.document.body.offsetHeight + 180) + 'px';
		$(this).contents().find('.btn').off();
		$(this).contents().find('.btn').click(function() {
			if($(this).closest('body').find('select').val() != '' && confirm('Are you sure you want to send the '+target+' to the selected user?')) {
				if(target == 'alert') {
					$.ajax({
						method: 'POST',
						url: 'project_ajax_all.php?fill=ticketsendalert',
						data: { id: id, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				else if(target == 'email') {
					$.ajax({
						method: 'POST',
						url: 'project_ajax_all.php?fill=ticketsendemail',
						data: { id: id, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				else if(target == 'reminder') {
					$.ajax({
						method: 'POST',
						url: 'project_ajax_all.php?fill=ticketsendreminder',
						data: { id: id, schedule: date, user: $(this).closest('body').find('select').val() },
						complete: function(result) { console.log(result.responseText); }
					});
				}
				$(this).closest('body').find('select').val('');
				$('.close_iframer').click();
			}
			else if($(this).closest('body').find('select').val() == '') {
				$('.close_iframer').click();
			}
		});
	});
	$('#iframe_instead_of_window').attr('src', '<?php echo WEBSITE_URL; ?>/Staff/select_staff.php?target='+target);
	$('.iframe_title').text(title);
	$('.iframe_holder').show();
	$('.hide_on_iframe').hide();
}
function ticket_send_alert(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	ticket_choose_user('alert', ticket_id);
}
function ticket_send_email(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	ticket_choose_user('email', ticket_id);
}
function ticket_send_reminder(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	$('[name=ticket_reminder_'+ticket_id+']').show().focus();
	$('[name=ticket_reminder_'+ticket_id+']').keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	});
	$('[name=ticket_reminder_'+ticket_id+']').change(function() {
		$(this).hide();
		var date = $(this).val().trim();
		$(this).val('');
		if(date != '') {
			ticket_choose_user('reminder', ticket_id, date);
		}
	});
}
function ticket_send_reply(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	$('[name=ticket_reply_'+ticket_id+']').show().focus();
	$('[name=ticket_reply_'+ticket_id+']').keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	});
	$('[name=ticket_reply_'+ticket_id+']').blur(function() {
		$(this).hide();
		var note = $(this).val().trim();
		$(this).val('');
		if(note != '') {
			$.ajax({
				method: 'POST',
				url: 'project_ajax_all.php?fill=ticketsendnote',
				data: { id: ticket_id, note: note },
				complete: function(result) { console.log(result.responseText); }
			})
		}
	});
}
function ticket_attach_file(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	var file_id = 'ticket_attach_'+ticket_id;
	$('[name='+file_id+']').change(function() {
		var fileData = new FormData();
		fileData.append('file',$(ticket).parents('li').find('[name='+file_id+']')[0].files[0]);
		$.ajax({
			contentType: false,
			processData: false,
			type: "POST",
			url: "project_ajax_all.php?fill=ticketsendupload&id="+ticket_id,
			data: fileData,
			complete: function(result) { console.log(result.responseText); }
		});
	});
	$(ticket).parents('li').find('[name='+file_id+']').click();
}
function ticket_flag_item(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	$.ajax({
		method: "POST",
		url: "project_ajax_all.php?fill=ticketflag",
		data: { id: ticket_id },
		complete: function(result) {
			console.log(result.responseText);
			$(ticket).closest('li').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
		}
	});
}
function ticket_add_time(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	$('[name=ticket_time_'+ticket_id+']').show();
	$('[name=ticket_time_'+ticket_id+']').timepicker('option', 'onClose', function(time) {
		var time = $(this).val();
		$('[name=ticket_time_'+ticket_id+']').hide();
		$(this).val('00:00');
		if(time != '' && time != '00:00') {
			$.ajax({
				method: 'POST',
				url: 'project_ajax_all.php?fill=ticketquicktime',
				data: { id: ticket_id, time: time+':00' },
				complete: function(result) { console.log(result.responseText); }
			})
		}
	});
	$('[name=ticket_time_'+ticket_id+']').timepicker('show');
}
function ticket_archive(ticket) {
	ticket_id = $(ticket).parents('span').data('ticket');
	if(confirm("Are you sure you want to archive this ticket?")) {
		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "project_ajax_all.php?fill=ticketquickarchive&id="+ticket_id,
			dataType: "html",   //expect html to be returned
			success: function(response){
				console.log(response.responseText);
				$(ticket).parents('li').hide();
			}
		});
	}
}
</script>
